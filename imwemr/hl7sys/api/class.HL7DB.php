<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
?>
<?php
/*	
File: CLS_makeHL7.php
Purpose: Class for HL7
Access Type: Include 
*/
require_once(dirname(__FILE__)."/../../config/globals.php");
//error_reporting(-1);
//ini_set("display_errors",-1);
class HL7DB{
	public $authId, $msgType, $msgSubType, $application_module, $msgFor, $ZMSgivenMR, $patient_id, $source_id, $interface_id;
	function __construct(){ //constructor
		$this->authId 					= $_SESSION['authId'];
		$this->application_module		= '';
		$this->msgType					= false;
		$this->msgSubType				= '';
		$this->msgFor					= '';
		$this->patient_id				= 0;
		$this->source_id				= 0;
		$this->interface_id				= 0;
		$this->ZMSgivenMR				= false;
	}

	function get_active_interfaces(){
		$q = "SELECT id as interface_id, interface_with, interface_mode, HL7_version FROM hl7_interface_master WHERE status = '1'";
		$res = imw_query($q);
		if($res){
			$r = array();
			while($rs = imw_fetch_assoc($res)){
				$interface_id = $rs['interface_id'];
				unset($rs['interface_id']);
				$r[$interface_id] = $rs;
			}
			return $r;
		}
		return false;		
	}
	
	function get_enabled_outbound_message_types($app_module){
		$interfaces = $this->get_active_interfaces();
		if($interfaces){
			$interface_arr = array();
			foreach($interfaces as $interface_id=>$interface_rs){
				$q ="SELECT msg_type FROM hl7_interface_connection WHERE status='1' AND application_module='".$app_module."' AND in_out='OUT' AND interface_id = '".$interface_id."'";
				$res = imw_query($q);
				if($res){
					$r = array();
					while($rs = imw_fetch_assoc($res)){
						$msg_type = explode(',',$rs['msg_type']);
						foreach($msg_type as $msg){
							$interface_rs[$msg]=1;
						}
					}
				}
				$interface_arr[$interface_id] = $interface_rs;
			}
			return $interface_arr;
		}
		return false;
	}
	
	function get_segments_message_type_wise($interface_id, $msg_type){
		$q ="SELECT msg_segments,trigger_events FROM hl7_interface_message_custom WHERE interface_id = $interface_id AND msg_type='".$msg_type."' LIMIT 1";
		$res =  imw_query($q);
		if($res && imw_num_rows($res)==1) {
			$rs = imw_fetch_assoc($res);
			return $rs;
		}
		else{
			$q ="SELECT msg_segments,trigger_events FROM hl7_interface_message_custom WHERE msg_type='".$msg_type."' LIMIT 1";
			$res =  imw_query($q);
			if($res && imw_num_rows($res)==1){
				$rs = imw_fetch_assoc($res);
				return $rs;
			}
		}
		return false;
	}
	
	function get_master_segment_settings($segment){
		$q ="SELECT isc.id, isc.sequence, isc.val, isc.val_type FROM hl7_interface_segment_master isc ";
		$q.="WHERE isc.segment='".$segment."' ORDER BY isc.sequence";
		$res = imw_query($q);
		if($res && imw_num_rows($res)>0){
			$r = array();
			while($rs = imw_fetch_assoc($res)){
				$seq= $rs['sequence'];
				unset($rs['sequence']);
				$r[$seq][] = $rs;
			}
			return $r;
		}
		return false;
	}
	
	function get_profiled_segment_settings($segment,$interface_id){
		$q ="SELECT isc.id, isc.sequence, isc.val, isc.val_type FROM hl7_interface_segment_custom isc ";
		$q.="WHERE isc.interface_id='".$interface_id."' AND isc.segment='".$segment."' ORDER BY isc.sequence";
		$res = imw_query($q);
		if($res && imw_num_rows($res)>0){
			$r = array();
			while($rs = imw_fetch_assoc($res)){
				$seq= $rs['sequence'];
				unset($rs['sequence']);
				$r[$seq][] = $rs;
			}
			return $r;
		}
		return false;
	}
	
