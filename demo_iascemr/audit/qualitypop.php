<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include_once("../globalsSurgeryCenter.php");
include("common/auditLinkfile.php");
 $get_http_path=$_REQUEST['get_http_path'];
 
 $fac_con="";
if($_SESSION['iasc_facility_id'])
{
	$fac_con=" And ST.iasc_facility_id='$_SESSION[iasc_facility_id]'"; 
}


//set surgerycenter detail
			
			$SurgeryQry="select * from surgerycenter where surgeryCenterId=1";
			$SurgeryRes= imw_query($SurgeryQry);
			while($SurgeryRecord=imw_fetch_array($SurgeryRes))
			{
		    $nameSur= stripslashes($SurgeryRecord['name']);
			$address= stripslashes($SurgeryRecord['address'].' '.$SurgeryRecord['city'].' '.$SurgeryRecord['state']);
			$imgsur = $SurgeryRecord['logoName'];
			$surgeryCenterLogo=$SurgeryRecord['surgeryCenterLogo'];
			}
        //$file=@fopen('../html2pdf/white.jpg','w+');
		//@fputs($file,$surgeryCenterLogo);
		$bakImgResource = imagecreatefromstring($surgeryCenterLogo);
		imagejpeg($bakImgResource,'../html2pdf/white.jpg');
		
		$size=getimagesize('../html2pdf/white.jpg');
	    $hig=$size[1];
	    $wid=$size[0];
		
//
$filename='../html2pdf/white.jpg';
 function showThumbImages($fileName='white.jpg',$targetWidth=170,$targetHeight=50)
{ 

if(file_exists($fileName))
{ 
$img_size=getimagesize('../html2pdf/white.jpg');
 $width=$img_size[0];
 $height=$img_size[1];
 $filename;
do
{
if($width > $targetWidth)
{
  $width=$targetWidth;
   $percent=$img_size[0]/$targetWidth;
 $height=$img_size[1]/$percent; 
}
if($height > $targetHeight)
{
$height=$targetHeight;
 $percent=$img_size[1]/$targetHeight;
 $width=$img_size[0]/$percent; 
}

}while($width > $targetWidth || $height > $targetHeight);

$returnArr[] = "<img src='white.jpg' width='$width' height='$height'>";
$returnArr[] = $width;
$returnArr[] = $height;
return $returnArr; } 
return "";
}
//		
// end set surgerycenter detail   
// echo $user1=$_REQUEST['$user1']; 
 $userid = $_SESSION['loginUserId'];
 $lnamest= $_REQUEST['lnamest'];
 $lnamend=$_REQUEST['lnamend'];
 $number=$_REQUEST['number'];
 $month=$_REQUEST['mon'];
 $year=$_REQUEST['year1'];
 $type=$_REQUEST['sum'];
$curdate=date("m/d/Y");
 $patient=$_REQUEST['patient'];
 
$qrid=imw_query("select * from patient_data_tbl where patient_fname='$patient' or patient_lname between '$lnamest' and '$lnamend'");
 	$idcount=imw_num_rows($qrid);
while($idrec=imw_fetch_array($qrid))
{
	  $patientid[]=$idrec[0];
	
	  
}
$query=imw_query("select * from users where usersId='".$_SESSION['loginUserId']."'");
	
	while(@$name=imw_fetch_array($query))
	{

	$username=stripslashes($name['lname'].", ".$name['fname']);
	}
