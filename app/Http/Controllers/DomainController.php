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
            return $this->res(true, ['message' => 'Your Domain added succesfully.']);
        } catch (\Throwable $th) {
            return $this->res(false, ['message' => 'Add domain failed!']);
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
