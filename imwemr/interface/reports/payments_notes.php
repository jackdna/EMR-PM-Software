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
FILE : productivity_physcian_payments.php
PURPOSE : PRODUCTIVITY PAYMENTS FOR PHYSICIAN
ACCESS TYPE : DIRECT
*/
$ignoreAuth = true;
include_once(dirname(__FILE__)."/../../config/globals.php");
require_once($GLOBALS['fileroot'].'/library/classes/cls_common_function.php');
include_once($GLOBALS['fileroot'].'/library/classes/SaveFile.php');

$dateFormat = get_sql_date_format();
$Start_date=$End_date=date('Y-m-d');

if($_REQUEST['start_date']!='' && $_REQUEST['end_date']!=''){
	$Start_date=$_REQUEST['start_date'];
	$End_date=$_REQUEST['end_date'];
}

//--- CHANGE DATE FORMAT ----
$startDate = getDateFormatDB($Start_date);
$endDate   = getDateFormatDB($End_date);

//MAKING OUTPUT DATA
//$file_name="payments_notes_".time().".csv";
$file_name="payments_notes.csv";
$csv_file_name= write_html("", $file_name);

//CSV FILE NAME
if(file_exists($csv_file_name)){
	unlink($csv_file_name);
}
$fp = fopen ($csv_file_name, 'a+');

$pfx=",";

$data_output='';
$data_output.="NextGen Practice".$pfx;
$data_output.="Location".$pfx;
$data_output.="Rendering Provider".$pfx;
$data_output.="Date of Service".$pfx;
$data_output.="Payment Posting Date".$pfx;
$data_output.="CPT / Charge Code".$pfx;
$data_output.="CPT / Charge Code Description".$pfx;
$data_output.="CPT / Charge Code Quantity".$pfx;
$data_output.="Total Payment Amount".$pfx;
$data_output.="Acquisition Note";
$data_output.= "\n";


//TRANSACTIONS TABLE
$qry="Select trans.report_trans_id, trans.encounter_id, trans.charge_list_detail_id, trans.trans_by, trans.trans_ins_id, 
trans.trans_type, trans.trans_amount, trans.trans_code_id, trans.trans_qry_type, trans.parent_id, trans.facility_id,
trans.trans_dot, trans.trans_del_operator_id, main.units,
DATE_FORMAT(main.date_of_service,'".$dateFormat."') as date_of_service,
DATE_FORMAT(trans.trans_dot,'".$dateFormat."') as 'trans_date',
users.lname,users.fname, users.mname,
pos_facilityies_tbl.facilityPracCode,
pos_tbl.pos_prac_code,
cpt_fee_tbl.cpt4_code, cpt_fee_tbl.cpt_desc
FROM report_enc_trans trans 
JOIN report_enc_detail main ON main.charge_list_detail_id = trans.charge_list_detail_id	
JOIN patient_data on patient_data.id = main.patient_id 
LEFT JOIN users on users.id = main.primary_provider_id_for_reports 
LEFT JOIN pos_facilityies_tbl on pos_facilityies_tbl.pos_facility_id = main.facility_id
LEFT JOIN pos_tbl ON pos_tbl.pos_id = pos_facilityies_tbl.pos_id 
LEFT JOIN cpt_fee_tbl on cpt_fee_tbl.cpt_fee_id = main.proc_code_id 
WHERE trans.trans_type IN('paid','copay-paid','deposit','interest payment','negative payment','copay-negative payment') 
AND (trans_dot BETWEEN '$startDate' and '$endDate')
AND (trans.trans_del_operator_id='0' OR (trans.trans_del_operator_id>0 && trans.trans_del_date<='$endDate')) 
ORDER BY trans.master_tbl_id, trans.report_trans_id, trans.trans_dot, trans.trans_dot_time";

