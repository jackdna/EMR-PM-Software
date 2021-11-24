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
/*
FILE : surgery_appointment_facesheet_print.php
PURPOSE : Display results of Surgery Appointments report
ACCESS TYPE : Direct
*/

//Function files
set_time_limit(900);
// require_once(dirname(__FILE__)."/../common/functions.inc.php");
// require_once(dirname(__FILE__)."/../main/common_functions.php");	
// require_once(dirname(__FILE__)."/../patient_access/common/config.php");	
// require_once(dirname(__FILE__)."/../patient_access/common/functions.php");	
// require_once(dirname(__FILE__)."/../main/main_functions.php");
// require_once(dirname(__FILE__)."/../admin/chart_more_functions.php");
// require_once(dirname(__FILE__)."/../common/audit_common_function.php");
//require_once(dirname(__FILE__)."/../Medical_history/common/common_functions.php");

//$objManageData = new ManageData;

$delimiter = '~|~';
$hifen = ' - ';
function underLine($to){
	$NBSP = "<u>";
	for($counter = 1; $counter<=$to; $counter++){
		$NBSP .= "&nbsp;";	
	}
	$NBSP .= "</u>";
	return $NBSP;
}

//in case all facilities are selected
$str_rep_fac = implode(",", $rep_fac);
if($rep_fac == ''){
	$qry = imw_query("select id from facility");
	$res = array();
	$fac_arr = array();
	while ($fac_res = imw_fetch_assoc($qry)) {
		$res[] = $fac_res;
	}
	for($i=0;$i<count($res);$i++){
		$fac_arr[] = $res[$i]['id'];
	}
	$str_rep_fac = implode(',',$fac_arr);
}
//getting selected provider ids
$strProviderIds = implode(",",$providerID);

if(trim($strProviderIds) == ""){
	$provQry = imw_query("select id from users where Enable_Scheduler = 1");
	$provQryRes = array();
	$$provid = array();
	while ($pro_res = imw_fetch_assoc($provQry)) {
		$provQryRes[] = $pro_res;
	}
	for($i=0;$i<count($provQryRes);$i++){
		$provid[] = $provQryRes[$i]['id'];
	}
	$strProviderIds = implode(',',$provid);
}

$blIncludePatientAddress = false;
if(isset($_REQUEST['include_pat_Add']) && $_REQUEST['include_pat_Add'] == 1){
	$blIncludePatientAddress = true;
}

//changing date format
$dtEffectiveDate = $_REQUEST['eff_date'];
list($m,$d,$y) = preg_split('/-/', $dtEffectiveDate);
$dtDBEffectDate = $y.'-'.$m.'-'.$d;

$intTimeStamp = mktime(0, 0, 0, $m, $d, $y);
$dtShowEffectDate = date("m/d/Y", $intTimeStamp);
$strDayName = date('l', $intTimeStamp);

//getting week
$week = ceil($d/7);

//getting week day
$weekDay = date("N",$intTimeStamp);

$str_appt_qry = "SELECT sa.id, sa.sa_app_starttime as orgnalAppStartTime, sp.proc, u.lname, fc.name, sa.sa_patient_id, sa.sa_facility_id, DATE_FORMAT(sa.sa_app_start_date, '%a ".get_sql_date_format('','y','/')."') as sa_app_start_date, TIME_FORMAT(sa.sa_app_starttime,'%h:%i %p') as sa_app_starttime
					FROM schedule_appointments sa 
						LEFT JOIN facility fc ON fc.id = sa.sa_facility_id 
						LEFT JOIN users u ON u.id = sa.sa_doctor_id 
						LEFT JOIN users o ON o.username = sa.sa_madeby 
						LEFT JOIN patient_data p ON p.id = sa.sa_patient_id  
						LEFT JOIN slot_procedures sp ON sp.id = sa.procedureid  
						LEFT JOIN patient_tests pt ON (pt.id = sa.sa_test_id AND pt.facility = sa.sa_facility_id) 
					WHERE 
						sa_facility_id IN (".$str_rep_fac.") 
						AND sa_patient_app_status_id NOT IN(201,18) 
						AND ('".$dtDBEffectDate."' BETWEEN sa_app_start_date AND sa_app_end_date) 
						AND sa_doctor_id IN (".$strProviderIds.") ";
