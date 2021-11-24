<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
$ignoreAuth = true;
include_once("../../../../config/globals.php");
$msg_info=array();

//START CODE TO ADD SCHEDULE STATUS 'Aborted Surgery' 
$qry = "SELECT id, alias, col_type FROM schedule_status WHERE status_name = 'Aborted Surgery' ORDER BY id DESC LIMIT 0,1"; 
$res = imw_query($qry)or die($qry.imw_error());
if(imw_num_rows($res)>0) {
	$row 		= imw_fetch_assoc($res);
	$id 		= $row["id"];
	$alias 		= trim($row["alias"]);
	$col_type 	= $row["col_type"];
	$field_arr 	= array();
	$field_arr[]= " modify_by = '0' ";
	$field_arr[]= " modify_datetime = '".date('Y-m-d H:i:s')."' ";
	if(!$alias || $col_type !='0') {
		if(!$alias) {
			$field_arr[] = " alias = '".imw_real_escape_string('A/Sx')."' ";
		}
		if($col_type !='0') {
			$field_arr[] = " col_type = '0' ";
		}
		$qry_fields = implode(",",$field_arr);
		$qry_save = "UPDATE schedule_status SET   ".$qry_fields." WHERE id = '".$id."' ";
		$res_save = imw_query($qry_save) or die($qry_save.imw_error());
	}
}else {
	$qry_save = "INSERT INTO schedule_status (status_name,alias,status,col_type,added_datetime) 
				VALUES ('Aborted Surgery','".imw_real_escape_string('A/Sx')."','1','0','".date('Y-m-d H:i:s')."') ";
	$res_save = imw_query($qry_save) or die($qry_save.imw_error());
}
//END CODE TO ADD SCHEDULE STATUS 'Aborted Surgery'


if(imw_error() || count($msg_info)>0)
{
	$message = "<br><br><b>Update 1  run FAILED!</b><br>".implode("<br>",$msg_info)."<br>";
	$color = "red";
}
else
{	
	$message = "<br><br><b>Update 1  run successfully!</b><br>";
	$color = "green";			
}
?>
<html>
<head>
<title>Update 1</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
    <br><br>
    <?php 
    if($message!=""){?>
        <font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo $message;?></font>
    <?php
    }
    ?> 
</body>
</html>