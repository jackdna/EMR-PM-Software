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
require_once("../../../../library/classes/cls_common_function.php");
require_once($GLOBALS['srcdir']."/classes/common_function.php");
$OBJCommonFunction = new CLSCommonFunction;

$_REQUEST = array_map('trim',$_REQUEST);
extract($_REQUEST);
$authId = $_SESSION['authId'];

$return = array('action'=>$action);

switch($action)
{
	case 'refPhyDetails':
		
		if($refPhyId != ''){
			$q = "SELECT physician_Reffer_id ,TRIM(CONCAT(LastName, ', ', FirstName, ' ', MiddleName, if(MiddleName!='',' ',''),Title)) as refName, Address1, Address2, ZipCode, City, State, physician_phone, physician_fax, physician_email,comments 
				  FROM refferphysician	WHERE physician_Reffer_id IN(".$refPhyId.")";
			$r = imw_query($q);
			if($r && imw_num_rows($r) > 0){
				$detailHTML = "";
				while($rs = imw_fetch_array($r)){
					$strRefNameDB = "";
					//$strRefNameDB = $rs['refName'];
					$strRefNameDB = $OBJCommonFunction->getRefPhyName($rs["physician_Reffer_id"]);
					$address = core_extract_user_input($rs['Address1']);
					if($address != '' && $rs['Address2']!= ''){
						$address .= ', '.core_extract_user_input($rs['Address2']);
					}else if($address == '' && $rs['Address2']!= ''){
						$address = core_extract_user_input($rs['Address2']);
					}
					if($address !=''){
						$address .= ', '.core_extract_user_input($rs['City']);
					}
					else{
						$address = core_extract_user_input($rs['City']);
					}
					if($address !=''){
						$address .= ', '.core_extract_user_input($rs['State']).' '.core_extract_user_input($rs['ZipCode']);
					}
					else{
						$address = core_extract_user_input($rs['State']).' '.core_extract_user_input($rs['ZipCode']);
					}
					if(trim($address) !=''){
						$address .= '<br>';
					}
					if($rs['physician_phone']!=''){
						$address .= ''.$rs['physician_phone'].'<br>';
					}
					if($rs['comments']!=''){
						$address .= ''.$rs['comments'].'<br>';
					}
					
					$detailHTML .= "<div>".$strRefNameDB."<br>";
					$detailHTML .= $address."</div><br>";
					
				}
				/*
				$address = core_extract_user_input($rs['Address1']);
				if($address != '' && $rs['Address2']!= ''){
					$address .= ', '.core_extract_user_input($rs['Address2']);
				}else if($address == '' && $rs['Address2']!= ''){
					$address = core_extract_user_input($rs['Address2']);
				}
				if($address !=''){
					$address .= ', '.core_extract_user_input($rs['City']);
				}
				else{
					$address = core_extract_user_input($rs['City']);
				}
				if($address !=''){
					$address .= ', '.core_extract_user_input($rs['State']).' '.core_extract_user_input($rs['ZipCode']);
				}
				else{
					$address = core_extract_user_input($rs['State']).' '.core_extract_user_input($rs['ZipCode']);
				}
				if(trim($address) !=''){
					$address .= '<br>';
				}
				if($rs['physician_phone']!=''){
					$address .= ' <b>Phone: </b>'.$rs['physician_phone'].'<br>';
				}
				*/
/*				$address .= ' <b>Fax: </b>'.$rs['physician_fax'].'<br>';
				$address .= ' <b>Email: </b>'.$rs['physician_email'];*/
//				if(trim($address)==''){$address='<b>Details not found.</b>';}
				//$return = trim($address);
			}
		}
		elseif(($refPhyId == '') && $_REQUEST["multi"] != ""){
			$detailHTML = "";
			$qrySelpatRefPhy = "select TRIM(CONCAT(refPhy.LastName, ', ', refPhy.FirstName, ' ', refPhy.MiddleName,if(refPhy.MiddleName!='',' ',''),refPhy.Title)) as refName, refPhy.Address1, 
								refPhy.Address2, refPhy.ZipCode, refPhy.City, refPhy.State, refPhy.physician_phone, refPhy.physician_fax, refPhy.physician_email, refPhy.comments, 
								refPhy.physician_Reffer_id refPhyID
								from patient_multi_ref_phy pmrf INNER JOIN 
								refferphysician refPhy ON pmrf.ref_phy_id = refPhy.physician_Reffer_id 
								where pmrf.patient_id = '".$id."' and pmrf.phy_type IN(".$_REQUEST["multi"].") and pmrf.status = '0' ORDER BY pmrf.id";							
			$rsSelpatRefPhy = imw_query($qrySelpatRefPhy);
			if(imw_num_rows($rsSelpatRefPhy) > 0){				
				if(trim($_REQUEST["multi"]) == "1"){
					$detailHTML = "<div class=\"section_header\" style=\"color:black;min-width:300px;\">Referring Physicians</div>";
				}
				elseif(trim($_REQUEST["multi"]) == "2"){
					$detailHTML = "<div class=\"section_header\" style=\"color:black;min-width:300px;\">Co-Managed Physicians</div>";
				}
				elseif(trim($_REQUEST["multi"]) == "3,4"){
					$detailHTML = "<div class=\"section_header\" style=\"color:black;min-width:300px;\">Primary Care Physicians</div>";
				}
				$detailHTML.="~||~";
				$detailHTML_con="";
				$arrTemp = array();
				while($rowSelpatRefPhy = imw_fetch_array($rsSelpatRefPhy)){
					if(in_array((int)$rowSelpatRefPhy["refPhyID"], $arrTemp) == false){
						$strRefNameDB = "";
						//$strRefNameDB = trim(stripslashes($rowSelpatRefPhy["refName"]));
						$strRefNameDB = $OBJCommonFunction->getRefPhyName($rowSelpatRefPhy["refPhyID"]);
						$address = "";
						$address = core_extract_user_input($rowSelpatRefPhy['Address1']);
						if($address != "" && $rowSelpatRefPhy['Address2']!= ""){
							$address .= ", ".core_extract_user_input($rowSelpatRefPhy['Address2']);
						}
						else if($address == "" && $rowSelpatRefPhy['Address2']!= ""){
							$address = core_extract_user_input($rowSelpatRefPhy['Address2']);
						}
						if($address != ""){
							$address .= ", ".core_extract_user_input($rowSelpatRefPhy['City']);
						}
						else{
							$address = core_extract_user_input($rowSelpatRefPhy['City']);
						}
						if($address != ""){
							$address .= ", ".core_extract_user_input($rowSelpatRefPhy['State'])." ".core_extract_user_input($rowSelpatRefPhy['ZipCode']);
						}
						else{
							$address = core_extract_user_input($rowSelpatRefPhy['State'])." ".core_extract_user_input($rowSelpatRefPhy['ZipCode']);
						}
						if(trim($address) != ""){
							$address .= "\n";
						}
						if($rowSelpatRefPhy['physician_phone'] != ""){
							$address .= "Phone: ".$rowSelpatRefPhy['physician_phone']."\n";
						}
						if($rowSelpatRefPhy['comments'] != ""){
							$address .= "Comments: ".$rowSelpatRefPhy['comments']."\n";
						}
						$address = trim($address);
						$detailHTML_con .= "<div class=\"clr_black\" style=\"color:black;\">".$strRefNameDB."<br>";
						$detailHTML_con .= $address."</div><br>";
					}
					$arrTemp[] = $rowSelpatRefPhy["refPhyID"];
				}
				$detailHTML .= $detailHTML_con;
			}					
			
		}
		$data = trim($detailHTML);
		break;
	
	case 'insCompsAnchors':
		$p = $_REQUEST['p'] ? $_REQUEST['p'] : 1;	//Page Number
		$pp = $_REQUEST['pp'] ? $_REQUEST['pp'] : 500; // Per Page
		$paging = ($_REQUEST['paging'] == 'true') ? true : false; // Per Page
		$data = insurance_provider('', array(), 'dropdown','','','',$paging,$p,$pp);
		break;
	case 'authProvider':
		$data = $OBJCommonFunction->drop_down_providers($auth_provider,'','1','true');
		$data = array_flip($data);
		break;
	case 'cptData':
		$q = explode(";",$_REQUEST['query']);
		$s = end($q);
		if($s) $data = cpt_typeahead($s);
		break;	
	case 'updateAuthHxDates':
			$ids = $_POST['ids'];
			$val = $_POST['val'];
			$cnt = 0;
			if( is_array($ids) && count($ids) > 0 && is_array($val) && count($val) > 0  ) {
				foreach( $ids as $i => $id) {
					$value = getDateFormatDB($val[$i]);
					
					$qry = "update patient_auth set end_date = '$value' Where a_id = '".$id."' ";
					$sql = imw_query($qry) or die(imw_error());
					$cnt += imw_affected_rows();
				}
			}
			$return['rows_affected'] = $cnt;
		break;
	case 'ins_comp_practice_code':
		$dofrom = isset($dofrom) ? $dofrom : false; //acc_reviewpt
		$i1providerRCOIdV = "";
		$providerRCOId = $_REQUEST["providerRCOId"];
		if(constant("EXTERNAL_INS_MAPPING") == "YES")
		{
			$qryGetIdxInvRCOId = "SELECT invision_plan_code, invision_plan_description, IDX_description, IDX_FSC 
																FROM idx_invision_rco WHERE id = '".$providerRCOId."' LIMIT 1";
			$rsGetIdxInvRCOId = imw_query($qryGetIdxInvRCOId);
			if(imw_num_rows($rsGetIdxInvRCOId) > 0)
			{
				$dbInvisionPlanCode = $dbInvisionPlanDescription = $dbIDXDescription = $dbIDXFSC = "";
				$rowGetIdxInvRCOId = imw_fetch_row($rsGetIdxInvRCOId);
				$dbInvisionPlanCode = $rowGetIdxInvRCOId[0];
				$dbInvisionPlanDescription = $rowGetIdxInvRCOId[1];
				$dbIDXDescription = $rowGetIdxInvRCOId[2];
				$dbIDXFSC = $rowGetIdxInvRCOId[3];
			}					
		}

		$data = '<span class="closeBtn" onclick="$(\'#tool_tip_div\').hide();"></span><br>';
		$insuranceDetail = get_insurance_details($id);
		if($dofrom && $dofrom=='acc_reviewpt')
		{
			$q1 = "SELECT * FROM insurance_data WHERE provider ='".$id."' AND pid ='".$_SESSION['patient']."'";
			$r1 = imw_query($q1);
			if($r1 && imw_num_rows($r1)>0){
				$rs1 = imw_fetch_assoc($r1);
				$data .= '<b>Policy#: </b>'.$rs1['policy_number'].'<br>';
			}
		}

		if($insuranceDetail->City){
			$city = $insuranceDetail->City.', '.$insuranceDetail->State.' '.$insuranceDetail->Zip;
		}
		
		if(constant("EXTERNAL_INS_MAPPING") == "YES"){
			$data .= implode(',',$insuranceDetail).'<b>Name: </b>'.$dbInvisionPlanCode.' - '.$dbInvisionPlanDescription.' - '.$dbIDXDescription.' - '.$dbIDXFSC.'<br>';
		}
		else{
			$data .= implode(',',$insuranceDetail).'<b>Name: </b>'.$insuranceDetail->in_house_code.' - '.$insuranceDetail->name.'<br>';
		}
		if($insuranceDetail->contact_address){
			$address = $insuranceDetail->contact_address;
		}
		$address .= ($city) ? ' - '.$city : '';
		
		$data .= '<b>Address: </b>'.$address.'<br>';
		if($insuranceDetail->phone != ''){
			$data .= '<b>Phone#: </b>'.$insuranceDetail->phone.'<br>';
		}
		
		
		break;
	case 'check_exist_ins':
		
		$query = "select id from insurance_data where ins_caseid = '".$ins_case_id."'
							and actInsComp = '1' and type = '".$ins_type."' and pid = '".$patient_id."'";
		$sql = imw_query($query);
		$row = imw_fetch_assoc($sql);
		$id = trim($row['id']);
		$is_exists = ($id) ? true : false;
		$return['is_exist'] = $is_exists;
		$return['ins_type'] = $ins_type;
	break;	
	
	default:
		//do nothing..
}

$return['data'] = $data;
$return['title'] = $title;

echo json_encode($return);
?>
