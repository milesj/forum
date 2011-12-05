<?php

$this->Html->addCrumb($settings['site_name'], array('controller' => 'forum', 'action' => 'index'));

if (!empty($topic['Forum']['Parent']['slug'])) {
	$this->Html->addCrumb($topic['Forum']['Parent']['title'], array('controller' => 'stations', 'action' => 'view', $topic['Forum']['Parent']['slug']));
}

$this->Html->addCrumb($topic['Forum']['title'], array('controller' => 'stations', 'action' => 'view', $topic['Forum']['slug']));
$this->Html->addCrumb($topic['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $topic['Topic']['slug'])); ?>

<div class="title">
	<h2><?php __d('forum', 'Edit Topic'); ?></h2>
</div>

<?php echo $this->Form->create('Topic', array(
	'url' => array($topic['Topic']['slug'])
)); ?>

<div class="container">
	<div class="containerContent">
		<?php echo $this->Form->input('title', array('label' => __d('forum', 'Title', true)));

		if ($this->Common->hasAccess(AccessLevel::SUPER, $topic['Forum']['id'])) {
			echo $this->Form->input('forum_id', array('label' => __d('forum', 'Forum', true), 'options' => $forums, 'escape' => false, 'empty' => '-- '. __d('forum', 'Select a Forum', true) .' --'));
			echo $this->Form->input('status', array('label' => __d('forum', 'Status', true), 'options' => $this->Common->options('topicStatus')));
			echo $this->Form->input('type', array('options' => $this->Common->options('topicTypes'), 'label' => __d('forum', 'Type', true)));
		} else {
			echo $this->Form->input('forum_id', array('type' => 'hidden'));
		} 

		if (!empty($topic['Poll']['id'])) { ?>

			<div class="input poll">
				<?php echo $this->Form->label('Poll.id', __d('forum', 'Poll Options', true));
				echo $this->Form->input('Poll.id', array('type' => 'hidden')); ?>

				<div class="pollOptions">
					<table>
						<?php foreach ($topic['Poll']['PollOption'] as $row => $option) { ?>
							<tr>
								<td>
									<?php echo $this->Form->input('Poll.PollOption.'. $row .'.id', array('type' => 'hidden')); ?>
									<?php echo $this->Form->input('Poll.PollOption.'. $row .'.option', array('div' => false, 'label' => false)); ?>
								</td>
								<td>
									<?php echo $this->Form->input('Poll.PollOption.'. $row .'.delete', array('type' => 'checkbox', 'div' => false, 'label' => false, 'value' => 0)); ?> 
									<?php __d('forum', 'Delete?'); ?>
								</td>
							</tr>
						<?php } ?>
					</table>
				</div>
			</div>

			<?php echo $this->Form->input('Poll.expires', array(
				'label' => __d('forum', 'Expiration Date', true), 
				'after' => '<span class="inputText">'. __d('forum', 'How many days till expiration? Leave blank to last forever.', true) .'</span>', 
				'class' => 'numeric',
				'type' => 'text'
			));
		}
		
		echo $this->Form->input('FirstPost.id', array('type' => 'hidden'));
			
		echo $this->Form->input('FirstPost.content', array(
			'after' => '<span class="inputText">[b], [u], [i], [img], [url], [email], [code], [align], [list], [li], [color], [size], [quote]</span>',
			'label' => __d('forum', 'Content', true), 
			'type' => 'textarea', 
			'rows' => 15
		));
		
		echo $this->element('markitup', array('textarea' => 'FirstPostContent')); ?>
	</div>
</div>

<?php 
echo $this->Form->submit(__d('forum', 'Edit Topic', true), array('class' => 'button'));
echo $this->Form->end(); ?>