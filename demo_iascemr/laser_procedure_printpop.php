<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
session_start();
include_once("common/conDb.php");
$rootServerPath = $_SERVER['DOCUMENT_ROOT'];
$tablename = "laser_procedure_patient_table";
//include("common/linkfile.php");
include("common_functions.php");
include_once("admin/classObjectFunction.php"); 
$objManageData = new manageData;
include("new_header_print.php");
$pconfId= $_REQUEST['pConfId'];

$detailConfirmation = $objManageData->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pconfId);
$laser_patientConfirmSiteTempSite = $detailConfirmation->site;
$patient_id = $detailConfirmation->patientId;
// APPLYING NUMBERS TO PATIENT SITE
	if($laser_patientConfirmSiteTempSite == 1) {
		$laser_patientConfirmSiteTemp = "Left Eye";  //OD
	}else if($laser_patientConfirmSiteTempSite == 2) {
		$laser_patientConfirmSiteTemp = "Right Eye";  //OS
	}else if($laser_patientConfirmSiteTempSite == 3) {
		$laser_patientConfirmSiteTemp = "Both Eye";  //OU
	}else if($laser_patientConfirmSiteTempSite == 4) {
		$laser_patientConfirmSiteTemp = "Left Lower Lid";  //OU
	}else if($laser_patientConfirmSiteTempSite == 5) {
		$laser_patientConfirmSiteTemp = "Left Lower Lid";  //OU
	}else if($laser_patientConfirmSiteTempSite == 6) {
		$laser_patientConfirmSiteTemp = "Right Upper Lid";  //OU
	}else if($laser_patientConfirmSiteTempSite == 7) {
		$laser_patientConfirmSiteTemp = "Right Lower Lid";  //OU
	}else if($laser_patientConfirmSiteTempSite == 8) {
		$laser_patientConfirmSiteTemp = "Bilateral Upper Lid";  //OU
	}else if($laser_patientConfirmSiteTempSite == 9) {
		$laser_patientConfirmSiteTemp = "Bilateral Lower Lid";  //OU
	}else{
		$laser_patientConfirmSiteTemp = "Operative Eye";  //OU
	}
// END APPLYING NUMBERS TO PATIENT SITE

//allergies
$allergy_lp = "Select * from patient_allergies_tbl where patient_confirmation_id='".$_REQUEST["pConfId"]."'";
$result_lp = imw_query($allergy_lp);
$num_lp = @imw_num_rows($result_lp);	

//START GET IDOC APPOINTMENT ID
$sc_emr_iasc_appt_id = "";
$stQry = "Select appt_id from stub_tbl where patient_confirmation_id='".$_REQUEST["pConfId"]."' ORDER BY stub_id DESC LIMIT 0,1 ";
$stRes = imw_query($stQry);
if(imw_num_rows($stRes)>0) {
	$stRow = imw_fetch_array($stRes);
	$sc_emr_iasc_appt_id = $stRow["appt_id"];
}
//END GET IDOC APPOINTMENT ID

//select the detail from database

	$ViewlaserprocedureQry = "select *, date_format(signSurgeon1DateTime,'%m-%d-%Y %h:%i %p') as signSurgeon1DateTimeFormat, date_format(signNurseDateTime,'%m-%d-%Y %h:%i %p') as signNurseDateTimeFormat from `laser_procedure_patient_table` where  confirmation_id = '".$_REQUEST["pConfId"]."'";
	$ViewlaserprocedureRes = imw_query($ViewlaserprocedureQry) or die(imw_error()); 
	$ViewlaserprocedueNumRow = imw_num_rows($ViewlaserprocedureRes);
	$ViewlaserprocedureRow = imw_fetch_array($ViewlaserprocedureRes); 
	$ViewlaserprocedureID=$ViewlaserprocedureRow['laser_procedureRecordID'];

