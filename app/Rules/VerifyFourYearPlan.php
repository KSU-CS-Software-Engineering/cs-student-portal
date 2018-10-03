<?php

namespace App\Rules;
//These are copied and pasted, likely we don't need all of this.
use App\Models\User;
use App\Models\Student;
use App\Models\Advisor;
use App\Models\Plan;
use App\Models\Semester;
use App\Models\Planrequirement;
use App\Models\Degreeprogram;
use App\Models\Degreerequirement;

//How do we want to test the validity of the 4yr plan?

//I think that the DegreeRequirements table has the classes that are needed.

//We will want to:
  //Check plan for 4yr validity
  //Check completed classes for graduation ability.
  //Check Advisor flag.




//We don't want to take in the planrequirement or degree requirements.
//I want FlowchartsController to send over the array of Planrequirements
//and degree requirements which can then be checked against each other.

//I want to send over a plan object and degree program object.
  public function CheckCISRequirementsPlan(Plan $plan) {
    //We need to test ot make sure all classes in Requirements are in plan
    $degreerequirements = App\Models\Degreerequirement::where('degreeprogram_id', $plan->degreeprogram_id)->get();
    $planrequirements = App\Models\Planrequirement::where('plan_id', $plan->id);
    foreach($degreerequirements as $degreerequirement) {
      if($planrequirements.contains())
    }
    //Pseudocode
    //Get the arrays for the Plan and Degree Requirements
    //Foreach degreerequirement, use the .contains() on the plan collection
    //to check if the Degreerequirement is in the plan requirement.
    //If this ever returns false, the plan fails the check.
  }

?>
