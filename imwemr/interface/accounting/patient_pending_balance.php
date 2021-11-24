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
include('acc_header.php');
$operatorNameSess = $_SESSION['authUser'];
function mysqlifetchData($qry){
	$sql_qry = imw_query($qry);
	while($row = imw_fetch_array($sql_qry)){
		$return_arr[] = $row;
	}
	return $return_arr;
}

function getRowRecord($table, $conditionId, $value, $orderBy=0){
	if($orderBy){
		$qryStr = "SELECT * FROM $table WHERE $conditionId = '$value' ORDER BY $orderBy DESC";
	}else{
		$qryStr = "SELECT * FROM $table WHERE $conditionId = '$value'";
	}		
	$qryQry = imw_query($qryStr);
	if($qryQry){
		$qryRow = imw_fetch_object($qryQry);
		return $qryRow;
	}
}

$patient_id = $_REQUEST['patient_id'];
if($patient_id==""){
$patient_id = $_SESSION['patient'];
}
$today = getdate();
$today_year = $today['year'];
$today_mon = $today['mon'];
$today_day = $today['mday'];
if($today_day<=9){
	$today_day='0'.$today_day;
}
if($today_mon<=9){
	$today_mon='0'.$today_mon;
}
$todate = $today_mon."-".$today_day."-".$today_year;
$delDate = date('Y-m-d');

// DELETE PAYMET MADE
$payIdDel = $_REQUEST['payIdDel'];
$payDetailId = $_REQUEST['payDetailId'];
$paidAmtToManage = $_REQUEST['payAmt'];
// DELETE DENIAL RECORD.
$refresh = false;
$refresh2 = false;

$getRefChkStr = "Select refraction FROM copay_policies WHERE policies_id = '1'";
$getRefChkQry = @imw_query($getRefChkStr);
$getRefChkRow = @imw_fetch_assoc($getRefChkQry);
$refractionChk = $getRefChkRow['refraction'];
$b_id=$_REQUEST['b_id'];
?>
<script type="text/javascript">
var today = new Date();
var day = today.getDate();
var month = today.getMonth()
var year = y2k(today.getYear());
var mon = month+1;
if(mon<=9){
	mon='0'+mon;
}
if(day<=9){
	day='0'+day;
}
var todaydate=mon+'-'+day+'-'+year;
function y2k(number){
	return (number < 1000)? number+1900 : number;
}

function chechNaN(str, obj, appDedAmt){
	if(isNaN(str)){
		alert("Please enter only numeric values")
		var appDedAmt = (appDedAmt.toFixed(2));
		document.getElementById(obj).value = appDedAmt;
		document.getElementById(obj).focus();
		return false;
	}
}
function closeMe(){
	top.document.makePaymentFrm.location.reload();
}


function EditDenial(id, eId){
	window.open("editDenial.php?DenialId="+id+'&eId='+eId,'paymentDenial','width=925,height=300,top=35,left=25,scrollbars=yes,resizable=yes');
}
function delDenial(id, eId){
	var ask = confirm("Delete selected transaction !");
	if(ask==true){
		window.location.href='patientPendingDosDetails.php?encounter_id='+eId+'&delDenialId='+id;
	}
}
function delWriteOff(id, eId, cDetailsId){
	var ask = confirm("Delete selected transaction !");
	if(ask==true){
		window.location.href='patientPendingDosDetails.php?encounter_id='+eId+'&delWriteOffId='+id+'&cDetailsId='+cDetailsId;
	}
}
function delCoPayWOFn(eId, idWO){
	var ask = confirm("Delete selected transaction !");
	if(ask==true){
		window.location.href='patientPendingDosDetails.php?encounter_id='+eId+'&delCoPayWO='+true+'&idWO='+idWO;
	}
}
function delCoPayPaymentFn(eId, pDId, pId ,copayamt){
	var ask = confirm("Delete selected transaction !");
	if(ask==true){
		window.location.href='patientPendingDosDetails.php?encounter_id='+eId+'&delCoPayPayment='+true+'&pDId='+pDId+'&pId='+pId+'&dcopayamt='+copayamt;
	}
}
function editPaymentFn(dpayId, payId, eId){
	window.open("editPayments.php?payId="+payId+'&eId='+eId,'editPayment','width=1025,height=600,top=35,left=0,scrollbars=yes,resizable=yes');	
}
function savePaymentFn(payId, dpayId){
	var payAmt = document.paymentFrm.paidAmt.value;	
	document.paymentEditFrm.detailPaymentId.value = dpayId;
	document.paymentEditFrm.paymentId.value = payId;
	document.paymentEditFrm.paymentAmt.value = payAmt;
	document.paymentEditFrm.submit();
}
function changePayAmt(s, c, cPaid, over){	
	var pay = document.getElementById('paidAmtText'+s).value;
	var payNow = document.getElementById('payNew'+s).value;
	if(isNaN(payNow)){
		document.getElementById('payNew'+s).value = '0.00';
		return false;
	}
	document.getElementById('paidAmtText'+s).value = payNow;
	checkChkBox(s)
	makePaymentFor(pay, s, c, cPaid, over)
	getTotalPayAmtPend()
}
function delDeductible(id, eId,deductamt){
	var ask = confirm('Delete selected transaction !')
	if(ask==true){
		window.location.href='patientPendingDosDetails.php?encounter_id='+eId+'&delDeductible='+id;
	}
}
function editDeductible(id, eId){
	window.open("editDeductible.php?DeductibleId="+id+'&eId='+eId,'deductPayment','width=925,height=300,top=35,left=25,scrollbars=yes,resizable=yes');
}
/////////////////////////////////////////	NEW JAVA SCRIPT PAYMENT FUNCTIONS	///////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


function overpaid_chk(){
	var tot_print_arr = document.getElementsByName('chkbx[]').length;
	if(top.document.makePaymentFrm.ovr_paid_sum){
		var ovr_paid_chk=top.document.makePaymentFrm.ovr_paid_sum.value;
	}else{
		var ovr_paid_chk=top.document.makePaymentFrm.ovr_paid.value;
	}
	
	if(ovr_paid_chk>0){
		var tot_pay_new=0.00;
		for(g=1;g<=tot_print_arr;g++){
			if(document.getElementById('chkbx'+g).checked==true){
				var bal_ch=document.getElementById('bal_chk_for_copay'+g).value;
				var paynew_amt=document.getElementById('payNew'+g).value;
				tot_pay_new += parseFloat(paynew_amt);
				//alert(tot_pay_new);
			}
			if(parseFloat(tot_pay_new)>parseFloat(ovr_paid_chk)){
				var temp=parseFloat(tot_pay_new)-parseFloat(paynew_amt);
				var last_amt=parseFloat(ovr_paid_chk)-parseFloat(temp);
				alert("Credit amount can not be greater than debit amount");
				document.getElementById('payNew'+g).value=Math.round(last_amt*100)/100;
				top.document.makePaymentFrm.paidAmountNow.value=Math.round(ovr_paid_chk*100)/100;
				return false;
			}else if(parseFloat(paynew_amt)>parseFloat(bal_ch)){
				document.getElementById('payNew'+g).focus();
				alert("Credit amount can not be greater than new balance");
				return false;
			}else{
				top.document.makePaymentFrm.paidAmountNow.value=Math.round(tot_pay_new*100)/100;
			}
		}
	}else{
			top.fAlert("Credit amount can not be greater than debit amount");
			return false;
	}
	//alert(tot_pay_new);
}

