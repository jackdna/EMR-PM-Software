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
include("common/conDb.php");
if($_SESSION["loginUserId"]=="" && $_SESSION['loginUserName']=="") {
	echo '<script>top.location.href="index.php"</script>';
}
$imw_db_dot=$_REQUEST['hidd_imw_db_dot'];
$get_http_path=$_REQUEST['get_http_path'];
include_once("admin/classObjectFunction.php");
global $objManageData;
$objManageData = new manageData;
$loginUser 	= $_SESSION['loginUserId'];
$asc 		= $_REQUEST['asc'];
$ascTmp 	= $asc;
if(!$ascTmp) { $ascTmp = $_SESSION['facility']; }


$user_name="";
if($loginUser){
	$qry_user="SELECT fname,mname,lname from users where usersId='".$loginUser."'";
	$res_user=imw_query($qry_user);
	$row_user=imw_fetch_assoc($res_user);
	$user_name=trim($row_user['lname'].", ".$row_user['fname']." ".$row_user['mname']);
}
//include("common/linkfile.php");
 //set surgerycenter detail 
$queryFac=imw_query("select * from facility_tbl where fac_id='".$ascTmp."' ")or die(imw_error());
$dataFac=imw_fetch_object($queryFac);
$name=stripslashes($dataFac->fac_name);
$address=stripslashes($dataFac->fac_address1).' '.stripslashes($dataFac->fac_address2).' '.stripslashes($dataFac->fac_city).' '.stripslashes($dataFac->fac_state);
$iasc_facility_id 	= $dataFac->fac_idoc_link_id;
$ascQry 			= "";
if($iasc_facility_id) {
	$ascQry 		= " AND st.iasc_facility_id = '".$iasc_facility_id."' ";	
}
			
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
imagejpeg($bakImgResource,'new_html2pdf/white.jpg');
$size=getimagesize('new_html2pdf/white.jpg');
$hig=$size[1];
$wid=$size[0];
$higinc=$hig+10;
$filename='new_html2pdf/white.jpg';
//function knatsort($arr){return uksort($arr,function($a, $b){return strnatcmp($a,$b);}); 
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

$patientConfirmationIdSet = trim($_REQUEST['patientConfirmationId']);
if( substr($patientConfirmationIdSet,-1,1) == "," ){
	$patientConfirmationIdSet = substr($patientConfirmationIdSet,0,-1);
}
$patientConfirmationIdSet = ($patientConfirmationIdSet) ? $patientConfirmationIdSet : '0';
//date1
$selected_date	= $_REQUEST['startdate'];
$selected_date2	= $_REQUEST['enddate'];

$frm_date 	= date('m-d-Y',strtotime($selected_date));
$to_date 	= date('m-d-Y',strtotime($selected_date2));

$current_date=date("m-d-Y");
$img_logo = showThumbImages('new_html2pdf/white.jpg',170,50);
$imgheight= $img_logo[2]+8;
$imgwidth= $img_logo[1]+135;	

// Users Data
$userGrpQry = "SELECT u.usersId,u.fname, u.mname, u.lname 
								FROM patientconfirmation pc
								INNER JOIN users u ON (u.usersId=pc.surgeonId)
								INNER JOIN stub_tbl st ON (st.patient_confirmation_id = pc.patientConfirmationId ".$ascQry.")
								WHERE pc.patientConfirmationId in(".$patientConfirmationIdSet.") AND pc.surgeonId!='0' 
								GROUP BY pc.surgeonId 
								ORDER BY pc.surgery_time";
$userGrpRes = imw_query($userGrpQry) or die($userGrpQry.imw_error());
$t=0;
$table = $tableCounty = '';
$surgeonIdArr = $surgNameArr = array();

if(imw_num_rows($userGrpRes)>0) {
	while($userGrpRow 		= imw_fetch_array($userGrpRes)) {
		$uId				= $userGrpRow['usersId'];
		$surgeonIdArr[$uId] = $userGrpRow['usersId'];
		$surgNameArr[$uId] 	= $userGrpRow['fname'].' '.$userGrpRow['mname'].' '.$userGrpRow['lname'];	
	}
}

