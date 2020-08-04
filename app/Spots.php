<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Spots extends Model
{

    public function spotType() {
        return $this->belongsTo(SpotsTypes::class);
    }

}
