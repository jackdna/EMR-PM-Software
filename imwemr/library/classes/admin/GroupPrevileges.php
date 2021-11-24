<?php
/*
	The MIT License (MIT)
	Distribute, Modify and Contribute under MIT License
	Use this software under MIT License
*/
class GroupPrevileges {


	public function __construct()
	{

	}

	function fetchByReportType($report_type) {
		$report=array();
		$query = imw_query("SELECT * FROM `custom_reports` WHERE `report_type` = '".$report_type."' and `delete_status` = 0 and `default_report`=1 ");
		if(imw_num_rows($query) > 0){
		    while($row = imw_fetch_assoc($query)){
			$report_id = $row['id'];
			$report_name = $row['template_name'];
			$report_name = str_replace(array(" ", "/","-"), "_", $report_name);
			$report_name=strtolower($report_name);
			$report['priv_report_'.$report_name.$report_id] = $row['template_name'];
		    }
		}
		return $report;
	}

	function fetchMenuArray($more=0) {

		$setting = array();

		$setting['priv_group'] = 'Business Unit';
		$setting['priv_facility'] = 'Facilities';
		$setting['priv_admin_Heard_About_Us'] = 'Heard About Us';
		$setting['priv_admin_Provider_Groups'] = 'Provider Groups';
		$setting['priv_ref_physician'] = 'Ref. Physician';
		$setting['priv_provider_management'] = 'Users';
		$setting['priv_admin_CDS_Intervention'] = 'CDS Intervention';
		$setting['priv_admin_Updox'] = 'Updox';
		$setting['priv_grp_prvlgs'] = 'Group Privileges';
		$setting['priv_chng_prvlgs'] = 'Change Privileges';
		$setting['priv_rules_mngr'] = 'Rules Manager';
		$setting['priv_Office_Hours_Settings'] = 'Office Hours Settings';
		$setting['priv_vital_interactions'] = 'Vital Interactions';
		$setting['priv_ar_worksheet_setting'] = 'AR Worksheet';

		$billing = array();
		$billing['priv_billing_Adjustment_Codes'] = 'Adjustment Codes';
		$billing['priv_billing_Cases'] = 'Cases';
		$billing['priv_billing_CL_Charges'] = 'CL Charges';
		$billing['priv_billing_CPT'] = 'CPT';
		$billing['priv_billing_Department'] = 'Department';
		$billing['priv_billing_Discount_Codes'] = 'Discount Codes';
		$billing['priv_billing_Dx_Codes'] = 'Dx Codes';
		$billing['priv_billing_Fee_Table'] = 'Fee Table';
		$billing['priv_billing_ICD_10'] = 'ICD-10';
		$billing['priv_billing_Insurance'] = 'Insurance';
		$billing['priv_billing_Insurance_Groups'] = 'Insurance Groups';
		$billing['priv_billing_Messages'] = 'Messages';
		$billing['priv_billing_Modifiers'] = 'Modifiers';
		$billing['priv_billing_Phrases'] = 'Phrases';
		$billing['priv_billing_POE'] = 'POE';
		$billing['priv_billing_Policies'] = 'Policies';
		$billing['priv_billing_POS_Codes'] = 'POS Codes';
		$billing['priv_billing_POS_Facilities'] = 'POS Facilities';
		$billing['priv_billing_Pre_Auth_Templates'] = 'Pre Auth Templates';
		$billing['priv_billing_Proc_Codes'] = 'Proc Codes';
		$billing['priv_billing_Reason_Codes'] = 'Reason Codes';
		$billing['priv_billing_Revenue_Codes'] = 'Revenue Codes';
		$billing['priv_billing_Status'] = 'Status';
		$billing['priv_billing_Test_CPT_Preference'] = 'Test CPT Preference';
		$billing['priv_billing_Type_Of_Service'] = 'TOS (Type of Service)';
		$billing['priv_billing_Write_Off_Codes'] = 'Write Off Codes';
		$billing['priv_billing_Zip_Codes'] = 'Zip Codes';
		$billing['priv_billing_Payment_Methods'] = 'Payment Methods';
		$billing['priv_billing_Manage_POS'] = 'Manage POS';
		$billing['priv_billing_Denial_Mgmt'] = 'Denial Management';
		$billing['priv_billing_sage_sftp_cred'] = 'Sage SFTP Credentials';
		$billing['priv_billing_ccda_sftp_cred'] = 'CCDA SFTP Credentials';
		
		$clinical = array();
		$clinical['priv_admn_clinical_Allergies'] = 'Allergies';
		$clinical['priv_admn_clinical_AP_Policies'] = 'AP Policies';
		$clinical['priv_admn_clinical_Botox'] = 'Botox';
		$clinical['priv_admn_clinical_Exam_Extensions'] = 'Clinical Exam Extensions';
		$clinical['priv_admn_clinical_Custom_HPI'] = 'Custom HPI';
		$clinical['priv_admn_clinical_Drawings'] = 'Drawings';
		$clinical['priv_admn_clinical_Epost'] = 'Epost';
		$clinical['priv_erx_preferences'] = 'eRx Preferences';
		$clinical['priv_admn_clinical_FU'] = 'F/U';
		$clinical['priv_immunization'] = 'Immunization';
		$clinical['priv_admn_clinical_Labs_Rad'] = 'Labs/Rad';
		$clinical['priv_admn_clinical_Med'] = 'Med.';
		$clinical['priv_admn_clinical_Ophth_Drops'] = 'Ophth. Drops';
		$clinical['priv_admn_clinical_Order'] = 'Order';
		$clinical['priv_admn_clinical_Order_Sets'] = 'Order Sets';
		$clinical['priv_admn_clinical_Order_Templates'] = 'Order Templates';
		$clinical['priv_admn_clinical_Phrases'] = 'Phrases';
		$clinical['priv_admn_clinical_Procedures'] = 'Procedures';
		$clinical['priv_admn_clinical_Pt_Chart_Locked'] = 'Pt Chart Locked';
		$clinical['priv_admn_clinical_Rx_Template'] = 'Rx Template';
		$clinical['priv_admn_clinical_SCP_Reasons'] = 'SCP Reasons';
		$clinical['priv_admin_scp'] = 'Site Care Plan';
		$clinical['priv_admn_clinical_Specialty'] = 'Specialty';
		$clinical['priv_admn_clinical_Sx'] = 'Sx';
		$clinical['priv_admn_clinical_Sx_Planning'] = 'Sx Planning';
		$clinical['priv_admn_clinical_Template'] = 'Template';
		$clinical['priv_admn_clinical_Test_Template'] = 'Test Template';
		$clinical['priv_admn_clinical_Visit'] = 'Visit';
		$clinical['priv_vs'] = 'VS';
		$clinical['priv_admn_clinical_WNL'] = 'WNL';

		$documents = array();
		$documents['priv_admn_docs_Collection'] = 'Collection';
		$documents['priv_admn_docs_Consent'] = 'Consent';
		$documents['priv_admn_docs_Consult'] = 'Consult';
		$documents['priv_admn_docs_Education'] = 'Education';
		$documents['priv_admn_docs_Instructions'] = 'Instructions';
		$documents['priv_admn_docs_Logos'] = 'Logos';
		$documents['priv_admn_docs_Op_Notes'] = 'Op Notes';
		$documents['priv_admn_docs_Package'] = 'Package';
		$documents['priv_admn_docs_Panels'] = 'Panels';
		$documents['priv_admn_docs_Prescriptions'] = 'Prescriptions';
		$documents['priv_admn_docs_Pt_Docs'] = 'Pt. Docs';
		$documents['priv_admn_docs_Recalls'] = 'Recalls';
		$documents['priv_admn_docs_Scan_Upload_Folders'] = 'Scan/Upload Folders';
		$documents['priv_set_margin'] = 'Set Margin';
		$documents['priv_admn_docs_Smart_Tags'] = 'Smart Tags';
		$documents['priv_admn_docs_Statements'] = 'Statements';

		$iasc_link = array();
		$iasc_link['priv_admn_iasc_iASC_Link_Settings'] = 'iASC Link Settings';
		$iasc_link['priv_admn_iasc_Surgery_Consent_Form'] = 'Surgery Consent Form';

		$iMedic_Monitor = array();
		$iMedic_Monitor['priv_admn_imm_iMedic_Monitor'] = 'iMedic Monitor';
		$iMedic_Monitor['priv_room_assign'] = 'Room Assign';
		$iMedic_Monitor['priv_Manage_Columns'] = 'Manage Columns';

		$iPortal = array();
		$iPortal['priv_admn_ipl_Auto_Responder'] = 'Auto Responder';
		$iPortal['priv_admn_ipl_iPortal_Settings'] = 'iPortal Settings';
		$iPortal['priv_admn_ipl_Preferred_Images'] = 'Preferred Images';
		$iPortal['priv_admn_ipl_Print_Preferences'] = 'Print Preferences';
		$iPortal['priv_admn_ipl_Security_Questions'] = 'Security Questions';
		$iPortal['priv_admn_ipl_Set_Survey'] = 'Set Survey';
		$iPortal['priv_admn_ipl_Survey'] = 'Survey';
		if (isset($GLOBALS['ERP_API_PATIENT_PORTAL']) && $GLOBALS['ERP_API_PATIENT_PORTAL'] == 1){
            $iPortal['priv_admn_erp_portal'] = 'Eye Reach Patient Portal';
		}

		$Manage_Fields = array();
		$Manage_Fields['priv_admn_mcf_Custom_Fields'] = 'Custom Fields';
		$Manage_Fields['priv_admn_mcf_General_Health_Questns'] = 'General Health Questions';
		$Manage_Fields['priv_admn_mcf_Ocular_Questions'] = 'Ocular Questions';
		$Manage_Fields['priv_admn_mcf_Practice_Fields'] = 'Practice Fields';
		$Manage_Fields['priv_admn_mcf_Tech_Fields'] = 'Tech. Fields';

		$Admin_Optical = array();
		$Admin_Optical['priv_admn_optical_Frames'] = 'Frames';
		$Admin_Optical['priv_admn_optical_Lenses'] = 'Lenses';
		$Admin_Optical['priv_admn_optical_Vendor'] = 'Vendor';
		$Admin_Optical['priv_admn_optical_Contact_Lens'] = 'Contact Lens';
		$Admin_Optical['priv_admn_optical_Color'] = 'Color';
		$Admin_Optical['priv_admn_optical_Lens_Codes'] = 'Lens Codes';
		$Admin_Optical['priv_admn_optical_Make'] = 'Make';

		$Admin_iOptical = array();
		$Admin_iOptical['priv_Optical_POS'] = 'POS';
		$Admin_iOptical['priv_Optical_Inventory'] = 'Inventory';
		$Admin_iOptical['priv_Optical_Admin'] = 'Admin';
		$Admin_iOptical['priv_Optical_Reports'] = 'Reports';

		$Admin_iCnfdntlTxt = array();
		$Admin_iCnfdntlTxt['priv_CnfdntlTxt_Full'] = 'Full Access';
		$Admin_iCnfdntlTxt['priv_CnfdntlTxt_Read'] = 'Read Only';

		$Admin_Reports = array();
		$Admin_Reports['priv_admn_reports_Audit_Policies'] = 'Audit Policies';
		$Admin_Reports['priv_admn_reports_Compliance'] = 'Compliance';
		$Admin_Reports['priv_admn_reports_CPT_Groups'] = 'CPT Groups';
		$Admin_Reports['priv_admn_reports_Fac_Groups'] = 'Fac. Groups';
		$Admin_Reports['priv_admn_reports_Financials'] = 'Financials';
		$Admin_Reports['priv_admn_reports_Practice_Analytic'] = 'Practice Analytic';
		$Admin_Reports['priv_admn_reports_Ref_Groups'] = 'Ref. Groups';
		$Admin_Reports['priv_admn_reports_Scheduler'] = 'Scheduler';

		$Admin_Scheduler = array();
		$Admin_Scheduler['priv_admn_sch_Available'] = 'Available';
		$Admin_Scheduler['priv_admn_sch_Chain_Event'] = 'Chain Event';
		$Admin_Scheduler['priv_admn_sch_Procedure_Templates'] = 'Procedure Templates';
		$Admin_Scheduler['priv_admn_sch_Provider_Schedule'] = 'Provider Schedule';
		$Admin_Scheduler['priv_admn_sch_Schedule_Reasons'] = 'Schedule Reasons';
		$Admin_Scheduler['priv_admn_sch_Schedule_Status'] = 'Schedule Status';
		$Admin_Scheduler['priv_admn_sch_Schedule_Templates'] = 'Schedule Templates';
		//$Admin_Scheduler['priv_admn_sch_Setting'] = 'Setting';

		$IOLs = array();
		$IOLs['priv_admn_iols_Manage_Lenses'] = 'Manage Lenses';
		$IOLs['priv_admn_iols_Lens_Calculators'] = 'Lens Calculators';
		$IOLs['priv_admn_iols_IOL_Users_Lens'] = 'IOL Users Lens';

		 //Reports Tabs Privileges Starts
		$scheduler=array();
		$scheduler = $this->fetchByReportType('scheduler');
		$scheduler['priv_report_Patient_Monitor'] = 'Patient Monitor';
		$scheduler['priv_report_Day_Face_Sheet'] = 'Day Face Sheet';
		$scheduler['priv_report_Appointment_Report'] = 'Appointment Report';
		$scheduler['priv_report_Appointment_information'] = 'Appointment information';
		$scheduler['priv_report_Patient_Document'] = 'Patient Document';
		$scheduler['priv_report_Sx_Planning_Sheet'] = 'Sx Planning Sheet';
		$scheduler['priv_sc_recall_fulfillment'] = 'Recall Fulfillment';
		$scheduler['priv_report_Consult_Letters'] = 'Consult Letters';
		$scheduler['priv_report_Scheduler_Report'] = 'Scheduler Report';
		$scheduler['priv_report_Patients_CSV_Export'] = 'Patients CSV Export';
		$scheduler['priv_report_Surgery_Appointments'] = 'Surgery Appointments';
		$scheduler['priv_report_RTA_Query'] = 'RTA Query';
		$scheduler['priv_report_Clinical_Productivity'] = 'Clinical Productivity';
		$scheduler['priv_report_Providers_Report'] = 'Providers Report';
		$scheduler['priv_report_Procedures_Report'] = 'Procedures Report';

		$analytic_report = array();
		$analytic_report = $this->fetchByReportType('practice_analytic');

		$financial_report = array();
		$financial_report = $this->fetchByReportType('financial');
		$financial_report['priv_prev_hcfa'] = 'Previous HCFA';
		$financial_report['priv_report_eid_status'] = 'EID Status';
		$financial_report['priv_report_EID_Payments'] = 'EID Payments';
		$financial_report['priv_tfl_proof'] = 'TFL Proof';
		//Statements
		$financial_report['priv_new_statements'] = 'New Statement';
		$financial_report['priv_prev_statements'] = 'Previous Statement';
		$financial_report['priv_statements_pay'] = 'Statement Payments';
		//Scheduled Reports
		$financial_report['priv_saved_scheduled'] = 'Saved Schedules';
		$financial_report['priv_executed_report'] = 'Executed Reports';
		$financial_report['priv_pt_status'] = 'PT Status';
		$financial_report['priv_Ins_Enc'] = 'Institutional Encounters';

		$compliance_report = array();
		$compliance_report = $this->fetchByReportType('compliance');
		$compliance_report['priv_report_QRDA'] = 'QRDA';
		$compliance_report['priv_report_CQM_Import'] = 'CQM Import';

		$CCD_report = array();
		$CCD_report['priv_ccd_export'] = 'CCD Export';

		$API_report = array();
		$API_report['priv_report_Access_Log'] = 'Access Log';
		$API_report['priv_report_Call_Log'] = 'Call Log';

		$State_report = array();
		$State_report['priv_report_KY_State_Report'] = 'KY State Report';
		$State_report['priv_report_TN_State_Report'] = 'TN State Report';
		$State_report['priv_report_NC_State_Report'] = 'NC State Report';
		$State_report['priv_report_IL_State_Report'] = 'IL State Report';
			$State_report['priv_report_PA_State_Report'] = 'PA State Report';
		$State_report['priv_report_TX_State_Report'] = 'TX State Report';
		$State_report['priv_report_ASC_State_Report'] = 'ASC State Report';
		$State_report['priv_report_new_state_report'] = 'New State Report';
		$State_report['priv_report_SPARCS_Report'] = 'SPARCS Report';

		$opticals_report = array();
		$opticals_report['priv_cn_reports'] = 'Contact Lens Report';
		$opticals_report['priv_contact_lens'] = 'Contact Lens Orders';
		$opticals_report['priv_glasses'] = 'Glasses';

		$reminders_report = array();
		$reminders_report['priv_dat_appts'] = 'Day Appts';
		$reminders_report['priv_recalls'] = 'Recalls';
		$reminders_report['priv_reminder_lists'] = 'Reminder Lists';

		$clinical_report = array();
		$clinical_report = $this->fetchByReportType('clinical');
		$clinical_report['priv_report_Clinical_Report'] = 'Clinical Report';
		$clinical_report['priv_report_Auto_Finalize_Charts_Report'] = 'Auto Finalize Charts Report';

		$rules_report = array();
		$rules_report['priv_report_A_R_Aging_Rules'] = 'A/R Aging Rules';

		$iportal_report = array();
		$iportal_report['priv_report_Survey'] = 'Survey';

		$misc_report = array(
			'priv_cl_work_view' => 'Work View',
			'priv_cl_tests' => 'Tests',
			'priv_cl_medical_hx' => 'Medical Hx',
			'erx_chk' => 'eRx',
			'priv_break_glass' => 'Break Glass',
			'priv_pis' => 'Patient Information Summary',
			'priv_vo_clinical' => 'Clinical View-Only',
			'priv_chart_finalize' => 'Chart finalize',
			'priv_purge_del_chart' => 'Purge/Delete Chart',
			'priv_record_release' => 'Record Release',
			'priv_Front_Desk' => 'Manager',
			'priv_scheduler_demo' => 'Scheduler/Demo',
			'priv_Sch_Override' => 'Sch. Override',
			'priv_pt_Override' => 'Pt. Override',
			'priv_sch_lock_block' => 'Lock/Block Schedule',
			'priv_sch_telemedicine' => 'Telemedicine',
			'priv_vo_pt_info' => 'View-Only',
			'priv_ac_bill_manager' => 'Manager',
			'priv_Accounting' => 'Accounting',
			'priv_Billing' => 'Billing',
			'priv_edit_financials' => 'Edit Financials',
			'priv_ins_management' => 'Ins. Management',
			'priv_acchx' => 'Account History',
			'priv_vo_charges' => 'Charges',
			'priv_vo_payment' => 'Payment',
			'priv_bi_statements' => 'Statements',
			'priv_bi_day_chrg_rept' => 'Day Charges',
			'priv_bi_edit_batch' => 'Edit Batches',
			'priv_del_payment' => 'Delete Payments',
			'priv_del_charges_enc' => 'Delete Charges/Enc',
			'priv_pt_fdsk' => 'Front Desk',
			'priv_pt_clinical' => 'Clinical',
			'priv_pt_coordinate' => 'Message Coordinator',
			'priv_pt_icon_imm' => 'iMedicMonitor',
			'priv_pt_icon_optical' => 'Optical',
			'priv_pt_icon_iasclink' => 'iASC Link',
			'priv_financial_dashboard' => 'Financial Dashboard',
			'priv_pt_icon_support' => 'Support',
			'priv_financial_hx_cpt' => 'Financial - Hx CPT',
			'priv_ar_worksheet' => 'AR Worksheet',
			'priv_proc_amend' => 'Proc Amendments',
			'priv_def_wnl_stmt' => 'Define WNL Statements',
			'priv_edit_prescriptions' => 'Edit Prescriptions'

		);

		//Reports Tabs Privileges Ends

		$menu_arr = array();
		$menu_arr['Admin'] = $setting;
		$menu_arr['Billing'] = $billing;
		$menu_arr['Clinical'] = $clinical;
		$menu_arr['Documents'] = $documents;
		$menu_arr['iASC_Link'] = $iasc_link;
		$menu_arr['iMedic_Monitor'] = $iMedic_Monitor;
		$menu_arr['iPortal'] = $iPortal;
		$menu_arr['Manage_Fields'] = $Manage_Fields;
		$menu_arr['Optical_Settings'] = $Admin_Optical;
		$menu_arr['Setting_Reports'] = $Admin_Reports;
		$menu_arr['Setting_Scheduler'] = $Admin_Scheduler;
		$menu_arr['IOLs'] = $IOLs;
		$menu_arr['iOptical'] = $Admin_iOptical;
		$menu_arr['Confidential_Text'] = $Admin_iCnfdntlTxt;

		$menu_arr['Scheduler'] = $scheduler;
		$menu_arr['Practice_Analytics'] = $analytic_report;
		$menu_arr['Financials'] = $financial_report;
		$menu_arr['Compliance'] = $compliance_report;
		$menu_arr['CCD'] = $CCD_report;
		$menu_arr['API'] = $API_report;
		$menu_arr['State'] = $State_report;
		$menu_arr['Optical'] = $opticals_report;
		$menu_arr['Reminders'] = $reminders_report;
		$menu_arr['ReportClinical'] = $clinical_report;
		$menu_arr['Rules'] = $rules_report;
		$menu_arr['ReportiPortal'] = $iportal_report;

		if(!empty($more)){
			$menu_arr['Misc'] = $misc_report;
		}

		return $menu_arr;
	}

