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
class sch_service
{
	private $db_obj;
	protected $doctor_id, $reqDate, $facilitiesId, $apptId, $patId, $temp_start_time, $temp_end_time;
	public function __construct($db_obj)
	{
		$this->db_obj = $db_obj;
		$this->obj_db = $GLOBALS["adodb"]["db"];
	}
	
	public function day_proc_summary($callInternal = 0)
	{
		$doctor_id = trim($_REQUEST["phyId"]);
		$doctor_id = (int) $doctor_id;
		$this->doctor_id = $doctor_id;
		$this->facilitiesId = $_REQUEST["facilitiesId"];		
		$reqDate = trim($_REQUEST["reqDate"]);
		$this->reqDate = $reqDate;
						
		$qry_sch = "select sp.acronym, sp.proc, sa.sa_doctor_id, sa.procedureid, doc.fname, doc.lname, doc.mname from schedule_appointments sa left join slot_procedures sp on sp.id = sa.procedureid left join slot_procedures sp2 on sp2.id = sp.proc_time left join schedule_status st on st.id = sa.sa_patient_app_status_id left join users doc on doc.id = sa.sa_doctor_id where sa_facility_id IN (".$this->facilitiesId.") and sa_doctor_id = ? and sa_test_id = 0 and sa_patient_app_status_id NOT IN (203,201,18,19,20) and ? between sa_app_start_date and sa_app_end_date order by sa.sa_app_starttime, sa.sa_app_time desc";
		$this->db_obj->run_query($qry_sch, array($this->doctor_id, $this->reqDate));
		$day_proc_summary_obj = $this->db_obj->get_qry_result();
		
		$day_proc_summary_arr = array();
		
		foreach($day_proc_summary_obj as $day_proc_summary)
		{
			$day_proc_summary_arr[$day_proc_summary["procedureid"]]['proc'] = ($day_proc_summary["acronym"] != "") ? $day_proc_summary["acronym"] : $day_proc_summary["proc"]; 
			if(isset($day_proc_summary_arr[$day_proc_summary["procedureid"]]['appt_cnt']))
			{
				$day_proc_summary_arr[$day_proc_summary["procedureid"]]['appt_cnt']++;
			}
			else
			{
				$day_proc_summary_arr[$day_proc_summary["procedureid"]]['appt_cnt'] = 1;	
			}
		}
		
		$day_proc_final_summary = array();
		$total_appts = 0;
		
		foreach($day_proc_summary_arr as $dkey => $ds_arr)
		{
			$day_proc_final_summary['proc_appt_cnt'][] = $ds_arr;
			$total_appts += $ds_arr['appt_cnt'];			
		}
		
		foreach($day_proc_final_summary['proc_appt_cnt'] as $dkey => $ds_arr)
		{
			$day_proc_final_summary['proc_appt_cnt'][$dkey]['appt_cnt'] = (string) $ds_arr['appt_cnt'];
		}		
		
		$day_proc_final_summary['total_appts'] = (string) $total_appts;
		if($callInternal == 1)
		{
			return $day_proc_final_summary;	
		}
		else
		{
			echo json_encode($day_proc_final_summary);	
		}
	}
	
