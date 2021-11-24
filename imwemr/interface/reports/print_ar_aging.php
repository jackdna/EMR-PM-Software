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
FILE : print_ar_aging.php
PURPOSE :  PRINTING AR FOR AGINA
ACCESS TYPE : DIRECT
*/
include_once(dirname(__FILE__)."/../../config/globals.php");

$aging_start=0;
$aging_to = 180;
if($stm_aging_to>0){
	$aging_to=$stm_aging_to;
}
$aging_till = $aging_to+1;
$All_due = true;
$noOfCols=0;
$agingPdfData='';

//--- GET DATA FROM PATIENT CHARGE LIST ---
$qry = "Select patient_charge_list.charge_list_id,
	patient_charge_list.patient_id,patient_charge_list_details.pat_due, 
	patient_charge_list.encounter_id,
	DATEDIFF(NOW(), from_pat_due_date) as last_dop_diff,
	DATEDIFF(NOW(),date_of_service) as last_dos_diff 
	FROM patient_charge_list 
	LEFT JOIN patient_charge_list_details ON patient_charge_list_details.charge_list_id = patient_charge_list.charge_list_id 
	LEFT JOIN patient_data on patient_data.id = patient_charge_list.patient_id
	where patient_charge_list_details.pat_due > 0 
	AND patient_charge_list_details.del_status='0' AND patient_charge_list.patient_id in ($patient_id)";
	if(empty($grp_id) == false){
		$qry.=" AND patient_charge_list.gro_id IN(".$grp_id.")";
	}

$mainQryRun = imw_query($qry);
$patient_paid_encounter = array();
$insComIdArr = array();
$patientARAging = array();
while($mainQryRes=imw_fetch_array($mainQryRun)){
	$noOfCols=0;
	$encounter_id = $mainQryRes['encounter_id'];
	$patinet_id = $mainQryRes['patient_id'];

	$agingCompare = $mainQryRes["last_dop_diff"];
	if($agingCompare==NULL){ $agingCompare = $mainQryRes["last_dos_diff"]; }
	$patientDue = $mainQryRes['pat_due'];
	
	for($a=$aging_start;$a<=$aging_to;$a++){
		$start = $a;
		$a = $a > 0 ? $a - 1 : $a;
		$end = ($a) + $aggingCycle;
		//--- PATIENT AND INSURANCE TOTAL DUE AMOUNT ------
		if($agingCompare >= $start &&  $agingCompare <= $end){
			$patientARAging[$start][] = $patientDue;
		}
		$a += $aggingCycle;	
		$noOfCols+=1;			
	}
	
	if($All_due == true){
		//--- PATIENT AND INSURANCE TOTAL DUE AMOUNT BY 181+ ------			
		if($agingCompare >= $aging_till){
			$patientARAging[$aging_till][] = $patientDue;
		}
	}
}


if(count($patientARAging)>0){
	$headerTd = NULL;
	$totalPatDueData= NULL;
	$totalBalance_ar=0;
	$noOfCols+=2;
	$colWidth= floor(705 / $noOfCols);
	
	$style='style="border-right:1px solid #000000;border-bottom:1px solid #000000; width:'.$colWidth.'px;"';
	$styleD='style="border-right:1px solid #000000;height:20px;"';
	
	//---- GET PATIENT SPECIFIC DATA ----------
	for($a=$aging_start;$a<=$aging_to;$a++){
		$start = $a;
		$a = $a > 0 ? $a - 1 : $a;
		$patient_due = 0;
		//--- GET DUE AMOUNT FOR PATIENT -----------
		if(count($patientARAging[$start])>0){				
			$patient_due = array_sum($patientARAging[$start]);
			if($patient_due > 0){
				$totalPatDueArr[$start][] = $patient_due;
			}
		}
		
		$end = $a + $aggingCycle;

		$totalBalance_ar+=$patient_due;
		
		$headerTd.='<td align="center" '.$style.' >'.$start.' - '.$end.'</td>';
		$patient_due = numberFormat($patient_due,2);
		$totalPatDueData.='<td align="center" class="text_10" '.$styleD.'>'.$patient_due.'</td>';
		$a += $aggingCycle;
	}
	//--- GET DUE AMOUNT FOR PATIENT BY 181+ -----------
	if($All_due == true){
		$patient_due = 0;
		if(count($patientARAging[$aging_till])>0){				
			$patient_due = array_sum($patientARAging[$aging_till]);
			if($patient_due > 0){
				$totalPatDueArr[$aging_till][] = $patient_due;
			}
		}
		$totalBalance_ar+=$patient_due;
		$headerTd.='<td align="center"  '.$style.' >'.$aging_till.'+</td>';
		$patient_due = numberFormat($patient_due,2);
		$totalPatDueData.='<td align="center" class="text_10" '.$styleD.'>'.$patient_due.'</td>';
	}

	$style='style="border-bottom:1px solid #000000;width:'.$colWidth.'px;"';
	$headerTd.='<td align="center" '.$style.' >Balance</td>';
	$totalBalance_ar = numberFormat($totalBalance_ar,2);
	$totalPatDueData.='<td align="center" class="text_10b">'.$totalBalance_ar.'</td>';

	$agingPdfData='<table border="0" style="border:1px solid #000;margin-top:20px; width:700px;" cellpadding="0" cellspacing="0">
	<tr><td style="border-bottom:1px solid #000000;height:20px;" colspan="'.$noOfCols.'">&nbsp;<b>Patient A/R Aging</b></td></tr>
	<tr>'.$headerTd.'</tr>
	<tr>'.$totalPatDueData.'</tr>
	</table>';
}
	
?>