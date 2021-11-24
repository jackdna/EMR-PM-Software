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
class HL7Variables{
	public function __call($name, $args){
        if(method_exists($this, $name)){
            return $this->{$name}($args);
        }else{
            write_my_failures("","Method ($name) does not exist.");
        }
    }

	private function get_segment_value_format($interface_id,$seg,$seg_val){
		$q="SELECT format FROM hl7_interface_segment_custom WHERE interface_id='$interface_id' AND segment='$seg' AND val='\'{".$seg_val."}\''";
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
	}
	
	private function mask_array_to_format($array,$format){
		foreach($array as $k=>$v){
			$format = preg_replace('/\b'.$k.'\b/', $v, $format);//using preg to replace complete word; // str_ireplace($k,$v,$format);
		}
		return $format;
	}
	
	private function get_sch_id_by_patient_id($pt_id){
		if($pt_id <= 0) return false;
		//GETTING most recent timed appointment which is checked-in.
		$q ="SELECT sa.id FROM schedule_appointments sa WHERE sa_patient_id='".$pt_id."' AND sa_patient_app_status_id='13' ";
		$q.="AND sa_app_start_date = '".date('Y-m-d')."' ORDER BY sa.sa_app_starttime DESC LIMIT 0,1";
		$res = imw_query($q);
		if($res && imw_num_rows($res)==0){
			//if previous query didn't returned result, getting general appointment ID of today for this patient.
			$q ="SELECT sa.id FROM schedule_appointments sa WHERE sa_patient_id='".$pt_id."' ";
			$q.="AND sa_patient_app_status_id NOT IN (203,201,18,19,20,3) AND sa_app_start_date = '".date('Y-m-d')."' ORDER BY sa.sa_app_starttime ASC LIMIT 0,1";
			$res = imw_query($q);
		}
		if($res && imw_num_rows($res)==1){
			$rs = imw_fetch_assoc($res);
			return $rs['id'];
		}
		return false;
	}
	
	private function get_dos_by_sch_id($sch_id){
		if($sch_id <= 0) return false;
		$q ="SELECT sa.sa_app_start_date AS dos FROM schedule_appointments sa WHERE sa.id='".$sch_id."' LIMIT 0,1";
		$res = imw_query($q);
		if($res && imw_num_rows($res)==1){
			$rs = imw_fetch_assoc($res);
			return $rs['dos'];
		}
		return false;
	}
	
/*************CUSTOM VARIABLE FUNCTIONS CREATE BELOW THIS LINE***************/
	private function filler_appt_status_code($args=array()){
		$status = trim($args[0]['message_subtype']);
		switch($status){
			case '0':	return 'BOOKED'; 		break;
			case '11':	return 'DEPARTED'; 		break;
			case '13':	return 'ARRIVED'; 		break;
			case '18':	return 'CANCELLED';		break;
			case '202':	return 'RESCHEDULED';	break;
			case '4':	return 'REACHED';		break;
			default:
				$res = imw_query("SELECT alias FROM schedule_status WHERE id=".(int)$status." LIMIT 1");
				if($res && imw_num_rows($res)==1){
					$rs = imw_fetch_assoc($res);
					return strtoupper($rs['alias']);
				}
				return '';
		}
	}

    private function patient_location($args=array()){
		$res = false;
        if($args[0]['message_type']=='SIU'){
			$q = "SELECT f.id,f.name,f.external_id FROM facility f JOIN schedule_appointments sa ON (f.id=sa.sa_facility_id) WHERE sa.id='".$args[0]['source_id']."' LIMIT 0,1";
			$res = imw_query($q);
		}else if($args[0]['message_type']=='ADT'){
			$sch_id = $this->get_sch_id_by_patient_id($args[0]['patient_id']);
			if($sch_id){
				$q = "SELECT f.id,f.name,f.external_id FROM facility f JOIN schedule_appointments sa ON (f.id=sa.sa_facility_id) WHERE sa.id='".$sch_id."' LIMIT 0,1";
				$res = imw_query($q);
			}
		}
		if($res && imw_num_rows($res)>0){
			$rs = imw_fetch_assoc($res);
			$format = $this->get_segment_value_format($args[0]['interface_id'],$args[0]['segment'],'patient_location');
			return $this->mask_array_to_format($rs,$format);
		}
		return '';
    }


