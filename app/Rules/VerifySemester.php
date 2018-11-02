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
//        $previousSemestersArray = [];
//        $courseCount = 0;
//        $count = 0;
//
//        $plannedSemesters = Semester::where('plan_id', $plan->id)->get();
//
//        foreach($plannedSemesters as $plannedSemester) {
//            $semesterCourses = Planrequirement::where('semester_id', $plannedSemester->id)->get();
//            //Get courses for the semester, get the prereqs for each course, check that the previous semesters or completed classes contain that class
//            $semesterCourseObjectArray = [];
//            foreach($semesterCourses as $semesterCourse) {
//                $semesterCourseObjectArray[$count] = $semesterCourse->course();//Course::where('id', $semesterCourse->course_id)->get();
//                $prerequi = \App\Models\Prerequisite::where('id', 1)->get();
//                dd($prerequi);
//                foreach($semesterCourseObjectArray[$count]->prerequisites()->prerequisite_course_id as $coursePreReq){
//                  dd($coursePreReq);
//                }
//
//
//
//                $semesterCoursePreReqs = $semesterCourseObjectArray[$count]->prerequisites()->prerequisites;
//                dd($semesterCoursePreReqs);
//                $courseCount++;
//            }
//        }
//
        return $returnArray;

    }


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




    private function CheckPreReqsForSemester(Semester $semester){

    }
}
