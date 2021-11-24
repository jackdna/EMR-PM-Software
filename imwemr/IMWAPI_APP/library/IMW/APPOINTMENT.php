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

namespace IMW;

include_once($GLOBALS['fileroot']."/iportal_config/vocabulary.php");

use DateTime;
use DateInterval;
use DatePeriod;
use appt_scheduler;
use vocabulary;

/**
 * APPOINTMENT
 *
 * Main Appointment Class
 */
class APPOINTMENT
{
	public $dbh_obj;
	public $service_obj;
	public $currentDate;
	public $slotTime;
	public $slotStartTime;
	public $slotEndTime;
	
	public function __construct($db_obj = '', $service_obj = ''){
		
		$this->currentDate = strtotime(date('Y-m-d'));
		$this->slotTime = 10*60;
		$this->slotStartTime = strtotime("08:00:00");
		$this->slotEndTime = strtotime("17:10:00");
		
		if(empty($db_obj) == false){
			$this->dbh_obj = $db_obj;
		}
		
		if(empty($service_obj) == false){
			$this->service_obj = $service_obj;
		}
	}
	
	//Returns Available slots for the provided physician
	public function getAvailableSlots($startDate = '', $endDate = '', $physicianId = '', $facilityId = '', $validateId = ''){
		$dbh = $this->dbh_obj;
		
		if(empty($startDate) || empty($endDate) || empty($physicianId)) return false;
		$returnData = $dateArr = array();
		
		$startDateTime = strtotime($startDate);
		$endDateTime = strtotime($endDate);
		
		//End date should always be less than this date -- Max 1 week date range is allowed
		//Change $lastDateTime to increase the time span of the date range
		$lastDateTime = strtotime(DateTime::createFromFormat('Y-m-d',$startDate)
					   ->add(DateInterval::createFromDateString('1 week'))
					   ->format('Y-m-d'));
		
		if($this->currentDate > $startDateTime)	$returnData['errorStr'] = "Date range should be start from current date.";
//		if($endDateTime > $lastDateTime)	$returnData['errorStr'] = "Date range should lie between a week.";
		
		//If some error is there, abort the call
		if(isset($returnData['errorStr']) && empty($returnData['errorStr']) == false){
			return $returnData;
		}else{
			$dateArr['startDate'] = $startDateTime;
			$dateArr['endDate'] = $endDateTime;
			$dateArr['lastDate'] = $lastDateTime;
			
			//No. of days
			$StartDtObj = new DateTime(date('Y-m-d', $startDateTime));
			$EndDtObj = new DateTime(date('Y-m-d', $endDateTime));
			$EndDtObj = $EndDtObj->modify( '+1 day' ); 

			$interval = new DateInterval('P1D');
			$daterange = new DatePeriod($StartDtObj, $interval ,$EndDtObj);
			
			foreach($daterange as $date){
				$dateArr['dayCount'][] = $date->format("Y-m-d");
			}
		}
		
		//If dateArr['dayCount'] has something - proceed
		if(count($dateArr['dayCount']) > 0){
			//Get Physician Schedule Slots for the dates in the date range
			foreach($dateArr['dayCount'] as $dateObj){
				$selectedDate1 = str_replace("-","_", $dateObj);
				
				$arr_prov_sch = $this->getProviderSchedules($dateObj, array($physicianId), array($facilityId));
				
				foreach($arr_prov_sch as $key => &$val){
					if(count($val) > 0){
						
						//Get Facility
						$FacfacilityId = $val['facility'];
						$facilityName = '';
						
						$chkFacility = $dbh->imw_query('Select id, name FROM facility WHERE id = '.$val['facility'].' AND show_in_ptportal = 1');
						if($dbh->imw_num_rows($chkFacility) > 0){
							$rowFac = $dbh->imw_fetch_assoc($chkFacility);
							$facilityName = $rowFac['name'];
						}
						
						if(empty($facilityName) == false && isset($val['iportal_enable_slot']) && empty($val['iportal_enable_slot']) == false && isset($val['sch_tmp_id']) && empty($val['sch_tmp_id']) == false){
							
							$schTmpId = $val['sch_tmp_id'];
							$iportalSlots = unserialize(html_entity_decode(urldecode($val['iportal_enable_slot'])));
							
							//Get Office Time
							$arrTemplates = $this->getScheduleTemplate($schTmpId);
							
							//Get slot times based on templates
							$excludeSlots = $this->excludeSlots($arrTemplates, $FacfacilityId, $val['provider'], $val['today_date']);
							
							//Get iPortal Slot times
							$slotTimes = $this->getIPortalSlots($excludeSlots, $iportalSlots, $dateObj);
							
							$arrSlots = $slots = array();
							
							$slots[$dateObj] = $slotTimes;
							
							//Get available Time Slots
							$timeSlots = $this->getTimeSlots($slots);
							
							
							//Remove extra Slots other than available time slots
							 foreach($slots as $slKey => &$slVal){
								if(isset($slVal['slots']) && count($slVal['slots']) > 0){
									$flag = false;
									foreach($slVal['slots'] as $dtslKey => $dtslVal){
										
										if($timeSlots && isset($timeSlots[$dtslVal])){
											
											// ID is converted to 'strtotime' slValue so that it can be shown as a slValid ID for the consumer --> convert it to 'date(Y_m_d_h_i_a)' to get time slot id
											$tmpArr = array('ID' => $dtslVal, 'FacilityId' => $FacfacilityId, 'TimeSlot' => $timeSlots[$dtslVal], 'TemplateId' => $schTmpId);
											
											if(empty($validateId) == false && $flag == false){
												
												//Only return true/false -- to validate if book appointment got a valid time slot or not
												if($dtslVal == $validateId){
													$flag = true;
												}
												else{
													$flag=false;
												}
												//return false;
											}else{
												$arrSlots[] = $tmpArr;
											}
										}
									}
								}
							}
							$returnData[$facilityName][$dateObj] = $arrSlots;
						}
					}
				}
			}
			if(empty($validateId) == false){
				$returnData = $flag;
			}
			return $returnData;
			
		}else{
			$returnData['errorStr'] = "Unable to process the request.";
			return $returnData;
		}
	}
	
