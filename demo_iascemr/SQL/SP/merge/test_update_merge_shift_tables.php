<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
set_time_limit(0);
include_once("../../../common/conDb.php");  //MYSQL CONNECTION
$sv_file_name = "merge_plan.csv";
include_once("test_update_merge_db_detail.php");  //DB Details
$fl_name= "csv/".$sv_file_name;

$ignoreTables = array('users','consent_category','consent_forms_template','patient_data_tbl');

if(file_exists($fl_name) && $masterDB && $childDB){
	$fileContents = fopen($fl_name,"r");
	$row=0;
	while(($data=fgetcsv($fileContents,10000,',')) !== FALSE){	
		if($row >0){
			$tableName	=	trim($data[0]);
			if($tableName && !in_array($tableName,$ignoreTables) ) {
				$updateQry = "Insert Into ".$masterDB.".".$tableName." (Select * From ".$childDB.".".$tableName.") ";
				imw_query($updateQry) or $msg_info[] = 'Query = '.$updateQry.imw_error();
			}
		}
		$row++;
	}
	
	$updateQry = "Insert Into ".$masterDB.".patient_in_waiting_tbl (Select * From ".$childDB.".patient_in_waiting_tbl) ";
	imw_query($updateQry) or $msg_info[] = 'Query = '.$updateQry.imw_error();
	
	
}

$msg_info[] = "<br><br><b> Shifting Completed</b>";
?>
<html>
<head>
<title>Shift Tables </title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
<?php if($msg_info!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2"><?php echo(implode("<br>",$msg_info));?></font>
<?php
}
@imw_close();
?> 

</body>
</html>