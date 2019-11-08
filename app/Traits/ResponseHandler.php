<?php

namespace App\Traits;

use Illuminate\Http\Response;

trait ResponseHandler
{
    protected $response = [];

    public function res(bool $status, $input = null)
    {
        $override = [];
        if (is_string($input) && !empty($input)) {
            $override['message'] = $input;
        } else if (is_array($input)) {
            $override = $input;
        } else {
            $override = [];
        }
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
