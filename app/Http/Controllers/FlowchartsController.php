<?php

namespace App\Http\Controllers;

use App\Helpers\JsonSerializer;
use App\Models\Degreeprogram;
use App\Models\Plan;
use App\Models\Planrequirement;
use App\Models\Section;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Electivelistcourse;
use App\Models\Course;
use App\Rules\VerifySemester;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

//This is my dependency that I've added.
use App\Rules\VerifyFourYearPlan;
use App\scrapers\KSUCourseScraper;

class FlowchartsController extends Controller
{

    private $fractal;

    public function __construct()
    {
        $this->middleware('cas');
        $this->middleware('update_profile');
        $this->fractal = new Manager();
    }

    /**
     * Responds to requests to GET /courses
     */
    public function getIndex($id = -1){
        $user = Auth::user(); //I think this gets the user in question.


        if ($id < 0) {
            //no particular student requested
            if ($user->is_advisor) {
                //if advisor, show all students
                $students = $user->advisor->students;
                $students->load('plans');
                return view('flowcharts/studentlist')->with('students', $students);
            } else {
                //if student, show their plans
                $plans = $user->student->plans;
                return view('flowcharts/index')->with('plans', $plans)->with('student', $user->student);
            }
        } else {
            //currently authenticated user is an advisor and selecting a particular student
            if ($user->is_advisor) {
                //since they are an advisor, show that student's plans
                $student = Student::findOrFail($id);
                $plans = $student->plans;
                return view('flowcharts/index')->with('plans', $plans)->with('student', $student);
                //
            } else {
                //if they are not an advisor, redirect to their own page
                return redirect('flowcharts');
            }
        }
    }

    public function getFlowchart($id = -1)
    {
        if ($id < 0) {
            //no ID provided - redirect back to index
            return redirect('flowcharts/index');
        } else {
            $user = Auth::user();
            $plan = Plan::findOrFail($id);
            self::getRelatedSections($plan);
            $planreqs = self::CheckGradPlanRules($plan);
            $CISreqs = self::CheckCISReqRules($plan);
            $hours = self::CheckHoursRules($plan);
            $prereqs = self::CheckPreReqRules($plan);
            $courseplacement = self::CheckCoursePlacement($plan);
            $kstate = self::CheckKState8($plan); //Should all of these change to be the UpdatedView()?

            if ($user->is_advisor) {
                return view('flowcharts/flowchart')
                    ->with('plan', $plan)
                    ->with('planreqs',$planreqs)
                    ->with('CISreqs', $CISreqs)
                    ->with('hours',$hours)
                    ->with('prereqs',$prereqs)
                    ->with('courseplacement',$courseplacement)
                    ->with('kstate',$kstate);
            } else {
                if ($plan->student_id == $user->student->id) {
                    return view('flowcharts/flowchart')->with('plan', $plan);
                } else {
                    abort(404);
                }
            }
        }
    }

    public function newFlowchart($id = -1)
    {
        if ($id < 0) {
            abort(404);
        } else {
            $user = Auth::user();
            $plan = new Plan();
            if ($user->is_advisor) {
                $student = Student::findOrFail($id);
                $plan->student_id = $student->id;
            } else {
                $plan->student_id = $user->student->id;
            }
            $degreeprograms = Degreeprogram::orderBy('name', 'asc')->get();
            $degreeprogramUnknown = new Degreeprogram();
            $degreeprogramUnknown->name = "Unassigned";
            $degreeprogramUnknown->id = 0;
            $degreeprograms->prepend($degreeprogramUnknown);
            $semesters = collect([
                (object) ['id' => 0, 'name' => 'Unassigned'],
                (object) ['id' => 1, 'name' => 'Spring'],
                (object) ['id' => 2, 'name' => 'Summer'],
                (object) ['id' => 3, 'name' => 'Fall'],
            ]);
            return view('flowcharts.edit')
                ->with('plan', $plan)
                ->with('semesters', $semesters)
                ->with('degreeprograms', $degreeprograms);
        }
    }

