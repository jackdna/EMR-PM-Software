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
//--- LOAD PATIENT COMMUNICATION LISTING FILE WITH AJAX ----

require_once('../../config/globals.php');
include_once $GLOBALS['srcdir'].'/classes/SaveFile.php';
include_once $GLOBALS['srcdir'].'/classes/common_function.php';

$patient_id = $_SESSION['patient'];

function get_imw_data($qry){
	$return_arr = array();
	if(trim($qry) != ''){
		$qry_res = imw_query($qry);
		while($row = imw_fetch_array($qry_res)){
			$return_arr[] = $row;
		}
	}
	return $return_arr;	
}

//------ FUNCTION TO GET SOURCE VALUE/WHO PAID FOR TRANSACTION ------------
function getSource($charge_list_detail_id){
	global $arrSource;
	$sourceQry = "SELECT TRIM(pcdpi.paidBy) AS paidBy,TRIM(ins_comp.in_house_code) AS in_house_code
				  FROM
				  patient_chargesheet_payment_info pcpi
				  JOIN  patient_charges_detail_payment_info pcdpi
				  ON pcdpi.payment_id = pcpi.payment_id 
				  LEFT JOIN insurance_companies ins_comp
				  ON ins_comp.id = pcpi.insProviderId
				  WHERE
				  pcdpi.charge_list_detail_id = '".$charge_list_detail_id."'
				  AND pcdpi.deletePayment = 0
				  ";
	$sourceRes = get_imw_data($sourceQry);
	for($j=0;$j<count($sourceRes);$j++){
		if($sourceRes[$j]['paidBy'] == "Insurance" && !empty($sourceRes[$j]['in_house_code']))
		$arrSource[] = $sourceRes[$j]['in_house_code'];
		else if(!empty($sourceRes[$j]['paidBy']))
		$arrSource[] = $sourceRes[$j]['paidBy'];
	}
}
function getSourceForCopay($encounter_id){
	global $arrSource;
	
	$sourceQry = "SELECT pcdpi.paidBy,TRIM(ins_comp.in_house_code) AS in_house_code
				  FROM
				  patient_chargesheet_payment_info pcpi
				  JOIN  patient_charges_detail_payment_info pcdpi
				  ON pcdpi.payment_id = pcpi.payment_id 
				  LEFT JOIN insurance_companies ins_comp
				  ON ins_comp.id = pcpi.insProviderId
				  WHERE
				  pcpi.encounter_id  = '".$encounter_id."'
				  AND pcdpi.charge_list_detail_id = 0
				  AND pcdpi.deletePayment = 0
				  LIMIT 0,1
				  ";
		$sourceRes = get_imw_data($sourceQry);
		if($sourceRes[0]['paidBy'] == "Insurance")
		$source = $sourceRes[0]['in_house_code'];
		else 
		$source = $sourceRes[0]['paidBy'];
		$arrSource[] = $source;
		return $source;
}
function getCopayAmount($encounter_id){
	$sourceQry = "SELECT pcdpi.paidForProc,pcdpi.overPayment
				  FROM
				  patient_charges_detail_payment_info pcdpi
				  JOIN  patient_chargesheet_payment_info pcspi
				  ON pcdpi.payment_id = pcspi.payment_id
				  WHERE 
				  pcspi.encounter_id = '".$encounter_id."'
				  AND pcdpi.charge_list_detail_id = 0
				  AND pcdpi.deletePayment = 0
				  LIMIT 0,1
				  ";
	$sourceRes = get_imw_data($sourceQry);
	return $sourceRes[0]['paidForProc']+$sourceRes[0]['overPayment'];
}
function getRowSpan($column, $dos='', $provider_id='',$encounter_id =''){
	global $patient_id;
	if($column == "DOS"){
		$qry = "select count(*) as rowSpanDOS 
			from  
			patient_charge_list_details pcld 
			JOIN  patient_charge_list pcl ON pcld.charge_list_id = pcl.charge_list_id
			JOIN cpt_fee_tbl cft ON cft.cpt_fee_id = pcld.procCode
			where 
			pcld.del_status='0' and 
			pcl.date_of_service = '".$dos."'
			AND pcl.patient_id = '".$patient_id."'
			";//echo $qry."<br>";
		$res = get_imw_data($qry);
		$rowSpanDOS = $res[0]['rowSpanDOS'];
		return $rowSpanDOS;
	}
	else if ($column == "doctor"){
		$qry = "select count(*) as rowSpanDoctor 
			from  
			patient_charge_list_details pcld 
			JOIN  patient_charge_list pcl ON pcld.charge_list_id = pcl.charge_list_id
			JOIN cpt_fee_tbl cft ON cft.cpt_fee_id = pcld.procCode
			where 
			pcld.del_status='0' and 
			pcl.date_of_service = '".$dos."'
			AND pcld.primaryProviderId = '".$provider_id."'
			AND pcl.patient_id = '".$patient_id."'
			
			";//echo $qry."<br>";
		$res = get_imw_data($qry);
		$rowSpanDoctor = $res[0]['rowSpanDoctor'];
		return $rowSpanDoctor;
	}else if($column == "sbDOS"){
		$qry = "select count(*) as rowSpanDOS 
			from  
			procedureinfo spi 
			JOIN  superbill sb ON spi.idSuperBill = sb.idSuperBill
			where 
			spi.delete_status='0' AND sb.postedStatus='0'
			AND sb.dateOfService = '".$dos."'
			AND sb.patientId = '".$patient_id."'";
		$res = get_imw_data($qry);
		$rowSpanDOS = $res[0]['rowSpanDOS'];
		return $rowSpanDOS;
	}
	else if ($column == "sbdoctor"){
		$qry = "select count(*) as rowSpanDoctor 
			from  
			procedureinfo spi 
			JOIN  superbill sb ON spi.idSuperBill = sb.idSuperBill
			where 
			spi.delete_status='0'  AND sb.postedStatus='0'
			AND sb.dateOfService = '".$dos."'
			AND sb.physicianId = '".$provider_id."'
			AND sb.patientId = '".$patient_id."'";
		$res = get_imw_data($qry);
		$rowSpanDoctor = $res[0]['rowSpanDoctor'];
		return $rowSpanDoctor;
	}
}
function getPaidAmt($source,$id){
	if($source == "Insurance"){
		$qry = "SELECT pcdpi.paidForProc,pcdpi.overPayment,
				pcpi.paymentClaims	
				FROM 
				patient_charges_detail_payment_info pcdpi
				JOIN patient_chargesheet_payment_info pcpi
				ON pcpi.payment_id  = pcdpi.payment_id 	
				WHERE pcdpi.charge_list_detail_id = '".$id."'
				AND pcdpi.paidBy = 'Insurance'
				AND pcdpi.deletePayment = 0
				";
		$res = get_imw_data($qry);
		$ins_paid = 0;
		for($i=0; $i<count($res); $i++){
			if($res[$i]['paymentClaims'] == "Negative Payment"){
				$ins_paid -= $res[$i]['paidForProc']+$res[$i]['overPayment'];	
			}
			else{
				$ins_paid += $res[$i]['paidForProc']+$res[$i]['overPayment'];
			}
		}
		return $ins_paid;
	}
	if($source == "Patient"){
		$qry = "SELECT pcdpi.paidForProc,pcdpi.overPayment,
				pcpi.paymentClaims	
				FROM 
				patient_charges_detail_payment_info pcdpi
				JOIN patient_chargesheet_payment_info pcpi
				ON pcpi.payment_id  = pcdpi.payment_id 	
				WHERE pcdpi.charge_list_detail_id = '".$id."'
				AND (pcdpi.paidBy = 'Patient' OR pcdpi.paidBy = 'Res. Party')
				AND pcdpi.deletePayment = 0
				";
		$res = get_imw_data($qry);
		$pt_paid = 0;
		for($i=0; $i<count($res); $i++){
			if($res[$i]['paymentClaims'] == "Negative Payment"){
				$pt_paid -= $res[$i]['paidForProc']+$res[$i]['overPayment'];	
			}
			else{
				$pt_paid += $res[$i]['paidForProc']+$res[$i]['overPayment'];
			}
		}
		return $pt_paid;
	}
}

