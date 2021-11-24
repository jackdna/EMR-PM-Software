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
?><?php
/*
File: appt_cancel_portal_handle.php
Purpose: Add appointment in iDoc from Patient Portal
Access Type: Direct
*/

//$ignoreAuth=true;
set_time_limit(0);
include_once("../../config/globals.php");
require_once($GLOBALS['fileroot'] . '/library/erp_portal/appointments.php');
$AppointmentsObj = new Appointments();
$pull_cancel_appt = $_REQUEST['pull_cancel_appt'];
if($pull_cancel_appt == 'yes') {
	$patient_arr = array();
	$dtTm = date("Y_m_d_H_i_s");
	$id = $dtTm;
	$resource = 'appointmentcancelrequests/search?alreadySent=false';
	$method='GET';
	$msg = $AppointmentsObj->addUpdateAppointmentCancelStatuses($patient_arr,$id,$resource,$method);
	echo $msg;
	exit;
}


$sel_op_arr = array('approve'=>'Approved', 'decline'=>'Declined');
$sel_op = $_REQUEST['sel_op'];
$row_id = (int)$_REQUEST['row_id'];
$c_date = date("Y-m-d");
$c_time = date("H:i:s");
$status = 18; //CANCELLED
if($row_id) {
	$qry = "SELECT cr.id as row_id, cr.app_ext_id, cr.app_can_req_id, cr.can_reason, 
			sa.sa_patient_id, sa.sa_app_start_date, sa.sa_app_starttime, sa.sa_doctor_id, sa.sa_facility_id, sa.sa_comments, 
			sa.sa_patient_app_status_id, sa.sa_app_endtime, sa.procedureid, sa.sec_procedureid, sa.tertiary_procedureid 
			FROM iportal_app_reqs cr
			INNER JOIN schedule_appointments sa ON (sa.id = cr.app_ext_id)
			WHERE cr.id IN(".$row_id.") AND cr.aprv_dec = '0' AND cr.app_can_req_id != '' ";
	$res=imw_query($qry) or die(imw_error().$qry);
	$numrow = imw_num_rows($res);
	if($numrow>0) {
		while($row = imw_fetch_assoc($res)) {
			$sch_id 	= trim($row['app_ext_id']);
			$can_reason = $row['can_reason'];
			$qry_updt = $qry_prev = $qry_aprv = '';
			if($sel_op=='approve') {
				$approve_decline = '1';
				$aprved = true;
				$res_msg = "Approved - ".$row['can_reason'];
				$err_msg = "";
			}else {
				$approve_decline = '2';
				$aprved = false;
				$res_msg = "Declined - ".$row['can_reason'];
				$err_msg = "";
			}
            $apptArr=array();
            $apptArr['internalID']		= $row['app_can_req_id'];
            $apptArr['approved']		= $aprved;
            $apptArr['resultMessage']	= $res_msg;
            $apptArr['errorMessage']	= $err_msg;
			$id 						= $row['sa_patient_id'];
			$resource 					= 'AppointmentCancelRequests';
			$method						= 'POST';
			$response = $AppointmentsObj->addUpdateAppointmentCancelStatuses($apptArr,$id,$resource,$method);
			if(count($response) > 0) {
				if($sel_op=='approve') {
					$qry_updt 	= "UPDATE schedule_appointments SET sa_patient_app_status_id = '18' WHERE id = '".$sch_id."' ";
					$res_updt	= imw_query($qry_updt) or die(imw_error().$qry_updt);
					
					$qry_prev 	= "	INSERT INTO previous_status SET
									sch_id 					= '".$sch_id."',
									patient_id 				= '".$row['sa_patient_id']."',
									status_time 			= '".$c_time."',
									status_date 			= '".$c_date."',
									status 					= '".$status."',
									old_date 				= '".$row['sa_app_start_date']."',
									old_time 				= '".$row['sa_app_starttime']."',
									old_provider 			= '".$row['sa_doctor_id']."',
									old_facility 			= '".$row['sa_facility_id']."',
									statusComments 			= '".addslashes($row['sa_comments'])."',
									oldMadeBy 				= 'admin',
									statusChangedBy 		= 'admin',
									dateTime 				= '".$c_date." ".$c_time."',
									new_facility 			= '".$row['sa_facility_id']."',
									new_provider 			= '".$row['sa_doctor_id']."',
									old_status 				= '".$row['sa_patient_app_status_id']."',
									new_appt_date 			= '".$row['sa_app_start_date']."',
									new_appt_start_time 	= '".$row['sa_app_starttime']."',
									old_appt_end_time 		= '".$row['sa_app_endtime']."',
									new_appt_end_time 		= '".$row['sa_app_endtime']."',
									old_procedure_id 		= '".$row['procedureid']."',
									old_sec_procedure_id 	= '".$row['sec_procedureid']."',
									old_ter_procedure_id 	= '".$row['tertiary_procedureid']."',
									oldStatusComments 		= '".addslashes($row['sa_comments'])."',
									new_procedure_id 		= '".$row['procedureid']."',
									new_sec_procedure_id 	= '".$row['sec_procedureid']."',
									new_ter_procedure_id 	= '".$row['tertiary_procedureid']."',
									change_reason 			= '".addslashes($can_reason)."'";
				
					$res_prev	= imw_query($qry_prev) or die(imw_error().$qry_prev);
				}

				$qry_aprv 	= "UPDATE iportal_app_reqs SET aprv_dec = '".$approve_decline."', action_date_time = '".$c_date." ".$c_time."', operator_id = '".$_SESSION['authId']."' WHERE id = '".$row_id."'"; 
				$res_aprv	= imw_query($qry_aprv) or die(imw_error().$qry_aprv);
				
				//SEND STATUS TO PORTAL
				$response_portal= $AppointmentsObj->addUpdateAppointments($row['app_ext_id'],$row['sa_patient_id'],$row['can_reason']);
				
				if($sch_id && $res_aprv){
					$json_msg = json_encode(array('status'=>'success','pt_id'=>$row['sa_patient_id'],'approval'=>$sel_op_arr[$sel_op], 'msg'=>''));
				}
			}else {
				$json_msg =  json_encode(array('status'=>'error', 'msg'=>'Please check if RabbitMQ is working'));
			}
		}
	}else{
		$json_msg =  json_encode(array('status'=>'error', 'msg'=>'Appointment does not exist in IMW'));
	}
}
if(!$json_msg) {
	$json_msg = json_encode(array('status'=>'error', 'msg'=>'No data found'));
}
echo $json_msg;
?>