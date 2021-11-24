<?php
//--- UPDATE CREATED BY ---
set_time_limit(0);
$ignoreAuth = true;
include("../../../../config/globals.php");
////


$q="
CREATE TABLE `chart_usr_roles` (
  `id` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `role_type` int(10) NOT NULL,
  `logged_usr_type` int(2) NOT NULL,
  `create_dt` datetime NOT NULL,
  `modi_dt` datetime NOT NULL,
  `form_id` int(10) NOT NULL,
  `del_by` int(2) NOT NULL
) ENGINE=MyISAM;
";
imw_query($q) or $msg_info[] = imw_error();

$q="
ALTER TABLE `chart_usr_roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `form_id` (`form_id`);
";
imw_query($q) or $msg_info[] = imw_error();

$q="
ALTER TABLE `chart_usr_roles`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
";
imw_query($q) or $msg_info[] = imw_error();



?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Update 32</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
<font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
    <?php echo(@implode("<br>",$msg_info));?>
</font>

</body>
</html>