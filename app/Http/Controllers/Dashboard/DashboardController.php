<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Advisor;
use App\Models\Blackout;
use App\Models\Blackoutevent;
use App\Models\Completedcourse;
use App\Models\Course;
use App\Models\Degreeprogram;
use App\Models\Electivelist;
use App\Models\Groupsession;
use App\Models\Meeting;
use App\Models\Plan;
use App\Models\Student;
use App\Models\Transfercourse;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex()
    {
        $data["students"] = Student::count();
        $data["advisors"] = Advisor::count();
        $data["meetings"] = Meeting::count();
        $data["groupsessions"] = Groupsession::count();
        $data["blackouts"] = Blackout::count();
        $data["blackoutevents"] = Blackoutevent::count();
        $data["courses"] = Course::count();
        $data["plans"] = Plan::count();
        $data["degreeprograms"] = Degreeprogram::count();
        $data["electivelists"] = Electivelist::count();
        $data["completedcourses"] = Completedcourse::count();
        $data["transfercourses"] = Transfercourse::count();
        return view('dashboard.index')->with('page_title', "Advising Dashboard")->with('data', $data);
    }

}
