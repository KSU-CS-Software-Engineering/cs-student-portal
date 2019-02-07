<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseSection extends Model {
    //Class sections
    //Class type
    //Class name/slugline
    //Class sectionDays
    //Class section time
    //Class Facility
    //Class Faculty
    //Course - I think that I want to try to get this to match up with exisiting Course objects.
                //This would allow for more functionality down the road.
    //Schedule_id
    public function course() {
        return $this->belongsTo('App\Models\Course');
    }

    //protected $fillable = ['id', 'prerequisite_course_id', 'prerequisite_for_course_id'];
}
