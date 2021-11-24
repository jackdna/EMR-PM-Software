<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php 
include("connect_imwemr.php");

if(!function_exists("core_phone_format")){
	function core_phone_format($phone_number,$format=''){
		$return = "";
		$default_format = $format!=''? $format : $GLOBALS['phone_format'];
		$refined_phone = $default_format == '' ? $phone_number : preg_replace('/[^0-9]/','',$phone_number);
		
	
		switch($default_format){
			case "###-###-####"://-------1
				$return = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "$1-$2-$3", $refined_phone);
				break;
			case "(###) ###-####"://-------2
				$return = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $refined_phone);
				break;
			case "(##) ###-####"://-------3
				$return = preg_replace("/([0-9]{2})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $refined_phone);
				break;	
			case "(###) ###-###"://-------4
				$return = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{3})/", "($1) $2-$3", $refined_phone);
				break;	
			case "(####) ######"://-------5
				$return = preg_replace("/([0-9]{4})([0-9]{6})/", "($1) $2", $refined_phone);
				break;
			case "(####) #####"://-------6
				$return = preg_replace("/([0-9]{4})([0-9]{5})/", "($1) $2", $refined_phone);
				break;	
			case "(#####) #####"://-------7
				$return = preg_replace("/([0-9]{5})([0-9]{5})/", "($1) $2", $refined_phone);
				break;	
			case "(#####) ####"://-------8
				$return = preg_replace("/([0-9]{5})([0-9]{4})/", "($1) $2", $refined_phone);
				break;		
			default:
				$return = $refined_phone;
				break;
		}
		return $return;
	}
}
if(!function_exists("getApptHx")){
	function getApptHx($patient_id,$report_end_date){
		
		$appHx = '';
		$dateRange=" AND sc.sa_app_start_date <= '".date("Y-m-d")."' ";
		if($report_end_date){
			$dateRange=" AND sc.sa_app_start_date <='".$report_end_date."' ";	
		}
		$schDataQry = "SELECT DATE_FORMAT(sc.sa_app_start_date,'%m-%d-%Y') as appStrtDate,DATE_FORMAT(sc.sa_app_starttime,'%h:%i %p') as appStrtTime,
						sc.sa_patient_app_status_id as appStatus, CONCAT_WS(', ', us.lname, us.fname) as doctorName,fac.name as facName,slp.proc as procName 
						FROM schedule_appointments sc 
						LEFT JOIN users us ON us.id = sc.sa_doctor_id 
						LEFT JOIN facility fac ON fac.id = sc.sa_facility_id 
						LEFT JOIN slot_procedures slp ON slp.id = sc.procedureid 
						WHERE sc.sa_patient_id = '".$patient_id."'
						AND sc.sa_patient_app_status_id != '18'
						$dateRange
						ORDER BY sc.sa_app_start_date DESC
						LIMIT 0,3";
		
		$schDataQryRes = array();
		$qryQry = imw_query($schDataQry) or die($schDataQry.imw_error());
		while($schDataRow = imw_fetch_assoc($qryQry)){
			$schDataQryRes[] = $schDataRow;
		}
		$schDataDetailsArr = array();
		if(count($schDataQryRes)>0) {
			$appStatusArr = array(18=>"Cancel",13=>"Check-in",11=>"Check-out",2=>"Chart Pulled",202=>"Reschedule",6=>"Left Without Visit",3=>"No-Show",
									201=>"To-Do",0=>"New",200=>"Room # assignment",7=>"Insurance/Financial Issue");
			
			$appStatus='';
			foreach($appStatusArr as $key=>$val) {
				if($key==$schDataQryRes[$i]['appStatus']) {
					$appStatus = $val;	
				}
			}
			$appHx.='<table  border="0" cellpadding="0" cellspacing="0" style="width:700px;">
						<tr>
							<td align="left" colspan="6" bgcolor="#c0c0c0" style="width:700px;"><b>APPOINTMENT HISTORY</b></td>
						</tr>
						<tr align="left">
							<td class="text_10b" valign="top" style="width:80px;"><strong>Date</strong></td>
							<td class="text_10b" valign="top" style="width:80px;"><strong>Time</strong></td>
							<td class="text_10b" valign="top" style="width:110px;"><strong>Doctor</strong></td>
							<td class="text_10b" valign="top" style="width:120px;"><strong>Facility</strong></td>
							<td class="text_10b" valign="top" style="width:160px;"><strong>Procedure</strong></td>
							<td class="text_10b" valign="top" style="width:100px;"><strong>Status</strong></td>
						</tr>
					';
			for($i=0;$i<count($schDataQryRes);$i++){
				$appHx.='
						<tr align="left">
							<td class="text_10" valign="top" style="width:80px;">'.$schDataQryRes[$i]['appStrtDate'].'</td>
							<td class="text_10" valign="top" style="width:80px;">'.$schDataQryRes[$i]['appStrtTime'].'</td>
							<td class="text_10" valign="top" style="width:110px;">'.$schDataQryRes[$i]['doctorName'].'</td>
							<td class="text_10" valign="top" style="width:120px;">'.$schDataQryRes[$i]['facName'].'</td>
							<td class="text_10" valign="top" style="width:160px;">'.$schDataQryRes[$i]['procName'].'</td>
							<td class="text_10" valign="top" style="width:100px;">'.$appStatus.'</td>
						</tr>';
			}
			$appHx.='</table>';
		}
		return $appHx;
	}
}

ob_start();
$strQryGetPatientDemographicData="SELECT pd.*,date_format(pd.date,'%m-%d-%Y') as createdDate, sa.sa_app_start_date,
									date_format(pd.DOB,'%m-%d-%Y') as ptDOB,
									DATE_FORMAT(NOW(), '%Y') - DATE_FORMAT(dob, '%Y') - (DATE_FORMAT(NOW(), '00-%m-%d') < DATE_FORMAT(dob, '00-%m-%d')) AS age
									FROM schedule_appointments sa
									LEFT JOIN patient_data pd ON pd.pid = sa.sa_patient_id											
									WHERE sa.id = ".$schedule_id;									