	//Return Schedule Template Data - timings for slots
	public function getScheduleTemplate($id = ''){
		if(empty($id)) return false;
		
		$returnData = array();
		$str_tmp = $this->dbh_obj->imw_query("select id, morning_start_time, morning_end_time from schedule_templates where id = ".$id." order by id");
		if($this->dbh_obj->imw_num_rows($str_tmp) > 0){
			while($row = $this->dbh_obj->imw_fetch_assoc($str_tmp)){
				$returnData[] = $row;
			}
		}
		
		return $returnData;
	}
	
	//Returns Time slots to exclude or remove
	public function excludeSlots($arrData = array(), $facId = '', $phyId = '', $startDate = ''){
		if(count($arrData) == 0 || empty($facId) || empty($startDate) || empty($phyId)) return false;
		
		$lunchTime = $bookings = $returnData = array();
		
		foreach($arrData as $key => &$val){
			if(count($val) > 0){
				$templateId = $val['id'];
				$startTime = $val['morning_start_time'];
				$endTime = $val['morning_end_time'];
				
				$lbl = $this->dbh_obj->imw_query("SELECT `start_time`, `end_time` FROM `schedule_label_tbl` WHERE `sch_template_id`='".$templateId."' AND `label_type`='Lunch'");
				
				if($lbl && $this->dbh_obj->imw_num_rows($lbl)>0){
					$i = 1;
					while($row = $this->dbh_obj->imw_fetch_assoc($lbl)){
						$lunchTime[$i]['start'] = strtotime($row['start_time']);
						$lunchTime[$i]['end'] = strtotime($row['end_time']);
						$i++;
					}
				}

				$sql = "SELECT `id`, `sa_app_starttime`, `sa_app_endtime`, `sa_app_duration`, `sa_app_start_date`, `sa_app_end_date` FROM `schedule_appointments` WHERE `sa_doctor_id`='".$phyId."' AND `sa_patient_app_status_id`NOT IN('18','203') AND `sa_facility_id`='".$facId."' AND `sa_app_start_date`='".$startDate."' AND `sch_template_id`='".$templateId."'";
				$data = $this->dbh_obj->imw_query($sql);
		
				if($data && $this->dbh_obj->imw_num_rows($data)>0){
					$i = 1;
					while($row = $this->dbh_obj->imw_fetch_assoc($data)){
						$bookings[$i]['start'] = strtotime($row['sa_app_starttime']);
						$bookings[$i]['end'] = strtotime($row['sa_app_endtime']);
						$i++;
					}
				}
			}
		}
		
		if(count($lunchTime) > 0) $returnData['lunchTime'] = $lunchTime;
		if(count($bookings) > 0) $returnData['bookingTime'] = $bookings;
		
		return $returnData;
		
	}
	
