<?php

namespace App\Http\Controllers;

use App\scrapers\KSUCourseScraper;

class SchedulerController extends Controller
{


    public function getData()
    {
//
//        $scraper = new KSUCourseScraper();
//        $courseInfos = $scraper->GetClassTimes();
//
//        $course = [];
//
//        foreach ($courseInfos as $courseInfo) {
//
//        }
//
//
//
    }


    public function show()
    {



        return view('scheduler.schedule');
    }

 }



