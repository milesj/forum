<?php

if (!empty($topic['Forum']['Parent']['slug'])) {
	$this->Breadcrumb->add($topic['Forum']['Parent']['title'], array('controller' => 'stations', 'action' => 'view', $topic['Forum']['Parent']['slug']));
}

$this->Breadcrumb->add($topic['Forum']['title'], array('controller' => 'stations', 'action' => 'view', $topic['Forum']['slug']));
$this->Breadcrumb->add($topic['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $topic['Topic']['slug']));
$this->Breadcrumb->add(__d('forum', 'Post Reply'), array('action' => 'add', $topic['Topic']['slug'])); ?>

<div class="title">
	<h2><?php echo __d('forum', 'Post Reply'); ?></h2>
</div>

<?php echo $this->Form->create('Post'); ?>

<div class="container">
	<div class="containerContent">
		<?php
		echo $this->Form->input('content', array('type' => 'textarea', 'rows' => 15, 'label' => __d('forum', 'Content')));
		echo $this->element('decoda', array('id' => 'PostContent')); ?>
	</div>
</div>

<?php
echo $this->Form->submit(__d('forum', 'Post Reply'), array('class' => 'button'));
echo $this->Form->end();

if ($review) { ?>

	<div class="container">
		<div class="containerHeader">
			<h3><?php echo __d('forum', 'Topic Review - Last 10 Replies'); ?></h3>
		</div>

		<div class="containerContent" id="topicReview">
			<table class="table">

			<?php foreach ($review as $post) { ?>

				<tr class="altRow">
					<td colspan="2" class="align-right dark">
						<?php echo $this->Time->niceShort($post['Post']['created'], $this->Forum->timezone()); ?>
					</td>
				</tr>
				<tr>
					<td valign="top" style="width: 25%">
						<h4 class="username"><?php echo $this->Html->link($post['User'][$userFields['username']], $this->Forum->profileUrl($post['User'])); ?></h4>
					</td>
					<td valign="top">
						<?php echo $this->Decoda->parse($post['Post']['content']); ?>
					</td>
				</tr>

			<?php } ?>

			</table>
		</div>
	</div>

<?php } ?>