//SHOW DETAIL OF PATIENT PRE OP MEDICATION
	$preOpPhysicianOrdersId = $_REQUEST['preOpPhysicianOrdersId'];	//
	$laserProcDetailsQry = "select * from preopphysicianorders where patient_confirmation_id = '".$_REQUEST["pConfId"]."'";
	$laserProcDetailsRes = imw_query($laserProcDetailsQry) or die(imw_error());
	$laserProcDetailsNumRow = imw_num_rows($laserProcDetailsRes);
	if($laserProcDetailsNumRow>0) {
		$laserProcDetailsRow = imw_fetch_array($laserProcDetailsRes);
		$prefilMedicationStatus = $laserProcDetailsRow['prefilMedicationStatus'];
		$preOpPhysicianOrdersId=$laserProcDetailsRow['preOpPhysicianOrdersId'];
	}

	$preOpPatientDetails = $objManageData->getArrayRecords('patientpreopmedication_tbl', 'patient_confirmation_id', $_REQUEST["pConfId"]);
//SHOW DETAIL OF PATIENT PRE OP MEDICATION
		
		//HISTORY
		$laserProcFormStatus=$ViewlaserprocedureRow['form_status'];
		$laser_chk_chief_complaint=$ViewlaserprocedureRow['chk_laser_chief_complaint'];
		$laser_chief_complaint_detail=stripslashes($ViewlaserprocedureRow['laser_chief_complaint']);
		$laser_chk_present_illness_hx=$ViewlaserprocedureRow['chk_laser_present_illness_hx'];
		$laser_present_illness_hx_detail=stripslashes($ViewlaserprocedureRow['laser_present_illness_hx']);
		$laser_chk_past_med_hx=$ViewlaserprocedureRow['chk_laser_past_med_hx'];
		$laser_past_med_hx_detail=stripslashes($ViewlaserprocedureRow['laser_past_med_hx']);
		$laser_chk_medication=$ViewlaserprocedureRow['chk_laser_medication'];
		$laser_medication_detail=stripslashes($ViewlaserprocedureRow['laser_medication']);
		$allergies_status_laser=stripslashes($ViewlaserprocedureRow['allergies_status_reviewed']);
//new	
		$verified_nurseID =$ViewlaserprocedureRow['verified_nurse_Id'];
		$verified_nurseName =$ViewlaserprocedureRow['verified_nurse_name'];
		$verified_nurseStatus=$ViewlaserprocedureRow['verified_nurse_Status'];

		$verified_surgeonID =$ViewlaserprocedureRow['verified_surgeon_Id'];
		$verified_surgeonName =$ViewlaserprocedureRow['verified_surgeon_Name'];
		$verified_surgeonStatus=$ViewlaserprocedureRow['verified_surgeon_Status'];
//new end	
		
		$stable_chbx=stripslashes($ViewlaserprocedureRow['stable_chbx']);
		$stable_other_chbx=stripslashes($ViewlaserprocedureRow['stable_other_chbx']);
		$stable_other_txtbx=stripslashes($ViewlaserprocedureRow['stable_other_txtbx']);
		$best_correction_vision_R=stripslashes($ViewlaserprocedureRow['best_correction_vision_R']);
		$best_correction_vision_L=stripslashes($ViewlaserprocedureRow['best_correction_vision_L']);
		$glare_acuity_R=$ViewlaserprocedureRow['glare_acuity_R'];
		$glare_acuity_L=$ViewlaserprocedureRow['glare_acuity_L'];
		$laser_sle_detail=stripslashes($ViewlaserprocedureRow['laser_sle']);
		$laser_fundus_exam_detail=stripslashes($ViewlaserprocedureRow['laser_fundus_exam']);
		$laser_mental_state_detail=stripslashes($ViewlaserprocedureRow['laser_mental_state']);
		
		
		//$allergies_status_reviewed=$ViewlaserprocedureRow['chbx_drug_react_reviewed'];
		
		$pre_laser_IOP_R=stripslashes($ViewlaserprocedureRow['pre_laser_IOP_R']);
		$pre_laser_IOP_L=stripslashes($ViewlaserprocedureRow['pre_laser_IOP_L']);
		$pre_iop_na		=stripslashes($ViewlaserprocedureRow['pre_iop_na']);
