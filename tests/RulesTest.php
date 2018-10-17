<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use App\Models\Student;
use App\Models\Advisor;
use App\Models\Plan;
use App\Models\Department;
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
        //Here we'll grab Plan4, because it matches the DegreeRequirements exactly.
        $plan = App\Models\Plan::where('id', 4)->get();
        //Need to fake the completed classes based on the course name of the DegreeRequirements
        //From there, I can use the electivelist_id to choose a random course that matches the elective Id as the class we want.
    }
}
