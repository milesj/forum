<?php echo $this->Html->docType('xhtml-trans'); ?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php echo $this->Html->charset(); ?>
<title>
	<?php echo $this->Cupcake->settings['site_name']; ?> Installation -
	<?php echo $title_for_layout; ?>
</title>

<?php // Scripts
echo $this->Html->css('/forum/css/style.css');
echo $this->Html->script('/forum/js/jquery-1.5.min.js');
echo $this->Html->script('/forum/js/script.js');
echo $scripts_for_layout; ?>
</head>

<body>
<div id="wrapper">
	<div id="header">
    	<h1><?php echo $this->Cupcake->settings['site_name']; ?> Installation</h1>
    </div>

    <div id="content">
		<?php echo $content_for_layout; ?>
 	</div>

    <?php // Would love it if you kept this in all the pages :]
	echo $this->element('copyright'); ?>
</div>
</body>
</html>