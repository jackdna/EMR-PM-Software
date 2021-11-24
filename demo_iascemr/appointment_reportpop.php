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
if($_SESSION['iasc_facility_id'])
{
	$fac_con=" and stub_tbl.iasc_facility_id='$_SESSION[iasc_facility_id]'"; 
}
$procedure=$_REQUEST['procedure'];
$physician_data=$_REQUEST['physician'];
if(is_array($physician_data)) {
	$physician_data = implode(",",$physician_data);
}

if(!trim($physician_data)) {
	$physician_data='all';
}
$physician_data_req = $physician_data;
$startdate=$_REQUEST['startdate'];
$enddate=$_REQUEST['enddate'];
$physician_orders=$_REQUEST['physician_orders']; 
$get_http_path=$_REQUEST['get_http_path'];
$status=$_REQUEST['status'];
//get detail for logged in facility
$queryFac=imw_query("select * from facility_tbl where fac_id='$_SESSION[facility]'")or die(imw_error());
$dataFac=imw_fetch_object($queryFac);
$name=stripcslashes($dataFac->fac_name);
$address=stripcslashes($dataFac->fac_address1).' '.stripcslashes($dataFac->fac_address2).' '.stripcslashes($dataFac->fac_city).' '.stripcslashes($dataFac->fac_state);

//set surgerycenter detail
$SurgeryQry ="select * from surgerycenter where surgeryCenterId=1";
$SurgeryRes = imw_query($SurgeryQry);
while($SurgeryRecord = imw_fetch_array($SurgeryRes)){
	//$name = stripslashes($SurgeryRecord['name']);
	//$address = stripslashes($SurgeryRecord['address'].' '.$SurgeryRecord['city'].' '.$SurgeryRecord['state']);
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
$higinc=$hig-10;
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
}else $from_date=date('Y-m-d');
//extracting end date
$enddate=$_REQUEST['enddate'];
if($enddate!=0){
	$end_date=explode("-",$enddate);
	$end_date[0];
	$end_date[1];
	$end_date[2];
	$to_date=$end_date[2]."-".$end_date[0]."-".$end_date[1];
}else $to_date=date('Y-m-d');

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


//echo $physician_report="select * from patientconfirmation where surgeonId='$physician_data' && dos between '$from_date' AND '$to_date'";
$surgeonQuery = " AND users.usersId IN(".$physician_data.") ";
if(strtolower($physician_data)=='all' || trim($physician_data)=='') { 
	$physician_data=$physician_id='0';
	$physician_name='All'; 
	$surgeonQuery = "";//no need to filter
}

