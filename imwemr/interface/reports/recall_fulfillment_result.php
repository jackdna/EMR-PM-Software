<?php
extract($_REQUEST);
$objDB = $GLOBALS['adodb']['db'];
//$getSqlDateFormat= getSqlDateFormat();
// GET ALL FACILITIES	
$arrFac = array();	$allFacNames = array();
$qry = imw_query("select id,name,city, pam_code from facility order by name");
while($qryRes = imw_fetch_assoc($qry)){
	$id = $qryRes['id'];
	if($strFacIds == '') { $arrFac[] = $id; }
	$allFacNames[$id] = $qryRes['name'];
	$allFacCities[$id] = $qryRes['city'];
	$pam = $qryRes['pam_code'];
	if($pam =='' ) { $pam = '01'; }
	$allFacPAM[$id] = $pam;
}	
//setting heights
$intTotalHeight = $_SESSION["wn_height"] + 10;
$intUnFulfilledHeight = ($intTotalHeight/2) - 200;
$intFulfilledHeight = ($intTotalHeight/2) - 200;
$intButtonHeight = 20;
$repType= $_REQUEST['repType'];
$recallTemplatesListId = $_REQUEST['recallTemplatesListId'];
$Start_date = getDateFormatDB($_REQUEST['Start_date']);
$End_date = getDateFormatDB($_REQUEST['End_date']);
$facility_name = implode(',' ,$_REQUEST['facility_name']);
$procedures = implode(',' ,$_REQUEST['procedures']);

if($facility_name != ""){
	$whr_fac = " AND prec.facility_id in($facility_name)";
}
if($procedures != ""){
	$whr_proc = " AND prec.procedure_id in($procedures)";
}
if($months){
	$whr_month = " and date_format(prec.recalldate,'%m') = '".$months."'";
}
if($years){
	$whr_year = " and date_format(prec.recalldate,'%Y') = '".$years."'";
}
$hippa_whr = " AND pd.hipaa_voice='1'";

