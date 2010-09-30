<?php
    ///////////////////////////////////////////////////////////
    // razorXMLlib                                           //
    // xml.lib.php                                           //
    // GPLv3                                                 //
    // XML to Array parser                                   //
    // smiffy6969                                            //
    // www.mis-limited.com                                   //
    // 06/2009                                               //
    // ----------------------------------------------------- //
    // V0.1  -  06/2009  -  version 0.1 of the xml to array  //
    //                      class                            //
    // ----------------------------------------------------- //
    ///////////////////////////////////////////////////////////

    ////////////////////////
    // XML to array class //
    ////////////////////////
    class razorXML {
        // variables //
        var $xmlPointer;
        var $keyPointer;
        var $keyCache;
        
        // decode xml and store in array //
        function decodeXML($string) {
            $parser = xml_parser_create();    
            xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);    
            xml_parse_into_struct($parser, $string, $vals, $index);    
            xml_parser_free($parser);
            $this->xmlPointer = 0;
            $xmlArray = $this->sortXMLData($vals);
            return $xmlArray;
        }
        // end ////////////////////////////

        // sort data in to array groupings //
        function sortXMLData($vals) {
            $this->keyCache = $this->keyPointer;
            $this->keyPointer = 0;
            $sortArray = array();    
            foreach ($vals as $key=>$entry) {
                // skip through array to correct part //
                if($key < $this->xmlPointer) {
                    continue;
                }
                $this->xmlPointer++;
                // filter array data copy array data and nest children //
                if($entry['type'] == 'open'){
                    $sortArray[$this->keyPointer] = $entry;
                    unset($sortArray[$this->keyPointer]['level']);
                    $sortArray[$this->keyPointer]['child'] = $this->sortXMLData($vals);
                    $this->keyPointer++;
                } elseif ($entry['type'] == 'close') {
                    $this->keyPointer = $this->keyCache;
                    return $sortArray;
                } elseif ($entry['type'] == 'complete') {
                    $sortArray[$this->keyPointer] = $entry;
                    unset($sortArray[$this->keyPointer]['level']);
                    $this->keyPointer++;
                }
            }
            return $sortArray;
        }
        // end ////////////////////////////
    }
    ///////////////////////////
    // end XML 2 array class //
    ///////////////////////////
?>
