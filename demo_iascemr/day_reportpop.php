<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
session_start();
if($_REQUEST['hidd_report_format']!='csv') {
	echo '<table id="loader_tbl" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif;">
			<tr class="text_9" height="20" bgcolor="#EAF0F7" valign="top">
				<td align="center">Please wait while data is retrieving from the server.</td>
			</tr>
			<tr class="text_9" height="20" bgcolor="#EAF0F7" valign="top">
				<td align="center"><img src="images/pdf_load_img.gif"></td> 
			</tr>
		</table>';
}

set_time_limit(500);
include_once("common/conDb.php");
if($_SESSION["loginUserId"]=="" && $_SESSION['loginUserName']=="") {
	echo '<script>top.location.href="index.php"</script>';
}
$get_http_path=$_REQUEST['get_http_path'];
include_once("common/commonFunctions.php");
include_once("admin/classObjectFunction.php");
global $objManageData;
$objManageData = new manageData;
$loginUser = $_SESSION['loginUserId'];
//include("common/linkfile.php");
if($_SESSION['iasc_facility_id'])
{
	$fac_con=" and stub_tbl.iasc_facility_id='$_SESSION[iasc_facility_id]'"; 
}
//get detail for logged in facility
$queryFac=imw_query("select * from facility_tbl where fac_id='$_SESSION[facility]'")or die(imw_error());
$dataFac=imw_fetch_object($queryFac);
$name=stripcslashes($dataFac->fac_name);
$address=stripcslashes($dataFac->fac_address1).' '.stripcslashes($dataFac->fac_address2).' '.stripcslashes($dataFac->fac_city).' '.stripcslashes($dataFac->fac_state);

$SurgeryQry="select * from surgerycenter where surgeryCenterId=1";
$SurgeryRes= imw_query($SurgeryQry) or die($SurgeryQry.imw_error());
while($SurgeryRecord=imw_fetch_array($SurgeryRes))
{
	//$name= stripslashes($SurgeryRecord['name']);
	//$address= stripslashes($SurgeryRecord['address'].' '.$SurgeryRecord['city'].' '.$SurgeryRecord['state']);
	$img = $SurgeryRecord['logoName'];
	$surgeryCenterLogo=$SurgeryRecord['surgeryCenterLogo'];
}

$bakImgResource = imagecreatefromstring($surgeryCenterLogo);
//$canvasImgResource = imagecreatefromjpeg($bakImgResource);										
imagejpeg($bakImgResource,'new_html2pdf/white.jpg');
//$file=@fopen('new_html2pdf/white.jpg','w+');
//@fputs($file,$surgeryCenterLogo);


//var_dump(file_exists('new_html2pdf/white.jpg'));
$size=getimagesize('new_html2pdf/white.jpg');
//print_r($size);die('abcd');
$hig=$size[1];
$wid=$size[0];
$higinc=$hig+10;
$filename='new_html2pdf/white.jpg';
 function showThumbImages($fileName='white.jpg',$targetWidth=500,$targetHeight=70)
 { 

	if(file_exists($fileName))
	{ 
		$img_size=getimagesize('new_html2pdf/white.jpg');
		 $width=$img_size[0];
		 $height=$img_size[1];
		 $filename;
		do
		{
			if($width > $targetWidth)
			{
				 $width=$targetWidth;
				 $percent=$img_size[0]/$width;
				 $height=$img_size[1]/$percent; 
			}
			if($height > $targetHeight)
			{
				$height=$targetHeight;
				$percent=$img_size[1]/$height;
				$width=$img_size[0]/$percent; 
			}

		}while($width > $targetWidth || $height > $targetHeight);

		$returnArr[] = "<img src='white.jpg' width='$width' height='$height'>";
		$returnArr[] = $width;
		$returnArr[] = $height;
		return $returnArr; 
	} 
	return "";
 }				
// end set surgerycenter detail  
$showAllApptStatus = $_REQUEST['showAllApptStatus'];
$date=$_REQUEST['date12'];
$dType	=	$_REQUEST['dType'];
$dType=	!empty($dType)	?	$dType		:	'details' ;
$selected_date= $date;
$showDate = $date;
if($date!="") {
	$dat1=explode("-",$date);
	$dat1[0];
	$dat1[1];
	$dat1[2];
	$selected_date=$dat1[2].'-'.$dat1[0].'-'.$dat1[1];
}
$current_date=date("m-d-Y");
$img_logo = showThumbImages('new_html2pdf/white.jpg',170,50);
$imgheight= $img_logo[2]+8;
$imgwidth= $img_logo[1]+200;	

