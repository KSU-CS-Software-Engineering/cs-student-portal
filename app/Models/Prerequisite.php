<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prerequisite extends Model {

    public function PreRequisiteFor() {
        return $this->belongsToMany('App\Models\Course');
    }
}
