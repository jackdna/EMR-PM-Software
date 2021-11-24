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
set_time_limit(900);
include("common/conDb.php");
if($_SESSION["loginUserId"]=="" && $_SESSION['loginUserName']=="") {
	echo '<script>top.location.href="index.php"</script>';
}
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;

function download_file($file_name){
	$filename = $file_name;
	$content_type = "text/csv";
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);
	header("Content-Description: File Transfer");
	header("Content-Type: ".$content_type."; charset=utf-8");
	header("Content-disposition:attachment; filename=\"".$filename."\"");
	header("Content-Length: ".@filesize($filename));
	@readfile($filename) or die("File not found.");
	exit;	
}
$csv_content = "";

$fac_qry	=	" and stb.iasc_facility_id='$_SESSION[iasc_facility_id]' ";
$fac_con	=	($_SESSION['iasc_facility_id']	 ?	$fac_qry	 :	'' )	; 

//$provider_type=$_REQUEST['provider_type'];
$provider_type="";
$provider_data_arr=explode(",",$_REQUEST['provider']);
if(count($provider_data_arr)>0){
	foreach($provider_data_arr as $prov_ids){
		list($provider_id,$provider_type)=explode("@@",$prov_ids);
		if($provider_type=="Surgeon") {
			$confSrgnQryArr[]=$provider_id;
		}else if($provider_type=="Anesthesiologist") {
			$confAnesQryArr[]=$provider_id;
		}else if($provider_type=="Nurse") {
			$confNrsQryArr[]=$provider_id;
		}
		
	}	
}
$or_room_number=trim($_REQUEST['or_room_number']);
if(!trim($provider_data)) {
	$provider_data='all';
}
$startdate=$_REQUEST['startdate'];
$enddate=$_REQUEST['enddate'];
$physician_orders=$_REQUEST['physician_orders']; 
$get_http_path=$_REQUEST['get_http_path'];

if($_SESSION['iasc_facility_id']) {
	//get detail for logged in facility
	$queryFac=imw_query("select * from facility_tbl where fac_id='$_SESSION[facility]'")or die(imw_error());
	$dataFac=imw_fetch_object($queryFac);
	$name=stripcslashes($dataFac->fac_name);
	$address=stripcslashes($dataFac->fac_address1).' '.stripcslashes($dataFac->fac_address2).' '.stripcslashes($dataFac->fac_city).' '.stripcslashes($dataFac->fac_state);
}
	//set surgerycenter detail
			
$SurgeryQry ="select * from surgerycenter where surgeryCenterId=1";
$SurgeryRes = imw_query($SurgeryQry);
while($SurgeryRecord = imw_fetch_array($SurgeryRes)){
	if(!$_SESSION['iasc_facility_id']) {	
		$name = stripslashes($SurgeryRecord['name']);
		$address = stripslashes($SurgeryRecord['address'].' '.$SurgeryRecord['city'].' '.$SurgeryRecord['state']);
	}
	$img = $SurgeryRecord['logoName'];
	$surgeryCenterLogo=$SurgeryRecord['surgeryCenterLogo'];
}
//$file=@fopen('html2pdf/white.jpg','w+');
//@fputs($file,$surgeryCenterLogo);
$bakImgResource = imagecreatefromstring($surgeryCenterLogo);
imagejpeg($bakImgResource,'html2pdf/white.jpg');

$size=getimagesize('html2pdf/white.jpg');
$hig=$size[1];
$wid=$size[0];
$higinc=$hig+10;
$filename='html2pdf/white.jpg';

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

function getTotTime($tH, $tM, $tS){
	$docTime='';
	if($tS>59) {
		$tM+=floor($tS/60);
		$tS=$tS%60;
	}
	if($tM>59) {
		$tH+=floor($tM/60);
		$tM=floor($tM%60);
	}
	
	if($tH>0 || $tM>0 || $tS>0){
		$tH= ($tH<10) ? '0'.$tH : $tH;
		$tM= ($tM<10) ? '0'.$tM : $tM;
		$tS= ($tS<10) ? '0'.$tS : $tS;

		$tH= ($tH==0) ? '00' : $tH;
		$tM= ($tM==0) ? '00' : $tM;
		$tS= ($tS==0) ? '00' : $tS;

		$docTime = $tH.':'.$tM.':'.$tS;
	}
	
	return $docTime;
}

