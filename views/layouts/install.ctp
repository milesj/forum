<?php echo $html->docType('xhtml-trans'); ?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php echo $html->charset(); ?>
<title>
	<?php echo $cupcake->settings['site_name']; ?> Installation -
	<?php echo $title_for_layout; ?>
</title>

<?php // Scripts
echo $html->css('/forum/css/style.css');
echo $javascript->link('/forum/js/jquery-1.3.2.min.js');
echo $javascript->link('/forum/js/script.js');
echo $scripts_for_layout; ?>
</head>

<body>
<div id="wrapper">
	<div id="header">
    	<h1><?php echo $cupcake->settings['site_name']; ?> Installation</h1>
    </div>

    <div id="content">
		<?php echo $content_for_layout; ?>
 	</div>

    <?php // Would love it if you kept this in all the pages :]
	echo $this->element('copyright'); ?>
</div>
</body>
</html>