<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>

<?php
include_once("../../common/conDb.php");

$curDate=date("d_m_Y");
$icd10_data="icd10_data_".$curDate;

//create a copy of a table
imw_query("CREATE TABLE ".$icd10_data." AS (SELECT * FROM icd10_data)")or $msg_info[] = imw_error();
$chkQry = " SELECT id FROM icd10_data WHERE ";

$chkWhrQry1 = " icd10 = 'E10.319' AND icd9 = '' ";
$chkWhrQry2 = " icd10 = 'E10.319' AND icd9 = '' ";
$qry1 = "UPDATE icd10_data SET icd10 = 'E10.319',laterality = '',staging='',severity='', icd10_desc ='TYPE 1 DIABETES MELLITUS WITH UNSPECIFIED DIABETIC RETINOPATHY WITHOUT MACULAR EDEMA' WHERE ".$chkWhrQry1;
$res = imw_query($qry1) or $msg_info[] = $qry1.imw_error();
if(imw_num_rows(imw_query($chkQry.$chkWhrQry2))=='0') {
	$insQry1 = "INSERT INTO icd10_data SET cat_id = '4', icd10 = 'E10.319',laterality = '',staging='',severity='',icd9 = '',icd10_desc ='TYPE 1 DIABETES MELLITUS WITH UNSPECIFIED DIABETIC RETINOPATHY WITHOUT MACULAR EDEMA' ";
	$insRes1 = imw_query($insQry1) or $msg_info[] = $insQry1.imw_error();
}

$chkWhrQry1 = " icd10 = 'E10.329' AND icd9 = '362.07' ";
$chkWhrQry2 = " icd10 = 'E10.329-' AND icd9 = '362.07' ";
$qry1 = "UPDATE icd10_data SET icd10 = 'E10.329-',laterality = '1',staging='',severity='' WHERE ".$chkWhrQry1;
$res = imw_query($qry1) or $msg_info[] = $qry1.imw_error();
if(imw_num_rows(imw_query($chkQry.$chkWhrQry2))=='0') {
	$insQry1 = "INSERT INTO icd10_data SET cat_id = '4', icd10 = 'E10.329-',icd9 = '362.07',laterality = '1',staging='',severity='',icd10_desc ='TYPE 1 DIABETES MELLITUS WITH MILD NONPROLIFERATIVE DR WITHOUT MACULAR EDEMA' ";
	$insRes1 = imw_query($insQry1) or $msg_info[] = $insQry1.imw_error();
}

$chkWhrQry1 = " icd10 = 'E10.329' AND icd9 = '362.02' ";
$chkWhrQry2 = " icd10 = 'E10.329-' AND icd9 = '362.02' ";
$qry1 = "UPDATE icd10_data SET icd10 = 'E10.329-',laterality = '1',staging='',severity='' WHERE ".$chkWhrQry1;
$res = imw_query($qry1) or $msg_info[] = $qry1.imw_error();
if(imw_num_rows(imw_query($chkQry.$chkWhrQry2))=='0') {
	$insQry1 = "INSERT INTO icd10_data SET cat_id = '4', icd10 = 'E10.329-',laterality = '1',staging='',severity='',icd9 = '362.02',icd10_desc ='TYPE 1 DIABETES MELLITUS WITH MILD NPDR WITHOUT MACULAR EDEMA' ";
	$insRes1 = imw_query($insQry1) or $msg_info[] = $insQry1.imw_error();
}

