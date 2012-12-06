<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title><?=$page_title?> - <?=$site_desc?></title>
        <meta name="description" content="PHP Bounce is a product from Polyademy. Learn to code PHP interactively here!">
		<meta name="google-site-verification" content="XXX" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="shortcut icon" href="<?= base_url() ?>favicon.ico">
		<link rel="apple-touch-icon" href="<?= base_url() ?>apple-touch-icon.png">
		<link rel="stylesheet" href="<?= base_url($css_assets) ?>/main.css">
        <script src="<?= base_url($js_assets) ?>/modernizr/modernizr-2.6.1-respond-1.1.0.min.js"></script>
    </head>
	<body>
        <!--[if lt IE 7]>
            <p class="chromeframe">You are using an outdated browser. <a href="http://browsehappy.com/">Upgrade your browser today</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to better experience this site.</p>
        <![endif]-->
		<header>
			<nav class="navbar navbar-fixed-top center_nav" id="main_nav">
				<div class="navbar-inner">
					<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</a>
					<div class="nav-collapse collapse container">
						<ul class="nav">
							<li><a href="#home">PHP Bounce</a></li>
							<li class="divider-vertical"></li>
							<li><a href="#what_is">Mission 1</a></li>
							<li class="divider-vertical"></li>
						</ul>
					</div>
				</div>
			</nav>
		</header>