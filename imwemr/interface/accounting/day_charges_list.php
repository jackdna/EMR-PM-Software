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
$without_pat="yes";
$title = "Day Charges";
include_once(dirname(__FILE__)."/acc_header.php");
include_once(dirname(__FILE__)."/../../library/classes/SaveFile.php");
require_once(dirname(__FILE__)."/../../library/classes/common_function.php"); 
require_once(dirname(__FILE__)."/../../library/classes/cls_common_function.php");
$OBJCommonFunction = new CLSCommonFunction;

$operator_id=$_SESSION['authId'];
$cur_date=date('m-d-Y');

$provider_type=xss_rem($_REQUEST['provider_type'], 1);
$view_srh=xss_rem($_REQUEST['view_srh'], 1);
$ord_by=xss_rem($_REQUEST['ord_by'], 1);
$view_chart=xss_rem($_REQUEST['view_chart'], 1);
$srh_tot_poc_fac=$_REQUEST['srh_tot_poc_fac']; 

$ci_co_label="CI/CO";
$pt_pmt_label="Pt Pmt";
$copay_label="Copay";
$ci_co_data_wd=$pt_pmt_data_wd="45";
$copay_data_wd="40";
if(strtolower($billing_global_server_name)=='miramar'){
	$ci_co_label="Billing Provider";
	$copay_label=$pt_pmt_label=$pt_pmt_data_wd=$copay_data_wd="";
	$ci_co_data_wd="110";
}

if($_REQUEST['provider_srh_str']){
	$provider_srh_str=$_REQUEST['provider_srh_str'];
	$provider_srh_arr=explode(',',$provider_srh_str);
}else{
	$provider_srh_str=implode(',',$_REQUEST['provider_srh']);
	$provider_srh_arr=explode(',',$provider_srh_str);
}
if($_REQUEST['operator_srh_str']){
	$operator_srh_str=$_REQUEST['operator_srh_str'];
	$operator_srh_arr=explode(',',$operator_srh_str);
}else{
	$operator_srh_str=implode(',',$_REQUEST['operator_srh']);
	$operator_srh_arr=explode(',',$operator_srh_str);
}
if($_REQUEST['acc_fac_str']){
	$acc_fac_str=$_REQUEST['acc_fac_str'];
	$acc_fac_arr=explode(',',$acc_fac_str);
}else{
	$acc_fac_str=implode(',',$_REQUEST['acc_fac']);
	$acc_fac_arr=explode(',',$acc_fac_str);
}

if($_REQUEST['inscasetype_str']){
	$ins_case_type_str=$_REQUEST['inscasetype_str'];
	$ins_case_type_arr=explode(',',$ins_case_type_str);
}else{
	$ins_case_type_str=implode(',',$_REQUEST['inscasetype']);
	$ins_case_type_arr=explode(',',$ins_case_type_str);
}

if($_REQUEST['acc_ins_str']){
	$acc_ins_str=str_replace('~~~',',',$_REQUEST['acc_ins_str']);
	$acc_ins_send_str=$_REQUEST['acc_ins_str'];
	$acc_ins_arr=explode(',',str_replace('~~~',',',$acc_ins_send_str));
	$acc_ins_drop_arr=explode(',',$acc_ins_send_str);
}else{
	$acc_ins_str=str_replace('~~~',',',implode(',',$_REQUEST['acc_ins']));
	$acc_ins_send_str=implode(',',$_REQUEST['acc_ins']);
	$acc_ins_arr=explode(',',str_replace('~~~',',',$acc_ins_send_str));
	$acc_ins_drop_arr=explode(',',$acc_ins_send_str);
}

//----------------- Copay Policies Data -----------------//
$qry = imw_query("SELECT billing_amount,vip_copay_not_collect,vip_ref_not_collect FROM copay_policies WHERE policies_id='1'");
$pol_qry = imw_fetch_array($qry);
$vip_copay_not_collect = $pol_qry['vip_copay_not_collect'];
$vip_ref_not_collect = $pol_qry['vip_ref_not_collect'];
$billing_amount = $pol_qry['billing_amount'];

//----------------- Provider Data -----------------//
$provider_options = $OBJCommonFunction->drop_down_providers($provider_srh_str,'1','','','','consult_letter'); 
//----------------- Operator Data -----------------//
$operator_options = $OBJCommonFunction->drop_down_providers($operator_srh_str,'',''); 