	function getQueryResult($q,$seg,$msgType,$source_id){
		$ins_case_id_str = '0000';
		if($msgType=='ADT'){
			$this->patient_id = $source_id;
			$ins_case_id 	= $this->get_ins_case_ids_by_patient();
			if($ins_case_id && count($ins_case_id)>0){
				$ins_case_id_str = implode(',',$ins_case_id);
			}
		}else if($msgType=='SIU'){
			$sch_rs = $this->get_appointment_details($source_id);
			$this->patient_id 	= $sch_rs['sa_patient_id'];
			$ins_case_id[] 	= $sch_rs['case_type_id'];
		}else if($this->msgType=='ZMS' && $this->ZMSgivenMR && !empty($this->ZMSgivenMR) && substr($this->ZMSgivenMR,0,2)=='CL'){
			$clws_rs = $this->get_clws_details($source_id);
			$this->patient_id 	= $clws_rs['patient_id'];
		}else if($msgType=='ZMS'){
			$chart_rs = $this->get_form_id_details($source_id);
			$this->patient_id 	= $chart_rs['patient_id'];
		}
		if(!$this->patient_id) {write_my_failures("Patient ID not available to proceed with HL7 message."); return false;}
	
		//Switching segment name conditional.		
		if($this->msgType=='ZMS' && $this->ZMSgivenMR && !empty($this->ZMSgivenMR) && substr($this->ZMSgivenMR,0,2)=='CL' && $seg=='Z01'){$seg = 'Z01CL';}
		
		$q1 = "";
		switch($seg){
			case 'PID':{
				$q1 = "SELECT ".$q." FROM patient_data WHERE patient_data.id='".$this->patient_id."' LIMIT 0,1";
				break;
			}
			case 'PV1':{
				$q1 = "SELECT ".$q." FROM patient_data WHERE patient_data.id='".$this->patient_id."' LIMIT 0,1";
				break;
			}
			case 'GT1':{
				$q1 = "SELECT ".$q." FROM resp_party WHERE resp_party.patient_id='".$this->patient_id."' LIMIT 0,1";
				break;
			}
			case 'IN1':{
				$q1 = "SELECT ".$q." FROM insurance_data ";
				$q1.= "JOIN insurance_companies ON (insurance_companies.id=insurance_data.provider) ";
				$q1.= "LEFT JOIN insurance_case ON (insurance_case.ins_caseid = insurance_data.ins_caseid) ";
				$q1.= "LEFT JOIN insurance_case_types ON (insurance_case_types.case_id = insurance_case.ins_case_type) ";
				$q1.= "WHERE insurance_data.pid='".$this->patient_id."' AND insurance_data.actInsComp='1' AND insurance_data.del_status='0' ";
				$q1.= "AND insurance_data.ins_caseid IN ($ins_case_id_str) ORDER BY insurance_data.ins_caseid, insurance_data.type";
				break;
			}
			case 'SCH':{
				$q1 = "SELECT ".$q." FROM schedule_appointments ";
				$q1.= "JOIN slot_procedures ON (schedule_appointments.procedureid=slot_procedures.id) ";
				$q1.= "WHERE schedule_appointments.sa_patient_id='".$this->patient_id."' AND schedule_appointments.id='".$this->source_id."'";
				break;
			}
			case 'AIP':{
				$q1 = "SELECT ".$q." FROM users ";
				$q1.= "JOIN schedule_appointments ON (schedule_appointments.sa_doctor_id=users.id) ";
				$q1.= "WHERE schedule_appointments.sa_patient_id='".$this->patient_id."' AND schedule_appointments.id='".$this->source_id."'";
				break;
			}
			case 'AIL':{
				$q1 = "SELECT ".$q." FROM facility ";
				$q1.= "JOIN schedule_appointments ON (schedule_appointments.sa_facility_id=facility.id) ";
				$q1.= "WHERE schedule_appointments.sa_patient_id='".$this->patient_id."' AND schedule_appointments.id='".$this->source_id."'";
				break;
			}
			case 'Z01':{
				$q1 = "SELECT ".$q." FROM  chart_vis_master ";
				$q1.= "LEFT JOIN chart_pc_mr ON chart_pc_mr.id_chart_vis_master = chart_vis_master.id ";
				$q1.= "LEFT JOIN users ON (users.id = chart_pc_mr.provider_id) ";
				$q1.= "WHERE chart_vis_master.form_id='".$this->source_id."' AND chart_pc_mr.ex_type='MR' AND chart_pc_mr.delete_by='0' ";
				$q1.= "AND chart_pc_mr.mr_none_given!='' AND chart_pc_mr.mr_none_given='".$this->ZMSgivenMR."' ";
				$q1.= "Order By chart_pc_mr.ex_number LIMIT 1";
				break;
			}
			case 'Z01CL':{
				$q1 = "SELECT ".$q." FROM contactlensmaster ";
				$q1.= "LEFT JOIN clprintorder_master ON (clprintorder_master.clws_id=contactlensmaster.clws_id) ";
				$q1.= "JOIN users ON (contactlensmaster.provider_id=users.id) ";
				$q1.= "WHERE contactlensmaster.clws_id='".$this->source_id."' AND contactlensmaster.del_status=0 ORDER BY clprintorder_master.print_order_id DESC LIMIT 0,1";
				break;
			}
			case 'Z02':{// For CL
				$q1 = "SELECT ".$q." FROM contactlensmaster ";
				$q1.= "JOIN contactlensworksheet_det ON (contactlensworksheet_det.clws_id=contactlensmaster.clws_id) ";
				$q1.= "JOIN contactlensemake ON (contactlensemake.make_id=CONCAT(IF(contactlensworksheet_det.SclTypeOD_ID>0,contactlensworksheet_det.SclTypeOD_ID,''),IF(contactlensworksheet_det.RgpTypeOD_ID>0,contactlensworksheet_det.RgpTypeOD_ID,''),IF(contactlensworksheet_det.RgpCustomTypeOD_ID>0,contactlensworksheet_det.RgpCustomTypeOD_ID,''),IF(contactlensworksheet_det.SclTypeOS_ID>0,contactlensworksheet_det.SclTypeOS_ID,''),IF(contactlensworksheet_det.RgpTypeOS_ID>0,contactlensworksheet_det.RgpTypeOS_ID,''),IF(contactlensworksheet_det.RgpCustomTypeOS_ID>0,contactlensworksheet_det.RgpCustomTypeOS_ID,''))) ";
				$q1.= "WHERE contactlensmaster.clws_id='".$this->source_id."' AND contactlensmaster.del_status=0 ORDER BY contactlensworksheet_det.clEye ASC";
				break;
			}
			case 'Z03':{// For MR
				$q1 = "SELECT ".$q." FROM  chart_vis_master ";
				$q1.= "JOIN chart_pc_mr ON (chart_pc_mr.id_chart_vis_master = chart_vis_master.id) ";
				$q1.= "JOIN chart_pc_mr_values ON (chart_pc_mr.id =chart_pc_mr_values.chart_pc_mr_id) ";
				$q1.= "WHERE chart_vis_master.form_id='".$this->source_id."' AND chart_pc_mr.ex_type='MR' AND chart_pc_mr.delete_by='0' ";
				$q1.= "AND chart_pc_mr.mr_none_given!='' AND chart_pc_mr.mr_none_given='".$this->ZMSgivenMR."' ";
				$q1.= "Order By chart_pc_mr_values.site";
				break;
			}
		}
		if(empty($q1)) return false;
		$res = imw_query($q1);
		if($res){
			$r = array();
			while($rs = imw_fetch_assoc($res)){
				$r[] = $rs;
			}
			return $r;
		}
		return false;
	}
	
