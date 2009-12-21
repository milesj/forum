
<?php // Crumbs
$html->addCrumb($topic['ForumCategory']['Forum']['title'], array('controller' => 'home', 'action' => 'index'));
if (!empty($topic['ForumCategory']['Parent']['id'])) {
	$html->addCrumb($topic['ForumCategory']['Parent']['title'], array('controller' => 'categories', 'action' => 'view', $topic['ForumCategory']['Parent']['id']));
}
$html->addCrumb($topic['ForumCategory']['title'], array('controller' => 'categories', 'action' => 'view', $topic['ForumCategory']['id'])); ?>

<?php if (!$cupcake->user()) { ?>
<div class="fr">
	<?php echo $this->element('login'); ?>
</div>
<?php } ?>

<h2><?php echo $topic['Topic']['title']; ?></h2>

<?php if ($cupcake->user()) { ?>
<div class="forumOptions">
	<?php if ($cupcake->hasAccess('mod', $topic['ForumCategory']['id'])) {
		echo $html->link(__d('forum', 'Moderate', true), array('controller' => 'topics', 'action' => 'moderate', $topic['Topic']['id']));
	} ?>
	<?php if ($cupcake->hasAccess($topic['ForumCategory']['accessPost'])) {
		echo $html->link(__d('forum', 'Create Topic', true), array('controller' => 'topics', 'action' => 'add', $topic['ForumCategory']['id']));
	} ?>
    <?php if ($cupcake->hasAccess($topic['ForumCategory']['accessPoll'])) {
		echo $html->link(__d('forum', 'Create Poll', true), array('controller' => 'topics', 'action' => 'add', $topic['ForumCategory']['id'], 'poll'));
	} ?>
    <?php if ($cupcake->hasAccess($topic['ForumCategory']['accessReply'])) {
		if ($topic['Topic']['status'] == 0) {
			echo $html->link(__d('forum', 'Post Reply', true), array('controller' => 'posts', 'action' => 'add', $topic['Topic']['id']));
		} else {
			echo '<span>'. __d('forum', 'Closed', true) .'</span>';
		}	
	} ?>
</div>
<?php } ?>

