<?php
/*
$arr['provider']=$pro_id;
$arr['facility']=$fac_id;
$arr['template']=$new_template_id;
$arr_dates[$row['sa_app_start_date']]=$row['sa_app_start_date'];
$ids_to_replace_label[$sch_appt_id]=$sch_appt_id;
*/
foreach($arr_dates as $key=>$dt)
{
	$strQryAppts = "SELECT schedule_appointments.id, sa_doctor_id, sa_app_start_date, sa_app_starttime, sa_app_endtime, sa_facility_id, acronym, sch_template_id FROM schedule_appointments left join slot_procedures sp ON sp.id = schedule_appointments.procedureid where sa_patient_app_status_id NOT IN(201,18,19,20,203) 
	and sch_template_id != '0' 
	AND sa_app_start_date = '".$dt."'
	AND sa_doctor_id='$arr[provider]'
	AND sa_facility_id='$arr[facility]'
	ORDER BY id";
	$rsQryAppts = imw_query($strQryAppts) or $arrMsh[] = imw_error();
	while($rowQryAppts = imw_fetch_array($rsQryAppts)){
		$st_time = strtotime($rowQryAppts["sa_app_starttime"]);
		$ed_time = strtotime($rowQryAppts["sa_app_endtime"]);

		while($st_time < $ed_time){
			$add=false;
			$match_st_time = date("H:i", $st_time);
			$match_ed_time = date("H:i", ($st_time + (DEFAULT_TIME_SLOT * 60)));

			$qry3 = "select schedule_label_id, template_label, label_type, label_color, label_group from schedule_label_tbl where start_time = '".$match_st_time."' and sch_template_id = '".$rowQryAppts["sch_template_id"]."' AND (label_type = 'Procedure' OR label_type = 'Information') LIMIT 1";
			$res3 = imw_query($qry3);
			if(imw_num_rows($res3)>0){
				$arr3 = imw_fetch_assoc($res3);
				$chk = "SELECT id,temp_id FROM scheduler_custom_labels WHERE provider = '".$rowQryAppts["sa_doctor_id"]."' AND facility = '".$rowQryAppts["sa_facility_id"]."' AND start_date = '".$rowQryAppts["sa_app_start_date"]."' AND start_time = '".$match_st_time.":00' AND (l_type = 'Procedure' OR l_type = 'Information')";
				$chkres = imw_query($chk);
				if(imw_num_rows($chkres) == 0){$add=true;}
				else
				{
					$rs=imw_fetch_object($chkres);
					if($rs->temp_id!=$rowQryAppts["sch_template_id"])
					{
						$add=true;
						imw_query("delete from scheduler_custom_labels where id=$rs->id");
					}
				}
				if($add==true){
					$qry54 = "INSERT INTO scheduler_custom_labels SET labels_replaced = '', provider = '".$rowQryAppts["sa_doctor_id"]."', facility = '".$rowQryAppts["sa_facility_id"]."', start_date = '".$rowQryAppts["sa_app_start_date"]."', end_time = '".$match_ed_time.":00', start_time = '".$match_st_time.":00', l_text = '".addslashes($arr3["template_label"])."', l_show_text = '".addslashes($arr3["template_label"])."', l_type = '".addslashes($arr3["label_type"])."', l_color = '".addslashes($arr3["label_color"])."', label_group='".$arr3["label_group"]."', time_status = '".date('Y-m-d H:i:s')."', system_action = '1', temp_id='".$rowQryAppts["sch_template_id"]."'";
					imw_query($qry54);
				}
			}
			//---------------------------------
			$qry3 = "select id, l_text, l_show_text, labels_replaced, label_group from scheduler_custom_labels where start_date = '".$dt."' and start_time = '".$match_st_time.":00' and provider = '".$arr[provider]."' and facility = '".$arr[facility]."' and (l_type = 'Procedure' or l_type = 'Information') and l_show_text!='' LIMIT 1";
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
					imw_query($qry4);
				}
			}
			$st_time += (DEFAULT_TIME_SLOT * 60);
		}
	}
}
?>