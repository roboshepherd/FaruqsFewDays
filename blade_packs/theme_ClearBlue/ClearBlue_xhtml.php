<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title><?php loadSettings('sitename'); ?> &#126; <?php loadPageTitle(); ?></title>
    <meta http-equiv="Content-Type" content="application/xhtml+xml; charset=iso-8859-1" />
    <?php BsocketB('public-xhtml-head1'); ?>
    <link rel="stylesheet" type="text/css" href="<?php cssLocation(); ?>" />
    <!-- WIN IE Style Sheets -->
<!--[if IE]>
  <![if gte IE 5.5]>
   <![if gte IE 7]><link rel="stylesheet" 
	type="text/css" media="screen,projection" 
	href="ie.css" />
	<![endif]>
   <![if lt IE 7]><link rel="stylesheet" 
	type="text/css" media="screen,projection" 
	href="ie.css" />
	<![endif]>
  <![endif]>
  <![if lt IE 5.5]>
   <link rel="stylesheet"
	type="text/css" media="screen,projection" 
	href="ie.css" />
    <![endif]>
<![endif]-->
    <?php BsocketB('public-xhtml-head2'); ?>
    <link rel="shortcut icon" href="favicon.ico" />
</head>
<body>
<div id="wrap">
  <div id="wrap2">
    <div id="header">
      <h1 id="logo"><?php loadSettings('sitename'); ?></h1>
            <div id="slogan"><?php loadSettings('siteslogan'); ?></div>
	    <?php BsocketB('public-xhtml-header'); ?>
    </div>
    <div id="nav">
      <div id="nbar">
            <?php loadLinks('top-navigation'); ?>
	    <?php BsocketB('public-xhtml-topnav'); ?>
         </div>
    </div>
    <div id="content-wrap">
      <div id="sidebar">
<!--      <div id="right_sidebar">
          <div id="side_sky_ad"> -->
          	<!-- Sidebar Sky Scrapper Ad -->
            <!-- Remove all the below contents until </div> and place with 160 x 600 ad code -->
<!--		<?php loadInfoContents(); ?> 
		<?php BsocketB('public-xhtml-leftbar'); ?>  
          </div>
        </div>
-->        
      <div id="left_sidebar">
        <div class="widgetspace">
                    <?php loadLinks('sidebar'); ?>
<!--		    <?php BsocketB('public-xhtml-leftnav'); ?> -->
          </div>
          </div>
      </div>
      <div id="content">
                <?php loadSlabContents(); ?>
                <?php BsocketB('public-xhtml-content'); ?>
      </div>
      </div>
    
    <div class="clearfix"></div>
    <div id="footer">                 
                <?php loadLinks('footer'); ?>
                <?php loadSettings('copyright'); ?>
                <?php BsocketB('public-xhtml-footer'); ?>
                | <a href="http://ril.newport.ac.uk/sarker">Home</a>|
  </div>
  <!-- End Wrap2 -->
</div>
<!-- End Wrap -->
<?php BsocketB('public-xhtml-endofdoc'); ?>
</body>
</html>
