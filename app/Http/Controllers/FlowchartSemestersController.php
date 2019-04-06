<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FlowchartSemestersController extends Controller
{

    public function __construct()
    {
        $this->middleware('cas');
        $this->middleware('update_profile');
    }

    public function getSemesters(Request $request, Plan $plan)
    {
        $this->authorize('read', $plan);

        $semesters = $plan->semesters()->orderBy('ordering')->get();
        $resource = new Collection();
        foreach ($semesters as $semester) {
            $resource->push([
                'id' => $semester->id,
                'name' => $semester->name,
                'courses' => [],
            ]);
        }

        return response()->json($resource);
    }

    public function renameSemester(Request $request, Plan $plan, Semester $semester)
    {
        $this->authorize('modify', $plan);

        if ($semester->plan_id !== $plan->id) {
            //semester id does not match plan id given
            abort(404);
        }

        $semester->name = $request->input('name');
        $semester->save();
        return response()->json(trans('messages.item_saved'));
    }

    public function deleteSemester(Request $request, Plan $plan, Semester $semester)
    {
        $this->authorize('modify', $plan);

        if ($semester->plan_id !== $plan->id) {
            abort(404);
        }

        $semester->delete();
        return response()->json(trans('messages.item_deleted'));
    }

    public function addSemester(Request $request, Plan $plan)
    {
        $this->authorize('modify', $plan);

        $semester = new Semester();
        $semester->plan_id = $plan->id;
        $semester->name = "New Semester";
        $semester->ordering = $plan->semesters->max('ordering') + 1;
        $semester->save();
        $plan->DynamicallyRenameSemesters();
        $resource = [
            'id' => $semester->id,
            'name' => $semester->name,
            'ordering' => $semester->ordering,
            'courses' => [],
        ];

        return response()->json($resource);
    }

    public function postSemesterSetSummer(Request $request, $id = -1)
    {
        //What if I change this to be an alert, where the user can press Summer or not.
        //I think this may work.
        if ($id < 0) {
            abort(404);
        }

        $plan = Plan::with('semesters')->findOrFail($id);

        $this->authorize('modify', $plan);

        $semester = Semester::findOrFail($request->input('id'));

        $lastSemester = Semester::where('plan_id', $plan->id)->orderby('ordering', 'DESC')->first();
        $seasonYear = explode(' ', $lastSemester->name);
        $year = $seasonYear[1];
        if ($seasonYear[0] == "Fall") {
            $seasonYear[1]++;
        }

        if ($semester->plan_id == $id) {
            $semester->name = "Summer " . $year;// . $semester->year();
            $semester->save();
            return;
        } else {
            //semester id does not match plan id given
            abort(404);
        }
    }

    public function moveSemester(Request $request, Plan $plan)
    {
        $this->authorize('modify', $plan);

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
            $semester->ordering -= +$offset;
            $semester->save();
        }
        DB::commit();

        $plan->DynamicallyRenameSemesters();
        return response()->json(trans('messages.item_saved'));
    }
}
