<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(900);
include_once("../../common/conDb.php");
include_once("../../admin/classObjectFunction.php");
$objManageData = new manageData;

$sql1="ALTER TABLE  `dischargesummarysheet` ADD  `procedures_code_name` TEXT NOT NULL AFTER  `procedures_name`"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `dischargesummarysheet` ADD  `diag_names` TEXT NOT NULL AFTER  `diag_ids`"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE  `dischargesummarysheet` ADD  `icd10_name` TEXT NOT NULL AFTER  `icd10_code`"; 
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `surgerycenter` ADD `jump_merged_asc_id_status` INT( 11 ) NOT NULL "; //FOR MERGE PURPOSE
imw_query($sql1)or $msg_info[] = imw_error();

$sql1="ALTER TABLE `patientconfirmation` ADD `asc_id_before_merge` INT( 11 ) NOT NULL COMMENT 'store asc_id before merge'"; //FOR MERGE PURPOSE 
imw_query($sql1)or $msg_info[] = imw_error();

$proceduresTemp = $objManageData->getArrayRecords('procedures','1','1','procedureId','Asc',"And del_status <> 'yes' And code <> '' ");
$diagnosisTemp	= $objManageData->getArrayRecords('diagnosis_tbl','1','1','diag_id','Asc',"And del_status = '' And diag_code <> '' ");
$dxCodesTemp	= $objManageData->getArrayRecords('icd10_data','1','1','id','Asc',"And deleted = 0 ");
foreach($proceduresTemp as $_key => $val){
	$procedures[$val->procedureId] = $val;
}

foreach($diagnosisTemp as $_key => $val){
	$diagnosis[$val->diag_id] = $val;
}

foreach($dxCodesTemp as $_key => $val){
	$dxCodes[$val->id] = $val;
}


imw_query("CREATE TABLE dischargesummarysheet_bak_".date("d_m_Y")." AS (SELECT * FROM dischargesummarysheet)")or $msg_info[] = imw_error();	
$query="Select dischargeSummarySheetId, procedures_code, diag_ids, icd10_id From dischargesummarysheet Where procedures_code_name = '' And diag_names = '' And icd10_name = ''  Order By dischargeSummarySheetId Asc ";
$sql = imw_query($query) or $msg_info[] = imw_error();
$cnt = imw_num_rows($sql);
$counter = 0; 

while($row = imw_fetch_object($sql))
{
	$cptIds = $diagIds =  $dxIds = $cptIdArray = $diagIdArray = $dxIdArray = array();
	$cptIdImplode = $diagIdImplode =  $dxIdImplode = '';
	
	$cptIds	=	array_filter(explode(",",$row->procedures_code));
	$diagIds=	array_filter(explode(",",$row->diag_ids));
	$dxIds	=	array_filter(explode(",",$row->icd10_id));
	
	if(is_array($cptIds)  && count($cptIds) > 0 )
	{
		foreach($cptIds as $cptId)
		{
			$cptIdArray[]	=	$procedures[$cptId]->code;
		}
	}
	
	if(is_array($diagIds)  && count($diagIds) > 0 )
	{	
		foreach($diagIds as $diagId)
		{
			list($diag_code,$diag_desc)	=	explode(", ",$diagnosis[$diagId]->diag_code);
			$diagIdArray[]	=	trim(addslashes($diag_desc));
		}
	}
	
	if(is_array($dxIds)  && count($dxIds) > 0 )
	{	
		foreach($dxIds as $dxId)
		{
			$dxIdArray[]	=	addslashes($dxCodes[$dxId]->icd10_desc);
		}
	}
	$cptIdImplode	= implode('##',$cptIdArray);
	$diagIdImplode	= implode('@@',$diagIdArray);
	$dxIdImplode	= implode('@@',$dxIdArray);
		
	$updateQry = "Update dischargesummarysheet Set procedures_code_name = '".$cptIdImplode."', diag_names = '".$diagIdImplode."', icd10_name = '".$dxIdImplode."' Where dischargeSummarySheetId = ".$row->dischargeSummarySheetId." ";
	$updateSql = imw_query($updateQry) or $msg_info[] = imw_error();
	
	if($updateSql) $counter++; 
}

$message = $counter . ' Record(s) updated out of '.$cnt .' in Discharge Summary';

if(imw_error() || count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 99 Failed! </b><br>".$message."<br>".imw_error();
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 99 Success.</b><br>".$message;
	$color = "green";			
}

?>

<html>
<head>
<title>Update 99</title>
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