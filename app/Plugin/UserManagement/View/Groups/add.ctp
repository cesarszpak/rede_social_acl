<div class="groups form">
<?php echo $this->Form->create('Group'); ?>
	<fieldset>
		<legend><?php echo __('Add Group'); ?></legend>
	<?php
		echo $this->Form->input('name');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Groups'), array('plugin'=>'UserManagement','controller' => 'Groups','action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Users'), array('plugin'=>'UserManagement','controller' => 'Users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User'), array('plugin'=>'UserManagement','controller' => 'Users', 'action' => 'add')); ?> </li>
	</ul>
</div>
