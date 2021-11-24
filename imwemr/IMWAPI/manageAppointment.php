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
 * Purpose: Routes for Appointment regarding API calls.
*/

$this->respond(array('POST','GET'), '*', function($request, $response, $service, $app) use(&$patientId) {
	
	//Checking if Make Appointment option is available or not -- Commented as not required yet
	/* $validateCall = true;
	$addAppointmentCall = '';
	
	$chkCall = $app->dbh->imw_query('SELECT iportal_eve FROM  facility where facility_type = 1');
	if($app->dbh->imw_num_rows($chkCall) > 0){
		$rowPriv = $app->dbh->imw_fetch_assoc($chkCall);
		list( $eve, $glrx, $addAppointmentCall, $show_physician_rating_iportal, $do_not_show_upcoming_app_iportal ) = explode("~||~", $rowPriv['iportal_eve']);
		
		if(empty($addAppointmentCall)){
			$validateCall = false;
		}
	}else{
		$validateCall = false;
	}
	
	if($validateCall == false){
		$response->append('Access Denied. Call point disabled.');
		$this->abort(400);
	} */
	
	$where = $startDate = $endDate = '';
	$service->validateParam('patientId', 'Please provide valid Patient ID.')->isInt()->notNull()->isPatient($app);
	$patientId	= (int)$request->__get('patientId');
	
	if( $patientId <= 0)
	{
		$response->append('Invalid Patient ID. ');
		$this->abort(400);
	}
	
	//Physician ID
	$service->validateParam('physicianId', 'Please provide valid Physician ID.')->isInt()->notNull()->isPhysician($app, 'Scheduler');
	$physicianId	= (int)$request->__get('physicianId');
	$service->__set('physicianId', $physicianId);
	
});

$this->get('/getTimeSlots', function($request, $response, $service,$app) use(&$patientId){
	
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
	
	$apptObj = new IMW\APPOINTMENT($app->dbh, $service);
	
	//Get Time Slots
	$timeSlots = $apptObj->getAvailableSlots($startDate, $endDate, $physicianId);
	
	//If there is some error - abort the call
	if($timeSlots && isset($timeSlots['errorStr'])){
		$response->append($timeSlots['errorStr']);
		$this->abort(400);
	}
	
	$returnArr = array('TimeSlots' => 'No Time slots available.');
	if($timeSlots && count($timeSlots) > 0) $returnArr = array('TimeSlots' => $timeSlots);
	
	return json_encode($returnArr);
});

$this->post('/bookAppointment', function($request, $response, $service,$app) use(&$patientId){
	
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
	
	//If there is some error - abort the call
	if($status && isset($status['errorStr'])){
		$response->append($status['errorStr']);
		$this->abort(400);
	}
	
	return json_encode($status);
});

$this->respond(function($request, $response, $service) use(&$patientId) {

});