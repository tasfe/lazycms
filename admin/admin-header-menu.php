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
            array(__('Posts'),'post.php','post-list'),
            array(_x('Add New','post'),'post.php?action=new','post-new'),
            array(__('Categories'),'categories.php','categories'),
        )),

        'topics' => array(__('Topics'),'topic.php','a6',array(
            array(__('Topics'),'topic.php','topic-list'),
            array(_x('Add New','topic'),'topic.php?action=new','topic-new'),
        )),

        'models' => array(__('Models'),'model.php','a7',array(
            array(__('Models'),'model.php','model-list'),
            array(_x('Add New','model'),'model.php?action=new','model-new'),
            array(_x('Import','model'),'model.php?action=import','model-import'),
        )),
        
        '------------------------------------------------------------',
        
        'users' => array(__('Users'),'user.php','a4',array(
            array(__('Users'),'user.php','user-list'),
            array(_x('Add New','user'),'user.php?action=new','user-new'),
            array(__('Your Profile'),'profile.php'),
        )),
        /*
        'modules' => array(__('Modules'),'module.php','a3',array(
            array(__('Modules'),'module.php','module-list'),
            array(_x('Add New','module'),'module.php?action=new','module-new'),
        )),
        
        'tools' => array(__('Tools'),'tools.php','a8',array(
            array(__('Upgrade'),'upgrade.php','upgrade'),
        )),
        */
        'options'  => array(__('Settings'),'options/general.php','a5',array(
            array(__('General'),'options/general.php','option-general'),
        )),
    ));
    ?>
</ul>
<script type="text/javascript">$('#admin-menu').init_menu();</script>