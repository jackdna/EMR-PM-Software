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
$get_http_path=$_REQUEST['get_http_path'];
include_once("admin/classObjectFunction.php");
global $objManageData;
$objManageData = new manageData;
//$loginUser = $_SESSION['loginUserId'];
//include("common/linkfile.php");

//get detail for logged in facility
$queryFac=imw_query("select * from facility_tbl where fac_id='$_SESSION[facility]'")or die(imw_error());
$dataFac=imw_fetch_object($queryFac);
$name=stripcslashes($dataFac->fac_name);
$address=stripcslashes($dataFac->fac_address1).' '.stripcslashes($dataFac->fac_address2).' '.stripcslashes($dataFac->fac_city).' '.stripcslashes($dataFac->fac_state);


//set surgerycenter detail 
//$fac_qry	=	" and st.iasc_facility_id='$_SESSION[iasc_facility_id]' ";
//$fac_con	=	($_SESSION['iasc_facility_id']	 ?	$fac_qry	 :	'' )	; 
			
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

$size=getimagesize('new_html2pdf/white.jpg');
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

$selected_date								= date('m-d-Y', strtotime($_REQUEST['startdate']));
$selected_date2								= date('m-d-Y', strtotime($_REQUEST['enddate']));
$reportType										=	$_REQUEST['report_type'];

$current_date=date("m-d-Y");
$img_logo = showThumbImages('new_html2pdf/white.jpg',170,50);
$imgheight= $img_logo[2]+8;
$imgwidth= $img_logo[1]+200;
/*
$query = "SELECT 
	`cl`.`id`,
	`pc`.`dos`, 
	`pd`.`patient_fname` AS 'fname', 
	`pd`.`patient_mname` AS 'mname', 
	`pd`.`patient_lname` AS 'lname', 
	`pd`.`date_of_birth` AS 'dob', 
	`pd`.`amd_patient_id` AS 'amd_id', 
	`cl`.`amd_visit_id`, 
	`cl`.`status`, 
	`cl`.`reason`, 
	`cl`.`date_posted`, 
	`cl`.`m_amd_visit_id`, 
	`cl`.`type` 
FROM 
	`patientconfirmation` `pc` 
	INNER JOIN `patient_data_tbl` `pd` ON(
		`pc`.`patientId` = `pd`.`patient_id`
	) 
	INNER JOIN `stub_tbl` `st` ON(
		`pc`.`patientConfirmationId` = `st`.`patient_confirmation_id`
	) 
	INNER JOIN `patient_in_waiting_tbl` `wt` ON(
		`st`.`iolink_patient_in_waiting_id` = `wt`.`patient_in_waiting_id`
	) 
	INNER JOIN (SELECT * FROM `amd_charges_log` WHERE `id` IN(SELECT MAX(`id`) FROM `amd_charges_log` GROUP BY `m_amd_visit_id`,`type`)) `cl` ON(
		`wt`.`amd_visit_id` = `cl`.`m_amd_visit_id`
	) 
WHERE 
	`pc`.`dos` BETWEEN '".$_REQUEST['startdate']."' AND '".$_REQUEST['enddate']."'
GROUP BY `cl`.`m_amd_visit_id`, `type`";
*/
$query = "SELECT 
	`cl`.`id`,
	`pc`.`dos`, 
	`pd`.`patient_fname` AS 'fname', 
	`pd`.`patient_mname` AS 'mname', 
	`pd`.`patient_lname` AS 'lname', 
	`pd`.`date_of_birth` AS 'dob', 
	`pd`.`amd_patient_id` AS 'amd_id', 
	`cl`.`amd_visit_id`, 
	`cl`.`status`, 
	`cl`.`reason`, 
	`cl`.`date_posted`, 
	`cl`.`m_amd_visit_id`, 
	`cl`.`type` 
FROM 
	`patientconfirmation` `pc` 
	INNER JOIN `patient_data_tbl` `pd` ON(
		`pc`.`patientId` = `pd`.`patient_id`
	) 
	INNER JOIN `stub_tbl` `st` ON(
		`pc`.`patientConfirmationId` = `st`.`patient_confirmation_id`
	) 
	INNER JOIN `patient_in_waiting_tbl` `wt` ON(
		`st`.`iolink_patient_in_waiting_id` = `wt`.`patient_in_waiting_id`
	) 
	INNER JOIN `amd_charges_log` `cl` ON(`wt`.`amd_visit_id` = `cl`.`m_amd_visit_id`)
	INNER JOIN (SELECT MAX(`id`) AS `id` FROM `amd_charges_log` GROUP BY `m_amd_visit_id`,`type`) `clg` ON `cl`.id = `clg`.id
WHERE 
	`pc`.`dos` BETWEEN '".$_REQUEST['startdate']."' AND '".$_REQUEST['enddate']."'";
