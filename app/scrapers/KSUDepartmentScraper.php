<?php

namespace App\scrapers;

use IvoPetkov\HTML5DOMDocument;

class KSUDepartmentScraper
{

    public function GetAddresses()
    {

        $dom = new HTML5DOMDocument();
        //gets the html from this website
        $html = file_get_contents('https://courses.k-state.edu/spring2019/schedule.html');
        //libxml_use_internal_errors(TRUE);

        $count = 0;
        $returnArray = array();
        //if there is some html
        if (!empty($html)) {

            //load the htlm
            $dom->loadHTML($html);
            //gets the urls from html
            $addresses = $dom->querySelectorAll('h5 + ul');

            //filter urls for the ones we want
//            if ($addresses->length > 0 && $addresses->length < 6) {
                //go through all the urls
                foreach ($addresses as $address) {
                    //add each one to the array
                    $x = $address->querySelectorAll('li > a');
                    array_push($returnArray, ...$x);
                    $count++;
                }
//            }
        }
        return $returnArray;
    }
}
