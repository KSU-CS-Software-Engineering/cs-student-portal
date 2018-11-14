<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Planrequirement;
use App\Models\Degreeprogram;
use App\Models\Degreerequirement;
use App\Models\Semester;

class Plan extends Validatable
{
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    use SoftDeletes;

    protected $rules = array(
      'name' => 'required|string',
      'description' => 'required|string',
      'start_year' => 'required|integer|digits:4',
      'start_semester' => 'required|integer|between:0,3',
      'degreeprogram_id' => 'required|exists:degreeprograms,id',
      'student_id' => 'required|exists:students,id',
    );

    protected $fillable = ['name', 'description', 'start_year', 'start_semester', 'degreeprogram_id', 'student_id'];

    public function student(){
    	return $this->belongsTo('App\Models\Student')->withTrashed();
    }


    //public function checkRules() check to see if degree requirements are met.
    //If rules are all met, allow graduation.
  //}


    public function degreeprogram(){
        return $this->belongsTo('App\Models\Degreeprogram')->withTrashed();
    }

    public function requirements(){
    	return $this->hasMany('App\Models\Planrequirement');
    }

    public function semesters(){
      return $this->hasMany('App\Models\Semester');
    }

    public function getStarttextAttribute(){
        switch ($this->start_semester){
          case 1:
            return "Spring " . $this->start_year;
          case 2:
            return "Summer " . $this->start_year;
          case 3:
            return "Fall " . $this->start_year;
          default:
            return "Semester " . $this->start_semester . " " . $this->start_year;
        }
    }

    public function removeRequirements(){
      foreach($this->requirements as $requirement){
        $requirement->delete();
      }
      foreach($this->semesters as $semester){
        $semester->delete();
      }
    }

    public function fillRequirementsFromDegree(){
      //Get the degree program associated with the plan
      $degreeprogram = $this->degreeprogram;
      //Get the highest semester of all of the planrequirements. Where are these plan requirements set?
      $maxSemester = $degreeprogram->requirements->max('semester');
      //Set this semester variable to the start semester of the plan. This is chosen when creating the flowchar.
      $sem = $this->start_semester;
      //Same as above except setting thee year
      $year = $this->start_year;
      //This is a variable that is sets the ordering for the semesters.
      $order = 0;
      //Create an array that holds the semesters.
      $semesters = array();
      //If the sem variable (the start semester of the plan) == 0
      if($sem == 0){
        //Set the semester to 3. This maps it to be fall, I'm not sure what 0 is. Perhaps it's the default value if start semester is not chosen when creating the plan.
        $sem = 3;
      }
      //If the semester == 2 (This is a summer smeester I think)
      if($sem == 2){
        //create a new semester object
        $semester = new Semester();
        //Set the name of the semester object to be summer + the startyear
        $semester->name = "Summer " . $year;
        //Set the ordering of this semester to the order + 1. Does the semester ordering start at 0? Or does this change this for a certain reason
        $semester->ordering = $order++;
        //Set the plan id of this semester to this plan.
        $semester->plan_id = $this->id;
        //Set the semester to be 3. I assume this is to keep the loop moving?
        $sem = 3;
        //Save the new semester
        $semester->save();
        //Insert this summer semester into the end of the semesters array.
        $semesters[$maxSemester + 1] = $semester->id;
      }
      //For loop to hit all of the semesters.
      for($i = 0; $i <= $maxSemester; $i++){
        //If the sem == 1 (a sprint semester.)
        if($sem == 1){
          //Do the same stuff as the summer semester but with Spring in mind.
          $semester = new Semester();
          $semester->name = "Spring " . $year;
          $semester->ordering = $order++;
          $semester->plan_id = $this->id;
          //Set the semester to be fall, this is so on the next iteration it'll hit the fall one.
          $sem = 3;
          $semester->save();
          //Here it sets the semester in the correct order in this array. I wonder why this is like this.
          $semesters[$i] = $semester->id;
          //Do all of the same things as the other two but with fall in mind. Also increase the year since fall is the end of the year.
        }else if ($sem == 3){
          $semester = new Semester();
          $semester->name = "Fall " . $year;
          $semester->ordering = $order++;
          $semester->plan_id = $this->id;
          $sem = 1;
          $year++;
          $semester->save();
          $semesters[$i] = $semester->id;
        }
      }
      foreach($degreeprogram->requirements as $requirement){
        $data = collect($requirement->getAttributes())->except(['degreeprogram_id', 'semester'])->toArray();
        $data['semester_id'] = $semesters[$requirement->semester];
        $planrequirement = new Planrequirement();
        $planrequirement->fill($data);
        $planrequirement->degreerequirement_id = $requirement->id;
        $planrequirement->plan_id = $this->id;
        $planrequirement->save();
      }
    }