$arrQry = array();
$qry = imw_query("SELECT pd.id, pd.lname, pd.fname, pd.DOB,  pd.phone_home, pd.phone_cell, pd.phone_biz, pd.preferr_contact, pd.city, pd.state, pd.street, pd.street2, pd.postal_code, 
prec.descriptions, DATE_FORMAT(prec.recalldate,'".get_sql_date_format()."') as recalldate, prec.operator, sp.id as 'procID', sp.proc,
us.fname as 'dFname', us.lname as 'dLname' 
		FROM patient_app_recall AS prec
		LEFT JOIN patient_data AS pd ON prec.patient_id = pd.id 
		LEFT JOIN slot_procedures sp ON prec.procedure_id = sp.id 
		LEFT JOIN users us ON prec.operator = us.id 
		WHERE 1 = 1
		AND prec.descriptions != 'MUR_PATCH' 
		$whr_fac $whr_proc $whr_year $whr_month $hippa_whr 
		ORDER BY pd.lname asc, pd.fname asc");
while($qryRes = imw_fetch_assoc($qry)){
	$arrQry[] = $qryRes;
}

//getting previous to selected month
$showMonth = date("M", mktime(0, 0, 0, $months, 1, $years));
$monthPrevSelected = date("m", mktime(0, 0, 0, $months , 1, $years));
$yearPrevSelected = date("Y", mktime(0, 0, 0, $months , 1, $years));

$arrSchQry = array();
$schQry = imw_query("select sa.sa_patient_id, DATE_FORMAT(sa.sa_app_start_date, '".get_sql_date_format()."') as sa_app_start_date, TIME_FORMAT(sa.sa_app_starttime, '%h:%i %p') as sa_app_starttime, sa.sa_doctor_id, sa.sa_facility_id, sa.procedureid, sa.status_update_operator_id, us.fname, us.lname, sp.proc  
			from schedule_appointments sa 
			LEFT JOIN users us ON sa.sa_doctor_id = us.id 
			LEFT JOIN slot_procedures sp ON sa.procedureid = sp.id 
			where (sa.sa_app_start_date BETWEEN '".$Start_date."' AND '".$End_date."')
			AND sa.sa_patient_app_status_id NOT IN('3','18','203') 
			group by sa.sa_patient_id 
			order by sa.sa_app_start_date desc, sa.sa_app_starttime desc");
while($schRes = imw_fetch_assoc($schQry)){
	$arrSchQry[] = $schRes;
}
$arrSchDetails = array();
if(is_array($arrSchQry) && count($arrSchQry) > 0){
	foreach($arrSchQry as $thisSchAppt){
		$arrSchDetails[$thisSchAppt["sa_patient_id"]] = $thisSchAppt;
	}
}
unset($arrSchQry);

$fulfilled = array();
$unfulfilled = array();
$pat_id_arr = array();
if(is_array($arrQry) && count($arrQry) > 0){
	$fulid = 0;
	$unfulid = 0;
	foreach($arrQry as $thisRecall){
		$lname = $thisRecall['lname'];
		if($thisRecall['fname']){
			$fname = ', '.$thisRecall['fname'];
		}
		$DOB = strtotime($thisRecall['DOB']);
		$birth = date("".phpDateFormat()."", $DOB);
		$pat_nam = $lname.$fname;
		
		$phone_default = $thisRecall["phone_home"];
		$prefer_contact = $thisRecall["preferr_contact"];
		if($prefer_contact == 0)
		{
			if(trim($thisRecall["phone_home"]) != ""){$phone_default = $thisRecall["phone_home"]; }
		}
		else if($prefer_contact == 1)
		{
			if(trim($thisRecall["phone_biz"]) != ""){$phone_default = $thisRecall["phone_biz"]; }				
		}
		else if($prefer_contact == 2)
		{
			if(trim($thisRecall["phone_cell"]) != ""){$phone_default = $thisRecall["phone_cell"]; }				
		}
		
		$docID = $arrSchDetails[$thisRecall["id"]]["sa_doctor_id"];
		$docNameArr["LAST_NAME"] = $arrSchDetails[$thisRecall["id"]]["lname"];
		$docNameArr["FIRST_NAME"] = $arrSchDetails[$thisRecall["id"]]["fname"];
		$docName = changeNameFormat($docNameArr);

		$facilityID = $arrSchDetails[$thisRecall["id"]]["sa_facility_id"];

		$office_code=$allFacPAM[$facilityID];
		$phone = str_replace(' ','',str_replace('(','',str_replace(')','',str_replace('-','',$phone_default))));
		$ap_date ='';
		if($arrSchDetails[$thisRecall["id"]]["sa_app_start_date"]!='') {
			$aDate = explode("/",$arrSchDetails[$thisRecall["id"]]["sa_app_start_date"]);
			$ap_date = $aDate[0]."-".$aDate[1]."-".$aDate[2];
		}
		

		if(isset($arrSchDetails[$thisRecall["id"]]) && count($arrSchDetails[$thisRecall["id"]]) > 0){
			
			$fulfilled[$fulid]['pat_name'] = $pat_nam." - ".$thisRecall["id"];
			$fulfilled[$fulid]['phone'] = $thisRecall['phone_home'];
			$fulfilled[$fulid]['app_dat'] = $arrSchDetails[$thisRecall["id"]]["sa_app_start_date"].'<br>'.$arrSchDetails[$thisRecall["id"]]["sa_app_starttime"];
			$fulfilled[$fulid]['proc'] = $thisRecall['proc'];	

			$fulfilled[$fulid]['desc'] = $thisRecall['descriptions'];

			// STRING HOUSE CALLS
			$strFulHouseData.= '"'.$pat_nam.'"'.','.'"'.$phone_default.'"'.','.'"'.$arrSchDetails[$thisRecall["id"]]["sa_app_start_date"].'"'.','.'"'.$arrSchDetails[$thisRecall["id"]]["sa_app_starttime"].'"'.','.'"'.$thisRecall["id"].'"'.','.
			'"'.$arrSchDetails[$thisRecall["id"]]["sa_doctor_id"].'"'.','.'"'.$arrSchDetails[$thisRecall["id"]]["procedureid"].'"'.','.'"'.$docName.'"'.','.'"'.$arrSchDetails[$thisRecall["id"]]["proc"].'"'.','.'"'.$allFacCities[$facilityID].'"'.','.
			'"'.$thisRecall['street'].'"'.','.'"'.$thisRecall['city'].'"'.','.'"'.$thisRecall['state'].'"'.','.'"'.$thisRecall['postal_code'].'"';
			$strFulHouseData.= "\n";
			// ------------------	

			$strFulPamData.='"'.$thisRecall["id"].'",'.'"01",'.'"'.$office_code.'",'.'"01",'.'"'.$thisRecall['fname'].'",'.'"'.$thisRecall['lname'].'",'.
			'"'.$ap_date.'",'.'"'.$arrSchDetails[$thisRecall["id"]]["sa_app_starttime"].'",'.'"'.$arrSchDetails[$thisRecall["id"]]['status_update_operator_id'].'",'.
			'"'.$arrSchDetails[$thisRecall["id"]]["procedureid"].'",'.'"'.$phone.'",'.'""';
			$strFulPamData.= "\n";
			
			
			$fulid++;
		}else{

			$docID = $thisRecall["operator"];
			$docNameArr["LAST_NAME"] = $thisRecall["dLname"];
			$docNameArr["FIRST_NAME"] = $thisRecall["dFname"];
			$docName = changeNameFormat($docNameArr);
			
			$pat_id_arr[] = $thisRecall["id"];
			$unfulfilled[$unfulid]['pat_name'] = $pat_nam." - ".$thisRecall["id"];
			$unfulfilled[$unfulid]['dob'] = $birth;
			$unfulfilled[$unfulid]['phone'] = $phone_default;			
			$unfulfilled[$unfulid]['proc'] = $thisRecall['proc'];
			$unfulfilled[$unfulid]['desc'] = $thisRecall['descriptions'];

			// STRING HOUSE CALLS
			$strUnfulHouseData.= '"'.$pat_nam.'"'.','.'"'.$phone_default.'"'.','.'"'.$thisRecall["recalldate"].'"'.','.'""'.','.'"'.$thisRecall["id"].'"'.','.
			'"'.$docID.'"'.','.'"'.$thisRecall['procID'].'"'.','.'"'.$docName.'"'.','.'"'.$thisRecall['proc'].'"'.','.'"'.$allFacCities[$facilityID].'"'.','.
			'"'.$thisRecall['street'].'"'.','.'"'.$thisRecall['city'].'"'.','.'"'.$thisRecall['state'].'"'.','.'"'.$thisRecall['postal_code'].'"';
			$strUnfulHouseData.= "\n";
			// ------------------	
	
			// PAM DATA
			$strUnfulPamData.='"'.$thisRecall["id"].'",'.'"01",'.'"'.$office_code.'",'.'"01",'.'"'.$thisRecall['fname'].'",'.'"'.$thisRecall['lname'].'",'.
			'"'.$thisRecall["recalldate"].'",'.'"",'.'"'.$docID.'",'.
			'"'.$thisRecall['procID'].'",'.'"'.$phone.'",'.'""';
			$strUnfulPamData.= "\n";
			// END PAM DATA----
						
			$unfulid++;
		}
	}
}
$pat_id_imp = implode(',', $pat_id_arr);

unset($pat_id_arr);
unset($arrQry);
unset($arrSchDetails);

if($repType=='houseCalls')
{
	// FULFILL FILE
	$strFulHouseCalls = "Patient Name,Patient Home Phone,Appointment Date,Appointment Time,Patient Account Number,Doctor Number,Procedure Number,Doctor Name,Procedure Name,Location (office) Name,Patient Address,Patient City,Patient State,Patient Zip Code";
	$strFulHouseCalls.="\n".$strFulHouseData; 

	$full_filename = data_path().'users/UserId_'.$_SESSION['authId'].'/recalls_fulfillment.txt';
	$fileInfo = pathinfo($full_filename);
	if(!is_dir($fileInfo['dirname'])) mkdir($fileInfo['dirname'], 0777, true);
	$fp=fopen($full_filename,"w");
	@fwrite($fp,$strFulHouseCalls);
	@fclose($fp);

	// UNFULFILL FILE
	$strUnfulHouseCalls = "Patient Name,Patient Home Phone,Appointment Date,Appointment Time,Patient Account Number,Doctor Number,Procedure Number,Doctor Name,Procedure Name,Location (office) Name,Patient Address,Patient City,Patient State,Patient Zip Code";
	$strUnfulHouseCalls.="\n".$strUnfulHouseData; 

	$unfull_filename = data_path().'users/UserId_'.$_SESSION['authId'].'/recalls_unfulfillment.txt';
	$fileInfo = pathinfo($unfull_filename);
	if(!is_dir($fileInfo['dirname'])) mkdir($fileInfo['dirname'], 0777, true);
	$fp=fopen($unfull_filename,"w");
	@fwrite($fp,$strUnfulHouseCalls);
	@fclose($fp);
}
//------------

// PAM 2000
if($repType=='pam'){
// FULFILL FILE
	$exceltext="";
	$exceltext ='Account-ID,Message Type,Office,Language Type,Patient Fname,Patient Lname,App Date,App Time,Provider,App Type,Phone,Email';
	$exceltext.="\n".$strFulPamData;

	$fullPam_filename = data_path().'users/UserId_'.$_SESSION['authId'].'/recalls_fulfillment.txt';
	$fileInfo = pathinfo($fullPam_filename);
	if(!is_dir($fileInfo['dirname'])) mkdir($fileInfo['dirname'], 0777, true);
	$fp=fopen($fullPam_filename,"w");
	@fwrite($fp,$exceltext);
	@fclose($fp);

// UNFULFILL FILE
	$exceltext="";
	$exceltext ='Account-ID,Message Type,Office,Language Type,Patient Fname,Patient Lname,App Date,App Time,Provider,App Type,Phone,Email';
	$exceltext.="\n".$strUnfulPamData;
	
	$unfullPam_filename = data_path().'users/UserId_'.$_SESSION['authId'].'/recalls_unfulfillment.txt';
	$fileInfo = pathinfo($unfullPam_filename);
	if(!is_dir($fileInfo['dirname'])) mkdir($fileInfo['dirname'], 0777, true);
	$fp=fopen($unfullPam_filename,"w");
	@fwrite($fp,$exceltext);
	@fclose($fp);
}
//------------
?>
<div id="csvFileDataTable">
		<table id="report_header" class="rpt_table rpt rpt_table-bordered rpt_padding">
			<tr>	
				<td width="525" align="left" class="rptbx1">Recalls Fulfillment Report <?php echo $showMonth.". ".$years;?>&nbsp;</td>		
			</tr>
		</table> 
		<table id="unful_header" class="rpt_table rpt rpt_table-bordered rpt_padding">
			<tr>	
				<td class="text_b_w">Appointments Unfulfilled (<?php echo count($unfulfilled);?>)</td>
			</tr>
		</table>
		<table class="rpt_table rpt rpt_table-bordered rpt_padding">
			<tr class='text_9b' height="20" bgcolor="#c0c0c0">
				<td width="27%"  nowrap="nowrap">Patient Name - ID</td>
				<td width="12%"  nowrap="nowrap">DOB</td>
				<td width="16%"  nowrap="nowrap">Phone#</td>				
				<td width="17%"  align="left" nowrap="nowrap">Procedure</td>
				<td width="28%"  align="left" nowrap="nowrap">Reason</td>
			</tr>
		</table>
		<div id="unfulfilled_recalls" style="float:left;width:100%;height:<?php echo $intUnFulfilledHeight;?>px;overflow:scroll;overflow-x:hidden;">
		<table class="rpt_table rpt rpt_padding">
			<?php	
			if(count($unfulfilled) == 0){
				?>
				<tr class='text_b' height="20">
						<td colspan="5" align="center" class="failureMsg"> No Result. </td>
				</tr>				
				<?php
			}else{ for($j = 0; $j < count($unfulfilled); $j++){ ?>
				<tr>
					<td width="27%" nowrap="nowrap"  class="text_10" valign="top"><?php echo $unfulfilled[$j]['pat_name'];?></td>
					<td width="13%" class="text_10" align="left" valign="top"><?php echo $unfulfilled[$j]['dob']; ?> </td>					
					<td width="16%" class="text_10" align="left" valign="top"><?php echo core_phone_format($unfulfilled[$j]['phone']); ?> </td>		
					<td width="17%" class="text_10" align="left" valign="top"><?php echo $unfulfilled[$j]['proc']; ?> </td>
					<td width="28%" class="text_10" align="left" valign="top"><?php echo $unfulfilled[$j]['desc']; ?> </td>					
			   </tr>
			<?php } } ?>
			</table>
		</div>
		<table id="ful_header" class="rpt_table rpt rpt_table-bordered rpt_padding">
			<tr>	
				<td class="text_b_w">Appointments Fulfilled (<?php echo count($fulfilled);?>)</td>
			</tr>
		</table>
		<table class="rpt_table rpt rpt_table-bordered rpt_padding">
			<tr class='text_9b' height="20" bgcolor="#c0c0c0">
				<td width="27%" nowrap="nowrap">Patient Name - ID</td>
				<td width="16%" nowrap="nowrap">Phone#</td>			
				<td width="12%" nowrap="nowrap">Appointment</td>
				<td width="17%" align="left" nowrap="nowrap">Procedure</td>
				<td width="28%" align="left" nowrap="nowrap">Reason</td>
			</tr>
		</table>
		<div id="fulfilled_recalls" style="float:left;width:100%;height:<?php echo $intFulfilledHeight;?>px;overflow:scroll;overflow-x:hidden;">
			<table class="rpt_table rpt rpt_padding">
			<?php	
			if(count($fulfilled) == 0){
				?>
				<tr class='text_b' height="20">
					<td colspan="5" align="center" class="failureMsg"> No Result. </td>
				</tr>
				<?php
			}else{				
				for($j = 0; $j < count($fulfilled); $j++){
					?>
				<tr>
					<td width="27%" nowrap="nowrap"  class="text_10" valign="top"><?php echo $fulfilled[$j]['pat_name'];?></td>                
					<td width="16%" class="text_10" align="left" valign="top"><?php echo core_phone_format($fulfilled[$j]['phone']); ?> </td>		
					<td width="12%" class="text_10" align="left" valign="top"><?php echo $fulfilled[$j]['app_dat']; ?> </td>
					<td width="17%" class="text_10" align="left" valign="top"><?php echo $fulfilled[$j]['proc']; ?> </td>
					<td width="28%" class="text_10" align="left" valign="top"><?php echo $fulfilled[$j]['desc']; ?> </td>					
			   </tr>
					<?php 
				}
			}
			?>
			</table>
		</div>
</div> 