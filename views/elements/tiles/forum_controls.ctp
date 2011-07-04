
<?php if ($this->Common->user()) { ?>
    <div class="forumOptions <?php echo isset($class) ? $class : ''; ?>">
		<?php if ($this->Common->hasAccess('mod', $forum['Forum']['id'])) {
            echo $this->Html->link(__d('forum', 'Moderate', true), array('controller' => 'stations', 'action' => 'moderate', $forum['Forum']['slug']));
        }
		
		if ($forum['Forum']['status']) {
            if ($this->Common->hasAccess($forum['Forum']['accessPost'])) {
                echo $this->Html->link(__d('forum', 'Create Topic', true), array('controller' => 'topics', 'action' => 'add', $forum['Forum']['slug']));
            }
			
			if ($this->Common->hasAccess($forum['Forum']['accessPoll'])) {
                echo $this->Html->link(__d('forum', 'Create Poll', true), array('controller' => 'topics', 'action' => 'add', $forum['Forum']['slug'], 'poll'));
            }
        } else {
            echo '<span>'. __d('forum', 'Closed', true) .'</span>';
        } ?>
    </div>
<?php } ?>