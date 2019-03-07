<?php

namespace App\Http\Controllers;

use App\Helpers\JsonSerializer;
use App\Models\Plan;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class FlowchartSemestersController extends Controller
{

    private $fractal;

    public function __construct()
    {
        $this->middleware('cas');
        $this->middleware('update_profile');
        $this->fractal = new Manager();
    }

    public function getSemesterData(Request $request, $id = -1)
    {
        if ($id < 0) {
            abort(404);
        }
        $plan = Plan::findOrFail($id);
        return $this->getSemesters($request, $plan);
    }

    public function getSemesters(Request $request, Plan $plan)
    {
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

    public function renameSemester(Request $request, Plan $plan, Semester $semester)
    {
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
            abort(404);
        }
        if ($semester->plan_id !== $plan->id) {
            abort(404);
        }

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

    public function addSemester(Request $request, Plan $plan)
    {
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
        } else {
            $user = Auth::user();
            $plan = Plan::with('semesters')->findOrFail($id);
            $semester = Semester::findOrFail($request->input('id'));

            $lastSemester = Semester::where('plan_id', $plan->id)->orderby('ordering', 'DESC')->first();
            $seasonYear = explode(' ', $lastSemester->name);
            $year = $seasonYear[1];
            if ($seasonYear[0] == "Fall") {
                $seasonYear[1]++;
            }

            if ($user->is_advisor || (!$user->is_advisor && $user->student->id == $plan->student_id)) {
                if ($semester->plan_id == $id) {
                    $semester->name = "Summer " . $year;// . $semester->year();
                    $semester->save();
                    return;
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

    public function moveSemester(Request $request, Plan $plan)
    {
        $user = Auth::user();
        if (!$user->is_advisor && $user->student->id != $plan->student_id) {
            abort(404);
        }

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
