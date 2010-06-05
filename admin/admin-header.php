<?php
$title   = esc_html(strip_tags(admin_head('title')));
$styles  = admin_head('styles')?admin_head('styles'):array();
$scripts = admin_head('scripts')?admin_head('scripts'):array();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $title;?> &lsaquo; <?php echo C('SiteName');?>  &#8212; LazyCMS</title>
<?php
// 加载CSS
call_user_func_array('admin_css',array_merge(array('css/admin'),$styles));
// 加载JS
call_user_func_array('admin_script',array_merge(array('js/admin'),$scripts));
// 执行事件
$loadevents = admin_head('loadevents');
if ($loadevents) {
    echo '<script type="text/javascript">',"\n",'//<![CDATA[',"\n";
    if (is_array($loadevents)) {
    	foreach ($loadevents as $event) {
    		echo "LazyCMS.addLoadEvent({$event});\n";
    	}
    } else {
        echo "LazyCMS.addLoadEvent({$loadevents});\n";
    }
    echo '//]]>',"\n",'</script>',"\n";
}
?>
</head>

<body>
<div id="wrapper">
    <div id="header">
        <img id="header-logo" src="<?php echo ADMIN_ROOT.'images/logo.png';?>" alt="LazyCMS <?php echo LAZY_VERSION;?>" />
        <h1 id="header-visit">
            <a href="<?php echo WEB_ROOT;?>" target="_blank">
                <span><?php echo C('SiteName');?></span>
                <em><?php _e('Visit Site');?></em>
            </a>
        </h1>
        <div id="header-menu"><strong><a href="<?php echo ADMIN_ROOT.'profile.php';?>"><?php echo $_ADMIN['name'];?></a></strong>
             | <a href="<?php echo ADMIN_ROOT.'login.php?action=logout';?>" onclick="return $(this).logout();"><?php _e('Logout');?></a>
        </div>
    </div>

    <div id="admin-body">
        <?php require(ADMIN_PATH.'/admin-header-menu.php');?>
        <div id="admin-content">
            
