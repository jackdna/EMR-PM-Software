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

$arrFacilitySel=array();
$arrDoctorSel=array();
$arrInsSel=array();

$printFile = true;
if($_POST['form_submitted']){
	$printFile = false;
		
	//--- CHANGE DATE FORMAT ----
	$startDate = getDateFormatDB($Start_date);
	$endDate   = getDateFormatDB($End_date);
	
	// GET ALL POS FACILITIES DETAILS
	$qry = "Select facilityPracCode, pos_facility_id from pos_facilityies_tbl";
	$rs=imw_query($qry);
	$posFacilityArr = array();
	$posFacilityArr[0] = 'No Facility';
	while($posQryRes = imw_fetch_array($rs)){
		$pos_facility_id = $posQryRes['pos_facility_id'];
		$posFacilityArr[$pos_facility_id] = $posQryRes['facilityPracCode'];
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
	
	//--- GET GROUP NAME ---
	$group_name = "All Groups Selected";
	if(empty($grp_id) === false){
		$group_query = imw_query("select name from groups_new where gro_id = '$grp_id'");
		while($groupQryRes = imw_fetch_array($group_query)){
			$group_name = $groupQryRes['name'];
		}
	}

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

	//CHECK FOR PRIVILEGED FACILITIES
	if(sizeof($facility_name)<=0 && isPosFacGroupEnabled()){
		$facility_name = $CLSReports->getFacilityName('', '0', 'array');

		if(sizeof($facility_name)<=0){
			$facility_name[0]='NULL';
		}
	}

	$arrFacilitySel=$facility_name;
	$arrDoctorSel=$Physician;
	$arrInsSel=$ins_comp_id;
	if(sizeof($groups)>0){ $grp_id = implode(',', $groups); }
	if(sizeof($facility_name)>0){ $sc_name = implode(',', $facility_name); }
	if(sizeof($Physician)>0){ $Physician = implode(',', $Physician); }
	//---------------------------------------START DATA --------------------------------------------
	$qry="Select patChg.encounter_id FROM patient_charge_list patChg WHERE patChg.del_status='0' AND patChg.submitted='true' 
	AND patChg.primaryInsuranceCoId>0 AND patChg.totalBalance>0 AND (patChg.date_of_service BETWEEN '$startDate' AND '$endDate')";
	if(empty($grp_id) === false){
		$qry .= " AND patChg.gro_id in($grp_id)";
	}
	if(empty($Physician) === false){
		$qry .= " AND patChg.primary_provider_id_for_reports in($Physician)";
	}
	if(empty($sc_name) === false){
		$qry .= " AND patChg.facility_id in($sc_name)";
	}
	if(trim($ins_comp_id) != ''){			
		$qry .= " AND (patChg.primaryInsuranceCoId in($ins_comp_id) 
			OR patChg.secondaryInsuranceCoId in($ins_comp_id) 
			OR patChg.tertiaryInsuranceCoId in($ins_comp_id))";
	}
	$rs = imw_query($qry);
	while($res = imw_fetch_array($rs)){
		$tempEncIds[$res['encounter_id']] = $res['encounter_id'];
	}
	unset($rs);	
	
	//FROM SUBMITTED TABLE
	$arrSubmittedEncIds=array();
	if(sizeof($tempEncIds)>0){
		$splitted_encounters = array_chunk($tempEncIds,1500);

		foreach($splitted_encounters as $arr){
			$str_splitted_encs 	 = implode(',',$arr);
			$qry="Select encounter_id FROM submited_record WHERE encounter_id IN(".$str_splitted_encs.")";
			$rs = imw_query($qry);
			while($res = imw_fetch_array($rs)){
				$arrSubmittedEncIds[$res['encounter_id']]=$res['encounter_id'];
			}
		}
	}unset($tempEncIds);


	$arrResultData=array();
	$tempCaseTypeIds=array();
	$qry = "Select patChg.patient_id, patChg.encounter_id, patChg.facility_id, patChg.primary_provider_id_for_reports as 'primaryProviderId', DATE_FORMAT(patChg.date_of_service, '".get_sql_date_format()."') as 'date_of_service', patChg.primaryInsuranceCoId,
	patChg.totalAmt, patChg.case_type_id, pd.fname, pd.mname, pd.lname    
	FROM patient_charge_list patChg 
	JOIN patient_data pd ON pd.id = patChg.patient_id 
	LEFT JOIN pos_facilityies_tbl ON pos_facilityies_tbl.pos_facility_id = patChg.facility_id 
	LEFT JOIN users ON users.id = patChg.primary_provider_id_for_reports 
	WHERE patChg.del_status='0' AND (patChg.date_of_service BETWEEN '$startDate' AND '$endDate') 
	AND pd.lname !='doe' AND patChg.submitted='true'
	and patChg.primaryInsuranceCoId>0 and patChg.totalBalance>0 ";
	if(empty($grp_id) === false){
		$qry .= " AND patChg.gro_id in($grp_id)";
	}
	if(empty($Physician) === false){
		$qry .= " AND patChg.primary_provider_id_for_reports in($Physician)";
	}
	if(empty($sc_name) === false){
		$qry .= " AND patChg.facility_id in($sc_name)";
	}
	if(trim($ins_comp_id) != ''){			
		$qry .= " AND (patChg.primaryInsuranceCoId in($ins_comp_id) 
			OR patChg.secondaryInsuranceCoId in($ins_comp_id) 
			OR patChg.tertiaryInsuranceCoId in($ins_comp_id))";
	}
	$qry.=" ORDER BY users.lname, pos_facilityies_tbl.facility_name, pd.lname, pd.fname";
	$rs = imw_query($qry);
	while($res = imw_fetch_array($rs)){
		$eid = $res['encounter_id'];
		
		if(!$arrSubmittedEncIds[$eid]){
			$printFile=true;
			$paidAmt=0;
			$pid = $res['patient_id'];
			if($res['case_type_id']>0)$tempCaseTypeIds[$res['case_type_id']]=$res['case_type_id'];
			
			$phyId = $res['primaryProviderId'];
			$facId = $res['facility_id'];
			$patName = $pid.'~'.$res['fname'].'~'.$res['mname'].'~'.$res['lname'];
			
			$firstGroupBy = $phyId;
			$secGroupBy = $facId;
			if($groupBy=='facility'){
				$firstGroupBy = $facId;
				$secGroupBy = $phyId;
			}
			
			if($process=='Summary'){
				$arrResultData[$firstGroupBy][$secGroupBy]['charges']+= $res['totalAmt'];
			}else{
				$arrResultData[$firstGroupBy][$secGroupBy][$eid]['pat_name'] = $patName;
				$arrResultData[$firstGroupBy][$secGroupBy][$eid]['dos'] = $res['date_of_service'];
				$arrResultData[$firstGroupBy][$secGroupBy][$eid]['charges'] = $res['totalAmt'];
				$arrResultData[$firstGroupBy][$secGroupBy][$eid]['case_type_id'] = $res['case_type_id'];
				$arrResultData[$firstGroupBy][$secGroupBy][$eid]['primaryInsuranceCoId'] = $res['primaryInsuranceCoId'];
			}
		}
	} 
	unset($rs);
	unset($arrSubmittedEncIds);
	
	if(sizeof($tempCaseTypeIds)>0){
		$strCaseTypeIds=implode(',', $tempCaseTypeIds);
		$qry="Select ins_caseid, plan_name FROM insurance_data WHERE LOWER(type)='primary' AND ins_caseid IN(".$strCaseTypeIds.") AND plan_name!=''";
		$rs = imw_query($qry);
		while($res = imw_fetch_array($rs)){
			$arrEncPlanNames[$res['ins_caseid']] = $res['plan_name'];
		}
		unset($rs);			
	}unset($tempCaseTypeIds);

	if($printFile==true){
		$page_content='';

		require_once(dirname(__FILE__)."/unbilled_claims_html.php");
		
		if(trim($page_content) != ''){				
			
			//--- PAGE HEADER DATA ---
			$globalDateFormat = phpDateFormat();
			$curDate = date($globalDateFormat.' H:i A');
			$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
			$op_name = $op_name_arr[1][0];
			$op_name .= $op_name_arr[0][0];

			$facilitySelected='All';
			$doctorSelected='All';
			$insSelected='All';
			if(sizeof($arrFacilitySel)>0){
				$facilitySelected = (sizeof($arrFacilitySel)>1) ? 'Multi' : $posFacilityArr[$sc_name];  
			}
			if(sizeof($arrDoctorSel)>0){
				$doctorSelected = (sizeof($arrDoctorSel)>1) ? 'Multi' : $providerNameArr[$Physician];  
			}
			if(sizeof($arrInsSel)>0){
				$insSelected = (sizeof($arrInsSel)>1) ? 'Multi' : 'id';  
			}
			if($insSelected=='id'){
				if($arrInsSel[0]>0){
					$rs=imw_query("Select name FROM insurance_companies WHERE id = '".$arrInsSel[0]."'");
					$res=imw_fetch_array($rs);
					$insSelected = $res['name'];
				}
			}

			$stylePDF = '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
			$html_page_content = <<<DATA
				$stylePDF
				<page backtop="21mm" backbottom="13mm">			
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
							<td class="rptbx1" style="text-align:left; width:240px">Unbilled Claims Report ($process)</td>
							<td class="rptbx2" style="text-align:left; width:240px">DOS ($Start_date - $End_date)</td>
							<td class="rptbx3" style="text-align:left; width:240px;">Created by: $op_name on $curDate</td>
						</tr>
						<tr>
							<td class="rptbx1" style="text-align:left;">Selected Group: $group_name</td>
							<td class="rptbx2" style="text-align:left;">Selected Insurance: $insSelected</td>
							<td class="rptbx3" style="text-align:left;"></td>
						</tr>
						<tr>
							<td class="rptbx1" style="text-align:left;">Selected Facility : $facilitySelected</td>
							<td class="rptbx2" style="text-align:left;">Selected Physician : $doctorSelected</td>
							<td class="rptbx3" style="text-align:left;"></td>
						</tr>		
					</table>
					$pdfHeader
				</page_header>
				$pdf_content
				</page>
DATA;



			$file_location = write_html($html_page_content);

			//--- CREATE HTML FILE FOR PDF PRINTING ---
			if($callFrom != 'scheduled'){
		//	$html_file_name = get_pdf_name($_SESSION['authId'],'unbilled_claims');
			//file_put_contents("new_html2pdf/$html_file_name.html",$html_page_content);
			}
			
			$styleHTML='<style>'.file_get_contents('css/reports_html.css').'</style>';	
			$page_content = <<<DATA
				$styleHTML
				<table class="rpt_table rpt rpt_table-bordered rpt_padding">
					<tr>
						<td class="rptbx1" style="text-align:left; width:33%">Unbilled Claims Report Report ($process)</td>
						<td class="rptbx2" style="text-align:left; width:34%">DOS ($Start_date - $End_date)</td>
						<td class="rptbx3" style="text-align:left; width:33%">Created by: $op_name on $curDate</td>
					</tr>
					<tr>
						<td class="rptbx1" style="text-align:left; width:33%">Selected Group: $group_name</td>
						<td class="rptbx2" style="text-align:left; width:34%">Selected Insurance: $insSelected</td>
						<td class="rptbx3" style="text-align:left; width:33%"></td>
					</tr>
					<tr>
						<td class="rptbx1" style="text-align:left; width:33%">Selected Facility : $facilitySelected</td>
						<td class="rptbx2" style="text-align:left; width:34%">Selected Physician : $doctorSelected</td>
						<td class="rptbx3" style="text-align:left; width:33%"></td>
					</tr>		
				</table>
				$page_content
DATA;
			//$objManageData->Smarty->assign("html_file_name",$html_file_name);
			//$objManageData->Smarty->assign("showBtn",true);
			//$objManageData->Smarty->assign("csvFileData",$page_content);
			
		}
	}
}

$op='p';
if($callFrom != 'scheduled'){
	if($page_content){
		if($output_option=='view' || $output_option=='output_csv'){
			echo $page_content;
		}
	} else{
		echo '<div class="text-center alert alert-info">No Record Found.</div>';
	}
}
?>
