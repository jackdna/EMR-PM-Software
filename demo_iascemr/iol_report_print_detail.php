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
//get detail for logged in facility
if($_SESSION['iasc_facility_id'])
{
$queryFac=imw_query("select * from facility_tbl where fac_id='$_SESSION[facility]'")or die(imw_error());
$dataFac=imw_fetch_object($queryFac);
$name=stripslashes($dataFac->fac_name);
$address=stripslashes($dataFac->fac_address1).' '.stripslashes($dataFac->fac_address2).' '.stripslashes($dataFac->fac_city).' '.stripslashes($dataFac->fac_state);
}
 //set surgerycenter detail 
	
$SurgeryQry="select * from surgerycenter where surgeryCenterId=1";
$SurgeryRes= imw_query($SurgeryQry) or die($SurgeryQry.imw_error());
while($SurgeryRecord=imw_fetch_array($SurgeryRes))
{
	if($_SESSION['iasc_facility_id']) {}
	else
	{
		$name= stripslashes($SurgeryRecord['name']);
		$address= stripslashes($SurgeryRecord['address'].' '.$SurgeryRecord['city'].' '.$SurgeryRecord['state']);
	}
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
$patientConfirmationIdSet = trim($patientConfirmationIdSet);

if(substr($patientConfirmationIdSet,(strlen($patientConfirmationIdSet)-1),(strlen($patientConfirmationIdSet)))==","){
	$patientConfirmationIdSet = substr($patientConfirmationIdSet,0,(strlen($patientConfirmationIdSet)-1));
}
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
				INNER JOIN stub_tbl st On pc.patientConfirmationId = st.patient_confirmation_id
				INNER JOIN patient_data_tbl pdt ON (pdt.patient_id=pc.patientId)
				INNER JOIN operatingroomrecords opr ON (opr.confirmation_id=pc.patientConfirmationId)
				WHERE pc.patientConfirmationId in(".$patientConfirmationIdSet.") AND pc.surgeonId!='' 
				".$fac_con."
				GROUP BY pc.surgeonId 
				ORDER BY pc.surgery_time
				";
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
				.color_white{
					color:#FFFFFF;
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
			<page backtop="37mm" backbottom="15mm" orientation="landscape">			
			<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>';
	
	$recordCounter = 0; 
	foreach($surgeonIdArr as $surgeonId) {
		$t++;
		$sur_name = $surgNameArr[$surgeonId];
		$selQry = "SELECT pdt.patient_fname, pdt.patient_mname, pdt.patient_lname, 
					opr.manufacture, opr.model, opr.lensBrand, opr.Diopter,opr.iol_serial_number,date_format(pc.dos,'%m-%d-%Y') as dos, 
					vs.vision_20_40, vs.complication
					FROM patientconfirmation pc  
					INNER JOIN stub_tbl st On pc.patientConfirmationId = st.patient_confirmation_id
					INNER JOIN patient_data_tbl pdt ON (pdt.patient_id=pc.patientId)
					INNER JOIN operatingroomrecords opr ON (opr.confirmation_id=pc.patientConfirmationId)
					LEFT JOIN vision_success vs ON (vs.confirmation_id = pc.patientConfirmationId)
					WHERE pc.surgeonId='".$surgeonId."' AND pc.patientConfirmationId in(".$patientConfirmationIdSet.") 
					".$fac_con." 
					ORDER BY pc.surgery_time";
	
		$selRes = imw_query($selQry) or die($selQry.imw_error());
		$selRow = array();
		
		$selSummaryQry = "SELECT opr.manufacture, opr.lensBrand, opr.model,pc.surgeonId FROM patientconfirmation pc  
					INNER JOIN stub_tbl st On pc.patientConfirmationId = st.patient_confirmation_id
					INNER JOIN patient_data_tbl pdt ON (pdt.patient_id=pc.patientId)
					INNER JOIN operatingroomrecords opr ON (opr.confirmation_id=pc.patientConfirmationId)
					WHERE pc.surgeonId IN (".$surgeonId.") AND pc.patientConfirmationId in(".$patientConfirmationIdSet.")  
					".$fac_con."
					ORDER BY opr.lensBrand, pc.surgery_time";
	
		$selSummaryRes = imw_query($selSummaryQry) or die($selSummaryQry.imw_error());
		$selSummaryRow = array();		
		$csv_content 			.= "\n".','."".','."".',Surgery Center IOL Detail Report '.date("m-d-Y")."\n";		
		$csv_content 			.= "Dr. ".$sur_name.','."".','."".','."\n";
		$csv_content 			.= '"S.No"'.','.'"Patient Name"'.','.'"DOS"'.','.'"IOL Manufacturer"'.','.'"Model"'.','.'"Lens Brand"'.','.'"Diopter"'.','.'"S/N"'.','.'"Vision Status"'.','.'"Complication"'."\n";
		
		if(imw_num_rows($selRes)>0) {
			$table.='
				<page_header>
				<table width="100%" border="0" cellpadding="0" cellspacing="0" >
					<tr height="'.$higinc.'" >
						<td  class="text_16b color_white" style="background-color:#cd532f; padding-left:5px; width:33%;"  align="left" valign="middle"><b>-'.$name.'<br>'.$address.'</b>
						</td>
						 <td class="text_16b color_white" style="width:34%; text-align:center; background-color:#cd532f;">IOL Report<br>'.date("m-d-Y").' by '.$user_name.'</td>
						<td style="background-color:#cd532f;width:33%;"  align="right" height="'.$imgheight.'" width="'.$imgwidth.'">'.$img_logo[0].'</td>
					 </tr>
				
					<tr height="22" bgcolor="#F1F4F0">
						<td align="right" colspan="3" class="text_16b">Surgery Center IOL Detail Report</td>
						
					</tr>
					<tr height="30">
						<td colspan="3" class="text_18"><b>Dr. '.$sur_name.'</b></td>
					</tr>
					<tr >
						<td colspan="3">&nbsp;</td>
					</tr>				
				</table>
				<table width="100%" border="0" cellpadding="0" cellspacing="0">		
					<tr  valign="top">
						<td align="left"   class="text_b" width="40">S.No</td>
						<td align="left"   class="text_b" width="120">Patient Name</td>
						<td align="left"   class="text_b" width="90">DOS</td>
						<td align="left"   class="text_b" width="180">IOL Manufac.</td>
						<td align="left"   class="text_b" width="80">Model</td>
						<td align="left"   class="text_b" width="100">Lens Brand</td>
						<td align="left"   class="text_b" width="90">Diopter</td>
						<td align="left"   class="text_b" width="100">S/N</td>
						<td align="left"   class="text_b" width="130">Vision&nbsp;Status</td>
						<td align="left"   class="text_b" width="70">Complication</td>
					</tr>
				</table>
				</page_header>
				<table width="100%" border="0" cellpadding="0" cellspacing="0">';	
			$sq=0;
			while($selRows 	= imw_fetch_array($selRes)) {
				$recordCounter++;
				
				$selRow[] = $selRows;
			}//print'<pre>';print_r($selRow);die;
			for($k=0; $k<count($selRow);$k++) {
				$sq++;
				$patientFname 	= $selRow[$k]['patient_fname'];
				$patientMname 	= $selRow[$k]['patient_mname'];
				$patientLname 	= $selRow[$k]['patient_lname'];
				$patient_name  	= $patientLname.", ".$patientFname;
				$manufacture 	= $selRow[$k]['manufacture'];
				$model = preg_replace("/[\n\r]/",",",$selRow[$k]['model']);
				$model = implode(",\n",array_filter(explode(",",$model)));
				$lensBrand 		= $selRow[$k]['lensBrand'];
				$Diopter 		= $selRow[$k]['Diopter'];
				$iol_serial_number= $selRow[$k]['iol_serial_number'];
				$date_of_service= $selRow[$k]['dos'];
				$vision_20_40	= $selRow[$k]['vision_20_40'];
				$complication	= $selRow[$k]['complication'];
				
				$vis_better_worse = "";
				if($vision_20_40 == "Yes") {
					$vis_better_worse = "20/40 Better";
				}else if($vision_20_40 == "No") {
					$vis_better_worse = "Worse";
				}
				
				$table.='
					<tr valign="top">
						<td class="'.$borderBottomFirstRow.' text_15" width="40">'.$sq.'</td>
						<td class="'.$borderBottomFirstRow.' text_15" align="left" width="120">'.$patient_name.'</td>					
						<td class="'.$borderBottomFirstRow.' text_15" align="left" width="90">'.$date_of_service.'</td>
						<td class="'.$borderBottomFirstRow.' text_15" align="left" width="180">'.$manufacture.'</td>
						<td class="'.$borderBottomFirstRow.' text_15" align="left" width="80">'.$model.'</td>
						<td class="'.$borderBottomFirstRow.' text_15" align="left" width="100">'.$lensBrand.'</td>
						<td class="'.$borderBottomFirstRow.' text_15" align="left" width="90">'.$Diopter.'</td>
						<td class="'.$borderBottomFirstRow.' text_15" align="left" width="100">'.$iol_serial_number.'</td>
						<td class="'.$borderBottomFirstRow.' text_15" align="left" width="130">'.$vis_better_worse.'</td>
						<td class="'.$borderBottomFirstRow.' text_15" align="center" width="70">'.$complication.'</td>
						
					</tr>
				';
				
				//START CSV CODE
				$seq_csv 				=	'"'.trim($sq).'"';
				$patient_name_csv 		=	'"'.trim($patient_name).'"';
				$date_of_service_csv 	=	'"'.trim($date_of_service).'"';
				$manufacture_csv		=	'"'.trim($manufacture).'"';
				$model_csv				=	'"'.trim($model).'"';
				$lensBrand_csv			=	'"'.trim($lensBrand).'"';
				$Diopter_csv			=	'"'.trim($Diopter).'"';
				$iol_serial_number		=	'"'.trim($iol_serial_number).'"';
				$vis_better_worse		=	'"'.trim($vis_better_worse).'"';
				$complication			=	'"'.trim($complication).'"';
				
				$csv_content 			.= $seq_csv.','.$patient_name_csv.','.$date_of_service_csv.','.$manufacture_csv.','.$model_csv.','.$lensBrand_csv.','.$Diopter_csv.','.$iol_serial_number.','.$vis_better_worse.','.$complication."\n";		
				
				//END CSV CODE
			}
			$table .= '</table>';
			if(imw_num_rows($selSummaryRes)>0) {
				$table .= '</page><page orientation="portrait" >
							<page_footer>
								<table style="width: 100%;">
									<tr>
										<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
									</tr>
								</table>
							</page_footer>
							<table width="100%" border="0" cellpadding="0" cellspacing="0" >
								<tr height="'.$higinc.'" >
									<td  class="text_16b color_white" style="background-color:#cd532f; padding-left:5px;width:30%;" align="left" valign="middle"><b>'.$name.'<br>'.$address.'</b>
									 </td>
									  <td class="text_16b color_white" style="width:34%;text-align:left;background-color:#cd532f;">IOL Report<br>'.date("m-d-Y").' by '.$user_name.'</td>
									<td style="background-color:#cd532f; width:30%;"  align="right" height="'.$imgheight.'" width="'.$imgwidth.'">'.$img_logo[0].'</td>
								 </tr>
							
								<tr height="22" bgcolor="#F1F4F0">
									<td align="right" colspan="3" class="text_16b">Surgery Center IOL Summary Report</td>
									
								</tr>
								<tr height="30">
									<td colspan="3" class="text_18"><b>Dr. '.$sur_name.'</b></td>
								</tr>
								<tr >
									<td colspan="3">&nbsp;</td>
								</tr>				
							</table>
							';
					$sq=0;
					$csv_content 			.= "\n".','."".','."".',Surgery Center IOL Summary Report '.date("m-d-Y")."\n";		
					$csv_content 			.= "Dr. ".$sur_name.','."".','."".','."\n";
					$csv_content 			.= '"S.No"'.','.'"IOL Manufacturer"'.','.'"Model"'.','.'"Total"'."\n";
					
					$selSummaryRow = array();			
					while($selSummaryRows 	= imw_fetch_object($selSummaryRes)) {
						
						$model = preg_replace("/[\n\r]/",",",$selSummaryRows->model);
						$manufacture = $selSummaryRows->manufacture;
						
						if( $model )
						{
							$modelArr = array_filter(explode(",",$model));
							foreach($modelArr as $mName)
							{
								if( $mName )	$selSummaryRow[$manufacture][$mName] += 1; 	
							}
						}
					}
					
					$table .= '<table width="100%" border="0" cellpadding="0" cellspacing="0">		
								 <tr valign="top">
									<td colspan="6"  class="'.$borderBottomFirstRow.' text_16b" align="center"  >Summary</td>
								 </tr>
								 <tr valign="top">
									<td align="left" class="text_b" width="60">S.No</td>
									<td align="left" class="text_b" width="200">IOL Manufacturer</td>
									<td align="left" class="text_b" width="130">Model</td>
									<td align="left" class="text_b" width="130">Total</td>
								 </tr>';
					foreach( $selSummaryRow as $manufacture => $modelArr) {
						foreach($modelArr as $model => $count)
						{
							$sq++;
							$table .= '<tr valign="top">
													<td class="'.$borderBottomFirstRow.' text_15" width="60">'.$sq.'</td>
													<td class="'.$borderBottomFirstRow.' text_15" align="left" width="200">'.$manufacture.'</td>
													<td class="'.$borderBottomFirstRow.' text_15" align="left" width="130">'.$model.'</td>
													<td class="'.$borderBottomFirstRow.' text_15" align="left" width="130">'.$count.'</td>
												</tr>';
							//START CSV CODE
							$seq_csv 				=	'"'.trim($sq).'"';
							$manufacture_csv 		=	'"'.trim($manufacture).'"';
							$model_csv 			=	'"'.trim($model).'"';
							$total_csv			=	'"'.trim($count).'"';
						
							$csv_content 			.= $seq_csv.','.$manufacture_csv.','.$model_csv.','.$total_csv."\n";
							//END CSV CODE
						}
					}
					$table .= '</table>';
			}
			
			if(count($surgeonIdArr)>$t) {
				$table .= '</page><page backtop="37mm" backbottom="15mm" orientation="landscape">
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
	$csv_content1 = $name.','."".','."".','."";
	$csv_content2 = $address.','."".','."".','."";
	$table.='</page>';
}
//echo $table;

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
	//echo $table;die;
	fclose($fileOpen);
}

		
?>	
<!DOCTYPE html>
<html>
<head>
<title>IOL Report Detail</title>
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
if($recordCounter > 0){?>
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

