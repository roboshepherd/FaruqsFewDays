<?php
    ///////////////////////////////////////////////////////////
    // razorCMS v0.1                                         //
    // admin/index.php                                       //
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
    ///////////////////////////////////////////////////////////

    // start a new login session and fetch admin configuration //
    session_start();
    $oldSessionId = session_id();
    session_regenerate_id();
    include_once("core/admin_config.php");
    include_once("../core/public_config.php");
    include_once("../core/public_func.php");
    include_once("../core/public_class.php");
    /////////////////////////////////////////////////////////////

    // set variables //////////////////////////////////////////
    $razorArray = array();
    $cap;
    $bladeList = array();
    $spareVar1;
    $spareVar2;
    $spareVar3;
    $spareArray1 = array();
    $spareArray2 = array();
    $spareArray3 = array();
    ///////////////////////////////////////////////////////////

    // Load razor data file  //////////////////////////////////
    $razorArray = unserialize( file_get_contents( getSystemRoot(RAZOR_ADMIN_FILENAME).RAZOR_DATA ) );
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
        if(file_exists(getSystemRoot(RAZOR_ADMIN_FILENAME).RAZOR_BLADEPACK_DIR.$bladePack.'.php')) {
            include_once( getSystemRoot(RAZOR_ADMIN_FILENAME).RAZOR_BLADEPACK_DIR.$bladePack.'.php' );
        }
    }
    ///////////////////////////////////////////////////////////

    // edit razor array ///////////////////////////////////////
    BsocketB('admin-edit-razorarray', array( &$razorArray ));
    ///////////////////////////////////////////////////////////

    //  login  //
    if( !isset( $_SESSION['adminLogIn'] ) ) {
        if( isset($_POST['user']) && $_POST['user'] != '' && isset($_POST['pass']) && $_POST['pass'] != '') {
            if (!checkLog()) {
                if( $_POST['user'] == RAZOR_SADMIN_USER and createHash($_POST['pass'],substr(RAZOR_SADMIN_PASS,0,(strlen(RAZOR_SADMIN_PASS)/2)),'sha1') == RAZOR_SADMIN_PASS ) {
                    $_SESSION['loginTimeStamp'] = $ts = time();
                    $_SESSION['adminLogIn'] = sha1($_SERVER['REMOTE_ADDR'].RAZOR_SADMIN_USER.$ts.$_SERVER['HTTP_USER_AGENT']);
		    $_SESSION['adminType'] = 'sadmin';
                } elseif( $_POST['user'] == RAZOR_ADMIN_USER and createHash($_POST['pass'],substr(RAZOR_ADMIN_PASS,0,(strlen(RAZOR_ADMIN_PASS)/2)),'sha1') == RAZOR_ADMIN_PASS ) {
                    $_SESSION['loginTimeStamp'] = $ts = time();
                    $_SESSION['adminLogIn'] = sha1($_SERVER['REMOTE_ADDR'].RAZOR_ADMIN_USER.$ts.$_SERVER['HTTP_USER_AGENT']);
		    $_SESSION['adminType'] = 'admin';
                } elseif( $_POST['user'] == RAZOR_USER_USER and createHash($_POST['pass'],substr(RAZOR_USER_PASS,0,(strlen(RAZOR_USER_PASS)/2)),'sha1') == RAZOR_USER_PASS ) {
                    $_SESSION['loginTimeStamp'] = $ts = time();
                    $_SESSION['adminLogIn'] = sha1($_SERVER['REMOTE_ADDR'].RAZOR_USER_USER.$ts.$_SERVER['HTTP_USER_AGENT']);
		    $_SESSION['adminType'] = 'user';
                } else {
                    loginLog();
                }
            } else {
                MsgBox(lt('You have exceeded the max amount of login attempts in').' '.(RAZOR_LOGAT_TIME/60).' '.lt('minutes'), 'redbox');
            }
        }
    } else {
        if($_SESSION['adminLogIn'] == sha1($_SERVER['REMOTE_ADDR'].RAZOR_SADMIN_USER.$_SESSION['loginTimeStamp'].$_SERVER['HTTP_USER_AGENT'])) {
            $_SESSION['loginTimeStamp'] = $ts = time();
            $_SESSION['adminLogIn'] = sha1($_SERVER['REMOTE_ADDR'].RAZOR_SADMIN_USER.$ts.$_SERVER['HTTP_USER_AGENT']);
            $_SESSION['adminType'] = 'sadmin';
        } elseif($_SESSION['adminLogIn'] == sha1($_SERVER['REMOTE_ADDR'].RAZOR_ADMIN_USER.$_SESSION['loginTimeStamp'].$_SERVER['HTTP_USER_AGENT'])) {
            $_SESSION['loginTimeStamp'] = $ts = time();
            $_SESSION['adminLogIn'] = sha1($_SERVER['REMOTE_ADDR'].RAZOR_ADMIN_USER.$ts.$_SERVER['HTTP_USER_AGENT']);
            $_SESSION['adminType'] = 'admin';
        } elseif($_SESSION['adminLogIn'] == sha1($_SERVER['REMOTE_ADDR'].RAZOR_USER_USER.$_SESSION['loginTimeStamp'].$_SERVER['HTTP_USER_AGENT'])) {
            $_SESSION['loginTimeStamp'] = $ts = time();
            $_SESSION['adminLogIn'] = sha1($_SERVER['REMOTE_ADDR'].RAZOR_USER_USER.$ts.$_SERVER['HTTP_USER_AGENT']);
            $_SESSION['adminType'] = 'user';
        } else {
            @session_destroy();
            unset( $_SESSION['adminLogIn'] );
        }
    }
    // end //////

    //  logout  //
    if( isset( $_GET['logout'] ) )  {
        @session_destroy();
        unset( $_SESSION['adminLogIn'] );
    }
    // end ///////

    //  login form  //

    // setup data //
    $adminLoginTag = lt('Administration Login');
    $userLoginTag = lt('Username');
    $passLoginTag = lt('Password');
    $loginLoginTag = lt('Login');
    // end //

    if( !isset( $_SESSION['adminLogIn'] ) ) {
        @session_destroy();
	BsocketB('admin-pre-login-form');
        $form = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
        $form.= '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">';
        $form.= '<head><title>razorCMS Administration login</title><link rel="stylesheet" type="text/css" href="theme/default_css.css" /></head>';
        $form.= '<body>';
        $form.= "<div id='bracelogin'>
                     <div id='pageframelogin'>
                     <div id='pageframerlogin'>
                         <div id='headermidlogin'>
                         <div id='headerrlogin'>
                         <div id='headerlogin'>
                             <h1>razorCMS</h1>
                             <h2>$adminLoginTag</h2>
                         </div>
                         </div>
                         </div>
                         <div id='midbrace'>
                             <div id='midboxlogin'>
                                 <div id='contentlogin'>
                                     <form id='loginform' action='?' method='post'>
                                     <p>$userLoginTag<br /><input type=text name='user'></p>
                                     <p>$passLoginTag<br /><input type=password name='pass'></p>
                                     <p><input id='button' type='submit' value='$loginLoginTag'></p>
                                     </form>
                                 </div>
                             </div>
                         </div>
                         <div id='footer'>
                         <div id='footerr'>
                         <div id='footerl'>
                         <div id='footnav'>
                             <div id='footerLeft'>
                                 <a href='http://www.razorcms.co.uk'>www.razorcms.co.uk</a>
                             </div>
                             <div id='footerRight'>
                                 <a href='http://www.mis-limited.com'>www.mis-limited.com</a>
                             </div>
                         </div>  
                         </div>
                         </div>
                         </div>
                     </div>
                     </div>
                 </div>";
        $form.= '</body>';
        $form.= '</html>';
        BsocketB('admin-login-form', array( &$form ));
	echo $form;
        exit();
    }
    // end ///////////
    
    // if authenticated, continue //

    // set up functions library ///////////////////////////////
    include_once("core/admin_func.php");
    include_once('core/admin_class.php');
    include_once('lib/zip.lib.php'); 
    include_once('lib/unzip.lib.php');
    include_once('lib/razorXML.lib.php');
    ///////////////////////////////////////////////////////////

    // set up logs directory //////////////////////////////////
    createLogsDir();
    ///////////////////////////////////////////////////////////

    // set page contents to display ///////////////////////////
    setActivePage();
    ///////////////////////////////////////////////////////////

    // socket load points /////////////////////////////////////
    BsocketB('admin-index-socket1');
    BsocketB('admin-index-socket2');
    BsocketB('admin-index-socket3');
    ///////////////////////////////////////////////////////////

    // Load default theme for admin ////////////////////////// 
    $theme = "theme/default_admin_xhtml.php";
    BsocketB('admin-change-theme', array( &$theme ));
    include_once($theme);
    //////////////////////////////////////////////////////////

?> 
