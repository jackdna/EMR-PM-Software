<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
/*
File: appt_label_functions.php
Purpose: Define Class for labels and templates
Access Type: Direct
*/
class appt_label{
	private $doctor_id, $response, $facility_id, $appt_id;

	function __construct(){
		
	}
	
	function replaceLabel($startTime, $endTime, $startDate, $doctor_id, $facility_id, $appt_id, $sch_template_id=0)
	{
		if( (int)$appt_id < 1 ||
		    (int)$doctor_id < 1 ||
		    (int)$facility_id < 1 ||
			empty($startTime) ||
			empty($endTime) ||
			empty($startDate)
		)return false;
		
		
		$this->doctor_id=$doctor_id;
		$this->facility_id=$facility_id;
		$this->appt_id=$appt_id;
		
		//appt timings slot wise loop
		$st_time = strtotime($startTime);
		$ed_time = strtotime($endTime);
		
		$match_st_time = date("H:i", $st_time);
		$match_ed_time = date("H:i", $ed_time);
		
		if($sch_template_id<=0){
			$schTemplateId = $this->getTemplateId($startDate, $startTime);
		}else $schTemplateId =$sch_template_id;
		
		//get code from appt sch function file to get available labels
		//--------------------------------------------------------------
		for($looptm = $st_time; $looptm < $ed_time; $looptm += (DEFAULT_TIME_SLOT * 60)){
			$edtm2 = $looptm + (DEFAULT_TIME_SLOT * 60);

			$start_loop_time = date("H:i", $looptm);
			$end_loop_time = date("H:i", $edtm2);
		
			///////////////////////////////////////////////
			//we are using forcefull label replacement here
			///////////////////////////////////////////////
			
			// add label in custom table if we do have it in label table and not in custom label
			$qryLabel = "select schedule_label_id, template_label, label_type, label_color, label_group from schedule_label_tbl where start_time = '".$start_loop_time."' and sch_template_id = '".$schTemplateId."' AND (label_type = 'Procedure' OR label_type = 'Information') LIMIT 1";
			
			$resplabel = imw_query($qryLabel);
			$labeData = array();

			if($resplabel && imw_num_rows($resplabel) > 0)
			{
				$labeData = imw_fetch_assoc($resplabel);

				$sqlChkLabel = "SELECT `id` FROM `scheduler_custom_labels`
								WHERE
									provider = '".$this->doctor_id."' AND
									facility = '".$this->facility_id."' AND
									start_date = '".$startDate."' AND
									start_time = '".$start_loop_time.":00' AND
									end_time = '".$end_loop_time.":00'";
				$sqlChkLabel = imw_query($sqlChkLabel);
				//add only if it doest exist
				if($sqlChkLabel && imw_num_rows($sqlChkLabel) == 0)
				{
					$sqlLabel = "INSERT INTO scheduler_custom_labels SET
							labels_replaced = '',
							provider = '".$this->doctor_id."',
							facility = '".$this->facility_id."',
							start_date = '".$startDate."',
							end_time = '".$end_loop_time.":00',
							start_time = '".$start_loop_time.":00',
							l_text = '".addslashes($labeData["template_label"])."',
							l_show_text = '".addslashes($labeData["template_label"])."',
							l_type = '".addslashes($labeData["label_type"])."',
							l_color = '".addslashes($labeData["label_color"])."', 
							label_group='".$labeData["label_group"]."',
							time_status = '".date("Y-m-d H:i:s")."',
							temp_id='$schTemplateId',
							system_action = '1'";
					imw_query($sqlLabel);
					$_REQUEST['last_custom_inserted_id']=imw_insert_id();
					$_REQUEST['last_custom_query']=$sqlLabel;
					
				}
			}
			
			//check is any label exist in custom label table
			$res3 = imw_query("select id, l_text, l_show_text, labels_replaced, label_group from scheduler_custom_labels where start_date = '".$startDate."' and start_time = '".$start_loop_time.":00' and provider = '".$this->doctor_id."' and facility = '".$this->facility_id."' and (l_type = 'Procedure' or l_type = 'Information') and l_show_text!='' LIMIT 1");

			if(imw_num_rows($res3) > 0)
			{
				unset($arr_l_text);
				$arr3 = imw_fetch_assoc($res3);
				if($arr3["label_group"]==1)$arr_l_text[] = $arr3["l_show_text"];
				else $arr_l_text = explode("; ", $arr3["l_show_text"]);
				//check is this already replaced
				$flag = true;
				$arr_labels_replaced = explode("::", $arr3["labels_replaced"]);
				if(count($arr_labels_replaced) > 0 && !empty($arr3["labels_replaced"])){
					foreach($arr_labels_replaced as $label_data){
						$arr_label_data = explode(":", $label_data);
						if(trim($arr_label_data[0]) == $this->appt_id){
							$flag = false;
						}
					}
				}

				if($flag === true){
					$arr_replace = array();
					$str_replace = "";
					$bl_do = false;
					$labels_replaced = "::".$this->appt_id.":".$arr_l_text[0];
					$str_replace="";
					if(count($arr_l_text)>1){
						array_shift($arr_l_text);
						$str_replace=implode("; ",$arr_l_text);
					}else{
						$str_replace=$arr_l_text[1];
					}
					//update label replaced record
					imw_query("update scheduler_custom_labels set labels_replaced = CONCAT(labels_replaced,'".$labels_replaced."'), 
					l_show_text = '".$str_replace."' where id = '".$arr3["id"]."'");

				}
			}
			
			
		}
		custom_lbl_log('library\scheduler\appt_label_functions.php');
	}
	function getTemplateId($startDate, $startTime)
	{
		list($y, $m, $d) = explode("-", $startDate);
		$week = ceil($d/7);
		$intTimeStamp = mktime(0, 0, 0, $m, $d, $y);
		$weekDay = date("N", $intTimeStamp);
		$strQryCheck = "SELECT st.id 
						FROM schedule_templates st 
						INNER JOIN provider_schedule_tmp pst ON st.id = pst.sch_tmp_id 
						WHERE pst.provider = '".$this->doctor_id."'  
						AND '".$startDate."' >= pst.today_date
						AND pst.del_status = 0
						AND ((pst.status  = 'yes') OR (pst.status  = 'no' AND pst.today_date = '".$startDate."')) 
						AND pst.week$week = '".$weekDay."'  
						AND pst.facility = '".$this->facility_id."' ";
		$resQryCheck = imw_query($strQryCheck);
		
		$templateId = 0;
		
		while($arrQryCheck = imw_fetch_array($resQryCheck)){		
			$strQryTemp = "SELECT morning_start_time, morning_end_time FROM schedule_templates WHERE id = '".$arrQryCheck["id"]."'";
			$resQryTemp = imw_query($strQryTemp) or $msg_info[] = imw_error();
			$arrQryTemp = imw_fetch_array($resQryTemp);
	
			if($arrQryCheck["id"] > 0 && strtotime($startTime) >= strtotime($arrQryTemp["morning_start_time"]) && strtotime($startTime) <= strtotime($arrQryTemp["morning_end_time"])){
				$templateId = $arrQryCheck["id"];
				break;
			}
		}
		
		if($templateId){
			//add child template check here
			$q=imw_query("SELECT sch_tmp_id from provider_schedule_tmp_child where pid='$templateId'
				AND ($startDate BETWEEN start_date AND end_date)");
			if(imw_num_rows($q)>0)
			{
				$d=imw_fetch_object($q);
				$templateId = $d->sch_tmp_id;
			}
		}
		
		if( $templateId===0)
		{
			$this->response .= "Template ID not found."."\n";
		}
		else
		{
			$this->response .= "Template ID: ".$templateId."\n";
		}
		
		return $templateId;
	}

}
?>