<?php // Topic Poll
if (!empty($topic['Poll']['id'])) { ?>
<div id="pollWrap">
	<?php echo $form->create('Poll', array('url' => array('controller' => 'topics', 'action' => 'view', $topic['Topic']['id']))); ?>
  	<table cellspacing="0" class="table">
    <tr>
    	<th colspan="3"><?php echo $topic['Topic']['title']; ?></th>
	</tr>
    
    <?php // Has not voted
	if ($topic['Poll']['hasVoted'] == 'no') {
		$counter = 0;
		foreach ($topic['Poll']['PollOption'] as $row => $option) { ?>
        
    <tr<?php if ($counter % 2) echo ' class="altRow"'; ?>>
    	<td style="width: 20px" class="ac"><input type="radio" name="data[Poll][option]" value="<?php echo $option['id']; ?>"<?php if ($row == 0) echo ' checked="checked"'; ?> /></td>
        <td colspan="2"><?php echo $option['option']; ?></td>
    </tr>
    <?php ++$counter; } ?>
    
    <tr class="altRow2">
    	<td colspan="3" class="ac">
			<?php if ($cupcake->user()) {
				if (!empty($topic['Poll']['expires']) && $topic['Poll']['expires'] <= date('Y-m-d H:i:s')) { 
					__d('forum', 'Voting on this poll has been closed');
				} else { 
					echo $form->submit(__d('forum', 'Vote', true), array('div' => false));
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
    <?php echo $form->end(); ?>
</div>
<?php } ?>

<div id="postWrap">
	<?php echo $this->element('pagination'); ?>
    
	<table cellspacing="0" class="table">
    
    <?php foreach ($posts as $post) { ?>
    <tr class="altRow" id="post_<?php echo $post['Post']['id']; ?>">
		<td class="ar gray"><?php echo $time->niceShort($post['Post']['created'], $cupcake->timezone()); ?></td>
        <td class="ar gray">
        	<?php // Commands
			if ($cupcake->user()) {
				$links = array();
				if ($cupcake->hasAccess('super', $topic['ForumCategory']['id']) || $cupcake->user('id') == $post['Post']['user_id']) {
					if ($topic['Topic']['firstPost_id'] == $post['Post']['id']) {
						$links[] = $html->link(__d('forum', 'Edit', true), array('controller' => 'topics', 'action' => 'edit', $topic['Topic']['id']));
						$links[] = $html->link(__d('forum', 'Delete', true), array('controller' => 'topics', 'action' => 'delete', $topic['Topic']['id']), array('confirm' => __d('forum', 'Are you sure you want to delete?', true)));
						$links[] = $html->link(__d('forum', 'Report Topic', true), array('controller' => 'topics', 'action' => 'report', $topic['Topic']['id']));
					} else {
						$links[] = $html->link(__d('forum', 'Edit', true), array('controller' => 'posts', 'action' => 'edit', $post['Post']['id']));
						$links[] = $html->link(__d('forum', 'Delete', true), array('controller' => 'posts', 'action' => 'delete', $post['Post']['id']), array('confirm' => __d('forum', 'Are you sure you want to delete?', true)));
						$links[] = $html->link(__d('forum', 'Report Post', true), array('controller' => 'posts', 'action' => 'report', $post['Post']['id']));
					}
				}
				
				if ($cupcake->hasAccess($topic['ForumCategory']['accessReply'])) {
					$links[] = $html->link(__d('forum', 'Quote', true), array('controller' => 'posts', 'action' => 'add', $topic['Topic']['id'], $post['Post']['id']));
				}
				
				if (!empty($links)) {
					echo implode(' - ', $links);
				}
			} ?>
        </td>
    </tr>
    <tr>
    	<td valign="top" style="width: 25%">
        	<h4><?php echo $html->link($post['User']['username'], array('controller' => 'users', 'action' => 'profile', $post['User']['id'])); ?></h4>
        	<?php if (!empty($post['User']['Access'][0]['AccessLevel']['title'])) { ?>
        	<p><strong><?php echo $post['User']['Access'][0]['AccessLevel']['title']; ?></strong></p>
        	<?php } ?>

			<?php // Gravatar
			if ($cupcake->settings['enable_gravatar'] == 1) {
				if ($avatar = $cupcake->gravatar($post['User']['email'])) { ?>
			<p><?php echo $avatar; ?></p>
			<?php } } ?>
        	
        	<strong><?php __d('forum', 'Joined'); ?>:</strong> <?php echo $time->niceShort($post['User']['created'], $cupcake->timezone()); ?><br />
            <strong><?php __d('forum', 'Total Topics'); ?>:</strong> <?php echo number_format($post['User']['totalTopics']); ?><br />
            <strong><?php __d('forum', 'Total Posts'); ?>:</strong> <?php echo number_format($post['User']['totalPosts']); ?>
        </td>
        <td valign="top">
			<?php $decoda->parse($post['Post']['content']); ?>
            
            <?php if (!empty($post['User']['signature'])) { ?>
            <div class="signature">
            	<?php $decoda->parse($post['User']['signature'], false, array('b', 'i', 'u', 'img', 'url', 'align', 'color', 'size', 'code')); ?>
            </div>
            <?php } ?>
       	</td>
 	</tr>
    <?php } ?>
    
    </table>
    
    <?php echo $this->element('pagination'); ?>
</div>

<?php if ($cupcake->user()) { ?>
<div class="forumOptions">
	<?php if ($cupcake->hasAccess('mod', $topic['ForumCategory']['id'])) {
		echo $html->link(__d('forum', 'Moderate', true), array('controller' => 'topics', 'action' => 'moderate', $topic['Topic']['id']));
	} ?>
	<?php if ($cupcake->hasAccess($topic['ForumCategory']['accessPost'])) {
		echo $html->link(__d('forum', 'Create Topic', true), array('controller' => 'topics', 'action' => 'add', $topic['ForumCategory']['id']));
	} ?>
    <?php if ($cupcake->hasAccess($topic['ForumCategory']['accessPoll'])) {
		echo $html->link(__d('forum', 'Create Poll', true), array('controller' => 'topics', 'action' => 'add', $topic['ForumCategory']['id'], 'poll'));
	} ?>
    <?php if ($cupcake->hasAccess($topic['ForumCategory']['accessReply'])) {
		if ($topic['Topic']['status'] == 0) {
			echo $html->link(__d('forum', 'Post Reply', true), array('controller' => 'posts', 'action' => 'add', $topic['Topic']['id']));
		} else {
			echo '<span>'. __d('forum', 'Closed', true) .'</span>';
		}
	} ?>
</div>

<?php // Quick Reply
if ($cupcake->settings['enable_quick_reply'] == 1) { ?>
<div id="quickReply">
	<h3><?php __d('forum', 'Quick Reply'); ?></h3>
    
    <?php echo $form->create('Post', array('url' => array('controller' => 'posts', 'action' => 'add', $topic['Topic']['id']))); ?>
    <table cellspacing="0" class="table">
    <tr>
    	<td style="width: 25%">
        	<strong><?php echo $form->label('content', __d('forum', 'Message', true) .':'); ?></strong><br /><br />
            
            <?php echo $html->link(__d('forum', 'Advanced Reply', true), array('controller' => 'posts', 'action' => 'add', $topic['Topic']['id'])); ?><br />
            <?php __d('forum', 'BBCode Enabled'); ?>
        </td>
        <td>
			<?php echo $form->input('content', array('type' => 'textarea', 'rows' => 5, 'style' => 'width: 99%', 'div' => false, 'error' => false, 'label' => false)); ?>
       	</td>
  	</tr>
    <tr class="altRow">
    	<td colspan="2" class="ac">
        	<?php echo $form->submit(__d('forum', 'Post Reply', true)); ?>
        </td>
   	</tr> 
    </table>
    <?php echo $form->end(); ?>
</div>
<?php } } ?>
