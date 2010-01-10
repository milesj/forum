
<?php // Includes files and init!
$javascript->link('/forum/js/jquery.markitup.pack.js', false);
$javascript->link('/forum/js/sets/bbcode.js', false);
$html->css('/forum/css/markitup.css', 'stylesheet', array('media' => 'screen'), false);
$html->css('/forum/css/bbcode.css', 'stylesheet', array('media' => 'screen'), false); ?>

<script type="text/javascript">
   $(function() {
      $("#<?php echo $textarea; ?>").markItUp(mySettings);
   });
</script>
