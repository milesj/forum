
<?php // Crumbs

if (!empty($topic['Forum']['Parent']['slug'])) {
	$this->Html->addCrumb($topic['Forum']['Parent']['title'], array('controller' => 'stations', 'action' => 'view', $topic['Forum']['Parent']['slug']));
}

$this->Html->addCrumb($topic['Forum']['title'], array('controller' => 'stations', 'action' => 'view', $topic['Forum']['slug'])); ?>

<div class="forumHeader">
	<?php if (!$this->Common->user()) { ?>
		<div class="fr">
			<?php echo $this->element('login'); ?>
		</div>
	<?php } ?>

	<h2><?php echo $topic['Topic']['title']; ?></h2>
</div>

<?php echo $this->element('tiles/topic_controls', array(
	'topic' => $topic
)); ?>

<?php // Topic Poll
if (!empty($topic['Poll']['id'])) { ?>

<div id="pollWrap">
	<?php echo $this->Form->create('Poll', array('url' => array('controller' => 'topics', 'action' => 'view', $topic['Topic']['slug']))); ?>
  	
	<table cellspacing="0" class="table">

    <?php // Has not voted
	if (!$topic['Poll']['hasVoted']) {
		$counter = 0;
		foreach ($topic['Poll']['PollOption'] as $row => $option) { ?>

		<tr<?php if ($counter % 2) echo ' class="altRow"'; ?>>
			<td style="width: 20px" class="ac"><input type="radio" name="data[Poll][option]" value="<?php echo $option['id']; ?>"<?php if ($row == 0) echo ' checked="checked"'; ?> /></td>
			<td colspan="2"><?php echo $option['option']; ?></td>
		</tr>
	
		<?php ++$counter; 
	} ?>
    
		<tr class="altRow2">
			<td colspan="3" class="ac">
				<?php if ($this->Common->user()) {
					if (!empty($topic['Poll']['expires']) && $topic['Poll']['expires'] <= date('Y-m-d H:i:s')) { 
						__d('forum', 'Voting on this poll has been closed');
					} else { 
						echo $this->Form->submit(__d('forum', 'Vote', true), array('div' => false));
					}
				} else {
					__d('forum', 'Please login to vote!');
				} ?>
			</td>
		</tr>
    
    <?php // Has voted
	} else {
		$counter = 0;
		foreach ($topic['Poll']['PollOption'] as $row => $option) { ?>
        
		<tr<?php if ($counter % 2) echo ' class="altRow"'; ?>>
			<td><?php echo $option['option']; ?></td>
			<td style="width: 50%"><div class="pollBar" style="width: <?php echo $option['percentage']; ?>%"></div></td>
			<td>
				<?php echo number_format($option['vote_count']); ?> votes (<?php echo $option['percentage']; ?>%) 
				<?php if ($topic['Poll']['hasVoted'] == $option['id']) {
					echo '<em>('. __d('forum', 'Your Vote', true) .')</em>';
				} ?>
			</td>
		</tr>
	
    	<?php ++$counter; } 
	} ?>
    </table>
	
    <?php echo $this->Form->end(); ?>
</div>

<?php } ?>

