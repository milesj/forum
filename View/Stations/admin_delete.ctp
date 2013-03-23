<?php
$this->Admin->setBreadcrumbs($model, $result, $this->action);

$id = $result[$model->alias][$model->primaryKey];
$displayField = $this->Admin->getDisplayField($model, $result); ?>

<h2><?php echo $this->Admin->outputIconTitle($model, __d('admin', 'Delete %s', $model->singularName)); ?></h2>

<p><?php echo __d('admin', 'Are you sure you want to delete %s?', $this->Html->link($displayField, array('action' => 'read', $id, 'model' => $model->urlSlug))); ?></p>

<?php echo $this->element('Admin.crud/dependencies'); ?>

<p>Please provide a destination for all children topics and forums.</p>

<?php // Confirm delete form
echo $this->Form->create($model->alias, array(
	'url' => array('plugin' => 'admin', 'controller' => 'crud', 'model' => $model->urlSlug, 'action' => 'delete', $id),
	'class' => 'form-horizontal'
));

echo $this->element('Admin.input', array(
	'field' => 'move_topics',
	'data' => array(
		'type' => 'list',
		'title' => __d('forum', 'Move Topics To'),
		'null' => false
	)
));

echo $this->element('Admin.input', array(
	'field' => 'move_forums',
	'data' => array(
		'type' => 'list',
		'title' => __d('forum', 'Move Forums To'),
		'null' => false
	)
));

echo $this->element('Admin.form_actions');
echo $this->Form->end(); ?>