	//Return iPortal Time Slots
	public function getIPortalSlots($excludeArr = array(), $iportalArr = array(), $date = ''){
		if(count($iportalArr) == 0 || empty($date)) return false;
		$returnData = array();
		
		foreach($iportalArr as $obj){
			if(isset($excludeArr['bookingTime']) && count($excludeArr['bookingTime']) > 0){
				foreach($excludeArr['bookingTime'] as $booking){ /*Skip already booked time slots*/
					if($booking['start']<=$obj['start'] && $booking['end']>=$obj['end']){
						continue(2);
					}
					elseif($booking['start']>$obj['start'] && $booking['start']<$obj['end']){
						continue(2);
					}
					elseif($booking['end']>$obj['start'] && $booking['end']<$obj['end']){
						continue(2);
					}
				}
			}
			
			
			if(isset($excludeArr['lunchTime']) && count($excludeArr['lunchTime']) > 0){ /*Skip Lunch time slots*/
				foreach($excludeArr['lunchTime'] as $lt){
					if($lt['start']<=$obj['start'] && $lt['end']>=$obj['end']){
						continue(2);
					}
					elseif($lt['start']>$obj['start'] && $lt['start']<$obj['end']){
						continue(2);
					}
					elseif($lt['end']>$obj['start'] && $lt['end']<$obj['end']){
						continue(2);
					}
				}
			}
			$returnData['slots'][] = strtotime($date.' '.date('h:i a', $obj['start']));
		}
		return $returnData;
		
	}
	
	//Return time slots within time range - 8:00 - 17:00
	public function getTimeSlots($countDt = ''){
		if(empty($this->slotStartTime) || empty($this->slotEndTime) || empty($this->slotTime) || empty($countDt)) return false;
		
		$timeSlots = array();
		
		$flag = true;
		$slotEnd = $this->slotStartTime;
		
		//Creating Time slots
		$i = 1;
		do{
			$slotStart = $slotEnd;
			$slotEnd = strtotime("+".$this->slotTime." seconds", $slotStart);
			
			if($slotEnd > $this->slotEndTime){$flag = false;}
			else{
				$startTime = date("h:i A", $slotStart);
				$startTimeId = date("h:i a", $slotStart);
				
				foreach($countDt as $key => $val){
					$dateObj = New DateTime(str_replace('_', '-', $key));
					$elemId = $dateObj->format('Y')."-".$dateObj->format('m')."-".$dateObj->format('d')." ".$startTimeId;
					$timeSlots[strtotime($elemId)] = $startTime;
				}
			}
			$i++;
		}while($flag);
		
		return $timeSlots;
	}
	
