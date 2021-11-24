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

$dateTitle=($DateRangeFor=='transaction_date') ? 'DOT' : 'DOP';

//CSV FILE NAME
//$csv_file_name = "new_html2pdf/tmp/transaction_details".session_id().".csv";
if(file_exists($csv_file_name)){
	unlink($csv_file_name);
}
$fp = fopen ($csv_file_name, 'a+');

$arr=array();

$arr[]="Physician";
$arr[]="Rendering Facility";
$arr[]="Home Facility";
$arr[]="Patient ID";
$arr[]="Enc. #";
$arr[]="DOS";
$arr[]="CPT Code";
$arr[]=$dateTitle;
$arr[]="Primary Insurance";
$arr[]="Secondary Insurance";
$arr[]="Charges";
$arr[]="Pat. Paid";
$arr[]="Ins. Paid";
$arr[]="Pri. Paid";
$arr[]="Sec. Paid";
$arr[]="Credit";
$arr[]="Write-Off";
$arr[]="Adjustment";
$arr[]="Refund";
$arr[]="Payment Mode";
$arr[]="Check#/EFT#";

fputcsv($fp,$arr, ",","\"");
$fp = fopen ($csv_file_name, 'a+');

foreach($mainResArr as $firstGrpId => $firstGrpData){	
	$arrFirstGrpTotal = array();
	
	$firstGrpName = $providerNameArr[$firstGrpId];

	foreach($firstGrpData as $secGrpId => $secGrpData){
		$subTotalAmtArr = array();
		$arrSecGrpTotal = array();

		$secGrpName = $arrAllFacilities[$secGrpId];

		foreach($secGrpData as $chgDetId => $encDataArr){
			$enc_id= $encDataArr["encounter_id"];
	
			$patient_id = $encDataArr['patient_id'];
			$date_of_service = $encDataArr['date_of_service'];
			$primaryInsName  = $arrAllInsCompanies[$encDataArr['pri_ins_id']];
			$secondaryInsName = $arrAllInsCompanies[$encDataArr['sec_ins_id']];
			if($encDataArr["default_facility"]>0){
				$HostGrpName = $arrAllFacilities[$encDataArr["default_facility"]];	
			}else if($arrHomeFacOfPatients[$patient_id]){
				$pos_fac_id = $arrPosFacOfSchFac[$arrHomeFacOfPatients[$patient_id]];	
				$HostGrpName = $arrAllFacilities[$pos_fac_id];	
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
			//$subTotalAmtArr["totalAmt"][] = $totalAmt;

		
			//CREDIT - OVER PAYMENT
			$creditProcAmount+= $encDataArr['over_payment'];

			$cptCode = $encDataArr['cpt_prac_code'];
			
			foreach($arrAllTransactions[$chgDetId] as $key => $transDet){

				if($chgDetId==$old_chgDetId){
					$strCPT_CSV=$totalAmt='';	
				}
				$old_chgDetId=$chgDetId;

				$ins_paid= (empty($transDet['pri_payment'])==false) ? $transDet['pri_payment'] : $transDet['sec_payment'];
				$arr=array();
				
				$arr[]=$firstGrpName;
				$arr[]=$secGrpName;
				$arr[]=$HostGrpName;
				$arr[]=$patient_id;
				$arr[]=$enc_id;
				$arr[]=$date_of_service;
				$arr[]=$cptCode;
				$arr[]=$arrAllTransactions_date[$chgDetId][$key];
				$arr[]=$primaryInsName;
				$arr[]=$secondaryInsName;
				$arr[]=$CLSReports->numberFormat($totalAmt,2);
				$arr[]=$CLSReports->numberFormat($transDet['pat_payment'],2);
				$arr[]=$CLSReports->numberFormat($ins_paid,2);
				$arr[]=$CLSReports->numberFormat($transDet['pri_payment'],2);
				$arr[]=$CLSReports->numberFormat($transDet['sec_payment'],2);
				$arr[]="";
				$arr[]=$CLSReports->numberFormat($transDet['writeoff'],2);
				$arr[]=$CLSReports->numberFormat($transDet['adj'],2);
				$arr[]=$CLSReports->numberFormat($transDet['refund'],2);
				$arr[]=$arrAllTransactions_payinfo['mode'][$chgDetId][$key];
				$arr[]=$arrAllTransactions_payinfo['check_number'][$chgDetId][$key];
				
				
				fputcsv($fp,$arr, ",","\"");	
				$creditProcAmount=0;		
			}
			if($creditProcAmount>0){
				$arr=array();
				$arr[]=$firstGrpName;
				$arr[]=$secGrpName;
				$arr[]=$HostGrpName;
				$arr[]=$patient_id;
				$arr[]=$enc_id;
				$arr[]=$date_of_service;
				$arr[]=$cptCode;
				$arr[]="";
				$arr[]=$primaryInsName;
				$arr[]=$secondaryInsName;
				$arr[]="";
				$arr[]="";
				$arr[]="";
				$arr[]="";
				$arr[]="";
				$arr[]=$CLSReports->numberFormat($creditProcAmount,2);
				$arr[]="";
				$arr[]="";
				$arr[]="";
				$arr[]="";
				$arr[]="";

				fputcsv($fp,$arr, ",","\"");					
			}
			
			$insuranceDue = ($insuranceDue<0) ? 0 : $insuranceDue; 
			$subTotalAmtArr["insuranceDue"][] = $insuranceDue;
			$patientDue = ($patientDue<0) ? 0 : $patientDue;
			$patientDue = ($totalBalance<=0) ? 0 : $patientDue;
			
			$strCPT = implode(', ', $arrCPT);
			$strCPT_CSV = implode(', ', $arrCPT_CSV);
			$strDxCodes = implode(', ', $arrDxCodes);
			
			$old_enc_id=$enc_id;
			$old_chgDetId=$chgDetId;
		}
	} // END SECOND GROUP
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

//$csv_file_data = ob_get_contents();
//ob_end_clean();

//--- PDF FILE DATA ---
//ob_start();

fclose ($fp);

/*$show_msg= "<br><br><b>Transaction Details have been exported successfully.</b>";
$show_msg.="<b> <a href='file_save_export.php?fn=".$csv_file_name."' style='color:red' > Click here to save CSV file</a></b>";
echo $show_msg;*/
?>
