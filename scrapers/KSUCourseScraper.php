<?php

namespace scrapers;
use scrapers\KSUDepartmentScraper;

class KSUCourseScraper
{

    public function GetClassTimes()
    {
        $scraper = new KSUDepartmentScraper();
        $addresses = $scraper->GetAddresses();
        $dom = new IvoPetkov\HTML5DOMDocument();
        $returnArray = Array();
        $count = 0;

        foreach($addresses as $address){

            $html = file_get_contents('$address');
            libxml_use_internal_errors(TRUE);

            if (!empty($html)){

                $dom->loadHTML($html);
                libxml_clear_errors();
                $xpath = new DOMXPath($dom);

                $courses = $xpath->query('//tbody[@id]');
                $times = $xpath->query('//tbody[@class=""]');




            }

        }



    }
}