    private function attending_doctor($args=array()){
        if($args[0]['message_type']=='SIU'){
			$q ="SELECT u.id,u.lname,u.fname,u.mname,u.external_id,u.user_npi AS npi FROM users u JOIN schedule_appointments sa ON (u.id=sa.sa_doctor_id) ";
			$q.="WHERE sa.id='".$args[0]['source_id']."' LIMIT 0,1";
			$res = imw_query($q);
		}else if($args[0]['message_type']=='ADT'){
			$sch_id = $this->get_sch_id_by_patient_id($args[0]['patient_id']);
			if($sch_id){
				$q ="SELECT u.id,u.lname,u.fname,u.mname,u.external_id,u.user_npi AS npi FROM users u JOIN schedule_appointments sa ON (u.id=sa.sa_doctor_id) ";
				$q.="WHERE sa.id='".$sch_id."' LIMIT 0,1";
				$res = imw_query($q);
			}else{
				$q ="SELECT u.id,u.lname,u.fname,u.mname,u.external_id,u.user_npi AS npi FROM users u JOIN patient_data pd ON (u.id=pd.providerID) ";
				$q.="WHERE pd.id='".$args[0]['patient_id']."' LIMIT 0,1";
				$res = imw_query($q);
			}
		}
		if($res && imw_num_rows($res)>0){
			$rs = imw_fetch_assoc($res);
			$format = $this->get_segment_value_format($args[0]['interface_id'],$args[0]['segment'],'attending_doctor');
			return $this->mask_array_to_format($rs,$format);
		}
		return '';
    }


	private function referring_doctor($args=array()){
		$q ="SELECT rf.physician_Reffer_id AS id, rf.NPI AS npi, rf.external_id, rf.LastName AS lname, rf.FirstName As fname, rf.MiddleName AS mname FROM refferphysician rf ";
		$q.="JOIN patient_data pd ON (rf.physician_Reffer_id = pd.primary_care_id) WHERE pd.id='".$args[0]['patient_id']."'";
		$res = imw_query($q);
		if($res && imw_num_rows($res)>0){
			$rs = imw_fetch_assoc($res);
			$format = $this->get_segment_value_format($args[0]['interface_id'],$args[0]['segment'],'referring_doctor');
			return $this->mask_array_to_format($rs,$format);
		}
		return '';
    }
	
	private function visit_number($args=array()){
		$sch_id = false;
		if($args[0]['message_type']=='SIU'){
			$sch_id = $args[0]['source_id'];
		}else if($args[0]['message_type']=='ADT'){
			$sch_id = $this->get_sch_id_by_patient_id($args[0]['patient_id']);
		}
		if($sch_id){
			$format = $this->get_segment_value_format($args[0]['interface_id'],$args[0]['segment'],'visit_number');
			return $this->mask_array_to_format(array('id'=>$sch_id),$format);
		}
		return '';
    }
	
	private function current_patient_balance($args=array()){
		$q="SELECT SUM(totalBalance) AS current_patient_balance FROM patient_charge_list WHERE del_status='0' AND patient_id = '".$args[0]['patient_id']."' GROUP BY patient_id";
		$res = imw_query($q);
		$rs = imw_fetch_assoc($res);
		return $rs['current_patient_balance'];
	}
	
	private function patient_account_number($args=array()){
		$sch_id = false;
		if($args[0]['message_type']=='SIU'){
			$sch_id = $args[0]['source_id'];
		}else if($args[0]['message_type']=='ADT'){
			$sch_id = $this->get_sch_id_by_patient_id($args[0]['patient_id']);
		}else if($args[0]['message_type']=='DFT'){
		//	$sch_id = $this->get_sch_id_by_superbill_id($args[0]['patient_id']);
		}
		if($sch_id){
			$dos = $this->get_dos_by_sch_id($sch_id);
			$q="SELECT account_num FROM hl7_received_accno WHERE patient_id	= '".$args[0]['patient_id']."' AND dt_of_visit	= '".$dos."' ORDER BY id DESC LIMIT 0,1";
			$res = imw_query($q);
			if($res && imw_num_rows($res)==1){
				$rs = imw_fetch_assoc($res);
				return $rs['account_num'];
			}
			//return $sch_id;
		}
		return '';
    }
	
	private function cl_lens_type($args=array()){
		$q ="SELECT contactlensworksheet_det.clType FROM contactlensmaster JOIN contactlensworksheet_det ON (contactlensworksheet_det.clws_id=contactlensmaster.clws_id) ";
		$q.= "WHERE contactlensmaster.clws_id='".$args[0]['source_id']."' AND contactlensmaster.del_status=0 ORDER BY contactlensworksheet_det.clEye ASC";
		$res = imw_query($q);
		$rs = imw_fetch_assoc($res);
		switch($rs['clType']){
			case 'scl'		: return 'Soft Contact'; break;
			case 'rgp'		: return 'Hard Contact'; break;
			case 'cust_rgp'	: return 'Hard Contact'; break;
			default: '';
		}
	}
	
	
	
	
	
	

}?>