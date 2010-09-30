<?php
/////////////////////////////////////////
// Blade Pack Theme                    //
// Colours4 (colors-4) by ramblingsoul //
// www.ramblingsoul.com                //
// Creative Communs v2.5               //
// Version 0.2                         //
// 15-06-08                            //
// ported by smiffy6969                //
/////////////////////////////////////////

///////////////////////
// Socket Allocation //
///////////////////////

// Add Blades to Sockets $bladeList['blade'] = 'socket'; //
$bladeList['colours4XHTML'] = 'public-change-theme';
$bladeList['colours4CSS'] = 'public-css-address';
$bladeList['colours4EditorCSS'] = 'editor-css-path';

///////////////////////
//      Blades       //
///////////////////////

// blade - load new xhtml template //
function colours4XHTML(&$xhtmlTemplate) {
    $xhtmlTemplate = 'blade_packs/theme_colours4/colours4_xhtml.php';
}
// end ///////////////////////////////

// blade - load new css template //
function colours4CSS(&$cssTemplate) {
    $cssTemplate = 'blade_packs/theme_colours4/colours4_css.css';
}
// end ///////////////////////////////

// blade - load editor css template //
function colours4EditorCSS(&$cssfile) {
    $cssfile = 'blade_packs/theme_colours4/editor_css.css';
}
// end ///////////////////////////////

?>