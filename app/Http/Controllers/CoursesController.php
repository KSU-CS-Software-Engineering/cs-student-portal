<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\elective_list_course;
use App\Models\College;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CoursesController extends Controller
{

    /**
     * Responds to requests to GET /courses
     */
    public function getIndex()
    {
        $colleges = College::with('categories')->get();
        return view('courses/index')->with('colleges', $colleges);
    }

    public function getCategory($category)
    {
        $category = Category::where('url', $category)->with('prefixes')->first();

        $prefixes = array();

        foreach ($category->prefixes as $prefix) {
            $prefixes[] = $prefix->prefix;
        }

        $courses = Course::whereIn('prefix', $prefixes)->get();

        return view('courses/list')->with('courses', $courses)->with('category', $category);
    }

    public function getCourse($slug)
    {
        $courses = Course::where('slug', $slug)->with('prerequisites', 'followers', 'areas')->get();

        return view('courses/detail')->with('courses', $courses)->with('slug', $slug);
    }

    public function getCourseById($id)
    {
        $courses = Course::where('id', $id)->with('prerequisites', 'followers', 'areas')->get();

        return view('courses/detail')->with('courses', $courses)->with('slug', $courses->first()->slug);
    }

    public function getCoursefeed(Request $request)
    {
        $this->validate($request, [
            'query' => 'required|string',
            'electiveListId' => 'integer',
        ]);
        if($request->input('electiveListId') != null && $request->input('electiveListId') != '9') {
          $electiveListModels = elective_list_course::where('elective_list_id', $request->input('electiveListId'))->pluck('course_id');
          $courses = Course::whereIn('id', $electiveListModels)->filterName($request->input('query'))->get();
        }
        else {
          $courses = Course::filterName($request->input('query'))->get();
        }

        $resource = new Collection();
        foreach ($courses as $course) {
            $resource->push([
                'value' => $course->fullTitle,
                'data' => $course->id,
            ]);
        }

        return response()->jsonApi($resource);
    }

    public function getPrereqs($id)
    {
        $course = Course::findOrFail($id);
        $resource = [
            'prerequisites' => $course->prerequisites->pluck(['id'])->toArray(),
            'followers' => $course->followers->pluck(['id'])->toArray(),
        ];

        return response()->json($resource);
    }

}
