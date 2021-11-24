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
include_once('../../../config/globals.php');
$updir=substr(data_path(), 0, -1);
$srcDir = substr(data_path(1), 0, -1);
include_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_schedule_functions.php');
include_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_page_functions.php');
include_once($GLOBALS['fileroot'].'/library/classes/common_function.php');
include_once($GLOBALS['fileroot'].'/library/classes/cls_common_function.php');
include_once($GLOBALS['fileroot'].'/library/classes/class.language.php');  
include_once($GLOBALS['fileroot'].'/library/classes/demographics.class.php'); 

$demo = new Demographics(); 
$obj_scheduler = new appt_scheduler();
$other_language = $other_language_val = '';
$intClientWindowH = $_SESSION['wn_height'] - 140;
extract($_REQUEST);
/** Functions **/
	function get_reffering_physician_id($referrPhyName){
		$arr_title=array("Dr"=>"Dr.","DR"=>"Dr.","Dr."=>"Dr.","Dr.."=>"Dr.","Dr. 602"=>"Dr.","Dr.l"=>"Dr.","D.O."=>"DO","M.D"=>"MD","M.D."=>"MD","MD"=>"MD","Mr."=>"Mr.","Ms."=>"Ms","OD"=>"OD","DO"=>"DO","DR"=>"DR");
		$Reffer_physician_arr = preg_split('/,/',$referrPhyName);
		$phyLnameArr = preg_split('/,/',$Reffer_physician_arr[0]);
		$phylname = trim(end($phyLnameArr));
		$phyfname = trim($Reffer_physician_arr[1]);
		
		$arrPhyfname = array();
		$arrPhyfname = explode(' ',$phyfname);
		$phyfname = $arrPhyfname[0];
		if($phylname){
			$title_name_arr=array();
			$title_name_arr=explode(" ",trim($phylname));
			$title_val="";
			$title_val=$title_name_arr[0];
			if($arr_title[$title_val]){array_shift($title_name_arr);$phylname=implode(" ",$title_name_arr);}
		}
		
		$sql_qry = imw_query("select physician_Reffer_id from refferphysician where LastName = '$phylname' and FirstName = '$phyfname'");
		$refQryRes = imw_fetch_array($sql_qry);
		return $refQryRes['physician_Reffer_id'];
	}

	function check_consent_tab_color(){
		$patient_id = $_SESSION['patient'];
		$year = date('Y');
		$color = 'white';
		$arr_yr_templates = array();
		$arr_all_signed = array();
		
		$query_getAllYearlyTemplate = "SELECT consent_form_id FROM consent_form WHERE consent_form_status = 'Active' AND yearly_review = 1";
		$result_getAllYearlyTemplate = imw_query($query_getAllYearlyTemplate);
		if($result_getAllYearlyTemplate && imw_num_rows($result_getAllYearlyTemplate)>0){
			while($rs_getAllYearlyTemplate = imw_fetch_array($result_getAllYearlyTemplate)){
				$arr_yr_templates[] = $rs_getAllYearlyTemplate['consent_form_id'];
			}
		}
		
		$query = "SELECT pcfi.form_information_id, pcfi.consent_form_id, cf.yearly_review, DATE_FORMAT(pcfi.form_created_date,'%Y') as cr_year 
				  FROM patient_consent_form_information pcfi 
				  JOIN consent_form cf ON(cf.consent_form_id = pcfi.consent_form_id) 
				  WHERE patient_id='$patient_id' 
				  AND movedToTrash = 0 
				  AND DATE_FORMAT(pcfi.form_created_date,'%Y') = '$year'
				  ORDER BY pcfi.form_created_date DESC";
		$result = imw_query($query);
		if($result && imw_num_rows($result)>0){
			while($rs = imw_fetch_array($result)){
				$arr_all_signed[] = $rs['consent_form_id'];
			}
		}
		
		/*--setting color--*/
		if(count($arr_all_signed)==0){
			$color= 'white';
		}else if(count($arr_yr_templates) > 0){
			$arr3 = array_intersect($arr_yr_templates,$arr_all_signed);
			if(count($arr3)==count($arr_yr_templates)){
				$color= 'green';
			}else{
				$color= 'orange';
			}
		}
		/*--color setting end--*/
		return $color;
	}
	
	function mysqlifetchdata($query){
		$return = array();
		if($query != ''){
			$result = imw_query($query);
			if($result){
				if(imw_num_rows($result) > 0){
					if(count($result) > 0){
						while($row = imw_fetch_assoc($result)){
							foreach($row as $key => $val){
								$return[$key] = $val;
							}
							$returnResult[] = $return;
						} 
					}
				}
			}
		}
		return $returnResult;		
	}
	
	function get_real_ip_addr(){
		if (!empty($_SERVER['HTTP_CLIENT_IP'])){   //check ip from share internet
			$ip=$_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){   //to check ip is pass from proxy
			$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else{
		  $ip=$_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
	
	function chk_sec_copay_collect($pri_ins_id){
		$policyQryRes = mysqlifetchdata("SELECT * FROM copay_policies WHERE policies_id='1'");
		
		$sec_copay_for_ins = $policyQryRes[0]['sec_copay_for_ins'];
		$secondary_copay = $policyQryRes[0]['secondary_copay'];
		if($secondary_copay=='Yes' && $sec_copay_for_ins==''){
			$sec_copay_collect="Yes";
		}else if($sec_copay_for_ins=='Medicare as Primary'){
			$qryId = imw_query("SELECT id FROM insurance_companies WHERE 
					(in_house_code = 'medicare' or name like '%medicare%')
					and id='$pri_ins_id'");
			if(imw_num_rows($qryId)>0){
				$sec_copay_collect="Yes";
			}
		}
		return $sec_copay_collect;
	}
	
	function get_contract_fee($proc,$pri_ins,$reports=''){
		$contract_price="";
		$qryRes = mysqlifetchdata("select billing_amount from copay_policies");
		if($qryRes[0]['billing_amount']=='Default'){
			$contract_price=0;
			if($pri_ins>0){
				$qry_feeRes = mysqlifetchdata("select FeeTable from insurance_companies where id = '$pri_ins'");
				
				$FeeTable = (int)$qry_feeRes[0]['FeeTable'];
				if($FeeTable == 0 and empty($reports) === false){
					$FeeTable = 1;
				}
				if($FeeTable>0){
					$qry_feeRes1 = mysqlifetchdata("select cpt_fee_table.cpt_fee from cpt_fee_tbl
						join cpt_fee_table on cpt_fee_table.fee_table_column_id = '$FeeTable'
						where (cpt_fee_tbl.cpt_prac_code='$proc' OR 
						cpt_fee_tbl.cpt4_code='$proc' OR cpt_fee_tbl.cpt_desc='$proc')
						and cpt_fee_table.cpt_fee_id = cpt_fee_tbl.cpt_fee_id and cpt_fee_tbl.delete_status = '0'");
					$contract_price = $qry_feeRes1[0]['cpt_fee'];
				}
			}	
		}
		return $contract_price;
	}
	
	function get_header_about_us($ptHeard = ''){
		$ptHeard = trim($ptHeard);
		$query_part = " AND for_all=1";
		if($ptHeard != ''){
			$query_part = " AND (for_all=1 OR heard_id='".$ptHeard."')";
		}
		$sql ="SELECT DISTINCT heard_options,heard_id  FROM heard_about_us WHERE status='0'".$query_part." ORDER BY heard_options ASC";

		return imw_query($sql);
	}
	
	function get_demo_heard($val){
		$sql ="SELECT distinct heard_desc FROM heard_about_us_desc where heard_id = '$val'";

		return imw_query($sql);
	}	
 	
	function secondary_rte_icon($patient_id,$sec_ins_id){
		$sch_id = $_REQUEST['sch_id'];
		global $intClientWindowH;
		global $objCoreLang;
		$q1 = "SELECT sa_app_start_date from schedule_appointments WHERE id = '".$sch_id."' LIMIT 1";
		$res1 = imw_query($q1);
		$strAppDate = '';
		if($res1 && imw_num_rows($res1) == 1){
			$rs1 = imw_fetch_assoc($res1);
			$strAppDate = $rs1['sa_app_start_date'];
		
		$vsStatus = $vsTran = "";
		$vsToolTip = $vsStatusDate = $strEBResponce = $imgRealTimeEli = "";

		$q = "SELECT rtme.id as sec_rte_id,DATE_FORMAT(rtme.responce_date_time, '%m-%d-%Y %I:%i %p') as vs270RespDate, DATE_FORMAT(rtme.responce_date_time, '%m-%d-%Y') as vsRespDate, ";
		$q.= "DATE_FORMAT(rtme.responce_date_time, '%Y-%m-%d') as respDate, rtme.transection_error as vsTransectionError, rtme.EB_responce as vsEBLoopResp, ";
		$q.= "CONCAT_WS('',SUBSTRING(us.fname,1,1),SUBSTRING(us.mname,1,1), SUBSTRING(us.lname,1,1)) as elOpName,  rtme.responce_pat_policy_no as policy, ";
		$q.= "insComp.name as insCompName ";
		$q.= "FROM real_time_medicare_eligibility rtme LEFT JOIN users us on us.id = rtme.request_operator ";
		$q.= "LEFT JOIN insurance_data insData ON insData.id = rtme.ins_data_id ";
		$q.= "LEFT JOIN insurance_companies insComp ON insComp.id = insData.provider ";
		$q.= "WHERE rtme.patient_id = '".$patient_id."' AND rtme.ins_data_id='".$sec_ins_id."' ORDER BY rtme.id DESC LIMIT 0,1";
		$res = imw_query($q);
		if($res && imw_num_rows($res)>0){
		//	$rowGetRealTimeData = imw_fetch_object($rsGetRealTimeData);
			$rs = imw_fetch_assoc($res);
			$sec_rte_id = $rs['sec_rte_id'];
			$vsToolTip = "";
			if(($rs['vs270RespDate'] != "00-00-0000") && ($rs['vs270RespDate'] != "")){
				$vsToolTip .= "Date: ".$rs['vs270RespDate'];
			}else{
				$vsToolTip .= "Date: N/A";
			}
			$vsToolTip .= " \t \t";
			
			if($rs['elOpName'] != ""){
				$vsToolTip .= "Opr: ".$rs['elOpName'];
			}else{
				$vsToolTip .= "Opr: N/A";
			}
			$vsToolTip .= "\n";
			
			if($rs['insCompName'] != ""){
				$dbInsCompName = $rs['insCompName'];
			}else{
				$dbInsCompName = "N/A";
			}
			
			$dbInsCompName .= " \n";
			$vsToolTip .= $dbInsCompName;
			
			if($rs['policy'] != ""){
				$vsToolTip .= "Policy # ".$rs['policy'];
			}else{
				$vsToolTip .= "Policy # "."N/A";
			}
			$vsToolTip .= " \n";
			
			if(($rs['vs270RespDate'] != "00-00-0000") && ($rs['vs270RespDate'] != "")){
				$vsStatusDate = "Last Check Dt.:".$rs['vs270RespDate'];
			}
												
			if($rs['vsTransectionError'] != ""){
				$vsStatus = "Error: ".$vsStatusDate;
				$strBy = "BY: ".$rs['elOpName'];
				$vsTran = "error";
				$imgRealTimeEli = "<a id=\"anchorEligibility\" href=\"javascript:void(0);\" class=\"text_10b_purpule\" title=\"$vsToolTip\" onclick=\"getRealTimeEligibilityCI('".$sec_ins_id."','1', '".$GLOBALS['webroot']."', '".$sch_id."', '".$strAppDate."', '".$intClientWindowH."');\">";
				$imgRealTimeEli .= "<img id=\"imgEligibility\" src=\"../../../library/images/eligibility_red.png\" border=\"0\"/>";
				$imgRealTimeEli .= "</a>&nbsp;";
			}
			elseif($rs['vsEBLoopResp'] != ""){									
				$strEBResponce = $objCoreLang->get_vocabulary("vision_share_271", "EB", (string)trim($rs['vsEBLoopResp']));
				$vsStatus = $strEBResponce;
				$strBy = "BY: ".$rs['elOpName'];
				$vsTran = "sucss";
				if(($rs['vsEBLoopResp'] == "6") || ($rs['vsEBLoopResp'] == "7") || ($rs['vsEBLoopResp'] == "8") || ($rs['vsEBLoopResp'] == "V")){
					$vsTran = "error";
					$imgRealTimeEli = "<a id=\"anchorEligibility\" href=\"javascript:void(0);\" class=\"text_10b_purpule\" title=\"$vsToolTip\" onclick=\"getRealTimeEligibilityCI('".$sec_ins_id."','1', '".$GLOBALS['webroot']."', '".$sch_id."', '".$strAppDate."', '".$intClientWindowH."');\">";
					$imgRealTimeEli .= "<img id=\"imgEligibility\" src=\"../../../library/images/eligibility_red.png\" border=\"0\"/>";
					$imgRealTimeEli .= "</a>&nbsp;";
				}
				else{
					$imgRealTimeEli = "<a id=\"anchorEligibility\" href=\"javascript:void(0);\" class=\"text_10b_purpule\" title=\"$vsToolTip\" onclick=\"getRealTimeEligibilityCI('".$sec_ins_id."','1', '".$GLOBALS['webroot']."', '".$sch_id."', '".$strAppDate."', '".$intClientWindowH."');\">";
					$imgRealTimeEli .= "<img id=\"imgEligibility\" src=\"../../../library/images/eligibility_green.png\" border=\"0\"/>";
					$imgRealTimeEli .= "</a>&nbsp;";
				}
			}
		}else{
			$imgRealTimeEli = "<a id=\"anchorEligibility\" href=\"javascript:void(0);\" class=\"text_10b_purpule\" title=\"$vsToolTip\" onclick=\"getRealTimeEligibilityCI('".$sec_ins_id."','1', '".$GLOBALS['webroot']."', '".$sch_id."', '".$strAppDate."', '".$intClientWindowH."');\">";
			$imgRealTimeEli .= "<img id=\"imgEligibility\" src=\"../../../library/images/eligibility_blue.png\" border=\"0\" title=\"Realtime Medicare Eligibility Request\" />";
			$imgRealTimeEli .= "</a>&nbsp;";			
		}
		$streElTable = "<table style=\"border:0px; border-spacing:1px; width:100%; padding:0px; font-size:13px; margin-top:-4px;\"><tr><td>".$vsStatus."&nbsp;</td><td>".$vsStatusDate."</td></tr></table>";
		}
		return array($imgRealTimeEli,$sec_rte_id,$streElTable);
	}//end of function secondary_rte_icon()
	
	
/****/
if(isset($_GET['accounting_auth'])){
	$auth_number=$_REQUEST['auth_number'];
	$ins_case_id=$_REQUEST['ins_case_id'];
	$row=imw_query("select a_id,AuthAmount,date_format(cur_date,'%m-%d-%Y') as cur_date from patient_auth 
								where 
					auth_name = '$auth_number' and ins_case_id='$ins_case_id'");
			$fet=imw_fetch_array($row);
			$amount_auth = $fet['AuthAmount'];
			$auth_id= $fet['a_id'];
			if($fet['cur_date'] == '00-00-0000'){
				$fet['cur_date'] = '';
			}
			$cur_date= $fet['cur_date'];

	echo $getAuthData=$amount_auth."~~~".$auth_id."~~~".$cur_date;
	exit();
}

$_SESSION['temp_check_in_qsting'] = urlencode($_SERVER['QUERY_STRING']);
$tab_color = 'tab_'.check_consent_tab_color();
$bool_save_payments = true;
if(isset($_REQUEST['sch_id'])){
	$_REQUEST['sch_id'] = (int)$_REQUEST['sch_id'];
}
if(isset($_POST['sch_id'])){
	$_POST['sch_id'] = (int)$_POST['sch_id'];
}	
if(!core_check_privilege(array('priv_edit_financials'))){
	$prev_payment_qry = "SELECT payment_id FROM check_in_out_payment WHERE del_status=0 AND payment_type='checkin' AND sch_id='".$_REQUEST['sch_id']."' AND patient_id='".$_REQUEST['ci_pid']."' AND total_payment>0";
	$prev_payment_res = imw_query($prev_payment_qry);
	if($prev_payment_res && imw_num_rows($prev_payment_res)>0){
		$bool_save_payments = false;
	}		
}

$pat_val = 'Check In';
if(!$btn_submit){
	clean_patient_session();

	$_SESSION['patient'] = trim($_REQUEST['ci_pid']);
	$_SESSION['patient_ins_id_pri'] = $_REQUEST['patInsIdPri'];
	$_SESSION['patient_ins_id_sec'] = $_REQUEST['patInsIdSec'];
	$_SESSION['patient_ins_type']=$_REQUEST['currInsType'];
	$_SESSION['currentCaseid']=$_REQUEST['chg_current_caseid'];
	$pat_val = "Check In";
	if(empty($_SESSION['patient']) === true || $_REQUEST['sch_id']==''){
		$pat_val = "New";
		if($source == "demographics"){
			$closeDemoScript =  "window.opener.top.clean_patient_session();";
		}
	}
}

$objCoreLang = new core_lang;
$operator_id = $_SESSION['authId'];
$OBJCommonFunction = new CLSCommonFunction;

$defaults	=	array();
$defaults['emerg_relats']		=	get_relationship_array('emergency_relation');
$defaults['relations']			=	array('','self','spouse','child','POA','other');
//$defaults['marital_status']	=	array('','divorced','domestic partner','married','single','separated','widowed');
$defaults['marital_status']	=	marital_status();
$defaults['vocabulary']			=	$objCoreLang->get_vocabulary("patient_info", "demographics");
$vocabulary = $defaults['vocabulary'];
/* Reset Keys  */
if( isset($vocabulary['sex']) ) { $vocabulary['selGender'] = $vocabulary['sex'] ; unset($vocabulary['sex']); }
if( isset($vocabulary['status']) ) { $vocabulary['pat_marital_status'] = $vocabulary['status'] ; unset($vocabulary['status']); }
if( isset($vocabulary['ss']) ) { $vocabulary['ssnNumber'] = $vocabulary['ss'] ; unset($vocabulary['ss']); }
if( isset($vocabulary['email']) ) { $vocabulary['pat_email'] = $vocabulary['email'] ; unset($vocabulary['email']); }
if( isset($vocabulary['primaryCarePhy']) ) { $vocabulary['ref_phy_name'] = $vocabulary['primaryCarePhy']; $vocabulary['ref_phy_name'] = $vocabulary['primaryCarePhy']; unset($vocabulary['primaryCarePhy']); }
/*************** */
//Mandatory fields
$mandatory_fields_array = $mandatory_tmp_arr = array();
$advisory_fields_array = array();
/*$mandatory_fld_sql = imw_query("select * from demographics_mandatory");
while($row = imw_fetch_assoc($mandatory_fld_sql)){
	$mandatory_tmp_arr[] = $row;
}*/
$mandatory_tmp_arr = get_mandatory_fields('demographics');
foreach($mandatory_tmp_arr as $mandatory_field_name => $mandatory_field_val){
	//foreach($val as $mandatory_field_name => $mandatory_field_val){
	$extraField = '';
	if( $mandatory_field_name == 'sex') $mandatory_field_name = 'selGender';
	else if( $mandatory_field_name == 'status') $mandatory_field_name = 'pat_marital_status';
	else if( $mandatory_field_name == 'ss') $mandatory_field_name = 'ssnNumber';
	else if( $mandatory_field_name == 'email') $mandatory_field_name = 'pat_email';
	elseif($mandatory_field_name == 'primaryCarePhy') { $mandatory_field_name = 'ref_phy_name'; $extraField = 'primary_care_name'; }
	
		if($mandatory_field_val == 2){
			$mandatory_fields_array[] = $mandatory_field_name;
			if( $extraField ) $mandatory_fields_array[]= $extraField;
		}
		if($mandatory_field_val == 1){
			$advisory_fields_array[] = $mandatory_field_name;
			if( $extraField ) $advisory_fields_array[]= $extraField;
		}
	//}
}
//Bug Fix IM-7062:-unable to save new patients in schedule, missing dr signatures
$resp_mandatory_arr = array('fname1','lname1','relation1','status1','dob1','sex1','ss1','dlicence1','street1','rcode','rcity','rstate','phone_home1','phone_biz1','phone_cell1');

if( count($mandatory_fields_array) == 0 ){
	$mandatory_fields_array[] = 'fname';
	$mandatory_fields_array[] = 'lname';
}
$operatorDetails = mysqlifetchdata("select * from users where id='$operator_id'");
$operatorName = substr($operatorDetails[0]['fname'],0,1).substr($operatorDetails[0]['lname'],0,1);
$intRTEValidDays = 0;
if($_REQUEST["hidSaveInsSwap"] == "1"){
	$swapInsPid = $_POST['edit_patient_id'];
	$arrInsType = array('Primary','Secondary');
	for($i=0;$i<count($compId);$i++){
		$id = $compId[$i];
		if(empty($id) == false){
			$id = $compId[$i];			
			$arrNameData = array();
			$strInsType = "";
			$insDataId = 0;
			$arrNameData = explode("__",$_REQUEST['name_'.$arrInsType[$i]]);
			$strInsType = $arrNameData[0];
			$insDataId = $arrNameData[1];
			if((empty($strInsType) == false) && (empty($insDataId) == false)){
				$qryGetInsData = "select type,provider,referal_required,auth_required,ins_caseid from insurance_data where id = '".$insDataId."'";
				$rsGetInsData = imw_query($qryGetInsData);
				$rowGetInsData = imw_fetch_array($rsGetInsData);
				$qry = "update insurance_data set type = '".$strInsType."' where id = '".$insDataId."'";
				imw_query($qry);
				switch($strInsType){
					case 'primary':
						$new_reff_type = 1;
					break;
					case 'secondary':
						$new_reff_type = 2;
					break;
					
				}	
				if(imw_num_rows($rsGetInsData) > 0){
					$type = $rowGetInsData['type'];
					$provider = $rowGetInsData['provider'];
					$ins_caseid = $rowGetInsData['ins_caseid'];
					switch($type){
						case 'primary':
							$old_reff_type = 1;
						break;
						case 'secondary':
							$old_reff_type = 2;
						break;
					}
					//--- REFERRAL PROVIDER SWITCH ------
					if(strtolower($rowGetInsData['referal_required']) == 'yes'){	
						$qryGetPatRef = "select reff_id from patient_reff
								where ins_data_id = '".$insDataId."'
								and ins_provider = '".$provider."' and patient_id = '".$swapInsPid."'
								and reff_type = '".$old_reff_type."' order by reff_id desc limit 0,1";
						$rsGetPatRef = imw_query($qryGetPatRef);
						$rowGetPatRef = imw_fetch_array($rsGetPatRef);
						$update_ref_id = $rowGetPatRef['reff_id'];
						if($update_ref_id){
							//--- UPDATE PATIENT REFERRAL ---
							$qryUpdatePatRef = "update patient_reff set reff_type = '".$new_reff_type."' where reff_id = '".$update_ref_id."'";
							imw_query($qryUpdatePatRef);
							
							//--- UPDATE REFERRAL SCAN DATA ----
							$scan_data = $strInsType."_reff";
							$qryUpdatePatRefScan = "update upload_lab_rad_data set scan_from  = '".$scan_data."' where uplaod_primary_id = '".$update_ref_id."'";
							imw_query($qryUpdatePatRefScan);
						}
					}
					
					//--- AUTH REQUIRED SWITCH ------
					if(strtolower($rowGetInsData['auth_required']) == 'yes'){
						$qryUpdatepatAuth = "update patient_auth set ins_type = '".$new_reff_type."' where patient_id = '".$swapInsPid."' and ins_case_id = '".$ins_caseid."'";
						imw_query($qryUpdatepatAuth);
					}
				}
			}
		}
	}
}

function patient_audit_popup() {
		$opreaterId = $_SESSION['authId'];			
		$ip = getRealIpAddr();
		$URL = $_SERVER['PHP_SELF'];													 
		$os = getOS();
		$browserInfoArr = array();
		$browserInfoArr = _browser();
		$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
		$browserName = str_replace(";","",$browserInfo);													 
		$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);	
		
		global $patientDataArr,$patientDataFields,$restrictedProvidersDataFields,$edit_patient_id,$restricted_Row,$forSelTypeAhed,$pri_ins_data_arr,$sec_ins_data_arr;
		global $current_caseid,$ins_case_type_id,$insurance_primary_id,$pri_reff_id,$pri_ref_req_arr,$auth_pri_chk,$insurance_secondary_id,$sec_reff_id,$sec_ref_req_arr,$auth_sec_chk;		
		$pid = $edit_patient_id;
		$rowGetPatientData = $patientDataArr;
		$rowGetPatientData["ptID"] = $pid;
		$arrAuditTrail = $arrAuditTrailInsCase = $arrAuditTrailPri = $arrAuditTrailSec = $arrAuditTrailPriRef = $arrAuditTrailSecRef = $arrAuditTrailPriAuth = $arrAuditTrailSecAuth = array();
		$action="add";
		$arrAuditTrail [] = 
						array(
								"Pk_Id"=> $rowGetPatientData["ptID"],
								"Table_Name"=>"patient_data",
								"Data_Base_Field_Name"=> "patientStatus" ,
								"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"patientStatus") ,
								"Filed_Label"=> "elem_patientStatus",
								"Filed_Text"=> "Status",
								"Action"=> $action,
								"Operater_Id"=> $opreaterId,
								"Operater_Type"=> getOperaterType($opreaterId) ,
								"IP"=> $ip,
								"MAC_Address"=> $_REQUEST['macaddrs'],
								"URL"=> $URL,
								"Browser_Type"=> $browserName,
								"OS"=> $os,
								"Machine_Name"=> $machineName,
								"pid"=> $_SESSION['patient'],
								"Category"=> "patient_info",
								"Category_Desc"=> "demographics",	
								"New_Value"=> ($rowGetPatientData["ptPatientStatus"]) ? $rowGetPatientData["ptPatientStatus"] : ""																																										
							
							);
		$arrAuditTrail [] = 
						array(																								
								"Data_Base_Field_Name"=> "otherPatientStatus" ,
								"Filed_Text"=> "Status Text",
								"Filed_Label"=> "otherPatientStatus",
								"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"otherPatientStatus") ,
								"New_Value"=> addcslashes(addslashes($rowGetPatientData['ptOtherPatientStatus']),"\0..\37!@\177..\377")
								
							);																			
		$arrAuditTrail [] = 
						array(
								"Pk_Id"=> $rowGetPatientData["ptID"],
								"Table_Name"=>"patient_data",
								"Data_Base_Field_Name"=> "reportExemption" ,
								"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"reportExemption") ,
								"Filed_Label"=> "reportExemption",
								"Filed_Text"=> "Exempt from Reports",
								"Action"=> "add",
								"Operater_Id"=> $opreaterId,
								"Operater_Type"=> getOperaterType($opreaterId) ,
								"IP"=> $ip,
								"MAC_Address"=> $_REQUEST['macaddrs'],
								"URL"=> $URL,
								"Browser_Type"=> $browserName,
								"OS"=> $os,
								"Machine_Name"=> $machineName,
								"Category"=> "patient_info",
								"Category_Desc"=> "demographics",	
								"New_Value"=> ($rowGetPatientData["ptReportExemption"]) ? $rowGetPatientData["ptReportExemption"] : ""																																										
							);																		
							//echo '<pre>';
							//print_r($arrAuditTrail);die;
							if(empty($forSelTypeAhed) == true){
								$pt_heard_val="";
							}else{								
								$pt_heard_val = $forSelTypeAhed;
							}
		$arrAuditTrail [] = 
						array(																								
								"Data_Base_Field_Name"=> "heard_abt_us" ,
								"Filed_Label"=> "elem_heardAbtUs",
								"Filed_Text"=> "Heard about us",
								"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"heard_abt_us") ,
								"New_Value"=> addcslashes(addslashes($pt_heard_val),"\0..\37!@\177..\377")
								
							);
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "heard_abt_desc" ,
							"Filed_Label"=> "heardAbtDesc",
							"Filed_Text"=> "Heard about us Description",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"heard_abt_desc") ,
							"New_Value"=> addcslashes(addslashes($rowGetPatientData['ptHeardAbtDesc']),"\0..\37!@\177..\377")
							
						);
		$arrAuditTrail [] = 
					array(
							"Pk_Id"=> $rowGetPatientData["ptID"],
							"Table_Name"=>"patient_data",
							"Data_Base_Field_Name"=> "title" ,
							"Filed_Label"=> "title",
							"Filed_Text"=> "Patient Title",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"title") ,
							"New_Value"=> addcslashes(addslashes($rowGetPatientData['ptTitle']),"\0..\37!@\177..\377"),
																																																																													
						);
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "fname" ,
							"Filed_Label"=> "fname",
							"Filed_Text"=> "Patient First Name",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"fname") ,
							"New_Value"=> addcslashes(addslashes($rowGetPatientData['ptFname']),"\0..\37!@\177..\377")
							
						);	
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "mname" ,
							"Filed_Label"=> "mname",
							"Filed_Text"=> "Patient Middle Name",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"mname") ,
							"New_Value"=> addcslashes(addslashes($rowGetPatientData['ptMname']),"\0..\37!@\177..\377")
							
						);		
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "lname" ,
							"Filed_Label"=> "lname",
							"Filed_Text"=> "Patient Last Name",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"lname") ,
							"New_Value"=> addcslashes(addslashes($rowGetPatientData['ptLname']),"\0..\37!@\177..\377")
							
						);
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "suffix" ,
							"Filed_Label"=> "suffix",
							"Filed_Text"=> "Patient Suffix",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"suffix") ,
							"New_Value"=> addcslashes(addslashes($rowGetPatientData['ptSuffix']),"\0..\37!@\177..\377")
							
						);	
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "status" ,
							"Filed_Label"=> "status",
							"Filed_Text"=> "Patient Marital Status",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"status") ,
							"New_Value"=> addcslashes(addslashes($rowGetPatientData['status']),"\0..\37!@\177..\377")
							
						);		
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "sex" ,
							"Filed_Label"=> "sex",
							"Filed_Text"=> "Patient Gender",
							"Data_Base_Field_Type"=>  fun_get_field_type($patientDataFields,"sex") ,
							"New_Value"=> addcslashes(addslashes($rowGetPatientData['sex']),"\0..\37!@\177..\377")
							
						);	
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "ss" ,
							"Filed_Label"=> "ss",
							"Filed_Text"=> "Patient Social security",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"ss") ,
							"New_Value"=> addcslashes(addslashes($rowGetPatientData['ss']),"\0..\37!@\177..\377")
							
						);			
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "DOB" ,
							"Filed_Label"=> "pt_dob",
							"Filed_Text"=> "Patient DOB",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"DOB") ,
							"New_Value"=> (addcslashes(addslashes($_POST["DOB"]),"\0..\37!@\177..\377")!="0000-00-00") ? addcslashes(addslashes($_POST["DOB"]),"\0..\37!@\177..\377") : ""
							
						);		
		 $arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "street" ,
							"Filed_Label"=> "street",
							"Filed_Text"=> "Patient Address 1",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"street") ,
							"New_Value"=> addcslashes(addslashes($rowGetPatientData['street']),"\0..\37!@\177..\377")																								
						);			
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "street2" ,
							"Filed_Label"=> "street2",
							"Filed_Text"=> "Patient Address 2",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"street2") ,
							"New_Value"=> addcslashes(addslashes($rowGetPatientData['street2']),"\0..\37!@\177..\377")
							
						);		
		$zipPostal = (($GLOBALS['phone_country_code'] == '1') ? 'Zip Code' : 'Postal Code');
		$zipPostalExt = (($GLOBALS['phone_country_code'] == '1') ? 'Zip Ext' : 'Postal Ext');
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "postal_code" ,
							"Filed_Label"=> "code",							
							"Filed_Text"=> "Patient ".$zipPostal,
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"postal_code") ,
							"New_Value"=> addcslashes(addslashes($rowGetPatientData['postal_code']),"\0..\37!@\177..\377")																								
						);	
		
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "zip_ext" ,
							"Filed_Label"=> "zip_ext",							
							"Filed_Text"=> "Patient ".$zipPostalExt,
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"zip_ext") ,
							"New_Value"=> addcslashes(addslashes($rowGetPatientData['zip_ext']),"\0..\37!@\177..\377")																								
						);	
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "city" ,
							"Filed_Label"=> "city",
							"Filed_Text"=> "Patient City",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"city") ,
							"New_Value"=> addcslashes(addslashes($rowGetPatientData['city']),"\0..\37!@\177..\377")																								
						);	
		$stateLocality = ($GLOBALS['phone_country_code'] == '1') ? 'State' : 'Locality';				
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "state" ,
							"Filed_Label"=> "state",							
							"Filed_Text"=> "Patient ".$stateLocality,
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"state") ,
							"New_Value"=> addcslashes(addslashes($rowGetPatientData['state']),"\0..\37!@\177..\377")																								
						);
		
		
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "preferr_contact" ,
							"Filed_Label"=> "pf_contact",
							"Filed_Text"=> "Patient Contact Phone Selected",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"preferr_contact") ,
							"New_Value"=> addcslashes(addslashes(core_phone_format($rowGetPatientData['preferr_contact'])),"\0..\37!@\177..\377")
							
						);		

		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "phone_home" ,
							"Filed_Label"=> "phone_home",
							"Filed_Text"=> "Patient Home Phone#",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"phone_home") ,
							"New_Value"=> addcslashes(addslashes(core_phone_format($rowGetPatientData['phone_home'])),"\0..\37!@\177..\377")
							
						);		
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "phone_biz" ,
							"Filed_Label"=> "phone_biz",
							"Filed_Text"=> "Patient Work Phone#",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"phone_biz") ,
							"New_Value"=> addcslashes(addslashes(core_phone_format($rowGetPatientData['phone_biz'])),"\0..\37!@\177..\377")																								
						);
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "phone_cell" ,
							"Filed_Label"=> "phone_cell",
							"Filed_Text"=> "Patient Mobile Phone#",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"phone_cell") ,
							"New_Value"=> addcslashes(addslashes(core_phone_format($rowGetPatientData['phone_cell'])),"\0..\37!@\177..\377")
							
						);	
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "email" ,
							"Filed_Label"=> "email",
							"Filed_Text"=> "Patient Email-Id",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"email") ,
							"New_Value"=> addcslashes(addslashes($rowGetPatientData['email']),"\0..\37!@\177..\377")
							
						);
		$arrAuditTrail [] = 
					array(
							"Data_Base_Field_Name"=> "chk_mobile" ,
							"Filed_Label"=> "chk_mobile",
							"Filed_Text"=> "Checkbox email Mobile",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"chk_mobile") ,
							"New_Value"=> addcslashes(addslashes($rowGetPatientData['chk_mobile']),"\0..\37!@\177..\377")
							
						);		
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "contact_relationship" ,
							"Filed_Label"=> "contact_relationship",
							"Filed_Text"=> "Patient Emergency Name",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"contact_relationship") ,
							"New_Value"=> addcslashes(addslashes($rowGetPatientData['contact_relationship']),"\0..\37!@\177..\377")
							
						);		
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "emergencyRelationship" ,
							"Filed_Label"=> "emerRelation",
							"Filed_Text"=> "Patient Relationship",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"emergencyRelationship") ,
							"New_Value"=> addcslashes(addslashes($rowGetPatientData['emergencyRelationship']),"\0..\37!@\177..\377")
							
						);
		$arrAuditTrail [] = 
				array(																								
						"Data_Base_Field_Name"=> "emergencyRelationship_other" ,
						"Filed_Label"=> "relation_other_textbox",
						"Filed_Text"=> "Patient Relationship Other",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"emergencyRelationship_other") ,
						"New_Value"=> addcslashes(addslashes($rowGetPatientData['emergencyRelationship_other']),"\0..\37!@\177..\377")
					);
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "phone_contact" ,
							"Filed_Label"=> "phone_contact",
							"Filed_Text"=> "Patient Emergency Tel#",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"phone_contact") ,
							"New_Value"=> addcslashes(addslashes(core_phone_format($rowGetPatientData['phone_contact'])),"\0..\37!@\177..\377")																								
						);
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "providerID" ,
							"Filed_Label"=> "providerID",
							"Filed_Text"=> "Patient Physician",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"providerID") ,
							"Depend_Select"=> "select CONCAT_WS(', ',lname,fname) as provider" ,
							"Depend_Table"=> "users" ,
							"Depend_Search"=> "id" ,
							"New_Value"=> (addcslashes(addslashes($rowGetPatientData['providerID']),"\0..\37!@\177..\377")) ? addcslashes(addslashes($rowGetPatientData['ptProviderID']),"\0..\37!@\177..\377") : ""
							
						);
		$arrAuditTrail [] = 
						array(																								
							"Data_Base_Field_Name"=> "primary_care_phy_id" ,
							"Filed_Label"=> "pCarePhy",
							"Filed_Text"=> "Patient Physician",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"primary_care_phy_id") ,
							"Depend_Select"=> "select CONCAT_WS(', ',lname,fname) as provider" ,
							"Depend_Table"=> "users" ,
							"Depend_Search"=> "id" ,
							"New_Value"=> addcslashes(addslashes($rowGetPatientData['primary_care_phy_id']),"\0..\37!@\177..\377")							
						);
		$arrAuditTrail [] = 
						array(																								
							"Data_Base_Field_Name"=> "primary_care_id" ,
							"Filed_Label"=> "pcare",
							"Filed_Text"=> "Patient Referring Physician",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"primary_care_id") ,
							"Depend_Select"=> "select CONCAT_WS(', ',LastName,FirstName) as refPhy" ,
							"Depend_Table"=> "refferphysician" ,
							"Depend_Search"=> "physician_Reffer_id" ,
							"New_Value"=> addcslashes(addslashes($rowGetPatientData['primary_care_id']),"\0..\37!@\177..\377")							
						);
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "default_facility" ,
							"Filed_Label"=> "default_facility",
							"Filed_Text"=> "Patient Facility",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"default_facility") ,
							"Depend_Select"=> "SELECT facility_name as facilityName" ,
							"Depend_Table"=> "pos_facilityies_tbl" ,
							"Depend_Search"=> "pos_facility_id" ,
							"New_Value"=> addcslashes(addslashes($rowGetPatientData['default_facility']),"\0..\37!@\177..\377")							
						);
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "username" ,
							"Filed_Label"=> "usernm",
							"Filed_Text"=> "Patient Login-Id",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"username") ,
							"New_Value"=> addcslashes(addslashes($rowGetPatientData['username']),"\0..\37!@\177..\377")
							
						);
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "password" ,
							"Filed_Label"=> "pass1",
							"Filed_Text"=> "Patient Password",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"password") ,
							"New_Value"=> addcslashes(addslashes($rowGetPatientData['password']),"\0..\37!@\177..\377")																								
						);
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "password" ,
							"Filed_Label"=> "pass2",
							"Filed_Text"=> "Patient Confirm Password",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"password") ,
							"New_Value"=> addcslashes(addslashes($rowGetPatientData['password']),"\0..\37!@\177..\377")																								
						);
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "patient_notes" ,
							"Filed_Label"=> "patient_notes",
							"Filed_Text"=> "Patient Notes",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"patient_notes") ,
							"New_Value"=> addcslashes(addslashes($rowGetPatientData['patient_notes']),"\0..\37!@\177..\377")
						);
					
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "chk_notes_scheduler" ,
							"Filed_Label"=> "chkNotesScheduler",
							"Filed_Text"=> "Patient Checkbox Scheduler",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"chk_notes_scheduler") ,
							"New_Value"=> ($rowGetPatientData["chk_notes_scheduler"]) ? $rowGetPatientData["chk_notes_scheduler"] : "0"							
						);
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "chk_notes_chart_notes" ,
							"Filed_Label"=> "chkNotesChartNotes",
							"Filed_Text"=> "Patient Checkbox Chart Notes",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"chk_notes_chart_notes") ,							
							"New_Value"=> ($rowGetPatientData["chk_notes_chart_notes"]) ? $rowGetPatientData["chk_notes_chart_notes"] : "0"
						);
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "chk_notes_accounting" ,
							"Filed_Label"=> "chkNotesAccounting",
							"Filed_Text"=> "Patient Checkbox Accounting",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"chk_notes_accounting") ,
							"New_Value"=> ($rowGetPatientData["chk_notes_accounting"]) ? $rowGetPatientData["chk_notes_accounting"] : "0"							
						);			
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "chk_notes_optical" ,
							"Filed_Label"=> "chkNotesOptical",
							"Filed_Text"=> "Patient Checkbox Optical",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"chk_notes_optical") ,
							"New_Value"=> ($rowGetPatientData["chk_notes_optical"]) ? $rowGetPatientData["chk_notes_optical"] : "0"							
						);								
					
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "noBalanceBill" ,
							"Filed_Label"=> "noBalBill",
							"Filed_Text"=> "Patient No Balance Bill",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"noBalanceBill") ,
							"New_Value"=> ($rowGetPatientData["noBalanceBill"]) ? $rowGetPatientData["noBalanceBill"] : ""
						);
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "hold_statement" ,
							"Filed_Label"=> "pat_hs",
							"Filed_Text"=> "Patient Hold Statements",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"hold_statement") ,
							"New_Value"=> ($rowGetPatientData['hold_statement']) ? $rowGetPatientData["hold_statement"] : ""
						);

		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "EMR" ,
							"Filed_Label"=> "emr",
							"Filed_Text"=> "Patient Emr",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"EMR") ,
							"New_Value"=> ($rowGetPatientData['EMR']) ? $rowGetPatientData["EMR"] : ""
						);
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "created_by" ,
							"Filed_Label"=> "created_by",
							"Filed_Text"=> "Patient Created By",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"created_by") ,
							"Depend_Select"=> "select CONCAT_WS(', ',lname,fname) as createdBy" ,
							"Depend_Table"=> "users" ,
							"Depend_Search"=> "id" ,
							"New_Value"=> addcslashes(addslashes($rowGetPatientData['created_by']),"\0..\37!@\177..\377")
							
						);				
		$regDate = $rowGetPatientData['date'];
		$arrRegDate = explode(" ",$regDate);
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "date" ,
							"Filed_Label"=> "reg_date",
							"Filed_Text"=> "Patient Registration Date",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"date") ,
							"New_Value"=> addcslashes(addslashes($arrRegDate[0]),"\0..\37!@\177..\377")																								
						);		
		$arrAuditTrail [] = 
					array(																								
							"Data_Base_Field_Name"=> "driving_licence",
							"Filed_Label"=> "dlicence",
							"Filed_Text"=> "Patient Driving License",
							"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"driving_licence") ,
							"New_Value"=> $rowGetPatientData["driving_licence"]
						);
		
		// Advanced Directive
		
		// ----- Advanced Directive
		//Restrict Access
			
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "occupation",
						"Filed_Label"=> "occupation",
						"Filed_Text"=> "Patient Occupation",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"occupation") ,
						"New_Value"=> addcslashes(addslashes($rowGetPatientData["occupation"]),"\0..\37!@\177..\377")																													
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "monthly_income",
						"Filed_Label"=> "monthly_income",
						"Filed_Text"=> "Patient Monthly Income",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"monthly_income") ,
						"New_Value"=> addcslashes(addslashes($rowGetPatientData["monthly_income"]),"\0..\37!@\177..\377")																													
					);
		
		$arrAuditTrail [] = 																							
				array(
						"Pk_Id"=> $rowGetPatientData["ptID"],
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "hipaa_mail",
						"Filed_Label"=> "hipaa_mail",
						"Filed_Text"=> "Patient Allow postal Mail",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"hipaa_mail") ,
						"New_Value"=> addcslashes(addslashes($rowGetPatientData["hipaa_mail"]),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "hipaa_email",
						"Filed_Label"=> "hipaa_email",
						"Filed_Text"=> "Patient Allow eMail",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"hipaa_email") ,
						"New_Value"=> addcslashes(addslashes($rowGetPatientData["hipaa_email"]),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "hipaa_voice",
						"Filed_Label"=> "hipaa_voice",
						"Filed_Text"=> "Patient Voice Msg",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"hipaa_voice") ,
						"New_Value"=> addcslashes(addslashes($rowGetPatientData["hipaa_voice"]),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "hipaa_text",
						"Filed_Label"=> "hipaa_text",
						"Filed_Text"=> "Patient Text Msg",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"hipaa_text") ,
						"New_Value"=> addcslashes(addslashes($rowGetPatientData["hipaa_text"]),"\0..\37!@\177..\377")																							
					);			
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "genericval1",
						"Filed_Label"=> "genericval1",
						"Filed_Text"=> "Patient Miscellaneous User Defined 1",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"genericval1") ,
						"New_Value"=> addcslashes(addslashes($rowGetPatientData["genericval1"]),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "genericval2",
						"Filed_Label"=> "genericval2",
						"Filed_Text"=> "Patient Miscellaneous User Defined 2",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"genericval2") ,
						"New_Value"=> addcslashes(addslashes($rowGetPatientData["genericval2"]),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] = 																							
				array(
						"Pk_Id"=> $rowGetPatientData["ptID"],
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "relInfoName1",
						"Filed_Label"=> "relInfoName1",
						"Filed_Text"=> "Patient Release Information Name 1",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"relInfoName1") ,
						"New_Value"=> addcslashes(addslashes($rowGetPatientData["relInfoName1"]),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "relInfoPhone1",
						"Filed_Label"=> "relInfoPhone1",
						"Filed_Text"=> "Patient Release Information Phone# 1",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"relInfoPhone1") ,
						"New_Value"=> addcslashes(addslashes(core_phone_format($rowGetPatientData["relInfoPhone1"])),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "relInfoReletion1",
						"Filed_Label"=> "relInfoReletion1",
						"Filed_Text"=> "Patient Release Information Relationship 1",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"relInfoReletion1") ,
						"New_Value"=> addcslashes(addslashes($rowGetPatientData["relInfoReletion1"]),"\0..\37!@\177..\377")
						
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "otherRelInfoReletion1",
						"Filed_Label"=> "otherRelInfoReletion1",
						"Filed_Text"=> "Patient Release Information Relationship Other 1",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"otherRelInfoReletion1") ,
						"New_Value"=> addcslashes(addslashes($rowGetPatientData["otherRelInfoReletion1"]),"\0..\37!@\177..\377")																							
					);	
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "relInfoName2",
						"Filed_Label"=> "relInfoName2",
						"Filed_Text"=> "Patient Release Information Name 2",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"relInfoName2") ,
						"New_Value"=> addcslashes(addslashes($rowGetPatientData["relInfoName2"]),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "relInfoPhone2",
						"Filed_Label"=> "relInfoPhone2",
						"Filed_Text"=> "Patient Release Information Phone# 2",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"relInfoPhone2") ,
						"New_Value"=> addcslashes(addslashes(core_phone_format($rowGetPatientData["relInfoPhone2"])),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "relInfoReletion2",
						"Filed_Label"=> "relInfoReletion2",
						"Filed_Text"=> "Patient Release Information Relationship 2",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"relInfoReletion2") ,
						"New_Value"=> addcslashes(addslashes($rowGetPatientData["relInfoReletion2"]),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "otherRelInfoReletion2",
						"Filed_Label"=> "otherRelInfoReletion2",
						"Filed_Text"=> "Patient Release Information Relationship Other 2",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"otherRelInfoReletion2") ,
						"New_Value"=> addcslashes(addslashes($rowGetPatientData["otherRelInfoReletion2"]),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "relInfoName3",
						"Filed_Label"=> "relInfoName3",
						"Filed_Text"=> "Patient Release Information Name 3",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"relInfoName3") ,
						"New_Value"=> addcslashes(addslashes($rowGetPatientData["relInfoName3"]),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "relInfoPhone3",
						"Filed_Label"=> "relInfoPhone3",
						"Filed_Text"=> "Patient Release Information Phone# 3",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"relInfoPhone3") ,
						"New_Value"=> addcslashes(addslashes($rowGetPatientData["relInfoPhone3"]),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "relInfoReletion3",
						"Filed_Label"=> "relInfoReletion3",
						"Filed_Text"=> "Patient Release Information Relationship 3",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"relInfoReletion3") ,
						"New_Value"=> addcslashes(addslashes($rowGetPatientData["relInfoReletion3"]),"\0..\37!@\177..\377")
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "otherRelInfoReletion3",
						"Filed_Label"=> "otherRelInfoReletion3",
						"Filed_Text"=> "Patient Release Information Relationship Other 3",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"otherRelInfoReletion3") ,
						"New_Value"=> addcslashes(addslashes($rowGetPatientData["otherRelInfoReletion3"]),"\0..\37!@\177..\377")																							
					);			
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "relInfoName4",
						"Filed_Label"=> "relInfoName4",
						"Filed_Text"=> "Patient Release Information Name 4",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"relInfoName4") ,
						"New_Value"=> addcslashes(addslashes($rowGetPatientData["relInfoName4"]),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "relInfoPhone4",
						"Filed_Label"=> "relInfoPhone4",
						"Filed_Text"=> "Patient Release Information Phone# 4",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"relInfoPhone4") ,
						"New_Value"=> addcslashes(addslashes($rowGetPatientData["relInfoPhone4"]),"\0..\37!@\177..\377")
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "relInfoReletion4",
						"Filed_Label"=> "relInfoReletion4",
						"Filed_Text"=> "Patient Release Information Relationship 4",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"relInfoReletion4") ,
						"New_Value"=> addcslashes(addslashes($rowGetPatientData["relInfoReletion4"]),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "otherRelInfoReletion4",
						"Filed_Label"=> "otherRelInfoReletion4",
						"Filed_Text"=> "Patient Release Information Relationship Other 3",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"otherRelInfoReletion4") ,
						"New_Value"=> addcslashes(addslashes($rowGetPatientData["otherRelInfoReletion4"]),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "language",
						"Filed_Label"=> "language",
						"Filed_Text"=> "Patient Language",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"language") ,
						"New_Value"=> addcslashes(addslashes($rowGetPatientData["language"]),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "interpretter",
						"Filed_Label"=> "interpretter",
						"Filed_Text"=> "Patient Interpreter",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"interpretter") ,
						"New_Value"=> addcslashes(addslashes($rowGetPatientData["interpretter"]),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "race",
						"Filed_Label"=> "race",
						"Filed_Text"=> "Patient Race",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"race") ,
						"New_Value"=> addcslashes(addslashes($rowGetPatientData["race"]),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "otherRace",
						"Filed_Label"=> "otherRace",
						"Filed_Text"=> "Patient Other race",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"otherRace") ,
						"New_Value"=> addcslashes(addslashes($rowGetPatientData["otherRace"]),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] = 																							
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "ethnicity",
						"Filed_Label"=> "ethnicity",
						"Filed_Text"=> "Patient Ethnicity",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"ethnicity") ,
						"New_Value"=> addcslashes(addslashes($rowGetPatientData["ethnicity"]),"\0..\37!@\177..\377")																							
					);
		$arrAuditTrail [] =
				array(
						"Table_Name"=>"patient_data",
						"Data_Base_Field_Name"=> "otherEthnicity",
						"Filed_Label"=> "otherEthnicity",
						"Filed_Text"=> "Patient Other Ethnicity",
						"Data_Base_Field_Type"=> fun_get_field_type($patientDataFields,"otherEthnicity") ,
						"New_Value"=> addcslashes(addslashes($rowGetPatientData["otherEthnicity"]),"\0..\37!@\177..\377")																							
					);
		
		
		//START AUDIT INSURANCE INFO
		
		$arrAuditTrailInsCase [] = array(		
										"Pk_Id"=> $current_caseid,
										"Table_Name"=>"insurance_case",								
										"Data_Base_Field_Name"=> "ins_case_type" ,
										"Filed_Label"=> "choose_prevcase",
										"Filed_Text"=> "Patient Insurance Case Type",													
										"Data_Base_Field_Type"=> fun_get_field_type($insCaseDataFields,"ins_case_type"),				
										"Category"=> "patient_info",
										"Category_Desc"=> "insurence",	
										"Depend_Select"=> "select case_name as insCase" ,
										"Depend_Table"=> "insurance_case_types" ,
										"Depend_Search"=> "case_id" ,
										"New_Value"=> $ins_case_type_id			
														
									);
		
		$arrAuditTrailPri [] = array(																								
						"Ins_Type"=> "primary",	
						"Pk_Id"=> $insurance_primary_id,
						"Table_Name"=>"insurance_data",
						"Data_Base_Field_Name"=> "provider" ,
						"Filed_Text"=> "Patient Primary Insurance Ins. Provider",
						"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"provider") ,
						"Filed_Label"=> "insPriProv_id",																						
						"Category"=> "patient_info",
						"Category_Desc"=> "insurence",
						"Depend_Select"=> "select name as provider" ,
						"Depend_Table"=> "insurance_companies" ,
						"Depend_Search"=> "id" ,
						"New_Value"=> ($pri_ins_data_arr['provider']) ? trim($pri_ins_data_arr['provider']) : ""																																										
					);
		
		$arrAuditTrailPri [] = 
					array(																								
							"Ins_Type"=> "primary",
							"Data_Base_Field_Name"=> "self_pay_provider" ,
							"Filed_Label"=> "cbk_self_pay_provider",
							"Filed_Text"=> "Patient Insurance Checkbox Self Pay",
							"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"self_pay_provider") ,
							"New_Value"=> ($_POST["cbk_self_pay_provider"]) ? $_POST["cbk_self_pay_provider"] : "0"							
						);
		
		$arrAuditTrailPri [] = array(
									"Ins_Type"=> "primary",																
									"Data_Base_Field_Name"=> "policy_number" ,
									"Filed_Label"=> "insPriPolicy",
									"Filed_Text"=> "Patient Primary Insurance Policy",
									"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"policy_number") ,
									"New_Value"=> addcslashes(addslashes(trim($pri_ins_data_arr['policy_number'])),"\0..\37!@\177..\377")																					
								);
		$arrAuditTrailPri [] = array(
									"Ins_Type"=> "primary",																
									"Data_Base_Field_Name"=> "group_number" ,
									"Filed_Label"=> "insPriGroup",
									"Filed_Text"=> "Patient Primary Insurance Group Number",
									"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"group_number") ,
									"New_Value"=> addcslashes(addslashes(trim($pri_ins_data_arr['group_number'])),"\0..\37!@\177..\377")																					
								);
		
		$arrAuditTrailPri [] = array(
									"Ins_Type"=> "primary",																
									"Data_Base_Field_Name"=> "copay" ,
									"Filed_Label"=> "insPriCopay",
									"Filed_Text"=> "Patient Insurance Primary CoPay",
									"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"copay") ,
									"New_Value"=> addcslashes(addslashes(trim($pri_ins_data_arr['copay'])),"\0..\37!@\177..\377")																					
								);		
		$arrAuditTrailPri [] = array(
									"Ins_Type"=> "primary",																
									"Data_Base_Field_Name"=> "referal_required" ,
									"Filed_Label"=> "pri_ref_req",
									"Filed_Text"=> "Patient Insurance Primary Ref. Req",
									"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"referal_required") ,
									"New_Value"=> addcslashes(addslashes(trim($_POST['pri_ref_req'])),"\0..\37!@\177..\377")																					
								);		

		$arrAuditTrailPri [] = array(
									"Ins_Type"=> "primary",																
									"Data_Base_Field_Name"=> "auth_required" ,
									"Filed_Label"=> "pri_auth_req",
									"Filed_Text"=> "Patient Insurance Primary Auth. Req",
									"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"auth_required") ,
									"New_Value"=> addcslashes(addslashes(trim($_POST['pri_auth_req'])),"\0..\37!@\177..\377")																					
								);		


		$arrAuditTrailPri [] = array(
									"Ins_Type"=> "primary",																
									"Data_Base_Field_Name"=> "effective_date" ,
									"Filed_Label"=> "insPriActDt",
									"Filed_Text"=> "Patient Insurance Primary Effective Date",
									"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"effective_date") ,
									"New_Value"=> addcslashes(addslashes(trim($_POST['insPriActDt'])),"\0..\37!@\177..\377")																					
								);		
		$arrAuditTrailPri [] = array(
									"Ins_Type"=> "primary",																
									"Data_Base_Field_Name"=> "expiration_date" ,
									"Filed_Label"=> "insPriExpDt",
									"Filed_Text"=> "Patient Insurance Primary Expiration Date",
									"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"expiration_date") ,
									"New_Value"=> addcslashes(addslashes(trim($_POST['insPriExpDt'])),"\0..\37!@\177..\377")																					
								);		
		
		
		$arrAuditTrailPriRef [] = array(							
										"Ins_Type"=> "primary",			
										"Pk_Id"=> $pri_reff_id,
										"Table_Name"=>"patient_reff",															
										"Data_Base_Field_Name"=> "reff_phy_id" ,
										"Filed_Label"=> "pri_ref_phy_id",
										"Filed_Text"=> "Patient Primary Referral Ref. Physician",
										"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"reff_phy_id") ,																				
										"Category"=> "patient_info",
										"Category_Desc"=> "insurence",
										"Depend_Select"=> "select CONCAT_WS(', ',LastName,FirstName) as refPhy" ,
										"Depend_Table"=> "refferphysician" ,
										"Depend_Search"=> "physician_Reffer_id" ,
										"New_Value"=> addcslashes(addslashes(trim($pri_ref_req_arr["reff_phy_id"])),"\0..\37!@\177..\377")																					
									);
		$arrAuditTrailPriRef [] = array(							
										"Ins_Type"=> "primary",																
										"Data_Base_Field_Name"=> "effective_date" ,
										"Filed_Label"=> "pri_ref_stDt",
										"Filed_Text"=> "Patient Primary Referral Start Date",
										"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"effective_date") ,
										"New_Value"=> addcslashes(addslashes(trim($_POST['pri_ref_stDt'])),"\0..\37!@\177..\377")																					
									);
		$arrAuditTrailPriRef [] = array(							
										"Ins_Type"=> "primary",																
										"Data_Base_Field_Name"=> "end_date" ,
										"Filed_Label"=> "pri_ref_enDt",
										"Filed_Text"=> "Patient Primary Referral End Date",
										"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"end_date") ,
										"New_Value"=> addcslashes(addslashes(trim($_POST['pri_ref_enDt'])),"\0..\37!@\177..\377")																					
									);
		
		$arrAuditTrailPriRef [] = array(							
										"Ins_Type"=> "primary",																
										"Data_Base_Field_Name"=> "no_of_reffs" ,
										"Filed_Label"=> "pri_ref_visits",
										"Filed_Text"=> "Patient Primary Referral No. of Visits",
										"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"no_of_reffs") ,
										"New_Value"=> addcslashes(addslashes(trim($pri_ref_req_arr["no_of_reffs"])),"\0..\37!@\177..\377")																					
									);
		
		
		$arrAuditTrailPriRef [] = array(							
										"Ins_Type"=> "primary",																
										"Data_Base_Field_Name"=> "reffral_no" ,
										"Filed_Label"=> "pri_ref_number",
										"Filed_Text"=> "Patient Primary Referral#",
										"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"reffral_no") ,
										"New_Value"=> addcslashes(addslashes(trim($pri_ref_req_arr["reffral_no"])),"\0..\37!@\177..\377")																					
									);
		
		$arrAuditTrailPriAuth [] = array(																								
							"Ins_Type"=> "primary",	
							"Pk_Id"=> $auth_pri_chk,
							"Table_Name"=>"patient_auth",
							"Data_Base_Field_Name"=> "auth_name" ,
							"Filed_Text"=> "Patient Primary Insurance Auth Number",
							"Data_Base_Field_Type"=> fun_get_field_type($patientAuthDataFields,"auth_name") ,
							"Filed_Label"=> "AuthPriNumber",																						
							"Category"=> "patient_info",
							"Category_Desc"=> "insurence",
							"New_Value"=> ($_POST['AuthPriNumber']) ? trim($_POST['AuthPriNumber']) : ""																																										
						);
		$arrAuditTrailPriAuth [] = array(
									"Ins_Type"=> "primary",																
									"Data_Base_Field_Name"=> "AuthAmount" ,
									"Filed_Label"=> "AuthPriAmount",
									"Filed_Text"=> "Patient Primary Insurance Auth Amount",
									"Data_Base_Field_Type"=> fun_get_field_type($patientAuthDataFields,"AuthAmount") ,
									"New_Value"=> ($_POST['AuthPriAmount']) ? trim($_POST['AuthPriAmount']) : ""
									);
		$arrAuditTrailPriAuth [] = array(
									"Ins_Type"=> "primary",																
									"Data_Base_Field_Name"=> "auth_date" ,
									"Filed_Label"=> "pri_auth_date",
									"Filed_Text"=> "Patient Primary Insurance Auth Date",
									"Data_Base_Field_Type"=> fun_get_field_type($patientAuthDataFields,"auth_date") ,
									"New_Value"=> ($_POST['pri_auth_date']) ? trim($_POST['pri_auth_date']) : ""
									);

		$arrAuditTrailSec [] = array(																								
						"Ins_Type"=> "secondary",	
						"Pk_Id"=> $insurance_secondary_id,
						"Table_Name"=>"insurance_data",
						"Data_Base_Field_Name"=> "provider" ,
						"Filed_Text"=> "Patient Secondary Insurance Ins. Provider",
						"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"provider") ,
						"Filed_Label"=> "insSecProv_id",																						
						"Category"=> "patient_info",
						"Category_Desc"=> "insurence",
						"Depend_Select"=> "select name as provider" ,
						"Depend_Table"=> "insurance_companies" ,
						"Depend_Search"=> "id" ,
						"New_Value"=> ($sec_ins_data_arr['provider']) ? trim($sec_ins_data_arr['provider']) : ""																																										
					);
		
		$arrAuditTrailSec [] = array(
									"Ins_Type"=> "secondary",																
									"Data_Base_Field_Name"=> "policy_number" ,
									"Filed_Label"=> "insSecPolicy",
									"Filed_Text"=> "Patient Secondary Insurance Policy",
									"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"policy_number") ,
									"New_Value"=> addcslashes(addslashes(trim($sec_ins_data_arr['policy_number'])),"\0..\37!@\177..\377")																					
								);
		$arrAuditTrailSec [] = array(
									"Ins_Type"=> "secondary",																
									"Data_Base_Field_Name"=> "group_number" ,
									"Filed_Label"=> "insSecGroup",
									"Filed_Text"=> "Patient Secondary Insurance Group Number",
									"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"group_number") ,
									"New_Value"=> addcslashes(addslashes(trim($sec_ins_data_arr['group_number'])),"\0..\37!@\177..\377")																					
								);
		
		$arrAuditTrailSec [] = array(
									"Ins_Type"=> "secondary",																
									"Data_Base_Field_Name"=> "copay" ,
									"Filed_Label"=> "insSecCopay",
									"Filed_Text"=> "Patient Insurance Secondary CoPay",
									"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"copay") ,
									"New_Value"=> addcslashes(addslashes(trim($sec_ins_data_arr['copay'])),"\0..\37!@\177..\377")																					
								);		
		$arrAuditTrailSec [] = array(
									"Ins_Type"=> "secondary",																
									"Data_Base_Field_Name"=> "referal_required" ,
									"Filed_Label"=> "sec_ref_req",
									"Filed_Text"=> "Patient Insurance Secondary Ref. Req",
									"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"referal_required") ,
									"New_Value"=> addcslashes(addslashes(trim($_POST['sec_ref_req'])),"\0..\37!@\177..\377")																					
								);		
		$arrAuditTrailPri [] = array(
									"Ins_Type"=> "secondary",																
									"Data_Base_Field_Name"=> "referal_required" ,
									"Filed_Label"=> "sec_auth_req",
									"Filed_Text"=> "Patient Insurance Secondary Auth. Req",
									"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"auth_required") ,
									"New_Value"=> addcslashes(addslashes(trim($_POST['sec_auth_req'])),"\0..\37!@\177..\377")																					
								);		
		
		$arrAuditTrailSec [] = array(
									"Ins_Type"=> "secondary",																
									"Data_Base_Field_Name"=> "effective_date" ,
									"Filed_Label"=> "insSecActDt",
									"Filed_Text"=> "Patient Insurance Secondary Effective Date",
									"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"effective_date") ,
									"New_Value"=> addcslashes(addslashes(trim($_POST['insSecActDt'])),"\0..\37!@\177..\377")																					
								);		
		$arrAuditTrailSec [] = array(
									"Ins_Type"=> "secondary",																
									"Data_Base_Field_Name"=> "expiration_date" ,
									"Filed_Label"=> "insSecExpDt",
									"Filed_Text"=> "Patient Insurance Secondary Expiration Date",
									"Data_Base_Field_Type"=> fun_get_field_type($insDataFields,"expiration_date") ,
									"New_Value"=> addcslashes(addslashes(trim($_POST['insSecExpDt'])),"\0..\37!@\177..\377")																					
								);		

		$arrAuditTrailSecRef [] = array(							
										"Ins_Type"=> "secondary",			
										"Pk_Id"=> $sec_reff_id,
										"Table_Name"=>"patient_reff",															
										"Data_Base_Field_Name"=> "reff_phy_id" ,
										"Filed_Label"=> "sec_ref_phy_id",
										"Filed_Text"=> "Patient Secondary Referral Ref. Physician",
										"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"reff_phy_id") ,																				
										"Category"=> "patient_info",
										"Category_Desc"=> "insurence",
										"Depend_Select"=> "select CONCAT_WS(', ',LastName,FirstName) as refPhy" ,
										"Depend_Table"=> "refferphysician" ,
										"Depend_Search"=> "physician_Reffer_id" ,
										"New_Value"=> addcslashes(addslashes(trim($sec_ref_req_arr["reff_phy_id"])),"\0..\37!@\177..\377")																					
									);
		$arrAuditTrailSecRef [] = array(							
										"Ins_Type"=> "secondary",																
										"Data_Base_Field_Name"=> "effective_date" ,
										"Filed_Label"=> "sec_ref_stDt",
										"Filed_Text"=> "Patient Secondary Referral Start Date",
										"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"effective_date") ,
										"New_Value"=> addcslashes(addslashes(trim($_POST['sec_ref_stDt'])),"\0..\37!@\177..\377")																					
									);
		$arrAuditTrailSecRef [] = array(							
										"Ins_Type"=> "secondary",																
										"Data_Base_Field_Name"=> "end_date" ,
										"Filed_Label"=> "sec_ref_enDt",
										"Filed_Text"=> "Patient Secondary Referral End Date",
										"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"end_date") ,
										"New_Value"=> addcslashes(addslashes(trim($_POST['sec_ref_enDt'])),"\0..\37!@\177..\377")																					
									);
		$arrAuditTrailPriRef [] = array(							
										"Ins_Type"=> "secondary",																
										"Data_Base_Field_Name"=> "no_of_reffs" ,
										"Filed_Label"=> "sec_ref_visits",
										"Filed_Text"=> "Patient Secondary Referral No. of Visits",
										"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"no_of_reffs") ,
										"New_Value"=> addcslashes(addslashes(trim($sec_ref_req_arr["no_of_reffs"])),"\0..\37!@\177..\377")																					
									);
		
		$arrAuditTrailSecRef [] = array(							
										"Ins_Type"=> "secondary",																
										"Data_Base_Field_Name"=> "reffral_no" ,
										"Filed_Label"=> "sec_ref_number",
										"Filed_Text"=> "Patient Secondary Referral#",
										"Data_Base_Field_Type"=> fun_get_field_type($patientRefDataFields,"reffral_no") ,
										"New_Value"=> addcslashes(addslashes(trim($sec_ref_req_arr["reffral_no"])),"\0..\37!@\177..\377")																					
									);

		$arrAuditTrailSecAuth [] = array(																								
							"Ins_Type"=> "secondary",	
							"Pk_Id"=> $auth_sec_chk,
							"Table_Name"=>"patient_auth",
							"Data_Base_Field_Name"=> "auth_name" ,
							"Filed_Text"=> "Patient Secondary Insurance Auth Number",
							"Data_Base_Field_Type"=> fun_get_field_type($patientAuthDataFields,"auth_name") ,
							"Filed_Label"=> "AuthSecNumber",																						
							"Category"=> "patient_info",
							"Category_Desc"=> "insurence",
							"New_Value"=> ($_POST['AuthSecNumber']) ? trim($_POST['AuthSecNumber']) : ""																																										
						);
		$arrAuditTrailSecAuth [] = array(
									"Ins_Type"=> "secondary",																
									"Data_Base_Field_Name"=> "AuthAmount" ,
									"Filed_Label"=> "AuthSecAmount",
									"Filed_Text"=> "Patient Secondary Insurance Auth Amount",
									"Data_Base_Field_Type"=> fun_get_field_type($patientAuthDataFields,"AuthAmount") ,
									"New_Value"=> ($_POST['AuthSecAmount']) ? trim($_POST['AuthSecAmount']) : ""
									);
		$arrAuditTrailSecAuth [] = array(
									"Ins_Type"=> "secondary",																
									"Data_Base_Field_Name"=> "auth_date" ,
									"Filed_Label"=> "sec_auth_date",
									"Filed_Text"=> "Patient Secondary Insurance Auth Date",
									"Data_Base_Field_Type"=> fun_get_field_type($patientAuthDataFields,"auth_date") ,
									"New_Value"=> ($_POST['sec_auth_date']) ? trim($_POST['sec_auth_date']) : ""
									);		
		//END AUDIT INSURANCE INFO
		$arrAuditTrail = array_merge($arrAuditTrail,$arrAuditTrailInsCase,$arrAuditTrailPri,$arrAuditTrailSec,$arrAuditTrailPriRef,$arrAuditTrailSecRef,$arrAuditTrailPriAuth,$arrAuditTrailSecAuth);
		return $arrAuditTrail;										
	}