	function get_appointment_details($sch_id){
		$q = "SELECT sa_patient_id,case_type_id FROM schedule_appointments WHERE id='".$sch_id."' LIMIT 1";
		$r = imw_query($q);
		if($r){
			$rs = imw_fetch_assoc($r);
			return $rs;
		}
		return false;
	}
	
	function get_form_id_details($form_id){
		$q = "SELECT patient_id,date_of_service FROM chart_master_table WHERE id='".$form_id."' LIMIT 1";
		$r = imw_query($q);
		if($r){
			$rs = imw_fetch_assoc($r);
			return $rs;
		}
		return false;
	}
	
	function get_clws_details($clws_id){
		$q = "SELECT patient_id,provider_id,dos,clws_type FROM contactlensmaster WHERE clws_id='".$clws_id."' LIMIT 1";
		$r = imw_query($q);
		if($r){
			$rs = imw_fetch_assoc($r);
			return $rs;
		}
		return false;
	}
	
	function get_ins_case_ids_by_patient(){
		$q="SELECT insc.ins_caseid FROM insurance_case insc 
			JOIN insurance_case_types insct ON (insc.ins_case_type=insct.case_id) 
			JOIN insurance_data insd ON (insd.ins_caseid=insc.ins_caseid AND insd.provider >0 AND insd.actInsComp='1' AND insd.del_status='0') 
			JOIN insurance_companies inscomp ON (inscomp.id=insd.provider) 
			WHERE insc.patient_id='".$this->patient_id."' AND insc.case_status ='Open' AND insc.del_status='0' 
		 	GROUP BY insc.ins_caseid 
			ORDER BY insct.vision desc,insct.default_selected desc LIMIT 1";
		$r = imw_query($q);
		$caseIDs = false;
		if($r && imw_num_rows($r)>0){
			$caseIDs = array();
			$rs = imw_fetch_assoc($r);
			$caseIDs[]=$rs['ins_caseid'];
		}
		return $caseIDs;
	}
	
