<?php
require_once("../../../../config/globals.php");
require_once('../../../../library/classes/admin/scheduler_admin_func.php');
require_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_schedule_functions.php');
$strMode=$_POST['doremove'];
$pro_id=$_REQUEST["pro_id"];
$template_label=$_REQUEST['template_label'];
$label_group=$_REQUEST['group'];
$update_custom_labels_flag = 0;


/* FOR delete the lunch labels if not in template time range */
$req_qry = "SELECT id, fldLunchStTm,fldLunchEdTm FROM schedule_templates WHERE (fldLunchStTm not between morning_start_time and morning_end_time) and (fldLunchEdTm not between morning_start_time and morning_end_time) and (fldLunchStTm != morning_start_time && fldLunchEdTm != morning_end_time) and id=".$pro_id;
$sch_req_data_obj = imw_query($req_qry);
if(imw_num_rows($sch_req_data_obj) > 0)
{
	$sch_req_data = imw_fetch_assoc($sch_req_data_obj);						
	$del_req_qry = "DELETE FROM schedule_label_tbl where sch_template_id = '".$pro_id."' AND template_label = 'Lunch'";
	imw_query($del_req_qry);

	$req_qry = "UPDATE schedule_templates SET fldLunchStTm='00:00:00',fldLunchEdTm='00:00:00' WHERE id=".$pro_id;
	$result_scLObj = imw_query($req_qry);											
}
/* Lunch labels deletion ends  */

