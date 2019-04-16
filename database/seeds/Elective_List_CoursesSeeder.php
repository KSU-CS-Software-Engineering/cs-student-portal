<?php

use Illuminate\Database\Seeder;
use App\Models\Electivelistcourse;
use App\Models\Course;
use App\scrapers\KSUCourseScraper;

class Elective_List_CoursesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

      $courseSlugs = array();
      $courseElectiveListIds = array();
      $temp = Course::where('slug', "s")->first();
      //Get the list of electivelistcourses, this will then be used to grab the matching course objects.
      $electiveListModels = Electivelistcourse::get();
      //For each of the returned elective list
      foreach($electiveListModels as $electiveListModel) {
        //If electiveliostModel does not have a course_max_number, that means it only has the min_number so it's ajust a single class
        if($electiveListModel->course_max_number != null) {
          //When it has a the max, that means we want to iterate through the difference between max and min
          for($i = 0; $i <= ($electiveListModel->course_max_number - $electiveListModel->course_min_number); $i++) {
            $currentCourseNumber = $electiveListModel->course_min_number + $i;
            $tempInserter = Course::where("slug", $electiveListModel->course_prefix.$currentCourseNumber)->first();
              if($tempInserter != NULL && !in_array($tempInserter->id, $courseSlugs)) {
                array_push($courseSlugs, $tempInserter->id);
                array_push($courseElectiveListIds, $electiveListModel->electivelist_id);
              }
          }
        }
        $tempInserter = Course::where("slug", $electiveListModel->course_prefix.$electiveListModel->course_min_number)->first();
        if($tempInserter != NULL && !in_array($tempInserter->id, $courseSlugs)) {
          array_push($courseSlugs, $tempInserter->id);//array($tempInserter, $electiveListModel->electivelist_id));
          array_push($courseElectiveListIds, $electiveListModel->electivelist_id);
        }
      }
      $courseSlugsTemp = array();
      $dup = true;
      for($j = 0; $j < count($courseSlugs); $j++) {
        var_dump($courseSlugs[$j]);
        var_dump($courseElectiveListIds[$j]);
        if($courseSlugs[$j] != NULL && $courseElectiveListIds[$j] != NULL) {
          DB::table('elective_list_courses')->insert([
            'course_id' => $courseSlugs[$j],
            'elective_list_id' => $courseElectiveListIds[$j]
          ]);
        }

      }
    }
}
