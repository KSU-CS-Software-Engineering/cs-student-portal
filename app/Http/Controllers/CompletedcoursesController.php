<?php

namespace App\Http\Controllers;

use App\Models\Completedcourse;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CompletedcoursesController extends Controller
{

    public function __construct()
    {
        $this->middleware('cas');
    }

    public function getCompletedcoursefeed(Request $request, $id = -1)
    {
        if ($id < 0) {
            abort(404);
        }

        $user = Auth::user();

        if ($user->is_advisor || $user->student_id == $id) {
            $this->validate($request, [
                'query' => 'required|string',
            ]);

            $completedcourses = Completedcourse::where('student_id', $id)->filterName($request->input('query'))->get();

            $resource = new Collection();
            foreach ($completedcourses as $course) {
                $resource->push([
                    'value' => $course->fullTitle,
                    'data' => $course->id,
                ]);
            }

            return response()->jsonApi($resource);
        } else {
            abort(404);
        }
    }
}
