<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
session_start();
include("common/conDb.php");
if($_SESSION["loginUserId"]=="" && $_SESSION['loginUserName']=="") {
	echo '<script>top.location.href="index.php"</script>';
}
include("admin/classObjectFunction.php");
$objManageData = new manageData;
   
   $date=$_REQUEST['date12'];
   $get_http_path=$_REQUEST['get_http_path'];
   $fac_con="";
	if($_SESSION['iasc_facility_id'])
	{
		$fac_con=" and stub_tbl.iasc_facility_id='$_SESSION[iasc_facility_id]'"; 
	}	
	//get detail for logged in facility
	$queryFac=imw_query("select * from facility_tbl where fac_id='$_SESSION[facility]'")or die(imw_error());
	$dataFac=imw_fetch_object($queryFac);
	$name=$dataFac->fac_name;
	$address=$dataFac->fac_address1.' '.$dataFac->fac_address2.' '.$dataFac->fac_city.' '.$dataFac->fac_state;

	//set surgerycenter detail	
		$SurgeryQry="select * from surgerycenter where surgeryCenterId=1";
		$SurgeryRes= imw_query($SurgeryQry);
		while($SurgeryRecord=imw_fetch_array($SurgeryRes))
		{
	   // $name= stripslashes($SurgeryRecord['name']);
		//$address= stripslashes($SurgeryRecord['address'].' '.$SurgeryRecord['city'].' '.$SurgeryRecord['state']);
		$img = $SurgeryRecord['logoName'];
		$surgeryCenterLogo=$SurgeryRecord['surgeryCenterLogo'];
		}
        //$file=@fopen('html2pdf/white.jpg','w+');
		//@fputs($file,$surgeryCenterLogo);
		$bakImgResource = imagecreatefromstring($surgeryCenterLogo);
		imagejpeg($bakImgResource,'new_html2pdf/white.jpg');
		
		$size=getimagesize('new_html2pdf/white.jpg');
	    $hig=$size[1];
	    $wid=$size[0];


