
<?php // Includes files and init!
$this->Html->script('/forum/js/jquery.markitup.pack.js', array('inline' => false));
$this->Html->script('/forum/js/sets/bbcode.js', array('inline' => false));
$this->Html->css('/forum/css/markitup.css', 'stylesheet', array('media' => 'screen', 'inline' => false));
$this->Html->css('/forum/css/bbcode.css', 'stylesheet', array('media' => 'screen', 'inline' => false)); ?>

<script type="text/javascript">
   $(function() {
      $("#<?php echo $textarea; ?>").markItUp(mySettings);
   });
</script>
