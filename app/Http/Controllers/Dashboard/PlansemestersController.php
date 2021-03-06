<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class PlansemestersController extends Controller
{

    public function getPlansemestersForPlan(Request $request, $id = -1)
    {
        if ($id < 0) {
            abort(404);
        } else {
            $plan = Plan::withTrashed()->with('semesters')->findOrFail($id);
            $resource = new Collection();
            foreach ($plan->semesters as $semester) {
                $resource->push([
                    'id' => $semester->id,
                    'name' => $semester->name,
                    'ordering' => $semester->ordering,
                ]);
            }

            return response()->json($resource);
        }
    }

    public function getPlanSemester($id = -1)
    {
        if ($id < 0) {
            abort(404);
        } else {
            $semester = Semester::findOrFail($id);
            return view('dashboard.plansemesteredit')->with('semester', $semester)->with('plan_id', $semester->plan_id);
        }
    }

    public function getNewPlanSemester($id = -1)
    {
        if ($id < 0) {
            abort(404);
        } else {
            $semester = new Semester();
            return view('dashboard.plansemesteredit')->with('semester', $semester)->with('plan_id', $id);
        }
    }

    public function postNewPlanSemester(Request $request)
    {
        $data = $request->all();
        $semester = new Semester();
        if ($semester->validateWithParams($data, array(-1))) {
            $semester->fill($data);
            $semester->save();
            $request->session()->put('message', trans('messages.item_saved'));
            $request->session()->put('type', 'success');
            return response()->json(url('admin/plans/plansemester/' . $semester->id));
        } else {
            return response()->json($semester->errors(), 422);
        }
    }

    public function postPlanSemester(Request $request, $id = -1)
    {
        if ($id < 0) {
            abort(404);
        } else {
            $data = $request->all();
            $semester = Semester::findOrFail($id);
            if ($semester->validateWithParams($data, array($id))) {
                $semester->fill($data);
                $semester->save();
                return response()->json(trans('messages.item_saved'));
            } else {
                return response()->json($semester->errors(), 422);
            }
        }
    }

    public function postDeletePlanSemester(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:semesters',
        ]);
        $semester = Semester::findOrFail($request->input('id'));
        $semester->delete();
        $request->session()->put('message', trans('messages.item_deleted'));
        $request->session()->put('type', 'success');
        return response()->json(trans('messages.item_deleted'));
    }
}
