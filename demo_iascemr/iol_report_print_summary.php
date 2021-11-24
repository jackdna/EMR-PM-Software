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
$fac_qry	=	" and st.iasc_facility_id='$_SESSION[iasc_facility_id]' ";
$fac_con	=	($_SESSION['iasc_facility_id']	 ?	$fac_qry	 :	'' )	; 

$user_name="";
if($loginUser){
	$qry_user="SELECT fname,mname,lname from users where usersId='".$loginUser."'";
	$res_user=imw_query($qry_user);
	$row_user=imw_fetch_assoc($res_user);
	$user_name=trim($row_user['lname'].", ".$row_user['fname']." ".$row_user['mname']);
}
//include("common/linkfile.php");
 //set surgerycenter detail 
$queryFac=imw_query("select * from facility_tbl where fac_id='$_SESSION[facility]'")or die(imw_error());
$dataFac=imw_fetch_object($queryFac);
$name=stripslashes($dataFac->fac_name);
$address=stripslashes($dataFac->fac_address1).' '.stripslashes($dataFac->fac_address2).' '.stripslashes($dataFac->fac_city).' '.stripslashes($dataFac->fac_state);
			
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

$selected_date	= $_REQUEST['startdate'];
$selected_date2	= $_REQUEST['enddate'];

$current_date=date("m-d-Y");
$img_logo = showThumbImages('new_html2pdf/white.jpg',170,50);
$imgheight= $img_logo[2]+8;
$imgwidth= $img_logo[1]+200;	

// Users Data
$userGrpQry = "SELECT u.usersId,u.fname, u.mname, u.lname 
								FROM patientconfirmation pc
								INNER JOIN users u ON (u.usersId=pc.surgeonId)
								WHERE pc.patientConfirmationId in(".$patientConfirmationIdSet.") AND pc.surgeonId!='' 
								GROUP BY pc.surgeonId 
								ORDER BY pc.surgery_time";
$userGrpRes = imw_query($userGrpQry) or die($userGrpQry.imw_error());
$t=0;
$table='';
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
// Start Collecting IOL Summary Report Data
$query = "SELECT opr.manufacture, opr.lensBrand, opr.model,pc.surgeonId FROM patientconfirmation pc  
					INNER JOIN stub_tbl st On pc.patientConfirmationId = st.patient_confirmation_id
					INNER JOIN patient_data_tbl pdt ON (pdt.patient_id=pc.patientId)
					INNER JOIN operatingroomrecords opr ON (opr.confirmation_id=pc.patientConfirmationId)
					WHERE pc.surgeonId IN (".$users.") AND pc.patientConfirmationId in(".$patientConfirmationIdSet.")  
					".$fac_con."
					ORDER BY opr.lensBrand, pc.surgery_time";
$sql = imw_query($query) or die('Error found @ Line No. '.(__LINE__).': '.imw_error());				
$cnt = imw_num_rows($sql);
$iol_data = array();
if( $cnt )
{
	while( $row = imw_fetch_object($sql))
	{
		$model = preg_replace("/[\n\r]/",",",$row->model);
		$manufacture = $row->manufacture;
		//$lensBrand = $row->lensBrand;
		
		if( !array_key_exists($row->surgeonId,$iol_data)) $iol_data[$row->surgeonId] = array();
		
		if( $model )
		{
			$modelArr = array_filter(explode(",",$model));
			foreach($modelArr as $mName)
			{
				if( $manufacture || $mName )	
					$iol_data[$row->surgeonId][$manufacture][$mName] += 1; 	
			}
		}
		else
		{
			if( $manufacture || $model )	
					$iol_data[$row->surgeonId][$manufacture][$model] += 1;
		}
						
	}
}
//echo '<pre>';print_r($iol_data) ; echo '</pre>';exit;					

// Start Printing Data
	$headStyle='style="border-bottom:solid 1px #333; background-color:#CCC;"';
	$rowStyle='style="border-bottom:solid 1px #333; "';
	$page_header = '
		<page_header>
			<table width="100%" border="0" cellpadding="0" cellspacing="0" >
				<tr height="'.$higinc.'" >
					<td  class="text_16b" style="background-color:#cd532f; padding-left:5px;width:40%;color:#fff;"  align="left"   valign="middle" ><b>'.$name.'<br>'.$address.'</b></td>
					<td class="text_16b" style="width:35%;text-align:left;background-color:#cd532f;color:#fff;">IOL Report<br>'.date("m-d-Y").' by '.$user_name.'</td>
					<td style="background-color:#cd532f;width:25%;" align="right" height="'.$imgheight.'">'.$img_logo[0].'&nbsp;</td>
				</tr>
				<tr height="22" bgcolor="#F1F4F0">
					<td align="right" colspan="3" class="text_16b">Surgery Center IOL Summary Report</td>
				</tr>
			</table>
		</page_header>';
						
		$thead = '
			<thead>
				<tr height="35"><td colspan="4" '.$headStyle.'><b>Dr. {SURGEON_NAME}</b></td></tr>
				<tr valign="middle">
					<th align="left" width="60" '.$headStyle.' >S.No</th>
					<th align="left" width="270" '.$headStyle.'>IOL Manufacturer</th>
					<th align="left" width="200" '.$headStyle.'>IOL Model</th>
					<th align="left" width="200" '.$headStyle.'>Total</th>
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
	
	$provider=array();$table="";
	$recordCounter	=	0;

	$total = 0;
	foreach($surgeonIdArr as $surgeonId) {
		
		$sur_name = $surgNameArr[$surgeonId];
		$sur_data = $iol_data[$surgeonId];		
			
		if( array_key_exists($surgeonId,$iol_data) && count($sur_data) > 0 )
		{
			$counter = 0;
			$csv_content 			.= "Dr. ".$sur_name.','."".','."".','."\n";
			$csv_content 			.= '"S.No"'.','.'"IOL Manufacturer"'.','.'"Model"'.','.'"Total"'."\n";
			
			$table .= '<page backtop="25mm" backbottom="15mm">'.$page_footer.$page_header;
			$table .= '<table width="100%" border="0" cellpadding="2" cellspacing="0">';	
			$table .= str_replace('{SURGEON_NAME}',$sur_name,$thead) . '<tbody>';
			foreach($sur_data as $mfg => $tmpModel)
			{
				foreach( $tmpModel as $mdl => $count)
				{
					
						$counter++; $total++;
						$table .='
							<tr valign="top">
								<td '.$rowStyle.' >'.$counter.'</td>
								<td '.$rowStyle.' >'.htmlentities($mfg).'</td>
								<td '.$rowStyle.' >'.htmlentities($mdl).'</td>
								<td '.$rowStyle.' >'.$count.'</td>
							</tr>
							';
						$csv_content .= $counter . ',' .$mfg . ',' . $mdl . ',' . $count."\n";	
					
				}
			}
			
			$table .= '</tbody></table>';	
			$table .= "</page>";
		}
		
	}
	
	$csv_content1 = $name.','."".','."".',Surgery Center IOL Summary Report '.date("m-d-Y");
	$csv_content2 = $address.','."".','."".','."";
	//echo $csv_content;exit;

	if($_REQUEST['hidd_report_format']=='csv' && imw_num_rows($userGrpRes)>0) {
		$file_name=$_SERVER['DOCUMENT_ROOT'].'/'.$surgeryCenterDirectoryName.'/admin/pdfFiles/iol_report_print_summary.csv';
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
		fclose($fileOpen);
	}
		
?>	
<!DOCTYPE html>
<html>
<head>
<title>IOL Report Summary</title>
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
</html>