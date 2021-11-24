<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");
include_once("../../common/conDb.php");
include_once("../../admin/classObjectFunction.php");
$objManageData = new manageData;

$qry = "SELECT pc.patientConfirmationId, pw.patient_in_waiting_id FROM patient_in_waiting_tbl pw 
		INNER JOIN stub_tbl st on (st.iolink_patient_in_waiting_id = pw.patient_in_waiting_id)
		INNER JOIN patientconfirmation pc on pc.patientConfirmationId = st.patient_confirmation_id 
		WHERE pw.patient_in_waiting_id !='0'
		";
$res = imw_query($qry) or die(imw_error());	
if(imw_num_rows($res)>0) {
	while($row = imw_fetch_assoc($res)) {
		$patient_in_waiting_id = $row["patient_in_waiting_id"];	
		$stubTblConfirmationId = $row["patientConfirmationId"];	

		//START INSERTING QUESTIONS OF ADMIN FOR HEALTHQUESTIONNAIRE CHARTNOTE
		$iolinkHealthquestionAdminDetails = $objManageData->getArrayRecords('iolink_healthquestionadmin', 'patient_in_waiting_id', $patient_in_waiting_id);
		unset($iolinkAdminQuestionArr);
		if(count($iolinkHealthquestionAdminDetails)>0){
			foreach($iolinkHealthquestionAdminDetails as $iolinkHealthquestionAdmin){
				$iolinkAdminQuestion 		= $iolinkHealthquestionAdmin->adminQuestion;
				$iolinkAdminQuestionStatus 	= $iolinkHealthquestionAdmin->adminQuestionStatus;
				$iolinkAdminQuestionDesc 	= $iolinkHealthquestionAdmin->adminQuestionDesc;
					
				$iolinkAdminQuestionArr['adminQuestion'] 		= addslashes($iolinkAdminQuestion);
				$iolinkAdminQuestionArr['adminQuestionStatus'] 	= addslashes($iolinkAdminQuestionStatus);
				$iolinkAdminQuestionArr['adminQuestionDesc'] 	= addslashes($iolinkAdminQuestionDesc);
				$iolinkAdminQuestionArr['confirmation_id'] 		= $stubTblConfirmationId;
				$iolinkAdminQuestionArr['patient_id'] 			= $iolinkHealthquestionAdmin->patient_id;
				
				$iolinkAdminQuestionConditionArr['adminQuestion'] 	= addslashes($iolinkAdminQuestion);
				$iolinkAdminQuestionConditionArr['confirmation_id'] = $stubTblConfirmationId;
				$iolinkAdminQuestionConditionArr['patient_id'] 		= $iolinkHealthquestionAdmin->patient_id;
				
				$iolinkAdminQuestionExist = $objManageData->getMultiChkArrayRecords('healthquestionadmin', $iolinkAdminQuestionConditionArr);
				if($iolinkAdminQuestionExist) {
					foreach($iolinkAdminQuestionExist as $iolinkAdminQuestionDetail){
						$iolinkAdminQuestionId 	= $iolinkAdminQuestionDetail->id;
						//if(!$chkHealthQuestCompleteRecordExist) {//UPDATE ONLY IF FLAG IS NOT GREEN IN SURGERYCENTER
							$objManageData->updateRecords($iolinkAdminQuestionArr, 'healthquestionadmin', 'id', $iolinkAdminQuestionId);
						//}
					}
				}else {
					$objManageData->addRecords($iolinkAdminQuestionArr, 'healthquestionadmin');
				}
			}
		}	
		//END INSERTING QUESTIONS OF ADMIN FOR HEALTHQUESTIONNAIRE CHARTNOTE

	}
}

?>