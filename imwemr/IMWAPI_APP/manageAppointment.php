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
 * index.php
 * Access Type: InClude
 * Purpose: Routes for Appointment API calls.
*/

$this->respond(array('POST','GET'), '*', function($request, $response, $service, $app) use(&$patientId) {
		
	$where = $startDate = $endDate = '';
	$service->validateParam('patientId', 'Please provide valid Patient ID.')->isInt()->notNull()->isPatient($app);
	$patientId	= (int)$request->__get('patientId');
	
	if( $patientId <= 0)
	{
		$response->append('Invalid Patient ID. ');
		$this->abort(400);
	}
	
	//Physician ID
	$service->validateParam('physicianId', 'Please provide valid Physician ID.')->isInt()->notNull();
	$physicianId	= (int)$request->__get('physicianId');
	$service->__set('physicianId', $physicianId);
	
});

// To get Date of Time Slots //
$this->get('/getTimeSlotsDate', function($request, $response, $service,$app) use(&$patientId){
	$returnData = array();
	$returnDataPro = array();
	$responseFinal = array();
	$slotDate = array();
	$current_date = date("Y-m-d");
	
	$sql_pre_proc = $app->dbh->imw_query("SELECT procedureid, sa_doctor_id, sa_facility_id FROM schedule_appointments WHERE (sa_app_start_date <'".$current_date."' and sa_patient_id='".$patientId."' and sa_patient_app_status_id NOT IN('18', '203')) order by sa_app_start_date desc limit 0,1");
	if($sql_pre_proc && $app->dbh->imw_num_rows($sql_pre_proc) > 0){
			$row = $app->dbh->imw_fetch_assoc($sql_pre_proc);
			$responseFinal['proc_id'] = $row['procedureid'];
			$responseFinal['phy_id'] = $row['sa_doctor_id'];
			$responseFinal['facility_id'] = $row['sa_facility_id'];
		
	}
	// To Get Default Phycisian Name  //
		$qry_phy_name = $app->dbh->imw_query("SELECT 
									fname as FirstName,
									lname as LastName
								FROM users
								WHERE id = '".$responseFinal['phy_id']."'");
		$get_phy_name = $app->dbh->imw_fetch_assoc($qry_phy_name);
		$responseFinal['phy_name'] = $get_phy_name['LastName'].' '.$get_phy_name['FirstName'];
	
	// To Get Default procedure reason //
		$qryPro = $app->dbh->imw_query("SELECT id,
										proc as ProdecureName
									FROM slot_procedures
									WHERE id = '".$responseFinal['proc_id']."'");
		$get_proc = $app->dbh->imw_fetch_assoc($qryPro);
		$responseFinal['proc_name'] = $get_proc['ProdecureName'];
	
	// To Get Default facility name //
		$qryfac = $app->dbh->imw_query("SELECT id, name FROM facility WHERE id = '".$responseFinal['facility_id']."'");
		$get_fac = $app->dbh->imw_fetch_assoc($qryfac);
		$responseFinal['facility_name'] = $get_fac['name'];
	// To get All the Physician Name //
	$qry = $app->dbh->imw_query("SELECT 
									id as ProviderId,
									fname as FirstName,
									lname as LastName,
									user_npi as NPI				
								FROM users
								WHERE 
									user_type = 1 
								AND delete_status = 0
								AND superuser = 'no'
								ORDER BY lname ASC");
	if($qry && $app->dbh->imw_num_rows($qry) > 0){
		while($row = $app->dbh->imw_fetch_assoc($qry)){
			$row['name'] = $row['LastName'].' '.$row['FirstName'];
			unset($row['FirstName']);
			unset($row['LastName']);
			array_push($returnData, $row);
		}
	}unset($row);
	$responseFinal['Physician'] = $returnData;
	// End Of Code To get All the Physician Name //
	
	// To get Procedure //
	$qryPro = $app->dbh->imw_query("SELECT id,
										proc as ProdecureName,
										acronym as ProdecureAlias			
									FROM slot_procedures
									WHERE 
										active_status = 'yes'
									ORDER BY proc ASC");
	
	if($qryPro && $app->dbh->imw_num_rows($qryPro) > 0){
		while($rowPro = $app->dbh->imw_fetch_assoc($qryPro)){
			$rowPro['ProdecureName'] = trim($rowPro['ProdecureName']);
			if( empty($rowPro['ProdecureName']) == false ){
				array_push($returnDataPro, $rowPro);
			}
		}
		$app->dbh->imw_free_result($qryPro);
	}unset($rowPro);
	$responseFinal['Procedure'] = $returnDataPro;
	// End Of Code To get Procedure //
	//Validating Dates
	if($request->__isset('StartDate')){
		$service->validateParam('StartDate', 'Please provide valid start date.')->notNull()->isDate();
		
		$startDate	= $app->dbh->imw_escape_string( $request->__get('StartDate') );
	}
	
	if($request->__isset('EndDate') && trim($request->__get('EndDate')) !== '' ){
		$service->validateParam('EndDate', 'Please provide valid end date.')->notNull()->isDate();
		
		$endDate	= $app->dbh->imw_escape_string( $request->__get('EndDate') );
		
		if($request->__isset('StartDate')){
			
			$service->validateParam('StartDate', 'Please provide valid start date also.')->notNull()->isDate();
		}
	}
	
	if( empty($startDate) === false && empty($endDate) === true )
	{
		$endDate = $startDate;
	}
	
	$physicianId = $service->__get('physicianId');
	
	$facName = $request->__get('fac');
	
	if($facName!='' && $facName!='0'){
		$responseFinal['facility_name'] = ucfirst($facName);
	}
	$apptObj = new IMW\APPOINTMENT($app->dbh, $service);
	
	//Get Time Slots
	$timeSlots = $apptObj->getAvailableSlots($startDate, $endDate, $physicianId);
	
	$slotDate = array();
	$facilities = array();
	$i = 0;
	foreach($timeSlots as $facility => $slot){
		
		// To get time slotes of Default Facility //
		if(strtolower($facility) === strtolower($responseFinal['facility_name'])){
			
			foreach($slot as $key=>$val){
				$slotDate[0]['Facility'] = $facility;
				$shortDate = explode('-',$key);
				$slotDate[0]['Slots'][] = array("Date"=>date('m-d-Y',strtotime($key)),"DateY"=>$key,"ShortDate"=>$shortDate[2],"Color"=>"#f4cb42");
			}
			
		}
		$i++;
	}
	// To get Physician Facilities //
	$user_qry = "SELECT id, sch_facilities, user_group_id FROM users WHERE id = ".$physicianId;
	$resultSet = $app->dbh->imw_query($user_qry);
	$fetchRow = $app->dbh->imw_fetch_assoc($resultSet);
	$facilities_id_str = str_ireplace(";",",",$fetchRow['sch_facilities']);
	
	$qryFacilites = "SELECT id, name FROM facility WHERE id IN(".$facilities_id_str.")";
	$resultSetFacilities = $app->dbh->imw_query($qryFacilites);
	while($fetchFacilities = $app->dbh->imw_fetch_assoc($resultSetFacilities)){
		$facilities[]['Facility'] = $fetchFacilities['name'];
	}
	// End of code to Get Physician Facilities //
	
	//If there is some error - abort the call
	if($timeSlots && isset($timeSlots['errorStr'])){
		$response->append($timeSlots['errorStr']);
		$this->abort(400);
	}
	
	//if($timeSlots && count($timeSlots) > 0){
		$responseFinal['Facilities'] = ($facilities);
		$responseFinal['FacilitySlots'] = ($slotDate);
	//}
	$response = $responseFinal;
	return json_encode($response);
});

// To get Time Slots //

$this->get('/getTimeSlots', function($request, $response, $service,$app) use(&$patientId){
	
	$dateSlots = array();
	$finalTimeSlot =array();
	//Validating Dates
	if($request->__isset('StartDate')){
		$service->validateParam('StartDate', 'Please provide valid start date.')->notNull()->isDate();
		
		$startDate	= $app->dbh->imw_escape_string( $request->__get('StartDate') );
	}
	
	if($request->__isset('EndDate') && trim($request->__get('EndDate')) !== '' ){
		$service->validateParam('EndDate', 'Please provide valid end date.')->notNull()->isDate();
		
		$endDate	= $app->dbh->imw_escape_string( $request->__get('EndDate') );
		
		if($request->__isset('StartDate')){
			
			$service->validateParam('StartDate', 'Please provide valid start date also.')->notNull()->isDate();
		}
	}
	
	if( empty($startDate) === false && empty($endDate) === true )
	{
		$endDate = $startDate;
	}
	
	$physicianId = $service->__get('physicianId');
	
	$slotDate = $request->__get('slotDate');
	
	$facName = $request->__get('fac');
	
	$apptObj = new IMW\APPOINTMENT($app->dbh, $service);
	
	//Get Time Slots
	$timeSlots = $apptObj->getAvailableSlots($startDate, $endDate, $physicianId);
	
	$i = 0;
	foreach($timeSlots as $facility => $slot){
		if( strtolower($facility) == strtolower($facName)){
			foreach($slot as $key => $val){
				$nameOfDay = date('l', strtotime($key));
				$dateSlots[] = array('Date' => date('m-d-Y',strtotime($key)),"DateY"=>$key, "Day"=>$nameOfDay);
				if($slotDate === $key){
					$finalTimeSlot = array('Date' => date('m-d-Y',strtotime($key)), 'TimeSlots' => $val);
				}
			}
		}
		$i++;
	}
	//If there is some error - abort the call
	if($timeSlots && isset($timeSlots['errorStr'])){
		$response->append($timeSlots['errorStr']);
		$this->abort(400);
	}
	
	$returnArr = array();
	//if($timeSlots && count($timeSlots) > 0){ 
		$returnArr = array('ApptDates' => $dateSlots,'DateTimeSlots' => $finalTimeSlot);
	//}
	
	return json_encode($returnArr);
});

$this->get('/bookAppointment', function($request, $response, $service,$app) use(&$patientId){
	
	//TimeSlot ID
	$service->validateParam('TimeSlotId', 'Please provide valid TimeSlot ID.')->isInt()->notNull()->isTimeSlot($this);
	$TimeSlotId	= (int)$request->__get('TimeSlotId');
	
	//Facility ID
	$service->validateParam('facilityId', 'Please provide valid Facility ID.')->isInt()->notNull()->isFacility($app);
	$facilityId	= (int)$request->__get('facilityId');
	
	//Schedule Template ID
	$service->validateParam('TemplateId', 'Please provide valid Schedule Template ID.')->isInt()->notNull();
	$ScheduleTemplateId	= (int)$request->__get('TemplateId');
	
	//Appointment Reason
	$service->validateParam('AppointmentReason', 'Please provide valid Procedure ID.')->isInt()->notNull()->isProcedure($app);
	$ApptReasonId	= (int)$request->__get('AppointmentReason');
	
	//Appointment Information
	$AppointmentInformation = $request->__get('AppointmentInformation');
	
	$physicianId = $service->__get('physicianId');
			
	$apptObj = new IMW\APPOINTMENT($app->dbh, $service);
	
	//Book Appointment
	$status = $apptObj->bookAppointment($patientId, $TimeSlotId, $physicianId, $facilityId, $ApptReasonId, $AppointmentInformation, $ScheduleTemplateId);
	$returnResult = array("BookAppointment"=>$status);
	return json_encode($returnResult);
});

$this->respond(function($request, $response, $service) use(&$patientId) {

});