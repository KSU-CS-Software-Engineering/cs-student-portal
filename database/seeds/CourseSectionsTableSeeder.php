<?php

use Illuminate\Database\Seeder;
use app\scrapers;

class CourseSectionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $classScraper = new KSUCourseScraper();
      $courseSectionArray = $classScraper->GetClassTimes();
		  foreach($courseSectionArray as $courseSection){
          $count = 0;
			     DB::table('course_sections')->insert([
                 'id' => $count, //The courseSectionId
                 'courseNumber' => $courseSection->courseNumber, //The course slugline
				         'section' => $courseSection->sections->section,
				         'types' => $courseSection->sections->type,
                 'units' => $courseSection->sections->units,
                 'days' => $courseSection->sections->days,
                 'hours' => $courseSection->sections->hours,
                 'facility' => $courseSection->sections->facility,
                 'instructor' => $courseSection->sections->instructor,
                 'course_id' => $courseSection->course->id //The matching course object id.
			    ]);
          $count++;
		  }
    }
}
