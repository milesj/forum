<?php

$this->Html->addCrumb($settings['site_name'], array('controller' => 'forum', 'action' => 'index'));

if (!empty($topic['Forum']['Parent']['slug'])) {
	$this->Html->addCrumb($topic['Forum']['Parent']['title'], array('controller' => 'stations', 'action' => 'view', $topic['Forum']['Parent']['slug']));
}

$this->Html->addCrumb($topic['Forum']['title'], array('controller' => 'stations', 'action' => 'view', $topic['Forum']['slug']));
$this->Html->addCrumb($topic['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $topic['Topic']['slug'])); ?>

<div class="controls float-right">
	<?php
	echo $this->Html->link(__d('forum', 'Delete Topic'), array('controller' => 'topics', 'action' => 'delete', $topic['Topic']['slug']), array('class' => 'button', 'confirm' => __d('forum', 'Are you sure you want to delete?')));
	echo $this->Html->link(__d('forum', 'Return to Topic'), array('controller' => 'topics', 'action' => 'view', $topic['Topic']['slug']), array('class' => 'button')); ?>
</div>

<div class="title">
	<h2><span><?php echo __d('forum', 'Moderate'); ?>:</span> <?php echo h($topic['Topic']['title']); ?></h2>
</div>

<?php echo $this->Form->create('Post'); ?>

<div class="container">
	<div class="containerContent">
		<?php echo $this->element('pagination'); ?>

		<table class="table topics">
			<thead>
				<tr>
					<th><input type="checkbox" onclick="Forum.toggleCheckboxes(this, 'Post', 'items');" /></th>
					<th><?php echo __d('forum', 'User'); ?></th>
					<th><?php echo __d('forum', 'Post'); ?></th>
					<th><?php echo __d('forum', 'Date'); ?></th>
				</tr>
			</thead>
			<tbody>

			<?php foreach ($posts as $counter => $post) { ?>

				<tr<?php if ($counter % 2) echo ' class="altRow"'; ?>>
					<td class="icon">
						<?php if ($post['Post']['id'] == $topic['Topic']['firstPost_id']) { ?>
							<em class="gray">X</em>
						<?php } else { ?>
							<input type="checkbox" name="data[Post][items][]" value="<?php echo $post['Post']['id']; ?>" />
						<?php } ?>
					</td>
					<td>
						<?php echo $this->Html->link($post['User'][$config['userMap']['username']], array('controller' => 'users', 'action' => 'profile', $post['User']['id'])); ?>
					</td>
					<td>
						<?php echo str_replace("\n", '', $this->Text->truncate($post['Post']['content'], 100)); ?>
					</td>
					<td class="created">
						<?php echo $this->Time->niceShort($post['Post']['created'], $this->Common->timezone()); ?>
					</td>
				</tr>

			<?php } ?>

			</tbody>
		</table>

		<?php echo $this->element('pagination'); ?>
	</div>
</div>

<div class="moderate">
	<?php
	echo $this->Form->input('action', array('options' => array('delete' => __d('forum', 'Delete Post(s)')), 'div' => false, 'label' => __d('forum', 'Perform Action') . ': '));
	echo $this->Form->submit(__d('forum', 'Process'), array('div' => false, 'class' => 'buttonSmall')); ?>
</div>

<?php echo $this->Form->end(); ?>