function update_custom_labels($pro_id, $DEFAULT_TIME_SLOT, $obj_db){
	if(! is_object($obj_scheduler) )
	{
		//scheduler object
		$obj_scheduler = new appt_scheduler();
	}
	if($pro_id != ""):
	//setting custom labels accordingly
	$strQryAppts = "SELECT schedule_appointments.id, sa_doctor_id, sa_app_start_date, sa_app_starttime, sa_app_endtime, sa_facility_id, acronym, sch_template_id FROM schedule_appointments INNER JOIN slot_procedures sp ON sp.id = schedule_appointments.procedureid where sa_patient_app_status_id NOT IN(201,18,19,20,203) and sch_template_id = '".$pro_id."' and sa_app_start_date >= '".date('Y-m-d')."' ORDER BY id";
	$rsQryAppts = imw_query($strQryAppts) or $arrMsh[] = imw_error();
	while($rowQryAppts = imw_fetch_array($rsQryAppts)){

		//appt timings slot wise loop
		$st_time = strtotime($rowQryAppts["sa_app_starttime"]);
		$ed_time = strtotime($rowQryAppts["sa_app_endtime"]);

		while($st_time < $ed_time){
			$match_st_time = date("H:i", $st_time);
			$match_ed_time = date("H:i", ($st_time + ($DEFAULT_TIME_SLOT * 60)));

			if(!$deleted[$rowQryAppts["sa_doctor_id"]][$rowQryAppts["sa_facility_id"]][$rowQryAppts["sa_app_start_date"]][$match_st_time])
			{
				//get labels value to keep track
				$c_q=imw_query("select l_type, l_show_text FROM scheduler_custom_labels WHERE provider = '".$rowQryAppts["sa_doctor_id"]."' AND facility = '".$rowQryAppts["sa_facility_id"]."' AND start_date = '".$rowQryAppts["sa_app_start_date"]."' AND start_time = '".$match_st_time.":00' AND system_action = '1' AND (l_type = 'Procedure' OR l_type = 'Information')");
				if(imw_num_rows($c_q))
				{
					$c_rs=imw_fetch_assoc($c_q);
					$obj_scheduler->custom_lbl_log($rowQryAppts["sa_doctor_id"], $rowQryAppts["sa_facility_id"], $rowQryAppts["sa_app_start_date"], $match_st_time.":00", $match_ed_time.":00", $c_rs["l_type"], '', $c_rs["l_show_text"], '', $rowQryAppts["sch_template_id"], 'Label Removed from admin section', 0);
				}

				$TRUNCATE = "DELETE FROM scheduler_custom_labels WHERE provider = '".$rowQryAppts["sa_doctor_id"]."' AND facility = '".$rowQryAppts["sa_facility_id"]."' AND start_date = '".$rowQryAppts["sa_app_start_date"]."' AND start_time = '".$match_st_time.":00' AND system_action = '1' AND (l_type = 'Procedure' OR l_type = 'Information')"; 
				imw_query($TRUNCATE);
				$deleted[$rowQryAppts["sa_doctor_id"]][$rowQryAppts["sa_facility_id"]][$rowQryAppts["sa_app_start_date"]][$match_st_time]=$match_st_time;
			}
			
			$qry3 = "select schedule_label_id, template_label, label_type, label_color, label_group from schedule_label_tbl where start_time = '".$match_st_time."' and sch_template_id = '".$rowQryAppts["sch_template_id"]."' AND (label_type = 'Procedure' OR label_type = 'Information') LIMIT 1";
			$res3 = imw_query($qry3);
			if(imw_num_rows($res3) > 0){
				$arr3 = imw_fetch_array($res3);
				
				$chk = "SELECT id FROM scheduler_custom_labels WHERE provider = '".$rowQryAppts["sa_doctor_id"]."' AND facility = '".$rowQryAppts["sa_facility_id"]."' AND start_date = '".$rowQryAppts["sa_app_start_date"]."' AND start_time = '".$match_st_time.":00'";
				$chkres = imw_query($chk);
				if(imw_num_rows($chkres) == 0){

					$qry54 = "INSERT INTO scheduler_custom_labels SET labels_replaced = '', provider = '".$rowQryAppts["sa_doctor_id"]."', facility = '".$rowQryAppts["sa_facility_id"]."', start_date = '".$rowQryAppts["sa_app_start_date"]."', end_time = '".$match_ed_time.":00', start_time = '".$match_st_time.":00', l_text = '".addslashes($arr3["template_label"])."', l_show_text = '".addslashes($arr3["template_label"])."', l_type = '".addslashes($arr3["label_type"])."', l_color = '".addslashes($arr3["label_color"])."', label_group='".$arr3["label_group"]."',  time_status = '".date('Y-m-d H:i:s')."', system_action = '1', temp_id = '".$rowQryAppts["sch_template_id"]."'";
					imw_query($qry54);
				}
			}

			$qry3 = "select id, l_text, l_show_text, labels_replaced, label_group from scheduler_custom_labels where start_date = '".$rowQryAppts["sa_app_start_date"]."' and start_time = '".$match_st_time.":00' and provider = '".$rowQryAppts["sa_doctor_id"]."' and facility = '".$rowQryAppts["sa_facility_id"]."' LIMIT 1";
			$res3 = imw_query($qry3);
			if(imw_num_rows($res3) > 0){
				$arr3 = imw_fetch_assoc($res3);
				if($arr3["label_group"]==1)$arr_l_text[] = $arr3["l_show_text"];
				else $arr_l_text = explode("; ", $arr3["l_show_text"]);
				
				$boooooooooool = true;
				$arr_labels_replaced = explode("::", $arr3["labels_replaced"]);
				if(count($arr_labels_replaced) > 0){
					foreach($arr_labels_replaced as $chack_de_phatte){
						$arr_chack_de_phatte = explode(":", $chack_de_phatte);
						if(trim($arr_chack_de_phatte[0]) == $rowQryAppts["id"]){
							$boooooooooool = false;
						}
					}
				}

				if($boooooooooool === true){
					$arr_replace = array();
					$str_replace = "";
					$bl_do = false;
					$labels_replaced = "::".$rowQryAppts["id"].":".$arr_l_text[0];
					$str_replace="";
					if(count($arr_l_text)>1){
						array_shift($arr_l_text);
						$str_replace=implode("; ",$arr_l_text);
					}else{
						$str_replace=$arr_l_text[1];
					}
					
					
					$qry4 = "update scheduler_custom_labels set labels_replaced = CONCAT(labels_replaced,'".$labels_replaced."'), l_show_text = '".$str_replace."' where id = '".$arr3["id"]."'";
					//echo "<br>";echo $qry4."<br>";						die();
					imw_query($qry4);
					
				}
			}
			$st_time += ($DEFAULT_TIME_SLOT * 60);
		}
		custom_lbl_log('interface\admin\scheduler_admin\save_labels.php');
	}
	endif;
}

