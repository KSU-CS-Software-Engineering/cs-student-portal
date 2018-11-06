<?php

namespace scrapers;

class KSUDepartmentScraper
{

    public function GetAddresses()
    {

        $dom = new IvoPetkov\HTML5DOMDocument();
        $html = file_get_contents('https://courses.k-state.edu/spring2019/schedule.html');
        libxml_use_internal_errors(TRUE);

        $count = 0;
        $returnArray = array();

        if (!empty($html)) {

            $dom->loadHTML($html);
            libxml_clear_errors();

            $xpath = new DOMXPath($dom);
            //gets the urls from html
            $addresses = $xpath->query('//a[@herf="URL"]');
            //filter urls for the ones we want
            if ($addresses->length > 0 && $addresses->length < 6) {
                //go through all the urls
                foreach ($addresses as $address) {
                    //add each one to the array
                    $returnArray[$count] = $address;
                    $count++;
                }
            }
        }
        return $returnArray;
    }
}