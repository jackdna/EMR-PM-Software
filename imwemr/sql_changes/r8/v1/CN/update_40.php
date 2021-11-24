<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");


//error_reporting(E_ALL & ~E_NOTICE);
//ini_set("display_errors",1);

$q = array();
$q[] = '
	CREATE TABLE `test_diagnosis` (
  `id` int(10) NOT NULL,
  `diag_nm` varchar(200) NOT NULL,
  `test_id` int(10) NOT NULL,
  `test_sub_type` varchar(100) NOT NULL,
  `del_by` int(2) NOT NULL
) ENGINE=MyISAM;
';

$q[] = '
	ALTER TABLE `test_diagnosis`
  ADD PRIMARY KEY (`id`);
';

$q[] = '
	ALTER TABLE `test_diagnosis`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
';

foreach($q as $qry)
    imw_query($qry) or $msg_info[] = imw_error();

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 40 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 40 completed successfully. </b>";
	$color = "green";
	
	
}
?>
<html>
<head>
<title>Update 40</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>