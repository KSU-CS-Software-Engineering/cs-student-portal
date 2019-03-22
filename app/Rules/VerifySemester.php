<?php

namespace App\Rules;

use App\Models\Plan;

class VerifySemester
{

    public static function checkHours(Plan $plan)
    {
        $semestersOver21 = [];

        $semesters = $plan->semesters;

        foreach ($semesters as $semester) {
            $creditHours = $semester->requirements->sum->credits;

            if ($creditHours > 21) {
                $semestersOver21[] = ['message' => "{$semester->name} needs to be under 21 hours"];
            }
        }

        return $semestersOver21;
    }

    public static function checkPreReqs(Plan $plan)
    {
        $missingPrerequisites = [];

        $semesters = $plan->semesters;
        $completedCourses = $plan->student->completedcourses;

        foreach($semesters as $semester) {
            $semesterRequirements = $semester->requirements;
            $previousSemestersClasses = $plan->requirements
                ->where('semesters.ordering', '<', $semester->ordering);

            foreach ($semesterRequirements as $semesterRequirement) {
                if ($semesterRequirement->course === null) {
                    continue;
                }

                $prerequisites = $semesterRequirement->course->prerequisites;

                foreach ($prerequisites as $prerequisite) {
                    $prereqName = $prerequisite->prefix . ' ' . $prerequisite->number;

                    if ($previousSemestersClasses->contains('course_id', $prerequisite->id)
                        || $completedCourses->contains('name', $prereqName)) {
                        continue;
                    }

                    $item = ['message' => "{$prereqName} is a prerequisite for {$semesterRequirement->course_name}"];

                    if (!in_array($item, $missingPrerequisites)) {
                        $missingPrerequisites[] = $item;
                    }
                }
            }
        }

        return $missingPrerequisites;
    }

    public static function checkCoursePlacement(Plan $plan)
    {
        $misplacedCourses = [];

        $semesters = $plan->semesters;

        foreach($semesters as $semester) {
            $semesterNameStrings = explode(' ', $semester->name);
            $semesterName = $semesterNameStrings[0];
            $semesterRequirements = $semester->requirements;

            foreach($semesterRequirements as $semesterRequirement) {
                if ($semesterRequirement->electivelist === null) {
                    $course = $semesterRequirement->course;
                    $courseSemestersOffered = explode(', ', $course->semesters);

                    if (in_array("On sufficient demand", $courseSemestersOffered)) {
                        $misplacedCourses[] = ['message' => "{$course->slug} is offered with sufficient demand. " .
                            'Please consult your advisor for more information'];
                    }

                    if (in_array("odd years", $courseSemestersOffered)) {
                        $year = (int)$semesterNameStrings[1];

                        if ($year % 2 === 0) {
                            $misplacedCourses[] = ['message' => "{$course->slug} is only offered in odd years"];
                        }
                    }

                    if (in_array("even years", $courseSemestersOffered)) {
                        $year = (int)$semesterNameStrings[1];

                        if($year % 2 === 1) {
                            $misplacedCourses[] = ['message' => "{$course->slug} is only offered in even years"];
                        }
                    }

                    if (!in_array($semesterName, $courseSemestersOffered)) {
                        $misplacedCourses[] = ['message' => "{$course->slug} is not offered in the {$semesterName}"];
                    }
                }
            }
        }

        return $misplacedCourses;
    }
}
