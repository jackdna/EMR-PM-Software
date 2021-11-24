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
include_once("admin/classObjectFunction.php");
global $objManageData;
$objManageData = new manageData;
$loginUser = $_SESSION['loginUserId'];
//include("common/linkfile.php");

//get detail for logged in facility
$queryFac=imw_query("select * from facility_tbl where fac_id='$_SESSION[facility]'")or die(imw_error());
$dataFac=imw_fetch_object($queryFac);
$name=stripcslashes($dataFac->fac_name);
$address=stripcslashes($dataFac->fac_address1).' '.stripcslashes($dataFac->fac_address2).' '.stripcslashes($dataFac->fac_city).' '.stripcslashes($dataFac->fac_state);


//set surgerycenter detail 
$fac_qry	=	" and st.iasc_facility_id='$_SESSION[iasc_facility_id]' ";
$fac_con	=	($_SESSION['iasc_facility_id']	 ?	$fac_qry	 :	'' )	; 
			
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
 
$patientConfirmationIdSet 		= $_REQUEST['patientConfirmationId'];
$anesthesiologistIdSet				=	$_REQUEST['anesthesiologistId'];
$selected_date								= date('m-d-Y', strtotime($_REQUEST['startdate']));
$selected_date2								= date('m-d-Y', strtotime($_REQUEST['enddate']));
$reportType										=	$_REQUEST['report_type'];

$current_date=date("m-d-Y");
$img_logo = showThumbImages('new_html2pdf/white.jpg',170,50);
$imgheight= $img_logo[2]+8;
$imgwidth= $img_logo[1]+200;	
if(!trim($patientConfirmationIdSet)) {
	$patientConfirmationIdSet = '0';	
}
$userGrpQry = "SELECT u.usersId,u.fname, u.mname, u.lname 
								FROM narcotics_data_tbl ndt
								INNER JOIN users u ON (u.usersId=ndt.user_id)
								WHERE ndt.confirmation_id in(".$patientConfirmationIdSet.") 
								GROUP BY ndt.user_id 
							";
$userGrpRes = imw_query($userGrpQry) or die($userGrpQry.imw_error());
$userGrpCnt	=	imw_num_rows($userGrpRes);

$t	=	0;
$table	=	'';

$userIdArr = $userNameArr[0] = array();
if($userGrpCnt > 0)
{
		while($userGrpRow = imw_fetch_array($userGrpRes))
		{
			$userId		= $userGrpRow['usersId'];
			$userName	=	stripslashes($userGrpRow['fname']).' '.stripslashes($userGrpRow['mname']).' '.stripslashes($userGrpRow['lname']);
			$userIdArr[$userId] 	= $userId;
			$userNameArr[$userId] =	$userName;
		}
}

$userIdArr[0] = '0';
$userNameArr[0] = 'N/A';

$detailedData		=	array();
$summaryData		=	array();
$narcoticsData	=	array();

$anesSubQry	=	'';
if($anesthesiologistIdSet)
{
	$anesSubQry	=	" AND ndt.user_id IN (".$anesthesiologistIdSet.") ";
}

$medicationsArray = array();	
				
// Start Getting All Narcotics Data Record From DB
$fields	=	'ndt.*, pc.patient_primary_procedure, pc.ascId, pc.dos, 
					 pdt.patient_fname, pdt.patient_mname, pdt.patient_lname';
$query = "SELECT ".$fields." FROM patientconfirmation pc
						 INNER JOIN patient_data_tbl pdt ON pc.patientId = pdt.patient_id 
						 INNER JOIN narcotics_data_tbl ndt ON pc.patientConfirmationId = ndt.confirmation_id
						 INNER JOIN stub_tbl st ON (st.patient_confirmation_id = pc.patientConfirmationId AND st.chartSignedByAnes = 'green')
						 WHERE pc.patientConfirmationId in(".$patientConfirmationIdSet.") 
						  ".$anesSubQry." ".$fac_con."
						 ORDER BY ndt.user_fname ASC, ndt.user_lname ASC, pc.dos ASC, pc.surgery_time ASC, ndt.medicine_name ASC ";
$sql = 	imw_query($query) or die( 'Error found @ Line No. '.(__LINE__).': ' . imw_error());
	
