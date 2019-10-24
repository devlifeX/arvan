<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DomainModel extends Model
{
    public $table = "domains";

    protected $guarded = [];

    protected $hidden = [
        'activation_token',
    ];

    public function users()
    {
        $this->hasOne(User::class);
    }

    public function AddDomain()
    { }
}
