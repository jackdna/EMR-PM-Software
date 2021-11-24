<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php 
/*
File: electronic_billing_functions.php
Coded in PHP 7
Purpose: Contains functions used in electronic billing.
Access Type: Include file 
*/
require_once(dirname(__FILE__).'/SaveFile.php');
class ElectronicBilling{
	private $authId, $user_type, $clearingHouse, $batchType, $copayPolicies, $groupDetails, $HQFacility, $batchMode, $ARR_MODS;
	private $ARR_POS, $ARR_POS_FACILITIES;
	public $MarkSubmitted, $patientId, $groupId, $priPhysician, $priPayer, $InsDataId, $reviewServiceType, $onSetDate;
	public $ptDiagArr, $LineItemArr,$clearingHouseCredentials;
	private $oSaveFile;
	function __construct(){ //constructor
		$this->authId 			= intval($_SESSION['authId']);
		$this->user_type		= intval($_SESSION['logged_user_type']);
		$this->MarkSubmitted	= false;
		$this->reviewServiceType= '1';
		$this->oSaveFile		= new SaveFile();
	}
	
	/*--To get batch file list (status wise) --*/
	function get_batch_list($status='0',$insCompIDs='',$bypatient=''){

		$getSqlDateFormat= str_replace("Y","y",get_sql_date_format());
		$resultset = false; $q_part1 = "";
		if($insCompIDs != ''){$q_part1 = " AND bfs.ins_company_id IN ($insCompIDs)";}
		else if($bypatient != ''){
			//$q_part1 = " AND (".$this->get_encounters_ptwise($bypatient,'query').")";
			$encArr = $this->get_encounters_ptwise($bypatient);
			$batchIdArr = array();
			foreach($encArr as $eids){
				$batchIdArr[] = $this->getBatchFilesEncWise($eids);
			}
			$Batches = array();
			foreach($batchIdArr as $batchArr){
				foreach($batchArr as $batchData){
					$Batches[] = $batchData['Batch_file_submitte_id'];
				}
			}
			$strBatchIDs = implode(',',array_unique($Batches));
			if($strBatchIDs != ''){
				$q_part1 = " AND bfs.Batch_file_submitte_id IN ($strBatchIDs)";
			}
			$status="0','2";
		}
		$q1 = 	"SELECT bfs.file_name,bfs.Transaction_set_unique_control, bfs.status, bfs.clearing_house, bfs.file_format, 
				bfs.Batch_file_submitte_id, bfs.Interchange_control, bfs.ins_comp, 
				gn.name AS file_group_name, date_format(e9f.file_upload_date,'$getSqlDateFormat') as file_upload_date 
				FROM batch_file_submitte bfs 
				LEFT JOIN groups_new gn ON (gn.gro_id=bfs.group_id) 
				LEFT JOIN electronic_997_file e9f ON (e9f.set_number_id=bfs.Transaction_set_unique_control) 
				WHERE bfs.delete_status IN ('".$status."') AND bfs.file_name != ''".$q_part1." GROUP BY bfs.Interchange_control 
				ORDER BY bfs.Batch_file_submitte_id DESC, file_upload_date DESC";
		$res1 = imw_query($q1);
		if($res1 && imw_num_rows($res1)>0){
			while($rs1=imw_fetch_assoc($res1)){
				$resultset[] = $rs1;	
			}
		}
		return $resultset;
	}
	
	/**GET BATCH FILE LOG TO DISPLAY WHEN CREATED/DELETED/SUBMITTED/ARCHIVED/REGENERATED etc.***/
	function show_batch_file_log($file_id){
		$query = "select bfl.encounter_id, DATE_FORMAT(`action_date`,'%m-%d-%Y %H:%i:%s') as action_date, bfl.action, bfl.operator, 
				  concat(SUBSTRING(u.fname,1,1),SUBSTRING(u.lname,1,1)) as short_name, concat(u.lname,', ',u.fname) as medium_name, 
				  concat(u.lname,', ',u.fname,' ',u.mname) as full_name 
				  FROM batch_file_log bfl 
				  JOIN users u ON (bfl.operator = u.id) 
				  WHERE batch_file_submitte_id = $file_id order by bfl.id asc";
		$result = imw_query($query);echo imw_error();
		$data ='<table class="table table-bordered table-striped">';
		$data .='<thead><tr class="section_header">';
		$data .='<th style="width:130px;"># Encounters</th>';
		$data .='<th style="width:150px;">Action Date</th>';
		$data .='<th style="width:100px;">Action</th>';
		$data .='<th style="width:auto;">Operator</th>';
		$data .='</tr></thead>';
	//	$altclass= ' class="alt"';
		if(imw_num_rows($result)>0){
			while($row=imw_fetch_array($result))
			{
				if($row['action']=='regenrate') {$row['action']='regenerated';}
				$arr_encounters=explode("," , $row['encounter_id']);
			//	if($altclass == ''){$altclass= ' class="alt"';}else{$altclass= '';}
				$data .='<tr'.$altclass.'><td>' .count($arr_encounters). " Encounter(s)</td>";
				$data .="<td>" .$row['action_date']. "</td>";
				$data .="<td>" .ucwords($row['action']). "</td>";
				$data .="<td>" .$row['medium_name']. "</td>";
				$data .="</tr>";
			}
		}else{
			$data .= '<tr><td colspan="4" class="text-danger text-center">--No update history found for this file--</td></tr>';
		}
		
		$data .='</table>';
		return $data;
	}
	
	/*TO GET ALL THE ENCOUNTERS PATIENT WISE*/
	function get_encounters_ptwise($pat_name_val,$arrayORquery = 'array'){
		$search_by = "";
		if(is_numeric($pat_name_val) === true){
			$search_by = " AND id = '$pat_name_val'";
		}
		else{
			$pat_name_arr = preg_split('/,/', $pat_name_val);
			if(trim($pat_name_arr[0]) != ''){$search_by = " AND lname LIKE '%".trim($pat_name_arr[0])."%'";}
			if(trim($pat_name_arr[1]) != ''){$search_by .= " AND fname LIKE '%".trim($pat_name_arr[1])."%'";}	
		}
	
		//--- GET PATIENT ID ----
		$q1 = "SELECT id FROM patient_data WHERE 1=1".$search_by;
		$res1 = imw_query($q1);
		if($res1 && imw_num_rows($res1)>0){
			$arr_PtIDs = array();
			while($rs1 = imw_fetch_assoc($res1)){
				$arr_PtIDs[] = $rs1['id'];
			}
	
			if(count($arr_PtIDs) > 0){
				$ptid = join(',',$arr_PtIDs);
				$q2 = "select encounter_id from patient_charge_list where del_status='0' and patient_id in($ptid)";
				$res2 = imw_query($q2);
				if($res2 && imw_num_rows($res2)>0){
					$arr_encounters = array();
					$arr_encounterQueryPart = array();
					while($rs2 = imw_fetch_assoc($res2)){
						$arr_encounters[] = $rs2['encounter_id'];
						$arr_encounterQueryPart[] = "encounter_id like '%".$rs2['encounter_id']."%'";
					}
					$encounterIdStr = join(" or  ",$arr_encounterQueryPart);
					if($arrayORquery=='array'){return $arr_encounters;}
					else if($arrayORquery=='query'){return $encounterIdStr;}
				}
			}
		}
		return false;
	}
	
	/*SET BATCH FILE STATUS AS 'DELETED' OR 'ARCHIEVED'*/
	function set_file_status($fileIDs,$setFileStatus){
		$actionTitle = '';
		$q1 = "UPDATE batch_file_submitte SET delete_status='$setFileStatus' WHERE Batch_file_submitte_id IN ($fileIDs)";
		$res1 = imw_query($q1);
		if($res1){
			$numOfFiles = count(explode(',',$fileIDs));
			$actionTitle = ($setFileStatus==1) ? $numOfFiles.' File(s) Deleted' : (($setFileStatus==2) ? $numOfFiles.' File(s) Archived' : (($setFileStatus==0) ? $numOfFiles.' File(s) Un-Archived' : ''));
			$log_Status = ($setFileStatus==1) ? 'Deleted' : (($setFileStatus==2) ? 'Archived' : (($setFileStatus==0) ? 'Un-Archived' : ''));
			foreach(explode(',',$fileIDs) as $fileID){
				$this->batch_file_log($fileID,$log_Status);
			}
		}else{
			$actionTitle = '<span class="warning"><b>Unable to process request.</b><br><b>Error:</b> '.imw_error().'</span>';
		}
		return $actionTitle;
	}
	
	/*RECORD LOG OF ACTIONS TAKEN WITH A BATCH FILE*/
	function batch_file_log($file_id,$action){
		$action_date=date('Y-m-d H:i:s');
		$query="SELECT Batch_file_submitte_id, Interchange_control, encounter_id FROM batch_file_submitte 
				WHERE Batch_file_submitte_id  = $file_id";
		$result=imw_query($query);
		$row=imw_fetch_assoc($result);
		$ins_query="INSERT INTO batch_file_log SET batch_file_submitte_id = '".$row['Batch_file_submitte_id']."', 
					Interchange_no = '".$row['Interchange_control']."', encounter_id = '".$row['encounter_id']."', 
					action_date = '$action_date', action = '$action', operator = '".$this->authId."'";
		$ins_result=imw_query($ins_query);
		//echo imw_error();
	}
	
	/*GET GROUP DETAILS. <EMPTY> PARAMETER RETURNS ALL GROUPS, OTHERWISE SPECIFIC GROUUP*/
	function get_groups_detail($grpId=''){
		$return=false;
		$q = "SELECT * FROM groups_new WHERE del_status='0' ORDER BY name";
		if($grpId!=''){$q = "SELECT * FROM groups_new WHERE gro_id = '".intval($grpId)."' LIMIT 0,1";}
		$res = imw_query($q);
		if($res && imw_num_rows($res)>0){
			if($grpId!=''){$return = imw_fetch_assoc($res);}
			else{
				while($rs = imw_fetch_assoc($res)){
					$gro_id	= $rs['gro_id'];
					$return[$gro_id] = $rs;
				}				
			}
		}
		return $return;
	}
	
	/*GET FACTILITY DETAILS. <EMPTY> PARAMETER RETURNS HEAD QUARTER FACTILITY DETAILS, OTHERWISE SPECIFIC*/
	function get_facility_detail($facId='',$hq='yes'){
		$return=false; $facId  = intval($facId);
		$q = "SELECT * FROM facility WHERE facility_type='1'";
		if($facId > 0){$q = "SELECT * FROM facility WHERE id ='".intval($facId)."' LIMIT 0,1";}
		$res = imw_query($q);
		if($res && imw_num_rows($res)>0){
			$return = imw_fetch_assoc($res);
		}
		return $return;
	}

	/*CONVERT INTERFACE DATE TO DB DATE FORMAT*/
	function date_for_db($date){
		$getDate = explode("-",$date);
		$curDate = $getDate[2].'-'.$getDate[0].'-'.$getDate[1];	
		return $curDate;
	}
	
	/*CONVERT DB DATE TO INTERFACE FORMAT DATE*/
	function date_from_db($date){
		$getDate = explode("-",$date);
		$curDate = $getDate[1].'-'.$getDate[2].'-'.$getDate[0];	
		return $curDate;
	}
	
	//BUGFIX------BEGIN UPDATE BLANK INS SUBSCRIBER GENDER TO PATIENT GENDER FOR SELF RELATION INSURANCES
	function update_blank_ins_gender($comma_ptIDs){
		/******FIX TO BLANK GENDER OF SUBSCRIBER*********/
		$qry = "UPDATE insurance_data ins INNER JOIN patient_data pat ON (ins.pid = pat.id) SET ins.subscriber_sex = pat.sex WHERE pat.sex != '' AND LOWER(ins.subscriber_relationship='self') AND IFNULL(ins.subscriber_sex,'') = '' AND ins.actInsComp=1 AND ins.pid IN ($comma_ptIDs)";
		imw_query($qry);

		/******FIX TO different character case of SELF RELATIONSHIP OF SUBSCRIBER*********/		
		$qry1 = "UPDATE insurance_data SET subscriber_relationship='self' WHERE subscriber_relationship='Self' AND pid IN ($comma_ptIDs)";
		imw_query($qry1);
		
		/******FIX TO BLANK STREET ADDRESS OF SUBSCRIBER*********/
		$qry = "UPDATE insurance_data ins INNER JOIN patient_data pat ON (ins.pid = pat.id) SET ins.subscriber_street = pat.street WHERE pat.street != '' AND IFNULL(ins.subscriber_street,'') = '' AND LOWER(ins.subscriber_relationship='self') AND ins.actInsComp=1 AND ins.pid IN ($comma_ptIDs)";
		imw_query($qry);
		
		/******FIX TO BLANK STREET ADDRESS 2 OF SUBSCRIBER*********/
		$qry = "UPDATE insurance_data ins INNER JOIN patient_data pat ON (ins.pid = pat.id) SET ins.subscriber_street_2 = pat.street2 WHERE pat.street2 != '' AND IFNULL(ins.subscriber_street_2,'') = '' AND LOWER(ins.subscriber_relationship='self') AND ins.actInsComp=1 AND ins.pid IN ($comma_ptIDs)";
		imw_query($qry);
		
		/******FIX TO BLANK CITY OF SUBSCRIBER*********/
		$qry = "UPDATE insurance_data ins INNER JOIN patient_data pat ON (ins.pid = pat.id) SET ins.subscriber_city = pat.city WHERE pat.city != '' AND IFNULL(ins.subscriber_city,'') = '' AND LOWER(ins.subscriber_relationship='self') AND ins.actInsComp=1 AND ins.pid IN ($comma_ptIDs)";
		imw_query($qry);
		
		/******FIX TO BLANK STATE OF SUBSCRIBER*********/
		$qry = "UPDATE insurance_data ins INNER JOIN patient_data pat ON (ins.pid = pat.id) SET ins.subscriber_state = pat.state WHERE pat.state != '' AND IFNULL(ins.subscriber_state,'') = '' 
				AND LOWER(ins.subscriber_relationship='self') AND ins.actInsComp=1 AND ins.pid IN ($comma_ptIDs)";
		imw_query($qry);
		
		/******FIX TO BLANK POSTAL CODE OF SUBSCRIBER*********/
		$qry = "UPDATE insurance_data ins INNER JOIN patient_data pat ON (ins.pid = pat.id) SET ins.subscriber_postal_code = pat.postal_code WHERE pat.postal_code != '' AND IFNULL(ins.subscriber_postal_code,'') = '' AND LOWER(ins.subscriber_relationship='self') AND ins.actInsComp=1 AND ins.pid IN ($comma_ptIDs)";
		imw_query($qry);
		
		/******FIX TO BLANK POSTAL CODE EXTENSION OF SUBSCRIBER*********/
		$qry = "UPDATE insurance_data ins INNER JOIN patient_data pat ON (ins.pid = pat.id) SET ins.zip_ext = pat.zip_ext WHERE pat.zip_ext != '' AND IFNULL(ins.zip_ext,'') = '' AND LOWER(ins.subscriber_relationship='self') AND ins.actInsComp=1 AND ins.pid IN ($comma_ptIDs)";
		imw_query($qry);
		
		/******FIX TO BLANK LAST Name OF SUBSCRIBER*********/
		$qry = "UPDATE insurance_data ins INNER JOIN patient_data pat ON (ins.pid = pat.id) SET ins.subscriber_lname = pat.lname WHERE pat.lname != '' AND IFNULL(ins.subscriber_lname,'') = '' AND LOWER(ins.subscriber_relationship='self') AND ins.actInsComp=1 AND ins.pid IN ($comma_ptIDs)";
		imw_query($qry);
		
		/******FIX TO BLANK FIRST Name OF SUBSCRIBER*********/
		$qry = "UPDATE insurance_data ins INNER JOIN patient_data pat ON (ins.pid = pat.id) SET ins.subscriber_fname = pat.fname WHERE pat.fname != '' AND IFNULL(ins.subscriber_fname,'') = '' AND LOWER(ins.subscriber_relationship='self') AND ins.actInsComp=1 AND ins.pid IN ($comma_ptIDs)";
		imw_query($qry);
		
		/******FIX TO BLANK Middle Name OF SUBSCRIBER*********/
		$qry = "UPDATE insurance_data ins INNER JOIN patient_data pat ON (ins.pid = pat.id) SET ins.subscriber_mname = pat.mname WHERE pat.mname != '' AND IFNULL(ins.subscriber_mname,'') = '' AND LOWER(ins.subscriber_relationship='self') AND ins.actInsComp=1 AND ins.pid IN ($comma_ptIDs)";
		imw_query($qry);
		
		/******FIX TO BLANK DOB OF SUBSCRIBER*********/
		$qry = "UPDATE insurance_data ins INNER JOIN patient_data pat ON (ins.pid = pat.id) SET ins.subscriber_DOB = pat.DOB WHERE pat.DOB != '0000-00-00' AND ins.subscriber_DOB = '0000-00-00' AND LOWER(ins.subscriber_relationship='self') AND ins.actInsComp=1 AND ins.pid IN ($comma_ptIDs)";
		imw_query($qry);
		
		
	}
	
	//BUGFIX------BEGIN UPDATE BLANK SUBSCRIBER ADDRESS INSURANCES
	function update_subscriber_blank_address(){
		$qry = "UPDATE insurance_data ins,patient_data pat
				SET ins.subscriber_street 		 = pat.street, 
					ins.subscriber_street_2 	 = pat.street2, 
					ins.subscriber_postal_code	 = pat.postal_code, 
					ins.zip_ext 				 = pat.zip_ext, 
					ins.subscriber_city			 = pat.city, 
					ins.subscriber_state		 = pat.state,
					ins.subscriber_country	  	 = pat.country_code 
				WHERE pat.street != '' AND
			  	LOWER(ins.subscriber_relationship='self')
			  	AND IFNULL(ins.subscriber_street,'') = '' 
			  	AND ins.actInsComp=1
			  	AND ins.pid = pat.id
				";
		imw_query($qry);
		
		$qry1 = "UPDATE insurance_data SET subscriber_relationship='self' WHERE subscriber_relationship='Self'";
		imw_query($qry1);
	}
	//--------------------------------------------------------------------------------------------
	
	/*GET POSTED ENCOUTERS TO SHOW OR TO BATCH UP*/
	function process_claims($post){
		$this->clearingHouse	= 'Emdeon';//default value.
		$this->batchType		= 'Professional';
		$this->batchMode		= trim($post['bType']);
		$this->copayPolicies	= $this->get_copay_policies();
		$this->groupDetails		= $this->get_groups_detail($post['grp']);
		$this->HQFacility		= $this->get_facility_detail('','yes');
		$this->ARR_MODS 		= $this->get_all_modifiers();
		$this->ARR_POS			= $this->get_pos_details();
		$this->ARR_POS_FACILITIES = $this->get_pos_facilities();
		$return = array();
		if(trim($post['ProcessClaims'])!=''){
			$arr_charge_lists = $arr_claim_type = array();
			$main_chlist_ids_with_ClaimType = explode(', ',$post['ProcessClaims']);
			$arr_claims_to_process = $arr_claims_with_type = array();
			foreach($main_chlist_ids_with_ClaimType as $vals){
				$arr_vals_temp = explode(':',$vals);
				$arr_charge_lists 	= $arr_vals_temp[0];
				$arr_claim_type 	= $arr_vals_temp[1];
				$arr_claim_num 		= $arr_vals_temp[2];
				if($arr_claim_type!='8' || ($arr_claim_type=='8' && !empty($arr_claim_num))){//either not voided claim, or voided+claimControl#=yes
					$arr_claims_to_process[] = $arr_charge_lists;
					$arr_claims_with_type[$arr_charge_lists] = $arr_claim_type;
					$arr_claims_ctrl_num[$arr_charge_lists]  = $arr_claim_num;
				}
			}
			$post['ProcessClaims'] 	= implode(', ',$arr_claims_to_process);
			$PCLid_CliamType 		= json_encode($arr_claims_with_type);
			$PCLid_CliamCtrlNums 	= json_encode($arr_claims_ctrl_num);
		}
		$actionType = intval($post['actionType']);
		$ARR_PCL = $this->get_posted_encounters($post);
		
		$this->update_blank_ins_gender($ARR_PCL['Patients']); // updating missing data if any.

		$this->set_claims_category($ARR_PCL['pcldata'],$this->groupDetails);
		$insCommaStr = $ARR_PCL['usedInsComps'];
		$totalRecords = count($ARR_PCL['pcldata']);
		$return['resultFound'] = $totalRecords;
		$copayPolicies = $this->get_copay_policies();
		if($totalRecords>0){
			switch($actionType){
				case 0:
					$ARRclaimsToShow = $this->get_merged_claim_data($ARR_PCL,'show');//pre($ARRclaimsToShow);
					echo json_encode($ARRclaimsToShow);
					break;
				case 1:
					if($post['ct']=='Primary'){
						$InsComp = 'primaryInsuranceCoId';
					}else{
						$InsComp = 'secondaryInsuranceCoId';
					}
					$main_charge_list_id1 = $post['ProcessClaims'];
					imw_query("SET SESSION group_concat_max_len = 1000000;"); //increasing Concat limit temporarily.
					$separated_claims_arr = $this->separate_claims_for_batches($post['ProcessClaims'],$InsComp,$this->groupDetails['group_institution']);
					//pre($separated_claims_arr,1);
					$gro_id = $post['grp'];
					$createClaims = 'createClaims';
						
					$_REQUEST["ProductionFile"] = $ProductionFile = $post['bType'];
					global $billing_global_tsuc_separator;
					global $billing_global_server_name;
					global $arr_BL_payers;
					global $arr_Medicare_payers;
					global $billing_global_clia_num;
					global $arr_DME_payers;
					global $billing_global_taxonomy_number;
					$batchFormed = false;
					$batch_create_status = array();
					
					foreach($separated_claims_arr as $pos_fac_alt_npi=>$separated_rs_master){
						if($pos_fac_alt_npi!='') $overRightPayerWiseNPI = $pos_fac_alt_npi; else $overRightPayerWiseNPI=false;
						foreach($separated_rs_master as $inst_prof_type=>$sub_separated_rs_master){
							$grouped_ins_override = $inst_prof_type;
							foreach($sub_separated_rs_master as $separated_rs){
								$main_charge_list_id = $separated_rs['seprated_CHLids'];
								$posted_claim_ins_ids = $separated_rs['posted_claim_ins_ids'];
								$post['instype'] = $posted_claim_ins_ids;
								
								if(trim(strtoupper($separated_rs['ins_type']))!='MB'){$overRightPayerWiseNPI=false;}
								else if($pos_fac_alt_npi!=''){$overRightPayerWiseNPI = $pos_fac_alt_npi;}
								
								/******CHECK FOR INSTUTIONAL ANESTHESIA CLAIMS******/
								if($this->groupDetails['group_institution']=='1' || 1==1){ //THESE CHECKS WILL BE FOR ALL TYPE GROUPS NOW;AFTER DISCUSSION WITH ak ON 11-APRIL-2018.
									if($this->groupDetails['group_institution']!='1'){
										$grouped_ins_override = 'INST_PROF';
									}
									
									$q_separate_INST_ANESwise = "SELECT GROUP_CONCAT(charge_list_id) AS seprated_CHLids, GROUP_CONCAT(pcl.".$InsComp.") AS  posted_claim_ins_ids, pcl.billing_type AS inst_anes_claim_type 
															FROM patient_charge_list pcl 
															WHERE pcl.charge_list_id IN (".$main_charge_list_id.") GROUP BY (pcl.billing_type)";
									$res_separate_INST_ANESwise = imw_query($q_separate_INST_ANESwise);
									//echo $q_separate_INST_ANESwise.'<hr>'.imw_error().'<hr><hr>';die;
									if($res_separate_INST_ANESwise && imw_num_rows($res_separate_INST_ANESwise)>0){
										while($rs_separate_INST_ANESwise = imw_fetch_assoc($res_separate_INST_ANESwise)){
											$main_charge_list_id = $rs_separate_INST_ANESwise['seprated_CHLids'];
											if($rs_separate_INST_ANESwise['inst_anes_claim_type'] == '2'){
												$grouped_ins_override = 'INST_ONLY';
											}else if($rs_separate_INST_ANESwise['inst_anes_claim_type'] == '1' || $rs_separate_INST_ANESwise['inst_anes_claim_type'] == '3'){
												$grouped_ins_override = 'INST_PROF';
											}
											
											//if encounter type is not set, but group is marked as anaesthesia (no matter if marked institutional also)
											if($rs_separate_INST_ANESwise['inst_anes_claim_type'] == '0' && ($this->groupDetails['group_anesthesia']=='1' || $this->groupDetails['group_institution'] != '1')){
												$grouped_ins_override = 'INST_PROF';
											}
											if(trim(strtoupper($separated_rs['ins_type']))=='MB'){
												$grouped_ins_override = 'INST_PROF';
											}
											
											$gro_id = $post['grp'];
											$posted_claim_ins_ids = $rs_separate_INST_ANESwise['posted_claim_ins_ids'];
											$post['instype'] = $posted_claim_ins_ids;
											
											if(strtolower($billing_global_server_name)=='cec'){
												include("electronic_billing_bridge_cec.php");
											}else{
												include("electronic_billing_bridge.php");
											}
											if($insert_data > 0){
												$batchFormed = true;
												$batch_create_status['status'] = 'success';
												if($batch_create_status['batchid']!=''){$batch_create_status['batchid'] .= ',';}
												$batch_create_status['batchid'] .= $insert_data;
												unset($insert_data);
											}													
										}
									}
								}else{
									$grouped_ins_override = 'INST_PROF';
									if(strtolower($billing_global_server_name)=='cec'){
										include("electronic_billing_bridge_cec.php");
									}else{
										include("electronic_billing_bridge.php");
									}
									if($insert_data > 0){
										$batchFormed = true;
										$batch_create_status['status'] = 'success';
										if($batch_create_status['batchid']!=''){$batch_create_status['batchid'] .= ',';}
										$batch_create_status['batchid'] .= $insert_data;
										unset($insert_data);
									}
								}
							}
						}
					}
					if(isset($_SESSION['interchangecontrolnumber'])){
						$_SESSION['interchangecontrolnumber'] = '';
						unset($_SESSION['interchangecontrolnumber']);
					}
					if($batchFormed)echo json_encode($batch_create_status);
					break;	
			}
		}
	}
	
