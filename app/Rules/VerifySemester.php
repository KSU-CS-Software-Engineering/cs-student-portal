<?php

namespace App\Rules;
use App\Models\Course;
use App\Models\Plan;
use App\Models\Semester;
use App\Models\Student;
use spec\PhpSpec\Process\Prerequisites\SuitePrerequisitesSpec;

class VerifySemester
{


//Checks to see if the student has the correct number of hours to be a full time student
//Hours need to be less than 21 and greater than 0
    public function CheckHours(Plan $plan)
    {
        //Gets the semester information and adds all the credit hours into credithours ->sum('credits')-
        $semesters = \App\Models\Planrequirement:: where('semester_id', $plan->semesters)->get();
        foreach($semesters as $semester) {

            $creditHour = $semester->sum('credits');

            if ($creditHour > 21 || $creditHour < 1) {
                return false;
            } else {
                return true;
            }
        }

    }

//Checks to see if the student has the correct prereqs to take the current semester worth of classes
//Returns an array with the courses they need to take for a class if needed
    public function CheckPreReqs(Plan $plan)
    {
        $array = [];
        $courses = \App\Models\Planrequirement:: where('semester_id', $plan->semesters)->get();

        $StudentCompletedCourses = $plan->student->completedcourses;

        foreach ($courses as $course) {
            $prerequisites = $course->prerequisites;

            foreach ($prerequisites as $prereq) {

                $coursenumberlookup = App\Models\Course:: where('id', $prereq->prerequisite_for_course_id);
                if (($StudentCompletedCourses.contains('coursenumber', $coursenumberlookup->number)) == FALSE) {
                    $array->push($coursenumberlookup);
                }

            }

        }
        return $array;
    }
}
