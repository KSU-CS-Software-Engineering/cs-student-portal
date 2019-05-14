<?php

use Illuminate\Database\Seeder;
use App\Models\Electivelistcourse;
use App\Models\Course;
use App\scrapers\KSUCourseScraper;


//This class seeds the ElectiveListCourses table with data. This pretty much takes the values from the ElectiveList table and combines it with the coure objects.

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
            //Get the course number. Add the min number + i so we'll get from the min to max
            $currentCourseNumber = $electiveListModel->course_min_number + $i;
            //Get the Course object that matches that course number
            $tempInserter = Course::where("slug", $electiveListModel->course_prefix.$currentCourseNumber)->first();
              if($tempInserter != NULL && !in_array($tempInserter->id, $courseSlugs)) {
                //Add the course object and the elective list to the same position in the arrays so they map nicely.
                array_push($courseSlugs, $tempInserter->id);
                array_push($courseElectiveListIds, $electiveListModel->electivelist_id);
              }
          }
        }
        //Do the same thing as the first one, this is just if there's only one value for that elective list.
        $tempInserter = Course::where("slug", $electiveListModel->course_prefix.$electiveListModel->course_min_number)->first();
        if($tempInserter != NULL && !in_array($tempInserter->id, $courseSlugs)) {
          array_push($courseSlugs, $tempInserter->id);
          array_push($courseElectiveListIds, $electiveListModel->electivelist_id);
        }
      }
      $courseSlugsTemp = array();
      for($j = 0; $j < count($courseSlugs); $j++) {
        var_dump($courseSlugs[$j]);
        var_dump($courseElectiveListIds[$j]);
        if($courseSlugs[$j] != NULL && $courseElectiveListIds[$j] != NULL) {
          //Connect the arrays, and add the values to the table.
          DB::table('elective_list_courses')->insert([
            'course_id' => $courseSlugs[$j],
            'elective_list_id' => $courseElectiveListIds[$j]
          ]);
        }

      }
    }
}