$resp = imw_query($query);


$chargesList = array();
if( $resp && imw_num_rows($resp) > 0 )
{
	while( $row = imw_fetch_assoc($resp) )
	{
		if( !isset($chargesList[$row['m_amd_visit_id']]) )
			$chargesList[$row['m_amd_visit_id']] = array();
		
		$visitChargeData = &$chargesList[$row['m_amd_visit_id']];
		
		$visitChargeData['dos'] = date('m-d-Y', strtotime($row['dos']));
		$visitChargeData['m_visit'] = $row['m_amd_visit_id'];
		
		$visitChargeData['name'] = $row['fname'].', '.$row['lname'];
		$visitChargeData['name'] .= ( trim($row['mname'])!='' )? ' '.$row['mname'] : '';
		
		$visitChargeData['dob'] = date('m-d-Y', strtotime($row['dob']));
		$visitChargeData['amd_id'] = $row['amd_id'];
		
		//$visitChargeData['date_posted'] = date('m-d-Y H:i:s', strtotime($row['date_posted']));
		
		if( $row['type'] == '1' )
		{
			$visitChargeData['phy_stauts'] = (bool)$row['status'];
			$visitChargeData['phy_reason'] = trim($row['reason']);
			$visitChargeData['phy_visit_id'] = trim($row['amd_visit_id']);
			$visitChargeData['phy_posted_date'] = date('m-d-Y H:i:s', strtotime($row['date_posted']));
		}
		elseif( $row['type'] =='2' )
		{
			$visitChargeData['fac_stauts'] = (bool)$row['status'];
			$visitChargeData['fac_reason'] = trim($row['reason']);
			$visitChargeData['fac_visit_id'] = trim($row['amd_visit_id']);
			$visitChargeData['fac_posted_date'] = date('m-d-Y H:i:s', strtotime($row['date_posted']));
		}
		elseif( $row['type'] == '3' )
		{
			$visitChargeData['anes_stauts'] = (bool)$row['status'];
			$visitChargeData['anes_reason'] = trim($row['reason']);
			$visitChargeData['anes_visit_id'] = trim($row['amd_visit_id']);
			$visitChargeData['anes_posted_date'] = date('m-d-Y H:i:s', strtotime($row['date_posted']));
		}
	}
}

//print "\n\n";
//
//print_r($chargesList);
//
//die;


