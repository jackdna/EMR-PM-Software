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

 	if($showFor == 'op_room_record'){
		$operatingRoomRecordsId = $_REQUEST['id'];
		$operatingRoomRecordDetails = $objManageData->getRowRecord('operatingroomrecords', 'operatingRoomRecordsId', $operatingRoomRecordsId);
		$iolType = $operatingRoomRecordDetails->iol_type;
		$imgIol = $operatingRoomRecordDetails->iol_ScanUpload;
		
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
		
		$iolType2 = $dischargeSummarySheetDetails->dis_type2;
		
		$imgIol2 = $dischargeSummarySheetDetails->dis_ScanUpload2;
		if($imgIol2) {
			header($iolType2);
			echo $imgIol2;
		}	
	}else {
	
	}
		
?>