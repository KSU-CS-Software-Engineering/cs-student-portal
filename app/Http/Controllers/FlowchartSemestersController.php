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

    public function setSummer(Request $request, Plan $plan, Semester $semester)
    {
        $this->authorize('modify', $plan);

        if ($semester->plan_id !== $plan->id) {
            abort(404);
        }

        $seasonYear = explode(' ', $semester->name);
        $seasonYear[0] = 'Summer';
        $semester->name = implode(' ', $seasonYear);
        $semester->save();

        return response()->json(trans('messages.item_saved'));
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