	public function get_active_sch_dates()
	{
		$this->doctor_id = (int) $_REQUEST["phyId"];
		$this->facilitiesId = $_REQUEST["facilitiesId"];
		$req_fac_arr = explode(',', $this->facilitiesId);
		$month = (int) $_REQUEST["month"];
		$year = (int) $_REQUEST["year"];
		
		$active_sch_dates_arr = array();
				
		$st_ts = mktime(0,0,0,$month, 1, $year);		
		$month_days = date('t', $st_ts);
		$end_ts = $st_ts + (86400*($month_days-1));
		
		$month_days_range = range($st_ts,$end_ts, 86400);
				
		$appt_cnt_main = array();
		$am_appt_cnt = array();
		$pm_appt_cnt = array();
		
		$int_max_cnt = 0;
		$int_warn_percentage = 100;
		$qry_u = "SELECT max_per, max_appoint FROM users WHERE id = ? ";
		$this->db_obj->run_query($qry_u, array($this->doctor_id));
		$qry_u_obj = $this->db_obj->get_qry_result();
		if(count($qry_u_obj) > 0){
			$arr_u = $qry_u_obj[0];
			$int_max_cnt = (!empty($arr_u["max_appoint"])) ? $arr_u["max_appoint"] : 0;
			$int_warn_percentage = (!empty($arr_u["max_per"])) ? $arr_u["max_per"] : 100;
		}				
		
		foreach($month_days_range as $m_day)
		{
			$m_date = date('Y-m-d', $m_day);			
			$get_sch_status_arr = $this->get_provider_schedules($m_date, array($this->doctor_id));			
			if(count($get_sch_status_arr) > 0)
			{	
				$fac_tmp_id = array();							
				foreach($get_sch_status_arr as $sch_tmp_data)
				{					
					if(in_array($sch_tmp_data['facility'], $req_fac_arr))
					{
						$fac_tmp_id[$sch_tmp_data['facility']]['tmp_ids'][] = $sch_tmp_data["sch_tmp_id"];						
					}					
				}
				
				if(count($fac_tmp_id) == 0)
				{
					continue;
				}
				
				$fac_max_appts_arr =  array();
				
				$int_total_secs = 0;
				
				$main_tmp_id_arr = array();
								
				$before_start_time = '12:00:00';
				foreach($fac_tmp_id as $fac_key => $get_tmp_ids)
				{
					$main_tmp_id_arr[] = implode(',', $get_tmp_ids['tmp_ids']);					
				}
				$main_tmp_id_arr_str = implode(',', $main_tmp_id_arr);
				
				$sch_temp_qry = 'SELECT fldLunchStTm,fldLunchEdTm FROM schedule_templates WHERE id IN('.$main_tmp_id_arr_str.')';
				$this->db_obj->run_query($sch_temp_qry);
				$sch_temp_qry_obj = $this->db_obj->get_qry_result();
				
				if(count($sch_temp_qry_obj)> 0)
				{
					foreach($sch_temp_qry_obj as $sch_temp_qry_data)
					{
						$sch_temp_qry_data['fldLunchStTm'] = trim($sch_temp_qry_data['fldLunchStTm']);
						if(isset($sch_temp_qry_data['fldLunchStTm']) && $sch_temp_qry_data['fldLunchStTm'] != "00:00:00")
						{
							$before_start_time = $sch_temp_qry_data['fldLunchStTm'];
							break;	
						}								
					}					
				}
				
				/* Getting the appointments count before afternoon */
				
				$get_appt_cnt_qry = "SELECT COUNT(id) as appt_cnt, sa_facility_id, sa_app_start_date FROM schedule_appointments WHERE sa_facility_id IN (".$this->facilitiesId.") and sa_doctor_id = ? and sa_patient_app_status_id NOT IN (203,201,18,19,20) and sa_app_starttime < '".$before_start_time."' and sa_app_start_date = ? and sa_test_id = 0 GROUP BY sa_app_start_date, sa_facility_id";
				$this->db_obj->run_query($get_appt_cnt_qry, array($this->doctor_id, $m_date));
				$get_appt_cnt_qry_obj = $this->db_obj->get_qry_result();
				
				if(count($get_appt_cnt_qry_obj) > 0)
				{
					foreach($get_appt_cnt_qry_obj as $baac)
					{
						$am_appt_cnt[$baac["sa_app_start_date"]]["appt_cnt"] += $baac["appt_cnt"];
						$appt_cnt_main[$baac["sa_app_start_date"]]["appt_cnt"] += $baac["appt_cnt"];
						$appt_cnt_main[$baac["sa_app_start_date"]][$baac["sa_facility_id"]]["appt_cnt"] += $baac["appt_cnt"];
					}
				}
				
				/* Getting the appointments count after afternoon */
				
				$get_appt_cnt_qry = "SELECT COUNT(id) as appt_cnt, sa_facility_id, sa_app_start_date FROM schedule_appointments WHERE sa_facility_id IN (".$this->facilitiesId.") and sa_doctor_id = ? and sa_patient_app_status_id NOT IN (203,201,18,19,20) and sa_app_starttime >= '".$before_start_time."' and sa_app_start_date = ? and sa_test_id = 0 GROUP BY sa_app_start_date, sa_facility_id";
				$this->db_obj->run_query($get_appt_cnt_qry, array($this->doctor_id, $m_date));
				$get_appt_cnt_qry_obj = $this->db_obj->get_qry_result();
				
				if(count($get_appt_cnt_qry_obj)> 0)
				{
					foreach($get_appt_cnt_qry_obj as $aaac)
					{
						$pm_appt_cnt[$aaac["sa_app_start_date"]]["appt_cnt"] += $aaac["appt_cnt"];
						$appt_cnt_main[$aaac["sa_app_start_date"]]["appt_cnt"] += $aaac["appt_cnt"];
						$appt_cnt_main[$aaac["sa_app_start_date"]][$aaac["sa_facility_id"]]["appt_cnt"] += $aaac["appt_cnt"];
					}
				}								
				
				foreach($fac_tmp_id  as $fac_key => $tmp_ids_data)
				{
					$sch_temp_ids_for_req = implode(',', $tmp_ids_data['tmp_ids']);
					if(trim($sch_temp_ids_for_req) != "")
					{
						$max_appt_qry = "SELECT SUM(schedule_templates.MaxAppointments) as max_appts, TIME_TO_SEC(TIMEDIFF(MAX(morning_end_time), MIN(morning_start_time))) as tmp_time_sec FROM schedule_templates WHERE id IN(".$sch_temp_ids_for_req.")";
						$this->db_obj->run_query($max_appt_qry);
						$max_appt_qry_obj = $this->db_obj->get_qry_result();
						if(count($max_appt_qry_obj) > 0)
						{
							$max_appt_data = $max_appt_qry_obj[0];
							$fac_max_appts_arr[$fac_key] = $max_appt_data["max_appts"];
							$int_total_secs += $max_appt_data["tmp_time_sec"];
						}						
					}
				}
				
				$int_total_slots = $int_total_secs / 60 / DEFAULT_TIME_SLOT;
				
				$sch_bg_date_color = "common";
				$default_flag = 0;								
				foreach($fac_max_appts_arr as $fac_id_key => $pr_fac_max_appts)
				{	
					if($pr_fac_max_appts>0)
					{
						if(round(($appt_cnt_main[$m_date][$fac_id_key]["appt_cnt"]/$pr_fac_max_appts)*100) >= $int_warn_percentage)
						{
							$sch_bg_date_color = "exceed_appt";
							$default_flag = 1;	
							break;
						}
					}
				}
				
				if($default_flag == 0)
				{
					if($int_max_cnt > 0){
						$int_set_cnt = $appt_cnt_main[$m_date]["appt_cnt"];
						if(round(($int_set_cnt / ($int_max_cnt * $int_total_slots)) * 100) >= $int_warn_percentage){
							$sch_bg_date_color = "alert";
						}
					}
				}
				
				$day_have_sch = date('j', $m_day);
				$am_appt_cnt_n = isset($am_appt_cnt[$m_date]["appt_cnt"]) ? $am_appt_cnt[$m_date]["appt_cnt"] : 0;
				$pm_appt_cnt_n = isset($pm_appt_cnt[$m_date]["appt_cnt"]) ? $pm_appt_cnt[$m_date]["appt_cnt"] : 0;
				$day_have_sch = (int) $day_have_sch;
				
				$color_arrayp["common"] = "#dc71ff";
				$color_arrayp["alert"] = "#FF0000";
				$color_arrayp["exceed_appt"] = "#FF0000";
				
				$active_sch_dates_arr[$day_have_sch] = array(
															'day' => $day_have_sch,
															'bgColor' => $color_arrayp[$sch_bg_date_color],
															'apptCnt' => $am_appt_cnt_n.'/'.$pm_appt_cnt_n
														);				
			}
		}
		
		$unread_msg_qry = "SELECT COUNT(pt_msg_id) as unread_count FROM patient_messages  WHERE is_done = 0 and is_read = 0 and del_status = 0 and communication_type = 2";
		$this->db_obj->run_query($unread_msg_qry);
		$unread_msg_obj = $this->db_obj->get_qry_result();
		
		$unread_msg_data_obj = $unread_msg_obj[0];
		$unread_msg_count = $unread_msg_data_obj["unread_count"];		
		
		if($_REQUEST['app']=='android')
		{
			foreach($active_sch_dates_arr as $id=>$val)
			{
				$tmp_data[]=$val;
			}
			$active_sch_dates_arr=$tmp_data;
		}
		$main_ac_sch_unreadPtCnt_arr["calendar"] = $active_sch_dates_arr;
		$main_ac_sch_unreadPtCnt_arr["unread_msg_count"] = $unread_msg_count;	
		if($_REQUEST['pre'])pre($main_ac_sch_unreadPtCnt_arr);//print_r('<p>'.$responseArray.'</p>');
		echo json_encode($main_ac_sch_unreadPtCnt_arr);
	}
	
	public function patient_app_details()
	{
		$patient_appt_data = array();
		$apptId = (int) $_REQUEST["apptId"];
		if($apptId != "")
		{
			$this->apptId = $apptId;
			$appt_qry = "SELECT title, fname, lname, mname, preferr_contact, sa_patient_id as patient_id, DATE_FORMAT(DOB, '%m-%d-%Y') as DOB, street, street2, postal_code, zip_ext, city, state, country_code, phone_home, phone_biz, phone_contact, phone_cell, email, sa.sa_comments as appt_comments, sa.procedureid, sa.sec_procedureid, sa.tertiary_procedureid, primary_care_id, primary_care_phy_id FROM patient_data pd INNER JOIN schedule_appointments sa ON sa.sa_patient_id = pd.id WHERE sa.id = ?";	
			$this->db_obj->run_query($appt_qry, array($this->apptId));
			$appt_qry_obj = $this->db_obj->get_qry_result();
			
			foreach($appt_qry_obj as $appt_data_row)
			{
				if($appt_data_row["title"] != "")
				{
					$appt_data_row["title"] = $appt_data_row["title"]." ";
				}
				if($appt_data_row["mname"] != "")
				{
					$appt_data_row["mname"] = " ".$appt_data_row["mname"];	
				}
				$patient_name = $appt_data_row["title"].$appt_data_row["lname"].", ".$appt_data_row["fname"].$appt_data_row["mname"];
				
				$patient_address = trim(core_address_format($appt_data_row['street'], $appt_data_row['street2'], $appt_data_row['city'], $appt_data_row['state'], $appt_data_row['postal_code']));
				
				$prefer_contact = $appt_data_row["preferr_contact"];
				$prefer_contact_no = '';
				if($prefer_contact == 0)
				{
					$prefer_contact_no = core_phone_format($appt_data_row["phone_home"]);
				}
				else if($prefer_contact == 1)
				{
					$prefer_contact_no = core_phone_format($appt_data_row["phone_biz"]);
				}
				else if($prefer_contact == 2)
				{
					$prefer_contact_no = core_phone_format($appt_data_row["phone_cell"]);
				}
				
				if($prefer_contact_no == "" || $prefer_contact_no == "000-000-0000")
				{
					$prefer_contact_no = ($appt_data_row["phone_home"]!="" && $appt_data_row["phone_home"] != "000-000-0000") ? core_phone_format($appt_data_row["phone_home"]) : (($appt_data_row["phone_biz"]!="" && $appt_data_row["phone_biz"] != "000-000-0000") ? core_phone_format($appt_data_row["phone_biz"]) : (($appt_data_row["phone_cell"]!="" && $appt_data_row["phone_cell"] != "000-000-0000") ? core_phone_format($appt_data_row["phone_cell"]) : "N/A"));
				}
				
				$appt_comments = $appt_data_row["appt_comments"];
				
				$patient_appt_data[] = array(
												"patient_data" => array(
													array("field_name" => "Patient Name", "field_val" => $patient_name),
													array("field_name" => "Patient Id", "field_val" => (string) $appt_data_row["patient_id"]),
													array("field_name" => "DOB", "field_val" => $appt_data_row["DOB"]),
													array("field_name" => "Address", "field_val" => $patient_address),
													//array("field_name" => "Phone Home", "field_val" => $appt_data_row["phone_home"]),
													//array("field_name" => "Phone Work", "field_val" => $appt_data_row["phone_biz"]),
													//array("field_name" => "Mobile", "field_val" => $appt_data_row["phone_cell"]),													
													array("field_name" => "Phone", "field_val" => $prefer_contact_no),
													array("field_name" => "Email", "field_val" => $appt_data_row["email"]),
													array("field_name" => "Referring Phy", "field_val" => $this->getRefPhyName($appt_data_row["primary_care_id"])),
													array("field_name" => "PCP Phy", "field_val" => $this->getRefPhyName($appt_data_row["primary_care_phy_id"]))																																																																													
												),
												"appt_data" => array(
													array("field_name" => "Appointment Comments", "field_val" => $appt_comments=="" ? "" : $appt_comments),
													array("field_name" => "Procedure", "field_val" => $this->doctor_proc_to_default_proc($appt_data_row["procedureid"])),
													array("field_name" => "Sec. Procedure", "field_val" => $this->doctor_proc_to_default_proc($appt_data_row["sec_procedureid"])),
													array("field_name" => "Ter. Procedure", "field_val" => $this->doctor_proc_to_default_proc($appt_data_row["tertiary_procedureid"]))													
												)											
											);
			}
			if($_REQUEST['pre']==1)pre($patient_appt_data);
			else echo json_encode($patient_appt_data);
		}
		else
		{
			echo "APPOINTMENT ID NOT SPECIFIED";	
		}		
	}
	