//from timings
//saving labels for the select template timings With New DRag Select Method//
if($template_label<>"" && $_REQUEST["hidTimeRangeFinalString"]!=""){

	$arr_template_label = explode(";", $template_label);
	$arr_new_template_label = array();
	if(count($arr_template_label) > 0){
		foreach($arr_template_label as $this_template_label){
			$arr_new_template_label[] = trim($this_template_label);
		}
		$template_label = implode("; ", $arr_new_template_label);
	}

	$temStringArray=explode("~~~",$_REQUEST["hidTimeRangeFinalString"]);

    	for($j=0; $j <=count($temStringArray);$j++){        
       
       		$act = true;
			if($temStringArray[$j]!=""){
            	$startEndTimeArray=explode("---",$temStringArray[$j]);
				$times_from=$startEndTimeArray[0];
				$times_to=$startEndTimeArray[1];
				
			
			
			if($act == true){
                $chkqry="select sch_template_id from schedule_label_tbl where sch_template_id ='$pro_id' and start_time='".trim($times_from)."' and end_time='".trim($times_to)."'";
                $chkres=imw_query($chkqry);
				$label_type=($label_type)?$label_type:'Information';
                if(imw_num_rows($chkres)<=0){
                    $qry121 = "insert into schedule_label_tbl set
                                  sch_template_id ='$pro_id',
                                  start_time='".trim($times_from)."',
                                  end_time='".trim($times_to)."',
                                  template_label='".addslashes(trim($template_label))."',
								  label_type='".trim($label_type)."',
								  label_color='".addslashes(trim($label_color))."', 
								  label_group='$label_group',
                                  date_time='".date('Y-m-d H:i:s')."' ";
                }else{
                    $qry121 = "update schedule_label_tbl set 
                                  start_time='".trim($times_from)."',
                                  end_time='".trim($times_to)."',
                                  template_label='".addslashes(trim($template_label))."',
								  label_type='".trim($label_type)."',
								  label_color='".addslashes(trim($label_color))."',
                                  date_time='".date('Y-m-d H:i:s')."', 
								  label_group='$label_group'
                               where 
                                  sch_template_id ='$pro_id' and start_time='".trim($times_from)."' and end_time='".trim($times_to)."'
                               ";
                }
                imw_query($qry121);

				$update_custom_labels_flag = 1;
            }
		}
    }
	
	//clearing cache
	$strQryPSch = "SELECT today_date FROM provider_schedule_tmp WHERE sch_tmp_id = '".$pro_id."'";
	$resSet = imw_query($strQryPSch);
	if($resSet){
		if(imw_num_rows($resSet) > 0){
			while($arrCache = imw_fetch_array($resSet,imw_ASSOC)){
				$taskDate = $arrCache['today_date'];
				clear_cache("all",$taskDate,"","","sch");
			}
		}
	}
}

//////////////////End New Drag Select MethodBy Ram///////////////

$time_mor_from_hour=$_REQUEST['time_mor_from_hour'];
$time_mor_from_mins=$_REQUEST['time_mor_from_mins'];
$time_mor_from_AP=$_REQUEST['ap1_mor'];

if($time_mor_from_AP == "PM"){
    if($time_mor_from_hour < 12){
        $time_mor_from_hour += 12;
    }
}    
if($time_mor_from_AP == "AM"){
    if($time_mor_from_hour == 12){
        $time_mor_from_hour = "00";
    }
}

$time_mor_from_hour = (strlen($time_mor_from_hour) == 1) ? "0".$time_mor_from_hour : $time_mor_from_hour;

//to timings
$time_mor_to_hour=$_REQUEST['time_mor_to_hour'];
$time_mor_to_mins=$_REQUEST['time_mor_to_mins'];
$time_mor_to_AP=$_REQUEST['ap2_mor'];

if($time_mor_to_AP == "PM"){
    if($time_mor_to_hour < 12){
        $time_mor_to_hour += 12;
    }
}    
if($time_mor_to_AP == "AM"){
    if($time_mor_to_hour == 12){
        $time_mor_to_hour = "00";
    }
}

$time_mor_to_hour = (strlen($time_mor_to_hour) == 1) ? "0".$time_mor_to_hour : $time_mor_to_hour;

