<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title><?php loadSettings('sitename'); ?> - <?php loadPageTitle(); ?></title>
  <?php BsocketB('public-xhtml-head1'); ?>
  <link href="<?php cssLocation(); ?>" rel="stylesheet" type="text/css" />
  <?php BsocketB('public-xhtml-head2'); ?>
</head>

<body>
    <div id="topbar">
        <div id="TopSection">
            <h1 id="sitename"><?php loadSettings('sitename'); ?></h1>
            <div id="topbarnav">
                <div class="searchform">
                    <?php BsocketB('public-xhtml-header'); ?>
                </div>
            </div>
            <div class="clear">
            </div>
            <div id="topmenu">
                    <?php loadLinks('top-navigation'); ?>
                    <?php BsocketB('public-xhtml-topnav'); ?>
            </div>
        </div>
    </div>

    <div id="wrap">
        <div id="header">
            <h2 class="introtext"><?php loadSettings('siteslogan'); ?></h2>
        </div>
        <div id="contents">
            <div class="clear">
            </div>
            <div id="aboutdiv">
                <?php loadLinks('sidebar'); ?>
		<?php BsocketB('public-xhtml-leftnav'); ?>
            </div>
            <div id="highlights">
                <?php loadInfoContents(); ?>
                <?php BsocketB('public-xhtml-leftbar'); ?> 
            </div>
            <div id="homecontents"> 
                <?php loadSlabContents(); ?>
                <?php BsocketB('public-xhtml-content'); ?>
            </div>
            <div class="clear">
            </div>
        </div>
    </div>
    
    <div id="footer">
        <div id="footercontent">
                <?php loadLinks('footer'); ?>
            <div id="copyright">
                <p><?php loadSettings('copyright'); ?></p>
                <?php BsocketB('public-xhtml-footer'); ?> 
            </div>
        </div>
    </div>
    <div id="credit">
        <a title="Free Css Templates" href="http://www.ramblingsoul.com">CSS Template</a> by Rambling Soul | Valid <a href="http://validator.w3.org/check?uri=referer">XHTML 1.0</a> | <a href="http://validator.w3.org/check?uri=referer&quot;">CSS 2.0</a>
    </div>
    <?php BsocketB('public-xhtml-endofdoc'); ?>
</body>
</html>