$getField = get_copay_field('checkin_on_done');
$checkin_on_done = $getField['checkin_on_done'];
if( $checkin_on_done && $sch_id && !$btn_submit ) {
	$apptIds = $obj_scheduler->get_ap_ids_by_patient_and_sel_date($ci_pid,$sel_date,'true');
	//pre($apptIds);
}

if( $checkin_on_done && $sch_id && $hitDoneBtn ) {
	function do_check_in($get_date, $pid, $sch_id, $chg_to = '13', $reason='', $doNotkeepOrg=1){
		//echo '1: '.$get_date."\n".$pid."\n".$sch_id."\n".$chg_to."\n".$reason."\n".$doNotkeepOrg;
		
		if(is_array($sch_id)){
			$sch_id_arr=$sch_id;
		}else {
			$sch_id_arr=explode(',',$sch_id);
		}
		
		global $obj_scheduler;
		
		foreach($sch_id_arr as $sch_id_val)
		{
			$sch_id=(int)$sch_id_val;
			if($get_date != "" && $pid != "" && $sch_id != "" && $chg_to != "") {
				//Schedular slot time setting
				$qry = "insert into current_time_locator set sch_id = '".$sch_id."', uid = '".$_SESSION["authId"]."', `dated`='".date('Y-m-d')."'";
				imw_query($qry);

				//logging this action in previous status table
				$remote_req = 0;
				if($chg_to == 13){
					$obj_scheduler->logApptChangedStatus($sch_id, "", "", "", $chg_to, "", "", $_SESSION['authUser'], $reason, "", false);
					$remote_req = 1;
				}

				//updating schedule appointments details
				$obj_scheduler->updateScheduleApptDetails($sch_id, "", "", "", $chg_to, "", "", $_SESSION['authUser'], "", "", false);
				
				//flag to check do we have data regarding appointment or not
				$hv_appt_data=false;
				if($chg_to == 13 ){			
					for($i = 0; $i <= 3; $i++) {
						$green_id = "";
						$vquery_d = "SELECT min(end_date) , reff_id FROM `patient_reff` WHERE end_date >= current_date() and effective_date <= current_date() and reff_type = '".$i."' and patient_id='".$pid."' GROUP BY reff_id ORDER BY end_date limit 0,1 ";
						$vsql_d = imw_query($vquery_d);	
						$vrs_d = imw_fetch_array($vsql_d);			
						$green_id = $vrs_d['reff_id'];
						if($green_id == ""){
							$vquery_d = "SELECT min(no_of_reffs) , reff_id FROM `patient_reff` WHERE no_of_reffs > 0 and reff_type = '".$i."' and patient_id='".$pid."' GROUP BY reff_id ORDER BY no_of_reffs limit 0,1 ";
							$vsql_d = imw_query($vquery_d);	
							$vrs_d = imw_fetch_array($vsql_d);	
							$green_id = $vrs_d['reff_id'];
						}
						if($green_id != ""){
							$reff_ids[] = $green_id;
						}
					}
				}
			}
			/* Code for task manager */
			if($get_date != "" && $pid != "" && $sch_id != "" && $chg_to != ""){
					if($hv_appt_data==false)
					{
						if(!empty($sch_id)){
							$q = "SELECT sa_doctor_id,sa_patient_id  FROM schedule_appointments WHERE id = '".$sch_id."'";
							$r = imw_query($q);	
							$a = imw_fetch_array($r);
						}
					}

					if($a){
						$task_pid=$a['sa_patient_id'];
						$task_doctor_id=$a['sa_doctor_id'];

						$params=array();
						$params['patientid']=$task_pid;
						$params['operatorid']=$task_doctor_id;
						$params['section']='appointment';

						switch($chg_to) {
							case 18:
								$sub_section='appt_canceled';
								break;
							case 3:
								$sub_section='appt_no_show';
								break;
							default:
								$sub_section='other_action';
								break;
						}
						$params['sub_section']=$sub_section; //appt_canceled,appt_created,appt_deleted,appt_no_show,appt_reschedule
						$params['obj_value']=$sch_id;
						$serialized_arr = serialize($params);
						include_once("../../common/assign_new_task.php");
					}
			}

		}
		//-------- Get Date and Day name use for Scheduler Load -----
			
		list($y, $m, $d) = explode('-', $get_date);
		$dayName = date('l',mktime(0, 0, 0, $m, $d, $y));
		return array('pid' => $pid, 'status' => $chg_to, 'sch_id' => $sch_id,'sch_date' => $get_date, 'day_name' => $dayName);

	}
}
//--- SAVE PATIENT INFORMATION ----
if(trim($btn_submit) != ''){
	
	// Do check in if it is off from frondesk
	// based on global settings
	if( $checkin_on_done && $sch_id && $hitDoneBtn ) {
		
		$sel_date = isset($_POST['hiddSelDate']) ? $_POST['hiddSelDate'] : '';
		$apptIds = $obj_scheduler->get_ap_ids_by_patient_and_sel_date($ci_pid,$sel_date,'true');
		$hiddConfirmCheckInApplyAll = $_POST['hiddConfirmCheckInApplyAll'];
		
		//getchange_status(ga_ap_id, ga_st_type, ga_sel_date, ga_sel_fac, ga_pt_id);
		$schedule_id = $hiddConfirmCheckInApplyAll ? $apptIds : (int)$_POST['sch_id'] ;
		
		$checkInRes = do_check_in($sel_date,$edit_patient_id,$schedule_id);
		
		echo '
		<script>
			if( window.opener ) {
				if( window.opener.top.document.getElementById("appt_scheduler_status")){
					if( window.opener.top.document.getElementById("appt_scheduler_status").value = "loaded" ){
						if( window.opener.top ){
							if( window.opener.top.fmain ) {
								if( window.opener.top.fmain.pre_load_front_desk ) {
									window.opener.top.fmain.pre_load_front_desk('.(int)$checkInRes['pid'].', '.(int)$checkInRes['sch_id'].', false);	
								}
								if( window.opener.top.fmain.load_appt_schedule ) {
									window.opener.top.fmain.load_appt_schedule("'.$checkInRes['sch_date'].'", "'.$checkInRes['day_name'].'", "", "nonono");
								}
							}
						}
					}
				}
			}
		</script>';
		
	}
	
	// New Fields Sexual Orientation and Gender Identity
	$arrSOR = sexual_orientation();
	$arrGI = gender_identity();
	$other_sor= ('Other' == $_POST['sexual_orientation']) 	? addslashes(trim($_POST['otherSOR'])) : '';
	$other_gi = ('Other' == $_POST['gender_identity']) 		? addslashes(trim($_POST['otherGI'])) : '';
	$sor_code	= $arrSOR[$_POST['sexual_orientation']]['code'];
	$gi_code 	= $arrGI[$_POST['gender_identity']]['code'];


	$heardAbtDesc = xss_rem($heardAbtDesc);
	$language	= $_POST['language'];
	if($language=='Other' && $_REQUEST['otherLanguage']!=""){
		$language = "Other -- ".$_REQUEST['otherLanguage'];
	}	
	$patientDataArr = array();
	$edit_patient_id = $_POST['edit_patient_id'];

	$patientDataArr['title'] = ucwords($_POST['title']);
	$patientDataArr['p_imagename'] = $_POST['patient_photo_name'];
	$patientDataArr['fname'] = ucwords($_POST['fname']);
	$patientDataArr['mname'] = ucwords($_POST['mname']);
	$patientDataArr['lname'] = ucwords($_POST['lname']);
	$patientDataArr['suffix'] = $_POST['suffix'];
	$patientDataArr['sex'] = $_POST['selGender'];
	$patientDataArr['email'] = $_POST['pat_email'];
	$patientDataArr['status'] = $_POST['pat_marital_status'];	
	$patientDataArr['DOB'] = getDateFormatDB($_POST['pt_dob']);
	$patientDataArr['driving_licence'] = $_POST['dlicence'];
	$patientDataArr['licence_photo'] = $_POST['license_image_name'];
	$patientDataArr['ss'] = $_POST['ssnNumber'];
	$patientDataArr['default_facility'] = $_POST['default_facility'];
	$patientDataArr['street'] = $_POST['street'][0];
	$patientDataArr['street2'] = $_POST['street2'][0];
	$patientDataArr['postal_code'] = (inter_country() != "UK")?core_padd_char($_POST['postal_code'][0],5):$_POST['postal_code'][0];
	$patientDataArr['zip_ext'] = $_POST['zip_ext'][0];
	$patientDataArr['city'] = $_POST['city'][0];
	$patientDataArr['state'] = $_POST['state'][0];
	$patientDataArr['county'] = $_POST['county'][0];
	$patientDataArr['phone_home'] = $_POST['phone_home'];
	$patientDataArr['phone_biz'] = $_POST['phone_biz'];
	$patientDataArr['phone_biz_ext'] = $_POST['phone_biz_ext'];
	$patientDataArr['phone_cell'] = $_POST['phone_cell'];
	$patientDataArr['patient_notes'] = $_POST['patient_notes'];
	$patientDataArr['chk_notes_scheduler'] = $_POST['chkNotesScheduler'];
	$patientDataArr['chk_notes_chart_notes'] = $_POST['chkNotesChartNotes'];
	$patientDataArr['chk_notes_accounting'] = $_POST['chkNotesAccounting'];
	$patientDataArr['chk_notes_optical'] = $_POST['chkNotesOptical'];
	$patientDataArr["EMR"] = $_POST['pat_emr'];
	$patientDataArr["hold_statement"] = $_POST['pat_hs'];
	$patientDataArr["preferr_contact"] = $_POST['pf_contact'];
	$patientDataArr["race"] = (is_array($_POST['race']))?implode(",",$_POST['race']):"";
	$patientDataArr["otherRace"] =(is_array($_POST['race']) && in_array("Other",$_POST['race']))?$_POST['otherRace']:"";
	$patientDataArr["ethnicity"] =(is_array($_POST['ethnicity']) ) ?implode(",",$_POST['ethnicity']):$_POST['ethnicity'];
	$patientDataArr["otherEthnicity"] = (is_array($_POST['ethnicity']) && in_array("Other",$_POST['ethnicity']))?$_POST['otherEthnicity']:"";
	$patientDataArr["sor_txt"] = $_POST['sexual_orientation'];
	$patientDataArr["other_sor"] = $other_sor;
	$patientDataArr["sor_code"] = $sor_code;
	$patientDataArr["gi_txt"] = $_POST["gender_identity"];
	$patientDataArr["other_gi"] = $other_gi;
	$patientDataArr["gi_code"] = $gi_code;
    
    // Emergency contact fields starts here
    $patientDataArr["contact_relationship"] = convertUcfirst($contact_relationship);
    $patientDataArr["phone_contact"] = $phone_contact;
    $patientDataArr["emergencyRelationship"] = $emerRelation;
    $patientDataArr["emergencyRelationship_other"] = $relation_other_textbox;
    // Emergency contact fields ends here
    
	$patientDataArr["language"] = $language;
	$patientDataArr["lang_code"] = $lang_code;
	$patientDataArr["temp_key"] = $temp_key;
    
    
    //Release Information Starts here
    $patientDataArr["relInfoName1"] = $_POST['relInfoName1'];
    $patientDataArr["relInfoPhone1"] = $_POST['relInfoPhone1'];
    $patientDataArr["relInfoReletion1"] = $_POST['relInfoReletion1'];
    $patientDataArr["otherRelInfoReletion1"] = $_POST['otherRelInfoReletion1'];
    //$patientDataArr["relInfoComment1"] = $_POST['relInfoComment1'];
    $patientDataArr["relInfoName2"] = $_POST['relInfoName2'];
    $patientDataArr["relInfoPhone2"] = $_POST['relInfoPhone2'];
    $patientDataArr["relInfoReletion2"] = $_POST['relInfoReletion2'];
    $patientDataArr["otherRelInfoReletion2"] = $_POST['otherRelInfoReletion2'];
    //$patientDataArr["relInfoComment2"] = $_POST['relInfoComment2'];
    $patientDataArr["relInfoName3"] = $_POST['relInfoName3'];
    $patientDataArr["relInfoPhone3"] = $_POST['relInfoPhone3'];
    $patientDataArr["relInfoReletion3"] = $_POST['relInfoReletion3'];;
    $patientDataArr["otherRelInfoReletion3"] = $_POST['otherRelInfoReletion3'];
    //$patientDataArr["relInfoComment3"] = $_POST['relInfoComment3'];
    $patientDataArr["relInfoName4"] = $_POST['relInfoName4'];
    $patientDataArr["relInfoPhone4"] = $_POST['relInfoPhone4'];
    $patientDataArr["relInfoReletion4"] = $_POST['relInfoReletion4'];
    $patientDataArr["otherRelInfoReletion4"] = $_POST['otherRelInfoReletion4'];
    //$patientDataArr["relInfoComment4"] = $_POST['relInfoComment4'];
    //Release Information Ends here
    
	
	/*--START HEARD ABOUT US--*/
	$arrElemHeardAbtUs = explode("-",$_REQUEST['elem_heardAbtUs']);
	$elem_heardAbtUs = addslashes($arrElemHeardAbtUs[0]);
	$elem_heardAbtUsValue = addslashes($arrElemHeardAbtUs[1]);
	
	if($_REQUEST['heardAbtOther'] != ''){ // && core_check_privilege(array('priv_admin'))
		$chkqryHeardMaster = "SELECT heard_id FROM heard_about_us WHERE heard_options = '".addslashes($_REQUEST['heardAbtOther'])."' limit 1";
		$rschkqryHeardMaster = imw_query($chkqryHeardMaster);
		if($rschkqryHeardMaster){
			if(imw_num_rows($rschkqryHeardMaster) == 0){
				$priv_query_part = ", for_all=0 ";

				$qryHeard = "Insert into heard_about_us set heard_options = '".addslashes($_REQUEST['heardAbtOther'])."'".$priv_query_part;
				$resHeard = imw_query($qryHeard);
				$elem_heardAbtUs = imw_insert_id();
			}
			else{
				$rowChkQryHeardMaster = imw_fetch_array($rschkqryHeardMaster);
				$elem_heardAbtUs = $rowChkQryHeardMaster['heard_id'];		
				imw_free_result($rschkqryHeardMaster);
			}	
		}
	}
	$heardDate = date('Y-m-d');

	if(in_array($elem_heardAbtUsValue,array('Family','Friends','Doctor','Previous Patient.','Previous Patient')) ) {
		$heardAbtDesc = '';
	}
	else {
		$heardAbtSearch = $heardAbtSearchId = ''; 

		$heardQryRes = mysqlifetchdata("SELECT id FROM heard_about_us_desc WHERE heard_id = '$elem_heardAbtUs' and heard_desc = '$heardAbtDesc'");
		$heard_id = $heardQryRes[0]['id'];
		if(empty($heard_id) == true){
			$heardDataArr = array();
			$heardDataArr["heard_desc"] = $heardAbtDesc;
			$heardDataArr["heard_id"] = $elem_heardAbtUs;
			AddRecords($heardDataArr,"heard_about_us_desc");
		}
	}

	$patientDataArr["heard_abt_us"] = $elem_heardAbtUs;
	$patientDataArr["heard_abt_desc"] = $heardAbtDesc;
	$patientDataArr["heard_about_us_date"] = $heardDate;
	$patientDataArr["heard_abt_search"] = addslashes($_REQUEST['heardAbtSearch']);
	$patientDataArr["heard_abt_search_id"] = (int)$heardAbtSearchId;
	/*--END HEARD ABOUT US--*/
	
	$copay_policies_res = mysqlifetchdata("select erx_entry from copay_policies");
	$patientDataArr["erx_entry"] = intval($copay_policies_res[0]['erx_entry']);

	if($_POST['language']=='Other' && $_REQUEST['otherLanguage']!=""){
		$patientDataArr["language"] = "Other -- ".$_REQUEST['otherLanguage'];
	}
	$refPhyNameFullNew = NULL;
	$tmpRefPhyId = (int)$_POST['ref_phy_id'];
	if( !$tmpRefPhyId )
	{
		if($_REQUEST['ref_phy_name'] ){
			$arrTemp = explode(";", $_REQUEST['ref_phy_name']);
			$patientDataArr['primary_care'] = trim($arrTemp[0]);
			$intRefPhyId = 0;
			$strRefPhyName = "";
			list($intRefPhyId, $strRefPhyName) = $OBJCommonFunction->chk_create_ref_phy($patientDataArr['primary_care'],5);
			if((empty($intRefPhyId) == false) && (empty($strRefPhyName) == false)){
				$patientDataArr['primary_care_id'] = $intRefPhyId;
				$patientDataArr['primary_care'] = $strRefPhyName;
				$refPhyNameFullNew = trim($strRefPhyName);
			}	
		}
	}	
	else{
		$arrTemp = explode(";", $_REQUEST['ref_phy_name']);
		$patientDataArr['primary_care_id'] = $tmpRefPhyId;
		$patientDataArr['primary_care'] = trim($arrTemp[0]);
		$refPhyNameFullNew = trim($arrTemp[0]);
	}
	
	$priCarePhyNameFullNew = NULL;
	$tmpPrimaryCarePhyId = (int)$_POST['primary_care_phy_id'];
	if( !$tmpPrimaryCarePhyId )
	{
		if($_REQUEST['primary_care_name'] ){
			$arrTemp = explode(";", $_REQUEST['primary_care_name']);
			$patientDataArr['primary_care_phy_name'] = trim($arrTemp[0]);
			$intPCPId = 0;
			$strPCPName = "";
			list($intPCPId, $strPCPName) = $OBJCommonFunction->chk_create_ref_phy($patientDataArr['primary_care_phy_name'],5);
			if((empty($intPCPId) == false) && (empty($strPCPName) == false)){
				$patientDataArr['primary_care_phy_id'] = $intPCPId;
				$patientDataArr['primary_care_phy_name'] = $strPCPName;
				$priCarePhyNameFullNew = trim($strPCPName);
			}	
		}
	}	
	else{
		$arrTemp = explode(";", $_REQUEST['primary_care_name']);
		$patientDataArr['primary_care_phy_id'] = $tmpPrimaryCarePhyId;
		$patientDataArr['primary_care_phy_name'] = trim($arrTemp[0]);
		$priCarePhyNameFullNew = trim($arrTemp[0]);
	}

	$HL7newPtMode = false;
	//--- ADD NEW PATIENT ----
	if(trim($edit_patient_id) == ''){
	/*---CHECK FOR REMOTE PT.REG---*/
		$HL7newPtMode = true;
		$dpr_pt_data = array();
		$dpr_pt_data['ptfname'] 	= convertUcfirst($fname);
		$dpr_pt_data['ptlname'] 	= convertUcfirst($lname);
		$dpr_pt_data['ptdob'] 		= getDateFormatDB($_POST['pt_dob']);
		$dpr_pt_data['ptgender'] 	= $_POST['selGender'];
		$dpr_pt_data['ptzip'] 		= (inter_country() != "UK")?core_padd_char($_POST['code'],5):$_POST['code'];
		$dpr_pt_data['ptzip_ext'] 	= $_POST['zip_ext'];
		$dpr_pt_data['providerId'] 	= $_SESSION['authId'];
		//include_once("../../../remote_pt_reg/remote_patient_reg.php");

		$arr_patient_next_id = get_Next_PatientID($dpr_pt_data);
		if($arr_patient_next_id['error']==''){
			$patient_next_id = $arr_patient_next_id['patient_id'];
			$patient_src_server = $arr_patient_next_id['src_server'];
		}else{
			echo $arr_patient_next_id['error'];exit;
		}
		
		

		
		$patDataArr = array();
		$patDataArr["title"] = ucwords($title);
		$patDataArr["fname"] = trim(ucwords($fname));
		$patDataArr["mname"] = ucwords($mname);
		$patDataArr["lname"] = trim(ucwords($lname));
		$patDataArr["id"] = $patient_next_id;
		$patDataArr["pid"] = $patient_next_id;
		$patDataArr["date"] = date('Y-m-d  H:i:s');
		if($patient_src_server>0){$patDataArr["src_server"] = $patient_src_server;}
		$patDataArr["created_by"] = $operator_id;
		
		//Clear --
		clean_patient_session();

		//--- ADD NEW PATIENT ----
		$patDataArr["hipaa_mail"] = 1;
		$patDataArr["hipaa_email"] = 1;
		$patDataArr["hipaa_voice"] = 1;
		$patDataArr["hipaa_text"] = 1;
		if(trim($patDataArr["fname"]) && trim($patDataArr["lname"])){
			if(isset($temp_key_chk_val) && $temp_key_chk_val == 1){
				$patientDataArr["temp_key_chk_val"] = $temp_key_chk_val;
				$patientDataArr["temp_key_chk_opr_id"] = $_SESSION['authId'];
				$patientDataArr["temp_key_chk_datetime"] = date('Y-m-d H:i:s');
			}
			// Generate patient portal key if patient is new
			$patientDataArr["temp_key"] = temp_key_gen(6,0);
			
			$edit_patient_id = AddRecords($patDataArr,"patient_data");
		}
		$_SESSION['patient'] = trim($edit_patient_id);
		$_SESSION['patientNewFlag'] = true;
	}
	//----------BEGIN ADD MULTIPLE ADDRESSES--------------
	if( $edit_patient_id ) {
		$address_cnt = max(array_keys($_REQUEST['street']));
		$all_communication_index = (isset($_REQUEST['all_communication']))? $_REQUEST['all_communication']:"0";
		for($i=0; $i<=$address_cnt; $i++){
			if(($_POST['id_address'][$i] == "" || $_POST['id_address'][$i] == "0") && ($i ==0 || ($i!=0 && $_POST['postal_code'][$i]!=""))){	
				$qry = "INSERT INTO patient_multi_address 
					SET street = '".imw_real_escape_string($_POST['street'][$i])."',
					street2 = '".imw_real_escape_string($_POST['street2'][$i])."',
					postal_code = '".imw_real_escape_string($_POST['postal_code'][$i])."',
					zip_ext = '".imw_real_escape_string($_POST['zip_ext'][$i])."',
					city = '".imw_real_escape_string($_POST['city'][$i])."',
					state = '".imw_real_escape_string($_POST['state'][$i])."',
					country_code = '".imw_real_escape_string($_POST['country_code'][$i])."',
					county = '".imw_real_escape_string($_POST['county'][$i])."',
					patient_id  = '".$edit_patient_id."'
					";
				imw_query($qry) or die(imw_error());
				$address_id = imw_insert_id();
				if($i == $all_communication_index)
				$default_address_id = $address_id;
			}else if($_POST['postal_code'][$i] != ""){
				$qry = "UPDATE patient_multi_address 
				SET street = '".imw_real_escape_string($_POST['street'][$i])."',
				street2 = '".imw_real_escape_string($_POST['street2'][$i])."',
				postal_code = '".imw_real_escape_string($_POST['postal_code'][$i])."',
				zip_ext = '".imw_real_escape_string($_POST['zip_ext'][$i])."',
				city = '".imw_real_escape_string($_POST['city'][$i])."',
				state = '".imw_real_escape_string($_POST['state'][$i])."',
				country_code = '".imw_real_escape_string($_POST['country_code'][$i])."',
				county = '".imw_real_escape_string($_POST['county'][$i])."',
				patient_id  = '".$edit_patient_id."'
				WHERE id = '".$_POST['id_address'][$i]."'
				";
				imw_query($qry) or die(imw_error());
				$address_id = $_POST['id_address'][$i];
			}
			
			if($i == $all_communication_index){
				$patientDataArr['street'] = $_POST['street'][$i];
				$patientDataArr['street2'] = $_POST['street2'][$i];
				$patientDataArr['postal_code'] = $_POST['postal_code'][$i];
				$patientDataArr['zip_ext'] = $_POST['zip_ext'][$i];
				$patientDataArr['city'] = $_POST['city'][$i];
				$patientDataArr['state'] = $_POST['state'][$i];
				$patientDataArr['country_code'] = $_POST['country_code'][$i];
				$patientDataArr['county'] = $_POST['county'][$i];
				if($_POST['id_address'][$i] != "" && $_POST['id_address'][$i] != "0")
				$default_address_id = $_POST['id_address'][$i];
			}
		}
	}
	
	
	$address_del_id = trim($_REQUEST['address_del_id'],",");
	if($address_del_id != ""){
		imw_query("UPDATE patient_multi_address SET del_status = 1 WHERE id IN (".$address_del_id.")");
	}
	$patientDataArr['default_address'] = $default_address_id;
	//----------END ADD MULTIPLE ADDRESSES--------------
	
	//---------To update schedule appointments table
	if(trim($edit_patient_id))
	{
		$vquery_c = "select fname, mname, lname	from patient_data where id = '$edit_patient_id'";		
		$vsql = imw_query($vquery_c);
		$queryResult = imw_fetch_assoc($vsql);

		$exitingFirstName = $queryResult['fname'];
		$exitingMiddleName = $queryResult['mname'];
		$exitingLastName = $queryResult['lname'];


		$str_upd_appt = "UPDATE schedule_appointments SET ";
		$str_upd_appt_clause = "";
		if((trim($fname) != "" && (trim($fname) != trim($exitingFirstName))) || (trim($mname) != "" && (trim($mname) != trim($exitingMiddleName))) || (trim($lname) != "" && (trim($lname) != trim($exitingLastName)))){
			$str_sa_patient_name = "";
			$str_sa_patient_name = $lname != "" ? $lname.", " : "";
			$str_sa_patient_name .= $fname != "" ? $fname : "";
			$str_sa_patient_name .= $mname != "" ? ' '.strtoupper(substr($mname,0,1)) : "";
			if($str_sa_patient_name != ""){
				$str_upd_appt_clause .= " sa_patient_name = '".$str_sa_patient_name."', ";
			}
		}
		if($str_upd_appt_clause != ""){
			$str_upd_appt_clause = substr($str_upd_appt_clause, 0 ,-2);
			$str_upd_appt .= $str_upd_appt_clause;
			$str_upd_appt .= " WHERE sa_patient_id = '".$edit_patient_id."'";
			imw_query($str_upd_appt);
		}
	}
	//-------end of updating name in schedule appointments
	
	//pre($patientDataArr, 1);
	if(trim($patientDataArr["fname"]) != '' && trim($patientDataArr["lname"]) != ''){
		$row_pd = imw_fetch_assoc(imw_query("SELECT temp_key_chk_val FROM patient_data WHERE id = '".$edit_patient_id."'"));
		if(isset($temp_key_chk_val) && $temp_key_chk_val == 1 && $row_pd['temp_key_chk_val'] == 0){
			$patientDataArr["temp_key_chk_val"] = $temp_key_chk_val;
			$patientDataArr["temp_key_chk_opr_id"] = $_SESSION['authId'];
			$patientDataArr["temp_key_chk_datetime"] = date('Y-m-d H:i:s');
		}
		
		UpdateRecords($edit_patient_id,'id',$patientDataArr,'patient_data');
//------------BEGIN ADD/UPDATE REFERRING PHYSICIAN AND PCP IN PATIENT_MULTI_REF_PHY ------------------//	
		if( $_POST['ref_phy_id'] ){
			$qry_sel_multi_phy = "select id from patient_multi_ref_phy WHERE patient_id = '".$edit_patient_id."' and phy_type=1 and status=0 order by id asc limit 0,1";
			$res_multi_phy = imw_query($qry_sel_multi_phy);
			if(imw_num_rows($res_multi_phy)>0){
					$row_multi_phy = imw_fetch_assoc($res_multi_phy);
					$qry_update_multi_phy = "UPDATE patient_multi_ref_phy SET ref_phy_id='".$_POST['ref_phy_id']."',ref_phy_name='".$patientDataArr['primary_care']."',modified_by = '".$_SESSION['authId']."', modified_by_date_time = '".date('Y-m-d H:i:s')."' WHERE id = '".$row_multi_phy["id"]."'";
					imw_query($qry_update_multi_phy);
			}
			else{
				$qry_update_multi_phy = "INSERT INTO patient_multi_ref_phy SET ref_phy_id='".$_POST['ref_phy_id']."',ref_phy_name='".$patientDataArr['primary_care']."',phy_type='1',patient_id = '".$edit_patient_id."',created_by='".$_SESSION['authId']."',created_by_date_time='".date('Y-m-d H:i:s')."'";
				imw_query($qry_update_multi_phy);
			}
		}
		if( $_POST['primary_care_phy_id'] ){
			$qry_sel_multi_pcp = "select id,phy_type from patient_multi_ref_phy WHERE patient_id = '".$edit_patient_id."' and (phy_type=3 or phy_type=4 ) and status=0 order by id asc limit 0,1";
			$res_multi_pcp = imw_query($qry_sel_multi_pcp);
			if(imw_num_rows($res_multi_pcp)>0){
					$row_multi_pcp = imw_fetch_assoc($res_multi_pcp);
					$qry_update_multi_pcp = "UPDATE patient_multi_ref_phy SET ref_phy_id='".$_POST['primary_care_phy_id']."',ref_phy_name='".$patientDataArr['primary_care_phy_name']."',modified_by = '".$_SESSION['authId']."', modified_by_date_time = '".date('Y-m-d H:i:s')."' WHERE id = '".$row_multi_pcp["id"]."'";
					imw_query($qry_update_multi_pcp);
			}
			else{
				$qry_update_multi_phy = "INSERT INTO patient_multi_ref_phy SET ref_phy_id='".$_POST['primary_care_phy_id']."',ref_phy_name='".$patientDataArr['primary_care_phy_name']."',phy_type='4',patient_id = '".$_REQUEST["pt_id"]."',created_by='".$_SESSION['authId']."',created_by_date_time='".date('Y-m-d H:i:s')."'";
				imw_query($qry_update_multi_phy);
			}
		}
//------------END ADD/UPDATE REFERRING PHYSICIAN AND PCP IN PATIENT_MULTI_REF_PHY ------------------//	
		log_patient_update($patientDataArr);
	}

	#setting or inserting general health PCP------------start
	if($priCarePhyNameFullNew){
		$genQryRes = mysqlifetchdata("select general_id from general_medicine where patient_id = '$edit_patient_id'");
		$pat_general_id = NULL;
		if($genQryRes && count($genQryRes)>0){
			$pat_general_id = $genQryRes[0]['general_id'];
		}
		$gen_data_arr = array();
		$gen_data_arr["patient_id"] = $edit_patient_id;
		$gen_data_arr["med_doctor"] = $priCarePhyNameFullNew;
		
		if($pat_general_id==NULL){
			AddRecords($gen_data_arr,"general_medicine");
		}
		else{
			UpdateRecords($pat_general_id,"general_id",$gen_data_arr,"general_medicine");
		}
	}
	#setting or inserting general health PCP------------end	
    
    
    //--- RESPONSIBLE PARTY -----
    if(isset($_POST['lname1']) && trim($_POST['lname1'])!='' && isset($_POST['fname1']) && trim($_POST['fname1'])!='' && trim($edit_patient_id)!=''){
        $pid=$edit_patient_id;
        $dob1=$_POST['dob1'];
        $title1=$_POST['title1'];
        $fname1=$_POST['fname1'];
        $mname1=$_POST['mname1'];
        $lname1=$_POST['lname1'];
        $suffix1=$_POST['suffix1'];
        $sex1=$_POST['sex1'];
        $ss1=$_POST['ss1'];
        $street1=$_POST['street1'];
        $street_emp=$_POST['street_emp'];
        $city1=$_POST['city1'];
        $state2=$_POST['state2'];
        $postal_code1=$_POST['postal_code1'];
        $rzip_ext=$_POST['rzip_ext'];
        $country_code1=$_POST['country_code1'];
        $status1=$_POST['status1'];
        $relation1=$_POST['relation1'];
        $other1=$_POST['other1'];
        $emergency_contact1='';
        $phone_contact1='';
        $phone_home1=$_POST['phone_home1'];
        $phone_biz1=$_POST['phone_biz1'];
        $phone_cell1=$_POST['phone_cell1'];
        $email1=$_POST['email1'];
        $dlicence1=(isset($_POST['dlicence1']) && $_POST['dlicence1']!='')?$_POST['dlicence1']:"";
        $chkHippaRelResp=$_POST['chkHippaRelResp'];

        $date_convert1 = getDateFormatDB($dob1);
        $ocularSaveDataArr = array();
        $ocularSaveDataArr["patient_id"] = $pid;
        $ocularSaveDataArr["title"] = convertUcfirst($title1);
        $ocularSaveDataArr["fname"] = convertUcfirst($fname1);
        $ocularSaveDataArr["mname"] = convertUcfirst($mname1);
        $ocularSaveDataArr["lname"] = convertUcfirst($lname1);
        $ocularSaveDataArr["suffix"] = convertUcfirst($suffix1);
        $ocularSaveDataArr["dob"] = $date_convert1;
        $ocularSaveDataArr["sex"] = $sex1;
        $ocularSaveDataArr["ss"] = $ss1;
        $ocularSaveDataArr["address"] = convertUcfirst($street1);
        $ocularSaveDataArr["address2"] = convertUcfirst($street_emp);
        $ocularSaveDataArr["city"] = convertUcfirst($city1);
        $ocularSaveDataArr["state"] = ucwords($state2);
        $ocularSaveDataArr["zip"] = $postal_code1;
        $ocularSaveDataArr["zip_ext"] = $rzip_ext;
        $ocularSaveDataArr["country"] = $country_code1;
        $ocularSaveDataArr["marital"] = $status1;
        $ocularSaveDataArr["relation"] = $relation1;
        $ocularSaveDataArr["other1"] = $other1;
        $ocularSaveDataArr["emergency_contact"] = convertUcfirst($emergency_contact1);
        $ocularSaveDataArr["phone_contact"] = core_phone_unformat($phone_contact1);
        $ocularSaveDataArr["home_ph"] = core_phone_unformat($phone_home1);
        $ocularSaveDataArr["work_ph"] = core_phone_unformat($phone_biz1);
        $ocularSaveDataArr["mobile"] = core_phone_unformat($phone_cell1);
        $ocularSaveDataArr["email"] = $email1;
        $ocularSaveDataArr["licence"] = $dlicence1;
        $ocularSaveDataArr["hippa_release_status"] = $chkHippaRelResp;
		$ocularSaveDataArr["licence_image"] = addslashes($_POST['resp_license_image']);

		if( isset($_SESSION['rpscan_license_comment']) ) {
			$ocularSaveDataArr["licenseComments"] = addslashes($_SESSION['rpscan_license_comment']);
			unset($_SESSION['rpscan_license_comment']);
		}
		
		if( isset($_SESSION['rpscan_license_opr']) ) {
			$ocularSaveDataArr["licenseOperator"] = $_SESSION['rpscan_license_opr'];
			unset($_SESSION['rpscan_license_opr']);
		}
		
		
		
		$ocularSaveDataArr["erp_resp_username"] = "";
		$ocularSaveDataArr["erp_resp_imw_password"] = "";
		if(isERPPortalEnabled()) {
			$erp_resp_username=$_POST['erp_resp_username'];
			$erp_resp_password=$_POST['erp_resp_passwd'];
			$erp_resp_cpasswd=$_POST['erp_resp_cpasswd'];
			$erp_hidd_passwd=$_POST['erp_hidd_passwd'];
		
			$ocularSaveDataArr["erp_resp_username"] = $erp_resp_username;
			if($erp_resp_password!='' && $erp_resp_cpasswd!='') {
				$erp_resp_password = $erp_resp_password;
			}else if($erp_resp_password=='' && $erp_resp_cpasswd=='') {
				$erp_resp_password = $erp_resp_password;
			}
			
			$ocularSaveDataArr["erp_resp_imw_password"] = $erp_resp_password;
		}
	
        $query_string = "select id from resp_party where patient_id = '$pid'";
        $sql = imw_query($query_string);
        $resQryRes = imw_fetch_assoc($sql);

        $resp_party_id = $resQryRes['id'];

        if($resp_party_id > 0){
            UpdateRecords($resp_party_id,'id',$ocularSaveDataArr,'resp_party');		
        }
        else{
            $resp_party_id = AddRecords($ocularSaveDataArr,'resp_party');echo imw_error();
            if($_REQUEST['hid_create_acc_resp_party'] == "yes"){

                $dpr_pt_data1 = array();
                $dpr_pt_data1['ptfname'] 	= convertUcfirst($fname1);
                $dpr_pt_data1['ptlname'] 	= convertUcfirst($lname1);
                $dpr_pt_data1['ptdob'] 		= $date_convert1;
                $dpr_pt_data1['ptgender'] 	= $sex1;
                $dpr_pt_data1['ptzip'] 		= (inter_country() != "UK")?core_padd_char($postal_code1,5):$postal_code1;
                $dpr_pt_data1['ptzip_ext'] 	= $rzip_ext;

                $arr_patient_next_id = get_Next_PatientID($dpr_pt_data1);
                if($arr_patient_next_id['error']==''){
                    $resp_patient_next_id = $arr_patient_next_id['patient_id'];
                    $resp_patient_src_server = $arr_patient_next_id['src_server'];
                }else{
                    echo $arr_patient_next_id['error'];exit;
                }

                $arrRespNewSepAcc = array();
                $arrRespNewSepAcc["title"] = convertUcfirst($title1);
                $arrRespNewSepAcc["fname"] = convertUcfirst($fname1);
                $arrRespNewSepAcc["mname"] = convertUcfirst($mname1);
                $arrRespNewSepAcc["lname"] = convertUcfirst($lname1);
                $arrRespNewSepAcc["street"] = convertUcfirst($street1);
                $arrRespNewSepAcc["street2"] = convertUcfirst($street_emp);
                $arrRespNewSepAcc["city"] = convertUcfirst($city1);
                $arrRespNewSepAcc["state"] = ucwords($state2);			
                $arrRespNewSepAcc["postal_code"] = $postal_code1;
                $arrRespNewSepAcc["zip_ext"] = $rzip_ext;
                $arrRespNewSepAcc["country_code"] = $country_code;
                $arrRespNewSepAcc["ss"] = $ss1;
                $arrRespNewSepAcc["DOB"] = $date_convert1;
                $arrRespNewSepAcc["patientStatus"] = "Active";
                $arrRespNewSepAcc["sex"] = $sex1;			 			 
                $arrRespNewSepAcc["pid"] = $resp_patient_next_id;	
                $arrRespNewSepAcc["id"] = $resp_patient_next_id;
                $arrRespNewSepAcc["date"] = date('Y-m-d H:i:s');
                $arrRespNewSepAcc["created_by"] = $_SESSION['authId'];
                if($resp_patient_src_server>0){$arrRespNewSepAcc["src_server"] = $resp_patient_src_server;} 		
                //--- ADD NEW PATIENT (ability to create an account whenever a new Grantor/Responsible Party person is added)----
                AddRecords($arrRespNewSepAcc,"patient_data");
            }
        }	

        //--- UPLOAD RESPONSIBLE PARTY IMAGE ---

        // validate file content type
        if( isset($_FILES['userlic12']) && check_img_mime($_FILES['userlic12']['tmp_name']) ) {
            include_once($GLOBALS['fileroot']."/library/classes/SaveFile.php");	
            $save_file = new SaveFile($edit_patient_id);
            
            if( file_exists($_FILES['userlic12']['tmp_name'])) {
                $image_name = $save_file->copyfile($_FILES['userlic12']);
            }
        } 

        if(isset($image_name) && $image_name!='')
        {		
            $query_string = "select licence_image from resp_party where patient_id = '$pid'";
            $sql = imw_query($query_string);
            $imgQryRes = imw_fetch_assoc($sql);
            $oldimagename = $imgQryRes['licence_image'];
            $oldpath = ($oldimagename) ? $save_file->upDir.$oldimagename : '';
            @unlink($oldpath);

            imw_query("update resp_party set licence_image = '$image_name' where patient_id = '$pid'");
        }
    }

	//--- SAVE PATIENT PRIMARY INSURANCE COMPANY DATA ---
	$ins_case_type_arr = preg_split('/-/',$choose_prevcase);
	$ins_case_type_id = $ins_case_type_arr[0];
	$current_caseid = $ins_case_type_arr[3];
	if($ins_case_type_arr[1] == 0){
		$pri_ref_req = 'No';
		$sec_ref_req = 'No';
	}
	/*if($ins_case_type_arr[2] == 0){
		$pri_auth_req = 'No';
		$sec_auth_req = 'No';
	}*/
	
	if(count($ins_case_type_arr) == 3){
		$ins_case_data_arr = array();
		$ins_case_data_arr['ins_case_type'] = $ins_case_type_id;
		$ins_case_data_arr['patient_id'] = $edit_patient_id;
		$ins_case_data_arr['start_date'] = date('Y-m-d h:i:s');
		$ins_case_data_arr['case_status'] = 'Open';
		$chk_ins_case_qry=imw_query("select * from insurance_case where patient_id = '$edit_patient_id' and case_status = 'Open'
		and ins_case_type='$ins_case_type_id'");
		if(imw_num_rows($chk_ins_case_qry)==0){
			$current_caseid = AddRecords($ins_case_data_arr,"insurance_case");
		}else{
			$row_ins_case = imw_fetch_assoc($chk_ins_case_qry);
			$current_caseid = $row_ins_case['ins_caseid'];
		}
	}
	
	//--- ASSOCIATE CASE ID WITH APPOINTMENT ---
	$schedule_query = "update schedule_appointments set case_type_id = '$current_caseid'
				where sa_patient_id = '$edit_patient_id' and id = '$sch_id'";
	imw_query($schedule_query);
	if(trim($cbk_self_pay_provider) != ''){
		$pri_ins_data_arr = array();
		$pri_ins_data_arr['self_pay_provider'] = $cbk_self_pay_provider;
		$pri_ins_data_arr['type'] = 'primary';
		$pri_ins_data_arr['pid'] = $edit_patient_id;
		$pri_ins_data_arr['ins_caseid'] = $current_caseid;
		$pri_ins_data_arr['actInsComp'] = 1;
        if(isset($_POST['pri_lastName']) && $_POST['pri_lastName']!='' && isset($_POST['pri_subscriber_fname']) && $_POST['pri_subscriber_fname']!='' ) {
            $pri_ins_data_arr['subscriber_lname']=$_POST['pri_lastName'];
            $pri_ins_data_arr['subscriber_fname']=$_POST['pri_subscriber_fname'];
            $pri_ins_data_arr['subscriber_mname']=$_POST['pri_subscriber_mname'];
            $pri_ins_data_arr['subscriber_suffix']=$_POST['pri_suffix_rel'];
            $pri_ins_data_arr['subscriber_ss']=$_POST['pri_subscriber_ss'];
            $pri_ins_data_arr['subscriber_DOB']=getDateFormatDB($_POST['pri_subscriber_DOB']);
            if(isset($_POST['pri_subscriber_sex']) && $_POST['pri_subscriber_sex']!='')
            $pri_ins_data_arr['subscriber_sex']=$_POST['pri_subscriber_sex'];
            $pri_ins_data_arr['subscriber_relationship']=$_POST['pri_subscriber_relationship'];
            $pri_ins_data_arr['comments']=$_POST['pri_comments'];
            //$pri_paymentauth=$_POST['pri_paymentauth'];
            //$pri_signonfile=$_POST['pri_signonfile'];
        }
		
		if($insurance_primary_id != ''){
		
			UpdateRecords($insurance_primary_id,'id',$pri_ins_data_arr,'insurance_data');
		}
		else{
			
			//-------------In case of new patient--------------------// 
			$pri_ins_data_arr['subscriber_lname']=$_POST['lname'];
			$pri_ins_data_arr['subscriber_fname']=$_POST['fname'];
			$pri_ins_data_arr['subscriber_mname']=$_POST['mname'];
			$pri_ins_data_arr['subscriber_ss']=$_POST['ssnNumber'];
			$pri_ins_data_arr['subscriber_DOB']=getDateFormatDB($_POST['pt_dob']);
			$pri_ins_data_arr['subscriber_street']=$_POST['street'][$all_communication_index];
			$pri_ins_data_arr['subscriber_street_2']=$_POST['street2'][$all_communication_index];
			$pri_ins_data_arr['subscriber_postal_code']=(inter_country() != "UK")?core_padd_char($_POST['postal_code'][$all_communication_index],5):$_POST['postal_code'][$all_communication_index];
			$pri_ins_data_arr['zip_ext']=$_POST['zip_ext'][$all_communication_index];
			$pri_ins_data_arr['subscriber_city']=$_POST['city'][$all_communication_index];
			$pri_ins_data_arr['subscriber_state']=$_POST['state'][$all_communication_index];
			$pri_ins_data_arr['subscriber_country']=$_POST['country_code'][$all_communication_index];
			$pri_ins_data_arr['subscriber_phone']= $_POST['phone_home'];
			$pri_ins_data_arr['subscriber_biz_phone']=$_POST['phone_biz'];
			$pri_ins_data_arr['subscriber_mobile']=$_POST['phone_cell'];
			if($_POST['selGender']!='')
			$pri_ins_data_arr['subscriber_sex']=$_POST['selGender'];
			$pri_ins_data_arr['subscriber_relationship']="self";
			//--------------------------------------------------------//
			$pri_ins_data_arr['source'] = core_refine_user_input($_SERVER['HTTP_REFERER']);
            
            if(isset($_POST['pri_lastName']) && $_POST['pri_lastName']!='' && isset($_POST['pri_subscriber_fname']) && $_POST['pri_subscriber_fname']!='' ) {
                $pri_ins_data_arr['subscriber_lname']=$_POST['pri_lastName'];
                $pri_ins_data_arr['subscriber_fname']=$_POST['pri_subscriber_fname'];
                $pri_ins_data_arr['subscriber_mname']=$_POST['pri_subscriber_mname'];
                $pri_ins_data_arr['subscriber_suffix']=$_POST['pri_suffix_rel'];
                $pri_ins_data_arr['subscriber_ss']=$_POST['pri_subscriber_ss'];
                $pri_ins_data_arr['subscriber_DOB']=getDateFormatDB($_POST['pri_subscriber_DOB']);
                if(isset($_POST['pri_subscriber_sex']) && $_POST['pri_subscriber_sex']!='')
                $pri_ins_data_arr['subscriber_sex']=$_POST['pri_subscriber_sex'];
                $pri_ins_data_arr['subscriber_relationship']=$_POST['pri_subscriber_relationship'];
                $pri_ins_data_arr['comments']=$_POST['pri_comments'];
                //$pri_paymentauth=$_POST['pri_paymentauth'];
                //$pri_signonfile=$_POST['pri_signonfile'];
            }
            
			$insurance_primary_id = AddRecords($pri_ins_data_arr,"insurance_data");
			$patInsType="";
			$patInsType=$pri_ins_data_arr['type'];
		}
	}

	if(( (trim($insPriProv) != '') && ((int)$_POST['insPriProv_id'] > 0) && constant("EXTERNAL_INS_MAPPING")!="YES")  || (constant("EXTERNAL_INS_MAPPING")=="YES" && trim($insPriProv) != '')){
		
		$insPriProv_id_chk=$_POST['insPriProv_id'];
		$chk_pri_ins_qry = imw_query("select provider from insurance_data where ins_caseid = '$current_caseid'
						and pid = '$edit_patient_id' and type = 'primary' and provider = '$insPriProv_id_chk'
						and actInsComp = '1'");
						
		$pri_ins_data_arr = array();
		$pri_ins_data_arr['self_pay_provider'] = $cbk_self_pay_provider;
		$actIns = 1;
		$_POST['insPriActDt'] = getDateFormatDB($_POST['insPriActDt']);
		$curDate = date('Ymd');
		$activeDate = preg_replace("/[^0-9]/","",$_POST['insPriActDt']);
		/*if($activeDate > $curDate){
			$actIns = 0;
		}*/
		
		$_POST['insPriExpDt'] = getDateFormatDB($_POST['insPriExpDt']);
		$expireDate = preg_replace("/[^0-9]/","",$_POST['insPriExpDt']); 
		
		$pri_ins_data_arr['actInsComp'] = $actIns;
		$pri_ins_data_arr['actInsCompDate'] = $_POST['insPriActDt'];
		//scan
			if($_SESSION['scan_card_Primary']){
				$scan_card_Primary=$_SESSION['scan_card_Primary'];
				$scan_label_Primary=$_SESSION['scan_label_Primary'];
			}
			unset($_SESSION['scan_card_Primary']);
			unset($_SESSION['scan_label_Primary']);
		//scan
		
        if(isset($_POST['pri_lastName']) && $_POST['pri_lastName']!='' && isset($_POST['pri_subscriber_fname']) && $_POST['pri_subscriber_fname']!='' ) {
            $pri_ins_data_arr['subscriber_lname']=$_POST['pri_lastName'];
            $pri_ins_data_arr['subscriber_fname']=$_POST['pri_subscriber_fname'];
            $pri_ins_data_arr['subscriber_mname']=$_POST['pri_subscriber_mname'];
            $pri_ins_data_arr['subscriber_suffix']=$_POST['pri_suffix_rel'];
            $pri_ins_data_arr['subscriber_ss']=$_POST['pri_subscriber_ss'];
            $pri_ins_data_arr['subscriber_DOB']=getDateFormatDB($_POST['pri_subscriber_DOB']);
            if(isset($_POST['pri_subscriber_sex']) && $_POST['pri_subscriber_sex']!='')
            $pri_ins_data_arr['subscriber_sex']=$_POST['pri_subscriber_sex'];
            $pri_ins_data_arr['subscriber_relationship']=$_POST['pri_subscriber_relationship'];
            $pri_ins_data_arr['comments']=$_POST['pri_comments'];
            //$pri_paymentauth=$_POST['pri_paymentauth'];
            //$pri_signonfile=$_POST['pri_signonfile'];
        }
		if($insurance_primary_id != ''){
			UpdateRecords($insurance_primary_id,'id',$pri_ins_data_arr,'insurance_data');
			
		}
		else{
			$pri_ins_data_arr['source'] = core_refine_user_input($_SERVER['HTTP_REFERER']);
			if(imw_num_rows($chk_pri_ins_qry)==0){
				//-------------In case of new patient--------------------// 
				$pri_ins_data_arr['subscriber_lname']=$_POST['lname'];
				$pri_ins_data_arr['subscriber_fname']=$_POST['fname'];
				$pri_ins_data_arr['subscriber_mname']=$_POST['mname'];
				$pri_ins_data_arr['subscriber_ss']=$_POST['ssnNumber'];
				$pri_ins_data_arr['subscriber_DOB']=getDateFormatDB($_POST['pt_dob']);
				$pri_ins_data_arr['subscriber_street']=$_POST['street'][$all_communication_index];
				$pri_ins_data_arr['subscriber_street_2']=$_POST['street2'][$all_communication_index];
				$pri_ins_data_arr['subscriber_postal_code']=(inter_country() != "UK")?core_padd_char($_POST['postal_code'][$all_communication_index],5):$_POST['postal_code'][$all_communication_index];
				$pri_ins_data_arr['zip_ext']=$_POST['zip_ext'][$all_communication_index];
				$pri_ins_data_arr['subscriber_city']=$_POST['city'][$all_communication_index];
				$pri_ins_data_arr['subscriber_state']=$_POST['state'][$all_communication_index];
				$pri_ins_data_arr['subscriber_country']=$_POST['country_code'][$all_communication_index];
				$pri_ins_data_arr['subscriber_phone']= $_POST['phone_home'];
				$pri_ins_data_arr['subscriber_biz_phone']=$_POST['phone_biz'];
				$pri_ins_data_arr['subscriber_mobile']=$_POST['phone_cell'];
				if($_POST['selGender']!='')
				$pri_ins_data_arr['subscriber_sex']=$_POST['selGender'];
				$pri_ins_data_arr['subscriber_relationship']="self";
                
                if(isset($_POST['pri_lastName']) && $_POST['pri_lastName']!='' && isset($_POST['pri_subscriber_fname']) && $_POST['pri_subscriber_fname']!='' ) {
                    $pri_ins_data_arr['subscriber_lname']=$_POST['pri_lastName'];
                    $pri_ins_data_arr['subscriber_fname']=$_POST['pri_subscriber_fname'];
                    $pri_ins_data_arr['subscriber_mname']=$_POST['pri_subscriber_mname'];
                    $pri_ins_data_arr['subscriber_suffix']=$_POST['pri_suffix_rel'];
                    $pri_ins_data_arr['subscriber_ss']=$_POST['pri_subscriber_ss'];
                    $pri_ins_data_arr['subscriber_DOB']=getDateFormatDB($_POST['pri_subscriber_DOB']);
                    if(isset($_POST['pri_subscriber_sex']) && $_POST['pri_subscriber_sex']!='')
                    $pri_ins_data_arr['subscriber_sex']=$_POST['pri_subscriber_sex'];
                    $pri_ins_data_arr['subscriber_relationship']=$_POST['pri_subscriber_relationship'];
                    $pri_ins_data_arr['comments']=$_POST['pri_comments'];
                    //$pri_paymentauth=$_POST['pri_paymentauth'];
                    //$pri_signonfile=$_POST['pri_signonfile'];
                }
				//--------------------------------------------------------//
				$insurance_primary_id = AddRecords($pri_ins_data_arr,"insurance_data");
			}
			
		}

		//-----------BEGIN ENTERBNAL INSURANCE MAPPING------------
		$i1providerRCOCodeV = $i1providerRCOIdV = "";
		$i1provider = $_REQUEST['insPriProv_id'];
		if(trim($i1provider) == "" || trim($i1provider) == "undefined"){
			$insprovider = explode("*",$_REQUEST['insPriProv']);
			if(constant("EXTERNAL_INS_MAPPING") == "YES"){
				$arrTempProRCO = array();
				$arrTempProRCO = explode("-", $insprovider[0]);
				$i1providerRCOCodeV = trim($arrTempProRCO[0]);
				$i1provider = trim($insprovider[1]);
				$arrTempProRCO = array();
				$arrTempProRCO = explode("-", $i1provider);
				$i1provider = trim($arrTempProRCO[0]);
				$i1providerRCOIdV = trim($arrTempProRCO[1]);
			}
			else{
				$i1provider = trim($insprovider[1]);
			}
		}
		else{
			$i1providerRCOCodeV = $_REQUEST['i1providerRCOCode'];
			$i1providerRCOIdV = $_REQUEST['i1providerRCOId'];
		}
		//-----------END EXTERNAL INSURANCE MAPPING---------------
		
		$pri_ins_data_arr = array();
		if(constant("EXTERNAL_INS_MAPPING") == "YES"){
			if(strstr($_REQUEST['insPriProv'],"-") && trim($i1providerRCOCodeV)==''){
				list($i1providerRCOCodeV)=explode("-",$_REQUEST['insPriProv']);
			}
			if(strstr($i1provider,"-")){list($i1provider,$i1providerRCOIdV1)=explode("-",$i1provider);}
			if(trim($i1providerRCOIdV)=='' || $i1providerRCOIdV==0){
				$i1providerRCOIdV = $i1providerRCOIdV1;
			}
			$pri_ins_data_arr['provider'] = $i1provider;
		}else{
			$pri_ins_data_arr['provider'] = $_POST['insPriProv_id'];
		}
		$pri_ins_data_arr['rco_code'] = $i1providerRCOCodeV;
		$pri_ins_data_arr['rco_code_id'] = $i1providerRCOIdV;
		//
		$pri_ins_data_arr['policy_number'] = $_POST['insPriPolicy'];
		$pri_ins_data_arr['group_number'] = $_POST['insPriGroup'];
		$pri_ins_data_arr['copay'] = $_POST['insPriCopay'];
		$pri_ins_data_arr['co_ins'] = $_POST['insPriCoIns'];
		if(empty($_POST['insPriActDt']) == false){
			$pri_ins_data_arr['effective_date'] = $_POST['insPriActDt']." ".date("h:i:s");
		}
		else{
			$pri_ins_data_arr['effective_date'] = "";
		}
		if(empty($_POST['insPriExpDt']) == false){
			$pri_ins_data_arr['expiration_date'] = $_POST['insPriExpDt']." ".date("h:i:s");
			
			$activeDate = strtotime($_POST['insPriActDt']);
			$expDate = strtotime($_POST['insPriExpDt']);
						
			$todayDate = strtotime(date('Y-m-d'));
			if($expDate <= $todayDate){
				$pri_ins_data_arr['actInsComp'] = 0;
			}
			else{
				$pri_ins_data_arr['actInsComp'] = 1;
			}
		}
		else{
			$pri_ins_data_arr['expiration_date'] = "";
		}
		$pri_ins_data_arr['type'] = 'primary';
		$pri_ins_data_arr['pid'] = $edit_patient_id;
		$pri_ins_data_arr['ins_caseid'] = $current_caseid;
		$pri_ins_data_arr['referal_required'] = $pri_ref_req;
		$pri_ins_data_arr['auth_required'] = $_POST['pri_auth_req'];
		if($insurance_primary_id != ''){
			UpdateRecords($insurance_primary_id,'id',$pri_ins_data_arr,'insurance_data');
		}
	
		//--- PRIMARY REFERRAL REQUIRED DATA ----
		if(trim($pri_ref_req) == 'Yes' and $pri_ref_phy != ''){			
			if($pri_ref_phy != '' && ($pri_ref_phy_id=='' || $pri_ref_phy_id=='0')){
				$pri_ref_phy_id = get_reffering_physician_id($pri_ref_phy);
			}
			$pri_ref_req_arr = array();
			$pri_ref_req_arr["patient_id"] = $edit_patient_id;
			$pri_ref_req_arr["reff_by"] = $pri_ref_phy;
			$pri_ref_visits_arr = preg_split("/\//",$pri_ref_visits);
			$reff_used = 0;
			if(count($pri_ref_visits_arr) > 1){
				$reff_used = $pri_ref_visits_arr[1];
				$no_of_reffs = $pri_ref_visits_arr[0] - $reff_used;
			}
			else{
				$no_of_reffs = $pri_ref_visits_arr[0];
			}
			$pri_ref_req_arr["no_of_reffs"] = $no_of_reffs;
			$pri_ref_req_arr["reff_used"] = $reff_used;
			$pri_ref_req_arr["reff_type"] = 1;
			$pri_ref_req_arr["reffral_no"] = $pri_ref_number;
			$pri_ref_req_arr["insCaseid"] = $current_caseid;
			$pri_ref_req_arr["reff_phy_id"] = $pri_ref_phy_id;
			$pri_ref_req_arr["ins_provider"] = $_POST['insPriProv_id'];
			$pri_ref_req_arr["ins_data_id"] = $insurance_primary_id;
			
			$_POST['pri_ref_stDt'] = getDateFormatDB($_POST['pri_ref_stDt']);
			$pri_ref_req_arr["effective_date"] = preg_replace("/[^0-9]/","",$_POST['pri_ref_stDt']);

			$_POST['pri_ref_enDt'] = getDateFormatDB($_POST['pri_ref_enDt']);
			$pri_ref_req_arr["end_date"] = preg_replace("/[^0-9]/","",$_POST['pri_ref_enDt']);
			
			if(trim($pri_reff_id) == ''){
				$pri_reff_id = AddRecords($pri_ref_req_arr,'patient_reff');
			}
			else{
				UpdateRecords($pri_reff_id,"reff_id",$pri_ref_req_arr,'patient_reff');
			}
		}
	
		if($_POST['AuthPriNumber'] !="" and $_POST['pri_auth_req']=="Yes"){
			$pri_auth_data_arr = array();
			$auth_pri_chk=$_POST['auth_pri_id'];
			$pri_auth_data_arr['auth_name'] = $_POST['AuthPriNumber'];
			$pri_auth_data_arr['AuthAmount'] = $_POST['AuthPriAmount'];
			$pri_auth_data_arr['ins_case_id'] = $current_caseid;
			$pri_auth_data_arr['ins_provider'] = $_POST['insPriProv_id'];
			$pri_auth_data_arr['ins_data_id'] = $insurance_primary_id;
			$pri_auth_data_arr['patient_id'] = $edit_patient_id;
			$pri_auth_data_arr['ins_type'] = '1';
			$pri_auth_data_arr['auth_date'] = getDateFormatDB($pri_auth_date);
			
			//---------BEGIN INSURANCE AUTH END DATE AND VISIT CHANGES------------------------------
			$pri_auth_data_arr['end_date'] 		= getDateFormatDB($pri_auth_date_end);
			$arr_auth_visit_value_pri 			= explode("/",$_POST['pri_auth_visits']);
			$auth_no_of_reffs_pri 				= $arr_auth_visit_value_pri[0];
			$auth_reff_used_pri 				= $arr_auth_visit_value_pri[1];
			$pri_auth_data_arr['no_of_reffs'] 	= $auth_no_of_reffs_pri;
			$pri_auth_data_arr['reff_used'] 	= $auth_reff_used_pri;
			//---------END INSURANCE AUTH END DATE AND VISIT CHANGES------------------------------

			$auth_query = "select auth_name from patient_auth where a_id = '$auth_pri_chk' and auth_status='0' and ins_type='1'";
			$authQryRes = mysqlifetchdata($auth_query);
			
			if(count($authQryRes)>0){
				UpdateRecords($auth_pri_chk,'a_id',$pri_auth_data_arr,'patient_auth');
			}else{
				$pri_auth_data_arr['cur_date'] = date('Y-m-d');
				$pri_auth_data_arr['auth_operator'] = $operator_id;
				$auth_pri_chk = AddRecords($pri_auth_data_arr,"patient_auth");
			}
			$schedule_query = "update schedule_appointments set auth_pri_id = '$auth_pri_chk' where 
							sa_patient_id = '$edit_patient_id' and id = '$sch_id'";
			imw_query($schedule_query);
		}
	}
	//--- SAVE PATIENT SECONDARY INSURANCE COMPANY DATA ---
	//if((trim($insSecProv) != '') && ((int)$_POST['insSecProv_id'] > 0)){
	if(( (trim($insSecProv) != '') && ((int)$_POST['insSecProv_id'] > 0) && constant("EXTERNAL_INS_MAPPING")!="YES")  || (constant("EXTERNAL_INS_MAPPING")=="YES" && trim($insSecProv) != '')){
		$insSecProv_id_chk=$_POST['insSecProv_id'];
		$chk_sec_ins_qry = imw_query("select provider from insurance_data where ins_caseid = '$current_caseid'
						and pid = '$edit_patient_id' and type = 'secondary' and provider = '$insSecProv_id_chk'
						and actInsComp = '1'");
						
		$sec_ins_data_arr = array();
		$_POST['insSecActDt'] = getDateFormatDB($_POST['insSecActDt']);
		$curDate = date('Ymd');
		$activeDate = preg_replace("/[^0-9]/","",$_POST['insSecActDt']);
		$actIns = 1;
		/*if($activeDate > $curDate){
			$actIns = 0;
		}*/

		$_POST['insSecExpDt'] = getDateFormatDB($_POST['insSecExpDt']);
		$ExpireDate = preg_replace("/[^0-9]/","",$_POST['insSecExpDt']);

		$sec_ins_data_arr['actInsComp'] = $actIns;
		$sec_ins_data_arr['actInsCompDate'] = $_POST['insSecActDt'];
		//scan
			if($_SESSION['scan_card_Secondary']){
				$scan_card_Secondary=$_SESSION['scan_card_Secondary'];
				$scan_label_Secondary=$_SESSION['scan_label_Secondary'];
			}	
			unset($_SESSION['scan_card_Secondary']);
			unset($_SESSION['scan_label_Secondary']);
		//scan
            
        if(isset($_POST['sec_lastName']) && $_POST['sec_lastName']!='' && isset($_POST['sec_subscriber_fname']) && $_POST['sec_subscriber_fname']!='' ) {
            $sec_ins_data_arr['subscriber_lname']=$_POST['sec_lastName'];
            $sec_ins_data_arr['subscriber_fname']=$_POST['sec_subscriber_fname'];
            $sec_ins_data_arr['subscriber_mname']=$_POST['sec_subscriber_mname'];
            $sec_ins_data_arr['subscriber_suffix']=$_POST['sec_suffix_rel'];
            $sec_ins_data_arr['subscriber_ss']=$_POST['sec_subscriber_ss'];
            $sec_ins_data_arr['subscriber_DOB']=getDateFormatDB($_POST['sec_subscriber_DOB']);
            if($_POST['sec_subscriber_sex']!='')
            $sec_ins_data_arr['subscriber_sex']=$_POST['sec_subscriber_sex'];
            $sec_ins_data_arr['subscriber_relationship']=$_POST['sec_subscriber_relationship'];
            $sec_ins_data_arr['comments']=$_POST['sec_comments'];
            //$sec_paymentauth=$_POST['sec_paymentauth'];
            //$sec_signonfile=$_POST['sec_signonfile'];
        }
                
		if($insurance_secondary_id != ''){
			UpdateRecords($insurance_secondary_id,'id',$sec_ins_data_arr,'insurance_data');
			
		}
		else{
			$sec_ins_data_arr['source'] = core_refine_user_input($_SERVER['HTTP_REFERER']);
			if(imw_num_rows($chk_sec_ins_qry)==0){
				//-------------In case of new patient--------------------// 
				$sec_ins_data_arr['subscriber_lname']=$_POST['lname'];
				$sec_ins_data_arr['subscriber_fname']=$_POST['fname'];
				$sec_ins_data_arr['subscriber_mname']=$_POST['mname'];
				$sec_ins_data_arr['subscriber_ss']=$_POST['ssnNumber'];
				$sec_ins_data_arr['subscriber_DOB']=getDateFormatDB($_POST['pt_dob']);
				$sec_ins_data_arr['subscriber_street']=$_POST['street'][$all_communication_index];
				$sec_ins_data_arr['subscriber_street_2']=$_POST['street2'][$all_communication_index];
				$sec_ins_data_arr['subscriber_postal_code']=(inter_country() != "UK")?core_padd_char($_POST['postal_code'][$all_communication_index],5):$_POST['postal_code'][$all_communication_index];
				$sec_ins_data_arr['zip_ext']=$_POST['zip_ext'][$all_communication_index];
				$sec_ins_data_arr['subscriber_city']=$_POST['city'][$all_communication_index];
				$sec_ins_data_arr['subscriber_state']=$_POST['state'][$all_communication_index];
				$sec_ins_data_arr['subscriber_country']=$_POST['country_code'][$all_communication_index];
				$sec_ins_data_arr['subscriber_phone']= $_POST['phone_home'];
				$sec_ins_data_arr['subscriber_biz_phone']=$_POST['phone_biz'];
				$sec_ins_data_arr['subscriber_mobile']=$_POST['phone_cell'];
				if($_POST['selGender']!='')
				$sec_ins_data_arr['subscriber_sex']=$_POST['selGender'];
				$sec_ins_data_arr['subscriber_relationship']="self";
				//--------------------------------------------------------//
                if(isset($_POST['sec_lastName']) && $_POST['sec_lastName']!='' && isset($_POST['sec_subscriber_fname']) && $_POST['sec_subscriber_fname']!='' ) {
                    $sec_ins_data_arr['subscriber_lname']=$_POST['sec_lastName'];
                    $sec_ins_data_arr['subscriber_fname']=$_POST['sec_subscriber_fname'];
                    $sec_ins_data_arr['subscriber_mname']=$_POST['sec_subscriber_mname'];
                    $sec_ins_data_arr['subscriber_suffix']=$_POST['sec_suffix_rel'];
                    $sec_ins_data_arr['subscriber_ss']=$_POST['sec_subscriber_ss'];
                    $sec_ins_data_arr['subscriber_DOB']=getDateFormatDB($_POST['sec_subscriber_DOB']);
                    if($_POST['sec_subscriber_sex']!='')
                    $sec_ins_data_arr['subscriber_sex']=$_POST['sec_subscriber_sex'];
                    $sec_ins_data_arr['subscriber_relationship']=$_POST['sec_subscriber_relationship'];
                    $sec_ins_data_arr['comments']=$_POST['sec_comments'];
                    //$sec_paymentauth=$_POST['sec_paymentauth'];
                    //$sec_signonfile=$_POST['sec_signonfile'];
                }
                
				$insurance_secondary_id = AddRecords($sec_ins_data_arr,"insurance_data");
			}
		}
		//-----------BEGIN ENTERBNAL INSURANCE MAPPING------------
		
		$i2providerRCOCodeV = $i2providerRCOIdV = "";
		$i2provider=$_REQUEST['insSecProv_id'];
		if(trim($i2provider) == "" || trim($i2provider) =="undefined"){
			$secInsCompVal = explode("*",$_REQUEST['insSecProv']);
			if(constant("EXTERNAL_INS_MAPPING") == "YES"){
				$arrTempProRCO = array();
				$arrTempProRCO = explode("-", $secInsCompVal[0]);
				$i2providerRCOCodeV = trim($arrTempProRCO[0]);
				$i2provider = trim($secInsCompVal[1]);
				$arrTempProRCO = array();
				$arrTempProRCO = explode("-", $i2provider);
				$i2provider = trim($arrTempProRCO[0]);
				$i2providerRCOIdV = trim($arrTempProRCO[1]);
			}
			else{
				$i2provider = trim($secInsCompVal[1]);
			}
		}
		else{
			$i2providerRCOCodeV = $_REQUEST["i2providerRCOCode"];
			$i2providerRCOIdV = $_REQUEST["i2providerRCOId"];
		}
		//-----------END EXTERNAL INSURANCE MAPPING---------------
		$sec_ins_data_arr = array();
		if(constant("EXTERNAL_INS_MAPPING") == "YES"){
			if(strstr($_REQUEST['insSecProv'],"-") && trim($i2providerRCOCodeV)==''){
				list($i2providerRCOCodeV)=explode("-",$_REQUEST['insSecProv']);
			}
			if(strstr($i2provider,"-")){list($i2provider,$i2providerRCOIdV1)=explode("-",$i2provider);}
			if(trim($i2providerRCOIdV)=='' || $i2providerRCOIdV==0){
				$i2providerRCOIdV = $i2providerRCOIdV1;
			}
			$sec_ins_data_arr['provider'] = $i2provider;
		}else{
			$sec_ins_data_arr['provider'] = $_POST['insSecProv_id'];
		}
		$sec_ins_data_arr['rco_code'] = $i2providerRCOCodeV;
		$sec_ins_data_arr['rco_code_id'] = $i2providerRCOIdV;
		
		$sec_ins_data_arr['policy_number'] = $_POST['insSecPolicy'];
		$sec_ins_data_arr['group_number'] = $_POST['insSecGroup'];
		$sec_ins_data_arr['copay'] = $_POST['insSecCopay'];		
		if(empty($_POST['insSecActDt']) == false){
			$sec_ins_data_arr['effective_date'] = $_POST['insSecActDt']." ".date("h:i:s");
		}
		else{
			$sec_ins_data_arr['effective_date'] = "";
		}
		if(empty($_POST['insSecExpDt']) == false){
			$sec_ins_data_arr['expiration_date'] = $_POST['insSecExpDt']." ".date("h:i:s");
			
			$activeDate = strtotime($_POST['insSecActDt']);
			$expDate = strtotime($_POST['insSecExpDt']);
						
			$todayDate = strtotime(date('Y-m-d'));
			if($expDate <= $todayDate){
				$sec_ins_data_arr['actInsComp'] = 0;
			}
			else{
				$sec_ins_data_arr['actInsComp'] = 1;
			}
		}
		else{
			$sec_ins_data_arr['expiration_date'] = "";
		}
		
		$sec_ins_data_arr['type'] = 'secondary';
		$sec_ins_data_arr['pid'] = $edit_patient_id;
		$sec_ins_data_arr['ins_caseid'] = $current_caseid;
		$sec_ins_data_arr['referal_required'] = $sec_ref_req;
		$sec_ins_data_arr['auth_required'] = $_POST['sec_auth_req'];

		if($insurance_secondary_id != ''){
			UpdateRecords($insurance_secondary_id,'id',$sec_ins_data_arr,'insurance_data');
		}
		
		//--- SECONDARY REFERRAL REQUIRED DATA ----
		if(trim($sec_ref_req) == 'Yes' and $sec_ref_phy != ''){			
			if($sec_ref_phy != ''){
				$sec_ref_phy_id = get_reffering_physician_id($sec_ref_phy);
			}
			$sec_ref_req_arr = array();
			$sec_ref_req_arr["patient_id"] = $edit_patient_id;
			$sec_ref_req_arr["reff_by"] = $sec_ref_phy;
			$sec_ref_visits_arr = preg_split("/\//",$sec_ref_visits);
			$reff_used = 0;
			if(count($sec_ref_visits_arr) > 1){
				$reff_used = $sec_ref_visits_arr[1];
				$no_of_reffs = $sec_ref_visits_arr[0] - $reff_used;
			}
			else{
				$no_of_reffs = $sec_ref_visits_arr[0];
			}
			$sec_ref_req_arr["no_of_reffs"] = $no_of_reffs;
			$sec_ref_req_arr["reff_used"] = $reff_used;
			$sec_ref_req_arr["reff_type"] = 2;
			$sec_ref_req_arr["reffral_no"] = $sec_ref_number;
			$sec_ref_req_arr["insCaseid"] = $current_caseid;
			$sec_ref_req_arr["reff_phy_id"] = $sec_ref_phy_id;
			$sec_ref_req_arr["ins_provider"] = $_POST['insSecProv_id'];
			$sec_ref_req_arr["ins_data_id"] = $insurance_secondary_id;

			$_POST['sec_ref_stDt'] = getDateFormatDB($_POST['sec_ref_stDt']);
			$sec_ref_req_arr["effective_date"] = $_POST['sec_ref_stDt'];
			//$sec_ref_req_arr["effective_date"] = preg_replace("/[^0-9]/","",$_POST['sec_ref_stDt']);

			$_POST['sec_ref_enDt'] = getDateFormatDB($_POST['sec_ref_enDt']);
			$sec_ref_req_arr["end_date"] = $_POST['sec_ref_enDt'];
			//$sec_ref_req_arr["end_date"] = preg_replace("/[^0-9]/","",$_POST['sec_ref_enDt']);
			if(trim($sec_reff_id) == ''){
				AddRecords($sec_ref_req_arr,'patient_reff');
			}
			else{
				UpdateRecords($sec_reff_id,"reff_id",$sec_ref_req_arr,'patient_reff');
			}
			
		}
		
		if($_POST['AuthSecNumber'] !="" && $_POST['sec_auth_req'] == "Yes"){
			$sec_auth_data_arr = array();
			$auth_sec_chk=$_POST['auth_sec_id'];
			$sec_auth_data_arr['auth_name'] = $_POST['AuthSecNumber'];
			$sec_auth_data_arr['AuthAmount'] = $_POST['AuthSecAmount'];
			$sec_auth_data_arr['ins_case_id'] = $current_caseid;
			$sec_auth_data_arr['ins_provider'] = $_POST['insSecProv_id'];
			$sec_auth_data_arr['ins_data_id'] = $insurance_secondary_id;
			$sec_auth_data_arr['patient_id'] = $edit_patient_id;
			$sec_auth_data_arr['ins_type'] = '2';
			$sec_auth_data_arr['auth_date'] = getDateFormatDB($sec_auth_date);
			
			//---------BEGIN INSURANCE AUTH END DATE AND VISIT CHANGES------------------------------
			$sec_auth_data_arr['end_date'] = getDateFormatDB($sec_auth_date_end);
			$arr_auth_visit_value_sec 			= explode("/",$_POST['sec_auth_visits']);
			$auth_no_of_reffs_sec 				= $arr_auth_visit_value_sec[0];
			$auth_reff_used_sec					= $arr_auth_visit_value_sec[1];
			$sec_auth_data_arr['no_of_reffs'] 	= $auth_no_of_reffs_sec;
			$sec_auth_data_arr['reff_used'] 	= $auth_reff_used_sec;
			//---------END INSURANCE AUTH END DATE AND VISIT CHANGES------------------------------
			
			$auth_query = "select auth_name from patient_auth where a_id = '$auth_sec_chk' and auth_status='0'  and ins_type='2'";
			$authQryRes = mysqlifetchdata($auth_query);
			if(count($authQryRes)>0){
				UpdateRecords($auth_sec_chk,'a_id',$sec_auth_data_arr,'patient_auth');
			}else{
				$sec_auth_data_arr['cur_date'] = date('Y-m-d');
				$sec_auth_data_arr['auth_operator'] = $operator_id;
				$auth_sec_chk=AddRecords($sec_auth_data_arr,"patient_auth");
			}
			$schedule_query = "update schedule_appointments set auth_sec_id = '$auth_sec_chk' where 
						sa_patient_id = '$edit_patient_id' and id = '$sch_id'";
			imw_query($schedule_query);
		}
	}
	

	//START AUDIT		
		
	////patient_data fields
	$patientDataFields = $insCaseDataFields = $insDataFields = $patientRefDataFields = $patientAuthDataFields = array(); 
	$patientDataFields = make_field_type_array("patient_data");
	if($patientDataFields == 1146){
		$patientDataError = "Error : Table 'patient_data' doesn't exist";
	}
	
	$insCaseDataFields = make_field_type_array("insurance_case");
	if($insCaseDataFields == 1146){
		$insCaseDataError = "Error : Table 'insurance_case' doesn't exist";
	}
	$insDataFields = make_field_type_array("insurance_data");
	if($insDataFields == 1146){
		$insDataError = "Error : Table 'insurance_data' doesn't exist";
	}
	$patientRefDataFields = make_field_type_array("patient_reff");
	if($patientRefDataFields == 1146){
		$patientRefDataError = "Error : Table 'patient_reff' doesn't exist";
	}
	$patientAuthDataFields = make_field_type_array("patient_auth");
	if($patientAuthDataFields == 1146){
		$patientAuthDataError = "Error : Table 'patient_data' doesn't exist";
	}
	
	
	$table = array("patient_data","insurance_data");
	$error = array($patientDataError,$insDataError);
	$mergedArray = merging_array($table,$error);
	$policyStatus = 0;
	$policyStatus = (int)$_SESSION['AUDIT_POLICIES']['Patient_record_Created_Viewed_Updated'];
	$arrAuditTrail = array();
	
	if($policyStatus == 1){
		$arrAuditTrail = patient_audit_popup();   
		auditTrail($arrAuditTrail,$mergedArray,0,0,0);
	}
	//END AUDIT		

    /* MVE PORTAL CREATE NEW PATIENT */
	$erp_error=array();
    if( isERPPortalEnabled() ) {
		try {
			include_once($GLOBALS['srcdir']."/erp_portal/patients.php");
			$obj_patients = new Patients();
			$patientDetails = $obj_patients->addUpdatePatient($edit_patient_id);
		} catch(Exception $e) {
			$erp_error[]='Unable to connect to ERP Portal';
		}
    }

	/*********NEW HL7 ENGINE START************/
	require_once(dirname(__FILE__)."/../../../hl7sys/api/class.HL7Engine.php");
	$objHL7Engine = new HL7Engine();
	$objHL7Engine->application_module = 'demographics';
	if(isset($HL7newPtMode) && $HL7newPtMode == true)$objHL7Engine->msgSubType = 'add_patient'; else $objHL7Engine->msgSubType = 'update_patient';
	$objHL7Engine->source_id = $edit_patient_id;
	$objHL7Engine->generateHL7();
	/*********NEW HL7 ENGINE END*************/

	/* Purpose: Make ADT hl7 messages for ZEISS*/
	if(defined('HL7_ADT_GENERATION') && constant('HL7_ADT_GENERATION') === true && (defined('HL7_ADT_GENERATION_OLD') && constant('HL7_ADT_GENERATION_OLD') === true)){
		
		if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('boston'))){
			$remote_Facs = check_remote_facility();
			if(is_array($remote_Facs) && $remote_Facs != false){
				require_once( dirname(__FILE__).'/../../../hl7sys/old/CLS_makeHL7.php');
				$makeHL7 = new makeHL7();
			}
		}else{
			require_once( dirname(__FILE__).'/../../../hl7sys/old/CLS_makeHL7.php');
			$makeHL7 = new makeHL7();
		}
		//logging HL7 messages to send to IDX & Forum.
		if($_REQUEST['isNewPatient'] == 'yes'){
			if($makeHL7){$makeHL7->log_HL7_message($edit_patient_id,'Add_New_Patient');}
		}else{
			if($makeHL7){$makeHL7->log_HL7_message($edit_patient_id,'Update_Patient');}	
		}
	}else if( defined('HL7_ADT_GENERATION') && constant('HL7_ADT_GENERATION') === true ){
		require_once( dirname(__FILE__).'/../../../hl7sys/hl7GP/hl7FeedData.php');
		$hl7 = new hl7FeedData();

		$hl7->PD['id'] = $edit_patient_id;
		
		if($source == "demographics")
		{
			$hl7->msgtypes['ADT']['trigger_event'] = "A04";
			$hl7->msgtype = "ADD_NEW_PATIENT";
		}
		else{
			$hl7->msgtype = "UPDATE_PATIENT";
		}

		if( isset($GLOBALS['HL_RECEIVING']) && is_array($GLOBALS['HL_RECEIVING']) )
		{
			$hl7RecApp = ( isset($GLOBALS['HL_RECEIVING']['APPLICATION']) ) ? $GLOBALS['HL_RECEIVING']['APPLICATION'] : '';
			$hl7RecFac = ( isset($GLOBALS['HL_RECEIVING']['FACILITY']) ) ? $GLOBALS['HL_RECEIVING']['FACILITY'] : '';
			$hl7->setReceivingFacility($hl7RecApp, $hl7RecFac);
		}

		$hl7->addEVN($hl7->msgtypes['ADT']['trigger_event']);

		if( isset($GLOBALS['HL7_ADT_SEGMENTS']) && is_array($GLOBALS['HL7_ADT_SEGMENTS']) )
		{
			foreach( $GLOBALS['HL7_ADT_SEGMENTS'] as $segment )
			{
				$hl7->insertSegment($segment, 'ADT');
			}
		}

		$hl7->log_message();
	}
	/*End code*/
	
	//--- CHECK PAYMENTS ---
	if((count($check_in_out_pay) > 0 || $ci_comments!="" || $edit_payment_tbl_id_cash>0 || $edit_payment_tbl_id_check>0 || $edit_payment_tbl_id_eft>0 || $edit_payment_tbl_id_mo>0 || $edit_payment_tbl_id_card>0) && $bool_save_payments){
		$payment_data_arr = array();
		$payment_types = array('Cash'=>'tot_cash_payment', 'Check'=>'tot_check_payment', 'Credit Card'=>'tot_card_payment','EFT'=>'tot_eft_payment','Money Order'=>'tot_mo_payment');
		$payment_data_arr['patient_id'] = $edit_patient_id;
		$payment_data_arr['sch_id'] = $sch_id;
		$payment_data_arr['total_charges'] = $total_charges_txt;
		$payment_data_arr['check_no'] = $checkNo;
		$payment_data_arr['cc_type'] = $creditCardCo;
		$payment_data_arr['cc_no'] = $cCNo;
		$payment_data_arr['cc_expire_date'] = $CCexpireDate;
		$payment_data_arr['del_status'] = 0;
		$payment_data_arr['payment_type'] = 'checkin';
		$payment_data_arr['ci_comments'] = $ci_comments;
		foreach($payment_types as $key=>$val){
			$payment_data_arr['payment_method'] = $key;
			$payment_data_arr['total_payment'] = $$val;
			if($key == 'Cash'){
				$payment_data_arr['check_no'] = '';
				$payment_data_arr['cc_type'] = '';
				$payment_data_arr['cc_no'] = '';
				$payment_data_arr['cc_expire_date'] = '';
                $payment_data_arr['log_referenceNumber'] = '';
                $payment_data_arr['tsys_transaction_id'] = '';
				if($edit_payment_tbl_id_cash == ''){
					$payment_data_arr["created_on"] = date("Y-m-d");
					$payment_data_arr["created_time"] = date('h:i A');
					$payment_data_arr["created_by"] = $_SESSION['authId'];
					$edit_payment_tbl_id_cash = Addrecords($payment_data_arr,'check_in_out_payment');
				}else if(core_check_privilege(array('priv_edit_financials'))){
						$payment_data_arr["modified_on"] = date("Y-m-d");
						$payment_data_arr["modified_time"] = date('h:i A');
						$payment_data_arr["modified_by"] = $_SESSION['authId'];
						Updaterecords($edit_payment_tbl_id_cash,'payment_id',$payment_data_arr,'check_in_out_payment');
				}
			}
			if($key == 'Check' or $key == 'EFT' or $key == 'Money Order'){
				$payment_data_arr['check_no'] = $checkNo;
				$payment_data_arr['cc_type'] = '';
				$payment_data_arr['cc_no'] = '';
				$payment_data_arr['cc_expire_date'] = '';
                $payment_data_arr['log_referenceNumber'] = '';
                $payment_data_arr['tsys_transaction_id'] = '';
				if(trim($edit_payment_tbl_id_check)=='' || trim($edit_payment_tbl_id_eft)=='' || trim($edit_payment_tbl_id_mo)==''){
					$payment_data_arr["created_on"] = date("Y-m-d");
					$payment_data_arr["created_time"] = date('h:i A');
					$payment_data_arr["created_by"] = $_SESSION['authId'];
					if($key == 'Check'){
						$edit_payment_tbl_id_check = Addrecords($payment_data_arr,'check_in_out_payment');
					}
					else if($key == 'EFT'){
						$edit_payment_tbl_id_eft = Addrecords($payment_data_arr,'check_in_out_payment');
					}else if($key == 'Money Order'){
						$edit_payment_tbl_id_mo = Addrecords($payment_data_arr,'check_in_out_payment');
					}
				}else if(core_check_privilege(array('priv_edit_financials'))){
					$payment_data_arr["modified_on"] = date("Y-m-d");
					$payment_data_arr["modified_time"] = date('h:i A');
					$payment_data_arr["modified_by"] = $_SESSION['authId'];
					if($key == 'Check'){
						Updaterecords($edit_payment_tbl_id_check,'payment_id',$payment_data_arr,'check_in_out_payment');
					}
					else if($key == 'EFT'){
						Updaterecords($edit_payment_tbl_id_eft,'payment_id',$payment_data_arr,'check_in_out_payment');
					}
					else if($key == 'Money Order'){
						Updaterecords($edit_payment_tbl_id_mo,'payment_id',$payment_data_arr,'check_in_out_payment');
					}
				}
			}
			if($key == 'Credit Card'){
                $payment_data_arr['log_referenceNumber'] = $log_referenceNumber;
                $payment_data_arr['tsys_transaction_id'] = $tsys_transaction_id;
				$payment_data_arr['cc_type'] = $creditCardCo;
				$payment_data_arr['cc_no'] = $cCNo;
				$payment_data_arr['cc_expire_date'] = $CCexpireDate;
                if(isset($card_details_str_id) && $card_details_str_id!=''){
                    $card_details_arr=explode('~~',trim($card_details_str_id));
                    $payment_data_arr['cc_type']=$card_details_arr[0];
                    $payment_data_arr['cc_no']=$card_details_arr[1];
                    $payment_data_arr['cc_expire_date']=$card_details_arr[2];
                }
				$payment_data_arr['check_no'] = '';
				if($edit_payment_tbl_id_card == ''){
					$payment_data_arr["created_on"] = date("Y-m-d");
					$payment_data_arr["created_time"] = date('h:i A');
					$payment_data_arr["created_by"] = $_SESSION['authId'];
					$edit_payment_tbl_id_card = Addrecords($payment_data_arr,'check_in_out_payment');
				}else if(core_check_privilege(array('priv_edit_financials'))){
					$payment_data_arr["modified_on"] = date("Y-m-d");
					$payment_data_arr["modified_time"] = date('h:i A');
					$payment_data_arr["modified_by"] = $_SESSION['authId'];
					Updaterecords($edit_payment_tbl_id_card,'payment_id',$payment_data_arr,'check_in_out_payment');
				}
			}
		}
		
		if($edit_payment_tbl_id_cash > 0 || $edit_payment_tbl_id_check > 0 || $edit_payment_tbl_id_card > 0 || $edit_payment_tbl_id_eft > 0 || $edit_payment_tbl_id_mo > 0){
			//--- PROCEDURE PAYMENT DETAILS TABLES ----
			$ietm_id_arr = array_keys($chk_payment_detail_arr);
			for($i=0;$i<count($ietm_id_arr);$i++){
				$item_id = $ietm_id_arr[$i];
				$req_pay_method = 'pay_method_'.($item_id);
				$selct_pay_method = $_REQUEST[$req_pay_method];
				$pay_detail_arr = array();
				if($selct_pay_method=='Cash'){
					$pay_detail_arr["payment_id"] = $edit_payment_tbl_id_cash;
				}
				if($selct_pay_method=='Check' or $selct_pay_method == 'EFT' or $selct_pay_method == 'Money Order'){
					$pay_detail_arr["payment_id"] = $edit_payment_tbl_id_check;
				}
				if($selct_pay_method=='Credit Card'){
					$pay_detail_arr["payment_id"] = $edit_payment_tbl_id_card;
				}
				if($selct_pay_method=='EFT'){
					$pay_detail_arr["payment_id"] = $edit_payment_tbl_id_eft;
				}
				if($selct_pay_method=='Money Order'){
					$pay_detail_arr["payment_id"] = $edit_payment_tbl_id_mo;
				}
				$pay_detail_arr["item_id"] = $item_id;
				if($_POST['copay_dilated_'.$item_id]){
					$copay_type=1;
				}else if($_POST['copay_non_dilated_'.$item_id]){
					$copay_type=2;
				}else if($_POST['copay_test_dilated_'.$item_id]){
					$copay_type=1;
				}else if($_POST['copay_test_non_dilated_'.$item_id]){
					$copay_type=2;
				}else{
					$copay_type=0;
				}
				
				$pay_detail_arr["item_charges"] = preg_replace('/[\$,]/','',$_POST["item_charges_".$item_id]);
				$pay_detail_arr["item_payment"] = preg_replace('/[\$,]/','',$_POST["item_pay_".$item_id]);
				$pay_detail_arr["payment_type"] = 'checkin';
				//--- GET ALREADY EXISTS PAYMENT DETAILS --
				$payment_detail_id = $chk_payment_detail_arr[$item_id];
				if(in_array($item_id,$check_in_out_pay) === true){
					if($payment_detail_id == ''){
						$payment_detail_id = Addrecords($pay_detail_arr,'check_in_out_payment_details');
					}
					else if(core_check_privilege(array('priv_edit_financials'))){
						$item_charges = preg_replace('/[\$,]/','',$_POST["item_charges_".$item_id]);
						$item_payment = preg_replace('/[\$,]/','',$_POST["item_pay_".$item_id]);
						
						$up_checkIn=imw_query("update check_in_out_payment_details set 
									payment_id='".$pay_detail_arr["payment_id"]."',item_id='$item_id', 
									item_charges='$item_charges',item_payment='$item_payment', 
									copay_type='$copay_type'	
										where 
									id='$payment_detail_id' and payment_type='checkin'");
					}
				}
				else{
					/*$delQry = "update check_in_out_payment_details set status='1',delete_date='".date("Y-m-d")."',delete_time='".date('H:i:s')."',delete_operator_id='".$_SESSION['authId']."' where id = '$payment_detail_id'";
					imw_query($delQry);*/
				}
			}
		}
	}
	
	$printReciept = false;
	if(trim($btn_submit_print) != ''){
		$printReciept = true;		
	}
	$curInsType="";
	if($insurance_primary_id){
		$currInsIdPri=$insurance_primary_id;	
		$curInsType="primary";			
	}
	if($insurance_secondary_id){
		$currInsIdSec=$insurance_secondary_id;
		$curInsType="secondary";			
	}
	?>
    <script type="text/javascript">
		//--- PRINT RECIEPT CODE ---
		var printReciept = "<?php print $printReciept; ?>";
		if(printReciept){
			var edit_id = "<?php print $edit_payment_tbl_id; ?>";
			var sch_id = "<?php print $sch_id; ?>";
			window.open("payment_receipt.php?id="+sch_id+"&action=form_save",'print_receipt','width=800,height=550,top=10,left=40,scrollbars=yes,resizable=yes');
		}
		
		var pat_id = '<?php print $_SESSION['patient']; ?>';
		var sch_id = '<?php print $_POST['sch_id']; ?>';
		var sel_date = '<?php print $_POST['hiddSelDate']; ?>';	
		var current_caseid = '<?php print $current_caseid; ?>';
		var insuranceIdPri= '<?php print $currInsIdPri;?>';
		var insuranceIdSec= '<?php print $currInsIdSec;?>';
		var currInsuType='<?php print $curInsType; ?>';
		var hidd_scan_card_type='<?php print $hidd_scan_card_type; ?>';
		var location_url = "";
		<?php 
		if(isset($_REQUEST['after_save_url']) && $_REQUEST['after_save_url']=='consentTab'){
		 $_SESSION['temp_check_in_qsting'] = urlencode("ci_pid=".$_SESSION['patient']."&sch_id=".$_POST['sch_id']."&sel_date=".$_POST['hiddSelDate']."&chg_current_caseid=".$current_caseid."&frm_status=show_check_in");
			
		?>
			var location_url = '../../patient_info/check_in_consent_index.php';
		<?php
		}else if(isset($_REQUEST['after_save_url']) && $_REQUEST['after_save_url']=='closeCI'){?>
			<?php if($source != "demographics"){?>
			if(typeof(window.opener.top.fmain.pre_load_front_desk)!="undefined") {
				window.opener.top.fmain.pre_load_front_desk('<?php echo $_SESSION["patient"];?>','<?php echo $_REQUEST["sch_id"];?>');
			}
			if( window.opener.top.document.getElementById('hidChkDemoTabDbStatus'))
			{
				if( window.opener.top.document.getElementById('hidChkDemoTabDbStatus').value == 'loaded')
				{
					window.opener.top.fmain.location.reload(false);
				}

			}
			window.close();
			<?php }?>
			
		<?php
		}else{
		?>
			var location_url = "new_patient_info_popup_new.php?ci_pid="+pat_id+"&sch_id="+sch_id+"&sel_date="+sel_date+"&chg_current_caseid="+current_caseid+"&frm_status=show_check_in&patInsIdPri="+insuranceIdPri+"&patInsIdSec="+insuranceIdSec+"&currInsType="+currInsuType+"&hidd_scan_card_type="+hidd_scan_card_type;
		<?php } ?>
		if(location_url != ""){
            <?php if($source == "scheduler" && $sch_id == '')
				echo "window.opener.top.core_set_pt_session(window.opener.top.fmain, ".$_SESSION['patient'].", '../patient_info/demographics/index.php');";
			?>
			parent.window.location.href = location_url;
			<?php if($source == "demographics")
				echo "window.opener.top.core_set_pt_session(window.opener.top.fmain, ".$_SESSION['patient'].", '../patient_info/demographics/index.php');";
			?>
		}
	</script>
	<?php //die();//no need to process further in this case.
}
if(!$bool_save_payments){$visit_payment_readonly=' disabled';}else{$visit_payment_readonly='';}
//$objManageData->Smarty->assign("visit_payment_readonly",$visit_payment_readonly);
$patient_id = $_SESSION['patient'];
//$objManageData->Smarty->assign("patient_id",$patient_id);
//--- Start Query For Patient Who Has No Open Case --------
$qry = "select * from insurance_case where patient_id = '$patient_id' and case_status = 'Open'
		order by ins_case_type";
$qryId = imw_query($qry);
if(imw_num_rows($qryId)<=0){
	$insertData['case_status'] = 'Open';
	$insertData['ins_case_type'] = 1;
	$insertData['patient_id'] = $patient_id;
	$insertData['start_date'] = date('Y-m-d');
	$insertId = Addrecords($insertData,'insurance_case');		
}
//--- End Query For Patient Who Has No Open Case --------
//--- CHECK IN / OUT DATA ----
if($frm_status != ''){
	$polcies_query = "select count(show_check_in) as rowCount,refraction,secondary_copay,sec_copay_collect_amt,sec_copay_for_ins, RTEValidDays from copay_policies where $frm_status > '0'";
	$polciesQryRes = mysqlifetchdata($polcies_query);
	$collectSecCopay = false;
	$sec_copay_collect_amt = $polciesQryRes[0]['sec_copay_collect_amt'];
	$sec_copay_for_ins = $polciesQryRes[0]['sec_copay_for_ins'];
	$refractionChk = $polciesQryRes[0]['refraction'];
	$intRTEValidDays = $polciesQryRes[0]['RTEValidDays'];
}
else{
	$qryPolicy = "select RTEValidDays from copay_policies where policies_id = '1'";
	$rowPolicy = mysqlifetchdata($qryPolicy);
	$intRTEValidDays = $rowPolicy[0]['RTEValidDays'];
}

//--- SET IMAGE FOLDER PATH ---
//$objManageData->Smarty->assign("webroot",$webroot);

//--- SET CSS FILE PATH ---
//$objManageData->Smarty->assign("css_header",$css_header);
//$objManageData->Smarty->assign("css_patient",$css_patient);

//--- PATIENT TITLE DROP DOWN ---
$title_arr = array("Mr."=>"Mr.","Mrs."=>"Mrs.","Miss."=>"Miss.","Ms."=>"Ms.","Miss"=>"Miss","Master"=>"Master","Prof."=>"Prof.","Dr."=>"Dr.");

//$objManageData->Smarty->assign("title_data",$title_arr);

//--- PATIENT GENDER INFORMATION DROP DOWN ---
$gender_info_arr = gender();
//$objManageData->Smarty->assign("gender_info_str",$gender_info_arr);

//--- GET FACILITY DROP DOWN ---
$fac_query = "select pos_facilityies_tbl.pos_facility_id, pos_facilityies_tbl.facilityPracCode, 
			pos_tbl.pos_prac_code from pos_facilityies_tbl join pos_tbl 
			on pos_facilityies_tbl.pos_id = pos_tbl.pos_id order by headquarter desc, pos_facilityies_tbl.facilityPracCode";
$facQryRes = mysqlifetchdata($fac_query);
$facility_data_arr = array();
for($i=0;$i<count($facQryRes);$i++){
	$pos_facility_id = $facQryRes[$i]['pos_facility_id'];
	$facilityPracCode = $facQryRes[$i]['facilityPracCode'];
	$pos_prac_code = $facQryRes[$i]['pos_prac_code'];
	$facility_data_arr[$pos_facility_id] = $facilityPracCode.' - '.$pos_prac_code;
}
//$objManageData->Smarty->assign("facility_data_str",$facility_data_arr);

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
        
        $selQry1 = "select pos_facilityies_tbl.pos_facility_id, pos_facilityies_tbl.facilityPracCode, 
                    pos_tbl.pos_prac_code from pos_facilityies_tbl join pos_tbl 
                    on pos_facilityies_tbl.pos_id = pos_tbl.pos_id 
                    where posfacilitygroup_id IN(".$posfacgroup_ids_str.") 
                    order by headquarter desc, pos_facilityies_tbl.facilityPracCode";
        $res1 = imw_query($selQry1);
        while($row1 = imw_fetch_assoc($res1)){
            $user_pos_fac_arr[]=$row1['pos_facility_id'];	
        }
    }
}