function getCreditDebit($pcld_ID){
		global $ins_paid, $pt_paid, $patient_id,$arrSource; 
		$qry = "SELECT crap.amountApplied, crap.type, crap.ins_case, crap.charge_list_detail_id, crap.charge_list_detail_id_adjust, 
				TRIM(ins_comp.in_house_code) AS in_house_code	
				FROM 
				creditapplied crap
				LEFT JOIN insurance_companies ins_comp
				ON ins_comp.id = crap.ins_case
				WHERE 
				(crap.charge_list_detail_id = '".$pcld_ID."'
				AND crap.patient_id = '".$patient_id."')
				OR 
				(crap.charge_list_detail_id_adjust = '".$pcld_ID."'
				AND patient_id_adjust = '".$patient_id."'
				)
				
				AND crap.delete_credit = 0
				";
		$res = get_imw_data($qry);	
		for($i=0; $i<count($res); $i++){
//-------- DEDUCT FOR DEBIT CASE ------------			
			if($res[$i]['charge_list_detail_id'] == $pcld_ID){
				if($res[$i]['type'] == "Patient" || $res[$i]['type'] == "Res. Party"){
					$arrSource[] = $res[$i]['type'];
					$pt_paid -= $res[$i]['amountApplied'];
				}
				else if($res[$i]['type'] == "Insurance"){
					$arrSource[] = $res[$i]['in_house_code'];
					$ins_paid -= $res[$i]['amountApplied'];
				}
			}
//-------- ADD FOR CREDIT CASE ------------			
			else if($res[$i]['charge_list_detail_id_adjust'] == $pcld_ID){
				if($res[$i]['type'] == "Patient" || $res[$i]['type'] == "Res. Party"){
					$pt_paid += $res[$i]['amountApplied'];
					$arrSource[] = $res[$i]['type'];
				}
				else if($res[$i]['type'] == "Insurance"){
					$arrSource[] = $res[$i]['in_house_code'];
					$ins_paid += $res[$i]['amountApplied'];
				}
			}
		}
}
function array_replace_val($arr,$ori_val,$rep_val){
	foreach($arr as $key=>$val){
		if($val == $ori_val)
		$arr[$key] = $rep_val;
	}
	return $arr;
}
function format_price($number, $curr = "$"){
	$price = ($number<0)?"-$curr".number_format(abs($number),2):$curr.number_format($number,2);
	return $price;
}

