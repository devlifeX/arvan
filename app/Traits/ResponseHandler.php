<?php

namespace App\Traits;

use Illuminate\Http\Response;

trait ResponseHandler
{
    protected $response = [];

    public function res(bool $status, $override = [])
    {
        if ($status) {
            $this->response['success'] = true;
            $this->response['message'] = 'Your Operation successfully done.';
        } else {
            $this->response['success'] = false;
            $this->response['message'] = 'Your Operation failed!';
        }

        $this->response = array_merge($this->response, $override);

        response()->json($this->response)->send();
        exit;
    }
}
