<?php
/**************** Admin -> Scheduler Templates -> Procedure Template Functions ****************/

	//to change time format to hrs to ampm
	function convertTimeHrToAMPM($strTime){
		$arrTime = explode(":",$strTime);
		return date("h:i A",mktime($arrTime[0],$arrTime[1]));
	}
	
	function deleteCustomLabelAndBlockSlot($dt, $remove_tmp, $pro_id, $fac_id)
	{
		$where_time_str='';
		if(is_array($remove_tmp) && $remove_tmp[tmp_start_time] && $remove_tmp[tmp_end_time])
		{
			//apply parent template start and end time
			$where_time_str=" and (start_time BETWEEN '$remove_tmp[tmp_start_time]' AND '$remove_tmp[tmp_end_time]')";
		}
		
		if(is_array($remove_tmp) && $remove_tmp['child_tmp_id'])
		{
			if($remove_tmp['child_start_date']<=$dt && $remove_tmp['child_end_date']>=$dt && $remove_tmp[child_start_time] && $remove_tmp[child_end_time])
			{
				//apply child template start and end time
				$where_time_str=" and (start_time BETWEEN '$remove_tmp[child_start_time]' AND '$remove_tmp[child_end_time]')";
			}
		}
		
		//delete custom label
		$sql1="delete from scheduler_custom_labels where start_date='$dt' 
					AND provider='$pro_id'
					AND facility='$fac_id'
					$where_time_str";
		//delete block or locked slots
		$sql2="delete from block_times where start_date='$dt' 
					AND provider='$pro_id'
					AND facility='$fac_id'";
		imw_query($sql1);
		imw_query($sql2);
	}
	
	function getOpIdFromOpUsername($strNewApptOpUsername){
		$intStatusOpId = 0;
		if($strNewApptOpUsername != ""){
			$strQry = "SELECT id FROM users WHERE username = '".addslashes($strNewApptOpUsername)."'";
			$rsData = imw_query($strQry);	
			$arrData = imw_fetch_array($rsData);
			$intStatusOpId = $arrData['id'];
		}
		return $intStatusOpId;
	}
	
	function fetchArray($queryID=-1) {
		$arrRecords = array();
		if($queryID!=-1) {
		  $queryID = $queryID;
		}
		$fieldCount = imw_num_fields($queryID);
		$k = 0;
		while($record = imw_fetch_array($queryID)) {
			for($i=0;$i<$fieldCount; $i++) {
				$fieldName =  imw_fetch_field_direct($queryID, $i)->name;
				$arrRecords[$k][$fieldName] = $record[$fieldName];
			}
			$k++;
		}
		return $arrRecords;
	}
	
	
	
	
	//this function updates appointment details in schedule_appointments tabel
	function updateScheduleApptDetails($intApptId, $dtNewApptDate, $tmNewApptStartTime, $tmNewApptEndTime, $intNewApptStatusId, $intNewApptProviderId, $intNewApptFacilityId, $strNewApptOpUsername, $strNewApptComments, $intNewApptProcedureId, $blUpdateNew = false){
		
		$intStatusOpId = getOpIdFromOpUsername($strNewApptOpUsername);
		
		if($blUpdateNew == false){
			$strQry = "	SELECT 
							procedureid , sa_patient_app_status_id, sa_patient_id, sa_app_start_date, sa_app_starttime, sa_app_endtime,
							sa_comments, sa_facility_id, sa_madeby, sa_doctor_id, sa_comments, iolink_iosync_waiting_id 
						FROM 
							schedule_appointments 
						WHERE
							id = '".$intApptId."'";
			$rsData = imw_query($strQry);	
			$arrData = imw_fetch_array($rsData);
			
			$intPatientId = $arrData['sa_patient_id'];				//patient id
			$iolink_iosync_waiting_id = $arrData['iolink_iosync_waiting_id']; //USE FOR iolink-sync
			
			$dtNewApptDate = $arrData['sa_app_start_date'];			//New_appt_date
			$tmNewApptStartTime = $arrData['sa_app_starttime'];			//New_appt_start_time
			$tmNewApptEndTime = $arrData['sa_app_endtime'];			//New_appt_end_time
			$intNewApptProviderId = $arrData['sa_doctor_id'];			//New_provider
			$intNewApptFacilityId = $arrData['sa_facility_id'];		//New_facility
			$intNewApptProcedureId = $arrData['procedureid'];				//NewMadeBy
		}
		
		$strUpdQry = "	UPDATE schedule_appointments SET 
							sa_doctor_id = '".$intNewApptProviderId."',
							sa_patient_app_status_id = '".$intNewApptStatusId."',
							sa_app_time = '".date("Y-m-d H:i:s")."',
							sa_app_starttime = '".$tmNewApptStartTime."',
							sa_app_endtime = '".$tmNewApptEndTime."',
							sa_facility_id = '".$intNewApptFacilityId."',
							sa_app_start_date = '".$dtNewApptDate."',
							sa_app_end_date = '".$dtNewApptDate."',
							procedureid = '".$intNewApptProcedureId."',
							sa_madeby = '".$strNewApptOpUsername."',
							status_update_operator_id = '".$intStatusOpId."' 
						WHERE id = '".$intApptId."'";
		imw_query($strUpdQry);
	}

	//this function logs all the appointment status changes in previous_status table
	function logApptChangedStatus($intApptId, $dtNewApptDate, $tmNewApptStartTime, $tmNewApptEndTime, $intNewApptStatusId, $intNewApptProviderId, $intNewApptFacilityId, $strNewApptOpUsername, $strChangeReason, $intNewApptProcedureId, $blUpdateNew = false){
		
		$strQry = "	SELECT 
						procedureid , sa_patient_app_status_id, sa_patient_id, sa_app_start_date, sa_app_starttime, sa_app_endtime, 
						sa_comments, sa_facility_id, sa_madeby, sa_doctor_id, sa_comments  
					FROM 
						schedule_appointments 
					WHERE
					id = '".$intApptId."'";
		$rsData = imw_query($strQry);	
		$arrData = imw_fetch_array($rsData);
		
		$intPatientId = $arrData['sa_patient_id'];				//patient id
		
		$dtOldApptDate = $arrData['sa_app_start_date'];			//old_appt_date
		$tmOldApptStartTime = $arrData['sa_app_starttime'];			//old_appt_start_time
		$tmOldApptEndTime = $arrData['sa_app_endtime'];			//old_appt_end_time
		$intOldApptStatusId = $arrData['sa_patient_app_status_id'];	//old_status
		$intOldApptProviderId = $arrData['sa_doctor_id'];			//old_provider
		$intOldApptFacilityId = $arrData['sa_facility_id'];		//old_facility
		$strOldApptOpUsername = $arrData['sa_madeby'];				//oldMadeBy
		$intOldApptProcedureId = $arrData['procedureid'];				//oldMadeBy
		$strOldApptComments = $arrData['sa_comments'];				//oldMadeBy
		
		if($blUpdateNew == false){
			$dtNewApptDate = $arrData['sa_app_start_date'];			//New_appt_date
			$tmNewApptStartTime = $arrData['sa_app_starttime'];			//New_appt_start_time
			$tmNewApptEndTime = $arrData['sa_app_endtime'];			//New_appt_end_time
			$intNewApptProviderId = $arrData['sa_doctor_id'];			//New_provider
			$intNewApptFacilityId = $arrData['sa_facility_id'];		//New_facility
			$intNewApptProcedureId = $arrData['procedureid'];				//NewMadeBy
		}

		//getting history details, if any
		$strOldApptComments = "";
		$strHx = "SELECT id, statusComments FROM previous_status WHERE sch_id = '".$intApptId."' ORDER BY id DESC LIMIT 1";
		$resHx = imw_query($strHx);
		if(imw_num_rows($resHx) > 0){
			$arrHx = imw_fetch_array($resHx);
			$strOldApptComments = $arrHx["statusComments"];
		}
		
		//making log
		$strInsQry = "INSERT INTO previous_status SET
						sch_id = '".$intApptId."',
						patient_id = '".$intPatientId."',
						status_time = '".date("H:i:s")."',
						status_date = '".date("Y-m-d")."',
						status = '".$intNewApptStatusId."',
						old_date = '".$dtOldApptDate."',
						old_time = '".$tmOldApptStartTime."',
						old_provider = '".$intOldApptProviderId."',
						old_facility = '".$intOldApptFacilityId."',
						statusComments = '".addslashes($strChangeReason)."',
						oldStatusComments = '".$strOldApptComments."',
						oldMadeBy = '".$strOldApptOpUsername."',
						statusChangedBy = '".$strNewApptOpUsername."',
						dateTime = '".date("Y-m-d H:i:s")."',
						new_facility = '".$intNewApptFacilityId."',
						new_provider = '".$intNewApptProviderId."',
						old_status = '".$intOldApptStatusId."',
						old_appt_end_time = '".$tmOldApptEndTime."',
						new_appt_date = '".$dtNewApptDate."',
						new_appt_start_time = '".$tmNewApptStartTime."',
						new_appt_end_time = '".$tmNewApptEndTime."',
						old_procedure_id = '".$intOldApptProcedureId."',
						new_procedure_id = '".$intNewApptProcedureId."'";
		
			imw_query($strInsQry);
	}
	
	function getSchTmpData($selDate,$pro='',$wrdata=''){
		$dtArr = explode('-',$selDate);
		$weekDay = ceil($dtArr[2]/7);		
		$day = date('w',mktime(0,0,0,$dtArr[1],$dtArr[2],$dtArr[0]));
		if($day == 0){
			$day = 7;
		}
		$wrdata = trim($wrdata);
		if($wrdata!= "" && isset($wrdata))
		{
			$wrdata_arr = explode('|',$wrdata);
			if(count($wrdata_arr) == 2)
			{
				$weekDay = $wrdata_arr[0];
				$day = $wrdata_arr[1];					
			}
		}
		$res = array();
		if($pro){
			$pro = "and provider = '$pro'";
		}
		$qry = "select id,del_status,delete_row,status,provider,facility,today_date,sch_tmp_id
				from provider_schedule_tmp	where today_date <= '$selDate' $pro				
				and week$weekDay = '$day' order by provider, del_status";
		$sql_qry = imw_query($qry);
		$res = fetchArray($sql_qry);
		$count = count($res);
		for($i=0;$i<$count;$i++){
			$del_status = $res[$i]['del_status'];
			$delete_row = $res[$i]['delete_row'];
			$status = $res[$i]['status'];
			$provider = $res[$i]['provider'];
			$facility = $res[$i]['facility'];
			$sch_tmp_id = $res[$i]['sch_tmp_id'];
			$today_date = $res[$i]['today_date'];
			if($del_status == 1){
				$del_arr[] = $res[$i];
				unset($res[$i]);
			}
		}
		
		//--- Display Check for only Future Month --------
		if(count($res)>0){
			$res = array_values($res);
		}
		if(count($del_arr)>0){
			$del_arr = array_values($del_arr);
		}
		$del_cnt = count($del_arr);
		for($i=0;$i<$del_cnt;$i++){
			$provider = $del_arr[$i]['provider'];
			$facility = $del_arr[$i]['facility'];
			$sch_tmp_id = $del_arr[$i]['sch_tmp_id'];
			$today_date = $del_arr[$i]['today_date'];
			$delete_row = $del_arr[$i]['delete_row'];
			$deletedCount = count($res);
			$res = array_values($res);
			for($r=0;$r<$deletedCount;$r++){
				$provider1 = $res[$r]['provider'];
				$facility1 = $res[$r]['facility'];
				$sch_tmp_id1 = $res[$r]['sch_tmp_id'];
				$today_date1 = $res[$r]['today_date'];
				if(strtolower($delete_row) == 'all'){
					if($provider == $provider1 && $facility == $facility1 && $sch_tmp_id == $sch_tmp_id1 && strtotime($today_date) >= strtotime($today_date1) ){
						$del_arr[] = $res[$r];
						unset($res[$r]);
					}
				}
				if(strtolower($delete_row) == 'no'){
					if($provider == $provider1 && $facility == $facility1 && $sch_tmp_id == $sch_tmp_id1 && strtotime($today_date) == strtotime($selDate) ){
						$del_arr[] = $res[$r];
						unset($res[$r]);
					}
				}
			}
		}
		//--- Display Check for only Current Month --------
		if(count($res)>0){
			$res = array_values($res);
		}
		if(count($del_arr)>0){
			$del_arr = array_values($del_arr);
		}
		$count = count($res);
		for($i=0;$i<$count;$i++){
			$today_date = $res[$i]['today_date'];
			$status = $res[$i]['status'];
			if(strtotime($today_date) < strtotime($selDate) && strtolower($status) == 'no'){
				$del_arr[] = $res[$i];
				unset($res[$i]);
			}
		}
		if(count($res)>0){
			$res = array_values($res);
			$schId = array();
			for($i=0;$i<count($res);$i++){
				$schId[] = $res[$i]['id'];
			}
			$schIdStr = join(',',$schId);
			$qry = "select id,facility , provider,sch_tmp_id,today_date, status 
					from provider_schedule_tmp where id in ($schIdStr)
					order by provider,facility ,sch_tmp_id, id";
			$sql_qry = imw_query($qry);
			$return = fetchArray($sql_qry);
			$cnt = count($return);
			for($i=0;$i<$cnt;$i++){
				$provider = $return[$i]['provider'];
				$provider1 = $return[$i+1]['provider'];
				$facility = $return[$i]['facility'];
				$facility1 = $return[$i+1]['facility'];
				$sch_tmp_id = $return[$i]['sch_tmp_id'];
				$sch_tmp_id1 = $return[$i+1]['sch_tmp_id'];
				if($sch_tmp_id == $sch_tmp_id1 && $facility == $facility1 && $provider == $provider1){
					unset($return[$i]);
				}
			}
		}
		return $return;		
	}
	
	function adminSchTmpData($selDate,$pro='',$wrdata=''){
		$return = array();
		if($pro){
			$res = getSchTmpData($selDate,$pro,$wrdata);
		}
		if(count($res)>0){
			$res = array_values($res);
			$provider1 = 0;
			$facility1 = 0;
			$sch_tmp_id1 = 0;			
			for($i=0;$i<count($res);$i++){
				$provider = $res[$i]['provider'];
				$facility = $res[$i]['facility'];
				$sch_tmp_id = $res[$i]['sch_tmp_id'];
				$id = $res[$i]['id'];
				if($provider != $provider1 || $facility != $facility1 || $sch_tmp_id != $sch_tmp_id1){
					//-- Facility Details ----------
					$qry = "select name from facility where id = '$facility'";
					$sql_qry = imw_query($qry);
					$facRes = fetchArray($sql_qry);
					$facName = trim(strtolower($facRes[0]['name']));
					if($facName == 'whiting'){
						//$facName = 'wh';
					}
					else if($facName == 'brick'){
						//$facName = 'br';
					}
					else if($facName == 'surgery center'){
						//$facName = 'SC';
					}
					else if($facName == 'toms river'){
						//$facName = 'tr';
					}
					if(strlen($facName)>17){
					$return[] = '<span title="'.strtoupper($facName).'">'.strtoupper(substr($facName,0,17)).'..</span>';
					}
					elseif(strlen($facName)<=0){
					$return[] = '<span title="Facility Deleted" class="text-danger">Facility Deleted</span>';
					}
					else{
					$return[] = '<span title="'.strtoupper($facName).'">'.strtoupper($facName).'</span>';}
				}
				$provider1 = $provider;
				$facility1 = $facility;
				$sch_tmp_id1 = $sch_tmp_id;
			}
		}
		return $return;
	}
	
	
	function clear_cache($status,$task_Date,$themonth,$theyear,$mode = "both",$thePath = '../../../data/athwal/scheduler_common/load_xml'){
		if($mode == "both"){
			//deleting old schedule calendar template
			//$qry = "delete from schedular_calender where month >= ".intval($themonth)." and year >= ".intval($theyear);
			//imw_query($qry);
		}
		//---- Delete Xml For Future -------
		$dir = realpath($thePath);
		$op = opendir($dir);
		while($file = readdir($op)){
			$extn = substr($file,-3);
			if(strtolower($extn) == 'sch'){
				$fileDate = explode('-',$file);
				
				if($status == 'yes' || $status == 'all'){
					if(@strtotime($fileDate[0]."-".$fileDate[1]."-".$fileDate[2])>=strtotime($task_Date)){
						unlink($dir.'/'.$file);
					}
				}else{
					if(@strtotime($fileDate[0]."-".$fileDate[1]."-".$fileDate[2])==strtotime($task_Date)){
						unlink($dir.'/'.$file);
					}
				}
			}
		}
	}
	
	/**** Provider Schedule  ****/
	function getBaseLayerData($dt,$fac,$providerToLoad){
		list($y,$m,$d) = explode('-',$dt);
		$dt = date('Y-m-d',mktime(0,0,0,$m,$d,$y));
		$path = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/scheduler_common/load_xml/".$dt."_".$providerToLoad."_schedulerData.xml";
		
		if(file_exists($path)){		
			$strContent = file_get_contents($path);
			$xml = new SimpleXMLElement($strContent);
			$arrParsedData = array();
			$ff = 0;
			$intDefaultStartHr = 23;
			$intDefaultEndHr = 0;
			
			foreach ($xml->dt->facilities->facility as $objFacility){	
				
				$thisObjFacAttribs = $objFacility->attributes();
				$intB = $thisObjFacAttribs['id'][0];
				$strStartTime = $thisObjFacAttribs['start_time'][0];
				$intMinutes = $thisObjFacAttribs['hrToMin'][0];
				
				//start time
				$arrStartTime = explode(":",$strStartTime);
				$intStartHr = intval($arrStartTime[0]);
				$intStartMn = intval($arrStartTime[1]);
				
				if($intStartHr <= $intDefaultStartHr){
					$intDefaultStartHr = $intStartHr;
				}
				
				//end time
				$intHrs = round($intMinutes / 60)-1;
				$intEndHr = $intStartHr + $intHrs;
					
				if($intEndHr >= $intDefaultEndHr){
					$intDefaultEndHr = $intEndHr;
					
				}
				
				$pp = 0;
				$arrParsedData[$ff][$pp]['facility'] = $intB;
				foreach ($objFacility->providers->provider as $objProvider){	
					$proId = "";
					$proName = "";
					$proColor = "";
					$proType = "";
					
					foreach($objProvider->attributes() as $a => $b) {
						switch ($a){
							case "id":
								$arrParsedData[$ff][$pp]['id'] = $b;
								break;
							case "name":
								$arrParsedData[$ff][$pp]['name'] = $b;
								break;
							case "color":
								$arrParsedData[$ff][$pp]['color'] = $b;
								break;
							case "type":
								$arrParsedData[$ff][$pp]['type'] = $b;
								break;
							case "notes":
								$arrParsedData[$ff][$pp]['notes'] = $b;
								break;
							case "max_appoint":
								$arrParsedData[$ff][$pp]['max_appoint'] = $b;
								break;
						}
					}
					
					$ss = 0;
					foreach ($objProvider->slots->slot as $objSlot){																								
						
						$slotTiming = "";
						$slotStatus = "";
						$slotColor = "";
						$slotLabel = "";
						
						foreach($objSlot->attributes() as $a => $b){
							switch ($a){
								case "timing":
									$arrParsedData[$ff][$pp]['slots'][$ss]['timing'] = $b;
									break;
								case "status":
									$arrParsedData[$ff][$pp]['slots'][$ss]['status'] = $b;
									break;
								case "color":
									$arrParsedData[$ff][$pp]['slots'][$ss]['color'] = $b;
									break;
								case "tmpId":
									$arrParsedData[$ff][$pp]['slots'][$ss]['tmpId'] = $b;
									break;
								case "label":
									$arrParsedData[$ff][$pp]['slots'][$ss]['label'] = $b;
									break;
							}
						}
						$ss++;
					}
					$pp++;
				}
				$ff++;			
			}

			if(count($arrParsedData) > 0){
				
				$intDefaultStartHr = (strlen($intDefaultStartHr) == 1) ? "0".($intDefaultStartHr) : $intDefaultStartHr;
				//echo  "amit";
				$intDefaultEndHr = (strlen($intDefaultEndHr) == 1) ? "0".($intDefaultEndHr) : $intDefaultEndHr;
				//echo "<br>";
				
				$intStartMn = (strlen($intStartMn) == 1) ? "0".$intStartMn : $intStartMn;
				
				$arrOfficeTimings = array("start" => $intDefaultStartHr.":".$intStartMn, "end" => $intDefaultEndHr.":".$intStartMn);
				
				$arrReturn = array($arrParsedData,$arrOfficeTimings);
				
				return $arrReturn;
			}else{
				echo file_get_contents("../../../interface/admin/scheduler_admin/provider_schedule/defaultClosedSchedule.html");
				die;
			}
		}else{
			echo file_get_contents("../../../interface/admin/scheduler_admin/provider_schedule/defaultClosedSchedule.html");
			die;
		}
	}

	function getWidths($arrParsedData){
		$all_columns = count($arrParsedData);
		if($all_columns >= 3){
			$setcolumn_width = 190;		
			$scroll_width = $all_columns * 175;	
		}else{		
			$setcolumn_width = 530/$all_columns;
		}
		
		//div settings	
		$dsetcolumn_width = 514;
		if($all_columns == 2){
			$dsetcolumn_width = 250;
		}else if($all_columns >= 3){
			$dsetcolumn_width = 514/$all_columns;
		}	
		
		return array(0=>$all_columns,1=>$setcolumn_width,2=>$scroll_width,3=>$dsetcolumn_width);
	}

	function getBaseLayerTimings($arrParsedDataFac, $left_pane, $min_interval, $time_array, $mode="html", $arrOfficeTimings){
		
		$strHTML = "";
		$row_span=(60/$min_interval);
		$hei1 =(12*($min_interval/5))+30;
		
		if(is_array($arrOfficeTimings) && count($arrOfficeTimings) > 0){
			
			$start = $arrOfficeTimings['start'];
			$arrStart = explode(":",$start);
			$startHr = intval($arrStart[0]);
			
			$end = $arrOfficeTimings['end'];
			$arrEnd = explode(":",$end);
			$endHr = intval($arrEnd[0]);
			
			while($startHr < $endHr){
				
				$strHTML .= "
				<tr bgcolor=\"".$left_pane."\" height=\"".$hei1."\">  
					<td valign='top'>
							<table cellpadding=\"0\" style=\"border-right:0px solid #999999;border-left:0px solid #999999;\" cellspacing=\"0\" border=\"0\" align=\"right\">
							";
				
				$strHTML .= "
							<tr bgcolor=\"".$left_pane."\" height=\"".$hei1."\">
								<td rowspan=\"".$row_span."\" height=\"100%\" class=\"time_text\" valign=\"top\" align=\"right\">
									<p align=\"right\" style=\"margin-top:-3px; margin-left:-5;\">
										&nbsp;";
										$strHTML .= $time_array[$startHr];
										$strHTML .= "
									</p>
								</td>
							</tr>";
										
				$strHTML .= "
							
							
						</table>
					</td>
				</tr>";
				
				for($kk = 1; $kk <= 5; $kk++){
					$strHTML .= "
					<tr bgcolor=\"".$left_pane."\" height=\"".$hei1."\">  
						<td>
								<table cellpadding=\"0\" style=\"border-right:0px solid #999999;border-left:0px solid #999999;\" cellspacing=\"0\" border=\"0\" align=\"right\">
								";
					
					$strHTML .= "				
							<tr bgcolor=\"".$left_pane."\" height=\"".$hei1."\">
								<td class=\"text_9\" valign=\"top\" align=\"right\" style=\"".$sty1."\">&nbsp;
									<div align=\"center\" style=\"margin-top:-4; margin-left:-35; width:20px; background-color:#FFFFFF;position:absolute;border:1px solid #000000;\">";
										$strHTML .= $kk*10;
										$strHTML .= "
									</div>
								</td>
							</tr>";		
					$strHTML .= "
							</table>
						</td>
					</tr>";
				}
				$startHr++;			
			}
		}
		return $strHTML;
	}
	  
	function getBaseLayerHeadings($arrParsedData, $yes_color_title, $get_date){
		$strHTML = "";
		if(is_array($arrParsedData) && count($arrParsedData) > 0){
			$intCnt = 0;
			$arrWidth = getWidths($arrParsedData);
			$setcolumn_width = $arrWidth[1];
			foreach ($arrParsedData as $arrProvider){
				$intFacId =  intval($arrProvider[0]['facility']);
				$intProId =  intval($arrProvider[0]['id']);
				//getting facility name
				$qry = "select name from facility where id = '$intFacId'";
				$DRecfacRes = imw_query($qry);
				$facRes = fetchArray($DRecfacRes);
				$facName = trim(strtolower($facRes[0]['name']));
				
				$strHTML .= "<td valign=\"top\" width=\"".$setcolumn_width."\" style=\"border-top:0px outset #cccccc;border-left: 2px outset #cccccc;\" bgcolor=\"#FEA517\">
			<table class=\"scheduler_title\" border=\"0\" width=\"100%\" height=\"22\" cellpadding=\"0\" bgcolor=\"";
				
				$strHTML .= "#FBD78D";
				
				$strHTML .= "\" cellspacing=\"0\"><tr>";
				
				$strHTML .= "<td align=\"center\" valign=\"top\" class=\"text_10\"><b>";
				
				$proName = strtoupper($facName);
				
				$sel_sch_tot2="select sa_patient_id from schedule_appointments where sa_doctor_id = '".$intProId."' and sa_test_id=0 and sa_facility_id = '".$intFacId."' and sa_patient_app_status_id NOT IN(203,201,18,19,20) and '".$get_date."' between sa_app_start_date and sa_app_end_date  ";

				$sel_sch_rs2=@imw_query($sel_sch_tot2);
				$sel_sch_row2=@imw_num_rows($sel_sch_rs2);
				if($sel_sch_row2 == ""){
					$sel_sch_row2 = 0;
				}
				$proName .= " ($sel_sch_row2)";
				
				$strHTML .=  $proName."</b></td>";
				
				$strHTML .=  "</tr>
			</table>
		</td>";
				
				$intCnt++;
			}
		}
		return $strHTML;
	}

	function getBaseLayerHTML($arrParsedData, $min_interval, $time_array_h, $get_date, $left_pane, $arrOfficeTimings){

		$strPrivilegeCheck = 1;
		
		$start = $arrOfficeTimings['start'];
		$arrStart = explode(":",$start);
		$startHr = intval($arrStart[0]);
		
		$end = $arrOfficeTimings['end'];
		$arrEnd = explode(":",$end);
		$endHr = intval($arrEnd[0]);
		
		$up_height=(($min_interval/5)*11)+30;
		$strHTML = "";
		$row_span=(60/$min_interval);
		$hei1 =(12*($min_interval/5))+30;
		$div_margin_top=$min_interval-9;
		$div_slot_height=($min_interval/5)*11; 
		
		$arrWidth = getWidths($arrParsedData);
		$dsetcolumn_width = $arrWidth[3];
		$setcolumn_width = $arrWidth[1];
		
		$tsGetDate = convertToTimestamp($get_date);
		$eff_date_add_sch = date("m-d-Y", $tsGetDate);
		
		if(is_array($arrParsedData) && count($arrParsedData) > 0){
			$intCnt = 1;
			$intSCnt = 1;
			$intProvCount = count($arrParsedData);
			foreach ($arrParsedData as $arrProvider){
				$intFacId =  intval($arrProvider[0]['facility']);
				$intProId =  intval($arrProvider[0]['id']);
				
				$sel_sch="
							select 
							sa.id, sa.sa_app_starttime,sa.sa_app_endtime, sa.sa_patient_app_status_id, sa.procedureid, sa.sa_patient_id, sa.sa_patient_name, sa.sa_doctor_id,   
							pd.EMR, 
							sp.proc_color, sp.acronym, 
							st.status_name, st.status_icon, 
							sp2.times 
							from schedule_appointments sa 
							left join patient_data pd on pd.id = sa.sa_patient_id 
							left join slot_procedures sp on sp.id = sa.procedureid 
							left join slot_procedures sp2 on sp2.id = sp.proc_time  
							left join schedule_status st on st.id = sa.sa_patient_app_status_id 																																
							where sa_facility_id = '".$intFacId."' and sa_doctor_id = '".$intProId."' and sa_test_id=0 and sa_patient_app_status_id NOT IN (203,201,18,19,20) and '$get_date' between sa_app_start_date and sa_app_end_date order by sa.sa_app_starttime";


				$vsql_sch = imw_query($sel_sch);
				$totalAppts = imw_num_rows($vsql_sch);
				$arrStartTiming = array();
				if($totalAppts > 0){
					while($vrs_sch=@imw_fetch_array($vsql_sch)){
						$arrStartTiming[$vrs_sch["sa_app_starttime"]][] = $vrs_sch;
					}
				}
				
				//looping through all time slots from office schedule
				if(is_array($arrProvider[0]["slots"]) && count($arrProvider[0]["slots"]) > 0){
					
					$div_id = 'dive_'.str_replace("-","_",$get_date).'_'.$intFacId.'_'.$intProId;
					$imgid = "im".$div_id;
					$tabid = "tab".$div_id;
					$strHTML .= "<td width=\"".$setcolumn_width."\">$testing<table width='100%' cellpadding='0' border='1' bgcolor=\"#f0f0f0\" id='$tabid' cellspacing='0'>";
					//intializing array
					$arrPrevThisAppt = array();
					foreach ($arrProvider[0]["slots"] as $arrSlot){
						
						
						$intStartHr = (substr($arrSlot['timing'],0,2));
						$intStartMin = (substr($arrSlot['timing'],3,2));
						$times_from = $intStartHr.":".$intStartMin.":00";
						
						$intEndHr = (substr($arrSlot['timing'],6,2));
						$intEndMin = (substr($arrSlot['timing'],9,2));
						$times_to = $intEndHr.":".$intEndMin.":00";
											
						//adjusting previouse slot appointment in this slot
						if(count($arrPrevThisAppt) > 0){
							
							$intExistingApptsForThisSlot = count($arrStartTiming[$times_from]);
							
							foreach ($arrPrevThisAppt as $arrThisPrevThisAppt){
								$arrStartTiming[$times_from][$intExistingApptsForThisSlot] = $arrThisPrevThisAppt;
								$intExistingApptsForThisSlot++;
							}
							
							//resetting array
							$arrPrevThisAppt = array();
						}
						
						$tddblclick = "";
						$tddblclick_one = "";
						$tddblclick_blk = "";
						$strMsg = "";
						$tdMouseUp = "";
						
						if(array_key_exists($times_from,$arrStartTiming) == false){						
							//$tddblclick_blk = "onMouseDown=pop_menu_time('$loc','".$arrProvider['id']."','$times_from','$eff_date_add_sch','[{MODE}]');";	
						}
						
						//adding additional columns at start
						$testme = "";
						$intSCnt++;
						
						//provider not in office
						if($arrSlot["status"] == "off"){
							$strHTML .= "<tr width=\"".$scroll_width."\" bgcolor=\"".$left_pane."\" height=\"".$hei1."\">";
							if($strPrivilegeCheck == 1 && array_key_exists($times_from,$arrStartTiming) == false){
								$strHTML .= "<td height=\"".$heil."\" bgcolor=\"#f3f3f3\" valign=\"middle\" align=\"left\">
											<div $disp2 align=\"left\" id=\"grippy\" class='list_items' ></div>
										</TD>
										<td height=\"".$heil."\" width=\"".$setcolumn_width."\" ".str_replace("[{MODE}]","off",$tddblclick_blk)." bgcolor=\"".$arrSlot['color']."\" valign=\"top\" align=\"left\" style=\"border-top:2px outset #ffffff;border-left:2px outset #ffffff;\">";
							}else{
								$strHTML .= "<td height=\"".$heil."\" bgcolor=\"#FFFFFF\" valign=\"middle\" align=\"left\">
											<div $disp2 align=\"left\" id=\"grippy\" class='list_items' ></div>
										</TD>
										<td height=\"".$heil."\" width=\"".$setcolumn_width."\" ".str_replace("[{MODE}]","off",$tddblclick_blk)." bgcolor=\"".$arrSlot['color']."\" valign=\"top\" align=\"left\" style=\"border-top:2px outset #ffffff;border-left:2px outset #ffffff;\">";
							}
						}else if($arrSlot["status"] == "block"){
							
							$strHTML .= "<tr width=\"".$scroll_width."\" bgcolor=\"".$left_pane."\" height=\"".$hei1."\">";						
							if($strPrivilegeCheck == 1 && array_key_exists($times_from,$arrStartTiming) == false){
								$strHTML .= "<td height=\"".$heil."\" bgcolor=\"#f3f3f3\" valign=\"middle\" align=\"left\">
											<div $disp2 align=\"left\" id=\"grippy\" class='list_items' ></div>
										</TD>
										<td height=\"".$heil."\" width=\"".$setcolumn_width."\" ".str_replace("[{MODE}]","block",$tddblclick_blk)." bgcolor=\"".$arrSlot['color']."\" valign=\"top\" align=\"left\" style=\"border-top:2px outset #ffffff;border-left:2px outset #ffffff;\">";
							}else{
								$strHTML .= "<td height=\"".$heil."\" bgcolor=\"#FFFFFF\" valign=\"middle\" align=\"left\">
											<div $disp2 align=\"left\" id=\"grippy\" class='list_items' ></div>
										</TD>
										<td height=\"".$heil."\" width=\"".$setcolumn_width."\" ".str_replace("[{MODE}]","block",$tddblclick_blk)." bgcolor=\"".$arrSlot['color']."\" valign=\"top\" align=\"left\" style=\"border-top:2px outset #ffffff;border-left:2px outset #ffffff;\">";
							}
						
						}else if($arrSlot["status"] == "open"){
							
							$strHTML .= "<tr width=\"".$scroll_width."\" bgcolor=\"".$left_pane."\" height=\"".$hei1."\">";
							$strHTML .= "<td height=\"".$heil."\" bgcolor=\"#FFFFFF\" valign=\"middle\" align=\"left\"\">
											<div $disp2 align=\"left\" id=\"grippy\" class='list_items' ></div>
										</TD>
										<td height=\"".$heil."\" width=\"".$setcolumn_width."\" ".str_replace("[{MODE}]","open",$tddblclick_blk)." bgcolor=\"".$arrSlot['color']."\" valign=\"top\" align=\"left\" style=\"border-top:2px outset #ffffff;border-left:2px outset #ffffff;\">";
							
						}else if($arrSlot["status"] == "on"){	//provider in office
							
							$strHTML .= "<tr width=\"".$scroll_width."\" bgcolor=\"".$left_pane."\" height=\"".$hei1."\">";
							$strHTML .= "<td height=\"".$heil."\" bgcolor=\"#FFFFFF\" valign=\"middle\" align=\"left\"\">";
							
							$strHTML .= "<div $disp2 align=\"left\" id=\"grippy\" class='list_items' ></div>
										</TD>
										<td height=\"".$heil."\" width=\"".$setcolumn_width."\" ".str_replace("[{MODE}]","on",$tddblclick_blk)." bgcolor=\"".$arrSlot['color']."\" valign=\"top\" align=\"left\" style=\"border-top:2px outset #ffffff;\">";
						}
						
						//showing appointments in slot
						if(array_key_exists($times_from,$arrStartTiming)){
							
							//provider has appointments
							$strHTML .= "<div style='width:".($dsetcolumn_width)."px; margin-top:0px; position:absolute;' id='".$div_id.$times_from."' class='schedule_slip'>";
							$strHTML .= "<table width='100%' cellpadding='0' border='0' id='$tabid' cellspacing='0'>";	
						
							$intTotApptInSlot = count($arrStartTiming[$times_from]);
							$intShowAppt = 0;
							$intPrevApptCnt = 0;
							
							$hei12 = $hei1;
							$arrPrevThisAppt = array();
							foreach($arrStartTiming[$times_from] as $arrThisAppt){
								
								$divHeight = floor(51/$intTotApptInSlot);
								
								if($intTotApptInSlot == 2){
									$hei12 = $hei1/2;
								}elseif ($intTotApptInSlot >= 3){
									$hei12 = $hei1/3;
								}

								if($intShowAppt >= 3){
									$disp = " style=\"display:none;\" ";
								}else{
									$disp = " style=\"display:block;\" ";
								}
								//load appt using xml attributes and db fetch
								$strHTML .=  "<TR id='disprow".$intShowAppt."-".$div_id.$times_from."' $disp height='".($hei12-4)."' bgcolor='".$arrThisAppt['proc_color']."'>
													<td valign='top' style='cursor::pointer;' bgcolor='".$arrThisAppt['proc_color']."' width='3'>
														
												   </td>";
								$strHTML .= "<td width='10' bgcolor='".$arrThisAppt['proc_color']."'  id='".$arrThisAppt['id']."' class='schedule_text' $pp_menu align='center' valign='middle'>";
								if($arrThisAppt['status_name']=="Check-in"){
									$strHTML .= "<b style=\"color:#990000;font-size:12px;font-style:italic;\">CI</b>&nbsp;";
								}else if($arrThisAppt['status_name']=="Checked Out"){
									$strHTML .= "<b style=\"color:#990000;font-size:12px;font-style:italic;\">CO</b>&nbsp;";
								}elseif($arrThisAppt['status_name']=="Left without visit"){
									$strHTML .= "<b style=\"color:#990000;font-size:12px;font-style:italic;\">LV</b>&nbsp;";
								}elseif($arrThisAppt['status_name']=="No Show"){
									$strHTML .= "<b style=\"color:#990000;font-size:12px;font-style:italic;\">NS</b>&nbsp;";
								}elseif($arrThisAppt['status_name']=="Chart Pulled"){
									$strHTML .= "<b style=\"color:#990000;font-size:12px;font-style:italic;\">CP</b>&nbsp;";
								}elseif($arrThisAppt['status_name']=="Insurance/Financial Issue"){
									$strHTML .= "<b style=\"color:#990000;font-size:12px;font-style:italic;\">FI</b>&nbsp;";
								}else if(($arrThisAppt['status_icon']<>"") && (file_exists("../../images/".$arrThisAppt["status_icon"]))){
									$strHTML .= "<img src='../../images/".$arrThisAppt['status_icon']."' alt='".$vrs_st['status_name']."' align='absmiddle'>";
								}
								$strHTML .= "
							</td>
							<td bgcolor='".$arrThisAppt['proc_color']."' style='cursor:pointer;' id='".$arrThisAppt['id']."' class='schedule_text' $pp_menu align='left' valign='middle'> ";
								$display_name = $arrThisAppt['acronym']." - ". stripslashes($arrThisAppt['sa_patient_name']);
								if($intProvCount >= 3 && $intTotApptInSlot >= 3){
									$display_name = substr($arrThisAppt['acronym']." - ".stripslashes($arrThisAppt['sa_patient_name']), 0, 14)."..";
								}
								$strHTML .= "<span class=\"text_9b\">".$display_name."</span></td>";
								
								if($intShowAppt == 2 && $intTotApptInSlot > 3){
									$strHTML .=  "<td width='15' bgcolor=\"".$arrThisAppt['proc_color']."\"><a href='javascript:void(0)'>";
									$strHTML .=  "<img align='absmiddle' id='im".$div_id.$times_from."' style=\"display:block;\" src='images/arr_down.gif'><img align='absmiddle' id='mi".$div_id.$times_from."' style=\"display:none;\" src='images/arr_up.gif'></a></td><td class='text_9' bgcolor=\"".$arrThisAppt['proc_color']."\" id=\"td".$div_id.$times_from."\"> $intTotApptInSlot </td>";
								}else{
									$strHTML .=  "<td bgcolor=\"".$arrThisAppt['proc_color']."\"></td>";
									$strHTML .=  "<td  bgcolor=\"".$arrThisAppt['proc_color']."\"><i>$emrsymbol</i></td>";
								}
								
								$strHTML .= "</TR>";
								
								//slot end time 
								$arrTempSlotEndTime = explode(":",$times_to);
								$tsSlotEndTime = mktime($arrTempSlotEndTime[0],$arrTempSlotEndTime[1],$arrTempSlotEndTime[2]);

								//appt end time
								$arrTempAppEndTime = explode(":",$arrThisAppt['sa_app_endtime']);
								$tsAppEndTime = mktime($arrTempAppEndTime[0],$arrTempAppEndTime[1],$arrTempAppEndTime[2]);

								if(isset($arrThisAppt['repeat']) && $arrThisAppt['repeat'] != ""){
									if($tsAppEndTime > $tsSlotEndTime){									
										$arrTemp = array("repeat"=>$arrThisAppt['repeat']+1);
										unset($arrThisAppt['repeat']);
										$arrPrevThisAppt[$intPrevApptCnt] = array_merge($arrThisAppt,$arrTemp);
									}
								}else{
									if($tsAppEndTime > $tsSlotEndTime){
										$arrTemp = array("repeat"=>2);
										$arrPrevThisAppt[$intPrevApptCnt] = array_merge($arrThisAppt,$arrTemp);
									}
								}
								$intPrevApptCnt++;                   
								$intShowAppt++;
							}
							
							$strHTML .= "<div style=\"position:absolute;width:160;top:250;left:70;visibility:hidden;\" id=\"keepTotAppts".$div_id.$times_from."\">$testme</div></table></div>";
						}else{
							if($arrSlot["label"] != ""){
								$strHTML .=  "<span style=\"color:#666666; font-weight:bold;\">$testme".ucfirst($arrSlot["label"])."</span>";
							}else{
								$strHTML .=  "<span style=\"color:#666666; font-weight:bold;\">$testme</span>";
							}
						}
						
						$strHTML.= "</td></tr>";
						
						//adding additional columns at end
						$testme = "";
					}
					$strHTML .= "</table></td>";
				}
			
			}
		}
		return $strHTML;
	}
	
	
	function getOfficeTimings($dt,$strFacIds,$providerToLoad="", $dirPath=""){
		list($y,$m,$d) = explode('-',$dt);
		$dt = date('Y-m-d',mktime(0,0,0,$m,$d,$y));
		$finalResArr = getSchTmpData($dt,$providerToLoad,$strFacIds);
		
		//print "<pre>";
	//    print_r($finalResArr);
	//    die("amit");
		$tmpTableIdArr = array();
		for($i=0;$i<count($finalResArr);$i++){
			$tmpTableIdArr[] = $finalResArr[$i]['id'];
		}
		$tmpTableId = join(',',$tmpTableIdArr);
		$qry = "select provider_schedule_tmp.facility,provider_schedule_tmp.sch_tmp_id,
				provider_schedule_tmp.provider from provider_schedule_tmp
				join users on provider_schedule_tmp.provider = users.id
				where provider_schedule_tmp.id in ($tmpTableId) 
				order by users.lname,users.fname";
		
		$DRecfinalResArr = imw_query($qry);
		$finalResArr = fetchArray($DRecfinalResArr);
	   // print "<pre>";
	//    print_r($finalResArr);
	//    die("amit");	
		$facilityId = array();
		$providerId = array();
		$mainTmpId = array();
		$sch_tmp_id = array();
		for($i=0;$i<count($finalResArr);$i++){
			$sch_tmp_id[] = $finalResArr[$i]['sch_tmp_id'];
			$facilityId[] = $finalResArr[$i]['facility'];
			$providerId[] = $finalResArr[$i]['provider'];
			$facility_id = $finalResArr[$i]['facility'];
			$FacProviderId[$facility_id][$finalResArr[$i]['provider']] = $finalResArr[$i]['provider'];
			$provider_id = $finalResArr[$i]['provider'];
			$facSchTmpId[$facility_id][] = $finalResArr[$i]['sch_tmp_id'];
			$mainTmpId[$facility_id][$provider_id][] = $finalResArr[$i]['sch_tmp_id'];
		}	
		$sch_tmp_id = array_unique($sch_tmp_id);
		$schTmpId = join(',',$sch_tmp_id);
		$facilityId = array_unique($facilityId);
		$facilityId = array_values($facilityId);
		$providerId = array_unique($providerId);
		$providerId = array_values($providerId);
		$facSchTmp_id = array_keys($facSchTmpId);
		$facSchTmpId = array_values($facSchTmpId);	
		
		for($g=0;$g<count($facilityId);$g++){
			$FacProviderId[$facilityId[$g]] = array_values($FacProviderId[$facilityId[$g]]);
		}
		for($f=0;$f<count($facSchTmpId);$f++){
			$schTmpId = join(',',$facSchTmpId[$f]);
			//---- Start Get Facility Open and close Time ------------
			$qry = "select min(morning_start_time) as morning_start_time 
					from schedule_templates where id in($schTmpId)";
			$DRecstartTimeRes = imw_query($qry);
			$startTimeRes = fetchArray($DRecstartTimeRes);
			$slotStartTimeArr = explode(':',substr($startTimeRes[0]['morning_start_time'],0,-3));
			$qry = "select max(morning_end_time) as morning_end_time
					from schedule_templates where id in($schTmpId)";
			$DRecendTimeRes = imw_query($qry);
			$endTimeRes = fetchArray($DRecendTimeRes);
			$officeCloseTime = substr($endTimeRes[0]['morning_end_time'],0,2);
			$slotEndTime = explode(':',substr($endTimeRes[0]['morning_end_time'],0,-3));
			$j = 1;
			//-- get Office Open Hours ------	
			for($o=$slotStartTimeArr[0];$o<=$officeCloseTime;$o++){
				$j++;
			}
			$hrToMin = ($j * 60) + $slotEndTime[1];		
			$startHrTime = $slotStartTimeArr[0] - 1;
			$startMinTime = $slotStartTimeArr[1];
			$startHrTime = $startHrTime < 10 ? '0'.(int)$startHrTime : $startHrTime;
			$schTmpFacTime[$facSchTmp_id[$f]]['start_time'] = $startHrTime.':'.$startMinTime;
			$schTmpFacTime[$facSchTmp_id[$f]]['hrToMin'] = $hrToMin;
		}	
		//---- End Get Facility Open and close Time ------------
		//--- Get Time Sloat as per facility -----
		$timeSlotRes = DEFAULT_TIME_SLOT;
		if(count($facilityId)>0){
			//---- Xml File header Content --------
			$xmlData = '<?xml version="1.0" encoding="UTF-8" ?>
				<schedule>
					<dt value="'.$dt.'">
						<facilities>
			';
			//--- Start Facility loop -------
			for($f=0;$f<count($facilityId);$f++){ 
				//--- Get Facility Id --------
				$facId = $facilityId[$f];
				$fac_start_time = $schTmpFacTime[$facId]['start_time'];
				$facHrToMin = $schTmpFacTime[$facId]['hrToMin'];
				$xmlData .= '
					<facility id="'.$facilityId[$f].'" start_time="'.$fac_start_time.'" hrToMin="'.$facHrToMin.'">
						<providers>
				';
				//--- Start Provider loop -------
				for($p=0;$p<count($FacProviderId[$facId]);$p++){ 
					//--- Get Provider Details -------
					$pid = $FacProviderId[$facId][$p];
					$qry = "select provider_color,lname,fname,mname,user_type,max_appoint
							from users where id = '$pid'";
					$DRecphyRes = imw_query($qry);
					$phyRes = fetchArray($DRecphyRes);
					$phyLname = $phyRes[0]['lname'];
					$phyFname = $phyRes[0]['fname'];
					$phyMname = $phyRes[0]['mname'];
					$phyName = $phyLname;
					if($phyLname != '' && $phyFname != ''){
						$phyName = $phyLname.', '.$phyFname;
					}
					else if($phyLname == '' && $phyFname != ''){
						$phyName = $phyFname;
					}
					$phyName .= ' '.$phyMname;
					$provider_color = $phyRes[0]['provider_color'];
					$user_type = $phyRes[0]['user_type'];
					$max_appoint = $phyRes[0]['max_appoint'];
					$qry = "select count(*) as rowCount from provider_notes 
							where provider_id = '$pid' and facility_id = '$facId'
							and delete_status = '0' and notes_date = '$dt'";
					$DRecrowRes = imw_query($qry);
					$rowRes = fetchArray($DRecrowRes);
					$notesStatus = 0;
					if($rowRes[0]['rowCount'] > 0){
						$notesStatus = 1;
					}
					$xmlData .= '					
							<provider id="'.$pid.'" name="'.trim($phyName).'" color="'.$provider_color.'" type="'.$user_type.'" notes="'.$notesStatus.'" max_appoint="'.$max_appoint.'">
								<slots>
					';
					$schTmpId = join(',',$mainTmpId[$facId][$pid]);
					$slotStartTimeArr = explode(':',$fac_start_time);
					$startHrTime2 = $slotStartTimeArr[0];
					$startTime = $slotStartTimeArr[1];
					//--- Start Time loop -------				
					for($t=0;$t<$facHrToMin;$t++){
						if($startTime == 0){
							$startTime = '00';
						}
						if($startTime >= 60){
							$startHrTime2++;
							$startTime = '00';
						}
						if($startHrTime > 12){
							$startHrTime = 1;
						}
						$endHrTime = $startHrTime2;
						if($startTime >= 50){
							$endHrTime++;
							$endTime = '00';
						}
						else{
							$endTime = $startTime+$timeSlotRes;
						}
						if($startHrTime2 < 10){
							$startHrTime2 = '0'.(int)$startHrTime2;
						}
						if($endHrTime < 10){
							$endHrTime = '0'.(int)$endHrTime;
						}
						
						//--- Start Get Specific Template Open Time---------
						$times_from = $startHrTime2.':'.$startTime.':00';					
						$qry = "select id,fldLunchStTm,fldLunchEdTm,morning_end_time,morning_start_time 
								from schedule_templates where id in($schTmpId)
								and morning_start_time<='$times_from'
								and morning_end_time > '$times_from' 
								order by morning_end_time desc";
						$DRectmpRes = imw_query($qry);
						$tmpRes = fetchArray($DRectmpRes);
						$singleTmpId = '';
						$fldLunchStTm = '';
						$fldLunchEdTm = '';
						$morning_start_time = '';
						$morning_end_time = '';
						for($tp=0;$tp<count($tmpRes);$tp++){ 
							$singleTmpId = $tmpRes[$tp]['id'];
							$fldLunchStTm = explode(':',$tmpRes[$tp]['fldLunchStTm']);
							$fldLunchEdTm = explode(':',$tmpRes[$tp]['fldLunchEdTm']);
							$morning_start_time = explode(':',$tmpRes[$tp]['morning_start_time']);
							$morning_end_time = explode(':',$tmpRes[$tp]['morning_end_time']);
						}
						$status = 'off';
						$slotColor = DEFAULT_OFFICE_CLOSED_COLOR;
						$qry = "select count(id) as rowCount 
								from schedule_templates where id in($singleTmpId)
								and morning_end_time <= '$times_from'";
						$DRecrowRes = imw_query($qry);
						$rowRes = fetchArray($DRecrowRes);
						if($rowRes[0]['rowCount']>0){
							$status = 'off';
						}else{
							$qry = "select count(id) as rowCount 
									from schedule_templates where id in($singleTmpId)
									and morning_start_time <= '$times_from'";
							$DRecrowRes = imw_query($qry);
							$rowRes = fetchArray($DRecrowRes);
							if($rowRes[0]['rowCount']>0){
								$status = 'on';
								$slotColor = $provider_color;
							}						
						}
						//---- Block Open Time Check -------
						$qry = "select b_desc,id,time_status from block_times where 
								'$times_from' = start_time and start_date = '$dt'
								and facility in ($facId) and provider in ($pid)";
						$DRecblockQryRes = imw_query($qry);
						$blockQryRes = fetchArray($DRecblockQryRes);
						$label = '';
						if($blockQryRes[0]['id']){
							$slotTempColor = "#000000";
							if($blockQryRes[0]['b_desc']){
								$label = $blockQryRes[0]['b_desc'];
								$status = 'block';
							}
							else{
								$label = 'block';
								$status = 'block';
							}
							$slotColor = $slotTempColor;
							if($singleTmpId == ''){
								$label = '';
								$status = 'off';
								$slotColor = DEFAULT_OFFICE_CLOSED_COLOR;
							}
							if($blockQryRes[0]['time_status'] == 'open'){
								$status = 'on';
								$label = '';
								$slotColor = $provider_color;
							}
						}
						
						if($label == ''){
							if($singleTmpId){
								$qry = "select template_label from schedule_label_tbl
										where '$times_from' between start_time and end_time
										and sch_template_id = '$singleTmpId'";
								$DReclabelRes = imw_query($qry);
								$labelRes = fetchArray($DReclabelRes);
								$label = $labelRes[0]['template_label'];
							}
							preg_match('/lunch/',$label,$labelcheck);
							if(count($labelcheck) == 0){
								if($startHrTime2 == $fldLunchStTm[0]){
									if($startTime >= $fldLunchStTm[1]){
										$slotColor = $provider_color;
										$label = 'lunch';
									}
									if($startTime >= $fldLunchEdTm[1]){
										$label = '';
									}
								}
							}
						}
						//--- End Get Specific Template ---------				
						
						$xmlData .= '
							<slot timing="'.$startHrTime2.':'.$startTime.'-'.$endHrTime.':'.$endTime.'" status="'.$status.'" color="'.$slotColor.'" label="'.$label.'" tmpId="'.$singleTmpId.'"></slot>
						';
						$startTime += $timeSlotRes;
						$t += $timeSlotRes -1;
					}
					//--- End Time loop -------
					$xmlData .= '
								</slots>
							</provider>					
					';
				}
				//--- End Provider loop -------
				$xmlData .= '
						</providers>
					</facility>
				';
			}
			//--- End Facility loop -------
			//---- Xml File Footer Content --------
			$xmlData .= '
						</facilities>
					</dt>
				</schedule>
			';		
		if($providerToLoad==""){
				if(file_exists( $dirPath.'load_xml/'.$dt.'_schedulerData.xml')){
					unlink( $dirPath.'load_xml/'.$dt.'_schedulerData.xml');
				}		
				$fp = fopen( $dirPath.'load_xml/'.$dt.'_schedulerData.xml','w');
				$data = fputs($fp,$xmlData);
				fclose($fp);
			}
			if($providerToLoad!=""){
				if(file_exists( $dirPath.'load_xml/'.$dt."_".$providerToLoad.'_schedulerData.xml')){
					unlink( $dirPath.'load_xml/'.$dt."_".$providerToLoad.'_schedulerData.xml');
				}		
				$fp = fopen( $dirPath.'load_xml/'.$dt."_".$providerToLoad.'_schedulerData.xml','w');
				$data = fputs($fp,$xmlData);
				fclose($fp);
			}
		}
	}
	
	function convertToTimestamp($dtTemp){
		$arrDtFrom = explode("-",$dtTemp);
		$yrsFrom = $arrDtFrom[0];
		$mthFrom = $arrDtFrom[1];		
		$dysFrom = $arrDtFrom[2];
		$tsFrom = mktime(0,0,0,$mthFrom,$dysFrom,$yrsFrom);
		return $tsFrom;
	}
	
	function getWeekCount($date){
		$week = ceil($date/7);
		return $week;
	}
	
	function get_time_loop($start_time,$end_time,$slot="10",$format="h:i A"){
		$available_slot = array();
		if($start_time && $end_time){
			$start_time= strtotime($start_time);
			$end_time= strtotime($end_time);
			$min=$slot;
			$sec="60";
			$v=$min*$sec;
			$slot_1=$slot_2="";
			for($i=$start_time;$i<$end_time;$i=$i+$v){
				$slot = array();
				$slot['start'] = $i;
				$slot['end'] = $i+$v;
				array_push($available_slot, $slot);
			}
		}
		return($available_slot);
	}
	
	//function to keep log of added and deleted provider schedules
	function tmp_log($act_typ, $summary, $provider_id='', $facility_id='', $template_id='', $on_date='', $weekday='', $for_future='',$dev_summary='')
	{
		if(!$dev_summary)$dev_summary=$summary;
		if($template_id)
		{
			$q=imw_query("select schedule_name from schedule_templates WHERE id IN ($template_id) order by id asc");
			while($d=imw_fetch_object($q))
			{
				$template_name.=imw_real_escape_string($d->schedule_name).', ';	
			}
		}
		$act_typ="Schedule/$act_typ";
		imw_query("insert into schedule_template_log set `act_typ` = '$act_typ',
					`summary` = '$summary',
					`developer_summary` = '$dev_summary',
					`provider_id` = '$provider_id',
					`provider_name` = '',
					`facility_id` = '$facility_id',
					`facility_name` = '',
					`template_id` = '$template_id',
					`template_name` = '$template_name',
					`on_date` = '$on_date',
					`weekday` = '$weekday',
					`for_future` = '$for_future',
					`act_datetime` = '".date('Y-m-d H:i:s')."',
					`logged_user_id` = '$_SESSION[authUserID]',
					`logged_user_name` = '$_SESSION[authProviderName]',
					`logged_facility` = '$_SESSION[login_facility]',
					`ip` = '$_SERVER[REMOTE_ADDR]'");//or die(imw_error().' unable to log action');
	}
	
	

?>