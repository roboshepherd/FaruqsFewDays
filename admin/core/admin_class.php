<?php
    ///////////////////////////////////////////////////////////
    // razorCMS                                              //
    // admin/core/admin_class.php                            //
    // GPLv3                                                 //
    // smiffy6969                                            //
    // www.razorcms.co.uk                                    //
    // www.mis-limited.com                                   //
    // 06/2009                                               //
    // ----------------------------------------------------- //
    // V1.0  -  06/2009  -  ALPHA Security, Bug fix and new  //
    //                      functionality                    //
    // ----------------------------------------------------- //
    // V1.0  -  07/2009  -  BETA Bug fix and new             //
    //                      functionality                    //
    // ----------------------------------------------------- //
    // V1.0  -  08/2009  -  BETA2 Bug fix and new            //
    //                      functionality                    //
    // ----------------------------------------------------- //
    // V1.0  -  09/2009  -  RC Bug fix                       //
    // ----------------------------------------------------- //
    // V1.0  -  10/2009  -  Stable - Bug fix                 //
    // ----------------------------------------------------- //
    // V1.1  -  06/2010  -  Stable - Bug fix + Security fix  //
    // ----------------------------------------------------- //
    ///////////////////////////////////////////////////////////

    ////////////////////////
    // core admin classes //
    ////////////////////////

    /////////////////////////////
    // bladepack install class //
    /////////////////////////////
    class BPCONTROL {

        // variables //
        var $name;
        var $version;
        var $description;
        var $author;
        var $className;
        var $folderName;
        var $xmlName;
        var $bladepackName;
        var $zipContents = array();
        var $xmlContents = array();
        // end //

        // extract data from xml file //
        function extractXmlData() {
            foreach($this->zipContents as $parts) {
                $xmlext = explode('.',$parts->Name);
	        if(end($xmlext) == 'xml') {                    
                    // extract xml data
                    if($this->getXmlData($parts->Data, $parts->Name)) {
                        return true;
                    }                    
                }
            }
            return false;
        }
        // end /////////////
        
        // extract data from xml file //
        function getXmlData($xmlData, $xmlFileName) {
            $xml = new razorXML();
	    $this->xmlContents = $xml->decodeXML($xmlData);
	    $xmlFileData = $this->searchXmlArray($this->xmlContents, 'xml_file');
	    // check xml has correct value for xml install file if it does then return ok
	    if(is_array($xmlFileData) && in_array($xmlFileName, $xmlFileData)) {
	         if($xmlFileData['value'] == $xmlFileName) {
	              return true;
	         }
            } else {
                return false;
            }
        }
        // end /////////////

        // Search through xml array //
        function searchXmlArray($xmlArray, $findTag) {
            foreach($xmlArray as $key=>$content){
                if(is_array($content)) { 
                    $found = $this->searchXmlArray($content, $findTag);
                    if(is_array($found)) {
                        return $found;
                    }
                } else {
                    if ($key == 'tag' && $content == $findTag) {
                        return $xmlArray;
                    }
                }
            }
            return false;
        }
        // end ///////////////////////
        
        // Extract files from archive //
        function extractContents($fileName) {
            $zip = new SimpleUnzip();
            $this->zipContents = $zip->ReadFile('../'.$fileName);
        }
        // end /////////////
        
        // save files to system //
        function saveContents($pathToInstall) {
            foreach($this->zipContents as $parts) {
                if($parts->Path != '') {
                    // check dir exists, if not create it //
                    if(!file_exists('../'.$pathToInstall.$parts->Path)) {
                        $splitPath = array();
		        $splitPath = explode('/', $parts->Path);
		        $checkPath = '../'.$pathToInstall;
		        foreach($splitPath as $pathBit){
                            $checkPath.= $pathBit;
		            if(!file_exists($checkPath)) {
		                mkdir($checkPath, 0755);
		            }
		            $checkPath.= '/';
		        }
                    }
                    $path = $parts->Path.'/';
                } else {
                    $path = '';
                }
                put2file($pathToInstall.$path.$parts->Name, $parts->Data);
            }
        }
        // end /////////////       

        // Check contents //
        function checkContents($pathToInstall) {
            foreach($this->zipContents as $parts) {
                // check files exist //
                if($parts->Path == ''){
                    $slash = '';
                } else {
                    $slash = '/';
                }
                if(file_exists('../'.$pathToInstall.$parts->Path.$slash.$parts->Name)) {
                    return false;
                }
            }
            return true;
        }
        // end /////////////
        
        // get blade pack name //	
	function getBPName() {
	    $xmlFileData = $this->searchXmlArray($this->xmlContents, 'name');
            if(is_array($xmlFileData)) {
                if($xmlFileData['value'] != '') {
                    $this->name = $xmlFileData['value'];
                    return true;
                }
            }
            return false;
	}
        // end ////////////
        
        // get blade pack name //	
	function getBPVersion() {
	    $xmlFileData = $this->searchXmlArray($this->xmlContents, 'version');
            if(is_array($xmlFileData)) {
                if($xmlFileData['value'] != '') {
                    $this->version = $xmlFileData['value'];
                    return true;
                }
            }
            return false;
	}
        // end ////////////
        
        // get blade pack description //	
        function getBPDesc() {
	    $xmlFileData = $this->searchXmlArray($this->xmlContents, 'description');
            if(is_array($xmlFileData)) {
                if($xmlFileData['value'] != '') {
                    $this->description = $xmlFileData['value'];
                    return true;
                }
            }
            return false;
        }
        // end ////////////
        
        // get blade pack author //	
        function getBPAuth() {
	    $xmlFileData = $this->searchXmlArray($this->xmlContents, 'author');
            if(is_array($xmlFileData)) {
                if($xmlFileData['value'] != '') {
                    $this->author = $xmlFileData['value'];
                    return true;
                }
            }
            return false;
        }
        // end ////////////

        // get xml file name //	
        function getXMLName() {
	    $xmlFileData = $this->searchXmlArray($this->xmlContents, 'xml_file');
            if(is_array($xmlFileData)) {
                if($xmlFileData['value'] != '') {
                    $this->xmlName = $xmlFileData['value'];
                    return true;
                }
            }
            return false;
        }
        // end ////////////

        // get folder name //	
        function getDIRName() {
	    $xmlFileData = $this->searchXmlArray($this->xmlContents, 'bladepack_dir');
            if(is_array($xmlFileData)) {
                if($xmlFileData['value'] != '') {
                    $this->folderName = $xmlFileData['value'];
                    return true;
                }
            }
            return false;
        }
        // end ////////////

        // get BP file name //	
        function getBFName() {
	    $xmlFileData = $this->searchXmlArray($this->xmlContents, 'bladepack_file');
            if(is_array($xmlFileData)) {
                if($xmlFileData['value'] != '') {
                    $this->bladepackName = $xmlFileData['value'];
                    return true;
                }
            }
            return false;
        }
        // end ////////////

        // get BP class type //
        function getBPClass() {
            $xmlFileData = $this->searchXmlArray($this->xmlContents, 'class');
            if(is_array($xmlFileData)) {
                if($xmlFileData['value'] != '') {
                    $this->className = $xmlFileData['value'];
                    return true;
                }
            }
            return false;
        }
        // end ////////////
    }
    /////////////////////////////////
    // end bladepack install class //
    /////////////////////////////////
?>