while($row	=	imw_fetch_object($sql))
{
		$_pConfId		=	$row->confirmation_id;
		$_pAnesId		=	$row->user_id;
		$_procedure	=	stripslashes($row->patient_primary_procedure);
		$_pAscId		=	$row->ascId;
		$_pDos			=	date('m-d-Y', strtotime($row->dos));
		$_pFName		=	stripslashes($row->patient_fname);
		$_pMName		=	stripslashes($row->patient_mname);
		$_pLName		=	stripslashes($row->patient_lname);
		
		$_pFullName	=	$_pLName. ', ' . $_pFName . ($_pMName ? ' '.$_pMName : '' );
	
	
		$medicine_name=	ucfirst(strtolower($row->medicine_name));
		$medicine_qty	=	$row->quantity;
		
		// Collect Medications to list as column name in CSV
		if(!in_array($medicine_name,$medicationsArray))
			array_push($medicationsArray,$medicine_name);
			
		
		/******************************************************
		*
		* Check if array keys exists in Patient Detail Array
		* Anesthesionlogist ID/DOS/Patient Confirmation ID
		*
		*******************************************************/
		
		if(!array_key_exists($_pAnesId,$detailedData))	
			$detailedData[$_pAnesId] 	= array();
		if(!array_key_exists($_pDos,$detailedData[$_pAnesId]))
			$detailedData[$_pAnesId][$_pDos] 	= array();
		if(!array_key_exists($_pConfId,$detailedData[$_pAnesId][$_pDos]))
			$detailedData[$_pAnesId][$_pDos][$_pConfId] 	= array();
		
		
		/******************************************************
		*
		* Check if array keys exists in Narcotics Detail Array
		* Anesthesionlogist ID/DOS/Patient Confirmation ID
		*
		*******************************************************/
		
		if(!array_key_exists($_pAnesId,$narcoticsData))	
			$narcoticsData[$_pAnesId] 	= array();
		if(!array_key_exists($_pDos,$narcoticsData[$_pAnesId]))
			$narcoticsData[$_pAnesId][$_pDos] 	= array();
		if(!array_key_exists($_pConfId,$narcoticsData[$_pAnesId][$_pDos]))
			$narcoticsData[$_pAnesId][$_pDos][$_pConfId] 	= array();
			
	
		/******************************************************
		*
		* Check if array keys exists in Narcotics Summary Array
		* Anesthesionlogist ID
		*
		*******************************************************/	
		if(!array_key_exists($_pAnesId,$summaryData))
			$summaryData[$_pAnesId] 	= array();
	
	
		// Start Getting and calculating Narcotics Labels and Values
		if(!array_key_exists($medicine_name,$narcoticsData[$_pAnesId][$_pDos][$_pConfId]))
			$narcoticsData[$_pAnesId][$_pDos][$_pConfId][$medicine_name] = 0;
		if(!array_key_exists($medicine_name,$summaryData[$_pAnesId]))
			$summaryData[$_pAnesId][$medicine_name] = 0;	
			
						
		$narcoticsData[$_pAnesId][$_pDos][$_pConfId][$medicine_name] += $medicine_qty;
		$summaryData[$_pAnesId][$medicine_name] +=  $medicine_qty;
		
		
		// Keep Patient Information In PAtient Details Array 
		$detailedData[$_pAnesId][$_pDos][$_pConfId]['pname'] 	=	$_pFullName.($_pAscId ? '-'.$_pAscId : '');
		$detailedData[$_pAnesId][$_pDos][$_pConfId]['dos'] 		=	$_pDos;
		$detailedData[$_pAnesId][$_pDos][$_pConfId]['proc'] 	=	$_procedure;
		$detailedData[$_pAnesId][$_pDos][$_pConfId]['narco']  = $narcoticsData[$_pAnesId][$_pDos][$_pConfId];
		
}
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


natsort($medicationsArray);				// Sort Medications Key Array
$csvContent		=	array();
$csvContent[]	=	array($name,'','Surgery Center Narcotics Report '.ucwords($reportType).' '.date("m-d-Y"));
$csvContent[]	=	array($address,'','',);
$csvContent[]	=	array('','','');
$csvContent[] = array('', '', 'From : '.$selected_date, 'To : '.$selected_date2  );
$csvContent[]	=	array('','','',);

if($reportType == 'detail')
	$tempArr			=	array('S.No','Patient Name - ASC ID','Procedure');
else
 	$tempArr			=	array('S.No','Anesthesiolosgist',);
	
$csvContent[]	= array_merge($tempArr,$medicationsArray);