	function get_sum($sr_str, $flg_htm=0){
		$sr_str = trim($sr_str);
		$str = "";
		$ar_ret = array();
		$ar = $this->fetchMenuArray(1);
		//print_r($sr_str);exit();
		if(!empty($flg_htm)){
		$chk_ar = !empty($sr_str) ? unserialize(html_entity_decode($sr_str)) : array();
		}else{
		$chk_ar = !empty($sr_str) ? unserialize($sr_str) : array();
		}

		if(count($chk_ar) > 0 && count($ar) > 0){
			foreach($chk_ar as $k => $v){
				$chk_k = $k;
				$ar_ret[$k] = $v;
				if(!empty($v)){
					foreach($ar as $k_ar => $v_ar){
						if(isset($v_ar[$chk_k]) && !empty($v_ar[$chk_k])){
							$str .= $v_ar[$chk_k].",";
							break;
						}
					}
				}
			}
		}
		$str = trim($str); $str = trim($str, ",");
		if(!empty($str)){//sort
			$ar_str = explode(",",$str);
			natcasesort($ar_str);
			$str = implode(", ", $ar_str);
		}
		return array($str, $ar_ret);
	}

	function show_list($so, $soAD){
		$q = "SELECT id,gr_name, prevlgs FROM groups_prevlgs WHERE deleted_by='0' ORDER BY $so $soAD";
		$r = imw_query($q);
		$rs_set = array();
		if($r && imw_num_rows($r)>0){
			while($rs = imw_fetch_assoc($r)){
				list($sum, $ar_fields) = $this->get_sum($rs["prevlgs"]);
				$rs["prevlgs"] = $sum;
				$rs = array_merge($rs, $ar_fields);
				$rs_set[] = $rs;
			}
		}
		echo json_encode(array('records'=>$rs_set));

	}