<div id="postWrap">
	<?php echo $this->element('pagination'); ?>
    
	<table cellspacing="0" class="table">
    
    <?php foreach ($posts as $post) { ?>
		
    <tr class="altRow" id="post_<?php echo $post['Post']['id']; ?>">
		<td class="ar gray">
			<?php echo $this->Time->niceShort($post['Post']['created'], $this->Common->timezone()); ?>
		</td>
        <td class="ar gray">
        	<?php if ($this->Common->user()) {
				$links = array();
				
				if ($this->Common->hasAccess('super', $topic['Forum']['id']) || $this->Common->user('id') == $post['Post']['user_id']) {
					if ($topic['Topic']['firstPost_id'] == $post['Post']['id']) {
						$links[] = $this->Html->link(__d('forum', 'Edit', true), array('controller' => 'topics', 'action' => 'edit', $topic['Topic']['slug']));
						$links[] = $this->Html->link(__d('forum', 'Delete', true), array('controller' => 'topics', 'action' => 'delete', $topic['Topic']['slug']), array('confirm' => __d('forum', 'Are you sure you want to delete?', true)));
						$links[] = $this->Html->link(__d('forum', 'Report Topic', true), array('controller' => 'topics', 'action' => 'report', $topic['Topic']['slug']));
					} else {
						$links[] = $this->Html->link(__d('forum', 'Edit', true), array('controller' => 'posts', 'action' => 'edit', $post['Post']['slug']));
						$links[] = $this->Html->link(__d('forum', 'Delete', true), array('controller' => 'posts', 'action' => 'delete', $post['Post']['slug']), array('confirm' => __d('forum', 'Are you sure you want to delete?', true)));
						$links[] = $this->Html->link(__d('forum', 'Report Post', true), array('controller' => 'posts', 'action' => 'report', $post['Post']['slug']));
					}
				}
				
				if ($this->Common->hasAccess($topic['Forum']['accessReply'])) {
					$links[] = $this->Html->link(__d('forum', 'Quote', true), array('controller' => 'posts', 'action' => 'add', $topic['Topic']['slug'], $post['Post']['id']));
				}
				
				if (!empty($links)) {
					echo implode(' - ', $links);
				}
			} ?>
        </td>
    </tr>
    <tr>
    	<td valign="top" style="width: 25%">
        	<h4><?php echo $this->Html->link($post['User'][$config['userMap']['username']], array('controller' => 'users', 'action' => 'profile', $post['User']['id'])); ?></h4>
        	
			<?php if (!empty($post['User']['Access'])) { ?>
				<p><strong><?php echo $this->Common->highestAccessLevel($post['User']['Access']); ?></strong></p>
        	<?php } ?>

			<?php if ($settings['enable_gravatar']) { ?>
				<p><?php echo $this->Gravatar->image($post['User'][$config['userMap']['email']]); ?></p>
			<?php } ?>
        	
        	<strong><?php __d('forum', 'Joined'); ?>:</strong> <?php echo $this->Time->niceShort($post['User']['created'], $this->Common->timezone()); ?><br />
            <strong><?php __d('forum', 'Total Topics'); ?>:</strong> <?php echo number_format($post['User']['Profile']['totalTopics']); ?><br />
            <strong><?php __d('forum', 'Total Posts'); ?>:</strong> <?php echo number_format($post['User']['Profile']['totalPosts']); ?>
        </td>
        <td valign="top">
			<?php $this->Decoda->parse($post['Post']['content']); ?>
            
            <?php if (!empty($post['User']['signature'])) { ?>
				<div class="signature">
					<?php $this->Decoda->parse($post['User']['signature'], false, array('b', 'i', 'u', 'img', 'url', 'align', 'color', 'size', 'code')); ?>
				</div>
            <?php } ?>
       	</td>
 	</tr>
	
    <?php } ?>
    
    </table>
    
    <?php echo $this->element('pagination'); ?>
</div>

<?php echo $this->element('tiles/topic_controls', array(
	'topic' => $topic
)); ?>

<?php if ($settings['enable_quick_reply'] && $this->Common->hasAccess($topic['Forum']['accessReply'])) { ?>

	<div id="quickReply">
		<h3><?php __d('forum', 'Quick Reply'); ?></h3>

		<?php echo $this->Form->create('Post', array('url' => array('controller' => 'posts', 'action' => 'add', $topic['Topic']['id']))); ?>
		<table cellspacing="0" class="table">
		<tr>
			<td style="width: 25%">
				<strong><?php echo $this->Form->label('content', __d('forum', 'Message', true) .':'); ?></strong><br /><br />

				<?php echo $this->Html->link(__d('forum', 'Advanced Reply', true), array('controller' => 'posts', 'action' => 'add', $topic['Topic']['id'])); ?><br />
				<?php __d('forum', 'BBCode Enabled'); ?>
			</td>
			<td>
				<?php echo $this->Form->input('content', array('type' => 'textarea', 'rows' => 5, 'style' => 'width: 99%', 'div' => false, 'error' => false, 'label' => false)); ?>
				<?php echo $this->element('markitup', array('textarea' => 'PostContent')); ?>
			</td>
		</tr>
		<tr class="altRow">
			<td colspan="2" class="ac">
				<?php echo $this->Form->submit(__d('forum', 'Post Reply', true)); ?>
			</td>
		</tr> 
		</table>
		<?php echo $this->Form->end(); ?>
	</div>

<?php } ?>
