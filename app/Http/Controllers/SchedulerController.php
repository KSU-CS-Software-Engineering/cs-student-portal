<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Semester;

class SchedulerController extends Controller
{

    public function show(Plan $plan)
    {
        $this->authorize('read', $plan);

        return view('scheduler.schedule')
            ->with(['planId' => $plan->id]);
    }

    function getSemesters(Plan $plan){

        $this->authorize('read', $plan);

        $semesters = $plan->semesters;

        return $semesters;

    }

    function getCurrentSemesterId(Plan $plan)
    {
        $this->authorize('read', $plan);

        $currentSemesterName = Semester::currentSemester();

        $currentSemester = $plan->semesters->where('name', $currentSemesterName)->first();

        return $currentSemester;
    }

    public function getSemesterSections(Semester $semester)
    {
        $this->authorize('read', $semester->plan);

        return $semester->getSections();
    }
 }