$rs_appt = imw_query($str_appt_qry);
$patient_print_data = "";
$queryStaringString = "";
$int_tot_appt = imw_num_rows($rs_appt);
if($int_tot_appt > 0){
	$int_appt = 1;
	while($row_appt = imw_fetch_assoc($rs_appt)){
		ob_start();
		print '<newpage>';
		$pid = $row_appt["sa_patient_id"];
		//$appt_id = $row_appt["id"];
		
		$tdate=date("m-d-Y");
		$appt_dt = $row_appt["sa_app_start_date"];
		$appt_tm = $row_appt["sa_app_starttime"];
		$appt_fc = $row_appt["name"];
		$appt_pr = $row_appt["proc"];
		$appt_pv = $row_appt["lname"];

		//---get Detail For Patient -------
		$qry =imw_query("select * from patient_data where id = $pid");
		$patientDetails = array();
		while($row_pat = imw_fetch_assoc($qry)){
			$patientDetails[] = $row_pat;
		}	
		$patientName = $patientDetails[0]['lname'].', '.$patientDetails[0]['fname'].' ';
		$patientName .= $patientDetails[0]['mname'];
	
		$date = substr($patientDetails[0]['date'],0,strpos($patientDetails[0]['date'],' '));
		$created_date = get_date_format($date);
		$date_of_birth = get_date_format($patientDetails[0]['DOB']);
		$cityAddress = $patientDetails[0]['city'];
		if($patientDetails[0]['state'])
			$cityAddress .= ', '.$patientDetails[0]['state'].' ';
		else
			$cityAddress .= ' '.$patientDetails[0]['postal_code'];
		list($y,$m,$d) = explode('-',$patientDetails[0]['DOB']);
		$age = show_age($patientDetails[0]['DOB']) ;//date('Y') - $y ;

		//--- Get Physician Details --------
		$phyId = $patientDetails[0]['providerID'];
		if($phyId > 0){
			$qry = imw_query("select fname, lname, mname from users where id = '$phyId'");
			$phyNameArr = array();	
				while($rowphy = imw_fetch_assoc($qry)){	
					$phyNameArr[] = $rowphy;
				}
			$phyName = core_name_format($phyNameArr[0]['fname'],$phyNameArr[0]['mname'],$phyNameArr[0]['lname'],'');
		}

		//--- Get Reffering Physician Details --------
		$reffPhyId = $patientDetails[0]['primary_care_id'];
		$qry = imw_query("select FirstName, LastName, MiddleName from refferphysician where physician_Reffer_id = '$reffPhyId'");
		$reffPhyNameArr = array();	
		while($rowRF = imw_fetch_assoc($qry)){	
			$reffPhyNameArr[] = $rowRF;
		}
		$reffPhyName = core_name_format($reffPhyNameArr[0]['fname'],$reffPhyNameArr[0]['mname'],$reffPhyNameArr[0]['lname'],'');
		if(empty($reffPhyName) === true) $reffPhyName = $phyName;

		//---- Get Patient Facility Details -------
		$default_facility = $patientDetails[0]['default_facility'];
		$qry = imw_query("select facilityPracCode from pos_facilityies_tbl where pos_facility_id = '$default_facility'");
		$facilityRes = array();	
		while($rowfac = imw_fetch_assoc($qry)){	
			$facilityRes[] = $rowfac;
		}
		
		//--- Get Detail How create patient -------
		$created_by = $patientDetails[0]['created_by'];
		$qry = imw_query("select fname, lname from users where id = '$created_by'");
		$createByDetail = array();	
		while($rowCB = imw_fetch_assoc($qry)){	
			$createByDetail[] = $rowCB;
		}
		$createByName = substr(trim($createByDetail[0]['fname']), 0, 1).substr(trim($createByDetail[0]['lname'], 0 ,1));
		
		//--- Get Patient Responsible Party Details -----
		$qry = imw_query("select * from resp_party where patient_id = '$pid' and fname != '' and lname != ''");
		$res_party_detail = array();	
		while($rowrpd = imw_fetch_assoc($qry)){	
			$res_party_detail[] = $rowrpd;
		}
		//--- Get Patient Occupation Details ------
		$qry = imw_query("select * from employer_data where pid = '$pid' and name != ''");
		$emp_details = array();	
		while($rowemp = imw_fetch_assoc($qry)){	
			$emp_details[] = $rowemp;
		}
		//--- Get Default Facility Details -------
		$qry = imw_query("select default_group from facility where facility_type = '1'");
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

		//$qry1=imw_query("select * from  chart_left_cc_history where patient_id='$pid' and form_id='$form_id'");
		$qry1=imw_query("select * from  chart_master_table where patient_id='$pid' and id='$form_id'");
		$co=imw_num_rows($qry1);
		if(($co > 0)){
			$crow=imw_fetch_array($qry1);
			$date_of_service = date("m-d-Y", strtotime($crow["date_of_service"]));	
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
					$patientImage = '<img style="cursor:pointer" src="'.$dirPath.'" alt="patient Image" '.$imageWidth2.'>';
				}
				else{
					$patientImage = '<img style="cursor:pointer" src="'.$dirPath.'" alt="patient Image">';
				}		
			}
		}
		if($int_appt == 1){
?>
	<?php
		}
		?>		
		<table width="100%" border="0" cellspacing="0" rules="none" cellpadding="0">
			<tr> 
				<td><b><h1>FACE SHEET</h1></b></td>
				<td width="200px"><b><?php print $patientDetails[0]['title'].' '.$patientName; ?></b></td>
				<td align="center">ID :<b><?php print $patientDetails[0]['id']; ?></b></td>
				<td align="center"><b><?php print $groupDetails[0]['name']; ?></b></td>
			</tr>
			<tr>
				<td align="left" nowrap><b><?php echo $appt_dt;?> <?php echo $appt_tm;?><?php echo "-".$appt_pr;?></b></td>
				<?php
				if(isset($_REQUEST["appt_info"]) && $_REQUEST["appt_info"] == 1){
					?>
				<td><b>Dr. <?php echo $appt_pv;?> - <?php echo $appt_fc;?></b></td>
					<?php
				}else{
					?>
				<td></td>
					<?php
				}
				?>
				<td rowspan="3" align="center"><?php print $patientImage; ?></td>
				<td align="center"><b><?php print ucwords($groupDetails[0]['group_Address1']); ?></b></td>						
			</tr>
			
			<tr> 	
				<td></td>	
				<?php
				if(isset($_REQUEST["appt_info"]) && $_REQUEST["appt_info"] == 1){
					?>
				<td>&nbsp;</td>
					<?php
				}else{
					?>
				<td></td>
					<?php
				}
				?>
				<td align="center"><b><?php print $groupDetails[0]['group_City'].', '.$groupDetails[0]['group_State'].' '.$groupDetails[0]['group_Zip']; ?></b></td>	
			</tr>
			
		</table>
		<table cellpadding='0' cellspacing='0' border='<?php print $border; ?>' rules="none" width='100%' >
			<tr>
				<td bgcolor="#c0c0c0" align="left" colspan="4"><b><?php print strtoupper('Patient Demographics'); ?></b></td>
			</tr>
						
			<tr align="left" valign="top">
				<td class="text_10b" width="180" valign="bottom" height="5px" ><b><?php print trim($patientDetails[0]['title'].' '.ucwords($patientName)); ?></b></td>
				<td class="text_10b" width="180" valign="bottom" height="5px" ><b><?php print $date_of_birth; ?> (<?php print $age;?>)</b></td>
				<td class="text_10b" width="180" valign="bottom" height="5px" ><b><?php print $patientDetails[0]['id']; ?></b></td>
				<td class="text_10b" width="180" valign="<?php print (ucwords($patientDetails[0]['status'])) ? "bottom" : "top"; ?>" height="<?php print (ucwords($patientDetails[0]['status'])) ? "5px" : "14px"; ?>" >&nbsp;<b><?php print (ucwords($patientDetails[0]['status'])) ? ucwords($patientDetails[0]['status']) : underLine(35); ?></b></td>
			</tr>
									
			<tr align="left" valign="top" >
				<td class="text_10" style="color:#444444" width="180" valign="top" ><i>Patient Name</i></td>
				<td class="text_10" style="color:#444444" width="180" valign="top" ><i>DOB</i></td>
				<td class="text_10" style="color:#444444" width="180" valign="top" ><i>ID</i></td>
				<td class="text_10" style="color:#444444" width="180" valign="top" ><i>Marital</i></td>
			</tr>			
		
			<tr align="left" valign="top">
				<td class="text_10b" width="180" height="<?php print ($patientDetails[0]['ss'] != "--") ? "5px" : "14px"; ?>" valign="<?php print ($patientDetails[0]['ss'] != "--") ? "bottom" : "top"; ?>">&nbsp;<b><?php print ($patientDetails[0]['ss'] != "--") ? $patientDetails[0]['ss'] : underLine(35); ?></b></td>
				<?php
				if(isset($_REQUEST["appt_info"]) && $_REQUEST["appt_info"] == 1){
					?>
				<td></td>
					<?php
				}else{
					?>
				<td class="text_10b"><b><?php print $phyName; ?></b></td>
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
				<td class="text_10b" valign="top"><b><?php print ($facilityRes[0]['facilityPracCode']) ? $facilityRes[0]['facilityPracCode'] : underLine(35); ?></b></td>
					<?php
				}
				?>
				<td class="text_10b" width="180" height="<?php print ($created_date != "--") ? "5px" : "14px"; ?>" valign="top">&nbsp;<b><?php print ($created_date != "--") ? $created_date : underLine(35); ?></b></td>
			</tr>
			
			<tr align="left" valign="top">
				<td class="text_10" style="color:#444444" width="180"><i>Social Security#</i></td>
				<?php
				if(isset($_REQUEST["appt_info"]) && $_REQUEST["appt_info"] == 1){
					?>
				<td></td>
					<?php
				}else{
					?>
				<td class="text_10" style="color:#444444"><i>Physician</i></td>
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
				<td class="text_10" style="color:#444444"><i>Facility</i></td>
					<?php
				}
				?>
				<td class="text_10" style="color:#444444" width="180"><i>Registration date</i></td>
			</tr>
									
			<tr align="left" valign="top">
				<td class="text_10b" width="180" height="<?php print ($patientDetails[0]['sex']) ? "5px" : "14px"; ?>" valign="<?php print ($patientDetails[0]['sex']) ? "bottom" : "top"; ?>">&nbsp;<b><?php print ($patientDetails[0]['sex']) ? $patientDetails[0]['sex'] : underline(35); ?></b></td>
				<td class="text_10b" width="180" height="<?php print ($reffPhyName) ? "5px" : "14px"; ?>" valign="<?php print ($reffPhyName) ? "bottom" : "top"; ?>">&nbsp;<b><?php print ($reffPhyName) ? $reffPhyName : underLine(35); ?></b></td>
				<td class="text_10b" width="180" height="<?php print ($patientDetails[0]['driving_licence']) ? "5px" : "14px"; ?>" valign="<?php print ($patientDetails[0]['driving_licence']) ? "bottom" : "top"; ?>">&nbsp;<b><?php print ($patientDetails[0]['driving_licence']) ? $patientDetails[0]['driving_licence'] : underLine(35) ; ?></b></td>
				<?php
				if(isset($_REQUEST["appt_info"]) && $_REQUEST["appt_info"] == 1){
					?>
				<td></td>
					<?php
				}else{
					?>
				<td class="text_10b" colspan="2"><b><?php print ucwords($createByName); ?></b></td>
					<?php
				}
				?>				
			</tr>				
			<tr align="left" valign="top">
				<td class="text_10" style="color:#444444" width="180"><i>Sex</i></td>
				<td class="text_10" style="color:#444444" width="180"><i>Referring Dr.</i></td>
				<td class="text_10" style="color:#444444" width="180"><i>DL #</i></td>
				<?php
				if(isset($_REQUEST["appt_info"]) && $_REQUEST["appt_info"] == 1){
					?>
				<td></td>
					<?php
				}else{
					?>
				<td class="text_10" style="color:#444444" colspan="2"><i>Created By</i></td>	
					<?php
				}
				?>					
			</tr>			
			<?php
        
            //--- Check That All Fields Are Not Blank -------
            $street = trim(ucwords($patientDetails[0]['street']));
            if(trim(ucwords($patientDetails[0]['street'])) != '' && trim(ucwords($patientDetails[0]['street2'])) != ''){
                $street .= ', ';
            }
            if(trim(ucwords($patientDetails[0]['street2']))){
                $street .= trim(ucwords($patientDetails[0]['street2']));
            }
            
            ?>
            <tr align="left" valign="top">
                <td class="text_10b" width="180" height="<?php print ($street) ? "5px" : "14px"; ?>" valign="<?php print ($street) ? "bottom" : "top"; ?>">&nbsp;<b><?php print ($street) ? $street : underLine(35); ?></b></td>
                <td class="text_10b" width="180" height="<?php print (trim(ucwords($cityAddress))) ? "5px" : "14px"; ?>" valign="<?php print (trim(ucwords($cityAddress))) ? "bottom" : "top"; ?>">&nbsp;<b><?php print (trim(ucwords($cityAddress))) ? trim(ucwords($cityAddress)) : underLine(35); ?></b></td>
                <td class="text_10b" width="180" height="<?php print ($patientDetails[0]['phone_home'] != "000-000-0000") ? "5px" : "14px"; ?>" valign="<?php print ($patientDetails[0]['phone_home'] != "000-000-0000") ? "bottom," : "top"; ?>">&nbsp;<b><?php print ($patientDetails[0]['phone_home'] != "000-000-0000") ? core_phone_format($patientDetails[0]['phone_home']) : underLine(35); ?></b></td>
                <td class="text_10b"  width="180" height="<?php print ($patientDetails[0]['phone_biz']  != "000-000-0000") ? "5px" : "14px"; ?>" valign="<?php print ($patientDetails[0]['phone_biz']  != "000-000-0000") ? "bottom" : "top"; ?>">&nbsp;<b><?php print ($patientDetails[0]['phone_biz']  != "000-000-0000") ? core_phone_format($patientDetails[0]['phone_biz']) : underLine(35); ?></b></td>
            </tr>            
            <tr align="left" valign="top">
                <td class="text_10" style="color:#444444" width="180"><i>Street</i></td>
                <td class="text_10" style="color:#444444" width="180"><i>City, State Zip</i></td>
                <td class="text_10" style="color:#444444" width="180"><i>Home Phone#</i></td>
                <td class="text_10" style="color:#444444" width="180"><i>Work Phone#</i></td>
            </tr>
            <?php
            //--- get Occupation Details if patient fill in demographics --------
			if($emp_details[0]['city'])
				$emp_adderss .= $emp_details[0]['city'];
			if($emp_details[0]['state'])
				$emp_adderss .= ', '.$emp_details[0]['state'];
			if($emp_details[0]['postal_code'])
				$emp_adderss .= ' '.$emp_details[0]['postal_code'];
            ?>
            <tr valign="middle"  class="text_10" bgcolor="#c0c0c0">
                <td align="left" colspan="4"><b><?php print strtoupper('Patient Occupation'); ?></b></td>
            </tr>	
            <tr align="left">
                <td class="text_10b" width="180" height="<?php print (trim(ucwords($patientDetails[0]['occupation']))) ? "5px" : "14px"; ?>" valign="<?php print (trim(ucwords($patientDetails[0]['occupation']))) ? "bottom" : "top"; ?>">&nbsp;<b><?php print (trim(ucwords($patientDetails[0]['occupation']))) ? trim(ucwords($patientDetails[0]['occupation'])) : underLine(35); ?></b></td>
                <td class="text_10b" width="180" height="<?php print (trim(ucwords($emp_details[0]['name']))) ? "5px" : "14px"; ?>" valign="<?php print (trim(ucwords($emp_details[0]['name']))) ? "bottom" : "top"; ?>">&nbsp;<b><?php print (trim(ucwords($emp_details[0]['name']))) ? trim(ucwords($emp_details[0]['name'])) : underLine(35); ?></b></td>
                <td class="text_10b" width="180" height="<?php if(trim(ucwords($emp_details[0]['street'])) || trim(ucwords($emp_details[0]['street2']))){print "5px";}else {print "14px";} ?>" valign="<?php if(trim(ucwords($emp_details[0]['street'])) || trim(ucwords($emp_details[0]['street2']))){print "bottom";}else {print "top";} ?>">&nbsp;<b><?php if(trim(ucwords($emp_details[0]['street'])) || trim(ucwords($emp_details[0]['street2']))){print trim(ucwords($emp_details[0]['street']))." ".trim(ucwords($emp_details[0]['street2']));}else {print underLine(35);} ?></b></td>
                <td class="text_10b" width="180" height="<?php print (trim(ucwords($emp_adderss))) ? "5px" : "14px"; ?>" valign="<?php print (trim(ucwords($emp_adderss))) ? "bottom" : "top"; ?>">&nbsp;<b><?php print (trim(ucwords($emp_adderss))) ? trim(ucwords($emp_adderss)) : underLine(35); ?></b></td>
            </tr>
            
            <tr align="left">
                <td class="text_10" style="color:#444444" width="180"><i>Occupation</i></td>
                <td class="text_10"  style="color:#444444" width="180"><i>Employer Name</i></td>
                <td class="text_10" style="color:#444444" width="180"><i>Street</i></td>
                <td class="text_10"  style="color:#444444" width="180"><i>City, State Zip</i></td>
            </tr>
            <tr valign="middle" height="25px" class="text_10" bgcolor="#c0c0c0">
                <td align="left" colspan="4"><b><?php print strtoupper('Emergency Contact Information'); ?></b></td>
            </tr>		
            <tr align="left">
                <td class="text_10b" width="180" height="<?php print ($patientDetails[0]['contact_relationship']) ? "5px" : "14px"; ?>" valign="<?php print ($patientDetails[0]['contact_relationship']) ? "bottom" : "top"; ?>">&nbsp;<?php print ($patientDetails[0]['contact_relationship']) ? $patientDetails[0]['contact_relationship'] : underLine(35); ?></td>				
                <td class="text_10b" width="180" height="<?php print ($patientDetails[0]['phone_contact']) ? "5px" : "14px"; ?>" valign="<?php print ($patientDetails[0]['phone_contact']) ? "bottom" : "top"; ?>">&nbsp;<b><?php print ($patientDetails[0]['phone_contact']) ? core_phone_format($patientDetails[0]['phone_contact']) : underLine(35); ?></b></td>
                <!-- <td class="text_10b" colspan="2" align="left" nowrap height="<?php //print (trim(ucwords($patientDetails[0]['patient_notes']))) ? "5px" : "14px"; ?>" valign="<?php //print (trim(ucwords($patientDetails[0]['patient_notes']))) ? "bottom" : "top"; ?>">&nbsp;<b><?php //print (trim(ucwords($patientDetails[0]['patient_notes']))) ? trim(ucwords($patientDetails[0]['patient_notes'])) : underLine(60); ?></b></td>				 -->
            </tr>		
            <tr align="left" >
                <td class="text_10" style="color:#444444" width="180"><i>Emergency Contact</i></td>
                <td class="text_10" width="180" style="color:#444444"><i>Emergency Phone</i></td>
                <!-- <td class="text_10" colspan="2" style="color:#444444" width="180"><i>Notes</i></td> -->
            </tr>        
            <?php        
            //---- Get Responsible Party Details if patient has -------
            $resp_name_arr = array();
            $resp_name_arr["LAST_NAME"] = $res_party_detail[0]['lname'];
            $resp_name_arr["FIRST_NAME"] = $res_party_detail[0]['fname'];
            $resp_name_arr["MIDDLE_NAME"] = $res_party_detail[0]['mname'];
            $resp_name = changeNameFormat($resp_name_arr);
            if(empty($resp_name)){
                $resp_name = 'Self';
            }
            $dob = $res_party_detail[0]['dob'];
            $dateOfBirth = changeDates($dob);
            list($resp_y,$resp_m,$resp_d) = explode('-',$dob);
            $resp_age = date('Y') - $resp_y;
            $city_address = $res_party_detail[0]['city'].', ';
            $city_address .= $res_party_detail[0]['state'].' ';
            $city_address .= $res_party_detail[0]['zip'];
            if(trim($city_address) == ","){
                $city_address = NULL;
            }
            ?>		
            <tr valign="middle" height="25px" class="text_10" bgcolor="#c0c0c0">
                <td align="left" width="220" colspan="4"><b><?php print strtoupper('Responsibility Party'); ?></b></td>
            </tr>			
            <tr align="left">
                <td class="text_10b" width="180" height="<?php print (trim(ucwords($resp_name))) ? "5px" : "14px"; ?>" valign="<?php print (trim(ucwords($resp_name))) ? "bottom" : "top"; ?>">&nbsp;<b><?php print (trim(ucwords($resp_name))) ? trim(ucwords($resp_name)) : underLine(35); ?></b></td>
                <td class="text_10b" width="180" height="<?php print (trim(ucwords($res_party_detail[$r]['relation']))) ? "5px" : "14px"; ?>" valign="<?php print (trim(ucwords($res_party_detail[$r]['relation']))) ? "bottom" : "top"; ?>">&nbsp;<b><?php print (trim(ucwords($res_party_detail[$r]['relation']))) ? trim(ucwords($res_party_detail[$r]['relation'])) : underLine(35); ?></b></td>
                <td class="text_10b" width="180" height="<?php print ($dateOfBirth != "--") ? "5px" : "14px"; ?>" valign="top">&nbsp;<b><?php print ($dateOfBirth != "--") ? $dateOfBirth.' ( '.$resp_age.' ) ' : underline(35); ?></b></td>
                <td class="text_10b" width="180" height="<?php print (trim(ucwords($res_party_detail[$r]['sex']))) ? "5px" : "14px"; ?>" valign="top">&nbsp;<b><?php print (trim(ucwords($res_party_detail[$r]['sex']))) ? trim(ucwords($res_party_detail[$r]['sex'])) : underLine(35); ?></b></td>
            </tr>		
            <tr align="left">
                <td class="text_10" style="color:#444444" width="180">&nbsp;<i>Name</i></td>
                <td class="text_10"style="color:#444444" width="180">&nbsp;<i>Relation</i></td>
                <td class="text_10" width="180" style="color:#444444">&nbsp;<i>DOB</i></td>
                <td class="text_10"style="color:#444444" width="180">&nbsp;<i>Sex</i></td>
            </tr>						
            <tr align="left">
                <td class="text_10b" width="180" height="<?php print ($res_party_detail[0]['ss']) ? "5px" : "14px"; ?>" valign="top">&nbsp;<b><?php print ($res_party_detail[0]['ss']) ? $res_party_detail[0]['ss'] : underLine(35); ?></b></td>
                <td class="text_10b" width="180" height="<?php print ($res_party_detail[0]['licence']) ? "5px" : "14px"; ?>" valign="top">&nbsp;<b><?php print ($res_party_detail[0]['licence']) ? $res_party_detail[0]['licence'] : underLine(35); ?></b></td>
                <td class="text_10b" width="180" height="<?php print (trim(ucwords($res_party_detail[0]['marital']))) ? "5px" : "14px"; ?>" valign="<?php print (trim(ucwords($res_party_detail[0]['marital']))) ? "bottom" : "top"; ?>">&nbsp;<b><?php print (trim(ucwords($res_party_detail[0]['marital']))) ? trim(ucwords($res_party_detail[0]['marital'])) : underLine(35); ?></b></td>
                <td class="text_10b" width="180" height="<?php print (trim(ucwords($res_party_detail[0]['work_ph']))) ? "5px" : "14px"; ?>" valign="<?php print (trim(ucwords($res_party_detail[0]['work_ph']))) ? "bottom" : "top"; ?>">&nbsp;<b><?php print (trim(ucwords($res_party_detail[0]['work_ph']))) ? trim(ucwords($res_party_detail[0]['work_ph'])) : underLine(35); ?></b></td>
            </tr>		
            <tr align="left">
                <td class="text_10" width="180" style="color:#444444"><i>Social Security#</i></td>
                <td class="text_10" width="180" style="color:#444444" align="left"><i>DL #</i></td>
                <td class="text_10" width="180" style="color:#444444" align="left"><i>Marital</i></td>
                <td class="text_10" width="180" style="color:#444444" align="left"><i>Work Phone#</i></td>
            </tr>	
            <tr align="left">
                <td class="text_10b" width="180" height="<?php  if($res_party_detail[0]['address'] || $res_party_detail[0]['address2']){print "5px";} else{ echo "14px";} ?>" valign="<?php  if($res_party_detail[0]['address'] || $res_party_detail[0]['address2']){print "bottom";} else{ echo "top";} ?>">&nbsp;<b><?php  if($res_party_detail[0]['address'] || $res_party_detail[0]['address2']){print $res_party_detail[0]['address']." ".$res_party_detail[0]['address2'];} else{ echo underLine(35);} ?></b></td>
                <td class="text_10b" width="180" height="<?php print ($city_address) ? "5px" : "14px"; ?>" valign="<?php print ($city_address) ? "bottom" : "top"; ?>">&nbsp;<b><?php print ($city_address) ? $city_address : underLine(35) ; ?></b></td>
                <td class="text_10b" width="180" height="<?php print (trim(ucwords($res_party_detail[0]['home_ph']))) ? "5px" : "14px"; ?>" valign="<?php print (trim(ucwords($res_party_detail[0]['home_ph']))) ? "bottom" : "top"; ?>">&nbsp;<b><?php print (trim(ucwords($res_party_detail[0]['home_ph']))) ? core_phone_format(trim(ucwords($res_party_detail[0]['home_ph']))) : underLine(35); ?></b></td>
                <td class="text_10b" width="180" height="<?php print (trim(ucwords($res_party_detail[0]['mobile']))) ? "5px" : "14px"; ?>" valign="<?php print (trim(ucwords($res_party_detail[0]['mobile']))) ? "bottom" : "top"; ?>">&nbsp;<b><?php print (trim(ucwords($res_party_detail[0]['mobile']))) ? core_phone_format(trim(ucwords($res_party_detail[0]['mobile']))) : underLine(35); ?></b></td>
            </tr>		
            <tr align="left">
                <td class="text_10" width="180" style="color:#444444"><i>Street</i></td>
                <td class="text_10" width="180" style="color:#444444"><i>City, State Zip</i></td>
                <td class="text_10" width="180" style="color:#444444"><i>Home Phone#</i></td>
                <td class="text_10" width="180" style="color:#444444"><i>Mobile#</i></td>
            </tr>	
            <?php
		
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
				$counter = 1;
				$controlText = "";
				$controlLabel = "";
				$process = 0;
				while($row = imw_fetch_assoc($rsCustomField)){
					$writeData = "";
						if($process == 0){
							$process = 1;
						?>
						
							<tr valign="middle" class="text_10" bgcolor="#c0c0c0">
								<td align="left" colspan="4"><b><?php print strtoupper('Miscellaneous'); ?></b></td>
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
						$controlText .= "<td height=\"$hightMisc\" valign=\"$topBottom\">&nbsp;".$writeData."</td>";
						$controlLabel .= "<td class=\"text_10\" style=\"color:#444444\"><i>".$cbkTextBoxLabel."</i></td>";															
						$counter++;
						if($counter == 5){
							echo $controlText .= "</tr>";
							echo $controlLabel .= "</tr>";
							$controlText = "";
							$controlLabel = "";
							$counter = 1;
						}
						
				}
				if($process == 1){
					if(($counter > 1 || $counter == 1) && $counter < 5){
						echo $controlText .= "</tr>";
						echo $controlLabel .= "</tr>";													
					}
				}
			}
		?>
	</table>	
		<?php 	
		//---  Insurance Details --------
		$scan_card_var = true;
		$scan_card_arr = array();
		$insProcess = 0;
		$qry = imw_query("select * from insurance_case where patient_id = '$pid' and case_status = 'Open' order by ins_case_type");
		$caseDetail = array();	
		while($rowCD = imw_fetch_assoc($qry)){	
			$caseDetail[] = $rowCD;
		}
		for($r=0;$r<count($caseDetail);$r++){		
		//if(count($caseDetail)>0){
			$ins_caseid = $caseDetail[$r]['ins_caseid'];
			$start_date = substr($caseDetail[$r]['start_date'],0,strpos($caseDetail[$r]['start_date'],' '));
			$end_date = substr($caseDetail[$r]['end_date'],0,strpos($caseDetail[$r]['end_date'],' '));
			$openDate = changeDates($start_date);
			if($end_date != '0000-00-00')
				$end_Date = changeDates($end_date);
			//--- Get Insurance Case Type -------
			$ins_case_type = $caseDetail[$r]['ins_case_type'];
			$qry = imw_query("select case_name from insurance_case_types where case_id = $ins_case_type");
			$caseType = array();	
			while($rowCT = imw_fetch_assoc($qry)){	
				$caseType[] = $rowCT;
			}
			//-- Get Responsible Party Name -----
			$resp_name_arr = array();
			$resp_name_arr["LAST_NAME"] = $res_party_detail[0]['lname'];
			$resp_name_arr["FIRST_NAME"] = $res_party_detail[0]['fname'];
			$resp_name_arr["MIDDLE_NAME"] = $res_party_detail[0]['mname'];	
			$resp_name = changeNameFormat($resp_name_arr);
			if(trim($resp_name) == ','){
				$resp_name = 'Self';
			}	
			$insType = array('primary','Secondary','Tertiary');
			$ins = 0;
				foreach($insType as $val){
					$qry = "select * from insurance_data where pid='$pid' and type='$val' 
							and actInsComp='1' and ins_caseid='$ins_caseid' and provider > 0
							and effective_date <= now() 
							and (expiration_date = '0000-00-00 00:00:00' 
							 or expiration_date > now())";
					$insPriDetails = array();	
					while($rowIT = imw_fetch_assoc($qry)){	
						$insPriDetails[] = $rowIT;
					}
					if((count($insPriDetails)>0 && $val == 'Tertiary') || ($val == 'primary' || $val == 'Secondary')){
						
						$priInsHeading = "$val insurance carrier details";
						$scan_card = '';
						if($insPriDetails[0]['scan_card'] != '' || $insPriDetails[0]['scan_card2'] != ''){
							$scan_card = '<span class="text_10b" >Ins. Card Scanned</span>';
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
				<table width="100%" cellpadding="0" cellspacing="0" border="<?php print $border; ?>" rules="none">			
			<?php
			}
			if($ins == 0){		
			?>
				<tr valign="middle" height="25px" bgcolor="#c0c0c0">
					<td class="text_10" colspan="4"><b>Insurance</b></td>
				</tr>						
				<tr> 	
					<td class="text_10b" align="left" width="180" height="<?php print ($caseDetail[$r]['ins_caseid']) ? "5px" : "14px"; ?>" valign="<?php print ($caseDetail[$r]['ins_caseid']) ? "bottom" : "top"; ?>">&nbsp;<b><?php print ($caseDetail[$r]['ins_caseid']) ? $caseDetail[$r]['ins_caseid'] : underLine(35); ?></b></td>
					<td class="text_10b" width="180" height="<?php print ($caseType[0]['case_name']) ? "5px" : "14px"; ?>" valign="<?php print ($caseType[0]['case_name']) ? "bottom" : "top"; ?>">&nbsp;<b><?php print ($caseType[0]['case_name']) ? $caseType[0]['case_name'] : underLine(35); ?></b></td>
					<td class="text_10b" width="180" height="<?php print ($resp_name) ? "5px" : "14px"; ?>" valign="<?php print ($resp_name) ? "bottom" : "top"; ?>">&nbsp;<b><?php print ($resp_name) ? $resp_name : underline(35); ?></b></td>		
					<td class="text_10b" width="180" height="<?php print (($openDate) ? "5px" : "14px") or (($end_Date) ? "5px" : "14px"); ?>" valign="<?php print (($openDate) ? "bottom" : "top") or (($end_Date) ? "bottom" : "top"); ?>">&nbsp;<b><?php print (($openDate) ? $openDate : underLine(20)) .' / '.(($end_Date) ? $end_Date : underLine(20)); ?></b></td>
				</tr>
				<tr>
					<td class="text_10" style="color:#444444"><i>Ins. Case#</i></td>
					<td class="text_10" style="color:#444444"><i>Ins. Case Type</i></td>
					<td class="text_10" style="color:#444444"><i>Responsible Party</i></td>
					<td class="text_10" style="color:#444444"><i>Case Open/End Date</i></td>
				</tr>
			<?php
				}
			$ins++;
			?>									
				<tr valign="middle" height="25px" bgcolor="#c0c0c0">
					<td class="text_10" colspan="4"><b><?php print strtoupper(trim($priInsHeading)).' '.strtoupper(trim($scan_card)); ?></b></td>
				</tr>			
				<?php
				$provider = $insPriDetails[0]['provider'];
				$qry = imw_query("select name from insurance_companies where id = '$provider'");
				$insDetails = array();	
				while($rowID = imw_fetch_assoc($qry)){	
					$insDetails[] = $rowID;
				}
				$insProviderName = "";
				$insProviderName = ucwords(strtolower($insDetails[0]['name']));
				
				?>
				<tr align="left"> 	
					<td class="text_10b" width="180" height="<?php print ($insProviderName) ? "5px" : "14px"; ?>" valign="<?php print ($insProviderName) ? "bottom" : "top"; ?>">&nbsp;<b><?php print ($insProviderName) ? $insProviderName : underLine(35); ?></b></td>
					<td class="text_10b" width="180" height="<?php print ($insPriDetails[0]['policy_number']) ? "5px" : "14px"; ?>" valign="<?php print ($insPriDetails[0]['policy_number']) ? "bottom" : "top"; ?>">&nbsp;<b><?php print ($insPriDetails[0]['policy_number']) ? $insPriDetails[0]['policy_number'] : underLine(35); ?></b></td>
					<td class="text_10b" width="180" height="<?php print ($insPriDetails[0]['group_number']) ? "5px" : "14px"; ?>" valign="<?php print ($insPriDetails[0]['group_number']) ? "bottom" : "top"; ?>">&nbsp;<b><?php print ($insPriDetails[0]['group_number']) ? $insPriDetails[0]['group_number'] : underLine(35); ?></b></td>		
					<td class="text_10b" width="180" height="<?php print ($insPriDetails[0]['plan_name']) ? "5px" : "14px"; ?>" valign="<?php print ($insPriDetails[0]['plan_name']) ? "bottom" : "top"; ?>">&nbsp;<b><?php print ($insPriDetails[0]['plan_name']) ? $insPriDetails[0]['plan_name'] : underLine(35); ?></b></td>
				</tr>
				
				<tr>
					<td class="text_10" style="color:#444444"><i>Ins. Carrier</i></td>
					<td class="text_10" style="color:#444444"><i>Policy#</i></td>
					<td class="text_10" style="color:#444444"><i>Group#</i></td>
					<td class="text_10" style="color:#444444"><i>Plan Name</i></td>
				</tr>		
				<?php
				$actDate = substr($insPriDetails[0]['effective_date'],0,strpos($insPriDetails[0]['effective_date'],' '));
				$activeDate = '';
				if($actDate != '0000-00-00')
					$activeDate = changeDates($actDate);
				$expDate = substr($insPriDetails[0]['expiration_date'],0,strpos($insPriDetails[0]['expiration_date'],' '));
				$expireDate = '';
				if($expDate != '0000-00-00')
					$expireDate = changeDates($expDate);
				$copay = '$'.number_format($insPriDetails[0]['copay'],2);
				?>
				<tr align="left"> 	
					<td class="text_10b" width="180" height="<?php print ($activeDate != "--") ? "5px" : "14px"; ?>" valign="<?php print ($activeDate != "--") ? "bottom" : "top"; ?>">&nbsp;<b><?php print ($activeDate != "--") ? $activeDate : underLine(35); ?></b></td>
					<td class="text_10b" width="180" height="<?php print ($patientDetails[0]["ss"] != "--") ? "5px" : "14px"; ?>" valign="<?php print ($patientDetails[0]["ss"] != "--") ? "bottom" : "top"; ?>">&nbsp;<b><?php print ($patientDetails[0]["ss"] != "--") ? $patientDetails[0]["ss"] : underLine(35);//print $expireDate; ?></b></td>
					<td class="text_10b" width="180" height="<?php print ($copay != "$0.00") ? "5px" : "14px"; ?>" valign="<?php print ($copay != "$0.00") ? "bottom" : "top"; ?>">&nbsp;<b><?php print ($copay != "$0.00") ? $copay : underLine(35); ?></b></td>		
					<td class="text_10b" width="180" height="<?php print ($insPriDetails[0]['referal_required']) ? "5px" : "14px"; ?>" valign="<?php print ($insPriDetails[0]['referal_required']) ? "bottom" : "top"; ?>">&nbsp;<b><?php print ($insPriDetails[0]['referal_required']) ? $insPriDetails[0]['referal_required'] : underLine(35); ?></b></td>
				</tr>
				
				<tr>
					<td class="text_10" style="color:#444444"><i>Activaton Date</i></td>
					<td class="text_10" style="color:#444444"><i>Social Security#</i></td>
					<td class="text_10" style="color:#444444"><i>Copay</i></td>
					<td class="text_10" style="color:#444444"><i>Referal Required</i></td>
				</tr>	
				<?php
				$subscriberNameArr = array();
				$subscriberNameArr["LAST_NAME"] = $insPriDetails[0]['subscriber_lname'];
				$subscriberNameArr["FIRST_NAME"] = $insPriDetails[0]['subscriber_fname'];
				$subscriberNameArr["MIDDLE_NAME"] = $insPriDetails[0]['subscriber_mname'];
				$subscriberName = changeNameFormat($subscriberNameArr);
				$subscriber_DOB = NULL;
				if($insPriDetails[0]['subscriber_DOB'] != '0000-00-00')
					$subscriber_DOB = changeDates($insPriDetails[0]['subscriber_DOB']);
	
				$strToShowRelation = $insPriDetails[0]['subscriber_relationship'];
				if(strtolower($insPriDetails[0]['subscriber_relationship']) == "doughter"){
					$strToShowRelation = "Daughter";
				}
				?>
				<tr align="left"> 
					<td class="text_10b" width="180" height="<?php print (ucwords($strToShowRelation)) ? "5px" : "14px"; ?>" valign="<?php print (ucwords($strToShowRelation)) ? "bottom" : "top"; ?>">&nbsp;<b><?php print (ucwords($strToShowRelation)) ? ucwords($strToShowRelation) : underLine(35); ?></b></td>
					<td class="text_10b" width="180" height="<?php print (trim($subscriberName)) ? "5px" : "14px"; ?>" valign="<?php print (trim($subscriberName)) ? "bottom" : "top"; ?>">&nbsp;<b><?php print (trim($subscriberName)) ? trim($subscriberName) : underLine(35); ?></b></td>
					<td class="text_10b" width="180" height="<?php print ($subscriber_DOB != "--") ? "5px" : "14px"; ?>" valign="<?php print ($subscriber_DOB != "--") ? "bottom" : "top"; ?>">&nbsp;<b><?php print ($subscriber_DOB != "--") ? $subscriber_DOB : underLine(35); ?></b></td>		
					<td class="text_10b" width="180" height="<?php print ($insPriDetails[0]['subscriber_ss']) ? "5px" : "14px"; ?>" valign="<?php print ($insPriDetails[0]['subscriber_ss']) ? "bottom" : "top"; ?>">&nbsp;<b><?php print (($insPriDetails[0]['subscriber_ss']) ? $insPriDetails[0]['subscriber_ss'] : underLine(35)); ?></b></td>
				</tr>			
				<tr>
					<td class="text_10" style="color:#444444"><i>Subscriber Relation</i></td>
					<td class="text_10" style="color:#444444"><i>Subscriber Name</i></td>
					<td class="text_10" style="color:#444444"><i>DOB</i></td>
					<td class="text_10" style="color:#444444"><i>Social Security#</i></td>
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
					<td class="text_10b" width="180" height="<?php print ($insPriDetails[0]['subscriber_street']) ? "5px" : "14px"; ?>" valign="<?php print ($insPriDetails[0]['subscriber_street']) ? "bottom" : "top"; ?>">&nbsp;<b><?php print ($insPriDetails[0]['subscriber_street']) ? $insPriDetails[0]['subscriber_street'] : underLine(35); ?></b></td>
					<td class="text_10b" width="180" height="<?php print (trim($subscriberAddress)) ? "5px" : "14px"; ?>" valign="<?php print (trim($subscriberAddress)) ? "bottom" : "top"; ?>">&nbsp;<b><?php print (trim($subscriberAddress)) ? trim($subscriberAddress) : underLine(35); ?></b></td>
					<td class="text_10b" width="180">&nbsp;<b>Yes</b></td>		
					<td class="text_10b" width="180">&nbsp;<b>Yes</b></td>
				</tr>			
				<tr>
					<td class="text_10" style="color:#444444"><i>Street</i></td>
					<td class="text_10" style="color:#444444"><i>City, State Zip</i></td>
					<td class="text_10" style="color:#444444"><i>Payment Authorized</i></td>
					<td class="text_10" style="color:#444444"><i>Signature on File</i></td>
				</tr>				
					<?php
					if($insPriDetails[0]['referal_required'] == 'Yes'){
						$id = $insPriDetails[0]['id'];
						$qry = imw_query("select * from patient_reff where ins_data_id = '$id' and (no_of_reffs > 0 or now() between effective_date and end_date)");
						$reffDetails = array();	
						while($rowRD = imw_fetch_assoc($qry)){	
							$reffDetails[] = $rowRD;
						}
					?>			
					<tr valign="middle" height="25px" bgcolor="#c0c0c0">
						<td class="text_10" colspan="4"><b><?php print strtoupper('Referral Required '); ?></b></td>
					</tr>		
					<?php
					$reff_date = NULL;
					if($reffDetails[0]['reff_date'] != '0000-00-00')
						$reff_date = changeDates($reffDetails[0]['reff_date']);
						
					$reff_phy_id = $reffDetails[0]['reff_phy_id'];
					//--- get Reffering Physician Name --------
					$qry = imw_query("select concat(LastName,', ',FirstName) as name,MiddleName from refferphysician where physician_Reffer_id = '$reff_phy_id'");
					$reffPhyDetails = array();	
					while($rowRPD = imw_fetch_assoc($qry)){	
						$reffPhyDetails[] = $rowRPD;
					}
					$reffPhyName = $reffPhyDetails[0]['name'].' ';
					$reffPhyName .= $reffPhyDetails[0]['MiddleName'];
					?>
					<tr align="left"> 	
						<td class="text_10b" width="180" height="<?php print (trim($reffPhyName)) ? "5px" : "14px"; ?>" valign="<?php print (trim($reffPhyName)) ? "bottom" : "top"; ?>">&nbsp;<b><?php print (trim($reffPhyName)) ? trim($reffPhyName) : underLine(35); ?></b></td>
						<td class="text_10b" width="180" height="<?php print (trim($reffDetails[0]['reffral_no'])) ? "5px" : "14px"; ?>" valign="<?php print (trim($reffDetails[0]['reffral_no'])) ? "bottom" : "top"; ?>">&nbsp;<b><?php print (trim($reffDetails[0]['reffral_no'])) ? trim($reffDetails[0]['reffral_no']) : underLine(35); ?></b></td>
						<td class="text_10b" width="180" height="<?php print ($reff_date) ? "5px" : "14px"; ?>" valign="<?php print ($reff_date) ? "bottom" : "top"; ?>">&nbsp;<b><?php print ($reff_date) ? $reff_date : underLine(35); ?></b></td>		
						<td class="text_10b" width="180" height="<?php  if($reffDetails[0]['no_of_reffs'] || $reffDetails[0]['reff_used']) {print "5px";} else{ echo "14px";} ?>" valign="<?php  if($reffDetails[0]['no_of_reffs'] || $reffDetails[0]['reff_used']) {print "bottom";} else{ echo "top";} ?>">&nbsp;<b><?php  if($reffDetails[0]['no_of_reffs'] || $reffDetails[0]['reff_used']) {print $reffDetails[0]['no_of_reffs'] + $reffDetails[0]['reff_used'];} else{ echo underLine(35);} ?></b></td>
					</tr>		
					<tr>
						<td class="text_10" style="color:#444444" width="180"><i>Referring Dr.</i></td>
						<td class="text_10" style="color:#444444" width="180"><i>Ref #</i></td>
						<td class="text_10" style="color:#444444" width="180"><i>Ref Date</i></td>
						<td class="text_10" style="color:#444444" width="180"><i># of Visits</i></td>
					</tr>		
					<?php
					$effective_date = NULL;
					if($reffDetails[0]['effective_date'] != '0000-00-00')
						$effective_date = changeDates($reffDetails[0]['effective_date']);
					
					$end_date = NULL;
					if($reffDetails[0]['end_date'] != '0000-00-00')
						$end_date = changeDates($reffDetails[0]['end_date']);
					?>
					<tr align="left"> 	
						<td class="text_10b" width="220" height="<?php print ($effective_date) ? "5px" : "14px"; ?>" valign="<?php print ($effective_date) ? "bottom" : "top"; ?>">&nbsp;<b><?php print ($effective_date) ? $effective_date : underLine(35); ?></b></td>
						<td class="text_10b" width="180" height="<?php print (trim($end_date)) ? "5px" : "14px"; ?>" valign="<?php print (trim($end_date)) ? "bottom" : "top"; ?>">&nbsp;<b><?php print (trim($end_date)) ? trim($end_date) : underLine(35); ?></b></td>
						<td class="text_10b" colspan="2" height="<?php print ($reffDetails[0]['note']) ? "5px" : "14px"; ?>" valign="<?php print ($reffDetails[0]['note']) ? "bottom" : "top"; ?>">&nbsp;<b><?php print ($reffDetails[0]['note']) ? $reffDetails[0]['note'] : underLine(35); ?></b></td>		
					</tr>
				
					<tr>
						<td class="text_10" style="color:#444444"><i>Start Date</i></td>
						<td class="text_10" style="color:#444444"><i>End date</i></td>
						<td class="text_10" colspan="2" style="color:#444444"><i>Notes</i></td>
					</tr>
					<?php
					}
				}	
			}
		//}
		}
		if($insProcess > 0){
		?>
		<tr valign="middle"  class="text_10" bgcolor="#c0c0c0">
				<td align="left" colspan="4"><b><?php print strtoupper('Employer'); ?></b></td>
			</tr>	
		
			<tr align="left">				
				<td class="text_10b" width="180" height="<?php print (trim(ucwords($emp_details[0]['name']))) ? "5px" : "14px"; ?>" valign="<?php print (trim(ucwords($emp_details[0]['name']))) ? "bottom" : "top"; ?>">&nbsp;<b><?php print (trim(ucwords($emp_details[0]['name']))) ? trim(ucwords($emp_details[0]['name'])) : underLine(35); ?></b></td>
				<td class="text_10b" width="180" height="<?php if(trim(ucwords($emp_details[0]['street'])) || trim(ucwords($emp_details[0]['street2']))){print "5px";}else {print "14px";} ?>" valign="<?php if(trim(ucwords($emp_details[0]['street'])) || trim(ucwords($emp_details[0]['street2']))){print "bottom";}else {print "top";} ?>">&nbsp;<b><?php if(trim(ucwords($emp_details[0]['street'])) || trim(ucwords($emp_details[0]['street2']))){print trim(ucwords($emp_details[0]['street']))." ".trim(ucwords($emp_details[0]['street2']));}else {print underLine(50);} ?></b></td>
				<td class="text_10b" width="180" height="<?php print (trim(ucwords($emp_adderss))) ? "5px" : "14px"; ?>" valign="<?php print (trim(ucwords($emp_adderss))) ? "bottom" : "top"; ?>">&nbsp;<b><?php print (trim(ucwords($emp_adderss))) ? trim(ucwords($emp_adderss)) : underLine(50); ?></b></td>
			</tr>			
			<tr align="left">				
				<td class="text_10"  style="color:#444444" width="180"><i>Employer Name</i></td>
				<td class="text_10" style="color:#444444" width="180"><i>Street</i></td>
				<td class="text_10"  style="color:#444444" width="180"><i>City, State Zip</i></td>
			</tr>
			</table>
		<?php 
		}