    //Should be able to call this whenever, since names won't change if they're correct.
    public function DynamicallyRenameSemesters() {
        //How do we want to do this?
        //First we need to get all of the semesters for this plan.
        //Then we need to place the new semester in its place in the semester array.
        //From there, do the above function, except for creating a new semester, just change the name. I think the order and stuff is handled elsewhere
        //This function will simply handle the renaming of the semesters.
        //Also need to take into consideration summer semesters.
        //The designation as a summer semester needs to be done before, so it can just access the semester property to create the proper name for it.

        //Get the degree program associated with the plan
        $degreeprogram = $this->degreeprogram;
        //Get the highest semester of all of the planrequirements. Where are these plan requirements set?
        $maxSemester = $degreeprogram->requirements->max('semester');
        //Set this semester variable to the start semester of the plan. This is chosen when creating the flowchar.
        $sem = $this->start_semester;
        //Same as above except setting thee year
        $year = $this->start_year;
        //This is a variable that is sets the ordering for the semesters.
        $order = 0;
        //Get all of the semesters for this plan.
        $semesters = Semester::Where('plan_id', $this->id)->orderBy('ordering', 'ASC')->get();



        foreach($semesters as $semester) {
            if(strpos($semester->name, "Summer") !== false) {
              $semester->name = "Summer " . $year;
              $sem = 3;
              $semester->save();
            }
            else if($sem == 1) {
              $semester->name = "Spring " . $year;
              //$semester->ordering = $order++;
              //$semester->plan_id = $this->id;
              //Set the semester to be fall, this is so on the next iteration it'll hit the fall one.
              $sem = 3;
              $semester->save();
              //Here it sets the semester in the correct order in this array. I wonder why this is like this.
              //$semesters[$i] = $semester->id;
            }
            else if($sem == 3) {
              $semester->name = "Fall " . $year;
              $sem = 1;
              $semester->save();
              $year++;
            }
            //If the next semester is 2 and the semester name is Summer.

        }

    }

    private function fillSemesters(){
      $maxSemester = $this->requirements->max('semester');
      $sem = $this->start_semester;
      $year = $this->start_year;
      $order = 0;
      if($sem == 2){
        $semester = new Semester();
        $semester->name = "Summer " . $year;
        $semester->number = $maxSemester + 1;
        $semester->ordering = $order++;
        $semester->plan_id = $this->id;
        //CheckPreReqs
        $sem = 3;
        $semester->save();
      }
      for($i = 0; $i <= $maxSemester; $i++){
        if($sem == 1){
          $semester = new Semester();
          $semester->name = "Spring " . $year;
          //CheckPreReqs
          $semester->number = $i;
          $semester->ordering = $order++;
          $semester->plan_id = $this->id;
          $sem = 3;
          $semester->save();
        }else if ($sem == 3){
          $semester = new Semester();
          $semester->name = "Fall " . $year;
          $semester->number = $i;
          //CheckPreReqs
          $semester->ordering = $order++;
          $semester->plan_id = $this->id;
          $sem = 1;
          $year++;
          $semester->save();
        }
      }
    }








}