//laser
		$pre_laser_IOP_na=stripslashes($ViewlaserprocedureRow['pre_iop_na']);
		
		$laser_other=stripslashes($ViewlaserprocedureRow['laser_other']);
//new
		$laser_pre_op_diagnosis=stripslashes($ViewlaserprocedureRow['pre_op_diagnosis']);//
//new end

//medication
		$laser_other_pre_medication=stripslashes($ViewlaserprocedureRow['laser_other_pre_medication']);
		$laser_comments=stripslashes($ViewlaserprocedureRow['laser_comments']);

//pre vital
		$chk_laser_patient_evaluated=stripslashes($ViewlaserprocedureRow['chk_laser_patient_evaluated']);
		$prelaserVitalSignBP=stripslashes($ViewlaserprocedureRow['prelaserVitalSignBP']);
		$prelaserVitalSignP=stripslashes($ViewlaserprocedureRow['prelaserVitalSignP']);
		$prelaserVitalSignR=stripslashes($ViewlaserprocedureRow['prelaserVitalSignR']);
		
		$laser_chk_spot_duration=$ViewlaserprocedureRow['chk_laser_spot_duration'];
		$laser_spot_duration_detail=stripslashes($ViewlaserprocedureRow['laser_spot_duration']);
		
		$laser_chk_spot_size=$ViewlaserprocedureRow['chk_laser_spot_size'];
		$laser_spot_size_detail=stripslashes($ViewlaserprocedureRow['laser_spot_size']);
		
		$laser_chk_power=$ViewlaserprocedureRow['chk_laser_power'];
		$laser_power_detail=stripslashes($ViewlaserprocedureRow['laser_power']);
		
		$laser_chk_shots=$ViewlaserprocedureRow['chk_laser_shots'];
		$laser_shots_detail=stripslashes($ViewlaserprocedureRow['laser_shots']);
		
		$laser_chk_total_energy=$ViewlaserprocedureRow['chk_laser_total_energy'];
		$laser_total_energy_detail=stripslashes($ViewlaserprocedureRow['laser_total_energy']);
		
		$laser_chk_degree_of_opening=$ViewlaserprocedureRow['chk_laser_degree_of_opening'];
		$laser_degree_of_opening_detail=stripslashes($ViewlaserprocedureRow['laser_degree_of_opening']);
		
		$laser_chk_exposure=$ViewlaserprocedureRow['chk_laser_exposure'];
		$laser_exposure_detail=stripslashes($ViewlaserprocedureRow['laser_exposure']);

		$laser_chk_count=$ViewlaserprocedureRow['chk_laser_count'];
		$laser_count_detail=stripslashes($ViewlaserprocedureRow['laser_count']);
		
		$laser_post_progress_detail=stripslashes($ViewlaserprocedureRow['laser_post_progress']);							
		$laser_post_operative_detail=stripslashes($ViewlaserprocedureRow['laser_post_operative']);
		
		
		//post vital
		$postlaserVitalSignBP=stripslashes($ViewlaserprocedureRow['postlaserVitalSignBP']);
		$postlaserVitalSignP=stripslashes($ViewlaserprocedureRow['postlaserVitalSignP']);
		$postlaserVitalSignR=stripslashes($ViewlaserprocedureRow['postlaserVitalSignR']);
//laser
		$iop_pressure_l=stripslashes($ViewlaserprocedureRow['iop_pressure_l']);
		$iop_pressure_r=stripslashes($ViewlaserprocedureRow['iop_pressure_r']);
		$iop_pressure_na=stripslashes($ViewlaserprocedureRow['iop_na']);
		
//laser

//new
		$post_comment=stripslashes($ViewlaserprocedureRow['post_op_operative_comment']);
