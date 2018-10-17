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
    public function CheckHours(Semester $semester)
    {
        //Gets the semester information and adds all the credit hours into credithours
        $CreditHours = App\Models\Planrequirements:: where('semester_id', $semester->id)->sum('credits')->get();

        if ($CreditHours > 21 || $CreditHours < 1) {

            return false;
        } else {
            return true;
        }

    }

//Checks to see if the student has the correct prereqs to take the current semester worth of classes
//Returns an array with the courses they need to take for a class if needed
    public function CheckPreReqs(Semester $semester, Student $student)
    {
        $array = [];
        $courses = App\Models\Planrequirements:: where('semester_id', $semester->id)->get();

        $StudentCompletedCourses = $student->completedcourses;

        foreach ($courses as $course) {
            $prerequisites = $course->prerequisites;

            foreach ($prerequisites as $prereq) {

                $coursenumberlookup = App\Models\Course:: where('id', $prereq->prerequisite_for_course_id);
                if (!$StudentCompletedCourses.contains('coursenumber', $coursenumberlookup->number)) {
                    $array->push($coursenumberlookup);
                }

            }

        }
        return $array;
    }
}
