<?php
$ignoreAuth = true;
include("../../../../config/globals.php");

$status = imw_query("CREATE TABLE users_bkup_before_report_tab_27nov2018 AS SELECT * FROM users");

if(!function_exists('fetchByReportType')){
    function fetchByReportType() {
        $reports=array();
        $query = imw_query("SELECT * FROM `custom_reports` WHERE `delete_status` = 0 and `default_report`=1 ");	 
        if(imw_num_rows($query) > 0){
            while($row = imw_fetch_assoc($query)){
                $report_id = $row['id'];
                $report_name = $row['template_name'];
                $report_name = str_replace(array(" ", "/","-"), "_", $report_name);
                $report_name=strtolower($report_name);
                $reports['priv_report_'.$report_name.$report_id] = $row['template_name'];
            }
        }
        return $reports;
    }
}
//Reports Tabs Privileges Starts
$report=array();

$report = fetchByReportType();

$report['priv_report_Patient_Monitor'] = 'Patient Monitor';
$report['priv_report_Day_Face_Sheet'] = 'Day Face Sheet';
$report['priv_report_Appointment_Report'] = 'Appointment Report';
$report['priv_report_Appointment_information'] = 'Appointment information';
$report['priv_report_Patient_Document'] = 'Patient Document';
$report['priv_report_Sx_Planning_Sheet'] = 'Sx Planning Sheet';
$report['priv_sc_recall_fulfillment'] = 'Recall Fulfillment';
$report['priv_report_Consult_Letters'] = 'Consult Letters';
$report['priv_report_Scheduler_Report'] = 'Scheduler Report';
$report['priv_report_Patients_CSV_Export'] = 'Patients CSV Export';
$report['priv_report_Surgery_Appointments'] = 'Surgery Appointments';
$report['priv_report_RTA_Query'] = 'RTA Query';
$report['priv_report_Clinical_Productivity'] = 'Clinical Productivity';
$report['priv_report_Providers_Report'] = 'Providers Report';
$report['priv_report_Procedures_Report'] = 'Procedures Report';

//$analytic_report = $this->fetchByReportType('practice_analytic');

//$report = $this->fetchByReportType('financial');
$report['priv_prev_hcfa'] = 'Previous HCFA';
$report['priv_report_eid_status'] = 'EID Status';
$report['priv_report_EID_Payments'] = 'EID Payments';
$report['priv_tfl_proof'] = 'TFL Proof';
//Statements
$report['priv_new_statements'] = 'New Statement';
$report['priv_prev_statements'] = 'Previous Statement';
$report['priv_statements_pay'] = 'Statement Payments';
//Scheduled Reports
$report['priv_saved_scheduled'] = 'Saved Schedules';
$report['priv_executed_report'] = 'Executed Reports';

//$report = $this->fetchByReportType('compliance');
$report['priv_report_QRDA'] = 'QRDA';
$report['priv_report_CQM_Import'] = 'CQM Import';

$report['priv_ccd_export'] = 'CCD Export';

$report['priv_report_Access_Log'] = 'Access Log';
$report['priv_report_Call_Log'] = 'Call Log';

$report['priv_report_KY_State_Report'] = 'KY State Report';
$report['priv_report_TN_State_Report'] = 'TN State Report';
$report['priv_report_NC_State_Report'] = 'NC State Report';
$report['priv_report_IL_State_Report'] = 'IL State Report';

$report['priv_cn_reports'] = 'Contact Lens Report';
$report['priv_contact_lens'] = 'Contact Lens Orders';
$report['priv_glasses'] = 'Glasses';

$report['priv_dat_appts'] = 'Day Appts';
$report['priv_recalls'] = 'Recalls';
$report['priv_reminder_lists'] = 'Reminder Lists';

$report['priv_report_Clinical_Report'] = 'Clinical Report';
$report['priv_report_Auto_Finalize_Charts_Report'] = 'Auto Finalize Charts Report';

$report['priv_report_A_R_Aging_Rules'] = 'A/R Aging Rules';

$report['priv_report_Survey'] = 'Survey';

