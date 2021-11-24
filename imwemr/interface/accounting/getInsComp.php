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
include_once(dirname(__FILE__)."/../../library/classes/cls_common_function.php");
$objCLSCommonFunction = new CLSCommonFunction;
$ins=$_REQUEST['ins'];
$eid=$_REQUEST['eid'];
if($_REQUEST['dos_send']!=""){
	$dos_send_arr=explode('-',$_REQUEST['dos_send']);
	$dos_send_imp=$dos_send_arr[2].'-'.$dos_send_arr[0].'-'.$dos_send_arr[1];
}
if($_REQUEST['pid']){
	$patient_id=$_REQUEST['pid'];
}else{
	$patient_id=$_SESSION['patient'];
}
/* Get Patient Data*/
$qry = imw_query("select * from patient_data where id = '$patient_id'");
$patientDetail = imw_fetch_object($qry);

/* Get Insurance Data*/
$getStartEndDate="SELECT * FROM insurance_case WHERE ins_caseid='$ins'";
	$getStartEndDate=imw_query($getStartEndDate);
	$getStartEndDateRow=imw_fetch_array($getStartEndDate);
	$effective_date=$getStartEndDateRow['start_date'];
	$expiration_date=$getStartEndDateRow['end_date'];
	$ins_case_type=$getStartEndDateRow['ins_case_type'];
	
	$effective_date=get_date_format($effective_date);
	$expiration_date=get_date_format($expiration_date);

/* Get Insurance Case Type*/
$ins_type=imw_query("SELECT vision FROM insurance_case_types WHERE case_id='$ins_case_type'");
$ins_run=imw_fetch_array($ins_type);
$vision_chk=$ins_run['vision'];

/* Get Encounter detail*/	
if($eid){
	$sel_chl=imw_query("select date_of_service,primaryInsuranceCoId,secondaryInsuranceCoId,tertiaryInsuranceCoId,reff_phy_id,case_type_id,referral from patient_charge_list where del_status='0' and encounter_id='$eid'");
	$row_chl=imw_fetch_array($sel_chl);
	if(imw_num_rows($sel_chl)>0){
		if($row_chl['case_type_id']==$ins){
			$dos_date=$row_chl['date_of_service'];
			$primaryInsuranceCoId=$row_chl['primaryInsuranceCoId'];
			$secondaryInsuranceCoId=$row_chl['secondaryInsuranceCoId'];
			$tertiaryInsuranceCoId=$row_chl['tertiaryInsuranceCoId'];
		}
		$reff_phy_id=$row_chl['reff_phy_id'];
		$referral=$row_chl['referral'];
	}
}

//Secondary copay collect check
$copay_policies = ChkSecCopay_collect($primaryInsuranceCoId);
$secCopay=$copay_policies;
//Secondary copay collect check

$qry = imw_query("SELECT sec_copay_collect_amt,sec_copay_for_ins  FROM copay_policies WHERE policies_id='1'");
$policyQryRes=imw_fetch_array($qry);
$sec_copay_collect_amt = $policyQryRes['sec_copay_collect_amt'];
$sec_copay_for_ins = $policyQryRes['sec_copay_for_ins'];

