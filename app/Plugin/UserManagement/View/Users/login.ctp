<?php

echo $this->Html->css('UserManagement.custom.css');

echo $this->Form->create('User', array('action' => 'login'));
echo $this->Form->inputs(array(
    'legend' => __('Login'),
    'username',
    'password'
));
echo $this->Form->button('Login', array(
    'type' => 'submit',
    'escape' => true,
    'class'=>'user-btn'
));
/*echo "<span style='padding: 10px'>OR</span>";
echo $this->Html->link('Register',array('plugin'=>'UserManagement','controller' => 'Users', 'action' => 'register'), array(
    'escape' => true,
    'class'=>'user-btn',
    'style'=>'padding:11px 20px;'
));*/
echo $this->Form->end();

echo $this->element('UserManagement.login_box');
?>
