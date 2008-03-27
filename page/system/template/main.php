<?php System::header();?>
<?php echo menu($module->L('admin/@title').'|'.url('System','Main').'|true;'.$module->L('log/@title').'|'.url('System','Log'));?>
<div class="content">
    <table class="main">
        <tr>
            <th><?php echo $module->L('parameters/systemname');?></th>
            <td>-&rsaquo; <?php echo $module->system['systemname'];?></td>
        </tr>
        <tr>
            <th><?php echo $module->L('parameters/systemver');?></th>
            <td>-&rsaquo; <?php echo $module->system['systemver'];?></td>
        </tr>
        <tr>
            <th><?php echo $module->L('parameters/newversion');?></th>
            <td>-&rsaquo; <span id="verison"><img src="../system/images/loading.gif" border="0" /></span></td>
        </tr>
        <tr>
            <th><?php echo $module->L('parameters/dbver');?></th>
            <td>-&rsaquo; <?php echo $module->system['dbversion'];?></td>
        </tr>
        <tr>
            <th><?php echo $module->L('parameters/instdate');?></th>
            <td>-&rsaquo; <?php echo date('Y-m-d H:i:s',$module->system['instdate']);?></td>
        </tr>
        <tr>
            <th><?php echo $module->L('parameters/osite');?></th>
            <td>-&rsaquo; <a href="http://www.lazycms.net" target="_blank">www.LazyCMS.net</a></td>
        </tr>
        <tr>
            <th><?php echo $module->L('parameters/phpver');?></th>
            <td>-&rsaquo; <?php echo PHP_VERSION;?></td>
        </tr>
        <tr>
            <th><?php echo $module->L('parameters/xml_set_object');?></th>
            <td>-&rsaquo; <?php echo isOK(function_exists('xml_set_object'));?> <span class="gray">(xml_set_object)</span></td>
        </tr>
        <tr>
            <th><?php echo $module->L('parameters/gdver');?></th>
            <td>-&rsaquo; <?php echo isOK(function_exists('gd_info')).$gdInfo;?></td>
        </tr>
        <tr>
            <th><?php echo $module->L('parameters/mysql');?></th>
            <td>-&rsaquo; <?php echo $mysql;?></td>
        </tr>
        <tr>
            <th><?php echo $module->L('parameters/safe_mode');?></th>
            <td>-&rsaquo; <?php echo isOK(!get_cfg_var('safe_mode'));?> <span class="gray">(safe_mode)</span></td>
        </tr>
        <tr>
            <th><?php echo $module->L('parameters/max_execution_time');?></th>
            <td>-&rsaquo; <?php echo get_cfg_var('max_execution_time');?> second <span class="gray">(max_execution_time)</span></td>
        </tr>
        <tr>
            <th><?php echo $module->L('parameters/upload_max_filesize');?></th>
            <td>-&rsaquo; <?php echo get_cfg_var('upload_max_filesize');?> <span class="gray">(upload_max_filesize)</span></td>
        </tr>
        <tr>
            <th><?php echo $module->L('parameters/allow_url_fopen');?></th>
            <td>-&rsaquo; <?php echo isOK(get_cfg_var('allow_url_fopen'));?> <span class="gray">(allow_url_fopen)</span></td>
        </tr>
    </table>
</div>
<?php System::footer();?>