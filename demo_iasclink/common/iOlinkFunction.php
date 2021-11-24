<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php


//FUNCTION TO HIGHLIGHT SURGEON'S SCHEDULE

function getFirstSurgeryColor($srgnDos,$srgnId,$makeSurgeryTimeDiv='',$practiceName='',$coordinatorType='') {
	$firstSurgeryLimitQry='';
	list($YrFn,$MnFn,$DyFn)=explode('-',$srgnDos);
	if($srgnId) {
		// GETTING LOGIN USER NAME
		$srgnNameQry = "SELECT fname, mname, lname FROM users WHERE usersId = '".$srgnId."'";
		$srgnNameRes = imw_query($srgnNameQry);
		$srgnNameRow = imw_fetch_array($srgnNameRes);
		$srgnFName = $srgnNameRow['fname'];
		$srgnMName = $srgnNameRow['mname'];
		$srgnLName = $srgnNameRow['lname'];
		// GETTING LOGIN USER NAME
	
		$getSurgeryAndQry = " AND patient_in_waiting_tbl.surgeon_fname='".addslashes($srgnFName)."' 
								AND patient_in_waiting_tbl.surgeon_mname='".addslashes($srgnMName)."' 
								AND patient_in_waiting_tbl.surgeon_lname='".addslashes($srgnLName)."'
								AND patient_in_waiting_tbl.surgeon_fname=users.fname 
								AND patient_in_waiting_tbl.surgeon_mname=users.mname 
								AND patient_in_waiting_tbl.surgeon_lname=users.lname";
								
	}else {
		
		$AndPracticeNameQry="";
		if($coordinatorType!='Master') {
			$AndPracticeNameQry = " AND users.practiceName='' ";
			if($practiceName) { $AndPracticeNameQry = getPracticeUser($practiceName,"AND","users");}
		}
		
		$getSurgeryAndQry = " 	$AndPracticeNameQry 
								AND patient_in_waiting_tbl.surgeon_fname=users.fname 
								AND patient_in_waiting_tbl.surgeon_mname=users.mname 
								AND patient_in_waiting_tbl.surgeon_lname=users.lname";
	}

	$getSurgeryTimeQry = "SELECT patient_in_waiting_tbl.* FROM patient_in_waiting_tbl,users 
						  WHERE patient_in_waiting_tbl.dos='".$srgnDos."' 
						  AND patient_in_waiting_tbl.patient_status!='Canceled'
						  AND patient_in_waiting_tbl.iasc_facility_id='".$_SESSION['iolink_iasc_facility_id']."'
						  AND users.deleteStatus !='Yes'
						  $getSurgeryAndQry 
						  ORDER BY patient_in_waiting_tbl.surgery_time ASC";
	
			
	$getSurgeryTimeRes = imw_query($getSurgeryTimeQry) or die(imw_error());
	$getSurgeryTimeNumRow = imw_num_rows($getSurgeryTimeRes);
	$highLightSrgSchArr = array();
	if($getSurgeryTimeNumRow>0) {
		while($getSurgeryTimeRow = imw_fetch_array($getSurgeryTimeRes)) {
			$highLightSrgSchArr[]=$DyFn;
		}
	}
	return $highLightSrgSchArr;
}
//END FUNCTION TO HIGLIGHT SURGEON'S SCHEDULE

