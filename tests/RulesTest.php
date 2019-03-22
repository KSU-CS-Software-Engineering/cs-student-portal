<?php

namespace Tests;

use App\Models\Completedcourse;
use App\Models\Degreeprogram;
use App\Models\Degreerequirement;
use App\Models\Electivelistcourse;
use App\Models\Plan;
use App\Models\Planrequirement;
use App\Models\Semester;
use App\Rules\VerifyFourYearPlan;
use App\Rules\VerifySemester;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RulesTest extends TestCase
{
    use DatabaseTransactions, DatabaseMigrations;

    public function test4YrPlanValidity()
    {
        $plan = Plan::find(1);
        $this->assertEmpty(VerifyFourYearPlan::checkCISRequirementsPlan($plan));
    }

    public function testGraduationValidityDegreeRequirements()
    {
        $plan = factory(Plan::class)->create();
        $degreeRequirements = Degreeprogram::find(1)->requirements;

        foreach ($degreeRequirements as $degreeRequirement) {
            $name = $degreeRequirement->course_name;

            if ($degreeRequirement->electivelist_id !== null) {
                  $electiveCourse = Electivelistcourse
                      ::where('electivelist_id', $degreeRequirement->electivelist_id)
                      ->first();

                  $name = "{$electiveCourse->course_prefix} {$electiveCourse->course_min_number}";
            }

            factory(Completedcourse::class)->create([
                'name' => $name,
                'student_id' => $plan->student_id,
            ]);

            factory(Degreerequirement::class)->create([
                'degreeprogram_id' => $plan->degreeprogram_id,
                'course_name' => $name,
                'electivelist_id' => $degreeRequirement->electivelist_id,
                'semester' => $degreeRequirement->semester,
                'ordering' => $degreeRequirement->ordering,
            ]);
        }

        $this->assertEmpty(VerifyFourYearPlan::checkGraduationValidityDegreeRequirements($plan));
    }

    public function testGraduationValidityPlanRequirements()
    {
        $plan = factory(Plan::class)->create();
        $planRequirements = Plan::find(1)->requirements;

        foreach ($planRequirements as $planRequirement) {
            $name = $planRequirement->course_name;

            if (!Semester::where('id', $planRequirement->semester_id + 100)->exists()) {
                factory(Semester::class)->create([
                    'id' => $planRequirement->semester_id + 100,
                    'plan_id' => $plan->id,
                    'ordering' => $planRequirement->semester->ordering,
                ]);
            }

            if ($planRequirement->electivelist_id !== null) {
                $electiveCourse = Electivelistcourse
                    ::where('electivelist_id', $planRequirement->electivelist_id)
                    ->first();
                $name = "{$electiveCourse->course_prefix} {$electiveCourse->course_min_number}";
            }

            factory(Completedcourse::class)->create([
                'name' => $name,
                'student_id' => $plan->student_id,
            ]);

            factory(Planrequirement::class)->create([
                'ordering' => $planRequirement->ordering,
                'semester_id' => ($planRequirement->semester_id + 100),
                'plan_id' => $plan->id,
                'course_name' => $name,
                'electivelist_id' => $planRequirement->electivelist_id,
            ]);
        }

        $this->assertEmpty(VerifyFourYearPlan::checkGraduationValidityPlan($plan));
    }

    public function testSemesterCheckHours()
    {
        $plan = Plan::find(70);
        $this->assertEmpty(VerifySemester::checkHours($plan));
    }

    public function testCheckPreReqs()
    {
        $plan = Plan::find(2);
        $semesters = $plan->semesters;
        $completedCourses = $plan->student->completedcourses;

        foreach ($semesters as $semester) {
            $semesterRequirements = $semester->requirements;
            $previousSemestersClasses = $plan->requirements->where('semesters.ordering', '<', $semester->ordering);

            foreach ($semesterRequirements as $semesterRequirement) {
                if($semesterRequirement->course === null) {
                    continue;
                }

                $prerequisites = $semesterRequirement->course->prerequisites;

                foreach ($prerequisites as $prerequisite) {
                    $courseName = "{$prerequisite->prefix} {$prerequisite->number}";

                    if (!$previousSemestersClasses->contains('course_name', $courseName)
                        || !$completedCourses->contains('name', $courseName)) {
                        factory(Completedcourse::class)->create([
                            'name' => $courseName,
                            'student_id' => $plan->student_id
                        ]);
                    }
                }
            }
        }

        $this->assertEmpty(VerifySemester::checkPreReqs($plan));
    }

    public function testCheckCoursePlacement()
    {
        $plan = Plan::find(2);
        $this->assertEmpty(VerifySemester::checkCoursePlacement($plan));
    }

    public function testKState8()
    {
        $plan = Plan::find(70);
        $this->assertEmpty(VerifyFourYearPlan::checkKState8($plan));
    }
}