// Conditions Start	
	
	$qry = "select patientconfirmation.patientConfirmationId,patientconfirmation.dos 
			from patientconfirmation
			join patient_data_tbl on patient_data_tbl.patient_id = patientconfirmation.patientId 
			join stub_tbl ST on patientconfirmation.patientConfirmationId = ST.patient_confirmation_id Where 1=1 ";
	if($patient != '' && $lnamest != '')
		$qry .= " and (patient_data_tbl.patient_fname = '$patient' &&
					patient_data_tbl.patient_lname between '$lnamest'and '$lnamend')";
	else if($lnamest != ''){
		$qry .= " and patient_data_tbl.patient_lname between '$lnamest'and '$lnamend'";
	}
	else if($patient){
		$qry .= " and patient_data_tbl.patient_fname = '$patient'";
	}
	if($month!='' && $year!=''){ 
		//$date1= date("Y-m-d",mktime(0,0,0,$month,1,$year));
		//$date2= date("Y-m-d",mktime(0,0,0,$month+1,1,$year));
		//$date1 = '01-'.$month.'-'.$year;
		//$date2 = '31-'.$month.'-'.$year;
		$date1 = $year.'-'.$month.'-01';
		$date2 = $year.'-'.$month.'-31';
		
		$qry .= " and patientconfirmation.dos between '$date1' and '$date2'";
	}
	
	$qry .= $fac_con;
	
	if($number!='')
	{
	  $qry .=" limit 0, $number";
	}
	//echo $qry;
	$qryId = imw_query($qry);
	$qrn=imw_num_rows($qryId);
	$patientConfirmationId = array();
	$dos = array();
	while($res = imw_fetch_assoc($qryId)){
		$patientConfirmationId[] = $res['patientConfirmationId'];
		$dos[] = $res['dos'];
	}
	$dos = array_unique($dos);
	$patientConfirmation_id = implode(',',$patientConfirmationId);
	if(count($dos)>0){
		foreach($dos as $val){
			$qry = "select patientConfirmationId from patientconfirmation
					where dos = '$val' 
					and patientConfirmationId in ($patientConfirmation_id)";
			$res=imw_query($qry);
		$inc=0;	
		$confidNums=imw_num_rows($res);
		$totalpat=array();
		while($cnt= imw_fetch_array($res))
		{
		  
			$totalpat[] = $cnt['patientConfirmationId'];			
		}
		 $pconfId=implode(',',$totalpat);
		 
		$inc++;
		
$formnameSumm =array('No Of Patient','surgery_consent_form', 'hippa_consent_form','benefit_consent_form','insurance_consent_form ','preophealthquestionnaire','preopnursingrecord','postopnursingrecord','preopphysicianorders','postopphysicianorders','localanesthesiarecord','preopgenanesthesiarecord','genanesthesiarecord','genanesthesianursesnotes','operatingroomrecords','operativereport','dischargesummarysheet','patient_instruction_sheet','amendment','Successfully Completed');
$arrCount=count($formnameSumm);
$patient_confirmation_id_arr=array(11,12,17,18,19,20);
		//$data .= '<tr height="15"><td></td></tr>';
		$com = 0;
		for($f=0;$f<$arrCount;$f++)
		{	
		if($f == 8) $patient_confirmation_id='patient_confirmation_id';
		else if($f == 9) $patient_confirmation_id='patient_confirmation_id';
		else if($f == 17) $patient_confirmation_id='patient_confirmation_id';
		else $patient_confirmation_id='confirmation_id';
		if($f > 0 && $f < ($arrCount - 1)){
		$qryStatus = "select count(form_status) from ".$formnameSumm[$f]." 
						where $patient_confirmation_id in ($pconfId)
						and form_status = 'not completed';";
					//echo "<br>";	
			$qryRes = imw_query($qryStatus);
			list($confidNums) = imw_fetch_array($qryRes);
			if($confidNums==0)
			{
			  $confidNums='';
			}
			$qryStatus = "select count(form_status) from ".$formnameSumm[$f]." 
						where $patient_confirmation_id in ($pconfId)
						and form_status = 'completed';";
			$qryRes = imw_query($qryStatus);
			list($completed) = imw_fetch_array($qryRes);
			$com += $completed; 
		}
		if($f == ($arrCount - 1)){
			$confidNums = '';
			if($com > 0)
				$confidNums = $com;
		}
	    if($f>0)
		{
		 $val='';
		}
		if($f==0)
		{
		 $valbr=explode('-',$val);
		 $valYr=$valbr[0];
		 $valMn=$valbr[1];
		 $valDy=$valbr[2];
		 $val= $valMn.'-'.$valDy.'-'.$valYr;
		}
		$data .= '
			<tr>
				<td height="30" width="35%">'.$formnameSumm[$f].'</td>
				<td width="20%" align="center">'.$confidNums.'</td>
				<td width="21%" align="center">'.$val.'</td>
				<td width="24%"><b>&nbsp;</b></td>
			</tr>	
			
		';
		
		
		}	
	}
	}
