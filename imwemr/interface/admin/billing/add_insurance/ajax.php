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
require_once("../../../../config/globals.php");
require_once("../../../../library/classes/common_function.php");
require_once("../../../../library/classes/cls_common_function.php");

/** 
 * Parameters Sanitization to prevent arbitrary values - Security Fixes
 **/
$_REQUEST['so'] = xss_rem($_REQUEST['so'], 2, 'sanitize');	/* Sanitize unwanted characters */
$_REQUEST['alpha'] = xss_rem($_REQUEST['alpha'], 2);	/* Reject parameter with unwanted characters */
//$_POST['in_house_code'] = xss_rem($_POST['in_house_code']);

$_POST['name'] = imw_real_escape_string($_POST['name']);
$_REQUEST['name'] = imw_real_escape_string($_REQUEST['name']);
$_REQUEST['searchStr'] = imw_real_escape_string($_REQUEST['searchStr']);

$_POST['id'] = trim($_POST['id']);
if( !empty($_POST['id']) && !is_numeric($_POST['id']) )
{
	die('Invalid insurance id supplied');
}
$_REQUEST['id'] = trim($_REQUEST['id']);

/* ================ End Security Fixes block ================ */


$phy_id_cn=$GLOBALS['arrValidCNPhy'];
$OBJCommonFunction = new CLSCommonFunction;
$task	= isset($_REQUEST['ajax_task']) ? trim($_REQUEST['ajax_task']) : '';
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'to_do_id';
$soAD	= (strtoupper($_REQUEST['soAD'])=='DESC') ? 'DESC' : 'ASC';	/* Prevent arbitrary values - Security Fix */
$table	= "insurance_companies";
$pkId	= "id";
$chkFieldAlreadyExist="task";
//-------BEGIN CALCULATE RECORDS LIMIT----------
	$page = (isset($_REQUEST['page']) && $_REQUEST['page']!="")?$_REQUEST['page']:1;
	$record_limit = (isset($_REQUEST['record_limit']) && $_REQUEST['record_limit']!="")?$_REQUEST['record_limit']:19;
	$offset = ($page-1) * $record_limit;
	$count = $record_limit;
	