//START GET ALL ANESTHESIOLOGIST FROM USERS
$anesTypeQry 			= "SELECT usersId, userTitle, fname, mname, lname FROM users WHERE user_type = 'Anesthesiologist' ORDER BY usersId";
$anesTypeRes 			= imw_query($anesTypeQry) or die($anesTypeQry.imw_error());
$arrAnesId 				= $arrAnesTitle = $arrAnesFname = $arrAnesMname = $arrAnesLname = array();
if(imw_num_rows($anesTypeRes)>0) {
	while($anesTypeRow 	= imw_fetch_array($anesTypeRes)) {
		$arrAnesId[] 	= $anesTypeRow['usersId'];
		$arrAnesTitle[] = trim(stripslashes($anesTypeRow['userTitle']));
		$arrAnesFname[] = trim(stripslashes($anesTypeRow['fname']));
		$arrAnesMname[] = trim(stripslashes($anesTypeRow['mname']));
		$arrAnesLname[] = trim(stripslashes($anesTypeRow['lname']));
	}
}
//END GET ALL ANESTHESIOLOGIST FROM USERS
      
// GETTING LOGIN USER FIRST NAME, MIDDLE NAME, LAST NAME, USERTYPE, PRACTICENAME.
	$userTypeQry = "SELECT fname, mname, lname, 
			user_type,coordinator_type, practiceName FROM users
			WHERE usersId = '$loginUser'";
	$userTypeRes = imw_query($userTypeQry) or die($userTypeQry.imw_error());
	$userTypeRows = imw_fetch_array($userTypeRes);
	$surgeonLoggedFirstName = trim(stripslashes($userTypeRows['fname']));
	$surgeonLoggedMiddleName = trim(stripslashes($userTypeRows['mname']));
	$surgeonLoggedLastName = trim(stripslashes($userTypeRows['lname']));
	$userType = $userTypeRows['user_type'];
	$coordinatorType = $userTypeRows['coordinator_type'];
	$practiceName = stripslashes($userTypeRows['practiceName']);
// GETTING LOGIN USER FIRST NAME, MIDDLE NAME, LAST NAME, USERTYPE, PRACTICENAME.

//START CODE TO SHOW RECORD RELATED TO SPECIFIC USER
$showAllApptStatusQry = " AND  patient_status!='Canceled'";
if($showAllApptStatus=="Active") {
	$showAllApptStatusQry = " AND  patient_status!='Canceled'";	
}else if($showAllApptStatus=="Canceled") {
	$showAllApptStatusQry = " AND  patient_status='Canceled'";	
}
$surgeonSpecificQry="";
if($userType=='Coordinator' && $coordinatorType!='Master') { //IF USER TYPE IS Coordinator AND HE IS NOT MASTER THEN SHOW RECORD RELATED TO HIS PRACTICENAME
	$AndUserPracticeNameQry=getPracticeUser($practiceName,"AND","");  
	$practiceSurgeonQry = "select fname, mname, lname from users WHERE practiceName!='' ".$AndUserPracticeNameQry;
	$practiceSurgeonRes = imw_query($practiceSurgeonQry) or die(imw_error());
	$practiceSurgeonNumRow = imw_num_rows($practiceSurgeonRes);
	if($practiceSurgeonNumRow>0){
		$practiceSurgeonFname=array();
		$practiceSurgeonMname=array();
		$practiceSurgeonLname=array();
		while($practiceSurgeonRow = imw_fetch_array($practiceSurgeonRes)) {
			$practiceSurgeonFname[] = addslashes(stripslashes($practiceSurgeonRow['fname'])); // in case of string match with "IN" Query
			$practiceSurgeonMname[] = addslashes(stripslashes($practiceSurgeonRow['mname']));
			$practiceSurgeonLname[] = addslashes(stripslashes($practiceSurgeonRow['lname']));
		}
		$practiceSurgeonFnameImplode = implode("','",$practiceSurgeonFname);
		$practiceSurgeonMnameImplode = implode("','",$practiceSurgeonMname);
		$practiceSurgeonLnameImplode = implode("','",$practiceSurgeonLname);
		$surgeonSpecificQry = "AND surgeon_fname in('".$practiceSurgeonFnameImplode."') AND surgeon_lname in('".$practiceSurgeonLnameImplode."')";
	}
}
elseif( $userType == 'Surgeon' ){
	$surgeonSpecificQry = " AND surgeon_fname = '".$surgeonLoggedFirstName."' AND surgeon_lname = '".$surgeonLoggedLastName."' ";
}
//END CODE TO SHOW RECORD RELATED TO SPECIFIC USER
		      