//-------------------- get POS facility from Users POS facility group ------------------

//--- GET PATIENT INFORMATION -----
$patientQuery = "select *, date_format(DOB,'".get_sql_date_format()."') as patient_dob from patient_data where id = '$patient_id'";
$patientQryRes = mysqlifetchdata($patientQuery);
//--- ASSIGN PATIENT INFORMATION -----

//--- SET PATIENT IMAGE ----
if(trim($patientQryRes[0]["p_imagename"]) != ''){
	$upload_dir = '../../../data/'.constant('PRACTICE_PATH');
	$patient_image_path = realpath($upload_dir.$patientQryRes[0]["p_imagename"]);
	if(file_exists($patient_image_path) ){
		$patient_image_name = $patientQryRes[0]["p_imagename"];
		$patient_image_path = $upload_dir.$patientQryRes[0]["p_imagename"];
		$image_new_size = newImageResize($patient_image_path,150,90);
		$image_del_dis = 'block';
	}
	
}

if( !file_exists($patient_image_path) ){
	$patient_image_path = "../../../library/images/no_image_found.png";
	$image_new_size = "width='115' height='90'";
	$image_del_dis = 'none';
}

//--- PATIENT LICENCE IMAGE --
$license_image_name = $patientQryRes[0]["licence_photo"];
$lic_show_div = 'none';
$dis_lic_anchor = '';
if(trim($license_image_name) != ''){	
	$lic_img_path = $updir.$license_image_name;
	$licence_photo = realpath($lic_img_path);
	$lic_img_path_new = "";
	if($licence_photo != ''){
		$lic_show_div = 'block';
		$dis_lic_anchor = 'disabled';
		$lic_img_path_new = $srcDir.$license_image_name;
		$lic_img_src = "<img src=\"".$lic_img_path_new."\" $lic_img_wid style=\"cursor:pointer\" onClick=\"show_dl(this)\"  data-src=\"".$lic_img_path_new."\" title=\"Driviing License\">";
		$ptLicLargeDivSrc = "<img src=\"".$lic_img_path."\">";
		
	}
}else{
	//if license img is empty
	$lic_img_wid = newImageResize($lic_img_path,68,55);
	$lic_img_path = "../../../library/images/no_image_found.png";
	$lic_img_src = "<img src=\"".$lic_img_path."\">";
}	 
$q = "SELECT * FROM patient_multi_address 
	  WHERE patient_id = '".$_SESSION['patient']."'
		AND del_status = 0
		AND id != '".$patientQryRes[0]['default_address']."'
	  ";  
