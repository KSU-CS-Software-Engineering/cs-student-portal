<?php

namespace App\Rules;
use App\Models\Course;
use App\Models\Plan;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Planrequirement;
use spec\PhpSpec\Process\Prerequisites\SuitePrerequisitesSpec;

class VerifySemester
{


//Checks to see if the student has the correct number of hours to be a full time student
//Hours need to be less than 21 and greater than 0
    public function CheckHours(Plan $plan)
    {
        //This is the array that will be returned with the bad semesters in it.
        $returnArray = [];
        //Get the semesters for the plan
        $semesters = Semester::where('plan_id', $plan->id)->get();
        //Foreach of those smesters
        foreach($semesters as $semester) {
          //grab the plan requirements for that semester
            $classesForTheSemester = Planrequirement::where('plan_id', $plan->id)->where('semester_id', $semester->id)->get();
            //Need to keep track of the creditHOurs in this scope
            $creditHours = 0;
            //Foreach class in that semester
            foreach($classesForTheSemester as $class) {
                //Add the credits for that class to the credit hours
                $creditHours += $class->credits;
            }
            //If something is inocrrect with the semester add the semester to the array
            if ($creditHours > 21 || $creditHours < 1) {
              $returnArray->push($semester);
            }
        }
        return $returnArray;
    }

//Checks to see if the student has the correct prereqs to take the current semester worth of classes
//Returns an array with the courses they need to take for a class if needed
    public function CheckPreReqs(Plan $plan)
    {
        $array = [];
        //Get the courses from that semester to be checked.
        $coursesPlanRequirement = \App\Models\PlanRequirement:: where('semester_id', $plan->start_semester)->get();
        //Get all of the courses from that semesters as the course object.
        $courseArray  = [];
        foreach($coursePlanRequirements as $coursesPlanRequirement) {
          $courseArray->push(App\Models\Course::where('id', $coursesPlanRequirement->course_id));
        }
        //Get all of the completed classes for that user.
        $StudentCompletedCourses = $plan->student->completedcourses;

        foreach ($courseArray as $course) {
            //Need to get the prerequisites from that course object.
            //$prerequisites = Prerequisite::where('prerequisite_for_course_id', $course->course_id);// $course->prerequisites;
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
