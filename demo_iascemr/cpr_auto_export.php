<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
session_start();
include("common/conDb.php"); 
error_reporting(0);
ini_set("display_errors",0);

//get list of anesthesiologist to generate files
$anesQry=imw_query("SELECT * FROM users 
					  WHERE user_type = 'Anesthesiologist' 
					  		AND deleteStatus != 'Yes' 
							AND cpr_code != '' 
							AND cpr_db != '' 
					  ORDER BY lname");
if(imw_num_rows($anesQry)>=1)
{
	//assign default from and to date
	$from_date 	= $to_date 	= date('Y-m-d');
	
	//------getting all procedure (surgerycenter)------
	$arr_procs_mapping	= array();
	$procs_query = "SELECT procedureId, name, code FROM procedures";
	$procs_res	 = imw_query($procs_query);
	if($procs_res && imw_num_rows($procs_res)>0)
	{
		while($procs_rs = imw_fetch_assoc($procs_res)){
			$procs_rs_id							 = $procs_rs['procedureId'];
			$arr_procs_mapping[$procs_rs_id]['name'] = $procs_rs['name'];
			$arr_procs_mapping[$procs_rs_id]['cpt']  = $procs_rs['code'];
		}
	}
	
	//---getting patient location (surgerycenter location)---
	$location_result = imw_query("SELECT npi,name FROM surgerycenter LIMIT 0,1");
	$location_rs	 = imw_fetch_assoc($location_result);
	$patient_location= '^^^^'.preg_replace("/[^0-9]/","",$location_rs['npi']).'^^^'.$location_rs['name'];
	
	//---getting diagnosis codes-----
	$arr_diags_mapping = array();
	$diags_query = "SELECT diag_id,diag_code FROM diagnosis_tbl";
	$diags_result= imw_query($diags_query);
	if($diags_result && imw_num_rows($diags_result)>0){
		while($diags_rs = imw_fetch_assoc($diags_result)){
			$arr_diags_mapping[$diags_rs['diag_id']] = explode(', ',$diags_rs['diag_code']);
		}
	}
	
	while($anesRow=imw_fetch_array($anesQry))
	{
		
		
		$anesthesiologist 		= $anesRow["usersId"];
		$cpr_code 				= $anesRow["cpr_code"];
		$cpr_db 				= $anesRow["cpr_db"];
		$anesthesiologistQry 	= " AND  pc.anesthesiologist_id IN(".$anesthesiologist.") ";
		
		
$pcQry = "SELECT pc.patientConfirmationId as conf_id, pc.ascId, pc.dos, stt.appt_id, CONCAT(u.cpr_code,'^',u.lname,'^',u.fname,'^',u.mname) as anes_by 
				FROM patientconfirmation pc
				JOIN stub_tbl stt ON (stt.patient_confirmation_id = pc.patientConfirmationId) 
				LEFT JOIN users u ON (u.usersId = pc.anesthesiologist_id) 
				WHERE pc.ascId !='0' AND pc.anesthesiologist_id !='0' ".$anesthesiologistQry."
				AND pc.dos BETWEEN '".$from_date."' AND '".$to_date."'";
				
		$pcRes = imw_query($pcQry);
		$surgDataArr = $ascIdArr = array();
		if(imw_num_rows($pcRes)>0) {
			while($pcRow = imw_fetch_array($pcRes)) {
				$ascIdArr[]									= $pcRow["ascId"];
				$surgDataArr[$pcRow["ascId"]]["dos"] 		= $pcRow["dos"];
				$surgDataArr[$pcRow["ascId"]]["appt_id"] 	= $pcRow["appt_id"];
				$surgDataArr[$pcRow["ascId"]]["conf_id"] 	= $pcRow["conf_id"];
				$surgDataArr[$pcRow["ascId"]]["anes_by"] 	= $pcRow["anes_by"];
			}
			$ascId = implode(",",$ascIdArr);
			unset($ascIdArr);
			if(!$ascId) {$ascId='0';}
			include('connect_imwemr.php');
			/*-GETTING PATIENT ID AND ASC ID FROM SUPERBILL---*/
			$qry = "SELECT DISTINCT(patientId), ascId FROM superbill WHERE ascId!='0' AND ascId IN(".$ascId.")";
			$res = imw_query($qry);
			if(imw_num_rows($res)>0) {
				while($row = imw_fetch_array($res)){
					$surgDataArr[$row["ascId"]]["iAsc_ptid"] = $row["patientId"];
				}
			}
		}
		if(count($surgDataArr)>0){
			$file_root = "admin/pdfFiles/cpr_hl7/";
			if(!file_exists($file_root) || !is_dir($file_root)){
				mkdir($file_root);
			}
			
			//creating/selecting partiucular anesthesiologist folder
			//commented on 19 nove 2014
			/*$file_subfloder=$cpr_db;//"HL7_".$anesthesiologist;
			if(!file_exists($file_root.$file_subfloder) || !is_dir($file_root.$file_subfloder)){
				mkdir($file_root.$file_subfloder);
			}
			
			$file_date_val = str_replace('-','',$from_date);
			$file="/CPR_HL7_".$anesthesiologist."_".$file_date_val.".hl7";
			$text_file_name = $file_root.$file_subfloder.$file;
			
			//assign folder and file name to array to upload it later
			$UPLOAD_FILE_ARR[$file_subfloder]=$file;
			
			if(file_exists($text_file_name)==true){unlink($text_file_name);}*/
			if(!isset($PV1_Facility_String)){$PV1_Facility_String = '';}
			$ignoreAuth = true;
			$newMsgId	= 'PRAC';
			require_once("../".$imwDirectoryName."/interface/patient_info/CLS_makeHL7.php");
			$makeHL7		= new makeHL7;
			$makeHL7->authId= $_SESSION['loginUserId'];
			foreach($surgDataArr as $ascId=>$mainDataArr){
				$cpr_hl7_a08_msg = $cpr_hl7_p03_msg = '';
				$iasc_ptId					= $mainDataArr['iAsc_ptid'];
				$iasc_schId					= $mainDataArr['appt_id'];
				$iasc_dos					= $mainDataArr['dos'];
				$surg_conf_id				= $mainDataArr['conf_id'];
				$anesthesia_by				= $mainDataArr['anes_by'];
				$i++;
				$makeHL7->sch_id			= $iasc_schId;
				$makeHL7->date_of_service	= $iasc_dos;
				include("common/conDb.php");
				$newMsgId					= newMessageUniqueId();
				include('connect_imwemr.php');
				/*-GETTING REFERRING PHYSICIAN---*/
				$qry2 = "SELECT pd.id as patient_id, 
					CONCAT(rf2.NPI,'^',rf2.LastName,'^',rf2.FirstName,'^',rf2.MiddleName) AS Referring_Physician 
					FROM patient_data pd 
					LEFT JOIN refferphysician rf2 ON (rf2.physician_Reffer_id = pd.primary_care_id) WHERE pd.id = '".$iasc_ptId."'";
				$res2 = imw_query($qry2);	
				if(imw_num_rows($res2)==1) {
					while($row2 = imw_fetch_array($res2)){
						$ref_phy_str = $row2["Referring_Physician"];
					}
				}
				$cpr_hl7_a08_msg			= $makeHL7->get_HL7msgCPR_p1($iasc_ptId,'Update_Patient','CPR',$newMsgId);
				$pv1_Segment_rs				= array();						
				$pv1_Segment_rs[]  		 	= 1;	//1 set_id
				$pv1_Segment_rs[]			= ''; 	//2
				$pv1_Segment_rs[]			= $PV1_Facility_String; 	//3 FACILITY DETAILS
				$pv1_Segment_rs[]			= ''; 	//4 
				$pv1_Segment_rs[]			= ''; 	//5
				$pv1_Segment_rs[]			= '';   //6 
				$pv1_Segment_rs[]			= $anesthesia_by;   //7  Provider
				$pv1_Segment_rs[]			= $ref_phy_str; // 8 Referring Physician.
				$this_segment = array();
				$this_segment['PV1']		= $pv1_Segment_rs;
				$cpr_hl7_a08_msg		   .= $makeHL7->Make_hl7_from_array($this_segment);
				$cpr_hl7_a08_msg2		    = $makeHL7->get_HL7msgCPR_p2($iasc_ptId,'Update_Patient','CPR');
				if($cpr_hl7_a08_msg2=='' || $iasc_ptId==0 || $iasc_ptId=='') continue;
				$cpr_hl7_a08_msg		   .= $cpr_hl7_a08_msg2;
				include("common/conDb.php");
				//SAVING ADT.
				$text_file_name_ADT='';
				$adtID='';
				
				//create log
				imw_query("insert into hl7_sent set patient_id='$iasc_ptId',
							msg='". imw_real_escape_string($cpr_hl7_a08_msg) ."',
							msg_type='ADT',
							saved_on='".date('Y-m-d H:i:s')."',
							operator='$_SESSION[loginUserId]',
							send_to='$cpr_db',
							sch_id='$iasc_schId'")or die(imw_error());
				$adtID=imw_insert_id();
				
				$text_file_name_ADT = "CPR_HL7_ADT".$anesthesiologist."_".$adtID.".hl7";
				//assign folder and file name to array to upload it later
				$UPLOAD_FILE_ARR[$cpr_db.'_~_'.$adtID]=$text_file_name_ADT;
				$fp = fopen($file_root.$text_file_name_ADT,"a+");
				$fw = fwrite($fp, $cpr_hl7_a08_msg);
				fclose($fp);
				
				$newMsgId2					= newMessageUniqueId();
				
				include('connect_imwemr.php');
				$cpr_hl7_p03_msg			= $makeHL7->get_HL7msgCPR_p1($iasc_ptId,'Detailed Financial Transaction','CPR',$newMsgId2);
				$pv1_Segment_rs				= array();						
				$pv1_Segment_rs[]  		 	= 1;	//1 set_id
				$pv1_Segment_rs[]			= ''; 	//2
				$pv1_Segment_rs[]			= $PV1_Facility_String; 	//3 FACILITY DETAILS
				$pv1_Segment_rs[]			= ''; 	//4 
				$pv1_Segment_rs[]			= ''; 	//5
				$pv1_Segment_rs[]			= '';   //6 
				$pv1_Segment_rs[]			= $anesthesia_by;   //7  Provider
				$pv1_Segment_rs[]			= $ref_phy_str; // 8 Referring Physician.
				$this_segment = array();
				$this_segment['PV1']		= $pv1_Segment_rs;
				$cpr_hl7_p03_msg		   .= $makeHL7->Make_hl7_from_array($this_segment);
				$cpr_hl7_p03_msg		   .= $makeHL7->get_HL7msgCPR_p2($iasc_ptId,'Detailed Financial Transaction','CPR');
				/*----GETTING PROCS AND DIAGS-----*/
				include("common/conDb.php");
				$disch_summ_query			= "SELECT CONCAT(DATE_FORMAT(dischargeSummarySheetDate,'%Y%m%d'),DATE_FORMAT(dischargeSummarySheetTime,'%h%m%s')) AS transaction_dt_tm,
											procedures_code, diag_ids FROM dischargesummarysheet WHERE confirmation_id = '$surg_conf_id'";
				$disch_summ_res				= imw_query($disch_summ_query);
				if($disch_summ_res && imw_num_rows($disch_summ_res)>0){
					/***GET ANESTHESIA TIME***/
					$anes_time_q = "SELECT CEIL(TIME_TO_SEC(TIMEDIFF(stopTime, startTime))/60) AS minutes, TIME_FORMAT(stopTime,'%H%i%s') AS stopTime, TIME_FORMAT(startTime,'%H%i%s') AS startTime FROM localanesthesiarecord WHERE confirmation_id = '$surg_conf_id' AND stopTime != '00:00:00' AND startTime != '00:00:00' HAVING minutes > 0";
					$anes_time_res = imw_query($anes_time_q);
					$anes_time_minutes = '00';
					settype($anes_time_minutes, "string");
					if(imw_num_rows($anes_time_res)>0){
						$anes_time_rs = imw_fetch_assoc($anes_time_res);
						$anes_time_minutes = $anes_time_rs['minutes'];
						$range_start_time  = $anes_time_rs['startTime'];
						$range_stop_time   = $anes_time_rs['stopTime'];
					}
					
					//Send DFT message.
					while($disch_summ_rs = imw_fetch_assoc($disch_summ_res)){
						$procedures_code		= $disch_summ_rs['procedures_code'];
						$diag_ids				= $disch_summ_rs['diag_ids'];
						$transcation_dt_tm		= $disch_summ_rs['transaction_dt_tm'];
						$all_procs				= explode(',',$procedures_code);
						$all_diags				= explode(',',$diag_ids);
						$All_Diags_str			= '';
						foreach($all_diags AS $dgId){
							if($All_Diags_str!='') $All_Diags_str .= '^';
							$All_Diags_str	   .= $arr_diags_mapping[$dgId][0];
						}
						
						$pr1_segment_str		= '';
						$range_start_time1		= $transcation_dt_tm;
						if(($range_start_time != '' && $range_start_time != '000000') && ($range_stop_time != '' && $range_stop_time != '000000')){
							$range_start_time1 = substr($transcation_dt_tm,0,8).$range_start_time.'^'.substr($transcation_dt_tm,0,8).$range_stop_time;
						}
						for($i=0;$i<count($all_procs);$i++){
							$ft1_Segment_rs		= array();						
							$ft1_Segment_rs[]   = $i+1;					//1 set_id
							$ft1_Segment_rs[]	= ''; 					//2
							$ft1_Segment_rs[]	= ''; 					//3
							$ft1_Segment_rs[]	= $range_start_time1; 	//4 transaction_date_time
							$ft1_Segment_rs[]	= ''; 					//5
							$ft1_Segment_rs[]	= 'CG'; 				//6 transaction type
							$ft1_Segment_rs[]	= $arr_procs_mapping[$all_procs[$i]]['cpt'];	//7 transcation code (procedure code)
							$ft1_Segment_rs[]	= ''; 					//8
							$ft1_Segment_rs[]	= ''; 					//9
							$ft1_Segment_rs[]	= '1'; 					//10 transaction quantity; always 1;
							$ft1_Segment_rs[]	= ''; 					//11
							$ft1_Segment_rs[]	= ''; 					//12
							$ft1_Segment_rs[]	= ''; 					//13
							$ft1_Segment_rs[]	= ''; 					//14
							$ft1_Segment_rs[]	= ''; 					//15
							$ft1_Segment_rs[]	= $patient_location;	//16 assigned patient location
							$ft1_Segment_rs[]	= ''; 					//17
							$ft1_Segment_rs[]	= 'O'; 					//18 patient type; I-Inpatient; O-Outpatient
							$ft1_Segment_rs[]	= $All_Diags_str; 		//19 diagnosis code(s); can left empty if providing DG1 segment
							$ft1_Segment_rs[]	= $anesthesia_by;		//20 performed by user
							$ft1_Segment_rs[]	= ''; 					//21
							$ft1_Segment_rs[]	= ''; 					//22
							$ft1_Segment_rs[]	= $ascId; 				//23 filler order number; assuming ascId												
							$ft1_Segment_rs[]	= ''; 					//24 entered by code
							$ft1_Segment_rs[]	= $arr_procs_mapping[$all_procs[$i]]['cpt']; 	//25 procedure code
							$ft1_Segment_rs[]	= ''; 					//26 modifiers						
							$this_segment = array();
							$this_segment['FT1']= $ft1_Segment_rs;
							$cpr_hl7_p03_msg   .= $makeHL7->Make_hl7_from_array($this_segment);
							
							$pr1_Segment_rs 	= array();
							$pr1_Segment_rs[] 	= $i+1;					//1 set id
							$pr1_Segment_rs[] 	= 'CPT';				//2 Procedure coding method; CPT always;
							$pr1_Segment_rs[] 	= $arr_procs_mapping[$all_procs[$i]]['cpt']; //3 Procedure code;
							$pr1_Segment_rs[] 	= '';					//4
							$pr1_Segment_rs[] 	= $transcation_dt_tm;	//5 Procedure date time;
							$pr1_Segment_rs[] 	= '';					//6
							$pr1_Segment_rs[] 	= '';					//7
							$pr1_Segment_rs[] 	= '';					//8
							$pr1_Segment_rs[] 	= $arr_diags_mapping[$all_diags[0]][0];					//9 Anethesia Code
							$pr1_Segment_rs[] 	= $anes_time_minutes;	//10 Anethesia Minutes
							$pr1_Segment_rs[] 	= '';					//11
							$pr1_Segment_rs[]	= $anesthesia_by;		//12 Procedure practitioner
							$pr1_Segment_rs[] 	= '';					//13
							$pr1_Segment_rs[] 	= '';					//14
							$pr1_Segment_rs[] 	= '';					//15
							$pr1_Segment_rs[] 	= '';					//16 Procedure code modifiers
							$this_segment = array();
							$this_segment['PR1']= $pr1_Segment_rs;
							$pr1_segment_str   .= $makeHL7->Make_hl7_from_array($this_segment);
						}
						$cpr_hl7_p03_msg   .= $pr1_segment_str;
						
						for($i=0;$i<count($all_diags);$i++){
							$dg1_Segment_rs		= array();						
							$dg1_Segment_rs[]   = $i+1;					//1 set_id
							$dg1_Segment_rs[]	= 'ICD9'; 				//2
							$dg1_Segment_rs[]	= $arr_diags_mapping[$all_diags[$i]][0]; //3 diagnosis code
							$dg1_Segment_rs[]	= $arr_diags_mapping[$all_diags[$i]][1]; //4 diagnosis description
							$dg1_Segment_rs[]	= $transcation_dt_tm;; 	//5 diagnosis date time
							$dg1_Segment_rs[]	= 'F'; 					//6 diagnosis type (F=final)
							$dg1_Segment_rs[]	= '';					//7 
							$dg1_Segment_rs[]	= ''; 					//8
							$dg1_Segment_rs[]	= ''; 					//9
							$dg1_Segment_rs[]	= ''; 					//10 
							$dg1_Segment_rs[]	= ''; 					//11
							$dg1_Segment_rs[]	= ''; 					//12
							$dg1_Segment_rs[]	= ''; 					//13
							$dg1_Segment_rs[]	= ''; 					//14
							$dg1_Segment_rs[]	= '0'; 					//15
							$dg1_Segment_rs[]	= $anesthesia_by;		//16 diagnosis clinician
							$dg1_Segment_rs[]	= 'D'; 					//17 diagnosis classification (D-Diagnosis)
							$this_segment = array();
							$this_segment['DG1']= $dg1_Segment_rs;
							$cpr_hl7_p03_msg   .= $makeHL7->Make_hl7_from_array($this_segment);
						}
					}
								
				}
				
				
				
				include("common/conDb.php");
				$text_file_name_DFT='';
				$dftID='';
				//create log
				imw_query("insert into hl7_sent set patient_id='$iasc_ptId',
							msg='". imw_real_escape_string($cpr_hl7_p03_msg) ."',
							msg_type='DFT',
							saved_on='".date('Y-m-d H:i:s')."',
							operator='$_SESSION[loginUserId]',
							send_to='$cpr_db',
							sch_id='$iasc_schId'")or die(imw_error());
				$dftID=imw_insert_id();
				
				$text_file_name_DFT = "CPR_HL7_DFT".$anesthesiologist."_".$dftID.".hl7";
				//assign folder and file name to array to upload it later
				$UPLOAD_FILE_ARR[$cpr_db.'_~_'.$dftID]=$text_file_name_DFT;
				$fp = fopen($file_root.$text_file_name_DFT,"a+");
				$fw = fwrite($fp, $cpr_hl7_p03_msg);
				fclose($fp);
			}
		}
	}//end of while loop here
	

	FUNC_uploadFiles($UPLOAD_FILE_ARR);
}//end of checking if we have records here


function FUNC_uploadFiles($UPLOAD_FILE_ARR){
	//if we have files then try to upload then
	if(sizeof($UPLOAD_FILE_ARR)>=1){
		###########################################################
		# transmission code starts here
		###########################################################

		$strServerIP = "webapps11.itnsusa.com";
		$strServerPort = "65522";
		$strServerUsername = "sshuser";
		$strServerPassword = "M2cq6{&Xx(B(!~B[x6HWpBHPcTggR764{aW+p)mr";
		$strTimeOut=30;//seconds
		
		/* Set the correct include path to 'phpseclib'. Note that you will need 
		   to change the path below depending on where you save the 'phpseclib' lib.
		   The following is valid when the 'phpseclib' library is in the same 
		   directory as the current file.
		*/
		
		set_include_path(get_include_path() . PATH_SEPARATOR . './phpseclib0.3.8');
		 
		include('Net/SFTP.php');
		
		if(sizeof($UPLOAD_FILE_ARR)>=1)
		{
			/* Change the following directory path to your specification */
			$local_directory = 'admin/pdfFiles/cpr_hl7/';//local file dir to upload files from
			$local_archive_directory = 'admin/pdfFiles/cpr_hl7/archive/';//local archive dir to move file after successful upload
			if(!file_exists($local_archive_directory) || !is_dir($local_archive_directory))//checking is archive dir exist if not then create it
			{
				mkdir($local_archive_directory);
			}
			$remote_directory = '/inbound/';//remote dir to upload files
			
			/* Add the correct FTP credentials below */
			$sftp = new Net_SFTP($strServerIP,$strServerPort,$strTimeOut);
			if (!$sftp->login($strServerUsername,$strServerPassword)) 
			{
				exit('Login Failed');
			} 
	
			foreach($UPLOAD_FILE_ARR as $key_val=>$file)
			{	
				$key_arr 			= explode('_~_',$key_val);
				$cpr_db 			= $key_arr[0];				
				$table_id 			= $key_arr[1];
				$success = false;
				if(file_exists($local_directory.$file))
				{
					try
					{
						//now check is that folder exist on server if not then create it
						if(!$sftp->file_exists($remote_directory))
						{
							//create directory
							$sftp->mkdir($remote_directory);
						}
					}
					catch (Exception $e) {
						echo 'Caught exception: ',  $e->getMessage(), "\n";
					}
					
				   //Upload the local file to the remote server 
					$success = $sftp->put($remote_directory.$cpr_db.'/'.$file, 
										$local_directory.$file, 
										 NET_SFTP_LOCAL_FILE);
										 
				  if($success)
				  {
					//move that file to archieve folder
					rename($local_directory.$file,$local_archive_directory.$file);
					//update sent log for file
					if($table_id)
					{
						imw_query("update hl7_sent set sent=1,
									sent_on='".date('Y-m-d H:i:s')."'
									where id=$table_id")or die(imw_error());
					}
				  }
				}
			}//end of foreach loop
			
			//-------------------
			//check is there any pending upload if yes then repeat upload
			if(imw_num_rows(imw_query("select sent from hl7_sent where sent=0")));
			{
				FUNC_uploadFiles($UPLOAD_FILE_ARR);
			}
		}//end of checking array records
			
		
		###########################################################
		# transmission code ends here
		###########################################################

	}
}

function newMessageUniqueId(){
	$res1 = imw_query("SELECT if(MAX(id) IS NULL,0,MAX(id))+1  as NewMsgId FROM hl7_sent");
	if($res1 && imw_num_rows($res1)==1){
		$rs1 = imw_fetch_assoc($res1);
		$NewMsgId = $rs1['NewMsgId'];
		$set_number = $NewMsgId * 1000;
		$set_number = substr($set_number,0,7);
		$set_number = $set_number + $NewMsgId;
		return $set_number;
	}
	return false;
}
?>
