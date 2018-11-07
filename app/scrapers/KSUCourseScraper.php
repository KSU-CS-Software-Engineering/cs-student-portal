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

            $semester = 'spring2019';
            $url = $this->BASE_URL . $semester . '/' . $address->getAttribute('href');
            $dom->loadHTMLFile($url);

            if (!empty($dom)) {

                $headers = $dom->querySelectorAll('tbody.course-header');
                foreach ($headers as $header) {
                    $sections = [];
                    $sibling = $header->nextSibling;
                    while ($sibling !== null && $sibling->classList->contains("section")) {
                        array_push($sections, $sibling);
                        $sibling = $sibling->nextSibling;
                    }
                    $returnArray[] = [
                        'header' => $header,
                        'sections' => $sections,
                    ];
                }

            }
        }

        return $returnArray;
    }
}
