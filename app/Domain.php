<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    protected $guarded = [];

    protected $hidden = [
        'activation_token',
    ];

    public function users()
    {
        $this->hasOne(User::class);
    }


}