$cur_dat=date('Y-m-d');
if($primaryInsuranceCoId>0){
	$old_id_whr1="provider='$primaryInsuranceCoId'";
}else{
	if($dos_send_imp!="" && $dos_send_imp!='0000-00-00'){
		$dos_date=$dos_send_imp;
	}else if($dos_date){
		$dos_date=$dos_date;
	}else{
		$dos_date=$cur_dat;
	}
	$new_id_whr1="(date_format(effective_date,'%Y-%m-%d')<='$dos_date' and (date_format(expiration_date,'%Y-%m-%d')>='$dos_date' or  date_format(expiration_date,'%Y-%m-%d')='0000-00-00'))";
}
if($secondaryInsuranceCoId>0){
	$old_id_whr2="provider='$secondaryInsuranceCoId'";
}else{
	if($dos_send_imp!="" && $dos_send_imp!='0000-00-00'){
		$dos_date=$dos_send_imp;
	}else if($dos_date){
		$dos_date=$dos_date;
	}else{
		$dos_date=$cur_dat;
	}
	$new_id_whr2="(date_format(effective_date,'%Y-%m-%d')<='$dos_date' and (date_format(expiration_date,'%Y-%m-%d')>='$dos_date' or  date_format(expiration_date,'%Y-%m-%d')='0000-00-00'))";
}
if($tertiaryInsuranceCoId>0){
	$old_id_whr3="provider='$tertiaryInsuranceCoId'";
}else{
	if($dos_send_imp!="" && $dos_send_imp!='0000-00-00'){
		$dos_date=$dos_send_imp;
	}else if($dos_date){
		$dos_date=$dos_date;
	}else{
		$dos_date=$cur_dat;
	}
	$new_id_whr3="(date_format(effective_date,'%Y-%m-%d')<='$dos_date' and (date_format(expiration_date,'%Y-%m-%d')>='$dos_date' or  date_format(expiration_date,'%Y-%m-%d')='0000-00-00'))";
}
// PRIMARY
$getPrimaryInsCoDetails = imw_query("SELECT * FROM insurance_data WHERE ins_caseid='$ins' AND pid='$patient_id' AND type='primary' and provider > 0 and $old_id_whr1 $new_id_whr1 order by actInsComp desc, effective_date desc, id desc");
$getPrimaryInsCoRow = imw_fetch_array($getPrimaryInsCoDetails);
$providerId = $getPrimaryInsCoRow['provider'];
$ins_data_id_pri = $getPrimaryInsCoRow['id'];
	$copay1 = explode('/',$getPrimaryInsCoRow['copay']);
	$copay = $copay1[0];
	$auth_req_pri = $getPrimaryInsCoRow['auth_required'];
	$referal_req_pri = $getPrimaryInsCoRow['referal_required'];
	$ins_type = $getPrimaryInsCoRow['type'];
	
	$getPrimaryInsCompany = imw_query("SELECT * FROM insurance_companies WHERE id='$providerId'");
	$getPrimaryInsCompanyRow = imw_fetch_array($getPrimaryInsCompany);
	$primaryInsCoName = $getPrimaryInsCompanyRow['in_house_code'];
	$primaryInsId = $getPrimaryInsCompanyRow['id'];
	$primary_institutional_type = $getPrimaryInsCompanyRow['institutional_type'];
// PRIMARY

// First Scan Documents
$scan_img_wid = '';
$scan_img_src = '';
if(trim($getPrimaryInsCoRow['scan_card']) != ''){
	$firstScanImage = '../main/uploaddir'.$getPrimaryInsCoRow['scan_card'];
	if(realpath($firstScanImage) != ''){
		$scan_img_wid = newImageResize($firstScanImage,20,20);
		$scan_img_src = "<img style=\"cursor:pointer\" onClick=\"show_scanned('$ins_data_id_pri'__1__'$ins_type')\" src=\"".$firstScanImage."\" $scan_img_wid>";
	}
}
$pri_scan_card1 = $scan_img_src;
	
//--- SECOND SCAN DOCUMENTS -----
$scan2_img_wid = '';
$scan2_img_src = '';
if(trim($getPrimaryInsCoRow['scan_card2']) != ''){
	$secondScanImage = '../main/uploaddir'.$getPrimaryInsCoRow['scan_card2'];
	if(realpath($secondScanImage) != ''){
		$scan2_img_wid = newImageResize($secondScanImage,20,20);
		$scan2_img_src = "<img style=\"cursor:pointer\" onClick=\"show_scanned('$ins_data_id_pri'__2__'$ins_type')\" src=\"".$secondScanImage."\" $scan2_img_wid>";
	}
}
$pri_scan_card2 = $scan2_img_src;
	
