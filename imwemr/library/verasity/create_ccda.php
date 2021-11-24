<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/


//Function Files
// include_once(dirname(__FILE__)."/imm_data_arrays.php");
// include_once($GLOBALS["fileroot"]."/library/classes/AES.class.php");

$arrMedHxMedi = $arrMedHxAller = $arrMedHxProbList = array();
$arrData = $_REQUEST['arrData'];


$arrAllFiles = array(); 
$arrOptionsExclude = [];
$arrOptionsAll = array("mu_data_set","mu_data_set_medications","mu_data_set_problem_list","mu_data_set_allergies","mu_data_set_smoking","mu_data_set_ap","mu_data_set_superbill","mu_data_set_vs","mu_data_set_care_team_members","mu_data_set_lab", "provider_info", "location_info", "reason_for_visit", "diagnostic_tests_pending", "clinical_instruc", "future_appointment", "provider_referrals", "future_sch_test", "recommended_patient_decision_aids", "visit_medication_immu");

$arrOptions = array_diff($arrOptionsAll, $arrOptionsExclude);

//START
$patIdTmpArr = array();
foreach($arrData as $tmpArr){
	$patIdTmpArr[$tmpArr['pat_id']] = $tmpArr['pat_id'];
}
asort($patIdTmpArr);
$patIdTmp = implode(",",$patIdTmpArr);

$andDosQry = $andDosJnQry = $andPdQry = $andPdHcObsQry = $andPdHcConcernQry = $andPdHcRelObsQry = $andCmtQry = $andCmtJnQry = $andSchQry = $andVsmQry = "";
if($startDate != "" && $endDate!="") {
	$andDosQry 			.=  " AND (date_of_service BETWEEN '".getDateFormatDB($startDate)."' AND '".getDateFormatDB($endDate)."') ";
	$andDosJnQry 		.=  " AND (cmt.date_of_service BETWEEN '".getDateFormatDB($startDate)."' AND '".getDateFormatDB($endDate)."') ";
}
if($strElem_chkpid != "") {
	$andPdQry 			.=  " AND patient_data.pid IN(".$strElem_chkpid.") ";
	$andPdHcObsQry		.=  " AND pt_id IN(".$strElem_chkpid.") ";
	$andPdHcConcernQry	.=  " AND obs.pt_id IN(".$strElem_chkpid.") ";
	$andPdHcRelObsQry	.=  " AND obs.pt_id IN(".$strElem_chkpid.") ";
	$andCmtQry 			.=  " AND patient_id IN(".$strElem_chkpid.") ";
	$andCmtJnQry 		.=  " AND cmt.patient_id IN(".$strElem_chkpid.") ".$andDosJnQry;
	$andSchQry 			.=  " AND sa.sa_patient_id IN(".$strElem_chkpid.") ".$andDosJnQry;
	$andSmokeQry		.=  " AND patient_id IN(".$strElem_chkpid.") ";
	$andVsmQry			.=  " AND vsm.patient_id IN(".$strElem_chkpid.") ";
	$andRadQry			.=  " AND rad_patient_id IN(".$strElem_chkpid.") ";
	$andLabQry			.=  " AND ltd.lab_patient_id IN(".$strElem_chkpid.") ";
	$andDprQry			.=  " AND dpr.p_id IN(".$strElem_chkpid.") ";
	$andOsacndQry		.=  " AND osacn.patient_id IN(".$strElem_chkpid.") ";
	$andPhsQry 			.=  " AND patient_id IN(".$strElem_chkpid.") ";
	$andListQry			.=  " AND pid IN(".$strElem_chkpid.") ";
	$andClchQry			.=  " AND patient_id IN(".$strElem_chkpid.") ";
	$andCapQry			.=  " AND patient_id IN(".$strElem_chkpid.") ";
	$andimmunQry		.=  " AND patient_id IN(".$strElem_chkpid.") ";
	$andCsteQry			.=  " AND patient_id IN(".$strElem_chkpid.") ";
	
	
}

if(!trim($andCmtQry) && trim($patIdTmp)) {
	$andPdQry 			.=  " AND patient_data.pid IN(".$patIdTmp.") ";
	$andPdHcObsQry		.=  " AND pt_id IN(".$patIdTmp.") ";
	$andPdHcConcernQry	.=  " AND obs.pt_id IN(".$patIdTmp.") ";
	$andPdHcRelObsQry	.=  " AND obs.pt_id IN(".$patIdTmp.") ";
	$andCmtQry 			.=  " AND patient_id IN(".$patIdTmp.") ";
	$andCmtJnQry 		.=  " AND cmt.patient_id IN(".$patIdTmp.") ".$andDosJnQry;	
	$andSchQry 			.=  " AND sa.sa_patient_id IN(".$patIdTmp.") ".$andDosJnQry;
	$andSmokeQry		.=  " AND patient_id IN(".$patIdTmp.") ";
	$andVsmQry			.=  " AND vsm.patient_id IN(".$patIdTmp.") ";
	$andRadQry			.=  " AND rad_patient_id IN(".$patIdTmp.") ";
	$andLabQry			.=  " AND ltd.lab_patient_id IN(".$patIdTmp.") ";	
	$andDprQry			.=  " AND dpr.p_id IN(".$patIdTmp.") ";	
	$andOsacndQry		.=  " AND osacn.patient_id IN(".$patIdTmp.") ";
	$andPhsQry 			.=  " AND patient_id IN(".$patIdTmp.") ";
	$andListQry			.=  " AND pid IN(".$patIdTmp.") ";
	$andClchQry			.=  " AND patient_id IN(".$patIdTmp.") ";
	$andCapQry			.=  " AND patient_id IN(".$patIdTmp.") ";
	$andimmunQry		.=  " AND patient_id IN(".$patIdTmp.") ";
	$andCsteQry			.=  " AND patient_id IN(".$patIdTmp.") ";
}

$ptPatientDataRowArr = array();
$ptPatientDataQry = "SELECT patient_data.*,users.fname as ptProviderFName,users.mname as ptProviderMName,users.lname as ptProviderLName,users.user_npi as ptProviderNPI,
						refferphysician.Title as ptRefferPhyTitle,refferphysician.FirstName as ptRefferPhyFName,refferphysician.MiddleName as ptRefferPhyMName,
						refferphysician.LastName as ptRefferPhyLName,refferphysician.physician_phone as ptRefferPhyPhone
					FROM patient_data LEFT JOIN users on users.id = patient_data.providerID
					LEFT JOIN refferphysician ON refferphysician.physician_Reffer_id = patient_data.primary_care_id 
					WHERE 1=1 ".$andPdQry;
$ptPatientDataRes = imw_query($ptPatientDataQry) or die(imw_error().$ptPatientDataQry);
if(imw_num_rows($ptPatientDataRes) > 0){
	while($ptPatientDataRow = imw_fetch_assoc($ptPatientDataRes)) {
		$ptPatientDataPtId 	= $ptPatientDataRow["pid"];
		$ptPatientDataRowArr[$ptPatientDataPtId] = $ptPatientDataRow;
	}
}

$cmtQry = "SELECT id,date_of_service,patient_id,provIds FROM chart_master_table WHERE 1=1 ".$andCmtQry.$andDosQry."  ORDER BY date_of_service DESC, id";
$cmtRes = imw_query($cmtQry) or die(imw_error().$cmtQry);
$cmtRowArr = $cmtDOSArr = $cmtProvIdsArr = array();
if(imw_num_rows($cmtRes)>0) {
	while($cmtRow = imw_fetch_assoc($cmtRes)) {
		$cmtFormId = $cmtRow["id"];
		$cmtPtId = $cmtRow["patient_id"];
		$cmtRowArr[$cmtFormId] = $cmtRow;
		$cmtDOSArr[$cmtFormId] = $cmtRow['date_of_service'];
		$cmtProvIdsArr[$cmtFormId][$cmtPtId] = $cmtRow['provIds'];
		
	}
}

$ccdaCptCodeRowArr = array();
$cmtJnQry = "SELECT ct.ccda_cpt_code,cmt.id,cmt.patient_id FROM chart_master_table cmt JOIN chart_template ct ON cmt.templateId = ct.id WHERE 1=1 ".$andCmtJnQry." ORDER BY cmt.id ";
$cmtJnRes = imw_query($cmtJnQry) or die(imw_error().$cmtJnQry);
if(imw_num_rows($cmtJnRes)>0) {
	while($cmtJnRow = imw_fetch_assoc($cmtJnRes)) {
		$cmtJnFormId = $cmtJnRow["id"];
		$cmtJnPtId = $cmtJnRow["patient_id"];
		$ccdaCptCodeRowArr[$cmtJnFormId][$cmtJnPtId] = $cmtJnRow;
		
	}
}

$cmtJnDosRowArr = array();
$cmtJnDosQry = "SELECT cmt.date_of_service as date_of_service,ut.user_type_name as user_type FROM chart_master_table cmt JOIN users usr ON usr.id = cmt.providerId JOIN user_type ut ON usr.user_type = ut.user_type_id WHERE 1=1 ".$andCmtJnQry." ORDER BY cmt.id ";
$cmtJnDosRes = imw_query($cmtJnDosQry) or die(imw_error().$cmtJnDosQry);
if(imw_num_rows($cmtJnDosRes)>0) {
	while($cmtJnDosRow = imw_fetch_assoc($cmtJnDosRes)) {
		$cmtJnDosFormId = $cmtJnDosRow["id"];
		$cmtJnDosPtId = $cmtJnDosRow["patient_id"];
		$cmtJnDosRowArr[$cmtJnDosFormId] = $cmtJnDosRow;
		
	}
}


$usrRowArr = array();
$usrQry = "SELECT * FROM users ORDER BY id";  // PRIMARY PHYSICIAN
$usrRes = imw_query($usrQry) or die(imw_error().$usrQry);
if(imw_num_rows($usrRes) > 0){
	while($usrRow = imw_fetch_assoc($usrRes)) {
		$usrId = $usrRow["id"];
		$usrRowArr[$usrId] = $usrRow;
		$usrGrpArr[$usrId] = $usrRow["user_group_id"];
	}
}

$facRowArr = $facHqRowArr = array();
$facQry = "select name,phone,street,city,state,postal_code,facility_type,id as facility from facility order by id";
$facRes = imw_query($facQry) or die(imw_error().$facQry);
if(imw_num_rows($facRes) > 0){
	while($facRow = imw_fetch_assoc($facRes)) {
		$facId = $facRow["facility"];
		$facRowArr[$facId] = $facRow;
		if($facRow["facility_type"]=="1") {
			$facHqRowArr[] = $facRow;	
		}
	}
}

$refQry = "SELECT * FROM refferphysician ORDER BY physician_Reffer_id";
$refRes = imw_query($refQry) or die(imw_error().$refQry);
if(imw_num_rows($refRes) > 0){
	while($refRow = imw_fetch_assoc($refRes)) {
		$refId = $refRow["physician_Reffer_id"];
		$refRowArr[$refId] = $refRow;
	}
}

$csteRowArr = $csteRowPtArr = array();
$csteQry = "SELECT * FROM chart_schedule_test_external WHERE deleted_by = '0' ".$andCmtQry." ORDER BY patient_id ASC, id DESC";
$csteRes = imw_query($csteQry) or die(imw_error().$csteQry);
if(imw_num_rows($csteRes) > 0){
	while($csteRow = imw_fetch_assoc($csteRes)) {
		$cstePtId = $csteRow["patient_id"];
		$csteFormId = $csteRow["form_id"];
		$csteRowArr[$csteFormId] = $csteRow;
		$csteRowPtArr[$cstePtId][] = $csteRow;
	}
}


$schRowArr = array();
$schQry = "SELECT sa.sa_facility_id  as facility, sa.sa_patient_id, cmt.id as cmt_form_id FROM schedule_appointments sa JOIN chart_master_table cmt ON cmt.date_of_service = sa.sa_app_start_date WHERE 1=1 ".$andSchQry." ORDER BY sa.sa_patient_id, cmt.id";
$schRes = imw_query($schQry) or die(imw_error().$schQry);
if(imw_num_rows($schRes) > 0){
	while($schRow = imw_fetch_assoc($schRes)) {
		$schPtId 	= $schRow["sa_patient_id"];
		$schFormId 	= $schRow["cmt_form_id"];
		$schRowArr[$schFormId][$schPtId] = $schRow;
	}
}


$smokeRowArr = array();
$smokeQry = "SELECT smoking_status, patient_id,
			DATE_FORMAT(modified_on,'%Y%m%d') as smoking_modified_dt,DATE_FORMAT(modified_on,'%M %d, %Y') as smoking_modified_dt_show, 
			DATE_FORMAT(smoke_start_date,'%Y%m%d') as smoking_start_dt,DATE_FORMAT(smoke_start_date,'%M %d, %Y') as smoking_start_dt_show, 
			DATE_FORMAT(smoke_end_date,'%Y%m%d') as smoking_end_dt,DATE_FORMAT(smoke_end_date,'%M %d, %Y') as smoking_end_dt_show  
			FROM social_history WHERE 1=1 ".$andSmokeQry." ORDER BY patient_id";		
$smokeRes = imw_query($smokeQry) or die(imw_error().$smokeQry);
if(imw_num_rows($smokeRes) > 0){
	while($smokeRow = imw_fetch_assoc($smokeRes)) {
		$smokePtId 	= $smokeRow["patient_id"];
		$smokeRowArr[$smokePtId] = $smokeRow;
	}
}



$cnmhRowArr = array();
$cnmhQry = "SELECT no_value,patient_id,module_name FROM commonNoMedicalHistory WHERE module_name = 'Allergy' ".$andCmtQry;
$cnmhRes = imw_query($cnmhQry) or die(imw_error().$cnmhQry);
if(imw_num_rows($cnmhRes) > 0){
	while($cnmhRow = imw_fetch_assoc($cnmhRes)) {
		$cnmhPtId 	= $cnmhRow["patient_id"];
		$cnmhRowArr[$cnmhPtId] = $cnmhRow;
	}
}


$ptGoalRowArr = $ptGoalFormRowArr = array();
$ptGoalQry = 'SELECT pg.id, pg.patient_id, pg.form_id, pg.goal_set, pg.loinc_code, pg.goal_data, pg.goal_data_type, pg.gloal_data_type_unit, pg.operator_id,  DATE_FORMAT(pg.goal_date, \'%b %d, %Y\') AS \'goal_date\',DATE_FORMAT(pg.goal_date, \'%Y%m%d\') AS \'goal_date_show\' from patient_goals pg JOIN chart_master_table cmt ON cmt.id = pg.form_id WHERE pg.delete_status = 0 '.$andCmtJnQry;
$ptGoalRes = imw_query($ptGoalQry) or die(imw_error().$ptGoalQry);
if(imw_num_rows($ptGoalRes) > 0){
	while($ptGoalRow = imw_fetch_assoc($ptGoalRes)) {
		$ptGoalPtId 	= $ptGoalRow["patient_id"];
		$ptGoalFormId 	= $ptGoalRow["form_id"];
		$ptGoalRowArr[$ptGoalPtId][] = $ptGoalRow;
		$ptGoalFormRowArr[$ptGoalPtId][$ptGoalFormId][] = $ptGoalRow;
	}
}



$hcObsRowArr = $hcObsFormRowArr = array();
$hcObsQry = "SELECT `id`, `observation`, DATE_FORMAT(`observation_date`, '%Y%m%d') AS 'observation_date_raw',DATE_FORMAT(`observation_date`, '%b %d, %Y') AS 'observation_date',`snomed_code`,`status`,pt_id,form_id 
			 FROM `hc_observations` WHERE `del_status` = '0' ".$andPdHcObsQry;
$hcObsRes = imw_query($hcObsQry) or die(imw_error().$hcObsQry);
if(imw_num_rows($hcObsRes) > 0){
	while($hcObsRow = imw_fetch_assoc($hcObsRes)) {
		$hcObsPtId 		= $hcObsRow["pt_id"];
		$hcObsFormId 	= $hcObsRow["form_id"];
		$hcObsRowArr[$hcObsPtId][] = $hcObsRow;
		$hcObsFormRowArr[$hcObsPtId][$hcObsFormId][] = $hcObsRow;
	}
}



$hcConcernRowArr = $hcConcernFormRowArr = array();
$hcConcernQry = "SELECT `c`.`id`,`c`.`concern`, DATE_FORMAT(`c`.`concern_date`, '%Y%m%d') AS 'concern_date_raw', DATE_FORMAT(`c`.`concern_date`, '%b %d, %Y') AS 'concern_date', `c`.`status` ,obs.pt_id,obs.form_id
				FROM `hc_concerns` `c` INNER JOIN `hc_observations` `obs` ON(`c`.`observation_id`=`obs`.`id`)
				WHERE `c`.`del_status` = '0' ".$andPdHcConcernQry;
$hcConcernRes = imw_query($hcConcernQry) or die(imw_error().$hcConcernQry);
if(imw_num_rows($hcConcernRes) > 0){
	while($hcConcernRow = imw_fetch_assoc($hcConcernRes)) {
		$hcConcernPtId 		= $hcConcernRow["pt_id"];
		$hcConcernFormId 	= $hcConcernRow["form_id"];
		$hcConcernRowArr[$hcConcernPtId][] = $hcConcernRow;
		$hcConcernFormRowArr[$hcConcernPtId][$hcConcernFormId][] = $hcConcernRow;
	}
}




$hcRelObsRowArr = $hcRelObsFormRowArr = array();
$hcRelObsQry = "SELECT `rel`.`id`,`rel`.`rel_observation`,DATE_FORMAT(`rel`.`rel_observation_date`, '%Y%m%d') AS 'rel_observation_date_raw', DATE_FORMAT(`rel`.`rel_observation_date`, '%b %d, %Y') AS 'rel_observation_date', `rel`.`snomed_code` , obs.pt_id, obs.form_id
				FROM `hc_rel_observations` `rel` INNER JOIN `hc_observations` `obs` ON(`rel`.`observation_id`=`obs`.`id`)
				WHERE `rel`.`del_status` = 0 ".$andPdHcRelObsQry;
$hcRelObsRes = imw_query($hcRelObsQry) or die(imw_error().$hcRelObsQry);
if(imw_num_rows($hcRelObsRes) > 0){
	while($hcRelObsRow = imw_fetch_assoc($hcRelObsRes)) {
		$hcRelObsPtId 		= $hcRelObsRow["pt_id"];
		$hcRelObsFormId 	= $hcRelObsRow["form_id"];
		$hcRelObsRowArr[$hcRelObsPtId][] = $hcRelObsRow;
		$hcRelObsFormRowArr[$hcRelObsPtId][$hcRelObsFormId][] = $hcRelObsRow;
	}
}



$vsmRowArr = $vsmDateRowArr = array();
$vsmQry = 	"SELECT vsp.*,vsl.vital_sign,vsm.date_vital,vsm.patient_id,vsm.date_vital FROM vital_sign_master vsm 
			JOIN vital_sign_patient vsp ON vsm.id = vsp.vital_master_id 
			JOIN  vital_sign_limits vsl ON vsl.id = vsp.vital_sign_id 
			WHERE vsm.status = 0 ".$andVsmQry." ORDER BY vsp.id ASC";
$vsmRes = imw_query($vsmQry) or die(imw_error().$vsmQry);
if(imw_num_rows($vsmRes) > 0){
	while($vsmRow = imw_fetch_assoc($vsmRes)) {
		$vsmPtId 		= $vsmRow["patient_id"];
		$vsmDateVital 	= $vsmRow["date_vital"];
		$vsmRowArr[$vsmPtId][] = $vsmRow;
		$vsmDateRowArr[$vsmPtId][$vsmDateVital][] = $vsmRow;
	}
}



$radRowArr = array();
$radQry = "SELECT * FROM rad_test_data WHERE rad_status != 3 ".$andRadQry;
$radRes = imw_query($radQry) or die(imw_error().$radQry);
if(imw_num_rows($radRes) > 0){
	while($radRow = imw_fetch_assoc($radRes)) {
		$radPtId 	= $radRow["rad_patient_id"];
		$radRowArr[$radPtId][] = $radRow;
	}
}

$labRowArr = array();
$labQry = "SELECT lor.*,lore.id as result_id,ltd.lab_patient_id FROM lab_test_data ltd  
			LEFT JOIN lab_observation_requested lor ON lor.lab_test_id = ltd.lab_test_data_id 
			LEFT JOIN lab_observation_result lore ON lore.lab_test_id = ltd.lab_test_data_id
			WHERE ltd.lab_status !=3 ".$andLabQry;									
$labRes = imw_query($labQry) or die(imw_error().$labQry);
if(imw_num_rows($labRes) > 0){
	while($labRow = imw_fetch_assoc($labRes)) {
		$labPtId 	= $labRow["lab_patient_id"];
		$labRowArr[$labPtId][] = $labRow;
	}
}

$dprRowArr = $dprFormRowArr = array();
$dprQry = "SELECT dpr.name, doc.ccda_code, dpr.p_id, dpr.form_id  
			FROM document_patient_rel dpr 
			JOIN document doc ON dpr.doc_id = doc.id
			WHERE  1=1 ".$andDprQry;
$dprRes = imw_query($dprQry) or die(imw_error().$dprQry);
if(imw_num_rows($dprRes) > 0){
	while($dprRow = imw_fetch_assoc($dprRes)) {
		$dprPtId 	= $dprRow["p_id"];
		$dprFormId 	= $dprRow["form_id"];
		$dprRowArr[$dprPtId][] = $dprRow;
		$dprFormRowArr[$dprPtId][$dprFormId][] = $dprRow;
	}
}

$osacndRowArr = $osacndFormRowArr = array();
$osacndQry = "SELECT osacnd.inform ,od.name, od.snowmed, osacn.form_id, osacnd.order_set_associate_details_id, osacn.patient_id  
			FROM order_set_associate_chart_notes_details osacnd 
			JOIN order_set_associate_chart_notes osacn ON osacnd.order_set_associate_id = osacn.order_set_associate_id 
			JOIN order_details od ON od.id = osacnd.order_id	
			WHERE osacnd.delete_status = 0 AND osacn.delete_status = 0 ".$andOsacndQry;
$osacndRes = imw_query($osacndQry) or die(imw_error().$osacndQry);
if(imw_num_rows($osacndRes) > 0){
	while($osacndRow = imw_fetch_assoc($osacndRes)) {
		$osacndPtId 	= $osacndRow["patient_id"];
		$osacndFormId 	= $osacndRow["form_id"];
		$osacndRowArr[$osacndPtId][] = $osacndRow;
		$osacndFormRowArr[$osacndPtId][$osacndFormId][] = $osacndRow;
	}
}

$phsRowArr = $phsFormRowArr = array();
$phsQry = "SELECT * FROM patient_health_status WHERE del_status='0' ".$andPhsQry;
$phsRes = imw_query($phsQry) or die(imw_error().$phsQry);
if(imw_num_rows($phsRes) > 0){
	while($phsRow = imw_fetch_assoc($phsRes)) {
		$phsPtId 		= $phsRow["patient_id"];
		$phsFormId 		= $phsRow["form_id"];
		$phsStatusType 	= strtolower($phsRow["status_type"]);
		$phsRowArr[$phsPtId][$phsStatusType] = $phsRow;
		$phsFormRowArr[$phsPtId][$phsFormId][$phsStatusType] = $phsRow;
	}
}

$listRowArr = array();
$listQry = "SELECT * FROM lists WHERE type IN (5,6) AND allergy_status = 'Active' ".$andListQry;
$listRes = imw_query($listQry) or die(imw_error().$listQry);
if(imw_num_rows($listRes) > 0){
	while($listRow = imw_fetch_assoc($listRes)) {
		$listPtId 	= $listRow["pid"];
		$listRowArr[$listPtId][] = $listRow;
	}
}

$clchRowArr = array();
$clchQry = "SELECT * FROM chart_left_cc_history WHERE 1=1 ".$andClchQry;
$clchRes = imw_query($clchQry) or die(imw_error().$clchQry);
if(imw_num_rows($clchRes) > 0){
	while($clchRow = imw_fetch_assoc($clchRes)) {
		$clchPtId 				= $clchRow["patient_id"];
		$clchFormId 			= $clchRow["form_id"];
		$clchRowArr[$clchPtId] 	= $clchRow;
	}
}

$capRowArr = array();
$capQry = "SELECT * FROM chart_assessment_plans WHERE 1=1 ".$andCapQry." ORDER BY form_id ASC";
$capRes = imw_query($capQry) or die(imw_error().$capQry);
if(imw_num_rows($capRes) > 0){
	while($capRow = imw_fetch_assoc($capRes)) {
		$capPtId 				= $capRow["patient_id"];
		$capFormId 				= $capRow["form_id"];
		$capRowArr[$capPtId] 	= $capRow;
	}
}

