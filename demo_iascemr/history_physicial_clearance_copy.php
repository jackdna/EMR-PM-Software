<?php

$baseQry	=	"Select hp.form_status,pc.dos From history_physicial_clearance hp 
				 INNER JOIN patientconfirmation pc ON (pc.patientConfirmationId = hp.confirmation_id)
				 Where hp.confirmation_id = '".$_REQUEST['pConfId']."' ";
$baseSql	=	imw_query($baseQry) or die('Error Found at Line No.'.(__LINE__).':--- '.$baseQry.'---'.imw_error());
$baseCnt	=	imw_num_rows($baseSql);
if($baseCnt)
{ 
	// Nothing to Do
}
else
{
	$insertQry	=	"Insert Into history_physicial_clearance Set confirmation_id = '".$_REQUEST['pConfId']."', form_status = '' ";
	$insertSql	=	imw_query($insertQry) or die('Error Found at Line No.'.(__LINE__).':--- '.$insertQry.'---'.imw_error());
	
	$baseSql	=	imw_query($baseQry) or die('Error Found at Line No.'.(__LINE__).':--- '.$baseQry.'---'.imw_error());
	$baseCnt	=	imw_num_rows($baseSql);

}

$baseRow	=	imw_fetch_object($baseSql);
$baseFormStatus	=	$baseRow->form_status;
$baseFormDOS	=	$baseRow->dos;  

