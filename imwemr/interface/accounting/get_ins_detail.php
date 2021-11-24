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
$pat_id=$_SESSION['patient'];
$enc_dos=date('Y-m-d');

$_GET['insCompanyId'] = (int)xss_rem($_GET['insCompanyId'], 3); /* Sanitization to prevent arbitrary values - Security Fix */

if($_GET['insCompanyId']>0){
	$qryCheckInsCompClaim="Select ins_accept_assignment from insurance_companies where id=".$_GET['insCompanyId'];
	$resCheckInsCompClaim=imw_query($qryCheckInsCompClaim);
	$rowCheckInsCompClaim=imw_fetch_assoc($resCheckInsCompClaim);
	echo $insCompClaim=$rowCheckInsCompClaim['ins_accept_assignment'];
}else{
	if($_SESSION['currentCaseid']>0){
		$curCaseId=$_SESSION['currentCaseid'];
	}
	$qry_case_id = "select case_type_id from schedule_appointments where 
					sa_app_start_date='$enc_dos' and  sa_patient_id='$pat_id' and 
					sa_patient_app_status_id NOT IN (203,201,18,19,20) order by sa_app_starttime desc limit 0,1";												
	$run_case_id = imw_query($qry_case_id);	
	$fet_case_list=imw_fetch_array($run_case_id);
	if($fet_case_list['case_type_id']>0){
		$curCaseId=$fet_case_list['case_type_id'];
	}
	if($_SESSION['acc_usr_data']['caseTypeText'][$pat_id]>0){
		$curCaseId=$_SESSION['acc_usr_data']['caseTypeText'][$pat_id];
	}
	$case_chk="";
	if($curCaseId>0){
		$case_chk="and insurance_case.ins_caseid='$curCaseId'";
	}
	$qryCheckInsCompClaim = "select insurance_companies.ins_accept_assignment
	from insurance_case join insurance_case_types
	on insurance_case_types.case_id = insurance_case.ins_case_type 
	join insurance_data on insurance_data.ins_caseid = insurance_case.ins_caseid
	join insurance_companies on insurance_companies.id = insurance_data.provider
	where insurance_case.patient_id = '$pat_id'
	and insurance_case.case_status = 'Open' 
	and insurance_data.provider > 0
	and insurance_data.actInsComp='1'
	$case_chk
	and insurance_data.type='primary'
	and insurance_companies.in_house_code != 'n/a'
	GROUP BY insurance_case.ins_caseid 
	ORDER BY insurance_case.ins_case_type";
	
	$resCheckInsCompClaim=imw_query($qryCheckInsCompClaim);
	$rowCheckInsCompClaim=imw_fetch_assoc($resCheckInsCompClaim);
	echo $insCompClaim=$rowCheckInsCompClaim['ins_accept_assignment'];
}

 ?>