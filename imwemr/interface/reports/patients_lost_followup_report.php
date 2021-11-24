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
set_time_limit(180);
$filename = data_path().'users/UserId_'.$_SESSION['authId'].'/lostToFollowUp.txt';
$fileInfo = pathinfo($filename);
if(!is_dir($fileInfo['dirname'])) mkdir($fileInfo['dirname'], 0777, true);

$strHTML ='';

if($_POST)
{ 
	$globalDateQry = get_sql_date_format();		
	
	$repType= $_POST["repType"]; // getting report output type
	$letterTempId=$_POST["letterTempId"];// getting sub report type in case of user selected recall letter in report type 
	
	if($repType=='address_labels')$blIncludePatientAddress = true;
	
	//getting report generator name
	$report_generator_name = NULL;
	if(isset($_SESSION["authProviderName"]) && $_SESSION["authProviderName"] != ""){
		$arr_report_generator_name = explode(" ", $_SESSION["authProviderName"]);
		$report_generator_name = substr($arr_report_generator_name[1], 0, 1).substr($arr_report_generator_name[0], 0, 1);
		$report_generator_name = strtoupper($report_generator_name);
	}
	
	$strFacIds = implode(",", $_POST['facility_name']);//getting selected facility ids
	if(substr($strFacIds,0,1)==',')$strFacIds=substr($strFacIds,1);//removing coma(,) from 1st nd last space
	if(substr($strFacIds,strlen($strFacIds)-1,1)==',')$strFacIds=substr($strFacIds,0,strlen($strFacIds)-1);
	$strFacIds=str_replace(',,',',',$strFacIds);
	
	$strProvIds = implode(",", $_POST['phyId']);//getting selected provider / physican ids
	if(substr($strProvIds,0,1)==',')$strProvIds=substr($strProvIds,1);//removing coma(,) from 1st nd last space
	if(substr($strProvIds,strlen($strProvIds)-1,1)==',')$strProvIds=substr($strProvIds,0,strlen($strProvIds)-1);
	$strProvIds=str_replace(',,',',',$strProvIds);
	
	$strGroupIds = implode(",", $_POST['groups']);//getting selected gourp ids
	if(substr($strGroupIds,0,1)==',')$strGroupIds=substr($strGroupIds,1);//removing coma(,) from 1st nd last space
	if(substr($strGroupIds,strlen($strGroupIds)-1,1)==',')$strGroupIds=substr($strGroupIds,0,strlen($strGroupIds)-1);
	$strGroupIds=str_replace(',,',',',$strGroupIds);
	
	$strInsCompIds='';
	$arrSelInsComps=array();
	if(sizeof($_POST["insuranceName"])>0){
		$arrSelInsComps=array_combine($_POST["insuranceName"],$_POST["insuranceName"]);
		$strInsCompIds = implode(",", $_POST["insuranceName"]);//getting selected insurance companies
		if(substr($strInsCompIds,0,1)==',')$strInsCompIds=substr($strInsCompIds,1);//removing coma(,) from 1st nd last space
		if(substr($strInsCompIds,strlen($strInsCompIds)-1,1)==',')$strInsCompIds=substr($strInsCompIds,0,strlen($strInsCompIds)-1);
		$strInsCompIds=str_replace(',,',',',$strInsCompIds);
	}
	
	$strCptIds=implode(',',$_POST["cpt_code_id"]);//getting selected CPT code ids
	if(substr($strCptIds,0,1)==',')$strCptIds=substr($strCptIds,1);//removing coma(,) from 1st nd last space
	if(substr($strCptIds,strlen($strCptIds)-1,1)==',')$strCptIds=substr($strCptIds,0,strlen($strCptIds)-1);
	$strCptIds=str_replace(',,',',',$strCptIds);
	
	$strDxIds=implode(',',$_POST["dx_code"]);//getting selected DX code ids
	if(substr($strDxIds,0,1)==',')$strDxIds=substr($strDxIds,1);//removing coma(,) from 1st nd last space
	if(substr($strDxIds,strlen($strDxIds)-1,1)==',')$strDxIds=substr($strDxIds,0,strlen($strDxIds)-1);
	$strDxIds=str_replace(',,',',',$strDxIds);

	$strDx10Ids=implode('\',\'',$_POST["all_dx10"]);//getting selected DX code ids
	if(substr($strDx10Ids,0,1)==',')$strDx10Ids=substr($strDx10Ids,1);//removing coma(,) from 1st nd last space
	if(substr($strDx10Ids,strlen($strDx10Ids)-1,1)==',')$strDx10Ids=substr($strDx10Ids,0,strlen($strDx10Ids)-1);
	$strDx10Ids=str_replace(',,',',','\''.$strDx10Ids.'\'');
	
	$strDx_10_Id=implode(',',$_POST["all_dx10"]);//getting selected DX code ids
	if(substr($strDx_10_Id,0,1)==',')$strDx_10_Id=substr($strDx_10_Id,1);//removing coma(,) from 1st nd last space
	if(substr($strDx_10_Id,strlen($strDx_10_Id)-1,1)==',')$strDx_10_Id=substr($strDx_10_Id,0,strlen($strDx_10_Id)-1);
	$strDx_10_Id=str_replace(',,',',',$strDx_10_Id);

	//changing date format
	if(isset($_POST['Start_date'])){
		$start_date = $_POST['Start_date'];
		$start_date_format=$start_date;
		if($phpDateFormat=='m-d-Y'){
			list($m,$d,$y) = preg_split('/-/', $start_date);
		}else{
			list($d,$m,$y) = preg_split('/-/', $start_date);
		}
		$start_date = $y.'-'.$m.'-'.$d;
	}
	if(isset($_POST['End_date'])){
		$end_date = $_POST['End_date'];
		$end_date_format=$end_date;
		if($phpDateFormat=='m-d-Y'){
			list($m,$d,$y) = preg_split('/-/', $end_date);
		}else{
			list($d,$m,$y) = preg_split('/-/', $end_date);
		}
		$end_date = $y.'-'.$m.'-'.$d;
	}
	
	if(isset($_POST['Future_date'])){
		$future_date = $_POST['Future_date'];
		$future_date_format=$future_date;
		if($phpDateFormat=='m-d-Y'){
			list($m,$d,$y) = preg_split('/-/', $future_date);
		}else{
			list($d,$m,$y) = preg_split('/-/', $future_date);
		}
		$future_date = $y.'-'.$m.'-'.$d;
	}
	
	$varCriteria=$strGroupIds.'~'.$strFacIds.'~'.$strProvIds.'~'.$strInsCompIds.'~'.str_replace("'",'',$strCptIds).'~'.str_replace("'",'',$strDxIds).'~'.$_POST['dayReport'].'~'.$start_date.','.$end_date.','.$future_date;
	
	//variables  to display report header information
	$group_name = $CLSReports->report_display_selected($strGroupIds,'group',1,$core_drop_groups_cont);
	$facility_name = $CLSReports->report_display_selected($strFacIds,'facility_tbl',1,$core_drop_facility_cont);// this is not working
	$physician_name = $CLSReports->report_display_selected($strProvIds,'physician',1,$phyOption_cont);
	$insurance_name = $CLSReports->report_display_selected($strInsCompIds,'insurance',1,$insuranceName_cont);
	$cpt_name = $CLSReports->report_display_selected(str_replace('none','',$strCptIds),'cpt_code',1,$cpt_for_code_cont);
	$dx10_name = $CLSReports->report_display_selected(str_replace('none','',$strDx_10_Id),'dx_code',1,$dx10_code_cont);

	// GET ALL FACILITIES	
	$arrFac = array();	$allFacNames = array(); $allFacCities = array(); $allFacPAM = array();
	$arrPosfacOfFac=array();
	$arrFacOfPosfac=array();
	$allFacNames[0]='No Facility';
	
	$qry = "select id,name,city, pam_code, fac_prac_code from facility order by name";
	$qryRes = get_array_records_query($qry);
	for($i=0;$i<count($qryRes);$i++){
		$id = $qryRes[$i]['id'];
		$allFacNames[$id] = $qryRes[$i]['name'];
		$pam = $qryRes[$i]['pam_code'];
		if($pam =='' ) { $pam = '01'; }
		$allFacPAM[$id] = $pam;
		$allFacCities[$id] = $qryRes[$i]['city'];
		$arrPosfacOfFac[$id]=$qryRes[$i]['fac_prac_code'];
		$arrFacOfPosfac[$qryRes[$i]['fac_prac_code']]=$id;
	}
	
	$strPosFacIds='';
	if(sizeof($_POST['facility_name'])>0){
		foreach($_POST['facility_name'] as $facId){
			$posFacId=$arrPosfacOfFac[$facId];
			$arrPosFac[$posFacId]=$posFacId;
		}
		$strPosFacIds=implode(',', $arrPosFac);
	}
	
	$apptChkDate = "";
	if($check_sch_date=='1'){
		$apptChkDate = $future_date;
	}else{
		$apptChkDate = $end_date;
	}
	
	//--------------------
	if($include_claims==1){

		//IF INSURANCE SELETED
		$joinPart=$wherePart=$fetchPart='';
		if($strInsCompIds!=''){
			$fetchPart=", insd.provider, insd.type";
			$joinPart=" JOIN insurance_data insd ON insd.pid=patchg.patient_id";
			$wherePart=" AND insd.actInsComp='1' AND 
			IF(insd.expiration_date!='0000-00-00 00:00:00', 
				(IF(DATE_FORMAT(insd.expiration_date, '%Y-%m-%d')>='".$end_date."', '1',0))
				,1)";
		}

		$strQry = "Select patChg.patient_id,patChg.primary_provider_id_for_reports as 'primaryProviderId',patChg.facility_id, 
		patChg.primaryInsuranceCoId, patChg.secondaryInsuranceCoId, patChg.tertiaryInsuranceCoId".$fetchPart." 
		FROM patient_charge_list patChg 
		LEFT JOIN patient_charge_list_details 
		ON patChg.charge_list_id =patient_charge_list_details.charge_list_id 
		LEFT JOIN patient_data ON patient_data.id = patChg.patient_id 
		".$joinPart."
		WHERE LOWER(patient_data.patientStatus)='active' AND patChg.date_of_service BETWEEN '".$start_date."' AND '".$end_date."' 
		AND patChg.del_status ='0' ".$wherePart;
		if($strPosFacIds!='')
		{
			$strQryPart.= " 
						AND patChg.facility_id IN($strPosFacIds)";
		}
		if($strProvIds !='')
		{
			$strQryPart.= " 
						AND patChg.primary_provider_id_for_reports IN($strProvIds)";	
		}
		if($strGroupIds !='')
		{
			$strQryPart.= " 
						AND patChg.gro_id IN($strGroupIds)";
		}
		if($strCptIds!='')
		{
			$strQryPart.= " 
						AND patient_charge_list_details.procCode IN($strCptIds)";
		}
		$andOR=' AND ';
		if($strDxIds!='') 
		{
			$strQryPart.= " 
			AND (patient_charge_list_details.diagnosis_id1 IN($strDxIds)
			OR patient_charge_list_details.diagnosis_id2 IN($strDxIds)
			OR patient_charge_list_details.diagnosis_id3 IN($strDxIds)
			OR patient_charge_list_details.diagnosis_id4 IN($strDxIds))";
			$andOR=' OR ';
		}
		if($strDx_10_Id!='') 
		{
			$strQryPart.= $andOR."  
			(patient_charge_list_details.diagnosis_id1 IN($strDx10Ids)
			OR patient_charge_list_details.diagnosis_id2 IN($strDx10Ids)
			OR patient_charge_list_details.diagnosis_id3 IN($strDx10Ids)
			OR patient_charge_list_details.diagnosis_id4 IN($strDx10Ids))";
		}
		if($strInsCompIds!=''){
			$strQryPart.=" AND (patChg.primaryInsuranceCoId IN($strInsCompIds)
			OR patChg.secondaryInsuranceCoId IN($strInsCompIds) 
			OR patChg.tertiaryInsuranceCoId IN($strInsCompIds))";
		}
		$strQry.=$strQryPart;
		$strQry.=" GROUP BY patChg.patient_id ";
		$strQry.=" ORDER BY patChg.date_of_service ASC";
		$sch_query_res_temp = get_array_records_query($strQry);
		
		for($i =0; $i < count($sch_query_res_temp); $i++)
		{
			$getData=1;

			//IF INSURANCE SELECTED THEN SATISFY BELOW CONDITIONS
			if($strInsCompIds!=''){
				$getData=0;
				if($arrSelInsComps[$sch_query_res_temp[$i]['primaryInsuranceCoId']] && $sch_query_res_temp[$i]['type']=='primary' && $sch_query_res_temp[$i]['primaryInsuranceCoId']==$sch_query_res_temp[$i]['provider']){
					$getData=1;
				}
				if($arrSelInsComps[$sch_query_res_temp[$i]['secondaryInsuranceCoId']] && $sch_query_res_temp[$i]['type']=='secondary' && $sch_query_res_temp[$i]['secondaryInsuranceCoId']==$sch_query_res_temp[$i]['provider']){
					$getData=1;
				}
				if($arrSelInsComps[$sch_query_res_temp[$i]['tertiaryInsuranceCoId']] && $sch_query_res_temp[$i]['type']=='tertiary' && $sch_query_res_temp[$i]['tertiaryInsuranceCoId']==$sch_query_res_temp[$i]['provider']){
					$getData=1;
				}
			}

			if($getData==1){
				$arrTempPatId[$sch_query_res_temp[$i]['patient_id']] = $sch_query_res_temp[$i]['patient_id'];
				$arrTempDocId[$sch_query_res_temp[$i]['patient_id']] = $sch_query_res_temp[$i]['primaryProviderId'];
				
				$facId='0';
				if($arrFacOfPosfac[$sch_query_res_temp[$i]['facility_id']]){ 
					$facId=$arrFacOfPosfac[$sch_query_res_temp[$i]['facility_id']];
				}
				
				$arrTempFacId[$sch_query_res_temp[$i]['patient_id']] = $facId;
				//getting that user last date of service 
				$arrPtLastDos[$sch_query_res_temp[$i]['patient_id']] = LastDOS($sch_query_res_temp[$i]['patient_id']);
				//get last appointed doctor and schedule id
				$getpatInfo = get_array_records_query("Select sa_doctor_id,id from schedule_appointments where sa_patient_id =".$sch_query_res_temp[$i]['patient_id']."  ORDER BY `id` Desc limit 0,1");
				
				$arrTempSchId[$getpatInfo[0]['primaryProviderId']][$sch_query_res_temp[$i]['patient_id']]=$getpatInfo[0]['id'];
				unset($getpatInfo);
			}
		}
	}else{
		
		//IF INSURANCE SELETED
		$joinPart=$wherePart='';
		if($strInsCompIds!=''){
			$joinPart=" JOIN insurance_data insd ON insd.ins_caseid=sa.case_type_id";
			$wherePart=" AND insd.provider IN(".$strInsCompIds.") AND insd.actInsComp='1' AND 
			IF(insd.expiration_date!='0000-00-00 00:00:00', 
				(IF(DATE_FORMAT(insd.expiration_date, '%Y-%m-%d')>='".$end_date."', '1',0))
				,1)";
		}	
		
		$qry = "Select sa.sa_patient_id FROM schedule_appointments sa
		".$joinPart."
		WHERE (sa.sa_app_start_date BETWEEN '".$start_date."' AND '".$end_date."') 
		".$wherePart."
		AND sa.sa_patient_app_status_id NOT IN(18,3,203)";
		$rs = imw_query($qry);
		while($res = imw_fetch_array($rs)){
			$arrTempPatId[$res['sa_patient_id']]=$res['sa_patient_id'];
		}
		imw_free_result($rs);
	}

	$splitted_ids = array_chunk($arrTempPatId,1500, true);
	foreach($splitted_ids as $ids){
		//make string from patient id array
		$tempPatIds = implode(",", $ids);
		// remove those ids which have future appointment
		$patQry = "Select sa_patient_id FROM schedule_appointments WHERE sa_patient_id IN(".$tempPatIds.") AND sa_app_start_date > '".$apptChkDate."'";
		$patRs = imw_query($patQry);
		while($patRes = imw_fetch_array($patRs)){
			unset($arrTempPatId[$patRes['sa_patient_id']]);
		}
		imw_free_result($patRs);
	}
	
	//indexing array
	array_values($arrTempPatId);
	$splitted_ids = array_chunk($arrTempPatId,1500, true);
	foreach($splitted_ids as $ids){
		//make string from patient id array
		$tempPatIds = implode(",", $ids);
		// remove those ids which have future recalls
		$recallQry= "Select patient_id FROM patient_app_recall WHERE patient_id IN(".$tempPatIds.") AND recalldate > '".$apptChkDate."' AND descriptions != 'MUR_PATCH'";
		$recallRs = imw_query($recallQry);
		$numRecalls = imw_num_rows($recallRs);
		while($recallRes= imw_fetch_array($recallRs)){
			unset($arrTempPatId[$recallRes['patient_id']]);
		}
		imw_free_result($recallRs);
	}
	
	//indexing array
	array_values($arrTempPatId);
	$splitted_ids = array_chunk($arrTempPatId,1500, true);
	foreach($splitted_ids as $ids)
	{
		//make string from patient id array
		$tempPatIds = implode(",", $ids);
		if($tempPatIds!='')
		{
			//GETTING LAST DOS OF PATIENTS
			if($include_claims!=1){
				$qry = "Select sa_patient_id, DATE_FORMAT(sa_app_start_date, '$globalDateQry') as 'sa_app_start_date', sa_patient_app_status_id as 'status'  
				FROM schedule_appointments WHERE sa_patient_id IN(".$tempPatIds.") ORDER BY id";
				$rs = imw_query($qry);
				while($res = imw_fetch_assoc($rs)){
					$dos=($res['status']==3 || $res['status']==18 || $res['status']==203) ? '<span class="text-strike">'.$res['sa_app_start_date'].'</span>': $res['sa_app_start_date'];
					$arrPtLastDos[$res['sa_patient_id']] = $dos;
				}
				imw_free_result($rs);			
			}

			//preparing final query
			$schQry = "Select 
			sa.id as sch_id, sa.sa_doctor_id, sa.sa_facility_id, sa.sa_patient_id, sa.sa_patient_name, 
			sa.sa_app_start_date, sa.sa_app_starttime, sa.procedureid,
			
			pd.id, pd.fname as pFname, pd.mname as 'pMname', pd.lname as 'pLname', pd.street, pd.street2, pd.city, 
			pd.state, pd.postal_code, pd.phone_home, DATE_FORMAT(DOB, '$globalDateQry') as 'dob',
			pd.hipaa_mail,pd.hipaa_email,pd.hipaa_voice, pd.providerID, pd.default_facility, 
			pd.temp_key,
			us.fname as dFname, us.lname as dLname 
			FROM schedule_appointments AS sa 
			JOIN patient_data pd ON pd.id = sa.sa_patient_id 
			JOIN users us ON us.id = sa.sa_doctor_id 
			WHERE sa.sa_patient_id IN(".$tempPatIds.")";// AND sa.sa_app_start_date = '".$start_date."'
			//SELECT id ,sa_doctor_id,sa_facility_id, sa_patient_id, sa_patient_name, sa_app_start_date, sa_app_starttime,procedureid from schedule_appointments WHERE sa_patient_id IN(".$tempPatIds.") GROUP BY sa_patient_id ORDER BY sa_app_start_date DESC
			//adding sarch string with main query
			//$schQry.= $strQryPart;
			$schQry.= "AND (sa.sa_app_start_date BETWEEN '".$start_date."' AND '".$end_date."')";
			$schQry.= " AND pd.patientStatus='Active' ORDER BY sa.sa_app_start_date, sa.id";
			$rs=imw_query($schQry)or die(imw_error());
			while($res=imw_fetch_assoc($rs)){
				$pid=$res['sa_patient_id'];
				$sch_query_res[$pid]=$res;
			}
			
			/*----------------------------------*/
			//$top_mar = 30+$cpt_top_mar+$dx_top_mar;
			$top_mar = 10;
			$data .= '
			<page backtop="'.$top_mar.'mm" backbottom="5mm">
			<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>
			<page_header>';
			
			$common_data.= '<table class="rpt rpt_table rpt_table-bordered" width="100%">
			<tr class="rpt_headers">
				<td class="rptbx1"  width="200">Reminder Lists Report</td>
				<td class="rptbx2 text-right"  width="560" >
					Created By: '.$report_generator_name.' on '.date("m/d/Y h:i A").'&nbsp;
				</td>
			</tr>
			</table>
			<table class="rpt rpt_table rpt_table-bordered" width="100%">
				<tr>
					<td width="47" class="text_b_w" >&nbsp;S.No.</td>	
					<td width="180" class="text_b_w">Patient Name - ID</td>
					<td width="120" class="text_b_w">DOB(Age)</td>
					<td width="150" class="text_b_w">Address</td>
					<td width="150" class="text_b_w">Phone</td>
					<td width="150" class="text_b_w">Reminder Choices</td>
				</tr>
			</table>';
				
			$disp_data.= $common_data;	
			$data.= $common_data;	
			$data.='</page_header>';
			
			// GETTING FOLLOW UP PROCEDURE FOR HOUSE CALLS AND PAM
			$qry = "Select id, proc from slot_procedures WHERE proc = 'Follow Up'";
			$procQryRes = get_array_records_query($qry);
			$procID = $procQryRes[0]['id'];
			$procName = $procQryRes[0]['proc'];
			//---------------------------------
			
			//running final query to get records
			
			$arrPatientData = array();
			$arrPatientIds = array();
			$arrFacility = array();
			$arrAddLabels = array();
			$arrPatientMaster = array();
			$arrDocTotal = array();
			$arrApptTotal = array();
			
			foreach($sch_query_res as $pid => $data_detail)
			{	
				$facility='';	$docNameArr= array();	$patNameArr= array();
				
				$patId = $data_detail['sa_patient_id'];

				$facility = $arrTempFacId[$data_detail['sa_patient_id']];
				
				$doc_id = $data_detail['sa_doctor_id'];
				
				//if($arrTempSchId[$doc_id][$patId]==$data_detail['sch_id'])
				//{
				
				$pat_dob = $data_detail['dob'];
				
				$app_time = getMainAmPmTime($data_detail['sa_app_starttime']);
				
				$arrFacility[$facility] =  $allFacNames[$facility];
		
				$docNameArr["LAST_NAME"] = $data_detail['dLname'];
				$docNameArr["FIRST_NAME"] = $data_detail['dFname'];
				$docName = changeNameFormat($docNameArr);
				
				//PATIENT INFO
				$patNameArr["LAST_NAME"] = $data_detail['pLname'];
				$patNameArr["FIRST_NAME"] = $data_detail['pFname'];
				$patNameArr["MIDDLE_NAME"] = $data_detail['pMname'];
				$patName = changeNameFormat($patNameArr);
				$patientName = $patName."-".$patId;
				$patAddress = $data_detail['street'].' '.$data_detail['street2'].'<br>'.$data_detail['city'].' '.$data_detail['state'].' '.$data_detail['postal_code'];
				$patAddr = $data_detail['street'].' '.$data_detail['street2'];
				
				$phone_default = $data_detail["phone_home"];
				$prefer_contact = $data_detail["preferr_contact"];
				
				if($prefer_contact == 0)
				{
					if(trim($data_detail["phone_home"]) != ""){$phone_default = $data_detail["phone_home"]; }
				}
				else if($prefer_contact == 1)
				{
					if(trim($data_detail["phone_biz"]) != ""){$phone_default = $data_detail["phone_biz"]; }				
				}
				else if($prefer_contact == 2)
				{
					if(trim($data_detail["phone_cell"]) != ""){$phone_default = $data_detail["phone_cell"]; }				
				}
				
				//FINAL ARRAY
				$arrPatientData[$facility][$doc_id][$patId]['SID'] = $data_detail['sch_id'];
				$arrPatientData[$facility][$doc_id][$patId]['DOCTORID'] = $doc_id;
				$arrPatientData[$facility][$doc_id][$patId]['DOCTORNAME'] = $docName;
				$arrPatientData[$facility][$doc_id][$patId]['PATID'] = $patId;
				$arrPatientData[$facility][$doc_id][$patId]['PATNAME'] = $patientName;
				$arrPatientData[$facility][$doc_id][$patId]['PATIENTNAME'] = $patName;
				$arrPatientData[$facility][$doc_id][$patId]['ADDRESS'] = $patAddress;
				$arrPatientData[$facility][$doc_id][$patId]['PAT_ADDRESS'] = $patAddr;
				$arrPatientData[$facility][$doc_id][$patId]['CITY'] = $data_detail['city'];
				$arrPatientData[$facility][$doc_id][$patId]['STATE'] =  $data_detail['state'];
				$arrPatientData[$facility][$doc_id][$patId]['POSTAL_CODE'] =  $data_detail['postal_code'];
				$arrPatientData[$facility][$doc_id][$patId]['DOB'] =  $pat_dob;
				$arrPatientData[$facility][$doc_id][$patId]['PHONE'] = core_phone_format($phone_default);
				
				//get this user last date of service from schedule appointment table
				
				
				// Total Appointments
				$arrDocTotal[$facility][$doc_id] = $arrDocTotal[$facility][$doc_id] + 1;
				$arrApptTotal[$facility] = $arrApptTotal[$facility] + 1;
				
				if($repType =='phoneTree')//phone tree
				{
					//STRING for PHONE TREE ---------------------------------------
					$strPhoneTree.=addDoubleQuotes($patName).',';
					$strPhoneTree.=addDoubleQuotes($data_detail['sa_app_start_date']).',';
					$strPhoneTree.=addDoubleQuotes($app_time).',';
					$strPhoneTree.=addDoubleQuotes($phone_default).',';
					$strPhoneTree.=addDoubleQuotes($data_detail['phone_biz']).',';
					$strPhoneTree.=addDoubleQuotes($data_detail['email']).',';
					$strPhoneTree.=addDoubleQuotes(str_replace("&nbsp;"," ",$docName)).',';
					$strPhoneTree.=addDoubleQuotes($allFacCities[$facility]).',';
					$strPhoneTree.=addDoubleQuotes(getProcedureName($data_detail['procedureid']));
					$strPhoneTree.="\n";
					// END OF STRING for PHONE TREE ---------------------------------------
				}
				elseif($repType =='address_labels')//address labels
				{
					// ARRAY FOR ADDRESS LABLES ---------------------------------
					$arrAddLabels[$patId]['NAME'] = $patName."-".$patId;
					$arrAddLabels[$patId]['ADDRESS'] = $patAddress;
					// END ARRAY FOR ADDRESS LABLES ---------------------------------
				
				}
				elseif($repType =='houseCalls')//televox or house calls
				{
					// STRING HOUSE CALLS --------------------------------------
					$strHouseData.= addDoubleQuotes($pat_name).',';
					$strHouseData.= addDoubleQuotes($phone_default).',';
					$strHouseData.= addDoubleQuotes($data_detail['phone_cell']).',';
					$strHouseData.= '""'.','.'""'.',';
					$strHouseData.= addDoubleQuotes($patId).',';
					$strHouseData.= addDoubleQuotes($doc_id).',';
					$strHouseData.= addDoubleQuotes($procID).',';
					$strHouseData.= addDoubleQuotes($docName).',';
					$strHouseData.= addDoubleQuotes($procName).',';
					$strHouseData.= addDoubleQuotes($allFacCities[$facility]).',';
					$strHouseData.= addDoubleQuotes($data_detail['street']).',';
					$strHouseData.= addDoubleQuotes($data_detail['city']).',';
					$strHouseData.= addDoubleQuotes($data_detail['state']).',';
					$strHouseData.= addDoubleQuotes($data_detail['postal_code']).',';
					$strHouseData.= addDoubleQuotes($data_detail['email']);
					$strHouseData.= "\n";
					// END HOUSE CALS STRING --------------------------------------
				}
				elseif($repType =='pam')//pam2000
				{
					// STRING PAM DATA---------------------------------------------- 
					$office_code=$allFacPAM[$facility];
			
					$arrPAM[$i]['PATID'] = addDoubleQuotes($patId);
					$arrPAM[$i]['MESSAGE_TYPE'] = addDoubleQuotes('01');
					$arrPAM[$i]['OFFICE'] = addDoubleQuotes($office_code);
					$arrPAM[$i]['LANGUAGE_TYPE'] = addDoubleQuotes('01');
					$arrPAM[$i]['PATIENT_FNAME'] = addDoubleQuotes($data_detail['pLname']);
					$arrPAM[$i]['PATIENT_LNAME'] = addDoubleQuotes($data_detail['pFname']);
					$arrPAM[$i]['APP_DATE'] = '""';
					$arrPAM[$i]['APP_TIME'] = '""';
					$arrPAM[$i]['STATUS_OPERATOR_ID'] = addDoubleQuotes($doc_id);
					$arrPAM[$i]['PROCEDURE_ID'] = addDoubleQuotes($procID);
					$arrPAM[$i]['PHONE'] = addDoubleQuotes(str_replace(' ','',str_replace('(','',str_replace(')','',str_replace('-','',$phone_default)))));
					$arrPAM[$i]['EMAIL'] = addDoubleQuotes($data_detail['email']);
					
					// END PAM DATA---------------------------------------------- 
				}
				
				$printFile = true;
				
				$lastDOS=$arrPtLastDos[$patId];
				$data .= '					
				<table class="rpt rpt_table rpt_table-bordered" width="100%">
					<tr><td width="47" class="text">&nbsp;'.($i+1).'</td>
						<td width="180" class="text" valign="top" >'.$lastDOS.'</td>
						<td width="120" class="text" valign="top" >'.$pat_name.' - '.$patId.'</td>
						<td width="150" class="text" valign="top" >'.$pat_dob.'</td>
						<td width="150" class="text" valign="top" >'.$phone_default.'</td>
					</tr>';
				$data .= '</table>';
				
				$disp_data .= '					
					<tr><td width="47" class="text_11 white" >'.($i+1).'</td>
						<td width="180" class="text_11 white" valign="top" >'.$lastDOS.'</td>
						<td width="120" class="text_11 white" valign="top" >'.$pat_name.' - '.$patId.'</td>
						<td width="150" class="text_11 white" valign="top" >'.$pat_dob.'</td>
						<td width="150" class="text_11 white" valign="top" >'.$phone_default.'</td>
					</tr>';	
				//}//end of if condition
			}	
			$data .= '</page>';
			$disp_data.='</table>';
		
			//}//end of checking do we showing record or printing letter
		
			if(count($arrPatientData) > 0)
			{
				$strCSS = '
				<style>
					.text_b_w{
						font-size:12px;
						font-weight:bold;
						color:#000000;
						background-color:#c7c7c7;
						border-style:solid;
						border-color:#FFFFFF;
						border-width: 1px; 
					}
					.tb_heading{
						font-size:12px;
						font-family:Arial, Helvetica, sans-serif;
						font-weight:bold;
						color:#000000;
						background-color:#BCD5E1;
					}
					.text_b{
						font-size:12px;
						font-family:Arial, Helvetica, sans-serif;
						font-weight:bold;
						color:#000;
						background-color:#BCD5E1;
					}
					.text{
						font-size:13px;
						font-family:Arial, Helvetica, sans-serif;
						background-color:#FFFFFF;
					}
					
				</style>';
				$strCSSPdf= '<style>'.file_get_contents('css/reports_pdf.css').'</style>';
				$strHTML.= $strCSSPdf . '
				<page backtop="18mm" backbottom="10mm">			
				<page_footer>
					<table style="width: 100%;">
						<tr>
							<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
						</tr>
					</table>
				</page_footer>
				<page_header>
				<table width="100%" cellpadding="1" cellspacing="1" border="0" bgcolor="#FFF3E8" class="rpt_padding">
					<tr class="rpt_headers">
						<td class="rptbx1" width="350">Lost to Follow Up</td>
						<td class="rptbx2" width="350">Period: '.$start_date_format.' - '.$end_date_format.'</td>
						<td class="rptbx3" width="350">Created by '.$report_generator_name.' on '.date("".$phpDateFormat." h:i A").' </td>
					</tr>
					
					<tr class="rpt_headers">
						<td class="rptbx1">Selected Group: '.$group_name.'</td>
						<td class="rptbx2">Selected Facility : '.$facility_name.'</td>
						<td class="rptbx3">Selected Provider : '.$physician_name.'</td>
					</tr>
					<tr class="rpt_headers">
						<td class="rptbx1">Selected Ins. Company : '.$insurance_name.'</td>
						<td class="rptbx2">Selected CPT Code : '.$cpt_name.'</td>
						<td class="rptbx3">&nbsp;ICD10: '.$dx10_name.'</td>
					</tr>
					
				</table>
				<table width="100%" cellpadding="1" cellspacing="1" border="0" bgcolor="#FFF3E8" class="rpt_padding">
					<tr>
						<td width="60" class="text_b_w" align="left">S.No.</td>	
						<td width="250" class="text_b_w" align="left">Last DOS</td>
						<td width="250" class="text_b_w" align="left">Patient Name - ID</td>
						<td width="250" class="text_b_w" align="left">DOB</td>
						<td width="250" class="text_b_w" align="left">Phone#</td>				
					</tr>
				</table>
				</page_header>';
				$page_data.= $strCSS . '<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%">
					<tr class="rpt_headers">
						<td class="rptbx1" width="33%">Lost to followup</td>
						<td class="rptbx2" width="33%">Period: '.$start_date_format.' - '.$end_date_format.'</td>
						<td class="rptbx3 text-right" width="auto">Created by '.$report_generator_name.' on '.date("".$phpDateFormat." h:i A").' &nbsp;</td>
					</tr>
					<tr class="rpt_headers">
						<td class="rptbx1">Selected Group: '.$group_name.'</td>
						<td class="rptbx2">Selected Facility : '.$facility_name.'</td>
						<td class="rptbx3 text-right">Selected Provider : '.$physician_name.'  &nbsp;</td>	
					</tr>
					<tr class="rpt_headers">
						<td class="rptbx1">Selected Ins. Company : '.$insurance_name.'</td>
						<td class="rptbx2">Selected CPT Code : '.$cpt_name.'</td>
						<td class="rptbx3 text-right">&nbsp;ICD10: '.$dx10_name.'</td>
					</tr>
				</table>';
		
				if($only_cell_phone=='1'){ //IF DISPLAY ONLY CELL NUMBERS
					$page_data.='
					<table class="rpt rpt_table rpt_table-bordered rpt_padding" width="100%">
					<tr>
						<td width="47" class="text_b_w" >&nbsp;S.No.</td>	
						<td width="250px" class="text_b_w text-center">Patient Name - ID</td>
						<td width="auto" class="text_b_w">Phone#</td>				
					</tr>';			
				
					foreach($arrFacility as $key => $value)
					{
						$page_data .= '<tr><td class="text_b_w" colspan="3" >Facility : '.$value.' - Total Records ('.$arrApptTotal[$key].')</td></tr>';
				
						$strHTML .= '<table width="100%" cellpadding="1" cellspacing="1" border="0" bgcolor="#FFF3E8" class="rpt_padding">
						<tr>
						<td colspan="3" class="text_b_w" width="1050">Facility : '.$value.' - Total Records ('.$arrApptTotal[$key].')</td>
					</tr>
					';
				
						$page_content='';	$oldDocId='';
						foreach($arrPatientData[$key] as $docKey => $patData)
						{
							$patData = array_values($patData);
							for($i=0; $i<count($patData); $i++) 
							{
								$patDetail = $patData[$i];
								if($oldDocId != $patDetail['DOCTORID'])
								{
									$page_data.='<tr>
										<td colspan="3" class="text_b_w nowrap">
										Provider : '.$patDetail['DOCTORNAME'].' - Total Records ('.$arrDocTotal[$key][$patDetail['DOCTORID']].')
										</td>
									</tr>';	
												
									$strHTML.='<tr>
										<td colspan="3" class="text_b_w" width="730">Provider : '.$patDetail['DOCTORNAME'].' - Total Records ('.$arrDocTotal[$key][$patDetail['DOCTORID']].')</td>
									</tr>';	
								}
								$oldDocId = $patDetail['DOCTORID'];
					
								//to print
								$strHTML .= 
								'<tr>
									<td width="60" class="text_b_w" style="background-color: #FFFFFF;">&nbsp;'.($i+1).'</td>
									<td width="250" class="text_b_w" style="background-color: #FFFFFF;">&nbsp;'.$patDetail['PATNAME'].'</td>
									<td width="250" class="text_b_w" style="background-color: #FFFFFF;">&nbsp;'.$patDetail['PHONE'].'&nbsp;</td>
								</tr>';
								//to display
								$page_data.='
								<tr>
									<td width="50" class="text " >&nbsp;'.($i+1).'</td>
									<td width="250px" class="text ">&nbsp;'.$patDetail['PATNAME'].'</td>
									<td width="auto" class="text ">&nbsp;'.$patDetail['PHONE'].'&nbsp;</td>
								</tr>';
							}
						}
						$strHTML .= '</table>';
						
					}
					
					$page_data .= '</table>';
			
			}else
			{
				$page_data.='
				<table class="rpt rpt_table rpt_table-bordered" width="100%">
				<tr>
					<td width="50px" class="text_b_w" >&nbsp;S.No.</td>	
					<td width="250px" class="text_b_w">Last DOS</td>
					<td width="250px" class="text_b_w">Patient Name - ID</td>
					<td width="250px" class="text_b_w">DOB</td>
					<td width="250px" class="text_b_w">Phone#</td>				
				</tr>';	
			
				foreach($arrFacility as $key => $value)
				{
					$page_data .= '<tr><td class="text_b_w" colspan="5">Facility : '.$value.' - Total Records ('.$arrApptTotal[$key].')</td></tr>';
			
					$strHTML .= '<table width="100%" cellpadding="1" cellspacing="1" border="0" bgcolor="#FFF3E8" class="rpt_padding">
					<tr>
					<td colspan="5" class="text_b_w" width="1050">Facility : '.$value.' - Total Records ('.$arrApptTotal[$key].')</td>
					</tr>';
			
					$page_content='';	$oldDocId='';
					foreach($arrPatientData[$key] as $docKey => $patData)
					{
						$patData = array_values($patData);
						for($i=0; $i<count($patData); $i++) 
						{
							$patDetail = $patData[$i];
							if($oldDocId != $patDetail['DOCTORID'])
							{
								$page_data.='<tr>
									<td colspan="5" class="text_b_w text-left nowrap">
									Provider : '.$patDetail['DOCTORNAME'].' - Total Records ('.$arrDocTotal[$key][$patDetail['DOCTORID']].')
									</td>
								</tr>';	
											
								$strHTML.='<tr>
									<td colspan="5" class="text_b_w" width="1050">Provider : '.$patDetail['DOCTORNAME'].' - Total Records ('.$arrDocTotal[$key][$patDetail['DOCTORID']].')</td>
								</tr>';	
							}
							$oldDocId = $patDetail['DOCTORID'];

							$lastDOS=$arrPtLastDos[$patDetail['PATID']];
							//to print
							$strHTML .= 
							'<tr>
								<td width="60" class="text" style="background-color: #FFFFFF;">&nbsp;'.($i+1).'</td>
								<td width="250" class="text" style="background-color: #FFFFFF;">&nbsp;'.$lastDOS.'</td>
								<td width="250" class="text" style="background-color: #FFFFFF;">&nbsp;'.$patDetail['PATNAME'].'</td>
								<td width="250" class="text" style="background-color: #FFFFFF;">'.$patDetail['DOB'].'</td>
								<td width="250" class="text" style="background-color: #FFFFFF;">&nbsp;'.$patDetail['PHONE'].'&nbsp;</td>
							</tr>';
							//to display
							$page_data.='
							<tr>
								<td width="47" class="text" >&nbsp;'.($i+1).'</td>
								<td width="260px" class="text">&nbsp;'.$lastDOS.'</td>
								<td width="260px" class="text">&nbsp;'.$patDetail['PATNAME'].'</td>
								<td width="250px" class="text">'.$patDetail['DOB'].'</td>
								<td width="auto" class="text">&nbsp;'.$patDetail['PHONE'].'&nbsp;</td>
							</tr>';
						}
					}
					$strHTML .= '</table>';
				}
				$page_data .= '</table>';
			}
	
			// ADD ADDRESS LABELS
			if($blIncludePatientAddress == true){
				$num = sizeof($arrAddLabels);
				$p =0;	$l = 0;

				$strHTML_Label_St.='<page backtop="10mm" backbottom="10mm">';
				
				$strHTML_Label_Footer= '<page_footer>
						<table style="width: 100%;">
							<tr>
								<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
							</tr>
						</table>
					</page_footer>';
					
				$strHTML_Label.='<page_header>';
				$strHTML_Label.='<table width="100%" border="0" cellpadding="0" cellspacing="0">				
						<tr>
							<td width="736" class="text_b_w" align="left">Address Labels:</td>
						</tr>						
					</table>
					</page_header>';
					
				$strHTML_Label.= "
				<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr>";
				
				foreach($arrAddLabels as $patId => $patAdd)
				{
					$strHTML_Label.= "
					<td valign=\"top\" width=\"275\"><br><br>
						<table align=\"left\"  height=\"100%\" border=\"1\" rules=\"rows\"  cellpadding=\"2\" cellspacing=\"0\" width=\"".$width."\">
							<tr>
								<td width=\"175\" align=\"left\" valign=\"middle\" class=\"text_13b\">".$patAdd['NAME']."</TD>
							</tr>
							<tr>
							<td width=\"175\" valign=\"middle\" align=\"left\" class=\"text_13\">";
					
							if($patDetail['ADDRESS'] <> ""){ 
								$strHTML_Label .= $patAdd['ADDRESS'];
							}
							
							$strHTML_Label.= "
							</td>
							</tr>
						</table>
					</td>";
					$l++;
					$p++;
					$break = '';
					if($p >= 3){
						$break = "</tr><tr>";
						$p=0;
					}
					if($l == $num){
						$break = "</tr>";
					}
					$strHTML_Label .= $break;
					
				}
			}
	
			//----------END LABELS-------
		 
			$strHTML.='</page>';
				
			if($blIncludePatientAddress == true){
				$strHTML_Label .= "</table></page>";
			}
				
			if($repType =='houseCalls')//televox or house calls
			{
				$strHouseCalls = "Patient Name,Patient Home Phone,Patient Mobile Number, Appointment Date,Appointment Time,Patient Account Number,Doctor Number,Procedure Number,Doctor Name,Procedure Name,Location (office) Name,Patient Address,Patient City,Patient State,Patient Zip Code,Patient Email Address";
				$strHouseCalls.="\n".$strHouseData;

				
				$fp=fopen($filename,"w");
				@fwrite($fp,$strHouseCalls);
				@fclose($fp);
			}
			elseif($repType=='pam')//pam 2000
			{
				$exceltext="";
				$exceltext ='Account-ID,Message Type,Office,Language Type,Patient Fname,Patient Lname,App Date,App Time,Provider,App Type,Phone,Email';
				//$exceltext ='DOS,Patient Lname,Patient Fname,Patient ID,DOB,Phone';
				$exceltext.="\n";
				for($k=0;$k<count($arrPAM);$k++)  
				{
					$exceltext.= implode(",",$arrPAM[$k]);
					$exceltext.="\n";
				}
				
				$fp=fopen($filename,"w");
				@fwrite($fp,$exceltext);
				@fclose($fp);
			}
			elseif($repType=='phoneTree')
			{
				$exceltext="";
				$exceltext="Patient Name,Appointment Date,Appointment Time,Home Phone,Mobile Phone,Email Address,Doctor Name,Location(office),Appointment Type \n".$strPhoneTree;
							
				$fp=fopen($filename,"w");
				@fwrite($fp,$exceltext);
				@fclose($fp);

			}
			elseif($repType=='letters')
			{
				//echo 'we are hrer 0-----------------------------------------------------';
				//if we letter format then give output in form of letters
				require_once('patients_lost_followup_letter.php');
			}
		}

			$page_data.= $strHTML_Label_st.$strHTML_Label;
			$showBtn=1;
			if($boolPdf == false) {
				$file_location = write_html($strHTML);
				$strHTML1 = $strHTML_Label_St.$strHTML_Label_Footer.$strHTML_Label;
				if($strHTML1)$showBtn=1;
				if($strHTML1!='' && $blIncludePatientAddress== true){
					$file_location = write_html($strHTML1);
				}
			}
		}//end of checking do we have records 
	}//end of splitted array loop
}

if($showBtn==1 && $page_data!=''){
	echo"<div id=\"csvFileDataTable\">$page_data</div>";
	$hasData = 1;
}else {
	 echo '<div class="text-center alert alert-info">No record exists.</div>';
}
?>
</body>
</html>