$tmpDos = $tmpDos1 = $tmpDoctor = $rowSpanDoctor = '';
//--- GET ALL PATIENT COMMUNICATION MESSAGES --------
$msg_qry = "SELECT sb.dateOfService as dos,sb.encounterId as encounter_id,
			TRIM(CONCAT(users.lname,', ',users.fname,' ',users.mname)) as doctor,
			cft.cpt4_code as cpt_code, cft.cpt_desc as cpt_desc,cftb.cpt_fee,
			sb.todaysCharges as cpt_charges,spi.id as pcld_ID,sb.physicianId as primaryProviderId
			FROM superbill sb 
			JOIN procedureinfo spi ON sb.idSuperBill = spi.idSuperBill 
			JOIN cpt_fee_tbl cft ON cft.cpt_prac_code = spi.cptCode
			JOIN cpt_fee_table cftb ON cftb.cpt_fee_id = cft.cpt_fee_id
			LEFT JOIN users ON users.id = sb.physicianId
			WHERE spi.delete_status='0' and sb.patientId = '".$patient_id."' AND sb.postedStatus='0' and cft.delete_status='0'
			GROUP BY spi.id 
			ORDER BY sb.dateOfService DESC,sb.physicianId DESC";
$SBmainRes = get_imw_data($msg_qry);
for($i=0;$i<count($SBmainRes);$i++){
	//------ ROW SPAN FOR DOS COLUMN	------------
	if($tmpDos != $SBmainRes[$i]['dos']){
		$tmpDos = $SBmainRes[$i]['dos'];
		$rowSpanDOS = getRowSpan("sbDOS",$SBmainRes[$i]['dos']);
	}else{
		$rowSpanDOS = '';
	}
	
	//------ ROW SPAN FOR DOCTOR COLUMN	------------	
	if($tmpDoctor != $SBmainRes[$i]['primaryProviderId'] || $tmpDos1 != $SBmainRes[$i]['dos']){
		$tmpDos1 = $SBmainRes[$i]['dos'];
		$tmpDoctor = $SBmainRes[$i]['primaryProviderId'];
		$rowSpanDoctor = getRowSpan("sbdoctor",$SBmainRes[$i]['dos'],$SBmainRes[$i]['primaryProviderId'],$SBmainRes[$i]['encounter_id']);
	}else{
		$rowSpanDoctor = '';
	}
	
	$dos = get_date_format(trim($SBmainRes[$i]['dos']));
	$doctor = trim($SBmainRes[$i]['doctor']);
	$cpt_code = $SBmainRes[$i]['cpt_code'];
	$cpt_desc = $SBmainRes[$i]['cpt_desc'];
	if(strlen("$cpt_code - $cpt_desc")>30)
	$tmpCPTstr = substr("$cpt_code - $cpt_desc",0,30)."...";
	else
	$tmpCPTstr = "$cpt_code - $cpt_desc";
	
	//----- GET PROCEDURE CHARGES ------------	
	$cpt_charges_str=0;
	$cpt_charges = $SBmainRes[$i]['cpt_fee'];
	$cpt_charges_str = ($cpt_charges == "")?"":numberformat($cpt_charges,2,'yes');
	
	$total_charges += $cpt_charges;
	$total_charges_str = numberformat($total_charges,2,'yes');
	
	$content .= <<<DATA
		<tr class="valign-top">
DATA;
	if($rowSpanDOS != ''){
	$content .= <<<DATA
	<td rowspan="$rowSpanDOS" class="text-nowrap">$dos</td>
DATA;
	}
	if($rowSpanDoctor != ''){
	$content .= <<<DATA
	<td rowspan="$rowSpanDoctor">$doctor</td>
DATA;
	}
	if($rowSpanDOS != ''){
	$content .= <<<DATA
DATA;
	}
	else{
	$content .= <<<DATA
DATA;
	}
	
	$content .= '<td>'.$tmpCPTstr.'</td>
					<td></td>';
					
    if (core_check_privilege(array("priv_financial_hx_cpt")) == true) {
	$content .= '<td class="text-right">'.$cpt_charges_str.'</td>
					<td class="text-right"></td>
					<td class="text-right"></td>';
    }
    $content .='</tr>';
}

