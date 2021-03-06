<!doctype html>
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

  <title>MVC framework project</title>
  <meta name="description" content="MVC framework project">
  <meta name="author" content="ASh Lewis">

  <meta name="viewport" content="width=device-width,initial-scale=1">

  <!-- CSS concatenated and minified via ant build script-->
  <link rel="stylesheet" href="<?php echo Config::get(baseURL); ?>public/css/style.css">
  <!-- end CSS-->

  <script src="<?php echo Config::get(baseURL); ?>public/js/libs/modernizr-2.0.6.min.js"></script>
</head>

<body>
  <nav>
    <ul>
        <li><a href="/item">Items</a></li>
    </ul>
  </nav>
  <div id="container">
    <header>
		<h1>MVC framework project</h1>
    </header>
    <div id="main" role="main">