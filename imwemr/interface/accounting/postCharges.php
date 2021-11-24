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
include_once(dirname(__FILE__)."/../../library/classes/billing_functions.php");
include_once(dirname(__FILE__)."/../../library/classes/common_function.php");

$show = $_REQUEST['show'];
$operator_id = $_SESSION['authId'];
$entered_date = date('Y-m-d H:i:s');

//--- GET PATIENT NAME ----
if($post_pat_id>0){
	$patient_id = $post_pat_id;
}else{
	$patient_id = $_SESSION['patient'];
}

$open_ins_case = $_SESSION["current_caseid"];
if($encounter_id){
	$encounter_id = $encounter_id;
}else{
	$encounter_id = xss_rem($_REQUEST['eId']);
}
if($chld_day_ids!=""){
	$chld_ids = $chld_day_ids;
}else{
	$temp_chld_ids = xss_rem($_REQUEST['chld_ids']);
	$chld_ids = substr($temp_chld_ids,0,-1);
}
$proc_codes = substr($_REQUEST['proc_codes'],0,-1);

$getRefChkStr = "Select auto_pt_balance,write_off_code,accept_assignment FROM copay_policies WHERE policies_id = '1'";
$getRefChkQry = imw_query($getRefChkStr);
$getRefChkRow = imw_fetch_assoc($getRefChkQry);
$pol_auto_pt_balance = $getRefChkRow['auto_pt_balance'];
$pol_write_off_code = $getRefChkRow['write_off_code'];

//--- GET ENCOUNTER DETAILS ----
$qry = imw_query("Select * from patient_charge_list WHERE del_status='0' and encounter_id='$encounter_id' AND patient_id='$patient_id'");
$getPostOrRePostRow = imw_fetch_array($qry);
$submitStatus = $getPostOrRePostRow['submitted'];
$case_type_id = $getPostOrRePostRow['case_type_id'];
$firstSubmitDate = $getPostOrRePostRow['firstSubmitDate'];
$hcfaStatus = $getPostOrRePostRow['hcfaStatus'];
$Re_submitted = $getPostOrRePostRow['Re_submitted']; 
$primaryInsuranceCoId = $getPostOrRePostRow['primaryInsuranceCoId'];
$secondaryInsuranceCoId = $getPostOrRePostRow['secondaryInsuranceCoId'];
$tertiaryInsuranceCoId = $getPostOrRePostRow['tertiaryInsuranceCoId'];
$primary_paid  = $getPostOrRePostRow['primary_paid'];
$charge_list_id_send=$getPostOrRePostRow['charge_list_id'];

$ready_for_secondary="";
$chld_posted_amount_arr=array();
$chld_post_qry=imw_query("Select totalAmount,charge_list_detail_id,last_pri_paid_date,procCode from patient_charge_list_details where del_status='0' and charge_list_detail_id in($chld_ids) AND patient_id='$patient_id' and procCode>0");
while($chld_post_row=imw_fetch_array($chld_post_qry)){	
	$chld_posted_amount_arr[$chld_post_row['charge_list_detail_id']]=$chld_post_row['totalAmount'];
	if($chld_post_row['last_pri_paid_date']!='0000-00-00'){
		$ready_for_secondary="yes";
	}
	$chld_proc_id_arr[$chld_post_row['charge_list_detail_id']]=$chld_post_row['procCode'];
}
$postedAmount = array_sum($chld_posted_amount_arr);