if($from_date<>"" && $to_date<>"")
{
	$dateQuery=" AND stub_tbl.dos between '$from_date' AND '$to_date'";
}
$procTxtQuery=$procIdQuery=" 1=1";
if(trim($procedure)<>"" && $procedure<>"All")
{
	$procTxtQuery="(stub_tbl.patient_primary_procedure LIKE '%$procedure%' 
					OR stub_tbl.patient_secondary_procedure LIKE '%$procedure%'
					OR stub_tbl.patient_tertiary_procedure LIKE '%$procedure%')";
							
	$procIdQuery=" (stub_tbl.patient_primary_procedure LIKE '%$procedure%' 
					OR stub_tbl.patient_secondary_procedure LIKE '%$procedure%'
					OR stub_tbl.patient_tertiary_procedure LIKE '%$procedure%')";	
}
$statusQuery="";
if($status && $status<>'All')
{
	$statusQuery=" AND stub_tbl.patient_status='$status'";	
}
$physician_report = "SELECT stub_tbl.*, concat( stub_tbl.surgeon_lname, ', ', stub_tbl.surgeon_fname ) AS physician_name, pc.ascId as asc_Id
					FROM stub_tbl
					INNER JOIN users ON (stub_tbl.surgeon_fname  = users.fname
					AND stub_tbl.surgeon_mname  = users.mname
					AND stub_tbl.surgeon_lname  = users.lname)
					LEFT JOIN patientconfirmation as pc
					ON pc.patientConfirmationId=stub_tbl.patient_confirmation_id
					where 1=1 AND users.deleteStatus <> 'Yes' $surgeonQuery
					$dateQuery
					$fac_con
					$statusQuery
					AND IF(stub_tbl.patient_confirmation_id<>0, $procIdQuery, $procTxtQuery)
					ORDER BY stub_tbl.dos, stub_tbl.stub_id ASC
					";//group by patientconfirmation.patientConfirmationId

    $physician=@imw_query($physician_report);
	$rows=@imw_num_rows($physician); 
	$t=0;
	$csv_content = '';
	if(@imw_num_rows($physician)>0){
		$css='
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
						border-bottom-style:solid; border-bottom:1px;padding-top:4px;padding-bottom:4px;
						font-family:Arial, Helvetica, sans-serif;
					}
					.leftBorder {
						border-left:#000 solid 1px;padding-left:4px;padding-right:4px;
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
					.redClass 
					{
						color:#F00;	
					}					
				</style>';
				$table.=$css.'<page backtop="42mm" backbottom="15mm">			
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

			$csv_content1 = $name;
			$csv_content2 = $address.','."".','."".','."";
			
			foreach($physician_name_arr as $physician_name) {
				$a=1;
				$t++;
				
				$detailHeaderStr='<page_header>
					<table width="1000" border="0" cellpadding="0" cellspacing="0" >
						<tr height="'.$higinc.'" >
							<td  class="text_16b" width="700" style="background-color:#cd532f; padding-left:5px; color:white "  align="left" valign="middle">
								<font color="#FFFFFF"><b>'.$name.'<br>'.$address.'</b></font>
							 </td>
							<td style="background-color:#cd532f;"  align="right" height="'.$imgheight.'" width="'.$imgwidth.'">'.$img_logo[0].'&nbsp;</td>
						</tr>
						<tr style="background-color:#FFFFFF;padding-top:5px;"><td colspan="2"></td></tr>
						<tr height="22" >
							<td width="1000" colspan="2" style="background-color:#F1F4F0;padding-right:50px;" align="right" class="text_16b">Appointment Report Detail '.$current_date.'</td>
							
						</tr>
						<tr height="25">
							<td colspan="2" class="text_15" style="background-color:#F1F4F0;">
								<table width="100%" border="0" cellpadding="0" cellspacing="0">	
									<tr height="22" bgcolor="#F1F4F0">
										<td align="left" width="300" nowrap><b>Dr. '.$physician_name.'</b></td>';
									 
										$from_dateNew = $to_dateNew = "";
										if($from_date!='' && $to_date!='')
										{
										   $from_dateNew 	=  $objManageData->changeDateMDY($from_date);
										   $to_dateNew 		=  $objManageData->changeDateMDY($to_date);
										   $detailHeaderStr.='
											   <td align="right" style=" padding-right:5px; "><b>From&nbsp;'.$from_dateNew.'</b></td>
											   <td align="right" style=" padding-right:5px; "><b>To</b></td>
											   <td align="right" style=" padding-right:40px; "><b>'.$to_dateNew.'</b></td>
											';
										}	
										else
										{
										  $detailHeaderStr.='<td colspan="3" width="300">&nbsp;</td>';
										}
										$procedureStr=(strlen($procedure)>45)?substr($procedure,0,32).'...':$procedure;
										$detailHeaderStr.='
										<td align="left" width="300" height="40"><b>Proc.:&nbsp;'.$procedureStr.'</b></td>
										<td align="left" width="100" height="40"><b>Appt.&nbsp;Status:&nbsp;'.$status.'</b></td>
										
									</tr>
								</table>						
							</td>
						</tr>
					</table>
					<table width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color:#F1F4F0;">		
						<tr  valign="top">
							<td align="left" class="text_b" width="30" height="30">Seq</td>
							<td align="left" class="text_b" width="130">Patient Name</td>
							<td align="left" class="text_b" width="60">ASC-ID</td>
							<td align="left" class="text_b" width="150">Address</td>
							<td align="left" class="text_b" width="100">Contact</td>
							<td align="left" class="text_b" width="95">DOB</td>
							<td align="left" class="text_b" width="160">Procedure</td>
							<td align="left" class="text_b" width="80">Status</td>
							<td align="left" class="text_b" width="95">DOS</td>
							<td align="left" class="text_b" width="106">Surgery Time</td>
						</tr>
					</table>
				</page_header>';
				
				$summaryHeaderStr='<page_header>
					<table width="1000" border="0" cellpadding="0" cellspacing="0">
						<tr height="'.$higinc.'" >
							<td  class="text_16b" width="700" style="background-color:#cd532f; padding-left:5px; color:white "  align="left" valign="middle">
								<font color="#FFFFFF"><b>'.$name.'<br>'.$address.'</b></font>
							 </td>
							<td style="background-color:#cd532f;"  align="right" height="'.$imgheight.'" width="'.$imgwidth.'">'.$img_logo[0].'</td>
						</tr>
						<tr style="background-color:#FFFFFF;padding-top:5px;"><td></td></tr>
						<tr height="22" >
							<td width="1000" style="background-color:#F1F4F0;padding-right:50px;" align="right" class="text_16b" colspan="2">Appointment Report Summary '.$current_date.'</td>
						</tr>
						<tr height="25">
							<td colspan="2" class="text_15" style="background-color:#F1F4F0;">
								<table width="100%" border="0" cellpadding="0" cellspacing="0">	
									<tr height="22" bgcolor="#F1F4F0">
										<td align="left" width="300" nowrap><b>Dr. '.$physician_name.'</b></td>';
									 
										$from_dateNew = $to_dateNew = "";
										if($from_date!='' && $to_date!='')
										{
										   $from_dateNew 	=  $objManageData->changeDateMDY($from_date);
										   $to_dateNew 		=  $objManageData->changeDateMDY($to_date);
										   $summaryHeaderStr.='
											   <td align="right" style=" padding-right:5px; "><b>From&nbsp;'.$from_dateNew.'</b></td>
											   <td align="right" style=" padding-right:5px; "><b>To</b></td>
											   <td align="right" style=" padding-right:40px; "><b>'.$to_dateNew.'</b></td>
											';
										}	
										else
										{
										  $summaryHeaderStr.='<td colspan="3" width="300">&nbsp;</td>';
										}
										$procedureStr=(strlen($procedure)>45)?substr($procedure,0,32).'...':$procedure;
										$summaryHeaderStr.='
										<td align="left" width="300" height="40"><b>Proc.:&nbsp;'.$procedureStr.'</b></td>
										<td align="left" width="100" height="40"><b>Appt.&nbsp;Status:&nbsp;'.$status.'</b></td>
										
									</tr>
								</table>						
							</td>
						</tr>
					</table>
					<table width="100%" border="1" cellpadding="2" cellspacing="0" style="background-color:#F1F4F0;">		
						<tr valign="top">
							<td align="left" class="text_b" width="30" height="25">Seq</td>
							<td align="left" class="text_b leftBorder" width="230">Procedure</td>
							<td align="left" class="text_b leftBorder" width="86">Total</td>								
							<td align="left" class="text_b leftBorder" width="90">Cancelled</td>
							<td align="left" class="text_b leftBorder" width="90">Checked-In</td>
							<td align="left" class="text_b leftBorder" width="90">Checked-Out</td>
							<td align="left" class="text_b leftBorder" width="76">No Show</td>
							<td align="left" class="text_b leftBorder" width="150">Aborted&nbsp;Surgery</td>
							<td align="left" class="text_b leftBorder" width="71">Scheduled</td>
						</tr>
					</table>
				</page_header>';
				
				if($t == '1') {
					$procedure_sel_csv	 = trim(str_ireplace("<br>","  ",$procedure));
					$csv_content1 		.= ','."".','."From ".$from_dateNew." To ".$to_dateNew.','."".','."Procedure: ".$procedure_sel_csv.','."".','."Appt. Status: ".$status;
				}
				
				if($_REQUEST['reportType']=='Detail') {
					$physician_name_csv 	 = '"Dr. '.trim($physician_name).'"';
					$csv_content 			.= "\n\n".$physician_name_csv.','."".',Appointment Report Detail '.$current_date.','."\n";
					$csv_content 			.= '"Seq"'.','.'"Patient Name"'.','.'"ASC-ID"'.','.'"Address"'.','.'"Contact"'.','.'"DOB"'.','.'"Procedure"'.','.'"Status"'.','.'"DOS"'.','.'"Surgery Time"'.','."\n";
				}
				$table.=$detailHeaderStr.'
				
				<table width="100%" border="0" cellpadding="0" cellspacing="0">';

				foreach($report_srgn as $report) {
					if($physician_name==$report['physician_name']) {	
						$asc_id=$report['asc_Id'];
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
						$contact=$report['patient_home_phone'];
						$dos=$report['dos'];
						//exploding date of surgery
						$date=explode("-",$dos);
						$date[0];
						$date[1];
						$date[2];
						$date_of_surgery=$date[1]."-".$date[2]."-".$date[0];
						
						$dob=$report['patient_dob'];
						//exploding date of surgery
						$date=explode("-",$dob);
						$date[0];
						$date[1];
						$date[2];
						$date_of_birth=$date[1]."-".$date[2]."-".$date[0];
						
						
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
						$patient_fname=$report['patient_first_name'];
						$patient_mname=$report['patient_middle_name'];
						$patient_lname=$report['patient_last_name'];
						$patient_name=$patient_lname.", ".$patient_fname;
						//END OF CODE
						
						//CODE FOR STARTING TIME FROM PREOPNURSINGRECORD TABLE
							if($report['surgery_time']=="00:00:00" || $report['surgery_time']==""){
				   $from_time = "";
				}else{
				  $from_time = $objManageData->getTmFormat($report['surgery_time']);
				}
						/*$from_time=$report['surgery_time'];
						$frm_time=explode(" ",$from_time);
						$frm_time[0];
						$frm_time[1];
						$start_time1=explode(":",$frm_time[0]);
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
						}*/
						
						//END OF CODE
						$pt_status=$report['patient_status'];
						$redClass='';
						if($pt_status=='Canceled')
						{
							$redClass=" redClass";
							$pt_status='Cancelled';	
						}	
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
							$summary[$physician_name][$procedure_name][$pt_status]++;
							$borderBottomFirstRow = $borderBottomSecondRow = 'bottomBorder';
							$table.='
								<tr valign="top">
									<td class="'.$borderBottomFirstRow.' text_15" width="30">'.$a.'</td>
									<td class="'.$borderBottomFirstRow.' text_15" align="left" width="130">'.$patient_name.'</td>					
									<td class="'.$borderBottomFirstRow.' text_15" align="left" width="60">'.$asc_id.'</td>					
									<td class="'.$borderBottomFirstRow.' text_15" align="left" width="150">'.$address.'</td>
									<td class="'.$borderBottomFirstRow.' text_15" align="left" width="100">'.$contact.'</td>
									<td class="'.$borderBottomFirstRow.' text_15" align="left" width="95">'.$date_of_birth.'</td>
									<td class="'.$borderBottomFirstRow.' text_15" align="left" width="160">'.wordwrap($procedure_name,35,"<br>",1).'</td>
									<td class="'.$borderBottomFirstRow.$redClass.' text_15" align="left" width="80">'.$pt_status.'</td>
									<td class="'.$borderBottomFirstRow.' text_15" align="left" width="95">'.$date_of_surgery.'</td>
									<td class="'.$borderBottomFirstRow.' text_15" align="left" width="170">'.$from_time.'</td>
								</tr>
							';
							//START CSV CODE
							if($_REQUEST['reportType']=='Detail') {
								$seq_csv 				=	'"'.trim($a).'"';
								$patient_name_csv 		=	'"'.trim($patient_name).'"';
								$asc_id_csv 			=	'"'.trim($asc_id).'"';
								$address_csv 			=	'"'.trim($address).'"';
								$contact_csv 			= 	'"'.trim($contact).'"';
								$date_of_birth_csv 		= 	'"'.trim($date_of_birth).'"';
								$procedure_name_csv		=	'"'.trim(str_ireplace("<br>","  ",$procedure_name)).'"';
								$pt_status_csv			=	'"'.trim($pt_status).'"';
								$date_of_surgery_csv	=	'"'.trim($date_of_surgery).'"';
								$from_time_csv			=	'"'.trim($from_time).'"';
								
								$csv_content 			.= $seq_csv.','.$patient_name_csv.','.$asc_id_csv.','.$address_csv.','.$contact_csv.','.$date_of_birth_csv.','.$procedure_name_csv.','.$pt_status_csv.','.$date_of_surgery_csv.','.$from_time_csv."\n";		
							}
							//END CSV CODE
						}//ending of if statement
						$a++;
					}
				}
				$table .= '</table></page>';
				$summaryStr=$css;
				if(sizeof($summary)>=1)
				{
					$physician_name_csv 	 = '"Dr. '.trim($physician_name).'"';
					$csv_content 			.= "\n\n".$physician_name_csv.','."".',Appointment Report Summary '.$current_date.','."\n";
					$csv_content 			.= '"Seq"'.','.'"Procedure"'.','.'"Total"'.','.'"Cancelled"'.','.'"Checked-In"'.','.'"Checked-Out"'.','.'"No Show"'.','.'"Aborted Surgery"'.','.'"Scheduled"'.','."\n";
					$summaryStr .= '<page backtop="42mm" backbottom="15mm">			
						<page_footer>
							<table style="width: 100%;">
								<tr>
									<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
								</tr>
							</table>
						</page_footer>';
					$summaryStr .=$summaryHeaderStr;
					$summaryStr .= '<table width="100%" border="1" cellpadding="0" cellspacing="0">';
					foreach($summary as $surgeon=>$procedures)
					{
					
						$summaryStr .= '<tr valign="top"><td colspan="9" class="'.$borderBottomFirstRow.' text_15" style="padding:5px 0px; font-weight:bold">'.$surgeon.'</td></tr>';
						$total=0;
						foreach($procedures as $proc=>$counters)						
						{
							$counter++;
							$total=array_sum($counters);
							$summaryStr .= '<tr valign="top">
								<td width="30" class="'.$borderBottomFirstRow.' text_15" height="25">'.$counter.'</td>
								<td width="230" class="'.$borderBottomFirstRow.' text_15 leftBorder" align="left" >'.wordwrap($proc,35,"<br>",1).'</td>					
								<td width="86" class="'.$borderBottomFirstRow.' text_15 leftBorder" align="left">'.$total.'</td>					
								<td width="90" class="'.$borderBottomFirstRow.' text_15 leftBorder" align="left">'.$counters['Cancelled'].'</td>
								<td width="90" class="'.$borderBottomFirstRow.' text_15 leftBorder" align="left">'.$counters['Checked-In'].'</td>
								<td width="90" class="'.$borderBottomFirstRow.' text_15 leftBorder" align="left">'.$counters['Checked-Out'].'</td>
								<td width="76" class="'.$borderBottomFirstRow.' text_15 leftBorder" align="left">'.$counters['No Show'].'</td>
								<td width="150" class="'.$borderBottomFirstRow.' text_15 leftBorder" align="left">'.$counters['Aborted Surgery'].'</td>
								<td width="71" class="'.$borderBottomFirstRow.' text_15 leftBorder" align="left">'.$counters['Scheduled'].'</td>
							</tr>';
							
							
							//START CSV CODE
							$seq_summary_csv 			=	'"'.trim($counter).'"';
							$procedure_name_summary_csv	=	'"'.trim(str_ireplace("<br>","  ",$proc)).'"';
							$total_summary_csv 			=	'"'.trim($total).'"';
							$cancelled_summary_csv 		=	'"'.trim($counters['Cancelled']).'"';
							$checked_in_summary_csv 	=	'"'.trim($counters['Checked-In']).'"';
							$checked_out_summary_csv	= 	'"'.trim($counters['Checked-Out']).'"';
							$no_show_summary_csv 		= 	'"'.trim($counters['No Show']).'"';
							$aborted_surgery_summary_csv= 	'"'.trim($counters['Aborted Surgery']).'"';
							$pt_scheduled_csv			=	'"'.trim($counters['Scheduled']).'"';
							
							$csv_content 			.= $seq_summary_csv.','.$procedure_name_summary_csv.','.$total_summary_csv.','.$cancelled_summary_csv.','.$checked_in_summary_csv.','.$checked_out_summary_csv.','.$no_show_summary_csv.','.$aborted_surgery_summary_csv.','.$pt_scheduled_csv."\n";		
							//END CSV CODE
						}
						$counter=0;
					}
					$summaryStr .= '</table></page>';
				}
				unset($summary);
				$table .=$summaryStr;
				$reportSummary.=$summaryStr;
				if(count($physician_name_arr) > $t){
					$table .= '<page backtop="42mm" backbottom="15mm">			
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
		if($_REQUEST['reportType']=='Detail')$table=$table;
		else $table=$reportSummary;
		

if($_REQUEST['hidd_report_format']=='csv' && imw_num_rows($physician)>0) {
	$file_name=$_SERVER['DOCUMENT_ROOT'].'/'.$surgeryCenterDirectoryName.'/admin/pdfFiles/appointment_reportpop.csv';
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
if(@imw_num_rows($physician)<=0 && $_REQUEST['hidd_report_format']=='csv') {
?>
	<script type="text/javascript">
		location.href = "appointment_report.php?no_record=yes&date1=<?php echo $_REQUEST['startdate'];?>&date2=<?php echo $_REQUEST['enddate'];?>&procedure=<?php echo $_REQUEST['procedure'];?>&physician=<?php echo $physician_data_req;?>&status=<?php echo $_REQUEST['status'];?>&reportType=<?php echo $_REQUEST['reportType'];?>";
	</script>
<?php
}else if(@imw_num_rows($physician)>0){?>		
	<script type="text/javascript">
        submitfn();
    </script>
<?php 
}else {?>
	<script>
		if(document.getElementById("loader_tbl")) {
			document.getElementById("loader_tbl").style.display = "none";	
		}
	</script>	
	<table style=" font-family:Verdana, Geneva, sans-serif; font-size:12px; background-color:#EAF0F7; width:100%; height:100%;">
		<tr>
			<td class="alignCenter valignTop" style="width:100%;"><b>No Record Found</b></td> 
		</tr>
	</table>
<?php		
}?>
</body>
</html>
