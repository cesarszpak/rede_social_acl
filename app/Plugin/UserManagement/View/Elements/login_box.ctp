<div class="social_btn_container">
    <div class="social_login_buttons">
        <?php echo $this->Html->image('UserManagement.facebook.png',array('id'=>'facebook')); ?>
    </div>
    <div class="social_login_buttons">
        <?php echo $this->Html->image('UserManagement.google.png',array('id'=>'google')); ?>
    </div>
    <div class="social_login_buttons">
        <?php echo $this->Html->image('UserManagement.twitter.png',array('id'=>'twitter')); ?>
    </div>
    <div class="social_login_buttons">
        <?php echo $this->Html->image('UserManagement.linkedin.png',array('id'=>'linkedin')); ?>
    </div>

</div>



<script type="text/javascript">
    var fbpath='<?php echo Router::url(array('plugin'=>'UserManagement','controller' => 'Social', 'action' => 'facebookLogin'),true); ?>';
    var twpath='<?php echo Router::url(array('plugin'=>'UserManagement','controller' => 'Social', 'action' => 'twitterLogin'),true); ?>';
    var ldpath='<?php echo Router::url(array('plugin'=>'UserManagement','controller' => 'Social', 'action' => 'linkedinLogin'),true); ?>';
    var gpath='<?php echo Router::url(array('plugin'=>'UserManagement','controller' => 'Social', 'action' => 'googleLogin'),true); ?>';

</script>
<?php echo $this->Html->script(array('UserManagement.oauthpopup.js','UserManagement.social-login.js')); ?>