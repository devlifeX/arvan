<?php

namespace App\Http\Controllers;

use App\Domain;
use App\Traits\ResponseHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class DomainController extends Controller
{
    use ResponseHandler;

    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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

            $args = array_merge([
                'user_id' => 1, //auth()->id(),
                'domain' => $validated['domain'],
                'activation_token' => Str::random(60),
                'activation_status' => '0',
                'activation_type' => 'file',
            ], $validated);
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

        $requestedDomain = $this->getDomainOfCurrentUser($domain, $validated['domain']);
        if ($requestedDomain->isEmpty()) {
            return $this->res(false, ['message' => "Bad domain!"]);
        }

        $status = $this->confirmDomain($requestedDomain);
        if ($status) {
            $this->activateDomain($domain, $validated['domain']);
            return $this->res(true, ['message' => "Your domain activate successfully."]);
        }

        return $this->res(false, ['message' => "Something went wrong!"]);
    }

    protected function getDomainOfCurrentUser(Domain $domain, $input)
    {
        return $domain->where([
            ['user_id', '=', 1]/* auth()->id() */,
            ['domain', $input]
        ])->get();
    }

    protected function activateDomain(Domain $domain, $input)
    {
        return $domain
            ->where([
                ['user_id', '=', 1]/* auth()->id() */,
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Domain  $domain
     * @return \Illuminate\Http\Response
     */
    public function show(Domain $domain)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Domain  $domain
     * @return \Illuminate\Http\Response
     */
    public function edit(Domain $domain)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Domain  $domain
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Domain $domain)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Domain  $domain
     * @return \Illuminate\Http\Response
     */
    public function destroy(Domain $domain)
    {
        //
    }
}