	function get_previleges($id){
		$sum="";
		if(!empty($id)){
		$q = "SELECT id,gr_name, prevlgs FROM groups_prevlgs WHERE id='".$id."' and deleted_by='0' ";
		$r = imw_query($q);
		if($r && imw_num_rows($r)>0){
			$rs = imw_fetch_assoc($r);
			list($sum, $ar_fields) = $this->get_sum($rs["prevlgs"]);
		}
		}
		echo $sum;
	}

	function delete_gp($id){
		$q 		= " UPDATE groups_prevlgs set deleted_by = '2' WHERE id IN (".$id.")";
		$res 	= imw_query($q);
		if($res){
			echo '1';
		}else{
			echo '0';//.imw_error()."\n".$q;
		}
	}

	function get_posted_previliges($flgRet=0){
		//Access Previleges

		$priv_clinial = 0;
		if(isset($_REQUEST["priv_clinical"])){
			$priv_clinial = $_REQUEST["priv_clinical"];
		}
		$priv_cdc=0;
		if(isset($_REQUEST["privilege_cdc"])){
			$priv_cdc=$_REQUEST["privilege_cdc"];
		}
		$priv_erx=0;
		if(isset($_REQUEST["erx_chk"])){
			$priv_erx=$_REQUEST["erx_chk"];
		}

		$priv_cl_work_view = 0;
		$priv_cl_tests = 0;
		$priv_cl_medical_hx = 0;
		$priv_chart_finalize = 1;
		$priv_purge_del_chart = 0;

		if(trim($_REQUEST["priv_cl_work_view"])){
			$priv_cl_work_view = $_REQUEST["priv_cl_work_view"];
		}
		if(trim($_REQUEST["priv_cl_tests"])){
			$priv_cl_tests = $_REQUEST["priv_cl_tests"];
		}
		if(trim($_REQUEST["priv_cl_medical_hx"])){
			$priv_cl_medical_hx = $_REQUEST["priv_cl_medical_hx"];
		}

		$priv_chart_finalize = 0;
		if(trim($_REQUEST["priv_chart_finalize"])){
			$priv_chart_finalize = $_REQUEST["priv_chart_finalize"];
		}

		$priv_purge_del_chart = 0;
		if(trim($_REQUEST["priv_purge_del_chart"])){
			$priv_purge_del_chart = $_REQUEST["priv_purge_del_chart"];
		}

		$priv_record_release = 0;
		if(trim($_REQUEST["priv_record_release"])){
			$priv_record_release = $_REQUEST["priv_record_release"];
		}

		$priv_Front_Desk = 0;
		if(trim($_REQUEST["priv_Front_Desk"])){
			$priv_Front_Desk = $_REQUEST["priv_Front_Desk"];
		}

		$priv_sch_lock_block=0;
		if(trim($_REQUEST["priv_sch_lock_block"])){
			$priv_sch_lock_block = $_REQUEST["priv_sch_lock_block"];
		}

		$priv_sch_telemedicine=0;
		if(trim($_REQUEST["priv_sch_telemedicine"])){
			$priv_sch_telemedicine = $_REQUEST["priv_sch_telemedicine"];
		}

		$priv_scheduler_demo=0;
		if(trim($_REQUEST["priv_scheduler_demo"])){
			$priv_scheduler_demo = $_REQUEST["priv_scheduler_demo"];
		}

		$priv_Billing = 0;
		if(isset($_REQUEST["priv_Billing"])){
			$priv_Billing = $_REQUEST["priv_Billing"];
		}

		$priv_Accounting = 0;
		if(isset($_REQUEST["priv_Accounting"])){
			$priv_Accounting = $_REQUEST["priv_Accounting"];
		}

		if(isset($_REQUEST["priv_Acc_all"])){
			$priv_Acc_all = $_REQUEST["priv_Acc_all"];
		}
		if(isset($_REQUEST["priv_Acc_vonly"])){
			$priv_Acc_vonly = $_REQUEST["priv_Acc_vonly"];
		}

		$priv_Security = 0;
		if(isset($_REQUEST["priv_Security"])){
			$priv_Security = $_REQUEST["priv_Security"];
		}

		$priv_cnfdntl_txt = 0;
		if(isset($_REQUEST["priv_cnfdntl_txt"])){
			$priv_cnfdntl_txt = $_REQUEST["priv_cnfdntl_txt"];
		}

		$priv_Reports_manager = 0;
		if(isset($_REQUEST["priv_Reports_manager"])){
			$priv_Reports_manager = $_REQUEST["priv_Reports_manager"];
		}

		$priv_sc_daily = 0;
		if(isset($_REQUEST["priv_sc_daily"])){
			$priv_sc_daily = $_REQUEST["priv_sc_daily"];
		}
		$priv_acct_receivable = 0;
		if(isset($_REQUEST["priv_acct_receivable"])){
			$priv_acct_receivable = $_REQUEST["priv_acct_receivable"];
		}
		$priv_bi_analytics = 0;
		if(isset($_REQUEST["priv_bi_analytics"])){
			$priv_bi_analytics = $_REQUEST["priv_bi_analytics"];
		}


		//========================//
		$priv_report_payments = 0;
		if(trim($_REQUEST["priv_report_payments"])){
			$priv_report_payments = $_REQUEST["priv_report_payments"];
		}

		$priv_report_copay_rocan = 0;
		if(trim($_REQUEST["priv_report_copay_rocan"])){
			$priv_report_copay_rocan = $_REQUEST["priv_report_copay_rocan"];
		}

		$priv_un_superbills = 0;
		if(trim($_REQUEST["priv_un_superbills"])){
			$priv_un_superbills = $_REQUEST["priv_un_superbills"];
		}

		$priv_un_encounters = 0;
		if(isset($_REQUEST["priv_un_encounters"])){
			$priv_un_encounters = $_REQUEST["priv_un_encounters"];
		}

		$priv_un_payments = 0;
		if(trim($_REQUEST["priv_un_payments"])){
			$priv_un_payments = $_REQUEST["priv_un_payments"];
		}

		$priv_report_adjustment = 0;
		if(trim($_REQUEST["priv_report_adjustment"])){
			$priv_report_adjustment = $_REQUEST["priv_report_adjustment"];
		}

		$priv_report_refund = 0;
		if(trim($_REQUEST["priv_report_refund"])){
			$priv_report_refund = $_REQUEST["priv_report_refund"];
		}

		$priv_daily_balance = 0;
		if(trim($_REQUEST["priv_daily_balance"])){
			$priv_daily_balance = $_REQUEST["priv_daily_balance"];
		}

		$priv_fd_collection = 0;
		if(trim($_REQUEST["priv_fd_collection"])){
			$priv_fd_collection = $_REQUEST["priv_fd_collection"];
		}

		$priv_report_practice_analytics = 0;
		if(trim($_REQUEST["priv_report_practice_analytics"])){
			$priv_report_practice_analytics = $_REQUEST["priv_report_practice_analytics"];
		}

		$priv_cpt_analysis = 0;
		if(isset($_REQUEST["priv_cpt_analysis"])){
			$priv_cpt_analysis = $_REQUEST["priv_cpt_analysis"];
		}

		$priv_report_yearly = 0;
		if(trim($_REQUEST["priv_report_yearly"])){
			$priv_report_yearly = $_REQUEST["priv_report_yearly"];
		}

		$priv_report_revenue = 0;
		if(trim($_REQUEST["priv_report_revenue"])){
			$priv_report_revenue = $_REQUEST["priv_report_revenue"];
		}

		$priv_provider_mon = 0;
		if(trim($_REQUEST["priv_provider_mon"])){
			$priv_provider_mon = $_REQUEST["priv_provider_mon"];
		}

		$priv_ref_phy_monthly = 0;
		if(trim($_REQUEST["priv_ref_phy_monthly"])){
			$priv_ref_phy_monthly = $_REQUEST["priv_ref_phy_monthly"];
		}

		$priv_facility_monthly = 0;
		if(trim($_REQUEST["priv_facility_monthly"])){
			$priv_facility_monthly = $_REQUEST["priv_facility_monthly"];
		}

		$priv_report_ref_phy = 0;
		if(trim($_REQUEST["priv_report_ref_phy"])){
			$priv_report_ref_phy = $_REQUEST["priv_report_ref_phy"];
		}

		$priv_credit_analysis = 0;
		if(trim($_REQUEST["priv_credit_analysis"])){
			$priv_credit_analysis = $_REQUEST["priv_credit_analysis"];
		}

		$priv_report_patient = 0;
		if(trim($_REQUEST["priv_report_patient"])){
			$priv_report_patient = $_REQUEST["priv_report_patient"];
		}

		$priv_report_ins_cases = 0;
		if(trim($_REQUEST["priv_report_ins_cases"])){
			$priv_report_ins_cases = $_REQUEST["priv_report_ins_cases"];
		}

		$priv_report_eid_status = 0;
		if(trim($_REQUEST["priv_report_eid_status"])){
			$priv_report_eid_status = $_REQUEST["priv_report_eid_status"];
		}

		$priv_allowable_verify = 0;
		if(trim($_REQUEST["priv_allowable_verify"])){
			$priv_allowable_verify = $_REQUEST["priv_allowable_verify"];
		}

		$priv_vip_deferred = 0;
		if(trim($_REQUEST["priv_vip_deferred"])){
			$priv_vip_deferred = $_REQUEST["priv_vip_deferred"];
		}

		$priv_provider_rvu = 0;
		if(trim($_REQUEST["priv_provider_rvu"])){
			$priv_provider_rvu = $_REQUEST["priv_provider_rvu"];
		}

		$priv_sx_payment = 0;
		if(trim($_REQUEST["priv_sx_payment"])){
			$priv_sx_payment = $_REQUEST["priv_sx_payment"];
		}

		$priv_net_gross = 0;
		if(trim($_REQUEST["priv_net_gross"])){
			$priv_net_gross = $_REQUEST["priv_net_gross"];
		}

		$priv_ar_reports = 0;
		if(trim($_REQUEST["priv_ar_reports"])){
			$priv_ar_reports = $_REQUEST["priv_ar_reports"];
		}

		$priv_days_ar = 0;
		if(trim($_REQUEST["priv_days_ar"])){
			$priv_days_ar = $_REQUEST["priv_days_ar"];
		}

		$priv_receivables = 0;
		if(trim($_REQUEST["priv_receivables"])){
			$priv_receivables = $_REQUEST["priv_receivables"];
		}

		$priv_unworked_ar = 0;
		if(trim($_REQUEST["priv_unworked_ar"])){
			$priv_unworked_ar = $_REQUEST["priv_unworked_ar"];
		}

		$priv_unbilled_claims = 0;
		if(trim($_REQUEST["priv_unbilled_claims"])){
			$priv_unbilled_claims = $_REQUEST["priv_unbilled_claims"];
		}

		$priv_top_rej_reason = 0;
		if(trim($_REQUEST["priv_top_rej_reason"])){
			$priv_top_rej_reason = $_REQUEST["priv_top_rej_reason"];
		}

		$priv_new_statements = 0;
		if(trim($_REQUEST["priv_new_statements"])){
			$priv_new_statements = $_REQUEST["priv_new_statements"];
		}

		$priv_prev_statements = 0;
		if(trim($_REQUEST["priv_prev_statements"])){
			$priv_prev_statements = $_REQUEST["priv_prev_statements"];
		}

		$priv_prev_hcfa = 0;
		if(trim($_REQUEST["priv_prev_hcfa"])){
			$priv_prev_hcfa = $_REQUEST["priv_prev_hcfa"];
		}

		$priv_statements_pay = 0;
		if(trim($_REQUEST["priv_statements_pay"])){
			$priv_statements_pay = $_REQUEST["priv_statements_pay"];
		}

		$priv_pt_statements = 0;
		if(trim($_REQUEST["priv_pt_statements"])){
			$priv_pt_statements = $_REQUEST["priv_pt_statements"];
		}

		$priv_pt_collections = 0;
		if(trim($_REQUEST["priv_pt_collections"])){
			$priv_pt_collections = $_REQUEST["priv_pt_collections"];
		}

		$priv_assessment = 0;
		if(trim($_REQUEST["priv_assessment"])){
			$priv_assessment = $_REQUEST["priv_assessment"];
		}

		$priv_collection_report = 0;
		if(trim($_REQUEST["priv_collection_report"])){
			$priv_collection_report = $_REQUEST["priv_collection_report"];
		}

		$priv_tfl_proof = 0;
		if(trim($_REQUEST["priv_tfl_proof"])){
			$priv_tfl_proof = $_REQUEST["priv_tfl_proof"];
		}

		$priv_report_rta = 0;
		if(trim($_REQUEST["priv_report_rta"])){
			$priv_report_rta = $_REQUEST["priv_report_rta"];
		}

		$priv_billing_verification = 0;
		if(trim($_REQUEST["priv_billing_verification"])){
			$priv_billing_verification = $_REQUEST["priv_billing_verification"];
		}

		$priv_patient_status = 0;
		if(trim($_REQUEST["priv_patient_status"])){
			$priv_patient_status = $_REQUEST["priv_patient_status"];
		}

		$priv_saved_scheduled = 0;
		if(trim($_REQUEST["priv_saved_scheduled"])){
			$priv_saved_scheduled = $_REQUEST["priv_saved_scheduled"];
		}

		$priv_executed_report = 0;
		if(trim($_REQUEST["priv_executed_report"])){
			$priv_executed_report = $_REQUEST["priv_executed_report"];
		}

		$priv_cn_pending = 0;
		if(trim($_REQUEST["priv_cn_pending"])){
			$priv_cn_pending = $_REQUEST["priv_cn_pending"];
		}

		$priv_contact_lens = 0;
		if(trim($_REQUEST["priv_contact_lens"])){
			$priv_contact_lens = $_REQUEST["priv_contact_lens"];
		}

		$priv_cn_ordered = 0;
		if(trim($_REQUEST["priv_cn_ordered"])){
			$priv_cn_ordered = $_REQUEST["priv_cn_ordered"];
		}

		$priv_cn_received = 0;
		if(trim($_REQUEST["priv_cn_received"])){
			$priv_cn_received = $_REQUEST["priv_cn_received"];
		}

		$priv_cn_dispensed = 0;
		if(trim($_REQUEST["priv_cn_dispensed"])){
			$priv_cn_dispensed = $_REQUEST["priv_cn_dispensed"];
		}

		$priv_cn_reports = 0;
		if(trim($_REQUEST["priv_cn_reports"])){
			$priv_cn_reports = $_REQUEST["priv_cn_reports"];
		}

		$priv_glasses = 0;
		if(trim($_REQUEST["priv_glasses"])){
			$priv_glasses = $_REQUEST["priv_glasses"];
		}
		$priv_gl_pending = 0;
		if(trim($_REQUEST["priv_gl_pending"])){
			$priv_gl_pending = $_REQUEST["priv_gl_pending"];
		}

		$priv_gl_ordered = 0;
		if(trim($_REQUEST["priv_gl_ordered"])){
			$priv_gl_ordered = $_REQUEST["priv_gl_ordered"];
		}

		$priv_gl_received = 0;
		if(trim($_REQUEST["priv_gl_received"])){
			$priv_gl_received = $_REQUEST["priv_gl_received"];
		}

		$priv_gl_dispensed = 0;
		if(trim($_REQUEST["priv_gl_dispensed"])){
			$priv_gl_dispensed = $_REQUEST["priv_gl_dispensed"];
		}

		$priv_gl_report = 0;
		if(trim($_REQUEST["priv_gl_report"])){
			$priv_gl_report = $_REQUEST["priv_gl_report"];
		}

		$priv_alerts = 0;
		if(trim($_REQUEST["priv_alerts"])){
			$priv_alerts = $_REQUEST["priv_alerts"];
		}
		$priv_stage_iv = 0;
		if(trim($_REQUEST["priv_stage_iv"])){
			$priv_stage_iv = $_REQUEST["priv_stage_iv"];
		}
		$priv_stage_i = 0;
		if(trim($_REQUEST["priv_stage_i"])){
			$priv_stage_i = $_REQUEST["priv_stage_i"];
		}
		$priv_stage_ii = 0;
		if(trim($_REQUEST["priv_stage_ii"])){
			$priv_stage_ii = $_REQUEST["priv_stage_ii"];
		}

		$priv_stage_iii = 0;
		if(trim($_REQUEST["priv_stage_iii"])){
			$priv_stage_iii = $_REQUEST["priv_stage_iii"];
		}

		$priv_ccd_export = 0;
		if(trim($_REQUEST["priv_ccd_export"])){
			$priv_ccd_export = $_REQUEST["priv_ccd_export"];
		}

		$priv_ccd_import = 0;
		if(trim($_REQUEST["priv_ccd_import"])){
			$priv_ccd_import = $_REQUEST["priv_ccd_import"];
		}

		$priv_lab_import = 0;
		if(trim($_REQUEST["priv_lab_import"])){
			$priv_lab_import = $_REQUEST["priv_lab_import"];
		}

		$priv_ccr_import = 0;
		if(trim($_REQUEST["priv_ccr_import"])){
			$priv_ccr_import = $_REQUEST["priv_ccr_import"];
		}

		$priv_dat_appts = 0;
		if(trim($_REQUEST["priv_dat_appts"])){
			$priv_dat_appts = $_REQUEST["priv_dat_appts"];
		}

		$priv_recalls = 0;
		if(trim($_REQUEST["priv_recalls"])){
			$priv_recalls = $_REQUEST["priv_recalls"];
		}

		$priv_reminder_lists = 0;
		if(trim($_REQUEST["priv_reminder_lists"])){
			$priv_reminder_lists = $_REQUEST["priv_reminder_lists"];
		}

		$priv_no_shows = 0;
		if(trim($_REQUEST["priv_no_shows"])){
			$priv_no_shows = $_REQUEST["priv_no_shows"];
		}

		$ccr_exist_pat = 0;
		if(trim($_REQUEST["ccr_exist_pat"])){
			$ccr_exist_pat = $_REQUEST["ccr_exist_pat"];
		}

		$ccr_new_pat = 0;
		if(trim($_REQUEST["ccr_new_pat"])){
			$ccr_new_pat = $_REQUEST["ccr_new_pat"];

		}

		//========================//
		$priv_sc_scheduler = 0;
		$priv_sc_house_calls = 0;
		$priv_sc_recall_fulfillment = 0;

		$priv_bi_front_desk = 0;
		$priv_bi_ledger = 0;
		$priv_bi_prod_payroll = 0;
		$priv_bi_ar = 0;
		$priv_bi_statements = 0;
		$priv_bi_day_chrg_rept = 0;
		$priv_bi_edit_batch = 0;
		$priv_financial_hx_cpt = 0;
		$priv_purge_del_chart = 0;
		$priv_record_release = 0;
		$priv_bi_end_of_day = 0;

		$priv_cl_clinical = 0;
		$priv_cl_visits = 0;
		$priv_cl_ccd = 0;
		$priv_cl_order_set = 0;

		if(isset($_REQUEST["priv_sc_scheduler"])){
			$priv_sc_scheduler = $_REQUEST["priv_sc_scheduler"];
		}
		if(isset($_REQUEST["priv_sc_house_calls"])){
			$priv_sc_house_calls = $_REQUEST["priv_sc_house_calls"];
		}
		$priv_billing_fun=0;
		if(isset($_REQUEST["priv_billing_fun"])){
			$priv_billing_fun = $_REQUEST["priv_billing_fun"];
		}
		if(isset($_REQUEST["priv_sc_recall_fulfillment"])){
			$priv_sc_recall_fulfillment = $_REQUEST["priv_sc_recall_fulfillment"];
		}

		if(isset($_REQUEST["priv_bi_front_desk"])){
			$priv_bi_front_desk = $_REQUEST["priv_bi_front_desk"];
		}
		if(isset($_REQUEST["priv_bi_ledger"])){
			$priv_bi_ledger = $_REQUEST["priv_bi_ledger"];
		}
		if(isset($_REQUEST["priv_bi_prod_payroll"])){
			$priv_bi_prod_payroll = $_REQUEST["priv_bi_prod_payroll"];
		}
		if(isset($_REQUEST["priv_bi_ar"])){

			$priv_bi_ar = $_REQUEST["priv_bi_ar"];
		}
		if(isset($_REQUEST["priv_bi_statements"])){
			$priv_bi_statements = $_REQUEST["priv_bi_statements"];
		}
		if(isset($_REQUEST["priv_bi_day_chrg_rept"])){
			$priv_bi_day_chrg_rept = $_REQUEST["priv_bi_day_chrg_rept"];
		}
		if(isset($_REQUEST["priv_bi_edit_batch"])){
			$priv_bi_edit_batch = $_REQUEST["priv_bi_edit_batch"];
		}
		if(isset($_REQUEST["priv_financial_hx_cpt"])){
			$priv_financial_hx_cpt = $_REQUEST["priv_financial_hx_cpt"];
		}
		if(isset($_REQUEST["priv_purge_del_chart"])){
			$priv_purge_del_chart = $_REQUEST["priv_purge_del_chart"];
		}
		if(isset($_REQUEST["priv_record_release"])){
			$priv_record_release = $_REQUEST["priv_record_release"];
		}
		if(isset($_REQUEST["priv_bi_end_of_day"])){
			$priv_bi_end_of_day = $_REQUEST["priv_bi_end_of_day"];
		}

		if(isset($_REQUEST["priv_cl_clinical"])){
			$priv_cl_clinical = $_REQUEST["priv_cl_clinical"];
		}
		if(isset($_REQUEST["priv_cl_visits"])){
			$priv_cl_visits = $_REQUEST["priv_cl_visits"];
		}
		if(isset($_REQUEST["priv_cl_ccd"])){
			$priv_cl_ccd = $_REQUEST["priv_cl_ccd"];
		}
		if(isset($_REQUEST["priv_cl_order_set"])){
			$priv_cl_order_set = $_REQUEST["priv_cl_order_set"];
		}
		if(isset($_REQUEST["priv_no_reports"])){
			$priv_no_reports = $_REQUEST["priv_no_reports"];
		}


		$priv_View_Only = 0;
		if(isset($_REQUEST["priv_View_Only"])){
			$priv_View_Only = $_REQUEST["priv_View_Only"];
		}

		$priv_vo_clinical = 0;
		$priv_vo_pt_info = 0;
		$priv_vo_acc = 0;
		$priv_vo_charges = 0;
		$priv_vo_payment = 0;

		if(isset($_REQUEST["priv_vo_clinical"])){
			$priv_vo_clinical = $_REQUEST["priv_vo_clinical"];
		}

		if(isset($_REQUEST["priv_vo_pt_info"])){
			$priv_vo_pt_info = $_REQUEST["priv_vo_pt_info"];
		}
		if(isset($_REQUEST["priv_vo_acc"])){
			$priv_vo_acc = $_REQUEST["priv_vo_acc"];
		}
		if(isset($_REQUEST["priv_vo_charges"])){
			$priv_vo_charges = $_REQUEST["priv_vo_charges"];
		}
		if(isset($_REQUEST["priv_vo_payment"])){
			$priv_vo_payment = $_REQUEST["priv_vo_payment"];
		}
		$priv_del_charges_enc=0;
		if(isset($_REQUEST["priv_del_charges_enc"])){
			$priv_del_charges_enc = $_REQUEST["priv_del_charges_enc"];
		}

		$priv_del_payment=0;
		if(isset($_REQUEST["priv_del_payment"])){
			$priv_del_payment = $_REQUEST["priv_del_payment"];
		}
		$priv_Sch_Override = 0;
		if(isset($_REQUEST["priv_Sch_Override"])){
			$priv_Sch_Override = $_REQUEST["priv_Sch_Override"];
		}
		$priv_pt_Override = 0;
		if(isset($_REQUEST["priv_pt_Override"])){
			$priv_pt_Override = $_REQUEST["priv_pt_Override"];
		}

		$priv_ac_bill_manager=0;
		if(isset($_REQUEST["priv_ac_bill_manager"])){
			$priv_ac_bill_manager = $_REQUEST["priv_ac_bill_manager"];
		}
		$priv_admin = 0;
		if(isset($_REQUEST["priv_admin"])){
			$priv_admin = $_REQUEST["priv_admin"];
		}
		$priv_all_settings = 0;
		if(isset($_REQUEST["priv_all_settings"])){
			$priv_all_settings = $_REQUEST["priv_all_settings"];
		}

		$priv_group=0;
		if(isset($_REQUEST["priv_group"])){
			$priv_group = $_REQUEST["priv_group"];
		}
		$priv_facility=0;
		if(isset($_REQUEST["priv_facility"])){
			$priv_facility = $_REQUEST["priv_facility"];
		}
		$priv_document=0;
		if(isset($_REQUEST["priv_document"])){
			$priv_document = $_REQUEST["priv_document"];
		}
		$priv_iols=0;
		if(isset($_REQUEST["priv_iols"])){
			$priv_iols = $_REQUEST["priv_iols"];
		}
		$priv_console=0;
		if(isset($_REQUEST["priv_console"])){
			$priv_console = $_REQUEST["priv_console"];
		}
		$priv_report_financials=0;
		if(isset($_REQUEST["priv_report_financials"])){
			$priv_report_financials = $_REQUEST["priv_report_financials"];
		}
		$priv_report_tests=0;
		if(isset($_REQUEST["priv_report_tests"])){
			$priv_report_tests = $_REQUEST["priv_report_tests"];
		}
		$priv_report_optical=0;
		if(isset($_REQUEST["priv_report_optical"])){
			$priv_report_optical = $_REQUEST["priv_report_optical"];
		}
		$priv_report_reminders=0;
		if(isset($_REQUEST["priv_report_reminders"])){
			$priv_report_reminders = $_REQUEST["priv_report_reminders"];
		}
		$priv_report_audit=0;
		if(isset($_REQUEST["priv_report_audit"])){
			$priv_report_audit = $_REQUEST["priv_report_audit"];
		}
		$priv_pt_instruction=0;
		if(isset($_REQUEST["priv_pt_instruction"])){
			$priv_pt_instruction = $_REQUEST["priv_pt_instruction"];
		}
		$priv_report_mur=0;
		if(isset($_REQUEST["priv_report_mur"])){
			$priv_report_mur = $_REQUEST["priv_report_mur"];
		}
		$priv_report_schduled=0;
		if(isset($_REQUEST["priv_report_schduled"])){
			$priv_report_schduled = $_REQUEST["priv_report_schduled"];
		}

		$priv_admin_billing=0;
		if(isset($_REQUEST["priv_admin_billing"])){
			$priv_admin_billing = $_REQUEST["priv_admin_billing"];
		}
		$priv_set_margin=0;
		if(isset($_REQUEST["priv_set_margin"])){
			$priv_set_margin = $_REQUEST["priv_set_margin"];
		}
		$priv_erx_preferences=0;
		if(isset($_REQUEST["priv_erx_preferences"])){
			$priv_erx_preferences = $_REQUEST["priv_erx_preferences"];
		}
		$priv_room_assign=0;
		if(isset($_REQUEST["priv_room_assign"])){
			$priv_room_assign = $_REQUEST["priv_room_assign"];
		}
		$priv_chart_notes=0;
		if(isset($_REQUEST["priv_chart_notes"])){
			$priv_chart_notes = $_REQUEST["priv_chart_notes"];
		}
		$priv_admin_scp=0;
		if(isset($_REQUEST["priv_admin_scp"])){
			$priv_admin_scp = $_REQUEST["priv_admin_scp"];
		}
		$priv_vs=0;
		if(isset($_REQUEST["priv_vs"])){
			$priv_vs = $_REQUEST["priv_vs"];
		}
		$priv_immunization=0;
		if(isset($_REQUEST["priv_immunization"])){
			$priv_immunization = $_REQUEST["priv_immunization"];
		}
		$priv_manage_fields=0;
		if(isset($_REQUEST["priv_manage_fields"])){
			$priv_manage_fields = $_REQUEST["priv_manage_fields"];
		}
		$priv_orders=0;
		if(isset($_REQUEST["priv_orders"])){
			$priv_orders = $_REQUEST["priv_orders"];
		}
		$priv_iportal=0;
		if(isset($_REQUEST["priv_iportal"])){
			$priv_iportal = $_REQUEST["priv_iportal"];
		}
		$priv_pis=0;
		if(isset($_REQUEST["priv_pis"])){
			$priv_pis = $_REQUEST["priv_pis"];
		}

		$priv_provider_management=0;
		if(isset($_REQUEST["priv_provider_management"])){
			$priv_provider_management = $_REQUEST["priv_provider_management"];
		}
		$priv_break_glass = 0;
		if(isset($_REQUEST["priv_break_glass"])){
			$priv_break_glass = $_REQUEST["priv_break_glass"];
		}
		$priv_edit_financials = 0;
		if(isset($_REQUEST["priv_edit_financials"])){
			$priv_edit_financials = $_REQUEST["priv_edit_financials"];
		}
		$priv_ref_physician=0;
		if(isset($_REQUEST["priv_ref_physician"])){
			$priv_ref_physician = $_REQUEST["priv_ref_physician"];
		}
		$priv_admin_scheduler=0;
		if(isset($_REQUEST["priv_admin_scheduler"])){
			$priv_admin_scheduler = $_REQUEST["priv_admin_scheduler"];
		}
		$priv_admin_billing=0;
		if(isset($_REQUEST["priv_admin_billing"])){
			$priv_admin_billing = $_REQUEST["priv_admin_billing"];
		}
		$priv_admin_clinical=0;
		if(isset($_REQUEST["priv_admin_clinical"])){
			$priv_admin_clinical = $_REQUEST["priv_admin_clinical"];
		}
		$priv_iMedicMonitor=0;
		if(isset($_REQUEST["priv_iMedicMonitor"])){
			$priv_iMedicMonitor = $_REQUEST["priv_iMedicMonitor"];
		}
		$priv_Admin_Optical=0;
		if(isset($_REQUEST["priv_Admin_Optical"])){
			$priv_Admin_Optical = $_REQUEST["priv_Admin_Optical"];
		}
		$priv_Admin_Reports=0;
		if(isset($_REQUEST["priv_Admin_Reports"])){
			$priv_Admin_Reports = $_REQUEST["priv_Admin_Reports"];
		}
		$priv_ins_management=0;
		if(isset($_REQUEST["priv_ins_management"])){
			$priv_ins_management = $_REQUEST["priv_ins_management"];
		}
		$priv_Optical = 0;
		if(isset($_REQUEST["priv_Optical"])){
			$priv_Optical = $_REQUEST["priv_Optical"];
		}
		$priv_Optical_POS = 0;
		if(isset($_REQUEST["priv_Optical_POS"])){
			$priv_Optical_POS = $_REQUEST["priv_Optical_POS"];
		}
		$priv_Optical_Inventory = 0;
		if(isset($_REQUEST["priv_Optical_Inventory"])){
			$priv_Optical_Inventory = $_REQUEST["priv_Optical_Inventory"];
		}
		$priv_Optical_Admin = 0;
		if(isset($_REQUEST["priv_Optical_Admin"])){
			$priv_Optical_Admin = $_REQUEST["priv_Optical_Admin"];
		}
		$priv_Optical_Reports = 0;
		if(isset($_REQUEST["priv_Optical_Reports"])){
			$priv_Optical_Reports = $_REQUEST["priv_Optical_Reports"];
		}

		$priv_iOLink = 0;
		if(isset($_REQUEST["priv_iOLink"])){
			$priv_iOLink = $_REQUEST["priv_iOLink"];
		}
		$priv_acchx = 0;
		if(isset($_REQUEST["priv_acchx"])){
			$priv_acchx = $_REQUEST["priv_acchx"];
		}

		$priv_pt_fdsk=0;
		if(isset($_REQUEST["priv_pt_fdsk"])){
			$priv_pt_fdsk = $_REQUEST["priv_pt_fdsk"];
		}

		$priv_pt_clinical=0;
		if(isset($_REQUEST["priv_pt_clinical"])){
			$priv_pt_clinical = $_REQUEST["priv_pt_clinical"];
		}
		$priv_documents=0;
		if(isset($_REQUEST["priv_documents"])){
			$priv_documents=$_REQUEST["priv_documents"];
		}
		$priv_alerts=0;
		if(isset($_REQUEST["priv_alerts"])){
			$priv_alerts=$_REQUEST["priv_alerts"];
		}
		$priv_pt_icon_imm=0;
		if(isset($_REQUEST["priv_pt_icon_imm"])){
			$priv_pt_icon_imm = $_REQUEST["priv_pt_icon_imm"];
		}
		$priv_pt_icon_optical=0;
		if(isset($_REQUEST["priv_pt_icon_optical"])){
			$priv_pt_icon_optical = $_REQUEST["priv_pt_icon_optical"];
		}
		$priv_pt_icon_iasclink=0;
		if(isset($_REQUEST["priv_pt_icon_iasclink"])){
			$priv_pt_icon_iasclink = $_REQUEST["priv_pt_icon_iasclink"];
		}
		$priv_financial_dashboard=0;
		if(isset($_REQUEST["priv_financial_dashboard"])){
			$priv_financial_dashboard = $_REQUEST["priv_financial_dashboard"];
		}
		$priv_pt_icon_support=0;
		if(isset($_REQUEST["priv_pt_icon_support"])){
			$priv_pt_icon_support = $_REQUEST["priv_pt_icon_support"];
		}
		$priv_ar_worksheet=0;
		if(isset($_REQUEST["priv_ar_worksheet"])){
			$priv_ar_worksheet = $_REQUEST["priv_ar_worksheet"];
		}

		$priv_api_access = 0;
		if(trim($_REQUEST["priv_api_access"])){
			$priv_api_access = $_REQUEST["priv_api_access"];
		}

		$priv_grp_prvlgs = !empty($_REQUEST["priv_grp_prvlgs"]) ? $_REQUEST["priv_grp_prvlgs"] : 0;
		$priv_chng_prvlgs = !empty($_REQUEST["priv_chng_prvlgs"]) ? $_REQUEST["priv_chng_prvlgs"] : 0;
		$priv_rules_mngr = !empty($_REQUEST["priv_rules_mngr"]) ? $_REQUEST["priv_rules_mngr"] : 0;
		$priv_proc_amend = !empty($_REQUEST["priv_proc_amend"]) ? $_REQUEST["priv_proc_amend"] : 0;
		$priv_def_wnl_stmt = !empty($_REQUEST["priv_def_wnl_stmt"]) ? $_REQUEST["priv_def_wnl_stmt"] : 0;
		$priv_edit_prescriptions = !empty($_REQUEST["priv_edit_prescriptions"]) ? $_REQUEST["priv_edit_prescriptions"] : 0;

		$priv_report_compliance = 0;
		if(trim($_REQUEST["priv_report_compliance"])){
			$priv_report_compliance = $_REQUEST["priv_report_compliance"];
		}

		$priv_report_State = 0;
		if(trim($_REQUEST["priv_report_State"])){
			$priv_report_State = $_REQUEST["priv_report_State"];
		}
		$priv_report_api_access = 0;
		if(trim($_REQUEST["priv_report_api_access"])){
			$priv_report_api_access = $_REQUEST["priv_report_api_access"];
		}
		$priv_report_Rules = 0;
		if(trim($_REQUEST["priv_report_Rules"])){
			$priv_report_Rules = $_REQUEST["priv_report_Rules"];
		}
		$priv_report_iPortal = 0;
		if(trim($_REQUEST["priv_report_iPortal"])){
			$priv_report_iPortal = $_REQUEST["priv_report_iPortal"];
		}

		$pt_coodinator_priv=0;
		if($_REQUEST["priv_pt_coordinate"]){
			$pt_coodinator_priv=1;
		}

		$el_sel_settings = ($_REQUEST["el_sel_settings"]) ? 1 : 0;
		$el_sel_clinical = ($_REQUEST["el_sel_clinical"]) ? 1 : 0;
		$el_sel_fd = ($_REQUEST["el_sel_fd"]) ? 1 : 0;
		$el_sel_acc = ($_REQUEST["el_sel_acc"]) ? 1 : 0;
		$el_sel_rprt = ($_REQUEST["el_sel_rprt"]) ? 1 : 0;
		$el_sel_portal = ($_REQUEST["el_sel_portal"]) ? 1 : 0;
		$el_sel_icon = ($_REQUEST["el_sel_icon"]) ? 1 : 0;

        $menu_array = $this->fetchMenuArray();

        $privCheck=array();
        foreach($menu_array as $tab_head) {
            foreach($tab_head as $field_name => $label) {
                $privCheck[$field_name] = 0;
                if(trim($_REQUEST[$field_name])){
                    $privCheck[$field_name] = $_REQUEST[$field_name];
                }
            }
        }

        $privileges_temp = array();
        foreach($privCheck as $field => $priv) {
            $privileges_temp[$field] = intval($privCheck[$field]);
        }

		$privileges = array(
								"priv_edit_prescriptions" => intval($priv_edit_prescriptions),
								"priv_def_wnl_stmt" => intval($priv_def_wnl_stmt),
								"priv_proc_amend" => intval($priv_proc_amend),
								"priv_api_access" => intval($priv_api_access),
								"priv_grp_prvlgs" => intval($priv_grp_prvlgs),
								"priv_chng_prvlgs" => intval($priv_chng_prvlgs),
								"priv_rules_mngr" => intval($priv_rules_mngr),
								"priv_cl_work_view" => intval($priv_cl_work_view),
								"priv_cl_tests" => intval($priv_cl_tests),
								"priv_cl_medical_hx" => intval($priv_cl_medical_hx),
								"priv_chart_finalize" => intval($priv_chart_finalize),
								"priv_purge_del_chart" => intval($priv_purge_del_chart),
								"priv_record_release" => intval($priv_record_release),

								"priv_Front_Desk" => intval($priv_Front_Desk),
								"priv_sch_lock_block" => intval($priv_sch_lock_block),
								"priv_sch_telemedicine" => intval($priv_sch_telemedicine),
								"priv_scheduler_demo" => intval($priv_scheduler_demo),
								"priv_Billing" => intval($priv_Billing),
								"priv_Accounting" => intval($priv_Accounting),
								"priv_Acc_all" => intval($priv_Acc_all),
								"priv_Acc_vonly" => intval($priv_Acc_vonly),
								"priv_Security" => intval($priv_Security),
								"priv_cnfdntl_txt" => intval($priv_cnfdntl_txt),

								"priv_Reports_manager" => intval($priv_Reports_manager),
								"priv_sc_daily" => intval($priv_sc_daily),
								"priv_acct_receivable" => intval($priv_acct_receivable),
								"priv_bi_analytics" => intval($priv_bi_analytics),
								"priv_sc_scheduler" => intval($priv_sc_scheduler),
								"priv_sc_house_calls" => intval($priv_sc_house_calls),
								"priv_billing_fun" => intval($priv_billing_fun),

								"priv_report_compliance" => intval($priv_report_compliance),
								"priv_report_State" => intval($priv_report_State),
								"priv_report_api_access" => intval($priv_report_api_access),
								"priv_report_Rules" => intval($priv_report_Rules),
								"priv_report_iPortal" => intval($priv_report_iPortal),

								"priv_sc_recall_fulfillment" => intval($priv_sc_recall_fulfillment),
								"priv_bi_front_desk" => intval($priv_bi_front_desk),
								"priv_bi_ledger" => intval($priv_bi_ledger),
								"priv_bi_prod_payroll" => intval($priv_bi_prod_payroll),
								"priv_bi_ar" => intval($priv_bi_ar),
								"priv_bi_statements" => intval($priv_bi_statements),
								"priv_bi_day_chrg_rept" => intval($priv_bi_day_chrg_rept),
								"priv_bi_edit_batch" => intval($priv_bi_edit_batch),
								"priv_financial_hx_cpt" => intval($priv_financial_hx_cpt),
								"priv_purge_del_chart" => intval($priv_purge_del_chart),
								"priv_record_release" => intval($priv_record_release),
								"priv_bi_end_of_day" => intval($priv_bi_end_of_day),
								"priv_cl_clinical" => intval($priv_cl_clinical),
								"priv_cl_visits" => intval($priv_cl_visits),
								"priv_cl_ccd" => intval($priv_cl_ccd),
								"priv_cl_order_set" => intval($priv_cl_order_set),

								"priv_vo_clinical" => intval($priv_vo_clinical),
								"priv_vo_pt_info" => intval($priv_vo_pt_info),
								"priv_vo_acc" => intval($priv_vo_acc),
								"priv_vo_charges" => intval($priv_vo_charges),
								"priv_vo_payment" => intval($priv_vo_payment),
								"priv_del_charges_enc" => intval($priv_del_charges_enc),

								"priv_del_payment" => intval($priv_del_payment),


								"priv_Sch_Override" => intval($priv_Sch_Override),
								"priv_pt_Override" => intval($priv_pt_Override),
								"priv_ac_bill_manager" => intval($priv_ac_bill_manager),

								"priv_admin" => intval($priv_admin),
								"priv_all_settings" => intval($priv_all_settings),
								"priv_provider_management" => intval($priv_provider_management),

								"priv_group" => intval($priv_group),
								"priv_facility" => intval($priv_facility),
								"priv_document" => intval($priv_document),
								"priv_admin_billing" => intval($priv_admin_billing),
								"priv_admin_clinical" => intval($priv_admin_clinical),
                                "priv_iMedicMonitor" => intval($priv_iMedicMonitor),
                                "priv_Admin_Optical" => intval($priv_Admin_Optical),
                                "priv_Admin_Reports" => intval($priv_Admin_Reports),
								"priv_set_margin" => intval($priv_set_margin),
								"priv_erx_preferences" => intval($priv_erx_preferences),
								"priv_room_assign" => intval($priv_room_assign),
								"priv_chart_notes" => intval($priv_chart_notes),
								"priv_admin_scp" => intval($priv_admin_scp),
								"priv_vs" => intval($priv_vs),
								"priv_immunization" => intval($priv_immunization),
								"priv_manage_fields" => intval($priv_manage_fields),
								"priv_orders" => intval($priv_orders),
								"priv_iportal" => intval($priv_iportal),
								"priv_iols" => intval($priv_iols),
								"priv_console"=>intval($priv_console),
								"priv_report_financials"=>intval($priv_report_financials),
								"priv_report_tests"=>intval($priv_report_tests),
								"priv_report_optical"=>intval($priv_report_optical),
								"priv_report_reminders"=>intval($priv_report_reminders),
								"priv_report_audit"=>intval($priv_report_audit),
								"priv_pt_instruction"=>intval($priv_pt_instruction),
								"priv_report_mur"=>intval($priv_report_mur),
								"priv_report_schduled"=>intval($priv_report_schduled),

								"priv_Optical" => intval($priv_Optical),
								"priv_Optical_POS" => intval($priv_Optical_POS),
								"priv_Optical_Inventory" => intval($priv_Optical_Inventory),
								"priv_Optical_Admin" => intval($priv_Optical_Admin),
								"priv_Optical_Reports" => intval($priv_Optical_Reports),

								"priv_iOLink" => intval($priv_iOLink),
								"priv_break_glass" => intval($priv_break_glass),
								"priv_edit_financials" => intval($priv_edit_financials),
								"priv_ref_physician" => intval($priv_ref_physician),
								"priv_admin_scheduler" => intval($priv_admin_scheduler),
								"priv_admin_billing" => intval($priv_admin_billing),
								"priv_ins_management" => intval($priv_ins_management),

								"priv_pt_fdsk" => intval($priv_pt_fdsk),
								"priv_pt_clinical" => intval($priv_pt_clinical),
								"priv_no_reports" => intval($priv_no_reports),
								"priv_cdc" => intval($priv_cdc),
								"priv_acchx" => intval($priv_acchx),
								"priv_erx" => intval($priv_erx),

								"priv_pt_icon_imm"			=>	intval($priv_pt_icon_imm),
								"priv_pt_icon_optical"		=>	intval($priv_pt_icon_optical),
								"priv_pt_icon_iasclink"		=>	intval($priv_pt_icon_iasclink),
								"priv_financial_dashboard"	=>	intval($priv_financial_dashboard),
								"priv_pt_icon_support"		=>	intval($priv_pt_icon_support),
								"priv_ar_worksheet"			=>	intval($priv_ar_worksheet),

								"priv_report_payments" => intval($priv_report_payments),
								"priv_report_copay_rocan" => intval($priv_report_copay_rocan),
								"priv_un_superbills" => intval($priv_un_superbills),
								"priv_un_encounters" => intval($priv_un_encounters),
								"priv_un_payments" => intval($priv_un_payments),
								"priv_report_adjustment" => intval($priv_report_adjustment),
								"priv_report_refund" => intval($priv_report_refund),
								"priv_daily_balance" => intval($priv_daily_balance),
								"priv_fd_collection" => intval($priv_fd_collection),
								"priv_report_practice_analytics" => intval($priv_report_practice_analytics),
								"priv_cpt_analysis" => intval($priv_cpt_analysis),
								"priv_report_yearly" => intval($priv_report_yearly),
								"priv_report_revenue" => intval($priv_report_revenue),
								"priv_provider_mon" => intval($priv_provider_mon),
								"priv_ref_phy_monthly" => intval($priv_ref_phy_monthly),
								"priv_facility_monthly" => intval($priv_facility_monthly),
								"priv_report_ref_phy" => intval($priv_report_ref_phy),
								"priv_credit_analysis" => intval($priv_credit_analysis),
								"priv_report_patient" => intval($priv_report_patient),
								"priv_report_ins_cases" => intval($priv_report_ins_cases),
								"priv_report_eid_status" => intval($priv_report_eid_status),
								"priv_allowable_verify" => intval($priv_allowable_verify),
								"priv_vip_deferred" => intval($priv_vip_deferred),
								"priv_provider_rvu" => intval($priv_provider_rvu),
								"priv_sx_payment" => intval($priv_sx_payment),
								"priv_net_gross" => intval($priv_net_gross),
								"priv_ar_reports" => intval($priv_ar_reports),
								"priv_days_ar" => intval($priv_days_ar),
								"priv_receivables" => intval($priv_receivables),
								"priv_unworked_ar" => intval($priv_unworked_ar),
								"priv_unbilled_claims" => intval($priv_unbilled_claims),
								"priv_top_rej_reason" => intval($priv_top_rej_reason),
								"priv_new_statements" => intval($priv_new_statements),
								"priv_prev_statements" => intval($priv_prev_statements),
								"priv_report_payments" => intval($priv_report_payments),
								"priv_prev_hcfa" => intval($priv_prev_hcfa),
								"priv_statements_pay" => intval($priv_statements_pay),
								"priv_pt_statements" => intval($priv_pt_statements),
								"priv_pt_collections" => intval($priv_pt_collections),
								"priv_assessment" => intval($priv_assessment),
								"priv_collection_report" => intval($priv_collection_report),
								"priv_tfl_proof" => intval($priv_tfl_proof),
								"priv_report_rta" => intval($priv_report_rta),
								"priv_billing_verification" => intval($priv_billing_verification),
								"priv_patient_status" => intval($priv_patient_status),
								"priv_saved_scheduled" => intval($priv_saved_scheduled),
								"priv_executed_report" => intval($priv_executed_report),
								"priv_cn_pending" => intval($priv_cn_pending),
								"priv_contact_lens" => intval($priv_contact_lens),
								"priv_cn_ordered" => intval($priv_cn_ordered),
								"priv_cn_received" => intval($priv_cn_received),
								"priv_cn_dispensed" => intval($priv_cn_dispensed),
								"priv_cn_reports" => intval($priv_cn_reports),
								"priv_glasses" => intval($priv_glasses),
								"priv_gl_pending" => intval($priv_gl_pending),
								"priv_gl_ordered" => intval($priv_gl_ordered),
								"priv_gl_received" => intval($priv_gl_received),
								"priv_gl_dispensed" => intval($priv_gl_dispensed),
								"priv_gl_report" => intval($priv_gl_report),
								"priv_documents" => intval($priv_documents),
								"priv_alerts" => intval($priv_alerts),
								"priv_stage_iv" => intval($priv_stage_iv),
								"priv_stage_i" => intval($priv_stage_i),
								"priv_stage_ii" => intval($priv_stage_ii),
								"priv_stage_iii" => intval($priv_stage_iii),
								"priv_ccd_export" => intval($priv_ccd_export),
								"priv_ccd_import" => intval($priv_ccd_import),
								"priv_lab_import" => intval($priv_lab_import),
								"priv_ccr_import" => intval($priv_ccr_import),
								"priv_dat_appts" => intval($priv_dat_appts),
								"priv_recalls" => intval($priv_recalls),
								"priv_reminder_lists" => intval($priv_reminder_lists),
								"priv_no_shows" => intval($priv_no_shows),
								"ccr_exist_pat"=>intval($ccr_exist_pat),
								"ccr_new_pat"=>intval($ccr_exist_pat),
								"priv_pis" => intval($priv_pis),
								"priv_pt_coordinate" => intval($pt_coodinator_priv),
								"el_sel_settings" => intval($el_sel_settings),
								"el_sel_clinical" => intval($el_sel_clinical),
								"el_sel_fd" => intval($el_sel_fd),
								"el_sel_acc" => intval($el_sel_acc),
								"el_sel_rprt" => intval($el_sel_rprt),
								"el_sel_portal" => intval($el_sel_portal),
								"el_sel_icon" => intval($el_sel_icon)
							);

        $privileges = array_merge($privileges, $privileges_temp);

		/*
		//patch # 20100901 to accomodate the fact that admin has all privileges by default - starts here
		if($privileges["priv_all_settings"] == 1){
		//if($privileges["priv_admin"] == 1){
			$privileges = array(
								"priv_api_access" => intval($priv_api_access),
								"priv_grp_prvlgs" => intval($priv_grp_prvlgs),
								"priv_chng_prvlgs" => intval($priv_chng_prvlgs),
								"priv_rules_mngr" => intval($priv_rules_mngr),
								"priv_cl_work_view" => 1,
								"priv_cl_tests" => 1,
								"priv_cl_medical_hx" => 1,
								"priv_chart_finalize" => intval($priv_chart_finalize),

								"priv_Front_Desk" => 1,
								"priv_sch_lock_block" =>  1,
								"priv_sch_telemedicine" =>  0,
								"priv_scheduler_demo" => 1,
								"priv_Billing" => intval($priv_Billing),
								"priv_Accounting" => 1,
								"priv_Acc_all" => 1,
								"priv_Acc_vonly" => 0,
								"priv_Security" => intval($priv_Security),

								"priv_sc_scheduler" => intval($priv_sc_scheduler),
								"priv_sc_house_calls" => intval($priv_sc_house_calls),
								"priv_sc_recall_fulfillment" => intval($priv_sc_recall_fulfillment),

								"priv_bi_ledger" => 1,
								"priv_bi_prod_payroll" => 1,
								"priv_bi_ar" => 1,

								"priv_bi_end_of_day" => 1,
								"priv_cl_clinical" => intval($priv_cl_clinical),
								"priv_cl_visits" => 1,
								"priv_cl_ccd" => intval($priv_cl_ccd),
								"priv_cl_order_set" => 1,

								"priv_vo_clinical" => 0,
								"priv_vo_pt_info" => 0,
								"priv_vo_acc" => 0,
								"priv_vo_charges" => 1,
								"priv_vo_payment" => 1,
								"priv_del_payment" => intval($priv_del_payment),
								"priv_del_charges_enc" => intval($priv_del_charges_enc),


								"priv_group" => intval($priv_group),
								"priv_facility" => intval($priv_facility),
								"priv_document" => intval($priv_document),
								"priv_admin_billing" => intval($priv_admin_billing),
								"priv_admin_clinical" => intval($priv_admin_clinical),
								"priv_iMedicMonitor" => intval($priv_iMedicMonitor),
								"priv_Admin_Optical" => intval($priv_Admin_Optical),
								"priv_Admin_Reports" => intval($priv_Admin_Reports),
								"priv_set_margin" => intval($priv_set_margin),
								"priv_erx_preferences" => intval($priv_erx_preferences),
								"priv_room_assign" => intval($priv_room_assign),
								"priv_chart_notes" => intval($priv_chart_notes),
								"priv_admin_scp" => intval($priv_admin_scp),
								"priv_vs" => intval($priv_vs),
								"priv_immunization" => intval($priv_immunization),
								"priv_manage_fields" => intval($priv_manage_fields),
								"priv_orders" => intval($priv_orders),
								"priv_iportal" => intval($priv_iportal),
								"priv_iols" => ($priv_iols),
								"priv_console"=>intval($priv_console),
								"priv_report_financials"=>intval($priv_report_financials),
								"priv_report_tests"=>intval($priv_report_tests),
								"priv_report_optical"=>intval($priv_report_optical),
								"priv_report_reminders"=>intval($priv_report_reminders),
								"priv_report_audit"=>intval($priv_report_audit),
								"priv_pt_instruction"=>intval($priv_pt_instruction),
								"priv_report_mur"=>intval($priv_report_mur),
								"priv_report_schduled"=>intval($priv_report_schduled),


								"priv_Sch_Override" => 1,
								"priv_pt_Override" => 1,

								"priv_ac_bill_manager" => 1,

								"priv_admin" => 1,
								"priv_all_settings" => 1,
								"priv_provider_management" => intval($priv_provider_management),

								"priv_Optical" => intval($priv_Optical),
								"priv_Optical_POS" => intval($priv_Optical_POS),
								"priv_Optical_Inventory" => intval($priv_Optical_Inventory),
								"priv_Optical_Admin" => intval($priv_Optical_Admin),
								"priv_Optical_Reports" => intval($priv_Optical_Reports),

								"priv_iOLink" => intval($priv_iOLink),
								"priv_break_glass" => 1,
								"priv_edit_financials" => intval($priv_edit_financials),
								"priv_ref_physician" => intval($priv_ref_physician),
								"priv_admin_scheduler" =>intval($priv_admin_scheduler),
								"priv_admin_billing" =>intval($priv_admin_billing),
								"priv_ins_management" => intval($priv_ins_management),
								"priv_cdc" => 1,

								"priv_Reports_manager" => intval($priv_Reports_manager),
								"priv_sc_daily" => intval($priv_sc_daily),
								"priv_acct_receivable" => intval($priv_acct_receivable),
								"priv_bi_analytics" => intval($priv_bi_analytics),
								"priv_bi_statements" => 1,
                                "priv_bi_day_chrg_rept" => intval($priv_bi_day_chrg_rept),
                                "priv_financial_hx_cpt" => intval($priv_financial_hx_cpt),
								"priv_purge_del_chart" => intval($priv_purge_del_chart),
								"priv_record_release" => intval($priv_record_release),
								"priv_bi_front_desk" => 1,
								"priv_billing_fun" => intval($priv_billing_fun),
								"priv_pt_fdsk" => intval($priv_pt_fdsk),
								"priv_pt_clinical" => intval($priv_pt_clinical),

								"priv_erx" => 1,
								"priv_acchx" => 1,
								"pt_coordinator"=>1,

								"priv_pt_icon_imm"			=>	intval($priv_pt_icon_imm),
								"priv_pt_icon_optical"		=>	intval($priv_pt_icon_optical),
								"priv_pt_icon_iasclink"		=>	intval($priv_pt_icon_iasclink),
								"priv_financial_dashboard"	=>	intval($priv_financial_dashboard),
								"priv_pt_icon_support"		=>	intval($priv_pt_icon_support),

                                "priv_report_compliance" => intval($priv_report_compliance),
								"priv_report_State" => intval($priv_report_State),
								"priv_report_api_access" => intval($priv_report_api_access),
								"priv_report_Rules" => intval($priv_report_Rules),
								"priv_report_iPortal" => intval($priv_report_iPortal),

								"priv_report_payments" => intval($priv_report_payments),
								"priv_report_copay_rocan" => intval($priv_report_copay_rocan),
								"priv_un_superbills" => intval($priv_un_superbills),
								"priv_un_encounters" => intval($priv_un_encounters),
								"priv_un_payments" => intval($priv_un_payments),
								"priv_report_adjustment" => intval($priv_report_adjustment),
								"priv_report_refund" => intval($priv_report_refund),
								"priv_daily_balance" => intval($priv_daily_balance),
								"priv_fd_collection" => intval($priv_fd_collection),
								"priv_report_practice_analytics" => intval($priv_report_practice_analytics),
								"priv_cpt_analysis" => intval($priv_cpt_analysis),
								"priv_report_yearly" => intval($priv_report_yearly),
								"priv_report_revenue" => intval($priv_report_revenue),
								"priv_provider_mon" => intval($priv_provider_mon),
								"priv_ref_phy_monthly" => intval($priv_ref_phy_monthly),
								"priv_facility_monthly" => intval($priv_facility_monthly),
								"priv_report_ref_phy" => intval($priv_report_ref_phy),
								"priv_credit_analysis" => intval($priv_credit_analysis),
								"priv_report_patient" => intval($priv_report_patient),
								"priv_report_ins_cases" => intval($priv_report_ins_cases),
								"priv_report_eid_status" => intval($priv_report_eid_status),
								"priv_allowable_verify" => intval($priv_allowable_verify),
								"priv_vip_deferred" => intval($priv_vip_deferred),
								"priv_provider_rvu" => intval($priv_provider_rvu),
								"priv_sx_payment" => intval($priv_sx_payment),
								"priv_net_gross" => intval($priv_net_gross),
								"priv_ar_reports" => intval($priv_ar_reports),
								"priv_days_ar" => intval($priv_days_ar),
								"priv_receivables" => intval($priv_receivables),
								"priv_unworked_ar" => intval($priv_unworked_ar),
								"priv_unbilled_claims" => intval($priv_unbilled_claims),
								"priv_top_rej_reason" => intval($priv_top_rej_reason),
								"priv_new_statements" => intval($priv_new_statements),
								"priv_prev_statements" => intval($priv_prev_statements),
								"priv_report_payments" => intval($priv_report_payments),
								"priv_prev_hcfa" => intval($priv_prev_hcfa),
								"priv_statements_pay" => intval($priv_statements_pay),
								"priv_pt_statements" => intval($priv_pt_statements),
								"priv_pt_collections" => intval($priv_pt_collections),
								"priv_assessment" => intval($priv_assessment),
								"priv_collection_report" => intval($priv_collection_report),
								"priv_tfl_proof" => intval($priv_tfl_proof),
								"priv_report_rta" => intval($priv_report_rta),
								"priv_billing_verification" => intval($priv_billing_verification),
								"priv_patient_status" => intval($priv_patient_status),
								"priv_saved_scheduled" => intval($priv_saved_scheduled),
								"priv_executed_report" => intval($priv_executed_report),
								"priv_cn_pending" => intval($priv_cn_pending),
								"priv_contact_lens" => intval($priv_contact_lens),
								"priv_cn_ordered" => intval($priv_cn_ordered),
								"priv_cn_received" => intval($priv_cn_received),
								"priv_cn_dispensed" => intval($priv_cn_dispensed),
								"priv_cn_reports" => intval($priv_cn_reports),
								"priv_glasses" => intval($priv_glasses),
								"priv_gl_pending" => intval($priv_gl_pending),
								"priv_gl_ordered" => intval($priv_gl_ordered),
								"priv_gl_received" => intval($priv_gl_received),
								"priv_gl_dispensed" => intval($priv_gl_dispensed),
								"priv_gl_report" => intval($priv_gl_report),
								"priv_documents" => intval($priv_documents),
								"priv_alerts" => intval($priv_alerts),
								"priv_stage_iv" => intval($priv_stage_iv),
								"priv_stage_i" => intval($priv_stage_i),
								"priv_stage_ii" => intval($priv_stage_ii),
								"priv_stage_iii" => intval($priv_stage_iii),
								"priv_ccd_export" => intval($priv_ccd_export),
								"priv_ccd_import" => intval($priv_ccd_import),
								"priv_lab_import" => intval($priv_lab_import),
								"priv_ccr_import" => intval($priv_ccr_import),
								"priv_dat_appts" => intval($priv_dat_appts),
								"priv_recalls" => intval($priv_recalls),
								"priv_reminder_lists" => intval($priv_reminder_lists),
								"priv_no_shows" => intval($priv_no_shows),
								"ccr_exist_pat"=>intval($ccr_exist_pat),
								"ccr_new_pat"=>intval($ccr_exist_pat),
								"priv_pis" => intval($priv_pis),
								"priv_pt_coordinate" => intval($pt_coodinator_priv),
								"el_sel_settings" => intval($el_sel_settings),
								"el_sel_clinical" => intval($el_sel_clinical),
								"el_sel_fd" => intval($el_sel_fd),
								"el_sel_acc" => intval($el_sel_acc),
								"el_sel_rprt" => intval($el_sel_rprt),
								"el_sel_portal" => intval($el_sel_portal),
								"el_sel_icon" => intval($el_sel_icon)
							);

            $privileges = array_merge($privileges, $privileges_temp);
		}
		//patch # 20100901 ends here
		if($privileges["priv_api_access"] == 1){
		    $privileges["priv_report_api_access"] = 1;
		    $privileges["priv_report_Access_Log"] = 1;
		    $privileges["priv_report_Call_Log"] = 1;
		}
		//*/

		//echo "<pre>";
		//ksort($privileges);
		//print_r($privileges);
		//exit();

		$prevlgs = serialize($privileges);
		if(!empty($flgRet)){
		return array($prevlgs, $privileges);
		}else{
		return $prevlgs;
		}
	}

