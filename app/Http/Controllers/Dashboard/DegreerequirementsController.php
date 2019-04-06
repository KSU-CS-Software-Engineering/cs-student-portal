<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Degreeprogram;
use App\Models\Degreerequirement;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DegreerequirementsController extends Controller
{

    public function getDegreerequirementsForProgram(Request $request, $id = -1)
    {
        if ($id < 0) {
            abort(404);
        } else {
            $degreeprogram = Degreeprogram::withTrashed()->with('requirements')->findOrFail($id);
            $resource = new Collection();
            foreach ($degreeprogram->requirements as $requirement) {
                if (!empty($requirement->course_name)) {
                    $resource->push([
                        'id' => $requirement->id,
                        'notes' => $requirement->notes,
                        'semester' => $requirement->semester,
                        'ordering' => $requirement->ordering,
                        'credits' => $requirement->credits,
                        'name' => $requirement->course_name,
                    ]);
                } else {
                    $resource->push([
                        'id' => $requirement->id,
                        'notes' => $requirement->notes,
                        'semester' => $requirement->semester,
                        'ordering' => $requirement->ordering,
                        'credits' => $requirement->credits,
                        'name' => $requirement->electivelist->name,
                    ]);
                }
            }

            return response()->json($resource);
        }
    }

    public function getDegreerequirement(Request $request, $id = -1)
    {
        if ($id < 0) {
            abort(404);
        } else {
            $degreerequirement = Degreerequirement::findOrFail($id);

            $requirement = $degreerequirement;
            if (!empty($requirement->course_name)) {
                $resource = [
                    'id' => $requirement->id,
                    'notes' => $requirement->notes,
                    'semester' => $requirement->semester,
                    'ordering' => $requirement->ordering,
                    'credits' => $requirement->credits,
                    'type' => 'course',
                    'course_name' => $requirement->course_name,
                ];
            } else {
                $resource = [
                    'id' => $requirement->id,
                    'notes' => $requirement->notes,
                    'semester' => $requirement->semester,
                    'ordering' => $requirement->ordering,
                    'credits' => $requirement->credits,
                    'type' => 'electivelist',
                    'electivelist_id' => $requirement->electivelist->id,
                    'electivelist_name' => $requirement->electivelist->name,
                ];
            }

            return response()->json($resource);
        }
    }

    public function postNewdegreerequirement(Request $request)
    {
        $data = $request->all();
        $degreerequirement = new Degreerequirement();
        if ($degreerequirement->validateWithParams($data, array(-1))) {
            $degreerequirement->fill($data);
            if ($request->has("course_name")) {
                $degreerequirement->electivelist_id = null;
            }
            if ($request->has("electivelist_id")) {
                $degreerequirement->course_name = "";
            }
            $degreerequirement->save();
            return response()->json(trans('messages.item_saved'));
        } else {
            return response()->json($degreerequirement->errors(), 422);
        }
    }

    public function postDegreerequirement(Request $request, $id = -1)
    {
        if ($id < 0) {
            abort(404);
        } else {
            $data = $request->all();
            $degreerequirement = Degreerequirement::findOrFail($id);
            if ($degreerequirement->validateWithParams($data, array($id))) {
                $degreerequirement->fill($data);
                if ($request->has("course_name")) {
                    $degreerequirement->electivelist_id = null;
                }
                if ($request->has("electivelist_id")) {
                    $degreerequirement->course_name = "";
                }
                $degreerequirement->save();
                return response()->json(trans('messages.item_saved'));
            } else {
                return response()->json($degreerequirement->errors(), 422);
            }
        }
    }

    public function postDeletedegreerequirement(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:degreerequirements',
        ]);
        $degreerequirement = Degreerequirement::findOrFail($request->input('id'));
        $degreerequirement->delete();
        return response()->json(trans('messages.item_deleted'));
    }

}
