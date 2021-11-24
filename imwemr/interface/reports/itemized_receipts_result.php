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

ini_set("memory_limit","3072M");
set_time_limit (0);

$dateFormat= get_sql_date_format();
$curDate = date($phpDateFormat.' h:i A');

$FCName= $_SESSION['authId'];
$pureSelfPay=false;

//check is pure self pay (only)selected
if($ins_type=='Self Pay')
{
	$pureSelfPay=true;	
	//if this option is selected then we need to ignore selected insurance companies
	$insuranceName='';
}


$printFile = true;
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

	
	
	$printFile = false;
	$Sdate = $Start_date;
	$Edate = $End_date;
	$writte_off_arr = array();
	$arrInsOfEnc=array();

	//CHECK FOR PRIVILEGED FACILITIES
	if(sizeof($facility_id)<=0 && isPosFacGroupEnabled()){
		$facility_id = $CLSReports->getFacilityName('', '0', 'array');

		if(sizeof($facility_id)<=0){
			$facility_id[0]='NULL';
		}
	}
	
	//MAKE COMMA SEPEARATED OF ALL SEARCH CRITERIA
	$grp_id= (sizeof($grp_id)>0) ? implode(',',$grp_id) : '';
	$sc_name= (sizeof($facility_id)>0) ? implode(',',$facility_id) : '';
	$Physician= (sizeof($filing_provider)>0) ? implode(',',$filing_provider) : '';
	//---------------------------------------

	//--- CHANGE DATE FORMAT -------
	if($Start_date != '' && $End_date != ''){
		$Start_date = getDateFormatDB($Start_date);
		$End_date = getDateFormatDB($End_date);	
	}
		

	$qry="Select encounter_id from patient_charge_list where (date_of_service between '$Start_date' and '$End_date') AND del_status='0'";
	if(empty($patientId) == false){
		$qry.= " and patient_id='$patientId'";
	}
	if(empty($sc_name) == false){
		$qry.= " and facility_id IN ($sc_name)";
	}
	if(empty($Physician) === false){
		$qry.= " and primary_provider_id_for_reports IN ($Physician)";	
	}
	if(empty($grp_id) == false){
		$qry.= " and gro_id IN ($grp_id)";
	}
	
	$rs=imw_query($qry);
	while($res = imw_fetch_assoc($rs)){
		$printFile=true;
		$arr_encounters[$res['encounter_id']]=$res['encounter_id'];
	}
	
	if(sizeof($arr_encounters)>0){
		$str_encounters=implode(',', $arr_encounters);
	}
}



if($callFrom!='scheduled'){
	if($printFile==true){
		echo '<div class="text-center alert alert-info">Please click on below "Print Receipt" button to generate PDF.</div>';
	}else{
		echo '<div class="text-center alert alert-info">No Record Found.</div>';
	}
}
?>
