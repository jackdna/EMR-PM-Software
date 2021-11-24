<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(500);
include_once("../../common/conDb.php");
include_once("../../admin/classObjectFunction.php");
$objManageData = new manageData;

$consentnewQry = "SELECT consent_id, consent_data FROM consent_forms_template ORDER BY consent_id";
$consentnewRes = imw_query($consentnewQry) or $msg_info[] = imw_error(); 
if(imw_num_rows($consentnewRes)>0) {
	$cntr=0;
	while($consentnewRow		= imw_fetch_array($consentnewRes)) {
		$consent_id 			= $consentnewRow["consent_id"];	
		$consent_data 			= stripslashes($consentnewRow["consent_data"]);	
		unset($arrayRecord);
		$chkSignWitness1Var 	= stristr($consent_data,"{Witness's Signature}");
		$chkSignWitness1VarNew 	= stristr($consent_data,"{Witness's&nbsp;Signature}");
		if($chkSignWitness1Var || $chkSignWitness1VarNew) {
			$cntr++;
			$consent_data   	= str_ireplace("{Witness's Signature}","{Witness Signature}",$consent_data);
			$consent_data		= str_ireplace("{Witness's&nbsp;Signature}","{Witness Signature}",$consent_data);
			$arrayRecord['consent_data'] = addslashes($consent_data);
			$objManageData->updateRecords($arrayRecord, 'consent_forms_template', 'consent_id', $consent_id);
		}
	}
}


$instQry = "SELECT instruction_id, instruction_desc FROM instruction_template ORDER BY instruction_id";
$instRes = imw_query($instQry) or $msg_info[] = imw_error(); 
if(imw_num_rows($instRes)>0) {
	$cntrInst=0;
	while($instRow= imw_fetch_array($instRes)) {
		$instruction_id 			= $instRow["instruction_id"];	
		$instruction_desc 			= stripslashes($instRow["instruction_desc"]);	
		unset($arrayRecordInst);
		$chkSignWitness1InstVar 	= stristr($instruction_desc,"{Witness's Signature}");
		$chkSignWitness1InsVarNew 	= stristr($instruction_desc,"{Witness's&nbsp;Signature}");
		if($chkSignWitness1InstVar || $chkSignWitness1InsVarNew) {
			$cntrInst++;
			$instruction_desc   	= str_ireplace("{Witness's Signature}","{Witness Signature}",$instruction_desc);
			$instruction_desc		= str_ireplace("{Witness's&nbsp;Signature}","{Witness Signature}",$instruction_desc);
			$arrayRecordInst['instruction_desc'] = addslashes($instruction_desc);
			$objManageData->updateRecords($arrayRecordInst, 'instruction_template', 'instruction_id', $instruction_id);
		}
	}
}

$color = 'green';
if(count($msg_info)>0){
	$color = 'red';
}
$msg_info[] = "Total ".$cntr." Consent Forms Record(s) Updated For Witness Signature";
$msg_info[] = "Total ".$cntrInst." Instruction Sheet Record(s) Updated For Witness Signature";

?>

<html>
<head>
<title>Mysql Updates For Lens Brand in iOlink/EMR</title>
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







