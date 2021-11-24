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
include("common/conDb.php");
if($_SESSION["loginUserId"]=="" && $_SESSION['loginUserName']=="") {
	echo '<script>top.location.href="index.php"</script>';
}
include("admin/classObjectFunction.php");
$objManageData = new manageData;
$get_http_path=$_REQUEST['get_http_path'];
$start_date=$_REQUEST['from_date'];
$end_date=$_REQUEST['to_date'];
$checkbox=$_REQUEST['chkbox_unfinalized'];
if($_SESSION['iasc_facility_id'])
{
	$fac_con=" and stub_tbl.iasc_facility_id='$_SESSION[iasc_facility_id]'"; 
}	
//get detail for logged in facility
$queryFac=imw_query("select * from facility_tbl where fac_id='$_SESSION[facility]'")or die(imw_error());
$dataFac=imw_fetch_object($queryFac);
$name=stripslashes($dataFac->fac_name);
$address=stripslashes($dataFac->fac_address1).' '.stripslashes($dataFac->fac_address2).' '.stripslashes($dataFac->fac_city).' '.stripslashes($dataFac->fac_state);

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
return $returnArr; } 
return "";
}				
// end set surgerycenter detail 		
			// end set surgerycenter detail
if($start_date!="")
{
 $startdate=explode("-",$start_date);
    $startdate[0];
	$startdate[1];
	$startdate[2];
$starting_date=$startdate[2].'-'.$startdate[0].'-'.$startdate[1];
}
if($end_date!="")
{
 $enddate=explode("-",$end_date);
    $enddate[0];
	$enddate[1];
	$enddate[2];
 $ending_date=$enddate[2].'-'.$enddate[0].'-'.$enddate[1];
}
$current_date=date("m-d-Y");

$img_logo = showThumbImages('new_html2pdf/white.jpg',170,50);
$imgheight= $img_logo[2]+8;
$imgwidth= $img_logo[1]+8;