$chkWhrQry1 = " icd10 = 'E10.339' AND icd9 = '362.07' ";
$chkWhrQry2 = " icd10 = 'E10.339-' AND icd9 = '362.07' ";
$qry1 = "UPDATE icd10_data SET icd10 = 'E10.339-',laterality = '1',staging='',severity='' WHERE ".$chkWhrQry1;
$res = imw_query($qry1) or $msg_info[] = $qry1.imw_error();
if(imw_num_rows(imw_query($chkQry.$chkWhrQry2))=='0') {
	$insQry1 = "INSERT INTO icd10_data SET cat_id = '4', icd10 = 'E10.339-',laterality = '1',staging='',severity='',icd9 = '362.07',icd10_desc ='TYPE 1 DIABETES MELLITUS WITH MODERATE NONPROLFERATIVE DR WITHOUT MACULAR EDEMA' ";
	$insRes1 = imw_query($insQry1) or $msg_info[] = $insQry1.imw_error();
}

$chkWhrQry1 = " icd10 = 'E10.349' AND icd9 = '362.07' ";
$chkWhrQry2 = " icd10 = 'E10.349-' AND icd9 = '362.07' ";
$qry1 = "UPDATE icd10_data SET icd10 = 'E10.349-',laterality = '1',staging='',severity='' WHERE ".$chkWhrQry1;
$res = imw_query($qry1) or $msg_info[] = $qry1.imw_error();
if(imw_num_rows(imw_query($chkQry.$chkWhrQry2))=='0') {
	$insQry1 = "INSERT INTO icd10_data SET cat_id = '4', icd10 = 'E10.349-',laterality = '1',staging='',severity='',icd9 = '362.07',icd10_desc ='TYPE 1 DIABETES MELLITUS WITH SEVERE NONPROLIFERATIVE DR WITHOUT MACULAR EDEMA' ";
	$insRes1 = imw_query($insQry1) or $msg_info[] = $insQry1.imw_error();
}

$chkWhrQry1 = " icd10 = 'E10.351' AND icd9 = '362.02' ";
$chkWhrQry2 = " icd10 = 'E10.35--' AND icd9 = '362.02' ";
$qry1 = "UPDATE icd10_data SET icd10 = 'E10.35--',laterality = '1',staging='',severity='3' WHERE ".$chkWhrQry1;
$res = imw_query($qry1) or $msg_info[] = $qry1.imw_error();
if(imw_num_rows(imw_query($chkQry.$chkWhrQry2))=='0') {
	$insQry1 = "INSERT INTO icd10_data SET cat_id = '4', icd10 = 'E10.35--',laterality = '1',staging='',severity='3',icd9 = '362.02',icd10_desc ='TYPE 1 DIABETES MELLITUS WITH PROLIFERATIVE DR WITH MACULAR EDEMA' ";
	$insRes1 = imw_query($insQry1) or $msg_info[] = $insQry1.imw_error();
}

$chkWhrQry1 = " icd10 = 'E11.319' AND icd9 = '362.01' ";
$chkWhrQry2 = " icd10 = 'E11.319' AND icd9 = '362.01' ";
$qry1 = "UPDATE icd10_data SET icd10 = 'E11.319',laterality = '',staging='',severity='', icd10_desc ='TYPE 2 DIABETES MELLITUS WITH UNSPECIFIED DIABETIC RETINOPATHY WITHOUT MACULAR EDEMA' WHERE ".$chkWhrQry1;
$res = imw_query($qry1) or $msg_info[] = $qry1.imw_error();
if(imw_num_rows(imw_query($chkQry.$chkWhrQry2))=='0') {
	$insQry1 = "INSERT INTO icd10_data SET cat_id = '4', icd10 = 'E11.319',laterality = '',staging='',severity='',icd9 = '',icd10_desc ='TYPE 2 DIABETES MELLITUS WITH UNSPECIFIED DIABETIC RETINOPATHY WITHOUT MACULAR EDEMA' ";
	$insRes1 = imw_query($insQry1) or $msg_info[] = $insQry1.imw_error();
}