//---  Insurance Details End --------
		//if(isset($_REQUEST["face_sheet_scan"]) && $_REQUEST["face_sheet_scan"] == 1){
			//---  Show Scan Card Documents --------
			$pageHeight = 900;
			$imageSizeArr=array();
			$ins_scan_arr=array();
			$showHeadingScanDoc=false;
			$pageCount=0;
			if(count($scan_card_arr)>0){
				foreach($scan_card_arr as $key => $ins_scan_arr){
					foreach($ins_scan_arr as $scan_key => $scan_val){
						$img_real_path = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH').$scan_val;
						if(file_exists($img_real_path) != '' && is_dir($img_real_path) == ''){
							$showHeadingScanDoc=true;	
						}
						$ImageSize = getimagesize($img_real_path);
						$imageSizeArr[] = $ImageSize[1];
						$pageCount ++;
					}
				}
				if($showHeadingScanDoc==true) {
					print '<newpage>';
				}
			}
			$i=1;
			if(count($scan_card_arr)>0){
				foreach($scan_card_arr as $key => $ins_scan_arr){
					?>			
					<table width="100%" border="<?php print $border; ?>" rules="none" cellpadding="1" cellspacing="1">
					<?php
					if(count($ins_scan_arr) > 0 && $showHeadingScanDoc==true){
					?>
						<tr valign="middle" height="25px" class="text_10" bgcolor="#c0c0c0">
							<td class="text_10" colspan="2"><b><?php print strtoupper($key.' scan document'); ?></b></td>
						</tr>
					</table>
					<?php			
						foreach($ins_scan_arr as $scan_key => $scan_val){				
							$img_real_path = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH').$scan_val;
							if(file_exists($img_real_path) != '' && is_dir($img_real_path) == ''){
								$img_name = substr($scan_val,strrpos($scan_val,'/')+1);
								$patient_img[$key.'-'.$scan_key] = $img_name;
								$path_root = pathinfo($img_real_path);
								@copy($img_real_path,$path_root['dirname'].'/tmp/'.$img_name);
								$img_path = $img_name;
								$priImageSize = getimagesize($img_real_path);
								$newSize = NULL;
								if($priImageSize[0] > 395 && $priImageSize[1] < 840){
									$newSize = imageResize(680,400,710);						
									$image_view = '<img src="'.$img_path.'" '.$newSize.'>';
									$priImageSize[0] = 710;
								}		
								elseif($priImageSize[0] > 700){
									$newSize = newImageResize($img_real_path,700);
									$image_view = '<img src="'.$img_path.'" '.$newSize.'>';												
									$priImageSize[1] =700;
									
								}					
								elseif($priImageSize[1] > 840){
									$newSize = newImageResize($img_real_path,700,800);
									$image_view = '<img src="'.$img_path.'" '.$newSize.'>';												
									$priImageSize[1] = 800;
									
								}								
								else{					
									$newSize = $priImageSize[3];
									$image_view = '<img src="'.$img_path.'" '.$newSize.'>';
									//echo $image_view; die;
								}							
								if($priImageSize[1] > 800 ){					
									echo '</newpage><newpage>';												
								}								
								?>						
								<table width="100%" cellpadding="0" cellspacing="0" border="<?php print $border; ?>">
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
									echo '</newpage><newpage>';	
									$pageHeight = 900;
								}
													
								$i++;				
							}
						}
					}		
				}
			}
		//}
		if($int_appt == $int_tot_appt){
		?>
		<?php 
		}

