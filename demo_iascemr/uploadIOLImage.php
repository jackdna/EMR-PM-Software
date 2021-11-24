<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include_once("common/conDb.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
$operatingRoomRecordsId = $_REQUEST['operatingRoomRecordsId'];
$hidd_delImage = $_REQUEST['hidd_delImage'];
$uploadImage = $_FILES['uploadImage']['name'];
if($hidd_delImage=='yes') { //DELETE IMAGE
		unset($arrayRecord);
		$arrayRecord['iol_ScanUpload'] = "";
		$arrayRecord['iol_ScanStatus'] = "";  //THIS INDICATE THAT FIRST IMAGE/FILE IS YET TO SCAN
		$objManageData->updateRecords($arrayRecord, 'operatingroomrecords', 'operatingRoomRecordsId', $operatingRoomRecordsId);

}else if($hidd_delImage=='yes2') { //DELETE SECONDE IMAGE
		unset($arrayRecord);
		$arrayRecord['iol_ScanUpload2'] = "";
		$arrayRecord['iol_ScanStatus2'] = "";  //THIS INDICATE THAT FIRST IMAGE/FILE IS YET TO SCAN
		$objManageData->updateRecords($arrayRecord, 'operatingroomrecords', 'operatingRoomRecordsId', $operatingRoomRecordsId);

}else {  
	if(!empty($_FILES["uploadImage"]["name"])){
		$iolName = $_FILES["uploadImage"]["name"];
		$iolTmp = $_FILES["uploadImage"]["tmp_name"];		
		$iolSize = $_FILES["uploadImage"]["size"];
		$iolTempFile = fopen($_FILES["uploadImage"]["tmp_name"], "r");
		
		$iolImg = addslashes(fread($iolTempFile, $iolSize));
		$iol_type = $_FILES["uploadImage"]["type"];

		unset($arrayRecord);
		$arrayRecord['image_type'] = $iol_type;
		$arrayRecord['img_content'] = $iolImg;
		$arrayRecord['document_name'] = $iolName;
		$arrayRecord['document_size'] = $iolSize;
		$arrayRecord['confirmation_id'] = $_REQUEST["pConfId"];
		//$objManageData->addRecords($arrayRecord, 'scan_upload_tbl');
		

		$IOLoperatingRoomRecordDetails = $objManageData->getRowRecord('operatingroomrecords', 'operatingRoomRecordsId', $operatingRoomRecordsId);
		$field_iol_ScanUpload = $IOLoperatingRoomRecordDetails->iol_ScanUpload;		
		$field_iol_ScanUpload2 = $IOLoperatingRoomRecordDetails->iol_ScanUpload2;		
			
		//$field_iol_ScanStatus = $iol_scan_operatingRoomRecordDetails->iol_ScanStatus;		
		//$field_iol_ScanStatus2 = $iol_scan_operatingRoomRecordDetails->iol_ScanStatus2;	 	
		
		//unset($arrayRecord); 
		//$arrayRecord['iol_ScanUpload'] = $iolImg;
		if(!$field_iol_ScanUpload) {
			unset($arrayRecord);
			$arrayRecord['iol_ScanStatus'] = '';  //THIS INDICATE THAT FIRST IMAGE/FILE IS YET TO SCAN
			$arrayRecord['iol_ScanUpload'] = $iolImg;
		}else if(!$field_iol_ScanUpload2) {
			unset($arrayRecord);
			$arrayRecord['iol_ScanStatus2'] = ''; //THIS INDICATE THAT SECOND IMAGE/FILE IS YET TO SCAN
			$arrayRecord['iol_ScanUpload2'] = $iolImg;
		}else {
			echo "<script>alert('Can not upload more than two files');</script>";
			exit();
		}
		
		$objManageData->updateRecords($arrayRecord, 'operatingroomrecords', 'operatingRoomRecordsId', $operatingRoomRecordsId);
	}
}
?>