function getFirstSurgeryTime($srgnDos,$srgnId,$makeSurgeryTimeDiv='',$practiceName='',$coordinatorType='') {
	$firstSurgeryTime='';
	$firstSurgeryLimitQry='';
	list($YrFn,$MnFn,$DyFn)=explode('-',$srgnDos);
	if($srgnId) {
		// GETTING LOGIN USER NAME
		$srgnNameQry = "SELECT fname, mname, lname FROM users WHERE usersId = '".$srgnId."'";
		$srgnNameRes = imw_query($srgnNameQry);
		$srgnNameRow = imw_fetch_array($srgnNameRes);
		$srgnFName = $srgnNameRow['fname'];
		$srgnMName = $srgnNameRow['mname'];
		$srgnLName = $srgnNameRow['lname'];
		// GETTING LOGIN USER NAME
	
		$getSurgeryAndQry = " AND patient_in_waiting_tbl.surgeon_fname='".addslashes($srgnFName)."' 
								AND patient_in_waiting_tbl.surgeon_mname='".addslashes($srgnMName)."' 
								AND patient_in_waiting_tbl.surgeon_lname='".addslashes($srgnLName)."'
								AND patient_in_waiting_tbl.surgeon_fname=users.fname 
								AND patient_in_waiting_tbl.surgeon_mname=users.mname 
								AND patient_in_waiting_tbl.surgeon_lname=users.lname";
								
	}else {
		
		$AndPracticeNameQry="";
		if($coordinatorType!='Master') {
			$AndPracticeNameQry = " AND users.practiceName='' ";
			if($practiceName) { $AndPracticeNameQry = getPracticeUser($practiceName,"AND","users");}
		}
		$getSurgeryAndQry = " 	$AndPracticeNameQry 
								AND patient_in_waiting_tbl.surgeon_fname=users.fname 
								AND patient_in_waiting_tbl.surgeon_mname=users.mname 
								AND patient_in_waiting_tbl.surgeon_lname=users.lname";
	}
	
	if($makeSurgeryTimeDiv) {
		$firstSurgeryTime .= "<div id='iOLinkSurgeryTimeId".$DyFn."' style='position:absolute; z-index:9999; top:10; left:20px;background-color:#FFFF99; display:none;'>";
		
	}else {
		$firstSurgeryLimitQry =" limit 0,3";
	}
	$getSurgeryTimeQry = "SELECT patient_in_waiting_tbl.* FROM patient_in_waiting_tbl,users 
						  WHERE patient_in_waiting_tbl.dos='".$srgnDos."' 
						  AND patient_in_waiting_tbl.patient_status!='Canceled'
						  AND users.deleteStatus !='Yes'
						  AND patient_in_waiting_tbl.iasc_facility_id='".$_SESSION['iolink_iasc_facility_id']."'
						  $getSurgeryAndQry 
						  GROUP BY patient_in_waiting_tbl.surgeon_fname,patient_in_waiting_tbl.surgeon_mname,patient_in_waiting_tbl.surgeon_lname  
						  ORDER BY patient_in_waiting_tbl.surgery_time ASC 
						  $firstSurgeryLimitQry";	
			

	$getSurgeryTimeRes = imw_query($getSurgeryTimeQry) or die(imw_error());
	$getSurgeryTimeNumRow = imw_num_rows($getSurgeryTimeRes);
	if($getSurgeryTimeNumRow>0) {
		$firstSurgeryTime .= "<table class='table_collapse alignLeft' style='border:none;'>";
		$highLightSrgSchArr = array();
		while($getSurgeryTimeRow = imw_fetch_array($getSurgeryTimeRes)) {
			$srgnStubFName = $getSurgeryTimeRow['surgeon_fname'];
			$srgnStubMName = $getSurgeryTimeRow['surgeon_mname'];
			$srgnStubLName = $getSurgeryTimeRow['surgeon_lname'];
			
			$srgnStubFirstSurgeryTime='';
			$srgnStubLastSurgeryTime='';
			//$hrefLinkStart="<a class='link_home' onClick='javascript:iOLink_swap_cal_color(\"mon_$DyFn\",\"Yes\",".$DyFn.",document.getElementById(\"hiddSelectedPrevDayId\").value);schClick(\"$YrFn\",\"$MnFn\",\"$DyFn\",\"$srgnId\");'>";
			//$hrefLinkEnd="</a>";
			$srgnStubName = substr($srgnStubFName,0,1).substr($srgnStubLName,0,1);
			$firstSurgeryTime .="<tr><td class='text_10 alignLeft nowrap' style='font-size:9px; '>".$srgnStubName."</td><tr>";
		}
		$firstSurgeryTime .="</table>";
	}
	if($makeSurgeryTimeDiv) {
		$firstSurgeryTime .="</div>";
		
	}				 
	return $firstSurgeryTime;

}

