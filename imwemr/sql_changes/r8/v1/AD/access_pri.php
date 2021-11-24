<?php
$ignoreAuth = true;
include("../../../../config/globals.php");

$billing = array();
$billing['priv_admin_billing'] = 1;
$billing['priv_billing_Adjustment_Codes'] = 1;
$billing['priv_billing_Cases'] = 1;
$billing['priv_billing_CPT'] = 1;
$billing['priv_billing_Department'] = 1;
$billing['priv_billing_Discount_Codes'] = 1;
$billing['priv_billing_Dx_Codes'] = 1;
$billing['priv_billing_Fee_Table'] = 1;
$billing['priv_billing_ICD_10'] = 1;
$billing['priv_billing_Insurance'] = 1;
$billing['priv_billing_Insurance_Groups'] = 1;
$billing['priv_billing_Messages'] = 1;
$billing['priv_billing_Modifiers'] = 1;
$billing['priv_billing_Phrases'] = 1;
$billing['priv_billing_POE'] = 1;
$billing['priv_billing_Policies'] = 1;
$billing['priv_billing_POS_Codes'] = 1;
$billing['priv_billing_POS_Facilities'] = 1;
$billing['priv_billing_Proc_Codes'] = 1;
$billing['priv_billing_Reason_Codes'] = 1;
$billing['priv_billing_Revenue_Codes'] = 1;
$billing['priv_billing_Status'] = 1;
$billing['priv_billing_Type_Of_Service'] = 1;
$billing['priv_billing_Write_Off_Codes'] = 1;
        
$console = array();
$console['priv_admin_clinical'] = 1;
$console['priv_admin_billing'] = 1;
$console['priv_Admin_Reports'] = 1;
$console['priv_document'] = 1;
$console['priv_admn_clinical_Allergies'] = 1;
$console['priv_admn_clinical_AP_Policies'] = 1;
$console['priv_admn_clinical_Epost'] = 1;
$console['priv_admn_clinical_Med'] = 1;
$console['priv_admn_clinical_Phrases'] = 1;
$console['priv_admn_clinical_Procedures'] = 1;
$console['priv_admn_clinical_Rx_Template'] = 1;
$console['priv_admn_clinical_Sx'] = 1;
$console['priv_admn_clinical_Sx_Planning'] = 1;
$console['priv_billing_Zip_Codes'] = 1;
$console['priv_admin_Heard_About_Us'] = 1;
$console['priv_admin_Provider_Groups'] = 1;
$console['priv_billing_Pre_Auth_Templates'] = 1;
$console['priv_admn_docs_Prescriptions'] = 1;
$console['priv_admn_docs_Scan_Upload_Folders'] = 1;

$console['priv_admn_reports_Audit_Policies'] = 1;
$console['priv_admn_reports_CPT_Groups'] = 1;
$console['priv_admn_reports_Fac_Groups'] = 1;
$console['priv_admn_reports_Ref_Groups'] = 1;
//$console['priv_admn_reports_Compliance'] = 1;
//$console['priv_admn_reports_Financials'] = 1;
//$console['priv_admn_reports_Practice_Analytic'] = 1;
//$console['priv_admn_reports_Scheduler'] = 1;

$chart_notes = array();
$chart_notes['priv_admin_clinical'] = 1;
$chart_notes['priv_admin_billing'] = 1;
$chart_notes['priv_admn_clinical_Botox'] = 1;
$chart_notes['priv_admn_clinical_Drawings'] = 1;
$chart_notes['priv_admn_clinical_FU'] = 1;
$chart_notes['priv_admn_clinical_Labs_Rad'] = 1;
$chart_notes['priv_admn_clinical_Ophth_Drops'] = 1;
$chart_notes['priv_admn_clinical_Pt_Chart_Locked'] = 1;
$chart_notes['priv_admn_clinical_SCP_Reasons'] = 1;
$chart_notes['priv_admn_clinical_Specialty'] = 1;
$chart_notes['priv_admn_clinical_Template'] = 1;
$chart_notes['priv_admn_clinical_Test_Template'] = 1; //Test Profiles and Test Templates => Test Templates
$chart_notes['priv_admn_clinical_Visit'] = 1;
$chart_notes['priv_admn_clinical_WNL'] = 1;
$chart_notes['priv_billing_Test_CPT_Preference'] = 1;   //Tests => Test CPT Preference