	public function appt_hx()
	{
		$pat_appt_hx_list_arr = array();
		
		$patId = (int) $_REQUEST["patId"];
		if($patId != "")
		{
			$this->patId = $patId;
			$appt_hx_qry = "SELECT 
							schedule_appointments.sa_patient_id, schedule_appointments.sa_patient_app_status_id, 
							schedule_appointments.id,schedule_appointments.sa_madeby,schedule_appointments.sa_doctor_id,
							schedule_appointments.sa_comments, schedule_appointments.procedureid, 
							facility.name as fac_name, 
							date_format( schedule_appointments.sa_app_time, '%m-%d-%y' ) AS sa_app_time, 
							time_format( sa_app_starttime, '%h:%i %p' ) AS sa_app_starttime, 
							time_format( sa_app_starttime, '%H,%i,00' ) AS sa_app_starttime_js, 
							time_format( sa_app_endtime, '%h:%i %p' ) AS sa_app_endtime,
							date_format( sa_app_start_date, '%m/%d/%y' ) AS sa_app_start_date_disp, 
							date_format( sa_app_start_date, '%Y,%m,%d' ) AS sa_app_start_date_disp_full, 
							slot_procedures.proc, 
							sa_app_start_date as sa_app_start_date_Db, 
							slot_procedures.acronym, schedule_appointments.sa_facility_id  
						FROM 
							schedule_appointments
						LEFT JOIN slot_procedures ON slot_procedures.id = schedule_appointments.procedureid
						LEFT JOIN facility ON facility.id = schedule_appointments.sa_facility_id 
						WHERE schedule_appointments.sa_patient_id = ?  
						ORDER BY schedule_appointments.sa_app_start_date DESC 
						";	
						
			$this->db_obj->run_query($appt_hx_qry, array($this->patId));
			$appt_hx_qry_obj = $this->db_obj->get_qry_result();	
			
			foreach($appt_hx_qry_obj as $pat_appt_hx_list)
			{
				$backgroud_color = "#ffffff";
				$color = "#000000";
				
				if($pat_appt_hx_list['sa_patient_app_status_id'] == '18' || $pat_appt_hx_list['sa_patient_app_status_id'] == '203'){
					$backgroud_color = "#CCCCCC";
					$color = "#FF0000";					
				}elseif($pat_appt_hx_list['sa_patient_app_status_id'] == '3'){					
					$backgroud_color = "#f3f3f3";
					$color = "#FF8000";						
				}elseif($pat_appt_hx_list['sa_patient_app_status_id'] == '201'){
					$backgroud_color = "#FFFF00";
				}					
		
												
				$pat_appt_hx_list_arr[] = array(
												"appt_date_time" => $pat_appt_hx_list["sa_app_start_date_disp"]." ".$pat_appt_hx_list["sa_app_starttime"],
												"appt_comments" => $pat_appt_hx_list['sa_comments'],
												"appt_procedure" => $pat_appt_hx_list['proc'],
												"doctor" => $this->getProvider_name($pat_appt_hx_list["sa_doctor_id"], "full"),
												"facility" => $this->getFacilityNameInitial($pat_appt_hx_list["fac_name"]),
												"row_color" => $color,
												"row_bg_color" => $backgroud_color
											);
			}
			if($_REQUEST['pre']==1)pre($pat_appt_hx_list_arr);
			
			
			
			$pat_appt_hx_list_arr = json_encode($pat_appt_hx_list_arr);
				


			echo $pat_appt_hx_list_arr;
		}		
	}	

