<?php
//////////////////////////////////
// Blade Pack Theme             //
// ClearBlue                    //
// http://www.themebin.com/     //
// Free                         //
// 05 Aug 2009                  //
// Pete Jones                   //
//////////////////////////////////

///////////////////////
// Socket Allocation //
///////////////////////

// Add Blades to Sockets $bladeList['blade'] = 'socket'; //
$bladeList['ClearBlueXHTML'] = 'public-change-theme';
$bladeList['ClearBlueCSS'] = 'public-css-address';
$bladeList['ClearBlueEdCSS'] = 'editor-css-path';

///////////////////////
//      Blades       //
///////////////////////

// blade - load new xhtml template //
function ClearBlueXHTML(&$xhtmlTemplate) {
    $xhtmlTemplate = 'blade_packs/theme_ClearBlue/ClearBlue_xhtml.php';
}
// end ///////////////////////////////

// blade - load new css template //
function ClearBlueCSS(&$cssTemplate) {
    $cssTemplate = 'blade_packs/theme_ClearBlue/ClearBlue_css.css';
}
// end ///////////////////////////////

// blade - load editor css template //
function ClearBlueEdCSS(&$cssfile) {
    $cssfile = 'blade_packs/theme_ClearBlue/editor_css.css';
}
// end ///////////////////////////////

?>