	/*TO RE-GENERATE A BATCH FILE*/
	function regenerate_file($fileIDs,$batchType){
		$production_code = $batchType;
		$filesName = array($fileIDs);
		set_time_limit(600);
		$_REQUEST["ProductionFile"] = $ProductionFile = $batchType;
		global $billing_global_tsuc_separator;
		global $billing_global_server_name;
		global $arr_BL_payers;
		global $arr_Medicare_payers;
		global $billing_global_clia_num;
		global $arr_DME_payers;
		global $billing_global_taxonomy_number;
		if(strtolower($billing_global_server_name)=='cec'){
			include_once("re_print_file_cec.php");
		}else{
			include_once("re_print_file.php");
		}
		$response = array();
		$response['error'] = $errors.$err;
		$response['action'] = $script;
		$response['invalidCHLids'] = $inValidChargeListIds;
		return $response;
	}
	
	/*TO CREATE A SECONDARY BATCH FILE from ERA posting*/
	function regenerate_secondary_batch_from_era($main_charge_list_id,$InsComp){
		set_time_limit(600);
		global $main_charge_list_id;
		global $billing_global_tsuc_separator;
		global $billing_global_server_name;
		global $arr_BL_payers;
		global $arr_Medicare_payers;
		global $billing_global_clia_num;
		global $arr_DME_payers;
		global $billing_global_taxonomy_number;
		$createClaims = 'createClaims';
		$q1 = "SELECT gro_id, GROUP_CONCAT($InsComp) as posted_ins_ids FROM patient_charge_list WHERE charge_list_id IN ($main_charge_list_id) GROUP BY gro_id";
		$res1 = imw_query($q1);
		if($res1 && imw_num_rows($res1)==1){
			$rs1 = imw_fetch_assoc($res1);
			$gro_id = $rs1['gro_id'];
			include_once("electronic_billing_bridge_from_era.php");

			if($insert_data > 0){
				$batchFormed = true;
				$batch_create_status['status'] = 'success';
				if($batch_create_status['batchid']!=''){$batch_create_status['batchid'] .= ',';}
				$batch_create_status['batchid'] .= $insert_data;
				unset($insert_data);
			}
			return $batch_create_status;
		}else{
			//claims found for multiple groups.
		}
	}
	
