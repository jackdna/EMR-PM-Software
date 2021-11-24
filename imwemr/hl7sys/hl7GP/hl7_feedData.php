<?php
/*
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
//	-------------USED IN ZEISS HL7------------
*/
?>
<?php
/*
 *File: hl7Create.php
 *Puppose: Feed Data to hl7 message generation class
 *Access Type: Include
 */
include_once($path."hl7Create.php");
class hl7_feedData extends hl7Create{
    
    private $contacthlId=1;
    public $PD = array();
    public $msgtype, $fillStatus;
    /*private $insCaseId = ""; /*Insurance Case Id for IN1 segments in SIU messages for URAM*/
    private $defaultInsCaseId = "";
    private $sendInsDocs=false;
    
    
    public function __construct(){
        
        parent::__construct();
        if(constant("HL7_SIU_GENERATION")==true){
            if(strtolower($GLOBALS["LOCAL_SERVER"])=='uram'){ /*change receiving application and receiving fdacility name for URAM server*/
                $this->setReceivingFacility("TEST", "TEST");
                /*array_push($this->msgtypes['SIU']['segments'], "IN1");*/
            }
            $this->msgtypes['SIU']['msg_structure']="";
        }
    }
    
    public function insertSegment($segm, $msgType=""){
        
        switch($segm){
            case "PID":
                    $sql = "SELECT * FROM `patient_data` WHERE `id` ='".$this->PD['id']."' AND `DOB`!='0000-00-00' AND `DOB`!='' AND `sex`!=''";
                    $sqlD = imw_query($sql);
                    if($sqlD){
                        if(imw_num_rows($sqlD)>0){
                            $row = imw_fetch_assoc($sqlD);
                            $data = array();
                            $data['setId_PID']	=	1;
                            //$data['patient_external_id']['ioPtId'] = $row['iolinkPatientId'];
                            //$data['patient_external_id']['ioPtWtId'] = $row['iolinkPatientWtId'];
                            $data['patient_internal_id']	= $row['pid'];
                            $data['patient_name']['family_name'] = $row['lname'];
                            $data['patient_name']['given_name']  = $row['fname'];
                            $data['patient_name']['middle_name'] = $row['mname'];
                            $data['patient_name']['name_suffix'] = $row['suffix'];
                            $data['patient_name']['name_prefix'] = $row['title'];
                            $data['mother_maiden_name']['family_name'] = $row['maiden_lname'];
                            $data['mother_maiden_name']['given_name']  = $row['maiden_fname'];
                            $data['mother_maiden_name']['middle_name'] = $row['maiden_mname'];
                            $data['dob']	=	str_replace(" ", "", $row['DOB']);
                            $data['sex']	=	$row['sex'];
                            $data['race']	=	$row['race'];
                            $data['patient_address']['street']  = $row['street'];
                            $data['patient_address']['other_designation'] = $row['street2'];
                            $data['patient_address']['city'] =	$row['city'];
                            $data['patient_address']['state_province'] = $row['state']; 
                            $data['patient_address']['zip'] = $row['postal_code'].$row['zip_ext'];
                            $data['patient_address']['country'] = $row['country_code'];
                            $data['country_code']	=	$row['country_code'];
                            $data['phone_home']['telephone_number']['home'] = str_replace(" ", "", $row['phone_home']);
                            $data['phone_home']['telephone_number']['cell'] = str_replace(" ", "", $row['phone_cell']);
                            $data['phone_home']['email']  = $row['email'];
                            $data['phone_business']['telephone_number'] = str_replace(" ", "", $row['phone_biz']);
                            $data['primary_language'] = $row['language'];
                            $data['marital_status'] = $row['status'];
                            $data['ssn'] = $row['ss'];
                            $data['driver_licence'] = $row['driving_licence'];
                            $data['ethnic_group'] = $row['ethnicity'];
                            /*$hlPid = $data;*/
                            $this->addSegment("PID", $data);
                        }
                    }
                break;
            case "NTE":
                    $sql = "SELECT `patient_notes` FROM `patient_data` WHERE `id` ='".$this->PD['id']."'";
                    $sqlD = imw_query($sql);
                    if($sqlD){
                        if(imw_num_rows($sqlD)>0){
                            $row = imw_fetch_assoc($sqlD);
                            $data = array();
                            
                            $data = array();
                            $data['setId_NTE'] = 1;
                            $data['comment'] = $row['patient_notes'];
                            $this->addSegment("NTE", $data);
                        }
                    }
                break;
            case "NK1EMC": /*Emergency Contact*/
                    $sql = "SELECT `contact_relationship`, `emergencyRelationship`, `phone_contact` FROM `patient_data` WHERE `id` ='".$this->PD['id']."'";
                    $sqlD = imw_query($sql);
                    if($sqlD){
                        if(imw_num_rows($sqlD)>0){
                            $row = imw_fetch_assoc($sqlD);
                            if($row['contact_relationship']!="" || $row['emergencyRelationship']!="" || $row['phone_contact']!=""){
                                $data = array();
                                $data['setId_NK1'] = $this->contacthlId++;
                                $data['name']['given_name'] = $row['contact_relationship'];
                                $data['relationship'] = $row['emergencyRelationship'];
                                $data['phone_home']['telephone_number']['home'] = str_replace(" ", "", $row['phone_contact']);
                                $data['contact_role'] = "emergency contact";
                                $this->addSegment("NK1", $data, $msgType);
                            }
                        }
                    }
                break;
            case "NK1": /*Family Information*/
                    $sql = "SELECT * FROM `patient_family_info` WHERE `patient_id`='".$this->PD['id']."'";
                    $sqlD = imw_query($sql);
                    if($sqlD){
                        if(imw_num_rows($sqlD)>0){
                            while($row = imw_fetch_assoc($sqlD)){
                                $data = array();
                                $data['setId_NK1'] = $this->contacthlId++;
                                $data['name']['family_name'] = $row['lname'];
                                $data['name']['given_name'] = $row['fname'];
                                $data['name']['middle_name'] = $row['mname'];
                                $data['name']['name_suffix'] = $row['suffix'];
                                $data['name']['name_prefix'] = $row['title'];
                                $data['relationship'] = $row['patient_relation'];
                                $data['address']['street'] = $row['street1'];
                                $data['address']['other_designation'] = $row['street2'];
                                $data['address']['city'] = $row['city'];
                                $data['address']['state_province'] = $row['state'];
                                $data['address']['zip'] = $row['postal_code'].$row['zip_ext'];
                                $data['phone_home']['telephone_number']['home'] = str_replace(" ", "", $row['home_phone']);
                                $data['phone_home']['telephone_number']['cell'] = str_replace(" ", "", $row['mobile_phone']);
                                $data['phone_home']['email'] = $row['email_id'];
                                $data['phone_business']['telephone_number'] = str_replace(" ", "", $row['work_phone']);
                                $this->addSegment("NK1", $data, $msgType);
                            }
                        }
                    }
                break;
            case "PV1":
                if(!isset($this->PD['schid']) && $msgType!="ADT"){break;}
                    if($msgType=="ADT"){ /*PV1 segment for ADT messages- Primary Phy. is added as "Attending Doctor". Patient-id is used to query*/
                    $sql = "SELECT `u`.`user_npi` AS 'ap_code', `u`.`lname` AS 'ap_lname', `u`.`fname` AS 'ap_fname', `u`.`mname` AS 'ap_mname', `rf2`.`NPI` AS 'rp_code', `rf2`.`LastName` AS 'rp_lname', `rf2`.`FirstName` AS 'rp_fname', `rf2`.`MiddleName` AS 'rp_mname', `rf`.`NPI` AS 'adp_code', `rf`.`LastName` AS 'adp_lname', `rf`.`FirstName` AS 'adp_fname', `rf`.`MiddleName` AS 'adp_mname' FROM `patient_data` `pd` LEFT JOIN `refferphysician` `rf` ON (`rf`.`physician_Reffer_id` = `pd`.`primary_care_phy_id`) LEFT JOIN `refferphysician` `rf2` ON (`rf2`.`physician_Reffer_id` = `pd`.`primary_care_id`) LEFT JOIN `users` `u` ON (`u`.`id` = `pd`.`providerID`) WHERE `pd`.`pid`='".$this->PD['id']."'";
                }
                else{ /*PV1 segment for ADT messages- Phy. to witch appointment is booked is added as "Attending Doctor" Schedule-id/appointment-id is used to query*/
                    $sql = "SELECT `u`.`user_npi` AS 'ap_code', `u`.`lname` AS 'ap_lname', `u`.`fname` AS 'ap_fname', `u`.`mname` AS 'ap_mname', `rf2`.`NPI` AS 'rp_code', `rf2`.`LastName` AS 'rp_lname', `rf2`.`FirstName` AS 'rp_fname', `rf2`.`MiddleName` AS 'rp_mname', `rf`.`NPI` AS 'adp_code', `rf`.`LastName` AS 'adp_lname', `rf`.`FirstName` AS 'adp_fname', `rf`.`MiddleName` AS 'adp_mname', CONCAT(DATE_FORMAT(`appt`.`sa_app_start_date`,'%Y%m%d'),DATE_FORMAT(`appt`.`sa_app_starttime`,'%H%i%s')) AS 'visit_date_time', `appt`.`sa_facility_id`, `appt`.`id` AS 'appt_id', `appt`.`case_type_id` AS 'insurance_case_id' FROM `schedule_appointments` `appt` INNER JOIN `patient_data` `pd` ON(`appt`.`sa_patient_id`=`pd`.`pid`) LEFT JOIN `refferphysician` `rf` ON (`rf`.`physician_Reffer_id` = `pd`.`primary_care_phy_id`) LEFT JOIN `refferphysician` `rf2` ON (`rf2`.`physician_Reffer_id` = `pd`.`primary_care_id`) LEFT JOIN `users` `u` ON (`u`.`id` = `appt`.`sa_doctor_id`) WHERE `appt`.`id`='".$this->PD['schid']."'";
                }
                $sqlD = imw_query($sql);
                if($sqlD){
                    if(imw_num_rows($sqlD)>0){
                        $i = 1;
                        while($row = imw_fetch_assoc($sqlD)){
                            
                            $data = array();
                            $data['setId_PV1'] = $i++;
                            if(isset($row['sa_facility_id']) && $row['sa_facility_id']!=""){
                                $facility = $this->getFacilityName($row['sa_facility_id']);
                                $data['assigned_patient_location']['point_of_care'] = $facility['athenaID'];
                                $data['assigned_patient_location']['room'] = $facility['name'];
                            }
                            $data['attending_doctor']['id'] = $row['ap_code'];
                            $data['attending_doctor']['family_name'] = $row['ap_lname'];
                            $data['attending_doctor']['given_name'] = $row['ap_fname'];
                            $data['attending_doctor']['middle_name'] = $row['ap_mname'];
                            $data['referring_doctor']['id'] = $row['rp_code'];
                            $data['referring_doctor']['family_name'] = $row['rp_lname'];
                            $data['referring_doctor']['given_name'] = $row['rp_fname'];
                            $data['referring_doctor']['middle_name'] = $row['rp_mname'];
                            $data['admitting_doctor']['id'] = $row['adp_code'];
                            $data['admitting_doctor']['family_name'] = $row['adp_lname'];
                            $data['admitting_doctor']['given_name'] = $row['adp_fname'];
                            $data['admitting_doctor']['middle_name'] = $row['adp_mname'];
                            /*$data['admit_date_time'] = $row['visit_date_time'];*/
                            if(isset($row['appt_id']) && $row['appt_id']!=""){
                                if(strtolower($GLOBALS["LOCAL_SERVER"])!='uram'){ /*Remove Appointment ID from PV1.19 & PV1.50*/
                                    $data['visit_number'] = $row['appt_id'];
                                    $data['alternate_visit_id'] = $row['appt_id'];
                                }
                            }
                            $this->addSegment("PV1", $data, $msgType);
                            
                            /*AD IN1 segment in SIU messages for URAM*/
                            /*if($row['insurance_case_id']!=="" && $row['insurance_case_id']>0 && strtolower($GLOBALS["LOCAL_SERVER"])=='uram' && $msgType=="SIU"){
                                $this->insCaseId = $row['insurance_case_id']; /*Insurance Case Id for the appointment* /
                                $this->insertSegment("IN1", $msgType);
                                $this->insCaseId = "";
                            }*/
                        }
                    }
                }
                break;
            case "PR1":
                if(!isset($this->PD['schid'])){break;}
                $sqlAppointmentsQry="SELECT * FROM `schedule_appointments` WHERE `sa_patient_app_status_id` NOT IN(201) AND `id` = '".$this->PD['schid']."' ORDER BY `sa_app_start_date`";
                $sqlAppointmentsRes = imw_query($sqlAppointmentsQry);
                $sqlAppointmentsNumRow = imw_num_rows($sqlAppointmentsRes);
                if($sqlAppointmentsNumRow>0) {
                    $i=1;
                    while($sqlAppointmentsRow = imw_fetch_array($sqlAppointmentsRes)) {
                        $appt_date_of_surgery	   	= $sqlAppointmentsRow['sa_app_start_date'];
                        $sa_doctor_id	   			= $sqlAppointmentsRow['sa_doctor_id']; //Surgeon ID
                        $surgeonNameQry = "select * from users where id='".$sa_doctor_id."'";
                        $surgeonNameRes = imw_query($surgeonNameQry) or die($surgeonNameQry.imw_error());
                        $surgeonNameNumRow = imw_num_rows($surgeonNameRes);
                                
                        //START INITIALIZE VARIABLES OF SURGEON
                        $surgeonFirstName    = '';
                        $surgeonMiddleName   = '';
                        $surgeonLastName     = '';
                        
                        //END INITIALIZE VARIABLES OF SURGEON
                        if($surgeonNameNumRow>0) {
                            $surgeonNameRow = imw_fetch_array($surgeonNameRes);
                            $surgeonFirstName    = $surgeonNameRow['fname']; //Surgeon First Name
                            $surgeonMiddleName   = $surgeonNameRow['mname']; //Surgeon Middle Name
                            $surgeonLastName     = $surgeonNameRow['lname']; //Surgeon Last Name
                            $surgeonNpi     	 = $surgeonNameRow['user_npi']; //Surgeon Last Name
                            
                        }
                        $sa_app_starttime  			= $sqlAppointmentsRow['sa_app_starttime']; //Surgery Time
                        //added by amit on 13-06-09 for synch with imwemr
                        $pickup_time = "";
                        if(isset($sqlAppointmentsRow['pick_up_time'])){
                            $pickup_time = addslashes($sqlAppointmentsRow['pick_up_time']);
                        }
                        $arrival_time = "";
                        if(isset($sqlAppointmentsRow['arrival_time'])){
                            $arrival_time = addslashes($sqlAppointmentsRow['arrival_time']);
                        }			
                        $procedureid	   			= $sqlAppointmentsRow['procedureid']; //ProcedureId
                        $sec_procedureid	=	'';
                        if(isset($sqlAppointmentsRow['sec_procedureid'])){
                            $sec_procedureid = addslashes($sqlAppointmentsRow['sec_procedureid']);
                        }
                        $ter_procedureid	=	'';
                        if(isset($sqlAppointmentsRow['tertiary_procedureid'])){
                            $ter_procedureid = addslashes($sqlAppointmentsRow['tertiary_procedureid']);
                        }
                        //$procedureNameQry = "select * from slot_procedures where id='".$procedureid."'";
                        $procedureNameQry = "SELECT `prim_proc`.`proc` AS 'pri_proc', `prim_acronym`.`acronym` AS 'pri_acronym', 
                                                `secd_proc`.`proc` AS 'sec_proc', `secd_acronym`.`acronym` AS 'sec_acronym',
                                                `tert_proc`.`proc` AS 'ter_proc', `tert_acronym`.`acronym` AS 'ter_acronym'
                                                FROM (
                                                    SELECT `proc` FROM `slot_procedures` WHERE `id` ='".$procedureid."'
                                                ) AS `prim_proc`,
                                                (
                                                    SELECT IF(count(`id`)=1,`acronym`,'') AS 'acronym' FROM `slot_procedures` WHERE `id` = '".$procedureid."'
                                                ) AS `prim_acronym`,
                                                (
                                                    SELECT IF(count(`id`)=1, `proc`,'') AS 'proc' FROM `slot_procedures` WHERE `id` = '".$sec_procedureid."'
                                                ) AS `secd_proc`,
                                                (
                                                    SELECT IF(count(`id`)=1, `acronym`,'') AS 'acronym' FROM `slot_procedures` WHERE `id` = '".$sec_procedureid."'
                                                ) AS `secd_acronym`,
                                                ( 
                                                    SELECT IF(count(`id`)=1, `proc`,'') AS 'proc' FROM `slot_procedures` WHERE `id` = '".$ter_procedureid."'
                                                ) AS `tert_proc`,
                                                (
                                                    SELECT IF(count(`id`)=1, `acronym`,'') AS 'acronym' FROM `slot_procedures` WHERE `id` = '".$ter_procedureid."'
                                                ) AS `tert_acronym`
                                                ";
                                                
                        $procedureNameRes = imw_query($procedureNameQry) or die(imw_error());
                        $procedureNameNumRow = imw_num_rows($procedureNameRes);
                        
                        $procedureName   = "";
                        $site = "";
                        $confSiteNo='';
                        //START CODE FOR SITE(SET THIS ON PRIORITY)
                        if(isset($sqlAppointmentsRow['procedure_site'])){
                            $site = strtolower(addslashes($sqlAppointmentsRow['procedure_site']));
                            if($site=='bilateral') 	{ $site='both';}
                            
                            if($site=='left') 		{ $confSiteNo=1;
                            }else if($site=='right'){ $confSiteNo=2;
                            }else if($site=='both') { $confSiteNo=3;
                            }
                            
                        }
                        //END CODE FOR SITE(SET THIS ON PRIORITY)
                        
                        $procedureBasedSite='';
                        $procedureBasedConfSiteNo='';
                        if($procedureNameNumRow>0) {
                            $procedureNameRow = imw_fetch_array($procedureNameRes);
                            
                            $procedureName    		= $procedureNameRow['pri_proc']; //Procedure Name
                            $procedureAcronym   	= addslashes($procedureNameRow['pri_acronym']); //Procedure Name Acronym
                            $secProcedureName    	= $procedureNameRow['sec_proc']; //Secondary Procedure Name
                            $secProcedureAcronym 	= addslashes($procedureNameRow['sec_acronym']); //Secondary Procedure Acronym
                            $terProcedureName    	= $procedureNameRow['ter_proc']; //Tertiary Procedure Name
                            $terProcedureAcronym 	= addslashes($procedureNameRow['ter_acronym']); //Tertiary Procedure Acronym
                            //$site = "";
                            $siteTemp = substr(trim($procedureName),-2,2); //READ LAST TWO CHARACTERS OF PRIMARY PROCEDURE EXCLUDING SPACE
                            if($siteTemp=='OS') {
                                $procedureBasedSite = 'left';
                                $procedureBasedConfSiteNo=1;
                                $procedureName = trim(str_replace($siteTemp,'',$procedureName)); //REMOVE LAST TWO CHARACTERS EXCLUDING SPACE
                            }else if($siteTemp=='OD') {
                                $procedureBasedSite = 'right';
                                $procedureBasedConfSiteNo=2;
                                $procedureName = trim(str_replace($siteTemp,'',$procedureName)); //REMOVE LAST TWO CHARACTERS EXCLUDING SPACE
                            }else if($siteTemp=='OU') {
                                $procedureBasedSite = 'both';
                                $procedureBasedConfSiteNo=3;
                                $procedureName 	= trim(str_replace($siteTemp,'',$procedureName)); //REMOVE LAST TWO CHARACTERS EXCLUDING SPACE
                            }
                            $procedureName 		= addslashes($procedureName);
                            
                        }
                        
                        if($site=='') { //IF SITE IS STILL BLANK THEN SET IT ON PROCEDURE BASED	
                            $site 		= $procedureBasedSite;
                            $confSiteNo = $procedureBasedConfSiteNo;
                        } 
                        
                        $patient_status_id		= $sqlAppointmentsRow['sa_patient_app_status_id']; //PATIENT STATUS ID
                        
                        //SET PATIENT STATUS
                        $patient_status='Scheduled';
                        if($patient_status_id=='18') {
                            $patient_status='Canceled';
                        }
                        //END SET PATIENT STATUS
                        $comment					= stripslashes($sqlAppointmentsRow['sa_comments']); //Comment
                        $iDocSchAthenaId			= stripslashes($sqlAppointmentsRow['athenaID']); //Comment
                            
                        /*Modification by Pankaj Raturi*/
                        if($procedureName!=""){
                            
                            $data = array();
                            $data['setId_PR1'] = $i;
                            $data['procedure_code'] = $procedureAcronym;
                            $data['procedure_description'] = $procedureName;
                            $data['surgeon']['id'] = $surgeonNpi;
                            $data['surgeon']['family_name'] = $surgeonLastName;
                            $data['surgeon']['given_name'] = $surgeonFirstName;
                            $data['surgeon']['middle_name'] = $surgeonMiddleName;
                            $data['date_time'] = $appt_date_of_surgery.$sa_app_starttime;
                            /*$data['procedure_identifier'] = "waiting";*/
                            $this->addSegment("PR1", $data);
                            
                            $data = array();
                            $data['setId_ZPR'] = $i;
                            $data['pickup_time'] = $pickup_time;
                            $data['arrival_time'] = $arrival_time;
                            $data['patient_status'] = $patient_status;
                            $data['site'] = $site;
                            $data['idoc_sch_athena_id'] = $iDocSchAthenaId;
                            $data['comment'] = $comment;
                            $data['patient_secondary_procedure'] = $secProcedureName;
                            $data['patient_secondary_acroynm'] = $secProcedureAcronym;
                            $data['patient_tertiary_procedure'] = $terProcedureName;
                            $data['patient_tertiary_acroynm'] = $terProcedureAcronym;
                            $this->addSegment("ZPR", $data);
                            $i++;
                        }
                    }
                }
                break;
            case "SCH":
                if(!isset($this->PD['schid'])){break;}
                $sql = "SELECT `appt`.`id`, CONCAT(DATE_FORMAT(`appt`.`sa_app_start_date`, '%Y%m%d'),DATE_FORMAT(`appt`.`sa_app_starttime`,'%H%i%s')) AS 'start_date_time', CONCAT(DATE_FORMAT(`appt`.`sa_app_end_date`, '%Y%m%d'),DATE_FORMAT(`appt`.`sa_app_endtime`,'%H%i%s')) AS 'end_date_time', `proc`.`acronym`, `proc`.`proc`, TIMESTAMPDIFF(minute, `appt`.`sa_app_starttime`, `appt`.`sa_app_endtime`) AS 'duration', `appt`.`athenaID` AS 'facility_athenaId', `appt`.`sa_facility_id`  FROM `schedule_appointments` `appt` INNER JOIN `slot_procedures` `proc` ON(`appt`.`procedureid`=`proc`.`id`) WHERE `appt`.`id`='".$this->PD['schid']."' LIMIT 1";
                
                if($sqlD = imw_query($sql)){
                    if(imw_num_rows($sqlD)){
                        $row = imw_fetch_assoc($sqlD);
                        $data = array();
                        $data['placer_appointment_id']['entity_identifier']=$row['id'];
                        $data['filler_appointment_id']['entity_identifier']=$row['id'];
                        $data['appointment_reason']['identifier']=$row['acronym'];
                        $data['appointment_reason']['text']=$row['proc'];
                        $data['appointment_type'] = "Normal"; /*Default Value for appointment Type*/
                        $data['appointment_duration']=$row['duration'];
                        $data['appointment_duration_units']="minutes";
                        $data['appointment_timing_quantity']['start_date_time']=$row['start_date_time'];
                        $data['appointment_timing_quantity']['end_date_time']=$row['end_date_time'];
                        $data['filler_status_code']=$this->fillStatus;
                        $this->addSegment("SCH", $data);
                        
                        $data = array();
                        $facility = $this->getFacilityName($row['sa_facility_id']);
                        $data['location_resource_id']['point_of_care'] = $facility['athenaID'];
                        $data['location_resource_id']['room'] = $facility['name'];
                        $data['start_date_time'] = $row['start_date_time'];
                        $data['duration'] = $row['duration'];
                        $data['duration_units'] = "minutes";
                        $this->addSegment("AIL", $data);
                    }
                }
                break;
            case "GT1":
                $sqlGurantor = "SELECT * FROM `resp_party` WHERE `patient_id`='".$this->PD['id']."'";
                $gurantor = imw_query($sqlGurantor);
                if($gurantor){
                    if(imw_num_rows($gurantor)>0){
                        $i = 1;
                        while($row = imw_fetch_assoc($gurantor)){
                            $data = array();
                            $data['setId_GT1'] = $i++;
                            $data['name']['family_name'] = $row['lname'];
                            $data['name']['given_name'] = $row['fname'];
                            $data['name']['middle_name'] = $row['mname'];
                            $data['name']['name_suffix'] = $row['suffix'];
                            $data['name']['name_prefix'] = $row['title'];
                            $data['address']['street'] = $row['address'];
                            $data['address']['other_designation'] = $row['address2'];
                            $data['address']['city'] = $row['city'];
                            $data['address']['state_province'] = $row['state'];
                            $data['address']['zip'] = $row['zip'].$row['zip_ext'];
                            $data['address']['country'] = $row['country'];
                            $data['phone_home']['telephone_number']['home'] = str_replace(" ", "", $row['home_ph']);
                            $data['phone_home']['telephone_number']['cell'] = str_replace(" ", "", $row['mobile']);
                            $data['phone_home']['email'] = $row['email'];
                            $data['phone_business']['telephone_number'] = str_replace(" ", "", $row['work_ph']);
                            $data['dob'] = str_replace(" ", "", $row['dob']);
                            $data['sex'] = $row['sex'];
                            $data['relationship'] = $row['relation'];
                            $data['ssn'] = $row['ss'];
                            $data['marital_status'] = $row['marital'];
                            
                            $this->addSegment("GT1", $data);
                        }
                    }
                }
                break;
            case "IN1":
                $where = "";
                /*if($this->insCaseId !=""){$where = " AND `insData`.`ins_caseid`='".$this->insCaseId."'";}
                else{$where = "";}*/
                $strQryGetPatientInsurenceData="SELECT `insData`.* FROM `insurance_data` `insData` INNER JOIN `insurance_case` `insCase` ON (`insCase`.`ins_caseid`=`insData`.`ins_caseid` AND `insCase`.`case_status`='Open') LEFT JOIN `patient_auth` `ptAuth` ON (`ptAuth`.`ins_data_id`=`insData`.`id` AND `ptAuth`.`patient_id`=`insData`.`pid`) WHERE `insData`.`pid`='".$this->PD['id']."' AND `insData`.`type` IN('primary', 'secondary', 'tertiary') AND `insData`.`actInsComp`='1'".$where." ORDER BY `insData`.`type`";
                $rsQryGetPatientInsurenceData = imw_query($strQryGetPatientInsurenceData);
                if(imw_num_rows($rsQryGetPatientInsurenceData)<=0){
                $strQryGetPatientInsurenceData="SELECT `insData`.* FROM `insurance_data` `insData` INNER JOIN `insurance_case` `insCase` ON (`insCase`.`ins_caseid`=`insData`.`ins_caseid` AND `insCase`.`case_status`='Open' AND `insCase`.`ins_case_type`='".$this->getDefaultInsCase()."') LEFT JOIN `patient_auth` `ptAuth` ON (`ptAuth`.`ins_data_id`=`insData`.`id` AND `ptAuth`.`patient_id`= `insData`.`pid`) WHERE `insData`.`pid`='".$this->PD['id']."' AND `insData`.`type` IN('primary', 'secondary', 'tertiary') AND `insData`.`actInsComp`='1'".$where." ORDER BY `insData`.`type`";
                    $rsQryGetPatientInsurenceData = imw_query($strQryGetPatientInsurenceData);
                }
                if($rsQryGetPatientInsurenceData){
                    if(imw_num_rows($rsQryGetPatientInsurenceData)>0){
                        $i = 1;
                        while ($row = imw_fetch_array($rsQryGetPatientInsurenceData)) {
                            $provider = $row['provider'];
                            $insCompQry = "select id, name, contact_address, City, State, Zip, zip_ext, phone, contact_name from insurance_companies where id = $provider";
                            $rsInsCompQry = imw_query($insCompQry);	
                            $numRowrInsCompQry = imw_num_rows($rsInsCompQry);				
                            if($numRowrInsCompQry > 0){
                                $insDetails = imw_fetch_array($rsInsCompQry);
                                
                                $providerId     =	$insDetails['id'];
                                $providerName	=	$insDetails['name'];
                                $ins_contact 	=	$insDetails['contact_name'];
                                $ins_phone		=	$insDetails['phone'];
                                $insAddress		=	$insDetails['contact_address'];
                                $insCity		=	$insDetails['City'];
                                $insState		=	$insDetails['State'];
                                $insZip			=	$insDetails['Zip'].$insDetails['zip_ext'];
                            }
                            
                            $ptAuthQry = "select auth_name from patient_auth where ins_data_id = '".$row['id']."' AND auth_status='0'";
                            $ptAuthRes = imw_query($ptAuthQry);	
                            $ptAuthNumRow = imw_num_rows($ptAuthRes);				
                            $authorization_number='';
                            $authNumberArr=array();
                            if($ptAuthNumRow > 0){
                                while($ptAuthRow = imw_fetch_array($ptAuthRes)) {
                                    $authNumberArr[] = $ptAuthRow['auth_name'];
                                }
                                $authorization_number = implode(", ",$authNumberArr);
                            }
                            
                            $data = array();
                            $data['setId_IN1'] = $i;
                            
                            if(strtolower($GLOBALS["LOCAL_SERVER"])=='uram')
                                $data['plan_id'] = $providerId;
                            
                            $data['company_name'] = $providerName;
                            $data['company_address']['street'] = $insAddress;
                            $data['company_address']['city'] = $insCity;
                            $data['company_address']['state_province'] = $insState;
                            $data['company_address']['zip'] = $insZip;
                            $data['insurance_company_contact_person'] = $ins_contact;
                            $data['company_phone'] = $ins_phone;
                            $data['company_group_number'] = $row['group_number'];
                            $data['plan_effective_date'] = str_replace(" ", "", $row['effective_date']);
                            $data['plan_expire_date'] = str_replace(" ", "", $row['expiration_date']);
                            $data['name_insured']['family_name'] = $row['subscriber_lname'];
                            $data['name_insured']['given_name'] = $row['subscriber_fname'];
                            $data['name_insured']['middle_name'] = $row['subscriber_mname'];
                            $data['relationship'] = $row['subscriber_relationship'];
                            $data['insured_dob'] = $row['subscriber_DOB'];
                            $data['insured_address']['street'] = $row['subscriber_street'];
                            $data['insured_address']['other_designation'] = $row['subscriber_street_2'];
                            $data['insured_address']['city'] = $row['subscriber_city'];
                            $data['insured_address']['state_province'] = $row['subscriber_state'];
                            $data['insured_address']['zip'] = $row['subscriber_postal_code'].$row['zip_ext'];
                            $data['company_plan_code'] = $row['plan_name'];
                            $data['policy_number'] = $row['policy_number'];
                            $data['sex'] = $row['subscriber_sex'];
                            $this->addSegment("IN1", $data, $msgType);
                            
                            $data = array();
                            $data['setId_ZIN'] = $i;
                            $data['insurance_type'] = $row['type'];
                            $data['insured_phone_home']['telephone_number']['home'] = str_replace(" ", "", $row['subscriber_phone']);
                            $data['insured_phone_home']['telephone_number']['cell'] = str_replace(" ", "", $row['subscriber_mobile']);
                            $data['insured_phone_business']['telephone_number'] = str_replace(" ", "", $row['subscriber_biz_phone']);
                            $data['ssn'] = $row['subscriber_ss'];
                            $data['comment'] = $row['comments'];
                            $data['copay'] = $row['copay'];
                            $data['insurance_case_id'] = $row['ins_caseid'];
                            $data['referer_required'] = $row['referal_required'];
                            $data['Sec_HCFA'] = $row['Sec_HCFA'];
                            $data['new_comment_date'] = str_replace(" ", "", $row['newComDate']);
                            $data['actInsComp'] = $row['actInsComp'];
                            $data['actInsCompDate'] = str_replace(" ", "", $row['actInsCompDate']);
                            $data['claims_adjuster_name'] = $row['claims_adjustername'];
                            $data['claims_adjuster_phone'] = $row['claims_adjusterphone'];
                            $data['cardscan_date'] = str_replace(" ", "", $row['cardscan_date']);
                            $data['cardscan_comments'] = $row['cardscan_comments'];
                            $data['cardscan_operator_id'] = $row['cardscan_operator'];
                            $data['cardscan1_datetime'] = str_replace(" ", "", $row['cardscan1_datetime']);
                            $data['self_pay_provider'] = $row['self_pay_provider'];
                            $data['authorization_number'] = $authorization_number;
                            
                            if($this->sendInsDocs===true){
                                $data['scan_card_1'] = $row['scan_card'];
                                $data['scan_labe_l'] = $row['scan_label'];
                                $data['scan_card_2'] = $row['scan_card2'];
                                $data['scan_label_2'] = $row['scan_label2'];
                                
                                $ins_dataFolder = dirname(__FILE__).'/../../interface/main/uploaddir';
                                $scadData1='';
                                $scadData2='';
                                if($row['scan_card']!="") {
                                    $docPath = $ins_dataFolder.$row['scan_card'];
                                    $scadData1 = $this->encode_file($docPath);
                                }
                                if($row['scan_card2']!=""){
                                    $docPath = $ins_dataFolder.$row['scan_card2'];
                                    $scadData2 = $this->encode_file($docPath);
                                }
                                $data['scan_card_1_data'] = $scadData1;
                                $data['scan_card_2_data'] = $scadData2;
                            }
                            $data['paymt_auth'] = "1";
                            $data['sign_on_file'] = "1";
                            $this->addSegment("ZIN", $data, $msgType);
                            $i++;
                        }
                    }
                }
                break;
        }
    }
    
