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
?>
<?php 
include_once('../../config/globals.php');
include_once($GLOBALS['fileroot'].'/library/classes/common_function.php');
include_once($GLOBALS['fileroot'].'/library/classes/cls_common_function.php');
include_once($GLOBALS['fileroot'].'/library/classes/class.language.php');
include_once($GLOBALS['fileroot'].'/library/classes/work_view/wv_functions.php');
include_once($GLOBALS['fileroot'].'/interface/chart_notes/cl_functions.php'); 
include_once($GLOBALS['fileroot'].'/library/classes/work_view/Patient.php');

// GET LENSE CODES AND COLORS in ARRAY
$arrLensCode	=	getLensCodeArr();
$arrLensColor	=	getLensColorArr();
//---------------------------------

//GET ALL LENS MANUFACTURER IN ARRAY
$arrLensManuf = getLensManufacturer();

//GET FEE OF DEFAULT CL CHARGES
$arrDefaultCPTFee = getCPTDefaultCharges();

$clPriceArray = array();
$priceResult = imw_query("select clm.make_id, cft.cpt_fee from contactlensemake clm left 
join cpt_fee_table cft on clm.cpt_fee_id=cft.cpt_fee_id where cft.fee_table_column_id='1'");
while($priceRow = imw_fetch_array($priceResult)){
	$clPriceArray[$priceRow['make_id']] = $priceRow['cpt_fee'];
}

/** Ajax Call Functions **/

if(isset($_GET['val']) && isset($_GET['sid'])){
	$record = $_GET['val'];
	$qry = "select case_id from insurance_case_types where case_id ='$record' && vision = '1'";
	$records = imw_query($qry);
	print imw_num_rows($records);
	exit();
}

if(isset($_POST['get_manufac_details'])){
	$qry="Select cpt_fee_id FROM contactlensemake 
	WHERE make_id = ".$_POST['manufac_id']." and del_status=0 and (source = 0 || source = '') ORDER BY style, type ASC";
	$rs=imw_query($qry) or die(imw_error());
	if(imw_num_rows($rs)>0){
		while($res=imw_fetch_array($rs)){
			$cpt_fee = $arrDefaultCPTFee[$res['cpt_fee_id']];
			/*$styleType = ''; $sep='';
			if($res['manufacturer']!=''){ $styleType = $res['manufacturer']; $sep='-';}
			if($res['style']!=''){ $styleType.=$sep.$res['style']; $sep='-';}
			if($res['type']!=''){ $styleType.=$sep.$res['type']; }
			
			$arrManufac[]=$styleType;
			$arrManufacId[$styleType]=$res['make_id'];
			$arrCPTFee[]= $arrDefaultCPTFee[$res['cpt_fee_id']];*/
		}
		echo $cpt_fee;
	}
	exit();
}
 
$opt = new Patient($_SESSION['patient']);
//$insCaseId = $opt->getInuranceCaseId();
//

// Query for getting patient's insurance case id
$patId = $_SESSION['patient'];
$insCaseIdQuery = "select ins_caseid from insurance_case where patient_id='".$patId."' and  case_status='Open' order by ins_caseid DESC limit 1";
$insCaseIdResult = imw_query($insCaseIdQuery);
$insCaseIdRow = imw_fetch_array($insCaseIdResult);
$insCaseId = $insCaseIdRow['ins_caseid'];

//$objManageData = new ManageData;
$OBJCommonFunction = new CLSCommonFunction;

$clws_id = $_REQUEST['clws_id'];
$authUserID = $_SESSION['authUserID'];
$dos = $_REQUEST['dos'];//YYYY-mm-dd
$print_order_id = $_REQUEST['print_order_id'];
$displayCurrDate = date("m-d-Y");
$callFrom = $_REQUEST['callFrom'];

$LensBoxOD= array();
$LensBoxOS= array();
$LensBoxOU= array();
$PriceOD= array();
$PriceOS= array();
$PriceOU= array();
$arrPrintOD =array();
$arrPrintOS =array();
$arrPrintOU =array();
$clwsid_ArrOD = array();
$clwsid_ArrOS = array();
$clwsid_ArrOU = array();
$diag_code_arr=array();
if($clws_id<=0 && $print_order_id<=0){
	$rs=imw_query("Select clws_id,dos FROM contactlensmaster WHERE patient_id='".$_SESSION['patient']."' and patient_id>0 ORDER BY clws_id desc limit 0,1");
	if(imw_num_rows($rs)>0){
		$res=imw_fetch_array($rs);
		$_REQUEST['clws_id'] = $clws_id = $res['clws_id'];
		$_REQUEST['dos'] = $dos = $res['dos'];
	}
}

function mysqlifetchdata($qry){
	$return = array();
	$query_sql = imw_query($qry);
	while($row = imw_fetch_array($query_sql)){
		$return[] = $row;
	}
	return $return;
}


function getPatientDataRow($id){
	$sql = "SELECT id,fname,mname,lname,street,city,state,postal_code,zip_ext from patient_data where id = '$id'";
	$res = imw_query($sql);
	$row_address = @imw_fetch_array($res);
	return 	$row_address;
}

function changeDateFormat($selectDt) {
	$gtDate='';
	if($selectDt) {
		list($Mnt,$Dy,$Yr) = explode('-',$selectDt);
		if($Mnt && $Dy && $Yr) {
			$gtDate = $Yr.'-'.$Mnt.'-'.$Dy;
			//$setDate = date('Y-m-d',mktime(0,0,0,$Mnt,$Dy,$Yr));
		}	
	}
	return $gtDate;
}

function displayDateFormat($selectDt) {
	$setDate='';
	if($selectDt && $selectDt!='0000-00-00') {
		list($Yr,$Mnt,$Dy) = explode('-',$selectDt);
		if($Yr && $Mnt && $Dy) {
			//$setDate = $Mnt.'-'.$Dy.'-'.$Yr;
			$setDate = date('m-d-Y',mktime(0,0,0,$Mnt,$Dy,$Yr));
		}	
	}
	return $setDate;
}

function patient_proc_bal_update($encounter){
		if(empty($encounter) === false){
			$chld_cpt_self_arr=array();
			$getProcedureDetailsStr = "SELECT b.cpt_prac_code,b.cpt_desc,b.not_covered,b.cpt_fee_id
										FROM cpt_fee_tbl as b
										WHERE b.not_covered=1";
			$getProcedureDetailsQry = imw_query($getProcedureDetailsStr);
			while($getProcedureDetailsRows = imw_fetch_array($getProcedureDetailsQry)){
				$chld_cpt_ref_arr[$getProcedureDetailsRows['cpt_fee_id']]=$getProcedureDetailsRows['cpt_prac_code'];
			}
			
			$thS = imw_query("select * from patient_charge_list where del_status='0' and encounter_id='$encounter'");
			while($row = imw_fetch_array($thS)){
				$qryRes[] = $row;
			}
			$enc_id_arr=array();
			for($i=0;$i<count($qryRes);$i++){
				$patient_id = $qryRes[$i]['patient_id'];
				$encounter_id = $qryRes[$i]['encounter_id'];
				$charge_list_id = $qryRes[$i]['charge_list_id'];
				$primary_paid = $qryRes[$i]['primary_paid'];
				$secondary_paid = $qryRes[$i]['secondary_paid'];
				$tertiary_paid = $qryRes[$i]['tertiary_paid'];
				$primaryInsuranceCoId = $qryRes[$i]['primaryInsuranceCoId'];
				$secondaryInsuranceCoId = $qryRes[$i]['secondaryInsuranceCoId'];
				$tertiaryInsuranceCoId = $qryRes[$i]['tertiaryInsuranceCoId'];
				$copay = $qryRes[$i]['copay'];
				
				$getPaidByStr = "SELECT a.paid_by,a.insProviderId,
								 a.insCompany,b.overPayment,
								 a.paymentClaims,a.encounter_id,
								 b.paidForProc,b.charge_list_detail_id
								 FROM patient_chargesheet_payment_info as a,
								 patient_charges_detail_payment_info b
								 WHERE 
								 a.encounter_id in($encounter_id)
								 AND a.payment_id = b.payment_id
								 AND b.deletePayment='0'";
				$getPaidByQry = imw_query($getPaidByStr);
				while($getPaidByRows = imw_fetch_array($getPaidByQry)){
					$enc_id=$getPaidByRows['encounter_id'];
					if($getPaidByRows['paymentClaims']=='Negative Payment'){
						$tot_enc_neg_paid_amt_arr[$getPaidByRows['encounter_id']][$getPaidByRows['insCompany']][]=$getPaidByRows['paidForProc'];
						$tot_enc_neg_paid_amt_arr[$getPaidByRows['encounter_id']][$getPaidByRows['insCompany']][]=$getPaidByRows['overPayment'];
						
						$tot_chld_neg_paid_amt_arr[$getPaidByRows['charge_list_detail_id']][$getPaidByRows['insCompany']][]=$getPaidByRows['paidForProc'];
						$tot_chld_neg_paid_amt_arr[$getPaidByRows['charge_list_detail_id']][$getPaidByRows['insCompany']][]=$getPaidByRows['overPayment'];
						
						$final_tot_neg_paid_amt_arr[$getPaidByRows['insCompany']][]=$getPaidByRows['paidForProc'];
						$final_tot_neg_paid_amt_arr[$getPaidByRows['insCompany']][]=$getPaidByRows['overPayment'];
					}else{
						$tot_enc_paid_amt_arr[$getPaidByRows['encounter_id']][$getPaidByRows['insCompany']][]=$getPaidByRows['paidForProc'];
						$tot_enc_paid_amt_arr[$getPaidByRows['encounter_id']][$getPaidByRows['insCompany']][]=$getPaidByRows['overPayment'];
						
						$tot_chld_paid_amt_arr[$getPaidByRows['charge_list_detail_id']][$getPaidByRows['insCompany']][]=$getPaidByRows['paidForProc'];
						$tot_chld_paid_amt_arr[$getPaidByRows['charge_list_detail_id']][$getPaidByRows['insCompany']][]=$getPaidByRows['overPayment'];
						
						if($getPaidByRows['charge_list_detail_id']==0){
							$tot_chld_paid_amt_copay_arr[$getPaidByRows['encounter_id']][$getPaidByRows['insCompany']][]=$getPaidByRows['paidForProc'];
							$tot_chld_paid_amt_copay_arr[$getPaidByRows['encounter_id']][$getPaidByRows['insCompany']][]=$getPaidByRows['overPayment'];
						}
						$final_tot_paid_amt_arr[$getPaidByRows['insCompany']][]=$getPaidByRows['paidForProc'];
						$final_tot_paid_amt_arr[$getPaidByRows['insCompany']][]=$getPaidByRows['overPayment'];
					}
					
					if($getPaidByRows['paymentClaims']=='Deposit'){
						$paid_proc_arr[$enc_id]['paidForProc'][] = $getPaidByRows['paidForProc'];
						$deposit_proc_arr[$enc_id][$getPaidByRows['charge_list_detail_id']][] = $getPaidByRows['paidForProc'];
					}
				}
				
				$pri_bor_paid_col="";
				$sec_bor_paid_col="";
				$tri_bor_paid_col="";
				$pat_bor_paid_col="";
				$pat_heard_ins="false";
				if($primary_paid=="false" && $primaryInsuranceCoId>0){
					$pri_bor_paid_col="border";
				}else if($secondary_paid=="false" && $secondaryInsuranceCoId>0){
					$sec_bor_paid_col="border";
				}else if($tertiary_paid=="false" && $tertiaryInsuranceCoId>0){
					$tri_bor_paid_col="border";
				}else{
					if($newBal>0){
						$pat_bor_paid_col="border";
					}
					$pat_heard_ins="true";
				}
				$qry=imw_query("select * from patient_charge_list_details where del_status='0' and charge_list_id='$charge_list_id'");
				while($row=imw_fetch_array($qry)){
					$proc_approvedAmt=$row['approvedAmt'];
					$charge_list_detail_id=$row['charge_list_detail_id'];
					$chld_id=$row['charge_list_detail_id'];
					$proc_newBalance=$row['newBalance'];
					$proc_coPayAdjustedAmount = $row['coPayAdjustedAmount'];
					$proc_selfpay = $row['proc_selfpay'];
					$proc_deductAmt = $row['deductAmt'];
					$procCode  = $row['procCode'];
					
					$chld_deduct_arr=array();
					$getDeductDetailsStr = "SELECT deduct_ins_id,deduct_amount FROM payment_deductible WHERE delete_deduct=0 and
											deductible_by='Insurance' and charge_list_detail_id='$charge_list_detail_id'";
					$getDeductDetailsQry = imw_query($getDeductDetailsStr);
					while($getDeductDetailsRows = imw_fetch_array($getDeductDetailsQry)){
						$chld_deduct_arr[$getDeductDetailsRows['deduct_ins_id']][]=$getDeductDetailsRows['deduct_amount'];
					}
					$pri_deduct=0;
					$sec_deduct=0;
					$tri_deduct=0;
					$pri_deduct=array_sum($chld_deduct_arr[$primaryInsuranceCoId]);
					$sec_deduct=array_sum($chld_deduct_arr[$secondaryInsuranceCoId]);
					$tri_deduct=array_sum($chld_deduct_arr[$tertiaryInsuranceCoId]);
					$for_pri_deduct=true;
					$for_sec_deduct=false;
					$for_tri_deduct=false;
					$for_pat_deduct=false;
					
					$chld_denied_arr=array();
					$getDeniedDetailsStr = "SELECT deniedById,deniedAmount FROM deniedpayment WHERE denialDelStatus=0 and
											deniedBy='Insurance' and charge_list_detail_id='$charge_list_detail_id'";
					$getDeniedDetailsQry = imw_query($getDeniedDetailsStr);
					while($getDeniedDetailsRows = imw_fetch_array($getDeniedDetailsQry)){
						$chld_denied_arr[$getDeniedDetailsRows['deniedById']][]=$getDeniedDetailsRows['deniedAmount'];
					}
					$pri_denied=0;
					$sec_denied=0;
					$tri_denied=0;
					$pri_denied=array_sum($chld_denied_arr[$primaryInsuranceCoId]);
					$sec_denied=array_sum($chld_denied_arr[$secondaryInsuranceCoId]);
					$tri_denied=array_sum($chld_denied_arr[$tertiaryInsuranceCoId]);
					
					$tot_chld_pri_paid_amt_all_num=count($tot_chld_paid_amt_arr[$chld_id][1]);
					$tot_chld_sec_paid_amt_all_num=count($tot_chld_paid_amt_arr[$chld_id][2]);
					$tot_chld_tri_paid_amt_all_num=count($tot_chld_paid_amt_arr[$chld_id][3]);
					$tot_chld_pat_paid_amt_all_num=count($tot_chld_paid_amt_arr[$chld_id][0]);
					
					$tot_chld_pri_paid_amt_all=array_sum($tot_chld_paid_amt_arr[$chld_id][1])-array_sum($tot_chld_neg_paid_amt_arr[$chld_id][1]);	
					$tot_chld_sec_paid_amt_all=array_sum($tot_chld_paid_amt_arr[$chld_id][2])-array_sum($tot_chld_neg_paid_amt_arr[$chld_id][2]);
					$tot_chld_tri_paid_amt_all=array_sum($tot_chld_paid_amt_arr[$chld_id][3])-array_sum($tot_chld_neg_paid_amt_arr[$chld_id][3]);
					$tot_chld_pat_paid_amt_all=array_sum($tot_chld_paid_amt_arr[$chld_id][0])-array_sum($tot_chld_neg_paid_amt_arr[$chld_id][0]);
					
					if($proc_coPayAdjustedAmount>0){
						$tot_chld_pat_paid_amt_all=$tot_chld_pat_paid_amt_all+array_sum($tot_chld_paid_amt_copay_arr[$encounter_id][0]);
					}
					if($primary_paid=="true"){
						$for_pri_deduct=false;	
					}
					if($secondary_paid=="true"){
						$for_sec_deduct=false;	
					}
					if($secondaryInsuranceCoId>0 && ($pri_deduct>0 || $primary_paid=="true" || $tot_chld_pri_paid_amt_all>0 || $tot_chld_pri_paid_amt_all_num>0) && $sec_deduct<=0 && $secondary_paid=="false"){
						$for_sec_deduct=true;
						$for_pri_deduct=false;
					}
					if($secondaryInsuranceCoId>0 && ($pri_denied>0 || $primary_paid=="true" || $tot_chld_pri_paid_amt_all>0 || $tot_chld_pri_paid_amt_all_num>0) && $sec_denied<=0  && $secondary_paid=="false"){
						$for_sec_deduct=true;
						$for_pri_deduct=false;
					}
					
					if($for_sec_deduct==true && ($sec_deduct>0 || $sec_denied>0 || $tot_chld_sec_paid_amt_all>0 || $tot_chld_sec_paid_amt_all_num>0) && ($pri_denied>0 || $pri_deduct>0 || $tot_chld_pri_paid_amt_all>0 || $tot_chld_pri_paid_amt_all_num>0)){
						$for_sec_deduct=false;
						$for_pri_deduct=false;
						$for_tri_deduct=true;
					}
					if($for_pri_deduct==true && ($pri_deduct>0 || $pri_denied>0 || $primaryInsuranceCoId==0 || $tot_chld_pri_paid_amt_all>0 || $tot_chld_pri_paid_amt_all_num>0)){
						$for_pri_deduct=false;
					}
					if($for_pri_deduct==false && $for_sec_deduct==false){
						$for_tri_deduct=true;
					}
					if($for_tri_deduct==true && ($tri_deduct>0 || $tri_denied>0 || $tertiaryInsuranceCoId==0 || $tot_chld_tri_paid_amt_all>0 || $tot_chld_tri_paid_amt_all_num>0)){
						$for_pri_deduct=false;
					}
					
					if(($for_sec_deduct==false || $sec_deduct>0 || $sec_denied>0 || $secondaryInsuranceCoId==0 || $tot_chld_sec_paid_amt_all>0 || $tot_chld_sec_paid_amt_all_num>0) 
					&& ($for_tri_deduct==false || $tri_deduct>0 || $tri_denied>0 || $tertiaryInsuranceCoId==0 || $tot_chld_tri_paid_amt_all>0 || $tot_chld_tri_paid_amt_all_num>0) 
					&& $for_pri_deduct==false){
						$for_pat_deduct=true;
					}
					
					/*if($for_sec_deduct==false && $for_pri_deduct==false && ($sec_deduct>0 || $secondaryInsuranceCoId==0)){
						$for_pat_deduct=true;
					}
					
					if($for_sec_deduct==false && $for_pri_deduct==false && ($sec_denied>0 || $secondaryInsuranceCoId==0)){
						$for_pat_deduct=true;
					}*/
					$ref_amt="";
					$ref_amt=$chld_cpt_ref_arr[$procCode];
					$tot_pt_balance_proc=0;
					$pri_due=0;
					$sec_due=0;
					$tri_due=0;
					if($pat_heard_ins == "true"){
						$pri_due = 0;
						$sec_due = 0;
						$tri_due = 0;
						//$patientDueRes = $proc_approvedAmt-($tot_chld_pri_paid_amt_all+$tot_chld_sec_paid_amt_all);
						$tot_pt_balance_proc=$proc_newBalance;
					}else{	
						if($pat_bor_paid_col!=""){
							$tot_pt_balance_proc=$proc_newBalance;
							$pri_due = 0;
							$sec_due = 0;
							$tri_due = 0;
						}else{
							if($proc_coPayAdjustedAmount>0){	
								if($proc_selfpay>0){
									$tot_pt_balance_proc=$proc_newBalance;
								}else{
									if($for_pri_deduct == true){
										$pri_due=$proc_newBalance;
									}else{
										if($for_sec_deduct == true || $for_tri_deduct == true){
											//$tot_pt_balance_proc=$copay;
										}else{
											$tot_pt_balance_proc=$proc_newBalance;
										}
									}
								}
							}else{
								if($for_pri_deduct == true){
									$pri_due=$proc_newBalance;
								}else{
									if($for_sec_deduct == true || $for_tri_deduct == true){
									}else{
										$tot_pt_balance_proc=$proc_newBalance;
									}
								}
							}
							if($proc_selfpay>0 || $ref_amt>0){
								$tot_pt_balance_proc=$proc_newBalance;
							}
							//$tot_pt_balance_proc=$tot_pt_balance_proc-$tot_chld_pat_paid_amt_all;
						}
					}
					if($tot_pt_balance_proc<0){ $tot_pt_balance_proc=0;}
					
					if($for_sec_deduct == true){
						$sec_due=$proc_deductAmt;
					}
					if($for_sec_deduct == false && $for_tri_deduct == true){
						$tri_due=$proc_deductAmt;
					}
					if($for_pat_deduct == true){
						$tot_pt_balance_proc=$proc_newBalance;
					}
					if(($tot_chld_pri_paid_amt_all>0 || $tot_chld_pri_paid_amt_all_num>0) && $primaryInsuranceCoId>0){
						if(($tot_chld_sec_paid_amt_all>0 || $tot_chld_sec_paid_amt_all_num>0) && $secondaryInsuranceCoId>0){
							if(($tot_chld_tri_paid_amt_all>0 || $tot_chld_tri_paid_amt_all_num>0) && $tertiaryInsuranceCoId>0){
								$tot_pt_balance_proc=$proc_newBalance;
							}else{
								if($tertiaryInsuranceCoId>0 && $for_tri_deduct==true){
									$tri_due=$proc_newBalance-$tot_pt_balance_proc;
								}else{
									$tot_pt_balance_proc=$proc_newBalance;
								}
							}
						}else{
							if($secondaryInsuranceCoId>0 && $for_sec_deduct==true){
								$sec_due=$proc_newBalance-$tot_pt_balance_proc;
							}else{
								if(($tot_chld_tri_paid_amt_all>0 || $tot_chld_tri_paid_amt_all_num>0) && $tertiaryInsuranceCoId>0){
									$tot_pt_balance_proc=$proc_newBalance;
								}else{
									if($tertiaryInsuranceCoId>0 && $for_tri_deduct==true){
										$tri_due=$proc_newBalance-$tot_pt_balance_proc;
									}else{
										$tot_pt_balance_proc=$proc_newBalance;
									}
								}
							}
						}
					}else{
						if($primaryInsuranceCoId>0){
							$pri_due=$proc_newBalance-$tot_pt_balance_proc;
						}
						if($for_sec_deduct == true){
							$pri_due=0;
							$sec_due=$proc_newBalance;
						}
						if($for_sec_deduct == false && $for_tri_deduct == true){
							$pri_due=0;
							$sec_due=0;
							$tri_due=$proc_newBalance;
						}
						if($for_pat_deduct == true){
							$tot_pt_balance_proc=$proc_newBalance;
							$pri_due=0;
							$sec_due=0;
							$tri_due=0;
						}
						
					}
					if($proc_selfpay>0 || $ref_amt>0){
						$tot_pt_balance_proc=$proc_newBalance;
						$pri_due=0;
						$sec_due=0;
						$tri_due=0;
					}
					if($pri_due<0){ $pri_due=0;}
					if($sec_due<0){ $sec_due=0;}
					if($tri_due<0){ $tri_due=0;}
					if($proc_newBalance<=0){ $tot_pt_balance_proc=0;}
					
					if($proc_newBalance>0){
						$tot_due_amt=$pri_due+$sec_due+$tri_due+$tot_pt_balance_proc;
						$diff_due_amt=$tot_due_amt-$proc_newBalance;
						//echo $newBalance.'-'.$tot_due_amt.'-'.$pri_due.'-'.$sec_due.'-'.$pat_due;
						if($diff_due_amt!=0){
							if($pri_due>0){
								$pri_due=$pri_due-$diff_due_amt;
							}else if($sec_due>0){
								$sec_due=$sec_due-$diff_due_amt;
							}else if($tri_due>0){
								$tri_due=$tri_due-$diff_due_amt;
							}else if($tot_pt_balance_proc>0){
								$tot_pt_balance_proc=$tot_pt_balance_proc-$diff_due_amt;
							}
							if($pri_due<0){$pri_due=0;}
							if($sec_due<0){$sec_due=0;}
							if($tri_due<0){$tri_due=0;}
							if($tot_pt_balance_proc<0){$tot_pt_balance_proc=0;}
							if($pri_due==0 && $sec_due==0 && $tri_due==0){
								$tot_pt_balance_proc=$proc_newBalance;	
							}
						}else{
							if($tot_pt_balance_proc>$proc_newBalance){
								$tot_pt_balance_proc=$proc_newBalance;
							}
							if($pri_due==0 && $sec_due==0 && $tri_due==0){
								$tot_pt_balance_proc=$proc_newBalance;
							}
						}
					}else{
						$pri_due=0;
						$sec_due=0;
						$tri_due=0;
						$tot_pt_balance_proc=0;
					}
					
					$len_TX=0;
					$qry = imw_query("select charge_list_detail_id from tx_payments where charge_list_detail_id='$charge_list_detail_id' and del_status='0' limit 0,1");
					while($row = imw_fetch_array($qry)){
						$qryResTx[] = $row;
					}
					//$qryResTx = $this->mysqlifetchdata();
					$len_TX = count($qryResTx);
					if($len_TX==0){
						$up_qry="update patient_charge_list_details set pri_due=$pri_due,sec_due=$sec_due,tri_due=$tri_due,pat_due=$tot_pt_balance_proc where charge_list_detail_id ='$charge_list_detail_id'";
						imw_query($up_qry);
					}
					//echo $patient_id.'-'.$encounter_id.'<br>';
				}
			}
		}
	}


//START CODE TO GET USER INFORMATION
$qry_user = "SELECT * from users where id = '".$authUserID."'";
$res_user = imw_query($qry_user);
$row_user = imw_fetch_array($res_user);
$fname = $row_user['fname'];
$mname = $row_user['mname'];
$lname = $row_user['lname'];
if($mname != '' && $mname != 'NULL') {
	$userLoggedname = $fname."&nbsp;".$mname."&nbsp;".$lname;
}
else {
	$userLoggedname = $fname."&nbsp;".$lname;
}

//MAKE ARRAY FOR TYPEAHEAD
$arrManufac=$arrManufacId=array();
$qry="Select make_id, manufacturer, style, type, base_curve, diameter, cpt_fee_id FROM contactlensemake 
WHERE del_status=0 and (source = 0 || source = '') ORDER BY style, type ASC";
$rs=imw_query($qry) or die(imw_error());
if(imw_num_rows($rs)>0){
	while($res=imw_fetch_array($rs)){
		$styleType = ''; $sep='';
		if($res['manufacturer']!=''){ $styleType = $res['manufacturer']; $sep='-';}
		if($res['style']!=''){ $styleType.=$sep.$res['style']; $sep='-';}
		if($res['type']!=''){ $styleType.=$sep.$res['type']; }
		
		$arrManufac[]=$styleType;
		$arrManufacId[$styleType]=$res['make_id'];
		$arrCPTFee[]= $arrDefaultCPTFee[$res['cpt_fee_id']];
	}
}unset($rs);
json_encode($arrManufac);
json_encode($arrManufacId);
json_encode($arrCPTFee);

$alert_str = '';
//  START SAVE RECORD
if($_REQUEST['recordSave']=="saveTrue") {
		$recordExist =0;
		$insuranceCaseId = $primaryInsCoId = $secondaryInsCoId = $tertiaryInsCoId=0;
		$messageTo='';
		$messageTo= implode(',', $_POST['physicians']);		

		$dos_exp=explode('-',$_REQUEST['dos']);
		if(strlen($dos_exp[0])<4){
 			$dos_db=changeDateFormat($_REQUEST['dos']);
		}else{
			$dos_db=$_REQUEST['dos'];
		}
		//GET INSURANCE DETAILS
		$insuranceCaseId=$insCaseId;
		$getPrimaryInsCoDetails = imw_query("SELECT * FROM insurance_data 
												WHERE ins_caseid='$insuranceCaseId'
												AND pid='".$_SESSION['patient']."'
												AND actInsComp='1'");
		while($getPrimaryInsCoDetailsRow = imw_fetch_array($getPrimaryInsCoDetails)){
			if($getPrimaryInsCoDetailsRow['type']=="primary"){
				$primaryInsCoId = $getPrimaryInsCoDetailsRow['provider'];
			}
			if($getPrimaryInsCoDetailsRow['type']=="secondary"){
				$secondaryInsCoId = $getPrimaryInsCoDetailsRow['provider'];
			}
			if($getPrimaryInsCoDetailsRow['type']=="tertiary"){
				$tertiaryInsCoId = $getPrimaryInsCoDetailsRow['provider'];
			}
		}
				
		$insUpdtPrintOdrQry="INSERT INTO ";
		$whereQry='';

		if($print_order_id) {
			$insUpdtPrintOdrQry = 'UPDATE ';
			$whereQry="WHERE print_order_id='".$print_order_id."'";
			$recordExist =1;
		}
		
		$clSupply = $_REQUEST["clSupply"];
		
		$orderTrialDsp = $_REQUEST['orderTrialDsp'];
		$orderTrialDspNumber='';
		$orderTrialDspNumberAr=array();
		if($orderTrialDsp=='orderTrialDspDoctor') {  
			$orderTrialDspNumberAr = $_REQUEST['orderTrialDspNumber'];
			list($orderTrialDspNumber,$keyclwsid) = explode('-',$orderTrialDspNumberAr);
		}else if($orderTrialDsp=='orderTrial') {  
			$orderTrialDspNumberAr = $_REQUEST['orderTrialNumber'];
			list($orderTrialDspNumber,$keyclwsid) = explode('-',$orderTrialDspNumberAr);
		}

		$prescribedByQuery = "select prescribed_by from contactlensmaster where clws_id = '".$_REQUEST["clws_id"]."'";
		$prescribedByResult = imw_query($prescribedByQuery) or die(imw_error()." - ".$prescribedByQuery);
		$prescribedBy = 0;
		if(imw_num_rows($prescribedByResult) > 0){
			$prescribedByRow = imw_fetch_assoc($prescribedByResult);
			$prescribedBy = $prescribedByRow['prescribed_by'];
		}
		
		$insOrdersql = $insUpdtPrintOdrQry." `clprintorder_master` set ";
		
		$insOrdersql.="
			clws_id='".$_REQUEST["clws_id"]."',
			patient_id='".$_REQUEST["patient_id"]."',
			provider_id='".$_REQUEST["provider_id"]."',
			technician_id='".$_REQUEST["technician_id"]."',
			operator_id='".$_REQUEST["operator_id"]."',
			dos='".addslashes($dos_db)."',
			print_order_savedatetime='".addslashes($_REQUEST["print_order_savedatetime"])."',
			clEvaluationDate='".changeDateFormat($_REQUEST["clEvaluationDate"])."',
			clEvaluationComment='".addslashes($_REQUEST["clEvaluationComment"])."',
			clFittingDate='".changeDateFormat($_REQUEST["clFittingDate"])."', 
			clFittingComment='".addslashes($_REQUEST["clFittingComment"])."', 
			clTeachDate='".changeDateFormat($_REQUEST["clTeachDate"])."', 
			clTeachComment='".addslashes($_REQUEST["clTeachComment"])."', 
			orderTrialDsp='".addslashes($_REQUEST["orderTrialDsp"])."', 
			orderTrialDspNumber='".$orderTrialDspNumber."', 
			prescripClwsId='".$_REQUEST["prescripClwsId"]."', 
			orderReorderSupply='".addslashes($_REQUEST["orderReorderSupply"])."', 
			lensComment='".addslashes($_REQUEST["lensComment"])."', 
			clSupply='".addslashes($_REQUEST["clSupply"])."', 
			totalCharges='".addslashes($_REQUEST["totalCharges"])."', 
			ptPickUpAtFrontDesk='".addslashes($_REQUEST["ptPickUpAtFrontDesk"])."', 
			checkBoxShipToHomeAddress='".addslashes($_REQUEST["checkBoxShipToHome"])."', 
			ShipToHomeAddress='".addslashes($_REQUEST["ShipToHomeAddress"])."', 
			dateOrdered='".changeDateFormat($_REQUEST["dateOrdered"])."', 
			OrderedTrialSupply='".addslashes($_REQUEST["OrderedTrialSupply"])."', 
			OrderedComment='".addslashes($_REQUEST["OrderedComment"])."', 
			dateReceived='".changeDateFormat($_REQUEST["dateReceived"])."', 
			ReceivedTrialSupply='".addslashes($_REQUEST["ReceivedTrialSupply"])."', 
			ReceivedComment='".addslashes($_REQUEST["ReceivedComment"])."', 
			dateNotified='".changeDateFormat($_REQUEST["dateNotified"])."', 
			NotifiedComment='".addslashes($_REQUEST["NotifiedComment"])."', 
			datePickedUp='".changeDateFormat($_REQUEST["datePickedUp"])."', 
			PickedUpTrialSupply='".addslashes($_REQUEST["PickedUpTrialSupply"])."', 
			PickedUpComment='".addslashes($_REQUEST["PickedUpComment"])."',
			ins_case='".$_REQUEST["ins_case"]."', 
			auth_number='".$_REQUEST["auth_number"]."', 
			auth_amount='".addslashes($_REQUEST["auth_amount"])."', 
			disc_amount='".addslashes($_REQUEST["disc_amount"])."', 
			order_status='".addslashes($_REQUEST["orderStatus"])."',
			messageComments='".addslashes($_REQUEST["messageComments"])."',
			messageTo='".addslashes($messageTo)."',
			trial_order='".addslashes($_REQUEST["trial_order"])."',
			prescribed_by='".$prescribedBy."'";
	
		$insOrdersql.=$whereQry;
		$insOrderRes=imw_query($insOrdersql) or print(imw_error());
		if($recordExist==0) {
			$print_order_id = imw_insert_id();
		}

		// get ids of stored order details
		$arrrIDS = array();
		$arrrChldIDS = array();
		$qryIds=imw_query("Select id,chld_id_od,chld_id_os from clprintorder_det where print_order_id='".$print_order_id."'") or die(imw_error());
		while($resIds = imw_fetch_array($qryIds))
		{
			$chld='';
			$arrIDS[]=$resIds['id'];
			if($resIds['chld_id_od']!='' && $resIds['chld_id_od']!=0) { $chld = $resIds['chld_id_od']; }
			if($resIds['chld_id_os']!='' && $resIds['chld_id_os']!=0) { $chld = $resIds['chld_id_os']; }
			if($chld!=''){
				$arrrChldIDS[]=$chld;
			}
		}

		//START SAVE DATA IN DETAIL TABLE
		$totOD = $_REQUEST['txtTotOD'];
		$totOS = $_REQUEST['txtTotOS'];	
		
		$insOrdersql='';
		$allcptOD = $allcptOS = $allcptOU ='';
		$cptCodeOD = array();	
		$cptCodeOS = array();
		$arrNewIDS = array(); 
		$arrNewChlds = array(); 
		$delIDS 	= array();
		$delChilds	= array();	

		if($dos_db<'2015-10-01'){ //ICD9
			$sphereNegDxODCode=$sphereNegDxOSCode='367.1';
			$spherePosDxCode='367.0';
			$addDxCode='367.4';
			$enc_icd10='0';
		}else{ //ICD10
			$sphereNegDxODCode='H52.11';
			$sphereNegDxOSCode='H52.12';
			$spherePosDxCode='H52.0';
			$addDxCode='H52.4';		
			$enc_icd10='1';
		}
		$chl_diag_arr=array();

		// ADD OD
		$dxNumber=0;
		for($i=0; $i< $totOD; $i++)
		{	
			if($_REQUEST["QtyOD".$i]>0)
			{
				$insQry = $whereQry ='';
				$cptOD ='';	$addID =''; $Dx_codeOD='';
				$clDetId=$_REQUEST["cl_det_idOD".$i];
				$insQry="Insert into clprintorder_det set print_order_id='".$print_order_id."', clws_id = '".$clws_id."', ";

				if($_REQUEST['ordODId'.$i]!='~' && $_REQUEST['ordODId'.$i]!=''){
					list($addID, $chldid) = explode('~',$_REQUEST['ordODId'.$i]);
					$insQry="Update clprintorder_det set print_order_id='".$print_order_id."', clws_id = '".$clws_id."', ";					
					$whereQry =" WHERE id ='".$addID."'";
				}

				if($_POST['sphere'.$clDetId] < 0 ) $Dx_codeOD = $sphereNegDxODCode;
				else if($_POST['sphere'.$clDetId] > 0 ) $Dx_codeOD = $spherePosDxCode;
				else if($_POST['add'.$clDetId] <> '' ) $Dx_codeOD = $addDxCode;
				
				//ARRAY SETTING FOR DX CODES
				if($chl_diag_arr[$Dx_codeOD]){
					$dxNumber=$chl_diag_arr[$Dx_codeOD];
				}else{
					$dxNumber=sizeof($chl_diag_arr)+1;
				}
				$chl_diag_arr[$Dx_codeOD]=$dxNumber;				
			
				if($_REQUEST["LensBoxOD".$i."ID"]!=''){
					$cptOD = $arrLensManuf[$_REQUEST["LensBoxOD".$i."ID"]]['cpt_fee_id'];
				}
				//--------------------------

				if($cptOD>0){
					$boolVal = checkCPTCodeExists($cptOD);
					if($boolVal=='1'){
						$checkCptCodeResOD[]= $cptOD;
					}else{
						$arrNotCPT[$_REQUEST["LensBoxOD".$i]] = $_REQUEST["LensBoxOD".$i];
					}
				}else{
					$arrNotCPT[$_REQUEST["LensBoxOD".$i]] = $_REQUEST["LensBoxOD".$i];
				}
				if($_REQUEST["LensBoxOD".$i."ID"]==''){ $_REQUEST["LensBoxOD".$i."ID"]=getManufID($_REQUEST["LensBoxOD".$i]);}

				$insOrdersql=$insQry."	
				cl_det_id = '".trim(addslashes($clDetId))."',
				LensBoxOD='".trim(addslashes($_REQUEST["LensBoxOD".$i]))."',
				LensBoxOD_ID='".trim(addslashes($_REQUEST["LensBoxOD".$i."ID"]))."',
				lensNameIdList='".trim(addslashes($_REQUEST["lensNameIdList".$i]))."',
				colorNameIdList='".trim(addslashes($_REQUEST["colorNameIdList".$i]))."',
				PriceOD='".trim(addslashes($_REQUEST["PriceOD".$i]))."', 
				QtyOD='".trim(addslashes($_REQUEST["QtyOD".$i]))."', 
				SubTotalOD='".trim(addslashes($_REQUEST["SubTotalOD".$i]))."', 
				DiscountOD='".trim(addslashes($_REQUEST["DiscountOD".$i]))."', 
				TotalOD='".trim(addslashes($_REQUEST["TotalOD".$i]))."', 
				PaidOD='".trim(addslashes($_REQUEST["PaidOD".$i]))."', 
				InsOD='".trim(addslashes($_REQUEST["InsOD".$i]))."', 
				BalanceOD='".trim(addslashes($_REQUEST["BalanceOD".$i]))."', 
				cptCodeOD='".addslashes($cptOD)."', 
				Dx_codeOD='".addslashes($Dx_codeOD)."'";
				$insOrdersql.=$whereQry;

				imw_query($insOrdersql) or print(imw_error()); 
				if($addID==''){
					$addID = imw_insert_id();
				}
				if($addID!='')
				{
					$discAmt=$_REQUEST["DiscountOD".$i];
					if(strstr($_REQUEST["DiscountOD".$i],'%')){
						$disPer=str_replace('%','', $_REQUEST["DiscountOD".$i]);
						$discAmt= ($_REQUEST["SubTotalOD".$i] * $disPer) / 100;
					}
					$cptCodeOD[$addID]['code'] = $cptOD;
					$cptCodeOD[$addID]['PriceOD'] = $_REQUEST["PriceOD".$i];
					$cptCodeOD[$addID]['SubTotalOD'] = $_REQUEST["SubTotalOD".$i];
					$cptCodeOD[$addID]['QtyOD'] = $_REQUEST["QtyOD".$i];
					$cptCodeOD[$addID]['DiscountOD'] = $discAmt;
					$cptCodeOD[$addID]['cl_det_dis_idOD'] = $_REQUEST["cl_det_dis_idOD".$i];
					//$allcptOD.="'".$cptOD."',";
				}				
				$arrNewIDS[] = $addID;
				$arrNewChlds[] = $chldid;
			}
		}
		//$allcptOD = substr($allcptOD,0, strlen($allcptOD)-1);

		// ADD OS
		$insOrdersql='';
		for($i=0; $i< $totOS; $i++)
		{
			if($_REQUEST["QtyOS".$i]>0)
			{		
				$insQry = $whereQry ='';
				$cptOS = ''; $addID=''; $Dx_codeOS='';
				$clDetId=$_REQUEST["cl_det_idOS".$i];
				$insQry="Insert into clprintorder_det set print_order_id='".$print_order_id."', clws_id = '".$clws_id."', ";

				if($_REQUEST['ordOSId'.$i]!='~' && $_REQUEST['ordOSId'.$i]!=''){
					list($addID, $chldid) = explode('~',$_REQUEST['ordOSId'.$i]);
					$insQry="Update clprintorder_det set print_order_id='".$print_order_id."', clws_id = '".$clws_id."', ";										
					$whereQry =" WHERE id ='".$addID."'";
				}
				

				if($_POST['sphere'.$clDetId] < 0 ) $Dx_codeOS = $sphereNegDxOSCode;
				else if($_POST['sphere'.$clDetId] > 0 ) $Dx_codeOS = $spherePosDxCode;
				else if($_POST['add'.$clDetId] <> '' ) $Dx_codeOS = $addDxCode;

				//ARRAY SETTING FOR DX CODES
				if($chl_diag_arr[$Dx_codeOS]){
					$dxNumber= $chl_diag_arr[$Dx_codeOS];
				}else{
					$dxNumber=sizeof($chl_diag_arr)+1;
				}
				$chl_diag_arr[$Dx_codeOS]=$dxNumber;				
			
				if($_REQUEST["LensBoxOS".$i."ID"] !=''){
					$cptOS = $arrLensManuf[$_REQUEST["LensBoxOS".$i."ID"]]['cpt_fee_id'];
				}
				//--------------------------
				

				if($cptOS>0){
					$boolVal = checkCPTCodeExists($cptOS);
					if($boolVal=='1'){
						$checkCptCodeResOS[]= $cptOS;
					}else{
						$arrNotCPT[$_REQUEST["LensBoxOS".$i]] = $_REQUEST["LensBoxOS".$i];
					}
				}else{
					$arrNotCPT[$_REQUEST["LensBoxOS".$i]] = $_REQUEST["LensBoxOS".$i];
				}				
				
				if($_REQUEST["LensBoxOS".$i."ID"]==''){ $_REQUEST["LensBoxOS".$i."ID"]=getManufID($_REQUEST["LensBoxOS".$i]);}
				
				$insOrdersql=$insQry."
				cl_det_id = '".trim(addslashes($clDetId))."',
				LensBoxOS='".trim(addslashes($_REQUEST["LensBoxOS".$i]))."', 
				LensBoxOS_ID='".trim(addslashes($_REQUEST["LensBoxOS".$i."ID"]))."', 
				lensNameIdListOS='".trim(addslashes($_REQUEST["lensNameIdListOS".$i]))."',
				colorNameIdListOS='".trim(addslashes($_REQUEST["colorNameIdListOS".$i]))."',
				PriceOS='".trim(addslashes($_REQUEST["PriceOS".$i]))."', 
				QtyOS='".trim(addslashes($_REQUEST["QtyOS".$i]))."', 
				SubTotalOS='".trim(addslashes($_REQUEST["SubTotalOS".$i]))."', 
				DiscountOS='".trim(addslashes($_REQUEST["DiscountOS".$i]))."', 
				TotalOS='".trim(addslashes($_REQUEST["TotalOS".$i]))."', 
				PaidOS='".trim(addslashes($_REQUEST["PaidOS".$i]))."', 
				InsOS='".trim(addslashes($_REQUEST["InsOS".$i]))."', 
				BalanceOS='".trim(addslashes($_REQUEST["BalanceOS".$i]))."',  
				cptCodeOS='".addslashes($cptOS)."', 
				Dx_codeOS='".addslashes($Dx_codeOS)."'";
				$insOrdersql.=$whereQry;
				
				imw_query($insOrdersql) or print(imw_error());
				if($addID==''){
					$addID = imw_insert_id();
				}
				
				if($addID!='')
				{
					$discAmt=$_REQUEST["DiscountOS".$i];
					if(strstr($_REQUEST["DiscountOS".$i],'%')){
						$disPer=str_replace('%','', $_REQUEST["DiscountOS".$i]);
						$discAmt= ($_REQUEST["SubTotalOS".$i] * $disPer) / 100;
					}
					$cptCodeOS[$addID]['code'] = $cptOS;
					$cptCodeOS[$addID]['PriceOS'] = $_REQUEST["PriceOS".$i];
					$cptCodeOS[$addID]['SubTotalOS'] = $_REQUEST["SubTotalOS".$i];
					$cptCodeOS[$addID]['QtyOS'] = $_REQUEST["QtyOS".$i];
					$cptCodeOS[$addID]['DiscountOS'] = $discAmt;
					$cptCodeOS[$addID]['cl_det_dis_idOS'] = $_REQUEST["cl_det_dis_idOS".$i];
					//$allcptOS.="'".$cptOS."',";
				}
				$arrNewIDS[] = $addID;	
				$arrNewChlds[] = $chldid;			
			}
		}
		//$allcptOS = substr($allcptOS,0, strlen($allcptOS)-1);

		/*******HL7 code starts here********/
		if(defined('HL7_ZMS_GENERATION') && constant('HL7_ZMS_GENERATION') === true){
			$hl7_res_clm = imw_query("SELECT form_id FROM contactlensmaster WHERE clws_id = '".$_REQUEST["clws_id"]."' LIMIT 0,1");
			if($hl7_res_clm && imw_num_rows($hl7_res_clm)==1){
				$hl7_rs_clm = imw_fetch_assoc($hl7_res_clm);
				require_once(dirname(__FILE__)."/../../hl7sys/old/CLS_makeHL7.php");
				$makeHL7 = new makeHL7();
				$makeHL7->log_HL7_message($hl7_rs_clm['form_id'],'ZMS');
			}
		}
		/*************HL7 ends here************/


		// GET DELETED IDS FROM THIS ORDER
		$delIDS = array_diff($arrIDS, $arrNewIDS);
		$delChilds = array_diff($arrrChldIDS, $arrNewChlds);
		// DELETE RECORDS THAT ARE REMOVED AT CURRENT SAVING
		if(sizeof($delChilds)>0){
			$delChildsStr  = implode(",",$delChilds);
			$qryDel  =imw_query("update patient_charge_list_details set del_status='1',del_operator_id='".$_SESSION['authId']."',trans_del_date='".date('Y-m-d H:i:s')."' WHERE charge_list_detail_id IN(".$delChildsStr.")") or die(imw_error());
		}
		if(sizeof($delIDS)>0){
			$delIDSStr  = implode(",",$delIDS);
			$qryDel  =imw_query("Delete from clprintorder_det WHERE id IN(".$delIDSStr.")") or die(imw_error());
		}
		//--------------------------------------------------

		// SEND USER MESSAGES
		$messageDet='';
		$msgSCLTit = $msgRGPTit = 0;
		if($messageTo!=''){
			$messageToArr  =explode(',', $messageTo);
		}
		
		$messageDet = trim($_POST['messageComments']);

		if($_REQUEST['checkBoxShipToHome']=='PtPickYes'){
			$messageDet.='<br>Contact Lens will receive at : Office Address';
		}else if($_REQUEST['checkBoxShipToHome']=='HomeAddressYes'){
			$messageDet.='<br>Contact Lens will received at : Home Address';
		}
		
		if(sizeof($messageToArr)>0){
			//GET ORDER DETAIL
			$qry="Select LensBoxOD, LensBoxOS, clEye, clType, SclsphereOD, SclsphereOS, SclCylinderOD, SclCylinderOS, SclaxisOD, SclaxisOS, SclBcurveOD, SclBcurveOS, SclDiameterOD, SclDiameterOS, SclAddOD, SclAddOS,
				  RgpPowerOD, RgpPowerOS, RgpBCOD, RgpBCOS, RgpDiameterOD, RgpDiameterOS, RgpOZOD, RgpOZOS, RgpAddOD, RgpAddOS,
				  RgpCustomPowerOD, RgpCustomPowerOS, RgpCustomBCOD, RgpCustomBCOS, RgpCustomDiameterOD, RgpCustomDiameterOS, RgpCustomOZOD, RgpCustomOZOS, RgpCustomAddOD, RgpCustomAddOS 
				  FROM contactlensworksheet_det wsDet LEFT JOIN clprintorder_det wsOrd ON wsOrd.cl_det_id = wsDet.id WHERE wsDet.clws_id='".$_REQUEST['clws_id']."' ORDER BY wsDet.clType";
			$rs =imw_query($qry);
			if(imw_num_rows($rs)>0){
				$messageDet.='<table cellpadding="1" cellspacing="1" border="1">';
				while($res=imw_fetch_array($rs)){
					$eye=$res['clEye'];
					if($res['clType']=='scl'){
						if($msgSCLTit==0){
							$messageDet.='<tr style="height:25px;"><td style="width:70px;"><strong>Eye</strong></td><td style="width:70px;"><strong>BC / DIA</strong></td><td style="width:70px;"><strong>SPH</strong></td><td style="width:70px;"><strong>CYL</strong></td><td style="width:70px;"><strong>AXIS</strong></td><td style="width:70px;"><strong>ADD</strong></td><td style="width:100px;"><strong>Lens Type</strong></td></tr>';	
							$msgSCLTit=1;
						}

						$messageDet.='<tr style="height:25px;"><td>'.$eye.'</td><td>'.$res['SclBcurve'.$eye].' / '.$res['SclDiameter'.$eye].'</td><td>'.$res['Sclsphere'.$eye].'</td><td>'.$res['SclCylinder'.$eye].'</td><td>'.$res['Sclaxis'.$eye].'</td><td>'.$res['SclAdd'.$eye].'</td><td>'.$res['LensBox'.$eye].'</td></tr>';	
						
					}else if($res['clType']=='rgp' || $res['clType']=='cust_rgp'){
						
						if($msgRGPTit==0){
							$messageDet.='<tr style="height:25px;"><td style="width:70px;"><strong>Eye</strong></td><td style="width:70px;"><strong>BC / DIA</strong></td><td style="width:70px;"><strong>Power</strong></td><td style="width:140px;" colspan="2"><strong>Description</strong></td><td style="width:70px;"><strong>ADD</strong></td><td style="width:100px;"><strong>Lens Type</strong></td></tr>';	
							$msgRGPTit=1;
						}
						if($res['clType']=='rgp'){
							$messageDet.='<tr style="height:25px;"><td>'.$eye.'</td><td>'.$res['RgpBC'.$eye].' / '.$res['RgpDiameter'.$eye].'</td><td>'.$res['RgpPower'.$eye].'</td><td colspan="2">'.$res['RgpOZ'.$eye].'</td><td>'.$res['RgpAdd'.$eye].'</td><td>'.$res['LensBox'.$eye].'</td></tr>';	
						}
						if($res['clType']=='cust_rgp'){
							$messageDet.='<tr style="height:25px;"><td>'.$eye.'</td><td>'.$res['RgpCustomBC'.$eye].' / '.$res['RgpCustomDiameter'.$eye].'</td><td>'.$res['RgpCustomPower'.$eye].'</td><td colspan="2">'.$res['RgpCustomOZ'.$eye].'</td><td>'.$res['RgpCustomAdd'.$eye].'</td><td>'.$res['LensBox'.$eye].'</td></tr>';	
						}
					}
				}
				$messageDet.='</table>';
			}
			//----------------
			if(count($messageToArr) > 0){
				$arrMsgMaster = array();
				$arrMsgMaster['subject'] 	= 'Contact Lens Ordered';
				$arrMsgMaster['msg'] 		= $messageDet;
				$arrMsgMaster['sender_id'] 	= $_SESSION['authId'];
				$arrMsgMaster['sent_date'] 	= date('Y-m-d H:i:s');
				$master_insert_id 			= AddRecords($arrMsgMaster,'user_messages_master');
				foreach($messageToArr as $usrId){
					$msgQry="Insert into user_messages SET 
					message_subject ='Contact Lens Ordered',
					message_to = '".$usrId."',
					message_text = '".$messageDet."',
					message_sender_id = '".$_SESSION['authUserID']."',
					message_send_date = '".date('Y-m-d H:i:s')."',
					message_status = '0',
					message_read_status ='0',
					patientId = '".$_REQUEST["patient_id"]."'";
					$msgRs = imw_query($msgQry);
				}
			}
		}
		//-- END SEND Message 

	// CASE - ENTER CHARGES	
	if($_REQUEST['enterCharges']=='yes'){

		if($print_order_id){
			$qryChargeListId = "select chld_id_od,chld_id_os from clprintorder_det where print_order_id='$print_order_id'";
			$resChargeListId = imw_query($qryChargeListId);
			$rowChargeListId = imw_fetch_array($resChargeListId);
			$chld_id_od = $rowChargeListId['chld_id_od'];
			$chld_id_os = $rowChargeListId['chld_id_os'];
		}

		$patient_id = $_REQUEST["patient_id"];
		$total_charges = $_REQUEST["totalCharges"];

		//Insuracnce case ID
		$qryCaseType = "Select ins_caseid from insurance_case where patient_id='".$patient_id."' && 
						ins_case_type='".$_REQUEST["ins_case"]."' ORDER BY  `insurance_case`.`ins_caseid` DESC ";
		$resCaseType = imw_query($qryCaseType);
		$rowCaseType = imw_fetch_array($resCaseType);
		$case_type_id = $rowCaseType['ins_caseid'];
		
		if($_REQUEST['clws_charges_id']!=''){
			$clSupply+= $_REQUEST['cptEvalCharges'];
		}
		
		$totAmt = $total_charges;
		$charge_list_id ='';
		
		if((sizeof($checkCptCodeResOD)>0) || (sizeof($checkCptCodeResOS)>0)){
			$pos_fac_part='';
			
			//GETTING POS FACILITY
			$gro_id=0;
			$sch_fac_id=0;
			$qry="Select sa_facility_id FROM schedule_appointments WHERE sa_patient_id='".$_SESSION['patient']."' AND sa_app_start_date='".date('Y-m-d')."' ORDER BY id ASC LIMIT 0,1";
			$rs=imw_query($qry);
			$res=imw_fetch_assoc($rs);
			$sch_fac_id=$res['sa_facility_id'];
			unset($rs);
			if($sch_fac_id<=0 || $sch_fac_id==''){ $sch_fac_id=$_SESSION['login_facility']; }
			
			$qry="Select facility.default_group, pos_facilityies_tbl.pos_facility_id, pos_facilityies_tbl.pos_id FROM facility 
			JOIN pos_facilityies_tbl ON pos_facilityies_tbl.pos_facility_id=facility.fac_prac_code WHERE facility.id='".$sch_fac_id."'";
			$rs=imw_query($qry);
			$res=imw_fetch_assoc($rs);
			$gro_id=$res['default_group'];
			$pos_fac_id=$res['pos_facility_id'];
			$posId=$res['pos_id'];
			unset($rs);

			//GETTING GROUP
			if($gro_id<=0){
				$sel_proc=imw_query("select default_group from users where default_group>0 and id='".$_REQUEST["provider_id"]."'");
				$proc_row=imw_fetch_array($sel_proc);
				$gro_id=$proc_row['default_group'];
				unset($sel_proc);
			}
			if($gro_id<=0){
				$sel_proc=imw_query("Select gro_id from groups_new where group_institution='0' and del_status='0' order by gro_id asc");
				$proc_row=imw_fetch_array($sel_proc);
				$gro_id=$proc_row['gro_id'];
				unset($sel_proc);
			}
			if($gro_id>0){ 	$pos_fac_part=",gro_id	='".$gro_id."' "; }
			if($pos_fac_id>0){ 	$pos_fac_part.=",facility_id='".$pos_fac_id."' "; }	

			$recordExist=0;
			$encounterIDStr = '';
			//echo "Select charge_list_id, encounter_id, cl_print_ord_id from patient_charge_list WHERE del_status='0' and cl_print_ord_id='".$print_order_id."'";
			//exit();
			$qryRs=imw_query("Select charge_list_id, encounter_id, cl_print_ord_id from patient_charge_list WHERE del_status='0' and cl_print_ord_id='".$print_order_id."'") or die(imw_error());
			if(imw_num_rows($qryRs) > 0) { 			
				$qryRes = imw_fetch_array($qryRs);
				$qryEnterChargesQry = 'Update ';
				$qryWhere = " where cl_print_ord_id='$print_order_id'";
				$charge_list_id = $qryRes['charge_list_id'];
				$encounterIdText = $qryRes['encounter_id'];
				$recordExist=1;
			}else{
				$encounterIdText = getEncounterId();
				$qryEnterChargesQry = 'Insert into ';
				$encounterIDStr = " encounter_id = '$encounterIdText',entered_date='".date('Y-m-d')."',entered_time='".date('H:i:s')."',";
				$qryWhere = '';
			}
			
			//die(pre($_SESSION));
			
			/*$dateOfService = "";
			$dosQuery = "select date_of_service from chart_master_table where id = '".$_SESSION['form_id']."'";
			$dosResult = imw_query($dosQuery);
			$dosRow = imw_fetch_array($dosResult);
			$dateOfService = $dosRow['date_of_service'];
			
			// Get appointment from scheuler table for this DOS
			$apptQuery = "select * from schedule_appointments where sa_patient_id='".$_SESSION['patient']."' and sa_app_start_date='".$dateOfService."' and case_type_id='".$insCaseId."'";*/

			$qryEnterChargesQry = $qryEnterChargesQry."patient_charge_list set $encounterIDStr 
			vipStatus='false',submitted='false',collection='false',
			patient_id = '$patient_id', 
			case_type_id='".$insuranceCaseId."', primaryInsuranceCoId='".$primaryInsCoId."', 
			secondaryInsuranceCoId='".$secondaryInsCoId."', tertiaryInsuranceCoId='".$tertiaryInsCoId."',  
			primaryProviderId='".$_REQUEST["provider_id"]."', date_of_service='".$dos_db."',
			totalAmt='$clSupply', approvedTotalAmt='$clSupply',
			amountDue='$clSupply', patientAmt='$totAmt', totalBalance='$clSupply',
			operator_id='".$_SESSION['auth_id']."', auth_no='".$_REQUEST["auth_number"]."',
			auth_amount='".$_REQUEST["auth_amount"]."', cl_print_ord_id='$print_order_id', 
			enc_icd10='$enc_icd10'".$pos_fac_part 
			.$qryWhere;
						
			imw_query($qryEnterChargesQry) or print($qryEnterChargesQry. imw_error().exit());
			if($recordExist==0){
				$charge_list_id = imw_insert_id();
			}
		}

		$add_pchld_dto=",operator_id='".$_SESSION['authId']."',entered_date='".date('Y-m-d H:i:s')."'";	
		$strChldIDS = implode(',',$arrrChldIDS);

		// ADD PRESCRIPTION CL EXAM CHARGES
		if($charge_list_id!=''){
			if($_REQUEST['clws_charges_id']!=''){
				//GET ID OF DEFAULT FEE COLUMN
				$rs=imw_query("Select fee_table_column_id FROM fee_table_column WHERE LOWER(column_name)='default'");
				$res=imw_fetch_array($rs);
				$defaultFeeId = $res['fee_table_column_id'];
				
				$tempArr= explode(',', $_REQUEST['clws_charges_id']);
				for($i=0; $i<sizeof($tempArr); $i++){
					$qryWhere ='';
					$procID= $chld_id = $cpt_fee=  0;
					$dxCode='';
					$dxNum=1;
					$qryEnterChargesQry = 'Insert into ';
					
					$clws_charges_id = $tempArr[$i];
					if($clws_charges_id>0){
						$rs=imw_query("Select cl_charges.cpt_fee_id, diagnosis_code_tbl.dx_code FROM cl_charges LEFT JOIN diagnosis_code_tbl 
						ON diagnosis_code_tbl.diagnosis_id = cl_charges.dx_code_id WHERE cl_charges.cl_charge_id ='".$clws_charges_id."'");
						$res= imw_fetch_array($rs);
						$procID=$res['cpt_fee_id'];
						$dxCode=$res['dx_code'];
						
						if(sizeof($tempArr)>1){	// IF >0 THEN FETCH PARTICULAR CPT FEE
							$rs=imw_query("Select cpt_fee from cpt_fee_table WHERE fee_table_column_id='".$defaultFeeId."' AND cpt_fee_id='".$procID."'");
							$res=imw_fetch_array($rs);
							$cpt_fee= $res['cpt_fee'];
						}else{
							$cpt_fee = $_REQUEST['clExam'];
						}
						
						$rs=imw_query("Select charge_list_detail_id FROM patient_charge_list_details WHERE charge_list_id='".$charge_list_id."' AND procCode='".$procID."' AND charge_list_detail_id NOT IN(".$strChldIDS.") AND del_status='0'");
						if(imw_num_rows($rs)>0){
							$res=imw_fetch_array($rs);
							$chld_id = $res['charge_list_detail_id'];
						}
						if($chld_id>0){ 
							$qryEnterChargesQry = 'Update '; 
							$qryWhere = " where charge_list_detail_id='".$chld_id."'"; 
						}
						
						//ARRAY SETTING FOR DX CODES
						if($dxCode!=''){
							if($chl_diag_arr[$dxCode]){
								$dxNum= $chl_diag_arr[$dxCode];
							}else{
								$dxNum=sizeof($chl_diag_arr)+1;
							}
							$chl_diag_arr[$dxCode]=$dxNum;							
						}
						
						$qryEnterChargesPresQry = $qryEnterChargesQry."patient_charge_list_details set charge_list_id='$charge_list_id',
												patient_id='$patient_id', procCode='".$procID."',start_date='".$dos_db."',place_of_service='".$posId."',
												primaryProviderId='".$_REQUEST["provider_id"]."', procCharges='".$cpt_fee."',
												totalAmount='".$cpt_fee."', balForProc='".$cpt_fee."', diagnosis_id".$dxNum."='".$dxCode."',
												onset_type='ILL', onset_date='".$dos_db."', approvedAmt='".$cpt_fee."',units='1', posFacilityId='".$pos_fac_id."',
												newBalance='".$cpt_fee."'".$add_pchld_dto.$qryWhere;
						imw_query($qryEnterChargesPresQry);
						if($dxCode!=""){
							$diag_code_arr[$dxNum]=$dxCode;
						}
					}
				}
			}
		}
		//echo "<pre>"; print_r($cptCodeOD);
		if($charge_list_id!=''){
			
			$qryEnterChargesQry = 'Insert into ';
			$qryWhere = '';
			$i=0;	$alreadyRecord =0;
			foreach($cptCodeOD as $key =>$value){
				$dxNum=1;
				$qryEnterChargesQry = 'Insert into ';
				$qryWhere = '';	$alreadyRecord =0;	
				$qryRs = imw_query("Select chld_id_od from clprintorder_det WHERE id='".$key."'") or die(imw_error());
				if(imw_num_rows($qryRs) > 0){
					$qryRes = imw_fetch_row($qryRs);
					$chld_id_od = $qryRes[0];
					if($chld_id_od!='' && $chld_id_od!='0'){
						$qryEnterChargesQry = 'Update ';
						$qryWhere = " where charge_list_detail_id='".$chld_id_od."'";
						$alreadyRecord =1;
						$add_pchld_dto="";
					}
				}
				//GET DX COLUMN NAME
				if($Dx_codeOD!=''){
					$dxNum=$chl_diag_arr[$Dx_codeOD];
				}				
				
				$qryEnterChargesODQry = $qryEnterChargesQry."patient_charge_list_details set charge_list_id='$charge_list_id',
										patient_id='$patient_id', procCode='".$value['code']."',start_date='".$dos_db."',place_of_service='".$posId."',
										primaryProviderId='".$_REQUEST["provider_id"]."', procCharges='".$value['PriceOD']."',
										totalAmount='".$value['SubTotalOD']."', balForProc='".$value['SubTotalOD']."', diagnosis_id".$dxNum."='$Dx_codeOD',
										onset_type='ILL', onset_date='".$dos_db."', approvedAmt='".$value['SubTotalOD']."',units='".trim(addslashes($value['QtyOD']))."',
										posFacilityId='".$pos_fac_id."', newBalance='".$value['SubTotalOD']."'".$add_pchld_dto.$qryWhere;
				imw_query($qryEnterChargesODQry);
				if($Dx_codeOD!=""){
					$diag_code_arr[$dxNum]=$Dx_codeOD;
				}
				if($alreadyRecord==0){	
					$chld_id_od = imw_insert_id(); 
					imw_query("update clprintorder_det set chld_id_od='".$chld_id_od."' WHERE id='".$key."'");
				}
				
				//ADDING DISCOUNT IN ACCOUNTING TABLE
				$isDiscount=1;
				$discount_id_od=0;
				if($value["cl_det_dis_idOD"]>0){
					$qryDiscountODQry = "Update paymentswriteoff SET
					write_off_amount='".$value['DiscountOD']."', modified_date='".date('Y-m-d H:i:s')."', 
					modified_by='".$authUserID."' WHERE write_off_id ='".$value['cl_det_dis_idOD']."'";
					imw_query($qryDiscountODQry);
				}else if($value['DiscountOD']>0){
					$qryDiscountODQry = "Insert into paymentswriteoff SET patient_id='".$_REQUEST["patient_id"]."',
					encounter_id='".$encounterIdText."', charge_list_detail_id='".$chld_id_od."',
					write_off_amount='".$value['DiscountOD']."',write_off_operator_id='".$authUserID."',
					entered_date='".date('Y-m-d H:i:s')."', write_off_date='".date('Y-m-d')."', paymentStatus='Discount'";
					imw_query($qryDiscountODQry);
					$discount_id_od = imw_insert_id(); 
				}
				if($discount_id_od>0){
					imw_query("update clprintorder_det SET discount_table_id='".$discount_id_od."' WHERE id='".$key."'");
				}
				
				
			$i++;
			}
		}
		if($charge_list_id!=''){
			$qryEnterChargesQry = 'Insert into ';
			$qryWhere = '';
			$i=0;	$alreadyRecord =0;
			foreach($cptCodeOS as $key =>$value){
				$dxNum=1;
				$qryEnterChargesQry = 'Insert into ';
				$qryWhere = '';	$alreadyRecord =0;			
				$qryRs = imw_query("Select chld_id_os from clprintorder_det WHERE id='".$key."'") or die(imw_error());
				if(imw_num_rows($qryRs) > 0){
					$qryRes = imw_fetch_row($qryRs);
					$chld_id_os = $qryRes[0];
					if($chld_id_os!='' && $chld_id_os!='0'){					
						$qryEnterChargesQry = 'Update ';
						$qryWhere = " where charge_list_detail_id='".$chld_id_os."'";
						$alreadyRecord =1;
						$add_pchld_dto="";
					}
				}

				//GET DX COLUMN NAME
				if($Dx_codeOS!=''){
					$dxNum=$chl_diag_arr[$Dx_codeOS];
				}				
				$qryEnterChargesOSQry = $qryEnterChargesQry."patient_charge_list_details set charge_list_id='$charge_list_id',
										patient_id='$patient_id', procCode='".$value['code']."',start_date='".$dos_db."',place_of_service='".$posId."',
										primaryProviderId='".$_REQUEST["provider_id"]."', procCharges='".$value['PriceOS']."',
										totalAmount='".$value['SubTotalOS']."', balForProc='".$value['SubTotalOS']."', diagnosis_id".$dxNum."='$Dx_codeOS',
										onset_type='ILL', onset_date='".$dos_db."', approvedAmt='".$value['SubTotalOS']."',units='".trim(addslashes($value['QtyOS']))."' ,
										posFacilityId='".$pos_fac_id."', newBalance='".$value['SubTotalOS']."'".$add_pchld_dto.$qryWhere;
				imw_query($qryEnterChargesOSQry);
				if($Dx_codeOS!=""){
					$diag_code_arr[$dxNum]=$Dx_codeOS;
				}
				if($alreadyRecord==0){	
					$chld_id_os = imw_insert_id(); 
					imw_query("update clprintorder_det set chld_id_os='".$chld_id_os."' WHERE id='".$key."'");
				}
				
				//ADDING DISCOUNT IN ACCOUNTING TABLE
				$isDiscount=1;
				$discount_id_os=0;
				if($value["cl_det_dis_idOS"]>0){
					$qryDiscountOSQry = "Update paymentswriteoff SET
					write_off_amount='".$value['DiscountOS']."', modified_date='".date('Y-m-d H:i:s')."', 
					modified_by='".$authUserID."' WHERE write_off_id ='".$value['cl_det_dis_idOS']."'";
					imw_query($qryDiscountOSQry);
				}else if($value['DiscountOS']>0){
					$qryDiscountOSQry = "Insert into paymentswriteoff SET patient_id='".$_REQUEST["patient_id"]."',
					encounter_id='".$encounterIdText."', charge_list_detail_id='".$chld_id_os."',
					write_off_amount='".$value['DiscountOS']."',write_off_operator_id='".$authUserID."',
					entered_date='".date('Y-m-d H:i:s')."', write_off_date='".date('Y-m-d')."', paymentStatus='Discount'";
					imw_query($qryDiscountOSQry);
					$discount_id_os = imw_insert_id(); 
				}
				if($discount_id_os>0){
					imw_query("update clprintorder_det SET discount_table_id='".$discount_id_os."' WHERE id='".$key."'");
				}
				
			$i++;
			}
		}
		
		if($charge_list_id!=''){
			$arrDxCodes=array();
			$str_dx_codes="";
			for($i=1;$i<=12;$i++){
				$ci=$i;
				$arrDxCodes[$i]=$diag_code_arr[$ci];
			}
		
			$str_dx_codes=serialize($arrDxCodes);
			$update_chl="update patient_charge_list set 
							all_dx_codes='".imw_real_escape_string($str_dx_codes)."'
							where charge_list_id='$charge_list_id'";
							
			$update_chl_run=imw_query($update_chl);
		}
			if($encounterIdText>0) { $_SESSION['encounter_id']=''; }
			patient_proc_bal_update($encounterIdText);
			$_SESSION['encounterIdFromCL'] = $encounterIdText;
			// CHECK WHERE CPT FOR OD OR OD NOT EXISTS
			$notCPT ='';
			if(sizeof($arrNotCPT)>0){
				$notCPT='CPT Code for Following Contact Lens Styles not found in Database.\n';
				if($charge_list_id==''){ $notCPT.='No encounter has made for this order.\n\n'; }
				$notCPT.= implode(',\n',$arrNotCPT);
				$notCPT.='\n\n\n';
			}
			
			//Check whether to rediret to acc. or not
			$enter_charge_chk = (isset($_REQUEST['enterCharges']) && empty($_REQUEST['enterCharges']) == false) ? $_REQUEST['enterCharges'] : '';
/*			if($charge_list_id=='' && empty($enter_charge_chk) == false){ 
				$alert_str .= 'alert("'.$notCPT.' Can not redirect to accounting.");';
			}else{
				if($_REQUEST['callFrom']=='order'){	
					$alert_str .= 'opener.redirectToEnterCharges("'.$_REQUEST["patient_id"].'", "'.$encounterIdText.'")';
					$alert_str .= 'window.close()';
					echo '<script type="text/javascript">'+$alert_str.'</script>';
				}else{
					$alert_str .= 'alert("'.$notCPT.' Redirecting to Enter Charges.");';
					$alert_str .= 'var send_url ="../accounting/accountingTabs.php?flagSetPid=true&tab=enterCharges";
					var redirect_tab =  $("#AccountingEC",window.opener.parent.opener.parent.document).get(0);
					window.opener.parent.opener.parent.top.change_main_Selection(redirect_tab);
					window.close();';
				}
			} */
			?>
            
			<script type="text/javascript">
			
				'<?php if($charge_list_id=='' && empty($enter_charge_chk) == false){  ?>'
					alert('<?php echo $notCPT;?>Can not redirect to accounting.');
				'<?php } else{ ?>'
					'<?php	if($_REQUEST['callFrom']=='order'){?>'
								alert('<?php echo $notCPT;?>Redirecting to accounting.');								
								opener.redirectToEnterCharges('<?php echo $_REQUEST["patient_id"];?>','<?php echo $encounterIdText;?>');
								window.close();
					'<?php	}else{?>'
								alert('<?php echo $notCPT;?>Redirecting to accounting.');
								var eid='<?php echo $encounterIdText;?>';
								var send_url ='../accounting/accounting_view.php?encounter_id='+eid+'&uniqueurl='+eid+'&del_charge_list_id=0&tabvalue=Enter_Charges&show_load=yes';
								//var send_url ="../accounting/accountingTabs.php?flagSetPid=true&tab=enterCharges";
								window.opener.parent.core_redirect_to("Accounting", send_url);
								window.close();
					'<?php	}
					 } ?>'
					
			</script> 
                       
            <?php 
	}
}
// ---- END SAVE RECORD --------

//FETCH RECORD FROM SAVED CLPRINT ORDER TABLE
	if($print_order_id) {
		$GetPrintDataQuery= "SELECT clprintorder_master.*, clprintorder_master.clws_id as 'CLWSID', clprintorder_master.print_order_id as 'PRINTORDERID', clprintorder_det.*, clprintorder_det.id as 'orDetId' FROM clprintorder_master 
		LEFT JOIN clprintorder_det ON clprintorder_det.print_order_id = clprintorder_master.print_order_id 
		WHERE clprintorder_master.print_order_id='".$print_order_id."' ORDER BY clprintorder_det.id";

		$clOrderRes = mysqlifetchdata($GetPrintDataQuery);
		$GetPrintDataNumRow = sizeof($clOrderRes);

		
		$clTeachDate = $clOrderRes[0]['clTeachDate'];
		$clEvaluationDate = $clOrderRes[0]['clEvaluationDate'];
		$clFittingDate = $clOrderRes[0]['clFittingDate'];
		$messageTo = $clOrderRes[0]['messageTo'];
		$patientInsuranceType = $clOrderRes[0]['ins_case'];
		$auth_number = $clOrderRes[0]['auth_number'];
		$auth_amount = $clOrderRes[0]['auth_amount'];
		$disc_amount = $clOrderRes[0]['disc_amount'];
		
		/*********** Patient authorization from patient_auth table **********/
		$insuranceCaseType = 0;
		$insuranceAuthorizationNumber = 0;
		$insuranceAuthorizationAmount = 0;
		$insuranceAuthorizationDiscountAmount = 0;
		
		$patientAuthQuery = "select ins_type, auth_name, AuthAmount from patient_auth where patient_id='".$_REQUEST["patient_id"]."' order by patient_id DESC limit 1";
		$patientAuthResult = imw_query($patientAuthQuery);
		$patientAuthRow = imw_fetch_array($patientAuthResult);
		$insuranceCaseType = $patientAuthRow['ins_type'];
		$insuranceAuthorizationNumber = $patientAuthRow['auth_name'];
		$insuranceAuthorizationAmount = $patientAuthRow['AuthAmount'];
		/*********** Patient authorization from patient_auth table ends **********/
		
		$dos = $clOrderRes[0]['dos'];
		
		$j= $k= $m =0; 
		for($i=0; $i< $GetPrintDataNumRow; $i++)
		{
			if($clOrderRes[$i]['PriceOD']!='')
			{
				$price=($clOrderRes[$i]['PriceOD']>0)? $clOrderRes[$i]['PriceOD'] :$clPriceArray[$clOrderRes[$i]['LensBoxOD_ID']];

				$LensBoxOD[$j] = $arrLensManuf[$clOrderRes[$i]['LensBoxOD_ID']]['det'];
				$arrPrintOD[$j]['LensBoxOD_ID'] = $clOrderRes[$i]['LensBoxOD_ID'];
				$PriceOD[$j] = $price;
				$clwsid_ArrOD[$j] = $clOrderRes[$i]['cl_det_id'];
				$arrPrintOD[$j]['orDetId'] = $clOrderRes[$i]['orDetId'];
				$arrPrintOD[$j]['chldIdOD'] = $clOrderRes[$i]['chld_id_od'];
				$arrPrintOD[$j]['code'] = $clOrderRes[$i]['lensNameIdList'];
				$arrPrintOD[$j]['color'] = $clOrderRes[$i]['colorNameIdList'];
				$arrPrintOD[$j]['price'] = $price;				
				$arrPrintOD[$j]['qty'] = $clOrderRes[$i]['QtyOD'];
				$arrPrintOD[$j]['subTotal'] = $clOrderRes[$i]['SubTotalOD'];
				$arrPrintOD[$j]['discount'] = $clOrderRes[$i]['DiscountOD'];
				$arrPrintOD[$j]['total'] = $clOrderRes[$i]['TotalOD'];
				$arrPrintOD[$j]['insurance'] = $clOrderRes[$i]['InsOD'];
				$arrPrintOD[$j]['balance'] = $clOrderRes[$i]['BalanceOD'];
				$arrPrintOD[$j]['discount_table_id'] = $clOrderRes[$i]['discount_table_id'];
				$j++;
			}
			if($clOrderRes[$i]['PriceOS']!='')
			{
				$price=($clOrderRes[$i]['PriceOS']>0)? $clOrderRes[$i]['PriceOS'] :$clPriceArray[$clOrderRes[$i]['LensBoxOS_ID']];

				$LensBoxOS[$k] = $arrLensManuf[$clOrderRes[$i]['LensBoxOS_ID']]['det'];
				$arrPrintOS[$k]['LensBoxOS_ID'] = $clOrderRes[$i]['LensBoxOS_ID'];
				$PriceOS[$k] = $price;
				$clwsid_ArrOS[$k] = $clOrderRes[$i]['cl_det_id'];
				$arrPrintOS[$k]['orDetId'] = $clOrderRes[$i]['orDetId'];
				$arrPrintOS[$k]['chldIdOS'] = $clOrderRes[$i]['chld_id_os'];
				$arrPrintOS[$k]['code'] = $clOrderRes[$i]['lensNameIdListOS'];
				$arrPrintOS[$k]['color'] = $clOrderRes[$i]['colorNameIdListOS'];
				$arrPrintOS[$k]['price'] = $price;
				$arrPrintOS[$k]['qty'] = $clOrderRes[$i]['QtyOS'];
				$arrPrintOS[$k]['subTotal'] = $clOrderRes[$i]['SubTotalOS'];
				$arrPrintOS[$k]['discount'] = $clOrderRes[$i]['DiscountOS'];
				$arrPrintOS[$k]['total'] = $clOrderRes[$i]['TotalOS'];
				$arrPrintOS[$k]['insurance'] = $clOrderRes[$i]['InsOS'];
				$arrPrintOS[$k]['balance'] = $clOrderRes[$i]['BalanceOS'];
				$arrPrintOS[$k]['discount_table_id'] = $clOrderRes[$i]['discount_table_id'];
				$k++;
			}
		}
	}

	if($_REQUEST['clws_id'] !=''){ 
		$clws_id = $_REQUEST['clws_id'];
	}else{
		$clws_id = $clOrderRes[0]['CLWSID'];
	}
	
	//GETTING PROVIDER ID FROM RX TABLE
	$rx_done_by=$rx_user_type='';
	if($clws_id>0){
		$rs=imw_query("Select provider_id FROM contactlensmaster WHERE clws_id='".$clws_id."'");   
		$res=imw_fetch_assoc($rs);
		$rx_done_by=$res['provider_id'];
		
		if($rx_done_by>0){
			$qry = "SELECT user_type from users where id = '".$rx_done_by."'";
			$rs = imw_query($qry);
			$res = imw_fetch_array($rs);
			$rx_user_type= $res['user_type'];
		}
	}
	
//START SET DOCTOR-ID, TECHNICIAN-ID, OPERATOR-ID TO SAVE IN DATABASE
	$operator_id = $authUserID;
	if($rx_done_by>0){
		if(in_array($rx_user_type,$GLOBALS['arrValidCNPhy'])) {
			$provider_id=$rx_done_by;
		}
		if(in_array($rx_user_type,$GLOBALS['arrValidCNTech'])) {
			$technician_id=$rx_done_by;
		}
	}else{
		if(in_array($row_user['user_type'],$GLOBALS['arrValidCNPhy'])) {
			$provider_id=$authUserID;
		}
		if(in_array($row_user['user_type'],$GLOBALS['arrValidCNTech'])) {
			$technician_id=$authUserID;
		}
	}
	//IF PROVIDER ID IS STILL BLANK THEN GET IT FROM OTHER SOURCES
	if(empty($provider_id)==true){
		$qry="Select sa_doctor_id FROM schedule_appointments WHERE sa_patient_id='".$_SESSION['patient']."' AND sa_app_start_date='".date('Y-m-d')."' ORDER BY id ASC LIMIT 0,1";
		$rs=imw_query($qry);
		$res=imw_fetch_assoc($rs);
		$provider_id=$res['sa_doctor_id'];
			
		if(empty($provider_id)==true){
			$qry="Select providerID FROM patient_data WHERE sa_patient_id='".$_SESSION['patient']."'";
			$rs=imw_query($qry);
			$res=imw_fetch_assoc($rs);
			$provider_id=$res['providerID'];			
		}			
	}
//END SET DOCTOR-ID, TECHNICIAN-ID, OPERATOR-ID TO SAVE IN DATABASE
	
	if(!$print_order_id) {
		$GetClTeachQuery	= "SELECT saveDateTime FROM clteach WHERE workSheetID='".$clws_id."' AND workSheetID!='0'";
		$GetClTeachRes 		= imw_query($GetClTeachQuery) or die(imw_error());				
		$GetClTeachNumRow 	= imw_num_rows($GetClTeachRes);
		if($GetClTeachNumRow>0){
			$GetClTeachRow	=imw_fetch_array($GetClTeachRes);
			
			$clTeachDate = $GetClTeachRow['saveDateTime'];
		}
		
		$GetClEvaluationQuery	= "SELECT * FROM contactlensmaster WHERE patient_id='".$_SESSION['patient']."' AND dos='".$dos."' AND clws_type in('Evaluation','Fit','Refit')";
		$GetClEvaluationRes 	= imw_query($GetClEvaluationQuery) or die(imw_error());				
		$GetClEvaluationNumRow 	= imw_num_rows($GetClEvaluationRes);
		if($GetClEvaluationNumRow>0){
			$GetClEvaluationRow	=imw_fetch_array($GetClEvaluationRes);
			
			$clEvaluationDate = $GetClEvaluationRow['clws_savedatetime'];
			$clFittingDate 	  = $GetClEvaluationRow['clws_savedatetime'];
		}
	}
	
//END CODE TO VIEW RECORD

//START CODE TO GET TYPE MANUFACTURER
$GetTypeManufacQuery= "
SELECT clm.clws_id, clm.dos, clm.clGrp, clm.clws_type, clm.cpt_evaluation_fit_refit, clm.charges_id, 
clw.id, clw.clEye, clw.clType, 
clw.SclTypeOD, clw.SclTypeOD_ID, clw.RgpTypeOD, clw.RgpTypeOD_ID,  clw.RgpCustomTypeOD, clw.RgpCustomTypeOD_ID, 
clw.SclTypeOS, clw.SclTypeOS_ID, clw.RgpTypeOS, clw.RgpTypeOS_ID, clw.RgpCustomTypeOS, clw.RgpCustomTypeOS_ID 
FROM contactlensmaster clm
LEFT JOIN contactlensworksheet_det clw ON clw.clws_id = clm.clws_id 
WHERE clm.patient_id='".$_SESSION['patient']."' 
AND clm.clws_id='".$clws_id."' ORDER BY clw.id";

$GetTypeManufacRow = mysqlifetchdata($GetTypeManufacQuery);
$GetTypeManufacNumRow 	= sizeof($GetTypeManufacRow);

$clws_type = $GetTypeManufacRow[0]['clws_type'];

$typeManufac='';
$commaSepManuFac='';
$arrRxForOrder=array();

if($GetTypeManufacNumRow>0){
	$od=$os=0;
	for($i=0;$i < $GetTypeManufacNumRow; $i++)
	{
		$typeOD = $typeOS = $typeOU= '';
		$priceOD = $priceOS = $priceOU = '';
		$commaSepManuFac = '';
	
		$dos=$GetTypeManufacRow[$i]['dos'];
		$clID = $GetTypeManufacRow[$i]['id'];
		$adminStylOD = $adminManufacOD = $adminStylOS = $adminManufacOS =$adminStylOU = $adminManufacOU ='';
		 
		$clGrp	  = trim(stripslashes($GetTypeManufacRow[$i]['clGrp']));
		$clType	  = trim(stripslashes($GetTypeManufacRow[$i]['clType']));
		$clEye 	  = trim(stripslashes($GetTypeManufacRow[$i]['clEye']));
		
		if($clType=='scl'){
			$typeOD_ID = $GetTypeManufacRow[$i]['SclTypeOD_ID'];
			$typeOS_ID = $GetTypeManufacRow[$i]['SclTypeOS_ID'];
			$typeOD	  = trim(stripslashes($arrLensManuf[$typeOD_ID]['det']));
			$typeOS	  = trim(stripslashes($arrLensManuf[$typeOS_ID]['det']));
			$cpt_fee_id = $arrLensManuf[$typeOD_ID]['cpt_fee_id'];
			$priceOD  = $arrDefaultCPTFee[$cpt_fee_id];
			$cpt_fee_id = $arrLensManuf[$typeOS_ID]['cpt_fee_id'];
			$priceOS  = $arrDefaultCPTFee[$cpt_fee_id];
		}elseif($clType=='rgp'){
			$typeOD_ID = $GetTypeManufacRow[$i]['RgpTypeOD_ID'];
			$typeOS_ID = $GetTypeManufacRow[$i]['RgpTypeOS_ID'];
			$typeOD	  = trim(stripslashes($arrLensManuf[$typeOD_ID]['det']));
			$typeOS	  = trim(stripslashes($arrLensManuf[$typeOS_ID]['det']));
			$cpt_fee_id = $arrLensManuf[$typeOD_ID]['cpt_fee_id'];
			$priceOD  = $arrDefaultCPTFee[$cpt_fee_id];
			$cpt_fee_id = $arrLensManuf[$typeOS_ID]['cpt_fee_id'];
			$priceOS  = $arrDefaultCPTFee[$cpt_fee_id];
		}elseif($clType=='cust_rgp'){
			$typeOD_ID = $GetTypeManufacRow[$i]['RgpCustomTypeOD_ID'];
			$typeOS_ID = $GetTypeManufacRow[$i]['RgpCustomTypeOS_ID'];
			$typeOD	  = trim(stripslashes($arrLensManuf[$typeOD_ID]['det']));
			$typeOS	  = trim(stripslashes($arrLensManuf[$typeOS_ID]['det']));
			$cpt_fee_id = $arrLensManuf[$typeOD_ID]['cpt_fee_id'];
			$priceOD  = $arrDefaultCPTFee[$cpt_fee_id];
			$cpt_fee_id = $arrLensManuf[$typeOS_ID]['cpt_fee_id'];
			$priceOS  = $arrDefaultCPTFee[$cpt_fee_id];
		}
		
		if($typeOD && $typeOS) {
			$commaSepManuFac = ', ';
		}
		
		if($typeOD != $typeOS){
			if($typeOD!='' && ($clGrp=='OU' || $clGrp=='OD')) {
				$typeManufac.='OD - '.$typeOD;
				if(!$print_order_id){
					$LensBoxOD[]=$typeOD;
					$PriceOD[] = $priceOD;
					$arrPrintOD[$od]['LensBoxOD_ID']= $typeOD_ID;
					$arrPrintOD[$od]['price']= $priceOD;
					$clwsid_ArrOD[] = $clID;
					$od++;
				}
			}
			$typeManufac.=$commaSepManuFac;

			if($typeOS!='' && ($clGrp=='OU' || $clGrp=='OS')) {
				$typeManufac.='OS - '.$typeOS;
				list($adminStylOS,$adminManufacOS) = explode('-',$typeOS);
				if(!$print_order_id){
					$LensBoxOS[]=$typeOS;
					$PriceOS[] = $priceOS;
					$arrPrintOS[]['LensBoxOS_ID']= $typeOS_ID;
					$arrPrintOS[$os]['price']= $priceOS;
					$clwsid_ArrOS[] = $clID;
					$os++;
				}
			}
			$typeManufac.=$commaSepManuFac;
		}
		
		if($typeOD==$typeOS && ($typeOD!='' || $typeOS!='') && $clGrp=='OU') {  
			$typeManufac.='OD - '.$typeOD;
			if(!$print_order_id){
				if($typeOS!=''){
					$LensBoxOS[]=$typeOS;
					$PriceOS[] = $priceOS;
					$arrPrintOS[]['LensBoxOS_ID']= $typeOS_ID;
					$arrPrintOS[$os]['price']= $priceOS;
					$clwsid_ArrOS[] = $clID;
					$os++;
				}
				if($typeOD!=''){
					$LensBoxOD[]=$typeOD;
					$PriceOD[] = $priceOD;
					$arrPrintOD[]['LensBoxOD_ID']= $typeOD_ID;
					$arrPrintOD[$od]['price']= $priceOD;
					$clwsid_ArrOD[] = $clID;
					$od++;
				}
			}
			$typeManufac.=$commaSepManuFac;
		}
		$typeManufac.=" -- ";
	}
	$typeManufac=substr($typeManufac,0, strlen($typeManufac)-4);
}
//END CODE TO GET TYPE MNUFACTURER

$physicianOptions = $OBJCommonFunction->drop_down_providers($messageTo,'','');

$statusOptions=array('Pending', 'Ordered', 'Received', 'Dispensed');
?>
<!DOCTYPE html>
<html>
<head>
<title>CONTACT LENS Print Order</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<?php /*<link href="css_accounting.php" type="text/css" rel="stylesheet">*/ ?>
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery-ui.min.css" type="text/css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.min.css" type="text/css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap-select.css" type="text/css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css" type="text/css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/accounting.css" type="text/css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery.datetimepicker.min.css" type="text/css" rel="stylesheet">
<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'] ?>/library/messi/messi.css">
<?php /* HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries */ ?>
<?php /* WARNING: Respond.js doesn't work if you view the page via file:// */ ?>
<?php /*[if lt IE 9]>
  <script src="<?php echo $GLOBALS['webroot'];?>/library/js/html5shiv.min.js"></script>
  <script src="<?php echo $GLOBALS['webroot'];?>/library/js/respond.min.js"></script>
		<![endif]*/ ?>


<style>
	.la_sel_lts{
	background:url(images/la_sel_left.png); background-repeat:no-repeat; height:25px;width:7px;
	}
	.la_sel_mds2{
	}
	.la_sel_mds{ background:#999999;}
	.la_sel_rts{ background-image:url(images/la_sel_right.png); background-repeat:no-repeat; height:25px;width:7px;}
	.la_bg2{
		background-color:#3F7696;
		background-attachment: scroll;
		background-repeat: repeat-x;
		background-position: left;
	}
.table{margin-bottom:0px}	
.od{color:blue;}
.os{color:green;}
.ou{color:#9900CC;}	
.purple_bar [class*="col-"] {padding-top:0px!important; line-height: 22px }	
.contlns [class*="col-"]{padding:3px 0px; }

	.purple_bar { padding: 6px 0 0!important;}
	.glyphicon{ cursor:pointer;	}
</style>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.min.1.12.4.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery-ui.min.1.11.2.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/bootstrap-typeahead.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/bootstrap-select.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.datetimepicker.full.min.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/core_main.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/common.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/sc_script.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/messi/messi.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/work_view/typeahead.js"></script>
<script type="text/javascript">
var firstTime = 1;
var cptFee = [];
<?php
	$cptFeeQuery = "SELECT DISTINCT clm.make_id as makeId, cft.cpt_fee as fee FROM contactlensemake clm INNER JOIN cpt_fee_table cft ON clm.cpt_fee_id = cft.cpt_fee_id";
	$result = imw_query($cptFeeQuery);
	while($row = imw_fetch_array($result)){
?>
		cptFee[<?php echo $row['makeId']; ?>] = <?php echo $row['fee']; ?>
<?php
        echo "\n";
	}
?>

date_format = "<?php echo inter_date_format();?>";

var global_date_format = "<?php echo phpDateFormat(); ?>";
var arrManufac=[];
var arrManufacId=[];
var arrCPTFee=[];
var clPriceArrayJS = {};
arrManufac = <?php echo json_encode($arrManufac); ?>;
arrManufacId = <?php echo json_encode($arrManufacId); ?>;
arrCPTFee = <?php echo json_encode($arrCPTFee); ?>;

function dgi(obj){
	return document.getElementById(obj);
}


function checkSingle(elemId,grpName)
{
	var obgrp = document.getElementsByName(grpName);
	var objele = document.getElementById(elemId);
	var len = obgrp.length;		
	if(objele.checked == true)
	{		
		for(var i=0;i<len;i++)
		{
			if((obgrp[i].id != objele.id) && (obgrp[i].checked == true) )
			{
				obgrp[i].checked=false;
			}
		}	
	}
}

function hideDisplayOrderFn(orderTrialDspDoctorId,orderTrialId,orderTrialDspNumberId,orderTrialNumberId) {
	if(document.getElementById(orderTrialDspNumberId)) {
		document.getElementById(orderTrialDspNumberId).disabled=true;
	}
	if(document.getElementById(orderTrialNumberId)) {
		document.getElementById(orderTrialNumberId).disabled=true;
	}
	if(document.getElementById(orderTrialDspDoctorId)) {
		if(document.getElementById(orderTrialDspDoctorId).checked==true) {
			document.getElementById(orderTrialDspNumberId).disabled=false;
		}
	}
	if(document.getElementById(orderTrialId)) {
		if(document.getElementById(orderTrialId).checked==true) {
			document.getElementById(orderTrialNumberId).disabled=false;
		}
	}
}


function checkdate(objName,type){
	type=type||'';
	if(validate_date(objName,type)==false&&objName.value!=''){
		objName.value="";objName.focus();
	if(typeof(opener)=='undefined'&&typeof(top.opener)=='undefined'){
		top.fAlert('Please enter a valid Date');
		top.document.getElementById("divCommonAlertMsg").style.display="block";
	}else{
		top.fAlert('Please enter a valid Date');
	}
		return false;
	}
	else{
		return true;
	}
}

function checkDateCorrectness(objElem)
{	
	var valElem = objElem.value;
	if(valElem == ""){
		return;
	}
	
	if(checkdate(objElem))
	ifValid = true;
	else ifValid = null;
	
	if(ifValid == null)
	{
		//alert("Please enter date in correct format.\n(mm-dd-yyyy)");			
		objElem.value = objElem.defaultValue;
		objElem.select();
		objElem.focus();
		return false;
	}
	//objElem.value = valElem;
	return true;
}

function chkDateFrmat(obj)
	{
		var retVal = false;
		if(checkDateCorrectness(obj) == true)
		{		
			retVal = true;
		}
		return retVal;
	}
	
function justify2Decimal(objElem){
	var valElem = objElem.value;
	if(!isNaN(valElem)){
		var ptrn = "^[\+|\-]";
		var reg = new RegExp(ptrn,'g');
		var sign = valElem.match(reg);		
		var unJustNumber = (sign == null) ? valElem : valElem.substr(1);				
		var justNumber = (unJustNumber == "") ? "" : parseFloat(unJustNumber).toFixed(2);		
		objElem.value = (sign == null) ? justNumber : sign+justNumber;		
	}
	else{ 	
		objElem.value = valElem;
	}

	if(objElem.value < 0){
		objElem.value = 0;
	}	
}	
function calcTotalBalODFn(i,objElem) {
	if(!parseFloat(dgi('PriceOD'+i).value)) {
		dgi('PriceOD'+i).value=0;	
	}
	if(!parseFloat(dgi('QtyOD'+i).value)) {
		dgi('QtyOD'+i).value=0;	
	}
	if(!parseFloat(dgi('SubTotalOD'+i).value)) {
		dgi('SubTotalOD'+i).value=0;	
	}
	if(!parseFloat(dgi('DiscountOD'+i).value)) {
		dgi('DiscountOD'+i).value=0;	
	}
	if(!parseFloat(dgi('TotalOD'+i).value)) {
		dgi('TotalOD'+i).value=0;	
	}
	if(!parseFloat(dgi('InsOD'+i).value)) {
		dgi('InsOD'+i).value=0;	
	}
	if(!parseFloat(dgi('BalanceOD'+i).value)) {
		dgi('BalanceOD'+i).value=0;	
	}
	
	var PriceOD 				= parseFloat(dgi('PriceOD'+i).value);
	var QtyOD 					= parseFloat(dgi('QtyOD'+i).value);
	var SubTotalOD 				= parseFloat(dgi('SubTotalOD'+i).value);
	var DiscountOD 				= parseFloat(dgi('DiscountOD'+i).value);
	var TotalOD 				= parseFloat(dgi('TotalOD'+i).value);
	var InsOD 					= parseFloat(dgi('InsOD'+i).value);
	var BalanceOD 				= parseFloat(dgi('BalanceOD'+i).value);
	var SubTotalODValue			='';
	var TotalODValue			='';
	var BalanceODValue 			='';
	var TotalBalanceODOSValue	='';
	//if(PriceOD && QtyOD) {
		SubTotalODValue = PriceOD*QtyOD;
		dgi('SubTotalOD'+i).value=SubTotalODValue;
	//}

	var discVal=dgi('DiscountOD'+i).value;
	var sign = discVal.substr(discVal.length-1);
	if(sign=='%'){
		var percVal = discVal.substr(0, discVal.length-1);
		percDiscVal = (SubTotalODValue * percVal) / 100;
		TotalODValue = parseFloat(dgi('SubTotalOD'+i).value)-percDiscVal;
	}else{
		TotalODValue = parseFloat(dgi('SubTotalOD'+i).value)-parseFloat(dgi('DiscountOD'+i).value);		
	}
	
	dgi('TotalOD'+i).value=TotalODValue;
	BalanceODValue = parseFloat(dgi('TotalOD'+i).value);
	dgi('BalanceOD'+i).value=BalanceODValue;
	
	var discVal=dgi('InsOD'+i).value;
	var sign = discVal.substr(discVal.length-1);
	var insDiscVal=0;
	if(sign=='%'){
		var percVal = discVal.substr(0, discVal.length-1);
		var percDiscVal = (TotalODValue * percVal) / 100;
		var BalanceODValue = parseFloat(dgi('TotalOD'+i).value)-percDiscVal;
	}else{
		var BalanceODValue = parseFloat(dgi('TotalOD'+i).value)-parseFloat(dgi('InsOD'+i).value);		
	}
	dgi('BalanceOD'+i).value=BalanceODValue;
	//START CODE TO CLCULATE TOTAL BALANCE CHARGES OF OD AND OS AND OU
	if(!parseFloat(dgi('BalanceOD'+i).value)) { dgi('BalanceOD'+i).value=0;}
	if(dgi('BalanceOS'+i)) {
		if(!parseFloat(dgi('BalanceOS'+i).value)) { dgi('BalanceOS'+i).value=0;}
	}
	
	//START CODE TO SET DECIMAL VALUE
	justify2Decimal(dgi('SubTotalOD'+i));
	justify2Decimal(dgi('TotalOD'+i));
	justify2Decimal(dgi('BalanceOD'+i));
	//END CODE TO SET DECIMAL VALUE

	// CALCULATE GRAND TOTAL
	var ODLen = dgi('txtTotOD').value;
	var ODTot = 0;
	$('[id^=BalanceOD]').each(function(id,elem){
		var odVal = $(elem).val();
			if(odVal=='') { odVal=0; }
			ODTot = (parseFloat(ODTot)) + (parseFloat(odVal));
	});
	
	// CALCULATE GRAND TOTAL
	var OSLen = dgi('txtTotOS').value;
	var OSTot = 0;
	
	$('[id^=BalanceOS]').each(function(id,elem){
		var osVal = $(elem).val();
		if(osVal=='') { osVal=0; }
		OSTot = (parseFloat(OSTot)) + (parseFloat(osVal));
	});
	
		if(OSTot=='') { OSTot=0; }
		if(ODTot=='') { ODTot=0; }

		clSupplyValue = parseFloat(ODTot)+parseFloat(OSTot);
		dgi('clSupply').value=clSupplyValue;
		var clExam = dgi('clExam').value;
		
		if(clSupplyValue=='') { clSupplyValue=0; }
		if(clExam=='') { clExam=0; }
		
		dgi('totalCharges').value = parseFloat(clExam)+parseFloat(clSupplyValue);
		
		justify2Decimal(dgi('clExam'));
		justify2Decimal(dgi('clSupply'));
		justify2Decimal(dgi('totalCharges'));
}
function calcTotalBalOSFn(i) {
	
	if(!parseFloat(dgi('PriceOS'+i).value)) {
		dgi('PriceOS'+i).value=0;	
	}
	if(!parseFloat(dgi('QtyOS'+i).value)) {
		dgi('QtyOS'+i).value=0;	
	}
	if(!parseFloat(dgi('SubTotalOS'+i).value)) {
		dgi('SubTotalOS'+i).value=0;	
	}
	if(!parseFloat(dgi('DiscountOS'+i).value)) {
		dgi('DiscountOS'+i).value=0;	
	}

	if(!parseFloat(dgi('TotalOS'+i).value)) {
		dgi('TotalOS'+i).value=0;	
	}

	if(!parseFloat(dgi('InsOS'+i).value)) {
		dgi('InsOS'+i).value=0;	
	}
	if(!parseFloat(dgi('BalanceOS'+i).value)) {
		dgi('BalanceOS'+i).value=0;	
	}

	var PriceOS 				= parseFloat(dgi('PriceOS'+i).value);
	var QtyOS 					= parseFloat(dgi('QtyOS'+i).value);
	var SubTotalOS 				= parseFloat(dgi('SubTotalOS'+i).value);
	var DiscountOS 				= parseFloat(dgi('DiscountOS'+i).value);
	var TotalOS 				= parseFloat(dgi('TotalOS'+i).value);
	var InsOS 					= parseFloat(dgi('InsOS'+i).value);
	var BalanceOS 				= parseFloat(dgi('BalanceOS'+i).value);
	var SubTotalOSValue			= '';
	var TotalOSValue			= '';
	var BalanceOSValue 			= '';
	var TotalBalanceODOSValue 	= '';
	//if(PriceOS && QtyOS) {
		SubTotalOSValue = PriceOS*QtyOS;
		dgi('SubTotalOS'+i).value=SubTotalOSValue;
	//}

	var discVal=dgi('DiscountOS'+i).value;
	var sign = discVal.substr(discVal.length-1);
	
	if(sign=='%'){
		var percVal = discVal.substr(0, discVal.length-1);
		percDiscVal = (SubTotalOSValue * percVal) / 100;
		TotalOSValue = parseFloat(dgi('SubTotalOS'+i).value)-percDiscVal;
	}else{
		TotalOSValue = parseFloat(dgi('SubTotalOS'+i).value)-parseFloat(dgi('DiscountOS'+i).value);		
	}	

	dgi('TotalOS'+i).value=TotalOSValue;
	BalanceOSValue = parseFloat(dgi('TotalOS'+i).value);
	dgi('BalanceOS'+i).value=BalanceOSValue;

	var discVal=dgi('InsOS'+i).value;
	var sign = discVal.substr(discVal.length-1);
	var insDiscVal=0;
	if(sign=='%'){
		var percVal = discVal.substr(0, discVal.length-1);
		var percDiscVal = (TotalOSValue * percVal) / 100;
		var BalanceOSValue = parseFloat(dgi('TotalOS'+i).value)-percDiscVal;
	}else{
		var BalanceOSValue = parseFloat(dgi('TotalOS'+i).value)-parseFloat(dgi('InsOS'+i).value);
	}
	dgi('BalanceOS'+i).value=BalanceOSValue;


	//START CODE TO CLCULATE TOTAL BALANCE CHARGES OF OD AND OS OU
	if(dgi('BalanceOD'+i)){
		if(!parseFloat(dgi('BalanceOD'+i).value)) { dgi('BalanceOD'+i).value=0;}
	}

	if(!parseFloat(dgi('BalanceOS'+i).value)) { dgi('BalanceOS'+i).value=0;}

	//START CODE TO SET DECIMAL VALUE
	justify2Decimal(dgi('SubTotalOS'+i));
	justify2Decimal(dgi('TotalOS'+i));
	justify2Decimal(dgi('BalanceOS'+i));
	//END CODE TO SET DECIMAL VALUE

	// CALCULATE GRAND TOTAL
	var ODLen = dgi('txtTotOD').value;
	var ODTot = 0;
	$('[id^=BalanceOD]').each(function(id,elem){
		var odVal = $(elem).val();
			if(odVal=='') { odVal=0; }
			ODTot = (parseFloat(ODTot)) + (parseFloat(odVal));
	});
	
	/*
	for(i=0; i<ODLen; i++)
	{
		if(dgi('BalanceOD'+i)){
			var odVal = dgi('BalanceOD'+i).value;
			if(odVal=='') { odVal=0; }
			ODTot = (parseFloat(ODTot)) + (parseFloat(odVal));
		}
	}*/

	// CALCULATE GRAND TOTAL
	var OSLen = dgi('txtTotOS').value;
	var OSTot = 0;
	
	$('[id^=BalanceOS]').each(function(id,elem){
		var odVal = $(elem).val();
			if(odVal=='') { odVal=0; }
			ODTot = (parseFloat(ODTot)) + (parseFloat(odVal));
	});
	/*for(i=0; i<OSLen; i++)
	{
		if(dgi('BalanceOS'+i)){
			var osVal = dgi('BalanceOS'+i).value;
			if(osVal=='') { osVal=0; }
			OSTot = (parseFloat(OSTot)) + (parseFloat(osVal));
		}
	}*/
	

	if(OSTot=='') { OSTot=0; }
	if(ODTot=='') { ODTot=0; }

	clSupplyValue = parseFloat(ODTot)+parseFloat(OSTot);
	dgi('clSupply').value=clSupplyValue;
//}
	var clExam = dgi('clExam').value;
	
	if(clSupplyValue=='') { clSupplyValue=0; }
	if(clExam=='') { clExam=0; }	
		
	dgi('totalCharges').value = parseFloat(clExam)+parseFloat(clSupplyValue);

	justify2Decimal(dgi('clExam'));
	justify2Decimal(dgi('clSupply'));
	justify2Decimal(dgi('totalCharges'));		
}


function printOrderPdfFn(print_order_id) {
//	alert("print_order_pdf.php?print_order_id="+print_order_id,"printOrderPdf",'location=0,status=1,resizable=1,left=10,top=80,scrollbars=no,width=900,height=515');
	if(print_order_id) {
		window.open("print_order_pdf.php?print_order_id="+print_order_id,"printOrderPdf",'location=0,status=1,resizable=1,left=10,top=80,scrollbars=no,width=900,height=515');
	}else {
		alert('Please save record before print');
	}
	//print_order_pdf
}	

//FUNCTION TO DISPLAY CURRENT DATE
	function displayDate(obj,curDt) {
		if($(obj).length) {
			$(obj).val(curDt);
			$(obj).focus();
		}	
	}
//END FUNCTION TO DISPLAY CURRENT DATE

	var global_var;
	var global_auth_number;
	var global_auth_amount;
	
	function checkInsCase(val,auth_number,auth_amount)
	{
		global_var = val;
		global_auth_number = auth_number;
		global_auth_amount = auth_amount;
		var url = "<?php $_SERVER['PHP_SELF']; ?>";
		url = url+"?val="+global_var;
		url = url+"&sid="+Math.random();
		$.ajax({
			url:url,
			type:'GET',
			success:function(response){
				var checkCase =  response;
				if(checkCase == 0){
					document.getElementById('auth_number').value = '';
					document.getElementById('auth_number').disabled = true;
					document.getElementById('auth_amount').value = '';
					document.getElementById('auth_amount').disabled = true;
					$('[id^="InsOD"]').each(function(){
						if($(this).val()==''){
							$(this).val(0);
						}
					});
					
					$('[id^="InsOS"]').each(function() {
						if($(this).val()==''){
							$(this).val(0);
						}
					});
		
					$('[id^="InsOU"]').each(function() {
						if($(this).val()==''){
							$(this).val(0);
						}
					});

				}
				else if(checkCase == 1){
					document.getElementById('auth_number').value = global_auth_number;
					document.getElementById('auth_number').disabled = false;
					document.getElementById('auth_amount').value = global_auth_amount;
					document.getElementById('auth_amount').disabled = false;
				}
			}
		});	
	}


function shipHomeAddrFn(obj,Id,addrValue) { 
	if(obj && document.getElementById(Id)) {
		if(obj.checked==true) {
			document.getElementById(Id).value=addrValue;
			document.getElementById(Id).disabled=false;
		}else if(obj.checked==false) {
			document.getElementById(Id).value='';
			document.getElementById(Id).disabled=true;
		}
	}
}

window.focus();

function show_loading_image(val){
	document.getElementById("loading_img").style.display = val;
}


function setPrescription(ObjorderTrialDspDoctorId,ObjorderTrialId,ObjorderSupplyId,ObjreOrderSupplyId) {

	if(document.getElementById('prescripClwsId')) {
		document.getElementById('prescripClwsId').value='';
	}
	var dspNm='';

	if(ObjorderSupplyId.checked==true || ObjreOrderSupplyId.checked==true) {
		//START CODE TO SELECT TRIAL/SUPPLY DROPDOWN
		if(document.getElementById('OrderedTrialSupplyId')) {
			document.getElementById('OrderedTrialSupplyId').value='Supply';
		}
		if(document.getElementById('ReceivedTrialSupplyId')) {
			document.getElementById('ReceivedTrialSupplyId').value='Supply';
		}
		if(document.getElementById('PickedUpTrialSupplyId')) {
			document.getElementById('PickedUpTrialSupplyId').value='Supply';
		}
		//END CODE TO SELECT TRIAL/SUPPLY DROPDOWN
		if(document.getElementById('prescripClwsId')) {
			document.getElementById('prescripClwsId').value=document.getElementById('hiddClwsIdTemp').value;
		}
	}else if(ObjorderTrialDspDoctorId.checked==true || ObjorderTrialId.checked==true) {
		//START CODE TO SELECT TRIAL/SUPPLY DROPDOWN
		if(document.getElementById('OrderedTrialSupplyId')) {
			document.getElementById('OrderedTrialSupplyId').value='Trial';
		}
		if(document.getElementById('ReceivedTrialSupplyId')) {
			document.getElementById('ReceivedTrialSupplyId').value='Trial';
		}
		if(document.getElementById('PickedUpTrialSupplyId')) {
			document.getElementById('PickedUpTrialSupplyId').value='Trial';
		}
		//END CODE TO SELECT TRIAL/SUPPLY DROPDOWN
		
		//var dspNmAr= new Array();
		var dspNm='';
		if(ObjorderTrialDspDoctorId.checked==true) {
			//tdPrescripId
			//'orderTrialDspNumberId','orderTrialNumberId'
			dspNmAr = document.getElementById('orderTrialDspNumberId').value;
			dspNmAr = dspNmAr.split('-');
			dspNm = dspNmAr[1];
		}else if(ObjorderTrialId.checked==true) {
			dspNmAr = document.getElementById('orderTrialNumberId').value;
			dspNmAr = dspNmAr.split('-');
			dspNm = dspNmAr[dspNmAr.length-1];
		}	
	}
	$('#orderTrialDspNumberId,#orderTrialNumberId,#PickedUpTrialSupplyId,#ReceivedTrialSupplyId,#OrderedTrialSupplyId').selectpicker('refresh');
	if(dspNm!='') {		
		if(document.getElementById('prescripClwsId')) {
			document.getElementById('prescripClwsId').value=dspNm;
		}
		var url = "prescripAjax.php";
		url = url+"?clws_id="+dspNm;
		url = url+"&displayValuesIn=ClSupply";
		$.ajax({
			url:url,
			type:'GET',
			beforeSend:function(){
				show_loading_image('block');
				$('#tdPrescripId').css('opacity','0.4');
				$('#typeDiv').css('opacity','0.4');
				$('#typeManufac').css('opacity','0.4');
			},
			success:function(response){
				show_loading_image('none');
				// Set Old Row Values
				var dd = response.split("~~");
				$('#tdPrescripId').html(dd[0]);
				//$('#typeDiv').html(dd[1]);
				$('#typeManufac').html(dd[2]);
				$('#dos').val(dd[3]);
	
				if(dd[4]=='Current Trial'){
					$('#ptPickUpAtFrontDeskId').prop('checked',true);
				}else{
					$('#ptPickUpAtFrontDeskId').prop('checked',false);
				}
	
				document.frmOrderHx.print_order_id.value= '';
				document.frmPrintOrder.print_order_id.value= '';
				dgi('orderHx').value= '';
	
				document.getElementById('clws_id').value= dspNm;
				document.getElementById('prescripClwsId').value= dspNm;
				document.getElementById('hiddClwsIdTemp').value= dspNm;
				document.getElementById('txtTotOD').value= dd[5];
				document.getElementById('txtTotOS').value= dd[6];
				document.getElementById('clws_charges_id').value= dd[7];
				$('.selectpicker').selectpicker('refresh');
				$('#tdPrescripId').css('opacity','1');
				$('#typeDiv').css('opacity','1');
				$('#typeManufac').css('opacity','1');
			}
		});
	}
}

// DELETE ROW
function removeTableRow(rowId)
{
	$("#"+rowId).remove();
}
// ADD ROWS
function addNewRow(odos, oldRow, imgId, rowNum) 
{
	var newNum = parseInt(rowNum) + 1;
	var newTrId = oldRow+newNum;
	var newImgId = imgId+newNum;
	var typeBox = '';

	$.ajax({ 
		url: "add_contactLens_order.php?rowName="+oldRow+"&odos="+odos+"&rowNum="+rowNum,
		type:'GET',
		success: function(newRow){
			// Set Old Row Values
			var imgObj = dgi(imgId+rowNum);
			$("#" + imgId+rowNum).removeClass('glyphicon-plus').addClass('glyphicon-remove');
			imgObj.title = 'Delete Row';
			//imgObj.style.height = '16';
			//imgObj.src = '../../library/images/closebut.png';
			imgObj.onclick=function(){
				removeTableRow(oldRow+rowNum);
			}
			
			$("#"+oldRow+rowNum).after(newRow);	// ADD NEW ROW
			if(odos =='od') {
				$('#txtTotOD').val(parseInt(newNum)+1);
				typeBox='LensBoxOD';
			}else if(odos =='os') {
				$('#txtTotOS').val(parseInt(newNum)+1);
				typeBox='LensBoxOS';
			}
			$('#colorNameIdList'+newNum+'').selectpicker('refresh');
			$('#lensNameIdList'+newNum+'').selectpicker('refresh'); 
			$('#lensNameIdListOS'+newNum+'').selectpicker('refresh'); 
			$('#colorNameIdListOS'+newNum+'').selectpicker('refresh'); 
		}
	});
}

function disableBtns(){
	dgi('SaveBtn').disabled=true;
	dgi('SaveEnterCharges').disabled=true;
	dgi('PrintBtn').disabled=true;
}

function checkPriceQty(mode){
	var saveData=false;
	var totOD = dgi('txtTotOD').value;
	var totOS = dgi('txtTotOS').value;
	for(i=0; i<=totOD; i++){
		if(dgi('QtyOD'+i)){
			if(dgi('QtyOD'+i).value>0){
				saveData=true;
				break;
			}
		}
	}
	if(saveData==false){
		for(i=0; i<=totOS; i++){
			if(dgi('QtyOS'+i)){
				if(dgi('QtyOS'+i).value>0){
					saveData=true;
				}
			}
		}
	}
	if(saveData){
		if(mode=='enterCharges'){ dgi('frmPrintOrder').enterCharges.value='yes'; }
		dgi('frmPrintOrder').submit();
	    disableBtns();
	}else{
		alert('Fill Quantity for Order!');
		return false;
	}
}
	top.popup_resize(1600,770)
</script>
<?php 

//START CODE TO GET TRIAL NUMBER
$clws_typeArr = array();
$clws_trial_numberArr = array();

$orderTrialDspNumberQry = "SELECT *, DATE_FORMAT(clws_savedatetime, '%m-%d-%Y') as savedDate FROM contactlensmaster where patient_id='".$_SESSION['patient']."' order by clws_id DESC";
$orderTrialDspNumberRes = imw_query($orderTrialDspNumberQry);
$orderTrialDspNumberNumRow = imw_num_rows($orderTrialDspNumberRes);
if($orderTrialDspNumberNumRow>0) {
	while($orderTrialDspNumberRow = imw_fetch_array($orderTrialDspNumberRes)) {
		$Keyclws_id = $orderTrialDspNumberRow['clws_id'];
		$clws_typeArr[$Keyclws_id] = $orderTrialDspNumberRow['clws_type'];
		$clws_DateArr[$Keyclws_id] = $orderTrialDspNumberRow['savedDate'];
		$clws_trial_numberArr[$Keyclws_id] = $orderTrialDspNumberRow['clws_trial_number'];
	
	}
}
//END CODE TO GET TRIAL NUMBER

//START CODE TO GET ORDER HISTORY
if($_SESSION['patient'] && $clws_id!=""){
	$andClswIdQry='';
	//$andClswIdQry ="AND clws_id='".$clws_id."'";
	//$dosQry ="AND dos='".$dos."'";
	$orderHxQuery= "SELECT * FROM clprintorder_master WHERE patient_id='".$_SESSION['patient']."' $andClswIdQry ORDER BY print_order_savedatetime DESC";
	$orderHxRes = imw_query($orderHxQuery) or die(imw_error());				
	$orderHxNumRow = imw_num_rows($orderHxRes);
}

//END CODE TO GET ORDER HISTORY

//START GET PATIENT DETAIL
if($_SESSION['patient']){
	$row_address = getPatientDataRow($_SESSION['patient']);
}
//END GET PATIENT DETAIL

//START CODE TO GET AUTHORIZATION NUMBER
$authInfoQry = "
				SELECT patient_auth.auth_name,patient_auth.AuthAmount  
				FROM patient_auth,insurance_data
				WHERE insurance_data.pid='".$_SESSION['patient']."'
				AND insurance_data.type='primary'
				AND insurance_data.auth_required='Yes'
				AND insurance_data.id=patient_auth.ins_data_id
				ORDER BY patient_auth.a_id DESC";
				
$authInfoRes = imw_query($authInfoQry) or die(imw_error());				
$authInfoNumRow = imw_num_rows($authInfoRes);
if($authInfoNumRow<=0) {
	$authInfoQry = "
					SELECT patient_auth.auth_name,patient_auth.AuthAmount 
					FROM patient_auth,insurance_data
					WHERE insurance_data.pid='".$_SESSION['patient']."'
					AND insurance_data.type='secondary'
					AND insurance_data.auth_required='Yes'
					AND insurance_data.id=patient_auth.ins_data_id
					ORDER BY patient_auth.a_id DESC 
					";
	$authInfoRes = imw_query($authInfoQry) or die(imw_error());			
	$authInfoNumRow = imw_num_rows($authInfoRes);
}
if($authInfoNumRow>0) {
	$authInfoRow = imw_fetch_array($authInfoRes);
	$auth_numberPrint = $authInfoRow['auth_name'];
	$auth_amountPrint = $authInfoRow['AuthAmount'];
	$auth_number = $auth_numberPrint;
	$auth_amount = $auth_amountPrint;
	if(!$print_order_id){
		$auth_number = $authInfoRow['auth_name'];
		$auth_amount = $authInfoRow['AuthAmount'];
	}
}


//Insurance Case
if(!$print_order_id){
	$ptInsQry = "Select insurance_case_types.case_name,insurance_case_types.case_id  
				from insurance_case_types,insurance_case where insurance_case.patient_id='".$_SESSION['patient']."' 
				&& insurance_case.ins_case_type = insurance_case_types.case_id ORDER BY `insurance_case`.`ins_caseid` DESC";
	$ptInsRes = imw_query($ptInsQry);
	$ptInsRow = imw_fetch_array($ptInsRes);
	$patientInsuranceType = $ptInsRow['case_id'];
}

if($print_order_id){
	
}

//FOR PRESCRIPTION
$orderReOrderDspNumberQry = "SELECT contactlensmaster.*, contactlensworksheet_det.* FROM contactlensmaster 
							LEFT JOIN contactlensworksheet_det ON contactlensworksheet_det.clws_id = contactlensmaster.clws_id 
							where contactlensmaster.patient_id='".$_SESSION['patient']."' 
							AND contactlensmaster.clws_id='".$clws_id."' ORDER BY contactlensworksheet_det.id";
$GetTypeManufacRow = mysqlifetchdata($orderReOrderDspNumberQry);
$eVal = substr(trim($GetTypeManufacRow[0]['cpt_evaluation_fit_refit']),0,1);
if($eVal=="$"){
	$cptEvalCharges = substr($GetTypeManufacRow[0]['cpt_evaluation_fit_refit'],1,strlen($GetTypeManufacRow[0]['cpt_evaluation_fit_refit']));
}else{
	$cptEvalCharges = trim($GetTypeManufacRow[0]['cpt_evaluation_fit_refit']);
}
$clws_charges_id = $GetTypeManufacRow[0]['charges_id'];


//Insurance Case
$InsQry = "Select * from insurance_case_types order by case_name asc";
$InsRes = imw_query($InsQry);

//END CODE TO GET AUTHORIZATION NUMBER

$finalWrkshtExist='no';
?>
</head>
<body class="scrol_la_color">
<div>
	<div class="mainwhtbox contlns" style="height:630px;overflow-y:scroll;">

			<div align="center" id="loading_img" width="100%" style="display:none; top:50%; left:50%; z-index:1000; position:absolute;">
				<div class="loader"></div>
			</div>
			<form name="frmOrderHx"action="print_order.php" method="post" onSubmit="checkPriceQty();">
				<input type="hidden" name="patient_id" value="<?php echo $_SESSION['patient'];?>">    
				<input type="hidden" name="provider_id" value="<?php echo $provider_id;?>">
				<input type="hidden" name="technician_id" value="<?php echo $technician_id;?>">
				<input type="hidden" name="operator_id" value="<?php echo $operator_id;?>">
				<input type="hidden" name="print_order_id" id="print_order_id" value="<?php echo $print_order_id;?>">
				<input type="hidden" name="callFrom" id="callFrom" value="<?php echo $callFrom;?>">
			</form>
			
			<form action="print_order.php" method="post" name="frmPrintOrder" id="frmPrintOrder">
				<input type="hidden" name="recordSave" value="saveTrue">
				<input type="hidden" name="recordSavePrint" value="">
				<input type="hidden" name="patient_id" value="<?php echo $_SESSION['patient'];?>">    
				<input type="hidden" name="provider_id" value="<?php echo $provider_id;?>">
				<input type="hidden" name="technician_id" value="<?php echo $technician_id;?>">
				<input type="hidden" name="operator_id" value="<?php echo $operator_id;?>">
				<input type="hidden" name="clws_id" id="clws_id" value="<?php echo $clws_id;?>">
				<input type="hidden" name="print_order_id" id="print_order_id" value="<?php echo $print_order_id;?>">
				<input type="hidden" name="print_order_savedatetime" value="<?php echo date('Y-m-d H:i:s');?>">
				<input type="hidden" name="prescripClwsId" id="prescripClwsId" value="<?php echo $clOrderRes[0]['prescripClwsId'];?>">
				<input type="hidden" name="dos" id="dos" value="<?php echo $dos;?>">
				<input type="hidden" name="enterCharges" id="enterCharges" value="">
				<input type="hidden" name="cptEvalCharges" id="cptEvalCharges" value="<?php echo $cptEvalCharges;?>">
				<input type="hidden" name="clws_charges_id" id="clws_charges_id" value="<?php echo $clws_charges_id;?>">
				<input type="hidden" name="pageName" id="pageName" value="printOrder">
				<input type="hidden" name="callFrom" id="callFrom" value="<?php echo $callFrom;?>">
				<input type="hidden" name="hiddClwsIdTemp" id="hiddClwsIdTemp" value="<?php echo $hiddClwsIdTemp;?>">	
				<?php /* Heading */ ?>
			
					<div class="row purple_bar" style="margin-bottom:4px;">
						<div class="col-sm-3" style="padding-left:10px;">
							<label>&nbsp;Print Order</label>	
						</div>
						<div id="odrDtLblId" class="col-sm-5 text-center">
							<label><?php echo $row_address['fname'].' '.$row_address['lname'].' - '.$row_address['id'];?></label>
						</div>
						<div class="col-sm-4 form-inline text-right" style="padding-right:10px;">
							<label>Order Hx:</label>
							<select id="orderHx" name="orderHx" class="selectpicker" onChange="if(this.value!=''){ document.frmOrderHx.print_order_id.value = this.value;document.frmOrderHx.submit();}">
								<option value="" <?php if(!$print_order_id) {echo 'selected'; }?>>Select</option>
								<?php
								if($orderHxNumRow>0){
									while($orderHxRow = imw_fetch_array($orderHxRes)) {
										$orderHxId =$orderHxRow['print_order_id'];
										$ddParts  =explode(' ', $orderHxRow['print_order_savedatetime']);
										$orderHxDateTime =get_date_format($ddParts[0], 'yyyy-mm-dd'); 
										$orderHxDateTime.=date(' h:i A', strtotime($ddParts[1]));
										//$orderHxDateTime =date("m-d-Y h:i A",strtotime($orderHxRow['print_order_savedatetime'])); 
								?>
										<option value="<?php echo $orderHxId;?>" <?php if($print_order_id==$orderHxId) { echo 'selected'; }?>><?php echo $orderHxDateTime;?></option>
								<?php
									}
								}
								?>
							</select>	
						</div>	
					</div>	
			
				<?php /* Evaluation */ ?>
				
					<div class="row">
						<div class="col-sm-2" style="padding-left:10px;">
							<label>Evaluation/Fit:</label>	
						</div>
						<div class="col-sm-2">
							<div class="input-group">
								<input type="text" class="date-pick form-control" name="clEvaluationDate" id="clEvaluationDate" value="<?php echo displayDateFormat($clEvaluationDate);?>" onChange="chkDateFrmat(this);">
								<label class="input-group-addon" for="clEvaluationDate">
									<span class="glyphicon glyphicon-calendar"></span>	
								</label>	
							</div>
						</div>
						<div class="col-sm-8" style="padding-left:15px;">
							<textarea name="clEvaluationComment" id="clEvaluationComment" class="form-control" rows="1"><?php echo $clOrderRes[0]['clEvaluationComment'];?></textarea>	
						</div>	
					</div>	
					
				
				<?php /* CL Fitting */ ?>
				
					<div class="row">
						<div class="col-sm-2" style="padding-left:10px;">
							<label>CL Fitting:</label>	
						</div>
						<div class="col-sm-2">
							<div class="input-group">
								<input type="text" class="date-pick form-control" name="clFittingDate" id="clFittingDate" value="<?php echo displayDateFormat($clFittingDate);?>" onChange="chkDateFrmat(this);">
								<label class="input-group-addon" for="clFittingDate">
									<span class="glyphicon glyphicon-calendar"></span>	
								</label>	
							</div>
						</div>
						<div class="col-sm-8" style="padding-left:15px;">
							<textarea name="clFittingComment" id="clFittingComment" class="form-control" rows="1" ><?php echo $clOrderRes[0]['clFittingComment'];?></textarea>
						</div>	
					</div>	
			
				
				<?php /* CL Teach */ ?>
				
					<div class="row">
						<div class="col-sm-2" style="padding-left:10px;">
							<label>CL Teach:</label>	
						</div>
						<div class="col-sm-2">
							<div class="input-group">
								<input type="text" class="date-pick form-control" name="clTeachDate" id="clTeachDate" value="<?php echo displayDateFormat($clTeachDate);?>" onChange="chkDateFrmat(this);">
								<label class="input-group-addon" for="clTeachDate">
									<span class="glyphicon glyphicon-calendar"></span>	
								</label>	
							</div>
						</div>
						<div class="col-sm-8" style="padding-left:15px;">
							<textarea name="clTeachComment" id="clTeachComment" class="form-control" rows="1" ><?php echo $clOrderRes[0]['clTeachComment'];?></textarea>
						</div>	
					</div>	
			
				
				<?php /* Comment Box */ ?>
				
				<div class="row">
					<div class="col-sm-2" style="padding-left:10px;">
						<label>Comment</label>	
					</div>	
					<div class="col-sm-10">
						<textarea name="lensComment" id="lensComment" class="form-control" rows="3"><?php echo $clOrderRes[0]['lensComment'];?></textarea>	
					</div>	
				</div>
				<div class="clearfix"></div>
				
				<?php /* Order Prescription Selection */ ?>
				
				<div class="row">
					<div class="col-sm-3" style="padding-left:10px;">
						<div class="checkbox">
    						<input type="checkbox" name="trial_order" id="trial_order" onClick="javascript:setOrderAsTrial(this.id);" value="1" <?php if($clOrderRes[0]['trial_order']=='1') { echo 'checked'; }?>>
    						<label for="trial_order">Trial</label>
						</div>
					</div>
					<div class="col-sm-3">
    					<div class="checkbox">
    						<input type="checkbox" name="orderTrialDsp" id="orderSupplyId" onClick="javascript:checkSingle('orderSupplyId','orderTrialDsp');hideDisplayOrderFn('orderTrialDspDoctorId','orderTrialId','orderTrialDspNumberId','orderTrialNumberId');setPrescription(document.getElementById('orderTrialDspDoctorId'),document.getElementById('orderTrialId'),document.getElementById('orderSupplyId'),document.getElementById('reOrderSupplyId'));" value="orderSupply" <?php if($clOrderRes[0]['orderTrialDsp']=='orderSupply') { echo 'checked'; }?>>
    						<label for="orderSupplyId">Order&nbsp;Supply</label>	
    					</div>	
					</div>
					<div class="col-sm-3">
						<div class="row">
							<div class="col-sm-3">
								<label>Insurance Case&nbsp;</label>
							</div>
							<div class="col-sm-6">
								<select name="ins_case" id="ins_case" class="minimal" onChange="checkInsCase(this.value,'<?php echo $auth_numberPrint;?>','<?php echo $auth_amountPrint;?>');" data-width="60%" data-size="5" data-title="Please Select">
									<option value="0" <?php if($patientInsuranceType == 0){echo "selected";}?>>Self Pay</option>										
								<?php 
										while($InsRow = imw_fetch_array($InsRes)){ ?>
											<option value="<?php echo $InsRow['case_id'];?>" <?php if($InsRow['case_id'] == $patientInsuranceType){echo "selected";}?>>
												<?php echo $InsRow['case_name']; ?>
											</option>
								<?php } ?>
								</select>
							</div>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="row">
							<div class="col-sm-4">
								<label>Authorization#&nbsp;</label>
							</div>
							<div class="col-sm-6">
								<input type="text" class="form-control" name="auth_number" id="auth_number" value="<?php echo $insuranceAuthorizationNumber; ?>" style="width:164px;	display:inline;" />
							</div>
						</div>
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="row">
					<div class="col-sm-3" style="padding-left:10px;">
						<div class="checkbox">
							<input type="checkbox" name="orderTrialDsp" id="orderTrialId" onClick="javascript:checkSingle('orderTrialId','orderTrialDsp');hideDisplayOrderFn('orderTrialDspDoctorId','orderTrialId','orderTrialDspNumberId','orderTrialNumberId');setPrescription(document.getElementById('orderTrialDspDoctorId'),document.getElementById('orderTrialId'),document.getElementById('orderSupplyId'),document.getElementById('reOrderSupplyId'));" value="orderTrial" <?php if($orderTrialDsp=='orderTrial') { echo 'checked'; }?>>
							<label for="orderTrialId">Order</label>	
							<select name="orderTrialNumber" id="orderTrialNumberId" class="selectpicker minimal" onChange="setPrescription(document.getElementById('orderTrialDspDoctorId'),document.getElementById('orderTrialId'),document.getElementById('orderSupplyId'),document.getElementById('reOrderSupplyId'));">
								<?php
								$clws_typeNewValue='';
								if($clws_typeArr) {
									foreach($clws_typeArr as $key=>$clws_typeValue){
										if($clws_typeValue=='Evaluation') {
											$clws_typeNewValue = 'Evaluation';
										}else if($clws_typeValue=='Fit') {
											$clws_typeNewValue = 'Fit';
										}else if($clws_typeValue=='Refit') {
											$clws_typeNewValue = 'Refit';
										}else if($clws_typeValue=='Current Trial') {
											$clws_typeNewValue = 'Trial'.($clws_trial_numberArr[$key]);
										}else if($clws_typeValue=='Final') {
											$clws_typeNewValue = 'Final';
											$finalWrkshtExist='yes';
										}else if($clws_typeValue!='') {
											$clws_typeNewValue = $clws_typeValue;
										}
								?>
										<option value="<?php echo $clws_typeNewValue.'-'.$key;?>" <?php if($clOrderRes[0]['orderTrialDspNumber']==$clws_typeNewValue) { echo 'selected'; }?>><?php echo $clws_typeNewValue.' ('.$clws_DateArr[$key].')';?></option>
								<?php
									}
								}
								?>
							</select>
						</div>
						<div style="display:none">
							<input type="checkbox" name="orderTrialDsp" id="orderTrialDspDoctorId" value=""> <select name="orderTrialDspNumber" id="orderTrialDspNumberId">
							</select>
						</div> 
					</div>
					<div class="col-sm-3">
						<div class="checkbox">
							<input type="checkbox" name="orderTrialDsp" id="reOrderSupplyId" onClick="javascript:checkSingle('reOrderSupplyId','orderTrialDsp');hideDisplayOrderFn('orderTrialDspDoctorId','orderTrialId','orderTrialDspNumberId','orderTrialNumberId');setPrescription(document.getElementById('orderTrialDspDoctorId'),document.getElementById('orderTrialId'),document.getElementById('orderSupplyId'),document.getElementById('reOrderSupplyId'));" value="reOrderSupply" <?php if($clOrderRes[0]['orderTrialDsp']=='reOrderSupply') { echo 'checked'; }?>>
							<label for="reOrderSupplyId">Reorder&nbsp;Supply</label>	
						</div>
					</div>
					<div class="col-sm-3">
    					<div class="row">
							<div class="col-sm-3">
								<label>Auth&nbsp;Amount&nbsp;</label>
							</div>
							<div class="col-sm-6">
								<input type="text" name="auth_amount" id="auth_amount" value="<?php echo $insuranceAuthorizationAmount; ?>" class="form-control" style="width:164px;" />
							</div>
    					</div>
					</div>
					<div class="col-sm-3">
						<div class="row">
							<div class="col-sm-4">
								<label>Discount&nbsp;Amount&nbsp;</label>
							</div>
							<div class="col-sm-6">
								<input type="text" name="disc_amount" id="disc_amount" value="<?php echo $clOrderRes[0]['disc_amount'];?>" class="form-control" style="display:inline;width:164px;">
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12" style="padding-left:10px;">
						<label>Type&nbsp;</label>
						<textarea name="typeManufac" id="typeManufac" class="form-control" rows="1"><?php echo $typeManufac; ?></textarea>	
					</div>
				</div>

				<?php /* Prescription Section */ ?>	
			
				<div class="row" style="padding-left:10px;">
					<div id="tdPrescripId" class="col-sm-12 pt10">
						<div class="row">
					<?php
					$GetTypeManufacNumRow = sizeof($GetTypeManufacRow);
					if($GetTypeManufacNumRow>0){
						$oldClType = '';	$j=1;
					?>
						<div class="purple_bar" style="padding-left: 10px!important;">
							<label class="la_bg">Prescription</label>	
						</div>
						<?php	
							for($i=0;$i < $GetTypeManufacNumRow; $i++)
							{
								$id= $GetTypeManufacRow[$i]['id'];
								$clGrp				= trim(stripslashes($GetTypeManufacRow[$i]['clGrp']));
								$clEye 	  			= trim(stripslashes($GetTypeManufacRow[$i]['clEye']));
							
								$dispTitle =0;	$topPad='';	
								$odos = strtolower($GetTypeManufacRow[$i]['clEye']);
						
								$clType = $GetTypeManufacRow[$i]['clType'];
								if($clType != $oldClType) { 
									$dispTitle=1;	$topPad="padding-top:17px"; 
									//$divHeight = "45px"; $rgpHeight = "48px";
								}else {
									//$divHeight = "30px"; $rgpHeight = "30px";
								}
								if($clType =='scl')
								{ ?>
									<div id="CLRow<?php echo $j;?>" class="col-sm-12" style="display:block;height:<?php echo $divHeight;?>;" >
										<div class="row">
											<div class="col-sm-12">
												<table align="left" class="table table-bordered table-condensed" style="width:100%;" cellpadding = 0 cellspacing=0>
												  <?php if($dispTitle==1) { ?>
													<tr class="grythead">
														<th style="width:2%;"></th>
														<th style="width:2%;"></th>
														<th style="width:10%;">B. Curve</th> 
														<th style="width:10%;">Diameter</th> 
														<th style="width:10%;">Sphere</th>												
														<th style="width:10%;">Cylinder</th>
														<th style="width:7%;">Axis</th>  
														<th style="width:7%;">ADD</th>
														<th style="width:7%;">DVA</th>
														<th style="width:7%;">NVA</th>
														<th style="width:15%;">Type</th>
														<th style="width:10%;">Color</th>
													</tr>
												  <?php } ?>                 
												  <?php if($odos=='od') { ?>
												  <tr>
												  	<td class="od" style="width:2%;"><label>SCL</label></td>
													<td class="od" style="width:2%;font-size:12px;">OD</td>
													<td style="width:10%;">
														<input class="form-control" type="text" name="SclBcurveOD" value="<?php echo $GetTypeManufacRow[$i]['SclBcurveOD'];?>" readonly>
													</td> 
													<td style="width:10%;">
														<input  id="SclDiameterOD" type="text" class="form-control " name="SclDiameterOD" value="<?php echo $GetTypeManufacRow[$i]['SclDiameterOD'];?>" readonly>
													</td> 
													<td style="width:10%;">
														<input type="text" name="sphere<?php echo $id;?>" value="<?php echo $GetTypeManufacRow[$i]['SclsphereOD'];?>" class="form-control"  id="SclsphereOD"  onBlur="justify2Decimal(this)" readonly >
													</td>												
													<td style="width:10%;">
														<input  id="SclCylinderOD" type="text" name="SclCylinderOD" value="<?php echo $GetTypeManufacRow[$i]['SclCylinderOD'];?>" class="form-control"  onBlur="justify2Decimal(this)" readonly >
													</td> 
													<td style="width:7%;">
														<input type="text" name="SclaxisOD" value="<?php echo $GetTypeManufacRow[$i]['SclaxisOD'];?>" class="form-control"  id="SclaxisOD"  readonly>
													</td> 
													<td style="width:7%;">
														<input id="add<?php echo $id;?>" type="text" class="form-control " name="SclAddOD" value="<?php echo $GetTypeManufacRow[$i]['SclAddOD'];?>" readonly>
													</td>
													<td style="width:7%;">
														<input id="SclDvaOD" type="text" name="add<?php echo $id;?>" value="<?php echo $GetTypeManufacRow[$i]['SclDvaOD']; ?>" class="form-control" readonly>
													</td>
													<td style="width:7%;">
														<input type="text" name="SclNvaOD"  id="SclNvaOD" value="<?php echo $GetTypeManufacRow[$i]['SclNvaOD'];?>" class="form-control"  readonly>
													</td>
													<td style="width:15%;">
														<input class="form-control" type="text" name="SclTypeOD" id="SclTypeOD" value="<?php echo $GetTypeManufacRow[$i]['SclTypeOD'];?>" readonly   >
													</td>
													<td style="width:10%;">
														<input class="form-control" type="text" name="SclColorOD" id="SclColorOD" value="<?php echo $GetTypeManufacRow[$i]['SclColorOD'];?>" readonly >
													</td>
													</tr>
												  <?php } if($odos=='os') {  ?>
												  <tr>
												  	<td class="os" style="width:2%;"><label>SCL</label></td>
													<td class="os" style="width:2%;font-size:12px;">OS</td>
													<td style="width:10%;"><input type="text" name="SclBcurveOS" value="<?php echo $GetTypeManufacRow[$i]['SclBcurveOS'];?>" class="form-control" readonly> </td> 
													<td style="width:10%;"><input id="SclDiameterOS" type="text" class="form-control " name="SclDiameterOS" value="<?php echo $GetTypeManufacRow[$i]['SclDiameterOS'];?>" readonly></td> 
													<td style="width:10%;"><input type="text" name="sphere<?php echo $id;?>" value="<?php echo $GetTypeManufacRow[$i]['SclsphereOS'];?>" class="form-control" id="SclsphereOS" onKeyUp="check2Blur(this,'S','SclCylinderOS');" onBlur="justify2Decimal(this)" readonly/></td> 
													<td style="width:10%;"><input class="form-control" id="SclCylinderOS" type="text" name="SclCylinderOS" value="<?php echo $GetTypeManufacRow[$i]['SclCylinderOS'];?>" onkeyup="check2Blur(this,'C','SclaxisOS');" onBlur="justify2Decimal(this)" readonly> </td> 
													<td style="width:7%;"><input type="text" name="SclaxisOS" value="<?php echo $GetTypeManufacRow[$i]['SclaxisOS'];?>" class="form-control" id="SclaxisOS" onKeyUp="check2Blur(this,'A','SclaxisOS');" readonly ></td> 
													<td style="width:7%;"><input  id="SclAddOS" type="text" class="form-control " name="add<?php echo $id;?>" value="<?php echo $GetTypeManufacRow[$i]['SclAddOS'];?>" readonly></td>
													<td style="width:7%;"><input id="SclDvaOS" type="text" name="SclDvaOS" value="<?php echo $GetTypeManufacRow[$i]['SclDvaOS']; ?>" class="form-control" readonly></td>
													<td style="width:7%;"><input type="text" name="SclNvaOS"  id="SclNvaOS" value="<?php echo $GetTypeManufacRow[$i]['SclNvaOS'];?>" class="form-control" readonly></td>
													<td style="width:15%;"><input class="form-control" type="text" name="SclTypeOS" id="SclTypeOS" value="<?php echo $GetTypeManufacRow[$i]['SclTypeOS'];?>" readonly   ></td>
													<td style="width:10%;"><input class="form-control" type="text" name="SclColorOS" id="SclColorOS" value="<?php echo $GetTypeManufacRow[$i]['SclColorOS'];?>" readonly   ></td>
													</tr>
												  <?php } ?>                      
												</table>
											</div>	
										</div>
									</div>
								<?php
								}else if($clType =='rgp' || $clType =='rgp_soft' || $clType =='rgp_hard') { ?>
								<div id="CLRow<?php echo $j;?>" class="col-sm-12 pt10" style="display:block; clear:both; height:<?php echo $divHeight;?>;" >
									<div class="row">
										<div class="col-sm-12">
											<table class="table table-bordered table-condensed">
											  <?php if($dispTitle==1) { ?>
												<tr class="grythead">
													<?php
														/*<th class="od">&nbsp;</th>
														<th>BC</th> 												
														<th>Diameter</th>
														<th nowrap="nowrap">Power</th>
														<th>Description</th> 
														<th>Color</th> 
														<th class="text-left">Add</th> 
														<th>DVA</th> 
														<th>NVA</th> 
														<th>Type</th> 
														<th>Color</th>*/
													?>
													<th style="width:3%;"></th>
													<th style="width:2%;"></th>
													<th style="width:9%;">B. Curve</th> 
													<th style="width:10%;">Diameter</th> 
													<th style="width:9%;">Power</th>												
													<th style="width:10%;">Optical zone</th>
													<th style="width:9%;">Color</th>
													<th style="width:9%;">ADD</th>
													<th style="width:9%;">DVA</th>
													<th style="width:9%;">NVA</th>
													<th style="width:15%;">Type</th>
													
												</tr>
											  <?php } ?>                 
											  <?php if($odos=='od') { ?>
												<tr id="rgpODRow" style="display:<?php echo $rgpSupplyDisplayOD;?>;">
													<td class="od" style="width:3%;font-size:12px;">
														<label>
															<?php
																if($clType =='rgp'){
																	echo "RGP";
																}else if($clType =='rgp_soft'){
																	echo "RGP Soft";
																}else if($clType =='rgp_hard'){
																	echo "RGP Hard";
																}
															?>
														</label>
													</td>
													<td class="od" style="width:2%;font-size:12px;">OD</td>
													<td style="width:9%;"><input readonly class="form-control"  type="text" name="RgpBCOD" value="<?php echo $GetTypeManufacRow[$i]['RgpBCOD'];?>" ></td>	
													
													<td style="width:10%;"><input readonly type="text" name="RgpDiameterOD" value="<?php echo $GetTypeManufacRow[$i]['RgpDiameterOD'];?>" class="form-control" ></td> 
													
													<td style="width:9%;"><input readonly class="form-control"  type="text" name="sphere<?php echo $id;?>" value="<?php echo $GetTypeManufacRow[$i]['RgpPowerOD'];?> " ></td>
													
													<td style="width:10%;"><input readonly type="text" name="RgpOZOD" value="<?php echo $GetTypeManufacRow[$i]['RgpOZOD'];?>" class="form-control" /></td> 
													
													<td style="width:9%;"><input readonly type="text" name="RgpColorOD"  id="RgpColorOD" value="<?php  echo $GetTypeManufacRow[$i]['RgpColorOD']; ?>" class="form-control" ></td>
													
													<td style="width:9%;"><input readonly  id="RgpAddOD" type="text" class="form-control " name="add<?php echo $id;?>" value="<?php echo $GetTypeManufacRow[$i]['RgpAddOD'];?>" ></td> 
													
													<td style="width:9%;"><input readonly id="RgpDvaOD" type="text" name="RgpDvaOD" value="<?php if($GetTypeManufacRow[$i]['RgpDvaOD']) echo $GetTypeManufacRow[$i]['RgpDvaOD']; else echo '20/'; ?>" class="form-control" ></td>
													
													<td style="width:9%;"><input readonly type="text" name="RgpNvaOD"  id="RgpNvaOD" value="<?php if($GetTypeManufacRow[$i]['RgpNvaOD']) echo $GetTypeManufacRow[$i]['RgpNvaOD']; else echo '20/'; ?>" class="form-control" > </td>
													
													<td style="width:15%;"><input readonly class="form-control" type="text" name="RgpTypeOD" value="<?php echo $GetTypeManufacRow[$i]['RgpTypeOD'];?>"></td>
												</tr>
											<?php } if($odos=='os') { ?>
													<tr id="rgpOSRow" style="display:<?php echo $rgpSupplyDisplayOS;?>;">
													<td class="os" style="width:3%;font-size:12px;">
														<label>
															<?php
																if($clType =='rgp'){
																	echo "RGP";
																}else if($clType =='rgp_soft'){
																	echo "RGP Soft";
																}else if($clType =='rgp_hard'){
																	echo "RGP Hard";
																}
															?>
														</label>
													</td>
													<td class="os" style="width:2%;font-size:12px;">OS</td>
														
														<td style="width:9%;"><input readonly class="form-control"  type="text" name="RgpBCOS" value="<?php echo $GetTypeManufacRow[$i]['RgpBCOS'];?>" ></td>												
														
														<td style="width:10%;"><input readonly   type="text" name="RgpDiameterOS" value="<?php echo $GetTypeManufacRow[$i]['RgpDiameterOS'];?>" class="form-control" > </td> 
														
														<td style="width:9%;"><input readonly class="form-control"  type="text" name="sphere<?php echo $id;?>" value="<?php echo $GetTypeManufacRow[$i]['RgpPowerOS'];?> " ></td>
														
														<td style="width:10%;"><input readonly type="text" name="RgpOZOS" value="<?php echo $GetTypeManufacRow[$i]['RgpOZOS'];?>" class="form-control" /></td> 
														
														<td style="width:9%;"><input readonly type="text" name="RgpColorOS"  id="RgpColorOS" value="<?php echo $GetTypeManufacRow[$i]['RgpColorOS']; ?>" class="form-control" > </td>
														
														<td style="width:9%;"><input readonly id="RgpAddOS" type="text" class="form-control " name="add<?php echo $id;?>" value="<?php echo $GetTypeManufacRow[$i]['RgpAddOS'];?>" ></td> 
														
														<td style="width:9%;"><input readonly type="text" name="RgpDvaOS"  id="RgpDvaOS" value="<?php if($GetTypeManufacRow[$i]['RgpDvaOS']) echo $GetTypeManufacRow[$i]['RgpDvaOS']; else echo '20/'; ?>" class="form-control" > </td>
														
														<td style="width:9%;"><input readonly type="text" name="RgpNvaOS"  id="RgpNvaOS" value="<?php if($GetTypeManufacRow[$i]['RgpNvaOS']) echo $GetTypeManufacRow[$i]['RgpNvaOS']; else echo '20/'; ?>" class="form-control" > </td>
														
														<td style="width:15%;"><input readonly class="form-control" type="text" name="RgpTypeOS" value="<?php echo $GetTypeManufacRow[$i]['RgpTypeOS'];?>"> </td>
													</tr>
											<?php } ?>
											</table>	
										</div>	
									</div>	
								</div>        
								<?php	
								}else if($clType =='cust_rgp') {			?>
									<div id="CLRow<?php echo $j;?>" class="col-sm-12 pt10" style="display:block; clear:both; height:<?php echo $divHeight;?>;" >
										<div class="row">
											<div class="col-sm-12">
												<table class="table table-bordered table-condensed">
													<?php if($dispTitle==1) { ?>
														<tr class="grythead">
															<th style="width:2%;"></th>
															<th style="width:2%;"></th>
															<th style="width:6%;">BC</th>							
															<th style="width:7%;">Diameter</th>
															<th style="width:6%;">Power</th>	
															<th style="width:6%;">3&#176;/W</th>
															<th style="width:6%;">PC/W</th>
															<th style="width:6%;">2&#176;/W</th>
															<th style="width:6%;">OZ</th>
															<th style="width:7%;">Color</th>
															<th style="width:7%;">Blend</th>
															<th style="width:6%;">Edge</th>
															<th style="width:6%;">Add</th>
															<th style="width:6%;">DVA</th>
															<th style="width:6%;">NVA</th>
															<th style="width:12%;">Type</th>
														</tr>
												  <?php } ?>                 
												  <?php if($odos=='od') { ?>
														<tr id="rgpCustomODRow" style="display:<?php echo $rgpSupplyDisplayOD;?>;">
															<td class="od" style="width:2%;"><label>Custom RGP</label></td>
															<td class="od" style="width:2%;"><label>OD</label></td>
																
															<td style="width:6%;"><input readonly class="form-control"  type="text" name="RgpCustomBCOD" value="<?php echo($GetTypeManufacRow[$i]['RgpCustomBCOD']);?>" ></td>																										
															<td style="width:7%;"><input readonly  id="RgpCustomDiameterOD" type="text" class="form-control " name="RgpCustomDiameterOD" value="<?php echo $GetTypeManufacRow[$i]['RgpCustomDiameterOD'];?>" ></td> 
															
															<td style="width:6%;"><input readonly class="form-control"  type="text" name="sphere<?php echo $id;?>" value="<?php echo($GetTypeManufacRow[$i]['RgpCustomPowerOD']);?>" ></td> 
															
															<td style="width:6%;"><input readonly  type="text" name="RgpCustom2degreeOD" value="<?php echo($GetTypeManufacRow[$i]['RgpCustom2degreeOD']);?>" class="form-control" > </td> 
															
															<td style="width:6%;"><input readonly class="form-control"  type="text" name="RgpCustom3degreeOD" value="<?php echo($GetTypeManufacRow[$i]['RgpCustom3degreeOD']);?>" ></td>	
															
															<td style="width:6%;"><input readonly class="form-control"  type="text" name="RgpCustomPCWOD" value="<?php echo($GetTypeManufacRow[$i]['RgpCustomPCWOD']);?>" ></td>	
															
															<td style="width:6%;"><input readonly  id="RgpCustomOZOD" type="text" class="form-control " name="RgpCustomOZOD" value="<?php echo $GetTypeManufacRow[$i]['RgpCustomOZOD'];?>" ></td> 
															
															<td style="width:7%;"><input readonly type="text" name="RgpCustomColorOD"  id="RgpCustomColorOD" value="<?php echo $GetTypeManufacRow[$i]['RgpCustomColorOD']; ?>" class="form-control" > </td>
															
															<td style="width:7%;"><input readonly type="text" name="RgpCustomBlendOD"  id="RgpCustomBlendOD" value="<?php echo $GetTypeManufacRow[$i]['RgpCustomBlendOD']; ?>" class="form-control" > </td>
															
															<td style="width:6%;"><input readonly type="text" name="RgpCustomEdgeOD" value="<?php echo($GetTypeManufacRow[$i]['RgpCustomEdgeOD']);?>" class="form-control" /></td> 
															
															<td style="width:6%;"><input readonly  id="RgpCustomAddOD" type="text" class="form-control " name="add<?php echo $id;?>" value="<?php echo $GetTypeManufacRow[$i]['RgpCustomAddOD'];?>" ></td> 
															
															<td style="width:6%;"><input readonly id="RgpCustomDvaOD" type="text" name="RgpCustomDvaOD" value="<?php if($GetTypeManufacRow[$i]['RgpCustomDvaOD']) echo $GetTypeManufacRow[$i]['RgpCustomDvaOD']; else echo '20/'; ?>" class="form-control" ></td>
															
															<td style="width:6%;"><input readonly id="RgpCustomNvaOD" type="text" name="RgpCustomNvaOD" value="<?php if($GetTypeManufacRow[$i]['RgpCustomNvaOD']) echo $GetTypeManufacRow[$i]['RgpCustomNvaOD']; else echo '20/'; ?>" class="form-control" ></td>
																
															<td style="width:12%;"><input readonly  type="text" name="RgpCustomTypeOD" value="<?php echo($GetTypeManufacRow[$i]['RgpCustomTypeOD']);?>" class="form-control" ></td>
														</tr>
												  <?php } if($odos=='os') { ?>
														<tr id="rgpCustomOSRow" style="display:<?php echo $rgpSupplyDisplayOS;?>;">
															<td class="os" style="width:2%;"><label>Custom RGP</label></td>
															
															<td class="os" style="width:2%;"><label>OS</label></td>  
															
															<td style="width:6%;"><input readonly class="form-control"  type="text" name="RgpCustomBCOS" value="<?php echo($GetTypeManufacRow[$i]['RgpCustomBCOS']);?>"></td>											
															
															<td style="width:7%;"><input readonly  id="RgpCustomDiameterOS" type="text" class="form-control " name="RgpCustomDiameterOS" value="<?php echo $GetTypeManufacRow[$i]['RgpCustomDiameterOS'];?>" ></td> 
															
															<td style="width:6%;"><input readonly class="form-control"  type="text" name="sphere<?php echo $id;?>" value="<?php echo($GetTypeManufacRow[$i]['RgpCustomPowerOS']);?>" ></td> 
															
															<td style="width:6%;"><input readonly  type="text" name="RgpCustom2degreeOS" value="<?php echo($GetTypeManufacRow[$i]['RgpCustom2degreeOS']);?>" class="form-control" ></td> 
															
															<td style="width:6%;"><input readonly class="form-control"  type="text" name="RgpCustom3degreeOS" value="<?php echo($GetTypeManufacRow[$i]['RgpCustom3degreeOS']);?>" ></td>	
															
															<td style="width:6%;"><input readonly class="form-control"  type="text" name="RgpCustomPCWOS" value="<?php echo($GetTypeManufacRow[$i]['RgpCustomPCWOS']);?>" ></td>	
															
															<td style="width:6%;"><input readonly  id="RgpCustomOZOS" type="text" class="form-control " name="RgpCustomOZOS" value="<?php echo $GetTypeManufacRow[$i]['RgpCustomOZOS'];?>" ></td> 
															
															<td style="width:7%;"><input readonly type="text" name="RgpCustomColorOS"  id="RgpCustomColorOS" value="<?php echo $GetTypeManufacRow[$i]['RgpCustomColorOS']; ?>"  class="form-control" > </td>
															
															<td style="width:7%;"><input readonly type="text" name="RgpCustomBlendOS"  id="RgpCustomBlendOS" value="<?php echo $GetTypeManufacRow[$i]['RgpCustomBlendOS']; ?>"  class="form-control" > </td>
															
															<td style="width:6%;"><input readonly type="text" name="RgpCustomEdgeOS" value="<?php echo($GetTypeManufacRow[$i]['RgpCustomEdgeOS']);?>"  class="form-control" /></td> 
															
															<td style="width:6%;"><input readonly  id="RgpCustomAddOS" type="text" class="form-control " name="add<?php echo $id;?>" value="<?php echo $GetTypeManufacRow[$i]['RgpCustomAddOS'];?>"  ></td> 
															
															<td style="width:6%;"><input readonly id="RgpCustomDvaOS" type="text" name="RgpCustomDvaOS" value="<?php if($GetTypeManufacRow[$i]['RgpCustomDvaOS']) echo $GetTypeManufacRow[$i]['RgpCustomDvaOS']; else echo '20/'; ?>"  class="form-control" ></td>
															
															<td style="width:6%;"><input readonly id="RgpCustomNvaOS" type="text" name="RgpCustomNvaOS" value="<?php if($GetTypeManufacRow[$i]['RgpCustomNvaOS']) echo $GetTypeManufacRow[$i]['RgpCustomNvaOS']; else echo '20/'; ?>"  class="form-control" ></td>
															
															<td style="width:12%;"><input readonly  type="text" name="RgpCustomTypeOS" value="<?php echo($GetTypeManufacRow[$i]['RgpCustomTypeOS']);?>" class="form-control" ></td> 
														</tr>
												  <?php } ?>
												</table>	
											</div>	
										</div>
									</div>
									<?php
								}
								$oldClType = $clType;
								$j++;	
							}	// END WHILE LOOP
						}	// END IF LOOP	
						?>	
							</div>		
						</div>	
					</div>	
			
				<?php /* Type Section */ ?>
				<div id="typeDiv" class="pt10">
					<div class="row ">
						<?php /* Type Heading */ ?>
						<div class="col-sm-12">
							<div class="row purple_bar">
								<div class="col-sm-2">
									<label>&nbsp;Order Trial #/Supply</label>	
								</div>	
								<div class="col-sm-2">
									<?php /* <span id="unusedInsIdTd" style="visibility:<?php echo $authStyleVisible;?>;">Authorization#&nbsp;<?php echo $unusedAuthorization;?></span>	*/ ?>
								</div>	
								<div class="col-sm-8 text-right">
									<div class="row form-inline">
										<div class="col-sm-4">
											<label>CL Exam:</label>
											<div class="input-group">
												<div class="input-group-addon">
													<span class="glyphicon glyphicon-usd"></span>
												</div>
												<input type="text" readonly id="clExam" name="clExam" value="<?php echo $cptEvalCharges;?>" class="form-control" >	
											</div>
										</div>	
										<div class="col-sm-4">
											<label>CL Supply:</label>
											<div class="input-group">
												<div class="input-group-addon">
													<span class="glyphicon glyphicon-usd"></span>
												</div>
												 <input type="text" id="clSupply" name="clSupply" readonly value="<?php echo $clOrderRes[0]['clSupply'];?>" class="form-control" >
											</div>
										</div>	
										<div class="col-sm-4">
											<label>Total:</label>
											<div class="input-group">
												<div class="input-group-addon">
													<span class="glyphicon glyphicon-usd"></span>
												</div>
												<input type="text" id="totalCharges" readonly name="totalCharges" value="<?php echo $clOrderRes[0]['totalCharges'];?>" class="form-control" >
											</div>
										</div>	
									</div>	
								</div>		
							</div>	
						</div>

						<?php /* Type Content */ ?>
						<div class="col-sm-12 pt10">
							<div class="row">
								<table class="table table-condensed table-bordered">
									<tr class="grythead">
										<th class="od">&nbsp;</th>
										<th class="od">&nbsp;</th> 
										<th>Type</th>
										<th>Lens&nbsp;Code</th>
										<th>Color</th>
										<th>Price</th>
										<th>Qty</th>  
										<th class="text-nowrap">Sub Total</th> 
										<th>Discount</th> 
										<th>Total</th>
										<th>Ins.</th> 
										<th>Balance</th> 
									</tr>
									<?php
										$odLen = sizeof($LensBoxOD);
										for($i=0; $i< $odLen; $i++){  
										if($arrPrintOD[$i]['qty']<=0) {$arrPrintOD[$i]['qty']=1; }
									?>							
										<tr id="typeTrOD<?php echo $i;?>">
										  <td class="od">
											<?php
											if($i < ($odLen-1))
											{
											?>
												<!--<img id="imgOD<?php echo $i;?>" class="link_cursor" src="../../library/images/closebut.png" alt="Delete Row" title="Delete Row" onClick="removeTableRow('typeTrOD<?php echo $i;?>');">-->
												<span id="imgOD<?php echo $i;?>" class="glyphicon glyphicon-remove btn_add_pc_mr link_cursor" title="Delete Row" onClick="removeTableRow('typeTrOD<?php echo $i;?>');"></span>
											<?php
											}
											else
											{
											?>
												<!--<img id="imgOD<?php echo $i;?>" class="link_cursor" src="../../library/images/addinput.png" alt="Add More" title="Add More" onClick="addNewRow('od', 'typeTrOD', 'imgOD', '<?php echo $i;?>');">-->
												<span id="imgOD<?php echo $i;?>" class="glyphicon glyphicon-plus btn_add_pc_mr btn_add_pc_mr2 link_cursor" title="Add More" onClick="addNewRow('od', 'typeTrOD', 'imgOD', '<?php echo $i;?>');"></span>
											<?php
											}
											?>
										  </td>
											<td class="od">
												<label>&nbsp;OD</label>
												<input type="hidden" name="ordODId<?php echo $i;?>" id="ordODId<?php echo $i;?>" value="<?php echo $arrPrintOD[$i]['orDetId'].'~'.$arrPrintOD[$i]['chldIdOD'];?>" />
												<input type="hidden" name="cl_det_idOD<?php echo $i;?>" id="cl_det_idOD<?php echo $i;?>" value="<?php echo $clwsid_ArrOD[$i];?>" />
												<input type="hidden" name="cl_det_dis_idOD<?php echo $i;?>" id="cl_det_Dis_idOD<?php echo $i;?>" value="<?php echo $arrPrintOD[$i]['discount_table_id'];?>" />
											</td> 
											<td>
												<input type="text" name="LensBoxOD<?php echo $i;?>" id="LensBoxOD<?php echo $i;?>" class="typeAhead form-control" value="<?php echo $LensBoxOD[$i];?>" onFocus="set_typeahead(this)" data-sort="contain" />
												<input type="hidden" name="LensBoxOD<?php echo $i;?>ID" id="LensBoxOD<?php echo $i;?>ID" value="<?php echo $arrPrintOD[$i]['LensBoxOD_ID'];?>" funVars="printOrder~od~<?php echo $i;?>" >
											</td>
											<td>
												<select name="lensNameIdList<?php echo $i;?>" id="lensNameIdList<?php echo $i;?>" class="selectpicker" data-width="100%" data-size="5" data-title="Select">
													<?php echo lenseCodes($arrLensCode, $arrPrintOD[$i]['code']); ?>
												</select>
											</td>
											<td>
												<select name="colorNameIdList<?php echo $i;?>" id="colorNameIdList<?php echo $i;?>" class="selectpicker" data-width="100%" data-size="5" data-title="Select">
													<?php echo lenseColors($arrLensColor, $arrPrintOD[$i]['color']);?>
												</select>
											</td>
																							
											<td>
												<input onChange="javascript:calcTotalBalODFn('<?php echo $i;?>',this);justify2Decimal(this);" id="PriceOD<?php echo $i;?>" type="text" name="PriceOD<?php echo $i;?>" value="<?php echo $arrPrintOD[$i]['price']; ?>" class="form-control">
											</td> 
											<td>
												<input onChange="javascript:calcTotalBalODFn('<?php echo $i;?>',this);" type="text" name="QtyOD<?php echo $i;?>" value="<?php echo $arrPrintOD[$i]['qty'];?>" class="form-control"  id="QtyOD<?php echo $i;?>" />
											</td> 
											<td>
												<input onChange="javascript:calcTotalBalODFn('<?php echo $i;?>',this);justify2Decimal(this);" class="form-control" type="text" name="SubTotalOD<?php echo $i;?>" id="SubTotalOD<?php echo $i;?>" value="<?php echo $arrPrintOD[$i]['subTotal'];?>" >
											</td> 
											<td class="text-left">
												<input onChange="javascript:calcTotalBalODFn('<?php echo $i;?>',this);justify2Decimal(this);"  id="DiscountOD<?php echo $i;?>" type="text" class="form-control" name="DiscountOD<?php echo $i;?>" value="<?php echo $arrPrintOD[$i]['discount'];?>">
											</td> 
											<td>
												<input onChange="javascript:calcTotalBalODFn('<?php echo $i;?>');justify2Decimal(this);"  type="text" class="form-control" name="TotalOD<?php echo $i;?>" id="TotalOD<?php echo $i;?>" value="<?php echo $arrPrintOD[$i]['total'];?>">
											</td>
											<td>
												<input onChange="javascript:calcTotalBalODFn('<?php echo $i;?>');justify2Decimal(this);" class="form-control" type="text" name="InsOD<?php echo $i;?>" id="InsOD<?php echo $i;?>" value="<?php echo $arrPrintOD[$i]['insurance'];?>" >
											</td> 
											<td>
												<input onChange="javascript:calcTotalBalODFn('<?php echo $i;?>');justify2Decimal(this);" class="form-control" type="text" name="BalanceOD<?php echo $i;?>" id="BalanceOD<?php echo $i;?>" value="<?php echo $arrPrintOD[$i]['balance'];?>"/>
											</td> 
										</tr>
								   <?php }
									if($odLen==0){ ?>
										<tr id="typeTrOD0">
											<td class="od">
												<!--<img src="../../library/images/addinput.png" alt="Add More" class="link_cursor" id="imgOD0" title="Add Row" onClick="addNewRow('od', 'typeTrOD', 'imgOD', '0');">-->
												<span id="imgOD0" class="glyphicon glyphicon-plus btn_add_pc_mr btn_add_pc_mr2 link_cursor" title="Add Row" onClick="addNewRow('od', 'typeTrOD', 'imgOD', '0');"></span>
											</td>
											<td class="od">
												<label>&nbsp;OD</label>
												<input type="hidden" name="ordODId0" id="ordODId0" value="~" />
											</td> 
											<td>
												<input type="text" name="LensBoxOD0" id="LensBoxOD0" class="typeAhead form-control" value="" onFocus="set_typeahead(this)" />
												<input type="hidden" name="LensBoxOD0ID" id="LensBoxOD0ID" value="" funVars="printOrder~od~0">
											</td>
											<td>
												<select name="lensNameIdList0" id="lensNameIdList0" class="selectpicker" data-width="100%" data-size="5" data-title="Select">
													<?php echo lenseCodes($arrLensCode, ''); ?>
												</select>
											</td>
											<td>
												<select name="colorNameIdList0" id="colorNameIdList0" class="selectpicker" data-width="100%" data-size="5" data-title="Select">
													<?php echo lenseColors($arrLensColor, '');?>
												</select>
											</td>
											<td>
												<input onChange="javascript:calcTotalBalODFn(0);justify2Decimal(this);"  id="PriceOD0" type="text" name="PriceOD0" value="" class="form-control">
											</td> 
											<td>
												<input onChange="javascript:calcTotalBalODFn(0);" type="text" name="QtyOD0" value="1" class="form-control"  id="QtyOD0" />
											</td> 
											<td>
												<input onChange="javascript:calcTotalBalODFn(0);justify2Decimal(this);" class="form-control" type="text" name="SubTotalOD0" id="SubTotalOD0" value="" >
											</td> 
											<td class="text-left">
												<input onChange="javascript:calcTotalBalODFn(0);justify2Decimal(this);"  id="DiscountOD0" type="text" class="form-control" name="DiscountOD0" value="">
											</td> 
											<td>
												<input onChange="javascript:calcTotalBalODFn(0);justify2Decimal(this);"  type="text" class="form-control" name="TotalOD0" id="TotalOD0" value="">
											</td>
											<td>
												<input onChange="javascript:calcTotalBalODFn(0);justify2Decimal(this);" class="form-control" type="text" name="InsOD0" id="InsOD0" value="" >
											</td> 
											<td>
												<input onChange="javascript:calcTotalBalODFn(0);justify2Decimal(this);" class="form-control" type="text" name="BalanceOD0" id="BalanceOD0" value="" />
											</td> 
										</tr>
								<?php
									}
									$osLen = sizeof($LensBoxOS);
									for($i=0; $i< $osLen; $i++){
										if($arrPrintOS[$i]['qty']<=0) {$arrPrintOS[$i]['qty']=1; }	?>	                       
										<tr id="typeTrOS<?php echo $i;?>">
											<td class="os">
												<?php
												if($i < ($osLen-1))
												{
												?>
													<!--<img id="imgOS<?php echo $i;?>" class="link_cursor" src="../../library/images/closebut.png" alt="Delete Row" title="Delete Row" onClick="removeTableRow('typeTrOS<?php echo $i;?>');">-->
													<span id="imgOS<?php echo $i;?>" class="glyphicon glyphicon-remove btn_add_pc_mr link_cursor" title="Delete Row" onClick="removeTableRow('typeTrOS<?php echo $i;?>');"></span>
												<?php
												}
												else
												{
												?>
													<!--<img src="../../library/images/addinput.png" alt="Add More" class="link_cursor" id="imgOS<?php echo $i;?>" title="Add Row" onClick="addNewRow('os', 'typeTrOS', 'imgOS', '<?php echo $i;?>');">-->
													<span id="imgOS<?php echo $i;?>" class="glyphicon glyphicon-plus btn_add_pc_mr btn_add_pc_mr2 link_cursor" title="Add Row" onClick="addNewRow('os', 'typeTrOS', 'imgOS', '<?php echo $i;?>');"></span>
												<?php
												}
												?>
											</td>
											<td class="os">
												<label>&nbsp;OS</label>
												<input type="hidden" name="ordOSId<?php echo $i;?>" id="ordOSId<?php echo $i;?>" value="<?php echo $arrPrintOS[$i]['orDetId'].'~'.$arrPrintOS[$i]['chldIdOS'];?>" />
												<input type="hidden" name="cl_det_idOS<?php echo $i;?>" id="cl_det_idOS<?php echo $i;?>" value="<?php echo $clwsid_ArrOS[$i];?>" />
												<input type="hidden" name="cl_det_dis_idOS<?php echo $i;?>" id="cl_det_Dis_idOS<?php echo $i;?>" value="<?php echo $arrPrintOS[$i]['discount_table_id'];?>" />                                
											</td> 
											<td>
												<input type="text" name="LensBoxOS<?php echo $i;?>" id="LensBoxOS<?php echo $i;?>" class="typeAhead form-control" value="<?php echo $LensBoxOS[$i];?>" onFocus="set_typeahead(this)" data-sort="contain" />
												<input type="hidden" name="LensBoxOS<?php echo $i;?>ID" id="LensBoxOS<?php echo $i;?>ID" value="<?php echo $arrPrintOS[$i]['LensBoxOS_ID'];?>" funVars="printOrder~os~<?php echo $i;?>">												
											</td>
											<td>
												<select name="lensNameIdListOS<?php echo $i;?>" id="lensNameIdListOS<?php echo $i;?>" class="selectpicker" data-width="100%" data-size="5" data-title="Select">
												   <?php echo lenseCodes($arrLensCode, $arrPrintOS[$i]['code']); ?>
												</select>
											</td>
											<td>
												<select name="colorNameIdListOS<?php echo $i;?>" id="colorNameIdListOS<?php echo $i;?>" class="selectpicker" data-width="100%" data-size="5" data-title="Select">
												  <?php echo lenseColors($arrLensColor, $arrPrintOS[$i]['color']);?>                                      
												</select>
											</td>
											<td>
												<input onChange="javascript:calcTotalBalOSFn('<?php echo $i;?>');justify2Decimal(this);"  id="PriceOS<?php echo $i;?>" type="text" name="PriceOS<?php echo $i;?>" value="<?php echo $arrPrintOS[$i]['price']; ?>" class="form-control" >
											</td> 
											<td>
												<input onChange="javascript:calcTotalBalOSFn('<?php echo $i;?>');" type="text" name="QtyOS<?php echo $i;?>" value="<?php echo $arrPrintOS[$i]['qty'];?>" class="form-control" id="QtyOS<?php echo $i;?>" />
											</td> 
											<td>
												<input onChange="javascript:calcTotalBalOSFn('<?php echo $i;?>');justify2Decimal(this);" class="form-control" type="text" name="SubTotalOS<?php echo $i;?>" id="SubTotalOS<?php echo $i;?>" value="<?php echo $arrPrintOS[$i]['subTotal'];?>">
											</td> 
											<td class="text-left">
												<input onChange="javascript:calcTotalBalOSFn('<?php echo $i;?>');justify2Decimal(this);" id="DiscountOS<?php echo $i;?>" type="text" class="form-control " name="DiscountOS<?php echo $i;?>" value="<?php echo $arrPrintOS[$i]['discount'];?>">
											</td> 
											<td>
												<input onChange="javascript:calcTotalBalOSFn('<?php echo $i;?>');justify2Decimal(this);"  id="TotalOS<?php echo $i;?>" type="text" class="form-control" name="TotalOS<?php echo $i;?>" value="<?php echo $arrPrintOS[$i]['total'];?>">
											</td>
											<td>
												<input onChange="javascript:calcTotalBalOSFn('<?php echo $i;?>');justify2Decimal(this);" class="form-control" type="text" name="InsOS<?php echo $i;?>" id="InsOS<?php echo $i;?>" value="<?php echo $arrPrintOS[$i]['insurance'];?>">
											</td> 
											<td>
												<input onChange="javascript:calcTotalBalOSFn('<?php echo $i;?>');justify2Decimal(this);" class="form-control" type="text" name="BalanceOS<?php echo $i;?>" id="BalanceOS<?php echo $i;?>" value="<?php echo $arrPrintOS[$i]['balance'];?>" />
											</td> 
										</tr>
							<?php 	}
									if($osLen==0){ ?>
										<tr id="typeTrOS0">
											<td class="os">
												<!--<img src="../../library/images/addinput.png" alt="Add More" class="link_cursor" id="imgOS0" title="Add Row" onClick="addNewRow('os', 'typeTrOS', 'imgOS', '0');">-->
												<span class="glyphicon glyphicon-plus btn_add_pc_mr btn_add_pc_mr2 link_cursor" id="imgOS0" title="Add Row" onClick="addNewRow('os', 'typeTrOS', 'imgOS', '0');"></span>
											</td>
											<td class="os">&nbsp;OS
												<input type="hidden" name="ordOSId0" id="ordOSId0" value="~" />
											</td> 
											<td>
												<input type="text" name="LensBoxOS0" id="LensBoxOS0" class="typeAhead form-control" onFocus="set_typeahead(this)" value="" />
												<input type="hidden" name="LensBoxOS0ID" id="LensBoxOS0ID" value="" funVars="printOrder~os~0">                                                            
											</td>                                
											<td>
												<select name="lensNameIdListOS0" id="lensNameIdList0" class="selectpicker" data-width="100%" data-size="5" data-title="Select">
												   <?php echo lenseCodes($arrLensCode, ''); ?>
												</select>
											</td>
											<td>
												<select name="colorNameIdListOS0" id="colorNameIdList0" class="selectpicker" data-width="100%" data-size="5" data-title="Select">
												  <?php echo lenseColors($arrLensColor, '');?>                                      
												</select>
											</td>
											<td>
												<input onChange="javascript:calcTotalBalOSFn(0);justify2Decimal(this);"  id="PriceOS0" type="text" name="PriceOS0" value="" class="form-control" >
											</td> 
											<td>
												<input onChange="javascript:calcTotalBalOSFn(0);" type="text" name="QtyOS0" value="1" class="form-control"  id="QtyOS0" />
											</td> 
											<td>
												<input onChange="javascript:calcTotalBalOSFn(0);justify2Decimal(this);" class="form-control" type="text" name="SubTotalOS0" id="SubTotalOS0" value="" >
											</td> 
											<td class="text-left">
												<input onChange="javascript:calcTotalBalOSFn(0);justify2Decimal(this);" id="DiscountOS0" type="text" class="form-control " name="DiscountOS0" value="" >
											</td> 
											<td>
												<input onChange="javascript:calcTotalBalOSFn(0);justify2Decimal(this);"  id="TotalOS0" type="text" class="form-control " name="TotalOS0" value="">
											</td>
											<td>
												<input onChange="javascript:calcTotalBalOSFn(0);justify2Decimal(this);" class="form-control" type="text" name="InsOS0" id="InsOS0" value="" >
											</td> 
											<td>
												<input onChange="javascript:calcTotalBalOSFn(0);justify2Decimal(this);" class="form-control" type="text" name="BalanceOS0" id="BalanceOS0" value="" />
											</td> 
										</tr>
							<?php 	}	?>	
								</table>
							</div>			
						</div>	
					</div>
				</div>

				<?php /* Message Section */ ?>
		
					<div class="row">
						<div class="col-sm-4">
							<label>Message To:</label>
							<select name="physicians[]" id="physicians" class="selectpicker" data-width="100%" data-size="5" data-title="Please select" multiple>
								<?php
									echo $physicianOptions;
								?> 
							</select> 	
						</div>

						<div class="col-sm-3 col-sm-offset-1">
							<div class="row">
								<div class="col-sm-12">
									<div class="checkbox">
										<input type="checkbox" onClick="javascript:checkSingle('ptPickUpAtFrontDeskId','checkBoxShipToHome');document.getElementById('ShipToHomeAddress').value='';" id="ptPickUpAtFrontDeskId" name="checkBoxShipToHome" value="PtPickYes" <?php if($clOrderRes[0]['checkBoxShipToHomeAddress']=='PtPickYes' || $clws_type=='Current Trial') { echo 'checked'; }?>>
										<label for="ptPickUpAtFrontDeskId">Collect From Office</label>	
									</div>
								</div>
								<?php if(!trim($ShipToHomeAddress)) { 
									$ShipToHomeAddress = $row_address['fname'].' '.$row_address['lname'].' - '.$row_address['id'].',\n';
									$ShipToHomeAddress.=trim(stripslashes($row_address['street'])).'\n';
									$ShipToHomeAddress.=trim(stripslashes($row_address['city'].", ".$row_address['state']." ".$row_address['postal_code']));
									$ShipToHomeAddress.='-'.$row_address['zip_ext'];
								}?>
								<div class="col-sm-12">
									<div class="checkbox">
										<input type="checkbox" name="checkBoxShipToHome" id="checkBoxShipToHomeAddress" value="HomeAddressYes" onClick="javascript:checkSingle('checkBoxShipToHomeAddress','checkBoxShipToHome');shipHomeAddrFn(this,'ShipToHomeAddress','<?php echo addcslashes($ShipToHomeAddress,"\0..\37!@\177..\377");?>');" <?php if($clOrderRes[0]['checkBoxShipToHomeAddress']=='HomeAddressYes') { echo 'checked'; }?>>
										<label for="checkBoxShipToHomeAddress">Ship To: Home Address</label>
									</div>
								</div>	
							</div>	
						</div>

						<div class="col-sm-4">
							<textarea name="ShipToHomeAddress" id="ShipToHomeAddress" class="form-control" rows="2"><?php echo stripslashes($clOrderRes[0]['ShipToHomeAddress']);?></textarea>	
						</div>	
					</div>	
					<div class="row pt10">
						<div class="col-sm-12">
							<label>Message Comments:</label>
							<textarea name="messageComments" id="messageComments" class="form-control" rows="1"><?php echo stripslashes($clOrderRes[0]['messageComments']);?></textarea>	
						</div>	
					</div>
			
				
				<?php /* Date Ordered Section */ ?>
			
					<div class="row">
						<div class="col-sm-1 form-inline">
							<label>Date Ordered</label>
						</div>
						<div class="col-sm-2 form-inline">
							<select name="OrderedTrialSupply" id="OrderedTrialSupplyId" class="selectpicker" data-width="80%" data-size="5" data-title="Please select">
								<option value="Trial" <?php if($clOrderRes[0]['OrderedTrialSupply']=='Trial') { echo 'selected'; }?>>Trial</option>
								<option value="Supply" <?php if($clOrderRes[0]['OrderedTrialSupply']=='Supply') { echo 'selected'; }?>>Supply</option>
							</select>
						</div>
						<div class="col-sm-3">
							<div class="input-group">
								<input type="text" class="form-control date-pick" name="dateOrdered" id="dateOrdered" value="<?php echo displayDateFormat($clOrderRes[0]['dateOrdered']);?>" onChange="chkDateFrmat(this);">
								<label for="dateOrdered" class="input-group-addon">
									<span class="glyphicon glyphicon-calendar"></span>
								</label>	
							</div>
						</div>	
						<div class="col-sm-6">
							<textarea name="OrderedComment" id="OrderedComment" class="form-control" rows="1"><?php echo $clOrderRes[0]['OrderedComment'];?></textarea>	
						</div>		
					</div>	
			

				<?php /* Date Received Section */ ?>
			
					<div class="row">
						
						
						<div class="col-sm-1 form-inline">
							<label>Date Received</label>
						</div>
						<div class="col-sm-2 form-inline">
							<select name="ReceivedTrialSupply" id="ReceivedTrialSupplyId" class="selectpicker" data-width="80%" data-size="5" data-title="Please select">
								<option value="Trial" <?php if($clOrderRes[0]['ReceivedTrialSupply']=='Trial') { echo 'selected'; }?>>Trial</option>
								<option value="Supply" <?php if($clOrderRes[0]['ReceivedTrialSupply']=='Supply') { echo 'selected'; }?>>Supply</option>
							</select>
						</div>	
						
						
						
						<div class="col-sm-3">
							<div class="input-group">
								<input type="text" name="dateReceived" id="dateReceived" class="form-control date-pick" value="<?php echo displayDateFormat($clOrderRes[0]['dateReceived']);?>" onChange="chkDateFrmat(this);">
								<label for="dateReceived" class="input-group-addon">
									<span class="glyphicon glyphicon-calendar"></span>
								</label>	
							</div>
						</div>	
						<div class="col-sm-6">
							<textarea name="ReceivedComment" id="ReceivedComment" class="form-control" rows="1" ><?php echo $clOrderRes[0]['ReceivedComment'];?></textarea>
						</div>		
					</div>	
			
				
				<?php /* Date Notified Section */ ?>
				
					<div class="row">
						<div class="col-sm-3 form-inline">
							<label>Date Notified</label>
						</div>	
						<div class="col-sm-3">
							<div class="input-group">
								<input type="text" name="dateNotified" id="dateNotified" class="form-control date-pick" value="<?php echo displayDateFormat($clOrderRes[0]['dateNotified']);?>" onChange="chkDateFrmat(this);">
								<label for="dateNotified" class="input-group-addon">
									<span class="glyphicon glyphicon-calendar"></span>
								</label>	
							</div>
						</div>	
						<div class="col-sm-6">
							<textarea name="NotifiedComment" id="NotifiedComment" class="form-control" rows="1"><?php echo $clOrderRes[0]['NotifiedComment'];?></textarea>
						</div>		
					</div>	
				
				
				<?php /* Date Picked Up Section */ ?>
				
					<div class="row">
						<div class="col-sm-1 form-inline">
							<label>Date Picked Up</label>
						</div>
						<div class="col-sm-2 form-inline">
							<select name="PickedUpTrialSupply" id="PickedUpTrialSupplyId" class="selectpicker" data-width="80%" data-size="5" data-title="Please select">
								<option value="Trial" <?php if($clOrderRes[0]['PickedUpTrialSupply']=='Trial') { echo 'selected'; }?>>Trial</option>
								<option value="Supply" <?php if($clOrderRes[0]['PickedUpTrialSupply']=='Supply') { echo 'selected'; }?>>Supply</option>
							</select>
						</div>	
						<div class="col-sm-3">
							<div class="input-group">
								<input type="text" name="datePickedUp" id="datePickedUp" class="form-control date-pick" value="<?php echo displayDateFormat($clOrderRes[0]['datePickedUp']);?>" onChange="chkDateFrmat(this);">
								<label for="dateNotified" class="input-group-addon">
									<span class="glyphicon glyphicon-calendar"></span>
								</label>	
							</div>
						</div>	
						<div class="col-sm-6">
							<textarea name="PickedUpComment" id="PickedUpComment" class="form-control" rows="1" ><?php echo $clOrderRes[0]['PickedUpComment'];?></textarea>
						</div>		
					</div>	
			

				<?php /* Order Status Section */ ?>
				
					<div class="row">
						<div class="col-sm-1">
							<label>Order Status</label>
						</div>		
						<div class="col-sm-11">
							<select class="selectpicker"  name="orderStatus" id="orderStatus" data-width="100%" data-size="5">
								<?php 
									foreach($statusOptions as $key=>$val){
										$sel="";
										if($clOrderRes[0]['order_status']==$val){$sel="selected";}
										echo "<option value='$val' $sel >$val</option>";
									}
								?>
							</select>
						</div>		
					</div>
			
				<div class="clearfix"></div>
				<?php /* Footer Buttons */ ?>
				
				
	</div>
	<div class="" style="bottom:0px; width:100%; position:relative;">
		<div class="col-sm-12 pt10 text-center" style="padding-top:0px;">
			<?php
			$txtTotOD = (sizeof($LensBoxOD)==0) ? 1 : sizeof($LensBoxOD);
			$txtTotOS = (sizeof($LensBoxOS)==0) ? 1 : sizeof($LensBoxOS);
			$txtTotOU = 1;
			?>
			<input type="hidden" name="txtTotOD" id="txtTotOD" value="<?php echo $txtTotOD;?>">
			<input type="hidden" name="txtTotOS" id="txtTotOS" value="<?php echo $txtTotOS;?>">
			<input type="hidden" name="txtTotOU" id="txtTotOU" value="<?php echo $txtTotOU;?>">	
			<input type="button"  class="btn btn-success" name="SaveBtn" id="SaveBtn"  value="Save" onClick="checkPriceQty();" />
			<input type="button"  class="btn btn-primary" id="SaveEnterCharges" name="SaveEnterCharges"  value="Save & Enter Charges" onClick="checkPriceQty('enterCharges');"/>
			<input type="button"  class="btn btn-primary" id="PrintBtn"  value="Print" onClick="printOrderPdfFn('<?php echo $print_order_id;?>');" />
			<input type="button"  class="btn btn-danger"  id="CancelBtn" value="Close" onClick="window.close();">
		</div>	
	</div>
	</div>
</form>    
<script>
hideDisplayOrderFn('orderTrialDspDoctorId','orderTrialId','orderTrialDspNumberId','orderTrialNumberId');
shipHomeAddrFn(document.getElementById('checkBoxShipToHomeAddress'),'ShipToHomeAddress','<?php echo addcslashes($clOrderRes[0]['ShipToHomeAddress'],"\0..\37!@\177..\377");?>');
<?php 
if($_REQUEST["recordSavePrint"]=="Yes"){?>
	 printOrderPdfFn('<?php echo $print_order_id;?>');
<?php }?>

$('input[id^="PriceOD"').each(function(index, element) {
	if($(element).attr('id')){
		calcTotalBalODFn(index);
	}
});
$('input[id^="PriceOS"').each(function(index, element) {
	if($(element).attr('id')){
		calcTotalBalOSFn(index);
	}
});

</script>
<?php
//START CODE TO DISABLE CHECKBOXES OF ORDER SUPPLY IF FINAL WORKSHEET DOES NOT EXIST
if($finalWrkshtExist=='no') {
?>
<script>
	if(document.getElementById('orderSupplyId')) {
		document.getElementById('orderSupplyId').disabled=true;
	}
	if(document.getElementById('reOrderSupplyId')) {
		document.getElementById('reOrderSupplyId').disabled=true;
	}	
</script>
<?php	
}
//END CODE TO DISABLE CHECKBOXES OF ORDER SUPPLY IF FINAL WORKSHEET DOES NOT EXIST

include 'simpleMenuContent.php';
?>
<script type="text/javascript">
var clSupplyCharge = '';
checkInsCase(document.getElementById('ins_case').value,'<?php echo $auth_number;?>','<?php echo $auth_amount;?>');
function setOrderAsTrial(id){
	var priceOD = $("#PriceOD0").val();
	var priceOS = $("#PriceOS0").val();
	if($('#'+id).is(':checked')){
		$('#SaveEnterCharges').hide();
		//$('#clExam').val(0);
		$('#clSupply').val('0.00');
		$('#totalCharges').val($('#clExam').val());
		$('input[id^="PriceOD"],input[id^="PriceOS"],input[id^="InsOD"],input[id^="InsOS"],input[id^="DiscountOD"],input[id^="DiscountOS"]').each(function(index, element) {
            $(this).val(0);
						$(this).trigger("change");
						if($(this).attr('id').indexOf('Price')!='-1'){
							$(this).css('background-color', '#CCC');
							$(this).attr("readonly", true);
						}
        });
	}else{
		$('#clSupply').val(clSupplyCharge);
		var totalChanges = parseFloat(parseFloat($('#clExam').val()) + parseFloat($('#clSupply').val()));
		totalChanges = totalChanges.toFixed(2);
		$('#totalCharges').val(totalChanges);
		$('#SaveEnterCharges').show();
		$('input[id^="PriceOD"],input[id^="PriceOS"]').each(function(index, element) {
			if($(this).attr('id').indexOf('Price')!='-1'){
				$(this).css('background-color', '#FFF');
				$(this).attr("readonly", false);
			}
        });
	}
}
var manufac_id = 0;
$('.typeAhead').each(function(id,elem){
	$(elem).typeahead();
});

/** Set prices of lens from typeahead **/
	function set_typeahead(ths,set_typeahead){
		if(set_typeahead){
			$(ths).typeahead();
			$(ths).removeAttr('onFocus');
			$(ths).attr('onFocus','set_typeahead(this)');
		}
		id=$(ths).attr('id');
        odos='';
		if(id.indexOf('OD')!='-1'){
			num=id.replace('LensBoxOD','');	odos='OD';
		}else{
			num=id.replace('LensBoxOS','');	odos='OS';
		}
		
		$('#'+id).data('typeahead').source = arrManufac;
		$('#'+id).data('typeahead').updater = function(item){
			//alert(item);
			manufac_id = arrManufacId[item];
			var odos_val = odos+'~~'+num;
			$('#typeDiv').css('opacity','0.4');
			$.ajax({
				url:'<?php echo $_SERVER['PHP_SELF']; ?>',
				type:'POST',
				data:'manufac_id='+manufac_id+'&get_manufac_details=yes',
				success:function(response){
					$('#'+id+'ID').val(manufac_id);
					$('#Price'+odos+num).val("" + cptFee[manufac_id]);
					//return;
					arrArgu=odos_val.split('~~');
					if(arrArgu[0]) { odos=arrArgu[0];}
					if(arrArgu[1]) { num=arrArgu[1];}
					$('#Price'+odos+num).val("" + clPriceArrayJS[manufac_id]);
					if(odos=='OD'){
						calcTotalBalODFn(num, '');
					}else{
						calcTotalBalOSFn(num);
					}
					$(ths).val(item);
					$('#typeDiv').css('opacity','1');
					//$('#Price'+odos+num).val(cptFee["'" + response + "'"]);
					//alert("item: " + item);
					//if(firstTime == 0){
						//alert("manufac_id: " + manufac_id);
						//$('#Price'+odos+num).val("" + cptFee[manufac_id]);
						
					//}
				}
			});
			
		};	
	}	

$(function(){		
	$('[data-toggle="tooltip"]').tooltip();
	$('body').on('focus','.typeAhead',function(){
		$(this).typeahead();
	});
	
	$('body').on('click','table',function(){
		$(this).selectpicker('refresh');
	});
	
	
	
	$('.date-pick').datetimepicker({
		timepicker:false,
		format:global_date_format,
		formatDate:'Y-m-d',
		scrollInput:false
	});
});	
	
$(document).ready(function(){
	if($('#trial_order').attr('checked')){
		setOrderAsTrial('trial_order');
	}
	firstTime = 0;
	clSupplyCharge = $('#clSupply').val();
	//alert("Page load");
	//$('#PriceOD0').val("" + cptFee[manufac_id]);
	<?php foreach($clPriceArray as $makeId => $cptFee){ ?>
		clPriceArrayJS["<?php echo $makeId; ?>"] = '<?php echo $cptFee; ?>';
	<?php } ?>
	epost_addTypeAhead('#lensComment, #messageComments','<?php echo $GLOBALS['rootdir'];?>');
});
</script>
</body>
</html>