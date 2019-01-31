<?php

namespace App\scrapers;

use App\scrapers\KSUDepartmentScraper;
use IvoPetkov\HTML5DOMDocument;

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
            //This should be changed to be dynamic..
            $semester = 'spring2019';
            $url = $this->BASE_URL . $semester . '/' . $address->getAttribute('href');
            $dom->loadHTMLFile($url);

            if (!empty($dom)) {

                $headers = $dom->querySelectorAll('tbody.course-header');
                foreach ($headers as $header) {
                    $sections = [];
                    $sibling = $header->nextSibling;
                    while ($sibling !== null && $sibling->classList->contains('section')) {
                        $sectionSection = $sibling->firstChild->childNodes[0]->textContent;
                        $sectionType = $sibling->firstChild->childNodes[1]->textContent;
                        $sectionDays = $sibling->firstChild->childNodes[5]->textContent;
                        $sectionHours = $sibling->firstChild->childNodes[6]->textContent;
                        if ($sibling->firstChild->childNodes[5]->hasAttribute('colspan')) {
                            $sectionHours = '';
                        }
                        $sections[] = [
                            'days' => $sectionDays,
                            'hours' => $sectionHours,
                        ];
                        $sibling = $sibling->nextSibling;
                    }
                    $returnArray[] = [
                        'courseNumber' => $header->firstChild->getAttribute('id'),
                        'sections' => $sections,
                    ];
                }

            }
        }

        return $returnArray;
    }
}
