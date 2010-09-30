<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title><?php loadSettings('sitename'); ?> &raquo; <?php loadPageTitle(); ?></title>
    <?php BsocketB('public-xhtml-head1'); ?>
    <link rel="stylesheet" type="text/css" href="<?php cssLocation(); ?>" />
    <link rel="shortcut icon" href="theme/images/favicon.ico" type="image/x-icon"/>
    <?php BsocketB('public-xhtml-head2'); ?>
    <script type="text/javascript" src="<?php scriptPath(); ?>navbar.js"></script>
</head>
<body>
    <div id="brace">
    <div id="pageframe">
    <div id="pageframer">
        <div id="headermid">
        <div id="headerr">
        <div id="header">
            <h1><?php loadSettings('sitename'); ?></h1>
            <h2><?php loadSettings('siteslogan'); ?></h2>
	    <?php BsocketB('public-xhtml-header'); ?>
        </div>
        </div>
        </div>
        <div id="midbrace">
            <div id="topnav">
                <?php loadLinks('top-navigation'); ?>
                <?php BsocketB('public-xhtml-topnav'); ?>
            </div>
            <div id="midbox">
                <div id="leftbar">
                    <div id="leftnav">
                        <?php loadLinks('sidebar'); ?>
                        <?php BsocketB('public-xhtml-leftnav'); ?>
                    </div>
                    <?php loadInfoContents(); ?>
                    <?php BsocketB('public-xhtml-leftbar'); ?>            
                </div>
                <div id="content">
                    <div class="contentwh">
                        <?php loadSlabContents(); ?>
                    </div>
                    <?php BsocketB('public-xhtml-content'); ?>
                </div>
            </div>
            <div id="copyw">
                <div id="copynav">
                    <?php loadLinks('footer'); ?>
                </div>
                <?php loadSettings('copyright'); ?>
                <?php BsocketB('public-xhtml-footer'); ?>
            </div>  
        </div>
        <div id="footer">
        <div id="footerr">
        <div id="footerl">
        <div id="footnav">
            <div id="footerLeft">
                <a href="http://www.razorcms.co.uk">www.razorcms.co.uk</a>
            </div>
            <div id="footerRight">
                <a href="http://www.mis-limited.com">www.mis-limited.com</a>
            </div>
        </div>
        </div>
        </div>
        </div>
    </div>
    </div>
<?php BsocketB('public-xhtml-endofdoc'); ?>  
</body>
</html>

