<?php
$ignoreAuth = true;
include("../../../../config/globals.php");
$arrayScan = array( 'scans' => array('id','patient_id'),
					'folder_categories' => array('folder_categories_id','patient_id'),
					'scan_doc_tbl' => array('scan_doc_id','patient_id'),
					'idoc_drawing' =>  array('id','patient_id')
				  );

$array = array(	'amendments' => array('amendment_id','patient_id'),
				'amsler_grid' => array('id', 'patient_id'),
				'chart_assessment_plans' => array('id', 'patient_id'),
				'chart_correction_values' => array('cor_id', 'patient_id'),
				'chart_cvf' => array('cvf_id', 'patientId'),
				'chart_dialation' => array('dia_id', 'patient_id'),
				'chart_diplopia' => array('dip_id', 'patientId'),
				'chart_eom' => array('eom_id', 'patient_id'),
				'chart_external_exam' => array('ee_id', 'patient_id'),
				'chart_iop' => array('iop_id', 'patient_id'),
				'chart_la' => array('la_id', 'patient_id'),
				'chart_left_cc_history' => array('cc_id', 'patient_id'),
				'chart_left_provider_issue' => array('pr_is_id', 'patient_id'),
				'chart_master_table' => array('id', 'patient_id'),
				'chart_optic' => array('optic_id', 'patient_id'),
				'chart_ptpastdiagnosis' => array('ptDiag_id','patient_id'),
				'chart_pupil' => array('pupil_id','patientId'),
				'chart_rv' => array('rv_id','patient_id'),
				'chart_slit_lamp_exam' => array('sle_id','patient_id'),
				'chart_vision' => array('vis_id','patient_id'),
				'consent_form_signature' => array('consent_form_signature_id','patient_id'),
				'contact_lens_progress' => array('progress_id','patient_id'),
				'deniedpayment' => array('deniedId','patient_id'),
				'disc' => array('disc_id','patientId'),
				'disclosure' => array('id','patient_id'),
				'disc_external' => array('disc_id','patientId'),
				'dr_task' => array('dr_task_id','patient_id'),
				'employer_data' => array('id','pid'),
				'eposted' => array('epost_id','patient_id'),
				'form_encounter' => array('id','pid'),
				'gas_trl_dsp_fnl_contact_lens' => array('gas_trl_dsp_fnl_id','patient_id'),
				'general_medicine' => array('general_id','patient_id'),
				'glaucoma_medication_comments' => array('glmc_id','patient_id'),
				'glaucoma_miscellaneous' => array('glaucoma_id','patient_id'),
				'glaucoma_past_readings' => array('id','patientId'),
				'glucoma_main' => array('glucomaId','patientId'),
				'hippa' => array('id','patient_id'),
				'immunizations' => array('id','patient_id'),
				'insurancecase_data' => array('id','pid'),
				'insurance_case' => array('ins_caseid','patient_id'),
				'insurance_data' => array('id','pid'),
				'insurance_scan_documents' => array('scan_documents_id','patient_id'),
				'iolphyformulavalues' => array('id','patient_id'),
				'ivfa' => array('vf_id','patient_id'),
				'lists' => array('id','pid'),
				'memo_tbl' => array('memo_id','patient_id'),
				'nfa' => array('nfa_id','patient_id'),
				'ocular' => array('ocular_id','patient_id'),
				'ophtha' => array('ophtha_id','patient_id'),
				'optical_order_form' => array('Optical_Order_Form_id','patient_id'),
				'pachy' => array('pachy_id','patientId'),
				'patientcredit' => array('ptCrId','patientId'),
				'patient_app_recall' => array('id','patient_id'),
				'patient_charge_list' => array('charge_list_id','patient_id'),
				'patient_charge_list_details' => array('charge_list_detail_id','patient_id'),
				'patient_consent_form_information' => array('form_information_id','patient_id'),
				'patient_consult_letter_tbl' => array('patient_consult_id','patient_id'),
				'patient_contact' => array('id','patient_id'),
				'patient_last_examined' => array('patient_last_examined_id','patient_id'),
				'patient_location' => array('patient_location_id','patientId'),
				'patient_recall' => array('id','patient_id'),
				'patient_reff' => array('reff_id','patient_id'),
				'paymentscomment' => array('commentId','patient_id'),
				'paymentswriteoff' => array('write_off_id','patient_id'),
				'pnotes' => array('id','patient_id, pid'),
				'pnote_cat' => array('id','pid'),
				'prescriptions' => array('id','patient_id'),
				'previous_status' => array('id','patient_id'),
				'prev_gas_contact_lens' => array('gas_lens_id','patient_id'),
				'prev_soft_contact_lens' => array('soft_lens_id','patient_id'),
				'recent_users' => array('recent_user_id','patient_id'),
				'refraction' => array('id','patient_id'),
				'resp_party' => array('id','patient_id'),
				'schedule' => array('id','patient_id'),
				'schedule_appointments' => array('id','sa_patient_id'),
				'social_history' => array('social_id','patient_id'),
				'special_dgn_contact_lens' => array('sp_dgn_id','patient_id'),
				'statement_tbl' => array('statement_id','patient_id'),
				'submited_record' => array('submited_id','patient_id'),
				'superbill' => array('idSuperBill','patientId'),
				'surgical_tbl' => array('surgical_id','patient_id'),
				'tbl_def_val' => array('tbl_def_val_id','ptId'),
				'topography' => array('topo_id','patientId'),
				'trl_dsp_fnl_contact_lens' => array('trl_dsp_fnl_id','patient_id'),
				'vf' => array('vf_id','patientId'),
				'vf_nfa' => array('vf_nfa_id','patient_id'),
				'vision_contact_lens' => array('vision_id','patient_id'),
				'patient_family_info' => array('id','patient_id'),
				'alert_tbl_reason' => array('id','patient_id'),
				'chart_pt_data' => array('id','patient_id'),
				'chart_pt_lock' => array('id','pt_id'),
				'clprintorder' => array('print_order_id','patient_id'),
				'clteach' => array('clTeachId','patient_id'),
				'commonNoMedicalHistory' => array('common_id','patient_id'),
				'contactlensworksheet' => array('clws_id','patient_id'),
				'creditapplied' => array('crAppId','patient_id'),
				'document_patient_rel' => array('id','p_id'),
				'era_835_nm1_details' => array('NM1_id','NM1_patient_id'),
				'glaucoma_past_readings_bak' => array('id','patientId'),
				'immunizations_alerts' => array('alert_id','patient_id'),
				'oct' => array('oct_id','patient_id'),
				'patient_custom_field' => array('id','patient_id'),
				'patient_previous_data' => array('previous_id','patient_id'),
				'phy_todo_task' => array('phy_todo_task_id','patientId'),
				'pn_reports' => array('pn_rep_id','patient_id'),
				'previous_statement' => array('previous_statement_id','patient_id'),
				'rpp_recent_users' => array('recent_user_id','patient_id'),
				'schedule_appointments_bak' => array('is','sa_patient_id'),
				'schedule_appointments_new' => array('id','sa_patient_id'),
				'surgery_center_patient_scan_docs' => array('id','patient_id'),
				'user_messages' => array('user_message_id','patientId'),
				'vital_sign_master' => array('id','patient_id'),
				'patient_blood_sugar' => array('id','patient_id'),
				'patient_cholesterol' => array('id','patient_id'),
				'patient_confidential_text' => array('id','patient_id'),
				'alert_tbl' => array('alertId','patient_id'),
				'chart_gonio' => array('gonio_id','patient_id'),
				'chart_records_archive' => array('id','patient_id'),
				'manual_batch_transactions' => array('trans_id','patient_id'),
				'order_set_associate_chart_notes' => array('order_set_associate_id','patient_id'),
				'patient_auth' => array('a_id','patient_id'),
				'patient_erx_prescription' => array('patient_eRx_prescription_id','patient_eRx_Patient_id'),
				'pt_problem_list' => array('id','pt_id'),
				'restricted_providers' => array('restrict_id','patient_id'),
				'restricted_reasons' => array('reason_id','patient_id'),
				'surgery_center_patient_allergy' => array('pre_op_allergy_id','patient_id'),
				'surgery_center_patient_medication' => array('prescription_medication_id','patient_id'),
				'surgery_center_pre_op_health_ques' => array('preOpHealthQuesId','patient_id'),
				'surgery_consent_filled_form' => array('surgery_consent_id','patient_id'),
				'surgery_consent_form_signature' => array('consent_form_signature_id','patient_id'),
				'test_labs' => array('test_labs_id','patientId'),
				'test_other' => array('test_other_id','patientId'),
				'era_835_patient_details' => array('ERA_patient_details_id','CLP_claim_submitter_id'),
				'account_payments' => array('id','patient_id'),
				'check_in_out_payment' => array('payment_id','patient_id'),
				'check_in_out_payment_post' => array('id','patient_id'),
				'tx_payments' => array('id','patient_id'),
				'clprintorder_master' => array('print_order_id','patient_id'),
				'contactlensmaster' => array('clws_id','patient_id'),
				'consent_hold_sign' => array('id','patient_id'),
				'mpay_log' => array('id','patient_id'),
				'lab_test_data' => array('lab_test_data_id','lab_patient_id'),
				'provider_view_log_tbl' => array('id','patient_id'),
				'pt_problem_list_log' => array('id','pt_id'),
				'rad_test_data' => array('rad_test_data_id','rad_patient_id'),
				'chart_genhealth_archive' => array('id','patient_id')
			);
 