//removing labels for the select template OLD  Way TO remove Selected Time Range timings
if($strMode != "" && $strMode == "remove" && $_REQUEST["hidTimeRangeFinalString"]==""){     
    $times_from = $time_mor_from_hour.":".$time_mor_from_mins;    
    $min_interval=DEFAULT_TIME_SLOT;
  
    for($j = $time_mor_from_hour; $j <= $time_mor_to_hour; $j++){        
        $loop_in=(60/$min_interval);
        for($ii=0;$ii<$loop_in;$ii++){
            $loop_mins = $ii*$min_interval;
            $loop_mins = (strlen($loop_mins) == 1) ? "0".$loop_mins : $loop_mins;
            $loop_hrs = (strlen($j) == 1) ? "0".$j : $j; 
            $times_to = $loop_hrs.":".$loop_mins;
            
            $act = false;
            if($j == intval($time_mor_from_hour)){
               if($loop_mins > $time_mor_from_mins){
                   $act = true;
               } 
            }else if($j == intval($time_mor_to_hour)){
               if($loop_mins <= $time_mor_to_mins){
                    $act = true;
               } 
            }else if ($j > intval($time_mor_from_hour) && $j < intval($time_mor_to_hour)){
                $act = true;   
            }
                         
            if($act == true){
                $delQry = "delete from schedule_label_tbl where sch_template_id ='$pro_id' and start_time='".trim($times_from)."' and end_time='".trim($times_to)."'";
                
                imw_query($delQry);
            }
            $times_from = $loop_hrs.":".$loop_mins;  
        }
    }
    //updating lunch timings as per lunch labels added/updated 

    //getting labels where label those are for lunch
    $strQry = "SELECT start_time, end_time, template_label FROM schedule_label_tbl WHERE LOWER(TRIM(template_label)) = 'lunch' AND sch_template_id = '".$pro_id."' ORDER BY start_time";
    $rsLabel = imw_query($strQry);
    //capturing start and end time of lunch
    $tmStart = "00:00:00";
    $tmEnd = "00:00:00";
    $blStartTimeCaptured = false;
    while ($rowLbl = imw_fetch_array($rsLabel, imw_ASSOC)){
        if($blStartTimeCaptured == false){
            $tmStart = $rowLbl['start_time'].":00";
        }
        $blStartTimeCaptured = true;
        $tmEnd = $rowLbl['end_time'].":00";
    }
    //insert start and end timings of lunch in new columns
    $strQry = "UPDATE schedule_templates SET 
                    fldLunchStTm = '".$tmStart."',
                    fldLunchEdTm = '".$tmEnd."'
                WHERE id = '".$pro_id."'";
    imw_query($strQry);
	
	$req_qry = "SELECT id, fldLunchStTm,fldLunchEdTm FROM schedule_templates WHERE (fldLunchStTm not between morning_start_time and morning_end_time) and (fldLunchEdTm not between morning_start_time and morning_end_time) and (fldLunchStTm != morning_start_time && fldLunchEdTm != morning_end_time) and id=".$pro_id;
	$sch_req_data_obj = imw_query($req_qry);
	if(imw_num_rows($sch_req_data_obj) > 0)
	{
		$sch_req_data = imw_fetch_assoc($sch_req_data_obj);						
		if($sch_req_data['fldLunchStTm'] != "00:00:00" && $sch_req_data['fldLunchEdTm'] != "00:00:00")
		{
			$del_req_qry = "DELETE FROM schedule_label_tbl where sch_template_id = '".$pro_id."' AND template_label = 'Lunch'";
			imw_query($del_req_qry);

			$req_qry = "UPDATE schedule_templates SET fldLunchStTm='00:00:00',fldLunchEdTm='00:00:00' WHERE id=".$pro_id;
			$result_scLObj = imw_query($req_qry);							
		}				
	}
	 
	
	$update_custom_labels_flag = 1;
	
	//clearing cache
	$strQryPSch = "SELECT today_date FROM provider_schedule_tmp WHERE sch_tmp_id = '".$pro_id."'";
	$resSet = imw_query($strQryPSch);
	if($resSet){
		if(imw_num_rows($resSet) > 0){
			while($arrCache = imw_fetch_array($resSet,imw_ASSOC)){
				$taskDate = $arrCache['today_date'];
				clear_cache("all",$taskDate,"","","xml");
			}
		}
	}
	if($update_custom_labels_flag == 1)
	{
		update_custom_labels($pro_id, DEFAULT_TIME_SLOT);	
	}
    echo("<script> window.location.href = 'open.php?pro_id=".$pro_id."&refreshOpener=1&temp_parent_id=".$temp_parent_id."'; </script>");
    die;
}

