<!DOCTYPE html>
<html>
    <head>
	<title>RyanTheGreat <?php if($title) echo "| $title";?></title>
	<link rel="stylesheet" type="text/css" href="<?=format('css/global.css');?>" />
	<?php
	foreach($this -> css as $source)
	{?>
<link rel="stylesheet" type="text/css" href="<?=$source;?>" /><?="\n";?>
	<?}?><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="title" content="RyanTheGreat <?php if($title) echo "| $title";?>" />
	<meta name="author" content="Ryan Wagner" />
	<meta name="copyright" content="Ryan Wagner, 2012" />
	<link rel="icon" type="image/png" href="<?=format('images/favicon.png');?>">
	<!--[if IE]><link rel="shortcut icon" href="<?=format('images/favicon.ico');?>"/><![endif]-->
	<!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
    </head>
    <body>
<?php
$this -> render_partial('navigation');
?>
	<div id="container">
