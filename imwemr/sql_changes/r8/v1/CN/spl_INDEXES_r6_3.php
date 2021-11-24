<?php
$ignoreAuth = true;
include("../../../../config/globals.php");
$msg_info=array();

function sqlQuery2 ($statement){
  $query = imw_query($statement);
  /*
  $query = mysql_query($statement, $GLOBALS['dbh']) or 
  HelpfulDie("query failed: $statement (" . mysql_error() . ")");
  */
  $rez = imw_fetch_array($query);
  if ($rez == FALSE)
  return FALSE;
  return $rez;
}

$sql = "SHOW INDEXES FROM pt_docs_patient_templates WHERE Column_name = 'patient_id' ";
$row=sqlQuery2($sql);
if($row==false){
	$sql=" ALTER TABLE  `pt_docs_patient_templates` ADD INDEX (  `patient_id` ) ; ";
	$row=sqlQuery2($sql);
	echo "<br/>".$sql;
}


$sql = "SHOW INDEXES FROM insurance_data WHERE Column_name = 'expiration_date' ";
$row=sqlQuery2($sql);
if($row==false){
	$sql=" ALTER TABLE  `insurance_data` ADD INDEX (  `expiration_date` ) ; ";
	$row=sqlQuery2($sql);
	echo "<br/>".$sql;
}

$sql = "SHOW INDEXES FROM contactlens_evaluations WHERE Column_name = 'clws_id' ";
$row=sqlQuery2($sql);
if($row==false){
	$sql=" ALTER TABLE  `contactlens_evaluations` ADD INDEX (  `clws_id` ) ; ";
	$row=sqlQuery2($sql);
	echo "<br/>".$sql;
}


$sql = "SHOW INDEXES FROM patient_data WHERE Column_name = 'External_MRN_2' ";
$row=sqlQuery2($sql);
if($row==false){
	$sql=" ALTER TABLE  `patient_data` ADD INDEX (  `External_MRN_2` ) ; ";
	$row=sqlQuery2($sql);
	echo "<br/>".$sql;
}


$sql = "SHOW INDEXES FROM superbill WHERE Column_name = 'patientId' ";
$row=sqlQuery2($sql);
if($row==false){
	$sql=" ALTER TABLE  `superbill` ADD INDEX (  `patientId` ) ; ";
	$row=sqlQuery2($sql);
	echo "<br/>".$sql;
}


$sql = "SHOW INDEXES FROM check_in_out_payment_details WHERE Column_name = 'payment_id' ";
$row=sqlQuery2($sql);
if($row==false){
	$sql=" ALTER TABLE  `check_in_out_payment_details` ADD INDEX (  `payment_id` ) ; ";
	$row=sqlQuery2($sql);
	echo "<br/>".$sql;
}


$sql = "SHOW INDEXES FROM check_in_out_payment WHERE Column_name = 'patient_id' ";
$row=sqlQuery2($sql);
if($row==false){
	$sql=" ALTER TABLE  `check_in_out_payment` ADD INDEX (  `patient_id` ) ; ";
	$row=sqlQuery2($sql);
	echo "<br/>".$sql;
}


$sql = "SHOW INDEXES FROM check_in_out_payment WHERE Column_name = 'sch_id' ";
$row=sqlQuery2($sql);
if($row==false){
	$sql=" ALTER TABLE  `check_in_out_payment` ADD INDEX (  `sch_id` ) ; ";
	$row=sqlQuery2($sql);
	echo "<br/>".$sql;
}


$sql = "SHOW INDEXES FROM patient_data WHERE Column_name = 'providerID' ";
$row=sqlQuery2($sql);
if($row==false){
	$sql=" ALTER TABLE  `patient_data` ADD INDEX (  `providerID` ) ; ";
	$row=sqlQuery2($sql);
	echo "<br/>".$sql;
}

$sql = "SHOW INDEXES FROM superbill WHERE Column_name = 'formId' ";
$row=sqlQuery2($sql);
if($row==false){
	$sql=" ALTER TABLE  `superbill` ADD INDEX (  `formId` ) ; ";
	$row=sqlQuery2($sql);
	echo "<br/>".$sql;
}


$sql = "SHOW INDEXES FROM contactlensmaster WHERE Column_name = 'patient_id' ";
$row=sqlQuery2($sql);
if($row==false){
	$sql=" ALTER TABLE  `contactlensmaster` ADD INDEX (  `patient_id` ) ; ";
	$row=sqlQuery2($sql);
	echo "<br/>".$sql;
}

$sql = "SHOW INDEXES FROM pt_problem_list_log WHERE Column_name = 'problem_id' ";
$row=sqlQuery2($sql);
if($row==false){
	$sql=" ALTER TABLE  `pt_problem_list_log` ADD INDEX (  `problem_id` ) ";
	$row=sqlQuery2($sql);
	echo "<br/>".$sql;
}


$arr=array("oct_rnfl", "surgical_tbl", "iol_master_tbl", "test_bscan", "test_cellcnt", "test_gdx", "test_labs", "test_other", "disc", "disc_external", 
			"nfa", "icg", "ivfa", "oct", "pachy", "topography", "vf_gl");
foreach($arr as $key => $tbl){

	$pphy="phyName";
	if($tbl == "icg" && $tbl == "ivfa"){ $pphy="phy"; }
	
	//
	$sql = "SHOW INDEXES FROM ".$tbl." WHERE Column_name = '".$pphy."' ";
	$row=sqlQuery2($sql);
	if($row==false){
		$sql=" ALTER TABLE  `".$tbl."` ADD INDEX (  `".$pphy."` ) ; ";
		$row=sqlQuery2($sql);
		echo "<br/>".$sql;
	}

	//
	$sql = "SHOW INDEXES FROM ".$tbl." WHERE Column_name = 'ordrby' ";
	$row=sqlQuery2($sql);
	if($row==false){
		$sql=" ALTER TABLE  `".$tbl."` ADD INDEX (  `ordrby` ) ; ";
		$row=sqlQuery2($sql);
		echo "<br/>".$sql;
	}
}


if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Release 6.3: Update R6.3 indexes run FAILED!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Release 6.3: Update R6.3 indexes run successfully </b>";
	$color = "green";
}

?>

<!DOCTYPE HTML>
<html>
<head>
<title>Update R6 indexes</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
    <?php echo(@implode("<br>",$msg_info));?>
</font>
<font face="Arial, Helvetica, sans-serif" color="<?php echo $color_sts;?>" size="2">
    <?php echo(@implode("<br>",$msg_info_sts));?>
</font>
</body>
</html>