////////Drag select method to remove Time Ranges Labels//
if($strMode != "" && $strMode == "remove" && $_REQUEST["hidTimeRangeFinalString"]!=""){     
	$temStringArrayRemove=explode("~~~",$_REQUEST["hidTimeRangeFinalString"]);
	for($j=0; $j <=count($temStringArrayRemove);$j++){    
       if($temStringArrayRemove[$j]!=""){
		$startEndTimeArray=explode("---",$temStringArrayRemove[$j]);
		$times_from=$startEndTimeArray[0];
		$times_to=$startEndTimeArray[1];
		   $delQry = "delete from schedule_label_tbl where sch_template_id ='$pro_id' and start_time='".trim($times_from)."' and end_time='".trim($times_to)."'";
		   imw_query($delQry);
		
        }
    }
//updating lunch timings as per lunch labels added/updated 
	//getting labels where label those are for lunch
    $strQry = "SELECT start_time, end_time, template_label FROM schedule_label_tbl WHERE LOWER(TRIM(template_label)) = 'lunch' AND sch_template_id = '".$pro_id."' ORDER BY start_time";
    $rsLabel = imw_query($strQry);
    //capturing start and end time of lunch
    $tmStart = "00:00:00";
    $tmEnd = "00:00:00";
    $blStartTimeCaptured = false;
    while ($rowLbl = imw_fetch_array($rsLabel, imw_ASSOC)){
        if($blStartTimeCaptured == false){
            $tmStart = $rowLbl['start_time'].":00";
        }
        $blStartTimeCaptured = true;
        $tmEnd = $rowLbl['end_time'].":00";
    }
    //insert start and end timings of lunch in new columns
    $strQry = "UPDATE schedule_templates SET 
                    fldLunchStTm = '".$tmStart."',
                    fldLunchEdTm = '".$tmEnd."'
                WHERE id = '".$pro_id."'";
    imw_query($strQry);    
	
	//clearing cache
	$strQryPSch = "SELECT today_date FROM provider_schedule_tmp WHERE sch_tmp_id = '".$pro_id."'";
	$resSet = imw_query($strQryPSch);
	if($resSet){
		if(imw_num_rows($resSet) > 0){
			while($arrCache = imw_fetch_array($resSet,imw_ASSOC)){
				$taskDate = $arrCache['today_date'];
				clear_cache("all",$taskDate,"","","xml");
			}
		}
	}
	if($update_custom_labels_flag == 1)
	{
		update_custom_labels($pro_id, DEFAULT_TIME_SLOT, $obj_db);
	}	
    echo("<script> window.location.href = 'open.php?pro_id=".$pro_id."&refreshOpener=1&temp_parent_id=".$temp_parent_id."'; </script>");
    die;
}
////////End Drag select method to remove Time Ranges Labels//
//saving labels for the select template timings
if($template_label<>"" && $_REQUEST["hidTimeRangeFinalString"]==""){

	$arr_template_label = explode(";", $template_label);
	$arr_new_template_label = array();
	if(count($arr_template_label) > 0){
		foreach($arr_template_label as $this_template_label){
			$arr_new_template_label[] = trim($this_template_label);
		}
		$template_label = implode("; ", $arr_new_template_label);
	}

    $times_from = $time_mor_from_hour.":".$time_mor_from_mins;    
    $min_interval=DEFAULT_TIME_SLOT;

     for($j = $time_mor_from_hour; $j <= $time_mor_to_hour; $j++){        
        $loop_in=(60/$min_interval);
        for($ii=0;$ii<$loop_in;$ii++){
            $loop_mins = $ii*$min_interval;
            $loop_mins = (strlen($loop_mins) == 1) ? "0".$loop_mins : $loop_mins; 
            $loop_hrs = (strlen($j) == 1) ? "0".$j : $j; 
            $times_to = $loop_hrs.":".$loop_mins;
            
            $act = false;
            if($j == intval($time_mor_from_hour)){
               if($loop_mins > $time_mor_from_mins){
                   $act = true;
               } 
            }else if($j == intval($time_mor_to_hour)){
               if($loop_mins <= $time_mor_to_mins){
                    $act = true;
               } 
            }else if ($j > intval($time_mor_from_hour) && $j < intval($time_mor_to_hour)){
                $act = true;   
            }
                         
            if($act == true){
                $chkqry="select sch_template_id from schedule_label_tbl where sch_template_id ='$pro_id' and start_time='".trim($times_from)."' and end_time='".trim($times_to)."'";
                $chkres=imw_query($chkqry);
				$label_type=($label_type)?$label_type:'Information';
                if(imw_num_rows($chkres)<=0){
                    $qry121 = "insert into schedule_label_tbl set
                                  sch_template_id ='$pro_id',
                                  start_time='".trim($times_from)."',
                                  end_time='".trim($times_to)."',
                                  template_label='".addslashes(trim($template_label))."',
								  label_type='".trim($label_type)."',
								  label_color='".addslashes(trim($label_color))."',
                                  date_time='".date('Y-m-d H:i:s')."', 
								  label_group='$label_group' ";
                }else{
                    $qry121 = "update schedule_label_tbl set 
                                  start_time='".trim($times_from)."',
                                  end_time='".trim($times_to)."',
                                  template_label='".addslashes(trim($template_label))."',
								  label_type='".trim($label_type)."',
								  label_color='".addslashes(trim($label_color))."',
                                  date_time='".date('Y-m-d H:i:s')."', 
								  label_group='$label_group'
                               where 
                                  sch_template_id ='$pro_id' and start_time='".trim($times_from)."' and end_time='".trim($times_to)."'
                               ";
                }
                imw_query($qry121);

				$update_custom_labels_flag = 1;              
            }
            $times_from = $loop_hrs.":".$loop_mins;  
        }
    }
	
	//clearing cache
	$strQryPSch = "SELECT today_date FROM provider_schedule_tmp WHERE sch_tmp_id = '".$pro_id."'";
	$resSet = imw_query($strQryPSch);
	if($resSet){
		if(imw_num_rows($resSet) > 0){
			while($arrCache = imw_fetch_array($resSet,imw_ASSOC)){
				$taskDate = $arrCache['today_date'];
				clear_cache("all",$taskDate,"","","xml");
			}
		}
	}
}

