<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include_once("common/conDb.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;
$localAnesthesiaRecordId = $_REQUEST['localAnesthesiaRecordId'];
$hidd_delImage = $_REQUEST['hidd_delImage'];
$uploadImage = $_FILES['uploadImage']['name'];

if($hidd_delImage=='yes') { //DELETE IMAGE
		$anesRecordDetails = $objManageData->getRowRecord('localanesthesiarecord', 'localAnesthesiaRecordId', $localAnesthesiaRecordId);
		//$field_anes_ScanUploadPath = $anesRecordDetails->anes_ScanUploadPath;		
		if($field_anes_ScanUploadPath) {
			if(file_exists($field_anes_ScanUploadPath)) {
				unlink($field_anes_ScanUploadPath);	
			}
		}
		unset($arrayRecord);
		$arrayRecord['anes_ScanUpload'] 			= "";
		$arrayRecord['anes_ScanUploadType'] 		= "";
		$arrayRecord['anes_ScanStatus'] 			= "";  //THIS INDICATE THAT FIRST IMAGE/FILE IS YET TO SCAN
		$arrayRecord['anes_ScanUploadDateTime'] 	= "";
		$arrayRecord['anes_ScanUploadPath'] 		= "";
		$objManageData->updateRecords($arrayRecord, 'localanesthesiarecord', 'localAnesthesiaRecordId', $localAnesthesiaRecordId);

}else {  
	if(!empty($_FILES["uploadImage"]["name"])){
		$iolName 		= $_FILES["uploadImage"]["name"];
		$iolTmp 		= $_FILES["uploadImage"]["tmp_name"];		
		$iolSize 		= $_FILES["uploadImage"]["size"];
		$iolType 		= $_FILES["uploadImage"]["type"];
		$iolTempFile 	= fopen($_FILES["uploadImage"]["tmp_name"], "r");
		
		$iolImg 		= addslashes(fread($iolTempFile, $iolSize));
			
		$folderPath = 'admin/pdfFiles/local_anes_detail';
		if(!is_dir($folderPath)) {mkdir($folderPath, 0777);}
		$iolExtExplode = explode("/",$iolType);
		$iolExt = end($iolExtExplode);
		if($iolExt && $iolExt!='pdf') { 
			$iolExt='jpg';
			if(file_exists($folderPath.'/anes_'.$localAnesthesiaRecordId.'.pdf')) {
				unlink($folderPath.'/anes_'.$localAnesthesiaRecordId.'.pdf');	
			}
		}else if($iolExt) {
			if(file_exists($folderPath.'/anes_'.$localAnesthesiaRecordId.'.jpg')) {
				unlink($folderPath.'/anes_'.$localAnesthesiaRecordId.'.jpg');	
			}
			
		}
		$scanUploadSavePath = $folderPath.'/anes_'.$localAnesthesiaRecordId.'.'.$iolExt;
		copy($_FILES["uploadImage"]["tmp_name"],$folderPath.'/anes_'.$localAnesthesiaRecordId.'.'.$iolExt);
	
		unset($arrayRecord);
		$arrayRecord['anes_ScanUploadType'] = $iolType;
		$arrayRecord['anes_ScanStatus'] = 'UPLOADED';
		//$arrayRecord['anes_ScanUpload'] = $iolImg;
		$arrayRecord['anes_ScanUploadName'] = $iolName;
		$arrayRecord['anes_ScanUploadPath'] = $scanUploadSavePath;
		$arrayRecord['anes_ScanUploadDateTime'] = date('Y-m-d H:i:s');
		
		
		$objManageData->updateRecords($arrayRecord, 'localanesthesiarecord', 'localAnesthesiaRecordId', $localAnesthesiaRecordId);
	}
}	

?>