$chkWhrQry1 = " icd10 = 'E11.321' AND icd9 = '362.07' ";
$chkWhrQry2 = " icd10 = 'E11.321-' AND icd9 = '362.07' ";
$qry1 = "UPDATE icd10_data SET icd10 = 'E11.321-',laterality = '1',staging='',severity='' WHERE ".$chkWhrQry1;
$res = imw_query($qry1) or $msg_info[] = $qry1.imw_error();
if(imw_num_rows(imw_query($chkQry.$chkWhrQry2))=='0') {
	$insQry1 = "INSERT INTO icd10_data SET cat_id = '4', icd10 = 'E11.321-',laterality = '1',staging='',severity='',icd9 = '362.07',icd10_desc ='TYPE 2 DIABETES MELLITUS WITH MILD NONPROLIFERATIVE DIABETIC RETINOPATHY WITH MACULAR EDEMA' ";
	$insRes1 = imw_query($insQry1) or $msg_info[] = $insQry1.imw_error();
}

$chkWhrQry1 = " icd10 = 'E11.331' AND icd9 = '362.07' ";
$chkWhrQry2 = " icd10 = 'E11.331-' AND icd9 = '362.07' ";
$qry1 = "UPDATE icd10_data SET icd10 = 'E11.331-',laterality = '1',staging='',severity='' WHERE ".$chkWhrQry1;
$res = imw_query($qry1) or $msg_info[] = $qry1.imw_error();
if(imw_num_rows(imw_query($chkQry.$chkWhrQry2))=='0') {
	$insQry1 = "INSERT INTO icd10_data SET cat_id = '4', icd10 = 'E11.331-',laterality = '1',staging='',severity='',icd9 = '362.07',icd10_desc ='TYPE 2 DIABETES MELLITUS WITH MODERATE NONPROLIFERATIVE DIABETIC RETINOPATHY WITH MACULAR EDEMA' ";
	$insRes1 = imw_query($insQry1) or $msg_info[] = $insQry1.imw_error();
}

$chkWhrQry1 = " icd10 = 'E11.339' ";
$chkWhrQry2 = " icd10 = 'E11.339-' ";
$qry1 = "UPDATE icd10_data SET icd10 = 'E11.339-',laterality = '1',staging='',severity='' WHERE ".$chkWhrQry1;
$res = imw_query($qry1) or $msg_info[] = $qry1.imw_error();
if(imw_num_rows(imw_query($chkQry.$chkWhrQry2))=='0') {
	$insQry1 = "INSERT INTO icd10_data SET cat_id = '4', icd10 = 'E11.339-',laterality = '1',staging='',severity='',icd9 = '',icd10_desc ='TYPE 2 DIABETES MELLITUS WITH other opth complications' ";
	$insRes1 = imw_query($insQry1) or $msg_info[] = $insQry1.imw_error();
}

$chkWhrQry1 = " icd10 = 'E11.341' AND icd9 = '362.07' ";
$chkWhrQry2 = " icd10 = 'E11.341-' AND icd9 = '362.07' ";
$qry1 = "UPDATE icd10_data SET icd10 = 'E11.341-',laterality = '1',staging='',severity='' WHERE  ".$chkWhrQry1;
$res = imw_query($qry1) or $msg_info[] = $qry1.imw_error();
if(imw_num_rows(imw_query($chkQry.$chkWhrQry2))=='0') {
	$insQry1 = "INSERT INTO icd10_data SET cat_id = '4', icd10 = 'E11.341-',laterality = '1',staging='',severity='',icd9 = '362.07',icd10_desc ='TYPE 2 DIABETES MELLITUS WITH SEVERE NONPROLIFERATIVE DIABETIC RETINOPATHY WITH MACULAR EDEMA' ";
	$insRes1 = imw_query($insQry1) or $msg_info[] = $insQry1.imw_error();
}