$immunRowArr = $immunDateRowArr = array();
$immunQry = "SELECT * FROM immunizations immu WHERE 1=1 ".$andimmunQry;
$immunRes = imw_query($immunQry) or die(imw_error().$immunQry);
if(imw_num_rows($immunRes) > 0){
	while($immunRow = imw_fetch_assoc($immunRes)) {
		$immunPtId 		= $immunRow["patient_id"];
		$immunAdmDate 	= $immunRow["administered_date"];
		$immunRowArr[$immunPtId][] = $immunRow;
		$immunDateRowArr[$immunPtId][$immunAdmDate][] = $immunRow;
	}
}

$sql = "SELECT * FROM chart_schedule_test_external WHERE patient_id = '".$pid."'";

$csteRowArr = array();
$csteQry = "SELECT * FROM chart_schedule_test_external ORDER BY id ".$andCsteQry;
$csteRes = imw_query($csteQry) or die(imw_error().$csteQry);
if(imw_num_rows($csteRes) > 0){
	while($csteRow = imw_fetch_assoc($csteRes)) {
		$cstePtId 					= $csteRow["patient_id"];
		$csteRowArr[$cstePtId][] 	= $csteRow;
		$csteRowDOSArr[$cstePtId][] = $csteRow["DOS"];
		
	}
}

//END