$patient_multi_add = mysqlifetchdata($q);
/* $objManageData->Smarty->assign("patient_multi_add", $patient_multi_add);
$objManageData->Smarty->assign("license_image_name", $license_image_name);
$objManageData->Smarty->assign("dis_lic_anchor", $dis_lic_anchor);
$objManageData->Smarty->assign("lic_show_div", $lic_show_div);
$objManageData->Smarty->assign("lic_img_src", $lic_img_src);
$objManageData->Smarty->assign("ptLicLargeDivSrc", $ptLicLargeDivSrc);	
$objManageData->Smarty->assign("image_del_dis", $image_del_dis);
$objManageData->Smarty->assign("patient_image_name", $patient_image_name);
$objManageData->Smarty->assign("patient_image_path", $patient_image_path);
$objManageData->Smarty->assign("image_new_size", $image_new_size);
$objManageData->Smarty->assign("edit_patient_id", $patientQryRes[0]['pid']);
$objManageData->Smarty->assign("patient_title", $patientQryRes[0]['title']);
$objManageData->Smarty->assign("patient_fname",$patientQryRes[0]['fname']);
$objManageData->Smarty->assign("patient_mname",$patientQryRes[0]['mname']);
$objManageData->Smarty->assign("patient_lname",$patientQryRes[0]['lname']);
$objManageData->Smarty->assign("patient_suffix",$patientQryRes[0]['suffix']);
$objManageData->Smarty->assign("patient_gender_val",$patientQryRes[0]['sex']);
$objManageData->Smarty->assign("patient_email",$patientQryRes[0]['email']);
$objManageData->Smarty->assign("patient_username", trim($patientQryRes[0]['username']));
$objManageData->Smarty->assign("patient_password", trim($patientQryRes[0]['password']));
$objManageData->Smarty->assign("patient_temp_key", trim($patientQryRes[0]['temp_key'])); */
if($patientQryRes[0]['patient_dob'] != '00-00-0000'){
	$patient_age = $OBJCommonFunction->get_pat_age_year($patientQryRes[0]['id']);
	$date_qry = "SELECT DATE_FORMAT(SUBDATE(CAST(FROM_DAYS(DATEDIFF(NOW(),SUBDATE('".trim(getDateFormatDB($patientQryRes[0]['patient_dob']))."',INTERVAL 1 YEAR))) AS DATE),INTERVAL 1 YEAR),'%m') AS MONTHS,DATE_FORMAT(SUBDATE(CAST(FROM_DAYS(DATEDIFF(NOW(),SUBDATE('".trim(getDateFormatDB($patientQryRes[0]['patient_dob']))."',INTERVAL 1 YEAR))) AS DATE),INTERVAL 1 YEAR),'%y') AS YEAR ";
$date_result = imw_query($date_qry);
$arrDate = imw_fetch_array($date_result);
	if($patient_age_text == "Mon."){
		$patient_age_month = $patient_age;
		$patient_age = "";
	}
	else{
		if(date("d") == $day){
			$patient_age_month = 0;	
		}
		else{
			$patient_age_month = $arrDate[0]-1;	
		}
	}
	$patient_age = (($patient_age > 0) ? $patient_age : "0");
	$patient_age_month = (($patient_age_month > 0) ? $patient_age_month : "0");
	//$objManageData->Smarty->assign("patient_age",$patient_age);
	//$objManageData->Smarty->assign("patient_age_month",$patient_age_month);
	//$objManageData->Smarty->assign("patient_dob",$patientQryRes[0]['patient_dob']);
}
$marital_st_arr_val = array('','divorced','domestic partner','married','single','separated','widowed');
$marital_st_arr = array('','Divorced','Domestic Partner','Married','Single','Separated','Widowed');
//$objManageData->Smarty->assign("pat_marital_status_val",$marital_st_arr_val);
//$objManageData->Smarty->assign("pat_marital_status",$marital_st_arr);
//$objManageData->Smarty->assign("pat_marital_status_sel",$patientQryRes[0]['status']);
//$objManageData->Smarty->assign("patient_driving_licence",$patientQryRes[0]['driving_licence']);
//$objManageData->Smarty->assign("patient_ssnNumber",$patientQryRes[0]['ss']);
//$objManageData->Smarty->assign("patient_fac_id",$patientQryRes[0]['default_facility']);
//$objManageData->Smarty->assign("patient_address1",$patientQryRes[0]['street']);
//$objManageData->Smarty->assign("patient_address2",$patientQryRes[0]['street2']);
//$objManageData->Smarty->assign("patient_postal_code",$patientQryRes[0]['postal_code']);
//$objManageData->Smarty->assign("patient_zip_ext",$patientQryRes[0]['zip_ext']);
//$objManageData->Smarty->assign("patient_city",$patientQryRes[0]['city']);
//$objManageData->Smarty->assign("patient_state",$patientQryRes[0]['state']);
//$objManageData->Smarty->assign("int_county",$patientQryRes[0]['county']);
//$objManageData->Smarty->assign("default_address",$patientQryRes[0]['default_address']);
$pat_emr_ck = $patientQryRes[0]['EMR'] > 0 ? 'checked' : '';
//$objManageData->Smarty->assign("patient_emr",$pat_emr_ck);

