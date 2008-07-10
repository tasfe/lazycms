<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
</head>

<body>
<form id="form1" name="form1" method="post" action="">
  <p>
    <input name="title" type="text" id="title" size="30" value="<?php echo $_POST['title'];?>" />
  </p>
  <p>
    <textarea name="content" cols="70" rows="25" id="content"><?php echo $_POST['content'];?></textarea>
  </p>
  <p>
    <input type="submit" name="Submit" value="提交" />
  </p></form>
  <?php
if (isset($_POST['title'])) {
    $title = rawurlencode($_POST['title']);
    $content = rawurlencode($_POST['content']);
    $out = file_get_contents("http://keyword.discuz.com/related_kw.html?ics=utf-8&ocs=utf-8&title={$title}&content={content}");
    echo htmlspecialchars($out);
}
?>


</body>
</html>
