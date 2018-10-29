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

            if ($creditHour > 21 || $creditHour < 0) {
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
        //get the courses the student has for next semester
        $courses = \App\Models\Planrequirement:: where('semester_id', $plan->semesters)->get();
        //gets the coursese the student has already completed
        $studentCompletedCourses = $plan->student->completedcourses;

        //goes through each course for next semester
        foreach ($courses as $course) {
            //gets the prereqs for each course
            $prerequisites = $course->prerequisites;
            //goes through each prereq
            foreach ($prerequisites as $prereq) {

                foreach($studentCompletedCourses as $studentCompletedCourse){
                //gets the id of the prereq
                $coursenumberlookup = App\Models\Course:: where('id', $prereq->prerequisite_for_course_id);

                if (($studentCompletedCourse.contains('coursenumber', $coursenumberlookup->number)) == FALSE) {
                    $array->push($coursenumberlookup);
                    }
                }

            }

        }
        return $array;
    }
}