$report['priv_sc_scheduler'] = 'Scheduler';
$report['priv_report_practice_analytics'] = 'Practice Analytics';
$report['priv_report_financials'] = 'Financials';
$report['priv_report_compliance'] = 'Compliance';
$report['priv_cl_ccd'] = 'CCD';
$report['priv_report_api_access'] = 'API';
$report['priv_report_State'] = 'State';
$report['priv_report_optical'] = 'Optical';
$report['priv_sc_house_calls'] = 'Reminders';
$report['priv_cl_clinical'] = 'Clinical';
$report['priv_report_Rules'] = 'Rules';
$report['priv_report_iPortal'] = 'iPortal';
$report['priv_Reports_manager'] = 'Manager';
//Reports Tabs Privileges Ends


$arr_users=array();
$counter=0;
$query_rs=imw_query("Select id,access_pri from users where delete_status=0 ") or $msg_info[] = imw_error();

while($row=imw_fetch_assoc($query_rs)){
	$user_id=$row['id'];
	$user_priv=($row['access_pri']);
	$userpriviliges=unserialize(html_entity_decode($user_priv));
    $arr_report_priv=array('priv_sc_scheduler','priv_report_schduled','priv_billing_fun','priv_sc_daily','priv_bi_analytics','priv_acct_receivable','priv_report_audit','priv_pt_instruction','priv_report_mur','priv_cl_clinical','priv_cl_visits','priv_cl_ccd','priv_sc_house_calls','priv_sc_recall_fulfillment','priv_cl_order_set','priv_cdc','priv_report_tests','priv_bi_end_of_day','priv_bi_front_desk','priv_bi_ledger','priv_bi_prod_payroll','priv_bi_ar','priv_bi_statements','priv_report_payments','priv_report_copay_rocan','priv_un_superbills','priv_un_encounters','priv_un_payments','priv_report_adjustment','priv_report_refund','priv_daily_balance','priv_fd_collection','priv_report_practice_analytics','priv_cpt_analysis','priv_report_yearly','priv_bi_ledger','priv_report_revenue','priv_provider_mon','priv_ref_phy_monthly','priv_facility_monthly','priv_report_ref_phy','priv_credit_analysis','priv_report_patient','priv_report_ins_cases','priv_report_eid_status','priv_allowable_verify','priv_vip_deferred','priv_provider_rvu','priv_sx_payment','priv_net_gross','priv_ar_reports','priv_days_ar','priv_receivables','priv_unworked_ar','priv_unbilled_claims','priv_top_rej_reason','priv_new_statements','priv_prev_statements','priv_prev_hcfa','priv_statements_pay','priv_pt_statements','priv_pt_collections','priv_assessment','priv_collection_report','priv_tfl_proof','priv_report_rta','priv_billing_verification','priv_patient_status','priv_saved_scheduled','priv_executed_report','priv_cn_pending','priv_contact_lens','priv_cn_pending','priv_cn_ordered','priv_cn_received','priv_cn_dispensed','priv_cn_reports','priv_glasses','priv_gl_pending','priv_gl_ordered','priv_gl_received','priv_gl_dispensed','priv_gl_report','priv_documents','priv_alerts','priv_stage_i','priv_stage_ii','priv_stage_iii','priv_ccd_export','priv_ccd_import','priv_lab_import','priv_ccr_import','priv_dat_appts','priv_recalls','priv_reminder_lists','priv_no_shows');
    $report_tab_priv=false;
    foreach($arr_report_priv as $admn_tab) {
        if($userpriviliges[$admn_tab]==1){
            $report_tab_priv=true;
        }
    }

    if($report_tab_priv==true){
        $arr_users[$user_id]=$userpriviliges;
    }
}
if(empty($arr_users)==false) {
    foreach($arr_users as $user_id => $user_privliges){
        foreach($report as $key=> $value){
            $user_privliges[$key]=1;
        }
        if($user_privliges){
            $user_grant_priviliges=htmlentities(serialize($user_privliges));
            $qryUpdate="UPDATE users set access_pri='".$user_grant_priviliges."' where id='".$user_id."' ";
            $resUpdate=imw_query($qryUpdate);
            if($resUpdate){
                $counter++;
            }
        }
    }
}

$msg_info[] ="<br><b>Total Record Updated: ".$counter."</b>";
$msg_info[] = "<br><b>Release :<br> Update Success.</b>";

$color = "green";	

?>
<html>
<head>
    <title>Update 83</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br>
<br>
    <font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
        <?php echo(implode("<br>",$msg_info));?>
    </font>
</body>
</html>