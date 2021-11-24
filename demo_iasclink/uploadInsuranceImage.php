<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
include_once("common/conDb.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
$insuranceId = $_REQUEST['insuranceId'];
$hidd_delImage = $_REQUEST['hidd_delImage'];

$patient_id = $_REQUEST['patient_id'];
$patient_in_waiting_id = $_REQUEST['patient_in_waiting_id'];
$insuranceType = $_REQUEST['insuranceType'];

$uploadImage = $_FILES['uploadImage']['name'];

if(!$insuranceId) {
	$chkQry = "select * from insurance_data where  waiting_id = '".$patient_in_waiting_id."' and type = '".$insuranceType."'";
	$chkRes = imw_query($chkQry);
	$chkRow = @imw_fetch_array($chkRes);
	$insuranceId = $chkRow['id']; 
}

if($hidd_delImage=='yes') { //DELETE IMAGE
		unset($arrayRecord);
		$arrayRecord['insScan1Upload'] = "";
		$arrayRecord['insScan1Status'] = "";  //THIS INDICATE THAT FIRST IMAGE/FILE IS YET TO SCAN
		$objManageData->updateRecords($arrayRecord, 'insurance_data', 'id', $insuranceId);

}else if($hidd_delImage=='yes2') { //DELETE SECONDE IMAGE
		unset($arrayRecord);
		$arrayRecord['insScan2Upload'] = "";
		$arrayRecord['insScan2Status'] = "";  //THIS INDICATE THAT FIRST IMAGE/FILE IS YET TO SCAN
		$objManageData->updateRecords($arrayRecord, 'insurance_data', 'id', $insuranceId);

}else {  
	if(!empty($_FILES["uploadImage"]["name"])){
		$iolName = $_FILES["uploadImage"]["name"];
		$iolTmp = $_FILES["uploadImage"]["tmp_name"];		
		$iolSize = $_FILES["uploadImage"]["size"];
		$iolTempFile = fopen($_FILES["uploadImage"]["tmp_name"], "r");
		
		$iolImg = addslashes(fread($iolTempFile, $iolSize));
		$dis_type = $_FILES["uploadImage"]["type"];

		$insuranceDataDetails = $objManageData->getRowRecord('insurance_data', 'id', $insuranceId);
		if($insuranceDataDetails) {
			$field_insScan1Upload = $insuranceDataDetails->insScan1Upload;		
			$field_insScan2Upload = $insuranceDataDetails->insScan2Upload;		
				
			if(!$field_insScan1Upload) {
				unset($arrayRecord);
				$arrayRecord['insScan1Status'] = '';  //THIS INDICATE THAT FIRST IMAGE/FILE IS YET TO SCAN
				$arrayRecord['insScan1Upload'] = $iolImg;
				
			}else if(!$field_insScan2Upload) {
				unset($arrayRecord);
				$arrayRecord['insScan2Status'] = ''; //THIS INDICATE THAT SECOND IMAGE/FILE IS YET TO SCAN
				$arrayRecord['insScan2Upload'] = $iolImg;
			
			}else {
				echo "<script>alert('Can not upload more than two files');</script>";
				exit();
			}
			
			$objManageData->updateRecords($arrayRecord, 'insurance_data', 'id', $insuranceId);
		}else {
				unset($arrayRecord);
				$arrayRecord['insScan1Status'] = '';  //THIS INDICATE THAT FIRST IMAGE/FILE IS YET TO SCAN
				$arrayRecord['insScan1Upload'] = $iolImg;
				
				$arrayRecord['waiting_id'] = $patient_in_waiting_id;
				$arrayRecord['patient_id'] = $patient_id;
				$arrayRecord['type'] = $insuranceType;
				$objManageData->addRecords($arrayRecord, 'insurance_data');
		}
	}
}	

?>
