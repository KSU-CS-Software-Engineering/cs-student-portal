<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model {
    //ScheduleId
    //SemesterId
    //StudentId?




    //This will allow for the Schedule to access the scheduled class, and the semester that it is scheduled for.
    //Would we need to have a StudentId?. I guess it would depend on how the rest of this is implemented.
    //If there are ways to ensure that only the correct schedules are shown then it will be fine.
    public function sections() {
        return $this->hasMany('App\Models\Section');
    }


    protected $rules = array(
      'id' => 'required|integer',
      'semester_id' => 'required|exists:semester_id',
      'student_id' => 'required|exists:student_id',
    );


    protected $fillable = ['id', 'semester_id', 'student_id'];
}
