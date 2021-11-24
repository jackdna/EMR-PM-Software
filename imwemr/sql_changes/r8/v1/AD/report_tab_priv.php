<?php
$ignoreAuth = true;
include("../../../../config/globals.php");

function fetchByReportType() {
    $report=array();
    $query = imw_query("SELECT * FROM `custom_reports` WHERE `delete_status` = 0 and `default_report`=1 ");	 
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
	$arr_users[$user_id]=$userpriviliges;
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
    <title>Report Tab Privileges</title>
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