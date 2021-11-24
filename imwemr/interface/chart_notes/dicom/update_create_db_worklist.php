<?php
set_time_limit(0);
$ignoreAuth = true;
require(dirname(__FILE__).'/dicom_link.php');
require_once(dirname(__FILE__)."/dicomworklist.php");
//error_reporting(-1);

$f="scheduled";
if(!empty($_GET["full"])){ $f="full"; }

if(strpos(DICOM_AE_WLM,",")!==false){ exit("Error: DICOM_AE_WLM have comma in  it."); }

$fac_based_db = FAC_BASE_WLM_DB;

if(!empty($fac_based_db)){

	$db_worklist = DicomWorkList::get_facility_db_nms();
	
}else{

$db_worklist = DICOM_AE_WLM_DB;
if(empty($db_worklist)){ $db_worklist = DICOM_AE_WLM;  }

}

$ar_db_worklist = explode(",", $db_worklist); //explode :can be multiple AE_DB~!~AE, AE_DB~!~AE

if(count($ar_db_worklist)>0){
	foreach($ar_db_worklist as $k => $v){
		$fac_id="";
		//check format AE_DB~!~AE
		$ar_v = explode("~!~", $v);

		$tmp_db_ae = $ar_v[0];
		if(!empty($ar_v[1])){ 
			$tmp_ae = $ar_v[1]; 
			
			//get facility id
			if(!empty($fac_based_db)){
				$ar_tmp_ae = explode("_*_", $tmp_ae);
				$fac_id = trim($ar_tmp_ae[1]);
				$tmp_ae = trim($ar_tmp_ae[0]."".$ar_tmp_ae[1]);
			}
			
		}else{ $tmp_ae = DICOM_AE_WLM;  }

		$objDicomWorkList = new DicomWorkList($tmp_db_ae, DICOM_MODALITY, $tmp_ae, DICOM_DB_PATH );
		$objDicomWorkList->clearworklist();
		$objDicomWorkList->createDB($f, $fac_id);

	}
}
echo "Process DONE!";

?>