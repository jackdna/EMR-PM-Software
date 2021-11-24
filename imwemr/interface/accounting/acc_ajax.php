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
include_once(dirname(__FILE__)."/../../config/globals.php"); 
include_once(dirname(__FILE__)."/../../library/classes/acc_functions.php");
include_once(dirname(__FILE__)."/../../library/classes/common_function.php");
include_once(dirname(__FILE__)."/../../library/classes/work_view/wv_functions.php");
include_once(dirname(__FILE__)."/../../library/classes/work_view/Poe.php");
include_once(dirname(__FILE__)."/../../library/classes/work_view/CPT.php");

$action_type_arr=explode(',',$_REQUEST['action_type']);
$patient_id=$_SESSION['patient'];
$ins_id=$_REQUEST['ins_id'];
$ref_phy=$_REQUEST['ref_phy'];
$curr_date=date('Y-m-d');
$dat=$_REQUEST['dat'];
$pos_id=$_REQUEST['pos_id'];
$dx_code = $_REQUEST['dx'];
$proc_code = rawurldecode($_REQUEST['proc_code']);
$enc_id = $_REQUEST['enc_id'];
$visit_code = $_REQUEST['visit_code'];
$auth_number = $_REQUEST['auth_number'];
$ins_case_id = $_REQUEST['ins_case_id'];
$fac_id = $_REQUEST['fac_id'];
$phy_name=$_REQUEST['phy_name'];
$enc_proc_codes=$_REQUEST['enc_proc_codes'];
$del_comment_id=$_REQUEST['del_comment_id'];
$encounter_id=$_REQUEST['encounter_id'];
if(in_array("current_case",$action_type_arr)){
	$current_caseid=0;
	$getInsCaseTypeStr = "SELECT ins_caseid,ins_case_type FROM insurance_case WHERE patient_id='$patient_id' AND case_status='Open' Order By start_date DESC";
	$getInsCaseTypeQuery=imw_query($getInsCaseTypeStr);
	while($getInsCaseTypeRow=imw_fetch_array($getInsCaseTypeQuery)){
		$openInsCaseId[]=$getInsCaseTypeRow['ins_caseid'];
		$current_caseid=$getInsCaseTypeRow['ins_case_type'];
	}	
	$_SESSION["current_caseid"]=$current_caseid;
	$ret_data['current_caseid']=$current_caseid;
}

