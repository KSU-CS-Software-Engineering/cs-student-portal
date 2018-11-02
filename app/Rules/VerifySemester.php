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
      $completedCourses = Completedcourse::where('student_id', $plan->student_id);
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
                    $courseObjGetName = $courseObj->prefix . $courseObj->number;
                    //If the prereq does not appear in the previous semesters or completed courses.
                    if($previousSemestersClasses->contains('course_name', $courseObjGetName) == FALSE || $completedCourses->contains('name', $courseObjName) == FALSE) {
                          var_dump($courseObjGetName);
                          $returnArray[$count] = $courseObj;
                    }
                }

            }
              $count++;
          }
      }
      return $returnArray;













        // $returnArray = [];
        // $previousSemestersArray = [];
        // $courseCount = 0;
        // $count = 0;
        //
        // $plannedSemesters = Semester::where('plan_id', $plan->id)->get();
        // $completedCourses = Completedcourse::where('student_id', $plan->student_id);
        //
        // foreach($plannedSemesters as $plannedSemester) {
        //     $semesterCourses = Planrequirement::where('semester_id', $plannedSemester->id)->get();
        //     $previousSemestersClasses = Planrequirement::where('plan_id', $plan->id)->where('semester_id', '<', $plannedSemester->id)->get();
        //     //var_dump($semesterCourses);
        //     //Get courses for the semester, get the prereqs for each course, check that the previous semesters or completed classes contain that class
        //     $semesterPrereqObjectArray = [];
        //     foreach($semesterCourses as $semesterCourse) {
        //       if($semesterCourse->course != NULL) {
        //           //var_dump($semesterCourse->course->id);
        //           //$semesterPrereqObjectArray[$count]
        //           $prereqObjs = \App\Models\Prerequisite::where('prerequisite_for_course_id', $semesterCourse->course->id)->get();
        //           foreach($prereqObjs as $prereqObj) {
        //               //In theory there should only be one of these in the database, but it's giving me a collection object like there's multiple  objects.
        //               $courseObj = Course::where('id', $prereqObj->prerequisite_course_id)->get()[0];
        //               $courseObjGetName = $courseObj->prefix . $courseObj->number;
        //               if($previousSemestersClasses->contains('course_name', $courseObjGetName) == FALSE || $completedCourses->contains('name', $courseObjName) == FALSE) {
        //                   //dd($courseObj);
        //                   $returnArray[$count] = $courseObj; //What do we want to return?. This returns the course_prequisite_id
        //               }
        //           }
        //
        //       }
        //
        //         //$semesterPrereqObjectArray[$count] = \App\Models\Prerequisite::where('prerequisite_for_course_id', $semesterCourse->course->id)->get();
        //
        //           //var_dump($semesterPrereqObjectArray[$count]);//->prerequisite_course_id);
        //
        //
        //
        //         // if($semesterPrereqObjectArray[$count] != NULL) {
        //         //     $previousSemestersClasses = Planrequirement::where('plan_id', $plan->id)->where('semester_id', '<', $plannedSemester->id)->get();
        //         //     $preReqCourseObj = Course::where('id', $semesterPrereqObjectArray[$count]->prerequisite_course_id)->get();
        //         //     $preReqGetName = $preReqCourseObj->course_prefix . $preReqCourseObj->number;//$semesterPrereqObjectArray[$count]->course_prefix . $semesterPrereqObjectArray[$course]->number;
        //         //     dd($preReqGetName);
        //
        //
        //
        //             // if($previousSemesters->contains('course_id', $semesterPrereqObjectArray[$count]->prerequisite_course_id) == FALSE && $completedCourses->contains('name', (Course::where('id', ($semesterPrereqObjectArray[$count]->prerequisite_course_id)->get()->course_name)) == FALSE){
        //             //     $returnArray[$count] = $semesterPrereqObjectArray[$count];
        //             // }
        //         //}
        //         $count++;
        //     }
        // }










        // //Get the courses from that semester to be checked.
        // $coursePlanRequirements = PlanRequirement::where('semester_id', $plan->start_semester)->get();
        // //Get all of the courses from that semesters as the course object.
        // $courseArray  = [];
        // foreach($coursePlanRequirements as $coursePlanRequirement) {
        //   //$courseArray->push(Course::where('id', $coursePlanRequirement->course_id)->get()[0]);
        // //  dd(Course::where(Course::with('prerequisites')->where('id', $coursePlanRequirement->course_id)->get());
        //   $courseArray[$courseCount] = Course::with('prerequisites')->where('id', $coursePlanRequirement->course_id)->get();
        //
        //   $courseCount++;
        // }
        // //Get all of the completed classes for that user.
        // $StudentCompletedCourses = $plan->student->completedcourses;
        //
        // foreach ($courseArray as $course) {
        //     //Need to get the prerequisites from that course object.
        //     //$prerequisites = Prerequisite::where('prerequisite_for_course_id', $course->course_id);// $course->prerequisites;
        //     //dd($courseArray);
        //     $prerequisitesFromCourse = $course->prerequisites;
        //     foreach ($prerequisitesFromCourse as $prereq) {
        //       if ($studentCompletedCourses.contains('name', $prereq->prefix . ' ' . $prereq->number) == FALSE) {
        //           $array[$count] = $coursenumberlookup;
        //           //$array->push($coursenumberlookup);
        //       }
        //       $count++;












                // foreach($studentCompletedCourses as $studentCompletedCourse){
                // //gets the id of the prereq
                // $coursenumberlookup = Course::where('id', $prereq->prerequisite_for_course_id)->get();
                //
                //   if (($studentCompletedCourse.contains('coursenumber', $coursenumberlookup->number)) == FALSE) {
                //       $array[$count] = $coursenumberlookup;
                //       //$array->push($coursenumberlookup);
                //   }
                //   $count++;
                // }
                return $returnArray;

            }




    private function CheckPreReqsForSemester(Semester $semester){

    }
}
