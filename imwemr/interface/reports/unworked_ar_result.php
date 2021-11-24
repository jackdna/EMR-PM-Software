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
FILE : unworked_ar_html.php
PURPOSE : Display results of Unworked A/R report
ACCESS TYPE : Direct
*/
$process = ucfirst($summary_detail);
$arrDateRange= $CLSCommonFunction->changeDateSelection();
	if($dayReport=='Daily'){
		$Start_date = $End_date = date($phpDateFormat);
	}else if($dayReport=='Weekly'){
		$Start_date = $arrDateRange['WEEK_DATE'];
		$End_date = date($phpDateFormat);
	}else if($dayReport=='Monthly'){
		$Start_date = $arrDateRange['MONTH_DATE'];
		$End_date = date($phpDateFormat);
	}else if($dayReport=='Quarterly'){
		$Start_date = $arrDateRange['QUARTER_DATE_START'];
		$End_date = $arrDateRange['QUARTER_DATE_END'];
	}

	//--- CHANGE DATE FORMAT ----
	$startDate = getDateFormatDB($Start_date);
	$endDate = getDateFormatDB($End_date);
$printFile = true;
$hasData = 0;
if( $_POST['form_submitted'] ){
	$printFile = false;

	//CHECK FOR PRIVILEGED FACILITIES
	if(sizeof($facility_name)<=0 && isPosFacGroupEnabled()){
		$facility_name = $CLSReports->getFacilityName('', '0', 'array');

		if(sizeof($facility_name)<=0){
			$facility_name[0]='NULL';
		}
	}
		
	$insuranceName = '';
	$grp_id = implode(',',$groups);
	$sc_name = implode(',',$facility_name);
	$Physician = implode(',',$phyId);
	if( is_array($ins_carriers) && count($ins_carriers) > 0){
		$insuranceName = implode(',',$ins_carriers);
	}
	
	// GET DEFAULT FACILITY
	$rs = imw_fetch_assoc(imw_query("select fac_prac_code from facility where facility_type  = '1' LIMIT 1"));
	$headPosFacility=$rs['fac_prac_code'];
	
	// GET ALL POS FACILITIES DETAILS
	$qry = "Select facilityPracCode, pos_facility_id from pos_facilityies_tbl";
	$rs=imw_query($qry);
	$posFacilityArr = array();
	$posFacilityArr[0] = 'No Facility';
	while($posQryRes = imw_fetch_array($rs)){
		$pos_facility_id = $posQryRes['pos_facility_id'];
		$posFacilityArr[$pos_facility_id] = $posQryRes['facilityPracCode'];
	}	
	$insurance_data_arr = array();
	$ins_query = imw_query("select id, name from insurance_companies order by name");
	while($data_ins = imw_fetch_array($ins_query))
	{
		$insurance_data_arr[$data_ins['id']] = $data_ins['name'];
	}
	
	//GET ALL USERS
	$providerNameArr[0] = 'No Provider';
	$rs=imw_query("Select id, fname, mname, lname FROM users");	
	while($res=imw_fetch_array($rs)){
		$id  = $res['id'];
		$pro_name_arr = array();
		$pro_name_arr["LAST_NAME"] = $res['lname'];
		$pro_name_arr["FIRST_NAME"] = $res['fname'];
		$pro_name_arr["MIDDLE_NAME"] = $res['mname'];
		$pro_name = changeNameFormat($pro_name_arr);
		$providerNameArr[$id] = $pro_name;
	}
 	
	//---------------------------------------START DATA --------------------------------------------
	$qry="Select encounter_id, DATEDIFF(NOW(),submited_date) as 'submitted_date_diff' FROM submited_record ORDER BY submited_id DESC";
	$rs=imw_query($qry);
	while($res=imw_fetch_array($rs)){
		$encFirstSubDiff[$res['encounter_id']] = $res['submitted_date_diff'];
	}unset($rs);
	$arrResultData=array();
	$qry = "Select patChg.patient_id, patChg.encounter_id, patChg.facility_id, patChg.primary_provider_id_for_reports as 'primaryProviderId', DATE_FORMAT(patChg.date_of_service,'".get_sql_date_format()."') as 'date_of_service',
	patChgDet.totalAmount, patChgDet.pri_due, patChgDet.sec_due, patChgDet.tri_due, patChgDet.pat_due,
	patChg.primaryInsuranceCoId, DATEDIFF(NOW(),patChg.date_of_service) as last_dos_diff, pd.fname, pd.mname, pd.lname,
	patChgDet.last_sec_paid_date, patChgDet.last_ter_paid_date, patChgDet.last_pat_paid_date, 
	DATEDIFF(NOW(),date_of_service) as last_pri_dop_diff,
	DATEDIFF(NOW(),from_sec_due_date) as last_sec_dop_diff,
	DATEDIFF(NOW(),from_ter_due_date) as last_ter_dop_diff, 
	DATEDIFF(NOW(),from_pat_due_date) as last_pat_dop_diff 
	FROM patient_charge_list patChg 
	JOIN patient_charge_list_details patChgDet ON patChgDet.charge_list_id = patChg.charge_list_id  
	JOIN patient_data pd ON pd.id = patChg.patient_id 
	LEFT JOIN pos_facilityies_tbl ON pos_facilityies_tbl.pos_facility_id = patChg.facility_id 
	LEFT JOIN users ON users.id = patChg.primary_provider_id_for_reports 
	WHERE patChgDet.del_status='0' AND (patChg.date_of_service BETWEEN '$startDate' AND '$endDate') 
	AND pd.lname!='doe' AND (pri_due + sec_due + tri_due)>0";
	if(empty($grp_id) === false){
		$qry .= " AND patChg.gro_id in($grp_id)";
	}
	if(empty($Physician) === false){
		$qry .= " AND patChg.primary_provider_id_for_reports in($Physician)";
	}
	if(empty($sc_name) === false){
		$qry .= " AND patChg.facility_id in($sc_name)";
	}
	if(trim($insuranceName) != ''){			
		$qry .= " AND (patChg.primaryInsuranceCoId in($insuranceName) 
			OR patChg.secondaryInsuranceCoId in($insuranceName) 
			OR patChg.tertiaryInsuranceCoId in($insuranceName))";
	}
	$qry.=" ORDER BY users.lname, pos_facilityies_tbl.facility_name, pd.lname, pd.fname";
	//echo $qry;
	$rs = imw_query($qry);
	while($res = imw_fetch_array($rs)){
		$paidAmt=0;
		$arrSmallestAging=array();
		$pid = $res['patient_id'];
		$eid = $res['encounter_id'];
		$phyId = $res['primaryProviderId'];
		$facId = $res['facility_id'];
		$patName = $pid.'~'.$res['fname'].'~'.$res['mname'].'~'.$res['lname'];

		$arrSmallestAging[$eid][] = $res['last_pri_dop_diff'];
		if($res['last_sec_dop_diff']!=''){ $arrSmallestAging[$eid][] = $res['last_sec_dop_diff']; }
		if($res['last_ter_dop_diff']!=''){ $arrSmallestAging[$eid][] = $res['last_ter_dop_diff']; }
		if($res['last_pat_dop_diff']!=''){ $arrSmallestAging[$eid][] = $res['last_pat_dop_diff']; }
		
		if($res['pri_due']>0 && $res['last_sec_paid_date']=='0000-00-00' && $res['last_ter_paid_date']=='0000-00-00' && $res['last_pat_paid_date']=='0000-00-00'){
			$arrSmallestAging=array();
			$arrSmallestAging[$eid][] = $res['last_pri_dop_diff'];
			if($encFirstSubDiff[$eid] > 0) { $arrSmallestAging[$eid][] = $encFirstSubDiff[$eid]; }
		}
		sort($arrSmallestAging[$eid]);
		$agingDays = $arrSmallestAging[$eid][0];
		
		//GET ONLY IF AGING HAS ABOVE 30 DAYS
		if($agingDays>=30){
			$printFile=true;
			$firstGroupBy = $phyId;
			$secGroupBy = $facId;
			if($grpby_block=='grpby_facility'){
				$firstGroupBy = $facId;
				$secGroupBy = $phyId;
			}
			
			$totalBalance = $res['pri_due'] + $res['sec_due'] + $res['tri_due'] + $res['pat_due']; 
			
			if($summary_detail=='summary'){
				$arrResultData[$firstGroupBy]['charges']+= $res['totalAmount'];
			}else{
				$arrResultData[$firstGroupBy][$eid]['pat_name'] = $patName;
				$arrResultData[$firstGroupBy][$eid]['dos'] = $res['date_of_service'];
				$arrResultData[$firstGroupBy][$eid]['charges']+= $res['totalAmount'];
				$arrResultData[$firstGroupBy][$eid]['balance']+= $totalBalance;
				$arrResultData[$firstGroupBy][$eid]['aging'] = $agingDays;
				$arrResultData[$firstGroupBy][$eid]['insid'] = $res['primaryInsuranceCoId'];
			}
		}
	} 
	unset($rs);
	

	if($printFile==true){
		$page_content='';

		require_once(dirname(__FILE__)."/unworked_ar_html.php");
		
		if(trim($page_content) != ''){				
			$globalDateFormat = phpDateFormat();
			
			//--- PAGE HEADER DATA ---
			$curDate = date(''.$phpDateFormat.' H:i A');
			$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
			$op_name = $op_name_arr[1][0];
			$op_name .= $op_name_arr[0][0];

			$arrAllInsSel = explode(',', $insuranceName);
			$arrAllInsSel = array_unique($arrAllInsSel);
			$strAllInsSel  =implode(',', $arrAllInsSel);
			
			/* $group_name = $CLSReports->report_display_selected($grp_id,'group',1, $allGrpCount);
			$facilitySelected = $CLSReports->report_display_selected($sc_name,'practice',1, $allFacOptions);
			$doctorSelected = $CLSReports->report_display_selected($Physician,'physician',1, $allPhyOptions);
			$dateModeSelected= strtoupper($reptByForFun);
			*/	
			$group_name = $CLSReports->report_display_selected($grp_id,'group',1,$allGrpCount);
			$facilitySelected = $CLSReports->report_display_selected($sc_name,'facility',1,$allFacOptions);
			$doctorSelected = $CLSReports->report_display_selected($Physician,'physician',1,$allPhyOptions);
			$insSelected = $CLSReports->report_display_selected($strAllInsSel,'insurance',1,$allInsCount);
			$reports_pdf_var='<style>'.file_get_contents('css/reports_pdf.css').'</style>';
			$html_page_content = <<<DATA
				$reports_pdf_var
				<page backtop="21mm" backbottom="10mm">			
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
							<td width="320" align="left" class=" rptbx1 nowrap">Unworked A/R Report ($process)</td>	
							<td width="400" align="left" class=" rptbx2">DOS ($Start_date To $End_date)</td>			
							<td width="320" align="left" class=" rptbx3">Created by : $op_name on $curDate</td>
						</tr>
						<tr>	
							<td align="left" class="rptbx1"p>Selected Group : $group_name</td>	
							<td align="left" class="rptbx2">Selected Insurance : $insSelected</td>			
							<td align="left" class="rptbx3"></td>
						</tr>
						<tr>	
							<td align="left" class="rptbx1">Selected Facility : $facilitySelected</td>	
							<td align="left" class="rptbx2">Selected Physician : $doctorSelected</td>			
							<td align="left" class="rptbx3" ></td>
						</tr>
					</table>
					$pdfHeader
				</page_header>
				$pdf_content
				</page>
DATA;
			$file_location = write_html($html_page_content);
			
			//--- CSV FILE DATA --
			$page_content = <<<DATA
				<table class="rpt_table rpt rpt_table-bordered rpt_padding">
					<tr>	
						<td width="25%" align="left" class=" rptbx1" nowrap style="width:25%">Unworked A/R Report ($process)</td>	
						<td width="25%" align="left" class=" rptbx2" style="width:25%">DOS ($Start_date To $End_date)</td>			
						<td width="25%" align="left" class=" rptbx3" style="width:25%">Created by : $op_name on $curDate</td>
					</tr>
					<tr>	
						<td align="left" class="rptbx1" nowrap>Selected Group : $group_name</td>	
						<td align="left" class="rptbx2">Selected Insurance : $insSelected</td>			
						<td align="left" class="rptbx3" ></td>
					</tr>
					<tr>	
						<td align="left" class="rptbx1" nowrap>Selected Facility : $facilitySelected</td>	
						<td align="left" class="rptbx2">Selected Physician : $doctorSelected</td>			
						<td align="left" class="rptbx3" ></td>
					</tr>
				</table>
				$page_content
DATA;
			$claim_status_request = constant('CLAIM_STATUS_REQUEST');
		}
	}
}

//$objManageData->Smarty->assign("printFile",$printFile);
$hasData = 1;
$op='l';
//--- SET CHECK IN/OUT REPORT TEMPLATE ---
if($callFrom!='scheduled'){
	if(empty($page_content)==false){
		if($output_option=='view' || $output_option=='output_csv'){
			echo $page_content;
		}
	}else{
		echo '<div class="text-center alert alert-info">No Record Exists.</div>';
	}
}
?>
<script>
	function claim_file_fun(enc_id,patient_id){
		var url = top.JS_WEB_ROOT_PATH+'/interface/accounting/claims_file.php?enc_id='+enc_id+'&patient_id='+patient_id;
		var wname = "Claims";
		var features = "width=900px,height=450px,resizable=0,scrollbars=0,status=no";
		
		var claim_status_request = "<?php echo $claim_status_request; ?>";
		if(claim_status_request == 'YES'){
			url = top.JS_WEB_ROOT_PATH+'/interface/accounting/claims_status.php?clm_status=true&enc_id='+enc_id+'&patient_id='+patient_id;
			wname = "ClaimsStatus";
		}
		top.popup_win(url,wname,features);
	}
</script>