function getConsumeTimeWR($startTime, $endTime){
	$docTime='';
	$seconds = strtotime($endTime) - strtotime($startTime);
	if($seconds<60){
		$seconds= $seconds;
	}else{
		$minutes = floor($seconds/60);
		$seconds = $seconds%60;
		if($minutes>60) {
			$hour=floor($minutes/60);
			$minutes = $minutes%60;
		}else{
			$minutes= $minutes;
		}
	}
	if($hour>0 || $minutes>0 || $seconds>0){
		$hour= ($hour<10) ? '0'.$hour : $hour;
		$minutes= ($minutes<10) ? '0'.$minutes : $minutes;
		$seconds= ($seconds<10) ? '0'.$seconds : $seconds;

		$hour= ($hour==0) ? '00' : $hour;
		$minutes= ($minutes==0) ? '00' : $minutes;
		$seconds= ($seconds==0) ? '00' : $seconds;

		$docTime = $hour.':'.$minutes.':'.$seconds;
	}
	
	return $docTime;
}

//extracting strating date
$from_date = $to_date = "";
if($startdate!=0){
	$start_date=explode("-",$startdate);
	$start_date[0];
	$start_date[1];
	$start_date[2];
	$from_date=$start_date[2]."-".$start_date[0]."-".$start_date[1];
}
//extracting end date
$enddate=$_REQUEST['enddate'];
if($enddate!=0){
	$end_date=explode("-",$enddate);
	$end_date[0];
	$end_date[1];
	$end_date[2];
	$to_date=$end_date[2]."-".$end_date[0]."-".$end_date[1];
}



$procedure_tbl=imw_query("select procedureId, name from procedures where name='$procedure'");
$proc=imw_fetch_array($procedure_tbl);
$procedure_id=$proc['procedureId'];
$procedure_name=$proc['name'];	

$physician=imw_query("select usersId, fname, mname, lname from users where usersId= IN($provider_data)");
while($physician1=imw_fetch_array($physician)){
	$physician_id=$physician1['usersId'];
	$physician_fname= $physician1['fname'];
	$physician_mname= $physician1['mname'];
	$physician_lname= $physician1['lname'];
	$provider_name=stripslashes($physician_lname.", ".$physician_fname);
}		

$current_date=date("m-d-Y");
$img_logo = showThumbImages('new_html2pdf/white.jpg',170,50);
$imgheight= $img_logo[2]+8;
$imgwidth= $img_logo[1]+200;	

$andSearchConfIdQry = "";




$confPhyQry="";
$confPhyORQry="";
$boolProviderExist=false;
if($provider_data) { 
	if(count($confSrgnQryArr)>0){
		$surgeon_ids=implode(",",$confSrgnQryArr);
		$confPhyORQry .= " OR pc.surgeonId IN(".$surgeon_ids.") ";
		$boolProviderExist=true;
	}
	if(count($confAnesQryArr)>0) {
		$anes_ids=implode(",",$confAnesQryArr);
		$confPhyORQry .= " OR pc.anesthesiologist_id IN(".$anes_ids.") ";
		$boolProviderExist=true;
	}
	if(count($confNrsQryArr)>0) {
		$nurse_ids=implode(",",$confNrsQryArr);
		$confPhyORQry .= " OR pc.nurseId IN(".$nurse_ids.") ";
		$boolProviderExist=true;
	}
	$confPhyORQry = substr($confPhyORQry,3);
	if($boolProviderExist) {
		$confPhyQry=" AND ( ".$confPhyORQry.")  ";
	}
}

$dtQry = "";
if($from_date && $to_date) {
	$dtQry = " AND (pc.dos BETWEEN '".$from_date."' AND '".$to_date."')";	
}else if($from_date && !$to_date) {
	$dtQry = " AND (pc.dos >= '".$from_date."')";	
}else if(!$from_date && $to_date) {
	$dtQry = " AND (pc.dos <= '".$from_date."')";	
}

$oprJoinQry = "";
if($or_room_number) {
	$oprJoinQry = " INNER JOIN operatingroomrecords opr ON (opr.confirmation_id = pc.patientConfirmationId AND opr.surgeryORNumber='".$or_room_number."') ";
}

