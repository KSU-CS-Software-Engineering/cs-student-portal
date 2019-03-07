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

      $courseSlugs = array();
      //Get the list of electivelistcourses, this will then be used to grab the matching course objects.
      $electiveListModels = ElectiveListCourses::get();
      //For each of the returned elective list
      foreach($electiveListModel as $electiveListModels) {
        //If electiveliostModel does not have a course_max_number, that means it only has the min_number so it's ajust a single class
        if($electiveListModel->course_max_number != null) {
          //When it has a the max, that means we want to iterate through the difference between max and min
          for($i = 0; $i < ($electiveListModel->course_max_number - $electiveListModel->course_min_number); $i++) {
            $currentCourseNumber = $electiveListModel->course_min_number + 1;
            $courseSlugs[] = $electiveListModel->course_prefix + $currentCourseNumber;
          }
        }
        $courseSlugs[] = $electiveListModel->course_prefix + $electiveListModel->course_min_number;
      }
      $courses = Course::whereIn('slug', $courseSlugs);
      var_dump($courseSectionArray);
		  // foreach($courseSectionArray as $courseSection){
      //      var_dump($courseSection);
      //      DB::table('sections')->insert([
      //            'course_number' => $courseSection['courseNumber'], //The course slugline
			// 	         'section' => $courseSection['section'],
			// 	         'type' => $courseSection['type'],
      //            'units' => $courseSection['units'],
      //            'days' => $courseSection['days'],
      //            'hours' => $courseSection['hours'],
      //            'facility' => $courseSection['facility'],
      //            'instructor' => $courseSection['instructor'],
      //            'course_id' => $courseSection['courseId'] //The matching course object id.
			//     ]);
		  // }
    }
}