//------------------------ Insurance Company ------------------------//
$qry = "select * from insurance_companies where (id='$primaryInsuranceCoId' or id='$secondaryInsuranceCoId' or id='$tertiaryInsuranceCoId')  and name != 'SELF PAY'";
$res = imw_query($qry);
while($row = imw_fetch_array($res)){
	if($row['id']==$primaryInsuranceCoId){
		$insDetails[1]=$row;
	}
	if($row['id']==$secondaryInsuranceCoId){
		$insDetails[2]=$row;
	}
	if($row['id']==$tertiaryInsuranceCoId){
		$insDetails[3]=$row;
	}
}
//------------------------ Insurance Company ------------------------//
if($ar_ajax==""){
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<title>Post Charges</title>
<link href="../../library/css/bootstrap.min.css" type="text/css" rel="stylesheet">
<link href="../../library/css/bootstrap-select.css" type="text/css" rel="stylesheet">
<link href="../../library/css/common.css" type="text/css" rel="stylesheet">
<link href="../../library/css/accounting.css" type="text/css" rel="stylesheet">

<script type="text/javascript" src="../../library/js/jquery.min.1.12.4.js"></script>
<script type="text/javascript" src="../../library/js/bootstrap.min.js"></script>
<script type="text/javascript" src="../../library/js/bootstrap-select.js"></script>
<script type="text/javascript" src="../../library/js/common.js"></script>
<script type="text/javascript" src="../../library/js/acc_common.js"></script> 
</head>
<body>
<?php
}
$secInsId=array();
$terInsId=array();
if($submitToIns){
	$posted_time=date('H:i:s');
	$post_patient_id=$patient_id;
	if($_REQUEST["post_for"]==""){
		$_REQUEST["post_for"]=1;
	}
	$post_for=$_REQUEST["post_for"];
	
	include("capitation_writeoff_update.php");
	
	if($pol_auto_pt_balance>0){
		include("contract_writeoff_update.php");
	}
	if($post_date!="00-00-0000" && $post_date!=""){
		$post_date_exp=explode('-',$post_date);	
		$postedToInsDate = $post_date_exp[2].'-'.$post_date_exp[0].'-'.$post_date_exp[1];
	}else{
		$postedToInsDate = date('Y-m-d');
	}
	
	$qry = imw_query("Select * from patient_charge_list WHERE del_status='0' and encounter_id='$encounter_id' AND patient_id='$post_patient_id'");
	$getPostedAmtOfEncRow = imw_fetch_array($qry);
	$alreadyPosted = $getPostedAmtOfEncRow['postedAmount'];
	$charge_list_id = $getPostedAmtOfEncRow['charge_list_id'];
	$submitted = $getPostedAmtOfEncRow['submitted'];
	$primaryInsuranceCoId = $getPostedAmtOfEncRow['primaryInsuranceCoId'];
	$secondaryInsuranceCoId = $getPostedAmtOfEncRow['secondaryInsuranceCoId'];
	$tertiaryInsuranceCoId = $getPostedAmtOfEncRow['tertiaryInsuranceCoId'];
	$referral_status = $getPostedAmtOfEncRow['referral_status'];
	$reff_number_val = $getPostedAmtOfEncRow['referral'];
	$auth_status = $getPostedAmtOfEncRow['auth_status'];
	$auth_number_val=$getPostedAmtOfEncRow['auth_no'];
	$auth_id=$getPostedAmtOfEncRow['auth_id'];
	
	//----- Delete Charge_list_id From Statement Table ---------
	imw_query("delete from statement_tbl where charge_list_id = '$charge_list_id'");
	
	$selfPayFlag = true;
	if($primaryInsuranceCoId > 0){
		$selfPayFlag = false;
	}
	else if($secondaryInsuranceCoId > 0){
		$selfPayFlag = false;
	}
	else if($tertiaryInsuranceCoId > 0){
		$selfPayFlag = false;
	}
	//---- REFFRALL DECREMENT ------
	if($referral_status == 'false' and trim($reff_number_val) != '' and $selfPayFlag == false){
		$qry = imw_query("select patient_reff.reff_id,no_of_reffs,reff_used from patient_reff join insurance_data on 
				insurance_data.id = patient_reff.ins_data_id where patient_reff.insCaseid = insurance_data.ins_caseid
				and patient_reff.patient_id = insurance_data.pid
				and patient_reff.ins_provider = insurance_data.provider
				and patient_reff.no_of_reffs > 0
				and insurance_data.ins_caseid = '$case_type_id'
				and insurance_data.pid = '$post_patient_id'
				and insurance_data.referal_required = 'yes'
				and insurance_data.actInsComp = '1' 
				and patient_reff.reffral_no='$reff_number_val'
				and patient_reff.del_status='0'
				order by patient_reff.reff_id desc limit 0,1");
		$refferalRes = imw_fetch_array($qry);
		$reffId = $refferalRes['reff_id'];
		$no_of_reffs = $refferalRes['no_of_reffs'] - 1;
		$reff_used = $refferalRes['reff_used'] + 1;
		
		$qry = imw_query("update patient_reff set no_of_reffs = $no_of_reffs, reff_used = $reff_used where reff_id = '$reffId'");
		$referral_status = 'true';
		
	}
	//-----Authorization DECREMENT ----------------//
	if($auth_status=='false' and trim($auth_number_val) != '' and $selfPayFlag == false){
		$qry = imw_query("select a_id,no_of_reffs,reff_used from patient_auth where a_id='$auth_id' and no_of_reffs>0");
		$authRes = imw_fetch_array($qry);
		if(imw_num_rows($qry)>0){
			$authId = $authRes['a_id'];
			$no_of_reffs = $authRes['no_of_reffs'] - 1;
			$reff_used = $authRes['reff_used'] + 1;
			
			$qry = imw_query("update patient_auth set no_of_reffs = $no_of_reffs, reff_used = $reff_used where a_id = '$authId'");
			$auth_status='true';
		}
	}
	
	//--- CHECK SUBMITED RECORD ----
	$sub_qry = imw_query("select count(encounter_id) as rowCnt from submited_record where encounter_id = '$encounter_id'");
	$submitedRecordRes = imw_fetch_array($sub_qry);
	$submitRowCnt = $submitedRecordRes['rowCnt'];

	$post_status = '';
	$primary_paid = 'false';
	$secondary_paid = 'false';
	$tertiary_paid = 'false';
	$priSubmit = 0;

	if($submitRowCnt > 0){
		$reSubmited = 'true';
		$reSubmitedDate = date('Y-m-d');
		$posted_type="resubmitted";			
	}
	else{
		$reSubmited = 'false';
		$posted_type="posted";
	}
	
	$qry = imw_query("update patient_charge_list_details set posted_status='1',claim_status='0' where charge_list_detail_id in($chld_ids) and procCode>0");
	
	$enc_accept_assignment=$insDetails[1]['ins_accept_assignment'];
	$claim_delay_code=$_REQUEST["claim_delay_code"];
	$qry = imw_query("update patient_charge_list SET enc_accept_assignment='$enc_accept_assignment',claim_delay_code='$claim_delay_code' where encounter_id = '$encounter_id' and patient_id = '$post_patient_id'");
	
	//--- print HCFA form -----
	if($status){
		$qry = imw_query("select * from patient_charge_list where del_status='0' and charge_list_id = '$charge_list_id'");
		$chargeListDetail = imw_fetch_object($qry);
		$reffPhyscianId = $chargeListDetail->reff_phy_id;
		$reff_phy_nr = $chargeListDetail->reff_phy_nr;
		
		$qry = imw_query("select * from patient_data where id= '".$chargeListDetail->patient_id."'");
		$patientDetail = imw_fetch_object($qry);
		if($reffPhyscianId == 0 || $reffPhyscianId == ''){
			$reffPhyscianId = $patientDetail->providerID;
			
			$qry = imw_query("select * from users where id= '".$reffPhyscianId."'");
			$reffDetail = imw_fetch_object($qry);
			$reffPhysicianLname = $reffDetail->lname;
			$reffPhysicianFname = $reffDetail->fname;
			$reffPhysicianMname = $reffDetail->mname;					
			$npiNumber = $reffDetail->user_npi;
			$Texonomy = $reffDetail->TaxonomyId;
		}
		else{
			$qry = imw_query("select * from refferphysician where physician_Reffer_id= '".$reffPhyscianId."'");
			$reffDetail = imw_fetch_object($qry);
			$reffPhysicianLname = $reffDetail->LastName;
			$reffPhysicianFname = $reffDetail->FirstName;
			$reffPhysicianMname = $reffDetail->MiddleName;
			$npiNumber = $reffDetail->NPI;
			$Texonomy = $reffDetail->Texonomy;
		}
		$qry = imw_query("select * from users where id= '".$chargeListDetail->primaryProviderId."'");
		$renderingPhyDetail = imw_fetch_object($qry);
		//---- Patient Validate Check -------
		$validation = false;
		if($chargeListDetail->primaryInsuranceCoId ==0 && $validation == false){
			$validation = true;
			$error[$charge_list_id] = 'Patient Primary Infomation is Required.';
		}
		if($patientDetail->sex == '' && $validation == false){
			$validation = true;
			$error[$charge_list_id] = 'Patient Gender Infomation is Required.';
		}
		if($reff_phy_nr==0 && $npiNumber == '' && $validation == false){
			$validation = true;
			$error[$charge_list_id] = 'Referring Physician NPI # is Required.';
		}
		if($Texonomy == '' && $validation == false){
			//$validation = true;
			//$error[$charge_list_id] = 'Referring Physician Taxonomy # is Required.';
		}
		if($renderingPhyDetail->user_npi == '' && $validation == false){
			$validation = true;
			$error[$charge_list_id] = 'Rendering Physician NPI # is Required.';
		}
		if($renderingPhyDetail->TaxonomyId == '' && $validation == false){
			$validation = true;
			$error[$charge_list_id] = 'Rendering Physician Taxonomy # is Required.';
		}
		if($validation == true){
			$invalidChargeListId[0] = $charge_list_id;
		}
		else{
			$validChargeListId[0] = $charge_list_id;
			$_REQUEST['chl_chk_box'][]=$charge_list_id;
		}
		$priSubmit = 0;
		$InsComp=$post_for;
		$post_bill_type=$_REQUEST['post_bill_type'];
		$newFile="yes";
		$print_paper_type=$_REQUEST['print_hcfa_ub'];
		
		if(count($validChargeListId)>0){
			if($day_charges_chk==""){
				if($print_paper_type=="Printub" || $print_paper_type=="WithoutPrintub"){
					require_once(getcwd()."/../billing/print_ub.php");
				}else{
					require_once(getcwd()."/../billing/print_hcfa_form.php");
				}
				$priSubmit = 1;
			}
		}
		else{
			$errmsg = 'Invalid Claim.';
			//$errmsg = $error[$charge_list_id];
		}
	}

	$post_for=$_REQUEST["post_for"];
	if($chk_day_charges!=""){
		$post_for=1;
	}
	if($post_for==3){
		$ins_comp_id=$getPostedAmtOfEncRow['tertiaryInsuranceCoId'];
		$enterPostedDateQry = imw_query("update patient_charge_list 
		SET postedDate='$postedToInsDate',submit = '0', primarySubmit = '1',
		secondarySubmit = '1', TertairySubmit = '$priSubmit', postedAmount = '$postedAmount',
		payment_status = 'submitted', submitted = 'true', hcfaStatus = '0',
		referral_status = '$referral_status',auth_status='$auth_status', submitted_operator_id = '$operator_id',
		Re_submitted = '$reSubmited', Re_submitted_date = '$reSubmitedDate',
		firstSubmitDate = '$postedToInsDate', primary_paid = 'true'
		, secondary_paid = 'true', tertiary_paid = 'false'
		where encounter_id = '$encounter_id' and patient_id = '$post_patient_id'");
	}else if($post_for==2){
		$ins_comp_id=$getPostedAmtOfEncRow['secondaryInsuranceCoId'];
		$enterPostedDateQry = imw_query("update patient_charge_list 
		SET postedDate='$postedToInsDate',submit = '0', primarySubmit = '1',
		secondarySubmit = '$priSubmit', TertairySubmit = '0', postedAmount = '$postedAmount',
		payment_status = 'submitted', submitted = 'true', hcfaStatus = '0',
		referral_status = '$referral_status',auth_status='$auth_status', submitted_operator_id = '$operator_id',
		Re_submitted = '$reSubmited', Re_submitted_date = '$reSubmitedDate',
		firstSubmitDate = '$postedToInsDate', primary_paid = 'true'
		, secondary_paid = 'false', tertiary_paid = 'false'
		where encounter_id = '$encounter_id' and patient_id = '$post_patient_id'");
	}else if($post_for==1){
		$ins_comp_id=$getPostedAmtOfEncRow['primaryInsuranceCoId'];	
		if($ins_comp_id>0){
			$enterPostedDateQry = imw_query("update patient_charge_list 
			SET postedDate='$postedToInsDate',submit = '0', primarySubmit = '$priSubmit',
			secondarySubmit = '0', TertairySubmit = '0', postedAmount = '$postedAmount',
			payment_status = 'submitted', submitted = 'true', hcfaStatus = '0',
			referral_status = '$referral_status',auth_status='$auth_status', submitted_operator_id = '$operator_id',
			Re_submitted = '$reSubmited', Re_submitted_date = '$reSubmitedDate',
			firstSubmitDate = '$postedToInsDate', primary_paid = 'false'
			, secondary_paid = 'false', tertiary_paid = 'false'
			where encounter_id = '$encounter_id' and patient_id = '$post_patient_id'");
		}else{
			$post_for=0;
			if($getPostedAmtOfEncRow['primaryInsuranceCoId']==0 && $getPostedAmtOfEncRow['secondaryInsuranceCoId']==0 && $getPostedAmtOfEncRow['tertiaryInsuranceCoId']==0){
				$enterPostedDateQry = imw_query("update patient_charge_list 
				SET postedDate='$postedToInsDate',submit = '0', primarySubmit = '0',
				secondarySubmit = '0', TertairySubmit = '0', postedAmount = '$postedAmount',
				payment_status = 'submitted', submitted = 'true', hcfaStatus = '0',
				referral_status = '$referral_status',auth_status='$auth_status', submitted_operator_id = '$operator_id',
				Re_submitted = '$reSubmited', Re_submitted_date = '$reSubmitedDate',
				firstSubmitDate = '$postedToInsDate', primary_paid = 'false'
				, secondary_paid = 'false', tertiary_paid = 'false'
				where encounter_id = '$encounter_id' and patient_id = '$post_patient_id'");
			}
		}
	}else{
		if($getPostedAmtOfEncRow['primaryInsuranceCoId']==0 && $getPostedAmtOfEncRow['secondaryInsuranceCoId']==0 && $getPostedAmtOfEncRow['tertiaryInsuranceCoId']==0){
			$enterPostedDateQry = imw_query("update patient_charge_list 
			SET postedDate='$postedToInsDate',submit = '0', primarySubmit = '0',
			secondarySubmit = '0', TertairySubmit = '0', postedAmount = '$postedAmount',
			payment_status = 'submitted', submitted = 'true', hcfaStatus = '0',
			referral_status = '$referral_status',auth_status='$auth_status', submitted_operator_id = '$operator_id',
			Re_submitted = '$reSubmited', Re_submitted_date = '$reSubmitedDate',
			firstSubmitDate = '$postedToInsDate', primary_paid = 'false'
			, secondary_paid = 'false', tertiary_paid = 'false'
			where encounter_id = '$encounter_id' and patient_id = '$post_patient_id'");
		}
	}
	if($post_for>0 || ($getPostedAmtOfEncRow['primaryInsuranceCoId']==0 && $getPostedAmtOfEncRow['secondaryInsuranceCoId']==0 && $getPostedAmtOfEncRow['tertiaryInsuranceCoId']==0)){
		$enterPostedDateQry = imw_query("update patient_charge_list SET first_posted_date='$postedToInsDate',first_posted_opr_id='$operator_id'
		where encounter_id = '$encounter_id' and patient_id = '$post_patient_id' and first_posted_date='0000-00-00'");

		$chld_posted_amount=htmlentities(serialize($chld_posted_amount_arr));
		$enterPostedDateQry = imw_query("insert into posted_record 
			SET encounter_id='$encounter_id',charge_list_detail_ids='$chld_ids',patient_id = '$post_patient_id',chld_posted_amount='$chld_posted_amount',
			posted_amount = '$postedAmount',posted_date = '$postedToInsDate',posted_time='$posted_time',posted_for='$post_for',
			ins_comp_id='$ins_comp_id',operator_id='$operator_id',posted_type='$posted_type'");
	
		imw_query("update deniedpayment set status = '1',modified_date='$entered_date',modified_by='$operator_id' where encounter_id = '$encounter_id'");
	}
	if($post_for>0){
		set_due_by_posted($encounter_id,$chld_ids,$post_for,$enc_accept_assignment);
	}
	patient_bal_update($encounter_id);
	if($day_charges_chk==""){
	
	?>
		<script type="text/javascript">
			var errMsg = '<?php print $errmsg; ?>';			
			if(errMsg){
				alert(errMsg);
			}else{
				opener.top.alert_notification_show("Charges posted successfully.");
			}
			
			if(opener.top.fmain.$('#enter_charges').length>0){
				opener.top.change_main_Selection(window.opener.top.document.getElementById('AccountingEC'));
			}else if(opener.top.fmain.$('#makePaymentFrm').length>0){
				opener.top.change_main_Selection(window.opener.top.document.getElementById('AccountingRP'));
			}
			window.close();
		</script> 
	<?php
	}
}
if($day_charges_chk==""){
	$sub_qry = imw_query("select count(encounter_id) as rowCnt from submited_record where encounter_id = '$encounter_id'");
	$submitedRecordRes = imw_fetch_array($sub_qry);
	$submitRowCnt_chk = $submitedRecordRes['rowCnt'];
?>

<form name="postChargesFrm" action="postCharges.php" method="post">
	<input type="hidden" name="submitToIns" id="submitToIns" value="true">
	<input type="hidden" name="eId" id="eId" value="<?php echo xss_rem($encounter_id); ?>">
    <input type="hidden" name="chld_ids" id="chld_ids" value="<?php echo xss_rem($_REQUEST['chld_ids']); ?>">
    <input type="hidden" name="proc_codes" id="proc_codes" value="<?php echo $_REQUEST['proc_codes']; ?>">
    <input type="hidden" name="charge_list_id_send" id="charge_list_id_send" value="<?php echo $charge_list_id_send;?>">
    <input type="hidden" name="reff_number_val" id="reff_number_val" value="">
    <input type="hidden" name="status" id="status" value="">
    <div class="mainwhtbox" style="height:335px">
        <div class="row">
            <div class="col-sm-6 col-sm-offset-3">
                <div class="row">
                    <div class="col-sm-12 purple_bar">
                        <label>
                            <?php 
                                if($Re_submitted == 'true'){ 
                                    echo "Re-Submit"; 
                                }else{
                                    echo "Post Charges";
                                } 
                            ?>	
                        </label>	
                    </div>	
                     <?php if($submitRowCnt_chk>0 || $ready_for_secondary!=""){ $by_default_chk="checked";?>
                        <div class="col-sm-12 pt10">
                            <div class="row">
                                <?php if($primaryInsuranceCoId>0){?>
                                    <div class="col-xs-4">
                                        <div class="radio radio-inline">
                                            <input type="radio" id="primaryInsR" name="post_for" class="post_for_cls" value="1" <?php echo $by_default_chk; ?>>
                                            <label for="primaryInsR">Primary (<?php echo $insDetails[1]['in_house_code']; ?>) </label>
                                        </div>
                                    </div>
                                <?php $by_default_chk="";} ?>
                                
                                <?php if($secondaryInsuranceCoId>0){?>
                                    <div class="col-xs-4">
                                        <div class="radio radio-inline">
                                            <input type="radio" id="secondaryInsR" name="post_for" class="post_for_cls" value="2" <?php echo $by_default_chk; ?>>
                                            <label for="secondaryInsR">Secondary (<?php echo $insDetails[2]['in_house_code']; ?>)</label>
                                        </div>
                                    </div>	
                                <?php $by_default_chk="";} ?>
                                
                                <?php if($tertiaryInsuranceCoId>0){?>
                                    <div class="col-xs-4">
                                        <div class="radio radio-inline">
                                            <input type="radio" id="tertiaryInsR" name="post_for" class="post_for_cls" value="3" <?php echo $by_default_chk; ?>>
                                             <label for="tertiaryInsR">Tertiary (<?php echo $insDetails[3]['in_house_code']; ?>)</label> 
                                        </div>
                                    </div>	
                                <?php $by_default_chk="";} ?>		
                            </div>
                        </div>
                    <?php } ?>
                    <?php
					$cpt_alert_display_arr=array();
					$cpt_qry = imw_query("SELECT * FROM ins_cpt_alert WHERE ins_id = '".$primaryInsuranceCoId."' and del_by='0' and cpt_alert!='' order by id");
					if(imw_num_rows($cpt_qry)>0){
						while($cpt_rows=imw_fetch_assoc($cpt_qry)){
							$cpt_code_idArr=unserialize(html_entity_decode($cpt_rows['cpt_code_id']));
							foreach($chld_proc_id_arr as $key => $val){
								if($cpt_code_idArr[$val]!=""){
									$cpt_alert_display_arr[$cpt_code_idArr[$val]][]="&bull; ".$cpt_code_idArr[$val] ." - ". $cpt_rows['cpt_alert'].'<br>';
								}
							}
						}
						if(count($cpt_alert_display_arr)>0){
					?>
                        <div id="cpt_ins_alert" class="col-sm-12 pt10">
                            <div class="row">
                                <div class="col-sm-12">
                                    <label>Billing requirements by payer :-</label>	
                                </div>	
                                <div class="col-sm-12 text-primary">
                                   <p id="show_ins_cpt_alert_td">
                                   		<?php 
											foreach($cpt_alert_display_arr as $key => $val){
												foreach($cpt_alert_display_arr[$key] as $cpt_key => $cpt_val){
													echo $cpt_val;
												}
											}
										?> 
                                   </p>
                                </div>	
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    <?php }} ?>
                    <div id="invalid_claim_td" class="col-sm-12 pt10" style="display:none">
                        <div class="row">
                            <div class="col-sm-12">
                                <label>Charges can not be posted for clearing due to following errors :-</label>	
                            </div>	
                            <div class="col-sm-12 text-warning">
                                <p id="show_reason_td"></p>	
                            </div>	
                            <div class="col-sm-12 text-center" id="module_buttons">
                                <input type="button" onClick="window.close();" class="btn btn-danger" id="cancel_print" value="  Close  ">
                            </div>	
                        </div>
                    </div>
                    <div class="clearfix"></div>	
                    <div class="col-sm-12 pt10">
                        <?php 
                            $show_delay_arr=array();
                            if(in_array(strtoupper($insDetails[1]['Payer_id']),$arr_NE_Medicaid_payers) || in_array(strtoupper($insDetails[1]['Payer_id_pro']),$arr_NE_Medicaid_payers) || strtoupper($insDetails[1]['ins_type'])=='MC'){
                                $show_delay_arr[1]="yes";
                            }
                            if(in_array(strtoupper($insDetails[2]['Payer_id']),$arr_NE_Medicaid_payers) || in_array(strtoupper($insDetails[2]['Payer_id_pro']),$arr_NE_Medicaid_payers) || strtoupper($insDetails[2]['ins_type'])=='MC'){
                                $show_delay_arr[2]="yes";
                            }
                            if(in_array(strtoupper($insDetails[3]['Payer_id']),$arr_NE_Medicaid_payers) || in_array(strtoupper($insDetails[3]['Payer_id_pro']),$arr_NE_Medicaid_payers) || strtoupper($insDetails[3]['ins_type'])=='MC'){
                                $show_delay_arr[3]="yes";
                            }
                        ?>
                            <input type="hidden" name="delay_1" id="delay_1" value="<?php echo $show_delay_arr[1] ?>">
                            <input type="hidden" name="delay_2" id="delay_2" value="<?php echo $show_delay_arr[2] ?>">
                            <input type="hidden" name="delay_3" id="delay_3" value="<?php echo $show_delay_arr[3] ?>">
                        <?php
                            $is_delayed = check_claim_old_days($getPostOrRePostRow['date_of_service']);
                            if($is_delayed>=90 && count($show_delay_arr)>0){
                        ?> 
                            <div id="claim_delay_code_td">
                                <label>Medicaid HIPAA - Delay Reason Code:&nbsp;</label>
                                <select name="claim_delay_code" id="claim_delay_code" style="width:290px;" class="minimal">
                                    <option value="0">Please Select</option>
                                    <option value="1">1 - Proof of Eligibility Unknown or Unavailable</option>
                                    <option value="2">2 - Litigation</option>
                                    <option value="3">3 - Authorization Delays</option>
                                    <option value="4">4 - Delay in Certifying Provider</option>
                                    <option value="5">5 - Delay in Supplying Billing Forms</option>
                                    <option value="6">6 - Delay in Supplying Custom-made Appliances</option>
                                    <option value="7">7 - Third Party Processing Delay</option>
                                    <option value="8">8 - Delay in Eligibility Determination</option>
                                    <option value="9">9 - Original claim rejected or Denied Due to a Reason Unrelated to the Billing Limitation Rules</option>
                                    <option value="10">10 - Administration Delay in the Prior Approval Process</option>
                                    <option value="11">11 - Other</option>
                                    <option value="15">15 - Natural Disaster</option>
                                </select>
                            </div>		
                        <?php } ?>	
                    </div>
                    <div class="col-sm-12 text-left pd10" id="valid_claim_td">
                        <label>
                            <?php 
                                if($Re_submitted == 'true'){ 
                                    echo "Do you want to re-submit charges of $proc_codes for clearing?"; 
                                }else{
                                    echo "Do you want to post the charges of $proc_codes for clearing?";
                                } 
                            ?>		
                        </label>					
                    </div>
                    <div class="col-sm-12">
                        <?php 
                            $pri_for_hcfa="";
                            $sec_for_hcfa="";
                            $tri_for_hcfa="";
                            if($insDetails[1]['Insurance_payment'] != 'Electronics' && $insDetails[1]['Insurance_payment'] != ''){
                                $pri_for_hcfa="yes";
                            }
                            if($insDetails[2]['secondary_payment_method'] != 'Electronics' && $insDetails[2]['secondary_payment_method'] != ''){
                                $sec_for_hcfa="yes";
                            }
                            if($insDetails[3]['secondary_payment_method'] != 'Electronics' && $insDetails[3]['secondary_payment_method'] != ''){
                                $tri_for_hcfa="yes";
                            }
                        ?>	
                        <input type="hidden" name="ins_paper_1" id="ins_paper_1" value="<?php echo $pri_for_hcfa;?>">
                        <input type="hidden" name="ins_paper_2" id="ins_paper_2" value="<?php echo $sec_for_hcfa;?>">
                        <input type="hidden" name="ins_paper_3" id="ins_paper_3" value="<?php echo $tri_for_hcfa;?>">
    
                        <div id="hcfa_ub_rad_1" class="hu_but_td">
                            <div class="col-xs-3">
                                <div class="radio radio-inline">
                                    <input  type="radio" name="print_hcfa_ub" id="PrintCms_old" value="PrintCms"  checked="checked"/>
                                    <label for="PrintCms_old">CMS 1500</label>	
                                </div>
                            </div>
                                
                            <div class="col-xs-4">
                                <div class="radio radio-inline">
                                    <input type="radio" name="print_hcfa_ub" id="PrintCms_white_old" value="PrintCms_white" />
                                    <label for="PrintCms_white_old">CMS 1500 - Red Form</label>	
                                </div>
                            </div>
    
                            <div class="col-xs-2">
                                <div class="radio radio-inline">
                                    <input  type="radio" name="print_hcfa_ub" id="printub_old" value="Printub" />
                                    <label for="printub_old">UB-04</label>	
                                </div>	
                            </div>	
                            
                            <div class="col-xs-3">
                                <div class="radio radio-inline">
                                    <input type="radio" name="print_hcfa_ub" id="WithoutPrintub_old" value="WithoutPrintub" />
                                    <label for="WithoutPrintub_old">UB-04 - Red Form</label>	
                                </div>	
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-xs-3">
                                <div class="radio radio-inline">
                                    <input  type="radio" name="post_bill_type" id="bill_type_831" value="831"  checked="checked"/>
                                    <label for="bill_type_831">831 &nbsp;</label>	
                                </div>		
                            </div>
                            
                            <div class="col-xs-3">
                                <div class="radio radio-inline">
                                    <input type="radio" name="post_bill_type" id="bill_type_837" value="837" />
                                    <label for="bill_type_837">837 &nbsp;</label>	
                                </div>		
                            </div>
                        </div>	
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-xs-10 col-xs-offset-1 text-center pt10" id="valid_claim_button_tr">
                        <div class="row" id="module_buttons">
                            <div class="col-xs-2">
                                <input type="button" onClick="return postCharges(<?php echo $encounter_id; ?>,'');" class="btn btn-success" id="post_charges" value="   Yes   ">
                            </div>
                            
                            <?php if($insDetails[1]['Insurance_payment'] != 'Electronics' && $insDetails[1]['Insurance_payment'] != ''){?>
                                    <div id="hcfa_ub_but_1" class="col-xs-8 hu_but_td">
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <input type="button"  onClick="return postCharges(<?php echo $encounter_id; ?>,'1');" class="btn btn-success" id="post_hcfa_charges"  value="Yes & Print">
                                            </div>
                                        </div>
                                    </div>
                                <?php
                            }?>
                        <?php 
                            if($insDetails[2]['secondary_payment_method'] != 'Electronics' && $insDetails[2]['secondary_payment_method'] != ''){
                                ?>
                                <div id="hcfa_ub_but_2" class="col-xs-8 hu_but_td">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <input type="button"  onClick="return postCharges(<?php echo $encounter_id; ?>,'2');" class="btn btn-success" id="post_hcfa_charges"  value="Yes & Print">
                                        </div>	
                                    </div>
                                </div>
                        <?php
                            }
                        ?>	
                         <?php 
                            if($insDetails[3]['secondary_payment_method'] != 'Electronics' && $insDetails[3]['secondary_payment_method'] != ''){
                                ?>
                                <div id="hcfa_ub_but_3" class="col-xs-8 hu_but_td">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <input type="button"  onClick="return postCharges(<?php echo $encounter_id; ?>,'3');" class="btn btn-success" id="post_hcfa_charges"  value="Yes & Print">
                                        </div>	
                                    </div>
                                </div>
                        <?php
                            }
                        ?>
                            <div class="col-xs-2"><input type="button" onClick="window.close();" class="btn btn-danger" id="cancel_print"  value="  Cancel  "></div>
                        </div>
                    </div>	
                </div>	
            </div>	
        </div>
    </div>
</form>
<script type="text/javascript">
	$(document).ready(function(){
		$('.hu_but_td').hide();
		if($('#ins_paper_1').val()!=""){
			$('#hcfa_ub_but_1,#hcfa_ub_rad_1').show();
		}
		$('.post_for_cls').click(function(){
			v = $(this).val();
			$('.hu_but_td').hide();
			if($('#ins_paper_'+v).val()!=""){
				$('#hcfa_ub_but_'+v).show();
				$('#hcfa_ub_rad_1').show();
			}else{
				$('.hu_but_td').hide();
			}
			ajax_post_fun('invalid_claim',v);
		});
		var ins_accept_assign = '<?php echo $insDetails[1]['ins_accept_assignment'];?>';
		if(ins_accept_assign==2){
			$('#post_charges').trigger('click');
		}
	});
	function postCharges(eId,print_for){
		if($('#claim_delay_code').length>0){
			if($('#claim_delay_code_td').is(':visible')){
				if($('#claim_delay_code').val()==0){
					alert("Please Select Medicaid HIPAA - Delay Reason Code");
					return false;
				}
			}
		}
		if(print_for>0){
			$('#status').val('1');
		}
		document.postChargesFrm.submit();
	}
	
	function ajax_post_fun(action_type,cont){
		if($('#ins_paper_'+cont).val()!=""){
			var type =  'hcfa';
		}else{
			var type =  'elect';
		}
		var type =  'primary';
		if(cont>2){
			var type =  'tertiary';
		}else if(cont>1){
			var type =  'secondary';
		}
		
		var charge_list_id = $('#charge_list_id_send').val();
		if(action_type!=""){
			var url="acc_ajax.php?action_type="+action_type+"&charge_list_id="+charge_list_id+"&type="+type;
			$.ajax({
					type: "POST",
					url: url,
					success: function(resp){
						resp = jQuery.parseJSON(resp);
						var invalid_claim_reason = resp.invalid_claim_reason;
						if(invalid_claim_reason!=""){
							$('#show_reason_td').html(invalid_claim_reason);
							$('#invalid_claim_td').show();
							$('#valid_claim_td').hide();
							$('#valid_claim_button_tr').hide();
							$('#hcfa_ub_rad_1').hide();
							$('#claim_delay_code_td').hide();
						}else{
							$('#show_reason_td').html('');
							$('#invalid_claim_td').hide();
							$('#valid_claim_td').show();
							$('#valid_claim_button_tr').show();
							var chk_delay_td="delay_"+cont;
							if($('#'+chk_delay_td).val()!=""){
								$('#claim_delay_code_td').show();
							}else{
								$('#claim_delay_code_td').hide();
							}
							
						}
					}
			});
		}
	}
	ajax_post_fun('invalid_claim','1');
	var reff_number_val = $("#reff_number_val");
	var main_reff = opener.$("#referral");
	if(main_reff.val() != ''){
		reff_number_val.val(main_reff.val());
	}
</script>
<?php } if($ar_ajax==""){?>
</html>
<?php }?>