	public function appt_hx_detail()
	{
		$return_appt_hx_data = array();
		
		$apptId = (int) $_REQUEST["apptId"];
		$patId = (int) $_REQUEST["patId"];
		$this->patId = $patId;
		if($apptId != "" && $patId != "")
		{
			$this->apptId = $apptId;
			$appt_hx_qry = "SELECT 
								ps.sch_id, ps.dateTime, ps.patient_id, TIME_FORMAT(ps.status_time, '%h:%i %p') as status_time, DATE_FORMAT(ps.status_date, '%m-%d-%Y') as status_date, 
								ps.status, ps.old_status, 
								ps.statusComments, ps.oldStatusComments, 
								DATE_FORMAT(ps.new_appt_date,'%m/%d/%y') AS new_appt_date, DATE_FORMAT(ps.old_date,'%m/%d/%y') AS old_appt_date, 
								TIME_FORMAT(ps.new_appt_start_time,'%h:%i %p') AS new_appt_start_time, TIME_FORMAT(ps.old_time,'%h:%i %p') AS old_appt_start_time, 
								TIME_FORMAT(ps.new_appt_end_time,'%h:%i %p') AS new_appt_end_time, TIME_FORMAT(ps.old_appt_end_time,'%h:%i %p') AS old_appt_end_time, 
								ps.new_facility, ps.old_facility, 
								ps.new_provider, ps.old_provider, 
								ps.new_procedure_id, ps.old_procedure_id, 
								ps.statusChangedBy, ps.oldMadeBy, 
								oldsp.proc AS oldProc, newsp.proc AS newProc,
								oldsp.acronym AS oldProcA, newsp.acronym AS newProcA,
								oldf.name AS oldFac, newf.name AS newFac, 
								oldU.fname AS oldProvFN, newU.fname AS newProvFN,  							
								oldU.lname AS oldProvLN, newU.lname AS newProvLN,
								change_reason 
								FROM 
								previous_status AS ps 
								LEFT JOIN slot_procedures AS oldsp ON oldsp.id = ps.old_procedure_id 
								LEFT JOIN slot_procedures AS newsp ON newsp.id = ps.new_procedure_id 
								LEFT JOIN facility AS oldf ON oldf.id = ps.old_facility 
								LEFT JOIN facility AS newf ON newf.id = ps.new_facility 
								LEFT JOIN users AS oldU ON oldU.id = ps.old_provider 
								LEFT JOIN users AS newU ON newU.id = ps.new_provider 
								WHERE 
									ps.sch_id = ? 
									AND ps.status IN ( 1, 3, 5, 6, 11, 18, 13,17,2,0,21,22,23,'', 201,202,203) 
									AND ps.patient_id = ?  
								ORDER BY ps.id DESC";
								
			$this->db_obj->run_query($appt_hx_qry, array($this->apptId, $this->patId));
			$appt_hx_qry_obj = $this->db_obj->get_qry_result();			 
			foreach($appt_hx_qry_obj as $appt_hx_data)
			{
				$st_id=$appt_hx_data['status'];			
				
				if($st_id == 201){
					$stttt_us = "Moved to ToDo";
				}elseif($st_id == 18)	{
					$stttt_us = "Cancelled";
				}else if($st_id=='')	{
					$stttt_us = "Deleted";
				}else if($st_id == '0'){
					if($intZeroCnt > 1){
						$stttt_us = "Reset";
						if($intResetCnt > 0){
							$stttt_us = "Created";
						}
					}else{
						$stttt_us = "Created";
					}
					$intResetCnt++;
				}elseif ($st_id == 202){
					$stttt_us = "Rescheduled";
				}
				elseif($st_id == 203)
				{
					$stttt_us = "Deleted";			
				}
				else{
					$vquery_st = "SELECT status_name FROM `schedule_status` WHERE id = ?";
					$this->db_obj->run_query($vquery_st, array($st_id));
					$vquery_st_obj = $this->db_obj->get_qry_result();
					$rs_st = $vquery_st_obj[0];
					$stttt_us=$rs_st['status_name'];
				}								
				
				$return_appt_hx_data[] = array(
												"status_name" => $stttt_us,	
												"status_date" => $appt_hx_data["status_date"],
												"status_time" => $appt_hx_data["status_time"],
												"change_reason" => $appt_hx_data["change_reason"],
												"operator_name" => $this->getOperatorInitialByUsername($appt_hx_data["statusChangedBy"]),
												"old_details" => array(
																	"appt_date" => $appt_hx_data["old_appt_date"],
																	"appt_start_time" => $appt_hx_data["old_appt_start_time"],
																	"provider" => $appt_hx_data['oldProvFN']." ".$appt_hx_data['oldProvLN'],
																	"facility" => $appt_hx_data['oldFac'],
																	"procedure" => $appt_hx_data["oldProcA"],
																	"comments" => $appt_hx_data["oldStatusComments"]
																),
												"new_details" => array(
																	"appt_date" => $appt_hx_data["new_appt_date"],
																	"appt_start_time" => $appt_hx_data["new_appt_start_time"],
																	"provider" => $appt_hx_data['newProvFN']." ".$appt_hx_data['newProvLN'],
																	"facility" => $appt_hx_data['newFac'],
																	"procedure" => $appt_hx_data["newProcA"],
																	"comments" => $appt_hx_data["statusComments"]
																)
											);				
			}
			
			echo json_encode($return_appt_hx_data);
		}
	}
	
