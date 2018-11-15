<?php

namespace App\Http\Controllers;

use App\scrapers\KSUCourseScraper;

class SchedulerController extends Controller{


    public function getData(){

        $scraper = new KSUCourseScraper();
        $courseInfos = $scraper->GetClassTimes();

        $course = [];

        foreach($courseInfos as $courseInfo){

            $courseName = $courseInfo[0]['header']->firstChild->getAttribute('id');

            foreach($courseInfo[0]['sections'] as $section){


                $days = $section->firstChild->childNodes[4];
                $hours = $section->firstChild->childNodes[5];

                $course[] = [$courseName,$days,$hours];
            }

        }

        return view('scheduler.schedule')->with('course', $course);
    }















}