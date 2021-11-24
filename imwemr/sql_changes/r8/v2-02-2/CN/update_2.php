<?php
$ignoreAuth = true;
set_time_limit(0);
include(dirname(__FILE__)."/../../../../config/globals.php");

$s = array();
$s[] = "
			CREATE TABLE `patient_messages_attachment` (
			`id` int(10) NOT NULL,
			`patient_messages_id` int(10) NOT NULL,
			`file_name` varchar(250) NOT NULL,
			`size` int(10) NOT NULL,
			`mime` varchar(50) NOT NULL,
			`complete_path` tinytext NOT NULL,
			`patient_id` int(10) NOT NULL,
			`op_time` datetime NOT NULL,
			`del_by` int(10) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;
			";

$s[] = "
				ALTER TABLE `patient_messages_attachment`
				ADD PRIMARY KEY (`id`),
				ADD KEY `patient_messages_id` (`patient_messages_id`),
				ADD KEY `patient_id` (`patient_id`);
				";

$s[] = "
				ALTER TABLE `patient_messages_attachment`
				MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
				";

foreach($s as $k=>$sql){
	$result = imw_query($sql) or $msg_info[] = imw_error();
}

?>
<!DOCTYPE HTML>
<html>
<head>
<title>Update 2</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style>
	label{display:inline-block; width:100px; border:0px solid red;}
</style>
</head>
<body>
<br><br>
<font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
    <?php echo(@implode("<br>",$msg_info));?>
</font>

</body>
</html>