//updating lunch timings as per lunch labels added/updated
if($template_label<>""){
    //getting labels where label those are for lunch
    $strQry = "SELECT start_time, end_time, template_label, label_type FROM schedule_label_tbl WHERE label_type = 'Lunch' AND sch_template_id = '".$pro_id."' ORDER BY start_time";
    $rsLabel = imw_query($strQry);
    //capturing start and end time of lunch
    $tmStart = "00:00:00";
    $tmEnd = "00:00:00";
    $blStartTimeCaptured = false;
    while ($rowLbl = imw_fetch_array($rsLabel, imw_ASSOC)){
        if($blStartTimeCaptured == false){
            $tmStart = $rowLbl['start_time'].":00";
        }
        $blStartTimeCaptured = true;
        $tmEnd = $rowLbl['end_time'].":00";
    }
    //insert start and end timings of lunch in new columns
    $strQry = "UPDATE schedule_templates SET 
                    fldLunchStTm = '".$tmStart."',
                    fldLunchEdTm = '".$tmEnd."'
                WHERE id = '".$pro_id."'";
    imw_query($strQry);    
}
if($update_custom_labels_flag == 1)
{
	update_custom_labels($pro_id, DEFAULT_TIME_SLOT, $obj_db);	
	tmp_log('Updated', "Template labels updated", '', '',$pro_id);
}
echo("<script> window.location.href = 'open.php?pro_id=".$pro_id."&refreshOpener=1&temp_parent_id=".$temp_parent_id."'; </script>");
?>