$pat_hs_ck = $patientQryRes[0]['hold_statement'] > 0 ? 'checked' : '';
//$objManageData->Smarty->assign("patient_hs",$pat_hs_ck);

$pf_contact_chk = $patientQryRes[0]['preferr_contact'];
//$objManageData->Smarty->assign("preferr_contact",$pf_contact_chk);
//$objManageData->Smarty->assign("race",explode(",",$patientQryRes[0]['race']));
//$objManageData->Smarty->assign("otherRace",$patientQryRes[0]['otherRace']);
//$objManageData->Smarty->assign("ethnicity",explode(",",$patientQryRes[0]['ethnicity']));
//$objManageData->Smarty->assign("otherEthnicity",$patientQryRes[0]['otherEthnicity']);
//$objManageData->Smarty->assign("language",$patientQryRes[0]['language']);
//$objManageData->Smarty->assign("temp_key",$patientQryRes[0]['temp_key']);

$pt_key=($patientQryRes[0]['temp_key'])?'':'none';
//$objManageData->Smarty->assign("pt_key",$pt_key);

$temp_key_chk_status = '';
if($patientQryRes[0]['temp_key_chk_val'] == 1){
	$temp_key_chk_status = "checked";
}
//$objManageData->Smarty->assign("temp_key_chk_val",$temp_key_chk_status);
$other_language = substr($patientQryRes[0]['language'],0,5);
if($other_language == 'Other'){
	$other_language_val = substr($patientQryRes[0]['language'],9);
	$patientQryRes[0]['language'] = 'Other';
	//$objManageData->Smarty->assign("language","Other");
	//$objManageData->Smarty->assign("otherLanguage", substr($patientQryRes[0]["language"],9));
}

