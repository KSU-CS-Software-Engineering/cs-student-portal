<?php

namespace App\Listeners;

use App\Events\PlanRequirementSaved;
use App\Models\Completedcourse;
use App\Models\Course;

class UpdatePlanCoursesMap
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  PlanRequirementSaved  $event
     * @return void
     */
    public function handle(PlanRequirementSaved $event)
    {
        $requirement = $event->requirement;
        if ($requirement->course_id_lock == 0) {
            if ($requirement->isDirty('course_name') && strlen($requirement->course_name) > 0) {
                $courses = Course::filterName($requirement->course_name)->get();
                if ($courses->count() > 0) {
                    $course = $courses->first();
                    $requirement->course_id = $course->id;
                }
            }
        }
        if ($requirement->completedcourse_id_lock == 0) {
            if ($requirement->isDirty('course_name') && strlen($requirement->course_name) > 0) {
                $courses = Completedcourse::filterName($requirement->course_name)->get();
                if ($courses->count() == 1) {
                    $course = $courses->first();
                    $requirement->completedcourse_id = $course->id;
                }
            }
        }
    }
}
