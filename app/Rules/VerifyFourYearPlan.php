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
use App\Models\kstate8;
use App\Models\Course;

class VerifyFourYearPlan {

  //Written and/or Implemented
  //Check plan for 4yr validity against KState and CS rules. (Degree Requirements) Written, implemented, and tested.

  //Check completed classes for graduation ability against KSU and CS Rules. Written, unimplemented, untested.
  //Check completed classes for completion of student's plan. Written, inimplemented, untested.

  //Unimplemented
  //Check Advisor flag.

  //This is tested and works as intended.
  public function CheckCISRequirementsPlan(Plan $plan) {
    $count = 0;
    //We need to test ot make sure all classes in Requirements are in plan
    //Create array to put the missing classes in.
    $returnarray = [];
    //Get all of the degree requirements in a collection.
    $degreerequirements = Degreerequirement::where('degreeprogram_id', $plan->degreeprogram_id)->get();
    //dd($degreerequirements->count());
    //Get all of the planned classes to compare
    $planrequirements = Planrequirement::where('plan_id', $plan->id)->get();
    //Iterate through the degree requirements testing each against the planned classes
    foreach($degreerequirements as $degreerequirement) {
      //If the degree requirement is not in the plan requirements
      if($planrequirements->contains('degreerequirement_id', $degreerequirement->id) == FALSE) {
        //Add the missing degree requirement to the array.
        $returnarray[$count] = $degreerequirement;
      }
      $count++;
    }
    //Return the array.
    return $returnarray;
  }

//We will want to split up the graduation ability check into two different functions
//One will check that it passes KSU & CS requiremtns and the other will cehck
//That the student has completed their plan.

  //This is tested.
  //This checks that the user has completed all of the required classes to graduate
  //This does the same thing that the above function does.
  public function CheckGraduationValidityDegreeRequirements(Plan $plan) {
    $returnarray = [];
    $count = 0;
    $student = Student::where('id', $plan->student_id)->get()[0];
    $completedcourses = Completedcourse::where('student_id', $student->id)->get();
    $degreerequirements = Degreerequirement::where('degreeprogram_id', $plan->degreeprogram_id)->get();

    foreach($degreerequirements as $degreerequirement) {
      if($completedcourses->contains('name', $degreerequirement->course_name) == false) {
        $returnarray[$count] = $degreerequirement;
      }
      $count++;
    }
    return $returnarray;
  }






  public function CheckGraduationValidityPlan(Plan $plan) {
    $returnarray = [];
    $student = Student::where('id', $plan->student_id)->get()[0];
    $completedcourses = Completedcourse::where('student_id', $student->id)->get();
    $planrequirements = Planrequirement::where('plan_id', $plan->id)->get();
    foreach($planrequirements as $planrequirement) {
      if($completedcourses->contains('name', $planrequirement->course_name) == false) {

        $returnarray[] = $planrequirement;
      }
    }
    return $returnarray;
  }





  public function CheckKstate8(Plan $plan) {
      $returnArray = ['You are missing the Aesthetic Interpretation K-State 8 requirement.', 'You are missing the Empirical and Quantitative Reasoning K-State 8 requirement.',
      'You are missing the Ethical Reasoning and Responsibility K-State 8 requirement.', 'You are missing the Global Issues and Perspectives K-State 8 requirement.',
      'You are missing the Historical Perpectives K-State 8 requirement.', 'You are missing the Human Diversity within the U.S. K-State 8 requirement.',
      'You are missing the Natural and Physical Sciences K-State 8 requirement.', 'You are missing the Social Sciences K-State 8 requirement.'];
      //Get all of the plan requirements.
      //Check all of those plan requirements get the courses against the Kstate8 then the areas.

      $count = 0;
      //Get the plan requirements
      $planRequirements = Planrequirement::where('plan_id', $plan->id)->get();
      //Get the course object for all of these classes.
      foreach($planRequirements as $planRequirement) {
          if($planRequirement->course_id != NULL) {
              $courseObject = Course::where('id', $planRequirement->course_id)->first();
              $courseArea = kstate8::where('course_id', $courseObject->id)->get();
              if($courseArea != NULL) {
                foreach($courseArea as $courseAreaSingular) {
                  //var_dump($courseObject->slug);
                  //var_dump($courseAreaSingular->area_id);
                  $returnArray[$courseAreaSingular->area_id - 1] = NULL;
                }
              }
          }
      }
      //dd($returnArray);
      return $returnArray;





  }
}
