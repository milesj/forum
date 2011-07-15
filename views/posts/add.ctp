<?php 

$this->Html->addCrumb($settings['site_name'], array('controller' => 'forum', 'action' => 'index'));

if (!empty($topic['Forum']['Parent']['slug'])) {
	$this->Html->addCrumb($topic['Forum']['Parent']['title'], array('controller' => 'stations', 'action' => 'view', $topic['Forum']['Parent']['slug']));
}

$this->Html->addCrumb($topic['Forum']['title'], array('controller' => 'stations', 'action' => 'view', $topic['Forum']['slug']));
$this->Html->addCrumb($topic['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $topic['Topic']['slug'])); ?>

<div class="title">
	<h2><?php __d('forum', 'Post Reply'); ?></h2>
</div>

<div class="container">
	<div class="containerContent">
		<?php 
		echo $this->Form->create('Post', array('url' => $this->here));
		echo $this->Form->input('content', array('type' => 'textarea', 'rows' => 15, 'label' => __d('forum', 'Content', true)));?>

		<?php echo $this->element('markitup', array('textarea' => 'PostContent')); ?>

		<div class="input">
			<label><?php __d('forum', 'Allowed Tags'); ?>:</label> 
			[b], [u], [i], [img], [url], [email], [code], [align], [list], [li], [color], [size], [quote]
		</div>

		<?php 
		echo $this->Form->submit(__d('forum', 'Post', true), array('class' => 'button'));
		echo $this->Form->end(); ?>
	</div>
</div>

<?php if (!empty($review)) { ?>

	<div class="container">
		<div class="containerHeader">
			<h3><?php __d('forum', 'Topic Review - Last 10 Replies'); ?></h3>
		</div>

		<div class="containerContent" id="topicReview">
			<table class="table">

			<?php foreach ($review as $post) { ?>

				<tr class="altRow">
					<td colspan="2" class="align-right dark">
						<?php echo $this->Time->niceShort($post['Post']['created'], $this->Common->timezone()); ?>
					</td>
				</tr>
				<tr>
					<td valign="top" style="width: 25%">
						<h4><?php echo $this->Html->link($post['User'][$config['userMap']['username']], array('controller' => 'users', 'action' => 'profile', $post['User']['id'])); ?></h4>
						<strong><?php __d('forum', 'Joined'); ?>:</strong> <?php echo $this->Time->niceShort($post['User']['created'], $this->Common->timezone()); ?>
					</td>
					<td valign="top">
						<?php $this->Decoda->parse($post['Post']['content']); ?>
					</td>
				</tr>

			<?php } ?>

			</table>
		</div>      
	</div>  

<?php } ?>