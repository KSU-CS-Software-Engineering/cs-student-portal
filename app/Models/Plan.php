<?php

namespace App\Models;

use App\Rules\VerifyFourYearPlan;
use App\Rules\VerifySemester;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    public function student()
    {
        return $this->belongsTo('App\Models\Student')->withTrashed();
    }


    //public function checkRules() check to see if degree requirements are met.
    //If rules are all met, allow graduation.
  //}


    public function degreeprogram()
    {
        return $this->belongsTo('App\Models\Degreeprogram')->withTrashed();
    }

    public function requirements()
    {
        return $this->hasMany('App\Models\Planrequirement');
    }

    public function semesters()
    {
        return $this->hasMany('App\Models\Semester');
    }

    public function getStarttextAttribute()
    {
        switch ($this->start_semester) {
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

    public function removeRequirements()
    {
        foreach ($this->requirements as $requirement) {
            $requirement->delete();
        }
        foreach ($this->semesters as $semester) {
            $semester->delete();
        }
    }

    public function fillRequirementsFromDegree()
    {
        $degreeprogram = $this->degreeprogram;
        $maxSemester = $degreeprogram->requirements->max('semester');
        $sem = $this->start_semester;
        $year = $this->start_year;
        $order = 0;
        $semesters = array();
        if ($sem == 0) {
            $sem = 3;
        }
        if ($sem == 2) {
            $semester = new Semester();
            $semester->name = "Summer " . $year;
            $semester->ordering = $order++;
            $semester->plan_id = $this->id;
            $sem = 3;
            $semester->save();
            $semesters[$maxSemester + 1] = $semester->id;
        }
        for ($i = 0; $i <= $maxSemester; $i++) {
            if ($sem == 1) {
                $semester = new Semester();
                $semester->name = "Spring " . $year;
                $semester->ordering = $order++;
                $semester->plan_id = $this->id;
                $sem = 3;
                $semester->save();
                $semesters[$i] = $semester->id;
            } elseif ($sem == 3) {
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
        foreach ($degreeprogram->requirements as $requirement) {
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

    private function fillSemesters()
    {
        $maxSemester = $this->requirements->max('semester');
        $sem = $this->start_semester;
        $year = $this->start_year;
        $order = 0;
        if ($sem == 2) {
            $semester = new Semester();
            $semester->name = "Summer " . $year;
            $semester->number = $maxSemester + 1;
            $semester->ordering = $order++;
            $semester->plan_id = $this->id;
            $sem = 3;
            $semester->save();
        }
        for ($i = 0; $i <= $maxSemester; $i++) {
            if ($sem == 1) {
                $semester = new Semester();
                $semester->name = "Spring " . $year;
                $semester->number = $i;
                $semester->ordering = $order++;
                $semester->plan_id = $this->id;
                $sem = 3;
                $semester->save();
            } elseif ($sem == 3) {
                $semester = new Semester();
                $semester->name = "Fall " . $year;
                $semester->number = $i;
                $semester->ordering = $order++;
                $semester->plan_id = $this->id;
                $sem = 1;
                $year++;
                $semester->save();
            }
        }
    }

    public function getErrors() {
        return [
            // [
            //     'title' => 'Not finished courses',
            //     'errors' => $this->CheckGradPlanRules(),
            // ],
            [
                'title' => 'Courses missing',
                'errors' => $this->CheckCISReqRules(),
            ],
            [
                'title' => '',
                'errors' => $this->CheckHoursRules(),
            ],
            [
                'title' => 'Prerequisities missing',
                'errors' => $this->CheckPreReqRules(),
            ],
            [
                'title' => 'Courses not offered in its current semester placement',
                'errors' => $this->CheckCoursePlacement(),
            ],
            [
                'title' => 'K-State 8 Requirements Missing',
                'errors' => $this->CheckKState8(),
            ],
        ];
    }

    public function CheckCISReqRules()
    {
        $firstArrs = [];
        //Set the variables for the rules case
        $rules = new VerifyFourYearPlan();

        //Check the first one.
        $firstArrs = $rules->CheckCISRequirementsPlan($this);

        return $firstArrs;
    }

    public function CheckGradPlanRules()
    {
        //Check the second one.
        //This handles graduation ability, not validity of the plan, so no flag.
        $planreqs = [];
        $rules = new VerifyFourYearPlan();
        $planreqs = $rules->CheckGraduationValidityPlan($this);

        return $planreqs;
    }

    public function CheckGradRequirementsRules()
    {
        //Check the third one.
        //This handles graduation ability, not validity of the plan, so no fla
        $array = [];
        $rules = new VerifyFourYearPlan();
        $array = $rules->CheckGraduationValidityDegreeRequirements($this);
    }

    public function CheckHoursRules()
    {
        $rules = new VerifySemester();

        //returns true if correct number of hours and false if not
        //if not correct number of hours displays an alert
        $correcthours = $rules->CheckHours($this);
        return $correcthours;
    }

    public function CheckPreReqRules()
    {
        $rules = new VerifySemester();
        //returns an array with the missing prereqs or empty if all good
        $prereqs = $rules->CheckPreReqs($this);
        return $prereqs;
    }

    public function CheckCoursePlacement()
    {
        $rules = new VerifySemester();
        $courseplacement = $rules->CheckCoursePlacement($this);
        return $courseplacement;
    }

    public function CheckKState8()
    {
        $rules = new VerifyFourYearPlan();
        $kstate8 = $rules->CheckKstate8($this);
        return $kstate8;
    }
}