$order = array();
$order['priv_admin_clinical'] = 1;
$order['priv_admn_clinical_Order'] = 1;
$order['priv_admn_clinical_Order_Sets'] = 1;
$order['priv_admn_clinical_Order_Templates'] = 1;
        
$iPortal = array();
$iPortal['priv_iportal'] = 1;
$iPortal['priv_admn_ipl_Auto_Responder'] = 1;
$iPortal['priv_admn_ipl_iPortal_Settings'] = 1;
$iPortal['priv_admn_ipl_Preferred_Images'] = 1;
$iPortal['priv_admn_ipl_Print_Preferences'] = 1;
$iPortal['priv_admn_ipl_Security_Questions'] = 1;
$iPortal['priv_admn_ipl_Set_Survey'] = 1;
$iPortal['priv_admn_ipl_Survey'] = 1;

$Manage_Fields = array();
$Manage_Fields['priv_manage_fields'] = 1;
$Manage_Fields['priv_admn_mcf_Custom_Fields'] = 1;
$Manage_Fields['priv_admn_mcf_General_Health_Questns'] = 1;
$Manage_Fields['priv_admn_mcf_Ocular_Questions'] = 1;
$Manage_Fields['priv_admn_mcf_Practice_Fields'] = 1;
$Manage_Fields['priv_admn_mcf_Tech_Fields'] = 1;

$iasc_link = array();
$iasc_link['priv_iOLink'] = 1;
$iasc_link['priv_admn_iasc_iASC_Link_Settings'] = 1;
$iasc_link['priv_admn_iasc_Surgery_Consent_Form'] = 1;

$IOLs = array();
$IOLs['priv_iols'] = 1;
$IOLs['priv_admn_iols_Manage_Lenses'] = 1;
$IOLs['priv_admn_iols_Lens_Calculators'] = 1;
$IOLs['priv_admn_iols_IOL_Users_Lens'] = 1;

$Admin_Optical = array();
$Admin_Optical['priv_Optical'] = 1;
$Admin_Optical['priv_admin_billing'] = 1;
$Admin_Optical['priv_Admin_Optical'] = 1;
$Admin_Optical['priv_admn_optical_Frames'] = 1;
$Admin_Optical['priv_admn_optical_Lenses'] = 1;
$Admin_Optical['priv_admn_optical_Vendor'] = 1;
$Admin_Optical['priv_admn_optical_Contact_Lens'] = 1;
$Admin_Optical['priv_admn_optical_Color'] = 1;
$Admin_Optical['priv_admn_optical_Lens_Codes'] = 1;
$Admin_Optical['priv_admn_optical_Make'] = 1;
$Admin_Optical['priv_billing_CL_Charges'] = 1;

$Admin_iOptical = array();
$Admin_iOptical['priv_Optical'] = 1;
$Admin_iOptical['priv_Admin_Optical'] = 1;
$Admin_iOptical['priv_Optical_POS'] = 1;
$Admin_iOptical['priv_Optical_Inventory'] = 1;
$Admin_iOptical['priv_Optical_Admin'] = 1;
$Admin_iOptical['priv_Optical_Reports'] = 1;

$documents = array();
$documents['priv_document'] = 1;
$documents['priv_admn_docs_Collection'] = 1;
$documents['priv_admn_docs_Consent'] = 1;
$documents['priv_admn_docs_Consult'] = 1;
$documents['priv_admn_docs_Education'] = 1;
$documents['priv_admn_docs_Instructions'] = 1;
$documents['priv_admn_docs_Logos'] = 1;
$documents['priv_admn_docs_Op_Notes'] = 1;
$documents['priv_admn_docs_Package'] = 1;
$documents['priv_admn_docs_Panels'] = 1;
//$documents['priv_admn_docs_Prescriptions'] = 1;
$documents['priv_admn_docs_Pt_Docs'] = 1;
$documents['priv_admn_docs_Recalls'] = 1;
//$documents['priv_admn_docs_Scan_Upload_Folders'] = 1;
//$documents['priv_set_margin'] = 1;
$documents['priv_admn_docs_Smart_Tags'] = 1;
$documents['priv_admn_docs_Statements'] = 1;