// START CHECK PAYMENT MADE OR NOT
var tot= "";

// END CHECK PAYMENT MADE OR NOT

function getTotalPayAmtPend(){
	var copay = 0;
	var TotalPayAmount = 0;	
	var frmEle = document.paymentFrm.elements.length;
	var frmObj = document.paymentFrm;
	for(i=0;i<frmEle;i++){
		var eleName = frmObj.elements[i].name;
		if(eleName.indexOf('payNew') != -1){
			var amtToPay = frmObj.elements[i].value;
			if((amtToPay=='-0.00') || (amtToPay<0)){
				frmObj.elements[i].value = '0.00';
			}
			var TotalPayAmount = TotalPayAmount + parseFloat(amtToPay);
		}
	}
	
	// IF COPAY PAYMENT IS CHECKED
	if(document.getElementById('coPayChk')){
		var CopayChk = document.getElementById('coPayChk').checked;
		if(CopayChk==true){
			<?php if($copay){ ?>
			var copay = <?php echo $copay; ?>;
			<?php } ?>
			//TotalPayAmount = TotalPayAmount+copay;
			TotalPayAmount = TotalPayAmount;
		}
	}
	//SETTING AMOUNT FOR OUTER 
	var eleLen = top.document.makePaymentFrm.elements.length;
	for(var k=0; k<eleLen; k++){
		var eleName = top.document.makePaymentFrm.elements[k].name;
		if(eleName=='paidAmountNow'){
			top.document.makePaymentFrm.elements[k].value = TotalPayAmount.toFixed(2);
		}
	}
	// SETTING AMOUNT FOR OUTER 
}
function selectChanges(s){
	if(refractionChk(s)){
		$('#chkbx'+s).prop('checked',true);
	}
	overpaid_chk();
}
/*
function editWriteOff(wOffId, eId, cLDId){
	window.open("editWriteOff.php?wOffId="+wOffId+'&eId='+eId+'&cLDId='+cLDId,'editWriteOff','width=925,height=350,top=75,left=35,scrollbars=yes,resizable=yes');	
} 
*/
function refractionChk(s){
	var cptId = $.trim($('#cptIdTd'+s).html());
	var refractionChk = '<?php echo $refractionChk; ?>';
	if(cptId == 92015 && refractionChk == 'No'){
		alert("Refraction can not be collected.");
		$("#chkbx"+s).prop('checked',false);
		return false;
	}else{
		return true;
	}
}


/////////////////////////////////////////	NEW JAVA SCRIPT PAYMENT FUNCTIONS	///////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
</script>
<body >
<div class="row">
	<div class="col-sm-12">
