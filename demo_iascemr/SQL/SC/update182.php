<?php
// Under MIT License.
?>
<?php
set_time_limit(0);
include("../../common/conDb.php");
$msg_info = array();
//START TAKE BACKUP OF TABLE BEFORE ANY ACTION
$tbl = "localanesthesiarecord";
$bkTbl = $tbl.'_'.date('Y_m_d');
$sql1="CREATE  TABLE ".$bkTbl." LIKE ".$tbl;
$res=imw_query($sql1) or $msg_info[] = imw_error();
if( $res ) {
	$sql1="INSERT INTO ".$bkTbl." (SELECT *  FROM ".$tbl.");";
	imw_query($sql1)or $msg_info[] = imw_error();
}
//END TAKE BACKUP OF TABLE BEFORE ANY ACTION

$sql = array();
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med8_label`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med9_label`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med10_label`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med8_1`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med8_2`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med8_3`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med8_4`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med8_5`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med8_6`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med8_7`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med8_8`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med8_9`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med8_10`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med8_11`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med8_12`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med8_13`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med8_14`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med8_15`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med8_16`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med8_17`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med8_18`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med8_19`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med8_20`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med9_1`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med9_2`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med9_3`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med9_4`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med9_5`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med9_6`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med9_7`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med9_8`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med9_9`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med9_10`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med9_11`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med9_12`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med9_13`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med9_14`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med9_15`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med9_16`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med9_17`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med9_18`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med9_19`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med9_20`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med10_1`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med10_2`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med10_3`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med10_4`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med10_5`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med10_6`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med10_7`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med10_8`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med10_9`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med10_10`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med10_11`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med10_12`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med10_13`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med10_14`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med10_15`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med10_16`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med10_17`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med10_18`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med10_19`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `med10_20`;";

$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `blank1`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `blank2`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `propofol`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `midazolam`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `diprivan`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `Fentanyl`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `ketamine`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `labetalol`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `spo2`;";
$sql[] = "ALTER TABLE `localanesthesiarecord`  DROP `o2lpm`;";

foreach($sql as $qry){
	imw_query($qry) or $msg_info[] = imw_error();
}

//START CODE TO DIVIDE COLOUMNS IN SEPARATE TABLE
$medArr = array("blank1_label","blank2_label","blank3_label","blank4_label","mgPropofol_label","mgMidazolam_label",
"blank1_1","blank1_2","blank1_3","blank1_4","blank1_5","blank1_6","blank1_7","blank1_8","blank1_9","blank1_10","blank1_11","blank1_12","blank1_13","blank1_14","blank1_15","blank1_16","blank1_17","blank1_18","blank1_19","blank1_20",
"blank2_1","blank2_2","blank2_3","blank2_4","blank2_5","blank2_6","blank2_7","blank2_8","blank2_9","blank2_10","blank2_11","blank2_12","blank2_13","blank2_14","blank2_15","blank2_16","blank2_17","blank2_18","blank2_19","blank2_20",
"blank3_1","blank3_2","blank3_3","blank3_4","blank3_5","blank3_6","blank3_7","blank3_8","blank3_9","blank3_10","blank3_11","blank3_12","blank3_13","blank3_14","blank3_15","blank3_16","blank3_17","blank3_18","blank3_19","blank3_20",
"blank4_1","blank4_2","blank4_3","blank4_4","blank4_5","blank4_6","blank4_7","blank4_8","blank4_9","blank4_10","blank4_11","blank4_12","blank4_13","blank4_14","blank4_15","blank4_16","blank4_17","blank4_18","blank4_19","blank4_20",
"propofol_1","propofol_2","propofol_3","propofol_4","propofol_5","propofol_6","propofol_7","propofol_8","propofol_9","propofol_10","propofol_11","propofol_12","propofol_13","propofol_14","propofol_15","propofol_16","propofol_17","propofol_18","propofol_19","propofol_20",
"midazolam_1","midazolam_2","midazolam_3","midazolam_4","midazolam_5","midazolam_6","midazolam_7","midazolam_8","midazolam_9","midazolam_10","midazolam_11","midazolam_12","midazolam_13","midazolam_14","midazolam_15","midazolam_16","midazolam_17","midazolam_18","midazolam_19","midazolam_20");