if(in_array("payment_comment",$action_type_arr)){
	$pay_comm_arr=array();
	$pay_comm_imp="";
	$pay_comm_qry=imw_query("select paymentscomment.encComments from paymentscomment 
			join patient_charge_list on patient_charge_list.encounter_id=paymentscomment.encounter_id 
			where paymentscomment.patient_id='$patient_id' and paymentscomment.reminder_date='$curr_date' 
			and paymentscomment.commentsType='Internal' 
			and paymentscomment.encounter_id>0 and patient_charge_list.del_status='0'");
	if(imw_num_rows($pay_comm_qry)>0){
		while($pay_comm_row=imw_fetch_array($pay_comm_qry)){
			if(trim($pay_comm_row['encComments'])!=""){
				$pay_comm_arr[]= nl2br($pay_comm_row['encComments']);
			}
		}
		$pay_comm_imp=implode('<br>',$pay_comm_arr);
	}	
	$ret_data['pay_comm']=$pay_comm_imp;
}

if(in_array("pat_account_status",$action_type_arr)){
	$status_name="";
	$activeId = get_account_status_id('Active');
	$getCollectionAmtStr = "SELECT pat_account_status, account_status.status_name FROM patient_data LEFT JOIN account_status 
						ON account_status.id = patient_data.pat_account_status 
						WHERE patient_data.id='$patient_id' AND pat_account_status>0 AND pat_account_status!='$activeId'";
	$getCollectionAmtQry = imw_query($getCollectionAmtStr);
	if(imw_num_rows($getCollectionAmtQry)>0){
		$getCollectionAmtRes = imw_fetch_array($getCollectionAmtQry);
		$status_name=$getCollectionAmtRes['status_name'];
	}
	$ret_data['status_name']=$status_name;
}

if(in_array("acc_notes_comment",$action_type_arr)){
	$whr_div="and acc_comment='accounting'";
	$msg_arr=array();
	$sel_note=imw_query("select notes from patient_charge_list_details where del_status='0' and patient_id='$patient_id' and notes!='' $whr_div");
	while($row_note=imw_fetch_array($sel_note)){
		$msg_arr[$row_note['notes']]=$row_note['notes'];
	}
	$ret_data['notes']=implode('<br>',$msg_arr);
}

if(in_array("poe",$action_type_arr)){
	$oPoe = new Poe($patient_id,'');
	$ret_data['poe']=$oPoe->showAlert("3","yes");
}

/*
if(in_array("admin_alert",$action_type_arr)){
	$OBJPatSpecificAlert = new CLSAlerts();
	$alertToDisplayAt="Accounting";
	$ret_data['adm_alt']=$OBJPatSpecificAlert->getAdminAlert($patient_id,$alertToDisplayAt,'');
}

if(in_array("pat_specific_alert",$action_type_arr)){
	$OBJPatSpecificAlert = new CLSAlerts();
	$alertToDisplayAt="Accounting";
	$ret_data['pat_spec_alt']=$OBJPatSpecificAlert->getPatSpecificAlert($patient_id,$alertToDisplayAt).$OBJPatSpecificAlert->writeJS();
}*/

if(in_array("previous_statement_butt",$action_type_arr)){
	$rowCount=0;
	$qry = imw_query("select count(patient_id) as rowCount from previous_statement where previous_statement.statement_acc_status=1 and previous_statement.statement_satus  = '0' 
					  and previous_statement.patient_id = '$patient_id' limit 0,1");
	if(imw_num_rows($qry)>0){
		$preQryRes = imw_fetch_array($qry);
		$rowCount=$preQryRes['rowCount'];
	}
	$ret_data['prev_stmt_butt']=$rowCount;
}

if(in_array("payments_comment_butt",$action_type_arr)){
	$rowCountComm=0;
	$qry = imw_query("select count(patient_id) as rowCountComm from paymentscomment where paymentscomment.patient_id = '$patient_id' limit 0,1");
	if(imw_num_rows($qry)>0){
		$preQryRes = imw_fetch_array($qry);
		$rowCountComm=$preQryRes['rowCountComm'];
	}
	$ret_data['pay_comm_butt']=$rowCountComm;
}

if(in_array("insurance_description",$action_type_arr)){
	$ins_desc_arr=array();
	$ins_div_arr=getInsCompby_pid($patient_id);
	if(count($ins_div_arr)>0){
		for($i=0;$i<count($ins_div_arr);$i++){
			$j=$i+1;
			$ins_desc_arr[$ins_div_arr[$i]['name']]=' &bull; '.$ins_div_arr[$i]['name'].' - '.$ins_div_arr[$i]['attn'];
		}
	}
	$ret_data['ins_desc']=implode('<br>',$ins_desc_arr);
}

if(in_array("patient_notes",$action_type_arr)){
	$message="";
	$sql_notes_alert = "select patient_notes AS notes, chk_notes_accounting AS chk FROM patient_data Where id='$patient_id'";
	$resource = imw_query($sql_notes_alert);
	$notes_array = imw_fetch_array($resource);
	if($notes_array["chk"]==1 && trim($notes_array["notes"])!="")
	{
		$message = preg_replace("/[\n\r]/","<br/>",$notes_array["notes"]);
	}
	$ret_data['pat_notes']=$message;
}

if(in_array("ci_pp",$action_type_arr)){
	include_once("ci_pp_ajax.php");
}

if(in_array("ass_plan_rg",$action_type_arr)){
	$html_ob="yes";
	$a_p_dos="";
	if($_REQUEST['date_of_service']!=""){
		$a_p_dos=$_REQUEST['date_of_service'];
	}
	include_once("assesmentDivList.php");
}

if(in_array("invalid_claim",$action_type_arr)){
	if($_REQUEST['charge_list_id']!=""){
		$charge_list_id_arr[]=$_REQUEST['charge_list_id'];
		$type=$_REQUEST['type'];
		$charge_check = ShowValidChargeList($charge_list_id_arr,$type);
		if(count($charge_check['all_error'][$_REQUEST['charge_list_id']])>0){
			$ret_data['invalid_claim_reason']= "&bull; ".implode('<br>&bull; ',$charge_check['all_error'][$_REQUEST['charge_list_id']]);
		}else{
			$ret_data['invalid_claim_reason']="";
		}
	}
}
if(in_array("curr_case",$action_type_arr)){
	$qry_case_id = "select case_type_id from schedule_appointments where sa_app_start_date='$dat' and  sa_patient_id='$patient_id'";												
	$run_case_id = imw_query($qry_case_id);	
	$fet_case_list=imw_fetch_array($run_case_id);
	$curCaseId=$fet_case_list['case_type_id'];
	if($curCaseId>0){
		$qry_case_nam = "select insurance_case_types.case_name
		from insurance_case join insurance_case_types
		on insurance_case_types.case_id = insurance_case.ins_case_type 
		join insurance_data on insurance_data.ins_caseid = insurance_case.ins_caseid
		join insurance_companies on insurance_companies.id = insurance_data.provider
		where insurance_case.patient_id = '$patient_id' and insurance_case.case_status = 'Open' and insurance_data.provider > 0
		and insurance_case.ins_caseid='$curCaseId' and insurance_companies.in_house_code != 'n/a'";												
		$run_case_nam = imw_query($qry_case_nam);	
		$fet_case_nam_list=imw_fetch_array($run_case_nam);
		$curCaseName=$fet_case_nam_list['case_name'];
		$curCaseName_final=$curCaseName.'-';
		$ret_data['curr_case']=$curCaseName_final.$curCaseId;
	}else{
		$ret_data['curr_case']="";
	}
}
if(in_array("referral_info",$action_type_arr)){
	$reff_qry=imw_query("select patient_reff.reffral_no,patient_reff.reff_type,insurance_data.referal_required 
				from patient_reff join insurance_data
				on insurance_data.id = patient_reff.ins_data_id where 
				insurance_data.pid = '$patient_id' 
				and insurance_data.ins_caseid = '$ins_id'
				and patient_reff.reff_phy_id='$ref_phy'
				and (patient_reff.end_date='0000-00-00' or patient_reff.end_date >= '$dat') 
				and patient_reff.effective_date <= '$dat'
				and patient_reff.no_of_reffs > '0' and patient_reff.del_status='0'
				order by patient_reff.end_date desc,patient_reff.reff_id desc");
	while($reff_row=imw_fetch_array($reff_qry)){
		if($reff_row['referal_required']=='Yes' && $reff_row['reff_type']=='1'){
			$referral_arr[]=array($reff_row['reffral_no'],$xyz, $reff_row['reffral_no']);
		}
		if($reff_row['referal_required']=='Yes' && $reff_row['reff_type']=='2'){
			$referral_arr[]=array($reff_row['reffral_no'],$xyz, $reff_row['reffral_no']);
		}
		if($reff_row['referal_required']=='Yes' && $reff_row['reff_type']=='3'){
			$referral_arr[]=array($reff_row['reffral_no'],$xyz, $reff_row['reffral_no']);
		}
		$refNo1=$reff_row['reffral_no'];
	}
	$reff_drop_val_chk=get_simple_menu($referral_arr,"referral_menu","referral");
	$reff_drop_val=str_replace(',','__',$reff_drop_val_chk);
	
	$ret_data['reff_no']=$refNo1;
	$ret_data['reff_drop_val']=$reff_drop_val;
}

if(in_array("pos_fac_drop",$action_type_arr)){
	$data=$facility="";
	$qry=imw_query("select a.* from pos_facilityies_tbl a,pos_tbl b where b.pos_prac_code = '$pos_id' and b.pos_id = a.pos_id order by a.facilityPracCode");
	if(imw_num_rows($qry)>0){
		$facility_name = getFacilityNameRow($patient_id);
		$facility=$facility_name->pos_facility_id;	
	}else{
		$data='<option value="">'.imw_msg('drop_sel').'</option>';
	}
    $pos_facility_arr=array();
	while($row=imw_fetch_array($qry)){
        $pos_facility_arr[$row['pos_facility_id']]=$row;
	}
    
    //-------------------- get POS facility from Users POS facility group ------------------
    //if POS Facilty group exists and selected in logged in user
    $user_pos_fac_arr=array();
    if(isPosFacGroupEnabled() ){
        $u_sql_res=imw_query("Select id,posfacilitygroup_id from users where id='".$_SESSION['authId']."' and posfacilitygroup_id!='' ");
        $user_row=imw_fetch_assoc($u_sql_res);
        $user_pos_id_fac_data_arr=array();
        if(empty($user_row)==false && isset($user_row['posfacilitygroup_id']) && $user_row['posfacilitygroup_id']!='') {
            $posfacilitygroup_ids_arr=json_decode(html_entity_decode($user_row['posfacilitygroup_id']), true);
            $posfacgroup_ids_str=(empty($posfacilitygroup_ids_arr)==false)? implode(',',$posfacilitygroup_ids_arr): '';
            
            $res1=imw_query("select a.* from pos_facilityies_tbl a,pos_tbl b where b.pos_prac_code = '$pos_id' and posfacilitygroup_id IN(".$posfacgroup_ids_str.") and b.pos_id = a.pos_id order by a.facilityPracCode");
            while($row1 = imw_fetch_assoc($res1)){
                $user_pos_fac_arr[]=$row1['pos_facility_id'];	
            }
        }
    }
    //-------------------- get POS facility from Users POS facility group ------------------
    
    
	foreach($pos_facility_arr as $row){
		$id = $row['pos_facility_id'];
		$facilityPracCode = $row['facilityPracCode'];
		$sel = $id == $facility?'selected="selected"':'';
        if(empty($user_pos_fac_arr)==false && (!in_array($id,$user_pos_fac_arr)) ) {
            if($id == $facility)
                { /*do not skip already selected pos facility which does not exists in users pos facility group */ }
            else 
                { /*skip pos facility those does not exists in users pos facility group */
                continue; }
        }
        if(isPosFacGroupEnabled() ){
            if( isset($row['posfacilitygroup_id']) && $row['posfacilitygroup_id']==0  && $id != $facility ) continue;
        }
		$data .='<option value="'.$id.'" '.$sel.'>'.$facilityPracCode.'</option>';
	}
	$ret_data['pos_fac_drop_val']=$data;
}

if(in_array("dx_code",$action_type_arr)){
	$dxCode="";
	$qry=imw_query("SELECT dx_code FROM diagnosis_code_tbl WHERE d_prac_code='$dx_code' OR diag_description='$dx_code' OR dx_code='$dx_code'");
	if(imw_num_rows($qry)>0){
		$row=imw_fetch_array($qry);
		$dx_code=$row['dx_code'];
	}
	$ret_data['dx_code_val']=$dx_code;
}

if(in_array("rev_code",$action_type_arr)){
	$rev_code_rev="";
	$sel_cpt=imw_query("select rev_code from cpt_fee_tbl where (cpt4_code='$proc_code' or cpt_prac_code='$proc_code') AND delete_status = '0'");
	$row_cpt=imw_fetch_array($sel_cpt);
	$rev_code_cpt=$row_cpt['rev_code'];
	
	if($rev_code_cpt!=""){
		$sel_rev=imw_query("select r_code from revenue_code where r_id='$rev_code_cpt'");
		$row_rev=imw_fetch_array($sel_rev);
		$rev_code_rev=$row_rev['r_code'];
	}
	
	$ret_data['rev_code_val']=$rev_code_rev;
}

if(in_array("tos_code",$action_type_arr)){
	$sel=imw_query("SELECT a.tos_prac_cod FROM tos_tbl a,cpt_fee_tbl b WHERE a.tos_id = b.tos_id and (b.cpt4_code='$proc_code' or b.cpt_desc='$proc_code') AND b.delete_status = '0' order by b.status asc");
	$row=imw_fetch_array($sel);
	$tos_prac_cod=$row['tos_prac_cod'];
	$ret_data['tos_code_val']=$tos_prac_cod;
}

if(in_array("visit_code",$action_type_arr)){
	$chl_num=0;
	$sup_num=0;
	$getCPTchlQry = imw_query("SELECT patient_charge_list_details.charge_list_detail_id FROM patient_charge_list join patient_charge_list_details 
					on patient_charge_list.charge_list_id=patient_charge_list_details.charge_list_id
					join cpt_fee_tbl on cpt_fee_tbl.cpt_fee_id=patient_charge_list_details.procCode
					where patient_charge_list.encounter_id!='$enc_id' and patient_charge_list.date_of_service='$dat' and patient_charge_list_details.del_status='0'
					and cpt_fee_tbl.cpt_fee_id in($visit_code) and patient_charge_list.patient_id='$patient_id'");
	$chl_num=imw_num_rows($getCPTchlQry);
					
	$getCPTsupQry = imw_query("SELECT superbill.idSuperBill FROM superbill join procedureinfo 
					on superbill.idSuperBill=procedureinfo.idSuperBill
					join cpt_fee_tbl on cpt_fee_tbl.cpt_prac_code=procedureinfo.cptCode
					where superbill.encounterId!='$enc_id' and superbill.dateOfService='$dat' and procedureinfo.delete_status='0'
					and cpt_fee_tbl.cpt_fee_id in($visit_code) and superbill.patientId='$patient_id' and postedStatus='0'");	
	$sup_num=imw_num_rows($getCPTsupQry);	
	$ret_data['visit_count']=$chl_num+$sup_num;
}

if(in_array("cpt_comment",$action_type_arr)){
	$qry = imw_query("select cpt_comments,mod1,mod2,mod3,mod4,units,cpt_tax from cpt_fee_tbl where (cpt_prac_code='$proc_code' OR cpt4_code='$proc_code' OR cpt_desc='$proc_code')and delete_status = '0' limit 0,1");
	$get_row=imw_fetch_array($qry);
	$ret_data['cpt_comment_val']=$get_row['mod1'].'~~~'.$get_row['mod2'].'~~~'.$get_row['mod3'].'~~~'.$get_row['units'].'~~~'.$get_row['cpt_comments'].'~~~'.$get_row['cpt_tax'].'~~~'.$get_row['mod4'];
}

if(in_array("pat_auth",$action_type_arr)){
	$qry = imw_query("select a_id,AuthAmount,date_format(cur_date,'%m-%d-%Y') as cur_date from patient_auth where auth_name = '$auth_number' and ins_case_id='$ins_case_id' order by no_of_reffs desc");
	$get_row=imw_fetch_array($qry);
	if($get_row['cur_date'] == '00-00-0000'){
		$get_row['cur_date'] = '';
	}
	$cur_date= $get_row['cur_date'];
	$ret_data['auth_amount']=$get_row['AuthAmount'];
	$ret_data['auth_id']=$get_row['a_id'];
	$ret_data['auth_cur_date']=$cur_date;
}

if(in_array("auth_info",$action_type_arr)){
	$auth_arr=array();
	$auth_id=$auth_no=$auth_amount=$auth_drop_val="";
	$primaryProviderId=get_provider_id($phy_name);
	$enc_proc_codes_exp=explode(';',$enc_proc_codes);
	$auth_whr=" and (auth_provider = '".$primaryProviderId."' or auth_provider='0')";
	$auth_qry=imw_query("select patient_auth.auth_name,patient_auth.a_id,patient_auth.AuthAmount,patient_auth.auth_cpt_codes,patient_auth.ins_type,
				insurance_data.auth_required 
				from patient_auth join insurance_data on insurance_data.id = patient_auth.ins_data_id
				where insurance_data.pid = '$patient_id' and insurance_data.ins_caseid = '$ins_id'
				and (patient_auth.end_date='0000-00-00' or patient_auth.end_date >= '$dat') 
				and patient_auth.auth_date <= '$dat' and patient_auth.no_of_reffs > '0' and patient_auth.auth_status='0' $auth_whr
				order by patient_auth.end_date desc,patient_auth.a_id desc");
	while($auth_row=imw_fetch_array($auth_qry)){
		$auth_cpt_codes_exp=array();
		if(trim($auth_row['auth_cpt_codes'])!=""){
			$auth_cpt_codes_exp=explode(';',$auth_row['auth_cpt_codes']);
		}
		if(array_intersect($enc_proc_codes_exp,$auth_cpt_codes_exp) || count($auth_cpt_codes_exp)==0){
			if($auth_row['auth_required']=='Yes' && $auth_row['ins_type']=='1'){
				$auth_arr[]=array($auth_row['auth_name'],$xyz, $auth_row['auth_name']);
			}
			if($auth_row['auth_required']=='Yes' && $auth_row['ins_type']=='2'){
				$auth_arr[]=array($auth_row['auth_name'],$xyz, $auth_row['auth_name']);
			}
			if($auth_row['auth_required']=='Yes' && $auth_row['ins_type']=='3'){
				$auth_arr[]=array($auth_row['auth_name'],$xyz, $auth_row['auth_name']);
			}
			$auth_id=$auth_row['a_id'];
			$auth_no=$auth_row['auth_name'];
			$auth_amount=$auth_row['AuthAmount'];
		}
	}
	
	$auth_drop_val_chk=get_simple_menu($auth_arr,"auth_no_menu","auth_no");
	$auth_drop_val=str_replace(',','__',$auth_drop_val_chk);
	
	$ret_data['auth_id']=$auth_id;
	$ret_data['auth_no']=$auth_no;
	$ret_data['auth_amount']=$auth_amount;
	$ret_data['auth_drop_val']=$auth_drop_val;
}

if(in_array("get_tax",$action_type_arr)){
	$sel=imw_query("SELECT fac_tax from facility WHERE id = '$fac_id'");
	$row=imw_fetch_array($sel);
	$ret_data['fac_tax_val']=str_replace('%','',$row['fac_tax']);
}

if(in_array("del_comment",$action_type_arr)){
	$pay_comm_qry=imw_query("DELETE FROM paymentscomment WHERE commentId = '$del_comment_id'");
	$ret_data['del_pay_comm']=$del_comment_id;
}

if(in_array("pri_sec_active_ins",$action_type_arr)){
	$ins_sql="SELECT insd.id,insd.type,insd.policy_number,insd.pid,insd.expiration_date,insd.self_pay_provider,insct.normal 
            FROM insurance_data insd
            INNER JOIN insurance_case insc ON(insd.ins_caseid=insc.ins_caseid)
            INNER JOIN insurance_case_types insct ON(insc.ins_case_type=insct.case_id)
            INNER JOIN insurance_companies inscm ON(inscm.id = insd.provider)
            WHERE insct.status=0
            AND insc.patient_id = '".$_SESSION['patient']."'
            AND insc.case_status = 'Open' 
            AND inscm.in_house_code != 'n/a'
            ORDER BY insct.normal DESC
            ";
    $ins_result=imw_query($ins_sql);
    $ins_data=array();
    if($ins_result && imw_num_rows($ins_result)>0){
        while($result_row=imw_fetch_assoc($ins_result)){
            if($result_row['normal']=='1'){
                if( ($result_row['expiration_date'] >= date('Y-m-d H:i:s') || $result_row['expiration_date']=='0000-00-00 00:00:00') && $result_row['self_pay_provider'] == '0') {
                    $result_row['ins_active']='active';
                } else if( ($result_row['self_pay_provider'] > '0' || $result_row['expiration_date']!='0000-00-00 00:00:00') && $result_row['normal']=='1' ) {
                    $result_row['self_pay']='Self Pay';
                } 
				if($ins_data[$result_row['type']]['ins_active']!="active"){
                	$ins_data[$result_row['type']]=$result_row;
				}
            }
            if($ins_data[$result_row['type']]['self_pay']!='' && $result_row['normal']!='1'){
                if( ($result_row['expiration_date'] >= date('Y-m-d H:i:s') || $result_row['expiration_date']=='0000-00-00 00:00:00') && $result_row['self_pay_provider'] == '0') {
                    $result_row['self_pay']='';
                    $ins_data[$result_row['type']]=$result_row;
                }
            }
            
        }
    }else{
	 	$ins_data['primary']['self_pay']='Self Pay';
		$ins_data['secondary']['self_pay']='Self Pay';
	}
    
    $active_ins.="<span><b>Pri :</b>&nbsp;&nbsp;&nbsp;</span><span>";
    if(empty($ins_data)==false && (isset($ins_data['primary'])) ) {
        if(isset($ins_data['primary']['ins_active']) && $ins_data['primary']['ins_active']=='active'){
            $active_ins.=$ins_data['primary']['policy_number'];
        }else if(isset($ins_data['primary']['self_pay']) && $ins_data['primary']['self_pay']!='') {
            $active_ins.=$ins_data['primary']['self_pay'];
        }
    }
    $active_ins.="</span>";

    $active_ins.="<br><span><b>Sec :</b>&nbsp;</span><span>";
    if(empty($ins_data)==false && (isset($ins_data['secondary'])) ) {
        if(isset($ins_data['secondary']['ins_active']) && $ins_data['secondary']['ins_active']=='active'){
            $active_ins.=$ins_data['secondary']['policy_number'];
        }else if(isset($ins_data['secondary']['self_pay']) && $ins_data['secondary']['self_pay']!='') {
            $active_ins.=$ins_data['secondary']['self_pay'];
        }
    }
    $active_ins.="</span>";
   
    
	$ret_data['pri_sec_active_ins']=$active_ins;
}

if(in_array("enc_payment_comment",$action_type_arr)){
	$enc_comm_div="";
	$pay_comm_qry=imw_query("select encComments,reminder_date from paymentscomment where encounter_id='$encounter_id' and encComments!='' ORDER BY commentId desc");
	if(imw_num_rows($pay_comm_qry)>0){
		$enc_comm_div='<table class="table table-bordered table-hover table-striped scroll release-table"><thead class="header"><tr class="grythead"><td class="text_10b">Notes</td><td class="text_10b" nowrap>Reminder Date</td></tr></thead><tbody>';
		while($pay_comm_row=imw_fetch_assoc($pay_comm_qry)){
			$enc_comm=$pay_comm_row['encComments'];
			$reminder_date=get_date_format($pay_comm_row['reminder_date']);
			$enc_comm_div.='<tr><td>'.$enc_comm.'</td><td style="vertical-align:top!important;" nowrap>'.$reminder_date.'</td></tr>';
		}
		$enc_comm_div.='</tbody></table>';
	}	
	$ret_data['enc_pay_comm']=$enc_comm_div;
}

echo json_encode($ret_data);

?>