foreach($arrayScan as $k => $v){
	$sql = "SELECT * FROM merge_patient_tables WHERE table_name = '".$k."' ";
	$row = sqlQuery($sql);	
	if($row==false){
		$sql = "INSERT INTO merge_patient_tables(table_name, pk_id, pt_id, database_name,created_on,status) VALUES( '".$k."', '".$v[0]."', '".$v[1]."', '".$sqlconf["scan_db_name"]."', '".date('Y-m-d H:i:s')."', 1 );";
		$row = sqlQuery($sql);
	}
}


foreach($array as $k => $v){
	$sql = "SELECT * FROM merge_patient_tables WHERE table_name = '".$k."' ";
	$row = sqlQuery($sql);	
	if($row==false){
		$sql = "INSERT INTO merge_patient_tables(table_name, pk_id, pt_id, database_name, created_on, status) VALUES( '".$k."', '".$v[0]."', '".$v[1]."', '".$sqlconf["idoc_db_name"]."', '".date('Y-m-d H:i:s')."', 1 );";
		$row = sqlQuery($sql);
	}
}

if(count($msg_info)>0)
{
	$msg_info[] = '<br><br><b>Update 15 run FAILED!</b><br>'.imw_error();
	$color = "red";
}
else
{
	$msg_info[] = "<br><br><b>Update 15 run successfully!</b>";
	$color = "green";	
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Update 15 (PI)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<br><br>
        <font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
            <?php echo(implode("<br>",$msg_info));?>
        </font>
</body>
</html>