	function save(){
		$id = $_POST["id"];
		unset($_POST["id"]);
		unset($_POST['task']);

		// --
		$prevlgs = $this->get_posted_previliges();
		//--
		$query_part = "";
		$query_part .= "gr_name = '".sqlEscStr($_POST["gr_name"])."', ";
		$query_part .= "prevlgs = '".sqlEscStr($prevlgs)."' ";
		//--

		$qry_con = "";
		if($id){$qry_con=" AND id!='".$id."'";}
		$q_c="SELECT id from groups_prevlgs WHERE gr_name='".$_POST["gr_name"]."' AND deleted_by='0' ".$qry_con;
		$r_c=imw_query($q_c);
		if(imw_num_rows($r_c)==0){

			if($id==''){
				$q = "INSERT INTO groups_prevlgs SET ".$query_part;
			}else{
				$q = "UPDATE groups_prevlgs SET ".$query_part." WHERE id = '".$id."'";
			}
			$res = imw_query($q);
			if($res){
				echo 'Record Saved Successfully.';
			}else{
				echo 'Record Saving failed.'.imw_error()."\n".$q;
			}
		}else {
			echo "enter_unique";
		}


	}


	function get_users_list(){
		$ousr = new User();
		$str = $ousr->users_getAccordian();
		echo  $str;
	}

