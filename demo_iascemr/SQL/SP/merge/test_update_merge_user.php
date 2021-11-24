<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
	set_time_limit(0);
	include_once("../../../common/conDb.php");  //MYSQL CONNECTION
	$sv_file_name = "merge_users.csv";
	include_once("test_update_merge_db_detail.php");  //DB Details
	$fl_name= "csv/".$sv_file_name;
	
	
	if($masterDB && $childDB)
	{
		imw_query("CREATE TABLE $masterDB.users_bak_".date("d_m_Y")." AS (SELECT * FROM $masterDB.users)") or $msg_info[] = imw_error();
		imw_query("CREATE TABLE $childDB.users_bak_".date("d_m_Y")." AS (SELECT * FROM $childDB.users)") or $msg_info[] = imw_error();
		
		$RECORD_UPDATE=$RECORD_INSERT=0;
		/*
		$TABLE_TO_UPDATE=array("users"=>"usersId","chartnotes_change_audit_tbl"=>"user_id", "chart_log"=>"operator_id", "chart_pt_lock_tbl"=>"user_id", "consent_multiple_form"=>"signSurgeon1Id", "consent_multiple_form"=>"signNurseId", "consent_multiple_form"=>"signAnesthesia1Id", "consent_multiple_form"=>"signWitness1Id", "dischargesummarysheet"=>"surgeonId", "dischargesummarysheet"=>"signSurgeon1Id", "eposted"=>"modified_operator_id", "eposted"=>"created_operator_id", "finalize_history"=>"finalize_action_user_id", "genanesthesianursesnotes"=>"nurseId", "genanesthesianursesnotes"=>"createdByUserId", "genanesthesianursesnotes"=>"relivedNurseId", "genanesthesianursesnotes"=>"signNurseId", "genanesthesiarecord"=>"anesthesiologistId", "genanesthesiarecord"=>"relivedNurseId", "genanesthesiarecord"=>"signAnesthesia1Id", "history_physicial_clearance"=>"signSurgeon1Id", "history_physicial_clearance"=>"signAnesthesia1Id", "history_physicial_clearance"=>"signNurseId", "history_physicial_clearance"=>"save_operator_id", "iolink_consent_filled_form"=>"signSurgeon1Id", "iolink_consent_filled_form"=>"signNurseId", "iolink_consent_filled_form"=>"signAnesthesia1Id", "iolink_consent_filled_form"=>"signWitness1Id", "iolink_patient_allergy"=>"operator_id", "iolink_patient_prescription_medication"=>"operator_id", "iolink_preophealthquestionnaire"=>"nurseId", "iolink_preophealthquestionnaire"=>"signNurseId", "laser_procedure_patient_table"=>"verified_surgeon_Id", "laser_procedure_patient_table"=>"verified_nurse_Id", "laser_procedure_patient_table"=>"signSurgeon1Id", "laser_procedure_patient_table"=>"signNurseId", "lasusedpassword"=>"user_id", "localanesthesiarecord"=>"surgeonId", "localanesthesiarecord"=>"anesthesiologistId", "localanesthesiarecord"=>"relivedPreNurseId", "localanesthesiarecord"=>"relivedIntraNurseId", "localanesthesiarecord"=>"relivedPostNurseId", "localanesthesiarecord"=>"signAnesthesia1Id", "localanesthesiarecord"=>"signAnesthesia2Id", "localanesthesiarecord"=>"signAnesthesia3Id", "localanesthesiarecord"=>"signSurgeon1Id", "login_logout_audit_tbl"=>"user_id", "msg_tbl"=>"msg_user_id", "narcotics_log_tbl"=>"user_id", "operatingroomrecords"=>"iolConfirmedWithSurgeonId", "operatingroomrecords"=>"surgeonId1", "operatingroomrecords"=>"surgeonId2", "operatingroomrecords"=>"anesthesiologistId", "operatingroomrecords"=>"scrubTechId1", "operatingroomrecords"=>"scrubTechId2", "operatingroomrecords"=>"circulatingNurseId ", "operatingroomrecords"=>"nurseId", "operatingroomrecords"=>"signNurseId", "operatingroomrecords"=>"signSurgeon1Id", "operatingroomrecords"=>"signAnesthesia1Id", "operatingroomrecords"=>"signAnesthesia2Id", "operatingroomrecords"=>"signSurgeon2Id", "operatingroomrecords"=>"signSurgeon3Id", "operatingroomrecords"=>"signScrubTech1Id", "operatingroomrecords"=>"signScrubTech2Id", "operatingroomrecords"=>"signNurse1Id", "operativereport"=>"userId", "operativereport"=>"signSurgeon1Id", "password_change_reset_audit_tbl"=>"user_id", "patientconfirmation"=>"surgeonId", "patientconfirmation"=>"anesthesiologist_id", "patientconfirmation"=>"nurseId", "patient_allergies_tbl"=>"operator_id", "patient_anesthesia_medication_tbl"=>"operator_id", "patient_instruction_sheet"=>"signSurgeon1Id", "patient_instruction_sheet"=>"signNurseId", "patient_instruction_sheet"=>"signWitness1Id", "patient_in_waiting_tbl"=>"operator_id", "patient_prescription_medication_healthquest_tbl"=>"operator_id", "patient_prescription_medication_tbl"=>"operator_id", "postopnursingrecord"=>"nurseId", "postopnursingrecord"=>"relivedNurseId", "postopnursingrecord"=>"signNurseId", "postopphysicianorders"=>"surgeonId", "postopphysicianorders"=>"nurseId", "postopphysicianorders"=>"signSurgeon1Id", "postopphysicianorders"=>"signNurseId", "postopphysicianorders"=>"relivednurse", "postopphysicianorders"=>"saved_operator_id", "preopgenanesthesiarecord"=>"createdByUserId", "preopgenanesthesiarecord"=>"relivedNurseId", "preopgenanesthesiarecord"=>"nurseId", "preophealthquestionnaire"=>"nurseId", "preophealthquestionnaire"=>"signNurseId", "preophealthquestionnaire"=>"signWitness1Id", "preopnursingrecord"=>"nurseId", "preopnursingrecord"=>"relivedNurseId", "preopnursingrecord"=>"signNurseId", "preopphysicianorders"=>"surgeonId", "preopphysicianorders"=>"anesthesiologistId", "preopphysicianorders"=>"signSurgeon1Id", "preopphysicianorders"=>"signNurseId", "preopphysicianorders"=>"signNurse1Id", "preopphysicianorders"=>"relivednurse", "scan_documents_user"=>"user_id", "scan_documents_user"=>"operator_id", "scan_upload_tbl_user"=>"user_id", "scan_upload_tbl_user"=>"operator_id", "superbill_tbl"=>"modified_by", "surgical_check_list"=>"user_id", "surgical_check_list"=>"signNurse1Id", "surgical_check_list"=>"signNurse2Id", "surgical_check_list"=>"signNurse3Id", "surgical_check_list"=>"signNurse4Id", "surgical_check_list"=>"reliefNurse1", "surgical_check_list"=>"reliefNurse2", "surgical_check_list"=>"reliefNurse3", "surgical_check_list"=>"reliefNurse4", "tblprogress_report"=>"usersId", "transfer_followups"=>"signNurseId", "transfer_followups"=>"signSurgeon1Id", "transfer_followups"=>"signNurse1Id");
		*/
		//GET MASTER USER DETAIL
		$masStr="select npi, fname, mname, lname, usersId, user_type, user_sub_type, coordinator_type, deleteStatus from $masterDB.users order by usersId asc";
		$masQuery=imw_query($masStr)or $msg_info[] = $masStr.imw_error();
		$MASTER_NPI_ARR = array();
		while($masData=imw_fetch_object($masQuery))
		{
			
			$npi		=	trim(strtolower($masData->npi));
			$fName		=	trim(strtolower($masData->fname));
			$mName		=	trim(strtolower($masData->mname));
			$lName		=	trim(strtolower($masData->lname));
			$userType	=	trim(strtolower($masData->user_type));
			$userSubType=	trim(strtolower($masData->user_sub_type));
			$coordType	=	trim(strtolower($masData->coordinator_type));
			$delStatus	=	trim(strtolower($masData->deleteStatus));
			
			if($npi) {
				$MASTER_NPI_ARR[$npi]=$masData->usersId;
			}
			if($fName && $lName) {
				$MASTER_NAME_ARR[$fName][$mName][$lName][$userType][$userSubType][$coordType][$delStatus] = $masData->usersId;
			}
		}//echo'<pre>';
		//print_r($MASTER_NAME_ARR);die();
		//GET CHILD USER DETAIL
		$chilkStr = "select * from $childDB.users order by usersId asc";
		$childQuery=imw_query($chilkStr) or $msg_info[] = $chilkStr.imw_error();
		while($childData=imw_fetch_assoc($childQuery))
		{
			$master_id='';
			$npi		=	trim(strtolower($childData['npi']));
			$fName		=	trim(strtolower($childData['fname']));
			$mName		=	trim(strtolower($childData['mname']));
			$lName		=	trim(strtolower($childData['lname']));
			$userType	=	trim(strtolower($childData['user_type']));
			$userSubType=	trim(strtolower($childData['user_sub_type']));
			$coordType	=	trim(strtolower($childData['coordinator_type']));
			$delStatus	=	trim(strtolower($childData['deleteStatus']));
			
			//if user match then update in child database
			if($MASTER_NPI_ARR[$npi] || $MASTER_NAME_ARR[$fName][$mName][$lName][$userType][$userSubType][$coordType][$delStatus])
			{
				$primaryTableStatus = '';
				if($MASTER_NPI_ARR[$npi])
				{
					$master_id	=	$MASTER_NPI_ARR[$npi];
				}
				elseif($MASTER_NAME_ARR[$fName][$mName][$lName][$userType][$userSubType][$coordType][$delStatus])
				{
					$master_id	=	$MASTER_NAME_ARR[$fName][$mName][$lName][$userType][$userSubType][$coordType][$delStatus];	
				}
				
				//user does exist in master table update its id only(if not already updated)
				if($childData['usersId']!=$master_id)
				{
					if(file_exists($fl_name)){
						$fileContents = fopen($fl_name,"r");
						$row=0;
						while(($data=fgetcsv($fileContents,10000,',')) !== FALSE){	
							if($row >0){
								$primary_table_name=trim($data[0]);
								$primary_column_name=trim($data[1]);
								$foreign_table_name = trim($data[2]);
								$foreign_column_name = trim($data[3]);
								
								if(trim($primary_table_name) && trim($primary_column_name)) {
									$primary_updt_qry = "UPDATE $childDB.$primary_table_name SET $primary_column_name='".$master_id."' WHERE $primary_column_name = '".$childData["usersId"]."'";
									imw_query($primary_updt_qry) or $msg_info[] = $primaryTableStatus = 'PRIMARY = '.$primary_updt_qry.imw_error();
								}
								if(trim($foreign_table_name) && trim($foreign_column_name) && (!trim($primaryTableStatus)) ) {
									$foreign_updt_qry = "UPDATE $childDB.$foreign_table_name SET $foreign_column_name= '".$master_id."' WHERE $foreign_column_name = '".$childData["usersId"]."'";
									imw_query($foreign_updt_qry) or $msg_info[] = 'FOREIGN = '.$foreign_updt_qry.imw_error();
								}
							}
							$row++;
						}
					}
				}
				$RECORD_UPDATE++;	
			}
			else//if user doesn't match then insert user in master table
			{
				
				unset($q_str);
				foreach($childData as $key=>$val)
				{
					$val = trim($val);
					$q_str.=($q_str)?" ,$key='".addslashes($val)."'":" $key='".addslashes($val)."'";		
				}
				
				if($q_str){
					$qry_insert="INSERT INTO ".$masterDB.".users set $q_str";
					$res_insert=imw_query($qry_insert)or $msg_info[] = $qry_insert.imw_error();
					if($res_insert){
						$RECORD_INSERT++;
					}
				}
			}
		}
		
		$msg_info[] = "<br><br><b> Merge Users Completed</b><br/>$RECORD_UPDATE Records Updated and $RECORD_INSERT Inserted.";	
	}
	else
	{
		$msg_info[] = "<br><br><b> Merge Users Not Completed</b><br/>Database Not Found";		
	}
	
	
?>
<html>
<head>
<title>Merge User Data </title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
<?php if($msg_info!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2"><?php echo(implode("<br>",$msg_info));?></font>
<?php
}
@imw_close();
?> 

</body>
</html>