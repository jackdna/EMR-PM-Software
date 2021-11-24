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

require_once(dirname(__FILE__).'/../../config/globals.php');
require_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_schedule_functions.php');

//scheduler object
$obj_scheduler = new appt_scheduler();


$get_date = (isset($_REQUEST['dt']) && !empty($_REQUEST['dt'])) ? $_REQUEST['dt'] : "";
$pid = (isset($_REQUEST['patId']) && !empty($_REQUEST['patId'])) ? $_REQUEST['patId'] : "";
if($pid != ""){
	$_SESSION['patient'] = $pid;
}
$sch_id = (isset($_REQUEST['sch_id']) && !empty($_REQUEST['sch_id'])) ? $_REQUEST['sch_id'] : "";
$sch_id_arr=explode(',',$sch_id);

$chg_to = (isset($_REQUEST['chg_to']) && $_REQUEST['chg_to'] != "") ? $_REQUEST['chg_to'] : "";
$reason = (isset($_REQUEST['reason']) && !empty($_REQUEST['reason'])) ? $_REQUEST['reason'] : "";
$doNotkeepOrg= (isset($_REQUEST['keepOrg'])) ? $_REQUEST['keepOrg'] : 1;

foreach($sch_id_arr as $sch_id_val)
{
    $sch_id=(int)$sch_id_val;
    if($get_date != "" && $pid != "" && $sch_id != "" && $chg_to != ""){
            //Schedular slot time setting
            $qry = "insert into current_time_locator set sch_id = '".$sch_id."', uid = '".$_SESSION["authId"]."', `dated`='".date('Y-m-d')."'";
            imw_query($qry);

            //logging this action in previous status table
			$remote_req = 0;
            if($chg_to == 13){
                    $obj_scheduler->logApptChangedStatus($sch_id, "", "", "", $chg_to, "", "", $_SESSION['authUser'], $reason, "", false);
            		$remote_req = 1;
			}else{
                    $obj_scheduler->logApptChangedStatus($sch_id, "", "", "", $chg_to, "", "", $_SESSION['authUser'], $reason, "", false);
            		$remote_req = 1;
            }			

            //updating schedule appointments details
            $obj_scheduler->updateScheduleApptDetails($sch_id, "", "", "", $chg_to, "", "", $_SESSION['authUser'], "", "", false);
			//flag to check do we have data regarding appointment or not
            $hv_appt_data=false;
            if(($chg_to == 201 || $chg_to == 202 || $chg_to == 18 || $chg_to == 271) && $doNotkeepOrg==1){	
				if(!empty($sch_id)){
					$q = "SELECT sa_doctor_id, sa_facility_id, sa_app_start_date, sa_app_starttime, sa_app_endtime FROM schedule_appointments WHERE id = '".$sch_id."'";
					$r = imw_query($q);	
					$a = imw_fetch_array($r);
					$hv_appt_data=true;
					if(constant('ENABLE_SCHEDULER_TRACK_LOG')==1 && $chg_to == 18)
					{
						//create log for lbl replacement
						file_put_contents('lbl_replace.log',"\n ".date('Y-m-d H:i:s')." Appt ID:$sch_id, Action:Cancel, Slot: $a[sa_app_starttime] to $a[sa_app_endtime] on $a[sa_app_start_date]", FILE_APPEND);
					}
					$sttm = strtotime($a["sa_app_starttime"]);
					$edtm = strtotime($a["sa_app_endtime"]);

					for($looptm = $sttm; $looptm < $edtm; $looptm += (DEFAULT_TIME_SLOT * 60)){
						$edtm2 = $looptm + (DEFAULT_TIME_SLOT * 60);

						$start_loop_time = date("H:i:00", $looptm);
						$end_loop_time = date("H:i:00", $edtm2);

						$q2 = "SELECT id, provider, facility, start_date, start_time, end_time, labels_replaced, l_text, l_show_text FROM scheduler_custom_labels WHERE provider = '".$a["sa_doctor_id"]."' AND facility = '".$a["sa_facility_id"]."' AND start_date = '".$a["sa_app_start_date"]."' AND start_time = '".$start_loop_time."' AND end_time = '".$end_loop_time."'";
						$r2 = imw_query($q2);
						while($row = imw_fetch_assoc($r2)){
							$new_entry = $row["labels_replaced"];
							$l_text = $row["l_show_text"];
							$lbl_record_id = trim($row['id']);
							$lbl_replaced = trim($row['labels_replaced']);
							#temp fix to retrive labels if it wasn't in label_replaced field
							if(!$lbl_replaced && !trim($row["l_show_text"])){ 
								$sct_id=$row["id"];
								$provider=$row["provider"];
								$facility=$row["facility"];	
								$start_date=$row["start_date"];
								$start_time=$row["start_time"];
								$end_time=$row["end_time"];
								$l_text=$row["l_text"];
								
								$qry_appt="SELECT id,sch_template_id FROM schedule_appointments WHERE sa_doctor_id='".$provider."' 
								AND sa_facility_id='".$facility."' AND sa_app_start_date='".$start_date."' AND sa_app_starttime='".$start_time."'
								AND sa_patient_app_status_id NOT IN (203,201,18,19,20) 
								AND IF( sa_patient_app_status_id =271, sa_patient_app_show =0, sa_patient_app_show <>2 ) ";
								
								$res_appt=imw_query($qry_appt);
								if(imw_num_rows($res_appt)>0){
									$row_appt=imw_fetch_assoc($res_appt);	
									$sch_template_id=$row_appt["sch_template_id"];
									$HH=$MM=$HH1=$MM1="";
									list($HH,$MM)=explode(":",$start_time);
									list($HH1,$MM1)=explode(":",$end_time);
									$new_start_time=$HH.":".$MM;
									$new_end_time=$HH1.":".$MM1;
									$labels_replaced="";
									$label_name=$l_text;
									if(strstr($l_text,";")){
										list($label_name)=explode(";",$l_text);	
									}
									if($row_appt["id"] && trim($label_name)){
										$labels_replaced="::".$row_appt["id"].":".trim($label_name);
										if($row_appt["id"]==$appt_id){$lbl_replaced=$labels_replaced;}
									}
									$qry_update="UPDATE scheduler_custom_labels set labels_replaced='".$labels_replaced."' WHERE id='".$sct_id."'";
									$res_update=imw_query($qry_update);
								}else if(imw_num_rows($res_appt)==0){
									$qry_update="UPDATE scheduler_custom_labels set l_show_text=l_text WHERE id='".$sct_id."'";
									$res_update=imw_query($qry_update);
								}	
							}
							#temp fix ends here
							
							$lbl_replace_act=false;
							if(trim($row["labels_replaced"]) != ""){ 
								$arr_lbl_replaced = explode("::", $row["labels_replaced"]);
								if(count($arr_lbl_replaced) > 0){ 
									foreach($arr_lbl_replaced as $this_lbl_replaced){
										$arr_this_replaced2 = explode(":", $this_lbl_replaced);
										if(trim($arr_this_replaced2[0]) == $sch_id){ 
											$new_entry = str_replace("::".$arr_this_replaced2[0].":".$arr_this_replaced2[1], "", $row["labels_replaced"]);

											if(trim($row["l_show_text"]) != ""){
												$l_text = $row["l_show_text"]."; ".$arr_this_replaced2[1];
											}else{
												$l_text = $arr_this_replaced2[1];
											}
											$upd22 = "UPDATE scheduler_custom_labels SET l_show_text = '".$l_text."', labels_replaced = '".$new_entry."' WHERE id =	'".$row["id"]."'";
											imw_query($upd22);
											$lbl_replace_act=true;
										}
									}
									//revalidate is we have reversed label or not	
									if($lbl_replace_act==false)
									{
										//validate all label replaced record
										$obj_scheduler->validate_label_replaced($row);
									}
								}
							}
						}
					}
				//checking for more appointments with same procedure
				}
            }

            if($chg_to == 13 || $chg_to == 5){			
                    for($i = 0; $i <= 3; $i++){
                            $green_id = "";
                            $vquery_d = "SELECT min(end_date) , reff_id FROM `patient_reff` WHERE end_date >= current_date() and effective_date <= current_date() and reff_type = '".$i."' and patient_id='".$pid."' GROUP BY reff_id ORDER BY end_date limit 0,1 ";
                            $vsql_d = imw_query($vquery_d);	
                            $vrs_d = imw_fetch_array($vsql_d);			
                            $green_id = $vrs_d['reff_id'];
                            if($green_id == ""){
                                    $vquery_d = "SELECT min(no_of_reffs) , reff_id FROM `patient_reff` WHERE no_of_reffs > 0 and reff_type = '".$i."' and patient_id='".$pid."' GROUP BY reff_id ORDER BY no_of_reffs limit 0,1 ";
                                    $vsql_d = imw_query($vquery_d);	
                                    $vrs_d = imw_fetch_array($vsql_d);	
                                    $green_id = $vrs_d['reff_id'];
                            }
                            if($green_id != ""){
                                    $reff_ids[] = $green_id;
                            }
                    }
					/*
                    if(sizeof($reff_ids) > 0){
                            $reff_to = implode(',', $reff_ids);	
                            $saveq = "update patient_reff set reff_used = reff_used + 1, no_of_reffs = no_of_reffs - 1 where reff_id in(".$reff_to.")";											
                            imw_query($saveq);
                    }
					*/
            }
    }
    
    
    if($get_date != "" && $pid != "" && $sch_id != "" && $chg_to != ""){
            if($hv_appt_data==false)
            {
                if(!empty($sch_id)){
					$q = "SELECT sa_doctor_id,sa_patient_id  FROM schedule_appointments WHERE id = '".$sch_id."'";
					$r = imw_query($q);	
					$a = imw_fetch_array($r);
                }
            }
            
            if($a){
                $task_pid=$a['sa_patient_id'];
                $task_doctor_id=$a['sa_doctor_id'];
                
                $params=array();
                $params['patientid']=$task_pid;
                $params['operatorid']=$task_doctor_id;
                $params['section']='appointment';

                switch($chg_to) {
                    case 18:
                        $sub_section='appt_canceled';
                        break;
                    case 3:
                        $sub_section='appt_no_show';
                        break;
                    default:
                        $sub_section='other_action';
                        break;
                }
                $params['sub_section']=$sub_section; //appt_canceled,appt_created,appt_deleted,appt_no_show,appt_reschedule
                $params['obj_value']=$sch_id;
                $serialized_arr = serialize($params);
                include_once("../../interface/common/assign_new_task.php");
            }
    }
    
	/* MVE PORTAL CREATE NEW APPOINTMENT STARTS HERE*/
	$erp_error=array();
	if(isERPPortalEnabled()) {
		try {
			include_once($GLOBALS['srcdir']."/erp_portal/appointments.php");
			$obj_appointments = new Appointments;
			$appt_act_reason = "";
			if(isset($_REQUEST["ap_act_reason"]) && $_REQUEST["ap_act_reason"]!=''){
				$appt_act_reason = core_refine_user_input(urldecode($_REQUEST["ap_act_reason"]));
			}
			$obj_appointments->addUpdateAppointments($sch_id,$pid,$reason);
		} catch(Exception $e) {
			$erp_error[]='Unable to connect to ERP Portal';
		}
	}
	/* MVE PORTAL CREATE NEW APPOINTMENT ENDS HERE*/
	
	//SYNC PATIENT TO UPDOX IF CANCELLED
	if($chg_to == 18) {
		$obj_scheduler->patients_sync($sch_id);
	}
}
//-------- Get Date and Day name use for Scheduler Load -----


list($y, $m, $d) = explode('-', $get_date);
$dayName = date('l',mktime(0, 0, 0, $m, $d, $y));
echo $pid."-".$chg_to."-".$sch_id."-".$get_date."-".$dayName;
?>