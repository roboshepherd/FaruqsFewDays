<?php 
    ///////////////////////////////////////////////////////////
    // razorCMS                                              //
    // admin/core/admin_func.php                             //
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
    // V1.0  -  10/2009  -  Stable Bug fix                   //
    // ----------------------------------------------------- //
    // V1.1  -  06/2010  -  Stable - Bug fix + Security fix  //
    // ----------------------------------------------------- //
    ///////////////////////////////////////////////////////////

    ///////////////////////////////////////////////////////////
    // ################## General functions ################ //
    ///////////////////////////////////////////////////////////

    // check for admin access to this function library // 
    if( !($_SESSION['adminLogIn']) ) {
        die("Access Denied");
    }
    // end - if no access granted - do not continue /////

    // switch site to maintenance mode //
    function maintenanceMode() {
        global $razorArray;
        if(isset($_GET['action']) && $_GET['action'] == 'maintenance') {
            if(!isset($razorArray['settings']['maintenance'])) {
                $razorArray['settings']['maintenance'] = false;
            }
            if($razorArray['settings']['maintenance'] == false) {
                $razorArray['settings']['maintenance'] = true;
            } else {
                $razorArray['settings']['maintenance'] = false;
            } 
            saveRazorArray();
            if($razorArray['settings']['maintenance'] == false){
                MsgBox('<h1>'.lt('Maintenance Mode - Deactivated').'</h1>');
                MsgBox('<p>'.lt('Your website front end is now visable to the public').'</p>');
            } else {
                MsgBox('<h1>'.lt('Maintenance Mode - Activated').'</h1>', 'redbox');
                MsgBox('<p>'.lt('Your website front end is now not visable to the public, it has been replaced by a maintenance page').'</p>', 'redbox');
            }
        } 
    } 
    
    // check size of upload limit //
    function getMaxUploadSize($value) {
        // char = strip numbers from variable
        $num = preg_replace('/[^0-9]/', '', $value);
        // num = strip chars from variable
        $char = preg_replace('/[^a-zA-Z]/', '', $value);
        // convert char to lowercase
        $char = strtolower($char);
        // adjust value based on suffix (ignore additional letters)
        switch ($char[0]) {
            case 'g':
                $num = $num * 1000000000;
            break;
            case 'm':
                $num = $num * 1000000;
            break;
            case 'k':
                $num = $num * 1000;
            break;
        }
        return $num;
    }
    // end /////////////////////////

    // new function to show who logged in as //
    function loggedInAs() {
        if($_SESSION['adminType'] == 'user') {
            echo lt('Logged in as').' <b>'.lt('User').'</b>';
        } elseif($_SESSION['adminType'] == 'admin') {
            echo lt('Logged in as').' <b>'.lt('Administrator').'</b>';
        } elseif($_SESSION['adminType'] == 'sadmin') {
            echo lt('Logged in as').' <b>'.lt('Super Administrator').'</b>';
        }
    }
    // end ////////////////////////////////////

    // create logs dir //
    function createLogsDir() {
        if(file_exists(getSystemRoot(RAZOR_ADMIN_FILENAME).RAZOR_LOGS_DIR)) {
            if(!@chmod(getSystemRoot(RAZOR_ADMIN_FILENAME).RAZOR_LOGS_DIR, 0755)) {
                deleteDirR(RAZOR_LOGS_DIR);
            } else {
                return true;
            }
        }
        // make directory for logs //
        mkdir(getSystemRoot(RAZOR_ADMIN_FILENAME).RAZOR_LOGS_DIR);
        if (findServerOS() == 'LINUX') {
            @chmod(getSystemRoot(RAZOR_ADMIN_FILENAME).RAZOR_LOGS_DIR, 0755);
        }
    }
    // end //////////////

    // function for fetching remote file from server //
    function fetchRemoteFile($url) {
        // get host name and url path //
        $parsedUrl = parse_url($url);
        $host = $parsedUrl['host'];
        if (isset($parsedUrl['path'])) {
            $path = $parsedUrl['path'];
        } else {
            // url is pointing to host //
            $path = '/';
        }
        if (isset($parsedUrl['query'])) {
            $path.= '?' . $parsedUrl['query'];
        }
        if (isset($parsedUrl['port'])) {
            $port = $parsedUrl['port'];
        } else {
            $port = '80';
        }
        $timeOut = 3;
        $reply = '';
        // connect to remote server //
        $fp = @fsockopen($host, '80', $errno, $errstr, $timeOut );
        if( !$fp ) {
            return false;
        } else {
            // send headers //
            $headers = "GET $path HTTP/1.0\r\n";
            $headers.= "Host: $host\r\n";
            $headers.= "Referer: http://$host\r\n";
            $headers.= "Connection: Close\r\n\r\n";
            fwrite($fp, $headers);
            // retrieve the reply //
            while (!feof($fp)) {
                $reply.= fgets($fp, 256);
            }
            fclose($fp);
            // strip headers //
            $tempStr = strpos($reply, "\r\n\r\n");
            $reply = substr($reply, $tempStr + 4);
        }
        // return content //
        return $reply;
    }
    // end //////////////////////////////

    // rename a dir or file //
    function renameFile($oldFileName, $newFileName, $msgOut = true) {
        if ( @rename(getSystemRoot(RAZOR_ADMIN_FILENAME).$oldFileName, getSystemRoot(RAZOR_ADMIN_FILENAME).$newFileName) ) {
            if ($msgOut) {
                MsgBox( lt('rename complete'), 'greenbox' );
            }
            return true;
        } else {
            if ($msgOut) {
                MsgBox( lt('rename error'), 'redbox' );
            }
            return false;  
        }
    }
    // end ///////////

    // create a directory //
    function createDir($dirToCreate, $msgOut = true) {
        $dirPath = getSystemRoot(RAZOR_ADMIN_FILENAME).$dirToCreate;
        if (mkdir($dirPath)) {
            if (findServerOS() == 'LINUX') {
                $perms = file_perms($dirPath);
                if ( $perms != '0755') {
                    @chmod($dirPath, 0755);
                }
            }
            if($msgOut) {
                MsgBox( lt('Directory created'), 'greenbox' );
            }
            return true;
        } else {
            if($msgOut) {
                MsgBox( lt('Error creating directory'), 'redbox' );
            }
            return false;
        }
    }
    // end ///////////

    // copy dir plus contents //
    function copyDirR($fromDir, $toDir) {
        $result = false;
        $readFromDir = getSystemRoot(RAZOR_ADMIN_FILENAME).$fromDir;
        $readToDir = getSystemRoot(RAZOR_ADMIN_FILENAME).$toDir;
        createDir($toDir, false);
        if (is_dir($readFromDir)) {
            $filesArray = array();
            $filesArray = readDirContents($readFromDir);
            // do recursive delete if dir contains files //
            foreach($filesArray as $name) {
                if (is_dir($readFromDir.'/'.$name)) {
                    $result = copyDirR($fromDir.'/'.$name, $toDir.'/'.$name);
                } elseif (file_exists($readFromDir.'/'.$name)) {
                    $result = copyFile($fromDir.'/'.$name, $toDir.'/'.$name, false);
                }
            }
        }
        return $result;
    }
    // end ///////////

    // copy a file //
    function copyFile($copyFrom, $copyTo, $msgOut = true) {
        $fileFrom = getSystemRoot(RAZOR_ADMIN_FILENAME).$copyFrom;
        $fileTo = getSystemRoot(RAZOR_ADMIN_FILENAME).$copyTo;
        if (copy($fileFrom, $fileTo)) {
            if (findServerOS() == 'LINUX') {
                $perms = file_perms($fileTo);
                if ( $perms != '0644') {
                    @chmod($fileTo, 0644);
                }
            }
            if($msgOut) {
                MsgBox( lt('File copy complete'), 'greenbox' );
            }
            return true;
        } else {
            if($msgOut) {
                MsgBox( lt('File copy error'), 'redbox' );
            }
            return false;
        }
    }
    // end ///////////

    // delete file //
    function deleteFile($remote_file, $msgOut = true) {
        if ( @unlink(getSystemRoot(RAZOR_ADMIN_FILENAME).$remote_file) ) {
            if ($msgOut) {
                MsgBox( lt('file deleted'), 'greenbox' );
            }
            return true;
        } else {
            if ($msgOut) {
                MsgBox( lt('file not deleted'), 'redbox' );
            }
            return false;  
        }
    }
    // end ///////////

    // delete dir plus contents //
    function deleteDirR($remoteDir) {
        $errorMess = true;
        $readDir = getSystemRoot(RAZOR_ADMIN_FILENAME).$remoteDir;
        if (is_dir($readDir)) {
            $filesArray = array();
            $filesArray = readDirContents($readDir);
            // do recursive delete if dir contains files //
            foreach($filesArray as $name) {
                if (is_dir($readDir.'/'.$name)) {
                    deleteDirR($remoteDir.'/'.$name);

                } elseif (file_exists($readDir.'/'.$name)) {
                    deleteFile($remoteDir.'/'.$name, false);
                }
            }
            // remove dir //
            if (rmdir($readDir)) {
                $errorMess = false;
            } else {
                $errorMess = true;
            }
        } else {
            $errorMess = true;
        }
        return $errorMess;
    }
    // end ///////////

    // write to file //
    function put2file($remote_file,$contents) {
        $filename = getSystemRoot(RAZOR_ADMIN_FILENAME).$remote_file;
        $f=@fopen($filename,"w");
        if (!$f) {
            return false;
        } else {
            fwrite($f,$contents);
            fclose($f);
            if (findServerOS() == 'LINUX') {
                $perms = file_perms($filename);
                if ( $perms != '0644') {
                    @chmod($filename, 0644);
                }
            }
            return true;
        }
    }
    // end ///////////

    // upload file //
    function uploadFile($remoteFile, $uploadFile) {
        $filename = getSystemRoot(RAZOR_ADMIN_FILENAME).$remoteFile;
        if (move_uploaded_file($uploadFile, $filename)) {
            MsgBox( lt('File successfully uploaded'), 'greenbox' );
            return true;
        } else {
            MsgBox( lt('File upload error'), 'redbox' );
            return false;
        }
    }
    // end ///////////

    // save changes serialise data and write to file//
    function saveRazorArray() {
        global $razorArray;
        $pagesdata = serialize( $razorArray );
        if( !put2file( RAZOR_DATA, $pagesdata ) ) {
            MsgBox( lt('file writing error'), 'redbox' );
        }
    }
    // end ///////////////////////////////////////////

    // read dir contents //
    function readDirContents($dir) {
        $imageFiles = array();
        $imageContents = opendir($dir);
        if (! $imageContents) {
            die('Cannot list files for ' . $dir);
        }
        while ($imageFilename = readdir($imageContents)) {
            if ($imageFilename == '.' || $imageFilename == '..') {
                continue;
            }
            $imageFiles[$imageFilename] = $imageFilename;
        }
        closedir($imageContents);
        return $imageFiles;
    }
    // end read dir contents //

    // read all dir recursively and return dir only //
    function readAllDirR($dir) {
        $imageFiles = array();
        $imageContents = opendir($dir);
        if (! $imageContents) {
            die('Cannot list files for ' . $dir);
        }
        while ($imageFilename = readdir($imageContents)) {
            if ($imageFilename == '.' || $imageFilename == '..' || !is_dir($dir.'/'.$imageFilename)) {
                continue;
            }
            array_push($imageFiles, $imageFilename);
            $subArray = array();
            $subArray = readAllDirR($dir.'/'.$imageFilename);
            if (!empty($subArray)) {
                foreach($subArray as $key=>$sub) {
                    array_push($imageFiles, $imageFilename.'/'.$sub);
                }
            }
        }
        closedir($imageContents);
        return $imageFiles;
    }
    // end read dir contents //

    // chmod dir or file //
    function chmodDirFile($path, $perm) {
        if (!file_exists(getSystemRoot(RAZOR_ADMIN_FILENAME).$path)) {
            die('Invalid file or directory');
        }
        if ( @chmod(getSystemRoot(RAZOR_ADMIN_FILENAME).$path, $perm) ) {
            MsgBox( lt('Permission set'), 'greenbox' );
            return true;
        } else {
            MsgBox( lt('Error, could not set permission'), 'redbox' );
            return false;  
        }
    }
    // end ////////////////

    // chmod all dir in install root recursively //
    function chmodAllDir($path, $perm, $rec = false) {
        $readLoc = getSystemRoot(RAZOR_ADMIN_FILENAME).$path;
        $dirContents = readDirContents($readLoc);
        foreach($dirContents as $item){
            if ($item != '.' && $item != '..' && is_dir($readLoc.$item)) {
                chmodAllDir($path.$item.'/', $perm);
                if ($perm == 'safe') {
                    @chmod($readLoc.$item, 0755);
                } elseif ($perm == 'unsafe') {
                    @chmod($readLoc.$item, 0777);
                }
            }
        }
    }
    // end ////////////////

    // chmod all file in install root recursively //
    function chmodAllFile($path, $perm, $rec = false) {
        $readLoc = getSystemRoot(RAZOR_ADMIN_FILENAME).$path;
        $dirContents = readDirContents($readLoc);
        foreach($dirContents as $item){
            if ($item != '.' && $item != '..' && !is_dir($readLoc.$item)) {
                if ($perm == 'safe') {
                    @chmod($readLoc.$item, 0644);
                } elseif ($perm == 'unsafe') {
                    @chmod($readLoc.$item, 0777);
                }
            } elseif ($item != '.' && $item != '..' && is_dir($readLoc.$item)) {
                chmodAllFile($path.$item.'/', $perm);
            }
        }
    }
    // end ////////////////

    // read all dir permissions in install root recursively //
    function readAllDirPerm($path) {
        $permArray = array();
        $subPermArray = array();
        $dirContents = readDirContents($path);
        foreach($dirContents as $item){
            if ($item != '.' && $item != '..' && is_dir($path.$item)) {
                $permArray[$path.$item] = fileperms($path.$item.'/');
                $subPermArray = readAllDirPerm($path.$item.'/');
                if (count($subPermArray) != 0) {
                    foreach($subPermArray as $key=>$dir) {
                        $permArray[$key] = $dir;
                    }
                }
            }
        }
        return $permArray;
    }
    // end ////////////////

    // read all file permissions in install root recursively //
    function readAllFilePerm($path) {
        $permArray = array();
        $subPermArray = array();
        $dirContents = readDirContents($path);
        foreach($dirContents as $item){
            if ($item != '.' && $item != '..' && !is_dir($path.$item)) {
                $permArray[$path.$item] = fileperms($path.$item);
            } else {
                $subPermArray = readAllFilePerm($path.$item.'/');
                if (count($subPermArray) != 0) {
                    foreach($subPermArray as $key=>$dir) {
                        $permArray[$key] = $dir;
                    }
                }
            }
        }
        return $permArray;
    }
    // end ////////////////

    // create checkbox list for forms //
    function checkBoxList($checkList,$selected=array() ,$return=true) {
        $op='';
        if ( is_array($checkList) ) {
            foreach( $checkList as $key=>$ele ) {
                $keyname = $key;
                $checked = ( in_array($key,$selected)?" checked=checked ":'' );
                $val = "<span class='caps'>$key</span>";
                $op.= "<input type='checkbox' id='check_$keyname' name='check_$keyname' value='$key' $checked><label for='check_$keyname'>$val</label>";
                $op.= "&nbsp; &nbsp; &nbsp;";
            }
        } else {
            $checked = ( in_array($checkList,$selected)?" checked=checked ":'' );
            $val = "<span class='caps'>$checkList</span>";
            $op.= "<input type='checkbox' id='check_$checkList' name='check_$checkList' value='$checkList' $checked><label for='check_$checkList'>$val</label>";
            $op.= "&nbsp; &nbsp; &nbsp;";
        }
        if($return) {
            return $op;
        } else {
            echo $op;
        }
    }
    // end ////////////////////////////

    // selection box in html forms //
    function pagesList($fieldname,$list,$selected=1) {
        $t = "<select name='$fieldname'>";
        foreach( $list as $slabid=>$title ) {
            if( $slabid == $selected ) {
                $chk = " selected='selected' ";
            } else {
                $chk = '';
            }
            $t .= "<option value='$slabid' $chk>$title</option>";
        }
        $t .= "</select>";
        return $t;
    }
    // end //////////////////////////

    // admin master function switch //
    function adminFuncSwitch() {
        if( isset($_GET['action']) ) {
            switch ($_GET['action']) {
            case "addpage":
                $pageAdded = addPage();
                if($pageAdded){
                    performEdit($pageAdded);
                }
            break;
            case "extlink":
                $xLinkAdded = addExtLink();
                if($xLinkAdded){
                    performEditLink($xLinkAdded);
                }
            break;
            case "addinfo":
                $pageAdded = addPage();
                if($pageAdded){
                    performEdit($pageAdded);
                }
            break;
            case 'delete':
                doDelete();
            break;
            case 'edit':
                performEdit();
            break;
            case 'editinfo':
                performEdit();
            break;
            case 'editextlink':
                performEditLink();
            break;
            case 'showpages':
                manageContent();
            break;
            case 'showcats':
                manageCats();
            break;
            case 'showinfobar':
                manageInfobar();
            break;
            case 'fileman':
                fileManager();
            break;
            case 'filemanview':
                fileManager();
            break;
            case 'backuptool':
                backupTool_settings();
            break;
            case 'reordercat':
                performMove();
                manageCats();
            break;
            case 'reorderinfo':
                performMove();
                manageInfobar();
            break;
            case 'blademan':
                if($_SESSION['adminType'] != 'user') {
	    	    showBladePacks('system');
                }
            break;
            case 'bladesystem':
                if($_SESSION['adminType'] != 'user') {
                    showBladePacks('system');
                }
            break;
            case 'bladetheme':
                if($_SESSION['adminType'] != 'user') {
                    showBladePacks('theme');
                }
            break;
            case 'bladelanguage':
                if($_SESSION['adminType'] != 'user') {
                    showBladePacks('language');
                }
            break;
            case 'bladeupgrade':
                if($_SESSION['adminType'] != 'user') {
                    showBladePacks('upgrade');
                }
            break;
            case 'bladeinstall':
                if($_SESSION['adminType'] != 'user') {
		    bladepackInstall();
                }
            break;
            case 'coresettings':
                if($_SESSION['adminType'] != 'user') {
		    coreSettings();
                }
            break;
            case 'settingsman':
                if($_SESSION['adminType'] != 'user') {
		    bladeSettings();
                }
            break;
            case 'usermanager':
                if($_SESSION['adminType'] == 'admin') {
                    userManager('admin');
                } else {
                    userManager();
                }
            break;
            case 'userdata':
                userManager();
            break;
            case 'admindata':
                userManager('admin');
            break;
            case 'sadmindata':
                userManager('sadmin');
            break;
            case 'version':
                versionCheck();
            break;
            case 'helpinfo':
                helpAndInfo();
            break;
            case 'maintenance':
                maintenanceMode();
            break;
            default:
                $foundMenu = false;
                BsocketB( 'admin-page-select' , array( &$foundMenu ) );
                if (!$foundMenu) {
                    versionCheck();
                }
            }
        } else {
            versionCheck();
        }
    }
    // end ///////////////////////////

    // new function to add links into topnav for admin back end //
    function loadAdminTopLinks() {
        $contentMan = "";
        $fileMan = "";
        $bladeMan = "";
        $settingsMan = "";
        $userMan = "";
        $backupMan = "";
        $homePage = "";

        if( isset($_GET['action']) ) {
            switch ($_GET['action']) {
            case "addpage":
                $contentMan = "class='active'";
            break;
            case "extlink":
                $contentMan = "class='active'";
            break;
            case "addinfo":
                $contentMan = "class='active'";
            break;
            case 'delete':
                $contentMan = "class='active'";
            break;
            case 'edit':
                $contentMan = "class='active'";
            break;
            case 'editinfo':
                $contentMan = "class='active'";
            break;
            case 'editextlink':
                $contentMan = "class='active'";
            break;
            case 'reordercat':
                $contentMan = "class='active'";
            break;
            case 'reorderinfo':
                $contentMan = "class='active'";
            break;
            case 'showpages':
                $contentMan = "class='active'";
            break;
            case 'showcats':
                $contentMan = "class='active'";
            break;
            case 'showinfobar':
                $contentMan = "class='active'";
            break;
            case 'fileman':
                $fileMan = "class='active'";
            break;
            case 'filemanview':
                $fileMan = "class='active'";
            break;
            case 'backuptool':
                $backupMan = "class='active'";
            break;
            case 'blademan':
                $bladeMan = "class='active'";
            break;
            case 'bladesystem':
                $bladeMan = "class='active'";
            break;
            case 'bladetheme':
                $bladeMan = "class='active'";
            break;
            case 'bladelanguage':
                $bladeMan = "class='active'";
            break;
            case 'bladeupgrade':
                $bladeMan = "class='active'";
            break;
            case 'bladeinstall':
                $bladeMan = "class='active'";
            break;
            case 'coresettings':
                $settingsMan = "class='active'";
            break;
            case 'settingsman':
                $settingsMan = "class='active'";
            break;
            case 'usermanager':
                $userMan = "class='active'";
            break;
            case 'userdata':
                $userMan = "class='active'";
            break;
            case 'admindata':
                $userMan = "class='active'";
            break;
            case 'sadmindata':
                $userMan = "class='active'";
            break;
            case 'version':
                $homePage = "class='active'";
            break;
            case 'helpinfo':
                $homePage = "class='active'";
            break;
            case 'maintenance':
            break;    
            default:
                $homePage = "class='active'";
                $foundMenu = false;
                BsocketB( 'admin-menu-active' , array( &$foundMenu ) );
                if ($foundMenu) {
                    $homePage = "";
                    $settingsMan = "class='active'";
                }
            }
        } else {
            $homePage = "class='active'";
        }
        $topNavHomeTag = '<img src="theme/images/home_sml.png" title="'.lt('Home').'" alt="'.lt('Home').'" />';
        $topNavConManTag = '<img src="theme/images/content_sml.png" title="'.lt('Content').'" alt="'.lt('Content').'" />';
        $topNavFileManTag = '<img src="theme/images/file_sml.png" title="'.lt('File Manager').'" alt="'.lt('File Manager').'" />';
        $topNavBackManTag = '<img src="theme/images/backup_sml.png" title="'.lt('Backup').'" alt="'.lt('Backup').'" />';
        $topNavSetManTag = '<img src="theme/images/settings_sml.png" title="'.lt('Settings').'" alt="'.lt('Settings').'" />';
        $topNavUserManTag = '<img src="theme/images/user_sml.png" title="'.lt('Users').'" alt="'.lt('Users').'" />';
        $topNavBlaManTag = '<img src="theme/images/blade_sml.png" title="'.lt('Blades').'" alt="'.lt('Blades').'" />';
        echo "<ul>
	        <li><a $homePage href='?'>$topNavHomeTag</a></li>
	        <li><a $contentMan href='?action=showcats'>$topNavConManTag</a></li>
	        <li><a $fileMan href='?action=fileman'>$topNavFileManTag</a></li>
                <li><a $backupMan href='?action=backuptool'>$topNavBackManTag</a></li>";
        echo "<li><a $userMan href='?action=usermanager'>$topNavUserManTag</a></li>";
        if($_SESSION['adminType'] != 'user') {
            echo "<li><a $settingsMan href='?action=coresettings'>$topNavSetManTag</a></li>";
            echo "<li><a $bladeMan href='?action=blademan'>$topNavBlaManTag</a></li>";
	}
        echo "</ul>";
    }
    // end ////////////////////////////////////////////////////////

    // new function to add links into sidenav for admin back end //
    function loadAdminSubLinks() {
        // setup sub link text for content manager //
        $createNew = lt('Create Content');
        $pages = lt('Pages');
        $XLinks = lt('External Links');
        $infoCont = lt('Infobar Items');
        $manage = lt('Manage Content');
        $linkItems = lt('Unpublished Content');
        $infobarLinks = lt('Infobar Items');
        $catLinks = lt('Published Content');
        $utilities = lt('Utilities');
        $versionCheck = lt('System Check');
        $helpInfo = lt('Help and Info');
        $fileTools = lt('File Tools');
        $viewFiles = lt('View Files');
        $backupTool = lt('Backup Tool');
        $settings = lt('General Settings');
        $coreSettings = lt('Core Settings');
        $userManager = lt('Change User Data');
        $userData = lt('User');
        $adminData = lt('Admin');
        $sAdminData = lt('Super Admin');
        $bladeSettings = lt('Blade Settings');
        $various = lt('Various Blades');
        $bladePacks = lt('Blade Pack Tools');
        $bladeClass = lt('Blade Pack Class');
        $installBlades = lt('Install Blade Packs');
        $classSystem = lt('System');
        $classTheme = lt('Theme');
        $classLang = lt('Language');
        $classUpgrade = lt('Upgrade');
        // end //

        $menuOutput = '';
        BsocketB( 'admin-new-sub-menu' , array( &$menuOutput ) );

        if( isset($_GET['action']) ) {
            switch ($_GET['action']) {
            case "addpage":
                // output to content manager page //
                echo "<h1>$manage</h1><ul><li><a href='?action=showcats'>$catLinks</a></li>
                      <li><a href='?action=showpages'>$linkItems</a></li>
                      <li><a href='?action=showinfobar'>$infobarLinks</a></li></ul>";
                echo "<h1>$createNew</h1><ul><li><a class='active' href='?action=addpage'>$pages</a></li>
                      <li><a href='?action=extlink'>$XLinks</a></li>
                      <li><a href='?action=addinfo'>$infoCont</a></li></ul>";
                // end //
            break;
            case "extlink":
                // output to content manager page //
                echo "<h1>$manage</h1><ul><li><a href='?action=showcats'>$catLinks</a></li>
                      <li><a href='?action=showpages'>$linkItems</a></li>
                      <li><a href='?action=showinfobar'>$infobarLinks</a></li></ul>";
                echo "<h1>$createNew</h1><ul><li><a href='?action=addpage'>$pages</a></li>
                      <li><a class='active' href='?action=extlink'>$XLinks</a></li>
                      <li><a href='?action=addinfo'>$infoCont</a></li></ul>";
                // end //
            break;
            case "addinfo":
                // output to content manager page //
                echo "<h1>$manage</h1><ul><li><a href='?action=showcats'>$catLinks</a></li>
                      <li><a href='?action=showpages'>$linkItems</a></li>
                      <li><a href='?action=showinfobar'>$infobarLinks</a></li></ul>";
                echo "<h1>$createNew</h1><ul><li><a href='?action=addpage'>$pages</a></li>
                      <li><a href='?action=extlink'>$XLinks</a></li>
                      <li><a class='active' href='?action=addinfo'>$infoCont</a></li></ul>";
                // end //
            break;
            case 'delete':
                // output to content manager page //
                echo "<h1>$manage</h1><ul><li><a href='?action=showcats'>$catLinks</a></li>
                      <a class='active' href='?action=showpages'>$linkItems</a></li>
                      <li><a href='?action=showinfobar'>$infobarLinks</a></li></ul>";
                echo "<h1>$createNew</h1><ul><li><a href='?action=addpage'>$pages</a></li>
                      <li><a href='?action=extlink'>$XLinks</a></li>
                      <li><a href='?action=addinfo'>$infoCont</a></li></ul>";
                // end //
            break;
            case 'edit':
                // output to content manager page //
                echo "<h1>$manage</h1><ul><li><a href='?action=showcats'>$catLinks</a></li>
                      <li><a href='?action=showpages'>$linkItems</a></li>
                      <li><a href='?action=showinfobar'>$infobarLinks</a></li></ul>";
                echo "<h1>$createNew</h1><ul><li><a href='?action=addpage'>$pages</a></li>
                      <li><a href='?action=extlink'>$XLinks</a></li>
                      <li><a href='?action=addinfo'>$infoCont</a></li></ul>";
                // end //
            break;
            case 'editinfo':
                // output to content manager page //
                echo "<h1>$manage</h1><ul><li><a href='?action=showcats'>$catLinks</a></li>
                      <li><a href='?action=showpages'>$linkItems</a></li>
                      <li><a href='?action=showinfobar'>$infobarLinks</a></li></ul>";
                echo "<h1>$createNew</h1><ul><li><a href='?action=addpage'>$pages</a></li>
                      <li><a href='?action=extlink'>$XLinks</a></li>
                      <li><a href='?action=addinfo'>$infoCont</a></li></ul>";
                // end //
            break;
            case 'editextlink':
                // output to content manager page //
                echo "<h1>$manage</h1><ul><li><a href='?action=showcats'>$catLinks</a></li>
                      <li><a href='?action=showpages'>$linkItems</a></li>
                      <li><a href='?action=showinfobar'>$infobarLinks</a></li></ul>";
                echo "<h1>$createNew</h1><ul><li><a href='?action=addpage'>$pages</a></li>
                      <li><a href='?action=extlink'>$XLinks</a></li>
                      <li><a href='?action=addinfo'>$infoCont</a></li></ul>";
                // end //
            break;
            case 'reordercat':
                // output to content manager page //
                echo "<h1>$manage</h1><ul><li><a class='active' href='?action=showcats'>$catLinks</a></li>
                      <li><a href='?action=showpages'>$linkItems</a></li>
                      <li><a href='?action=showinfobar'>$infobarLinks</a></li></ul>";
                echo "<h1>$createNew</h1><ul><li><a href='?action=addpage'>$pages</a></li>
                      <li><a href='?action=extlink'>$XLinks</a></li>
                      <li><a href='?action=addinfo'>$infoCont</a></li></ul>";
                // end //
            break;
            case 'reorderinfo':
                // output to content manager page //
                echo "<h1>$manage</h1><ul><li><a href='?action=showcats'>$catLinks</a></li>
                      <li><a href='?action=showpages'>$linkItems</a></li>
                      <li><a class='active' href='?action=showinfobar'>$infobarLinks</a></li></ul>";
                echo "<h1>$createNew</h1><ul><li><a href='?action=addpage'>$pages</a></li>
                      <li><a href='?action=extlink'>$XLinks</a></li>
                      <li><a href='?action=addinfo'>$infoCont</a></li></ul>";
                // end //
            break;
            case 'showpages':
                // output to content manager page //
                echo "<h1>$manage</h1><ul><li><a href='?action=showcats'>$catLinks</a></li>
                      <li><a class='active' href='?action=showpages'>$linkItems</a></li>
                      <li><a href='?action=showinfobar'>$infobarLinks</a></li></ul>";
                echo "<h1>$createNew</h1><ul><li><a href='?action=addpage'>$pages</a></li>
                      <li><a href='?action=extlink'>$XLinks</a></li>
                      <li><a href='?action=addinfo'>$infoCont</a></li></ul>";
                // end //
            break;
            case 'showcats':
                // output to content manager page //
                echo "<h1>$manage</h1><ul><li><a class='active' href='?action=showcats'>$catLinks</a></li>
                      <li><a href='?action=showpages'>$linkItems</a></li>
                      <li><a href='?action=showinfobar'>$infobarLinks</a></li></ul>";
                echo "<h1>$createNew</h1><ul><li><a href='?action=addpage'>$pages</a></li>
                      <li><a href='?action=extlink'>$XLinks</a></li>
                      <li><a href='?action=addinfo'>$infoCont</a></li></ul>";
                // end //
            break;
            case 'showinfobar':
                // output to content manager page //
                echo "<h1>$manage</h1><ul><li><a href='?action=showcats'>$catLinks</a></li>
                      <li><a href='?action=showpages'>$linkItems</a></li>
                      <li><a class='active' href='?action=showinfobar'>$infobarLinks</a></li></ul>";
                echo "<h1>$createNew</h1><ul><li><a href='?action=addpage'>$pages</a></li>
                      <li><a href='?action=extlink'>$XLinks</a></li>
                      <li><a href='?action=addinfo'>$infoCont</a></li></ul>";
                // end //
            break;
            case 'fileman':
                echo "<h1>$fileTools</h1><ul><li><a class='active' href='?action=filemanview'>$viewFiles</a></li></ul>";
            break;
            case 'filemanview':
                echo "<h1>$fileTools</h1><ul><li><a class='active' href='?action=filemanview'>$viewFiles</a></li></ul>";
            break;
            case 'backuptool':
                echo "<h1>$backupTool</h1><ul><li><a class='active' href='?action=backuptool'>$backupTool</a></li></ul>";
            break;
            case 'blademan':
                if($_SESSION['adminType'] != 'user') {
                    echo "<h1>$bladeClass</h1>";
                    echo "<ul><li><a class='active' href='?action=bladesystem'>$classSystem</a></li>";
                    echo "<li><a href='?action=bladetheme'>$classTheme</a></li>";
                    echo "<li><a href='?action=bladelanguage'>$classLang</a></li>";
                    echo "<li><a href='?action=bladeupgrade'>$classUpgrade</a></li></ul>";
                    echo "<h1>$bladePacks</h1>";
                    echo "<ul><li><a href='?action=bladeinstall'>$installBlades</a></li></ul>";
                }   
            break;
            case 'bladesystem':
                if($_SESSION['adminType'] != 'user') {
                    echo "<h1>$bladeClass</h1>";
                    echo "<ul><li><a class='active' class='active' href='?action=bladesystem'>$classSystem</a></li>";
                    echo "<li><a href='?action=bladetheme'>$classTheme</a></li>";
                    echo "<li><a href='?action=bladelanguage'>$classLang</a></li>";
                    echo "<li><a href='?action=bladeupgrade'>$classUpgrade</a></li></ul>";
                    echo "<h1>$bladePacks</h1>";
                    echo "<ul><li><a href='?action=bladeinstall'>$installBlades</a></li></ul>";
                }   
            break;            
            case 'bladetheme':
                if($_SESSION['adminType'] != 'user') {
                    echo "<h1>$bladeClass</h1>";
                    echo "<ul><li><a href='?action=bladesystem'>$classSystem</a></li>";
                    echo "<li><a class='active' href='?action=bladetheme'>$classTheme</a></li>";
                    echo "<li><a href='?action=bladelanguage'>$classLang</a></li>";
                    echo "<li><a href='?action=bladeupgrade'>$classUpgrade</a></li></ul>";
                    echo "<h1>$bladePacks</h1>";
                    echo "<ul><li><a href='?action=bladeinstall'>$installBlades</a></li></ul>";
                }
            break;            
            case 'bladelanguage':
                if($_SESSION['adminType'] != 'user') {
                    echo "<h1>$bladeClass</h1>";
                    echo "<ul><li><a href='?action=bladesystem'>$classSystem</a></li>";
                    echo "<li><a href='?action=bladetheme'>$classTheme</a></li>";
                    echo "<li><a class='active' href='?action=bladelanguage'>$classLang</a></li>";
                    echo "<li><a href='?action=bladeupgrade'>$classUpgrade</a></li></ul>";
                    echo "<h1>$bladePacks</h1>";
                    echo "<ul><li><a href='?action=bladeinstall'>$installBlades</a></li></ul>";
                }   
            break;
            case 'bladeupgrade':
                if($_SESSION['adminType'] != 'user') {
                    echo "<h1>$bladeClass</h1>";
                    echo "<ul><li><a href='?action=bladesystem'>$classSystem</a></li>";
                    echo "<li><a href='?action=bladetheme'>$classTheme</a></li>";
                    echo "<li><a href='?action=bladelanguage'>$classLang</a></li>";
                    echo "<li><a class='active' href='?action=bladeupgrade'>$classUpgrade</a></li></ul>";
                    echo "<h1>$bladePacks</h1>";
                    echo "<ul><li><a href='?action=bladeinstall'>$installBlades</a></li></ul>";
                }   
            break;
            case 'bladeinstall':
                if($_SESSION['adminType'] != 'user') {
                    echo "<h1>$bladeClass</h1>";
                    echo "<ul><li><a href='?action=bladesystem'>$classSystem</a></li>";
                    echo "<li><a href='?action=bladetheme'>$classTheme</a></li>";
                    echo "<li><a href='?action=bladelanguage'>$classLang</a></li>";
                    echo "<li><a href='?action=bladeupgrade'>$classUpgrade</a></li></ul>";
                    echo "<h1>$bladePacks</h1>";
                    echo "<ul><li><a class='active' href='?action=bladeinstall'>$installBlades</a></li></ul>";
                } 
            break;
            case 'coresettings':
                if($_SESSION['adminType'] != 'user') {
                    echo "<h1>$settings</h1><ul><li><a class='active' href='?action=coresettings'>$coreSettings</a></li></ul>
                      <h1>$bladeSettings</h1><ul><li><a href='?action=settingsman'>$various</a></li>$menuOutput</ul>";
                } 
            break;
            case 'settingsman':
                if($_SESSION['adminType'] != 'user') {
                echo "<h1>$settings</h1><ul><li><a href='?action=coresettings'>$coreSettings</a></li></ul>
                      <h1>$bladeSettings</h1><ul><li><a class='active' href='?action=settingsman'>$various</a></li>$menuOutput</ul>";
                }
            break;
            case 'usermanager':
                echo "<h1>$userManager</h1><ul>";
                if($_SESSION['adminType'] != 'admin') {
                    echo "<li><a class='active' href='?action=userdata'>$userData</a></li>";
                }
                if($_SESSION['adminType'] != 'user') {
                    echo "<li><a href='?action=admindata'>$adminData</a></li>";
                }
                if($_SESSION['adminType'] == 'sadmin') {
                    echo "<li><a href='?action=sadmindata'>$sAdminData</a></li>";
                }
                echo "</ul>";
            break;
            case 'userdata':
                echo "<h1>$userManager</h1><ul>";
                if($_SESSION['adminType'] != 'admin') {
                    echo "<li><a class='active' href='?action=userdata'>$userData</a></li>";
                }
                if($_SESSION['adminType'] != 'user') {
                    echo "<li><a href='?action=admindata'>$adminData</a></li>";
                }
                if($_SESSION['adminType'] == 'sadmin') {
                    echo "<li><a href='?action=sadmindata'>$sAdminData</a></li>";
                }
                echo "</ul>";
            break;
            case 'admindata':
                echo "<h1>$userManager</h1><ul>";
                if($_SESSION['adminType'] != 'admin') {
                    echo "<li><a href='?action=userdata'>$userData</a></li>";
                }
                if($_SESSION['adminType'] != 'user') {
                    echo "<li><a class='active' href='?action=admindata'>$adminData</a></li>";
                }
                if($_SESSION['adminType'] == 'sadmin') {
                    echo "<li><a href='?action=sadmindata'>$sAdminData</a></li>";
                }
                echo "</ul>";
            break;
            case 'sadmindata':
                echo "<h1>$userManager</h1><ul>";
                if($_SESSION['adminType'] != 'admin') {
                    echo "<li><a href='?action=userdata'>$userData</a></li>";
                }
                if($_SESSION['adminType'] != 'user') {
                    echo "<li><a href='?action=admindata'>$adminData</a></li>";
                }
                if($_SESSION['adminType'] == 'sadmin') {
                    echo "<li><a class='active' href='?action=sadmindata'>$sAdminData</a></li>";
                }
                echo "</ul>";
            break;
            case 'version':
                // ouput to home page //
                echo "<h1>$utilities</h1><ul><li><a class='active' href='?'>$versionCheck</a></li>
                      <li><a href='?action=helpinfo'>$helpInfo</a></li></ul>";
                // end //
            break;
            case 'helpinfo':
                // ouput to home page //
                echo "<h1>$utilities</h1><ul><li><a href='?'>$versionCheck</a></li>
                      <li><a class='active' href='?action=helpinfo'>$helpInfo</a></li></ul>";
                // end //
            break;
            case 'maintenance':
            break;
            default:
                // output to home page //
                $defaultOutput = "<h1>$utilities</h1><ul><li><a class='active' href='?'>$versionCheck</a></li><li><a href='?action=helpinfo'>$helpInfo</a></li></ul>";
                $foundMenu = false;
                BsocketB( 'admin-menu-active' , array( &$foundMenu ) );
                if (!$foundMenu) {
                    echo $defaultOutput;
                } else {
                    echo "<h1>$settings</h1><ul><li><a href='?action=coresettings'>$coreSettings</a></li></ul>
                          <h1>$bladeSettings</h1><ul><li><a href='?action=settingsman'>$various</a></li>$menuOutput</ul>";
                }
            }
        } else {
            // output to home page //
            echo "<h1>$utilities</h1><ul><li><a class='active' href='?'>$versionCheck</a></li>
                  <li><a href='?action=helpinfo'>$helpInfo</a></li></ul>";
            // end //
        }
    }
    // end //////////////////////////////////////////

    ///////////////////////////////////////////////////////////
    // ############## End of General functions ############# //
    ///////////////////////////////////////////////////////////

    ///////////////////////////////////////////////////////////
    // ################ Home Page functions ################ //
    ///////////////////////////////////////////////////////////

    // version check utility //
    function versionCheck() {
        echo "<h1>".lt('System Check')."</h1>";
        print "<div class='systemmessage'><h3>".lt('System Messages')."</h3>";
        $sysMessUrl = "http://www.razorcms.co.uk/version/sysmess.txt";
        $messageData = fetchRemoteFile($sysMessUrl);
        if (!$messageData) {
            unset($messageData);
        } else {
            $messageData = preg_replace('/[^a-zA-Z0-9\.\s\,\-\_\<\>\/]/', '', $messageData);
        }
        if ( isset($messageData)) { 
            print "<p class='bluetext'>".$messageData."</p>";
        } else {
            $messageData = lt('connection failure, please check back later').'...';
            print "<p class='bluetext'>".$messageData."</p>";
        }
        print "</div>";
        $currentVersion = RAZOR_CURRENT_VERSION;
        $versionUrl = "http://www.razorcms.co.uk/version/latestversion.txt";
        $versionData = fetchRemoteFile($versionUrl);
        if (!$versionData) {
            unset($versionData);
        } else {
            $versionData = preg_replace('/[^a-zA-Z0-9\.\s\,\-\_]/', '', $versionData);
        }
   //////////////////////////////////////////////
   // commented out for non production version //
   //////////////////////////////////////////////
   
        if ( isset($versionData)) { 
            if ($versionData == $currentVersion) { 
                print "<div class='safe'><h3>".lt('Version Check')."</h3>";
                print "<ul><li>".lt('Current installed version')." $currentVersion</li>";
                print "<li>".lt('Latest available version')." "; 
                print "<span class='greentext'>".$versionData."</span>";
            } else if ($versionData == '') {
                print "<div class='normal'><h3>".lt('Version Check')."</h3>";
                print "<ul><li>".lt('Current installed version')." $currentVersion</li>";
                print "<li>".lt('Latest available version')." "; 
                $versionData = lt('connection failure');
                print "<span class='bluetext'>".$versionData."</span>";
            } else {
                print "<div class='unsafe'><h3>".lt('Version Check')."</h3>";
                print "<ul><li>".lt('Current installed version')." $currentVersion</li>";
                print "<li>".lt('Latest available version')." : "; 
                print "<span class='redtext'>".$versionData."</span>";
            }
        } else {
            print "<div class='normal'><h3>".lt('Version Check')."</h3>";
            print "<ul><li>".lt('Current installed version')." $currentVersion</li>";
            print "<li>".lt('Latest available version')." ";  
            $versionData = lt('connection failure');
            print "<span class='bluetext'>".$versionData."</span>";
        }
        print '</li></ul>';
        if ($versionData == $currentVersion) {
            print '<p class="greentext">'.lt('Your installation is up to date, please check back regularly for updates').'</p>';
        } else if ( $versionData == lt('connection failure') ) {
            print '<p class="bluetext">'.lt('There appears to be an error connecting to the remote version check file, please check back later').'</p>';
        } else {
            print '<p class="redtext">'.lt('Your installation is out of date, please visit').' <a href="http://www.razorcms.co.uk" target="_blank">official razorCMS website</a></p>';
        }
        print "</div>";
    
    //////////////////////////////////////////////
    // commented out for non production version //
    //////////////////////////////////////////////
    
    ////////////////////////////////////
    // add for non production version //
    ////////////////////////////////////
    /*
        if ( isset($versionData)) { 
            if ($versionData == $currentVersion) { 
                print "<div class='safe'><h3>".lt('Version Check')."</h3>";
                print "<ul><li>".lt('Current installed version')." $currentVersion</li>";
            } else if ($versionData == '') {
                print "<div class='normal'><h3>".lt('Version Check')."</h3>";
                print "<ul><li>".lt('Current installed version')." $currentVersion</li>";
            } else {
                print "<div class='safe'><h3>".lt('Version Check')."</h3>";
                print "<ul><li>".lt('Current installed version')." $currentVersion</li>";
            }
        } else {
            print "<div class='normal'><h3>".lt('Version Check')."</h3>";
            print "<ul><li>".lt('Current installed version')." $currentVersion</li>";
        }
        print '</ul><br />';
        print "</div>";
    */
    ////////////////////////////////////
    // add for non production version //
    ////////////////////////////////////
        if ( ini_get('register_globals') ) {
            echo "<div class='unsafe'><h3>".lt('Environment Check')."</h3>";
            echo '<p>'.lt('Register globals is set to').' <span class="redtext">'.lt('on').'</span></p>';
            echo '<p><span class="redtext">'.lt('Environment is not secure, register globals is switched on, this is a severe security hazard, please consult your host supplier for information on switching it off').'</span></p></div>';
        } else {
            echo "<div class='safe'><h3>".lt('Environment Check')."</h3>";
            echo '<p>'.lt('Register globals is set to').' <span class="greentext">'.lt('off').'</span></p>';
            echo '<p>'.lt('Environment is OK, register globals is switched off').'</p></div>';
        }
        securityManager();
    }
    // end /////////////////////////////////////////////////////////

    // help and info output //
    function helpAndInfo() {
        echo "<h1>".lt('Help and Info')."</h1>";
        echo '<div class="contentwh"><h3>'.lt('User Manual').'</h3><p>'.lt('For detailed information on using razorCMS, please consult the user manual, which can be found in the documents section of the').' <a href="http://www.razorcms.co.uk" target="_blank">'.lt('official razorCMS website').'</a>.</p>';
        echo '<h3>'.lt('Developement').'</h3><p>'.lt('If you wish to develope blade pack add ons, write guides or even create new themes for razorCMS, why not visit the documents section of the').' <a href="http://www.razorcms.co.uk" target="_blank">'.lt('official razorCMS website').'</a> '.lt('for more help and advice on where to start. Here you will be able to find development guides, sample code and lots lots more').'.</p>';
        echo '<h3>'.lt('Extra Functionality').'</h3><p>'.lt('If you require more functionality than the core install can offer, why not visit the').' <a href="http://www.razorcms.co.uk" target="_blank">'.lt('official razorCMS website').'</a> '.lt('for a whole array of blade packs from functional, in-page and theme blade packs').'</p>';
        echo '<h3>'.lt('Need Help').'</h3><p>'.lt('If your having trouble using the system, you have two places to solve your problems, try reading the guides and manuals listed on the').' <a href="http://www.razorcms.co.uk" target="_blank">'.lt('official razorCMS website').'</a>, '.lt('failing this ask a question or look for answers in our dedicated support forum').'.</p>';
        echo '<h3>'.lt('Links').'</h3><p><ul><li>'.lt('razorCMS core author').' <a href="mailto:smiffy6969@razorcms.co.uk">smiffy6969</a></li><li>'.lt('razorCMS support forum').' <a href="http://www.razorcms.co.uk/support">www.razorcms.co.uk/support</a></li><li>'.lt('official razorCMS website').' <a href="http://www.razorcms.co.uk">www.razorcms.co.uk</a></li><li>Morgan Integrated Systems Limited <a href="http://www.mis-limited.com">www.mis-limited.com</a></li></ul><br /></p></div>';
    }
    // end //////////////////////////

    // display security check //
    function securityManager() {
        // set directories and files //
        $installRootRead = getSystemRoot(RAZOR_ADMIN_FILENAME);
        $installRootPath = '';

        // change permissions //
        if (isset($_GET['permissions'])) {
            changePermissions($installRootPath);
        }

        // read all file and dir permissions //
        $permSafe = readPermissions($installRootRead);
        

        // display permissions //
        displayPerm($permSafe);
    }
    // end //////////////////////////

    // change setting //
    function changePermissions($installRootPath) {
        $permissionsTemp = $_GET['permissions'];
        if ($permissionsTemp == 'set') {
            chmodAllDir($installRootPath, 'safe');
            chmodAllFile($installRootPath, 'safe');
        } elseif ($permissionsTemp == 'unset') {
            if($_SESSION['adminType'] == 'user') {
	        return;
            }
            chmodAllDir($installRootPath, 'unsafe');
            chmodAllFile($installRootPath, 'unsafe');
        }
    }
    // end /////////////

    // display outcome of permissions check //
    function displayPerm($perms) {
        if(findServerOS() == 'LINUX') {
            if (count($perms) == 0) {
                echo "<div class='safe'><h3>".lt('Security Check')."</h3>";
                echo '<p>'.lt('Security - safe').'</p>';
                echo '<p>'.lt('All files are currently safe').'</p>';
                echo "<p><a href='?action=version&permissions=set'>".lt('Make razorCMS files safe')."</a></p>";
                if($_SESSION['adminType'] != 'user' && $_SESSION['adminType'] != 'admin') {
		    echo "<p><a href='?action=version&permissions=unset' onclick='return confirm(\"".lt('Are you sure you want to make all razorCMS files unsafe, THIS IS A SECURITY RISK')."?\");'>".lt('Make razorCMS files unsafe')."</a></p>";
                }
                echo "</div>";
            } else {
                echo "<div class='unsafe'><h3>".lt('Security Check')."</h3>";
                echo '<p>'.lt('Security - WARNING NOT SAFE').'</p>';
                echo '<p>'.lt('A directory or file is currently unsafe, please make all razorCMS files safe.').'</p>';
                echo '<p>'.lt('PLEASE NOTE This tool is unable to set your install root safe, this must be done manually using a 3rd party application. razorCMS has no permission or control to alter your install root.').'</p>';
                echo "<p><a href='?action=version&permissions=set'>".lt('Make razorCMS files safe')."</a></p>";
                if($_SESSION['adminType'] != 'user' && $_SESSION['adminType'] != 'admin') {
		    echo "<p><a href='?action=version&permissions=unset' onclick='return confirm(\"".lt('Are you sure you want to make all razorCMS files unsafe, THIS IS A SECURITY RISK')."?\");'>".lt('Make razorCMS files unsafe')."</a></p>";
                }
                echo '<p>'.lt('The following directories and files are unsafe').'<ul>';
                ksort($perms);
                foreach($perms as $path=>$perm) {
                    echo '<li>'.substr($path, 3).' - '.$perm.'</li>';
                }
                echo '</ul></p></div>';
            }
        } else {
            echo "<div class='normal'><h3>".lt('Security Check')."</h3>";
            echo '<p>'.lt('Security - UNKNOWN').'</p>';
            echo '<p>'.lt('You are using a non linux server').'</p>';
            echo '<p>'.lt('razorCMS cannot determine file permissions, please manage file permissions manually').'</p></div>';
        }
    }
    // end ///////////////////////////////////

    // read apache owned file permissions //
    function readPermissions($readPath) {
        $dirPermArray = array();
        $filePermArray = array();
        $allowedDirArray = array('0400','0600','0700','0444','0644','0744','0455','0655','0755');
        $allowedFileArray = array('0400','0600','0444','0644');
        $permSafety = array();

        // read in permissions //
        $rootPerm = fileperms($readPath);
        $dirPermArray = readAllDirPerm($readPath);
        $filePermArray = readAllFilePerm($readPath);

        // check every permission //
        if(!in_array(substr(decoct($rootPerm), 1, 4), $allowedDirArray)) {
            $permSafety['   Install root unsafe'] = substr(decoct($rootPerm), 1, 4);
        }
        foreach($dirPermArray as $dir=>$perm) {
            if(!in_array(substr(decoct($perm), 1, 4), $allowedDirArray)) {
                // check to see if it is a dir in install root
                $checkPath = explode('/',$dir);
                if($checkPath[0].'/'.$checkPath[1] == $dir){
                    // if is dir in install root check if owned by razor
                    $statA = stat('../index.php');
                    $statB = stat($dir);
                    if($statA['uid'] == $statB['uid']){
                        $permSafety[$dir] = substr(decoct($perm), 1, 4);
                    }
                } else {
                    $permSafety[$dir] = substr(decoct($perm), 1, 4);
                }
            }
        }
        foreach($filePermArray as $file=>$perm) {
            if(!in_array(substr(decoct($perm), 2, 4), $allowedFileArray)) {
                // check to see if it is a dir in install root
                $checkPath = explode('/',$file);
                if($checkPath[0].'/'.$checkPath[1] == $file){
                    // if is dir in install root check if owned by razor
                    $statA = stat('../index.php');
                    $statB = stat($file);
                    if($statA['uid'] == $statB['uid']){
                        $permSafety[$file] = substr(decoct($perm), 2, 4);
                    }
                } else {
                    $permSafety[$file] = substr(decoct($perm), 2, 4);
                }
            }
        }
        return $permSafety;
    }
    // end /////////////////////////////////

    ///////////////////////////////////////////////////////////
    // ############# End of Home Page functions ############ //
    ///////////////////////////////////////////////////////////

    ///////////////////////////////////////////////////////////
    // ############# Content Manager functions ############# //
    ///////////////////////////////////////////////////////////

    // slab creation function // 
    function addPage() {
        global $razorArray;
        $addtErrMsg = array();
        $catlist = $razorArray['links_cats'];
       	$newp = new SLAB();
        $newp->newSlabInit();
        $tempCatArray = array();
        $title = '';
        $ptitle = '';
        if(isset($_POST['title'])){
            $title = stripslashes($_POST['title']);
        }
        if(isset($_POST['ptitle'])){
            $ptitle = stripslashes($_POST['ptitle']);
        }
        foreach( $catlist as $tempCat=>$tempcc ) {
            if( isset( $_POST[ 'check_'.$tempCat ] ) ) {
                $tempCatArray[$tempCat] = $tempCat;
            }
        }

        // get form signature //
        $random = false;
        if(isset($_POST['random'])){
            $randomV = htmlspecialchars(stripslashes($_POST['random']), ENT_QUOTES);
            $randomVC = htmlspecialchars(stripslashes($_SESSION['random']), ENT_QUOTES);
            if($randomV == $randomVC){
                $random = true;
            }
        }

        // save data if form button clicked //
        if( isset( $_POST['save'] ) && $random ) {
            $isFormSubmitted = true;
            $content = stripslashes($_POST['content']);
            // fix for XSS Attacks to strip script tags only //
            $content = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $content);
            // fix end, also need to swap post var for content cleaned as below //
            $newp->editTitle( $title );
            $newp->editPTitle( $ptitle );
            if ( $_GET['action'] != 'addinfo' ) {
                $theme = '';
                if(isset($_POST['theme'])){
                    $theme = stripslashes($_POST['theme']);
                }
                $newp->editTheme($theme);
            }
            $slab = $newp->slab;
            $newp->catReset();
            foreach( $catlist as $cat=>$cc ) {
                if( isset( $_POST[ 'check_'.$cat ] ) ) {
                    $newp->addToCat( $cat );
                }
            }
            if( $title != '' and $content !='' ) {
                $newPageFile = findPageFile($slab, 'admin');
                if( !in_array($slab, $razorArray['slabs']) ) {
                    BsocketB('admin-xpage-info-input', array($slab));
                    $newp->commitChanges();
                    $fileAsID = array_search($slab, $razorArray['slabs']);
                    if( put2file( RAZOR_PAGES_DIR.findPageFile($fileAsID), $content ) ) {
                        saveRazorArray();
                    }
                    if ( $_GET['action'] == 'addinfo' ) {
                        $m = lt('The info was created successfully'). '<br />';
                    } else {
                        $m = lt('The page was created successfully'). '<br />';
                    }
                    $m.= lt('Title')." <b>".$razorArray['titles'][$fileAsID].'</b><br />';
                    $m.= lt('File Created')." <b>".$fileAsID.".".RAZOR_DEFAULT_FILE_EXT."</b><br />";
                    MsgBox( $m, 'greenbox' );
                    return $slab;
    		} else {
                    $addtErrMsg[] = lt('Save Failed');
                    if( in_array($slab, $razorArray['slabs']) ) {
                        $addtErrMsg[] = lt('Content with similar Title already exists');
                    }
                }
            } else {
                $addtErrMsg[] = lt('One of the input fields is empty, Please check your input');
            }
        }
        // end //
        
        // output any messages created during save data //
        if( count($addtErrMsg)!=0 and isset($title) ) {
            $em = '';
            foreach( $addtErrMsg as $msg ) {
                $em .= "<p>$msg</p>";
            }
            MsgBox( lt('Errors Occured').$em, 'redbox' );
        }
        // end //
        
        // set up data for input form //
        $submitButton = lt('Add Page');
        $filteredCats = array();
        if ( $_GET['action'] == 'addinfo' ) {
            $chkboxs = "<input type='hidden' name='check_".$razorArray['settings']['info-bar-cat']."' value='".$razorArray['settings']['info-bar-cat']."'><label for='check_".$razorArray['settings']['info-bar-cat']."'>".$razorArray['settings']['info-bar-cat']."</label>";
            $addnewpageLabel = lt('Infobar Content Details');
            $formAction = "?action=addinfo";
        } else {
            foreach ( $razorArray['links_cats'] as $linksCats=>$contents ) {
                if ( $linksCats != $razorArray['settings']['info-bar-cat'] ) {
                    $filteredCats[$linksCats] = $linksCats;
                }
            }
            $addnewpageLabel = lt('Page Details');
            $chkboxs = checkBoxList( $filteredCats, $tempCatArray );
            $formAction = "?action=addpage";
        }
        // create theme drop down for form
        $theme_default = lt('Default');
        $theme_one = lt('Theme One');
        $theme_two = lt('Theme Two');
        $theme_three = lt('Theme Three');
        $themeList = array('theme-default'=>$theme_default,'theme-one'=>$theme_one,'theme-two'=>$theme_two,'theme-three'=>$theme_three);
	$themeSelect = pagesList( 'theme',$themeList, 'theme-default' );
        // create labels
        $menuTitleLabel = lt('Menu Title');
        $pageTitleLabel = lt('Page Title').' ('.lt('optional').')';
        $themeSelectLabel = lt('Select Theme');
        $catsLabel = lt('Categories');
        $contentLabel = lt('Content');
        $contentManager = lt('Content Manager');
        // end //
        
        // display theme chooser if page (not info) //
        $chooseTheme = '';
        $longPageTitle = '';
        if ( $_GET['action'] == 'addpage' ) {
            $chooseTheme=<<<TET
                          <tr>
	                      <td>$themeSelectLabel</td>
	                      <td>$themeSelect</td>
                          </tr>
TET;
            $longPageTitle=<<<TET
                          <tr>
	    		      <td>$pageTitleLabel</td>
	    		      <td><input class='w300' type='text' value='$ptitle' name='ptitle'></td>
                          </tr>
TET;
        }
        // end //
        
        // form sockets for adding output or functions to form and add label for correct page//
        $extraInfo;
        $addFunction;
        if ( $_GET['action'] == 'addinfo' ) {
            BsocketB('admin-xinfo-info-output',array(&$extraInfo));
            BsocketB('admin-add-info-function',array(&$addFunction,&$content));
            $newItem = lt('Create New Infobar Content');
        } else {
            BsocketB('admin-xpage-info-output',array(&$extraInfo));
            BsocketB('admin-add-page-function',array(&$addFunction,&$content));
            $newItem = lt('Create New Page');
        }
        // end //

        // fix for & and text area bug //
        $content =  str_replace("&", "&amp;", $content);
        $content =  str_replace("</textarea>", "&lt;/textarea&gt;", $content);
        // end fix //

        // generate random signature for form //        
        $random = rand();     
        $_SESSION['random'] = $random;   

        // display form for data input //
        $te[1]=<<<TET
            <h1>$newItem</h1>
            <div class='contentwh'>
            <form action='$formAction' method=post class='pagemod_form'>
                <input type='hidden' name='random' value='$random'>
                <h3>$addnewpageLabel</h3>
                $extraInfo
                <table class='tableNewItem'>
                    <tr class='tableFooter'><td class='twenty'></td><td class='auto'></td></tr>
                    <tr>
                        <td>$menuTitleLabel</td>
                        <td><input type='text' value='$title' name='title'></td>
                    </tr>
                    $longPageTitle
                    $chooseTheme
                    <tr>
                        <td>$catsLabel</td>
                        <td>$chkboxs</td>
                    </tr>
                    <tr class='tableFooter'><td></td><td></td></tr>
                </table>
                <h3>$contentLabel</h3>
                <table class='tableNewItem'>
                    <tr class='tableFooter'><td></td></tr>
		$addFunction
                    <tr><td class='tableEditBox'>