function getSchTmpData($selDate,$pro='',$facId=''){
	$dtArr = explode('-',$selDate);
	$weekDay = ceil($dtArr[2]/7);
	$day = date('w',mktime(0,0,0,$dtArr[1],$dtArr[2],$dtArr[0]));
	if($day == 0){
		$day = 7;
	}
	$res = array();
	if($pro){
		$pro12 = " and provider_schedule_tmp.provider = '$pro'";
	}
	if($facId){
		$fac = " and provider_schedule_tmp.facility in($facId)";
	}		
	$qry = "select provider_schedule_tmp.id,provider_schedule_tmp.del_status,
			provider_schedule_tmp.delete_row,provider_schedule_tmp.status,
			provider_schedule_tmp.provider,provider_schedule_tmp.facility,
			provider_schedule_tmp.today_date,provider_schedule_tmp.sch_tmp_id
			from provider_schedule_tmp join users on provider_schedule_tmp.provider = users.id				
			where provider_schedule_tmp.today_date <= '$selDate' $pro12 $fac		
			and provider_schedule_tmp.week$weekDay = '$day' 
			order by provider_schedule_tmp.provider,provider_schedule_tmp.facility, 
			provider_schedule_tmp.del_status";
	//$res = ManageData::getQryRes($qry);	
	$res = getQryRes($qry);	
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
		$qry = "select id,facility , provider,sch_tmp_id,today_date
				from provider_schedule_tmp where id in ($schIdStr)
				order by provider,facility ,sch_tmp_id";
		//$return = manageData::getQryRes($qry);
		$return = getQryRes($qry);
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
	if(count($return)>0){	
		$return = @array_values($return);
	}
	return $return;		
}

