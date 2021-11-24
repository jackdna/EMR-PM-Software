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
File: future_appointments.php
Purpose: This file provides future appointments.
Access Type : Include file
*/
set_time_limit(900);

class future_appointment{
	private $patient_id;
	private $form_id;
	
	public function __construct($patient_id, $form_id){
		$this->patient_id= $patient_id;
		$this->form_id = $form_id;		
	}
	
	public function get_future_appointments(){
		$arrMainRet=array();	
		//require_once $GLOBALS["incdir"]."/chart_notes/common/functions.php";
		$patient_id = $this->patient_id;
		$form_id = $this->form_id;
	
		$arrMainRet=array();
		$qry_internal_appt = "SELECT DATE_FORMAT(schedule_appointments.sa_app_start_date,'".get_sql_date_format('','y')."') as start_date,
		DATE_FORMAT(schedule_appointments.sa_app_starttime,'%H:%i%p') as start_time,
		 users . fname,users . lname , facility . name ,slot_procedures.acronym as procName
		FROM schedule_appointments
		LEFT JOIN users ON users.id = schedule_appointments.sa_doctor_id
		LEFT JOIN facility ON facility.id = schedule_appointments.sa_facility_id
		LEFT JOIN slot_procedures ON slot_procedures.id = schedule_appointments.procedureid
		WHERE schedule_appointments.sa_patient_id = '$patient_id' and
		schedule_appointments.sa_patient_app_status_id NOT IN(201,18,19,20,203) and 
		CONCAT(schedule_appointments.sa_app_start_date ,' ',
		schedule_appointments.sa_app_starttime)
		> CONCAT(CURDATE(),' ',
		CURTIME())
		ORDER BY sa_app_start_date, sa_app_starttime
		";
		$rez=sqlStatement($qry_internal_appt);	
		while($row = imw_fetch_assoc($rez)){
			$arrMainRet['internal'][]=$row;
		}
		
		$qry_external_appt="SELECT *,DATE_FORMAT(chart_schedule_test_external.schedule_date,'".get_sql_date_format('','y')."') as schedule_date FROM chart_schedule_test_external WHERE patient_id = '".$patient_id."' AND deleted_by = '0' ORDER BY id";
		$rez_e=sqlStatement($qry_external_appt);	
		while($row_e= imw_fetch_assoc($rez_e)){
			$arrMainRet['external'][]=$row_e;
		}
		if(count($arrMainRet)==0){
			$arrMainRet['internal']=array();$arrMainRet['external']=array();
		}
		return $arrMainRet; 
	}
}
?>