$rsQryGetPatientDemographicData = imw_query($strQryGetPatientDemographicData);
$numRowQry = imw_num_rows($rsQryGetPatientDemographicData);
if($numRowQry > 0){
	$patientDetails = imw_fetch_array($rsQryGetPatientDemographicData);	
	$pid = $patientDetails['pid'];
	$imwDOS = $patientDetails['sa_app_start_date'];
	$patientName = $patientDetails['lname'].', '.$patientDetails['fname'].' ';
	$patientName .= $patientDetails['mname'];
	$created_date = $patientDetails['created_date'];	
	$date_of_birth = $patientDetails['ptDOB'];
	$cityAddress = $patientDetails['city'];
	if($patientDetails['state'])
		$cityAddress .= ', '.$patientDetails['state'].' ';
	else
		$cityAddress .= ' ';
	$cityAddress .= $patientDetails['postal_code'];
	$age = $patientDetails['age'];
	//--- Get Physician Details --------
	$phyId = $patientDetails['providerID'];
	if($phyId){
		$phyQry = "select concat(fname,', ',lname) as name, mname from users
				where id = '$phyId'";
		$rsPhyQry = @imw_query($phyQry);	
		$numRowPhyQry = @imw_num_rows(rsPhyQry);
		if($numRowPhyQry > 0){
			$phyDetails = imw_fetch_array($rsPhyQry);	
			$phyName = trim($phyDetails['name'].' '.$phyDetails['mname']);		
		}
		
	}
	//--- Get Reffering Physician Details --------
	$reffPhyId = $patientDetails['primary_care_id'];
	$refQry = "select concat(FirstName,', ',LastName) as name, MiddleName from refferphysician
				where physician_Reffer_id = '$reffPhyId'";
	$rsRefQry = imw_query($refQry);
	$numRowRefQry = imw_num_rows($rsRefQry);
	if($numRowRefQry > 0){
		$refPhyDetails = imw_fetch_array($rsRefQry);	
		$reffPhyName = trim($refPhyDetails['name'].' '.$refPhyDetails['MiddleName']);
	}	
	if(!$reffPhyName) $reffPhyName = $phyName;
	//---- Get Patient Facility Details -------
	$default_facility = $patientDetails['default_facility'];
	$facilityQry = "select facilityPracCode from pos_facilityies_tbl 
					where pos_facility_id = '$default_facility'";
	$rsFacilityQry = imw_query($facilityQry);				
	$numRowFacilityQry = imw_num_rows($rsFacilityQry);
	if($numRowFacilityQry > 0){
		$facilityDetail = imw_fetch_array($rsFacilityQry);			
		$facilityRes = $facilityDetail['facilityPracCode'];
	}	
	//--- Get Detail who create patient -------
	$created_by = $patientDetails['created_by'];
	$createdByQry = "select concat(fname,', ',lname) as name, mname from users
					where id = '$created_by'";
	$rsCreatedByQry = imw_query($createdByQry);
	$numRowCreatedByQry	= imw_num_rows($rsCreatedByQry);
	if($numRowCreatedByQry > 0){
		$createByDetail = imw_fetch_array($rsCreatedByQry);
		$createByName = trim($createByDetail['name'].' '.$createByDetail['mname']);
	}				
}
//--- Get Patient Responsible Party Details -----
$patientRespPartyQry = "select *,
						date_format(dob,'%m-%d-%Y') as respPartyDOB,
						DATE_FORMAT(NOW(), '%Y') - DATE_FORMAT(dob, '%Y') - (DATE_FORMAT(NOW(), '00-%m-%d') < DATE_FORMAT(dob, '00-%m-%d')) AS respPartyAge
						from resp_party where patient_id = '$pid'
						and fname != '' and lname != ''";
$rsPatientRespPartyQry = imw_query($patientRespPartyQry);	
$numRowPatientRespPartyQry = imw_num_rows($rsPatientRespPartyQry);				
if($numRowPatientRespPartyQry > 0){
	$res_party_detail[] = imw_fetch_array($rsPatientRespPartyQry);
}

//--- Get Patient Occupation Details ------
$patientOccQry = "select * from employer_data where pid = '$pid' and name != ''";
$rsPatientOccQry = imw_query($patientOccQry);	
$numRowPatientOccQry = imw_num_rows($rsPatientOccQry);				
if($numRowPatientOccQry > 0){
	$emp_details[] = imw_fetch_array($rsPatientOccQry);
}

//--- Get Default Facility Details -------
$defaultFacilityQry = "select default_group from facility where facility_type = 1";
$rsDefaultFacilityQry = imw_query($defaultFacilityQry);	
$numRowDefaultFacilityQry = imw_num_rows($rsDefaultFacilityQry);				
if($numRowDefaultFacilityQry > 0){
	$facilityDetail = imw_fetch_array($rsDefaultFacilityQry);
	if(count($facilityDetail)>0){
		$gro_id = $facilityDetail['default_group'];
		$groupQry = "select * from groups_new where gro_id = '$gro_id'";
		$rsQroupQry = imw_query($groupQry);	
		$numRowQroupQry = imw_num_rows($rsQroupQry);				
		if($numRowQroupQry > 0){
			$groupDetails = imw_fetch_array($rsQroupQry);
		}
	}
}


//--- Get Patient Image ----
$patientImage = "";
$p_imagename = $patientDetails['p_imagename'];
if($p_imagename<>""){
	$dirPath = $rootServerPath.'/'.$imwDirectoryName.'/interface/main/uploaddir'.$p_imagename;
	$dir_real_path = realpath($dirPath);
	$img_name = substr($p_imagename,strrpos($p_imagename,'/')+1);	
	//@copy($dir_real_path,'../../common/html2pdf/'.$img_name);
	if(file_exists($dir_real_path)){
		$patient_img['patient'] = $img_name;
		$fileSize = getimagesize($dir_real_path);		
		if($fileSize[0]>80 || $fileSize[0]>90){
			$imageWidth2 = 100;
			$patientImage = '<img style="cursor:pointer" src="'.$dirPath.'" alt="patient Image" width="'.$imageWidth2.'">';
		}else{
			$patientImage = '<img style="cursor:pointer" src="'.$dirPath.'" alt="patient Image">';
		}		
	}
}
?>

