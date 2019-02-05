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
        $scraper = new KSUDepartmentScraper();
        $addresses = $scraper->GetAddresses();
        $dom = new HTML5DOMDocument();
        $returnArray = [];
        $count = 0;

        foreach($addresses as $address) {
            //This should be changed to be dynamic in some way, it will need to be changed often.
            $semester = 'spring2019';
            $url = $this->BASE_URL . $semester . '/' . $address->getAttribute('href');
            $dom->loadHTMLFile($url);

            if (!empty($dom)) {

                $headers = $dom->querySelectorAll('tbody.course-header');
                //Each one of these $headers represent a class while the inner loop represents the different sections
                foreach ($headers as $header) {
                    $sections = [];
                    $courseSlug = $header->firstChild->getAttribute('id');
                    $sibling = $header->nextSibling;
                    //Find the matching course object. Throw an exception if it can't be found since we always want one.
                    $course = Course::where('slug', $courseSlug)->findOrFail();


                    //This part loops through all of the classes for that particular area.
                    //These siblings and childNodes are the indivdual elements in the table.
                    //The variable names match the name of the section on the site.
                    while ($sibling !== null && $sibling->classList->contains('section')) {
                        if ($sibling->firstChild->childNodes[5]->hasAttribute('colspan')) {
                            $sectionHours = ''; //What does this do?
                        }
                        //I changed this around, because how we
                        $returnArray[] = [
                            'courseNumber' => $courseNumber,
                            'section' => $sibling->firstChild->childNodes[0]->textContent, //This may be slightly unclear. This is the section label as in A, B, C to distinguish the different sections in the class.
                            'type' => $sibling->firstChild->childNodes[1]->textContent, //This is means recitation, lecture etc. Not 100% sure of the format yet.
                            'units' => $sibling->firstChild->childNodes[3]->textContent, //This is the amount of credits it's worth
                            'days' => $sibling->firstChild->childNodes[5]->textContent, //These are the days M,T,W,Th,F
                            'hours' => $sibling->firstChild->childNodes[6]->textContent, //Hours of the class 12:30-1:20
                            'facility' => $sibling->firstChild->childNodes[7]->textContent, //This is the room
                            'instructor' => $sibling->firstChild->childNodes[9]->textContent, //The course instructor
                            'course' => $course
                        ];
                        $sibling = $sibling->nextSibling;
                    }
                }

            }
        }

        return $returnArray;
    }
}
