<?php

$this->Html->script('/forum/js/jquery.markitup.pack.js', array('inline' => false));
$this->Html->script('/forum/js/sets/bbcode.js', array('inline' => false)); ?>

<script type="text/javascript">
   $(function() {
      $("#<?php echo $textarea; ?>").markItUp(bbcodeSettings);
   });
</script>