$chkWhrQry1 = " icd10 = 'E11.351' AND icd9 = '362.02' ";
$chkWhrQry2 = " icd10 = 'E11.35--' AND icd9 = '362.02' ";
$qry1 = "UPDATE icd10_data SET icd10 = 'E11.35--',laterality = '1',staging='',severity='3' WHERE ".$chkWhrQry1;
$res = imw_query($qry1) or $msg_info[] = $qry1.imw_error();
if(imw_num_rows(imw_query($chkQry.$chkWhrQry2))=='0') {
	$insQry1 = "INSERT INTO icd10_data SET cat_id = '4', icd10 = 'E11.35--',laterality = '1',staging='',severity='3',icd9 = '362.02',icd10_desc ='TYPE 2 DIABETES MELLITUS WITH SEVERE PROLIFERATIVE DIABETIC RETINOPATHY WITH MACULAR EDEMA' ";
	$insRes1 = imw_query($insQry1) or $msg_info[] = $insQry1.imw_error();
}

$chkWhrQry1 = " icd10 = 'E11.351' AND icd9 = '362.07' ";
$chkWhrQry2 = " icd10 = 'E11.35--' AND icd9 = '362.07' ";
$qry1 = "UPDATE icd10_data SET icd10 = 'E11.35--',laterality = '1',staging='',severity='3' WHERE ".$chkWhrQry1;
$res = imw_query($qry1) or $msg_info[] = $qry1.imw_error();
if(imw_num_rows(imw_query($chkQry.$chkWhrQry2))=='0') {
	$insQry1 = "INSERT INTO icd10_data SET cat_id = '4', icd10 = 'E11.35--',laterality = '1',staging='',severity='3',icd9 = '362.07',icd10_desc ='TYPE 2 DIABETES MELLITUS WITH SEVERE NONPROLIFERATIVE DIABETIC RETINOPATHY WITH MACULAR EDEMA' ";
	$insRes1 = imw_query($insQry1) or $msg_info[] = $insQry1.imw_error();
}

$chkWhrQry1 = " icd10 = 'E11.359' AND icd9 = '362.02' ";
$chkWhrQry2 = " icd10 = 'E11.359-' AND icd9 = '362.02' ";
$qry1 = "UPDATE icd10_data SET icd10 = 'E11.359-',laterality = '1',staging='',severity='' WHERE ".$chkWhrQry1;
$res = imw_query($qry1) or $msg_info[] = $qry1.imw_error();
if(imw_num_rows(imw_query($chkQry.$chkWhrQry2))=='0') {
	$insQry1 = "INSERT INTO icd10_data SET cat_id = '4', icd10 = 'E11.359-',laterality = '1',staging='',severity='',icd9 = '362.02',icd10_desc ='TYPE 2 DIABETES MELLITUS WITH SEVERE PROLIFERATIVE DIABETIC RETINOPATHY WITHOUT MACULAR EDEMA' ";
	$insRes1 = imw_query($insQry1) or $msg_info[] = $insQry1.imw_error();
}

$chkWhrQry1 = " icd10 = 'E13.321' AND icd9 = '362.07' ";
$chkWhrQry2 = " icd10 = 'E13.321-' AND icd9 = '362.07' ";
$qry1 = "UPDATE icd10_data SET icd10 = 'E13.321-',laterality = '1',staging='',severity='' WHERE ".$chkWhrQry1;
$res = imw_query($qry1) or $msg_info[] = $qry1.imw_error();
if(imw_num_rows(imw_query($chkQry.$chkWhrQry2))=='0') {
	$insQry1 = "INSERT INTO icd10_data SET cat_id = '4', icd10 = 'E13.321-',laterality = '1',staging='',severity='',icd9 = '362.07',icd10_desc ='DIABETES WITH MILD NP DR WITH ME' ";
	$insRes1 = imw_query($insQry1) or $msg_info[] = $insQry1.imw_error();
}

