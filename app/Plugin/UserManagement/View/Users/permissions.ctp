<?php echo $this->Html->css('UserManagement.custom.css');?>
<div class="permissions">
    <h2><?php echo __('Set group permission'); ?></h2>
    ** Administrator can access every pages. By default no one can access any methods. You need to check the box if you are giving permission for some groups. Hover on Controller and Method name to know its comments(definations)
    <?php if(!empty($groups)){?>
    <table class="permission_box" cellpadding="0" cellspacing="0">
        <tr>
            <th>Controller methods</th>
            <?php foreach($groups as $key=>$value){ ?>
                <th><?php echo $value?></th>
            <?php } ?>
        </tr>
        <?php if(!empty($allControllers)){?>
            <?php foreach($allControllers as $allControllerskey=>$allControllersvalue){
                $reflector = new ReflectionClass($allControllerskey.'Controller');
                ?>
                <tr>
                    <td colspan="<?php echo count($groups)+1;?>" title="<?php echo $reflector->getDocComment();?>"><h4>Controller: <?php echo $allControllerskey; ?> </h4></td>
                </tr>
                <?php foreach($allControllersvalue as $actions){ ?>
                <tr>
                    <td title="<?php echo $reflector->getMethod($actions)->getDocComment();?>"><?php echo $actions?></td>
                    <?php

                    foreach($groups as $k=>$v){
                        $checked="";
                        if(isset($gPermissions[$allControllerskey.','.$actions])){
                        $checkcode=$this->Permission->getAccessCodes($gPermissions[$allControllerskey.','.$actions]);
                        if(!empty($checkcode) && in_array($k,$checkcode)){
                            $checked="checked";
                        }
                        }
                        ?>
                        <td><input type="checkbox" class="save_permissions" id="<?php echo $allControllerskey.','.$actions.','.$k;?>" <?php echo $checked;?>></td>
                    <?php } ?>
                </tr>
        <?php } } } ?>
        <?php if(!empty($pluginsControllers)){?>
            <?php foreach($pluginsControllers as $pluginsControllerskey=>$pluginsControllersvalue){ ?>
                <tr>
                    <td colspan="<?php echo count($groups)+1;?>"><h3>Plugin: <?php echo $pluginsControllerskey; ?></h3> </td>
                </tr>
                <?php foreach($pluginsControllersvalue as $controllernames => $actionsvalue){
                    $reflector = new ReflectionClass($controllernames.'Controller');
                    ?>
                    <tr>
                        <td colspan="<?php echo count($groups)+1;?>" title="<?php echo $reflector->getDocComment();?>"><h4>Controller: <?php echo $controllernames;?> </h4></td>
                    </tr>
                    <?php foreach($actionsvalue as $actions){ ?>
                        <tr>
                            <td title="<?php echo $reflector->getMethod($actions)->getDocComment();?>"><?php echo $actions;?></td>
                            <?php
                            foreach($groups as $k=>$v){
                                $checked="";
                                if(isset($gPermissions[$pluginsControllerskey.','.$controllernames.','.$actions])){
                                $checkcode=$this->Permission->getAccessCodes($gPermissions[$pluginsControllerskey.','.$controllernames.','.$actions]);
                                if(!empty($checkcode) && in_array($k,$checkcode)){
                                    $checked="checked";
                                }
                                }
                                ?>
                                <td><input type="checkbox" class="save_permissions" id="<?php echo $pluginsControllerskey.','.$controllernames.','.$actions.','.$k;?>" <?php echo $checked;?>></td>
                            <?php } ?>
                        </tr>
        <?php } } } }?>


    </table>
    <?php } ?>

</div>
<script>
    var url ="<?php echo Router::url('/',true); ?>";
</script>
<?php echo $this->Html->script('UserManagement.permission.js');?>