$auth_arr=array();
//------ Get Auth number primary ------------
if($auth_req_pri=='Yes'){
	$selQry_auth = imw_query("select auth_name,a_id,AuthAmount from patient_auth 
						where 
					patient_id='$patient_id' and ins_case_id='$ins'  
					and ins_data_id='$ins_data_id_pri' and ins_type='1' and no_of_reffs>0
					and (end_date='0000-00-00' or end_date >= '$dos_date') and auth_date <= '$dos_date'
					order by a_id desc");
	while($row_auth = imw_fetch_array($selQry_auth)){
		$auth_no_pri[] = $row_auth['auth_name'];
		$auth_amount_pri[] = $row_auth['AuthAmount'];
		$auth_id_pri[] = $row_auth['a_id'];
		$auth_arr[]=array($row_auth['auth_name'],$xyz, $row_auth['auth_name']);
	}
}	
//------ Get Auth number primary ------------
	

if($reff_phy_id>0){
	$reffPhyId = $reff_phy_id;
	$refNo1 = $referral;
}else{
	$reffDetails = getPatientReffPhy($patient_id,$ins,'primary');
	if($reffDetails->reff_phy_id>0){
		$reffPhyId = $reffDetails->reff_phy_id;
	}
	else{
		$reffPhyId = $patientDetail->primary_care_id;
	}
}
$reffName=$objCLSCommonFunction->get_ref_phy_name($reffPhyId);

// SECONDARY
$getSecondaryInsCoDetails = imw_query("SELECT * FROM insurance_data WHERE ins_caseid='$ins' AND pid='$patient_id' AND type='secondary' and provider > 0 and $old_id_whr2 $new_id_whr2 order by actInsComp desc, effective_date desc, id desc ");
$getSecondaryInsCoRow = imw_fetch_array($getSecondaryInsCoDetails);
$providerId = $getSecondaryInsCoRow['provider'];
$ins_data_id_sec = $getSecondaryInsCoRow['id'];
$auth_req_sec = $getSecondaryInsCoRow['auth_required'];
$referal_req_sec = $getSecondaryInsCoRow['referal_required'];
$sec_ins_type=$getSecondaryInsCoRow['type'];
if($secCopay == "Yes"){
	if($sec_copay_collect_amt>=$getSecondaryInsCoRow['copay'] || $sec_copay_for_ins==''){
		$secCopayAmt1 = explode('/',$getSecondaryInsCoRow['copay']);
		$secCopayAmt = $secCopayAmt1[0];
	}
}
$getSecondaryInsCompany = imw_query("SELECT * FROM insurance_companies WHERE id='$providerId'");
$getSecondaryInsCompanyRow = imw_fetch_array($getSecondaryInsCompany);
$secondaryInsCoName = $getSecondaryInsCompanyRow['in_house_code'];
$secondaryInsId = $getSecondaryInsCompanyRow['id'];
		
// First Scan Documents
$scan_img_wid = '';
$scan_img_src = '';
if(trim($getSecondaryInsCoRow['scan_card']) != ''){
	$firstScanImage = '../main/uploaddir'.$getSecondaryInsCoRow['scan_card'];
	if(realpath($firstScanImage) != ''){
		$scan_img_wid = newImageResize($firstScanImage,20,20);
		$scan_img_src = "<img style=\"cursor:pointer\" onClick=\"show_scanned('$ins_data_id_sec'__1__'$sec_ins_type')\" src=\"".$firstScanImage."\" $scan_img_wid>";
	}
}
$sec_scan_card1 = $scan_img_src;
	
//--- SECOND SCAN DOCUMENTS -----
$scan2_img_wid = '';
$scan2_img_src = '';
if(trim($getSecondaryInsCoRow['scan_card2']) != ''){
	$secondScanImage = '../main/uploaddir'.$getSecondaryInsCoRow['scan_card2'];
	if(realpath($secondScanImage) != ''){
		$scan2_img_wid = newImageResize($secondScanImage,20,20);
		$scan2_img_src = "<img style=\"cursor:pointer\" onClick=\"show_scanned('$ins_data_id_sec'__2__'$sec_ins_type')\" src=\"".$secondScanImage."\" $scan2_img_wid>";
	}
}
$sec_scan_card2 = $scan2_img_src;
		
//------ Get Auth number Secondary ------------
if($auth_req_sec=='Yes'){
	$selQry_auth1 = imw_query("select auth_name,a_id,AuthAmount from patient_auth where patient_id='$patient_id' and ins_case_id='$ins' and ins_data_id='$ins_data_id_sec' and ins_type='2'
								and no_of_reffs>0 and (end_date='0000-00-00' or end_date >= '$dos_date') and auth_date <= '$dos_date' order by a_id desc");
	while($row_auth1 = imw_fetch_array($selQry_auth1)){
		$auth_no_sec[] = $row_auth1['auth_name'];
		$auth_amount_sec[] = $row_auth1['AuthAmount'];
		$auth_id_sec[] = $row_auth1['a_id'];
		$auth_sec_arr[]=array($row_auth1['auth_name'],$xyz, $row_auth1['auth_name']);
	}
}	
//------ Get Auth number Secondary ------------
		
// SECONDARY
$reffDetails = getPatientReffPhy($patient_id,$ins,'secondary');
$refNo2 = $reffDetails->reffral_no;

// TERTIARY
$getTertiaryInsCoDetails = imw_query("SELECT * FROM insurance_data WHERE ins_caseid='$ins' AND pid='$patient_id' AND type='tertiary' and provider > 0 and $old_id_whr3 $new_id_whr3 order by actInsComp desc, effective_date desc, id desc");
$getTertiaryInsCoRow = imw_fetch_array($getTertiaryInsCoDetails);
$providerId=$getTertiaryInsCoRow['provider'];
$auth_req_tri=$getTertiaryInsCoRow['auth_required'];
$referal_req_ter = $getTertiaryInsCoRow['referal_required'];
$ins_data_id_tri = $getTertiaryInsCoRow['id'];
$tri_ins_type = $getTertiaryInsCoRow['type'];
	
$getTertiaryInsCompany = imw_query("SELECT * FROM insurance_companies WHERE id='$providerId'");
$getTertiaryInsCompanyRow = imw_fetch_array($getTertiaryInsCompany);
$tertiaryInsCoName = $getTertiaryInsCompanyRow['in_house_code'];
$tertiaryInsId = $getTertiaryInsCompanyRow['id'];
		
// First Scan Documents
$scan_img_wid = '';
$scan_img_src = '';
if(trim($getTertiaryInsCoRow['scan_card']) != ''){
	$firstScanImage = '../main/uploaddir'.$getTertiaryInsCoRow['scan_card'];
	if(realpath($firstScanImage) != ''){
		$scan_img_wid = newImageResize($firstScanImage,20,20);
		$scan_img_src = "<img style=\"cursor:pointer\" onClick=\"show_scanned('$ins_data_id_tri'__1__'$tri_ins_type')\" src=\"".$firstScanImage."\" $scan_img_wid>";
	}
}
$ter_scan_card1 = $scan_img_src;
	
//--- SECOND SCAN DOCUMENTS -----
$scan2_img_wid = '';
$scan2_img_src = '';
if(trim($getTertiaryInsCoRow['scan_card2']) != ''){
	$secondScanImage = '../main/uploaddir'.$getTertiaryInsCoRow['scan_card2'];
	if(realpath($secondScanImage) != ''){
		$scan2_img_wid = newImageResize($secondScanImage,20,20);
		$scan2_img_src = "<img style=\"cursor:pointer\" onClick=\"show_scanned('$ins_data_id_tri'__2__'$tri_ins_type')\" src=\"".$secondScanImage."\" $scan2_img_wid>";
	}
}
$ter_scan_card2 = $scan2_img_src;
		
//------ Get Auth number Secondary ------------
if($auth_req_tri=='Yes'){
	$selQry_auth2 = imw_query("select auth_name,a_id,AuthAmount from patient_auth where patient_id='$patient_id' and ins_case_id='$ins' and ins_data_id='$ins_data_id_tri' and ins_type='3'
								and no_of_reffs>0 and (end_date='0000-00-00' or end_date >= '$dos_date') and auth_date <= '$dos_date' order by a_id desc");
	while($row_auth2 = imw_fetch_array($selQry_auth2)){
		$auth_no_tri[] = $row_auth2['auth_name'];
		$auth_amount_tri[] = $row_auth2['AuthAmount'];
		$auth_id_tri[] = $row_auth2['a_id'];
		$auth_tri_arr[]=array($row_auth2['auth_name'],$xyz, $row_auth2['auth_name']);
	}
}	
	
if(count($auth_no_pri)>0){
	$auth_no=$auth_no_pri[0];
	$auth_id=$auth_id_pri[0];
	$auth_amount=$auth_amount_pri[0];
	$auth_arr=$auth_arr;
}else if(count($auth_no_sec)>0){
	$auth_no=$auth_no_sec[0];
	$auth_id=$auth_id_sec[0];
	$auth_amount=$auth_amount_sec[0];
	$auth_arr=$auth_sec_arr;
}else if(count($auth_no_tri)>0){
	$auth_no=$auth_no_tri[0];
	$auth_id=$auth_id_tri[0];
	$auth_amount=$auth_amount_tri[0];
	$auth_arr=$auth_tri_arr;
}
$auth_drop_val_chk=get_simple_menu($auth_arr,"auth_no_menu","auth_no");
$auth_drop_val=str_replace(',','__',$auth_drop_val_chk);
//------ Get Auth number Secondary ------------
		
// TERTIARY

$referral_arr=array();
if($referal_req_pri=='Yes' || $referal_req_sec=='Yes' || $referal_req_ter=='Yes'){
	$reff_qry=imw_query("select reffral_no,reff_type from patient_reff where insCaseid='$ins' 
	and patient_id='$patient_id' and reff_phy_id='$reffPhyId'
	and (end_date='0000-00-00' or end_date >= '$dos_date') and effective_date <= '$dos_date'
	and no_of_reffs > '0' and del_status='0' order by end_date desc,reff_id desc");
	while($reff_row=imw_fetch_array($reff_qry)){
		if($referal_req_pri=='Yes' && $reff_row['reff_type']=='1'){
			$referral_arr[]=array($reff_row['reffral_no'],$xyz, $reff_row['reffral_no']);
			if($refNo1==""){
				$refNo1=$reff_row['reffral_no'];
			}
		}
		if($referal_req_sec=='Yes' && $reff_row['reff_type']=='2'){
			$referral_arr[]=array($reff_row['reffral_no'],$xyz, $reff_row['reffral_no']);
			if($refNo1==""){
				$refNo1=$reff_row['reffral_no'];
			}
		}
		if($referal_req_ter=='Yes' && $reff_row['reff_type']=='3'){
			$referral_arr[]=array($reff_row['reffral_no'],$xyz, $reff_row['reffral_no']);
			if($refNo1==""){
				$refNo1=$reff_row['reffral_no'];
			}
		}
	}
}
$reff_drop_val_chk=get_simple_menu($referral_arr,"referral_menu","referral");
$reff_drop_val=str_replace(',','__',$reff_drop_val_chk);

$getPrimaryPhysicianStr = "SELECT * FROM chart_master_table WHERE patient_id='$patient_id'";
$getPrimaryPhysicianQry = imw_query($getPrimaryPhysicianStr);
$getPrimaryPhysicianRow = imw_fetch_array($getPrimaryPhysicianQry);
	$providerId = $getPrimaryPhysicianRow['providerId'];
	$getNameStr = "SELECT * FROM users WHERE id='$providerId' AND user_type = '1'";
	$getNameQry = imw_query($getNameStr);
	$getNameRow = imw_fetch_array($getNameQry);
	$fname = $getNameRow['fname'];
	$lname = $getNameRow['lname'];
	$phyName = $fname." ".$lname;
	
if($expiration_date == '00-00-0000'){
	$expiration_date = '';
}

$ajax_arr=array();
$ajax_arr['effective_date']=$effective_date;
$ajax_arr['expiration_date']=$expiration_date;
$ajax_arr['primaryInsCoName']=$primaryInsCoName;
$ajax_arr['primaryInsId']=$primaryInsId;
$ajax_arr['secondaryInsCoName']=$secondaryInsCoName;
$ajax_arr['secondaryInsId']=$secondaryInsId;
$ajax_arr['tertiaryInsCoName']=$tertiaryInsCoName;
$ajax_arr['tertiaryInsId']=$tertiaryInsId;
$ajax_arr['copay']=$copay;
$ajax_arr['secCopayAmt']=$secCopayAmt;
$ajax_arr['refNo1']=$refNo1;
$ajax_arr['auth_id']=$auth_id;
$ajax_arr['auth_no']=$auth_no;
$ajax_arr['auth_amount']=$auth_amount;
$ajax_arr['pri_scan_card1']=$pri_scan_card1;
$ajax_arr['pri_scan_card2']=$pri_scan_card2;
$ajax_arr['sec_scan_card1']=$sec_scan_card1;
$ajax_arr['sec_scan_card2']=$sec_scan_card2;
$ajax_arr['ter_scan_card1']=$ter_scan_card1;
$ajax_arr['ter_scan_card2']=$ter_scan_card2;
$ajax_arr['auth_drop_val']=$auth_drop_val;
$ajax_arr['reff_drop_val']=$reff_drop_val;
$ajax_arr['refNo2']=$refNo2;
$ajax_arr['reffName']=$reffName;
$ajax_arr['reffPhyId']=$reffPhyId;
$ajax_arr['primary_institutional_type']=$primary_institutional_type;
echo json_encode($ajax_arr);
?>