//new end
		//surgeon sign
		$signSurgeon1Id =stripslashes($ViewlaserprocedureRow['signSurgeon1Id']);
		$signSurgeon1FirstName =stripslashes($ViewlaserprocedureRow['signSurgeon1FirstName']);
		$signSurgeon1MiddleName =stripslashes($ViewlaserprocedureRow['signSurgeon1MiddleName']);
		$signSurgeon1LastName =stripslashes($ViewlaserprocedureRow['signSurgeon1LastName']);
		$signSurgeon1Status =stripslashes($ViewlaserprocedureRow['signSurgeon1Status']);
		$signSurgeonName = stripslashes($signSurgeon1LastName).", ".stripslashes($signSurgeon1FirstName)." ".stripslashes($signSurgeon1MiddleName);
	
		//nurse sign
		$signNurseId =stripslashes($ViewlaserprocedureRow['signNurseId']);
		$signNurseFirstName =stripslashes($ViewlaserprocedureRow['signNurseFirstName']);
		$signNurseMiddleName =stripslashes($ViewlaserprocedureRow['signNurseMiddleName']);
		$signNurseLastName =stripslashes($ViewlaserprocedureRow['signNurseLastName']);
		$signNurseStatus =stripslashes($ViewlaserprocedureRow['signNurseStatus']);
		$signNurseName = $signNurseLastName.", ".$signNurseFirstName." ".$signNurseMiddleName;
		
		//Image
		$laser_procedure_image =stripslashes($ViewlaserprocedureRow['laser_procedure_image']);
		$laser_procedure_image_path =stripslashes($ViewlaserprocedureRow['laser_procedure_image_path']);
		
		$verified_nurseTimeout	=	$ViewlaserprocedureRow['verified_nurse_timeout'];
		if($verified_nurseTimeout <> '0000-00-00 00:00:00' && !empty($verified_nurseTimeout)){
			$verified_nurseTimeout=date('h:i A',strtotime($verified_nurseTimeout));
		}else{
			$verified_nurseTimeout	=	'';	
		}
		
		$verified_surgeonTimeout	=	$ViewlaserprocedureRow['verified_surgeon_timeout'];
		if($verified_surgeonTimeout <> '0000-00-00 00:00:00' && !empty($verified_surgeonTimeout)){
			$verified_surgeonTimeout=date('h:i A',strtotime($verified_surgeonTimeout));
		}else{
			$verified_surgeonTimeout	=	'';	
		}
		
		$asa_status	=	$ViewlaserprocedureRow['asa_status'];
		$prelaserVitalSignTime	=	$ViewlaserprocedureRow['prelaserVitalSignTime'];
		if($prelaserVitalSignTime <> '0000-00-00 00:00:00' && !empty($prelaserVitalSignTime)){
			$prelaserVitalSignTime=date('h:i A',strtotime($prelaserVitalSignTime));
		}else{
			$prelaserVitalSignTime	=	'';	
		}
		
		$postlaserVitalSignTime	=	$ViewlaserprocedureRow['postlaserVitalSignTime'];
		if($postlaserVitalSignTime <> '0000-00-00 00:00:00' && !empty($postlaserVitalSignTime)){
			$postlaserVitalSignTime=date('h:i A',strtotime($postlaserVitalSignTime));
		}else{
			$postlaserVitalSignTime	=	'';	
		}
		
		$proc_start_time	=	$ViewlaserprocedureRow['proc_start_time'];
		if($proc_start_time <> '0000-00-00 00:00:00' && !empty($proc_start_time)){
			$proc_start_time=date('h:i A',strtotime($proc_start_time));
		}else{
			$proc_start_time	=	'';	
		}
		$proc_end_time	=	$ViewlaserprocedureRow['proc_end_time'];
		if($proc_end_time <> '0000-00-00 00:00:00' && !empty($proc_end_time)){
			$proc_end_time=date('h:i A',strtotime($proc_end_time));
		}else{
			$proc_end_time	=	'';	
		}
		
		$discharge_home = (int)$ViewlaserprocedureRow['discharge_home'];
		$patients_relation = stripslashes($ViewlaserprocedureRow['patients_relation']);
		$patients_relation_other = stripslashes($ViewlaserprocedureRow['patients_relation_other']);
		$patient_transfer = (int)$ViewlaserprocedureRow['patient_transfer'];
		$discharge_time = $ViewlaserprocedureRow['discharge_time'];
		$discharge_time = ($discharge_time && $discharge_time <> '0000-00-00 00:00:00') ? $objManageData->getTmFormat($discharge_time) : '';
		$version_num_laser_proc = (int)$ViewlaserprocedureRow['version_num'];
		
