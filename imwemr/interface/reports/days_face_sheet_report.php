<?php
$globalDateQry = get_sql_date_format();
function underLine($to){
	$NBSP = "<u>";
	for($counter = 1; $counter<=$to; $counter++){
		$NBSP .= "&nbsp;";	
	}
	$NBSP .= "</u>";
	return $NBSP;
}

$fac_name = implode(",",$_REQUEST["fac_name"]);
// Fetch all Facilities of Schedulers
if($fac_name ==''){
	$str_fac_qry = "SELECT id FROM facility ORDER BY name ASC";
	$rs_fac = imw_query($str_fac_qry);
	if(imw_num_rows($rs_fac) > 0){
		while($row_fac = imw_fetch_array($rs_fac)){
			$fac_name.=$row_fac['id'].",";
		}
		$fac_name = substr($fac_name, 0, strlen($fac_name)-1);
	}
}
	
$strProvIds = implode(",",$_REQUEST["providerID"]);
// Fetch all Providers of Schedulers
if($strProvIds ==''){
	$arrProvIds = $CLSCommonFunction->provider_Ids('','1','');
	$strProvIds = implode(",",$arrProvIds);
}


//changing date format
if(isset($_REQUEST['start_date']) && $_REQUEST['start_date'] != ""){
	$dtEffectiveDate1 = $_REQUEST['start_date'];
	$dtDBEffectDate1 = getDateFormatDB($_REQUEST['start_date']);

	$dtEffectiveDate2 = $_REQUEST['end_date'];
	$dtDBEffectDate2 = getDateFormatDB($_REQUEST['end_date']);
}


$paddingTDTop = "padding-top:-3px;";
$str_appt_qry = "SELECT sa.id, sa_app_starttime as orgnalAppStartTime, sp.proc, sp.proc, u.fname, u.lname, fc.name, sa.sa_patient_id, sa.sa_facility_id, 
					DATE_FORMAT(sa.sa_app_start_date,'%a ".get_sql_date_format('','y','/')."') as sa_app_start_date, sa.sa_app_start_date as appStartDate, TIME_FORMAT(sa.sa_app_starttime,'%h:%i %p') as sa_app_starttime,
					sa.case_type_id as saCaseTypeId,sa.procedure_site as saProcSite,ict.case_name as ictCaseName
					FROM schedule_appointments sa 
					LEFT JOIN facility fc ON fc.id = sa.sa_facility_id 
					LEFT JOIN users u ON u.id = sa.sa_doctor_id 
					LEFT JOIN slot_procedures sp ON sp.id = sa.procedureid					
					LEFT JOIN insurance_case ic ON ic.ins_caseid = sa.case_type_id	
					LEFT JOIN insurance_case_types ict ON ict.case_id =  ic.ins_case_type										
					WHERE sa.sa_app_start_date BETWEEN '".$dtDBEffectDate1."' AND '".$dtDBEffectDate2."'  
					AND sa.sa_doctor_id IN(".$strProvIds.")
					AND sa.sa_facility_id IN(".$fac_name.")
					AND sa.sa_patient_app_status_id  NOT IN (203,201,18,19,20)
					ORDER BY u.lname, orgnalAppStartTime
					";					
$rs_appt = imw_query($str_appt_qry);
$patient_print_data = "";
$queryStaringString = "";

