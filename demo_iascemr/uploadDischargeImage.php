<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include_once("common/conDb.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
$dischargeSummarySheetId = $_REQUEST['dischargeSummarySheetId'];
$hidd_delImage = $_REQUEST['hidd_delImage'];
$uploadImage = $_FILES['uploadImage']['name'];

if($hidd_delImage=='yes') { //DELETE IMAGE
		unset($arrayRecord);
		$arrayRecord['dis_ScanUpload'] = "";
		$arrayRecord['dis_ScanStatus'] = "";  //THIS INDICATE THAT FIRST IMAGE/FILE IS YET TO SCAN
		$objManageData->updateRecords($arrayRecord, 'dischargesummarysheet', 'dischargeSummarySheetId', $dischargeSummarySheetId);

}else if($hidd_delImage=='yes2') { //DELETE SECONDE IMAGE
		unset($arrayRecord);
		$arrayRecord['dis_ScanUpload2'] = "";
		$arrayRecord['dis_ScanStatus2'] = "";  //THIS INDICATE THAT FIRST IMAGE/FILE IS YET TO SCAN
		$objManageData->updateRecords($arrayRecord, 'dischargesummarysheet', 'dischargeSummarySheetId', $dischargeSummarySheetId);

}else {  
	if(!empty($_FILES["uploadImage"]["name"])){
		$iolName = $_FILES["uploadImage"]["name"];
		$iolTmp = $_FILES["uploadImage"]["tmp_name"];		
		$iolSize = $_FILES["uploadImage"]["size"];
		$iolTempFile = fopen($_FILES["uploadImage"]["tmp_name"], "r");
		
		$iolImg = addslashes(fread($iolTempFile, $iolSize));
		$dis_type = $_FILES["uploadImage"]["type"];

		unset($arrayRecord);
		$arrayRecord['image_type'] = $dis_type;
		$arrayRecord['img_content'] = $iolImg;
		$arrayRecord['document_name'] = $iolName;
		$arrayRecord['document_size'] = $iolSize;
		$arrayRecord['confirmation_id'] = $_REQUEST["pConfId"];
		//$objManageData->addRecords($arrayRecord, 'scan_upload_tbl');
		

		$IOLoperatingRoomRecordDetails = $objManageData->getRowRecord('dischargesummarysheet', 'dischargeSummarySheetId', $dischargeSummarySheetId);
		$field_dis_ScanUpload = $IOLoperatingRoomRecordDetails->dis_ScanUpload;		
		$field_dis_ScanUpload2 = $IOLoperatingRoomRecordDetails->dis_ScanUpload2;		
			
		//$field_dis_ScanStatus = $dis_scan_operatingRoomRecordDetails->dis_ScanStatus;		
		//$field_dis_ScanStatus2 = $dis_scan_operatingRoomRecordDetails->dis_ScanStatus2;		
		
		//unset($arrayRecord);
		//$arrayRecord['dis_ScanUpload'] = $iolImg;
		if(!$field_dis_ScanUpload) {
			unset($arrayRecord);
			$arrayRecord['dis_ScanStatus'] = '';  //THIS INDICATE THAT FIRST IMAGE/FILE IS YET TO SCAN
			$arrayRecord['dis_ScanUpload'] = $iolImg;
		}else if(!$field_dis_ScanUpload2) {
			unset($arrayRecord);
			$arrayRecord['dis_ScanStatus2'] = ''; //THIS INDICATE THAT SECOND IMAGE/FILE IS YET TO SCAN
			$arrayRecord['dis_ScanUpload2'] = $iolImg;
		}else {
			echo "<script>alert('Can not upload more than two files');</script>";
			exit();
		}
		
		$objManageData->updateRecords($arrayRecord, 'dischargesummarysheet', 'dischargeSummarySheetId', $dischargeSummarySheetId);
	}
}	

?>
