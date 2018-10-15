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
        //Use the All() to get the object to an array.
        $plan = App\Models\Plan::where('id', 1)->get()->All();

        $rules = new VerifyFourYearPlan();
        //$rules.SetRuleVariables($plan);

        //Make sure to grab the first one in the array (it'sthe only one in the array)
        $this->AssertEmpty($rules->CheckCISRequirementsPlan($plan[0]));
    }
}
