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


/*------SAVING SOCIAL RECORDS----------*/


$otherSocial = $_POST["elem_otherSocial"];
$source_of_smoke = $_POST["source_of_smoke"];
$source_of_smoke_other = $_POST["source_of_smoke_other"];
$number_of_years_with_smoke = $_POST["number_of_years_with_smoke"];
$source_of_alcohal_other = $_POST["source_of_alcohal_other"];
$alcohal_time = $_POST["alcohal_time"];
$alcohal_consumption = $_POST["alcohal_quentity"];
$smoke_status = $_POST["SmokingStatus"];
$smoke_start_date = getDateFormatDB($_POST["smoke_start_date"]);
$smoke_end_date = getDateFormatDB($_POST["smoke_end_date"]);
$dateOCC = getDateFormatDB($_REQUEST["txtDateOfferedCessationCounselling"]);

if($dateOCC == "0000-00-00" || $dateOCC == "--" || $dateOCC == ""){
	$dateOCC = "";
}

$check_data2="select * from social_history where patient_id=$pid";
$checkSql2 = imw_query($check_data2) or die (imw_error());
$checkrows2=imw_num_rows($checkSql2);
if($checkrows2>0){
		// update
		$row = imw_fetch_assoc($checkSql2);
		$newSocialHistoryId =  $row['social_id'];	
		$socialsaveqry = "update social_history set ";
		$socialsaveqry .= " patient_id=".(int) $pid." ";
	}else{			
		// insert new
		$socialsaveqry = "insert into social_history set ";					
		$socialsaveqry .= " patient_id=".(int) $pid." ";
	}
	
	$add_smoke_detail="";
	$add_smoke_id="";
	if($smoke_status!=""){
		$smoke_qry=imw_query("select * from smoking_status_tbl where id='".$smoke_status."'");		
		$smoke_row=imw_fetch_array($smoke_qry);
		$add_smoke_detail= ucfirst($smoke_row['desc']).' / '.$smoke_row['code'];
		$add_smoke_id= $smoke_row['id'];
	}
	
	$offered_cessation_counseling = $_REQUEST["offered_cessation_counseling"];
	$socialsaveqry.= " ,smoke='".imw_real_escape_string(htmlentities($smoke))."' ";
	$socialsaveqry.= " ,smoke_perday='".imw_real_escape_string(htmlentities($smoke_perday))."' ";
	$socialsaveqry.= " ,smoke_counseling ='".imw_real_escape_string(htmlentities($offered_cessation_counseling))."' ";	
	$socialsaveqry.= " ,list_drugs='".imw_real_escape_string(htmlentities($list_drugs))."' ";
	$socialsaveqry.= " ,alcohal='".imw_real_escape_string(htmlentities(implode(",",$alcohal)))."' ";
	$socialsaveqry.= " ,otherSocial='".imw_real_escape_string(htmlentities($otherSocial))."' ";
	$socialsaveqry.= " ,source_of_smoke ='".imw_real_escape_string(htmlentities($source_of_smoke))."' ";
	$socialsaveqry.= " ,source_of_smoke_other ='".imw_real_escape_string(htmlentities($source_of_smoke_other))."' ";
	$socialsaveqry.= " ,number_of_years_with_smoke ='".imw_real_escape_string(htmlentities($number_of_years_with_smoke))."' ";
	$socialsaveqry.= " ,smoke_years_months ='".imw_real_escape_string(htmlentities($_REQUEST['smoke_years_months']))."' ";
	$socialsaveqry.= " ,source_of_alcohal_other ='".imw_real_escape_string(htmlentities($source_of_alcohal_other))."' ";
	$socialsaveqry.= " ,alcohal_time ='".imw_real_escape_string(htmlentities($alcohal_time))."' ";
	$socialsaveqry.= " ,consumption ='".imw_real_escape_string(htmlentities($alcohal_consumption))."' ";
	$socialsaveqry.= " ,smoking_status ='".imw_real_escape_string(htmlentities($add_smoke_detail))."' ";
	$socialsaveqry.= " ,smoking_status_id ='".imw_real_escape_string(htmlentities($add_smoke_id))."' ";
	$socialsaveqry.= " ,offered_cessation_counselling_date ='".imw_real_escape_string(htmlentities($dateOCC))."' ";
	$socialsaveqry.= " ,cessation_counselling_other  ='".imw_real_escape_string(htmlentities($_REQUEST['cessationCounsellingOther']))."' ";
	$socialsaveqry.= " ,cessation_counselling_option ='".imw_real_escape_string(htmlentities($_REQUEST['cessationCounselling']))."' ";	
	$socialsaveqry.= " ,intervention_not_performed_status ='".imw_real_escape_string(htmlentities($_REQUEST['interventionNotPerformedStatus']))."' ";	
	$socialsaveqry.= " ,intervention_reason_option ='".imw_real_escape_string(htmlentities($_REQUEST['interventionReason']))."' ";	
	$socialsaveqry.= " ,med_order_not_performed_status ='".imw_real_escape_string(htmlentities($_REQUEST['medOrderNotPerformedStatus']))."' ";	
	$socialsaveqry.= " ,med_order_reason_option ='".imw_real_escape_string(htmlentities($_REQUEST['medOrderReason']))."' ";	
	$socialsaveqry.= " ,modified_on = now()";
	$socialsaveqry.= " ,modified_by ='".$_SESSION["authId"]."' ";
	$socialsaveqry.= " ,smoke_start_date ='".imw_real_escape_string(htmlentities($smoke_start_date))."' ";
	$socialsaveqry.= " ,smoke_end_date ='".imw_real_escape_string(htmlentities($smoke_end_date))."' ";
	$socialsaveqry.= " ,use_of_alcohol ='".imw_real_escape_string(htmlentities($_REQUEST['use_of_alcohol']))."' ";
	$socialsaveqry.= " ,use_of_drugs ='".imw_real_escape_string(htmlentities($_REQUEST['use_of_drugs']))."' ";
	
	if($checkrows2>0){			
		// update
		$socialsaveqry .= " where patient_id='".(int) $pid."' ";
	}
	
	$socialsaveSql = imw_query($socialsaveqry);
	if($checkrows2==0){			
		$newSocialHistoryId = imw_insert_id();
	}
	if(imw_error() != ''){
		echo ("Error : ". imw_error() ."<br>".$socialsaveSql);
		$socialHistoryError = "Error : ". imw_errno() . ": " . imw_error();
	}
/*--------END OF SAVING SOCIAL SMOKE--------*/
if(!$_REQUEST['number_of_years_with_smoke']){
	$_REQUEST['number_of_years_with_smoke'] = 0;
}

?>