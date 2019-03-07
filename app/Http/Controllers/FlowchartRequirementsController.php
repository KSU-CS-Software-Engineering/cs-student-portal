<?php

namespace App\Http\Controllers;

use App\Helpers\JsonSerializer;
use App\Models\Plan;
use App\Models\Planrequirement;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class FlowchartRequirementsController extends Controller
{

    private $fractal;

    public function __construct()
    {
        $this->middleware('cas');
        $this->middleware('update_profile');
        $this->fractal = new Manager();
    }

    public function getFlowchartData(Request $request, $id = -1)
    {
        if ($id < 0) {
            abort(404);
        }
        $plan = Plan::findOrFail($id);
        return $this->getCourses($request, $plan);
    }

    public function getCourses(Request $request, Plan $plan)
    {
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
                'electivelist_abbr' => $requirement->electivelist()
                    ->exists() ? $requirement->electivelist->abbreviation : null,
                'electivelist_id' => $requirement->electivelist_id,
                'degreerequirement_id' => $requirement->degreerequirement_id,
                'course_name' => $requirement->course()->exists() ? $requirement->course->fullTitle : null,
                'course_id' => $requirement->course_id,
                'completedcourse_name' => $requirement->completedcourse()
                    ->exists() ? $requirement->completedcourse->fullTitle : null,
                'completedcourse_id' => $requirement->completedcourse_id,
                'course_id_lock' => $requirement->course_id_lock === 1 ? true : false,
                'completedcourse_id_lock' => $requirement->completedcourse_id_lock === 1 ? true : false,
            ];
        });
        $this->fractal->setSerializer(new JsonSerializer());
        return $this->fractal->createData($resource)->toJson();
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
}
