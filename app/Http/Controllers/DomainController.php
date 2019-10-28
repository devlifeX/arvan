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
                'domain' => 'required|url',
                'type' =>  'string|min:3'
            ]);

            $url = MyHelper::urlSanitize($validated['domain']);
            $activation_type = $this->activationTypesCheck($validated['type']);

            $args = [
                'user_id' => auth()->id(),
                'domain' => $url,
                'activation_token' => Str::random(60),
                'activation_status' => '0',
                'activation_type' => $activation_type,
            ];
            $domain->create($args);
            return $this->res(
                true,
                [
                    'message' => 'Your Domain added succesfully.',
                    'token' => $args['activation_token']
                ]
            );
        } catch (\Throwable $th) {
            Log::debug($th);
            return $this->res(false, ['message' => 'Add domain failed!']);
        }
    }

    public function confirm(Domain $domain, Request $req)
    {
        $validated = $req->validate([
            'domain' => 'url',
        ]);

        $url = MyHelper::urlSanitize($validated['domain']);

        $requestedDomain = $this->getDomainOfCurrentUser($domain, $url);

        if ($requestedDomain->isEmpty()) {
            return $this->res(false, ['message' => "Bad domain!"]);
        }

        $status = false;

        $type = $this->getTypeOfActivation($requestedDomain);
        if ($type === 'file') {
            $status = $this->confirmDomainByFile($requestedDomain);
        } else if ($type === 'dns') {
            $status = $this->confirmDomainByDns($requestedDomain);
        }

        if ($status) {
            $this->activateDomain($domain, $url);
            return $this->res(true, ['message' => "Your domain activate successfully."]);
        }

        return $this->res(false, ['message' => "Something went wrong!"]);
    }

    protected function getTypeOfActivation($requestedDomain)
    {
        return $requestedDomain->first()->value('activation_type');
    }

    protected function getDomainOfCurrentUser(Domain $domain, $input)
    {
        return $domain->where([
            ['user_id', '=',  auth()->id()],
            ['domain', $input]
        ])->get();
    }

    protected function activateDomain(Domain $domain, $input)
    {
        return $domain
            ->where([
                ['user_id', '=', auth()->id()],
                ['domain', $input]
            ])
            ->update(['activation_status' => true]);
    }

    protected function confirmDomainByFile($requestedDomain)
    {
        try {
            $item = $requestedDomain->first()->toArray();
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
            $item = $requestedDomain->first()->toArray();
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
                'activation_status' => $domain['activation_status'],
            ];
        });
        return $this->res(true, ['domains' => $newDomains]);
    }

    protected function dnsTxtRecords($url)
    {
        try {
            $prepareUrl = function ($url) {
                return str_replace('www.', '', parse_url(strtolower($url))['host']);;
            };
            $dns = new Dns($prepareUrl($url));
            $result  = $dns->getRecords('TXT');
            if (!$result)  return [];
            return
                collect(explode("\n", $result))
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
}
