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
/*
FILE : DETAILPRODUCTIVITY.PHP
PURPOSE :  PRODUCTVITY PHYSICIAN DETAIL REPORT
ACCESS TYPE : INCLUDED
*/
//ob_start();

$providerIdArr = array_keys($mainResArr);
$pdf_page_content = NULL;
$csvFileData = NULL;
$grandTotalAmtArr = array();

$chart_j = $k = $m = $n = 0; 
$chartData = array();
$chartProviderId = array();
$chartFacilityId = array();
$chartFacilityName = array();
$chartFacilityNId = array();
$chartProviderNId = array();

$colspan=19;
$colspanPdf=15;
$startColspan=8;
$startColspanPDF=5;

$tdTotal='';

//CSV FILE NAME
//$csv_file_name = "new_html2pdf/tmp/transaction_details".session_id().".csv";
if(file_exists($csv_file_name)){
	unlink($csv_file_name);
}
$csv_file_name;
$fp = fopen ($csv_file_name, 'a+') or die();

$arr=array();

$arr[]="Physician";
$arr[]="Rendering Facility";
$arr[]="Home Facility";
$arr[]="Patient ID";
$arr[]="Enc. #";
$arr[]="CPT Code";
$arr[]="CPT Code Description";
$arr[]="DOS";
$arr[]="Insurance";
$arr[]="Units";
$arr[]="Total Charges";
$arr[]="Allowed Amount";
$arr[]="Pat. Paid";
$arr[]="Ins. Paid";
$arr[]="Total Payment";
$arr[]="Credit";
$arr[]="Adjustment";
$arr[]="Pat. Due";
$arr[]="Ins. Due";
$arr[]="Balance";


fputcsv($fp,$arr, ",","\"");
$fp = fopen ($csv_file_name, 'a+');


