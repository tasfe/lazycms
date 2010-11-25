<ul id="admin-menu">
    <?php 
    /**
     * 系统菜单
     *
     * @author  Lukin <my@lukin.cn>
     */
    system_menu(array(

        'cpanel' => array(__('Control Panel'),'index.php','a1',array(
            array(__('Control Panel'),'index.php'),
            array(__('Publish Posts'),'publish.php','publish'),
            array(__('Upgrade'),'upgrade.php','upgrade'),
            array(__('Your Profile'),'profile.php'),
        )),

        '------------------------------------------------------------',
        
        'posts' => array(__('Posts'),'post.php','a2',array(
            array(__('Posts'),'post.php','post-list'),
            array(_x('Add New','post'),'post.php?method=new','post-new'),
            array(__('Categories'),'categories.php','categories'),
        )),

        'pages' => array(__('Pages'),'page.php','b5',array(
            array(__('Pages'),'page.php','page-list'),
            array(_x('Add New','page'),'page.php?method=new','page-new'),
        )),

        'models' => array(__('Models'),'model.php','a7',array(
            array(__('Models'),'model.php','model-list'),
            array(_x('Add New','model'),'model.php?method=new','model-new'),
        )),

        'comments' => array(__('Comments'),'comment.php','c8','comment-list'),
        
        '------------------------------------------------------------',
        
        'users' => array(__('Users'),'user.php','a4',array(
            array(__('Users'),'user.php','user-list'),
            array(_x('Add New','user'),'user.php?method=new','user-new'),
        )),
        
        'plugins' => array(__('Plugins'),'plugins.php','a3',array(
            array(__('Plugins'),'plugins.php','plugin-list'),
            array(_x('Add New','plugin'),'plugins.php?method=new','plugin-new'),
        )),
        
        'tools' => array(__('Tools'),'tools/clean-cache.php','a8',array(
            array(__('Clean cache'),'tools/clean-cache.php','clean-cache'),
        )),

        'options'  => array(__('Settings'),'options/general.php','a5',array(
            array(__('General'),'options/general.php','option-general'),
            array(_x('Posts','setting'),'options/posts.php','option-posts'),
        )),
    ));
    ?>
</ul>
<script type="text/javascript">$('#admin-menu').init_menu();</script>