//START GET TIME SPENT BY SURGEON IN EACH APPOINTMENT
$srgnTimeSpentQry = "
SELECT c1.confirmation_id,c1.chart_open_time,c1.chart_close_time , 
(SELECT c2.chart_close_time AS close_time FROM chart_log AS c2 
WHERE c2.confirmation_id = c1.confirmation_id 
and c2.operator_type='Surgeon' 
AND c1.chart_open_date = c2.chart_close_date
AND c2.chart_close_date != '0000-00-00' 
ORDER by chart_close_date desc,chart_close_time desc limit 0,1) AS real_close_time, 
timediff( (SELECT c2.chart_close_time AS close_time FROM chart_log AS c2 
WHERE c2.confirmation_id = c1.confirmation_id 
AND c2.operator_type='Surgeon' 
AND c1.chart_open_date = c2.chart_close_date
AND c2.chart_close_date != '0000-00-00' 
ORDER by chart_close_date desc,chart_close_time desc limit 0,1), c1.chart_open_time ) AS surgeon_time_difference
FROM chart_log AS c1
INNER JOIN patientconfirmation pc on (pc.patientConfirmationId=c1.confirmation_id and pc.dos=c1.chart_open_date and c1.operator_type = 'Surgeon' ".$dtQry.")
WHERE c1.operator_type = 'Surgeon'
AND c1.chart_open_date != '0000-00-00'
AND c1.chart_close_time != ' 00:00:00'
GROUP BY c1.confirmation_id
ORDER BY c1.chart_open_date, c1.chart_open_time
";
$srgnTimeSpentArr = array();
$srgnTimeSpentRes = imw_query($srgnTimeSpentQry);
if(imw_num_rows($srgnTimeSpentRes)>0){
	while($srgnTimeSpentRow = imw_fetch_assoc($srgnTimeSpentRes)) {
		$srgnTimeSpentConfId= $srgnTimeSpentRow['confirmation_id'];
		$srgnTimeSpentDiff 	= $srgnTimeSpentRow['surgeon_time_difference'];
		$srgnTimeSpentArr[$srgnTimeSpentConfId] = $srgnTimeSpentDiff;
	}
}
//END GET TIME SPENT BY SURGEON IN EACH APPOINTMENT

//START GET TIME SPENT BY NURSE IN EACH APPOINTMENT
$nrsTimeSpentQry = "
SELECT c1.confirmation_id,c1.chart_open_time,c1.chart_close_time , 
(SELECT c2.chart_close_time AS close_time FROM chart_log AS c2 
WHERE c2.confirmation_id = c1.confirmation_id 
and c2.operator_type='Nurse' 
AND c1.chart_open_date = c2.chart_close_date
AND c2.chart_close_date != '0000-00-00' 
ORDER by chart_close_date desc,chart_close_time desc limit 0,1) AS real_close_time, 
timediff( (SELECT c2.chart_close_time AS close_time FROM chart_log AS c2 
WHERE c2.confirmation_id = c1.confirmation_id 
AND c2.operator_type='Nurse' 
AND c1.chart_open_date = c2.chart_close_date
AND c2.chart_close_date != '0000-00-00' 
ORDER by chart_close_date desc,chart_close_time desc limit 0,1), c1.chart_open_time ) AS surgeon_time_difference
FROM chart_log AS c1
inner join patientconfirmation pc on (pc.patientConfirmationId=c1.confirmation_id and pc.dos=c1.chart_open_date and c1.operator_type = 'Nurse' ".$dtQry.")
WHERE c1.operator_type = 'Nurse'
AND c1.chart_open_date != '0000-00-00'
AND c1.chart_close_time != ' 00:00:00'
GROUP BY c1.confirmation_id
ORDER BY c1.chart_open_date, c1.chart_open_time
";
$nrsTimeSpentArr = array();
$nrsTimeSpentRes = imw_query($nrsTimeSpentQry);
if(imw_num_rows($nrsTimeSpentRes)>0){
	while($nrsTimeSpentRow = imw_fetch_assoc($nrsTimeSpentRes)) {
		$nrsTimeSpentConfId= $nrsTimeSpentRow['confirmation_id'];
		$nrsTimeSpentDiff 	= $nrsTimeSpentRow['surgeon_time_difference'];
		$nrsTimeSpentArr[$nrsTimeSpentConfId] = $nrsTimeSpentDiff;
	}
}
//END GET TIME SPENT BY NURSE IN EACH APPOINTMENT

