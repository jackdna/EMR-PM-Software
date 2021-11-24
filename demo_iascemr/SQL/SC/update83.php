<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");
include_once("../../../".$iolinkDirectoryName."/common/conDb.php");

$sql1="ALTER TABLE `patient_in_waiting_tbl`  ADD `iasc_facility_id` INT(11) NOT NULL COMMENT 'add value if iDOC and iASC is same'"; 
imw_query($sql1)or $msg_info[] = imw_error();

if(trim($constantImwFacilityId)) {
	$constantImwFacilityIdArr = explode(",",$constantImwFacilityId);
	$constantImwFacilityIdMain = $constantImwFacilityIdArr[0];
	
	imw_query("CREATE TABLE patient_in_waiting_tbl_bak AS (SELECT * FROM patient_in_waiting_tbl)")or $msg_info[] = imw_error();
	
	$sql1="UPDATE `patient_in_waiting_tbl` SET  iasc_facility_id = '".$constantImwFacilityIdMain."' WHERE iasc_facility_id = '0'"; 
	imw_query($sql1)or $msg_info[] = imw_error();
}


if(count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 83 Failed! </b>";
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 83 Success.</b>";
	$color = "green";			
}
?>

<html>
<head>
<title>Update 83</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<?php if($msg_info!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo(implode("<br>",$msg_info));?></font>
<?php
@imw_close();
}
?> 
</body>
</html>