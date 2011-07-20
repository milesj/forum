<?php 

$this->Html->addCrumb($settings['site_name'], array('controller' => 'forum', 'action' => 'index'));

if (!empty($topic['Forum']['Parent']['slug'])) {
	$this->Html->addCrumb($topic['Forum']['Parent']['title'], array('controller' => 'stations', 'action' => 'view', $topic['Forum']['Parent']['slug']));
}

$this->Html->addCrumb($topic['Forum']['title'], array('controller' => 'stations', 'action' => 'view', $topic['Forum']['slug']));
$this->Html->addCrumb($topic['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $topic['Topic']['slug'])); ?>

<div class="title">
	<h2><span><?php __d('forum', 'Moderate'); ?>:</span> <?php echo $topic['Topic']['title']; ?></h2>
</div>

<div class="controls">
	<?php 
	echo $this->Html->link(__d('forum', 'Delete Topic', true), array('controller' => 'topics', 'action' => 'delete', $topic['Topic']['slug']), array('class' => 'button', 'confirm' => __d('forum', 'Are you sure you want to delete?', true)));
	echo $this->Html->link(__d('forum', 'Return to Topic', true), array('controller' => 'topics', 'action' => 'view', $topic['Topic']['slug']), array('class' => 'button')); ?>
</div>

<?php echo $this->Form->create('Post', array('url' => $this->here)); ?>

<div class="container">
	<div class="containerContent">
		<?php echo $this->element('pagination'); ?>

		<table class="table topics">
			<thead>
				<tr>
					<th><input type="checkbox" onclick="toggleCheckboxes(this, 'Post', 'items');" /></th>
					<th><?php __d('forum', 'User'); ?></th>
					<th><?php __d('forum', 'Post'); ?></th>
					<th><?php __d('forum', 'Date'); ?></th>
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
						<?php echo $this->Text->truncate($post['Post']['content'], 100); ?>
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

<div class="controls">
	<?php 
	echo $this->Html->link(__d('forum', 'Delete Topic', true), array('controller' => 'topics', 'action' => 'delete', $topic['Topic']['slug']), array('class' => 'button', 'confirm' => __d('forum', 'Are you sure you want to delete?', true)));
	echo $this->Html->link(__d('forum', 'Return to Topic', true), array('controller' => 'topics', 'action' => 'view', $topic['Topic']['slug']), array('class' => 'button')); ?>
</div>

<div class="moderate">
	<?php 
	echo $this->Form->input('action', array('options' => array('delete' => __d('forum', 'Delete Post(s)', true)), 'div' => false, 'label' => __d('forum', 'Perform Action', true) .': '));
	echo $this->Form->submit(__d('forum', 'Process', true), array('div' => false, 'class' => 'buttonSmall')); ?>
</div>

<?php echo $this->Form->end(); ?>
