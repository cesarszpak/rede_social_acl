<div class="users index">
    <?php
    $name=ucfirst($userinfo['username']);
    $picture='';
    $userdetails=json_decode($userinfo['social_info']);
    if($userinfo['social_connect']=='facebook'){
        $name=$userdetails->first_name.' '.$userdetails->last_name ;
        $picture="https://graph.facebook.com/". $userdetails->id ."/picture";
    }elseif($userinfo['social_connect']=='google'){
        $name=$userdetails->name;
        $picture=$userdetails->picture;

    }elseif($userinfo['social_connect']=='twitter'){
        $name=$userdetails->name;
        $picture=$userdetails->profile_image_url_https;

    }elseif($userinfo['social_connect']=='linkedin'){
        $name=$userdetails->firstName ;
        $picture=$userdetails->pictureUrl;
    }
    ?>
    <h2><?php echo __('Welcome '.$name);?></h2>
    <?php if(isset($picture) && $picture!=''){?>
        <img src="<?php echo $picture;?>" width="100">
    <?php } ?>
</div>
<?php if(isset($userinfo) && $userinfo['group_id']==1){?>
    <div class="actions">
        <h3><?php echo __('Actions'); ?></h3>
        <ul>
            <li><?php echo $this->Html->link(__('Users'), array('plugin'=>'UserManagement','controller' => 'Users','action' => 'index')); ?></li>
            <li><?php echo $this->Html->link(__('Groups'), array('plugin'=>'UserManagement','controller' => 'Groups', 'action' => 'index')); ?> </li>
            <li><?php echo $this->Html->link(__('New Group'), array('plugin'=>'UserManagement','controller' => 'Groups', 'action' => 'add')); ?> </li>
            <li><?php echo $this->Html->link(__('Group Permissions'), array('plugin'=>'UserManagement','controller' => 'Users', 'action' => 'permissions')); ?> </li>
        </ul>
    </div>
<?php } ?>
