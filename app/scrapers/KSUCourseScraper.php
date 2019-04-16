<?php

namespace App\scrapers;

use App\scrapers\KSUDepartmentScraper;
use IvoPetkov\HTML5DOMDocument;
use App\Models\Course;
use IvoPetkov\HTML5DOMElement;

class KSUCourseScraper
{
    private $BASE_URL = 'https://courses.k-state.edu/';
    private $NBSP5 = 'html5-dom-document-internal-entity1-nbsp-end';

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
            $dom->loadHTMLFile($url, HTML5DOMDocument::ALLOW_DUPLICATE_IDS);
            //$dom->loadHTMLFile($url);

            if (!empty($dom)) {

                $headers = $dom->querySelectorAll('tbody.course-header');
                //Each one of these $headers represent a class while the inner loop represents the different sections
                foreach ($headers as $header) {
                    $courseSlug = $header->firstChild->getAttribute('id');
                    $sibling = $header->nextSibling;
                    while (!($sibling instanceof HTML5DOMElement) && $sibling !== null) {
                        $sibling = $sibling->nextSibling;
                    }
                    //Find the matching course object. Throw an exception if it can't be found since we always want one.
                    $course = Course::where('slug', $courseSlug)->first();

                    //This part loops through all of the classes for that particular area.
                    //These siblings and childNodes are the indivdual elements in the table.
                    //The variable names match the name of the section on the site.
                    while ($sibling !== null && $sibling->classList->contains('section')) {
                        $sectionRow = $sibling->firstChild;
                        $commentRow = $sibling->lastChild;
                        $offset = 0;
                        $daysEl = $sibling->firstChild->childNodes[5];
                        if ($daysEl->hasAttribute('colspan')) {
                            $offset = intval($daysEl->getAttribute('colspan')) - 1;
                        }
                        $sectionSection = $sectionRow->childNodes[0]->textContent;
                        $sectionType = $sectionRow->childNodes[1]->textContent;
                        // $sectionNumber = $sectionRow->childNodes[2]->textContent;
                        $sectionUnits = $sectionRow->childNodes[3]->textContent;
                        // $sectionBasis = $sectionRow->childNodes[4]->textContent;
                        $sectionDays = str_replace($this->NBSP5, ' ', $sectionRow->childNodes[5]->textContent);
                        if ($offset == 1) {
                            $sectionHours = null;
                            $sectionFacility = $sectionRow->childNodes[7 - $offset]->textContent;
                        } elseif ($offset == 2) {
                            $sectionHours = null;
                            $sectionFacility = null;
                        } else {
                            $sectionHours = str_replace($this->NBSP5, ' ', $sectionRow->childNodes[6]->textContent);
                            $sectionFacility = $sectionRow->childNodes[7]->textContent;
                        }
                        $sectionInstructor = $sectionRow->childNodes[9 - (int) $offset]->textContent;
                        $courseId = $course == null ? null : $course->id;
                        $sectionNotes = str_replace($this->NBSP5 , ' ', $commentRow->textContent);

                        $returnArray[] = [
                            'courseNumber' => $courseSlug,
                            'section' => $sectionSection,//$sibling->firstChild->childNodes[0]->textContent, //This may be slightly unclear. This is the section label as in A, B, C to distinguish the different sections in the class.
                            // 'sectionNumber' => $sectionNumber,
                            'type' => $sectionType,//$sibling->firstChild->childNodes[1]->textContent, //This is means recitation, lecture etc. Not 100% sure of the format yet.
                            'units' => $sectionUnits,//$sibling->firstChild->childNodes[3]->textContent, //This is the amount of credits it's worth
                            // 'basis' => $sectionBasis,
                            'days' => $sectionDays,//$sibling->firstChild->childNodes[5]->textContent, //These are the days M,T,W,Th,F
                            'hours' => $sectionHours,//$sibling->firstChild->childNodes[6]->textContent, //Hours of the class 12:30-1:20
                            'facility' => $sectionFacility,//$sibling->firstChild->childNodes[7]->textContent, //This is the room
                            'instructor' => $sectionInstructor,//$sibling->firstChild->childNodes[9]->textContent, //The course instructor
                            'courseId' => $courseId,
                            'notes' => $sectionNotes,
                        ];

                        // }
                        $sibling = $sibling->nextSibling;
                        while (!($sibling instanceof HTML5DOMElement) && $sibling !== null) {
                            $sibling = $sibling->nextSibling;
                        }
                    }
                }
            }
        }
      //  dd($returnArray);
        return $returnArray;
    }
}