$users = implode(",",array_filter(array_keys($surgeonIdArr)));
// Start Collecting NC State Report Data
$physician_report = "SELECT st.*, pc.patientConfirmationId, pc.patient_primary_procedure, pc.surgeonId, pc.dos, pc.ascId as asc_Id, 
			st.imwPatientId, CONCAT(pd.patient_lname,', ',pd.patient_fname,' ',pd.patient_mname) AS patient_name, 
			ds.summarySaveDateTime AS summary_last_modified,
			concat( u.lname, ', ', u.fname ) AS physician_name, hs.saved_on AS dft_saved_date, hs.sent AS dft_sent_status
			FROM patientconfirmation pc  
			INNER JOIN stub_tbl st ON (st.patient_confirmation_id = pc.patientConfirmationId AND st.patient_confirmation_id !='0' ".$ascQry.")
			INNER JOIN patient_data_tbl pd ON (pd.patient_id = pc.patientId )
			INNER JOIN dischargesummarysheet ds ON(ds.confirmation_id = pc.patientConfirmationId) 
			INNER JOIN ".$imw_db_dot."hl7_sent hs ON (hs.sch_id = pc.patientConfirmationId AND hs.sch_id!='0' AND hs.msg_type = 'DFT' AND ds.summarySaveDateTime > hs.saved_on)
			INNER JOIN users u ON (u.usersId  = pc.surgeonId)
			WHERE pc.surgeonId IN (".$users.") AND pc.patientConfirmationId in(".$patientConfirmationIdSet.")
			ORDER BY pc.dos, pc.surgery_time";

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
					font-size:15px;
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
			$table.=$css.'<page backtop="35mm" backbottom="2mm">			
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
				<table width="100%" border="0" cellpadding="0" cellspacing="0" >
					<tr height="'.$higinc.'" >
						<td  class="text_16b color_white" style="background-color:#cd532f; padding-left:5px;width:37%;color:white;"  align="left"   valign="middle" ><b>'.$name.'<br>'.$address.'</b></td>
						<td class="text_16b color_white" style="text-align:left;background-color:#cd532f;color:white">Charges posted after discharge summary was signed</td>
						<td style="background-color:#cd532f;color:white"  align="right" height="'.$imgheight.'" width="'.$imgwidth.'">'.$img_logo[0].'&nbsp;</td>
					</tr>
					<tr height="22" bgcolor="#F1F4F0">
						<td height="30"  class="text_b" style="background-color:#F1F4F0; padding-left:5px;width:37%;"  align="left"   valign="middle" ><b>Dr. '.$physician_name.'</b></td>
						<td class="text_b" style="text-align:left;background-color:#F1F4F0;">From&nbsp;'.$frm_date.'&nbsp;&nbsp;&nbsp;&nbsp;To&nbsp;'.$to_date.'</td>
						<td style="background-color:#F1F4F0;white-space:nowrap"  align="right" >Report Date '.$current_date.' by '.$user_name.'</td>
					</tr>
				</table>
				<table width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color:#F1F4F0;">		
					<tr  valign="top">
						<td align="left" class="text_b" width="30" height="30">Seq</td>
						<td align="left" class="text_b" width="130">Patient Name</td>
						<td align="left" class="text_b" width="95">DOB</td>
						<td align="left" class="text_b" width="60">ASC-ID</td>
						<td align="left" class="text_b" width="95">DOS</td>
						<td align="left" class="text_b" width="160">Procedure</td>
						<td align="left" class="text_b" width="150">DFT Date/Time</td>
						<td align="left" class="text_b" width="100">DFT Status</td>
						<td align="left" class="text_b" width="230">Discharge Summary Last Saved</td>
					</tr>
				</table>
			</page_header>';
			if($t == '1') {
				$procedure_sel_csv	 = trim(str_ireplace("<br>","  ",$procedure));
				$user_name_csv 		 =	'"'.trim($user_name).'"';
				$csv_content1 		.= ','."".','."From ".$from_dateNew." To ".$to_dateNew.','."".','.'"Charges posted after discharge summary was signed"'.','.'Report Date '.$current_date.' by '.$user_name_csv.','."\n";
			}
			
			$physician_name_csv 	 = '"Dr. '.trim($physician_name).'"';
			$csv_content 			.= "\n\n".$physician_name_csv.','."\n";
			$csv_content 			.= '"Seq"'.','.'"Patient Name"'.','.'"DOB"'.','.'"ASC-ID"'.','.'"DOS"'.','.'"Procedure"'.','.'"DFT Date/Time"'.','.'"DFT Status"'.','.'"Discharge Summary Last Saved"'.','."\n";
			$table.=$detailHeaderStr.'
			
			<table width="100%" border="0" cellpadding="0" cellspacing="0">';

			foreach($report_srgn as $report) {
				if($physician_name==$report['physician_name']) {	
					$asc_id=$report['asc_Id'];
					if(!$asc_id) {$asc_id=""; }
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
					
					$pt_status=$report['patient_status'];
					$redClass='';
					if($pt_status=='Canceled')
					{
						$redClass=" redClass";
						$pt_status='Cancelled';	
					}	

					//hs.saved_on AS dft_saved_date, hs.sent AS dft_sent_status					
					//Set Nbsp in Seq & Name
					$dft_saved_date				= $objManageData->getFullDtTmFormat($report['dft_saved_date']);
					$dft_sent_status			= ($report['dft_sent_status']=="1") ? "Sent" : "";
					$summary_last_modified		= $objManageData->getFullDtTmFormat($report['summary_last_modified']);
					
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
								<td class="'.$borderBottomFirstRow.' text_15" align="left" width="95">'.$date_of_birth.'</td>
								<td class="'.$borderBottomFirstRow.' text_15" align="left" width="60">'.$asc_id.'</td>					
								<td class="'.$borderBottomFirstRow.' text_15" align="left" width="95">'.$date_of_surgery.'</td>
								<td class="'.$borderBottomFirstRow.' text_15" align="left" width="160">'.wordwrap($procedure_name,35,"<br>",1).'</td>
								<td class="'.$borderBottomFirstRow.' text_15" align="left" width="150">'.$dft_saved_date.'</td>
								<td class="'.$borderBottomFirstRow.' text_15" align="left" width="100">'.$dft_sent_status.'</td>
								<td class="'.$borderBottomFirstRow.' text_15" align="left" width="250">'.$summary_last_modified.'</td>
							</tr>
						';
						//START CSV CODE
						$seq_csv 					=	'"'.trim($a).'"';
						$patient_name_csv 			=	'"'.trim($patient_name).'"';
						$asc_id_csv 				=	'"'.trim($asc_id).'"';
						$address_csv 				=	'"'.trim($address).'"';
						$contact_csv 				= 	'"'.trim($contact).'"';
						$date_of_birth_csv 			= 	'"'.trim($date_of_birth).'"';
						$procedure_name_csv			=	'"'.trim(str_ireplace("<br>","  ",$procedure_name)).'"';
						$pt_status_csv				=	'"'.trim($pt_status).'"';
						$date_of_surgery_csv		=	'"'.trim($date_of_surgery).'"';
						$from_time_csv				=	'"'.trim($from_time).'"';
						$dft_saved_date_csv			=	'"'.trim($dft_saved_date).'"';
						$dft_sent_status_csv		=	'"'.trim($dft_sent_status).'"';
						$summary_last_modified_csv	=	'"'.trim($summary_last_modified).'"';
						
						$csv_content 				.= $seq_csv.','.$patient_name_csv.','.$date_of_birth_csv.','.$asc_id_csv.','.$date_of_surgery_csv.','.$procedure_name_csv.','.$dft_saved_date_csv.','.$dft_sent_status_csv.','.$summary_last_modified_csv."\n";		
						//END CSV CODE
					}//ending of if statement
					$a++;
				}
			}
			$table .= '</table></page>';
			if(count($physician_name_arr) > $t){
				$table .= '<page backtop="35mm" backbottom="2mm">			
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
//if($_REQUEST['reportType']=='Detail')$table=$table;
//else $table=$reportSummary;
		
if($_REQUEST['hidd_report_format']=='csv' && imw_num_rows($physician)>0) {
	$file_name=$_SERVER['DOCUMENT_ROOT'].'/'.$surgeryCenterDirectoryName.'/admin/pdfFiles/charges_posted_reportpop.csv';
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
		location.href = "charges_posted_report.php?no_record=yes&date1=<?php echo $_REQUEST['startdate'];?>&date2=<?php echo $_REQUEST['enddate'];?>&procedure=<?php echo $_REQUEST['procedure'];?>&physician=<?php echo $physician_data_req;?>&status=<?php echo $_REQUEST['status'];?>&reportType=<?php echo $_REQUEST['reportType'];?>";
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