	public function getProviderSchedules($wd, $ap = array(), $arrFacility = array()){
		//variable declarations
		$pr = false;	$wno = $dno = 0;	$ar_wd = $arr_sch = $arr_del_sch = $arr_sch_tmp = $arr_sch2 = array();	$q = $r = $str_sch = "";

		//selected provider
		if(count($ap) > 0){	$pr = "(".implode("','", $ap).")";	}

		//selected facility
		$strFacility='';
		if(count($arrFacility) > 0){ $strFacility = implode(",", $arrFacility);	}

		//calculating week day no and week no
		$ar_wd = explode("-", $wd);	$wno = ceil($ar_wd[2] / 7);	$dno = date("w", mktime(0, 0, 0, $ar_wd[1], $ar_wd[2], $ar_wd[0]));	if($dno == 0) $dno = 7;
		
		//quering provider schedules
		$q = "select id, del_status, delete_row, status, provider, facility, today_date, sch_tmp_id from provider_schedule_tmp where today_date <= '".$wd."' and week".$wno." = '".$dno."' ";
		if($pr != false){	$q .= " and provider_schedule_tmp.provider IN ".$pr." ";	}
		if(empty($strFacility)==false){ $q .= " and provider_schedule_tmp.facility IN (".$strFacility.") ";	 }
		
		$q .= "order by provider, facility, sch_tmp_id, today_date";
		$r = $this->dbh_obj->imw_query($q);
		if($this->dbh_obj->imw_num_rows($r) > 0){
			while($row1 = $this->dbh_obj->imw_fetch_assoc($r)){
				$arr_sch[] = $row1;
			}
			$arr_sch_tmp = $arr_sch;
			for($i = 0; $i < count($arr_sch_tmp); $i++){
				//removing deleted schedules
				if($arr_sch_tmp[$i]["del_status"] == 1){
					$arr_del_sch[] = $arr_sch_tmp[$i];
					unset($arr_sch[$i]);
				}
			}
		}
		if(count($arr_sch)>0) $arr_sch = array_values($arr_sch);
		if(count($arr_del_sch)>0) $arr_del_sch = array_values($arr_del_sch);
		
		//removing shcedules which have been deleted for future
		$arr_sch_tmp = $arr_sch;
		if(count($arr_del_sch)>0){
			for($j = 0; $j < count($arr_del_sch); $j++){
				for($k = 0; $k < count($arr_sch_tmp); $k++){
					if(strtolower($arr_del_sch[$j]["delete_row"]) == "all"){
						if($arr_del_sch[$j]["provider"] == $arr_sch_tmp[$k]["provider"] && $arr_del_sch[$j]["facility"] == $arr_sch_tmp[$k]["facility"] && $arr_del_sch[$j]["sch_tmp_id"] == $arr_sch_tmp[$k]["sch_tmp_id"] && strtotime($arr_del_sch[$j]["today_date"]) >= strtotime($arr_sch_tmp[$k]["today_date"])){							
							unset($arr_sch[$k]);
						}
					}
					if(strtolower($arr_del_sch[$j]["delete_row"]) == "no"){
						if($arr_del_sch[$j]["provider"] == $arr_sch_tmp[$k]["provider"] && $arr_del_sch[$j]["facility"] == $arr_sch_tmp[$k]["facility"] && $arr_del_sch[$j]["sch_tmp_id"] == $arr_sch_tmp[$k]["sch_tmp_id"] && strtotime($arr_del_sch[$j]["today_date"]) == strtotime($wd)){							
							unset($arr_sch[$k]);
						}
					}
				}
			}
		}
		if(count($arr_sch)>0) $arr_sch = array_values($arr_sch);
		if(count($arr_del_sch)>0) $arr_del_sch = array_values($arr_del_sch);

		//removing schedules which were created for a single day earlier than the sought date
		$arr_sch_tmp = $arr_sch;
		if(count($arr_sch_tmp)>0){	
			for($i = 0; $i < count($arr_sch_tmp); $i++){
				if(strtotime($arr_sch_tmp[$i]["today_date"]) < strtotime($wd) && strtolower($arr_sch_tmp[$i]["status"]) == "no"){
					$arr_del_sch[] = $arr_sch_tmp[$i];					
					unset($arr_sch[$i]);
				}
			}
		}
		if(count($arr_sch)>0) $arr_sch = array_values($arr_sch);
		if(count($arr_del_sch)>0) $arr_del_sch = array_values($arr_del_sch);
		
		//removing duplicate records if any
		if(count($arr_sch)>0){
			$arr_sch_tmp = array();	//resetting array
			for($i = 0; $i < count($arr_sch); $i++){
				$arr_sch_tmp[] = $arr_sch[$i]["id"];
			}
			$str_sch = join(',', $arr_sch_tmp);
			$q = "select id, facility , provider, sch_tmp_id, today_date,iportal_enable_slot from provider_schedule_tmp where id in (".$str_sch.") ";
			if($pr != false){	$q .= "and provider_schedule_tmp.provider IN ".$pr." ";	}
			if(empty($strFacility)==false){ $q .= " and provider_schedule_tmp.facility IN (".$strFacility.") ";	 }
			$q .= "order by provider, facility, sch_tmp_id, today_date";
			$r = $this->dbh_obj->imw_query($q);
			if($this->dbh_obj->imw_num_rows($r) > 0){
				while($row2 = $this->dbh_obj->imw_fetch_assoc($r)){
					$arr_sch2[] = $row2;
				}
				$arr_sch3 = $arr_sch2;
				for($n = 0; $n < count($arr_sch2); $n++){
					if($arr_sch2[$n]['sch_tmp_id'] == $arr_sch2[$n+1]['sch_tmp_id'] && $arr_sch2[$n]['facility'] == $arr_sch2[$n+1]['facility'] && $arr_sch2[$n]['provider'] == $arr_sch2[$n+1]['provider']){
						$arr_del_sch[] = $arr_sch2[$n];
						unset($arr_sch3[$n]);
					}
				}
			}			
		}
		$arr_sch = $arr_sch3;
		if(count($arr_sch)>0) $arr_sch = array_values($arr_sch);
		if(count($arr_del_sch)>0) $arr_del_sch = array_values($arr_del_sch);

		//unsetting variables
		unset($pr, $wno, $dno, $ar_wd, $arr_del_sch, $arr_sch_tmp, $arr_sch2, $q, $r, $str_sch);

		return $arr_sch;
	}
	
