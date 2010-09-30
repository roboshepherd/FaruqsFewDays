<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title><?php loadSettings('sitename'); ?> &raquo; <?php loadPageTitle(); ?></title>
    <meta http-equiv="Content-Type" content="application/xhtml+xml; charset=iso-8859-1" />
    <?php BsocketB('public-xhtml-head1'); ?>
    <link rel="stylesheet" type="text/css" href="<?php cssLocation(); ?>" />
    <?php BsocketB('public-xhtml-head2'); ?>
</head>
<body>
    <div id="pageframe">
        <div id="header">
            <h1><?php loadSettings('sitename'); ?></h1>
            <h2><?php loadSettings('siteslogan'); ?></h2>
	    <?php BsocketB('public-xhtml-header'); ?>
        </div>
        <div id="topnav">
            <?php loadLinks('top-navigation'); ?>
	    <?php BsocketB('public-xhtml-topnav'); ?>
        </div>
        <div id="midbox">
            <div id="content">
                <?php loadSlabContents(); ?>
                <?php BsocketB('public-xhtml-content'); ?>
            </div>
            <div id="leftbar">
                <div id="leftnav">
                    <?php loadLinks('sidebar'); ?>
		    <?php BsocketB('public-xhtml-leftnav'); ?>
                </div>
		<?php loadInfoContents(); ?>
		<?php BsocketB('public-xhtml-leftbar'); ?>            
            </div>
        </div>
        <div id="footer">  
        <div id="footnav">
            <?php loadLinks('footer'); ?>
            <?php loadSettings('copyright'); ?>
            <?php BsocketB('public-xhtml-footer'); ?>
        </div>
        </div>

    </div>
<?php BsocketB('public-xhtml-endofdoc'); ?>  
</body>
</html>

