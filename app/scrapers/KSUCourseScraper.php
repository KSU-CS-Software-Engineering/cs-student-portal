<?php

namespace App\scrapers;

use App\scrapers\KSUDepartmentScraper;
use IvoPetkov\HTML5DOMDocument;
use App\Models\Course;

class KSUCourseScraper
{
    private $BASE_URL = 'https://courses.k-state.edu/';

    public function GetClassTimes()
    {
        $badlyFormattedClasses = array("AGEC460", "AGEC713", "AGEC750", "AGEC770", "ASI561", "ASI561");
        $scraper = new KSUDepartmentScraper();
        $addresses = $scraper->GetAddresses();
        $dom = new HTML5DOMDocument();
        $returnArray = [];
        $count = 0;

        foreach($addresses as $address) {
            //This should be changed to be dynamic in some way, it will need to be changed often.
            $semester = 'spring2019';
            $url = $this->BASE_URL . $semester . '/' . $address->getAttribute('href');
            $dom->loadHTMLFile($url, HTML5DOMDocument::ALLOW_DUPLICATE_IDS);

            if (!empty($dom)) {

                $headers = $dom->querySelectorAll('tbody.course-header');
                //Each one of these $headers represent a class while the inner loop represents the different sections
                foreach ($headers as $header) {
                    $sections = [];

                    $courseSlug = $header->firstChild->getAttribute('id');
                    $sibling = $header->nextSibling;
                    //Find the matching course object. Throw an exception if it can't be found since we always want one.
                    $course = Course::where('slug', $courseSlug)->first();
                    var_dump($header->firstChild->childNodes[0]->textContent);

                    //This part loops through all of the classes for that particular area.
                    //These siblings and childNodes are the indivdual elements in the table.
                    //The variable names match the name of the section on the site.
                    while ($sibling !== null && $sibling->classList->contains('section')) {
                        if ($sibling->firstChild->childNodes[5]->hasAttribute('colspan')) {
                            $sectionHours = ''; //What does this do?
                        }
                        // $sectionSection = str_replace("&nbsp;", '', $sibling->firstChild->childNodes[0]->textContent);
                        // $sectionType = str_replace("&nbsp;", '', $sibling->firstChild->childNodes[1]->textContent);
                        // $sectionUnits = str_replace("&nbsp;", '', $sibling->firstChild->childNodes[3]->textContent);
                        // $sectionDays = str_replace("&nbsp;", '', $sibling->firstChild->childNodes[5]->textContent);
                        // $sectionHours = str_replace("&nbsp;", '', $sibling->firstChild->childNodes[6]->textContent);
                        // $sectionFacility = str_replace("&nbsp;", '', $sibling->firstChild->childNodes[7]->textContent);
                        // $sectionInstructor = str_replace("&nbsp;", '', $sibling->firstChild->childNodes[9]->textContent);

                        if(in_array($courseSlug, $badlyFormattedClasses)) {
                          echo "here\n";
                          break;
                        }

                        else {
                          echo "here2\n";
                          var_dump($courseSlug);
                          var_dump($sibling->firstChild->childNodes[0]->textContent);
                          var_dump($sibling->firstChild->childNodes[1]->textContent);
                          var_dump($sibling->firstChild->childNodes[3]->textContent);
                          var_dump($sibling->firstChild->childNodes[5]->textContent);
                          var_dump($sibling->firstChild->childNodes[6]->textContent);
                          var_dump($sibling->firstChild->childNodes[7]->textContent);
                          var_dump($sibling->firstChild->childNodes[9]->textContent);
                          $sectionSection = str_replace("&nbsp;", '', $sibling->firstChild->childNodes[0]->textContent);
                          $sectionType = str_replace("&nbsp;", '', $sibling->firstChild->childNodes[1]->textContent);
                          $sectionUnits = str_replace("&nbsp;", '', $sibling->firstChild->childNodes[3]->textContent);
                          $sectionDays = str_replace("&nbsp;", '', $sibling->firstChild->childNodes[5]->textContent);
                          $sectionHours = str_replace("&nbsp;", '', $sibling->firstChild->childNodes[6]->textContent);
                          $sectionFacility = str_replace("&nbsp;", '', $sibling->firstChild->childNodes[7]->textContent);
                          $sectionInstructor = str_replace("&nbsp;", '', $sibling->firstChild->childNodes[9]->textContent);
                          $returnArray[] = [
                              'course_number' => $courseSlug,
                              'section' => $sectionSection,//$sibling->firstChild->childNodes[0]->textContent, //This may be slightly unclear. This is the section label as in A, B, C to distinguish the different sections in the class.
                              'type' => $sectionType,//$sibling->firstChild->childNodes[1]->textContent, //This is means recitation, lecture etc. Not 100% sure of the format yet.
                              'units' => $sectionUnits,//$sibling->firstChild->childNodes[3]->textContent, //This is the amount of credits it's worth
                              'days' => $sectionDays,//$sibling->firstChild->childNodes[5]->textContent, //These are the days M,T,W,Th,F
                              'hours' => $sectionHours,//$sibling->firstChild->childNodes[6]->textContent, //Hours of the class 12:30-1:20
                              'facility' => $sectionFacility,//$sibling->firstChild->childNodes[7]->textContent, //This is the room
                              'instructor' => $sectionInstructor,//$sibling->firstChild->childNodes[9]->textContent, //The course instructor
                              'course' => $course
                          ];

                        }
                        $sibling = $sibling->nextSibling;
                    }
                }
            }
        }
        return $returnArray;
    }
}
