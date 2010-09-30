<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php loadSettings('sitename'); ?> &raquo; <?php loadPageTitle(); ?></title>
<?php BsocketB('public-xhtml-head1'); ?>
<link href="<?php cssLocation(); ?>" rel="stylesheet" type="text/css" />
<?php BsocketB('public-xhtml-head2'); ?>
</head>

<body>
    <div id="wrap">
        <div id="header">
            <h1 class="Logo"><?php loadSettings('sitename'); ?><span><?php loadSettings('siteslogan'); ?></span><span>
            <?php BsocketB('public-xhtml-header'); ?></span></h1>
            <div id="menu">
                <?php loadLinks('top-navigation'); ?>
                <?php BsocketB('public-xhtml-topnav'); ?>
            </div>
        </div>
        <div id="contents">
            <div id="main">
                <?php loadSlabContents(); ?>
                <?php BsocketB('public-xhtml-content'); ?>
            </div>
            <div id="sidebar">
                <?php loadLinks('sidebar'); ?>
                <?php BsocketB('public-xhtml-leftnav'); ?>
                <?php loadInfoContents(); ?>
                <?php BsocketB('public-xhtml-leftbar'); ?> 
            </div>
            <div class="clear">
            </div>
        </div>
        <div id="bottom">
            <?php loadLinks('footer'); ?>
            <?php loadSettings('copyright'); ?>
            <?php BsocketB('public-xhtml-footer'); ?><br />
        </div>
        <div id="footer"><a href="http://www.ramblingsoul.com">CSS Template</a> by Rambling Soul<br />
	<a href="http://www.razorcms.co.uk">razorCMS : flat file CMS</a></div>
    </div>
<?php BsocketB('public-xhtml-endofdoc'); ?> 
</body>
</html>