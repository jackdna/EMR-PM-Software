<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
set_time_limit(900);
$fileName = $_GET['fileName'];
if($fileName && $fileName!=''){
	header("Content-Type: application/download");
	header("Content-Disposition: attachment; filename=".$fileName."");	
	//header("Content-Transfer-Encoding: binary");
	header('Content-Length: '.filesize($fileName));
	readfile("$fileName");
}
include("common/link_new_file.php");
include("common/conDb.php");
if($_SESSION["loginUserId"]=="" && $_SESSION['loginUserName']=="") {
	echo '<script>top.location.href="index.php"</script>';
}

$fac_qry	=	" and stt.iasc_facility_id='$_SESSION[iasc_facility_id]' ";
$fac_con	=	($_SESSION['iasc_facility_id']	 ?	$fac_qry	 :	'' )	; 

if($_REQUEST['cpr_save']=='yes') {
	$date1 = trim($_REQUEST["date1"]);
	$date2 = trim($_REQUEST["date2"]);
	
	list($frMnth,$frDay,$frYr)=explode("-",$date1);
	list($toMnth,$toDay,$toYr)=explode("-",$date2);
	
	$from_date 	= $frYr.'-'.$frMnth.'-'.$frDay;
	$to_date 	= $toYr.'-'.$toMnth.'-'.$toDay;
	
	/*------getting all procedure (surgerycenter)------*/
	$arr_procs_mapping	= array();
	$procs_query = "SELECT procedureId, name, code FROM procedures";
	$procs_res	 = imw_query($procs_query);
	if($procs_res && imw_num_rows($procs_res)>0){
		while($procs_rs = imw_fetch_assoc($procs_res)){
			$procs_rs_id							 = $procs_rs['procedureId'];
			$arr_procs_mapping[$procs_rs_id]['name'] = $procs_rs['name'];
			$arr_procs_mapping[$procs_rs_id]['cpt']  = $procs_rs['code'];
		}
	}
	/*---getting patient location (surgerycenter location)---*/
	$location_result = imw_query("SELECT npi,name FROM surgerycenter LIMIT 0,1");
	$location_rs	 = imw_fetch_assoc($location_result);
	$patient_location= '^^^^'.preg_replace("/[^0-9]/","",$location_rs['npi']).'^^^'.$location_rs['name'];
	
	/*---getting diagnosis codes------*/
	$arr_diags_mapping = array();
	$diags_query = "SELECT diag_id,diag_code FROM diagnosis_tbl";
	$diags_result= imw_query($diags_query);
	if($diags_result && imw_num_rows($diags_result)>0){
		while($diags_rs = imw_fetch_assoc($diags_result)){
			$arr_diags_mapping[$diags_rs['diag_id']] = explode(', ',$diags_rs['diag_code']);
		}
	}
	
	
	if($_REQUEST["anesthesiologist"]) {
		$anesthesiologist = $_REQUEST["anesthesiologist"];
		$anesthesiologistQry = 	" AND  pc.anesthesiologist_id IN(".$anesthesiologist.") ";
	}
	$pcQry = "SELECT pc.patientConfirmationId as conf_id, pc.ascId, pc.dos, stt.appt_id, CONCAT(u.cpr_code,'^',u.lname,'^',u.fname,'^',u.mname) as anes_by 
			FROM patientconfirmation pc
			JOIN stub_tbl stt ON (stt.patient_confirmation_id = pc.patientConfirmationId) 
			LEFT JOIN users u ON (u.usersId = pc.anesthesiologist_id) 
			WHERE pc.ascId !='0' AND pc.anesthesiologist_id !='0' ".$anesthesiologistQry."
			AND pc.dos BETWEEN '".$from_date."' AND '".$to_date."' ".$fac_con;
			
	$pcRes = imw_query($pcQry);
	$surgDataArr = $ascIdArr = array();
	if(imw_num_rows($pcRes)>0) {
		while($pcRow = imw_fetch_array($pcRes)) {
			$ascIdArr[]							= $pcRow["ascId"];
			$surgDataArr[$pcRow["ascId"]]["dos"] 		= $pcRow["dos"];
			$surgDataArr[$pcRow["ascId"]]["appt_id"] 	= $pcRow["appt_id"];
			$surgDataArr[$pcRow["ascId"]]["conf_id"] 	= $pcRow["conf_id"];
			$surgDataArr[$pcRow["ascId"]]["anes_by"] 	= $pcRow["anes_by"];
		}
		$ascId = implode(",",$ascIdArr);
		unset($ascIdArr);
		if(!$ascId) {$ascId='0';}
		include('connect_imwemr.php');
		$qry = "SELECT DISTINCT(patientId), ascId FROM superbill WHERE ascId!='0' AND ascId IN(".$ascId.")";
		$res = imw_query($qry);
		if(imw_num_rows($res)>0) {
			while($row = imw_fetch_array($res)){
				$surgDataArr[$row["ascId"]]["iAsc_ptid"] = $row["patientId"];
			}
		}
	}
	//echo '<pre>';print_r($surgDataArr);
	if(count($surgDataArr)>0){
		$file_root = "admin/pdfFiles/cpr_hl7";
		if(!file_exists($file_root) || !is_dir($file_root)){
			mkdir($file_root);
		}
		$text_file_name = $file_root."/CPR_HL7_".$_REQUEST["anesthesiologist"]."_".date("Ymd").".hl7";
		if(file_exists($text_file_name)==true){unlink($text_file_name);}
		$ignoreAuth = true;
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
			include('connect_imwemr.php');
			$cpr_hl7_a08_msg			= $makeHL7->get_HL7msgCPR_p1($iasc_ptId,'Update_Patient','CPR');
			$pv1_Segment_rs				= array();						
			$pv1_Segment_rs[]  		 	= 1;	//1 set_id
			$pv1_Segment_rs[]			= ''; 	//2
			$pv1_Segment_rs[]			= ''; 	//3
			$pv1_Segment_rs[]			= ''; 	//4 
			$pv1_Segment_rs[]			= ''; 	//5
			$pv1_Segment_rs[]			= '';   //6 
			$pv1_Segment_rs[]			= $anesthesia_by;   //7  Provider
			$this_segment = array();
			$this_segment['PV1']		= $pv1_Segment_rs;
			$cpr_hl7_a08_msg		   .= $makeHL7->Make_hl7_from_array($this_segment);
			$cpr_hl7_a08_msg		   .= $makeHL7->get_HL7msgCPR_p2($iasc_ptId,'Update_Patient','CPR');
			$cpr_hl7_p03_msg			= $makeHL7->get_HL7msgCPR_p1($iasc_ptId,'Detailed Financial Transaction','CPR');
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
						$ft1_Segment_rs[]	= ''; 					//19 diagnosis code(s); can left empty if providing DG1 segment
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
			$fp = fopen($text_file_name,"a+");
			$fw = fwrite($fp, $cpr_hl7_a08_msg."\n\n".$cpr_hl7_p03_msg."\n\n");
			fclose($fp);
		}
		//	echo $cpr_hl7_a08_msg;
		//	echo '<hr>'.$cpr_hl7_p03_msg;
			
			echo '<div class=" subtracting-head">';
			echo '<div class="head_scheduler new_head_slider padding_head_adjust_admin">';
			echo '<span>CPR Export </span>';
			echo '</div>';
			echo '</div>';
			
			
			echo '<div class="row"></div>';
			echo '<div class="clearfix margin_adjustment_only"></div>';
			echo '<div class="clearfix margin_adjustment_only"></div>';
			echo '<div class="col-log-12 col-md-12 col-xs-12 col-sm-12"';
			echo '<div class="col-log-6 col-md-6 col-xs-12 col-sm-12"';
			echo '<div class="rowaudit_wrap">';
			echo '<div class="form_outer">';
			echo '<div class="clearfix margin_adjustment_only"></div>';
			
			echo '<div class="col-md-12 col-sm-12 col-lg-12 col-xs-12">';
			echo '<div class="form_reg">';
			
			echo '<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">';
			
			echo '<div class="form_reg text-center">';
			echo '<label class="" for="text">';
			echo '<b>HL7 text file generated successfully.</b><br>';
			echo 'Please click on the link to download file: ';
			echo '<a class="btn btn-info" href="?fileName='.$text_file_name.'"><b class="fa fa-download"></b>&nbsp;Click here to download</a>';
			echo '</label>';
			echo '</div>';
			
            echo '</div>';
			
			echo '</div>';
            
			echo '</div>';
			
			echo '</div>';
			
			echo '</div>';
			
			echo '</div>';
			
			echo '</div>';
			
			
			
	}
	else
	{
		
		echo '<script type="text/javascript">location.href=\'cpr_export.php?record_exist=no&date1='.$_REQUEST["date1"].'&date2='.$_REQUEST["date2"].'&anesthesiologist='.$_REQUEST["anesthesiologist"].'\';</script>';
		
	}
}
?>