TET;
        $te[2] = "<textarea name='content' id='editbox' class='editbox'>$content</textarea>";
        $te[3]=<<<TET
                    </td>
                    </tr>
                    <tr class='tableFooter'><td></td></tr>
                </table>
                <input id='button' type='submit' value='$submitButton' name='save'>
            </form>
            </div>
TET;
        $te[4] = $content;
        echo $te[1];
        BsocketB( 'editor' , array( &$te ) );
        echo $te[2];
        echo $te[3];
        // end //
        
        return false;
    }
    // end /////////////////////////

    // New external link creation function // 
    function addExtLink() {
        global $razorArray;
        $externalLink = '';
        $extLinkFlag = false;
        $addtErrMsg = array();
        $catlist = $razorArray['links_cats'];
        $newp = new SLAB();
        $newp->newSlabInit();
        $tempCatArray = array();
        $title = '';
        if(isset($_POST['title'])){
            $title = stripslashes($_POST['title']);
        }
        foreach( $catlist as $tempCat=>$tempcc ) {
            if( isset( $_POST[ 'check_'.$tempCat ] ) ) {
                $tempCatArray[$tempCat] = $tempCat;
            }
        }

        // get form signature //
        $random = false;
        if(isset($_POST['random'])){
            $randomV = htmlspecialchars(stripslashes($_POST['random']), ENT_QUOTES);
            $randomVC = htmlspecialchars(stripslashes($_SESSION['random']), ENT_QUOTES);
            if($randomV == $randomVC){
                $random = true;
            }
        }
        
        // save external link data from form if save button clicked // 
        if( isset( $_POST['save'] ) && $random) {
            $isFormSubmitted = true;
            $externalLink = stripslashes($_POST['externallink']);
            $newp->editTitle( $title );
            $slab = $newp->slab;
            $newp->catReset();
            foreach( $catlist as $cat=>$cc ) {
                if( isset( $_POST[ 'check_'.$cat ] ) ) {
                    $newp->addToCat( $cat );
                }
            }
            if ($title != '' and $externalLink !='') {   
                if( !in_array($slab, $razorArray['slabs']) ) {
                    BsocketB('admin-xlink-info-input', array($slab));
                    $newp->commitChanges();
                    if ( isset($_POST['new-win']) ) {
                        if ( $_POST['new-win'] == 'show' ) {
                            $razorArray['ext_link_win'][$slab] = true;
                        }
                    }
                    $razorArray['ext_links'][$slab] = $externalLink;
                    saveRazorArray();
                    $m = lt('The link was created successfully'). '<br />';
                    $m.= lt('Title')." <b>".$title.'</b><br />';
                    $m.= lt('XLink Created')." <b>".$externalLink."</b><br />";
                    MsgBox( $m, 'greenbox' );
                    return $slab;
                } else {
                    $addtErrMsg[] = lt('Save Failed');               
                    if ( in_array($slab,$razorArray['slabs'] )) {
                        $addtErrMsg[] = lt('Content with similar Title already exists');
                    }
                }
            } else {
                $addtErrMsg[] = lt('One of the input fields is empty, Please check your input');
            }
        }
        // end //
        
        // output any messages collected //
        if( count($addtErrMsg)!=0 and isset($title) ) {
            $em = '';
            foreach( $addtErrMsg as $msg ) {
                $em .= "<p>$msg</p>";
            }
            MsgBox( lt('Errors Occured').$em, 'redbox' );
        }
        // end //
        
        // set up form data //
        $formAction = "?action=extlink";
        $submitButton = lt('Add Link');
        $pageTitleLabel = lt('External Link Title');
        $urlLable = lt('URL');
        $addnewpageLabel = lt('External Link Details');
        $externalLinkTitle = lt('External Link');
        $filteredCats = array();
        foreach ( $razorArray['links_cats'] as $linksCats=>$contents ) {
            if ( $linksCats != $razorArray['settings']['info-bar-cat'] ) {
                $filteredCats[$linksCats] = $linksCats;
            }
        }
        $chkboxs = checkBoxList( $filteredCats, $tempCatArray );
        $newWinLabel = lt('Display in New Window');
        $newWinChkbox = "<input type='checkbox' name='new-win' value='show'>";
        $catsLabel = lt('Categories');
        $contentManager = lt('Content Manager');
        $createNewLink = lt('Create New External Link');
        // end //
        
        // sockets for extra output and function output //
        $extraInfo;
        BsocketB('admin-xlink-info-output',array(&$extraInfo));
        $addFunction;
        BsocketB('admin-add-link-function',array(&$addFunction,&$content));
        // end //

        // generate random signature for form //
        $random = rand();     
        $_SESSION['random'] = $random; 
        
        // ouput form for data input //
        $te[1]=<<<TET
            <h1>$createNewLink</h1>
                <div class='contentwh'>
            <form action='$formAction' method=post class='pagemod_form'>
                <input type='hidden' name='random' value='$random'>
                <h3>$addnewpageLabel</h3>
                $extraInfo
                <table class='tableNewItem'>
                    <tr class='tableFooter'><td class='twenty'></td><td class='auto'></td></tr>
                    <tr>
                        <td>$pageTitleLabel</td>
                        <td><input type='text' value='$title' name='title'></td>
                    </tr>
                    <tr>
                        <td>$catsLabel</td>
                        <td>$chkboxs</td>
                    </tr>
                    <tr>
                        <td>$newWinLabel</td>
                        <td>$newWinChkbox</td>
                    </tr>
                    <tr class='tableFooter'><td></td><td></td></tr>
                </table>
                <h3>$externalLinkTitle</h3>
                <table class='tableNewItem'>
                    <tr class='tableFooter'><td></td><td></td></tr>
                    $addFunction
TET;
        $te[2] = "<tr><td class='twenty'>URL</td><td>http:// <input type='externallink' value='$externalLink' name='externallink'></td></tr>";
        $te[3]=<<<TET
                    <tr class='tableFooter'><td></td><td></td></tr>
                </table>
                <input id='button' type='submit' value='$submitButton' name='save'>
            </form>
            </div>
TET;
        echo $te[1];
        echo $te[2];
        echo $te[3];
        // end //
        
        return false;
    }
    // end /////////////////////////

    // edit slab and or detials // 
    function performEdit($slabIn = '') {
        global $editErrMsg, $razorArray;
        if($slabIn == '') {
            $slab = $_GET['slab'];
        } else {
            $slab = $slabIn;
        }
        if (!in_array($slab, $razorArray['slabs'])){
            MsgBox(lt('Invalid name'), 'redbox');
            return;
        }
        $fileAsID = array_search($slab, $razorArray['slabs']);
        $addtErrMsg = array();
        $catlist = $razorArray['links_cats'];

        // get form signature //
        $random = false;
        if(isset($_POST['random'])){
            $randomV = htmlspecialchars(stripslashes($_POST['random']), ENT_QUOTES);
            $randomVC = htmlspecialchars(stripslashes($_SESSION['random']), ENT_QUOTES);
            if($randomV == $randomVC){
                $random = true;
            }
        }

        // process form data //
        if( isset( $_POST['save'] ) && $slabIn == '' && $random) {
            $newp = new SLAB();
            $newp->loadSlab($slab);
            $oldSlab = $slab;
            $oldTitle = $newp->title;
            $title = '';
            if(isset($_POST['title'])){
                $title = stripslashes($_POST['title']);
            }
            if(isset($_POST['ptitle'])){
                $ptitle = stripslashes($_POST['ptitle']);
            } else {
                $ptitle = '';
            }
            $newSlab = cleanSlabTitle( $_POST['title'],$newp->slabId );
            if( $title=='' ) {
                $addtErrMsg[]= lt('Title cannot be empty');
            } else {
                $fileName = findPageFile($slab);
                $newFileName = $newName = findPageFile( $newSlab );
                $newp->catReset();
                foreach( $catlist as $cat=>$cc ) {
                    if( isset( $_POST[ 'check_'.$cat ] ) ) {
                        $newp->addToCat( $cat );
                    }
                }
                //edit theme//
                if(isset($_POST['theme'])){
                    $newp->editTheme($_POST['theme']);
                }
                $renameErr = false;
                // Fix for slab title renaming error (thanks to sguy) //
                if( $newSlab!=$oldSlab || $title!=$oldTitle ) {
                    if ( $title!=$oldTitle && in_array($title,$razorArray['titles']) ) {
                        $addtErrMsg[] = lt('Cannot Rename Title, Content with similar title already exists');
                        $renameErr = true;
                    } else if( $newSlab!=$oldSlab && in_array($newSlab,$razorArray['slabs']) ) {
                        $addtErrMsg[] = lt('Cannot Rename Title, content with similar title already exists');
                        $renameErr = true;
                    } else {
                        if ( $razorArray['homepage'] == $slab ) {  //if was homepage change to new name
                            $razorArray['homepage'] = $newSlab;
                        }
                        $slab = $newSlab;
                    }
                    if( !$renameErr ) {
                        $newp->editTitle( $title );
                    }
                    $fileName = $newName;
                }
                if(isset($ptitle)){
                    $newp->editPTitle( $ptitle );
                }
                // end fix //
                // fix for XSS Attacks to strip script tags only //
                $contentClean = '';
                $contentClean = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $_POST['content']);
                // fix end, also need to swap post var for content cleaned as below //
                if( !put2file( RAZOR_PAGES_DIR.findPageFile($fileAsID), stripslashes( $contentClean ) ) ) {
                    $addtErrMsg[]=lt('Error writing to file');
                }
            }
        }
        // end //

        // ouput error message if failed or save changes if passed //
        if( count($addtErrMsg) != 0 ) {
            $em = '';
            foreach( $addtErrMsg as $msg ) {
                $em .= "<p>$msg</p>";
            }
            MsgBox( lt('Errors Occured').$em, 'redbox' );
        } elseif( isset($newp) && count($addtErrMsg) == 0 ) { 
            BsocketB('admin-xpage-info-input', array($slab));
            $newp->commitChanges();
            saveRazorArray();
            if ( $_GET['action'] == 'editinfo' ) {
                $m = lt('Info Edited Successfully').'<br />';
            } else {
                $m = lt('Page Edited Successfully').'<br />';
            }
            $m.= lt('Title')." <b>".$razorArray['titles'][$fileAsID].'</b><br />';
            $m.= lt('File Edited')." <b>".$fileAsID.".".RAZOR_DEFAULT_FILE_EXT."</b><br />";
            MsgBox( $m, 'greenbox' );
        }
        // end //
        
        // set up form data //
        $ep = new SLAB();
        $ep->loadSlab($slab);
        $fileName = findPageFile( $fileAsID, 'admin' );
        $fileContent = file_exists( $fileName )? file_get_contents( $fileName ) : lt('no content yet');
        $submitButton = lt('Save Content');
        $editLabel = lt('Edit Content');
        $catLabel = lt('Categories');
        $contentLabel = lt('Content');
        $title = $ep->title;
        $ptitle = $ep->ptitle;
        $themeSelectLabel = lt('Select Theme');
        $contentManager = lt('Content Manager');
        $addnewpageLabel = lt('Details');
        $menuTitleLabel = lt('Menu Title');
        $pageTitleLabel = lt('Page Title').' ('.lt('optional').')';
        // end //
        
        // page and infobar filter to change form data //
        $filteredCats = array();
        if ( $_GET['action'] == 'editinfo' ) {
            $chkboxs = "<input type='hidden' name='check_".$razorArray['settings']['info-bar-cat']."' value='".$razorArray['settings']['info-bar-cat']."'><label for='check_".$razorArray['settings']['info-bar-cat']."'>".$razorArray['settings']['info-bar-cat']."</label>";
            $addnewpageLabel = lt('Infobar Content Details');
            $formAction = "?action=editinfo&slab=$slab";
            $chooseTheme = '';
        } else {
            foreach ( $razorArray['links_cats'] as $linksCats=>$contents ) {
                if ( $linksCats != $razorArray['settings']['info-bar-cat'] ) {
                    $filteredCats[$linksCats] = $linksCats;
                }
            }
            $addnewpageLabel = lt('Page Details');
            $chkboxs = checkBoxList( $filteredCats, $ep->cats );
            $formAction = "?action=edit&slab=$slab";
            // create theme drop down for form
            if(isset($razorArray['themes'][$slab]) && $razorArray['themes'][$slab] != ''){
                $choice = $razorArray['themes'][$slab];
            } else {
                $choice = 'theme-default';
            }
            $theme_default = lt('Default');
            $theme_one = lt('Theme One');
            $theme_two = lt('Theme Two');
            $theme_three = lt('Theme Three');
            $themeList = array('theme-default'=>$theme_default,'theme-one'=>$theme_one,'theme-two'=>$theme_two,'theme-three'=>$theme_three);
	    $themeSelect = pagesList( 'theme',$themeList, $choice );
            $chooseTheme=<<<TET
                          <tr>
	                      <td>$themeSelectLabel</td>
	                      <td>$themeSelect</td>
                          </tr>
TET;
            $longPageTitle=<<<TET
                          <tr>
	    		      <td>$pageTitleLabel</td>
	    		      <td><input class='w300' type='text' value='$ptitle' name='ptitle'></td>
                          </tr>
TET;
        }
        // end //
        
        // sockets for extra output and add function //
        $extraInfo;
        $addFunction;
        if ( $_GET['action'] == 'editinfo' ) {
            BsocketB('admin-xinfo-info-output-ed',array(&$extraInfo,$slab));
            BsocketB('admin-add-info-function',array(&$addFunction,&$fileContent));
            $newItem = lt('Edit Infobar Content');
        } else {
            BsocketB('admin-xpage-info-output-ed',array(&$extraInfo,$slab));
            BsocketB('admin-add-page-function',array(&$addFunction,&$fileContent));
            $newItem = lt('Edit Page');
        }
        // end //

        // fix for & and text area bug //
        $fileContent =  str_replace("&", "&amp;", $fileContent);
        $fileContent =  str_replace("</textarea>", "&lt;/textarea&gt;", $fileContent);
        // end fix //

        // generate random signature for form //
        $random = rand();     
        $_SESSION['random'] = $random;   

        // output form for data input //
        $te[1]=<<<TET
            <h1>$newItem $ep->title</h1>
            <div class='contentwh'>
            <form action='$formAction' method=post class='pagemod_form'>
            <input type='hidden' name='random' value='$random'>
            <h3>$addnewpageLabel</h3>
            $extraInfo
            <table class='tableEditItem'>
                <tr class='tableFooter'><td class='twenty'></td><td class='auto'></td></tr>
                <tr>
                    <td>$menuTitleLabel</td>
                    <td><input type='text' value='$title' name='title'></td>
                </tr>
                $longPageTitle
                $chooseTheme
                <tr>
                    <td>$catLabel</td>
                    <td>$chkboxs</td>
                </tr>
                <tr class='tableFooter'><td></td><td></td></tr>
            </table>
            <h3>$contentLabel</h3>
            <table class='tableEditItem'>
	    $addFunction
                <tr class='tableFooter'><td></td><td></td></tr>
                <tr><td class='tableEditBox'>
