<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=iso-8859-1" />
<title><?php loadSettings('sitename'); ?> &raquo; <?php loadPageTitle(); ?></title>
<?php BsocketB('public-xhtml-head1'); ?>
<link href="<?php cssLocation(); ?>" rel="stylesheet" type="text/css" />
<?php BsocketB('public-xhtml-head2'); ?>
<!--[if IE 7]>
  <style>
  /*<![CDATA[*/
#sitename {
	display:block;
	padding: 32px 55px 0 45px;
	color: #808040;
	font: bolder small-caps 22px "Trebuchet MS", Verdana, sans-serif;
	text-align:right;
}
  /*]]>*/
  </style>
<![endif]-->
</head>

<body>
<div id="wrap">
<div id="header">
<div id="topmenu">
<?php loadLinks('top-navigation'); ?>
<?php BsocketB('public-xhtml-topnav'); ?>
</div>
<div class="clear"></div>
<h1><?php loadSettings('sitename'); ?></h1>
<h2><?php loadSettings('siteslogan'); ?></h2>
<?php BsocketB('public-xhtml-header'); ?>

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
<div class="clear">

</div>
<div id="footer">
<div id="btm_cont">


</div>
<div id="ft_btm">            <?php loadLinks('footer'); ?>
            <?php loadSettings('copyright'); ?>
            <?php BsocketB('public-xhtml-footer'); ?><br />
<!--Credits -->
<a href="http://ramblingsoul.com">CSS Template</a> by Rambling Soul<br />
Images from<a href="http://sxc.hu"> sxc.hu</a>
<!--/Credits -->


</div>

</div>

</div>
<?php BsocketB('public-xhtml-endofdoc'); ?>  
</body>
</html>