    public function saveNewFlowchart($id = -1, Request $request)
    {
        //Rules processing here.
        //getUserCourses();from completed courses.
        //CheckRules():
        //Send the output of CheckRules to here.
        if ($id < 0) {
            abort(404);
        } else {
            $user = Auth::user();
            $plan = new Plan();
            $data = $request->all();
            if ($user->is_advisor) {
                $student = Student::findOrFail($id);
                $data['student_id'] = $student->id;
            } else {
                $data['student_id'] = $user->student->id;
            }

            if ($plan->validate($data)) {
                //var_dump($data);
                //Check user classses
                //$userClasses = $user->GetClasses(); return as JSON.
                //Take user class data, check against rules
                //$userRulesResults = $plan->checkRules($userClasses); TAKE JSON, FORCE THROUGH RULES
                //Have the plan know whether or not degree requirements are met.
                $plan->fill($data);
                $plan->save();
                $plan->fillRequirementsFromDegree();
                //Check taken against requirements.
                $request->session()->put('message', trans('messages.item_saved'));
                $request->session()->put('type', 'success');
                return response()->json(url('flowcharts/view/' . $plan->id));
            } else {
                return response()->json($plan->errors(), 422);
            }
        }
    }

    public function resetFlowchart(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:plans',
        ]);
        $user = Auth::user();
        $plan = Plan::findOrFail($request->input('id'));
        if ($user->is_advisor || (!$user->is_advisor && $user->student->id == $plan->student_id)) {
            $plan->removeRequirements();
            $plan->fillRequirementsFromDegree();
            return response()->json(trans('messages.item_populated'));
        } else {
            abort(404);
        }
    }

    public function deleteFlowchart(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:plans',
        ]);
        $user = Auth::user();
        $plan = Plan::findOrFail($request->input('id'));
        if ($user->is_advisor || (!$user->is_advisor && $user->student->id == $plan->student_id)) {
            $plan->delete();
            $request->session()->put('message', trans('messages.item_deleted'));
            $request->session()->put('type', 'success');
            return response()->json(trans('messages.item_deleted'));
        } else {
            abort(404);
        }
    }

    public function editFlowchart($id = -1)
    {
        if ($id < 0) {
            abort(404);
        } else {
            $user = Auth::user();
            $plan = Plan::findOrFail($id);
            if ($user->is_advisor || (!$user->is_advisor && $user->student->id == $plan->student_id)) {
                $degreeprograms = Degreeprogram::orderBy('name', 'asc')->get();
                $degreeprogramUnknown = new Degreeprogram();
                $degreeprogramUnknown->name = "Unassigned";
                $degreeprogramUnknown->id = 0;
                $degreeprograms->prepend($degreeprogramUnknown);
                $semesters = collect([
                    (object) ['id' => 0, 'name' => 'Unassigned'],
                    (object) ['id' => 1, 'name' => 'Spring'],
                    (object) ['id' => 2, 'name' => 'Summer'],
                    (object) ['id' => 3, 'name' => 'Fall'],
                ]);
                return view('flowcharts.edit')
                    ->with('plan', $plan)
                    ->with('semesters', $semesters)
                    ->with('degreeprograms', $degreeprograms);
            } else {
                abort(404);
            }
        }
    }

    public function saveFlowchart($id = -1, Request $request)
    {
        if ($id < 0) {
            abort(404);
        } else {
            $user = Auth::user();
            $plan = Plan::findOrFail($id);
            self::getRelatedSections($plan);
            $data = $request->all();
            if ($user->is_advisor || (!$user->is_advisor && $user->student->id == $plan->student_id)) {
                $data['student_id'] = $plan->student_id;
                if ($plan->validate($data)) {
                    $plan->fill($data);
                    $plan->save(); //This writes to the DB. do the rules checking before this.
                    return response()->json(trans('messages.item_saved'));
                } else {
                    return response()->json($plan->errors(), 422);
                }
            } else {
                abort(404);
            }
        }
    }

    public function getFlowchartData(Request $request, $id = -1)
    {
        if ($id < 0) {
            abort(404);
        }
        $plan = Plan::findOrFail($id);
        return $this->getCourses($request, $plan);
    }

    public function getCourses(Request $request, Plan $plan) {
        $user = Auth::user();
        if (!$user->is_advisor && $user->student->id !== $plan->student_id) {
            abort(404);
        }

        $requirements = $plan->requirements()->orderBy('ordering')->get();
        $requirements->load('electivelist');
        $requirements->load('course');
        $requirements->load('completedcourse');
        $resource = new Collection($requirements, function (Planrequirement $requirement) {
            return [
                'id' => $requirement->id,
                'notes' => $requirement->notes,
                'semester_id' => $requirement->semester_id,
                'credits' => $requirement->credits,
                'name' => $requirement->course_name,
                'electivelist_name' => $requirement->electivelist()->exists() ? $requirement->electivelist->name : null,
                'electivelist_abbr' => $requirement->electivelist()->exists() ? $requirement->electivelist->abbreviation : null,
                'electivelist_id' => $requirement->electivelist_id,
                'degreerequirement_id' => $requirement->degreerequirement_id,
                'course_name' => $requirement->course()->exists() ? $requirement->course->fullTitle : null,
                'course_id' => $requirement->course_id,
                'completedcourse_name' => $requirement->completedcourse()->exists() ? $requirement->completedcourse->fullTitle : null,
                'completedcourse_id' => $requirement->completedcourse_id,
                'course_id_lock' => $requirement->course_id_lock === 1 ? true : false,
                'completedcourse_id_lock' => $requirement->completedcourse_id_lock === 1 ? true : false,
            ];
        });
        $this->fractal->setSerializer(new JsonSerializer());
        return $this->fractal->createData($resource)->toJson();
    }

    public function getSemesterData(Request $request, $id = -1)
    {
        if ($id < 0) {
            abort(404);
        }
        $plan = Plan::findOrFail($id);
        return $this->getSemesters($request, $plan);
    }

    public function getSemesters(Request $request, Plan $plan) {
        $user = Auth::user();
        if (!$user->is_advisor && $user->student->id !== $plan->student_id) {
            abort(404);
        }

        $semesters = $plan->semesters()->orderBy('ordering')->get();
        $resource = new Collection($semesters, function ($semester) {
            return [
                'id' => $semester->id,
                'name' => $semester->name,
                'courses' => [],
            ];
        });
        $this->fractal->setSerializer(new JsonSerializer());
        return $this->fractal->createData($resource)->toJson();
    }

    public function postSemesterSave(Request $request, $id = -1)
    {
        if ($id < 0) {
            //id not found
            abort(404);
        } else {
            $user = Auth::user();
            $plan = Plan::with('semesters')->findOrFail($id);
            self::getRelatedSections($plan);
            if ($user->is_advisor || (!$user->is_advisor && $user->student->id == $plan->student_id)) {
                $semester = Semester::findOrFail($request->input('id'));
                if ($semester->plan_id == $id) {
                    $semester->name = $request->input('name');
                    $semester->save();
                    return response()->json(trans('messages.item_saved'));
                } else {
                    //semester id does not match plan id given
                    abort(404);
                }
            } else {
                //cannot edit a plan if you aren't the student or an advisor
                abort(404);
            }
        }
    }

    public function renameSemester(Request $request, Plan $plan, Semester $semester) {
        $user = Auth::user();
        if (!$user->is_advisor && $user->student->id !== $plan->student_id) {
            //cannot edit a plan if you aren't the student or an advisor
            abort(404);
        }
        if ($semester->plan_id !== $plan->id) {
            //semester id does not match plan id given
            abort(404);
        }

        $semester->name = $request->input('name');
        $semester->save();
        return response()->json(trans('messages.item_saved'));
    }

    public function postSemesterDelete(Request $request, $id = -1)
    {
        if ($id < 0) {
            //id not found
            abort(404);
        }
        $plan = Plan::findOrFail($id);
        $semester = Semester::findOrFail($request->input('id'));
        return $this->deleteSemester($request, $plan, $semester);
    }

    public function deleteSemester(Request $request, Plan $plan, Semester $semester)
    {
        $user = Auth::user();
        if (!$user->is_advisor && $user->student->id !== $plan->student_id) {
            abort(404) ;
        }
        if ($semester->plan_id !== $plan->id) {
            abort(404);
        }

        $rules = $this->UpdatedView($plan);

        $semester->delete();
        return response()->json(trans('messages.item_deleted'));
    }

    public function postSemesterAdd(Request $request, $id = -1)
    {
        if ($id < 0) {
            //id not found
            abort(404);
        }
        $plan = Plan::findOrFail($id);
        return $this->addSemester($request, $plan);
    }

    public function addSemester(Request $request, Plan $plan) {
        $user = Auth::user();
        if (!$user->is_advisor && $user->student->id !== $plan->student_id) {
            //cannot edit a plan if you aren't the student or an advisor
            abort(404);
        }

        $semester = new Semester();
        $semester->plan_id = $plan->id;
        $semester->name = "New Semester";
        $semester->ordering = $plan->semesters->max('ordering') + 1;
        $semester->save();
        $plan->DynamicallyRenameSemesters();
        $resource = new Item($semester, function ($semester) {
            return [
                'id' => $semester->id,
                'name' => $semester->name,
                'ordering' => $semester->ordering,
                'courses' => [],
            ];
        });
        $this->fractal->setSerializer(new JsonSerializer());
        return $this->fractal->createData($resource)->toJson();
    }

    public function postSemesterSetSummer(Request $request, $id = -1)
    {
        //What if I change this to be an alert, where the user can press Summer or not.
        //I think this may work.
        if ($id < 0) {
            abort(404);
        }
        else {
            $user = Auth::user();
            $plan = Plan::with('semesters')->findOrFail($id);
            $semester = Semester::findOrFail($request->input('id'));

            $lastSemester = Semester::where('plan_id', $plan->id)->orderby('ordering', 'DESC')->first();
            $seasonYear = explode(' ', $lastSemester->name);
            $year = $seasonYear[1];
            if ($seasonYear[0] == "Fall") {
                $seasonYear[1]++;
            }

            self::getRelatedSections($plan);
            $planreqs = self::CheckGradPlanRules($plan);
            $CISreqs = self::CheckCISReqRules($plan);
            $hours = self::CheckHoursRules($plan);
            $prereqs = self::CheckPreReqRules($plan);
            $courseplacement = self::CheckCoursePlacement($plan);


            if ($user->is_advisor || (!$user->is_advisor && $user->student->id == $plan->student_id)) {

                if ($semester->plan_id == $id) {
                    $semester->name = "Summer " . $year;// . $semester->year();
                    $semester->save();
                    //  $window.location.reload();
                    return; //view('flowcharts/flowchart')->with('plan', $plan)->with('planreqs',$planreqs)->with('CISreqs', $CISreqs)->with('hours',$hours)->with('prereqs',$prereqs)->with('courseplacement',$courseplacement);
                } else {
                    //semester id does not match plan id given
                    abort(404);
                }
            } else {
              abort(404);
            }
        }
      }

    public function postSemesterMove(Request $request, $id = -1)
    {
        if ($id < 0) {
            //id not found
            abort(404);
        }
        $plan = Plan::findOrFail($id);
        return $this->moveSemester($request, $plan);
    }

    public function moveSemester(Request $request, Plan $plan) {
        $user = Auth::user();
        if (!$user->is_advisor && $user->student->id != $plan->student_id) {
            abort(404);
        }

        $rules = $this->UpdatedView($plan);
        $semesters = $plan->semesters;
        $orderings = $request->input('ordering');

        if ($semesters->count() !== count($orderings)) {
            abort(404);
        }

        $offset = $semesters->max('ordering') + 1;

        DB::beginTransaction();
        foreach ($orderings as $ordering) {
            $semester = $semesters->where('id', $ordering['id'])->first();
            $semester->ordering = $ordering['ordering'] + $offset;
            $semester->save();
        }
        foreach ($orderings as $ordering) {
            $semester = $semesters->where('id', $ordering['id'])->first();
            $semester->ordering -= + $offset;
            $semester->save();
        }
        DB::commit();

        $plan->DynamicallyRenameSemesters();
        return response()->json(trans('messages.item_saved'));
    }

    public function moveRequirement(Request $request, Plan $plan, Planrequirement $requirement)
    {
        $user = Auth::user();
        if (!$user->is_advisor && $user->student->id !== $plan->student_id) {
            //cannot edit a plan if you aren't the student or an advisor
            abort(404);
        }

        //move requirement to new semester
        if ($requirement->plan_id !== $plan->id) {
            //can't move course not on the plan;
            abort(404);
        }

        $semester = Semester::findOrFail($request->input('semesterId'));
        if ($semester->plan_id !== $plan->id) {
            //can't move course to semester not on the plan;
            abort(404);
        }

        //move requirement to new semester
        if ($requirement->semester_id !== $semester->id) {
            $oldSemester = $requirement->semester;
            $maxOrder = $semester->requirements->count();
            $requirement->semester_id = $semester->id;
            $requirement->ordering = $maxOrder;
            $requirement->save();
            $oldSemester->repairRequirementsOrder();
        }

        $newOrder = collect($request->input('order'));

        $semester->reorderRequirements($newOrder);

        $rules = $this->UpdatedView($plan);

        return response()->json(trans('messages.item_saved'));
    }

    public function addRequirement(Request $request, Plan $plan)
    {
        $user = Auth::user();
        if (!$user->is_advisor && $user->student->id !== $plan->student_id) {
            // cannot edit a plan if you aren't the student or an advisor
            abort(404);
        }

        $requirement = new Planrequirement();
        if (!$requirement->customEditValidate($request->all(), [$plan->student_id])) {
            return response()->json($requirement->errors(), 422);
        }

        $semester = $plan->semesters->sortBy('ordering')->last();
        $requirement->fill($request->all());
        $requirement->semester()->associate($semester);
        $requirement->ordering = $semester->requirements->sortBy('ordering')->last()->ordering + 1;
        $requirement->plan()->associate($plan);
        $requirement->save();
        return response()->json(trans('messages.item_saved'));
    }

    public function updateRequirement(Request $request, Plan $plan, Planrequirement $requirement)
    {
        $user = Auth::user();
        if (!$user->is_advisor && $user->student->id !== $plan->student_id) {
            // cannot edit a plan if you aren't the student or an advisor
            abort(404);
        }
        if ($requirement->plan_id !== $plan->id) {
            // can't edit course not on the plan;
            abort(404);
        }

        $data = $this->filterRequirementEditData($request, $requirement);

        if (!$requirement->validateEdit($data, [$plan->student_id])) {
            return response()->json($requirement->errors(), 422);
        }

        $requirement->fill($data);
        if (!$requirement->validateElectiveCourse()) {
            $errors = new MessageBag();
            $errors->add('course_name', 'Course is not listed in selected elective list');
            return response()->json($errors, 422);
        }

        $requirement->save();
        return response()->json(trans('messages.item_saved'));
    }

    public function deleteRequirement(Request $request, Plan $plan, Planrequirement $requirement)
    {
        $user = Auth::user();
        if (!$user->is_advisor && $user->student->id !== $plan->student_id) {
            //cannot edit a plan if you aren't the student or an advisor
            abort(404);
        }
        if ($requirement->plan_id !== $plan->id) {
            //can't edit course not on the plan;
            abort(404);
        }
        if ($requirement->degreerequirement_id !== null) {
            return response()->json(trans('errors.default_req'), 403);
        }

        $requirement->delete();

        return response()->json(trans('messages.item_deleted'));
    }

    public function filterRequirementEditData(Request $request, Planrequirement $requirement)
    {
        if ($requirement->degreerequirement()->exists()) {
            // is not custom, so only certain fields can be updated
            $allowedFields = [
                'notes',
                'completedcourse_id',
                'course_id',
                'course_id_lock',
                'completedcourse_id_lock',
            ];

            if ($requirement->electivelist()->exists()) {
                // has elective list, so course name can also be changed
                $allowedFields += [
                    'course_name',
                ];
            }

            return $request->only($allowedFields);
        }

        return $request->all();
    }

    public static function CheckCISReqRules(Plan $plan) {

        $firstArrs = [];
        //Set the variables for the rules case
        $rules = new VerifyFourYearPlan();

        //Check the first one.
        $firstArrs = $rules->CheckCISRequirementsPlan($plan);

        return $firstArrs;


    }

    public static function CheckGradPlanRules(Plan $plan){

        //Check the second one.
        //This handles graduation ability, not validity of the plan, so no flag.
        $planreqs = [];
        $rules = new VerifyFourYearPlan();
        $planreqs = $rules->CheckGraduationValidityPlan($plan);

        return $planreqs;

    }

    public static function CheckGradRequirementsRules(Plan $plan){

        //Check the third one.
        //This handles graduation ability, not validity of the plan, so no fla
        $array = [];
        $rules = new VerifyFourYearPlan();
        $array = $rules->CheckGraduationValidityDegreeRequirements($plan);

    }

    public static function CheckHoursRules(Plan $plan) {
        $rules = new VerifySemester();

        //returns true if correct number of hours and false if not
        //if not correct number of hours displays an alert
        $correcthours = $rules->CheckHours($plan);
        return $correcthours;
    }

    public static function CheckPreReqRules(Plan $plan) {

        $rules = new VerifySemester();
        //returns an array with the missing prereqs or empty if all good
        $prereqs = $rules->CheckPreReqs($plan);
        return $prereqs;

    }

    public static function CheckCoursePlacement(Plan $plan){


        $rules = new VerifySemester();
        $courseplacement = $rules->CheckCoursePlacement($plan);
        return $courseplacement;

    }

    public static function CheckKState8(Plan $plan) {
        $rules = new VerifyFourYearPlan();
        $kstate8 = $rules->CheckKstate8($plan);
        return $kstate8;
    }

    public function UpdatedView(Plan $plan){

        $planreqs = self::CheckGradPlanRules($plan);
        $CISreqs = self::CheckCISReqRules($plan);
        $hours = self::CheckHoursRules($plan);
        $prereqs = self::CheckPreReqRules($plan);
        $courseplacement = self::CheckCoursePlacement($plan);
        $kstate = self::CheckKState8($plan);

        return view('flowcharts/flowchart')->with('plan', $plan)->with('planreqs',$planreqs)->with('CISreqs', $CISreqs)->with('hours',$hours)->with('prereqs',$prereqs)->with('courseplacement',$courseplacement)->with('kstate',$kstate);

    }

    //return like getSemester
    public function getRelatedSections(Plan $plan){

        $courses = [];
        $elective_id = [];
        //$electiveListCourse = new Electivelistcourse();
        $currentSemester = $plan->semesters[0];
        $classesForSemester = Planrequirement::where('plan_id', $plan->id)->where('semester_id', $currentSemester->id)->get();

        /*adds all class id's to courses array
        if no class id, get elective list id */
        foreach($classesForSemester as $class){
            if($class->course_id == NULL){
                array_push($elective_id, $class->electivelist_id);
            }
            else{
                array_push($courses, $class->course_id);
            }
        }

        if(count($elective_id) > 1) {
            //goes through every elective student has in the semester
            foreach ($elective_id as $id) {

                $electiveCourses = Electivelistcourse::where('electivelist_id',$id)->get();

                foreach($electiveCourses as $electiveCourse) {

                    array_push($courses, $electiveCourse);
                }
            }
        }
        else{
            //only add for the one elective
            $electiveCourses = Electivelistcourse::where('electivelist_id', $elective_id);
            foreach ($electiveCourses as $electiveCourse){
                array_push($courses, $electiveCourse);
            }
        }


        $betterArray = [];
        $returnArray = [];

        /*takes courses student had and elective courses in semester
          gets the times they are offered*/
        foreach($courses as $course){
            if(is_int($course)){
                array_push($returnArray, Section::where('course_id', $course)->get());
                //dd($returnArray);
            }
            else{
               // array_push($betterArray, $course->);

               // $courseTimes = Section::where('course_number',$course-> )->get();

            }
        }

        //dd($returnArray);
        return $returnArray;
    }


}
