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
?>
<?php
/*
FILE : scheduler_new_report_result.php
PURPOSE : Display result for scheduler report
ACCESS TYPE : Direct
*/
ini_set("memory_limit","2048M");
set_time_limit (300);

$dateFormat= get_sql_date_format();
$phpDateFormat = phpDateFormat();
$curDate = date($phpDateFormat.' h:i A');		

$FCName= $_SESSION['authId'];

$page_data = NULL;
$pdf_data = NULL;
$curDate = date($phpDateFormat);
if($Start_date == ""){
	$Start_date = $curDate;
	$End_date = $curDate;
}
$printPdFBtn  = 0;	
$csvBtn  = 0;	
if($_POST['form_submitted']){
	//DATE RANGE ARRAY WEEKLY/MONTHLY/QUARTERLY
	$arrDateRange= $CLSCommonFunction->changeDateSelection();
	if($dayReport=='Daily'){
		$Start_date = $End_date= date($phpDateFormat);
	}else if($dayReport=='Weekly'){
		$Start_date = $arrDateRange['WEEK_DATE'];
		$End_date= date($phpDateFormat);
	}else if($dayReport=='Monthly'){
		$Start_date = $arrDateRange['MONTH_DATE'];
		$End_date= date($phpDateFormat);
	}else if($dayReport=='Quarterly'){
		$Start_date = $arrDateRange['QUARTER_DATE_START'];
		$End_date = $arrDateRange['QUARTER_DATE_END'];
	}
	//---------------------


	//VARIABLE DECLARATION
	$join_query=$where_query='';	
	
	$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
	$createdBy = ucfirst(trim($op_name_arr[1][0]));
	$createdBy .= ucfirst(trim($op_name_arr[0][0]));

	//SET ARRAY
	if(empty($_REQUEST['facility_name'])==false){
		$facility_name=array_combine($_REQUEST['facility_name'],$_REQUEST['facility_name']);
	}
	//FACILITY
	$facility_name_str = join(',',$facility_name);
	//PROCEDURE
	$rqArrProcedures = $_REQUEST['procedures'];
	$rqProcedures = join(',',$rqArrProcedures);
	//PHYSICIAN
	$rqArrPhyId = $_REQUEST['phyId'];
	$rqPhyId = join(',',$rqArrPhyId);
	//INS TYPE
	$rqArrInsType = $_REQUEST['ins_type'];
	foreach ($rqArrInsType as $key => $value) {
		$rqArrInsType[$key] = "'".$value."'";
	}
	$rqInsType = join(',',$rqArrInsType);
	//INSURANCE
	$rqArrInsProvider = $_REQUEST['insId'];	
	$rqInsProvider = join(',',$rqArrInsProvider);
	//ICD10
	$Dxcodes = $_REQUEST['Dxcode10'];	
	if(sizeof($Dxcodes)>0){
		$rqDxcode10=implode(',',$Dxcodes);
		$rqDxcode10= "'".str_replace(",", "','", $rqDxcode10)."'";
	}	
	//APPT STATUS
	$rqArrAppStatus = $_REQUEST['ap_status'];
	$rqAppStatus = join(',',$rqArrAppStatus);

	//OPERATOR
	$operator_id=array_combine($operator_id,$operator_id);
	$operator_id_str = join(',',$operator_id);	

	//ARRAY MAP FOR PREFFERED PHONE
	$arrMapPreferredPhone=array('0'=>'Home Phone','1'=>'Work Phone','2'=>'Mobile Phone');

/*	$display_fields=array();
	if(empty($_REQUEST['display_fields'])==false){
		$display_fields=array_combine($_REQUEST['display_fields'],$_REQUEST['display_fields']);
	}	
*/	
	//DATE FORMAT
	$StartDate = getDateFormatDB($Start_date);
	$EndDate = getDateFormatDB($End_date);
	
	if(empty($rqHeard) === true && trim($_REQUEST["inc_ins_id"]) != 1){
		$td_width = '104px';
		$cnt_td_width = '100px';
	}else{
		$td_width = '100px';
		$cnt_td_width = '80px';
	}
	
	//COLUMNS NUMBERS
	$pro_cols = 7;
	$pro_cols_pdf=5;
	//if(empty($rqHeard) === false){
		$td_width = '50px';
	//}
	//if(trim($_REQUEST["inc_ins_id"]) == 1){

	//}	
	
	if($inc_appt_detail==1){$pro_cols+=3; $pro_cols_pdf+=3;}
	if($inc_demographics==1){$pro_cols += 10;}
	if($inc_recalls==1){$pro_cols += 1;}
	if($inc_appt_status==1){$pro_cols += 5;}
	if($inc_portal_key==1){$pro_cols += 1; $pro_cols_pdf+=1;}
	if($inc_insurance==1){$pro_cols += 2;}
	if($inc_appt_made==1){$pro_cols += 2; $pro_cols_pdf+=2;}
	if($date_made==1){$pro_cols += 2; $pro_cols_pdf+=1;}	
	if($inc_pcp==1){$pro_cols += 1;}
	if($inc_ref_phy==1){$pro_cols += 1;}

	$no_of_cols = ($pro_cols - 2);
	$no_of_cols=$no_of_cols-1;
	
	$page_width=1050-23;
	$td_width =	( $page_width / $no_of_cols );
	$tdwidth = $td_width;
	
	$tdwidth_pdf =	floor( $page_width / $pro_cols_pdf);
	$pro_cols_pdf+=1;
	
	//ALL FACILITY
	$arrFacOfPracCode=array();
	$arrAllFacilities=array();
	$arrSelPosFacilities=array();
	$qry = "Select id, name, fac_prac_code FROM facility";
	$qryRs = imw_query($qry);
	while($qryRes  =imw_fetch_assoc($qryRs)){
		$id = $qryRes['id'];
		$name = $qryRes['name'];
		$arrAllFacilities[$id] = $name;
		
		//IF FACILITY SELECTED IN SEARCH CRITERIA
		if(empty($facility_name_str)==false && $facility_name[$id]){
			$arrSelPosFacilities[$qryRes['fac_prac_code']]=$qryRes['fac_prac_code'];
		}
	}
	if(sizeof($arrSelPosFacilities)>0){
		$strSelPosFacilities=implode(',', $arrSelPosFacilities);
	}

	//--- GET ALL PROVIDER NAME ----
	$providerRs = imw_query("Select id,fname,mname,lname,username from users");
	$providerNameArr = array();
	$arrUsersUName=array();
	$strUsersUName='';
	while($providerResArr = imw_fetch_assoc($providerRs)){
		$id = $providerResArr['id'];
		$name= core_name_format($providerResArr['lname'], $providerResArr['fname'], $providerResArr['mname']);;
		$providerNameArr[$id] = $name;
		
		// two character array
		$operatorInitial = substr($providerResArr['fname'],0,1);
		$operatorInitial .= substr($providerResArr['lname'],0,1);
		$userNameTwoCharArr[$id] = strtoupper($operatorInitial);
		
		$arrAllUsersUName[$providerResArr['username']] = $id;
		if($operator_id[$id]){
			$arrSelUsersUName[$providerResArr['username']] = $providerResArr['username'];
		}
	}
	if(sizeof($arrSelUsersUName)>0){
		$strSelUsersUName = "'".implode("','", $arrSelUsersUName)."'";
	}

	//--- GET PATIENT REFERRING PHYSICIAN DETAILS ----
	$pt_ref_phy_arr = array();
	$qryPtRef="Select physician_Reffer_id as refphyId, Title as refPhyTitle, FirstName as refphyFName,LastName as refphyLName, 
	MiddleName as refPhyMname FROM refferphysician order by refphyLName asc";
	$rs=imw_query($qryPtRef);	
	while($res = imw_fetch_array($rs)){
		$pt_phy_name_arr = array();
		$refphyId = $res['refphyId'];
		$pt_phy_name_arr["LAST_NAME"] = $res['refphyLName'];
		$pt_phy_name_arr["FIRST_NAME"] = $res['refphyFName'];
		$pt_phy_name_arr["MIDDLE_NAME"] = $res['refPhyMname'];
		$pt_phy_name = changeNameFormat($pt_phy_name_arr);
		$pt_ref_phy_arr[$refphyId] = $pt_phy_name;
	}unset($rs);

	//ALL POS FACILITIES
	$arrAllPosFacilities=array();
	if($_REQUEST['registered_fac']==1){
		$arrAllFacilities[0] = 'No Facility';
		$qry = "Select pos_facilityies_tbl.facilityPracCode as name,
			pos_facilityies_tbl.pos_facility_id as id,
			pos_tbl.pos_prac_code
			from pos_facilityies_tbl
			left join pos_tbl on pos_tbl.pos_id = pos_facilityies_tbl.pos_id
			order by pos_facilityies_tbl.headquarter desc,
			pos_facilityies_tbl.facilityPracCode";
		$qryRs = imw_query($qry);
		while($qryRes  =imw_fetch_assoc($qryRs)){
			$id = $qryRes['id'];
			$name = $qryRes['name'];
			$pos_prac_code = $qryRes['pos_prac_code'];
			$arrAllPosFacilities[$id] = $name.' - '.$pos_prac_code;
		}
	}
	
	//GET ALL INSURANCE COMAPNIES
	$arrAllInsCompanies[0]='No Insurance';
	$arrAllInsCompanies['SELF PAY']='SELF PAY';
	$qry="Select id as insCompId,in_house_code as insCompINHouseCode,name as insCompName FROM insurance_companies";
	$rs=imw_query($qry);
	while($res=imw_fetch_assoc($rs)){
		$id = $res['insCompId'];
		$insName = $ers['insCompINHouseCode'];
		if(trim($insName) == ''){
			$insName = substr($res['insCompName'],0,20);
		}
		
		$arrAllInsCompanies[$id] = $insName;
	}

	
	//IF MORNING/AFTERNOON SELECTED
	if($_REQUEST['day']=='morning' || $_REQUEST['day']=='afternoon'){
		$join_query.=' LEFT JOIN schedule_templates st ON st.id = appt.sch_template_id';

		if($_REQUEST['day'] == "morning"){
			$where_query .= " AND IF(st.fldLunchStTm, (TIME_FORMAT(appt.sa_app_starttime,'%H:%i:%s') < DATE_FORMAT(st.fldLunchStTm,'%H:%i:%s')), (TIME_FORMAT(appt.sa_app_starttime,'%H:%i:%s') < '12:00:00') ) ";
		}
		if($_REQUEST['day'] == "afternoon"){
			$where_query .= " AND IF(st.fldLunchStTm, (TIME_FORMAT(appt.sa_app_starttime,'%H:%i:%s') >= DATE_FORMAT(st.fldLunchStTm,'%H:%i:%s')), (TIME_FORMAT(appt.sa_app_starttime,'%H:%i:%s') >= '12:00:00') ) ";
		}
	}
	
	//IF REGISTERED SELECTED THEN SELECT ACCORDINGLY AND SHOW REGISTERED FACILITY.
	//Pt Portal Key Made
	$posFacPatIds= $arrPtPotalKay = array();
	$strPosFacPatIds='';
	if(($_REQUEST['registered_fac']==1 && empty($strSelPosFacilities)==false) || $inc_portal_key==1 ){
		$whereCondition = "";
		if(!$inc_portal_key) {
			$whereCondition = "WHERE default_facility IN(".$strSelPosFacilities.")"; 
		}
		$qry="Select id, temp_key FROM patient_data $whereCondition";
		$rs = imw_query($qry);
		while($res= imw_fetch_assoc($rs)){
			$arrPosFacPatIds[$res['id']] = "'".$res['id']."'";
			$arrPtPotalKay[$res['id']] = $res['temp_key'];
		}
		$strPosFacPatIds=implode(',', $arrPosFacPatIds);
	}
	
	$ins_policy_inc = "";
	if($inc_insurance==1){
		$ins_policy_inc = ", insd.policy_number as ins_policy_no, insd.provider as ins_id, insd.type";	
		$where_query .= " AND (date_format(insd.effective_date,'%Y-%m-%d')<='$StartDate' and (date_format(insd.expiration_date,'%Y-%m-%d')>='$EndDate' or  date_format(insd.expiration_date,'%Y-%m-%d')='0000-00-00'))";
	}

	//GETTING ALL SLOT PROCEDURE NAMES
	$arrAllSlotProc=array();
	$qry="Select id, proc FROM slot_procedures WHERE source=''";
	$rs= imw_query($qry);
	while($res=imw_fetch_assoc($rs)){	
		$arrAllSlotProc[$res['id']]=$res['proc'];
	}

	//GETTING MADE BY
	$prevStatusInfo=array();
	$arrConditionSchIds=array();
	if($date_made=='1' || $inc_appt_made=='1'){
		$qry="SELECT sch_id, status_date, oldMadeBy, DATE_FORMAT( status_date,  '".$dateFormat."' ) AS prv_status_date FROM previous_status 
		GROUP BY sch_id
		HAVING status_date BETWEEN '$StartDate'	AND '$EndDate'
		AND MIN( status_date ) >=  '$StartDate'";
		if(empty($strSelUsersUName)==false){
			$qry.=" AND oldMadeBy IN(".$strSelUsersUName.")";
		}
		$rs = imw_query($qry);
		while($res= imw_fetch_assoc($rs)){
			$prevStatusInfo[$res['sch_id']]=$res;
			$arrConditionSchIds[$res['sch_id']]=$res['sch_id'];
		}
	}

	//IF CANCEL REASON IS SELECTED	
	if(empty($cancel_reason)==false){
		$str_sch_ids='';
		if($date_made=='1'){
			if(sizeof($arrConditionSchIds)>0){
				$str_sch_ids=implode(',', $arrConditionSchIds);			
			}
		}else{
			$qry="Select id FROM schedule_appointments WHERE (sa_app_start_date between '$StartDate' AND '$EndDate')";
			$rs = imw_query($qry);
			while($res= imw_fetch_assoc($rs)){
				$arrSchIds[$res['id']]=$res['id'];
			}			
			$str_sch_ids=implode(',', $arrSchIds);			
		}

		if(empty($str_sch_ids)==false){
			$arrConditionSchIds=array(); //DO EMPTY SO THAT ALL EXTRA IDS SHOULD REMOVE AND ONLY REQUIRED WILL BE ASSIGNED TO ARRAY
			$qry="SELECT id, sch_id, status, change_reason 
			FROM previous_status WHERE id IN (SELECT MAX(id) FROM previous_status WHERE sch_id IN(".$str_sch_ids.") GROUP BY sch_id)
			AND status='18'";
			if($cancel_reason=='Other'){
				$strAllReasons= "'".implode("','", $arrAllCancelReasons)."'";
				$qry.=" AND change_reason NOT IN(".$strAllReasons.")";
			}else{
				$qry.=" AND change_reason='".$cancel_reason."'";
			}	
			
			$rs = imw_query($qry);
			while($res= imw_fetch_assoc($rs)){
				$arrConditionSchIds[$res['sch_id']]=$res['sch_id'];
			}			
		}
	}

	//MAIN QUERY
	$query = "SELECT appt.id as apptid, appt.sa_doctor_id as sdocid, appt.sa_patient_id as spid, facility.name, appt.sa_doctor_id,
		appt.procedureid as procId, appt.sec_procedureid, appt.tertiary_procedureid, appt.sa_app_start_date, DATE_FORMAT(appt.sa_app_start_date, '".$dateFormat."') as apptDate, TIME_FORMAT(appt.sa_app_starttime, '%h:%i %p') as starttime, 
		TIME_FORMAT(appt.sa_app_endtime, '%h:%i %p') as endtime, u.lname as ulname, 
		u.fname as ufname, u.mname as umname, u.sign_path as usersign, pd.lname as plname, pd.fname as pfname, pd.mname as pmname, 
		pd.street,pd.street2, pd.city, pd.state, pd.postal_code, pd.email, pd.default_facility, 
		pd.phone_home, pd.phone_biz, pd.phone_cell, pd.preferr_contact,pd.primary_care_phy_id,pd.primary_care_id,
		ss.status_name as ssname, sp.proc as procedurename, pd.id as pdfacid, appt.status_update_operator_id, appt.sa_facility_id, 
		appt.sa_comments as 'sa_comments', appt.sa_patient_app_status_id as apptStatus
		".$ins_policy_inc."
		FROM schedule_appointments appt 
		INNER JOIN patient_data pd ON pd.id = appt.sa_patient_id 
		LEFT JOIN users u ON u.id = appt.sa_doctor_id 
		LEFT JOIN schedule_status ss ON ss.id = appt.sa_patient_app_status_id 
		LEFT JOIN facility ON facility.id = appt.sa_facility_id 
		LEFT JOIN slot_procedures sp ON sp.id = appt.procedureid 
		".$join_query;
		
		if((empty($rqInsType) == false) || (empty($rqInsProvider) == false) || $inc_insurance==1){ 
			$query.=" LEFT JOIN insurance_data insd ON insd.ins_caseid = appt.case_type_id AND insd.ins_caseid != 0 ";
		}
		if((empty($rqInsType)== false) && (empty($rqInsProvider)== false) && $inc_insurance==1){
			$query .= " LEFT JOIN insurance_data insd ON insd.ins_caseid = appt.case_type_id AND insd.type IN ($rqInsType) ";
		}
		$query.=" WHERE 1=1";
		
		if($date_made=='1' || empty($cancel_reason)==false){
			if(sizeof($arrConditionSchIds)>0){
				$str_sch_ids=implode(',', $arrConditionSchIds);
				$query.=" AND appt.id IN(".$str_sch_ids.")";
			}else{
				$query.=" AND 4=5";
			}
		}else{
			$query.=" AND (appt.sa_app_start_date between '$StartDate' AND '$EndDate')";
		}
		$query.=$where_query;
		
		$orderBy = " ,appt.sa_app_start_date asc, appt.sa_app_starttime";

	if(empty($operator_id_str)==false && $date_made!='1'){
		$user_query = "select username from users where id in ($operator_id_str)";
		$userQryRs = imw_query($user_query);
		$userNameArr = array();
		while($userQryRes= imw_fetch_assoc($userQryRs)){
			$userNameArr[] = "'".$userQryRes['username']."'";
		}
		$userNameStr = join(',',$userNameArr);
		$query .= " and appt.status_update_operator_id IN ($operator_id_str)";
	}
	
	if(empty($facility_name_str) == false){
		if($_REQUEST['registered_fac']==1){
			$query .= " and pd.id IN ($strPosFacPatIds)";
		}else{
			$query .= " and appt.sa_facility_id IN ($facility_name_str)";			
		}
	}

	if(empty($rqPhyId) == false){
		$query .= " and appt.sa_doctor_id IN ($rqPhyId)";
	}
	if($rqAppStatus != NULL ){
		$query .= " AND appt.sa_patient_app_status_id IN ($rqAppStatus)";
		$orderBy .= ", appt.sa_patient_app_status_id";
	}
	if(empty($rqProcedures) == false){
		$query .= " and appt.procedureid IN ($rqProcedures)";
		$orderBy .= ", appt.procedureid";
	}
	if(empty($rqInsType) == false){
		$query .= " and insd.type IN ($rqInsType)";
	}	
	if(empty($rqInsType) != false && trim($_REQUEST["inc_ins_id"]) == 1){
		//$query .= " and insd.type IN ('primary')";
	}		
	if(empty($rqInsProvider) == false){
		$query .= " and insd.provider IN ($rqInsProvider)";
	}
	if($aging_from>0) {
		$query .= " and DATEDIFF('".date('Y-m-d')."', pd.DOB) / 365 >=".$aging_from;
	}
	if($aging_to>0) {
		$query .= " and DATEDIFF('".date('Y-m-d')."', pd.DOB) / 365 <=".$aging_to;
	}
	$query .= " order by u.lname, u.fname $orderBy";

	$rs= imw_query($query);
	$arrPatIds=$arrApptIds=array();
	while($res=imw_fetch_assoc($rs)){	
		$arrPatIds[$res['spid']]=$res['spid'];
		$arrApptIds[$res['apptid']]=$res['apptid'];
		$result[]= $res;
	}
	$strPatIds=implode(',', $arrPatIds);
	
	//GETTING RECALL INFO
	$arrRecallInfo=array();
	if($inc_recalls==1 && sizeof($arrPatIds)>0){
		$qry="Select patient_id, recalldate 
		FROM patient_app_recall WHERE patient_id IN(".$strPatIds.") AND recalldate>'$StartDate' ORDER BY recalldate ASC";
		$res= imw_query($qry);
		while($res=imw_fetch_assoc($res)){	
			$arrRecallInfo[$res['patient_id']]=$res['recalldate'];
			
		}
	}
	//GETTING CHECK IN/OUT INFO
	$arrCICOInfo=array();
/*	if($inc_appt_status==1 && sizeof($arrApptIds)>0){
		$strApptIds=implode(',', $arrApptIds);
		$qry="Select ci.patient_id, ci.sch_id, ci.created_time, ci.payment_type	 
		FROM check_in_out_payment ci WHERE ci.sch_id IN(".$strApptIds.")";
		$resq= imw_query($qry);
		while($res=imw_fetch_assoc($resq)){	
			$arrCICOInfo[$res['sch_id']][$res['payment_type']]=$res['created_time'];
		}
	}*/
	
	//GETTING MADE INFO IF "INCLUDE MADE INFO" SELECTED BUT "DATE MADE" CHECKBOX IS NOT SELECTED
	if((($inc_appt_made=='1' && $date_made!='1') || $inc_appt_status==1) && sizeof($arrApptIds)>0){
		$strApptIds=implode(',', $arrApptIds);
		$qry="SELECT sch_id, status_date, oldMadeBy, statusChangedBy, DATE_FORMAT(status_time, '%h:%i %p') as 'status_time', status, DATE_FORMAT( status_date,  '".$dateFormat."' ) AS prv_status_date
		FROM previous_status WHERE sch_id IN(".$strApptIds.") ORDER BY id DESC";
		$rs = imw_query($qry);
		while($res= imw_fetch_assoc($rs)){
			$prevStatusInfo[$res['sch_id']]=$res;
			
			if($res['status']=='13'){
				$arrCICOInfo[$res['sch_id']]['checkin']=$res['status_time'];
				$arrCICOInfo[$res['sch_id']]['checkin_by']=$arrAllUsersUName[$res['statusChangedBy']];
			}else if($res['status']=='11'){
				$arrCICOInfo[$res['sch_id']]['checkout']=$res['status_time'];
				$arrCICOInfo[$res['sch_id']]['checkout_by']=$arrAllUsersUName[$res['statusChangedBy']];
			}				
		}
	}
	
	// Query for dispay DX code in Result set
	$all_dx_arr=array();
	$dxCodeQry = "SELECT pcl.patient_id, pcld.diagnosis_id1, pcld.diagnosis_id2, pcld.diagnosis_id3, pcld.diagnosis_id4 
	FROM patient_charge_list pcl INNER JOIN patient_charge_list_details pcld ON (pcld.charge_list_id = pcl.charge_list_id)
	where pcl.patient_id IN(".$strPatIds.") and (pcl.date_of_service between '$StartDate' AND '$EndDate')";	
	$rsq = imw_query($dxCodeQry);
	while($res= imw_fetch_array($rsq)){
		$diagnosis_id1 = trim($res['diagnosis_id1']);
		$diagnosis_id2 = trim($res['diagnosis_id2']);
		$diagnosis_id3 = trim($res['diagnosis_id3']);
		$diagnosis_id4 = trim($res['diagnosis_id4']);
		if($diagnosis_id1!=""){
				$all_dx_arr[$res['patient_id']][]=$diagnosis_id1;
			}
			if($diagnosis_id2!=""){
				$all_dx_arr[$res['patient_id']][]=$diagnosis_id2;
			}
			if($diagnosis_id3!=""){
				$all_dx_arr[$res['patient_id']][]=$diagnosis_id3;
			}
			if($diagnosis_id4!=""){
				$all_dx_arr[$res['patient_id']][]=$diagnosis_id4;
			}
	}
	
	$dxCodeSuperBillQry = "SELECT sb.patientId AS sbPatId, procInfo.id as procInfoId, 
	procInfo.dx1,procInfo.idSuperBill, procInfo.dx1, procInfo.dx2, procInfo.dx3, procInfo.dx4 FROM superbill sb
	INNER JOIN procedureinfo procInfo ON (procInfo.idSuperBill = sb.idSuperBill)
	where sb.patientId IN(".$strPatIds.") and sb.postedStatus = '0' and procInfo.delete_status = '0' 
	and (sb.dateOfService between '$StartDate' AND '$EndDate')";	
	$resqu = imw_query($dxCodeSuperBillQry);
	while($res= imw_fetch_array($resqu)){
		$dx1 = trim($res['dx1']);
		$dx2 = trim($res['dx2']);
		$dx3 = trim($res['dx3']);
		$dx4 = trim($res['dx4']);
		if($dx1!=""){
			$all_dx_arr[$res['patientId']][]=$dx1;
		}
		if($dx2!=""){
			$all_dx_arr[$res['patientId']][]=$dx2;
		}
		if($dx3!=""){
			$all_dx_arr[$res['patientId']][]=$dx3;
		}
		if($dx4!=""){
			$all_dx_arr[$res['patientId']][]=$dx4;
		}
	}
	
	foreach($all_dx_arr as &$obj) $obj = array_unique($obj);
	$arr_physician = array();
	$arr_patient_id = array();
	$arr_apStatus = array();
	$arr_apDt = array();
	$arr_procedure = array();
	$arr_heardAbt = array();

	if(imw_num_rows($rs) > 0){
		$pat_for_sch_arr=array();
		$pat_for_sch_appt_arr=array();

		if(empty($rqDxcode10) == false){ 
			$arrPatHasDXCode=array();
			//CHECK PATIENTS IN SUPERBILL
			if(sizeof($arrPatIds)>0){
				$strPatIds=implode(',', $arrPatIds);
				$qryGetDxCodeSuperBill = "SELECT sb.patientId AS sbPatId, procInfo.id as procInfoId, 
				procInfo.dx1,procInfo.idSuperBill FROM superbill sb
				INNER JOIN procedureinfo procInfo ON (procInfo.idSuperBill = sb.idSuperBill)
				where sb.patientId IN(".$strPatIds.") and sb.postedStatus = '0' and procInfo.delete_status = '0' 
				and (sb.dateOfService between '$StartDate' AND '$EndDate') 
				AND (procInfo.dx1 IN ($rqDxcode10) OR procInfo.dx2 IN ($rqDxcode10) OR 
				procInfo.dx3 IN ($rqDxcode10) OR procInfo.dx4 IN ($rqDxcode10))";
				$rs = imw_query($qryGetDxCodeSuperBill);
				while($res= imw_fetch_array($rs)){
					$arrPatHasDXCode[$res['patientId']]=$res['patientId'];
					unset($arrPatIds[$res['patientId']]);
				}
				unset($rs);
			}

			//CHECK PENDING PATIENTS IN PATIENT CHARGELIST
			if(sizeof($arrPatIds)>0){
				$strPatIds=implode(',', $arrPatIds);
				$qryGetDxCodeAcc = "SELECT pcl.patient_id 
				FROM patient_charge_list pcl
				INNER JOIN patient_charge_list_details pcld ON 
				(pcld.charge_list_id = pcl.charge_list_id )
				where pcl.patient_id IN(".$strPatIds.") 
				and (pcl.date_of_service	between '$StartDate' AND '$EndDate')
				AND (pcld.diagnosis_id1 IN ($rqDxcode10) OR pcld.diagnosis_id2 IN ($rqDxcode10) 
					OR pcld.diagnosis_id3 IN ($rqDxcode10) OR pcld.diagnosis_id4 IN ($rqDxcode10))";
				$rs = imw_query($qryGetDxCodeAcc);
				while($res=imw_fetch_array($rs)){
					$arrPatHasDXCode[$res['patient_id']]=$res['patient_id'];
					unset($arrPatIds[$res['patient_id']]);
				}
				unset($rs);
			}		
			unset($arrPatIds);
		}
	}
	for($i=0; $i<count($result); $i++){
		$sa_patient_app_status_id = $result[$i]['apptStatus'];
		$intPatIdSA = $result[$i]['spid'];

		if($i==0) {
			$intPatIdSAComma = $intPatIdSA;
		}else {
			$intPatIdSAComma .= ','.$intPatIdSA;
		}
		$pat_for_sch_arr[$result[$i]['spid']]=$result[$i]['spid'];
		$pat_for_sch_appt_arr[$result[$i]['spid']]=$result[$i]['ulname']."~~".$result[$i]['ufname']."~~".$result[$i]['apptDate']."~~".$result[$i]['starttime']."~~".$result[$i]['name']."~~".$result[$i]['procedurename']."~~".$result[$i]['usersign'];
		$blProcessDxRecordPat = false;
		if(empty($rqDxcode) == false || empty($rqDxcode10) == false){
			if($arrPatHasDXCode[$intPatIdSA]){
				$blProcessDxRecordPat = true;		
			}
		}
		else{
			$blProcessDxRecordPat = true;
		}
		
		if($blProcessDxRecordPat == true){
			$arrApptProc=array();
			$apptid= $result[$i]['apptid'];
			$page_data_arr = array();
			$sa_doctor_id = $result[$i]["sa_doctor_id"];
			
			if($_REQUEST['registered_fac']==1){
				$sa_facility_id = $result[$i]["pdfacid"];
			}else{
				$sa_facility_id = $result[$i]["sa_facility_id"];	
			}
			
			$sa_operator_id = $result[$i]["status_update_operator_id"];
			$firstGrpBy = $sa_doctor_id;
			if($grpby_block=='grpby_facility'){
				$firstGrpBy= $sa_facility_id;
			}
			if($grpby_block=='grpby_operators'){
				$firstGrpBy= $sa_operator_id;
			}
			
			$pro_name = core_name_format($result[$i]['ulname'], $result[$i]['ufname'], $result[$i]['umname']);

			$arrApptProc[$arrAllSlotProc[$result[$i]['procId']]]=$arrAllSlotProc[$result[$i]['procId']];
			$arrProcCount[$arrAllSlotProc[$result[$i]['procId']]][]=1;
			if($result[$i]['sec_procedureid']>0){
				$arrApptProc[$arrAllSlotProc[$result[$i]['sec_procedureid']]]=$arrAllSlotProc[$result[$i]['sec_procedureid']];
				$arrProcCount[$arrAllSlotProc[$result[$i]['sec_procedureid']]][]=1;
			}
			if($result[$i]['tertiary_procedureid']>0){
				$arrApptProc[$arrAllSlotProc[$result[$i]['tertiary_procedureid']]]=$arrAllSlotProc[$result[$i]['tertiary_procedureid']];
				$arrProcCount[$arrAllSlotProc[$result[$i]['tertiary_procedureid']]][]=1;
			}
			$strApptProc=implode(', ', $arrApptProc);
			
			$page_data_arr['PROVIDER_NAME'] = $pro_name;
			
			$pat_name = core_name_format($result[$i]['plname'], $result[$i]['pfname'], $result[$i]['pmname']);
			$page_data_arr['PATIENT_NAME'] = $pat_name." - ".$intPatIdSA;
			$page_data_arr["LNAME"] = $result[$i]['plname'];
			$page_data_arr["FNAME"] = $result[$i]['pfname'];

			if($sa_patient_app_status_id == 201){
				$result[$i]['ssname'] = 'ToDo';
			}
			
			$appt_made_date=$appt_made_by='';
			if($date_made=='1' || $inc_appt_made=='1'){
				$username=$prevStatusInfo[$apptid]['oldMadeBy'];
				$user_id=$arrAllUsersUName[$username];
				$appt_made_by=$providerNameArr[$user_id];
				$appt_made_date=$prevStatusInfo[$apptid]['prv_status_date'];
			}
			
			$page_data_arr['APPT_STATUS'] = ucwords(($result[$i]['ssname']) ? $result[$i]['ssname'] : "New Appointment");
			$page_data_arr['APPT_DATE'] = $result[$i]['apptDate'];
			$page_data_arr['APPT_TIME'] = trim($result[$i]['starttime']).'&nbsp;to&nbsp;'.trim($result[$i]['endtime']);
			$page_data_arr['APPT_MADE_DATE'] = $appt_made_date;
			$page_data_arr['APPT_MADE_BY'] = $appt_made_by;
			$page_data_arr['SA_COMMENTS'] = trim(stripslashes($result[$i]['sa_comments']));
			$page_data_arr['STREET1'] = trim($result[$i]['street']);
			$page_data_arr['STREET2'] = trim($result[$i]['street2']);
			$page_data_arr['CITY'] = $result[$i]['city'];
			$page_data_arr['STATE'] = $result[$i]['state'];
			$page_data_arr['POSTAL_CODE'] = $result[$i]['postal_code'];
			$page_data_arr['EMAIL'] = $result[$i]['email'];
			$page_data_arr['PHONE_HOME'] = $result[$i]['phone_home'];
			$page_data_arr['PHONE_BIZ'] = $result[$i]['phone_biz'];
			$page_data_arr['PHONE_CELL'] = $result[$i]['phone_cell'];
			$page_data_arr['PREFERRED_PHONE'] = $arrMapPreferredPhone[$result[$i]['preferr_contact']];
			$page_data_arr['PROCEDURE'] = $strApptProc;
			$page_data_arr['INSURANCE_POLICY_NO'] = $result[$i]['ins_policy_no'];				
			$page_data_arr['INSURANCE_TYPE'] = ucfirst(substr($result[$i]['type'],0,3));				
			$page_data_arr['INSURANCE_COMPANY'] = $arrAllInsCompanies[$result[$i]['ins_id']];				
			$page_data_arr['PATIENT_ID'] = $intPatIdSA;
			$page_data_arr['CHECK_IN'] = $arrCICOInfo[$apptid]['checkin'];
			$page_data_arr['CHECK_IN_BY'] = $providerNameArr[$arrCICOInfo[$apptid]['checkin_by']];
			$page_data_arr['CHECK_OUT'] = $arrCICOInfo[$apptid]['checkout'];
			$page_data_arr['CHECK_OUT_BY'] = $providerNameArr[$arrCICOInfo[$apptid]['checkout_by']];
			$page_data_arr['PRIMARY_CARE_PHY'] = $pt_ref_phy_arr[$result[$i]['primary_care_phy_id']];
			$page_data_arr['PRIMARY_CARE'] = $pt_ref_phy_arr[$result[$i]['primary_care_id']];
			
			if($_REQUEST['registered_fac']==1){
				$page_data_arr['FACILITY_NAME'] = $arrAllPosFacilities[$result[$i]['default_facility']];
			}else{
				$page_data_arr['FACILITY_NAME'] = $result[$i]['name'];
			}

			//GETTING NEXT RECALL DATE
			if($arrRecallInfo[$intPatIdSA]){
				$recallStrToTime=strtotime($arrRecallInfo[$intPatIdSA]);
				if($recallStrToTime > strtotime($result[$i]['sa_app_start_date'])){
					$page_data_arr['RECALL_DATE'] = date($phpDateFormat, $recallStrToTime);
				}
			}
			
			$arr_physician[$firstGrpBy][] = $page_data_arr;
		}
	}
		
		
	$pat_for_sch_imp=implode(',',array_unique($pat_for_sch_arr));
	if($report_type=="Address Labels"){
		if(count($pat_for_sch_arr)>0){
			include_once "new_account_recall_letter.php";
		}
	}else if($report_type=="Recall letter"){
		if(count($pat_for_sch_arr)>0){
			//$pat_for_srh_appt_imp=implode(',',array_unique($pat_for_sch_appt_arr));
			array_unique($pat_for_sch_appt_arr);
			include_once "new_account_recall_letter.php";
		}
	}else if($report_type=="Post Card"){
		if(count($pat_for_sch_arr)>0){
			include_once "new_account_recall_letter.php";
		}
	}else{
		foreach($arr_physician as $firstGrpBy => $data_arr)	{
			$firstTitle='Physician';
			$firstGrpByName = $providerNameArr[$firstGrpBy];
		
			if($_REQUEST['registered_fac']==1){
				$facname = $arrAllPosFacilities[$firstGrpBy];
			}else{
				$facname = $arrAllFacilities[$firstGrpBy];	
			}
			if($grpby_block=='grpby_facility'){
				$firstTitle='Facility';
				$firstGrpByName = $facname;
			}
			if($grpby_block=='grpby_operators'){
				$firstTitle='Operator';
				$firstGrpByName= $providerNameArr[$firstGrpBy];
			}					
			
			$page_data.='<tr><td class="text_b_w alignLeft" colspan="'.$pro_cols.'">'.$firstTitle.' : '.$firstGrpByName.'</td></tr>';
			$pdf_data .='<tr><td class="text_b_w" width="100%" colspan="'.$pro_cols_pdf.'">'.$firstTitle.' : '.$firstGrpByName.'</td></tr>';
			for($jb=0;$jb<=count($data_arr);$jb++){
				$var_arr[] = $data_arr[$jb]['INSURANCE_POLICY_NO'];
			}
			
			$allEmpty = true;
			foreach( $var_arr as $key => $val) {
				if($val != '') {
					$allEmpty = false;
					break;
				}
			}
			
			if($allEmpty == true){
				$ins_width = '116px';
			}else{
				$ins_width = '50px';
			}
			
			for($p=0,$cnt=1;$p<count($data_arr);$p++,$cnt++){					
				$cntPack++;
				$patId = $data_arr[$p]['PATIENT_ID'];
				$patientName = $data_arr[$p]['PATIENT_NAME'];
				$street1 = $data_arr[$p]['STREET1'];
				$street2 = $data_arr[$p]['STREET2'];
				$city = $data_arr[$p]['CITY'];
				$state = $data_arr[$p]['STATE'];
				$postal_code = $data_arr[$p]['POSTAL_CODE'];
				$email = $data_arr[$p]['EMAIL'];
				$facility = $data_arr[$p]['FACILITY_NAME'];
				$app_date = $data_arr[$p]['APPT_DATE'];
				$app_time = $data_arr[$p]['APPT_TIME'];
				$app_comments = htmlentities($data_arr[$p]['SA_COMMENTS']);
				$app_status = $data_arr[$p]['APPT_STATUS'];
				$app_procedure = $data_arr[$p]['PROCEDURE'];
				$ins_policy_no = $data_arr[$p]['INSURANCE_POLICY_NO'];
				$ins_company = $data_arr[$p]['INSURANCE_COMPANY'];
				$instype = $data_arr[$p]['INSURANCE_TYPE'];
				//$heard_abt = $data_arr[$p]['HEARD_OPTIONS'];
				$patientLName = $data_arr[$p]['LNAME'];
				$patientFName = $data_arr[$p]['FNAME'];
				$pri_care_phy_id = $data_arr[$p]['PRIMARY_CARE_PHY'];
				$pri_care_id = $data_arr[$p]['PRIMARY_CARE'];
				
				$dxCodes = $all_dx_arr[$patId];
				$dxCodes = implode(", ",$dxCodes);
				$rqHeardTd = NULL;

				$chbxPackage 			= '';
				if($report_type=="Package"){
					$appStatusTd		= '';
					$pdf_appStatusTd 	= '';
					$chbxPackage 		= '<input type="checkbox" name="chbxPackage[]" id="chbxPackage'.$cntPack.'" checked value="'.$patId.'" onClick="chkPtIdComma()">';
				}
				
				
				$ins_policy_td = '';
				if($inc_insurance==1){
					$ins_policy_td = '<td class="valignTop text_10" style="text-align:left">'.$ins_policy_no.'</td>
									  <td class="valignTop text_10" style="text-align:left">'.$ins_company.' ('.$instype.'.)</td>';
				}
				if(empty($rqHeard) === true){	
					$pdf_ins_policy_td 	= '<td class="text_10" width='.$tdwidth.' style="text-align:left; background:#FFFFFF;">'.$ins_policy_no.'</td>
										  <td class="text_10" width='.$tdwidth.' style="text-align:left; background:#FFFFFF;">'.$ins_company.' ('.$instype.'.)</td>';
				}else{
					$pdf_ins_policy_td 	= '<td class="text_10" width='.$tdwidth.' style="text-align:left; background:#FFFFFF;">'.$ins_policy_no.'</td>
										   <td class="text_10" width='.$tdwidth.' style="text-align:left; background:#FFFFFF;">'.$ins_company.' ('.$instype.'.)</td>';
				}
				
				$inc_pcp_td = '';
				if($inc_pcp==1){
					$inc_pcp_td = '<td class="valignTop text_10" style="text-align:left">'.$pri_care_phy_id.'</td>';
				}
				$inc_ref_phy_td = '';
				if($inc_ref_phy==1){
					$inc_ref_phy_td = '<td class="valignTop text_10" style="text-align:left">'.$pri_care_id.'</td>';
				}
			
				$apptDetailsTD='';
				if($inc_appt_detail==1){
					$apptDetailsTD='
						<td class="text_10" width='.$tdwidth_pdf.' style="text-align:center; background:#FFFFFF;">'.$app_date.'</td>
						<td class="text_10" width='.$tdwidth_pdf.' style="text-align:center; background:#FFFFFF;">'.$app_time.'</td>
						<td class="text_10" width='.$tdwidth_pdf.' style="text-align:left; background:#FFFFFF;">'.$app_comments.'</td>';
				}
				if($date_made=='1' || $inc_appt_made=='1'){
					$apptDetailsTD.='
						<td class="text_10" width='.$tdwidth_pdf.' style="text-align:center; background:#FFFFFF;">'.$data_arr[$p]['APPT_MADE_DATE'].'</td>
						<td class="text_10" width='.$tdwidth_pdf.' style="text-align:left; background:#FFFFFF;">'.$data_arr[$p]['APPT_MADE_BY'].'</td>';
				}
				
				$demographicsTD='';
				if($inc_demographics==1){
					$demographicsTD='
						<td class="valignTop text_10" style="text-align:left">'.$street1.'</td>
						<td class="valignTop text_10" style="text-align:left">'.$street2.'</td>
						<td class="valignTop text_10" style="text-align:left">'.$city.'</td>
						<td class="valignTop text_10" style="text-align:left">'.$state.'</td>
						<td class="valignTop text_10" style="text-align:left">'.$postal_code.'</td>
						<td class="valignTop text_10" style="text-align:left">'.$email.'</td>
						<td class="valignTop text_10" style="text-align:left">'.$data_arr[$p]['PHONE_HOME'].'</td>
						<td class="valignTop text_10" style="text-align:left">'.$data_arr[$p]['PHONE_BIZ'].'</td>
						<td class="valignTop text_10" style="text-align:left">'.$data_arr[$p]['PHONE_CELL'].'</td>
						<td class="valignTop text_10" style="text-align:left">'.$data_arr[$p]['PREFERRED_PHONE'].'</td>
						';
				}
				$demographicsPFDTD='';
				if($inc_demographics==1){
					$demographicsPFDTD='
						<td class="valignTop text_10" width='.$tdwidth.'>'.$street1.'</td>
						<td class="valignTop text_10" width='.$tdwidth.'>'.$street2.'</td>
						<td class="valignTop text_10" width='.$tdwidth.'>'.$city.'</td>
						<td class="valignTop text_10" width='.$tdwidth.'>'.$state.'</td>
						<td class="valignTop text_10" width='.$tdwidth.'>'.$postal_code.'</td>
						<td class="valignTop text_10" width='.$tdwidth.'>'.$email.'</td>
						<td class="valignTop text_10" width='.$tdwidth.'>'.$data_arr[$p]['PHONE_HOME'].'</td>
						';
				}
				
				$recallTD='';
				if($inc_recalls==1){
					$recallTD='<td class="valignTop text_10"  width='.$tdwidth.' style="text-align:center">'.$data_arr[$p]['RECALL_DATE'].'</td>';
				}
				$apptStsTD='';
				if($inc_appt_status==1){
					$apptStsTD='<td class="valignTop text_10" width='.$tdwidth.' style="text-align:center">'.$data_arr[$p]['CHECK_IN'].'</td>
					<td class="valignTop text_10" width='.$tdwidth.' style="text-align:left">'.$data_arr[$p]['CHECK_IN_BY'].'</td>
					<td class="valignTop text_10" width='.$tdwidth.' style="text-align:center;">'.$data_arr[$p]['CHECK_OUT'].'</td>
					<td class="valignTop text_10" width='.$tdwidth.' style="text-align:left;">'.$data_arr[$p]['CHECK_OUT_BY'].'</td>
					<td class="valignTop text_10" width='.$tdwidth.' style="text-align:left;">'.$data_arr[$p]['APPT_STATUS'].'</td>';
				}
				
				$incPtPortalkey='';
				if($inc_portal_key ==1){
					$incPtPortalkey = '<td class="text_10" width='.$tdwidth_pdf.' style="text-align:center; background:#FFFFFF;">'.$arrPtPotalKay[$patId].'</td>';
				}
				
				$page_data .= <<<DATA
					<tr bgcolor="#FFFFFF">
						<td class="valignTop text_10 nowrap" style="text-align:center;">$cnt $chbxPackage</td>
						<td class="valignTop text_10" style="text-align:left">$patId</td>
						<td class="valignTop text_10" style="text-align:left">$patientLName</td>
						<td class="valignTop text_10" style="text-align:left">$patientFName</td>
						$inc_pcp_td
						$inc_ref_phy_td
						$demographicsTD
						<td class="valignTop text_10" style="text-align:left">$facility</td>
						$apptDetailsTD
						<td class="valignTop text_10" style="text-align:left">$app_procedure</td>
						<td class="valignTop text_10" style="text-align:left">$dxCodes</td>
						$ins_policy_td
						$recallTD
						$apptStsTD
						$incPtPortalkey
					</tr>
DATA;
				$email_str=array();
				if(strlen(trim($email)) > 12){
					$email_str = str_split($email,12);
					$email = "";
					for($e=0;$e<count($email_str);$e++){
						$email .= $email_str[$e]."<br>";
					} 
				}
				
				$ptWidth = 200;
				$pdf_data .= <<<DATA
					<tr bgcolor="#FFFFFF">
						<td class="valignTop text_10" width="23" style="text-align:center">$cnt</td>
						<td class="valignTop text_10" width="$tdwidth_pdf" style="text-align:left">$patientName</td>
						<td class="valignTop text_10" width="$tdwidth_pdf" style="text-align:left">$facility</td>
						$apptDetailsTD
						<td class="valignTop text_10" width="$tdwidth_pdf" style="text-align:left">$app_procedure</td>
						<td class="valignTop text_10" width="$tdwidth_pdf" style="text-align:left">$dxCodes</td>
						$incPtPortalkey
					</tr>					
DATA;

					}
				}
			//}
		}
	//}
	
	
	//ALL APPOINTMENTS WHICH ARE SCHEDULED BETWEEN SELECTED DATE RANGE.
	$arrRescheduled=array();
	//GET LAST DATE OF RE-SCHEDULE BETWEEN SELECTED DATE RANGE WHICH HAVE NO RESCEDULE AFTER SELECTED DATE RANGE.
	$qry="SELECT sa.id, sa.sa_patient_id, DATE_FORMAT(sa.sa_app_start_date , '%m-%d-%Y' ) AS sa_app_start_date,
	sa.sa_facility_id, sa.sa_doctor_id, sa.procedureid, sa.sec_procedureid, sa.tertiary_procedureid, 
	DATE_FORMAT(ps.status_date, '%m-%d-%Y' ) AS status_date, ps.statusChangedBy, pd.fname, pd.mname, pd.lname,
	pd.primary_care_phy_id, pd.primary_care_id
	FROM schedule_appointments sa 
	INNER JOIN previous_status ps ON sa.id = ps.sch_id
	AND ps.new_appt_date = sa.sa_app_start_date
	AND ps.status = 202
	INNER JOIN patient_data pd ON pd.id = sa.sa_patient_id 
	WHERE (sa.sa_app_start_date BETWEEN '$StartDate' AND '$EndDate')";	
	if(empty($strSelUsersUName)==false){
		$qry.=" AND ps.statusChangedBy IN(".$strSelUsersUName.")";
	}
	if(empty($facility_name_str) == false){
		$qry .= " and sa.sa_facility_id IN ($facility_name_str)";			
	}
	if(empty($rqPhyId) == false){
		$qry .= " and sa.sa_doctor_id IN ($rqPhyId)";
	}
	if(empty($rqProcedures) == false){
		$qry .= " and sa.procedureid IN ($rqProcedures)";
	}
	$qry.=" ORDER BY pd.lname, pd.fname";		
	$rs= imw_query($qry);
	while($res=imw_fetch_assoc($rs)){
		$sch_id=$res['id'];
		$arrRescheduled[$sch_id]=$res;
	}
	imw_free_result($rs);
		
	//--- REPORT HEADER ---
	if(empty($page_data) === false || sizeof($arrRescheduled)>0){
		$curDate.= date(" g:i A",time());
		$header_hrd_td = NULL;
		$content_hrd_td = NULL;
		$ins_title='';
		$td_width = '100px';
		$cnt_td_width = '100px';
		//if(empty($rqHeard) === false){
			$td_width = '80px';
			$cnt_td_width = '80px';
		//}
		$ins_policy_td_print = '';
		if($inc_insurance==1){
			$ins_title = '<td style="width:60px; text-align:center;" class="text_b_w">Ins. Policy#</td>
						  <td style="width:60px; text-align:center;" class="text_b_w">Ins. Company</td>';
			$ins_policy_td_print = '<td width="'.$tdwidth.'" style="text-align:center;" class="text_b_w">Ins. Policy #</td>
									<td width="'.$tdwidth.'" style="text-align:center;" class="text_b_w">Ins. Company</td>';
		}
		//if(trim($_REQUEST["inc_ins_id"]) == 1){
			if(empty($rqHeard) === true){
				//$ins_policy_td_print = '<td style="width:'.$tdwidth.'; text-align:center;" class="text_b_w">Ins. Policy #</td>';
			}else{
				//$ins_policy_td_print = '<td style="width:'.$tdwidth.'; text-align:center;" class="text_b_w">Ins. Policy #</td>';
			}
			$ins_policy_td_print_content = '<td style="width:50px; height:1px"></td>';
			if(empty($rqHeard) === false){
				$td_width = '135px';
				$cnt_td_width = '135px';				
			}
			else
			{
				$td_width = '80px';
				$cnt_td_width = '80px';	
			}
		//}
		
		$apptDetailsTitle='';
		if($inc_appt_detail==1){
			$apptDetailsTitle = '
			<td width="'.$tdwidth_pdf.'" style="text-align:center;" class="text_b_w">Appt. Date</td>
			<td width="'.$tdwidth_pdf.'" style="text-align:center;" class="text_b_w">Appt. Time</td>
			<td width="'.$tdwidth_pdf.'" style="text-align:center;" class="text_b_w">Appt. Comments</td>';
		}		
		if($date_made==1 || $inc_appt_made=='1'){
			$apptDetailsTitle.= '
			<td width="'.$tdwidth_pdf.'" style="text-align:center;" class="text_b_w">Made Date</td>
			<td width="'.$tdwidth_pdf.'" style="text-align:center;" class="text_b_w">Made By</td>';
		}
		$demographicsTitle='';
		if($inc_demographics==1){
			$demographicsTitle = '
				<td style="width:100px; text-align:center;" class="text_b_w">Address1</td>
				<td style="width:60px; text-align:center;" class="text_b_w">Address2</td>
				<td style="width:90px; text-align:center;" class="text_b_w">City</td>
				<td style="width:30px; text-align:center;" class="text_b_w">State</td>
				<td style="width:50px; text-align:center;" class="text_b_w">Zip</td>
				<td style="width:100px; text-align:center;" class="text_b_w">Email</td>
				<td style="width:100px; text-align:center;" class="text_b_w">Home Phone</td>
				<td style="width:100px; text-align:center;" class="text_b_w">Biz Phone</td>
				<td style="width:100px; text-align:center;" class="text_b_w">Cell Phone</td>
				<td style="width:100px; text-align:center;" class="text_b_w">Preferred</td>';
		}
		
		$demographicsPFDTitle='';
		if($inc_demographics==1){
			$demographicsPFDTitle = '
				<td width="'.$tdwidth.'" style="text-align:center;" class="text_b_w">Address1</td>
				<td width="'.$tdwidth.'" style="text-align:center;" class="text_b_w">Address2</td>
				<td width="'.$tdwidth.'" style="text-align:center;" class="text_b_w">City</td>
				<td width="'.$tdwidth.'" style="text-align:center;" class="text_b_w">State</td>
				<td width="'.$tdwidth.'" style="text-align:center;" class="text_b_w">Zip</td>
				<td width="'.$tdwidth.'" style="text-align:center;" class="text_b_w">Email</td>
				<td width="'.$tdwidth.'" style="text-align:center;" class="text_b_w">Home Phone</td>';
		}
		
		$recallTitle='';
		if($inc_recalls==1){
			$recallTitle = '<td style="width:60px; text-align:center;" class="text_b_w">Recall</td>';
		}
		$apptStsTitle='';
		if($inc_appt_status==1){
			$apptStsTitle = '<td width="'.$tdwidth.'" style="text-align:center;" class="text_b_w">Check In</td>
			<td width="'.$tdwidth.'" style="text-align:center;" class="text_b_w">Check In By</td>
			<td width="'.$tdwidth.'" style="text-align:center;" class="text_b_w">Check Out</td>
			<td width="'.$tdwidth.'" style="text-align:center;" class="text_b_w">Check Out By</td>
			<td width="'.$tdwidth.'" style="ext-align:center;" class="text_b_w">Status</td>';
		}
		$inc_portal_key='';
		if($_REQUEST['inc_portal_key']==1){
			$inc_portal_key = '<td width="'.$tdwidth_pdf.'" style="text-align:center;" class="text_b_w">Pt. Portal Key</td>';
		}

		$inc_pcp_title = '';
		if($inc_pcp==1){
			$inc_pcp_title = '<td width="'.$tdwidth.'" style="text-align:center;" class="text_b_w">PCP</td>';
		}
		$inc_ref_phy_title = '';
		if($inc_ref_phy==1){
			$inc_ref_phy_title = '<td width="'.$tdwidth.'" style="text-align:center;" class="text_b_w">Ref. Phy</td>';
		}

		$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';		
		//PROCEDURE COUNT BLOCK
		$html_proc_count=$html_count_part='';
		if(sizeof($arrProcCount)>0){
			foreach($arrProcCount as $proc_name => $proc_data){
				$html_count_part.=
				'<tr>
				<td class="valignTop text_10" style="text-align:left">'.$proc_name.'</td>
				<td class="valignTop text_10" style="text-align:center">'.count($proc_data).'</td>
				<td class="valignTop text_10" style="text-align:left"></td>
				</tr>';
			}
			
			$html_proc_count='
			<br>
			<table class="rpt_table rpt rpt_table-bordered" width="1050">
			<tr id="heading_orange"><td colspan="3">Procedure Summary</td></tr>
			<tr>
				<td style="text-align:center;" class="text_b_w" width="200">Procedures</td>
				<td style="text-align:center;" class="text_b_w" width="150">Count</td>
				<td style="text-align:center;" class="text_b_w" width="700"></td>
			</tr>'
			.$html_count_part.
			'</table>';
		}
		
		//RESCHEDULED BLOCK
		$html_rescheduled=$html_rescheduled_part='';
		if(sizeof($arrRescheduled)>0){
			$i=1;
			foreach($arrRescheduled as $sch_id => $data){
				$pat_name = core_name_format($data['lname'], $data['fname'], $data['mname']);
				$pat_name.=' - '.$data['sa_patient_id'];

				$username=$data['statusChangedBy'];
				$user_id=$arrAllUsersUName[$username];
				$rescheduled_by=$providerNameArr[$user_id];
				
				$dxCode = $all_dx_arr[$data['sa_patient_id']];
				$dxCode = implode(", ",$dxCode);
				
				$arrApptProc=array();
				if($data['procedureid']>0)$arrApptProc[$arrAllSlotProc[$data['procedureid']]]=$arrAllSlotProc[$data['procedureid']];
				if($data['sec_procedureid ']>0)$arrApptProc[$arrAllSlotProc[$data['sec_procedureid ']]]=$arrAllSlotProc[$data['sec_procedureid ']];
				if($data['tertiary_procedureid']>0)$arrApptProc[$arrAllSlotProc[$data['tertiary_procedureid']]]=$arrAllSlotProc[$data['tertiary_procedureid']];
				
				$strApptProc='';
				if(sizeof($arrApptProc)>0){	$strApptProc=implode(', ', $arrApptProc); }
				
				
				$inc_pcp_td = '';
				if($inc_pcp==1){
					$inc_pcp_td = '<td class="valignTop text_10" style="text-align:left">'.$pt_ref_phy_arr[$data['primary_care_phy_id']].'</td>';
				}
				$inc_ref_phy_td = '';
				if($inc_ref_phy==1){
					$inc_ref_phy_td = '<td class="valignTop text_10" style="text-align:left">'.$pt_ref_phy_arr[$data['primary_care_id']].'</td>';
				}
				
				$html_rescheduled_part.=
				'<tr>
				<td class="valignTop text_10" style="text-align:left">'.$i.'</td>
				<td class="valignTop text_10" style="text-align:left">'.$pat_name.'</td>
				'.$inc_pcp_td.'
				'.$inc_ref_phy_td.'
				<td class="valignTop text_10" style="text-align:center">'.$data['sa_app_start_date'].'</td>
				<td class="valignTop text_10" style="text-align:center">'.$data['status_date'].'</td>
				<td class="valignTop text_10" style="text-align:left">'.$rescheduled_by.'</td>
				<td class="valignTop text_10" style="text-align:left">'.$strApptProc.'</td>
				<td class="valignTop text_10" style="text-align:left">'.$arrAllFacilities[$data['sa_facility_id']].'</td>
				<td class="valignTop text_10" style="text-align:left">'.$providerNameArr[$data['sa_doctor_id']].'</td>
				<td class="valignTop text_10" style="text-align:left">'.$dxCode.'</td>
				</tr>';
				$i++;
			}
			$colspan = 9;
			$res_inc_pcp_title = '';
			if($inc_pcp==1){
			
				$res_inc_pcp_title = '<td style="text-align:center;" class="text_b_w" width="100">PCP</td>';
				$colspan = $colspan + 1; 
			}
			$res_inc_ref_phy_title = '';
			if($inc_ref_phy==1){
				$res_inc_ref_phy_title = '<td style="text-align:center;" class="text_b_w" width="100">Referring Phy.</td>';
				$colspan = $colspan + 1; 
			}
			
			$html_rescheduled='
			<table class="rpt_table rpt rpt_table-bordered" width="1050">
			<tr id="heading_orange"><td colspan="'.$colspan.'">Rescheduled For Selected Date Range</td></tr>
			<tr>
				<td style="text-align:center;" class="text_b_w" width="40">#</td>
				<td style="text-align:center;" class="text_b_w" width="120">Pat Name-ID</td>
				'.$res_inc_pcp_title.'
				'.$res_inc_ref_phy_title.'
				<td style="text-align:center;" class="text_b_w" width="100">New Appt Date</td>
				<td style="text-align:center;" class="text_b_w" width="100">Rescheduled On</td>
				<td style="text-align:center;" class="text_b_w" width="100">Rescheduled By</td>
				<td style="text-align:center;" class="text_b_w" width="100">Procedure</td>
				<td style="text-align:center;" class="text_b_w" width="100">Facility</td>
				<td style="text-align:center;" class="text_b_w" width="100">Physician</td>
				<td style="text-align:center;" class="text_b_w" width="100">ICD10</td>
			</tr>'
			.$html_rescheduled_part.
			'</table>';
		}				
		

		//--- PDF FILE CONTENT ---
		$pdf_file_content = <<<DATA
			$stylePDF
			<page backtop="11mm" backbottom="10mm">   
				<page_footer>
					<table style="width:100%;">
						<tr>
							<td style="text-align:center; width: 100%">Page [[page_cu]]/[[page_nb]]</td>
						</tr>
					</table>
				</page_footer>
				<page_header>
					
					<table style="width:100%;" cellpadding="1" cellspacing="1" border="0" bgcolor="#FFF3E8" class="rpt_padding">
					   <tr>
						   <td style="width:33.3%;text-align:center" class="rpt_headers rptbx1">$dbtemp_name</td>
						   <td style="width:33.3%;"text-align:center" class="rpt_headers rptbx2">Report Period : $Start_date to $End_date</td>
						   <td style="width:33.3%;"text-align:center" class="rpt_headers rptbx3">Created By $createdBy on $curDate</td>
					   </tr>
					</table>
					
					<table width="100%" cellpadding="1" cellspacing="1" border="0"  bgcolor="#FFF3E8">
					<tr>
						<td width="23" style="text-align:center;" class="text_b_w">#</td>
						<td width="$tdwidth_pdf" style="text-align:left;" class="text_b_w">Patient Name</td>
						<td width="$tdwidth_pdf" style="text-align:left;" class="text_b_w">Facility</td>
						$apptDetailsTitle
						<td width="$tdwidth_pdf" style="text-align:left;" class="text_b_w">Procedure</td>
						<td width="$tdwidth_pdf" style="text-align:left;" class="text_b_w">ICD10</td>
						$inc_portal_key
					</tr>
				</table>
			</page_header>
			<table width="100%" cellspacing="1" cellpadding="0" border="0" style="background-color:#FFF3E8">
				$pdf_data
			</table>
			$html_proc_count
			$html_rescheduled
			</page>
DATA;


		//--- CREATE HTML FILE FOR PDF PRINTING ----
		//$html_file_name = get_scheduled_pdf_name('scheduler_report', '../common/new_html2pdf');
		//file_put_contents("../common/new_html2pdf/".$html_file_name.".html", $pdf_file_content);
		
		$file_location = write_html($pdf_file_content);	
	//	exit;
	}
}