	/*GETTING ALL THE POSTED ENCOUNTERS*/
	function get_posted_encounters($post){
		$ARR_PCL = array();
		$insurance_in = $post['instype'];
		$pf = $post['pf']!='' ? $this->date_for_db($post['pf']) : '';
		$pu = $post['pu']!='' ? $this->date_for_db($post['pu']) : '';
		$df = $post['df']!='' ? $this->date_for_db($post['df']) : '';
		$du = $post['du']!='' ? $this->date_for_db($post['du']) : '';

		$claims_to_process = trim($post['ProcessClaims']);
		/*--SUB-SELECTION ACCORDING TO INS.GROUP--*/
		$sub_charges_query = '';
		$allGroupedIns = trim($post['allIns']);
		$grouped_ins = trim($post['insGrp']);
		switch($post['oInsComps']){
			case 'selected':
				if($grouped_ins != ''){$sub_charges_query = " AND ic.id IN ($grouped_ins) ";}
				break;
			case 'exclude':
				if($grouped_ins != ''){$sub_charges_query = " AND ic.id NOT IN ($grouped_ins) ";}					
				break;
			case 'all':
				if($allGroupedIns != ''){$sub_charges_query = " AND ic.id IN ($allGroupedIns) ";}
				break;
		}
		
		$charges_qry = "SELECT pcl.charge_list_id AS ClId, 
						pcl.encounter_id AS EncId, pcl.patient_id AS PtId, pcl.primaryInsuranceCoId AS PriIns, 
						pcl.secondaryInsuranceCoId AS SecIns, pcl.tertiaryInsuranceCoId AS TerIns, 
						pcl.date_of_service AS encDOS, pcl.postedDate AS PostedDate, 
						pcl.primaryProviderId AS PhysicianId, pcl.admit_date, pcl.disch_date, 
						pcl.moaQualifier, pcl.case_type_id, 
						pcl.claim_ctrl_pri, pcl.claim_ctrl_sec, 
						pcl.reff_phy_id, pcl.enc_icd10, pcl.reff_phy_nr, pcl.void_notify, 
						pcl.billing_type, 
						pd.fname AS ptFname, pd.lname AS ptLname, substring(pd.mname,1,1) AS ptMname, pd.street AS ptStreet, 
						pd.street2 AS ptStreet2, CONCAT(pd.postal_code,pd.zip_ext) AS ptPostal_code, pd.city AS ptCity, 
						pd.state AS ptState, pd.sex AS ptSex, pd.DOB as ptDOB, pd.default_facility AS ptDefaultFacility, 
						pd.primary_care_id AS ptPrimaryCare, pd.providerID AS ptProviderID, 
						ic.contact_address AS InsCompAddress, ic.City AS InsCompCity, ic.State AS InsCompState, 
						CONCAT(ic.Zip,ic.zip_ext) AS InsCompZip, ic.phone AS InsCompPhone, ic.id AS InsCompId, 
						ic.Payer_id AS InsCompPayerId, ic.Payer_id_pro AS InsCompPayerIdPro, ic.BatchFile, 
						ic.in_house_code, ic.name as InsName, ic.Reciever_id, ic.payer_type, ic.claim_type, 
						ic.Insurance_payment, ic.secondary_payment_method, 
						u.user_npi AS userNpi, u.BlueShieldId AS BlueShieldIds,
						trim(u.fname) AS usersFname, substring(trim(u.mname),1,1) AS usersMname,
						trim(u.lname) AS usersLname, u.id AS usersId,
						u.TaxonomyId AS usersTaxonomyId, u.TaxId AS usersTaxId,
						u.default_group AS users_default_group,
						u.federaltaxid AS usersFederaltaxid 
						FROM patient_charge_list pcl 
						LEFT JOIN patient_charge_list_details pcld ON (pcld.charge_list_id = pcl.charge_list_id AND pcld.differ_insurance_bill != 'true') 
						LEFT JOIN cpt_fee_tbl cft ON cft.cpt_fee_id = pcld.procCode 
						LEFT JOIN users u ON u.id = pcl.primaryProviderId 
						LEFT JOIN patient_data pd ON pcl.patient_id = pd.id ";
		if($post['ct']=='Primary'){
			$charges_qry .= "JOIN insurance_companies ic ON (ic.id = pcl.primaryInsuranceCoId 
						AND ic.id in($insurance_in)".$sub_charges_query." AND ic.name != 'SELF PAY' AND ic.Insurance_payment = 'Electronics') ";
		}else if($post['ct']=='Secondary'){
			$charges_qry .= "JOIN insurance_companies ic ON (ic.id = pcl.secondaryInsuranceCoId 
						AND ic.id in($insurance_in)".$sub_charges_query." AND ic.name != 'SELF PAY' AND ic.secondary_payment_method = 'Electronics') ";
		}
		$charges_qry .= "WHERE ((pcld.del_status='0' 
						AND pcld.claim_status = '0' 
						AND pcl.submitted = 'true') OR (pcl.void_notify='1' AND pcl.void_claim_date='0000-00-00 00:00:00')) 
						AND pcld.proc_selfpay = '0' 
						AND pcld.posted_status = '1' 
						AND (cft.not_covered = '0' or cft.not_covered is NULL) ";
		if($post['ct']=='Primary'){
			$charges_qry .= "AND ((pcl.primary_paid = 'false' 
							AND pcl.primarySubmit = '0' 
							AND pcl.hcfaStatus != '1') OR pcl.void_notify='1')  
							AND pcl.primaryInsuranceCoId > 0 
							AND (pcl.totalBalance > '0' OR (pcl.postedAmount > 0 AND pcl.date_of_service >= '2013-01-01')) ";//AND ic.Insurance_payment = 'Electronics'
		}else if($post['ct']=='Secondary'){
			$charges_qry .= "AND ((pcl.primary_paid = 'true' 
							AND pcl.secondary_paid = 'false' 
							AND pcl.primarySubmit  = '1') OR pcl.void_notify='1') 
							AND (pcl.totalBalance > '0' OR (pcl.postedAmount > 0 AND pcl.date_of_service >= '2013-03-01')) 
							AND pcl.secondaryInsuranceCoId > 0 
							AND pcl.secondarySubmit = '0' ";//AND ic.secondary_payment_method = 'Electronics' 
		}
		
		if(empty($pf) === false && empty($pu) === false){
			$charges_qry .= " AND (pcl.postedDate BETWEEN '$pf' AND '$pu')";
		}else if(empty($pf) === false && empty($pu) === true){
			$charges_qry .= " AND (pcl.postedDate >= '$pf')";
		}else if(empty($pf) === true && empty($pu) === false){
			$charges_qry .= " AND (pcl.postedDate <= '$pu')";
		}
		
		if(empty($df) === false && empty($du) === false){
			$charges_qry .= " AND (pcl.date_of_service BETWEEN '$df' AND '$du')";
		}else if(empty($df) === false && empty($du) === true){
			$charges_qry .= " AND (pcl.date_of_service >= '$df')";
		}else if(empty($df) === true && empty($du) === false){
			$charges_qry .= " AND (pcl.date_of_service <= '$du')";
		}
		
		if(intval($post['grp']) > 0){
			$charges_qry .= " AND pcl.gro_id = '".$post['grp']."'";
		}
		if($claims_to_process != ''){
			$charges_qry .= " AND pcl.charge_list_id IN (".$claims_to_process.")";	
		}
		$charges_qry .= " AND pcl.enc_accept_assignment IN (0,1)";	
		$charges_qry .= " GROUP BY pcl.charge_list_id ORDER BY pcl.enc_icd10, usersLname, pcl.primaryProviderId, ptLname";
		
		$charges_res = imw_query($charges_qry);
		if($charges_res && imw_num_rows($charges_res)>0){
			$PCLids = $usedInsComps = $claimPatients = $encounters = array();
			
			while($charges_rs = imw_fetch_assoc($charges_res)){
				$PCLids[]				= $charges_rs['ClId'];
				$claimPatients[]		= $charges_rs['PtId'];
				$encounters[]			= $charges_rs['EncId'];
				if($post['ct']=='Primary'){
					$usedInsComps[] = $charges_rs['PriIns'];
					$charges_rs['EnqIns'] = $charges_rs['PriIns'];
				}else if($post['ct']=='Secondary'){
					$usedInsComps[] = $charges_rs['SecIns'];
					$charges_rs['EnqIns'] = $charges_rs['SecIns'];
				}
				$ARR_PCL['pcldata'][]	= $charges_rs;
			}
			
			//all insurance providers used in posted encounters.
			$usedInsComps = array_unique($usedInsComps);
			$strUsedInsComps = implode(', ',$usedInsComps);
			$ARR_PCL['usedInsComps'] = $strUsedInsComps;
			
			//all charge_list_id of posted encounters.
			$strPCLids = implode(', ',$PCLids);
			$ARR_PCL['ChListIDs'] = $strPCLids;
			
			//all patients in posted encounters.
			$strPatients = implode(', ',$claimPatients);
			$ARR_PCL['Patients'] = $strPatients;
			
			//all encounters in posted encounters.
			$strEncounters = implode(', ',$encounters);
			$ARR_PCL['Encounters'] = $strEncounters;		
		}
		return $ARR_PCL;
	}
	
	/*DECIDE CLEARING HOUSE*/
	function set_claims_category($arr_PCLdata,$arr_GroupDetails){
		$institutional = intval($arr_GroupDetails['group_institution']);
		if(strtolower($this->copayPolicies['Name'])=='navicure'){
			$this->clearingHouse = 'Navicure';
		}else{
			for($i=0; $i<count($arr_PCLdata); $i++){
				$directBilling	= $arr_PCLdata[$i]['BatchFile'];
				$ctype			= $arr_PCLdata[$i]['claim_type'];
				if($directBilling=='1' && $ctype=='1'){
					$this->clearingHouse = 'Medicare';
					break;
				}
			}
		}
		if($institutional==1){
			if($this->clearingHouse=='Navicure'){$this->clearingHouse = 'GHN';}
			$this->batchType = 'Institutional';
		}
		
	}
	
	/*CHECKING ADMING SETTINGS*/
	function get_copay_policies($fields='Name'){
		$return=false;
		$q = "SELECT $fields FROM copay_policies WHERE policies_id = 1 LIMIT 0,1";
		$res = imw_query($q);
		if($res && imw_num_rows($res)==1){
			$result = imw_fetch_assoc($res);
		}
		return $result;
	}
	
	/*GET PLACE OF SERVICE FACILITY DETAILS*/
	function get_pos_details(){
		$return=false;
		$q = "SELECT * FROM pos_tbl";
		$res = imw_query($q);
		if($res && imw_num_rows($res)>0){
			while($rs = imw_fetch_assoc($res)){
				$posid = $rs['pos_id'];
				$result[$posid] = $rs;
			}
		}
		return $result;
	}
	
	/*GET ALL POS FACILITIES*/
	function get_pos_facilities(){
		$return=false;
		$q = "SELECT * FROM pos_facilityies_tbl";
		$res = imw_query($q);
		if($res && imw_num_rows($res)>0){
			while($rs = imw_fetch_assoc($res)){
				$posFacId = $rs['pos_facility_id'];
				$result[$posFacId] = $rs;
			}
		}
		return $result;		
	}
	
	/*GET SPECIFIC REFERRING PHYSICIAN DETAILS*/
	function get_ref_phy_details($refPhyId){
		$return=false;
		$q = "SELECT * FROM refferphysician WHERE physician_Reffer_id='".$refPhyId."'";
		$res = imw_query($q);
		if($res && imw_num_rows($res)==1){
			$return = imw_fetch_assoc($res);
		}
		return $return;		
	}
	
	/*GETTING CHARGE LIST DETAILS (ENCOUNTER DETAILS)*/
	function get_charges_details($arr_PCLdata){
		$commaCLids = $arr_PCLdata['ChListIDs'];
		$return = false;
		$q1 = "SELECT pcld.*, cft.cpt4_code, cft.cpt_desc FROM patient_charge_list_details pcld 
				LEFT JOIN cpt_fee_tbl cft ON (cft.cpt_fee_id = pcld.procCode) 
				JOIN patient_charge_list pcl ON (pcl.charge_list_id = pcld.charge_list_id) 
				WHERE pcld.charge_list_id IN ($commaCLids) 
				AND pcld.posted_status='1' 
				AND ((pcld.claim_status = '0' 
				AND pcld.del_status='0') OR pcl.void_notify='1')  
				AND pcld.proc_selfpay != '1' AND cft.not_covered != '1' 
				ORDER BY pcld.charge_list_id";
		$res1 = imw_query($q1);
		if($res1 && imw_num_rows($res1)>0){
			$return = array();
			while($rs1 = imw_fetch_assoc($res1)){
				$PCL_id		= $rs1['charge_list_id'];
				$return[$PCL_id][] = $rs1;
			}
		}
		return $return;
	}
	
	/*FILTERING SUBMITTED ENCOUNTERS FROM THE PROVIDED ENCOUNTER LIST*/
	function get_submitted_encounters($arr_PCLdata,$priSec){
		$commaEncIds = $arr_PCLdata['Encounters'];
		$return = false;
		$q1 = "SELECT DISTINCT(encounter_id) as encounterID FROM submited_record 
				WHERE encounter_id IN ($commaEncIds) AND LOWER(Ins_type)='".strtolower($priSec)."'";
		$res1 = imw_query($q1);
		if($res1 && imw_num_rows($res1)>0){
			$return = array();
			while($rs1 = imw_fetch_assoc($res1)){
				$return[] = $rs1['encounterID'];
			}
		}
		return $return;
	}
	
	/*GETTING PATIENT DETAILS FOR PROVIDED LIST*/
	function get_patient_details($arr_PCLdata){
		$commaPTids = $arr_PCLdata['Patients'];
		$return = false;
		$q1 = "SELECT * FROM patient_data WHERE id IN ($commaPTids) ORDER BY lname, fname";
		$res1 = imw_query($q1);
		if($res1 && imw_num_rows($res1)>0){
			$return = array();
			while($rs1 = imw_fetch_assoc($res1)){
				$PT_id		= $rs1['id'];
				$return[$PT_id] = $rs1;
			}
		}
		return $return;
	}
	
	/*GETTING PATIENT INSURNACE DETAILS ACCORDING TO CASE, DOS, TYPE*/
	function get_patient_insurance($case_type_id,$PtId,$type,$DOS){
		$return = false;
		$q = "SELECT id.*, ic.contact_address AS InsCompAddress, ic.City AS InsCompCity, ic.State AS InsCompState, 
		  CONCAT(ic.Zip,ic.zip_ext) AS InsCompZip, ic.phone AS InsCompPhone, ic.id AS InsCompId, 
		  ic.Payer_id AS InsCompPayerId, ic.Payer_id_pro AS InsCompPayerIdPro, ic.BatchFile, 
		  ic.in_house_code, ic.name as InsName, ic.Reciever_id, ic.payer_type, ic.claim_type, ic.ins_type, 
		  ic.Insurance_payment, ic.secondary_payment_method, ic.institutional_type  FROM insurance_data id 
		JOIN insurance_companies ic ON ic.id = id.provider 
		WHERE id.ins_caseid = '$case_type_id' AND id.pid = '$PtId' 
		AND LOWER(id.type) = '".strtolower($type)."' AND id.provider > '0' 
		AND ic.ins_del_status  = '0' AND ic.name NOT LIKE 'self pay' 
		AND DATE_FORMAT(id.effective_date,'%Y-%m-%d') <= '$DOS' 
		AND (id.expiration_date = '0000-00-00 00:00:00' OR DATE_FORMAT(id.expiration_date,'%Y-%m-%d') >= '$DOS') 
		ORDER BY id.actInsComp DESC LIMIT 0,1";
		$res = imw_query($q);
		if($res && imw_num_rows($res)){
			$return = imw_fetch_assoc($res);
		}
		return $return;
	}
	
	/*GETTING PATIENT ICN BY ENCOUNTER ID*/
	function get_patient_icn_byEID($eid,$pt_id,$payer_id){
		$icn = false;
		$q2 = "SELECT e835pd.CLP_payer_claim_control_number AS pat_icn FROM era_835_patient_details e835pd 
			  JOIN era_835_proc_details e835procD ON (e835pd.ERA_patient_details_id=e835procD.ERA_patient_details_id 
			  AND e835procD.REF_prov_identifier LIKE '".$eid."MCR%') 
			  JOIN era_835_details e835d ON (e835pd.835_Era_Id=e835d.835_Era_Id)
			  WHERE e835pd.CLP_claim_submitter_id='$pt_id' AND e835d.REF_provider_ref_id='$payer_id' Limit 0,1";
		$r2 = imw_query($q2);//echo $q2.'<hr>';
		if($r2 && imw_num_rows($r2)==1){
			$rs2 = imw_fetch_assoc($r2);
			$icn = $rs2['pat_icn'];
		}	
		return $icn;
	}
	
	/*TO SET OUTPUT FOR "VIEW CLAIMS" ACTION*/
	function get_merged_claim_data($ARR_PCL, $type='show'){
		$finalDataArr = array(); $ARR_ERRORS = array();
		$CLarray = $ARR_PCL['pcldata'];//pre($CLarray);
		$chargesDetails = $this->get_charges_details($ARR_PCL);
		$submittedEncounters = $this->get_submitted_encounters($ARR_PCL,$_REQUEST['ct']);
		$ARR_MODS = $this->ARR_MODS;
		$type = 'Primary';
		$secondtype = 'Secondary';
		if($_REQUEST['ct']=='Secondary'){
			$type = 'Secondary';
			$secondtype = 'Primary';
		}

		
		for($i=0;$i<count($CLarray);$i++){
			$anesthesia_flag = false;
			if(($this->groupDetails['group_anesthesia']=='1' && $CLarray[$i]['billing_type']=='0') || $CLarray[$i]['billing_type']=='1'){
				$anesthesia_flag = true;
			}
			
			$charge_list_id 			= $CLarray[$i]['ClId'];
			$claim_ctrl_number = '';
			if($type=='Secondary'){
				$claim_ctrl_number			= $CLarray[$i]['claim_ctrl_sec'];
			}else{
				$claim_ctrl_number			= $CLarray[$i]['claim_ctrl_pri'];
			}
			if($type=='Secondary' && (stristr($CLarray[$i]['moaQualifier'],'MA18') || stristr($CLarray[$i]['moaQualifier'],'MA07'))){$finalDataArr[$i]['MA18']	= $CLarray[$i]['moaQualifier'];}
			$finalDataArr[$i]['EncError'] = '';
			$finalDataArr[$i]['icd9_10'] = $CLarray[$i]['enc_icd10'];
			$finalDataArr[$i]['Phy']	= trim($CLarray[$i]['usersLname'].', '.$CLarray[$i]['usersFname'].' '.$CLarray[$i]['usersMname']);
			$finalDataArr[$i]['CLid']	= $charge_list_id;
			$finalDataArr[$i]['PtFunc']	= 'showE("'.$CLarray[$i]['PtId'].'","'.$CLarray[$i]['EncId'].'")';
			if(in_array($CLarray[$i]['EncId'],$submittedEncounters) || $claim_ctrl_number!=''){
				if($CLarray[$i]['void_notify']=='1') $finalDataArr[$i]['ClmFormat']	= '838';
				else  $finalDataArr[$i]['ClmFormat']	= '837';
			}else{
				$finalDataArr[$i]['ClmFormat']	= '831';	
			}
			$finalDataArr[$i]['PtName']	= trim($CLarray[$i]['ptLname'].', '.$CLarray[$i]['ptFname'].' '.$CLarray[$i]['ptMname']).'. - '.$CLarray[$i]['PtId'];
			$finalDataArr[$i]['Eid']	= $CLarray[$i]['EncId'];
			$finalDataArr[$i]['DOS']	= get_date_format($CLarray[$i]['encDOS'],"yyyy-mm-dd");
			$finalDataArr[$i]['InsName']= $CLarray[$i]['in_house_code'];
			
			$arranged_charge_Details = array();
			foreach($chargesDetails[$charge_list_id] as $key=>$val){
				$arranged_charge_Details['procCode'][]		= $val['procCode'];
				$posFacilityId 								= $val['posFacilityId'];
				$place_of_service 							= $val['place_of_service'];
			
				$arranged_charge_Details['cpt'][]			= $val['cpt4_code'];
				$arranged_charge_Details['dx'][]			= $val['diagnosis_id1'];
				if($val['diagnosis_id2']!=''){
					$arranged_charge_Details['dx'][]		= $val['diagnosis_id2'];
				}
				if($val['diagnosis_id3']!=''){
					$arranged_charge_Details['dx'][]		= $val['diagnosis_id3'];
				}
				if($val['diagnosis_id4']!=''){
					$arranged_charge_Details['dx'][]		= $val['diagnosis_id4'];
				}
				if($val['diagnosis_id5']!=''){
					$arranged_charge_Details['dx'][]		= $val['diagnosis_id5'];
				}
				if($val['diagnosis_id6']!=''){
					$arranged_charge_Details['dx'][]		= $val['diagnosis_id6'];
				}
				if($val['diagnosis_id7']!=''){
					$arranged_charge_Details['dx'][]		= $val['diagnosis_id7'];
				}
				if($val['diagnosis_id8']!=''){
					$arranged_charge_Details['dx'][]		= $val['diagnosis_id8'];
				}
				if($val['diagnosis_id9']!=''){
					$arranged_charge_Details['dx'][]		= $val['diagnosis_id9'];
				}
				if($val['diagnosis_id10']!=''){
					$arranged_charge_Details['dx'][]		= $val['diagnosis_id10'];
				}
				if($val['diagnosis_id11']!=''){
					$arranged_charge_Details['dx'][]		= $val['diagnosis_id11'];
				}
				if($val['diagnosis_id12']!=''){
					$arranged_charge_Details['dx'][]		= $val['diagnosis_id12'];
				}
				$arranged_charge_Details['unit'][]			= $val['units'];
				$arranged_charge_Details['charges'][]		= $val['totalAmount'];
				$arranged_charge_Details['mod'][]			= $val['modifier_id1'];
				if($val['modifier_id2']!=''){
					$arranged_charge_Details['mod'][]		= $val['modifier_id2'];	
				}
				if($val['modifier_id3']!=''){
					$arranged_charge_Details['mod'][]		= $val['modifier_id3'];	
				}
				if($val['modifier_id4']!=''){
					$arranged_charge_Details['mod'][]		= $val['modifier_id4'];	
				}
			}
			$ARRfinalCharges = array();
			if(count($arranged_charge_Details['cpt'])>1){
				$tempCpt	= implode(', ',array_unique($arranged_charge_Details['cpt']));
				$ARRfinalCharges['cpt'] = '<span title="'.$tempCpt.'">Multi</span>';
			}else{
				$ARRfinalCharges['cpt'] = $arranged_charge_Details['cpt'];
			}
				$tempDx		= array_unique($arranged_charge_Details['dx']);
			$ARRfinalCharges['dx']		= implode(', ',$tempDx);
			$ARRfinalCharges['units']	= array_sum($arranged_charge_Details['unit']);
			$ARRfinalCharges['charges']	= number_format(array_sum($arranged_charge_Details['charges']),2);
				$tempMOD	= array_unique($arranged_charge_Details['mod']); //unset($tempMOD['0']); unset($tempMOD['']);
				$newMOD = array(); $finalMOD = '';
				foreach($tempMOD as $MODid){if($MODid>0)$newMOD[] = $ARR_MODS[$MODid];}
				$finalMOD = implode(', ',$newMOD);
			$ARRfinalCharges['mod']		= $finalMOD;
			/*--VALIDATION CHECK--*/
			//PROCEDURE CODE
			foreach($arranged_charge_Details['procCode'] as $val){
				if(intval($val)==0){
					$ARR_ERRORS[$charge_list_id][]='Procedure code missing for encounter-ID. '.$finalDataArr[$i]['Eid'];
					break;
				}
			}

			foreach($arranged_charge_Details['cpt'] as $val){
				if(trim($val)=='G8447'){
					$ARR_ERRORS[$charge_list_id][]='Procedure code G8447 is not valid.';
					break;
				}
			}			
			
			if(trim($CLarray[$i]['ptSex'])==''){
				$ARR_ERRORS[$charge_list_id][]='Patient Gender Infomation is Required';
			}
			
			//POS CHECK
			$ARR_pos_details = $this->ARR_POS[$place_of_service];
			if(!$ARR_pos_details){
				$ARR_ERRORS[$charge_list_id][]='POS Required for patient. '.$finalDataArr[$i]['PtName'];
			}
			
			//POS FACILITY CHECK
			if($posFacilityId == '' || $posFacilityId == '0'){$posFacilityId = $CLarray[$i]['ptDefaultFacility'];}
			$posFacilityDetail = $this->ARR_POS_FACILITIES[$posFacilityId];
			if($posFacilityId != 'Home' && !$posFacilityDetail){
				$ARR_ERRORS[$charge_list_id][] = 'POS Facility Required For Encounter-ID. '.$finalDataArr[$i]['Eid'];
			}
			
			//INSURANCE DATA CHECK
			$subscriberDetails1 	= $this->get_patient_insurance($CLarray[$i]['case_type_id'],$CLarray[$i]['PtId'],$type,$CLarray[$i]['encDOS']);
			$payment_method1		= $CLarray[$i]['Insurance_payment'];
			if($subscriberDetails1){//InsCompPayerId//InsCompPayerIdPro//Insurance_payment//secondary_payment_method
				if(!isset($payment_method1) || empty($payment_method1)){
					$payment_method1 = $subscriberDetails1['Insurance_payment'];
				}
				if($type=='Secondary'){$payment_method1 = $subscriberDetails1['secondary_payment_method'];}
				$payer_id1 = $subscriberDetails1['InsCompPayerId'];
				$InsuranceType = 'CI';
				if(intval($subscriberDetails1['claim_type'])==1){
					$InsuranceType = 'MB';
					$finalDataArr[$i]['ClmFormat']	= '831';
					$this->batchType='Professional';
					if(strlen(trim($subscriberDetails1['ins_type']))==2){$InsuranceType = strtoupper(trim($subscriberDetails1['ins_type']));}
				}

				if($this->batchType=='Professional'){$payer_id1 = $CLarray[$i]['InsCompPayerIdPro'];}
				
				/*****NEW CHECK: pt insurnace marked expired afteer posting encounter (expiry date before DOS).**/
				if(strtolower($type)=='primary'){
					if($CLarray[$i]['PriIns'] != $subscriberDetails1['provider']){
						$ARR_ERRORS[$charge_list_id][] = "Primary Insurance Carrier mismatch for encounter.";
					}
				}else if(strtolower($type)=='secondary'){
					if($CLarray[$i]['SecIns'] != $subscriberDetails1['provider']){
						$ARR_ERRORS[$charge_list_id][] = "Secondary Insurance Carrier mismatch for encounter.";
					}
				}
				/*****NEW CHECK: end**/
				
				if($payment_method1 != "Electronics"){
					$ARR_ERRORS[$charge_list_id][] = $type." Insurance Carrier is not set for Electronics File Claims.";
				}
				if(trim($payer_id1) == ''){
					$ARR_ERRORS[$charge_list_id][] = $type." Insurance Carrier Payer Id is Required.";
				}
				else if(trim($payer_id1) != '' && strlen(trim($payer_id1)) < 3){
					$ARR_ERRORS[$charge_list_id][] = $type." Insurance Carrier Payer Id minimum length violation.";
				}
				if($subscriberDetails1['InsCompAddress'] == '' || $subscriberDetails1['InsCompCity'] == '' || $subscriberDetails1['InsCompState'] == '' || $subscriberDetails1['InsCompZip'] == ''){
					$ARR_ERRORS[$charge_list_id][] = $type." Insurance Carrier Address information is required.";
				}
				/*
				//THIS VALIDATION COMMENTED AFTER DISCUSSION WITH AK on 11-april-2018.
				if($this->groupDetails['group_institution']=='0' && ($subscriberDetails1['BatchFile'] == '1' && $subscriberDetails1['institutional_type'] != 'INST_PROF')){//BatchFile
					$ARR_ERRORS[$charge_list_id][] = $type." Insurance Carrier set to 837i, but Group is professional.";
				}*/
				
				$ref_phy_not_required = $CLarray[$i]['reff_phy_nr'];
				$reffPhyscianId = $CLarray[$i]['reff_phy_id'];
				if($reffPhyscianId == 0 || $reffPhyscianId == ''){
					$reffPhyscianId 	= isset($CLarray[$i]['primaryProviderId']) ? $CLarray[$i]['primaryProviderId'] : '';
					$reffPhysicianLname = isset($CLarray[$i]['usersLname']) ? $CLarray[$i]['usersLname'] : '';
					$reffPhysicianFname = isset($CLarray[$i]['usersFname']) ? $CLarray[$i]['usersFname'] : '';
					$reffPhysicianMname = isset($CLarray[$i]['usersMname']) ? $CLarray[$i]['usersMname'] : '';
					$reffNPI 			= isset($CLarray[$i]['userNpi']) ? $CLarray[$i]['userNpi'] : '';
					$reffTaxonomy 		= isset($CLarray[$i]['usersTaxonomyId']) ? $CLarray[$i]['usersTaxonomyId'] : '';
				}
				else{
					$reffDetail 		= $this->get_ref_phy_details($reffPhyscianId);
					$reffPhysicianLname = $reffDetail['LastName'];
					$reffPhysicianFname = $reffDetail['FirstName'];
					$reffPhysicianMname = substr($reffDetail['MiddleName'],0,1);
					$reffNPI 			= $reffDetail['NPI'];
					$reffTaxonomy 		= $reffDetail['Texonomy'];
				}
				
				if(trim($CLarray[$i]['userNpi']) == ''){
					$ARR_ERRORS[$charge_list_id][] = 'Rendering Physician NPI # is Required.';
				}
				if(trim($CLarray[$i]['usersTaxonomyId']) == '' && (isset($invalidClaim) && $invalidClaim == false)){
					$ARR_ERRORS[$charge_list_id][] = 'Rendering Physician Taxonomy # is Required.';
				}
				if($anesthesia_flag){
					$tempp_admit_date 	= explode(' ',$CLarray[$i]['admit_date']);
					$admit_date 		= $tempp_admit_date[0];
					$anes_start_time 	= $tempp_admit_date[1];
					if(trim($anes_start_time) == '' || $anes_start_time=='00:00:00'){
						$ARR_ERRORS[$charge_list_id][] = 'Anesthesia Start Time required For Encounter-ID. '.$finalDataArr[$i]['Eid'];
					}
					
					$tempp_disch_date = explode(' ',$CLarray[$i]['disch_date']);
					$disch_date = $tempp_disch_date[0];
					$anes_stop_time = $tempp_disch_date[1];
					if(trim($anes_stop_time) == '' || $anes_stop_time=='00:00:00'){
						$ARR_ERRORS[$charge_list_id][] = 'Anesthesia Stop Time required For Encounter-ID. '.$finalDataArr[$i]['Eid'];
					}					
				}
				if($ref_phy_not_required=='0' && isset($reffDetail) && $reffDetail==false){
					$ARR_ERRORS[$charge_list_id][] = 'Referring Physician is Required.';
				}
				if($ref_phy_not_required=='0' && trim($reffNPI) == ''){
					$ARR_ERRORS[$charge_list_id][] = 'Referring Physician NPI # is Required.';
				}

				if(trim($subscriberDetails1['policy_number']) == ''){
					$ARR_ERRORS[$charge_list_id][] = $type." Insurance Carrier Policy # is Required.";
				}
				
				if($subscriberDetails1['subscriber_street'] == '0'){$subscriberDetails1['subscriber_street'] = '';}					
				if(trim($subscriberDetails1['subscriber_street']) == ''){
					$ARR_ERRORS[$charge_list_id][] = $type." Subscriber Address is Required.";
				}
				if(trim($subscriberDetails1['subscriber_postal_code']) == ''){
					$ARR_ERRORS[$charge_list_id][] = $type." Subscriber Postal Code is Required.";
				}
				if(trim($subscriberDetails1['subscriber_state']) == ''){
					$ARR_ERRORS[$charge_list_id][] = $type." Subscriber State is Required.";
				}
				if(trim($subscriberDetails1['subscriber_city']) == ''){
					$ARR_ERRORS[$charge_list_id][] = $type." Subscriber City is Required.";
				}
				if(trim($subscriberDetails1['subscriber_lname']) == ''){
					$ARR_ERRORS[$charge_list_id][] = $type." Subscriber Last Name is Required.";
				}
				if(trim($subscriberDetails1['subscriber_fname']) == ''){
					$ARR_ERRORS[$charge_list_id][] = $type." Subscriber First Name is Required.";
				}
				if(trim($subscriberDetails1['subscriber_sex']) == ''){
					$ARR_ERRORS[$charge_list_id][] = $type." Subscriber Gender Information is Required.";
				}
				if(trim($subscriberDetails1['subscriber_DOB']) == '0000-00-00'){
					$ARR_ERRORS[$charge_list_id][] = $type." Subscriber Date of Birth is Required.";
				}
				if($InsuranceType=='MB' && strtolower(trim($subscriberDetails1['subscriber_relationship'])) != 'self'){
					$ARR_ERRORS[$charge_list_id][] = $type." Subscriber-Patient Relationship must be SELF (for Medicare Insurance).";
				}
				
				$subscriberDetails2 = $this->get_patient_insurance($CLarray[$i]['case_type_id'],$CLarray[$i]['PtId'],$secondtype,$CLarray[$i]['encDOS']);
				if($type!='Secondary' && ($subscriberDetails2['InsCompId'] != $CLarray[$i]['SecIns'])){
					$subscriberDetails2 = false;
					unset($subscriberDetails2);
				}
				if(!isset($subscriberDetails2)){
					if($type=='Secondary'){
						$ARR_ERRORS[$charge_list_id][] = $secondtype." Insurance Provider Details not found for Encounter-ID. ".$finalDataArr[$i]['Eid'];
					}
				}else{
					$payment_method2 = $subscriberDetails2['secondary_payment_method'];
					if($type=='Secondary'){$payment_method2 = $subscriberDetails2['Insurance_payment'];}
					$payer_id2 = $subscriberDetails2['InsCompPayerId'];
					if($this->batchType=='Professional'){$payer_id2 = $subscriberDetails2['InsCompPayerIdPro'];}
				
					if(trim($payer_id2) == ''){
						$ARR_ERRORS[$charge_list_id][] = $secondtype." Insurance Carrier Payer Id is Required.";
					}
					else if(trim($payer_id2) != '' && strlen(trim($payer_id2)) < 3){
						$ARR_ERRORS[$charge_list_id][] = $secondtype." Insurance Carrier Payer Id minimum length violation.";
					}
					if($subscriberDetails2['InsCompAddress'] == '' || $subscriberDetails2['InsCompCity'] == '' || $subscriberDetails2['InsCompState'] == '' || $subscriberDetails2['InsCompZip'] == ''){
						$ARR_ERRORS[$charge_list_id][] = $secondtype." Insurance Carrier Address information is required.";
					}
					
					if(trim($subscriberDetails2['policy_number']) == ''){
						$ARR_ERRORS[$charge_list_id][] = $secondtype." Insurance Carrier Policy # is Required.";
					}
					
					if($subscriberDetails1['subscriber_street'] == '0'){$subscriberDetails1['subscriber_street'] = '';}					
					if(trim($subscriberDetails2['subscriber_street']) == ''){
						$ARR_ERRORS[$charge_list_id][] = $secondtype." Subscriber Address is Required.";
					}
					if(trim($subscriberDetails2['subscriber_postal_code']) == ''){
						$ARR_ERRORS[$charge_list_id][] = $secondtype." Subscriber Postal Code is Required.";
					}
					if(trim($subscriberDetails2['subscriber_state']) == ''){
						$ARR_ERRORS[$charge_list_id][] = $secondtype." Subscriber State is Required.";
					}
					if(trim($subscriberDetails2['subscriber_city']) == ''){
						$ARR_ERRORS[$charge_list_id][] = $secondtype." Subscriber City is Required.";
					}
					if(trim($subscriberDetails2['subscriber_lname']) == ''){
						$ARR_ERRORS[$charge_list_id][] = $secondtype." Subscriber Last Name is Required.";
					}
					if(trim($subscriberDetails2['subscriber_fname']) == ''){
						$ARR_ERRORS[$charge_list_id][] = $secondtype." Subscriber First Name is Required.";
					}
					if(trim($subscriberDetails2['subscriber_sex']) == ''){
						$ARR_ERRORS[$charge_list_id][] = $secondtype." Subscriber Gender Information is Required.";
					}
					if(trim($subscriberDetails2['subscriber_DOB']) == '0000-00-00'){
						$ARR_ERRORS[$charge_list_id][] = $secondtype." Subscriber Date of Birth is Required.";
					}
				}
			}else{
				$ARR_ERRORS[$charge_list_id][] = $type." Insurance Provider Details not found for Encounter-ID. ".$finalDataArr[$i]['Eid'];
			}
			
			
			
			/*--VALIDATION END--*/			
			$finalDataArr[$i]['ChargesDetails']	= $ARRfinalCharges;
			if($claim_ctrl_number==''){
				$claim_ctrl_number = $this->get_patient_icn_byEID($finalDataArr[$i]['Eid'],$CLarray[$i]['PtId'],$payer_id1);
			}
			$finalDataArr[$i]['claimNum']	= $claim_ctrl_number;

			if(isset($ARR_ERRORS[$charge_list_id]) && is_array($ARR_ERRORS[$charge_list_id]) && count($ARR_ERRORS[$charge_list_id])>0){
				$finalDataArr[$i]['EncError']	= implode('<br>&bull; ',$ARR_ERRORS[$charge_list_id]);
			}
		}//pre($finalDataArr);
		return $finalDataArr;
	}
	
	/*THIS FUNCTION IS IN-COMPLETE, NOT IN USE.*/
	function get_interchange_data($ARR_PCL){ 
		global $billing_global_taxonomy_number;
		$nowDate = date('Ymd'); $nowTime = date('hi');
		$ARR_Interchange_Data = array();
		$submitter_id = $this->groupDetails['sub_id']; $submitter_id = $this->padd_string($submitter_id,15,' ','suffix');
		$receiver_id = $this->groupDetails['rec_id']; $receiver_id = $this->padd_string($receiver_id,15,' ','suffix');
		$HQ_BillingLocation = intval($this->HQFacility['billing_location']);
		$interchange_control_standard_id = '^';
		if($this->clearingHouse=='Emdeon' && $this->batchType=='Professional'){$interchange_control_standard_id = 'U';}
		else if($this->clearingHouse=='Medicare'){$interchange_control_standard_id = '|';}		
		
		$interchange_control_version_number = '00501';		
		$interchange_unique_headers = $this->get_unique_headers();
		$new_interchange_number		= $interchange_unique_headers['new_interchange_num'];
		$interchange_control_number = $interchange_unique_headers['interchange_control_num'];
		$transaction_set_controller = $interchange_unique_headers['transaction_set_unique_control'];
		$header_control_identifier  = $interchange_unique_headers['header_control_identifier'];
		$implementation_convention_reference = '005010X222A1';
		if($this->batchType=='Institutional'){$implementation_convention_reference = '005010X222A2';}
		
		//INTERCHANGE CONTROL HEADER
		$ARR_Interchange_Data['ICH']['ISA']['01'] = '00';
		$ARR_Interchange_Data['ICH']['ISA']['02'] = '          ';	//10 SPACES.
		$ARR_Interchange_Data['ICH']['ISA']['03'] = '00';
		$ARR_Interchange_Data['ICH']['ISA']['04'] = '          ';	//10 SPACES.
		$ARR_Interchange_Data['ICH']['ISA']['05'] = 'ZZ';
		$ARR_Interchange_Data['ICH']['ISA']['06'] = $submitter_id;	//INTERCHANGE SENDER ID
		$ARR_Interchange_Data['ICH']['ISA']['07'] = $this->clearingHouse=='Navicure' ? '27' : 'ZZ';
		$ARR_Interchange_Data['ICH']['ISA']['08'] = $receiver_id;	//INTERCHANGE SENDER ID
		$ARR_Interchange_Data['ICH']['ISA']['09'] = $nowDate;
		$ARR_Interchange_Data['ICH']['ISA']['10'] = $nowTime;
		$ARR_Interchange_Data['ICH']['ISA']['11'] = $interchange_control_standard_id;
		$ARR_Interchange_Data['ICH']['ISA']['12'] = $interchange_control_version_number;
		$ARR_Interchange_Data['ICH']['ISA']['13'] = $interchange_control_number;
		$ARR_Interchange_Data['ICH']['ISA']['14'] = '1';
		$ARR_Interchange_Data['ICH']['ISA']['15'] = $this->batchMode;
		$ARR_Interchange_Data['ICH']['ISA']['16'] = ':';
		
		//FUNCTIONAL GROUP HEADER
		$ARR_Interchange_Data['FGH']['GS']['01']	= 'HC';
		$ARR_Interchange_Data['FGH']['GS']['02']	= $submitter_id;
		$ARR_Interchange_Data['FGH']['GS']['03']	= $receiver_id;
		$ARR_Interchange_Data['FGH']['GS']['04']	= $nowDate;
		$ARR_Interchange_Data['FGH']['GS']['05']	= $nowTime;
		$ARR_Interchange_Data['FGH']['GS']['06']	= $header_control_identifier;
		$ARR_Interchange_Data['FGH']['GS']['07']	= 'X';
		$ARR_Interchange_Data['FGH']['GS']['08']	= '005010X222A1';
		
		//TRANSACTION SET HEADER
		$ARR_Interchange_Data['TSH']['ST']['01']	= '837';
		$ARR_Interchange_Data['TSH']['ST']['02']	= $transaction_set_controller;
		$ARR_Interchange_Data['TSH']['ST']['03']	= $receiver_id;

		//BEGINNING OF HIERARCHICAL TRANSACTION
		$ARR_Interchange_Data['BOH']['BHT']['01']	= '0019';
		$ARR_Interchange_Data['BOH']['BHT']['02']	= '00';
		$ARR_Interchange_Data['BOH']['BHT']['03']	= $new_interchange_number;
		$ARR_Interchange_Data['BOH']['BHT']['04']	= $nowDate;
		$ARR_Interchange_Data['BOH']['BHT']['05']	= $nowTime;
		$ARR_Interchange_Data['BOH']['BHT']['06']	= 'CH';
		
		//LOOP 1000A (SUBMITTER NAME & CONTACT INFORMATION)
		//'NM1*41*2*'.substr($groupDetails->name,0,50).'*****46*'.$submitterId.'~';
		$ARR_Interchange_Data['1000A']['NM1']['01']	= '41';
		$ARR_Interchange_Data['1000A']['NM1']['02']	= '2';
		$ARR_Interchange_Data['1000A']['NM1']['03']	= $this->dataCleanup(substr($this->groupDetails['name'],0,60));
		$ARR_Interchange_Data['1000A']['NM1']['04']	= '';
		$ARR_Interchange_Data['1000A']['NM1']['05']	= '';
		$ARR_Interchange_Data['1000A']['NM1']['06']	= '';
		$ARR_Interchange_Data['1000A']['NM1']['07']	= '';
		$ARR_Interchange_Data['1000A']['NM1']['08']	= '46';
		$ARR_Interchange_Data['1000A']['NM1']['09']	= $submitter_id;

		//SUBMITTER EDI CONTACT INFORMATION
		$ARR_Interchange_Data['1000A']['PER']['01']	= 'IC';
		$ARR_Interchange_Data['1000A']['PER']['02']	= ($HQ_BillingLocation == 1) ? $this->dataCleanup($this->HQFacility['billing_attention']) : $this->dataCleanup($this->groupDetails['Contact_Name']);
		$ARR_Interchange_Data['1000A']['PER']['03']	= 'TE';
		$ARR_Interchange_Data['1000A']['PER']['04']	= ($HQ_BillingLocation == 1) ? $this->dataCleanup($this->HQFacility['phone'],'phone') : $this->dataCleanup($this->groupDetails['group_Telephone'],'phone');;

		//LOOP 1000B (RECEIVER NAME)
		$ARR_Interchange_Data['1000B']['NM1']['01']	= '40';
		$ARR_Interchange_Data['1000B']['NM1']['02']	= '2';
		$ARR_Interchange_Data['1000B']['NM1']['03']	= $this->dataCleanup($this->copayPolicies['Name']);
		$ARR_Interchange_Data['1000B']['NM1']['04']	= '';
		$ARR_Interchange_Data['1000B']['NM1']['05']	= '';
		$ARR_Interchange_Data['1000B']['NM1']['06']	= '';
		$ARR_Interchange_Data['1000B']['NM1']['07']	= '';
		$ARR_Interchange_Data['1000B']['NM1']['08']	= '46';
		$ARR_Interchange_Data['1000B']['NM1']['09']	= $receiver_id;

		//LOOP 2000A (BILLING PROVIDER HIERARCHICAL LEVEL)
		$subscriberId = 1;
		$ARR_Interchange_Data['2000A']['HL']['01']	= $subscriberId;
		$ARR_Interchange_Data['2000A']['HL']['02']	= '';
		$ARR_Interchange_Data['2000A']['HL']['03']	= '20';
		$ARR_Interchange_Data['2000A']['HL']['04']	= '1';
		
		//LOOP 2000A (BILLING PROVIDER SPECIALTY INFORMATION)
		if(trim($billing_global_taxonomy_number) != '' && $billing_global_server_name != 'gewirtz'){
			$ARR_Interchange_Data['2000A']['PRV']['01']	= 'BI';
			$ARR_Interchange_Data['2000A']['PRV']['02']	= 'PXC';
			$ARR_Interchange_Data['2000A']['PRV']['03']	= trim(substr($this->dataCleanup($billing_global_taxonomy_number),0,50));
		}		
		
		//LOOP 2010AA (BILLING PROVIDER NAME)
		$ARR_Interchange_Data['2010AA']['NM1']['01']	= '85';
		$ARR_Interchange_Data['2010AA']['NM1']['02']	= '2';
		$ARR_Interchange_Data['2010AA']['NM1']['03']	= $this->dataCleanup(substr($this->groupDetails['name'],0,60));
		$ARR_Interchange_Data['2010AA']['NM1']['04']	= '';
		$ARR_Interchange_Data['2010AA']['NM1']['05']	= '';
		$ARR_Interchange_Data['2010AA']['NM1']['06']	= '';
		$ARR_Interchange_Data['2010AA']['NM1']['07']	= '';
		$ARR_Interchange_Data['2010AA']['NM1']['08']	= 'XX';
		$ARR_Interchange_Data['2010AA']['NM1']['09']	= $this->dataCleanup($this->groupDetails['group_NPI'],'phone');						
		
		//LOOP 2010AA (BILLING PROVIDER ADDRESS)
		if($HQ_BillingLocation==1){
			$billing_location_street = trim($this->HQFacility['street']);
			$billing_location_city 	 = $this->HQFacility['city'];
			$billing_location_state  = $this->HQFacility['state'];
			$billing_location_zip    = $this->HQFacility['postal_code'].$this->HQFacility['zip_ext'];
		}else{
			$billing_location_street = $this->groupDetails['group_Address1'].' '.$this->groupDetails['group_Address2'];
			$billing_location_city 	 = $this->groupDetails['group_City'];
			$billing_location_state  = $this->correctStateName(trim($this->groupDetails['group_State']));
			$billing_location_zip    = $this->groupDetails['group_Zip'].$this->groupDetails['zip_ext'];
		}
		if($billing_location_street == ''){$billing_location_street = $billing_location_city;}
		$ARR_Interchange_Data['2010AA']['N3']['01']	= trim(substr($this->dataCleanup($billing_location_street),0,55));
		$ARR_Interchange_Data['2010AA']['N4']['01']	= trim(substr($this->dataCleanup($billing_location_city),0,30));
		$ARR_Interchange_Data['2010AA']['N4']['02']	= trim($this->dataCleanup($billing_location_state));
		$ARR_Interchange_Data['2010AA']['N4']['03']	= substr($this->dataCleanup($billing_location_zip,'phone'),0,15);
		
		//LOOP 2010AA (BILLING PROVIDER TAX IDENTIFICATION)
		$ARR_Interchange_Data['2010AA']['REF']['01']	= 'EI';
		$ARR_Interchange_Data['2010AA']['REF']['02']	= $this->dataCleanup($this->groupDetails['group_Federal_EIN'],'phone');
		
		//SITE REFERENCE TO GET REPORTS
		if($this->clearingHouse=='Emdeon' && constant('DEFAULT_PRODUCT') == 'imwemr'){
			$ARR_Interchange_Data['2010AA']['REF2']['01']	= 'G5';
			$ARR_Interchange_Data['2010AA']['REF2']['02']	= $this->dataCleanup($this->groupDetails['site_id']);
		}
		
		
		//$this->ARR_MODS;
		//pre($ARR_PCL);

		return $ARR_Interchange_Data;
		
	}
	
	/*CLEANING UP DATE AND PHONE*/
	function dataCleanup($str,$type=''){
		//preg_replace('/[^a-zA-Z0-9_*\- ~.:]/',' ',$str);
		if($type=='phone'){
			return trim(preg_replace('/[^0-9]/','',$str));
		}
		return trim(preg_replace('/[^a-zA-Z0-9_\- .]/','',$str));
	}
	
	/*GET ALL MODIFIERS*/
	function get_all_modifiers(){
		$return = false;
		$q = "SELECT mod_prac_code, modifiers_id FROM modifiers_tbl";
		$res = imw_query($q);
		if($res && imw_num_rows($res)>0){
			$return = array();
			while($rs = imw_fetch_assoc($res)){
				$mod_id = $rs['modifiers_id'];
				$return[$mod_id] = $rs['mod_prac_code'];
			}
		}
		return $return;
	}
	
	/*TO FIX A BUG IN SOME STATE NAMES*/
	function correctStateName($zipCodeVal){
		$code_name_arr = array("/NEW JERSY/","/NEW YORK/");
		$new_code_arr = array("NJ","NY");
		$zipCodeVal = strtoupper(trim($zipCodeVal));
		$zipCodeVal = preg_replace($code_name_arr,$new_code_arr,$zipCodeVal);
		$zipCodeVal = preg_replace('/[^A-Z]/',"",$zipCodeVal);
		return $zipCodeVal;
	}
	
	/*TO PADD A STRING/WORD WITH SPECIFI CHARACTERS*/
	function padd_string($str,$totLength,$paddwith=' ',$prefixOrSuffix = 'prefix'){
		$diff = $totLength - strlen($str);
		$padd = '';
		if($diff > 0){
			$padd = str_repeat($paddwith,$diff);
		}
		if($prefixOrSuffix=='prefix'){return $padd.$str;}else{return $str.$padd;}
	}
	
	/*GETTING NEW UNIQUE HEADERS WHILE CREATING NEW RECORD FOR BATCH FILE, CLAIM STATUS REQUEST, PRE-AUTH ETC.*/
	function get_unique_headers($type=837){
		$ARR_headerNumbers = array();
		$res1 = imw_query("SELECT max(Interchange_control)+1 as NewInterchangeNum FROM batch_file_submitte");
		if($type==276){
			$res1 = imw_query("SELECT if(MAX(id) IS NULL,0,MAX(id))+1  as NewInterchangeNum FROM claim_status_enquiry");	
		}
		if($type==278){
			$res1 = imw_query("SELECT if(MAX(id) IS NULL,0,MAX(id))+1  as NewInterchangeNum FROM claim_pre_auth");	
		}
		if($res1 && imw_num_rows($res1)==1){
			$rs1 = imw_fetch_assoc($res1);
			if($type!='276' &&  $type!='278' && isset($_SESSION['interchangecontrolnumber']) && $_SESSION['interchangecontrolnumber']!=''){
				$rs1['NewInterchangeNum'] = $_SESSION['interchangecontrolnumber'];
				$Interchange_controls = $rs1['NewInterchangeNum'];
			}else{
				$Interchange_controls = $rs1['NewInterchangeNum'];
				$_SESSION['interchangecontrolnumber'] = $Interchange_controls;
			}
			$InterchangeControlNumber = $this->padd_string($Interchange_controls,9,'0','prefix');
			
			$set_number = $Interchange_controls * 100000;
			$set_number = substr($set_number,0,7);
			$set_number = $set_number + $Interchange_controls;
			$Transaction_set_unique_control = $set_number;
			$header_control_identifier = $set_number;
			$ARR_headerNumbers['new_id_num'] 						= $rs1['NewInterchangeNum'];
			$ARR_headerNumbers['new_interchange_num'] 				= $Interchange_controls;
			$ARR_headerNumbers['interchange_control_num'] 			= $InterchangeControlNumber;
			$ARR_headerNumbers['transaction_set_unique_control']	= $Transaction_set_unique_control;
			if($type==276 || $type==278){
				$ARR_headerNumbers['transaction_set_unique_control']	= $this->padd_string($Interchange_controls,4,'0','prefix');
			}
			$ARR_headerNumbers['header_control_identifier'] 		= $header_control_identifier;
			return $ARR_headerNumbers;			
		}else{
			return false;
		}
	}
	
	/*FETCH EMDEON REPORT BY ID*/
	function getEmdeonReport($id){
		$q = "SELECT * FROM emdeon_reports 
				WHERE emdeon_report_id = '$id' LIMIT 1";
		$res = imw_query($q);
		if($res && imw_num_rows($res)==1){
			return imw_fetch_assoc($res);
		}
		return false;
	}
	
	/*SETTING CLEARING HOUSE REPORT STATUS (READ/UNREAD)*/
	function MarkReportStatus($clHouse,$rptId,$status){
		$q1 = $q2 = "";
		switch($clHouse){
			case 'comm':
				$q1 = "SELECT read_status FROM emdeon_reports WHERE emdeon_report_id = '".$rptId."'";
				$q2 = "UPDATE emdeon_reports SET read_status = '".$status."' WHERE emdeon_report_id = '".$rptId."'";
				break;
			case 'medi':
				$q1 = "SELECT imedic_process as read_status FROM vision_share_batch_receive_list WHERE id = '".$rptId."'";
				$q2 = "UPDATE vision_share_batch_receive_list SET imedic_process = '".$status."' WHERE id = '".$rptId."'";
				break;
		}
		if(!empty($q1)){
			$res1 = imw_query($q1);
			if($res1 && imw_num_rows($res1)==1){
				$rs1 = imw_fetch_assoc($res1);
				if($rs1['read_status']==0){
					if(!empty($q2)){
						$res2 = imw_query($q2);
						if($res2){$this->MarkSubmitted = true;}
					}
				}else{
					$this->MarkSubmitted = false;
				}
			}
		}
	}
	
	/*MARK ENCOUTERS ARE SUBMITTED (AFTER RECEIVING 997 FOR BATCH FILE)*/
	function MarkSubmittedRecords($batchId,$report_date,$Bstatus,$raw_report_data){
		$file_status = 0;
		if($Bstatus=='A'){$file_status = 1;}
		if($Bstatus=='E'){$file_status = 2;}
		$batchDetails = $this->getBatchFileDetails($batchId,Batch_file_submitte_id);
		$report_date = $this->date_for_db($report_date);
		if($this->MarkSubmitted && ($Bstatus=='A' || $Bstatus=='E')){
			$commaEncounters = $batchDetails['encounter_id'];
			$arrEncounters = explode(',',$commaEncounters);
			foreach($arrEncounters as $encounter){
				$resPCL = imw_query("SELECT * FROM patient_charge_list WHERE del_status='0' AND encounter_id = '".$encounter."'");
				if($resPCL && imw_num_rows($resPCL) == 1){
					$rsPCL = imw_fetch_assoc($resPCL);
					if($batchDetails['ins_comp'] == 'primary'){
						$insCompId = $rsPCL['primaryInsuranceCoId'];
					}else{
						$insCompId = $rsPCL['secondaryInsuranceCoId'];
					}
					$submitedRec['submited_date'] = $fileDateVal;
					$SR_insertQ = "INSERT INTO submited_record (encounter_id, patient_id, Ins_type, Ins_company_id, posted_amount, operator_id, submited_date) 
								   values('".$encounter."','".$rsPCL['patient_id']."','".$batchDetails['ins_comp']."','".$insCompId."','".$rsPCL['postedAmount']."', '".$_SESSION['authUserID']."','".$report_date."')";
					$SR_result = imw_query($SR_insertQ);
				}
			}
			
		}
		
		$setNumber	= $batchDetails['Transaction_set_unique_control'];
		$e997_Q = "INSERT INTO electronic_997_file (File_name,set_number_id,file_data,file_upload_date,operator_id,file_status) 
				   values ('','".$setNumber."','".$raw_report_data."','".$report_date."','".$_SESSION['authUserID']."','".$file_status."')";
		$e997_res = imw_query($e997_Q);
		
		$bfs_q 		= "UPDATE batch_file_submitte SET status='".$file_status."' WHERE Batch_file_submitte_id='".$batchId."' LIMIT 1";
		$bfs_res	= imw_query($bfs_q);
		
	}
	
	/*TO SHOW HUMAN READABLE 997*/
	function EDI2Human($report_id,$report_type){
		switch($report_type){
			case 'comm':
				//COMMERCIAL / EMDEON
				$res1 = imw_query("SELECT * FROM emdeon_reports WHERE emdeon_report_id = '$report_id' LIMIT 0,1");
				if($res1 && imw_num_rows($res1)==1){
					$rs1 = imw_fetch_assoc($res1);
					$report_data = $rs1['report_data'];
					if(strpos(strtoupper($report_data), "ST*997*") !== false || strpos(strtoupper($report_data), "ST*999*") !== false){
						$this->MarkReportStatus($report_type,$report_id,1);
						$arr_ReportDetails = $this->get997toArray($report_data);
						return $arr_ReportDetails;
					}else{
						return false;
					}
				}
				break;
			case 'medi':
				//MEDICARE / VISIONSHARE
				$res1 = imw_query("SELECT vs_file_data AS report_data FROM vision_share_batch_receive_list WHERE id = '".$report_id."' LIMIT 1");
				if($res1 && imw_num_rows($res1)==1){
					$rs1 = imw_fetch_assoc($res1);
					$report_data = $rs1['report_data'];
					if(strpos(strtoupper($report_data), "ST*997*") !== false || strpos(strtoupper($report_data), "ST*999*") !== false){
						$this->MarkReportStatus($report_type,$report_id,1);
						$arr_ReportDetails = $this->get997toArray($report_data);
						return $arr_ReportDetails;
					}else{
						return false;
					}									
				}				
				break;
		}
	}
	
	/*USED IN edi2human()*/
	function get997toArray($report_data1){
		if(substr(trim($report_data1),0,1) == 'R'){
				$report_data_arr = array();
				$report_data_arr = explode("\n",$report_data1);
				for($i=0;$i<=count($report_data_arr);$i++){
					$report_data_line = trim($report_data_arr[$i]);
					if(substr($report_data_line,0,1) == 'R'){
						$space_place = strpos($report_data_line," ");
						if($space_place <= 50 && $space_place > 25){
							$replace_character = substr($report_data_line,0,$space_place);	
							$report_data_line  = str_replace($replace_character," ",$report_data_line);
						}
					}
					$report_data_line = trim($report_data_line);
					if(substr($report_data_line,0,1) == 'L'){
						$space_place = strpos($report_data_line," ");
						if($space_place <= 10 && $space_place > 3){
							$replace_character = substr($report_data_line,0,$space_place);	
							$report_data_line  = str_replace($replace_character," ",$report_data_line);
						}
					}
					$report_data .= trim($report_data_line);
				} 
		}else if(substr(trim($report_data1),0,20) == 'SA*00*          *00*'){
			/**THIS is case encountered for some 997 reports in year 2016 (from TEC firstly)**/
			$report_data = str_ireplace('SA*00*          *00*','ISA*00*          *00*',$report_data1);
			
		}else{$report_data = $report_data1;}
		
		$ReportsARR = explode('ISA*',''.$report_data);
		$arr_segments = array();
		for($i=0;$i<count($ReportsARR);$i++){
			$report_data = "";
			if(trim($ReportsARR[$i])==''){continue;}
			$report_data = trim('ISA*'.$ReportsARR[$i]);
			$arr_segmentLines = explode('~',$report_data);
			$ln = 0;
			foreach($arr_segmentLines as $segment){
				$temp_segments = explode('*',$segment);
				$segName = $temp_segments[0];
				unset($temp_segments[0]);
				if($segName!=''){
					$arr_segments[$i]['segments'][$ln][$segName] 	= $temp_segments;
					$arr_segments[$i]['ReportData'] = $report_data;
					$ln++;
				}
			}
		}
		return $arr_segments;
	}
	
	/*USED IN edi2human()*/
	function Render_EDI_DataARR($EDI_data_arr){
		$FinalData = array();
		$report_data = $EDI_data_arr['ReportData'];
		$arr_segments = $EDI_data_arr['segments'];
		for($n=0;$n<count($arr_segments);$n++){
			$curSegArr = $arr_segments[$n];
			//pre($curSegArr);echo '<hr>';
			foreach($curSegArr as $key=>$valArr){
				if($key=='AK1'){$AK1 = $valArr;}
				else if($key=='AK2'){$AK2 = $valArr;}
				else if($key=='AK5'){$AK5 = $valArr;}
				else if($key=='AK9'){$AK9 = $valArr;}
			}
		}
		//GETTING HEADER CONTROL IDENTIFIER
		//$AK1 = $arr_segments['AK1']; $AK2 = $arr_segments['AK2'];
		$headerControlIdnetifier = $AK1[2]!='' ? $AK1[2] : ($AK2[2]!='' ? $AK2[2] : '');
		
		//GETTING BATCH STATUS (ACCEPTED OR REJECTED)
		//$AK5 = $arr_segments['AK5']; $AK9 = $arr_segments['AK9'];
		$batchStatus = $AK5[1]!='' ? $AK5[1] : ($AK9[1]!='' ? $AK9[1] : '');
		//pre($arr_segments,1);
		$FinalData['BatchControlIden']	= preg_replace('/[^0-9]/','',$headerControlIdnetifier);
		$FinalData['BatchStatus']		= trim($batchStatus);
		$FinalData['Submitter']			= $arr_segments['0']['ISA']['6'];
		$FinalData['Receiver']			= $arr_segments['0']['ISA']['8'];
		$FinalData['ReportDate']		= '20'.$arr_segments['0']['ISA']['9'];
		$FinalData['Segments']			= $arr_segments;
		return $FinalData;	
	}
	
	/*MAKING LINES OF 837 EDI*/
	function get837toArray($batch_data1){
		$batch_data = array();
		$arr_segmentLines = explode('~',$batch_data1);
		$arr_segments = array();
		$i = 0;
		foreach($arr_segmentLines as $segment){
			$temp_segments = explode('*',$segment);
			$arr_segments[$i] = $temp_segments;
			$i++;
		}
		return $arr_segments;	
	}
	
	/*GETTING DETAILS OF BATCH FILE (ID WISE)*/
	function getBatchFileDetails($val,$col='Batch_file_submitte_id'){
		$row = false;
		$q = "SELECT * FROM batch_file_submitte WHERE $col  = '$val' LIMIT 0,1";
		$r=imw_query($q);
		if($r && imw_num_rows($r)==1){
			$row=imw_fetch_assoc($r);
		}
		return $row;		
	}
	
	/*USED IN EDI2HUMAN*/
	function FilterErrors($arrSegments){
		$ARR_Errors = array();	$i = 0;
		foreach($arrSegments as $Segments){
			foreach($Segments as $Segment=>$Values){
				//echo $Segment.'=>'.$Values.'<br>';
				if(in_array($Segment,array('IK3','AK3'))){
					$ARR_Errors[$i]['IK3_AK3'] 	= $Segment;
					$ARR_Errors[$i]['SegID'] 	= $Values['1'];
					$ARR_Errors[$i]['SegPos'] 	= $Values['2'];
					$ARR_Errors[$i]['LoopID'] 	= $Values['3'];
					$ARR_Errors[$i]['Code']		= $Values['4'];
					$ARR_Errors[$i]['Msg3']	= $this->AK3IK3_codeValues($ARR_Errors[$i]['Code']);
				}
				if(in_array($Segment,array('IK4','AK4'))){
					$ARR_Errors[$i]['IK4_AK4'] 			= $Segment;
					$ARR_Errors[$i]['ElementPos'] 		= $Values['1'];
					//$ARR_Errors[$i]['SegPos'] 		= $Values['2'];
					$ARR_Errors[$i]['AK4_Code'] 		= $Values['3'];
					$ARR_Errors[$i]['DataInErr']		= $Values['4'];
					$ARR_Errors[$i]['Msg4']	= $this->AK4IK4_codeValues($ARR_Errors[$i]['AK4_Code']);
				}
				if(count($ARR_Errors[$i])>0){$i++;}
			}
		}
		if(count($ARR_Errors)>0){return $ARR_Errors;}else{return false;}
	}
	
	/*USED IN EDI2HUMAN*/
	function AK3IK3_codeValues($code){
		$ARR_IK3AK3_Code_Values = array();
		$ARR_IK3AK3_Code_Values['1']	= "Unrecognized Segment.";
		$ARR_IK3AK3_Code_Values['2']	= "Unexpected Segment.";
		$ARR_IK3AK3_Code_Values['3']	= "Required Segment Missing.";
		$ARR_IK3AK3_Code_Values['4']	= "Loop Occurs Over Maximum Times.";
		$ARR_IK3AK3_Code_Values['5']	= "Segment Exceeds Maximum Use.";
		$ARR_IK3AK3_Code_Values['6']	= "Segment Not in Defined Transaction Set.";
		$ARR_IK3AK3_Code_Values['7']	= "Segment Not in Proper Sequence.";
		$ARR_IK3AK3_Code_Values['8']	= "Segment Has Data Element Errors.";
		$ARR_IK3AK3_Code_Values['I4']	= "Implementation “Not Used” Segment Present.";
		$ARR_IK3AK3_Code_Values['I6']	= "Implementation Dependent Segment Missing.";
		$ARR_IK3AK3_Code_Values['I7']	= "Implementation Loop Occurs Under Minimum Times.";
		$ARR_IK3AK3_Code_Values['I8']	= "Implementation Segment Below Minimum Use.";
		$ARR_IK3AK3_Code_Values['I9']	= "Implementation Dependent “Not Used” Segment Present.";
		return $ARR_IK3AK3_Code_Values[$code];
	}
	
	/*USED IN EDI2HUMAN*/
	function AK4IK4_codeValues($code){
		$ARR_IK4AK4_Code_Values = array();
		$ARR_IK4AK4_Code_Values['1']	= "Mandatory data element missing.";
		$ARR_IK4AK4_Code_Values['2']	= "Conditionally required data element missing.";
		$ARR_IK4AK4_Code_Values['3']	= "Too many data elements.";
		$ARR_IK4AK4_Code_Values['4']	= "Data element is too short.";
		$ARR_IK4AK4_Code_Values['5']	= "Data element too long.";
		$ARR_IK4AK4_Code_Values['6']	= "Invalid character in data element.";
		$ARR_IK4AK4_Code_Values['7']	= "Invalid code value.";
		$ARR_IK4AK4_Code_Values['8']	= "Invalid date.";
		$ARR_IK4AK4_Code_Values['9']	= "Invalid time.";
		$ARR_IK4AK4_Code_Values['10']	= "Exclusion condition violated.";
		//$ARR_IK4AK4_Code_Values['11']	= "Invalid date.";
		$ARR_IK4AK4_Code_Values['12']	= "Too many repetitions.";
		$ARR_IK4AK4_Code_Values['13']	= "Too many components.";
		$ARR_IK4AK4_Code_Values['I10']	= "Implementation “Not Used” data element Present.";
		$ARR_IK4AK4_Code_Values['I11']	= "Implementation too few repetitions.";
		$ARR_IK4AK4_Code_Values['I12']	= "Implementation pattern match failure.";
		$ARR_IK4AK4_Code_Values['I13']	= "Implementation dependent “Not Used” data element Present.";
		$ARR_IK4AK4_Code_Values['I6']	= "Code value not used in Implementation.";
		$ARR_IK4AK4_Code_Values['I9']	= "Implementation dependent data element missing.";
		return $ARR_IK4AK4_Code_Values[$code];
	}
	
	/*GETTING INSURANCE COMPANIES, GROUPED BY CLEARING HOUSE*/
	function getPayersClearingHouseWise(){
		$ClearingHouse = $this->ClearingHouse();
		$groupName = $ClearingHouse[0]['house_name'];
		
		$ins_com_data = array();
		$all_commertical_carriers = array();
		//--- GET INSURANCE COMPANIES LIST -----
		$q1 = "SELECT id, in_house_code, name,BatchFile, claim_type 
				FROM insurance_companies 
				WHERE (Insurance_payment = 'Electronics' OR secondary_payment_method = 'Electronics') 
				ORDER BY in_house_code";
		$res1 = imw_query($q1);
		while($rs1 = imw_fetch_assoc($res1)){
			$BatchFile = $rs1['BatchFile'];
			$claim_type = $rs1['claim_type'];
			//--- DIRECT BILLING COMPANIES ------
			if($BatchFile > 0){
				$ins_name = trim($rs1['in_house_code']);
				if(empty($ins_name) === true){
					$ins_name = substr($rs1['name'],0,10).'...';
				}
				$ins_com_data[$ins_name] = $rs1['id'];
				if($claim_type=='0') $all_commertical_carriers[] = $rs1['id'];
			}
			//--- CLEARING HOUSE COMPANIES LIST ---
			else{
				$insId[$groupName][] 		= $rs1['id'];
				$all_commertical_carriers[] = $rs1['id'];
			}
		}	
		$ins_com_data[$groupName.' (All Comm. Payers)'] = implode(',',$all_commertical_carriers);
		$ins_com_data[$groupName] = implode(',',$insId[$groupName]);
		return array_reverse($ins_com_data);
	}
	
	/*SEARCH BATCH FILES, ENCOUNTER ID WISE*/
	function getBatchFilesEncWise($eid){
		$return = false;
		$q = "SELECT * FROM batch_file_submitte WHERE (encounter_id LIKE '".$eid.",%' OR encounter_id LIKE '%,".$eid.",%' OR encounter_id LIKE '%,".$eid."' OR encounter_id = '".$eid."')";
		$res = imw_query($q);
		if($res && imw_num_rows($res)>0){
			$return = array();
			while($rs = imw_fetch_assoc($res)){
				$batch_id = $rs['Batch_file_submitte_id'];
				$return[] = $rs;
			}
		}else{
			$return = false;
		}
		return $return;
	}
	
	/*GETTING INSURANCE COMPANY DETAILS*/
	function getInsCompDetails($insId,$del_status='check'){
		$return = false;
		$del_query = " ins_del_status  = '0' AND";
		if($del_status=='nocheck') $del_query = "";
		$q = "SELECT * FROM insurance_companies WHERE".$del_query." id='$insId' LIMIT 0,1";
		$res = imw_query($q);
		if($res && imw_num_rows($res)){
			$return = imw_fetch_assoc($res);
		}
		return $return;
	}
	
	/*GETTING ENCOUNTER DETAILS*/
	function getEncounterDetails($eid){
		$return = false;
		$q = "SELECT * FROM patient_charge_list WHERE encounter_id  = '".$eid."' LIMIT 0,1";
		$res = imw_query($q);
		if($res && imw_num_rows($res)){
			$return = imw_fetch_assoc($res);
		}
		return $return;
	}
	
	/*SEARCHING SPECIFIC VALUES FROM 837 EDI*/
	function findValueFrom837EDI($case,$lineNum,$loop,$ARR_batchData){
		if($loop=='' || intval($loop)==0){return false;}
		$found = false;
		$mode = '++';
		switch($case){
			case 'PatientId':
				if($loop<2300){
					$mode = '--';
				}
				if($mode=='++'){
					for($i=$lineNum;!$found;$i++){
						$clm = $ARR_batchData[$i][0];
						if(strtolower($clm) == 'clm'){$found=true;return array($ARR_batchData[$i][1],$ARR_batchData[$i][2]);}
					}
				}
				else if($mode=='--'){
					for($i=$lineNum;!$found;$i--){
						$clm = $ARR_batchData[$i][0];
						if(strtolower($clm) == 'clm'){$found=true;return array($ARR_batchData[$i][1],$ARR_batchData[$i][2]);}
					}
				}
				break;
			case 'EncounterId':
				if($loop<2400){
					$mode = '--';
				}
				if($mode=='++'){
					for($i=$lineNum;!$found;$i++){
						$seg   = $ARR_batchData[$i][0];
						$segId = $ARR_batchData[$i][1];
						if(strtolower($seg) == 'ref' && strtolower($segId) == '6r'){
							$found=true;
							$TempEnq = explode('MCR',$ARR_batchData[$i][2]);
							return $TempEnq[0];
						}
					}
				}
				else if($mode=='--'){
					for($i=$lineNum;!$found;$i--){
						$seg   = $ARR_batchData[$i][0];
						$segId = $ARR_batchData[$i][1];
						if(strtolower($seg) == 'ref' && strtolower($segId) == '6r'){
							$found=true;
							$TempEnq = explode('MCR',$ARR_batchData[$i][2]);
							return $TempEnq[0];
						}
					}
				}
				break;
		}
	}
	
	/*GET A SPECIFIC SEGMENT FROM ARRAY OF SEGMENTS*/
	function getSegment($ARRsegments,$segment){
		foreach($ARRsegments as $Line){
			$SegArr = explode('*',$Line);
			if(strtoupper($SegArr[0])==strtoupper($segment)){
				 return $SegArr;
			}
		}
		return false;
	}
	
	/*GETTING EDI MESSAGE ACCORDING TO CORD RECEIVED IN 277 OR 997*/
	function getEDImessage($section,$code){
		$q = "SELECT message FROM edi_msg_codes WHERE type='$section' AND code='$code' AND status='0' LIMIT 1";
		$res = imw_query($q);
		if($res && imw_num_rows($res)==1){
			$row = imw_fetch_assoc($res);
			return stripslashes($row['message']);
		}
		return false;
	}
	
	/*GETTING INTERCHANGE NUMBER FROM 277*/
	function getSTCvals($STCval,$EDI='EDI277'){
		$MSGarray = array();
		$chunks = explode(':',$STCval);
		foreach($chunks as $val){
			$res = $this->getEDImessage($EDI,$val);	
			if($res){
				$MSGarray[$val] = $res;	
			}
		}
		if(count($MSGarray)==1){
			return $MSGarray['0'];
		}else if(count($MSGarray)>1){
			return $MSGarray;
		}
		return $STCval;
	}
	
	/*CONVERT 277 TO HUMAN READABLE*/
	function render277CA($edi277){
		$ARRsegments	= explode('~',$edi277);
		$STC			= $this->getSegment($ARRsegments,'STC');
		if($STC && is_array($STC)){
			if(isset($STC['1']) && $STC['1']!=''){
				$claimStatus['STC1'] = $this->getSTCvals($STC['1'],'EDI277'); //claim status
			}
			if(isset($STC['2']) && $STC['2']!='' && strlen($STC['2'])==8){
				$claimStatus['STC2'] = substr($STC['2'],4,2).'-'.substr($STC['2'],6,2).'-'.substr($STC['2'],0,4); //status report date
			}
			if(isset($STC['3']) && $STC['3']!=''){
				$claimStatus['STC3'] = $this->getSTCvals($STC['3'],'EDI277'); //action performed
			}
			if(isset($STC['4']) && $STC['4']!=''){
				$claimStatus['STC4'] = $this->getSTCvals($STC['4'],'EDI277'); //Total claim amount
			}
			if(isset($STC['5']) && $STC['5']!=''){
				$claimStatus['STC5'] = $this->getSTCvals($STC['5'],'EDI277'); //Total amount paid
			}
			if(isset($STC['6']) && $STC['6']!=''){
				$claimStatus['STC6'] = substr($STC['6'],4,2).'-'.substr($STC['6'],6,2).'-'.substr($STC['6'],0,4); //Payment date
			}
			if(isset($STC['7']) && $STC['7']!=''){
				$claimStatus['STC7'] = $this->getSTCvals($STC['7'],'EDI277'); //Payment method
			}
			if(isset($STC['8']) && $STC['8']!=''){
				$claimStatus['STC8'] = substr($STC['8'],4,2).'-'.substr($STC['8'],6,2).'-'.substr($STC['8'],0,4); //Payment issue date
			}
			if(isset($STC['9']) && $STC['9']!=''){
				$claimStatus['STC9'] = $this->getSTCvals($STC['9'],'EDI277'); //Payment# (check#, eft# etc.)
			}
			if(isset($STC['10']) && $STC['10']!=''){
				$claimStatus['STC10'] = $this->getSTCvals($STC['10'],'EDI277'); //Payment# (check#, eft# etc.)
			}
			if(isset($STC['11']) && $STC['11']!=''){
				$claimStatus['STC11'] = $this->getSTCvals($STC['11'],'EDI277'); //Payment# (check#, eft# etc.)
			}
		}
		$response = array();
		$response['decoded']		= $claimStatus;
		$response['main_status']	= $STC['1'];
		$response['segment']		= implode('*',$STC);
		return $response;
	}
	
	/*GETTING ALL ACTIVE USERS ARRAY*/
	function getUserArr(){
		$return	= false;
		$q 		= "SELECT id, fname, mname, lname FROM users WHERE delete_status=0";
		$res 	= imw_query($q);
		if($res && imw_num_rows($res)>0){
			$return 	= array();
			while($rs 	= imw_fetch_assoc($res)){
				$id 	= $rs['id'];
				$fn		= $rs['fname'];
				$ln		= $rs['lname'];
				$mn		= $rs['mname'];
				$abr 	= strtoupper(substr($fn,0,1).substr($ln,0,1));
				$short	= $ln.', '.$fn;
				$long	= $ln.', '.$fn.' '.substr($mn,0,1);
				$return[$id]['abr'] 	= $abr;
				$return[$id]['short'] 	= $short;
				$return[$id]['long'] 	= $long;
			}
		}
		return $return;
	}
	
	/*GET SPECIFIC PROVIDER DETAILS*/
	function getProviderDetails($userID){
		$q 		= "SELECT * FROM users WHERE id='".$userID."'";
		$res 	= imw_query($q);
		if($res && imw_num_rows($res)==1){
			$rs = imw_fetch_assoc($res);
			return $rs;
		}else{
			return false;
		}
	}
	
	function get_group_detail_by_CLuserName($groups_rs){
		$new_groups_rs = array();
		foreach($groups_rs as $groupid=>$groups_rs){
			$new_groups_rs[$groups_rs['user_id']] = $groups_rs;
		}
		return $new_groups_rs;
	}
	
	/*GET CLEARING HOUSE REPORTS*/
	function getDBReportsEmdeon($st,$upto,$EmdGroupSelVal){
		$return = false; $query_part = "";
		$groups_rs 			= $this->get_groups_detail();
		$groups_rs_byCLN 	= $this->get_group_detail_by_CLuserName($groups_rs);
		$getSqlDateFormat	= get_sql_date_format();
		if($EmdGroupSelVal && $EmdGroupSelVal!=''){
			$query_part 	= " AND er.group_id IN ($EmdGroupSelVal)";
		}
		$query = "SELECT er.operator_id,er.report_status,DATE_FORMAT(er.report_recieve_date,'$getSqlDateFormat %H:%i %p') as report_recieve_date,er.group_id, ";
		$query .="er.emdeon_report_id, er.read_status, er.report_data, er.ws_file_name, er.wsMessageType, er.wsGetFile, er.HttpsLoginFailure, er.wsUserID ";
		$query .="FROM emdeon_reports er WHERE report_status IN (0,1) ".$query_part;//." GROUP BY (emdeon_report_id) ";
	
		$firstQ = "SELECT count(emdeon_report_id) AS totalReports FROM emdeon_reports WHERE report_status IN (0,1)";
		$result = imw_query($firstQ);
		
		if($result || $st>0){
			if($st==0){
				$temp_rs = imw_fetch_assoc($result);
				$total_reports_emd 	=  $temp_rs['totalReports'];
				$Resultsremaining	= fmod($total_reports_emd,$upto);
				$lastpageflag		= false;
				if($Resultsremaining>0){$lastpageflag=true;}
				$pagesToShow=($total_reports_emd-$Resultsremaining)/$upto;
				if($lastpageflag==true){$pagesToShow++;}
			}
			$q = $query." ORDER BY emdeon_report_id DESC LIMIT $st,$upto";
			$res = imw_query($q);
			if($res && imw_num_rows($res)>0){
				$users = $this->getUserArr();
				$resultset = array();
				while($rs = imw_fetch_assoc($res)){
					$group_name = '';
					if(!empty($rs['group_id']) && $rs['group_id']>0){
						$group_name = $groups_rs[$rs['group_id']]['name'];
					}else{
						$group_name = $groups_rs_byCLN[$rs['wsUserID']]['name'];	
					}
					$rs['group_name'] 			= ''.$group_name;
					$rs['operator_nm'] 			= $users[$rs['operator_id']]['short'];
					$rs['financial_enquiry'] 	= '';
					/****SETTING FILE NAME TO SHOW****/
					$rs['anchor_file_name'] 	= '';
					$rs['anchor_to_file']		= '';
					if($rs['report_status']==1){
						$rs['anchor_file_name'] = $rs['HttpsLoginFailure'];
					}else if($rs['report_status']==0){
						if(strlen($rs['ws_file_name'])==0){
							$rs['anchor_file_name'] = "File_".$rs['emdeon_report_id'];;
						}else{
							$rs['anchor_file_name'] = $rs['ws_file_name'];
						}
						$fData = strtoupper(trim($rs['report_data']));
						$ReportNum = $this->getValue_fromTextReport($fData,'REPORT#');
						$ReportDate = $this->getValue_fromTextReport($fData,'REPORTDATE');
						if(!$ReportDate){$ReportDate = $this->getValue_fromTextReport($fData,'REPORT DATE');}
						if($ReportNum && $ReportDate){
							$rs['anchor_file_name'] = $ReportNum.' ('.$ReportDate.')';
						}else{
							if(strpos($fData, "ST*997*") !== false){
								$rs['anchor_file_name'] = "997.".$rs['anchor_file_name'];
							}else if(strpos($fData, "ST*999*") !== false){
								$rs['anchor_file_name'] = "999.".$rs['anchor_file_name'];
							}else if(strpos($fData, "ST*277*") !== false){
								$rs['anchor_file_name'] = "277.".$rs['anchor_file_name'];
								$rs['financial_enquiry'] = '<a class="a_clr1 link_cursor" title="View Claim Financial Enquiry" onClick=\'open_file("view_reports.php?id='.$rs['emdeon_report_id'].'&page=1&process=277","View Report");\'>Claim Fin. Enq.</a>';
							}
						}
						$rs['anchor_to_file'] = '<a class="a_clr1 link_cursor" title="view Report" onClick=\'open_file("view_reports.php?id='.$rs['emdeon_report_id'].'&page=1","View Report");\'>'.$rs['anchor_file_name'].'</a>';
						if(strpos($fData, "ST*999*") !== false || strpos($fData, "ST*997*") !== false){
							$rs['anchor_to_file'] = '<a class="a_clr1 link_cursor" title="view Report" onClick=\'viewHumanReadable("'.$rs['emdeon_report_id'].'");\'>'.$rs['anchor_file_name'].'</a>';
						}
						
					}
					
					if($rs['anchor_file_name']=='' || !$rs['anchor_file_name']){
						$rs['anchor_file_name'] = 'No Report Available';
					}
					unset($rs['report_data']);
					$resultset[] = $rs;
				}
				$return = array();
				if($st==0){
					$return['total'] 		= $total_reports_emd;
					$return['pagesToShow']	= $pagesToShow;
				}
				$return['result'] 		= $resultset;
			}
		}
		return $return;
	}
	
	/*GET CLEARING HOUSE REPORTS*/
	function getDBReportsVision($st,$upto,$VisGroupSelVal){
		$return = false; $query_part = "";
		$getSqlDateFormat= getSqlDateFormat();
		$MedGroupsArr = explode(',',$VisGroupSelVal);
		/******CODE FOR CERT AND GROUP***/
		$cert_data_arr = $this->getVScertNgroups();
		$certs_in_use_arr = array();
		if(is_array($cert_data_arr) && count($cert_data_arr)>0){
			foreach($cert_data_arr as $cert_rs){
				if($VisGroupSelVal && $VisGroupSelVal!='' && in_array($cert_rs['group_id'],$MedGroupsArr)){
					$certs_in_use_arr[$cert_rs['vs_cert_in_use']] = $cert_rs['group_name'];
				}else if(!$VisGroupSelVal || $VisGroupSelVal==''){
					$certs_in_use_arr[$cert_rs['vs_cert_in_use']] = $cert_rs['group_name'];	
				}
			}
		}
		if($VisGroupSelVal && $VisGroupSelVal!=''){
			$cert_to_filter_arr = array_keys($certs_in_use_arr);
			$cert_to_filter_str = "'".implode("','",$cert_to_filter_arr)."'";
			$q_part = " AND vsbtrl.cert_use IN ($cert_to_filter_str)";
		}

		$query = "SELECT vsbtrl.id as vs_report_id, vsbtrl.batch_receive_list_xml batchXML,
			vsbtrl.vs_file_data, vsbtrl.receive_operator, 
			DATE_FORMAT(vsbtrl.receive_time_date,'$getSqlDateFormat %h:%i %p') as receiveTimeDate, vsbtrl.vs_file_name as VSFileName,
			vsbtrl.vs_uri as VSFileURI, vsbtrl.imedic_process as imedicProcessStatus, vsbtrl.process_997 as ProcessStatus997, 
			vsbtrl.cert_use as VSCertUsed 
			FROM vision_share_batch_receive_list vsbtrl 
			WHERE vsbtrl.del_status = 0".$q_part;
		$result = imw_query($query);
		if($result){
			$total_reports_vis 	=  imw_num_rows($result);
			$Resultsremaining	= fmod($total_reports_vis,$upto);
			$lastpageflag		= false;
			if($Resultsremaining>0){$lastpageflag=true;}
			$pagesToShow=($total_reports_vis-$Resultsremaining)/$upto;
			if($lastpageflag==true){$pagesToShow++;}
			
			$q = $query." ORDER BY receive_time_date DESC LIMIT $st,$upto"; 
			$res = imw_query($q);
			if($res && imw_num_rows($res)>0){
				$users = $this->getUserArr();
				$resultset = array();
				while($rs = imw_fetch_assoc($res)){
					$cert_used = $rs['VSCertUsed'];
					$group_name = $certs_in_use_arr[$cert_used];
					$rs['group_name']			= $group_name;
					$rs['operator_nm'] 			= $users[$rs['receive_operator']]['short'];
					if(simplexml_load_string($rs['batchXML']) === FALSE) {
						$dbVSFileName = $rs['VSFileName'];
						$dbVSFileURI = $rs['VSFileURI'];
						$dbImedicProcessStatus = $rs['imedicProcessStatus'];		
						$arrVSFileName = array();
						$arrVSFileName = explode(".", $dbVSFileName);
						$get_997_pos=strrpos($vs_file_data,'~ST*997*');
						if(trim($arrVSFileName[0]) == "997" || trim($arrVSFileName[0]) == "999"){
							$db997ProcessStatus = $vsQryRes[$i]['ProcessStatus997'];	
						}
						if(trim($arrVSFileName[0]) == "277CA"){
							$db997ProcessStatus = "277CA";	
						}
						else{
							$db997ProcessStatus = "1000"; // Deliberately send to smarty that this particular file is not a 997	
						}
					}	
					else{
						$arrBatchXML = $objCLSCommonFunction->XMLToArray($rs['batchXML']);
						$blGetFileName = false;		
						$fileName = $fileURI = "";
						foreach($arrBatchXML as $key => $val){
							if(is_array($val) == true){
								if((trim($val["tag"]) == "file") && (trim($val["type"]) == "open")){
									$blGetFileName = true;
								}
								elseif((trim($val["tag"]) == "file") && (trim($val["type"]) == "close")){				
									$arrFileNameURI[] = array("file_name" => $fileName, "file_uri" => $fileURI);
									$fileName = $fileURI = "";
									$blGetFileName = false;
								}
								if($blGetFileName == true){
									if((trim($val["tag"]) == "name") && (trim($val["type"]) == "complete") && (empty($fileName) == true)){
										$fileName = trim($val["value"]);
									}
									elseif((trim($val["tag"]) == "uri") && (trim($val["type"]) == "complete") && (empty($fileURI) == true)){
										$fileURI = trim($val["value"]);
									}	
								}
							}
						}
					}
					$rs['arrFileNameURI'] 		= $arrFileNameURI;
					$rs['db997ProcessStatus'] 	= $db997ProcessStatus;
					unset($rs['receive_operator']);
					unset($rs['batchXML']);
					unset($rs['vs_file_data']);
					$resultset[] = $rs;
	
				}
				$return = array();
				$return['total'] 		= $total_reports_vis;
				$return['pagesToShow']	= $pagesToShow;
				$return['result'] 		= $resultset;
			}
		}
		return $return;
	}
	
	/*GETTING VISION SHARE (ABILITY) CERTIFICATE GROUPS*/
	function getVScertNgroups(){
		$return = false;
		$q = "SELECT vscc.group_id, vscc.ins_comp_id, SUBSTRING_INDEX(vscc.vs_cert_in_use, '-', -1) AS vs_cert_in_use, vscc.vs_paaword AS vs_password, gn.name AS group_name
			  FROM vision_share_cert_config vscc 
			  JOIN groups_new gn ON (gn.gro_id = vscc.group_id)";
		$res = imw_query($q);
		if($res && imw_num_rows($res)>0){
			$return = array();
			while($rs = imw_fetch_assoc($res)){
				$return[] = $rs;
			}
		}
		return $return;
	}

	/*GETTTNG REPORT NAME AND DATE FROM HUMAN READABLE EMDEON REPORTS*/
	function getValue_fromTextReport($report_data,$column,$delimeter=':'){//to get emdeon report name.
		$return = false;
		$arr_report_data = explode("\n",$report_data);
		for($i==0;$i<count($arr_report_data);$i++){
			if(trim($arr_report_data[$i])==''){
				unset($arr_report_data[$i]);
			}else if(substr(trim($arr_report_data[$i]),0,1) == 'R'){
				$space_place = strpos($arr_report_data[$i]," ");
				if($space_place <= 50 && $space_place > 25){
					$replace_character = substr($arr_report_data[$i],0,$space_place);	
					$arr_report_data[$i] = str_replace($replace_character," ",$arr_report_data[$i]);
				}
				$arr_report_data[$i] = str_replace(' ','',$arr_report_data[$i]);
			//	echo $arr_report_data[$i].'<br>';
			} 
		}
		$findIndexOf = strtoupper($column);$foundAt = -1; $f = 1;
		foreach($arr_report_data as $key=>$val){//echo $key.'=>'.$val.'<br>';
			$f = strpos($val,$findIndexOf);
			if($f === 0 && $foundAt < 0){
				$foundAt = $key;
				$colValString = $arr_report_data[$foundAt];//echo $colValString.'<br>';
				break;
			}else if($f>0 && $foundAt < 0){
				$val = substr($val,$f);
				$foundAt = $key;
				$colValString = $val;//echo $colValString.'<br>';
				break;
			}
		}
	
		if($foundAt != -1){
			$Arr_colVal = explode($delimeter,$colValString);
			$return = $Arr_colVal['1'];
		}
		return $return;
	}
	
	/*CHANGE REPORT STATUS TO DELETED*/
	function delete_reports($cl,$file_ids){
		if($cl=='emd_reports_del'){
			$q = "UPDATE emdeon_reports SET report_status = '3' WHERE emdeon_report_id IN($file_ids)";
		}else if($cl=='vis_reports_del'){
			$q = "UPDATE vision_share_batch_receive_list SET del_status = '1' WHERE id IN($file_ids)";
		}//echo $q.'<hr>';
		$res = imw_query($q);// echo imw_error();
		if($res) return 'success';
		else return 'fail';
	}
	
	/*MAKING PRE-AUTHORIZATION REQUEST DATA*/
	function make278EDI(){
		global $billing_global_taxonomy_number;
		global $overwriteTID;
		$return = array();
		$return['response'] 	= '';
		$return['error']		= '';
		//$this->patientId;
		if($this->groupId==''){//--GETTING HQ FACILITY DETAILS
			$arr_HQFac		= $this->get_facility_detail('','yes');
			$this->groupId	= $arr_HQFac['default_group'];
		}

		$arr_group		= $this->get_groups_detail($this->groupId);
		$arr_Provider	= $this->getProviderDetails($this->priPhysician);
		$arr_InsData	= $this->getInsuranceData($this->InsDataId);
		if($this->InsDataId != ''){//if Ins.Provider not posted, but Ins.Data Id is posted
			$SubscriberDetails	= $this->getInsuranceData($this->InsDataId);
			$this->priPayer				= $SubscriberDetails['provider'];
		}
		$arr_PriPayer	= $this->getInsCompDetails($this->priPayer);
		
		//COMPOSING EDI DATA (X12 278)
		$submitter_id 	= $arr_group['prod_tid'];
		if($overwriteTID!='') {$submitter_id = $overwriteTID;}
		if(trim($submitter_id)==''){
			$return['error']		= 'Submitter ID not found.';
			return $return;
		}
		$submitter_id = $this->padd_string($submitter_id,15,' ','suffix');
		
		$receiver_id 	= 'EMDEON';//$arr_group['rec_id'];
		if(trim($receiver_id)==''){
			$return['error']		= 'Receiver ID not found.';
			return $return;
		}
		$receiver_id = $this->padd_string($receiver_id,15,' ','suffix');
		
		$HQ_BillingLocation = intval($arr_HQFac['billing_location']);
		$nowDate = date('Ymd'); $nowTime = date('hi');
		$interchange_control_standard_id = '^';
		$interchange_control_version_number = '00501';		
		$interchange_unique_headers = $this->get_unique_headers('278');
		if(!$interchange_unique_headers){
			$return['error']		= 'Unable to get Unique Headers.';
			return $return;
		}
		$new_interchange_number		= $interchange_unique_headers['new_interchange_num'];
		$interchange_control_number = $interchange_unique_headers['interchange_control_num'];
		$transaction_set_controller = $interchange_unique_headers['transaction_set_unique_control'];
		$header_control_identifier  = $interchange_unique_headers['header_control_identifier'];
		$implementation_convention_reference = '005010X217';//'005010X215';
		
		//INTERCHANGE CONTROL HEADER
		$ISA 		= array();
		$ISA['01'] 	= '00';
		$ISA['02'] 	= '          ';	//10 SPACES.
		$ISA['03'] 	= '00';
		$ISA['04'] 	= '          ';	//10 SPACES.
		$ISA['05'] 	= 'ZZ';
		$ISA['06'] 	= $submitter_id;	//INTERCHANGE SENDER ID
		$ISA['07'] 	= 'ZZ';
		$ISA['08'] 	= $receiver_id;	//INTERCHANGE receiver ID
		$ISA['09'] 	= date('ymd');
		$ISA['10'] 	= $nowTime;
		$ISA['11'] 	= $interchange_control_standard_id;
		$ISA['12'] 	= $interchange_control_version_number;
		$ISA['13'] 	= $interchange_control_number;
		$ISA['14'] 	= '0';
		$ISA['15'] 	= 'T';
		$ISA['16'] 	= ':';
		$EDI_DATA	= 'ISA*'.implode('*',$ISA).'~';
		
		//FUCNTIONAL GROUP HEADER //GS*HI*15929599*EMDEON*20130606*0650*000000001*X*005010X212A1~
		$GS			= array();
		$GS['01'] 	= 'HI';
		$GS['02'] 	= trim($submitter_id);
		$GS['03'] 	= trim($receiver_id);
		$GS['04'] 	= $nowDate;
		$GS['05'] 	= $nowTime;
		$GS['06'] 	= $new_interchange_number;
		$GS['07'] 	= 'X';
		$GS['08'] 	= $implementation_convention_reference;
		$EDI_DATA	.= 'GS*'.implode('*',$GS).'~';
		
		//TRANSACTION SET HEADER //ST*276*0001*005010X212A1~
		$Line_counter = 1;
		$ST			= array();
		$ST['01']		= '278';
		$ST['02']		= $transaction_set_controller;
		$ST['03']		= $implementation_convention_reference;
		$EDI_DATA	.= 'ST*'.implode('*',$ST).'~';
		$Line_counter++;
		
		//BEGINNING OF HIERARCHICAL TRANSACTION //BHT*0010*13*ALL*20130606*0650~
		$BHT		= array();
		$BHT['01']	= '0007'; //0007 = Information Source, Information Receiver, Subscriber, Dependent, Event, Services
		$BHT['02']	= '13';
		$BHT['03']	= $header_control_identifier;
		$BHT['04']	= $nowDate;
		$BHT['05']	= $nowTime;
		//$BHT['06']	= 'RU'; //RU=Medical Services Reservation.
		$EDI_DATA	.= 'BHT*'.implode('*',$BHT).'~';
		$Line_counter++;
		
		//HIERARCHICAL LEVEL 1 //PAYER NAME LOOP (2000A)
		$HL_level = 1;
		$EDI_DATA	.= 'HL*'.$HL_level.'**20*1~';
		$Line_counter++;
		$HL_level++;
		
		//UTILIZATION MANAGEMENT ORGANIZATION (umo) LOOP 2010A;          NM1*X3*2*AARP HEALTH PLAN*****PI*36273~
		if($arr_PriPayer==false || !is_array($arr_PriPayer)){
			$return['error']		= 'Primary Payer details not found.';
			return $return;
		}else if(intval($arr_PriPayer['pre_atuh_chk'])==0){
			$return['error']		= 'Pre-Authorization is not enabled for this Payer ('.stripslashes($arr_PriPayer['name']).'). Contact Administrator.';
			return $return;
		}else if($arr_PriPayer['emdeon_payer_eligibility']=='' || intval($arr_PriPayer['emdeon_payer_eligibility'])==0){
			$return['error']		= 'Payer\'s RealTime Payer ID is not valid.';
			return $return;
		}
		$payer_name = preg_replace('/[^a-zA-Z0-9_\- .]/','',trim(substr($arr_PriPayer['name'],0,60)));
		$payer_id	= trim($arr_PriPayer['emdeon_payer_eligibility']);
		if($payer_name=='')		{$return['error'] = 'Invalid payer name.';return $return;}
		else if($payer_id=='')	{$return['error'] = 'Payer ID not found for claim status request.';return $return;}
		$NM1_payer			= array();
		$NM1_payer['01']	= 'PR';//'X3';
		$NM1_payer['02']	= '2';
		$NM1_payer['03']	= $payer_name;
		$NM1_payer['04']	= '';
		$NM1_payer['05']	= '';
		$NM1_payer['06']	= '';
		$NM1_payer['07']	= '';
		$NM1_payer['08']	= 'PI';
		$NM1_payer['09']	= $payer_id;
		$EDI_DATA			.= 'NM1*'.implode('*',$NM1_payer).'~';
		$Line_counter++;
		
		//HIERARCHICAL LEVEL 2 //REQUESTER LEVEL (Loop 2000B)
		$EDI_DATA	.= 'HL*'.$HL_level.'*1*21*1~';
		$Line_counter++;
		$HL_level++;
		
		//----TEST CODE--- START
		if(trim($arr_Provider['user_npi'])==''){
			$return['error']	= 'Rendering provider NPI not found';
			return $return;
		}
		$NM1_rprovider			= array();
		$NM1_rprovider['01']	= '1P';
		$NM1_rprovider['02']	= '1';
		$NM1_rprovider['03']	= trim($arr_Provider['lname']);
		$NM1_rprovider['04']	= trim($arr_Provider['fname']);;
		$NM1_rprovider['05']	= trim(substr($arr_Provider['mname'],0,1));
		$NM1_rprovider['06']	= '';
		$NM1_rprovider['07']	= '';
		$NM1_rprovider['08']	= 'XX';
		$NM1_rprovider['09']	= preg_replace("/-/","",$arr_Provider['user_npi']);
		$EDI_DATA			.= 'NM1*'.implode('*',$NM1_rprovider).'~';
		$Line_counter++;
		/*
		//REQUEST CONTACT INFORMATION
		$PER		= array();
		$PER['01']	= 'IC';
		$PER['02']	= trim(substr($pt_pcp_details['LastName'].', '.$pt_pcp_details['FirstName'],0,60));
		$EDI_DATA .=	'PER*'.implode('*',$PER).'~';
		$Line_counter++;
		*/
		//REQUESTER NAME (LOOP 2010B)         NM1*1P*2*PATIENT_PCP*****XX*1093737017~
		/***COMMENTED FOR ABOVE TEST CODE********
		$pt_pcp_id				= $this->get_patient_PCP($this->patientId);
		if($pt_pcp_id==false || $pt_pcp_id <= 0){
			$return['error']		= 'Patient\'s Primary Care Physician not found.';
			return $return;
		}
		$pt_pcp_details			= $this->get_ref_phy_details($pt_pcp_id);
		if(trim($pt_pcp_details['FirstName'])=='' || trim($pt_pcp_details['LastName'])==''){
			$return['error']		= 'Primary Care Physician name is not valid.';
			return $return;
		}else if(trim($pt_pcp_details['NPI'])==''){
			$return['error']		= 'Primary Care Physician name is not valid.';
			return $return;	
		}

		$NM1_requester		= array();
		$NM1_requester['01']	= '1P';
		$NM1_requester['02']	= '1';
		$NM1_requester['03']	= trim($pt_pcp_details['FirstName']);
		$NM1_requester['04']	= trim($pt_pcp_details['LastName']);
		$NM1_requester['05']	= trim(substr($pt_pcp_details['MiddleName'],0,1));
		$NM1_requester['06']	= '';
		$NM1_requester['07']	= '';
		$NM1_requester['08']	= 'XX';
		$NM1_requester['09']	= trim(preg_replace("/-/","",trim($pt_pcp_details['NPI'])));
		$EDI_DATA			.= 'NM1*'.implode('*',$NM1_requester).'~';
		$Line_counter++;
		
		//REQUESTER ADDRESS
		$N3_requester			= array();
		if(empty($pt_pcp_details['Address1']) == false){
			$N3_requester['01']	= trim($pt_pcp_details['Address1']);
		}
		if(empty($pt_pcp_details['Address2']) == false){
			$N3_requester['02']	= trim($pt_pcp_details['Address2']);
		}
		$EDI_DATA				.= 'N3*'.implode('*',$N3_requester).'~';
		$Line_counter++;
		
		//REQUESTER CITY
		$N4_requester			= array();
		$N4_requester['01']		= trim($pt_pcp_details['City']);
		$N4_requester['02']		= trim($pt_pcp_details['State']);
		$N4_requester['03']		= trim($pt_pcp_details['ZipCode']).trim($arr_group['zip_ext']);
		if((empty($pt_pcp_details['City']) == false) && (empty($pt_pcp_details['State']) == false) && (empty($pt_pcp_details['ZipCode']) == false)){
			$EDI_DATA			.= 'N4*'.implode('*',$N4_requester).'~';
			$Line_counter++;
		}
		
		//REQUEST CONTACT INFORMATION
		$PER		= array();
		$PER['01']	= 'IC';
		$PER['02']	= trim(substr($pt_pcp_details['LastName'].', '.$pt_pcp_details['FirstName'],0,60));
		$PER['03']	= 'TE';
		$PER['04']	= preg_replace("/-/","",trim($pt_pcp_details['physician_phone']));
		if(preg_replace("/-/","",trim($pt_pcp_details['physician_fax']))!=''){
			$PER['05']	= 'FX';
			$PER['06']	= preg_replace("/-/","",trim($pt_pcp_details['physician_fax']));
		}
		if(trim($pt_pcp_details['physician_email'])!=''){
			$PER['07']	= 'EM';
			$PER['08']	= trim($pt_pcp_details['physician_email']);
		}
		if((empty($pt_pcp_details['physician_phone']) == false) || (empty($pt_pcp_details['physician_fax']) == false) || (empty($pt_pcp_details['physician_email']) == false)){
			$EDI_DATA .=	'PER*'.implode('*',$PER).'~';
			$Line_counter++;
		}
		**COMMENTED FOR TEST CODE*****/
		
		//PROVIDER SPECIALITY CODE //PRV*BI*PXC*207W00000X~
		/*--COMMENTED ON 7TH APRIL 2015 AFTER LIESC TESTIN
		$PRV		= array();
		$PRV['01']	= 'BI';
		$PRV['02']	= 'PXC';
		$PRV['03']	= $billing_global_taxonomy_number;
		if(trim($billing_global_taxonomy_number)!=''){
			$EDI_DATA .=	'PRV*'.implode('*',$PRV).'~';
			$Line_counter++;
		}
		-------*/
		
		//Patient/Subscriber (Loop 2000C)NM1*QC*1*DOE*JOHN****MI*R11056841~
		if($arr_InsData==false || !is_array($arr_InsData)){
			$return['error']		= 'Subscriber information not found';
			return $return;
		}
		$pat_sub_relation		= trim($arr_InsData['subscriber_relationship']);
		$blDepLoopExit 			= false;
		//HIERARCHICAL SUBSCRIBER NAME level (Loop 2000C)
		if(strtolower($pat_sub_relation)=='self'){
			$EDI_DATA	.= 'HL*'.$HL_level.'*'.($HL_level-1).'*22*1~';
			$Line_counter++;
			$HL_level++;
		}else{
			$return['error']		= 'Patient-Subscriber relation must be "SELF"';
			return $return;
		}
		
		//NM1 SUBSCRIBER INFO (LOOP 2010C)
		$NM1_subscriber		= array();
		$NM1_subscriber['01']	= 'IL';
		$NM1_subscriber['02']	= '1';
		$NM1_subscriber['03']	= trim($SubscriberDetails['subscriber_lname']);
		$NM1_subscriber['04']	= trim($SubscriberDetails['subscriber_fname']);
		$NM1_subscriber['05']	= trim(substr($SubscriberDetails['subscriber_mname'],0,1));
		$NM1_subscriber['06']	= '';
		$NM1_subscriber['07']	= '';
		$NM1_subscriber['08']	= 'MI';
		$NM1_subscriber['09']	= preg_replace("/-/","",$SubscriberDetails['policy_number']);
		$EDI_DATA			.= 'NM1*'.implode('*',$NM1_subscriber).'~';
		$Line_counter++;
		
		/*
		//Loop: 2100C -> SUBSCRIBER ADDITIONAL IDENTIFICATION (SUBSCRIBER SSN)
		$dbSubscriberSS			= trim(preg_replace("/-/","",$SubscriberDetails['subscriber_ss']));
		if((empty($dbSubscriberSS) == false)){
			$EDI_DATA .= "REF*SY*".trim($dbSubscriberSS)."~";
			$Line_counter++;
		}
		*/
		
		//DMG (Date of Birth)
		$subs_DOB			= trim(preg_replace("/-/","",$SubscriberDetails['subscriber_DOB']));
		$DMG_array 			= array();
		$DMG_array['01']	= 'D8';
		$DMG_array['02']	= $subs_DOB;
		$DMG_array['03']	= substr($SubscriberDetails['subscriber_sex'],0,1);
		if(strlen($subs_DOB) != 8 || $subs_DOB=='00000000'){
			$return['error']	= 'Subscriber DOB is not valid';
			return $return;
		}else{
			$EDI_DATA .= 'DMG*'.implode('*',$DMG_array).'~';
			$Line_counter++;	
		}
		
		//HIERARCHICAL LEVEL //PATIENT EVENT LEVEL (Loop 2000E)
		$EDI_DATA	.= 'HL*'.$HL_level.'*'.($HL_level-1).'*EV*0~';
		$Line_counter++;
		$HL_level++;

		//LOOP 2000E; HEALTH CARE SERVICE REVIEW INFORMATION
		$UM					= array();
		$UM['01']			= 'HS';
		$UM['02']			= 'I';
		$UM['03']			= '44';//$this->reviewServiceType; //SERVICE TYPE CODE. FOR vision, is is "AL".
		$UM['04']			= '11:B';
		$EDI_DATA			.= 'UM*'.implode('*',$UM).'~';
		$Line_counter++;	
		
		//CURRENT DIAGNOSIS ONSET DATE (current illness date)
		$DTP_onset			= array();
		$DTP_onset['01']	= 'AAH';
		$DTP_onset['02']	= 'RD8';
		$DTP_onset['03']	= trim(preg_replace("/-/","",$this->onSetDate)).'-'.trim(preg_replace("/-/","",$this->onSetDate));
		$EDI_DATA			.= 'DTP*'.implode('*',$DTP_onset).'~';
		$Line_counter++;
		
		//CURRENT DIAGNOSIS (HI*BK:36653*BF:37515*BF:36251*BF:3674~)
		if(is_array($this->ptDiagArr) && count($this->ptDiagArr)>0){
			$diag_str		= implode('*ABF:',$this->ptDiagArr);
			$diag_str		= trim(preg_replace("/\./","",$diag_str));
			$EDI_DATA		.= 'HI*ABK:'.$diag_str.'~';
			$Line_counter++;
		}else{
			$return['error']	= 'Patient Diagnosis not found';
			return $return;
		}
		/*
		//PROCEDURES TO PERFORM, AND AMOUNT ASKING FROM PAYER.
		$sv_segment		= 'SV1';
		foreach($this->LineItemArr as $LineItem){
			$dx				= explode(',',$LineItem['dx']);
			$dxPointerKeyArr= array();
			foreach($dx as $dxPointerVal){
				$dxPointerKey = array_search($dxPointerVal, $this->ptDiagArr);
				$dxPointerKeyArr[] = $dxPointerKey + 1;
			}
			$dxPointerKeyArr = array_slice($dxPointerKeyArr,0,4);
			$dxPointerKey = join(':', $dxPointerKeyArr);

			$SV1			= array();
			if(trim($arr_group['group_institution'])=='1'){
				$sv_segment		= 'SV2';
				$SV1['00']		= $LineItem['rev'];	//REV CODE
			}
			$SV1['01']		= 'HC:'.$LineItem['cpt'];
			$SV1['02']		= $LineItem['amount'];
			$SV1['03']		= 'UN';
			$SV1['04']		= $LineItem['unit'];
			if(trim($arr_group['group_institution'])!='1'){
				$SV1['05']		= '';
				$SV1['06']		= '';
				$SV1['07']		= $dxPointerKey; //dx pointers
			}
			$EDI_DATA			.= $sv_segment.'*'.implode('*',$SV1).'~';
			$Line_counter++;
		}			
		$HSD_segment			= array();
		$HSD_segment['01']	= 'VS';
		$HSD_segment['02']	= '1';
		$EDI_DATA			.= 'HSD*'.implode('*',$HSD_segment).'~';
		$Line_counter++;
		*/
		
		
		//LOOP 2010F; RENDERING PROVIDER.
		if(trim($arr_Provider['user_npi'])==''){
			$return['error']	= 'Rendering provider NPI not found';
			return $return;
		}
		$NM1_rprovider			= array();
		$NM1_rprovider['01']	= 'SJ';
		$NM1_rprovider['02']	= '1';
		$NM1_rprovider['03']	= trim($arr_Provider['lname']);
		$NM1_rprovider['04']	= trim($arr_Provider['fname']);;
		$NM1_rprovider['05']	= trim(substr($arr_Provider['mname'],0,1));
		$NM1_rprovider['06']	= '';
		$NM1_rprovider['07']	= '';
		$NM1_rprovider['08']	= 'XX';
		$NM1_rprovider['09']	= preg_replace("/-/","",$arr_Provider['user_npi']);
		$EDI_DATA			.= 'NM1*'.implode('*',$NM1_rprovider).'~';
		$Line_counter++;
		
		//SERVICE PROVIDER INFORMATION
		/*---------
		$arr_Provider['TaxonomyId'] = trim(preg_replace("/-/","",$arr_Provider['TaxonomyId']));
		$PRV		= array();
		$PRV['01']	= 'PE';
		$PRV['02']	= 'PXC';
		$PRV['03']	= $arr_Provider['TaxonomyId'];
		if($arr_Provider['TaxonomyId']!=''){
			$EDI_DATA .=	'PRV*'.implode('*',$PRV).'~';
			$Line_counter++;
		}
		---------*/

		$EDI_DATA .= 'SE*'.$Line_counter.'*'.$transaction_set_controller.'~';
		$EDI_DATA .= 'GE*1*'.$new_interchange_number.'~';
		$EDI_DATA .= 'IEA*1*'.$interchange_control_number.'~';
		$EDI_DATA = preg_replace('/[^a-zA-Z0-9_*\- ~.:|@^\']/',' ',$EDI_DATA);
		
		$return['response'] 	= strtoupper($EDI_DATA);
		return $return;
	}
	
	/*READING PRE-AUTH RESPONSE DATA*/
	function read278EDI($EDI278Data){
		$EDI278Data	= trim($EDI278Data);
		//echo $EDI278Data.'<hr>';
		$response = array('error'=>'','result'=>'');
		$hl_array = array();
		$previous_segment = '';
		if($EDI278Data!=''){
			$TA3 = $this->FindSegmentInX12EDI($EDI278Data,'TA3');
			if($TA3==false){//NO TA3 FOUND,
				$ST 		= $this->FindSegmentInX12EDI($EDI278Data,'ST');
				$EDI_type 	= trim($this->FindSegmentValue($ST,1));
				if($EDI_type=='278'){
					$EDI278Lines = explode('~',$EDI278Data);
					$EDI278Lines = preg_split('/~/', $EDI278Data, -1, PREG_SPLIT_NO_EMPTY);
					$response_summary 	= array();
					$checked_segments	= array();
					$HL_1 = $HL_2 = $HL_3 = $HL_4 =true;
					foreach($EDI278Lines as $Lines){
						$segmentName	= $this->FindSegmentValue($Lines,0);
						$segmentInitials= $this->FindSegmentValue($Lines,1);
						//CHECK HERE IN LOOP FOR ALL SEGMENTS AND INITIALS, STORE IN ARRAY AND RETURN.
						if($segmentName=='HL'){
							if($segmentInitials=='1'){//Utilization Management Organization (UMO) Level
								$hl_array['loop'] = '2000A (Utilization Management Organization (UMO) Level).';	
							}else if($segmentInitials=='2'){
								$hl_array['loop'] = '2000B (Requester Information Level).';	
							}else if($segmentInitials=='3'){
								$hl_array['loop'] = '2000C (Subscriber Information Level).';	
							}else if($segmentInitials=='4'){
								$hl_array['loop'] = '2000D (Dependent Information Level).';	
							}
							//$response_summary[]	= $hl_array;
							$checked_segments[]	= 'HL';
						}else if($segmentName=='AAA'){
							$aaa_array = array();
							$aaa01	= strtoupper(trim($this->FindSegmentValue($Lines,1)));
							$aaa03	= trim($this->FindSegmentValue($Lines,3));
							$aaa04	= trim($this->FindSegmentValue($Lines,4));
							if($aaa01=='Y'){
								$aaa_array['valid_request_indicator'] = 'Yes';
							}else if($aaa01!='Y'){
								$aaa_array['valid_request_indicator'] = 'No';
							}
							if($aaa03!=''){
								$aaa_array['validation_message'] = $this->getAAAmsgByCode($aaa03);
							}
							if($aaa04!=''){
								$aaa_array['followup_action'] = $this->getAAAactionByCode($aaa04);
							}
							$response_summary[]	= $hl_array;
							$response_summary[]	= $aaa_array;
							$checked_segments[]	= 'AAA';
						}else if($segmentName=='UM'){
							$um_array = array();
							switch($segmentInitials){
								case 'AR': $um_array['request_cateogry_code'] = 'Admission Review.'; break;
								case 'HS': $um_array['request_cateogry_code'] = 'Health Service Review.'; break;
								case 'SC': $um_array['request_cateogry_code'] = 'Specialty Care Review.'; break;
								case 'IN': $um_array['request_cateogry_code'] = 'Individual.'; break;
							}
							
							switch($this->FindSegmentValue($Lines,2)){
								case '1': $um_array['certification_type_code'] = 'Appeal - Immediate.'; break;
								case '2': $um_array['certification_type_code'] = 'Appeal - Standard.'; break;
								case '3': $um_array['certification_type_code'] = 'Cancel.'; break;
								case '4': $um_array['certification_type_code'] = 'Extension.'; break;
								case 'I': $um_array['certification_type_code'] = 'Initial.'; break;
								case 'R': $um_array['certification_type_code'] = 'Renewal.'; break;
								case 'S': $um_array['certification_type_code'] = 'Revised.'; break;
							}
							
							switch($this->FindSegmentValue($Lines,3)){
								case '1': $um_array['service_type_code'] 	= 'Medical Care.'; break;
								case '2': $um_array['service_type_code'] 	= 'Surgical.'; break;
								case '3': $um_array['service_type_code'] 	= 'Consultation.'; break;
								case 'A0': $um_array['service_type_code'] 	= 'Professional (Physician) Visit - Outpatient.'; break;
							}
							$response_summary[]	= $um_array;
							$checked_segments[]	= 'UM';
						}else if($segmentName=='HCR' && in_array('UM',$checked_segments)){
							$HCR_array = array();
							$HCR_array['authorization_number']				= $this->FindSegmentValue($Lines,2).'<br>';
							$response_summary[]	= $HCR_array;
							$checked_segments[]	= 'HCR';
						}
						
						$previous_segment = $segmentName;
					}
					$response['result'] = $response_summary;
				}else{
					$response['error'] = 'Not a valid X12 278 EDI response.';
				}				
			}else{//READ TA3.
				$ta3RejectionCode = $this->FindSegmentValue($TA3,3);
				switch($ta3RejectionCode){
					case '28': $response['error'] = '28 - Time Out. Not Delivered.'; break;
					case '29': $response['error'] = '29 - Time Out. Deliverd.'; break;
					case '31': $response['error'] = '31 - Receiver Not On-Line.'; break;
					case '32': $response['error'] = '32 - Abnormal Conditions.'; break;			
				}
			}
		}else{
			$response['error'] = '&lt;empty&gt; data string provided.';
		}
		
		return $response;
	}
	
	/*DECODING AAA MESSAGE*/
	function getAAAmsgByCode($msgCode){
		$messageArray = array();
		$messageArray['04'] 	= 'Authorized Quantity Exceeded.';
		$messageArray['13'] 	= 'Required application data missing.';
		$messageArray['15'] 	= 'Required application data missing.';
		$messageArray['33'] 	= 'Input Errors.';
		$messageArray['35'] 	= 'Out of Network.';
		$messageArray['41'] 	= 'Authorization/Access Restrictions.';
		$messageArray['42'] 	= 'Unable to Respond at Current Time.';
		$messageArray['43'] 	= 'Invalid/Missing Provider Identification.';
		$messageArray['44'] 	= 'Invalid/Missing Provider Name.';
		$messageArray['45'] 	= 'Invalid/Missing Provider Specialty.';
		$messageArray['46'] 	= 'Invalid/Missing Provider Phone Number.';
		$messageArray['47'] 	= 'Invalid/Missing Provider State.';
		$messageArray['48'] 	= 'Invalid/Missing Referring Provider Identification Number.';
		$messageArray['49'] 	= 'Provider is Not Primary Care Physician.';
		$messageArray['50'] 	= 'Provider Ineligible for Inquiries.';
		$messageArray['51'] 	= 'Provider Not on File.';
		$messageArray['52'] 	= 'Service Dates Not Within Provider Plan Enrollment.';
		$messageArray['56'] 	= 'Inappropriate Date.';
		$messageArray['57'] 	= 'Invalid/Missing Date(s) of Service.';
		$messageArray['58'] 	= 'Invalid/Missing Date-of-Birth.';
		$messageArray['60'] 	= 'Date of Birth Follows Date(s) of Service.';
		$messageArray['61'] 	= 'Date of Death Precedes Date(s) of Service.';
		$messageArray['62'] 	= 'Date of Service Not Within Allowable Inquiry Period.';
		$messageArray['63'] 	= 'Date of Service in Future.';
		$messageArray['64'] 	= 'Invalid/Missing Patient ID.';
		$messageArray['65'] 	= 'Invalid/Missing Patient Name.';
		$messageArray['66'] 	= 'Invalid/Missing Patient Gender Code.';
		$messageArray['67'] 	= 'Patient Not Found.';
		$messageArray['68'] 	= 'Duplicate Patient ID Number.';
		$messageArray['71'] 	= 'Patient Birth Date Does Not Match That for the Patient on the Database.';
		$messageArray['72'] 	= 'Invalid/Missing Subscriber/Insured ID.';
		$messageArray['73'] 	= 'Invalid/Missing Subscriber/Insured Name.';
		$messageArray['74'] 	= 'Invalid/Missing Subscriber/Insured Gender Code.';
		$messageArray['75'] 	= 'Subscriber/Insured Not Found.';
		$messageArray['76'] 	= 'Duplicate Subscriber/Insured ID Number.';
		$messageArray['77'] 	= 'Subscriber Found, Patient Not Found.';
		$messageArray['78'] 	= 'Subscriber/Insured Not in Group/Plan Identified.';
		$messageArray['79'] 	= 'Invalid Participant Identification.';
		$messageArray['80'] 	= 'No Response Received – Transaction Terminated.';
		$messageArray['97'] 	= 'Invalid or Missing Provider Address.';
		$messageArray['T4'] 	= 'Payer Name or Identifier Missing.';
		$messageArray['15'] 	= 'Required application data missing.';

		return $messageArray[$msgCode];
	}
	
	/*DECODING AAA MESSAGE ACTION*/
	function getAAAactionByCode($msgCode){
		$msgCode			= strtoupper($msgCode);
		$messageArray 		= array();
		$messageArray['C'] 	= 'Please Correct and Resubmit.';
		$messageArray['N']	= 'Resubmission Not Allowed.';
		$messageArray['P']	= 'Please Resubmit Original Transaction.';
		$messageArray['R']	= 'Resubmission Allowed.';
		$messageArray['S']	= 'Do Not Resubmit; Inquiry Initiated to a Third Party.';
		$messageArray['W']	= 'Please Wait 30 Days and Resubmit.';
		$messageArray['X']	= 'Please Wait 10 Days and Resubmit.';
		$messageArray['Y']	= 'Do Not Resubmit; We Will Hold Your Request and Respond Again Shortly.';

		return $messageArray[$msgCode];
	}
	
	/*FIND SEGMENT IN 278 EDI*/
	function FindSegmentInX12EDI($EDI,$FindMe){
		$EDI278Lines = preg_split('/~/', $EDI, -1, PREG_SPLIT_NO_EMPTY);
		foreach($EDI278Lines as $Line){
			$Line_elements 	= explode('*',$Line);
			$segment		= $Line_elements[0];
			if($segment==$FindMe){
				return $Line;
			}
		}
		return false;
	}
	
	/*FIND PARTICULAR INDEX VALUE IN EDI SEGMENT*/
	function FindSegmentValue($SegmentLine,$valueNumber){
		$valueNumber = intval($valueNumber);
		if($SegmentLine==''){
			return false;
		}else{
			$values = explode('*',$SegmentLine);
			return $values[$valueNumber];
		}
	}
	
	/*GETTING HUMAN READABLE REQUEST-FROM*/
	function x12RequestFrom($num){
		$num	= intval($num);
		$arr	= array();
		$arr[0]	= '';
		$arr[1]	= 'Scheduler';
		$arr[2]	= 'Insurance';
		return $arr[$num];
	}
	
	/*278 RESPONSE ARRAY TO TEXT*/
	function responseArray2Text($respArray){
		$str = '';
		foreach($respArray as $key=>$val){
			//echo $key.' = '.$val.'<br>';
			if(is_array($val)){
				$str .= $this->responseArray2Text($val);
			}else{
				if($key=='error' && $val=='') continue;
				$str .= '<b>'.ucwords(str_replace('_',' ',$key)).': </b>'.$val.'<br>';
			}
		}		
		return $str;
	}
	
	/*GET PATIENT INSURANCE DATA DETAILS*/
	function getInsuranceData($insDataId){
		$insDataId = trim($insDataId);
		if(trim($insDataId)=='') return false;
		$q = "SELECT * FROM insurance_data WHERE id='".$insDataId."' LIMIT 1";
		$res = imw_query($q);
		if($res && imw_num_rows($res)>0){
			$rs = imw_fetch_assoc($res);
			return $rs;
		}else{
			return false;
		}
	}
	
	/*GET PATIENT PRIMARY CARE PHYSICIAN*/
	function get_patient_PCP($paient_id){
		$pcp 	= 0;
		$q		= "SELECT primary_care_phy_id FROM patient_data WHERE id = '$paient_id' LIMIT 0,1";
		$res	= imw_query($q);
		if($res && imw_num_rows($res)==1){
			$rs	= imw_fetch_assoc($res);
			$pcp= $rs['primary_care_phy_id'];
		}
		return $pcp;
	}
	
	/*REMOVE EMPTY VALUE KEY FROM ARRAY*/
	function remove_empty($array) {
		foreach($array as $k=>$v){
			if($v=='') unset($array[$k]);
		}
		return $array;
	}
	
	/*RE-ARRANGE ARRAY KEYS TO SEQUENTIAL INTEGER*/
	function set_int_keys($array) {
		$array2 = array();
		foreach($array as $k=>$v){
			$array2[] = $v;
		}
		return $array2;
	}