foreach($arrData as $key=>$arr){
	$pat_id = $arr['pat_id'];
	$form_id = $arr['form_id'];
	$ccd_type = 'ccd';
	if($form_id == "all")
	$form_id = "";
	$dos = $arr['dos'];
	
	$_REQUEST['pid'] = $pat_id;
	$_REQUEST['electronicDOSCCD'] = $form_id;

	if($_REQUEST['pid'] != ""){
		$pid = $_REQUEST['pid'];
		$form_id = $_REQUEST['electronicDOSCCD'];
		
		$dos = $cmtDOSArr[$form_id];
	}
	$currentDate = date("YmdHis");
	$rowPatient = $ptPatientDataRowArr[$pid];

	/* BEGIN PATIENT DATA */
	$XMLpatient_data = '<recordTarget>';
	$XMLpatient_data .= '<patientRole>';
	if($rowPatient['ss'] != "")
		$XMLpatient_data .= '<id extension="'.$rowPatient['ss'].'" root="2.16.840.1.113883.4.1"/>';
	else
		$XMLpatient_data .= '<id root="2.16.840.1.113883.4.6"/>';
	
	$XMLpatient_data .= '<id root="idoc_mrn" extension="'.$pid.'" />';
	
	$XMLpatient_data .= '<addr use="HP">';

	if($rowPatient['street'] != "")
		$XMLpatient_data .= '<streetAddressLine>'.$rowPatient['street'].'</streetAddressLine>';
	else
		$XMLpatient_data .= '<streetAddressLine nullFlavor="NI"/>';
	
	if($rowPatient['street2'] != "")
		$XMLpatient_data .= '<streetAddressLine>'.$rowPatient['street2'].'</streetAddressLine>';
	else
		$XMLpatient_data .= '<streetAddressLine nullFlavor="NI"/>';
	
	if($rowPatient['city'] != "")
		$XMLpatient_data .= '<city>'.$rowPatient['city'].'</city>';
	else
		$XMLpatient_data .= '<city nullFlavor="NI"/>';
	
	if($rowPatient['state'] != "")
		$XMLpatient_data .= '<state>'.$rowPatient['state'].'</state>';
	else
		$XMLpatient_data .= '<state nullFlavor="NI"/>';
	
	if($rowPatient['postal_code'] != "")
		$XMLpatient_data .= '<postalCode>'.$rowPatient['postal_code'].'</postalCode>';
	else
		$XMLpatient_data .= '<postalCode nullFlavor="NI"/>';
	
	$XMLpatient_data .= '<country>US</country>';
	$XMLpatient_data .= '</addr>';
		
	if($rowPatient['phone_home'] != "")
		$XMLpatient_data .= '<telecom value="tel:+1-'.core_phone_format($rowPatient['phone_home']).'" use="HP"/>';
	else
		$XMLpatient_data .= '<telecom nullFlavor="NI" use="HP"/>';
		
		if($rowPatient['phone_biz'] != "")
		$XMLpatient_data .= '<telecom value="tel:+1-'.core_phone_format($rowPatient['phone_biz']).'" use="WP"/>';

		if($rowPatient['phone_cell'] != "")
			$XMLpatient_data .= '<telecom value="tel:+1-'.core_phone_format($rowPatient['phone_cell']).'" use="MC"/>';

		if($rowPatient['email'] != "")
			$XMLpatient_data .= '<telecom use="HP" value="mailto:'.$rowPatient['email'].'"/>';
		
		$XMLpatient_data .= '<patient>';
		$XMLpatient_data .= '<name>';
		
		if($rowPatient['suffix'] != ""){
			$XMLpatient_data .= '<prefix>'.$rowPatient['title'].'</prefix>';
		}

		$XMLpatient_data .= '<given qualifier="CL">'.$rowPatient['fname'].'</given>';
		
		//Setting Sequence when call is from reports
		if(isset($set_sequence_ccda_report) && $set_sequence_ccda_report === true){
			if($rowPatient['mname']!=""){
			$XMLpatient_data .= '<given>'.$rowPatient['mname'].'</given>';
			}
			if($rowPatient['mname_br']!=""){
				$XMLpatient_data .= '<given qualifier="BR">'.$rowPatient['mname_br'].'</given>';
			}
		}else{
			if($rowPatient['mname_br']!=""){
				$XMLpatient_data .= '<given qualifier="BR">'.$rowPatient['mname_br'].'</given>';
			}
			if($rowPatient['mname']!=""){
				$XMLpatient_data .= '<given>'.$rowPatient['mname'].'</given>';
			}
		}
	
		$XMLpatient_data .= '<family>'.$rowPatient['lname'].'</family>';
			
		if($rowPatient['suffix'] != ""){
			$XMLpatient_data .= '<suffix>'.$rowPatient['suffix'].'</suffix>';
		}
	
	$XMLpatient_data .= '</name>';
	
	$arrGender = array();
	$arrGender = gender_srh(strtolower($rowPatient['sex']));
	if($arrGender['code']!="" && $arrGender['display_name']!=""){	
	$XMLpatient_data .= '<administrativeGenderCode code="'.$arrGender['code'].'" codeSystem="2.16.840.1.113883.5.1"
										displayName="'.$arrGender['display_name'].'" codeSystemName="AdministrativeGender"/>';
	}else{
	$XMLpatient_data .= '<administrativeGenderCode nullFlavor="NI"/>';	
	}
	$dob = str_replace("-","",$rowPatient['DOB']);
	if($dob != "00000000"){
	$XMLpatient_data .= '<birthTime value="'.$dob.'"/>';
	}else{
		$XMLpatient_data .= '<birthTime nullFlavor="NI"/>';
	}
	
	$arrMarried = array();
	$arrMarried = marr_status_srh(strtolower($rowPatient['status']));
	if($arrMarried['code']!="" && $arrMarried['display_name']!=""){
	$XMLpatient_data .= '<maritalStatusCode code="'.$arrMarried['code'].'" displayName="'.$arrMarried['display_name'].'"
								codeSystem="2.16.840.1.113883.5.2"
								codeSystemName="MaritalStatus"/>';
	}
	
	//PATIENT RACE, NEW LOGIC FOR RACE-EXTENION----
	$PT_race_heirarcy = get_race_heirarcy($rowPatient['race'],$rowPatient['race_code']);
	$PT_race_name_joined = '';
	if(count($PT_race_heirarcy)>0){
		for($i=0; $i < count($PT_race_heirarcy); $i++){
			if($i==0){
				if( empty($PT_race_heirarcy[$i]['cdc_code']) || $PT_race_heirarcy[$i]['cdc_code']==='ASKU')
				{
					$XMLpatient_data .= '<raceCode nullFlavor="ASKU"/>';
				}
				else
				{
					$XMLpatient_data .= '<raceCode code="'.$PT_race_heirarcy[$i]['cdc_code'].'" displayName="'.$rowPatient['race'].'" codeSystem="2.16.840.1.113883.6.238" codeSystemName="Race and Ethnicity - CDC"/>';
					$PT_race_name_joined = $PT_race_heirarcy[$i]['race_name'];
				}
			}else{
				$PT_race_name_joined .= ' '.$PT_race_heirarcy[$i]['race_name'];
				$XMLpatient_data .= '<sdtc:raceCode code="'.$PT_race_heirarcy[$i]['cdc_code'].'" displayName="'.$PT_race_name_joined.'" codeSystem="2.16.840.1.113883.6.238" codeSystemName="Race and Ethnicity - CDC"/>';

			}
		}
	}else{
		$XMLpatient_data .= '<raceCode nullFlavor="NI"/>';
	}		

	$arrEthnicity = array();
	$arrEthnicity = ethnicity_srh(strtolower($rowPatient['ethnicity']));
	if($arrEthnicity['code']!="" && $arrEthnicity['display_name']!=""){		
		if( strtolower($arrEthnicity['display_name']) == "unknown" ){		
			$XMLpatient_data .= '<ethnicGroupCode nullFlavor="UNK" displayName="Unknown" />';
		}
		else
		{
			$XMLpatient_data .= '<ethnicGroupCode code="'.$arrEthnicity['code'].'"
						displayName="'.$arrEthnicity['display_name'].'"
						codeSystem="2.16.840.1.113883.6.238"
						codeSystemName="Race and Ethnicity - CDC"/>
						';
		}
	}else{
		$XMLpatient_data .= '<ethnicGroupCode nullFlavor="NI"/>
		';
	}
	$arrLanguage = array();
	if(trim($rowPatient['lang_code'])==''){
		$arrLanguage = language_srh(strtolower($rowPatient['language']));
	}else{
		if(trim($rowPatient['lang_code'])=='eng') $rowPatient['lang_code']='en';
		$arrLanguage['display_name'] = trim($rowPatient['language']);
		$arrLanguage['code'] 		 = trim($rowPatient['lang_code']);
	}
	if($arrLanguage['code']!="" && $arrLanguage['display_name']!=""){					
	$XMLpatient_data .= '<languageCommunication>
							<languageCode code="'.$arrLanguage['code'].'"/>
							<modeCode code="ESP" displayName="Expressed spoken" codeSystem="2.16.840.1.113883.5.60" codeSystemName="LanguageAbilityMode"/>
							<preferenceInd value="true"/>
						</languageCommunication>
						';
	}
	$XMLpatient_data .= '</patient>';
	$XMLpatient_data .= '</patientRole>';
	$XMLpatient_data .= '</recordTarget>';
	/* END PATIENT DATA */

	/* BEGIN CARE TEAM MEMBERS */
	if(in_array('mu_data_set_care_team_members',$arrOptions) || in_array('provider_info',$arrOptions)){
		
		/*Get Providers IDS for the Chart note - $form_id*/
		$providerIds = array();
		$providersGroups = array();
		$providerIds = $cmtProvIdsArr[$form_id][$pid];
		
		if(trim($providerIds))
		{
			$providerIds = explode(',', $providerIds);
			
			$providerIds = array_map('convertToInt', $providerIds);
			$providerIds = array_filter($providerIds);
			
			$providerIds = array_unique($providerIds);
			
			if( is_array($providerIds) && count($providerIds) > 0 )
			{
				foreach($providerIds as $prvId) {
					$providersGroups[$prvId] = (int)$usrGrpArr[$usrId];
				}
			}
		}
		
			$providerID = $rowPatient['providerID'];
			
			$tempProviderID = false;
			$tempTechnicianID = false;
			
			if( count($providerIds) > 0)
			{
				foreach($providersGroups as $groupKey=>$groupVal)
				{
					if( $groupVal === 2 && $tempProviderID === false )
					{
						$tempProviderID = (int)$groupKey;
					}
					elseif( $groupVal === 5 && $tempTechnicianID === false )
					{
						$tempTechnicianID = (int)$groupKey;
					}
				}
				
				if( $tempProviderID !== false && $tempProviderID > 0)
				{
					$providerID = $tempProviderID;
				}
			}
			
			$XML_documentationof_data = '<documentationOf>';
			$XML_documentationof_data .= '<serviceEvent classCode="PCPR">';
			$XML_documentationof_data .= '<effectiveTime>
										<low value="'.$currentDate.'"/>
										<high value="'.$currentDate.'"/>
										</effectiveTime>';
										
			if(in_array('mu_data_set_care_team_members',$arrOptions) || in_array('provider_info',$arrOptions)){
				$row_provider = $usrRowArr[$providerID];
				
				if(count($row_provider) > 0){
					
					$XML_documentationof_data .= '<performer typeCode="PRF">
					<functionCode code="PCP" displayName="Primary Care Provider" codeSystem="2.16.840.1.113883.5.88" codeSystemName="participationFunction">
						<originalText>Primary Care Provider</originalText>
					</functionCode>
					<assignedEntity>
					<!-- NPI 12345 -->
					';
					if($row_provider['user_npi'] != ""){
						$XML_documentationof_data .= '<id extension="'.$row_provider['user_npi'].'" root="2.16.840.1.113883.4.6"/>
					';
					}else{
						$XML_documentationof_data .= '<id nullFlavor="NI"/>
						';
					}
					
					if($row_provider['default_facility'] > 0){
						$row_facility = $facRowArr[$row_provider['default_facility']];
					}
					else{
						$row_facility = $facHqRowArr[0];
					}
					
					$PCP_represented_location = '';
					$XML_documentationof_data .= '<addr use="WP">
					';
					if($row_facility['street'] != ""){
						$PCP_represented_location .= '<streetAddressLine>'.$row_facility['street'].'</streetAddressLine>
						';
					}
					if($row_facility['city'] != ""){
						$PCP_represented_location .= '<city>'.$row_facility['city'].'</city>
						';
					}
					if($row_facility['state'] != ""){
						$PCP_represented_location .= '<state>'.$row_facility['state'].'</state>
						';
					}
					if($row_facility['postal_code'] != ""){
						$PCP_represented_location .= '<postalCode>'.$row_facility['postal_code'].'</postalCode>
						';
					}
					$PCP_represented_location .= '<country>US</country>';
					
					$XML_documentationof_data .= $PCP_represented_location.'
												</addr>';
					if($row_facility['phone'] != ""){
						$XML_documentationof_data .= '<telecom use="WP" value="tel:+1-'.core_phone_format($row_facility['phone']).'"/>
						';
						$PCP_represented_location = '<telecom use="WP" value="tel:+1-'.core_phone_format($row_facility['phone']).'"/><addr>'.$PCP_represented_location.'</addr>';
					}else{
						$XML_documentationof_data .= '<telecom nullFlavor="NI"/>
						';
						$PCP_represented_location = '<telecom nullFlavor="NI"/><addr>'.$PCP_represented_location.'</addr>';
					}
					$XML_documentationof_data .= '<assignedPerson>
													<name>
														<given>'.$row_provider['fname'].'</given>
														<family>'.$row_provider['lname'].'</family>
													</name>
													</assignedPerson>
													<representedOrganization>
													<id nullFlavor="NI"/>
													<name>'.$row_facility['name'].'</name>
													'.$PCP_represented_location.'
													</representedOrganization>';
					
					$XML_documentationof_data .= '</assignedEntity>';
					$XML_documentationof_data .= '</performer>';
				}
				else{
					$XML_documentationof_data .= '<performer typeCode="PRF">';
					$XML_documentationof_data .= '<functionCode code="PCP" displayName="Provider Care Provider" codeSystem="2.16.840.1.113883.5.88" codeSystemName="participationFunction"/>';
					$XML_documentationof_data .= '<assignedEntity>';
					$XML_documentationof_data .= '<!-- NPI 12345 -->';
					$XML_documentationof_data .= '<id nullFlavor="NI"/>';
					$XML_documentationof_data .= '<addr>';
					$XML_documentationof_data .= '<streetAddressLine nullFlavor="NI"/>';
					$XML_documentationof_data .= '<city nullFlavor="NI"/>';
					$XML_documentationof_data .= '<state nullFlavor="NI"/>';
					$XML_documentationof_data .= '<postalCode nullFlavor="NI"/>';
					$XML_documentationof_data .= '<country nullFlavor="NI"/>';
					$XML_documentationof_data .= '</addr>';
					$XML_documentationof_data .= '<telecom nullFlavor="NI"/>';								
					$XML_documentationof_data .= '<assignedPerson>';
					$XML_documentationof_data .= '<name>';
					$XML_documentationof_data .= '<given nullFlavor="NI"/>';
					$XML_documentationof_data .= '<family nullFlavor="NI"/>';
					$XML_documentationof_data .= '</name>';
					$XML_documentationof_data .= '</assignedPerson>';
					$XML_documentationof_data .= '</assignedEntity>';
					$XML_documentationof_data .= '</performer>';
				}
			}
			if(in_array('mu_data_set_care_team_members',$arrOptions)){
				$row_reff = $refRowArr[$rowPatient['primary_care_id']];
				
				if(count($row_reff) > 0){
					
					$XML_documentationof_data .= '<performer typeCode="PRF">
													<time><low nullFlavor="UNK"/></time>
												<assignedEntity>
												<!-- NPI 12345 -->
												';
					if($row_reff['NPI'] != "")
						$XML_documentationof_data .= '<id extension="'.$row_reff['NPI'].'" root="2.16.840.1.113883.4.6"/>';
					else
						$XML_documentationof_data .= '<id extension="'.$row_reff['physician_Reffer_id'].'" root="1.3.6.1.4.1.22812.4.99930.4"/>';
					
					if($row_reff['default_facility'] > 0){
						$row_facility = $facRowArr[$row_reff['default_facility']];
					}
					else{
						$row_facility = $facHqRowArr[0];
					}
					
					$XML_documentationof_data .= '<addr use="WP">';
					
					if($row_facility['street'] != "")
						$XML_documentationof_data .= '<streetAddressLine>'.$row_facility['street'].'</streetAddressLine>
						';
			
					if($row_facility['city'] != "")
						$XML_documentationof_data .= '<city>'.$row_facility['city'].'</city>
						';
				
					if($row_facility['state'] != "")
						$XML_documentationof_data .= '<state>'.$row_facility['state'].'</state>
						';
					
					if($row_facility['postal_code'] != "")
						$XML_documentationof_data .= '<postalCode>'.$row_facility['postal_code'].'</postalCode>
						';
				
					$XML_documentationof_data .= '<country>US</country>
					';
					$XML_documentationof_data .= '</addr>
					';
					
					if($row_facility['phone'] != ""){
						$XML_documentationof_data .= '<telecom use="WP" value="tel:+1-'.core_phone_format($row_facility['phone']).'"/>
						';
					}else{
						$XML_documentationof_data .= '<telecom nullFlavor="NI"/>';
					}
					
					$XML_documentationof_data .= '<assignedPerson>';
					$XML_documentationof_data .= '<name>';
					
					if($row_reff['Title']!="")
						$XML_documentationof_data .= '<prefix>'.$row_reff['Title'].'</prefix>';
			
					$XML_documentationof_data .= '<given>'.$row_reff['FirstName'].'</given>';
					$XML_documentationof_data .= '<family>'.$row_reff['LastName'].'</family>';
					$XML_documentationof_data .= '</name>';
					
					$XML_documentationof_data .= '</assignedPerson>';
					$XML_documentationof_data .= '</assignedEntity>';
					$XML_documentationof_data .= '</performer>';
				}
				
				$row_reff = $refRowArr[$rowPatient['primary_care_phy_id']];
				
				if(count($row_reff) > 0){
					
					$XML_documentationof_data .= '<performer typeCode="PRF">';
					$XML_documentationof_data .= '<functionCode code="PCP" displayName="Primary Care Physician" codeSystem="2.16.840.1.113883.5.88" 	  																	
													codeSystemName="participationFunction"/>';
					$XML_documentationof_data .= '<assignedEntity>';
					$XML_documentationof_data .= '<!-- NPI 12345 -->';
					if($row_reff['NPI'] != "")
					$XML_documentationof_data .= '<id extension="'.$row_reff['NPI'].'" root="2.16.840.1.113883.4.6"/>';
					else
					$XML_documentationof_data .= '<id nullFlavor="NI"/>';
					
					if($row_reff['default_facility'] > 0){
						$row_facility = $facRowArr[$row_reff['default_facility']];
					}
					else{
						$row_facility = $facHqRowArr[0];
					}
					
					$XML_documentationof_data .= '<addr use="WP">';
					
					if($row_facility['street'] != "")
						$XML_documentationof_data .= '<streetAddressLine>'.$row_facility['street'].'</streetAddressLine>';

					if($row_facility['city'] != "")
						$XML_documentationof_data .= '<city>'.$row_facility['city'].'</city>';
			
					if($row_facility['state'] != "")
						$XML_documentationof_data .= '<state>'.$row_facility['state'].'</state>';
				
					if($row_facility['postal_code'] != "")
						$XML_documentationof_data .= '<postalCode>'.$row_facility['postal_code'].'</postalCode>';
					
					$XML_documentationof_data .= '<country>US</country>';
					$XML_documentationof_data .= '</addr>';
					
					if($row_facility['phone'] != "")
						$XML_documentationof_data .= '<telecom use="WP" value="tel:+1-'.core_phone_format($row_facility['phone']).'"/>';
					$XML_documentationof_data .= '<assignedPerson>';
					$XML_documentationof_data .= '<name>';
					
					if($row_reff['Title']!="")
						$XML_documentationof_data .= '<prefix>'.$row_reff['Title'].'</prefix>';
					
					$XML_documentationof_data .= '<given>'.$row_reff['FirstName'].'</given>';
					$XML_documentationof_data .= '<family>'.$row_reff['LastName'].'</family>';
					$XML_documentationof_data .= '</name>';
					
					$XML_documentationof_data .= '</assignedPerson>';
			
					$XML_documentationof_data .= '</assignedEntity>';
					$XML_documentationof_data .= '</performer>';
				}
			}
			
			if(in_array('mu_data_set_care_team_members',$arrOptions)){
				
				$provIDTmp = (($tempTechnicianID!==false)?$tempTechnicianID:$rowPatient['assigned_nurse']);
				$row_provider = $usrRowArr[$provIDTmp];
				
				if(count($row_provider) > 0){

					$XML_documentationof_data .= '<performer typeCode="PRF">';
					$XML_documentationof_data .= '<functionCode code="NASST" displayName="nurse assistant" codeSystem="2.16.840.1.113883.5.88" 	  																	
													codeSystemName="participationFunction"/>';
					$XML_documentationof_data .= '<assignedEntity>';
					$XML_documentationof_data .= '<!-- NPI 12345 -->';
					if($row_provider['user_npi'] != "")
					$XML_documentationof_data .= '<id extension="'.$row_provider['user_npi'].'" root="2.16.840.1.113883.4.6"/>';
					else
					$XML_documentationof_data .= '<id nullFlavor="NI"/>';
					
					
					if($row_provider['facility'] > 0){
						$row_facility = $facRowArr[$row_provider['facility']];
					}
					else{
						$row_facility = $facHqRowArr[0];
					}
					
					$XML_documentationof_data .= '<addr use="WP">';
					if($row_facility['street'] != "")
					$XML_documentationof_data .= '<streetAddressLine>'.$row_facility['street'].'</streetAddressLine>';
					if($row_facility['city'] != "")
					$XML_documentationof_data .= '<city>'.$row_facility['city'].'</city>';
					if($row_facility['state'] != "")
					$XML_documentationof_data .= '<state>'.$row_facility['state'].'</state>';
					if($row_facility['postal_code'] != "")
					$XML_documentationof_data .= '<postalCode>'.$row_facility['postal_code'].'</postalCode>';
					$XML_documentationof_data .= '<country>US</country>';
					$XML_documentationof_data .= '</addr>';
					if($row_facility['phone'] != "")
					$XML_documentationof_data .= '<telecom use="WP" value="tel:+1-'.core_phone_format($row_facility['phone']).'"/>';
					$XML_documentationof_data .= '<assignedPerson>';
					$XML_documentationof_data .= '<name>';
					$XML_documentationof_data .= '<given>'.$row_provider['fname'].'</given>';
					$XML_documentationof_data .= '<family>'.$row_provider['lname'].'</family>';
					$XML_documentationof_data .= '</name>';
					$XML_documentationof_data .= '</assignedPerson>';
					$XML_documentationof_data .= '</assignedEntity>';
					$XML_documentationof_data .= '</performer>';
				}	
			}
			$XML_documentationof_data .= '</serviceEvent>';
			$XML_documentationof_data .= '</documentationOf>';
	}
	/* END CARE TEAM MEMBERS */

	/* BEGIN REFERRAL TO OTHER PROVIDETRS */
	if(in_array('provider_referrals',$arrOptions)){
		if($form_id!=""){
				$dos = $cmtDOSArr[$form_id];
				if(count($csteRowPtArr[$pid])>0) {
					foreach($csteRowPtArr[$pid] as $csteRowPt) {
						if($form_id != "" && strtotime($csteRowPt["schedule_date"]) >= strtotime($dos) && $csteRowPt["appoint_test"] == "Referral"){
							$row = 	$csteRowPt;
						}else if(strtotime($csteRowPt["schedule_date"]) >= strtotime(date('Y-m-d')) && $csteRowPt["appoint_test"] == "Referral") {
							$row = 	$csteRowPt;	
						}
					}
				}

			if($row['reff_phy'] !=""){
			$arr = explode(",",$row['reff_phy']);
			$arrFirst = explode(" ",trim($arr[0]));
			$arrSecond = explode(" ",trim($arr[1]));
			if(count($arrFirst)>1){
				$title = $arrFirst[0];
				$fname = $arrFirst[1];
			}else{
				$fname = $arrFirst[0];
			}
			
			if(count($arrSecond)>1){
				$lname = $arrSecond[0];
				$mname = $arrSecond[1];
			}else{
				$lname = $arrSecond[0];
			}
			$XML_referral_to_providers = '<componentOf>
											<encompassingEncounter>
												<id extension="'.$form_id.'" root="2.16.840.1.113883.4.6"/>
												<effectiveTime value="'.str_replace("-","",$row['schedule_date']).'" />
												<encounterParticipant typeCode="ATND">
													<assignedEntity>
														<id root="2.16.840.1.113883.4.6"/>
														<assignedPerson>
															<name>';
															if(isset($title) && $title!="")
																$XML_referral_to_providers .= '<prefix>'.$title.'</prefix>';
															if(isset($fname) && $fname!="")
																$XML_referral_to_providers .= '<given>'.$fname.'</given>';
															if(isset($lname) && $lname!="")
																$XML_referral_to_providers .= '<family>'.$lname.'</family>';
									
									$XML_referral_to_providers .='</name>
														</assignedPerson>
													</assignedEntity>
												</encounterParticipant>
											</encompassingEncounter>
										</componentOf>';
			}
		}
	}
	/* END REFERRAL TO OTHER PROVIDERS*/


	/* BEGIN AUTHOR DATA */
	$row_user = $usrRowArr[$_SESSION['authId']];
	$XML_author_data = '<author>';
	$XML_author_data .= '<time value="'.$currentDate.'"/>';
	$XML_author_data .= '<assignedAuthor>';
	if($row_user['user_npi'] != "")
	$XML_author_data .= '<id extension="'.$row_user['user_npi'].'" root="2.16.840.1.113883.4.6"/>';
	else
	$XML_author_data .= '<id root="2.16.840.1.113883.4.6"/>';
	
	if($row_user['facility'] > 0){
		$row_facility = $facRowArr[$row_user['facility']];
	}
	else{
		$row_facility = $facHqRowArr[0];
	}
	
	$XML_author_data .= '<addr use="WP">';
	if($row_facility['street'] != "")
	$XML_author_data .= '<streetAddressLine>'.$row_facility['street'].'</streetAddressLine>';
	if($row_facility['city'] != "")
	$XML_author_data .= '<city>'.$row_facility['city'].'</city>';
	if($row_facility['state'] != "")
	$XML_author_data .= '<state>'.$row_facility['state'].'</state>';
	if($row_facility['postal_code'] != "")
	$XML_author_data .= '<postalCode>'.$row_facility['postal_code'].'</postalCode>';
	$XML_author_data .= '<country>US</country>';
	$XML_author_data .= '</addr>';
	
	if($row_facility['phone'] != "")
	$XML_author_data .= '<telecom use="WP" value="tel:+1-'.core_phone_format($row_facility['phone']).'"/>';
	else
	$XML_author_data .= '<telecom nullFlavor="NI" use="WP"/>';
	
	$XML_author_data .= '<assignedPerson>';
	$XML_author_data .= '<name>';
	if($row_user['mname'] != "")
	$XML_author_data .= '<given>'.$row_user['mname'].'</given>';
	$XML_author_data .= '<given qualifier="CL">'.$row_user['fname'].'</given>';
	$XML_author_data .= '<family>'.$row_user['lname'].'</family>';
	$XML_author_data .= '</name>';
	
	$XML_author_data .= '</assignedPerson>';
	$XML_author_data .= '</assignedAuthor>';
	$XML_author_data .= '</author>';
	/* END AUTHOR DATA   */

	/* BEGIN DATA ENTERER DATA */
	$row_user = $usrRowArr[$_SESSION['authId']];
	
	$XML_data_enterer_data ='<dataEnterer>';
	$XML_data_enterer_data .='<assignedEntity>';
	if($row_user['user_npi'] != "")
	$XML_data_enterer_data .='<id root="2.16.840.1.113883.19.5" extension="'.$row_user['user_npi'].'"/>';
	else
	$XML_data_enterer_data .= '<id nullFlavor="NAV"/>';
	
	if($row_user['facility'] > 0){
		$row_facility = $facRowArr[$row_user['facility']];
	}
	else{
		$row_facility = $facHqRowArr[0];
	}
	
	$XML_data_enterer_data .= '<addr use="WP">';
	if($row_facility['street'] != "")
	$XML_data_enterer_data .= '<streetAddressLine>'.$row_facility['street'].'</streetAddressLine>';
	if($row_facility['city'] != "")
	$XML_data_enterer_data .= '<city>'.$row_facility['city'].'</city>';
	if($row_facility['state'] != "")
	$XML_data_enterer_data .= '<state>'.$row_facility['state'].'</state>';
	if($row_facility['postal_code'] != "")
	$XML_data_enterer_data .= '<postalCode>'.$row_facility['postal_code'].'</postalCode>';
	$XML_data_enterer_data .= '<country>US</country>';
	$XML_data_enterer_data .= '</addr>';
	if($row_facility['phone'] != "")
	$XML_data_enterer_data .= '<telecom use="WP" value="tel:+1-'.core_phone_format($row_facility['phone']).'"/>';
	else
	$XML_data_enterer_data .= '<telecom nullFlavor="NI" use="WP"/>';
	
	$XML_data_enterer_data .='<assignedPerson>';
	$XML_data_enterer_data .='<name>';
	$XML_data_enterer_data .='<given>'.$row_user['fname'].'</given>';
	$XML_data_enterer_data .='<family>'.$row_user['lname'].'</family>';
	$XML_data_enterer_data .='</name>';
	
	$XML_data_enterer_data .='</assignedPerson>';
	$XML_data_enterer_data .='</assignedEntity>';
	$XML_data_enterer_data .='</dataEnterer>';
	/* END AUTHOR DATA   */

	/* BEGIN CUSTODIAN (FACILITY) DATA */
	$facility = "";
	if(isset($form_id) && $form_id!=""){
		$row = $schRowArr[$form_id][$pid];
		
		$facility = $row['facility'];
		
	}
	if($facility == "" || $facility == "0"){
		
		$pos_facility = $rowPatient['default_facility'];
		$row = $facRowArr[$pos_facility];
		
		$facility = $row['facility'];
	}
	if($facility > 0){
		$row_facility = $facRowArr[$facility];
	}
	else{
		$row_facility = $facHqRowArr[0];
	}
	
	$XML_custodian_data = '<custodian>';
	$XML_custodian_data .= '<assignedCustodian>';
	$XML_custodian_data .= '<representedCustodianOrganization>';
	$XML_custodian_data .= '<id root="1.1.1.1.1.1.1.1.2"/>';
	if($row_facility['name'] != "")
	$XML_custodian_data .= '<name>'.htmlentities($row_facility['name']).'</name>';
	
	if($row_facility['phone'] != "")
	$XML_custodian_data .= '<telecom use="WP" value="tel:+1-'.core_phone_format($row_facility['phone']).'"/>';
	else
	$XML_custodian_data .= '<telecom nullFlavor="NI"/>';
	$XML_custodian_data .= '<addr>';
	if($row_facility['street'] != "")
	$XML_custodian_data .= '<streetAddressLine>'.$row_facility['street'].'</streetAddressLine>';
	if($row_facility['city'] != "")
	$XML_custodian_data .= '<city>'.$row_facility['city'].'</city>';
	if($row_facility['state'] != "")
	$XML_custodian_data .= '<state>'.$row_facility['state'].'</state>';
	if($row_facility['postal_code'] != "")
	$XML_custodian_data .= '<postalCode>'.$row_facility['postal_code'].'</postalCode>';
	$XML_custodian_data .= '<country>US</country>';
	$XML_custodian_data .= '</addr>';
	
	$XML_custodian_data .= '</representedCustodianOrganization>';
	$XML_custodian_data .= '</assignedCustodian>';
	$XML_custodian_data .= '</custodian>';
	/* END CUSTODIAN (FACILITY) DATA */

	/* BEGIN SOCIAL HISTORY SECTION */
	if(in_array('mu_data_set_smoking',$arrOptions)){
		
			$row_social = $smokeRowArr[$pid];
			$arrTmp = explode('/',$row_social['smoking_status']);
			$smoking_status = trim($arrTmp[1]);
			$pt_birth_sex_rs = getBirthSexInfo($pid);
			if($smoking_status){
				$smoking_modified_on 	= $row_social['smoking_modified_dt'] != '00000000' ? $row_social['smoking_modified_dt'] : '';
				$smoking_start_dt 		= $row_social['smoking_start_dt'] != '00000000' ? $row_social['smoking_start_dt'] : '';
				$smoking_end_dt 		= $row_social['smoking_end_dt'] != '00000000' ? $row_social['smoking_end_dt'] : '';
				
				$smoking_modified_on_show 	= $row_social['smoking_modified_dt'] != '00000000' ? $row_social['smoking_modified_dt_show'] : '';
				$smoking_start_dt_show 		= $row_social['smoking_start_dt'] != '00000000' ? $row_social['smoking_start_dt_show'] : '';
				$smoking_end_dt_show 		= $row_social['smoking_end_dt'] != '00000000' ? $row_social['smoking_end_dt_show'] : '';
			}
			else{
				$smoking_modified_on 	= '';
				$smoking_start_dt 		= '';
				$smoking_end_dt 		= '';
				
				$smoking_modified_on_show 	= '';
				$smoking_start_dt_show 		= '';
				$smoking_end_dt_show 		= '';
			}
			
			$arrSmoking = array();
			$arrSmoking = smoking_status_srh(strtolower($smoking_status));
			$XML_social_history_section = '<component>
											<section>
											<templateId root="2.16.840.1.113883.10.20.22.2.17" extension="2015-08-01"/>
											<templateId root="2.16.840.1.113883.10.20.22.2.17"/>
											<code code="29762-2" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="Social History"/>
											<title>SOCIAL HISTORY</title>
											<text>
											<table border = "1" width = "100%">
											<thead>
												<tr>
													<th>Social History Observation</th>
													<th>Description</th>
													<th>Dates Observed</th>
												</tr>
											</thead>
											';
			if($arrSmoking['code']!="" && $arrSmoking['display_name']!=""){						
			$XML_social_history_section .='
									<tbody>
									<tr>
										<td>Smoking Status</td>
										<td>'.$arrSmoking['display_name'].' (SNOMED-CT: '.$arrSmoking['code'].')</td>
										<td>';
			if($smoking_start_dt != '') $XML_social_history_section .= $smoking_start_dt_show;
			if($smoking_end_dt != '') $XML_social_history_section .= ' - '.$smoking_end_dt_show;
			if($smoking_start_dt == '' && $smoking_end_dt == '' && $smoking_modified_on != '') $XML_social_history_section .= $smoking_modified_on_show;
			$XML_social_history_section .='
										</td>
									</tr>';
			if($pt_birth_sex_rs){						
				$XML_social_history_section .='		
									<tr>
										<td ID="BirthSexInfo">Birth Sex</td>
										<td>'.$pt_birth_sex_rs['birth_sex'].'</td>
										<td>'.date('F d,Y',strtotime($pt_birth_sex_rs['birth_sex_date'])).'</td>
									</tr>';
			}						
			$XML_social_history_section .='
									</tbody>
								';
			}
			$XML_social_history_section .= '</table>
											</text>
											';
					
					$XML_smoking_status_entry .=	'<entry typeCode="DRIV">
														<observation classCode="OBS" moodCode="EVN">
															<!-- ** Smoking Status - Meaningful Use (V2) ** -->
															<templateId root="2.16.840.1.113883.10.20.22.4.78" extension="2014-06-09"/>
															<templateId root="2.16.840.1.113883.10.20.22.4.78"/>
															<id nullFlavor = "NI"/>
															<!-- code SHALL be 72166-2 for Smoking Status - Meaningful Use (V2) -db -->
															<code code="72166-2" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="Tobacco smoking status NHIS"/>
															<statusCode code="completed"/>
															<!-- The effectiveTime reflects when the current smoking status was observed. -->';
				if($smoking_start_dt != '' && $smoking_end_dt != ''){
					$XML_smoking_status_entry .= '<effectiveTime>
													<low value="'.$smoking_start_dt.'"/>
													<high value="'.$smoking_end_dt.'"/>
												</effectiveTime>
					';
				}else if($smoking_start_dt != '' && $smoking_end_dt == ''){
					$XML_smoking_status_entry .= '<effectiveTime value="'.$smoking_start_dt.'"/>
					';
				}else if($smoking_start_dt == '' && $smoking_end_dt == '' && $smoking_modified_on != ''){
					$XML_smoking_status_entry .= '<effectiveTime value="'.$smoking_modified_dt.'"/>
					';
				}else if($smoking_start_dt == '' && $smoking_end_dt == '' && $smoking_modified_on == ''){
					$XML_smoking_status_entry .= '<effectiveTime nullFlavor="NI"/>
					';
				}
				
				$XML_smoking_status_entry .= '<!-- The value represents the patient\'s smoking status currently observed. -->
															<!-- Consol Smoking Status Meaningful Use2 SHALL contain exactly one [1..1] value (CONF:1098-14810), which SHALL be selected from ValueSet Current Smoking Status 2.16.840.1.113883.11.20.9.38 STATIC 2014-09-01 (CONF:1098-14817) -db -->
															<value xsi:type="CD" code="'.$arrSmoking['code'].'" displayName="'.$arrSmoking['display_name'].'" codeSystem="2.16.840.1.113883.6.96"/>
														</observation>
												</entry>';
										
					/* END SMOKING STATUS ENTRY */
					
					/* Birth Sex Entry */
						$birth_sex_entry = '';
						
						if($pt_birth_sex_rs){
							
							$arrgender = gender();
							$pt_birth_status_date = date("Ymd",strtotime($pt_birth_sex_rs['birth_sex_date']));
							$gender_code = $arrgender[$pt_birth_sex_rs['birth_sex']];
							$display_name_pt_birth_sex = $pt_birth_sex_rs['birth_sex'];
							
							$birth_sex_entry .=	'
								<entry>
									<observation classCode="OBS" moodCode="EVN">
									<templateId root="2.16.840.1.113883.10.20.22.4.200" extension="2016-06-01"/>
									<code code="76689-9" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="Sex Assigned At Birth"/>
									<text>
										<reference value="#BirthSexInfo"/>
									</text>
									<statusCode code="completed"/>
									<effectiveTime value="'.$pt_birth_status_date.'"/>
									<value code="'.$gender_code.'" codeSystem="2.16.840.1.113883.5.1" xsi:type="CD" displayName="'.$display_name_pt_birth_sex.'"/>
									</observation>
								</entry>
								';
						}
						
						if(empty($birth_sex_entry) === false){
							$XML_smoking_status_entry .= $birth_sex_entry;
						}
					/* Birth Sex $End */
			$XML_social_history_section .= $XML_smoking_status_entry;
			$XML_social_history_section .= '</section>
										</component>
										';
	}
	/* END SOCIAL HISTORY SECTION */	

	/* BEGIN MEDICATIONS SECTION */
	if(in_array('mu_data_set_medications',$arrOptions)){
			$XML_medication_section = '<component>';
			$XML_medication_section .= '<section>';
			$XML_medication_section .= '<templateId root="2.16.840.1.113883.10.20.22.2.1.1" extension="2014-06-09"/>';
			$XML_medication_section .= '<templateId root="2.16.840.1.113883.10.20.22.2.1.1"/>';
			$XML_medication_section .= '<code code="10160-0" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="History of medications"/>';
			$XML_medication_section .= '<title>MEDICATIONS</title>';
			$XML_medication_section .= '<text>';
			$XML_medication_section .= '<table border = "1" width = "100%">';
			$XML_medication_section .= '<thead>
									<tr>
										<th>Medication</th>
										<th>Start Date</th>
										<th>End Date</th>
										<th>Route</th>
										<th>Dose</th>
										<th>Frequency</th>                                    
									</tr>
								</thead>';
			
			$arrType = array("1","4");
			$arrMedication = get_medical_data($form_id,$arrType ,$pid);
			$flag = 0;
			if(count($arrMedication)>0){
				$XML_medication_section .= ' <tbody>';
				
				foreach($arrMedication as $medication){	
					if($medication['ccda_code'] == ""){
						$arrCCDA = getRXNormCode($medication['title']);
						$ccda_code = $arrCCDA['ccda_code'];
					}else{
						$ccda_code = $medication['ccda_code'];
					}
					if(!in_array($medication['title'],$arrMedHxMedi)){
						$flag = 1;	
						$XML_medication_section .= '<tr>
													<td>
														<content ID = "Med'.$medication['id'].'">'.htmlentities($medication['title']).'    [RxNorm: '.$ccda_code.']</content>
													</td>
													<td>';
						$XML_medication_section .= ($medication['begdate']!="" && $medication['begdate']!='0000-00-00')?date('M d,Y',strtotime($medication['begdate'])):"";
						$XML_medication_section .='</td>
														<td>';
						$XML_medication_section .=($medication['enddate']!="" && $medication['enddate']!='0000-00-00')?date('M d,Y',strtotime($medication['enddate'])):"";
						$XML_medication_section .='</td>
														<td ID = "MEDROUTE'.$medication['id'].'">'.$medication['med_route'].'</td>
														<td ID = "MEDFORM'.$medication['id'].'">'.$medication['destination'].'</td>
														<td ID = "Instruct'.$medication['id'].'">'.htmlentities($medication['sig']).'</td>
													</tr>
												';	
					}
				}
				
				$XML_medication_section .= ' </tbody>';
			}
			if($flag == 0)
			{
				$XML_medication_section .= ' <tbody><tr><td colspan="7">No known Medications</td></tr></tbody>';
			}
			
			$XML_medication_section .= '</table>';
			$XML_medication_section .= '</text>';
					$arrType = array("1","4");
					$arrMedication = get_medical_data($form_id,$arrType ,$pid);
					$flag = 0;
					if(count($arrMedication)>0){
					foreach($arrMedication as $medication){
						
						if(!in_array($medication['title'],$arrMedHxMedi)){
							$flag = 1;
							
						if($medication['ccda_code']!=""){
							$arrCCDA = getRXNorm_by_code($medication['ccda_code']);
							
							$ccda_code_med = $medication['ccda_code'];
							$ccda_display_name_med = $medication['title'];
						}else{
							$arrCCDA = getRXNormCode($medication['title']);
							if(count($arrCCDA)>0){
							$ccda_code_med = $arrCCDA['ccda_code'];
							$ccda_display_name_med = $arrCCDA['ccda_display_name'];
							}
							
						}
						
					/*  BEGIN MEDICATION ENTRY  */
						$XML_medication_activity_entry = '<entry typeCode="DRIV">';
						$XML_medication_activity_entry .= '<substanceAdministration classCode="SBADM" moodCode="EVN">';
						$XML_medication_activity_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.16" extension="2014-06-09"/>';
						$XML_medication_activity_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.16"/>';
						$XML_medication_activity_entry .= '<id nullFlavor="NI"/>';
						$XML_medication_activity_entry .= '<text>';
						
						$XML_medication_activity_entry .= '<reference value="#Med'.$medication['id'].'"/>'.htmlentities($medication['title']).'    [RxNorm: '.$ccda_code_med.']';
						$XML_medication_activity_entry .= '</text>';
						$XML_medication_activity_entry .= '<statusCode code="completed"/>';
						$XML_medication_activity_entry .= '<effectiveTime xsi:type="IVL_TS">';
						if($medication['begdate'] !="" && $medication['begdate']!="0000-00-00")
						$XML_medication_activity_entry .= '<low value="'.str_replace("-","",$medication['begdate']).'"/>';
						else 
						$XML_medication_activity_entry .= '<low nullFlavor="NI"/>';
						if($medication['enddate'] !="" && $medication['enddate']!="0000-00-00")
						$XML_medication_activity_entry .= '<high value="'.str_replace("-","",$medication['enddate']).'"/>';
						else 
						$XML_medication_activity_entry .= '<high nullFlavor="NI"/>';
						$XML_medication_activity_entry .= '</effectiveTime>';
						
						$medDosage = trim($medication['destination']);
						$arrMedDosage = preg_split("/(?<=\d)(?=[a-zA-Z])|(?<=[a-zA-Z])(?=\d)/",preg_replace('/\s/','',$medDosage));
						
						$medDosageVal = $arrMedDosage[0];
						$medDosageUnit = $arrMedDosage[1];
						/* DYNAMIC ROUTE Medication Route FDA Value Set :: Code System(s): National Cancer Institute (NCI) Thesaurus*/
						if($medication['sites'] == 1 || $medication['sites'] == 2 || $medication['sites'] == 3 ){
							$XML_medication_activity_entry .= '<routeCode code="C38287" codeSystem="2.16.840.1.113883.3.26.1.1"
															codeSystemName="National Cancer Institute (NCI) Thesaurus"
															displayName="OPHTHALMIC"/>';
						}else if($medication['sites'] == 4){
							$XML_medication_activity_entry .= '<routeCode code="C38288" codeSystem="2.16.840.1.113883.3.26.1.1"
															codeSystemName="National Cancer Institute (NCI) Thesaurus"
															displayName="ORAL"/>';
						}else{
							$routeCode 		= get_med_route_val_code($medication['med_route'],'code');
							$XML_medication_activity_entry .= '<routeCode code="'.$routeCode.'" codeSystem="2.16.840.1.113883.3.26.1.1"
									codeSystemName="National Cancer Institute (NCI) Thesaurus"
									displayName="'.$medication['med_route'].'"/>';
							
						}
						
						/* DYNAMIC Approach Site CODE :: Body Site Value Set:: Code System(s): SNOMED CT 2.16.840.1.113883.6.96*/
						if($medication['sites'] == 1){ // LEFT EYE OS
						$XML_medication_activity_entry .= '<approachSiteCode code="362503005" codeSystem="2.16.840.1.113883.6.96"
															codeSystemName="SNOMED CT"
															displayName="Entire left eye (body structure)"/>';
						}else if($medication['sites'] == 2){ // RIGHT EYE OD
						$XML_medication_activity_entry .= '<approachSiteCode code="362502000" codeSystem="2.16.840.1.113883.6.96"
															codeSystemName="SNOMED CT"
															displayName="Entire right eye (body structure)"/>';
						}
						else if($medication['sites'] == 3){ // BOTH EYES OU
						$XML_medication_activity_entry .= '<approachSiteCode code="244486005" codeSystem="2.16.840.1.113883.6.96"
															codeSystemName="SNOMED CT"
															displayName="Entire eye (body structure)"/>';
						}
						else if($medication['sites'] == 4){ // ORAL PO
						$XML_medication_activity_entry .= '<approachSiteCode code="26643006" codeSystem="2.16.840.1.113883.6.96"
															codeSystemName="SNOMED CT"
															displayName="taking by mouth"/>';
						}
						if($medDosageVal > 0 && $medDosageUnit != "")
						$XML_medication_activity_entry .= '<doseQuantity value="'.trim($medDosageVal).'"/>';
						else
						$XML_medication_activity_entry .= '<doseQuantity nullFlavor="NI"/>';
						
						$XML_medication_activity_entry .= '<consumable>';
						$XML_medication_activity_entry .= '<manufacturedProduct classCode="MANU">';
						$XML_medication_activity_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.23" extension="2014-06-09"/>';
						$XML_medication_activity_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.23"/>';
						$XML_medication_activity_entry .= '<manufacturedMaterial>';
														/* DYNAMIC MEDICATION CODE FROM RXNORM Medication Clinical Drug  */
						
							$XML_medication_activity_entry .= '<code code="'.$ccda_code_med.'"
															codeSystem="2.16.840.1.113883.6.88"
															codeSystemName="RxNorm"
															displayName="'.$ccda_display_name_med.'">';
						
						$XML_medication_activity_entry .= '</code>';
						$XML_medication_activity_entry .= '</manufacturedMaterial>';
						$XML_medication_activity_entry .= '</manufacturedProduct>';
						$XML_medication_activity_entry .= '</consumable>';
						
							$XML_medication_activity_entry .= '</substanceAdministration>';
							$XML_medication_activity_entry .= '</entry>';
							$XML_medication_section .= $XML_medication_activity_entry;
						}
					
					}
					}
						
					if($flag == 0){
					/*  BEGIN MEDICATION ENTRY  */
						$XML_medication_activity_entry .= '
						<entry>
							<substanceAdministration moodCode="EVN" classCode="SBADM" negationInd="true">
								<!-- ** Medication Activity (V2) ** -->
								<templateId root="2.16.840.1.113883.10.20.22.4.16" extension="2014-06-09"/>
								<templateId root="2.16.840.1.113883.10.20.22.4.16"/>
								<id nullFlavor="NI"/>
								<statusCode code="completed"/>
								<effectiveTime nullFlavor="NA"/>
								<doseQuantity nullFlavor="NA"/>
								<consumable>
									<manufacturedProduct classCode="MANU">
										<templateId root="2.16.840.1.113883.10.20.22.4.23" extension="2014-06-09"/>
										<templateId root="2.16.840.1.113883.10.20.22.4.23"/>
										<manufacturedMaterial>
											<code nullFlavor="OTH" codeSystem="2.16.840.1.113883.6.88"> 
												<translation code="410942007" displayName="drug or medication"
													codeSystem="2.16.840.1.113883.6.96"            
													codeSystemName="SNOMED CT"/>
											</code>
										</manufacturedMaterial>
									</manufacturedProduct>
								</consumable>
							</substanceAdministration>
						</entry>
						';
						
						
						
						$XML_medication_section .= $XML_medication_activity_entry;
					
					}
					/*  END MEDICATION ENTRY*/
			$XML_medication_section .= '</section>';
			$XML_medication_section .= '</component>';
	}
	/* END MEDICATIONS SECTION */

	/* BEGIN ALLERGIES SECTION */
	if(in_array('mu_data_set_allergies',$arrOptions)){
			$XML_allergies_section = '<component>';
			$XML_allergies_section .= '<section>';
			$XML_allergies_section .= '<templateId root="2.16.840.1.113883.10.20.22.2.6.1" extension="2015-08-01"/>';
			$XML_allergies_section .= '<templateId root="2.16.840.1.113883.10.20.22.2.6.1"/>';
			$XML_allergies_section .= '<code code="48765-2" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="History of allergies"/>';
			$XML_allergies_section .= '<title>ALLERGIES &amp; REACTIONS</title>';
			$XML_allergies_section .= '<text>';
			$XML_allergies_section .= '<table border = "1" width = "100%">';
			$XML_allergies_section .= '<thead>
										<tr>
											<th>Type</th>
											<th>Substance</th>
											<th>Begin Date</th>
											<th>Reactions</th>
											<th>Severity</th>
											<th>Status</th>
										</tr>
									</thead>';
			$XML_allergies_section .= '<tbody>';
			$arrType = array("3","7");
			$arrAllergies = get_medical_data($form_id, $arrType,$pid);
			$flag = 0;
			if(count($arrAllergies)>0){
			foreach($arrAllergies as $allergy){		
				if(!in_array($allergy['title'],$arrMedHxAller)){
					
				$arrAllerType = allergy_type_srh($allergy['ag_occular_drug']);
				$strAllerType = '';
				if(count($arrAllerType)>0)
				$strAllerType = '  - '.$arrAllerType['display_name'];
				$flag = 1;
				$XML_allergies_section .= '<tr ID = "ALGSUMMARY_'.$allergy['id'].'">
										<td ID = "ALGTYPE_'.$allergy['id'].'">'.$arrAllerType['display_name'].'</td>
										<td ID = "ALGSUB_'.$allergy['id'].'">'.htmlentities($allergy['title']).'</td>
										<td ID = "ALGBEGIN_'.$allergy['id'].'">
										';
				$XML_allergies_section .=(preg_replace("/-/",'',$allergy['begdate'])>0)?date('M d,Y',strtotime($allergy['begdate'])):"";
				$XML_allergies_section .='</td>
										<td ID = "ALGREACT_'.$allergy['id'].'">'.htmlentities($allergy['comments']).'</td>
										<td ID = "ALGRESEV_'.$allergy['id'].'">'.htmlentities(ucwords(strtolower($allergy['severity']))).'</td>
										<td ID = "ALGSTATUS_'.$allergy['id'].'">'.$allergy['allergy_status'].'</td>
									</tr>';
				}
			}
			}
			if($flag == 0)
			{
				$XML_allergies_section .= ' <tr><td colspan="5">No allergy data.</td></tr>';
			}

			$row = $cnmhRowArr[$pid];
			$negationInd = '';
			if($row['no_value'] == "NoAllergies"){
				$XML_allergies_section .= ' <tr><td colspan="5">No Known Drug Allergy (NKDA)</td></tr>';
			}
			
			$XML_allergies_section .= '</tbody>';
			$XML_allergies_section .= '</table>';
			$XML_allergies_section .= '</text>';
					/* BEGIN ALLERGIES PROBLEM ACT */
					$arrType = array("3","7");
					$arrAllergies = get_medical_data($form_id, $arrType,$pid);
					$flag = 0;
					if(count($arrAllergies)>0){
					foreach($arrAllergies as $allergy){
						
						if(!in_array($allergy['title'],$arrMedHxAller)){
						$flag = 1;	
						$XML_allergies_problem_act = '<entry typeCode="DRIV">';
						$XML_allergies_problem_act .= '<act classCode="ACT" moodCode="EVN">';
						$XML_allergies_problem_act .= '<templateId root="2.16.840.1.113883.10.20.22.4.30" extension="2015-08-01"/>';
						$XML_allergies_problem_act .= '<templateId root="2.16.840.1.113883.10.20.22.4.30"/>';
						$XML_allergies_problem_act .= '<id nullFlavor="NI"/>';
						$XML_allergies_problem_act .= '<!-- Allergy Problem Act template -->';
						
						//NEW LINE BELOW FOR 2.1 IMPLEMENTATION
						$XML_allergies_problem_act .= '<code code="CONC" codeSystem="2.16.840.1.113883.5.6"/>';
						$XML_allergies_problem_act .= '<statusCode code="active"/>';
						$XML_allergies_problem_act .= '<effectiveTime>';
						if($allergy['begdate']!= ""){
						$XML_allergies_problem_act .= '<low value="'.str_replace('-','',$allergy['begdate']).'"/>';
						}else{
						$XML_allergies_problem_act .= '<low nullFlavor="NI"/>';	
						}
						$XML_allergies_problem_act .= '</effectiveTime>';
						$XML_allergies_problem_act .= '<entryRelationship typeCode="SUBJ">';
						
						$XML_allergies_problem_act .= '<observation classCode="OBS" moodCode="EVN">';
						$XML_allergies_problem_act .= '<templateId root="2.16.840.1.113883.10.20.22.4.7" extension="2014-06-09"/>';
						$XML_allergies_problem_act .= '<templateId root="2.16.840.1.113883.10.20.22.4.7"/>';
						$XML_allergies_problem_act .= '<id nullFlavor="NI"/>';
						$XML_allergies_problem_act .= '<!-- Allergy - intolerance observation template -->';
						$XML_allergies_problem_act .= '<code code="ASSERTION" codeSystem="2.16.840.1.113883.5.4"/>';
						$XML_allergies_problem_act .= '<statusCode code="completed"/>';
						if($allergy['begdate']!= ""){
						$XML_allergies_problem_act .= '<effectiveTime>';
						$XML_allergies_problem_act .= '<low value="'.str_replace('-','',$allergy['begdate']).'"/>';
						$XML_allergies_problem_act .= '</effectiveTime>';
						}else{
						$XML_allergies_problem_act .= '<effectiveTime>';
						$XML_allergies_problem_act .= '<low nullFlavor="NI"/>';
						$XML_allergies_problem_act .= '</effectiveTime>';
						}
						$arrAllerType = allergy_type_srh($allergy['ag_occular_drug']);
						if($arrAllerType['code'] != "" && $arrAllerType['display_name'] != ""){				
						$XML_allergies_problem_act .= '<value xsi:type="CD" code="'.$arrAllerType['code'].'"
														displayName="'.$arrAllerType['display_name'].'"
														codeSystem="2.16.840.1.113883.6.96"
														codeSystemName="SNOMED CT">';
						$XML_allergies_problem_act .= '<originalText>';
						$XML_allergies_problem_act .= '<reference value="#ALGSUB_'.$allergy['id'].'"/>';
						$XML_allergies_problem_act .= htmlentities($allergy['title']);
						$XML_allergies_problem_act .= '</originalText>';
						$XML_allergies_problem_act .= '</value>';
						}
						$XML_allergies_problem_act .= '<participant typeCode="CSM">';
						$XML_allergies_problem_act .= '<participantRole classCode="MANU">';
						$XML_allergies_problem_act .= '<playingEntity classCode="MMAT">';
						/* */
						if($allergy['ag_occular_drug'] == "fdbATIngredient" || $allergy['ag_occular_drug'] == "fdbATAllergenGroup"){ // Food Allergy
														
							if($allergy['ccda_code'] != "")	{
							$ccda_code_aller = $allergy['ccda_code'];
							$ccda_display_name_aller = $allergy['title'];
							}
							else{
							/* DYNAMIC CODE FROM Ingredient Name Value Set (Unique Ingredient Identifier (UNII) Code System)*/		
							$ccda_code_aller = $allergy['ccda_code'];
							$ccda_display_name_aller = $allergy['title'];	
							}
							$XML_allergies_problem_act .= '<code code="'.$ccda_code_aller.'" displayName="'.htmlentities($ccda_display_name_aller).'"
															codeSystem="2.16.840.1.113883.4.9" codeSystemName="UNII">';
							$XML_allergies_problem_act .= '<originalText>';
							$XML_allergies_problem_act .= '<reference value="#ALGSUB_'.$allergy['id'].'"/>';
							$XML_allergies_problem_act .= '</originalText>';
							$XML_allergies_problem_act .= '</code>';
						}
						else if($allergy['ag_occular_drug'] == "fdbATDrugName"){ // Drug Allergy
							if($allergy['ccda_code'] != "")	{
								$ccda_code_aller = $allergy['ccda_code'];
								$ccda_display_name_aller = $allergy['title'];
							}
							else{
								/* DYNAMIC CODE FROM Medication Clinical Drug Value Set (RxNorm Code System)*/
								$arrCCDA = getRXNormCode($allergy['title']);
								if(count($arrCCDA)>0){
								$ccda_code_aller = $arrCCDA['ccda_code'];
								$ccda_display_name_aller = $arrCCDA['ccda_display_name'];
								}	
							}
							
							$XML_allergies_problem_act .= '<code code="'.$ccda_code_aller.'" displayName="'.htmlentities($ccda_display_name_aller).'"
															codeSystem="2.16.840.1.113883.6.88" codeSystemName="RxNorm">';
							
							$XML_allergies_problem_act .= '<originalText>';
							$XML_allergies_problem_act .= '<reference value="#ALGSUB_'.$allergy['id'].'"/>';
							$XML_allergies_problem_act .= '</originalText>';
							$XML_allergies_problem_act .= '</code>';
						}
						
						$XML_allergies_problem_act .= '</playingEntity>';
						$XML_allergies_problem_act .= '</participantRole>';
						$XML_allergies_problem_act .= '</participant>';
						$XML_allergies_problem_act .= '<entryRelationship typeCode = "SUBJ" inversionInd = "true">
											<observation classCode = "OBS" moodCode = "EVN">
												<templateId root = "2.16.840.1.113883.10.20.22.4.28" extension="2014-06-09"/>
												<templateId root = "2.16.840.1.113883.10.20.22.4.28"/>
												<code code = "33999-4" codeSystem = "2.16.840.1.113883.6.1" codeSystemName = "LOINC" displayName = "Status"/>
												<statusCode code = "completed"/>
												<value xsi:type = "CE" code = "55561003" codeSystem = "2.16.840.1.113883.6.96" codeSystemName="SNOMED-CT" displayName = "'.$allergy['allergy_status'].'"/>
											</observation>
										</entryRelationship>';
					$XML_allergies_problem_act .= '<entryRelationship typeCode = "MFST" inversionInd = "true">
											<observation classCode = "OBS" moodCode = "EVN">
												<templateId root = "2.16.840.1.113883.10.20.22.4.9" extension="2014-06-09"/>
												<templateId root = "2.16.840.1.113883.10.20.22.4.9"/>
												<id nullFlavor="NI"/>
												<code code = "ASSERTION" codeSystem = "2.16.840.1.113883.5.4"/>
												<text>
													<reference value = "#ALGREACT_'.$allergy['id'].'"/>'.
													htmlentities($allergy['comments'])
													.'
												</text>
												<statusCode code = "completed"/>';
						$arrAllReaction = getProblemCode($allergy['comments']);
										// DYNAMIC REACTION CODE //
						if($arrAllReaction['ccda_code']!="" && $arrAllReaction['ccda_display_name']){					
							$XML_allergies_problem_act .= '<value xsi:type="CD"
													code="'.$arrAllReaction['ccda_code'].'"
													codeSystem="2.16.840.1.113883.6.96"
													codeSystemName="SNOMED CT"
													displayName="'.$arrAllReaction['ccda_display_name'].'"/>';
						}else if(trim($allergy['comments'])!="" && trim($allergy['reaction_code'])!=''){					
							$XML_allergies_problem_act .= '<value xsi:type="CD"
													code="'.trim($allergy['reaction_code']).'"
													codeSystem="2.16.840.1.113883.6.96"
													codeSystemName="SNOMED CT"
													displayName="'.trim($allergy['comments']).'"/>';
						}else{
							$XML_allergies_problem_act .= '<value xsi:type="CD" nullFlavor="NI"/>';
						} 
						//----if severity available--new code below -----
						if(trim($allergy['severity'])!=''){
							$XML_allergies_problem_act .= '
							<entryRelationship typeCode="SUBJ" inversionInd="true">
								<observation classCode="OBS" moodCode="EVN">
									<!--Severity Observation (V2)-->
									<templateId root="2.16.840.1.113883.10.20.22.4.8" extension="2014-06-09"/>
									<templateId root="2.16.840.1.113883.10.20.22.4.8"/>
									<code code="SEV" displayName="Severity Observation" codeSystem="2.16.840.1.113883.5.4" codeSystemName="ActCode"/>
									<text>
										<reference value="#ALGRESEV_'.$allergy['id'].'"/>
									</text>
									<statusCode code="completed"/>';
							if(strtolower(trim($allergy['severity']))=='fatal')					$severity_value_code = '399166001';
							else if(strtolower(trim($allergy['severity']))=='mild') 			$severity_value_code = '255604002';
							else if(strtolower(trim($allergy['severity']))=='mild to moderate')	$severity_value_code = '371923003';
							else if(strtolower(trim($allergy['severity']))=='moderate') 		$severity_value_code = '6736007';
							else if(strtolower(trim($allergy['severity']))=='moderate to severe')$severity_value_code= '371924009';
							else if(strtolower(trim($allergy['severity']))=='severe') 			$severity_value_code = '24484000';
							
							$XML_allergies_problem_act .= '							
									<value xsi:type="CD" code="'.$severity_value_code.'"
										displayName="'.trim(ucwords(strtolower($allergy['severity']))).'"
										codeSystem="2.16.840.1.113883.6.96"
										codeSystemName="SNOMED-CT"/>
								</observation>
							</entryRelationship>
							';
						}
						//------severity code end------------------------
						
						$XML_allergies_problem_act .='</observation>
										</entryRelationship>';
						$XML_allergies_problem_act .= '</observation>';
						$XML_allergies_problem_act .= '</entryRelationship>';
						$XML_allergies_problem_act .= '</act>';
						$XML_allergies_problem_act .= '</entry>';
						$XML_allergies_section .= $XML_allergies_problem_act;
						}
					
					}
					}
					
					if($flag == 0){
						
						$XML_allergies_problem_act = '<entry typeCode="DRIV">';
						$XML_allergies_problem_act .= '	<act classCode="ACT" moodCode="EVN">';
						$XML_allergies_problem_act .= ' <templateId root="2.16.840.1.113883.10.20.22.4.30" extension="2015-08-01"/>';
						$XML_allergies_problem_act .= '	<templateId root="2.16.840.1.113883.10.20.22.4.30"/>';
						$XML_allergies_problem_act .= '	<id nullFlavor="NI"/>';
						$XML_allergies_problem_act .= '	<!-- Allergy Problem Act template -->';
						$XML_allergies_problem_act .= '	<code code="48765-2" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="Allergies, adverse reactions, alerts"/>';
						$XML_allergies_problem_act .= '	<statusCode code="active"/>';
						$XML_allergies_problem_act .= '	<effectiveTime>';
						$XML_allergies_problem_act .= '		<low nullFlavor="NI"/>';
						$XML_allergies_problem_act .= '	</effectiveTime>';
						$XML_allergies_problem_act .= '	<entryRelationship typeCode="SUBJ">';
						
						$row = $cnmhRowArr[$pid];
						$negationInd = '';
						if($row['no_value'] == "NoAllergies")
						$negationInd = "negationInd='true'";
						$XML_allergies_problem_act .= '		<observation classCode="OBS" moodCode="EVN" '.$negationInd.'>';
						$XML_allergies_problem_act .= '			<templateId root="2.16.840.1.113883.10.20.22.4.7" extension="2014-06-09"/>';
						$XML_allergies_problem_act .= '			<templateId root="2.16.840.1.113883.10.20.22.4.7"/>';
						$XML_allergies_problem_act .= '			<id nullFlavor="NI"/>';
						$XML_allergies_problem_act .= '			<!-- Allergy - intolerance observation template -->';
						$XML_allergies_problem_act .= '			<code code="ASSERTION" codeSystem="2.16.840.1.113883.5.4"/>';
						$XML_allergies_problem_act .= '			<statusCode code="completed"/>';
						$XML_allergies_problem_act .= '			<effectiveTime>';
						$XML_allergies_problem_act .= '				<low nullFlavor="UNK"/>';
						$XML_allergies_problem_act .= '			</effectiveTime>';
						$XML_allergies_problem_act .= '			';
						$XML_allergies_problem_act .= '<value xsi:type="CD" nullFlavor="NI"/>';
						$XML_allergies_problem_act .= '			<participant typeCode="CSM">';
						$XML_allergies_problem_act .= '				<participantRole classCode="MANU">';
						$XML_allergies_problem_act .= '					<playingEntity classCode="MMAT">';
						$XML_allergies_problem_act .= '						<code nullFlavor="NI"/>';
						$XML_allergies_problem_act .= '					</playingEntity>';
						$XML_allergies_problem_act .= '				</participantRole>';
						$XML_allergies_problem_act .= '			</participant>';
						$XML_allergies_problem_act .= '		</observation>';
						$XML_allergies_problem_act .= '</entryRelationship>';
						$XML_allergies_problem_act .= '</act>';
						$XML_allergies_problem_act .= '</entry>';
						$XML_allergies_section .= $XML_allergies_problem_act;
					
					}
					/* END ALLERGIES PROBLEM ACT */
			$XML_allergies_section .= '</section>';
			$XML_allergies_section .= '</component>';
	}
	/* END ALLERGIES SECTION */

	/* BEGIN IMMUNIZATION SECTION */
	$qry_immu = '';
	
	$res_immu = $immunRowArr[$pid];
	if(!in_array('visit_medication_immu',$arrOptions)){
		if($dos != ""){
			
			$res_immu = $immunDateRowArr[$pid][$dos];
		}
	}

	$XML_immunization_section = '<component>';
	$XML_immunization_section .= '<section>';
	$XML_immunization_section .= '<templateId root="2.16.840.1.113883.10.20.22.2.2.1"/>';
	$XML_immunization_section .= '<!-- ******** Immunizations section template ******** -->';
	$XML_immunization_section .= '<code code="11369-6"
								codeSystem="2.16.840.1.113883.6.1"
								codeSystemName="LOINC"
								displayName="History of immunizations"/>';
	$XML_immunization_section .= '<title>IMMUNIZATIONS</title>';
	$XML_immunization_section .= '<text>';

	
	if(count($res_immu)){
		$XML_immunization_section .= ' <table border = "1" width = "100%">';
		$XML_immunization_section .= '<thead>
								<tr>
									<th>Vaccine Code</th>
									<th>Vaccine Name</th>
									<th>Date</th>
									<th>Status</th>
									<th>Lot#</th>
									<th>Manufacturer</th>
									<th>Additional Notes</th>
								</tr>
							</thead>
							<tbody>
							';
		
		foreach($res_immu as $row_immu) {
			$admi_date = $admi_route = $dosage = $manu = $admn_by ="";
			if(str_replace('-','',$row_immu['administered_date']) != '00000000') $admi_date = $row_immu['administered_date'];
			
			if($row_immu['immzn_route_site'] != '') $admi_route = $row_immu['immzn_route_site'];
			
			if($row_immu['immzn_dose_unit'] != '' && $row_immu['immzn_dose'] != "") $dosage = $row_immu['immzn_dose']. " ".$row_immu['immzn_dose_unit'];
			
			if($row_immu['manufacturer'] != "") $manu = $row_immu['manufacturer'];
			
			if($row_immu['administered_by_id']!="" && $row_immu['administered_by_id']>0 ){
				$row_admin_by = $usrRowArr[$row_immu['administered_by_id']];
				
				if(count($row_admin_by) > 0){
					$admn_by = $row_admin_by['fname']." ".$row_admin_by['lname'];
				}
			}	
			
			$temp_immunization_id 		 = explode(' - ',$row_immu['immunization_id']);
			$row_immu['immunization_id'] = $temp_immunization_id[1];
			
			$XML_immunization_section .= '<tr>
								<td><content ID = "immun'.$row_immu['id'].'"/>CVX: '.$row_immu['immunization_cvx_code'].'</td>
								<td>'.htmlentities($row_immu['immunization_id']).'</td>
								<td>';
			$XML_immunization_section .=(preg_replace("/-/",'',$admi_date)>0)?date('M d,Y',strtotime($admi_date)):"";
			$XML_immunization_section .='</td>
								<td>'.$row_immu['scpStatus'].'</td>
								<td>'.$row_immu['lot_number'].'</td>
								<td>'.htmlentities($manu).'</td>
								<td>'.$row_immu['note'].'<br/>'.$row_immu['refusal_reason'].'</td>
							</tr>
							';
		}
		$XML_immunization_section .= '</tbody>
									</table>
									';
	}

	$XML_immunization_section .= '</text>';

	//$res_immu = imw_query($qry_immu);
	if(count($res_immu)){
		foreach($res_immu as $row_immu) {
			$ccda_code_route = $ccda_display_name_route = "";
			$XML_immunization_entry = '<entry typeCode="DRIV">';
			$XML_immunization_entry .= '<substanceAdministration classCode="SBADM" moodCode="EVN"
											negationInd="false">';
			$XML_immunization_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.52" extension="2015-08-01"/>';
			$XML_immunization_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.52"/>';
			$XML_immunization_entry .= '<id nullFlavor="NI"/>';
			$XML_immunization_entry .= '<!-- **** Immunization activity template **** -->';
			$XML_immunization_entry .= '<text>';
			$XML_immunization_entry .= '<reference value="#immun'.$row_immu['id'].'"/>';
			$XML_immunization_entry .= '</text>';
			$XML_immunization_entry .= '<statusCode code="completed"/>';
			if($row_immu['administered_date']!="")
			$XML_immunization_entry .= '<effectiveTime xsi:type="IVL_TS" value="'.str_replace('-','',$row_immu['administered_date']).'"/>';
			else
			$XML_immunization_entry .= '<effectiveTime xsi:type="IVL_TS" nullFlavor="NI"/>';
			/* DYNAMIC VALUE FOR ROUTE (EX ORAL) Medication Route FDA Value Set Code System(s):National Cancer Institute (NCI) Thesaurus 2.16.840.1.113883.3.26.1.1 */
			if($row_immu['immzn_route_site']!=""){
				$arrCCDA = getRouteCode($row_immu['immzn_route_site']);
				$ccda_code_route = $arrCCDA['ccda_code'];
				$ccda_display_name_route = $arrCCDA['ccda_display_name'];
			}
			if($ccda_code_route != "" && $ccda_display_name_route!=""){
				$XML_immunization_entry .= '<routeCode code="'.$ccda_code_route.'" codeSystem="2.16.840.1.113883.3.26.1.1"
										codeSystemName="NCI Thesaurus"
										displayName="'.$ccda_display_name_route.'"/>';
			}else{
				$XML_immunization_entry .= '<routeCode nullFlavor="NI"/>';
			}
			
			if($row_immu['immzn_dose'] != "" && $row_immu['immzn_dose_unit'] != "")							
			$XML_immunization_entry .= '<doseQuantity value="'.trim($row_immu['immzn_dose']).'" unit="'.trim($row_immu['immzn_dose_unit']).'"/>';
			else
			$XML_immunization_entry .= '<doseQuantity nullFlavor="NI"/>';
			$XML_immunization_entry .= '<consumable>';
			$XML_immunization_entry .= '<manufacturedProduct classCode="MANU">';
			$XML_immunization_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.54" extension="2014-06-09"/>';
			$XML_immunization_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.54"/>';
			$XML_immunization_entry .= '<!-- **** Immunization Medication Information **** -->';
			$XML_immunization_entry .= '<manufacturedMaterial>';
			/* DYNAMIC VALUE Vaccine Administered Value Set Code System(s):Vaccines administered (CVX) 2.16.840.1.113883.12.292 */
			
			$XML_immunization_entry .= '<code code="'.$row_immu['immunization_cvx_code'].'"
										codeSystem="2.16.840.1.113883.12.292"
										codeSystemName="CVX"
										displayName="'.$row_immu['immunization_id'].'">';							
			$XML_immunization_entry .= '<originalText><reference value = "#immun'.$row_immu['id'].'"/>'.$row_immu['immunization_id'].'</originalText>';
			$XML_immunization_entry .= '</code>';
			$XML_immunization_entry .= '</manufacturedMaterial>';
			if($row_immu['manufacturer'] != ""){
			$XML_immunization_entry .= '<manufacturerOrganization>
										<name>'.htmlentities($row_immu['manufacturer']).'</name>
									</manufacturerOrganization>';
			}
			$XML_immunization_entry .= '</manufacturedProduct>';
			$XML_immunization_entry .= '</consumable>';
			
				if($row_immu['administered_by_id']!="" && $row_immu['administered_by_id']>0 ){
					$row_admin_by = $usrRowArr[$row_immu['administered_by_id']];
					
					if(count($row_admin_by) > 0){
					
					$XML_immunization_entry .= '<performer typeCode="PRF">';
					$XML_immunization_entry .= '<assignedEntity>';
					$XML_immunization_entry .= '<!-- NPI 12345 -->';
					if($row_admin_by['user_npi'] != "")
						$XML_immunization_entry .= '<id extension="'.$row_admin_by['user_npi'].'" root="2.16.840.1.113883.4.6"/>';
					else
						$XML_immunization_entry .= '<id nullFlavor="NI"/>';
					if($row_admin_by['facility'] > 0){
						$row_facility = $facRowArr[$row_admin_by['facility']];
					}
					else{
						$row_facility = $facHqRowArr[0];
					}
					
					$XML_immunization_entry .= '<addr use="WP">';
					if($row_facility['street'] != "")
					$XML_immunization_entry .= '<streetAddressLine>'.$row_facility['street'].'</streetAddressLine>';
					if($row_facility['city'] != "")
					$XML_immunization_entry .= '<city>'.$row_facility['city'].'</city>';
					if($row_facility['state'] != "")
					$XML_immunization_entry .= '<state>'.$row_facility['state'].'</state>';
					if($row_facility['postal_code'] != "")
					$XML_immunization_entry .= '<postalCode>'.$row_facility['postal_code'].'</postalCode>';
					$XML_immunization_entry .= '<country>US</country>';
					$XML_immunization_entry .= '</addr>';
					
					if($row_facility['phone'] != "")
					$XML_immunization_entry .= '<telecom use="WP" value="tel:+1-'.core_phone_format($row_facility['phone']).'"/>';
					
					$XML_immunization_entry .= '<assignedPerson>';
					$XML_immunization_entry .= '<name>';
					$XML_immunization_entry .= '<given>'.$row_admin_by['fname'].'</given>';
					$XML_immunization_entry .= '<family>'.$row_admin_by['lname'].'</family>';
					$XML_immunization_entry .= '</name>';
					$XML_immunization_entry .= '</assignedPerson>';
					$XML_immunization_entry .= '</assignedEntity>';
					$XML_immunization_entry .= '</performer>';
				}
			}
				if($row_immu['adverse_reaction']!=""){
				$XML_immunization_entry .= '<entryRelationship typeCode="CAUS">';
				$XML_immunization_entry .= '<observation classCode="OBS" moodCode="EVN">';
				$XML_immunization_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.9"/>';
				$XML_immunization_entry .= '<!-- Reaction observation template -->';
				$XML_immunization_entry .= '<id nullFlavor="NI"/>';
				$XML_immunization_entry .= '<code code="ASSERTION" codeSystem="2.16.840.1.113883.5.4"/>';
				$XML_immunization_entry .= '<statusCode code="completed"/>';
				$XML_immunization_entry .= '<effectiveTime xsi:type="IVL_TS">';
				if($row_immu['adverse_reaction_date'] != "" && $row_immu['adverse_reaction_date'] != '0000-00-00 00:00:00'){
				$date = date('Ymd',strtotime($row_immu['adverse_reaction_date']));
				$XML_immunization_entry .= '<low value="'.$date.'"/>';
				}else{
				$XML_immunization_entry .= '<low nullFlavor="NI"/>';
				}
				$XML_immunization_entry .= '</effectiveTime>';
				$arrReaction = getProblemCode($row_immu['adverse_reaction']);
										// DYNAMIC REACTION CODE //
				if($arrReaction['ccda_code']!="" && $arrReaction['ccda_display_name']){					
				$XML_immunization_entry .= '<value xsi:type="CD"
											code="'.$arrReaction['ccda_code'].'"
											codeSystem="2.16.840.1.113883.6.96"
											codeSystemName="SNOMED CT"
											displayName="'.$arrReaction['ccda_display_name'].'"/>';
				}else{
					$XML_immunization_entry .= '<value xsi:type="CD" nullFlavor="NI"/>';
				}
				$XML_immunization_entry .= '</observation>';
				$XML_immunization_entry .= '</entryRelationship>';
				}
			$XML_immunization_entry .= '</substanceAdministration>';
			$XML_immunization_entry .= '</entry>';
			$XML_immunization_section .= $XML_immunization_entry;
		}
	}else{
		
	$XML_immunization_entry = '<entry typeCode="DRIV">
								<substanceAdministration classCode="SBADM" moodCode="EVN" negationInd="false">
								<templateId root="2.16.840.1.113883.10.20.22.4.52" extension="2015-08-01"/>
								<templateId root="2.16.840.1.113883.10.20.22.4.52"/>
								<id nullFlavor="NI"/>
								<!-- **** Immunization activity template **** -->
								<statusCode code="completed"/>
								<effectiveTime nullFlavor="NI"/>
								<consumable>
								<manufacturedProduct classCode="MANU">
								<templateId root="2.16.840.1.113883.10.20.22.4.54" extension="2014-06-09"/>
								<templateId root="2.16.840.1.113883.10.20.22.4.54"/>
								<!-- **** Immunization Medication Information **** -->
								<manufacturedMaterial>
								<code nullFlavor="NI"/>
								</manufacturedMaterial>
								</manufacturedProduct>
								</consumable>';

	$XML_immunization_entry .= '</substanceAdministration>';
	$XML_immunization_entry .= '</entry>';
	$XML_immunization_section .= $XML_immunization_entry;

	}
	$XML_immunization_section .= '</section>';
	$XML_immunization_section .= '</component>';

	/* END IMMUNIZATION SECTION */

	/* BEGIN VITAL SIGN SECTION */
	if(in_array('mu_data_set_vs',$arrOptions)){
		$XML_vital_section = '';
			$vsmRowArr[$vsmPtId][] = $vsmRow;
			$vsmDateRowArr[$vsmPtId][$vsmDateVital][] = $vsmRow;
			if($form_id == ""){
				$result_vital = $vsmRowArr[$pid];
			}else {
				$result_vital = $vsmDateRowArr[$pid][$cmtDOSArr[$form_id]];
			}
			
			if(count($result_vital)>0){
				$XML_vital_section = '<component>';
				$XML_vital_section .= '<section>';
				$XML_vital_section .= '<templateId root="2.16.840.1.113883.10.20.22.2.4.1" extension="2015-08-01"/>';
				$XML_vital_section .= '<templateId root="2.16.840.1.113883.10.20.22.2.4.1"/>';
				
				$XML_vital_section .= '<code code="8716-3"
										codeSystem="2.16.840.1.113883.6.1"
										codeSystemName="LOINC"
										displayName="VITAL SIGNS" />';
				$XML_vital_section .= '<title>VITAL SIGNS</title>';
				$XML_vital_section .= '<text>';			
					
			
				$XML_vital_section .= '<table border = "1" width = "100%">';	
				$XML_vital_section .= '<thead>
										<tr>
											<th>Vital Sign</th>
											<th >Value</th>
											<th>Date Time</th>
										</tr>
									</thead>';
				$XML_vital_section .= '<tbody>';					
				
				foreach($result_vital as $row_vital) {
					$arr_vs_result_type = vs_result_type_srh($row_vital['vital_sign']);
					if($arr_vs_result_type['code'] != "" && $arr_vs_result_type['display_name'] != "" && $row_vital['range_vital']!=""){						
					
					if(strtolower($row_vital['unit'])=='mmhg') $row_vital['unit'] = 'mm[Hg]';
					else if(strtolower($row_vital['unit'])=='beats/minute') $row_vital['unit'] = '/min';
					else if(strtolower($row_vital['unit'])=='breaths/minute') $row_vital['unit'] = '/min';
					else if(strtolower(trim($row_vital['unit']))=='kg/sqr. m' || strtolower(trim($row_vital['unit']))=='kg/sqr.m') $row_vital['unit'] = 'kg/m2';
					else if(strtolower($row_vital['unit'])=='°c' || strtolower($row_vital['unit'])=='&deg;c') $row_vital['unit'] = 'Cel';
					
					$XML_vital_section .= '
										<tr>
											<td>'.$row_vital['vital_sign'].'</td>
											<td ID = "VS_Val_'.$row_vital['id'].'">'.$row_vital['range_vital']." ".html_entity_decode($row_vital['unit']).'</td>
											<td ID = "VS_'.$row_vital['id'].'">';
					$XML_vital_section .=(preg_replace("/-/",'',$row_vital['date_vital'])>0)?date('M d,Y',strtotime($row_vital['date_vital'])):"";
					$XML_vital_section .='</td>
										</tr>
										';
					}
				}
				$XML_vital_section .= '</tbody>';
				$XML_vital_section .= '</table>';
				$XML_vital_section .= '</text>';
			
			
				$XML_vital_entry = '';
				
				if(count($result_vital)>0){
					$XML_vital_entry = '';

					foreach($result_vital as $row_vital) {	
						$arr_vs_result_type = vs_result_type_srh($row_vital['vital_sign']);
						if($arr_vs_result_type['code'] != "" && $arr_vs_result_type['display_name'] != "" && $row_vital['range_vital']!=""){
							
							if($XML_vital_entry==''){
							$XML_vital_entry = '<entry typeCode="DRIV">
												<organizer classCode="CLUSTER" moodCode="EVN">
												<!-- Vital Signs Organizer template -->
												<templateId root="2.16.840.1.113883.10.20.22.4.26" extension="2015-08-01"/>
												<templateId root="2.16.840.1.113883.10.20.22.4.26"/>
												<id nullFlavor="NI"/>
												<code code="46680005" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED CT" displayName="Vital Signs">
													<translation code="74728-7" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="Vital Signs"/>
												</code>
												<statusCode code="completed"/>
												<effectiveTime>
													<low value="'.str_replace('-','',$row_vital['date_vital']).'"/>
													<high value="'.str_replace('-','',$row_vital['date_vital']).'"/>
												</effectiveTime>';
							}
							$XML_vital_entry .= '
												<component>
													<!-- VITAL SIGN OBSERVATIONS -->
													<observation classCode="OBS" moodCode="EVN">
														<templateId root="2.16.840.1.113883.10.20.22.4.27" extension="2014-06-09"/>
														<templateId root="2.16.840.1.113883.10.20.22.4.27"/>
														<id nullFlavor="NI"/>
														<code code="'.$arr_vs_result_type['code'].'" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="'.$arr_vs_result_type['display_name'].'"/>
														<statusCode code="completed"/>
														<effectiveTime value="'.str_replace('-','',$row_vital['date_vital']).'"/>';
							if($row_vital['range_vital']!=""){
								if(strtolower($row_vital['unit'])=='mmhg') $row_vital['unit'] = 'mm[Hg]';
								else if(strtolower($row_vital['unit'])=='beats/minute') $row_vital['unit'] = '/min';
								else if(strtolower($row_vital['unit'])=='breaths/minute') $row_vital['unit'] = '/min';
								else if(strtolower(trim($row_vital['unit']))=='kg/sqr. m' || strtolower(trim($row_vital['unit']))=='kg/sqr.m') $row_vital['unit'] = 'kg/m2';
								else if(strtolower($row_vital['unit'])=='°c' || strtolower($row_vital['unit'])=='&deg;c') $row_vital['unit'] = 'Cel';
								
								$XML_vital_entry .= '<value xsi:type="PQ" value="'.trim($row_vital['range_vital']).'" unit="'.html_entity_decode(preg_replace('/\s/','',trim($row_vital['unit']))).'"/>
								';
							}else{
								$XML_vital_entry .= '<value xsi:type="PQ" nullFlavor="NI"/>
								';
							}
							$XML_vital_entry .= '</observation>
												</component>
												';

						}
					}
					$XML_vital_entry .= '</organizer>
										</entry>
										';
					$XML_vital_section .= $XML_vital_entry;				
				}
				$XML_vital_section .= '</section>';
				$XML_vital_section .= '</component>
				';
			}else{
				$XML_vital_section .= '
				<component>
					<section nullFlavor="NI">
						<!-- Vitals Section (entries required) (V3) nullflavor -->
						<templateId root="2.16.840.1.113883.10.20.22.2.4.1" extension="2015-08-01"/>
						<templateId root="2.16.840.1.113883.10.20.22.2.4.1"/>
						<code code="8716-3" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="VITAL SIGNS"/>
						<title>VITAL SIGNS</title>
						<text>No Vital Signs Information</text>
					</section>
				</component>
				';
			}
	}
	/* END VITAL SIGN SECTION */

	/* BEGIN PROBLEM SECTION */
	if(in_array('mu_data_set_problem_list',$arrOptions)){
			
			$arrProblemList = get_pt_problem_list($form_id, $pid);
			
			$XML_problem_section = '<component>
									<section>
									<!-- Problem Section with Coded Entries Required templateID -->
									<templateId root="2.16.840.1.113883.10.20.22.2.5.1" extension="2015-08-01"/>
									<templateId root="2.16.840.1.113883.10.20.22.2.5.1"/>
									<code code="11450-4" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="PROBLEM LIST"/>
									<title>PROBLEMS</title>
									<text>
									<table border = "1" width = "100%">
									<thead>
									<tr>
										<th>Problem</th>
										<th>Effective Dates</th>
										<th>Problem Type</th>
										<th>Problem Status</th>
									</tr>
								</thead>
								<tbody>
								';
			$flag = 0;					
			if(count($arrProblemList)>0){					
				foreach($arrProblemList as $problemList){
					if(!in_array($problemList['problem_name'],$arrMedHxProbList)){
						$flag = 1;
						$XML_problem_section .= '<tr ID = "PROBSUMMARY_'.$problemList['id'].'">
												<td ID = "PROBKIND_'.$problemList['id'].'">'.htmlentities($problemList['problem_name']).' [SNOMED-CT: '.$problemList['ccda_code'].']</td>
												<td>'.date('M d,Y',strtotime($problemList['onset_date'])).'</td>
												<td ID = "PROBTYPE_'.$problemList['id'].'">'.$problemList['prob_type'].'</td>
												<td ID = "PROBSTATUS_'.$problemList['id'].'">'.$problemList['status'].'</td>
											</tr>
										';
					}
				}
			}
			
			if($flag == 0){
				$XML_problem_section .= '<tr><td colspan="4">No known health problems</td></tr>
				';
			}
			$XML_problem_section .= '</tbody>
									</table>
									</text>
									<!-- Problem Concern Act -->
									';
			$flag = 0;
			if(count($arrProblemList)>0){
				foreach($arrProblemList as $problemList){
					if(!in_array($problemList['problem_name'],$arrMedHxProbList)){
						$flag = 1;
						$XML_problem_entry = '<entry>
												<act classCode="ACT" moodCode="EVN">
												<!-- Problem Concern Act template -->
												<templateId root="2.16.840.1.113883.10.20.22.4.3" extension="2015-08-01"/>
												<templateId root="2.16.840.1.113883.10.20.22.4.3"/>
												<id nullFlavor="NI"/>
												<code code="CONC" codeSystem="2.16.840.1.113883.5.6" displayName="Concern"/>
												<statusCode code="active"/>
												<effectiveTime>
													<low value="'.str_replace('-','',$problemList['onset_date']).'"/>
												</effectiveTime>
												<entryRelationship typeCode="SUBJ">
													<observation classCode="OBS" moodCode="EVN">
														<!-- Problem Observation template -->
														<templateId root="2.16.840.1.113883.10.20.22.4.4" extension="2015-08-01"/>
														<templateId root="2.16.840.1.113883.10.20.22.4.4"/>
														<id nullFlavor="NI"/>
														';
						$arrProbType = problem_type_srh($problemList['prob_type']);
						if($arrProbType['code']!="" && $arrProbType['display_name']!=""){
							$XML_problem_entry .= '<code code="'.$arrProbType['code'].'" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED CT" displayName="'.$arrProbType['display_name'].'">
							';
						$translation_code = '';						  
						if($arrProbType['code']=='409586006'){//COMPLAINT.
							$translation_code = '10154-3';
						}else if($arrProbType['code']=='282291009'){//DIAGNOSIS.
							$translation_code = '29308-4';
						}else if($arrProbType['code']=='64572001'){//CONDITION.
							$translation_code = '75323-6';
						}else if($arrProbType['code']=='55607006'){//PROBLEM.
							$translation_code = '75326-9';
						}else if($arrProbType['code']=='404684003'){//FINDING
							$translation_code = '75321-0';
						}
						if($translation_code!=''){
							$XML_problem_entry .= '<translation code="'.$translation_code.'" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="'.$arrProbType['display_name'].'" />
							';
						}
							
						$XML_problem_entry .= '</code>
						';
						}
						else{
							$XML_problem_entry .= '<code nullFlavor="NI"/>
							';
						}
						
						$XML_problem_entry .= '<statusCode code="completed"/>
						';
						if($problemList['onset_date'] != ""){
							$XML_problem_entry .= '<effectiveTime>
														<low value="'.str_replace('-','',$problemList['onset_date']).'"/>
												</effectiveTime>
												';
						}else{
							$XML_problem_entry .= '<effectiveTime nullFlavor="NI"/>
							';
						}
						
						// DYNAMIC PROBLEM VALUE //
						if($problemList['ccda_code']!=""){									
							$XML_problem_entry .= '<value xsi:type="CD" code="'.$problemList['ccda_code'].'" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED CT" displayName="'.htmlentities($problemList['problem_name']).'"/>
							';
						}else{
							$arrProblem = getProblemCode($problemList['problem_name']);
							
							// DYNAMIC REACTION CODE //
							if($arrProblem['ccda_code']!="" && $arrProblem['ccda_display_name']){					
								$XML_problem_entry .= '<value xsi:type="CD" code="'.$arrProblem['ccda_code'].'" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED CT" displayName="'.htmlentities($arrProblem['ccda_display_name']).'"/>
								';
							}else{
								$XML_problem_entry .= '<value xsi:type="CD" code="" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED CT" displayName="'.htmlentities($problemList['problem_name']).'"/>
								';
							}  
						}
						
						$XML_problem_entry .= '</observation>
											</entryRelationship>
										</act>
									</entry>
									';
						$XML_problem_section .= $XML_problem_entry;
					}
				}
			}
			if($flag == 0){
				$XML_problem_entry = '
						<entry typeCode="DRIV">
							<!-- Problem Concern Act -->	
							<act classCode="ACT" moodCode="EVN">
								<!-- ** Problem Concern Act (V3) ** -->
								<templateId root="2.16.840.1.113883.10.20.22.4.3" extension="2015-08-01" />
								<templateId root="2.16.840.1.113883.10.20.22.4.3" />
								<id nullFlavor="NI"/>
								<code code="CONC" codeSystem="2.16.840.1.113883.5.6"/>
								<text><reference value="#Concern_1"></reference></text>
								<statusCode code="active"/>
								<!-- The concern is not active, in terms of there being an active condition to be managed.-->
								<effectiveTime>
									<low nullFlavor="NI"/> <!-- Time at which THIS “concern” began being tracked.-->
								</effectiveTime> <!-- status is active so high is not applicable. If high is present it should have nullFlavor of NA-->
								<entryRelationship typeCode="SUBJ">
									<!-- Model of Meaning for No Problems -->
									<!-- The use of negationInd corresponds with the newer Observation.ValueNegationInd -->
									<!-- The negationInd = true negates the value element. --> 
									<!-- problem observation template -->
									<observation classCode="OBS" moodCode="EVN" negationInd="true">
										<!-- ** Problem observation  (V3)** -->
										<templateId root="2.16.840.1.113883.10.20.22.4.4" extension="2015-08-01"/>
										<templateId root="2.16.840.1.113883.10.20.22.4.4"/>
										<id nullFlavor="NI"/>
										<!-- updated for R2.1 -db -->
										<code code="55607006" displayName="Problem" codeSystemName="SNOMED-CT" codeSystem="2.16.840.1.113883.6.96">
											<!-- This code SHALL contain at least one [1..*] translation, which SHOULD be selected from ValueSet Problem Type (LOINC) -->
											<translation code="75326-9" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="Problem"/>
										</code>									
										<text><reference value="#problems1"></reference></text>
										<statusCode code="completed"/>
										<!-- The time when this was biologically relevant ie True for the patient. -->
										<!-- As a minimum time interval over which this is true, populate the effectiveTime/low with the current time. -->
										<!-- It would be equally valid to have a longer range of time over which this statement was represented as being true. -->
										<!-- As a maximum, you would never indicate an effectiveTime/high that was greater than the current point in time. -->
										<effectiveTime>
											<low nullFlavor="NI"/>
										</effectiveTime>
										<!-- This idea assumes that the value element could come from the Problem value set, or-->
										<!-- when negationInd was true, is could also come from the ProblemType value set (and code would be ASSERTION). -->
										<value xsi:type="CD" code="55607006"
											displayName="Problem"
											codeSystem="2.16.840.1.113883.6.96"
											codeSystemName="SNOMED CT">
											<originalText><reference value="#problems1"></reference></originalText>
										</value>
									</observation>
								</entryRelationship>
							</act>
						</entry>			
				';
				
				
				
				
				$XML_problem_section .= $XML_problem_entry;
			
			}
			
			$XML_problem_section .= '</section>';
			$XML_problem_section .= '</component>';
	}
	/* END PROBLEM SECTION */

	/* BEGIN LAB TEST SECTION */

	if(in_array('mu_data_set_lab',$arrOptions)){
		$XML_results_section = '';
		$lab_test_ordered = getPatientLabOrdered($pid);
		$no_results_lab_tests_array = array();
		if($lab_test_ordered){
			$XML_results_section = '<component>
									<section>
									<templateId root="2.16.840.1.113883.10.20.22.2.3.1"  extension="2015-08-01"/>
									<templateId root="2.16.840.1.113883.10.20.22.2.3.1"/>
									<code code="30954-2" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="RESULTS" />
									<title>RESULTS</title>
									<text>
									<table border = "1" width = "100%">
										<thead>
											<tr>
												<th>Test Code</th>
												<th>Code System</th>
												<th colspan="3">Lab Test Name</th>
												<th>Date</th>
											</tr>
										</thead>
										<tbody>
										';
				//--- LAB REQUESTS FOUND
				$j = 1;
				foreach($lab_test_ordered as $lab_test_rs){
					if($j>1){
						$XML_results_section .= '
											<tr><td colspan="6" align="center">----</td></tr>
											<tr>
												<th>Test Code</th>
												<th>Code System</th>
												<th colspan="3">Lab Test Name</th>
												<th>Date</th>
											</tr>
						';	
					}
					$XML_results_section .= '<tr>
												<td>'.$lab_test_rs['loinc'].'</td>
												<td>LOINC</td>
												<td colspan="3">'.$lab_test_rs['service'].'</td>
												<td>'.$lab_test_rs['lab_test_date_html'].'</td>
											</tr>';
				
					$lab_test_results = getPatientLabResults($lab_test_rs['lab_test_id'],$pid);
					if($lab_test_results){
						$XML_results_section .= '
												<tr>
													<th colspan="7">LABORATORY TEST RESULTS</th>
												</tr>
												<tr>
													<th>Result Code</th>
													<th>Code System</th>
													<th>Result Name</th>
													<th>Value &amp; Units</th>
													<th>Date</th>
													<th>Ref. Range</th>
												</tr>
												
												';
						//--- LAB RESULTS FOUND				
						foreach($lab_test_results as $lab_result_rs){
							$XML_results_section .= '<tr>
														<td>'.$lab_result_rs['result_loinc'].'</td>
														<td>LOINC</td>
														<td>'.$lab_result_rs['observation'].'</td>
														<td>'.$lab_result_rs['result'].' '.$lab_result_rs['uom'].'</td>
														<td>'.$lab_result_rs['lab_result_date_html'].'</td>
														<td>'.$lab_result_rs['result_range'].'</td>
													</tr>
													';
						}
						if($lab_test_rs['lab_destination']){
							$temp_lab_contact_name_arr 	= explode('-',$lab_test_rs['lab_destination']['lab_contact_name']);
							$lab_contact_name 			= trim($temp_lab_contact_name_arr['0']);
							$lab_contact_id 			= trim($temp_lab_contact_name_arr['1']);
							$lab_address_arr = array();
							if($lab_contact_id) 	$lab_address_arr['ID'] = $lab_contact_id;
							if($lab_contact_name) 	$lab_address_arr['Lab Name'] = $lab_contact_name;
							$lab_address_arr['Address']	= $lab_test_rs['lab_destination']['lab_radiology_address'];
							$lab_address_arr['City']	= $lab_test_rs['lab_destination']['lab_radiology_city'];
							$lab_address_arr['State']	= $lab_test_rs['lab_destination']['lab_radiology_state'];
							$lab_address_arr['Zip']		= $lab_test_rs['lab_destination']['lab_radiology_zip'];
							$lab_address_arr['Phone']	= $lab_test_rs['lab_destination']['lab_radiology_phone'];
							
							$XML_results_section .= '<tr>
									<td colspan="6">';
							foreach($lab_address_arr as $lab_ad_key=>$lab_ad_val){
								if(trim($lab_ad_val)=='') continue;
								$XML_results_section .= $lab_ad_key.': '.$lab_ad_val.'<br/>
								';
							}
							
							$XML_results_section .=	'
									</td>
								</tr>
								';
						}
					}else{
						$no_results_lab_tests_array[] = $lab_test_rs['lab_test_id'];
						$XML_results_section .= '<tr>
												<td colspan="6">No result information.</td>
											</tr>';	
					}
					$j++;
				}
				$XML_results_section .= '</tbody>
									</table>
									</text>
									';
		}else{
		//--- LAB REQUESTS NOT FOUND
				$XML_results_section .= '
				<component>
					<section nullFlavor="NI">
						<!-- Results Section (entries required) (V3) -->
						<templateId root="2.16.840.1.113883.10.20.22.2.3.1" extension="2015-08-01"/>
						<templateId root="2.16.840.1.113883.10.20.22.2.3.1"/>
						<code code="30954-2" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="RESULTS"/>
						<title>RESULTS</title>
						<text>Laboratory Test: None. Laboratory Values/Results: No Lab Result data</text>
					</section>
				</component>	
			';
		}
			
		if($lab_test_ordered){
			//--- LAB REQUESTS FOUND
			foreach($lab_test_ordered as $lab_test_rs){
				$current_result_lab_test_id = $lab_test_rs['lab_test_id'];			
				if(in_array($current_result_lab_test_id,$no_results_lab_tests_array)) continue; //skip this loop.
				
				$XML_results_section .= '<entry typeCode="DRIV">
										<!-- Result organizer template  -->
										<organizer classCode="BATTERY" moodCode="EVN">
											<templateId root="2.16.840.1.113883.10.20.22.4.1" extension="2015-08-01"/>
											<templateId root="2.16.840.1.113883.10.20.22.4.1"/>
											<id nullFlavor="NI"/>
											';
				if($lab_test_rs['loinc']!= "" && $lab_test_rs['service'] != ""){					
					$XML_results_section .= '<code code="'.$lab_test_rs['loinc'].'" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="'.$lab_test_rs['service'].'"/>';
				}else{
					$XML_results_section .= '<code nullFlavor="NI"/>';
				}
				
				$arrResultStatus = result_status_srh($lab_test_rs['lab_status']);	
				if($arrResultStatus['code']!="" && $arrResultStatus['display_name']!="")					
					$XML_results_section .= '<statusCode code="'.$arrResultStatus['code'].'"/>';
				else
					$XML_results_section .= '<statusCode nullFlavor="NI"/>';
				
				if($lab_test_rs['lab_test_date_ccd']!='00000000'){
					$XML_results_section .= '<effectiveTime>
												<low value="'.$lab_test_rs['lab_test_date_ccd'].'"/>
												<high value="'.$lab_test_rs['lab_test_date_ccd'].'"/>
											</effectiveTime>
					';
				}else{
					$XML_results_section .= '<effectiveTime nullFlavor="NI"/>
					';	
				}
				$lab_test_results = getPatientLabResults($lab_test_rs['lab_test_id'],$pid);
				if($lab_test_results){
					//-----------STARTING LAB RESULT COMPONENT------
					$i = 1;
					foreach($lab_test_results as $lab_result_rs){
						$current_result_lab_test_id = $lab_result_rs['lab_test_id'];
						if(in_array($current_result_lab_test_id,$no_results_lab_tests_array)) continue; //skip this loop.
						$XML_results_section .= '
									<component>
										<observation classCode="OBS" moodCode="EVN">
										<!-- Result observation template -->
										<templateId root="2.16.840.1.113883.10.20.22.4.2" extension="2015-08-01"/>
										<templateId root="2.16.840.1.113883.10.20.22.4.2"/>
										<id nullFlavor="NI"/>';
										/* DYNAMIC CODE FROM LOINC ResultTypeCode  */
						if($lab_result_rs['observation'] != "")						
							$XML_results_section .= '<code code="'.$lab_result_rs['result_loinc'].'" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="'.$lab_result_rs['observation'].'"/>';
						else
							$XML_results_section .= '<code nullFlavor="NI"/>
							';
						$XML_results_section .= '<text>
													<reference value="#result'.$i.'"/>
												</text>
												<statusCode code="completed"/>
												';
						
						if($lab_result_rs['lab_result_date_ccd'] != "00000000")
							$XML_results_section .= '<effectiveTime value="'.$lab_result_rs['lab_result_date_ccd'].'"/>
							';
						else
							$XML_results_section .= '<effectiveTime nullFlavor="NI"/>
							';
						
						$place_observation_range = false;	
						if($lab_result_rs['result'] != "" && $lab_result_rs['result_range']!="" && $lab_result_rs['result']==$lab_result_rs['result_range']){
							$XML_results_section .= '<value xsi:type="ST">'.trim($lab_result_rs['result']).'</value>
							';
						}else if($lab_result_rs['result'] != "" && $lab_result_rs['result_range']!="" && $lab_result_rs['uom']!="" && $lab_result_rs['result']!=$lab_result_rs['result_range']){
							$XML_results_section .= '<value xsi:type="PQ" value="'.trim($lab_result_rs['result']).'" unit="'.trim($lab_result_rs['uom']).'"/>
							';
						}else if($lab_result_rs['result'] != "" && $lab_result_rs['result_range']!="" && $lab_result_rs['uom']=="" && $lab_result_rs['result']!=$lab_result_rs['result_range']){
							$XML_results_section .= '<value xsi:type="PQ" value="'.trim($lab_result_rs['result']).'"/>
							';
							$place_observation_range = true;
						}else{
							$XML_results_section .= '<value xsi:type="PQ" nullFlavor="NI"/>
							';
						}
						if($lab_result_rs['abnormal_flag']=='') $lab_result_rs['abnormal_flag'] = 'N';
						if($lab_result_rs['abnormal_flag'] != "")
							$XML_results_section .= '<interpretationCode code="'.$lab_result_rs['abnormal_flag'].'" codeSystem="2.16.840.1.113883.5.83"/>
							';
						else
							$XML_results_section .= '<interpretationCode nullFlavor="NI"/>
							';
						
						$XML_results_section .= '
												</observation>
												</component>';
						
							
						$i++;
					}
					//-------END OF LAB RESULT COMPONENT-----------
				}else{
				//---code here IF NO RESULT FOUND----
					$XML_results_section .= '<component>
											<observation classCode="OBS" moodCode="EVN">
											<!-- Result observation template -->
											<templateId root="2.16.840.1.113883.10.20.22.4.2" extension="2015-08-01"/>
											<templateId root="2.16.840.1.113883.10.20.22.4.2"/>
											<id nullFlavor="NI"/>
											<code nullFlavor="NI"/>
											<statusCode code="completed"/>
											<effectiveTime nullFlavor="NI"/>
											<value xsi:type="PQ" nullFlavor="NI"/>
											</observation>
										</component>
										';				
				}
				$XML_results_section .= '	</organizer>
										</entry>
										';
			}
		}
		if($lab_test_ordered){
			$XML_results_section .= '</section>';
			$XML_results_section .= '</component>';
		}
		
		
	}
	/* END LAB TEST SECTION */

	/* BEGIN ASSESSMENT SECTION */
	$arrApVals = array();
	$row = valuesNewRecordsAssess($pid);
	if($row != false){
		$strXml = stripslashes($row["assess_plan"]);
		$oChartApXml = new ChartAP($pid,$form_id);
		
		$arrApVals = $oChartApXml->getVal();
		$arrApVals = $arrApVals['data']['ap'];
	}
	$XML_assessment_section = '
	<!-- ASSESSMENT SECTION -->
	<!-- There is no R2.1 (R2.0) version of assessment section, using R1.1 templateId only -->
								<component>
								<section>
								<templateId root="2.16.840.1.113883.10.20.22.2.8"/>
								<code codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" code="51848-0" displayName="ASSESSMENTS"/>
								<title>ASSESSMENTS</title>
								<text>
									<table border = "1" width = "100%">
										<thead>
											<tr>
												<th>Assessment</th>
											</tr>
										</thead>
										<tbody>
										';
	$flag = 0;
	foreach($arrApVals as $apVals){
		if($apVals['assessment'] != ""){
			$split_by_colon = explode(';',$apVals['assessment']);
			if(isset($split_by_colon[1])){
				$temp_assess_text_part = explode('(',trim($split_by_colon[1]));
			}else{
				$temp_assess_text_part = explode('(',trim($split_by_colon[0]));	
			}
			$assess_text_part	= $temp_assess_text_part[0];
			$flag = 1;
			$XML_assessment_section .= '<tr>
										<td>'.htmlentities($assess_text_part).'</td>
									</tr>
									';
		}
	}
	if($flag == 0){
		$XML_assessment_section .= '<tr><td>No data.</td></tr>
			';
	}
	$XML_assessment_section .= 	'
								</tbody>
								</table>
								</text>
								</section>
								</component>
								';
	/* END ASSESSMENT SECTION */

	/* BEGIN PLAN OF TREATMENT SECTION */
	$arrApVals = array();
	$row = valuesNewRecordsAssess($pid);
	if($row != false){
		$strXml = stripslashes($row["assess_plan"]);
		$oChartApXml = new ChartAP($pid,$row["form_id"]);
		
		$arrApVals = $oChartApXml->getVal();
		$arrApVals = $arrApVals['data']['ap'];
	}
	$XML_assessment_section .= '
							<!-- PLAN OF TREATMENT SECTION -->
							<component>
							<section>
							<templateId root="2.16.840.1.113883.10.20.22.2.10"/>
							<templateId root="2.16.840.1.113883.10.20.22.2.10" extension="2014-06-09"/>
							<code codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" code="18776-5" displayName="PLAN OF TREATMENT"/>
							<title>PLAN OF TREATMENT</title>
							<text>
								<table border = "1" width = "100%">
									<thead>
										<tr>
											<th>Plan of Treatment</th>
										</tr>
									</thead>
									<tbody>
										';
	$flag = 0;
	foreach($arrApVals as $apVals){
			$flag = 1;
			$XML_assessment_section .= '
									<tr>
										<td>'.nl2br(htmlentities($apVals['plan'])).'</td>
									</tr>
									';
	}
	if($flag == 0){
		$XML_assessment_section .= '<tr><td>No data.</td></tr>
			';
	}
	$XML_assessment_section .= 	'
								</tbody>
								</table>
								</text>
								</section>
								</component>
								';

	/* END PLAN OF TREATMENT SECTION */

	/* Patient Goals */
	$qry_form_id='';
	if(empty($form_id) == false){$qry_form_id=' AND form_id ="'.$form_id.'"';}
	
	if(empty($form_id) == false){
		$row_arr = $ptGoalFormRowArr[$pid][$form_id];
	}else {
		$row_arr = $ptGoalRowArr[$pid];
	}

	$XML_goals_section = '
		<!-- GOAL SECTION -->
			<component>
					<section '.((is_array($row_arr) && count($row_arr) <= 0) ? 'nullFlavor="NI"' : '').'>
					<templateId root="2.16.840.1.113883.10.20.22.2.60"/>
					<code code="61146-7" displayName="Goals" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC"/>
					<title>GOALS</title>
					<text>
						<table border = "1" width = "100%">
							<thead>
								<tr>
									<th>Goal</th>
									<th>Value</th>
									<th>Date</th>
								</tr>
							</thead>
							<tbody>
							';
	if(is_array($row_arr) && count($row_arr)>0){
		$xml_goals_entry = '';
		foreach($row_arr as $res_arr_goal){
			$goal_set=trim(addslashes($res_arr_goal["goal_set"]));
			$loinc_code=trim(addslashes($res_arr_goal["loinc_code"]));
			$goal_data=trim(addslashes($res_arr_goal["goal_data"]));
			$goal_data_type=trim(addslashes($res_arr_goal["goal_data_type"]));
			$gloal_data_type_unit=trim(addslashes($res_arr_goal["gloal_data_type_unit"]));
			$goal_date=trim(addslashes($res_arr_goal["goal_date"]));
			$goal_date_show=trim(addslashes($res_arr_goal["goal_date_show"]));
			$operator_id=trim($res_arr_goal["operator_id"]);
			
			$res_user = $usrRowArr[$operator_id];
			
			if(count($res_user) > 0){
				
				$fname=$res_user["fname"];
				$lname=$res_user["lname"];
				$mname=$res_user["mname"];
				$pro_suffix=$res_user["pro_suffix"];
			}
			$XML_goals_section.='<!-- the following two do not need to be coded in entries and can be combined together -->';
			$XML_goals_section.='<tr>';
			$XML_goals_section.='<td>'.$goal_set.'</td>';
			$XML_goals_section.='<td>'.$goal_data.'</td>';
			$XML_goals_section.='<td>'.$goal_date.'</td>';
			$XML_goals_section.='</tr>';
			
			$xml_goals_entry .= '<entry>';
			$xml_goals_entry.='<!-- Goal Observation -->';
			$xml_goals_entry.='<observation classCode="OBS" moodCode="GOL">';
			$xml_goals_entry.='<templateId root="2.16.840.1.113883.10.20.22.4.121"/>';
			$xml_goals_entry.='<id root="3700b3b0-fbed-11e2-b778-0800200c9a66"/>';
			$xml_goals_entry.='<!-- TODO (min - not required for test data): find a more suitable LOINC code for generic fever or for Visual Inspection -db -->';
			$xml_goals_entry.='<code code="'.$loinc_code.'" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="'.$goal_set.'"/>';
			$xml_goals_entry.='<statusCode code="active"/>';
			$xml_goals_entry.='<effectiveTime value="'.$goal_date_show.'"/>';
			$xml_goals_entry.='<!-- this may not be the recommended way to record a visual inspection -db -->';
			if(empty($goal_data)){
				$xml_goals_entry.='<value xsi:type="ST" nullFlavor="NP" />';
			}else{
				$xml_goals_entry.='<value xsi:type="ST">'.$goal_data.'</value>';
			}
			$xml_goals_entry.='<author>';
			$xml_goals_entry.='<templateId root="2.16.840.1.113883.10.20.22.4.119"/>';
			$xml_goals_entry.='<time value="'.$goal_date_show.'"/>';
			$xml_goals_entry.='<assignedAuthor>';
			$xml_goals_entry.='<id root="d839038b-7171-4165-a760-467925b43857"/>';
			$xml_goals_entry.='<code code="163W00000X" displayName="Registered nurse" codeSystem="2.16.840.1.113883.6.101" codeSystemName="Healthcare Provider Taxonomy (HIPAA)"/>';
			$xml_goals_entry.='<assignedPerson>';
			$xml_goals_entry.='<name>';
			$xml_goals_entry.='<given>'.$fname.'</given>';
			$xml_goals_entry.='<family>'.$lname.'</family>';
			if(empty($pro_suffix)){
				$xml_goals_entry.='<suffix nullFlavor="NP"></suffix>';
			}else{
				$xml_goals_entry.='<suffix>'.$pro_suffix.'</suffix>';
			}
			$xml_goals_entry.='</name>';
			$xml_goals_entry.='</assignedPerson>';
			$xml_goals_entry.='</assignedAuthor>';
			$xml_goals_entry.='</author>';
			$xml_goals_entry.='<!-- Patient Author -->';
			$xml_goals_entry.='<author typeCode="AUT">';
			$xml_goals_entry.='<templateId root="2.16.840.1.113883.10.20.22.4.119"/>';
			$xml_goals_entry.='<time/>';
			$xml_goals_entry.='<assignedAuthor>';
			$xml_goals_entry.='<!-- This id can point back to the record target already described in the CDA header (or someone else can be described here) -->';
			$xml_goals_entry.='<!-- This particular example points back to the record target -->';
			$xml_goals_entry.='<id extension="996-756-495" root="2.16.840.1.113883.19.5"/>';
			$xml_goals_entry.='</assignedAuthor>';
			$xml_goals_entry.='</author>';
			$xml_goals_entry.='</observation>';
			$xml_goals_entry .= '</entry>';
		}
	}else{
		$XML_goals_section .= '<tr><td colspan="3">No Goals Data</td></tr>';
	}

	$XML_goals_section.='</tbody></table></text>';
	$XML_goals_section .= $xml_goals_entry;
	$XML_goals_section.='</section></component>';
	/* Patient Goals End */

	/* Health Concerns */
	$resp_observation = $hcObsRowArr[$pid];
	if(!empty($form_id)){
		$resp_observation = $hcObsFormRowArr[$pid][$form_id];
	}

	unset($res_sql);


	$resp_concern = $hcConcernRowArr[$pid];
	if(!empty($form_id)){
		$resp_concern = $hcConcernFormRowArr[$pid][$form_id];
	}

	unset($res_sql);

	$hcRelObsRowArr[$hcRelObsPtId][] = $hcRelObsRow;
	$hcRelObsFormRowArr[$hcRelObsPtId][$hcRelObsFormId][] = $hcRelObsRow;
	$resp_rel_observation = $hcRelObsRowArr[$pid];
	if(!empty($form_id)){
		$resp_rel_observation = $hcRelObsFormRowArr[$pid][$form_id];
	}
	
	unset($res_sql);

	$component = new SimpleXMLElement('<component/>');

		$section = $component->addChild('section');
		$section->addAttribute('nullFlavor', 'NI');
		
		/*Start static Data for the Section*/
		$templateId = $section->addChild('templateId');
		$templateId->addAttribute('root', '2.16.840.1.113883.10.20.22.2.58');
		$templateId->addAttribute('extension', '2015-08-01');
		
		$templateId = $section->addChild('code');
		$templateId->addAttribute('code', '75310-3');
		$templateId->addAttribute('displayName', 'Health Concerns Document');
		$templateId->addAttribute('codeSystem', '2.16.840.1.113883.6.1');
		$templateId->addAttribute('codeSystemName', 'LOINC');
		
		$section->addChild('title', 'Health Concerns Section');
		/*End static Data for the Section*/
		
		/*Dynamic Data - Starting <text> element*/
		$text = $section->addChild('text');
		
		/*Observation table*/
			$observation = $text->addChild('table');
			$text->addChild('br');
			
			$observation->addAttribute('border', '1');
			$observation->addAttribute('width', '100%');
			
			/*Static table head*/
			$observationThead = $observation->addChild('thead');
			$observationTheadTr = $observationThead->addChild('tr');
			
			$observationTheadTr->addChild('th', 'Observations');
			$observationTheadTr->addChild('th', 'Status');
			$observationTheadTr->addChild('th', 'Date');
			/*End Static table head*/
			
			/*Dynamic Table Rows*/
			$observationTbody = $observation->addChild('tbody');
			
			
			if( $resp_observation && count($resp_observation) > 0 && is_array($resp_observation))
			{
				foreach($resp_observation as $row)
				{
					$observationTbodyTr = $observationTbody->addChild('tr');;
					$observationTbodyTr->addChild('td', addslashes($row['observation']));
					$observationTbodyTr->addChild('td', addslashes($row['status']));
					$observationTbodyTr->addChild('td', addslashes($row['observation_date']));
					
					/*Entry Tag*/
					$entry1 = $section->addChild('entry');
					$entryObervation = $entry1->addChild('observation');
						$entryObervation->addAttribute('classCode', 'OBS');
						$entryObervation->addAttribute('moodCode', 'EVN');
					
					$template1 = $entryObervation->addChild('templateId');
					$template1->addAttribute('root', '2.16.840.1.113883.10.20.22.4.5');
					$template1->addAttribute('extension', '2014-06-09');
					$entryObervation->addChild('templateId')->addAttribute('root', '2.16.840.1.113883.10.20.22.4.5');
					
					$entryObervation->addChild('id')->addAttribute('root', createGUID((int)$row['id'], 'observation'));
					
					$code = $entryObervation->addChild('code');
					$code->addAttribute('code', '11323-3');
					$code->addAttribute('codeSystem', '2.16.840.1.113883.6.1');
					$code->addAttribute('codeSystemName', 'LOINC');
					$code->addAttribute('displayName', 'Health status');
					
					$entryObervation->addChild('statusCode')->addAttribute('code', 'completed');
					
					$value = $entryObervation->addChild('value');
					$value->addAttribute('xmlns:xsi:type', 'CD');
					$value->addAttribute('code', addslashes($row['snomed_code']));
					$value->addAttribute('codeSystem', '2.16.840.1.113883.6.96');
					$value->addAttribute('codeSystemName', 'SNOMED-CT');
					$value->addAttribute('displayName', addslashes($row['observation']));
				}
			}
			else
			{
				$observationTbodyTr = $observationTbody->addChild('tr');
				$observationTbodyTrTd = $observationTbodyTr->addChild('td', 'No Health Observation');
				$observationTbodyTrTd->addAttribute('colspan', '3');
			}
			/*End Dynamic Table Rows*/
		/*End Observation table*/
		
		/*Concern table*/
			$concern = $text->addChild('table');
			$text->addChild('br');
			
			$concern->addAttribute('border', '1');
			$concern->addAttribute('width', '100%');
			
			/*Static table head*/
			$concernThead = $concern->addChild('thead');
			$concernTheadTr = $concernThead->addChild('tr');
			
			$concernTheadTr->addChild('th', 'Concern - HealthCare Concerns refer to underlying clinical facts');
			$concernTheadTr->addChild('th', 'Status');
			$concernTheadTr->addChild('th', 'Date');
			/*End Static table head*/
			
			/*Dynamic Table Rows*/
			$concernTbody = $concern->addChild('tbody');
			
			if( $resp_concern && count($resp_concern) > 0 && is_array($resp_concern))
			{
				/*Entry Tag*/
				$entry2 = $section->addChild('entry');
				$actConcern = $entry2->addChild('act');
				$actConcern->addAttribute('classCode', 'ACT');
				$actConcern->addAttribute('moodCode', 'EVN');
				
				$template = $actConcern->addChild('templateId');
				$template->addAttribute('root', '2.16.840.1.113883.10.20.22.4.132');
				$template->addAttribute('extension', '2015-08-01');
				
				$actConcern->addChild('id')->addAttribute('root', createGUID((int)$row['id'], 'concernOuter'));
				
				$code = $actConcern->addChild('code');
				$code->addAttribute('code', '75310-3');
				$code->addAttribute('codeSystem', '2.16.840.1.113883.6.1');
				$code->addAttribute('codeSystemName', 'LOINC');
				$code->addAttribute('displayName', 'Health Concern');
				
				$actConcern->addChild('statusCode')->addAttribute('code', 'completed');
					
				foreach($resp_concern as $row)
				{
					$concernTbodyTr = $concernTbody->addChild('tr');;
					$concernTbodyTr->addChild('td', addslashes($row['concern']));
					$concernTbodyTr->addChild('td', addslashes($row['status']));
					$concernTbodyTr->addChild('td', addslashes($row['concern_date']));
					
					$concern = $actConcern->addChild('entryRelationship');
					$concern->addAttribute('typeCode', 'REFR');
					
					$act = $concern->addChild('act');
					$act->addAttribute('classCode', 'ACT');
					$act->addAttribute('moodCode', 'EVN');
					
					$act->addChild('templateId')->addAttribute('root', '2.16.840.1.113883.10.20.22.4.122');
					
					$act->addChild('id')->addAttribute('root', createGUID((int)$row['id'], 'concern'));
					
					$act->addChild('code')->addAttribute('nullFlavor', 'NP');
					
					$act->addChild('statusCode')->addAttribute('code', addslashes(strtolower($row['status'])));
				}
			}
			else
			{
				$concernTbodyTr = $concernTbody->addChild('tr');
				$concernTbodyTrTd = $concernTbodyTr->addChild('td', 'No Health Concern.');
				$concernTbodyTrTd->addAttribute('colspan', '3');
			}
			/*End Dynamic Table Rows*/
		/*End Concern table*/
		
		
		/*Related Observations table*/
			$relObservation = $text->addChild('table');
			$text->addChild('br');
			
			$relObservation->addAttribute('border', '1');
			$relObservation->addAttribute('width', '100%');
			
			/*Static table head*/
			$relObservationThead = $relObservation->addChild('thead');
			$relObservationTheadTr = $relObservationThead->addChild('tr');
			
			$relObservationTheadTr->addChild('th', 'Related observation');
			$relObservationTheadTr->addChild('th', 'Date');
			/*End Static table head*/
			
			/*Dynamic Table Rows*/
			$relObservationTbody = $relObservation->addChild('tbody');
			
			if( $resp_rel_observation && count($resp_rel_observation) > 0 && is_array($resp_rel_observation))
			{
				foreach($resp_rel_observation as $row)
				{
					$relObservationTbodyTr = $relObservationTbody->addChild('tr');;
					$relObservationTbodyTr->addChild('td', addslashes($row['rel_observation']));
					$relObservationTbodyTr->addChild('td', addslashes($row['rel_observation_date']));
					
					
					/*Entry Tag*/
					if( !isset($actConcern) && !is_object($actConcern) )
					{
						$entry2 = $section->addChild('entry');
						$actConcern = $entry2->addChild('act');
						$actConcern->addAttribute('classCode', 'ACT');
						$actConcern->addAttribute('moodCode', 'EVN');
					}
					
					$entryRel = $actConcern->addChild('entryRelationship');
					$entryRel->addAttribute('typeCode', 'REFR');
					$entryRel->addAttribute('inversionInd', 'true');
					
					$obsRel = $entryRel->addChild('observation');
					$obsRel->addAttribute('classCode', 'OBS');
					$obsRel->addAttribute('moodCode', 'EVN');
					
					$template = $obsRel->addChild('templateId');
					$template->addAttribute('root', '2.16.840.1.113883.10.20.22.4.4');
					$template->addAttribute('extension', '2014-06-09');
					
					$obsRel->addChild('templateId')->addAttribute('root', '2.16.840.1.113883.10.20.22.4.4');
					
					$obsRel->addChild('id')->addAttribute('root', createGUID((int)$row['id'], 'obsRel'));
					
					$code = $obsRel->addChild('code');
					$code->addAttribute('code', '29308-4');

					$code->addAttribute('codeSystem', '2.16.840.1.113883.6.1');
					$code->addAttribute('codeSystemName', 'LOINC');
					$code->addAttribute('displayName', 'Diagnosis');
					
					$obsRel->addChild('statusCode')->addAttribute('code', 'completed');
					
					$obsRel->addChild('effectiveTime')->addChild('low')->addAttribute('value', $row['rel_observation_date_raw']);
					
					$value = $obsRel->addChild('value');
					$value->addAttribute('xmlns:xsi:type', 'CD');
					$value->addAttribute('code', addslashes($row['snomed_code']));
					$value->addAttribute('codeSystem', '2.16.840.1.113883.6.96');
					$value->addAttribute('codeSystemName', 'SNOMED-CT');
					$value->addAttribute('displayName', addslashes($row['rel_observation']));
				}
			}
			else
			{
				$relObservationTbodyTr = $relObservationTbody->addChild('tr');
				$relObservationTbodyTrTd = $relObservationTbodyTr->addChild('td', 'No Related Observation.');
				$relObservationTbodyTrTd->addAttribute('colspan', '2');
			}
			/*End Dynamic Table Rows*/
		/*End Concern table*/
		
		
		
		/*End Dynamic Data - <text>*/
		
	$finalHealthConcern = $component->saveXML();
	$finalHealthConcern = str_replace("<?xml version=\"1.0\"?>\n", "", $finalHealthConcern);
	/* Health Concerns End */


	/* BEGIN ENOCUNTERS SECTION */
	$encounter_diagnosis = getEncounterDiagnosis($form_id, $pid);
	if($encounter_diagnosis){
			$XML_encouters_section = '<component>
										<section>
										<templateId root="2.16.840.1.113883.10.20.22.2.22.1" extension="2015-08-01"/>
										<templateId root="2.16.840.1.113883.10.20.22.2.22.1"/>
										<code code="46240-8" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="History of encounters"/>
										<title>ENCOUNTERS</title>
										<text>
										<table border = "1" width = "100%">
										<thead>
										<tr>
											<th>Encounter Diagnosis</th>
											<th>Location</th>
											<th>Date</th>
										</tr>
										</thead>
										<tbody>
										';
			
			$flag = 0;
			foreach($encounter_diagnosis as $problemList){	
				if(!in_array($problemList['problem_name'],$arrMedHxProbList)){	
				$flag = 1;			
				$XML_encouters_section .= '
					<tr>
						<td ID="enc_problem'.$problemList['id'].'">'.htmlentities($problemList['problem_name']).'</td>';

					$encounter_diag_location = getEncounterFacility($problemList['form_id'],$problemList['pt_id']);
					$XML_encouters_section .= '
						<td ID="enc_problem_location'.$problemList['id'].'">'.htmlentities($encounter_diag_location['name']). " - ".$encounter_diag_location['street'].",".$encounter_diag_location['city']." ".$encounter_diag_location['state'].' - '.$encounter_diag_location['postal_code'].'</td>';
						
				$XML_encouters_section .= '
						<td ID="enc_problem_date'.$problemList['id'].'">'.date('M d,Y',strtotime($problemList['onset_date'])).'</td>
					</tr>
					';
				}
			}
			
			if($flag == 0){
				$XML_encouters_section .= '<tr><td>No Data.</td></tr>
				';
			}
			$XML_encouters_section .= '</tbody>
									</table>
								</text>
								';
						
			/* BEGIN ENCOUNTER ACTIVITIES */
			foreach($encounter_diagnosis as $problemList){
			
				$XML_encouter_entry = '<entry typeCode="DRIV">
										<encounter classCode="ENC" moodCode="EVN">
										<!-- Encounter Activities -->
										<templateId root="2.16.840.1.113883.10.20.22.4.49" extension="2015-08-01"/>
										<templateId root="2.16.840.1.113883.10.20.22.4.49"/>
										<id nullFlavor="NI"/>
										';
				/* DYNAMIC ENCOUNTER TYPE CODE TO GET FROM CHART ENTRY*/ 
				
				$row = $ccdaCptCodeRowArr[$problemList['form_id']][$problemList['pt_id']];
				if($row['ccda_cpt_code'] != "" && $row['ccda_cpt_code']!=0){				
					$XML_encouter_entry .= '<code code="'.$row['ccda_cpt_code'].'" displayName="'.$row['temp_name'].'" codeSystem="2.16.840.1.113883.6.12" codeSystemVersion="4">
												<translation code="AMB" codeSystem="2.16.840.1.113883.5.4" displayName="Ambulatory" codeSystemName="HL7 ActEncounterCode"/>
											</code>
											';
				}else if($row['ccda_cpt_code']==0){				
					$XML_encouter_entry .= '<code code="0123" displayName="comprehensive" codeSystem="2.16.840.1.113883.6.12" codeSystemVersion="4">
												<translation code="AMB" codeSystem="2.16.840.1.113883.5.4" displayName="Ambulatory" codeSystemName="HL7 ActEncounterCode"/>
											</code>
											';
				}else{
					$XML_encouter_entry .= '<code nullFlavor="NI"/>
						';
				}

				$row = $cmtJnDosRowArr[$problemList['form_id']];
				if($row['date_of_service'] != ""){
					$XML_encouter_entry .= '<effectiveTime value="'.str_replace("-","",$row['date_of_service']).'"/>';
				}else{
					$XML_encouter_entry .= '<effectiveTime nullFlavor="NI"/>';
				}
			
				$arrProviderType = get_provider_code($row['user_type']);
			
				//------BEGIN CHART PROVIDER INFO --------
				if($arrProviderType['code'] != "" && $arrProviderType['display_name'] != ""){
				$XML_encouter_entry .= '<performer>
										<assignedEntity>
										<id nullFlavor="NI"/>
										<code code="'.$arrProviderType['code'].'" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED CT" displayName="'.$arrProviderType['display_name'].'"/>
										</assignedEntity>
										</performer>
										';
				}
			
				//------END CHART PROVIDER INFO --------						
				$encounter_diag_location = getEncounterFacility($problemList['form_id'],$problemList['pt_id']);
				if($encounter_diag_location){
					//---------BEGIN LOCATION ----------
					$XML_encouter_entry .= '<participant typeCode = "LOC">
												<participantRole classCode = "SDLOC">
												<templateId root = "2.16.840.1.113883.10.20.22.4.32"/>
												<!--Service Delivery Location template -->
												<code nullFlavor="NI"/>
												<addr>
												';
					if($encounter_diag_location['street'] != "")
						$XML_encouter_entry .= '<streetAddressLine>'.$encounter_diag_location['street'].'</streetAddressLine>';
					if($encounter_diag_location['city'] != "")
						$XML_encouter_entry .= '<city>'.$encounter_diag_location['city'].'</city>';
					if($encounter_diag_location['state'] != "")
						$XML_encouter_entry .= '<state>'.$encounter_diag_location['state'].'</state>';
					if($encounter_diag_location['postal_code'] != "")
						$XML_encouter_entry .= '<postalCode>'.$encounter_diag_location['postal_code'].'</postalCode>';
					
					$XML_encouter_entry .= '<country>US</country>
										</addr>
										';	
					if($encounter_diag_location['phone'] != "")
						$XML_encouter_entry .= '<telecom use="WP" value="tel:+1-'.core_phone_format($encounter_diag_location['phone']).'"/>
						';
					else
						$XML_encouter_entry .= '<telecom nullFlavor="NI"/>
						';
					$XML_encouter_entry .='
									<playingEntity classCode = "PLC">
										<name>'.htmlentities($encounter_diag_location['name']).'</name>
									</playingEntity>
								</participantRole>
							</participant>
							';
				}
				//---------END LOCATION ----------			
				if(!in_array($problemList['problem_name'],$arrMedHxProbList)){
					$XML_encouter_entry .= '<entryRelationship typeCode="SUBJ" >';
					/* BEGIN ENCOUNTER DIAGNOSIS ACT*/
					$XML_encouter_entry .= '<act classCode="ACT" moodCode="EVN">
											<!-- Encounter diagnosis act -->
											<templateId root="2.16.840.1.113883.10.20.22.4.80" extension="2015-08-01"/>
											<templateId root="2.16.840.1.113883.10.20.22.4.19"/>
											<id nullFlavor="NI"/>
											<code code="29308-4" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="ENCOUNTER DIAGNOSIS"/>
											<!-- <statusCode code="active"/> -->
											<effectiveTime><low value="'.str_replace("-","",$row['date_of_service']).'"/></effectiveTime>
											<entryRelationship typeCode="SUBJ" inversionInd="false">
											<!-- Problem Observation (V3) -->
											<observation classCode="OBS" moodCode="EVN">
											<templateId root="2.16.840.1.113883.10.20.22.4.4" extension="2015-08-01"/>
											<templateId root="2.16.840.1.113883.10.20.22.4.4"/>
											<id nullFlavor="NI"/>
											';
					$arrProbListType = problem_type_srh(strtolower($problemList['prob_type']));
					if($arrProbListType['code']!="" && $arrProbListType['display_name']!=""){
						$XML_encouter_entry .= '<code code="'.$arrProbListType['code'].'" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED CT" displayName="'.$arrProbListType['display_name'].'">
													<translation code="75321-0" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="Clinical Finding"/>
												</code>
						';
					}else{
						$XML_encouter_entry .= '<code nullFlavor="NI"/>
						';
					}
					/* BEGIN ENCOUNTER ENTRY */
					$XML_encouter_entry .= '<!-- Problem Observation template -->
					';
					$arrProbList = array();
					
					$XML_encouter_entry .= '<statusCode code="completed"/>
											<effectiveTime><low value="'.str_replace("-","",$row['date_of_service']).'"/></effectiveTime>
											';
											/* DYNAMIC SNOMED CT CODE FROM PROBLEM VALUE SET */
					if($problemList['ccda_code']!=""){
						$disp_problem_name = str_replace('(ICD-10-CM ','(',$problemList['problem_name']);						
						$XML_encouter_entry .= '<value xsi:type="CD" code="'.$problemList['ccda_code'].'" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED CT" displayName="'.$disp_problem_name.'"/>';
					}else{
						$arrProblem = getProblemCode($problemList['problem_name']);
						// DYNAMIC REACTION CODE //
						if($arrProblem['ccda_code']!="" && $arrProblem['ccda_display_name']){					
							$XML_encouter_entry .= '<value xsi:type="CD"
														code="'.$arrProblem['ccda_code'].'"
														codeSystem="2.16.840.1.113883.6.96"
														codeSystemName="SNOMED CT"
														displayName="'.$arrProblem['ccda_display_name'].'"/>';
						}else{
							$XML_encouter_entry .= '<value xsi:type="CD" nullFlavor="NI"/>';
						} 
					}
					/* END ENCOUNTER ENTRY */							
					$XML_encouter_entry .= '</observation>';
					$XML_encouter_entry .= '</entryRelationship>';
					$XML_encouter_entry .= '</act>';
					/* END ENCOUNTER DIAGNOSIS ACT */
					$XML_encouter_entry .= '</entryRelationship>';
				}
				/* END ENCOUNTER ACTIVITIES */
				$XML_encouter_entry .= '</encounter>';
				$XML_encouter_entry .= '</entry>';
			}
			
			/* END PROBLEM OBSERVATION */
			$XML_encouters_section .= $XML_encouter_entry;
			$XML_encouters_section .= '</section>';
			$XML_encouters_section .= '</component>';
	}
	/* END ENOCUTERS SECTION */

	/* BEGIN PLAN OF CARE */
	if(in_array('mu_data_set_ap',$arrOptions) || in_array('future_appointment',$arrOptions) || in_array('clinical_instruc',$arrOptions) || in_array('future_sch_test',$arrOptions) || in_array('recommended_patient_decision_aids',$arrOptions)){
			
			$XML_plan_of_care_section = '<component>';
			$XML_plan_of_care_section .= '<section>';
			$XML_plan_of_care_section .= '<!-- ** Plan of Care Section Template -->';
			$XML_plan_of_care_section .= '<templateId root="2.16.840.1.113883.10.20.22.2.10"/>';
			$XML_plan_of_care_section .= '<!-- CCDA Plan of Care Section definition requires this code -->';
			$XML_plan_of_care_section .= '<code code="18776-5" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC"
											displayName="Treatment plan"/>';
			$XML_plan_of_care_section .= '<title>PLAN OF CARE</title>';
			$XML_plan_of_care_section .= ' <text>';
			$XML_plan_of_care_section .= '<table border = "1" width = "100%">
									<thead>
										<tr>
											<th>Name</th>
											<th>Result</th>
											<th>Status</th>
											<th>Date / Reason</th>
										</tr>
									</thead>
									<tbody>';
			$flag = 0;
			
			//------- BEGIN FUTURE APPOINTMENTS AND TESTS-------
			if(in_array('future_appointment',$arrOptions) || in_array('future_sch_test',$arrOptions) || in_array('provider_referrals',$arrOptions)){
				$current_date = date("Y-m-d");
				$current_time = date("H:i:s");
				
				$dos = $cmtDOSArr[$form_id];
				
				$rowTmpArr = $rowCste = array();
				foreach($csteRowArr[$pid] as $csteRow) {
					if($form_id != ""){
						if($csteRow["schedule_date"] >= $dos) {
							$rowTmpArr[$pid][] = $csteRow;	
						}
					}
				}
				if(count($rowTmpArr)>0) {
					$rowCste = 	$rowTmpArr[$pid];
				}else {
					$rowCste = 	$csteRowArr[$pid];
				}
				
				foreach($rowCste as $row) {
					
					if($row['appoint_test'] == "Test" && in_array('future_sch_test',$arrOptions)){
						$flag = 1;
						$XML_plan_of_care_section .= '<tr><td>'.htmlentities($row['test_type'])." : ".htmlentities($row['test_name']).'</td>
															<td>Future Sch Test</td>
															<td>';
						$XML_plan_of_care_section .=(preg_replace("/-/",'',$row['schedule_date'])>0)?date('M d,Y',strtotime($row['schedule_date'])):"";									
						$XML_plan_of_care_section .=" ".htmlentities($row['variation']).'</td>
															</tr>';
					}
					if($row['appoint_test'] == "Appointment" && in_array('future_appointment',$arrOptions)){
						$flag = 1;
						$XML_plan_of_care_section .= '<tr><td>'.htmlentities($row['reff_phy']).'</td>
															<td>Future Scheduled Appointment</td>
															<td>'.htmlentities($row['phy_address'])." ON ";
						$XML_plan_of_care_section .=(preg_replace("/-/",'',$row['schedule_date'])>0)?date('M d,Y',strtotime($row['schedule_date'])):"";									
						$XML_plan_of_care_section .=" ".htmlentities($row['variation']).'</td></tr>';
					}
					if($row['appoint_test'] == "Referral" && in_array('provider_referrals',$arrOptions)){
						$flag = 1;
						$XML_plan_of_care_section .= '<tr><td>'.htmlentities($row['reff_phy']).'</td>
															<td>Referral to other providers</td>
															<td>'.htmlentities($row['phy_address'])." ON ";
						$XML_plan_of_care_section .=(preg_replace("/-/",'',$row['schedule_date'])>0)?date('M d,Y',strtotime($row['schedule_date'])):"";									
						$XML_plan_of_care_section .=" ".$row['variation']." FOR ".htmlentities($row['reason']).'</td></tr>';
					}
				}
			}	
			//------- END FUTURE APPOINTMENTS AND TESTS----------
			
			//-------BEGIN DIAGNOSTICS TESTS PENDING --------------//
			if(in_array('diagnostic_tests_pending',$arrOptions)){
				
				$res = $radRowArr[$pid];
				foreach($res as $row) {	
					$flag = 1;
					$status = ($row['rad_status'] == 1) ? 'Pending' : 'Completed';
					$rad_results=trim(addslashes($row['rad_results']));
					$XML_plan_of_care_section .= '<tr><td ID="RAD_Result_'.$row['rad_test_data_id'].'"> RAD : '.htmlentities($row['rad_name']).' [LOINC:'.$row['rad_loinc'].']</td>
														<td>'.$rad_results.'</td>
														<td>'.$status.'</td>
														<td>';
						$XML_plan_of_care_section .=(preg_replace("/-/",'',$row['rad_order_date'])>0)?date('M d,Y',strtotime($row['rad_order_date'])):"";									
						$XML_plan_of_care_section .='</td>
														</tr>';
				}
				
				$res1 = $labRowArr[$pid];
				foreach($res1 as $row) {
					if($row['result_id'] == "" || $row['result_id'] == NULL){	
					$flag = 1;
					$XML_plan_of_care_section .= '<tr><td>LAB: '.htmlentities($row['service']).' [LOINC:'.$row['loinc'].']</td>
													<td></td>
													<td>Diagnostic Test pending</td>
													<td></td>
													</tr>';
					}
				}
			}
			//-------END DIAGNOSTICS TESTS PENDING --------------//
			
			//-------BEGIN RECOMMENDED PATIENT DECISION AIDS --------------//
			if(in_array('recommended_patient_decision_aids',$arrOptions)){
				
				$res = $dprFormRowArr[$pid][$form_id];
				foreach($res as $row) {
					$flag = 1;
					$XML_plan_of_care_section .= '<tr><td>'.htmlentities($row['name']).' SNOMED CT :'.$row['ccda_code'].'</td><td>Recommended Patient Decision Aids</td><td></td></tr>';
				}
			}
			//-------END RECOMMENDED PATIENT DECISION AIDS --------------//
			
			if($flag == 0)
			$XML_plan_of_care_section .= '<tr><td colspan="4"></td></tr>';
			$XML_plan_of_care_section .= '</tbody>';							
			$XML_plan_of_care_section .= '</table>';
			$XML_plan_of_care_section .= '</text>';
			
			//----BEGIN GOALS/ INSTRUCTION ENTRY-----------------
			if(in_array('mu_data_set_ap',$arrOptions) || in_array('clinical_instruc',$arrOptions)){
				
				$res = $osacndFormRowArr[$pat_id][$form_id];
				foreach($res as $row) {
					
					if($row['name'] != "" && $row['snowmed'] != ""){
					$XML_plan_of_care_entry ='<entry>
											<observation classCode = "OBS" moodCode = "GOL">
												<templateId root = "2.16.840.1.113883.10.20.22.4.44"/>
												<id nullFlavor="NI"/>
												<code code = "'.$row['snowmed'].'" codeSystem = "2.16.840.1.113883.6.96" displayName = "'.$row['name'].'"/>
												<statusCode code = "new"/>
												<effectiveTime>
													<center value = "'.str_replace("-","",$cmtDOSArr[$row['form_id']]).'"/>
												</effectiveTime>
											</observation>
										</entry>';
					$XML_plan_of_care_section .= $XML_plan_of_care_entry;	
					}
					if($row['inform'] != ""){
					$XML_plan_of_care_entry ='<entry>
												<act classCode = "ACT" moodCode = "INT">
													<templateId root = "2.16.840.1.113883.10.20.22.4.20"/>
													<code nullFlavor="NI"/>
													<text>
														<reference value = "#instructions_'.$row['order_set_associate_details_id'].'"/>
														'.htmlentities($row['inform']).'
													</text>
													<statusCode code = "completed"/>
												</act>
											</entry>';
					$XML_plan_of_care_section .= $XML_plan_of_care_entry;	
					}
				}
			}
			//----END GOALS/ INSTRUCTION ENTRY-----------------	
			
			//-------BEGIN FUTURE APPOINTMENT ENTRY-------------
			if(in_array('future_appointment',$arrOptions) || in_array('future_sch_test',$arrOptions)){
				$current_date = date("Y-m-d");
				$current_time = date("H:i:s");
				
				$sql = "SELECT * FROM chart_schedule_test_external WHERE patient_id = '".$pid."'";
				if($form_id != ""){
					$sql .= " AND schedule_date >= '".$dos."'";
				}else{
					$sql .= " AND schedule_date >= '".date('Y-m-d')."'";
				}
				$sql .= " AND deleted_by = '0'";
				$res = imw_query($sql);
				while($row = imw_fetch_assoc($res)){
					
					switch($row['test_type']){
						case "Imaging":
						$ccda_code = $row['snomed'];
						$codeSystem = "2.16.840.1.113883.6.96";
						$codeSystemName = "SNOMED -CT";
						break; 
						
						case "Lab":
						$ccda_code = $row['loinc'];
						$codeSystem = "2.16.840.1.113883.6.1";
						$codeSystemName = "LOINC";
						break;
						
						case "Procedure":
						$ccda_code = $row['cpt'];
						$codeSystem = "2.16.840.1.113883.6.12";
						$codeSystemName = "CPT";
						break;
					}
					
					if($row['appoint_test'] == "Test" && in_array('future_sch_test',$arrOptions)){
					$XML_plan_of_care_entry ='<entry typeCode="DRIV">
													<act moodCode = "RQO" classCode = "ACT">
													<templateId root = "2.16.840.1.113883.10.20.22.4.39"/>
													<id nullFlavor="NI"/>
													<code code = "'.$ccda_code.'" codeSystem = "'.$codeSystem.'" codeSystemName = "'.$codeSystemName.'" displayName = "'.$row['test_name'].'"/>
													<statusCode code = "new"/>
													<effectiveTime>
														<center value = "'.str_replace("-","",$row['schedule_date']).'"/>
													</effectiveTime>
													</act>
												</entry>';	
					}else if($row['user_type'] == "Appointment" && in_array('future_appointment',$arrOptions)){
						$XML_plan_of_care_entry ='<entry typeCode="DRIV">
													<act moodCode = "RQO" classCode = "ACT">
													<templateId root = "2.16.840.1.113883.10.20.22.4.39"/>
													<id nullFlavor="NI"/>
													<code nullFlavor="NI"/>
													<statusCode code = "new"/>
													<effectiveTime>
														<center value = "'.str_replace("-","",$row['schedule_date']).'"/>
													</effectiveTime>
													</act>
												</entry>';	
					}else if($row['user_type'] == "Referral" && in_array('provider_referrals',$arrOptions)){
						$XML_plan_of_care_entry ='<entry typeCode="DRIV">
													<act moodCode = "RQO" classCode = "ACT">
													<templateId root = "2.16.840.1.113883.10.20.22.4.39"/>
													<id nullFlavor="NI"/>
													<code nullFlavor="NI"/>
													<statusCode code = "new"/>
													<effectiveTime>
														<center value = "'.str_replace("-","",$row['schedule_date']).'"/>
													</effectiveTime>
													</act>
												</entry>';	
					}
					
					$XML_plan_of_care_section .= $XML_plan_of_care_entry;					
				}
			}
			//-------END FUTURE APPOINTMENT ENTRY-------------
			
			//-------BEGIN DIAGNOSTICS RAD TESTS PENDING --------------//
			if(in_array('diagnostic_tests_pending',$arrOptions)){
				
				$res = $radRowArr[$pid];
				foreach($res as $row) {	
					$XML_plan_of_care_entry ='<entry typeCode="DRIV">
														<observation classCode="OBS" moodCode="RQO">
														<templateId root="2.16.840.1.113883.10.20.22.4.44"/>
														<!-- Plan of Care Activity Observation template -->
														<id nullFlavor="NI"/>
														<code code="'.$row['rad_loinc'].'" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC"
														displayName="'.$row['rad_name'].'"/>
														<text>
															<reference value = "#RAD_Result_'.$row['rad_test_data_id'].'"/>
															'.htmlentities($row['rad_results']).'
														</text>
														<statusCode code="new"/>
														<effectiveTime nullFlavor="NI"/>
														</observation>
													</entry>';
					$XML_plan_of_care_section .= $XML_plan_of_care_entry;									
				}
			}
			//-------END DIAGNOSTICS RAD TESTS PENDING --------------//
			
			//-------BEGIN DIAGNOSTICS LAB TESTS PENDING --------------//
			if(in_array('diagnostic_tests_pending',$arrOptions)){

				$res = $labRowArr[$pid];
				foreach($res as $row) {
					if($row['result_id'] == "" || $row['result_id'] == NULL){
					$XML_plan_of_care_entry ='<entry typeCode="DRIV">
														<observation classCode="OBS" moodCode="RQO">
														<templateId root="2.16.840.1.113883.10.20.22.4.44"/>
														<!-- Plan of Care Activity Observation template -->
														<id nullFlavor="NI"/>
														<code code="'.$row['loinc'].'" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC"
														displayName="'.$row['service'].'"/>
														<statusCode code="new"/>
														<effectiveTime nullFlavor="NI"/>
														</observation>
													</entry>';
					$XML_plan_of_care_section .= $XML_plan_of_care_entry;	
					}
				}
			}
			//-------END DIAGNOSTICS RAD TESTS PENDING --------------//
			
			//----BEGIN RECOMMENDED PATIENT DECISION AIDS-----------//
			if(in_array('recommended_patient_decision_aids',$arrOptions)){
				$res = $dprFormRowArr[$pid][$form_id];
				foreach($res as $row) {
				$XML_plan_of_care_entry ='<entry typeCode="DRIV">
											<supply moodCode="INT" classCode="SPLY">
											<templateId root="2.16.840.1.113883.10.20.22.4.43"/>
											<!-- ** Plan of Care Activity Supply ** -->
											<id nullFlavor="NI"/>
											<code xsi:type="CE" code="'.$row['ccda_code'].'" codeSystem="2.16.840.1.113883.6.96"
												displayName="'.$row['name'].'"/>
											</supply>
										</entry>';
				}
			}
			$XML_plan_of_care_section .= $XML_plan_of_care_entry;										
			//----END RECOMMENDED PATIENT DECISION AIDS-----------//
			
			$XML_plan_of_care_section .= '</section>';
			$XML_plan_of_care_section .= '</component>';
	}
	/* END PLAN OF CARE */

	/* BEGIN FUNCTIONAL STATUS SECTION */
	$row = $phsRowArr[$pid]['functional'];
	if($form_id != '') {
		$row = $phsFormRowArr[$pid][$form_id]['functional'];
	}
	
	$XML_functional_status_section = '
	<!--	********************************************************
		FUNCTIONAL STATUS
		******************************************************** -->
		<component>
			<section>
				<templateId root="2.16.840.1.113883.10.20.22.2.14" extension="2014-06-09"/>
				<templateId root="2.16.840.1.113883.10.20.22.2.14"/>
				<!--  ******** Functional status section template   ******** -->
				<code code="47420-5" codeSystem="2.16.840.1.113883.6.1"/>
				<title>FUNCTIONAL STATUS</title>
				<text>
				<table border = "1" width = "100%">
				';

	if(count($row)>0){	
		$XML_functional_status_section .= '
		<thead>
			<tr>
				<th>Functional Condition</th>
				<th>Code/Code System</th>
				<th>Status Date</th>
			</tr>
		</thead>
		<tbody>
		';
		//$row = imw_fetch_assoc($res);	
		$XML_functional_status_section .= '
		<tr>
			<td>'.$row['status_text'].'</td>
			<td>'.$row['ccd_code'].'<br/>'.$row['ccd_code_system'].'</td>
			<td>'.date('M d,Y',strtotime($row['status_date'])).'</td>
		</tr>
		';
		$XML_functional_status_section .= '
			</tbody>
		';
	}else{
		$XML_functional_status_section .= '<tbody><tr><td>No informtion</td></tr></tbody>';
	}
	$XML_functional_status_section .= '
		</table>
	</text>
	';
	$XML_functional_status_section .= '
	</section>
	</component>
	';
	/* END FUNCTIONAL STATUS SECTION */

	/* BEGIN COGNITIVE STATUS SECTION */

	$row = $phsRowArr[$pid]['cognitive'];
	if($form_id != '') {
		$row = $phsFormRowArr[$pid][$form_id]['cognitive'];
	}
	$XML_functional_status_section .= '
	<!--	********************************************************
		COGNITIVE STATUS
		******************************************************** -->
		<component>
			<section>
				<!-- There is no R1.1 version of this template -db -->
				<templateId root="2.16.840.1.113883.10.20.22.2.56" extension="2015-08-01" />
				<!-- Mental Status Section -->
				<code code="10190-7" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="MENTAL STATUS" />
				<title>COGNITIVE STATUS (MENTAL STATUS)</title>
				<text>
				<table border = "1" width = "100%">
				';

	if(count($row)>0){	
		$XML_functional_status_section .= '
		<thead>
			<tr>
				<th>COGNITIVE STATUS</th>
				<th>Code/Code System</th>
				<th>Status Date</th>
			</tr>
		</thead>
		<tbody>
		';
		
		$XML_functional_status_section .= '
		<tr>
			<td>'.$row['status_text'].'</td>
			<td>'.$row['ccd_code'].'<br/>'.$row['ccd_code_system'].'</td>
			<td>'.date('M d,Y',strtotime($row['status_date'])).'</td>
		</tr>
		';
		$XML_functional_status_section .= '
			</tbody>
		';
	}else{
		$XML_functional_status_section .= '<tbody><tr><td>No informtion</td></tr></tbody>';
	}
	$XML_functional_status_section .= '
		</table>
	</text>
	';
	$XML_functional_status_section .= '
	</section>
	</component>
	';
	/* END COGNITIVE STATUS SECTION */


	/* BEGIN PROCEDURES SECTION */
	if(in_array('mu_data_set_superbill',$arrOptions)){	
			$flag = 0;
			$XML_procedures_section = '<component>
										<section>
											<!-- Procedures section template -->
											<templateId root="2.16.840.1.113883.10.20.22.2.7.1" extension="2014-06-09"/>
											<templateId root="2.16.840.1.113883.10.20.22.2.7.1"/>
											<code code="47519-4" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="HISTORY OF PROCEDURES"/>
											<title>PROCEDURES</title>
											<text>
											<table border = "1" width = "100%">
												<thead>
													<tr>
														<th>Name</th>
														
														<th>Date</th>
														<th>Provider</th>
													</tr>
												</thead>
												<tbody>
												';
			
			$res_sx = $listRowArr[$listPtId];
			foreach($res_sx as $row_sx) {
				$flag = 1;
				$XML_procedures_section .= '<tr>
											<td ID = "procedure_sx_'.$row_sx['id'].'">'.htmlentities($row_sx['title']).'</td>
											<td ID = "date_sx_'.$row_sx['id'].'">';
				$XML_procedures_section .=(preg_replace("/-/",'',$row_sx['begdate'])>0)?date('M d,Y',strtotime($row_sx['begdate'])):"";
				$XML_procedures_section .='</td>
											<td >'.$row_sx['referredby'].'</td>
											</tr>';
			}
			
			if($flag == 0){
				$XML_procedures_section .= '<tr><td>No Procedures.</td></tr>';	
			}
			$XML_procedures_section .= '		</tbody>';
			$XML_procedures_section .= '		</table>';
			$XML_procedures_section .= '		</text>';
			
			foreach($res_sx as $row_sx) {
				$flag = 1;
			$XML_procedures_entry = '<entry typeCode="DRIV">
										<procedure classCode="PROC" moodCode="EVN">
										<!-- Procedure  Activity Procedure Template -->
										<templateId root="2.16.840.1.113883.10.20.22.4.14" extension="2014-06-09"/>
										<templateId root="2.16.840.1.113883.10.20.22.4.14"/>
										<id nullFlavor="NI"/>
										<code code="'.$row_sx['ccda_code'].'" codeSystem="2.16.840.1.113883.6.96"	displayName="'.$row_sx['title'].'" codeSystemName="SNOMED CT"></code>
										<statusCode code="completed"/>
										';
			if($row_sx['begdate'] !="" && preg_replace("/-/","",$row_sx['begdate'])>0){
				$XML_procedures_entry .= '		<effectiveTime value="'.preg_replace("/-/","",$row_sx['begdate']).'"/>
				';
			}else
				$XML_procedures_entry .= '		<effectiveTime nullFlavor="NI"/>
			';
			
			$row_provider = $refRowArr[$row_sx['referredby_id']];
			
			
			if(count($row_provider) > 0){
				
				$XML_procedures_entry .= '		<performer>
													<assignedEntity>
												<!-- NPI 34567 -->
												';
				if($row_provider['NPI']!=""){
					$XML_procedures_entry .= '<id extension="'.$row_provider['NPI'].'" root="2.16.840.1.113883.4.6"/>
					';
				}else{
					$XML_procedures_entry .= '				<id nullFlavor="NI"/>
					';
				}
				$XML_procedures_entry .= '<addr>
				';
				if($row_provider['Address1'] != "")
					$XML_procedures_entry .= '					<streetAddressLine>'.$row_provider['Address1'].'</streetAddressLine>
					';
				if($row_provider['City'] != "")
					$XML_procedures_entry .= '					<city>'.$row_provider['City'].'</city>
					';
				if($row_provider['State'] != "")
					$XML_procedures_entry .= '					<state>'.$row_provider['State'].'</state>
					';
				if($row_provider['postal_code'] != "")
					$XML_procedures_entry .= '					<postalCode>'.$row_provider['postal_code'].'</postalCode>
					';
				
				$XML_procedures_entry .= '					<country>US</country>';
				$XML_procedures_entry .= '				</addr>';
			
				if($row_provider['physician_phone'] != ""){
					$XML_procedures_entry .= '	<telecom use="WP" value="tel:+1-'.core_phone_format($row_provider['physician_phone']).'"/>
					';
				}else{
					$XML_procedures_entry .= '	<telecom nullFlavor="NI"/>
					';
				}
				
				$XML_procedures_entry .= '				<assignedPerson>';
				$XML_procedures_entry .= '					<name>';
				$XML_procedures_entry .= '						<given>'.$row_provider['FirstName'].'</given>';
				$XML_procedures_entry .= '						<family>'.$row_provider['LastName'].'</family>';
				$XML_procedures_entry .= '					</name>';
				$XML_procedures_entry .= '				</assignedPerson>';
				$XML_procedures_entry .= '			</assignedEntity>';
				$XML_procedures_entry .= '		</performer>';
			}
			/********UDI data new block ************/
			$udi_for_this_proc = getUDIprocWise($row_sx['id']);
			if($udi_for_this_proc){
				foreach($udi_for_this_proc as $UDI_rs){
					$XML_procedures_entry .= '
					<participant typeCode="DEV">
						<participantRole classCode="MANU">
							<!-- ** Product instance ** -->
							<templateId root="2.16.840.1.113883.10.20.22.4.37"/>
							<id assigningAuthorityName="FDA" extension="'.$UDI_rs['title'].'" root="2.16.840.1.113883.3.3719"/>
							<playingDevice>
								<!-- the actual UDI device -db -->
								<code nullFlavor="UNK">
									<originalText>
										<reference value="#'.$row_sx['id'].'"/>
									</originalText>
							</code>
								
							</playingDevice>
							<!-- FDA Scoping Entity OID for UDI-db -->
							<scopingEntity>
								<id root="2.16.840.1.113883.3.3719" extension="'.$UDI_rs['title'].'"/>
							</scopingEntity>
						</participantRole>
					</participant>
					';
				}			
			}
			
			/********END OF UDI block **************/
				$XML_procedures_entry .= '	</procedure>';
				$XML_procedures_entry .= '	</entry>';
				$XML_procedures_section .= $XML_procedures_entry;
			}
			
			if($flag == 0){
			$XML_procedures_entry = '	<entry>';
			$XML_procedures_entry .= '	<procedure classCode="PROC" moodCode="EVN">';
			$XML_procedures_entry .= '		<!-- Procedure  Activity Procedure Template -->';
			$XML_procedures_entry .= '		<templateId root="2.16.840.1.113883.10.20.22.4.14"/>';
			$XML_procedures_entry .= '		<id nullFlavor="NI"/>';
			$XML_procedures_entry .= '		<code nullFlavor="NI"/>';
			$XML_procedures_entry .= '		<statusCode code="completed"/>';
			$XML_procedures_entry .= '		<effectiveTime nullFlavor="NI"/>';
			$XML_procedures_entry .= '	</procedure>';
			$XML_procedures_entry .= '	</entry>';
			$XML_procedures_section .= $XML_procedures_entry;
			}
			
			$XML_procedures_section .= '</section>';
			$XML_procedures_section .= '</component>';
	}
	/* END PROCEDURES SECTION */

	/* BEGIN CHIEF COMPLAINT SECTION */
	if(in_array('reason_for_visit',$arrOptions)){
			
			$row = $clchFormRowArr[$pid][$form_id];
			
			$XML_chief_complaint_section = '<component>
					<section>
						<templateId root = "2.16.840.1.113883.10.20.22.2.13"/>
						<code code = "46239-0" codeSystem = "2.16.840.1.113883.6.1" codeSystemName = "LOINC" displayName = "CHIEF COMPLAINT AND REASON FOR VISIT"/>
						<title>CHIEF COMPLAINT</title>
						<text>';
		if($row['ccompliant'] != ""){				
		$XML_chief_complaint_section .= '<table border = "1" width = "100%">
								<thead>
									<tr>
										<th>Reason for Visit/Chief Complaint</th>
									</tr>
								</thead>';
							
			$XML_chief_complaint_section .= '<tbody>
									<tr>
										<td>'.htmlentities($row['ccompliant']).'</td>
									</tr>
								</tbody>';
			
			$XML_chief_complaint_section .= '</table>';
			}
			$XML_chief_complaint_section .= '</text>
					</section>
				</component>';
	}
	/* END CHIEF COMPLAINT SECTION */	

	/* BEGIN REASON FOR REFERRAL SECTION */
	if( in_array('provider_referrals',$arrOptions) )
	{

		$row = $capRowArr[$pid];
		if(trim($row['consult_reason']) != ''){
			$XML_reason_for_referral = '
									<component>
										<section>
											<templateId root="1.3.6.1.4.1.19376.1.5.3.1.3.1" extension="2014-06-09"/>
											<templateId root="1.3.6.1.4.1.19376.1.5.3.1.3.1"/>
											<!-- ** Reason for Referral Section Template ** -->
											<code codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" code="42349-1"
												displayName="REASON FOR REFERRAL"/>
											<title>REASON FOR REFERRAL</title>
											<text>
												<paragraph>'.htmlentities($row['consult_reason']).'</paragraph>
											</text>
										</section>
									</component>
									';
		}else{
			$XML_reason_for_referral = '
								<component>				
									<section nullFlavor="NI">
										<!-- Reason for Referral Section (V2) -->
										<templateId root="1.3.6.1.4.1.19376.1.5.3.1.3.1" extension="2014-06-09"/>
										<templateId root="1.3.6.1.4.1.19376.1.5.3.1.3.1"/>
										<code code="42349-1" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="Reason for Referral"/>
										<title>REASON FOR REFERRAL</title>
										<text>No Reason for Referral information</text>
									</section>
								</component>
								';
		}
		$rn_information_recipient = '';
		if($ccd_type=='rn'){
			$info_recipi_doc = trim($row['doctor_name']);
			if($info_recipi_doc != ''){
				$temp_info_recip_arr = explode(',',$info_recipi_doc);
				$infoRecipi_prefix = 'Dr';
				$infoRecipi_lname = $temp_info_recip_arr[0];
				$infoRecipi_fname = $temp_info_recip_arr[1];
				$infoRecipi_fname_temp = explode(' ',trim($infoRecipi_fname));
				if(count($infoRecipi_fname_temp)>1){
					$infoRecipi_fname  = $infoRecipi_fname_temp[0];
					$infoRecipi_prefix = $infoRecipi_fname_temp[1];
				}
				/*****INFORMATION RECIPIENT******/
				$rn_information_recipient = '
				<!-- INFORMATION RECIPIENT INFORMATION -->
				<!-- The informationRecipient represents the intended recipient of the document -->
				<informationRecipient>
					<intendedRecipient>
						<informationRecipient>
							<name>
								<prefix>'.trim($infoRecipi_prefix).'</prefix>
								<given>'.trim($infoRecipi_fname).'</given>
								<family>'.trim($infoRecipi_lname).'</family>
							</name>
						</informationRecipient>
						<receivedOrganization>
							<name>Neighborhood Physicians Practice</name>
						</receivedOrganization>
					</intendedRecipient>
				</informationRecipient>
				
				';
			}
		}
	}
	/* END REASON FOR REFERRAL SECTION */	

	/* BEGIN XML BODY */
	$XML_cda_body = '<component>';
	$XML_cda_body .= '<structuredBody>';
		
	
	if(in_array('mu_data_set_smoking',$arrOptions)){
	$XML_cda_body .= '<!-- SOCIAL HISTORY SECTION -->';
	$XML_cda_body .= $XML_social_history_section;		   // INCLUDES SMOKING STATUS
	}
	
	if(in_array('mu_data_set_medications',$arrOptions)){
	$XML_cda_body .= '<!-- MEDICATIONS SECTION -->';
	$XML_cda_body .= $XML_medication_section;
	}
	
	if(in_array('mu_data_set_allergies',$arrOptions)){
	$XML_cda_body .= '<!-- ALLERGIES SECTION -->';
	$XML_cda_body .= $XML_allergies_section;
	}
	
	$XML_cda_body .= '<!-- IMMUNIZATION SECTION -->';
	$XML_cda_body .= $XML_immunization_section;
	
	if(in_array('mu_data_set_vs',$arrOptions)){
	$XML_cda_body .= '<!-- VITAL SIGN SECTION -->';
	$XML_cda_body .= $XML_vital_section;
	}
	
	if(in_array('mu_data_set_problem_list',$arrOptions)){
	$XML_cda_body .= '<!-- PROBLEM SECTION -->';
	$XML_cda_body .= $XML_problem_section;
	}

	if(in_array('mu_data_set_lab',$arrOptions)){
	$XML_cda_body .= '<!-- LAB TESTS SECTION -->';
	$XML_cda_body .= $XML_results_section;				   // INCLUDES LAB RESULTS
	}
	
	
	$XML_cda_body .= '
	<!-- ASSESSMENT SECTION -->
	';	
	$XML_cda_body .= $XML_assessment_section;
	
	$XML_cda_body .= '
	<!-- GOALS SECTION -->
	';	
	$XML_cda_body .= $XML_goals_section;
	
	$XML_cda_body .= '
	<!-- HEALTH CONCERNS SECTION -->
	';	
	$XML_cda_body .= $finalHealthConcern;
	
	if(isset($XML_encouters_section) && $XML_encouters_section!=''){	
		$XML_cda_body .= '
		<!-- ENCOUNTERS SECTION -->
		';
		$XML_cda_body .= $XML_encouters_section;				// INCLUDES PROBLEMS
	}
	
	if(in_array('mu_data_set_ap',$arrOptions) || in_array('future_appointment',$arrOptions) || in_array('future_sch_test',$arrOptions) || in_array('clinical_instruc',$arrOptions) || in_array('recommended_patient_decision_aids',$arrOptions)){
	$XML_cda_body .= '<!-- PLAN OF CARE SECTION -->';
	$XML_cda_body .= $XML_plan_of_care_section;				// INCLUDES CHART ASSESSMENTS
	}
	
	
	if(in_array('mu_data_set_ap',$arrOptions) || in_array('clinical_instruc',$arrOptions)){
	$XML_cda_body .= '<!-- INSTRICTIONS SECTION -->';      // INCLUDED STATIC
	$XML_cda_body .= $XML_instructions_section;
	}
	
	$XML_cda_body .= '<!-- FUNCTIONAL STATUS SECTION -->';  // INCLUDED STATIC
	$XML_cda_body .= $XML_functional_status_section;
	
	if(in_array('reason_for_visit',$arrOptions)){
	$XML_cda_body .= '<!-- CHIEF COMPLAINT AND REASON FOR VISIT SECTION -->';  // INCLUDED STATIC
	$XML_cda_body .= $XML_chief_complaint_section;
	}
	
	if(in_array('visit_medication_immu',$arrOptions)){
	$XML_cda_body .= '<!-- MEDICATIONS ADMINISTERED SECTION -->';  // INCLUDED STATIC
	$XML_cda_body .= $XML_medication_admin_section;
	}
	
	if(in_array('mu_data_set_superbill',$arrOptions)){	
	$XML_cda_body .= '<!-- PROCEDURES SECTION -->';   // INCLUDED STATIC
	$XML_cda_body .= $XML_procedures_section;
	}
	if(in_array('provider_referrals',$arrOptions)){
	$XML_cda_body .= '<!-- REASON FOR REFERRAL SECTION -->';   // INCLUDED STATIC
	$XML_cda_body .= $XML_reason_for_referral;
	}
	
	$XML_cda_body .= '</structuredBody>';
	$XML_cda_body .= '</component>';
	/* END XML BODY */

	/* <?xml-stylesheet type="text/xsl" href="CDA.xsl"?> */

	$xml = '<?xml version="1.0" encoding="UTF-8"?>
	<!--
	Title: Continuity of Care Document (CCD).
	-->
	<ClinicalDocument xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
		xmlns="urn:hl7-org:v3" 
		xmlns:voc="urn:hl7-org:v3/voc" 
		xmlns:sdtc="urn:hl7-org:sdtc">
	<realmCode code="US"/>
	<typeId extension="POCD_HD000040" root="2.16.840.1.113883.1.3"/>
	<!-- indicates conformance with US Realm Clinical Document Header template -->
	<templateId root="2.16.840.1.113883.10.20.22.1.1" extension="2015-08-01"/>
	<templateId root="2.16.840.1.113883.10.20.22.1.1"/>
	';
	if($ccd_type == 'rn'){
		$xml .= '
		<!-- Referral Note (V2) template ID -->
		<templateId root="2.16.840.1.113883.10.20.22.1.14" extension="2015-08-01"/>
		<templateId root="2.16.840.1.113883.10.20.22.1.14"/>
	';
	}else{
		$xml .= '
	<!-- conforms to CCD Template(V2) ID requirements -->
	<templateId root="2.16.840.1.113883.10.20.22.1.2" extension="2015-08-01"/>
	<templateId root="2.16.840.1.113883.10.20.22.1.2"/>
	';
		
	}
	$xml .= '
	<!-- UNIQUE DOCUMENT IDENTIFIER -->
	<id extension="Test CCDA" root="1.1.1.1.1.1.1.1.1"/>
	';
	if($ccd_type == 'rn'){
		$xml .= '
	<code code="57133-1" displayName="Referral Note" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC"/>
	';
	}else{
		$xml .= '
	<code code="34133-9" displayName="Summarization of patient data" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC"/>
	';
	}
	$xml .= '
	<title>HEALTH HISTORY &amp; PHYSICAL</title>
	<effectiveTime value="'.$currentDate.'"/>
	<confidentialityCode code="N" codeSystem="2.16.840.1.113883.5.25"/>
	<languageCode code="en-US"/>';

	$xml .= $XMLpatient_data;
	$xml .= $XML_author_data;
	$xml .= $XML_data_enterer_data;

	$xml .= $XML_custodian_data;

	if($ccd_type=='rn'){
		$xml .= $rn_information_recipient;
	}
	if(in_array('mu_data_set_care_team_members',$arrOptions) || in_array('provider_info',$arrOptions))
	$xml .= $XML_documentationof_data; // CARE TEAM MEMBERS

	if(in_array('provider_referrals',$arrOptions) && $form_id != ""){
	$xml .= $XML_referral_to_providers;
	}

	$xml .= $XML_cda_body;
	$xml .= '</ClinicalDocument>';

	$XML_file_name = $verasityDataDirectory.'/'.$pat_id.'-'.$form_id.'-'.$currentDatePlain.'.xml';
	file_put_contents($XML_file_name,$xml);
}
