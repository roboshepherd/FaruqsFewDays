<?php
    ///////////////////////////////////////////////////////////
    // razorCMS                                              //
    // core/public_class.php                                 //
    // GPLv3                                                 //
    // smiffy6969                                            //
    // www.razorcms.co.uk                                    //
    // www.mis-limited.com                                   //
    // 03/2008                                               //
    // ----------------------------------------------------- //
    // V0.1  -  03/2008  -  Version 0.1 first release        //
    //                                                       //
    //                      Some parts loosely based on      //
    //                      nanoCMS V0.3 - with thanks to    //
    //                      Kalyan Chakravarthy              //
    // ----------------------------------------------------- //
    // V0.2  -  06/2008  -  No changes in this file          //
    //                      up issue to V0.2BETA or RC       //
    // ----------------------------------------------------- //
    // V0.2  -  08/2008  -  RC2 Bug fix release              //
    //                      Changes in this file             //
    // ----------------------------------------------------- //
    // V0.3  -  11/2008  -  BETA1 Bug fix and new features   //
    // ----------------------------------------------------- //
    // V0.3  -  12/2008  -  RC Bug fix and code walk         //
    // ----------------------------------------------------- //
    // V0.3  -  02/2009  -  RC2 Bug fix, code walk and some  //
    //                      movement of functions.           //
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

    /////////////////////////
    // core public classes //
    /////////////////////////

    ////////////////
    // slab class //
    ////////////////
    class SLAB {
        var $cats = array();
        var $slab;
        var $slabId;
        var $title;
        var $ptitle;
        var $theme;
        var $isEdited=false;

        // Initialize new blank page //
        function newSlabInit() {
            global $razorArray;
            $cnt = $razorArray['slab_count'];
            $razorArray['slab_count'] = ++$cnt;
            $this->slabId = $cnt;
        }
        // end ///////////////////////

        // create slab name from title  //
        function editTitle($s) {
            if( $s != $this->slab ) {
                $this->isEdited=true;
            }
            $this->title = $s;
            $this->slab = cleanSlabTitle( $s,$this->slabId );
        }
        // end /////////////
        
        // create slab name from title  //
        function editPTitle($s) {
            if( $s != $this->slab ) {
                $this->isEdited=true;
            }
            $this->ptitle = $s;
        }
        // end /////////////
        
        // create slab name from title  //
        function editTheme($t) {
            if( $t != $this->theme ) {
                $this->isEdited=true;
            }
            $this->theme = $t;
        }
        // end /////////////
        
        // check if the page is present in the test category //	
        function isInCat($c) { 
            if( array_search($c,$this->cats) ) {
                return true;
            } else {
                return false;
            }
        }
        // end ////////////////////////////////////////////////

        // add the current page to a category //
        function addToCat($cat) {
            // check for admin access to this function // 
            if( !($_SESSION['adminLogIn']) ) {
                die("Access Denied");
            }
            // end - if no access granted - do not continue /////
            if( !$this->isInCat($cat) ) {
                $this->isEdited=true;
                $this->cats[] = $cat;
            }
        }
        // end ////////////////////////////////

        // reset the catetory listing //
        function catReset() {
            $this->cats = array();
        }
        // end /////////////////////////

        // remove catagory //
        function removeCat($ca) {
            $catInd = array_flip( $this->cats );
            unset( $this->cats[ $catInd[$ca] ] );
        }
        // end //////////////

        // laod slab details //
        function loadSlabDetails() {
            global $razorArray;
            $tt = $razorArray['titles'];
            $oCats = $razorArray['links_cats'];
            $sid = $this->slabId;
            $this->title = $tt[ $this->slabId ];
            if(!isset($razorArray['ptitles'][$this->slabId])){
                $this->ptitle = '';
            } else {
	        $this->ptitle = $razorArray['ptitles'][$this->slabId];
            }
            if(!isset($razorArray['themes'][$this->slab])){
                $this->theme = '';
            } else {
                $this->theme = $razorArray['themes'][$this->slab];
            }
            BsocketB('create-new-slab-title', array( $this->slab, &$this->title ));
            foreach( $oCats as $oCat=>$oCatC ) {
                if( in_array($sid,$oCatC) ) {
                    $this->cats[] = $oCat;
                }
            }
        }
        // end /////////////////

        // load page details from a slab //
        function loadSlab($slab) {
            global $razorArray;
            $detectSlab = false;
            $slabs = $razorArray['slabs'];
            $sids = array_flip($slabs);
            if( !in_array($slab,$slabs) ) {
                BsocketB('detect-new-slab', array( $slab, &$detectSlab ));
                if ( !$detectSlab ) {
                    return false;
                }
            }   
            $this->slab = $slab;
            if ( !$detectSlab ) {
                $this->slabId = $sids[$slab];
            }
            $this->loadSlabDetails();
        }
        // end ///////////////////////////

        // commit changes made //
        function commitChanges() {
            // check for admin access to this function // 
            if( !($_SESSION['adminLogIn']) ) {
                die("Access Denied");
            }
            // end - if no access granted - do not continue /////
            global $razorArray;
            $catList = $razorArray['links_cats'];
            $sd = $razorArray['slabs'];
            $tt = $razorArray['titles'];
            $tt[ $this->slabId ] = $this->title;
            $sd[ $this->slabId ] = $this->slab;
            foreach($catList as $catName=>$catslabs) {
                $isCategoryInOurList = in_array( $catName ,$this->cats );
                $isslabInMasterCategory = in_array( $this->slabId, $catslabs );
                if(  $isCategoryInOurList and !$isslabInMasterCategory ) {
                    array_push($catList[$catName],$this->slabId);
                }
                if(  !$isCategoryInOurList and $isslabInMasterCategory ) {
                    $catslabsIndexes = array_flip( $catList[$catName] );
                    array_splice( $catList[$catName], $catslabsIndexes[$this->slabId],1 );
                }
            }
            $razorArray['links_cats'] = $catList;
            $razorArray['slabs'] = $sd;
            $razorArray['titles'] = $tt;
            if(isset($this->theme) && !empty($this->theme)) {
                $razorArray['themes'][$this->slab] = $this->theme;
            }
            if(isset($this->ptitle)) {
	        $razorArray['ptitles'][$this->slabId] = $this->ptitle;
            }
        }
        // end ///////////////////
    }
    ////////////////////
    // end page class //
    ////////////////////
?>
