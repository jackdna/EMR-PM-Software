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
FILE : top_rej_reasons.php
PURPOSE : Display result of Top Rejection Reasons report
ACCESS TYPE : Direct
*/

//Function file
$arrFacilitySel=array();
$arrDoctorSel=array();

//$showCurrencySymbol =showCurrency();

$printFile = true;
if($_POST['form_submitted']){
	$printFile = false;
		
	//--- CHANGE DATE FORMAT ----
	//$startDate = $objManageData->__getDateFormat($Start_date);
	//$endDate = $objManageData->__getDateFormat($End_date);

	$startDate = getDateFormatDB($Start_date);
	$endDate   = getDateFormatDB($End_date);

	// COMBINE INS AND INS GROUPS
	if(sizeof($ins_carriers)>0){ $tempInsArr = $ins_carriers; }
	if(sizeof($insuranceGrp)>0){ $tempInsArr = $insuranceGrp; }

	if(sizeof($ins_carriers)>0 && sizeof($insuranceGrp)>0){
		$tempInsArr=array_merge($ins_carriers, $insuranceGrp);
	}
	$tempSelIns = implode(',', $tempInsArr);
	$tempInsArr = array();
	if(empty($tempSelIns)==false){
		$tempInsArr = explode(',', $tempSelIns);
	}
	$tempInsArr = array_unique($tempInsArr);
	$insuranceName  = implode(',', $tempInsArr);
	$arrInsurance=array();
	if(sizeof($tempInsArr)>0){
		$arrInsuranceSel=array_combine($tempInsArr,$tempInsArr);
		$ins_comp_id=implode(",",$tempInsArr);
	}
	unset($tempInsArr);

	//GET ALL USERS
	$providerNameArr[0] = 'No Provider';
	$rs=imw_query("Select id, fname, mname, lname FROM users");	
	while($res=imw_fetch_array($rs)){
		$id  = $res['id'];
		$providerNameArr[$id] = core_name_format($res['lname'], $res['fname'], $res['mname']);
	}
	
	// CPT CODE ARRAY
	$arrAllCPTCodes=array();
	$rs=imw_query("Select cpt_fee_id, cpt_prac_code FROM cpt_fee_tbl");
	while($res=imw_fetch_array($rs)){
		$arrAllCPTCodes[$res['cpt_fee_id']] = $res['cpt_prac_code'];
	}

	if(sizeof($reason_code)>0){
		$count = 0;
		$condtion = '';
		$resonquery = " AND (";
		foreach($reason_code as $reason){
			$count += count($reason);
			if($count > 1) $condtion = 'OR ';
			$exploded=explode(" ",$reason);
			$str_reason_type =	$exploded[0];
			$str_reason_code =	$exploded[1];
			if(empty($str_reason_type) == false &&  empty($str_reason_code) == false){
				$resonquery .= " $condtion den.CAS_type ='".$str_reason_type."' AND den.CAS_code ='".$str_reason_code."'";
			} else{
				$resonquery .= " $condtion den.CAS_type ='".$str_reason_type."'";
				if($reason=='0'){
					$resonquery .= " OR den.CAS_type =''";
				}
			}
			$count++;
		}
		$resonquery.= ")";
		//IF "No Reason Code" selected then add a blank value
		if(strstr($str_reason_code, '"0"')){ $str_reason_code.=',""';}
	}

	$dateTitle = ($DateRangeFor=='transaction_date') ? 'DOT' : 'DOS';
	// REJECTION REASONS 
	$arrReasonCode[0] = 'No Reason Code';
	$rs=imw_query("Select * FROM cas_reason_code");
	while($res=imw_fetch_array($rs)){
		if(strlen($res['cas_desc'])>78){
			$res['cas_desc'] = substr($res['cas_desc'], 0 ,78).'...';
		}
		$arrReasonCode[$res['cas_code']]= $res['cas_code'].' - '.$res['cas_desc'];
	}
	
	// INSURANCE COMPANIES
	$rs=imw_query("Select id, name, in_house_code FROM insurance_companies");
	while($res=imw_fetch_array($rs)){
		$arrInsuranceComps[$res['id']]= $res['in_house_code'].' - '.$res['name'];
	}

	//CHECK FOR PRIVILEGED FACILITIES
	$facility_id=array();
	if(isPosFacGroupEnabled()){
		l$facility_id = $CLSReports->getFacilityName('', '0', 'array');

		if(sizeof($facility_id)<=0){
			$facility_id[0]='NULL';
		}
	}

	$arrOperatorSel=$operator_id;
	$arrReasonSel=$reason_code;
	if(sizeof($operator_id)>0){ $operatorName = implode(',', $operator_id); }
	if(sizeof($cpt)>0){ $rep_proc = implode(',', $cpt); }
	if(sizeof($icd10_codes)>0){ $icd10 = implode(',', $icd10_codes); }
	if(sizeof($facility_id)>0){ $sc_name = implode(',', $facility_id); }
	//if(empty($reason_code)===false){ $arrReasonSel = explode(',', $reason_code); }


	//---------------------------------------START DATA --------------------------------------------

	$join_part = $orderBy = '';

	if($process=='Detail'){
		$columns = " ,DATE_FORMAT(patChg.date_of_service, '".get_sql_date_format()."') as 'date_of_service', den.patient_id, 
		den.deniedBy, den.denialOperatorId, den.deniedById, patChgDet.procCode, patChgDet.diagnosis_id1, 
		patChgDet.diagnosis_id2, patChgDet.diagnosis_id3, patChgDet.diagnosis_id4, 
		pd.fname,pd.mname,pd.lname";
		$join_part = " LEFT JOIN patient_data pd ON pd.id = den.patient_id";
		$orderBy = " ,pd.lname";
	}
	//--- GET POSTED PAYMENT
	$qry = "Select patChg.encounter_id, den.deniedAmount, den.CAS_type,den.CAS_code $columns FROM patient_charge_list patChg 
	JOIN deniedpayment den ON den.encounter_id = patChg.encounter_id 
	JOIN patient_charge_list_details patChgDet ON patChgDet.charge_list_detail_id = den.charge_list_detail_id  
	$join_part
	WHERE 1=1";
	if($DateRangeFor=='transaction_date'){
		$qry .= " AND (DATE_FORMAT(den.entered_date, '%Y-%m-%d') BETWEEN '$startDate' AND '$endDate')";
	}else{
		$qry .= " AND (patChg.date_of_service BETWEEN '$startDate' AND '$endDate')";
	}
	if(empty($sc_name) == false){
		$qry.= " AND patChg.facility_id IN ($sc_name)";	
	}		
	if(empty($operatorName) == false){
		$qry .= " AND den.denialOperatorId IN($operatorName)";
	}
	if(trim($ins_comp_id) != ''){			
		$qry .= " AND (LOWER(den.deniedBy)='insurance' AND den.deniedById IN ($ins_comp_id))";
	}
	if(empty($reason_code)==false){			
		$qry .= $resonquery;
	}
	if(empty($rep_proc)==false){
		$qry.= " and patChgDet.procCode IN($rep_proc)";
	}
	if(empty($icd10)==false){
		$qry.= " AND (patChgDet.diagnosis_id1 in ($icd10) 
		or patChgDet.diagnosis_id2 in ($icd10)
		or patChgDet.diagnosis_id3 in ($icd10)
		or patChgDet.diagnosis_id4 in ($icd10))";
	}		
	$qry.= ' ORDER BY den.CAS_type'.$orderBy;
	
	$rs = imw_query($qry);
	while($res = imw_fetch_array($rs)){
		$printFile=true;
		$denAmt=0;
		$pid = $res['patient_id'];
		$eid = $res['encounter_id'];
		$rtype = $res['CAS_type'];
		$rcode = $res['CAS_code'];
		if($rtype != "" && $rcode != ""){
			$code = $rtype.' '.$rcode;
		} else{
			$code = ($rtype=='') ? 0 : $res['CAS_type'];
		}
		$patName = $pid.'~'.$res['fname'].'~'.$res['mname'].'~'.$res['lname'];
		$encounterIdArr[$eid] = $eid;
		$denAmt = $res['deniedAmount'];
		$arrResDataSumm[$code]['count']+= 1; 
		$arrResDataSumm[$code]['amount']+= $denAmt; 
		
		$denInsComp = (strtolower($res['deniedBy'])=='insurance')? $arrInsuranceComps[$res['deniedById']] : 'Denied by Patient';
		
		$arrResData[$code][$eid]['pat_name']=$patName;
		$arrResData[$code][$eid]['dos']=$res['date_of_service'];
		$arrResData[$code][$eid]['ins_comp']= $denInsComp;
		$arrResData[$code][$eid]['den_opr']= $providerNameArr[$res['denialOperatorId']];		
		$arrResData[$code][$eid]['amount']+= $denAmt;
		
		if($process=='Detail'){
			$cptCode = $arrAllCPTCodes[$res['procCode']];
			$arrCPTnDXCodes['cpt'][$code][$eid][$cptCode] = $cptCode;
			
			if(empty($res['diagnosis_id1'])==false) { $arrCPTnDXCodes['dx'][$code][$eid][$res['diagnosis_id1']] = $res['diagnosis_id1'];}
			if(empty($res['diagnosis_id2'])==false) { $arrCPTnDXCodes['dx'][$code][$eid][$res['diagnosis_id2']] = $res['diagnosis_id2'];}
			if(empty($res['diagnosis_id3'])==false) { $arrCPTnDXCodes['dx'][$code][$eid][$res['diagnosis_id3']] = $res['diagnosis_id3'];}
			if(empty($res['diagnosis_id4'])==false) { $arrCPTnDXCodes['dx'][$code][$eid][$res['diagnosis_id4']] = $res['diagnosis_id4'];}
		}
		$arrResDataOrd[$code]+=1;
	} 
	unset($rs);
	//	ARRANGE TOP REJECTIONS
	arsort($arrResDataOrd);

	if($printFile==true){
		$page_content='';
		
		if($process=='Summary'){
			if(sizeof($arrResDataOrd)>0){
				$dataExists=true;
				$totCount = $totAmount =0;		
				foreach($arrResDataOrd as $code => $val){
					$resDetail = $arrResDataSumm[$code];
					
					$totCount+=$resDetail['count'];
					$totAmount+=$resDetail['amount'];
					
					$reasonCode = $arrReasonCode[$code];
					if(!$arrReasonCode[$code]) { $reasonCode = 'Code Not Exists'; }
					
					$content_part .= '
					<tr>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:70%">&nbsp;'.$reasonCode.'</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:15%">'.$resDetail['count'].'&nbsp;</td>
						<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:15%">'.$CLSReports->numberFormat($resDetail['amount'],2).'&nbsp;</td>
					</tr>';			
				}
				
				// TOTAL
				$pdfColHeads='
				<table class="rpt_table rpt rpt_table-bordered rpt_padding" style="width:100%">
				<tr>
					<td class="text_b_w" style="width:70%; text-align:left;">Reason Code</td>
					<td class="text_b_w" style="width:15%; text-align:right;">No. Of Encounters</td>
					<td class="text_b_w" style="width:15%; text-align:right;">Amount&nbsp;</td>
				</tr>
				</table>';
				$htmlColHeads='
				<table class="rpt_table rpt rpt_table-bordered rpt_padding" style="width:100%">
				<tr>
					<td class="text_b_w" style="width:70%; text-align:left;">Reason Code</td>
					<td class="text_b_w" style="width:15%; text-align:right;">No. Of Encounters</td>
					<td class="text_b_w" style="width:15%; text-align:right;">Amount&nbsp;</td>
				</tr>
				</table>';
				
				$page_content .=' 
				<table class="rpt_table rpt rpt_table-bordered rpt_padding" style="width:100%">'
				.$content_part.'
				<tr><td style="height:2px; background:green;" colspan="3"></td></tr>
				<tr>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right; width:70%;">Total&nbsp;:</td>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right; width:15%;">'.$totCount.'&nbsp;</td>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right; width:15%;">'.$CLSReports->numberFormat($totAmount,2).'&nbsp;</td>
				</tr>
				<tr><td style="height:2px; background:green;" colspan="3"></td></tr>
				</table>';		
			}
		}else{
			// DETAIL
			if(sizeof($arrResDataOrd)>0){
				$dataExists=true;
				$totCount = $totAmount =0;		
				foreach($arrResDataOrd as $code => $val){

					$reasonCode = $arrReasonCode[$code];
					if(!$arrReasonCode[$code]) { $reasonCode = 'Code Not Exists'; }

					$content_part.='
					<tr>
						<td class="text_b_w" style="text-align:left; background-color:#878787; color:#fff" colspan="5">&nbsp;Reason Code:&nbsp;&nbsp;'.$reasonCode.'</td>
						<td class="text_b_w" style="text-align:left; background-color:#878787; color:#fff" colspan="3">&nbsp;Encounters#:&nbsp;'.$arrResDataSumm[$code]['count'].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Amount:&nbsp;'.$CLSReports->numberFormat($arrResDataSumm[$code]['amount'],2).'</td>						
					</tr>';

					$totCount+=$arrResDataSumm[$code]['count'];
					$totAmount+=$arrResDataSumm[$code]['amount'];

					foreach($arrResData[$code] as $eid => $resDetail){
						$pName = explode('~', $resDetail['pat_name']);

						$patient_name = core_name_format($pName[3], $pName[1], $pName[2]);
						
						$patient_name.= ' - '.$pName[0];
						
						$cptCodes = implode(', ', $arrCPTnDXCodes['cpt'][$code][$eid]);
						$dxCodes = implode(', ', $arrCPTnDXCodes['dx'][$code][$eid]);
						
						$content_part .= '
						<tr>
							<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:180px;">&nbsp;'.$patient_name.'</td>
							<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:100px;">'.$eid.'</td>
							<td class="text_10" bgcolor="#FFFFFF" style="text-align:center; width:100px;">'.$resDetail['dos'].'</td>
							<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:120px;">'.$cptCodes.'</td>
							<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:120px;">'.$dxCodes.'</td>
							<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:210px;">&nbsp;'.$resDetail['ins_comp'].'</td>
							<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:100px;">&nbsp;'.$resDetail['den_opr'].'</td>
							<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:100px;">'.$CLSReports->numberFormat($resDetail['amount'],2).'&nbsp;</td>
						</tr>';
					}
				}
				
				
				// TOTAL
				$pdfColHeads='
				<table class="rpt_table rpt rpt_table-bordered rpt_padding" style="width:100%;">
				<tr>
					<td class="text_b_w" style="width:180px; text-align:left;">&nbsp;Patient Name - Id</td>
					<td class="text_b_w" style="width:100px; text-align:center;">Encounter</td>
					<td class="text_b_w" style="width:100px; text-align:center;">DOS</td>
					<td class="text_b_w" style="width:120px; text-align:center;">CPT Codes</td>
					<td class="text_b_w" style="width:120px; text-align:center;">ICD-9/10 Codes</td>
					<td class="text_b_w" style="width:210px; text-align:center;">Ins. Company</td>
					<td class="text_b_w" style="width:100px; text-align:center;">Denied By Opr.</td>
					<td class="text_b_w" style="width:100px; text-align:right;">Amount&nbsp;</td>
				</tr>
				</table>';
				$htmlColHeads='<table class="rpt_table rpt rpt_table-bordered rpt_padding" style="width:100%;">
				<tr>
					<td class="text_b_w" style="width:180px; text-align:left;">&nbsp;Patient Name - Id</td>
					<td class="text_b_w" style="width:100px; text-align:center;">Encounter</td>
					<td class="text_b_w" style="width:100px; text-align:center;">DOS</td>
					<td class="text_b_w" style="width:100px; text-align:center;">CPT Codes</td>
					<td class="text_b_w" style="width:100px; text-align:center;">ICD-9/10 Codes</td>
					<td class="text_b_w" style="width:210px; text-align:center;">Ins. Company</td>
					<td class="text_b_w" style="width:100px; text-align:center;">Denied By Opr.</td>
					<td class="text_b_w" style="width:100px; text-align:right;">Amount&nbsp;</td>
				</tr>
				</table>';
					
				$page_content .=' 
				<table class="rpt_table rpt rpt_table-bordered rpt_padding" style="width:100%;">'
				.$content_part.'
				<tr><td style="height:2px; background:green;" colspan="8"></td></tr>
				<tr>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="5">Total Rejected Encounters: '.$totCount.'</td>
					<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="3">Total Rejected Amount: '.$CLSReports->numberFormat($totAmount,2).'&nbsp;</td>
				</tr>
				<tr><td style="height:2px; background:green;" colspan="8"></td></tr>
				</table>';		
			}
		}
		
		if(trim($page_content) != ''){				
			
			//--- PAGE HEADER DATA ---
			$globalDateFormat = phpDateFormat();
			$curDate = date($globalDateFormat.' H:i A');
			$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
			$op_name = $op_name_arr[1][0];
			$op_name .= $op_name_arr[0][0];

			$operatorSel='All';
			$insuranceSel='All';
			$reasonSel='All';
			if(sizeof($arrOperatorSel)>0){
				$operatorSel = (sizeof($arrOperatorSel)>1) ? 'Multi' : $providerNameArr[$operatorName];  
			}
			if(sizeof($arrInsuranceSel)>0){
				$insuranceSel = (sizeof($arrInsuranceSel)>1) ? 'Multi' : $arrInsuranceComps[$ins_comp_id];  
			}
			if(sizeof($arrReasonSel)>0){
				$reasonSel = (sizeof($arrReasonSel)>1) ? 'Multi' : substr($arrReasonCode[$reason_code], 0, 30).'...';  
			}

			
			$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
			$html_page_content = <<<DATA
				$stylePDF
				<page backtop="16mm" backbottom="10mm">			
				<page_footer>
					<table style="width: 100%;">
						<tr>
							<td style="text-align:center;width:100%"> Page [[page_cu]]/[[page_nb]]</td>
						</tr>
					</table>
				</page_footer>
				<page_header>
					<table class="rpt_table rpt rpt_table-bordered rpt_padding">
						<tr>
							<td class="rptbx1" style="text-align:left; width:320px;">Top Rejection Reasons Report ($process)</td>
							<td class="rptbx2" style="text-align:left; width:400px;">$dateTitle ($Start_date - $End_date)</td>
							<td class="rptbx3" style="text-align:left; width:320px;">Created by: $op_name on $curDate</td>
						</tr>	
						<tr>
							<td class="rptbx1" style="text-align:left;">Selected Reason: $reasonSel</td>
							<td class="rptbx2" style="text-align:left;">Selected Operator : $operatorSel</td>
							<td class="rptbx3" style="text-align:left;">Selected Insurance : $insuranceSel</td>
						</tr>					
					</table>
					$pdfColHeads
				</page_header>
				$page_content
				</page>
DATA;
			//--- CREATE HTML FILE FOR PDF PRINTING ---
			
			$file_location = write_html($html_page_content);
			
			//--- CSV FILE DATA --
			$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
			$page_content = <<<DATA
				$styleHTML
				<table class="rpt_table rpt rpt_table-bordered rpt_padding">
					<tr>
						<td class="rptbx1" style="text-align:left; width:33%">Top Rejection Reasons Report ($process)</td>
						<td class="rptbx2" style="text-align:left; width:34%">$dateTitle ($Start_date - $End_date)</td>
						<td class="rptbx3" style="text-align:left; width:33%">Created by: $op_name on $curDate</td>
					</tr>	
					<tr>
						<td class="rptbx1" style="text-align:left;">Selected Reason: $reasonSel</td>
						<td class="rptbx2" style="text-align:left;">Selected Operator : $operatorSel</td>
						<td class="rptbx3" style="text-align:left;">Selected Insurance : $insuranceSel</td>
					</tr>					
				</table>
				$htmlColHeads
				$page_content
DATA;
		}
	}
}

if($callFrom != 'scheduled'){
	$op='l';
	if($printFile==true){
		if($output_option=='view' || $output_option=='output_csv'){
			echo $page_content;
		}
	}else {
		 echo '<div class="text-center alert alert-info">No record exists.</div>';
	}
}
?>