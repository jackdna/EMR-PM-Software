<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");

//error_reporting(E_ALL & ~E_NOTICE);
//ini_set("display_errors",1);

$q = array();
$q[] = '
	ALTER TABLE `chart_vision` ADD `vis_dis_od_sel_4` VARCHAR(20) NOT NULL AFTER `mr_hash`, ADD `vis_dis_od_txt_4` VARCHAR(20) NOT NULL AFTER `vis_dis_od_sel_4`, ADD `vis_dis_os_txt_4` VARCHAR(20) NOT NULL AFTER `vis_dis_od_txt_4`, ADD `vis_dis_ou_txt_4` VARCHAR(20) NOT NULL AFTER `vis_dis_os_txt_4`, ADD `vis_dis_act_4` TINYTEXT NOT NULL AFTER `vis_dis_ou_txt_4`;
';



foreach($q as $qry)
    imw_query($qry) or $msg_info[] = imw_error();

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 42 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 42 completed successfully. </b>";
	$color = "green";
	
	
}
?>
<html>
<head>
<title>Update 42</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>