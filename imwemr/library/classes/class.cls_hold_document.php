<?php

include_once(dirname(__FILE__)."/class.cls_notifications.php");

class CLSHoldDocument{
	public $section_col;
	public $section_col_value;
	public $hold_sign_id;
	private $hold_to;
	private $operator_id;
	private $now;
	public $patient_id;
	function __construct()
	{
		$this->hold_to = isset($_REQUEST['hidd_hold_to_physician']) ? intval($_REQUEST['hidd_hold_to_physician']) : 0;
		$this->operator_id = $_SESSION['authId'];
		$this->now = date('Y-m-d H:i:s');
		$this->patient_id = $_SESSION['patient'];		
	}
	
	function save_hold_sign(){/*  TO HOLD THE DOCUMENT  */
		
		$CLS_notify_iconbar = new core_notifications();
		$return = false;
		if($this->hold_to != 0){
			$query = "INSERT INTO consent_hold_sign SET 
					  ".$this->section_col." = ".$this->section_col_value.", 
					  physician_id = ".$this->hold_to.", 
					  patient_id = ".$this->patient_id.", 
					  created_on = '".$this->now."', 
				 	  created_by = ".$this->operator_id.", 
					  signed = '0', 
					  signed_on = ''";
			$result = imw_query($query);
			if($result){
				$return = imw_insert_id();
				$this->track_hold($return);
				$notify_section = '';
				if($this->section_col=='consult_id'){$notify_section='consult';}
				else if($this->section_col=='opnote_id'){$notify_section='opnote';}
				if($notify_section!=''){
					$CLS_notify_iconbar->set_notification_status($notify_section);/*updating testeye icon status*/
					$CLS_notify_iconbar->set_switched_hold($this->hold_to,$notify_section);
				}
			}else{
				$return = false;
			}
		}
		return $return;
	}
	
	function track_hold($holded_id){
		$return = false;
		$query = "INSERT INTO consent_hold_track SET 
			  consent_hold_id = ".$holded_id.", 
			  hold_by = ".$this->operator_id.", 
			  hold_to = ".$this->hold_to.", 
			  date_time = '".$this->now."'";
		$result = imw_query($query);
		if($result){
			$return = imw_insert_id();
		}else{
			$return = false;
		}
		return $return;
	}
	
	function finalize_holded_doc(){/*  TO finally un-hold THE DOCUMENT  */
		$query = "UPDATE consent_hold_sign SET signed = 1, signed_by='".$this->operator_id."', signed_on = '".$this->now."' WHERE id=".$this->hold_sign_id;
		$result = imw_query($query);
		if($result){
			$this->get_section_by_chsId($this->hold_sign_id);
			return true;
		}else{
			return false;
		}
	}
	
	function switch_holdto_physician(){/*  TO finally un-hold THE DOCUMENT  */
		$query = "UPDATE consent_hold_sign SET physician_id = ".$this->hold_to." WHERE id=".$this->hold_sign_id;
		$result = imw_query($query);
		if($result){
			$this->get_section_by_chsId($this->hold_sign_id);
			$this->get_section_by_chsId($this->hold_sign_id,$this->hold_to);
			$this->track_hold($this->hold_sign_id);
			return true;
		}else{
			return false;
		}
	}
	
	function get_section_by_chsId($id,$hold_to=''){
		$query = "SELECT if(consult_id=0,'opnote','consult')as section FROM consent_hold_sign WHERE consent_id =0 AND sx_consent_id=0 
				  WHERE id='$id'";
		$res = imw_query($query);
		if($res && imw_num_rows($res)==1){
			$CLS_notify_iconbar = new core_notifications();
			$rs = imw_fetch_assoc($res);
			$section = trim($rs['section']);
			if($hold_to==''){
				$CLS_notify_iconbar->set_notification_status($section);	
			}else{
				$CLS_notify_iconbar->set_switched_hold($hold_to,$section);
			}
		}
	}
}//end of class.
?>