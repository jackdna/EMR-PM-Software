<?php 
$dateFormat= get_sql_date_format();
$curDate=date($phpDateFormat);
if(empty($Start_date) === true){
	$Start_date = $curDate;
	$End_date = $curDate;
}
if($_POST['form_submitted']){
	$start_date = getDateFormatDB($Start_date);
	$end_date = getDateFormatDB($End_date);

	//CHECK FOR PRIVILEGED FACILITIES
	if(sizeof($facility_name)<=0 && isPosFacGroupEnabled()){
		$facility_name = $CLSReports->getFacilityName('', '0', 'array');

		if(sizeof($facility_name)<=0){
			$facility_name[0]='NULL';
		}
	}
	
	//MAKE COMMA SEPEARATED OF ALL SEARCH CRITERIA
	if(sizeof($groups)>0){ $grp_id = implode(',', $groups); }
	if(sizeof($facility_name)>0){ $sc_name = implode(',', $facility_name); }
	if(sizeof($Physician)>0){ $Physician = implode(',', $Physician); }
	$credit_physician= (sizeof($crediting_provider)>0) ? implode(',',$crediting_provider) : '';

	$group_name = $CLSReports->report_display_selected($grp_id,'group',1,$allGrpCount);
	$physician_name = $CLSReports->report_display_selected($Physician,'physician',1,$allPhyCount);
	$selCrPhy = $CLSReports->report_display_selected($credit_physician,'physician',1, $allCrPhyCount);
	$practice_name = $CLSReports->report_display_selected($sc_name,'practice',1,$allFacCount);
	
	
	//MAKING OUTPUT DATA
	$file_name="institutional_encounters.csv";
	$csv_file_name= write_html("", $file_name);

	if(file_exists($csv_file_name)){
		unlink($csv_file_name);
	}
	$fp = fopen ($csv_file_name, 'a+');

	$arr=array();
	$arr[]="Institutional Encounters";
	$arr[]="From :" .$Start_date." To :" .$End_date;
	$arr[]="Created by" .$op_name." on" .$curDate;
	fputcsv($fp,$arr, ",","\"");
	
	$arr=array();
	$arr[]="Selected Group :" .$group_name;
	$arr[]="Selected Physician :" .$physician_name;
	$arr[]="Selected Crediting Physician :" .$selCrPhy;
	$arr[]="Selected Facility :" .$practice_name;
	fputcsv($fp,$arr, ",","\"");
	
	$arr=array();
	$arr[]="Patient Name-ID";
	$arr[]="Encounter";
	$arr[]="DOS";
	$arr[]="CPT Codes";
	$arr[]="Dx Codes";
	$arr[]="Modifiers";
	$arr[]="HL7 Create Date";
	$arr[]="HL7 Sent Date";
	fputcsv($fp,$arr, ",","\"");
	$fp = fopen ($csv_file_name, 'a+');
	
	// --- Get modifiers_tbl Data ----
	$qry = imw_query("select modifiers_id,mod_prac_code from modifiers_tbl WHERE delete_status = '0'");
	$modifier_arr = array();
	while($modifierQryRes=imw_fetch_array($qry)){	
		$modifiers_id = $modifierQryRes['modifiers_id'];
		$mod_prac_code = $modifierQryRes['mod_prac_code'];
		$modifier_arr[$modifiers_id] = $mod_prac_code;
	}
	
	function hl7sentData($encounter_id=0){
		if($encounter_id>0){
			$hl7qry = imw_query("SELECT patient_id, msg, 
			date_format(saved_on, '%m-%d-%Y') as saved_on, 
			date_format(sent_on, '%m-%d-%Y') as sent_on,
			acc_encounter_id 
			FROM hl7_sent 
			where sent='1' AND msg_type='Detailed Financial Transaction' AND acc_encounter_id='$encounter_id'");
			$hl7_arr = array();
			while($hl7qryRes=imw_fetch_assoc($hl7qry)){	
				$hl7_arr[] = $hl7qryRes;
			}
			return $hl7_arr;
		}
	}
	
	$qry = "select patient_charge_list.encounter_id,patient_charge_list.patient_id,
	date_format(patient_charge_list.date_of_service, '%m-%d-%Y') as date_of_service,
	patient_charge_list.primary_provider_id_for_reports as 'primaryProviderId',
	patient_charge_list.charge_list_id,
	patient_charge_list.facility_id,
	patient_charge_list.patient_id,
	patient_charge_list_details.charge_list_detail_id,
	patient_charge_list_details.diagnosis_id1,
	patient_charge_list_details.diagnosis_id2,
	patient_charge_list_details.diagnosis_id3,
	patient_charge_list_details.diagnosis_id4,
	patient_charge_list_details.modifier_id1,
	patient_charge_list_details.modifier_id2,
	patient_charge_list_details.modifier_id3,
	cpt_fee_tbl.cpt4_code,
	patient_data.lname as pat_lname,patient_data.fname as pat_fname,
	patient_data.mname as pat_mname,users.lname as providerLname,
	users.fname as providerFname,users.mname as providerMname
	from patient_charge_list 
	join patient_charge_list_details on patient_charge_list_details.charge_list_id = 
	patient_charge_list.charge_list_id 
	join patient_data on patient_data.id = patient_charge_list.patient_id
	join users on users.id = patient_charge_list.primary_provider_id_for_reports
	join pos_facilityies_tbl on pos_facilityies_tbl.pos_facility_id = 
	patient_charge_list.facility_id
	join cpt_fee_tbl on cpt_fee_tbl.cpt_fee_id = patient_charge_list_details.procCode
	where (patient_charge_list.date_of_service BETWEEN '$start_date' and '$end_date') AND patient_charge_list.billing_type='2' AND patient_charge_list_details.posted_status='1' ";
	if(empty($grp_id) == false){
		$qry.= " AND patient_charge_list.gro_id IN ($grp_id)";
	}
	if(empty($Physician) == false){
		$qry.= " AND patient_charge_list.primary_provider_id_for_reports IN ($Physician)";
	}
	if(empty($credit_physician) === false){
		$qry.= " and patient_charge_list.secondaryProviderId IN ($credit_physician)";
	}
	if($chksamebillingcredittingproviders==1){
		$qry.= " and patient_charge_list.primary_provider_id_for_reports!=patient_charge_list.secondaryProviderId";							
	}
	if(trim($sc_name) != ''){
		$qry.= " AND patient_charge_list.facility_id IN ($sc_name)";
	}
	
	$rs=imw_query($qry);
	while($res=imw_fetch_assoc($rs)){
		$encounter_id = $res['encounter_id'];
		$charge_list_id = $res['charge_list_id'];
		$charge_list_detail_id = $res['charge_list_detail_id'];
		$mainArr[$charge_list_id][$charge_list_detail_id] = $res;
	}	
	
	$page_content = '';
	foreach($mainArr as $chrg_list_id => $chrg_list_detail_id){
		$printFile=true;
		$diagnosisIdArr = array();
		$modifierIdArr = array();
		$cpt4_code_arr = array();
		foreach($chrg_list_detail_id as $detal => $data){
			$diagnosis_id1 =$data['diagnosis_id1'];
			$diagnosis_id2 =$data['diagnosis_id2'];
			$diagnosis_id3 =$data['diagnosis_id3'];
			$diagnosis_id4 =$data['diagnosis_id4'];
		
			$diagnosisIdArr[$diagnosis_id1] = $diagnosis_id1;
			$diagnosisIdArr[$diagnosis_id2] = $diagnosis_id2;
			$diagnosisIdArr[$diagnosis_id3] = $diagnosis_id3;
			$diagnosisIdArr[$diagnosis_id4] = $diagnosis_id4;
					
			$modifier_id1 = $data['modifier_id1'];
			$modifier_id2 = $data['modifier_id2'];
			$modifier_id3 = $data['modifier_id3'];
			$modifierIdArr[$modifier_id1] = $modifier_arr[$modifier_id1];
			$modifierIdArr[$modifier_id2] = $modifier_arr[$modifier_id2];
			$modifierIdArr[$modifier_id3] = $modifier_arr[$modifier_id3];
		
			$cpt4_code = $data['cpt4_code']; 
			$cpt4_code_arr[$cpt4_code] = $cpt4_code;
		}
		$hl7data = hl7sentData($data['encounter_id']);
		$cpt4_code_text = join(', ',array_unique($cpt4_code_arr));
		$modifierIdArr = array_unique($modifierIdArr);
		sort($modifierIdArr);
		if($modifierIdArr[0] == ''){
			array_shift($modifierIdArr);
		}
		$modifierId = join(', ',$modifierIdArr);
		$diagnosisIdArr = array_unique($diagnosisIdArr);
		sort($diagnosisIdArr);
		if($diagnosisIdArr[0] == ''){
			array_shift($diagnosisIdArr);
		}
		$diagnosisId = join(', ',$diagnosisIdArr);
		
		//--- PAGE HEADER DATA ---
		$curDate = date(''.$phpDateFormat.' H:i A');
		$op_name_arr = preg_split('/, /',strtoupper($_SESSION['authProviderName']));
		$op_name = $op_name_arr[1][0];
		$op_name .= $op_name_arr[0][0];
		
		$rowSpanStr = "";
		$rowSpan = ($hl7data && count($hl7data) > 0) ? count($hl7data) : '';
		if($rowSpan) $rowSpanStr = " rowspan = ".$rowSpan." ";


		$hl7FirstStr = "";
		// Hl7 First row
		if($hl7data && $hl7data[0]){
			$obj = $hl7data[0];
			$hl7FirstStr = '<td class="text alignLeft white" style="width:10%;">&nbsp;'. $obj['saved_on'].'</td>
			<td class="text alignLeft white" style="width:10%;">&nbsp;'.$obj['sent_on'].'</td>
			<td class="text alignLeft white" style="width:10%;" data-placement="left" data-html="true" data-toggle="tooltip" data-container="body" data-original-title="'.$obj['msg'].'">&nbsp;<span class="text_10b_purpule" style="cursor: pointer;"><b>HL7 Sent Msg</b></span></td>';
		}


		$page_content.='
			<style>.tooltip-inner{min-width: 100px;max-width: 800px; text-align:left}</style>
			<tr >
				<td '.$rowSpanStr.' class="text alignLeft white" valign="top" style="width:14%;">&nbsp;'.$data['pat_lname'].', '.$data['pat_fname'].' - '.$data['patient_id'].'</td>
				<td '.$rowSpanStr.' class="text alignLeft white" valign="top" style="width:10%;">&nbsp;'.$data['encounter_id'].'</td>
				<td '.$rowSpanStr.' class="text alignLeft white" valign="top" style="width:10%;">&nbsp;'.$data['date_of_service'].'</td>
				<td '.$rowSpanStr.' class="text alignLeft white" valign="top" style="width:14%;">&nbsp;'.$cpt4_code_text.'</td>
				<td '.$rowSpanStr.' class="text alignLeft white" valign="top" style="width:14%;">&nbsp;'.$diagnosisId.'</td>
				<td '.$rowSpanStr.' class="text alignLeft white" valign="top" style="width:10%;">&nbsp;'.$modifierId.'</td>
				'.$hl7FirstStr.'
			</tr>';
			
			if(!empty($rowSpan)){
				$startLoop = (empty($hl7FirstStr)) ? 0 : 1;
				for($i = $startLoop; $i<$rowSpan; $i++){
					$obj = ($hl7data[$i]) ? $hl7data[$i] : false;
					$page_content .= '<tr>
						<td class="text alignLeft white" style="width:8%;">&nbsp;'. $obj['saved_on'].'</td>
						<td class="text alignLeft white" style="width:8%;">&nbsp;'.$obj['sent_on'].'</td>
						<td class="text alignLeft white" style="width:10%;" data-placement="left" data-html="true" data-toggle="tooltip" data-container="body" data-original-title="'.$obj['msg'].'">&nbsp;<span class="text_10b_purpule" style="cursor: pointer;"><b>HL7 Sent Msg</b></span></td>
					</tr>';
				}
			}
			
			$arr=array();
			$arr[]= $data['pat_lname'].', '.$data['pat_fname'].' - '.$data['patient_id'];
			$arr[]= $data['encounter_id'];
			$arr[]= $data['date_of_service'];
			$arr[]= $cpt4_code_text;
			$arr[]= $diagnosisId;
			$arr[]= $modifierId;
			if(!empty($rowSpan)){
				$counter =0;
				foreach($hl7data as $obj){
					$counter++;
					if($counter == 1){
						$arr[]= $obj['saved_on'];
						$arr[]= $obj['sent_on'];
						fputcsv($fp,$arr, ",","\"");	
					}else{
						$arr=array();
						$arr[]= "";
						$arr[]= "";
						$arr[]= "";
						$arr[]= "";
						$arr[]= "";
						$arr[]= "";
						$arr[]= $obj['saved_on'];
						$arr[]= $obj['sent_on'];
						fputcsv($fp,$arr, ",","\"");
					}
				}
			}else{
				fputcsv($fp,$arr, ",","\"");
			}
	}
	$HTMLCreated=0;	
	if($printFile == true){
	$HTMLCreated = 1;
	echo $tblHeader = '<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="1050" >
			<tr class="rpt_headers">
				<td class="rptbx1" style="width:342px;">Institutional Encounters</td>
				<td class="rptbx2" style="width:342px;">Selected Date Range ('.$Start_date.' - '.$End_date.')</td>
				<td class="rptbx3" style="width:342px;">Created by: '.$op_name.' on '.$curDate.'</td>
			</tr>
			<tr>
					<td align="left" class="rptbx1">Selected Group : '.$group_name.'</td>
					<td align="left" class="rptbx2">Physician : '.$physician_name.'&nbsp;&nbsp;&nbsp;&nbsp;Cr. Phy.: '.$selCrPhy.'</td>
					<td align="left" class="rptbx3">Selected Facility : '.$practice_name.'</td>
				</tr>
		</table>';
	echo '<table class="rpt_table rpt_table-bordered" style="width:100%">
			<tr>
				<td class="text_b_w" width="10%" style="text-align:left">Patient Name-ID</td>
				<td class="text_b_w" width="10%" style="text-align:left">Encounter</td>
				<td class="text_b_w" width="10%" style="text-align:left">DOS</td>
				<td class="text_b_w" width="10%" style="text-align:left">CPT Codes</td>
				<td class="text_b_w" width="10%" style="text-align:left">Dx Codes</td>
				<td class="text_b_w" width="10%" style="text-align:left">Modifiers</td>
				<td class="text_b_w" width="10%" style="text-align:left">HL7 Create Date</td>
				<td class="text_b_w" width="10%" style="text-align:left">HL7 Sent Date</td>
				<td class="text_b_w" width="10%" style="text-align:left">HL7 Message</td>
			</tr>
			'.$page_content.'
			</table>';
	} else{
		echo '<div class="text-center alert alert-info">No record found.</div>';
	}
}
?> 