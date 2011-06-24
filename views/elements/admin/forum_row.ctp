
<tr id="category_<?php echo $forum['id']; ?>">
	<?php if (isset($child)) { ?>
		<td>&nbsp;</td>
    	<td style="width: 35px">
			<?php // Order
			echo $this->Form->input('Forum.'. $forum['id'] .'.orderNo', array('value' => $forum['orderNo'], 'div' => false, 'label' => false, 'style' => 'width: 30px'));
			echo $this->Form->input('Forum.'. $forum['id'] .'.id', array('value' => $forum['id'], 'type' => 'hidden')); ?>
        </td>
        <td><strong><?php echo $this->Html->link($forum['title'], array('action' => 'edit', $forum['id'])); ?></strong></td>
    <?php } else { ?>
		<td>
			<?php // Order
			echo $this->Form->input('Forum.'. $forum['id'] .'.orderNo', array('value' => $forum['orderNo'], 'div' => false, 'label' => false, 'style' => 'width: 30px'));
			echo $this->Form->input('Forum.'. $forum['id'] .'.id', array('value' => $forum['id'], 'type' => 'hidden')); ?>
		</td>
		<td colspan="2"><strong><?php echo $this->Html->link($forum['title'], array('action' => 'edit', $forum['id'])); ?></strong></td>
	<?php } ?>
	<td class="ac"><?php echo $this->Common->options(2, $forum['status']); ?></td>
	<td class="ac"><?php echo number_format($forum['topic_count']); ?></td>
	<td class="ac"><?php echo number_format($forum['post_count']); ?></td>
	<td class="ac"><?php echo $this->Common->options(4, $forum['accessRead'], true); ?></td>
	<td class="ac"><?php echo $this->Common->options(4, $forum['accessPost']); ?></td>
	<td class="ac"><?php echo $this->Common->options(4, $forum['accessReply']); ?></td>
	<td class="ac"><?php echo $this->Common->options(4, $forum['accessPoll']); ?></td>
	<td class="ac gray">
		<?php echo $this->Html->link(__d('forum', 'Edit', true), array('action' => 'edit', $forum['id'])); ?> -
		<?php echo $this->Html->link(__d('forum', 'Delete', true), array('action' => 'delete', $forum['id'])); ?>
	</td>
</tr>