	/*FUNCTION: newMessageUniqueId()*/
	/*PURPOSE: To get next message ID (unique message ID)*/	
	function newMessageUniqueId(){
		$res1 = imw_query("SELECT if(MAX(id) IS NULL,0,MAX(id))+1  as NewMsgId FROM hl7_interface_messages_out");
		if($res1 && imw_num_rows($res1)==1){
			$rs1 = imw_fetch_assoc($res1);
			$NewMsgId = $rs1['NewMsgId'];
			$set_number = $NewMsgId * 1000;
			$set_number = substr($set_number,0,7);
			$set_number = $set_number + $NewMsgId;
			return $set_number;
		}
		return false;
	}
	
	function get_segment_value_format($seg,$seg_val){
		$q="SELECT format FROM hl7_interface_segment_custom WHERE interface_id='".$this->interface_id."' AND segment='$seg' AND val='\'{".$seg_val."}\''";
		$res = imw_query($q);
		if($res && imw_num_rows($res)==0){
			$q="SELECT format FROM hl7_interface_segment_master WHERE segment='$seg' AND val='\'{".$seg_val."}\''";
			$res = imw_query($q);
			if($res && imw_num_rows($res)==1){
				$rs = imw_fetch_assoc($res);
				return $rs['format'];
			}			
		}else if($res && imw_num_rows($res)==1){
			$rs = imw_fetch_assoc($res);
			return $rs['format'];
		}
		return '';
	}
	
	function save_new_hl7_message($msgData){
		$q = "INSERT INTO hl7_interface_messages_out SET ";
		$q.= "interface_id='".$this->interface_id."', ";
		$q.= "patient_id='".$this->patient_id."', ";
		$q.= "msg='".addslashes($msgData)."', ";
		$q.= "msg_type='".$this->msgType."', ";
		$q.= "saved_on='".date('Y-m-d H:i:s')."', ";
		$q.= "operator='".$_SESSION['authId']."', ";
		$q.= "source_id='".$this->source_id."', ";
		$q.= "source_name='".$this->application_module."', ";
		$q.= "msg_for='".$this->msgFor."'";
		$res = imw_query($q);
		if($res){
			return true;
		}
		return false;
	}
	
