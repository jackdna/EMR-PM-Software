<?php 
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
function getHqFacility_imw_sc()
{
	$sql = "SELECT id FROM facility WHERE facility_type = '1' LIMIT 0,1 ";
	$row = imw_query($sql) or die($sql.imw_error());
	$rez = @imw_fetch_array($row);
	if($rez != false)
	{
		return $rez["id"];
	}
	else
	{
		// Fix if No Hq. is selected
		$sql = "SELECT id FROM facility LIMIT 0,1 ";
		$row = imw_query($sql) or die($sql.imw_error());
		$rez = @imw_fetch_array($row);
		if($rez != false)
		{
			return $rez["id"];
		}
	}
}
function getEncounterId_imw_sc()
{
	$facilityId = getHqFacility_imw_sc();
	/*$sql = "SELECT encounterId FROM facility WHERE id='".$facilityId."' ";
	$row = imw_query($sql) or die($sql.imw_error());
	$rez = @imw_fetch_array($row);

	if($rez != false)
	{
		$encounterId = $rez["encounterId"];
	}

	//$sql = "UPDATE facility SET encounterId = '".($encounterId+1)."' WHERE id='".$facilityId."' ";
	//$row = imw_query($sql);
	*/

	$qry = "SELECT encounterId FROM facility WHERE id='".$facilityId."' ";
	$sql = imw_query($qry) or die($sql.imw_error());
	if($sql){ $res = imw_fetch_assoc($sql); $encounterId = $res["encounterId"]; }
	
	//get from policies
	$sql = "select Encounter_ID from copay_policies WHERE policies_id = '1' ";
	$sql = imw_query($qry) or die($sql.imw_error());
	if($sql){ $res = imw_fetch_assoc($sql); $encounterId_2 = $res["Encounter_ID"];}

	//bigg
	if($encounterId<$encounterId_2){
		$encounterId = $encounterId_2;
	}
		
	//--		
	$counter=0; //check only 100 times
	do{
		
		$flgbreak=1;
		//check in superbill
		if($flgbreak==1){
			$qry = "select idSuperBill FROM superbill WHERE encounterId='".$encounterId."' ";
			$sql = imw_query($qry) or die($qry.imw_error());
			if($sql && imw_num_rows($sql)>0){
				$flgbreak=0;
			}	
		}
		
		//check in chart_master_table--
		if($flgbreak==1){
			$qry = "select id FROM chart_master_table WHERE encounterId='".$encounterId."' ";
			$sql = imw_query($qry) or die($qry.imw_error());
			if($sql && imw_num_rows($sql)>0){
				$flgbreak=0;
			}
		}
		
		//check in Accounting
		if($flgbreak==1){
			$qry = "select charge_list_id FROM patient_charge_list WHERE encounter_id='".$encounterId."'";
			$sql = imw_query($qry) or die($qry.imw_error());
			if($sql && imw_num_rows($sql)>0){
				$flgbreak=0;
			}	
		}
		if($flgbreak==0) {$encounterId=$encounterId+1;}
		$counter++;
	}while($flgbreak==0 && $counter<100);
	if($counter>=100){ exit("Error: encounter Id counter needs to reset."); }
		//--
	return array($encounterId,$facilityId);
}
function getBillingGroup(){
	$grp_arr = array();
	$sqlQry = "SELECT * FROM facility_tbl WHERE fac_id = '".$_SESSION['facility']."' AND (fac_group_institution > '0' OR fac_group_anesthesia > '0' OR fac_group_practice > '0')";
	$sqlRes = imw_query($sqlQry);
	$rowsCount = imw_num_rows($sqlRes);
	if($rowsCount>0){
		$sqlRow = imw_fetch_assoc($sqlRes);
		$grp_arr=array("inst_grp"=>$sqlRow["fac_group_institution"],"anes_grp"=>$sqlRow["fac_group_anesthesia"],"prac_grp"=>$sqlRow["fac_group_practice"]);
	}
	return $grp_arr;
}
$billing_groups_new = getBillingGroup();
$billing_groups_arr	= $billing_groups_new;	
$imwApptId = 0;
$iASCConfirmationId = 0;
$imwApptIdQry = "SELECT st.appt_id, pc.patientConfirmationId FROM patientconfirmation pc 
				INNER JOIN stub_tbl st ON (st.patient_confirmation_id = pc.patientConfirmationId AND st.patient_confirmation_id !='0') 
				WHERE pc.ascId = '".$ascId."' AND pc.ascId != '0' LIMIT 0,1";