$date_filter_txt = ($start_date!='' && $end_date!='') ? "<b>From&nbsp;&nbsp;".$start_date."&nbsp;&nbsp;To&nbsp;&nbsp;".$end_date."</b>" : "";

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
							<td align="left"><b>Un-finalized Patient Chart</b></td>
							<td >&nbsp;'.$date_filter_txt.'</td>
						</tr>
					</table>	
				</page_header>

				<table width="700" border="1" cellpadding="3" cellspacing="0" bgcolor="#FFFFFF">
					<thead>
						<tr class="text_printb">
							<td width="25" class="text_b">Seq</td>
							<td width="100" class="text_b">Name</td>
							<td width="70" class="text_b">ASC-ID</td>
							<td width="220" class="text_b">Procedure</td>
							<td width="50" class="text_b">Site</td>
							<td width="75" class="text_b">DOS</td>
							<td width="75" class="text_b">From</td>
							<td width="75" class="text_b">To</td>
						</tr>
					</thead>
					{{PAGE_CONTENT}}
				</table>
			</page>	

			';

		 if($checkbox=="range" && $starting_date<>"" && $ending_date<>"")
		{ 
		  
		  $unfinalized="select patientconfirmation.*,
			patient_data_tbl.patient_fname,
			patient_data_tbl.patient_mname,
			patient_data_tbl.patient_lname,
			preopnursingrecord.preopnursingSaveDateTime,
			dischargesummarysheet.summarySaveDateTime from patientconfirmation 
			LEFT JOIN patient_data_tbl on patientconfirmation.patientId = patient_data_tbl.patient_id 
			LEFT JOIN preopnursingrecord on patientconfirmation.patientConfirmationId=preopnursingrecord.confirmation_id
			LEFT JOIN dischargesummarysheet on patientconfirmation.patientConfirmationId= dischargesummarysheet.confirmation_id
			LEFT JOIN stub_tbl ON stub_tbl.patient_confirmation_id=dischargesummarysheet.confirmation_id
			where patientconfirmation.finalize_status='' && patientconfirmation.dos between '$starting_date' AND '$ending_date' 
			AND stub_tbl.patient_confirmation_id!='' 
		   $fac_con
		   group by patientconfirmation.patientConfirmationId";
		}elseif($checkbox=="range" && $starting_date<>"" && $ending_date=="")
	    { 
	
		 $unfinalized="select patientconfirmation.*,
			patient_data_tbl.patient_fname,
			patient_data_tbl.patient_mname,
			patient_data_tbl.patient_lname,
			preopnursingrecord.preopnursingSaveDateTime,
			dischargesummarysheet.summarySaveDateTime from patientconfirmation 
			left join patient_data_tbl on patientconfirmation.patientId = patient_data_tbl.patient_id
			left join preopnursingrecord on patientconfirmation.patientConfirmationId=preopnursingrecord.confirmation_id 
			left join dischargesummarysheet on patientconfirmation.patientConfirmationId= dischargesummarysheet.confirmation_id
			LEFT JOIN stub_tbl ON stub_tbl.patient_confirmation_id=dischargesummarysheet.confirmation_id
		    where patientconfirmation.finalize_status=''  && patientconfirmation.dos>='$starting_date'
			AND stub_tbl.patient_confirmation_id!=''   
		   $fac_con
		   group by patientconfirmation.patientConfirmationId";
	     }
		elseif($checkbox=="detail")
		{ 
		
		 $unfinalized="select patientconfirmation.*,
			patient_data_tbl.patient_fname,
			patient_data_tbl.patient_mname,
			patient_data_tbl.patient_lname,
			preopnursingrecord.preopnursingSaveDateTime,
			dischargesummarysheet.summarySaveDateTime from patientconfirmation 
			left join patient_data_tbl on patientconfirmation.patientId = patient_data_tbl.patient_id
			left join preopnursingrecord on patientconfirmation.patientConfirmationId=preopnursingrecord.confirmation_id
			left join dischargesummarysheet on patientconfirmation.patientConfirmationId= dischargesummarysheet.confirmation_id
			LEFT JOIN stub_tbl ON stub_tbl.patient_confirmation_id=dischargesummarysheet.confirmation_id
		   where patientconfirmation.finalize_status='' AND patientconfirmation.dos!='0000-00-00' 
			AND stub_tbl.patient_confirmation_id!='' 
		   $fac_con
		   group by patientconfirmation.patientConfirmationId";
		}
		//Change on all queries
	// left join patient_data_tbl on patientconfirmation.ascId =patient_data_tbl.asc_id 
	     //Above line is replaced with left join patient_data_tbl on patientconfirmation.patientId = patient_data_tbl.patient_id 			
   $unfinal_query=imw_query($unfinalized);
   $rows=@imw_num_rows($unfinal_query);
   $csv_content_val = '';
   if(@imw_num_rows($unfinal_query)>0){

			
			   $a=1;
				while($unfinal=imw_fetch_array($unfinal_query))
				{   
				   foreach($unfinal as $key=>$val)
				   {
				    
				     $unfinal_rpt[$a][$key]=$val;
					
				   }    
			        $a++;
				}
				    
			   $num=imw_num_rows($unfinal_query);
			   $rowbreak=14;
			   $numb=ceil($num/$rowbreak);
			   $rowstart=1;
			   $rowend = 14;
			   for($rows=0;$rows<$numb;$rows++)
			   {
			    	$tablerow='';
            		for($i=$rowstart;$i<$rowend;$i++)
		          	{
				    	$asc_id=$unfinal_rpt[$i]['ascId'];
				    	$patient_id=$unfinal_rpt[$i]['patientId'];
					 	$confirmation_id=$unfinal_rpt[$i]['patientConfirmationId'];
						// $surgeon_name=$report['surgeon_name'];
						$procedure_pname=$unfinal_rpt[$i]['patient_primary_procedure'];
						 
					 	$procedure_sname=$unfinal_rpt[$i]['patient_secondary_procedure'];
					 	//FINDING PROCEUDRE NAME TO BE APPLIED
					 	if($procedure_sname<>"N/A") {
							$procedure_name=$procedure_pname.","."<br>".$procedure_sname;
					 		
					 	} 
						else {
							$procedure_name= wordwrap($procedure_pname,40,"<br>");
							
						}
						//END
					   	$dos=$unfinal_rpt[$i]['dos'];
					 	//exploding date of surgery
					  	$date=explode("-",$dos);
						$date[0];$date[1];$date[2];
						$date_of_surgery=$date[1]."-".$date[2]."-".$date[0];
						//end of exploding
						$site=$unfinal_rpt[$i]['site'];
						//APPLYING NUMBERS TO SITE

						if($site==1) {
							$eyesite="Left eye";
						}
						elseif($site==2) {
							$eyesite="Right eye";
						}
						elseif($site==3) {
							$eyesite="Both eyes";
						} //END

						//CODE FOR PATIENT NAME FROM PATIENT_DATA_TBL
						$patient_fname=$unfinal_rpt[$i]['patient_fname'];
						$patient_mname=$unfinal_rpt[$i]['patient_mname'];
						$patient_lname=$unfinal_rpt[$i]['patient_lname'];
						$patient_name=$patient_lname.", ".$patient_fname;
						//END OF CODE
				
						//CODE FOR STARTING DATE FOR UNFINALIZED PATIENT FROM PREOPNURSING TABLE
						if($unfinal_rpt[$i]['preopnursingSaveDateTime']=="0000-00-00 00:00:00" || $unfinal_rpt[$i]['preopnursingSaveDateTime']==""){
						$start_time = "";
						}else{
						$start_time = $objManageData->getTmFormat($unfinal_rpt[$i]['preopnursingSaveDateTime']);
						}
						//END OF CODE
				
						//CODE FOR END DATE FOR UNFINALIZED PATIENT FROM DISCHARGESUMMARYSHEET TABLE
						if($unfinal_rpt[$i]['summarySaveDateTime']=="0000-00-00 00:00:00" || $unfinal_rpt[$i]['summarySaveDateTime']==""){
							$end_time = "";
						}else{
							$end_time = $objManageData->getTmFormat($unfinal_rpt[$i]['summarySaveDateTime']);
						}
						//END OF CODE
						if($i<10) {
							$nbsp="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
						}
						elseif($i>=10 && $i<100) {
							$nbsp="&nbsp;&nbsp;&nbsp;&nbsp;";
						}
						elseif($i>100  && $i<1000) {
							$nbsp=" ";
						}
				
						if($i<=$num) {
							$j=$i+1;
							
							$tablerow.='
							<tr>
								<td >'.$i.'</td>
								<td >'.$patient_name.'</td>
								<td >'.$asc_id.'</td>
								<td height="'.$hight.'" >'.trim($procedure_name).'</td>
								<td >'.$eyesite.'</td>
								<td >'.$date_of_surgery.'</td>
								<td >'.$start_time.'</td>
								<td >'.$end_time.'</td>
							</tr>';
				
							//START CSV CODE
							$seq_csv 				=	'"'.trim($i).'"';
							$patient_name_csv 		=	'"'.trim($patient_name).'"';
							$asc_id_csv 			=	'"'.trim($asc_id).'"';
							//$surgeon_name_csv 		=	'"'.trim($surgeon_name).'"';
							$procedure_name_csv 	= 	'"'.trim(str_ireplace("<br>","  ",$procedure_name)).'"';
							$eyesite_csv			=	'"'.trim($eyesite).'"';
							$date_of_surgery_csv	=	'"'.trim($date_of_surgery).'"';
							$start_time_csv			=	'"'.trim($start_time).'"';
							$end_time_csv			=	'"'.trim($end_time).'"';
							
							$csv_content_val 		.= $seq_csv.','.$patient_name_csv.','.$asc_id_csv.','.$procedure_name_csv.','.$eyesite_csv.','.$date_of_surgery_csv.','.$start_time_csv.','.$end_time_csv."\n";		
							//END CSV CODE
						}//ending of if statement
		      		}//ending of inner for loop			
			
		 			$rowstart = $rowend;
	      			$rowend=$rowend + 14;
		 			
					$csv_content1 = $name.','."".','."".',Un-Finalized Patient Chart Report '.$current_date.','."".','."".','."".','."";
					$csv_content2 = $address.','."".','.$start_date_csv.','."".','.$end_date_csv.','."".','."".','."";
					$csv_content .= 'Seq'.','.'"Name"'.','.'"ASC-ID"'.','.'"Procedure"'.','.'"Site"'.','.'"DOS"'.','.'"From"'.','.'"To"'."\n";
					$csv_content .= $csv_content_val."\n";		
					
				}//ending of outer for loop
	}//end of outer if condition
	else
	{
		$msg="No Record found!";
		$tablerow.='<tr><td valign="middle" align="center" colspan="8">'.$msg.'</td></tr>';
	}
	
	$table = str_replace('{{PAGE_CONTENT}}',$tablerow,$table);

