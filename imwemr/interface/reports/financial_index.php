<?php
set_time_limit(900);
$without_pat = "yes";
require_once("reports_header.php");
include_once($GLOBALS['fileroot'] . '/library/classes/SaveFile.php');
require_once('../../library/classes/class.reports.php');
require_once('../../library/classes/cls_common_function.php');
//require_once('common/report_logic_info.php');
	$temp_id = $_REQUEST['sch_temp_id'];
	if($temp_id){
		$sql_query = imw_query("SELECT * FROM `custom_reports` WHERE id='$temp_id' and `delete_status` = 0");
		if(imw_num_rows($sql_query) > 0){
			$row = imw_fetch_assoc($sql_query);
			$dbtemp_id  = $row['id'];
			$dbtemp_name  = trim(ucfirst($row['template_name']));
			$dbreport_type  = trim($row['report_sub_type']);
			switch($dbreport_type){
				case 'daily':
				    switch($dbtemp_name){
						case 'Refund Report':
							include('refund_report.php');
						break;
						case 'Front Desk':
							include('front_desk.php');
						break;
						case 'FD Collection':
							include('fd_collection_index.php');
						break;
						default:
							include('day_report.php');
					}
				break;
				case 'analytics':
					switch($dbtemp_name){
						case 'Deferred/VIP':
							include('deferred_vip.php');
						break;
						case 'Ledger':
							include('ledger.php');
						break;
						case 'CPT Analysis':
							include('Procedural.php');
						break;
						case 'Referring Physician':
							include('report_referring_physician.php');
						break;
						case 'Allowable Verify':
							include('allowVerifyReport.php');
						break;
						case 'Credit Analysis':
							include('creditReport.php');
						break;
						case 'Deleted Payments':
							include('deleted_payments.php');
						break;
						case 'Patient Report':
							include('patient_report.php');
						break;
						case 'Modified Encounters':
							include('modified_encounters.php');
						break;
						case 'Yearly':
							include('yearly_report.php');
						break;
						case 'Provider RVU':
							include('rvu_report.php');
						break;
						case 'Transaction Details':
							include('transaction_details.php');
						break;
						case 'Procedure Payments':
							include('procedure_payments.php');
						break;						
						case 'Cash Lag Analysis':
							include('cash_lag_analyses.php');
						break;												
						case 'Office Production':
							include('office_production.php');
						break;							
						case 'Patient Status':
							include('patient_status.php');
						break;
						default:
							include('productivity.php');
					}
				break;
				case 'account_receivable':
					include('account_receivable_index.php');
				break;
				case 'claims':
                    switch($dbtemp_name){
						case 'Denial Records':
							include('denial_records.php');
						break;
						default:
							include('claims_index.php');
					}
				break;
				case 'pt_collections':
					include('pt_collections_index.php');
				break;
			}
		}
	}   
?>