//select the detail from database


	$table_print.=$head_table;
	if(file_exists('laser_procedure_printpop_common.php'))
	{
		include'laser_procedure_printpop_common.php';
	}
	$tabletlaser = $table_print;//die($tabletlaser);

if($laserProcFormStatus=='completed' || $laserProcFormStatus=='not completed') {
	if($iDocOpNoteSave == "yes") { //ADD CONTENT IN IDOC OPERATIVE NOTE
		imw_close($link); //CLOSE SURGERYCENTER CONNECTION
		include_once('connect_imwemr.php'); // imwemr connection
		$chkLaserOpnoteQry="SELECT pn_rep_id FROM pn_reports WHERE patient_id = '".$imwPatientIdLaser."' AND sc_emr_laser_report_id = '".$laserprocedureRecordpostedID."'";
		$chkLaserOpnoteRes=imw_query($chkLaserOpnoteQry) or die($chkLaserOpnoteQry.imw_error());
		$insUpdtLaserOpQry = " INSERT INTO ";
		$insUpdtLaserOpWhereQry =  " ";
		if(imw_num_rows($chkLaserOpnoteRes)>0){
			$insUpdtLaserOpQry = " UPDATE ";
			$insUpdtLaserOpWhereQry =  " WHERE patient_id = '".$imwPatientIdLaser."' AND sc_emr_laser_report_id = '".$laserprocedureRecordpostedID."' ";	
		}
		$setLaserOpnoteQry = $insUpdtLaserOpQry." pn_reports SET  
							patient_id 					= '".$imwPatientIdLaser."',
							txt_data 					= '".addslashes($tabletlaser)."',
							pn_rep_date 				= '".date("Y-m-d H:i:s")."',
							sc_emr_template_name 		= 'ASC Laser Procedure',
							sc_emr_laser_report_id 		= '".$laserprocedureRecordpostedID."',
							sc_emr_iasc_appt_id			= '".$sc_emr_iasc_appt_id."',
							status 						= '0'
							".$insUpdtLaserOpWhereQry;
		$setLaserOpnoteRes=imw_query($setLaserOpnoteQry) or die($setLaserOpnoteQry.imw_error());
		imw_close($link_imwemr); //CLOSE IMWEMR CONNECTION
		include("common/conDb.php");  //SURGERYCENTER CONNECTION
		
	}else {
		$fileOpen    = fopen('new_html2pdf/pdffile.html','w+');
		$filePut     = fputs($fileOpen,$tabletlaser);
		fclose($fileOpen);
		$URL='http://'. $_SERVER['HTTP_HOST'].'/surgerycenter/testPdf.html';
	
?>

        <table bgcolor="#FFFFFF"  style="font:vetrdana; font-size:14;" width="100%" height="100%">
            <tr>
                <td width="100%" align="center" valign="middle"><img src="images/ajax-loader.gif"></td> 
            </tr>
        </table>	
        
         <form name="printlaser_procedure" action="new_html2pdf/createPdf.php?op=p" method="post">
         </form>
        
        <script language="javascript">
            function submitfn()
            {
                document.printlaser_procedure.submit();
            }
        </script>
        
        <script type="text/javascript">
            submitfn();
        </script>
<?php
	}
}else {
	echo "<center>Please verify/save this form before print</center>";
}
?>