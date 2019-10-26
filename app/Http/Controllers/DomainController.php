<?php

namespace App\Http\Controllers;

use App\Domain;
use App\Traits\ResponseHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\MyUtil\MyHelper;

class DomainController extends Controller
{
    use ResponseHandler;

    public function __construct()
    {
        $this->middleware('auth');
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
                'domain' => 'url',
            ]);

            $url = MyHelper::urlSanitize($validated['domain']);

            $args = [
                'user_id' => auth()->id(),
                'domain' => $url,
                'activation_token' => Str::random(60),
                'activation_status' => '0',
                'activation_type' => 'file',
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

        $status = $this->confirmDomain($requestedDomain);
        if ($status) {
            $this->activateDomain($domain, $url);
            return $this->res(true, ['message' => "Your domain activate successfully."]);
        }

        return $this->res(false, ['message' => "Something went wrong!"]);
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

    protected function confirmDomain($requestedDomain)
    {
        try {
            $item = $requestedDomain->first()->toArray();
            $result = file_get_contents($item['domain'] . '/' . 'arvancloud-' . $item['activation_token'] . '.txt');
            if (trim($result) === $item['activation_token']) {
                return true;
            }
            return false;
        } catch (\Throwable $th) {
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
}
