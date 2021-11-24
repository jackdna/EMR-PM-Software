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
$OBJCommonFunction = new CLSCommonFunction;

$start = $_REQUEST['start'];
$aText = strtoupper($ins_name[0]);
$aPage = $_REQUEST['aPage'];

///////Code To Check Duplicate Name And Code**********/
if($ins_name != "" && $ins_id == ""){
	$vquery_chk = "select * from insurance_companies where name = '$ins_name' and in_house_code = '$in_house_code'";
	$vsql_chk = imw_query($vquery_chk);
	$rt_chk = imw_num_rows($vsql_chk);
	if($rt_chk>0){
		header('location:index.php?err_exist=1&st=$start');
	}
}
if($ins_name<>"" && $ins_id<>""){
	$vquery_chk = "select * from insurance_companies where id!='$ins_id' and name='$ins_name' and in_house_code='$in_house_code' ";
	$vsql_chk = imw_query($vquery_chk);
	$rt_chk = imw_num_rows($vsql_chk);
	if($rt_chk>0){
		header('location:index.php?err_exist=1&st=$start');
	}
}
//***********End Code To check Duplicate Name*******//
$phone = core_phone_unformat($phone);
$fax = core_phone_unformat($fax);
if($Print_form == 1){
	$Print_form = "yes";
}
else{
	$Print_form = "No";
}
if($FeeTable<=0){
	$FeeTable=1;
}

//--- SAVE INSURANCE DATA -------
$insDataArr = array();
$insDataArr['name'] = $ins_name;
$insDataArr['contact_address'] = $contact_address;
$insDataArr['contact_name'] = $contact_name;
$insDataArr['email'] = $email;
$insDataArr['phone'] = core_phone_unformat($phone);
$insDataArr['fax'] = core_phone_unformat($fax);
$insDataArr['payer_type'] = $payer_type;
$insDataArr['Insurance_payment'] = $Insurance_payment;
$insDataArr['secondary_payment_method'] = $secondary_payment_method;
$insDataArr['processSecondaryIns'] = $processSecondaryIns;
$insDataArr['processTertiaryIns'] = $processTertiaryIns;
$insDataArr['attn'] = $ins_desc;
$insDataArr['cms_id'] = 1;
$insDataArr['freeb_type'] = 1;
$insDataArr['x12_receiver_id'] = 1;
$insDataArr['x12_default_partner_id'] = 1;
$insDataArr['in_house_code'] = $in_house_code;
$insDataArr['City'] = $City;
$insDataArr['State'] = $State;
$insDataArr['Zip'] = $Zip;
$insDataArr['zip_ext'] = $zip_ext;
$insDataArr['insurance_Practice_Code_id'] = $insurance_Practice_Code_id;
$insDataArr['BatchFile'] = $BatchFile;
$insDataArr['Reciever_id'] = $Reciever_id;
$insDataArr['Payer_id'] = $Payer_id;
$insDataArr['Payer_id_pro'] = $Payer_id_pro;
$insDataArr['FeeTable'] = $FeeTable;
$insDataArr['institutional_Code_id'] = $institutional_Code_id;
$insDataArr['collect_copay'] = $collect_copay;
$insDataArr['frontdesk_desc'] = $frontdesk_desc;
$insDataArr['billing_desc'] = $billing_desc;
$insDataArr['ins_del_status'] = $ins_status;
$insDataArr['ins_type'] = $ins_type;
$insDataArr['claim_type'] = $claim_type;
$insDataArr['institutional_type'] = $institutional_type;
$insDataArr['emdeon_payer_eligibility'] = $_REQUEST["txtPayerEmdEli"];
$insDataArr['co_ins'] = $co_ins;
$insDataArr['collect_sec_ins'] = $collect_sec;
$insDataArr['groupedIn'] = $select_ins_comp_group;

if($ins_id>0){
	UpdateRecords($ins_id,'id',$insDataArr,'insurance_companies');
	if($collect_sec  == '1' && $co_ins != ""){
		if($co_ins != $pre_co_ins){
			$qry_update ="update insurance_data set co_ins='".$co_ins."' where type='primary' and provider='".$ins_id."' and actInsComp='1'";
			$res_update = imw_query($qry_update);
		}else if($pre_collect_sec != $collect_sec){
			$qry_update ="update insurance_data set co_ins='".$co_ins."' where type='primary' and provider='".$ins_id."' and actInsComp='1'";
			$res_update = imw_query($qry_update);
			
		}
	}
	
}else{
	AddRecords($insDataArr,'insurance_companies');
}
if(constant("EXTERNAL_INS_MAPPING") == "YES"){
	$OBJCommonFunction->createInsCompXMLCrossMap();
}
else{
	$OBJCommonFunction->createInsCompXML();
}
//$OBJCommonFunction->createAllInsCompXML();
header("location: index.php?text=$aText&st=$start&page=$aPage");
?>