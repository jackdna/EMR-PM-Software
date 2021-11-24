<?php
/*
 The MIT License (MIT)
 Distribute, Modify and Contribute under MIT License
 Use this software under MIT License
 
 Coded in PHP7
 Purpose: Medical History -> CC & History class
 Access Type: Indirect Access.
 
*/

include_once $GLOBALS['srcdir'].'/classes/CLSAlerts.php';
$cls_alerts = new CLSAlerts;

class CC_Hx extends MedicalHistory
{
	//Public variabels
	public $pat_id_audit_trail = array();
	
	public function __construct($tab = 'ocular')
	{
		parent::__construct($tab);
		
	}
	
	public function get_cc_data(){
		$sql = imw_query("Select cc_id,date_format(c1.date_of_service, '".get_sql_date_format()."') as DOS ,reason  as History, ccompliant as CC from chart_master_table c1 LEFT JOIN chart_left_cc_history c2 ON c1.id = c2.form_id where c1.patient_id  = '$this->patient_id' AND c1.purge_status='0' AND c1.delete_status='0' ORDER BY c1.date_of_service DESC");
		if(imw_num_rows($sql) > 0){
			while($row = imw_fetch_array($sql)){
				$return_arr[] = $row;
				$this->pat_id_audit_trail[] = $row['cc_id'];
			}
		}
		return $return_arr;
	}
	
	//Set CLS Alerts
	public function set_cls_alerts(){
		global $cls_alerts;
		$return_str= '';
		$alertToDisplayAt = "admin_specific_chart_note_med_hx";
		$return_str .= $cls_alerts->getAdminAlert($_SESSION['patient'],$alertToDisplayAt,$form_id,"350px");
		$alertToDisplayAt = "patient_specific_chart_note_med_hx";
		$return_str .= $cls_alerts->getPatSpecificAlert($_SESSION['patient'],$alertToDisplayAt,"350px");
		$return_str .= $cls_alerts->autoSetDivLeftMargin("140","265");
		$return_str .= $cls_alerts->autoSetDivTopMargin("250","30");
		$return_str .= $cls_alerts->writeJS();
		return $return_str;	
	}	
}
?>