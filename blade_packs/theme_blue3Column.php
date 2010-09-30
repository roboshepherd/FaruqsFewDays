<?php
//////////////////////////////////////////
// Blade Pack Theme                     //
// blue3column by Minimalistic Design   //
// www.minimalistic-design.net          //
// Free (?)                             //
// 01-09-08                             //
// ported by Netbox AS                  //
//////////////////////////////////////////

///////////////////////
// Socket Allocation //
///////////////////////

// Add Blades to Sockets $bladeList['blade'] = 'socket'; //
$bladeList['blue3ColumnXHTML'] = 'public-change-theme';
$bladeList['blue3ColumnCSS'] = 'public-css-address';
$bladeList['blue3ColumnEditorCSS'] = 'editor-css-path';

///////////////////////
//      Blades       //
///////////////////////

// blade - load new xhtml template //
function blue3ColumnXHTML(&$xhtmlTemplate) {
    $xhtmlTemplate = 'blade_packs/theme_blue3Column/blue3Column_xhtml.php';
}
// end ///////////////////////////////

// blade - load new css template //
function blue3ColumnCSS(&$cssTemplate) {
    $cssTemplate = 'blade_packs/theme_blue3Column/blue3Column_css.css';
}
// end ///////////////////////////////

// blade - load editor css template //
function blue3ColumnEditorCSS(&$cssfile) {
    $cssfile = 'blade_packs/theme_blue3Column/editor_css.css';
}
// end ///////////////////////////////

?>