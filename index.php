<?php
    ///////////////////////////////////////////////////////////
    // razorCMS                                              //
    // index.php                                             //
    // GPLv3                                                 //
    // smiffy6969                                            //
    // www.razorcms.co.uk                                    //
    // www.mis-limited.com                                   //
    // 03/2008                                               //
    // ----------------------------------------------------- //
    // V0.1  -  03/2008  -  Version 0.1 first release        //
    // ----------------------------------------------------- //
    // V0.2  -  06/2008  -  No changes in this file          //
    //                      up issue to V0.2BETA or RC       //
    // ----------------------------------------------------- //
    // V0.2  -  08/2008  -  RC2 Bug fix release              //
    //                      No changes to this file          //
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

    // start session for blade packs requiring session ////////
    session_start();
    session_regenerate_id();
    ///////////////////////////////////////////////////////////

    // check for presence of install file or password file ////
    if (file_exists('install.php')) {
    	die('Please remove install.php file before using system');
    }
    if (file_exists('passchange.php')) {
    	die('Please remove passchange.php file before using system');
    }
    ///////////////////////////////////////////////////////////

    // Load core config and core libraries ////////////////////
    include_once("core/public_config.php");
    include_once("core/public_func.php");
    include_once("core/public_class.php");
    ///////////////////////////////////////////////////////////

    // set variables //////////////////////////////////////////
    $razorArray = array();
    $cap;
    $slabFlag;
    $bladeList = array();
    $loadTheme = '';
    $loadCSS = '';
    $loadScript = '';
    // spare //
    $spareVar1;
    $spareVar2;
    $spareVar3;
    $spareArray1 = array();
    $spareArray2 = array();
    $spareArray3 = array();
    ///////////////////////////////////////////////////////////

    // start buffering of output //////////////////////////////
    ob_start();
    ///////////////////////////////////////////////////////////

    // Load razor data file  //////////////////////////////////
    $razorArray = unserialize( file_get_contents( getSystemRoot(RAZOR_HOME_FILENAME).RAZOR_DATA ) );
    ///////////////////////////////////////////////////////////
    
    // Setup headers to set charset ///////////////////////////
    $charset = 'ISO-8859-1';
    if(isset($razorArray['settings']['charset'])){
        $charset = $razorArray['settings']['charset'];
    }
    header("Content-type: text/html; charset=$charset");
    ///////////////////////////////////////////////////////////
    
    // load active installed blades ///////////////////////////
    foreach( $razorArray['active-bladepack'] as $bladePack ) {
        if(file_exists(getSystemRoot(RAZOR_HOME_FILENAME).RAZOR_BLADEPACK_DIR.$bladePack.'.php')) {
            include_once( getSystemRoot(RAZOR_HOME_FILENAME).RAZOR_BLADEPACK_DIR.$bladePack.'.php' );
        }
    }
    ///////////////////////////////////////////////////////////

    // edit razor array ///////////////////////////////////////
    BsocketB('public-edit-razorarray', array( &$razorArray ));
    ///////////////////////////////////////////////////////////

    // set page contents to display ///////////////////////////
    $theme = setActivePage();
    ///////////////////////////////////////////////////////////

    // socket load points /////////////////////////////////////
    BsocketB('public-index-socket1');
    BsocketB('public-index-socket2');
    BsocketB('public-index-socket3');
    ///////////////////////////////////////////////////////////

    // end buffering of output ////////////////////////////////
    ob_end_flush();
    ///////////////////////////////////////////////////////////

    // Load theme for public //////////////////////////////////
    if(isset($razorArray['settings']['maintenance']) && $razorArray['settings']['maintenance'] && !isset($_SESSION['adminLogIn'])) {
        include_once('theme/maintenance_xhtml.php');
    } else {
        include_once($loadTheme);
    }
    ///////////////////////////////////////////////////////////
?>
