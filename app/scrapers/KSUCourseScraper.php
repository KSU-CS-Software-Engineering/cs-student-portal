<?php

namespace App\scrapers;
use App\scrapers\KSUDepartmentScraper;
use DOMXPath;
use IvoPetkov\HTML5DOMDocument;

class KSUCourseScraper
{

    public function GetClassTimes()
    {
        $scraper = new KSUDepartmentScraper();
        $addresses = $scraper->GetAddresses();
        $dom = new HTML5DOMDocument();
        $returnArray = Array();
        $count = 0;

        foreach($addresses as $address){

            $address = $address->getAttribute('href');
            $html = file_get_contents("https://courses.k-state.edu/spring2019/$address");
            dd($address);
            if (!empty($html)){

                $dom->loadHTML($html);


                $headers = $dom->querySelectorAll('tbody.course-header');
                foreach ($headers as $header) {
                    $sections = [];
                    $sibling = $header->nextSibling;
                    while ($sibling !== null && $sibling->classList.contains("section")) {
                        array_push($sections, $sibling);
                        $sibling = $sibling->nextSibling;
                    }
                    dd($sections);
                }
                dd($headers);

            }

        }

        return $returnArray;
    }
}