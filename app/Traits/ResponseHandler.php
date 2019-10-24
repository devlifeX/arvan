<?php

namespace App\Traits;

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
        return $this->response;
    }
}