	function save_previleges(){

		// --
		$prevlgs = $this->get_posted_previliges();
		//--
		$privileges = $_REQUEST["el_privileges"];
		$uids = trim($_REQUEST["uids"]);
		$uids = trim($uids,",");
		$pt_coordinator=$_REQUEST["priv_pt_coordinate"];

		if(!empty($privileges)){
			$str_phrase=" groups_prevlgs_id='".sqlEscStr($privileges)."' ";
			$prevlgs="";
		}else{
			$str_phrase=" access_pri = '".sqlEscStr($prevlgs)."', groups_prevlgs_id='', pt_coordinator='".$pt_coordinator."' ";
		}

		if(!empty($str_phrase) ){
		$sql = "UPDATE users set ".$str_phrase." WHERE id IN (".$uids.") ";
		$res = imw_query($sql);
		}
		if($res){
			echo 'Privileges are changed successfully.';

			//
			$sql = "INSERT INTO log_change_prvlgs VALUES (NULL, '".$_SESSION["authId"]."','".date("Y-m-d H:i:s")."','".sqlEscStr($prevlgs)."','".sqlEscStr($privileges)."','".$uids."') ";
			$res = imw_query($sql);

		}else{
			echo 'Privileges change failed.'.imw_error()."\n".$sql;
		}
	}

