<?php

if (!empty($forum['Parent']['slug'])) {
	$this->Breadcrumb->add($forum['Parent']['title'], array('controller' => 'stations', 'action' => 'view', $forum['Parent']['slug']));
}

$this->Breadcrumb->add($forum['Forum']['title'], array('controller' => 'stations', 'action' => 'view', $forum['Forum']['slug']));
$this->Breadcrumb->add($pageTitle, array('controller' => 'topics', 'action' => 'add', $forum['Forum']['slug'])); ?>

<div class="title">
	<h2><?php echo $pageTitle; ?></h2>
</div>

<?php echo $this->Form->create('Topic'); ?>

<div class="container">
	<div class="containerContent">
		<?php
		echo $this->Form->input('title', array('label' => __d('forum', 'Title')));
		echo $this->Form->input('forum_id', array('options' => $forums, 'empty' => '-- ' . __d('forum', 'Select a Forum') . ' --', 'label' => __d('forum', 'Forum')));

		if ($this->Forum->isMod($forum['Forum']['id'])) {
			echo $this->Form->input('status', array('options' => $this->Utility->enum('Forum.Topic', 'status'), 'label' => __d('forum', 'Status')));
			echo $this->Form->input('type', array('options' => $this->Utility->enum('Forum.Topic', 'type'), 'label' => __d('forum', 'Type')));
		}

		if ($type === 'poll') {
			echo $this->Form->input('options', array(
				'type' => 'textarea',
				'label' => __d('forum', 'Poll Options'),
				'after' => '<span class="inputText">' . __d('forum', 'One option per line. Max 10 options.') . '</span>',
				'rows' => 5
			));

			echo $this->Form->input('expires', array(
				'label' => __d('forum', 'Expiration Date'),
				'after' => '<span class="inputText">' . __d('forum', 'How many days till expiration? Leave blank to last forever.') . '</span>',
				'class' => 'numeric'
			));
		}

		if ($forum['Forum']['excerpts']) {
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

		echo $this->Form->input('content', array(
			'label' => __d('forum', 'Content'),
			'type' => 'textarea',
			'rows' => 15
		));

		echo $this->element('decoda', array('id' => 'TopicContent')); ?>
	</div>
</div>

<?php
echo $this->Form->submit($pageTitle, array('class' => 'button'));
echo $this->Form->end(); ?>