//$objManageData->Smarty->assign("vip_status",$patientQryRes[0]['vip']);

//--- GET PATIENT REFERRING PHYSICIAN ID ---
$reff_phy_id = $patientQryRes[0]["primary_care_id"];
if($reff_phy_id > 0){
	$ref_phy_class = "";
	//$ref_phy_name = getRefferPhysicianName($reff_phy_id);
	$ref_phy_name =  $OBJCommonFunction->get_ref_phy_name($reff_phy_id);
	/*if(is_ref_phy_deleted($reff_phy_id)){
		$ref_phy_class = "del_val";
		//$ref_phy_name = getRefferPhysicianName($reff_phy_id);
		$ref_phy_name =  $OBJCommonFunction->get_ref_phy_name($reff_phy_id);
	}*/
    $ref_phy_status = $OBJCommonFunction->get_ref_phy_del_status($reff_phy_id);
    if($ref_phy_status) {
        $ref_phy_class = " red-font ";
    }

	$ref_phy_name = str_replace('"', '&quot;', $ref_phy_name); 
}
$refPhyStr = $refPhyPopover = '';
$refPhyStr = $ref_phy_name;
if( $ref_phy_name)
	$refPhyPopover .= '<span class="col-xs-12 '.$ref_phy_class.'">&bull; '.$ref_phy_name.'</span>';
// get all referring physicians
$refPhyArr = $demo->get_referring_physician((int)$patientQryRes[0]['id'],'1');
$refPhyIdArr = $refPhyArr->id;
$refPhyNameArr = $refPhyArr->name;
$refPhyStatusArr = $refPhyArr->status;
for($i = 0; $i<count($refPhyIdArr);$i++)
{
	if($refPhyIdArr[$i] <> $reff_phy_id && $reff_phy_id != "") {
		$refPhyStr .= "; ".$refPhyNameArr[$i];
		$refPhyPopover .= '<span class="col-xs-12 '.($refPhyStatusArr[$i]?'red-font':'').'">&bull; '.$refPhyNameArr[$i].'</span>';
	}
}

$primary_care_phy_id = $patientQryRes[0]['primary_care_phy_id'];
if($primary_care_phy_id > 0){
	$primary_care_phy_class = "";
	//$primary_care_phy_name = getRefferPhysicianName($primary_care_phy_id);
	$primary_care_phy_name =  $OBJCommonFunction->get_ref_phy_name($primary_care_phy_id);
	/*if(is_ref_phy_deleted($primary_care_phy_id)){
		  $primary_care_phy_class = "del_val";
		//$primary_care_phy_name = getRefferPhysicianName($primary_care_phy_id);
		$primary_care_phy_name =  $OBJCommonFunction->get_ref_phy_name($primary_care_phy_id);
		//$primary_care_phy_id = '';
	}*/
	$primary_care_phy_status = $OBJCommonFunction->get_ref_phy_del_status($primary_care_phy_id);
    if($primary_care_phy_status) {
        $primary_care_phy_class = " red-font ";
    }	
	$primary_care_phy_name = str_replace('"', '&quot;', $primary_care_phy_name); 
}
$primaryPhyStr = $primaryPhyPopover = '';
$primaryPhyStr = $primary_care_phy_name;
if( $primary_care_phy_name ) 
	$primaryPhyPopover .= '<span class="col-xs-12 '.$primary_care_phy_class.'">&bull; '.$primary_care_phy_name.'</span>';
// get all referring physicians
$primaryPhyArr = $demo->get_referring_physician((int)$patientQryRes[0]['id'],'3,4');
$primaryPhyIdArr = $primaryPhyArr->id;
$primaryPhyNameArr = $primaryPhyArr->name;
$primaryPhyStatusArr = $primaryPhyArr->status;
for($i = 0; $i<count($primaryPhyIdArr);$i++)
{
	if($primaryPhyIdArr[$i] <> $primary_care_phy_id && $primary_care_phy_id != "") {
		$primaryPhyStr .= "; ".$primaryPhyNameArr[$i];
		$primaryPhyPopover .= '<span class="col-xs-12 '.($primaryPhyStatusArr[$i]?'red-font':'').'">&bull; '.$primaryPhyNameArr[$i].'</span>';
	}
}


//--- CHANGE PHONE FORMAT ---
$phone_home = core_phone_format($patientQryRes[0]['phone_home']);
$phone_biz = core_phone_format($patientQryRes[0]['phone_biz']);
$phone_biz_ext = $patientQryRes[0]['phone_biz_ext'];
$phone_cell = core_phone_format($patientQryRes[0]['phone_cell']);
//$objManageData->Smarty->assign("patient_phone_home",$phone_home);
//$objManageData->Smarty->assign("patient_phone_biz",$phone_biz);
//$objManageData->Smarty->assign("zip_length",inter_zip_length());
//$objManageData->Smarty->assign("state_length",interStateLength());
//$objManageData->Smarty->assign("patient_phone_biz_ext",$phone_biz_ext);
//$objManageData->Smarty->assign("patient_phone_cell",$phone_cell);
//$objManageData->Smarty->assign("phone_format",interPhoneFormat());
//$objManageData->Smarty->assign("patient_notes",$patientQryRes[0]['patient_notes']);

$scheduler_chk = $patientQryRes[0]['chk_notes_scheduler'] == 1 ? 'checked' : '';
$chart_notes_chk = $patientQryRes[0]['chk_notes_chart_notes'] == 1 ? 'checked' : '';
$accounting_chk = $patientQryRes[0]['chk_notes_accounting'] == 1 ? 'checked' : '';
//$objManageData->Smarty->assign("scheduler_chk",$scheduler_chk);
//$objManageData->Smarty->assign("chart_notes_chk",$chart_notes_chk);
//$objManageData->Smarty->assign("accounting_chk",$accounting_chk);

//--- GET INSURANCE COMPANY DETAILS ---
$insCaseDataArr = array();
//--- GET INSURANCE CASES DROP DOWN --
if($patient_id > 0){
	$openIsCaseQry = "SELECT insurance_case_types.case_id, insurance_case_types.case_name, insurance_case.ins_caseid, 
					insurance_case_types.vision, insurance_case_types.normal,insurance_case_types.default_selected FROM insurance_case 
					LEFT JOIN insurance_case_types ON insurance_case_types.case_id = insurance_case.ins_case_type 
					WHERE insurance_case.patient_id = '$patient_id' AND insurance_case.case_status = 'Open'";
	$openIsCaseQryRes = mysqlifetchdata($openIsCaseQry);
	if( is_array($openIsCaseQryRes) && count($openIsCaseQryRes) > 0 )
		$insCaseDataArr = array_merge($insCaseDataArr,$openIsCaseQryRes);
	
	$caseIdArr = array();
	for($i=0;$i<count($openIsCaseQryRes);$i++){
		$caseIdArr[] = $openIsCaseQryRes[$i]['case_id'];
	}
	$caseIdStr = join(',',$caseIdArr);
}

//--- GET CLOSED INSURANCE CASE ---
$close_case_query = "SELECT insurance_case_types.case_id, insurance_case_types.case_name, insurance_case_types.vision, 
				insurance_case_types.normal,insurance_case_types.default_selected FROM insurance_case_types ";
if($caseIdStr != ''){
	$close_case_query .= " WHERE insurance_case_types.case_id NOT IN ($caseIdStr) and insurance_case_types.status=0";
}else{
	$close_case_query .= " WHERE insurance_case_types.status=0";
}
$close_case_query .= " ORDER BY insurance_case_types.case_name";
$closeCaseQryRes = mysqlifetchdata($close_case_query);
if( is_array($closeCaseQryRes) && count($closeCaseQryRes) > 0 )
	$insCaseDataArr = array_merge($insCaseDataArr,$closeCaseQryRes);

