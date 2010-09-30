<?php
///////////////////////////////////////////////////////////
// morgan integrated systems limited - razorCMS          //
// Blade Pack                                            //
// NicEdit                                               //
// GPLv3                                                 //
// Version 1.0                                           //
// smiffy6969                                            //
// www.razorcms.co.uk                                    //
// www.mis-limited.com                                   //
// 06/2009                                               //
///////////////////////////////////////////////////////////

///////////////////////
// Socket Allocation //
///////////////////////

// Add Blades to Sockets $bladeList['blade'] = 'socket'; //
$bladeList['adminHeadnicEdit'] = 'admin-xhtml-head';
$bladeList['nicEdit'] = 'editor';

///////////////////////
//      Blades       //
///////////////////////

// blade - add script link to admin xhtml head //
function adminHeadnicEdit() {
    if($_SESSION['adminLogIn']) {
        echo '<script src="../blade_packs/system_NicEdit/nicEdit.js" type="text/javascript"></script>';
    }
}

// blade - start editor //
function nicEdit(&$te) {
    if($_SESSION['adminLogIn']) {
?>
        <script type="text/javascript">
        bkLib.onDomLoaded(function() {
            new nicEditor({fullPanel : true, iconsPath : '../blade_packs/system_NicEdit/nicEditorIcons.gif'}).panelInstance('editbox');;
        });
        </script>
<?php
        $te[2] = '<textarea style="width: 700px;" id="editbox" name="content">'.$te[4].'</textarea>';
    }
}
// end ///////////////////

?>