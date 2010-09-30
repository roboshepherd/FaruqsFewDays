<?php
/////////////////////////////////////
// Blade Pack Theme                //
// Inspire by Payal Dhar           //
// 16-09-08                        //
// ported by rincerofwinds         //
/////////////////////////////////////

///////////////////////
// Socket Allocation //
///////////////////////

// Add Blades to Sockets $bladeList['blade'] = 'socket'; //
$bladeList['inspireHTML'] = 'public-change-theme';
$bladeList['inspireCSS'] = 'public-css-address';
$bladeList['inspireEditorCSS'] = 'editor-css-path';

///////////////////////
//      Blades       //
///////////////////////

// blade - load new html template //
function inspireHTML(&$htmlTemplate) {
    $htmlTemplate = 'blade_packs/theme_inspire/inspire_html.php';
}
// end ///////////////////////////////

// blade - load new css template //
function inspireCSS(&$cssTemplate) {
    $cssTemplate = 'blade_packs/theme_inspire/inspire_css.css';
}
// end ///////////////////////////////

// blade - load editor css template //
function inspireEditorCSS(&$cssfile) {
    $cssfile = 'blade_packs/theme_inspire/editor_css.css';
}
// end ///////////////////////////////

?>