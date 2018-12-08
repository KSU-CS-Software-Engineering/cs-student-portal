<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prerequisite extends Model {

    public function PreRequisiteFor() {
        return $this->belongsToMany('App\Models\Course');
    }

    public function prerequisite_course_id() {
        return $this->prerequisite_course_id;
    }



    protected $fillable = ['id', 'prerequisite_course_id', 'prerequisite_for_course_id'];
}
