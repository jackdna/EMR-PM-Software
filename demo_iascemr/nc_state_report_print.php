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
if($asc) {
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
$imgwidth= $img_logo[1]+200;	

// Users Data
$userGrpQry = "SELECT u.usersId,u.fname, u.mname, u.lname 
								FROM patientconfirmation pc
								INNER JOIN users u ON (u.usersId=pc.surgeonId)
								INNER JOIN stub_tbl st ON (st.patient_confirmation_id = pc.patientConfirmationId ".$ascQry.")
								WHERE pc.patientConfirmationId in(".$patientConfirmationIdSet.") AND pc.surgeonId!='0' AND pc.ascId !='0' 
								GROUP BY pc.surgeonId 
								ORDER BY pc.surgery_time";
$userGrpRes = imw_query($userGrpQry) or die($userGrpQry.imw_error());
$t=0;
$table = $tableCounty = '';
$surgeonIdArr = $surgNameArr = array();
$csv_content = '';
if(imw_num_rows($userGrpRes)>0) {
	while($userGrpRow 		= imw_fetch_array($userGrpRes)) {
		$uId				= $userGrpRow['usersId'];
		$surgeonIdArr[$uId] = $userGrpRow['usersId'];
		$surgNameArr[$uId] 	= $userGrpRow['fname'].' '.$userGrpRow['mname'].' '.$userGrpRow['lname'];	
	}
}

$users = implode(",",array_filter(array_keys($surgeonIdArr)));
// Start Collecting NC State Report Data
$query = "SELECT pc.patientConfirmationId, pc.patient_primary_procedure, pc.surgeonId, pc.dos, pc.ascId, pr.code, pr.catId, 
			st.imwPatientId, op.surgeryORNumber, CONCAT(patient_lname,', ',patient_fname,' ',patient_mname) AS patient_name
			FROM patientconfirmation pc  
			INNER JOIN stub_tbl st ON (st.patient_confirmation_id = pc.patientConfirmationId ".$ascQry.")
			INNER JOIN procedures pr ON (pr.procedureId = pc.patient_primary_procedure_id )
			INNER JOIN patient_data_tbl pd ON (pd.patient_id = pc.patientId )
			LEFT JOIN operatingroomrecords op ON (op.confirmation_id = pc.patientConfirmationId)
			WHERE pc.surgeonId IN (".$users.") AND pc.patientConfirmationId in(".$patientConfirmationIdSet.") AND pc.ascId !='0'  
			ORDER BY pr.code, pc.patient_primary_procedure, pc.dos";
$sql = imw_query($query) or die('Error found @ Line No. '.(__LINE__).': '.imw_error());				
$cnt = imw_num_rows($sql);
$laserCnt = $imwPatientIdImplode = 0;
$nc_state_data = $imwPatientIdArr = $imwCountyDataArr = array();

if( $cnt ){
	while( $row = imw_fetch_object($sql)) {
		$confId 		= $row->patientConfirmationId;
		$dos 			= $row->dos;
		$ascId 			= $row->ascId;
		$patient_name 	= stripslashes($row->patient_name);
		$patient_primary_procedure = preg_replace("/[\n\r]/",",",stripslashes($row->patient_primary_procedure));
		$cpt_code 		= $row->code;
		$proc_cat_id 	= $row->catId;
		$surgeryORNumber= $row->surgeryORNumber;
		$imwPatientIdArr[]	= $row->imwPatientId;
		if( $patient_primary_procedure ) {
			if($proc_cat_id !="2") {
				if(trim($surgeryORNumber) == '1') {
					$nc_state_data['OR1'][$cpt_code][$patient_primary_procedure] += 1; 
					$imwCountyDataArr['OR1'][$confId] = $row->imwPatientId;
				}elseif(trim($surgeryORNumber) == '2') {
					$nc_state_data['OR2'][$cpt_code][$patient_primary_procedure] += 1; 
					$imwCountyDataArr['OR2'][$confId] = $row->imwPatientId;
				}else {
					$nc_state_data['BLANK'][$cpt_code][$patient_primary_procedure] += 1; 
					$imwCountyDataArr['BLANK'][$confId] = $row->imwPatientId;
					
					$noOrDataArr[$dos][$confId]['ascid'] = $ascId;
					$noOrDataArr[$dos][$confId]['patient_name'] = $patient_name;
					$noOrDataArr[$dos][$confId]['dos'] = $dos;
					$noOrDataArr[$dos][$confId]['cpt_code'] = $cpt_code;
					$noOrDataArr[$dos][$confId]['patient_primary_procedure'] = $patient_primary_procedure;
					
				}

			}else {
				$laserCnt++;
				$imwCountyDataArr['Laser Room'][$confId] = $row->imwPatientId;
			}
		}
	}
	$imwPatientIdImplode = implode(",", $imwPatientIdArr);
}
if($laserCnt > 0 ) {
	$nc_state_data['Laser Room']['']['Yag Laser'] = $laserCnt;	
}

imw_close($link); //CLOSE SURGERYCENTER CONNECTION
include('connect_imwemr.php'); // imwemr connection
$ptCountyArr = array();
$imwQry = "SELECT pid, county FROM patient_data WHERE pid in(".$imwPatientIdImplode.") ORDER BY pid";
$imwRes = imw_query($imwQry) or die('Error found @ Line No. '.(__LINE__).': '.imw_error());				
if(imw_num_rows($imwRes)>0) {
	while($imwRow = imw_fetch_assoc($imwRes)) {
		$imwPtId = $imwRow["pid"];
		$county = $imwRow["county"];
		$ptCountyArr[$imwPtId] = $county;	
	}
}

imw_close($link_imwemr); //CLOSE IMWEMR CONNECTION
include("common/conDb.php");
$countyMainArr = array();
foreach($imwCountyDataArr as $orNumKey => $imwCountyArr ) {
	foreach($imwCountyArr as $ptConfId => $imwPatientId ) {
		$ptCounty = $ptCountyArr[$imwPatientId];
		if(!$ptCounty) {
			$ptCounty = "Unknown (Not Specified)";	
		}
		$countyMainArr[$orNumKey][$ptCounty] += 1;	
	}
}
//knatsort($countyMainArr);
array_multisort(array_keys($countyMainArr["OR1"]), SORT_NATURAL, $countyMainArr["OR1"]);
array_multisort(array_keys($countyMainArr["OR2"]), SORT_NATURAL, $countyMainArr["OR2"]);
array_multisort(array_keys($countyMainArr["BLANK"]), SORT_NATURAL, $countyMainArr["BLANK"]);
array_multisort(array_keys($noOrDataArr), SORT_NATURAL, $noOrDataArr);
//print'<pre>';print_r($countyMainArr);die;
// Start Printing Data
	$headStyle='style="border-bottom:solid 1px #333; background-color:#CCC;"';
	$rowStyle='style="border-bottom:solid 1px #333; "';
	$page_header = '
		<page_header>
			<table width="100%" border="0" cellpadding="0" cellspacing="0" >
				<tr height="'.$higinc.'" >
					<td  class="text_16b color_white" style="background-color:#cd532f; padding-left:5px;width:40%;color:white;"  align="left"   valign="middle" ><b>'.$name.'<br>'.$address.'</b></td>
					<td class="text_16b color_white" style="width:40%;text-align:left;background-color:#cd532f;color:white">NC State Report {OR_NUMBER}<br>{COUNTY_WISE}</td>
					<td style="background-color:#cd532f;width:20%;color:white"  align="right" height="'.$imgheight.'" width="'.$imgwidth.'">'.$img_logo[0].'</td>
				</tr>
				<tr height="22" bgcolor="#F1F4F0">
					<td align="left" colspan="3" class="text_16b">
						From&nbsp;'.$frm_date.'&nbsp;&nbsp;&nbsp;&nbsp;To&nbsp;'.$to_date.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Report Date '.$current_date.' by '.$user_name.'
					</td>
				</tr>
			</table>
		</page_header>';
						
		$thead = '
			<thead>
				
				<tr valign="middle">
					<th align="left" width="60" '.$headStyle.' >S.No</th>
					<th align="left" width="120" '.$headStyle.'>CPT Code</th>
					<th align="left" width="380" '.$headStyle.'>Procedure Name</th>
					<th align="center" width="130" '.$headStyle.'>Cases</th>
				</tr>
			</thead>';

		$theadCounty = '
			<thead>
				
				<tr valign="middle">
					<th align="left" width="690" colspan="4"  >&nbsp;</th>
				</tr>
				<tr valign="middle">
					<th align="center" width="690" colspan="4" '.$headStyle.'  >NC State Report County Wise {OR_NUMBER}</th>
				</tr>
				<tr valign="middle">
					<th align="left" width="60" '.$headStyle.' >S.No</th>
					<th align="left" width="150" '.$headStyle.'>County</th>
					<th align="center" width="180" '.$headStyle.'>Appointments</th>
					<th align="left" width="300" '.$headStyle.'>&nbsp;</th>
				</tr>
			</thead>';

		$theadNoOr = '
			<thead>
				<tr valign="middle">
					<th align="left" width="690" colspan="6"  >&nbsp;</th>
				</tr>
				<tr valign="middle">
					<th align="center" width="690" colspan="6" '.$headStyle.'  >NC State Report - List Of Patients (OR Not Recorded) </th>
				</tr>
				<tr valign="middle">
					<th align="left" width="60" '.$headStyle.' >S.No</th>
					<th align="left" width="100" '.$headStyle.'>DOS</th>
					<th align="left" width="130" '.$headStyle.'>Patient Name</th>
					<th align="left" width="100" '.$headStyle.'>ASC-ID</th>
					<th align="left" width="100" '.$headStyle.'>CPT Code</th>
					<th align="left" width="200" '.$headStyle.'>Procedure Name</th>
				</tr>
			</thead>';
	
	$page_footer = '
		<page_footer>
			<table style="width: 100%;">
				<tr><td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td></tr>
			</table>
		</page_footer>';
		
		
	$table .='
			<style>
				.text_16b{
					font-size:16px;
					font-family:Arial, Helvetica, sans-serif;
					font-weight:bold;
					color:#000000;
				}
				.color_white{
					color:#FFFFFF;
				}
			</style>';
	
	
	$sur_name = $surgNameArr[$surgeonId];
	$ncORNumArr = array("Laser Room","OR1", "OR2", "BLANK");
	if( count($nc_state_data) > 0 ) {
		foreach($ncORNumArr as $ncORNum) {
			if(count($nc_state_data[$ncORNum])>0) {
				$orNumVal = $ncORNum;
				if($ncORNum == "BLANK") {
					$orNumVal = "OR Not Recorded";	
				}
				$total = 0;
				$counter = 0;
				$csv_content 			.= '"'.$name.'"'.','."".','."NC State Report ".'('.$orNumVal.')  '.date("m-d-Y").','."\n";
				$csv_content 			.= '"'.$address.'"'.','."".','."DOS From ".$frm_date." To ".$to_date.','."\n\r";
				$csv_content 			.= '"S.No"'.','.'"CPT Code"'.','.'"Procedure Name"'.','.'"Cases"'."\n";
				$page_headerMain= $page_header;
				$page_headerMain= str_replace('{COUNTY_WISE}','',$page_headerMain);
				$page_headerMain= str_replace('{OR_NUMBER}','('.$orNumVal.')',$page_headerMain);
				$table .= '<page backtop="25mm" backbottom="15mm">'.$page_footer.$page_headerMain;
				$table .= '<table width="100%" border="0" cellpadding="2" cellspacing="0">';	
				$table .= $thead;
				$table .= '<tbody>';
				foreach($nc_state_data[$ncORNum] as $priProcCode => $tmpPriProc)
				{
					foreach( $tmpPriProc as $priProcName => $count)
					{
							
							$counter++; $total = $total+$count;
							$table .='
								<tr valign="top">
									<td '.$rowStyle.' width="60">'.$counter.'</td>
									<td '.$rowStyle.' width="120">'.htmlentities($priProcCode).'</td>
									<td '.$rowStyle.' width="380">'.htmlentities($priProcName).'</td>
									<td '.$rowStyle.' align="center" width="130">'.$count.' '.$countyMain.'</td>
								</tr>
								';
							$csv_content .= '"'.$counter.'"' . ',' . '"'.$priProcCode.'"' . ',' .'"'.$priProcName.'"' . ',' . '"'.$count.'"'."\n";	
						
					}
				}

				if($total > 0) {
					$table .='
						<tr valign="top">
							<th align="left" colspan="3" width="560" '.$headStyle.' >Total Cases</th>
							<th align="center" width="130" '.$headStyle.'>'.$total.'</th>
						</tr>
						';
					$csv_content .= '"Total Cases"' . ',' . '' . ',' . '' . ',' . '"'.$total.'"'."\n\n\n";		
				}
				$table .= '</tbody></table>';	
				//$table .= "</page>";
			
				
				//START SHOWING COUNT OF PATIENT COUNTY
				$counterCounty = $totalCounty = 0;
				$tableCounty = "";
				if(count($countyMainArr[$ncORNum])>0) {
					$csv_content 			.= '"'.$name.'"'.','."".','."NC State Report County Wise ".'('.$orNumVal.')  '.date("m-d-Y").','."\n";
					$csv_content 			.= '"'.$address.'"'.','."".','."DOS From ".$frm_date." To ".$to_date.','."\n\n\r";
					
					$csv_content 			.= '"S.No"'.','.'"County"'.','.'"Appointments"'.','.'""'."\n";
					
					
					$page_headerCounty 		= $page_header;
					$page_headerCounty 		= str_replace('{COUNTY_WISE}','County Wise',$page_headerCounty);
					$page_headerCounty 		= str_replace('{OR_NUMBER}','('.$orNumVal.')',$page_headerCounty);
					//$tableCounty 			.= '<page backtop="25mm" backbottom="15mm">'.$page_footer.$page_headerCounty;
					$tableCounty 			.= '<table width="100%" border="0" cellpadding="2" cellspacing="0">';	
					$theadCountyMain		= $theadCounty;
					$theadCountyMain		= str_replace('{OR_NUMBER}','('.$orNumVal.')',$theadCountyMain);
					$tableCounty 			.= $theadCountyMain;
					$tableCounty 			.= '<tbody>';
					foreach($countyMainArr[$ncORNum] as $countyMainKey => $countyMainCount) {
						$counterCounty++;
						$totalCounty = $totalCounty+$countyMainCount;
						$tableCounty .='
							<tr valign="top">
								<td '.$rowStyle.' width="60">'.$counterCounty.'</td>
								<td '.$rowStyle.' width="150">'.htmlentities($countyMainKey).'</td>
								<td '.$rowStyle.' align="center" width="180">'.$countyMainCount.'</td>
								<td '.$rowStyle.' align="left" width="300">&nbsp;</td>
							</tr>
							';
						$csv_content .= '"'.$counterCounty.'"' . ',' . '"'.$countyMainKey.'"' . ',' .'"'.$countyMainCount.'"' . ',' . '' ."\n";	
					}
					if($totalCounty > 0) {
						$tableCounty .='
							<tr valign="top">
								<th align="left" colspan="2" width="210" '.$headStyle.' >Total</th>
								<th align="center" width="180" '.$headStyle.'>'.$totalCounty.'</th>
								<td '.$headStyle.' align="left" width="300">&nbsp;</td>
							</tr>
							';
						$csv_content .= '"Total Appointments"' . ',' . '' . ',' . '"'.$totalCounty.'"' . ',' . '' ."\n\n\n";		
					}
					
					$tableCounty .= '</tbody></table>';	
					$table .= $tableCounty;
				}
				//$table .= "</page>";
				//END SHOWING COUNT OF PATIENT COUNTY
				if($ncORNum == "BLANK") {
					//$total = 0;
					$counterNoOrData = 0;
					$csv_content 			.= '"'.$name.'"'.','."".','."NC State Report ".'('.$orNumVal.')  '.date("m-d-Y").','."\n";
					$csv_content 			.= '"'.$address.'"'.','."".','."DOS From ".$frm_date." To ".$to_date.','."\n\r";
					$csv_content 			.= '"S.No"'.','.'"CPT Code"'.','.'"Patient Name"'.','.'"DOS"'.','.'"ASC-ID"'.','.'"Procedure Name"'."\n";
					$page_headerNoOrMain= $page_header;
					$page_headerNoOrMain= str_replace('{COUNTY_WISE}','',$page_headerNoOrMain);
					$page_headerNoOrMain= str_replace('{OR_NUMBER}','('.$orNumVal.')',$page_headerNoOrMain);
					//$table .= '<page backtop="25mm" backbottom="15mm">'.$page_footer.$page_headerNoOrMain;
					$table .= '<table width="100%" border="0" cellpadding="2" cellspacing="0">';	
					$table .= $theadNoOr;
					$table .= '<tbody>';
					foreach($noOrDataArr as $noOrDOSKey => $noOrDOSArr) {
						foreach($noOrDOSArr as $noOrConfId => $apptArr) {
							$counterNoOrData++; 
							$noOrDOS = date("m-d-Y", strtotime($apptArr["dos"]));
							$table .='
								<tr valign="top">
									<td '.$rowStyle.' align="left" width="60">'.$counterNoOrData.'</td>
									<td '.$rowStyle.' align="left" width="100">'.$noOrDOS.'</td>
									<td '.$rowStyle.' align="left" width="130">'.$apptArr["patient_name"].'</td>
									<td '.$rowStyle.' align="left" width="100">'.$apptArr["ascid"].'</td>
									<td '.$rowStyle.' align="left" width="100">'.$apptArr["cpt_code"].'</td>
									<td '.$rowStyle.' align="left" width="200">'.$apptArr["patient_primary_procedure"].'</td>
								</tr>
								';
							$csv_content .= '"'.$counterNoOrData.'"' . ',' . '"'.$noOrDOS.'"' . ',' .'"'.$apptArr["patient_name"].'"' . ',' .'"'.$apptArr["ascid"].'"' . ',' .'"'.$apptArr["cpt_code"].'"' . ',' .'"'.$apptArr["patient_primary_procedure"].'"' . ',' . '' ."\n";	
						}
					}
					$table .= '</tbody></table>';	
						
				}
				$table .= "</page>";
			}
		}
	}

	//die($table);
	//$csv_content1 = $name.','."".','."NC State Report ".date("m-d-Y").','."";
	//$csv_content2 = $address.','."".','."DOS From ".$frm_date." To ".$to_date.','."";
	//echo $csv_content;exit;

	if($_REQUEST['hidd_report_format']=='csv' && imw_num_rows($userGrpRes)>0) {
		$file_name=$_SERVER['DOCUMENT_ROOT'].'/'.$surgeryCenterDirectoryName.'/admin/pdfFiles/nc_state_report_print.csv';
		if(file_exists($file_name)) {
			@unlink($file_name);
		}
		$fpH1 = fopen($file_name,'w');
		//fwrite($fpH1, $csv_content1."\n");
		//fwrite($fpH1, $csv_content2."\n\r");
		fwrite($fpH1, $csv_content."\n\r");
		$objManageData->download_file($file_name);
		fclose($fpH1);
		exit;
	} else {
		$fileOpen = fopen('new_html2pdf/pdffile.html','w+');
		$intBytes = fputs($fileOpen,$table);
		fclose($fileOpen);
	}
		
?>	
<!DOCTYPE html>
<html>
<head>
<title>NC State Report</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="nofollow">
<meta name="googlebot" content="noindex">
<link rel="stylesheet" href="css/style_surgery.css" type="text/css" >   

<script language="javascript">
	function submitfn()
	{
		window.focus();
		document.printFrm.submit();
	}
</script>
</head>
<body>
<form name="printFrm" action="new_html2pdf/createPdf.php?op=p" method="post">

</form>
<?php 
if($total > 0){?>
	<script type="text/javascript">
        window.focus();
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

