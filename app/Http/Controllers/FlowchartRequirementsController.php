<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Planrequirement;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;

class FlowchartRequirementsController extends Controller
{

    public function __construct()
    {
        $this->middleware('cas');
        $this->middleware('update_profile');
    }

    public function getCourses(Request $request, Plan $plan)
    {
        $this->authorize('read', $plan);

        $requirements = $plan->requirements()->orderBy('ordering')->get();
        $requirements->load('electivelist');
        $requirements->load('course');
        $requirements->load('completedcourse');
        $resource = new Collection();
        foreach ($requirements as $requirement) {
            $resource->push([
                'id' => $requirement->id,
                'notes' => $requirement->notes,
                'semester_id' => $requirement->semester_id,
                'credits' => $requirement->credits,
                'name' => $requirement->course_name,
                'electivelist_name' => $requirement->electivelist !== null ? $requirement->electivelist->name : null,
                'electivelist_abbr' => $requirement->electivelist !== null ? $requirement->electivelist->abbreviation : null,
                'electivelist_id' => $requirement->electivelist_id,
                'degreerequirement_id' => $requirement->degreerequirement_id,
                'course_name' => $requirement->course !== null ? $requirement->course->fullTitle : null,
                'course_id' => $requirement->course_id,
                'completedcourse_name' => $requirement->completedcourse !== null ? $requirement->completedcourse->fullTitle : null,
                'completedcourse_id' => $requirement->completedcourse_id,
                'course_id_lock' => $requirement->course_id_lock === 1 ? true : false,
                'completedcourse_id_lock' => $requirement->completedcourse_id_lock === 1 ? true : false,
            ]);
        }

        return response()->json($resource);
    }

    public function moveRequirement(Request $request, Plan $plan, Planrequirement $requirement)
    {
        $this->authorize('modify', $plan);

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
        $this->authorize('modify', $plan);

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
        $this->authorize('modify', $plan);

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
        $this->authorize('modify', $plan);

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
        if ($requirement->degreerequirement !== null) {
            // is not custom, so only certain fields can be updated
            $allowedFields = [
                'notes',
                'completedcourse_id',
                'course_id',
                'course_id_lock',
                'completedcourse_id_lock',
            ];

            if ($requirement->electivelist !== null) {
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