//--- GET ALL PATIENT COMMUNICATION MESSAGES --------
$msg_qry = "SELECT pcl.date_of_service as dos,pcl.encounter_id,
			TRIM(CONCAT(users.lname,', ',users.fname,' ',users.mname)) as doctor,
			cft.cpt4_code as cpt_code, cft.cpt_desc as cpt_desc,
			pcld.totalAmount as cpt_charges,pcld.charge_list_detail_id as pcld_ID,pcld.coPayAdjustedAmount,pcld.primaryProviderId,
			SUM(pcdpi.paidForProc) as paid_amt,pcdpi.charge_list_detail_id
			FROM
			patient_charge_list pcl
			JOIN patient_charge_list_details pcld
			ON pcld.charge_list_id = pcl.charge_list_id
			JOIN cpt_fee_tbl cft
			ON cft.cpt_fee_id = pcld.procCode
			LEFT JOIN users
			ON users.id = pcld.primaryProviderId
			LEFT JOIN patient_charges_detail_payment_info pcdpi
			ON ((pcdpi.charge_list_detail_id = pcld.charge_list_detail_id) AND pcdpi.deletePayment = 0) 
			WHERE
			pcld.del_status='0' and 
			pcl.patient_id = '".$patient_id."'
			GROUP BY 
			pcld.charge_list_detail_id 
			ORDER BY
			pcl.date_of_service DESC,
			pcld.primaryProviderId DESC
			";
$mainRes = get_imw_data($msg_qry);

