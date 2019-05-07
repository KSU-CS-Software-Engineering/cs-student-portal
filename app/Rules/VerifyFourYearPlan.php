<?php

namespace App\Rules;

use App\Models\Area;
use App\Models\Plan;

class VerifyFourYearPlan
{

    public static function checkCISRequirementsPlan(Plan $plan)
    {
        $missingDegreeRequirements = [];

        $degreeRequirements = $plan->degreeprogram->requirements;
        $planRequirements = $plan->requirements;

        foreach ($degreeRequirements as $degreeRequirement) {
            if (!$planRequirements->contains('degreerequirement_id', $degreeRequirement->id)) {
                $missingDegreeRequirements[] = $degreeRequirement;
            }
        }

        return $missingDegreeRequirements;
    }

    public static function checkGraduationValidityDegreeRequirements(Plan $plan)
    {
        $incompleteDegreeRequirements = [];

        $student = $plan->student;
        $completedCourses = $student->completedcourses;
        $degreeRequirements = $plan->degreeprogram->requirements;

        foreach ($degreeRequirements as $degreeRequirement) {
            if (!$completedCourses->contains('name', $degreeRequirement->course_name)) {
                $incompleteDegreeRequirements[] = $degreeRequirement;
            }
        }

        return $incompleteDegreeRequirements;
    }

    public static function checkGraduationValidityPlan(Plan $plan)
    {
        $incompletePlanRequirements = [];

        $student = $plan->student;
        $completedCourses = $student->completedcourses;
        $planRequirements = $plan->requirements;

        foreach ($planRequirements as $planRequirement) {
            if (!$completedCourses->contains('name', $planRequirement->course_name)) {
                $incompletePlanRequirements[] = $planRequirement;
            }
        }

        return $incompletePlanRequirements;
    }

    public static function checkKState8(Plan $plan)
    {
        $missingKState8 = [];

        $allKState8 = Area::all();
        $planRequirements = $plan->requirements;

        foreach ($planRequirements as $planRequirement) {
            if ($planRequirement->course !== null) {
                $courseAreas = $planRequirement->course->areas;

                foreach ($courseAreas as $courseArea) {
                    $allKState8 = $allKState8->where('id', '!==', $courseArea->id);
                }
            }
        }

        foreach ($allKState8 as $missingArea) {
            $missingKState8[] = ['message' => "You are missing the {$missingArea->area_name} K-State 8 requirement"];
        }

        return $missingKState8;
    }
}
