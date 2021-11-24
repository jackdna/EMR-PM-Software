<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
include_once("../globalsSurgeryCenter.php");
include_once("logout.php");
include_once("classObjectFunction.php");
$objManageData = new manageData;
$showFor = $_REQUEST['from'];
$contentType = "Content-Type:";
	if($showFor == 'surgery_center'){
		$surgeryCenterDetails = $objManageData->getRowRecord('surgerycenter', 'surgeryCenterId', 1);
		$surgeryCenterLogo = $surgeryCenterDetails->surgeryCenterLogo;
		$logoType = $surgeryCenterDetails->logoType;
		if($surgeryCenterLogo) {
			header($contentType.$logoType);
			echo $surgeryCenterLogo;
		}
	}else if($showFor == 'op_room_record'){
		$operatingRoomRecordsId = $_REQUEST['id'];
		$operatingRoomRecordDetails = $objManageData->getRowRecord('operatingroomrecords', 'operatingRoomRecordsId', $operatingRoomRecordsId);
		$iolType = $operatingRoomRecordDetails->iol_type;
		$imgIol = $operatingRoomRecordDetails->iol_ScanUpload;
		//header($iolType);
		//echo $imgIol;
		//echo $imgIol;
		if($imgIol) {
			header($contentType.$iolType);
			echo $imgIol;
		}	
		
		$iolType2 = $operatingRoomRecordDetails->iol_type2;
		$imgIol2 = $operatingRoomRecordDetails->iol_ScanUpload2;
		if($imgIol2) {
			header($contentType.$iolType2);
			echo $imgIol2;
		}	
		
	}else if($showFor == 'discharge_summary_sheet'){
		$dischargeSummarySheetId = $_REQUEST['id'];
		$dischargeSummarySheetDetails = $objManageData->getRowRecord('dischargesummarysheet', 'dischargeSummarySheetId', $dischargeSummarySheetId);
		$iolType = $dischargeSummarySheetDetails->dis_type;
		$imgIol = $dischargeSummarySheetDetails->dis_ScanUpload;
		if($imgIol) {
			header($contentType.$iolType);
			echo $imgIol;
		}	
		
		$iolType2 = $dischargeSummarySheetDetails->dis_type2;
		$imgIol2 = $dischargeSummarySheetDetails->dis_ScanUpload2;
		if($imgIol2) {
			header($contentType.$iolType2);
			echo $imgIol2;
		}
			
	//START	
	}else if($showFor == 'local_anesthesia_record'){
		$localAnesthesiaRecordId = $_REQUEST['id'];
		$anesthesiaSummarySheetDetails = $objManageData->getRowRecord('localanesthesiarecord', 'localAnesthesiaRecordId', $localAnesthesiaRecordId);
		$iolType = $anesthesiaSummarySheetDetails->anes_ScanUploadType;
		$imgIol = $anesthesiaSummarySheetDetails->anes_ScanUpload;
		if($imgIol) {
			header($contentType.$iolType);
			echo $imgIol;
		}	
		/*
		$iolType2 = $anesthesiaSummarySheetDetails->dis_type2;
		$imgIol2 = $anesthesiaSummarySheetDetails->dis_ScanUpload2;
		if($imgIol2) {
			header($contentType.$iolType2);
			echo $imgIol2;
		}*/	
		
	//END	
	}else if($showFor == 'Consent'){
		$scan_upload_id = $_REQUEST['scan_upload_id'];
		$dataContents = $objManageData->getRowRecord('scan_upload_tbl', 'scan_upload_id', $scan_upload_id);
			$imageCont = $dataContents->img_content;
			$imageType = $dataContents->document_type;
		header($contentType.$imageType);
		echo $imageCont;		
	}else if($showFor == 'ScanPopUP'){
		$imageId = $_REQUEST['imageId'];
		$imageScaned = $objManageData->getRowRecord('scan_upload_tbl', 'scan_upload_id', $imageId);
			$img_content = $imageScaned->img_content;
			$img_type = $imageScaned->image_type;
			header($contentType.$img_type);
			echo $img_content;
	}else if($showFor == 'ScanSubPopUP'){
		$imageId = $_REQUEST['SubImageId'];
		$imageScaned = $objManageData->getRowRecord('scan_upload_tbl', 'parent_sub_doc_id', $imageId);
			$img_content = $imageScaned->img_content;
			$img_type = $imageScaned->image_type;
			header($contentType.$img_type);
			echo $img_content;
	}else if($showFor == 'ScanPopUPUser'){
		$imageId = $_REQUEST['imageId'];
		$imageScaned = $objManageData->getRowRecord('scan_upload_tbl_user', 'scan_upload_id', $imageId);
			$img_content = $imageScaned->img_content;
			$img_type = $imageScaned->image_type;
			header($contentType.$img_type);
			echo $img_content;
	}else{
		$dischargeSummarySheetId = $_REQUEST['id'];
		$dischargeSheetDetails = $objManageData->getRowRecord('dischargesummarysheet', 'dischargeSummarySheetId', $dischargeSummarySheetId);
		$imgType = $dischargeSheetDetails->img_type;
		$imgDischargeSheet = $dischargeSheetDetails->img_binary;
		header($contentType.$imgType);
		echo $imgDischargeSheet;
	}
?>