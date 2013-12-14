<?php

$this->Breadcrumb->add(__d('forum', 'Search'), array('controller' => 'search', 'action' => 'index')); ?>

<div class="title">
    <h2><?php echo __d('forum', 'Search'); ?></h2>
</div>

<div class="container">
    <?php echo $this->Form->create('Topic', array('class' => 'form--inline', 'url' => array('controller' => 'search', 'action' => 'proxy'))); ?>

    <div class="form-filters" id="search">
        <?php
        echo $this->Form->input('keywords', array('div' => 'field', 'label' => __d('forum', 'With keywords')));
        echo $this->Form->input('forum_id', array('div' => 'field', 'label' => __d('forum', 'in forum'), 'options' => $forums, 'empty' => true));
        echo $this->Form->input('byUser', array('div' => 'field', 'label' => __d('forum', 'by user')));
        echo $this->Form->input('orderBy', array('div' => 'field', 'label' => __d('forum', 'order by'), 'options' => $orderBy)); ?>
    </div>

    <?php
    echo $this->Form->submit(__d('forum', 'Search Topics'), array('class' => 'button'));
    echo $this->Form->end();

    if ($searching) {
        echo $this->element('Admin.pagination', array('class' => 'top')); ?>

        <div class="panel">
            <div class="panel-body">
                <table class="table table--hover table--sortable">
                    <thead>
                        <tr>
                            <th colspan="2"><?php echo $this->Paginator->sort('Topic.title', __d('forum', 'Topic')); ?></th>
                            <th><?php echo $this->Paginator->sort('Topic.forum_id', __d('forum', 'Forum')); ?></th>
                            <th><?php echo $this->Paginator->sort('User.' . $userFields['username'], __d('forum', 'Author')); ?></th>
                            <th><?php echo $this->Paginator->sort('Topic.created', __d('forum', 'Created')); ?></th>
                            <th><?php echo $this->Paginator->sort('Topic.post_count', __d('forum', 'Posts')); ?></th>
                            <th><?php echo $this->Paginator->sort('Topic.view_count', __d('forum', 'Views')); ?></th>
                            <th><?php echo $this->Paginator->sort('LastPost.created', __d('forum', 'Activity')); ?></th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php if (!$topics) { ?>

                        <tr>
                            <td colspan="8" class="no-results"><?php echo __d('forum', 'No results were found, please refine your search criteria'); ?></td>
                        </tr>

                    <?php } else {
                        foreach ($topics as $counter => $topic) {
                            echo $this->element('tiles/topic_row', array(
                                'topic' => $topic,
                                'counter' => $counter,
                                'columns' => array('forum')
                            ));
                        }
                    } ?>

                    </tbody>
                </table>
            </div>
        </div>

        <?php echo $this->element('Admin.pagination', array('class' => 'bottom'));
    } ?>
</div>