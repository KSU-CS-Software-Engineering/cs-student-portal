<?php

use Illuminate\Database\Seeder;
use App\scrapers\KSUCourseScraper;

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
      //var_dump($courseSectionArray);
		  foreach($courseSectionArray as $courseSection){
           var_dump($courseSection);
           DB::table('sections')->insert([
                 'course_number' => $courseSection['courseNumber'], //The course slugline
				         'section' => $courseSection['section'],
				         'type' => $courseSection['type'],
                 'units' => $courseSection['units'],
                 'days' => $courseSection['days'],
                 'hours' => $courseSection['hours'],
                 'facility' => $courseSection['facility'],
                 'instructor' => $courseSection['instructor'],
                 'course_id' => $courseSection['courseId'] //The matching course object id.
			    ]);
		  }
    }
}