	 function get_privileges_opts($sid=0, $flg_cus=0){
		$sel_cus = "selected";
		$str = "";
		$q = "SELECT id, gr_name FROM groups_prevlgs where deleted_by='0' Order By gr_name";
		$query = imw_query($q);
		while($row = imw_fetch_assoc($query)){
			$id = $row['id'];
			$grp_name = $row['gr_name'];
			$sel =  "";
			if($sid == $id){
				$sel =  "selected" ; $sel_cus = "";
			}
			$str .= "<option value=\"".$id."\" ".$sel.">".$grp_name."</option>";

		}
		if(!empty($flg_cus)){
			$str .= "<option value=\"-1\" ".$sel_cus." >Custom</option>";
		}
		return $str;
	}

	function get_report_data($primaryProviderId){

		$arr_pro = array();
		if(!empty($primaryProviderId)){
			$user_query = "select c1.id, c1.fname, c1.mname, c1.lname, c1.access_pri, c1.groups_prevlgs_id, c2.prevlgs, c2.gr_name
						from users c1
						LEFT JOIN groups_prevlgs c2 ON c1.groups_prevlgs_id = c2.id
						where delete_status=0 AND c1.id IN (".$primaryProviderId.")";

			$user_query_res = imw_query($user_query);
			while ($user_res = imw_fetch_array($user_query_res)) {
				$sel='';
				$user_id = $user_res['id'];

				$user_name = "";
				if(!empty($user_res["fname"])){  $user_name .=$user_res["fname"]." "; }
				if(!empty($user_res["mname"])){  $user_name .=$user_res["mname"]." "; }
				if(!empty($user_res["lname"])){  $user_name .=$user_res["lname"]." "; }
				$user_name = trim($user_name);

				$grp_prvlg = $prmsns =  "";
				$grp_prvlg = $user_res["gr_name"];
				if(empty($grp_prvlg)){ $grp_prvlg = "Custom"; list($prmsns, $ar_fields) = $this->get_sum($user_res["access_pri"],1); }
				else{ list($prmsns, $ar_fields) = $this->get_sum($user_res["prevlgs"]); }

				//
				$arr_pro[$user_id] = array("user"=>$user_name, "grp_prvlg"=>$grp_prvlg, "prmsns"=>$prmsns);
			}
		}
		return $arr_pro;
	}

