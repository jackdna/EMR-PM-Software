<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
//START CREATE PATIENT-INFO, CLINICAL AND OTHER FOLDERS FOR SCAN AND INSERT SCAN CARD FROM IOLINK	
	$iolinkFormFolderArr = array('Pt. Info', 'Clinical','IOL','H&P','EKG','Health Questionnaire','Ocular Hx','Consent');
	//$iolinkFormFolderArr = array('Pt. Info', 'Clinical');
	foreach($iolinkFormFolderArr as $iolinkFormFolder){
		
		$iolinkScanFolderName = $iolinkFormFolder; //DEFAULT SETTING
		if($iolinkFormFolder=='Pt. Info') 					{	$iolinkScanFolderName = 'ptInfo';
		}else if($iolinkFormFolder=='Clinical') 			{	$iolinkScanFolderName = 'clinical';
		}else if($iolinkFormFolder=='IOL') 					{	$iolinkScanFolderName = 'iol';
		}else if($iolinkFormFolder=='H&P') 					{	$iolinkScanFolderName = 'h&p';
		}else if($iolinkFormFolder=='EKG') 					{	$iolinkScanFolderName = 'ekg';
		}else if($iolinkFormFolder=='Health Questionnaire') {	$iolinkScanFolderName = 'healthQuest';
		}else if($iolinkFormFolder=='Ocular Hx') 			{	$iolinkScanFolderName = 'ocularHx';
		}else if($iolinkFormFolder=='Consent') 				{	$iolinkScanFolderName = 'consent';
		}	
		
		$iolink_scan_consent_qry 			= "select * from iolink_scan_consent where patient_in_waiting_id = '".$iolink_patient_in_waiting_id."' AND iolink_scan_folder_name = '".$iolinkScanFolderName."' AND patient_id = '".$patient_id."'";
		$iolink_scan_consent_res 			= imw_query($iolink_scan_consent_qry) or die(imw_error());
		$iolink_scan_consent_numrow 		= imw_num_rows($iolink_scan_consent_res);
		
		/* $stubIdQry - it is from admin/scanPopUp.php */
		$chk_iolink_scan_document_qry 		= "select document_id from scan_documents where document_name = '".$iolinkFormFolder."' AND patient_id = '".$patient_id."' AND confirmation_id = '".$pConfId."' AND dosOfScan = '".$Confirm_patientDos."'".$stubIdQry;
		$chk_iolink_scan_document_res 		= imw_query($chk_iolink_scan_document_qry) or die(imw_error());
		$chk_iolink_scan_document_numrow	= imw_num_rows($chk_iolink_scan_document_res);
		
		if($iolink_scan_consent_numrow>0 || $iolinkFormFolder=='Pt. Info' || $iolinkFormFolder=='Clinical') {
			if($chk_iolink_scan_document_numrow<=0) {
				unset($iolinkArrayScanRecord);
				$iolinkArrayScanRecord['patient_id'] 		= $patient_id;
				$iolinkArrayScanRecord['confirmation_id'] 	= $pConfId;
				$iolinkArrayScanRecord['document_name'] 	= $iolinkFormFolder;
				$iolinkArrayScanRecord['dosOfScan'] 		= $Confirm_patientDos;
				$iolinkArrayScanRecord['stub_id'] 			= $stub_id;
				$iolinkInsertScanId = $objManageData->addRecords($iolinkArrayScanRecord, 'scan_documents');
			}else if($chk_iolink_scan_document_numrow>0) {
				$chk_iolink_scan_document_row= imw_fetch_array($chk_iolink_scan_document_res);
				$iolinkInsertScanId 		 = $chk_iolink_scan_document_row['document_id'];
			}
		}
		if($iolink_scan_consent_numrow>0) {
			while($iolink_scan_consent_row 	= imw_fetch_array($iolink_scan_consent_res)) {
				$iolinkScanConsentId 		= $iolink_scan_consent_row['scan_consent_id'];
				$iolink_img_content 		= $iolink_scan_consent_row['scan1Upload'];
				$iolink_upload_document_name= urldecode($iolink_scan_consent_row['document_name']);
				
				$iolink_mask 				= urldecode($iolink_scan_consent_row['mask']);
								
				//START REMOVE .JPG FROM DOCUMENT NAME
				if($iolink_upload_document_name) {
					$iolinkDocumentNameArr 	= explode('.',$iolink_upload_document_name);
					if(trim($iolinkDocumentNameArr[1])=='jpg') {
						$iolink_upload_document_name = $iolinkDocumentNameArr[0];
					}	
				}
				if($iolink_mask) { 
					$iolinkMaskArr 			= explode('.',$iolink_mask);
					$iolink_upload_document_name = $iolinkMaskArr[0];
				}
				//END REMOVE .JPG FROM DOCUMENT NAME
				
				$iolink_image_type 			= $iolink_scan_consent_row['image_type'];
				$iolink_document_size 		= $iolink_scan_consent_row['document_size'];
				$scan_save_date_time 		= $iolink_scan_consent_row['scan_save_date_time'];
				$iolink_pdfFilePath 		= urldecode($iolink_scan_consent_row['pdfFilePath']);
				$iolink_pdfFilePath 		= str_ireplace("//","/",$iolink_pdfFilePath);
				//if($iolink_img_content) {
					unset($iolinkArrayScanUploadRecord);
					
					$iolinkArrayScanUploadRecord['image_type'] 				= $iolink_image_type;
					$iolinkArrayScanUploadRecord['pdfFilePath'] 			= $iolink_pdfFilePath;
					$iolinkArrayScanUploadRecord['document_size'] 			= $iolink_document_size;
					$iolinkArrayScanUploadRecord['document_name'] 			= addslashes($iolink_upload_document_name);
					$iolinkArrayScanUploadRecord['document_size'] 			= '';
					$iolinkArrayScanUploadRecord['confirmation_id'] 		= $pConfId;
					$iolinkArrayScanUploadRecord['patient_id'] 				= $patient_id;
					$iolinkArrayScanUploadRecord['document_id'] 			= $iolinkInsertScanId;
					$iolinkArrayScanUploadRecord['stub_id'] 				= $stub_id;
					$iolinkArrayScanUploadRecord['img_content'] 			= addslashes($iolink_img_content);
					$iolinkArrayScanUploadRecord['iolink_scan_consent_id'] 	= $iolinkScanConsentId;
					$iolinkArrayScanUploadRecord['dosOfScan'] 				= $Confirm_patientDos;
					$iolinkArrayScanUploadRecord['scan_upload_save_date_time'] 	= $scan_save_date_time;
					if($iolink_pdfFilePath) {
						$iolinkPdfFileFullPath 			= 	$rootServerPath.'/'.$iolinkDirectoryName.'/'.'admin/'.$iolink_pdfFilePath;
						$surgerycenterPdfFileFullPath 	= 	$rootServerPath.'/'.$surgeryCenterDirectoryName.'/'.'admin/'.$iolink_pdfFilePath;
						
						$iolink_pdfFilePathExplode = explode('/',$iolink_pdfFilePath);
						
						$iolink_pdfFilePathExplodeNew = '/'.$iolink_pdfFilePathExplode[1];
						if(stristr($iolink_pdfFilePathExplode[1],".pdf") !== false || stristr($iolink_pdfFilePathExplode[1],".jpg") !== false) {
							$iolink_pdfFilePathExplodeNew = '';
							$oldPdfFolderNameInSurgerycenter = $rootServerPath.'/'.$surgeryCenterDirectoryName.'/'.'admin/'.$iolink_pdfFilePathExplode[0].'/'.$iolink_pdfFilePathExplode[1];
							if(is_dir($oldPdfFolderNameInSurgerycenter)) {
								$newPdfFolderNameInSurgerycenter = str_ireplace(".","_",$oldPdfFolderNameInSurgerycenter);
								rename($oldPdfFolderNameInSurgerycenter,$newPdfFolderNameInSurgerycenter);
							}
						}
						$pdfFolderNameInSurgerycenter = $rootServerPath.'/'.$surgeryCenterDirectoryName.'/'.'admin/'.$iolink_pdfFilePathExplode[0].$iolink_pdfFilePathExplodeNew;

						//if(!file_exists($surgerycenterPdfFileFullPath)){
							//if(file_exists($iolinkPdfFileFullPath)){
								if(is_dir($pdfFolderNameInSurgerycenter)) {
									//DO NOT CREATE FOLDER AGAIN
								}else {
									mkdir($pdfFolderNameInSurgerycenter, 0777);
								}
								
								$iolinkPdfFileContent = @file_get_contents($iolinkPdfFileFullPath);
								@file_put_contents($surgerycenterPdfFileFullPath,$iolinkPdfFileContent);	
								/*
								if(is_dir($pdfFolderNameInSurgerycenter)) {
									@copy($iolinkPdfFileFullPath,$surgerycenterPdfFileFullPath);
								}*/
							//}
						//}
					}
					
					//GET INSERT OR UPDATE
					$iolinkScanedCardAlreadyExist = $objManageData->getRowRecord('scan_upload_tbl', 'iolink_scan_consent_id', $iolinkScanConsentId);
					if($iolinkScanedCardAlreadyExist){
						//$objManageData->updateRecords($iolinkArrayScanUploadRecord, 'scan_upload_tbl', 'iolink_scan_consent_id', $iolinkScanConsentId);
					}else{
						$objManageData->addRecords($iolinkArrayScanUploadRecord, 'scan_upload_tbl');		
					}
				//}
			}
		}
	}
		//END CREATE PATIENT-INFO, CLINICAL AND OTHER FOLDERS FOR SCAN AND INSERT SCAN CARD FROM IOLINK	
?>