TET;
        $te[2] = "<textarea name='content' rows=20 cols=70 id='editbox' class='editbox'>$fileContent</textarea>";
        $te[3]=<<<TET
                    </td>
                </tr>
                <tr class='tableFooter'><td></td><td></td></tr>
            </table>
            <input id='button' type='submit' value='$submitButton' name='save'>
        </form>
        </div>
TET;
        $te[4] = $fileContent;
        echo $te[1];
        BsocketB( 'editor' , array( &$te ) );
        echo $te[2];
        echo $te[3];
        // end //

    }
    // end ///////////////////////

    // edit external link detials // 
    function performEditLink($xLinkIn = '') {
        global $editErrMsg, $razorArray;
        if($xLinkIn == '') {
            $slab = $_GET['slab'];
        } else {
            $slab = $xLinkIn;
        }
        if (!in_array($slab, $razorArray['slabs'])){
            MsgBox(lt('Invalid name'), 'redbox');
            return;
        }
        $addtErrMsg = array();
        $catlist = $razorArray['links_cats'];

        // get form signature //
        $random = false;
        if(isset($_POST['random'])){
            $randomV = htmlspecialchars(stripslashes($_POST['random']), ENT_QUOTES);
            $randomVC = htmlspecialchars(stripslashes($_SESSION['random']), ENT_QUOTES);
            if($randomV == $randomVC){
                $random = true;
            }
        }

        // process form data if clicked //
        if( isset( $_POST['save'] ) && $xLinkIn == '' && $random) {
            $newp = new SLAB();
            $newp->loadSlab($slab);
            $oldTitle = $newp->title;
            $oldSlab = $slab;
            $newSlab = cleanSlabTitle( $_POST['title'],$newp->slabId);
            $externalLink = stripslashes($_POST['externallink']);
            $title = '';
            if(isset($_POST['title'])){
                $title = stripslashes($_POST['title']);
            }
            if( $title=='' ) {
                $addtErrMsg[]= lt('Title cannot be empty');
            } else {
                $newp->catReset();
                foreach( $catlist as $cat=>$cc ) {
                    if( isset( $_POST[ 'check_'.$cat ] ) ) {
                        $newp->addToCat( $cat );
                    }
                }
                $renameErr = false;
                // Fix for slab title renaming error (thanks to sguy) //
                if( $newSlab!=$oldSlab || $title!=$oldTitle ) {
                    if ( $title!=$oldTitle && in_array($title,$razorArray['titles']) ) {
                        $addtErrMsg[] = lt('Cannot Rename Title, content with similar title already exists');
                        $renameErr = true;
                    } else if( $newSlab!=$oldSlab && in_array($newSlab,$razorArray['slabs']) ) {
                        $addtErrMsg[] = lt('Cannot Rename Title, content with similar title already exists');
                        $renameErr = true;
                    } else {
                            $slab = $newSlab;
                    }
                    if( !$renameErr ) {
                        $newp->editTitle( $title );
                        unset($razorArray['ext_links'][$oldSlab]);
                    }
                }
                // end fix //
                if ( !$renameErr ) {
                    $razorArray['ext_links'][$slab] = $externalLink;
                    if ( isset($_POST['new-win']) ) {
                        if ( $_POST['new-win'] == 'show' ) {
                            $razorArray['ext_link_win'][$slab] = true;
                        } else {
                            if ( isset($razorArray['ext_link_win'][$slab] )) {
                                unset($razorArray['ext_link_win'][$slab]);
                            }
                        }
                    } else {
                        if ( isset($razorArray['ext_link_win'][$slab] )) {
                            unset($razorArray['ext_link_win'][$slab]);
                        }
                    }
                }
            }
        }
        // end //

        // output message if failed or save changes if worked //
        if( count($addtErrMsg) != 0) {
            $em = '';
            foreach( $addtErrMsg as $msg ) {
                $em .= "<p>$msg</p>";
            }
            MsgBox( lt('Errors Occured').$em, 'redbox' );
        } elseif ( isset($newp) && count($addtErrMsg) == 0 ) { 
            BsocketB('admin-xpage-info-input', array($slab));
            $newp->commitChanges();
            saveRazorArray();
            $m = lt('Link Edited Successfully').'<br />';
            $m.= lt('Title')." <b>".$title.'</b><br />';
            $m.= lt('XLink Edited')." <b>".$externalLink."</b><br />";
            MsgBox( $m, 'greenbox' );
        }
        // end //

        // filter categories //
        $filteredCats = array();
        foreach ( $razorArray['links_cats'] as $linksCats=>$contents ) {
            if ( $linksCats != $razorArray['settings']['info-bar-cat'] ) {
                $filteredCats[$linksCats] = $linksCats;
            }
        }
        // end //

        // set up form data //
        $ep = new SLAB();
        $ep->loadSlab($slab);
        $externalLink = $razorArray['ext_links'][$slab];
        $title = $ep->title;
        $formAction = "?action=editextlink&slab=$slab";
        $submitButton = lt('Edit Link');
        $pageTitleLabel = lt('External Link Title');
        $externalLinkTitle = lt('External Link');
        $urlLable = lt('URL');
        if ( isset($razorArray['ext_link_win']) ) {
            if ( in_array($slab,$razorArray['ext_link_win']) ) {
                if ( $razorArray['ext_link_win'][$slab] ) {
                    $nwchecked = 'checked';
                }
            }
        }
        $newWinLabel = lt('Display in New Window');
        $newWinChkbox = "<input type='checkbox' name='new-win' value='show' $nwchecked>";
        $addnewpageLabel = lt('External Link Details');
        $chkboxs = checkBoxList( $filteredCats, $ep->cats );
        $catsLabel = lt('Categories');
        $contentManager = lt('Content Manager');
        $editContent = lt('Edit External Link');
        // end //        

        // sockets for extra ouput and add function //
        $extraInfo;
        BsocketB('admin-xlink-info-output',array(&$extraInfo));
        $addFunction;
        BsocketB('admin-add-link-function',array(&$addFunction,&$content));
        // end //

        // generate random signature for form //
        $random = rand();     
        $_SESSION['random'] = $random;  

        // output form for data input //Search...
        $te[1]=<<<TET
            <h1>$editContent $ep->title</h1>
            <div class='contentwh'>
            <form action='$formAction' method=post class='pagemod_form'>
            <input type='hidden' name='random' value='$random'>
                <h3>$addnewpageLabel</h3>
                $extraInfo
                <table class='tableEditItem'>
                    <tr class='tableFooter'><td class='twenty'></td><td class='auto'></td></tr>
                    <tr>
                        <td>$pageTitleLabel</td>
                        <td><input type='text' value='$title' name='title'></td>
                    </tr>
                    <tr>
                        <td>$catsLabel</td>
                        <td>$chkboxs</td>
                    </tr>
                    <tr>
                        <td>$newWinLabel</td>
                        <td>$newWinChkbox</td>
                    </tr>
                    <tr class='tableFooter'><td></td><td></td></tr>
                </table>
                <h3>$externalLinkTitle</h3>
                <table class='tableEditItem'>
                    <tr class='tableFooter'><td class='twenty'></td><td></td></tr>
        $addFunction
TET;
        $te[2] = "</tr><td>$urlLable</td><td>http:// <input type='externallink' value='$externalLink' name='externallink'></td></tr>";
        $te[3]=<<<TET
                    <tr class='tableFooter'><td></td><td></td></tr>
                </table>
                <input id='button' type='submit' value='$submitButton' name='save'>
            </form>
            </div>
TET;
        $te[4] = $content;
        echo $te[1];
        echo $te[2];
        echo $te[3];
        // end //

    }      
    // end ///////////////////////

    // content manager manage content //
    function manageContent() {
        global $razorArray;
        $titles = $razorArray['titles'];

        echo "<h1>".lt('Unpublished Content')."</h1>";

        // check if infobar is set //
        if ( isset($razorArray['settings']['info-bar-cat']) ) {
            if ( isset($razorArray['links_cats'][$razorArray['settings']['info-bar-cat']]) ) {
                $infobarFlag = true;
            }
        } else {
            $infobarFlag = false;
        }
        // end //

        // output pages only //
        echo "<div class='contentwh'><h3>".lt('Pages')."</h3>";
        echo "<table class='tableLinkItems'>";
        echo "<tr class='tableTitle'><th class='five'>".lt('ID')."</th><th class='five'>".lt('T')."</th><th class='auto'>".lt('Page')."</th><th class='twenty'>".lt('Options')."</th></tr>";
                
        foreach($razorArray['slabs'] as $slabID=>$slabName) {
            $published = false;
            foreach($razorArray['links_cats'] as $catArray){
                if(in_array($slabID, $catArray)) {
                    $published = true;
                }
            }
            if ($infobarFlag) {
                if ( !in_array( $slabName,array_keys($razorArray['ext_links'])) and !$published) {
                    if(!isset($razorArray['themes'][$slabName])){
		        $themeSelected = '';
		    } else {
		        $themeSelected = $razorArray['themes'][$slabName];
		    }
		    switch ($themeSelected) {
		        case 'theme-one':
		    	    $themeKey = '1';
		        break;
		        case 'theme-two':
		            $themeKey = '2';
		        break;
		        case 'theme-tree':
		            $themeKey = '3';
		        break;
		        default:
		    	    $themeKey = 'D';
                    }
                    $deleteConfirmMsg = lt('Are you sure you want to delete this page, Remember Once you delete you cannot retreive again, Proceed').'?';
                    $delTxt = lt('Delete');
                    $editTxt = lt('Edit');
                    $s = "<tr>
                            <td>$slabID </td>
                            <td>$themeKey </td>
                            <td>".$titles[$slabID]."</td>
                            <td>
                              <a href='?action=edit&slab=".$slabName."' title='$editTxt'><img class='edit' src='theme/images/edit.gif' alt='$editTxt' /></a> 
                              <a href='?action=delete&slab=".$slabName."' title='$delTxt' onclick='return confirm(\"$deleteConfirmMsg\");'><img class='delete' src='theme/images/trash.gif' alt='$delTxt' /></a>
                            </td>
                          </tr>";
                    echo $s;
                }
            }
        }
        echo "<tr class='tableFooter'><th></th><th></th><th></th><th></th></tr>";
        echo "</table>";
        // end //

        // output external links only //
        echo "<h3>".lt('External Links')."</h3><table class='tableLinkItems'>";
        echo "<tr class='tableTitle'><th class='ten'>".lt('ID')."</th><th class='auto'>".lt('X-Link')."</th><th class='twenty'>".lt('Options')."</th></tr>";
        foreach($razorArray['slabs'] as $slabID=>$slabName) {
            $published = false;
            foreach($razorArray['links_cats'] as $catArray){
                if(in_array($slabID, $catArray)) {
                    $published = true;
                }
            }
            if (  in_array( $slabName,array_keys($razorArray['ext_links'])) and !$published) {
                $deleteConfirmMsg = lt('Are you sure you want to delete this external link, Remember Once you delete you cannot retreive again, Proceed').'?';
                $delTxt = lt('Delete');
                $editTxt = lt('Edit');
                $s = "<tr>
                   <td>$slabID </td>
                   <td class='edittablerow'>".$titles[$slabID]."</td>
                   <td>
                    <a href='?action=editextlink&slab=".$slabName."' title='$editTxt'><img class='edit' src='theme/images/edit.gif' alt='$editTxt' /></a> 
                    <a href='?action=delete&slab=".$slabName."' title='$delTxt' onclick='return confirm(\"$deleteConfirmMsg\");'><img class='delete' src='theme/images/trash.gif' alt='$delTxt' /></a>
                   </td>
                  </tr>";
                echo $s;
            }
        }
        echo "<tr class='tableFooter'><th></th><th></th><th></th></tr>";
        echo "</table>";
        // end //
        echo "</div>";
    }
    // end ///////////////////////


    // content manager manage categories //
    function manageCats() {
        global $razorArray;
        $cdt = $razorArray['links_cats'];
        $slabs = $razorArray['slabs'];
        $titles = $razorArray['titles'];
        $musthaveCats = explode( ',', $razorArray['settings']['must-have-cats'] );
        $selectedCat = 1;
        $toggStat = 'false';
        
        // get form signature //
        $random = false;
        if(isset($_POST['random'])){
            $randomV = htmlspecialchars(stripslashes($_POST['random']), ENT_QUOTES);
            $randomVC = htmlspecialchars(stripslashes($_SESSION['random']), ENT_QUOTES);
            if($randomV == $randomVC){
                $random = true;
            }
        }

        // add new category process //
        if( isset($_GET['addcat']) && $random) {
            $newCatName = strtolower( stripslashes($_POST['catname']) );
            $newCatName = cleanSlabTitle( $newCatName );
            if( in_array( $newCatName, array_keys($cdt) ) ) {
                $msg = sprintf( lt('Cannot add duplicate catagory, name already in use'), "<b>$newCatName</b>" );
                MsgBox( $msg, 'redbox' );
            } else {
                $cdt[$newCatName] = array();
                $msg = sprintf( lt('Category added successfully'), "<b>$newCatName</b>" );
                MsgBox( $msg,'greenbox' );
                $razorArray['links_cats'] = $cdt;
                saveRazorArray();
            }
        }
        // end //

        // add new sub category process //
        if( isset($_GET['addsubcat']) && $random ) {
            $newSubCatName = strtolower( stripslashes($_POST['subcatname']) );
            $newSubCatName = cleanSlabTitle( $newSubCatName );
            $underCat = $_POST['undercat'];
            if( in_array( $newSubCatName, array_keys($cdt) ) ) {
                $msg = lt('Cannot add duplicate catagory')." <b>$newSubCatName</b> ".lt('name already in use');
                MsgBox( $msg, 'redbox' );
            } else {
                $cdt[$newSubCatName] = array();
                $keyForTitle = array_search($underCat, $razorArray['slabs']);
                $pageTitle = $razorArray['titles'][$keyForTitle];
                $msg = lt('Sub category')." <b>$newSubCatName</b> ".lt('added successfully under the page')." <b>$pageTitle</b>";
                MsgBox( $msg,'greenbox' );
                $razorArray['links_cats'] = $cdt;
                $razorArray['sub_cat_flag'][$newSubCatName] = $underCat;
                saveRazorArray();
            }
        }
        // end //

        // remove category process //
        if( isset($_GET['removecat']) ) {
            $catN = strtolower( stripslashes($_GET['removecat']) );
            $catN = cleanSlabTitle( $catN );
            if( !in_array($catN,array_keys($cdt) ) ) {
                MsgBox( lt('Category to be deleted does not exist'), 'redbox' );
            } else {
                if( in_array( $catN, $musthaveCats ) ) {
                    MsgBox( "<b>$catN</b> ".lt('Cannot be deleted'), 'redbox' );
                } else {
                    unset( $cdt[ $catN ] );
                    $msg = sprintf( lt('Category removed successfully'), "<b>$catN</b>" );
                    MsgBox( $msg,'greenbox' );
                    if ( $catN == $razorArray['settings']['info-bar-cat'] ) {
                        $razorArray['settings']['info-bar-cat'] = '';
                    }
                    if ( isset($razorArray['sub_cat_flag'][$catN]) ){
                        unset( $razorArray['sub_cat_flag'][$catN] );
                    }
                    $razorArray['links_cats'] = $cdt;
                    saveRazorArray();
                }
            }           
        }
        // end //
        
        // add page to a category process //
        if( isset($_GET['addtocat']) && $_GET['addtocat'] && $random) {
            if ( isset($_POST['pageassign']) && isset($_POST['catassign'])) {
                $pageSelected = $_POST['pageassign'];
                $catSelected = $_POST['catassign'];
                $pageID = array_search($pageSelected, $razorArray['slabs']);

                // check if category already present and update //
                if( in_array($pageID, $razorArray['links_cats'][$catSelected]) ) {
                    MsgBox( "<b>".$razorArray['titles'][$pageID]."</b> ".lt('already present in category')." <b>$catSelected</b>", 'redbox' );
                } else {
                    MsgBox( "<b>".$razorArray['titles'][$pageID]."</b> ".lt('has been added to category')." <b>$catSelected</b>", 'greenbox' );
                    array_push($razorArray['links_cats'][$catSelected], $pageID);
                    saveRazorArray();
                }                
            }
        }
        // end //

        // rename category process //
        if( isset($_GET['rencat']) && $_GET['rencat']  && $random) {
            if ( isset($_POST['renamecatnew']) && isset($_POST['renamecatnew'])) {
                $oldCatName = $_POST['catrename'];
                $newCatName = $_POST['renamecatnew'];
                $newCatName = cleanSlabTitle($newCatName);
                                
                if( in_array( $newCatName, array_keys($cdt) ) ) {
                    $msg = lt('Cannot rename catagory, name already in use');
                    MsgBox( $msg, 'redbox' );
                } elseif($newCatName == '') {
                    $msg = lt('Cannot rename catagory, new name empty');
                    MsgBox( $msg, 'redbox' );
                } else {
                    $newCatArray = array();
                    foreach($cdt as $catName=>$catData){
                        if($catName == $oldCatName){
                            $newCatArray[$newCatName] = $catData;
                        } else {
                            $newCatArray[$catName] = $catData;
                        }
                    }
                    $cdt = $newCatArray;
                    $razorArray['links_cats'] = $cdt;
                    saveRazorArray();
                    $msg = sprintf( lt('Category renamed successfully'), "<b>$newCatName</b>" );
                    MsgBox( $msg,'greenbox' );
                }               
            }
        }
        // end //

        // remove page from a category process //
        if( isset($_GET['unpub']) && $_GET['unpub']) {
            if ( isset($_GET['slabID']) && isset($_GET['catname'])) {
                $catSelected = strtolower( stripslashes($_GET['catname']) );
                $catSelected = cleanSlabTitle( $catSelected );
                $slabID = $_GET['slabID'];
                if(is_numeric($slabID)) {
                    // check slab is listed in the cat and update //  
                    if( !in_array($slabID, $razorArray['links_cats'][$catSelected]) ) {
                        MsgBox( "<b>".$razorArray['titles'][$slabID]."</b> ".lt('not present in category')." <b>$catSelected</b>", 'redbox' );
                    } else {
                        MsgBox( "<b>".$razorArray['titles'][$slabID]."</b> ".lt('has been removed from category')." <b>$catSelected</b>", 'greenbox' );
                        $newCatArray = array();
                        foreach($razorArray['links_cats'][$catSelected] as $catPosition=>$catSlabID) {
                            if($catSlabID != $slabID) {
                                array_push($newCatArray,$catSlabID);
                            }
                        }
                        $razorArray['links_cats'][$catSelected] = $newCatArray;
                        saveRazorArray();
                    }
                } else {
                    MsgBox( lt('not present in category'), 'redbox' );
                } 
            }       
        }
        // end //

        $cdt = $razorArray['links_cats'];
                
        // set up data to display on content manager page //
        $catSelectList = array();
        foreach( $cdt as $cN=>$cSC ) {
            $catSelectList[$cN] = $cN;
        }
        $noInfoCatList = array();
        foreach( $cdt as $cN=>$cSC ) {
            if ($cN != $razorArray['settings']['info-bar-cat']) {
                $noInfoCatList[$cN] = $cN;
            }
        }
        $catSelectMenu = pagesList( 'catassign',$noInfoCatList );
        $catRenameList = pagesList( 'catrename',$noInfoCatList );
        $renameCat = lt('Rename category');
        $pagesAndOpt = lt('Category Tools');
        $manageCategories = lt('Published Content');
        $addNewCat = lt('Add new category');
        $addNewSubCat = lt('Add new sub category');
        $addUnder = lt('under');
        $addLabel = lt('Submit');
        $addPage = lt('Add page');
        $addToCat = lt('to category');
        $toNewLabel = lt('to new name');
        $removeConfirmMsg = lt('Are you sure you want to remove this item from the category').'?';
        $delTxt = lt('Delete');
        $remTxt = lt('Remove');
        $editTxt = lt('Edit');

        // output category options //
        $pageSelectList = array();
        foreach($razorArray['slabs'] as $slabID=>$slabName) {
            if (  !in_array( $slabID,$razorArray['links_cats'][$razorArray['settings']['info-bar-cat']]) and !in_array( $slabName,array_keys($razorArray['ext_links']))) {
                $pageSelectList[$slabName] = $titles[$slabID];
            }
        }
        $pageSelectList2 = array();
        foreach($razorArray['slabs'] as $slabID=>$slabName) {
            if (  !in_array( $slabID,$razorArray['links_cats'][$razorArray['settings']['info-bar-cat']])) {
                $pageSelectList2[$slabName] = $titles[$slabID];
            }
        }

        // generate random signature for form //
        $random = rand();     
        $_SESSION['random'] = $random;  

        $pageListMenu = pagesList( 'undercat',$pageSelectList );
        $pageListMenu2 = pagesList( 'pageassign',$pageSelectList2 );
        echo "<h1>$manageCategories</h1>";
        echo "<div class='contentwh'>";
        echo "<h3>$pagesAndOpt</h3>";
        echo "<table class='tableNewCats'><tr class='tableFooter'><td class='twenty'></td><td class='auto'></td><td class='ten'></td></tr>
                <tr>
                    <form action='?action=showcats&addcat=true' method='post'>
                        <input type='hidden' name='random' value='$random'>
                        <td>$addNewCat</td><td><input type='text' name='catname'></td><td><input id='button' type='submit' value='$addLabel'></td>
                    </form>
                    </tr>
                    <tr class='tableFooter'><td></td><td></td><td></td></tr>
                    <form action='?action=showcats&addsubcat=true' method='post'>
                        <input type='hidden' name='random' value='$random'>
                        <tr><td>$addNewSubCat</td><td><input type='text' name='subcatname'></td><td></td></tr>
                        <tr><td>$addUnder</td><td>$pageListMenu</td><td><input id='button' type='submit' value='$addLabel'></td></tr>
                    </form>
                    <tr class='tableFooter'><td></td><td></td><td></td></tr>
                    <form action='?action=showcats&rencat=true' method='post'>
                        <input type='hidden' name='random' value='$random'>
                        <tr><td>$renameCat</td><td>$catRenameList</td><td></td></tr>
                        <tr><td>$toNewLabel</td><td><input type='text' name='renamecatnew'></td><td><input id='button' type='submit' value='$addLabel'></td></tr>
                    </form>
                    <tr class='tableFooter'><td></td><td></td><td></td></tr>
                    <form action='?action=showcats&addtocat=true' method='post'>
                        <input type='hidden' name='random' value='$random'>
                        <tr><td>$addPage</td><td>$pageListMenu2</td><td></td></tr>
                        <tr><td>$addToCat</td><td>$catSelectMenu</td><td><input id='button' type='submit' value='$addLabel'></td></tr>
                    </form>
                <tr class='tableFooter'><td></td><td></td><td></td></tr>
            </table></div>";
        // end //
        
        $v=0;
        echo "<div class='contentwh'>";

        // output navigation category listings //
        $deleteConfirmMsg = lt('Are you sure you want to delete this category, Remember Once you delete you cannot retreive again, Proceed').'?';
        $removeTag = lt('remove');
        foreach($cdt as $catname=>$catslabs) {
            if ($catname != $razorArray['settings']['info-bar-cat']) {
                $v++;
                $slabids = array_values($catslabs);
                $n = count($slabids)-1;
                if( !in_array( $catname, $musthaveCats )) {
                    $removeOpt = "<a href='?action=showcats&removecat=$catname' title='Delete' onclick='return confirm(\"$deleteConfirmMsg\");'><img class='delete' src='theme/images/trash.gif' alt='$delTxt' /></a>";
                } else {
                    $removeOpt = '';
                }
                $parentCatName = '';
                if ( isset($razorArray['sub_cat_flag'][$catname]) ) {
                    $keyForTitle = '';
                    $keyForTitle = array_search($razorArray['sub_cat_flag'][$catname], $razorArray['slabs']);
                    $parentCatName.= $razorArray['titles'][$keyForTitle];
                    $parentCatName.= ' &gt ';
                }
                echo "<h3>$parentCatName $catname $removeOpt</h3>";
                echo "<table class='tableCats'><tr class='tableTitle'><th class='five'>#</th><th class='five'>".lt('T')."</th><th class='auto'>".lt('Content')."</th><th class='twenty'>".lt('Options')."</th></tr>";
                if( count($slabids) == 0 ) {
                    echo "<tr><td></td><td></td><td>".lt('No items are added under this category')."</td><td></td></tr>";
                    echo "<tr class='tableFooter'><th></th><th></th><th></th><th></th></tr></table>";
                    continue;
                }
                $upTag = lt('up');
                $downTag = lt('down');
                foreach($slabids as $pos=>$ids) {
                    if (  !in_array( $ids,$razorArray['links_cats'][$razorArray['settings']['info-bar-cat']]) and !in_array( $slabs[$ids],array_keys($razorArray['ext_links']))) {
                        $editItem = "<a href='?action=edit&slab=".$slabs[$ids]."' title='$editTxt'><img class='edit' src='theme/images/edit.gif' alt='$editTxt' /></a>";
                        $removeItem = "<a href='?action=showcats&unpub=true&slabID=$ids&catname=$catname' title='$remTxt' onclick='return confirm(\"$removeConfirmMsg\");'><img class='delete' src='theme/images/eject.gif' alt='$remTxt' /></a>";
                        if(!isset($razorArray['themes'][$slabs[$ids]])){
                            $themeSelected = '';
                        } else {
                            $themeSelected = $razorArray['themes'][$slabs[$ids]];
                        }
                        switch ($themeSelected) {
			    case 'theme-one':
			        $themeKey = '1';
			    break;
			    case 'theme-two':
			        $themeKey = '2';
			    break;
			    case 'theme-three':
			        $themeKey = '3';
			    break;
			    default:
			        $themeKey = 'D';
                        }
                    } elseif (  in_array( $slabs[$ids],array_keys($razorArray['ext_links'])) ) {
                        $editItem = "<a href='?action=editextlink&slab=".$slabs[$ids]."' title='Edit'><img class='edit' src='theme/images/edit.gif' alt='$editTxt' /></a>";
                        $removeItem = "<a href='?action=showcats&unpub=true&slabID=$ids&catname=$catname' title='Unpublish' onclick='return confirm(\"$removeConfirmMsg\");'><img class='delete' src='theme/images/eject.gif' alt='$remTxt' /></a>";
                        $themeKey = '';
                    }
                    $lineNum = $pos + 1;
                    $ul = makeLink("?action=reordercat&cat=$catname&param=".$pos.','.($pos-1),'<img class="updown" src="theme/images/arrow1_n.gif" title="'.$upTag.'" alt="'.$upTag.'" />');
                    $dl = makeLink("?action=reordercat&cat=$catname&param=".$pos.','.($pos+1),'<img class="updown" src="theme/images/arrow1_s.gif" title="'.$downTag.'" alt="'.$downTag.'" />');
                    if( $pos==0 ) {
                        $ul='<img src="theme/images/arrow1_n.gif" title="'.$upTag.'" alt="'.$upTag.'" />';
                    }
                    if( $pos==$n ) {
                        $dl='<img src="theme/images/arrow1_s.gif" title="'.$downTag.'" alt="'.$downTag.'" />';
                    }
                    $s = "<tr>
                            <td>$lineNum</td>
                            <td>$themeKey</td>
                            <td>".$titles[$ids]."</td>
                            <td>$ul $dl $editItem $removeItem</td>
                          </tr>";
                    echo $s;
                }
                echo "<tr class='tableFooter'><th></th><th></th><th></th><th></th></tr></table>";
            }
        }
        echo "</div>";
        // end //
    }
    // end ///////////////////////

    // content manager manage infobar content //
    function manageInfobar() {
        global $razorArray;
        $slabs = $razorArray['slabs'];
        $titles = $razorArray['titles'];

        // allocate infobar content to page process //
        if( isset( $_GET['addInfo'] ) ) {
            $infoData = array();
            $infoKey = array();
            $infoValue = array();
            if ( isset($_POST['infoBarDes']) ) {
                foreach ($_POST['infoBarDes'] as $key=>$infoMixedData) {
                    $infoData = explode( ',', $infoMixedData );
                    array_push($infoKey,$infoData[0]);
                    array_push($infoValue,$infoData[1]); 
                }
            }
            $globalArray = array();
            if(isset($_POST['global'])){
                $globalArray = $_POST['global'];
            }
            $razorArray['info-bar-global'] = $globalArray;
            $razorArray['info-bar-key'] = $infoKey;
            $razorArray['info-bar-value'] = $infoValue;
            saveRazorArray();
            $messageOut = lt('Infobar content allocation successfull');
            MsgBox( $messageOut, 'greenbox' );
        }
        // end //

        $infoBarTitle = lt('Infobar Items');
        echo "<h1>$infoBarTitle</h1>";

        // check if infobar is set //
        if ( isset($razorArray['settings']['info-bar-cat']) ) {
            if ( isset($razorArray['links_cats'][$razorArray['settings']['info-bar-cat']]) ) {
                $infobarFlag = true;
            }
        } else {
            $infobarFlag = false;
        }
        // end //

        // output infobar listing //
        if ( $infobarFlag ) {
            echo "<div class='contentwh'>";
            $catname = $razorArray['settings']['info-bar-cat'];
            echo "<h3>$catname</h3>";
            echo "<form action='?action=showinfobar&addInfo=true' method='post'>";
            echo "<table class='tableInfoAllocation'>";
            echo "<tr class='tableTitle'><th class='five'></th><th class='five'>".lt('G')."</th><th class='twenty'>".lt('Info')."</th><th class='auto'>".lt('Show on')."</th><th class='twenty'>".lt('Options')."</th></tr>";
            $delTxt = lt('Delete');
            $editTxt = lt('Edit');
            $upTag = lt('up');
            $downTag = lt('down');
            $deleteConfirmMsg = lt('Are you sure you want to delete this info, Remember Once you delete you cannot retreive again, Proceed').'?';
            $slabids = array();
            if ( $infobarFlag ) {
                $slabids = array_values($razorArray['links_cats'][$razorArray['settings']['info-bar-cat']]);
                $n = count($slabids)-1;
            }
            if( count($slabids) == 0 ) {
                echo "<tr><td></td><td></td><td>".lt('No info items exist')."</td><td></td><td></td></tr><tr class='tableFooter'><th></th><th></th><th></th><th></th><th></th></tr></form></table></div>";
            } else {
                $position = 1;
                foreach($slabids as $pos=>$ids) {
                    $editItem = "<a href='?action=editinfo&slab=".$slabs[$ids]."' title='$editTxt'><img class='edit' src='theme/images/edit.gif' alt='$editTxt' /></a>";
                    $removeItem = "<a href='?action=delete&slab=".$slabs[$ids]."' title='$delTxt' onclick='return confirm(\"$deleteConfirmMsg\");'><img class='delete' src='theme/images/trash.gif' alt='$delTxt' /></a>";
                    $ul = makeLink("?action=reorderinfo&cat=$catname&param=".$pos.','.($pos-1),'<img class="updown" src="theme/images/arrow1_n.gif" title="'.$upTag.'" alt="'.$upTag.'" />');
                    $dl = makeLink("?action=reorderinfo&cat=$catname&param=".$pos.','.($pos+1),'<img class="updown" src="theme/images/arrow1_s.gif" title="'.$downTag.'" alt="'.$downTag.'" />');
                    if( $pos==0 ) {
                        $ul='<img src="theme/images/arrow1_n.gif" title="'.$upTag.'" alt="'.$upTag.'" />';
                    }
                    if( $pos==$n ) {
                        $dl='<img src="theme/images/arrow1_s.gif" title="'.$downTag.'" alt="'.$downTag.'" />';
                    }          
                    $readSlabs = $razorArray['slabs'];
                    $readInfoKey = $razorArray['info-bar-key'];
                    $readInfoBarCat = $razorArray['settings']['info-bar-cat'];
                    $infoChkBox = '';
                    $sentName = $slabs[$ids];
                    $notShow = $razorArray['links_cats'][$readInfoBarCat];
                    foreach( $readSlabs as $slabKey=>$slabName ) {
                        if (!in_array($slabKey,$notShow) and !in_array( $slabName,array_keys($razorArray['ext_links']))) {
                            $checked = '';
                            foreach( $readInfoKey as $infoKey=>$tempInfoKey ) {
                                if ($tempInfoKey == $slabs[$ids] && $razorArray['info-bar-value'][$infoKey] == $slabName) {
                                    $checked = 'checked';
                                }
                            }
                            $infoChkBox.= "<input type='checkbox' name='infoBarDes[]' value='$sentName".","."$slabName' $checked ><label for='infobardes'>".$razorArray['titles'][$slabKey]." </label>";
                        } 
                    }
                    if(isset($razorArray['info-bar-global']) && in_array($slabs[$ids],$razorArray['info-bar-global'])){
                        $gchecked = 'checked';
                        $hidden = 'Global';
                        $infoChkBox = '<span class="hidden">'.$infoChkBox.'</span>';
                    } else {
                        $gchecked = '';
                        $hidden = '';
                    }
                    $s = "<tr>
                    <td>$position</td>
                    <td><input type='checkbox' name='global[]' value='$slabs[$ids]' $gchecked ></td>
                    <td>$titles[$ids]</td>
                    <td>$hidden$infoChkBox</td>
                    <td>$ul $dl $editItem $removeItem</td>
                          </tr>";
                    echo $s;
                    $position = $position + 1;
                }
            echo "<tr class='tableFooter'><th></th><th></th><th></th><th></th><th></th></tr></table><input class='tableCenter' id='button' type='submit' value='".lt('Add Info')."'></form></div>";
            }
        }
        // end //
    }
    // end ///////////////////////

    // reorder slabs //
    function reorder($cat,$pos1,$pos2) {
        global $razorArray;
        $cats = $razorArray['links_cats'];
        $cc = $cats[$cat][$pos1];
        $cats[$cat][$pos1] = $cats[$cat][$pos2];
        $cats[$cat][$pos2] = $cc;
        $razorArray['links_cats'] = $cats;
    }
    // end ////////////

    // perform reorder slabs //
    function performMove() {
        if( $_GET['action']=='reordercat' || $_GET['action']=='reorderinfo') {
            $catName = strtolower( stripslashes($_GET['cat']) );
            $catName = cleanSlabTitle( $catName );
            $par = explode(',',$_GET['param']);
            if( is_numeric($par[0]) && is_numeric($par[1]) ) {
                $par[0] = (int)$par[0];
                $par[1] = (int)$par[1];
                reorder( $catName, $par[0], $par[1] );
                saveRazorArray();
                $_SESSION['opencat']=$catName;
            }
        }
    }
    // end ////////////////////

    // delete slab from system //
    function doDelete() {
        global $razorArray;
        $slab = $_GET['slab'];
        if (!in_array($slab, $razorArray['slabs'])){
            MsgBox(lt('Invalid name'), 'redbox');
            return;
        }
        $slabList = $razorArray['slabs'];
        $titleList = $razorArray['titles'];
        $hp = $razorArray['homepage'];
        $delpg = new SLAB();
        $delpg->loadslab($slab);
        $title = $titleList[ $delpg->slabId ];
        if( $slab == $hp ) {
            $msg = sprintf( lt('Cannot Delete, Your homepage cannot be deleted'),$title );
            MsgBox( $msg, 'redbox' );
            return;
        }
        $delpg->catReset();
        $delpg->commitChanges();
        unset( $slabList[ $delpg->slabId ] );
        unset( $titleList[ $delpg->slabId ] );
        $fileAsID = array_search($delpg->slab, $razorArray['slabs']);
        if ( isset( $razorArray['ext_links']) ) {
            if ( in_array( $slab,array_keys($razorArray['ext_links']) ) ) {
                unset($razorArray['ext_links'][$slab]);
                if ( isset( $razorArray['ext_link_win']) ) {
                    if ( in_array( $slab,array_keys($razorArray['ext_link_win']) ) ) {
                        unset($razorArray['ext_link_win'][$slab]);
		    }
                }
            } else {
                deleteFile( RAZOR_PAGES_DIR.findPageFile( $fileAsID ) );
            }
        } else {
            deleteFile( RAZOR_PAGES_DIR.findPageFile( $fileAsID ) );
        }   
        $razorArray['slabs'] = $slabList;
        $razorArray['titles'] = $titleList;
        BsocketB('admin-on-page-delete', array($slab));   
        saveRazorArray();
        $msg = sprintf( "<strong>".lt('Content successfully deleted').'</strong>',"<b>$title</b>" );
        echo '<br />';
        MsgBox( $msg, 'greenbox' );
    }
    // end /////////////////////

    ///////////////////////////////////////////////////////////
    // ########## End of Content Manager functions ######### //
    ///////////////////////////////////////////////////////////

    ///////////////////////////////////////////////////////////
    // ############### File Manager functions ############## //
    ///////////////////////////////////////////////////////////

    // file manager view files //
    function fileManager() {
        if($_SESSION['adminType'] == 'sadmin') {
            $fileManPath = RAZOR_SADMIN_PATH;
        } else {
            $fileManPath = RAZOR_FILEMAN_PATH;
        }
        // set upload limits if server permits //
        @ini_set( 'upload_max_filesize', '100M' );
        @ini_set( 'post_max_size', '105M');
        @ini_set( 'memory_limit', '205M');
        @ini_set( 'max_execution_time', '300' );
        // end of set // 
        $dirToView = '';
        $dirFound = false;
        $fileFound = false;
        if(isset($_GET['dir']) && $_GET['dir']) {
            // get sub dir from url //
            $subDir = $_GET['dir'];
            // ensure no one has injected url, force to correct format //
            // IMPORTANT - TO PREVENT ACCESS TO OTHER AREAS ON SERVER //
            $subDir = str_replace( '/','>',$subDir );
            $subDir = str_replace( '..','',$subDir );
            // change to path format //
            $subDir = str_replace( '>','/',$subDir );
            if (is_dir(getSystemRoot(RAZOR_ADMIN_FILENAME).$fileManPath.$subDir)) {
                $dirToView = $subDir.'/';
                $dirFound = true;
            } elseif (file_exists(getSystemRoot(RAZOR_ADMIN_FILENAME).$fileManPath.$subDir)) {
                $dirToView = $subDir;
                $fileFound = true;
            }
            // remove user level ability to do anything with restricted files //
            if($_SESSION['adminType'] == 'user'){
                $noAccessArray = noReadWriteAccess();
                if(in_array($dirToView, $noAccessArray)){
                    return;
                }
            }
        }
        if(isset($_GET['del']) && $_GET['del']) {
            if ($dirFound) {
                $dirArray = array();
                if(substr($dirToView,-1) == '/') {
                    $dirToView = substr($dirToView,0,-1);
                }
                $dirArray = explode('/',$dirToView);
                array_pop($dirArray);
                $verifyDir = implode('/',$dirArray).'/';
                if (is_dir(getSystemRoot(RAZOR_ADMIN_FILENAME).$fileManPath.$verifyDir)) {
                    $dirDelRes = deleteDirR($fileManPath.$dirToView);
                    if(!$dirDelRes) {
                        MsgBox(lt('Folder and contents deleted').'...', 'greenbox');
                    } else {
                        MsgBox(lt('Error deleting folder and contents').'...', 'redbox');
                    }
                    $dirToView = $verifyDir;
                }
            } elseif ($fileFound) {
                $verifyFile = str_replace( basename($dirToView),'',$dirToView );
                if (is_dir(getSystemRoot(RAZOR_ADMIN_FILENAME).$fileManPath.$verifyFile)) {
                    deleteFile($fileManPath.$dirToView);
                    $dirToView = $verifyFile;
                }
            }
        }
        if (isset($_POST['upload'])) {
            uploadFiles();
        }
        if (isset($_POST['rename'])) {
            renameFileDir();
        }
        if (isset($_POST['copy'])) {
            copyFileDir();
        }
        if (isset($_POST['move'])) {
            moveFileDir();
        }
        if (isset($_POST['createnewdir'])) {
            createNewDir();
        }
        if (isset($_POST['edit'])) {
            editFile();
        }
        $filesPath = getSystemRoot(RAZOR_ADMIN_FILENAME).$fileManPath.$dirToView;
        if (is_dir($filesPath)) {
            if (isset($_GET['up']) && $_GET['up']) {
                displayFileUpload($dirToView);
            } elseif (isset($_GET['ren']) && $_GET['ren']) {
                displayRenameFileDir($dirToView, false);
            } elseif (isset($_GET['cop']) && $_GET['cop']) {
                displayCopyFileDir($dirToView, false);
            } elseif (isset($_GET['mov']) && $_GET['mov']) {
                displayMoveFileDir($dirToView, false);
            } elseif (isset($_GET['newdir']) && $_GET['newdir']) {
                displayCreateNewDir($dirToView);
            } else {
                $readFiles = array();
                $readFiles = readDirContents($filesPath);
                BsocketB('admin-datastore-data', array( &$filesPath, &$readFiles ));
                displayDirContents($readFiles, $dirToView);
            }
        } elseif (file_exists($filesPath)) {
            if (isset($_GET['ren']) && $_GET['ren']) {
                displayRenameFileDir($dirToView, true);
            } elseif (isset($_GET['cop']) && $_GET['cop']) {
                displayCopyFileDir($dirToView, true);
            } elseif (isset($_GET['mov']) && $_GET['mov']) {
                displayMoveFileDir($dirToView, true);
            } elseif (isset($_GET['edit']) && $_GET['edit']) {
                displayEditFile($dirToView);
            } else {
                displayFileType($dirToView);
            }
        }
    }
    // end ///////////////////////

    // output file to screen //
    function displayFileType($filePath) {
        if($_SESSION['adminType'] == 'sadmin') {
            $fileManPath = RAZOR_SADMIN_PATH;
            $sadmin = true;
        } else {
            $fileManPath = RAZOR_FILEMAN_PATH;
            $sadmin = false;
        }
        $displayPath = getSystemRoot(RAZOR_ADMIN_FILENAME).$fileManPath.$filePath;
        $fileName = basename($filePath);
        $tempInfo = pathinfo($fileName);
        $fileExt = '';
        if(isset($tempInfo['extension'])){
             $fileExt = strtolower($tempInfo['extension']);
        }

        // create breadcrumb //
        $breadArray = array();
        $strippedPath = str_replace( $fileName,'',$filePath );
        if(substr($strippedPath,-1) == '/') {
            $strippedPath = substr($strippedPath,0,-1);
        }
        $breadArray = explode('/', $strippedPath);
        if(substr($fileManPath,-1) == '/') {
            $fileHome = substr($fileManPath,0,-1);
        } else {
            $fileHome = $fileManPath;
        }
        $fileHome = str_replace( '/',' / ',$fileHome );
        if($sadmin) {
            $fileHome.= 'root';
        }
        $breadcrumb = "<a href='?action=filemanview'>".$fileHome."</a> / ";
        $pathLink = '';
        foreach($breadArray as $crumb) {
            if (!empty($crumb)) {
                if($pathLink != '' && substr($pathLink,-1) != '/') {
                    $pathLink.= '/'; 
                }
                $pathLink.= $crumb; 
                if (is_dir(getSystemRoot(RAZOR_ADMIN_FILENAME).$fileManPath.$pathLink)) {
                    $crumbLink = str_replace( '/','>',$pathLink );
                    $breadcrumb.= "<a href='?action=filemanview&dir=$crumbLink'>$crumb</a> / ";
                }
            }
        }
        $imageExt = explode(',',RAZOR_FILEMAN_VIEW_MED);
        $docExt = explode(',',RAZOR_FILEMAN_VIEW_DOC);

        if(in_array($fileExt, $imageExt)) {
            list($iWidth, $iHeight) = getimagesize($displayPath);
            $iHeight = $iHeight + 40;
            $showFile = "<iframe src ='$displayPath' width='99.5%' height='".$iHeight."px' frameborder='0'></iframe>";
        } elseif(in_array($fileExt, $docExt)) {
            $showFile = "<iframe src ='$displayPath' width='99.5%' height='500px' frameborder='0'></iframe>";
        } else {
            $showFile = '';
            MsgBox(lt('Cannot show file, unknown filetype'),'redbox');
        }
        echo "<h1>".lt('File Viewer')."</h1>";
        echo "<div class='contentwh'>";
        echo '<p><b>'.lt('Path').' : </b>'.$breadcrumb.$fileName.'</p>';
        echo $showFile;
        echo "</div>";
    }
    // end //////////////////

    // output edit file display //
    function displayEditFile($filePath) {
        if($_SESSION['adminType'] == 'sadmin') {
            $fileManPath = RAZOR_SADMIN_PATH;
            $sadmin = true;
        } else {
            $fileManPath = RAZOR_FILEMAN_PATH;
            $sadmin = false;
        }
        $fileManagerTag = 'File Editor';
        $fileManagerAlt = 'File Manager';
        $filename = basename($filePath);
        $tempInfo = pathinfo($filename);
        $fileExt = '';
        if(isset($tempInfo['extension'])){
             $fileExt = strtolower($tempInfo['extension']);
        }
        $submitButton = 'save';
        $pathTag = 'Path';

        $readPath = getSystemRoot(RAZOR_ADMIN_FILENAME).$fileManPath.$filePath;

        $docExt = explode(',',RAZOR_FILEMAN_EDIT_TYPE);

        if(in_array($fileExt, $docExt)) {
            if(file_exists($readPath)) {
                $fileContent = file_get_contents($readPath);
            } else {
                $fileContent = false;
            }
        } else {
            $fileContent = false;
        }

        // create breadcrumb //
        $breadArray = array();
        $strippedPath = str_replace( $filename,'',$filePath );
        if(substr($strippedPath,-1) == '/') {
            $strippedPath = substr($strippedPath,0,-1);
        }
        $breadArray = explode('/', $strippedPath);
        if(substr($fileManPath,-1) == '/') {
            $fileHome = substr($fileManPath,0,-1);
        } else {
            $fileHome = $fileManPath;
        }
        $fileHome = str_replace( '/',' / ',$fileHome );
        if($sadmin) {
            $fileHome.= 'root';
        }
        $breadcrumb = "<a href='?action=filemanview'>".$fileHome."</a> / ";
        $pathLink = '';
        foreach($breadArray as $crumb) {
            if (!empty($crumb)) {
                if($pathLink != '' && substr($pathLink,-1) != '/') {
                    $pathLink.= '/'; 
                }
                $pathLink.= $crumb; 
                if (is_dir(getSystemRoot(RAZOR_ADMIN_FILENAME).$fileManPath.$pathLink)) {
                    $crumbLink = str_replace( '/','>',$pathLink );
                    $breadcrumb.= "<a href='?action=filemanview&dir=$crumbLink'>$crumb</a> / ";
                }
            }
        }

        // get return path for where to go after copy //
        $pos = strripos($filePath, $filename); 
        if ($pos !== false) {
            $returnPath = substr($filePath, 0, $pos);
        }
        if(substr($returnPath,-1) == '/') {
            $returnPath = substr($returnPath,0,-1);
        }
        $returnPath = str_replace( '/','>',$returnPath );
        // work out correct form action //
        if ($returnPath == '') {
            $formAction = '?action=fileman';
        } else {
            $formAction = '?action=fileman&dir='.$returnPath;
        }
        
        if (!$fileContent) {
            // output if file not ok //
            MsgBox(lt('Cannot edit file, unsupported or invalid file type'), 'redbox');
            $te[1]=<<<TET
                <h1>$fileManagerTag</h1>
                <div class='contentwh'>
                <p><b>$pathTag : </b>$breadcrumb$filename</p>
                </div>
TET;
            $te[2] = '';
            $te[3] = '';
            $te[4] = '';
        } else {
            // output if file is ok to edit //
            $te[1]=<<<TET
                <h1>$fileManagerTag</h1>
                <div class='contentwh'>
                <p><b>$pathTag : </b>$breadcrumb$filename</p>
                <form action='$formAction' method=post class='pagemod_form'>
                <table class='tableEditItem'>
                    <tr class='tableFooter'><td></td><td></td></tr>
                    <tr><td class='tableEditBox'>
TET;
            $te[2] = "<textarea name='content' rows=20 cols=70 id='editbox' class='editbox'>";
            $te[3] = "</textarea>";
            $te[4]=<<<TET
                    </td>
                    </tr>
                    <tr class='tableFooter'><td></td><td></td></tr>
                </table>
                <input type='hidden' name='filepath' value='$filePath' />
                <input id='button' type='submit' value='$submitButton' name='edit' />
            </form>
            </div>
TET;
        }
        echo $te[1];
        echo $te[2];
        $fileContent =  str_replace("&", "&amp;", $fileContent);
        $fileContent =  str_replace("</textarea>", "&lt;/textarea&gt;", $fileContent);
        echo $fileContent;
        echo $te[3];
        echo $te[4];
        // end //
    }
    // end ///////////////////////

    // edit a file //
    function editFile() {
        if($_SESSION['adminType'] == 'sadmin') {
            $fileManPath = RAZOR_SADMIN_PATH;
        } else {
            $fileManPath = RAZOR_FILEMAN_PATH;
        }
        if( isset($_POST['content'])) {
            $content = $_POST['content'];
        } 
        if( isset($_POST['filepath'])) {
            $filePath = $_POST['filepath'];
        }  

        if (isset($content) && isset($filePath)) {
            if( !put2file( $fileManPath.$filePath, stripslashes($content) ) ) {
                MsgBox(lt('Error writing to file'), 'redbox');
            } else {
                MsgBox(lt('File saved'), 'greenbox');
            }
        }
    }
    // end //////////
    
    // limit access to certain files or directories //
    function noReadWriteAccess() {
        $razorDataFile = basename(RAZOR_DATA);
        if(substr(RAZOR_BACKUP_DIR,-1) == '/') {
            $backupStrip = substr(RAZOR_BACKUP_DIR,0,-1);
        } else {
            $backupStrip = RAZOR_BACKUP_DIR;
        }
        $backupDir = basename($backupStrip);
        if(substr(RAZOR_LOGS_DIR,-1) == '/') {
	            $logStrip = substr(RAZOR_LOGS_DIR,0,-1);
	        } else {
	            $logStrip = RAZOR_LOGS_DIR;
        }
        $logDir = basename($logStrip);
        $indexFile = 'index.htm';
        
        return array($razorDataFile,$backupDir,$logDir,$indexFile);
    }
    // end ///////////////////////////////////////////

    // output datastore dir contents //
    function displayDirContents($filesArray, $dirPath) {
        if($_SESSION['adminType'] == 'sadmin') {
            $fileManPath = RAZOR_SADMIN_PATH;
            $sadmin = true;
        } elseif($_SESSION['adminType'] == 'admin') {
            $fileManPath = RAZOR_FILEMAN_PATH;
            $sadmin = false;
        } else {
            $fileManPath = RAZOR_FILEMAN_PATH;
            $sadmin = false;
            $noAccessArray = noReadWriteAccess();
            if(in_array(substr($dirPath,0,-1), $noAccessArray)){
                $filesArray = array();
                $dirPath = '';
            }
        }
        $breadPath = $dirPath;
        $breadPath = str_replace( '/','>',$breadPath );
        $breadArray = array();
        $breadArray = explode('/', $dirPath);
        $crumbLink = '';
        $pathLink = '';
        if(substr($fileManPath,-1) == '/') {
            $fileHome = substr($fileManPath,0,-1);
        } else {
            $fileHome = $fileManPath;
        }
        $fileHome = str_replace( '/',' / ',$fileHome );
        if($sadmin) {
            $fileHome.= 'root';
        }
        $breadcrumb = "<a href='?action=filemanview'>".$fileHome."</a> / ";
        $pathLink = '';
        foreach($breadArray as $crumb) {
            if (!empty($crumb)) {
                if($pathLink != '' && substr($pathLink,-1) != '/') {
                    $pathLink.= '/'; 
                }
                $pathLink.= $crumb; 
                if (is_dir(getSystemRoot(RAZOR_ADMIN_FILENAME).$fileManPath.$pathLink)) {
                    $crumbLink = str_replace( '/','>',$pathLink );
                    $breadcrumb.= "<a href='?action=filemanview&dir=$crumbLink'>$crumb</a> / ";
                }
            }
        }
        $dirArray = array();
        $fileArray = array();
        foreach ($filesArray as $key=>$fileName) {
            if (is_dir(getSystemRoot(RAZOR_ADMIN_FILENAME).$fileManPath.$dirPath.$fileName)) {
                array_push($dirArray, $fileName);
            } else {
                array_push($fileArray, $fileName);
            }
        }
        sort( $dirArray );
        sort( $fileArray );
        $deleteConfirmMsg = lt('Are you sure you want to delete this file').'?';
        echo "<h1>".lt('View Files')."</h1>";
        echo "<div class='contentwh'>";
        $createDirTag = lt('Create Dir');
        $uploadTag = lt('Upload');
        if ($crumbLink == '') {
            echo "<p><a href='?action=filemanview&newdir=true'>$createDirTag</a></p>";
            echo "<p><a href='?action=filemanview&up=true'>$uploadTag</a></p>"; 
        } else {
            echo "<p><a href='?action=filemanview&dir=$crumbLink&newdir=true'>$createDirTag</a></p>";
            echo "<p><a href='?action=filemanview&dir=$crumbLink&up=true'>$uploadTag</a></p>";
        }
        echo '<p><b>'.lt('Path').' : </b>'.$breadcrumb.'</p>';
        echo "<table class='tableViewFiles'><tr class='tableTitle'><th class='five'></th><th class='auto'>".lt('Contents')."</th><th class='twenty'>".lt('Options')."</th></tr>";
        $renameTag = lt('Rename');
        $moveTag = lt('Move');
        $copyTag = lt('Copy');
        $deleteTag = lt('Delete');
        $editTag = lt('Edit');
        $viewTag = lt('View');

        foreach($dirArray as $key => $dirName) {
            if($_SESSION['adminType'] == 'user' && $dirPath == '' && in_array($dirName, $noAccessArray)) {
                echo '<tr><td class="tdgreyd"><img src="theme/images/folder.gif" alt="Folder" /></td><td class="media"><a>'.$dirName.'</a></td>
                <td class="media">'.lt('Access denied').'</td></tr>';
            } else {
                echo '<tr><td class="tdgreyd"><img src="theme/images/folder.gif" alt="Folder" /></td><td class="media"><a href=\'?action=filemanview&dir='.$breadPath.$dirName.'\'>'.$dirName.'</a></td>
                <td class="media"><a href="?action=fileman&dir='.$breadPath.$dirName.'&ren=true" title="'.$renameTag.'" ><img class="edit" src="theme/images/loop.gif" alt="'.$renameTag.'" /></a>
                <a href="?action=fileman&dir='.$breadPath.$dirName.'&mov=true" title="'.$moveTag.'" ><img class="edit" src="theme/images/move.gif" alt="'.$moveTag.'" /></a>
                <a href="?action=fileman&dir='.$breadPath.$dirName.'&cop=true" title="'.$copyTag.'" ><img class="edit" src="theme/images/copy.gif" alt="'.$copyTag.'" /></a>
                <a href="?action=fileman&dir='.$breadPath.$dirName.'&del=true" title="'.$deleteTag.'" onclick=\'return confirm("'.$deleteConfirmMsg.'");\'><img class="delete" src="theme/images/trash.gif" alt="'.$deleteTag.'" /></a></td></tr>';
            }
        }
        foreach($fileArray as $key => $filename) {
            if($_SESSION['adminType'] == 'user' && $dirPath == '' && in_array($filename, $noAccessArray)) {
                echo '<tr><td class="tdgreyl"><img src="theme/images/file.gif" alt="File" /></td><td class="media">'.$filename.'</td>
                <td class="media">'.lt('Access denied').'</td></tr>';
            } else {
                echo '<tr><td class="tdgreyl"><img src="theme/images/file.gif" alt="File" /></td><td class="media">'.$filename.'</td>
                <td class="media"><a href=\'?action=filemanview&dir='.$breadPath.$filename.'\' title="'.$viewTag.'" ><img class="updown" src="theme/images/window.gif" alt="'.$viewTag.'" /></a>
                <a href=\'?action=filemanview&dir='.$breadPath.$filename.'&edit=true\' title="'.$editTag.'" ><img class="edit" src="theme/images/edit.gif" alt="'.$editTag.'" /></a>
                <a href="?action=fileman&dir='.$breadPath.$filename.'&ren=true" title="'.$renameTag.'" ><img class="edit" src="theme/images/loop.gif" alt="'.$renameTag.'" /></a>
                <a href="?action=fileman&dir='.$breadPath.$filename.'&mov=true" title="'.$moveTag.'" ><img class="edit" src="theme/images/move.gif" alt="'.$moveTag.'" /></a>
                <a href="?action=fileman&dir='.$breadPath.$filename.'&cop=true" title="'.$copyTag.'" ><img class="edit" src="theme/images/copy.gif" alt="'.$copyTag.'" /></a>
                <a href="?action=fileman&dir='.$breadPath.$filename.'&del=true" title="'.$deleteTag.'" onclick=\'return confirm("'.$deleteConfirmMsg.'");\'><img class="delete" src="theme/images/trash.gif" alt="'.$deleteTag.'" /></a></td></tr>';
            }
        }
        echo "<tr class='tableFooter'><th class='five'></th><th class='auto'></th><th class='twenty'></th></tr>";
        echo '</table></div>';
    }
    // end //////////////////

    // output copy display //
    function displayCopyFileDir($path, $isFile) {
        if($_SESSION['adminType'] == 'sadmin') {
            $fileManPath = RAZOR_SADMIN_PATH;
        } else {
            $fileManPath = RAZOR_FILEMAN_PATH;
        }
        $copyName = basename(getSystemRoot(RAZOR_ADMIN_FILENAME).$fileManPath.$path);
        echo "<h1>".lt('Copy File or Directory')."</h1>";
        echo "<div class='contentwh'>";
        $contentsArray = array();
        $contentsArray = readAllDirR(getSystemRoot(RAZOR_ADMIN_FILENAME).$fileManPath);
        array_push($contentsArray, '');
        asort($contentsArray);
        $dirSelectMenu = "<table class='tableViewFiles'><tr class='tableFooter'><th class='five'></th><th class='auto'></th></tr>";
        foreach($contentsArray as $dirName) {
            $end = basename($dirName);
            $pre = '';
            $pathArray = explode('/',$dirName);
            $amount = count($pathArray);
            if ($end == '') {
                if(substr($fileManPath,-1) == '/') {
                    $end = substr($fileManPath,0,-1);
                } else {
                    $end = $fileManPath;
                }
                $amount = 0;
            }
            for ($c = 0; $c < $amount; $c++) {
                $pre.= ' > ';
            }
            $dirSelectMenu.= '<tr><td class="tdgreyd"><input type="radio" name="dirselected" value="'.$dirName.'" /></td><td class="media">'.$pre.$end.'</td></tr>';
        }
        $dirSelectMenu.= "<tr class='tableFooter'><th class='five'></th><th class='auto'></th></tr>";
        $dirSelectMenu.= '</table>';
        // get return path for where to go after copy //
        $pos = strripos($path, $copyName); 
        if ($pos !== false) {
            $returnPath = substr($path, 0, $pos);
        }
        if(substr($returnPath,-1) == '/') {
            $returnPath = substr($returnPath,0,-1);
        }
        $returnPath = str_replace( '/','>',$returnPath );
        // work out correct form action //
        if ($returnPath == '') {
            echo '<form enctype="multipart/form-data" action="?action=fileman" method="POST">';
        } else {
            echo '<form enctype="multipart/form-data" action="?action=fileman&dir='.$returnPath.'" method="POST">';
        }
        // output form data dependant on file or directory
        if ($isFile) {
            echo '<p><b>'.lt('Copy file').' </b>'.$copyName.'<input type="hidden" name="copyfile" value="'.$path.'" /></p>';
            echo '<p><b>'.lt('To directory').' </b></p>'.$dirSelectMenu;
        } else {
            echo '<p><b>'.lt('Copy directory').' </b>'.$copyName.'<input type="hidden" name="copydir" value="'.$path.'" /></p>';
            echo '<p><b>'.lt('To directory').' </b></p>'.$dirSelectMenu;
        }
        echo '<input id="button" type="submit" value="'.lt('copy').'" name="copy" /></form></div>';
    }
    // end //////////////////

    // output copy display //
    function displayMoveFileDir($path, $isFile) {
        if($_SESSION['adminType'] == 'sadmin') {
            $fileManPath = RAZOR_SADMIN_PATH;
        } else {
            $fileManPath = RAZOR_FILEMAN_PATH;
        }
        $copyName = basename(getSystemRoot(RAZOR_ADMIN_FILENAME).$fileManPath.$path);
        echo "<h1>".lt('Move File or Directory')."</h1>";
        echo "<div class='contentwh'>";
        $contentsArray = array();
        $contentsArray = readAllDirR(getSystemRoot(RAZOR_ADMIN_FILENAME).$fileManPath);
        array_push($contentsArray, '');
        asort($contentsArray);
        $dirSelectMenu = "<table class='tableViewFiles'><tr class='tableFooter'><th class='five'></th><th class='auto'></th></tr>";
        foreach($contentsArray as $dirName) {
            $end = basename($dirName);
            $pre = '';
            $pathArray = explode('/',$dirName);
            $amount = count($pathArray);
            if ($end == '') {
                if(substr($fileManPath,-1) == '/') {
                    $end = substr($fileManPath,0,-1);
                } else {
                    $end = $fileManPath;
                }
                $amount = 0;
            }
            for ($c = 0; $c < $amount; $c++) {
                $pre.= ' > ';
            }
            $dirSelectMenu.= '<tr><td class="tdgreyd"><input type="radio" name="newdirname" value="'.$dirName.'" /></td><td class="media">'.$pre.$end.'</td></tr>';
        }
        $dirSelectMenu.= "<tr class='tableFooter'><th class='five'></th><th class='auto'></th></tr>";
        $dirSelectMenu.= '</table>';
        // get return path for where to go after copy //
        $pos = strripos($path, $copyName); 
        if ($pos !== false) {
            $returnPath = substr($path, 0, $pos);
        }
        if(substr($returnPath,-1) == '/') {
            $returnPath = substr($returnPath,0,-1);
        }
        $returnPath = str_replace( '/','>',$returnPath );
        // work out correct form action //
        if ($returnPath == '') {
            echo '<form enctype="multipart/form-data" action="?action=fileman" method="POST">';
        } else {
            echo '<form enctype="multipart/form-data" action="?action=fileman&dir='.$returnPath.'" method="POST">';
        }
        // output form data dependant on file or directory
        if ($isFile) {
            echo '<p><b>'.lt('Move file').' </b>'.$copyName.'<input type="hidden" name="oldfilename" value="'.$path.'" /></p>';
            echo '<p><b>'.lt('To directory').' </b></p>'.$dirSelectMenu;
        } else {
            echo '<p><b>'.lt('Move directory').' </b>'.$copyName.'<input type="hidden" name="olddirname" value="'.$path.'" /></p>';
            echo '<p><b>'.lt('To directory').' </b></p>'.$dirSelectMenu;
        }
        echo '<input id="button" type="submit" value="'.lt('move').'" name="move" /></form></div>';
    }
    // end //////////////////

    // output rename display //
    function displayRenameFileDir($path, $isFile) {
        if($_SESSION['adminType'] == 'sadmin') {
            $fileManPath = RAZOR_SADMIN_PATH;
        } else {
            $fileManPath = RAZOR_FILEMAN_PATH;
        }
        $oldName = basename(getSystemRoot(RAZOR_ADMIN_FILENAME).$fileManPath.$path);
        echo "<h1>".lt('Rename File or Directory')."</h1>";
        echo "<div class='contentwh'>";
        $linkPath = $path;

        $pos = strripos($linkPath, $oldName); 
        if ($pos !== false) {
            $linkPath = substr($linkPath, 0, $pos);
        }
        if(substr($linkPath,-1) == '/') {
            $linkPath = substr($linkPath,0,-1);
        }
        $linkPath = str_replace( '/','>',$linkPath );
        if ($linkPath == '') {
            echo '<form enctype="multipart/form-data" action="?action=fileman" method="POST">';
        } else {
            echo '<form enctype="multipart/form-data" action="?action=fileman&dir='.$linkPath.'" method="POST">';
        }
        echo '<input type="hidden" name="path" value="'.$linkPath.'" />';
        echo '<input class="IEbugFix" type="text" name="IEbugFix" />';
        if ($isFile) {
            echo '<p><b>'.lt('Old filename').' </b><input type="hidden" name="oldfilename" value="'.$oldName.'" />'.$oldName.'</p>';
            echo '<p><b>'.lt('New filename').' </b><input type="text" name="newfilename" value="'.$oldName.'" /></p>';
        } else {
            echo '<p><b>'.lt('Old directory name').' </b><input type="hidden" name="olddirname" value="'.$oldName.'" />'.$oldName.'</p>';
            echo '<p><b>'.lt('New directory name').' </b><input type="text" name="newdirname" value="'.$oldName.'" /></p>';
        }
        echo '<input id="button" type="submit" value="'.lt('rename').'" name="rename" /></form></div>';

    }
    // end //////////////////

    // copy files or directory //
    function copyFileDir() {
        if($_SESSION['adminType'] == 'sadmin') {
            $fileManPath = RAZOR_SADMIN_PATH;
        } else {
            $fileManPath = RAZOR_FILEMAN_PATH;
        }
        $dirSelected = '';

        if( isset($_POST['copyfile'])) {
            $copyFrom = $_POST['copyfile'];
        } 
        if( isset($_POST['copydir'])) {
            $copyDir = $_POST['copydir'];
        }        
        if( isset($_POST['dirselected'])) {
            $dirSelected = $_POST['dirselected'];
        }
        // copy file to directory //
        if(isset($copyFrom) && isset($dirSelected)) {
            $copyTo = $dirSelected.'/'.basename($copyFrom);
            if(!file_exists(getSystemRoot(RAZOR_ADMIN_FILENAME).$fileManPath.$copyTo)) {
                copyFile($fileManPath.$copyFrom, $fileManPath.$copyTo);
            } else {
                MsgBox(lt('Error copying, filename already exists in chosen directory'), 'redbox');
            }
        }
        if(isset($copyDir) && isset($dirSelected)) {
            $copyTo = $dirSelected.'/'.basename($copyDir);
            if(!is_dir(getSystemRoot(RAZOR_ADMIN_FILENAME).$fileManPath.$copyTo)) {
                  if(copyDirR($fileManPath.$copyDir, $fileManPath.$copyTo)) {
                      MsgBox(lt('Directory and contents copy complete'), 'greenbox');
                  }
            } else {
                MsgBox(lt('Error copying, directory name already exists in chosen directory'), 'redbox');
            }
        }

    }

    // end //////////////////

    // rename files or directory //
    function renameFileDir() {
        if($_SESSION['adminType'] == 'sadmin') {
            $fileManPath = RAZOR_SADMIN_PATH;
        } else {
            $fileManPath = RAZOR_FILEMAN_PATH;
        }
        $oldName = '';
        $newName = '';
        $linkPath = '';
        
        if (isset($_POST['oldfilename']) && $_POST['oldfilename']) {
            $oldName = $_POST['oldfilename'];
        }
        if (isset($_POST['newfilename']) && $_POST['newfilename']) {
            $newName = $_POST['newfilename'];
        }
        if (isset($_POST['olddirname']) && $_POST['olddirname']) {
            $oldName = $_POST['olddirname'];
        }
        if (isset($_POST['newdirname']) && $_POST['newdirname']) {
            $newName = $_POST['newdirname'];
        }
        if (isset($_POST['path']) && $_POST['path'] != '') {
            $linkPath = $_POST['path'].'/';
        }

        $cleanName = preg_replace('/[^0-9a-z\.\_\-]/i','',$newName);

        if ($cleanName == $newName) {
            $linkPath = str_replace( '>','/',$linkPath );
            $oldPath = $fileManPath.$linkPath.$oldName;
            $newPath = $fileManPath.$linkPath.$cleanName;
            if (!file_exists(getSystemRoot(RAZOR_ADMIN_FILENAME).$newPath)) {
                renameFile($oldPath, $newPath);
            } else {
                MsgBox( lt('Name already exists, please use another'), 'redbox' );
            }
        } else {
            MsgBox( lt('Name invalid, try').' <b>'.$cleanName.'</b>', 'redbox' );
        }  
    }
    // end //////////////////

    // rename files or directory //
    function moveFileDir() {
        if($_SESSION['adminType'] == 'sadmin') {
            $fileManPath = RAZOR_SADMIN_PATH;
        } else {
            $fileManPath = RAZOR_FILEMAN_PATH;
        }
        $oldName = '';
        $newName = '';
        
        if (isset($_POST['oldfilename']) && $_POST['oldfilename']) {
            $oldName = $_POST['oldfilename'];
        }
        if (isset($_POST['olddirname']) && $_POST['olddirname']) {
            $oldName = $_POST['olddirname'];
        }
        if (isset($_POST['newdirname']) && $_POST['newdirname']) {
            $newName = $_POST['newdirname'];
        }

        $oldPath = $fileManPath.$oldName;
        $newPath = $fileManPath.$newName.'/'.basename($oldName);
        if (!file_exists(getSystemRoot(RAZOR_ADMIN_FILENAME).$newPath)) {
            if (renameFile($oldPath, $newPath, false)) {
                MsgBox( lt('Move complete'), 'greenbox' );
            } else {
                MsgBox( lt('Error when moving'), 'redbox' );
            }
        } else {
            MsgBox( lt('Name already exists, error when moving'), 'redbox' );
        }
    }
    // end //////////////////

    // output create dir display //
    function displayCreateNewDir($upPath) {
        echo "<h1>".lt('Create New Directory')."</h1>";
        echo "<div class='contentwh'>";
        echo '<p>'.lt('To create a new directory, simply input the new directory name below and click create').'</p><br />';
        if ($upPath == '') {
            echo '<form enctype="multipart/form-data" action="?action=fileman" method="POST">';
        } else {
            $linkPath = $upPath;
            if(substr($linkPath,-1) == '/') {
                $linkPath = substr($linkPath,0,-1);
            }
            $linkPath = str_replace( '/','>',$linkPath );
            echo '<form enctype="multipart/form-data" action="?action=fileman&dir='.$linkPath.'" method="POST">';
        }
        echo '<input class="IEbugFix" type="text" name="IEbugFix" />';
        echo '<input type="hidden" name="path" value="'.$upPath.'" /><input type="text" name="newdirname" /><br />';
        echo '<input id="button" type="submit" value="'.lt('Create').'" name="createnewdir"/>
            </form><br /></div>';
    }
    // end //////////////////

    // create new directory //
    function createNewDir() {
        if($_SESSION['adminType'] == 'sadmin') {
            $fileManPath = RAZOR_SADMIN_PATH;
        } else {
            $fileManPath = RAZOR_FILEMAN_PATH;
        }
        $path = $fileManPath;
        $newDir = '';

        if (isset($_POST['path']) && $_POST['path']) {
            $path.= $_POST['path'].'/';
        }
        if (isset($_POST['newdirname']) && $_POST['newdirname']) {
            $newDir = $_POST['newdirname'];
        }
        $cleanDir = preg_replace('/[^0-9a-z\.\_\-]/i','',$newDir);
        if ($newDir == '') {
            MsgBox( lt('Name invalid, name field empty'), 'redbox' );
        } elseif ($cleanDir != $newDir) {
            MsgBox( lt('Name invalid, try').' <b>'.$cleanDir.'</b>', 'redbox' );
        } elseif (file_exists(getSystemRoot(RAZOR_ADMIN_FILENAME).$path.$newDir)) {
            MsgBox( lt('Name alread exists, please use another'), 'redbox' );
        } else {
            createDir($path.$newDir);
        }
    }
    // end //////////////////

    // output upload display //
    function displayFileUpload($upPath) {
        echo "<h1>".lt('Upload New Files')."</h1>";
        echo "<div class='contentwh'>";
        echo "<p>".lt('Upload limit per file').' - '.ini_get('upload_max_filesize')."</p>";
        echo '<p>'.lt('To upload files in multiple formats, please use the file upload function below. Please note that file upload is limited by your server, razorCMS attempts to up this limit to 100mb per file but your server may restrict access. If the limit above is not set to 100mb, please visit the help forum on other ways to increase this limit.').'</p><br />';
        if ($upPath == '') {
            echo '<form enctype="multipart/form-data" action="?action=fileman" method="POST">';
        } else {
            $linkPath = $upPath;
            if(substr($linkPath,-1) == '/') {
                $linkPath = substr($linkPath,0,-1);
            }
            $linkPath = str_replace( '/','>',$linkPath );
            echo '<form enctype="multipart/form-data" action="?action=fileman&dir='.$linkPath.'" method="POST">';
        }
        echo '<input type="hidden" name="MAX_FILE_SIZE" value="'.getMaxUploadSize(ini_get('upload_max_filesize')).'" /><input type="hidden" name="path" value="'.$upPath.'" />';
        $c = 0;
        while($c < 6) {
            echo "<input name='file-upload[]' type='file' /><br />";
            $c ++;
        }
        echo '<input id="button" type="submit" value="'.lt('upload files').'" name="upload"/>
            </form><br /></div>';
    }
    // end //////////////////

    // upload file to server //
    function uploadFiles() {
        if($_SESSION['adminType'] == 'sadmin') {
            $fileManPath = RAZOR_SADMIN_PATH;
        } else {
            $fileManPath = RAZOR_FILEMAN_PATH;
        }
        $uploadPath = $fileManPath;
        if(isset($_POST['path']) && $_POST['path']) {
            $uploadPath.= $_POST['path'];
        }
        while(list($key,$name) = each($_FILES['file-upload']['name'])) {
            if ($name != '') {
                // prob check file size, if over limit, then do not allow and save to error msgbox
                $filename = basename($name);
                $filename = str_replace( ' ','_',$filename );
                $cFilename = $filename;
                $datastoreDir = getSystemRoot(RAZOR_ADMIN_FILENAME).$uploadPath;
                $datastoreFiles = readDirContents($datastoreDir);
                $counter = 0;
                while ( in_array( $filename, $datastoreFiles ) ) {
                    $filename = $counter.'_'.$cFilename;
                    $counter++;
                }
                $result = uploadFile($uploadPath.$filename, $_FILES['file-upload']['tmp_name'][$key]);
            }
        }
    }
    // end /////////////////////

    ///////////////////////////////////////////////////////////
    // ########### End of File Manager functions ########### //
    ///////////////////////////////////////////////////////////

    ///////////////////////////////////////////////////////////
    // ############## Backup Manager functions ############# //
    ///////////////////////////////////////////////////////////

    // check name for backup file //
    function backupTool_checkName($fileName) {
        if(!isset($fileName) || $fileName == '') {
            return lt('Error, please provide a filename').'...';
        }
        if ($handle = opendir(getSystemRoot(RAZOR_ADMIN_FILENAME).RAZOR_BACKUP_DIR)) {
            while (false !== ($file = readdir($handle))) {
                if ($file == $fileName) {
                    return lt('Error, a backup with this name already exists').'...';
                }
            }
            closedir($handle);
        }
        return false;
    }
    // end //

    // get list of directories to backup - recursive //
    function getDirectory( &$tempfileArray, $path = '.', $level = 0 ){
        $backupDirectory = basename(rtrim(RAZOR_BACKUP_DIR, '/'));
        $ignore = array( 'cgi-bin', '.', '..', $backupDirectory ); 
        // Directories to ignore when listing output. Many hosts 
        // will deny PHP access to the cgi-bin. 
        $dh = @opendir( $path ); 
        // Open the directory to the handle $dh 
        while( false !== ( $file = readdir( $dh ) ) ){ 
            // Loop through the directory 
            if( !in_array( $file, $ignore ) ){ 
                // Check that this file is not to be ignored 
                if( is_dir( "$path/$file" ) ){ 
                    // Its a directory, so we need to keep reading down...
                    //array_push($tempfileArray,$path.'/'.$file);             
                    getDirectory( $tempfileArray, "$path/$file", ($level+1) ); 
                    // Re-call this same function but on a new directory. 
                    // this is what makes function recursive. 
                } else {
                    array_push($tempfileArray,$path.'/'.$file);
                }         
             } 
        } 
        closedir( $dh ); 
        // Close the directory handle 
    } 

    // output the new page for settings //
    function backupTool_settings() {
        // set upload limits if server permits //
        @ini_set( 'upload_max_filesize', '100M' );
        @ini_set( 'post_max_size', '105M');
        @ini_set( 'memory_limit', '350M');
        @ini_set( 'max_execution_time', '300' );
        // end of set // 
        $backupList = array();
        // create backup dir if not present
        if(!file_exists(getSystemRoot(RAZOR_ADMIN_FILENAME).RAZOR_BACKUP_DIR)) {
            mkdir(getSystemRoot(RAZOR_ADMIN_FILENAME).RAZOR_BACKUP_DIR, 0755);
        }
        if (isset($_GET['backup']) && $_GET['backup']) {
            $fileName = str_replace( array(',',"'",'"','?','/','*','(',')','@','!','&','=','<','>'),'',$_POST['backupname'] );
            $errorMsg = backupTool_checkName($fileName);
            if(!$errorMsg) {
                // do backup //
                $tempfileArray = array();
                getDirectory($tempfileArray, getSystemRoot(RAZOR_ADMIN_FILENAME).RAZOR_DATASTORE_DIR);
                $zipfilename = $fileName.'.zip'; 
                // form is posted, handle it    
                $zipfile = new zipfile();
                // new stuff //
                foreach($tempfileArray as $file) {   
                    $f_tmp = @fopen( $file, 'r'); 
                    if($f_tmp){ 
                        $dump_buffer=fread( $f_tmp, filesize($file));
                        $tempFile = explode( '../', $file); 
                        $zipfile -> addFile($dump_buffer, $tempFile[1]); 
                        fclose( $f_tmp ); 
                    } 
                }
                // new stuff //
                $dump_buffer = $zipfile -> file(); 
                // write the file to disk //
                if(put2file(RAZOR_BACKUP_DIR.$zipfilename, $dump_buffer, strlen($dump_buffer))) {
                    MsgBox(lt('Backup created'), 'greenbox');
                } else {
                    MsgBox(lt('Error creating backup'), 'redbox');
                }
            } else {
                MsgBox($errorMsg, 'redbox');
            }
        }
        if (isset($_GET['restore']) && $_GET['restore'] && $_SESSION['adminType'] != 'user') {
            set_time_limit ( 60 );
            // new - clean datastore first //
            $cleanfileArray = array();
            getDirectory($cleanfileArray, getSystemRoot(RAZOR_ADMIN_FILENAME).RAZOR_DATASTORE_DIR);
            foreach($cleanfileArray as $file) {
                if($file != 'razor_data.txt') {
                    // try using the delete function here so this works with ftp mode too //
                    unlink($file);
                    // try using the delete function here so this works with ftp mode too //
                }             
            }
            $zip = new SimpleUnzip();
            $filename = getSystemRoot(RAZOR_ADMIN_FILENAME).RAZOR_BACKUP_DIR.$_GET['restore'];
            $entries = $zip->ReadFile($filename);
            $restoreMess = '';
            $restoreOK = 'greenbox';
            foreach($entries as $entry) {
                // check dir exists, if not create it //
                if($entry->Path != '' && !file_exists('../'.$entry->Path)) {
                    $splitPath = array();
                    $splitPath = explode('/', $entry->Path);
                    $checkPath = '..';
                    foreach($splitPath as $pathBit){
                        $checkPath.= '/'.$pathBit;
                        if(!file_exists($checkPath)) {
                            mkdir($checkPath, 0755);
                        }
                    }
                }
                // check end //
                if(put2file($entry->Path.'/'.$entry->Name, $entry->Data)) {
                    $restoreMess.= lt('Restoring')." $entry->Name <br />";
                } else {
                    $restoreMess.= lt('error restoring')." $entry->Name <br />";
                    $restoreOK = 'redbox';
                }
            }
            MsgBox($restoreMess, $restoreOK);
        }
        if (isset($_GET['delete']) && $_GET['delete']) {
            deleteFile(RAZOR_BACKUP_DIR.$_GET['delete']);
        }
        if (isset($_GET['upload']) && $_GET['upload'] && isset($_POST['upload']) && $_SESSION['adminType'] != 'user') {
            $filename = basename($_FILES['file-upload']['name']);
            $stripFileName = explode( '.', $filename);
            if ( end($stripFileName) == 'zip' ) {
                $backupDir = getSystemRoot(RAZOR_ADMIN_FILENAME).RAZOR_BACKUP_DIR;
                $backupFiles = readDirContents($backupDir);
                $counter = 0;
                while ( in_array( $filename, $backupFiles ) ) {
                    $counter++;
                    $filename = $stripFileName[0].'('.$counter.')'.'.'.$stripFileName[1];
                }
                $result = uploadFile(RAZOR_BACKUP_DIR.$filename, $_FILES['file-upload']['tmp_name']);
            } else {
                MsgBox( lt("Wrong file type, only zip files allowed"), 'redbox' );
            }
        }
        // setup output //
        if(file_exists(getSystemRoot(RAZOR_ADMIN_FILENAME).RAZOR_BACKUP_DIR)) {
            if ($handle = opendir(getSystemRoot(RAZOR_ADMIN_FILENAME).RAZOR_BACKUP_DIR)) {
                while (false !== ($file = readdir($handle))) {
                    if ($file != "." && $file != "..") {
                        $fileDate = filemtime(getSystemRoot(RAZOR_ADMIN_FILENAME).RAZOR_BACKUP_DIR.$file);
                        $backupList[$file] = $fileDate;
                    }
                }
                closedir($handle);
            }
        }
        // end setup //
        asort($backupList);
        $deleteConfirmMsg = lt( "Are you sure you want to delete this backup, remember once you delete you cannot retreive again, proceed").'?';
        $restoreConfirmMsg = lt( "Are you sure you want to restore this backup, remember once you restore all old data will be lost, proceed").'?';
        echo "<h1>".lt('Backup Tool')."</h1>";
        echo '<div class="contentwh">';
        echo '<h3>'.lt('Archived Backups').'</h3>';
        echo "<table class='tableViewBackup'>";
        echo "<tr class='tableFooter'><th class='twentyFive'>".lt('Date Created')."</th><th class='auto'>".lt('File Name')."</th><th class='twenty'>".lt('Options')."</th></tr>";
        $restoreBackup = '';
        foreach($backupList as $bkFile=>$bkFileDate) {
            if($_SESSION['adminType'] != 'user'){
                $restoreBackup = "<a href='?action=backuptool&restore=$bkFile' title='".lt('Restore')."' onclick='return confirm(\"$restoreConfirmMsg\");'><img class='updown' src='theme/images/restore.gif' alt=".lt('restore')." /></a> ";
            }
            $formatDT = date("d-m-Y H:i:s", $bkFileDate);
            echo "<tr><td>$formatDT</td><td>$bkFile</td><td>".$restoreBackup."<a href='../datastore/backup/$bkFile' title='".lt('Download')."'><img class='edit' src='theme/images/download.gif' alt=".lt('download')." /></a> <a href='?action=backuptool&delete=$bkFile' title='".lt('Delete')."' onclick='return confirm(\"$deleteConfirmMsg\");'><img class='delete' src='theme/images/trash.gif' alt=".lt('delete')." /></a></td></tr>";
        }
        echo "<tr class='tableFooter'><th class='twentyFive'></th><th class='auto'></th><th class='twentyFive'></th></tr></table>";

        echo '<h3>'.lt('Create New Backup').'</h3>';
        echo "<form action='?action=backuptool&backup=true' method='post'>";
        echo "<table class='tableViewBackup'>";
        echo "<tr class='tableFooter'><th class='auto'></th><th class='ten'></th></tr>";
        echo "<tr><td><input type='text' name='backupname' value=''>.zip</td><td><input id='button' type='submit' value='".lt('Submit')."' class='floatright'></td></tr>";
        echo "<tr class='tableFooter'><th class='twentyFive'></th><th class='auto'></th></tr></table></form>";
        if( $_SESSION['adminType'] != 'user' ) {
            echo '<h3>'.lt('Upload to Archived Backups').'</h3>';
            echo "<p>".lt('Upload limit per file').' - '.ini_get('upload_max_filesize')."</p>";
            echo '<p>'.lt('Please upload backups in zip format. Please note that file upload is limited by your server, razorCMS attempts to up this limit to 100mb per file but your server may restrict access. If the limit above is not set to 100mb, please visit the help forum on other ways to increase this limit.').'</p>';
            echo '<form enctype="multipart/form-data" action="?action=backuptool&upload=true" method="POST">';
            echo '<input type="hidden" name="MAX_FILE_SIZE" value="'.getMaxUploadSize(ini_get('upload_max_filesize')).'" />';
            echo "<table class='tableViewBackup'>";
            echo "<tr class='tableFooter'><th class='auto'></th><th class='ten'></th></tr>";
            echo '<tr><td><input name="file-upload" type="file" /></td><td><input id="button" type="submit" value="'.lt('upload file').'" name="upload"/></td></tr>';
            echo "<tr class='tableFooter'><th class='twentyFive'></th><th class='auto'></th></tr></table></form>";
        }
        echo "</div>";
    }
    // end /////////////////////////////

    ///////////////////////////////////////////////////////////
    // ########### End of Backup Manager functions ######### //
    ///////////////////////////////////////////////////////////

    ///////////////////////////////////////////////////////////
    // ############# Settings Manager functions ############ //
    ///////////////////////////////////////////////////////////

    // new settings management //
    function coreSettings() {
        if($_SESSION['adminType'] == 'user') {
            return;
        }
        global $razorArray;

        // get form signature //
        $random = false;
        if(isset($_POST['random'])){
            $randomV = htmlspecialchars(stripslashes($_POST['random']), ENT_QUOTES);
            $randomVC = htmlspecialchars(stripslashes($_SESSION['random']), ENT_QUOTES);
            if($randomV == $randomVC){
                $random = true;
            }
        }

        // process settings changed //
        if (isset($_GET['savesettings'])) {
            if ($_GET['savesettings'] == 'true' && $random) {
                $razorArray['settings']['site-name'] = htmlspecialchars(stripslashes($_POST['sitename']), ENT_QUOTES);
                $razorArray['settings']['site-slogan'] = htmlspecialchars(stripslashes($_POST['siteslogan']), ENT_QUOTES);
                $razorArray['settings']['copyright-footer'] = htmlspecialchars(stripslashes($_POST['copyright']), ENT_QUOTES);
                $razorArray['homepage'] = $_POST['homepage'];
                $razorArray['settings']['charset'] = $_POST['charset'];
                $razorArray['settings']['theme-default'] = $_POST['themedefault'];
                $razorArray['settings']['theme-one'] = $_POST['themeone'];
                $razorArray['settings']['theme-two'] = $_POST['themetwo'];
                $razorArray['settings']['theme-three'] = $_POST['themethree'];
                if(empty($_POST['catselect'])){
                    $result = '';
                } else {
                    $result = '';
                    foreach ($_POST['catselect'] as $key=>$selected) {
                        $result = $result.','.$selected; 
                    }
                }
                $razorArray['settings']['must-have-cats'] = $result;
                saveRazorArray();
                MsgBox(lt('Settings updated successfully').'...', 'greenbox');
            } else {
                die("cannot save settings");
            }
        }
        // end //

        // load settings //
        $siteName = $razorArray['settings']['site-name'];
        $siteSlogan = $razorArray['settings']['site-slogan'];
        $copyrightFooter = $razorArray['settings']['copyright-footer'];
        $charset = 'ISO-8859-1';
        if(isset($razorArray['settings']['charset'])){
            $charset = $razorArray['settings']['charset'];
        }
        $themeDefault = '';
        if(isset($razorArray['settings']['theme-default'])){
            $themeDefault = $razorArray['settings']['theme-default'];
        }
        $themeOne = '';
        if(isset($razorArray['settings']['theme-one'])){
            $themeOne = $razorArray['settings']['theme-one'];
        }
        $themeTwo = '';
        if(isset($razorArray['settings']['theme-two'])){
            $themeTwo = $razorArray['settings']['theme-two'];
        }
        $themeThree = '';
        if(isset($razorArray['settings']['theme-three'])){
            $themeThree = $razorArray['settings']['theme-three'];
        }
        // end //

        // read in catagories protected //
        $readCats = array();
        $selectedCats = array();
        $readCats = $razorArray['links_cats'];
        $selectedCats = explode( ',', $razorArray['settings']['must-have-cats'] );
        if(!empty($readCats)){
            foreach( $readCats as $singleCat=>$contents ) {
                if ( $singleCat != $razorArray['settings']['info-bar-cat'] ) {
                    $checked = '';
                    if (in_array($singleCat, $selectedCats)) {
                        $checked = 'checked';
                    }
                    $outcome = "<input type='checkbox' name='catselect[]' value='$singleCat' $checked ><label for='catselect'>$singleCat</label><br />";
                }
            }
        }
        // end //

        // check if infobar cat is set //
        if ( isset($razorArray['settings']['info-bar-cat']) ) {
            if ( isset($razorArray['links_cats'][$razorArray['settings']['info-bar-cat']]) ) {
                $infobarFlag = true;
            }
        } else {
            $infobarFlag = false;
        }
        // end //
        
        // filter slabs and output homepage radio buttons //
        $slabTitles = $razorArray['titles'];
        $homepage = '';
        foreach($razorArray['slabs'] as $slabID=>$slabName) {
            $checked = '';
            if ( $infobarFlag ) {
                if (  !in_array( $slabID,$razorArray['links_cats'][$razorArray['settings']['info-bar-cat']]) and !in_array( $slabName,array_keys($razorArray['ext_links']))) {
                    if ($slabName == $razorArray['homepage']) {
                        $checked = 'checked';
                    }
                    $homepage.= "<input type='radio' name='homepage' value='$slabName' $checked ><label for='homepage'>".$slabTitles[$slabID]." </label>";  
                }
            } else {
                if ( !in_array( $slabName,array_keys($razorArray['ext_links']))) {
                    $homepage.= "<input type='radio' name='homepage' value='$slabName' $checked ><label for='homepage'>".$slabTitles[$slabID]." </label>";
                    if ($slabName == $razorArray['homepage']) {
                        $checked = 'checked';
                    }
                }
            }
        }
        // end //
        
        // output charset radio buttons //
        $charsets = explode(',',RAZOR_HTML_CHARSETS);
        $htmlCharset = '';
	foreach($charsets as $key=>$type) {
	    if ($type == $charset) {
	        $checked = 'checked';
	    } else {
	        $checked = '';
	    }
	    $htmlCharset.= "<input type='radio' name='charset' value='$type' $checked ><label for='charset'>$type</label>";  
	}
        // end //   

        // output theme radio buttons //
        // get an array of active theme packs //
        $packList = array('razorCMS'=>'razorCMS');
        foreach($razorArray['active-bladepack'] as $activePack) {
            if(file_exists(getSystemRoot(RAZOR_ADMIN_FILENAME).RAZOR_BLADEPACK_DIR.$activePack.'.xml')) {
                $xmlData = file_get_contents(getSystemRoot(RAZOR_ADMIN_FILENAME).RAZOR_BLADEPACK_DIR.$activePack.'.xml');
            	$bpDetails = new BPCONTROL();
	    	if($bpDetails->getXmlData($xmlData, $activePack.'.xml')) {
	    	    // read xml data and remove file
	            $bpDetails->getBPClass();
	            $bpDetails->getBPName();
	            if($bpDetails->className == 'theme'){
	                          //pack ID      //pack name
	                $packList[$activePack] = $bpDetails->name;
	            }
                }
            
            }
        }
        // construct drop down list for choosing themes
        $outThemeDefault = pagesList( 'themedefault',$packList, $themeDefault );
        $outThemeOne = pagesList( 'themeone',$packList, $themeOne );
        $outThemeTwo = pagesList( 'themetwo',$packList, $themeTwo );
        $outThemeThree = pagesList( 'themethree',$packList, $themeThree );
        // end //  

        displaySettings($siteName,$siteSlogan,$copyrightFooter,$outcome,$homepage,$htmlCharset,$outThemeOne,$outThemeTwo,$outThemeThree,$outThemeDefault);
    }
    // end /////////////////////////////////

    // display all settings related info to screen //
    function displaySettings($siteName,$siteSlogan,$copyrightFooter,$outcome,$homepage,$htmlCharset,$outThemeOne,$outThemeTwo,$outThemeThree,$outThemeDefault) {
        // generate random signature for form //
        $random = rand();     
        $_SESSION['random'] = $random;  

        echo "<h1>".lt('Core Settings')."</h1>";
        echo "<div class='contentwh'>";
        echo '<p>'.lt('Below are various core settings, to change the razorCMS core system.').'</p>';
        echo "<form action='?action=coresettings&savesettings=true' method='post'><table class='tableViewSettings'>";
        echo "<input type='hidden' name='random' value='".$random."'>";
        echo "<input type='hidden' name='sitesettings' value='Website Settings'>
                <tr class='tableTitle'><th class='twentyFive'>".lt('Settings')."</th><th class='auto'>".lt('Values')."</th></tr>
                <tr>
                    <td>".lt('Website Name')." </td>
                    <td><input class='w300' type='text' name='sitename' value='$siteName'></td>
                </tr>
                <tr>
                    <td>".lt('Website Slogan')." </td>
                    <td><input class='w300' type='text' name='siteslogan' value='$siteSlogan'></td>
                </tr>
                <tr>
                    <td>".lt('Copyright Footer')." </td>
                    <td><input class='w300' type='text' name='copyright' value='".stripslashes($copyrightFooter)."'></td>
                </tr>
                <tr>
                    <td>".lt('Protect Catagories')." </td>
                    <td>";
        echo $outcome;
        echo "</td></tr>
	         <tr>
	            <td>".lt('Set Homepage')." </td>
	            <td>";
        echo $homepage;
        echo "</td>
                </tr>
                <tr>
                    <td>".lt('HTML Character Set')." </td>
                    <td>";
        echo $htmlCharset;
        echo "</td></tr>
                <tr>
                    <td>".lt('Theme Default')." </td>
                    <td>";
        echo $outThemeDefault;
        echo "</td></tr>
                <tr>
                    <td>".lt('Theme One')." </td>
                    <td>";
        echo $outThemeOne;
        echo "</td></tr>
                <tr>
                    <td>".lt('Theme Two')." </td>
                    <td>";
        echo $outThemeTwo;
        echo "</td></tr>
                <tr>
                    <td>".lt('Theme Three')." </td>
                    <td>";
        echo $outThemeThree;
        echo "</td></tr>
                <tr class='tableFooter'><td class='twentyFive'></td><td class='auto'></td></tr>
            </table>
            <input id='button' type='submit' value='".lt('Save')."' class='floatright'></form></div>";
    }
    // end ////////////////////////////////////////////////////////

    // new function to load blade pack settings //
    function bladeSettings() {
        if($_SESSION['adminType'] == 'user') {
            return;
        }
        echo "<h1>".lt('Various Blades')."</h1>";
        echo "<div class='contentwh'>";
        echo '<p>'.lt('Below are various blade settings without a specific page, grouped by blade pack.').'</p>';
       
        // sockets for processing new settings //
        BsocketB('admin-settings-add1');
        BsocketB('admin-settings-add2');
        BsocketB('admin-settings-add3');
        // end //

        // sockets for displaying new settings //
        BsocketB('admin-settings-display1');
        BsocketB('admin-settings-display2');
        BsocketB('admin-settings-display3');
        // end //

        echo '</div>';
    }
    // end //

    ///////////////////////////////////////////////////////////
    // ######### End of Settings Manager functions ######### //
    ///////////////////////////////////////////////////////////
    
    ///////////////////////////////////////////////////////
    // ############# User Manager functions ############ //
    ///////////////////////////////////////////////////////

    // user management //
    function userManager($who = 'user') {
        if($_SESSION['adminType'] == 'user') {
            if( $who != 'user' ) {
                MsgBox(lt('You do not have privelages to view or alter this account'), 'redbox');
	        return;
	    }
        } elseif($_SESSION['adminType'] == 'admin') {
	    if( $who == 'sadmin' || $who == 'user' ) {
	        MsgBox(lt('You do not have privelages to view or alter this account'), 'redbox');
	        return;
	    }
        }
        
        global $razorArray;
        $userData = array();
        
        // get user data //
        $userData = checkUserType($who);        

        // process settings changed //
        if (isset($_GET['savesettings'])) {
            if ($_GET['savesettings'] == 'true') {
                $passwordFail = false;
                $saveResult = false;
                $usernameFail = false;
                $saveResult2 = false;
                if (isset($_POST['oldAdminPass']) && isset($_POST['newAdminPass']) && isset($_POST['newAdminPassC'])) {
                    if ($_POST['oldAdminPass'] != '' || $_POST['newAdminPass'] != '' || $_POST['newAdminPassC'] != '') {
                        $oldPassword = $_POST['oldAdminPass'];
                        $newPassword = $_POST['newAdminPass'];
                        $newPasswordC = $_POST['newAdminPassC'];
                        $passwordFail = changePasswordCheck($oldPassword, $newPassword, $newPasswordC, $userData);
                        if (!$passwordFail) {
                            $newPassword = createHash($newPassword,NULL,'sha1');
                            $saveResult = savePasswordCheck($newPassword, $userData);
                        }
                    }
                }
                if (isset($_POST['adminUsername'])) {
                    if ($_POST['adminUsername'] != '') {
                        $adminUsername = $_POST['adminUsername'];
                        $usernameFail = changeUserCheck($adminUsername, $userData);
                        if (!$usernameFail) {
                            $saveResult2 = saveUserCheck($adminUsername, $userData);
                            if (!$saveResult2) {
                                $userData['username'] = $adminUsername;
                            }
                        }
                    } else {
                        $usernameFail = lt('Error username cannot be empty').'...';
                    }
                }
                $masterFail = false;
                if ($passwordFail) {
                    MsgBox( lt($passwordFail), 'redbox' );
                    $masterFail = true;
                } elseif ($saveResult) {
                    MsgBox( lt($saveResult), 'redbox' );
                    $masterFail = true;
                }
                if ($usernameFail) {
                    MsgBox( lt($usernameFail), 'redbox' );
                    $masterFail = true;
                } elseif ($saveResult2) {
                    MsgBox( lt($saveResult2), 'redbox' );
                    $masterFail = true;
                }
                if (!$masterFail) {
                    MsgBox(lt('User data updated successfully').'...', 'greenbox');
                }
            } else {
                die("cannot save settings");
            }
        }
        // end //
        displayUserData($userData,$who);
    }
    // end /////////////////////////////////

    // display all user data //
    function displayUserData($userData,$who) {
        $formAction = '';
        if($who == 'user') {
            $formAction = 'action=userdata&savesettings=true';
        } elseif($who == 'admin') {
            $formAction = 'action=admindata&savesettings=true';
        } elseif($who == 'sadmin') {
            $formAction = 'action=sadmindata&savesettings=true';
        }
        echo "<h1>".lt('User Manager')."</h1>";
        echo "<div class='contentwh'>";
        echo '<h2>'.lt($userData['title']).'</h2>';
        echo '<p>'.lt('Alter account for access to the administration panel, you are currently altering the account for ').'<b>'.lt($userData['title']).'</b>.</p>';
        echo "<form action='?".$formAction."' method='post'><table class='tableViewSettings'>";
        echo "<input type='hidden' name='sitesettings' value='Website Settings'>
                <tr class='tableTitle'><th class='twentyFive'>".lt('Settings')."</th><th class='auto'>".lt('Values')."</th></tr>
                <tr><td>".lt('Username')."</td><td><input class='user' type='text' name='adminUsername' value='".$userData['username']."'></td></tr>";
        if($_SESSION['adminType'] != 'sadmin' || $userData['title'] == 'Super Administrator') {
            echo "<tr><td>".lt('Old Password')."</td><td><input class='user' type='password' name='oldAdminPass'></td></tr>";
        } else {
            echo "<tr class='tableTitle'><td>".lt('Super Admin Password')."</td><td><input class='user' type='password' name='oldAdminPass'></td></tr>";
        }        
        echo "<tr><td>".lt('New Password')."</td><td><input class='user' type='password' name='newAdminPass'></td></tr>
                <tr><td>".lt('Confirm New Password')."</td><td><input class='user' type='password' name='newAdminPassC'></td></tr>
                <tr class='tableFooter'><td class='twentyFive'></td><td class='auto'></td></tr>
            </table>
            <input id='button' type='submit' value='".lt('Save')."' class='floatright'></form></div>";
    }
    // end ////////////////////////////////////////////////////////
    
    // new function to select user type //
    function checkUserType($who) {
        $userData = array();
        if($who == 'sadmin') {
            $userData['title'] = 'Super Administrator';
            $userData['finduser'] = 'sadmin_username';
            $userData['findpass'] = 'sadmin_password';
            $userData['username'] = RAZOR_SADMIN_USER;
            $userData['password'] = RAZOR_SADMIN_PASS;
        } elseif($who == 'admin') {
            $userData['title'] = 'Administrator';
            $userData['finduser'] = 'admin_username';
            $userData['findpass'] = 'admin_password';
            $userData['username'] = RAZOR_ADMIN_USER;
            $userData['password'] = RAZOR_ADMIN_PASS;
        } else {
            $userData['title'] = 'User';
            $userData['finduser'] = 'user_username';
            $userData['findpass'] = 'user_password';
            $userData['username'] = RAZOR_USER_USER;
            $userData['password'] = RAZOR_USER_PASS;
        }
        return $userData;
    }
    // end ///////////////////////////////

    // new function to check admin password when changing //
    function changePasswordCheck($oldPassword, $newPassword, $newPasswordC, $userData) {
        if($_SESSION['adminType'] != 'sadmin' || $userData['title'] == 'Super Administrator') {
            if (!isset($oldPassword) || $oldPassword == '') {
                $result = lt('Error old password box is empty').'...';
                return $result;
            }
            $oldPassHash = createHash($oldPassword,substr($userData['password'],0,(strlen($userData['password'])/2)),'sha1');        
            if ($oldPassHash != $userData['password']) {
                $result = lt('Error old password was not validated correctly').'...';
                return $result;
            }
        } else {
            if (!isset($oldPassword) || $oldPassword == '') {
                $result = lt('Error old password box is empty').'...';
                return $result;
            }
            $oldPassHash = createHash($oldPassword,substr(RAZOR_SADMIN_PASS,0,(strlen(RAZOR_SADMIN_PASS)/2)),'sha1');        
            if ($oldPassHash != RAZOR_SADMIN_PASS) {
                $result = lt('Error super admin password was not validated correctly').'...';
                return $result;
            }
        }
        if (!isset($newPassword) || !isset($newPasswordC) || $newPassword == '' || $newPasswordC == '' ) {
            $result = lt('Error a new password box is empty').'...';
            return $result;
        }
        if($newPassword != $newPasswordC) {
            $result = lt('Error new password was not confirmed correctly').'...';
            return $result;
        }
        return false;
    }
    // end //

    // new function to save new admin password when changing //
    function savePasswordCheck($newPassword, $userData) {
        $adminFile = getSystemRoot(RAZOR_ADMIN_FILENAME).'admin/core/admin_config.php';
        $adminProfile = file_get_contents( $adminFile );
        if (isset($adminProfile)) { 
            $adminProfile = str_replace("RAZOR['".$userData['findpass']."'] = '".$userData['password']."'", "RAZOR['".$userData['findpass']."'] = '".$newPassword."'", $adminProfile);
        }
        $result = put2file('admin/core/admin_config.php', $adminProfile);
        if ($result) {
            $result = false;
        } else {
            $result = lt('Error writing data to file').'...';
        }
        return $result;
    }
    // end //

    // new function to check username when changing //
    function changeUserCheck($adminUsername, $userData) {
        if (!isset($adminUsername) || $adminUsername == '') {
            $result = lt('Error username box is empty').'...';
            return $result;
        }
        if (preg_match('/[!$%^&*():;@~#<>?\'"|{}]/', $adminUsername)) {
            $result = lt('Error username contains invalid characters').'...';
            return $result;
        }
        if($userData['finduser'] == 'sadmin_username') {
            if($adminUsername == RAZOR_ADMIN_USER || $adminUsername == RAZOR_USER_USER) {
                $result = lt('Error username already in use in another account').'...';
                return $result;
            }
        } elseif ($userData['finduser'] == 'admin_username') {
            if($adminUsername == RAZOR_SADMIN_USER || $adminUsername == RAZOR_USER_USER) {
	        $result = lt('Error username already in use in another account').'...';
	        return $result;
            }
        } else {
            if($adminUsername == RAZOR_SADMIN_USER || $adminUsername == RAZOR_ADMIN_USER) {
	        $result = lt('Error username already in use in another account').'...';
	        return $result;
            }
        }
        return false;
    }
    // end //

    // new function to save username when changing //
    function saveUserCheck($adminUsername, $userData) {
        $adminFile = getSystemRoot(RAZOR_ADMIN_FILENAME).'admin/core/admin_config.php';
        $adminProfile = file_get_contents( $adminFile );
        if (isset($adminProfile)) { 
            $adminProfile = str_replace("RAZOR['".$userData['finduser']."'] = '".$userData['username']."'", "RAZOR['".$userData['finduser']."'] = '".$adminUsername."'", $adminProfile);
        }
        $result = put2file('admin/core/admin_config.php', $adminProfile);
        if ($result) {
            $result = false;
        } else {
            $result = lt('Error writing data to file').'...';
        }
        return $result;
    }
    // end //

    ///////////////////////////////////////////////////////
    // ######### End of user Manager functions ######### //
    ///////////////////////////////////////////////////////

    ///////////////////////////////////////////////////////////
    // ############## Blade Manager functions ############## //
    ///////////////////////////////////////////////////////////

    // compare function for blade pack xml list //
    function xmlCmp($a, $b) {
        return strcmp($a['title'], $b['title']);
    }
    //////////////////////////////////////////////

    // show blade packs installed //
    function showBladePacks($type) {
        if($_SESSION['adminType'] == 'user') {
            return;
        }
        if(isset($_GET['bladeinfo']) && isset($_GET['blade']) && $_GET['bladeinfo'] == 'show' ){
            $bladeName = stripslashes($_GET['blade']);
            if(file_exists(getSystemRoot(RAZOR_ADMIN_FILENAME).RAZOR_BLADEPACK_DIR.$bladeName.'.xml')) {
                showBladePackInfo($bladeName.'.xml');
            }
        }
        global $bladeList, $razorArray;
        $activeBlades = $razorArray['active-bladepack'];
        $xmlList = array();
        $descTxt = lt('Description');
        $optTxt = lt('Options');
        
        // generate list of xml files and get data //
        $fileList = readDirContents(getSystemRoot(RAZOR_ADMIN_FILENAME).RAZOR_BLADEPACK_DIR);
        foreach($fileList as $foundFile) {
            $getFileExt = explode( '.', basename($foundFile));
            $fileExt = end($getFileExt);
            if($fileExt == 'xml') {
                $xmlData = file_get_contents(getSystemRoot(RAZOR_ADMIN_FILENAME).RAZOR_BLADEPACK_DIR.$foundFile);
	        $bpDetails = new BPCONTROL();
	        if($bpDetails->getXmlData($xmlData, $foundFile)) {
		    // read xml data and remove file
                    $bpDetails->getBPName();
                    $bpDetails->getBPVersion();
                    $bpDetails->getBPDesc();
                    $bpDetails->getBPAuth();
                    $bpDetails->getBPClass();
                    $tempVar = explode( '.', basename($foundFile));
                    $bladeID = reset($tempVar);
                    $xmlList[$bladeID]['title'] = $bpDetails->name;
                    $xmlList[$bladeID]['version'] = $bpDetails->version;
                    $xmlList[$bladeID]['description'] = $bpDetails->description;
                    $xmlList[$bladeID]['author'] = $bpDetails->author;
                    $xmlList[$bladeID]['class'] = $bpDetails->className;
                }
            }
        }

        // process form data //
        if( isset( $_GET['do'] ) ) {
            $do = $_GET['do'];
            $blade = $_GET['blade'];
            if(in_array($blade, array_keys($xmlList))) {
                if( $do == 'activate' && !in_array( $blade, $activeBlades ) ) {
                    $activeBlades[] = $blade;
                    echo MsgBox( lt('Blade Pack Activated'), 'greenbox');
                    $razorArray['active-bladepack'] = $activeBlades;
                    saveRazorArray();
                }
                if( $do == 'deactivate' && in_array( $blade, $activeBlades ) ) {
                    $activeBlades = array_diff( $activeBlades, (array)$blade );
                    echo MsgBox( lt('Blade Pack Deactivated'), 'greenbox' );
                    $razorArray['active-bladepack'] = $activeBlades;
                    saveRazorArray();
                }
                if( $do == 'delete' && $blade) {
                    // remove the bladepack permanently
                    if(file_exists(getSystemRoot(RAZOR_ADMIN_FILENAME).RAZOR_BLADEPACK_DIR.$blade.'.xml')) {
                        $xmlData = file_get_contents(getSystemRoot(RAZOR_ADMIN_FILENAME).RAZOR_BLADEPACK_DIR.$blade.'.xml');
                        $bpRemove = new BPCONTROL();
                        if($bpRemove->getXmlData($xmlData, $blade.'.xml')) {
			    // read xml data and remove file
			    if($bpRemove->getXMLName() && $bpRemove->getBFName()){
			        if(deleteFile(RAZOR_BLADEPACK_DIR.$bpRemove->xmlName ,false) && deleteFile(RAZOR_BLADEPACK_DIR.$bpRemove->bladepackName ,false)) {
                                    if($bpRemove->getDIRName()) {
                                        deleteDirR(RAZOR_BLADEPACK_DIR.$bpRemove->folderName);
			            }
			            // remove from active blades //			            
			            if( in_array( $blade, $activeBlades ) ) {
				        $activeBlades = array_diff( $activeBlades, (array)$blade );
				        $razorArray['active-bladepack'] = $activeBlades;
				        saveRazorArray();
                                    }
                                    MsgBox(lt('Blade pack removed sucessfully'), 'greenbox');
                                } else {
                                    MsgBox(lt('Remove error, all files could not be deleted'), 'redbox');
                                }      
                            } else {
                                MsgBox(lt('Could not remove bladepack, XML data incorrect'), 'redbox');
                            }
                        } else {
                            MsgBox(lt('Could not remove bladepack, could not get XML data'), 'redbox');
                        } 
                    } else {
                        MsgBox(lt('Could not remove bladepack, XML file not found'), 'redbox');
                    }
                }
            }
        }
        // end //
        
        // scrub xml list clean //
        $xmlList = array();
        // re-generate list of xml files and get data //
        $fileList = readDirContents(getSystemRoot(RAZOR_ADMIN_FILENAME).RAZOR_BLADEPACK_DIR);
        foreach($fileList as $foundFile) {
            $getFileExt = explode( '.', basename($foundFile));
            $fileExt = end($getFileExt);
            if($fileExt == 'xml') {
                $xmlData = file_get_contents(getSystemRoot(RAZOR_ADMIN_FILENAME).RAZOR_BLADEPACK_DIR.$foundFile);
	        $bpDetails = new BPCONTROL();
	        if($bpDetails->getXmlData($xmlData, $foundFile)) {
		    // read xml data and remove file
                    $bpDetails->getBPName();
                    $bpDetails->getBPVersion();
                    $bpDetails->getBPDesc();
                    $bpDetails->getBPAuth();
                    $bpDetails->getBPClass();
                    $tempVar = explode( '.', basename($foundFile));
                    $bladeID = reset($tempVar);
                    $xmlList[$bladeID]['title'] = $bpDetails->name;
                    $xmlList[$bladeID]['version'] = $bpDetails->version;
                    $xmlList[$bladeID]['description'] = $bpDetails->description;
                    $xmlList[$bladeID]['author'] = $bpDetails->author;
                    $xmlList[$bladeID]['class'] = $bpDetails->className;
                }
            }
        }
        
        // sort xml data using xml compare function to sort by title //
        uasort($xmlList, "xmlCmp");      

        // output text and table for blades installed //
        $bladeerDescTxt = lt('Blade Packs are sets of blades, each blade can be allocated to a socket to add extra functionality to the base system. The base system is capable of using many blades, even allocating more than one to a single slot. Use the manager to activate and deactivate your blade packs.');
        $delConfirmMsg = lt('Are you sure you want to permanently delete the blade pack from the system').'?';
        BsocketB('output-blade-manager');
        echo "<h1>".lt('Installed Blade Packs')."</h1>";
        echo "<div class='contentwh'>";
        echo "<h3>".lt(ucfirst($type))."</h3>";
        echo "<p>$bladeerDescTxt</p>";
        $t = "<table class='tableViewBlades'>";
        $t.= "<tr class='tableTitle'><th class='twenty'>".lt('Blade Pack')."</th><th class='auto'>$descTxt</th><th class='ten'>".lt('Version')."</th><th class='ten'>".lt('Author')."</th><th class='ten'>".lt('Control')."</th></tr>";
        foreach( $xmlList as $bladeName=>$bladeInfo ) {
            if(strtolower($bladeInfo['class']) == $type) {
                if( in_array( $bladeName , $activeBlades ) ) {
                    $Lable = makeLink( "?action=blade$type&do=deactivate&blade=$bladeName", "<img class='deactivate' src='theme/images/subtract.gif' alt='".lt('Deactivate')."' />" );
                } else {
                    $Lable = makeLink( "?action=blade$type&do=activate&blade=$bladeName", "<img class='activate' src='theme/images/add.gif' alt='".lt('Activate')."' />" );
                }
                $desc = $xmlList[$bladeName][ 'description' ];
                $name = $xmlList[$bladeName][ 'title' ];
                $version = $xmlList[$bladeName][ 'version' ];
                $author = $xmlList[$bladeName][ 'author' ];
                $removePack = makeLink( "?action=blade$type&do=delete&blade=$bladeName", "<img class='deactivate' src='theme/images/trash.gif' alt='".lt('Delete')."' onclick='return confirm(\"$delConfirmMsg\");'/>" );
                $info = makeLink( "?action=blade$type&bladeinfo=show&blade=$bladeName", "<img class='edit' src='theme/images/alert.gif' alt='".lt('Info')."'/>" );
                $t.= "<tr><td>$name</td><td>$desc</td><td>$version</td><td>$author</td><td>$Lable $removePack $info</td></tr>";
            }
        }
        $t.= "<tr class='tableFooter'><th class='twenty'></th><th class='auto'></th><th class='ten'></th><th class='ten'></th><th class='ten'></th></tr></table></div>";
        echo $t;
        // end //
    }
    // end /////////////////////////

    // show install information //
    function showBladePackInfo($filename) {
        $xmlData = file_get_contents(getSystemRoot(RAZOR_ADMIN_FILENAME).RAZOR_BLADEPACK_DIR.$filename);
	$bpDetails = new BPCONTROL();
	if($bpDetails->getXmlData($xmlData, $filename)) {
            if($bpDetails->searchXmlArray($bpDetails->xmlContents, 'note')) {
                MsgBox(lt('PLEASE READ SPECIAL NOTES FOR BLADE PACK'), 'yellowbox');
                $sentance = '';
                $noteArray = array();
                $noteArray = $bpDetails->searchXmlArray($bpDetails->xmlContents, 'note');
                foreach($noteArray['child'] as $paras) {
                    $sentance.= '<'.$paras['tag'].'>'.$paras['value'].'</'.$paras['tag'].'>';
                }
                MsgBox($sentance, 'yellowbox');
            }
        }
    }
    // end ///////////////////////

    // bladepack installer function //
    function bladepackInstall() {
        if($_SESSION['adminType'] == 'user') {
            return;
        }
        $startInstall = false;
        if(isset($_GET['startinstall']) && $_GET['startinstall']) {
            $startInstall = true;
        }
        if($startInstall){
            $filename = basename($_FILES['file-upload']['name']);
            $stripFileName = explode( '.', $filename);
            if ( end($stripFileName) == 'zip' ) {
                $bladepacksDir = getSystemRoot(RAZOR_ADMIN_FILENAME).RAZOR_BLADEPACK_DIR;
                $bladepackFiles = readDirContents($bladepacksDir);
                $fileExists = false;
                foreach($bladepackFiles as $fileCheck) {
                    $fileCheckT = explode( '.', $fileCheck);
                    $filenameT = explode( '.', $filename);
                    if (reset($fileCheckT) == reset($filenameT)) {
                        $fileExists = true;
                    }
                }
                if (!$fileExists) {
                    uploadFile(RAZOR_BLADEPACK_DIR.$filename, $_FILES['file-upload']['tmp_name']);
                    $bpInstall = new BPCONTROL();
                    $bpInstall->extractContents(RAZOR_BLADEPACK_DIR.$filename);
                    if ($bpInstall->checkContents(RAZOR_BLADEPACK_DIR)) {
                        if($bpInstall->extractXmlData()) {
                            $bpInstall->saveContents(RAZOR_BLADEPACK_DIR);
                            MsgBox(lt('Bladepack installed successfully'), 'greenbox');
                            if($bpInstall->searchXmlArray($bpInstall->xmlContents, 'note')) {
                                MsgBox(lt('PLEASE READ SPECIAL NOTES FOR BLADE PACK'), 'yellowbox');
                                $sentance = '';
                                $noteArray = array();
                                $noteArray = $bpInstall->searchXmlArray($bpInstall->xmlContents, 'note');
                                foreach($noteArray['child'] as $paras) {
                                    $sentance.= '<'.$paras['tag'].'>'.$paras['value'].'</'.$paras['tag'].'>';
                                }
                                MsgBox($sentance, 'yellowbox');
                            }
                            deleteFile(RAZOR_BLADEPACK_DIR.$filename, false);
                        } else {
                            MsgBox(lt('Error installing bladepack, cannot parse xml, attempting to clean up install file'), 'redbox');
                            deleteFile(RAZOR_BLADEPACK_DIR.$filename);
                        }
                    } else {
                        MsgBox(lt('Error installing bladepack, unpack error, attempting to clean up install file'), 'redbox');
                        deleteFile(RAZOR_BLADEPACK_DIR.$filename);
                    }
                } else {
                    MsgBox(lt('Error installing bladepack, bladepack already present'), 'redbox');
                }
            } else {
                MsgBox( lt("Error installing bladepack, only bladepack parcels allowed"), 'redbox' );
            }
        }
        echo "<h1>".lt('Install Blade Packs')."</h1>";
        echo "<div class='contentwh'>";
        echo "<h3>".lt('Upload blade pack parcel')."</h3>";
        echo '<p>'.lt('Please only upload verified blade pack parcel files downloaded from the official razorCMS website. All blade pack parcels are distributed in zip archive format').'.</p>';
        echo '<form enctype="multipart/form-data" action="?action=bladeinstall&startinstall=true" method="POST">';
        echo "<table class='tableViewBlades'>";
        echo "<tr class='tableFooter'><th class='auto'></th><th class='ten'></th></tr>";
        echo '<tr><td><input name="file-upload" type="file" /></td><td><input id="button" type="submit" value="'.lt('upload file').'" name="upload"/></td></tr>';
        echo "<tr class='tableFooter'><th class='twentyFive'></th><th class='auto'></th></tr></table></form></div>";

    }
    // end ///////////////////////////

    ///////////////////////////////////////////////////////////
    // ########### End of Blade Manager functions ########## //
    ///////////////////////////////////////////////////////////
?>