//-------END CALCULATE RECORDS LIMIT----------
switch($task){
	case 'delete':
		$id = $_POST['pkId'];
		//$q 		= "DELETE FROM ".$table." WHERE ".$pkId." IN (".$id.")";
		$del_info="Delete Reason:".trim(addslashes($_REQUEST['delReason']))."\nDate Time:".date("Y-m-d H:i:s",time())."\ndel_operator:".$_SESSION['authId']."";
		$q 		= "UPDATE ".$table." SET ins_del_status = '1',del_info='".$del_info."' WHERE ".$pkId." IN (".$id.")";
		//echo $q;die();
		$res 	= imw_query($q);
		if($res){
			if(constant("EXTERNAL_INS_MAPPING") == "YES"){
  			$OBJCommonFunction->createInsCompXMLCrossMap();
  		}else{
   			$OBJCommonFunction->createInsCompXML();
  		}
			echo '1';
		}else{
			echo '0';//.imw_error()."\n".$q;
		}
		break;
	case 'save_update':
		$chk_cap_pop=$_POST['chk_cap_pop'];
		if($_POST['name'] != "" && $_POST['id'] == ""){
			$q = "select * from insurance_companies where name = '".$_POST['name']."' and in_house_code = '".$_POST['in_house_code']."'";
			$r = imw_query($q);
			$count = imw_num_rows($r);
			if($count>0){
				echo 'enter_unique';
				die();
			}
		}
		if($_POST['name'] != "" && $_POST['id'] != ""){
			$q = "select * from insurance_companies where id!='".$_POST['id']."' and name='".$_POST['name']."' and in_house_code='".$_POST['in_house_code']."' and (ins_del_status = 0 || ins_del_status IS NULL)";
			$r = imw_query($q);
			$count = imw_num_rows($r);
			if($count>0){
				echo 'enter_unique';
				die();
			}
		}
		$id = $_POST[$pkId];
		unset($_POST[$pkId]);
		unset($_POST['ajax_task']);
		if($_POST['chk_cap_pop']=="yes"){
			$_POST['cap_cpt_code']=implode(',',$_POST['cap_cpt_code']);
			$_POST['cap_user']=implode(',',$_POST['cap_user']);
			$_POST['cap_wrt_code']=$_POST['cap_wrt_code'];
			unset($_POST['chk_cap_pop']);
		}else{
			if(!isset($_POST['BatchFile']))
			$_POST['BatchFile'] = 0;
			
			if(!isset($_POST['collect_copay']))
			$_POST['collect_copay'] = 0;
			
			if(!isset($_POST['ref_management']))
			$_POST['ref_management'] = 0;
			
			if(!isset($_POST['collect_sec_ins']))
			$_POST['collect_sec_ins'] = 0;
			
			if(!isset($_POST['frontdesk_desc']))
			$_POST['frontdesk_desc'] = 0;
			
			if(!isset($_POST['capitation']))
			$_POST['capitation'] = 0;
			
			if(!isset($_POST['billing_desc']))
			$_POST['billing_desc'] = 0;
			if(!isset($_POST['pre_atuh_chk'])){
				$_POST['pre_atuh_chk']=0;
			}
			if(!isset($_POST['rte_chk'])){
				$_POST['rte_chk']=0;
			}
			if(!isset($_POST['transmit_ndc'])){
				$_POST['transmit_ndc']=0;
			}
			unset($_POST['cap_cpt_code']);
			unset($_POST['cap_user']);
			unset($_POST['cap_wrt_code']);
			unset($_POST['chk_cap_pop']);
		}
		$query_part = "";
		
		if($id==''){
			$_POST['added_by'] = $_SESSION['authId'];
			$_POST['added_date_time'] = date("Y-m-d H:i:s",time());
		}
		else{
			$_POST['modified_by'] = $_SESSION['authId'];
			$_POST['modified_date_time'] = date("Y-m-d H:i:s",time());
		}
		
		foreach($_POST as $k=>$v){
			switch($k){
				case "id":
				case "pre_co_ins":
				case "pre_collect_sec":
				break;
				case "phone":
				case "fax":
				$query_part .= $k."='".core_phone_unformat($v)."', ";
				break;
				case "Print_form":
					if($v == 1)
					$query_part .= $k."='yes', ";
					else
					$query_part .= $k."='No', ";
				break;
				case "FeeTable":
					$v = xss_rem($v, 3, 'sanitize');	/* Sanitize arbitrary values - Security Fix */
					if($v <= 0)
					$query_part .= $k."='1', ";
					else
					$query_part .= $k."='".$v."', ";
				break;
				default:
				$query_part .= $k."='".addslashes($v)."', ";
			}
		}
		$query_part .='cms_id = 1 ,';
		$query_part .='freeb_type = 1 ,';
		$query_part .='x12_receiver_id = 1 ,';
		$query_part .='x12_default_partner_id = 1 ,';
		$query_part = substr($query_part,0,-2);
		$qry_con = "";
		if($id){$qry_con=" AND ".$pkId."!='".$id."' ";}
		$q_c="SELECT ".$pkId." from ".$table." WHERE ".$chkFieldAlreadyExist."='".$_POST[$chkFieldAlreadyExist]."' ".$qry_con;
		$r_c=imw_query($q_c);
		if(imw_num_rows($r_c)==0){		
		
			if($id==''){
				$q = "INSERT INTO ".$table." SET ".$query_part;
				$res = imw_query($q);
				$insert_id = imw_insert_id();
				$ins_id=$insert_id;
			}else{
				$q = "UPDATE ".$table." SET ".$query_part." WHERE ".$pkId." = '".$id."'";
				$res = imw_query($q);
				if($_POST['collect_sec']  == '1' && $_POST['co_ins'] != ""){
					if($_POST['co_ins'] != $_POST['pre_co_ins']){
						$qry_update ="update insurance_data set co_ins='".$_POST['co_ins']."' where type='primary' and provider='".$id."' and actInsComp='1'";
						$res_update = imw_query($qry_update);
					}else if($pre_collect_sec != $collect_sec){
						$qry_update ="update insurance_data set co_ins='".$_POST['co_ins']."' where type='primary' and provider='".$id."' and actInsComp='1'";
						$res_update = imw_query($qry_update);
						
					}
				}
				$ins_id=$id;
			}
			if(constant("EXTERNAL_INS_MAPPING") == "YES"){
				$OBJCommonFunction->createInsCompXMLCrossMap();
			}
			else{
				$OBJCommonFunction->createInsCompXML();
			}
		//	$OBJCommonFunction->createAllInsCompXML();
			//echo $q;die();
			if($res){
				echo $ins_id;
			}else{
				echo 'Record Saving failed.'.imw_error()."\n".$q;
			}
		}else {
			echo "enter_unique";	
		}
		break;
	case "set_status":
		$sql = "UPDATE ".$table." SET ins_del_status = ".$_REQUEST['status']." WHERE ".$pkId." = ".$_REQUEST['id'];
		imw_query($sql);
		if(constant("EXTERNAL_INS_MAPPING") == "YES"){
  			$OBJCommonFunction->createInsCompXMLCrossMap();
  		}else{
   			$OBJCommonFunction->createInsCompXML();
  		}
  	//	$OBJCommonFunction->createAllInsCompXML();
	break;
	case "get_ins_data":
		$sql = "SELECT * FROM ".$table. " WHERE LOWER(name) = '".trim(strtolower($_REQUEST['name']))."' AND ins_del_status = 0";
		$res = imw_query($sql);
		if(imw_num_rows($res)>0){
			$row = imw_fetch_assoc($res);
			echo $row['id'];
		}else echo "";
		
	break;
	case 'show_list':
		$q = "SELECT `ins`.*, DATE_FORMAT(IF(`ins`.`modified_by`, `ins`.`modified_date_time`, `ins`.`added_date_time`), '".get_sql_date_format()."') AS `hx_date_time`, IFNULL(UPPER(IF(`usr`.`mname`!='', CONCAT(SUBSTRING(`usr`.`fname`, 1, 1),SUBSTRING(`usr`.`mname`, 1, 1),SUBSTRING(`usr`.`lname`, 1, 1)), CONCAT(SUBSTRING(`usr`.`fname`, 1, 1),SUBSTRING(`usr`.`lname`, 1, 1)))), '') AS 'hx_operator', IFNULL(IF(`usr`.`mname`!='', CONCAT(`usr`.`lname`,', ',`usr`.`fname`,' ',`usr`.`mname`), CONCAT(`usr`.`lname`,', ',`usr`.`fname`)), '') AS 'hx_operator_name' FROM ".$table." `ins` LEFT JOIN `users` `usr` ON (IF(`ins`.`modified_by`, `ins`.`modified_by`=`usr`.`id`, `ins`.`added_by`=`usr`.`id`))";
			  
		$arrWhere = array();
		$status = (isset($_REQUEST['s'])&& $_REQUEST['s'] !="")?$_REQUEST['s']:"0";
		
		if($status == "0"){
			$arrWhere[] = "  (ins_del_status = 0 || ins_del_status IS NULL)";
		}
		else if($status != 'all'){
			$arrWhere[] = "  ins_del_status = ".$status;
		}
		
		if(isset($_REQUEST['alpha']) && $_REQUEST['alpha'] !="" && $_REQUEST['searchStr']==""){
			if($_REQUEST['alpha']=='0-9'){
				$arrWhere[] = "  name LIKE '1%' OR name LIKE '2%'  OR name LIKE '3%'  OR name LIKE '4%' OR name LIKE '5%' OR name LIKE '6%' OR name LIKE '6%' OR name LIKE '8%' OR name LIKE '9%' OR name LIKE '0%'";
			}else{
				$arrWhere[] = "   name LIKE '".$_REQUEST['alpha']."%'";
			}
		}
		if(isset($_REQUEST['searchStr']) && $_REQUEST['searchStr']!=""){
			$arrWhere[] = "  (name LIKE '".$_REQUEST['searchStr']."%' OR in_house_code LIKE '".$_REQUEST['searchStr']."%')";
		}
		if(count($arrWhere)>0){
			$q .= " WHERE ".implode(" AND ",$arrWhere);
		}
		$q .= " ORDER BY $so $soAD ";
		$total_pages = ceil(imw_num_rows(imw_query($q))/$count);
		$q .= "LIMIT $offset,$count"; 
		
		//echo $status;echo $q;die();
		
		$r = imw_query($q);
		$rs_set =array();
		if($r && imw_num_rows($r)>0){
			while($rs = imw_fetch_assoc($r)){
				//$rs_set[] = $rs;
				$insCompFullAdd = "";
				if ($rs['contact_address']){
					$insCompFullAdd .= trim($rs['contact_address']);
				}
				if($rs['City'] != '' && $rs['State'] != ''){
					$insCompFullAdd .= " ".trim($rs['City']).', '.trim($rs['State']).' '.trim($rs['Zip']);
				}
				if($rs['zip_ext'] != ''){
					$insCompFullAdd .= "-".trim($rs['zip_ext']);
				}
				$rs['pre_co_ins'] = $rs['co_ins'];
				$rs['pre_collect_sec'] = $rs['collect_sec_ins'];
				$rs['address'] = $insCompFullAdd;
				
				if($rs['Insurance_payment']=='Electronics'){$rs['Insurance_payment']='Electronics';}
				if($rs['secondary_payment_method']=='Electronics'){$rs['secondary_payment_method']='Electronics';}
				$rs['pri_sec_pay'] = ucfirst($rs['Insurance_payment']).'&nbsp;/&nbsp;'.ucfirst($rs['secondary_payment_method']);
				$rs['td_claim_type']= get_default_claim_type();
				if($rs['claim_type'] > 0){
					$rs['td_claim_type'] = 'Medicare';
				}
				if($rs['claim_type'] > 1){
					$rs['td_claim_type'] = 'PI';
				}
				if($rs['ins_del_status']==1) {
					$rs['status'] = "<a href='javascript:void()'  onclick='set_status(0,".$rs['id'].")' target='_parent'><img src='../../../../library/images/inactive.jpg' title='Inactive' class='noborder'></a>";
                }else{ 
					$rs['status'] = "<a href='javascript:void()'  onclick='set_status(1,".$rs['id'].")' target='_parent'><img src='../../../../library/images/active.jpg' title='Active' class='noborder'></a>";
                }

				$rs['cap_cpt_code[]'] = cpt_code($rs['cap_cpt_code']);
				$rs['cap_user[]'] = prov_list($phy_id_cn,$rs['cap_user']);
				$rs_set[] = $rs;
			}
		}
		//echo count($rs_set);die();
		$primary_payment_type = primary_payment_type();
		$secondary_payment_type = secondary_payment_type();
		$claim_type = claim_type();
		$institutional_type = institutional_type();
		$ins_state_payer_code = ins_state_payer_code();
		$ins_type = ins_type();
		$fee_table = fee_table();
		$ins_grp = ins_grp();
		//$prov_list = prov_list($phy_id_cn);
		//$cpt_code = cpt_code();
		$wrt_code = wrt_code();
		$MSP_types = getMSPTypes('options');
		echo json_encode(array('records'=>$rs_set,"total_pages"=>$total_pages,"primary_payment_type"=>$primary_payment_type,"secondary_payment_type"=>$secondary_payment_type,"claim_type"=>$claim_type,"institutional_type"=>$institutional_type,"ins_state_payer_code"=>$ins_state_payer_code,"ins_type"=>$ins_type,"fee_table"=>$fee_table,"ins_grp"=>$ins_grp,"MSP_types"=>$MSP_types,"wrt_code"=>$wrt_code));
	break;
	case "get_cpt_alert":
		$ins_id = $_POST['ins_id'];
		$r = imw_query("select cpt_fee_id,cpt_prac_code,cpt_desc from cpt_fee_tbl WHERE status='active' and delete_status = '0' order by cpt_prac_code ASC");
		if($r && imw_num_rows($r)>0){
			while($row = imw_fetch_assoc($r)){
				$arr_cpt_code[$row['cpt_fee_id']] = $row['cpt_prac_code'];
			}
		}

		$cpt_dt = '';
		$saved_cpt_alert=0;
		$qry = "SELECT * FROM ins_cpt_alert WHERE ins_id = '".$ins_id."' and del_by='0' order by id";
		$rs = imw_query($qry);
		$i = 1;
		$rows=imw_num_rows($rs);
		if(imw_num_rows($rs)>0){
			while($res=imw_fetch_assoc($rs)){
				$cpt_code_options='';
				//INS TYPES
                $cpt_code_idArr=unserialize(html_entity_decode($res['cpt_code_id']));
                $cpt_alert=$res['cpt_alert'];
				foreach($arr_cpt_code as $key => $val){
					$sel='';
					if($cpt_code_idArr[$key]!=""){
						$sel='SELECTED';
					}
					
					$cpt_code_options.='<option value="'.$key.'" '.$sel.'>'.$val.'</option>';
				}			
				$cpt_dt.='<tr id="cptAlertRow'.$i.'">';
				$cpt_dt.='<td style="vertical-align: top!important; width:30%; max-width:220px;"><input type="hidden" name="cpt_ins_edit_id'.$i.'" id="cpt_ins_edit_id'.$i.'" value="'.$res['id'].'"/>';
                $cpt_dt.='<select name="cpt_code_id'.$i.'[]" id="cpt_code_id'.$i.'" multiple="multiple" class="selectpicker" data-container="#selectpickerUI" data-actions-box="true" data-width="100%" data-size="10" data-live-search="true">';
				$cpt_dt.= $cpt_code_options;
				$cpt_dt.='</select></td>';
				$cpt_dt.='<td style="width:60%;"><textarea class="form-control" name="cpt_comment'.$i.'" id="cpt_comment'.$i.'" rows="3">'.$cpt_alert.'</textarea></td>';
				$cpt_dt.='<td class="pt10 text-center pointer" style="vertical-align:middle;width:10%;" onClick="top.fancyConfirm(\'Are you sure you want to delete this record?\',\'\',\'top.fmain.remove_cpt_alert('.$i.')\');"><img id="add_cpt_alert_row'.$i.'" title="Delete Row" src="'.$GLOBALS['webroot'].'/library/images/closerd.png" alt="Delete Row" ></td>';
				$cpt_dt.='</tr>';
				
				$saved_cpt_alert=$i;
				$i++;
			}
            
			echo $cpt_dt.'~'.$saved_cpt_alert.'~'.$default_no;
		}else{
			echo "";
		}
	break;
    case 'save_cpt_alert':
		$msg="";
		$ins_id=$_POST['cpt_ins_id'];
        $cpt_row_count=$_POST['cpt_row_count'];
        $entered_by = $_SESSION['authId'];
		$entered_date = date("Y-m-d H:i:s",time());
		
		$r = imw_query("select cpt_fee_id,cpt_prac_code,cpt_desc from cpt_fee_tbl WHERE status='active' and delete_status = '0' order by cpt_prac_code ASC");
		if($r && imw_num_rows($r)>0){
			while($row = imw_fetch_assoc($r)){
				$arr_cpt_code[$row['cpt_fee_id']] = $row['cpt_prac_code'];
			}
		}
        
        for($i=1;$i<=$cpt_row_count;$i++) {
			$cpt_ins_edit_id=$_POST['cpt_ins_edit_id'.$i];
			$cpt_code_arr=array();
			foreach($_POST['cpt_code_id'.$i] as $key=>$val){
				$cpt_code_arr[$val]=$arr_cpt_code[$val];
			}
            $cpt_alert=addslashes(trim($_POST['cpt_comment'.$i]));
			if($cpt_alert!="" && count($cpt_code_arr)>0){
				if($cpt_ins_edit_id>0){
					$alertSql="update ins_cpt_alert set cpt_code_id='".serialize($cpt_code_arr)."',cpt_alert='".$cpt_alert."',modified_by='".$entered_by."',modified_date='".$entered_date."' where id ='".$cpt_ins_edit_id."'";
				}else{
            		$alertSql="insert into ins_cpt_alert set ins_id=".$ins_id.",cpt_code_id='".serialize($cpt_code_arr)."',cpt_alert='".$cpt_alert."',entered_by='".$entered_by."',entered_date='".$entered_date."' ";
				}
				imw_query($alertSql);
				$msg ="CPT code alert saved successfully";
			}
        }
		echo $msg;
    break;
	case 'del_cpt_alert':
		$cpt_ins_del_id=$_POST['cpt_ins_del_id'];
        $entered_by = $_SESSION['authId'];
		$entered_date = date("Y-m-d H:i:s",time());
		$alertSql="update ins_cpt_alert set del_by='".$entered_by."',del_date='".$entered_date."' where id ='".$cpt_ins_del_id."'";
		imw_query($alertSql);
     break;	
	default: 
}

