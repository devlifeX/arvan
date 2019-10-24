<?php

namespace App\Http\Controllers;

use App\Traits\ResponseHandler;
use Illuminate\Http\Request;

class DomainController extends Controller
{
    use ResponseHandler;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create()
    {
        try {
            //code...
            $domain = new \App\DomainModel([
                'user_id' => 1,
                'domain' => 'devlife.ir',
                'activation_token' => '34234234',
                'activation_status' => '0',
                'activation_type' => 'file',
            ]);
            $status =  $domain->save();
            return $this->res($status, ['message' => 'Your Domain added succesfully.']);
        } catch (\Throwable $th) {
            return $this->res(false, ['message' => 'Add domain failed!']);
        }
    }
}
