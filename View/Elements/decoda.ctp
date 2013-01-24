<?php
$this->Html->css('/utility/css/decoda-1.1.0.min.css');
$this->Html->script('/utility/js/decoda-1.1.0.min.js'); ?>

<script type="text/javascript">
	window.addEvent('domready', function() {
		var decoda = new Decoda('<?php echo $id; ?>', {
			previewUrl: '/forum/post/preview',
			onInitialize: function() {
				this.editor.getParent('div').addClass('input-decoda');
			},
			onSubmit: function() {
				return this.clean();
			}
		}).defaults();
	});
</script>