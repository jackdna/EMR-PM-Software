<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
session_start();
set_time_limit(900);
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

include("common/conDb.php"); 
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
	//die();
	header("Content-disposition:attachment; filename=\"".$filename."\"");
	
	header("Content-Length: ".@filesize($filename));
	//echo filesize($filename);
	@readfile($filename) or die("File not found.");
	exit;	
}
$csv_content = "";
//$csv_content1 = "";


if($_SESSION['iasc_facility_id'])
{
	$fac_con=" and stub_tbl.iasc_facility_id='$_SESSION[iasc_facility_id]'"; 
}
$procedure=$_REQUEST['procedure'];
$physician_data=$_REQUEST['physician'];
if(!trim($physician_data)) {
	$physician_data='all';
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
$file=@fopen('html2pdf/white.jpg','w+');
@fputs($file,$surgeryCenterLogo);
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
//extracting strating date
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

$physician=imw_query("select usersId, fname, mname, lname from users where usersId= IN($physician_data)");
while($physician1=imw_fetch_array($physician)){
	$physician_id=$physician1['usersId'];
	$physician_fname= $physician1['fname'];
	$physician_mname= $physician1['mname'];
	$physician_lname= $physician1['lname'];
	$physician_name=stripslashes($physician_lname.", ".$physician_fname);
}		

$current_date=date("m-d-Y");
$img_logo = showThumbImages('new_html2pdf/white.jpg',170,50);
$imgheight= $img_logo[2]+8;
$imgwidth= $img_logo[1]+200;	


//START CODE TO SEARCH A FREE STRING VALUE
$searchConfIdArr = array();
$andSearchConfIdQry = "";
if(trim($physician_orders)) {
	
	//SEARCH FROM PREOP-PHYSICIAN ORDER
	$preOpPhyQry = "SELECT patient_confirmation_id FROM preopphysicianorders WHERE preOpOrdersOther like '%".$physician_orders."%' OR comments like '%".$physician_orders."%'";
	$preOpPhyRes = imw_query($preOpPhyQry);
	if(imw_num_rows($preOpPhyRes)>0) {
		while($preOpPhyRow=imw_fetch_array($preOpPhyRes)){
			$searchConfIdArr[] = $preOpPhyRow["patient_confirmation_id"];
		}
	}
	$preOpMedQry = "SELECT patient_confirmation_id FROM patientpreopmedication_tbl WHERE medicationName like '%".$physician_orders."%' OR strength like '%".$physician_orders."%' OR direction like '%".$physician_orders."%'";
	$preOpMedRes = imw_query($preOpMedQry);
	if(imw_num_rows($preOpMedRes)>0) {
		while($preOpMedRow=imw_fetch_array($preOpMedRes)){
			$searchConfIdArr[] = $preOpMedRow["patient_confirmation_id"];
		}
	}
	
	//SEARCH FROM POSTOP-PHYSICIAN ORDER	
	$postOpPhyQry = "SELECT patient_confirmation_id FROM postopphysicianorders WHERE patientToTakeHome like '%".$physician_orders."%' OR comment like '%".$physician_orders."%'";
	$postOpPhyRes = imw_query($postOpPhyQry);
	if(imw_num_rows($postOpPhyRes)>0) {
		while($postOpPhyRow=imw_fetch_array($postOpPhyRes)){
			$searchConfIdArr[] = $postOpPhyRow["patient_confirmation_id"];
		}
	}
	
	//SEARCH FROM OPERATING-ROOM ORDER
	$opRoomQry = "SELECT confirmation_id FROM operatingroomrecords WHERE preOpDiagnosis like '%".$physician_orders."%' OR operativeProcedures like '%".$physician_orders."%' OR postOpDiagnosis like '%".$physician_orders."%' OR postOpDrops like '%".$physician_orders."%' OR nurseNotes like '%".$physician_orders."%'";
	$opRoomRes = imw_query($opRoomQry);
	if(imw_num_rows($opRoomRes)>0) {
		while($opRoomRow=imw_fetch_array($opRoomRes)){
			$searchConfIdArr[] = $opRoomRow["confirmation_id"];
		}
	}
	
	
	$andSearchConfIdQry = " AND patientconfirmation.patientConfirmationId IN(0)";	
	if(count($searchConfIdArr)>0) {
		$searchConfIdArr = array_unique($searchConfIdArr);
		$searchConfIdImplode = implode(",",$searchConfIdArr);
		$andSearchConfIdQry = " AND patientconfirmation.patientConfirmationId IN(".$searchConfIdImplode.")";	
	}
}
//END CODE TO SEARCH A FREE STRING VALUE

//echo $physician_report="select * from patientconfirmation where surgeonId='$physician_data' && dos between '$from_date' AND '$to_date'";
$confPhyQry = "patientconfirmation.surgeonId IN(".$physician_data.")";
if($physician_data=='all') { 
	$physician_data=$physician_id='0';
	$physician_name='All'; 
	$confPhyQry = "patientconfirmation.surgeonId != '".$physician_data."'";
}

if( $procedure=="" && $physician_data<>"" && $from_date=="" && $to_date==""){
		$physician_report = "SELECT patientconfirmation.* , 
								patient_data_tbl.patient_fname, 
								patient_data_tbl.patient_mname, 
								patient_data_tbl.patient_lname, 
								operatingroomrecords.surgeryTimeIn,
								operatingroomrecords.surgeryStartTime,
								operatingroomrecords.surgeryEndTime,
								operatingroomrecords.surgeryTimeOut,
								DATE_FORMAT(operatingroomrecords.surgeryStartTime,'%h:%i %p') as surgeryStartTimeFormat,
								DATE_FORMAT(operatingroomrecords.surgeryEndTime,'%h:%i %p') as surgeryEndTimeFormat,
								TIMEDIFF(operatingroomrecords.surgeryEndTime, operatingroomrecords.surgeryStartTime) as totalSurgeryTime,
								concat( users.lname, ', ', users.fname ) AS physician_name 
							FROM patientconfirmation
							left join patient_data_tbl on patientconfirmation.patientId = patient_data_tbl.patient_id
							LEFT JOIN operatingroomrecords ON patientconfirmation.patientConfirmationId = operatingroomrecords.confirmation_id
							LEFT JOIN users ON patientconfirmation.surgeonId  = users.usersId
							LEFT JOIN stub_tbl ON (stub_tbl.patient_confirmation_id=patientconfirmation.patientConfirmationId)
							where ".$confPhyQry.$andSearchConfIdQry."
							and stub_tbl.patient_confirmation_id !=0 $fac_con ORDER BY patientconfirmation.dos, patientconfirmation.ascId ASC
							";//group by patientconfirmation.patientConfirmationId
     }elseif( $procedure=="All" && $physician_data<>"" && $from_date=="" && $to_date==""){
	  
		$physician_report = "SELECT patientconfirmation. * , 
								patient_data_tbl.patient_fname, 
								patient_data_tbl.patient_mname, 
								patient_data_tbl.patient_lname, 
								operatingroomrecords.surgeryTimeIn,
								operatingroomrecords.surgeryStartTime,
								operatingroomrecords.surgeryEndTime,
								operatingroomrecords.surgeryTimeOut,
								DATE_FORMAT(operatingroomrecords.surgeryStartTime,'%h:%i %p') as surgeryStartTimeFormat,
								DATE_FORMAT(operatingroomrecords.surgeryEndTime,'%h:%i %p') as surgeryEndTimeFormat,
								TIMEDIFF(operatingroomrecords.surgeryEndTime, operatingroomrecords.surgeryStartTime) as totalSurgeryTime, 
								concat( users.lname, ', ', users.fname ) AS physician_name
							FROM patientconfirmation
							left join patient_data_tbl on patientconfirmation.patientId = patient_data_tbl.patient_id
							LEFT JOIN operatingroomrecords ON patientconfirmation.patientConfirmationId = operatingroomrecords.confirmation_id
							LEFT JOIN users ON patientconfirmation.surgeonId  = users.usersId
							LEFT JOIN stub_tbl ON (stub_tbl.patient_confirmation_id=patientconfirmation.patientConfirmationId)
							where ".$confPhyQry.$andSearchConfIdQry."
							and stub_tbl.patient_confirmation_id !=0 $fac_con  ORDER BY patientconfirmation.dos, patientconfirmation.ascId ASC";
     }elseif( $procedure=="All" && $from_date<>"" && $to_date<>""){
	  
		$physician_report = "SELECT patientconfirmation. * , 
								patient_data_tbl.patient_fname, 
								patient_data_tbl.patient_mname, 
								patient_data_tbl.patient_lname, 
								operatingroomrecords.surgeryTimeIn,
								operatingroomrecords.surgeryStartTime,
								operatingroomrecords.surgeryEndTime,
								operatingroomrecords.surgeryTimeOut,
								DATE_FORMAT(operatingroomrecords.surgeryStartTime,'%h:%i %p') as surgeryStartTimeFormat,
								DATE_FORMAT(operatingroomrecords.surgeryEndTime,'%h:%i %p') as surgeryEndTimeFormat,
								TIMEDIFF(operatingroomrecords.surgeryEndTime, operatingroomrecords.surgeryStartTime) as totalSurgeryTime,
								concat( users.lname, ', ', users.fname ) AS physician_name 
							FROM patientconfirmation
							left join patient_data_tbl on patientconfirmation.patientId = patient_data_tbl.patient_id
							LEFT JOIN operatingroomrecords ON patientconfirmation.patientConfirmationId = operatingroomrecords.confirmation_id
							LEFT JOIN users ON patientconfirmation.surgeonId  = users.usersId
							LEFT JOIN stub_tbl ON (stub_tbl.patient_confirmation_id=patientconfirmation.patientConfirmationId)
							where ".$confPhyQry.$andSearchConfIdQry."   
							and stub_tbl.patient_confirmation_id !=0 $fac_con
							AND patientconfirmation.dos between '$from_date' AND '$to_date'
							ORDER BY patientconfirmation.dos, patientconfirmation.ascId ASC ";
	
     }elseif( $procedure=="All" && $physician_data<>"" && $from_date==""){
	  
		$physician_report = "SELECT patientconfirmation. * , 
								patient_data_tbl.patient_fname, 
								patient_data_tbl.patient_mname, 
								patient_data_tbl.patient_lname, 
								operatingroomrecords.surgeryTimeIn,
								operatingroomrecords.surgeryStartTime,
								operatingroomrecords.surgeryEndTime,
								operatingroomrecords.surgeryTimeOut,
								DATE_FORMAT(operatingroomrecords.surgeryStartTime,'%h:%i %p') as surgeryStartTimeFormat,
								DATE_FORMAT(operatingroomrecords.surgeryEndTime,'%h:%i %p') as surgeryEndTimeFormat,
								TIMEDIFF(operatingroomrecords.surgeryEndTime, operatingroomrecords.surgeryStartTime) as totalSurgeryTime,
								concat( users.lname, ', ', users.fname ) AS physician_name 
							FROM patientconfirmation
							left join patient_data_tbl on patientconfirmation.patientId = patient_data_tbl.patient_id
							LEFT JOIN operatingroomrecords ON patientconfirmation.patientConfirmationId = operatingroomrecords.confirmation_id
							LEFT JOIN users ON patientconfirmation.surgeonId  = users.usersId
							LEFT JOIN stub_tbl ON (stub_tbl.patient_confirmation_id=patientconfirmation.patientConfirmationId)
							where ".$confPhyQry.$andSearchConfIdQry."
							and stub_tbl.patient_confirmation_id !=0 $fac_con
							AND patientconfirmation.dos >= '".$from_date."'
							ORDER BY patientconfirmation.dos, patientconfirmation.ascId ASC ";
     }elseif($procedure==$procedure_name &&$procedure<>"" && $physician_data<>"" && $from_date=="" && $to_date==""){
	
		$physician_report = "SELECT patientconfirmation. * , 
								patient_data_tbl.patient_fname, 
								patient_data_tbl.patient_mname, 
								patient_data_tbl.patient_lname, 
								operatingroomrecords.surgeryTimeIn,
								operatingroomrecords.surgeryStartTime,
								operatingroomrecords.surgeryEndTime,
								operatingroomrecords.surgeryTimeOut,
								DATE_FORMAT(operatingroomrecords.surgeryStartTime,'%h:%i %p') as surgeryStartTimeFormat,
								DATE_FORMAT(operatingroomrecords.surgeryEndTime,'%h:%i %p') as surgeryEndTimeFormat,
								TIMEDIFF(operatingroomrecords.surgeryEndTime, operatingroomrecords.surgeryStartTime) as totalSurgeryTime,
								concat( users.lname, ', ', users.fname ) AS physician_name 
							FROM patientconfirmation
							left join patient_data_tbl on patientconfirmation.patientId = patient_data_tbl.patient_id
							LEFT JOIN operatingroomrecords ON patientconfirmation.patientConfirmationId = operatingroomrecords.confirmation_id
							LEFT JOIN users ON patientconfirmation.surgeonId  = users.usersId
							LEFT JOIN stub_tbl ON (stub_tbl.patient_confirmation_id=patientconfirmation.patientConfirmationId)
							where (patientconfirmation.patient_primary_procedure_id='$procedure_id' || patientconfirmation.patient_secondary_procedure_id='$procedure_id') 
							and stub_tbl.patient_confirmation_id !=0 $fac_con
							AND ".$confPhyQry.$andSearchConfIdQry." ORDER BY patientconfirmation.dos, patientconfirmation.ascId ASC ";
   }elseif($procedure==$procedure_name && $procedure<>"" && $physician_data<>"" && $from_date<>"" && $to_date==""){
        
		$physician_report = "SELECT patientconfirmation. * , 
								patient_data_tbl.patient_fname, 
								patient_data_tbl.patient_mname, 
								patient_data_tbl.patient_lname, 
								operatingroomrecords.surgeryTimeIn,
								operatingroomrecords.surgeryStartTime,
								operatingroomrecords.surgeryEndTime,
								operatingroomrecords.surgeryTimeOut,
								DATE_FORMAT(operatingroomrecords.surgeryStartTime,'%h:%i %p') as surgeryStartTimeFormat,
								DATE_FORMAT(operatingroomrecords.surgeryEndTime,'%h:%i %p') as surgeryEndTimeFormat,
								TIMEDIFF(operatingroomrecords.surgeryEndTime, operatingroomrecords.surgeryStartTime) as totalSurgeryTime,
								concat( users.lname, ', ', users.fname ) AS physician_name 
							FROM patientconfirmation
							left join patient_data_tbl on patientconfirmation.patientId = patient_data_tbl.patient_id
							LEFT JOIN operatingroomrecords ON patientconfirmation.patientConfirmationId = operatingroomrecords.confirmation_id							
							LEFT JOIN users ON patientconfirmation.surgeonId  = users.usersId
							LEFT JOIN stub_tbl ON (stub_tbl.patient_confirmation_id=patientconfirmation.patientConfirmationId)
							where (patientconfirmation.patient_primary_procedure='$procedure' || patientconfirmation.patient_secondary_procedure='$procedure') 
							and stub_tbl.patient_confirmation_id !=0 $fac_con
							AND ".$confPhyQry.$andSearchConfIdQry." AND patientconfirmation.dos >= '$from_date'
							ORDER BY patientconfirmation.dos, patientconfirmation.ascId ASC ";
	}elseif( $procedure==$procedure_name && $from_date<>"" && $to_date<>""){ 
	 
		$physician_report = "SELECT patientconfirmation. * , 
								patient_data_tbl.patient_fname, 
								patient_data_tbl.patient_mname, 
								patient_data_tbl.patient_lname, 
								operatingroomrecords.surgeryTimeIn,
								operatingroomrecords.surgeryStartTime,
								operatingroomrecords.surgeryEndTime,
								operatingroomrecords.surgeryTimeOut,
								DATE_FORMAT(operatingroomrecords.surgeryStartTime,'%h:%i %p') as surgeryStartTimeFormat,
								DATE_FORMAT(operatingroomrecords.surgeryEndTime,'%h:%i %p') as surgeryEndTimeFormat,
								TIMEDIFF(operatingroomrecords.surgeryEndTime, operatingroomrecords.surgeryStartTime) as totalSurgeryTime,
								concat( users.lname, ', ', users.fname ) AS physician_name 
							FROM patientconfirmation
							left join patient_data_tbl on patientconfirmation.patientId = patient_data_tbl.patient_id
							LEFT JOIN operatingroomrecords ON patientconfirmation.patientConfirmationId = operatingroomrecords.confirmation_id
							LEFT JOIN users ON patientconfirmation.surgeonId  = users.usersId
							LEFT JOIN stub_tbl ON (stub_tbl.patient_confirmation_id=patientconfirmation.patientConfirmationId)
							where (patientconfirmation.patient_primary_procedure_id='$procedure_id' || patientconfirmation.patient_secondary_procedure_id='$procedure_id') 
							and stub_tbl.patient_confirmation_id !=0 $fac_con
							AND ".$confPhyQry.$andSearchConfIdQry." AND patientconfirmation.dos between '$from_date' AND '$to_date'
							ORDER BY patientconfirmation.dos, patientconfirmation.ascId ASC ";
	}else{
		$msg="No Record found!";
	}
    //echo $physician_report;exit;
	$physician=@imw_query($physician_report);
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
					.text_13 {
						font-size:13px;
						font-family:Arial, Helvetica, sans-serif;
						
					}
					.text13b{
						font-size:13px;
						font-family:Arial, Helvetica, sans-serif;
						font-weight:bold;
						color:#000000;
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
				<page backtop="45mm" backbottom="15mm">			
				<page_footer>
					<table style="width: 100%;">
						<tr>
							<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
						</tr>
					</table>
				</page_footer>';
			while($rpt=imw_fetch_array($physician)) {
				$report_srgn[] = $rpt;
				if(!in_array($rpt['physician_name'],$physician_name_arr)) {
					$physician_name_arr[] = $rpt['physician_name'];
				}
			}//print"<pre>";print_r($physician_name_arr);die();
			foreach($physician_name_arr as $physician_name) {
				$a=1;
				$t++;
				$table.='
				<page_header>
					<table width="100%" border="0" cellpadding="0" cellspacing="0" >
						<tr height="'.$higinc.'" >
							<td  class="text_16b" width="770" style="background-color:#cd532f; padding-left:5px; "  align="left" valign="middle">
								<font color="#FFFFFF"><b>'.$name.'<br>'.$address.'</b></font>
							</td>
							<td style="background-color:#cd532f;"  align="right" height="'.$imgheight.'" width="'.$imgwidth.'">'.$img_logo[0].'</td>
						</tr>
						<tr style="background-color:#FFFFFF;padding-top:5px;"><td></td></tr>
						<tr height="22" >
							<td width="100%" style="background-color:#F1F4F0;" align="right" colspan="5" class="text_16b">'.$current_date.'</td>
						</tr>
						<tr height="25">
							<td colspan="2" class="text_13" style="background-color:#F1F4F0;">
								<table width="100%" border="0" cellpadding="0" cellspacing="0">	
									<tr height="22" bgcolor="#F1F4F0">
										<td align="left" width="300" nowrap><b>Phys.Name:'.$physician_name.'</b></td>';
										
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
				
										$table.='
										<td align="left"  height="40" ><b>Proc.&nbsp;Name:&nbsp;'.$procedure.'</b></td>
										
									</tr>
								</table>						
							</td>
						</tr>
					</table>
					<table width="100%" border="0" cellpadding="0" cellspacing="0">		
						<tr  valign="top">
							<td align="left"   class="text13b" width="30">Seq</td>
							<td align="left"   class="text13b" width="160">Patient&nbsp;Name</td>
							<td align="left"   class="text13b" width="60">ASC-id</td>
							<td align="left"   class="text13b" width="210">Procedure</td>
							<td align="left"   class="text13b" width="70" style=" padding-left:15px;">Site</td>
							<td align="left"   class="text13b" width="70">Date</td>
							<td align="left"   class="text13b" width="70">Time In</td>
							<td align="left"   class="text13b" width="70">Start Time</td>
							<td align="left"   class="text13b" width="70">End Time</td>
							<td align="left"   class="text13b" width="70">Time Out</td>
							<td align="left"   class="text13b" width="100">Surgery Time(Mins)</td>
							<td align="left"   class="text13b" width="100">Room Time(Mins)</td>
						</tr>
						
					</table>
				</page_header>
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
				';

				$csv_content.= $name.','."".','."".','."".','."".','."".','."".','.$current_date;
				$csv_content.= $address.','."".','."".','."".','."".','."".','."".','."";
				$csv_content.= "Phys.Name: ".$physician_name."\n";
				$csv_content.= "From: ".$from_dateNew." To ".$to_dateNew."\n";
				$csv_content.= "Proc.: ".$procedure."\n\n";
				$csv_content.= 'Seq'.','.'"Patient Name"'.','.'"ASC-id"'.','.'"Procedure"'.','.'"Site"'.','.'"Date"'.','.'"Time"'.','.'"Start Time"'.','.'"End Time"'.','.'"End Time"'.','.'"Time Out"'.','.'"Surgery Time(Mins)"'.','.'"Room Time(Mins)"'."\n";
	
				foreach($report_srgn as $report) {
					if($physician_name==$report['physician_name']) {	
						$asc_id=$report['ascId'];
						if($asc_id == '0') {$asc_id=""; }
						$patient_id=$report['patientId'];
						$confirmation_id=$report['patientConfirmationId'];
						// $surgeon_name=$report['surgeon_name'];
						$procedure_pname=$report['patient_primary_procedure'];
						$procedure_sname=$report['procedure_secondary_procedure'];
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
						
						$surgeryTimeIn						= trim($report['surgeryTimeIn']);
						$surgeryTimeInFormat				= $objManageData->getTmFormat($surgeryTimeIn); 
						$surgeryStartTime					= trim($report['surgeryStartTime']);
						if ($report['surgeryStartTime']=="00:00:00" || $report['surgeryStartTime']==""){
						   $surgeryStartTimeFormat = "";
						}else{
						  $surgeryStartTimeFormat = $objManageData->getTmFormat($report['surgeryStartTime']);
						}
						
						$surgeryEndTime						= trim($report['surgeryEndTime']);
						if ($report['surgeryEndTime']=="00:00:00" || $report['surgeryEndTime']==""){
						   $surgeryEndTimeFormat = "";
						}else{
						  $surgeryEndTimeFormat = $objManageData->getTmFormat($report['surgeryEndTime']);
						}
						$surgeryTimeOut						= trim($report['surgeryTimeOut']);
						$surgeryTimeOutFormat				= $objManageData->getTmFormat($surgeryTimeOut);
						
						//$surgeryEndTimeFormat = $totalSurgeryTime = $totalRoomTime = '';
						$surgeryEndTimeHr = $surgeryEndTimeMin = $surgeryEndTimeSec = '';
						$surgeryEndTimeHr = $surgeryEndTimeMin = $surgeryEndTimeSec = '';
						
						
						/*if($surgeryStartTime!='00:00:00') 	{ 
							list($surgeryStartTimeHr,$surgeryStartTimeMin,$surgeryStartTimeSec) = explode(":",$surgeryStartTime);
							
							//set temprary code as operating room record is saving incorrect surgery time for hours as 24 instead of 12
							if($surgeryStartTimeHr=='24') {
								$surgeryStartTime = '12:'.$surgeryStartTimeMin.':'.$surgeryStartTimeMin;
								$report['surgeryStartTimeFormat'] =  date("h:i A", strtotime($surgeryStartTime));
							}
							
							$surgeryStartTimeFormat			= $report['surgeryStartTimeFormat']; 
						}*/
						/*if($surgeryEndTime!='00:00:00') 	{ 
							list($surgeryEndTimeHr,$surgeryEndTimeMin,$surgeryEndTimeSec) = explode(":",$surgeryEndTime);
							
							//set temprary code as operating room record is saving incorrect surgery time for hours as 24 instead of 12
							if($surgeryEndTimeHr=='24') {
								$surgeryEndTime = '12:'.$surgeryEndTimeMin.':'.$surgeryEndTimeMin;
								$report['surgeryEndTimeFormat'] =  date("h:i A", strtotime($surgeryEndTime));
							}
							$surgeryEndTimeFormat			= $report['surgeryEndTimeFormat']; 
						}
						*/
						$totalSurgeryTimeInMinutes 			= '';
						if($surgeryStartTime!='00:00:00' && $surgeryEndTime!='00:00:00') {
							
							$totalSurgeryTime 				= $report['totalSurgeryTime'];
							list($sTimeHH,$sTimeMM) 		= explode(':',$totalSurgeryTime);
							$totalSurgeryTimeInMinutes 		= (($sTimeHH*60)+$sTimeMM);
							
							//set temprary code as operating room record is saving incorrect surgery time for hours as 24 instead of 12
							if($surgeryStartTimeHr=='24' || $surgeryEndTimeHr=='24') {
								$totalSurgeryTimeInMinutes = floor(((strtotime($surgeryEndTime)- strtotime($surgeryStartTime))/60));
							}
							
							if(strlen(trim($totalSurgeryTimeInMinutes))==1) { $totalSurgeryTimeInMinutes = '0'.$totalSurgeryTimeInMinutes; }
						}
						
						$totalRoomTimeInMinutes 			= '';
						if($surgeryTimeIn && $surgeryTimeOut) {
							$tmInHH = $tmInMM = $tmOutHH = $tmOutMM = 0;
							
							list($tmInHH,$tmInTmp) 			= explode(":",$surgeryTimeIn);
							list($tmInMM,$tmInAP) 			= explode(' ',$tmInTmp);
							if(strtoupper(trim($tmInAP))=='PM' && $tmInHH && $tmInHH!='12') { 
								$tmInHH = $tmInHH+12; 
							}
							
							list($tmOutHH,$tmOutTmp) 		= explode(":",$surgeryTimeOut);
							list($tmOutMM,$tmOutAP)			= explode(' ',trim($tmOutTmp));
							if(strtoupper(trim($tmOutAP)=='PM' && $tmOutHH && $tmOutHH!='12')) { 
								$tmOutHH = $tmOutHH+12; 
							}
							$totalRoomTime					= date("H:i:s",mktime($tmOutHH-$tmInHH,$tmOutMM-$tmInMM,0,0,0,0));
							
							list($rTimeHH,$rTimeMM) 		= explode(':',$totalRoomTime);
							$totalRoomTimeInMinutes 		= (($rTimeHH*60)+$rTimeMM);
							if(strlen(trim($totalRoomTimeInMinutes))==1) { $totalRoomTimeInMinutes = '0'.$totalRoomTimeInMinutes; }
						}
						if($i<=$num){	
							$j=$i+1;
							$borderBottomFirstRow = 'bottomBorder';
							$table.='
								<tr valign="top">
									<td class="'.$borderBottomFirstRow.' text_13" width="30">'.$a.'</td>
									<td class="'.$borderBottomFirstRow.' text_13" align="left" width="160">'.$patient_name.'</td>					
									<td class="'.$borderBottomFirstRow.' text_13" align="left" width="60">'.$asc_id.'</td>
									<td class="'.$borderBottomFirstRow.' text_13" align="left" width="210">'.$procedure_name.'</td>
									<td class="'.$borderBottomFirstRow.' text_13" align="left" width="70" style=" padding-left:15px;">'.$eyesite.'</td>
									<td class="'.$borderBottomFirstRow.' text_13" align="left" width="70">'.$date_of_surgery.'</td>
									<td class="'.$borderBottomFirstRow.' text_13" align="left" width="70">'.$surgeryTimeInFormat.'</td>
									<td class="'.$borderBottomFirstRow.' text_13" align="left" width="70">'.$surgeryStartTimeFormat.'</td>
									<td class="'.$borderBottomFirstRow.' text_13" align="left" width="70">'.$surgeryEndTimeFormat.'</td>
									<td class="'.$borderBottomFirstRow.' text_13" align="left" width="70">'.$surgeryTimeOutFormat.'</td>
									<td class="'.$borderBottomFirstRow.' text_13" align="left" width="100">'.$totalSurgeryTimeInMinutes.'</td>
									<td class="'.$borderBottomFirstRow.' text_13" align="left" width="100">'.$totalRoomTimeInMinutes.'</td>
								</tr>
							';

							$csv_content.= $a.','.str_ireplace(","," ",$patient_name).','.$asc_id.','.$procedure_name.','.$eyesite.','.$date_of_surgery.','.$surgeryTimeInFormat.','.$surgeryStartTimeFormat.','.$surgeryEndTimeFormat.','.$surgeryTimeOutFormat.','.$totalSurgeryTimeInMinutes.','.$totalRoomTimeInMinutes."\n";									
						
						}//ending of if statement
						$a++;
					}
				}
				$table .= '</table></page>';
				if(count($physician_name_arr) > $t){
					$table .= '<page backtop="45mm" backbottom="15mm">			
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

if($_REQUEST['phy_save']=='yes') {
	if(@imw_num_rows($physician)>0){
		$file_name="admin/pdfFiles/physician_reportpop_surgerytime.csv";
		@unlink($file_name);
		$fpH1 = fopen($file_name,'w');
		fwrite($fpH1, $csv_content."\n\r");
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
<script language="javascript">
	window.focus();
	function submitfn()
	{
		document.printFrm.submit();
	}
</script>

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
	
	if($_REQUEST['phy_save']=='yes') {
	?>
	<script type="text/javascript">
        location.href = "physician_report.php?no_record=yes";
    </script>
	<?php
	}
	?>
	<table style=" font-family:Verdana, Geneva, sans-serif; font-size:12;" bgcolor="#EAF0F7" width="100%" height="100%">
		<tr>
			<td width="100%" align="center" valign="top"><b>No Record Found</b></td> 
		</tr>
	</table>
<?php		
}?>
</body>