$med2Arr = array("mgKetamine_label","mgLabetalol_label","mcgFentanyl_label",
"Fentanyl_1","Fentanyl_2","Fentanyl_3","Fentanyl_4","Fentanyl_5","Fentanyl_6","Fentanyl_7","Fentanyl_8","Fentanyl_9","Fentanyl_10","Fentanyl_11","Fentanyl_12","Fentanyl_13","Fentanyl_14","Fentanyl_15","Fentanyl_16","Fentanyl_17","Fentanyl_18","Fentanyl_19","Fentanyl_20",
"ketamine_1","ketamine_2","ketamine_3","ketamine_4","ketamine_5","ketamine_6","ketamine_7","ketamine_8","ketamine_9","ketamine_10","ketamine_11","ketamine_12","ketamine_13","ketamine_14","ketamine_15","ketamine_16","ketamine_17","ketamine_18","ketamine_19","ketamine_20",
"labetalol_1","labetalol_2","labetalol_3","labetalol_4","labetalol_5","labetalol_6","labetalol_7","labetalol_8","labetalol_9","labetalol_10","labetalol_11","labetalol_12","labetalol_13","labetalol_14","labetalol_15","labetalol_16","labetalol_17","labetalol_18","labetalol_19","labetalol_20",
"spo2_1","spo2_2","spo2_3","spo2_4","spo2_5","spo2_6","spo2_7","spo2_8","spo2_9","spo2_10","spo2_11","spo2_12","spo2_13","spo2_14","spo2_15","spo2_16","spo2_17","spo2_18","spo2_19","spo2_20",
"o2lpm_1","o2lpm_2","o2lpm_3","o2lpm_4","o2lpm_5","o2lpm_6","o2lpm_7","o2lpm_8","o2lpm_9","o2lpm_10","o2lpm_11","o2lpm_12","o2lpm_13","o2lpm_14","o2lpm_15","o2lpm_16","o2lpm_17","o2lpm_18","o2lpm_19","o2lpm_20");


$medColomns = implode(",",$medArr);
$med2Colomns = implode(",",$med2Arr);
$colmnQry = "";
$colmn2Qry = "";
foreach($medArr as $medField) {
	$colmnQry .= $medField." varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '', ";		
}
foreach($med2Arr as $med2Field) {
	$colmn2Qry .= $med2Field." varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '', ";		
}

//START CREATE FIRST TABLE TO DIVIDE COLUMNS
$sqlQry = "CREATE TABLE `localanesthesiarecordmedgrid` (
		`med_grid_id` int(11) NOT NULL AUTO_INCREMENT,
		`confirmation_id` int(11) NOT NULL DEFAULT '0',
		".$colmnQry."
		PRIMARY KEY (`med_grid_id`),
		KEY `confirmation_id` (`confirmation_id`)
		)ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1;";

$res = imw_query($sqlQry) or $msg_info[] = imw_error();
if($res) {
	//INSERT VALUES IN COLUMNS FROM ORIGINAL TABLE
	$sql1="INSERT INTO localanesthesiarecordmedgrid (confirmation_id,".$medColomns.") (SELECT confirmation_id, ".$medColomns."  FROM localanesthesiarecord);";
	$res1 = imw_query($sql1)or $msg_info[] = imw_error();
	if($res1) {
		foreach($medArr as $dropField) {
			//DROP INSERTED COLUMNS FROM ORGINAL TABLE
			$sql2 = "ALTER TABLE `localanesthesiarecord`  DROP ".$dropField;
			imw_query($sql2)or $msg_info[] = imw_error();
		}
	}
}
//END CREATE FIRST TABLE TO DIVIDE COLUMNS

//START CREATE SECOND TABLE TO DIVIDE COLUMNS
$sql2Qry = "CREATE TABLE `localanesthesiarecordmedgridsec` (
		`med_grid_sec_id` int(11) NOT NULL AUTO_INCREMENT,
		`confirmation_id` int(11) NOT NULL DEFAULT '0',
		".$colmn2Qry."
		PRIMARY KEY (`med_grid_sec_id`),
		KEY `confirmation_id` (`confirmation_id`)
		)ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1;";

$res2 = imw_query($sql2Qry) or $msg_info[] = imw_error();
if($res2) {
	//INSERT VALUES IN COLUMNS FROM ORIGINAL TABLE
	$sql1="INSERT INTO localanesthesiarecordmedgridsec (confirmation_id,".$med2Colomns.") (SELECT confirmation_id, ".$med2Colomns."  FROM localanesthesiarecord);";
	$res1 = imw_query($sql1)or $msg_info[] = imw_error();
	if($res1) {
		foreach($med2Arr as $dropField) {
			//DROP INSERTED COLUMNS FROM ORGINAL TABLE
			$sql2 = "ALTER TABLE `localanesthesiarecord`  DROP ".$dropField;
			imw_query($sql2)or $msg_info[] = imw_error();
		}
	}
}
//END CREATE SECOND TABLE TO DIVIDE COLUMNS

$message = '';
if(count($msg_info)>0)
{
	$message = "<br><br><b>Update 182 Failed!</b><br>".implode("<br>",$msg_info)."<br>";
	$color = "red";	
}
else
{	
	$message = "<br><br><b>Update 182 Success.</b><br>";
	$color = "green";			
}

?>
<html>
<head>
<title>Update 182</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<?php if($message!=""){?>
	<font face="Arial "; Helvetica "; sans-serif" size="2" color="<?php print $color; ?>"><?php echo($message);?></font>
<?php
imw_close();
}
?> 
</body>
</html>