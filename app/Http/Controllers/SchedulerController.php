<?php

namespace App\Http\Controllers;

use App\Models\Electivelistcourse;
use App\Models\Planrequirement;
use App\scrapers\KSUCourseScraper;
use App\Models\Section;
use App\Models\Plan;
use App\Models\Semester;

class SchedulerController extends Controller
{

    public function getRelatedSections(Semester $semester){

        $courses = [];
        $elective_id = [];
        //$electiveListCourse = new Electivelistcourse();
        $currentSemester = $semester[0];
        $classesForSemester = Planrequirement::where('semester_id', $currentSemester->id)->get();

        /*adds all class id's to courses array
        if no class id, get elective list id */
        foreach($classesForSemester as $class){
            if($class->course_id == NULL){
                array_push($elective_id, $class->electivelist_id);
            }
            else{
                array_push($courses, $class->course_id);
            }
        }

        if(count($elective_id) > 1) {
            //goes through every elective student has in the semester
            foreach ($elective_id as $id) {

                $electiveCourses = Electivelistcourse::where('electivelist_id',$id)->get();

                foreach($electiveCourses as $electiveCourse) {

                    array_push($courses, $electiveCourse);
                }
            }
        }
        else{
            //only add for the one elective
            $electiveCourses = Electivelistcourse::where('electivelist_id', $elective_id);
            foreach ($electiveCourses as $electiveCourse){
                array_push($courses, $electiveCourse);
            }
        }

        $returnArray = [];
        /*takes courses student had and elective courses in semester
          gets the times they are offered*/
        foreach($courses as $course){
            if(is_int($course)){
                array_push($returnArray, Section::where('course_id', $course)->get());
                //dd($returnArray);
            }
            else{
                // array_push($betterArray, $course->);

                // $courseTimes = Section::where('course_number',$course-> )->get();

            }
        }

        dd($returnArray);
        return Semester::find('1')->sections();


    }


    public function show()
    {


        return view('scheduler.schedule');
    }

 }
