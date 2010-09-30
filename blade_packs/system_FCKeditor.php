<?php
///////////////////////
// ADMIN BLADEPACK   //
// FCKeditor         //
// razorCMS          //
// GPLv3             //
// Version 0.4       //
// 08-08-08          //
// smiffy6969        //
///////////////////////

///////////////////////
// Socket Allocation //
///////////////////////

// Add Blades to Sockets $bladeList['fckeditor'] = 'editor'; //
$bladeList['fckeditor'] = 'editor';

///////////////////////
//      Blades       //
///////////////////////

// blade - start editor //
function fckeditor(&$te) {
    if($_SESSION['adminLogIn']) {
        include_once("../blade_packs/system_FCKeditor/fckeditor.php") ;
        $editorCSSPath = "../../../";
        $editorCSS = "theme/editor_css.css";
        BsocketB( 'editor-css-path' , array( &$editorCSS ) );
        $oFCKeditor = new FCKeditor('content') ;
        $oFCKeditor->Config['EditorAreaCSS'] = $editorCSSPath.$editorCSS;
        $oFCKeditor->BasePath = "../blade_packs/system_FCKeditor/";
        $oFCKeditor->Value = "$te[4]" ;
        $oFCKeditor->Height = '600' ;
        $oFCKeditor->Create() ;
        $te[2] = '';
    }
}
// end ///////////////////

?>
