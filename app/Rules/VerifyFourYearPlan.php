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

  //This is untested.
  public function CheckCISRequirementsPlan(Plan $plan) {
    //We need to test ot make sure all classes in Requirements are in plan
    //Create array to put the missing classes in.
    $returnarray = [];
    //Get all of the degree requirements in a collection.
    $degreerequirements = App\Models\Degreerequirement::where('degreeprogram_id', $plan->degreeprogram_id)->get();
    //Get all of the planned classes to compare
    $planrequirements = App\Models\Planrequirement::where('plan_id', $plan->id);
    //Iterate through the degree requirements testing each against the planned classes
    foreach($degreerequirements as $degreerequirement) {
      //If the degree requirement is not in the plan requirements
      if(!$planrequirements.contains('degreerequirement_id', $degreerequirement->id)) {
        //Add the missing degree requirement to the array.
        $returnarray->push($degreerequirement);
      }
    }
    //Return the array.
    return $returnarray;
  }

?>