/*GET SEPARATED CLAIMS BASED ON POS & 837P/I IF GROUP IS INSTITUTIONAL*/
	function separate_claims_for_batches($charge_list_ids,$InsCompField,$group_institution){
		$q1 			= "SELECT alt_npi_number as npi, GROUP_CONCAT(pos_facility_id) as pos_fac_ids FROM pos_facilityies_tbl GROUP BY (alt_npi_number)";
		$res1 			= imw_query($q1);
		$posNPI_array 	= array();
		if($res1 && imw_num_rows($res1)>0){
			while($rs1 = imw_fetch_assoc($res1)){
				$rs1['npi'] = trim($rs1['npi']);
				$posNPI_array[$rs1['pos_fac_ids']] = $rs1['npi'];
			}
		}
		
		//FILTER charge_list_ids ACCORDING TO pos_facilities.
		$npi_chl_array 	= array();
		foreach($posNPI_array as $pos_ids=>$npi){
			//echo $npi.'=>'.$pos_ids.'<br>';
			$q2 = "SELECT DISTINCT(pcl.charge_list_id) as chl_ids 
					FROM patient_charge_list pcl 
					JOIN patient_charge_list_details pcld ON (pcld.charge_list_id = pcl.charge_list_id) 
					WHERE pcld.posFacilityId IN ($pos_ids) AND pcl.charge_list_id IN ($charge_list_ids)";
			//echo $q2.'<hr>';
			$res2 = imw_query($q2);
			$chl_ids = array();
			while($rs2 = imw_fetch_assoc($res2)){
				$chl_ids[] = $rs2['chl_ids'];
			}
			if(count($chl_ids)>0) $npi_chl_array[$npi] = implode(',',$chl_ids);
		}
		//pre($npi_chl_array);
		$return_array = array();
		foreach($npi_chl_array as $npi=>$chl_ids){
			$q3 = "SELECT GROUP_CONCAT(charge_list_id) AS seprated_CHLids, 
						  ic.institutional_type, 
						  GROUP_CONCAT(DISTINCT(pcl.".$InsCompField.")) AS  posted_claim_ins_ids, 
						  ic.ins_type 
					FROM patient_charge_list pcl 
					JOIN insurance_companies ic ON (pcl.".$InsCompField." = ic.id) 
					WHERE pcl.charge_list_id IN (".$chl_ids.") GROUP BY (concat(ic.institutional_type,ic.ins_type))";
			$res3 = imw_query($q3); 
			//echo '<br>'.imw_num_rows($res3).' :::: '.$q3.'<br>'.imw_error().'<hr>';
			if($res3 && imw_num_rows($res3)>0){	
				while($rs3 = imw_fetch_assoc($res3)){
					//pre($rs3);echo $npi;
					$inst_type = $rs3['institutional_type'];
					unset($rs3['institutional_type']);
					//echo $npi.'::'.$inst_type.'<hr>';
					$return_array[$npi][$inst_type][] 			= $rs3;
					unset($inst_type);
				}
			}
		}
		//pre($return_array,1);
		return $return_array;
	}
	
	/*COUNT TODAY'S BATCH FILE; USED TO NAME BATCH FILE*/
	function count_today_batch_files(){
		$q1 	= "SELECT COALESCE(MAX(batch_file_id),0) AS max_id FROM batch_file_detail WHERE submit_date = '".date('Y-m-d')."'";
		$res1 	= imw_query($q1);
		$max	= 0;
		if($res1 && imw_num_rows($res1)==1){
			$rs1 = imw_fetch_assoc($res1);
			$max = $rs1['max_id'];
			if($max > 0){
				$max = $max+1;
				$max = $this->padd_string($max,4,0);
				return $max;
			}
		}
		imw_query("TRUNCATE batch_file_detail");
		return '0001';
	}
	
	
	
	/**********************CLEARING HOUSE RELATED FUNCTIONS BELOW****************/
	function ClearingHouse(){
		$q = "SELECT * FROM clearing_houses WHERE status='1' ORDER BY id DESC";
		$res = imw_query($q);
		if($res && imw_num_rows($res)>0){
			$cl_array = array();
			while($rs = imw_fetch_assoc($res)){
				$cl_array[] = $rs;
			}
			return $cl_array;
		}
		return false;
	}
	
	function getCLreplyID($ClaimFileID){
		$q = "SELECT Emdeon_reply_id FROM emdeon_reply WHERE batch_file_submitte_id = '$ClaimFileID' ORDER BY Emdeon_reply_id DESC LIMIT 0,1";
		//echo '<hr>'.$q.'<hr>';
		$res = imw_query($q);
		if($res && imw_num_rows($res)>0){
			$rs	= imw_fetch_assoc($res);
			return $rs['Emdeon_reply_id'];
		}
		return false;
	}
	
	function submitClaimFile($CL_rs,$ClaimFile,$ClaimData,$Group_rs){
		$CL_mode 		= $CL_rs['connect_mode'];
		$CL_url			= ($CL_mode=='T') ? $CL_rs['test_url'] : $CL_rs['prod_url'];
		
		$CL				= $CL_rs['abbr'];
		
		//MAKING CLAIM FILE NAME
		$CL_filename 	= substr($ClaimFile,0,-4).'.zip';
		
		//CLAIM FILE STORE LOCATION BEFORE UPLOAD
		$CL_fileroot	= $this->oSaveFile->upDir.'/BatchFiles';
		if(!is_dir($CL_fileroot) || !file_exists($CL_fileroot)){
			mkdir($CL_fileroot,0777,true);
		}
		
		//CHANGING FILE NAME; CLEARING HOUSE SPECIFIC		
		if($CL == 'PI'){
			$CL_filename= substr($ClaimFile,0,-4).'.clm';
		}
		
		//MAKING FINAL FILE NAME IWTH PATH; WRITING CLAIM DATA TO IT.
		$CL_filepath	= $CL_fileroot.'/'.$CL_filename;
		file_put_contents($CL_filepath,stripslashes($ClaimData));
		
		//------CLEARING HOUSE CREDENTIALS--
		$CL_userId 			= trim($Group_rs['user_id']);
		$CL_passWord 		= trim($Group_rs['user_pwd']);
		
		switch($CL){
			case 'EMD': {//EMDEON CASE
				if(file_exists($CL_filepath)){
					$loginfields['wsMessageType'] = 'MCD';
					if($CL_mode=='T') $loginfields['wsMessageType'] = 'MCT';
					
					if($Group_rs['group_institution']==1 && in_array(strtolower($billing_global_server_name), array('hammad_iasc'))){
						$loginfields['wsMessageType'] = str_replace('M','H',$loginfields['wsMessageType']);
					}
					$cfile = new CURLFile($CL_filepath);
					$loginfields['wsUserID'] = $CL_userId;
					$loginfields['wsPassword'] = $CL_passWord;
					$loginfields['wsPutFile'] = $cfile;
					$cur 		= curl_init();//echo $CL_url."ITS/post.aspx".'<br>';
					curl_setopt($cur,CURLOPT_URL,$CL_url."ITS/post.aspx");
					curl_setopt($cur, CURLOPT_SSL_VERIFYPEER, FALSE);
					curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
					//curl_setopt($cur, CURLOPT_SAFE_UPLOAD,TRUE);
					curl_setopt($cur, CURLOPT_POSTFIELDS, $loginfields); 
					$output	= curl_exec($cur);
					$error	= curl_error($cur);//var_dump($output);var_dump($error);pre( $loginfields);die;
					curl_close($cur);
					unlink($CL_filepath);
					return array('response'=>$output,'error'=>$error);
				}
				break;
			}
			case 'PI':{//PRACTICE INSIGHT
				if(file_exists($CL_filepath)){
					$raw = file($CL_filepath); // 1st arg is path and file name to send
					$ansi_in	= $raw[0];
					$logon		= $CL_userId;//"imwemr";
					$passwd		= $CL_passWord;//"MuUNWzZ3vk8q";
					$cur 		= curl_init($CL_url."transfer/upload.php");
					curl_setopt($cur, CURLOPT_POST, 1);
					curl_setopt($cur, CURLOPT_POSTFIELDS,$ansi_in);
					// The Post data is our file data to upload
					curl_setopt($cur, CURLOPT_USERPWD, $logon.":".$passwd);
					curl_setopt($cur, CURLOPT_SSL_VERIFYPEER, FALSE);
					curl_setopt($cur, CURLOPT_SSL_VERIFYHOST, FALSE);
					curl_setopt($cur, CURLOPT_RETURNTRANSFER, TRUE);
					curl_setopt($cur, CURLOPT_TIMEOUT, 300);
					$output	=  curl_exec($cur);
					$error	= curl_error($cur);
					curl_close($cur);
					unlink($CL_filepath);
					//--------MODIFYING CONDITION OUTPUT--
					if(stripos($output,'duplicate file')>= 0){
						$tmp_output = explode('(',$output);
						$output = trim($tmp_output[0]);
					}
					return array('response'=>$output,'error'=>$error);
				}
			}
		}
	}
	
	function checkCLresponseType($response){
		$response = strtoupper($response);
		if(strpos(strtoupper($response), "ST*997*") !== false || strpos(strtoupper($response), "ST*999*") !== false){
			return '999';
		}else if(strpos(strtoupper($response), "ST*277*") !== false){
			return '277';
		}else if(strpos(strtoupper($response), "ST*271*") !== false){
			return '271';
		}else{
			return 'text';
		}
	}
	
	function log837CLresponse($CL_rs,$ClaimFile,$ClaimFileID,$Group_rs,$response){
		$CL				= $CL_rs['abbr'];
		$process999		= false;
		$respType		= 'text';
		$report_id		= 0;
		//CHECK IF CLEARING HOUSE REPLY EXISITS FOR THIS CLAIM BATCH FILE
		$CL_reply_id	= $this->getCLreplyID($ClaimFileID);
		
		switch($CL){
			case 'EMD':{
				$response_arr 	= explode(',',$response);
				$sent_to		= $response_arr[0];
				$reference		= $response_arr[1];
				$date			= $response_arr[2];
				$subject		= $response_arr[3];
				$size			= $response_arr[4];
				$response		= $response_arr[5];
				break;
			}
			case 'PI':{
				$respType = $this->checkCLresponseType($response);
				$sent_to		= 'PI';
				$reference		= '';
				$date			= date('D M d h:i:s Y'); //Mon Aug 25 05:50:51 2008
				$subject		= $ClaimFile;
				$size			= '';
				$response		= $response;
				if($respType=='999'){//save report also.
					$process999 = true;					
				}
				break;
			}
		}
		
		//--INSERT/UPDATE CLEARING HOUSE REPLY LOG-----
		$CL_reply_q 	= "INSERT INTO emdeon_reply SET ";
		$CL_reply_q_end	= "";
		if($CL_reply_id){
			$CL_reply_q 	= "UPDATE emdeon_reply SET ";
			$CL_reply_q_end	= " WHERE Emdeon_reply_id = '$CL_reply_id'";
		}
		
		$CL_reply_q .= "file_name 				= '$ClaimFile', 
						batch_file_submitte_id 	= '$ClaimFileID',
						file_from				= '$CL', 
						file_to					= '$sent_to', 
						file_Reference			= '$reference', 
						file_date				= '$date', 
						file_subject			= '$subject', 
						file_size				= '$size', 
						file_response			= '$response'";
		$CL_reply_q .= $CL_reply_q_end;			
		$CL_reply_res = imw_query($CL_reply_q);
			
		if($process999){
			//--FIRST INSERT THE 999/997 DATA TO TABLE----
			$CL_rpt_q = "INSERT INTO emdeon_reports SET 
						 ws_file_name		= '999.ClaimFileReport', 
						 report_data		= '".$response."', 
						 report_recieve_date= '".date('Y-m-d H:i:s')."', 
						 operator_id		= '".$this->authId."', 
						 wsUserID 			= '".$Group_rs['user_id']."',
						 report_status		= '0', 
						 group_id 			= '".$Group_rs['gro_id']."'";
			$CL_rpt_res	= imw_query($CL_rpt_q);
			$report_id	= imw_insert_id();
			if($CL_rpt_res){
				
			}
			
		}
		return array('response'=>'OK','response_text'=>$response,'report_type'=>$respType,'report_id'=>$report_id);	
	

	}
	
	function SaveCommercialReportsList($output,$Group_rs,$CL){
		switch($CL){
			case 'PI':{//277_21758962I1630030C2380.277|04/07/2015|277 Response File;
				if(strpos($output,';')){
					$files = explode(';',$output);
					foreach($files as $RptFile){
						if(empty($RptFile)) continue;
						$fileElems 	= explode('|',$RptFile);
						$fileName 	= $fileElems[0];
						/******SKIP THE 835 FILES****/
						$fileExt	= substr($fileName,-4);//echo ($fileExt.'<br>');
						if($fileExt=='.835') continue;
						
						$fileDate 	= $fileElems[1];
						
						$res1 = imw_query("SELECT emdeon_report_id FROM emdeon_reports WHERE LOWER(ws_file_name) = '".strtolower($fileName)."' LIMIT 1");
						if($res1 && imw_num_rows($res1)==0){
							$q = "INSERT INTO emdeon_reports SET 
								  ws_file_name		= '".$fileName."', 
								  report_data		= '',
								  report_recieve_date = '".date('Y-m-d H:i:s')."',
								  operator_id	 	= '".$this->authId."',
								  report_status		= 0, 
								  Online_Url 		= '',
								  wsUserID 			= '".$Group_rs['user_id']."',
								  wsPassword 		= '',
								  wsMessageType 	= '',
								  wsGetFile 		= '',
								  group_id			= '".$Group_rs['gro_id']."'";
							$res = imw_query($q);
							$report_id = imw_insert_id();
							$this->getPIreportData($fileName,$Group_rs,$report_id);
						}
					}					
				}
				break;
			}
		}
		
	}
	
	function getPIreportData($fileName,$Group_rs,$report_id){		
		$ClearingHouse	= $this->ClearingHouse();
		$CL		 		= $ClearingHouse[0]['abbr'];
		$CL_mode 		= $ClearingHouse[0]['connect_mode'];
		$CL_url			= ($CL_mode=='T') ? $ClearingHouse[0]['test_url'] : $ClearingHouse[0]['prod_url'];
		
		//-------SKIPPING LOOP IF ANY OF THE REQUIRED DATA IS MISSING------
		if(trim($Group_rs['user_id'])!='' && trim($Group_rs['user_pwd'])!=''){
			$cur = curl_init($CL_url."transfer/download.php?file=".$fileName);
			curl_setopt($cur, CURLOPT_USERPWD, $Group_rs['user_id'].":".$Group_rs['user_pwd']);
			curl_setopt($cur, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($cur, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($cur, CURLOPT_RETURNTRANSFER, TRUE);
			$output	=  curl_exec($cur);
			$error	= curl_error($cur);
			curl_close($cur);
			//echo $output.'<hr>'.$error.'<hr><hr><hr>';
			if(strtolower(substr($output,0,5))=='error'){//if text started with ERROR...
				$report_data = $output.'<br><br>This report is either already downloaded from clearing house or not prepared yet.';
				$valid_response = false;
			}else if(!empty($error)){//If any curl error found..
				$report_data = $error;
				$valid_response = false;
			}else{
				if($output!='' && $error==''){
					$reportDetails = $this->SaveCommercialReport($output,$report_id,$CL);
				}
			}
		}
	}
	
	function SaveCommercialReport($output,$report_id,$CL){
		switch($CL){
			case 'PI':{//277_21758962I1630030C2380.277|04/07/2015|277 Response File;
				$q = "UPDATE emdeon_reports SET 
					  report_data		= '".addslashes(htmlentities($output))."',
					  operator_id	 	= '".$this->authId."' 
					  where emdeon_report_id = '".$report_id."'";
				$res = imw_query($q);
				return $this->getEmdeonReport($report_id);
				break;
			}
		}
		
	}
	
	function makePI270request($insRecId, $from="insTab", $patId = 0, $imedic_DOS = "",$sch_id=""){
		$PI_RTE_DATA = '';
		$RTEerror	 = array();
		$patient_id = '';
		$imedic_DOS		= '';
		$defaultGroup	= '';
		/*
		ELIGIBILITY*Dr Smith*MCI*01/08/2018 
		19999.1*01/08/2018*TAYLOR*RICH*JOE*10/28/56*U0999495102*0101010101*01*TAYLOR*TAMMY*M*10/26/52***CIGNA HMO*62308*UPIN*MEDICAL CENTER*MEDICAL SENDER*MESSAGE TEXT*M*F*30**
		*/
		$this->HQFacility		= $this->get_facility_detail('','yes');
		$defaultGroup			= $this->HQFacility['default_group'];
		if(!empty($defaultGroup)){
			$groupDetails			= $this->get_groups_detail($defaultGroup);
			if(is_array($groupDetails)){
				if($groupDetails['group_NPI']==''){
					$RTEerror[] = ' - Group NPI is missing.';
				}
				$PI_RTE_HEADER		= array();
				$PI_RTE_HEADER[0]	= 'ELIGIBILITY';
			$PI_RTE_HEADER[1]	= addslashes(substr($groupDetails['name'],0,60));
			$PI_RTE_HEADER[2]	= 'Practice Insight';
			$PI_RTE_HEADER[3]	= date('m/d/Y');
			
				$PI_RTE_DATA	.= implode('*',$PI_RTE_HEADER)."\n";
			
				/***GETTING PATIENT INSURNACE DATA DETAILS****/
				$SubscriberData	= $this->getInsuranceData($insRecId);
				if(is_array($SubscriberData)){
					/***GETTING INSURANCE/PAYER DETAILS ***/
					$PayerData		= $this->getInsCompDetails($SubscriberData['provider']);
					if(is_array($PayerData)){
						if(empty($PayerData['emdeon_payer_eligibility'])){
							$PayerData['emdeon_payer_eligibility'] = $PayerData['Payer_id_pro'];
						}
						if(empty($PayerData['emdeon_payer_eligibility'])){
							$PayerData['emdeon_payer_eligibility'] = $PayerData['Payer_id'];
						}
						if(!empty($PayerData['emdeon_payer_eligibility'])){
							/***GETTING PATIENT DETAILS ****/
							$patient_id		= $SubscriberData['pid'];
							$dbInsDataTypePriSec = $SubscriberData['type']; 
							$PatientDataArr	= $this->get_patient_details(array('Patients'=>$patient_id));
							$PatientData	= $PatientDataArr[$patient_id];		
							if(is_array($PatientData)){
								$PI_RTE_BODY		= array();
								$PI_RTE_BODY[0]		= $patient_id; // PATIENT ACCOUNT NUMBER/PATIENT ID (R)
								$PI_RTE_BODY[1]		= ''; // APPOINTMENT DATE, FOR REFERENCE ONLY, CAN BE BLANK
								$PI_RTE_BODY[2]		= $PatientData['lname']; // PATIENT LAST NAME (R)
								$PI_RTE_BODY[3]		= $PatientData['fname']; // PATIENT FIRST NAME (R)
								$PI_RTE_BODY[4]		= $PatientData['mname']; // PATIENT MIDDLE INITIALS (R WHEN KNOWN)
								$patDOB				= date_create($PatientData['DOB']);
								$PI_RTE_BODY[5]		= date_format($patDOB,"m/d/Y"); // PATIENT DOB MM/DD/YYYY (R)
								
								$PI_RTE_BODY[6]		= $SubscriberData['policy_number']; // PATIENT INSURANCE ID/POLICY# FROM HEALTH ID CARD (R)
								$PI_RTE_BODY[7]		= ''; // PATIENT SS# (R IF PAT. INS.ID BLANK)
								$PI_RTE_BODY[8]		= $this->getSubsRelationCode($SubscriberData['subscriber_relationship']); // PATIENT RELATION TO INSURED - ANSI VALUE ONLY
								$PI_RTE_BODY[9]		= $SubscriberData['subscriber_lname']; // SUBSCRIBER LAST NAME
								$PI_RTE_BODY[10]	= $SubscriberData['subscriber_fname']; // SUBSCRIBER FIRST NAME
								$PI_RTE_BODY[11]	= $SubscriberData['subscriber_mname']; // SUBSCRIBER MIDDLE INITIALS
								$subDOB				= date_create($SubscriberData['subscriber_DOB']);
								$PI_RTE_BODY[12]	= date_format($subDOB,"m/d/Y"); // SUBSCRIBER DOB
								$PI_RTE_BODY[13]	= $SubscriberData['policy_number']; // SUBSCRIBER INSURANCE ID / POLICY# FROM HEALTH ID CARD
								$PI_RTE_BODY[14]	= ''; // SUBSCRIBER SSN# (R IF SUBS. INS.ID BLANK)
								
								$PI_RTE_BODY[15]	= addslashes($PayerData['name']); // PAYER NAME
								$PI_RTE_BODY[16]	= $this->padd_string($PayerData['emdeon_payer_eligibility'],5,'0','prefix'); // PAYER ID NUMBER (R)
								
								$PI_RTE_BODY[17]	= str_replace(array('-',' '),'',$groupDetails['group_NPI']); // PROVIDER NPI (R)
								$PI_RTE_BODY[18]	= addslashes(substr($groupDetails['name'],0,60));; // PROVIDER LAST NAME (R)
								$PI_RTE_BODY[19]	= addslashes(substr($groupDetails['name'],0,60));; // PROVIDER FIRST NAME (R)
								$PI_RTE_BODY[20]	= ''; // MESSAGE (UPTO 2000 CHARS). LOADS INTO THE NOTES FIELD.
								$PI_RTE_BODY[21]	= substr($PatientData['sex'],0,1); // PATIENT GENDER (M/F)
								$PI_RTE_BODY[22]	= substr($SubscriberData['subscriber_sex'],0,1); // SUBSCRIBER GENDER (M/F)
								$PI_RTE_BODY[23]	= '30'; // SERVICE TYPE 1 OR 2 CHARS. DEFAULTS TO 30 (HEALTH BENEFIT PLAN)
								$PI_RTE_BODY[24]	= $SubscriberData['group_number']; // PATIENT GROUP NUMBER
								$PI_RTE_BODY[25]	= $SubscriberData['group_number']; // SUBSCRIBER GROUP NUMBER
								
								$PI_RTE_DATA	.= implode('*',$PI_RTE_BODY).chr(13);
							}else{
								$RTEerror[] = ' - Unable to fetch Patient details.';
							}
						}else{
							$RTEerror[] = ' - Eligibility Payer ID missing for Payer.';
						}
					}else{
						$RTEerror[] = ' - Unable to fetch Payer (Insurance Company) details.';
					}
				}else{
					$RTEerror[] = ' - Unable to fetch Subscriber details.';
				}
			}else{
				$RTEerror[] = ' - Unable to fetch Provider (Group) details.';
			}
		}else{
			$RTEerror[] = ' - Default Group not assigned with HQ facility.';
		}
		return array($PI_RTE_DATA, $SubscriberData['pid'], $insRecId, $imedic_DOS, $defaultGroup, $RTEerror,$dbInsDataTypePriSec);
	}
	
	function getSubsRelationCode($rel){
		$code = '18';
		 switch(strtolower($rel)){
			case 'self': 		$code = '18';		break;
			case 'son': 		$code = '19';		break;
			case 'daughter': 	$code = '19';		break;
			case 'mother': 		$code = 'G8';		break;
			case 'father': 		$code = 'G8';		break;
			case 'guardian':	$code = '18';		break;
			case 'employee': 	$code = '20';		break;
			case 'spouse': 		$code = '01';		break;
		 }
		 return $code;
	}
	
	function parseHDRreport($report_data,$extension){
		if(strtolower(substr($report_data,0,4))=='hdr*'){
			$lines = explode("\n", $report_data);
			$return_data = array();
			$ack_cnt = $cst_cnt = 0;
			foreach($lines as $line){
				$line_items = explode('*',$line);
				//echo $line_items[0].' :: ';
				switch($line_items[0]){
					case 'HDR':{
						if($extension=='.RSP'){$report_type = 'Response from Clearing House';}
						if($extension=='.INS'){$report_type = 'Response with Payer Status';}						
						$return_data['HDR']['Report Type'] 								= $report_type;
						if(!empty($line_items[1])) $return_data['HDR']['Report By'] 	= $line_items[1];
						if(!empty($line_items[2])) $return_data['HDR']['Record For'] 	= $line_items[2];
						if(!empty($line_items[3])) $return_data['HDR']['Report Date']	= substr($line_items[3],4,2).'-'.substr($line_items[3],6,2).'-'.substr($line_items[3],0,4);
						break;	
					}
					case 'ACK':
					case 'CST':{
						//ACK*280166927**345153**ACK*20180112**CLAIM FORWARDED TO PAYER
						//CST*280166927**345153**ACK-999*01/15/2018**01/15/2018: Claim Acknowledged in an Accepted 999 Batch (Claim Acknowledged in an Accepted 999 Batch)
						if($line_items[5]=='ACK'){$line_items[5] = 'Acknowledged, may still be rejected later on.';}
						if($line_items[5]=='REJ'){$line_items[5] = 'Claim is rejected.';}
						if($line_items[5]=='INF'){$line_items[5] = 'Information only.';}
						if($line_items[5]=='WRN'){$line_items[5] = 'Warning.';}
						$line_items[6] = trim(str_replace(array('/','-'),'',$line_items[6]));
						if($line_items[0]=='CST'){
							$line_items[6] = substr($line_items[6],0,2).'-'.substr($line_items[6],2,2).'-'.substr($line_items[6],4,4);
						}else{
							$line_items[6] = substr($line_items[6],4,2).'-'.substr($line_items[6],6,2).'-'.substr($line_items[6],0,4);
						}
												
						if(!empty($line_items[1])) $return_data[$line_items[0]][$ack_cnt]['PI Claim ID'] 	= $line_items[1];
						if(!empty($line_items[2])) $return_data[$line_items[0]][$ack_cnt]['Encounter ID']	= $line_items[2];
						if(!empty($line_items[3])) $return_data[$line_items[0]][$ack_cnt]['Patient ID'] 		= (int)$line_items[3];
						if(!empty($line_items[4])) $return_data[$line_items[0]][$ack_cnt]['Payer Claim ID'] 	= $line_items[4];
						if(!empty($line_items[5])) $return_data[$line_items[0]][$ack_cnt]['Response Type']	= $line_items[5];
						if(!empty($line_items[6])) $return_data[$line_items[0]][$ack_cnt]['Response Date'] 	= $line_items[6];
						if(!empty($line_items[8])) $return_data[$line_items[0]][$ack_cnt]['Response']		= $line_items[8];
						if(!empty($line_items[7]) && !empty($return_data[$line_items[0]][$ack_cnt]['Response']))
													$return_data[$line_items[0]][$ack_cnt]['Response']		.= ' ('.$line_items[7].')';
						$ack_cnt++;
						break;	
					}
					case 'TRL':{
						//TRL*PRACTICE INSIGHT, LLC*IMW_KOCHEYE*20180112*1
						//TRL*BCBSRI - Rhode Island*imw_kocheye*20180115*1
						if(!empty($line_items[1])) $return_data['TRL']['Report By'] 	= $line_items[1];
						if(!empty($line_items[2])) $return_data['TRL']['Record For'] 	= $line_items[2];
						if(!empty($line_items[3])) $return_data['TRL']['Report Date']	= substr($line_items[3],4,2).'-'.substr($line_items[3],6,2).'-'.substr($line_items[3],0,4);
						if(!empty($line_items[2])) $return_data['TRL']['Count'] 		= $line_items[4];
						break;	
					}
				}
			}
			return $return_data;

		}else return false;
	}
	
	function getCommentRTEdata($html){
		$rcomments = array();
		$comments = array();
		if (preg_match_all('#<\!--(.*?)-->#is', $html, $rcomments)) {
			foreach ($rcomments as $c) {
				if(count($comments)>0) continue;
				$comments[] = $c[0];
			}
			$comments = str_replace(array('<!--','-->'),'',$comments[0]);
			$comments = explode("\n",$comments);
			for($i=0;$i<count($comments);$i++){if(substr($comments[$i],0,4)=='ISA*') {return($comments[$i]);break;}}
			return false;
		}
		return null;
	}
	
	function get_835_cas_codes($chld_id){
		$cas_segments = false;
		$q = "SELECT epp.charge_list_detail_id, epp.cas_type, epp.cas_code, epp.cas_amt, epatd.CLP_claim_status, ";
		$q.= "eprocd.SVC_provider_pay_amt FROM era_835_proc_posted epp ";
		$q.= "JOIN era_835_proc_details eprocd ON (eprocd.835_Era_proc_Id = epp.era_835_proc_id) ";
		$q.= "JOIN era_835_patient_details epatd ON (epatd.ERA_patient_details_id = eprocd.ERA_patient_details_id) ";
		$q.= "WHERE epp.charge_list_detail_id = '".$chld_id."' AND epp.ins_type = '1' ";
		$q.= "ORDER BY  eprocd.SVC_provider_pay_amt desc,epp.chk_date DESC, id DESC LIMIT 1";
		$res = imw_query($q);
		if($res && imw_num_rows($res)==1){
			$cas_segments = '';
			$rs = imw_fetch_assoc($res);
			if(trim($rs['cas_type']) != '' && trim($rs['cas_code']) != ''){
				$ct_arr	= explode(', ',$rs['cas_type']);
				$cc_arr = explode(', ',$rs['cas_code']);
				$ca_arr = explode(', ',$rs['cas_amt']);
				if(count($ct_arr)>=1 && (count($ct_arr)==count($cc_arr) && count($ct_arr)==count($ca_arr))){
					//MULTI CAS CODES
					$cas_type_vals = array();
					foreach($ct_arr as $ct){
						$cas_type_vals[$ct] = '';
					}
					for($i=0;$i<count($ct_arr);$i++){
						//$cas_segments .= 'CAS*'.$ct_arr[$i].'*'.$cc_arr[$i].'*'.$ca_arr[$i].'*1~';
						$cas_type_vals[$ct_arr[$i]] .= '*'.$cc_arr[$i].'*'.$ca_arr[$i].'*1';
					}
					
					foreach(array_unique($ct_arr) as $ct){
						if($cas_type_vals[$ct] != ''){
							$cas_segments .= 'CAS*'.$ct.$cas_type_vals[$ct].'~';
						}
					}
				}else if(count($ct_arr)==1 && (count($ct_arr)<count($cc_arr) && count($ct_arr)<count($ca_arr))){
					//SINGLE CAS TYPE; MULTI CODE & ADJUSTMENT AMOUNT
					$cas_segments .= 'CAS*'.$ct_arr[0];
					for($i=0;$i<count($ct_arr);$i++){
						$cas_segments .= '*'.$cc_arr[$i].'*'.$ca_arr[$i].'*1';
					}
					$cas_segments .= '~';
				}else{
					$cas_segments .= 'CAS*'.$ct_arr[0].'*'.$cc_arr[0].'*'.$ca_arr[0].'*1~';
				}
			}
		}
		return $cas_segments;
	}
	
	/******FUNCTION DETERMINE SV101-07 NEED TO BE EMPTY OR NOT*****/
	function is_not_otherwise_classified_code($jcode){
		$NOC_JCODES = array('J0220', 'J0256', 'J0833', 'J1566', 'J1566', 'J1599', 'J1729', 'J3301', 'J3490', 'J3590', 'J7192', 'J7195', 'J7199', 'J7312', 'J7599', 'J7699', 'J7799', 'J7999', 'J8498', 'J8499', 'J8597', 'J8655', 'J8999', 'J9020', 'J9999','L8699','J9035','J0585','J0178','J2778','66999','67999','J7313','J1096','J1097','J0179');
		if(in_array($jcode,$NOC_JCODES)) return true;
		else return false;
	}
	
	/******GET LAST USED PAYER FOR ENCOUNTER JUST BEFORE BATCH CREATION; FROM POSTED RECORD TABLE**/
	function get_payer_when_batch_created($eid,$pid,$batch_file_id,$pri_sec){
		$q1 = "SELECT action_date FROM batch_file_log bfl WHERE batch_file_submitte_id='$batch_file_id' AND action IN ('created','regenrate') ORDER BY action_date DESC LIMIT 0,1";
		$res1 = imw_query($q1);
		if($res1 && imw_num_rows($res1)==1){
			$rs1 = imw_fetch_assoc($res1);
			$dt	 = $rs1['action_date'];
			$posted_for = '1';
			if($pri_sec=="secondary") $posted_for = '2';
			$q2 = "SELECT ins_comp_id FROM posted_record WHERE encounter_id='".$eid."' AND patient_id='".$pid."' AND posted_for='".$posted_for."' ";
			$q2.= "AND CONCAT (posted_date,' ',posted_time) < '".$dt."' ORDER BY CONCAT (posted_date,' ',posted_time) DESC LIMIT 0,1";
			//echo $q2.'<hr>';
			$res2 = imw_query($q2);
			if($res2 && imw_num_rows($res2)==1){
				$rs2 = imw_fetch_assoc($res2);
				$ins_comp_id = $rs2['ins_comp_id'];
				if(!empty($ins_comp_id) && $ins_comp_id>0){
					$res3 = imw_query("SELECT in_house_code FROM insurance_companies WHERE id = '$ins_comp_id' LIMIT 0,1");
					if($res3 && imw_num_rows($res3)==1){
						$rs3 = imw_fetch_assoc($res3);
						if(!empty($rs3['in_house_code'])) return $rs3['in_house_code'];
					}else return false;
				}else return false;
			}else return false;			
		}else return false;
	}
	
}//end of class.
?>
