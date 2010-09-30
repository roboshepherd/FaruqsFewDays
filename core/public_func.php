<?php
    ///////////////////////////////////////////////////////////
    // razorCMS                                              //
    // core/public_func.php                                  //
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
    // V0.2  -  06/2008  -  This file contains changes for   //
    //                      up issue to V0.2BETA and RC      //
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

    // message system for process messages //
    function MsgBox($msg,$class='greenbox',$style='',$echo=true) {
        $t = "<div class='$class' style=\"$style\">$msg</div>";
        if( $echo ) {
            echo $t;
        } else {
            return $t;
        }
    }
    // end //////////////////////////////////

    // theme switcher //
    function themeSwitcher($slab){
        global $razorArray, $loadTheme, $loadCSS, $loadScript;
        // set system default theme
        $loadTheme = "theme/default_xhtml.php";

        // set system default css
        $sample = explode('/', $_SERVER['REQUEST_URI']);
        $urloutput = NULL;
        $urloutputb = NULL;
        foreach ($sample as $key=>$value) {
            if ( !strstr($value, '.htm') && $value != '' && !strstr($value, '?') && !strstr($value, '.php')) {
                $urloutput.= '/'.$value;
            }
        }
        $loadCSS = $urloutput.'/'.RAZOR_CSS_FILE;

        // set system default script
        $sampleb = explode('/', $_SERVER['REQUEST_URI']);
        foreach ($sampleb as $keyb=>$valueb) {
            if ( !strstr($valueb, '.htm') && $valueb != '' && !strstr($valueb, '?') && !strstr($valueb, '.php')) {
                $urloutputb.= '/'.$valueb;
            }
        }
        $loadScript = $urloutputb.'/'.RAZOR_SCRIPT_PATH;

        // get theme allocation for slab //
        if(!isset($razorArray['themes'][$slab]) || $razorArray['themes'][$slab] == ''){
            $themeAlloc = 'theme-default';
        } else {
            $themeAlloc = $razorArray['themes'][$slab];
        }

        // check razorArray for correct theme to load
        $getTheme = '';
        BsocketB('public-change-theme', array( &$getTheme ), true);
        if($getTheme != ''){
            foreach($getTheme as $theme){
                $checkTheme = str_replace(RAZOR_BLADEPACK_DIR,'',$theme);
                $checkTheme = explode('/',$checkTheme);
                if(isset($razorArray['settings'][$themeAlloc]) && $razorArray['settings'][$themeAlloc] != ''){
                    if($checkTheme[0] == $razorArray['settings'][$themeAlloc]){
                        $loadTheme = $theme;
                    }
                }
            }
        }

        // check razorArray for correct css to load
        $getCSS = '';
        BsocketB('public-css-address', array(&$getCSS), true);
        if($getCSS != ''){
            foreach($getCSS as $css){
                $checkTheme = str_replace(RAZOR_BLADEPACK_DIR,'',$css);
                $checkTheme = explode('/',$checkTheme);
                if(isset($razorArray['settings'][$themeAlloc]) && $razorArray['settings'][$themeAlloc] != ''){
                    if($checkTheme[0] == $razorArray['settings'][$themeAlloc]){
                        $cssAddress = explode(RAZOR_CSS_FILE, $loadCSS);
                        $loadCSS = $cssAddress[0].$css;
                    }
                }
            }
        }

        // check razorArray for correct script to load
        $getScript = '';
        BsocketB('public-script-address', array( &$getScript ), true);
        if($getScript != ''){
            foreach($getScript as $script){
                $checkTheme = str_replace(RAZOR_BLADEPACK_DIR,'',$script);
                $checkTheme = explode('/',$checkTheme);
                if(isset($razorArray['settings'][$themeAlloc]) && $razorArray['settings'][$themeAlloc] != ''){
                    if($checkTheme[0] == $razorArray['settings'][$themeAlloc]){
                        $scriptAddress = explode(RAZOR_SCRIPT_PATH, $loadScript);
                        $loadScript = $scriptAddress[0].$script;
                    }
                }
            }
        }
    }
    // end /////////////

    // work out file permissions for linux //
    function file_perms($file)
    {
        if(!file_exists($file)) {
            return false;
        }
        $perms = sprintf('%o', fileperms($file));
        if ( substr($perms, 0, 2) == '40' ) {
            $perms = substr($perms, 1, 4);
        } elseif ( substr($perms, 0, 2) == '10' ) {
            $perms = substr($perms, 2, 4);
        }
        return $perms;
    }
    // end /////////////////////////////////

    // check log when logging in //
    function checkLog() {
        $logPath = getSystemRoot(RAZOR_ADMIN_FILENAME).RAZOR_LOGS_DIR.RAZOR_FAILED_LOGIN_LOG;

        // find IP of user and ensure no funny IP injection scripts //
        $userIP = preg_replace('/[^0-9.]/', '', $_SERVER['REMOTE_ADDR']);
        if($userIP == '' || $userIP == NULL) {
            $userIP = 'Could Not Log IP';
        }    
        // check if IP file exists, to speed up processing //
        if (!file_exists( $logPath )) {
            return false;
        }
        // Look for occurancies in file to speed up processing // 
        $tempLog = file_get_contents( $logPath );   
        if(substr_count($tempLog, $userIP) < (RAZOR_LOG_ATT - 1)) {
            return false;
        }
        // read file into array //
        $loginLogArray = array_reverse(file($logPath));
        // shorten array list by certain amount //
        if (count($loginLogArray) > RAZOR_LOG_AMOUNT) {
            $loginLogArray = array_slice($loginLogArray, 0, RAZOR_LOG_AMOUNT);
        }
        // setup variables for log details //
        $currentTime = time();
        $t = 0;
        $c = 0;
        // itterate through shortened log list but end if older than 60 min //
        // then count logs, if greater than set amount reject login         //
        while($c < count($loginLogArray)) {
            $temp = str_replace(array('##',' '),'',$loginLogArray[$c]);
            $tempArray = explode(':', $temp);
            if ($userIP == $tempArray[0]) {
                $t++;
            }
            if (($currentTime - $tempArray[1]) > RAZOR_LOGAT_TIME ) {
                break;
            }
            $c++;            
        }
        // check amount of logins in 60 min //
        if ($t > RAZOR_LOG_ATT) {
            return true;
        }
        return false;
    }
    // end ////////////////////////

    // write failed attempts to a log //
    function loginLog() {
        $contents = '';
        $logPath = getSystemRoot(RAZOR_ADMIN_FILENAME).RAZOR_LOGS_DIR.RAZOR_FAILED_LOGIN_LOG;
        if(!file_exists(getSystemRoot(RAZOR_ADMIN_FILENAME).RAZOR_LOGS_DIR)) {
            return false;
        }
        // find IP of user and ensure no funny IP injection scripts //
        $userIP = preg_replace('/[^0-9.]/', '', $_SERVER['REMOTE_ADDR']);
        if($userIP == '' || $userIP == NULL) {
            $userIP = 'Could Not Log IP';
        }
        // read in any old data //
        if (file_exists( $logPath )) {
            // read file into array //
            $loginLogArray = array_reverse(file($logPath));
            // shorten array list by certain amount //
            if (count($loginLogArray) > 300) {
                $loginLogArray = array_slice($loginLogArray, 0, 300);
            }
            $shortArray = array_reverse($loginLogArray);
            $contents = implode('', $shortArray);
        }
        // create data to write //
        $contents.= '##'.$userIP.':'.time().'##'."\r\n";
        // write IP to log //
        $f=@fopen($logPath,"w");
        if (!$f) {
            return false;
        } else {
            @fwrite($f,$contents);
            fclose($f);
            if (findServerOS() == 'LINUX') { 
                $perms = file_perms($logPath);
                if ( $perms != '0644') {
                    chmod($logPath, 0644);
                }
            }
            return true;
        }
    }
    // end ///////////

    // function to obscure passwords //
    function createHash($inText, $saltHash=NULL, $mode='sha1'){
        // check if hash function available, else fallback to sha1 //
        $hashOK = false;
        if(function_exists('hash')) {
	    $hashOK = true;        
        }
        // hash the text //
        if($hashOK) {
            $textHash = hash($mode, $inText);
        } else {
            $textHash = sha1($inText);
        }
        // set where salt will appear in hash //
        $saltStart = strlen($inText);
        // if no salt given create random one //
        if($saltHash == NULL) {
            if($hashOK) {
                $saltHash = hash($mode, uniqid(rand(), true));
            } else {
                $saltHash = sha1(uniqid(rand(), true));
            }
        }
        // add salt into text hash at pass length position and hash it //
        if($saltStart > 0 && $saltStart < strlen($saltHash)) {
            $textHashStart = substr($textHash,0,$saltStart);
            $textHashEnd = substr($textHash,$saltStart,strlen($saltHash));
            if($hashOK) {
                $outHash = hash($mode, $textHashEnd.$saltHash.$textHashStart);
            } else {
                $outHash = sha1($textHashEnd.$saltHash.$textHashStart);
            }
        } elseif($saltStart > (strlen($saltHash)-1)) {
            if($hashOK) {
                $outHash = hash($mode, $textHash.$saltHash);
            } else {
                $outHash = sha1($textHash.$saltHash);
            }
        } else {
            if($hashOK) {
                $outHash = hash($mode, $saltHash.$textHash);
            } else {
                $outHash = sha1($saltHash.$textHash);
            }
        }
        // put salt at front of hash //
        $output = $saltHash.$outHash;
        return $output;
    }
    // end ///////////////////////////

    // detect server OS type //
    function findServerOS() {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $OSType = 'WIN';
        } elseif (strtoupper(substr(PHP_OS, 0, 5)) === 'LINUX') {
            $OSType = 'LINUX';
        }
        return $OSType;
    }
    // end ////////////////////

    // find location and extention of slab //
    function findPageFile( $name, $locFlag='' ) {
        global $razorArray;
        $pages_Dir = '';
        if ($locFlag == 'admin') {
            $pages_Dir = getSystemRoot(RAZOR_ADMIN_FILENAME).RAZOR_PAGES_DIR;
        } elseif ($locFlag == 'home') {
            $pages_Dir = getSystemRoot(RAZOR_HOME_FILENAME).RAZOR_PAGES_DIR;
        }
        if(file_exists($pages_Dir."$name.".RAZOR_DEFAULT_FILE_EXT)) {
            return $pages_Dir."$name.".RAZOR_DEFAULT_FILE_EXT;
        }
        $ext = explode( ',', RAZOR_EXTENSIONS_ORDER );
	foreach( $ext as $ex ) {
            if( file_exists( $pages_Dir."$name.$ex" ) ) {
                return $pages_Dir."$name.$ex";
            }
        }
	return $pages_Dir."$name.".RAZOR_DEFAULT_FILE_EXT;
    }
    // end ////////////////////////////////////////

    // clean up slab title //
    function cleanSlabTitle($title, $slabID=0) {
        $slabTitle = urlencode($title);
        $catTitle = strtolower( $title );
        BsocketB('clean-title-support', array( &$slabTitle ));
        $slabTitle = strtolower( $slabTitle );
        if ( $slabID == 0 ) {
            $catTitle = str_replace( array('$',';',':','<','>',',',"'",'?','/','*','(',')','@','!','&','='),'',$catTitle );
            $catTitle = str_replace( array('  ','   ','    '),' ', $catTitle );
            $cleanTitle = str_replace( array(' ','+'),'-', $catTitle );
        } else {
            if ( preg_match('!^[^%]+$!', $slabTitle) ) {
                $slabTitle = str_replace( array('$',';',':','<','>',',',"'",'?','/','*','(',')','@','!','&','='),'',$slabTitle );
                $slabTitle = str_replace( array('  ','   ','    '),' ', $slabTitle );
                $cleanTitle = str_replace( array(' ','+'),'-', $slabTitle );
            } else {
                $cleanTitle = RAZOR_DEFAULT_CONTENT_NAME.'id-'.$slabID;
            }
        }
        return $cleanTitle;
    }
    // end //////////////////

    // sets current active page //
    function setActivePage() {
        global $razorArray,$cap,$slabFlag;
        $detectSlab = false;
        $cap = new SLAB();
        if (isset($_GET[RAZOR_DEFAULT_CONTENT_NAME])) {
            $slab = $_GET[RAZOR_DEFAULT_CONTENT_NAME];
        }
        BsocketB('url-in', array( &$slab ));
        if( $slab == ''){
            $slab = $razorArray['homepage'];
        }
        if( !in_array($slab,$razorArray['slabs']) ) {
            BsocketB('detect-new-slab', array( $slab, &$detectSlab ));
            if ( !$detectSlab ) {
                $slab = "404-error-page";
                $headerError = 'HTTP/1.0 404 Not Found';
                BsocketB('404-error-page', array( &$slab ));
                BsocketB('header-error', array( &$headerError ));
                // set header here for 404 //
                header($headerError);
            }
        }
        $theme = themeSwitcher($slab);
        if (isset($slab) && $slab != '') {
            $cap->loadSlab($slab);
            $slabFlag = $slab;
        }
        return $theme;
    }
    // end //////////////////////

    // load slab contents into page //
    function loadSlabContents() {
        global $cap, $razorArray;
        $errorOutput = "<p><br /><br />404 : File Requested was Not Found<br /><br /></p>";
        $slab = $cap->slab;
        $fileAsID = array_search($slab, $razorArray['slabs']);
        $contentFile = findPageFile( $fileAsID, 'home' );
        BsocketB('issue-slab-page-name', array( $slab, &$contentFile ));
        if( file_exists( $contentFile ) ) {
            $contentSlug = file_get_contents( $contentFile );
            BsocketB('include-new-slab', array( $slab, &$contentSlug ));
            BsocketB('scan-content-slug', array( &$contentSlug ));
            echo $contentSlug;
        } else {
            BsocketB('error-page-not-found', array( &$errorOutput ));
            echo $errorOutput;
        }
    }
    // end //////////////////////////

    // load page (slab) title //
    function loadPageTitle() {
        global $cap;
        if($cap->ptitle == ''){
            echo $cap->title;
        } else {
            echo $cap->ptitle;
        }
    }
    // end /////////////////////

    // load settings data into page //
    function loadSettings($areaName) {
        global $razorArray;
        switch ($areaName) {
        case "sitename":
            echo html_entity_decode($razorArray['settings']['site-name'], ENT_QUOTES);
            break;
        case 'siteslogan':
            echo html_entity_decode($razorArray['settings']['site-slogan'], ENT_QUOTES);
            break;
        case 'copyright':
            echo html_entity_decode($razorArray['settings']['copyright-footer'], ENT_QUOTES);
            break;
        }
    }
    // end //////////////////////////

    // load links from a given catagory //
    function loadLinks( $link_cat, $format='', $before='', $after='', $first=true ) {
        global $razorArray;
        $linklist = getLinksArray($link_cat);
        if ( !empty($linklist) ) {
            if( $format == '' ) {
                $format = "<li>%s</li>";
            }
            if( $format == 'nolist' ) {
                $format = '%s';
            }
            if($first){
                if( $format == '<li>%s</li>' ){
                    echo sprintf('<ul class="first">');
                }
            } else {
                if( $format == '<li>%s</li>' ){
                    echo sprintf('<ul>');
                }
            }
            foreach( $linklist as $slab=>$title ) {
                $extLinkFlag = false;
                if ( isset( $razorArray['ext_links'] ) ) {
                    if ( in_array( $slab,array_keys($razorArray['ext_links']) ) ) {
                        $extLinkFlag = true;
                    }
                }
                BsocketB( 'create-link-from-cat-before' );
                if ( $extLinkFlag ) {
                    if ( isset( $razorArray['ext_link_win'][$slab] ) ) {
                        if ( $razorArray['ext_link_win'][$slab] ) {
                            echo sprintf( $format, $before.makeLinkWin( 'http://'.$razorArray['ext_links'][$slab],$title ).$after );
                        } else {
                            echo sprintf( $format, $before.makeLink( 'http://'.$razorArray['ext_links'][$slab],$title ).$after );
                        }
                    } else {
                        echo sprintf( $format, $before.makeLink( 'http://'.$razorArray['ext_links'][$slab],$title ).$after );
                    }
                } else {
                    $addSubCatLinks = '';
                    if ( !isset($razorArray['sub_cat_flag'])) {
                        $razorArray['sub_cat_flag'] = array();
                    }
                    if ( in_array($slab, $razorArray['sub_cat_flag']) ) {
                        $format = "<li>%s";
                    } else {
                        $format = "<li>%s</li>";
                    }
                    $slabUrlIn = slabUrl($slab);
                    if ($slabUrlIn == '?') {
                        $stripURL = explode(RAZOR_HOME_FILENAME,$_SERVER['PHP_SELF']);
                        echo sprintf( $format, $before.makeLink( $stripURL[0],$title ).$after );
                    } else {
                        echo sprintf( $format, $before.makeLink( $slabUrlIn,$title ).$after );
                    }
                    foreach ( $razorArray['sub_cat_flag'] as $subCat=>$underSlab ) {
                        if ( $slab == $underSlab && !empty($razorArray['links_cats'][$subCat])) {
                            $addSubCatLinks = sprintf( loadLinks($subCat, '', '', '', false));
                        }
                    }
                    if ( in_array($slab, $razorArray['sub_cat_flag']) ) {
                        echo sprintf( "</li>" );
                        $format = "<li>%s</li>";
                    }
                }
                BsocketB( 'create-link-from-cat-after' );
            }
            if( $format == '<li>%s</li>' ){
                echo sprintf('</ul>');
            }
        }
    }
    // end //////////////////////////////

    // makes link from slab name and title //
    function makeLink($slab,$title) {
        global $razorArray;
        $slabName = array();
        $active = $razorArray['homepage'];
        if( isset( $_GET[RAZOR_DEFAULT_CONTENT_NAME] ) ) {
            $active = $_GET[RAZOR_DEFAULT_CONTENT_NAME];
        }
        BsocketB('url-in', array( &$active ));
        if ($active == '') {
            $active = $razorArray['homepage'];
        }
        if ($slab == '/') {
            $slabName[1] = $razorArray['homepage'];
        } elseif ( isset($_GET[RAZOR_DEFAULT_CONTENT_NAME]) ) {
            $slabName = explode('=', $slab);
        } else {
            unset($slabNameTemp);
            $slabNameTemp = explode('/', $slab);
            $slabPointer = count($slabNameTemp) - 1;
            if ( $slabNameTemp[$slabPointer] == '' ) {
                $slabName[1] = $razorArray['homepage'];
            } else {
                $slabName[1] = str_replace('.htm', '', $slabNameTemp[$slabPointer]);
            }
        }
        if (isset($slabName[1]) && $active == $slabName[1]) {
            $returnLink = "<a class='active' href='$slab'>$title</a>";
            BsocketB( 'edit-link-creation' , array( &$returnLink,$slab,$title ));
            return $returnLink;
        } else {
            $returnLink = "<a href='$slab'>$title</a>";
            BsocketB( 'edit-link-creation' , array( &$returnLink,$slab,$title ));
            return $returnLink;
        }
    }
    // end /////////////////////////////////

    // makes link from slab name and title with link appearing in new window //
    function makeLinkWin($slab,$title) {
        $returnLink = "<a href='$slab' target='_blank'>$title</a>";
        BsocketB( 'edit-link-creation' , array( &$returnLink,$slab,$title ));
        return $returnLink;
    }
    // end /////////////////////////////////

    // returns url for a slab //
    function slabUrl($slab) {
        global $razorArray; 
        $urlFormat = sprintf(RAZOR_URL_FORMAT,$slab);
        if ($slab == $razorArray['homepage']) {
            $urlFormat = '?';
        }
        BsocketB('url-out', array( &$urlFormat));
        return $urlFormat;
    }
    // end ////////////////////

    // fetch links array //
    function getLinksArray( $cat ) {
        global $razorArray;
        $cdt = $razorArray['links_cats'];
        if( in_array( $cat, array_keys($cdt) ) ) {
            $scids = $cdt[$cat];
            $slabs = $razorArray['slabs'];
            $titles = $razorArray['titles'];
            $dslabs = array();
            foreach( $scids as $sid ) {
                $dslabs[ $slabs[$sid] ] = $titles[$sid];
            }
            return $dslabs;
        } else {
            return '';
        }
    }
    // end ///////////////

    // find system root, specify what to chop off end of running script //
    function getSystemRoot($ignoreEnd) {
        $rootAddress = array();
        $rootAddress = explode($ignoreEnd, $_SERVER['PHP_SELF']);
        if ( $ignoreEnd == RAZOR_ADMIN_FILENAME ) {
             return '../';      
        } else {
            return '';
        }
    }
    // end ///////////////////////////////////////////////////////////////

    // Log Blade packs that have been added //
    function logBladePack( $tLocation, $tName ) {
        global $bladeList;
        $bladeList[ $tLocation ] = array_merge( (array)$bladeList[ $tLocation ], (array)$tName );
    }
    // end //////////////////////////////////

    // Socket function that blades load through //
    function BsocketB( $location, $data=array(), $all = false ) {
        global $bladeList;
        $oldData = $data;
        $tempData = array();
        $blades = array();
        $blades = array_keys($bladeList,$location);
        if( empty($blades) ) {
            return;
        }
        foreach( $blades as $tFunc ) {
            if( is_callable( $tFunc ) ) {
                call_user_func_array( $tFunc, $data );
                if(!empty($data)){
                    array_push($tempData,$data[0]);
                }
                $data = $oldData;
            }
        }
        if($all){
            $data[0] = $tempData;
        }
    }
    // end ///////////////////////////////////////

    // Load page into info box //
    function loadInfoContents() {
        global $razorArray,$slabFlag;
        if (isset($razorArray['settings']['info-bar-cat'])) {
            if( isset( $_GET[RAZOR_DEFAULT_CONTENT_NAME] ) ) {
                $infoKey = $_GET[RAZOR_DEFAULT_CONTENT_NAME];               
            }
            BsocketB('url-in', array( &$infoKey ));
            if ( !$infoKey ) {
                $infoKey = $razorArray['homepage'];
            }
            // scan each infobar content and check if has page allocated against it
            foreach ($razorArray['links_cats']['infobar'] as $slabID) {
                if ( isset($razorArray['info-bar-global']) && in_array($razorArray['slabs'][$slabID],$razorArray['info-bar-global'])){
                    $infoName = $razorArray['slabs'][$slabID];
                    $fileAsID = array_search(strtolower($infoName), $razorArray['slabs']);
                    $infoFile = findPageFile($fileAsID, 'home' );
                    if( file_exists( $infoFile ) ){
                        $contentInfo = file_get_contents( $infoFile );
                        BsocketB('scan-content-info', array( &$contentInfo ));
                        echo $contentInfo;
                    }
                } else {
                    if ( is_array($razorArray['info-bar-key']) && is_array($razorArray['info-bar-value']) ) {
                        if ( in_array($razorArray['slabs'][$slabID], $razorArray['info-bar-key']) ) {
                            foreach ( $razorArray['info-bar-value'] as $key=>$value ) {
                                if ( $value == $infoKey && $razorArray['info-bar-key'][$key] == $razorArray['slabs'][$slabID] ) {
                                    $infoName = $razorArray['slabs'][$slabID];
                                    $fileAsID = array_search(strtolower($infoName), $razorArray['slabs']);
                                    $infoFile = findPageFile($fileAsID, 'home' );
                                    if( file_exists( $infoFile ) ){
                                       $contentInfo = file_get_contents( $infoFile );
                                       BsocketB('scan-content-info', array( &$contentInfo ));
                                       echo $contentInfo;
                                    }
			        }
                            }
	                }
                    }
                }
            }          
        }
    }
    // end /////////////////////

    // find css location (in case of sef url) //
    function cssLocation() {
        global $loadCSS;
        echo $loadCSS;
    }
    // end /////////////////////

    // find script path (in case of sef url) //
    function scriptPath() {
        global $loadScript;
        echo $loadScript;
    }
    // end /////////////////////

    // language interface function //
    function lt( $defaultText ) {
        $text = $defaultText;
        BsocketB('language-select', array(&$text));
        return $text;
    }
    // end /////////////////////////
?>
