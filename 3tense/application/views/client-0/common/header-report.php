<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo $title; ?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="3Tense">
	<meta name="author" content="Aakarsoft">

    <!--<link href="<?php /*echo base_url(); */?>assets/users/less/styles.less" rel="stylesheet/less" media="all">-->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/users/css/styles.css?=140">
    <!-- <link rel="stylesheet" href="assets/css/styles.css?=121"> -->
    <link href='https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600' rel='stylesheet' type='text/css'>

	<?php if (isset($_COOKIE["theme"])) {
	    echo "<link href='.base_url().'assets/users/demo/variations/". $_COOKIE["theme"] ." rel='stylesheet' type='text/css' media='all' id='styleswitcher'>";
	} else { ?>
        <link href='<?php echo base_url(); ?>assets/users/demo/variations/default.css' rel='stylesheet' type='text/css' media='all' id='styleswitcher'>
    <?php } ?>

    <?php if (isset($_COOKIE["headerstyle"])) {
        echo "<link href='.base_url().'assets/users/demo/variations/". $_COOKIE["headerstyle"] ."' rel='stylesheet' type='text/css' media='all' id='headerswitcher'>";
    } else { ?>
        <link href='<?php echo base_url(); ?>assets/users/demo/variations/default.css' rel='stylesheet' type='text/css' media='all' id='headerswitcher'>
    <?php } ?>

	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries. Placeholdr.js enables the placeholder attribute -->
	<!--[if lt IE 9]>
        <link rel="stylesheet" href= "<?php echo base_url(); ?>assets/users/css/ie8.css">
		<script type="text/javascript" src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/respond.js/1.1.0/respond.min.js"></script>
	<![endif]-->
    <?php
    if(isset($css))
    {
        foreach($css as $css_item)
        {
            echo "<link rel='stylesheet' href='".base_url().$css_item."'>\n";
        }
    }
    ?>
    <link rel="stylesheet" href= "<?php echo base_url(); ?>assets/users/plugins/codeprettifier/prettify.css"> <!-- Google Code Prettifier -->
    <link rel="stylesheet" href= "<?php echo base_url(); ?>assets/users/plugins/form-toggle/toggles.css"><!-- Toggles -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/users/plugins/ladda-bootstrap-master/dist/ladda-themeless.min.css">
    <script src="<?php echo base_url(); ?>assets/users/plugins/ladda-bootstrap-master/dist/spin.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/users/plugins/ladda-bootstrap-master/dist/ladda.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/users/js/jquery-1.10.2.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/users/js/jqueryui-1.10.3.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/users/js/bootstrap.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/users/js/common.js'); ?>"></script>
    <link rel='stylesheet' type='text/css' href="<?php echo base_url(); ?>assets/users/plugins/pines-notify/jquery.pnotify.default.css">
    <script type='text/javascript' src="<?php echo base_url(); ?>assets/users/plugins/pines-notify/jquery.pnotify.min.js"></script>

    <?php
    if(isset($js))
    {
        foreach($js as $js_item)
        {
            echo "<script type='text/javascript' src='".base_url($js_item)."'></script>\n";
        }
    }
    ?>
</head>
<body class="<?php if (isset($_COOKIE["fixed-header"])) echo ' static-header'; ?>">
  
