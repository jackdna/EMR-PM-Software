<?php
$without_pat = "yes";
require_once(dirname(__FILE__)."/reports_header.php");
include_once($GLOBALS['fileroot'] . '/library/classes/SaveFile.php');
require_once('../../library/classes/class.reports.php');
require_once('../../library/classes/cls_common_function.php');
require_once('common/report_logic_info.php');
	$temp_id = $_REQUEST['sch_temp_id'];
	if($temp_id){
		
		$sql_query = imw_query("SELECT * FROM `custom_reports` WHERE id='$temp_id' and `delete_status` = 0");
		if(imw_num_rows($sql_query) > 0){
			$row = imw_fetch_assoc($sql_query);
			$dbtemp_id  = $row['id'];
			$dbreport_type  = trim($row['report_sub_type']);
			$dbtemplate_name = trim($row['template_name']);
			switch($dbreport_type){
				case 'mur':
					include(dirname(__FILE__).'/mur/mur_report.php');
				break;
				case 'audit':
					include('audit_report.php');
				break;	
				case 'User Log':
					include('user_log.php');
				break;					
			}
		}
	}   
?>