	public function dr_schedule()
	{
		$doctor_id = trim($_REQUEST["phyId"]);
		$doctor_id = (int) $doctor_id;
		$this->doctor_id = $doctor_id;
		$facilitiesId = trim($_REQUEST["facilitiesId"]);
		$this->facilitiesId = $facilitiesId;
		$facilities_arr = explode(",", $facilitiesId);
		$reqDate = trim($_REQUEST["reqDate"]);
		$this->reqDate = $reqDate;
		
		if($doctor_id != "" && $facilitiesId != "" && $reqDate != "" && count($facilities_arr) > 0)
		{		
			$dr_schedule_arr = array();
			$dr_schedule_arr["sch_exists"] = "no";				
			
			/* Schedule Exists or not */
			$prov_arr = $this->get_provider_schedules($reqDate, array($doctor_id));
			$prov_sch_count = count($prov_arr);
			$prov_arr_available = array();
			$template_ids = array();
			$tempate_fac_arr = array();

			if($prov_sch_count > 0)
			{
				foreach($prov_arr as $prov_arr_row)
				{
					if(in_array($prov_arr_row["facility"], $facilities_arr))
					{
						$prov_arr_available[] = $prov_arr_row;
						$template_ids[] = $prov_arr_row["sch_tmp_id"];
						$tempate_fac_arr[$prov_arr_row["sch_tmp_id"]] = $prov_arr_row["facility"];
					}
				}				
			}	
													
			if(count($prov_arr_available) > 0)
			{
				$facilities_color_data = array();
				/* Facilities array based on the available colors */
				$req_qry = "SELECT id, name, facility_color FROM facility";
				$this->db_obj->run_query($req_qry);				
				$qry_obj = $this->db_obj->get_qry_result();
				foreach($qry_obj as $fac_color_data)
				{
					$facilities_color_data[$fac_color_data["id"]]["facility_color"] = $fac_color_data["facility_color"];
					$facilities_color_data[$fac_color_data["id"]]["fac_name"] = $fac_color_data["name"];
				}

				/* Schedule setting for the iPhone */
				$dr_schedule_arr["settings"] = $this->get_sch_settings($template_ids);
			
				$dr_schedule_arr["sch_exists"] = "yes";	
				/* Schedule template data */				
				$dr_schedule_arr["template_data"] = $this->get_template_data($template_ids);
				foreach ($dr_schedule_arr["template_data"] as $tmp_data_key => $tmp_data) {
					$dr_schedule_arr["template_data"][$tmp_data_key]["facility"] = $facilities_color_data[$tempate_fac_arr[$tmp_data["id"]]]["fac_name"];
					$dr_schedule_arr["template_data"][$tmp_data_key]["facility_color"] = trim($facilities_color_data[$tempate_fac_arr[$tmp_data["id"]]]["facility_color"]) == "" ? "#9d9a8b" : $facilities_color_data[$tempate_fac_arr[$tmp_data["id"]]]["facility_color"];					
				}
				//$dr_schedule_arr["template_label_data"] = $this->get_template_label_data($template_ids);
				//$dr_schedule_arr["appt_data"] = $this->get_schedule_appt_data();

				$range = range(strtotime($this->temp_start_time),strtotime($this->temp_end_time) - (DEFAULT_TIME_SLOT*60), DEFAULT_TIME_SLOT*60);
				$temp_label_data = $this->get_template_label_data($template_ids, $range);
				$appt_data = $this->get_schedule_appt_data();
				
				//getting custom labels
				$arr_custom_labels = array();
				$qry_cl = "select l_type, l_text, l_show_text, l_color, start_date, start_time, labels_replaced from scheduler_custom_labels where facility IN (".$str_process_fac.") and provider = '".$pr_id."' and start_date = '".$pr_detail["dt"]."' order by start_time";					
				$res_cl = imw_query($qry_cl);
				if(imw_num_rows($res_cl) > 0){							
					$arr_cl = array();
					while($row = imw_fetch_assoc($res_cl)){
						$arr_cl[] = $row;
					}
					if(count($arr_cl) > 0){
						foreach($arr_cl as $this_cl){
							$arr_custom_labels[$this_cl["start_time"]] = $this_cl;
						}
					}
				}	
						
				$appt_label_data = array();	
				$android_app_key=0;
				//refine data arry in case of android for template timing first
				if($_REQUEST['app']=='android')
				{
					foreach ($dr_schedule_arr["template_data"] as $tmp_data_key => $tmp_data) 
					{
						unset($temp_range);
						$temp_range = range(strtotime($tmp_data["temp_start_time"]),strtotime($tmp_data["temp_end_time"]) - (DEFAULT_TIME_SLOT*60), DEFAULT_TIME_SLOT*60);
						foreach($temp_range as $time)
						{
							$temp_final_key = date("h:i A",$time);
							if(!array_key_exists($temp_final_key, $appt_data) && $is_repeated==false)	
							{
								unset($tmpArr);
								$tmpArr['appt_id'] = '~~~~';
								$tmpArr['patient_id'] = '~~~~';
								$tmpArr['patient_name'] = '~~~~';
								$tmpArr['start_time'] = '~~~~';
								$tmpArr['end_time'] = '~~~~';
								$tmpArr['proc_color'] = '~~~~';
								$tmpArr['status'] = '~~~~';
								
								$appt_data[$temp_final_key][]=$tmpArr;
							}
							if(!array_key_exists($temp_final_key, $temp_label_data))
							{
								unset($tmpArr);
								$tmpArr['label_name'] = '~~~~';
								$tmpArr['label_type'] = '~~~~';
								$tmpArr['label_color'] ='~~~~';
								$tmpArr['start_time'] = '~~~~';//$final_key
								$tmpArr['end_time'] = '~~~~';
								$temp_label_data[$temp_final_key][]=$tmpArr;
							}
						}
					}
				}
				foreach($range as $time){
					$final_key = date("h:i A",$time);
					if($_REQUEST['app']=='android')
					{
						$is_repeated=false;
						//check for previous appts to repeat
						if(sizeof($repeatArr['count'])>0)
						{
							//save orignal appt array to set it on last position
							$appt_data_temp=$appt_data[$final_key];
							//unset this array to remove keys values
							unset($appt_data[$final_key]);
							foreach($repeatArr['count'] as $key=>$val)
							{
								if($val>=1)
								{
									$is_repeated=true;
									$appt_data[$final_key][]=$repeatArr['arr'][$key];
									$val-=1;
									$repeatArr['count'][$key]=$val;	
								}
							}
							//add up skipped appts by assigning them new keys
							if(sizeof($appt_data_temp)>=1)
							{
								foreach($appt_data_temp as $key=>$val)
								{
									$appt_data[$final_key][]=$val;	
								}
							}
						}
						//check is from current time slot do we need to repeat any appt
						if(array_key_exists($final_key, $appt_data))	
						{
							foreach($appt_data[$final_key] as $tmpDataArr)
							{
								if($final_key==$tmpDataArr["start_time"])
								{
									$repeat=$appt_duration =0;
									$appt_duration = (strtotime($tmpDataArr["end_time"]) - strtotime($tmpDataArr["start_time"])) / 60;
									if($appt_duration>DEFAULT_TIME_SLOT){
										$repeat=intval($appt_duration/DEFAULT_TIME_SLOT);
										if($repeat>1)
										{
											$repeatArr['count'][]=$repeat-1;
											$repeatArr['arr'][]=$tmpDataArr;	
										}
									}
								}
							}
						}
			
						if(!array_key_exists($final_key, $appt_data) && $is_repeated==false)	
						{
							unset($tmpArr);
							$tmpArr['appt_id'] = '~~CLOSED~~';
                            $tmpArr['patient_id'] = '~~CLOSED~~';
                            $tmpArr['patient_name'] = '~~CLOSED~~';
                            $tmpArr['start_time'] = '~~CLOSED~~';
                            $tmpArr['end_time'] = '~~CLOSED~~';
                            $tmpArr['proc_color'] = '~~CLOSED~~';
                            $tmpArr['status'] = '~~CLOSED~~';
							
                            $appt_data[$final_key][]=$tmpArr;
						}
						if(!array_key_exists($final_key, $temp_label_data))
						{
							unset($tmpArr);
							$tmpArr['label_name'] = '~~CLOSED~~';
							$tmpArr['label_type'] = '~~CLOSED~~';
							$tmpArr['label_color'] ='~~CLOSED~~';
							$tmpArr['start_time'] = '~~CLOSED~~';//$final_key
							$tmpArr['end_time'] = '~~CLOSED~~';
							$temp_label_data[$final_key][]=$tmpArr;
						}
					}
					if(array_key_exists($final_key, $temp_label_data) || array_key_exists($final_key, $appt_data))
					{
						if(array_key_exists($final_key, $temp_label_data) && array_key_exists($final_key, $appt_data))
						{
							foreach($temp_label_data[$final_key] as $key => $temp_data_lbl)
							{
								if(trim($temp_data_lbl['label_type']) == "" || strtolower(trim($temp_data_lbl['label_type'])) == "reserved")
								{
									unset($temp_label_data[$final_key][$key]);	
								}
							}
						}
						if($_REQUEST['app']=='android')
						{
							//$key=key($appt_label_data);	
							$appt_label_data[$android_app_key] = array(
													"appt_data_$android_app_key" => $appt_data[$final_key],
													"label_data_$android_app_key" => $temp_label_data[$final_key]
												);
							$android_app_key++;
						}
						else
						{
							$appt_label_data[] = array(
													"appt_data" => $appt_data[$final_key],
													"label_data" => $temp_label_data[$final_key]
												);		
						}
					}					
				}
				
				$dr_schedule_arr["label_appt_data"] = $appt_label_data; 
			}
			$dr_schedule_arr["day_summary"] = $this->day_proc_summary(1);
			if($_REQUEST['pre']==1){
				pre($dr_schedule_arr,1);	
			}
			//android specific changes
			if($_REQUEST['app']=='android')
			{echo str_replace('null','~~~~',json_encode($dr_schedule_arr));}
			else
			{echo json_encode($dr_schedule_arr);}			
		}
	}

	private function getProvider_name($id, $mode=""){
		$qry = "SELECT fname,lname FROM `users` WHERE id= ?";
		$this->db_obj->run_query($qry, array($id));
		$qry_obj = $this->db_obj->get_qry_result();
		$provider_data = $qry_obj[0];
		
		$fname = $provider_data["fname"];
		$lname = $provider_data["lname"];
		
		if($mode == "tiny"){
			return strtoupper(substr($fname,0,1)).strtoupper(substr($lname,0,1));
		}else{
			return $fname." ".$lname;
		}
	}
	
	private function getRefPhyName($id)
	{
		$strName = '';
		if($id != 0 && $id != ""){
			$qry = "select Title,FirstName,MiddleName,LastName from refferphysician where physician_Reffer_id = ? ";
			$this->db_obj->run_query($qry, array($id));
			$qry_obj = $this->db_obj->get_qry_result();
			if(count($qry_obj)> 0){
				$row = $qry_obj[0];
				if(!isset($GLOBALS['REF_PHY_FORMAT']) || strtolower($GLOBALS['REF_PHY_FORMAT']) != 'boston'){
					$strName .= $row['Title'] != '' ? trim($row['Title']).' ':'';
					$strName .= $row['LastName'] != '' ? trim($row['LastName']).', ':'';
					$strName .= $row['FirstName'] != '' ? trim($row['FirstName']).' ':'';
					$strName .= $row['MiddleName'] != '' ? trim($row['MiddleName']):'';
				}
				else if(isset($GLOBALS['REF_PHY_FORMAT']) && strtolower($GLOBALS['REF_PHY_FORMAT']) == 'boston'){
					$strName .= $row['LastName'] != '' ? trim($row['LastName']).', ':'';
					$strName .= $row['FirstName'] != '' ? trim($row['FirstName']).' ':'';
					$strName .= $row['MiddleName'] != '' ? trim($row['MiddleName']).' ':'';
					$strName .= $row['Title'] != '' ? trim($row['Title']):'';
				}
			}
		}
		return $strName;
	}	
	
