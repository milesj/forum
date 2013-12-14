<?php
if (!empty($forum['Parent']['slug'])) {
    $this->Breadcrumb->add($forum['Parent']['title'], array('controller' => 'stations', 'action' => 'view', $forum['Parent']['slug']));
}

$this->Breadcrumb->add($forum['Forum']['title'], array('controller' => 'stations', 'action' => 'view', $forum['Forum']['slug']));
$this->Breadcrumb->add(__d('forum', 'Moderate'), array('action' => 'moderate', $forum['Forum']['slug'])); ?>

<div class="title">
    <div class="action-buttons">
        <?php echo $this->Html->link(__d('forum', 'Return to Forum'), array('controller' => 'stations', 'action' => 'view', $forum['Forum']['slug']), array('class' => 'button')); ?>
    </div>

    <h2><span><?php echo __d('forum', 'Moderate'); ?>:</span> <?php echo h($forum['Forum']['title']); ?></h2>
</div>

<div class="container">
    <?php echo $this->Form->create('Topic', array('class' => 'form--inline'));

    echo $this->element('Admin.pagination', array('class' => 'top')); ?>

    <div class="panel" id="topics">
        <div class="panel-body">
            <table class="table table--hover table--sortable">
                <thead>
                    <tr>
                        <th><span><input type="checkbox" onclick="Forum.toggleCheckboxes(this);"></span></th>
                        <th><?php echo $this->Paginator->sort('Topic.title', __d('forum', 'Topic')); ?></th>
                        <th><?php echo $this->Paginator->sort('Topic.status', __d('forum', 'Status')); ?></th>
                        <th><?php echo $this->Paginator->sort('User.' . $userFields['username'], __d('forum', 'Author')); ?></th>
                        <th><?php echo $this->Paginator->sort('Topic.created', __d('forum', 'Created')); ?></th>
                        <th><?php echo $this->Paginator->sort('Topic.post_count', __d('forum', 'Posts')); ?></th>
                        <th><?php echo $this->Paginator->sort('Topic.view_count', __d('forum', 'Views')); ?></th>
                        <th><?php echo $this->Paginator->sort('LastPost.created', __d('forum', 'Activity')); ?></th>
                    </tr>
                </thead>
                <tbody>

                <?php if ($topics) {
                    foreach ($topics as $counter => $topic) {
                        echo $this->element('tiles/topic_row', array(
                            'topic' => $topic,
                            'counter' => $counter,
                            'columns' => array('status')
                        ));
                    }
                } else { ?>

                    <tr>
                        <td colspan="8" class="no-results"><?php echo __d('forum', 'There are no topics within this forum'); ?></td>
                    </tr>

                <?php } ?>

                </tbody>
            </table>
        </div>
    </div>

    <?php echo $this->element('Admin.pagination', array('class' => 'bottom')); ?>

    <div class="mod-actions">
        <?php
        echo $this->Form->input('action', array(
            'options' => array(
                'open' => __d('forum', 'Open Topic(s)'),
                'close' => __d('forum', 'Close Topic(s)'),
                'move' => __d('forum', 'Move Topic(s)'),
                'delete' => __d('forum', 'Delete Topic(s)')
            ),
            'div' => 'field',
            'label' => __d('forum', 'Perform Action') . ': '
        ));

        echo $this->Form->input('move_id', array('options' => $forums, 'div' => 'field', 'label' => __d('forum', 'Move To') . ': '));
        echo $this->Form->submit(__d('forum', 'Process'), array('div' => false, 'class' => 'button small')); ?>
    </div>

    <?php echo $this->Form->end(); ?>
</div>