//echo '<pre>'; print_r($detailedData); exit;
// HTML Generation Starts Here
$borderBottomFirstRow = $borderBottomSecondRow = 'bottomBorder';
	$table.='
			<style>
				.BdrAll { 
					border:solid 1px #999; 
					padding-top:2px; padding-bottom:2px; padding-left:2px; font-family:Arial, Helvetica, sans-serif;	
				}
				.BdrTBR { 
					border:solid 1px #999; border-left: solid 0px #fff;
					padding-top:2px; padding-bottom:2px; padding-left:2px; font-family:Arial, Helvetica, sans-serif;
				}
				.BdrLBR { 
					border:solid 1px #999; border-top: solid 0px #fff;
					padding-top:2px; padding-bottom:2px; padding-left:2px; font-family:Arial, Helvetica, sans-serif;
				}
				.BdrLB { 
					border-bottom: solid 1px #999;
					border-left: solid 1px #999;
					padding-top:2px; padding-bottom:2px; padding-left:2px; font-family:Arial, Helvetica, sans-serif;
				}
				.BdrBR {
					border-bottom: solid 1px #999;
					border-right: solid 1px #999;
					padding-top:2px; padding-bottom:2px; padding-left:2px; font-family:Arial, Helvetica, sans-serif;
				}
				.BdrR {
					border-right: solid 1px #999;
					padding-top:2px; padding-bottom:2px;  padding-left:2px; font-family:Arial, Helvetica, sans-serif;
				}
				.BdrB {
					border-bottom: solid 1px #999;
					padding-top:2px; padding-bottom:2px;  padding-left:2px; font-family:Arial, Helvetica, sans-serif;
				}
				
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
				.color_white{
					color:#FFFFFF;
				}
				.text{
					font-size:13px;
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
				
			
			</style>
						
			';	

$recordCounter = 0;
$successImg = '<img src="check_mark16.jpg">';
$errorImg = '<img src="Cr.jpg">';

if(count($chargesList) > 0 )	
{
	$counter	=	0;
	
	/******************************************
	* Start Creating HTML for Report Footer
	*******************************************/
	$footer	=	'';
	$footer.=	'
				
				<table style="width:700px;">
					<tr>
						<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			';
			
	/******************************************
	* Start Creating HTML for Report Header
	*******************************************/
	$header = '';
	$header.='
			<table style="width:700px;" border="0" cellpadding="0" cellspacing="0">
				<tr style="background-color:#cd532f;" >
					<td  class="text_16b color_white" style="background-color:#cd532f; padding-left:5px; width:500px;" align="left" valign="middle" ><b>'.$name.'<br>'.$address.'</b></td>
					<td style="background-color:#cd532f; border:solid 1px #cd532f; width:200px;" align="right" height="'.$imgheight.'">'.$img_logo[0].'</td>
				</tr>
				
				<tr height="20">
					<td align="center" colspan="2" class="text_16b" style="background-color:#CCC; width:700px;">Surgery Center AMD Charges Report '.(ucwords($reportType)).'</td>
				</tr>
				
				<tr height="5"><td colspan="2">&nbsp;</td></tr>
				
			</table>';
	
		$headerCols ='			
				<thead>
					<tr  valign="top">
						<td align="center" class="text_b BdrAll" style="width:40px;">S.No.</td>
						<td align="left"   class="text_b BdrTBR" style="width:230px;">Patient Name (DOB) - AMD ID</td>
						<td align="left"   class="text_b BdrTBR" style="width:110px;">DOS/Date Posted</td>
						<td align="center"   class="text_b BdrTBR" style="width:100px;">AMD Visit ID</td>
						<td align="center"   class="text_b BdrTBR" style="width:80px;">Physician</td>
						<td align="center"   class="text_b BdrTBR" style="width:60px;">Facility</td>
						<td align="center"   class="text_b BdrTBR" style="width:80px;">Anesthesia</td>
					</tr>
				</thead>';
		
				
		//if($reportType === 'detail')
		//{
			/***********************************************
			* Start Creating HTML for Detailed Information
			************************************************/
			$table	.=	'
			<page backtop="30mm" backbottom="15mm">
				<page_header>'.$header.'</page_header>
				<page_footer>'.$footer.'</page_footer>';
			
			$table.='<table style="width:700px;" border="0" cellpadding="0" cellspacing="0" id="bodyTable">';	
			$table .= 	$headerCols;
				foreach($chargesList as $chargeData)
				{
					$recordCounter++;
					$counter++;
						$table .= '<tr>';
						$table .= '<td align="center" class="BdrLBR">'.$counter.'</td>';
						$table .= '<td class="BdrBR">'.$chargeData['name'].' - ('.$chargeData['dob'].') - '.$chargeData['amd_id'].'</td>';
						$table .= '<td class="BdrBR">'.$chargeData['dos'].' /<br />'.$chargeData['phy_posted_date'].'</td>';
						$table .= '<td align="center" class="BdrBR">'.$chargeData['m_visit'].'</td>';
						$table .= '<td align="center" class="BdrBR">'.( ($chargeData['phy_stauts'])?$successImg.'<br />'.$chargeData['phy_visit_id']:$errorImg ).'</td>';
						$table .= '<td align="center" class="BdrBR">'.( ($chargeData['fac_stauts'])?$successImg.'<br />'.$chargeData['fac_visit_id']:$errorImg ).'</td>';
						$table .= '<td align="center" class="BdrBR">'.( ($chargeData['anes_stauts'])?$successImg.'<br />'.$chargeData['anes_visit_id']:$errorImg ).'</td>';
						$table .= '</tr>';
				}
			$table.='</table>';
			
			$table.='</page>';
		//}
}
if( $_REQUEST['hidd_report_format']=='csv' )
{
	$file_name = $_SERVER['DOCUMENT_ROOT'].'/'.$surgeryCenterDirectoryName.'/admin/pdfFiles/narcotics_report_print_summary.csv';
	
	if( file_exists($file_name) )
	{
		@unlink($file_name);
	}
	
	$fpH1 = fopen($file_name,'w');
	
	foreach( $csvContent as $csv )
	{
		fputcsv($fpH1, $csv);
	}
	
	//fwrite($fpH1, $csv_content1."\n");
	//fwrite($fpH1, $csv_content2."\n\r");
	//fwrite($fpH1, $csv_content."\n\r");
	$objManageData->download_file($file_name);
	fclose($fpH1);
	exit;
}
else
{
	$fileOpen = fopen('new_html2pdf/pdffile.html','w+');
	$intBytes = fputs($fileOpen,$table);
	//echo $table;die;
	fclose($fileOpen);
}
		
?>	
<!DOCTYPE html>
<html>
<head>
<title>Narcotics Report</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="css/style_surgery.css" type="text/css" >   

<script language="javascript">
	window.focus();
	function submitfn()
	{
		document.printFrm.submit();
	}
</script>
</head>
<body >
<form name="printFrm" action="new_html2pdf/createPdf.php?op=p" method="post">

</form>
<?php if($recordCounter > 0) { ?>		
	<script type="text/javascript">
        window.focus();
		submitfn();
    </script>
<?php 
} else {?>
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