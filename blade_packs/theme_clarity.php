<?php
////////////////////////
// Blade Pack Theme   //
// Clarity            //
// razorCMS           //
// Version 0.2        //
// GPLv3              //
// 03-05-08           //
// smiffy6969         //
////////////////////////

///////////////////////
// Socket Allocation //
///////////////////////

// Add Blades to Sockets $bladeList['blade'] = 'socket'; //
$bladeList['clarityXHTML'] = 'public-change-theme';
$bladeList['clarityCSS'] = 'public-css-address';
$bladeList['clarityEditorCSS'] = 'editor-css-path';

///////////////////////
//      Blades       //
///////////////////////

// blade - load new xhtml template //
function clarityXHTML(&$xhtmlTemplate) {
    $xhtmlTemplate = 'blade_packs/theme_clarity/clarity_xhtml.php';
}
// end ///////////////////////////////

// blade - load new css template //
function clarityCSS(&$cssTemplate) {
    $cssTemplate = 'blade_packs/theme_clarity/clarity_css.css';
}
// end ///////////////////////////////

// blade - load editor css template //
function clarityEditorCSS(&$cssfile) {
    $cssfile = 'blade_packs/theme_clarity/editor_css.css';
}
// end ///////////////////////////////

?>