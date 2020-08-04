<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SpotsTypes extends Model
{

    public function spots() {
        return $this->hasMany(Spots::class, 'spot_type_id');
    }

}
