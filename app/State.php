<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    /**
     * Get the company of a particular state.
     */
    public function companies()
    {
        return $this->hasMany('App\Company');
    }
}
