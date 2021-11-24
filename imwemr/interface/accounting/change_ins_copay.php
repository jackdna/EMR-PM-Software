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
require_once("../../config/globals.php");
require_once("../../library/classes/acc_functions.php"); 
require_once("../../library/classes/common_function.php");  
$encounter_id=$_REQUEST['enc_id'];
$copay_ins_pri=$_REQUEST['copay_ins_pri'];
$copay_ins_sec=$_REQUEST['copay_ins_sec'];
$copay_ins_tri=$_REQUEST['copay_ins_tri'];
$getPriInsId=$_REQUEST['getPriInsId'];
$getSecInsId=$_REQUEST['getSecInsId'];
$ins=$_REQUEST['ins'];
$patient_id = $_SESSION['patient'];
$ins_case_type=$_REQUEST['ins_case_type'];

$qry = imw_query("SELECT sec_copay_collect_amt,sec_copay_for_ins  FROM copay_policies WHERE policies_id='1'");
$policyQryRes = imw_fetch_array($qry);
$sec_copay_collect_amt = $policyQryRes['sec_copay_collect_amt'];
$sec_copay_for_ins = $policyQryRes['sec_copay_for_ins'];

if($encounter_id){
	$getCaseTypeStr = "SELECT * FROM patient_charge_list WHERE del_status='0' and encounter_id = '$encounter_id' AND patient_id = '$patient_id'";
	$getCaseTypeQry = imw_query($getCaseTypeStr);
	$getCaseTypeRow = imw_fetch_array($getCaseTypeQry);
	$charge_list_id = $getCaseTypeRow['charge_list_id'];
	$copay = $getCaseTypeRow['copay'];
	$copayPaid = $getCaseTypeRow['copayPaid'];
	$case_type_id = $getCaseTypeRow['case_type_id'];		
	
	$getSubmittedDateStr = "SELECT DATE_FORMAT(submited_date, '%m-%d-%Y') FROM submited_record WHERE encounter_id = '$encounter_id' ORDER BY submited_id DESC";
	$getSubmittedDateQry = imw_query($getSubmittedDateStr);			
	if(imw_num_rows($getSubmittedDateQry)<=0){
		// GET COPAY OF SEC. AND TER. IS TO ADD OR NOT
		if(($copayPaid!=1)){
			if($copay_ins_pri){
				if($ins=='yes'){
					$ins_up = imw_query("UPDATE insurance_data set copay='$copay_ins_pri' WHERE ins_caseid = '$ins_case_type' AND pid = '$patient_id' AND provider ='$getPriInsId' AND actInsComp = '1' and type='primary'");
				}	
			}
			if($copay_ins_sec){
				if($ins=='yes'){
					$ins_up = imw_query("UPDATE insurance_data set copay='$copay_ins_sec' WHERE ins_caseid = '$ins_case_type' AND pid = '$patient_id' AND provider ='$getSecInsId' AND actInsComp = '1' and type='secondary'");
				}	
			}
		}	
		
		//Secondary copay collect check
		$copay_policies = ChkSecCopay_collect($getPriInsId);
		$secCopay=$copay_policies;
		if($secCopay=='Yes'){
			if($sec_copay_collect_amt>=$copay_ins_sec || $sec_copay_for_ins==''){
				$copay_ins_sec=$copay_ins_sec;
			}else{
				$copay_ins_sec=0;
			}
		}else{
			$copay_ins_sec=0;
		}
		$copay_chlist=$copay_ins_pri+$copay_ins_sec;
		//Secondary copay collect check
		// UPDATE COPAY IF CHANGED AND NOT PAID
		if(($copayPaid!=1)){
				$up_char_list=imw_query("update patient_charge_list set copay='$copay_chlist',pri_copay='$copay_ins_pri',sec_copay='$copay_ins_sec' where encounter_id = '$encounter_id'");
		}
	}
}
?>