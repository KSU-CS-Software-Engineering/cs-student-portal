<?php

namespace App\Http\Controllers;

use App\Models\Degreeprogram;
use App\Models\Plan;
use App\Models\Student;
use Auth;
use Illuminate\Http\Request;

class FlowchartsController extends Controller
{

    public function __construct()
    {
        $this->middleware('cas');
        $this->middleware('update_profile');
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
        }

        $user = Auth::user();
        $plan = Plan::findOrFail($id);

        if (!$user->is_advisor && $plan->student_id !== $user->student->id) {
            abort(403);
        }

        return view('flowcharts/flowchart')
            ->with('plan', $plan);
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

    public function errors(Plan $plan)
    {
        return $plan->getErrors();
    }

}
