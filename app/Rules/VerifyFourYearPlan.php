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
use App\Models\Completedcourse;

//How do we want to test the validity of the 4yr plan?
  private Plan $plan;
  private Student = $student;

  public function CheckFourYearPlanRules(Plan $planParam) {
    //Set the global variables.
    $plan = $planParam;
    $student = App\Models\Student::where('Id', $planParam->student_id);

    $cisRequirements = CheckCISRequirementsPlan();
    //Handle the output
    $ksuGraduationRequirements = CheckGraduationValidityDegreeRequirements();
    //Handle the output
    $planGraduationRequirements = CheckGraduationValidityPlan();
    //Handle the output.

    //Return something to be used.

  }

//We will want to:
  //Implemented
  //Check plan for 4yr validity against KState and CS rules. (Degree Requirements)
  //Check completed classes for graduation ability against KSU and CS Rules
  //Check completed classes for completion of student's plan.

  //Unimplemented
  //Check Advisor flag.

  //This is untested.
  public function CheckCISRequirementsPlan() {
    //We need to test ot make sure all classes in Requirements are in plan
    //Create array to put the missing classes in.
    $returnarray = [];
    //Get all of the degree requirements in a collection.
    $degreerequirements = App\Models\Degreerequirement::where('degreeprogram_id', $plan->degreeprogram_id)->get();
    //Get all of the planned classes to compare
    $planrequirements = App\Models\Planrequirement::where('plan_id', $plan->id)->get();
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

//We will want to split up the graduation ability check into two different functions
//One will check that it passes KSU & CS requiremtns and the other will cehck
//That the student has completed their plan.

  //This is untested.
  //This checks that the user has completed all of the required classes to graduate
  //This does the same thing that the above function does.
  public function CheckGraduationValidityDegreeRequirements() {
    $returnarray = [];

    $completedcourses = App\Models\Completedcourse::where('student_id', $student->id)->get();
    $degreerequirements = App\Models\Degreerequirement::where('degreeprogram_id', $plan->degreeprogram_id)->get();

    foreach($degreerequirements as $degreerequirement) {

      if(!$completedcourses.contains('name', $degreerequirement->course_name)) {

        $returnarray->push($degreerequirement);
      }
    }
    return $returnarray;
  }

  public function CheckGraduationValidityPlan() {
    $returnarray = [];

    $completedcourses = App\Models\Completedcourse::where('student_id', $student->id)->get();
    $planrequirements = App\Models\Planrequirements::where('plan_id', $plan->id)->get();

    foreach($planrequirements as $planrequirement) {

      if(!$completedcourses.contains('name', $planrequirement->course_name)) {

        $returnarray->push($planrequirement);
      }
    }
    return $returnarray;
  }
?>