<table style="width:700px; " border="0" cellspacing="0" cellpadding="0">
	<tr> 
		<td style="width:250px;"><b><h1>FACE SHEET</h1></b></td>
		<td style="width:150px;"><b><?php echo $patientDetails['title'].' '.$patientName; ?></b></td>
		<td style="width:125px;" align="left">ID : <b><?php echo $patientDetails['id']; ?></b></td>
		<td align="center" style="width:175px;"><b><?php echo $groupDetails['name']; ?></b></td>
	</tr>
	<tr>
		<td align="left">DOB: <b><?php echo $date_of_birth." ($age)"; ?></b></td>
		<td></td>			
		<td rowspan="6" ><?php echo $patientImage; ?></td>
		<td align="center"><b><?php echo ucwords($groupDetails['group_Address1']); ?></b></td>						
	</tr>
	<tr> 	
		<td>Sex: <b><?php echo $patientDetails['sex']; ?></b></td>
		<td></td>
		<td align="center" class="text_9b"><b><?php echo ucwords($groupDetails['group_Address2']); ?></b></td>	
	</tr>
	<tr> 	
		<td><!--SS#: <b><?php //echo $patientDetails['ss']; ?></b>--></td>	
		<td></td>	
		<td align="center"><b><?php echo $groupDetails['group_City'].', '.$groupDetails['group_State'].' '.$groupDetails['group_Zip']; ?></b></td>	
	</tr>
	<tr>
		<td></td>
		<td></td>
		<td align="center"><b>Ph. # <?php echo core_phone_format($groupDetails['group_Telephone']); ?></b></td>
	</tr>
	<tr>
		<td></td>
		<td></td>
		<td align="center"><b>Fax # <?php echo core_phone_format($groupDetails['group_Fax']); ?></b></td>
	</tr>
</table>

<table cellpadding='0' cellspacing='0' border='0' style="width:700px; margin-top:5px;">
	<tr>
		<td bgcolor="#c0c0c0" align="left" colspan="4" style="width:100%;"><b><?php echo strtoupper('Patient Demographics'); ?></b></td>
	</tr>
	<tr align="left" valign="top">
		<td class="text_10b"><b><?php echo trim($patientDetails['title'].' '.ucwords($patientName)); ?></b></td>
		<td class="text_10b" width="180"><b><?php echo $date_of_birth; ?> (<?php echo $age;?>)</b></td>
		<td class="text_10b"><b><?php echo $patientDetails['id']; ?></b></td>
		<td class="text_10b"><b><?php echo ucwords($patientDetails['status']); ?></b></td>
	</tr>
	<tr align="left" valign="top">
		<td class="text_10" style="color:#444444"><i>Patient Name</i></td>
		<td class="text_10" style="color:#444444"><i>DOB</i></td>
		<td class="text_10" style="color:#444444"><i>ID</i></td>
		<td class="text_10" style="color:#444444"><i><?php if($patientDetails['status']) echo 'Marital'; else '&nbsp;'; ?></i></td>
	</tr>
	<tr><td height="7"></td></tr>
	<?php
	//--- Check That All Fields Are Not Blank -------
	if($patientDetails['ss'] != '' || $phyName != '' || $facilityRes != '' || $created_date != ''){
	?>	
	<tr align="left" valign="top">
		<td class="text_10b"><b><?php if(trim($patientDetails['ss'])!==''){ echo ucwords(substr_replace($patientDetails['ss'],'XXX-XX',0,6)); } ?></b></td>
		<td class="text_10b"><b><?php echo $phyName; ?></b></td>
		<td class="text_10b" valign="top"><b><?php echo $facilityRes; ?></b></td>
		<td class="text_10b"><b><?php echo $created_date; ?></b></td>
	</tr>
	<tr align="left" valign="top">
		<td class="text_10" style="color:#444444"><i><?php if($patientDetails['ss']) echo 'Social Security#'; else '&nbsp;'; ?></i></td>
		<td class="text_10" style="color:#444444"><i><?php if($phyName) echo 'Physician'; else '&nbsp;'; ?></i></td>
		<td class="text_10" style="color:#444444"><i><?php if($facilityRes) echo 'Facility'; else '&nbsp;'; ?></i></td>
		<td class="text_10" style="color:#444444"><i><?php if($created_date) echo 'Registration date'; else '&nbsp;'; ?></i></td>
	</tr>
	<tr><td height="7"></td></tr>
	<?php
	}
	//--- Check That All Fields Are Not Blank -------
	if($patientDetails['sex'] != '' || $reffPhyName != '' || $patientDetails['driving_licence'] != '' || $createByName != ''){
	?>				
	<tr align="left" valign="top">
		<td class="text_10b"><b><?php echo $patientDetails['sex']; ?></b></td>
		<td class="text_10b"><b><?php echo $reffPhyName; ?></b></td>
		<td class="text_10b"><b><?php echo $patientDetails['driving_licence']; ?></b></td>
		<td class="text_10b"><b><?php echo ucwords($createByName); ?></b></td>
	</tr>
	<tr align="left" valign="top">
		<td class="text_10" style="color:#444444"><i><?php if($patientDetails['sex']) echo 'Sex'; else '&nbsp;'; ?></i></td>
		<td class="text_10" style="color:#444444"><i><?php if($reffPhyName) echo 'Referring Dr.'; else '&nbsp;'; ?></i></td>
		<td class="text_10" style="color:#444444"><i><?php if($patientDetails['driving_licence']) echo 'DL #'; else '&nbsp;'; ?></i></td>
		<td class="text_10" style="color:#444444"><i><?php if(trim($createByName)) echo 'Created By'; else '&nbsp;'; ?></i></td>		
	</tr>
	<tr><td height="7"></td></tr>
	<?php
	}
	//--- Check That All Fields Are Not Blank -------
	if(trim($patientDetails['street']) != '' || $patientDetails['phone_home'] != '' || $cityAddress != '' || $patientDetails['phone_biz'] != '' || $patientDetails['phone_cell'] != ''){
	$street = trim(ucwords($patientDetails['street']));
	if(trim(ucwords($patientDetails['street'])) != '' && trim(ucwords($patientDetails['street2'])) != ''){
		$street .= ', ';
	}
	if(trim(ucwords($patientDetails['street2']))){
		$street .= trim(ucwords($patientDetails['street2']));
	}
	$phone_biz = $patientDetails['phone_biz'];
	$phone_label = 'Work Phone#';
	if(!$phone_biz){
		$phone_biz = $patientDetails['phone_cell'];
		$phone_label = 'Mobile Phone#';
	}
	?>
	<tr align="left" valign="top">
		<td class="text_10b"><b><?php echo $street; ?></b></td>
		<td class="text_10b"><b><?php echo trim(ucwords($cityAddress)); ?></b></td>
		<td class="text_10b"><b><?php echo core_phone_format($patientDetails['phone_home']); ?></b></td>
		<td class="text_10b"><b><?php echo core_phone_format($phone_biz); ?></b></td>
	</tr>
	<tr align="left" valign="top">
		<td class="text_10" style="color:#444444"><i><?php if(trim($patientDetails['street'])) echo 'Address'; else '&nbsp;'; ?></i></td>
		<td class="text_10" style="color:#444444"><i><?php if(trim($cityAddress)) echo 'City,State Zip'; else '&nbsp;'; ?></i></td>
		<td class="text_10" style="color:#444444"><i><?php if($patientDetails['phone_home']) echo 'Home Phone#'; else '&nbsp;'; ?></i></td>
		<td class="text_10" style="color:#444444"><i><?php if($phone_biz) echo $phone_label; else '&nbsp;'; ?></i></td>
	</tr>	
	<?php
	}
	?>