    public function log_message($insdt, $id="", $name="", $zeissTest=""){
        if($zeissTest==""){
            $zeissTest = $this->msgtype;
        }
        $insdt = strtoupper($insdt);
        $message = $this->getMessage(0);
        foreach($message as $msg){
            switch($insdt){
                case "ZEISS_FORUM":
                    $sql = "INSERT INTO `hl7_sent_forum`(`patient_id`, `msg`, `msg_type`, `test_id`, `test_name`, `send_to`, `saved_on`, `operator`) VALUES('".$this->PD['id']."', '".addslashes($msg)."', '".$zeissTest."', '".$id."', '".$name."', 'ZEISS', NOW(), '".$this->authorId."')";
                    $resp = imw_query($sql);
                    if($resp){
                        $logFile = $this->hl7FlagPath();
						$logFile .= DIRECTORY_SEPARATOR.'senderCheckZeissDB.log';
						file_put_contents($logFile, '1');
                    }
                    break;
                case "ATHENA_SIU":
                    //$message = $this->getMessage();
                    $sql = "INSERT INTO `hl7_sent`(`patient_id`, `msg`, `msg_type`, `saved_on`, `operator`) VALUES('".$this->PD['id']."', '".addslashes($msg)."', '".$this->msgtype."', '".date('Y-m-d H:i:s')."', '".$this->authorId."')";
                    $resp = imw_query($sql);
                    if($resp){
                        $msg = addslashes($msg);
                        $new_id = imw_insert_id();
                        if(constant('OUTBOUND_HL7_DIR')!=''){
                            if(is_dir(constant('OUTBOUND_HL7_DIR')) && file_exists(constant('OUTBOUND_HL7_DIR'))){
                                $f = file_put_contents(constant('OUTBOUND_HL7_DIR').'/'.$new_id.'.txt',stripslashes($msg));
                                if($f){
                                    imw_query("UPDATE `hl7_sent` SET `sent`=1, `sent_on`='".date('Y-m-d H:i:s')."', `status_text`='Sent to OUTBOUND_HL7_DIR' WHERE `id`='$new_id'");
                                }
                            }
                        }
                    }
                    break;
            }
        }
    }
    