?>
<html>
<head>	

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"></head>
<?php 
$table.='<table width="100%" border="1">

 
'.$data.'
</table>';

$tr_arr=explode('<tr',$table);
 $rowCount =(count($tr_arr))-1;

   $pageCount=count($tr_arr);
    if($pageCount>20)
	{
	  $pageCount=ceil($rowCount/20);
	}
	else
	{
	  $pageCount=1;
	}
   $cur_page=1;
   $statement_data='';
if($qrn>0)
{   
$img_logo = showThumbImages('../html2pdf/white.jpg',170,50);
 $imgheight= $img_logo[2]+8;
 $imgwidth= $img_logo[1]+8;   
              $table_pdf.='<table width="100%"  border="0" cellpadding="3" cellspacing="0" class="text_print" bgcolor="#FFFFFF">
							 <tr height="30"  bgcolor="#cd532f" >
								<td  align="left"   valign="bottom" >
								  <font color="#FFFFFF"><b>'.$name.'<br>'.$address.'</b></font>
								</td>
								<td  align="right" height="'.$imgheight.'" width="'.$imgwidth.'">'.$img_logo[0].'</td>
										</tr>
							</table>
				<table width="100%">
				<tr>
				  <td width="35%">&nbsp;</td>
				  <td width="20%" align="center"><b>Summary</b></td>
				  <td width="21%" align="center"><b>Date Of Report</b></td>
				  <td width="25%"><b>Operator:&nbsp;&nbsp;&nbsp;&nbsp;</b>'.$username.'</td>
				</tr>';	
		for($tr=1;$tr<count($tr_arr);$tr++){
			$firstPageTrCount++;
			if($firstPageTrCount ==20){
				$firstPageTrCount = 0;				
				$table_pdf .= '<tr'.$tr_arr[$tr].'';
				
				$table_pdf.='
				</table>';
				 $pageCount;
				
				if($pageCount/$cur_page>1)
				{
				
				  $table_pdf.='<newpage>';
				  $table_pdf.='<table width="100%">
		          <tr height="30"  bgcolor="#cd532f" >
					<td  align="left"   valign="bottom">
					  <font color="#FFFFFF"><b>'.$name.'<br>'.$address.'</b></font>
					</td>
					<td  align="right" height="'.$imgheight.'" width="'.$imgwidth.'">'.$img_logo[0].'</td>
							</tr>
				</table>';
				}
				
				
				$table_pdf.='
				<table width="100%">
				<tr>
				  <td width="35%">&nbsp;</td>
				  <td width="20%" align="center"><b>Summary</b></td>
				  <td width="21%" align="center"><b>Date Of Report</b></td>
				  <td width="25%"><b>Operator:</b>'.$username.'</td>
			   </tr>
			   <tr height="10"><td colspan="4">&nbsp;</td></tr>
			    ';
				$cur_page++;
				
			 }
			 else
			 {
			    $table_pdf.='<tr'.$tr_arr[$tr].'';
			 }
		}	
}
else
{
$img_logo = showThumbImages('../html2pdf/white.jpg',170,50);
 $imgheight= $img_logo[2]+8;
 $imgwidth= $img_logo[1]+8;
 $table_pdf.='<table width="100%"  border="0" cellpadding="3" cellspacing="0" class="text_print" bgcolor="#FFFFFF">
	 
	          <tr height="30"  bgcolor="#cd532f" >
				<td  align="left"   valign="bottom">
				  <font color="#FFFFFF"><b>'.$name.'<br>'.$address.'</b></font>
				</td>
				<td  align="right" height="'.$imgheight.'" width="'.$imgwidth.'">'.$img_logo[0].'</td>
			</tr>
	</table>
	<table width="100%">
				<tr><td height="100" colspan="2">&nbsp;</td></tr>
				<tr><td colspan="2" align="center"><b>No record found!</b></td></tr>
				</table>';
}		 	
//echo $table_pdf;
$filename="../testPdf.html";
unlink($filename);
$fileOpen=fopen($filename,'w+');
$filePut=fwrite($fileOpen,$table_pdf);
fclose($fileOpen);
?>
<form  name="printfrm"  action="../html2pdf/index.php?AddPage=P" method="post">
</form>
<script language="javascript">
document.printfrm.submit();
</script>