//$stub_tbl_group_query = "select * from stub_tbl where dos = '$selected_date' AND patient_status!='Canceled' $surgeonSpecificQry group by surgeon_lname,surgeon_mname,surgeon_fname ORDER BY surgery_time, surgeon_fname"; 		
$stub_tbl_group_query = "select surgeon_fname, surgeon_mname, surgeon_lname from stub_tbl where dos = '$selected_date' $surgeonSpecificQry $fac_con $showAllApptStatusQry ORDER BY surgery_time, surgeon_fname"; 		
$stub_tbl_group_res = imw_query($stub_tbl_group_query) or die($stub_tbl_group_query.imw_error());
$stub_tbl_group_NumRow = imw_num_rows($stub_tbl_group_res);
$t=0;
$table='';
if($stub_tbl_group_NumRow>0){
	$table.='
			<style>
				.tb_heading{
					font-size:12px;
					font-family:Arial, Helvetica, sans-serif;
					font-weight:bold;
					color:#000000;
					background-color:#FE8944;
				}
				.text_b{
					font-size:16px;
					font-family:Arial, Helvetica, sans-serif;
					font-weight:bold;
					color:#000000;
				}
				.text_16b{
					font-size:16px;
					font-family:Arial, Helvetica, sans-serif;
					font-weight:bold;
					color:#000000;
				}
				.text{
					font-size:14px;
					font-family:Arial, Helvetica, sans-serif;
					background-color:#FFFFFF;
				}
				.orangeFace{
					color:#FE8944;
				}
				.text_15 {
					font-size:15px;
					font-family:Arial, Helvetica, sans-serif;
					
				}
				.text_18 {
					font-size:18px;
					font-family:Arial, Helvetica, sans-serif;
					
				}
				.bottomBorder {
					border-bottom-style:solid; border-bottom:2px;padding-top:4px;padding-bottom:4px;
					font-family:Arial, Helvetica, sans-serif;
				}
				.lightBlue {
					border-bottom-style:solid; border-bottom:2px;
					font-family:Arial, Helvetica, sans-serif;
					background-color:#EAF4FD;
				}
				.midBlue {
					font-family:Arial, Helvetica, sans-serif;
					background-color:#80AFEF;
				}
				.text_orangeb{
					font-weight:bold;
					font-family:Arial, Helvetica, sans-serif;
					background-color:#FFFFFF;
					color:#CB6B43;
				}
				.lightGreen {
					font-size:12px;
					font-family:Arial, Helvetica, sans-serif;
					background-color:#ECF1EA;
				}
				.lightorange {
					font-size:12px;
					font-family:Arial, Helvetica, sans-serif;
					background-color:#CB6B43;
				}
				.midorange {
					font-size:18px;
					font-family:Arial, Helvetica, sans-serif;
					background-color:#FE8944;
				}
				
			</style>
			<page backtop="37mm" backbottom="15mm">			
			<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>';
	
	$stub_tbl_groupTemp = array();
	while($stub_tbl_group_row = imw_fetch_array($stub_tbl_group_res)) {
		$stub_surgeon_name = "";
		$stub_tbl_group_surgeon_name = "";
		$stub_surgeon_name = $stub_tbl_group_row['surgeon_fname'].' '.$stub_tbl_group_row['surgeon_mname'].' '.$stub_tbl_group_row['surgeon_lname'];
		
		if(!in_array($stub_surgeon_name,$stub_tbl_groupTemp)) {
			$stub_tbl_groupTemp[] = $stub_surgeon_name;
			$stub_tbl_group_surgeon_fname[] = $stub_tbl_group_row['surgeon_fname'];
			$stub_tbl_group_surgeon_mname[] = $stub_tbl_group_row['surgeon_mname'];
			$stub_tbl_group_surgeon_lname[] = $stub_tbl_group_row['surgeon_lname'];
		}
	}
	$tmpCnt = 1;
	$prev_sur_name='';
	
	for($s=0;$s<count($stub_tbl_groupTemp);$s++) { 
		$sur_name='';
		$stub_tbl_group_surgeon_lname[$s];
		$stub_tbl_group_surgeon_fname[$s];
		$stub_tbl_group_surgeon_mname[$s];
		$sur_name = $stub_tbl_groupTemp[$s];
		$t++;
		//if($i == 0 || ($intTempProviderId != $intProviderId)) {
			
		//}
		$table.='
			<page_header>
			<table width="100%" border="0" cellpadding="0" cellspacing="0" >
				<tr height="'.$higinc.'" >
					<td  class="text_16b" style="background-color:#CD523F; padding-left:5px; color:white; width:60%; " align="left"   valign="middle" ><b>'.$name.'<br>'.$address.'</b>
					 </td>
					<td style="background-color:#CD523F;width:40%;"  align="right" height="'.$imgheight.'">'.$img_logo[0].'&nbsp;</td>
				 </tr>
			
				<tr height="22" bgcolor="#F1F4F0">
					<td align="right" colspan="2" class="text_16b">Surgery Center Day Report&nbsp;-&nbsp;'.$showDate.'</td>
					
				</tr>
				<tr height="30">
					<td colspan="2" class="text_18"><b>Dr. '.$sur_name.'</b></td>
				</tr>
				<tr >
					<td colspan="2">&nbsp;</td>
				</tr>				
			</table>
			<table width="100%" border="0" cellpadding="0" cellspacing="0">	';	
				if($dType === 'summary')
				{
						$csv_content1 = $name.','."".','."".','."".','."".','."".','."".','."".',Surgery Center Day Report '.$showDate;
						$csv_content2 = $address.','."".','."".','."".','."".','."".','."".','."";
						$csv_content .= "Phys.Name: Dr. ".$sur_name."\n";
						$csv_content .= 'Seq'.','.'"Name"'.','.'"DOB"'.','.'"PickUp"'.','.'"Arrival"'.','.'"Surgery"'.','.'"Procedure"'.','.'"Comments"'.','.'"Anesthesiologist"'."\n";
						
						$table.='<tr  valign="top">
										<td align="left"   class="text_b" width="30">Seq</td>
										<td align="left"   class="text_b" width="150">Name</td>
										<td align="left"   class="text_b" width="110">DOB</td>
										<td align="left"   class="text_b" width="80">PickUp</td>
										<td align="left"   class="text_b" width="80">Arrival</td>
										<td align="left"   class="text_b" width="70">Surgery</td>
										<td align="left"   class="text_b" width="200">Procedure</td>
										<td style="padding-left:20px;" align="left"   class="text_b" width="140">Comments</td>
										<td style="padding-left:10px;" align="left"   class="text_b" width="130">Anesthesiologist</td>
									</tr>';	
				}
				else
				{
						$csv_content1 = $name.','."".','."".','."".','."".','."".',Surgery Center Day Report '.$showDate;
						$csv_content2 = $address.','."".','."".','."".','."".','."".','."".','."";
						$csv_content .= "Phys.Name: Dr. ".$sur_name."\n";
						$csv_content .= 'Seq'.','.'"Patient Name"'.','.'"Patient ID"'.','.'"DOB"'.','.'"Age"'.','
										.'"Street"'.','.'"City"'.','.'"State"'.','.'"Zip"'.','.'"Phone(H)"'.','.'"Phone(W)"'.','
										.'"PickUp"'.','.'"Arrival"'.','.'"Surgery"'.','.'"Procedure"'.','.'"Comments"'.','.'"Anesthesiologist"'."\n";
						
						
						$table.='<tr  valign="top">
										<td align="left"   class="text_b" width="30">Seq</td>
										<td align="left"   class="text_b" width="200">Name</td>
										<td align="left"   class="text_b" width="180">Address</td>
										<td align="left"   class="text_b" width="80">PickUp</td>
										<td align="left"   class="text_b" width="80">Arrival</td>
										<td align="left"   class="text_b" width="70">Surgery</td>
										<td align="left"   class="text_b" width="200">Procedure</td>
										<td style="padding-left:40px;" align="left"   class="text_b" width="120">Comments</td>
									</tr>';
				}
	$table.='</table>
			</page_header>
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				';
			//$report="SELECT * from stub_tbl where surgeon_fname = '".$stub_tbl_group_surgeon_fname[$s]."' and surgeon_mname = '".$stub_tbl_group_surgeon_mname[$s]."' and surgeon_lname = '".$stub_tbl_group_surgeon_lname[$s]."' and dos = '$selected_date' and patient_status!='Canceled' order by surgery_time"	;	
			$report="SELECT *, DATE_FORMAT(patient_dob,'%m/%d/%Y') as patient_dob_format from stub_tbl where surgeon_fname = '".$stub_tbl_group_surgeon_fname[$s]."' and surgeon_mname = '".$stub_tbl_group_surgeon_mname[$s]."' and surgeon_lname = '".$stub_tbl_group_surgeon_lname[$s]."' and dos = '$selected_date' $showAllApptStatusQry $fac_con order by surgery_time"	;	
			$day_rpt=imw_query($report) or die($report.imw_error());
		    $n=@imw_num_rows($day_rpt);
			$a=0;
			$sq=0;
			while($rpt=imw_fetch_array($day_rpt)) {
				$sq++;
				$confi						= 	$rpt['patient_confirmation_id'];
				$patient_id_stub			=	$rpt['patient_id_stub'];
				$patientfname				=	ucwords($rpt['patient_first_name']);
				$patientmname				=	ucwords($rpt['patient_middle_name']);
				$patientlname				=	ucwords($rpt['patient_last_name']);
				$patient_name				=	$patientlname.", ".$patientfname;
				if(trim($patientmname)) {
					$patient_name 			= 	$patient_name." ".trim($patientmname);	
				}
				$patientDOB					=	trim($rpt['patient_dob_format']);
				$patient_dob_temp			=	trim($rpt['patient_dob']);
				$patient_ageNew=$objManageData->dob_calc($patient_dob_temp);
				$stub_tbl_patient_status	=	$rpt['patient_status'];
				//$from_time					=	$rpt['pickup_time'];
				$from_time					=	$objManageData->getTmFormat($rpt['pickup_time']);
				//$to_time					=	$rpt['arrival_time'];
				$to_time					=	$objManageData->getTmFormat($rpt['arrival_time']);
				//$surgeryTime				= 	$rpt['surgery_time'];	
				$surgeryTime				= 	$objManageData->getTmFormat($rpt['surgery_time']);
				$procedure_name				=	$rpt['patient_primary_procedure'];
				$sec_procedure_name			=	$rpt['patient_secondary_procedure'];
				$ter_procedure_name			=	$rpt['patient_tertiary_procedure'];
				$stub_tbl_site				=	$rpt['site'];
				$stub_tbl_secondary_site 	= 	$rpt['stub_secondary_site'];
				$stub_tbl_tertiary_site 	=  	$rpt['stub_tertiary_site'];
				$in_time					=	$rpt['checked_in_time'];
				$out_time					=	$rpt['checked_out_time'];
				$comment					= 	$rpt['comment']; 
				$patientStreet1 			= 	ucwords($rpt['patient_street1']);
				$patientCity 				= 	ucwords($rpt['patient_city']);
				$patientState 				= 	ucwords($rpt['patient_state']);
				$patientZip 				= 	$rpt['patient_zip'];
				$patientHomePhone 			= 	$rpt['patient_home_phone'];
				$patientHomePhone 			=   $objManageData->format_phone($patientHomePhone);
				$patientWorkPhone 			= 	$rpt['patient_work_phone'];
				$patientWorkPhone 			=   $objManageData->format_phone($patientWorkPhone);
				
				$anesthesiologist_title 	= 	"";
				$anesthesiologist_fname 	= 	trim(stripslashes($rpt['anesthesiologist_fname']));
				$anesthesiologist_mname 	= 	trim(stripslashes($rpt['anesthesiologist_mname']));
				$anesthesiologist_lname 	= 	trim(stripslashes($rpt['anesthesiologist_lname']));
				for($p=0; $p<count($arrAnesId);$p++) {
					if(strtolower($anesthesiologist_fname) == strtolower($arrAnesFname[$p]) && strtolower($anesthesiologist_mname) == strtolower($arrAnesMname[$p]) && strtolower($anesthesiologist_lname) == strtolower($arrAnesLname[$p])) {
						$anesthesiologist_title = $arrAnesTitle[$p];
						$anesthesiologist_fname = $arrAnesFname[$p]; 
						$anesthesiologist_mname = $arrAnesMname[$p]; 
						$anesthesiologist_lname = $arrAnesLname[$p];
							
					}
				}
				
				
				//START CODE TO GET ASC-ID, anesthesiologist_id
		     	$getAscQry="SELECT ascId,patientConfirmationId,anesthesiologist_id
						FROM patientconfirmation
						WHERE patientConfirmationId='".$confi."'
						ORDER BY patientConfirmationId DESC LIMIT 0,1";
				$getAscRes=imw_query($getAscQry) or die($getAscQry.imw_error());
				$getAscRow=imw_fetch_array($getAscRes);
				$confirmAnesId = $getAscRow['anesthesiologist_id'];
				//END CODE TO GET ASC-ID
				
				//START GET ANESTHESIOLOGIST IF CONFIRMED
				for($q=0; $q<count($arrAnesId);$q++) {
					if($confirmAnesId == $arrAnesId[$q]) {
						$anesthesiologist_title = $arrAnesTitle[$q];
						$anesthesiologist_fname = $arrAnesFname[$q]; 
						$anesthesiologist_mname = $arrAnesMname[$q]; 
						$anesthesiologist_lname = $arrAnesLname[$q];
					}
				}
				$anesthesiologistName = $anesthesiologist_mname_new="";
				if($anesthesiologist_mname) { $anesthesiologist_mname_new = " ".$anesthesiologist_mname;}
				//$anesthesiologistName = trim($anesthesiologist_title." ".$anesthesiologist_fname.$anesthesiologist_mname_new." ".$anesthesiologist_lname);
				$anesthesiologistName = trim($anesthesiologist_fname.$anesthesiologist_mname_new." ".$anesthesiologist_lname);
				//END GET ANESTHESIOLOGIST IF CONFIRMED
				
				//START CODE TO GET START TIME
				$dischargeQry = "select dischargeTime from postopnursingrecord where confirmation_id=$confi ORDER BY confirmation_id DESC LIMIT 0,1";
				$dischargeRes=imw_query($dischargeQry) or die($dischargeQry.imw_error());
				$dischargeRow=imw_fetch_array($dischargeRes);
				//END CODE TO GET START TIME

				//START CODE TO SET SURGERY-TIME
				$surgery_time='';
				if($surgeryTime!='00:00:00' && $surgeryTime!='0') {
					$surgery_time = $surgeryTime;
				}
				//END CODE TO SET SURGERY-TIME
				
				//START APPEND OS/OD/OU WITH PRIMARY, SECONDARY AND TERTIARY PROCEDURE BASED ON SITE
					if($stub_tbl_site=='left') {
						$procedure_name = $procedure_name.' OS';
					}else if($stub_tbl_site=='right') {
						$procedure_name = $procedure_name.' OD';
					}else if($stub_tbl_site=='both') {
						$procedure_name = $procedure_name.' OU';
					}else if($stub_tbl_site=='left upper lid'){
						$procedure_name = $procedure_name.' LUL';
					}else if($stub_tbl_site=='left lower lid'){
						$procedure_name = $procedure_name.' LLL';
					}else if($stub_tbl_site=='right upper lid'){
						$procedure_name = $procedure_name.' RUL';
					}else if($stub_tbl_site=='right lower lid'){
						$procedure_name = $procedure_name.' RLL';
					}else if($stub_tbl_site=='bilateral upper lid'){
						$procedure_name = $procedure_name.' BUL';
					}else if($stub_tbl_site=='bilateral lower lid'){
						$procedure_name = $procedure_name.' BLL';
					}

					$stub_tbl_secondary_site = trim($stub_tbl_secondary_site) ? trim($stub_tbl_secondary_site) : $stub_tbl_site;
					if($stub_tbl_secondary_site=='left') {
						if(trim($sec_procedure_name)) {$procedure_name .= '<br>'.$sec_procedure_name.' OS';}
					}else if($stub_tbl_secondary_site=='right') {
						if(trim($sec_procedure_name)) {$procedure_name .= '<br>'.$sec_procedure_name.' OD';}
					}else if($stub_tbl_secondary_site=='both') {
						if(trim($sec_procedure_name)) {$procedure_name .= '<br>'.$sec_procedure_name.' OU';}
					}else if($stub_tbl_secondary_site=='left upper lid'){
						if(trim($sec_procedure_name)) {$procedure_name .= '<br>'.$sec_procedure_name.' LUL';}
					}else if($stub_tbl_secondary_site=='left lower lid'){
						if(trim($sec_procedure_name)) {$procedure_name .= '<br>'.$sec_procedure_name.' LLL';}
					}else if($stub_tbl_secondary_site=='right upper lid'){
						if(trim($sec_procedure_name)) {$procedure_name .= '<br>'.$sec_procedure_name.' RUL';}
					}else if($stub_tbl_secondary_site=='right lower lid'){
						if(trim($sec_procedure_name)) {$procedure_name .= '<br>'.$sec_procedure_name.' RLL';}
					}else if($stub_tbl_secondary_site=='bilateral upper lid'){
						if(trim($sec_procedure_name)) {$procedure_name .= '<br>'.$sec_procedure_name.' BUL';}
					}else if($stub_tbl_secondary_site=='bilateral lower lid'){
						if(trim($sec_procedure_name)) {$procedure_name .= '<br>'.$sec_procedure_name.' BLL';}
					}
					
					$stub_tbl_tertiary_site = trim($stub_tbl_tertiary_site) ? trim($stub_tbl_tertiary_site) : $stub_tbl_site;
					if($stub_tbl_tertiary_site=='left') {
						if(trim($ter_procedure_name)) {$procedure_name .= '<br>'.$ter_procedure_name.' OS';}
					}else if($stub_tbl_tertiary_site=='right') {
						if(trim($ter_procedure_name)) {$procedure_name .= '<br>'.$ter_procedure_name.' OD';}
					}else if($stub_tbl_tertiary_site=='both') {
						if(trim($ter_procedure_name)) {$procedure_name .= '<br>'.$ter_procedure_name.' OU';}
					}else if($stub_tbl_tertiary_site=='left upper lid'){
						if(trim($ter_procedure_name)) {$procedure_name .= '<br>'.$ter_procedure_name.' LUL';}
					}else if($stub_tbl_tertiary_site=='left lower lid'){
						if(trim($ter_procedure_name)) {$procedure_name .= '<br>'.$ter_procedure_name.' LLL';}
					}else if($stub_tbl_tertiary_site=='right upper lid'){
						if(trim($ter_procedure_name)) {$procedure_name .= '<br>'.$ter_procedure_name.' RUL';}
					}else if($stub_tbl_tertiary_site=='right lower lid'){
						if(trim($ter_procedure_name)) {$procedure_name .= '<br>'.$ter_procedure_name.' RLL';}
					}else if($stub_tbl_tertiary_site=='bilateral upper lid'){
						if(trim($ter_procedure_name)) {$procedure_name .= '<br>'.$ter_procedure_name.' BUL';}
					}else if($stub_tbl_tertiary_site=='bilateral lower lid'){
						if(trim($ter_procedure_name)) {$procedure_name .= '<br>'.$ter_procedure_name.' BLL';}
					}
					
					
				//END APPEND OS/OD/OU WITH PRIMARY, SECONDARY AND TERTIARY PROCEDURE BASED ON SITE 				
				$comment = wordwrap($comment,40,"<br>",1);
				if(strlen($comment)>200){ $comment = substr($comment,0,200);}
				
				$borderBottomFirstRow = $borderBottomSecondRow = 'bottomBorder';
				if($anesthesiologist_fname) { $borderBottomFirstRow = ''; }
				
				$patientDOBVal		=	"";
				if($patientDOB!="00/00/0000" && $patientDOB!=0) {
					$patientDOBVal 	= 	$patientDOB;	
				}
				
				//START CSV CODE
				$patient_name_csv 	=	'"'.trim($patient_name).'"';
				$comments_csv 		=	'"'.trim(str_ireplace("<br>","  ",$comment)).'"';
				$procedure_name_csv	=	'"'.str_ireplace("<br>","  ",$procedure_name).'"';
				$patientStreet1Csv	=	'"'.stripslashes(htmlentities($patientStreet1)).'"';
				$patientCityCsv 	=	'"'.trim($patientCity).'"';
				$patientStateCsv 	=	'"'.trim($patientState).'"';
				$patientZipCsv 		=	'"'.trim($patientZip).'"';
				$patientHomePhoneCsv=	'"'.stripslashes($patientHomePhone).'"';
				$patientWorkPhoneCsv=	'"'.stripslashes($patientWorkPhone).'"';
				//END CSV CODE
				
				if($dType === 'summary')
				{
					$table.='
						<tr valign="top">
							<td class="'.$borderBottomSecondRow.' text_15" width="30">'.$sq.'</td>
							<td class="'.$borderBottomSecondRow.' text_15" width="150">'.$patient_name.'-'.$patient_id_stub.'</td>
							<td class="'.$borderBottomSecondRow.' text_15" width="110">'.$patientDOBVal.'('.$patient_ageNew.')</td>
							<td class="'.$borderBottomSecondRow.' text_15" align="left" width="80">'.$from_time.'</td>
							<td class="'.$borderBottomSecondRow.' text_15" align="left" width="80">'.$to_time.'</td>
							<td class="'.$borderBottomSecondRow.' text_15" align="left" width="70">'.$surgery_time.'</td>
							<td class="'.$borderBottomSecondRow.' text_15" align="left" width="200">'.$procedure_name.'</td>
							<td style="padding-left:20px;"  class="'.$borderBottomSecondRow.' text_15" align="left" width="140">'.strip_tags($comment,"<br>").'</td>
							<td style="padding-left:10px;"  class="'.$borderBottomSecondRow.' text_15" align="left" width="130">'.$anesthesiologistName.'</td>
							
						</tr>
					';
					$csv_content .= $sq.','.$patient_name_csv.','.$patientDOBVal.','.$from_time.','.$to_time.','.$surgery_time.','.$procedure_name_csv.','.$comments_csv.','.$anesthesiologistName."\n";		
				}
				else
				{
					
					$table.='
						<tr valign="top">
							<td class="'.$borderBottomFirstRow.' text_15" width="30">'.$sq.'</td>
							<td class="'.$borderBottomFirstRow.' text_15" align="left" width="200">
									<b>'.$patient_name.'</b><br><b>Patient ID:</b> '.$patient_id_stub.'<br>
									<b>DOB:</b> '.$patientDOBVal.'<br>
									<b>Age:</b> '.$patient_ageNew.'<br>
							</td>
							<td class="'.$borderBottomFirstRow.' text_15" align="left" width="180">
									'.stripslashes(htmlentities($patientStreet1)).'<br>
									'.stripslashes($patientCity.",".$patientState." ".$patientZip).'<br>
									<b>Phone&nbsp;(H):</b> '.stripslashes($patientHomePhone).'<br>
									<b>Phone&nbsp;(W):</b> '.stripslashes($patientWorkPhone).'
							</td>					
							<td class="'.$borderBottomFirstRow.' text_15" align="left" width="80">'.$from_time.'</td>
							<td class="'.$borderBottomFirstRow.' text_15" align="left" width="80">'.$to_time.'</td>
							<td class="'.$borderBottomFirstRow.' text_15" align="left" width="70">'.$surgery_time.'</td>
							<td class="'.$borderBottomFirstRow.' text_15" align="left" width="200">'.$procedure_name.'</td>
							<td style="padding-left:40px;"  class="'.$borderBottomFirstRow.' text_15" align="left" width="120">'.strip_tags($comment,"<br>").'</td>
						</tr>
					';
					
					$csv_content .= $sq.','.$patient_name_csv.','.$patient_id_stub.','.$patientDOBVal.','.$patient_ageNew.','
									.$patientStreet1Csv.','.$patientCityCsv.','.$patientStateCsv.','.$patientZipCsv.','.$patientHomePhoneCsv.','.$patientWorkPhoneCsv.','
									.$from_time.','.$to_time.','.$surgery_time.','.$procedure_name_csv.','.$comments_csv.','.$anesthesiologistName."\n";		
					if($anesthesiologist_fname) {
						$table.='
							<tr valign="top">
								<td class="'.$borderBottomSecondRow.' text_15" width="30"></td>
								<td class="'.$borderBottomSecondRow.' text_15" align="left" width="200"></td>
								<td class="'.$borderBottomSecondRow.' text_15" align="left" width="180"></td>					
								
								<td colspan="2" class="'.$borderBottomSecondRow.' text_15" align="left" width="160"><b>Anesthesiologist:</b></td>
								<td colspan="2" class="'.$borderBottomSecondRow.' text_15" align="left" width="270">'.$anesthesiologistName.'</td>
								<td style="padding-left:40px;"  class="'.$borderBottomSecondRow.' text_15" align="left" width="120"></td>
							</tr>
						';
					}
				
				}
			    $a++;
			}
			
	    	//$prev_sur_name = $sur_name;
			$table .= '</table></page>';
			if(count($stub_tbl_groupTemp)>$t) {
				//$table .= '<page pageset="old">';
				$table .= '<page backtop="37mm" backbottom="15mm">
							<page_footer>
								<table style="width: 100%;">
									<tr>
										<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
									</tr>
								</table>
							</page_footer>';
			}
		}
		
		
	}