    /*Function to delete any segment from hl7 message array crated through addSegmetn()*/
    public function skipSegment($msg, $segment){
        if($msg!="" && $segment!=""){
            if(isset($this->message[$msg])){unset($this->message[$msg][$segment]);}
            else{unset($this->message[$segment]);}
        }
    }
    
    /*Set Trigger Event & Structure*/
    public function setTrigger($msg, $trigger){
        switch($msg){
            case "SIU":
                if($trigger=="0"){$this->msgtypes['SIU']['trigger_event']="S12";$this->msgtype="book_appointemnt";$this->fillStatus="Booked";}
                elseif($trigger=="18"){$this->msgtypes['SIU']['trigger_event']="S15";$this->msgtype="cancel_appointemnt";$this->fillStatus="Cancelled";}
                elseif($trigger=="202"){$this->msgtypes['SIU']['trigger_event']="S13";$this->msgtype="reschedule_appointemnt";$this->fillStatus="Rescheduled";}
                elseif($trigger=="203"){$this->msgtypes['SIU']['trigger_event']="S17";$this->msgtype="delete_appointemnt";$this->fillStatus="Deleted";}
                elseif($trigger=="13"){$this->msgtypes['SIU']['trigger_event']="S14";$this->msgtype="checkIn_appointment";$this->fillStatus="Arrived";}
                elseif($trigger=="11"){$this->msgtypes['SIU']['trigger_event']="S14";$this->msgtype="checkOut_appointment";$this->fillStatus="Complete";}
                else{$this->msgtypes['SIU']['trigger_event']="S14";$this->fillStatus="";}
                break;
        }
    }
    
