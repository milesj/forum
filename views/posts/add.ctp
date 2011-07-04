
<?php // Crumbs
if (!empty($topic['Forum']['Parent']['slug'])) {
	$this->Html->addCrumb($topic['Forum']['Parent']['title'], array('controller' => 'stations', 'action' => 'view', $topic['Forum']['Parent']['slug']));
}
$this->Html->addCrumb($topic['Forum']['title'], array('controller' => 'stations', 'action' => 'view', $topic['Forum']['slug']));
$this->Html->addCrumb($topic['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $topic['Topic']['slug'])); ?>

<div class="forumHeader">
	<h2><?php __d('forum', 'Post Reply'); ?></h2>
</div>

<?php echo $this->Form->create('Post', array('url' => $this->here)); ?>

<div class="input textarea">
	<?php echo $this->Form->label('content', __d('forum', 'Content', true)); ?>

	<div id="textarea">
		<?php echo $this->Form->input('content', array('type' => 'textarea', 'rows' => 15, 'label' => false, 'div' => false)); ?>
	</div>

	<span class="clear"><!-- --></span>
	<?php echo $this->element('markitup', array('textarea' => 'PostContent')); ?>
</div>

<div class="input ac">
	<strong><?php __d('forum', 'Allowed Tags'); ?>:</strong> [b], [u], [i], [img], [url], [email], [code], [align], [list], [li], [color], [size], [quote]
</div>

<?php echo $this->Form->end(__d('forum', 'Post', true)); ?>

<?php // Topic review
if (!empty($review)) { ?>

<div class="forumWrap">
    <h3><?php __d('forum', 'Topic Review - Last 10 Replies'); ?></h3>
    
    <div id="topicReview">
        <table class="table" cellspacing="0">
        
        <?php foreach ($review as $post) { ?>
			
			<tr class="altRow" id="post_<?php echo $post['Post']['id']; ?>">
				<td colspan="2" class="ar"><?php echo $this->Time->niceShort($post['Post']['created'], $this->Common->timezone()); ?></td>
			</tr>
			<tr>
				<td valign="top" style="width: 25%">
					<h4><?php echo $this->Html->link($post['User'][$config['userMap']['username']], array('controller' => 'users', 'action' => 'profile', $post['User']['id'])); ?></h4>
					<strong><?php __d('forum', 'Joined'); ?>:</strong> <?php echo $this->Time->niceShort($post['User']['created'], $this->Common->timezone()); ?>
				</td>
				<td valign="top"><?php $this->Decoda->parse($post['Post']['content']); ?></td>
			</tr>
			
        <?php } ?>
        
        </table>
  	</div>      
</div>  

<?php } ?>