if($baseFormStatus <> 'completed' && $baseFormStatus <> 'not completed' )
{
	$chkPrevHPQry	=	"SELECT hp.*,pc.dos,pc.allergiesNKDA_status FROM history_physicial_clearance hp
									INNER JOIN patientconfirmation pc 
									ON (pc.patientConfirmationId = hp.confirmation_id 
									AND pc.patientId = '".$_REQUEST['patient_id']."' 
									AND pc.dos  < '".$baseFormDOS."')
									WHERE hp.confirmation_id !='0' 
									And hp.form_status != '' 
									ORDER BY pc.dos Desc, pc.patientConfirmationId Desc Limit 0,1";
									
	$chkPrevHPSql	=	imw_query($chkPrevHPQry) or die($chkPrevHPQry.'---'.imw_error());
	$chkPrevHPCnt	=	imw_num_rows($chkPrevHPSql);
	
	if($chkPrevHPCnt)
	{
		
		$chkPrevHPRow	=	imw_fetch_object($chkPrevHPSql);
		$chartValidDate	=	($chkPrevHPRow->chart_copied) ? $chkPrevHPRow->copied_dos : $chkPrevHPRow->dos ;
		$chartDos		=	$baseFormDOS;//$chkprocedureConfirmationDetails->dos;
		$chartValidFrom	=	$objManageData->getDateSubtract($chartDos,30);
		$isValidDoc		=	($chartValidFrom <= $chartValidDate )	?	true	:	false;
		
		if($_REQUEST["hpCopy"] == "yes") { //FROM history_physicial_clearance.php
			$isValidDoc	= true;	
		}
		$chkPrevHPRow->copied_dos = ($chkPrevHPRow->chart_copied) ? $chkPrevHPRow->copied_dos : $chkPrevHPRow->dos;
		$chkPrevHPRow->chart_copied =	1;
				
		if(($chkPrevHPRow->form_status == 'completed' || $chkPrevHPRow->form_status == 'not completed') && $isValidDoc )
		{ 
			$chkPrevHPRow->form_status = 'not completed'; // SET THIS TO "NOT COMPLETED" FORCEFULLY AS USERS SIGNATURE DO NOT CARRY FORWARD.
			// Copying H&P Fields from previous appointment
			/*
			$upFields	=	array('patient_id','form_status','date_of_h_p','cadMI','cadMIDesc','cvaTIA','cvaTIADesc','htnCP','htnCPDesc','anticoagulationTherapy','anticoagulationTherapyDesc','respiratoryAsthma','respiratoryAsthmaDesc','arthritis','arthritisDesc','diabetes','diabetesDesc','recreationalDrug','recreationalDrugDesc','giGerd','giGerdDesc','ocular','ocularDesc','kidneyDisease','kidneyDiseaseDesc','hivAutoimmune','hivAutoimmuneDesc','historyCancer','historyCancerDesc','organTransplant','organTransplantDesc','badReaction','badReactionDesc','otherHistoryPhysical','wearContactLenses','wearContactLensesDesc','smoking','smokingDesc','drinkAlcohal','drinkAlcohalDesc','haveAutomatic','haveAutomaticDesc','medicalHistoryObtained','medicalHistoryObtainedDesc','otherNotes' ,'signSurgeon1Id','signSurgeon1FirstName','signSurgeon1MiddleName','signSurgeon1LastName','signSurgeon1Status','signSurgeon1DateTime','signAnesthesia1Id','signAnesthesia1FirstName','signAnesthesia1MiddleName','signAnesthesia1LastName','signAnesthesia1Status','signAnesthesia1DateTime','signNurseId','signNurseFirstName','signNurseMiddleName','signNurseLastName','signNurseStatus','signNurseDateTime','resetDateTime','resetBy','save_date_time','save_operator_id','chart_copied','copied_dos');
*/			
			$upFields	=	array('patient_id','form_status','date_of_h_p','cadMI','cadMIDesc','cvaTIA','cvaTIADesc','htnCP','htnCPDesc','anticoagulationTherapy','anticoagulationTherapyDesc','respiratoryAsthma','respiratoryAsthmaDesc','arthritis','arthritisDesc','diabetes','diabetesDesc','recreationalDrug','recreationalDrugDesc','giGerd','giGerdDesc','ocular','ocularDesc','kidneyDisease','kidneyDiseaseDesc','hivAutoimmune','hivAutoimmuneDesc','historyCancer','historyCancerDesc','organTransplant','organTransplantDesc','badReaction','badReactionDesc','otherHistoryPhysical','wearContactLenses','wearContactLensesDesc','smoking','smokingDesc','drinkAlcohal','drinkAlcohalDesc','haveAutomatic','haveAutomaticDesc','medicalHistoryObtained','medicalHistoryObtainedDesc','otherNotes' ,'resetDateTime','resetBy','save_date_time','save_operator_id','chart_copied','copied_dos','heartExam','heartExamDesc','lungExam','lungExamDesc','discussedAdvancedDirective');
			
			$sql	=	'';	
			foreach($upFields as $fieldName)
			{
				$sql	.=	", ".$fieldName." = '".addslashes($chkPrevHPRow->$fieldName)."' ";
			}
			$sql	=	($sql)	?	substr($sql,1)	:	''	;
			// Adding Latest chart Version Num while copying
			$sql  = $sql. ", version_num = '3', version_date_time = '".date('Y-m-d H:i:s')."' ";
			$HPInsrtQry	=	"Update history_physicial_clearance Set ".$sql." Where confirmation_id = '".$_REQUEST['pConfId']."' ";
						//echo $HPInsrtQry.'<br><br>';
			imw_query($HPInsrtQry) or die($HPInsrtQry.'---'.imw_error());
			
			//SET NKDA STATUS FROM PREVIOUS ALLERGIES
			$nkdaStatusQry	=	"Update patientconfirmation Set allergiesNKDA_status = '".$chkPrevHPRow->allergiesNKDA_status."' Where patientConfirmationId = '".$_REQUEST['pConfId']."' ";
			$nkdaStatusRes = imw_query($nkdaStatusQry);
			
			//START COPY ALLERGIES FROM PREVIOUS H&P
			$allergyCopyGetQry	=	"Select * From patient_allergies_tbl Where patient_confirmation_id = '".$chkPrevHPRow->confirmation_id."' ";
			$allergyCopyGetRes	=	imw_query($allergyCopyGetQry) or die($allergyCopyGetQry.'---'.imw_error());
			$allergyCopyGetNumRow	=	imw_num_rows($allergyCopyGetRes);
			if($allergyCopyGetNumRow)
			{
				
				
				while($allergyCopyGetRow	=	imw_fetch_object($allergyCopyGetRes)) {
					unset($allergyCopyArr);
					$allergyCopyArr['allergy_name'] 			= addslashes($allergyCopyGetRow->allergy_name);
					$allergyCopyArr['reaction_name'] 			= addslashes($allergyCopyGetRow->reaction_name);
					$allergyCopyArr['patient_confirmation_id'] 	= $_REQUEST['pConfId'];
					$allergyCopyArr['patient_id'] 				= $_REQUEST['patient_id'];
					
					$chkAllergyCopyExist = $objManageData->getMultiChkArrayRecords('patient_allergies_tbl', $allergyCopyArr);
					if($chkAllergyCopyExist) {
						//DO NOT UPDATE/COPY
					}else {
						$allergyCopyArr['operator_name'] 		= $_SESSION['loginUserName'];
						$allergyCopyArr['operator_id'] 			= $_SESSION['loginUserId'];
						$objManageData->addRecords($allergyCopyArr, 'patient_allergies_tbl');
					}
				}
			}
			//END COPY MEDICATION FROM PREVIOUS H&P
			
			//START COPY MEDICATION FROM PREVIOUS H&P
			$medCopyGetQry	=	"Select * From patient_anesthesia_medication_tbl Where confirmation_id = '".$chkPrevHPRow->confirmation_id."' ";
			$medCopyGetRes	=	imw_query($medCopyGetQry) or die($medCopyGetQry.'---'.imw_error());
			$medCopyGetNumRow	=	imw_num_rows($medCopyGetRes);
			if($medCopyGetNumRow)
			{
				while($medCopyGetRow	=	imw_fetch_object($medCopyGetRes)) {

					unset($medCopyArr);
					$medCopyArr['prescription_medication_name'] = addslashes($medCopyGetRow->prescription_medication_name);
					$medCopyArr['prescription_medication_desc'] = addslashes($medCopyGetRow->prescription_medication_desc);
					$medCopyArr['prescription_medication_sig'] = addslashes($medCopyGetRow->prescription_medication_sig);
					$medCopyArr['patient_confirmation_id'] 	= $_REQUEST['pConfId'];
					$medCopyArr['patient_id'] 				= $_REQUEST['patient_id'];
					
					$chkMedCopyExist = $objManageData->getMultiChkArrayRecords('patient_anesthesia_medication_tbl', $medCopyArr);
					if($chkMedCopyExist) {
						//DO NOT UPDATE/COPY
					}else {
						$medCopyArr['operator_name'] 		= $_SESSION['loginUserName'];
						$medCopyArr['operator_id'] 			= $_SESSION['loginUserId'];
						$objManageData->addRecords($medCopyArr, 'patient_anesthesia_medication_tbl');
					}
				}
			}
			//END COPY MEDICATION FROM PREVIOUS H&P
			
			
			// Start Copying scan document for H&P Folder
			$HPScanDocQry	=	"Select * From scan_documents Where confirmation_id = '".$chkPrevHPRow->confirmation_id."' And document_name='H&P' ";
			$HPScanDocSql	=	imw_query($HPScanDocQry) or die($HPScanDocQry.'---'.imw_error());
			$HPScanDocCnt	=	imw_num_rows($HPScanDocSql);
			if($HPScanDocCnt)
			{
				$HPScanDocRow	=	imw_fetch_object($HPScanDocSql);
				
				$HPScanDocQry_chk =	"Select * From scan_documents Where confirmation_id = '".$_REQUEST['pConfId']."' And document_name='H&P' ";
				$HPScanDocSql_chk =	imw_query($HPScanDocQry_chk) or die($HPScanDocQry_chk.'---'.imw_error());
				$HPScanDocCnt_chk =	imw_num_rows($HPScanDocSql_chk);
				if(!$HPScanDocCnt_chk)
				{
					$HPDocInsertQry	="Insert Into scan_documents Set confirmation_id = '".$_REQUEST['pConfId']."', patient_id = '".$_REQUEST['patient_id']."', document_name = 'H&P', dosOfScan='".$chkPrevHPRow->dos."', stub_id='".$_REQUEST['stub_id']."' ";
					imw_query($HPDocInsertQry) or die($HPDocInsertQry.'---'.imw_error());
					$documentId	=	imw_insert_id();
				}
				else
				{
					$HPScanDocRow_chk = imw_fetch_object($HPScanDocSql_chk);
					$documentId	=	$HPScanDocRow_chk->document_id;
				}
				
				
				$HPScanUploadQry	=	"Select * From scan_upload_tbl Where confirmation_id = '".$chkPrevHPRow->confirmation_id."' And document_id='".$HPScanDocRow->document_id."' ";
				$HPScanUploadSql	=	imw_query($HPScanUploadQry) or die($HPScanUploadQry.'---'.imw_error());
				$HPScanUploadCnt	=	imw_num_rows($HPScanUploadSql);
				
				
				if($HPScanUploadCnt)
				{
					while($HPScanUploadRow	=	imw_fetch_object($HPScanUploadSql))
					{
						$sql = '';
						unset($HPScanUploadRow->scan_upload_id);
						$HPScanUploadRow->stub_id = $_REQUEST['stub_id'];
						$HPScanUploadRow->document_id = $documentId;
						$HPScanUploadRow->confirmation_id = $_REQUEST['pConfId'];
						
						$HPScanUploadRow->document_name = 	addslashes($HPScanUploadRow->document_name);
						$HPScanUploadRow->img_content   =	addslashes($HPScanUploadRow->img_content);
						
						foreach($HPScanUploadRow as $fieldName=>$fieldValue)
						{
							$sql .=	", ".$fieldName." = '".$HPScanUploadRow->$fieldName."' ";		
						}
						$sql = ($sql) ? substr($sql,1) : '' ;
						$HPUploadInsertQry = "Insert Into scan_upload_tbl Set ".$sql." ";
						//echo $HPUploadInsertQry.'<br><br>';
						imw_query($HPUploadInsertQry) or die($HPUploadInsertQry.'---'.imw_error());
					
					} // End While
				}
				
			}
			
			// End Copying scan document for H&P Folder
			
			
			// Start Copying Predefine Questions
			$getAddQuestions = $objManageData->getAllRecords('history_physical_ques','', array('confirmation_id = '=>$chkPrevHPRow->confirmation_id), array(),array('ques + 0'=>'ASC'));
			$predefineHPCount = count($getAddQuestions);
			if( is_array($getAddQuestions) && $predefineHPCount > 0 ) {
				foreach($getAddQuestions as $qArr) {
					$HPQuesQry = "Insert Into history_physical_ques Set confirmation_id = '".$_REQUEST['pConfId']."', patient_id = '".$_REQUEST['patient_id']."', ques = '".$qArr->ques."', ques_status = '".$qArr->ques_status."', ques_desc = '".$qArr->ques_desc."' ";
					imw_query($HPQuesQry) or die($HPQuesQry.'---'.imw_error());
				}
			}
			// End Copying Predefine Questions
			
		}
	}
	
}

?>