//START
		$arrMEDHXGenralHealth = array();
		
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "any_conditions_you","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "High Blood Pressure");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "any_conditions_you","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Heart Problem");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "any_conditions_you","Filed_Label_Val"=> '7',"Filed_Label_Og_Val"=> "Arthritis");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "any_conditions_you","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Lung Problems");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "any_conditions_you","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Stroke");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "any_conditions_you","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "Thyroid Problems");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "any_conditions_you","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Diabetes");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "any_conditions_you","Filed_Label_Val"=> '8',"Filed_Label_Og_Val"=> "Ulcers");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "any_conditions_you","Filed_Label_Val"=> '13',"Filed_Label_Og_Val"=> "LDL");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "any_conditions_you","Filed_Label_Val"=> '14',"Filed_Label_Og_Val"=> "Cancer");
		
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "any_conditions_relative","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Relative High Blood Pressure :");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "any_conditions_relative","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Relative Heart Problem :");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "any_conditions_relative","Filed_Label_Val"=> '7',"Filed_Label_Og_Val"=> "Relative Arthritis :");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "any_conditions_relative","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Relative Lung Problems :");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "any_conditions_relative","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Relative Stroke :");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "any_conditions_relative","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "Relative Thyroid Problems :");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "any_conditions_relative","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Relative Diabetes :");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "any_conditions_relative","Filed_Label_Val"=> '8',"Filed_Label_Og_Val"=> "Relative Ulcers :");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "any_conditions_relative","Filed_Label_Val"=> '13',"Filed_Label_Og_Val"=> "Relative LDL :");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "any_conditions_relative","Filed_Label_Val"=> '14',"Filed_Label_Og_Val"=> "Relative Cancer :");
		
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "any_conditions_others_both","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "You Other");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "any_conditions_others_both","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Relative Other");
		
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_const","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Fever");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_const","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Weight Loss");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_const","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Rash");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_const","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Skin Disease");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_const","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Fatigue");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_head","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Sinus Infection");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_head","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Post Nasal Drips");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_head","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Runny Nose");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_head","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Dry Mouth");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_head","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Deafness");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_resp","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Cough");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_resp","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Bronchitis");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_resp","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Shortness of Breath");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_resp","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Asthma");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_resp","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Emphysema");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_resp","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "COPD");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_resp","Filed_Label_Val"=> '7',"Filed_Label_Og_Val"=> "TB");

		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_card","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Chest Pain");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_card","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Congestive Heart Failure");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_card","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Irregular Heart beat");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_card","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Shortness of Breath");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_card","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "High Blood Pressure");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_card","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "Low Blood Pressure");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_card","Filed_Label_Val"=> '7',"Filed_Label_Og_Val"=> "Pacemaker/defibrillator");

		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_gastro","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Vomiting");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_gastro","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Ulcers");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_gastro","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Diarrhea");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_gastro","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Bloody Stools");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_gastro","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Hepatitis");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_gastro","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "Jaundice");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_gastro","Filed_Label_Val"=> '7',"Filed_Label_Og_Val"=> "Constipation");

		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_genit","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Genital Ulcers");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_genit","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Discharge");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_genit","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Kidney Stones");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_genit","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Blood in Urine");

		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_aller","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Seasonal Allergies");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_aller","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Hay Fever");

		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Headache");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Migraines");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Paralysis Fever");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Joint Ache");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Seizures");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "Numbness");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '7',"Filed_Label_Og_Val"=> "Faints");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '8',"Filed_Label_Og_Val"=> "Stroke");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '9',"Filed_Label_Og_Val"=> "Multiple Sclerosis");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '10',"Filed_Label_Og_Val"=> "Alzheimer's Disease");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '11',"Filed_Label_Og_Val"=> "Parkinson's Disease");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_neuro","Filed_Label_Val"=> '12',"Filed_Label_Og_Val"=> "Dementia");

		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_intgmntr","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Rashes");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_intgmntr","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Wounds");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_intgmntr","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Breast Lumps");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_intgmntr","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Eczema");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_intgmntr","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Dermatitis");

		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_psychiatry","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Depression");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_psychiatry","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Anxiety");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_psychiatry","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Paranoia");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_psychiatry","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Sleep Patterns");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_psychiatry","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Mental and/or emotional factors");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_psychiatry","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "Alzheimer's Disease");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_psychiatry","Filed_Label_Val"=> '7',"Filed_Label_Og_Val"=> "Parkinson's disease");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_psychiatry","Filed_Label_Val"=> '8',"Filed_Label_Og_Val"=> "Memory Loss");

		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_blood_lymph","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Anemia");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_blood_lymph","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Blood Transfusions");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_blood_lymph","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Excessive Bleeding");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_blood_lymph","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Purpura");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_blood_lymph","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Infection");

		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_musculoskeletal","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Pain");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_musculoskeletal","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Joint Ache");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_musculoskeletal","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Stiffness");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_musculoskeletal","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Swelling");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_musculoskeletal","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Paralysis Fever");

		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_endocrine","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Mood Swings");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_endocrine","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Constipation");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_endocrine","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Polydipsia");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_endocrine","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Hypothyroidism");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_endocrine","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Hyperthyroidism");

		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_eye","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Vision loss");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_eye","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Eye pain");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_eye","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Double vision");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "review_eye","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Headache");

		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '1',"Filed_Label_Og_Val"=> "Negative for constitutional");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '2',"Filed_Label_Og_Val"=> "Negative for ear, nose, mouth & throat");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '3',"Filed_Label_Og_Val"=> "Negative for respiratory");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '4',"Filed_Label_Og_Val"=> "Negative for cardiovascular");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '5',"Filed_Label_Og_Val"=> "Negative for gastrointenstinal");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '6',"Filed_Label_Og_Val"=> "Negative for genitourinary");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '7',"Filed_Label_Og_Val"=> "Negative for allergic/immunologic");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '8',"Filed_Label_Og_Val"=> "Negative for neurological");

		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '9',"Filed_Label_Og_Val"=> "Negative for integumentary");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '10',"Filed_Label_Og_Val"=> "Negative for psychiatry");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '11',"Filed_Label_Og_Val"=> "Negative for hemotologic/lymphatic");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '12',"Filed_Label_Og_Val"=> "Negative for musculoskeletal");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '13',"Filed_Label_Og_Val"=> "Negative for endocrine");
		$arrMEDHXGenralHealth [] = array("Filed_Label"=> "negChkBx","Filed_Label_Val"=> '14',"Filed_Label_Og_Val"=> "Negative for eyes");
		//
		$cpr = new CmnFunc($pid);
		
		$qryGetGenHealth = "select * from general_medicine where patient_id=$pid";
		$rsGetGenHealth = imw_query($qryGetGenHealth);
		$arrRsGetGenHealth = imw_fetch_assoc($rsGetGenHealth);

		$medDoctorValueGH=$genMedCommentsValueGH=$anyConditionYouValueGH=$relDescHighBpValueGH=
		$relDescHeartProbValueGH=$relDescLungProbValueGH=$relDescStrokeProbValueGH=$relDescThyroidProbValueGH=
		$relDescArthritisProbValueGH=$relDescUlcersProbValueGH=$relDescLDLValueGH=$relDescCancerProbValueGH=
		$descValueGH=$descRValueGH=$anyConditionOtherValueGH=$ghRelDescOthersValueGH=$anyReviewConstValueGH=
		$reviewHeadValueGH=$reviewConstOtherValueGH=$reviewHeadOtherValueGH=$reviewRespValueGH=$reviewCardValueGH=
		$reviewRespOtherValueGH=$reviewCardOtherValueGH=$reviewGastroValueGH=$reviewGenitValueGH=
		$reviewGastroOtherValueGH=$reviewGenitOtherValueGH=$reviewAllerValueGH=$reviewNeuroValueGH=
		$reviewAllerOtherValueGH=$reviewNeuroOtherValueGH=$descValueDHB=$descValueDA=$descValueDLP=
		$descValueDS=$descValueDTP=$descValueDU=$descValueDC=$descValueDHP=$descValueDGPP=$descValueDLDL=
		$descValueDV=$anyConditionYouValueInDb=$subArthritisProbValueGH='';
		
		foreach ((array)$arrRsGetGenHealth as $key => $value) {	
			switch ($key):	
				case "med_doctor":																							
					$medDoctorValueGH = $value;
					break;		
				case "any_conditions_you":										
					$orignalValue = getOrignalValComa($value,$value,$arrMEDHXGenralHealth,$key);										
					$orignalValue = explode("~~~~",$orignalValue);										
					$anyConditionYouValueGH = $orignalValue[0];	
					$anyConditionYouValueInDb = $value;
				break;		
				case "any_conditions_relative":										
					$orignalValue = getOrignalValComa($value,$value,$arrMEDHXGenralHealth,$key);										
					$orignalValue = explode("~~~~",$orignalValue);										
					$anyConditionRelativeValueGH = $orignalValue[0];
					break;
				case "any_conditions_others_both":										
					$orignalValue = getOrignalValComa($value,$value,$arrMEDHXGenralHealth,$key);										
					$orignalValue = explode("~~~~",$orignalValue);										
					$anyConditionOtherBothValueGH = $orignalValue[0];
					break;
				case "any_conditions_others":																					
					$anyConditionOtherValueGH = isDate($value);
					break;
				case "review_sys":							
					$review_sys = $value;
					if(!empty($review_sys)){
						$ar_review_sys = json_decode($review_sys, true);
						$ar_tmp = array('review_intgmntr',	'review_psychiatry', 'review_blood_lymph',
									'review_musculoskeletal','review_endocrine','review_eye');
						foreach($ar_tmp as $k => $v){
							$value = $ar_review_sys[$v];
							if(!empty($value)){
								$orignalValue = getOrignalValComa($value,$value,$arrMEDHXGenralHealth,$v);										
								$orignalValue = explode("~~~~",$orignalValue);
								$vgh = $v."GH";	
								$$vgh = "".$orignalValue[0];
							}
							$vother = $v."_others";
							if(!empty($ar_review_sys[$vother])){
								$vothr = $v."OtherGH";
								$$vothr = $ar_review_sys[$vother];
							}
						}
					}
					break;		
				case "review_const":										
					$orignalValue = getOrignalValComa($value,$value,$arrMEDHXGenralHealth,$key);										
					$orignalValue = explode("~~~~",$orignalValue);										
					$anyReviewConstValueGH = $orignalValue[0];
					break;
				case "review_const_others":										
					$reviewConstOtherValueGH = isDate($value);
					break;
				case "review_head":										
					$orignalValue = getOrignalValComa($value,$value,$arrMEDHXGenralHealth,$key);										
					$orignalValue = explode("~~~~",$orignalValue);										
					$reviewHeadValueGH = $orignalValue[0];
					break;
				case "review_head_others":										
					$reviewHeadOtherValueGH = isDate($value);
					break;
				case "review_resp":										
					$orignalValue = getOrignalValComa($value,$value,$arrMEDHXGenralHealth,$key);										
					$orignalValue = explode("~~~~",$orignalValue);										
					$reviewRespValueGH = $orignalValue[0];
					break;	
				case "review_resp_others":										
					$reviewRespOtherValueGH = isDate($value);
					break;	
				case "review_card":										
					$orignalValue = getOrignalValComa($value,$value,$arrMEDHXGenralHealth,$key);										
					$orignalValue = explode("~~~~",$orignalValue);										
					$reviewCardValueGH = $orignalValue[0];
					break;	
				case "review_card_others":										
					$reviewCardOtherValueGH = isDate($value);
					break;	
				case "review_gastro":										
					$orignalValue = getOrignalValComa($value,$value,$arrMEDHXGenralHealth,$key);										
					$orignalValue = explode("~~~~",$orignalValue);										
					$reviewGastroValueGH = $orignalValue[0];
					break;	
				case "review_gastro_others":										
					$reviewGastroOtherValueGH = isDate($value);
					break;
				case "review_genit":										
					$orignalValue = getOrignalValComa($value,$value,$arrMEDHXGenralHealth,$key);										
					$orignalValue = explode("~~~~",$orignalValue);										
					$reviewGenitValueGH = $orignalValue[0];
					break;	
				case "review_genit_others":										
					$reviewGenitOtherValueGH = isDate($value);
					break;		
				case "review_aller":										
					$orignalValue = getOrignalValComa($value,$value,$arrMEDHXGenralHealth,$key);										
					$orignalValue = explode("~~~~",$orignalValue);										
					$reviewAllerValueGH = $orignalValue[0];
					break;	
				case "review_aller_others":										
					$reviewAllerOtherValueGH = isDate($value);
					break;	
				case "review_neuro":										
					$orignalValue = getOrignalValComa($value,$value,$arrMEDHXGenralHealth,$key);										
					$orignalValue = explode("~~~~",$orignalValue);										
					$reviewNeuroValueGH = $orignalValue[0];
					break;	
				case "review_neuro_others":										
					$reviewNeuroOtherValueGH = isDate($value);
					break;
				case "desc_u":					
					$descValueGH = isDate($value);
					break;
				case "desc_r":					
					$descRValueGH = isDate($value);
					break;		
				case "genMedComments":										
					$genMedCommentsValueGH = isDate($value);
					break;
				case "relDescHighBp":
					
					$relDescHighBpValueGH =	($value) ? "Relative High Blood Pressure :" : "";											
					$relDescHighBpValueGH .= $value;
					break;
				case "relDescHeartProb":				
					$relDescHeartProbValueGH = ($value) ? "Relative Heart Problem :" : "";																			
					$relDescHeartProbValueGH .= $value;
					break;	
				case "relDescLungProb":	
					$relDescLungProbValueGH = ($value) ? "Relative Lung Problems :" : "";																					
					$relDescLungProbValueGH .= $value;
					break;						
				case "relDescStrokeProb":	
					$relDescStrokeProbValueGH = ($value) ? "Relative Stroke :" : "";																				
					$relDescStrokeProbValueGH .= $value;
					break;	
				case "relDescThyroidProb":
					$relDescThyroidProbValueGH = ($value) ? "Relative Thyroid Problems :" : "";																						
					$relDescThyroidProbValueGH .= $value;
					break;	
				case "relDescArthritisProb":		
					$relDescArthritisProbValueGH = ($value) ? "Relative Arthritis :" : "";																				
					$relDescArthritisProbValueGH .= $value;
					break;
				case "sub_conditions_you":		
					$subArthritisProbValueGH = isDate($value);																				
					break;		
				case "relDescUlcersProb":	
					$relDescUlcersProbValueGH = ($value) ? "Relative Ulcers :" : "";																				
					$relDescUlcersProbValueGH .= $value;
					break;
				case "relDescLDL":	
					$relDescLDLValueGH = ($value) ? "Relative LDL :" : ""; 																				
					$relDescLDLValueGH .= $value;
					break;
				case "relDescCancerProb":	
					$relDescCancerProbValueGH = ($value) ? "Relative Cancer :" : "";																				
					$relDescCancerProbValueGH .= $value;
					break;

				case "ghRelDescOthers":																					
					$ghRelDescOthersValueGH = $value;
					break;	
				case "negChkBx":										
					$orignalValue = getOrignalValComa($value,$value,$arrMEDHXGenralHealth,$key);										
					$orignalValue = explode("~~~~",$orignalValue);										
					$negChkBxValueGH = $orignalValue[0];
					break;
					
				case "desc_high_bp":					
					$descValueDHB = isDate($value);
					break;
				case "desc_arthrities":					
					$descValueDA = isDate($value);
					break;
				case "desc_lung_problem":					
					$descValueDLP = isDate($value);
					break;
				case "desc_stroke":					
					$descValueDS = isDate($value);
					break;
				case "desc_thyroid_problems":					
					$descValueDTP = isDate($value);
					break;
				case "desc_ulcers":					
					$descValueDU = isDate($value);
					break;
				case "desc_cancer":					
					$descValueDC = isDate($value);
					break;
				case "desc_heart_problem":					
					$descValueDHP = isDate($value);
					break;
				case "desc_genPreviousProblem":					
					$descValueDGPP = isDate($value);
					break;
				case "desc_LDL":					
					$descValueDLDL = isDate($value);
					break;
				case "diabetes_values":					
					$descValueDV = isDate($value);
					break;
																			
																			
			endswitch;
		}
		//Get Medication
		$getMedication = "select * from lists where pid='".$pid."' and (type='1' or type='4') order by id";
		$rsGetMedication = imw_query($getMedication);
		//Get Allergies
		$getAllergies = "select * from lists where pid='".$pid."' and type in(3,7) order by id";
		$rsGetAllergies = imw_query($getAllergies);
			
		//Get Surgeries
		$getSurgeries = "select * from lists where pid='".$pid."' and (type='5' or type='6') order by id";
		$rsGetSurgeries = imw_query($getSurgeries);	
		
		if (imw_num_rows($rsGetGenHealth) > 0 || imw_num_rows($rsGetMedication) > 0 || imw_num_rows($rsGetAllergies) > 0 || imw_num_rows($rsGetSurgeries) > 0) {
			if($showHeadingScanDoc==true) {echo '</newpage><newpage>'; }
		?>
		<table width="100%" cellpadding="2" cellspacing="0" border="<?php print $border; ?>" rules="none">	
			<tr valign="middle" height="25px" bgcolor="#c0c0c0">
				<td colspan="4" class="text_10" >
					<b>Medical History</b>
				</td>		
			</tr>
		</table>
        <?php
		}
		?>