	public function bookAppointment($patientId = '', $timeSlotId = '', $physicianId = '', $facilityId = '', $procedureId = '', $apptInfo = '', $ScheduleTemplateId = ''){
		//DataBase Object
		$dbh = $this->dbh_obj;
		
		if(empty($patientId) || empty($timeSlotId) || empty($physicianId) || empty($facilityId) || empty($procedureId)) return false;
		
		//If already appointment exists abort the call
		
		$chkAppt = $dbh->imw_query("select schedule_appointments.id, schedule_appointments.sa_facility_id, schedule_appointments.procedureid,TIME_FORMAT(schedule_appointments.sa_app_starttime, '%h:%i %p') as sa_app_starttime, DATE_FORMAT(schedule_appointments.sa_app_start_date, '%m-%d-%y') as sa_app_start_date, schedule_appointments.sa_doctor_id, schedule_appointments.sa_patient_app_status_id, slot_procedures.acronym, slot_procedures.proc, slot_procedures.id from schedule_appointments schedule_appointments LEFT JOIN slot_procedures ON slot_procedures.id=schedule_appointments.procedureid where (sa_patient_id='".$patientId."'  and sa_patient_app_status_id NOT IN('18', '203')) AND (sa_app_start_date >='".date('Y-m-d', $this->currentDate)."' and sa_patient_id='".$patientId."' and sa_patient_app_status_id NOT IN('18', '203')) order by sa_app_start_date,sa_app_starttime limit 0,1");
		if($dbh->imw_num_rows($chkAppt) > 0) return $returnData = array("Status" => false,"Reason" =>'An appointment already exists. Unable to proceed.');

		$timeSlotDt = strtotime(date('Y-m-d H:i', $timeSlotId));
		
		if($this->currentDate > $timeSlotDt) return $returnData = array("Status" => false,"Reason" => 'Selected Slot Date is invalid');
		
		//Validating Time slot -- Providing last argument will return whether timeSlotId is valid or not
		$timeSlots = $this->getAvailableSlots(date('Y-m-d',$timeSlotDt), date('Y-m-d',$timeSlotDt), $physicianId, $facilityId, $timeSlotId);
		
		if($timeSlots === false) return $returnData = array("Status" => false,"Reason" =>'Please provide a valid time Slot.');
		
		//------ Setting Appointment Variables
		$physicianName = $facilityName = $ptData = $appDuration = $apptStart = $apptEnd = $apptDate = $procedureName = '';
		
		$apptDate = date('Y-m-d', $timeSlotDt);
		
		//Physician Name
		$chkPhy = $dbh->imw_query('SELECT fname, lname, mname from users where id = '.$physicianId.' AND delete_status = 0 AND user_type = 1 AND Enable_Scheduler = 1');
		if($dbh->imw_num_rows($chkPhy) > 0){
			$rowPhy = $dbh->imw_fetch_assoc($chkPhy);
			$physicianName = $rowPhy['lname'].', '.$rowPhy['fname'].' '.$rowPhy['mname'];
		}
		
		//Facility Name
		$chkFac = $dbh->imw_query('SELECT id,name from facility where id = '.$facilityId.'');
		if($dbh->imw_num_rows($chkFac) > 0){
			$rowFac = $dbh->imw_fetch_assoc($chkFac);
			$facilityName = $rowFac['name'];
		}
		
		//Patient Data
		$chkPt = $dbh->imw_query('SELECT id,fname, mname, lname, email, CONCAT(street, ", ", street2) as address, phone_home as phone FROM patient_data WHERE id = '.$patientId.'');
		if($dbh->imw_num_rows($chkPt) > 0){
			//print_r(get_defined_functions());
			$rowPt = $dbh->imw_fetch_assoc($chkPt);
			$ptName = core_name_format($rowPt['fname'], $rowPt['lname'], $rowPt['mname']);
			$ptData['patientId'] = $rowPt['id'];
			$ptData['name'] = $ptName;
			$ptData['email'] = $rowPt['email'];
			$ptData['address'] = $rowPt['address'];
			$ptData['phone'] = $rowPt['phone'];
		}
		
		//Getting Procedure Details
		$qryGetSP = "
			SELECT 
				spa.times as procTime, 
				spb.id as procId,
				spb.proc
			FROM 
				slot_procedures spa, slot_procedures spb 
			WHERE 
				(spb.id) = ('".$procedureId."') and 
				spa.doctor_id = '0' and 
				spb.proc_time = spa.id
			ORDER BY 
				spb.id ASC 
			LIMIT 1";
		$rsGetSP = $dbh->imw_query($qryGetSP);		
		if($dbh->imw_num_rows($rsGetSP) > 0){
			$rowGetSP = $dbh->imw_fetch_array($rsGetSP);
			$intAppProcedure = (int)$rowGetSP['procId'];
			$intAppTimeDuration = (int)$rowGetSP['procTime'];
			$intAppTimeDuration_set = "00:".(int)$rowGetSP['procTime'].":00";
			$appDuration = $intAppTimeDuration * 60;
			$procedureName = $rowGetSP['proc'];
		}
		
		if(empty($appDuration) == false){
			$apptStart = strtotime(date('H:i', $timeSlotDt));
			$apptEnd = (int)$appDuration+(int)$apptStart;
		}
		
		//Variable Array
		$arrVariable = array();
		$arrVariable['physicianId'] = $physicianId;
		$arrVariable['facilityId'] = $facilityId;
		$arrVariable['apptDate'] = $apptDate;
		$arrVariable['procedureId'] = $procedureId;
		$arrVariable['apptStart'] = $apptStart;
		$arrVariable['apptEnd'] = $apptEnd;
		$arrVariable['appDuration'] = $appDuration;
		$arrVariable['ScheduleTemplateId'] = $ScheduleTemplateId;
		$arrVariable['PtData'] = $ptData;
		
		if(
			empty($arrVariable['physicianId']) == false && 
			empty($arrVariable['facilityId']) == false && 
			empty($arrVariable['apptDate']) == false && 
			empty($arrVariable['procedureId']) == false && 
			empty($arrVariable['apptStart']) == false && 
			empty($arrVariable['apptEnd']) == false && 
			empty($arrVariable['appDuration']) == false && 
			empty($arrVariable['ScheduleTemplateId']) == false
		){
			
			
			// Search for Already Applied Label In the selected Time Slot
			$replaceLabels = $this->getReplaceLabelArr($arrVariable['ScheduleTemplateId'], $arrVariable['apptStart'], $arrVariable['apptEnd'], $procedureName);
			
			//Add Appointment
			$cDate = date("Y-m-d",time());
			$cTime = date("H:i:s",time());
			
			$sql = "
				INSERT INTO `schedule_appointments` SET 
				`sa_doctor_id`='".$arrVariable['physicianId']."',
				`sa_patient_id`='".$arrVariable['PtData']['patientId']."',
				`sa_patient_name`='".$arrVariable['PtData']['name']."',
				`sa_patient_app_status_id`='0',
				`sa_comments`='Appointment Added From Patient Portal',
				`sa_app_time`='".$cDate." ".$cTime."',
				`sa_app_starttime`='".date("H:i:s", $arrVariable['apptStart'])."',
				`sa_app_endtime`='".date("H:i:s", $arrVariable['apptEnd'])."',
				`sa_app_duration`='".$arrVariable['appDuration']."',
				`sa_facility_id`='".$arrVariable['facilityId']."',
				`sa_app_start_date`='".$arrVariable['apptDate']."',
				`sa_app_end_date`='".$arrVariable['apptDate']."',
				`procedureid`='".$arrVariable['procedureId']."',
				`case_type_id`='',
				`sa_madeby`='admin',
				`status_update_operator_id`='1',
				`RoutineExam`='Yes',
				`sch_template_id`='".$arrVariable['ScheduleTemplateId']."'";
			$apptId = false;
			$sql = $dbh->imw_query($sql);
			if($sql){
				$apptId = $dbh->imw_insert_id();
			}
			
			//After Adding Appt. Insert a entry in Previous Status Table
			if($apptId){
				$sql1 = "INSERT INTO `previous_status` SET 
						`sch_id`='".$apptId."',
						`patient_id`='".$arrVariable['PtData']['patientId']."',
						`status_time`='".$cTime."',
						`status_date`='".$cDate."',
						`status`='0',
						`old_date`='".$arrVariable['apptDate']."',
						`old_time`='".date("H:i:s", $arrVariable['apptStart'])."',
						`old_provider`='".$arrVariable['physicianId']."',
						`old_facility`='".$arrVariable['facility']."',
						`statusComments`='Appointment Added From Patient Portal',
						`oldMadeBy`='admin',
						`statusChangedBy`='admin',
						`dateTime`='".$cDate." ".$cTime."',
						`new_facility`='".$arrVariable['facilityId']."',
						`new_provider`='".$arrVariable['physicianId']."',
						`old_status`='0',
						`new_appt_date`='".$arrVariable['apptData']."',
						`new_appt_start_time`='".date("H:i:s", $arrVariable['apptStart'])."',
						`old_appt_end_time`='".date("H:i:s", $arrVariable['apptEnd'])."',
						`new_appt_end_time`='".date("H:i:s", $arrVariable['apptEnd'])."',
						`old_procedure_id`='".$arrVariable['procedureId']."',
						`new_procedure_id`='".$arrVariable['procedureId']."'";
				imw_query($sql1);
				
				if(count($replaceLabels)>0){
					$labelReplaced = "::".$apptId.":".$procedureName;
					foreach($replaceLabels as $lbl){
						$sql2 = "INSERT INTO `scheduler_custom_labels` SET 
								`provider`='".$arrVariable['physicianId']."',
								`facility`='".$arrVariable['facilityId']."',
								`start_date`='".$arrVariable['apptDate']."',
								`start_time`='".$lbl['start']."',
								`end_time`='".$lbl['end']."',
								`l_type`='".$lbl['type']."',
								`l_text`='".$lbl['text']."',
								`l_show_text`='".$lbl['show_text']."',
								`l_color`='".$lbl['color']."',
								`time_status`='".$cDate." ".$cTime."',
								`system_action`='1',
								`labels_replaced`='".$labelReplaced."'";
						//condition added to stop garbage value
						if($lbl['type']=='Procedure' || $lbl['type']=='Information' || $lbl['type']=='Lunch' || $lbl['type']=='Reserved')
						{
							imw_query($sql2);
						}else custom_lbl_log('imwapi_app\library\imw\appointment.php');
					}
				}
				$returnData = array("Status" => true,"Reason" =>'Appointment Request Send successfully.');
			}
			
			//Sending Notification to the provider
			$msgData = $autoResponder = '';
			
			$chkAutoResponser = $dbh->imw_query("SELECT `data`, `forwarder` FROM `iportal_autoresponder_templates` WHERE `type`='1' AND `del_status`='0' AND `status`='1' LIMIT 1 ");
			if($dbh->imw_num_rows($chkAutoResponser) > 0){
				$autoResponder = $dbh->imw_fetch_assoc($chkAutoResponser);
			}
			
			$chkMsgs = $dbh->imw_query("SELECT pd.fname, pd.mname, pd.lname, pm.sender_id, pm.msg_subject, pm.msg_data, DATE_FORMAT(pm.msg_date_time,'%m %h:%i %p') AS msg_date_time FROM patient_messages pm INNER JOIN patient_data pd ON(pm.sender_id=pd.id) WHERE pm.pt_msg_id = '' ");
			if($dbh->imw_num_rows($chkMsgs) > 0){
				$msgData = $dbh->imw_fetch_assoc($chkMsgs);
			}
			
			if(empty($autoResponder) == false && isset($autoResponder['data'])){
				$name_sendTo = $msgData['lname'].', '.$msgData['fname'];
				$ORsenderName = "Patient Co-ordinator";
				$sentDate = $msgData["msg_date_time"];
				$originalSubject = $msgData["msg_subject"];
				$originalTextPrefix = "<br /><br />----ORIGINAL MESSAGE----<br />";
				$originalTextPrefix .= "	From: ".$name_sendTo."<br />";
				$originalTextPrefix .= "	To: ".$ORsenderName."<br />";
				$originalTextPrefix .= "	Sent: ".$sentDate."<br />";
				$originalTextPrefix .= "	Subject: ".$originalSubject."<br /><br />";
				
				$originalTextPrefix .= $msgData["msg_data"];
				
				$obj = new vocabulary;
				$obj->pt_id = ($msgData['sender_id']=="") ? $arrVariable['PtData']['patientId'] : $msgData['sender_id'];
				$obj->phy_id = $arrVariable['physicianId'];
				$obj->fac_name = $facilityName;
				$autoResponder['data'] = $obj->parse($autoResponder['data']);
				
				$msg_data = $autoResponder['data'].$originalTextPrefix;
				
				$msg_subject = "Re: ".$msgData["msg_subject"];
				$msg_data = $msg_data;
				
				$req_qry = "INSERT INTO patient_messages SET receiver_id = '".$msgData['sender_id']."', sender_id = '0', communication_type = 1, msg_subject = '".$msg_subject."', msg_data = '".$msg_data."', message_urgent='".$message_urgent."', replied_id=''";
				
				
				$req_qry_obj = $dbh->imw_query($req_qry);
				
				if($req_qry_obj){
					//Mail sending Work pending
					
					$msg_data = "<b> Patient Name </b> - ".$arrVariable['PtData']['patientId']."<br />";
					$msg_data .= "<b> Email </b> - ".$arrVariable['PtData']['email']."<br />";
					$msg_data .= "<b> Phone </b> - ".$arrVariable['PtData']['phone']."<br />";
					$msg_data .= "<b> Address </b> - ".$arrVariable['PtData']['address']."<br />";
					$msg_data .= "<b> Physician Name </b> - ".$physicianName."<br />";
					$msg_data .= "<b> Selected Facility </b> - ".$facilityName."<br />";
					$msg_data .= "<b> Appointment Reason </b> - ".$procedureName."<br />";
					$msg_data .= "<b> Appointment Date </b> - ".$arrVariable['apptDate']."<br />";
					$msg_data .= "<b> Appointment Time </b> - ".date("H:i:s", $arrVariable['apptStart'])."<br />";
					$msg_data .= "<b> Additional Information </b> - ".$apptInfo."<br />";
					
					$req_qry = $dbh->imw_query("INSERT INTO patient_messages SET receiver_id = '".$physicianName."', sender_id = '".$patientId."', communication_type = 2, msg_subject = '".addslashes($msg_subject)."', msg_data = '".$msg_data."'");
					
					$returnData = array("Status" => true,"Reason" =>'Appointment Request Send successfully.');
				}else{
					$returnData = array("Status" => false,"Reason" =>'Unable to process the request.Please Try Again.');
				}
			}
			
			return $returnData;
		}else{
			return $returnData = array("Status" => false,"Reason" =>'Invalid Request. Try Again.');
		}
	}
	
