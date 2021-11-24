<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");
include_once("../../common/conDb.php");
//$idocDB = "asheville_imwemr";
//$scemrDB = "asheville_scemr";

/*
USE asheville_scemr;

ALTER TABLE `stub_tbl` ADD `orig_imwPatientId` VARCHAR( 255 ) NOT NULL; 
ALTER TABLE `patientconfirmation` ADD `orig_imwPatientId` VARCHAR( 255 ) NOT NULL; 
ALTER TABLE `patient_data_tbl` ADD `orig_imwPatientId` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE `patient_in_waiting_tbl` ADD `orig_drOfficePatientId` VARCHAR( 255 ) NOT NULL;

ALTER TABLE `stub_tbl` CHANGE `orig_imwPatientId` `orig_imwPatientId` BIGINT( 20 ) NOT NULL 
ALTER TABLE `patientconfirmation` CHANGE `orig_imwPatientId` `orig_imwPatientId` BIGINT( 20 ) NOT NULL 
ALTER TABLE `patient_data_tbl` CHANGE `orig_imwPatientId` `orig_imwPatientId` BIGINT( 20 ) NOT NULL 



ALTER TABLE `stub_tbl` ADD INDEX  `orig_imwPatientId` (  `orig_imwPatientId` );
ALTER TABLE `patientconfirmation` ADD INDEX  `orig_imwPatientId` (  `orig_imwPatientId` );
ALTER TABLE `patient_data_tbl` ADD INDEX  `orig_imwPatientId` (  `orig_imwPatientId` );
ALTER TABLE `patient_in_waiting_tbl` ADD INDEX  `orig_drOfficePatientId` (  `orig_drOfficePatientId` );

UPDATE stub_tbl SET orig_imwPatientId = imwPatientId WHERE 1;
UPDATE patientconfirmation SET orig_imwPatientId = imwPatientId WHERE 1;
UPDATE patient_data_tbl SET orig_imwPatientId = imwPatientId WHERE 1;
UPDATE patient_in_waiting_tbl SET orig_drOfficePatientId = drOfficePatientId WHERE 1;

CREATE  TABLE asheville_scemr.stub_tbl_05may18 LIKE asheville_scemr.stub_tbl;
CREATE  TABLE asheville_scemr.patientconfirmation_05may18 LIKE asheville_scemr.patientconfirmation;
CREATE  TABLE asheville_scemr.patient_data_tbl_05may18 LIKE asheville_scemr.patient_data_tbl;
CREATE  TABLE asheville_scemr.patient_in_waiting_tbl_05may18 LIKE asheville_scemr.patient_in_waiting_tbl;


INSERT INTO asheville_scemr.stub_tbl_05may18 (SELECT *  FROM asheville_scemr.stub_tbl);
INSERT INTO asheville_scemr.patientconfirmation_05may18 (SELECT *  FROM asheville_scemr.patientconfirmation);
INSERT INTO asheville_scemr.patient_data_tbl_05may18 (SELECT *  FROM asheville_scemr.patient_data_tbl);
INSERT INTO asheville_scemr.patient_in_waiting_tbl_05may18 (SELECT *  FROM asheville_scemr.patient_in_waiting_tbl);

*/

include("../../connect_imwemr.php");
$imwIdArr = array();
$qry = "SELECT id, orig_id FROM patient_data  ORDER BY orig_id";
$res = imw_query($qry)  or die(imw_error().$qry);
if(imw_num_rows($res)>0) {
	while($row = imw_fetch_assoc($res)) {
		$imwId 		= $row["id"];
		$imwOrigId 	= $row["orig_id"];
		$imwIdArr[$imwOrigId] = $imwId;
	}
}
//print'<pre>';
//print_r($imwIdArr);die;
include("../../common/conDb.php");

$qry = "SELECT stub_id,orig_imwPatientId FROM stub_tbl  ORDER BY stub_id";
$res = imw_query($qry) or die(imw_error());	
if(imw_num_rows($res)>0) {
	while($row = imw_fetch_assoc($res)) {
		$stub_id = $row["stub_id"];
		$orig_imwPatientIdStub = $row["orig_imwPatientId"];
		$newPtIdStub = $imwIdArr[$orig_imwPatientIdStub];
		if($newPtIdStub!="") {
			$uQry = "UPDATE stub_tbl SET imwPatientId = '".$newPtIdStub."' WHERE stub_id = '".$stub_id."'";	
			imw_query($uQry)  or die(imw_error().$uQry);
		}
	}
}

$qry = "SELECT patient_id,orig_imwPatientId FROM patient_data_tbl  ORDER BY patient_id ";
$res = imw_query($qry) or die(imw_error());	
if(imw_num_rows($res)>0) {
	while($row = imw_fetch_assoc($res)) {
		$patient_id = $row["patient_id"];
		$orig_imwPatientIdPatient = $row["orig_imwPatientId"];
		$newPtIdPatient = $imwIdArr[$orig_imwPatientIdPatient];
		if($newPtIdPatient!="") {
			$uQry = "UPDATE patient_data_tbl SET imwPatientId = '".$newPtIdPatient."' WHERE patient_id = '".$patient_id."'";	
			imw_query($uQry)  or die(imw_error().$uQry);
		}
	}
}

$qry = "SELECT patientConfirmationId,orig_imwPatientId FROM patientconfirmation  ORDER BY patientConfirmationId ";
$res = imw_query($qry) or die(imw_error());	
if(imw_num_rows($res)>0) {
	while($row = imw_fetch_assoc($res)) {
		$patientConfirmationId = $row["patientConfirmationId"];
		$orig_imwPatientIdConfirm = $row["orig_imwPatientId"];
		$newPtIdConfirm = $imwIdArr[$orig_imwPatientIdConfirm];
		if($newPtIdConfirm!="") {
			$uQry = "UPDATE patientconfirmation SET imwPatientId = '".$newPtIdConfirm."' WHERE patientConfirmationId = '".$patientConfirmationId."'";	
			imw_query($uQry)  or die(imw_error().$uQry);	
		}
	}
}


$ptQry = "SELECT pwt.patient_in_waiting_id, pwt.patient_id, pwt.drOfficePatientId FROM ".$scemrDB.".patient_in_waiting_tbl pwt ORDER BY pwt.patient_id ASC";
$res = imw_query($ptQry)or $msg_info[] = $ptQry.imw_error();
if(imw_num_rows($res)>0){
	while($row = imw_fetch_assoc($res))
	{
		list($imwPatientId, $scemrPatientId) = explode('-',$row['drOfficePatientId']);
		if($imwPatientId)
		{
			$imwPatientIdNew = $imwIdArr[$imwPatientId];
			$drOfficePatientID_New = $imwPatientIdNew.'-'.$scemrPatientId;
			$uQry = "Update ".$scemrDB.".patient_in_waiting_tbl pwt SET pwt.drOfficePatientId = '".$drOfficePatientID_New."' WHERE pwt.patient_in_waiting_id = ".$row['patient_in_waiting_id'];	
			imw_query($uQry) or $msg_info[] = 'PatientWaitingID = '.$uQry.imw_error();
		}
	}
}


echo "UPDATE Done";
?>