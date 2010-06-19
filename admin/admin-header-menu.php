<ul id="admin-menu">
    <?php 
    /**
     * 系统菜单
     *
     * @author  Lukin <my@lukin.cn>
     */
    admin_menu(array(

        'cpanel' => array(__('Control Panel'),'index.php','a1'),

        '------------------------------------------------------------',
        
        'posts' => array(__('Posts'),'post.php','a2',array(
            array(__('Edit'),'post.php','post-list'),
            array(_x('Add New','post'),'post.php?action=new','post-new'),
            array(__('Categories'),'categories.php','categories'),
        )),
        
        'models' => array(__('Models'),'model.php','a7',array(
            array(__('Edit'),'model.php','model-list'),
            array(_x('Add New','model'),'model.php?action=new','model-new'),
            array(_x('Import','model'),'model.php?action=import','model-import'),
        )),
        
        'fragments' => array(__('Fragment'),'fragment.php','a6',array(
            array(__('Edit'),'fragment.php','fragment-list'),
            array(_x('Add New','fragment'),'fragment.php?action=new','fragment-new'),
        )),
        
        '------------------------------------------------------------',
        
        'users' => array(__('Users'),'user.php','a4',array(
            array(__('Users'),'user.php','user-list'),
            array(_x('Add New','user'),'user.php?action=new','user-new'),
            array(__('Your Profile'),'profile.php'),
        )),
        
        'modules' => array(__('Modules'),'module.php','a3',array(
            array(__('Edit'),'module.php','module-list'),
            array(_x('Add New','module'),'module.php?action=new','module-new'),
        )),
        
        'tools' => array(__('Tools'),'tools.php','a8',array(
            array(__('Upgrade'),'upgrade.php','upgrade'),
        )),

        'options'  => array(__('Settings'),'options.php','a5',array(
            array(__('General'),'options.php','option-general'),
            array(__('Writing'),'options.php?action=writing','option-writing'),
        )),
    ));
    ?>
</ul>
<script type="text/javascript">$('#admin-menu').init_menu();</script>