	private function doctor_proc_to_default_proc($appt_procedure_id){

		$proc_id_qry = "SELECT procedureId FROM slot_procedures WHERE id = ? ";								
		$this->db_obj->run_query($proc_id_qry, array($appt_procedure_id));
		$proc_id_qry_obj = $this->db_obj->get_qry_result();			 		

		if(count($proc_id_qry_obj) > 0){
			$proc_id_data = $proc_id_qry_obj[0];
		}
		if($proc_id_data["procedureId"] == 0){
			$default_procedure_id = $appt_procedure_id;
		}else{
			$default_procedure_id = $proc_id_data["procedureId"];
		}
		
		$main_proc_name_qry = "SELECT id, proc FROM slot_procedures WHERE id = ?";
		$this->db_obj->run_query($main_proc_name_qry, array($default_procedure_id));
		$main_proc_name_qry_obj = $this->db_obj->get_qry_result();	
		$main_proc_data = $main_proc_name_qry_obj[0];
		
		$proc_name = $main_proc_data["proc"];	 				
		$proc_name = $proc_name == "" ? "" : $proc_name;
		return $proc_name;
	}	
	
	private function getOperatorInitialByUsername($strUsername){
		if(trim($strUsername) != ""){
			$strQry = "SELECT fname, lname FROM users WHERE username = ? ";
			$this->db_obj->run_query($strQry, array($strUsername));
			$strQry_obj = $this->db_obj->get_qry_result();
			$arrData = $strQry_obj[0];
			return strtoupper(substr($arrData['fname'],0,1)).strtoupper(substr($arrData['lname'],0,1));	
		}else{
			return "";
		}
	}
	
	private function getFacilityNameInitial($name){
		$arrName = explode(" ",$name);
		$intNameParts = count($arrName);
		if($intNameParts <= 0){
			return "";
		}elseif($intNameParts == 1){
			return strtoupper(substr($arrName[0],0,2));
		}else{
			return strtoupper(substr($arrName[0],0,1)).strtoupper(substr($arrName[1],0,1));
		}
	}		
	
	private function get_sch_settings($template_ids_arr)
	{
		$sch_settings_arr = array();
		$sch_settings_arr["DEFAULT_TIME_SLOT"] = DEFAULT_TIME_SLOT; 		
		$sch_settings_arr["DEFAULT_OFFICE_CLOSED_COLOR"] = DEFAULT_OFFICE_CLOSED_COLOR;
		
		/* Getting schedule start time and end time */
		$template_ids = implode(",", $template_ids_arr);
		//$req_qry = "SELECT DATE_FORMAT(min(morning_start_time), '%h:%i %p') as sch_start_time, DATE_FORMAT(min(morning_start_time), '%H:%i') as sch_st, DATE_FORMAT(max(morning_end_time), '%h:%i %p') as sch_end_time, DATE_FORMAT(max(morning_end_time), '%H:%i') as sch_et FROM schedule_templates WHERE id IN(".$template_ids.")";
		/*$req_qry = "SELECT DATE_FORMAT(morning_start_time, '%h:%i %p') as sch_start_time, DATE_FORMAT(morning_start_time, '%H:%i') as sch_st, DATE_FORMAT(morning_end_time, '%h:%i %p') as sch_end_time, DATE_FORMAT(morning_end_time, '%H:%i') as sch_et FROM schedule_templates WHERE id IN(".$template_ids.")";
		$this->db_obj->run_query($req_qry);
		$sch_timing_data_obj = $this->db_obj->get_qry_result();		
		$sch_timing_data = $sch_timing_data_obj[0];
		$sch_settings_arr["SCH_START_TIME"] = $sch_timing_data["sch_start_time"];
		$sch_settings_arr["SCH_END_TIME"] = $sch_timing_data["sch_end_time"];
		
		$this->temp_start_time = $sch_timing_data["sch_st"];
		$this->temp_end_time = $sch_timing_data["sch_et"];*/				
		
		$req_qry = "SELECT 
			MIN( morning_start_time ) AS sch_start_time, 
			MAX( morning_end_time ) AS sch_end_time
			FROM schedule_templates
			WHERE id IN(".$template_ids.")";
		$this->db_obj->run_query($req_qry);
		$sch_timing_data_obj = $this->db_obj->get_qry_result();		
		$sch_timing_data = $sch_timing_data_obj[0];
		
		list($mrng_hh,$mrng_mm,$mrng_sec)=explode(":",$sch_timing_data["sch_start_time"]);
		list($evng_hh,$evng_mm,$evng_sec)=explode(":",$sch_timing_data["sch_end_time"]);
		$sch_settings_arr["SCH_START_TIME"] = date("h:i A",mktime($mrng_hh,$mrng_mm,$mrng_sec));
		$sch_settings_arr["SCH_END_TIME"] = date("h:i A",mktime($evng_hh,$evng_mm,$evng_sec));
		
		$this->temp_start_time = date("H:i",mktime($mrng_hh,$mrng_mm,$mrng_sec));
		$this->temp_end_time = date("H:i",mktime($evng_hh,$evng_mm,$evng_sec));	
		
		/* Provider Default Color */
		$provider_default_color = "SELECT provider_color FROM users WHERE id = ? ";
		$this->db_obj->run_query($provider_default_color, array($this->doctor_id));
		$pdc_data_obj = $this->db_obj->get_qry_result();		
		$provider_color_data = $pdc_data_obj[0];
		$default_provider_color = $provider_color_data["provider_color"];
		$default_provider_color = trim($default_provider_color) == "" ? "#FFFFFF" : $default_provider_color;
		$sch_settings_arr["DEFAULT_PROVIDER_COLOR"] = $default_provider_color;
				
		return $sch_settings_arr;
	}
	
	private function get_template_data($template_ids_arr)
	{
		$sch_template_data = array();
		
		$template_ids = implode(",", $template_ids_arr);
		$req_qry = "SELECT id, schedule_name as temp_name, DATE_FORMAT(morning_start_time, '%h:%i %p') as temp_start_time, DATE_FORMAT(morning_end_time, '%h:%i %p') as temp_end_time, DATE_FORMAT(fldLunchStTm, '%h:%i %p') as lunch_start_time, DATE_FORMAT(fldLunchEdTm, '%h:%i %p') as lunch_end_time, MaxAppointments as maxAppointments FROM schedule_templates WHERE id IN(".$template_ids.")";
		$this->db_obj->run_query($req_qry);
		$temp_data = $this->db_obj->get_qry_result();		
		
		if(count($temp_data) > 0)
		{
			foreach($temp_data as $temp_data_row)
			{
				$sch_template_data[] =  $temp_data_row;
			}
		}
		
		return $sch_template_data;
	}
	