if($_REQUEST['hidd_report_format']=='csv' && imw_num_rows($unfinal_query)>0) {
	$file_name=$_SERVER['DOCUMENT_ROOT'].'/'.$surgeryCenterDirectoryName.'/admin/pdfFiles/unfinalizedpatient_reportpop.csv';
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
<title>Discharge Summary Report</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="nofollow">
<meta name="googlebot" content="noindex">
<link rel="stylesheet" href="css/style_surgery.css" type="text/css" >   

<body>
<form name="printFrm"  action="new_html2pdf/createPdf.php?op=P" method="post">

</form>	
<?php 
if($_REQUEST['hidd_report_format']!='csv' && imw_num_rows($unfinal_query)>0) {?>
	<script type="text/javascript">
		function submitfn() {
			document.printFrm.submit();
		}
		submitfn();
    </script>
<?php 
}else {
?>
	<script>
		if(document.getElementById("loader_tbl")) {
			document.getElementById("loader_tbl").style.display = "none";	
		}
	</script>	

<?php	
	if($_REQUEST['hidd_report_format']=='csv') {
	?>
		<script type="text/javascript">
			location.href = "unfinalizedpatient_report.php?no_record=yes&from_date=<?php echo $_REQUEST['from_date'];?>&to_date=<?php echo $_REQUEST['to_date'];?>&chkbox_unfinalized=<?php echo $_REQUEST['chkbox_unfinalized'];?>";
        </script>
    <?php
	}
	?>
	<table style=" font-family:Verdana, Geneva, sans-serif; font-size:12px; background-color:#EAF0F7; width:100%; height:100%;">
		<tr>
			<td class="alignCenter valignTop" style="width:100%;"><b>No Record Found</b></td> 
		</tr>
	</table>
<?php		
}?>	
</body>
	