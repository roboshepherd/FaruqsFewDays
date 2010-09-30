<?php
/////////////////////////////////////////
// Blade Pack Theme                    //
// Imagination by ramblingsoul         //
// www.ramblingsoul.com                //
// Creative Communs v2.5               //
// Version 1.0                         //
// 09-09-09                            //
// ported by smiffy6969                //
/////////////////////////////////////////

///////////////////////
// Socket Allocation //
///////////////////////

// Add Blades to Sockets $bladeList['blade'] = 'socket'; //
$bladeList['theme_imaginationXHTML'] = 'public-change-theme';
$bladeList['theme_imaginationCSS'] = 'public-css-address';
$bladeList['theme_imaginationEditorCSS'] = 'editor-css-path';

///////////////////////
//      Blades       //
///////////////////////

// blade - load new xhtml template //
function theme_imaginationXHTML(&$xhtmlTemplate) {
    $xhtmlTemplate = RAZOR_BLADEPACK_DIR.'theme_imagination/imagination_xhtml.php';
}
// end ///////////////////////////////

// blade - load new css template //
function theme_imaginationCSS(&$cssTemplate) {
    $cssTemplate = RAZOR_BLADEPACK_DIR.'theme_imagination/imagination_css.css';
}
// end ///////////////////////////////

// blade - load editor css template //
function theme_imaginationEditorCSS(&$cssfile) {
    $cssfile = RAZOR_BLADEPACK_DIR.'theme_imagination/imaginationEditor_css.css';
}
// end ///////////////////////////////

?>