</table>
<?php
//--- Emergency Contact Details Check ------
if(trim($patientDetails['contact_relationship']) != '' || $patientDetails['phone_contact'] != '' || trim($patientDetails['patient_notes'] != '')){
?>
<table style="width:700px; margin-top:5px;" rules="none" cellpadding="0" cellspacing="0" border="0">
	<tr valign="middle" height="25px" class="text_10" bgcolor="#c0c0c0">
		<td align="left" colspan="3" bgcolor="#c0c0c0" style="width:100%;"><b><?php echo strtoupper('Emergency Contact Information'); ?></b></td>
	</tr>
	<tr align="left" valign="top">
		<td class="text_10b"><b><?php echo $patientDetails['contact_relationship']; ?></b></td>
		<td class="text_10b" width="180"><b><?php echo core_phone_format($patientDetails['phone_contact']); ?></b></td>
		<td class="text_10b"><b><?php echo trim(ucwords($patientDetails['patient_notes'])); ?></b></td>
	</tr>
	<tr align="left" valign="top">
		<td class="text_10" style="color:#444444"><i><?php if($patientDetails['contact_relationship']) echo 'Emergency Contact'; else '&nbsp;'; ?></i></td>
		<td class="text_10" width="180" style="color:#444444"><i><?php if($patientDetails['phone_contact']) echo 'Emergency Phone'; else '&nbsp;'; ?></i></td>
		<td class="text_10" style="color:#444444"><i><?php if(trim($patientDetails['patient_notes'])) echo 'Notes'; else '&nbsp;'; ?></i></td>
	</tr>
</table>
<?php
}
//---- Get Responsible Party Details if patient has -------
for($r=0;$r<count($res_party_detail);$r++){
	$resp_name = $res_party_detail[$r]['lname'].', ';
	$resp_name .= $res_party_detail[$r]['fname'].' ';
	$resp_name .= $res_party_detail[$r]['mname'];
	$dateOfBirth = $res_party_detail[$r]['respPartyDOB'];
	$resp_age = $res_party_detail[$r]['respPartyAge'];
	$city_address = $res_party_detail[$r]['city'].', ';
	$city_address .= $res_party_detail[$r]['state'].' ';
	$city_address .= $res_party_detail[$r]['zip'];
?>
<table style="width:700px; margin-top:5px;" cellpadding="0" cellspacing="0" border="0" rules="none">
	<tr valign="middle" height="25px" class="text_10" bgcolor="#c0c0c0">
		<td align="left" width="220" colspan="3" bgcolor="#c0c0c0" style="width:100%;"><b><?php echo strtoupper('Responsibility Party'); ?></b></td>
	</tr>
	<?php
	//--- Check That All Fields Are Not Blank -------
	if(trim($resp_name) != '' || $dateOfBirth != '' || $res_party_detail[$r]['marital'] != ''){
	?>
	<tr align="left">
		<td class="text_10b"><b><?php echo trim(ucwords($resp_name)); ?></b></td>					
		<td class="text_10b"><b><?php echo $dateOfBirth.' ( '.$resp_age.' ) '; ?></b></td>
		<td class="text_10b"><b><?php echo trim(ucwords($res_party_detail[$r]['marital'])); ?></b></td>
	</tr>
	<tr align="left">
		<td class="text_10" style="color:#444444"><i><?php if(trim($resp_name)) echo 'Name'; else echo '&nbsp;'; ?></i></td>
		<td class="text_10" width="180" style="color:#444444"><i><?php if($dateOfBirth) echo 'DOB'; else echo '&nbsp;'; ?></i></td>
		<td class="text_10"style="color:#444444"><i><?php if($res_party_detail[$r]['marital']) echo 'Marital'; else echo '&nbsp;'; ?></i></td>
	</tr>
	<tr><td height="7"></td></tr>
	<?php
	}
	//--- Check That All Fields Are Not Blank -------
	if(trim($res_party_detail[$r]['ss']) != '' || $res_party_detail[$r]['licence'] != ''){
	?>
	<tr align="left">
		<td class="text_10b"><b><?php echo $res_party_detail[$r]['ss']; ?></b></td>
		<td class="text_10b"><b><?php echo $res_party_detail[$r]['licence']; ?></b></td>
		<td class="text_10b">&nbsp;</td>
	</tr>
	<tr align="left">
		<td class="text_10" width="220" style="color:#444444"><i><?php if($res_party_detail[$r]['ss']) echo 'Social Security#'; else echo '&nbsp;'; ?></i></td>
		<td class="text_10" width="180" style="color:#444444"><i><?php if($res_party_detail[$r]['licence']) echo 'DL #'; else echo '&nbsp;'; ?></i></td>
		<td class="text_10b">&nbsp;</td>
	</tr>
	<tr><td height="7"></td></tr>
	<?php
	}
	//--- Check That All Fields Are Not Blank -------
	if(trim($res_party_detail[$r]['address']) != '' || $res_party_detail[$r]['address2'] != '' || $city_address != ''){
	?>
	<tr align="left">
		<td class="text_10b"><b><?php echo $res_party_detail[$r]['address']; ?></b></td>
		<td class="text_10b"><b><?php echo $res_party_detail[$r]['address2']; ?></b></td>
		<td class="text_10b"><b><?php echo $city_address; ?></b></td>					
	</tr>
	<tr align="left">
		<td class="text_10" style="color:#444444"><i><?php if(trim($res_party_detail[$r]['address'])) echo 'Address1'; else echo '&nbsp;'; ?></i></td>
		<td class="text_10" width="180" style="color:#444444"><i><?php if(trim($res_party_detail[$r]['address2'])) echo 'Address2'; else echo '&nbsp;'; ?></i></td>
		<td class="text_10" style="color:#444444"><i><?php if(trim($city_address)) echo 'City, State Zip'; else echo '&nbsp;'; ?></i></td>
	</tr>
	<tr><td height="7"></td></tr>
	<?php
	}
	//--- Check That All Fields Are Not Blank -------
	if(trim($res_party_detail[$r]['home_ph']) != '' || $res_party_detail[$r]['work_ph'] != '' || $res_party_detail[$r]['mobile'] != ''){
	?>
	<tr align="left">
		<td class="text_10b"><b><?php echo $res_party_detail[$r]['home_ph']; ?></b></td>
		<td class="text_10b"><b><?php echo $res_party_detail[$r]['work_ph']; ?></b></td>
		<td class="text_10b"><b><?php echo $res_party_detail[$r]['mobile']; ?></b></td>
	</tr>
	<tr align="left">
		<td class="text_10" style="color:#444444"><i><?php if($res_party_detail[$r]['home_ph']) echo 'Home Phone#'; else echo '&nbsp;'; ?></i></td>
		<td class="text_10" width="180" style="color:#444444"><i><?php if($res_party_detail[$r]['work_ph']) echo 'Work Phone#'; else echo '&nbsp;'; ?></i></td>
		<td class="text_10" style="color:#444444"><i><?php if($res_party_detail[$r]['mobile']) echo 'Mobile Phone#'; else echo '&nbsp;'; ?></i></td>
	</tr>
	<tr><td height="7"></td></tr>				
	<?php
	}
	?>
</table>
<?php
}
//--- get Occupation Details if patient fill in demographics --------
for($f=0;$f<count($emp_details);$f++){
	if($emp_details['city'])
		$emp_adderss .= $emp_details['city'];
	if($emp_details['state'])
		$emp_adderss .= ', '.$emp_details['state'];
	if($emp_details['postal_code'])
		$emp_adderss .= ' '.$emp_details['postal_code'];
?>
<table style="width:700px; margin-top:5px;" cellpadding="0" cellspacing="0" border="0" rules="none">
	<tr valign="middle" height="25px" class="text_10" bgcolor="#c0c0c0">
		<td align="left" colspan="3" bgcolor="#c0c0c0" style="width:100%;"><b><?php echo strtoupper('Patient Occupation'); ?></b></td>
	</tr>
	<?php
	//--- Check That All Fields Are Not Blank -------
	if(trim($patientDetails['occupation']) != '' || trim($emp_details['name']) != ''){
	?>
	<tr align="left">
		<td class="text_10b"><b><?php echo trim(ucwords($patientDetails['occupation'])); ?></b></td>
		<td class="text_10b"><b><?php echo trim(ucwords($emp_details['name'])); ?></b></td>
	</tr>
	<tr align="left">
		<td class="text_10" style="color:#444444"><i><?php if(trim($patientDetails['occupation'])) echo 'Occupation'; else echo '&nbsp;'; ?></i></td>
		<td class="text_10" width="180" style="color:#444444"><i><?php if(trim($emp_details['name'])) echo 'Employer Name'; else echo '&nbsp;'; ?></i></td>
	</tr>
	<tr><td height="7"></td></tr>
	<?php
	}
	//--- Check That All Fields Are Not Blank -------
	if(trim($emp_details['street']) != '' || trim($emp_details['street2']) != '' || trim($emp_adderss) != ''){
	?>
	<tr align="left">
		<td class="text_10b"><b><?php echo trim(ucwords($emp_details['street'])); ?></b></td>
		<td class="text_10b"><b><?php echo trim(ucwords($emp_details['street2'])); ?></b></td>
		<td class="text_10b"><b><?php echo trim(ucwords($emp_adderss)); ?></b></td>
	</tr>
	<tr align="left">
		<td class="text_10" style="color:#444444"><i><?php if(trim($emp_details['street'])) echo 'Address1'; else echo '&nbsp;'; ?></i></td>
		<td class="text_10" width="180" style="color:#444444"><i><?php if(trim($emp_details['street2'])) echo 'Address2'; else echo '&nbsp;'; ?></i></td>
		<td class="text_10" style="color:#444444"><i><?php if(trim($emp_adderss)) echo 'City,State Zip'; else echo '&nbsp;'; ?></i></td>
	</tr>
	<?php
	}
	?>
</table>
<?php
}
if($patientDetails['ethnoracial'] != '' || $patientDetails['interpretter'] != '' || $patientDetails['migrantseasonal'] != '' || $patientDetails['family_size'] != '' || $patientDetails['monthly_income'] != '' || $patientDetails['homeless'] != '' || $patientDetails['financial_review'] != ''){ 
?>
<table style="width:700px; margin-top:5px;" cellpadding="0" cellspacing="0" border="0" rules="none">
	<tr valign="middle" height="25px" class="text_10" bgcolor="#c0c0c0">
		<td align="left" colspan="4" bgcolor="#c0c0c0" style="width:100%;"><b><?php echo strtoupper('Miscellaneous'); ?></b></td>
	</tr>
	<tr align="left">
		<td class="text_10b"><b><?php echo trim(ucwords($patientDetails['hipaa_mail'])); ?></b></td>
		<td class="text_10b"><b><?php echo trim(ucwords($patientDetails['hipaa_voice'])); ?></b></td>
		<td class="text_10b"><b><?php echo trim(ucwords($patientDetails['genericval1'])); ?></b></td>
	</tr>
	<tr align="left">
		<td class="text_10" width="220" style="color:#444444"><i><?php if($patientDetails['hipaa_mail']) echo 'Allow Mail'; else echo '&nbsp;'; ?></i></td>
		<td class="text_10" width="180" style="color:#444444"><i><?php if($patientDetails['hipaa_voice']) echo 'Allow Voice Msg'; else echo '&nbsp;'; ?></i></td>
		<td class="text_10" style="color:#444444"><i><?php if($patientDetails['genericval1']) echo 'User defined 1'; else echo '&nbsp;'; ?></i></td>
	</tr>
	<tr><td height="7"></td></tr>
	<?php
	if($patientDetails['genericval2'] != '' || $patientDetails['language'] != '' || $patientDetails['ethnoracial'] != '' || $patientDetails['interpretter'] != ''){
	?>
	<tr align="left">
		<td class="text_10b"><b><?php echo trim(ucwords($patientDetails['genericval2'])); ?></b></td>
		<td class="text_10b"><b><?php echo trim(ucwords($patientDetails['language'])); ?></b></td>
		<td class="text_10b"><b><?php echo trim(ucwords($patientDetails['ethnoracial'])); ?></b></td>
		<td class="text_10b"><b><?php echo trim(ucwords($patientDetails['interpretter'])); ?></b></td>

	</tr>
	<tr align="left">
		<td class="text_10" width="220" style="color:#444444"><i><?php if($patientDetails['genericval2']) echo 'User defined 2'; else echo '&nbsp;'; ?></i></td>
		<td class="text_10" width="180" style="color:#444444"><i><?php if($patientDetails['language']) echo 'Language'; else echo '&nbsp;'; ?></i></td>
		<td class="text_10" style="color:#444444"><i><?php if($patientDetails['ethnoracial']) echo 'Race / Ethnicity'; else echo '&nbsp;'; ?></i></td>
		<td class="text_10" style="color:#444444"><i><?php if($patientDetails['interpretter']) echo 'Interpretter'; else echo '&nbsp;'; ?></i></td>
	</tr>
	<tr><td height="7"></td></tr>
	<?php
	}
	?>
</table>
<?php
}
//---  Insurance Details --------
$insQry = "select *,date_format(start_date,'%m-%d-%Y') as start_date,
		  date_format(end_date,'%m-%d-%Y') as end_date
		  from insurance_case 
	      where patient_id = '$pid' and case_status = 'Open'
	      order by ins_case_type
	      LIMIT 0,1 ";