//--- GET CURRENT CASE ID FROM APPOITMENT TABLE ---
if(trim($sch_id) != ""){
	$schedule_query = "select schedule_appointments.case_type_id,auth_pri_id,
						auth_sec_id,auth_ter_id,sa_doctor_id,sa_app_start_date,rte_id  FROM schedule_appointments
		where schedule_appointments.sa_patient_id = '$patient_id' and schedule_appointments.id = '$sch_id'";
	$schQryRes = mysqlifetchdata($schedule_query);
	$current_caseid = $schQryRes[0]['case_type_id'];
	$sch_auth_pri_id = $schQryRes[0]['auth_pri_id'];
	$sch_auth_sec_id = $schQryRes[0]['auth_sec_id'];
	$sch_auth_ter_id = $schQryRes[0]['auth_ter_id'];
	$sa_doctor_id = $schQryRes[0]['sa_doctor_id'];
	$sa_app_start_date = $schQryRes[0]['sa_app_start_date'];	
	$sch_rte_id = $schQryRes[0]['rte_id'];
}
if($chg_current_caseid != ''){
	$current_caseid = $chg_current_caseid;
}
$getRefChkStr = imw_query("Select id,user_type,collect_refraction  FROM users 
						WHERE id ='$sa_doctor_id'");
$usr_detail=imw_fetch_array($getRefChkStr);
$user_type=$usr_detail['user_type'];
$collect_refraction=$usr_detail['collect_refraction'];
if($refractionChk=='No'){
	if($collect_refraction>0){
		$refractionChk='yes';
	}
}
$insCaseDropVal = '';
//--- SET INSURANCE CASE DROP DOWN ----
//hack code to set one value by default selected if none was it 
$haveDefault=false;
for($i=0;$i<count($insCaseDataArr);$i++){
	if($insCaseDataArr[$i]['default_selected']==1)
	{
		$haveDefault=true;
		break;
	}
}
for($i=0;$i<count($insCaseDataArr);$i++){
	$case_id = $insCaseDataArr[$i]['case_id'];
    $normal = $insCaseDataArr[$i]['normal'];
    $vision = $insCaseDataArr[$i]['vision'];
    $ins_caseid = $insCaseDataArr[$i]['ins_caseid'];
    $case_name = $insCaseDataArr[$i]['case_name']; 
    $default_sel = $insCaseDataArr[$i]['default_selected']; 
	$dis_name = $case_name;
	$dis_val = "$case_id-$normal-$vision";
	if(empty($current_caseid) === true and $normal > 0){
		$current_caseid = $ins_caseid;
	}
	if($ins_caseid > 0){
		$dis_name .= '-'.$ins_caseid;
		$dis_val .= '-'.$ins_caseid;
	}	
	$sel = '';
	if(empty($patient_id) === false){
		if($current_caseid == $ins_caseid){
			$sel = 'selected="selected"';
			$vision_chk = $vision;
			$normal_chk = $normal;
		}
	}
	/*else if($normal == '1'){
		$sel = 'selected';
		$normal_chk = $normal;
	}*/
	else if($default_sel=='1' || $haveDefault==false){
		$sel = 'selected';		
		$vision_chk = $vision;
		$normal_chk = $normal;
		$haveDefault=true;
	}
	$insCaseDropVal .= "<option value='$dis_val' $sel >$dis_name</option>";
}

$vision_dis = $vision_chk == 1 ? "block" : "none";
//$objManageData->Smarty->assign("sch_id",$sch_id);
//$objManageData->Smarty->assign("current_caseid",$current_caseid);
//$objManageData->Smarty->assign("insCaseDropVal",$insCaseDropVal);
//$objManageData->Smarty->assign("vision_dis",$vision_dis);
$normal_dis = $normal_chk != '0' ? "block" : "none";
$coins_dis = ($vision_chk == 1  || $normal_chk == 1) ? "block" : "none";
//$objManageData->Smarty->assign("normal_dis",$normal_dis);
//$objManageData->Smarty->assign("refraction_Chk",$refractionChk);


$policyFld = "Policy ".getHashOrNo(false);
$groupFld = "Group ".getHashOrNo(false);
$copayFld = "Copay";
$copay_collect = true;
if($vision_chk == 'none' and $normal_dis == 'none'){
	$policyFld = "Claim";
	$groupFld = "Emp. Name";
	$copayFld = "Adj. Name";
	$copay_collect = false;
}
//$objManageData->Smarty->assign("policyFld",$policyFld);
//$objManageData->Smarty->assign("groupFld",$groupFld);
//$objManageData->Smarty->assign("copayFld",$copayFld);

function get_insurance_typeahead(){
	global $OBJCommonFunction;
	
	$data_path = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH');
	
	
	if(constant("EXTERNAL_INS_MAPPING") == "YES")$insCompXMLFile = $data_path."/xml/Insurance_Comp_Cross_Map.xml";
	else $insCompXMLFile = $data_path."/xml/Insurance_Comp.xml";
	
	if(file_exists($insCompXMLFile)) $insCompXMLFileExits = true;
	else{
		if(constant("EXTERNAL_INS_MAPPING") == "YES"){
			$OBJCommonFunction->createInsCompXMLCrossMap();
		}
		else{
			$OBJCommonFunction -> createInsCompXML();	
		}
		if(file_exists($insCompXMLFile)){
			$insCompXMLFileExits = true;	
		}	
	}
	
	if($insCompXMLFileExits == true){
		$values = array();
		$XML = file_get_contents($insCompXMLFile);
		$values = $OBJCommonFunction -> XMLToArray($XML);		
		$arrInsCompData = array();	
		$ptResName = "";
		$resNameComp = "";	
		foreach($values as $key => $val){
			$insRtName = "";		
			if( ($val["tag"] =="insCompInfo") && ($val["type"]=="complete") && ($val["level"]=="2") ){		
				$insCompId = $insCompINHouseCode = $insCompName = $insCompAdd = $insCompCity = $insCompState = $insCompZip = "";
				$crossMapIdxInvRCOId = $crossMapInvisionPlanCode = $crossMapInvisionPlanDescription = $crossMapIDXDescription = $crossMapIDXFSC = "";
				
				$insCompId = $val["attributes"]["insCompId"];	
				$insCompINHouseCode = str_replace("'","",$val["attributes"]["insCompINHouseCode"]);
				$insCompName = str_replace("'","",$val["attributes"]["insCompName"]);
				$insCompAdd = str_replace("'","",$val["attributes"]["insCompAdd"]);
				$insCompCity = str_replace("'","",$val["attributes"]["insCompCity"]);
				$insCompState = str_replace("'","",$val["attributes"]["insCompState"]);
				$insCompZip = str_replace("'","",$val["attributes"]["insCompZip"]);
				
				if(constant("EXTERNAL_INS_MAPPING") == "YES"){
					$crossMapIdxInvRCOId = str_replace("'","",$val["attributes"]["dbIdxInvRCOId"]);
					$crossMapInvisionPlanCode = str_replace("'","",$val["attributes"]["dbInvisionPlanCode"]);
					$crossMapInvisionPlanDescription = str_replace("'","",$val["attributes"]["dbInvisionPlanDescription"]);
					$crossMapIDXDescription = str_replace("'","",$val["attributes"]["dbIDXDescription"]);
					$crossMapIDXFSC = str_replace("'","",$val["attributes"]["dbIDXFSC"]);
				}
				if(is_numeric($resName) == true){			
					if(trim($insCompId) == trim($resName) && empty($ptResName) == true){				
						if(strlen($insCompName) > 12){
							$resNameComp = substr($insCompName,0,12).'....';
						}
						else{
							$resNameComp = $insCompName;
						}				
						$resNameComp = trim($resNameComp);					
					}
				}
				if($insCompINHouseCode){
					$insRtName = $insCompINHouseCode;
				}else{
					$insRtName = substr($insCompName,0,4).'....';
				}		
				$sep = '';
				if(empty($insCompINHouseCode) == false){
					$sep = ' - ';
				}
			
				if(constant("EXTERNAL_INS_MAPPING") == "YES"){
					$arrInsCompData[] = $crossMapInvisionPlanCode." - ".$crossMapInvisionPlanDescription." - ".$crossMapIDXDescription." - ".$crossMapIDXFSC." - ".$insCompName." ".$insCompAdd." - ".$insCompCity.", ".$insCompState." ".$insCompZip." * $insCompId-$crossMapIdxInvRCOId";
				}
				else{
					if(trim($insCompINHouseCode) && trim($insCompName)){
						$arrInsCompData[] = $insCompINHouseCode." ".$sep." ".$insCompName." ".$insCompAdd." - ".$insCompCity.", ".$insCompState." ".$insCompZip." * $insCompId";		
					}
					elseif((trim($insCompINHouseCode) == "") && (trim($insCompName) != "")){
						$arrInsCompData[] = $insCompName." ".$sep." ".$insCompINHouseCode." ".$insCompAdd." - ".$insCompCity.", ".$insCompState." ".$insCompZip." * $insCompId";		
					}
				}
				
			}
		}
		if(count($arrInsCompData)>0){
			return $arrInsCompData;
		}	
	}
}

$insCompanyStr = get_insurance_typeahead();
//$objManageData->Smarty->assign("insCompanyTypeAhead",remLineBrk($insCompanyStr));


//--- GET INSURANCE COMPANY DETAILS ---
$ins_com_query = "select insurance_data.id as insurance_dataId, 
						insurance_data.group_number,
						insurance_data.copay,
						date_format(insurance_data.effective_date, '".get_sql_date_format()."') as effective_date,
						insurance_data.policy_number,  
						date_format(insurance_data.expiration_date, '".get_sql_date_format()."') as expiration_date,
						insurance_data.co_ins as co_ins,
						insurance_data.provider,
						insurance_companies.in_house_code,
						insurance_companies.id as ins_comp_id,
						insurance_companies.name, 
						insurance_data.scan_card,
						insurance_data.scan_card2,
						insurance_data.actInsComp,
						insurance_data.type, 
						insurance_data.self_pay_provider,
						insurance_data.auth_required ,
						insurance_data.referal_required,
						insurance_data.auth_required,
						insurance_companies.collect_copay,
						insurance_data.copay_type, 
						insurance_data.rco_code, 
						insurance_data.rco_code_id, 
						insurance_companies.claim_type as claimType,
						insurance_companies.FeeTable,
                        insurance_data.subscriber_lname,
                        insurance_data.subscriber_mname,
                        insurance_data.subscriber_fname,
                        insurance_data.subscriber_suffix,
                        insurance_data.subscriber_relationship,
                        insurance_data.subscriber_ss,
                        date_format(insurance_data.subscriber_DOB,'".get_sql_date_format()."') as subscriber_DOB,
                        insurance_data.subscriber_sex,
                        insurance_data.comments,
                        insurance_companies.ins_accept_assignment
						from 
						insurance_data 
						left join insurance_companies
						on insurance_companies.id = insurance_data.provider 
						where 
						insurance_data.ins_caseid = '$current_caseid' 
						and insurance_data.pid = '$patient_id'
						and insurance_data.actInsComp = '1' 
						and insurance_data.type != 'tertiary' 
						order by insurance_data.type";
	
$insQryRes = get_array_records_query($ins_com_query);
$insDataArr = array();
$copayAmtArr = array();
$copayAmtDilatedArr= array();
$copayAmtNonDilatedArr= array();
$collect_copay_dilated_test_amt = array();
$collect_copay_non_dilated_test_amt = array();
$insurance_dataId_arr = array();
$arrInsSwapData = array();
$primary_referal_required_dis = 'none';
$secondary_referal_required_dis = 'none';
$primary_auth_required_dis = 'none';
$secondary_auth_required_dis = 'none';
$collect_copay_test = false;
$collect_copay_test_amt = array();
for($i=0;$i<count($insQryRes);$i++){
	$ins_type = strtolower($insQryRes[$i]['type']);
	$insurance_dataId_arr[] = $insQryRes[$i]['insurance_dataId'];
	if($insQryRes[$i]['self_pay_provider']){
		$self_pay_provider = $insQryRes[$i]['self_pay_provider'];
	}
	$insDataArr[$ins_type]['rco_code'] = $insQryRes[$i]['rco_code'];
	$insDataArr[$ins_type]['rco_code_id'] = $insQryRes[$i]['rco_code_id'];
	$insDataArr[$ins_type]['MAIN_ID'] = $insQryRes[$i]['insurance_dataId'];
	$insDataArr[$ins_type]['group_number'] = $insQryRes[$i]['group_number'];
	$insDataArr[$ins_type]['FeeTable'] = $insQryRes[$i]['FeeTable'];
	if($insQryRes[$i]['effective_date'] != '00-00-0000'){
		$insDataArr[$ins_type]['effective_date'] = $insQryRes[$i]['effective_date'];
	}
	if($insQryRes[$i]['expiration_date'] == '00-00-0000'){
		$insDataArr[$ins_type]['expiration_date'] = '';
	}else{
		$insDataArr[$ins_type]['expiration_date'] = $insQryRes[$i]['expiration_date'];
	}

	$insDataArr[$ins_type]['policy_number'] = $insQryRes[$i]['policy_number'];
	$insDataArr[$ins_type]['copay'] = $insQryRes[$i]['copay'];
	$insDataArr[$ins_type]['co_ins'] = $insQryRes[$i]['co_ins'];
	if($insQryRes[$i]['auth_required'] == 'Yes'){
		$insDataArr[$ins_type]['auth_required'] = 'selected';
	}
    
    //Policy Holder Information Starts
    $insDataArr[$ins_type]['ins_all_details']=$insQryRes[$i];
    $insDataArr[$ins_type]['subscriber_lname']=$insQryRes[$i]['subscriber_lname'];
    $insDataArr[$ins_type]['subscriber_mname']=$insQryRes[$i]['subscriber_mname'];
    $insDataArr[$ins_type]['subscriber_fname']=$insQryRes[$i]['subscriber_fname'];
    $insDataArr[$ins_type]['subscriber_suffix']=$insQryRes[$i]['subscriber_suffix'];
    $insDataArr[$ins_type]['subscriber_relationship']=$insQryRes[$i]['subscriber_relationship'];
    $insDataArr[$ins_type]['subscriber_ss']=$insQryRes[$i]['subscriber_ss'];
    $insDataArr[$ins_type]['subscriber_DOB']=$insQryRes[$i]['subscriber_DOB'];
    $insDataArr[$ins_type]['subscriber_sex']=$insQryRes[$i]['subscriber_sex'];
    $insDataArr[$ins_type]['comments']=$insQryRes[$i]['comments'];
    $insDataArr[$ins_type]['ins_accept_assignment']=$insQryRes[$i]['ins_accept_assignment'];
	//Policy Holder Information Ends
    
	if($insQryRes[$i]['collect_copay'] == 1){
		if($insQryRes[$i]['copay_type']>0){
			if($insQryRes[$i]['copay_type']==2){
				$collect_copay_test = true;
				$collect_copay_test_amt = explode('/',$insQryRes[$i]['copay']);	
				$collect_copay_non_dilated_test_amt[] = $collect_copay_test_amt[1];
			}else{
				$collect_copay_test = true;
				$collect_copay_test_amt = explode('/',$insQryRes[$i]['copay']);	
				$collect_copay_dilated_test_amt[] = $collect_copay_test_amt[0];	
				$collect_copay_non_dilated_test_amt[] = $collect_copay_test_amt[1];
			}
		}else{
			$collect_copay_test = true;
			$collect_copay_test_amt = explode('/',$insQryRes[$i]['copay']);	
			$collect_copay_non_dilated_test_amt[] = $collect_copay_test_amt[0];
		}
	}
	//--- GET TOTAL COPAY AMOUNT ----	
	if(strtolower($ins_type) == 'primary'){
		$primaryInsuranceCoId=$insQryRes[$i]['ins_comp_id'];
		$rte_ins_copay[]=$insQryRes[$i]['copay'];
		$insurance_dataId = $insQryRes[$i]['insurance_dataId'];
		$co_ins_pat = $insQryRes[$i]['co_ins'];
	}
	if(strtolower($ins_type) == 'primary' and $copay_collect === true){
		if($insQryRes[$i]['copay_type']>0){
			if($insQryRes[$i]['copay_type']==2){
				if($user_type==1 || $user_type==12){
					$copayAmtArr = explode('/',$insQryRes[$i]['copay']);	
					$copayAmtDilatedArr[] = $copayAmtArr[0];	
				}
			}else{
				$copayAmtArr = explode('/',$insQryRes[$i]['copay']);	
				$copayAmtDilatedArr[] = $copayAmtArr[0];	
				$copayAmtNonDilatedArr[] = $copayAmtArr[1];
			}	
		}else{
			$copayAmtArr = explode('/',$insQryRes[$i]['copay']);	
			$copayAmtDilatedArr[] = $copayAmtArr[0];	
		}
		
	}
	if(strtolower($ins_type) == 'secondary'){
		$insurance_dataId = $insQryRes[$i]['insurance_dataId'];
		}
	if(strtolower($ins_type) == 'secondary' and $copay_collect === true){
		$secCopay=chk_sec_copay_collect($primaryInsuranceCoId);
		if($secCopay == 'Yes'){
			if($sec_copay_collect_amt>=$insQryRes[$i]['copay'] || $sec_copay_for_ins==''){
				$rte_ins_copay[]=$insQryRes[$i]['copay'];
				if($insQryRes[$i]['copay_type']>0){
					if($insQryRes[$i]['copay_type']==2){
						if($user_type==1 || $user_type==12){
							$copayAmtArr = explode('/',$insQryRes[$i]['copay']);	
							$copayAmtDilatedArr[] = $copayAmtArr[0];	
						}
					}else{
						$copayAmtArr = explode('/',$insQryRes[$i]['copay']);	
						$copayAmtDilatedArr[] = $copayAmtArr[0];	
						$copayAmtNonDilatedArr[] = $copayAmtArr[1];
					}
				}else{
					$copayAmtArr = explode('/',$insQryRes[$i]['copay']);	
					$copayAmtDilatedArr[] = $copayAmtArr[0];
				}
			}
		}
	}
	
	$ins_name = trim($insQryRes[$i]['in_house_code']);
	if($ins_name == ''){
		$ins_name = $insQryRes[$i]['name'];
	}
	$streElTable = "";
	$intDDCAmt = $intCopayAmt = "";
	$strCoInsAmt = "";
	$intRTMEid = 0;
	$intTotDeductibleAmt = $intTotCopayAmt = 0;
	$arrPatCopay = array();
	if(constant("ENABLE_REAL_ELIGILIBILITY") == "YES" && $ins_type=='primary'){
		include_once(dirname(__FILE__).'/../../../library/classes/class.cls_eligibility.php');
		$OBJEligibility = new CLSEligibility;
		$intTotVSCertInsComp = 0;
		$qryGetCertInfo = "SELECT ins_comp_id FROM vision_share_cert_config	WHERE ins_comp_id = '".(int)$insQryRes[$i]['provider']."' LIMIT 1 ";
		$rsGetCertInfo = imw_query($qryGetCertInfo);
		$intTotVSCertInsComp = imw_num_rows($rsGetCertInfo);
	
		$qryGetRTMEid = "select rte_id, sa_app_start_date from schedule_appointments where id = '".$sch_id."' LIMIT 1";
		$rsGetRTMEid = imw_query($qryGetRTMEid);
		if($rsGetRTMEid){
			if(imw_num_rows($rsGetRTMEid) > 0){
				$strAppDate = "";
				$rowGetRTMEid = imw_fetch_row($rsGetRTMEid);
				$intRTMEid = $rowGetRTMEid[0];
				$strAppDate = $rowGetRTMEid[1];
				if($intRTMEid > 0){
					$vsStatus = $vsTran = "";
					$vsToolTip = $vsStatusDate = $strEBResponce = $imgRealTimeEli = $dbRespDDC = $dbRespCopay = $dbRespCoIns = $strRTEAmtInfo = "";
					
					$arrRespDDC = $arrRespCopay = $arrRespCoIns = array();

					//$objManageData->Smarty->assign("PRACCODEVS", "MEDICARE");
					$qryGetRealTimeData = "select DATE_FORMAT(rtme.responce_date_time, '%m-%d-%Y %I:%i %p') as vs270RespDate, DATE_FORMAT(rtme.responce_date_time, '%m-%d-%Y') as vsRespDate, DATE_FORMAT(rtme.responce_date_time, '%Y-%m-%d') as respDate, 
											rtme.transection_error as vsTransectionError, 
											rtme.EB_responce as vsEBLoopResp, CONCAT_WS('',SUBSTRING(us.fname,1,1),SUBSTRING(us.mname,1,1), SUBSTRING(us.lname,1,1)) as elOpName, rtme.response_deductible, rtme.response_copay, rtme.response_co_insurance, CONCAT(SUBSTRING(us.fname,1,1),SUBSTRING(us.lname,1,1),SUBSTRING(us.mname,1,1)) as elOpNameIn,rtme.responce_pat_policy_no as policy, insComp.name as insCompName, rtme.xml_271_responce as respXMLPath, rtme.eligibility_ask_from  as elAsk
											from real_time_medicare_eligibility rtme LEFT JOIN users us on us.id = rtme.request_operator
											LEFT JOIN insurance_data insData ON insData.id = rtme.ins_data_id  
				                            LEFT JOIN insurance_companies insComp ON insComp.id = insData.provider 
											where rtme.id = '".$intRTMEid."' limit 1";
					$rsGetRealTimeData = imw_query($qryGetRealTimeData);
					if($intTotVSCertInsComp > 0){
						$askElFrom = 0;
					}
					elseif($intTotVSCertInsComp == 0){
						$askElFrom = 1;
					}
					if($rsGetRealTimeData){
						if(imw_num_rows($rsGetRealTimeData)>0){
							$rowGetRealTimeData = imw_fetch_object($rsGetRealTimeData);
							$vsToolTip = "";
							$dbRespDDC = $rowGetRealTimeData->response_deductible;
							$arrRespDDC = explode("-", $dbRespDDC);
							$intDDCAmt = (int)$arrRespDDC[4];
							
							$dbRespCopay = $rowGetRealTimeData->response_copay;
							$arrRespCopay = explode("-", $dbRespCopay);
							$intCopayAmt = (float)$arrRespCopay[4];
							
							$dbRespCoIns = $rowGetRealTimeData->response_co_insurance;
							$arrRespCoIns = explode("-", $dbRespCoIns);
							$strCoInsAmt = $arrRespCoIns[6];
							if(substr($strCoInsAmt,0,1) == "."){$strCoInsAmt = str_replace(".","",$strCoInsAmt); }
							
							if($intCopayAmt > 0){
								$strRTEAmtInfo .= "<span id=\"spCopay\">CoPay:&nbsp;$".$intCopayAmt."</span>&nbsp;&nbsp;";
							}
							else{
								$strRTEAmtInfo .= "<span id=\"spCopay\">CoPay:&nbsp;N/A</span>&nbsp;&nbsp;";
							}
							if($intDDCAmt > 0){
								$strRTEAmtInfo .= "<span id=\"spDDC\">DED:&nbsp;$".$intDDCAmt."</span>&nbsp;&nbsp;";
							}
							else{
								$strRTEAmtInfo .= "<span id=\"spDDC\">DED:&nbsp;N/A</span>&nbsp;&nbsp;";
							}
							if(empty($strCoInsAmt) == false){
								$strRTEAmtInfo .= "<span id=\"spCoins\">Co-Ins:&nbsp;".$strCoInsAmt."%</span>";
							}
							else{
								$strRTEAmtInfo .= "<span id=\"spCoins\">Co-Ins:&nbsp;N/A</span>";
							}
							
							if(($rowGetRealTimeData->vs270RespDate != "00-00-0000") && ($rowGetRealTimeData->vs270RespDate != "")){
								$vsToolTip .= "Date: ".$rowGetRealTimeData->vs270RespDate;
							}
							else{
								$vsToolTip .= "Date: N/A";
							}
							$vsToolTip .= " \t \t";
							
							if($rowGetRealTimeData->elOpName != ""){
								$vsToolTip .= "Opr: ".$rowGetRealTimeData->elOpNameIn;
							}
							else{
								$vsToolTip .= "Opr: N/A";
							}
							$vsToolTip .= "\n";
							
							if($rowGetRealTimeData->insCompName != ""){
								$dbInsCompName = $rowGetRealTimeData->insCompName;
							}
							else{
								$dbInsCompName = "N/A";
							}
							
							$dbInsCompName .= " \n";
							$vsToolTip .= $dbInsCompName;
							
							if($rowGetRealTimeData->policy != ""){
								$vsToolTip .= "Policy # ".$rowGetRealTimeData->policy;
							}
							else{
								$vsToolTip .= "Policy # "."N/A";
							}
							$vsToolTip .= " \n";
											
							if($intCopayAmt > 0){
								$vsToolTip .= "CoPay: $".$intCopayAmt;
							}
							else{
								$vsToolTip .= "CoPay: "."N/A";
							}
							$vsToolTip .= " \t";
							
							if($intDDCAmt > 0){
								$vsToolTip .= "DED: $".$intDDCAmt;
							}
							else{
								$vsToolTip .= "DED: "."N/A";
							}
							$vsToolTip .= " \t";
							if(empty($strCoInsAmt) == false){
								$vsToolTip .= "Co-Ins: ".$strCoInsAmt."%";
							}
							else{
								$vsToolTip .= "Co-Ins: "."N/A";
							}
							$vsToolTip .= " \n";
							
							if(($rowGetRealTimeData->vs270RespDate != "00-00-0000") && ($rowGetRealTimeData->vs270RespDate != "")){
								$vsStatusDate = "Last Check Dt.:".$rowGetRealTimeData->vs270RespDate;
							}		
							$dbRespXMLPath = "";
							if($rowGetRealTimeData->elAsk == 0){
								$dbRespXMLPath = $rowGetRealTimeData->respXMLPath;
								list($intTotDeductibleAmt, $intTotCopayAmt, $arrPatCopay) = $OBJEligibility->getTotalAmt271Response($dbRespXMLPath);											
							}
							elseif($rowGetRealTimeData->elAsk == 1){
								$dbRespXMLPath = $include_root."/main/uploaddir/".$rowGetRealTimeData->respXMLPath;
								list($intTotDeductibleAmt, $intTotCopayAmt, $arrPatCopay) = $OBJEligibility->getTotalAmt271Response($dbRespXMLPath);
							}
							if(empty($insDataArr["primary"]["copay"]) == false){
								$patCopayIns = 0;
								$patCopayIns = (float)$insDataArr["primary"]["copay"];
								$temp_patCopayIns = explode('.',$patCopayIns);
								if($temp_patCopayIns['1']=='00'){$patCopayIns = $temp_patCopayIns['0'];}
								if(in_array($patCopayIns, $arrPatCopay) == false){
									$RTE_COPAY_MISMATCH_ALERT = 1;
								}
								else{
									$RTE_COPAY_MISMATCH_ALERT = 0;
								}
							}									
							if($rowGetRealTimeData->vsTransectionError != ""){
								$vsStatus = "Error: ".$vsStatusDate;
								$strBy = "BY: ".$rowGetRealTimeData->elOpName;
								$vsTran = "error";
								$imgRealTimeEli = "<a id=\"anchorEligibility\" href=\"javascript:void(0);\" class=\"text_10b_purpule\" title=\"$vsToolTip\" onclick=\"getRealTimeEligibilityCI('".$insurance_dataId."','".$askElFrom."', '".$GLOBALS['webroot']."', '".$sch_id."', '".$strAppDate."', '".$intClientWindowH."');\">";
								$imgRealTimeEli .= "<img id=\"imgEligibility\" src=\"../../../library/images/eligibility_red.png\" border=\"0\"/>";
								$imgRealTimeEli .= "</a>&nbsp;";
							}
							elseif($rowGetRealTimeData->vsEBLoopResp != ""){									
								$strEBResponce = $objCoreLang->get_vocabulary("vision_share_271", "EB", (string)trim($rowGetRealTimeData->vsEBLoopResp));
								$vsStatus = $strEBResponce;
								$strBy = "BY: ".$rowGetRealTimeData->elOpName;
								$vsTran = "sucss";
								if(($rowGetRealTimeData->vsEBLoopResp == "6") || ($rowGetRealTimeData->vsEBLoopResp == "7") || ($rowGetRealTimeData->vsEBLoopResp == "8") || ($rowGetRealTimeData->vsEBLoopResp == "V")){
									$vsTran = "error";
									$imgRealTimeEli = "<a id=\"anchorEligibility\" href=\"javascript:void(0);\" class=\"text_10b_purpule\" title=\"$vsToolTip\" onclick=\"getRealTimeEligibilityCI('".$insurance_dataId."','".$askElFrom."', '".$GLOBALS['webroot']."', '".$sch_id."', '".$strAppDate."', '".$intClientWindowH."');\">";
									$imgRealTimeEli .= "<img id=\"imgEligibility\" src=\"../../../library/images/eligibility_red.png\" border=\"0\"/>";
									$imgRealTimeEli .= "</a>&nbsp;";
								}
								else{
									$imgRealTimeEli = "<a id=\"anchorEligibility\" href=\"javascript:void(0);\" class=\"text_10b_purpule\" title=\"$vsToolTip\" onclick=\"getRealTimeEligibilityCI('".$insurance_dataId."','".$askElFrom."', '".$GLOBALS['webroot']."', '".$sch_id."', '".$strAppDate."', '".$intClientWindowH."');\">";
									$imgRealTimeEli .= "<img id=\"imgEligibility\" src=\"../../../library/images/eligibility_green.png\" border=\"0\"/>";
									$imgRealTimeEli .= "</a>&nbsp;";
								}
							}
							$streElTable = "<table style=\"border:0px; border-spacing:1px; width:100%; padding:0px; font-size:13px; margin-top:-4px;\"><tr><td>".$vsStatus."&nbsp;</td><td>".$vsStatusDate."</td></tr><tr><td>".$strBy."</td><td>".$strRTEAmtInfo."</td></tr></table>";
						}
						else{
							$imgRealTimeEli = "<a id=\"anchorEligibility\" href=\"javascript:void(0);\" class=\"text_10b_purpule\" title=\"$vsToolTip\" onclick=\"getRealTimeEligibilityCI('".$insurance_dataId."','".$askElFrom."', '".$GLOBALS['webroot']."', '".$sch_id."', '".$strAppDate."', '".$intClientWindowH."');\">";
							$imgRealTimeEli .= "<img id=\"imgEligibility\" src=\"../../../library/images/eligibility_blue.png\" border=\"0\" title=\"Realtime Medicare Eligibility Request\" />";
							$imgRealTimeEli .= "</a>&nbsp;";			
						}
					}
				}
				else{
					$imgRealTimeEli = "<a id=\"anchorEligibility\" href=\"javascript:void(0);\" class=\"text_10b_purpule\" title=\"$vsToolTip\" onclick=\"getRealTimeEligibilityCI('".$insurance_dataId."','".$askElFrom."', '".$GLOBALS['webroot']."', '".$sch_id."', '".$strAppDate."', '".$intClientWindowH."');\">";
					$imgRealTimeEli .= "<img id=\"imgEligibility\" src=\"../../../library/images/eligibility_blue.png\" border=\"0\" title=\"Realtime Medicare Eligibility Request\" />";
					$imgRealTimeEli .= "</a>&nbsp;";			
				}
			}
		}
		//##
		//To Get Previous Data Of RTE		
		$daysBetween = 0;
		$intDDCAmtPre = $intCopayAmtPre = "";
		$strCoInsAmtPre = "";
		$rtme_id = "";
		$qryGetRTEAmountPrevious = "select rtme.id,rtme.response_deductible, rtme.response_copay, rtme.response_co_insurance,DATE_FORMAT(rtme.responce_date_time, '%Y-%m-%d') as respDate,rtme.xml_271_responce as respXMLPath, rtme.eligibility_ask_from  as elAsk 
								from real_time_medicare_eligibility rtme LEFT JOIN users us on us.id = rtme.request_operator
								where rtme.patient_id = '".$patient_id."'
								and rtme.ins_data_id = '".$insurance_dataId."' 
								and rtme.EB_responce != '' and rtme.transection_error = ''
								order by rtme.responce_date_time desc ";
		if($intRTMEid == 0){
			$qryGetRTEAmountPrevious .= "limit 1";
		}
		else{
			$qryGetRTEAmountPrevious .= "limit 0, 1";
		}
		$qryGetRTEAmountPrevious;
		$rsGetRTEAmountPrevious = imw_query($qryGetRTEAmountPrevious);
		
		if($rsGetRTEAmountPrevious){
			if(imw_num_rows($rsGetRTEAmountPrevious)>0){
				$rowGetRTEAmountPrevious = imw_fetch_object($rsGetRTEAmountPrevious);	
				
				if(empty($rowGetRTEAmountPrevious->id) == false){
					$rtme_id = $rowGetRTEAmountPrevious->id;
				}
				if(empty($rowGetRTEAmountPrevious->response_deductible) == false){			
					$dbRespDDCPre = $rowGetRTEAmountPrevious->response_deductible;
					$arrRespDDCPre = explode("-", $dbRespDDCPre);
					$intDDCAmtPre = (int)$arrRespDDCPre[4];
				}
				if(empty($rowGetRTEAmountPrevious->response_copay) == false){			
					$dbRespCopayPre = $rowGetRTEAmountPrevious->response_copay;
					$arrRespCopayPre = explode("-", $dbRespCopayPre);
					$intCopayAmtPre = (float)$arrRespCopayPre[4];
				}
				if(empty($rowGetRTEAmountPrevious->response_co_insurance) == false){			
					$dbRespCoInsPre = $rowGetRTEAmountPrevious->response_co_insurance;
					$arrRespCoInsPre = explode("-", $dbRespCoInsPre);
					$strCoInsAmtPre = $arrRespCoInsPre[6];
				//	if(substr($strCoInsAmtPre,0,1) == "."){ $strCoInsAmtPre = str_replace(".", "", $strCoInsAmtPre); }
					$strCoInsAmtPre  = (float)$strCoInsAmtPre * 100;
				}
				$dbRespXMLPath = "";
				if($rowGetRTEAmountPrevious->elAsk == 0){
					$dbRespXMLPath = $rowGetRealTimeData->respXMLPath;
					list($intTotDeductibleAmt, $intTotCopayAmt, $arrPatCopay) = $OBJEligibility->getTotalAmt271Response($dbRespXMLPath);											
				}
				elseif($rowGetRTEAmountPrevious->elAsk == 1){
					$dbRespXMLPath = $include_root."/main/uploaddir/".$rowGetRealTimeData->respXMLPath;
					list($intTotDeductibleAmt, $intTotCopayAmt, $arrPatCopay) = $OBJEligibility->getTotalAmt271Response($dbRespXMLPath);
				}
				
				if(empty($insDataArr["primary"]["copay"]) == false){
					$patCopayIns = 0;
					$patCopayIns = (float)$insDataArr["primary"]["copay"];
					$temp_patCopayIns = explode('.',$patCopayIns);
					if($temp_patCopayIns['1']=='00'){$patCopayIns = $temp_patCopayIns['0'];}					
					if(in_array($patCopayIns, $arrPatCopay) == false){
						$RTE_COPAY_MISMATCH_ALERT = 1;
					}
					else{
						$RTE_COPAY_MISMATCH_ALERT = 0;
					}
				}			
				if((empty($rowGetRTEAmountPrevious->respDate) == false) && ((empty($intDDCAmtPre) == false) || (empty($intCopayAmtPre) == false) || (empty($strCoInsAmtPre) == false))){
					$startDate = strtotime($rowGetRTEAmountPrevious->respDate);
					$endDate = strtotime($strAppDate);
					$daysBetween = ceil(abs($endDate - $startDate) / 86400);
				}
				if((empty($rowGetRTEAmountPrevious->respDate) == false)){
					$startDate = strtotime($rowGetRTEAmountPrevious->respDate);
					$endDate = strtotime(date('Y-m-d'));
					$rte_days = ceil(abs($endDate - $startDate) / 86400);
				}
			}
		}
		// Get Rte Valid days Range
				$query = "select RTEValidDays from copay_policies";
				$result = imw_query($query) or die(imw_error);
				while($row = imw_fetch_array($result)){
					$valid_days  = $row["RTEValidDays"];
				}
				if($rte_days <= $valid_days){
					$date_remain = "YES";
				}else{
					$date_remain = "NO";
				}
		//echo $daysBetween."--".$intRTEValidDays;
		
		$IMGREALTIMEELI = $imgRealTimeEli;
		$RTME_ID = $rtme_id;
		$VSSTATUS = $streElTable;
		$VSTRAN = $vsTran;
		$VSRESPDATECOMP = $rowGetRealTimeData->vsRespDate;
		$VSTOOLTIP = $vsToolTip;
		$RTE_COPAY_AMT = $intCopayAmt;
		$REPORT_WINDOW = $_SESSION['wn_height'] - 140;
		$RTE_DDC_AMT = $intDDCAmt;
		$RTE_COINS_AMT = $strCoInsAmt;
		
		$RTE_COPAY_AMT_PRE = $intCopayAmtPre;
		$RTE_DDC_AMT_PRE = $intDDCAmtPre;
		$RTE_COINS_AMT_PRE = $strCoInsAmtPre;
		
		if($daysBetween > $intRTEValidDays){
			$RTE_OUT_DAYS_STATUS_COLOR = 'red';
			//$objManageData->Smarty->assign("RTE_OUT_DAYS_STATUS_COLOR", "red");
		}
		elseif(($daysBetween <= $intRTEValidDays) && ((empty($intCopayAmtPre) == false) || (empty($intDDCAmtPre) == false) || (empty($strCoInsAmtPre) == false))){
			$RTE_OUT_DAYS_STATUS_COLOR = 'green';
			//$objManageData->Smarty->assign("RTE_OUT_DAYS_STATUS_COLOR", "green");
		}
	}
		
	$insDataArr[$ins_type]['ins_com_id'] = $insQryRes[$i]['provider'];
	$insDataArr[$ins_type]['ins_name'] = $ins_name;
	$referal_required = '';
	if($insQryRes[$i]['auth_required'] == 'Yes'){
		if(strtolower($ins_type) == 'primary'){
			$primary_auth_required_dis = 'block';
		}
		if(strtolower($ins_type) == 'secondary'){
			$secondary_auth_required_dis = 'block';
		}
	}
	
	if($insQryRes[$i]['referal_required'] == 'Yes'){
		$referal_required = 'selected';
		$referal_required_dis = 'block';
		
		if(strtolower($ins_type) == 'primary'){
			$insType1 = '1';
			$primary_referal_required_dis = 'block';
		}
		if(strtolower($ins_type) == 'secondary'){
			$insType1 = '2';
			$secondary_referal_required_dis = 'block';
		}
		
		//--- GET REFERRAL DATA ---		
		$ref_query = "select reff_id ,reff_phy_id,reffral_no,reff_used, no_of_reffs, 
					date_format(patient_reff.effective_date, '".get_sql_date_format()."') as effective_date,
		 			date_format(patient_reff.end_date, '".get_sql_date_format()."') as end_date 
					from patient_reff
					where patient_id = '$patient_id' and ins_data_id = '$insurance_dataId'
					and reff_type = '$insType1' order by reff_id desc limit 0,1";					
		$refQryRes = mysqlifetchdata($ref_query);
		$reff_phy_id = $refQryRes[0]["reff_phy_id"];
		$qry_ref_phy = "select TRIM(CONCAT(LastName, ', ', FirstName, ' ', MiddleName, if(MiddleName!='',' ',''),Title)) as refName from refferphysician where  physician_Reffer_id =".$reff_phy_id;
		$res_ref_phy = imw_query($qry_ref_phy);
		$row_ref_phy = imw_fetch_assoc($res_ref_phy);
		//$pri_ref_phy = preg_replace('/\'/','',$refPhyDataArr[$reff_phy_id]);
		//$pri_ref_phy = $row_ref_phy['refName'];
		$pri_ref_phy = $OBJCommonFunction->get_ref_phy_name($reff_phy_id);
		$totalVisists = $refQryRes[0]["no_of_reffs"] + $refQryRes[0]["reff_used"];
		$totalVisists = $totalVisists ."/". $refQryRes[0]["reff_used"];
		if($refQryRes[0]["reff_id"]){
			$insDataArr[$ins_type]['reff_id'] = $refQryRes[0]["reff_id"];
			$insDataArr[$ins_type]['pri_ref_phy_id'] = $reff_phy_id;
			$insDataArr[$ins_type]['pri_ref_phy'] = $pri_ref_phy;
			$insDataArr[$ins_type]['pri_ref_visits'] = $totalVisists;
			$insDataArr[$ins_type]['reffral_no'] = $refQryRes[0]["reffral_no"];
			$insDataArr[$ins_type]['pri_ref_stDt'] = $refQryRes[0]["effective_date"];
			$insDataArr[$ins_type]['pri_ref_enDt'] = $refQryRes[0]["end_date"];
		}
	}
	
	$insDataArr[$ins_type]['referal_required'] = $referal_required;
	$insDataArr[$ins_type]['referal_required_dis'] = $referal_required_dis;
	
	//--- SCAN DOCUMENTS -----
	$scan_img_wid = '';
	$scan_img_src = '';
	if(trim($insQryRes[$i]['scan_card']) != ''){
		$firstScanImage = $updir.$insQryRes[$i]['scan_card'];
		$firstScanImageNew = $srcDir.$insQryRes[$i]['scan_card'];
		if(realpath($firstScanImage) != ''){
			$scan_img_wid = newImageResize($firstScanImage,35,35);
			$data_src = $firstScanImageNew;
			$ext = pathinfo($firstScanImageNew, PATHINFO_EXTENSION);
			$ext = strtolower($ext);
			if( $ext == 'pdf' ) {
				$firstScanImageNew = $GLOBALS['webroot'].'/library/images/pdfimg.png';
				$scan_img_wid = 'width="21" height="21" ';
			}
			$scan_img_src = "<img onClick=\"show_scanned(this)\" src=\"".$firstScanImageNew."\" $scan_img_wid data-src=\"".$data_src."\" title=\"".ucfirst($ins_type)." Scanned Document\" style=\"cursor:pointer;max-height:21px!important; border:solid 1px #ccc;\" />";
		}
	}
	$insDataArr[$ins_type]['scan_card'] = $scan_img_src;
	
	//--- SECOND SCAN DOCUMENTS -----
	$scan2_img_wid = '';
	$scan2_img_src = '';
	if(trim($insQryRes[$i]['scan_card2']) != ''){
		$secondScanImage = $updir.$insQryRes[$i]['scan_card2'];
		$secondScanImageNew = $srcDir.$insQryRes[$i]['scan_card2'];
		if(realpath($secondScanImage) != ''){
			$scan2_img_wid = newImageResize($secondScanImage,35,35);
			$data_src2 = $secondScanImageNew;
			$ext = pathinfo($secondScanImage, PATHINFO_EXTENSION);
			$ext = strtolower($ext);
			if( $ext == 'pdf' ) {
				$secondScanImageNew = $GLOBALS['webroot'].'/library/images/pdfimg.png';
				$scan2_img_wid = 'width="21" height="21" ';
			}
			
			$scan2_img_src = "<img onClick=\"show_scanned(this)\" src=\"".$secondScanImageNew."\" $scan2_img_wid data-src=\"".$data_src2."\" title=\"".ucfirst($ins_type)." Scanned Document\" style=\"cursor:pointer;max-height:21px!important; border:solid 1px #ccc;\" />";
		}
	}
	$insDataArr[$ins_type]['scan_card2'] = $scan2_img_src;
	if(strtolower($ins_type) == 'primary'){
		$arrInsSwapData[] = array("ins_case_id" => $current_caseid, "insData" => array("insType" => "Primary", "insDataId" => $insurance_dataId, "providerId" => $insQryRes[$i]['ins_comp_id'], "providerName" => $insQryRes[$i]['name']));
	}
	elseif(strtolower($ins_type) == 'secondary'){
		$arrInsSwapData[] = array("ins_case_id" => $current_caseid, "insData" => array("insType" => "Secondary", "insDataId" => $insurance_dataId, "providerId" => $insQryRes[$i]['ins_comp_id'], "providerName" => $insQryRes[$i]['name']));
	}
}
//pre($arrInsSwapData, 1);
//--- REFERRAL REQUIRED DISPLAY ---
//$objManageData->Smarty->assign("primary_referal_required_dis",$primary_referal_required_dis);
//$objManageData->Smarty->assign("secondary_referal_required_dis",$secondary_referal_required_dis);

//--- AUTH REQUIRED DISPLAY ---
//$objManageData->Smarty->assign("primary_auth_required_dis",$primary_auth_required_dis);
//$objManageData->Smarty->assign("secondary_auth_required_dis",$secondary_auth_required_dis);

//------ GET AUTH NUMBER DATA ------------
$authQryRes = mysqlifetchdata("SELECT auth_name,a_id,AuthAmount,ins_type,
									date_format(auth_date,'%m-%d-%Y') as auth_date ,
									date_format(end_date,'%m-%d-%Y') as end_date,
									no_of_reffs,reff_used
								FROM patient_auth 
								WHERE ins_type != '3' 
										AND patient_id = '$patient_id' 
										AND ins_case_id = '$current_caseid' 
										AND auth_status = '0' 
										AND ((end_date >= current_date() || end_date = '0000-00-00') 
												AND (reff_used < no_of_reffs OR (no_of_reffs = 0 AND reff_used = 0))
											)
								ORDER BY a_id desc");
if(count($authQryRes)>0){
	for($i=0;$i<count($authQryRes);$i++){
		$ins_auth_type = $authQryRes[$i]['ins_type'];
		$authDataArr[$ins_auth_type]['auth_name'][] = $authQryRes[$i]['auth_name'];
		$authDataArr[$ins_auth_type]['AuthAmount'][] = $authQryRes[$i]['AuthAmount'];
		if($authQryRes[$i]['auth_date'] == '00-00-0000'){
			$authQryRes[$i]['auth_date'] = '';
		}
		$authDataArr[$ins_auth_type]['auth_date'][] = $authQryRes[$i]['auth_date'];
		
		//--------------------INSURANCE AUTH END DATE AND VISIT CHANGES---------------------
		if($authQryRes[$i]['end_date'] == '00-00-0000'){
			$authQryRes[$i]['end_date'] = '';
		}
		$authDataArr[$ins_auth_type]['end_date'][] 	= $authQryRes[$i]['end_date'];
		$authDataArr[$ins_auth_type]['a_id'][] 		= $authQryRes[$i]['a_id'];
		
		if($authQryRes[$i]['no_of_reffs'] + $authQryRes[$i]['reff_used'] == '0'){
			$auth_visit_value="";
		}
		else{
			if($authQryRes[$i]['reff_used'] >0)
			$auth_visit_value	=	$authQryRes[$i]['no_of_reffs']  .'/'.$authQryRes[$i]['reff_used'];
			else
			$auth_visit_value	=	$authQryRes[$i]['no_of_reffs'];
		}	
		$authDataArr[$ins_auth_type]['visits'][] = $auth_visit_value;	
		//----------------------------------------------------------------------------------
		if($authQryRes[$i]['ins_type'] == '1'){
			$auth_pri_arr[] = array($authQryRes[$i]['auth_name'],"", $authQryRes[$i]['auth_name']);
		}else if($authQryRes[$i]['ins_type'] == '2'){
			$auth_sec_arr[] = array($authQryRes[$i]['auth_name'],"", $authQryRes[$i]['auth_name']);
		}
	}
}

//$objManageData->Smarty->assign("authDataPriArr",$auth_pri_arr);
//$objManageData->Smarty->assign("authDataSecArr",$auth_sec_arr);
//$objManageData->Smarty->assign("authDataDetailArr",$authDataArr);

//$objManageData->Smarty->assign("insComDetailsArr",$insDataArr);
//pre($insDataArr, 1);
$insDisabled = '';
if($self_pay_provider != ''){
	$self_pay_provider_val = 'checked';
	$insDisabled = 'disabled';
}

//$objManageData->Smarty->assign("insDisabled",$insDisabled);
//$objManageData->Smarty->assign("self_pay_provider_val",$self_pay_provider_val);

$totalCopayDilatedAmt = array_sum($copayAmtDilatedArr);
$totalCopayNonDilatedAmt = array_sum($copayAmtNonDilatedArr);

$totalCopayTestDilatedAmt = array_sum($collect_copay_dilated_test_amt);
$totalCopayTestNonDilatedAmt = array_sum($collect_copay_non_dilated_test_amt);

$secDisabled = '';
if($insDataArr['primary']['MAIN_ID'] == '' || $self_pay_provider != ''){
	$secDisabled = 'disabled';
}
//$objManageData->Smarty->assign("secDisabled",$secDisabled);

// MAKE ARRAY FOR RACE
/*
$arrRace = array(
"American Indian or Alaska Native" => "American Indian or Alaska Native",
"Asian" => "Asian",
"Black or African American" => "Black or African American",
"Native Hawaiian or Other Pacific Islander" => "Native Hawaiian or Other Pacific Islander",
"White" => "White",
"Other" => "Other"
);
*/
$arrRace = $demo->race_modal(1);

//$arrTemp = array(trim($patientQryRes[0]["race"]),"Other",trim($rowGetPatientData["otherRace"]));
//$raceClass = core_get_prac_field_css_class($arrPracFieldStatus,"race",$arrTemp);
//$raceClassOnKeyUp = core_get_prac_field_css_class($arrPracFieldStatus,"race");
//$objManageData->Smarty->assign("race_arr", $arrRace);
//$objManageData->Smarty->assign("raceClass",$raceClass);
//$objManageData->Smarty->assign("raceClassOnKeyUp",$raceClassOnKeyUp);
// MAKE ARRAY FOR ATHNICITY

$arrEthnicity = $demo->ethnicity_modal(1);
//$arrTemp = array(trim($patientQryRes[0]["ethnicity"]),"Other",trim($$patientQryRes[0]["otherEthnicity"]));

$fromFile='demographics';
include_once("../../common/practice_mandotry_field.php");

$mandatory_fields_row_get=array();
foreach($mandatory_fields_row as $m_key=> $mend_val){
	if(($mend_val==2) && !is_numeric($m_key)){
		$mandatory_fields_row_get[$m_key]=$mend_val;
	}
}

$mandatory_arr_js=json_encode($mandatory_fields_row_get);


//$ethnicityClass = core_get_prac_field_css_class($arrPracFieldStatus,"ethnicity",$arrTemp);
//$ethnicityClassOnKeyUp = core_get_prac_field_css_class($arrPracFieldStatus,"ethnicity");
//$objManageData->Smarty->assign("arrEthnicity", $arrEthnicity);
//$objManageData->Smarty->assign("ethnicityClass",$ethnicityClass);
//$objManageData->Smarty->assign("ethnicityClassOnKeyUp",$ethnicityClassOnKeyUp);

// MAKE ARRAY FOR LANGUAGE
/*$arrLang = array('English','Spanish','French','German','Russian','Japanese','Portuguese','Italian');
sort($arrLang);
$arrLang[] = 'Declined to Specify';

foreach($arrLang as $lang)
{
	$arrLanguage[$lang]=$lang;
}
$arrLanguage['Other'] = 'Other';*/
$arrLanguage = $demo->language_modal(1);
//$objManageData->Smarty->assign("arrLanguage", $arrLanguage);

$qry_lang="SELECT name from pt_languages WHERE del_status!=1";
$res_lang=imw_query($qry_lang);
$lang_arr=array();
while($lang_val=imw_fetch_assoc($res_lang)){
	$lang_arr[]=$lang_val['name'];
}
//$languageTypeAhead = join(',',$lang_arr);
$languageTypeAhead=json_encode($lang_arr);
//$objManageData->Smarty->assign("languageTypeAhead", $languageTypeAhead);

$showViewPaymentsRow = 'none';
$showViewPayments = false;
if($polciesQryRes[0]['rowCount'] > 0 && $patient_id>0 && $_REQUEST['sch_id']>0){
	$showViewPaymentsRow = 'block';
	$showViewPayments = true;
}
//$objManageData->Smarty->assign("showViewPaymentsRow",$showViewPaymentsRow);
//$objManageData->Smarty->assign("showViewPayments",$showViewPayments);

$top_ci_header='CI-'.get_date_format(date('Y-m-d')).' '.date('h:i A');
//$objManageData->Smarty->assign("top_ci_header",$top_ci_header);
//$objManageData->Smarty->assign("ciBy",$operatorName);


// TO Make an array of an Group Name 
$group_name = array();
$qry_group = "select gro_id , name from groups_new where del_status='0'";
$res_group = imw_query($qry_group) or die(imw_error());
while($row_group = imw_fetch_array($res_group)){
		$disp_name = $row_group["name"];
		$disp_name_arr = explode(' ', $disp_name);
		$disp_name="";
		for($i=0;$i<count($disp_name_arr);$i++){
			$disp_name .= substr($disp_name_arr[$i],0,1);
		}
		$group_name[$row_group["gro_id"]] = $disp_name;
}
//--- SET TOTAL CHARGES ---

	$pclBillQuery = "SELECT gro_id,charge_list_id, SUM(totalAmt) as pclBillCharges, sum(insuranceDue) as Insurance_Due, SUM(approvedTotalAmt) as allowable_charges,
					SUM(patientDue) as today_patientDue,sum(copay) as today_copay,
					sum(deductibleTotalAmt) as today_deduct,primaryInsuranceCoId,secondaryInsuranceCoId
					FROM patient_charge_list WHERE del_status='0' and date_of_service='".$sa_app_start_date."' AND patient_id='".$patient_id."' group by gro_id";
	$pclBillResult = imw_query($pclBillQuery); 
	$total_group = imw_num_rows($pclBillResult);
	if($pclBillResult && imw_num_rows($pclBillResult)>0){
	$today_charges = array();	
		while($pclBillRow = imw_fetch_array($pclBillResult)){
			
			
			$today_charges["Patient_Due"][] = $pclBillRow["today_patientDue"];
			$today_charges["Insurance_Due"][] = $pclBillRow["Insurance_Due"];
			$today_charges["gro_id"][] = $pclBillRow["gro_id"];
			
			$primaryInsurance_chld =  $pclBillRow['primaryInsuranceCoId'];
			$secondaryInsurance_chld =  $pclBillRow['secondaryInsuranceCoId'];	
			$charge_list_id =  $pclBillRow['charge_list_id'];	
			$pracCodeCharges=0;
			$chld_qry=imw_query("select procCode,units from patient_charge_list_details where del_status='0' and charge_list_id='$charge_list_id'");
			while($chld_row=imw_fetch_array($chld_qry)){
				$procCode =  $chld_row['procCode'];	
				$units =  $chld_row['units'];	
				$getCptFeeDetailsStr = "SELECT cpt_prac_code FROM cpt_fee_tbl WHERE cpt_fee_id = '$procCode' AND delete_status = '0'";
				$getCptFeeDetailsQry = imw_query($getCptFeeDetailsStr);
				$getCptFeeDetailsRow = imw_fetch_array($getCptFeeDetailsQry);
				$cptPracCode = $getCptFeeDetailsRow['cpt_prac_code'];
				
				$pracCodePrize = get_contract_fee($cptPracCode,$primaryInsurance_chld);
				$pracCodeCharges += $pracCodePrize*$units;
			}
			$today_charges["allowable_charges"][] = $pracCodeCharges;
			//$pclBillRs = imw_fetch_array($pclBillResult);
			$totalVisitCharges +=  $pracCodeCharges;
			$total_allowable_charges +=  $pracCodeCharges;
			//$total_today_patientDue =  $pclBillRs['today_patientDue'];
			$total_today_copay +=  $pclBillRow['today_copay'];
			//$total_today_deduct =  $pclBillRs['today_deduct'];   
		
		}		
	}
//} 
//$objManageData->Smarty->assign("group_name", $group_name);
//$objManageData->Smarty->assign('Today_Charges', numberFormat($total_allowable_charges,2,"yes"));

// Code For Previous Charges of Patient 
$previous_data = array();
$pclBillQuery = "SELECT sum(totalBalance) as grp_totalBalance,sum(patientDue) as grp_patientDue, sum(insuranceDue) as grp_insuranceDue, sum(overPayment) as grp_overPayment,gro_id
					FROM patient_charge_list WHERE del_status='0' and date_of_service<'".$sa_app_start_date."' AND patient_id='".$patient_id."'
					group by gro_id";
	$pclBillResult = imw_query($pclBillQuery);
	while($pclBillRs = imw_fetch_array($pclBillResult)){
		$previous_data[$pclBillRs["gro_id"]]["Patient_Due"][] = $pclBillRs["grp_patientDue"];
		$previous_data[$pclBillRs["gro_id"]]["Insurance_Due"][] = $pclBillRs["grp_insuranceDue"];
		$patient_total_due +=  $pclBillRs["grp_patientDue"];
	}


if($showViewPayments == true){
	if($patient_id != ''){
		//--- PATIENT TOTAL DUE AMOUNT ----
		$patDueQry = "select sum(patientDue) as patientDue,sum(totalBalance) as pat_totalBalance,sum(overPayment) as pat_overPayment from patient_charge_list where del_status='0' and patient_id = '$patient_id' and date_of_service<'".$sa_app_start_date."'";
		$patDueQryRes = mysqlifetchdata($patDueQry);
		$pat_totalBalance=$patDueQryRes[0]['pat_totalBalance'];
		$pat_overPayment=$patDueQryRes[0]['pat_overPayment'];
		$pat_totalBalance_final=$patDueQryRes[0]['pat_totalBalance']-$patDueQryRes[0]['pat_overPayment'];
	}
	$pclBillQuery = "SELECT SUM(patientDue) as prev_patientDue FROM patient_charge_list WHERE del_status='0' and date_of_service<'$sa_app_start_date' AND patient_id='$patient_id'";
	$pclBillResult = imw_query($pclBillQuery); 
	if($pclBillResult && imw_num_rows($pclBillResult)>0){
		$pclBillRs = imw_fetch_array($pclBillResult);
		$total_prev_patientDue =  $pclBillRs['prev_patientDue'];
	}

	//--- GET CHECK IN FIELDS ----
	$check_in_fields_qry = "select * from check_in_out_fields where item_name != '' and item_show > '0'";
	$checkInFieldsQryRes = mysqlifetchdata($check_in_fields_qry);
	$itemNameArr = array();
	for($i=0;$i<count($checkInFieldsQryRes);$i++){
		$itemNameArr[] = "'".$checkInFieldsQryRes[$i]['item_name']."'";
	}
	$itemNameStr = join(',',$itemNameArr);
	$fee_table_column_id=1;
	if($insDataArr["primary"]["FeeTable"]>1){
		$fee_table_column_id=$insDataArr["primary"]["FeeTable"];
	}
	//--- GET PROCEDURE AMOUNT ---
	$procQuery = "select cpt_fee_table.cpt_fee, cpt_fee_tbl.cpt_prac_code, cpt_fee_tbl.cpt_desc, cpt_fee_table.cpt_fee_id
				 from cpt_fee_tbl join cpt_fee_table on cpt_fee_tbl.cpt_fee_id = cpt_fee_table.cpt_fee_id
				 where cpt_fee_table.fee_table_column_id = '$fee_table_column_id' 
				 and (cpt_fee_tbl.cpt_desc in($itemNameStr) or cpt_fee_tbl.cpt_prac_code in($itemNameStr))";
	$procQryRes = mysqlifetchdata($procQuery);
	$procAmtArr = array();
	for($i=0;$i<count($procQryRes);$i++){
		$cpt_desc = "'".$procQryRes[$i]['cpt_desc']."'";
		if(in_array($cpt_desc,$itemNameArr) === true){
			$itemName = preg_replace('/\'/','',trim($cpt_desc));
		}
		else{
			$itemName = trim($procQryRes[$i]['cpt_prac_code']);
		}
		if($itemName=="Refraction" && $refractionChk=='No'){
			$procAmtArr[$itemName] = 0;
		}else{
			$procAmtArr[$itemName] = $procQryRes[$i]['cpt_fee'];
		}
	}
	
	//--- SET TOTAL PAYMENTS ---
	$pay_query = "select check_in_out_payment.payment_id as main_pay_id, check_in_out_payment.total_payment, 
				check_in_out_payment.payment_method,check_in_out_payment.check_no, check_in_out_payment.ci_comments,
				check_in_out_payment.cc_type,check_in_out_payment.cc_no , check_in_out_payment.cc_expire_date, 
				check_in_out_payment_details.item_id, check_in_out_payment_details.item_payment,
				check_in_out_payment_details.payment_type,check_in_out_payment_details.id as pay_detail_id,
				check_in_out_payment_details.copay_type as copay_type
				from check_in_out_payment join check_in_out_payment_details on 
				check_in_out_payment_details.payment_id = check_in_out_payment.payment_id
				where check_in_out_payment.patient_id = '$patient_id' and check_in_out_payment.sch_id = '$sch_id'
				and check_in_out_payment.del_status = '0'
				and check_in_out_payment_details.status = '0'
				and check_in_out_payment_details.payment_type='checkin'";
		
	$paymentQryRes = mysqlifetchdata($pay_query);
	$total_payment_arr = array(0.00);
	$checkInPayArr = array();
	$payment_select = $paymentQryRes[0]["payment_method"];
	$main_pay_id = $paymentQryRes[0]["main_pay_id"];
	$payment_chk_number = $paymentQryRes[0]["check_no"];
	
	if(count($paymentQryRes)==0){
		$comm_qry=imw_query("select ci_comments from check_in_out_payment where patient_id = '$patient_id' and sch_id = '$sch_id' and payment_type='checkin' limit 0,1");
		$comm_row=imw_fetch_array($comm_qry);
		$paymentQryRes[0]["ci_comments"] = $comm_row['ci_comments'];
	}
	
	//$objManageData->Smarty->assign("edit_payment_tbl_id",$main_pay_id);
	//$objManageData->Smarty->assign("payment_chk_number",$payment_chk_number);
	//$objManageData->Smarty->assign("ci_comments_text",core_extract_user_input($paymentQryRes[0]["ci_comments"]));
	//--- SET CREDIT CARD DROP DOWN ---
	$cr_name_arr = array(""=>"");
	$cr_name_arr["AX"] = "American Express";
	$cr_name_arr["Care Credit"] = "Care Credit";
	$cr_name_arr["Dis"] = "Discover";
	$cr_name_arr["MC"] = "Master Card";
	$cr_name_arr["Visa"] = "Visa";
	$cr_name_arr["Others"] = "Others";
	//$objManageData->Smarty->assign("cr_options",$cr_name_arr);
	
	$creditCardNumber = $paymentQryRes[0]["cc_no"];
	if(empty($paymentQryRes[0]["cc_expire_date"]) === false){
		$cc_expire_date  = $paymentQryRes[0]["cc_expire_date"];
	}
	
	$cr_selected = $paymentQryRes[0]["cc_type"];
	$creditCardDate = $cc_expire_date;

	for($i=0;$i<count($paymentQryRes);$i++){
		$item_id = $paymentQryRes[$i]['item_id'];
		$main_pay_id = $paymentQryRes[$i]['main_pay_id'];
		$total_payment_arr[$main_pay_id] = $paymentQryRes[$i]["total_payment"];
		$total_payment_ci_arr[] = $paymentQryRes_ci[$i]["item_payment"];
		$checkInPayArr[$item_id] = $paymentQryRes[$i];
	}
	if($date_remain != "YES"){
		$total_today_copay="0";
	}
	else{
		if($intCopayAmtPre>0){
			$total_today_copay =$intCopayAmtPre;
		}
	}

	if($total_today_copay>0){
		$total_today_copay=$total_today_copay;
	}else{
		$total_today_copay=array_sum($rte_ins_copay);
	}
	
	if($date_remain != "YES"){
		$total_today_deduct="0";
	}
	else{
		$total_today_deduct=$intDDCAmtPre;
	}
	$copay_deduct_total=$total_today_copay+$total_today_deduct;
	if($date_remain != "YES"){
		$CoInsAmt = "0";		
		$co_ins_pat_exp=explode('/',$co_ins_pat);
		$visit_co_ins=$co_ins_pat_exp[1];
	}else{
		$CoInsAmt = $strCoInsAmtPre;
		if($strCoInsAmtPre>0){
			$visit_co_ins=$CoInsAmt;
		}else{
			$co_ins_pat_exp=explode('/',$co_ins_pat);
			$visit_co_ins=$co_ins_pat_exp[1];
		}
	}
	$pat_total_today_patientDue = 0;
	for($i=0;$i<count($total_group);$i++){
		$grp_allowable_charges =  $today_charges["allowable_charges"][$i];
		$grp_group_id =  $today_charges["gro_id"][$i];
		$grp_patient_due =  $today_charges["Insurance_Due"][$i];
		$grp_insurance_due = $today_charges["Patient_Due"][$i];
		if($copay_deduct_total >= $grp_allowable_charges){
			$pat_total_today_patientDue += $grp_allowable_charges;
		}else{
			if($secondaryInsurance_chld > 0){
				$pat_total_today_patientDue += $copay_deduct_total;
			}else{
				$pat_total_today_patientDue += $copay_deduct_total+($grp_allowable_charges*($visit_co_ins/100));
			}
		} 
	}
//	$objManageData->Smarty->assign("Previous_Data", $previous_data);
	$total_balance = $pat_total_today_patientDue + $total_prev_patientDue; 
	
	$total_amt_arr = array();
	for($i=0;$i<count($checkInFieldsQryRes);$i++){
		$item_name = strtolower($checkInFieldsQryRes[$i]['item_name']);
		$copay_item_id=$checkInFieldsQryRes[$i]['id'];
		switch($item_name){
			case "copay-visit":
				$visitCopayId = "item_pay_".$copay_item_id;
				$total_amt_arr[] = $totalCopayDilatedAmt;
				$totalCopayAmt_final = str_replace(',','',number_format($totalCopayDilatedAmt,2));
				$totalCopayAmt_non_dilated_final = str_replace(',','',number_format($totalCopayNonDilatedAmt,2));
				$checkInFieldsQryRes[$i]['item_amt']['dilated'] = $totalCopayAmt_final;
				$checkInFieldsQryRes[$i]['item_amt']['nondilated'] = $totalCopayAmt_non_dilated_final;
				$checkInFieldsQryRes[$i]['item_name'] = ucfirst($item_name);
			break;
			case "copay":
				$visitCopayId = "item_pay_".$copay_item_id;
				$total_amt_arr[] = $totalCopayDilatedAmt;
				$totalCopayAmt_final = str_replace(',','',number_format($totalCopayDilatedAmt,2));
				$totalCopayAmt_non_dilated_final = str_replace(',','',number_format($totalCopayNonDilatedAmt,2));
				$checkInFieldsQryRes[$i]['item_amt']['dilated'] = $totalCopayAmt_final;
				$checkInFieldsQryRes[$i]['item_amt']['nondilated'] = $totalCopayAmt_non_dilated_final;
				$checkInFieldsQryRes[$i]['item_name'] = ucfirst($item_name);
			break;
			case "prv. t.bal":
				$prevBalId = "item_pay_".$copay_item_id;
				$totalPreBal_Amt_final = str_replace(',','',numberFormat($pat_totalBalance_final,2,"yes"));
				$total_amt_arr[] = $pat_totalBalance_final;
				$checkInFieldsQryRes[$i]['item_amt'] = $totalPreBal_Amt_final;
			break;
			case "copay-test":
				$testCopayId = "item_pay_".$copay_item_id;
				if($collect_copay_test == true){
					$total_amt_arr[] = $totalCopayTestDilatedAmt;
					$total_collect_dilated_amt = str_replace(',','',number_format($totalCopayTestDilatedAmt,2));
					$total_collect_non_dilated_amt = str_replace(',','',number_format($totalCopayTestNonDilatedAmt,2));
					$checkInFieldsQryRes[$i]['item_amt']['test_dilated'] = $total_collect_dilated_amt;
					$checkInFieldsQryRes[$i]['item_amt']['test_nondilated'] = $total_collect_non_dilated_amt;
					$checkInFieldsQryRes[$i]['item_name'] = ucfirst($item_name);
				}
				else{
					$checkInFieldsQryRes[$i]['item_amt'] = '-';
					$checkInFieldsQryRes[$i]['item_name'] = ucfirst($item_name);
				}
			break;
			
			case "deductible":
				$prevBalId = "item_pay_".$copay_item_id;
				$total_deduct_final = str_replace(',','',numberFormat($total_today_deduct,2,"yes"));
				$total_amt_arr[] = $total_today_deduct;
				$checkInFieldsQryRes[$i]['item_amt'] = $total_deduct_final;	
				
			break; 
			
			case "pt co-ins":
				$prevBalId = "item_pay_".$copay_item_id;
				$show_co_ins = $total_allowable_charges*($visit_co_ins/100);
				$totalCopayAmt_final = str_replace(',','',numberFormat($show_co_ins,2,"yes"));
				$total_amt_arr[] = $show_co_ins;
				$checkInFieldsQryRes[$i]['item_amt'] = $totalCopayAmt_final;
				
			break;  
			
			case "deposit":
				$checkInFieldsQryRes[$i]['item_amt'] = '-';
			break;
			
			case "pt balance":
				$prevBalId = "item_pay_".$copay_item_id;
				$totalPtBal_final = str_replace(',','',numberFormat($total_balance,2,"yes"));
				$total_amt_arr[] = $total_balance;
				$checkInFieldsQryRes[$i]['item_amt'] = $totalPtBal_final;
			break;
			
			default:
				$item_default_amt = '-';
				$item_name = $checkInFieldsQryRes[$i]['item_name'];
				if($procAmtArr[$item_name] > 0){
					$item_default_amt = str_replace(',','',numberFormat($procAmtArr[$item_name],2,"yes"));
				}
				$total_amt_arr[] = $procAmtArr[$item_name];
				$checkInFieldsQryRes[$i]['item_amt'] = $item_default_amt;
			break;
		}
		
		//--- CHECK IN / OUT ITEMS PAYMENT DETAILS ---
		$item_id = strtolower($checkInFieldsQryRes[$i]['id']);
		$chk_pay_det_arr = $checkInPayArr[$item_id];
		if(count($chk_pay_det_arr) > 0){
			$item_payment = str_replace(',','',number_format($chk_pay_det_arr['item_payment'],2));		
			$checkInFieldsQryRes[$i]['item_payment'] = $item_payment;
			$checkInFieldsQryRes[$i]['pay_detail_id'] = $chk_pay_det_arr['pay_detail_id'];
			$checkInFieldsQryRes[$i]['item_checked'] = 'checked';
			$checkInFieldsQryRes[$i]['copay_type'] = $chk_pay_det_arr['copay_type'];
		}	
	}
	
	/*--SETTING JS VARIABLES TO CARRY FIELD NAMES FOR COPAY AND PREVIOUS BALANCE FIELDS--*/
//	$objManageData->Smarty->assign("visitCopayId",$visitCopayId);
//	$objManageData->Smarty->assign("testCopayId",$testCopayId);
//	$objManageData->Smarty->assign("prevBalId",$prevBalId);
		
	//--- SET TOTAL CHARGES ---
	$pat_due_payment=$pat_total_today_patientDue+$total_prev_patientDue;
	//$objManageData->Smarty->assign("totalPreviousBal",str_replace(',','',number_format($pat_totalBalance_final,2)));
	//$objManageData->Smarty->assign("total_pat_due_payment",str_replace(',','',number_format($pat_due_payment,2)));
	
	//--- SET TOTAL PAYMENTS ---
	//$objManageData->Smarty->assign("total_payment",str_replace(',','',number_format(array_sum($total_payment_arr),2)));
	
	//--- SET PAYMENT METHOD ---
	$pay_method = array("Cash"=>"Cash","Check"=>"Check","Credit Card"=>"Credit Card","EFT"=>"EFT","Money Order"=>"Money Order");
	
	//$objManageData->Smarty->assign("payment_method",$pay_method);
	//$objManageData->Smarty->assign("payment_select",$payment_select);
	
	
	$selected_pay_method = array();
	for($i=0;$i<count($paymentQryRes);$i++){
		$item_id = $paymentQryRes[$i]['item_id'];
		$main_pay_id = $paymentQryRes[$i]['main_pay_id'];
		$total_payment_arr[$main_pay_id] = $paymentQryRes[$i]["total_payment"];
		if($paymentQryRes[$i]['payment_type']=='checkin'){
			$total_payment_co_arr[] = $paymentQryRes[$i]["item_payment"];
			$total_payment_ci_main_arr[$main_pay_id][]=$paymentQryRes[$i]["item_payment"];
		}else{
			$total_payment_ci_arr[] = $paymentQryRes[$i]["item_payment"];
		}
		$checkInPayArr[$item_id][$paymentQryRes[$i]['payment_type']] = $paymentQryRes[$i];
		$selected_pay_method[$paymentQryRes[$i]["item_id"]] = $paymentQryRes[$i]["payment_method"];
	}//pre($selected_pay_method,1);
	foreach($total_payment_arr as $key=>$val){
		if($key>0){
			$total_payment_ci_arr_sum=array_sum($total_payment_ci_main_arr[$key]);
			imw_query("update check_in_out_payment set total_payment='$total_payment_ci_arr_sum' where payment_id='$key'");
		}
	}
	//$objManageData->Smarty->assign("selected_pay_method",$selected_pay_method);
/*----*/
$checkRow = "none";
$creditCardRow = "none";

$query_get_saved_pay_types = "SELECT payment_id, total_payment, payment_method, check_no, cc_type, cc_no, cc_expire_date 
					FROM check_in_out_payment 
					WHERE patient_id='$patient_id' and sch_id='$sch_id' 
					AND del_status = '0' 
					AND payment_type='checkin'";
$res_get_saved_pay_types = imw_query($query_get_saved_pay_types);
if($res_get_saved_pay_types && imw_num_rows($res_get_saved_pay_types)>0){
	while($rs_get_saved_pay_types = imw_fetch_array($res_get_saved_pay_types)){
		if($rs_get_saved_pay_types['payment_method']=='Cash'){
			$edit_payment_tbl_id_cash = $rs_get_saved_pay_types['payment_id'];
			//$objManageData->Smarty->assign("edit_payment_tbl_id_cash",$rs_get_saved_pay_types['payment_id']);
		}else if($rs_get_saved_pay_types['payment_method']=='Check'){
			$rs_get_saved_pay_types['check_no'] = $rs_get_saved_pay_types['check_no'];
			$payment_chk_number = $rs_get_saved_pay_types['check_no'];
			$edit_payment_tbl_id_check = $rs_get_saved_pay_types['payment_id'];
			//$objManageData->Smarty->assign("edit_payment_tbl_id_check",$rs_get_saved_pay_types['payment_id']);
			if($rs_get_saved_pay_types['total_payment'] != '' && $rs_get_saved_pay_types['total_payment'] != '0.00'){$checkRow = "inline-block";}
		
		}else if($rs_get_saved_pay_types['payment_method']=='Credit Card'){
			$cr_selected = $rs_get_saved_pay_types["cc_type"];
			$creditCardNumber = $rs_get_saved_pay_types['cc_no'];
			$creditCardDate = $rs_get_saved_pay_types["cc_expire_date"];
			//$objManageData->Smarty->assign("cr_selected",$rs_get_saved_pay_types["cc_type"]);
			$edit_payment_tbl_id_card = $rs_get_saved_pay_types['payment_id'];
			//$objManageData->Smarty->assign("edit_payment_tbl_id_card",$rs_get_saved_pay_types['payment_id']);
			if($rs_get_saved_pay_types['total_payment'] != '' && $rs_get_saved_pay_types['total_payment'] != '0.00'){$creditCardRow = "inline-table";}
		
		}else if($rs_get_saved_pay_types['payment_method']=='EFT'){
			$payment_chk_number = $rs_get_saved_pay_types['check_no'];
			//$objManageData->Smarty->assign("edit_payment_tbl_id_eft",$rs_get_saved_pay_types['payment_id']);
			$edit_payment_tbl_id_eft = $rs_get_saved_pay_types['payment_id'];
			if($rs_get_saved_pay_types['total_payment'] != '' && $rs_get_saved_pay_types['total_payment'] != '0.00'){$checkRow = "inline-block";}
		}else if($rs_get_saved_pay_types['payment_method']=='Money Order'){
			$payment_chk_number = $rs_get_saved_pay_types['check_no'];
			//$objManageData->Smarty->assign("edit_payment_tbl_id_mo",$rs_get_saved_pay_types['payment_id']);
			$edit_payment_tbl_id_mo = $rs_get_saved_pay_types['payment_id'];
			if($rs_get_saved_pay_types['total_payment'] != '' && $rs_get_saved_pay_types['total_payment'] != '0.00'){$checkRow = "inline-block";}
		}
	}
}
//$objManageData->Smarty->assign("checkRow",$checkRow);
//$objManageData->Smarty->assign("creditCardRow",$creditCardRow);
/*----*/
	//--- SET CHECK IN / OUT FILEDS HTML ---
	//$objManageData->Smarty->assign("check_in_data",$checkInFieldsQryRes);
}
	//--SETTING HEARD ABOUT US
	$heardAbtUsRes = get_header_about_us(trim($patientQryRes[0]["heard_abt_us"]));
	$arrTypeHead = array();
	while($heardAbtVal = imw_fetch_array($heardAbtUsRes)){
		$demoHeard = get_demo_heard($heardAbtVal['heard_id']);
		while($rowDemo = imw_fetch_array($demoHeard)){	
			if($rowDemo['heard_desc'] != ''){
				$arrTypeHead[$heardAbtVal['heard_options']][] = $rowDemo['heard_desc'];
			}
		}
	}
	$heardAbtUs = get_header_about_us(trim($patientQryRes[0]["heard_abt_us"]));
	//$heardclass = core_get_prac_field_css_class($arrPracFieldStatus,"elem_heardAbtUs",trim($patientQryRes[0]["heard_abt_us"]));
	$patientQryRes[0]["heard_abt_desc"] = addslashes($patientQryRes[0]["heard_abt_desc"]);								
	//$objManageData->Smarty->assign("heardclass",$heardclass);

	$display_heardimg = "none";
	$sel ="";
	$forSelTypeAhed = "";
	$heard_select_options = '';
	if(trim($patientQryRes[0]["heard_abt_us"])=='' || trim($patientQryRes[0]["heard_abt_us"])=='0'){
		$display_heardtextarea = 'none';		
	}else{
		$display_heardtextarea = 'block';
	}
	while($rowHeardAbtUs = imw_fetch_array($heardAbtUs)) {
		if(trim($patientQryRes[0]["heard_abt_us"]) == trim($rowHeardAbtUs['heard_id'])){																
			$sel = ' selected="selected"';
			$forSelTypeAhed = $rowHeardAbtUs['heard_options'];
			$display_heardimg = "none";																						
		}else{
			$sel = "";																																		
			if($display_heardimg!="none"){
				$display_heardimg = "block";
			}
		}
		$heard_select_options .= '<option value="'.$rowHeardAbtUs['heard_id'].'-'.$rowHeardAbtUs['heard_options'].'"'.$sel.'>'.$rowHeardAbtUs['heard_options'].'</option>
		';
	}
$qry_lang="SELECT name,iso_639_2 from pt_languages WHERE del_status!=1";
$res_lang=imw_query($qry_lang);
$lang_arr=array();
while($lang_val=imw_fetch_assoc($res_lang)){
	$language_name=str_replace("'","",$lang_val['name']);
	if(strstr($lang_val['iso_639_2'],"/")){
		$keysBy=$lang_val['iso_639_2'];
		$kesVal=explode("/",$keysBy);
		for($k=0;$k<count($kesVal);$k++){
			$lang_arr[][$kesVal[$k]]=$lang_val['name'];	
		}	
	}else{
		$lang_arr[][$lang_val['iso_639_2']]=$language_name;
	}
	$lang_arr_key_arr[]=$language_name;
}
$count=imw_num_rows($res_lang);
$js_arr_lang=json_encode($lang_arr);
$lang_arr_key=json_encode($lang_arr_key_arr);


if($_SERVER['REQUEST_METHOD'] == "POST" && $source == "demographics"){
	echo "<script>window.opener.top.core_set_pt_session(window.opener.top.fmain, ".$_SESSION['patient'].", '../patient_info/demographics/index.php');</script>";
	echo "<script>window.close();</script>";
	die();
}

//$objManageData->Smarty->assign("display_heardtextarea",$display_heardtextarea);
//$objManageData->Smarty->assign("display_heardimg",$display_heardimg);
//$objManageData->Smarty->assign("heard_select_options",$heard_select_options);
//$objManageData->Smarty->assign("ptHeardAbtDesc",stripslashes($patientQryRes[0]["heard_abt_desc"]));
//$objManageData->Smarty->assign("ARR_INS_SWAP_DATA", $arrInsSwapData);
	
//---END OF HEARD ABOUT US CODE
//--- SET SOURCE(FROM WHERE POPUP IS OPENING) OF POPUP

//$objManageData->Smarty->assign("source", $source);	
$isMpay = verify_payment_method("MPAY");
//$objManageData->Smarty->assign("isMpay",verify_payment_method("MPAY"));
//$objManageData->Smarty->assign("pat_val",$pat_val);
//--- GET PATIENT INFO POPUP TEMPLATE -----
//$objManageData->Smarty->assign("DAY_REMAIN", $date_remain);
//$objManageData->Smarty->assign("OPENMODE", $_REQUEST["mode"]);
//$objManageData->Smarty->assign("consent_tab_color", $tab_color);
$popheight = (isset($_GET['popheight']) && intval($_GET['popheight'])>0) ? intval($_GET['popheight']) : $_SESSION['wn_height'];
//$objManageData->Smarty->assign("popheight_body_div", $popheight);
//$objManageData->Smarty->assign("web_root", $web_root);
//$objManageData->Smarty->assign("fun_NewPtfromDemo", $closeDemoScript);
//$objManageData->Smarty->assign("todayDate", get_date_format(date('Y-m-d')));
//$objManageData->Smarty->assign("zip_size",inter_zip_length());
//$objManageData->Smarty->assign("default_currency",htmlspecialchars_decode(show_currency()));
$zip_ext_view = inter_zip_ext() ? 'inline' : 'none';
//$objManageData->Smarty->assign("zip_ext",$zip_ext_view);
//$objManageData->Smarty->assign("zip_ext_status",inter_zip_ext());
//$objManageData->Smarty->assign("state_label",inter_state_label());
//$objManageData->Smarty->assign("state_val",inter_state_val());
//$objManageData->Smarty->assign("date_format",jQueryIntDateFormat());

//$objManageData->Smarty->assign("ssn_length",inter_ssn_length());
//$objManageData->Smarty->assign("ssn_format",inter_ssn_format());
//$objManageData->Smarty->assign("ssn_reg_exp_js",inter_ssn_reg_exp_js());

//$objManageData->Smarty->assign("phone_length",inter_phone_length());
//$objManageData->Smarty->assign("phone_min_length",inter_phone_min_length());
//$objManageData->Smarty->assign("phone_format",inter_phone_format());
//$objManageData->Smarty->assign("int_country",inter_country());
//$objManageData->Smarty->assign("lang_code_arr",$js_arr_lang);
//$objManageData->Smarty->assign("lang_arr_key", $lang_arr_key);
//$objManageData->Smarty->assign("arr_count",$count);


$strOpName = "";
if(isset($_SESSION['authProviderName']) && $_SESSION['authProviderName'] != ""){
	$arrOpName = explode(",",$_SESSION['authProviderName']);
	$strOpName = strtoupper(substr(trim($arrOpName[1]),0,1)).strtoupper(substr(trim($arrOpName[0]),0,1));
}
//$objManageData->Smarty->assign("strOpName", $strOpName);
$notes_date = get_date_format(date("Y-m-d"),'','',2,"/");
//$objManageData->Smarty->assign("default_currency",show_currency());
//$objManageData->Smarty->assign("dm_date_format",inter_date_format());
//$objManageData->Smarty->assign("stop_zipcode_validation",constant("STOP_ZIPCODE_VALIDATION"));
if(isset($_REQUEST['btn_submit']) && $_REQUEST['btn_submit'] == 'Save'){
	$_SESSION['patient'] = trim($_REQUEST['edit_patient_id']);
?>
	<script>
		window.opener.top.fmain.location.reload(false);
        window.close();	
    </script>
<?php
}

$login_facility=$_SESSION['login_facility'];
$pos_device=false;
$devices_sql="Select tsys_device_details.id from tsys_device_details
              JOIN tsys_merchant ON tsys_merchant.id= tsys_device_details.merchant_id
              WHERE device_status=0
              AND tsys_device_details.facility_id='".$login_facility."' 
              AND merchant_status=0
              ";
$resp = imw_query($devices_sql);
if($resp && imw_num_rows($resp)>0){
    $pos_device=true;
}

include('new_patient_info_popup_new_html.php');
?>
