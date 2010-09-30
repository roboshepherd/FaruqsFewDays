<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php loadSettings('sitename'); ?> &#126; <?php loadPageTitle(); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <?php BsocketB('public-xhtml-head1'); ?>
    <link rel="stylesheet" type="text/css" href="<?php cssLocation(); ?>" />
    <?php BsocketB('public-xhtml-head2'); ?>

</head>
<body>
<div id="wrap">

<div id="header">
<h1><a href="<?php echo $_SESSION['PHP_SELF']; ?>"><?php loadSettings('sitename'); ?></a></h1>
<h2><?php loadSettings('siteslogan'); ?></h2>
</div>

<div id="menu">
	<?php loadLinks('top-navigation'); ?>
	<?php BsocketB('public-xhtml-topnav'); ?>
</div>

<div id="content">

<div id="left">
<h2>Categories : </h2>
<div class="box">
	<?php loadLinks('sidebar'); ?>
	<?php BsocketB('public-xhtml-leftnav'); ?>
</div>
</div>

<div id="right">

<div class="contentleft">
<div class="contentleftbox">
	<?php loadSlabContents(); ?>
	<?php BsocketB('public-xhtml-content'); ?>

</div>
</div>

<div class="contentright">
<h2>Nyheter</h2>
<div class="contentrightbox">
	<?php loadInfoContents(); ?>
	<?php BsocketB('public-xhtml-leftbar'); ?>            
</div>
</div>

</div>

</div>

<div style="clear: both;"> </div>

<div id="footer">
                <?php loadSettings('copyright'); ?>
                <?php BsocketB('public-xhtml-footer'); ?> 
| Design by <a href="http://www.minimalistic-design.net">Minimalistic Design</a> 
| Adapted for <a href="http://razorcms.co.uk/">razorCMS</a> by Netbox AS <a href="http://netbox.no/">Webhotell</a></div>

</div>
<?php BsocketB('public-xhtml-endofdoc'); ?>  
</body>
</html>