$total_proc_found = count($mainRes);
//$total_charges = "0";
$total_ins_paid = "0";
$total_pt_paid = "0";
$tmpDos = $tmpDos1 = '';
$tmpDoctor = '';$rowSpanDoctor = '';
for($i=0;$i<count($mainRes);$i++){
	$id = $mainRes[$i]['charge_list_detail_id'];
//------ ROW SPAN FOR DOS COLUMN	------------
	if($tmpDos != $mainRes[$i]['dos']){
		$tmpDos = $mainRes[$i]['dos'];
		$rowSpanDOS = getRowSpan("DOS",$mainRes[$i]['dos']);
	}else{
		$rowSpanDOS = '';
	}
//------ ROW SPAN FOR DOCTOR COLUMN	------------	
	if($tmpDoctor != $mainRes[$i]['primaryProviderId'] || $tmpDos1 != $mainRes[$i]['dos']){
		$tmpDos1 = $mainRes[$i]['dos'];
		$tmpDoctor = $mainRes[$i]['primaryProviderId'];
		$rowSpanDoctor = getRowSpan("doctor",$mainRes[$i]['dos'],$mainRes[$i]['primaryProviderId'],$mainRes[$i]['encounter_id']);
	}else{
		$rowSpanDoctor = '';
	}
	
	$dos = get_date_format(trim($mainRes[$i]['dos']));
	$doctor = trim($mainRes[$i]['doctor']);
	$cpt_code = $mainRes[$i]['cpt_code'];
	$cpt_desc = $mainRes[$i]['cpt_desc'];
	if(strlen("$cpt_code - $cpt_desc")>30)
	$tmpCPTstr = substr("$cpt_code - $cpt_desc",0,30)."...";
	else
	$tmpCPTstr = "$cpt_code - $cpt_desc";
//----- GET SOURCE OF PAYMENT IN ARRAY ------------	
	$arrSource = array();
	if(!empty($mainRes[$i]['charge_list_detail_id']))
	getSource($mainRes[$i]['charge_list_detail_id']);

//----- GET PROCEDURE CHARGES ------------	
	$cpt_charges = $mainRes[$i]['cpt_charges'];
	$cpt_charges_str = ($cpt_charges == "")?"":numberformat($cpt_charges,2,'yes');
//----- GET PAID AMOUNT FOR PROCEDURE ------------		
	if(!empty($mainRes[$i]['charge_list_detail_id'])){
		$ins_paid = getPaidAmt("Insurance", $mainRes[$i]['charge_list_detail_id']);
		$pt_paid =  getPaidAmt("Patient", $mainRes[$i]['charge_list_detail_id']);
	}
	else{$ins_paid = $pt_paid = $ins_paid_str = $pt_paid_str = "";}
//----- GET COPAY AMOUNT FOR PROCEDURE ------------		
		if($mainRes[$i]['coPayAdjustedAmount'] == 1){
		$copay	= getCopayAmount($mainRes[$i]['encounter_id']);
//----- GET COPAY SOURCE ------------		
		$coPaySource = getSourceForCopay($mainRes[$i]['encounter_id']);
		}
		else
		$copay = "";
		
		if($coPaySource == "Patient" || $coPaySource == "Res. Party")
		$pt_paid += $copay;
		else
		$ins_paid += $copay;
		
		getCreditDebit($mainRes[$i]['pcld_ID']);
		
		$ins_paid_str = ($ins_paid == "")?"":numberformat($ins_paid,2,'yes');
		$pt_paid_str = ($pt_paid == "")?"":numberformat($pt_paid,2,'yes');

		if(count($arrSource)>0){
		$arrSourceTmp = array_unique(array_filter($arrSource));
		$ptKey = array_search("Patient",$arrSourceTmp);
		$arrSourceTmp = array_replace_val($arrSourceTmp,"Patient","Pt");
		$source = implode("/ ",$arrSourceTmp);
		}
		else
		$source = "";
	
	$total_charges += $cpt_charges;
	$total_charges_str = numberformat($total_charges,2,'yes');
	$total_ins_paid += $ins_paid;
	$total_ins_paid_str = numberformat($total_ins_paid,2,'yes');
	$total_pt_paid += $pt_paid;
	$total_pt_paid_str = numberformat($total_pt_paid,2,'yes');
	
	
	
	$content .= '<tr class="valign-top">';
	if($rowSpanDOS != ''){
        $content .= '<td  width="100"  rowspan="'.$rowSpanDOS.'" class="text-nowrap">'.$dos.'</td>';
	}
	if($rowSpanDoctor != ''){
	$content .= '<td width="80" rowspan="'.$rowSpanDoctor.'">'.$doctor.'</td>';
	}
	if($rowSpanDOS != ''){
        $content .= '&nbsp;';
	}
	else{
        $content .= '&nbsp;';
	}
	
	$content .= '<td width="180">'.$tmpCPTstr.'</td>
					<td width="100">'.$source.'</td>';
            if (core_check_privilege(array("priv_financial_hx_cpt")) == true) {
    $content .= '<td  width="70" class="text-right">'.$cpt_charges_str.'</td>
					<td  width="70" class="text-right">'.$ins_paid_str.'</td>
					<td  width="70" class="text-right">'.$pt_paid_str.'</td>';
            }
    $content .= '</tr>';

}
    if (core_check_privilege(array("priv_financial_hx_cpt")) == true) {
	$content .= '<tr>
			<td colspan="3">&nbsp;</td>
			<td class="purple_bar text-right">Total</td>
			<td class="purple_bar text-right">'.$total_charges_str.'</td>
			<td class="purple_bar text-right">'.$total_ins_paid_str.'</td>
			<td class="purple_bar text-right">'.$total_pt_paid_str.'</td>
		</tr>';
    }