$filename='new_html2pdf/white.jpg';
 function showThumbImages($fileName='white.jpg',$targetWidth=500,$targetHeight=70)
{ 

if(file_exists($fileName))
{ 
$img_size=getimagesize('new_html2pdf/white.jpg');
 $width=$img_size[0];
 $height=$img_size[1];
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
// end set surgerycenter detail  
$img_logo = showThumbImages('new_html2pdf/white.jpg',170,50);
$imgheight= $img_logo[2]+8;
$imgwidth= $img_logo[1]+8;

   if($date!="") {
      $sel_date=explode("-",$date);
	  $sel_date[0];
	  $sel_date[1];
	  $sel_date[2];
	  $select_date=$sel_date[2]."-".$sel_date[0]."-".$sel_date[1];
	  $showDate = $date;
   }
   
   $current_date=date('m-d-Y');
   
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
   <page backtop=25mm" backbottom="5mm">			
	   <page_footer>
		   <table style="width: 100%;">
			   <tr>
				   <td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
			   </tr>
		   </table>
	   </page_footer>
	   <page_header>
		   <table width="750" border="0" cellpadding="3" cellspacing="0">
			   <tr height="30">
				   <td  align="left" style="background-color:#CD523F; padding-left:5px; color:white; width:420px;" valign="middle">
					   <b>'.$name.'<br>'.$address.'</b>
				   </td>
				   <td style="background-color:#CD523F;width:320px;"align="right" height="'.$imgheight.'" >'.$img_logo[0].'&nbsp;</td>
			   </tr>
			   <tr height="30" bgcolor="#F1F4F0">
				   <td align="left"><b>Discharge Summary Report</b></td>
				   <td align="right">&nbsp;'.$showDate.'</td>
			   </tr>
		   </table>	
	   </page_header>

	   <table width="700" border="1" cellpadding="3" cellspacing="0" bgcolor="#FFFFFF">
		   <thead>
			   <tr class="text_printb">
	   				<td width="20" class="text_b">Seq</td>
					<td width="90" class="text_b">Name</td>
					<td width="60" class="text_b">ASC-ID</td>
					<td width="90" class="text_b">Surgeon</td>
					<td width="170" class="text_b">Procedure</td>
					<td width="90" class="text_b">Dx-Code</td>
					<td width="75" class="text_b">Site</td>
					<td width="75" class="text_b">Sch.Time</td>
				</tr>
		   </thead>
		   {{PAGE_CONTENT}}
	   </table>
   </page>	

   ';


 	//data from dischargesummarysheet 
	//$discharge="select * from dischargesummarysheet where dischargeSummarySheetDate='$select_date'";
	$discharge="select ds.* from dischargesummarysheet as ds 
				LEFT JOIN stub_tbl ON stub_tbl.patient_confirmation_id=ds.confirmation_id
				where ds.form_status='completed'
				and stub_tbl.patient_confirmation_id !=0
				$fac_con";
            $discharge_sheet=imw_query($discharge) or die(imw_error());
			if(imw_num_rows($discharge_sheet)>0)
			{
			 while($dis=imw_fetch_array($discharge_sheet))
			 {
			  $confirmation_id[]=$dis['confirmation_id'];
			  $ascid[]=$dis['ascId'];
			  $diag_id[]=$dis['diag_ids'];
			 }
			   $asc_Id=implode(',',$ascid);
			   $diag_Id=implode(',',$diag_id);
			   $confirmationid =implode(',',$confirmation_id);
			 }
			 else
			 {
			    $msg="No record found!";
			 }
			 
			$report="SELECT patientconfirmation. * , patient_data_tbl.patient_fname, patient_data_tbl.patient_mname, patient_data_tbl.patient_lname,dischargesummarysheet.otherMiscellaneous, dischargesummarysheet.other1, dischargesummarysheet.other2,dischargesummarysheet.dischargeSummarySheetDate,dischargesummarysheet.procedures_name,dischargesummarysheet.procedures_code,dischargesummarysheet.diag_ids, diagnosis_tbl.diag_code, dischargesummarysheet.icd10_code, dischargesummarysheet.procedures_code_name
					    FROM patientconfirmation
						LEFT JOIN patient_data_tbl ON patientconfirmation.patientId  = patient_data_tbl.patient_id 
						LEFT JOIN dischargesummarysheet ON patientconfirmation.patientConfirmationId  = dischargesummarysheet.confirmation_id 
						LEFT JOIN diagnosis_tbl ON dischargesummarysheet.diag_ids = diagnosis_tbl.diag_id
						WHERE patientconfirmation.patientConfirmationId in($confirmationid)
						&& patientconfirmation.dos ='$select_date' && dischargesummarysheet.form_status='completed'
						
						group by patientconfirmation.patientConfirmationId";			 
           		
			$discharge_report=imw_query($report);
		    
            if(@imw_num_rows($discharge_report)>0){
             $a=1;
				while($report=@imw_fetch_array($discharge_report))
				{
				   foreach($report as $key=>$val)
				  {
				    
				    $report1[$a][$key]=$val;
					
				  }    
			        $a++;
				}
		       $num=imw_num_rows($discharge_report);
			   $rowbreak=10;
			   $numb=ceil($num/$rowbreak);
			   $rowstart=1;
			   $rowend = 10;
			   for($rows=0;$rows<$numb;$rows++)
			   {
			      $csv_content_val = '';
				  $tablerow='';
            	   for($i=$rowstart;$i<=$rowend;$i++)
				  {
					$asc_id=$report1[$i]['ascId'];
					$surgeon_name=$report1[$i]['surgeon_name'];
					$procedure_name=$report1[$i]['procedures_name'];
					$procedure_name_list=explode(',',$procedure_name);
					$procedure_length=count($procedure_name_list);
					 
					$procedure_code=$report1[$i]['procedures_code'];
					$procedure_code_list=array_filter(explode(',',$procedure_code));
					 
					$procNameExplode = $procCodeNameExplode = $procNameArray = $procCodeNameArray = array();
					$procedures_nameDB	=	$report1[$i]['procedures_name'];
					$procedures_codeDB	=	$report1[$i]['procedures_code_name'];
					$procNameExplode	=	array_filter(explode("!,!",$procedures_nameDB));
					$procCodeNameExplode=	array_filter(explode("##",$procedures_codeDB));
					
					if(is_array($procedure_code_list) && count($procedure_code_list) > 0)
					{
						foreach($procedure_code_list as $_key=>$_val)	
						{
							$procNameArray[$_val]		=	trim($procNameExplode[$_key]);
							$procCodeNameArray[$_val]	=	trim($procCodeNameExplode[$_key]);
						}
					}
					
					 $diag_ids=$report1[$i]['diag_ids'];
					 $diag_ids_list=explode(',',$diag_ids);
					 $diag_ids_length=count($diag_ids_list);
					 
					 $heightTd=45*$procedure_length+1;
					// $procedure_name12[1]; 
					 
					 //$procedure_code_len=count($procedure_code_list)
					 $site=$report1[$i]['site'];
					 $schd_time=$report1[$i]['surgery_time'];
					 //$schd_time=$rpt['dateConfirmation'];
					 $patient_id=$report1[$i]['patientId'];
							// APPLYING NUMBERS TO PATIENT SITE
					if($site == 1) {
						$eyesite = "Left Eye";  //OD
					}else if($site == 2) {
						$eyesite = "Right Eye";  //OS
					}else if($site == 3) {
						$eyesite = "Both Eye";  //OU
					}
					// END APPLYING NUMBERS TO PATIENT SITE
					
					//CODE FOR PATIENT NAME FROM PATIENT_DATA_TBL
						$patient_fname=$report1[$i]['patient_fname'];
						$patient_mname=$report1[$i]['patient_middle_name'];
						$patient_lname=$report1[$i]['patient_lname'];
						$patient_name=$patient_lname.", ".$patient_fname;
					//END OF CODE
					 //SURGERY TIME
					if($report1[$i]['surgery_time']=="00:00:00" || $report1[$i]['surgery_time']=="") {
						$surgery_time = "";	
					}else {
						$surgery_time= $objManageData->getTmFormat($report1[$i]['surgery_time']);
					}
					
				  //END OF CODE FOR SURGERY TIME	
					//CODE FOR GETTING DIAGNOSIS CODE 
					$diagonsis_code=$report1[$i]['diag_code'];
					$diag_code=explode(",",$diagonsis_code);
					$diag_code[0];
					$diag_code[1];
					$diagnos_code=$diag_code[0];
					//END OF CODE	
					//SET SPACE FOR SEQ & NAME
					if($i<10)
					{
						$nbsp="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					}
					elseif($i>=10 && $i<100)
					{
						$nbsp="&nbsp;&nbsp;&nbsp;&nbsp;";
					}
					elseif($i>100  && $i<1000)
					{
						$nbsp=" ";
					}	
					//END SET SPACE FOR SEQ & NAME		
				 	if($i<=$num)
					{	  
						$j=$i+1;		
						//bgcolor="#F1F4F0"	
						$tablerow.='<tr >
						  <td align="left">'.$i.'</td>
						  <td align="left">'.$patient_name.'</td>
						  <td align="center">'.$asc_id.'</td>
						  <td align="left">'.$surgeon_name.'</td>
						  <td valign="middle" align="left">';
						  
						//START CSV CODE
						$seq_csv 			=	'"'.trim($i).'"';
						$patient_name_csv 	=	'"'.trim($patient_name).'"';
						$asc_id_csv 		=	'"'.trim($asc_id).'"';
						$surgeon_name_csv 	=	'"'.trim($surgeon_name).'"';
						$procedure_name_csv = 	'';
						$dx_code_csv 		= 	'';
						$eyesite_csv		=	'"'.trim($eyesite).'"';
						$surgery_time_csv	=	'"'.trim($surgery_time).'"';
						//END CSV CODE
						  for($c=0;$c<=$procedure_length;$c++){
							if($procedure_length>=1)
							{ 
								$selProcedureQry ='select * from procedures where procedureId="'.$procedure_code_list[$c].'"';
								$selProcedureRes = imw_query($selProcedureQry);
								if(imw_num_rows($selProcedureRes)>0) {  
									$selProcedureRow = imw_fetch_array($selProcedureRes);
									
									if($procNameArray[$selProcedureRow['procedureId']] && $selProcedureRow['name'] <> $procNameArray[$selProcedureRow['procedureId']])
									{
										$selProcedureRow['name'] = $procNameArray[$selProcedureRow['procedureId']];
									}
									if($procCodeNameArray[$selProcedureRow['procedureId']] && $selProcedureRow['code'] <> $procCodeNameArray[$selProcedureRow['procedureId']])
									{
										$selProcedureRow['code'] = $procCodeNameArray[$selProcedureRow['procedureId']];
									}
									
									$selProcedureName = $selProcedureRow['name'];
									$selProcedureCode = $selProcedureRow['code'];
									$tablerow.=''. $selProcedureCode.'&nbsp;&nbsp;'.$selProcedureName.'<br>';
									//$tablerow.=''. $procedure_code_list[$c].'&nbsp;&nbsp;'.$procedure_name_list[$c].'<br>';
									
									if($c >= 1) {
										$procedure_name_csv 	.=	trim(trim(', '.$selProcedureCode).'  '.trim($selProcedureName));
									}else {
										$procedure_name_csv 	=	trim(trim($selProcedureCode).'  '.trim($selProcedureName));
									}
								} 
							}
						  }
						  $procedure_name_csv		=	'"'.trim($procedure_name_csv).'"';
						$tablerow.='</td><td align="left">'; 
						  
						  for($t=0;$t<=$diag_ids_length;$t++){
							if($diag_ids_length>=1)
							{ 
								$selDiagQry ='select * from diagnosis_tbl where diag_id="'.$diag_ids_list[$t].'"';
								$selDiagRes = imw_query($selDiagQry);
								if(imw_num_rows($selDiagRes)>0) {  
									$selDiagRow = imw_fetch_array($selDiagRes);
									$selDiagCodeTemp = $selDiagRow['diag_code'];
									$selDiagCodeExplode = explode(',',$selDiagCodeTemp);
									$selDiagCode = $selDiagCodeExplode[0];
									
									$tablerow.=''.$selDiagCode.'<br>';
									$dx_code_csv 	.=	trim($selDiagCode);
								} 
							}
						  }
						   
						  if($report1[$i]['icd10_code']) {
							 $tablerow.= str_ireplace(',','<br>',$report1[$i]['icd10_code']); 
							 $dx_code_csv 	.=	str_ireplace(',',', ',$report1[$i]['icd10_code']); 
						  }
						  $dx_code_csv		=	'"'.trim($dx_code_csv).'"';
						  
						$tablerow.='</td>
						  <td align="left">'.$eyesite.'</td>
						  <td align="left">'.$surgery_time.'</td>
						</tr>';
						$csv_content_val .= $seq_csv.','.$patient_name_csv.','.$asc_id_csv.','.$surgeon_name_csv.','.$procedure_name_csv.','.$dx_code_csv.','.$eyesite_csv.','.$surgery_time_csv."\n";		
					
					}//ending of if statement
				}//ending of inner for loop			
			
				$rowstart = $rowend;
				$rowend=$rowend + 10;
				
				$csv_content1 = $name.','."".','."".','."".','."".','."".','."".',Discharge Summary Report '.$showDate;
				$csv_content2 = $address.','."".','."".','."".','."".','."".','."".','."";
				$csv_content .= 'Seq'.','.'"Name"'.','.'"ASC-ID"'.','.'"Surgeon"'.','.'"Procedure"'.','.'"Dx-Code"'.','.'"Site"'.','.'"Sch.Time"'."\n";
				$csv_content .= $csv_content_val."\n";		
				
				}//ending of outer for loop
			}//ending of outer if condition
			 
	    
			else {
				$msg="No Record found!";
				$tablerow.='<tr><td valign="middle" align="center" colspan="8">'.$msg.'</td></tr>';
			}

			$table = str_replace('{{PAGE_CONTENT}}',$tablerow,$table);

if($_REQUEST['hidd_report_format']=='csv' && imw_num_rows($discharge_report)>0) {
	$file_name=$_SERVER['DOCUMENT_ROOT'].'/'.$surgeryCenterDirectoryName.'/admin/pdfFiles/discharge_summary_reportpop.csv';
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
	$filePut = fputs($fileOpen,$table);
	fclose($fileOpen);
	//$URL='http://'. $_SERVER['HTTP_HOST'].'/surgerycenter/testPdf.html';
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Discharge Summary Report</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="nofollow">
<meta name="googlebot" content="noindex">
<link rel="stylesheet" href="css/style_surgery.css" type="text/css" >   

<body style="background-color:#ECF1EA;">
<script language="javascript">
	function submitfn()
	{
		document.printFrm.submit();
	}
</script>



<form name="printFrm"  action="new_html2pdf/createPdf.php?op=P" method="post">

</form>	
<?php 
if($_REQUEST['hidd_report_format']!='csv' && imw_num_rows($discharge_report)>0) {?>
    <table class="table_collapse" style=" font-family:Verdana, Geneva, sans-serif; font-size:12px; background-color:#ECF1EA; height:1005;" >
        <tr>
            <td class="alignCenter valignMidddle" style="width:100%;" ><img src="images/pdf_load_img.gif"></td> 
        </tr>
    </table>

	<script type="text/javascript">
        submitfn();
    </script>
<?php 
}else {
	if($_REQUEST['hidd_report_format']=='csv') {
	?>
		<script type="text/javascript">
			location.href = "discharge_summary_report.php?no_record=yes&date12=<?php echo $_REQUEST['date12'];?>";
        </script>
    <?php
	}
	?>

<table style=" font-family:Verdana, Geneva, sans-serif; font-size:12px; background-color:#ECF1EA; width:100%; height:100%;">
	<tr>
		<td class="alignCenter valignTop" style="width:100%;"><b>No Record Found</b></td> 
	</tr>
</table>
<?php		
}?>	
</body>