//function getSurgeonAvailability($year,$month,$srgnFname,$srgnMname,$srgnLname,$facilityId) 
function month_scheduleprovider_Highlight($dt,$sel_week_day,$sel_pro,$faclitySel,$syncStatus='') {
	$schExist = '';
	$faclitySel=(!$faclitySel)?$_SESSION['iolink_iasc_facility_id']:$faclitySel;
	if(!$sel_week_day) {
		list($yrSel,$mnthSel,$dySel) = explode('-',$dt);
		$eff_day = date("j",mktime(0, 0, 0, $mnthSel,$dySel,$yrSel));
		$eff_wday = date("w",mktime(0, 0, 0, $mnthSel,$dySel,$yrSel));
		$sel_week_day = ($i+1).'_'.$eff_wday.'_'.$eff_day;
	}
	$imedicFileName = 'connect_imwemr.php';
	if($syncStatus=='iosync') {
		include_once __DIR__ . "/../".$imedicFileName;
		//include_once($imedicFileName);
	}else {
		include __DIR__ . "/../".$imedicFileName;
	}
	$finalRes = getSchTmpData($dt,$sel_pro,$faclitySel);
	$tmpTableIdArr = array();
	for($i=0;$i < count($finalRes);$i++){
		$tmpTableIdArr[] = $finalRes[$i]['id'];
	}
	$tmpTableId = join(',',$tmpTableIdArr);
	if(!$tmpTableId) { $tmpTableId='0'; }
	$qry = "select provider_schedule_tmp.facility,provider_schedule_tmp.sch_tmp_id,
			provider_schedule_tmp.provider from provider_schedule_tmp
			join users on provider_schedule_tmp.provider = users.id
			where provider_schedule_tmp.id in ($tmpTableId)
			AND provider_schedule_tmp.facility='".$faclitySel."'
			order by users.lname,users.fname";	
		
	//$finalRes = ManageData::getQryRes($qry);
	$finalRes = getQryRes($qry);
	
	//START 
		//--- Start to get the data for display ----------------				
		$ids_nm='nm'.$dys;
		$myheight="";
		$output=""; 
		$cnt = 0;
		$cnt1 = 3;
		list($y,$m,$d) = preg_split('/-/',$dt);
		$day = date('w',mktime(0,0,0,$m,$d,$y));
		if($day == 6 || $day == 0){
			$cnt1 = 1;
		}
		
		$providerSchData = array();
		for($a = 0;$a < count($finalRes);$a++){
			$facilityId = $finalRes[$a]['facility'];
			$sch_tmp_id = $finalRes[$a]['sch_tmp_id'];
			$provider  = $finalRes[$a]['provider'];
			$vquery_css = "select schedule_name,morning_start_time,morning_end_time from schedule_templates where id='$sch_tmp_id'";				
			$vsql_css = imw_query($vquery_css);
			$rt_rss=@imw_fetch_array($vsql_css);							
			$sch_name=$rt_rss['schedule_name'];	
			//GETTING START TIME AND END TIME ie A & P INSTEAD OF AM AND PM BY SURINDER
			$vquery_css = "select schedule_name,morning_start_time,morning_end_time from schedule_templates where id='$sch_tmp_id'";				
			$vsql_css = imw_query($vquery_css);
			$rt_rss = imw_fetch_array($vsql_css);
			$morning_start_time = $rt_rss['morning_start_time'];		
			list($start_hours,$start_minutes,$start_sec) = explode(":",$morning_start_time);
			$sch_time_start = mktime($start_hours, $start_minutes, $start_sec);
			$sch_start_time = date("g:iA",$sch_time_start);
			$morning_end_time = $rt_rss['morning_end_time'];
			list($start_hours,$start_minutes,$start_sec) = explode(":",$morning_end_time);
			$sch_time_end = mktime($start_hours, $start_minutes, $start_sec);
			$sch_end_time = date("g:iA",$sch_time_end);
			//$sch_end_time_value = $sch_start_time." : ".$sch_end_time;
			$sch_time = $sch_start_time."-".$sch_end_time;
			//END TIME GETTING
			//CODE TO GET FIRST TWO LETTER OF CITY(LOCATION)
			$vquery_d = "select name,city from facility where id='$facilityId'";				
			$vsql_d = imw_query($vquery_d);
			$rs_d = imw_fetch_array($vsql_d);							
			$fac_name=$rs_d["name"];
			$fac_city=$rs_d["city"];
			$fac_name_explode=explode(" ",$fac_name);
			$fac_name_firststring=substr($fac_name_explode{0},0,1);
			$fac_name_secondstring=substr($fac_name_explode{1},0,1);
			if($fac_name_secondstring==""){
				$fac_name_firststring=substr($fac_name_explode{0},0,2);
			}
			if($fac_name_secondstring=="") {  //if city has only single name like 'Colt' THEN GET 'CO'
				$fac_namefirst2letters=$fac_name_firststring.$fac_name_secondstring;
			} else {  //if city has not single name like 'Colt Neck' then get 'CN'
				$fac_namefirst2letters=$fac_name_firststring.$fac_name_secondstring;
			}	
			//END CODE TO GET FIRST TWO LETTER OF CITY(LOCATION)			
			//CODE TO GET PHYSICIAN INITIAL
			$physician_name_qry = "select fname,lname,provider_color,id from users where id='$provider'";
			$physician_name_res = imw_query($physician_name_qry);
			$physician_name_row = imw_fetch_array($physician_name_res);	
			$physician_name = array();
			$physician_name[] = substr($physician_name_row["fname"],0,1);
			$physician_name[] = substr($physician_name_row["lname"],0,1);
			$physician_intial = join($physician_name);
			if($physician_intial != '' || $fac_namefirst2letters != ''){
				$schExist = 'yes';
				$new_data .= '<tr><td alighn="left" class="text_10b" style="background-color:#9C6A9D;">';//purple
				$new_data .= str_replace('\'','',strtoupper($physician_intial)."-".strtoupper($fac_namefirst2letters).' ');
				$new_data .= $sch_time;
				$new_data .= '</td></tr>';
				if($cnt < $cnt1){
					$providerSchData[$cnt]['name'] = str_replace('\'','',strtoupper($physician_intial)."-".strtoupper($fac_namefirst2letters).' ');
					$providerSchData[$cnt]['provider_color'] = $physician_name_row["provider_color"];
					$providerSchData[$cnt]['id'] = $physician_name_row["id"];
					$providerSchData[$cnt]['facId'] = $facilityId;
					$cnt++;
				}
			}
		}
		//-- Tool Tip Div ----
		if($new_data){
			$output.= "
				<div style='display:none; background-color:#FFCC33; width:160px; z-index:1000; position:absolute;' id='".$dt."' >
					<table cellpadding='0' cellspacing='0'>
					".$new_data."
					</table>
				</div>
			";
		}
		if(count($finalRes)>0)
		{
			$MOUSEOUT="onMouseout='calender_tool_tip(\"".$dt."\",\"none\");'";
			$MOUSEOVER="onMouseOver='calender_tool_tip(\"".$dt."\",\"block\");'";
			$output.="<table cellpadding='0' border='0' $MOUSEOUT $MOUSEOVER cellspacing='0' style='padding-left:0px;'>";
		}
		for($a=0;$a < count($providerSchData);$a++){
			$provider = $providerSchData[$a]['id'];
			$name = $providerSchData[$a]['name'];
			$provider_color = $providerSchData[$a]['provider_color'];
			$facilityId = $providerSchData[$a]['facId'];
			//--- Get Records as per provider and facility and date ---------//
			$qry = "select count(*) as rowCount from provider_notes 
					where provider_id = '$provider' and facility_id = '$facilityId' 
					and notes_date = '$dt'";
			$qryId = imw_query($qry);
			list($rowCount) = imw_fetch_array($qryId);
			$class= 'text_9';
			$provider_color1 = '';
			if($rowCount>0){
				$class= 'text_10b';
				$colorArr = array('#ECE9D8','#FEFED5','#FFFFCC','#FFCC33','#FFCC66');
				if(!in_array($provider_color,$colorArr)){
					$provider_color1 = 'style="color:'.$provider_color.'"';
				}
			}
			$schExist = 'yes';
			$output.= "<tr><td valign='top' class='text_9' style=' background-color:#9C6A9D;' >";	
			//$output .= "<a $provider_color1 class='$class' href='javascript:void(0)' id='".$dt."_id_".$a."' onClick='open_note(\"$provider\",\"$facilityId\",\"$dt\",this.id)'>";
			//$output.= $name;
			$output.= "</a></td></tr>";
		}
		if(sizeof($finalRes)>0)
		{
			$output.= "</table>";	
		}else{
			$output="";
		}
			
	//END
	imw_close($link_imwemr);
	//$scEMRFileName = 'common/conDb.php';
	$scEMRFileName = 'conDb.php';
	if($syncStatus=='iosync') {
		include_once __DIR__ . "/".$scEMRFileName;
	}else {
		include __DIR__ . "/".$scEMRFileName;
	}
	//return $output;	
	return $schExist;
}