//WITHOUT GROUP BY DEPARTMENT
foreach($mainResArr as $firstGrpId => $firstGrpData){	
	$arrFirstGrpTotal = array();
	
	$firstGrpName = $providerNameArr[$firstGrpId];

	foreach($firstGrpData as $secGrpId => $secGrpData){
		$subTotalAmtArr = array();
		$arrSecGrpTotal = array();

		$secGrpName = $arrAllFacilities[$secGrpId];

		foreach($secGrpData as $chgDetId => $encDataArr){

			$enc_id= $encDataArr["encounter_id"];
			$submitted = $encDataArr["submitted"];
			$first_posted_date = $encDataArr["first_posted_date"];
			$patient_id = $encDataArr['patient_id'];
			$date_of_service = $encDataArr['date_of_service'];
			$default_facility = $encDataArr['default_facility'];
			$primaryInsName  = $arrAllInsCompanies[$encDataArr['pri_ins_id']];
			$secondaryInsName = $arrAllInsCompanies[$encDataArr['sec_ins_id']];
			
			if($encDataArr["default_facility"]>0){
				$HostGrpName = $arrAllFacilities[$encDataArr["default_facility"]];	
			}else if($arrHomeFacOfPatients[$patient_id]){
				$pos_fac_id = $arrPosFacOfSchFac[$arrHomeFacOfPatients[$patient_id]];	
				$HostGrpName = $arrAllFacilities[$pos_fac_id];	
			}
			
			$insuranceName= $primaryInsName;
			if(empty($secondaryInsName)==false){
				$insuranceName.= ", ".$secondaryInsName;
			}
			
			if($default_facility<=0 || $default_facility==''){
				$default_facility = $arrHomeFacOfPatients[$patient_id];
			}

			//--- GET TOTAL AMOUNT ----		
			$totalAmtArr = array();		
			$write_off_amt_arr = array();
			$arrCPT = array();
			$arrCPT_CSV = array();
			$arrDxCodes = array();
			$totalBalance =$insuranceDue = $patientDue = $patPaidAmt= $insPaidAmt= $priPaidAmt= $secPaidAmt= 0;
			$patientPaidAmt = $crd_dbt_amt = $patCrdDbt = $insCrdDbt = $adj_amt = $write_off_amt= $creditProcAmount=0;
			$lastPaidDOT='';
			
			$patCD=$insCD=0; 

			$subTotalAmtArr["units"][] = $encDataArr['units'];
			$totalAmt=$encDataArr['totalAmt'];
			$subTotalAmtArr["totalAmt"][] = $totalAmt;

			$allowed_amt=  $encDataArr['approved_amt'];
			$subTotalAmtArr["allowed_amt"][] = $allowed_amt;
			
			//PAT & INS DUES
			$insuranceDue+= $encDataArr['pri_due'] + $encDataArr['sec_due'] + $encDataArr['tri_due'];
			$patientDue+= $encDataArr['pat_due'];
		
			//CREDIT/DEBIT
			$patCrdDbt= $pay_crd_deb_arr[$chgDetId]['Patient'];
			$insCrdDbt= $pay_crd_deb_arr[$chgDetId]['Insurance'];

			// TOTAL PAYMENT
			$patientPaidAmt+= $mainEncounterPayArr[$chgDetId] + ($patCrdDbt + $insCrdDbt);

			//WRITE-OFF & ADJUSTMENTS
			$adj_amt= $arrAdjustmentAmt[$chgDetId];
			$write_off_amt+= $writte_off_arr[$chgDetId]+$normalWriteOffAmt[$chgDetId];
			$adj_amt+=$write_off_amt;
				
				
			//CREDIT - OVER PAYMENT
			$creditProcAmount+= $encDataArr['over_payment'];

			//BALANCE
			$balAmt=0;
			if($encDataArr["proc_balance"]>0){
				$balAmt= $encDataArr['proc_balance'];
			}else{
				if($encDataArr['over_payment']>0){
					$balAmt= $encDataArr['proc_balance'] - $encDataArr['over_payment'];
				}else{
					$balAmt= $encDataArr['proc_balance'];
				}
			}
			$totalBalance+=$balAmt;

			// PATIENT PAID
			$patPaidAmt+= $patPayDetArr[$chgDetId]['patPaid'];
			// INSURANCE PAID
			$insPaidAmt+= $patPayDetArr[$chgDetId]['insPaid'] + $insCrdDbt;
			// PRI INSURANCE PAID
			if($patPayDetArr[$chgDetId]['priPaid']>0){
				$priPaidAmt+= $patPayDetArr[$chgDetId]['priPaid'] + $insCrdDbt;
				$insCrdDbt=0;
			}
			// SEC+TER INSURANCE PAID
			$secPaidAmt+= $patPayDetArr[$chgDetId]['secPaid'] + $patPayDetArr[$chgDetId]['terPaid'] + $insCrdDbt;

			$cptCode = $encDataArr['cpt_prac_code'];
			$arrCPT_CSV[$cptCode]=$cptCode;
			if(strlen($cptCode)>7){ $cptCode = substr($cptCode, 0, 7).'..';}
			$arrCPT[] = $cptCode;
	
			$insuranceDue = ($insuranceDue<0) ? 0 : $insuranceDue; 
			$subTotalAmtArr["insuranceDue"][] = $insuranceDue;
			$patientDue = ($patientDue<0) ? 0 : $patientDue;
			$patientDue = ($totalBalance<=0) ? 0 : $patientDue;
			
			$subTotalAmtArr["patientDue"][] = $patientDue;
			$subTotalAmtArr["totalBalance"][] = $totalBalance;

			$subTotalAmtArr["pat_paid_amt"][] = $patientPaidAmt;
			$subTotalAmtArr["patPaidAmt"][] = $patPaidAmt;
			$subTotalAmtArr["insPaidAmt"][] = $insPaidAmt;
			$subTotalAmtArr["priPaidAmt"][] = $priPaidAmt;
			$subTotalAmtArr["secPaidAmt"][] = $secPaidAmt;
			$subTotalAmtArr["creditProcAmount"][] = $creditProcAmount;
			$subTotalAmtArr["adj_amt"][] = $adj_amt;

			
			//--- CHANGE NUMBER FORMAT FOR ENCOUNTER ---
			$totalAmt = $CLSReports->numberFormat($totalAmt,2);
			$allowed_amt = $CLSReports->numberFormat($allowed_amt,2);
			$patientPaidAmt = $CLSReports->numberFormat($patientPaidAmt,2);
			$insuranceDue = $CLSReports->numberFormat($insuranceDue,2);
			$patientDue = $CLSReports->numberFormat($patientDue,2);		
			$creditProcAmount = $CLSReports->numberFormat($creditProcAmount,2);		
			$adj_amt = $CLSReports->numberFormat($adj_amt,2);
			$totalBalance = $CLSReports->numberFormat($totalBalance,2);
			$patPaidAmt = $CLSReports->numberFormat($patPaidAmt,2);
			$insPaidAmt = $CLSReports->numberFormat($insPaidAmt,2);
			
			$strCPT = implode(', ', $arrCPT);
			$strCPT_CSV = implode(', ', $arrCPT_CSV);
			$strDxCodes = implode(', ', $arrDxCodes);

			$arr=array();
			
			$arr[]=$firstGrpName;
			$arr[]=$secGrpName;
			$arr[]=$HostGrpName;
			$arr[]=$patient_id;
			$arr[]=$enc_id;
			$arr[]=$strCPT_CSV;
			$arr[]=$encDataArr['cpt_desc'];
			$arr[]=$date_of_service;
			$arr[]=$insuranceName;
			$arr[]=$encDataArr['units'];
			$arr[]=$totalAmt;
			$arr[]=$allowed_amt;
			$arr[]=$patPaidAmt;
			$arr[]=$insPaidAmt;
			$arr[]=$patientPaidAmt;
			$arr[]=$creditProcAmount;
			$arr[]=$adj_amt;
			$arr[]=$patientDue;
			$arr[]=$insuranceDue;
			$arr[]=$totalBalance;
			
			fputcsv($fp,$arr, ",","\"");	
		}


		// SECOND GROUP TOTAL
		$sub_total_units = array_sum($subTotalAmtArr['units']);
		$sub_total_amt = array_sum($subTotalAmtArr['totalAmt']);
		$sub_allowed_amt = array_sum($subTotalAmtArr['allowed_amt']);
		$sub_pat_paid_amt = array_sum($subTotalAmtArr['pat_paid_amt']);
		$sub_ins_due = array_sum($subTotalAmtArr['insuranceDue']);
		$sub_patient_due = array_sum($subTotalAmtArr['patientDue']);
		$sub_credit_amt = array_sum($subTotalAmtArr['creditProcAmount']);
		$sub_adj_amt = array_sum($subTotalAmtArr['adj_amt']);
		$sub_total_balance = array_sum($subTotalAmtArr['totalBalance']);
		$sub_patPaidAmt = array_sum($subTotalAmtArr['patPaidAmt']);
		$sub_insPaidAmt = array_sum($subTotalAmtArr['insPaidAmt']);
		$sub_priPaidAmt = array_sum($subTotalAmtArr['priPaidAmt']);
		$sub_secPaidAmt = array_sum($subTotalAmtArr['secPaidAmt']);


		//--- GET GRAND TOTAL AMIUNT ---
		$arrFirstGrpTotal["units"]+= $sub_total_units;
		$arrFirstGrpTotal["totalAmt"]+= $sub_total_amt;
		$arrFirstGrpTotal["allowed_amt"]+= $sub_allowed_amt;
		$arrFirstGrpTotal["pat_paid_amt"]+= $sub_pat_paid_amt;
		$arrFirstGrpTotal["insuranceDue"]+= $sub_ins_due;
		$arrFirstGrpTotal["patientDue"]+= $sub_patient_due;
		$arrFirstGrpTotal["creditProcAmount"]+= $sub_credit_amt;
		$arrFirstGrpTotal["adj_amt"]+= $sub_adj_amt;
		$arrFirstGrpTotal["totalBalance"]+= $sub_total_balance;			
		$arrFirstGrpTotal["patPaidAmt"]+= $sub_patPaidAmt;
		$arrFirstGrpTotal["insPaidAmt"]+= $sub_insPaidAmt;
		$arrFirstGrpTotal["priPaidAmt"]+= $sub_priPaidAmt;
		$arrFirstGrpTotal["secPaidAmt"]+= $sub_secPaidAmt;

	} // END SECOND GROUP
	
	
	// FIRST GROUP TOTAL
	$grandTotalAmtArr["totalAmt"]+= $arrFirstGrpTotal["totalAmt"];
	$grandTotalAmtArr["units"]+= $arrFirstGrpTotal["units"];
	$grandTotalAmtArr["allowed_amt"]+= $arrFirstGrpTotal["allowed_amt"];
	$grandTotalAmtArr["patPaidAmt"]+= $arrFirstGrpTotal["patPaidAmt"];
	$grandTotalAmtArr["insPaidAmt"]+= $arrFirstGrpTotal["insPaidAmt"];
	$grandTotalAmtArr["priPaidAmt"]+= $arrFirstGrpTotal["priPaidAmt"];
	$grandTotalAmtArr["secPaidAmt"]+= $arrFirstGrpTotal["secPaidAmt"];
	$grandTotalAmtArr["pat_paid_amt"]+= $arrFirstGrpTotal["pat_paid_amt"];	
	$grandTotalAmtArr["creditProcAmount"]+= $arrFirstGrpTotal["creditProcAmount"];
	$grandTotalAmtArr["adj_amt"]+= $arrFirstGrpTotal["adj_amt"];
	$grandTotalAmtArr["insuranceDue"]+= $arrFirstGrpTotal["insuranceDue"];
	$grandTotalAmtArr["patientDue"]+= $arrFirstGrpTotal["patientDue"];
	$grandTotalAmtArr["totalBalance"]+= $arrFirstGrpTotal["totalBalance"];	


	$arr=array();
	
	$arr[]="";
	$arr[]="";
	$arr[]="";
	$arr[]="";
	$arr[]="";
	$arr[]="";
	$arr[]="";
	$arr[]="";
	$arr[]="Total:";
	$arr[]=$arrFirstGrpTotal["units"];
	$arr[]=$CLSReports->numberFormat($arrFirstGrpTotal["totalAmt"],2);
	$arr[]=$CLSReports->numberFormat($arrFirstGrpTotal["allowed_amt"],2);
	$arr[]=$CLSReports->numberFormat($arrFirstGrpTotal["patPaidAmt"],2);
	$arr[]=$CLSReports->numberFormat($arrFirstGrpTotal["insPaidAmt"],2);
	$arr[]=$CLSReports->numberFormat($arrFirstGrpTotal["pat_paid_amt"],2);
	$arr[]=$CLSReports->numberFormat($arrFirstGrpTotal["creditProcAmount"],2);
	$arr[]=$CLSReports->numberFormat($arrFirstGrpTotal["adj_amt"],2);
	$arr[]=$CLSReports->numberFormat($arrFirstGrpTotal["patientDue"],2);
	$arr[]=$CLSReports->numberFormat($arrFirstGrpTotal["insuranceDue"],2);
	$arr[]=$CLSReports->numberFormat($arrFirstGrpTotal["totalBalance"],2);
	fputcsv($fp,$arr, ",","\"");		
}	



