<?php
$forum = isset($forum['Forum']) ? $forum['Forum'] : $forum;
$subForums = array();

if (isset($forum['Children'])) {
    foreach ($forum['Children'] as $sub) {
        $subForums[] = $this->Html->link($sub['title'], array('controller' => 'stations', 'action' => 'view', $sub['slug']));
    }
} ?>

<tr id="forum-<?php echo $forum['id']; ?>">
    <td class="col-icon">
        <?php echo $this->Forum->forumIcon($forum); ?>
    </td>
    <td>
        <?php echo $this->Html->link($forum['title'], array('controller' => 'stations', 'action' => 'view', $forum['slug']), array('class' => 'forum-title')); ?>

        <div class="forum-desc">
            <?php echo h($forum['description']); ?>
        </div>

        <?php if ($subForums) { ?>
            <div class="forum-children">
                <span><?php echo __d('forum', 'Sub-Forums'); ?>:</span> <?php echo implode(', ', $subForums); ?>
            </div>
        <?php } ?>
    </td>
    <td class="col-stat"><?php echo number_format($forum['topic_count']); ?></td>
    <td class="col-stat"><?php echo number_format($forum['post_count']); ?></td>
    <td class="col-activity">
        <?php if (!empty($forum['LastTopic']['id'])) {
            $lastTime = isset($forum['LastPost']['created']) ? $forum['LastPost']['created'] : $forum['LastTopic']['modified'];

            echo $this->Html->link($forum['LastTopic']['title'], array('controller' => 'topics', 'action' => 'view', $forum['LastTopic']['slug'])) . ' ';
            echo $this->Html->link('<span class="fa fa-external-link"></span>', array('controller' => 'topics', 'action' => 'view', $forum['LastTopic']['slug'], 'page' => $forum['LastTopic']['page_count'], '#' => 'post-' . $forum['lastPost_id']), array('escape' => false)); ?><br>

            <em><?php echo $this->Time->timeAgoInWords($lastTime, array('timezone' => $this->Forum->timezone())); ?></em>

            <?php if (!empty($forum['LastUser']['id'])) { ?>
                <span class="text-muted"><?php echo __d('forum', 'by'); ?> <?php echo $this->Html->link($forum['LastUser'][$userFields['username']], $this->Forum->profileUrl($forum['LastUser'])); ?></span>
            <?php }
        } else { ?>
            <em class="text-muted"><?php echo __d('forum', 'No latest activity to display'); ?></em>
        <?php } ?>
    </td>
</tr>