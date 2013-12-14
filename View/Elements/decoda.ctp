<?php
$this->Html->css('Utility.decoda.min', 'stylesheet', array('inline' => false));
$this->Html->css('Forum.decoda', 'stylesheet', array('inline' => false));

// Perhaps I should convert Decoda to jQuery
$this->Html->script('//cdnjs.cloudflare.com/ajax/libs/mootools/1.4.5/mootools-core-full-nocompat-yc.js', array('inline' => false));
$this->Html->script('//cdnjs.cloudflare.com/ajax/libs/mootools-more/1.4.0.1/mootools-more-yui-compressed.min.js', array('inline' => false));
$this->Html->script('Utility.decoda.min', array('inline' => false)); ?>

<script type="text/javascript">
    $(function() {
        var decoda = new Decoda('<?php echo $id; ?>', {
            previewUrl: '/forum/posts/preview',
            onInitialize: function() {
                this.editor.getParent('div').addClass('input-decoda');
            },
            onSubmit: function() {
                return this.clean();
            },
            onRenderToolbar: function(toolbar) {
                toolbar.getElements('button').each(function(button) {
                    button.set('data-tooltip', button.get('title')).addClass('js-tooltip').removeProperty('title');
                });
            },
            onRenderHelp: function(table) {
                table.addClass('table');
            }
        }).defaults();
    });
</script>