	private function get_template_label_data($template_ids_arr, $time_range)
	{
		$sch_template_label_data = array();

		/* Getting the labels data color */
		$proc_color_arr = array();
		$proc_color_qry = "SELECT acronym, proc_color FROM slot_procedures WHERE active_status = 'yes' and doctor_id = 0";
		$this->db_obj->run_query($proc_color_qry);
		$proc_data = $this->db_obj->get_qry_result();
		foreach($proc_data as $proc_data_row)
		{
			$proc_color_arr[$proc_data_row["acronym"]] = $proc_data_row["proc_color"];
		}		
		
		/* Getting scheduler custom labels */
		$custom_label_data_arr = array();
		$custom_label_qry = "SELECT TIME_FORMAT(start_time, '%h:%i %p') as c_start_time, TIME_FORMAT(end_time, '%h:%i %p') as c_end_time, l_type, l_text, l_show_text, l_color FROM scheduler_custom_labels WHERE start_date = ? and provider = ?  and facility IN(".$this->facilitiesId.") ORDER BY start_time";
		$this->db_obj->run_query($custom_label_qry, array($this->reqDate, $this->doctor_id));
		$custom_label_data_obj = $this->db_obj->get_qry_result();
		foreach($custom_label_data_obj as $custom_data_row)
		{	
			$custom_label_data_arr[$custom_data_row["c_start_time"]] = $custom_data_row;					
		}
		
		$template_ids = implode(",", $template_ids_arr);
		$req_qry = "SELECT template_label, label_type, label_color, TIME_FORMAT(start_time,'%H%i') as st_key, TIME_FORMAT(start_time, '%h:%i %p') as start_time, TIME_FORMAT(end_time, '%h:%i %p') as end_time FROM schedule_label_tbl WHERE sch_template_id IN(".$template_ids.") ORDER BY st_key ASC";
		$this->db_obj->run_query($req_qry);
		$temp_label_data = $this->db_obj->get_qry_result();
		$template_label_data_arr = array();
		
		foreach($temp_label_data as $tldr)
		{
			$target_key = $tldr["start_time"];			
			$template_label_data_arr[$target_key] = $tldr;			
		}
		
		// getting the block/lock times
		
		$blk_lock_qry = "SELECT * FROM block_times WHERE start_date = ? and provider = ? and facility IN(".$this->facilitiesId.") and (time_status = 'block' || time_status = 'lock')";
		$this->db_obj->run_query($blk_lock_qry, array($this->reqDate, $this->doctor_id));
		$block_lock_data = $this->db_obj->get_qry_result();
		$block_lock_arr = array();
		if(count($block_lock_data) > 0)
		{
			foreach($block_lock_data as $blk_lck_data)
			{
				$block_lock_arr[] = $blk_lck_data; 
			}			
		}
		
		$block_lock_count = count($block_lock_arr);
		
		foreach($time_range as $time){
			$time_lbl_exists = 0;
			$end_time_slot = $time+(DEFAULT_TIME_SLOT*60);						
			$end_time_slot_val = date("h:i A", $end_time_slot);
			$target_key = date("h:i A", $time);
			
			
			$is_block_slot = 0;
			if($block_lock_count > 0)
			{
				$start_slot_ref = date_create_from_format('h:i A', $target_key);
				$end_slot_ref = date_create_from_format('h:i A', $end_time_slot_val);				

				foreach($block_lock_arr as $block_lock_arr_row)
				{
					$start_time_bl = $block_lock_arr_row["start_time"];
					$end_time_bl = $block_lock_arr_row["end_time"];
					
					$start_bl_ref = date_create_from_format('H:i:s', $start_time_bl);
					$end_bl_ref = date_create_from_format('H:i:s', $end_time_bl);
					
					if($start_slot_ref >= $start_bl_ref && $end_slot_ref <= $end_bl_ref)
					{
						$tmp_label = $block_lock_arr_row["b_desc"];
						$label_type = "block_lock";
						$label_color = "#000000";
						$sch_template_label_data[$target_key][] = array(
															"label_name" => $tmp_label,
															"label_type" => $label_type,
															"label_color" => $label_color,
															"start_time" => $target_key,
															"end_time" => $end_time_slot_val
														);
						$is_block_slot = 1;
						break;																			
					}
				}
			}
			
			if($is_block_slot == 1)
			{
				continue;	
			}
			
			if(isset($custom_label_data_arr[$target_key]))
			{
				$template_label = $custom_label_data_arr[$target_key]["l_show_text"];
				$label_type = $custom_label_data_arr[$target_key]["l_type"];
				$label_color = $custom_label_data_arr[$target_key]["l_color"];
				if(strtolower($label_type) == "information" && $label_color == "")
				{
					$label_color = '#ADCEEB';	
				}
				$target_end_time = $custom_label_data_arr[$target_key]["c_end_time"];
				$time_lbl_exists = 1;
			}
			else if(isset($template_label_data_arr[$target_key]))
			{
				$template_label = $template_label_data_arr[$target_key]["template_label"];
				$label_type = $template_label_data_arr[$target_key]["label_type"];
				if($label_type == "Procedure")
				{
					$label_color = $proc_color_arr[$template_label_data_arr[$target_key]["template_label"]] == "" ? $template_label_data_arr[$target_key]["label_color"] :  $proc_color_arr[$template_label_data_arr[$target_key]["template_label"]];					
				}
				else
				{
					$label_color = $template_label_data_arr[$target_key]["label_color"];
					if(strtolower($label_type) == "information" && $label_color == "")
					{
						$label_color = '#ADCEEB';	
					}										
				}
				
				$target_end_time = $template_label_data_arr[$target_key]["end_time"];
				$time_lbl_exists = 1;
			}
			
			if($time_lbl_exists == 1)
			{
				$label_color = trim($label_color);							
							
				$template_label_arr = explode(";", $template_label);
				
				foreach($template_label_arr as $tmp_label)
				{				
					$tmp_label = trim($tmp_label);
					if(strtolower($label_type) == "procedure")
					{
						$label_color = $proc_color_arr[$tmp_label];
					}
					
					if(trim($tmp_label) != "")
					{
						$sch_template_label_data[$target_key][] = array(
															"label_name" => $tmp_label,
															"label_type" => $label_type,
															"label_color" => $label_color,
															"start_time" => $target_key,
															"end_time" => $target_end_time
														);							
					}					
				}					
			}
		}		
		
		return $sch_template_label_data;		
	}
	