<?php
$encounter_id="";
if($encounter_id==""){
	$seq = 0;
	$getChargeDertailsStr = "SELECT charge_list_id,procCode,patient_id,charge_list_detail_id
							 FROM patient_charge_list_details 
							WHERE del_status='0' and charge_list_detail_id in($chld_id)";					
	$getChargeDertailsQry = imw_query($getChargeDertailsStr);
	$getChargeDertailsRow = imw_fetch_array($getChargeDertailsQry);
	
	$app_deb_chl = $getChargeDertailsRow['charge_list_id'];
	$app_deb_procCode = $getChargeDertailsRow['procCode'];
	$app_deb_pat = $getChargeDertailsRow['patient_id'];
	$app_deb_chld = $chld_id;
	
	$getChargeDertailsStr_all = "SELECT encounter_id FROM patient_charge_list 
							WHERE del_status='0' and charge_list_id='$app_deb_chl'";
	$getChargeDertailsQry_all = imw_query($getChargeDertailsStr_all);
	$getChargeDertailsRow_all = imw_fetch_array($getChargeDertailsQry_all);
	
	$app_deb_enc = $getChargeDertailsRow_all['encounter_id'];
?>
	<form name="paymentFrm" action="all_payments.php" method="post" onSubmit="return check();">
	<input type="hidden" name="copay" id="copay" value="<?php echo $copay; ?>">
	<input type="hidden" name="apply" id="apply">
	<input type="hidden"  name="pro_arr" id="pro_arr">
	<input type="hidden" value="<?php echo $app_deb_pat; ?>" name="app_deb_pat" id="app_deb_pat">
	<input type="hidden" value="<?php echo $app_deb_enc; ?>" name="app_deb_enc" id="app_deb_enc">
	<input type="hidden" value="<?php echo $app_deb_chld; ?>" name="app_deb_chld" id="app_deb_chld">
	<input type="hidden" value="<?php echo $app_deb_chl; ?>" name="app_deb_chl" id="app_deb_chl">
	<input type="hidden" value="<?php echo $app_deb_procCode; ?>" name="app_deb_procCode" id="app_deb_procCode">
    <input type="hidden" value="<?php echo $ovr_paid; ?>" name="app_deb_ovr_paid" id="app_deb_ovr_paid">
	<input type="hidden" value="other_procedure" name="adjust_other_txt" id="adjust_other_txt">
	<input type="hidden" name="paymentClaims" value="Debit_Credit" id="Debit_Credit">
    <input type="hidden" name="b_id" value="<?php echo $b_id; ?>" id="b_id">
	<div class="row">
		<?php
		$getEncounterDetailsStr = "SELECT * FROM patient_charge_list
									WHERE del_status='0' and patient_id='$patient_id' and (totalBalance>0) order by totalBalance desc";
		$getEncounterDetailsQry = @imw_query($getEncounterDetailsStr);
		$num_bal=imw_num_rows($getEncounterDetailsQry);
		if($num_bal > 0){
		while($getEncounterDetailsRow = @imw_fetch_array($getEncounterDetailsQry)){
		$encounter_id=$getEncounterDetailsRow['encounter_id'];
		?>
			<div class="col-sm-12 pt10">
				<input type="hidden" name="encounter_id_arr[]" value="<?php echo $encounter_id; ?>">	
				<?php
					$date_of_service = $getEncounterDetailsRow['date_of_service'];
					list($year, $month, $day) = explode("-", $date_of_service);
					//$date_of_service = $month."-".$day."-".$year;
					$date_of_service = date('m-d-y',mktime(0,0,0,$month,$day,$year));
					$case_type_id = $getEncounterDetailsRow['case_type_id'];
					if($case_type_id=='0') $case_type_id = 'Self Pay';
					$copay_chh = $getEncounterDetailsRow['copay'];
					$primaryInsuranceCoId = $getEncounterDetailsRow['primaryInsuranceCoId'];
					$getInsCo1DetailsStr = "SELECT * FROM insurance_companies WHERE id='$primaryInsuranceCoId'";
					$getInsCo1DetailsQry = @imw_query($getInsCo1DetailsStr);
					$getInsCo1DetailsRow = @imw_fetch_array($getInsCo1DetailsQry);
					$insCo1NameCode = $getInsCo1DetailsRow['in_house_code'];
					if(!$insCo1NameCode){
						$insCo1Name = $getInsCo1DetailsRow['name'];
						$insCo1NameLen = strlen($insCo1Name);
						if($insCo1NameLen>13){
							$insCo1NameCode = substr($insCo1Name, 0, 13)."..";
						}else{
							$insCo1NameCode=$insCo1Name;
						}
					}	
					$secondaryInsuranceCoId = $getEncounterDetailsRow['secondaryInsuranceCoId'];
					$getInsCo2DetailsStr = "SELECT * FROM insurance_companies WHERE id='$secondaryInsuranceCoId'";
					$getInsCo2DetailsQry = @imw_query($getInsCo2DetailsStr);
					$getInsCo2DetailsRow = @imw_fetch_array($getInsCo2DetailsQry);
					$insCo2NameCode = $getInsCo2DetailsRow['in_house_code'];
					if(!$insCo2NameCode){
						$insCo2Name = $getInsCo2DetailsRow['name'];
						$insCo2NameLen = strlen($insCo2Name);
						if($insCo2NameLen>13){
							$insCo2NameCode = substr($insCo2Name, 0, 13)."..";
						}else{
							$insCo2NameCode = $insCo2Name;
						}
					}	
					$tertiaryInsuranceCoId = $getEncounterDetailsRow['tertiaryInsuranceCoId'];
					$getInsCo3DetailsStr = "SELECT * FROM insurance_companies WHERE id='$tertiaryInsuranceCoId'";
					$getInsCo3DetailsQry = @imw_query($getInsCo3DetailsStr);
					$getInsCo3DetailsRow = @imw_fetch_array($getInsCo3DetailsQry);
					$insCo3NameCode = $getInsCo3DetailsRow['in_house_code'];
					if(!$insCo3NameCode){
						$insCo3Name = $getInsCo3DetailsRow['name'];
						$insCo3NameLen = strlen($insCo3Name);
						if($insCo3NameLen>13){
							$insCo3NameCode = substr($insCo3Name, 0, 13)."..";
						}else{
							$insCo3NameCode=$insCo3Name;
						}
					}	
					$insCoArray[] = $insCo1NameCode;
					$insCoArray[] = $insCo2NameCode;
					$insCoArray[] = $insCo3NameCode;
					?>
				<table class="table table-bordered table-condensed"><!--FFF3E8-->
					<tr class='grythead'>
						<th class="text-center">DOS</th>
						<th class="text-center">EId</th>
						<th class="text-center">Ins. Case</th>
						<th class="text-center">CoPay</th>
						<th class="text-center">Ins. Provider Primary</th>
						<th class="text-center">Ins. Provider Secondary</th>
						<th class="text-center">Ins. Provider Tertiary</th>
						<th class="text-center">Operator</th>
					</tr>
					<tr>
						<th><?php echo $date_of_service; ?></th>
						<th><?php echo $encounter_id; ?></th>
						<th><?php echo $case_type_id; ?></th>
						<th><?php echo number_format($copay_chh, 2); ?></th>
						<th><?php echo $insCo1NameCode; ?></th>
						<th><?php if($insCo2NameCode) echo $insCo2NameCode; else echo '-'; ?></th>
						<th><?php if($insCo3NameCode) echo $insCo3NameCode; else echo '-'; ?></th>
						<th><?php echo $operatorNameSess; ?></th>
					</tr>
				</table>
			</div>
			<div class="col-sm-12 pt10" style="overflow-y:scroll">
				<table class="table table-bordered table-condensed"><!--FFF3E8--><!--D8DDFE-->
					<tr class="grythead">
					  	<th>Apply</th>
						<th>Procedure</th>
						<th>Description</th>
						<th class="text-nowrap">Total Charges </th>										
						<th style="display:none;">Balance</th>					
						<th>Allowed</th>
						<th>Deductible</th>
						<th>Write Off</th>
						<th>Credit</th>					
						<th>Paid</th>
						<th>Amount</th>
						<th class="text-nowrap">New Balance</th>
						<th>Over Paid </th>
						<th>DOT </th>
						<th>Method</th>
						<th class="text-nowrap">CC / Ch.# </th>					
						<th class="text-nowrap">CC Exp. Date</th>
						<th class="text-nowrap">Submitted Date</th>
						<th>Operator</th>
					</tr>
					<?php
					$getChargesDetailsStr = "SELECT * FROM patient_charge_list
											WHERE del_status='0' and encounter_id='$encounter_id'";
					$getChargesDetailsQry = imw_query($getChargesDetailsStr);
					$cpt_onetime=0;
					$copayPaid=0;
					$min_copay_one=0;
					while($getChargesDetailsRow = imw_fetch_array($getChargesDetailsQry)){
						$chargeListId = $getChargesDetailsRow['charge_list_id'];
						$copay = $getChargesDetailsRow['copay'];
						$coPay = $getChargesDetailsRow['copay'];
						$copayPaid = $getChargesDetailsRow['copayPaid'];
						$referactionPaid = $getChargesDetailsRow['referactionPaid'];
						$coPayNotRequired = $getChargesDetailsRow['coPayNotRequired'];
						$coPayWriteOff = $getChargesDetailsRow['coPayWriteOff'];
						
						$amountDue = $getChargesDetailsRow['amountDue'];
						$totalPaidAmt = $getChargesDetailsRow['amtPaid'];
						$totalAmt = $getChargesDetailsRow['totalAmt'];
						$approvedTotalAmt = $getChargesDetailsRow['approvedTotalAmt'];
						$deductibleTotalAmt = $getChargesDetailsRow['deductibleTotalAmt'];
						$totalEncounterBalance = number_format($getChargesDetailsRow['totalBalance'], 2);
						$coPayAdjusted = $getChargesDetailsRow['coPayAdjusted'];
						$creditAmountBalance = $getChargesDetailsRow['creditAmount'];
						$overPayment = $getChargesDetailsRow['overPayment'];
						$operator_id = $getChargesDetailsRow['operator_id'];
							//GET OPERATOR INITIALS
							$operatorDetails = getRowRecord('users', 'id', $operator_id);
							$operatorNamePaid = substr($operatorDetails->fname,0,1).substr($operatorDetails->lname,0,1);

						$encCommentsInt = $getChargesDetailsRow['encCommentsInt'];
						$encCommentsExt = $getChargesDetailsRow['encCommentsExt'];
						$encCommentsIntDate = $getChargesDetailsRow['encCommentsIntDate'];
							list($yearComment, $monthComment, $dayComment) = explode("-", $encCommentsIntDate);
							$encCommentsIntDate = $monthComment."-".$dayComment."-".$yearComment;
							
						$encCommentsExtDate = $getChargesDetailsRow['encCommentsExtDate'];
							list($yearComment, $monthComment, $dayComment) = explode("-", $encCommentsExtDate);
							$encCommentsExtDate = $monthComment."-".$dayComment."-".$yearComment;
						$encCommentsIntOperatorId = $getChargesDetailsRow['encCommentsIntOperatorId'];

							//--------------------	GETTING OPERATOR NAME --------------------//
							$getCommentsIntOperatorNameStr = "SELECT * FROM users WHERE id = '$encCommentsIntOperatorId'";
							$getCommentsIntOperatorNameQry = imw_query($getCommentsIntOperatorNameStr);
							$getCommentsIntOperatorNameRow = imw_fetch_array($getCommentsIntOperatorNameQry);
							$commentsIntOperatorName = $getCommentsIntOperatorNameRow['lname'].', '.$getCommentsIntOperatorNameRow['fname'];
							//--------------------	GETTING OPERATOR NAME --------------------//
						
						$encCommentsExtOperatorId = $getChargesDetailsRow['encCommentsExtOperatorId'];
							//--------------------	GETTING OPERATOR NAME --------------------//
							$getCommentsExtOperatorNameStr = "SELECT * FROM users WHERE id = '$encCommentsExtOperatorId'";
							$getCommentsExtOperatorNameQry = imw_query($getCommentsExtOperatorNameStr);
							$getCommentsExtOperatorNameRow = imw_fetch_array($getCommentsExtOperatorNameQry);
							$commentsExtOperatorName = $getCommentsExtOperatorNameRow['lname'].", ".$getCommentsExtOperatorNameRow['fname'];
							//--------------------	GETTING OPERATOR NAME --------------------//
						
						//$postedDate = $getChargesDetailsRow['postedDate'];
							$postedDate = $getChargesDetailsRow['firstSubmitDate'];
								list($year, $month, $day)=explode("-", $postedDate);
								$postedDate=$month."-".$day."-".$year;
							
						$totalBalance = $totalAmt - $totalPaidAmt;
						$deductAmt = false;
						$whoPaidAmt = '';
						//-------------------- ASC ORDER BY CPT DESC. --------------------//

						$totalRefractionAmountFor = 0;
						$reflactionAmt = 0;
						$amountToPay = 0;
						$total_write_off_amount = 0;
						if(($copay>0) && ($copayPaid<=0) && ($coPayNotRequired!=1) && ($coPayWriteOff!='1')){
							$amountToPay = $amountToPay + $copay;
						}
						$getproccode1 = "SELECT sum(paidForProc) as tot_paidproc FROM 
											patient_chargesheet_payment_info a,
											patient_charges_detail_payment_info b
											WHERE a.encounter_id = '$encounter_id'
											AND a.payment_id = b.payment_id
											AND b.charge_list_detail_id = 0
											AND b.deletePayment=0
											ORDER BY a.payment_id DESC";
							$getproccodeQry1 = imw_query($getproccode1);
							$getproccodeRow1 = imw_fetch_array($getproccodeQry1);
							$paidForProc_chk1 = $getproccodeRow1['tot_paidproc'];
							$tot_paid_chk1=$paidForProc_chk1;
						$show_row="";
						$proc_code_imp = get_proc_code($chargeListId);
						$copay_collect_proc = copay_apply_chk($proc_code_imp,'','');
						$getProcDetailsStr = "SELECT a.* FROM
												patient_charge_list_details a,
												cpt_fee_tbl b
												WHERE 
												a.del_status='0'
												and a.charge_list_id='$chargeListId'
												AND a.procCode=b.cpt_fee_id
												and (a.newBalance>0)
												ORDER BY a.display_order asc,a.charge_list_detail_id  ASC";
						//-------------------- ASC ORDER BY CPT DESC. --------------------//
						$getProcDetailsQry = imw_query($getProcDetailsStr);
						$getProcCountRows = imw_num_rows($getProcDetailsQry);
						$getProcCountRows_all+=$getProcCountRows;
						while($getProcDetailsRows = imw_fetch_array($getProcDetailsQry)){
							$charge_list_detail_id = $getProcDetailsRows['charge_list_detail_id'];
							$procIdForCredit = $getProcDetailsRows['procCode'];							
							//----------------------- GETTING PAYMENT DETAILS -----------------------//
							
							$getPaymentDetailEncStr = "SELECT * FROM patient_charges_detail_payment_info 
														WHERE charge_list_detail_id = '$charge_list_detail_id'";							
							$getPaymentDetailEncQry = imw_query($getPaymentDetailEncStr);
							$getPaymentRows = imw_num_rows($getPaymentDetailEncQry);
							$getPaymentDetailEncRow = imw_fetch_array($getPaymentDetailEncQry);
							$paymentIdNew = $getPaymentDetailEncRow['payment_id'];							
							$deletePayment = $getPaymentDetailEncRow['deletePayment'];						
							$paidByNew = $getPaymentDetailEncRow['paidBy'];
								//---------------- PAYMENT MADE BY --------------//
								$getPaymentDetailsStr = "SELECT * FROM 
														patient_chargesheet_payment_info a,
														patient_charges_detail_payment_info b
														WHERE a.payment_id = b.payment_id
														AND b.charge_list_detail_id = '$charge_list_detail_id'";
								$getPaymentDetailsQry = imw_query($getPaymentDetailsStr);
								$getRowsCount = imw_num_rows($getPaymentDetailsQry);
								if($getRowsCount>0){
									while($getPaymentDetailsRows = imw_fetch_array($getPaymentDetailsQry)){
										$payment_id = $getPaymentDetailsRows['payment_id'];
										if($deletePayment!=1){
											$paidBy = $getPaymentDetailsRows['paidBy'];
											if($paidBy != $paidByNew){
												$paidByNew = 'Multi';
											}
										}
									}
								}							
								//---------------- PAYMENT MADE BY --------------//
							$paidDateNew = $getPaymentDetailEncRow['paidDate'];
							if($paidDateNew!=''){
								list($year, $month, $day)=explode("-", $paidDateNew);
								$paidDateNew = $month."-".$day."-".$year;
							}else{
								$paidDateNew = '-';
							}
							$writeOffAmount = 0;
							//----------------------- GETTING PAYMENT DETAILS -----------------------//
							$balForProc = $getProcDetailsRows['balForProc'];
							$procNewBalance = $getProcDetailsRows['newBalance'];
							$approvedAmt = $getProcDetailsRows['approvedAmt'];
							$deductAmt = $getProcDetailsRows['deductAmt'];
							
							$getPaymentExistsChkStr = "SELECT * FROM patient_chargesheet_payment_info
														WHERE encounter_id='$encounter_id'
														AND payment_id ='$paymentIdNew'";
							$getPaymentExistsChkQry = @imw_query($getPaymentExistsChkStr);
							$getPaymentExistsChkRow = @imw_fetch_array($getPaymentExistsChkQry);
							$paid_by = $getPaymentExistsChkRow['paid_by'];
							$paidDate = $getPaymentExistsChkRow['date_of_payment'];
								list($year, $month, $day)=explode("-", $paidDate);
								$paidDate=$month."-".$day."-".$year;
							if($paidDate=="--"){
								$paidDate="-";
							}
							$payment_mode = $getPaymentExistsChkRow['payment_mode'];
							if($payment_mode==""){
								$payment_mode="-";
							}
							if($payment_mode=='Check'){
								$checkCCNo = $getPaymentExistsChkRow['checkNo'];
							}else{
								$checkCCNo = "-";
							}
							if($payment_mode=='Credit Card'){
								$checkCCNo = $getPaymentExistsChkRow['creditCardNo'];
							}
							if($payment_mode=='Cash'){
								$checkCCNo='-';
							}
							$expirationDate = $getPaymentExistsChkRow['expirationDate'];
							$operatorId = $getPaymentExistsChkRow['operatorId'];
							//--------------------	GETTING OPERATOR NAME --------------------//
								$getOperatorNameStr = "SELECT * FROM users WHERE id = '$operatorId'";
								$getOperatorNameQry = @imw_query($getOperatorNameStr);
								$getOperatorNameRow = @imw_fetch_array($getOperatorNameQry);
							//--------------------	GETTING OPERATOR NAME --------------------//
							$claimDenied = $getProcDetailsRows['claimDenied'];
							$paidStatus = $getProcDetailsRows['paidStatus'];
							$procId = $getProcDetailsRows['procCode'];
							$units = $getProcDetailsRows['units'];
							$procCharges = $getProcDetailsRows['procCharges'];
							$totalAmount = $getProcDetailsRows['totalAmount'];
							$paidForProc = $getProcDetailsRows['paidForProc'];
							$balForProc = $getProcDetailsRows['balForProc'];
							$write_off_Proc = $getProcDetailsRows['write_off'];
								$writeOffId = $getProcDetailsRows['charge_list_detail_id'];
							if($write_off_Proc){
								//$writeOffAmount = $write_off_Proc;
							}else{
								//$writeOffAmount = 0;
							}							
							$getWriteOffAmtStr = "SELECT * FROM paymentswriteoff
												WHERE patient_id = '$patient_id'
												AND encounter_id = '$encounter_id'
												AND charge_list_detail_id = '$charge_list_detail_id'
												AND delStatus = 0";
							$getWriteOffAmtQry = imw_query($getWriteOffAmtStr);
							while($getWriteOffAmtRow = imw_fetch_array($getWriteOffAmtQry)){
								$writeOffAmount = $writeOffAmount+$getWriteOffAmtRow['write_off_amount'];
							}					
							
							$NewBalance = $getProcDetailsRows['newBalance'];
							$coPayAdjustedAmount =  $getProcDetailsRows['coPayAdjustedAmount'];
							$creditProcAmount =  $getProcDetailsRows['creditProcAmount'];
							$overPaymentForProc = $getProcDetailsRows['overPaymentForProc'];
							$getCptFeeDetailsStr = "SELECT * FROM cpt_fee_tbl WHERE cpt_fee_id = '$procId' AND delete_status = '0'";
							$getCptFeeDetailsQry = imw_query($getCptFeeDetailsStr);
							$getCptFeeDetailsRow = imw_fetch_array($getCptFeeDetailsQry);
								$cptPracCode = $getCptFeeDetailsRow['cpt_prac_code'];
								$cpt4_code = $getCptFeeDetailsRow['cpt4_code'];
								$cptDesc = $getCptFeeDetailsRow['cpt_desc'];

							if(($cptPracCode=='92015') || ($cpt4_code=='92015')){
								$totalRefractionAmountFor = $totalRefractionAmountFor + $NewBalance;
								$reflactionAmt = $NewBalance;
								$refractionExists = true;
								if($paidStatus == 'Paid'){
									$paid_by = 'Patient';
								}
							}else{
								$refractionExists = false;
							}
							
							if(($reflactionAmt>0) && ($referactionPaid<=0)){
								$amountToPay = $amountToPay + $reflactionAmt;
							}
							
							$writeOff = $totalAmount - $approvedAmt;

							if($cptPracCode=='92015'){ 
								//$writeOff = '0.00';
							}					
							
							++$seq;

							$totalBalanceNewDue = $approvedTotalAmt - $creditAmount - $totalPaidAmt - $deductibleTotalAmt;
							if($copay){
								if(($copayPaid!=1) && ($coPayNotRequired != 0) && ($coPayWriteOff!='1')){
									$amount = $amount - $copay;
							}
						}
						$approvedAmt = number_format($approvedAmt, 2);
						$deductAmt = number_format($deductAmt, 2);
						$paidForProc = number_format($paidForProc, 2);
						$creditProcAmount = number_format($creditProcAmount, 2);
						$totalAmount = number_format($totalAmount, 2);
						$amount = number_format($amount, 2);
						$copay_collect=copay_apply_chk($cpt4_code,'','');
						
						if($_REQUEST['b_id']>0){
							$batch_crd_amt_adust=0;
							$gettot_crd3 = "SELECT sum(amountApplied) as amt_adust  FROM manual_batch_creditapplied WHERE charge_list_detail_id_adjust  = '$charge_list_detail_id'  and delete_credit='0' and credit_applied='1' and post_status='0'";
							$gettot_crdQry3 = imw_query($gettot_crd3);
							$gettot_crdrow3 = imw_fetch_array($gettot_crdQry3);
							$batch_crd_amt_adust = $gettot_crdrow3['amt_adust'];
							
							$batch_deb_amt_adust=0;
							$gettot_crd4 = "SELECT sum(amountApplied) as amt_adust  FROM manual_batch_creditapplied WHERE charge_list_detail_id  = '$charge_list_detail_id'  and delete_credit='0' and credit_applied='1' and post_status='0'";
							$gettot_crdQry4 = imw_query($gettot_crd4);
							$gettot_crdrow4 = imw_fetch_array($gettot_crdQry4);
							$batch_deb_amt_adust = $gettot_crdrow4['amt_adust'];
							
							$trans_amt_total="";
							$sel_tran_amt="select sum(trans_amt) as trans_amt_total 
											from manual_batch_transactions
											where
											charge_list_detaill_id='$charge_list_detail_id'
											and (payment_claims='Paid' || payment_claims='Deposit' 
												|| payment_claims='Interest Payment')
											and post_status!=1
											and del_status=0
											";
							$trans_paid_amt=mysqlifetchData($sel_tran_amt);
							$trans_amt_total=$batch_crd_amt_adust+$trans_paid_amt[0]['trans_amt_total'];
							
						
							
							$trans_deduct_amt_total="";
							$sel_tran_amt_deduct="select sum(trans_amt) as trans_amt_total 
											from manual_batch_transactions
											where
											charge_list_detaill_id='$charge_list_detail_id'
											and (payment_claims='Deductible')
											and post_status!=1
											and del_status=0
											";
							$trans_deduct_amt = mysqlifetchData($sel_tran_amt_deduct);
							$trans_deduct_amt_total=$trans_deduct_amt[0]['trans_amt_total'];
							
							$trans_write_amt_total="";
							$sel_tran_amt_write="select sum(trans_amt) as trans_amt_total 
											from manual_batch_transactions
											where
											charge_list_detaill_id='$charge_list_detail_id'
											and (payment_claims='Write Off' || payment_claims='Discount')
											and post_status!=1
											and del_status=0
											";
							$trans_write_amt = mysqlifetchData($sel_tran_amt_write);
							$trans_write_amt_total=$trans_write_amt[0]['trans_amt_total'];
							
							$writeOffAmount=$writeOffAmount+$trans_write_amt_total;
							
							$trans_allow_write_amt_total="";
							$trans_allow_amt_total="";
							$write_off_code_trans_id="";
							$sel_tran_amt_allow="select trans_amt,proc_allow_amt,write_off_code_id 
											from manual_batch_transactions
											where
											charge_list_detaill_id='$charge_list_detail_id'
											and (payment_claims='Allowed')
											and post_status!=1
											and del_status=0
											";
							$trans_allow_amt=mysqlifetchData($sel_tran_amt_allow);
							$trans_allow_amt_total=$trans_allow_amt[0]['proc_allow_amt'];
							$trans_allow_write_amt_total=$trans_allow_amt[0]['trans_amt'];
							$write_off_code_trans_id=$trans_allow_amt[0]['write_off_code_id'];
							
							if($trans_allow_write_amt_total>0){
								$chk_old_writeoff_amt=$trans_allow_write_amt_total-$writeOff;
								$writeOff=$trans_allow_write_amt_total;
							}else{
								$chk_old_writeoff_amt=$trans_allow_write_amt_total;
							}
										
							$adj_amt_total="";
							$sel_adj_amt="select sum(trans_amt) as trans_amt_total 
											from manual_batch_transactions
											where
											charge_list_detaill_id='$charge_list_detail_id'
											and (payment_claims='Adjustment' || payment_claims='Over Adjustment')
											and post_status!=1
											and del_status=0
											";
							$adj_paid_amt=mysqlifetchData($sel_adj_amt);
							$adj_amt_total=$adj_paid_amt[0]['trans_amt_total'];
										 
							$total_writ_amt=$trans_write_amt_total+$chk_old_writeoff_amt;
							$NewBalance=$NewBalance-$trans_amt_total-$total_writ_amt;
							
							if($NewBalance<0){
								$overPaymentForProc_trans=substr($NewBalance,1);
								$overPaymentForProc=$overPaymentForProc+$overPaymentForProc_trans;
							}
							if($adj_amt_total>0){
								if($overPaymentForProc>0){
									if($overPaymentForProc>=$adj_amt_total){
										$overPaymentForProc=$overPaymentForProc-$adj_amt_total;
										$NewBalance=0;
									}else{
										$chk_adj_bal=$adj_amt_total-$overPaymentForProc;
										$NewBalance=$chk_adj_bal;
										$paidForProc=$paidForProc-$chk_adj_bal;
										$overPaymentForProc=0;
									}
								}else{
									$NewBalance=$adj_amt_total;
									$overPaymentForProc=0;
									$paidForProc=$paidForProc-$adj_amt_total;
								}
							}	
							$deductAmt=$deductAmt+$trans_deduct_amt_total;
							//$writeOff=$writeOff+$trans_write_amt_total;
							if($batch_deb_amt_adust>0){
								if($overPaymentForProc_trans>=$batch_deb_amt_adust){
									$overPaymentForProc = $overPaymentForProc-$batch_deb_amt_adust;
								}else{
									$chk_ovr_deb_amt=$batch_deb_amt_adust-$overPaymentForProc_trans;
									$paidForProc = $paidForProc-$chk_ovr_deb_amt;
									$NewBalance = $NewBalance+$chk_ovr_deb_amt;
									$overPaymentForProc = 0;
								}
							}
							$paidForProc=($paidForProc+$trans_amt_total)-($overPaymentForProc_trans);
							$approvedAmt = $approvedAmt;
							$deductAmt = number_format($deductAmt, 2);
							$paidForProc = number_format($paidForProc, 2);
							$creditProcAmount = number_format($creditProcAmount, 2);
							$totalAmount = number_format($totalAmount, 2);
							$amount = number_format($amount, 2);
							$copay_collect=copay_apply_chk($cpt4_code,'','');
							
							if($trans_allow_amt_total>0){
								if($write_off_code_trans_id>0){
									$write_off_code_id=$write_off_code_trans_id;
								}
								$w_code_qry = imw_query("SELECT w_code FROM write_off_code 
															WHERE w_id = '$write_off_code_id'");							
								$w_code_row = imw_fetch_array($w_code_qry);
								$write_off_code=$w_code_row['w_code'];
								$approvedAmt=$trans_allow_amt_total;
							}
						}
						?>
						<tr>
							<td>
								<div class="checkbox">
									<input type="checkbox" value="<?php echo $charge_list_detail_id; ?>" id="chkbx<?php echo $seq; ?>" name="chkbx[]" onClick="return checkPaymentBox('<?php echo $seq; ?>', '<?php echo $writeOffAmount; ?>','<?php echo $getProcCountRows_all; ?>')" />
									<label for="chkbx<?php echo $seq; ?>"></label>
								</div>
								
							</td>
							<input type="hidden" value="<?php echo $encounter_id; ?>" id="enc_arr<?php echo $seq; ?>" name="enc_arr[]">
							 <?php 
							 if($cpt_onetime==0 && $procNewBalance>0){
								$cpt_onetime=0;
								 if($copay_collect_proc[0]==true && $copay_collect_proc[1]==true){
							 		if($copay_collect[0]==true && $copay_collect[1]==true){
							?>
										<input type="hidden" value="<?php echo $charge_list_detail_id; ?>" name="copay_apply_procedure<?php echo $seq; ?>" id="copay_apply_procedure<?php echo $seq; ?>">
							<?php 
										$cpt_onetime++;
									}	
								}else{
									if($balForProc>0){
							?>
								<input type="hidden" value="<?php echo $charge_list_detail_id; ?>" name="copay_apply_procedure<?php echo $seq; ?>" id="copay_apply_procedure<?php echo $seq; ?>">
							<?php 	
									$cpt_onetime++;
									}	
								}
							}
							if($min_copay_one==0 && $procNewBalance>0){
								$min_copay_one=0;
								$minus_copay_value=0;
								 if($coPayAdjustedAmount==1){
									if($coPay==$tot_paid_chk1){
										$minus_copay_value=$coPay;
										$min_copay_one++;
									}else{
										if($tot_paid_chk1<>0){
											$minus_copay_value=$coPay-$tot_paid_chk1;
											$min_copay_one++;
										}
									}
								}
							}else{
								$minus_copay_value=0.00;
							}
							
							//echo $tot_paid_chk1;
							?>
							<input type="hidden" value="<?php echo $minus_copay_value; ?>" name="minus_copay<?php echo $seq; ?>" id="minus_copay<?php echo $seq; ?>">
							<td>
								<div class="row">
									<?php if($coPayAdjustedAmount==1){ ?>
										<div class="col-sm-4">
											<img src="../../library/images/confirm.gif" width="16px" align="middle"/>
										</div>
									<?php } ?>
									<div class="col-sm-8" id="cptIdTd<?php echo $seq; ?>">
										<?php echo $cptPracCode;//$charge_list_detail_id; ?>	
									</div>		
								</div>
							</td>
							
							<td><?php echo $cptDesc; ?></td>
							<td id="totalFee<?php echo $seq; ?>"><?php echo "$".$totalAmount; ?></td>
							<td id="getBalTd<?php echo $seq; ?>" style="display:none;"><?php echo $balForProc; ?></td>
							
                            <td>
								<div class="input-group"  style="width:130px">
									<div class="input-group-addon">
										<span class="glyphicon glyphicon-usd"></span>	
									</div>	
									<input type="text" name="approvedText<?php echo $seq; ?>" id="approvedText<?php echo $seq; ?>" class="form-control" value="<?php echo $approvedAmt; ?>" onChange="return checkChkBox(<?php echo $seq; ?>);">
									<input type="hidden" size="10" name="appActualText<?php echo $seq; ?>" id="appActualText<?php echo $seq; ?>" value="<?php echo $approvedAmt; ?>">
								</div>
							</td>
							<td>
								<div class="input-group" style="width:130px">
									<div class="input-group-addon">
										<span class="glyphicon glyphicon-usd"></span>	
									</div>	
									<input type="text" <?php if($deductAmt>0){echo"style='color:#FFC000;font-weight:bold;'";}?> name="deductibleText<?php echo $seq; ?>" id="deductibleText<?php echo $seq; ?>" class="form-control" value="<?php echo $deductAmt; ?>">
								</div>
                            </td>
							<td id="writeOffTd<?php echo $seq; ?>"><?php echo "$".number_format($writeOff, 2); ?></td>
							<!-- Credit Paid Pay -->
							<td id="creditAmtTd<?php echo $seq; ?>" style="color:#009900;font-weight:bold;">
							<?php 
									$credit_total=0;
									$amt_payment=0;
									$gettot_crd1 = "SELECT sum(amountApplied) as amt_adust  FROM creditapplied WHERE charge_list_detail_id_adjust  = '$charge_list_detail_id'  and delete_credit='0' and credit_applied='1'";
									$gettot_crdQry1 = imw_query($gettot_crd1);
									$gettot_crdrow1 = imw_fetch_array($gettot_crdQry1);
									$amt_adust = $gettot_crdrow1['amt_adust'];
									
									$gettot_crd7 = "SELECT  sum(amountApplied) as amt_payment FROM creditapplied WHERE charge_list_detail_id = '$charge_list_detail_id' and crAppliedTo='payment' and delete_credit='0' and credit_applied='1'";
									$gettot_crdQry7 = imw_query($gettot_crd7);
									$gettot_crdrow7 = imw_fetch_array($gettot_crdQry7);
									$amt_payment=$gettot_crdrow7['amt_payment'];
									$credit_total_final=$amt_adust+$amt_payment;
									echo "$".number_format($credit_total_final,2);
								?>
							</td>
							<td id="paidAmtPrev<?php echo $seq; ?>"  style="color:#009900;font-weight:bold;"><?php if($paidForProc<0) echo "$".'0.00'; else echo "$".$paidForProc; ?></td>
							<td>
								<div class="input-group" style="width:130px">
									<div class="input-group-addon">
										<span class="glyphicon glyphicon-usd"></span>	
									</div>	
									<input type="text" size="10" name="payNew<?php echo $seq; ?>" id="payNew<?php echo $seq; ?>" class="form-control" value="<?php echo "0.00"; ?>" onBlur="return selectChanges('<?php echo $seq; ?>')" onChange="return paymentChange('<?php echo $seq; ?>', '<?php echo $writeOffAmount; ?>')">
								</div>
                            </td>
							<!--<td width="61"  class="text_10">-->
								<input type="hidden" name="counterIdArr[]" id="counterIdArr[]" value="<?php echo $seq; ?>">
								<input type="hidden" name="chargeListDetailIdArr[]" id="chargeListDetailIdArr[]" value="<?php echo $charge_list_detail_id; ?>">
								<input type="hidden" size="10" name="paidAmtText<?php echo $seq; ?>" id="paidAmtText<?php echo $seq; ?>" class="text_10" onChange="return checkChkBox(<?php echo $seq; ?>);" value="<?php echo "0.00"; ?>">
							<!--</td>-->
							<!-- Credit Paid Pay -->
							<!-- O/s Charges Paid By -->
							<td id="newBalanceTd<?php echo $seq; ?>">
								<font color="<?php echo ($NewBalance <= 0) ? "Green" : "Red";?>">
									<?php if($NewBalance<0){ echo "$".'0.00'; }else{ if($overPaymentForProc>0) echo '-'. "$".number_format($overPaymentForProc,2); else echo "$".number_format($NewBalance,2); } ?>
								</font>
								<input type="hidden" value="<?php if($NewBalance<0){ echo '0.00'; }else{ if($overPaymentForProc>0) echo '0.00'; else echo $NewBalance; } ?>" name="bal_chk_for_copay" id="bal_chk_for_copay<?php echo $seq; ?>">
						  </td>
							<td class="text-right" id="overPaidPrev<?php echo $seq; ?>" style="color:#5D738E;font-weight:bold;"><?php if($overPaymentForProc>0) echo number_format($overPaymentForProc,2); else echo number_format($overPaymentForProc,2); ?></td>
							<!--<td width="61" align="right" class="text_10">-->
								<input type="hidden" readonly size="10" name="overPayment<?php echo $seq; ?>" id="overPayment<?php echo $seq; ?>" class="text_10" value="<?php echo $overPaymentForProc; ?>">
								<input type="hidden" readonly size="10"  name="overPaymentNow<?php echo $seq; ?>" id="overPaymentNow<?php echo $seq; ?>" class="text_10" value="">
								<input type="hidden" readonly size="10" name="overPayments_chk<?php echo $seq; ?>" id="overPayments_chk<?php echo $seq; ?>" class="text_10" value="<?php echo $overPaymentForProc; ?>">
							<!--</td>-->
							<!-- <td width="60"  class="text_10b">&nbsp;</td> -->
							<!-- O/s Charges Paid By -->
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td><?php if($postedDate!="00-00-0000") echo $postedDate; else echo "-"; ?></td>					
							
							<td><?php echo $operatorNamePaid; ?></td>
						</tr>
							<?php 
								if($cptPracCode==99212 || $cptPracCode==99213 || $cptPracCode==99214 || $cptPracCode==99215 || 
									$cptPracCode==99201 || $cptPracCode==99202 || $cptPracCode==99203 || $cptPracCode==99204 || 
									$cptPracCode==99205 || $cptPracCode==99241 || $cptPracCode==99242 || $cptPracCode==99243 ||
									$cptPracCode==99244 || $cptPracCode==99245 || $cptPracCode==92012 || $cptPracCode==92013 ||
									$cptPracCode==92014 || $cptPracCode==92002 || $cptPracCode==92003 || $cptPracCode==92004){
									if($copay>0 && $procNewBalance>0){
										//echo $encounter_id_chk_copay.'=='.$encounter_id;
										if($encounter_id_chk_copay==$encounter_id){
											//$coPayNotRequired=1;
											$encounter_id_chk_copay=0;
										}
										$encounter_id_chk_copay=$encounter_id;
										
										$getCoPayWriteOffIDStr = "SELECT * FROM paymentswriteoff
																	WHERE patient_id = '$patient_id'
																	AND encounter_id = '$encounter_id'
																	AND charge_list_detail_id = 0
																	AND delStatus = 0";
										$getCoPayWriteOffIDQry = imw_query($getCoPayWriteOffIDStr);
										$coPayWriteOffCount = imw_num_rows($getCoPayWriteOffIDQry);
										if($coPayWriteOffCount>0){
											$getCoPayWriteOffIDRow = imw_fetch_array($getCoPayWriteOffIDQry);
												$coPay_write_off_id = $getCoPayWriteOffIDRow['write_off_id'];
										}
										$paymentStatusType = $getCoPayWriteOffIDRow['paymentStatus'];
										if(($coPayNotRequired == 0) && ($coPayWriteOff!='1')){
											$coPayNotRequired=1;
											if($copayPaid<=0){
											$show_row="
												<tr>
													<td class='text-center'>
														<input  name='copayAmount_hid_new' id='copayAmount_hid_new".$seq."' type='hidden' size='8' value='".number_format(($copay),2)."' />
														<div class='checkbox'>
															<input type='checkbox' name='coPayChk".$seq."' id='coPayChk".$seq."' value='true'/>
															<label for='coPayChk".$seq."'></label>
														</div>
													</td>
													<td align='left' class='text_10b' colspan='18'>CoPay</td>
												</tr>
												";
											}else{
												?>
												<tr height="25">									
													<td colspan="2"  class="text_10b"><img src="../../library/images/confirm.png" width="16px" class="text_10b" /></td>
													<td colspan="20" align="left" class="text_10b">CoPay</td>
												</tr>
												<?php
											}
										  }	
									   } 
									} 
								 ?>
						<?php
						}
						echo $show_row;
					}
					
					?>
					<input type="hidden" name="amountDue" id="amountDue" value="<?php echo $amountDue; ?>" />
					<input type="hidden" name="copaySt" id="copaySt" value="<?php echo $getRowValidation; ?>" />
					<input type="hidden" name="totalBalance" id="totalBalance" value="<?php echo $totalBalanceNewDue; ?>">
					<input type="hidden" name="charge_list_detail_id" id="charge_list_detail_id" value="<?php echo $charge_list_detail_id; ?>">
					<input type="hidden" value="<?php echo $totalRefractionAmountFor; ?>" name="totalRefractionAmountFor" id="totalRefractionAmountFor">
					<input type="hidden" value="<?php echo $seq; ?>" name="sequence" id="sequence">
			  </table>	
			</div>	
		<?php
		}
		}else{
			?>
			<table class="table table-condensed table-bordered">
				<tr>
					<td><span style="font-family:Arial; font-size:11px; font-weight:bold;">No Dues For Any DOS.</span></td>
				</tr>
			</table>
			<?php
		}
		?>
	</div>
	<table class="table table-condensed table-bordered"><!--FFF3E8-->
				<tr style="display:none;">
					<td align="left">
						<table >
							<tr>
								<td class="text-left">
									<table class="table table-bordered table-condensed">
										<tr>
											<td>Amount Due:</td>
											<td id="payAmt"><?php echo $totalEncounterBalance; ?></td>
											<!-- <td width="120" align="right" class="text_10b">Credit Balalance:</td>
											<td width="65" align="left" class="text_10"><?php //echo number_format($creditAmountBalance, 2); ?></td>
 -->										<td class="text-right">Credit Balance:</td>
											<td><?php echo number_format($overPayment, 2); ?></td>
											<td>Paid Date:</td>
											<td>
												<table border="0" cellpadding="0" cellspacing="0">
													<tr>
													  <td width="78"><input id="date1" type="text"  name="paidDate" onBlur="checkdate(this);" value="<?php echo $todate; ?>" size='13' maxlength="10" class="text_10" /></td>
														<td width="251"><a href="javascript:newWindow(1);"><img src='../../images/clender2.png' border='0' /></a></td>
													</tr>	
												</table>
										  </td>
										</tr>
								  </table>
							  </td>
							</tr>
							<tr height="25">
								<td align="left">
									<table border="0" cellpadding="0" cellspacing="0">
										<tr>
											<td width="50"  class="text_10b">Amount:</td>
											<td width="50"  class="text_10"><input type="hidden" name="insSelected" id="insSelected" value="">
												<input name="paidAmount" id="paidAmount" readonly type="text" size="8" value="<?php echo number_format($amountToPay, 2); ?>" class="text_10" />
											</td>
											<td width="10"></td>
											<td width="50"  class="text_10b">Copay:</td>
											<td width="50"  class="text_10">
												<input id="copayAmount" name="copayAmount" readonly type="text" size="8"  class="text_10" />
											</td>
											
											<td width="10"></td>
											<td width="58" align="right" class="text_10b">Who Paid: </td>
											<td width="75" align="left" class="text_10b">
												<!-- <select name="paidBy" class="text_10" style="width:75px;" onChange="return paymentModeFn();">
													<option value="Patient">Patient</option>
													<option value="Res. Party">Res. Party</option>
													<option value="Insurance" selected="selected">Insurance</option>
												</select> -->
												<input type="hidden"  name="paidBy" id="paidBy" value="">
											</td>
											<td width="10"></td>									
											<td width="42"  class="text_10b">Method:</td>
											<td width="65"  class="text_10">
												<select name="paymentMode" id="paymentMode" class="text_10" style="width:65px;" onChange="return showRow();">
													<option value="Cash">Cash</option>
													<option value="Check" selected>Check</option>
													<option value="Credit Card">Credit Card</option>
												</select>
											</td>
											<input type="hidden" name="credit_note" id="credit_note" value="">
											<input type="hidden" name="dos" id="dos" value="<?php echo $dos;?>">
											<input type="hidden" name="insProviderName" id="insProviderCoId" value="">
											<td width="10"></td>
											<td width="140" align="left" id="checkRow" style="display:block;">
												<table border="0" cellpadding="0" cellspacing="0">
													<tr>
														<td width="50" align="right" class="text_10b">Check #:</td>
														<td width="90" align="left"><input name="checkNo" id="checkNo" type="text" class="text_10" size="15" value="" /></td>	
													</tr>
												</table>
											</td>
											<td>
												<table border="0" cellpadding="0" cellspacing="0">
													<tr>
														<td colspan="3" id="insCoNames">
														<table>
															<tr>
																<td width="50" class="text_10b">Ins. Pr.:</td>
																<td width="60" align="left" class="text_10b">
																	<!-- <select name="insProviderName" class="text_10" style="width:55px;">
																	<option value=""></option>
																	<?php
																		/*foreach($insProvidersNameArr as $id => $insCoName){
																			if($insCoName!=''){
																				$lenInsCoName = strlen($insCoName);
																				if($lenInsCoName>5){
																					$insCoName = substr($insCoName, 0, 5)."..";
																				}
																				?>
																				<option value="<?php echo $id; ?>"><?php echo $insCoName; ?></option>
																				<?php
																			}
																		}*/
																	?>
																	</select> -->
																</td>
																<td width="2"></td>
														  </tr>
														  </table>
														</td>
														<td width="480" align="left" id="creditCardRow" style="display:none;">
															<table border="0" cellpadding="0" cellspacing="0">
																<tr>
																	<td width="30" align="right" class="text_10b">CC #:</td>
																	<td width="92" align="left"><input name="cCNo" id="cCNo" type="text" class="text_10" size="15" value="" /></td>
																	<td width="71" align="right" class="text_10b">Exp. Date:</td>
																	<td width="108" align="left">
																		<input id="date2" type="text"  name="expireDate" value="" size='13' maxlength="10" class="text_10" />
																	</td>
																	<td width="63" class="text_10b" align="right">CC Type.:</td>
																	<td width="100" align="left" id="creditCardCoTd">
																		<select name="creditCardCo" id="creditCardCo" style="width:100px;" class="text_10" onChange="return cCCompany();">
																			<option value=""></option>
																			<option value="AX">American Express</option>
                                                                            <option value="Care Credit">Care Credit</option>
																			<option value="Dis">Discover</option>
																			<option value="MC">Master Card</option>
																			<option value="Visa">Visa</option>
																			<option value="Others">Others</option>
																		</select>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr height="25">
								<td align="left" style="padding-left:25px;">
									<table border="0">
										<tr>
											<td width="" align="left">
												<table border="0">
													<tr>
														<td  class="text_10b"><input type="submit" name="applySubmit" id="applySubmit" value="  Apply  " class="button" /></td>
														<td  class="text_10b"><input type="submit" name="applyRecieptSubmit" id="applyRecieptSubmit" value="Apply & Print Receipt" class="button" /></td>
														<td  class="text_10b"><input type="button" name="printRecieptSubmit" id="printRecieptSubmit" value="Print Receipt" class="button" onClick="return printReciept(<?php echo $encounter_id; ?>);" /></td>
														<td  class="text_10b"><input type="button" name="collectEid" id="collectEid" value="Collection" class="button" onClick="return makeCollection(<?php echo $encounter_id; ?>);" /></td>
														<td  class="text_10b"><input type="button" name="cancel" id="cancel" value="  Cancel  " class="button" onClick="return closeMe();" /></td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			  </table>
			</td>
		</tr>
		
		<tr>
			<td>&nbsp;</td>
		</tr>
	</table>
	</form>
<?php
}
?>
</div>
</div>
</body>
</html>