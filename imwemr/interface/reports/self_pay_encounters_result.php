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

$arrDoctorSel=array();
$arrInsSel=array();
//pre($_POST);die;
$printFile = true;
if(empty($form_submitted) == false || $submitted=='submit'){
	$printFile = false;
	$groupBy = 'Facility';
	
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
	
	
	$report_visible_name = 'Self Pay Encounters Report';
	$csv_file_name = 'self_pay_encounters_report'.date('YmdHis');
	if($from_report == 'tx_state_report'){
		$Start_date = $Start_date;
		$End_date	= $End_date;
		$report_visible_name = 'TX State Report';
		$csv_file_name = 'tx_state_report'.date('YmdHis');
	}
	$startDate = getDateFormatDB($Start_date);
	$endDate   = getDateFormatDB($End_date);

	//GET ALL USERS
	$providerNameArr[0] = 'No Provider';
	$rs=imw_query("Select id, fname, mname, lname FROM users");	
	while($res=imw_fetch_array($rs)){
		$id  = $res['id'];
		$pro_name_arr = array();
		$pro_name = core_name_format($res['lname'], $res['fname'], $res['mname']);	
		$providerNameArr[$id] = $pro_name;
	}

	//---GETTING POS FACILITY RESULTSET
	$posFacilityArr[0] = "No POS Facility Selected";
	$selected_thcic_id = '';
	$pos_qry = "SELECT pft.facility_name AS pos_fac_name, pft.pos_facility_id AS pos_fac_id, pft.thcic_id, pos_tbl.pos_prac_code
				FROM pos_facilityies_tbl pft 
				LEFT JOIN pos_tbl ON pos_tbl.pos_id = pft.pos_id ";
	if(empty($pos_facility) === false){
		$pos_qry .= " WHERE pft.pos_facility_id IN ($pos_facility) ";
	}
	$pos_qry .= " ORDER BY pft.facility_name";
	$pos_res = imw_query($pos_qry);
	if(imw_num_rows($pos_res)>0){
		while($pos_rs=imw_fetch_array($pos_res)){
			$posFacilityArr[$pos_rs['pos_fac_id']] = $pos_rs['pos_prac_code'].'-'.$pos_rs['pos_fac_name'].'-'.$pos_rs['thcic_id'];
			if($pos_rs['pos_fac_id'] == $pos_facility && $selected_thcic_id=='') $selected_thcic_id = $pos_rs['thcic_id'];
		}
	}
	
	//--- GET GROUP NAME ---
	$group_name = "All Groups Selected";
	$group_institution = 0;
	$THCICSubmitterId='';
	if(empty($grp_id) === false){
		$group_query = "select name,group_institution,THCICSubmitterId from groups_new where gro_id = '$grp_id'";
		$rs = imw_query($group_query);
		while($groupQryRes = imw_fetch_array($rs)){
			$group_name = $groupQryRes['name'];
			$group_institution = $groupQryRes['group_institution'];
			$THCICSubmitterId=$groupQryRes['THCICSubmitterId'];
		}
	}

	if(empty($Physician)===false){ $arrDoctorSel = explode(',', $Physician); }


	//---------------------------------------START DATA --------------------------------------------
	$arrResultData=array();
	$qry = "Select patChg.charge_list_id, patChg.patient_id, patChg.encounter_id, patChg.facility_id, patChg.primary_provider_id_for_reports as 'primaryProviderId', DATE_FORMAT(patChg.date_of_service, '".get_sql_date_format()."') as 'date_of_service', 
	patChg.totalAmt, pd.fname, pd.mname, pd.lname    
	FROM patient_charge_list patChg 
	JOIN patient_data pd ON pd.id = patChg.patient_id 
	LEFT JOIN pos_facilityies_tbl ON pos_facilityies_tbl.pos_facility_id = patChg.facility_id 
	LEFT JOIN users ON users.id = patChg.primary_provider_id_for_reports 
	WHERE patChg.del_status='0' 
	  AND (patChg.date_of_service BETWEEN '$startDate' AND '$endDate') 
	  AND pd.lname !='doe' ";
	  if($from_report != 'tx_state_report'){
	  	$qry .= "
	  	AND (patChg.primaryInsuranceCoId=0 AND patChg.secondaryInsuranceCoId=0 AND patChg.tertiaryInsuranceCoId=0) ";
	  }
	  $qry .= "
	  AND (patChg.totalBalance > '0' OR (patChg.postedAmount > 0 AND patChg.date_of_service >= '2013-01-01')) 
	  AND patChg.submitted = 'true' ";
	if(empty($grp_id) === false){
		$qry .= " AND patChg.gro_id in($grp_id)";
	}
	if(empty($pos_facility) === false){
		$qry .= " AND patChg.facility_id in($pos_facility)";
	}
	if(empty($Physician) === false){
		$qry .= " AND patChg.primary_provider_id_for_reports in($Physician)";
	}
	$qry.=" ORDER BY pos_facilityies_tbl.facility_name, users.lname, pd.lname, pd.fname";
	//echo ($qry);
	$rs = imw_query($qry);
	$arr_charge_list_ids = array();
	while($res = imw_fetch_array($rs)){
		$eid = $res['encounter_id'];
		$charge_list_id = $res['charge_list_id'];
		$printFile=true;
		$paidAmt=0;
		$pid = $res['patient_id'];
		
		$phyId = $res['primaryProviderId'];
		$facId = $res['facility_id'];
		$patName = $pid.'~'.$res['fname'].'~'.$res['mname'].'~'.$res['lname'];
		
		$firstGroupBy = $facId;
		$secGroupBy = $phyId;
		
		if($process=='Summary'){
			$arrResultData[$firstGroupBy][$secGroupBy]['charges']+= $res['totalAmt'];
		}else{
			$arrResultData[$firstGroupBy][$secGroupBy][$eid]['pat_name'] = $patName;
			$arrResultData[$firstGroupBy][$secGroupBy][$eid]['dos'] = $res['date_of_service'];
			$arrResultData[$firstGroupBy][$secGroupBy][$eid]['charges'] = $res['totalAmt'];
		}
		$arr_charge_list_ids[] = $charge_list_id;

	}
	
	include_once("self_pay_create_837.php");
	
	unset($rs);
	unset($arrSubmittedEncIds);

	if($printFile==true){
		$page_content='';
		$edi_837_data;
		require_once(dirname(__FILE__)."/self_pay_encounters_html.php");
		if(trim($page_content) != ''){				
			
			//--- PAGE HEADER DATA ---
			$globalDateFormat = phpDateFormat();
			$curDate = date($globalDateFormat.' H:i A');
			$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
			$op_name = $op_name_arr[1][0];
			$op_name .= $op_name_arr[0][0];

			//$facilitySelected='All';
			$doctorSelected='All';
			$insSelected='All';
			/*
			if(sizeof($arrFacilitySel)>0){
				$facilitySelected = (sizeof($arrFacilitySel)>1) ? 'Multi' : $posFacilityArr[$sc_name];  
			}
			*/
			if(sizeof($arrDoctorSel)>0){
				$doctorSelected = (sizeof($arrDoctorSel)>1) ? 'Multi' : $providerNameArr[$Physician];  
			}
			/*
			if(sizeof($arrInsSel)>0){
				$insSelected = (sizeof($arrInsSel)>1) ? 'Multi' : 'id';  
			}
			if($insSelected=='id'){
				if($arrInsSel[0]>0){
					$rs=imw_query("Select name FROM insurance_companies WHERE id = '".$arrInsSel[0]."'");
					$res=imw_fetch_array($rs);
					$insSelected = $res['name'];
				}
			}*/

			$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
			$html_page_content = <<<DATA
				$stylePDF
				<page backtop="19mm" backbottom="10mm">			
				<page_footer>
					<table style="width: 100%;">
						<tr>
							<td style="text-align:center;width:100%"> Page [[page_cu]]/[[page_nb]]</td>
						</tr>
					</table>
				</page_footer>
				<page_header>
				<table class="rpt_table rpt rpt_table-bordered rpt_padding"  width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr class="rpt_headers">
						<td class="rptbx1" style="width:33%">
							$report_visible_name
						</td>
						<td class="rptbx2" style="width:34%">
							 DOS ($Start_date - $End_date)
						</td>
						<td class="rptbx3" style="width:33%">
							Created by: $op_name on $curDate
						</td>
					</tr>
					<tr class="rpt_headers">
						<td class="rptbx1" style="width:33%">
							Selected Group: $group_name
						</td>
						<td class="rptbx2" style="width:34%">
							Selected Physician : $doctorSelected
						</td>
						<td class="rptbx3" style="width:33%">
							
						</td>
					</tr>		
				</table>
				$pdfHeader
				</page_header>
				$pdf_content
				</page>
DATA;
					
			//--- CSV FILE DATA --
			$page_content = <<<DATA
				<table class="rpt_table rpt rpt_table-bordered rpt_padding"  width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr class="rpt_headers">
						<td class="rptbx1" style="width:33%">
							$report_visible_name
						</td>
						<td class="rptbx2" style="width:34%">
							 DOS ($Start_date - $End_date)
						</td>
						<td class="rptbx3" style="width:33%">
							Created by: $op_name on $curDate
						</td>
					</tr>
					<tr class="rpt_headers">
						<td class="rptbx1" style="width:33%">
							Selected Group: $group_name
						</td>
						<td class="rptbx2" style="width:34%">
							Selected Physician : $doctorSelected
						</td>
						<td class="rptbx3" style="width:33%">
							
						</td>
					</tr>		
				</table>
				$page_content
DATA;
			/* $objManageData->Smarty->assign("html_file_name",$html_file_name);
			$objManageData->Smarty->assign("showBtn",true);
			$objManageData->Smarty->assign("csvFileData",$page_content);
			$objManageData->Smarty->assign("from_report_source",$from_report);
			$objManageData->Smarty->assign("csv_file_name",$csv_file_name); */
		}
	}
}

$HTMLCreated=0;
if(empty($THCICSubmitterId)){
	$csv_file_data = '<div class="text-center alert alert-info">THCIC ID not entered with Practice Group.</div>';
}else{
	if($printFile == true and $page_content != ''){
		$HTMLCreated=1;
		$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
		$csv_file_data= $styleHTML.$page_content;
	
		$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
		$strHTML = $stylePDF.$html_page_content;
	
		$file_location = write_html($strHTML);
	}else{
		$csv_file_data = '<div class="text-center alert alert-info">No Record Found.</div>';
	}
}
echo $csv_file_data;

?>
