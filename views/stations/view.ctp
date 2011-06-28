
<?php // Crumbs
$this->Html->addCrumb($settings['site_name'], array('controller' => 'home', 'action' => 'index'));

if (!empty($forum['Parent']['slug'])) {
	$this->Html->addCrumb($forum['Parent']['title'], array('controller' => 'stations', 'action' => 'view', $forum['Parent']['slug']));
}

$this->Html->addCrumb($forum['Forum']['title'], array('controller' => 'stations', 'action' => 'view', $forum['Forum']['slug'])); ?>

<div class="forumHeader">
	<?php if (!$this->Common->user()) { ?>
	<div class="fr">
		<?php echo $this->element('login'); ?>
	</div>
	<?php } ?>

	<h2><?php echo $forum['Forum']['title']; ?></h2>
	<p><?php echo $forum['Forum']['description']; ?></p>
</div>

<?php echo $this->element('forum/forum_controls', array(
	'forum' => $forum
)); ?>

<?php // Sub Forums
if (!empty($forum['SubForum'])) { ?>
	<div class="forumWrap" id="subForums_<?php echo $forum['Forum']['id']; ?>">
		<h3><?php __d('forum', 'Sub-Forums'); ?></h3>

		<table cellspacing="0" class="table">
		<tr>
			<th colspan="2"><?php __d('forum', 'Forum'); ?></th>
			<th><?php __d('forum', 'Topics'); ?></th>
			<th><?php __d('forum', 'Posts'); ?></th>
			<th><?php __d('forum', 'Activity'); ?></th>
		</tr>

		<?php $counter = 0;
		foreach ($forum['SubForum'] as $subForum) {
			echo $this->element('tiles/forum_row', array(
				'forum' => $subForum,
				'counter' => $counter
			));

			++$counter;
		} ?>

		</table>
	</div>
<?php } ?>

<div id="topicWrap">
	<?php echo $this->element('pagination'); ?>
    
	<table cellspacing="0" class="table">
    <tr>
        <th colspan="2"><?php echo $this->Paginator->sort(__d('forum', 'Topic', true), 'Topic.title'); ?></th>
        <th><?php echo $this->Paginator->sort(__d('forum', 'Author', true), 'User.'. $config['userMap']['username']); ?></th>
        <th><?php echo $this->Paginator->sort(__d('forum', 'Created', true), 'Topic.created'); ?></th>
        <th><?php echo $this->Paginator->sort(__d('forum', 'Posts', true), 'Topic.post_count'); ?></th>
        <th><?php echo $this->Paginator->sort(__d('forum', 'Views', true), 'Topic.view_count'); ?></th>
        <th><?php echo $this->Paginator->sort(__d('forum', 'Activity', true), 'LastPost.created'); ?></th>
    </tr>
    
    <?php // Stickies, etc
	if (!empty($stickies)) {
		$counter = 0; ?>

		<tr class="altRow2">
			<td colspan="7"><?php __d('forum', 'Important Topics'); ?></td>
		</tr>

		<?php foreach ($stickies as $topic) {
			echo $this->element('tiles/forum_row', array(
				'counter' => $counter,
				'topic' => $topic
			));

			++$counter;
		} ?>

		<tr class="altRow2">
			<td colspan="7"><?php __d('forum', 'Regular Topics'); ?></td>
		</tr>
    
	<?php }
    
    // Topics
	if (!empty($topics)) {
		$counter = 0;
		
		foreach ($topics as $topic) {
			echo $this->element('tiles/topic_row', array(
				'counter' => $counter,
				'topic' => $topic
			));

			++$counter;
		}
	} else { ?>
    
		<tr>
			<td colspan="7" class="empty"><?php __d('forum', 'There are no topics within this forum category.'); ?></td>
		</tr>
    
    <?php } ?>
    
    </table>
    
    <?php echo $this->element('pagination'); ?>
</div>

<div id="categoryStats">
	<?php echo $this->element('forum/forum_controls', array(
		'forum' => $forum,
		'class' => 'fr'
	)); ?>
    
    <?php // Moderators
	$moderators = array();
	if (!empty($forum['Moderator'])) {
		foreach ($forum['Moderator'] as $mod) {
			$moderators[] = $this->Html->link($mod['User'][$config['userMap']['username']], array('controller' => 'users', 'action' => 'profile', $mod['User']['id'])); 
		}	
	} ?>

	<table cellspacing="5" style="width: 50%">
    <tr>
        <td class="ar"><?php __d('forum', 'Total Topics'); ?>: </td>
        <td><strong><?php echo $forum['Forum']['topic_count']; ?></strong></td>
		
        <td class="ar"><?php __d('forum', 'Increases Post Count'); ?>: </td>
        <td><strong><?php __d('forum', $forum['Forum']['settingPostCount'] ? 'Yes' : 'No'); ?></strong></td>
		
        <td class="ar"><?php __d('forum', 'Can Read Topics'); ?>: </td>
        <td><strong><?php __d('forum', $this->Common->hasAccess($forum['Forum']['accessRead']) ? 'Yes' : 'No'); ?></strong></td>
		
        <td class="ar"><?php __d('forum', 'Can Create Topics'); ?>: </td>
        <td><strong><?php __d('forum', $this->Common->hasAccess($forum['Forum']['accessPost']) ? 'Yes' : 'No'); ?></strong></td>
    </tr>
    <tr>
        <td class="ar"><?php __d('forum', 'Total Posts'); ?>: </td>
        <td><strong><?php echo $forum['Forum']['post_count']; ?></strong></td>
		
        <td class="ar"><?php __d('forum', 'Auto-Lock Topics'); ?>: </td>
        <td><strong><?php __d('forum', $forum['Forum']['settingAutoLock'] ? 'Yes' : 'No'); ?></strong></td>
		
        <td class="ar"><?php __d('forum', 'Can Reply'); ?>: </td>
        <td><strong><?php __d('forum', $this->Common->hasAccess($forum['Forum']['accessReply']) ? 'Yes' : 'No'); ?></strong></td>
		
        <td class="ar"><?php __d('forum', 'Can Create Polls'); ?>: </td>
        <td><strong><?php __d('forum', $this->Common->hasAccess($forum['Forum']['accessPoll']) ? 'Yes' : 'No'); ?></strong></td>
    </tr>
    <?php if (!empty($moderators)) { ?>
    <tr>
    	<td class="ar"><?php __d('forum', 'Moderators'); ?>: </td>
        <td colspan="7"><?php echo implode(', ', $moderators); ?></td>
   	</tr>
    <?php } ?>
    </table>
</div>