//--- SET CSV FILE CONTENT ----
if(empty($page_data) === false || sizeof($arrRescheduled)>0){
	if(!empty($appStatusTd)) {
		
	}
	$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
	
	
	$csv_file_data= $styleHTML.'
	<table class="rpt_table rpt rpt_table-bordered rpt_padding">
		<tr>
			<td class="rptbx1" style="width:33%">'.$dbtemp_name.'</td>
			<td class="rptbx2" style="text-align:center; width:33%">Report Period : '.$Start_date.' to '.$End_date.'</td>
			<td class="rptbx3" style="text-align:center; width:33%">
				Created By '.$createdBy.' on '.$curDate.'
			</td>
		</tr>
	</table>';
	if(empty($page_data) === false){
		$csv_file_data.='
		<table class="rpt_table rpt rpt_table-bordered">
			<tr>
				<td style="width:20px; text-align:center;" class="text_b_w">#</td>
				<td style="width:80px; text-align:center;" class="text_b_w">Pt. Id</td>
				<td style="width:90px; text-align:center;" class="text_b_w">Pt. Last Name</td>
				<td style="width:90px; text-align:center;" class="text_b_w">Pt. First Name</td>
				'.$inc_pcp_title.'
				'.$inc_ref_phy_title.'
				'.$demographicsTitle.'
				<td style="width:70px; text-align:center;" class="text_b_w">Facility</td>
				'.$apptDetailsTitle.'
				<td style="width:90px; text-align:center;" class="text_b_w">Procedure</td>
				<td style="width:90px; text-align:center;" class="text_b_w">ICD10</td>
				'.$ins_title.'
				'.$recallTitle.'
				'.$apptStsTitle.'
				'.$inc_portal_key.'
			</tr>
			'.$page_data.'
		</table>
		'.$html_proc_count;
	}

	$csv_file_data.=$html_rescheduled.'
</div>';

	$printPdFBtn  = 1;
	$csvBtn  = 1;
	$showbtn  = 1;
}else{
	$csv_file_data = '<div class="text-center alert alert-info">No Record Found.</div>';
	$csvBtn = 0;
	$printPdFBtn  = 0;
	$showbtn  = 0;
}
echo $csv_file_data;


if($report_type=="Recall letter" || $report_type=="Address Labels" || $report_type=="Post Card")
{
	if($num>0)
	{
		//$objGetResult->Smarty->assign('blank_page','yes');
	}
}
$package_category_id='';
if($report_type=='Package') {
	$package_category_id = $packageListId;	
}
?>