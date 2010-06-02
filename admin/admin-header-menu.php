<ul id="admin-menu">
    <?php 
    /**
     * 系统菜单
     *
     * @author  Lukin <my@lukin.cn>
     */
    admin_menu(array(

        'cpanel' => array(_('Control Panel'),'index.php','a1'),

        '------------------------------------------------------------',
        
        'posts' => array(_('Posts'),'post.php','a2',array(
            array(_('Edit'),'post.php','post-list'),
            array(__('Add New','post'),'post.php?action=new','post-new'),
            array(_('Categories'),'categories.php','categories'),
        )),
        
        'models' => array(_('Models'),'model.php','a7',array(
            array(_('Edit'),'model.php','model-list'),
            array(__('Add New','model'),'model.php?action=new','model-new'),
            array(__('Import','model'),'model.php?action=import','model-import'),
        )),
        
        'fragments' => array(_('Fragment'),'fragment.php','a6',array(
            array(_('Edit'),'fragment.php','fragment-list'),
            array(__('Add New','fragment'),'fragment.php?action=new','fragment-new'),
        )),
        
        '------------------------------------------------------------',
        
        'users' => array(_('Users'),'user.php','a4',array(
            array(_('Users'),'user.php','user-list'),
            array(__('Add New','user'),'user.php?action=new','user-new'),
            array(_('Your Profile'),'profile.php'),
        )),
        
        'modules' => array(_('Modules'),'module.php','a3',array(
            array(_('Edit'),'module.php','module-list'),
            array(__('Add New','module'),'module.php?action=new','module-new'),
        )),
        
        'tools' => array(_('Tools'),'tools.php','a8',array(
            array(_('Upgrade'),'upgrade.php','upgrade'),
        )),

        'options'  => array(_('Settings'),'options.php','a5',array(
            array(_('General'),'options.php','option-general'),
            array(_('Writing'),'options.php?action=writing','option-writing'),
        )),
    ));
    ?>
</ul>
<script type="text/javascript">$('#admin-menu').init_menu();</script>