if($_REQUEST['hidd_report_format']=='csv' && $stub_tbl_group_NumRow>0) {
	$file_name=$_SERVER['DOCUMENT_ROOT'].'/'.$surgeryCenterDirectoryName.'/admin/pdfFiles/physician_reportpop.csv';
	if(file_exists($file_name)) {
		@unlink($file_name);
	}
	$fpH1 = fopen($file_name,'w');
	fwrite($fpH1, $csv_content1."\n");
	fwrite($fpH1, $csv_content2."\n\r");
	fwrite($fpH1, $csv_content."\n\r");
	$objManageData->download_file($file_name);
	fclose($fpH1);
	exit;
} else {
	$fileOpen = fopen('new_html2pdf/pdffile.html','w+');
	$intBytes = fputs($fileOpen,$table);
	//echo $table;die;
	fclose($fileOpen);
}

	
?>	
<!DOCTYPE html>
<html>
<head>
<title>Physician Report</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="nofollow">
<meta name="googlebot" content="noindex">	
<link rel="stylesheet" href="css/style_surgery.css" type="text/css" >   

<script language="javascript">
	function submitfn()
	{
		document.printFrm.submit();
	}
</script>
</head>
<body>
<form name="printFrm" action="new_html2pdf/createPdf.php?op=l" method="post">

</form>
<?php 
if($stub_tbl_group_NumRow>0){?>		
	<script type="text/javascript">
        submitfn();
    </script>
<?php 
}else {
?>
	<script>
		if(document.getElementById("loader_tbl")) {
			document.getElementById("loader_tbl").style.display = "none";	
		}
	</script>	
<?php
	if($_REQUEST['hidd_report_format']=='csv') {
	?>
		<script type="text/javascript">
			location.href = "day_report.php?no_record=yes&date12=<?php echo $_REQUEST['date12'];?>&dType=<?php echo $_REQUEST['dType'];?>";
        </script>
    <?php
	}
	?>
	<table style=" font-family:Verdana, Geneva, sans-serif; font-size:12px; background-color:#EAF0F7; width:100%; height:100%;">
		<tr>
			<td class="alignCenter valignTop" style="width:100%;"><b>No Record Found</b></td> 
		</tr>
	</table>
<?php		
}?>
</body>