//-- OPERATOR INITIAL -------
$authProviderNameArr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
$opInitial = $authProviderNameArr[1][0];
$opInitial .= $authProviderNameArr[0][0];
$opInitial = strtoupper($opInitial);
$printFile = true;
$notPosted='';

$totalLabel='Total';
$showTotalAgain=0;

$otherTotals='';
$otherPayments=$otherWriteOff=$otherAdj=$otherCharges=$otherInsDue=$otherPatDue=$otherOverPayment=$otherBalance=0;

if(count($providerIdArr)>0){ 
	$arr=array();
	
	$arr[]="";
	$arr[]="";
	$arr[]="";
	$arr[]="";
	$arr[]="";
	$arr[]="";
	$arr[]="";
	$arr[]="";
	$arr[]="Grand Total:";
	$arr[]=$grandTotalAmtArr['units'];
	$arr[]=$CLSReports->numberFormat($grandTotalAmtArr["totalAmt"],2);
	$arr[]=$CLSReports->numberFormat($grandTotalAmtArr["allowed_amt"],2);
	$arr[]=$CLSReports->numberFormat($grandTotalAmtArr["patPaidAmt"],2);
	$arr[]=$CLSReports->numberFormat($grandTotalAmtArr["insPaidAmt"],2);
	$arr[]=$CLSReports->numberFormat($grandTotalAmtArr["pat_paid_amt"],2);
	$arr[]=$CLSReports->numberFormat($grandTotalAmtArr["creditProcAmount"],2);
	$arr[]=$CLSReports->numberFormat($grandTotalAmtArr["adj_amt"],2);
	$arr[]=$CLSReports->numberFormat($grandTotalAmtArr["patientDue"],2);
	$arr[]=$CLSReports->numberFormat($grandTotalAmtArr["insuranceDue"],2);
	$arr[]=$CLSReports->numberFormat($grandTotalAmtArr["totalBalance"],2);
	fputcsv($fp,$arr, ",","\"");	
} 
fclose ($fp);
?>