?>
<?php
$html_heading = '<div class="row">
			<div class="col-sm-12">
				<table class="table table-bordered table-condensed">
				<tr class="grythead">
					<th class="text-nowrap">DOS</th>
					<th>Doctor</th>
					<th >CPT Code - Desc</th>
					<th >Payment By</th>';
        if (core_check_privilege(array("priv_financial_hx_cpt")) == true) {
	$html_heading .= '<th >Charges</th>
					<th >Ins Paid</th>
					<th >Pt Paid</th>';	
        }
    $html_heading .= '</tr>';


$html_footer = '
	<div class="col-sm-12 text-center">
		
	</div>';

if(count($mainRes)>0 || count($SBmainRes)>0)
$html_content = $content; 
else
$html_content .= '<tr ><td colspan="7">No records found.</td></tr>';
$html_content .= '</table></div>';	
$html_data = $html_heading.$html_content;
	
$table_pdf_heading = '<table  class="table_collapse cellBorder3" style="background-color:#FFF3E8;">
		<tr><td colspan="7">History of CPT Services</td></tr>
        <tr class="subheading">
            <td width="100" align="left" class="text_b_w">DOS</td>
            <td width="80" align="left" class="text_b_w">Doctor</td>
            <td width="620" class="text_b_w" >
				<table class="table_collapse cellBorder3 white" >
					<tr class="subheading">
					<td width="180" align="left">CPT Code - Desc</td>
					<td width="100" align="left">Payment By</td>';
        if (core_check_privilege(array("priv_financial_hx_cpt")) == true) {
$table_pdf_heading .='<td width="70" align="left">Charges</td>
					<td width="70" align="left">Ins Paid</td>
					<td width="70" align="left">Pt Paid</td>';
        }
$table_pdf_heading .='</tr>
				</table>
			</td>	
        </tr>
		</table>';

$pdf_content = '<table  class="table_collapse cellBorder3" >';
		if(count($mainRes)>0 || count($SBmainRes)>0)
		$pdf_content .= $content; 
		else
		$pdf_content .= '<tr style="height:35px;"><td colspan="7">No records found.</td></tr>';
		
$pdf_content .= '</table>';
$pageHt = '12mm';
$pdf_data .= '<page backtop="'.$pageHt.'" backbottom="5mm">
			<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
			<page_header>	
			'.$table_pdf_heading.'
			</page_header>	
			'.$pdf_content.'
			</page>';
	

$strHTML = <<<DATA
		<style>
			.text_b_w{
				font-size:11px;
				font-family:Arial, Helvetica, sans-serif;
				font-weight:bold;
				color:#FFFFFF;
				background-color:#4684ab;
			}
			.text_10b{
				font-size:11px;
				font-family:Arial, Helvetica, sans-serif;
				font-weight:bold;
				background-color:#FFFFFF;
			}
			.text_10{
				font-size:9px;
				font-family:Arial, Helvetica, sans-serif;
				background-color:#FFFFFF;
			}
			.text_b_w_9{
				font-size:9px;
				font-family:Arial, Helvetica, sans-serif;
				font-weight:bold;
				color:#FFFFFF;
				background-color:#4684ab;
			}
			.textBold{ font-weight:bold;}
			
		</style>
		$pdf_data
DATA;
$flName = 'hxcptserv'.$_SESSION['authId'];
$file_location = write_html($strHTML);
$html_data .= '
<form name="printFrmALLPDF" action="'.$GLOBALS['webroot'].'/library/html_to_pdf/createPdf.php" method="POST" target="_blank">
	<input type="hidden" name="onePage" value="false">
	<input type="hidden" name="op" value="P" >
	<input type="hidden" name="file_location" value="'.$file_location.'">
</form>
</div>';
 echo $html_data;
?>