    private function getFacilityName($fid){
        $facility = "";
        if($fid!=""){
            $q = "SELECT `name`, `athenaID` FROM `facility` WHERE `id`='".$fid."'";
            $sqlD = imw_query($q);
            if($sqlD && imw_num_rows($sqlD)>0){
                $facility = imw_fetch_assoc($sqlD);
                //$facility['name'] = $facility['name'];
            }
        }
        return($facility);
    }
    
    public function setReceivingFacility($app="", $facility=""){
        $this->receiving['application'] = $app;
        $this->receiving['facility'] = $facility;
    }
    
    private function getDefaultInsCase(){
        if($this->defaultInsCaseId==""){
            $defaultCaseTypeQry 	= "SELECT case_id, case_name FROM insurance_case_types WHERE normal = '1'";
            $defaultCaseTypeRes 	= imw_query($defaultCaseTypeQry);
            if($defaultCaseTypeRes){
                if(imw_num_rows($defaultCaseTypeRes)>0){
                    /*$defaultCaseTypeNumRow 	= imw_num_rows($defaultCaseTypeRes);*/
                    $defaultCaseTypeRow = imw_fetch_array($defaultCaseTypeRes);
                    $this->defaultInsCaseId = $defaultCaseTypeRow['case_id'];
                    /*$defaultCaseName 	= $defaultCaseTypeRow['case_name'];*/
                }
            }
        }
        return($this->defaultInsCaseId);
    }
    
    /*Add Event Segment*/
    public function addEVN($EVN){
        $data = array();
        $data['event_typeCode'] = trim($EVN);
        $data['recorded_daTime'] = date("YmdHis");
        $data['operatorId'] = strtoupper($_SESSION['authUser']);
        $this->addSegment("EVN", $data);
    }
}
?>