	private function get_schedule_appt_data()
	{
		$schedule_appt_data = array();
		
		$arr_all_proc = array();
		$sql_proc = "SELECT id, acronym, proc, proc_color, procedureId FROM slot_procedures WHERE proc != ''";
		$this->db_obj->run_query($sql_proc);
		$res_proc = $this->db_obj->get_qry_result();
		if(count($res_proc) > 0){
			foreach($res_proc as $this_proc)	
			{
				$arr_all_proc[$this_proc["id"]]["acronym"] = $this_proc["acronym"];
				$arr_all_proc[$this_proc["id"]]["name"] = $this_proc["proc"];
				$arr_all_proc[$this_proc["id"]]["color"] = $this_proc["proc_color"];
				$arr_all_proc[$this_proc["id"]]["procedureId"] = $this_proc["procedureId"];				
			}
		}		
		$arr_room=$arr_room_sch=array();
		$sel_room="select app_room,patientId,sch_id from patient_location WHERE cur_date='".$this->reqDate."' ORDER BY patient_location_id ASC";
		$res_room=imw_query($sel_room);
		while($row_room=imw_fetch_assoc($res_room)){
			$patientId=$row_room['patientId'];
			$app_room=$row_room['app_room'];
			$app_room_sch=$row_room['sch_id'];
			$arr_room[$patientId]=$app_room;
			if($app_room_sch>0){
				$arr_room_sch[$app_room_sch]=$app_room;
			}
		}
		
		$appt_qry_params = "";
		if(constant("ENABLE_REAL_ELIGILIBILITY") == "YES"){
			$appt_qry_params .= " rtme.transection_error as elTransectionError, rtme.EB_responce as elEBLoopResp, ";
		}		
		$appt_qry = "SELECT ".$appt_qry_params." sa.id as appt_id, sa.sa_patient_app_status_id as status_code, sa.EMR, sa.iolinkPatientId, sa.iolinkPatientWtId, sa.procedure_site as site, sa.procedureid, sa.sec_procedureid, sa.tertiary_procedureid, sa_patient_id as patient_id, sa_patient_name as patient_name, TIME_FORMAT(sa_app_starttime, '%h:%i %p') as start_time, TIME_FORMAT(sa_app_endtime, '%h:%i %p') as end_time FROM schedule_appointments sa  " ;
		if(constant("ENABLE_REAL_ELIGILIBILITY") == "YES"){
			$appt_qry .= " LEFT JOIN real_time_medicare_eligibility as rtme ON rtme.id = sa.rte_id ";
		}		
		$appt_qry .= "WHERE sa.sa_patient_app_status_id NOT IN (18,203,201) and sa_app_start_date = ? and sa_doctor_id = ? and sa_facility_id IN(".$this->facilitiesId.") ORDER BY sa_app_starttime";
		$this->db_obj->run_query($appt_qry, array($this->reqDate, $this->doctor_id));
		$appt_data_obj = $this->db_obj->get_qry_result();		
		foreach($appt_data_obj as $appt_data_row )
		{//DEFAULT_TIME_SLOT
			if($_REQUEST['app']=='android')
			{
				$appt_data_row['appt_id']='"'.$appt_data_row[appt_id].'"';
				$appt_data_row["patient_id"]='"'.$appt_data_row[patient_id].'"';
			}
			else
			{
				$appt_data_row['appt_id']=$appt_data_row[appt_id];
				$appt_data_row["patient_id"]=$appt_data_row[patient_id];	
			}
			$appt_data_row["proc_color"] = $arr_all_proc[$appt_data_row["procedureid"]]["color"];
			$proc_arr = array();
			
			if($arr_all_proc[$appt_data_row["procedureid"]]["procedureId"] == 0)
			{
				$proc_arr[] = $arr_all_proc[$appt_data_row["procedureid"]]["acronym"];	
			}
			else
			{
				$procedureid = $arr_all_proc[$appt_data_row["procedureid"]]["procedureId"];
				$proc_arr[] = $arr_all_proc[$procedureid]["acronym"];		
			}
			
			if(trim($appt_data_row["sec_procedureid"]) != "" && trim($appt_data_row["sec_procedureid"]) != 0)
			{
				if($arr_all_proc[$appt_data_row["sec_procedureid"]]["procedureId"] == 0)
				{				
					$proc_arr[] = $arr_all_proc[$appt_data_row["sec_procedureid"]]["acronym"];
				}
				else
				{
					$procedureid = $arr_all_proc[$appt_data_row["sec_procedureid"]]["procedureId"];
					$proc_arr[] = $arr_all_proc[$procedureid]["acronym"];		
				}				
				
				if(trim($appt_data_row["tertiary_procedureid"]) != "" && trim($appt_data_row["tertiary_procedureid"]) != 0)
				{
					if($arr_all_proc[$appt_data_row["tertiary_procedureid"]]["procedureId"] == 0)
					{				
						$proc_arr[] = $arr_all_proc[$appt_data_row["tertiary_procedureid"]]["acronym"];
					}
					else
					{
						$procedureid = $arr_all_proc[$appt_data_row["tertiary_procedureid"]]["procedureId"];
						$proc_arr[] = $arr_all_proc[$procedureid]["acronym"];		
					}
				}
			}
			
			$proc = implode(", ", $proc_arr);
			
			$site_arr['right'] = "OD";
			$site_arr['left'] = "OS";
			$site_arr['bilateral'] = "OU";
			
			$site = $site_arr[strtolower($appt_data_row["site"])];
			if($site != "")
			{
				$site = " (".$site.")";	
			}			
			
			$status = $this->get_status_name_by_code($appt_data_row["status_code"]);
			if($appt_data_row['iolinkPatientId'] != 0 && $appt_data_row['iolinkPatientWtId'] != 0)
			{
				$status = "IO ".$status; 
			}					
			
			$appt_data_row["status"] = $status;

			$proc_site = $proc.$site;
			if(trim($proc_site) != "")
			{
				$proc_site = $proc_site." - ";	
			}
			
			$sch_id=$appt_data_row["id"];
			$get_room_no="";
			/*if($arr_room_sch[$sch_id]){
				if($arr_room_sch[$sch_id]!="N/A"){
					$get_room_no="\n(".$arr_room_sch[$sch_id].")";
				}
			}else*/ 
			if($arr_room[$appt_data_row["patient_id"]]){
				if($arr_room[$appt_data_row["patient_id"]]!="N/A"){
					$get_room_no="\n(".$arr_room[$appt_data_row["patient_id"]].")";
				}
			}
			$appt_data_row["patient_name"] = $proc_site.$appt_data_row["patient_name"].$get_room_no;
			
			//$appt_data_row["room_no"] = $get_room_no;
			if($appt_data_row["EMR"])
			{
				$appt_data_row["patient_name"] = $appt_data_row["patient_name"].' - e';
			}

			if(constant("ENABLE_REAL_ELIGILIBILITY") == "YES"){
				$er_pt = "";
				if($appt_data_row['elTransectionError'] != ""){
					$er_pt = "#ReR";
				}
				elseif($appt_data_row['elEBLoopResp'] != ""){		
					if(($appt_data_row['elEBLoopResp'] == "6") || ($appt_data_row['elEBLoopResp'] == "7") || ($appt_data_row['elEBLoopResp'] == "8") || ($appt_data_row['elEBLoopResp'] == "V")){
						$er_pt = "#ReR";
					}
					else{
						$er_pt = "#GeR";
					}
				}
				$appt_data_row["patient_name"] = $appt_data_row["patient_name"].$er_pt;
			}			
			
			unset($appt_data_row["status_code"]);
			unset($appt_data_row["iolinkPatientId"]);
			unset($appt_data_row["iolinkPatientWtId"]);
			
			unset($appt_data_row["procedureid"]);
			unset($appt_data_row["sec_procedureid"]);
			unset($appt_data_row["tertiary_procedureid"]);
			
			if(constant("ENABLE_REAL_ELIGILIBILITY") == "YES")
			{
				unset($appt_data_row["elTransectionError"]);
				unset($appt_data_row["elEBLoopResp"]);
			}
			
			unset($appt_data_row["EMR"]);
			unset($appt_data_row["site"]);
						
			$schedule_appt_data[$appt_data_row["start_time"]][] = $appt_data_row;
		}
		
		return $schedule_appt_data;
	}
	
	private function get_status_name_by_code($c)
	{
		$status_name_arr = array(
								13 => 'CI',
								11 => 'CO',
								6 => 'LV',
								3 => 'NS',
								2 => 'CP',
								7 => 'FI',
								100 => 'W/Sx',
								101 => 'S/Sx',
								17 => 'CF',
								21 => 'PC',
								22 => 'LM',
								23 => 'NC'
							);
		return $status_name_arr[$c];
	}
	
	/*
	Function: get_provider_schedules
	Purpose: This function gets all the active schedules of the given provider if any else for all providers for the given date
	Author: AA
	Arguments: accepts Date in Y-m-d format only
	Returns: ARRAY
	*/
	private function get_provider_schedules($wd, $ap = array()){
		//variable declarations
		$pr = false;	$wno = $dno = 0;	$ar_wd = $arr_sch = $arr_del_sch = $arr_sch_tmp = $arr_sch2 = array();	$q = $r = $str_sch = "";

		//selected provider
		if(count($ap) > 0){	$pr = "(".implode("','", $ap).")";	}

		//calculating week day no and week no
		$ar_wd = explode("-", $wd);	$wno = ceil($ar_wd[2] / 7);	$dno = date("w", mktime(0, 0, 0, $ar_wd[1], $ar_wd[2], $ar_wd[0]));	if($dno == 0) $dno = 7;
		
		//quering provider schedules
		$q = "select id, del_status, delete_row, status, provider, facility, today_date, sch_tmp_id from provider_schedule_tmp where today_date <= '".$wd."' and week".$wno." = '".$dno."' ";
		if($pr != false){	$q .= "and provider_schedule_tmp.provider IN ".$pr." ";	}
		$q .= "order by provider, facility, sch_tmp_id, today_date";
		$r = imw_query($q);
		
		$arr_sch = array();
		
		if(imw_num_rows($r) > 0){
			while($row = imw_fetch_assoc($r)){
				$arr_sch[] = $row;
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
		$arr_sch2 = array();
		if(count($arr_sch)>0){
			$arr_sch_tmp = array();	//resetting array
			for($i = 0; $i < count($arr_sch); $i++){
				$arr_sch_tmp[] = $arr_sch[$i]["id"];
			}
			$str_sch = join(',', $arr_sch_tmp);
			$q = "select id, facility , provider, sch_tmp_id, today_date from provider_schedule_tmp where id in (".$str_sch.") ";
			if($pr != false){	$q .= "and provider_schedule_tmp.provider IN ".$pr." ";	}
			$q .= "order by provider, facility, sch_tmp_id, today_date";
			$r = imw_query($q);
			if(imw_num_rows($r) > 0){
				while($row = imw_fetch_assoc($r)){
					$arr_sch2[] = $row;
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
}
?>