function get_default_claim_type(){
	$returnStr = '';
	$q = "select name from copay_policies";		
	$r = imw_query($q);
	$row = imw_fetch_assoc($r);
	$returnStr = $row['name'];
	return $returnStr;
}
function primary_payment_type(){
	$returnArr = array();
	$returnArr[] = array("id"=>"HCFA1500","type"=>"HCFA1500");
	$returnArr[] = array("id"=>"Electronics","type"=>"Electronics");
	return $returnArr;
}
function secondary_payment_type(){
	$returnArr = array();
	$returnArr[] = array("id"=>"HCFA1500","type"=>"HCFA1500");
	$returnArr[] = array("id"=>"Electronics","type"=>"Electronics");
	return $returnArr;
}
function claim_type(){
	$returnArr = array();
	$q = "select name from copay_policies";		
	$r = imw_query($q);
	$row = imw_fetch_assoc($r);
	$default_claim_type = $row['name'];
	$claimTypeArr = array($default_claim_type,'Medicare','PI');
	for($i=0;$i<count($claimTypeArr);$i++){
		$claimType = $claimTypeArr[$i];
		$returnArr[] = array("id"=>$i,"type"=>$claimType);
	}
	return $returnArr;
}
function institutional_type(){
	$returnArr = array();
	$returnArr[] = array("id"=>'INST_ONLY',"type"=>'837i');
	$returnArr[] = array("id"=>'INST_PROF',"type"=>'837p');
	return $returnArr;
}
function ins_state_payer_code(){
	$returnArr = array();
	$arr_ins_state_payer_code = array(""=>"","Medicare"=>"A", 
										"Blue Cross/Blue Shield (not managed care)"=>"B", 
										"Federal, Tricare, Champus (Military)"=>"C", 
										"Medicaid"=>"D", 
										"Commercial Insurance (not managed care)"=>"I", 
										"Medicare (not managed care)"=>"M", 
										"Division of Health Services (Voc. Rehab.)"=>"N", 
										"Other, Unknown"=>"O", 
										"Self-Pay"=>"P", 
										"Self-Insured, Self-Administered"=>"S", 
										"Workers/State Compensation"=>"W", 
										"Medically Indigent/Free"=>"Z", 
										"Cover TN (also known as Blue Cross InReach plan)"=>"11", 
										"Cover Kids"=>"12", 
										"Access TN"=>"13", 
										"TennCare-Plan Unspecified"=>"T", 
										"UAHC (previously Omni Care)"=>"5", 
										"Windsor Health Plan of TN, Inc. (previously VHP Community Care)"=>"7", 
										"AmeriChoice (previously John Deere/Heritage)"=>"8", 
										"Preferred Health Partnership"=>"9", 
										"TLC Family Care"=>"F", 
						  				"Blue Care (TennCare plan offered by Blue Cross/Blue Shield)"=>"J", 
										"TennCare Select (State’s TennCare product administered by Blue Cross)"=>"Q", 
										"Unison Health Plan (previously Better Health Plans, Inc.)"=>"R", 
										"AmeriGroup Community Care (new TennCare MCO)"=>"10", 
										"BHO – plan unspecified"=>"E", 
										"Tennessee Behavioral Health, Inc."=>"U", 
										"Premier Behavioral Systems of TN"=>"X", 
										"Blue Cross Managed Care – HMO/PPO/Other Managed Care"=>"H", 
										"Commercial (Managed Care – HMO/PPO/Other Managed Care)"=>"L", 
										"Medicare (HMO/PSO)"=>"K");

	if(constant("SHOW_INS_STATE_PAYER_CODE")=="PA") {
		$arr_ins_state_payer_code = array(""=>"",
										"Aetna Health, Inc. - Medicare PPO\nAmerican Progressive - Medicare PPO\nCapital Blue Cross (Central PA & Lehigh Valley) - Medicare PPO\nGeisinger Health Plan - Medicare PPO\nHealthAssurance Pennsylvania, Inc. - Medicare PPO\nHighmark Senior Health Company - Medicare PPO\nHumana Insurance Company - Medicare PPO\nQCC Insurance Company - Medicare PPO\nUnited Healthcare - Medicare PPO\nUPMC Health Network, Inc. - Medicare PPO"=>"12",
										"Medicare Part A & B - Medicare"=>"14",
										"Aetna Health, Inc. - Medicare HMO\nAmeriHealth - Medicare HMO\nBravo Health Pennsylvania, Inc. - Medicare HMO\nGateway Health Plan, Inc. - Medicare HMO\nGeisinger Health Plan - Medicare HMO\nHealth Partners of Philadelphia, Inc. - Medicare HMO\nHealthAmerica (Central and Pittsburgh) - Medicare HMO\nHighmark, Inc. - Medicare HMO\nHumana Insurance Company - Medicare HMO\nKeystone Health Plan Central, Inc. - Medicare HMO\nKeystone Health Plan East, Inc. - Medicare HMO\nKeystone Health Plan West, Inc. - Medicare HMO\nQCC Insurance Company - Medicare HMO\nPA Health & Wellness - Medicare HMO\nUnited Healthcare - Medicare HMO\nUPMC Health Plan, Inc. - Medicare HMO"=>"15",
										"Medical Assistance (Medicaid) - Medicaid"=>"24",
										"Aetna Better Health, Inc. - Medicaid HMO\nAmeriHealth - Medicaid HMO\nCoventry Health - Medicaid HMO\nGateway Health Plan, Inc. - Medicaid HMO\nGeisinger Health Plan - Medicaid HMO\nHealth Partners of Philadelphia, Inc. - Medicaid HMO\nHighmark, Inc. - Medicaid HMO\nKeystone Health Plan East, Inc. - Medicaid HMO\nPA Health & Wellness - Medicaid HMO\nUnited Healthcare - Medicaid HMO\nUPMC Health Plan, Inc. - Medicaid HMO\nVista Health Plan, Inc. - Medicaid HMO"=>"25",
										"Capital Blue Cross (Central PA & Lehigh Valley) - PPO\nFirst Priority Life Ins. Co. - PPO\nHighmark, Inc. - PPO\nHospital Service Association of Northeastern PA - PPO\nQCC Insurance Company - PPO\nOut-of-State Blue Cross - PPO"=>"32",
										"Capital Blue Cross (Central PA & Lehigh Valley) - POS\nHighmark, Inc. - POS\nHMO of Northeastern Pennsylvania - POS\nKeystone Health Plan East, Inc. - POS\nOut-of-State Blue Cross - POS"=>"33",
										"Capital Blue Cross (Central PA & Lehigh Valley) - Fee for Service\nFirst Priority Life Ins. Co. - Fee for Service\nHighmark, Inc. - Fee for Service\nHospital Service Association of Northeastern PA - Fee for Service\nIndependence Blue Cross (Eastern PA) - Fee for Service\nOut-of-State Blue Cross - Fee for Service"=>"34",
										"HMO of Northeastern Pennsylvania - HMO\nKeystone Health Plan Central, Inc. - HMO\nKeystone Health Plan East, Inc. - HMO\nKeystone Health Plan West, Inc. - HMO\nUnited Healthcare - HMO\nOut-of-State Blue Cross - HMO"=>"35",
										"Out-of-State Blue Cross - Unknown/Not Listed"=>"39",
										"Aetna Life Insurance Company - PPO\nAmeriHealth - PPO\nCIGNA Health and Life Insurance Company - PPO\nCoventry Health - PPO\nEducators Mutual Life Insurance Company - PPO\nGeisinger Health Plan - PPO\nHealthAssurance Pennsylvania, Inc. - PPO\nHumana Insurance Company - PPO\nUnited Healthcare - PPO\nUPMC Health Network, Inc. - PPO\nAetna Ins Co of CT - PPO\nAetna Life Ins Co - PPO\nAetna Hlth Ins Co - PPO\nAetna Hlth & Life Ins Co - PPO\nIng Life Ins & Ann Co - PPO"=>"42",
										"Aetna Life Insurance Company - POS\nAmeriHealth - POS\nCIGNA Health and Life Insurance Company - POS\nGeisinger Health Plan - POS\nHealthAssurance Pennsylvania, Inc. - POS\nHorizon Healthcare PA - POS\nUnited Healthcare - POS\nUPMC Health Network, Inc. - POS\nUPMC Health Plan, Inc. - POS\nAetna Ins Co of CT - POS\nAetna Life Ins Co - POS\nAetna Hlth Ins Co - POS\nAetna Hlth & Life Ins Co - POS\nIng Life Ins & Ann Co - POS"=>"43",
										"Aetna Life Insurance Company - Fee for Service\nAmerican Progressive - Fee for Service\nBravo Health Insurance Co. Inc. - Fee for Service\nCIGNA Health and Life Insurance Company - Fee for Service\nCoventry Health - Fee for Service\nEducators Mutual Life Insurance Company - Fee for Service\nGeisinger Indemnity Insurance Company - Fee for Service\nHumana Insurance Company - Fee for Service\nPrudential Companies - Fee for Service\nWestchester Fire Ins Co - Fee for Service\nAce Prop & Cas Ins Co - Fee for Service\nAce Fire Underwriters Ins Co - Fee for Service\nAce Amer Ins Co - Fee for Service\nR&Q Reins Co - Fee for Service\nLM Prop & Cas Ins Co - Fee for Service\nAetna Ins Co of CT - Fee for Service\nLM Gen Ins Co - Fee for Service\nAetna Life Ins Co - Fee for Service\nConnecticut Gen Life Ins Co - Fee for Service\nCigna Life Ins Co Of NY - Fee for Service\nWilton Reassur Co - Fee for Service\nPrudential Ins Co Of Amer - Fee for Service\nAetna Hlth Ins Co - Fee for Service\nAetna Hlth & Life Ins Co - Fee for Service\nIng Life Ins & Ann Co - Fee for Service\nPrudential Retirement Ins & Ann Co - Fee for Service"=>"44",
										"Aetna Health, Inc. - HMO\nAmeriHealth - HMO\nCIGNA Health and Life Insurance Company - HMO\nGeisinger Health Plan - HMO\nHealthAmerica (Central and Pittsburgh) - HMO\nHorizon Healthcare PA - HMO\nOptimum Choice, Inc. of PA - HMO\nUPMC Health Plan, Inc. - HMO"=>"45",
										"Workers' Compensation Insurers - Workers' Compensation"=>"47",
										"All Automobile Insurers - Automobile"=>"48",
										"Behavioral Health Coverage - Unknown/Not Listed\nThird Party Administrators - Unknown/Not Listed"=>"49",
										"Government - PPO"=>"82",
										"Government - Fee for Service"=>"84",
										"TriCare - HMO"=>"85",
										"Other Federal/State/County Funded Programs - Unknown/Not Listed\nState-Hospital Funded Psychiatric Care - Unknown/Not Listed"=>"89",
										"Uninsured - Self Pay or Charity/Indigent Care"=>"00"
										);
	}
	if(constant("SHOW_INS_STATE_PAYER_CODE")=="ASC") {
		$arr_ins_state_payer_code = array(""=>"",
											"Medicare. Patients covered by Medicare where Centers for Medicare & Medicaid Services is the direct payer."=>"A",
											"Medicare Managed Care. Patients covered by Medicare Advantage plans,\nMedicare HMO,\nMedicare PPO,\nMedicare Private Fee for Service or any other type of Medicare plan where Centers for Medicare & Medicaid Services is not the direct payer."=>"B",
											"Medicaid. Patients covered by state administered, non-managed Florida Medicaid.\nThis would include those Medicaid recipients enrolled in MediPass."=>"C",
											"Medicaid Managed Care. Patients covered by Medicaid HMOs,\nMedicaid provider sponsored networks (PSNs) or other Medicaid funded plans that are licensed in the state of Florida.\nThis would include any type of program where the patient qualifies for Medicaid\nbut payment is not directly from the State of Florida Medicaid program regardless of whether the hospital has a contract with that plan."=>"D",
											"Commercial Health Insurance. Patients covered by any type of private coverage, including HMO, PPO or self-insured plans."=>"E",
											"Workers Compensation. Patients covered by any type of workers compensation plan, including self insured plans,\nmanaged care plans or the State of Florida sponsored workers compensation plan."=>"H",
											"TriCare or Other Federal Government. Patients covered by any federal government program for active and retired military and their families;Black Lung,\nSection 1011; the Federal Prison System; or any other federal program."=>"I",
											"VA. Patients covered by the Veteran’s Administration (VA)."=>"J",
											"Other State/Local Government. Patients covered by a state program or local government that does not fall into any of the payer categories listed.\nThis would include those covered by the Florida Department of Corrections or any county or local corrections department,\npatients covered by county or local government indigent care programs if the reimbursement is at the patient level; any out-of-state Medicaid programs and county health departments or clinics."=>"K",
											"Self Pay. Patients with no insurance coverage."=>"L",
											"Other. This would include patients covered by any other type of payer not meeting the descriptions in paragraphs (a)-(j) above or paragraphs (l)-(o) below."=>"M",
											"Non-Payment. Includes charity,\nprofessional courtesy,\nno charge,\nresearch/clinical trial,\nrefusal to pay/bad debt,\nHill Burton free care,\nresearch/donor that is known at the time of reporting."=>"N",
											"KidCare. Includes Healthy Kids, MediKids and Children’s Medical Services."=>"O",
											"Unknown. Unknown shall be reported if principal payer information is not available and type of service is “2” and patient status is “07”."=>"P",
											"Commercial Liability Coverage. Patients whose health care is covered under a liability policy, such as automobile, homeowners or general business."=>"Q"
										);	
	}										
	
	 foreach($arr_ins_state_payer_code as $arr_ins_state_payer_code_key => $arr_ins_state_payer_code_val){
		 $returnArr[] = array("id"=>$arr_ins_state_payer_code_val,"type"=>$arr_ins_state_payer_code_val,"title"=>$arr_ins_state_payer_code_key);
	}
	return $returnArr;
}
function ins_type(){
	$returnArr = array();
	$arr_ins_type = array("","11", "12", "13", "14", "15", "16", "17", "AE", "AM", "BL", "CH", "CI", "DS", "FI", "HM", "LM", "MA", "MB", "MC", "OF", "TV", "VA", "WC", "ZZ");
	 foreach($arr_ins_type as $arr_ins_type_val){
		 $returnArr[] = array("id"=>$arr_ins_type_val,"type"=>$arr_ins_type_val);
	}
	return $returnArr;
}
function fee_table(){
	$q = "select * from fee_table_column";	
	$r = imw_query($q);
	while($row = imw_fetch_assoc($r)){
		$columnId = $row['fee_table_column_id'];
		$value = $row['column_name'];
		$returnArr[] = array("id"=>$columnId,"value"=>$value);
	}
	return $returnArr;
}
function ins_grp(){
	$returnArr = array();
	$r = imw_query("SELECT id,title FROM ins_comp_groups WHERE delete_status=0 ORDER BY title");
	if($r){
		while($row = imw_fetch_assoc($r)){
			$returnArr[] = array("id"=>$row['id'],"value"=>stripslashes(ucwords($row['title'])));
		}
	}
	return $returnArr;
}
function prov_list($phy_id_cn,$val){
	$returnArr = array();
	$sql = imw_query("select id,fname,lname,mname,sx_physician,user_type,Enable_Scheduler from users WHERE delete_status='0' and  id in($val) order by lname ASC");
	while($row=imw_fetch_assoc($sql)){	
		$fname=$row["fname"];
		$lname=$row["lname"];
		$mname="";
		if($row["mname"]!=""){
			$mname=" ".trim($row["mname"]).'.';
		}
		$name=$lname.", ".$fname.$mname;
		if($row["Enable_Scheduler"]=='1' || in_array($row["user_type"],$phy_id_cn)){
			$returnArr[] = array("id"=>$row['id'],"value"=>stripslashes($name));
		}
	}
	return $returnArr;
}
function cpt_code($val){
	$returnArr = array();
	$r = imw_query("select cpt_fee_id,cpt_prac_code,cpt_desc from cpt_fee_tbl WHERE status='active' and delete_status = '0' and  cpt_fee_id in($val) order by cpt_prac_code ASC");
	if($r){
		while($row = imw_fetch_assoc($r)){
			$returnArr[] = array("id"=>$row['cpt_fee_id'],"value"=>stripslashes(str_replace('"','',$row['cpt_desc']).' - '.$row['cpt_prac_code']));
		}
	}
	return $returnArr;
}
function wrt_code(){
	$returnArr = array();
	$r = imw_query("select w_id,w_code from write_off_code order by w_code");
	if($r){
		while($row = imw_fetch_assoc($r)){
			$returnArr[] = array("id"=>$row['w_id'],"value"=>stripslashes(ucwords($row['w_code'])));
		}
	}
	return $returnArr;
}
?>