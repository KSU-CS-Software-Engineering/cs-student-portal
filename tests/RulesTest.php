<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use App\Models\Student;
use App\Models\Advisor;
use App\Models\Plan;
use App\Models\Department;
use App\Models\Completedcourse;
use App\Models\Degreerequirement;
use App\Models\Electivelistcourse;
use App\Models\Degreeprogram;
use App\Rules\VerifyFourYearPlan;




class RulesTest extends TestCase {
    use DatabaseTransactions;


    public function test4YrPlanValidity() {
        //We will just choose this as the test case.
        $plan = App\Models\Plan::where('id', 1)->get();
        $rules = new VerifyFourYearPlan();
        //Make sure to grab the first one in the array (it'sthe only one in the array)
        $this->AssertEmpty($rules->CheckCISRequirementsPlan($plan[0]));
    }


    public function testGraduationValidityDegreeRequirements() {
        //Again we'll just grab exisitng data for the tests.
        $rules = new VerifyFourYearPlan();
        factory(Degreeprogram::class)->create();
        //$plan = App\Models\Plan::where('id', 1)->get();
        $plan = factory(Plan::class)->create();
        $count = 0;

        $completedCoursesArray = [];
        $degreerequirements = Degreerequirement::where('degreeprogram_id', 1)->get();
        foreach($degreerequirements as $degreerequirement) {
            if($degreerequirement->course_name == '') {
                  //This just grabs the first class that is listed under that ElectiveCourseId. This is just a placeholder to create proper completedClasses.
                  $electiveListToGetClassNameFrom = Electivelistcourse::where('electivelist_id', $degreerequirement->electivelist_id)->get()[0];
                  //This creates a new CompletedCourse with a name of the course_prefix and course number of the selected
                  $completedCoursesArray[$count] = factory(Completedcourse::class)->create(['name' => (string)$electiveListToGetClassNameFrom->course_prefix . (string)$electiveListToGetClassNameFrom->course_min_number, 'student_id' => $plan->student_id]);
                  // Here I think I need to do something similar to the degreerequirements since there's empty courseName's
                  factory(Degreerequirement::class)->create(['course_name' => (string)$electiveListToGetClassNameFrom->course_prefix . (string)$electiveListToGetClassNameFrom->course_min_number, 'electivelist_id' => $degreerequirement->electivelist_id, 'semester' => $degreerequirement->semester, 'ordering' => $degreerequirement->ordering]);
            }
            else {
                  $completedCoursesArray[$count] = factory(Completedcourse::class)->create(['name' => $degreerequirement->course_name, 'student_id' => $plan->student_id]);
                  factory(Degreerequirement::class)->create(['course_name' => $degreerequirement->course_name, 'electivelist_id' => $degreerequirement->electivelist_id, 'semester' => $degreerequirement->semester, 'ordering' => $degreerequirement->ordering]);
            }
            $count++;
        }
        //$plan[0]->degreeprogram_id = 2;
        $this->AssertEmpty($rules->CheckGraduationValidityDegreeRequirements($plan));


        //Need to fake the completed classes based on the course name of the DegreeRequirements
        //From there, I can use the electivelist_id to choose a random course that matches the elective Id as the class we want.
    }
}