//START GET TIME SPENT BY ANESTHESIOLOGIST IN EACH APPOINTMENT
$anesTimeSpentQry = "
SELECT c1.confirmation_id,c1.chart_open_time,c1.chart_close_time , 
(SELECT c2.chart_close_time AS close_time FROM chart_log AS c2 
WHERE c2.confirmation_id = c1.confirmation_id 
and c2.operator_type='Anesthesiologist' 
AND c1.chart_open_date = c2.chart_close_date
AND c2.chart_close_date != '0000-00-00' 
ORDER by chart_close_date desc,chart_close_time desc limit 0,1) AS real_close_time, 
timediff( (SELECT c2.chart_close_time AS close_time FROM chart_log AS c2 
WHERE c2.confirmation_id = c1.confirmation_id 
AND c2.operator_type='Anesthesiologist' 
AND c1.chart_open_date = c2.chart_close_date
AND c2.chart_close_date != '0000-00-00' 
ORDER by chart_close_date desc,chart_close_time desc limit 0,1), c1.chart_open_time ) AS surgeon_time_difference
FROM chart_log AS c1
inner join patientconfirmation pc on (pc.patientConfirmationId=c1.confirmation_id and pc.dos=c1.chart_open_date and c1.operator_type = 'Anesthesiologist' ".$dtQry.")
WHERE c1.operator_type = 'Anesthesiologist'
AND c1.chart_open_date != '0000-00-00'
AND c1.chart_close_time != ' 00:00:00'
GROUP BY c1.confirmation_id
ORDER BY c1.chart_open_date, c1.chart_open_time
";
$anesTimeSpentArr = array();
$anesTimeSpentRes = imw_query($anesTimeSpentQry);
if(imw_num_rows($anesTimeSpentRes)>0){
	while($anesTimeSpentRow = imw_fetch_assoc($anesTimeSpentRes)) {
		$anesTimeSpentConfId= $anesTimeSpentRow['confirmation_id'];
		$anesTimeSpentDiff 	= $anesTimeSpentRow['surgeon_time_difference'];
		$anesTimeSpentArr[$anesTimeSpentConfId] = $anesTimeSpentDiff;
	}
}
//END GET TIME SPENT BY ANESTHESIOLOGIST IN EACH APPOINTMENT

