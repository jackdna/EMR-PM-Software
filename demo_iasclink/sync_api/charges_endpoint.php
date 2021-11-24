<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
$output = print_r($dataArrNew, true);
file_put_contents($logFolderPath.'/charges_test.txt', $output);
$dataObj 	= $dataArrNew;
$error_result 	= $success_result = array();
$pdfMsg = $dataVal = "";
if($dataObj->ExternalId) {
	$charges_id_primary 		= $dataObj->charges_id_primary;
	$ExternalId 				= $dataObj->ExternalId;
	$DOS 						= $dataObj->DOS;
	$charges_confirmation_id	= $dataObj->charges_confirmation_id;
	$Comments 					= $dataObj->Comments;
	$iasclink_appt_id 			= $dataObj->iasclink_appt_id;
	$CptCodesArr 				= $dataObj->Charges->CptCodes;
	$chargesShowArr = array();
	foreach($CptCodesArr as $CptCodes) {
		$CptGroup = $CptCodes->CptGroup;
		$CptCode = $CptCodes->CptCode;
		$Units = $CptCodes->Units;
		$modifiersArr = $CptCodes->modifiers;
		$DiagnosticCodesArr = $CptCodes->DiagnosticCodes;
		$chargesShowArr[] = "CptGroup='".$CptGroup."', CptCode='".$CptCode."', Units='".$Units."'";
		foreach($modifiersArr as $modifiers) {
			$modifier = $modifiers->modifier;
			$description = $modifiers->description;
			$chargesShowArr[] = "modifier='".$modifier."', description='".$description."'";
		}
		foreach($DiagnosticCodesArr as $DiagnosticCodes) {
			$DiagnosticType = $DiagnosticCodes->DiagnosticType;
			$DiagnosticCode = $DiagnosticCodes->DiagnosticCode;
			$DiagnosticDescription = $DiagnosticCodes->DiagnosticDescription;
			$chargesShowArr[] = "DiagnosticType='".$DiagnosticType."', DiagnosticCode='".$DiagnosticCode."', DiagnosticDescription='".$DiagnosticDescription."'";
		}
	}
	$chargesShowImpl 		= implode(",",$chargesShowArr);
	$chargesShowImpl		= ($chargesShowImpl) ? ",".$chargesShowImpl : "";
	$dataVal .= "\n charges_id_primary='".$charges_id_primary."', ExternalId='".$ExternalId."', DOS='".$DOS."', charges_confirmation_id='".$charges_confirmation_id."', Comments='".$Comments."', iasclink_appt_id='".$iasclink_appt_id."'".$chargesShowImpl;
	
	file_put_contents($logFolderPath.'/charges_receive_'.$charges_confirmation_id.'.txt', $dataVal);
	$pdfMsg = "\n CPT Charges Success";
}else {
	$pdfMsg = "\n No CPT Charges found in ASCEMR ";
}

echo $pdfMsg;
?>    
