<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
$q = array();
$q[] = 'ALTER TABLE  `chart_sps_lens` ADD  `prov_id` INT( 11 ) NOT NULL AFTER  `id` ';
$q[] = 'ALTER TABLE  `chart_sps_ast_plan_tpa` ADD  `prov_id` INT( 11 ) NOT NULL';
$q[] = 'ALTER TABLE  `chart_sps_ast_plan_tpa` ADD  `lens_type` INT( 11 ) NOT NULL';

  
foreach($q as $qry)
    imw_query($qry) or $msg_info[] = imw_error();

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 21 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 21 completed successfully. </b>";
	$color = "green";
}
?>
<html>
<head>
<title>Update 21</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>