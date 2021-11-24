<?php
/*
	The MIT License (MIT)
	Distribute, Modify and Contribute under MIT License
	Use this software under MIT License
*/
require_once($GLOBALS['srcdir'].'/classes/class.language.php');
require_once($GLOBALS['srcdir'].'/classes/cls_common_function.php');
$cls_common = new CLSCommonFunction();

class AutoRespond{
	public $auth_id = '';
	
	public $id = false;
	public $type = false;
	public $nid = false;
	public $type_arr = array('Appointment' => 1, 'Practice Message' => 2, 'Email - Account Registration' => 4, 'Email - Account Verification' => 5);
		
		
	public function __construct($auth_id){
		$this->auth_id = $auth_id;
	}
	
	//Returns Template regarding data
	public function get_template_data(){
		$return_arr = $appointment = $practice_msg = $direct_msg = $email_reg = $email_verif = array();
		$sql = "SELECT * FROM `iportal_autoresponder_templates` WHERE `del_status`='0'";
		$data = imw_query($sql);
		if($data && imw_num_rows($data)>0){
			while($row = imw_fetch_assoc($data)){
				$rowData = array();
				$rowData['name'] = $row['name'];
				$rowData['type'] = $row['type'];
				$rowData['forwarder'] = $row['forwarder'];
				$rowData['data'] = $row['data'];
				$rowData['status'] = $row['status'];
				
				switch($row['type']){
					case '1':
						$appointment[$row['id']] = $rowData;
					break;
					case '2':
						$practice_msg[$row['id']] = $rowData;
					break;
					case '3':
						$direct_msg[$row['id']] = $rowData;
					break;
					case '4':
						$email_reg[$row['id']] = $rowData;
					break;
					case '5':
						$email_verif[$row['id']] = $rowData;
					break;
				}
			}
		}
		
		$return_arr['Appointment'] = $appointment;
		$return_arr['Practice Message'] = $practice_msg;
		$return_arr['Email - Account Registration'] = $email_reg;
		$return_arr['Email - Account Verification'] = $email_verif;
		return $return_arr;
	}
	
	
	//Returns array of variable available for this section
	public function get_variable_tags(){
		$return_arr = array();
		//Patient Variables
		$return_arr['Patient']['ID']="{PATIENT ID}";
		$return_arr['Patient']['Name Title']="{PATIENT NAME TITLE}";
		$return_arr['Patient']['First Name']="{PATIENT FIRST NAME}";
		$return_arr['Patient']['Middle Name']="{PATIENT MIDDLE NAME}";
		$return_arr['Patient']['Last Name']="{PATIENT LAST NAME}";
		$return_arr['Patient']['Temp Key']="{TEMP KEY}";
		$return_arr['Patient']['Registration Token']="{REG TOKEN}";
		
		//Physician Variables
		$return_arr['Physician']['Name Title ']="{PHYSICIAN NAME TITLE}";
		$return_arr['Physician']['First Name']="{PHYSICIAN FIRST NAME}";
		$return_arr['Physician']['Middle Name']="{PHYSICIAN MIDDLE NAME}";
		$return_arr['Physician']['Last Name']="{PHYSICIAN LAST NAME}";
		$return_arr['Physician']['Name Suffix']="{PHYSICIAN NAME SUFFIX}";
		$return_arr['Physician']['Name']="{PHYSICIAN NAME}";
		
		//Facility Variables
		$return_arr['Facility']['Name']="{FACILITY NAME}";
		$return_arr['Facility']['Mailing Address']="{FACILITY MAILING ADDRESS}";
		$return_arr['Facility']['Zip Code']="{FACILITY ZIP CODE}";
		$return_arr['Facility']['City']="{FACILITY CITY}";
		$return_arr['Facility']['State']="{FACILITY STATE}";
		
		return $return_arr;
	}
	