function getiAscOfficeTimings($dt,$strFacIds,$providerToLoad="", $dirPath=""){
	
	//include('connect_imwemr.php');
	list($y,$m,$d) = explode('-',$dt);
	$dt = date('Y-m-d',mktime(0,0,0,$m,$d,$y));
	$finalResArr = getSchTmpData($dt,$providerToLoad,$strFacIds);
    
    //print "<pre>";
    //print_r($finalResArr);
    //die("amit");
	
	$tmpTableIdArr = array();
	for($i=0;$i<count($finalResArr);$i++){
		$tmpTableIdArr[] = $finalResArr[$i]['id'];
	}
	$tmpTableId = join(',',$tmpTableIdArr);
	if(!$tmpTableId) { $tmpTableId='0';}
	$qry = "select provider_schedule_tmp.facility,provider_schedule_tmp.sch_tmp_id,
			provider_schedule_tmp.provider from provider_schedule_tmp
			join users on provider_schedule_tmp.provider = users.id
			where provider_schedule_tmp.id in ($tmpTableId) 
			order by provider_schedule_tmp.id DESC LIMIT 0,1";
	
	$res = imw_query($qry) or die(imw_error());
	$sch_tmp_id='';
	if(imw_num_rows($res)) {
		$row = imw_fetch_array($res);
		$sch_tmp_id = $row['sch_tmp_id'];
	}
	//imw_close($link_imwemr);
	//include("common/conDb.php");
	return $sch_tmp_id;
}

function is_hl7_generate($type){
	if(strtoupper($type)=='ADT')
		return ( defined( 'HL7_ADT_GENERATION' ) && HL7_ADT_GENERATION === true );
	else
		return false;
}

?>