$rs=imw_query($qry);
$dataexist=0;
while($res = imw_fetch_assoc($rs)){
	$dataexist=1;
	$report_trans_id=$res['report_trans_id'];
	$encounter_id= $res['encounter_id'];
	$trans_type= strtolower($res['trans_type']);
	$trans_by= strtolower($res['trans_by']);			

	$dos=$res['date_of_service'];
	$trans_date=$res['trans_date'];
	$cpt=$res['cpt4_code'];
	$primaryProviderId = $res['primaryProviderId'];
	$facility_name= $res['facilityPracCode'].' - '.$res['pos_prac_code'];
	$provider_name = core_name_format($res['lname'], $res['fname'], $res['mname']);

	$tempRecordData[$report_trans_id]=$res['trans_amount'];
	
	$paidForProc=$res['trans_amount'];
	if($trans_type=='negative payment' || $trans_type=='copay-negative payment' || $res['trans_del_operator_id']>0)$paidForProc="-".$res['trans_amount'];
	if(($trans_type=='negative payment' || $trans_type=='copay-negative payment') && $res['trans_del_operator_id']>0)$paidForProc=$res['trans_amount'];

	//IF parent_id >0 THEN IT MEANS RECORD IS UPDATED. THEN REMOVE PREVIOUS FETCHED AMOUNT.
	$prevFetchedAmt=0;
	if($res['parent_id']>0 && $tempRecordData[$res['parent_id']] && $res['trans_del_operator_id']<=0){
		$prevFetchedAmt = ($trans_type=='negative payment' || $trans_type=='copay-negative payment') ? $tempRecordData[$res['parent_id']] : "-".$tempRecordData[$res['parent_id']];
	}
	$paidForProc+=$prevFetchedAmt;	
	
	$arrResult[$facility_name][$provider_name][$dos][$trans_date][$cpt]['payment']+=$paidForProc;
	$arrResult[$facility_name][$provider_name][$dos][$trans_date][$cpt]['cpt_desc']=$res['cpt_desc'];
	
	//if(!$arrChkChgDetIds[$res['charge_list_detail_id']]){
		$arrResult[$facility_name][$provider_name][$dos][$trans_date][$cpt]['units']+=$res['units'];	
		//$arrChkChgDetIds[$res['charge_list_detail_id']]=$res['charge_list_detail_id'];
	//}
}

if(sizeof($arrResult)>0){
	
	foreach($arrResult as $fac_name =>$facData){
		foreach($facData as $prov_name =>$provData){
			foreach($provData as $dos =>$dosData){
				foreach($dosData as $trans_date =>$transData){
					foreach($transData as $cpt =>$cptData){
						
						$data_output.="".$pfx;
						$data_output.='"'.$fac_name.'"'.$pfx;
						$data_output.='"'.$prov_name.'"'.$pfx;
						$data_output.='"'.$dos.'"'.$pfx;
						$data_output.='"'.$trans_date.'"'.$pfx;
						$data_output.='"'.$cpt.'"'.$pfx;
						$data_output.='"'.$cptData['cpt_desc'].'"'.$pfx;
						$data_output.='"'.$cptData['units'].'"'.$pfx;
						$data_output.='"'.$cptData['payment'].'"'.$pfx;
						$data_output.="";
						$data_output.= "\n";						
					}
				}
			}
		}
	}

		
	$fp=fopen($csv_file_name,"w");
	@fwrite($fp,$data_output);
	@fclose($fp);
	
	echo "Output created from $Start_date to $End_date.<br><br>";
	echo $csv_file_name.'<br><br><br>';?>
	<form name="csvDirectDownloadForm" id="csvDirectDownloadForm" action="downloadCSV.php" method ="post" > 
		<input type="hidden" name="file_format" id="file_format" value="csv">
		<input type="hidden" name="zipName" id="zipName" value="">	
		<input type="hidden" name="file" id="file" value="<?php echo $csv_file_name;?>" />
		<input type="submit" name="sbtbtn" id="sbtbtn" value="Download CSV File">
	</form> 
<?php	
}else{
	echo "No data found between $Start_date to $End_date";
}
?>