	//Returns filtered name for creating toggles
	public function get_toggle_nm($name){
		 //Lower case everything
		$string = strtolower($name);
		//Make alphanumeric (removes all other characters)
		$string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
		//Clean up multiple dashes or whitespaces
		$string = preg_replace("/[\s-]+/", " ", $string);
		//Convert whitespaces and underscore to dash
		$string = preg_replace("/[\s-]/", "_", $string);
		
		return $string;
	}
	
	//Perform task based actions and returns alerts acc. to it
	public function manipulate_data($request){
		$save_status = array();
		
		//Saving, Updating, Deleting Data
		$task = (isset($request['autoresp_action']) && empty($request['autoresp_action']) == false) ? $request['autoresp_action'] : '';
		if(isset($task) && empty($task) == false){
			$id = $request['autoresp_tempId'];
			$name = imw_real_escape_string($request['auroresp_temp_name']);
			$type = $request['autoresp_catg'];
			$forwarder = imw_real_escape_string($request['auroresp_forward_email']);
			$data = imw_real_escape_string($request['tempCont']);
			$status = isset($request['auroresp_temp_status']) ? $request['auroresp_temp_status'] : 0;
			
			$save_status['id'] = $id;
			$save_status['type'] = $type;
			$save_status['action'] = $task;
			
			switch($task){
				//Add new records	
				case 'addNew':
					if($name != "" && $id == ""){
						if($status){
							$sql = "UPDATE `iportal_autoresponder_templates` SET `status`='0' WHERE `type`='".$type."'";
							imw_query($sql);
						}
						$sql = "INSERT INTO `iportal_autoresponder_templates` SET `name`='".$name."', `type`='".$type."', `forwarder`='".$forwarder."', `data`='".$data."', `status`='".$status."'";
						$resp = imw_query($sql);
						if($resp){
							$nid = imw_insert_id(); /*Id of new Template*/
							$save_status['new_id'] = $nid;
							unset($save_status['id']);
						}
					}
				break;
				
				//Updating records
				case 'save':
					if($name != "" && $id != ""){
						if($status){
							$sql = "UPDATE `iportal_autoresponder_templates` SET `status`='0' WHERE `type`='".$type."'";
							imw_query($sql);
						}
						$sql = "UPDATE `iportal_autoresponder_templates` SET `name`='".$name."', `type`='".$type."', `forwarder`='".$forwarder."', `data`='".$data."', `status`='".$status."' WHERE `id`='".$id."'";
						$resp = imw_query($sql);
					}
				break;
				
				//Deleting records
				case 'delete':
					if($id != ""){
						$sql = "UPDATE `iportal_autoresponder_templates` SET `del_status`='".$_SESSION['authUserID']."' WHERE `id`='".$id."'";
						$resp = imw_query($sql);
					}
				break;	
			}
			
			$sv_alert_str = '';
			if($save_status['id'] && $save_status['type']){
				if($save_status['action']=="delete"){
					$sv_alert_str .= "top.alert_notification_show('Template deleted successfully');";
				}
				else{
					$sv_alert_str .= "top.alert_notification_show('Template saved successfully');";
				}
			}
			elseif(!$save_status['id'] && $save_status['type'] && $save_status['new_id']){
				$sv_alert_str .= "top.alert_notification_show('Template added successfully');";
			}
		}
		return $sv_alert_str;
	}
	
	//Returns array of variables needed in js file
	public function get_js_arr(){
		$js_php_arr = array();
		$template_data = $this->get_template_data();
		$js_php_arr['appt_data'] = $template_data['Appointment'];
		$js_php_arr['prac_msg'] = $template_data['Practice Message'];
		$js_php_arr['msg_regis'] = $template_data['Email - Account Registration'];
		$js_php_arr['msg_verify'] = $template_data['Email - Account Verification'];
		
		if(isset($_REQUEST['autoresp_tempId'])){
			$js_php_arr['sel_id'] = $_REQUEST['autoresp_tempId'];
		}
		
		if(isset($_REQUEST['autoresp_catg'])){
			$js_php_arr['sel_type'] = $_REQUEST['autoresp_catg'];
		}
		
		$return_arr = json_encode($js_php_arr);
		return $return_arr;
	}
}
?>