$rsInsQry = imw_query($insQry);	
$numRowInsQry = imw_num_rows($rsInsQry);				
if($numRowInsQry > 0){
	$caseDetail = imw_fetch_array($rsInsQry);

	$ins_caseid = $caseDetail['ins_caseid'];
	$openDate = $caseDetail['start_date'];
	$end_date = $caseDetail['end_date'];	
	//--- Get Insurance Case Type -------
	$ins_case_type = $caseDetail['ins_case_type'];
	$insCaseQry = "select case_name from insurance_case_types 
					where case_id = $ins_case_type";
	$rsInsCaseQry = imw_query($insCaseQry);	
	$numRowInsCaseQry = imw_num_rows($rsInsCaseQry);				
	if($numRowInsCaseQry > 0){
		$caseDetail = imw_fetch_array($rsInsCaseQry);
		//echo_r($caseDetail);
	}	
	//-- Get Responsible Party Name -----
	$resp_name = $res_party_detail['lname'].', ';
	$resp_name .= $res_party_detail['fname'].' ';
	$resp_name .= $res_party_detail['mname'];	
	if(trim($resp_name) == ',')
		$resp_name = 'Self';
	$insType = array('primary','secondary');
	$ins = 0;
	foreach($insType as $val){		
		$insDataQry = "select *,
						date_format(effective_date,'%m-%d-%Y') as effective_date,
						date_format(expiration_date,'%m-%d-%Y') as expiration_date,
						date_format(subscriber_DOB,'%m-%d-%Y') as subscriber_DOB						
						from insurance_data where pid='$pid' and type='$val' 
						and actInsComp='1' and ins_caseid='$ins_caseid' and provider > 0
						and effective_date <= now() 
						and (expiration_date = '0000-00-00 00:00:00' 
						or expiration_date > now())";
		
		$rsInsDataQry = imw_query($insDataQry);	
		$numRowInsDataQry = imw_num_rows($rsInsDataQry);				
		if($numRowInsDataQry > 0){
			$insPriDetails = imw_fetch_array($rsInsDataQry);
			if(count($insPriDetails)>0){
				$priInsHeading = "$val insurance carrier details";
				$scan_card = '';
				if($insPriDetails['scan_card'] != '' || $insPriDetails['scan_card2'] != ''){
					$scan_card = '<span class="text_10b" >Ins. Card Scanned</span>';
					if($insPriDetails['scan_card']){
						$scan_card_arr[$val]['scan_card'] = $insPriDetails['scan_card'];
					}
					if($insPriDetails['scan_card2']){
						$scan_card_arr[$val]['scan_card2'] = $insPriDetails['scan_card2'];
					}				
				}
						
			?>
			<table style="width:700px; margin-top:5px;" cellpadding="0" cellspacing="0" border="0" rules="none">
				<?php
				if($ins == 0){
				?>
				<tr valign="middle" height="25px" bgcolor="#c0c0c0">
					<td class="text_10" colspan="4" bgcolor="#c0c0c0" style="width:100%;"><b>Insurance</b></td>
				</tr>
				<tr><td height="7"></td></tr>
				<tr> 	
					<td class="text_10b" align="left"><b><?php echo $caseDetail['ins_caseid']; ?></b></td>
					<td class="text_10b"><b><?php echo $caseDetail['case_name']; ?></b></td>
					<td class="text_10b"><b><?php echo $resp_name; ?></b></td>		
					<td class="text_10b"><b><?php echo $openDate.' / '.$end_Date; ?></b></td>
				</tr>
				<tr>
					<td class="text_10" style="color:#444444"><i>Ins. Case#</i></td>
					<td class="text_10" style="color:#444444"><i>Ins. Case Type</i></td>
					<td class="text_10" style="color:#444444"><i>Responsible Party</i></td>
					<td class="text_10" style="color:#444444"><i>Case Open/End Date</i></td>
				</tr>
				<tr height="10"><td></td></tr>
				<?php
				}
				$ins++;
				?>
				<tr valign="middle" height="25px" bgcolor="#c0c0c0" style="width:100%;">
					<td class="text_10" colspan="4" bgcolor="#c0c0c0" style="width:100%;"><b><?php echo strtoupper($priInsHeading.' '.$scan_card); ?></b></td>
				</tr>
				<tr><td height="7"></td></tr>
				<?php
					$provider = $insPriDetails['provider'];
					$insCompQry = "select name from insurance_companies 
									where id = $provider";
					$rsInsCompQry = imw_query($insCompQry);	
					$numRowrInsCompQry = imw_num_rows($rsInsCompQry);				
					if($numRowrInsCompQry > 0){
						$insDetails = imw_fetch_array($rsInsCompQry);
					}
				?>
				<tr align="left"> 	
					<td class="text_10b" width="220"><b><?php echo $insDetails['name']; ?></b></td>
					<td class="text_10b" width="180"><b><?php echo $insPriDetails['policy_number']; ?></b></td>
					<td class="text_10b"><b><?php echo $insPriDetails['group_number']; ?></b></td>		
					<td class="text_10b"><b><?php echo $insPriDetails['plan_name']; ?></b></td>
				</tr>
				<tr>
					<td class="text_10" style="color:#444444"><i><?php if($insDetails['name']) echo 'Ins. Carrier'; else echo '&nbsp;'; ?></i></td>
					<td class="text_10" style="color:#444444"><i><?php if($insPriDetails['policy_number']) echo 'Policy#'; else echo '&nbsp;'; ?></i></td>
					<td class="text_10" style="color:#444444"><i><?php if($insPriDetails['group_number']) echo 'Group#'; else echo '&nbsp;'; ?></i></td>
					<td class="text_10" style="color:#444444"><i><?php if($insPriDetails['plan_name']) echo 'Plan Name'; else echo '&nbsp;'; ?></i></td>
				</tr>
				<tr><td height="7"></td></tr>
				<?php
					$activeDate = $insPriDetails['effective_date'];			
					$expireDate = $insPriDetails['expiration_date'];					
					$copay = '$'.number_format($insPriDetails['copay'],2);
				?>
				<tr align="left"> 	
					<td class="text_10b" width="220"><b><?php echo $activeDate; ?></b></td>
					<td class="text_10b" width="180"><b><?php echo $expireDate; ?></b></td>
					<td class="text_10b"><b><?php echo $copay; ?></b></td>		
					<td class="text_10b"><b><?php echo $insPriDetails['referal_required']; ?></b></td>
				</tr>
				<tr>
					<td class="text_10" style="color:#444444"><i><?php if($actDate) echo 'Activaton Date'; else echo '&nbsp;'; ?></i></td>
					<td class="text_10" style="color:#444444"><i><?php if($expireDate) echo 'Expiration Date'; else echo '&nbsp;'; ?></i></td>
					<td class="text_10" style="color:#444444"><i>Copay</i></td>
					<td class="text_10" style="color:#444444"><i><?php if($insPriDetails['referal_required']) echo 'Referal Required'; else echo '&nbsp;'; ?></i></td>
				</tr>
				<tr><td height="7"></td></tr>
				<?php
					$subscriberName = $insPriDetails['subscriber_lname'].', ';
					$subscriberName .= $insPriDetails['subscriber_fname'].' ';
					$subscriberName .= $insPriDetails['subscriber_mname'];
					$subscriber_DOB = $insPriDetails['subscriber_DOB'];				
				?>
				<tr align="left"> 	
					<?php
					$strToShowRelation = $insPriDetails['subscriber_relationship'];
					if(strtolower($insPriDetails['subscriber_relationship']) == "doughter"){
						$strToShowRelation = "Daughter";
					}
					?>
					<td class="text_10b" width="220"><b><?php echo ucwords($strToShowRelation); ?></b></td>
					<td class="text_10b" width="180"><b><?php echo trim($subscriberName); ?></b></td>
					<td class="text_10b"><b><?php echo $subscriber_DOB; ?></b></td>		
					<td class="text_10b"><b><?php echo $insPriDetails['subscriber_ss']; ?></b></td>
				</tr>
				<tr>
					<td class="text_10" style="color:#444444"><i><?php if($insPriDetails['subscriber_relationship']) echo 'Subscriber Relation'; else echo '&nbsp;'; ?></i></td>
					<td class="text_10" style="color:#444444"><i><?php if(trim($subscriberName)) echo 'Subscriber Name'; else echo '&nbsp;'; ?></i></td>
					<td class="text_10" style="color:#444444"><i><?php if($subscriber_DOB) echo 'DOB'; else echo '&nbsp;'; ?></i></td>
					<td class="text_10" style="color:#444444"><i><?php if($insPriDetails['subscriber_ss']) echo 'Social Security#'; else echo '&nbsp;'; ?></i></td>
				</tr>
				<tr><td height="7"></td></tr>
				<?php
					$subscriberAddress = $insPriDetails['subscriber_city'].', ';
					$subscriberAddress .= $insPriDetails['subscriber_state'].' ';
					$subscriberAddress .= $insPriDetails['subscriber_postal_code'];
				?>
				<tr align="left"> 	
					<td class="text_10b" width="220"><b><?php echo $insPriDetails['subscriber_street']; ?></b></td>
					<td class="text_10b" width="180"><b><?php echo trim($subscriberAddress); ?></b></td>
					<td class="text_10b"><b>Yes</b></td>		
					<td class="text_10b"><b>Yes</b></td>
				</tr>
				<tr>
					<td class="text_10" style="color:#444444"><i><?php if($insPriDetails['subscriber_street']) echo 'Address'; else echo '&nbsp;'; ?></i></td>
					<td class="text_10" style="color:#444444"><i><?php if(trim($subscriberAddress)) echo 'City, State ZIP'; else echo '&nbsp;'; ?></i></td>
					<td class="text_10" style="color:#444444"><i>Payment Authorized</i></td>
					<td class="text_10" style="color:#444444"><i>Signature on File</i></td>
				</tr>	
				
			</table>
			<?php
			}		
		}
	}
}

