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
FILE : INS_DEFER_RESULT_RESULT.PHP
PURPOSE :  FETCH ALL RESULT FOR PAYROLL REPORT DATA
ACCESS TYPE : DIRECT
*/
$dateFormat= get_sql_date_format();
$curDate=date($phpDateFormat);
$billTypeArr = explode(",", $billType);

$printFile = true;
if($_POST['form_submitted']){
	//DATE RANGE ARRAY WEEKLY/MONTHLY/QUARTERLY
	$arrDateRange= $CLSCommonFunction->changeDateSelection();

	if($dayReport=='Daily'){
		$Start_date = $End_date= date($phpDateFormat);
	}else if($dayReport=='Weekly'){
		$Start_date = $arrDateRange['WEEK_DATE'];
		$End_date= date($phpDateFormat);
	}else if($dayReport=='Monthly'){
		$Start_date = $arrDateRange['MONTH_DATE'];
		$End_date= date($phpDateFormat);
	}else if($dayReport=='Quarterly'){
		$Start_date = $arrDateRange['QUARTER_DATE_START'];
		$End_date = $arrDateRange['QUARTER_DATE_END'];
	}

	if(trim($Start_date) == ""){
		$Start_date = $curDate;
	}
	if(trim($End_date) == ""){
		$End_date = $curDate;
	}

	//--- GET ALL PROVIDER NAME ----
	$providerRs = imw_query("Select id,fname,mname,lname from users");
	$providerNameArr = array();
	while($providerResArr = imw_fetch_assoc($providerRs)){
		$id = $providerResArr['id'];
		$providerNameArr[$id] = core_name_format($providerResArr['lname'], $providerResArr['fname'], $providerResArr['mname']);
	}

	//CHECK FOR PRIVILEGED FACILITIES
	if(sizeof($facility_id)<=0 && isPosFacGroupEnabled()){
		$facility_id = $CLSReports->getFacilityName('', '0', 'array');

		if(sizeof($facility_id)<=0){
			$facility_id[0]='000001';
		}
	}
	
	//MAKE COMMA SEPEARATED OF ALL SEARCH CRITERIA
	$grp_id= (sizeof($grp_id)>0) ? implode(',',$grp_id) : '';
	$facility_name= (sizeof($facility_id)>0) ? implode(',',$facility_id) : '';
	$Physician= (sizeof($filing_provider)>0) ? implode(',',$filing_provider) : '';
	$credit_physician= (sizeof($crediting_provider)>0) ? implode(',',$crediting_provider) : '';
	$pt_status= (sizeof($pt_status)>0) ? implode(',',$pt_status) : '';
	$acc_status= (sizeof($acc_status)>0) ? implode(',',$acc_status) : '';
	
	$group_name = $CLSReports->report_display_selected($grp_id,'group',1,$core_drop_groups_cont);
	$physician_name = $CLSReports->report_display_selected($Physician,'physician',1,$phyOption_cont);
	$crediting_physician_name = $CLSReports->report_display_selected($credit_physician,'physician',1,$allCrPhyCount);
	$practice_name = $CLSReports->report_display_selected($facility_name,'practice',1,$facilityName_cont);
	$account_status_name = $CLSReports->report_display_selected($acc_status,'account_status',1,$acc_status_cont);
	
	$patient_status_name="All";
	$pt_status_exp=explode(',', $pt_status);
	if(count($pt_status_exp)==1 && $pt_status!=""){
		$patient_status_name=$pt_status;
	}else if(count($pt_status_exp)>0 && count($pt_status_exp)!=$pat_status_cont && $pt_status!=""){
		$patient_status_name="Multi";
	}
	if(empty($pt_status)==false){
		$pt_status = "'".str_replace(",", "','", $pt_status)."'";
	}

	 
	//--- CHANGE DATE FORMAT FOR DATABASE -----------
	$StartDate = getDateFormatDB($Start_date);
	$EndDate = getDateFormatDB($End_date);	
	$printFile = false;
	$qry = "select patient_charge_list.encounter_id,patient_charge_list.patient_id,
			date_format(patient_charge_list.date_of_service, '".$dateFormat."') as date_of_service,
			patient_charge_list.facility_id, pos_facilityies_tbl.facilityPracCode,
			patient_charge_list.patient_id,patient_charge_list_details.newBalance,
			patient_charge_list_details.procCharges * patient_charge_list_details.units as procCharges,
			patient_charge_list_details.write_off,cpt_fee_tbl.cpt4_code,
			patient_charge_list_details.charge_list_detail_id,
			patient_charge_list_details.coPayAdjustedAmount,patient_charge_list.primary_provider_id_for_reports as 'primaryProviderId',
			patient_charge_list.secondaryProviderId,
			patient_charge_list.totalAmt,patient_charge_list.approvedTotalAmt,patient_charge_list.primaryInsuranceCoId,
			patient_charge_list.secondaryInsuranceCoId,
			patient_charge_list.tertiaryInsuranceCoId,
			patient_data.lname as pat_lname,patient_data.fname as pat_fname,
			patient_data.mname as pat_mname,users.lname as providerLname,
			users.fname as providerFname,users.mname as providerMname,patient_data.vip
			from patient_charge_list 
			join patient_charge_list_details on patient_charge_list_details.charge_list_id = 
			patient_charge_list.charge_list_id 
			join patient_data on patient_data.id = patient_charge_list.patient_id
			join users on users.id = patient_charge_list.primary_provider_id_for_reports
			join pos_facilityies_tbl on pos_facilityies_tbl.pos_facility_id = 
			patient_charge_list.facility_id
			join cpt_fee_tbl on cpt_fee_tbl.cpt_fee_id = patient_charge_list_details.procCode
			where patient_charge_list_details.del_status='0' 
			";
	if(empty($grp_id) == false){
		$oqry.= " AND patient_charge_list.gro_id IN ($grp_id)";
	}
	if(empty($Physician) == false){
		$qry.= " AND patient_charge_list.primary_provider_id_for_reports IN ($Physician)";
	}
	if(empty($credit_physician) === false){
		$qry.= " and patient_charge_list_details.secondaryProviderId IN ($credit_physician)";
	}
	if($chksamebillingcredittingproviders==1){
		$qry.= " and patient_charge_list_details.primary_provider_id_for_reports!=patient_charge_list_details.secondaryProviderId";							
	}	
	if(trim($facility_name) != ''){
		$qry.= " and patient_charge_list.facility_id IN ($facility_name)";
	}
	if($no_date<=0){
		$qry.= " and (patient_charge_list.date_of_service between '$StartDate' and '$EndDate')";
	}
	$sel_def_ins="No";
	$sel_def_pat="No";
	if($def_ins_chg>0){
		$ins_def_srh ="patient_charge_list_details.differ_insurance_bill = 'true'";
		$sel_def_ins="Yes";
	}
	if($def_pat_chg>0){
		$sel_def_pat="Yes";
		if($ins_def_srh!=""){
			$whr_or="or";
		}
		$pat_def_srh=" $whr_or patient_charge_list_details.differ_patient_bill = 'true'";
	}
	$sel_vip_status="No";

	if($vip_pat>0){
		if($ins_def_srh=="" &&  $pat_def_srh==""){
			$whr_or="";
		}else{
			$whr_or="or";
		}
		$pat_vip_srh=" $whr_or patient_data.vip>0";
		$sel_vip_status="Yes";
	}
	
	if(empty($ins_def_srh)==false || empty($pat_def_srh)==false || empty($pat_vip_srh)==false){
		$qry.= " and ($ins_def_srh $pat_def_srh $pat_vip_srh)";
	}

	if(trim($pt_status) != ''){
		$qry .= " and patient_data.patientStatus IN ($pt_status)";
	}
	
	if(trim($acc_status) != ''){
		$qry.= " and patient_data.pat_account_status IN ($acc_status)";
	}
	
	$qry.= " group by patient_charge_list_details.charge_list_id order by 
			users.lname, users.fname, users.mname,
			patient_charge_list.date_of_service desc";
	$rs=imw_query($qry);		
	$encounter_id_arr = array();
	$ins_id_arr = array();
	while($res=imw_fetch_assoc($rs)){	
		$encounter_id = $res['encounter_id'];
		$encounter_id_arr[$encounter_id] = $res['encounter_id'];
		$primaryInsuranceCoId = $res['primaryInsuranceCoId'];
		$secondaryInsuranceCoId = $res['secondaryInsuranceCoId'];
		$doctor_id=$res['primaryProviderId'];
		$encounter_id = $res['encounter_id'];
		
		if(empty($Physician) == true && empty($credit_physician) === false){
			$doctor_id=$res['secondaryProviderId'];
		}
		
		if(trim($primaryInsuranceCoId) != ''){
			$ins_id_arr[$primaryInsuranceCoId] = $primaryInsuranceCoId;
		}
		if(trim($secondaryInsuranceCoId) != ''){
			$ins_id_arr[$secondaryInsuranceCoId] = $secondaryInsuranceCoId;
		}
		if(trim($tertiaryInsuranceCoId) != ''){
			$ins_id_arr[$tertiaryInsuranceCoId] = $tertiaryInsuranceCoId;
		}
		$chargesQryRes_data[$doctor_id][$res['facility_id']][]=$res;
		$provider_name_arr[$doctor_id]=$providerNameArr[$doctor_id];
		$fac_name_arr[$doctor_id][$res['facility_id']]=$res['facilityPracCode'];
	}

	
	//--- GET ALL PAYMENTS -----
	$encounter_id_str = join(',',$encounter_id_arr);
	
	//GETTING ENCOUNTER NOTES
	if(sizeof($encounter_id_arr)>0){
		$qry="Select encounter_id, encComments FROM paymentscomment WHERE encounter_id IN(".$encounter_id_str.")";
		$rs=imw_query($qry);
		while($res=imw_fetch_assoc($rs)){
			$eid=$res['encounter_id'];
			if($res['encComments']!=''){
				$arrEncNotes[$eid][]=stripslashes($res['encComments']);
			}
		}
	}

	if(count($chargesQryRes_data) > 0){
		$printFile = true;
		$page_content_data = '';
		$total_amount_arr = array();
		$sub_total_arr = array();
		

		foreach($provider_name_arr as $prov_key => $prov_val){
			$chargesQryRes_prov_data=$prov_val;
			$total_amount_arr=array();
			$csv_data .= <<<DATA
					<tr>
						<td class="text_b_w" colspan="7" valign="top" width="752">Physician Name: $prov_val</td>
					</tr>
DATA;
			$pdf_data .= <<<DATA
					<tr bgcolor="#FFFFFF"><td colspan="7" style="padding-top:1px;"></td></tr>
					<tr>
						<td class="text_b_w" colspan="7" valign="top" width="752">Physician Name: $prov_val</td>
					</tr>
DATA;
			foreach($fac_name_arr[$prov_key] as $fac_key => $fac_val){	
			$fac_total_proc_charges=array();
			$chargesQryRes_fac_data=$fac_val;	
			$csv_data .= <<<DATA
					<tr>
						<td class="text_b_w" colspan="7" valign="top" width="752">Facility: $fac_val</td>
					</tr>
DATA;
				$pdf_data .= <<<DATA
					<tr>
						<td class="text_b_w" colspan="7" valign="top" width="752">Facility: $fac_val</td>
					</tr>
DATA;
				for($i=0;$i<=count($chargesQryRes_data[$prov_key][$fac_key]);$i++){
					$charges_pat_data=$chargesQryRes_data[$prov_key][$fac_key];
					if($charges_pat_data[$i]['date_of_service']!=""){
						
						$strEncNotes='';
						$eid=$charges_pat_data[$i]['encounter_id'];
						$comments= stripslashes($charges_pat_data[$i]['comment']);
						$totalAmt=$charges_pat_data[$i]['totalAmt'];
						$write_off=$charges_pat_data[$i]['totalAmt']-$charges_pat_data[$i]['approvedTotalAmt'];
						
						if(sizeof($arrEncNotes[$eid])>0){
							$strEncNotes= implode('<br>', $arrEncNotes[$eid]);
						}
						
						$vip="No";
						if($charges_pat_data[$i]['vip']>0){
							$vip="Yes";
						}else{
							$write_off="";
						}
						if($vip_pat>0){
						}else{
							$vip="";
						}
						//--- SUB TOTAL AMOUNT ARRAY -----
						$grand_total_arr['totalAmt'][] = $totalAmt;
						$grand_total_arr['write_off'][] = $write_off;
						
						//--- TOTAL AMOUNT ARRAY -----
						$total_amount_arr['totalAmt'][] = $totalAmt;
						$total_amount_arr['write_off'][] = $write_off;
						
						$fac_total_proc_charges['totalAmt'][] = $totalAmt;
						$fac_total_proc_charges['write_off'][] = $write_off;
						
						//---- PATIENT NAME ----
						$patient_name = core_name_format($charges_pat_data[$i]['pat_lname'], $charges_pat_data[$i]['pat_fname'], $charges_pat_data[$i]['pat_mname']);
						
						$patient_name .= ' - '.$charges_pat_data[$i]['patient_id'];
						$date_of_service=$charges_pat_data[$i]['date_of_service'];
						$totalAmt=$CLSReports->numberFormat($totalAmt,2);
						$write_off=$CLSReports->numberFormat($write_off,2);
						$csv_data .= <<<DATA
							<tr bgcolor="#FFFFFF">
								<td class="text_10" valign="top">$patient_name</td>
								<td class="text_10" style="text-align:center;" valign="top">$date_of_service</td>
								<td class="text_10" style="text-align:center;" valign="top">$eid</td>
								<td class="text_10" style="text-align:right;" valign="top">$totalAmt</td>
								<td class="text_10" style="text-align:right;" valign="top">$write_off</td>
								<td class="text_10" style="text-align:left;" valign="top">$strEncNotes</td>
								<td class="text_10" style="text-align:left; padding-left:30px;" valign="top">$vip</td>
							</tr>
DATA;
					$pdf_data .= <<<DATA
							<tr bgcolor="#FFFFFF">
								<td class="text_10" valign="top" width="170">$patient_name</td>
								<td class="text_10" style="text-align:center;" valign="top" width="60">$date_of_service</td>
								<td class="text_10" style="text-align:center;" valign="top" width="60">$eid</td>
								<td class="text_10" style="text-align:right;" valign="top" width="80">$totalAmt</td>
								<td class="text_10" style="text-align:right;" valign="top" width="130">$write_off</td>
								<td class="text_10" style="text-align:left;" valign="top" width="120">$strEncNotes</td>
								<td class="text_10" style="text-align:center;" valign="top" width="100">$vip</td>
							</tr>
DATA;
					}
				}
				
				$fac_tot_amt="";
				$fac_wrt_amt="";
				$fac_tot_amt = array_sum($fac_total_proc_charges['totalAmt']);
				$fac_wrt_amt = array_sum($fac_total_proc_charges['write_off']);
				
				$fac_tot_amt_final = $CLSReports->numberFormat($fac_tot_amt,2);
				$fac_wrt_amt_final = $CLSReports->numberFormat($fac_wrt_amt,2);
				
				
				$csv_data .= <<<DATA
				<tr>
					<td class="total-row" colspan="7"></td>
				</tr>
				<tr bgcolor="#FFFFFF">
					<td></td>
					<td class="text_10b" style="text-align:right;" colspan="2">Sub Total : </td>
					<td class="text_10b" style="text-align:right;">$fac_tot_amt_final</td>
					<td class="text_10b" style="text-align:right;">$fac_wrt_amt_final</td>
					<td class="text_10b" style="text-align:left;"></td>
					<td class="text_10b" style="text-align:right;"></td>
				</tr>
				<tr>
					<td class="total-row" colspan="7"></td>
				</tr>
DATA;
			$pdf_data .= <<<DATA
				<tr>
					<td class="total-row" colspan="7"></td>
				</tr>
				<tr bgcolor="#FFFFFF">
					<td></td>
					<td class="text_10b" style="text-align:right;" colspan="2">Sub Total : </td>
					<td class="text_10b" style="text-align:right;">$fac_tot_amt_final</td>
					<td class="text_10b" style="text-align:right;">$fac_wrt_amt_final</td>
					<td class="text_10b" style="text-align:right;"></td>
					<td class="text_10b" style="text-align:right;"></td>
				</tr>
				<tr>
					<td class="total-row" colspan="7"></td>
				</tr>
DATA;
			}
			
				$prov_tot_amt="";
				$prov_wrt_amt="";
				$prov_tot_amt = array_sum($total_amount_arr['totalAmt']);
				$prov_wrt_amt = array_sum($total_amount_arr['write_off']);
				
				$prov_tot_amt_final = $CLSReports->numberFormat($prov_tot_amt,2);
				$prov_wrt_amt_final = $CLSReports->numberFormat($prov_wrt_amt,2);
				
				
				$csv_data .= <<<DATA
				<tr>
					<td class="total-row" colspan="7"></td>
				</tr>
				<tr bgcolor="#FFFFFF">
					<td></td>
					<td class="text_10b" style="text-align:right;" colspan="2">Total : </td>
					<td class="text_10b" style="text-align:right;">$prov_tot_amt_final</td>
					<td class="text_10b" style="text-align:right;">$prov_wrt_amt_final</td>
					<td class="text_10b" style="text-align:right;"></td>
					<td class="text_10b" style="text-align:right;"></td>
				</tr>
				<tr>
					<td class="total-row" colspan="7"></td>
				</tr>
DATA;
		$pdf_data .= <<<DATA
				<tr>
					<td class="total-row" colspan="7"></td>
				</tr>
				<tr bgcolor="#FFFFFF">
					<td></td>
					<td class="text_10b" style="text-align:right;" colspan="2">Total : </td>
					<td class="text_10b" style="text-align:right;">$prov_tot_amt_final</td>
					<td class="text_10b" style="text-align:right;">$prov_wrt_amt_final</td>
					<td class="text_10b" style="text-align:right;"></td>
					<td class="text_10b" style="text-align:right;"></td>
				</tr>
				<tr>
					<td class="total-row" colspan="7"></td>
				</tr>
DATA;
		}
		
		$show_vip_heading="VIP";
		if($vip_pat<1){
			$show_vip_heading="";
		}
		if($date_chk>0){
			$dos_srh="All";
		}else{
			$dos_srh=$Start_date ." To ". $End_date;
		}
		//--- GET TOTAL AMOUNT ------
		$grand_proc_charges = array_sum($grand_total_arr['totalAmt']);
		$grand_write_off = array_sum($grand_total_arr['write_off']);
		
		//--- NUMBER FORMAT -------
		$grand_proc_charges = $CLSReports->numberFormat($grand_proc_charges,2);
		$grand_write_off = $CLSReports->numberFormat($grand_write_off,2);
		
		//--- OPERATOR INITIAL ----
		$pro_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
		$pro_name = $pro_name_arr[1][0];
		$pro_name .= $pro_name_arr[0][0];
		$pro_name = strtoupper($pro_name);
		
		$curDate = date(phpDateFormat().' h:i A');
		//---  HTML PAGE HEADER ---
		$page_content = <<<DATA
			<page backtop="19mm" backbottom="5mm">
			<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
			<page_header>
			<table class="rpt_table rpt rpt_table-bordered rpt_padding">
				<tr>	
					<td align="left" class="rptbx1" style="width:215px;">Deferred/VIP Report</td>
					<td align="left" class="rptbx2" style="width:230px;" colspan="2">Selected Group : $group_name</td>
					<td align="left" class="rptbx3" style="width:230px;" colspan="2">Created by $op_name on $curDate </td>
				</tr>
				<tr>
					<td align="left" class="rptbx1" style="width:215px;">Selected Physician : $physician_name</td>
					<td align="left" class="rptbx2" style="width:230px;" colspan="2">Selected Facility : $practice_name</td>
					<td align="left" class="rptbx3" style="" colspan="2">Selected DOS : $dos_srh</td>
				</tr>
				<tr>
					<td align="left" class="rptbx1" style="">Patient Status : $patient_status_name</td>	
					<td align="left" class="rptbx2" style="">Account Status : $account_status_name</td>
					<td align="left" class="rptbx2" style="">VIP Patients : $sel_vip_status</td>
					<td align="left" class="rptbx3" style="">Deferred Insurance  : $sel_def_ins</td>
					<td align="left" class="rptbx3" style="">Deferred Patient : $sel_def_pat</td>
				</tr>
			</table>
			<table class="rpt_table rpt rpt_table-bordered rpt_padding">
				<tr>
					<td class="text_b_w" style="text-align:left;" width="170">Patient Name</td>
					<td class="text_b_w" style="text-align:left;" width="60">DOS</td>
					<td class="text_b_w" style="text-align:left;" width="60">Encounter</td>
					<td class="text_b_w" style="text-align:center;" width="80">Total Charges</td>
					<td class="text_b_w" style="text-align:center;" width="130">VIP-Adjusted Charges</td>
					<td class="text_b_w" style="text-align:center;" width="150">Notes</td>
					<td class="text_b_w" style="text-align:center;" width="30">$show_vip_heading</td>
				</tr>
				<tr>
					<td height="1px" bgcolor="#FFFFFF" colspan="7"></td>
				</tr>
			</table>
			</page_header>
			<table class="rpt_table rpt rpt_table-bordered rpt_padding">
				$pdf_data
				<tr>
					<td class="total-row" colspan="7"></td>
				</tr>
				<tr bgcolor="#FFFFFF">
					<td></td>
					<td class="text_10b" style="text-align:right;" colspan="2">Grand Total : </td>
					<td class="text_10b" style="text-align:right;">$grand_proc_charges</td>
					<td class="text_10b" style="text-align:right;">$grand_write_off</td>
					<td class="text_10b" style="text-align:right;"></td>
					<td class="text_10b" style="text-align:right;"></td>
				</tr>
				<tr>
					<td class="total-row" colspan="7"></td>
				</tr>
			</table>
			</page>
DATA;

		// for CSV data
		$csv_content = <<<DATA
			<table class="rpt_table rpt rpt_table-bordered rpt_padding">
				<tr>	
					<td align="left" class="rptbx1" style="width:225px;">Deferred/VIP Report</td>
					<td align="left" class="rptbx2" style="width:255px;">Selected Group : $group_name</td>
					<td align="left" class="rptbx2" style="width:215px;" colspan="2">Selected Physician : $physician_name
					&nbsp;&nbsp;&nbsp;&nbsp;Cr. Phy.:$crediting_physician_name</td>
					<td align="left" class="rptbx3" style="width:215px;">Selected Facility : $practice_name</td>
					<td align="left" class="rptbx3" style="width:235px;">Created by $op_name on $curDate </td>
				</tr>
				<tr>
					<td align="left" class="rptbx1" style="">Patient Status : $patient_status_name</td>	
					<td align="left" class="rptbx2" style="">Account Status : $account_status_name</td>
					<td align="left" class="rptbx2" style="">Deferred Insurance  : $sel_def_ins</td>
					<td align="left" class="rptbx2" style="">Deferred Patient : $sel_def_pat</td>
					<td align="left" class="rptbx3" style="">VIP Patients : $sel_vip_status</td>
					<td align="left" class="rptbx3" style="">Selected DOS : $dos_srh</td>
				</tr>
			</table>
			<table class="rpt_table rpt rpt_table-bordered rpt_padding">
				<tr>
					<td class="text_b_w" style="text-align:center;" width="190">Patient Name</td>
					<td class="text_b_w" style="text-align:center;" width="60">DOS</td>
					<td class="text_b_w" style="text-align:center;" width="60">Encounter Id</td>
					<td class="text_b_w" style="text-align:center;" width="90">Total Charges</td>
					<td class="text_b_w" style="text-align:center;" width="90">VIP Adjusted Charges</td>
					<td class="text_b_w" style="text-align:center;" width="150">Notes</td>
					<td class="text_b_w" style="text-align:left; padding-left:30px;" width="80">$show_vip_heading</td>
				</tr>
				$csv_data
				<tr>
					<td class="total-row" colspan="7"></td>
				</tr>
				<tr bgcolor="#FFFFFF">
					<td></td>
					<td class="text_10b" style="text-align:right;" colspan="2">Grand Total : </td>
					<td class="text_10b" style="text-align:right;">$grand_proc_charges</td>
					<td class="text_10b" style="text-align:right;">$grand_write_off</td>
					<td class="text_10b" style="text-align:right;"></td>
					<td class="text_10b" style="text-align:right;"></td>
				</tr>
				<tr>
					<td class="total-row" colspan="7"></td>
				</tr>
			</table>
			</page>
DATA;
	}
	
	//--- CREATE HTML FILE FOR PDF ----
	if($printFile == true){
		$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';				
		$csv_file_data= $styleHTML.$csv_content;

		$stylePDF='<style>'.file_get_contents('css/reports_pdf.css').'</style>';	
		$strHTML = $stylePDF.$page_content;
		$file_location = write_html($strHTML);
	}
	else{
		$csv_file_data = '<div class="text-center alert alert-info">No Record Found.</div>';
	}
}

$op='p';
if($output_option=='view' || $output_option=='output_csv'){
	if($callFrom != 'scheduled'){
		echo $csv_file_data;	
	}
}

//--- GET PAYROLL REPORT RESULT TEMPLATE -----
/*if($callFrom == 'scheduled'){
	if($strHTML != ""){
		$page_html_script = $csv_content;
		$html_file_name = get_scheduled_pdf_name('ins_defer', '../common/new_html2pdf');
		file_put_contents('../common/new_html2pdf/'.$html_file_name.'.html',$strHTML);
	}
	
}*/
?>