//----------------- POS Facility Data -----------------//
$pos_fac_arr = $poc_fac_all_arr = array();
$qry_pos_fac=imw_query("select pos_facilityies_tbl.facilityPracCode as name,
						pos_facilityies_tbl.pos_facility_id as id,
						pos_tbl.pos_prac_code
						from pos_facilityies_tbl
						left join pos_tbl on pos_tbl.pos_id = pos_facilityies_tbl.pos_id
						order by pos_facilityies_tbl.facilityPracCode");	
while($fet_pos_fac=imw_fetch_array($qry_pos_fac)){
	$pos_fac_arr[$fet_pos_fac['id']]=$fet_pos_fac['name'];
	$poc_fac_all_arr[$fet_pos_fac['id']]=$fet_pos_fac['pos_prac_code'];
	$poc_fac_opt_arr[]=$fet_pos_fac;
}

//----------------- Insurance Cases Data -----------------//
$ins_case_type_opt_arr = $arr_ins_case_type_all = array();
$qry_ins_case_type =imw_query("select case_id,case_name from insurance_case_types where insurance_case_types.status='0' order by case_name");	
while($fet_ins_case_type=imw_fetch_array($qry_ins_case_type)){
	$arr_ins_case_type_all[$fet_ins_case_type['case_id']]=$fet_ins_case_type['case_name'];
	$ins_case_type_opt_arr[]=$fet_ins_case_type;
}

//----------------- Insurance Companies Data -----------------//
$ins_comp_arr=$ins_id_arr=$ins_grp_arr=array();
$sel_ins_comp=imw_query("select id,name,in_house_code,groupedIn,FeeTable from insurance_companies WHERE in_house_code IS NOT NULL order by in_house_code");
while($fet_ins_comp=imw_fetch_array($sel_ins_comp)){
	if($fet_ins_comp['in_house_code']){
		$ins_comp_arr[$fet_ins_comp['id']]=$fet_ins_comp['in_house_code'];
	}else{
		$ins_comp_arr[$fet_ins_comp['id']]=$fet_ins_comp['name'];
	}
	$ins_id_arr[]=$fet_ins_comp['id'];
	$ins_grp_arr[$fet_ins_comp['groupedIn']][]=$fet_ins_comp['id'];
	$ins_fee_tbl_arr[$fet_ins_comp['id']]=$fet_ins_comp['FeeTable'];
}

if($acc_fac_str!= ""){

	$data_detail=$mod_code_arr=$pat_arr=$tos_code_arr=$pos_codes_arr=$phy_id_arr=$arrAllUsers=array();	
	//----------------- CPT Data -----------------//
	$sql = "select * from cpt_fee_tbl WHERE status='active' AND delete_status = '0' order by cpt_prac_code ASC";
	$rezCodes = imw_query($sql);
	if(imw_num_rows($rezCodes) > 0){
		while($rowCodes=imw_fetch_array($rezCodes)){
			$code = $rowCodes["cpt_prac_code"];
			$cpt_desc = $rowCodes["cpt_desc"];
			$stringAllProcedures.="'".str_replace("'","",$code)."',";	
			$stringAllProcedures.="'".str_replace("'","",$cpt_desc)."',";
			$cpt_desc_arr[$rowCodes["cpt_prac_code"]]=$rowCodes["cpt_desc"];
		}
	}
	$stringAllProcedures = substr($stringAllProcedures,0,-1);
	
	//----------------- Modifiers Data -----------------//
	$sql = "select * from modifiers_tbl WHERE delete_status = '0' order by mod_prac_code ASC";
	$rez = imw_query($sql);	
	while($row = imw_fetch_array($rez)){
		$code=$row["mod_prac_code"];
		$mod_description=$row["mod_description"];
		$stringAllModifiers.="'".str_replace("'","",$code)."',";
		//$stringAllModifiers.="'".str_replace("'","",$mod_description)."',";
		$mod_code_arr[$row['modifiers_id']]=$code;
		$chk_mod_in_db_arr[$row["mod_prac_code"]]=$code;
	}
	$stringAllModifiers = substr($stringAllModifiers,0,-1);
	
	//----------------- TOS Data -----------------//
	$selQry = "select * from tos_tbl order by tos_prac_cod ASC";
	$res = imw_query($selQry);
	while($row = imw_fetch_array($res)){
		$code = $row['tos_prac_cod'];
		$stringAllTos.="'".str_replace("'","",$code)."',";
		$tos_code_arr[$row['tos_id']]=$code;
	}
	$stringAllTos = substr($stringAllTos,0,-1);
	
	//----------------- POS Data -----------------//
	$selQry = "select * from pos_tbl order by pos_prac_code ASC";
	$res = imw_query($selQry);
	while($row = imw_fetch_array($res)){
		$id = $row['pos_id'];
		$code = $row['pos_prac_code'];
		$stringAllPos.="'".str_replace("'","",$code)."',";
		$pos_id_arr[]=$id;
		$pos_codes_arr[$id]=$row["pos_prac_code"];
		$pos_data_arr[$id]=$row["pos_code"]."-".$row["pos_prac_code"];
	}
	$stringAllPos = substr($stringAllPos,0,-1);
	
	//----------------- DIAGNOSIS Data -----------------//
	$sql = "SELECT * FROM diagnosis_code_tbl order by d_prac_code ASC";
	$rezCodes = imw_query($sql);
	if(imw_num_rows($rezCodes) > 0){
		while($rowCodes=imw_fetch_array($rezCodes)){
			$dx_code=str_replace('"','',$rowCodes["dx_code"]);
			$diag_description=str_replace('"','',$rowCodes["diag_description"]);
			$d_prac_code=str_replace('"','',$rowCodes["d_prac_code"]);
			
			$arrDxCodechld[$rowCodes["dx_code"]]=$rowCodes["diag_description"];
			$arrDxCodechld[$dx_code]=$diag_description;
			
			$stringAllDiag.="'".str_replace("'","",$d_prac_code)."',";
			if($d_prac_code!=$dx_code){
				$stringAllDiag.="'".str_replace("'","",$dx_code)."',";
			}
			$stringAllDiag.="'".str_replace("'","",$diag_description)."',";
		}
	}		
	$stringAllDiag = substr($stringAllDiag,0,-1);	

	$cpt_alert_display_arr=array();
	$cpt_qry = imw_query("SELECT * FROM ins_cpt_alert WHERE del_by='0' and cpt_alert!='' order by id");
	if(imw_num_rows($cpt_qry)>0){
		while($cpt_rows=imw_fetch_assoc($cpt_qry)){
			$cpt_code_idArr=unserialize(html_entity_decode($cpt_rows['cpt_code_id']));
			foreach($cpt_code_idArr as $key => $val){
				$cpt_alert_display_arr[$cpt_rows['ins_id']][$val][]=$cpt_rows['cpt_alert'];
			}
		}
	}
	//----------------- Users Data -----------------//
	$sel_prov=imw_query("select id,lname,fname,mname,user_type from users order by lname,fname asc");
	while($fet_prov=imw_fetch_array($sel_prov)){
		if($fet_prov['user_type']=='1'){
			$phy_id_arr[]=$fet_prov['id'];
			$phy_id_name[$fet_prov['id']]=$fet_prov['lname'].', '.$fet_prov['fname'];
		}
		$arrAllUsers[$fet_prov['id']]=$fet_prov['lname'].', '.$fet_prov['fname'];
		$arrAllUsersInt[$fet_prov['id']]=substr($fet_prov['fname'],0,1).substr($fet_prov['mname'],0,1).substr($fet_prov['lname'],0,1);
	}
	
	$dos_to=preg_replace('/[^0-9 \-]+/','',$_REQUEST['dos_to']);
	$dos_frm=preg_replace('/[^0-9 \-]+/','',$_REQUEST['dos_frm']);
	$dos_to=xss_rem($_REQUEST['dos_to'], 1);
	$dos_frm=xss_rem($_REQUEST['dos_frm'], 1);
	if($dos_to=="" && $dos_frm==""){
		$dos_to=$cur_date;
		$dos_frm=$cur_date;
	}
	
	list($dm,$dd,$dy)=explode('-',$dos_to);
	list($dmf,$ddf,$dyf)=explode('-',$dos_frm);
	$qry_dos_to_date=$dy.'-'.$dm.'-'.$dd;
	$qry_dos_frm_date=$dyf.'-'.$dmf.'-'.$ddf;
	
	if($dos_to!="" && $dos_frm!=""){
		$qry_dos_superbill=" and superbill.dateOfService between '$qry_dos_frm_date' and '$qry_dos_to_date'";
		$qry_dos_chl=" patient_charge_list.date_of_service between '$qry_dos_frm_date' and '$qry_dos_to_date'";
		$qry_dos_app=" where sa_app_start_date between '$qry_dos_frm_date' and '$qry_dos_to_date'";
	}else if($dos_to!=""){
		$qry_dos_superbill=" and superbill.dateOfService = '$qry_dos_to_date'";
		$qry_dos_chl=" patient_charge_list.date_of_service = '$qry_dos_to_date'";
		$qry_dos_app=" where sa_app_start_date = '$qry_dos_to_date'";
	}else if($dos_frm!=""){
		$qry_dos_superbill=" and  superbill.dateOfService = '$qry_dos_frm_date'";
		$qry_dos_chl=" patient_charge_list.date_of_service = '$qry_dos_frm_date'";
		$qry_dos_app=" where sa_app_start_date = '$qry_dos_frm_date'";
	}
	
	$rec_app_arr=array();
	$qry_case_id = imw_query("select  sa_app_start_date,sa_app_starttime,sa_patient_id from schedule_appointments $qry_dos_app order by sa_app_start_date,sa_app_starttime");												
	while($fet_case_list=imw_fetch_array($qry_case_id)){
		$rec_app_arr[$fet_case_list['sa_patient_id']][$fet_case_list['sa_app_start_date']] = $fet_case_list['sa_app_start_date'].'-'.$fet_case_list['sa_app_starttime'];
	}
	
	$rec_sch_app_arr=array();
	if($qry_dos_app){
		$qry_dos_app_new=$qry_dos_app." and schedule_appointments.sa_patient_app_status_id not in(201,203)";
	}else{
		$qry_dos_app_new=" where  schedule_appointments.sa_patient_app_status_id not in(201,203)";
	}
	
	$sch_qry_chr = "select schedule_appointments.sa_patient_app_status_id,schedule_appointments.id,
			schedule_appointments.sa_facility_id, facility.name,schedule_appointments.case_type_id,
			schedule_appointments.sa_patient_id,schedule_appointments.sa_app_start_date,
			schedule_appointments.sa_doctor_id,patient_data.lname,patient_data.fname,patient_data.mname,
			slot_procedures.proc,schedule_appointments.sa_app_starttime,slot_procedures.non_billable
			from schedule_appointments join 
			patient_data on schedule_appointments.sa_patient_id = patient_data.id 
			left join facility on schedule_appointments.sa_facility_id = facility.id 
			left join slot_procedures on schedule_appointments.procedureid=slot_procedures.id
			$qry_dos_app_new";
	if($acc_fac_str!= ""){
		$sch_qry_chr .= " and facility.fac_prac_code in($acc_fac_str)";
	}
	if($provider_srh_str!= ""){
		$sch_qry_chr .= " and schedule_appointments.sa_doctor_id in($provider_srh_str)";
	}
	if($operator_srh_str!= ""){
		$sch_qry_chr .= " and schedule_appointments.status_update_operator_id in($operator_srh_str)";
	}
	$sch_qry_chr .= " order by schedule_appointments.sa_app_start_date,schedule_appointments.sa_app_starttime,
					patient_data.lname,patient_data.fname";
				
	$qry_case_id = imw_query($sch_qry_chr);												
	while($fet_case_list=imw_fetch_array($qry_case_id)){
		
		$patNameArr = array();
		$patNameArr["LAST_NAME"] = $fet_case_list['lname'];
		$patNameArr["FIRST_NAME"] = $fet_case_list['fname'];
		$patNameArr["MIDDLE_NAME"] = $fet_case_list['mname'];
		$patName = changeNameFormat($patNameArr);
		
		$intAppStatus = $fet_case_list['sa_patient_app_status_id'];
		$strAppStatus = "";
		switch ($intAppStatus):
			case 0:
				$strAppStatus = "New Appointment";
			break;
			case 2:
				$strAppStatus = "Chart Pulled";
			break;
			case 3:
				$strAppStatus = "No-Show";
			break;			
			case 6:
				$strAppStatus = "Left Without Visit";
			break;	
			case 7:
				$strAppStatus = "Insurance/Financial Issue";
			break;	
			case 11:
				$strAppStatus = "Check-out";
			break;
			case 13:
				$strAppStatus = "Check-in";
			break;
			case 17:
				$strAppStatus = "Confirmed";
			break;
			case 18:
				$strAppStatus = "Canceled";
			break;
			case 100:
				$strAppStatus = "Waiting for Surgery";
			break;
			case 101:
				$strAppStatus = "Scheduled for Surgery";
			break;
			case 200:
				$strAppStatus = "Room assignment";
			break;
			case 202:
				$strAppStatus = "Rescheduled";
			break;
		endswitch;
		
		$rec_sch_app_arr['sa_patient_name_id'][] = $patName.' - '.$fet_case_list['sa_patient_id'];
		$rec_sch_app_arr['sa_patient_id'][] = $fet_case_list['sa_patient_id'];
		$rec_sch_app_arr['sa_app_start_date'][] = $fet_case_list['sa_app_start_date'];
		$rec_sch_app_arr['proc'][] = $fet_case_list['proc'];
		$rec_sch_app_arr['sa_app_starttime'][] = $fet_case_list['sa_app_starttime'];
		$rec_sch_app_arr['sa_patient_app_status_id'][] = $strAppStatus;
		$rec_sch_app_arr['case_type_id'][$fet_case_list['sa_patient_id']][$fet_case_list['sa_app_start_date']] = $fet_case_list['case_type_id'];
		$rec_sch_app_arr['schedule_appointment_id'][] = $fet_case_list['id'];
		if($fet_case_list['non_billable']>0){
			$slot_proc_non_billed[$fet_case_list['id']]=$fet_case_list['non_billable'];
		}
	}
	$fac_sup_arr=array();
	if($acc_fac_str){
		for($k=0;$k<count($acc_fac_arr);$k++){
			$fac_sup_arr[]=$poc_fac_all_arr[$acc_fac_arr[$k]];
		}
		$fac_sup_str="'".implode("','",$fac_sup_arr)."'";
	}
	
	if($ins_case_type_str!=""){
		$qry_ins_case_type_join = " left join insurance_case on superbill.insuranceCaseId  = insurance_case.ins_caseid   ";	
		$qry_ins_case_type_where="";
		$fet_ins_case_type=",insurance_case.ins_case_type as chk_ins_case_type";					
	}
	else{
		$qry_ins_case_type_join = "";	
		$qry_ins_case_type_where = "";	
		$fet_ins_case_type="";
	}
	
	$sch_posid_arr=$sch_pos_id_arr=$sch_posid_sid_arr=$sch_pos_id_sid_arr=array();
	if($acc_fac_str){
		$sel_app="select facility.fac_prac_code,pos_facilityies_tbl.pos_id,
				schedule_appointments.sa_app_start_date,
				schedule_appointments.sa_patient_id,schedule_appointments.id
					from 
				schedule_appointments  as schedule_appointments,
				facility as facility,
				pos_facilityies_tbl as pos_facilityies_tbl
				$qry_dos_app 
				and schedule_appointments.sa_facility_id=facility.id
				and pos_facilityies_tbl.pos_facility_id =facility.fac_prac_code
				and fac_prac_code in($acc_fac_str)
				and schedule_appointments.sa_patient_app_status_id not in(3,18,201,203)
				order by schedule_appointments.sa_app_start_date,schedule_appointments.sa_app_starttime";
		$sel_appQry = imw_query($sel_app);
		while($sel_appRow = imw_fetch_array($sel_appQry)){
			$sch_posid_arr[$sel_appRow['sa_app_start_date']][$sel_appRow['sa_patient_id']]=$sel_appRow['fac_prac_code'];
			$sch_pos_id_arr[$sel_appRow['sa_app_start_date']][$sel_appRow['sa_patient_id']]=$sel_appRow['pos_id'];
			$sch_posid_sid_arr[$sel_appRow['sa_app_start_date']][$sel_appRow['sa_patient_id']][$sel_appRow['id']]=$sel_appRow['fac_prac_code'];
			$sch_pos_id_sid_arr[$sel_appRow['sa_app_start_date']][$sel_appRow['sa_patient_id']][$sel_appRow['id']]=$sel_appRow['pos_id'];
		}
	}	
	
	$qry_ins_chart_where="";
	if($view_chart!=""){
		$qry_ins_chart_join = " left join chart_master_table on chart_master_table.id = superbill.formId";	
		if($view_chart=="finalized"){
			$qry_ins_chart_where =" and chart_master_table.finalize='1'";
		}else if($view_chart=="un_finalized"){
			$qry_ins_chart_where =" and chart_master_table.finalize='0'";
		}else if($view_chart=="re_finalized"){
			$qry_ins_chart_where =" and superbill.modified_by>0 and chart_master_table.finalize='0'";
		}
		$rec_chart_log_arr=array();
		$qry = imw_query("select chart_save_log.form_id,chart_save_log.finalized,chart_save_log.dttime from superbill join chart_save_log on superbill.formId=chart_save_log.form_id where superbill.postedStatus = '0' and superbill.merged_with = '0' and superbill.del_status  = '0' $qry_dos_superbill order by chart_save_log.id asc");												
		while($qry_row=imw_fetch_assoc($qry)){
			$rec_chart_log_arr[$qry_row['form_id']][] = $qry_row;
		}
		$fet_chart_finalize=",chart_master_table.finalize";
	}
	
	if($view_srh!='Posted'){
		$qry_sup=imw_query("select 
				superbill.physicianId,superbill.dateOfService,superbill.patientId,
				superbill.encounterId,procedureinfo.cptCode,procedureinfo.dx1,procedureinfo.dx2,
				procedureinfo.dx3,procedureinfo.dx4,procedureinfo.dx5,procedureinfo.dx6,procedureinfo.dx7,
				procedureinfo.dx8,procedureinfo.dx9,procedureinfo.dx10,procedureinfo.dx11,procedureinfo.dx12,
				procedureinfo.modifier1,procedureinfo.modifier2,
				procedureinfo.modifier3,procedureinfo.modifier4,procedureinfo.units,superbill.pos,superbill.tos,superbill.arr_dx_codes as all_dx_codes,
				superbill.todaysCharges,superbill.coPay as copay,superbill.idSuperBill,procedureinfo.id as procedureinfo_id,
				procedureinfo.description as cpt_desc,
				superbill.insuranceCaseId as case_type_id,superbill.vipSuperBill,superbill.formId,
				superbill.pri_ins_id as primaryInsuranceCoId,superbill.sec_ins_id as secondaryInsuranceCoId,
				superbill.refferingPhysician,patient_data.lname  as lname,superbill.modified_date_time,superbill.modified_by,
				patient_data.fname  as fname,superbill.sch_app_id,
				patient_data.primary_care_id  as primary_care_id,
				patient_data.default_facility  as pt_default_facility
				$fet_ins_case_type $fet_chart_finalize
				 from 
				superbill join procedureinfo on
				superbill.idSuperBill=procedureinfo.idSuperBill
				join patient_data on
				patient_data.id=superbill.patientId
				$qry_ins_case_type_join
				$qry_ins_chart_join
				 where 
				superbill.postedStatus = '0'
				and superbill.merged_with = '0'
				and superbill.del_status  = '0'
				and procedureinfo.delete_status  = '0'
				$qry_dos_superbill
				$qry_prov_sup
				$qry_fac_sup
				$qry_ins_case_type_where
				$qry_ins_chart_where
				order by patient_data.lname,patient_data.fname,procedureinfo.porder,procedureinfo.id asc");
		while($fet_sup=imw_fetch_array($qry_sup)){
			$chart_refinalize=$rec_chart_log_old=0;
			$dttime="";
			if($fet_sup['idSuperBill']>0 && $view_chart=="re_finalized"){
				foreach($rec_chart_log_arr[$fet_sup['formId']] as $rec_chart_log_key => $rec_chart_log_val){
					$rec_chart_log=$rec_chart_log_arr[$fet_sup['formId']][$rec_chart_log_key];
					if($rec_chart_log['finalized']!=$rec_chart_log_old){
						$rec_chart_log_old=$rec_chart_log['finalized'];
						$dttime=$rec_chart_log['dttime'];
						$chart_refinalize++;
					}
				}
				if($chart_refinalize<=1 || $dttime>$fet_sup['modified_date_time']){
					continue;
				}
			}
			
			$physicianId = $fet_sup['physicianId'];
			$dateOfService = $fet_sup['dateOfService'];
			$dateOfService2 = $fet_sup['dateOfService'];
			$patientId  = $fet_sup['patientId'];
			$encounterId  = $fet_sup['encounterId'];
			$idSuperBill = $fet_sup['idSuperBill'];
			$pri_ins_id = $fet_sup['primaryInsuranceCoId'];
			$sec_ins_id = $fet_sup['secondaryInsuranceCoId'];
			$primary_care_id = $fet_sup['primary_care_id'];
			$sup_case_type_id="";
			$pat_fac_id_arr[$patientId]=$fet_sup['pt_default_facility'];
			if($primary_care_id>0){
				$pat_ref_phy_arr[$primary_care_id]=$primary_care_id;
			}
			if($fet_sup['refferingPhysician']>0){
				$pat_ref_phy_arr[$fet_sup['refferingPhysician']]=$fet_sup['refferingPhysician'];
			}
			if($cpt_desc_arr[$fet_sup['cptCode']]!=""){
				$fet_sup['cpt_desc']=$cpt_desc_arr[$fet_sup['cptCode']];
			}
			$fet_sup['all_dx_codes'] = remove_spec_dx($fet_sup['all_dx_codes']);
			if((in_array($fet_sup['pos'],$fac_sup_arr) or $fac_sup_str=="" or count($acc_fac_arr)==$srh_tot_poc_fac) && ($slot_proc_non_billed[$fet_sup['sch_app_id']]<=0)){
				if(in_array($fet_sup['chk_ins_case_type'],$ins_case_type_arr) or $ins_case_type_str==""){
					if(in_array($fet_sup['physicianId'],$provider_srh_arr) or $provider_srh_str==""){
						$all_sup_dx_codes_arr[]=unserialize(html_entity_decode($fet_sup['all_dx_codes']));
						if($fet_sup['case_type_id']==0){
							$sup_case_type_id=$rec_sch_app_arr['case_type_id'][$patientId][$dateOfService];
							$fet_sup['case_type_id']=$sup_case_type_id;
							if($sup_case_type_id==0){
								$act_ins_caseid_arr=array();
								$getInsPriCoStr="SELECT * FROM insurance_data WHERE pid='$patientId' AND actInsComp = '1' AND provider!=''";
								$getInsPriCoQry = imw_query($getInsPriCoStr);
								while($getInsPriCoRow=@imw_fetch_array($getInsPriCoQry)){
									$act_ins_caseid_arr[$getInsPriCoRow['ins_caseid']]=$getInsPriCoRow['ins_caseid'];
								}
								$act_ins_caseid_imp=implode(',',$act_ins_caseid_arr);
								$getInsCaseStr = "SELECT insurance_case.ins_caseid,insurance_case_types.normal FROM 
								insurance_case join insurance_case_types on insurance_case.ins_case_type=insurance_case_types.case_id 
								WHERE insurance_case.patient_id='$patientId' AND insurance_case.case_status='Open' and insurance_case.ins_caseid in($act_ins_caseid_imp)";
								$getInsCase=@imw_query($getInsCaseStr);
								while($getInsCaseRow=@imw_fetch_array($getInsCase)){
									if($getInsCaseRow['normal']=='1'){
										$normal_ins_case_id=$getInsCaseRow['ins_caseid'];
									}
								}
								if($normal_ins_case_id>0){
									$fet_sup['case_type_id']=$normal_ins_case_id;
									$sup_case_type_id=$normal_ins_case_id;
								}
							}
							imw_query("update superbill set insuranceCaseId='$sup_case_type_id' where idSuperBill='$idSuperBill' and patientId='$patientId'");
						}
						$primary_ins_id_chk=0;
						if($fet_sup['case_type_id']>0 && ($pri_ins_id==0 || $sec_ins_id==0)){
							$case_type_id=$fet_sup['case_type_id'];
							if($acc_ins_str!=""){
								$prov_whr=" and provider in($acc_ins_str)";
							}
							$getPrimaryInsCoDetails = imw_query("SELECT copay,provider,type FROM insurance_data WHERE
																 ins_caseid='$case_type_id' AND pid='$patientId' and actInsComp = 1 
																 and (date_format(effective_date,'%Y-%m-%d')<='$dateOfService') $prov_whr");
						}
						$pri_ins_copay=0;
						$sec_ins_copay=0;
						if(imw_num_rows($getPrimaryInsCoDetails)>0 && $fet_sup['case_type_id']>0){
							while($row_pri_ins=imw_fetch_array($getPrimaryInsCoDetails)){
								if($row_pri_ins['type']=="primary" && $pri_ins_id==0){
									$pri_ins_id=$row_pri_ins['provider'];
									$up_sup=imw_query("update superbill set pri_ins_id='$pri_ins_id' where idSuperBill='$idSuperBill'");
									$fet_sup['primaryInsuranceCoId']=$pri_ins_id;
								}
								if($row_pri_ins['type']=="secondary" && $sec_ins_id==0){
									$sec_ins_id=$row_pri_ins['provider'];
									$up_sup=imw_query("update superbill set sec_ins_id='$sec_ins_id' where idSuperBill='$idSuperBill'");
									$fet_sup['secondaryInsuranceCoId']=$sec_ins_id;
								}
								if($row_pri_ins['type']=="tertiary"){
									$fet_sup['tertiaryInsuranceCoId']=$row_pri_ins['provider'];
								}
								if($row_pri_ins['type']=="primary"){
									$pri_ins_copay=$row_pri_ins['copay'];
								}
								if($row_pri_ins['type']=="secondary"){
									$sec_ins_copay=$row_pri_ins['copay'];
								}
							}
							if($fet_sup['copay']<=0){
								$pri_id_ins=$fet_sup['primaryInsuranceCoId'];
								$sec_id_ins=$fet_sup['secondaryInsuranceCoId'];
								$tot_copay=0;
								$pri_copay=0;
								$sec_copay=0;
								if($pri_id_ins>0){
									$copay_proc_code_arr=array();
									$copay_proc_code_qry=imw_query("select cptCode from procedureinfo where delete_status='0' and idSuperBill='$idSuperBill'");
									while($copay_proc_code_row=imw_fetch_array($copay_proc_code_qry)){
										$copay_proc_code_arr[] =$copay_proc_code_row['cptCode'];
									}
									$cpt_prac_code_imp=implode(',',$copay_proc_code_arr);
									$copay_collect_chk=copay_apply_chk($cpt_prac_code_imp,$pri_id_ins,$sec_id_ins);
									
									$copay_policies = ChkSecCopay_collect($pri_id_ins);
									$secCopay_collect_chk=$copay_policies;
									
									if($copay_collect_chk[0]==true && $copay_collect_chk[1]==true){
										$pri_copay=$pri_ins_copay;
										if($secCopay_collect_chk=='Yes'){
											$sec_copay=$sec_ins_copay;
										}
										$tot_copay=$pri_copay+$sec_copay;
									}else if($copay_collect_chk[0]==true){
										$pri_copay=$pri_ins_copay;
										$sec_copay=0;
										$tot_copay=$pri_copay+$sec_copay;
									}else if($copay_collect_chk[1]==true && $secCopay_collect_chk=='Yes'){
										$pri_copay=0;
										$sec_copay=$sec_ins_copay;
										$tot_copay=$pri_copay+$sec_copay;
									}
									$fet_sup['copay']=$tot_copay;
									$up_sup=imw_query("update superbill set copay='$tot_copay',pri_copay='$pri_copay',sec_copay='$sec_copay' where idSuperBill='$idSuperBill'");
								}
							}else{
								if($pri_ins_copay==0 && $sec_ins_copay==0){
									$up_sup=imw_query("update superbill set copay='0',pri_copay='0',sec_copay='0' where idSuperBill='$idSuperBill'");
								}
							}
						}else{
							if($fet_sup['copay']>0 && $pri_ins_copay==0 && $sec_ins_copay==0){
								$up_sup=imw_query("update superbill set copay='0',pri_copay='0',sec_copay='0' where idSuperBill='$idSuperBill'");
							}
						}
						if(($fet_sup['case_type_id']==0 || !in_array($fet_sup['primaryInsuranceCoId'],$acc_ins_arr)) && $acc_ins_str!=""){
						}else{
							if($acc_fac_str!=""){
								$pos_id_chk="";
								$pos_id_chk=$sch_posid_arr[$dateOfService][$patientId];
								if(count($acc_fac_arr)==$srh_tot_poc_fac || ($pos_id_chk>0 && in_array($pos_id_chk,$acc_fac_arr))){
									$cptCode_arr[] = $fet_sup['cptCode'];
									$pat_arr[]=$patientId;
									if($ord_by=="billing_provider"){
										$data_detail[$physicianId][$dateOfService][$patientId][$encounterId][]=$fet_sup;
									}else if($ord_by=="credited_provider"){
										$data_detail[0][$dateOfService][$patientId][$encounterId][]=$fet_sup;
									}else if($ord_by=="pri_ins"){
										$sup_pri_qry=imw_query("select pri_ins_id from superbill where idSuperBill='$idSuperBill'");
										if(imw_num_rows($sup_pri_qry)>0){
											$sup_pri_sel=imw_fetch_array($sup_pri_qry);
											$pri_ins_id=$sup_pri_sel['pri_ins_id'];
										}else{
											$pri_ins_id=0;
										}
										$data_detail[$pri_ins_id][$dateOfService][$patientId][$encounterId][]=$fet_sup;
									}else if($ord_by=="pt_last_name"){
										$data_detail[0][111111111111111][$patientId][$encounterId][]=$fet_sup;
									}else{
										if($rec_app_arr[$patientId][$dateOfService] != ""){
											$date_Of_Service=str_replace(":","",str_replace("-","",$rec_app_arr[$patientId][$dateOfService]));
											$data_detail[0][$date_Of_Service][$patientId][$encounterId][]=$fet_sup;	
										}else{
											$data_detail[0][111111111111111][$patientId][$encounterId][]=$fet_sup;
										}
									}
									$sch_posid[$dateOfService][$patientId]=$sch_posid_arr[$dateOfService][$patientId];
								}
							}else{
								$cptCode_arr[] = $fet_sup['cptCode'];
								$pat_arr[]=$patientId;
								if($ord_by=="billing_provider"){
									$data_detail[$physicianId][$dateOfService][$patientId][$encounterId][]=$fet_sup;
								}else if($ord_by=="credited_provider"){
									$data_detail[0][$dateOfService][$patientId][$encounterId][]=$fet_sup;
								}else if($ord_by=="pri_ins"){
									$sup_pri_qry=imw_query("select pri_ins_id from superbill where idSuperBill='$idSuperBill'");
									if(imw_num_rows($sup_pri_qry)>0){
										$sup_pri_sel=imw_fetch_array($sup_pri_qry);
										$pri_ins_id=$sup_pri_sel['pri_ins_id'];
									}else{
										$pri_ins_id=0;
									}
									$data_detail[$pri_ins_id][$dateOfService][$patientId][$encounterId][]=$fet_sup;
								}else if($ord_by=="pt_last_name"){
									$data_detail[0][111111111111111][$patientId][$encounterId][]=$fet_sup;
								}else{
									if($rec_app_arr[$patientId][$dateOfService] != ""){
										$date_Of_Service=str_replace(":","",str_replace("-","",$rec_app_arr[$patientId][$dateOfService]));
										$data_detail[0][$date_Of_Service][$patientId][$encounterId][]=$fet_sup;	
									}else{
										$data_detail[0][111111111111111][$patientId][$encounterId][]=$fet_sup;
									}
								}
								$sch_posid[$dateOfService][$patientId]=$sch_posid_arr[$dateOfService][$patientId];
							}
						}
					}
				}
			}
			$data_detail_for_sch[$dateOfService2][$patientId]=$fet_sup;
		}	
	}
	if($view_srh=='Posted'){
		$posted_qry="and (patient_charge_list.submitted='true' and patient_charge_list_details.posted_status='1')";
	}else if($view_srh=='Not Posted'){
		$posted_qry="and (patient_charge_list.submitted='false' or patient_charge_list_details.posted_status='0')";
	}
	if($ins_case_type_str!=""){
		$qry_ins_case_type_join = " left join insurance_case on patient_charge_list.case_type_id = insurance_case.ins_caseid   ";	
		$qry_ins_case_type_where="";
		$fet_ins_case_type=",insurance_case.ins_case_type as chk_ins_case_type";
	}
	else{
		$qry_ins_case_type_join = "";	
		$qry_ins_case_type_where = "";	
		$fet_ins_case_type="";
	}
	if($view_srh=='Posted' || $view_srh=='Not Posted'){
		$sch_qry_chld=imw_query("select date_of_service,patient_id from patient_charge_list where del_status='0' and $qry_dos_chl");
		while($sch_fet_chld=imw_fetch_array($sch_qry_chld)){
			$data_detail_for_sch[$sch_fet_chld['date_of_service']][$sch_fet_chld['patient_id']]=$sch_fet_chld;
		}
	}
	if($qry_ins_chart_where==""){
		$qry_chld=imw_query("select 
			patient_charge_list.charge_list_id,
			patient_charge_list.primaryProviderId as physicianId,
			patient_charge_list.secondaryProviderId as secondaryProviderId,
			patient_charge_list.tertiaryProviderId as tertiaryProviderId,
			patient_charge_list.date_of_service as dateOfService,
			patient_charge_list.patient_id as patientId,
			patient_charge_list.encounter_id as encounterId,
			patient_charge_list.copay as copay,
			patient_charge_list.case_type_id as case_type_id,
			patient_charge_list.primaryInsuranceCoId as primaryInsuranceCoId,
			patient_charge_list.secondaryInsuranceCoId as secondaryInsuranceCoId,
			patient_charge_list.tertiaryInsuranceCoId as tertiaryInsuranceCoId,
			patient_charge_list.copayPaid as copayPaid,
			patient_charge_list.totalAmt as chl_totalAmt,
			patient_charge_list.totalBalance as chl_totalBalance,
			patient_charge_list.submitted as submitted,
			patient_charge_list.auth_no as auth_no,patient_charge_list.reff_phy_id as pcl_reff_phy_id,
			patient_charge_list.facility_id,patient_charge_list.billing_facility_id,patient_charge_list.all_dx_codes,patient_charge_list.operator_id,
			patient_charge_list_details.charge_list_detail_id  as charge_list_detail_id,
			patient_charge_list_details.diagnosis_id1 as dx1,
			patient_charge_list_details.diagnosis_id2 as dx2,
			patient_charge_list_details.diagnosis_id3 as dx3,
			patient_charge_list_details.diagnosis_id4 as dx4,
			patient_charge_list_details.diagnosis_id5 as dx5,
			patient_charge_list_details.diagnosis_id6 as dx6,
			patient_charge_list_details.diagnosis_id7 as dx7,
			patient_charge_list_details.diagnosis_id8 as dx8,
			patient_charge_list_details.diagnosis_id9 as dx9,
			patient_charge_list_details.diagnosis_id10 as dx10,
			patient_charge_list_details.diagnosis_id11 as dx11,
			patient_charge_list_details.diagnosis_id12 as dx12,
			patient_charge_list_details.modifier_id1  as modifier_id1,
			patient_charge_list_details.modifier_id2  as modifier_id2,
			patient_charge_list_details.modifier_id3  as modifier_id3,
			patient_charge_list_details.modifier_id4  as modifier_id4,
			patient_charge_list_details.units as units,
			patient_charge_list_details.place_of_service as place_of_service,
			patient_charge_list_details.type_of_service as type_of_service,
			patient_charge_list_details.procCharges as procCharges,
			patient_charge_list_details.totalAmount as totalAmount,
			patient_charge_list_details.approvedAmt as approvedAmt,
			patient_charge_list_details.paidForProc as paidForProc,
			patient_charge_list_details.posFacilityId as posFacilityId,
			patient_charge_list_details.idSuperBill as chl_superbill_id,
			patient_charge_list_details.referral as referral,
			patient_charge_list_details.posted_status as posted_status,
			patient_charge_list_details.proc_selfpay,
			patient_charge_list.case_type_id as ins_case_type,
			patient_charge_list.enc_icd10,
			cpt_fee_tbl.cpt_prac_code as cptCode,
			cpt_fee_tbl.cpt_desc as cpt_desc,
			patient_data.lname  as lname,
			patient_data.fname  as fname, 
			patient_data.default_facility  as pt_default_facility
			$fet_ins_case_type
			 from 
			patient_charge_list  join patient_charge_list_details on
			patient_charge_list.charge_list_id=patient_charge_list_details.charge_list_id
			join cpt_fee_tbl on
			cpt_fee_tbl.cpt_fee_id = patient_charge_list_details.procCode
			join patient_data on
			patient_data.id=patient_charge_list.patient_id
			$qry_ins_case_type_join
			where patient_charge_list_details.del_status='0' and $qry_dos_chl
			$qry_prov_chl $posted_qry
			$qry_fac_chl
			$qry_ins_chl
			$qry_ins_case_type_where
			order by patient_data.lname,patient_data.fname,patient_charge_list_details.display_order asc");
	
		while($fet_chld=imw_fetch_array($qry_chld)){
			$physicianId = $fet_chld['physicianId'];
			$dateOfService = $fet_chld['dateOfService'];
			$dateOfService2 = $fet_chld['dateOfService'];
			$patientId   = $fet_chld['patientId'];
			$encounterId  = $fet_chld['encounterId'];
			$pri_ins_id  = $fet_chld['primaryInsuranceCoId'];
			$pat_arr[]=$patientId;
			$pat_fac_id_arr[$patientId]=$fet_chld['pt_default_facility'];
			if($fet_chld['pcl_reff_phy_id']>0){
				$pat_ref_phy_arr[$fet_chld['pcl_reff_phy_id']]=$fet_chld['pcl_reff_phy_id'];
			}
			$chk_posted=1;
			if($view_srh=='Posted'){
				if($fet_chld['submitted']=='true' && $fet_chld['posted_status']=='1'){
					$chk_posted="";
				}
			}else if($view_srh=='Not Posted'){
				if($fet_chld['submitted']=='false' && $fet_chld['posted_status']=='0'){
					$chk_posted="";
				}
			}else{
				$chk_posted="";
			}
			$provider_srh_chk=1;
			if($provider_srh_str!=""){
				if($provider_type==1){
					if(in_array($fet_chld['physicianId'],$provider_srh_arr)){
						$provider_srh_chk="";
					}
				}else if($provider_type==2){
					if(in_array($fet_chld['secondaryProviderId'],$provider_srh_arr)){
						$provider_srh_chk="";
					} 
				}else{
					if(in_array($fet_chld['physicianId'],$provider_srh_arr) or  in_array($fet_chld['secondaryProviderId'],$provider_srh_arr) or in_array($fet_chld['tertiaryProviderId'],$provider_srh_arr)){
						$provider_srh_chk="";
					}
				}
			}else{
				$provider_srh_chk="";
			}
			$operator_srh_chk=1;
			if($operator_srh_str!=""){
				if(in_array($fet_chld['operator_id'],$operator_srh_arr)){
					$operator_srh_chk="";
				} 
			}else{
				$operator_srh_chk="";
			}
			if($chk_posted==""){
				if(in_array($fet_chld['facility_id'],$acc_fac_arr) or $acc_fac_str==""){
					$all_sup_dx_codes_arr[]=unserialize(html_entity_decode($fet_chld['all_dx_codes']));
					if(in_array($fet_chld['primaryInsuranceCoId'],$acc_ins_arr) or $acc_ins_str==""){
						if($provider_srh_chk=="" && $operator_srh_chk==""){
							if((in_array($fet_chld['chk_ins_case_type'],$ins_case_type_arr) and ($fet_chld['primaryInsuranceCoId']>0 or $fet_chld['secondaryInsuranceCoId'] or $fet_chld['tertiaryInsuranceCoId'])) or $ins_case_type_str==""){
								if($ord_by=="pri_ins"){
									if($ord_by=="billing_provider"){
										$data_detail[$physicianId][$dateOfService][$patientId][$encounterId][]=$fet_chld;
									}else if($ord_by=="credited_provider"){
										$data_detail[$fet_chld['secondaryProviderId']][$dateOfService][$patientId][$encounterId][]=$fet_chld;
									}else if($ord_by=="pri_ins"){
										$data_detail[$pri_ins_id][$dateOfService][$patientId][$encounterId][]=$fet_chld;
									}else if($ord_by=="pt_last_name"){
										$data_detail[0][111111111111111][$patientId][$encounterId][]=$fet_chld;
									}else{
										if($rec_app_arr[$patientId][$dateOfService] != ""){
											$date_Of_Service=str_replace(":","",str_replace("-","",$rec_app_arr[$patientId][$dateOfService]));
											$data_detail[0][$date_Of_Service][$patientId][$encounterId][]=$fet_chld;
										}else{
											$data_detail[0][111111111111111][$patientId][$encounterId][]=$fet_chld;
										}
										
									}
								}else{
									if($ord_by=="billing_provider"){
										$data_detail[$physicianId][$dateOfService][$patientId][$encounterId][]=$fet_chld;
									}else if($ord_by=="credited_provider"){
										$data_detail[$fet_chld['secondaryProviderId']][$dateOfService][$patientId][$encounterId][]=$fet_chld;
									}else if($ord_by=="pri_ins"){
										$data_detail[$pri_ins_id][$dateOfService][$patientId][$encounterId][]=$fet_chld;
									}else if($ord_by=="pt_last_name"){
										$data_detail[0][111111111111111][$patientId][$encounterId][]=$fet_chld;
									}else{
										if($rec_app_arr[$patientId][$dateOfService] != ""){
											$date_Of_Service=str_replace(":","",str_replace("-","",$rec_app_arr[$patientId][$dateOfService]));
											$data_detail[0][$date_Of_Service][$patientId][$encounterId][]=$fet_chld;
										}else{
											$data_detail[0][111111111111111][$patientId][$encounterId][]=$fet_chld;
										}
										
									}	
								}
							}
						}
					}
				}
			}
			$data_detail_for_sch[$dateOfService2][$patientId]=$fet_chld;
		}
	}
	$pat_imp=implode("','",array_unique($pat_arr));
	$qry_case = imw_query("SELECT insct.case_name,insct.vision,insct.normal,insc.ins_caseid,insc.ins_case_type,insc.patient_id,insd.type,
						insd.referal_required,insd.auth_required
						FROM insurance_case_types insct 
						JOIN insurance_case insc ON (insc.ins_case_type=insct.case_id AND insc.case_status='Open') 
						JOIN insurance_data insd ON (insd.ins_caseid=insc.ins_caseid AND insd.provider>0) 
						JOIN insurance_companies inscomp ON (inscomp.id=insd.provider AND inscomp.in_house_code != 'n/a') 
						WHERE insc.patient_id in('$pat_imp') ORDER BY insc.ins_case_type,insd.actInsComp");
	while($caseRes = imw_fetch_array($qry_case)){
		$patient_id = $caseRes['patient_id'];
		$ins_caseid = $caseRes['ins_caseid'];
		$insCasesOption[$patient_id][]=$caseRes;
		$insCasesOption_data[$patient_id][$ins_caseid]=$caseRes;
		$act_ins_caseid_data_arr[$patient_id][$ins_caseid][$caseRes['type']]=$caseRes['referal_required'];
		$act_ins_caseid_auth_data_arr[$patient_id][$ins_caseid][$caseRes['type']]=$caseRes['auth_required'];
	}
	if($cptCode_arr){
		$cptCode_imp=implode("','",array_unique($cptCode_arr));
		$sup_cpt_fee_arr=array();
		$fee_whr="";
		if($billing_amount=='Default'){
			$fee_whr=" AND a.fee_table_column_id = '1'";
		}
		$getCPTPriceQry = imw_query("SELECT cpt_fee,cpt4_code,cpt_prac_code,a.fee_table_column_id FROM cpt_fee_table a,
										cpt_fee_tbl b
										WHERE 
										b.cpt4_code in('$cptCode_imp')
										AND a.cpt_fee_id = b.cpt_fee_id
										$fee_whr
										order by b.delete_status desc,b.status asc");
		while($getCPTPriceRow = imw_fetch_assoc($getCPTPriceQry)){
			$sup_cpt_fee_arr[$getCPTPriceRow['fee_table_column_id']][$getCPTPriceRow['cpt4_code']]=$getCPTPriceRow['cpt_fee'];
		}
		$getCPTPriceQry = imw_query("SELECT cpt_fee,cpt4_code,cpt_prac_code,a.fee_table_column_id FROM cpt_fee_table a,
										cpt_fee_tbl b
										WHERE 
										b.cpt_prac_code in('$cptCode_imp') 
										AND a.cpt_fee_id = b.cpt_fee_id
										$fee_whr
										order by b.delete_status desc,b.status asc");
		while($getCPTPriceRow = imw_fetch_assoc($getCPTPriceQry)){
			$sup_cpt_fee_arr[$getCPTPriceRow['fee_table_column_id']][$getCPTPriceRow['cpt_prac_code']]=$getCPTPriceRow['cpt_fee'];
		}
	}
	
	$chk_in_pay=array();
	$sel_check_in_payment=imw_query("select sum(total_payment) as total_payment,patient_id,created_on,payment_id,sch_id,group_concat(payment_id) as payment_ids
									 from  check_in_out_payment where del_status='0' and total_payment>0 group by sch_id order by payment_id");
	while($fet_check_in_out = imw_fetch_assoc($sel_check_in_payment)){	
		$chk_in_pay[$fet_check_in_out['patient_id']][$fet_check_in_out['created_on']]['total_payment']=$fet_check_in_out['total_payment'];
		$chk_in_pay[$fet_check_in_out['patient_id']][$fet_check_in_out['created_on']]['sch_id']=$fet_check_in_out['sch_id'];
		$chk_in_pay[$fet_check_in_out['patient_id']][$fet_check_in_out['created_on']]['payment_id']=$fet_check_in_out['payment_ids'];
	}
	$pt_pmt=array();
	$pt_all_pmt=array();
	$sel_pt_pmt_payment=imw_query("select paid_amount,patient_id,entered_date,id,apply_payment_type,apply_amount
								   from patient_pre_payment where del_status='0' and paid_amount>0 order by id");
	while($fet_pt_pmt = imw_fetch_assoc($sel_pt_pmt_payment)){	
		if($fet_pt_pmt['apply_payment_type']=='manually'){
			$pt_pmt[$fet_pt_pmt['patient_id']][$fet_pt_pmt['entered_date']]['apply_amount'][]=$fet_pt_pmt['apply_amount'];
			$pt_all_pmt[$fet_pt_pmt['patient_id']]['apply_amount'][]=$fet_pt_pmt['apply_amount'];
		}
		$pt_pmt[$fet_pt_pmt['patient_id']][$fet_pt_pmt['entered_date']]['id'][]=$fet_pt_pmt['id'];
		$pt_pmt[$fet_pt_pmt['patient_id']][$fet_pt_pmt['entered_date']]['paid_amount'][]=$fet_pt_pmt['paid_amount'];
		
		$pt_all_pmt[$fet_pt_pmt['patient_id']]['id'][]=$fet_pt_pmt['id'];
		$pt_all_pmt[$fet_pt_pmt['patient_id']]['paid_amount'][]=$fet_pt_pmt['paid_amount'];
	}
	
	$chk_in_main_ids=array();
	$sel_check_in_copay=imw_query("select check_in_out_payment_details.id,check_in_out_payment_details.payment_id,check_in_out_payment_details.item_id,
								   check_in_out_payment_details.item_payment,check_in_out_fields.item_name
								   from check_in_out_payment_details join check_in_out_fields 
									 on check_in_out_payment_details.item_id=check_in_out_fields.id 
								   where check_in_out_payment_details.status='0'");
	while($fet_check_in_out_copay = imw_fetch_assoc($sel_check_in_copay)){	
		if($fet_check_in_out_copay['item_payment']>0){
			if(strtolower($fet_check_in_out_copay['item_name'])=="copay" || strtolower($fet_check_in_out_copay['item_name'])=="copay-visit" || strtolower($fet_check_in_out_copay['item_name'])=="copay-test"){
				$chk_in_main_ids[$fet_check_in_out_copay['payment_id']]=$fet_check_in_out_copay['payment_id'];
			}
			$ci_main_id[$fet_check_in_out_copay['id']]=$fet_check_in_out_copay['payment_id'];
		}
	}
	
	$reff_qry=imw_query("select patient_id,reffral_no,reff_type,reff_phy_id,insCaseid,effective_date,end_date from patient_reff 
						where patient_id in('$pat_imp') and no_of_reffs > '0' and del_status='0' 
						order by end_date desc,reff_id desc");
	while($reff_row=imw_fetch_array($reff_qry)){
		$pat_reff_arr[$reff_row['patient_id']][]=$reff_row;
		if($reff_row['reff_phy_id']>0){
			$pat_ref_phy_arr[$reff_row['reff_phy_id']]=$reff_row['reff_phy_id'];
		}
	}
	
	$reff_qry=imw_query("select a_id,patient_id,auth_name,AuthAmount,ins_case_id,ins_provider,ins_data_id,ins_type,auth_date,end_date,auth_provider,auth_cpt_codes from patient_auth 
						where patient_id in('$pat_imp') and auth_status='0' and no_of_reffs>0 order by a_id desc");
	while($reff_row=imw_fetch_array($reff_qry)){
		$pat_auth_arr[$reff_row['patient_id']][]=$reff_row;
	}
	
	$qry=imw_query("select ref_amt,pmt_id,ci_co_id from ci_pmt_ref where patient_id in('$pat_imp') and del_status='0'");
	while($row=imw_fetch_array($qry)){
		if($row['pmt_id']>0){
			$pt_pmt_ref_arr[$row['pmt_id']][]=$row['ref_amt'];
		}
		if($row['ci_co_id']>0){
			$ci_co_ref_arr[$ci_main_id[$row['ci_co_id']]][]=$row['ref_amt'];
		}
	}
	
	$qry=imw_query("select id,manually_payment,check_in_out_payment_id,acc_payment_id from check_in_out_payment_post where patient_id in('$pat_imp') and status='0'");
	while($row=imw_fetch_array($qry)){
		if($row['manually_payment']>0){
			$ci_co_post_arr[$row['check_in_out_payment_id']][] = $row['manually_payment'];
		}
		$cico_pcpi_ids_arr[$row['acc_payment_id']]=$row['check_in_out_payment_id'];
	}
	
	$qry = imw_query("SELECT pcdpi.paidForProc,pcdpi.overPayment,pcdpi.patient_pre_payment_id,pcpi.payment_id
					  FROM patient_chargesheet_payment_info as pcpi join patient_charges_detail_payment_info as pcdpi
					  on pcpi.payment_id=pcdpi.payment_id
					  join patient_charge_list on patient_charge_list.encounter_id=pcpi.encounter_id
					  WHERE pcdpi.deletePayment ='0' and pcdpi.unapply='0'
					  and patient_charge_list.patient_id in('$pat_imp')
					  group by pcdpi.payment_details_id");
	while($row = imw_fetch_array($qry)){
		if($row['patient_pre_payment_id']>0){
			$pt_pmt_post_arr[$row['patient_pre_payment_id']][] = $row['overPayment']+$row['paidForProc'];
		}
		if($cico_pcpi_ids_arr[$row['payment_id']]>0){
			$ci_co_post_arr[$cico_pcpi_ids_arr[$row['payment_id']]][] = $row['overPayment']+$row['paidForProc'];
		}
	}
}
								
if($ord_by=="pt_last_name"){
	$sel_ord_pat=imw_query("select id from patient_data where id in('$pat_imp') group by id order by lname,fname");
	while($row_ord_pat=imw_fetch_array($sel_ord_pat)){
		$pat_id_row_arr[]=$row_ord_pat['id'];
	}
}

for($hk=0;$hk<=count($all_sup_dx_codes_arr);$hk++){
	$all_dx_codes_imp.=implode(',',$all_sup_dx_codes_arr[$hk]);
}
$all_dx_codes_arr=array_values(array_unique(array_filter(explode(',',$all_dx_codes_imp))));

//pre($all_dx_codes_arr);exit();
if(count($all_dx_codes_arr)>0){
	$icd10_desc_arr=get_icd10_desc($all_dx_codes_arr,0);
}

//----------------- Referring Physician -----------------//
if(count($pat_ref_phy_arr)>0){
	$pat_ref_phy_imp=implode(",",$pat_ref_phy_arr);
	$sel_prov=imw_query("select physician_Reffer_id,LastName,FirstName,MiddleName from refferphysician where physician_Reffer_id in($pat_ref_phy_imp) order by LastName,FirstName asc");
	while($fet_prov=imw_fetch_array($sel_prov)){
		$arrAllRef[$fet_prov['physician_Reffer_id']]=$fet_prov['LastName'].', '.$fet_prov['FirstName'];
		$arrAllRefInt[$fet_prov['physician_Reffer_id']]=substr($fet_prov['FirstName'],0,1).substr($fet_prov['MiddleName'],0,1).substr($fet_prov['LastName'],0,1);
	}
}
?>
<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'] ?>/library/messi/messi.css">
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/messi/messi.js"></script>
<script type="text/javascript">
	var customarrayTos;
	var customarrayPos;
	<?php
	if($stringAllProcedures!=""){
	?>
		var customarrayProcedure= new Array(<?php echo remLineBrk($stringAllProcedures); ?>);
	<?php
	}if($stringAllDiag!=""){
	?>
		var customarrayDiag= new Array(<?php echo remLineBrk($stringAllDiag); ?>);
	<?php
	}if($stringAllModifiers!=""){
	?>
		var customarrayModifiers= new Array(<?php echo remLineBrk($stringAllModifiers); ?>);
	<?php
	}if($stringAllTos!=""){
		?>
		var customarrayTos= new Array(<?php echo remLineBrk($stringAllTos); ?>);
	<?php
	}if($stringAllPos!=""){
	?>
		var customarrayPos= new Array(<?php echo remLineBrk($stringAllPos); ?>);
	<?php
	}
	?>
	
	$(document).ready(function(){
		var n = $(".diagText_all_css").length;
		$('#alt_clr_tbl tr').click(function(){
			if($(this).attr('class')!='text_b_w' && $(this).attr('bgcolor') != '#009900'){
				if($(this).attr('class')!='alt'){
					$(this).addClass('alt');
				}else{
					$(this).removeClass('alt');	
				}
			}
		});
	});
</script>

<div class="purple_bar"><label>Day Charges Search</label></div>
<!-- Search Block -->
<div class="pt10">
    <form name="srh_frm" method="post" action="day_charges_list.php?search=yes">
        <div class="pull-left" style="width:10%; padding-right:10px;">
            <label>Provider Type</label>
            <select name="provider_type" id="provider_type" class="selectpicker" data-width="100%">
                <option value="" <?php if($provider_type <=0){ echo ("selected"); } ?>>Select Type</option>
                <option value="1" <?php if($provider_type == 1){ echo ("selected"); } ?>>Billing Provider</option>
                <option value="2" <?php if($provider_type == 2){ echo ("selected"); } ?>>Credited Provider</option>
            </select>	
        </div>
        <div class="pull-left" style="width:11%; padding-right:10px;">
            <label>Provider</label>
            <select name="provider_srh[]" id="provider_srh" class="selectpicker" data-width="100%" multiple data-actions-box="true" data-title="Select Provider" data-header="Select Provider" data-size="15">
                <?php echo $provider_options;?>
            </select>
        </div>
        <div class="pull-left" style="width:10%; padding-right:10px;">
            <label>Operator</label>
            <select name="operator_srh[]" id="operator_srh" class="selectpicker" data-width="100%" multiple data-actions-box="true" data-title="All Operator" data-header="All Operator" data-size="15">
                <?php echo $operator_options;?>
            </select>	
        </div>
        <div class="pull-left" style="width:9%; padding-right:10px;">
            <input type="hidden" name="srh_tot_poc_fac" value="<?php echo count($poc_fac_opt_arr); ?>">
            <label>Facility</label>	
            <select name="acc_fac[]" id="acc_fac" class="selectpicker" data-width="100%" multiple data-actions-box="true" data-title="Select Facility"  data-header="Select Facility" data-size="15">
                <?php 
                    for($i=0;$i<count($poc_fac_opt_arr);$i++){
                        $id = $poc_fac_opt_arr[$i]['id'];
                        $facilityPracCode = $poc_fac_opt_arr[$i]['name'];
                        $pos_code = $poc_fac_opt_arr[$i]['pos_prac_code'];
                        if(in_array($id,$acc_fac_arr)){
                            $sel = 'selected="selected"';
                        }else{
                            $sel = '';
                        }
                        print '<option '.$sel.' value="'.$id.'">'.$facilityPracCode.' - '.$pos_code.'</option>';
                    }
                ?>
            </select>
        </div>
        <div class="pull-left" style="width:9%; padding-right:10px;">
            <label>Ins. Case Type</label>
            <select name="inscasetype[]" id="inscasetype" class="selectpicker" data-width="100%" multiple data-actions-box="true" data-title="Select Case" data-header="Select Case">
                <?php 
                    for($i=0;$i<count($ins_case_type_opt_arr);$i++){
                        $case_id = $ins_case_type_opt_arr[$i]['case_id'];
                        $case_name = $ins_case_type_opt_arr[$i]['case_name'];
                        
                        if(in_array($case_id,$ins_case_type_arr)){
                            $sel = 'selected="selected"';
                        }else{
                            $sel = '';
                        }
                        print '<option '.$sel.' value="'.$case_id.'">'.$case_name.'</option>';
                    }
                ?>
            </select>	
        </div>
        <div class="pull-left" style="width:10%; padding-right:10px;">
            <label>Primary Insurance</label>
            <select name="acc_ins[]" id="acc_ins" class="selectpicker" data-width="100%" multiple data-actions-box="true" data-header="All Insurance" data-title="All Insurance" data-size="15">
                <?php 
                $grp_qry = imw_query("SELECT id, title  FROM  ins_comp_groups WHERE delete_status = 0");
                if(imw_num_rows($grp_qry)>0) {
                    print '<optgroup label="Ins. Groups">';
                    while($insGroupQryRes=imw_fetch_array($grp_qry)){
                        $ins_grp_id = $insGroupQryRes['id'];
                        $ins_grp_name = $insGroupQryRes['title'];
                        $tmp_grp_ins_arr = array();
                        if(count($ins_grp_arr[$ins_grp_id])>0){
                            foreach($ins_grp_arr[$ins_grp_id] as $ins_grp_key => $grp_ins_id){
                                $tmp_grp_ins_arr[] = $grp_ins_id;
                            }
                            $grp_ins_ids = implode("~~~", $tmp_grp_ins_arr);
                            if(in_array($grp_ins_ids,$acc_ins_drop_arr)){
                                $sel = 'selected="selected"';
                            }else{
                                $sel = '';
                            }
                            print '<option '.$sel.' value="'.$grp_ins_ids.'">'.$ins_grp_name.'</option>';
                        }
                    }
                    print '</optgroup>';
                    print '<optgroup label="Ins. Companies">';
                    foreach($ins_comp_arr as $ins_key_id => $ins_val){
                        if(in_array($ins_key_id,$acc_ins_drop_arr)){
                            $sel = 'selected="selected"';
                        }else{
                            $sel = '';
                        }
                        print '<option '.$sel.' value="'.$ins_key_id.'">'.$ins_val.'</option>';
                    }
                    print '</optgroup>';
                }else{
                    foreach($ins_comp_arr as $ins_key_id => $ins_val){
                        if(in_array($ins_key_id,$acc_ins_drop_arr)){
                            $sel = 'selected="selected"';
                        }else{
                            $sel = '';
                        }
                        print '<option '.$sel.' value="'.$ins_key_id.'">'.$ins_val.'</option>';
                    }
                }
                ?>
            </select>
        </div>
        <?php
            if($acc_fac_str== ""){
                $dos_to=$cur_date;
                $dos_frm=$cur_date;
            }
        ?> 
        <div class="pull-left" style="width:8%; padding-right:10px;">
            <label>DOS From</label>
            <div class="input-group">
                <input id="dos_frm" type="text" name="dos_frm" value="<?php echo $dos_frm; ?>"  onBlur="checkdate(this);" class="date-pick input-sm form-control">
                <label for="dos_frm" class="input-group-addon pointer">
                    <span class="glyphicon glyphicon-calendar"></span>	
                </label>
            </div>
        </div>
        <div class="pull-left" style="width:8%; padding-right:10px;">
            <label>DOS To</label>
            <div class="input-group">
                <input id="dos_to" type="text" name="dos_to" value="<?php echo $dos_to; ?>"  onBlur="checkdate(this);" class="date-pick input-sm form-control">
                <label for="dos_to" class="input-group-addon pointer">
                    <span class="glyphicon glyphicon-calendar"></span>	
                </label>
            </div>
        </div>
        <div class="pull-left" style="width:10%; padding-right:10px;">
            <label>Sort By</label>
            <select name="ord_by" id="ord_by" class="selectpicker" data-width="100%">
                <option value="appointment" <?php if($ord_by == "appointment"){ echo ("selected"); } ?>>Appointment</option>
                <option value="billing_provider" <?php if($ord_by == "billing_provider"){ echo ("selected"); } ?>>Billing Provider</option>
                <option value="credited_provider" <?php if($ord_by == "credited_provider"){ echo ("selected"); } ?>>Credited Provider</option>
                <option value="pri_ins" <?php if($ord_by == "pri_ins"){ echo ("selected"); } ?>>Primary Insurance</option>
                <option value="pt_last_name" <?php if($ord_by == "pt_last_name"){ echo ("selected"); } ?>>Pt Last Name</option>
            </select>
        </div>
        <div class="pull-left" style="width:8%; padding-right:10px;">
            <label>View</label>
            <select name="view_srh" id="view_srh" class="selectpicker" data-width="100%">
                <option value="All" <?php if($view_srh=='All'){ echo "selected";} ?>>All</option>
                <option value="Posted" <?php if($view_srh=='Posted'){ echo "selected";} ?>>Posted</option>
                <option value="Not Posted" <?php if($view_srh=='Not Posted'){ echo "selected";} ?>>Not Posted</option>
            </select>
        </div>
        <div class="pull-left" style="width:7%; padding-right:10px;">
            <label>Chart</label>
            <select name="view_chart" id="view_chart" class="selectpicker" data-width="100%">
                <option value="all" <?php if($view_chart==''){ echo "selected";} ?>>All</option>
                <option value="finalized" <?php if($view_chart=='finalized'){ echo "selected";} ?>>Finalized</option>
                <option value="un_finalized" <?php if($view_chart=='un_finalized'){ echo "selected";} ?>>Un-finalized</option>
                <option value="re_finalized" <?php if($view_chart=='re_finalized'){ echo "selected";} ?>>Re-finalized</option>
            </select>
        </div>
    </form>
</div>
<form action="day_charges_update.php" name="post_charge" method="post">
    <input type="hidden" name="print_frm" id="print_frm" value="">
    <input type="hidden" name="prov_frm_type" id="prov_frm_type" value="<?php echo $provider_type; ?>">
    <input type="hidden" name="prov_frm_srh" id="prov_frm_srh" value="<?php echo $provider_srh_str; ?>">
    <input type="hidden" name="oper_frm_srh" id="oper_frm_srh" value="<?php echo $operator_srh_str; ?>">
    <input type="hidden" name="dos_to_srh" id="dos_to_srh" value="<?php echo $dos_to; ?>">
    <input type="hidden" name="dos_frm_srh" id="dos_frm_srh" value="<?php echo $dos_frm; ?>">
    <input type="hidden" name="view_frm_srh" id="view_frm_srh" value="<?php echo $view_srh; ?>">
    <input type="hidden" name="view_frm_chart" id="view_frm_chart" value="<?php echo $view_chart; ?>">
    <input type="hidden" name="ord_by_srh" id="ord_by_srh" value="<?php echo $ord_by; ?>">
    <input type="hidden" name="acc_fac_frm_srh" id="acc_fac_frm_srh" value="<?php echo $acc_fac_str; ?>">
    <input type="hidden" name="acc_ins_frm_srh" id="acc_ins_frm_srh" value="<?php echo $acc_ins_send_str; ?>">
    <input type="hidden" name="acc_ins_type_frm_srh" id="acc_ins_type_frm_srh" value="<?php echo $ins_case_type_str; ?>">
    <input type="hidden" name="srh_tot_poc_fac" value="<?php echo count($poc_fac_opt_arr); ?>">	
    <div style="width:100%; overflow:scroll;" id="main_container_div" class="pt10">
    <?php if($acc_fac_str!= ""){?>
        <table class="table table-bordered table-striped">
            <tbody id="alt_clr_tbl">
                <tr class="grythead">
                    <th style="width:110px"><div class="checkbox"><input type="checkbox" name="all_chk" id="all_chk" onClick="sel_unpost();"><label for="all_chk"></label></div></th>
                    <th style="width:110px;cursor:pointer;" onClick="sel_unpost_hcfa();">HCFA&nbsp;</th>
                    <th style="width:110px" nowrap>Pt Name - ID</th>
                    <th style="width:90px" nowrap>DOS</th>
                    <th style="width:90px" nowrap>Eid</th>
                    <th style="width:110px" nowrap="nowrap">Ins Case</th>
                    <th style="width:110px" nowrap>Pri</th>
                    <th style="width:110px" nowrap>Sec</th>
                    <th style="width:50px" nowrap>Self Pay</th>
                    <th style="width:110px" nowrap>CPT</th>
                    <th style="width:70px" nowrap>Units</th>
                    <th style="width:110px" nowrap>Dx Codes</th>
                    <th style="width:90px" nowrap>Mod1</th>
                    <th style="width:90px" nowrap>Mod2</th>
                    <th style="width:90px" nowrap>Mod3</th>
                    <th style="width:90px" nowrap>Mod4</th>
                    <th style="width:100px" nowrap>Charges</th>
                    <th style="width:100px" nowrap>Net Amt</th>
                    <th style="width:100px" nowrap>Paid Amt</th>
                    <th style="width:100px" nowrap>Copay</th>
                    <th style="width:180px" nowrap>CI/CO Total/Balance</th>
                    <th style="width:180px" nowrap>Pt Pmt Total/Balance</th>
                    <th style="width:120px" nowrap>Total Visit Charges</th>
                    <th style="width:70px" nowrap>TOS</th>
                    <th style="width:110px" nowrap>POS</th>
                    <th style="width:110px" nowrap>POS Facility</th>
                    <th style="width:110px" nowrap>Ref#/Auth#</th>
                    <th style="width:110px" nowrap>Billing Provider</th>
                    <th style="width:110px" nowrap>Credited Provider</th>
                    <th style="width:110px" nowrap>Referring Provider</th>
                </tr>
                <?php
                    $g=0;
                    $print_data="";
                    $total_amt_arr=$total_copay_arr=$total_copay_paid_arr=$total_paid_arr=$unpost_ids_arr=$tot_ids_arr=$total_cico_arr=array();
                    if($ord_by=="billing_provider" || $ord_by=="credited_provider"){
                        $phy_id_arr[]=0;
                    }else if($ord_by=="pri_ins"){
                        $phy_id_arr=array();
                        $phy_id_arr=$ins_id_arr;
                        $phy_id_arr[]=0;
                    }else if($ord_by=="pt_last_name"){
                        $phy_id_arr=array();
                        $phy_id_arr[]=0;
                    }else{
                        $phy_id_arr=array();
                        $phy_id_arr[]=0;
                    }

                    for($i=0;$i<count($phy_id_arr);$i++){
                        $phy_id=$phy_id_arr[$i];

                        if($ord_by=="billing_provider" && sizeof($data_detail[$phy_id])>0){
                            $pdf_phy_name= $arrAllUsers[$phy_id];
                            $print_data.='<tr><td colspan="19">Physician: '.$pdf_phy_name.'</td></tr>';
                        }
                                                
                        $dos_arr=array_keys($data_detail[$phy_id]);
                        if($ord_by!="billing_provider" && $ord_by!="credited_provider" && $ord_by!="pri_ins" && $ord_by!="pt_last_name"){
                            sort($dos_arr);
                        }

                        for($j=0;$j<count($dos_arr);$j++){
                            $dos_details=$dos_arr[$j];
                            if($ord_by=="pt_last_name"){
                                $pat_id_arr=$pat_id_row_arr;
                            }else{
                                $pat_id_arr=array_keys($data_detail[$phy_id][$dos_details]);
                            }
                            for($k=0;$k<count($pat_id_arr);$k++){
                                $pat_id=$pat_id_arr[$k];
                                $enc_id_arr=array_keys($data_detail[$phy_id][$dos_details][$pat_id]);
                                for($l=0;$l<count($enc_id_arr);$l++){
                                    $enc_id=$enc_id_arr[$l];
                                    $enc_detail=$data_detail[$phy_id][$dos_details][$pat_id][$enc_id];
                                    $hide_some_rows=true;
                                    for($m=0;$m<count($enc_detail);$m++){
                                        $g++;
                                        $paid_amt_readonly=$submitted=$posted_status="";
                                        if($enc_detail[$m]['charge_list_detail_id']>0){
                                            $arr_id=$enc_detail[$m]['charge_list_id'];
                                            $arr_detail_id=$enc_detail[$m]['charge_list_detail_id'];
                                            $chl_superbill_id=$enc_detail[$m]['chl_superbill_id'];
                                            if((float)$enc_detail[$m]['chl_totalAmt']==(float)$enc_detail[$m]['chl_totalBalance']){
                                                $paid_amt_readonly="";
                                                $chg_proc_amt_chk="no";
                                            }else{
                                                $paid_amt_readonly="";
                                                $chg_proc_amt_chk="no";
                                            }
                                            $submitted=$enc_detail[$m]['submitted'];
                                            $posted_status=$enc_detail[$m]['posted_status'];
                                        }else if($enc_detail[$m]['idSuperBill']>0){
                                            $arr_id='sup_'.$enc_detail[$m]['idSuperBill'];
                                            $arr_detail_id=$enc_detail[$m]['procedureinfo_id'];
                                            $chl_superbill_id=$arr_id;
                                            $paid_amt_readonly="";
                                        }
                                        if($submitted!='true'){
                                            $unpost_ids_arr[]=$arr_id;
                                            $bg_col_post="background-color:#ffffff";
                                        }else{
                                            if($posted_status>0){
                                                $bg_col_post="background-color:#c9f0ca";
                                            }else{
                                                $unpost_ids_arr[]=$arr_id;
                                                $bg_col_post="background-color:#ffffff";
                                            }
                                        }
                                        $tot_ids_arr[]=$arr_id;
                                        $show_dos=$enc_detail[$m]['dateOfService'];
                                        $sch_pos_id=$sch_posid[$show_dos][$pat_id];
                                        
                                        if($enc_detail[$m]['totalAmount']>$enc_detail[$m]['approvedAmt']){
                                            $chg_read_only="readonly";
                                        }else{
                                            $chg_read_only="";
                                        }
                                        $print_data .="<tr ".$bg_col_post.">";
                                        $show_dos_mmddyy=get_date_format($show_dos);
                                        $show_dos_print_mmddyy=get_date_format($show_dos,1);
                                ?>		
                                        <tr style=" <?php echo $bg_col_post;?>">
                                            <?php if($hide_some_rows==true){?>
                                                <td>
                                                    <?php 
                                                        $chk_case="";
                                                        if($enc_detail[$m]['charge_list_detail_id']>0){
                                                    ?>
                                                            <div class="checkbox"><input type="checkbox" name="chl_chk_box[]" id="chl_chk_box_<?php echo $arr_id; ?>" value="<?php echo $enc_detail[$m]['charge_list_id']; ?>" class="chl_chk_box_css"><label for="chl_chk_box_<?php echo $arr_id; ?>"></label></div>
                                                    <?php 
                                                        } 
                                                        if($enc_detail[$m]['idSuperBill']>0){
                                                            $chk_case=$enc_detail[$m]['case_type_id'];
                                                    ?>
                                                            <div class="checkbox"><input type="checkbox" name="sup_chk_box[]" id="chl_chk_box_<?php echo $arr_id; ?>" value="<?php echo $enc_detail[$m]['idSuperBill']; ?>" class="chl_chk_box_css"><label for="chl_chk_box_<?php echo $arr_id; ?>"></label></div>
                                                    <?php } ?>
                                                </td>
                                                <td><div class="checkbox"><input type="checkbox" name="hcfa_chk_box_arr[<?php echo $arr_id; ?>]" id="hcfa_chk_box_<?php echo $arr_id; ?>" value="yes" onClick="sel_chk('<?php echo $arr_id;?>');"><label for="hcfa_chk_box_<?php echo $arr_id; ?>"></label></div></td>
                                                <?php $csv_arr[$g]['patient_id']=$enc_detail[$m]['lname'].", ".$enc_detail[$m]['fname']." - ".$pat_id;$print_data .="<td valign='top' width='130' class='text_10'>".$enc_detail[$m]['lname'].", ".$enc_detail[$m]['fname']." - ".$pat_id."</td>"; ?>
                                                <input type="hidden" name="pat_id[<?php echo $arr_id; ?>]" id="pat_id[<?php echo $arr_id; ?>]" value="<?php echo $pat_id; ?>">
                                                <?php 
													$chart_tooltip=$dttime="";
													$chart_refinalize=$rec_chart_log_old=0;
													if($enc_detail[$m]['idSuperBill']>0){
														foreach($rec_chart_log_arr[$enc_detail[$m]['formId']] as $rec_chart_log_key => $rec_chart_log_val){
															$rec_chart_log=$rec_chart_log_arr[$enc_detail[$m]['formId']][$rec_chart_log_key];
															if($rec_chart_log['finalized']!=$rec_chart_log_old){
																$rec_chart_log_old=$rec_chart_log['finalized'];
																if($enc_detail[$m]['finalize']==1){
																	if($rec_chart_log['finalized']==0){
																		$dttime=$rec_chart_log['dttime'];
																	}
																}else{
																	$dttime=$rec_chart_log['dttime'];
																}
																$chart_refinalize++;
															}
														}
														
														if($chart_refinalize>1 && $enc_detail[$m]['modified_date_time']!='0000-00-00 00:00:00'){
															if($enc_detail[$m]['finalize']==0 && $enc_detail[$m]['modified_date_time']>$dttime){
																$chart_tooltip = show_tooltip("Chart was unfinalized, modification to Superbill was made and Chart is not finalized");
															}else if($enc_detail[$m]['finalize']==1 && $enc_detail[$m]['modified_date_time']>$dttime){
																$chart_tooltip = show_tooltip("Chart was unfinalized, a modification to the Superbill made and Chart is re-finalized");
															}
														}
													}
												?>
                                                <td class="<?php echo $post_class; ?>" nowrap="nowrap"><?php echo $enc_detail[$m]['lname'].', '.$enc_detail[$m]['fname']; ?> - <?php echo $pat_id; ?><?php if($enc_detail[$m]['idSuperBill']>0 && $chart_tooltip!=""){?><span style="padding-right:14px;" <?php echo $chart_tooltip; ?> data-container="body"><img src="../../library/images/infobutton.png"></span><?php } ?>
                                                </td>
                                                <?php $csv_arr[$g]['dos']=$show_dos_print_mmddyy;$print_data .="<td valign='top' class='text_10'>".$show_dos_print_mmddyy."</td>"; ?>
                                                <td><input id="dos[<?php echo $arr_id; ?>]"  type="text" value="<?php echo $show_dos_mmddyy; ?>" name="dos[<?php echo $arr_id; ?>]" class="input-sm form-control" style="width:73px;"></td>
                                                <?php $csv_arr[$g]['enc_id']=$enc_id; //$print_data .="<td valign='top' class='text_10'><!--".$enc_id."--></td>"; ?>
                                                <td><input type="hidden" value="<?php echo $chl_superbill_id; ?>" name="chl_superbill_id[<?php echo $arr_id; ?>]" id="chl_superbill_id[<?php echo $arr_id; ?>]"><input id="enc_id[<?php echo $arr_id; ?>]" readonly  type="text" value="<?php echo $enc_id; ?>" name="enc_id[<?php echo $arr_id; ?>]" class="input-sm form-control enc_id_css_<?php echo $arr_id; ?>" onClick="sel_chk('<?php echo $arr_id;?>');" style="width:73px;"></td>
                                                <?php
                                                    if($enc_detail[$m]['primaryInsuranceCoId']>0 || $enc_detail[$m]['secondaryInsuranceCoId']>0){
                                                        $chk_case=$enc_detail[$m]['case_type_id'];
                                                    }
                                                    $sup_proc_amt="";
                                                    if($enc_detail[$m]['idSuperBill']>0){
                                                        $sup_proc_amt="yes";
                                                    }
                                                ?>
                                                <td><select id="caseTypeText_<?php echo $g; ?>" name="caseTypeText[<?php echo $arr_id; ?>]" class="form-control minimal" onChange="loadCaseInfoDay('<?php echo $g;?>','<?php echo $enc_id;?>','<?php echo $pat_id;?>','<?php echo $sup_proc_amt;?>');sel_chk('<?php echo $arr_id;?>');" onClick="sel_chk('<?php echo $arr_id;?>');" style="width:120px;" data-size="5">
                                                        <?php	
                                                            foreach($insCasesOption_data[$pat_id] as $ins_case_key => $ins_case_val){
                                                                $insCasesArr = $insCasesOption_data[$pat_id][$ins_case_key]['case_name'].'-'.$insCasesOption_data[$pat_id][$ins_case_key]['ins_caseid'];
                                                        ?>
                                                                <option value="<?php echo $insCasesOption_data[$pat_id][$ins_case_key]['ins_caseid'];?>" <?php if($insCasesOption_data[$pat_id][$ins_case_key]['ins_caseid']==$chk_case){ echo "selected";} ?>><?php echo $insCasesArr;?></option>
                                                        <?php	
                                                            }
                                                            $copay_chk_sel="";
                                                            if($chk_case=="" || $chk_case=="0"){
                                                                $copay_chk_sel="selected='selected'";
                                                            }
                                                        ?>
                                                            <option value="Self" <?php echo $copay_chk_sel; ?>>Self Pay</option>
                                                    </select></td>
                                                <?php 
                                                    if($chk_case>0){
														$csv_arr[$g]['case_id']=$insCasesOption_data[$pat_id][$chk_case]['case_name']." - ".$chk_case;
                                                        $print_data .="<td valign='top' class='text_10' width='90'>".$insCasesOption_data[$pat_id][$chk_case]['case_name']." - ".$chk_case."</td>"; 
                                                    }else{
														$csv_arr[$g]['case_id']="Self Pay";
                                                        $print_data .="<td valign='top' class='text_10'>Self Pay</td>";
                                                    }
                                                ?>
                                                <?php $pri_ins=$ins_comp_arr[$enc_detail[$m]['primaryInsuranceCoId']]; ?>
                                                <td><input id="pri_ins_id_<?php echo $g; ?>"   type="hidden" value="<?php echo $enc_detail[$m]['primaryInsuranceCoId'];?>" name="pri_ins_id[<?php echo $arr_id; ?>]" ><input id="pri_ins_<?php echo $g; ?>" readonly  type="text" value="<?php echo $pri_ins;?>" name="pri_ins['<?php echo $arr_id; ?>']" class="input-sm form-control" onChange="sel_chk('<?php echo $arr_id;?>');" onClick="sel_chk('<?php echo $arr_id;?>');" title="<?php echo $pri_ins;?>" style="width:90px;"></td>
                                                <?php $csv_arr[$g]['pri_ins']=$pri_ins;$print_data .="<td valign='top' class='text_10'>".substr($pri_ins,0,5)."</td>"; ?>
                                                <?php $sec_ins=$ins_comp_arr[$enc_detail[$m]['secondaryInsuranceCoId']];?>
                                                <td><input id="sec_ins_id_<?php echo $g; ?>"  type="hidden" value="<?php echo $enc_detail[$m]['secondaryInsuranceCoId'];?>" name="sec_ins_id[<?php echo $arr_id; ?>]" ><input id="sec_ins_<?php echo $g; ?>"  readonly type="text" value="<?php echo $sec_ins;?>" name="sec_ins['<?php echo $arr_id; ?>']" class="input-sm form-control" onChange="sel_chk('<?php echo $arr_id;?>');" onClick="sel_chk('<?php echo $arr_id;?>');" title="<?php echo $sec_ins;?>" style="width:90px;">
                                                	<input id="ter_ins_id_<?php echo $g; ?>"  type="hidden" value="<?php echo $enc_detail[$m]['tertiaryInsuranceCoId'];?>" name="ter_ins_id[<?php echo $arr_id; ?>]" >
												</td>
												<?php $csv_arr[$g]['sec_ins']=$sec_ins;$print_data .="<td valign='top' class='text_10'>".substr($sec_ins,0,5)."</td>"; ?>
                                                <?php
                                                    $FeeTable=1;
                                                    if($enc_detail[$m]['idSuperBill']>0){
                                                        $sup_id=$enc_detail[$m]['idSuperBill'];
                                                ?>
                                                <?php 
                                                        $pri_ins_id=$enc_detail[$m]['primaryInsuranceCoId'];
                                                        if($billing_amount=='Default'){
                                                            $FeeTable=1;
                                                        }else{
                                                            if($pri_ins_id>0){
                                                                $FeeTable=$ins_fee_tbl_arr[$pri_ins_id];
                                                            }
                                                        }
                                                    } 
                                                }else{
                                                ?>
                                                <td colspan="8">&nbsp;</td>
                                                <?php
													$csv_arr[$g]['patient_id']=$csv_arr[$g]['dos']=$csv_arr[$g]['enc_id']=$csv_arr[$g]['case_id']=$csv_arr[$g]['pri_ins']=$csv_arr[$g]['sec_ins']=''; 
													$print_data .="<td valign='top' class='text_10'>&nbsp;</td><td >&nbsp;</td><td >&nbsp;</td><td >&nbsp;</td><td >&nbsp;</td>"; 
												?>
                                            <?php
                                                }
                                                $sup_proc_code="0";
                                                if($enc_detail[$m]['idSuperBill']>0){
                                                    $sup_proc_code="1";
                                                }
                                             ?>
                                                 <td><div class="checkbox"><input type="checkbox" name="self_pay_chk_box_arr[<?php echo $arr_id; ?>][<?php echo $arr_detail_id; ?>]" id="self_pay_chk_box_css_<?php echo $g; ?>" class="self_pay_chk_box_css_<?php echo $enc_id; ?>" value="yes" <?php if($enc_detail[$m]['proc_selfpay']=='1'){echo "checked";} ?> onClick="sel_chk('<?php echo $arr_id;?>');"><label for="self_pay_chk_box_css_<?php echo $g; ?>"></label></div></td>
                                            <?php
                                                if($enc_detail[$m]['proc_selfpay']=="1"){
                                                    $self_pay_chk_box_print="Yes";
                                                }else{
                                                    $self_pay_chk_box_print="No";
                                                }
                                            ?>
                                            <?php $csv_arr[$g]['self_pay']=$self_pay_chk_box_print;$print_data .="<td valign='top' class='text_10'>".$self_pay_chk_box_print."</td>"; ?>
                                            <?php $csv_arr[$g]['cpt_code']=$enc_detail[$m]['cptCode'];$print_data .="<td valign='top' class='text_10'>".substr($enc_detail[$m]['cptCode'],0,10)."</td>"; ?>
                                            <?php 
												$tooltip = show_tooltip(addslashes(remove_line_breaks($enc_detail[$m]['cpt_desc']))); 
												$cpt_alert_tooltip= show_tooltip(addslashes(implode($cpt_alert_display_arr[$enc_detail[$m]['primaryInsuranceCoId']][$enc_detail[$m]['cptCode']],'<br>'))); 
											?>
                                            <td>
                                            	<div class="input-group">
                                                	<input <?php echo $tooltip; ?> data-container="body" id="cpt_code_<?php echo $g; ?>" <?php echo $chg_read_only; ?>  type="text" value="<?php echo $enc_detail[$m]['cptCode']; ?>" name="cpt_codes_arr[<?php echo $sup_proc_code; ?>][<?php echo $arr_id; ?>][<?php echo $arr_detail_id; ?>]" class="input-sm form-control" onChange="sel_chk('<?php echo $arr_id;?>');<?php if($chg_read_only==""){?>ajaxFunction2('<?php echo $g; ?>','<?php echo $chg_proc_amt_chk; ?>');<?php } ?>" onClick="sel_chk('<?php echo $arr_id;?>');" onKeyUp="sel_chk('<?php echo $arr_id;?>');" style="width:75px;">
                                                	<?php if($cpt_alert_tooltip!=""){?>
                                                    	<label style="top:0px !important;" class="input-group-addon glyphicon glyphicon-info-sign pointer" data-toggle="tooltip" data-placement="right" <?php echo $cpt_alert_tooltip; ?>></label>
                                                	<?php } ?>
                                                </div>
                                            </td>
                                            <?php $csv_arr[$g]['unit']=$enc_detail[$m]['units'];$print_data .="<td valign='top' class='text_10'>".$enc_detail[$m]['units']."</td>"; ?>
                                            <td><input id="units_<?php echo $g; ?>" <?php echo $chg_read_only; ?> <?php echo $paid_amt_readonly; ?>  type="text" value="<?php echo $enc_detail[$m]['units']; ?>" name="units_arr[<?php echo $arr_id; ?>][<?php echo $arr_detail_id; ?>]" onChange="totalAmtDay('<?php echo $g; ?>');sel_chk('<?php echo $arr_id;?>');" class="input-sm form-control" onClick="sel_chk('<?php echo $arr_id;?>');" onKeyUp="sel_chk('<?php echo $arr_id;?>');" style="width:55px;"></td>
                                            <?php
                                            $enc_icd10 = $enc_detail[$m]['enc_icd10'];
                                            $dx_desc_arr=array();
                                            $dx_desc="";
                                            for($f=1;$f<=12;$f++){
                                                $dx_val=$enc_detail[$m]['dx'.$f];
                                                if($dx_val!="" && $icd10_desc_arr[$dx_val]!=""){
                                                    $dx_desc_arr[$dx_val]=$dx_val.' - '.$icd10_desc_arr[$dx_val];
                                                }else if($dx_val!="" && $arrDxCodechld[$dx_val]!=""){
                                                    $dx_desc_arr[$dx_val]=$dx_val.' - '.$arrDxCodechld[$dx_val];
                                                }
                                            } 
                                            $dx_desc=implode($dx_desc_arr,'<br>');
                                            ?>
                                            <td <?php echo show_tooltip(addslashes($dx_desc)); ?>  data-container="body"><span onClick="edit_enc_dx('<?php echo $arr_id; ?>');">
                                                <?php
                                                    $all_dx_codes_arr=unserialize(html_entity_decode($enc_detail[$m]['all_dx_codes']));
                                                    $dx_sel_arr=array();
                                                    $dx_sel_imp="";
                                                    for($f=1;$f<=12;$f++){
                                                        if($all_dx_codes_arr[$f]==$enc_detail[$m]['dx'.$f] && $enc_detail[$m]['dx'.$f]!=""){
                                                            $dx_sel_arr[]=$enc_detail[$m]['dx'.$f];
                                                        }
                                                    }
                                                    $dx_sel_imp=implode(', ',$dx_sel_arr);
                                                ?>
                                                  <input style="cursor:pointer;" disabled type="text" name="diagText_all[<?php echo $arr_id; ?>][<?php echo $arr_detail_id; ?>][]" id="diagText_all_<?php echo $g; ?>" value="<?php echo $dx_sel_imp;?>" class="diagText_all_css diagText_all_css_mul_<?php echo $arr_id; ?>"></span></td>
                                            <?php 
                                                $dx_arr=array();
                                                for($f=1;$f<=12;$f++){
                                                    if($enc_detail[$m]['dx'.$f]){
                                                        $dx_arr[]=$enc_detail[$m]['dx'.$f];
                                                    }
                                                }
                                            ?>
                                            <?php 
												if($enc_detail[$m]['modifier1']){
													$mod_nam=$chk_mod_in_db_arr[$enc_detail[$m]['modifier1']];
												}else{
													$mod_nam=$mod_code_arr[$enc_detail[$m]['modifier_id1']];
												}
											?>
                                            <td><input id="mod1_<?php echo $g; ?>"  type="text" value="<?php echo $mod_nam; ?>" name="mod1[<?php echo $arr_id; ?>][<?php echo $arr_detail_id; ?>]" class="input-sm form-control" onChange="sel_chk('<?php echo $arr_id;?>');" onClick="sel_chk('<?php echo $arr_id;?>');" onKeyUp="sel_chk('<?php echo $arr_id;?>');" style="width:55px;"></td>
                                            <?php 
												if($enc_detail[$m]['modifier2']){
													$mod_nam2=$chk_mod_in_db_arr[$enc_detail[$m]['modifier2']];
												}else{
													$mod_nam2=$mod_code_arr[$enc_detail[$m]['modifier_id2']];
												}
											?>
                                            <td><input id="mod2_<?php echo $g; ?>"  type="text" value="<?php echo $mod_nam2; ?>" name="mod2[<?php echo $arr_id; ?>][<?php echo $arr_detail_id; ?>]" class="input-sm form-control" onChange="sel_chk('<?php echo $arr_id;?>');" onClick="sel_chk('<?php echo $arr_id;?>');" onKeyUp="sel_chk('<?php echo $arr_id;?>');" style="width:55px;"></td>
                                            <?php 
                                                    if($enc_detail[$m]['modifier3']){
                                                        $mod_nam3=$chk_mod_in_db_arr[$enc_detail[$m]['modifier3']];
                                                    }else{
                                                        $mod_nam3=$mod_code_arr[$enc_detail[$m]['modifier_id3']];
                                                    }
                                                ?>
                                            <td><input id="mod3_<?php echo $g; ?>"  type="text" value="<?php echo $mod_nam3; ?>" name="mod3[<?php echo $arr_id; ?>][<?php echo $arr_detail_id; ?>]" class="input-sm form-control" onChange="sel_chk('<?php echo $arr_id;?>');" onClick="sel_chk('<?php echo $arr_id;?>');" onKeyUp="sel_chk('<?php echo $arr_id;?>');" style="width:55px;"></td>
                                            <?php 
												if($enc_detail[$m]['modifier4']){
													$mod_nam4=$chk_mod_in_db_arr[$enc_detail[$m]['modifier4']];
												}else{
													$mod_nam4=$mod_code_arr[$enc_detail[$m]['modifier_id4']];
												}
											?>
                                            <td><input id="mod4_<?php echo $g; ?>"  type="text" value="<?php echo $mod_nam4; ?>" name="mod4[<?php echo $arr_id; ?>][<?php echo $arr_detail_id; ?>]" class="input-sm form-control" onChange="sel_chk('<?php echo $arr_id;?>');" onClick="sel_chk('<?php echo $arr_id;?>');" onKeyUp="sel_chk('<?php echo $arr_id;?>');" style="width:55px;"></td>
											<?php 
                                                $mod_arr=array();
                                                if($mod_nam){
                                                    $mod_arr[]=$mod_nam;
                                                }
                                                if($mod_nam2){
                                                    $mod_arr[]=$mod_nam2;
                                                }
                                                if($mod_nam3){
                                                    $mod_arr[]=$mod_nam3;
                                                }
												if($mod_nam4){
                                                    $mod_arr[]=$mod_nam4;
                                                }
                                                $mod_txt="";
                                                if(count($mod_arr)>0){
                                                    $mod_txt=' - '.implode(', ',$mod_arr);
                                                }
												$csv_arr[$g]['dx_code']=implode(', ',$dx_arr);
												$csv_arr[$g]['mod']=$mod_txt;
                                                $print_data .="<td class='text_10' style='font-size:11px;' valign='top' width='55'>".implode(', ',$dx_arr).$mod_txt."</td>";
                                            ?>
                                            <td nowrap>
                                            <?php
                                                $total_enc_chrg="";
                                                if($enc_detail[$m]['todaysCharges']>0 || $enc_detail[$m]['idSuperBill']>0){
                                                    $total_amt=$sup_cpt_fee_arr[$FeeTable][$enc_detail[$m]['cptCode']];
                                                    $total_enc_chrg=$enc_detail[$m]['todaysCharges'];
                                                }else{
                                                    $total_amt=$enc_detail[$m]['procCharges'];
                                                    $total_enc_chrg=$enc_detail[$m]['chl_totalAmt'];
                                                }
                                                if($vip_ref_not_collect>0 && $enc_detail[$m]['vipSuperBill']>0){	
                                                    if($enc_detail[$m]['cptCode']=="92015" || $enc_detail[$m]['cptCode']=="Refraction" || $enc_detail[$m]['cptCode']=="refraction"){
                                                        $total_amt=0;
                                                    }
                                                }
                                                $total_charge_arr[]=$total_amt;
                                            ?>
                                            <?php $csv_arr[$g]['tot_charge']=number_format($total_amt,2);//$print_data .="<td class='text-right text_10' valign='top'>$".number_format($total_amt,2)."</td>"; ?>
                                                <div class="input-group"><div class="input-group-addon"><span class="<?php echo $post_class; ?>">$</span></div><input id="proc_charges_<?php echo $g; ?>" <?php echo $chg_read_only; ?> <?php echo $paid_amt_readonly; ?> type="text" value="<?php echo str_replace(',','',number_format($total_amt,2)); ?>" name="proc_charges[<?php echo $arr_id; ?>][<?php echo $arr_detail_id; ?>]" onChange="totalAmtDay('<?php echo $g; ?>');sel_chk('<?php echo $arr_id;?>');" class="input-sm form-control" onClick="sel_chk('<?php echo $arr_id;?>');" style="width:60px;"></div></td>
                                            <td nowrap>
                                            <?php
                                                if($enc_detail[$m]['todaysCharges']>0 || $enc_detail[$m]['idSuperBill']>0){
                                                    $total_amt=$sup_cpt_fee_arr[$FeeTable][$enc_detail[$m]['cptCode']]*$enc_detail[$m]['units'];
                                                }else{
                                                    $total_amt=$enc_detail[$m]['totalAmount'];
                                                }
                                                if($vip_ref_not_collect>0 && $enc_detail[$m]['vipSuperBill']>0){	
                                                    if($enc_detail[$m]['cptCode']=="92015" || $enc_detail[$m]['cptCode']=="Refraction" || $enc_detail[$m]['cptCode']=="refraction"){
                                                        $total_amt=0;
                                                    }
                                                }
                                                $total_amt_arr[]=$total_amt;
                                            ?>
                                            <?php $csv_arr[$g]['tot_amt']=number_format($total_amt,2);$print_data .="<td class='text-right text_10' valign='top'>$".number_format($total_amt,2)."</td>"; ?>
                                                <div class="input-group"><div class="input-group-addon"><span class="<?php echo $post_class; ?>">$</span></div><input id="total_amt_<?php echo $g; ?>"  readonly type="text" value="<?php echo str_replace(',','',number_format($total_amt,2)); ?>" name="total_amt[<?php echo $arr_id; ?>][<?php echo $arr_detail_id; ?>]" class="input-sm form-control" onClick="sel_chk('<?php echo $arr_id;?>');" style="width:60px;"></div></td>	
                                            <td nowrap>
                                            <?php 
                                                $total_paid_arr[]=$enc_detail[$m]['paidForProc']; 
                                                $csv_arr[$g]['tot_paid']=number_format($enc_detail[$m]['paidForProc'],2);$print_data .="<td class='text-right text_10' valign='top'>$".number_format($enc_detail[$m]['paidForProc'],2)."</td>"; ?>
                                                <div class="input-group"><div class="input-group-addon"><span class="<?php echo $post_class; ?>">$</span></div><input id="total_paid_amt_<?php echo $g; ?>" type="text" value="<?php echo number_format($enc_detail[$m]['paidForProc'],2); ?>" name="total_paid_amt[<?php echo $arr_id; ?>][<?php echo $arr_detail_id; ?>]" class="input-sm form-control" onChange="sel_chk('<?php echo $arr_id;?>');" onClick="sel_chk('<?php echo $arr_id;?>');" onKeyUp="sel_chk('<?php echo $arr_id;?>');" style="width:60px;"></div></td>
                                            <?php if($hide_some_rows==true){ ?>
                                                <td nowrap="nowrap" align="right">
                                                	<?php $csv_arr[$g]['tot_copay']=number_format($enc_detail[$m]['copay'],2);
														if($copay_label!=""){
															$print_data .="<td class='text-right text_10' valign='top'>$".number_format($enc_detail[$m]['copay'],2)."</td>"; 
														}else{
															$print_data .="<td class='text-right text_10' valign='top'></td>"; 
														}
													?>
                                                    <?php 
                                                        $copay_readonly="";
                                                        $copay_size="style='width:70px;'";
														$copay_paid_grn_chk="";
														$copay_paid_grn_chk_wdt="";
                                                        if($enc_detail[$m]['copayPaid']==1){
                                                            $copay_paid_grn_chk= "<img src='../../library/images/confirm.gif' width='16px'>";
                                                            $copay_readonly="readonly";
                                                            $copay_size="style='width:50px;'";
                                                            $total_copay_paid_arr[]=$enc_detail[$m]['copay'];
															$copay_paid_grn_chk_wdt='style="width:40% !important;"';
                                                        }
                                                        $total_copay_arr[]=$enc_detail[$m]['copay'];
                                                    ?>
                                                     <div class="input-group"><div class="input-group-addon" <?php echo $copay_paid_grn_chk_wdt; ?>><span class="<?php echo $post_class; ?>"><?php echo $copay_paid_grn_chk; ?>$</span></div><input type="hidden" name="copay_paid_chk[<?php echo $arr_id; ?>]" id="copay_paid_chk_<?php echo $g; ?>" value="<?php echo $enc_detail[$m]['copayPaid'];?>"><input id="copay_amt_<?php echo $g; ?>"  readonly  type="text" value="<?php echo numberformat($enc_detail[$m]['copay'],2,'yes','','no','yes');?>" name="copay_amt[<?php echo $arr_id; ?>][<?php echo $arr_detail_id; ?>]" class="input-sm form-control" onClick="sel_chk('<?php echo $arr_id;?>');" <?php echo $copay_size;?>></div></td>
                                                <?php
                                                    $total_cico_post_amt_enc_arr=$cico_payment_id_enc_arr=$ci_past_payment_chk_arr=array();
                                                    $pending_cico_amt_show=$total_cico_post_amt_enc=$red_colr="";
                                                    $red_colr_cont=false;
                                                    $cico_payment_id_enc_arr=explode(',',$chk_in_pay[$pat_id][$show_dos]['payment_id']);
                                                    if(count($cico_payment_id_enc_arr)>0){
                                                        foreach($cico_payment_id_enc_arr as $ci_pmt_id){
                                                            if($ci_pmt_id>0){
                                                                $ci_past_payment_chk_arr[]=array_sum($ci_co_ref_arr[$ci_pmt_id]);
                                                                $total_cico_post_amt_enc_arr[]=array_sum($ci_co_post_arr[$ci_pmt_id]);
                                                            }
                                                            if($enc_detail[$m]['copay']>0){
                                                                $chk_in_val = $chk_in_main_ids[$ci_pmt_id];
                                                                if(empty($chk_in_val) === false){
                                                                    $red_colr_cont = true;
                                                                }
                                                                if($red_colr_cont == false){
                                                                    $red_colr = 'style="background:#F00;"';
                                                                }
                                                            }
                                                        }
                                                        $total_cico_post_amt_enc=array_sum($total_cico_post_amt_enc_arr)+array_sum($ci_past_payment_chk_arr);
                                                        $pending_cico_amt_show=$chk_in_pay[$pat_id][$show_dos]['total_payment']-$total_cico_post_amt_enc;
                                                    }else{
                                                        $pending_cico_amt_show='0.00';
                                                    }
                                                    
                                                    $total_pt_pmt_post_enc_arr=$pmt_past_payment_chk_arr=array();
                                                    $total_pt_pmt_post_enc=$pending_pt_pmt_amt_show="";
                                                    if(count($pt_all_pmt[$pat_id]['id'])>0){
                                                        foreach($pt_all_pmt[$pat_id]['id'] as $pt_pmt_id){
                                                            if($pt_pmt_id>0){
                                                                $total_pt_pmt_post_enc_arr[]=array_sum($pt_pmt_post_arr[$pt_pmt_id]);
                                                                $pmt_past_payment_chk_arr[]=array_sum($pt_pmt_ref_arr[$pt_pmt_id]);
                                                            }
                                                        }
                                                        $total_pt_pmt_post_enc=array_sum($total_pt_pmt_post_enc_arr)+array_sum($pt_all_pmt[$pat_id]['apply_amount'])+array_sum($pmt_past_payment_chk_arr);
                                                        $pending_pt_pmt_amt_show=array_sum($pt_all_pmt[$pat_id]['paid_amount'])-$total_pt_pmt_post_enc;
                                                    }else{
                                                        $pending_pt_pmt_amt_show='0.00';
                                                    }
                                                    
                                                    if($chk_cico_pat_arr[$pat_id][$show_dos]==1){
                                                ?>
                                                <td nowrap onClick="show_ci_co_info('<?php echo $pat_id; ?>','','<?php echo $show_dos; ?>','');">
                                                     <?php $csv_arr[$g]['ci_co_payment']=0;
													 if($pt_pmt_label!=""){
													 	$print_data .="<td class='text-right text_10' valign='top'>$0.00</td>"; 
													 }else{
														 $print_data .="<td class='text-right text_10' valign='top' width='110'>".$arrAllUsers[$enc_detail[$m]['physicianId']]."</td>"; 
													 }
													 ?>
                                                    <div class="input-group pull-left" style="width:55%"><div class="input-group-addon"><span class="<?php echo $post_class; ?>">$</span></div><input id="check_in_out_paid_<?php echo $g; ?>" value="0.00"  readonly type="text" name="check_in_out_paid_arr[<?php echo $arr_id; ?>]" class="input-sm form-control" onClick="sel_chk('<?php echo $arr_id;?>');"></div><span class="pull-right" style="width:45%">&nbsp;<span style="color:#F00"> / $0.00</span><input type="hidden" value="0" name="cico_payment_id_arr[<?php echo $arr_id; ?>]" id="cico_payment_id_arr[<?php echo $arr_id; ?>]"></span></td>
                                                <?php }else{?>
                                                <?php $csv_arr[$g]['ci_co_payment']=number_format($chk_in_pay[$pat_id][$show_dos]['total_payment'],2);
													  if($pt_pmt_label!=""){
													  	$print_data .="<td class='text-center text_10' valign='top'>$".number_format($chk_in_pay[$pat_id][$show_dos]['total_payment'],2)."</td>";
													  }else{
														$print_data .="<td class='text-center text_10' valign='top' width='110'>".$arrAllUsers[$enc_detail[$m]['physicianId']]."</td>";
													  }
												?>
                                                <td nowrap onClick="show_ci_co_info('<?php echo $pat_id; ?>','<?php echo $chk_in_pay[$pat_id][$show_dos]['total_payment']; ?>','<?php echo $show_dos; ?>','<?php echo $chk_in_pay[$pat_id][$show_dos]['sch_id']; ?>');">
                                                    <div class="input-group pull-left" style="width:55%"><div class="input-group-addon"><span class="<?php echo $post_class; ?>">$</span></div><input <?php echo $red_colr; ?> id="check_in_out_paid_<?php echo $g; ?>" value="<?php echo numberformat($chk_in_pay[$pat_id][$show_dos]['total_payment'],2,'yes','','no','yes');?>"  readonly type="text" name="check_in_out_paid_arr[<?php echo $arr_id; ?>]" class="input-sm form-control" onClick="sel_chk('<?php echo $arr_id;?>');"></div><span class="pull-right" style="width:45%">&nbsp;<span style="color:#F00"> / $<?php echo number_format($pending_cico_amt_show,2);?></span><input type="hidden" value="<?php echo $chk_in_pay[$pat_id][$show_dos]['payment_id'];?>" name="cico_payment_id_arr[<?php echo $arr_id; ?>]" id="cico_payment_id_arr[<?php echo $arr_id; ?>]"></span></td>
                                                <?php 
													$total_cico_arr[]=$chk_in_pay[$pat_id][$show_dos]['total_payment'];
													$total_cico_bal_arr[]=$pending_cico_amt_show;
												?>
                                                <?php } 
                                                $chk_cico_pat_arr[$pat_id][$show_dos]=1;
                                                $pt_pmt_enc=implode(',',$pt_all_pmt[$pat_id]['id']);
                                                if($chk_pt_pmt_arr[$pat_id][$show_dos]==1){
                                                ?>
                                                <?php $csv_arr[$g]['pt_pre_payment']=0;
													if($pt_pmt_label!=""){
														$print_data .="<td class='text-right text_10' valign='top'>$0.00</td>"; 
													}else{
														$print_data .="<td class='text-right text_10' valign='top'></td>"; 
													}
												?>
                                                <td nowrap><div class="input-group pull-left" style="width:55%"><div class="input-group-addon"><span class="<?php echo $post_class; ?>">$</span></div><input id="pt_pmt_paid_<?php echo $g; ?>" value="0.00"  readonly type="text" name="pt_pmt_paid_arr[<?php echo $arr_id; ?>]" class="input-sm form-control" onClick="sel_chk('<?php echo $arr_id;?>');"></div><span class="pull-right" style="width:45%">&nbsp;<span style="color:#F00"> / $0.00</span><input type="hidden" value="0" name="pt_pmt_id_arr[<?php echo $arr_id; ?>]" id="pt_pmt_id_arr[<?php echo $arr_id; ?>]"></span></td>
                                                <?php }else{ 
                                                    $total_pt_pmt_amt=0;
                                                    $total_pt_pmt_amt=array_sum($pt_all_pmt[$pat_id]['paid_amount']);
                                                ?>
                                                <?php $csv_arr[$g]['pt_pre_payment']=number_format($total_pt_pmt_amt,2);
													  if($pt_pmt_label!=""){
													  	$print_data .="<td class='text-center text_10' valign='top'>$".number_format($total_pt_pmt_amt,2)."</td>";
													  }else{
														 $print_data .="<td class='text-center text_10' valign='top' width='2'></td>"; 
													  }
												?>
                                                <td nowrap><div class="input-group pull-left" style="width:55%"><div class="input-group-addon"><span class="<?php echo $post_class; ?>">$</span></div><input id="pt_pmt_paid_<?php echo $g; ?>" value="<?php echo numberformat($total_pt_pmt_amt,2,'yes','','no','yes');?>"  readonly type="text" name="pt_pmt_paid_arr[<?php echo $arr_id; ?>]" class="input-sm form-control" onClick="sel_chk('<?php echo $arr_id;?>');"></div><span class="pull-right" style="width:45%">&nbsp;<span style="color:#F00"> / $<?php echo number_format($pending_pt_pmt_amt_show,2);?></span><input type="hidden" value="<?php echo $pt_pmt_enc;?>" name="pt_pmt_id_arr[<?php echo $arr_id; ?>]" id="pt_pmt_id_arr[<?php echo $arr_id; ?>]"></span></td>
                                                <?php 
													$total_pt_pmt_arr[$pat_id]=$total_pt_pmt_amt;
													$total_pt_pmt_bal_arr[$pat_id]=$pending_pt_pmt_amt_show;
												?>
												<?php } 
                                                    $chk_pt_pmt_arr[$pat_id][$show_dos]=1;
                                                ?>
                                                <?php $csv_arr[$g]['tot_visit_charges']=number_format($total_enc_chrg,2);//$print_data .="<td class='text-right text_10' valign='top'>$".number_format($total_enc_chrg,2)."&nbsp;&nbsp;</td>"; ?>
                                                <td nowrap="nowrap"><div class="input-group"><div class="input-group-addon"><span class="<?php echo $post_class; ?>">$</span></div><input id="total_enc_chrg_<?php echo $g; ?>"  type="text" value="<?php echo number_format($total_enc_chrg,2); ?>" name="total_enc_chrg[<?php echo $arr_id; ?>]" class="input-sm form-control"  readonly></div></td><td>
                                                    <?php if($enc_detail[$m]['tos']){$tos=$enc_detail[$m]['tos'];}else{$tos=$tos_code_arr[$enc_detail[$m]['type_of_service']];}$csv_arr[$g]['tos']=$tos; ?>
                                                    <input id="tos_<?php echo $g; ?>"  type="text" value="<?php echo $tos; ?>" name="tos[<?php echo $arr_id; ?>]" class="input-sm form-control" onChange="sel_chk('<?php echo $arr_id;?>');" onClick="sel_chk('<?php echo $arr_id;?>');" onKeyUp="sel_chk('<?php echo $arr_id;?>');" style="width:50px;"></td>
                                                <td><input id="main_fac_id_<?php echo $g; ?>"  type="hidden" value="<?php echo $enc_detail[$m]['billing_facility_id']; ?>" name="main_fac_id[<?php echo $arr_id; ?>]"> 
                                                    <?php
                                                        $posFacilityId="";
                                                        if($enc_detail[$m]['pos']){
                                                            $pos=$enc_detail[$m]['pos'];
                                                        }else{
                                                            $pos=$pos_codes_arr[$enc_detail[$m]['place_of_service']];
                                                            $posFacilityId=$enc_detail[$m]['posFacilityId'];
                                                        }
                                                        if($posFacilityId>0){
                                                            $posFacilityId=$posFacilityId;
                                                        }else{
                                                            if($enc_detail[$m]['sch_app_id']>0){
																$posFacilityId=$sch_posid_sid_arr[$show_dos][$pat_id][$enc_detail[$m]['sch_app_id']];
															}else{
																$posFacilityId=$sch_posid_arr[$show_dos][$pat_id];
															}
                                                            
                                                            if($posFacilityId>0){
                                                                if($enc_detail[$m]['sch_app_id']>0){
																	$pos=$pos_codes_arr[$sch_pos_id_sid_arr[$show_dos][$pat_id][$enc_detail[$m]['sch_app_id']]];
																}else{
																	$pos=$pos_codes_arr[$sch_pos_id_arr[$show_dos][$pat_id]];
																}
                                                                $posFacilityId=$posFacilityId;
                                                            }else{
                                                                $posFacilityId=$sch_pos_id;
                                                                if($pat_fac_id_arr[$pat_id]>0){
                                                                    $posFacilityId=$pat_fac_id_arr[$pat_id];
                                                                    $pos=$poc_fac_all_arr[$posFacilityId];
                                                                }
                                                            }
                                                        }
                                                        ?>			
                                                            <select id="pos_<?php echo $g; ?>" name="pos[<?php echo $arr_id; ?>]"  class="form-control minimal" onChange="return checkPosFn('<?php echo $g;?>','<?php echo $arr_id; ?>');sel_chk('<?php echo $arr_id;?>');" onClick="sel_chk('<?php echo $arr_id;?>');" onKeyUp="sel_chk('<?php echo $arr_id;?>');" data-width="100%" data-size="10">
                                                        <?php
                                                            if(count($pos_id_arr)>0){								
                                                                for($y=0;$y<count($pos_id_arr);$y++){
                                                                    $id=$pos_id_arr[$y];
                                                                    $pos_codes=$pos_codes_arr[$id];
                                                                    $pos_val = $pos_data_arr[$id];
                                                                    $sel_pos = $pos == $pos_codes ? 'selected="selected"': '';
                                                                    print '<option '.$sel_pos.' value="'.$pos_codes.'">'.$pos_val.'</option>';
                                                                }
                                                            }
                                                        ?>
                                                    </select></td>
                                                <?php $csv_arr[$g]['pos']=$pos; ?>
                                                <?php $posFacilityDetails = posFacilityDetails($pos,'facilityPracCode'); ?>
                                                <td id="fac_id_<?php echo $g; ?>"><select id="posFacilityCode_<?php echo $g; ?>" name="posFacilityCode[<?php echo $arr_id; ?>]"  class="form-control minimal" onChange="sel_chk('<?php echo $arr_id;?>');" onClick="sel_chk('<?php echo $arr_id;?>');" onKeyUp="sel_chk('<?php echo $arr_id;?>');" style="width:140px;" data-size="5">
                                                        <?php
                                                            if(count($posFacilityDetails)>0){
                                                                foreach($posFacilityDetails as $obj){
                                                                    $id = $obj->pos_facility_id;
                                                                    $facilityPracCode = $obj->facilityPracCode;
                                                                    $sel = $posFacilityId == $id ? 'selected="selected"': '';
                                                                    print '<option '.$sel.' value="'.$id.'">'.$facilityPracCode.'</option>';
                                                                }
                                                            }
                                                        ?>
                                                    </select></td>
                                                <?php 
													$final_pos_fac="";
													$final_pos_fac=str_replace('-',' - ',$pos_fac_arr[$posFacilityId]);
													$final_pos_fac=str_replace('  -  ',' - ',$final_pos_fac);
													$csv_arr[$g]['pos_fac']=$final_pos_fac;
													$print_data .="<td class='text_10' valign='top' width='55' style='font-size:11px;'>".$final_pos_fac."</td>"; 
												?>
                                               
                                                <td>
                                                    <?php
                                                        if($enc_detail[$m]['auth_no']){
                                                            $ref_auth=$enc_detail[$m]['auth_no'];
                                                        }else{
                                                            $ref_auth=$enc_detail[$m]['referral'];
                                                        }
                                                        $reff_phy_id=$ref_no_chk="";
                                                        if($ref_auth=="" && $enc_detail[$m]['charge_list_detail_id']<=0){
                                                            $referral_arr=array();
                                                            $chk_reff_date=$show_dos;
                                                            if($enc_detail[$m]['refferingPhysician']>0){
                                                                $reff_phy_id=$enc_detail[$m]['refferingPhysician'];
                                                            }else{
                                                                $reff_phy_id=$enc_detail[$m]['primary_care_id'];
                                                            }
                                                            if($act_ins_caseid_data_arr[$pat_id][$chk_case]['primary']=='Yes' || $act_ins_caseid_data_arr[$pat_id][$chk_case]['secondary']=='Yes' || $act_ins_caseid_data_arr[$pat_id][$chk_case]['tertiary']=='Yes'){
                                                                foreach($pat_reff_arr[$pat_id] as $reff_key=>$reff_val){
                                                                    $reff_row=$pat_reff_arr[$pat_id][$reff_key];
                                                                    //echo $reff_row['insCaseid'].'=='.$chk_case.'&& ('.$reff_phy_id.'==0 || '.$reff_row['reff_phy_id'].'=='.$reff_phy_id.') && '.$reff_row['effective_date'].'<='.$chk_reff_date.' && ('.$reff_row['end_date'].'=='."0000-00-00".' || '.$reff_row['end_date'].'>='.$chk_reff_date.')<br>';
                                                                    if($reff_row['insCaseid']==$chk_case && ($reff_phy_id==0 || $reff_row['reff_phy_id']==$reff_phy_id) && $reff_row['effective_date']<=$chk_reff_date && ($reff_row['end_date']=="0000-00-00" || $reff_row['end_date']>=$chk_reff_date)){
                                                                        if($act_ins_caseid_data_arr[$pat_id][$chk_case]['primary']=='Yes' && $reff_row['reff_type']=='1'){
                                                                            $referral_arr[]=array($reff_row['reffral_no'],$xyz, $reff_row['reffral_no']);
                                                                            if($referral==""){
                                                                                $ref_auth=$reff_row['reffral_no'];
                                                                            }
                                                                            if($reff_phy_id<=0){
                                                                                $reff_phy_id=$reff_row['reff_phy_id'];
                                                                            }
                                                                        }
                                                                        if($act_ins_caseid_data_arr[$pat_id][$chk_case]['secondary']=='Yes' && $reff_row['reff_type']=='2'){
                                                                            $referral_arr[]=array($reff_row['reffral_no'],$xyz, $reff_row['reffral_no']);
                                                                            if($ref_auth==""){
                                                                                $ref_auth=$reff_row['reffral_no'];
                                                                            }
                                                                            if($reff_phy_id<=0){
                                                                                $reff_phy_id=$reff_row['reff_phy_id'];
                                                                            }
                                                                        }
                                                                        if($act_ins_caseid_data_arr[$pat_id][$chk_case]['tertiary']=='Yes' && $reff_row['reff_type']=='3'){
                                                                            $referral_arr[]=array($reff_row['reffral_no'],$xyz, $reff_row['reffral_no']);
                                                                            if($ref_auth==""){
                                                                                $ref_auth=$reff_row['reffral_no'];
                                                                            }
                                                                            if($reff_phy_id<=0){
                                                                                $reff_phy_id=$reff_row['reff_phy_id'];
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
															$auth_name="";
															if($act_ins_caseid_auth_data_arr[$pat_id][$chk_case]['primary']=='Yes' || $act_ins_caseid_auth_data_arr[$pat_id][$chk_case]['secondary']=='Yes' || $act_ins_caseid_auth_data_arr[$pat_id][$chk_case]['tertiary']=='Yes'){
																foreach($pat_auth_arr[$pat_id] as $auth_key=>$auth_val){
																	$auth_row=$pat_auth_arr[$pat_id][$auth_key];
																	if($auth_row['ins_case_id']==$chk_case && $auth_row['auth_date']<=$chk_reff_date && ($auth_row['end_date']=="0000-00-00" || $auth_row['end_date']>=$chk_reff_date)){
																		$auth_cpt_codes_exp=$enc_proc_codes_exp=array();
																		if(trim($auth_row['auth_cpt_codes'])!=""){
																			$auth_cpt_codes_exp=explode(';',$auth_row['auth_cpt_codes']);
																		}
																		for($mk=0;$mk<count($enc_detail);$mk++){
																			$enc_proc_codes_exp[$enc_detail[$mk]['cptCode']]=$enc_detail[$mk]['cptCode'];
																		}
																		if(($auth_row['auth_provider']==0 || $auth_row['auth_provider']==$enc_detail[$m]['physicianId']) && (array_intersect($enc_proc_codes_exp,$auth_cpt_codes_exp) || count($auth_cpt_codes_exp)==0)){
																			if($act_ins_caseid_auth_data_arr[$pat_id][$chk_case]['primary']=='Yes' && $auth_row['ins_type']=='1'){
																				$auth_name=$auth_row['auth_name'];
																				$a_id=$auth_row['a_id'];
																				$AuthAmount=$auth_row['AuthAmount'];
																			}
																			if($act_ins_caseid_auth_data_arr[$pat_id][$chk_case]['secondary']=='Yes' && $auth_row['ins_type']=='2' && $auth_name==""){
																				$auth_name=$auth_row['auth_name'];
																				$a_id=$auth_row['a_id'];
																				$AuthAmount=$auth_row['AuthAmount'];
																			}
																			if($act_ins_caseid_auth_data_arr[$pat_id][$chk_case]['tertiary']=='Yes' && $auth_row['ins_type']=='3' && $auth_name==""){
																				$auth_name=$auth_row['auth_name'];
																				$a_id=$auth_row['a_id'];
																				$AuthAmount=$auth_row['AuthAmount'];
																			}
																		}
																	}
																}
															}
															if($ref_auth==""){
																$ref_auth=$auth_name;
															}else{
																$ref_no_chk=$ref_auth;
															}
                                                        }
														if($enc_detail[$m]['charge_list_detail_id']>0){
															$reff_phy_id=$enc_detail[$m]['pcl_reff_phy_id'];
														}
                                                    ?>
                                                    <input id="ref_auth<?php echo $g; ?>" readonly  type="text" value="<?php echo $ref_auth; ?>" name="ref_auth_arr[<?php echo $arr_id; ?>]" class="input-sm form-control" onClick="sel_chk('<?php echo $arr_id;?>');" onKeyUp="sel_chk('<?php echo $arr_id;?>');"> 
                                                    <input id="reff_phy_id<?php echo $g; ?>"  type="hidden" value="<?php echo $reff_phy_id; ?>" name="reff_phy_id_arr[<?php echo $arr_id; ?>]"> 
                                                    <input id="ref_auth_chk<?php echo $g; ?>" type="hidden" value="<?php echo $enc_detail[$m]['auth_no']; ?>" name="ref_auth_chk_arr[<?php echo $arr_id; ?>]"> 
                                                	<input id="auth_name_<?php echo $g; ?>" type="hidden" value="<?php echo $auth_name; ?>" name="auth_name_arr[<?php echo $arr_id; ?>]">
                                                    <input id="a_id_<?php echo $g; ?>" type="hidden" value="<?php echo $a_id; ?>" name="a_id_arr[<?php echo $arr_id; ?>]">
                                                    <input id="AuthAmount_<?php echo $g; ?>" type="hidden" value="<?php echo $AuthAmount; ?>" name="AuthAmount_arr[<?php echo $arr_id; ?>]">
                                                    <input id="ref_no_chk<?php echo $g; ?>" type="hidden" value="<?php echo $ref_no_chk; ?>" name="ref_no_chk_arr[<?php echo $arr_id; ?>]"></td>
                                             <?php $csv_arr[$g]['ref_auth']=$ref_auth;$print_data .="<td valign='top' class='text_10'>".$ref_auth."</td>"; ?>
                                             <td class="<?php echo $post_class; ?>"><span id="primary_provider_<?php echo $g; ?>" class="text_purple pointer" onClick="edit_day_charges('<?php echo $arr_id; ?>','<?php echo $g; ?>');"  title="<?php echo $arrAllUsers[$enc_detail[$m]['physicianId']]; ?>"><?php echo strtoupper($arrAllUsersInt[$enc_detail[$m]['physicianId']]); ?></span></td>
                                             <td class="<?php echo $post_class; ?>"><span id="secondary_provider_<?php echo $g; ?>" class="text_purple pointer" onClick="edit_day_charges('<?php echo $arr_id; ?>','<?php echo $g; ?>');"  title="<?php echo $arrAllUsers[$enc_detail[$m]['secondaryProviderId']]; ?>"><?php echo strtoupper($arrAllUsersInt[$enc_detail[$m]['secondaryProviderId']]); ?></span></td>
                                         	 <td class="<?php echo $post_class; ?>"><span id="ref_provider_<?php echo $g; ?>" class="text_purple pointer" onClick="edit_day_charges('<?php echo $arr_id; ?>','<?php echo $g; ?>');"  title="<?php echo $arrAllRef[$reff_phy_id]; ?>"><?php if($arrAllRefInt[$reff_phy_id]!=""){echo strtoupper($arrAllRefInt[$reff_phy_id]);}else{echo"N/A";} ?></span></td>
											<?php $csv_arr[$g]['billing_provider']=$arrAllUsers[$enc_detail[$m]['physicianId']];$csv_arr[$g]['credited_provider']=$arrAllUsers[$enc_detail[$m]['secondaryProviderId']];$csv_arr[$g]['ref_provider']=$arrAllRef[$reff_phy_id]; ?>	
                                            <?php }else{?>
                                                <?php $print_data .="<td colspan='11' class='text_10'>&nbsp;</td>"; ?>
                                                <td class="<?php echo $post_class; ?>" colspan="11">&nbsp;</td>	
                                            <?php } ?>
                                            
                                         <?php $print_data .="</tr>"; ?>	
                                        </tr>
                    <?php				
                                        $hide_some_rows=false; 
                                    }	
                                }
                            }
                        }
                    }

                    if(count($data_detail)>0){
                    ?>	
                        <tr style="font-weight:bold;">
                        <td colspan='14'>&nbsp;</td>
                        <td class="purple_bar">Total&nbsp;</td>
                        <td class="text-right purple_bar"><?php echo "$".number_format(array_sum($total_charge_arr),2);?></td>
                        <td class="text-right purple_bar"><?php echo "$".number_format(array_sum($total_amt_arr),2);?></td>
                        <td class="text-right purple_bar"><?php echo "$".number_format(array_sum($total_paid_arr),2);?></td>
                        <td class="text-right purple_bar"><?php echo "$".number_format(array_sum($total_copay_paid_arr),2);?></td>
                        <td class="text-right purple_bar" nowrap><?php echo "$".number_format(array_sum($total_cico_arr),2);?><span style="color:#F00">/<?php echo "$".number_format(array_sum($total_cico_bal_arr),2);?></span></td>
                        <td class="text-right purple_bar" nowrap><?php echo "$".number_format(array_sum($total_pt_pmt_arr),2);?><span style="color:#F00">/<?php echo "$".number_format(array_sum($total_pt_pmt_bal_arr),2);?></span></td>
                        <td colspan='8'>&nbsp;</td></tr>
                    <?php
                    $print_data .="
                    <tr>
                        <td colspan='16' style='height:10px'></td>	
                    </tr>
                    <tr>
                        <td colspan='8'></td>
                        <td bgcolor='#009933' colspan='6'></td>
                        <td colspan='2'></td>
                    </tr>
                    <tr>
                        <td colspan='8'>&nbsp;</td>
                        <td>Total :</td>
                        <!--<td class='text-right'>
                            $".number_format(array_sum($total_charge_arr),2)."
                        </td>-->
                        <td class='text-right'>
                            $".number_format(array_sum($total_amt_arr),2)."
                        </td>
                        <td class='text-right'>
                            $".number_format(array_sum($total_paid_arr),2)."
                        </td>";
						if($pt_pmt_label!=""){
							$print_data .="<td class='text-right'>
								$".number_format(array_sum($total_copay_paid_arr),2)."
							</td>
						
							<td class='text-right'>
								$".number_format(array_sum($total_cico_arr),2)."
							</td>
							<td class='text-right'>
								$".number_format(array_sum($total_pt_pmt_arr),2)."
							</td>";
						}else{
							$print_data .="<td class='text-right'></td><td class='text-right'></td><td class='text-right'></td>";
						}
					$print_data .="<td colspan='2'>&nbsp;</td>	
                    </tr>
                    <tr>
                        <td colspan='8'></td>
                        <td bgcolor='#009933' colspan='6'></td>
                        <td colspan='2'></td>
                    </tr>
                    ";
                    }else{
                ?>
                    <tr><td colspan="31" class="lead text-left"><?php echo imw_msg('no_rec');?></td></tr>
                <?php		

             } ?>
             <?php
                $no_enc_data="";
                $cn_no_enc_header="";
                for($sg=0;$sg<=count($rec_sch_app_arr['sa_patient_id']);$sg++){
                    $sch_pat_id=$rec_sch_app_arr['sa_patient_id'][$sg];
                    $sch_pat_name_id=$rec_sch_app_arr['sa_patient_name_id'][$sg];
                    $sch_app_date=$rec_sch_app_arr['sa_app_start_date'][$sg];
                    $sch_app_status=$rec_sch_app_arr['sa_patient_app_status_id'][$sg];
                    $sch_proc=$rec_sch_app_arr['proc'][$sg];
                    $sch_app_time=$rec_sch_app_arr['sa_app_starttime'][$sg];
                    if($data_detail_for_sch[$sch_app_date][$sch_pat_id]!=""){
                    }else{
                        if($sch_pat_id>0 && $slot_proc_non_billed[$rec_sch_app_arr['schedule_appointment_id'][$sg]]<=0){
                            $sch_app_date_exp=explode('-',$sch_app_date);
                            $sch_app_date_imp=$sch_app_date_exp[1].'-'.$sch_app_date_exp[2].'-'.$sch_app_date_exp[0];
                            $sch_app_time_exp=explode(':',$sch_app_time);
                            $sch_app_time_imp=date('h:i A',mktime($sch_app_time_exp[0],$sch_app_time_exp[1],$sch_app_time_exp[2],$sch_app_date_exp[1],$sch_app_date_exp[2],$sch_app_date_exp[0]));
                            if($sch_app_status=='Canceled' || $sch_app_status=='No-Show'){
                                $app_enc_arr['cn_no_enc_header'][]=$sch_pat_name_id;
                                $cn_no_enc_header .="<tr><td class='text-left text-nowrap'>".$sch_app_date_imp."</td><td class='text-left text-nowrap'>".$sch_app_time_imp."</td><td class='text-left text-nowrap'><a href='javascript:void(0);' class='text-left' onClick='acc_redirect($sch_pat_id);' style='color:#F00;'>".$sch_pat_name_id."</a></td><td class='text-left text-nowrap'>".$sch_proc."</td><td class='text-left'>".$sch_app_status."</td></tr>";
                            }else{
                                $app_enc_arr['no_enc_data'][]=$sch_pat_name_id;
                                $no_enc_data .="<tr><td class='text-left text-nowrap'>".$sch_app_date_imp."</td><td class='text-left text-nowrap'>".$sch_app_time_imp."</td><td class='text-left text-nowrap' style='color:#F00;'><a href='javascript:void(0);' class='text-left' onClick='acc_redirect($sch_pat_id);' style='color:#F00;'>".$sch_pat_name_id."</a></td><td class='text-left text-nowrap'>".$sch_proc."</td><td class='text-left'>".$sch_app_status."</td></tr>";
                           		$sb_csv_arr[$sg]['nsb_sch_app_date_imp']=$sch_app_date_imp;
								$sb_csv_arr[$sg]['nsb_sch_app_time_imp']=$sch_app_time_imp;
								$sb_csv_arr[$sg]['nsb_sch_pat_name_id']=$sch_pat_name_id;
								$sb_csv_arr[$sg]['nsb_sch_proc']=$sch_proc;
								$sb_csv_arr[$sg]['nsb_sch_app_status']=$sch_app_status;
						    }
                        }
                    }
                }
                ?>
                </tbody>
                </table>
                <?php
                if($no_enc_data!=""){
                    $no_enc_header ='<div class="panel-group" id="auto_resp_temp"><div class="panel"><div class="panel panel-default"><div class="panel-heading pointer" data-toggle="collapse" data-parent="#auto_resp_temp" href="#no_enc_data_id" aria-expanded="true"><h4 class="panel-title"><span class="glyphicon glyphicon-menu-down"></span><span>Not Processed/ No Super Bill Patients ('.count($app_enc_arr['no_enc_data']).')</span></h4></div></div><div id="no_enc_data_id" class="panel-collapse collapse" data-temp-type="1" aria-expanded="true"><table class="table table-bordered table-striped"><tr class="grythead"><td style="width:90px;">Appt Date</td><td style="width:90px;">Appt Time</td><td style="width:270px;">Pt Name - ID</td><td style="width:270px;">Procedure</td><td>Status</td></tr>'.$no_enc_data.'</table></div></div></div>';
					$sb_csv_header_main=array('Not Processed/ No Super Bill Patients ('.count($app_enc_arr['no_enc_data']).')');
				}
                if($cn_no_enc_header!=""){
                    $cn_no_enc_header ='<div class="panel-group" id="auto_resp_temp1"><div class="panel"><div class="panel panel-default"><div class="panel-heading pointer" data-toggle="collapse" data-parent="#auto_resp_temp1" href="#cn_no_enc_header_id" aria-expanded="true"><h4 class="panel-title"><span class="glyphicon glyphicon-menu-down"></span><span>Cancel/No Show Appointments ('.count($app_enc_arr['cn_no_enc_header']).')</span></h4></div></div><div id="cn_no_enc_header_id" class="panel-collapse collapse" data-temp-type="1" aria-expanded="true"><table class="table table-bordered table-striped"><tr class="grythead"><td style="width:90px;">Appt Date</td><td style="width:90px;">Appt Time</td><td style="width:270px;">Pt Name - ID</td><td style="width:270px;">Procedure</td><td>Status</td></tr>'.$cn_no_enc_header.'</table></div></div></div>';
                }
                echo $no_enc_header.$cn_no_enc_header;
            ?>	
        <?php } ?>
    </div><input type="hidden" name="total_ids" id="total_ids" value="<?php echo $g;?>"></form>
<div class="row ad_modal_footer" style="margin-top:5px;">	
    <?php if(count($data_detail)>0){ ?>
        <!-- Post Payments/Charges Date -->
        <div class="col-sm-4 form-inline"><label>Post Payments/Post Charges Date:</label>	<div class="input-group"><input id="date2" type="text"  name="post_date" value="<?php echo date('m-d-Y');?>"  onBlur="checkdate(this);" class="date-pick input-sm form-control"><div class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>		</div>	</div></div>
        <div class="col-sm-7 text-left col-sm-offset-1" id="module_buttons">
            <?php $un_post_imp=implode(',',array_unique($unpost_ids_arr)); ?>
            <?php $tot_ids_imp=implode(',',array_unique($tot_ids_arr)); ?>
            <input type="hidden" name="total_ids_imp" id="total_ids_imp" value="<?php echo $tot_ids_imp; ?>">
            <input type="hidden" name="unpost_ids_imp" id="unpost_ids_imp" value="<?php echo $un_post_imp; ?>">
            <input type="button" class="btn btn-success" id="search"  name="search" value="Search"  onClick="day_search();"/>
            <input type="button" class="btn btn-success" id="post_payments"  name="post_payments" value="Post Payments" onClick="post_payment_fun();"/>
            <input type="button" class="btn btn-success" id="post_charges"  name="post_charges" value="Post Charges" onClick="post_charges_fun('');"/>
            <input type="button" class="btn btn-success" id="print"  name="print" value="Save & Print" onClick="print_save();"/>
            <input type="button" class="btn btn-success" id="export_csv"  name="export_csv" value="Export CSV" onClick="download_file_name();"/>
            <input type="button" class="btn btn-danger" id="close"  name="close" value="Close" onClick="window.close();"/>
        </div>	
    <?php }else{?>
        <div class="col-sm-12 text-center" id="module_buttons"><input type="button" class="btn btn-success" id="search"  name="search" value="Search"  onClick="day_search();"/></div>
    <?php } ?>
</div>
<script type="text/javascript">
	var f='<?php echo $g; ?>';
	for(var j=1;j<=f;j++){
		var obj1 = $('#cpt_code_'+j).typeahead({source:customarrayProcedure});
		var obj2 = $('#dx1_'+j).typeahead({source:customarrayDiag});
		var obj3 = $('#dx2_'+j).typeahead({source:customarrayDiag});
		var obj4 = $('#dx3_'+j).typeahead({source:customarrayDiag});
		var obj5 = $('#dx4_'+j).typeahead({source:customarrayDiag});
		var obj6 = $('#mod1_'+j).typeahead({source:customarrayModifiers});
		var obj7 = $('#mod2_'+j).typeahead({source:customarrayModifiers});
		var obj8 = $('#mod3_'+j).typeahead({source:customarrayModifiers});
		var obj11 = $('#mod4_'+j).typeahead({source:customarrayModifiers});
		if(document.getElementById('tos_'+j)){
			var obj9 = $('#tos_'+j).typeahead({source:customarrayTos}); 
		}
		if(document.getElementById('pos_'+j)){
			var obj10 = $('#pos_'+j).typeahead({source:customarrayPos}); 
		}
	}
	$(function(){
		$('.collapse').on('show.bs.collapse', function(){
			$(this).prev('.panel').find(".glyphicon-menu-down").removeClass("glyphicon-menu-down").addClass("glyphicon-menu-up");
		}).on('hide.bs.collapse', function(){
			$(this).prev('.panel').find(".glyphicon-menu-up").removeClass("glyphicon-menu-up").addClass("glyphicon-menu-down");
		});
		$("#main_container_div").css('height',($(window).height()-200));
	});
</script>
<?php

if($_REQUEST['print_frm']=='print'){
$print_file_content='
	<page backtop="4mm" backbottom="5mm">
			<table width="1024" bordercolor="#FFF3E8"  cellpadding="0" cellspacing="0" border="1">
				<tr bgcolor="#4684ab" >
					<td class="text_b_w"  width="170" style="font-size:11px;">Pt Name - ID</td>
					<td class="text_b_w"  width="45"  style="font-size:11px;">DOS</td>
				<!--<td class="text_b_w"  width="2"	  style="font-size:11px;">Eid</td>-->
					<td class="text_b_w"  width="70"  style="font-size:11px;">Ins Case</td>
					<td class="text_b_w"  width="35"  style="font-size:11px;">Pri</td>
					<td class="text_b_w"  width="35"  style="font-size:11px;">Sec</td>
					<td class="text_b_w"  width="50"  style="font-size:11px;" align="center">Self Pay</td>
					<td class="text_b_w"  width="40"  style="font-size:11px;">CPT</td>
					<td class="text_b_w"  width="35"  style="font-size:11px;">Units&nbsp;</td>
					<td class="text_b_w"  width="60"  style="font-size:11px;">DX - Mod</td>
				<!--<td class="text_b_w"  width=""    style="font-size:11px;" align="center">Charges</td>-->
					<td class="text_b_w"  width="60"  style="font-size:11px;" align="center">Net Amt</td>
					<td class="text_b_w"  width="60"  style="font-size:11px;" align="center">Paid Amt</td>
					<td class="text_b_w"  width="'.$copay_data_wd.'"  style="font-size:11px;" align="center">'.$copay_label.'</td>
					<td class="text_b_w"  width="'.$ci_co_data_wd.'" style="font-size:11px;" align="center">'.$ci_co_label.'</td>
					<td class="text_b_w"  width="'.$pt_pmt_data_wd.'" style="font-size:11px;" align="center">'.$pt_pmt_label.'</td>
				<!--<td class="text_b_w"  width="" style="font-size:11px;" align="center">T.Visit Charges</td>-->
					<td class="text_b_w"  width="" style="font-size:11px;">POS Fac.</td>
					<td class="text_b_w"  width="55"  style="font-size:11px;">Auth / Ref</td>
				</tr>
				<tr>
					<td style="height:10px" colspan="19"></td>
				</tr>
			'.$print_data.'
		</table>
	</page>
';


$print_file_name = "day_charges_print.html";
$file_path = write_html($print_file_content,$print_file_name );
?>
<script type="text/javascript">
	var file_name = '<?php print $print_file_name; ?>';
	var html_file_loc = '<?php echo $file_path; ?>';
	top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
	top.html_to_pdf(html_file_loc,'l',file_name);
</script>
<?php } 
$csv_file = "day_charges_csv.csv"; 
$csv_file_path = write_html('',$csv_file);
$csv_headers = array('Pt Name - ID','DOS','Eid','Ins Case','Pri','Sec','Self Pay','CPT','Units','Dx Codes','Mod','Charges','Net Amt','Paid Amt','Copay','CI/CO Total/Bal','Pt Pmt Total/Bal','Total Visit Charges','TOS','POS','POS Facility','Ref#/Auth#','Billing Provider','Credited Provider','Referring Provider');
$fp = fopen($csv_file_path,'w');
fputcsv($fp,$csv_headers);
foreach($csv_arr as $row)
fputcsv($fp, $row,",","\"");

if($sb_csv_header_main!=""){
	for($k=0;$k<=5;$k++){
		fputcsv($fp,array(''));
	}
	fputcsv($fp,$sb_csv_header_main);
	$sb_csv_header = array('Appt Date','Appt Time','Pt Name - ID','Procedure','Status');
	fputcsv($fp,$sb_csv_header);
	foreach($sb_csv_arr as $row)
	fputcsv($fp, $row,",","\"");
}

fclose($fp);	
?>
<script type="text/javascript">
	function download_file_name(){
		var csv_file_path = '<?php echo base64_encode($csv_file_path); ?>';
		var url = "../billing/downloadFile.php?day_charges_file="+csv_file_path;
		window.location=url;
	}
</script>
<style>
.tooltip-inner{
    max-width: 300px !important;
}
</style>   
</body>
</html>