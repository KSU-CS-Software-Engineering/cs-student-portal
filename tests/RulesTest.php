<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use App\Models\Student;
use App\Models\Course;
use App\Models\Advisor;
use App\Models\Plan;
use App\Models\Department;
use App\Models\Completedcourse;
use App\Models\Degreerequirement;
use App\Models\Electivelistcourse;
use App\Models\Degreeprogram;
use App\Models\Planrequirement;
use App\Models\Semester;
use App\Rules\VerifyFourYearPlan;
use App\Rules\VerifySemester;




class RulesTest extends TestCase {
    use DatabaseTransactions;


    public function test4YrPlanValidity() {
        //We will just choose this as the test case.
        $plan = App\Models\Plan::where('id', 1)->get();
        $rules = new VerifyFourYearPlan();
        //Make sure to grab the first one in the array (it'sthe only one in the array)
        $this->AssertEmpty($rules->CheckCISRequirementsPlan($plan[0]));
    }


    //This method looks complicated, but it's a lot of superflous info.
    //This creates an entire career of completed classes, based on the class_name from the degree requirements.
    //Some of the degree requirements don't have course_name populated, so it looks at the electivelist_id, and chooses one of the classes to make the class name.
    //These degree requirements including class names for all of the classes need to be compared to the completed classes, so that's why there's a whole new degreerequirements being create
    //The plan gets created because it needs to have the correct degreeprogram_id and degreerequirements.
    public function testGraduationValidityDegreeRequirements() {
        //Create rules object
        $rules = new VerifyFourYearPlan();
        //Create a new degree program and plan that these degreerequirements will map to.
        //$degreeprogramCreated = factory(Degreeprogram::class)->create();
        $plan = factory(Plan::class)->create();
        //Grab the degree requirements to copy.
        $degreerequirements = Degreerequirement::where('degreeprogram_id', 1)->get();

        //For all of the degree requirements
        foreach($degreerequirements as $degreerequirement) {
            //If the degree requirement does not have a coursename (aka an elective)
            if($degreerequirement->course_name == '') {
                  //This just grabs the first class that is listed under that ElectiveCourseId. This is just a placeholder to create proper completedClasses.
                  $electiveListToGetClassNameFrom = Electivelistcourse::where('electivelist_id', $degreerequirement->electivelist_id)->get()[0];
                  //This creates a new CompletedCourse with a name of the course_prefix and course number of the selected
                  factory(Completedcourse::class)->create(['name' => (string)$electiveListToGetClassNameFrom->course_prefix . (string)$electiveListToGetClassNameFrom->course_min_number, 'student_id' => $plan->student_id]);
                  // This creates a new degree requirement using the proper course name (also the semester and ordering are unique keys)
                  factory(Degreerequirement::class)->create(['degreeprogram_id' => $plan->degreeprogram_id ,'course_name' => (string)$electiveListToGetClassNameFrom->course_prefix . (string)$electiveListToGetClassNameFrom->course_min_number, 'electivelist_id' => $degreerequirement->electivelist_id, 'semester' => $degreerequirement->semester, 'ordering' => $degreerequirement->ordering]);
            }
            else {
                  //Copy the course name and student id over to the new created course
                  factory(Completedcourse::class)->create(['name' => $degreerequirement->course_name, 'student_id' => $plan->student_id]);
                  //Copy the course name and the foreign keys to the new data.
                  factory(Degreerequirement::class)->create(['degreeprogram_id' => $plan->degreeprogram_id ,'course_name' => $degreerequirement->course_name, 'electivelist_id' => $degreerequirement->electivelist_id, 'semester' => $degreerequirement->semester, 'ordering' => $degreerequirement->ordering]);
            }
        }
        //Send the created plan which has the mapped completed classes and degreerequirements etc.
        $this->AssertEmpty($rules->CheckGraduationValidityDegreeRequirements($plan)); //as the class we want.
    }

    //This needs to be similar to the function above.
    //Instead, I want to check completed courses against planrequirements rather than degree requirements
    public function testGraduationValidityPlanRequirements() {
        $rules = new VerifyFourYearPlan();
        $plan = factory(Plan::class)->create();
        $planrequirements = Planrequirement::where('plan_id', 1)->get();
        foreach($planrequirements as $planrequirement) {
            if(Semester::where('id', $planrequirement->semester_id + 100)->exists() == false) {
              factory(Semester::class)->create(['id' => ($planrequirement->semester_id + 100), 'plan_id' => $plan->id, 'ordering' => $planrequirement->semester->ordering]);
            }
            if ($planrequirement->course_name == '') {
                $electiveListToGetClassNameFrom = Electivelistcourse::where('electivelist_id', $planrequirement->electivelist_id)->get()[0];
                $newCompletedCourse = factory(Completedcourse::class)->create(['name' => (string)$electiveListToGetClassNameFrom->course_prefix . (string)$electiveListToGetClassNameFrom->course_min_number, 'student_id' => $plan->student_id]);

                $newPlanRequirement = factory(Planrequirement::class)->create(['ordering' => $planrequirement->ordering, 'semester_id' => ($planrequirement->semester_id + 100),'plan_id'=> $plan->id, 'course_name' => (string)$electiveListToGetClassNameFrom->course_prefix . (string)$electiveListToGetClassNameFrom->course_min_number, 'electivelist_id' => $planrequirement->electivelist_id]);
            }
            else {
              factory(Completedcourse::class)->create(['name' => $planrequirement->course_name, 'student_id' => $plan->student_id]);
              //Copy the course name and the foreign keys to the new data.
              factory(Planrequirement::class)->create(['ordering' => $planrequirement->ordering,'semester_id' => ($planrequirement->semester_id + 100),'plan_id' => $plan->id, 'course_name' => $planrequirement->course_name, 'electivelist_id' => $planrequirement->electivelist_id]);

            }
        }

        $this->AssertEmpty($rules->CheckGraduationValidityPlan($plan));
    }




    public function testSemesterCheckHours() {
        $plan = Plan::where('id', 1)->get()[0];
        $rules = new VerifySemester();
        $this->AssertEmpty($rules->CheckHours($plan));
    }

    public function testCheckPreReqs() {
        $plan = Plan::where('id', 2)->get()[0];
        $rules = new VerifySemester();

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
                      if($previousSemestersClasses->contains('course_name', $courseObjGetName) == FALSE || $completedCourses->contains('name', $courseObjGetName) == FALSE) {
                          //Create the class so the tests pass.
                          factory(Completedcourse::class)->create(['name'=> $courseObjGetName, 'student_id'=>$plan->student_id]);
                      }
                  }

              }
                $count++;
            }
        }

        $this->AssertEmpty($rules->CheckPreReqs($plan));
    }






}
