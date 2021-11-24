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
include_once(dirname(__FILE__).'/user_app.php');
class imedic_monitor extends user_app{	
	public function __construct(){
		parent::__construct();
	}
	public function get_scheduled_patients(){
		$date = isset($_REQUEST['date'])?$_REQUEST['date']:date('Y-m-d');
		$facility_id = isset($_REQUEST['facility_id'])?$_REQUEST['facility_id']:'';
		
		$this->db_obj->qry  = "SELECT schedule_appointments.id, sa_patient_id AS patient_id, 
								 sa_patient_name AS patient_name, 
								 sa_patient_app_status_id AS status, 			 								
								 TIME_FORMAT(sa_app_starttime, '%h:%i %p') AS appt_time, 
								 sp.proc,
								 patient_data.phone_cell,
								 patient_data.phone_home,
								 patient_data.sex,
								 patient_data.DOB,
								 facility.name AS facility_name,
								 replace(facility.facility_color,'#','') AS facility_color
								 FROM `schedule_appointments` 
								 LEFT JOIN slot_procedures sp ON sp.id = schedule_appointments.procedureid 
								 LEFT JOIN facility ON facility.id = schedule_appointments.sa_facility_id
								 
								 LEFT JOIN patient_data ON patient_data.pid = schedule_appointments.sa_patient_id
								 
								 WHERE sa_app_start_date='".$date ."' 
								 AND schedule_appointments.sa_doctor_id='".$this->authId."'
								 AND schedule_appointments. sa_patient_app_status_id NOT IN(18,201,203)";
		if($facility_id!="")						 
		$this->db_obj->qry .=  " AND schedule_appointments.sa_facility_id IN(".$facility_id.")";
		$this->db_obj->qry .= " order by sa_doctor_id, sa_app_starttime";
		//echo $this->db_obj->qry;						 
		$result_arr = $this->db_obj->get_resultset_array();
		$result_arr_new=array();
		foreach($result_arr as $arr){
			// enter  a new value "n/a" insterd of blank //
				if($arr['phone_cell'] == ""){
					$arr['phone_cell'] = 'N/A';
					

					
				}
				if($arr['phone_home'] == ""){
					$arr['phone_home'] = 'N/A';
				
		         }
				 // end of code "N/A"
				 
				 // change a dob into age
				 $birthdate = new DateTime($arr['DOB']);
        		 $today   = new DateTime('today');
        		 $age = $birthdate->diff($today)->y;
      			 $arr['age']=$age;
				 $result_arr_new[]=$arr;
				 
				 //end of age
				 
		}
		
		return $result_arr_new;						 
	}
	
	public function get_waiting_patients(){
		$date = isset($_REQUEST['date'])?$_REQUEST['date']:date('Y-m-d');
		$facility_id = isset($_REQUEST['facility_id'])?$_REQUEST['facility_id']:'';
		
		$this->db_obj->qry  = "SELECT schedule_appointments.id, 
								 sa_patient_id AS patient_id, 
								 sa_patient_name AS patient_name, 
								 sa_patient_app_status_id AS status, 			 								
								 TIME_FORMAT(sa_app_starttime, '%h:%i %p') AS appt_time, 
								 sp.proc,
								  patient_data.sex,
								 patient_data.DOB,
								 patient_data.phone_cell,
								 patient_data.phone_home,
								 facility.name AS facility_name
								 FROM `schedule_appointments` 
								 LEFT JOIN slot_procedures sp ON sp.id = schedule_appointments.procedureid 
								 LEFT JOIN facility ON facility.id = schedule_appointments.sa_facility_id
								
								 LEFT JOIN patient_data ON patient_data.pid = schedule_appointments.sa_patient_id
								 WHERE sa_app_start_date='".$date ."' 
								 AND schedule_appointments.sa_doctor_id='".$this->authId."'
								 ";
		if($facility_id!="")						 
		$this->db_obj->qry .=  " AND schedule_appointments.sa_facility_id IN(".$facility_id.")";
		$this->db_obj->qry .=  " AND schedule_appointments.sa_patient_app_status_id NOT IN (203,201,18,19,20,11) 
								 ORDER BY sa_doctor_id, sa_app_starttime";
		$this->db_obj->qry;						 
		$result_arr = $this->db_obj->get_resultset_array();
		$arrReturn = array();
		$count = 0;
		foreach($result_arr as $arr){
			//$arrReturn[$count] = $arr;
			$this->db_obj->qry  = "SELECT TIME_FORMAT(status_time, '%h:%i %p') AS checkin_time,previous_status.* FROM previous_status WHERE sch_id IN (".$arr['id'].") AND status = '13' ORDER BY id DESC LIMIT 0,1";
			$prev_status = $this->db_obj->get_resultset_array();
			if(count($prev_status)>0){
				$arr['status'] = 13;
				$arr['checkin_time'] = $prev_status[0]['checkin_time'];
				
				// enter  a new value "n/a" insterd of blank //
				if($arr['phone_cell'] == ""){
					$arr['phone_cell'] = 'N/A';
					

					
				}
				if($arr['phone_home'] == ""){
					$arr['phone_home'] = 'N/A';
				
		         }
				  // change a dob into age
				 $birthdate = new DateTime($arr['DOB']);
        		 $today   = new DateTime('today');
        		 $age = $birthdate->diff($today)->y;
      			 $arr['age']=$age;
				 $result_arr_new[]=$arr;
				 
				 //end of age
				 
				 // end of code "n/a"
				$arrReturn[$count] = $arr;
				
			}
			$this->db_obj->qry  = "SELECT * FROM patient_location WHERE patientId IN (".$arr['patient_id'].") AND cur_date = '".$date."' ORDER BY patient_location_id DESC LIMIT 0,1";
			$patient_loca = $this->db_obj->get_resultset_array();
			if(count($patient_loca)>0){
				if($patient_loca[0]['pt_with'] == 6){
					unset($arrReturn[$count]);
				}
			}
			$count++;
		}
		$arrReturn1 = array();
		
		foreach($arrReturn as $result_arr_new){
			$arrReturn1[] = $arr;
		}
		
		
		return $arrReturn1;
								 
	}
}

?>