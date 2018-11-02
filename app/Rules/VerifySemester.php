<?php

namespace App\Rules;
use App\Models\Course;
use App\Models\Plan;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Planrequirement;
use App\Models\Completedcourse;
use spec\PhpSpec\Process\Prerequisites\SuitePrerequisitesSpec;

class VerifySemester
{


//Checks to see if the student has the correct number of hours to be a full time student
//Hours need to be less than 21 and greater than 0
    public function CheckHours(Plan $plan)
    {
        $count = 0;
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
              $returnArray[$count] = $semester;
            }
            $count++;
        }
        return $returnArray;
    }

//Checks to see if the student has the correct prereqs to take the current semester worth of classes
//Returns an array with the courses they need to take for a class if needed
    public function CheckPreReqs(Plan $plan)
    {
      $returnArray = [];
      $count = 0;
      //Get all of the semesters from the plan to iterate through
      $plannedSemesters = Semester::where('plan_id', $plan->id)->get();
      //Get all of the completedcourses for the student's plan
      $completedCourses = Completedcourse::where('student_id', $plan->student_id)->get();
      //Foreach semester in the plan
      foreach($plannedSemesters as $plannedSemester) {
          //Get the courses that are being taken that semester.
          $semesterCourses = Planrequirement::where('semester_id', $plannedSemester->id)->get();
          //Get all of the classes that are taken in the semesters before the semester we're iterating on.
          $previousSemestersClasses = Planrequirement::where('plan_id', $plan->id)->where('semester_id', '<', $plannedSemester->id)->get();
          //Foreach course being taken that semester.
          foreach($semesterCourses as $semesterCourse) {
            //If the Planrequirement object is not mapped to a course object (This is an elective that does not have a course matched with it yet.)
            if($semesterCourse->course != NULL) {
                //Get the prereqs for that course.
                $prereqObjs = \App\Models\Prerequisite::where('prerequisite_for_course_id', $semesterCourse->course->id)->get();
                //Foreach one of these prereqs
                foreach($prereqObjs as $prereqObj) {
                    //In theory there should only be one of these in the database, but it's giving me a collection object like there's multiple  objects.
                    //Get the course object for that prereq (This is used to get the course name)
                    $courseObj = Course::where('id', $prereqObj->prerequisite_course_id)->get()[0];
                    //Concatenate the course Prefix and the course number ot get the name of the course
                    $courseObjGetName = $courseObj->prefix . " " . $courseObj->number;
                    //If the prereq does not appear in the previous semesters or completed courses.
                    if($previousSemestersClasses->contains('course_name', $courseObjGetName) == FALSE && $completedCourses->contains('name', $courseObjGetName) == FALSE) {
                          if(in_array($courseObjGetName . " is a prerequisite for " . $semesterCourse->course_name, $returnArray) == FALSE) {
                            $returnArray[$count] = $courseObjGetName . " is a prerequisite for " . $semesterCourse->course_name;
                          }
                    }
                      $count++;
                }
            }

          }
      }
      //dd($returnArray);
      return $returnArray;
    }

    public function CheckCoursePlacement(Plan $plan){
        $returnArray = [];




        return $returnArray;
    }

}