$int_tot_appt = imw_num_rows($rs_appt);
if($int_tot_appt > 0){
	$int_appt = 1;
	while($row_appt = imw_fetch_array($rs_appt)){
		$primaryInsuranceCarrier = $secondaryInsuranceCarrier = $strIctCaseName = $strSaProcSite = "";
		ob_start();
		$appt_date = $row_appt['appStartDate'];
		unset($scan_card_arr);
		$strSaProcSite = $row_appt["saProcSite"];	
		//---get Ins Case Deatail For Patient Start-------
		$intSaCaseTypeId = 0;
		$intSaCaseTypeId = (int)$row_appt["saCaseTypeId"];
		if($intSaCaseTypeId > 0){
			$strIctCaseName = $row_appt["ictCaseName"];			
			$qryGetInsCaseDeatil = "select insComp.name as insName,insData.type insType from insurance_data insData
									LEFT JOIN insurance_companies insComp ON insComp.id = insData.provider								
									where insData.ins_caseid = '".$intSaCaseTypeId."'";
			$rsGetInsCaseDeatil = imw_query($qryGetInsCaseDeatil);						
			if($rsGetInsCaseDeatil){
				if(imw_num_rows($rsGetInsCaseDeatil) > 0){
					while($rowGetInsCaseDeatil = imw_fetch_array($rsGetInsCaseDeatil)){
						if($rowGetInsCaseDeatil["insType"] == "primary"){
							$primaryInsuranceCarrier = $rowGetInsCaseDeatil["insName"];
						}
						elseif($rowGetInsCaseDeatil["insType"] == "secondary"){
							$secondaryInsuranceCarrier = $rowGetInsCaseDeatil["insName"];
						}	
					}
				}
				imw_free_result($rsGetInsCaseDeatil);
			}			
		}
		//---get Ins Case Deatail For Patient End-------
		
		$pid = $row_appt["sa_patient_id"];
		
		//---get pat last 2 appointments Start-----
		$pastAppData = "";
		$arrPastAppData = array();
		$qryGetLastTwoApp = "SELECT DATE_FORMAT(sa_app_start_date,'%a ".get_sql_date_format('','y','/')."') as pastDate, 
							TIME_FORMAT(sa_app_starttime,'%H:%i %p') as pastTime, sp.acronym as acronym, 
							users.lname, users.fname
							FROM schedule_appointments sa 
							JOIN users ON users.id = sa.sa_doctor_id 
							JOIN slot_procedures sp ON sa.procedureid = sp.id 
							where sa_patient_id = '".$pid."' and sa_app_start_date < '".$appt_date."'
							ORDER BY `sa_app_start_date` DESC limit 2";

		$rsGetLastTwoApp = imw_query($qryGetLastTwoApp);						
		if($rsGetLastTwoApp){
			if(imw_num_rows($rsGetLastTwoApp) > 0){
				$strPastSite = NULL;
				while($rowGetLastTwoApp = imw_fetch_array($rsGetLastTwoApp)){
					$strPastSite = ucfirst($rowGetLastTwoApp['fname'][0]);
					$strPastSite .= ucfirst($rowGetLastTwoApp['lname'][0]);
					$strPastSite .= " - ".$rowGetLastTwoApp['pastDate']." ".$rowGetLastTwoApp['pastTime']." ".$rowGetLastTwoApp['acronym'];
					$arrPastAppData[] = $strPastSite;
				}								
				imw_free_result($rsGetLastTwoApp);
			}
		}
		
		//---get pat last 2 appointments End-------
		//$appt_id = $row_appt["id"];
		
		$tdate=date("m-d-Y");
		$appt_dt = $row_appt["sa_app_start_date"];
		$appt_tm = $row_appt["sa_app_starttime"];
		$appt_fc = $row_appt["name"];
		$appt_pr = $row_appt["proc"];
		$appt_pv = $row_appt["lname"];
		if($row_appt["fname"]!=''){
			$appt_pvf= ',&nbsp;'.$row_appt["fname"];
		}
		//---get Detail For Patient -------
		$qry = imw_query("select *from patient_data where id = $pid");
		$patientDetails = array();
		while($rowpt = imw_fetch_assoc($qry)){
			$patientDetails[] = $rowpt;
		}
		$patientName = $patientDetails[0]['lname'].', '.$patientDetails[0]['fname'].' ';
		$patientName .= $patientDetails[0]['mname'];
		$date = substr($patientDetails[0]['date'],0,strpos($patientDetails[0]['date'],' '));
		$created_date = get_date_format($date);
		$date_of_birth = get_date_format($patientDetails[0]['DOB']);
		$ptemail= trim($patientDetails[0]['email']);
		$cityAddress = $patientDetails[0]['city'];
		if($patientDetails[0]['state'])
			$cityAddress .= ', '.$patientDetails[0]['state'].' ';
		else
			$cityAddress .= ' ';
			$cityAddress .= $patientDetails[0]['postal_code'];
		list($y,$m,$d) = explode('-',$patientDetails[0]['DOB']);
		$age =show_age($patientDetails[0]['DOB']) ;//date('Y') - $y ;

		//--- Get Physician Details --------
		//$phyId = $patientDetails[0]['providerID'];
		if((int)$patientDetails[0]['providerID'] > 0){
			$phyId = $patientDetails[0]['providerID'];
		}
		else{
			$appointmentQryRes = $CLSReports->__getPatientLastAppointment($pid);
			$phyId = $appointmentQryRes[0]['sa_doctor_id'];
		}

		if($phyId){
			$qry = imw_query("select concat(fname,', ',lname) as name, mname from users where id = '$phyId'");
			$phyDetails = array();
			while($rowptA = imw_fetch_assoc($qry)){
				$phyDetails[] = $rowptA;
			}
			$phyName = trim($phyDetails[0]['name']);
			$phyName .= $phyDetails[0]['mname'] != '' ? ' '.substr($phyDetails[0]['mname'],0,1).'.' : '';
		}

		//--- Get Reffering Physician Details --------
		$reffPhyId = $patientDetails[0]['primary_care_id'];
		$qry = imw_query("select concat(FirstName,', ',LastName) as name, MiddleName from refferphysician
				where physician_Reffer_id = '$reffPhyId'");
		$refPhyDetails = array();	
		while($rowRF = imw_fetch_assoc($qry)){	
			$refPhyDetails[] = $rowRF;
		}
		$reffPhyName = trim($refPhyDetails[0]['name']);
		$reffPhyName .= $refPhyDetails[0]['MiddleName'] != '' ? ' '.substr($refPhyDetails[0]['MiddleName'],0,1).'.' : '';
		
		//---- Get Patient Facility Details -------
		$default_facility = $patientDetails[0]['default_facility'];
		$qry = imw_query("select facilityPracCode from pos_facilityies_tbl 
				where pos_facility_id = '$default_facility'");
		$facilityRes = array();	
		while($rowFC = imw_fetch_assoc($qry)){	
			$facilityRes[] = $rowFC;
		}
		//--- Get Detail How create patient -------
		$created_by = $patientDetails[0]['created_by'];
		$qry = imw_query("select fname, lname, mname from users where id = '$created_by'");
		$createByDetail = array();	
		while($rowCD = imw_fetch_assoc($qry)){	
			$createByDetail[] = $rowCD;
		}
		$createByName = core_name_format($createByDetail[0]['fname'],$createByDetail[0]['mname'],$createByDetail[0]['lname'],'');


		//--- Get Patient Responsible Party Details -----
		$qry = imw_query("select * from resp_party where patient_id = '$pid' and fname != '' and lname != ''");
		$res_party_detail = array();	
		while($rowrp = imw_fetch_assoc($qry)){	
			$res_party_detail[] = $rowrp;
		}		
		//--- Get Patient Occupation Details ------
		
		$qry = imw_query("select * from employer_data where pid = '$pid' and name != ''");
		$emp_details = array();	
		while($rowed = imw_fetch_assoc($qry)){	
			$emp_details[] = $rowed;
		}	
		//--- Get Default Facility Details -------
		$qry = imw_query("select default_group from facility where facility_type = 1");
		
		$facilityDetail = array();	
		while($rowfd = imw_fetch_assoc($qry)){	
			$facilityDetail[] = $rowfd;
		}
		if(count($facilityDetail)>0){
			$gro_id = $facilityDetail[0]['default_group'];
			$qry = imw_query("select * from groups_new where gro_id = '$gro_id'");
			$groupDetails = array();	
			while($rowgd = imw_fetch_assoc($qry)){	
				$groupDetails[] = $rowgd;
			}
		}

		/////get Date Of Service For Chart Notes////
		#form_id
		if(isset($_SESSION["form_id"]) && !empty($_SESSION["form_id"]))	{
			$form_id = $_SESSION["form_id"];	
			$finalize_flag = 0;		
		}else if(isset($_SESSION["finalize_id"]) && !empty($_SESSION["finalize_id"])){	
			#form id
			$form_id = $_SESSION["finalize_id"];		
			$finalize_flag = 1;						
		}
		#####
		// IF PRINT THEN FORM ID
		$print_form_id = $_REQUEST['print_form_id'];
		if($print_form_id){
			$form_id = $print_form_id;
		}
		// IF PRINT THEN FORM ID

		$qry1=imw_query("select * from  chart_master_table where patient_id='$pid' and id='$form_id'");
		$co=imw_num_rows($qry1);
		if(($co > 0)){
			$crow=imw_fetch_array($qry1);
			$date_of_service = get_date_format($crow["date_of_service"]);	
		}
		$dontshowPDF=true;
		$face_check = true;
		
		unset($patient_info);
		$patient_info[0] = 'face_sheet';
		$border = 0;

		//--- Get Patient Image ----
		$patientImage = "";
		$p_imagename = $patientDetails[0]['p_imagename'];
		if($p_imagename<>""){
			$dirPath = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH').$p_imagename;
			$dir_real_path = realpath($dirPath);
			$img_name = substr($p_imagename,strrpos($p_imagename,'/')+1);	
			$path_root = pathinfo($dir_real_path);
				
			@copy($dir_real_path,$path_root['dirname'].'/tmp/'.$img_name);
			if($face_check == true){
				$dirPath = $img_name;
			}
			if(file_exists($dir_real_path)){
				$patient_img['patient'] = $img_name;
				$fileSize = getimagesize($dir_real_path);
				if($fileSize[0]>80 || $fileSize[0]>90){
					$imageWidth2 = imageResize($fileSize[0],$fileSize[1],70);
					$patientImage = '<img style="cursor:pointer" src="'.$path_root['dirname'].'/tmp/'.$dirPath.'" alt="patient Image" '.$imageWidth2.'>';
				}
				else{
					$patientImage = '<img style="cursor:pointer" src="'.$path_root['dirname'].'/tmp/'.$dirPath.'" alt="patient Image">';
				}		
			}
		}
		if($int_appt == 1){
		}
		$strAppInfo = $strAppInfodateTime = "";
		$strSaProcSite = ($strSaProcSite) ? "-".$strSaProcSite : ""; 
		$strAppInfodateTime = trim($appt_dt)." ".trim($appt_tm); 
		if($strAppInfodateTime){
			$strAppInfodateTimeProc = trim($appt_pr).trim($strSaProcSite);
		}				
		$patientName = trim($patientDetails[0]['title'])." ".trim($patientName);
		
		//-----BLANK.GIF IMAGE PATH SETTINGS------
		$imgPath = "../../library/images/blank.gif";
		?>
        <page>		
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr> 
				<td style="width:25%;"><b>FACE SHEET</b></td>
				<td style="width:35%;"><b><?php print trim($patientName); ?></b></td>                
                <td style="width:15%;" align="center"><b><?php print "ID: ".trim($patientDetails[0]['id']); ?></b></td>                
				<td style="width:25%;"><b><?php print wordwrap(trim($groupDetails[0]['name']), 22, "<br />\n"); ?></b></td>
			</tr>            
			<tr>
				<td style="width:25%;"><?php print $strAppInfodateTime; ?></td>
				<?php
				if(isset($_REQUEST["appt_info"]) && $_REQUEST["appt_info"] == 1){
					?>
				<td style="width:35%;">
                	<b>Dr. <?php echo $appt_pv.$appt_pvf;?> - <?php echo $appt_fc;?></b>                  
                </td>
				<?php
				}else{
					?>
                <td style="width:35%;"></td>
                    <?php 
				}
				$insDataCase = "";													
				$insDataCase = ($strIctCaseName) ? "<b>Insurance Case</b>: ".wordwrap(ucwords(strtolower(trim($strIctCaseName))), 25, "<br />\n") : "";													
				$insDataPri = "";													
				$insDataPri = ($primaryInsuranceCarrier) ? "<b>Primary Ins</b>: ".wordwrap(ucwords(strtolower(trim($primaryInsuranceCarrier))), 25, "<br />\n") : "";
				$insDataSec = "";													
				$insDataSec = ($secondaryInsuranceCarrier) ? "<b>Secondary Ins</b>: ".wordwrap(ucwords(strtolower(trim($secondaryInsuranceCarrier))), 25, "<br />\n") : "";
				?>				
                <td style="width:15%;" rowspan="5" valign="top" align="center"><?php print $patientImage; ?></td>								                
				<td><b><?php print ucwords(wordwrap(trim($groupDetails[0]['group_Address1']), 22, "<br />\n"));?></b></td>						
			</tr>
            <tr>
            	<td><?php echo $strAppInfodateTimeProc; ?></td>
                <td><?php echo $insDataCase; ?></td>                
                <td colspan="2"><b><?php print $groupDetails[0]['group_City'].', '.$groupDetails[0]['group_State'].' '.$groupDetails[0]['group_Zip']; ?></b></td>	
            </tr>
            <tr>
            	<td><b><?php echo ($arrPastAppData[0]) ? "Past Appt. History" : "" ?></b></td>
                <td colspan="3"><?php echo $insDataPri; ?></td>                
            </tr>
            <tr>
            	<td><?php echo $arrPastAppData[0]; ?></td>                
                <td colspan="3"><?php echo $insDataSec; ?></td>
            </tr>
            <tr>
            	<td colspan="4"><?php echo $arrPastAppData[1]; ?></td>                
            </tr>
            <tr>
				<td colspan="4" style="height:1px;"><img src="<?php echo $imgPath; ?>"/></td>
			</tr>
		</table>
		<table cellpadding='0' cellspacing='0' border='0' rules="none" width='100%' >
        	
			<tr>
				<td bgcolor="#c0c0c0" align="left" colspan="4"><b>PATIENT DEMOGRAPHICS</b></td>
			</tr>
						
			<tr align="left" valign="top">
				<td class="text_10b" width="180" valign="bottom" height="5px" ><b><u><?php print ucwords($patientName); ?></u></b></td>
				<td class="text_10b" width="180" valign="bottom" height="5px" ><b><u><?php print $date_of_birth; ?> (<?php print $age;?>)</u></b></td>
				<td class="text_10b" width="180" valign="bottom" height="5px" ><b><u><?php print $patientDetails[0]['id']; ?></u></b></td>
				<td class="text_10b" width="180" valign="<?php print (ucwords($patientDetails[0]['status'])) ? "bottom" : "top"; ?>" height="<?php print (ucwords($patientDetails[0]['status'])) ? "5px" : "14px"; ?>" ><b><u><?php print (ucwords($patientDetails[0]['status'])) ? ucwords($patientDetails[0]['status']) : underLine(35); ?></u></b></td>
			</tr>
									
			<tr align="left" valign="top" style="">
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>" width="180"  ><i>Patient Name</i></td>
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>" width="180"  ><i>DOB</i></td>
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>" width="180"  ><i>ID</i></td>
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>" width="180"  ><i>Marital</i></td>
			</tr>			
		
			<tr align="left" valign="top">
				<td class="text_10b" width="180" height="<?php print ($patientDetails[0]['ss'] != "--" && trim($patientDetails[0]['ss']) != "") ? "5px" : "14px"; ?>" valign="<?php print ($patientDetails[0]['ss'] != "--"  && trim($patientDetails[0]['ss']) != "") ? "bottom" : "top"; ?>"><b><u><?php print ($patientDetails[0]['ss'] != "--"  && trim($patientDetails[0]['ss']) != "") ? ucwords(substr_replace($patientDetails[0]['ss'],'XXX-XX',0,6)) : underLine(35); ?></u></b></td>
				<?php
				if(isset($_REQUEST["appt_info"]) && $_REQUEST["appt_info"] == 1){
					?>
				<td></td>
					<?php
				}else{
					?>
				<td class="text_10b"><b><u><?php print $phyName; ?></u></b></td>
					<?php
				}
				?>
				<?php
				if(isset($_REQUEST["appt_info"]) && $_REQUEST["appt_info"] == 1){
					?>
				<td></td>
					<?php
				}else{
					?>
				<td class="text_10b" valign="top"><b><u><?php print ($facilityRes[0]['facilityPracCode']) ? $facilityRes[0]['facilityPracCode'] : underLine(35); ?></u></b></td>
					<?php
				}
				?>
				<td class="text_10b" width="180" height="<?php print ($created_date != "--" && $created_date != "00-00-0000") ? "5px" : "14px"; ?>" valign="top"><b><u><?php print ($created_date != "--"  && $created_date != "00-00-0000") ? $created_date : underLine(35); ?></u></b></td>
			</tr>
			
			<tr align="left" valign="top">
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>" width="180" ><i>Social Security#</i></td>
				<?php
				if(isset($_REQUEST["appt_info"]) && $_REQUEST["appt_info"] == 1){
					?>
				<td></td>
					<?php
				}else{
					?>
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>"><i>Physician</i></td>
					<?php
				}
				?>
				<?php
				if(isset($_REQUEST["appt_info"]) && $_REQUEST["appt_info"] == 1){
					?>
				<td></td>
					<?php
				}else{
					?>
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>"><i>Facility</i></td>
					<?php
				}
				?>
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>" width="180"><i>Registration date</i></td>
			</tr>
									
			<tr align="left" valign="top">
				<td class="text_10b" width="180" height="<?php print ($patientDetails[0]['sex']) ? "5px" : "14px"; ?>" valign="<?php print ($patientDetails[0]['sex']) ? "bottom" : "top"; ?>"><b><u><?php print ($patientDetails[0]['sex']) ? $patientDetails[0]['sex'] : underline(35); ?></u></b></td>
				<td class="text_10b" width="180" height="<?php print ($reffPhyName) ? "5px" : "14px"; ?>" valign="<?php print ($reffPhyName) ? "bottom" : "top"; ?>"><b><u><?php print ($reffPhyName) ? $reffPhyName : underLine(35); ?></u></b></td>
				<td class="text_10b" width="180" height="<?php print ($patientDetails[0]['driving_licence']) ? "5px" : "14px"; ?>" valign="<?php print ($patientDetails[0]['driving_licence']) ? "bottom" : "top"; ?>"><b><u><?php print ($patientDetails[0]['driving_licence']) ? $patientDetails[0]['driving_licence'] : underLine(35) ; ?></u></b></td>
				<?php
				if(isset($_REQUEST["appt_info"]) && $_REQUEST["appt_info"] == 1){
					?>
				<td></td>
					<?php
				}else{
					?>
				<td class="text_10b" colspan="2" width="180" height="<?php print ($createByName) ? "5px" : "14px"; ?>" valign="<?php print ($createByName) ? "bottom" : "top"; ?>" ><b><u><?php print ($createByName) ? ucwords($createByName) : underLine(35) ; ?></u></b></td>
					<?php
				}
				?>
				
			</tr>
				
			<tr align="left" valign="top">
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>" width="180"><i>Sex</i></td>
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>" width="180"><i>Referring Dr.</i></td>
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>" width="180"><i>DL #</i></td>
				<?php
				if(isset($_REQUEST["appt_info"]) && $_REQUEST["appt_info"] == 1){
					?>
				<td></td>
					<?php
				}else{
					?>
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>" colspan="2"><i>Created By</i></td>	
					<?php
				}
				?>
					
			</tr>
			
				<?php
			
			//--- Check That All Fields Are Not Blank -------
				$street = trim(ucwords($patientDetails[0]['street']));
				if(trim(ucwords($patientDetails[0]['street'])) != '' && trim(ucwords($patientDetails[0]['street2'])) != ''){
					$street .= '<br>';
				}
				if(trim(ucwords($patientDetails[0]['street2']))){
					$street .= trim(ucwords($patientDetails[0]['street2']));
				}
				
				?>
			<tr align="left" valign="top">
				<td class="text_10b" width="180" height="<?php print ($street) ? "5px" : "14px"; ?>" valign="<?php print ($street) ? "bottom" : "top"; ?>"><b><u><?php print ($street) ? htmlentities($street) : underLine(35); ?></u></b></td>
				<td class="text_10b" width="180" height="<?php print (trim(ucwords($cityAddress))) ? "5px" : "14px"; ?>" valign="<?php print (trim(ucwords($cityAddress))) ? "bottom" : "top"; ?>"><b><u><?php print (trim(ucwords($cityAddress))) ? trim(ucwords($cityAddress)) : underLine(35); ?></u></b></td>
				<td class="text_10b" width="180" height="<?php print ($patientDetails[0]['phone_home'] != "000-000-0000" && trim($patientDetails[0]['phone_home']) != "") ? "5px" : "14px"; ?>" valign="<?php print ($patientDetails[0]['phone_home'] != "000-000-0000"  && trim($patientDetails[0]['phone_home']) != "") ? "bottom," : "top"; ?>"><b><u><?php print ($patientDetails[0]['phone_home'] != "000-000-0000"  && trim($patientDetails[0]['phone_home']) != "") ? core_phone_format($patientDetails[0]['phone_home']) : underLine(35); ?></u></b></td>
				<td class="text_10b"  width="180" height="<?php print ($patientDetails[0]['phone_biz']  != "000-000-0000" && trim($patientDetails[0]['phone_biz']) != "") ? "5px" : "14px"; ?>" valign="<?php print ($patientDetails[0]['phone_biz']  != "000-000-0000" && trim($patientDetails[0]['phone_biz']) != "") ? "bottom" : "top"; ?>"><b><u><?php print ($patientDetails[0]['phone_biz']  != "000-000-0000" && trim($patientDetails[0]['phone_biz']) != "") ? core_phone_format($patientDetails[0]['phone_biz']) : underLine(35); ?></u></b></td>
			</tr>
			
			<tr align="left" valign="top">
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>" width="180"><i>Street</i></td>
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>" width="180"><i>City, State Zip</i></td>
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>" width="180"><i>Home Phone#</i></td>
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>" width="180"><i>Work Phone#</i></td>
			</tr>
            <tr align="left" valign="top">
				<td class="text_10b"  width="180" height="<?php print ($patientDetails[0]['phone_cell']  != "000-000-0000" && trim($patientDetails[0]['phone_cell']) != "") ? "5px" : "14px"; ?>" valign="<?php print ($patientDetails[0]['phone_cell']  != "000-000-0000" && trim($patientDetails[0]['phone_cell']) != "") ? "bottom" : "top"; ?>"><b><u><?php print ($patientDetails[0]['phone_cell']  != "000-000-0000" && trim($patientDetails[0]['phone_cell']) != "") ? core_phone_format($patientDetails[0]['phone_cell']) : underLine(35); ?></u></b></td>
				<td class="text_10b" width="180" height="<?php print ($ptemail) ? "5px" : "14px"; ?>" valign="<?php print ($ptemail) ? "bottom" : "top"; ?>"><b><u><?php if($ptemail){ print $ptemail; }else{ print underLine(35); } ?></u></b></td>
				<td class="text_10b" width="180"></td>
				<td class="text_10b"  width="180"></td>
			</tr>
        	<tr align="left" valign="top">
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>" width="180"><i>Mobile Phone#</i></td>
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>" width="180"><i>E-mail</i></td>
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>" width="180"></td>
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>" width="180"></td>
			</tr>		
		
		<?php
					//--- get Occupation Details if patient fill in demographics --------
		//for($f=0;$f<count($emp_details);$f++){
			if($emp_details[0]['city'])
				$emp_adderss .= $emp_details[0]['city'];
			if($emp_details[0]['state'])
				$emp_adderss .= ', '.$emp_details[0]['state'];
			if($emp_details[0]['postal_code'])
				$emp_adderss .= ' '.$emp_details[0]['postal_code'];
		?>
			<!--<tr valign="middle"  class="text_10" bgcolor="#c0c0c0">
				<td align="left" colspan="4"><b><?php //print strtoupper('Patient Occupation'); ?></b></td>
			</tr>	
			<tr align="left">
				<td class="text_10b" width="180" height="<?php //print (trim(ucwords($patientDetails[0]['occupation']))) ? "5px" : "14px"; ?>" valign="<?php //print (trim(ucwords($patientDetails[0]['occupation']))) ? "bottom" : "top"; ?>">&nbsp;<b><?php //print (trim(ucwords($patientDetails[0]['occupation']))) ? trim(ucwords($patientDetails[0]['occupation'])) : underLine(35); ?></b></td>
				<td class="text_10b" width="180" height="<?php //print (trim(ucwords($emp_details[0]['name']))) ? "5px" : "14px"; ?>" valign="<?php //print (trim(ucwords($emp_details[0]['name']))) ? "bottom" : "top"; ?>">&nbsp;<b><?php //print (trim(ucwords($emp_details[0]['name']))) ? trim(ucwords($emp_details[0]['name'])) : underLine(35); ?></b></td>
				<td class="text_10b" width="180" height="<?php //if(trim(ucwords($emp_details[0]['street'])) || trim(ucwords($emp_details[0]['street2']))){print "5px";}else {print "14px";} ?>" valign="<?php //if(trim(ucwords($emp_details[0]['street'])) || trim(ucwords($emp_details[0]['street2']))){print "bottom";}else {print "top";} ?>">&nbsp;<b><?php //if(trim(ucwords($emp_details[0]['street'])) || trim(ucwords($emp_details[0]['street2']))){print trim(ucwords($emp_details[0]['street']))." ".trim(ucwords($emp_details[0]['street2']));}else {print underLine(35);} ?></b></td>
				<td class="text_10b" width="180" height="<?php //print (trim(ucwords($emp_adderss))) ? "5px" : "14px"; ?>" valign="<?php //print (trim(ucwords($emp_adderss))) ? "bottom" : "top"; ?>">&nbsp;<b><?php //print (trim(ucwords($emp_adderss))) ? trim(ucwords($emp_adderss)) : underLine(35); ?></b></td>
			</tr>
			
			<tr align="left">
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>" width="180"><i>Occupation</i></td>
				<td class="text_10"  style="color:#444444; <?php echo $paddingTDTop; ?>" width="180"><i>Employer Name</i></td>
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>" width="180"><i>Street</i></td>
				<td class="text_10"  style="color:#444444; <?php echo $paddingTDTop; ?>" width="180"><i>City, State Zip</i></td>
			</tr>-->
		
<?php
//}?>			
            <tr>
				<td colspan="4" style="height:1px;"><img src="<?php echo $imgPath; ?>"/></td>
			</tr>
            <tr>
				<td bgcolor="#c0c0c0" align="left" colspan="4"><b>EMERGENCY CONTACT INFORMATION</b></td>
			</tr>	
			<tr align="left">
				<td class="text_10b" width="180" height="<?php print ($patientDetails[0]['contact_relationship']) ? "5px" : "14px"; ?>" valign="<?php print ($patientDetails[0]['contact_relationship']) ? "bottom" : "top"; ?>"><u><?php print ($patientDetails[0]['contact_relationship']) ? $patientDetails[0]['contact_relationship'] : underLine(35); ?></u></td>				
				<td class="text_10b" width="180" height="<?php print ($patientDetails[0]['phone_contact']) ? "5px" : "14px"; ?>" valign="<?php print ($patientDetails[0]['phone_contact']) ? "bottom" : "top"; ?>"><b><u><?php print ($patientDetails[0]['phone_contact']) ? core_phone_format($patientDetails[0]['phone_contact']) : underLine(35); ?></u></b></td>
				<!-- <td class="text_10b" colspan="2" align="left" nowrap height="<?php //print (trim(ucwords($patientDetails[0]['patient_notes']))) ? "5px" : "14px"; ?>" valign="<?php //print (trim(ucwords($patientDetails[0]['patient_notes']))) ? "bottom" : "top"; ?>"><b><?php //print (trim(ucwords($patientDetails[0]['patient_notes']))) ? trim(ucwords($patientDetails[0]['patient_notes'])) : underLine(60); ?></b></td>				 -->
			</tr>		
			<tr align="left" >
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>" width="180"><i>Emergency Contact</i></td>
				<td class="text_10" width="180" style="color:#444444; <?php echo $paddingTDTop; ?>"><i>Emergency Phone</i></td>
				<!-- <td class="text_10" colspan="2" style="color:#444444; <?php echo $paddingTDTop; ?>" width="180"><i>Notes</i></td> -->
			</tr>
		
			<?php
		
		//---- Get Responsible Party Details if patient has -------
		//for($r=0;$r<count($res_party_detail);$r++){
			$resp_name = $res_party_detail[0]['lname'].', ';
			$resp_name .= $res_party_detail[0]['fname'].' ';
			$resp_name .= $res_party_detail[0]['mname'];
			if(trim($resp_name) == ","){
				$resp_name = "";
			}	
			$dob = $res_party_detail[0]['dob'];
			$dateOfBirth = get_date_format($dob);
			list($resp_y,$resp_m,$resp_d) = explode('-',$dob);
			$resp_age = show_age($res_party_detail[0]['dob']); // date('Y') - $resp_y;
			$city_address = $res_party_detail[0]['city'].', ';
			$city_address .= $res_party_detail[0]['state'].' ';
			$city_address .= $res_party_detail[0]['zip'];
			if(trim($city_address) == ","){
				$city_address = "";
			}
			?>
            <tr>
				<td colspan="4" style="height:1px;"><img src="<?php echo $imgPath; ?>"/></td>
			</tr>
            <tr>
				<td bgcolor="#c0c0c0" align="left" colspan="4"><b>RESPONSIBILITY PARTY</b></td>
			</tr>
			
			<tr align="left">
				<td class="text_10b" width="180" height="<?php print (trim(ucwords($resp_name))) ? "5px" : "14px"; ?>" valign="<?php print (trim(ucwords($resp_name))) ? "bottom" : "top"; ?>"><b><u><?php print (trim(ucwords($resp_name))) ? trim(ucwords($resp_name)) : underLine(35); ?></u></b></td>
				<td class="text_10b" width="180" height="<?php print (trim(ucwords($res_party_detail[$r]['relation']))) ? "5px" : "14px"; ?>" valign="<?php print (trim(ucwords($res_party_detail[$r]['relation']))) ? "bottom" : "top"; ?>"><b><u><?php print (trim(ucwords($res_party_detail[$r]['relation']))) ? trim(ucwords($res_party_detail[$r]['relation'])) : underLine(35); ?></u></b></td>
				<td class="text_10b" width="180" height="<?php print ($dateOfBirth != "--") ? "5px" : "14px"; ?>" valign="top"><b><u><?php print ($dateOfBirth != "--") ? $dateOfBirth.' ( '.$resp_age.' ) ' : underline(35); ?></u></b></td>
				<td class="text_10b" width="180" height="<?php print (trim(ucwords($res_party_detail[$r]['sex']))) ? "5px" : "14px"; ?>" valign="top"><b><u><?php print (trim(ucwords($res_party_detail[$r]['sex']))) ? trim(ucwords($res_party_detail[$r]['sex'])) : underLine(35); ?></u></b></td>
			</tr>
		
			<tr align="left">
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>" width="180"><i>Name</i></td>
				<td class="text_10"style="color:#444444; <?php echo $paddingTDTop; ?>" width="180"><i>Relation</i></td>
				<td class="text_10" width="180" style="color:#444444; <?php echo $paddingTDTop; ?>"><i>DOB</i></td>
				<td class="text_10"style="color:#444444; <?php echo $paddingTDTop; ?>" width="180"><i>Sex</i></td>
			</tr>
						
            <tr align="left">
                <td class="text_10b" width="180" height="<?php print ($res_party_detail[0]['ss']) ? "5px" : "14px"; ?>" valign="top"><b><u><?php print ($res_party_detail[0]['ss']) ? ucwords(substr_replace($res_party_detail[0]['ss'],'XXX-XX',0,6)) : underLine(35); ?></u></b></td>
                <td class="text_10b" width="180" height="<?php print ($res_party_detail[0]['licence']) ? "5px" : "14px"; ?>" valign="top"><b><u><?php print ($res_party_detail[0]['licence']) ? $res_party_detail[0]['licence'] : underLine(35); ?></u></b></td>
                <td class="text_10b" width="180" height="<?php print (trim(ucwords($res_party_detail[0]['marital']))) ? "5px" : "14px"; ?>" valign="<?php print (trim(ucwords($res_party_detail[0]['marital']))) ? "bottom" : "top"; ?>"><b><u><?php print (trim(ucwords($res_party_detail[0]['marital']))) ? trim(ucwords($res_party_detail[0]['marital'])) : underLine(35); ?></u></b></td>
                <td class="text_10b" width="180" height="<?php print (trim(ucwords($res_party_detail[0]['work_ph']))) ? "5px" : "14px"; ?>" valign="<?php print (trim(ucwords($res_party_detail[0]['work_ph']))) ? "bottom" : "top"; ?>"><b><u><?php print (trim(ucwords($res_party_detail[0]['work_ph']))) ? trim(ucwords($res_party_detail[0]['work_ph'])) : underLine(35); ?></u></b></td>
            </tr>
		
            <tr align="left">
                <td class="text_10" width="180" style="color:#444444; <?php echo $paddingTDTop; ?>"><i>Social Security#</i></td>
                <td class="text_10" width="180" style="color:#444444; <?php echo $paddingTDTop; ?>" align="left"><i>DL #</i></td>
                <td class="text_10" width="180" style="color:#444444; <?php echo $paddingTDTop; ?>" align="left"><i>Marital</i></td>
                <td class="text_10" width="180" style="color:#444444; <?php echo $paddingTDTop; ?>" align="left"><i>Work Phone#</i></td>
            </tr>
	
            <tr align="left">
                <td class="text_10b" width="180" height="<?php  if($res_party_detail[0]['address'] || $res_party_detail[0]['address2']){print "5px";} else{ echo "14px";} ?>" valign="<?php  if($res_party_detail[0]['address'] || $res_party_detail[0]['address2']){print "bottom";} else{ echo "top";} ?>"><b><u><?php  if($res_party_detail[0]['address'] || $res_party_detail[0]['address2']){print $res_party_detail[0]['address']." ".$res_party_detail[0]['address2'];} else{ echo underLine(35);} ?></u></b></td>
                <td class="text_10b" width="180" height="<?php print ($city_address) ? "5px" : "14px"; ?>" valign="<?php print ($city_address) ? "bottom" : "top"; ?>"><b><u><?php print ($city_address) ? $city_address : underLine(35) ; ?></u></b></td>
                <td class="text_10b" width="180" height="<?php print (trim(ucwords($res_party_detail[0]['home_ph']))) ? "5px" : "14px"; ?>" valign="<?php print (trim(ucwords($res_party_detail[0]['home_ph']))) ? "bottom" : "top"; ?>"><b><u><?php print (trim(ucwords($res_party_detail[0]['home_ph']))) ? core_phone_format(trim(ucwords($res_party_detail[0]['home_ph']))) : underLine(35); ?></u></b></td>
                <td class="text_10b" width="180" height="<?php print (trim(ucwords($res_party_detail[0]['mobile']))) ? "5px" : "14px"; ?>" valign="<?php print (trim(ucwords($res_party_detail[0]['mobile']))) ? "bottom" : "top"; ?>"><b><u><?php print (trim(ucwords($res_party_detail[0]['mobile']))) ? core_phone_format(trim(ucwords($res_party_detail[0]['mobile']))) : underLine(35); ?></u></b></td>
            </tr>
		
            <tr align="left">
                <td class="text_10" width="180" style="color:#444444; <?php echo $paddingTDTop; ?>"><i>Street</i></td>
                <td class="text_10" width="180" style="color:#444444; <?php echo $paddingTDTop; ?>"><i>City, State Zip</i></td>
                <td class="text_10" width="180" style="color:#444444; <?php echo $paddingTDTop; ?>"><i>Home Phone#</i></td>
                <td class="text_10" width="180" style="color:#444444; <?php echo $paddingTDTop; ?>"><i>Mobile#</i></td>
            </tr>
	
			<?php
		//}
		
		//---  Patient custom field Data --------
		$getCustomField = "select cf.control_lable as adminControlLable,cf.control_type as adminControltype,cf.default_value as adminDefaultvalue,	
							pcf.patient_control_value as patientControlVal from custom_fields cf 
							LEFT JOIN patient_custom_field pcf on 
							(cf.id = pcf.admin_control_id and pcf.patient_id = '$pid') 
							where cf.module = 'Patient_Info' 
							and cf.sub_module ='Demographics' 
							and cf.status = '0' order by cf.id ";
		
		$rsCustomField = imw_query($getCustomField);
		if(imw_num_rows($rsCustomField)>0){
		?>				
		<?php
			$counter= 1;
			$controlText = "";
			$controlLabel = "";
			$process = 0;
			while($row = imw_fetch_assoc($rsCustomField)){
				$writeData = "";
					if($process == 0){
						$process = 1;
					?>	
                    <tr>
                        <td colspan="4" style="height:1px;"><img src="<?php echo $imgPath; ?>"/></td>
                    </tr>				
                    <tr>
                        <td bgcolor="#c0c0c0" align="left" colspan="4"><b>MISCELLANEOUS</b></td>
                    </tr>	
					<?php 
					}	
					if($counter == 1){				
						$controlText .= "<tr class=\"bgColor\">";
						$controlLabel .= "<tr>";
					}
					if($row['adminControltype'] == "checkbox"){
						$controlType = "checkbox";								
						if($row['patientCbkControlVal']){
							if($row['patientCbkControlVal'] == "checked"){
								$checked = "checked";
							}									
						}	
						elseif($row['adminCbkDefaultSelect'] == 1){
							$checked = "checked";
						}	
						
						if($row['patientControlVal'] != ""){
							if($row['adminDefaultvalue']){
								$cbkValue = $row['adminDefaultvalue'];
							}
							else{
								$cbkValue = $row['patientControlVal'];
							}
						}
						elseif($row['adminDefaultvalue'] != ""){
							$cbkValue = $row['adminDefaultvalue'];
						}		
						elseif($checked == "checked"){
							$cbkValue = "checked";
						}
						else{
							$cbkValue = "checked";
						}			
						$cbkTextBox = "<input type=\"checkbox\" class=\"input_text_10\" value='".$cbkValue."' name='".$row['adminControlName']."' $checked/>";
						if($row['adminDefaultvalue']){
							$cbkTextBoxLabel = $row['adminControlLable']."(".$row['adminDefaultvalue'].")";
						}
						else{
							$cbkTextBoxLabel = $row['adminControlLable'];
						}
					}
					elseif($row['adminControltype'] == "text"){
						$controlType = "text";
						$cbkTextBox = $row['patientControlVal'];
						$cbkTextBoxLabel = $row['adminControlLable'];
					}
					$hightMisc = $topBottom = "";
					if($cbkTextBox){
					 	$writeData = $cbkTextBox;
						$hightMisc = "5px";
						$topBottom = "bottom";
					}
					else{
						$writeData = underLine(35);
						$hightMisc = "14px";
						$topBottom = "top";
					}
					$controlText .= "<td height=\"$hightMisc\" valign=\"$topBottom\">".$writeData."</td>";
					$controlLabel .= "<td class=\"text_10\" width=\"180\" style=\"color:#444444; $paddingTDTop; \"><i>".$cbkTextBoxLabel."</i></td>";															
					$counter++;
					if($counter == 5){
						echo $controlText .= "</tr>";
						echo $controlLabel .= "</tr>";
						$controlText = "";
						$controlLabel = "";
						$counter = 1;
						$rowClosed = true;
					}
					
			}
			if($process == 1){
				if($counter < 5 && $rowClosed==false){
					echo $controlText .= "</tr>";
					echo $controlLabel .= "</tr>";																	
				}else{$rowClosed=false;}
			}
		?>
			<?php 
			if($process == 1){
			?>
			
			<?php 
			}
			?>
		<?php	
		}
		?>
	</table>	
		<?php 	
		//---  Insurance Details --------
		if($face_sheet_scan == 1 || $insurance_scan == 1){
			$scan_card_var = true;
		}else{
			$scan_card_var = false;
		}
		$insProcess = 0;
		$qry = imw_query("select * from insurance_case where patient_id = '$pid' and case_status = 'Open' order by ins_case_type");
		$caseDetail = array();	
		while($rowCD = imw_fetch_assoc($qry)){	
			$caseDetail[] = $rowCD;
		}		  
		for($r=0;$r<count($caseDetail);$r++){		
		//if(count($caseDetail)>0){
			$ins_caseid = "";
			$ins_caseid = $caseDetail[$r]['ins_caseid'];
			$start_date = substr($caseDetail[$r]['start_date'],0,strpos($caseDetail[$r]['start_date'],' '));
			$end_date = substr($caseDetail[$r]['end_date'],0,strpos($caseDetail[$r]['end_date'],' '));
			$openDate = get_date_format($start_date);
			if($end_date != '0000-00-00')
				$end_Date = get_date_format($end_date);
			//--- Get Insurance Case Type -------
			$ins_case_type = $caseDetail[$r]['ins_case_type'];
			$qry = imw_query("select case_name from insurance_case_types where case_id = $ins_case_type");
			$caseType = array();	
			while($rowCT = imw_fetch_assoc($qry)){	
				$caseType[] = $rowCT;
			}
			//-- Get Responsible Party Name -----
			$resp_name = $res_party_detail[0]['lname'].', ';
			$resp_name .= $res_party_detail[0]['fname'].' ';
			$resp_name .= $res_party_detail[0]['mname'];	
			if(trim($resp_name) == ','){
				$resp_name = 'Self';
			}	
			$insType = array('primary','Secondary','Tertiary');
			$ins = 0;
			foreach($insType as $val){
				$qry = imw_query("select * from insurance_data where pid='$pid' and type='$val' 
						and actInsComp='1' and ins_caseid='$ins_caseid' and provider > 0
						and effective_date <= now() 
						and (expiration_date = '0000-00-00 00:00:00' 
						or expiration_date > now())");
				$insPriDetails = array();	
				while($rowinsp = imw_fetch_assoc($qry)){	
					$insPriDetails[] = $rowinsp;
				}
				if((count($insPriDetails)>0 && $val == 'Tertiary') || ($val == 'primary' || $val == 'Secondary')){
					$priInsHeading = "$val insurance carrier details";
					$scan_card = '';
					if($insPriDetails[0]['scan_card'] != '' || $insPriDetails[0]['scan_card2'] != ''){
						$scan_card = 'Ins. Card Scanned';
						//--- Scan Card Print Or Not ------
						if($scan_card_var){					
							if($insPriDetails[0]['scan_card']){
								$scan_card_arr[$val]['scan_card'] = $insPriDetails[0]['scan_card'];
							}
							if($insPriDetails[0]['scan_card2']){
								$scan_card_arr[$val]['scan_card2'] = $insPriDetails[0]['scan_card2'];
							}
						}
					}							
		if($insProcess == 0){
			$insProcess++;
		?>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">			
		<?php
		}
		if($ins == 0){		
		?>
            <tr>
				<td colspan="4" style="height:1px;"><img src="<?php echo $imgPath; ?>"/></td>
			</tr>
            <tr>
                <td bgcolor="#c0c0c0" align="left" colspan="4"><b>INSURANCE</b></td>
            </tr>					
			<tr> 	
				<td class="text_10b" align="left" width="180" height="<?php print ($caseDetail[$r]['ins_caseid']) ? "5px" : "14px"; ?>" valign="<?php print ($caseDetail[$r]['ins_caseid']) ? "bottom" : "top"; ?>"><b><u><?php print ($caseDetail[$r]['ins_caseid']) ? $caseDetail[$r]['ins_caseid'] : underLine(35); ?></u></b></td>
				<td class="text_10b" width="180" height="<?php print ($caseType[0]['case_name']) ? "5px" : "14px"; ?>" valign="<?php print ($caseType[0]['case_name']) ? "bottom" : "top"; ?>"><b><u><?php print ($caseType[0]['case_name']) ? $caseType[0]['case_name'] : underLine(35); ?></u></b></td>
				<td class="text_10b" width="180" height="<?php print ($resp_name) ? "5px" : "14px"; ?>" valign="<?php print ($resp_name) ? "bottom" : "top"; ?>"><b><u><?php print ($resp_name) ? $resp_name : underline(35); ?></u></b></td>		
				<td class="text_10b" width="180" height="<?php print (($openDate) ? "5px" : "14px") or (($end_Date) ? "5px" : "14px"); ?>" valign="<?php print (($openDate) ? "bottom" : "top") or (($end_Date) ? "bottom" : "top"); ?>"><b><u><?php print (($openDate) ? $openDate : underLine(20)) .' / '.(($end_Date) ? $end_Date : underLine(20)); ?></u></b></td>
			</tr>
			<tr>
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>"><i>Ins. Case#</i></td>
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>"><i>Ins. Case Type</i></td>
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>"><i>Responsible Party</i></td>
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>"><i>Case Open/End Date</i></td>
			</tr>
		<?php
			}
		$ins++;
		?>									
            <tr>
				<td colspan="4" style="height:1px;"><img src="<?php echo $imgPath; ?>"/></td>
			</tr>
            <tr>
                <td bgcolor="#c0c0c0" align="left" colspan="4"><b><?php print strtoupper(trim($priInsHeading)).' '.strtoupper(trim($scan_card)); ?></b></td>
            </tr>								
			<?php
			$provider = $insPriDetails[0]['provider'];
			$qry = imw_query("select name from insurance_companies where id = '".$provider."' ");
			$insDetails = array();	
			while($rowins = imw_fetch_assoc($qry)){	
				$insDetails[] = $rowins;
			}		
			$insProviderName = "";
			$insProviderName = ucwords(strtolower($insDetails[0]['name']));
			
			?>
			<tr align="left"> 	
				<td class="text_10b" width="180" height="<?php print ($insProviderName) ? "5px" : "14px"; ?>" valign="<?php print ($insProviderName) ? "bottom" : "top"; ?>"><b><u><?php print ($insProviderName) ? $insProviderName : underLine(35); ?></u></b></td>
				<td class="text_10b" width="180" height="<?php print ($insPriDetails[0]['policy_number']) ? "5px" : "14px"; ?>" valign="<?php print ($insPriDetails[0]['policy_number']) ? "bottom" : "top"; ?>"><b><u><?php print ($insPriDetails[0]['policy_number']) ? $insPriDetails[0]['policy_number'] : underLine(35); ?></u></b></td>
				<td class="text_10b" width="180" height="<?php print ($insPriDetails[0]['group_number']) ? "5px" : "14px"; ?>" valign="<?php print ($insPriDetails[0]['group_number']) ? "bottom" : "top"; ?>"><b><u><?php print ($insPriDetails[0]['group_number']) ? $insPriDetails[0]['group_number'] : underLine(35); ?></u></b></td>		
				<td class="text_10b" width="180" height="<?php print ($insPriDetails[0]['plan_name']) ? "5px" : "14px"; ?>" valign="<?php print ($insPriDetails[0]['plan_name']) ? "bottom" : "top"; ?>"><b><u><?php print ($insPriDetails[0]['plan_name']) ? $insPriDetails[0]['plan_name'] : underLine(35); ?></u></b></td>
			</tr>
			
			<tr>
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>"><i>Ins. Carrier</i></td>
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>"><i>Policy#</i></td>
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>"><i>Group#</i></td>
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>"><i>Plan Name</i></td>
			</tr>		
			<?php
			$actDate = substr($insPriDetails[0]['effective_date'],0,strpos($insPriDetails[0]['effective_date'],' '));
			$activeDate = '';
			if($actDate != '0000-00-00')
				$activeDate = get_date_format($actDate);
				$expDate = substr($insPriDetails[0]['expiration_date'],0,strpos($insPriDetails[0]['expiration_date'],' '));
				$expireDate = '';
			if($expDate != '0000-00-00')
				$expireDate = get_date_format($expDate);
				$copay = numberFormat($insPriDetails[0]['copay'],2);
			?>
			<tr align="left"> 	
				<td class="text_10b" width="180" height="<?php print ($activeDate != "--") ? "5px" : "14px"; ?>" valign="<?php print ($activeDate != "--") ? "bottom" : "top"; ?>"><b><u><?php print ($activeDate != "--") ? $activeDate : underLine(35); ?></u></b></td>
				<td class="text_10b" width="180" height="<?php print ($patientDetails[0]["ss"] != "--") ? "5px" : "14px"; ?>" valign="<?php print ($patientDetails[0]["ss"] != "--") ? "bottom" : "top"; ?>"><b><u><?php print ($patientDetails[0]["ss"] != "--") ? ucwords(substr_replace($patientDetails[0]["ss"],'XXX-XX',0,6)) : underLine(35);//print $expireDate; ?></u></b></td>
				<td class="text_10b" width="180" height="<?php print ($copay != "$0.00") ? "5px" : "14px"; ?>" valign="<?php print ($copay != "$0.00") ? "bottom" : "top"; ?>"><b><u><?php print ($copay != "$0.00") ? $copay : underLine(35); ?></u></b></td>		
				<td class="text_10b" width="180" height="<?php print ($insPriDetails[0]['referal_required']) ? "5px" : "14px"; ?>" valign="<?php print ($insPriDetails[0]['referal_required']) ? "bottom" : "top"; ?>"><b><u><?php print ($insPriDetails[0]['referal_required']) ? $insPriDetails[0]['referal_required'] : underLine(35); ?></u></b></td>
			</tr>
			
			<tr>
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>"><i>Activaton Date</i></td>
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>"><i>Social Security#</i></td>
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>"><i>Copay</i></td>
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>"><i>Referal Required</i></td>
			</tr>	
			<?php
			$subscriberName = $insPriDetails[0]['subscriber_lname'].', ';
			$subscriberName .= $insPriDetails[0]['subscriber_fname'].' ';
			$subscriberName .= $insPriDetails[0]['subscriber_mname'];
			if(trim($subscriberName) == ","){
				$subscriberName = "";
			}
			if($insPriDetails[0]['subscriber_DOB'] != '0000-00-00')
				$subscriber_DOB = get_date_format($insPriDetails[0]['subscriber_DOB']);
			else
				$subscriber_DOB = '';
			?>
				
			<?php
			$strToShowRelation = $insPriDetails[0]['subscriber_relationship'];
			if(strtolower($insPriDetails[0]['subscriber_relationship']) == "doughter"){
				$strToShowRelation = "Daughter";
			}
			?>
			<tr align="left"> 
				<td class="text_10b" width="180" height="<?php print (ucwords($strToShowRelation)) ? "5px" : "14px"; ?>" valign="<?php print (ucwords($strToShowRelation)) ? "bottom" : "top"; ?>"><b><u><?php print (ucwords($strToShowRelation)) ? ucwords($strToShowRelation) : underLine(35); ?></u></b></td>
				<td class="text_10b" width="180" height="<?php print (trim($subscriberName)) ? "5px" : "14px"; ?>" valign="<?php print (trim($subscriberName)) ? "bottom" : "top"; ?>"><b><u><?php print (trim($subscriberName)) ? trim($subscriberName) : underLine(35); ?></u></b></td>
				<td class="text_10b" width="180" height="<?php print ($subscriber_DOB != "--") ? "5px" : "14px"; ?>" valign="<?php print ($subscriber_DOB != "--") ? "bottom" : "top"; ?>"><b><u><?php print ($subscriber_DOB != "--") ? $subscriber_DOB : underLine(35); ?></u></b></td>		
				<td class="text_10b" width="180" height="<?php print ($insPriDetails[0]['subscriber_ss']) ? "5px" : "14px"; ?>" valign="<?php print ($insPriDetails[0]['subscriber_ss']) ? "bottom" : "top"; ?>"><b><u><?php print (($insPriDetails[0]['subscriber_ss']) ? ucwords(substr_replace($insPriDetails[0]['subscriber_ss'],'XXX-XX',0,6)) : underLine(35)); ?></u></b></td>
			</tr> 
			
			<tr>
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>"><i>Subscriber Relation</i></td>
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>"><i>Subscriber Name</i></td>
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>"><i>DOB</i></td>
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>"><i>Social Security#</i></td>
			</tr>	
			<?php
				$subscriberAddress = $insPriDetails[0]['subscriber_city'].', ';
				$subscriberAddress .= $insPriDetails[0]['subscriber_state'].' ';
				$subscriberAddress .= $insPriDetails[0]['subscriber_postal_code'];
				if(trim($subscriberAddress) == ","){
					$subscriberAddress = "";
				}
			?>
			<tr align="left"> 	
				<td class="text_10b" width="180" height="<?php print ($insPriDetails[0]['subscriber_street']) ? "5px" : "14px"; ?>" valign="<?php print ($insPriDetails[0]['subscriber_street']) ? "bottom" : "top"; ?>"><b><u><?php print ($insPriDetails[0]['subscriber_street']) ? htmlentities($insPriDetails[0]['subscriber_street']) : underLine(35); ?></u></b></td>
				<td class="text_10b" width="180" height="<?php print (trim($subscriberAddress)) ? "5px" : "14px"; ?>" valign="<?php print (trim($subscriberAddress)) ? "bottom" : "top"; ?>"><b><u><?php print (trim($subscriberAddress)) ? trim($subscriberAddress) : underLine(35); ?></u></b></td>
				<td class="text_10b" width="180"><b><u>Yes</u></b></td>		
				<td class="text_10b" width="180"><b><u>Yes</u></b></td>
			</tr>			
			<tr>
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>"><i>Street</i></td>
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>"><i>City, State Zip</i></td>
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>"><i>Payment Authorized</i></td>
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>"><i>Signature on File</i></td>
			</tr>				
			<?php
			if($insPriDetails[0]['referal_required'] == 'Yes'){
				$id = $insPriDetails[0]['id'];
				$qry = imw_query("select * from patient_reff where ins_data_id = $id and (no_of_reffs > 0 or now() between effective_date and end_date)");
				$reffDetails = array();	
				while($rowreff = imw_fetch_assoc($qry)){	
					$reffDetails[] = $rowreff;
				}	
			?>	
            <tr>
				<td colspan="4" style="height:1px;"><img src="<?php echo $imgPath; ?>"/></td>
			</tr>					
            <tr>
                <td bgcolor="#c0c0c0" align="left" colspan="4"><b>REFERRAL REQUIRED</b></td>
            </tr>	
			<?php
			if($reffDetails[0]['reff_date'] != '0000-00-00')
				$reff_date = get_date_format($reffDetails[0]['reff_date']);
			else
				$reff_date = '';
			$reff_phy_id = $reffDetails[0]['reff_phy_id'];
			//--- get Reffering Physician Name --------
			$qry = imw_query("select concat(LastName,', ',FirstName) as name,MiddleName from refferphysician where physician_Reffer_id = $reff_phy_id");
			$reffPhyDetails = array();
			while($rowreffd = imw_fetch_assoc($qry)){	
				$reffPhyDetails[] = $rowreffd;
			}		
			$reffPhyName = $reffPhyDetails[0]['name'];
			$reffPhyName .= $reffPhyDetails[0]['MiddleName'] != '' ? ' '.substr($reffPhyDetails[0]['MiddleName'],0,1).'.' : '';
			?>
			<tr align="left"> 	
				<td class="text_10b" width="180" height="<?php print (trim($reffPhyName)) ? "5px" : "14px"; ?>" valign="<?php print (trim($reffPhyName)) ? "bottom" : "top"; ?>"><b><u><?php print (trim($reffPhyName)) ? trim($reffPhyName) : underLine(35); ?></u></b></td>
				<td class="text_10b" width="180" height="<?php print (trim($reffDetails[0]['reffral_no'])) ? "5px" : "14px"; ?>" valign="<?php print (trim($reffDetails[0]['reffral_no'])) ? "bottom" : "top"; ?>"><b><u><?php print (trim($reffDetails[0]['reffral_no'])) ? trim($reffDetails[0]['reffral_no']) : underLine(35); ?></u></b></td>
				<td class="text_10b" width="180" height="<?php print ($reff_date) ? "5px" : "14px"; ?>" valign="<?php print ($reff_date) ? "bottom" : "top"; ?>"><b><u><?php print ($reff_date) ? $reff_date : underLine(35); ?></u></b></td>		
				<td class="text_10b" width="180" height="<?php  if($reffDetails[0]['no_of_reffs'] || $reffDetails[0]['reff_used']) {print "5px";} else{ echo "14px";} ?>" valign="<?php  if($reffDetails[0]['no_of_reffs'] || $reffDetails[0]['reff_used']) {print "bottom";} else{ echo "top";} ?>"><b><u><?php  if($reffDetails[0]['no_of_reffs'] || $reffDetails[0]['reff_used']) {print $reffDetails[0]['no_of_reffs'] + $reffDetails[0]['reff_used'];} else{ echo underLine(35);} ?></u></b></td>
			</tr>
		
			<tr>
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>" width="180"><i>Referring Dr.</i></td>
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>" width="180"><i>Ref #</i></td>
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>" width="180"><i>Ref Date</i></td>
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>" width="180"><i># of Visits</i></td>
			</tr>		
			<?php
			if($reffDetails[0]['effective_date'] != '0000-00-00')
				$effective_date = get_date_format($reffDetails[0]['effective_date']);
			else
				$effective_date = '';
			if($reffDetails[0]['end_date'] != '0000-00-00')
				$end_date = get_date_format($reffDetails[0]['end_date']);
			else
				$end_date = '';
			?>
			<tr align="left"> 	
				<td class="text_10b" width="180" height="<?php print ($effective_date) ? "5px" : "14px"; ?>" valign="<?php print ($effective_date) ? "bottom" : "top"; ?>"><b><u><?php print ($effective_date) ? $effective_date : underLine(35); ?></u></b></td>
				<td class="text_10b" width="180" height="<?php print (trim($end_date)) ? "5px" : "14px"; ?>" valign="<?php print (trim($end_date)) ? "bottom" : "top"; ?>"><b><u><?php print (trim($end_date)) ? trim($end_date) : underLine(35); ?></u></b></td>
				<td class="text_10b" colspan="2" height="<?php print ($reffDetails[0]['note']) ? "5px" : "14px"; ?>" valign="<?php print ($reffDetails[0]['note']) ? "bottom" : "top"; ?>"><b><u><?php print ($reffDetails[0]['note']) ? $reffDetails[0]['note'] : underLine(35); ?></u></b></td>		
			</tr>
		
			<tr>
				<td class="text_10" width="180" style="color:#444444; <?php echo $paddingTDTop; ?>"><i>Start Date</i></td>
				<td class="text_10" width="180" style="color:#444444; <?php echo $paddingTDTop; ?>"><i>End date</i></td>
				<td class="text_10" colspan="2" style="color:#444444; <?php echo $paddingTDTop; ?>"><i>Notes</i></td>
			</tr>
			<?php
			}
			}	
			}
		//}
		?>		
		
		<?php 
		}
		if($insProcess > 0){
		?>
			<tr>
				<td colspan="4" style="height:1px;"><img src="<?php echo $imgPath; ?>"/></td>
			</tr>
            <tr>
                <td bgcolor="#c0c0c0" align="left" colspan="4"><b>EMPLOYER</b></td>
            </tr>	
			<tr align="left">				
				<td class="text_10b" width="180" height="<?php print (trim(ucwords($emp_details[0]['name']))) ? "5px" : "14px"; ?>" valign="<?php print (trim(ucwords($emp_details[0]['name']))) ? "bottom" : "top"; ?>"><b><u><?php print (trim(ucwords($emp_details[0]['name']))) ? trim(ucwords($emp_details[0]['name'])) : underLine(35); ?></u></b></td>
				<td class="text_10b" width="180" height="<?php if(trim(ucwords($emp_details[0]['street'])) || trim(ucwords($emp_details[0]['street2']))){print "5px";}else {print "14px";} ?>" valign="<?php if(trim(ucwords($emp_details[0]['street'])) || trim(ucwords($emp_details[0]['street2']))){print "bottom";}else {print "top";} ?>"><b><u><?php if(trim(ucwords($emp_details[0]['street'])) || trim(ucwords($emp_details[0]['street2']))){print trim(ucwords($emp_details[0]['street']))." ".trim(ucwords($emp_details[0]['street2']));}else {print underLine(50);} ?></u></b></td>
				<td class="text_10b" colspan="2" width="180" height="<?php print (trim(ucwords($emp_adderss))) ? "5px" : "14px"; ?>" valign="<?php print (trim(ucwords($emp_adderss))) ? "bottom" : "top"; ?>"><b><u><?php print (trim(ucwords($emp_adderss))) ? trim(ucwords($emp_adderss)) : underLine(50); ?></u></b></td>
			</tr>			
			<tr align="left">				
				<td class="text_10"  style="color:#444444; <?php echo $paddingTDTop; ?>" width="180"><i>Employer Name</i></td>
				<td class="text_10" style="color:#444444; <?php echo $paddingTDTop; ?>" width="180"><i>Street</i></td>
				<td class="text_10" colspan="2" style="color:#444444; <?php echo $paddingTDTop; ?>" width="180"><i>City, State Zip</i></td>
			</tr>
		</table>
		<?php 
		}
//---  Insurance Details End --------
		if(isset($_REQUEST["face_sheet_scan"]) && $_REQUEST["face_sheet_scan"] == 1){
			//---  Show Scan Card Documents --------
			$pageHeight = 900;
			if(sizeof($scan_card_arr)>0){
				print '</page><page>';
				foreach($scan_card_arr as $key => $ins_scan_arr){
					foreach($ins_scan_arr as $scan_key => $scan_val){
						$img_path = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH').$scan_val;
						$img_real_path = realpath($img_path);
						$ImageSize = getimagesize($img_real_path);
						$imageSizeArr[] = $ImageSize[1];
						$pageCount ++;
					}
				}
			}
			$i=1;
			if(count($scan_card_arr)>0){
				foreach($scan_card_arr as $key => $ins_scan_arr){
					?>			
                    <table width="100%" border="0" rules="none" cellpadding="1" cellspacing="1">
                                <?php
                                if(count($ins_scan_arr) > 0){
                                    ?>
                        <tr valign="middle" height="25px" class="text_10" bgcolor="#c0c0c0">
                            <td class="text_10" colspan="2"><b><?php print strtoupper($key.' scan document'); ?></b></td>
                        </tr>
                    </table>
						<?php			
						foreach($ins_scan_arr as $scan_key => $scan_val){	
							$img_path = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH').$scan_val;
							$img_real_path = realpath($img_path);
							$path_root = pathinfo($img_real_path);
							if(file_exists($img_real_path) != '' && is_dir($img_real_path) == ''){                             
								$img_name = substr($scan_val,strrpos($scan_val,'/')+1);
								$patient_img[$key.'-'.$scan_key] = $img_name;
								@copy($img_real_path,$path_root['dirname'].'/tmp/'.$img_name);
								$img_path = $img_name;
								$priImageSize = getimagesize($img_real_path);
								$newSize = '';
								if($priImageSize[1]>600){
									$newSize = imageResize($priImageSize[0],$priImageSize[1],600);
									$image_view = '<img src="'.$path_root['dirname'].'/tmp/'.$img_path.'" '.$newSize.'>';
									$priImageSize[1] = 600;
								}else{
									$image_view = '<img  src="'.$path_root['dirname'].'/tmp/'.$img_path.'">';
								}					
								?>						
                                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                        <tr>
                                            <td class="text_10"><b><?php print $img_name; ?></b></td>
                                        </tr>
                                        <tr>
                                            <td class="text_10b"><i><?php print $scan_key; ?></i></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" align="center"><?php print $image_view; ?></td>
                                        </tr>
                                    </table>
								<?php
								$pageHeight = $pageHeight - $priImageSize[1];
								if($pageHeight < $imageSizeArr[$i-1] && $i < $pageCount ){
									print '</page><page>';
									$pageHeight = 900;
								}
								$i++;
							}
						}
					}
				}
			}
		}
		if($int_appt == $int_tot_appt){		
			print '</page>';
		}
		if($int_appt < $int_tot_appt){
			print '</page>';
		}
		$query_staring = array();
		if(count($patient_img)>0){
			foreach($patient_img as $key => $val){
				$query_staring[] = $val;
			}
		}
		$queryStaringString .= implode(',',$query_staring);
		$patient_print_data .= ob_get_contents();
		ob_end_clean();
		$int_appt++;
	}
}
$patient_print_data = trim($patient_print_data);
if(empty($patient_print_data) == false){
	$patient_print_data .= '<style type="text/css">table{font-size:12px;}</style>';	
	$file_location = write_html($patient_print_data);
?>
<div class="text-center alert alert-info">Please Check PDF.</div>
<?php
}
else{
?>
<div class="text-center alert alert-info">No Search Done.</div>
<?php
}
?>