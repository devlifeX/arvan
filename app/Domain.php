<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    protected $guarded = [];


    public function isDomainConfirmed($domain)
    {
        return $this
            ->where('owner_id', '<>', null)
            ->where(
                'domain',
                '=',
                $domain
            )
            ->count() > 0;
    }

    public function users()
    {
        return $this->hasOne(User::class);
    }
}
