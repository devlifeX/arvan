<?php

namespace App\Http\Controllers;

use App\Domain;
use App\Traits\ResponseHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\MyUtil\MyHelper;
use Spatie\Dns\Dns;
use Log;


class DomainController extends Controller
{
    use ResponseHandler;
    protected $prefixActivation = 'arvancloud-';

    public function __construct()
    {
        $this->middleware('auth');
    }

    private function activationTypesCheck($type)
    {
        $types = [
            'dns',
            'file'
        ];
        if (in_array($type, $types)) {
            return $type;
        }
        return 'dns';
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Domain $domain, Request $req)
    {
        try {

            $validated = $req->validate([
                'domain' => 'required|active_url',
                'type' =>  'string|min:3'
            ]);

            $url = MyHelper::urlSanitize($validated['domain']);

            if (auth()->user()->isDomainExistByUserID($url)) {
                throw new \Exception("Domain Already exist!");
            }

            $activation_type = $this->activationTypesCheck($validated['type']);

            $args = [
                'user_id' => auth()->id(),
                'domain' => $url,
                'activation_token' => Str::random(60),
                'activation_status' => '0',
                'activation_type' => $activation_type,
            ];
            $createdDomain = $domain->create($args);
            $this->res(
                true,
                [
                    'message' => 'Your Domain added succesfully.',
                    'domain' => [
                        'token' => $createdDomain['activation_token'],
                        'id' => $createdDomain['id'],
                    ]
                ]
            );
        } catch (\Throwable $th) {
            Log::debug($th);
            $message = $th->getMessage() ?? 'Add domain failed!';
            $this->res(false, ['message' => $message]);
        }
    }

    public function confirm(Domain $domain, Request $req)
    {
        $validated = $req->validate([
            'domain_id' => 'required|integer|min:1',
        ]);

        $requestedDomain = $this->getDomain($validated);

        $this->beforeConfirm($requestedDomain);

        $status = false;

        $type = $this->getTypeOfActivation($requestedDomain);
        if ($type === 'file') {
            $status = $this->confirmDomainByFile($requestedDomain);
        } else if ($type === 'dns') {

            $status = $this->confirmDomainByDns($requestedDomain);
        }

        if ($status) {
            $this->activateDomain($requestedDomain);
            $this->res(true, ['message' => "Your domain activate successfully."]);
        } else {
            $this->res(false, ['message' => "Your domain activatation FAILED!"]);
        }
    }

    protected function beforeConfirm($requestedDomain)
    {
        if ($requestedDomain->count() <= 0) {
            $this->res(false, ['message' => "Bad domain!"]);
        }

        $owner =  $requestedDomain->owner_id;
        $isOwner = $requestedDomain->owner_id == auth()->id();
        if ($owner && $isOwner) {
            $this->res(false, ['message' => "You are not owner of this domain!"]);
        }

        if ($requestedDomain->activation_status === 1) {
            $this->res(false, ['message' => "Your domain already activated!"]);
        }
    }

    protected function getTypeOfActivation($requestedDomain)
    {
        return $requestedDomain->activation_type;
    }

    protected function activateDomain($requestedDomain)
    {
        return $requestedDomain
            ->update([
                'activation_status' => true,
                'owner_id' => auth()->id()
            ]);
    }

    protected function confirmDomainByFile($requestedDomain)
    {
        try {
            $item = $requestedDomain->toArray();
            $result = file_get_contents($item['domain'] . '/' . $this->prefixActivation . $item['activation_token'] . '.txt');
            if (trim($result) === $item['activation_token']) {
                return true;
            }
            return false;
        } catch (\Throwable $th) {
            Log::debug($th);
            return false;
        }
    }

    protected function confirmDomainByDns($requestedDomain)
    {
        try {
            $item = $requestedDomain->toArray();
            $txtRecords  = $this->dnsTxtRecords($item['domain']);
            return $this->dnsHasToken($txtRecords, $item['activation_token']);
        } catch (\Throwable $th) {
            Log::debug($th);
            return false;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Domain  $domain
     * @return \Illuminate\Http\Response
     */
    public function show(Domain $domain)
    {
        $domains = $domain->where('user_id', '=', auth()->id())->get();
        $newDomains =  $domains->map(function ($domain) {
            return [
                'id' => $domain['id'],
                'user_id' => $domain['user_id'],
                'domain' => $domain['domain'],
                'activation_token' => $domain['activation_token'],
                'activation_type' => $domain['activation_type'],
                'activation_status' => $domain['activation_status'],
            ];
        });
        $this->res(true, ['domains' => $newDomains]);
    }

    protected function dnsTxtRecords($url)
    {
        try {
            $getTXTRecord = function ($url) {
                $dns = new Dns($url);
                return $dns->getRecords('TXT');
            };
            $withOutWWW = function ($url) {
                return str_replace('www.', '', parse_url(strtolower($url))['host']);;
            };
            $spliter = function ($string) {
                return explode("\n", $string);
            };
            $results =
                [
                    $spliter($getTXTRecord($url)), // with WWW
                    $spliter($getTXTRecord($withOutWWW($url))) // without WWW
                ];

            return
                collect($results)
                ->flatten(1)
                ->filter(function ($item) {
                    return !empty($item);
                })
                ->toArray();
        } catch (\Throwable $th) {
            Log::debug($th);
            return [];
        }
    }

    protected function dnsHasToken($txtRecords, $token)
    {
        try {
            if (!$txtRecords) return false;
            return collect($txtRecords)
                ->map(function ($item) use ($token) {
                    return strpos($item, $this->prefixActivation . $token) !== false;
                })
                ->some(true);
        } catch (\Throwable $th) {
            Log::debug($th);
            return false;
        }
    }

    public function getDomain($input)
    {
        extract($input);
        return auth()
            ->user()
            ->domains()
            ->where('id', $domain_id)->first();
    }

    public function delete(Domain $domain, $id)
    {
        $domain = $domain->findOrFail($id);
        if ($domain->user_id === auth()->id()) {
            $domain->delete();
            $this->res(true);
        }

        $this->res(false);
    }
}