$chkWhrQry1 = " icd10 = 'E13.331' AND icd9 = '362.07' ";
$chkWhrQry2 = " icd10 = 'E13.331-' AND icd9 = '362.07' ";
$qry1 = "UPDATE icd10_data SET icd10 = 'E13.331-',laterality = '1',staging='',severity='' WHERE ".$chkWhrQry1;
$res = imw_query($qry1) or $msg_info[] = $qry1.imw_error();
if(imw_num_rows(imw_query($chkQry.$chkWhrQry2))=='0') {
	$insQry1 = "INSERT INTO icd10_data SET cat_id = '4', icd10 = 'E13.331-',laterality = '1',staging='',severity='',icd9 = '362.07',icd10_desc ='DIABETES WITH MODERATE NPDR WITH ME' ";
	$insRes1 = imw_query($insQry1) or $msg_info[] = $insQry1.imw_error();
}

$chkWhrQry1 = " icd10 = 'E13.341' AND icd9 = '362.07' ";
$chkWhrQry2 = " icd10 = 'E13.341-' AND icd9 = '362.07' ";
$qry1 = "UPDATE icd10_data SET icd10 = 'E13.341-',laterality = '1',staging='',severity='' WHERE ".$chkWhrQry1;
$res = imw_query($qry1) or $msg_info[] = $qry1.imw_error();
if(imw_num_rows(imw_query($chkQry.$chkWhrQry2))=='0') {
	$insQry1 = "INSERT INTO icd10_data SET cat_id = '4', icd10 = 'E13.341-',laterality = '1',staging='',severity='',icd9 = '362.07',icd10_desc ='DIABETES WITH SEVERE NPDR WITH ME' ";
	$insRes1 = imw_query($insQry1) or $msg_info[] = $insQry1.imw_error();
}

$chkWhrQry1 = " icd10 = 'E13.351' AND icd9 = '362.02' ";
$chkWhrQry2 = " icd10 = 'E13.35--' AND icd9 = '362.02' ";
$qry1 = "UPDATE icd10_data SET icd10 = 'E13.35--',laterality = '1',staging='',severity='3' WHERE ".$chkWhrQry1;
$res = imw_query($qry1) or $msg_info[] = $qry1.imw_error();
if(imw_num_rows(imw_query($chkQry.$chkWhrQry2))=='0') {
	$insQry1 = "INSERT INTO icd10_data SET cat_id = '4', icd10 = 'E13.35--',laterality = '1',staging='',severity='3',icd9 = '362.02',icd10_desc ='DIABETES WITH PROLIFERATIVE NPDR WITH ME' ";
	$insRes1 = imw_query($insQry1) or $msg_info[] = $insQry1.imw_error();
}

$chkWhrQry1 = " icd10 = 'E13.351' AND icd9 = '362.07' ";
$chkWhrQry2 = " icd10 = 'E13.35--' AND icd9 = '362.07' ";
$qry1 = "UPDATE icd10_data SET icd10 = 'E13.35--',laterality = '1',staging='',severity='3' WHERE ".$chkWhrQry1;
$res = imw_query($qry1) or $msg_info[] = $qry1.imw_error();
if(imw_num_rows(imw_query($chkQry.$chkWhrQry2))=='0') {
	$insQry1 = "INSERT INTO icd10_data SET cat_id = '4', icd10 = 'E13.35--',laterality = '1',staging='',severity='3',icd9 = '362.07',icd10_desc ='DIABETES WITH PROLIFERATIVE NPDR WITH ME' ";
	$insRes1 = imw_query($insQry1) or $msg_info[] = $insQry1.imw_error();
}

$chkWhrQry1 = " icd10 = 'E13.359' AND icd9 = '362.02' ";
$chkWhrQry2 = " icd10 = 'E13.359-' AND icd9 = '362.02' ";
$qry1 = "UPDATE icd10_data SET icd10 = 'E13.359-',laterality = '1',staging='',severity='' WHERE ".$chkWhrQry1;
$res = imw_query($qry1) or $msg_info[] = $qry1.imw_error();
if(imw_num_rows(imw_query($chkQry.$chkWhrQry2))=='0') {
	$insQry1 = "INSERT INTO icd10_data SET cat_id = '4', icd10 = 'E13.359-',laterality = '1',staging='',severity='',icd9 = '362.02',icd10_desc ='DIABETES WITH PROLIFERATIVE NPDR WITHOUT ME' ";
	$insRes1 = imw_query($insQry1) or $msg_info[] = $insQry1.imw_error();
}

