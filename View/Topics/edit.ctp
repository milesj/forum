<?php

if (!empty($topic['Forum']['Parent']['slug'])) {
	$this->Breadcrumb->add($topic['Forum']['Parent']['title'], array('controller' => 'stations', 'action' => 'view', $topic['Forum']['Parent']['slug']));
}

$this->Breadcrumb->add($topic['Forum']['title'], array('controller' => 'stations', 'action' => 'view', $topic['Forum']['slug']));
$this->Breadcrumb->add($topic['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $topic['Topic']['slug']));
$this->Breadcrumb->add(__d('forum', 'Edit Topic'), array('controller' => 'topics', 'action' => 'edit', $topic['Topic']['slug'])); ?>

<div class="title">
	<h2><?php echo __d('forum', 'Edit Topic'); ?></h2>
</div>

<?php echo $this->Form->create('Topic'); ?>

<div class="container">
	<div class="containerContent">
		<?php echo $this->Form->input('title', array('label' => __d('forum', 'Title')));

		if ($this->Forum->isMod($topic['Forum']['id'])) {
			echo $this->Form->input('forum_id', array('label' => __d('forum', 'Forum'), 'options' => $forums, 'empty' => '-- ' . __d('forum', 'Select a Forum') . ' --'));
			echo $this->Form->input('status', array('label' => __d('forum', 'Status'), 'options' => $this->Utility->enum('Forum.Topic', 'status')));
			echo $this->Form->input('type', array('options' => $this->Utility->enum('Forum.Topic', 'type'), 'label' => __d('forum', 'Type')));
		} else {
			echo $this->Form->input('forum_id', array('type' => 'hidden'));
		}

		if (!empty($topic['Poll']['id'])) { ?>

			<div class="input poll">
				<?php echo $this->Form->label('Poll.id', __d('forum', 'Poll Options'));
				echo $this->Form->input('Poll.id', array('type' => 'hidden')); ?>

				<div class="pollOptions">
					<table>
						<?php foreach ($topic['Poll']['PollOption'] as $row => $option) { ?>
							<tr>
								<td>
									<?php echo $this->Form->input('Poll.PollOption.' . $row . '.id', array('type' => 'hidden')); ?>
									<?php echo $this->Form->input('Poll.PollOption.' . $row . '.option', array('div' => false, 'label' => false)); ?>
								</td>
								<td>
									<?php echo $this->Form->input('Poll.PollOption.' . $row . '.delete', array('type' => 'checkbox', 'div' => false, 'label' => false, 'value' => 0)); ?>
									<?php echo __d('forum', 'Delete?'); ?>
								</td>
							</tr>
						<?php } ?>
					</table>
				</div>
			</div>

			<?php echo $this->Form->input('Poll.expires', array(
				'label' => __d('forum', 'Expiration Date'),
				'after' => '<span class="inputText">' . __d('forum', 'How many days till expiration? Leave blank to last forever.') . '</span>',
				'class' => 'numeric',
				'type' => 'text'
			));
		}

		if ($topic['Forum']['excerpts']) {
			$chars = isset($this->request->data['Topic']['excerpt']) ? strlen($this->request->data['Topic']['excerpt']) : 0;
			$maxLength = $settings['excerptLength'];

			echo $this->Form->input('excerpt', array(
				'label' => __d('forum', 'Excerpt'),
				'type' => 'textarea',
				'rows' => 5,
				'onkeyup' => 'Forum.charsRemaining(this, ' . $maxLength . ');',
				'after' => '<span class="inputText">' . __d('forum', '%s characters remaining', '<span id="TopicExcerptCharsRemaining">' . ($maxLength - $chars) . '</span>') . '</span>',
			));
		}

		echo $this->Form->input('FirstPost.id', array('type' => 'hidden'));

		echo $this->Form->input('FirstPost.content', array(
			'label' => __d('forum', 'Content'),
			'type' => 'textarea',
			'rows' => 15
		));

		echo $this->element('decoda', array('id' => 'FirstPostContent')); ?>
	</div>
</div>

<?php
echo $this->Form->submit(__d('forum', 'Edit Topic'), array('class' => 'button'));
echo $this->Form->end(); ?>