$Admin_Scheduler = array();
$Admin_Scheduler['priv_admin_scheduler'] = 1;
$Admin_Scheduler['priv_admn_sch_Available'] = 1;
$Admin_Scheduler['priv_admn_sch_Chain_Event'] = 1;
$Admin_Scheduler['priv_admn_sch_Procedure_Templates'] = 1;
$Admin_Scheduler['priv_admn_sch_Provider_Schedule'] = 1;
$Admin_Scheduler['priv_admn_sch_Schedule_Reasons'] = 1;
$Admin_Scheduler['priv_admn_sch_Schedule_Status'] = 1;
$Admin_Scheduler['priv_admn_sch_Schedule_Templates'] = 1;  


        
$counter=0;
//$query=imw_query("Select id,access_pri from users where id=1");
$query_rs=imw_query("Select id,access_pri from users");
while ($row = imw_fetch_assoc($query_rs)) {
    $user_priviliges=unserialize(html_entity_decode(trim($row['access_pri'])));

    $privileges=array();
    foreach($user_priviliges as $key => $val) {
        $privileges[$key]=$val;
        if($val==1) {
            switch($key) {
                case 'priv_admin_billing':
                    $privileges = array_merge($privileges, $billing);
                    break;
                case 'priv_console':
                    $privileges = array_merge($privileges, $console);
                    break;
                case 'priv_chart_notes':
                    $privileges = array_merge($privileges, $chart_notes);
                    break;
                case 'priv_orders':
                    $privileges = array_merge($privileges, $order);
                    break;
                case 'priv_iportal':
                    $privileges = array_merge($privileges, $iPortal);
                    break;
                case 'priv_manage_fields':
                    $privileges = array_merge($privileges, $Manage_Fields);
                    break;
                case 'priv_iOLink':
                    $privileges = array_merge($privileges, $iasc_link);
                    break;
                case 'priv_iols':
                    $privileges = array_merge($privileges, $IOLs);
                    break;
                case 'priv_Optical':
                    $privileges = array_merge($privileges, $Admin_Optical);
                    $privileges = array_merge($privileges, $Admin_iOptical);
                    break;
                case 'priv_document':
                    $privileges = array_merge($privileges, $documents);
                    break;
                case 'priv_admin_scheduler':
                    $privileges = array_merge($privileges, $Admin_Scheduler);
                    break;
                case 'priv_immunization':
                case 'priv_vs':
                case 'priv_erx_preferences':
                case 'priv_admin_scp':
                    $privileges['priv_admin_clinical'] = 1;
                    break;
                case 'priv_set_margin':
                    $privileges['priv_document'] = 1;
                    break;
                case 'priv_provider_management':
                case 'priv_group':
                case 'priv_facility':
                case 'priv_ref_physician':
                case 'priv_admin':
                    $privileges['priv_admin'] = 1;
                    break;
                case 'priv_room_assign':
                    $privileges['priv_iMedicMonitor'] = 1;
                    $privileges['priv_admn_imm_iMedic_Monitor'] = 1;
                    break;
                //R8 new privileges
                case 'priv_api_access':
                    $privileges['priv_api_access'] = 1;
                    break;
                case 'priv_pis':
                    $privileges['priv_pis'] = 1;
                    break;
                case 'priv_Security':
                    $privileges['priv_Security'] = 1;
                    break;
            }
        }
    }
    
    $rq = "UPDATE users SET access_pri = '".serialize($privileges)."' WHERE id = '".$row['id']."'";
    $rq_obj = imw_query($rq);
    $counter++;
    //echo "Update done for user ".$row['id']." <br />";
}

echo "Update done for $counter users <br />";



?>