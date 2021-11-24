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

//include("common/linkfile.php");
 
 //get detail for logged in facility
$queryFac=imw_query("select * from facility_tbl where fac_id='$_SESSION[facility]'")or die(imw_error());
$dataFac=imw_fetch_object($queryFac);
$name=stripslashes($dataFac->fac_name);
$address=stripslashes($dataFac->fac_address1).' '.stripslashes($dataFac->fac_address2).' '.stripslashes($dataFac->fac_city).' '.stripslashes($dataFac->fac_state);

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
/*
$patientConfirmationIdSet = $_REQUEST['patientConfirmationId'];
$patientConfirmationIdSet = substr($patientConfirmationIdSet,0,(strlen($patientConfirmationIdSet)-1));
$patientConfirmationIdSingle = explode(",", $patientConfirmationIdSet);
*/
$patientConfirmationIdSet = $_REQUEST['patientConfirmationId'];
$patientConfirmationIdSet = array_filter(explode(",", $patientConfirmationIdSet));
$patientConfirmationIdSet = implode(',',$patientConfirmationIdSet);	

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
				td { font-family:Arial;}
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
					border-bottom-style:solid; border-bottom:1px;
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
			<page backtop="40mm" backbottom="15mm">			
			<page_footer>
				<table style="width: 100%;">
					<tr>
						<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
					</tr>
				</table>
			</page_footer>';
	$recordCounter = 0;
	
	$tempOprArray = array();
	$suppliesArrayDetail	=	array();
	$d_keys= array('patient_fname','patient_mname','patient_lname','patientConfirmationId','OtherSuppliesUsed','surgeonId');
	
	$selQry = "SELECT pdt.patient_fname, pdt.patient_mname, pdt.patient_lname, pc.patientConfirmationId, opr.OtherSuppliesUsed, pc.surgeonId,ops.* 	
							FROM patientconfirmation pc  
							INNER JOIN stub_tbl st On pc.patientConfirmationId = st.patient_confirmation_id
							INNER JOIN patient_data_tbl pdt ON (pdt.patient_id=pc.patientId)
							INNER JOIN operatingroomrecords opr ON (opr.confirmation_id=pc.patientConfirmationId)
							INNER JOIN operatingroomrecords_supplies ops ON (ops.confirmation_id=pc.patientConfirmationId)
							WHERE pc.patientConfirmationId in(".$patientConfirmationIdSet.") ".$fac_con." 
							ORDER BY pc.surgery_time";
	$selRes = imw_query($selQry) or die($selQry.imw_error());							
	$selCnt = imw_num_rows($selRes);
	while($selRow = imw_fetch_assoc($selRes))
	{
		$t_sid = $t_pid = '';
		$t_sid = $selRow['surgeonId'];
		$t_pid = $selRow['patientConfirmationId'];
		
		if($t_sid && $t_pid)
		{
			$d_arr = $s_arr = array();
			foreach($d_keys as $k) { $d_arr[$k] = $selRow[$k]; unset($selRow[$k]); }
			$s_arr = $selRow;
			
			if(!array_key_exists($t_sid,$tempOprArray)) $tempOprArray[$t_sid] = array();
			
			if(!array_key_exists($t_pid,$tempOprArray[$t_sid])) $tempOprArray[$t_sid][$t_pid] = $d_arr;
			
			$suppliesArrayDetail[$t_pid][] = $s_arr;

		}
		
	}
	/*echo '<pre>';
	print_r($tempOprArray); echo '<br>';
	print_r($suppliesArrayDetail);
	exit;*/
	
	foreach($tempOprArray as $surgeonId => $surgeonData) {
		$t++;
		$suppliesArray		=	array();
		$sur_name = $surgNameArr[$surgeonId];
		//$surgeonData = $tempOprArray[$surgeonId];
		
		$selRow = array();
		$csv_content 			.= "Dr. ".$sur_name.','."".','."".','."\n";
		$csv_content 			.= '"S.No"'.','.'"Supply Used"'.','.'"Total"'."\n";
		if(count($surgeonData) > 0 ) {
			$table.='
				<page_header>
				<table width="100%" border="0" cellpadding="0" cellspacing="0" >
					<tr >
						<td  class="text_16b color_white" width="700" style="background-color:#cd532f; padding-left:5px; "  align="left"   valign="middle" ><b>'.$name.'<br>'.$address.'</b>
						 </td>
						<td style="background-color:#cd532f; "  align="right" height="'.$imgheight.'" width="'.$imgwidth.'">'.$img_logo[0].'</td>
					 </tr>
				
					<tr height="22" bgcolor="#F1F4F0">
						<td align="right" colspan="2" class="text_16b">Surgery Center Supply Used Report Summary</td>
						
					</tr>
					<tr height="30">
						<td colspan="2" class="text_18"><b>Dr. '.$sur_name.'</b></td>
					</tr>
					<tr >
						<td colspan="2">&nbsp;</td>
					</tr>				
				</table>
				
				<table width="100%" border="0" cellpadding="0" cellspacing="0">		
					<tr  valign="top"><td align="left"   class="text_b" width="60"></td></tr>
				</table>
				
				<table width="990" border="1" cellpadding="3" cellspacing="0" style="border:collapse;">		
					<tr  valign="top" >
						<td align="left"   class="text_b " width="245">Supply Used</td>
						<td align="left"   class="text_b" width="85">Total</td>
						<td align="left"   class="text_b" width="245">Supply Used</td>
						<td align="left"   class="text_b" width="85">Total</td>
						<td align="left"   class="text_b" width="245">Supply Used</td>
						<td align="left"   class="text_b" width="85">Total</td>
					</tr>
				</table>
				</page_header>
				';	
			$sq=0;
			foreach($surgeonData as $pConfId => $selRows)
			{
				$recordCounter++;
				$suppliesData = $suppliesArrayDetail[$pConfId];
				
				foreach($suppliesData as $m)
				{
					if(!array_key_exists($m['suppName'],$suppliesArray))
						$suppliesArray[$m['suppName']]		=	0;
					
					if($m['suppQtyDisplay'])
					{
						$tempQty	=	0;
						$tempQty	=	(int) str_ireplace('X','',$m['suppList']);
						$suppliesArray[$m['suppName']]		=	$suppliesArray[$m['suppName']] + $tempQty;		
					}
					else
					{
						if($m['suppChkStatus'])
							$suppliesArray[$m['suppName']]		=	$suppliesArray[$m['suppName']]	 + 1;			
					}
				}
				
			}
			
				
			if(is_array($suppliesArray) && count($suppliesArray) > 0 )
			{
				$totalRows	=	floor(count($suppliesArray)/3);
				$table.='<table width="990" border="1" cellpadding="3" cellspacing="0" style="border:collapse; margin-top:-8px;">';
				$table.='<tr  valign="top">';
				$innCounter	=1;
				foreach($suppliesArray as $supplyName=>$quantity)
				{
					$css	=	(floor($innCounter/3) < $totalRows	?	'bottomBorder'	:	'') ;
					$table.='<td align="left" class="'.$css.'"    width="245">'.$supplyName.'</td>';
					$table.='<td align="left" class="text_b '.$css .'" width="85">'.$quantity.'</td>';
					if($innCounter %3 == 0 && $innCounter < count($suppliesArray) )	
					{
						$table.='</tr><tr  valign="top">';	
					}
					//START CSV CODE
					$seq_csv 				=	'"'.trim($innCounter).'"';
					$supplyName_csv 		=	'"'.trim($supplyName).'"';
					$quantity_csv 			=	'"'.trim($quantity).'"';
					
					$csv_content 			.= $seq_csv.','.$supplyName_csv.','.$quantity_csv."\n";		
					//END CSV CODE
					
					$innCounter++;
					
				}
				$innCounter--;
				if($innCounter%3 == 2)
				{
					$table.='<td align="left" width="245">&nbsp;</td>';
					$table.='<td align="left" width="85">&nbsp;</td>';
					
				}
				elseif($innCounter%3 == 1 )
				{
					$table.='<td align="left" width="245">&nbsp;</td>';
					$table.='<td align="left" width="85">&nbsp;</td>';
					$table.='<td align="left" width="245">&nbsp;</td>';
					$table.='<td align="left" width="85">&nbsp;</td>';
				}
				
				$table.='</tr>';
				$table.='</table>';
					
				
			}
			
			$table .= '</page>';
			
			
			if(count($tempOprArray) >$t) {
				$table .= '<page backtop="40mm" backbottom="15mm">
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
	$csv_content1 = $name.','."".','."".',Surgery Center Supply Used Report Summary '.date("m-d-Y");
	$csv_content2 = $address.','."".','."".','."";
}
//echo $table;
//die;
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
<title>Supply Used Report Summary</title>
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
if($recordCounter>0){?>		
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