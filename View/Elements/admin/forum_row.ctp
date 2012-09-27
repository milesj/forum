<tr id="category_<?php echo $forum['id']; ?>">
	<?php if (isset($child)) { ?>
		<td>&nbsp;</td>
    	<td style="width: 35px">
			<?php
			echo $this->Form->input('Forum.' . $forum['id'] . '.orderNo', array('value' => $forum['orderNo'], 'div' => false, 'label' => false, 'style' => 'width: 20px', 'maxlength' => 2, 'class' => 'align-center'));
			echo $this->Form->input('Forum.' . $forum['id'] . '.id', array('value' => $forum['id'], 'type' => 'hidden')); ?>
        </td>
        <td>
			<strong><?php echo $this->Html->link($forum['title'], array('action' => 'edit', $forum['id'])); ?></strong>
		</td>

    <?php } else { ?>
		<td class="icon">
			<?php
			echo $this->Form->input('Forum.' . $forum['id'] . '.orderNo', array('value' => $forum['orderNo'], 'div' => false, 'label' => false, 'style' => 'width: 20px', 'maxlength' => 2, 'class' => 'align-center'));
			echo $this->Form->input('Forum.' . $forum['id'] . '.id', array('value' => $forum['id'], 'type' => 'hidden')); ?>
		</td>
		<td colspan="2">
			<strong><?php echo $this->Html->link($forum['title'], array('action' => 'edit', $forum['id'])); ?></strong>
		</td>
	<?php } ?>

	<td class="align-center"><?php echo $this->Common->options('forumStatus', $forum['status']); ?></td>
	<td class="stat"><?php echo number_format($forum['topic_count']); ?></td>
	<td class="stat"><?php echo number_format($forum['post_count']); ?></td>
	<td class="align-center"><?php echo $this->Common->options('access', $forum['accessRead'], true); ?></td>
	<td class="align-center"><?php echo $this->Common->options('access', $forum['accessPost']); ?></td>
	<td class="align-center"><?php echo $this->Common->options('access', $forum['accessReply']); ?></td>
	<td class="align-center"><?php echo $this->Common->options('access', $forum['accessPoll']); ?></td>
	<td class="align-center gray">
		<?php echo $this->Html->link(__d('forum', 'Edit'), array('action' => 'edit', $forum['id'])); ?> -
		<?php echo $this->Html->link(__d('forum', 'Delete'), array('action' => 'delete', $forum['id'])); ?>
	</td>
</tr>