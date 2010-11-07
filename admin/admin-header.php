<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo esc_html(strip_tags(admin_head('title')));?> &lsaquo; <?php echo C('SiteTitle');?>  &#8212; LazyCMS</title>
<?php
// 加载核心CSS
admin_css('css/admin');
// 加载模块CSS
admin_css(admin_head('styles'));
// 加载JS核心库
admin_script('js/common');
// 输出js语言包
echo '<script type="text/javascript">'.admin_jslang().'</script>';
// 加载模块JS库
admin_script(admin_head('scripts'));
?>
<script type="text/javascript">
//<![CDATA[
window.addLoadEvent=function(a){if(typeof jQuery!='undefined')jQuery(document).ready(a);else if(typeof LazyOnload!='function')LazyOnload=a;else{var b=LazyOnload;LazyOnload=function(){b();a()}}};
<?php
// 执行事件
$loadevents = admin_head('loadevents');
if ($loadevents) {
    if (is_array($loadevents)) {
    	foreach ($loadevents as $event) {
    		echo "addLoadEvent({$event});";
    	}
    } else {
        echo "addLoadEvent({$loadevents});";
    }
}
// 检查是否有需要生成的进度
if (publish_check_process()) echo "addLoadEvent(common_publish);";
?>
//]]>
</script>
</head>

<body>
<div id="wrapper">
    <div id="header">
        <img id="header-logo" src="<?php echo ADMIN_ROOT.'images/logo.png';?>" alt="LazyCMS <?php echo LAZY_VERSION;?>" />
        <h1 id="header-visit">
            <a href="<?php echo WEB_ROOT;?>" target="_blank">
                <span><?php echo C('SiteTitle');?></span>
                <em><?php _e('Visit Site');?></em>
            </a>
        </h1>
        <div id="header-menu"><strong><a href="<?php echo ADMIN_ROOT.'profile.php';?>"><?php echo $_USER['name'];?></a></strong>
             | <a href="<?php echo ADMIN_ROOT.'login.php?method=logout';?>" onclick="return $(this).logout();"><?php _e('Logout');?></a>
        </div>
    </div>

    <div id="admin-body">
        <?php require(ADMIN_PATH.'/admin-header-menu.php');?>
        <div id="admin-content">
            
