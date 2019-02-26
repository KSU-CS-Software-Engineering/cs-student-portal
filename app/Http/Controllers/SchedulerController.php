<?php

namespace App\Http\Controllers;

use App\scrapers\KSUCourseScraper;
use App\Models\Section;
use App\Models\Plan;

class SchedulerController extends Controller
{
    public function getData()
    {

    }


    public function show()
    {


        return view('scheduler.schedule');
    }

 }
