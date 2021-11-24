<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
session_start();
set_time_limit(0);
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
	
	unset($condArr);
	$condArr['1']='1';
	$supplies	=	$objManageData->getMultiChkArrayRecords('predefine_suppliesused',$condArr,'name','Asc ', 'And deleted=0');
	$suppliesInHeader	=	(count($supplies)	> 8)	?	8 	:	count($supplies);
	
	$columnsExceed = true;
	if(is_array($supplies)  && count($supplies) > 0 && count($supplies) <= $suppliesInHeader)
	{
		$columnsExceed = false;				
	} 
	$rowWidth = round(810/($suppliesInHeader) );
				
				
	$borderBottomFirstRow = $borderBottomSecondRow = 'bottomBorder';
	$table.='
			<style>
				.BdrAll { 
					border:solid 1px #999; 
					padding-top:1px; padding-bottom:1px; padding-left:1px; font-family:Arial, Helvetica, sans-serif;	
				}
				.BdrTBR { 
					border:solid 1px #999; border-left: solid 0px #fff;
					padding-top:1px; padding-bottom:1px; padding-left:1px; font-family:Arial, Helvetica, sans-serif;
				}
				.BdrLBR { 
					border:solid 1px #999; border-top: solid 0px #fff;
					padding-top:1px; padding-bottom:1px; padding-left:1px; font-family:Arial, Helvetica, sans-serif;
				}
				.BdrBR {
					border-bottom: solid 1px #999;
					border-right: solid 1px #999;
					padding-top:1px; padding-bottom:1px; padding-left:1px; font-family:Arial, Helvetica, sans-serif;
				}
				.BdrR {
					border-right: solid 1px #999;
					padding-top:1px; padding-bottom:1px; padding-left:1px; font-family:Arial, Helvetica, sans-serif;
				}
				.BdrB {
					border-bottom: solid 1px #999;
					padding-top:1px; padding-bottom:1px; padding-left:1px; font-family:Arial, Helvetica, sans-serif;
				}
				
				.tb_heading{ 
					font-size:12px;
					font-family:Arial, Helvetica, sans-serif;
					font-weight:bold;
					color:#000000;
					background-color:#FE8944;
				}
				.sup_cell {font-size:10px;}
				.text_b{
					font-size:16px;
					font-family:Arial, Helvetica, sans-serif;
					font-weight:bold;
					color:#000000;
				}
				.text_b10{
					font-size:12px;
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
			</style>
			<page backtop="27mm" backbottom="2mm">
				<page_footer>
					<table style="width: 100%;">
						<tr>
							<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
						</tr>
					</table>
				</page_footer>
				';
	
	$recordCounter = 0;
	
	$tempOprArray = array();
	$suppliesArray	=	array();
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
			
			$suppliesArray[$t_pid][] = $s_arr;

		}
		
	}
	/*echo '<pre>';
	print_r($tempOprArray); echo '<br>';
	print_r($suppliesArray); echo '<br>';
	print_r($surgeonIdArr);
	exit;*/
	
	foreach($tempOprArray as $surgeonId => $surgeonData ) {
		$t++;
		$sur_name = $surgNameArr[$surgeonId];
		//$surgeonData = $tempOprArray[$surgeonId];
						
		$selRow=	array();
		$csv_content 			.= "\n\nDr. ".$sur_name.','."".','."".','."\n";
		$csv_content 			.= '"S.No"'.','.'"Patient Name"';
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
							<td align="right" colspan="2" class="text_16b">Surgery Center Supply Used Report Detail</td>
							
						</tr>
						<tr height="30">
							<td colspan="2" class="text_18"><b>Dr. '.$sur_name.'</b></td>
						</tr>
				</table>
				</page_header>
				
				<table width="100%" border="0" cellpadding="0" cellspacing="0" id="bodyTable">
					<thead>
							<tr>
							<td align="left"   class="text_b BdrAll" style="width:40px;">S.No</td>
							<td align="left"   class="text_b BdrTBR " style="width:130px;">Patient Name</td>';		
							if(is_array($supplies)  && count($supplies) > 0 && count($supplies) <= $suppliesInHeader)
							{
								foreach($supplies as $key=> $supply)
								{
									$table	.='<td align="left"   class="text_b BdrTBR" style="width:'.$rowWidth.'px;">'.$supply->name.'</td>';
								}
							} 
							else
							{
								$table	.='<td align="left" class="text_b BdrTBR" style="width:880px;">&nbsp;</td>';	
							}
					$table.='
						</tr>
					</thead>';
				
				$sq=0;
				$suppliesDetailToPrint	=	array();
				$totalArray	=	array();
				foreach($surgeonData as $pConfId => $selRows)
				{
					$recordCounter++;
					$dataArray=	array();
					$dataArray['patientName']	=	$selRows['patient_lname'].", ".$selRows['patient_fname'];
					$suppliesData = $suppliesArray[$pConfId];
					
					foreach($suppliesData as $m)
					{
						$dataArray[$m['suppName']]	=		$m;
					}
					array_push($suppliesDetailToPrint,$dataArray);
					
				}
				//echo '<pre>';print_r($suppliesDetailToPrint);
				
				$totalSupplies	=	array();
				$lastRowStart	=	(ceil(count($supplies)/$suppliesInHeader) -1 ) * $suppliesInHeader;
				
				if(is_array($suppliesDetailToPrint) && count($suppliesDetailToPrint) > 0)
				{
					$innCounter =0 ;
					foreach($suppliesDetailToPrint as $data)
					{
						$patientSupplies	=	'';
						$supp_list_csv 		= '';
						$counter	=	$mainCounter =	0;
						if($columnsExceed)
						{
							$patientSupplies	.=	'<td align="left" class="BdrBR" style="width:880px;padding:0px;" valign="top">
																<table width="100%" cellpadding="0" cellspacing="0" >' ;
						}
						
						$com = ''; $row2 = $row1 =	($columnsExceed) ? '<tr>' : '' ;
						$pendingSupplies	=	count($supplies);//print'<pre>';print_r($supplies);die;
						foreach($supplies as $key => $supply)
						{ 	$counter++; $mainCounter++ ; $pendingSupplies-- ;
							if(!array_key_exists($supply->name,$totalSupplies))
									$totalSupplies[$supply->name]	=	0 ;
							if($columnsExceed)
							{
								$row1Class	=	'class="text_b10 BdrBR" ';
								if($counter == $suppliesInHeader ) {
									$row1Class	= 'class="text_b10 BdrB"';		
								}
							
								$row1	.=	'<td align="left" '.$row1Class.' style="width:'.$rowWidth.'px; ">'.$supply->name.'</td>';
								
								$supp_name_csv 		= '"'.trim($supply->name).'"';
								if($innCounter ==0) {
									$csv_content 	   .= ','.$supp_name_csv;
								}
							}
							
							$row2Class	=	'class="BdrBR" ' ;
							if($columnsExceed)
							{
								if($counter < $suppliesInHeader) {
									$row2Class	= ($mainCounter <= $lastRowStart)	?	'class="BdrBR"' : 'class="BdrR"' ;	
								} else if($counter == $suppliesInHeader ) { 
									$row2Class	= ($mainCounter <= $lastRowStart)	?	'class="BdrB" ' : '' ;		
								}
							}
							
							if($data[$supply->name])
							{
								if($data[$supply->name]['suppQtyDisplay'])
								{ 
									$row2	.=	'<td align="left" '.$row2Class.'  style="width:'.$rowWidth.'px;">'.$data[$supply->name]['suppList'].'</td>';
									$tempQty	=	0;
									$tempQty	=	(int) str_ireplace('X','',$data[$supply->name]['suppList']);
									$totalSupplies[$supply->name]	=	'X'.((int) str_replace('X','',$totalSupplies[$supply->name]) + $tempQty);
									
									$supp_list_csv_content		 	= '"'.trim($data[$supply->name]['suppList']).'"';
									$supp_list_csv 				   .= ','.$supp_list_csv_content;
								}
								else
								{
									if($data[$supply->name]['suppChkStatus']){
										$row2	.=	'<td align="left" '.$row2Class.' style="width:'.$rowWidth.'px;">Yes</td>';
										$totalSupplies[$supply->name]=	((int) str_replace('X','',$totalSupplies[$supply->name]) + 1);
										$supp_list_csv_content 		 = '"Yes"';
										$supp_list_csv 				.= ','.$supp_list_csv_content;
									
									}
									else
									{
										$row2	.=	'<td align="left" '.$row2Class.' style="width:'.$rowWidth.'px;">&nbsp;</td>';	
										$supp_list_csv 				.= ','.'""';
									
									}
								}
								
							}
							else
							{
								$row2	.=	'<td align="left" '.$row2Class.' style="width:'.$rowWidth.'px;">&nbsp;</td>';
								$supp_list_csv 				.= ','.'""';	
							}
							if($counter == $suppliesInHeader && $pendingSupplies > 0)
							{
								$com .= $row1.($columnsExceed ? '</tr>' : '').$row2.($columnsExceed ? '</tr>' : '');
								$row1 = ($columnsExceed) ? '<tr>' : '' ;
								$row2 = ($columnsExceed) ? '<tr>' : '' ;
								$counter=	0	;	
							}
						}
						
						
						if($counter <= $suppliesInHeader )
						{
								for($loop = $counter; $loop < ($suppliesInHeader); $loop++)
								{
									$row1 .=	($columnsExceed) ? '<td align="left" '.($loop == ($suppliesInHeader-1) ? 'class="BdrB"' : 'class="BdrBR"' ) .'   style="width:'.$rowWidth.'px;">&nbsp;</td>'		:	''	;
									$row2 .=	'<td align="left" '.($loop == ($suppliesInHeader-1) ? '' : 'class="BdrR"' ) .' style="width:'.$rowWidth.'px;" >&nbsp;</td>';	
								}
						}
						
						$com .= $row1.($columnsExceed ? '</tr>' : '').$row2.($columnsExceed ? '</tr>' : '');
								
						$patientSupplies	.=	$com;		
						if($columnsExceed)
						{
							$patientSupplies	.=	'</table></td>' ;
						}
						
						
						$table.= '<tr>';
						$table.= '<td align="center" class=" BdrLBR" style="width:40px; " >'.(++$innCounter).'</td>';
						$table.= '<td align="left" class=" BdrBR" style="width:130px;">'.$data['patientName'].'</td>';
						$table.= $patientSupplies;
						$table.= '</tr>';
						
						$patient_name_csv 	= '"'.trim($data['patientName']).'"';
						$csv_content 	   .= "\n";
						$csv_content 	   .= $innCounter.','.$patient_name_csv.$supp_list_csv;
						
						
					}
				}
				//die($csv_content);
				
				
				// Start Printing Total 
				
				$innCounter =0 ;
				
				$patientSupplies	=	'';
				$counter	=	$mainCounter =	0;
				if($columnsExceed)
				{
					$patientSupplies	.=	'<td align="left" class="BdrBR" style="width:880px;padding:0px;" valign="top">
														<table width="100%" cellpadding="0" cellspacing="0" >
													' ;
				}
				
				$com = ''; $row2 = $row1 =	($columnsExceed) ? '<tr>' : '' ;
				$pendingSupplies	=	count($supplies);
				$total_supp_list_csv = '';
				foreach($supplies as $key => $supply)
				{ 	
					
					$counter++; $mainCounter++ ; $pendingSupplies-- ;
					if($columnsExceed)
					{
						$row1Class	=	'class="text_b10 BdrBR" ';
						if($counter == $suppliesInHeader ) {
							$row1Class	= 'class="text_b10 BdrB"';		
						}
					
						$row1	.=	'<td align="left" '.$row1Class.' style="width:'.$rowWidth.'px; ">'.$supply->name.'</td>';
					}
					
					$row2Class	=	'class="BdrBR" ' ;
					if($columnsExceed)
					{
						if($counter < $suppliesInHeader) {
							$row2Class	= ($mainCounter <= $lastRowStart)	?	'class="BdrBR"' : 'class="BdrR"' ;	
						} else if($counter == $suppliesInHeader ) { 
							$row2Class	= ($mainCounter <= $lastRowStart)	?	'class="BdrB" ' : '' ;		
						}
					}
					
					$temp	=	(int) str_replace('X','',$totalSupplies[$supply->name]);
					
					$row2	.=	'<td align="left" '.$row2Class.'  style="width:'.$rowWidth.'px;">'.($temp ? $totalSupplies[$supply->name] : $temp ).'</td>';
					
					$total_supp_list_csv_content		 	= '"'.trim($temp ? $totalSupplies[$supply->name] : $temp ).'"';
					$total_supp_list_csv 				   .= ','.$total_supp_list_csv_content;
					
					if($counter == $suppliesInHeader && $pendingSupplies > 0)
					{
						$com .= $row1.($columnsExceed ? '</tr>' : '').$row2.($columnsExceed ? '</tr>' : '');
						$row1 = ($columnsExceed) ? '<tr>' : '' ;
						$row2 = ($columnsExceed) ? '<tr>' : '' ;
						$counter=	0	;	
					}
				}
				
				
				if($counter <= $suppliesInHeader )
				{
						for($loop = $counter; $loop < ($suppliesInHeader); $loop++)
						{
							$row1 .=	($columnsExceed) ? '<td align="left" '.($loop == ($suppliesInHeader-1) ? 'class="BdrB"' : 'class="BdrBR"' ) .'   style="width:'.$rowWidth.'px;">&nbsp;</td>'		:	''	;
							$row2 .=	'<td align="left" '.($loop == ($suppliesInHeader-1) ? '' : 'class="BdrR"' ) .' style="width:'.$rowWidth.'px;" >&nbsp;</td>';	
							//$total_supp_list_csv 			.= ','.'""';
						}
				}
				
				$com .= $row1.($columnsExceed ? '</tr>' : '').$row2.($columnsExceed ? '</tr>' : '');
						
				$patientSupplies	.=	$com;		
				if($columnsExceed)
				{
					$patientSupplies	.=	'</table></td>' ;
				}
				
				
				$table.= '<tr>';
				$table.= '<td align="center" class="text_b BdrLBR" style="width:40px; " >&nbsp;</td>';
				$table.= '<td align="left" class="text_b BdrBR" style="width:130px;">Total</td>';
				$table.= $patientSupplies;
				$table.= '</tr>';
				$csv_content 	   .= "\n";
				$csv_content 	   .= '"Total"'.','.'""'.$total_supp_list_csv;						
				//End Printing Total Row 	
				
				
			$table .= '</table></page>';
			if(count($tempOprArray)>$t) { 
				$table .= '<page backtop="27mm" backbottom="2mm">
								<page_footer >
									<table style="width: 100%;">
										<tr>
											<td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
										</tr>
									</table>
								</page_footer>';
			}
			
		}//die($csv_content11);
	}

	$csv_content1 = $name.','."".',Surgery Center Supply Used Report Detail '.date("m-d-Y");
	$csv_content2 = $address.','."".','."".','."";

}
//echo $table; die;

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
<title>Supply Used Report Detail</title>
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
<body >
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