$imwApptIdRes = imw_query($imwApptIdQry) or die($imwApptIdQry.imw_error());				
if(imw_num_rows($imwApptIdRes)>0) {
	$imwApptIdRow = imw_fetch_array($imwApptIdRes);
	$imwApptId = $imwApptIdRow["appt_id"];
	$iASCConfirmationId = (int)$imwApptIdRow["patientConfirmationId"];
}
$ptDtlQry = "SELECT imwPatientId,patient_fname,patient_mname,patient_lname,date_of_birth FROM patient_data_tbl WHERE patient_id='".$patient_id."' LIMIT 0,1 ";
$ptDtlRes = imw_query($ptDtlQry) or die($ptDtlQry.imw_error());
if(imw_num_rows($ptDtlRes)>0) {
	$ptDtlRow 			= 	imw_fetch_array($ptDtlRes);
	$imwPatientId 	= 	$ptDtlRow["imwPatientId"];

	imw_close($link); //CLOSE SURGERYCENTER CONNECTION
	include_once('connect_imwemr.php'); // imwemr connection
	$iascPtDataQry="SELECT id FROM patient_data where id='".$imwPatientId."' LIMIT 0,1 ";
	$iascPtDataRes = imw_query($iascPtDataQry) or die($iascPtDataQry.imw_error());
	if(imw_num_rows($iascPtDataRes)>0) {
		
		//START GET ANES USER-ID FOR ANES BILLING
		$iascAnesUserIdTemp = "";
		$iascAnesUserAndQry = " AND fname='".addslashes($fname_Anes)."' AND mname='".addslashes($mname_Anes)."' AND lname='".addslashes($lname_Anes)."' ";
		if(constant("CHECK_USER_NPI")=="YES") {
			if($npi_Anes) {
				$iascAnesUserAndQry = " AND user_npi = '".$npi_Anes."'  AND user_npi != ''   AND user_npi != '0' ";		
			}
		}
		
		$iascAnesUserQry = "SELECT id FROM users WHERE delete_status='0' ".$iascAnesUserAndQry." LIMIT 0,1 ";		
		$iascAnesUserRes = imw_query($iascAnesUserQry) or die($iascAnesUserQry.imw_error());
		if(imw_num_rows($iascAnesUserRes)>0) {
			$iascAnesUserRow 	= imw_fetch_array($iascAnesUserRes);
			$iascAnesUserIdTemp	= $iascAnesUserRow["id"];	
		}
		//END GET ANES USER-ID FOR ANES BILLING
		
		$iascUserAndQry = " AND fname='".addslashes($fname_Surgeon)."' AND mname='".addslashes($mname_Surgeon)."' AND lname='".addslashes($lname_Surgeon)."' ";
		if(constant("CHECK_USER_NPI")=="YES") {
			if($npi_Surgeon) {
				$iascUserAndQry = " AND user_npi = '".$npi_Surgeon."'  AND user_npi != ''   AND user_npi != '0' ";		
			}
		}
		
		$iascUserQry = "SELECT id FROM users WHERE delete_status='0' ".$iascUserAndQry." LIMIT 0,1 ";		
		$iascUserRes = imw_query($iascUserQry) or die($iascUserQry.imw_error());
		if(imw_num_rows($iascUserRes)>0) {
			$iascUserRow 	= imw_fetch_array($iascUserRes);
			$iascUserIdTemp	= $iascUserRow["id"];	
		
			$insuranceCaseId= '0';
			$refferingPhysicianId = '0';
			$report_provider_id   = $iascUserRow["id"];		
		
			$iascRefPhyAndQry = " AND TRIM(FirstName)='".addslashes(trim($fname_Surgeon))."' AND TRIM(MiddleName)='".addslashes(trim($mname_Surgeon))."' AND TRIM(LastName)='".addslashes(trim($lname_Surgeon))."' ";	
			if(constant("CHECK_USER_NPI")=="YES") {
				if($npi_Surgeon) {
					$iascRefPhyAndQry = " AND NPI = '".$npi_Surgeon."'  AND NPI != ''   AND NPI != '0' ";		
				}
			}		
			
			$iascRefPhyQry = "SELECT physician_Reffer_id FROM refferphysician WHERE physician_Reffer_id != '0' ".$iascRefPhyAndQry." And delete_status <> '1' LIMIT 0,1 ";
			$iascRefPhyRes = imw_query($iascRefPhyQry) or die($iascRefPhyQry.imw_error());
			if(imw_num_rows($iascRefPhyRes)>0) {
				$iascRefPhyRow 	= imw_fetch_array($iascRefPhyRes);
				$refferingPhysicianId 	= $iascRefPhyRow["physician_Reffer_id"];
			}
			
			//=======================GET POS PRACTICE CODE===============================//
			$pos_prac_code="SC";
			$qry_sch_appt="SELECT pos_tbl.pos_prac_code FROM facility JOIN schedule_appointments  on
							schedule_appointments.sa_facility_id=facility.id
							JOIN pos_facilityies_tbl on pos_facilityies_tbl.pos_facility_id=facility.fac_prac_code
							JOIN pos_tbl on pos_tbl.pos_id=pos_facilityies_tbl.pos_id
							 WHERE schedule_appointments.id='".$imwApptId."' LIMIT 0,1";
			$res_sch_appt=imw_query($qry_sch_appt);
			if(imw_num_rows($res_sch_appt)>0){
				$row_sch_appt=imw_fetch_assoc($res_sch_appt);
				$pos_prac_code=addslashes($row_sch_appt['pos_prac_code']);
				
			}
			
			if(count($billing_groups_arr)>0 && is_array($billing_groups_arr)){
				$billing_groups_imp=implode(',',$billing_groups_arr);
				$grp_whr="gro_id in($billing_groups_imp) ";
			}else{
				$grp_whr="group_institution='1' LIMIT 0,1 ";
			}
			
			$groupsArr	=	array();
			$sel_proc_qry = "select gro_id,group_institution,group_anesthesia from groups_new where $grp_whr";
			$sel_proc_res=imw_query($sel_proc_qry) or die($sel_proc_qry.imw_error());
			while($sel_proc_row=imw_fetch_array($sel_proc_res)){
				
				$data = array();
				$data['gro_id']				= $sel_proc_row['gro_id'];
				$data['group_institution']	= $sel_proc_row['group_institution'];
				$data['group_anesthesia']	= $sel_proc_row['group_anesthesia'];
				
				array_push($groupsArr,$data);
			}
			
			if(is_array($groupsArr) && count($groupsArr) == 0)
			{
				$data = array();
				$data['gro_id']				= 0;
				$data['group_institution']	= 0;
				$data['group_anesthesia']	= 0;
				
				array_push($groupsArr,$data);			
			}
			$chkGrpArr = array();
			foreach($groupsArr as $sel_proc_row)
			{	
				$log = "";
				$gro_id=$sel_proc_row['gro_id'];
				$group_institution=$sel_proc_row['group_institution'];
				$group_anesthesia=$sel_proc_row['group_anesthesia'];
				
				$enc_idArr 			= getEncounterId_imw_sc();
				$enc_id				= $enc_idArr[0];
				$sbfacilityId		= $enc_idArr[1];
				$iascUserId			= $iascUserIdTemp;
				if($constantImwProviderId>0 && $billing_groups_arr["inst_grp"] == $gro_id) {
					$iascUserId = $constantImwProviderId;
				}else if($iascAnesUserIdTemp>0 && $billing_groups_arr["anes_grp"] == $gro_id) {
					$iascUserId = $iascAnesUserIdTemp;
				}
				$log .= "\n"."ASCID - ".$ascId;
				$log .= "\n"."GROUP ID - ".$gro_id;
				//============================================================================//
				$insUpdtSbQry = " INSERT INTO ";
				$insUpdtSbWhrQry = "";
				$ins_supper=$insUpdtSbQry." superbill set patientId='$imwPatientId',
							physicianId='$iascUserId',insuranceCaseId='$insuranceCaseId',
							encounterId = '$enc_id', timeSuperBill='$surgeryTime',dateOfService='$dos',
							patientStatus='Active',refferingPhysician='$refferingPhysicianId',financialStatus='Self',
							methodOfPayment='Cash',pos='".$pos_prac_code."',tos='2',ascId='$ascId',gro_id='$gro_id',primary_provider_id_for_reports='$report_provider_id', sch_app_id = '".$imwApptId."' ".$insUpdtSbWhrQry;
							
				$ins_supper_run=imw_query($ins_supper) or die($ins_supper.imw_error());
				//if(imw_num_rows($chkSupBillRes)<=0) {
					$sup_ins_id=imw_insert_id();	
					$log .= "\n"."SUPERBILL ID IS - ".$sup_ins_id;
					$sql = "UPDATE facility SET encounterId = '".($enc_id+1)."' WHERE id='".$sbfacilityId."' ";
					$row = imw_query($sql) or die($sql.imw_error());

					$sql = "UPDATE copay_policies SET Encounter_ID = '".($enc_id+1)."' WHERE policies_id='1' ";
					$row = imw_query($sql) or die($sql.imw_error());	
				//}
					//====>now data base is surgerycenter<=======
					imw_close($link_imwemr); //CLOSE IMWEMR CONNECTION
					include("common/conDb.php");  //SURGERYCENTER CONNECTION
					
					$anes_start_time = $anes_stop_time = "";
					if( $group_anesthesia ) {
						// start get anesthesia start/stop time from General anesthesia record
						$anesQry = "Select startTime, stopTime From genanesthesiarecord Where confirmation_id = ".$iASCConfirmationId." LIMIT 0,1";
						$anesSql = imw_query($anesQry) or die($anesQry.': '.imw_error());
						$anesCnt = imw_num_rows($anesSql);
						if( $anesCnt > 0 ){
							$anesRes = imw_fetch_assoc($anesSql);
							$anes_start_time = $objManageData->setTmFormat($anesRes['startTime']);	
							$anes_stop_time = $objManageData->setTmFormat($anesRes['stopTime']);
						}
						// End  get anesthesia start/stop time from General anesthesia record
						
						// start get anesthesia start/stop time from local anesthesia record if not found in general anesthesia
						if( !$anes_start_time && !$anes_stop_time )
						{
							$anesQry = "Select startTime, stopTime From localanesthesiarecord Where confirmation_id = ".$iASCConfirmationId." LIMIT 0,1";
							$anesSql = imw_query($anesQry) or die($anesQry.': '.imw_error());
							$anesCnt = imw_num_rows($anesSql);
							if( $anesCnt > 0 ){
								$anesRes = imw_fetch_assoc($anesSql);
								$anes_start_time = $objManageData->setTmFormat($anesRes['startTime']);	
								$anes_stop_time = $objManageData->setTmFormat($anesRes['stopTime']);
							}
						}
						// End get anesthesia start/stop time from local anesthesia record if not found in general anesthesia
					}
					//echo 'Start Time: '.$anes_start_time."<br>";
					//echo 'Stop Time: '.$anes_stop_time."<br>";

					/** Start Get Cpt/Dx/Modifiers Detail From Superbill */
					unset($condArr);
					$condArr['confirmation_id'] = $pConfId;
					$condArr['deleted'] = 0;
					$condArr['bill_user_type'] = 2;
					if($group_anesthesia > 0 || ($billing_groups_arr["anes_grp"] == $gro_id && $billing_groups_arr["anes_grp"] > 0))
					{
						$condArr ['bill_user_type'] = 1;	
					}else if($group_institution > 0 || ($billing_groups_arr["inst_grp"] == $gro_id && $billing_groups_arr["inst_grp"] > 0)) 
					{
						$condArr ['bill_user_type'] = 3;	
					}

					//START CODE - IF FACILITY BILL NOT EXIST THEN DO NOT MERGE FACILITY BILL IN SURGEON BILL (DO NOT CREATE SURGEON BILL TWICE)
					if($condArr['bill_user_type'] == 2 && in_array($condArr['bill_user_type'],$chkGrpArr)) 
					{ 
						//continue;
					}
					$chkGrpArr[] = $condArr['bill_user_type'];							
					//END CODE - IF FACILITY BILL NOT EXIST THEN DO NOT MERGE FACILITY BILL IN SURGEON BILL (DO NOT CREATE SURGEON BILL TWICE
					$procData	=	$objManageData->getMultiChkArrayRecords('superbill_tbl sb 
												INNER JOIN procedures pr ON(pr.procedureId = sb.cpt_id)
												INNER JOIN procedurescategory prc ON(prc.proceduresCategoryId = pr.catId)', $condArr,"prc.name = 'G-Codes' DESC, sb.cpt_code", 'Asc');
					
					$procDataArr	=	array(); $proc_code = '';
					$procDetailArr = array();
					
					if(is_array($procData) && count($procData) >  0)
					{
						foreach($procData as $key=>$procedure)
						{
							array_push($procDataArr,$procedure->cpt_id);
							//$procDetailArr['cptId'][$procedure->cpt_code]= $procedure->cpt_id ;
							$procDetailArr['cpt'][$procedure->cpt_id] 	 = $procedure->cpt_code ;
							$procDetailArr['dx'][$procedure->cpt_code] 	 = $procedure->dxcode_icd10 ;
							$procDetailArr['mod1'][$procedure->cpt_code] = $procedure->modifier1 ;
							$procDetailArr['mod2'][$procedure->cpt_code] = $procedure->modifier2 ;
							$procDetailArr['mod3'][$procedure->cpt_code] = $procedure->modifier3 ;
							$procDetailArr['unit'][$procedure->cpt_code] = $procedure->quantity ;
							$procDetailArr['isAnes'][$procedure->cpt_code] = $procedure->bill_user_type ;
						}
						$proc_code	=	implode(',',$procDataArr);
					}
					/** End Get Cpt/Dx/Modifiers Detail From Superbill */
					$proc_id_final=array();
					$proc_id_final_imp="";
					$proc_id_final_chk_imp="";
					$all_proc_id_final_imp="";
					$all_proc_id_final_chk_imp = "";
					$all_proc_id_final_array=array();
					if($proc_code) {
						$sql_qry_proc="SELECT procedureId, code,codeFacility,codePractice FROM procedures where procedureId in (".$proc_code.")";
						$sql_qry_res=imw_query($sql_qry_proc) or die($sql_qry_proc.imw_error());//$proc_code variable get from discharge_summary_sheet.php line no.157
						if(@imw_num_rows($sql_qry_res)>0){
							$proc_id_final=array();
							while($sqlRow_proc = imw_fetch_array($sql_qry_res)){
								if($procDetailArr['cpt'][$sqlRow_proc['procedureId']] <> $sqlRow_proc['code'])
								{
									$sqlRow_proc['code'] = $procDetailArr['cpt'][$sqlRow_proc['procedureId']];
								}
								
								$proc_id_final['default'][]="'".$sqlRow_proc['code']."'";
								
								if($group_institution>0 || ($billing_groups_arr["inst_grp"] == $gro_id && $billing_groups_arr["inst_grp"] > 0)){
									if($sqlRow_proc['codeFacility']!=""){
										$proc_id_final['code_inst'][]="'".$sqlRow_proc['codeFacility']."'";
										$all_proc_id_final_array[$sqlRow_proc['codeFacility']]="'".$sqlRow_proc['code']."'";
									}elseif(constant("STOP_PARENT_SUPERBILL")=="YES") {
										//DO NOTHING
									}else{
										$proc_id_final['code_inst'][]="'".$sqlRow_proc['code']."'";
										$all_proc_id_final_array[$sqlRow_proc['code']]="'".$sqlRow_proc['code']."'";
									}
								}else if($group_anesthesia>0 || ($billing_groups_arr["anes_grp"] == $gro_id && $billing_groups_arr["anes_grp"] > 0)){
									
										if($sqlRow_proc['codePractice']!=""){
											$proc_id_final['code_anes'][]="'".$sqlRow_proc['codePractice']."'";
											$all_proc_id_final_array[$sqlRow_proc['codePractice']]="'".$sqlRow_proc['code']."'";
										}elseif(constant("STOP_PARENT_SUPERBILL")=="YES") {
											//DO NOTHING
										}else{
											$proc_id_final['code_anes'][]="'".$sqlRow_proc['code']."'";
											$all_proc_id_final_array[$sqlRow_proc['code']]="'".$sqlRow_proc['code']."'";
										}
								}else{
									if($sqlRow_proc['codePractice']!=""){
										$proc_id_final['code_prac'][]="'".$sqlRow_proc['codePractice']."'";
										$all_proc_id_final_array[$sqlRow_proc['codePractice']]="'".$sqlRow_proc['code']."'";
									}elseif(constant("STOP_PARENT_SUPERBILL")=="YES") {
										//DO NOTHING
									}else{
										$proc_id_final['code_prac'][]="'".$sqlRow_proc['code']."'";
										$all_proc_id_final_array[$sqlRow_proc['code']]="'".$sqlRow_proc['code']."'";
									}
								}
							}
							
							if($group_institution>0 || ($billing_groups_arr["inst_grp"] == $gro_id && $billing_groups_arr["inst_grp"] > 0)){
								$groupKey = 'code_inst';
							}else if($group_anesthesia>0 || ($billing_groups_arr["anes_grp"] == $gro_id && $billing_groups_arr["anes_grp"] > 0)){
								$groupKey = 'code_anes';
							}else{
								$groupKey = 'code_prac';
							}
							$proc_id_final_imp			=	implode(",",$proc_id_final[$groupKey]);
							$proc_id_final_chk_imp		=	$proc_id_final_imp;
							$all_proc_id_final_imp		=	implode(",",$all_proc_id_final_array);
							$all_proc_id_final_chk_imp	=	$all_proc_id_final_imp;
							
						}
					}
					$log .= "\n"."GROUP KEY -".$groupKey;
					$log .= "\n"."PROCEDURE ID STRING VAR proc_id_final_chk_imp - ".$proc_id_final_chk_imp;
					$log .= "\n"."ALL PROCEDURE ID STRING VAR all_proc_id_final_chk_imp - ".$all_proc_id_final_chk_imp;
					/*
					print_r($proc_id_final);echo '<br>';
					print_r($all_proc_id_final_array);echo '<br>';
					echo 'FinalCheck:-'.$proc_id_final_chk_imp . '<br>Final All Check :-'.$all_proc_id_final_chk_imp;
					*/
					
					
					//START IF SIGN ALL BY SURGEON AND DX CODE TYPE IS NOT SET THEN GET DX CODE TYPE FROM ADMIN
					if(!$dx_code_type) {
						$queryDxTyp=imw_query("select `diagnosis_code_type` from `surgerycenter`")or die(imw_error());
						$dataDxTyp=imw_fetch_object($queryDxTyp);
						if($dataDxTyp->diagnosis_code_type)
						{
							$dx_code_type=$dataDxTyp->diagnosis_code_type;
						}
						
					}
					//END IF SIGN ALL BY SURGEON AND DX CODE TYPE IS NOT SET THEN GET DX CODE TYPE FROM ADMIN
					
					if($diag_ids || ($dos <= '2015-09-30' && trim($icd10_code)=='')) {
						if($diag_ids){
							$sql_qry_dx= "SELECT diag_code  FROM diagnosis_tbl  where diag_id in (".$diag_ids.")";
							$sql_qry_dx=imw_query($sql_qry_dx) or die($sql_qry_dx.imw_error());//$proc_code variable get from discharge_summary_sheet.php line no.156
							if(@imw_num_rows($sql_qry_dx)>0){
								$diag_code_arr='';
								while($sqlRow_dx = imw_fetch_array($sql_qry_dx)){
									$diag_code_exp=explode(',',$sqlRow_dx['diag_code']);
									$diag_code_arr[]=$diag_code_exp[0];
								}
							}
						}
						$dx_code_type = 'icd9';
					}
					elseif($icd10_code)
					{
						$diag_code_arr=explode(',',str_replace('@@',',',$icd10_code));
						$dx_code_type = 'icd10';
					}
					if(trim($dx_code_type)) {
						if(strtolower($dx_code_type) == 'icd9') {
							$dx_code_type_imedic = 0;	
						}else if(strtolower($dx_code_type) == 'icd10') {
							$dx_code_type_imedic = 1;	
						}
						
					}
					
					$update_conf_sc="update patientconfirmation set import_status='true' where patientConfirmationId='".$pConfId."'";
					$update_conf_run_sc=imw_query($update_conf_sc) or die($update_conf_sc.imw_error());
					//====end database===//
					
				
					imw_close($link); //CLOSE SURGERYCENTER CONNECTION
					include('connect_imwemr.php'); // imwemr connection
					
					// Match Procedures Code with iDoc
					$matchResultPracCode	=	array(); 
					if(trim($proc_id_final_chk_imp)) {
						$match_qry_proc_imw	= "Select cpt4_code, cpt_prac_code From cpt_fee_tbl where cpt_prac_code in (".$proc_id_final_chk_imp.") and status='Active' AND delete_status = '0'  ORDER BY cpt_prac_code ";
						$match_res_proc_imw	=	imw_query($match_qry_proc_imw) or die($match_qry_proc_imw.imw_error());
						if(@imw_num_rows($match_res_proc_imw)>0){
							while($match_row_proc_imw = imw_fetch_array($match_res_proc_imw)){
								$practiceCode	=	$match_row_proc_imw['cpt_prac_code'] ;
								$cptCode		=	$match_row_proc_imw['cpt4_code'] ;
								$matchResultPracCode[$practiceCode]	=	array('prac_code'=>$practiceCode,'cpt_code'=>$cptCode);
							}
						}
					}
					//echo '<br><br>'; print_r($matchResultPracCode);
					// End Match Procedures Code with iDoc
					
					
					// Match CPT4 Code with iDoc
					$matchResultCptCode	=	array(); 
					if(trim($all_proc_id_final_chk_imp)) {
						$match_qry_cpt_imw	= "Select cpt4_code, cpt_prac_code From cpt_fee_tbl where cpt4_code in (".$all_proc_id_final_chk_imp.") and status='Active' AND delete_status = '0' Group By cpt4_code ORDER BY cpt_prac_code ";
						$match_res_cpt_imw	=	imw_query($match_qry_cpt_imw) or die($match_qry_cpt_imw.imw_error());
						if(@imw_num_rows($match_res_cpt_imw)>0){
							while($match_row_cpt_imw = imw_fetch_array($match_res_cpt_imw)){
								$practiceCode	=	$match_row_cpt_imw['cpt_prac_code'] ;
								$cptCode		=	$match_row_cpt_imw['cpt4_code'] ;
								$matchResultCptCode[$cptCode] =	array('prac_code'=>$practiceCode,'cpt_code'=>$cptCode);
							}
						}
					}
					//echo '<br><br>'; print_r($matchResultCptCode);
					// End Match CPT4 Code with iDoc
					$log .= "\n"."PROC ID FINAL ARR - ".json_encode($proc_id_final);
					foreach($proc_id_final[$groupKey] as $temp_key=>$temp_prac_code)
					{
						$temp_prac_code		=	str_replace("'",'',$temp_prac_code);
						if(!array_key_exists($temp_prac_code,$matchResultPracCode))	//in_array($temp_prac_code,$matchResultPracCode)
						{
							$temp_cpt_code	=	str_replace("'",'',$all_proc_id_final_array[$temp_prac_code]);
							if(array_key_exists($temp_cpt_code,$matchResultCptCode))
							{
								$proc_id_final[$groupKey][$temp_key]=	"'".$matchResultCptCode[$temp_cpt_code]['prac_code']."'";
								$all_proc_id_final_array[$matchResultCptCode[$temp_cpt_code]['prac_code']]=$matchResultCptCode[$temp_cpt_code]['cpt_code'];				
							}
							elseif($temp_cpt_code)	
							{
								$proc_id_final[$groupKey][$temp_key]=	"'".$temp_cpt_code."'";	
								$all_proc_id_final_array[$temp_cpt_code]="'".$temp_cpt_code."'";
							}
						}
					}
					$proc_id_final_imp	=	implode(',',$proc_id_final[$groupKey]);
					$log .= "\n"."FINAL PROCEDURES- ".$proc_id_final_imp;
					// End Procedures Code with iDoc
					/*
					echo '<br> After Match:';
					print_r($proc_id_final);echo '<br>';
					print_r($all_proc_id_final_array);echo '<br>';
					echo 'After Implode:-'.$proc_id_final_imp ;
					*/
					
					//update dx code type in superbill
					imw_query("update superbill set sup_icd10='$dx_code_type_imedic', anes_start_time = '".$anes_start_time."', anes_stop_time = '".$anes_stop_time."' WHERE idSuperBill = '".$sup_ins_id."'");
					
					if(trim($proc_id_final_imp)) {
						
						// include POE Class File
						include_once ('Poe.php');
						//POE Object
						$oPoe = new Poe($imwPatientId,$enc_id);
						
						$sql_qry_proc_imw="SELECT * FROM cpt_fee_tbl where cpt_prac_code in (".$proc_id_final_imp.") and status='Active' AND delete_status = '0'  ORDER BY cpt_prac_code";
						$sql_qry_res_imw=imw_query($sql_qry_proc_imw) or die($sql_qry_proc_imw.imw_error());
						if(@imw_num_rows($sql_qry_res_imw)>0){
							$sqlRow_proc_imw = $proc_with_gcode = $proc_without_gcode = array();
							while($sqlRow_proc_imw_tmp = imw_fetch_array($sql_qry_res_imw)){
								$prac_code = $sqlRow_proc_imw_tmp['cpt_prac_code'];
								if(strtoupper($prac_code[0])=="G") {
									$proc_with_gcode[] = $sqlRow_proc_imw_tmp;	
								}else {
									$proc_without_gcode[] = $sqlRow_proc_imw_tmp;
								}
							}
							$sqlRow_proc_imw = array_merge($proc_without_gcode,$proc_with_gcode);
							$proCnt=0;
							$dx_count = 0;
							$proc_id_imw_final = $proc_id_imw_final_str = array();
							for($i=0;$i<count($sqlRow_proc_imw);$i++) {
								$proCnt++;
								$proc_id_imw_final[]=$sqlRow_proc_imw[$i]['cpt_prac_code'];
								$proc_id_imw_final_str[]="'".$sqlRow_proc_imw[$i]['cpt_prac_code']."'";
								$proc_cptcode_id=$sqlRow_proc_imw[$i]['cpt_prac_code'];
								$org_proc_cptcode=str_replace("'",'',$all_proc_id_final_array[$proc_cptcode_id]);
								$proc_desc_imw=$sqlRow_proc_imw[$i]['cpt_desc'];
								$proc_id_imw=$sqlRow_proc_imw[$i]['cpt_prac_code'];
								$proc_dx_codesArr = explode(',',$procDetailArr['dx'][$org_proc_cptcode]);
								
								$oPoe->isPoeCode($proc_cptcode_id);
								
								/*if($group_anesthesia > 0 )
								{
									$proc_unit	=	$sqlRow_proc_imw[$i]['units'];
									$proc_mod1	=	$sqlRow_proc_imw[$i]['mod1'];
									$proc_mod2	=	$sqlRow_proc_imw[$i]['mod2'];
									$proc_mod3	=	$sqlRow_proc_imw[$i]['mod3'];	
								}
								else*/
								{
									$proc_unit	=	$procDetailArr['unit'][$org_proc_cptcode];
									$proc_mod1	=	$procDetailArr['mod1'][$org_proc_cptcode];
									$proc_mod2	=	$procDetailArr['mod2'][$org_proc_cptcode];
									$proc_mod3	=	$procDetailArr['mod3'][$org_proc_cptcode];
								}
								
								$isAnes	=	$procDetailArr['isAnes'][$org_proc_cptcode];
								
								$dx1=$dx2=$dx3=$dx4=$dx5=$dx6=$dx7=$dx8=$dx9=$dx10=$dx11=$dx12='';
								for( $loop = 0;$loop < 12 ; $loop++)
								{
									$diagArrKey = array_search($proc_dx_codesArr[$loop],$diag_code_arr);
									if($diagArrKey>=0 && $proc_dx_codesArr[$loop]!='')
									{	
										$diagArrKeyNew=$diagArrKey+1;
										$varName = 'dx'.$diagArrKeyNew;
										$$varName = $proc_dx_codesArr[$loop];
									}
								}
								
								$insUpdtProQry = " INSERT INTO ";	
								$insUpdtProWhrQry = "";
								
								$ins_pro_imw=$insUpdtProQry." procedureinfo set cptCode ='".$proc_cptcode_id."',
											procedureName='".addslashes($proc_desc_imw)."',description ='".addslashes($proc_desc_imw)."',
											idSuperBill ='".$sup_ins_id."',units ='".$proc_unit."',
											dx1='".$dx1."',dx2='".$dx2."',dx3='".$dx3."',dx4='".$dx4."',dx5='".$dx5."',dx6='".$dx6."',
											dx7='".$dx7."',dx8='".$dx8."',dx9='".$dx9."',dx10='".$dx10."',dx11='".$dx11."',dx12='".$dx12."',
											modifier1='".$proc_mod1."',modifier2='".$proc_mod2."',modifier3='".$proc_mod3."',
											porder='".$proCnt."' ".$insUpdtProWhrQry;
											
								$ins_pro_run_imw=imw_query($ins_pro_imw) or die($ins_pro_imw.imw_error());	
							}
							
							if( constant("LOG_SYNC_SUPERBILL") == 'YES') {
								$diffArr = array();
								$diffArr = array_diff($proc_id_final[$groupKey],$proc_id_imw_final_str);
								if( count($diffArr) > 0 ) {
									$log .= "\n"."CPT CODE not found in iDOC SIDE- ".json_encode($diffArr);
									$log .= "\n"."==============================";
									$rootServerPath = $_SERVER['DOCUMENT_ROOT'];
									$logFolderPath = $rootServerPath."/".$surgeryCenterDirectoryName."/admin/pdfFiles/superbill_sync_log";
									if(!is_dir($logFolderPath)){		
										mkdir($logFolderPath, 0777);
									}
									$file_name = 'log_sync_superbill_'.date('Y_m_d').'.txt';
									$file_path = $logFolderPath.'/'.$file_name;
									file_put_contents($file_path,$log,FILE_APPEND);
								}
							}

							$arrDxCodes=array();
							$str_dx_codes="";
							for($i=1;$i<=12;$i++){
								$ic=$i-1;
								$arrDxCodes[$i]=$diag_code_arr[$ic];
							}
							$str_dx_codes=serialize($arrDxCodes);
							$proc_code_order = implode(',',$proc_id_imw_final);
							$update_supper="update superbill set procOrder ='".$proc_code_order."',
											arr_dx_codes='".imw_real_escape_string($str_dx_codes)."'
											where encounterId='".$enc_id."'";
							$update_supper_run=imw_query($update_supper) or die($update_supper.imw_error());
							
							//Set POE DATE 
							$oPoe->setPoeEnId();
							
						}
					}
					else {
						if( constant("LOG_SYNC_SUPERBILL") == 'YES') {
							$log .= "\n"."NO CPT CODE selected";
							$log .= "\n"."==============================";
							$rootServerPath = $_SERVER['DOCUMENT_ROOT'];
							$logFolderPath = $rootServerPath."/".$surgeryCenterDirectoryName."/admin/pdfFiles/superbill_sync_log";
							if(!is_dir($logFolderPath)){		
								mkdir($logFolderPath, 0777);
							}
							$file_name = 'log_sync_superbill_'.date('Y_m_d').'.txt';
							$file_path = $logFolderPath.'/'.$file_name;
							file_put_contents($file_path,$log,FILE_APPEND);
						}
					}
			}
			//===end====	
		}
	}
	
	
	imw_close($link_imwemr); //CLOSE IMWEMR CONNECTION
	include("common/conDb.php");  //SURGERYCENTER CONNECTION
			
}

// ADD/UPDATE SX PROCEDURE IN iASC
include_once("sync_sx_procedure.php");

?>