	function get_users_of_grp($id){
		$str = "";
		$msg = "Are you sure you want to delete?";
		$q 	= " SELECT fname, lname FROM users WHERE groups_prevlgs_id IN (".$id.")";
		$res 	= imw_query($q);
		$i=0;
		while ($row = imw_fetch_array($res)) {
			$i++;
			$str .= "<br/>&nbsp;&nbsp;-&nbsp;".$row["fname"]." ".$row["lname"];

		}
		if(!empty($str)){
			$msg = $msg."<br/>Following <span class=\"label label-warning\">".$i."</span> users will be affected:-<div id=\"aff_usrs\" style=\"max-height:100px; overflow:auto;\">".$str."</div>";
		}
		echo $msg;
	}

	function show_log(){
		$sql = "SELECT c1.*, c2.gr_name FROM log_change_prvlgs c1
				LEFT JOIN groups_prevlgs c2 ON c1.groups_prevlgs_id = c2.id
				ORDER BY op_tm DESC ";
		$res = sqlStatement($sql);
		for($i=1; $row=sqlFetchArray($res); $i++){
			$groups_prevlgs_id = $row["groups_prevlgs_id"];
			$access_pri = $row["access_pri"];
			$effcted_uids = $row["effcted_uids"];
			$gr_name = $row["gr_name"];
			$op_id = $row["op_id"];
			$str_usrs = "";
			if(!empty($effcted_uids)){
				$ar_usr_ids = explode(",", $effcted_uids);
				$ar_usr=array();
				if(count($ar_usr_ids)){
					foreach($ar_usr_ids as $k => $v){
						$v = trim($v);
						if(!empty($v)){
							$ousr = new User($v);
							$ar_usr[] = $ousr->getName(3);
						}
					}
					if(count($ar_usr)>0){
						$str_usrs = implode(", ", $ar_usr);
					}
				}
			}

			$sum="";
			if(empty($groups_prevlgs_id) && !empty($access_pri)){
				list($sum, $ar_fields) = $this->get_sum($access_pri);
			}

			$op_name="";
			if(!empty($op_id)){
				$ousr = new User($op_id);
				$op_name = $ousr->getName(3);
			}

			$op_tm = wv_formatDate($row["op_tm"],0,1);
			$op_tm = str_replace(" ", "<br/>", $op_tm);

			$str .= "<tr valign=\"top\"><td  >".$op_tm."</td><td >".$op_name."</td><td class=\"text-justify\">".$str_usrs."</td><td class=\"text-justify\">".$sum."</td><td>".$gr_name."</td></tr>";

		}

		if(!empty($str)){
			$str = "<div id=\"logdv\" class=\"table-responsive\"><table class=\"table table-striped table-bordered\"><tr><th>Time</th><th>Operator</th><th>Affected Users</th><th>Privileges</th><th>Group Name</th></tr>".$str."</table></div>";
		}else{ $str = "No record found."; }
		echo $str;
	}

}
?>
