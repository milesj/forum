
<h2><?php __d('forum', 'Delete Forum Category'); ?></h2>

<?php if (empty($categories)) { ?>
<p><?php __d('forum', 'Cannot process deletion, you must have atleast 1 active category.'); ?></p>

<?php } else { ?>
<p><?php printf(__d('forum', 'Before you delete the category %s , please select which forum all topics should be moved to upon deletion. Additionally, any sub-forums will be moved to the main parent forum and any moderators will be deleted.', true), '<strong>'. $category['ForumCategory']['title'] .'</strong>'); ?></p>

	<?php echo $form->create('ForumCategory', array('url' => array('controller' => 'categories', 'action' => 'delete_category', $id, 'admin' => true)));
	echo $form->input('category_id', array('options' => $categories, 'escape' => false, 'label' => __d('forum', 'Move Topics To', true)));
	echo $form->end(__d('forum', 'Delete', true));
}?>