$chkWhrQry1 = " icd10 = 'H34.81-' AND icd9 = '362.35' ";
$chkWhrQry2 = " icd10 = 'H34.81--' AND icd9 = '362.35' ";
$qry1 = "UPDATE icd10_data SET icd10 = 'H34.81--',laterality = '1',staging='',severity='3' WHERE ".$chkWhrQry1;
$res = imw_query($qry1) or $msg_info[] = $qry1.imw_error();
if(imw_num_rows(imw_query($chkQry.$chkWhrQry2))=='0') {
	$insQry1 = "INSERT INTO icd10_data SET cat_id = '14', icd10 = 'H34.81--',laterality = '1',staging='',severity='3',icd9 = '362.35',icd10_desc ='CENTRAL RETINAL VEIN OCCLUSION' ";
	$insRes1 = imw_query($insQry1) or $msg_info[] = $insQry1.imw_error();
}

$chkWhrQry1 = " icd10 = 'H40.11X-' AND icd9 = '365.11' ";
$chkWhrQry2 = " icd10 = 'H40.11--' AND icd9 = '365.11' ";
$qry1 = "UPDATE icd10_data SET icd10 = 'H40.11--',laterality = '1',staging='',severity='3' WHERE ".$chkWhrQry1;
$res = imw_query($qry1) or $msg_info[] = $qry1.imw_error();
if(imw_num_rows(imw_query($chkQry.$chkWhrQry2))=='0') {
	$insQry1 = "INSERT INTO icd10_data SET cat_id = '14', icd10 = 'H40.11--',laterality = '1',staging='',severity='3',icd9 = '365.11',icd10_desc ='POAG' ";
	$insRes1 = imw_query($insQry1) or $msg_info[] = $insQry1.imw_error();
}

$delQry = "DELETE FROM icd10_data WHERE icd10 = 'E10.311' OR icd10 = 'E11.311' ";
$delRes = imw_query($delQry) or $msg_info[] = $delQry.imw_error();

//START ADD NEW CODES OF ICD10
$icd10_code_arr = array("H34.83--"=>"BRANCH RETINAL VEIN OCCLUSION","H35.31--"=>"ARMD, WET","H35.32--"=>"ARMD, DRY");
foreach($icd10_code_arr as $icd10_code => $icd10_code_desc) {
	$qry = "SELECT id FROM icd10_data WHERE icd10 = '".$icd10_code."' LIMIT 0,1";
	$res = imw_query($qry) or die($qry.imw_error());
	if(imw_num_rows($res)==0){
		$insQry = "INSERT INTO icd10_data SET cat_id ='4',icd10 = '".$icd10_code."',laterality = '1',staging='',severity='3',icd10_desc='".$icd10_code_desc."' ";	
		$insRes = imw_query($insQry) or $msg_info[] = $insQry.imw_error();
	}
}
//END ADD NEW CODES OF ICD10

//START CODE TO SET ALL DESCRIPTION OF ICD10 CODE IN UPPER CASE
$qryUpper = "UPDATE `icd10_data` SET `icd10_desc` = UPPER(`icd10_desc`) WHERE 1=1";
$resUpper = imw_query($qryUpper) or die($qryUpper.imw_error());
//END CODE TO SET ALL DESCRIPTION OF ICD10 CODE IN UPPER CASE

$msg_info[] = "<br><br><b>ICD10 Codes updated Successfully</b>";

?>
<html>
<head>
<title>Mysql Updates After Launch </title>
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
