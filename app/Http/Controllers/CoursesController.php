<?php

namespace App\Http\Controllers;

use App\Helpers\JsonSerializer;
use App\Models\Category;
use App\Models\College;
use App\Models\Course;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class CoursesController extends Controller
{

    public function __construct()
    {
        $this->fractal = new Manager();
    }

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

        $courseSlugs = array();
        //Get the list of electivelistcourses, this will then be used to grab the matching course objects.
        $electiveListModels = ElectiveListCourses::where('electivelist_id', $electiveListId)->get();
        //For each of the returned elective list
        foreach($electiveListModel as $electiveListModels) {
          //If electiveliostModel does not have a course_max_number, that means it only has the min_number so it's ajust a single class
          if($electiveListModel->course_max_number != null) {
            //When it has a the max, that means we want to iterate through the difference between max and min
            for($i = 0; $i < ($electiveListModel->course_max_number - $electiveListModel->course_min_number); $i++) {
              $currentCourseNumber = $electiveListModel->course_min_number + 1;
              $courseSlugs[] = $electiveListModel->course_prefix + $currentCourseNumber;
            }
          }
          $courseSlugs[] = $electiveListModel->course_prefix + $electiveListModel->course_min_number;
        }
        dd($courseSlugs);
        $courses = Course::whereIn('slug', $courseSlugs);
        //$courses = Course::filterName($request->input('query'))->get();
        //Need to go to the electivelistcourses and fgrab the slug, and then go through courses and grab the slugs and then compare.
        //$courses = Course::where('elective_list_id')->course


        $resource = new Collection($courses, function ($course) {
            return [
                'value' => $course->fullTitle,
                'data' => $course->id,
            ];
        });

        return $this->fractal->createData($resource)->toJson();
    }

    public function getPrereqs($id)
    {
        $course = Course::findOrFail($id);
        $resource = new Item($course, function ($course) {
            return [
                'prerequisites' => $course->prerequisites->pluck(['id'])->toArray(),
                'followers' => $course->followers->pluck(['id'])->toArray(),
            ];
        });
        $this->fractal->setSerializer(new JsonSerializer());
        return $this->fractal->createData($resource)->toJson();
    }

}
