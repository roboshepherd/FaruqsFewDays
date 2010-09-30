<?php
// check for admin access to this function library // 
if( !($_SESSION['adminLogIn'] ) ) {
    die("Access Denied");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title><?php loadSettings('sitename'); ?> - ADMIN</title>
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <link rel="stylesheet" type="text/css" href="theme/default_css.css" />
    <link rel="shortcut icon" href="theme/images/favicon.ico" type="image/x-icon"/>
    <?php BsocketB('admin-xhtml-head'); ?>
</head>
<body>
    <div id="brace">
    <div id="pageframe">
    <div id="pageframer">
        <div id="headermid">
        <div id="headerr">
        <div id="header">
            <h1>razorCMS <span class='redtext'><?php if(isset($razorArray['settings']['maintenance']) && $razorArray['settings']['maintenance'] == true) echo lt('MAINTENANCE MODE'); ?></span></h1>
            <h2><?php echo lt('Administration Console'); ?></h2>
            <h3><?php loggedInAs(); ?></h3>
    	    <?php BsocketB('admin-xhtml-header'); ?>
        </div>
        </div>
        </div>
            <div id="topnav">
                <?php loadAdminTopLinks(); ?>
    	        <?php BsocketB('admin-xhtml-topnav'); ?>
                <div class="viewsitelink">
                    <a href="?action=maintenance" title="<?php echo lt('Maintenance Mode'); ?>" ><img class='edit' src='theme/images/spanner.gif' alt="<?php echo lt('Maintenance Mode'); ?>" onclick='return confirm("<?php echo lt("Are you sure you want to switch maintenance mode?"); ?>");' /></a> 
                    <a href="../" title="<?php echo lt('View Site'); ?>" target="_blank"><img class='updown' src='theme/images/window.gif' alt="<?php echo lt('View Site'); ?>" /></a> 
                    <a href="?logout" title="<?php echo lt('Logout'); ?>"><img class='delete' src='theme/images/trash.gif' alt="<?php echo lt('Logout'); ?>" <?php if(isset($razorArray['settings']['maintenance']) && $razorArray['settings']['maintenance'] == true) echo "onclick='return confirm(\"".lt("You are in maintenance mode, are you sure you want to log out in maintenance mode")."\");'"; ?> /></a>
                </div>
            </div>
        <div id="midbrace">
            <div id="midbox">
                <div id="leftbar">
                    <div id="leftnav">
                        <?php loadAdminSubLinks(); ?>
                        <?php BsocketB('admin-xhtml-leftnav'); ?>
                    </div>
                    <?php BsocketB('admin-xhtml-leftbar'); ?>
                </div>
                <div id="content">
                    <?php adminFuncSwitch(); ?>
		    <?php BsocketB('admin-xhtml-content'); ?>
                </div>
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
	    <?php BsocketB('admin-xhtml-footer'); ?>
        </div>  
        </div>
        </div>
        </div>
    </div>
    </div>
    </div>
<?php BsocketB('admin-xhtml-endofdoc'); ?>  
</body>
</html>