<?php  if(
			(!empty($medDoctorValueGH)) || 	
			(!empty($genMedCommentsValueGH)) || 	
			(!empty($anyConditionYouValueGH)) || 
			(!empty($relDescHighBpValueGH)) || 
			(!empty($relDescHeartProbValueGH)) || 
			(!empty($relDescLungProbValueGH)) || 
			(!empty($relDescStrokeProbValueGH)) || 
			(!empty($relDescThyroidProbValueGH)) || 
			(!empty($relDescArthritisProbValueGH)) || 
			(!empty($subArthritisProbValueGH)) || 
			(!empty($relDescUlcersProbValueGH)) ||
			(!empty($relDescLDLValueGH)) ||
			(!empty($relDescCancerProbValueGH)) ||
			(!empty($descValueGH)) ||
			(!empty($descRValueGH)) ||
			(!empty($anyConditionOtherValueGH)) ||
			(!empty($ghRelDescOthersValueGH)) || 
			(!empty($anyReviewConstValueGH)) ||
			(!empty($reviewHeadValueGH)) ||
			(!empty($reviewConstOtherValueGH)) ||
			(!empty($reviewHeadOtherValueGH)) ||
			(!empty($reviewRespValueGH)) ||
			(!empty($reviewCardValueGH)) ||
			(!empty($reviewRespOtherValueGH)) ||
			(!empty($reviewCardOtherValueGH)) ||					
			(!empty($reviewGastroValueGH)) ||
			(!empty($reviewGenitValueGH)) ||
			(!empty($reviewGastroOtherValueGH)) ||
			(!empty($reviewGenitOtherValueGH)) ||					
			(!empty($reviewAllerValueGH)) ||
			(!empty($reviewNeuroValueGH)) ||
			(!empty($reviewAllerOtherValueGH)) ||
			(!empty($reviewNeuroOtherValueGH)) ||
			(!empty($descValueDHB)) ||
			(!empty($descValueDA)) ||
			(!empty($descValueDLP)) ||
			(!empty($descValueDS)) ||
			(!empty($descValueDTP)) ||
			(!empty($descValueDU)) ||
			(!empty($descValueDC)) ||
			(!empty($descValueDHP)) ||
			(!empty($descValueDGPP)) ||
			(!empty($descValueDLDL)) ||
			(!empty($descValueDV)) 
			
		){?>
            <table width="100%" cellpadding="2" cellspacing="0">	
                <tr>
                    <td colspan="4" class="text_10b">
                        <b>General Health</b>
                    </td>				
                </tr>	
                <tr><td height="7" colspan="4"></td></tr>
                <tr>
                    <td width="18%" valign="top" class="text_10">
                        <?php if($medDoctorValueGH){?>
                        <i><b>Medical Doctor :-</b></i>
                        <?php }?>		
                    </td>
                    <td class="text_10" colspan="3" valign="top">
                        <?php echo $medDoctorValueGH; ?>
                    </td>
                </tr>	
                <tr><td height="7" colspan="4"></td></tr>
                <tr>
                    <td class="text_10" valign="top">
                    <?php if($genMedCommentsValueGH){?>
                        <i><b>Comments :-</b></i>
                    <?php }?>		
                    </td>
                    <td class="text_10" colspan="3" valign="top">
                        <?php echo $genMedCommentsValueGH; ?>
                    </td>
                </tr>
                <tr><td height="7" colspan="4"></td></tr>
                <tr>
                    <td class="text_10" colspan="4" valign="top">
                    <?php 		
                    if(	
                        (!empty($anyConditionYouValueGH)) || 
                        (!empty($relDescHighBpValueGH)) || 
                        (!empty($relDescHeartProbValueGH)) || 
                        (!empty($relDescLungProbValueGH)) || 
                        (!empty($relDescStrokeProbValueGH)) || 
                        (!empty($relDescThyroidProbValueGH)) || 
                        (!empty($relDescArthritisProbValueGH)) || 
                        (!empty($subArthritisProbValueGH)) || 
						(!empty($relDescUlcersProbValueGH)) ||
						(!empty($relDescLDLValueGH)) ||
						(!empty($relDescCancerProbValueGH)) ||
						(!empty($descValueGH)) ||
                        (!empty($descRValueGH)) ||
                        (!empty($anyConditionOtherValueGH)) ||
                        (!empty($ghRelDescOthersValueGH)) || 
                        (!empty($anyReviewConstValueGH)) ||
                        (!empty($reviewHeadValueGH)) ||
                        (!empty($reviewConstOtherValueGH)) ||
                        (!empty($reviewHeadOtherValueGH)) ||
                        (!empty($reviewRespValueGH)) ||
                        (!empty($reviewCardValueGH)) ||
                        (!empty($reviewRespOtherValueGH)) ||
                        (!empty($reviewCardOtherValueGH)) ||					
                        (!empty($reviewGastroValueGH)) ||
                        (!empty($reviewGenitValueGH)) ||
                        (!empty($reviewGastroOtherValueGH)) ||
                        (!empty($reviewGenitOtherValueGH)) ||					
                        (!empty($reviewAllerValueGH)) ||
                        (!empty($reviewNeuroValueGH)) ||
                        (!empty($reviewAllerOtherValueGH)) ||
                        (!empty($reviewNeuroOtherValueGH)) ||
						(!empty($descValueDHB)) ||
						(!empty($descValueDA)) ||
						(!empty($descValueDLP)) ||
						(!empty($descValueDS)) ||
						(!empty($descValueDTP)) ||
						(!empty($descValueDU)) ||
						(!empty($descValueDC)) ||
						(!empty($descValueDHP)) ||
						(!empty($descValueDGPP)) ||
						(!empty($descValueDLDL)) ||
						(!empty($descValueDV)) 
						
                    ){?>
                        Any condition you or blood relative :-
                    <?php }?>	
                    </td>			
                </tr>
                <tr><td height="7" colspan="4"></td></tr>	
                <tr>
                    <td colspan="2" class="text_10"  valign="top">
                    <?php if($anyConditionYouValueGH){?>
                        <i><b>Any condition you :-</b></i>
                    <?php }?>			
                    </td>		
                    <td width="59%" colspan="2"  valign="top" class="text_10">
                    <?php if($relDescHighBpValueGH || $relDescHeartProbValueGH || $relDescLungProbValueGH || $relDescStrokeProbValueGH || $relDescThyroidProbValueGH || $relDescArthritisProbValueGH || $subArthritisProbValueGH || $relDescUlcersProbValueGH || $relDescLDLValueGH || $relDescCancerProbValueGH) {?>
                        <i><b>Any condition blood relative :-</b></i>
                    <?php }?>			
                    </td>		
                </tr>
                <tr><td height="7" colspan="4"></td></tr>
<?php
$anyConditionYouValueGHExplode='';
$anyConditionYouValueGHDesc='';
if($anyConditionYouValueGH) {
	$anyConditionYouValueGHExplode = explode('<br>',$anyConditionYouValueGH);
	foreach($anyConditionYouValueGHExplode as $acyValue) {
		switch ($acyValue):
			case "High Blood Pressure":					
				$anyConditionYouValueGHDesc.=$acyValue.get_set_pat_rel_values_retrive($descValueDHB,"pat",$delimiter,$hifen).'<br>';
				break;
			case "Heart Problem":					
				$anyConditionYouValueGHDesc.=$acyValue.get_set_pat_rel_values_retrive($descValueDHP,"pat",$delimiter,$hifen).'<br>';
				break;
			case "Arthritis":					
				$anyConditionYouValueGHDesc.=$acyValue.get_set_pat_rel_values_retrive($subArthritisProbValueGH,"pat",$delimiter,$hifen).get_set_pat_rel_values_retrive($descValueDA,"pat",$delimiter,$hifen).'<br>';
				break;
			case "Lung Problems":					
				$anyConditionYouValueGHDesc.=$acyValue.get_set_pat_rel_values_retrive($descValueDLP,"pat",$delimiter,$hifen).'<br>';
				break;
			case "Stroke":					
				$anyConditionYouValueGHDesc.=$acyValue.get_set_pat_rel_values_retrive($descValueDS,"pat",$delimiter,$hifen).'<br>';
				break;
			case "Thyroid Problems":					
				$anyConditionYouValueGHDesc.=$acyValue.get_set_pat_rel_values_retrive($descValueDTP,"pat",$delimiter,$hifen).'<br>';
				break;
			case "Diabetes":					
				$anyConditionYouValueGHDesc.=$acyValue.get_set_pat_rel_values_retrive($descValueGH,"pat",$delimiter,$hifen).'<br>';
				break;
			case "Ulcers":					
				$anyConditionYouValueGHDesc.=$acyValue.get_set_pat_rel_values_retrive($descValueDU,"pat",$delimiter,$hifen).'<br>';
				break;
			case "LDL":					
				$anyConditionYouValueGHDesc.=$acyValue.get_set_pat_rel_values_retrive($descValueDLDL,"pat",$delimiter,$hifen).'<br>';
				break;
			case "Cancer":					
				$anyConditionYouValueGHDesc.=$acyValue.get_set_pat_rel_values_retrive($descValueDC,"pat",$delimiter,$hifen).'<br>';
				break;
		endswitch;
	}
}
if(!$anyConditionYouValueGHDesc) { $anyConditionYouValueGHDesc = $anyConditionYouValueGH;}
?>                
                <tr>
                    <td colspan="2" class="text_10">
                        <?php echo $anyConditionYouValueGHDesc; ?>
                    </td>	
                    <td colspan="2" class="text_10" align="left" valign="top">
                        <?php echo $relDescHighBpValueGH.get_set_pat_rel_values_retrive($descValueDHB,"rel",$delimiter,$hifen);
							  echo '<br>'.$relDescHeartProbValueGH.get_set_pat_rel_values_retrive($descValueDHP,"rel",$delimiter,$hifen);
							  echo '<br>'.$relDescLungProbValueGH.get_set_pat_rel_values_retrive($descValueDLP,"rel",$delimiter,$hifen);
							  echo '<br>'.$relDescStrokeProbValueGH.get_set_pat_rel_values_retrive($descValueDS,"rel",$delimiter,$hifen);
							  echo '<br>'.$relDescThyroidProbValueGH.get_set_pat_rel_values_retrive($descValueDTP,"rel",$delimiter,$hifen);
							  echo '<br>'.$relDescArthritisProbValueGH.get_set_pat_rel_values_retrive($subArthritisProbValueGH,"rel",$delimiter,$hifen).get_set_pat_rel_values_retrive($descValueDA,"rel",$delimiter,$hifen);
							  echo '<br>'.$relDescUlcersProbValueGH.get_set_pat_rel_values_retrive($descValueDU,"rel",$delimiter,$hifen);
							  echo '<br>'.$relDescLDLValueGH.get_set_pat_rel_values_retrive($descValueDLDL,"rel",$delimiter,$hifen);
							  echo '<br>'.$relDescCancerProbValueGH.get_set_pat_rel_values_retrive($descValueDC,"rel",$delimiter,$hifen);
							  echo '<br>'; ?>
                    </td>	
                </tr>
                <tr><td height="7" colspan="4"></td></tr>
                <tr>
                    <td colspan="2" class="text_10">
                    <?php if($descValueGH){?>
                        You Diabetes :
                    <?php }?>			
                    </td>	
                    <td colspan="2" class="text_10">
                    <?php if($descRValueGH){?>
                        Relative Diabetes :
                    <?php }?>		
                    </td>			
                </tr>
                <tr><td height="7" colspan="4"></td></tr>
                <tr>		
                    <td colspan="2" class="text_10" align="left" valign="top">
                        <?php 
							echo get_set_pat_rel_values_retrive($descValueGH,"pat",$delimiter);
							echo get_set_pat_rel_values_retrive($descValueDV,"pat",$delimiter,$hifen);
						?>
                    </td>	
                    <td colspan="2" class="text_10" align="left" valign="top">
                        <?php 
							echo $descRValueGH; 
							echo get_set_pat_rel_values_retrive($descValueGH,"rel",$delimiter,$hifen);
							echo get_set_pat_rel_values_retrive($descValueDV,"rel",$delimiter,$hifen);
						?>
                    </td>	
                </tr>
                <tr><td height="7" colspan="4"></td></tr>
                <tr>		
                    <td colspan="4" class="text_10" align="left" valign="top">
                    <?php if($anyConditionOtherValueGH){?>
                        Any condition other
                    <?php }?>			
                    </td>			
                </tr>
                <tr><td height="7" colspan="4"></td></tr>
                <tr>				
                    <td colspan="4" class="text_10" align="left" valign="top">
                        <?php echo get_set_pat_rel_values_retrive($anyConditionOtherValueGH,"pat",$delimiter);?>
                    </td>	
                </tr>
                <tr><td height="7" colspan="4"></td></tr>
                <tr>		
                    <td colspan="4" class="text_10" align="left" valign="top">
                    <?php if($ghRelDescOthersValueGH){?>
                        Relative condition other
                    <?php }?>				
                    </td>			
                </tr>
                <tr><td height="7" colspan="4"></td></tr>
                <tr>				
                    <td colspan="4" class="text_10" align="left" valign="top">
                        <?php echo $ghRelDescOthersValueGH; 
							  echo get_set_pat_rel_values_retrive($anyConditionOtherValueGH,"rel",$delimiter,$hifen);	
						?>
                    </td>	
                </tr>
                <tr><td height="7" colspan="4"></td></tr>
                <tr>				
                    <td colspan="4" class="text_10" align="left" valign="top">
                    <?php if(
				
				(!empty($anyReviewConstValueGH)) ||
				(!empty($reviewHeadValueGH)) ||
				(!empty($reviewConstOtherValueGH)) ||
				(!empty($reviewHeadOtherValueGH)) ||
				(!empty($reviewRespValueGH)) ||
				(!empty($reviewCardValueGH)) ||
				(!empty($reviewRespOtherValueGH)) ||
				(!empty($reviewCardOtherValueGH)) ||					
				(!empty($reviewGastroValueGH)) ||
				(!empty($reviewGenitValueGH)) ||
				(!empty($reviewGastroOtherValueGH)) ||
				(!empty($reviewGenitOtherValueGH)) ||					
				(!empty($reviewAllerValueGH)) ||
				(!empty($reviewNeuroValueGH)) ||
				(!empty($reviewAllerOtherValueGH)) ||
				(!empty($reviewNeuroOtherValueGH)) ||
				(!empty($review_intgmntrGH)) ||
				(!empty($review_psychiatryGH)) ||
				(!empty($review_blood_lymphGH)) ||				
				(!empty($review_musculoskeletalGH)) ||
				(!empty($review_endocrineGH)) ||
				(!empty($review_eyeGH)) ||				
				(!empty($negChkBxValueGH))
				
                                
                            ){?>
                        <i>Review of Systems</i>
                    <?php }?>	
                    </td>	
                </tr>
                <tr><td height="7" colspan="4"></td></tr>
		<?php
			
		//ROS --
		$ros_pos=array(  $reviewAllerValueGH, $reviewCardValueGH,
					$anyReviewConstValueGH, $reviewHeadValueGH,
					$review_endocrineGH, $review_eyeGH, 
					$reviewGastroValueGH, $reviewGenitValueGH,
					$review_blood_lymphGH, $review_intgmntrGH,
					$review_musculoskeletalGH,$reviewNeuroValueGH,
					$review_psychiatryGH,$reviewRespValueGH);
		$ros_pos_other=array( 
						$reviewAllerOtherValueGH,$reviewCardOtherValueGH,
						$reviewConstOtherValueGH,$reviewHeadOtherValueGH,
						$review_endocrineOtherGH,$review_eyeOtherGH,
						$reviewGastroOtherValueGH,$reviewGenitOtherValueGH,
						$review_blood_lymphOtherGH,$review_intgmntrOtherGH,
						$review_musculoskeletalOtherGH, $reviewNeuroOtherValueGH, 
						$review_psychiatryOtherGH,$reviewRespOtherValueGH);
		$cpr->get_med_ros($ros_pos, $ros_pos_other, $negChkBxValueGH, 2);
		
		?>
                
                <tr><td height="7" colspan="7"></td></tr>
            </table>
<?php	
		}
		if (imw_num_rows($rsGetMedication) > 0) {	
		?>
            <table width="100%" cellpadding="2" cellspacing="0">	
                <tr>
                    <td width="100%" colspan="7" class="text_10b">
                        <b>Medication</b>
                    </td>				
                </tr>	
                <tr><td height="7" colspan="7"></td></tr>
                <tr>
                    <td class="text_10" nowrap valign="top">
                        <i><b>Ocular</b></i>
                    </td>
                    <td class="text_10" nowrap valign="top">
                        <i><b>Medication</b></i>
                    </td>
                    <td class="text_10" nowrap valign="top">
                        <i><b>Strength</b></i>
                    </td>
                    <td class="text_10" nowrap valign="top">
                        <i><b>Prescribed By</b></i>
                    </td>
                    <td class="text_10" nowrap valign="top">
                        <i><b>Begin Date</b></i>
                    </td>
                    <td class="text_10" nowrap valign="top">
                        <i><b>End Date</b></i>
                    </td>
                    <td class="text_10" nowrap valign="top">
                        <i><b>Comments</b></i>
                    </td>		
                </tr>	
                <tr><td height="7"></td></tr>
                
                <?php 	
                while($row = imw_fetch_assoc($rsGetMedication)){
                ?>
                    <tr>
                    <?php 
                    if($row["type"] == "1"){
                        ?>
                        <td class="text_10" valign="top">
                        
                        </td>
                        <?php	
                    }	
                    else if($row["type"] == "4"){
                        ?>
                        <td class="text_10" valign="top">
                            Ocular
                        </td>
                        <?php	
                    }
                        ?>
                        <td class="text_10" valign="top">
                            <?php echo $row["title"]; ?>
                        </td>
                        
                        <td class="text_10" valign="top">
                            <?php echo $row["destination"]; ?>
                        </td>
                        
                        <td class="text_10" valign="top">
                            <?php echo $row["referredby"]; ?>
                        </td>
                        
                        <td class="text_10" valign="top">
                            <?php echo isDate($row["begdate"]); ?>
                        </td>
                        
                        <td class="text_10" valign="top">
                            <?php echo isDate($row["enddate"]); ?>
                        </td>
                        
                        <td class="text_10" valign="top">
                            <?php echo isDate($row["comments"]); ?>
                        </td>
                    </tr>				
                        <?php								
                }
                ?>
                <tr><td height="7" colspan="7"></td></tr>
            </table>
		<?php
		}
		
		if (imw_num_rows($rsGetAllergies) > 0) {	
		?>
            <table width="100%" cellpadding="2" cellspacing="0">	
                <tr>
                    <td width="100%" colspan="7" class="text_10b">
                        <b>Allergies</b>
                    </td>				
                </tr>	
                <tr><td height="7" colspan="7"></td></tr>
                <tr>
                    <td class="text_10" nowrap valign="top">
                        <i><b>Drug</b></i>
                    </td>
                    <td class="text_10" nowrap valign="top">
                        <i><b>Name</b></i>
                    </td>
                    <td class="text_10" nowrap valign="top">
                        <i><b>Begin Date</b></i>
                    </td>
                    
                    <td class="text_10" nowrap valign="top">
                        <i><b>Acute</b></i>
                    </td>
                    <td class="text_10" nowrap valign="top">
                        <i><b>Chronic</b></i>
                    </td>
                    <td class="text_10" nowrap valign="top">
                        <i><b>Reactions</b></i>
                    </td>
                    <td class="text_10" nowrap valign="top">
                        <i><b>Comments</b></i>
                    </td>		
                </tr>	
                <tr><td height="7"></td></tr>
                
                <?php 	
                while($row = imw_fetch_assoc($rsGetAllergies)){
                ?>
                    <tr>
                    <?php 
                    if($row["type"] == "7"){
                        ?>
                        <td class="text_10" valign="top">
                            Drug
                        </td>
                        <?php	
                    }
                        ?>
                        <td class="text_10" valign="top">
                            <?php echo $row["title"]; ?>
                        </td>
                        
                        <td class="text_10" valign="top">
                            <?php echo isDate($row["begdate"]); ?>
                        </td>
                        <td class="text_10" valign="top">
                            <?php echo ucwords($row["acute"]); ?>
                        </td>
                        
                        <td class="text_10" valign="top">
                            <?php echo ucwords($row["chronic"]); ?>
                        </td>
                        
                        <td class="text_10" valign="top">
                            <?php echo ucwords($row["reactions"]); ?>
                        </td>	
                        <td class="text_10" valign="top">
                            <?php echo ucwords($row["comments"]); ?>
                        </td>		
                    </tr>				
                        <?php								
                }
                ?>
                <tr><td height="7" colspan="7"></td></tr>
            </table>
		<?php
		}
		
		if (imw_num_rows($rsGetSurgeries) > 0) {	
		?>
            <table width="100%" cellpadding="2" cellspacing="0">	
                <tr>
                    <td width="100%" colspan="7" class="text_10b">
                        <b>Surgeries</b>
                    </td>				
                </tr>	
                <tr><td height="7" colspan="7"></td></tr>
                <tr>
                    <td class="text_10" nowrap valign="top">
                        <i><b/>Ocular</b></i>
                    </td>
                    <td class="text_10" nowrap valign="top">
                        <i><b/>Name</b></i>
                    </td>
                    <td class="text_10" nowrap valign="top">
                        <i><b/>Date of Surgery</b></i>
                    </td>
                    
                    <td class="text_10" nowrap valign="top">
                        <i><b/>Physician</b></i>
                    </td>
                    <td class="text_10" nowrap valign="top">
                        <i><b/>Comments</b></i>
                    </td>		
                </tr>	
                <tr><td height="7" colspan="5"></td></tr>
                
                <?php 		
                while($row = imw_fetch_assoc($rsGetSurgeries)){
                ?>
                    <tr>
                    <?php 
                    if($row["type"] == "6"){
                        ?>
                        <td class="text_10" valign="top">
                            Ocular
                        </td>
                        <?php	
                    }else if($row["type"] == "5"){
                        ?>
                        <td class="text_10" valign="top">
                            
                        </td>
                        <?php	
                    }
                    
                        ?>
                        <td class="text_10" valign="top">
                            <?php echo $row["title"]; ?>
                        </td>
                        
                        <td class="text_10" valign="top">
                            <?php echo isDate($row["begdate"]); ?>
                        </td>
                        <td class="text_10" valign="top">
                            <?php echo ucwords($row["referredby"]); ?>
                        </td>
                        
                        <td class="text_10" valign="top">
                            <?php echo ucwords($row["comments"]); ?>
                        </td>			
                    </tr>				
                        <?php								
                }
                ?>
                <tr><td height="7" colspan="5"></td></tr>
            </table>
		<?php
		}		
//END
		//if($int_appt < $int_tot_appt){
		print '</newpage>';
		//}
		$query_staring = array();
		$query_st = array();
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

//--- GET PDF FILE -----
$sug_app_fac = 0;
if(empty($patient_print_data) == false){
	$patient_print_data = str_replace(array("<newpage>","</newpage>","<PAGE></PAGE>"), array("<PAGE>","</PAGE>",""), $patient_print_data);
	$file_location = write_html($patient_print_data);
	echo '<div class="text-center alert alert-info">Please Check PDF.</div>';
	$sug_app_fac = 1;
}else {
	echo '<div class="text-center alert alert-info">No Recod Exists.</div>';
	$sug_app_fac = 0;
}