//START CODE TO SHOW APPOINTMENT HISTORY
$apptHistory =  getApptHx($pid,$imwDOS);
echo $apptHistory;
//END CODE TO SHOW APPOINTMENT HISTORY

//---  Show Scan Card Documents --------
$pageHeight = 900;
if(count($scan_card_arr)>0){/*
	echo '<newpage>';
	foreach($scan_card_arr as $key => $ins_scan_arr){
		foreach($ins_scan_arr as $scan_key => $scan_val){
			$img_path = '../../main/uploaddir'.$scan_val;
			$img_real_path = realpath('../../main/uploaddir'.$scan_val);
			$ImageSize = getimagesize($img_real_path);
			$imageSizeArr[] = $ImageSize[1];
			$pageCount ++;
		}
	}*/
}
$i=1;
if(count($scan_card_arr)>0){
	foreach($scan_card_arr as $key => $ins_scan_arr){
		?>			
		<table style="width:700px; margin-top:5px;" border="<?php echo $border; ?>" rules="none" cellpadding="1" cellspacing="1">
		<?php
		if(count($ins_scan_arr) > 0){
		?>
			<tr valign="middle" height="25px" class="text_10" bgcolor="#c0c0c0">
				<td class="text_10" colspan="2"><b><?php echo strtoupper($key.' scan document'); ?></b></td>
			</tr>
		</table>
		<?php			
			foreach($ins_scan_arr as $scan_key => $scan_val){	
				/*
				$chk_img=explode("/",$scan_val);
				$chk_img_ext=explode(".",end($chk_img));
				if(end($chk_img_ext)=="pdf"){continue;}			
				
				$img_path = '../../main/uploaddir'.$scan_val;
				$img_real_path = realpath('../../main/uploaddir'.$scan_val);
				if(file_exists($img_real_path) != '' && is_dir($img_real_path) == ''){
					$img_name = substr($scan_val,strrpos($scan_val,'/')+1);
					$patient_img[$key.'-'.$scan_key] = $img_name;
					copy($img_real_path,'../../common/html2pdf/'.$img_name);
					$img_path = $img_name;
					$priImageSize = getimagesize($img_real_path);
					
					$newSize = '';
					if($priImageSize[1]>840){
						$newHeight = 840;
						$newSize = 500;
						$image_view = '<img src="'.$img_path.'" height="'.$newHeight.'" width="'.$newSize.'">';
						$priImageSize[1] = 840;
					}
					else{
						$image_view = '<img src="'.$img_path.'">';
					}					
					?>						
					<table style="width:700px; margin-top:5px;" cellpadding="0" cellspacing="0" border="<?php echo $border; ?>">
						<tr>
							<td class="text_10"><b><?php echo $img_name; ?></b></td>
						</tr>
						<tr>
							<td class="text_10b"><i><?php echo $scan_key; ?></i></td>
						</tr>
						<tr>
							<td colspan="2" align="center"><?php echo $image_view; ?></td>
						</tr>
					</table>
					<?php
					$pageHeight = $pageHeight - $priImageSize[1];	
					if($pageHeight < $imageSizeArr[$i-1] && $i < $pageCount ){
						echo '<newpage>';
						$pageHeight = 900;
					}					
					$i++;				
				}
			*/
			}
		}		
	}
}
//die($patientPrintData);
$patientPrintData = ob_get_contents();
ob_end_clean();

/*
$fpH2 = fopen($_SERVER['DOCUMENT_ROOT'].'/'.$surgeryCenterDirectoryName.'/'.$dateFolderPath.'/facesheet_'.$facesheetPdfPatientName.'_'.$facesheetPdfAscId.'.html','w');
fwrite($fpH2, $patientPrintData." \n");
//fclose();
*/
if(trim($patientPrintData)) {
	$content = $patientPrintData; 
	$op = 'p';
	$savePdfFileName = $_SERVER['DOCUMENT_ROOT'].'/'.$surgeryCenterDirectoryName.'/'.$dateFolderPath.'/facesheet_'.$facesheetPdfPatientName.'_'.$facesheetPdfAscId.'.pdf';
	$html2pdf = new HTML2PDF($op,'A4','en');
	$html2pdf->setTestTdInOnePage(false);
	$html2pdf->WriteHTML($content, isset($_GET['vuehtml']));
	$html2pdf->Output($savePdfFileName,'F');
	
}
@imw_close($link_imwemr);
include("common/conDb.php");
?>