	function check_new_old_msg_for_same_sourceid($source_id,$msg_type){
		$res = imw_query("SELECT id,sent FROM hl7_interface_messages_out WHERE msg_type='".$msg_type."' AND source_id='".$source_id."' AND source_name='".$this->application_module."'");
		if($res && imw_num_rows($res)>0){
			return true;
		}
		return false;
	}
	
	
	/*********FUNCTION BELOW USED IN SENDER SCRIPT***********/
	
	function get_connection_details($connnection_id=''){
		if(empty($connnection_id)) return false;
		$q ="SELECT * FROM hl7_interface_connection WHERE status='1' AND id = '".$connnection_id."' LIMIT 1";
		$res = imw_query($q);
		if($res && imw_num_rows($res)==1){
			$interface_arr = array();
			return imw_fetch_assoc($res);
		}
		return false;
	}
	
	function make_log_of_action($tbl,$pkid,$action){
		/***THIS PROGRAM WILL NOT REPEATITIVE SAME TYPE OF ACTION LOGS. WILL LOG INITIAL AND LAST LOGS ONLY SENT SAME TEXT****/
		$q = "INSERT INTO hl7_interface_log SET ";
		$where = "";
		$res_find = imw_query("SELECT id,action FROM hl7_interface_log ORDER BY id DESC LIMIT 0,2");
		if($res_find && imw_num_rows($res_find)>=2){
			$rs_find 			= imw_fetch_assoc($res_find);
			$last_id 			= $rs_find['id'];
			$last_action 		= $rs_find['action'];
			$rs_find 			= imw_fetch_assoc($res_find);
			$second_last_action = $rs_find['action'];
			if($last_action==$second_last_action && $last_action==$action){
				$q = "UPDATE hl7_interface_log SET ";
				$where = " WHERE id='".$last_id."'";
			}
		}
		$q.= "table_name='".$tbl."', ";
		$q.= "table_pkid='".$pkid."', ";
		$q.= "dt='".date('Y-m-d H:i:s')."', ";
		$q.= "op='".$_SESSION['authId']."', ";
		$q.= "action='".addslashes($action)."'";
		$q.= $where;
		$res = imw_query($q);
	}
	
	function get_pending_hl7_outbound($interface_id,$msg_type=NULL)
	{
	//	$msg_type = $this->get_msg_type_for_module($am);
		if(!$msg_type) return;
		$q = "SELECT id,msg,msg_type,patient_id FROM hl7_interface_messages_out WHERE interface_id = '".$interface_id."' AND sent='0' AND msg_type IN (".$msg_type.")";
		$res = imw_query($q);
		if($res){
			$r = array();
			while($rs = imw_fetch_assoc($res)){
				$r[] = $rs;
			}
			return $r;
		}
		return false;
	}
	
	function check_other_msgtypes_on_same_destination ( $connectionID,$MessageType )
	{
		$q = "SELECT GROUP_CONCAT(msg_type) AS msg_types FROM hl7_interface_connection WHERE ";
		$q.= "send_with_connection = '".$connectionID."' AND send_with_connection != '0' AND status='1' AND in_out='OUT' AND id != '".$connectionID."' ";
		$q.= "GROUP BY (send_with_connection)";
		$res = imw_query( $q );
		if( $res && imw_num_rows( $res )>0 ){
			$rs = imw_fetch_assoc( $res );
			$newMsgType = explode( ',',$rs['msg_types'] );
			array_push( $newMsgType,$MessageType );
			$newMsgType =  array_unique($newMsgType);
			return "'".implode("','",$newMsgType )."'";
		}
		return false;
	}
	
	
}?>