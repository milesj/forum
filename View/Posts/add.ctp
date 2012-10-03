<?php

$this->Html->addCrumb($settings['site_name'], array('controller' => 'forum', 'action' => 'index'));

if (!empty($topic['Forum']['Parent']['slug'])) {
	$this->Html->addCrumb($topic['Forum']['Parent']['title'], array('controller' => 'stations', 'action' => 'view', $topic['Forum']['Parent']['slug']));
}

$this->Html->addCrumb($topic['Forum']['title'], array('controller' => 'stations', 'action' => 'view', $topic['Forum']['slug']));
$this->Html->addCrumb($topic['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $topic['Topic']['slug'])); ?>

<div class="title">
	<h2><?php echo __d('forum', 'Post Reply'); ?></h2>
</div>

<?php echo $this->Form->create('Post'); ?>

<div class="container">
	<div class="containerContent">
		<?php
		echo $this->Form->input('content', array(
			'type' => 'textarea',
			'rows' => 15,
			'after' => '<span class="inputText">[b], [u], [i], [s], [img], [url], [email], [color], [size], [left], [center], [right], [justify], [list], [olist], [li], [quote], [code]</span>',
			'label' => __d('forum', 'Content')));

		echo $this->element('markitup', array('textarea' => 'PostContent')); ?>
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
						<?php echo $this->Time->niceShort($post['Post']['created'], $this->Common->timezone()); ?>
					</td>
				</tr>
				<tr>
					<td valign="top" style="width: 25%">
						<h4 class="username"><?php echo $this->Html->link($post['User'][$config['userMap']['username']], array('controller' => 'users', 'action' => 'profile', $post['User']['id'])); ?></h4>
					</td>
					<td valign="top">
						<?php echo $post['Post']['contentHtml']; ?>
					</td>
				</tr>

			<?php } ?>

			</table>
		</div>
	</div>

<?php } ?>