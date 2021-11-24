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
 *File: hl7FeedData.php
 *Puppose: Feed Data to hl7 message generation class
 *Access Type: Include
 */
require_once( dirname(__FILE__).'/hl7Create.php' );

class hl7FeedData extends hl7Create{
    
    private $contacthlId=1;
    public $PD = array();
    public $msgtype, $fillStatus;
    /*private $insCaseId = ""; /*Insurance Case Id for IN1 segments in SIU messages for URAM*/
    private $defaultInsCaseId = "";
    private $sendInsDocs=false;
    
    
    public function __construct(){
        
        parent::__construct();
		$this->setReceivingFacility();
		$this->setSendingFacility();
        if(constant("HL7_SIU_GENERATION")==true){
            $this->msgtypes['SIU']['msg_structure']="";
        }
    }
    
    public function insertSegment($segm, $msgType="", $options=array()){
        
        switch($segm){
            case "PID":
                    $sql = "SELECT * FROM `patient_data` WHERE `id` ='".$this->PD['id']."'";
                    $sqlD = imw_query($sql);
                    if($sqlD){
                        if(imw_num_rows($sqlD)>0){
                            $row = imw_fetch_assoc($sqlD);
							if($row['DOB']=='0000-00-00') $row['DOB'] = '';
							 
                            $data = array();
                            if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('visionfirst','ohioeye','berkeleyeye','utaheye','eyelasikcenter','cumberlandvalleyretina'))){
								$data['setId_PID']	=	'0001';
								if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('ohioeye','berkeleyeye','utaheye','cumberlandvalleyretina'))){
									$data['patient_external_id']['ioPtId'] = $row['pid'];
								}
								$data['patient_external_id']['ioPtWtId'] = $row['pid'];
							}else{
								$data['setId_PID']	=	'1';
							}
							$data['patient_internal_id']	= $row['pid'];
							if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('islandeye','valleyeye'))){
								$data['setId_PID']	=	'1';
								$data['patient_external_id'] = $row['pid'];//$row['External_MRN_2'];
								$data['patient_internal_id'] = $row['External_MRN_2'];
							}
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

							if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('valleyeye'))){
								$temp_country_mapping_arr = array();
								$temp_country_mapping_arr['US'] = '5D1DFBDD-5D79-467D-AF80-69E50FD6CDF7';
								$temp_country_mapping_arr['USA'] = '2A8D792E-3B90-4EBA-8B18-ECA8F05CEE03';
								$temp_csv_data_file = dirname(__FILE__).'/../old/valleyeye_county.csv';
								$temp_csv_fp = fopen($temp_csv_data_file,"r");
								$temp_county_mapping_arr = array();
								while($temp_csv_data = fgetcsv($temp_csv_fp)){
									$temp_county_mapping_arr[ucwords($temp_csv_data[0])]		= ucwords($temp_csv_data[1]);					
								}
								$data['patient_address']['country'] = $temp_country_mapping_arr[strtoupper($row['country_code'])];
								$data['patient_address']['county'] = $temp_county_mapping_arr[strtoupper($row['county'])];
								$data['country_code']	=	'';
							}else{
	                            $data['patient_address']['country'] = $row['country_code'];
    	                        $data['patient_address']['county'] = $row['county'];
								$data['country_code']	=	$row['country_code'];
							}
							
                            if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('visionfirst','eyelasikcenter'))){
								$data['phone_home']['telephone_number'] = str_replace(array(' ','-'), "", $row['phone_home']);
								$data['phone_home']['telComm_useCode'] = str_replace(array(' ','-'), "", $row['phone_cell']);
							}else if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('marioneye'))){
								$structure = array("phone_home"=>array("one"=>'', "two"=>'', "three"=>'', "four"=>'', "five"=>'', "six"=>'', "seven"=>'', "eight"=>'', "nine"=>'', ));
								$this->altersegment("PID", $structure);
								$data['phone_home']['one'] = str_replace(array(' ','-'), "", $row['phone_home']);
								$data['phone_home']['four'] = $row['email'];
								$data['phone_home']['nine'] = str_replace(array(' ','-'), "", $row['phone_cell']);
							}else{
								$data['phone_home']['telephone_number']['home'] = str_replace(" ", "", $row['phone_home']);
    	                        $data['phone_home']['telephone_number']['cell'] = str_replace(" ", "", $row['phone_cell']);
							}
							
							if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('marioneye'))){
								$data['phone_home']['email']  = '';						
							}else{
								$data['phone_home']['email']  = $row['email'];	
							}
                            $data['phone_business']['telephone_number'] = str_replace(" ", "", $row['phone_biz']);
                            $data['primary_language'] = $row['language'];
                            $data['marital_status'] = $row['status'];
							if($row['ss']!='000-00-0000' && $row['ss']!='000000000'){
	                            $data['ssn'] = $row['ss'];
							}
                            $data['driver_licence'] = $row['driving_licence'];
                            $data['ethnic_group'] = $row['ethnicity'];
                            
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
            case "NK1EMC":
					/*Emergency Contact*/
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
            case "NK1":
					/*Family Information*/
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
                    $sql = "SELECT `u`.`user_npi` AS 'ap_code', `u`.`id` AS 'ap_id', `u`.`lname` AS 'ap_lname', `u`.`fname` AS 'ap_fname', `u`.`mname` AS 'ap_mname', `rf2`.`NPI` AS 'rp_code', `rf2`.`physician_Reffer_id` AS 'rp_id', `rf2`.`LastName` AS 'rp_lname', `rf2`.`FirstName` AS 'rp_fname', `rf2`.`MiddleName` AS 'rp_mname', `rf`.`NPI` AS 'adp_code', `rf`.`physician_Reffer_id` AS 'adp_id', `rf`.`LastName` AS 'adp_lname', `rf`.`FirstName` AS 'adp_fname', `rf`.`MiddleName` AS 'adp_mname' FROM `patient_data` `pd` LEFT JOIN `refferphysician` `rf` ON (`rf`.`physician_Reffer_id` = `pd`.`primary_care_phy_id`) LEFT JOIN `refferphysician` `rf2` ON (`rf2`.`physician_Reffer_id` = `pd`.`primary_care_id`) LEFT JOIN `users` `u` ON (`u`.`id` = `pd`.`providerID`) WHERE `pd`.`pid`='".$this->PD['id']."'";
                }
                else{ /*PV1 segment for ADT messages- Phy. to witch appointment is booked is added as "Attending Doctor" Schedule-id/appointment-id is used to query*/
                    $sql = "SELECT `u`.`user_npi` AS 'ap_code', `u`.`id` AS 'ap_id', `u`.`lname` AS 'ap_lname', `u`.`fname` AS 'ap_fname', `u`.`mname` AS 'ap_mname', `rf2`.`NPI` AS 'rp_code', `rf2`.`physician_Reffer_id` AS 'rp_id', `rf2`.`LastName` AS 'rp_lname', `rf2`.`FirstName` AS 'rp_fname', `rf2`.`MiddleName` AS 'rp_mname', `rf`.`NPI` AS 'adp_code', `rf`.`physician_Reffer_id` AS 'adp_id', `rf`.`LastName` AS 'adp_lname', `rf`.`FirstName` AS 'adp_fname', `rf`.`MiddleName` AS 'adp_mname', CONCAT(DATE_FORMAT(`appt`.`sa_app_start_date`,'%Y%m%d'),DATE_FORMAT(`appt`.`sa_app_starttime`,'%H%i%s')) AS 'visit_date_time', `appt`.`sa_facility_id`, `appt`.`id` AS 'appt_id', `appt`.`case_type_id` AS 'insurance_case_id',`appt`.`sa_patient_id` as sa_patient_id  FROM `schedule_appointments` `appt` INNER JOIN `patient_data` `pd` ON(`appt`.`sa_patient_id`=`pd`.`pid`) LEFT JOIN `refferphysician` `rf` ON (`rf`.`physician_Reffer_id` = `pd`.`primary_care_phy_id`) LEFT JOIN `refferphysician` `rf2` ON (`rf2`.`physician_Reffer_id` = `pd`.`primary_care_id`) LEFT JOIN `users` `u` ON (`u`.`id` = `appt`.`sa_doctor_id`) WHERE `appt`.`id`='".$this->PD['schid']."'";
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
								if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('visionfirst','eyelasikcenter'))){
									$data['assigned_patient_location']['point_of_care'] = $facility['id'];
								}else{
									$data['assigned_patient_location']['point_of_care'] = $facility['athenaID'];
								}
                                $data['assigned_patient_location']['room'] = $facility['name'];
                            }