$recordCounter = 0;	
foreach($userIdArr as $anesId)
{
	if(count($detailedData[$anesId]) > 0 && is_array($detailedData[$anesId]) )	
	{
		$recordCounter++;
		$counter	=	0;
		$anesName = $userNameArr[$anesId];
		
		
		
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
						<td align="center" colspan="2" class="text_16b" style="background-color:#CCC; width:700px;">Surgery Center Narcotics Report '.(ucwords($reportType)).'</td>
					</tr>
					
					<tr height="20">
						<td class="text_18" valign="bottom" colspan="2" >
							<table style="width:700px;" border="0" cellpadding="0" cellspacing="0" >
								<tr>
									<td style="width:400px;"><b>Anesthesiologist: '.$anesName.'</b></td>
									<td style="width:300px;"><b>From : </b>'.$selected_date.'<b>&nbsp;To&nbsp;:&nbsp;</b>'.$selected_date2.'</td>
								</tr>
							</table>
						</td>		
					</tr>
					
					<tr height="5"><td colspan="2">&nbsp;</td></tr>
					
				</table>';
		
			$headerCols ='			
					<thead>
						<tr  valign="top">
							<td align="center" class="text_b BdrAll " style="width:30px;">S.No</td>
							<td align="left"   class="text_b BdrTBR " style="width:160px;">Patient Name - ASC ID</td>
							<td align="left"   class="text_b BdrTBR " style="width:260px;">Procedure</td>
							<td align="left"   class="text_b BdrTBR " style="width:250px;">Narcotics Detail</td>
						</tr>
					</thead>';
			
					
			if($reportType === 'detail')
			{	
				$csvContent[] = array('Anesthesiolosgist : '.$anesName, '', '');
				$csvContent[]	=	array('','','');
			
				/***********************************************
				* Start Creating HTML for Detailed Information
				************************************************/
				$table	.=	'
				<page backtop="30mm" backbottom="15mm">
					<page_header>'.$header.'</page_header>
					<page_footer>'.$footer.'</page_footer>';
				
				$table.='<table style="width:700px;" border="0" cellpadding="0" cellspacing="0" id="bodyTable">';	
				$table .= 	$headerCols;
					foreach($detailedData[$anesId] as $dos => $dosData)
					{
						
						if(is_array($dosData) && count($dosData) > 0 )
						{
							$table.= '<tr><td colspan="5" class="text_b BdrAll" style="background-color:#DDD;">DOS - '.$dos.'</td></tr>';
							$csvContent[] = array('DOS : '.$dos,'','');
							foreach($dosData as $pConfId => $patientData)
							{
									$counter++;
									$patientName	=	$patientData['pname'];
									$patientDos		=	$patientData['dos'];
									$patientProc	=	$patientData['proc'];
									$patientNarco	=	$patientData['narco'];
									//.'""'.','. '\n'
									$csvData = $csvPatData = $csvNarcoData = array();
									$csvPatData	 	= array($counter,$patientName,$patientProc);
									
									$table.= '<tr>';
									$table.= '<td align="center"   class="text BdrLBR" style="width:30px; ">'.$counter.'</td>';
									$table.= '<td align="left"   class="text BdrBR " style="width:150px;">'.$patientName.'</td>';
									$table.= '<td align="left"   class="text BdrBR " style="width:270px;">'.$patientProc.'</td>';
									$table.= '<td align="left"   class="text BdrBR " style="width:250px;">';
									if(is_array($patientNarco) && count($patientNarco) > 0 )
									{
										$narcoCount	=	count($patientNarco);
										$n = 0;
										$table.= '<table style="width:320px;" border="0" cellpadding="0" cellspacing="0">';
										foreach($patientNarco as $narcoLabel => $narcoDosage)
										{
											$n++;
											$table.= '<tr>
																	<td align="left" class="text '.($n < $narcoCount ? 'BdrBR' : 'BdrR' ).' " style="width:195px;" >'.$narcoLabel.'</td>
																	<td align="left" class="text '.($n < $narcoCount ? 'BdrB' : '' ).' " style="width:45px;" >'.$narcoDosage.'</td>	
																</tr>';
											/*if($n == 1) {
												$csvData[] =	$narcoLabel .' : '.$narcoDosage ;
												$csvContent[]	=	$csvData;
											}
											else $csvContent[] =	array('','','','',$narcoLabel .' : '.$narcoDosage);*/
										}
										$table.= '</table>';
									}
									/*** colleting Medication Dosage for CSV ***/
									foreach($medicationsArray as $medications)
										$csvNarcoData[] = ($patientNarco[$medications]) ? $patientNarco[$medications] : 0;
									
									$csvData = array_merge($csvPatData,$csvNarcoData);
									$csvContent[]	=	$csvData;
									/********************************************/
									
									$table.= '</td>';
									$table.= '</tr>';
									
							}
							
						}
					}
				$table.='</table>';
				
				$table.='</page>';
				
				$csvContent[]	=	array('','','');
			}
		
		
			/*************************************************************
			* Start Creating HTML for Summary Table of Anesthesioligist
			*************************************************************/
			
			$columnInRow 	= 2;
			$labelWidth 	= number_format(((700/$columnInRow) * 0.7),0);
			$dosageWidth 	= (700/$columnInRow) * 0.3;
			$colspan			=	$columnInRow * 2;
			if($reportType === 'detail')
				$csvSummaryData	=	array('Total','','');
			else
				$csvSummaryData	=	array($recordCounter,$anesName);
			$table	.=	'
				<page backtop="35mm" backbottom="15mm">
					<page_header>'.$header.'</page_header>
					<page_footer>'.$footer.'</page_footer>';
				
			$table.='<table style="width:700px; margin-top:20px;" border="0" cellpadding="0" cellspacing="0">';	
			$table.='<tr><td class="text_b BdrALL" colspan="'.$colspan.'" style="background-color:#DDD;">Summary</td></tr>';	
			if(is_array($summaryData[$anesId]) && count($summaryData[$anesId]) > 0 )
			{
				$narcoCount	=	count($summaryData[$anesId]);
				$n = 0;
				$k = 0;
				$table.= '<tr>';
				$csvSummaryStr =	'';
				foreach($summaryData[$anesId] as $narcoLabel => $narcoDosage)
				{
					$n++;
					$k++;
					$class1	=	($n < $narcoCount) 	? 'BdrL' : 'BdrL';
					$class1 =	($k == $columnInRow) ? 'BdrR' : 'BdrLB';
					$table.= '<td align="left" class="text BdrLB " style="width:'.$labelWidth.'px;" >'.$narcoLabel.'</td>';
					$table.= '<td align="left" class="text BdrLB '.$class1.' '.$class2.' " style="width:'.$dosageWidth.'px;" >'.$narcoDosage.'</td>';
					
					if($n == $narcoCount && (($n%2) <> 0))
					{
						$table.= '<td colspan="2" class="text BdrLBR" style="width:'.($labelWidth +$dosageWidth) .'px;" >&nbsp;</td>';
					}
					
					if($k == $columnInRow)
					{
						$k = 0;
						$table.= '</tr><tr>';
					}
					
					/*$csvSummaryStr	.= $narcoLabel.' : '.$narcoDosage.',';
					if($n%5 == 0 || $n == $narcoCount )	
					{
						$csvContent[]	=	explode(',',substr($csvSummaryStr,0,-1));
						$csvSummaryStr	=	'';
					}*/
					
				}
				$table.= '</tr>';
				
				/*** colleting Medication Dosage Toal for CSV ***/
				$csvSummaryNarcoData = array();
				foreach($medicationsArray as $medications)
					$csvSummaryNarcoData[] = ($summaryData[$anesId][$medications]) ? $summaryData[$anesId][$medications] : 0;
									
				$csvSummaryData = array_merge($csvSummaryData,$csvSummaryNarcoData);
				$csvContent[]		=	$csvSummaryData;
				/********************************************/
			}
			
			$table.='</table>';
			$table.='</page>';
			
			$csvContent[]	=	array('','','');
	
	}
}
//echo '<pre>';print_r($csvContent);exit;
//echo $table;
if($_REQUEST['hidd_report_format']=='csv' && imw_num_rows($userGrpRes)>0) {
	
	$file_name = $_SERVER['DOCUMENT_ROOT'].'/'.$surgeryCenterDirectoryName.'/admin/pdfFiles/narcotics_report_print_summary.csv';
	if(file_exists($file_name)) {
		@unlink($file_name);
	}
	$fpH1 = fopen($file_name,'w');
	foreach($csvContent as $csv)
	{
		fputcsv($fpH1, $csv);	
	}
	
	//fwrite($fpH1, $csv_content1."\n");
	//fwrite($fpH1, $csv_content2."\n\r");
	//fwrite($fpH1, $csv_content."\n\r");
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
<title>Narcotics Report</title>
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