<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
include("common/conDb.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
$showFor = $_REQUEST['from'];
	if($showFor == 'surgery_center'){
		$surgeryCenterDetails = $objManageData->getRowRecord('surgerycenter', 'surgeryCenterId', 1);
		$surgeryCenterLogo = $surgeryCenterDetails->surgeryCenterLogo;
		$logoType = $surgeryCenterDetails->logoType;
		header($logoType);
		echo $surgeryCenterLogo;
	}else if($showFor == 'op_room_record'){
		$operatingRoomRecordsId = $_REQUEST['id'];
		$operatingRoomRecordDetails = $objManageData->getRowRecord('operatingroomrecords', 'operatingRoomRecordsId', $operatingRoomRecordsId);
		$iolType = $operatingRoomRecordDetails->iol_type;
		$imgIol = $operatingRoomRecordDetails->iol_ScanUpload;
		//header($iolType);
		//echo $imgIol;
		//echo $imgIol;
		if($imgIol) {
			header($iolType);
			echo $imgIol;
		}	
		
		$iolType2 = $operatingRoomRecordDetails->iol_type2;
		$imgIol2 = $operatingRoomRecordDetails->iol_ScanUpload2;
		if($imgIol2) {
			header($iolType2);
			echo $imgIol2;
		}	
		
	}else if($showFor == 'discharge_summary_sheet'){
		$dischargeSummarySheetId = $_REQUEST['id'];
		$dischargeSummarySheetDetails = $objManageData->getRowRecord('dischargesummarysheet', 'dischargeSummarySheetId', $dischargeSummarySheetId);
		$iolType = $dischargeSummarySheetDetails->dis_type;
		$imgIol = $dischargeSummarySheetDetails->dis_ScanUpload;
		if($imgIol) {
			header($iolType);
			echo $imgIol;
		}	
		
		$iolType2 = $dischargeSummarySheetDetails->dis_type2;
		$imgIol2 = $dischargeSummarySheetDetails->dis_ScanUpload2;
		if($imgIol2) {
			header($iolType2);
			echo $imgIol2;
		}	
		
	}else if($showFor == 'Consent'){
		$scan_upload_id = $_REQUEST['scan_upload_id'];
		$dataContents = $objManageData->getRowRecord('scan_upload_tbl', 'scan_upload_id', $scan_upload_id);
			$imageCont = $dataContents->img_content;
			$imageType = $dataContents->document_type;
		header($imageType);
		echo $imageCont;		
	}else if($showFor == 'ScanPopUP'){
		$imageId = $_REQUEST['imageId'];
		$imageScaned = $objManageData->getRowRecord('scan_upload_tbl', 'scan_upload_id', $imageId);
			$img_content = $imageScaned->img_content;
			$img_type = $imageScaned->image_type;
			header($img_type);
			echo $img_content;
	}else if($showFor == 'ScanSubPopUP'){
		$imageId = $_REQUEST['SubImageId'];
		$imageScaned = $objManageData->getRowRecord('scan_upload_tbl', 'parent_sub_doc_id', $imageId);
			$img_content = $imageScaned->img_content;
			$img_type = $imageScaned->image_type;
			header($img_type);
			echo $img_content;
	}else if($showFor == 'iolink_scan_consent'){
		$iolinkScanConsentId = $_REQUEST['id'];
		$iolinkScanConsentDetails = $objManageData->getRowRecord('iolink_scan_consent', 'scan_consent_id', $iolinkScanConsentId);
		$iolType = $iolinkScanConsentDetails->image_type;
		//$iolType = 'SCANED';
		$imgIol = $iolinkScanConsentDetails->scan1Upload;
		if($imgIol) {
			header($iolType);
			echo $imgIol;
		}	
	}else if($showFor == 'iolink_insurance_card'){
		$insuranceId = $_REQUEST['id'];
		$insuranceDetails = $objManageData->getRowRecord('insurance_data', 'id', $insuranceId);
		//$iolType = $insuranceDetails->dis_type;
		$iolType = 'SCANED';
		$imgIol = $insuranceDetails->insScan1Upload;
		if($imgIol) {
			//header($iolType);
			echo $imgIol;
		}	
	}else{
		$dischargeSummarySheetId = $_REQUEST['id'];
		$dischargeSheetDetails = $objManageData->getRowRecord('dischargesummarysheet', 'dischargeSummarySheetId', $dischargeSummarySheetId);
		$imgType = $dischargeSheetDetails->img_type;
		$imgDischargeSheet = $dischargeSheetDetails->img_binary;
		header($imgType);
		echo $imgDischargeSheet;
	}
	
?>