$provider_report = "
SELECT pc.patientConfirmationId, pc.ascId, stb.surgery_time, stb.checked_in_time, stb.checked_out_time,
if(stb.checked_out_time!= '',TIMEDIFF(TIME(STR_TO_DATE(stb.checked_out_time, '%h:%i %p')),TIME(STR_TO_DATE(stb.checked_in_time, '%h:%i %p'))),'') AS total_time,
pdt.patient_fname,pdt.patient_mname,pdt.patient_lname,
CONCAT( usr.lname, ', ', usr.fname ) 	AS provider_name,
CONCAT( anes.lname, ', ', anes.fname ) 	AS anes_name,
CONCAT( nrs.lname, ', ', nrs.fname ) 	AS nurse_name
FROM  patientconfirmation pc
".$oprJoinQry."
LEFT JOIN patient_data_tbl pdt ON(pdt.patient_id = pc.patientId)
LEFT JOIN users usr ON (usr.usersId = pc.surgeonId)
LEFT JOIN users anes ON (anes.usersId = pc.anesthesiologist_id)
LEFT JOIN users nrs ON (nrs.usersId = pc.nurseId)
LEFT JOIN stub_tbl stb ON (stb.patient_confirmation_id = pc.patientConfirmationId)
WHERE pc.patientConfirmationId!='0' AND pc.surgeonId !='' ".$confPhyQry.$dtQry . $fac_con;
//echo $provider_report; 
$physician=@imw_query($provider_report) or die(imw_error());//echo ' <br>numrow - '.imw_num_rows($physician);die;
if(@imw_num_rows($physician)>0){
	
}else{
	$msg="No Record found!";
}
	//echo $provider_report;exit;
	
	$rows=@imw_num_rows($physician); 
	$t=0;
	if(@imw_num_rows($physician)>0){
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
					.text_11 {
						font-size:12px;
						font-family:Arial, Helvetica, sans-serif;
						
					}
					.text_12 {
						font-size:12px;
						font-family:Arial, Helvetica, sans-serif;
						
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
						border-bottom-style:solid; border-bottom:1px;padding-top:4px;padding-bottom:4px;
						font-family:Arial, Helvetica, sans-serif;
					}
					.rightBorder {
						border-right-style:solid;border-right:1px;
						font-family:Arial, Helvetica, sans-serif;
					}
					.leftBorder {
						border-left-style:solid;border-left:1px;
						font-family:Arial, Helvetica, sans-serif;
					}
					.topBorder {
						border-top-style:solid;border-top:1px;
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
					.alignLeft {
						text-align:left;
					}
					
				</style>
				<page backtop="45mm" backbottom="15mm">			
				<page_footer>
					<table style="width:1000px;">
						<tr>
							<td style="text-align:center;width:1000px;">Page [[page_cu]]/[[page_nb]]</td>
						</tr>
					</table>
				</page_footer>';
			while($rpt=imw_fetch_array($physician)) {
				$report_srgn[] = $rpt;
				if(!in_array($rpt['provider_name'],$provider_name_arr)) {
					$provider_name_arr[] = $rpt['provider_name'];
				}
			}//print"<pre>";print_r($provider_name_arr);die();
			$totAppts=0;
			$grandTot = array();
			foreach($provider_name_arr as $provider_name) {
				$a=1;
				$t++;
				$provider_name1 = str_ireplace(","," ",$provider_name);
				$table.='
				<page_header>
					<table style="width:1000px;" width="100%" border="0" cellpadding="0" cellspacing="0" >
						<tr height="'.$higinc.'" >
							<td  class="text_16b" width="750" style="background-color:#CD523F; padding-left:5px; color:white "  align="left"   valign="middle" ><b>'.$name.'<br>'.$address.'</b>
							 </td>
							<td style="background-color:#CD523F;"  align="right" height="'.$imgheight.'" width="'.$imgwidth.'">'.$img_logo[0].'</td>
						</tr>
						<tr style="background-color:#FFFFFF;padding-top:5px;"><td></td></tr>
						<tr height="22" >
							<td width="100%" style="background-color:#F1F4F0;" align="right" colspan="5" class="text_16b">'.$current_date.'</td>
						</tr>
						<tr height="25">
							<td colspan="2" class="text_15" style="background-color:#F1F4F0;">
								<table style="width:1000px;" border="0" cellpadding="0" cellspacing="0">	
									<tr height="22" bgcolor="#F1F4F0">
										<td align="left" width="300" nowrap><b>Phys.Name:&nbsp;'.$provider_name.'</b></td>';
										$from_dateNew = $to_dateNew = "";
										if($from_date!='' && $to_date!='')
										{
										   $from_dateNew 	=  $objManageData->changeDateMDY($from_date);
										   $to_dateNew 		=  $objManageData->changeDateMDY($to_date);
										   $table.='
											   <td align="right" style=" padding-right:5px; "><b>From&nbsp;'.$from_dateNew.'</b></td>
											   <td align="right" style=" padding-right:5px; "><b>To</b></td>
											   <td align="right" style=" padding-right:40px; "><b>'.$to_dateNew.'</b></td>
											';
										}	
										else
										{
											$table.='<td colspan="3" width="200">&nbsp;</td>';
										}
										$csv_content1 = $name.','."".','."".','."".','."".','."".','."".','."".','."".','.$current_date;
										$csv_content2 = $address.','."".','."".','."".','."".','."".','."".','."".','."".','."";
										$csv_content .= "Phys.Name: ".$provider_name1.','."From ".$from_dateNew." To ".$to_dateNew."\n";
										//$csv_content .= "Phys.Name: ".$provider_name1."\n";
										$csv_content .= 'Seq'.','.'"Patient Name"'.','.'"ASC-id"'.','.'"CI Time"'.','.'"Waiting Room"'.','.'"Nurse"'.','.'"Anesthesiologist"'.','.'"Surgeon"'.','.'"CO Time"'.','.'"Total Time"'.','."\n";
										$table.='
										<td align="left"  height="40" ></td>
										
									</tr>
								</table>						
							</td>
						</tr>
					</table>
					<table style="width:1000px;" border="0" cellpadding="0" cellspacing="0">		
						<tr  valign="top">
							<td class="text_b alignLeft" style="width:30px;">Seq</td>
							<td class="text_b alignLeft" style="width:210px;">Patient Name</td>
							<td class="text_b alignLeft" style="width:80px;">ASC-id</td>
							<td class="text_b alignLeft" style="width:80px;">CI Time</td>
							<td class="text_b alignLeft" style="width:120px;">Waiting&nbsp;Room</td>
							<td class="text_b alignLeft" style="width:75px;">Nurse</td>
							<td class="text_b alignLeft" style="width:135px;">Anesthesiologist</td>
							<td class="text_b alignLeft" style="width:90px; ">Surgeon</td>
							<td class="text_b alignLeft" style="width:80px;">CO Time</td>
							<td class="text_b alignLeft" style="width:120px;">Total Time</td>
						</tr>
					</table>
				</page_header>
				<table style="width:1000px;" border="0" cellpadding="0" cellspacing="0">
				';
				$arrSubTot = array();
				$docTotAppts=0;
				$recordExist=false;
				foreach($report_srgn as $report) {
					if($provider_name==$report['provider_name']) {	
						$recordExist = true;
						$asc_id=$report['ascId'];
						if($asc_id == '0') {$asc_id=""; }
						$patient_id=$report['patientId'];
						$confirmation_id	= $report['patientConfirmationId'];
						$nurseTimeSpent 	= $nrsTimeSpentArr[$confirmation_id];
						$anesTimeSpent 		= $anesTimeSpentArr[$confirmation_id];
						$surgeonTimeSpent 	= $srgnTimeSpentArr[$confirmation_id];
						$procedure_pname	= $report['patient_primary_procedure'];
						$procedure_sname	= $report['procedure_secondary_procedure'];
						
						$checked_in_time=$objManageData->getTmFormat($report['checked_in_time']);
						$checked_out_time=$objManageData->getTmFormat($report['checked_out_time']);
						$total_time=$report['total_time'];	
						
						
						$nurse_tt = explode(':', $nurseTimeSpent);
						$arrSubTot['NURSE']['TOT_HR']+=$nurse_tt[0];
						$arrSubTot['NURSE']['TOT_MIN']+=$nurse_tt[1];
						$arrSubTot['NURSE']['TOT_SEC']+=$nurse_tt[2];

						$anes_tt = explode(':', $anesTimeSpent);
						$arrSubTot['ANES']['TOT_HR']+=$anes_tt[0];
						$arrSubTot['ANES']['TOT_MIN']+=$anes_tt[1];
						$arrSubTot['ANES']['TOT_SEC']+=$anes_tt[2];

						$srgn_tt = explode(':', $surgeonTimeSpent);
						$arrSubTot['SURGEON']['TOT_HR']+=$srgn_tt[0];
						$arrSubTot['SURGEON']['TOT_MIN']+=$srgn_tt[1];
						$arrSubTot['SURGEON']['TOT_SEC']+=$srgn_tt[2];
						
						$all_tt = explode(':', $total_time);
						$arrSubTot['TOTAL_TIME']['TOT_HR']+=$all_tt[0];
						$arrSubTot['TOTAL_TIME']['TOT_MIN']+=$all_tt[1];
						$arrSubTot['TOTAL_TIME']['TOT_SEC']+=$all_tt[2];
						
						
						
						//FINDING PROCEUDRE NAME TO BE APPLIED
						if($procedure_sname && $procedure_sname<>"N/A"){
							$procedure_name=$procedure_pname.","."<br>".$procedure_sname;
						}else{
							$procedure_name=$procedure_pname;
						}
						//END
						$dos=$report['dos'];
						//exploding date of surgery
						$date=explode("-",$dos);
						$date[0];
						$date[1];
						$date[2];
						$date_of_surgery=$date[1]."-".$date[2]."-".$date[0];
						//end of exploding
						$site=$report['site']; 
						//APPLYING NUMBERS TO SITE
						if($site==1){
							$eyesite="Left eye";
						}elseif($site==2){
							$eyesite="Right eye";
						}elseif($site==3){
							$eyesite="Both eyes";
						} //END
						
						//CODE FOR PATIENT NAME FROM PATIENT_DATA_TBL
						$patient_fname=$report['patient_fname'];
						$patient_mname=$report['patient_mname'];
						$patient_lname=$report['patient_lname'];
						$patient_name=$patient_lname.", ".$patient_fname;
						//END OF CODE
						

						
						//CODE FOR STARTING TIME FROM PREOPNURSINGRECORD TABLE
						$from_time=$report['preopnursingSaveDateTime'];
						$frm_time=explode(" ",$from_time);
						$frm_time[0];
						$frm_time[1];
						$start_time1=explode(":",$frm_time[1]);
						if($start_time1[0]>=12){
							$am_pm = "PM";
						}else{
							$am_pm = "AM";
						}	      
						if($start_time1[0]>=13){
							$start_time1[0]=$start_time1[0]-12;
							if(strlen($start_time1[0])==1){
								$start_time1[0] = "0".$start_time1[0];
							}
						}
						if($start_time1[0]>1){
							$start_time=$start_time1[0].":".$start_time1[1]." ".$am_pm;
						}else{
							$start_time='';
						}
						//END OF CODE
						
						//CODE FOR ENDING TIME FROM DISCHARGESUMMARYSHEET TABLE
						$to_time=$report['summarySaveDateTime'];
						$totime=explode(" ",$to_time);
						$totime[0];
						$totime[1];
						$end_time1=explode(":",$totime[1]);
						if($end_time1[0]>=12){
							$am_pm = "PM";
						}else {
							$am_pm = "AM";
						}	      
						if($end_time1[0]>=13){
							$end_time1[0]=$end_time1[0]-12;
							if(strlen($end_time1[0])==1){
								$end_time1[0] = "0".$end_time1[0];
							}				
						}
						if($end_time1[0]>=1){
							$end_time=$end_time1[0].":".$end_time1[1]."".$am_pm;
						}else{
							$end_time='';
						}
						//END OF CODE	
						//Set Nbsp in Seq & Name
						if($i<10){
							$nbsp="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
						}elseif($i>=10 && $i<100){
							$nbsp="&nbsp;&nbsp;&nbsp;&nbsp;";
						}elseif($i>100  && $i<1000){
							$nbsp=" ";
						}
						//Set Nbsp in Seq & Name
						if($i<=$num){	
							$j=$i+1;
							$borderBottomFirstRow = 'bottomBorder leftBorder';
							if($docTotAppts==0) { $borderBottomFirstRow = 'bottomBorder topBorder leftBorder';}
							$table.='
								<tr valign="top">
									<td class="'.$borderBottomFirstRow.' text_15 alignLeft" style="width:30px;">'.$a.'</td>
									<td class="'.$borderBottomFirstRow.' text_15 alignLeft" style="width:210px;">'.$patient_name.'</td>					
									<td class="'.$borderBottomFirstRow.' text_15 alignLeft" style="width:60px;">'.$asc_id.'</td>
									<td class="'.$borderBottomFirstRow.' text_15 alignLeft" style="width:80px;">'.$checked_in_time.'</td>
									<td class="'.$borderBottomFirstRow.' text_15 alignLeft" style="width:120px;">'.'-'.'</td>
									<td class="'.$borderBottomFirstRow.' text_15 alignLeft" style="width:90px;">'.$nurseTimeSpent.'</td>
									<td class="'.$borderBottomFirstRow.' text_15 alignLeft" style="width:120px;">'.$anesTimeSpent.'</td>
									<td class="'.$borderBottomFirstRow.' text_15 alignLeft" style="width:90px;">'.$surgeonTimeSpent.'</td>
									<td class="'.$borderBottomFirstRow.' text_15 alignLeft" style="width:80px;">'.$checked_out_time.'</td>
									<td class="'.$borderBottomFirstRow.' rightBorder text_15 alignLeft" style="width:100px;">'.$total_time.'</td>
								</tr>
							';
						$csv_content .= $a.','.str_ireplace(","," ",$patient_name).','.$asc_id.','.$checked_in_time.','."-".','.$nurseTimeSpent.','.$anesTimeSpent.','.$surgeonTimeSpent.','.$checked_out_time.','.$total_time."\n";	
							
						}//ending of if statement
						$a++;
						$docTotAppts++;
						$totAppts++;
					}
					
				}
				if($recordExist == true) {
					//$arrSubTot['NURSE']['TOT_HR']=12;$arrSubTot['NURSE']['TOT_MIN']=51;$arrSubTot['NURSE']['TOT_SEC']=00;$docTotAppts=26;
					
					//START NURSE TIMES
					$totMin = ($arrSubTot['NURSE']['TOT_HR']*60) + $arrSubTot['NURSE']['TOT_MIN'];
					$nurseTime = getTotTime('00', $totMin, $arrSubTot['NURSE']['TOT_SEC']);
					$totSec = ($totMin*60) + $arrSubTot['NURSE']['TOT_SEC'];
					$ts = round($totSec / $docTotAppts);
					$nurseAvgTime = getTotTime('00', '00', $ts);
					//END NURSE TIMES
	
					//START ANES TIMES 
					$totMin = ($arrSubTot['ANES']['TOT_HR']*60) + $arrSubTot['ANES']['TOT_MIN'];
					$anesTime = getTotTime('00', $totMin, $arrSubTot['ANES']['TOT_SEC']);
					$totSec = ($totMin*60) + $arrSubTot['ANES']['TOT_SEC'];
					$ts = round($totSec / $docTotAppts);
					$anesAvgTime = getTotTime('00', '00', $ts);
					//END ANES TIMES
	
					//START SURGEON TIMES 
					$totMin = ($arrSubTot['SURGEON']['TOT_HR']*60) + $arrSubTot['SURGEON']['TOT_MIN'];
					$surgeonTime = getTotTime('00', $totMin, $arrSubTot['SURGEON']['TOT_SEC']);
					$totSec = ($totMin*60) + $arrSubTot['SURGEON']['TOT_SEC'];
					$ts = round($totSec / $docTotAppts);
					$surgeonAvgTime = getTotTime('00', '00', $ts);
					//END SURGEON TIMES
	
					//START TOTAL TIMES FROM CHECK-IN TO CHEK-OUT
					$totMin = ($arrSubTot['TOTAL_TIME']['TOT_HR']*60) + $arrSubTot['TOTAL_TIME']['TOT_MIN'];
					$checkoutTotalTime = getTotTime('00', $totMin, $arrSubTot['TOTAL_TIME']['TOT_SEC']);
					$totSec = ($totMin*60) + $arrSubTot['TOTAL_TIME']['TOT_SEC'];
					$ts = round($totSec / $docTotAppts);
					$checkoutAvgTime = getTotTime('00', '00', $ts);
					//END TOTAL TIMES FROM CHECK-IN TO CHEK-OUT
					
					//START CREATING ARRAY OF GRAND TOTAL TIMES FROM CHECK-IN TO CHEK-OUT
					$grand_tt = explode(':', $checkoutTotalTime);
					$grandTot['GRAND_TOTAL_TIME']['TOT_HR']+=$grand_tt[0];
					$grandTot['GRAND_TOTAL_TIME']['TOT_MIN']+=$grand_tt[1];
					$grandTot['GRAND_TOTAL_TIME']['TOT_SEC']+=$grand_tt[2];
					//END CREATING ARRAY OF GRAND TOTAL TIMES FROM CHECK-IN TO CHEK-OUT
					
					$table.='
									<tr valign="top">
										<td class="'.$borderBottomFirstRow.' text_11 alignLeft" colspan="5"></td>
										<td class="'.$borderBottomFirstRow.' text_11 alignLeft" style="width:80px;"><b>Total:</b> '.$nurseTime.'<br><b>Avg:&nbsp;&nbsp;</b> '.$nurseAvgTime.'</td>
										<td class="'.$borderBottomFirstRow.' text_11 alignLeft" style="width:120px;"><b>Total:</b> '.$anesTime.'<br><b>Avg:&nbsp;&nbsp;</b> '.$anesAvgTime.'</td>
										<td class="'.$borderBottomFirstRow.' text_11 alignLeft" style="width:80px;"><b>Total:</b> '.$surgeonTime.'<br><b>Avg:&nbsp;&nbsp;</b> '.$surgeonAvgTime.'</td>
										<td class="'.$borderBottomFirstRow.' text_11 alignLeft" style="width:80px;"></td>
										<td class="'.$borderBottomFirstRow.' rightBorder text_11 alignLeft" style="width:100px;"><b>Total:</b> '.$checkoutTotalTime.'<br><b>Avg:&nbsp;&nbsp;</b> '.$checkoutAvgTime.'</td>
									</tr>
								';
								
								$csv_content .= "".','."".','."".','."".','."".','."".','."Total: ".$nurseTime."".','."Total: ".$anesTime."".','."Total: ".$surgeonTime.','."Total: ".$checkoutTotalTime.""."\n";
								$csv_content .= "".','."".','."".','."".','."".','."".','."Avg: ".$nurseAvgTime.','."Avg: ".$anesAvgTime.','."Avg: ".$surgeonAvgTime.','."Avg: ".$checkoutAvgTime."\n";
					if(count($report_srgn)==$totAppts){
						
						//START GRAND TOTAL TIMES FROM CHECK-IN TO CHEK-OUT
						$totMin = ($grandTot['GRAND_TOTAL_TIME']['TOT_HR']*60) + $grandTot['GRAND_TOTAL_TIME']['TOT_MIN'];
						$grandTotalTime = getTotTime('00', $totMin, $grandTot['GRAND_TOTAL_TIME']['TOT_SEC']);
						$totSec = ($totMin*60) + $grandTot['GRAND_TOTAL_TIME']['TOT_SEC'];
						$ts = round($totSec / $totAppts);
						$grandAvgTime = getTotTime('00', '00', $ts);
						//END GRAND TOTAL TIMES FROM CHECK-IN TO CHEK-OUT
						
						$table.='
									<tr valign="top">
										<td class="'.$borderBottomFirstRow.' rightBorder text_15 alignLeft" colspan="10"><b>Total Appointments:</b>&nbsp;'.$totAppts.'</td>
									</tr>
									<tr valign="top">
										<td class="'.$borderBottomFirstRow.' rightBorder text_15 alignLeft" colspan="10"><b>Average time:</b>&nbsp;'.$grandAvgTime.'</td>
									</tr>
								';
								$csv_content3 .= "Total Appointments: ".$totAppts."\n";
								$csv_content3 .= "Average time: ".$grandAvgTime."\n";
					}
				}
				
				$table .= '</table></page>';
				if(count($provider_name_arr) > $t){
					$table .= '<page backtop="45mm" backbottom="15mm">			
						<page_footer>
							<table style="width:1000px;">
								<tr>
									<td style="text-align:center;width:1000px;">Page [[page_cu]]/[[page_nb]]</td>
								</tr>
							</table>
						</page_footer>';					
				}
			}
			
		}
		
	if($_REQUEST['patient_save']=='yes') {
		if(@imw_num_rows($physician)>0){
			$file_name="admin/pdfFiles/patient_reportpop.csv";
			@unlink($file_name);
			$fpH1 = fopen($file_name,'w');
			fwrite($fpH1, $csv_content1."\n\r");
			fwrite($fpH1, $csv_content2."\n\r");
			fwrite($fpH1, $csv_content."\n\r");
			fwrite($fpH1, $csv_content3."\n\r");
			download_file($file_name);
			fclose($fpH1);
			exit;
		}
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
	window.focus();
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
if(@imw_num_rows($physician)>0){?>		
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
	if($_REQUEST['patient_save']=='yes') {
	?>
	<script type="text/javascript">
        location.href = "patient_monitor_report.php?no_record=yes";
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
</html>
