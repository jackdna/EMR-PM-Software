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
FILE : pt_status_result.php
PURPOSE : PATIENT STATUS REPORT RESULT
ACCESS TYPE : DIRECT
*/
$arrFacilitySel=array();
$arrDoctorSel=array();

$printFile = true;
if($_POST['form_submitted']){
	$printFile = false;
	
	$activeStsId = get_account_status_id('active');
	if($activeStsId<=0){$activeStsId=0; }
	
	$arrPatAcctSts = get_all_account_status();
	$status_types = implode(',', $status_types);

	$sel_status_types='';
	if(empty($status_types)===true){
		$sel_status_types = 'Account Status, Deferred Patients, VIP Patients, Hold Statements';	
	}else{
		if(stristr($status_types, 'account_status')){ $arr_sel_status_types[]='Account Status'; }
		if(stristr($status_types, 'deferred_patients')){ $arr_sel_status_types[]='Deferred Patients'; }
		if(stristr($status_types, 'vip_patients')){ $arr_sel_status_types[]='VIP Patients'; }
		if(stristr($status_types, 'hold_statements')){ $arr_sel_status_types[]='Hold Statements'; }
		$sel_status_types = implode(', ', $arr_sel_status_types);
	}

	//CHECK FOR PRIVILEGED FACILITIES
	$str_facility_id='';
	if(isPosFacGroupEnabled()){
		$facility_id = $CLSReports->getFacilityName('', '0', 'array');

		if(sizeof($facility_name)<=0){
			$facility_id[0]='NULL';
		}

		$str_facility_id=implode(',', $facility_id);
	}	

		
	//---------------------------------------START DATA --------------------------------------------
	// ACCOUNT STATUS
	if(empty($status_types)===true || stristr($status_types, 'account_status')){
		$rs=imw_query("Select pd.id, pd.fname,pd.mname,pd.lname, pd.pat_account_status FROM patient_data pd WHERE pd.pat_account_status NOT IN(0, $activeStsId) ORDER BY pd.lname, pd.fname");
		while($res=imw_fetch_array($rs)){	
			$pid = $res['id'];
			$patName = $pid.'~'.$res['fname'].'~'.$res['mname'].'~'.$res['lname'];
			$arrAcctStsResult[$pid]['pat_name']=$patName;
			$arrAcctStsResult[$pid]['account_status']= $res['pat_account_status'];
		}
		
		if(sizeof($arrAcctStsResult)>0){
			$strPatIds  =implode(',', array_keys($arrAcctStsResult));
			
			$qry = "Select patient_charge_list_details.patient_id, patient_charge_list_details.pat_due FROM patient_charge_list_details 
			JOIN patient_charge_list ON patient_charge_list.charge_list_id=patient_charge_list_details.charge_list_id 
			WHERE patient_charge_list_details.patient_id IN(".$strPatIds.") AND patient_charge_list_details.del_status='0'";
			if(empty($str_facility_id)==false){
				$qry.=" AND patient_charge_list.facility_id IN(".$str_facility_id.")";
			}
			$rs = imw_query($qry);
			while($res = imw_fetch_array($rs)){
				$printFile=true;
				$denAmt=0;
				$pid = $res['patient_id'];
				
				$arrAcctStsResult[$pid]['amount']+= $res['pat_due'];
			} 
		}
		unset($rs);
	}


	// DEFERRED PATIENTS	
	if(empty($status_types)===true || stristr($status_types, 'deferred_patients')){
		
		$qry = "Select patChgDet.pat_due, pd.id, pd.fname,pd.mname,pd.lname, SUM(patChgDet.pat_due) as 'pat_due'  
		FROM patient_charge_list_details patChgDet 
		JOIN patient_charge_list patChg ON patChg.charge_list_id=patChgDet.charge_list_id 
		JOIN patient_data pd ON pd.id = patChgDet.patient_id 
		WHERE LOWER(pd.lname)!='doe' AND patChgDet.differ_patient_bill='true' AND patChgDet.del_status='1'";
		if(empty($str_facility_id)==false){
			$qry.=" AND patChg.facility_id IN(".$str_facility_id.")";
		}
		$qry.=" GROUP BY pd.id ORDER BY pd.lname, pd.fname";
		$rs = imw_query($qry);
		while($res = imw_fetch_array($rs)){
			$printFile=true;
			$denAmt=0;
			$pid = $res['id'];
			
			$patName = $pid.'~'.$res['fname'].'~'.$res['mname'].'~'.$res['lname'];
			
			$arrPatDefResult[$pid]['pat_name']=$patName;
			$arrPatDefResult[$pid]['amount']+= $res['pat_due'];
		} 
		unset($rs);
	}


	// VIP PATIENTS	
	if(empty($status_types)===true || stristr($status_types, 'vip_patients')){
		$qry = "Select patChgDet.patient_id, patChgDet.pat_due FROM patient_charge_list patChg 
		JOIN patient_charge_list_details patChgDet ON patChgDet.charge_list_id = patChg.charge_list_id 
		WHERE patChg.vipStatus='true' AND patChgDet.del_status='0'";
		if(empty($str_facility_id)==false){
			$qry.=" AND patChg.facility_id IN(".$str_facility_id.")";
		}		
		$rs = imw_query($qry);
		while($res = imw_fetch_array($rs)){
			$printFile=true;
			$denAmt=0;
			$pid = $res['patient_id'];
			
			$arrVIPResult[$pid]['amount']+= $res['pat_due'];
		} 
		unset($rs);

		if(sizeof($arrVIPResult)>0){
			$strPatIds  =implode(',', array_keys($arrVIPResult));
			$qryPart=" OR pd.id IN(".$strPatIds.")";
		}
		$qry = "Select pd.id, pd.fname,pd.mname,pd.lname FROM patient_data pd WHERE pd.vip='1' ".$qryPart." ORDER BY pd.lname, pd.fname";
		$rs = imw_query($qry);
		while($res = imw_fetch_array($rs)){
			$printFile=true;
			$denAmt=0;
			$pid = $res['id'];
			$patName = $pid.'~'.$res['fname'].'~'.$res['mname'].'~'.$res['lname'];
			
			$arrVIPResult[$pid]['pat_name']=$patName;
		} 
		unset($rs);
	}

	// HOLD STATEMENTS	
	if(empty($status_types)===true || stristr($status_types, 'hold_statements')){
		
		$qry = "Select patChgDet.pat_due, pd.id, pd.fname,pd.mname,pd.lname, patChgDet.pat_due   
		FROM patient_data pd LEFT JOIN patient_charge_list_details patChgDet ON patChgDet.patient_id = pd.id  
		LEFT JOIN patient_charge_list patChg ON patChg.charge_list_id = patChgDet.charge_list_id 
		WHERE pd.hold_statement='1'";
		if(empty($str_facility_id)==false){
			$qry.=" AND patChg.facility_id IN(".$str_facility_id.")";
		}		
		$qry.=" ORDER BY pd.lname, pd.fname";
		$rs = imw_query($qry);
		while($res = imw_fetch_array($rs)){
			$printFile=true;
			$denAmt=0;
			$pid = $res['id'];
			
			$patName = $pid.'~'.$res['fname'].'~'.$res['mname'].'~'.$res['lname'];
			
			$arrHSResult[$pid]['pat_name']=$patName;
			$arrHSResult[$pid]['amount']+= $res['pat_due'];
		} 
		unset($rs);
	}	


	if($printFile==true){
		$page_content='';

		// HTML CREATION
		$curDate = date(''.phpDateFormat().' H:i A');
		$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
		$op_name = $op_name_arr[1][0];
		$op_name .= $op_name_arr[0][0];

		$operatorSel='All';
		$insuranceSel='All';
		$reasonSel='All';
		$grandPatDue=0;
		
		$pdfHeader='
		
		<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%" >
				<tr class="rpt_headers">
					<td class="rptbx1" width="250">Patient Status Report</td>
					<td class="rptbx2" width="550">Selected Status Types: '.$sel_status_types.'</td>
					<td class="rptbx3" width="250">Created by: '.$op_name.' on '.$curDate.'</td>
				</tr>			
			</table>
		';

		// ACCOUNT STATUS
		if(sizeof($arrAcctStsResult)>0){
			$dataExists=true;
			$content_part='';
			$totCount = $totAmount =0;		
			foreach($arrAcctStsResult as $pid => $patDetail){
				$pName = explode('~', $patDetail['pat_name']);
				$patient_name = core_name_format($pName[3], $pName[1], $pName[2]);
				$patient_name.= ' - '.$pName[0];

				$totAmount+=$patDetail['amount'];
				
				$content_part .= '
				<tr>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:25%">&nbsp;'.$patient_name.'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:30%">&nbsp;'.$arrPatAcctSts[$patDetail['account_status']].'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:15%">'.showCurrency().''.number_format($patDetail['amount'],2).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="width:30%"></td>
				</tr>';
			}
			// TOTAL
			$AS_pdfColHeads='
			<table style="padding:1px; border-collapse:separate; border:none; width:100%" bgcolor="#FFF3E8" class="rpt_table rpt_table-bordered">
			<tr><td class="text_b_w" style="text-align:left; width:100%; cursor:pointer;" colspan="4">Account Status Patients</td></tr>
			<tr>
				<td class="text_b_w" style="width:25%; text-align:left;">&nbsp;Patient Name - Id</td>
				<td class="text_b_w" style="width:30%; text-align:left;">&nbsp;Account Status</td>
				<td class="text_b_w" style="width:15%; text-align:right;">Patient Due&nbsp;</td>
				<td class="text_b_w" style="width:30%;"></td>
			</tr>
			</table>';
			$AS_htmlColHeads='<table style="padding:1px; border-collapse:separate; border:none; width:100%" bgcolor="#FFF3E8" class="rpt_table rpt_table-bordered">
			<tr><td class="text_b_w" style="text-align:left; width:100%; cursor:pointer;" colspan="4">Account Status Patients</td></tr>			
			<tr>
				<td class="text_b_w" style="width:25%; text-align:left;">&nbsp;Patient Name - Id</td>
				<td class="text_b_w" style="width:30%; text-align:left;">&nbsp;Account Status</td>
				<td class="text_b_w" style="width:15%; text-align:right;">Patient Due&nbsp;</td>
				<td class="text_b_w" style="width:30%;"></td>
			</tr>
			</table>';
				
			$AS_page_content .=' 
			<table style="width:100%" bgcolor="#FFF3E8" class="rpt_table rpt_table-bordered">'
			.$content_part.'
			<tr><td style="height:2px; background:green;" colspan="4"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="3">Total : '.showCurrency().''.number_format($totAmount,2).'&nbsp;</td>
				<td class="text_10b" bgcolor="#FFFFFF"></td>
			</tr>
			<tr><td style="height:2px; background:green;" colspan="4"></td></tr>
			</table>';	
			
			$grandPatDue+=$totAmount;
			
			$AS_pdf_contents='
			<page backtop="18mm" backbottom="10mm">			
			<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align:center;width:100%"> Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
			<page_header>'.
				$pdfHeader.
				$AS_pdfColHeads.
			'</page_header>'.			
				$AS_page_content.
			'</page>';
		}

		// DEFERRED PATIENTS
		if(sizeof($arrPatDefResult)>0){
			$dataExists=true;
			$content_part='';
			$totCount = $totAmount =0;		
			foreach($arrPatDefResult as $pid => $patDetail){
				$pName = explode('~', $patDetail['pat_name']);
				$patient_name = core_name_format($pName[3], $pName[1], $pName[2]);
				$patient_name.= ' - '.$pName[0];
				$totAmount+=$patDetail['amount'];
				
				$content_part .= '
				<tr>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:25%">&nbsp;'.$patient_name.'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:15%">'.showCurrency().''.number_format($patDetail['amount'],2).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="width:60%"></td>
				</tr>';
			}
			// TOTAL
			$DE_pdfColHeads='
			<table style="padding:1px; border-collapse:separate; border:none; width:100%" bgcolor="#FFF3E8" class="rpt_table rpt_table-bordered">
			<tr><td class="text_b_w" style="text-align:left; width:100%; cursor:pointer;" colspan="3">Patients with Deffered Bill</td></tr>
			<tr>
				<td class="text_b_w" style="width:25%; text-align:left;">&nbsp;Patient Name - Id</td>
				<td class="text_b_w" style="width:15%; text-align:right;">Patient Due&nbsp;</td>
				<td class="text_b_w" style="width:60%;"></td>
			</tr>
			</table>';
			$DE_htmlColHeads='<table style="padding:1px; border-collapse:separate; border:none; width:100%" bgcolor="#FFF3E8" class="rpt_table rpt_table-bordered">
			<tr><td class="text_b_w" style="text-align:left; width:100%; cursor:pointer;" colspan="3">Patients with Deffered Bill</td></tr>			
			<tr>
				<td class="text_b_w" style="width:25%; text-align:left;">&nbsp;Patient Name - Id</td>
				<td class="text_b_w" style="width:15%; text-align:right;">Patient Due&nbsp;</td>
				<td class="text_b_w" style="width:60%;"></td>
			</tr>
			</table>';
				
			$DE_page_content .=' 
			<table style="width:100%" bgcolor="#FFF3E8" class="rpt_table rpt_table-bordered">'
			.$content_part.'
			<tr><td style="height:2px; background:green;" colspan="3"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="2">Total : '.showCurrency().''.number_format($totAmount,2).'&nbsp;</td>
				<td class="text_10b" bgcolor="#FFFFFF"></td>
			</tr>
			<tr><td style="height:2px; background:green;" colspan="3"></td></tr>
			</table>';
			
			$grandPatDue+=$totAmount;
			
			$DE_pdf_contents='
			<page backtop="18mm" backbottom="10mm">			
			<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align:center;width:100%"> Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
			<page_header>'.
				$pdfHeader.
				$DE_pdfColHeads.
			'</page_header>'.			
				$DE_page_content.
			'</page>';
		}

		// VIP PATIENTS
		if(sizeof($arrVIPResult)>0){
			$dataExists=true;
			$content_part='';
			$totCount = $totAmount =0;		
			foreach($arrVIPResult as $pid => $patDetail){
				$pName = explode('~', $patDetail['pat_name']);
				$patient_name = core_name_format($pName[3], $pName[1], $pName[2]);
				$patient_name.= ' - '.$pName[0];

				$totAmount+=$patDetail['amount'];
				
				$content_part .= '
				<tr>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:25%">&nbsp;'.$patient_name.'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:15%">'.showCurrency().''.number_format($patDetail['amount'],2).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="width:60%"></td>
				</tr>';
			}
			// TOTAL
			$VIP_pdfColHeads='
			<table style="padding:1px; border-collapse:separate; border:none; width:100%" bgcolor="#FFF3E8" class="rpt_table rpt_table-bordered">
			<tr><td class="text_b_w" style="text-align:left; width:100%; cursor:pointer;" colspan="3">VIP Patients</td></tr>
			<tr>
				<td class="text_b_w" style="width:25%; text-align:left;">&nbsp;Patient Name - Id</td>
				<td class="text_b_w" style="width:15%; text-align:right;">Patient Due&nbsp;</td>
				<td class="text_b_w" style="width:60%;"></td>
			</tr>
			</table>';
			$VIP_htmlColHeads='<table style="padding:1px; border-collapse:separate; border:none; width:100%" bgcolor="#FFF3E8" class="rpt_table rpt_table-bordered">
			<tr><td class="text_b_w" style="text-align:left; width:100%; cursor:pointer;" colspan="3">VIP Patients</td></tr>			
			<tr>
				<td class="text_b_w" style="width:25%; text-align:left;">&nbsp;Patient Name - Id</td>
				<td class="text_b_w" style="width:15%; text-align:right;">Patient Due&nbsp;</td>
				<td class="text_b_w" style="width:60%;"></td>
			</tr>
			</table>';
				
			$VIP_page_content .=' 
			<table style="width:100%" bgcolor="#FFF3E8" class="rpt_table rpt_table-bordered">'
			.$content_part.'
			<tr><td style="height:2px; background:green;" colspan="3"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="2">Total : '.showCurrency().''.number_format($totAmount,2).'&nbsp;</td>
				<td class="text_10b" bgcolor="#FFFFFF"></td>
			</tr>
			<tr><td style="height:2px; background:green;" colspan="3"></td></tr>
			</table>';	
			
			$grandPatDue+=$totAmount;

			$VIP_pdf_contents='
			<page backtop="18mm" backbottom="10mm">			
			<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align:center;width:100%"> Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
			<page_header>'.
				$pdfHeader.
				$VIP_pdfColHeads.
			'</page_header>'.			
				$VIP_page_content.
			'</page>';							
		}

		// HOLD PATIENTS
		if(sizeof($arrHSResult)>0){
			$dataExists=true;
			$content_part='';
			$totCount = $totAmount =0;		
			foreach($arrHSResult as $pid => $patDetail){
				$pName = explode('~', $patDetail['pat_name']);
				$patient_name = core_name_format($pName[3], $pName[1], $pName[2]);
				$patient_name.= ' - '.$pName[0];

				$totAmount+=$patDetail['amount'];
				
				$content_part .= '
				<tr>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:left; width:25%">&nbsp;'.$patient_name.'</td>
					<td class="text_10" bgcolor="#FFFFFF" style="text-align:right; width:15%">'.showCurrency().''.number_format($patDetail['amount'],2).'&nbsp;</td>
					<td class="text_10" bgcolor="#FFFFFF" style="width:60%"></td>
				</tr>';
			}
			// TOTAL
			$HS_pdfColHeads='
			<table style="padding:1px; border-collapse:separate; border:none; width:100%" bgcolor="#FFF3E8" class="rpt_table rpt_table-bordered">
			<tr><td class="text_b_w" style="text-align:left; width:100%; cursor:pointer;" colspan="3">Patients with Hold Statements</td></tr>
			<tr>
				<td class="text_b_w" style="width:25%; text-align:left;">&nbsp;Patient Name - Id</td>
				<td class="text_b_w" style="width:15%; text-align:right;">Patient Due&nbsp;</td>
				<td class="text_b_w" style="width:60%;"></td>
			</tr>
			</table>';
			$HS_htmlColHeads='<table style="padding:1px; border-collapse:separate; border:none; width:100%" bgcolor="#FFF3E8" class="rpt_table rpt_table-bordered">
			<tr><td class="text_b_w" style="text-align:left; width:100%; cursor:pointer;" colspan="3">Patients with Hold Statements</td></tr>
			<tr>
				<td class="text_b_w" style="width:25%; text-align:left;">&nbsp;Patient Name - Id</td>
				<td class="text_b_w" style="width:15%; text-align:right;">Patient Due&nbsp;</td>
				<td class="text_b_w" style="width:60%;"></td>
			</tr>
			</table>';
				
			$HS_page_content .=' 
			<table style="width:100%" bgcolor="#FFF3E8" class="rpt_table rpt_table-bordered">'
			.$content_part.'
			<tr><td style="height:2px; background:green;" colspan="3"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:right;" colspan="2">Total : '.showCurrency().''.number_format($totAmount,2).'&nbsp;</td>
				<td class="text_10b" bgcolor="#FFFFFF"></td>
			</tr>
			<tr><td style="height:2px; background:green;" colspan="3"></td></tr>
			</table>';	

			$grandPatDue+=$totAmount;
			
			$HS_pdf_contents='
			<page backtop="18mm" backbottom="10mm">			
			<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align:center;width:100%"> Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
			<page_header>'.
				$pdfHeader.
				$HS_pdfColHeads.
			'</page_header>'.			
				$HS_page_content.
			'</page>';							
		}

		$grandRow='
		<table style="padding:1px; border-collapse:separate; border:none; width:100%" bgcolor="#FFF3E8" class="rpt_table rpt_table-bordered">
			<tr><td style="height:3px; background:green;"></td></tr>
			<tr>
				<td class="text_10b" bgcolor="#FFFFFF" style="text-align:left; width:100%">&nbsp;&nbsp;Grand Patient Due : '.showCurrency().''.number_format($grandPatDue,2).'&nbsp;</td>
			</tr>
			<tr><td style="height:3px; background:green;"></td></tr>
		</table>';
		
		//ALL HTML FOR PAGE
		$page_content=
			$AS_htmlColHeads.
			$AS_page_content.
			$DE_htmlColHeads.
			$DE_page_content.
			$VIP_htmlColHeads.
			$VIP_page_content.
			$HS_htmlColHeads.
			$HS_page_content.
			$grandRow;

		$pdf_page_content=
			$AS_pdf_contents.
			$DE_pdf_contents.
			$VIP_pdf_contents.
			$HS_pdf_contents.
			$grandRow;
	}
}

//--- CREATE PDF FILE FOR PRINTING -----
$hasData = 0;
if($printFile == true){
	$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
	$csvFileData= $styleHTML.$pdfHeader.$page_content;

	$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
	$strHTML = $stylePDF.$pdf_page_content;

	//--- CREATE HTML FILE FOR PDF ----
	$file_location = write_html($strHTML); 
	$csvFileData;	
	$hasData = 1;
}else{
	$csvFileData = '<div class="text-center alert alert-info">No Record Found.</div>';
}

echo $csvFileData;
?>
