<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
session_start();
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
$patientConfirmationIdSet = $_REQUEST['patientConfirmationId'];
$patientConfirmationIdSet = substr($patientConfirmationIdSet,0,(strlen($patientConfirmationIdSet)-1));
$patientConfirmationIdSingle = explode(",", $patientConfirmationIdSet);
$selected_date	= $_REQUEST['startdate'];
$selected_date2	= $_REQUEST['enddate'];


$current_date=date("m-d-Y");
$img_logo = showThumbImages('new_html2pdf/white.jpg',170,50);
$imgheight= $img_logo[2]+8;
$imgwidth= $img_logo[1]+200;	

//$userGrpQry = "SELECT surgeonId FROM patientconfirmation WHERE patientConfirmationId in(".$patientConfirmationIdSet.") GROUP BY surgeonId ORDER BY surgery_time";
$userGrpQry = "SELECT u.usersId,u.fname, u.mname, u.lname 
				FROM patientconfirmation pc
				INNER JOIN users u ON (u.usersId=pc.surgeonId)
				WHERE pc.patientConfirmationId in(".$patientConfirmationIdSet.") AND pc.surgeonId!='' 
				GROUP BY pc.surgeonId 
				ORDER BY pc.surgery_time
				";
$userGrpRes = imw_query($userGrpQry) or die($userGrpQry.imw_error());
$t=0;
$table='';
$surgeonIdArr = $surgNameArr = array();
if(imw_num_rows($userGrpRes)>0) {
	while($userGrpRow 		= imw_fetch_array($userGrpRes)) {
		$uId				= $userGrpRow['usersId'];
		$surgeonIdArr[$uId] = $userGrpRow['usersId'];
		$surgNameArr[$uId] 	= $userGrpRow['fname'].' '.$userGrpRow['mname'].' '.$userGrpRow['lname'];	
	}
	$borderBottomFirstRow = $borderBottomSecondRow = 'bottomBorder';
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
	
	foreach($surgeonIdArr as $surgeonId) {
		$t++;
		$sur_name = $surgNameArr[$surgeonId];
		$selQry = "SELECT pdt.patient_fname, pdt.patient_mname, pdt.patient_lname, 
					opr.manufacture, opr.model, opr.lensBrand, opr.Diopter,
					opr.HealonList, opr.OccucoatList, opr.ProviscList, opr.MiostatList,
					opr.HealonGVList, opr.DiscoviscList, opr.AmviscPlusList, opr.TrypanBlue,
					opr.Healon5List, opr.ViscoatList, opr.MiocholList, opr.percent_txt, opr.percent
					FROM patientconfirmation pc  
					INNER JOIN patient_data_tbl pdt ON (pdt.patient_id=pc.patientId)
					INNER JOIN operatingroomrecords opr ON (opr.confirmation_id=pc.patientConfirmationId)
					WHERE pc.surgeonId='".$surgeonId."' AND pc.patientConfirmationId in(".$patientConfirmationIdSet.") 
					ORDER BY pc.surgery_time";
	
		$selRes = imw_query($selQry) or die($selQry.imw_error());
		if(imw_num_rows($selRes)>0) {
			$table.='
				<page_header>
				<table width="100%" border="0" cellpadding="0" cellspacing="0" >
					<tr height="'.$higinc.'" >
						<td  class="text_16b" width="750" style="background-color:#BCD2B0; padding-left:5px; "  align="left"   valign="middle" ><b>'.$name.'<br>'.$address.'</b>
						 </td>
						<td style="background-color:#BCD2B0; "  align="right" height="'.$imgheight.'" width="'.$imgwidth.'">'.$img_logo[0].'</td>
					 </tr>
				
					<tr height="22" bgcolor="#F1F4F0">
						<td align="right" colspan="2" class="text_16b">Surgery Center IOL Report&nbsp;-&nbsp;'.$showDate.'</td>
						
					</tr>
					<tr height="30">
						<td colspan="2" class="text_18"><b>Dr. '.$sur_name.'</b></td>
					</tr>
					<tr >
						<td colspan="2">&nbsp;</td>
					</tr>				
				</table>
				<table width="100%" border="0" cellpadding="0" cellspacing="0">		
					<tr  valign="top">
						<td align="left"   class="text_b" width="60">Seq</td>
						<td align="left"   class="text_b" width="200">Patient Name</td>
						<td align="left"   class="text_b" width="200">IOL Manufacturer</td>
						<td align="left"   class="text_b" width="190">Model</td>
						<td align="left"   class="text_b" width="130">Lens Brand</td>
						<td align="left"   class="text_b" width="220">Diopter</td>
					</tr>
				</table>
				</page_header>
				<table width="100%" border="0" cellpadding="0" cellspacing="0">';	
			$sq=0;
			while($selRows 	= imw_fetch_array($selRes)) {
				$selRow[] = $selRows;
			}//print'<pre>';print_r($selRow);die;
			for($k=0; $k<count($selRow);$k++) {
				$sq++;
				$patientFname 	= $selRow[$k]['patient_fname'];
				$patientMname 	= $selRow[$k]['patient_mname'];
				$patientLname 	= $selRow[$k]['patient_lname'];
				$patient_name  	= $patientLname.", ".$patientFname;
				$manufacture 	= $selRow[$k]['manufacture'];
				$model 			= $selRow[$k]['model'];
				$lensBrand 		= $selRow[$k]['lensBrand'];
				$Diopter 		= $selRow[$k]['Diopter'];
				
				$table.='
					<tr valign="top">
						<td class="'.$borderBottomFirstRow.' text_15" width="60">'.$sq.'</td>
						<td class="'.$borderBottomFirstRow.' text_15" align="left" width="200">'.$patient_name.'</td>					
						<td class="'.$borderBottomFirstRow.' text_15" align="left" width="200">'.$manufacture.'</td>
						<td class="'.$borderBottomFirstRow.' text_15" align="left" width="190">'.$model.'</td>
						<td class="'.$borderBottomFirstRow.' text_15" align="left" width="130">'.$lensBrand.'</td>
						<td class="'.$borderBottomFirstRow.' text_15" align="left" width="220">'.$Diopter.'</td>
					</tr>
				';
			
			
			}
			$table .= '</table>
			<table width="100%" border="0" cellpadding="0" cellspacing="0">		
				<tr  valign="top">
					<td align="left"   class="text_b" width="70">Seq</td>
					<td align="left"   class="text_b" width="70">Patient Name</td>
					<td align="left"   class="text_b" width="70">Healon</td>
					<td align="left"   class="text_b" width="70">Occucoat</td>
					<td align="left"   class="text_b" width="70">Provisc</td>
					<td align="left"   class="text_b" width="70">Miostat</td>
					<td align="left"   class="text_b" width="70">HealonGV</td>
					<td align="left"   class="text_b" width="70">Duovisc</td>
					<td align="left"   class="text_b" width="70">Amvisc Plus</td>
					<td align="left"   class="text_b" width="70">Trypan Blue</td>
					<td align="left"   class="text_b" width="70">Healon5</td>
					<td align="left"   class="text_b" width="70">Viscoat</td>
					<td align="left"   class="text_b" width="70">Miochol</td>
					<td align="left"   class="text_b" width="70">Xylocaine MPF 1%</td>
					<td align="left"   class="text_b" width="70">Other</td>
				</tr>
			</table>
			<table width="100%" border="0" cellpadding="0" cellspacing="0">';
			$sq=0;
			for($k=0; $k<count($selRow);$k++) {
				$sq++;
				$patientFname 	= $selRow[$k]['patient_fname'];
				$patientMname 	= $selRow[$k]['patient_mname'];
				$patientLname 	= $selRow[$k]['patient_lname'];
				$patient_name  	= $patientLname.", ".$patientFname;
				$HealonList 	= $selRow[$k]['HealonList'];
				$OccucoatList 	= $selRow[$k]['OccucoatList'];
				$ProviscList 	= $selRow[$k]['ProviscList'];
				$MiostatList 	= $selRow[$k]['MiostatList'];
				$HealonGVList 	= $selRow[$k]['HealonGVList'];
				$DiscoviscList 	= $selRow[$k]['DiscoviscList'];
				$AmviscPlusList = $selRow[$k]['AmviscPlusList'];
				$TrypanBlue 	= $selRow[$k]['TrypanBlue'];
				$Healon5List 	= $selRow[$k]['Healon5List'];
				$ViscoatList 	= $selRow[$k]['ViscoatList'];
				$MiocholList 	= $selRow[$k]['MiocholList'];
				$percent_txt 	= $selRow[$k]['percent_txt'];
				$percent 		= $selRow[$k]['percent'];
				
				
				$table.='
					<tr valign="top">
						<td class="'.$borderBottomFirstRow.' text_15" width="70">'.$sq.'</td>
						<td class="'.$borderBottomFirstRow.' text_15" align="left" width="70">'.$patient_name.'</td>					
						<td class="'.$borderBottomFirstRow.' text_15" align="left" width="70">'.$HealonList.'</td>					
						<td class="'.$borderBottomFirstRow.' text_15" align="left" width="70">'.$OccucoatList.'</td>					
						<td class="'.$borderBottomFirstRow.' text_15" align="left" width="70">'.$ProviscList.'</td>					
						<td class="'.$borderBottomFirstRow.' text_15" align="left" width="70">'.$MiostatList.'</td>					
						<td class="'.$borderBottomFirstRow.' text_15" align="left" width="70">'.$HealonGVList.'</td>					
						<td class="'.$borderBottomFirstRow.' text_15" align="left" width="70">'.$DiscoviscList.'</td>					
						<td class="'.$borderBottomFirstRow.' text_15" align="left" width="70">'.$AmviscPlusList.'</td>					
						<td class="'.$borderBottomFirstRow.' text_15" align="left" width="70">'.$TrypanBlue.'</td>					
						<td class="'.$borderBottomFirstRow.' text_15" align="left" width="70">'.$Healon5List.'</td>					
						<td class="'.$borderBottomFirstRow.' text_15" align="left" width="70">'.$ViscoatList.'</td>					
						<td class="'.$borderBottomFirstRow.' text_15" align="left" width="70">'.$MiocholList.'</td>					
						<td class="'.$borderBottomFirstRow.' text_15" align="left" width="70">'.$percent.'</td>
						<td class="'.$borderBottomFirstRow.' text_15" align="left" width="70">'.$percent_txt.'</td>					
					</tr>
				';
			}
			
			$table .= '</table></page>';
			if(count($surgeonIdArr)>$t) {
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

}
//echo $table;

$fileOpen = fopen('new_html2pdf/pdffile.html','w+');
$intBytes = fputs($fileOpen,$table);
//echo $table;die;
fclose($fileOpen);
		
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
<body style="background-color:#ECF1EA;">
<form name="printFrm" action="new_html2pdf/createPdf.php?op=l" method="post">

</form>
<?php 
if($userGrpRes>0){?>		
    <table class="table_collapse" style=" font-family:Verdana, Geneva, sans-serif; font-size:12px; background-color:#ECF1EA; height:1005;" >
        <tr>
            <td class="alignCenter valignMidddle" style="width:100%;" ><img src="images/pdf_load_img.gif"></td> 
        </tr>
    </table>

	<script type="text/javascript">
        submitfn();
    </script>
<?php 
}else {?>
<table style=" font-family:Verdana, Geneva, sans-serif; font-size:12px; background-color:#ECF1EA; width:100%; height:100%;">
	<tr>
		<td class="alignCenter valignTop" style="width:100%;"><b>No Record Found</b></td> 
	</tr>
</table>
<?php		
}?>
</body>