//                            $data['attending_doctor']['id'] = $row['ap_code'];
							if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('islandeye'))){
								$data['attending_doctor']['id'] = '1629152053';
							}else if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('valleyeye'))){
								$data['attending_doctor']['id'] = '1437104718';
							}else{
								$data['attending_doctor']['id'] = $row['ap_id'];
							}
                            $data['attending_doctor']['family_name'] = $row['ap_lname'];
                            $data['attending_doctor']['given_name'] = $row['ap_fname'];
                            $data['attending_doctor']['middle_name'] = $row['ap_mname'];
							
//                            $data['referring_doctor']['id'] = $row['rp_code'];
							$data['referring_doctor']['id'] = $row['rp_id'];
                            $data['referring_doctor']['family_name'] = $row['rp_lname'];
                            $data['referring_doctor']['given_name'] = $row['rp_fname'];
                            $data['referring_doctor']['middle_name'] = $row['rp_mname'];
							
//                            $data['admitting_doctor']['id'] = $row['adp_code'];
							$data['admitting_doctor']['id'] = $row['adp_id'];
                            $data['admitting_doctor']['family_name'] = $row['adp_lname'];
                            $data['admitting_doctor']['given_name'] = $row['adp_fname'];
                            $data['admitting_doctor']['middle_name'] = $row['adp_mname'];
                            if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('ohioeye','berkeleyeye','utaheye','cumberlandvalleyretina'))){
								$data['visit_number'] = $this->PD['schid'];
							}
							
							if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('islandeye','valleyeye')) && strtolower($this->fillStatus)=='arrived'){
								$data['admit_date_time'] = date('YmdHis');
							}
							
							if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('eyelasikcenter'))){
								$TotBalptRes = imw_query("SELECT SUM(totalBalance) AS totalBalance FROM patient_charge_list WHERE del_status='0' AND patient_id = '".$row['sa_patient_id']."' GROUP BY patient_id");
								$TotBalptRS = imw_fetch_assoc($TotBalptRes);
								$data['current_patient_balance'] = $TotBalptRS['totalBalance'];
							}
							
							$this->addSegment("PV1", $data, $msgType);
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
                $sql = "SELECT `appt`.`id`, CONCAT(DATE_FORMAT(`appt`.`sa_app_start_date`, '%Y%m%d'),DATE_FORMAT(`appt`.`sa_app_starttime`,'%H%i%s')) AS 'start_date_time', CONCAT(DATE_FORMAT(`appt`.`sa_app_end_date`, '%Y%m%d'),DATE_FORMAT(`appt`.`sa_app_endtime`,'%H%i%s')) AS 'end_date_time', `proc`.`acronym`, `proc`.`proc`, TIMESTAMPDIFF(minute, CONCAT( `appt`.`sa_app_start_date`, ' ',`appt`.`sa_app_starttime`), CONCAT( `appt`.`sa_app_start_date`, ' ',`appt`.`sa_app_endtime`)) AS 'duration', `appt`.`athenaID` AS 'facility_athenaId', `appt`.`sa_facility_id`, `appt`.`procedureid` AS appt_procedureid, `appt`.`sa_doctor_id` AS 'Appt_Doctor_id', `appt`.`hl7_sender_fac_id` AS 'MISC_external_id',sa_comments as appt_comments_text  FROM `schedule_appointments` `appt` INNER JOIN `slot_procedures` `proc` ON(`appt`.`procedureid`=`proc`.`id`) WHERE `appt`.`id`='".$this->PD['schid']."' LIMIT 1";
                
                if($sqlD = imw_query($sql)){
                    if(imw_num_rows($sqlD)){
                        $row = imw_fetch_assoc($sqlD);
                        $data = array();
                        $data['placer_appointment_id']['entity_identifier']=$row['id'];
                        $data['filler_appointment_id']['entity_identifier']=$row['id'];
						if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('islandeye','valleyeye'))){
							if(!empty($row['MISC_external_id'])){
								$data['event_reason']=$row['MISC_external_id'];
							}else{
								$data['event_reason']=$row['acronym'];	
							}
							if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('valleyeye'))){
								$data['event_reason']='PRO';		
							}
						}
						
						if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('visionfirst','eyelasikcenter'))){					
	                        $data['appointment_reason']['identifier']=$row['appt_procedureid'];
						}else{
							if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('islandeye','valleyeye'))){
								$structure = array("appointment_reason"=>'');
								$this->altersegment("SCH", $structure);
								$data['appointment_reason']=trim(substr($row['acronym'],0,35));
							}else{
								$data['appointment_reason']['identifier']=$row['acronym'];
							}
						}
                        if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('islandeye','valleyeye'))){
						//	$data['appointment_reason']['text']=$row['appt_comments_text'];
						}else{
							$data['appointment_reason']['text']=$row['proc'];
						}
						//$data['appointment_type'] = "Normal"; /*Default Value for appointment Type*/
						if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('visionfirst','eyelasikcenter'))){					
	                        $data['appointment_type']=$row['appt_procedureid'];
						}else{
							if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('islandeye','valleyeye'))){
								$data['appointment_type']=trim(substr($row['acronym'],0,35));
							}else{
								$data['appointment_type']=$row['acronym'];
							}
						}
						
                        $data['appointment_duration']=$row['duration'];
                        $data['appointment_duration_units']="minutes";
                        $data['appointment_timing_quantity']['start_date_time']=$row['start_date_time'];
                        $data['appointment_timing_quantity']['end_date_time']=$row['end_date_time'];
                        if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('islandeye','valleyeye')) && strtolower($this->fillStatus)=='arrived'){
							 $data['filler_status_code']='KEPT';
						}else{
							$data['filler_status_code']=$this->fillStatus;	
						}
						
                        $this->addSegment("SCH", $data);
                        
						//---ADDING LOCATION SEGMENT
                        $data = array();
                        $facility = $this->getFacilityName($row['sa_facility_id']);
						if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('visionfirst','ohioeye','berkeleyeye','utaheye','eyelasikcenter','cumberlandvalleyretina'))){
						    $data['location_resource_id']['point_of_care'] = $facility['id'];
						}else{
	                        $data['location_resource_id']['point_of_care'] = $facility['athenaID'];
						}
                        $data['location_resource_id']['room'] = $facility['name'];
						
						if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('islandeye','valleyeye'))){
							$data['location_resource_id']	= $facility['athenaID'];
							$data['location_type_ail']['type_id']		= '';
							$data['location_type_ail']['type_name']		= 'Place';
						}
						$data['setId_AIL'] = '1';						
                        $data['start_date_time'] = $row['start_date_time'];
                        $data['duration'] = $row['duration'];
                        $data['duration_units'] = "minutes";
						if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('islandeye','valleyeye')) && strtolower($this->fillStatus)=='arrived'){
							$data['filler_status_code'] = 'KEPT';
						}
						$this->addSegment("AIL", $data);
						
						//-- ADDING PROVIDER SEGMENT
						$data = array();
                        $ApptDoctor = $this->getDoctorDetails($row['Appt_Doctor_id']);
						if(is_array($ApptDoctor) && in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('islandeye','valleyeye'))){
							$data['setId_AIP'] = '1';
							
							if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('valleyeye'))){
								$data['personnel_resource_id']['id'] = '1437104718';
							}else{
								$data['personnel_resource_id']['id'] = '1629152053';
							}
							$data['personnel_resource_id']['lname'] = $ApptDoctor['lname'];
							$data['personnel_resource_id']['fname'] = $ApptDoctor['fname'];
							$data['personnel_resource_id']['mname'] = $ApptDoctor['mname'];
							
							$data['resource_role']['code']	= 'P';
							$data['resource_role']['value']	= 'Attending';
							if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('islandeye','valleyeye')) && strtolower($this->fillStatus)=='arrived'){
								$data['filler_status_code'] = 'KEPT';
							}
							$this->addSegment("AIP", $data);

							$data = array();
							$data['setId_AIP'] = '2';
							if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('islandeye'))){
								$data['personnel_resource_id']['id'] = $ApptDoctor['external_id'].'0002';
							}else{
								$data['personnel_resource_id']['id'] = $ApptDoctor['external_id'];	
							}
							$data['personnel_resource_id']['lname'] = $ApptDoctor['lname'];
							$data['personnel_resource_id']['fname'] = $ApptDoctor['fname'];
							$data['personnel_resource_id']['mname'] = $ApptDoctor['mname'];
							
							$data['resource_role']['code']	= 'S';
							$data['resource_role']['value']	= 'Resource';
							if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('islandeye','valleyeye')) && strtolower($this->fillStatus)=='arrived'){
								$data['filler_status_code'] = 'KEPT';
							}
							$this->addSegment("AIP", $data);
						}
						if($facility && in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('ohioeye','berkeleyeye','utaheye','cumberlandvalleyretina'))){
							$data = array();
							$data['setId_AIP'] = '1';
							$data['personnel_resource_id']['id'] = $ApptDoctor['user_npi'];//'1629152053';
							$data['personnel_resource_id']['lname'] = $ApptDoctor['lname'];
							$data['personnel_resource_id']['fname'] = $ApptDoctor['fname'];
							$data['personnel_resource_id']['mname'] = $ApptDoctor['mname'];
							
							$data['resource_role']['code']	= 'P';
							$data['resource_role']['value']	= 'Attending';
							$this->addSegment("AIP", $data);
							
							$data = array();
							$data['setId_LOC'] = '1';
							$data['office_adddress']['street1'] = $facility['street'];
							$data['office_adddress']['street2'] = '';
							$data['office_adddress']['city'] = $facility['city'];
							$data['office_adddress']['state'] = $facility['state'];
							$data['office_adddress']['zip'] = $facility['postal_code'].$facility['zip_ext'];
							$data['contactNumber']['phone']	= $facility['phone'];
							$data['contactNumber']['fax']	= $facility['fax'];
							$this->addSegment("LOC", $data);
						}
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
                $strQryGetPatientInsurenceData="SELECT `insData`.*, `insCaseType`.`case_name`
						FROM `insurance_data` `insData` 
						INNER JOIN `insurance_case` `insCase` 
							ON (
								`insCase`.`ins_caseid`=`insData`.`ins_caseid` AND
								`insCase`.`case_status`='Open'
							)
                        LEFT JOIN `insurance_case_types` `insCaseType`
                            ON (
                                `insCaseType`.`case_id` = `insCase`.`ins_case_type`
                            )
						WHERE
							`insData`.`pid`='".$this->PD['id']."' AND
							`insData`.`type` IN('primary', 'secondary', 'tertiary') AND
							`insData`.`actInsComp`='1' ".$where."
						ORDER BY `insData`.`type`";
				
                $rsQryGetPatientInsurenceData = imw_query($strQryGetPatientInsurenceData);
                if(imw_num_rows($rsQryGetPatientInsurenceData)<=0){
					$strQryGetPatientInsurenceData="SELECT `insData`.*, `insCaseType`.`case_name`
							FROM `insurance_data` `insData`
							INNER JOIN `insurance_case` `insCase`
								ON (
									`insCase`.`ins_caseid`=`insData`.`ins_caseid` AND
									`insCase`.`case_status`='Open' AND
									`insCase`.`ins_case_type`='".$this->getDefaultInsCase()."'
								)
                            LEFT JOIN `insurance_case_types` `insCaseType`
                                ON (
                                    `insCaseType`.`case_id` = `insCase`.`ins_case_type`
                                )
							WHERE
								`insData`.`pid`='".$this->PD['id']."' AND
								`insData`.`type` IN('primary', 'secondary', 'tertiary') AND
								`insData`.`actInsComp`='1' ".$where."
							ORDER BY `insData`.`type`";
                    $rsQryGetPatientInsurenceData = imw_query($strQryGetPatientInsurenceData);
                }
                if($rsQryGetPatientInsurenceData){
                    if(imw_num_rows($rsQryGetPatientInsurenceData)>0){
                        $i = 1;
                        while ($row = imw_fetch_array($rsQryGetPatientInsurenceData)) {
                            $provider = $row['provider'];
                            $insCompQry = "select id, name, in_house_code, contact_address, City, State, Zip, zip_ext, phone, contact_name from insurance_companies where id = $provider";
                            $rsInsCompQry = imw_query($insCompQry);	
                            $numRowrInsCompQry = imw_num_rows($rsInsCompQry);				
                            if($numRowrInsCompQry > 0){
                                $insDetails = imw_fetch_array($rsInsCompQry);
                                
                                $providerId     =	$insDetails['id'];
                                $inhousecode	=	$insDetails['in_house_code'];
								$providerName	=	$insDetails['name'];
                                $ins_contact 	=	$insDetails['contact_name'];
                                $ins_phone		=	str_replace('-','',$insDetails['phone']);
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
                            if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('uram','ohioeye','berkeleyeye','utaheye','cumberlandvalleyretina'))){
                                $data['plan_id'] = $providerId;
							}
                            if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('visionfirst','ohioeye','berkeleyeye','utaheye','eyelasikcenter','cumberlandvalleyretina'))){
                                $data['company_id'] = $inhousecode;
							}else{
								$data['company_id'] = $providerId;
							}
                            $data['company_name'] = $providerName;
                            $data['company_address']['street'] = $insAddress;
                            $data['company_address']['city'] = $insCity;
                            $data['company_address']['state_province'] = $insState;
                            $data['company_address']['zip'] = $insZip;
                            $data['insurance_company_contact_person'] = $ins_contact;
                            $data['company_phone'] = $ins_phone;
                            $data['company_group_number'] = $row['group_number'];
                            $data['plan_effective_date'] = str_replace("-", "", substr($row['effective_date'],0,10));
                            $data['plan_expire_date'] = str_replace("-", "", substr($row['expiration_date'],0,10));
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
							if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('ohioeye','berkeleyeye','utaheye','cumberlandvalleyretina'))){
								 $data['verification_date'] = str_replace(array('-',' ',':'),'',$row['timestamp']);
							}
                            $this->addSegment("IN1", $data, $msgType);
							
							if( defined('HL7_INCLUDE_ZIN') && constant('HL7_INCLUDE_ZIN') === true)
							{
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
                                $data['insurance_case_name'] = $row['case_name'];

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
							}
                            $i++;
                        }
                    }
                }
                break;
			case "REFPHY":
					/*Query Referring Physician*/
					$sql = "SELECT 
								`physician_Reffer_id`, `LastName`, `FirstName`, `MiddleName`, `Title`, `Address1`, `Address2`, `ZipCode`, `zip_ext`, `City`, `State`, `NPI`, `delete_status`, `physician_phone`, `physician_fax`, `physician_email`
								FROM 
									`refferphysician` 
								WHERE
									`physician_Reffer_id` = ".(int)$options['ref_phy_id'];
					$resp = imw_query($sql);
					if( $resp && imw_num_rows($resp) == 1 )
					{
						$phyData = imw_fetch_assoc($resp);	/*Physician Data from DB*/

						/*MFI - Master File Identification*/
						$this->addSegment('MFI');

						/*MFE - Master File Entry*/
						$data = array();
						$data['entry_code'] = ($this->msgtype=='Add_Referring_Phisician')?'MAD':'MUP';
						$data['control_id']['id_key'] = $phyData['physician_Reffer_id'];
						$data['control_id']['id_string'] = $phyData['LastName'].','.$phyData['FirstName'];
						$data['primary_key_value'] = $phyData['physician_Reffer_id'];
						$this->addSegment('MFE', $data);

						/*STF - Staff Identification*/
						$data = array();
						$data['primary_key_value']['id'] = $phyData['physician_Reffer_id'];
						$data['primary_key_value']['text'] = $phyData['LastName'].','.$phyData['FirstName'];
						$data['id_code'] = $phyData['physician_Reffer_id'];
						$data['name']['family_name'] = $phyData['LastName'];
						$data['name']['given_name'] = $phyData['FirstName'];
						$data['name']['middle_name'] = $phyData['MiddleName'];
						$data['name']['prefix'] = $phyData['Title'];
						
							/*Physician Status*/
						if($phyData['delete_status'] == '0')
							$status = 'A';
						elseif($phyData['delete_status']=='1')
							$status = 'I';
						elseif($phyData['delete_status']=='2')
							$status = 'N';
						else
							$status = '';

						$data['active_inactive_flag'] = $status;

						$data['phone']['number'] = $phyData['physician_phone'];
						$data['phone']['email_address'] = $phyData['physician_email'];

						$data['address']['street'] = $phyData['Address1'];
						$data['address']['other_designation'] = $phyData['Address2'];
						$data['address']['city'] = $phyData['City'];
						$data['address']['state_province'] = $phyData['State'];
						$data['address']['zip'] = trim($phyData['ZipCode']).trim($phyData['zip_ext']);

						$this->addSegment('STF', $data);

						/*PRA - Practitioner Detail*/
						$data = array();
						$data['primary_key_value'] = $phyData['physician_Reffer_id'];
						$data['practitioner_id_number']['id_number'] = $phyData['NPI'];
						$data['practitioner_id_number']['type_of_id_numer'] = 'NPI';

						$this->addSegment('PRA', $data);
					}
				break;
        }
    }
    
    public function log_message(){
        
		$hl7SentTable = $this->hl7SentTable();
		
        $message = $this->getMessage(0);
        foreach($message as $msg){
			
			$sql = "INSERT INTO `".$hl7SentTable."`(`patient_id`, `msg`, `msg_type`, `saved_on`, `operator`) VALUES('".$this->PD['id']."', '".addslashes($msg)."', '".$this->msgtype."', '".date('Y-m-d H:i:s')."', '".$this->authorId."')";
			$resp = imw_query($sql);
			
			/*Set sender Flag*/
			if($resp)
			{
				$logFile = $this->hl7FlagPath();
				$logFile .= DIRECTORY_SEPARATOR.'senderCheckDB.log';
				file_put_contents($logFile, '1');
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
		$trigger = (int)$trigger;
        switch($msg){
            case "SIU":
                if($trigger==0){$this->msgtypes['SIU']['trigger_event']="S12";$this->msgtype="book_appointemnt";$this->fillStatus="Booked";}
                elseif($trigger==18){$this->msgtypes['SIU']['trigger_event']="S15";$this->msgtype="cancel_appointemnt";$this->fillStatus="Cancelled";}
                elseif($trigger==202){
					$this->msgtypes['SIU']['trigger_event']="S13";
					if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('valleyeye'))){
						$this->msgtypes['SIU']['trigger_event']="S14";
					}
					$this->msgtype="reschedule_appointemnt";$this->fillStatus="Rescheduled";
				}
                elseif($trigger==203){$this->msgtypes['SIU']['trigger_event']="S17";$this->msgtype="delete_appointemnt";$this->fillStatus="Deleted";}
                elseif($trigger==13){$this->msgtypes['SIU']['trigger_event']="S14";$this->msgtype="checkIn_appointment";$this->fillStatus="Arrived";}
                elseif($trigger==11){$this->msgtypes['SIU']['trigger_event']="S14";$this->msgtype="checkOut_appointment";$this->fillStatus="Complete";}
                else{$this->msgtypes['SIU']['trigger_event']="S14";$this->msgtype="update_appointment";$this->fillStatus="Updated";}
                break;
        }
    }
    
    private function getFacilityName($fid){
        $facility = "";
        if($fid!=""){
            $q = "SELECT `id`, `name`, `athenaID`, `phone`, `street`,`city`,`state`,`postal_code`,`zip_ext`,`fax` FROM `facility` WHERE `id`='".$fid."'";
            $sqlD = imw_query($q);
            if($sqlD && imw_num_rows($sqlD)>0){
                $facility = imw_fetch_assoc($sqlD);
            }
        }
        return($facility);
    }
	
	private function getDoctorDetails($did){
		$doctor = "";
		if($did!=""){
			$q = "SELECT `id`, `fname`, `lname`, `mname`, `athenaID`, `external_id`, `user_npi` FROM `users` WHERE `id`='".$did."'";
			$sqlD = imw_query($q);
			if($sqlD && imw_num_rows($sqlD)>0){
				$doctor = imw_fetch_assoc($sqlD);
			}
			
		}
		return($doctor);
	}
    
    public function setReceivingFacility($app='', $facility=''){
		if(isset($GLOBALS['HL_RECEIVING']) && is_array($GLOBALS['HL_RECEIVING'])){
        	if($app=='') 		$app 		= $GLOBALS['HL_RECEIVING']['APPLICATION'];
			if($facility=='') 	$facility 	= $GLOBALS['HL_RECEIVING']['FACILITY'];
		}		
		$this->receiving['application'] = ($app === '') ? 'TEST' : $app;
        $this->receiving['facility'] = ($facility === '') ? 'TEST' : $facility;
    }
	
	public function setSendingFacility($app='', $facility=''){
		if(isset($GLOBALS['HL_SENDING']) && is_array($GLOBALS['HL_SENDING'])){
        	if($app=='') 		$app 		= $GLOBALS['HL_SENDING']['APPLICATION'];
			if($facility=='') 	$facility 	= $GLOBALS['HL_SENDING']['FACILITY'];
		}		
		$this->sending['application'] 	= ($app === '') 		? 'TEST' : $app;
        $this->sending['facility'] 		= ($facility === '') 	? 'TEST' : $facility;
    }
    
    private function getDefaultInsCase(){
        if($this->defaultInsCaseId==""){
            $defaultCaseTypeQry 	= "SELECT case_id, case_name FROM insurance_case_types WHERE normal = '1'";
            $defaultCaseTypeRes 	= imw_query($defaultCaseTypeQry);
            if($defaultCaseTypeRes){
                if(imw_num_rows($defaultCaseTypeRes)>0){
                    
                    $defaultCaseTypeRow = imw_fetch_array($defaultCaseTypeRes);
                    $this->defaultInsCaseId = $defaultCaseTypeRow['case_id'];
                }
            }
        }
        return($this->defaultInsCaseId);
    }
    
    /*Add Event Segment*/
    public function addEVN($EVN){
        if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('visionfirst','eyelasikcenter'))) return;
        $data = array();
        $data['event_typeCode'] = trim($EVN);
        $data['recorded_daTime'] = date("YmdHis");
        $data['operatorId'] = strtoupper($_SESSION['authUser']);
        $this->addSegment("EVN", $data);
    }
}
?>