	public function getReplaceLabelArr($templateId = '', $apptStart = '', $apptEnd = '', $procedure = ''){
		if(empty($apptStart) || empty($apptEnd) || empty($procedure)) return false;
		
		$dbh = $this->dbh_obj;
		$returnData = array();
		
		$chkLabels = $dbh->imw_query("
			SELECT 
				`schedule_label_id`, 
				`template_label`, 
				`label_type`, 
				`label_color`,
				TIME_FORMAT(`start_time`,'%H:%i:%s') AS 'start_time',
				TIME_FORMAT(`end_time`,'%H:%i:%s') AS 'end_time'
			FROM 
				`schedule_label_tbl`
			WHERE 
				`sch_template_id` = '".$templateId."'
				AND `start_time` >= '".date("H:i", $apptStart)."'
				AND `end_time` <= '".date("H:i", $apptEnd)."'
				AND `del_status` = ''
		");
			
		if($dbh->imw_num_rows($chkLabels) > 0){
			while($row = imw_fetch_assoc($chkLabels)){
				$label = $row['template_label'];
				$labels = explode("; ",$label);
				$labels1 = $labels;
				
				$lblId = $row['schedule_label_id'];
				
				/*Find Selected Procedure name in Lables*/
				foreach($labels as $key=>$lbl){
					if($lbl==$procedure){
						unset($labels1[$key]);
						krsort($labels1);
						
						$returnData[$lblId]['type'] = $row['label_type'];
						$returnData[$lblId]['text'] = $label;
						$returnData[$lblId]['show_text'] = implode("; ", $labels1);
						
						$returnData[$lblId]['type'] = $row['label_type'];
						$returnData[$lblId]['color'] = $row['label_color'];
						$returnData[$lblId]['start'] = $row['start_time'];
						$returnData[$lblId]['end'] = $row['end_time'];
						
					}
				}
			}
		}
		
		return $returnData;
	}
	
}