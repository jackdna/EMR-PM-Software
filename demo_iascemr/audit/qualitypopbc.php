<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include_once("../globalsSurgeryCenter.php");
include("common/auditLinkfile.php");
// echo $user1=$_REQUEST['$user1'];
 $userid = $_REQUEST['name'];
 $lnamest= $_REQUEST['lnamest'];
 $lnamend=$_REQUEST['lnamend'];
 $number=$_REQUEST['number'];
 $month=$_REQUEST['mon'];
 $year=$_REQUEST['year1'];
 $type=$_REQUEST['sum'];
$curdate=date("m/d/Y");
 $patient=$_REQUEST['patient'];
 
$qrid=imw_query("select * from patient_data_tbl where patient_fname='$patient' or patient_lname between '$lnamest' and '$lnamend' ");
 	$idcount=imw_num_rows($qrid);
while($idrec=imw_fetch_array($qrid))
{
	  $patientid[]=$idrec[0];
	
	  
}
$query=imw_query("select * from users where usersId=$userid");
	
	while(@$name=imw_fetch_array($query))
	{

	 $username=$name[1]." ".$name[3];
	}
?><html>
<head>	

</head>

<?php 
							
								//for($b=0;$b<$idcount;$b++)
								if($patient!='' && $lnamest!='' && $lnamend!=''  && $month!='' && $year!='' && $number!='' )
								{	//echo "a";
									$dat1= date(mktime(0,0,0,$month,1,$year));
 									$date1= date("Y-m-d",$dat1);
									$dat2= date(mktime(0,0,0,$month+1,1,$year));
									$date2= date("Y-m-d",$dat2);
									$qqr=imw_query ("select * from patientconfirmation where  dos between '$date1' and '$date2' limit 0,$number ");
									
									while($ascid=@imw_fetch_array($qqr))
									{
									 $asc=imw_num_rows($qqr);
									$qry=("select * from patient_data_tbl where patient_fname='$patient' and patient_lname between '$lnamest' and '$lnamend' limit 0,$number ");
									}
								}  
								elseif($patient!=''  && $month!='' && $year!='' && $number!='' )
								{	//echo "b";
									$dat1= date(mktime(0,0,0,$month,1,$year));
 									$date1= date("Y-m-d",$dat1);
									$dat2= date(mktime(0,0,0,$month+1,1,$year));
									$date2= date("Y-m-d",$dat2);
									$qqr=imw_query ("select * from patientconfirmation where  dos between '$date1' and '$date2' limit 0,$number ");
									
									while($ascid=@imw_fetch_array($qqr))
									{
									 $asc=imw_num_rows($qqr);
									$qry=("select * from patient_data_tbl where patient_fname='$patient' limit 0,$number ");
									}
								}  
								elseif($patient!=''  && $month!='' && $year!='' )
								{	//echo "a";
									$dat1= date(mktime(0,0,0,$month,1,$year));
 									$date1= date("Y-m-d",$dat1);
									$dat2= date(mktime(0,0,0,$month+1,1,$year));
									$date2= date("Y-m-d",$dat2);
									$qqr=imw_query ("select * from patientconfirmation where  dos between '$date1' and '$date2'");
									
									while($ascid=@imw_fetch_array($qqr))
									{
									 $asc=imw_num_rows($qqr);
									$qry=("select * from patient_data_tbl where patient_fname='$patient'");
									}
								}  
								
								elseif($patient!='' && $lnamest!='' && $lnamend!='' && $number!='')
								{//echo "c";
									$qry=("select * from patient_data_tbl where patient_fname='$patient' and patient_lname between '$lnamest' and '$lnamend' limit 0,$number ");
								} 
								elseif(($patient<>'') && ($lnamest<>'') && ($lnamend<>''))
								{//echo "b";
									$qry=("select * from patient_data_tbl where patient_fname='$patient' and patient_lname between '$lnamest' and '$lnamend' ");
								}	
								elseif($lnamest!='' && $lnamend!=''  && $month!='' && $year!='' && $number!='')
								{
									
									$qry=("select * from patient_data_tbl where  patient_lname between '$lnamest' and '$lnamend' limit 0,$number  ");
								}  
								elseif($lnamest!='' && $lnamend!=''  && $month!='' && $year!='')
								{//echo "d";
									
									$qry=("select * from patient_data_tbl where  patient_lname between '$lnamest' and '$lnamend'  ");
								}  
								elseif($lnamest!='' && $lnamend!='' && $number!='')
								{//echo "e";
									$qry=("select * from patient_data_tbl where  patient_lname between '$lnamest' and '$lnamend' limit 0,$number ");
								}  
								elseif($lnamest!='' && $lnamend!='')
								{//echo "g";
									$qry=("select * from patient_data_tbl where  patient_lname between '$lnamest' and '$lnamend' ");
								}  
								elseif($patient!='')
								{
									$qry=("select * from patient_data_tbl where patient_fname='$patient'");
								}  
								elseif($patient!='' && $number!='')
								{
									$qry=("select * from patient_data_tbl where patient_fname='$patient' limit 0,$number");
								}  
								elseif($month!='' && $year!='' )
								{	//echo "d";
									 $dat1= date(mktime(0,0,0,$month,1,$year));
 									$date1= date("Y-m-d",$dat1);
									$dat2= date(mktime(0,0,0,$month+1,1,$year));
									$date2= date("Y-m-d",$dat2);
									$qry = "select * from patientconfirmation where  dos between '$date1' and '$date2' ";
									$qqy=imw_query ($qry);
									$id = array();
									while($ascid=@imw_fetch_array($qqy))
									{
									 	$id[]=$ascid['ascId'];
									}
									$asId = implode(',',$id);
									$qry=("select * from patient_data_tbl where asc_id in($asId)");
								}
								elseif($number!='')
								{
									$qry=("select * from patient_data_tbl limit 0,$number");
								}
								elseif($month!='' && $year!=''  )
								{
									 $dat1= date(mktime(0,0,0,$month,1,$year));
 									$date1= date("Y-m-d",$dat1);
									$dat2= date(mktime(0,0,0,$month+1,1,$year));
									$date2= date("Y-m-d",$dat2);
									$qry = "select * from patientconfirmation where  dos between '$date1' and '$date2' ";
									$qqy=imw_query ($qry);
									$id = array();
									while($ascid=@imw_fetch_array($qqy))
									{
									 	$id[]=$ascid['ascId'];
									}
									$asId = implode(',',$id);
									$qry=("select * from patient_data_tbl where asc_id in($asId) ");
								}
								//print $qry;
								$qury=imw_query($qry); 
								$l = 0;
								while($res=@imw_fetch_array($qury))
								{
									foreach($res as $key => $val){
										$patientDetails[$l][$key] = $val;
									}
									$l++;
								 	
								}
								//print '<p >';
								//print_r($patientDetails);
								$row=@imw_num_rows($qury);
								$rowBreak = 3;
								$rows= ceil($row /$rowBreak);
								$tdStart = 0;
								$formnameArr =array('Name','Asc_id','DOS','surgery_consent_form', 'hippa_consent_form','benefit_consent_form','preophealthquestionnaire','preopnursingrecord','postopnursingrecord','preopphysicianorders','postopphysicianorders','localanesthesiarecord ','preopgenanesthesiarecord','genanesthesiarecord','genanesthesianursesnotes ','operatingroomrecords','dischargesummarysheet ','postopinstruc...','amendment');
								for($c=0;$c<$rows;$c++){
									$tdStart = $c * $rowBreak;
									if($tdStart > 0){
										$rowBreak = $rowBreak + $tdStart;
									}
									$rowsData = '';
									for($f=0;$f<count($formnameArr);$f++){			
										$tdData = '';
										for($t=$tdStart;$t < $rowBreak;$t++){
											$getRecAscId = $patientDetails[$t]['asc_id'];
											if($formnameArr[$f] == 'Name'){
												$patientData = $patientDetails[$t]['patient_fname'].' '.$patientDetails[$t]['patient_lname'];
											}
											else if($formnameArr[$f] == 'Asc_id'){
												$patientData = $patientDetails[$t]['asc_id'];
											}
											else if($formnameArr[$f] == 'DOS'){
												$dosAscId = $patientDetails[$t]['asc_id'];
												if($month!='' && $year!=''){ 
													$date1= date("Y-m-d",mktime(0,0,0,$month,1,$year));
													$date2= date("Y-m-d",mktime(0,0,0,$month+1,1,$year));
												}
												$qry = "select dos from patientconfirmation where ascId = '$dosAscId'";
												if($date1 != '' and $date2 != ''){
													$qry .=" and dos between '$date1' and '$date2' ";
												}
												$qryId = imw_query($qry);
												list($dos) = imw_fetch_array($qryId);
												list($yy,$mm,$dd) = explode('-',$dos);
												$patientData = $mm.'-'.$dd.'-'.$yy;
											}
											else{
												$qry = "select form_status from ".$formnameArr[$f]." where ascId='$getRecAscId'";
												$qryId = @imw_query($qry);
												
												$n=@imw_num_rows($qryId);
												//for($s=0;$s<=$f;){
												//if($n=0)
												//{
													//echo $s++;
												//}
												//}
												(list($form_status) = @imw_fetch_array($qryId));
												if($form_status == 'yes'){
													$img = '../images/check_mark16.png';
													
												}
												elseif($form_status == ''){
													$img = '../images/red_flag.png';
													
												}
												else
												{
													$img = '../images/tpixel.gif';
												}
												$patientData = '<img  src='.$img.' width="15" height="15">';
											}											
											$tdData .= '<td width="20%" align="center">'.$patientData.'</td>';
										}			
																
										$rowsData .= '
											<tr>
												<td style=" font-weight:bold;" width="40%">'.$formnameArr[$f].'</td>
												'.$tdData.'
											</tr>
										';		
																		
									}
									
									
									$tableRows .= '
									<table style="font:vetrdana; font-size:14;" height="100%" >
										<tr height="35" bgcolor="#BCD2B0">
											<td  height="35" align="left" width="45%"  valign="top" ><div class="text_printb" >Imedic Surgery Center</div>
													 22 plainfield ave Lavallette, NJ 08735 </td>
											<td align="right"><img src="../images/logo1.gif"></td>
											</tr>
											<tr height="22"  bgcolor="#F1F4F0">
											<td valign="top" align="left"><span ><img src="../images/tpixel.gif" width="14">Operator: </span><span class="red_txt">'.$username.'</span></td>
											<td align="right" valign="top"><span >Date: </span><span class="red_txt">'.$curdate.'</span><img src="../images/tpixel.gif" width="14"></td>
										</tr>
										<tr>
											<td valign="top" colspan="3">
												<table width="100%" align="center">
													'.$rowsData.'
												</table>
											</td>
										</tr>
										
									</table>	
									';
								}
$filename="testPdf.html";
$fileOpen = fopen($filename,'w+');
$filePut = fwrite($fileOpen,$tableRows);
fclose($fileOpen);
$URL='http://'. $_SERVER['HTTP_HOST'].'/surgerycenter/audit/testPdf.html';
echo $tableRows;
?>
<script language="javascript">
	function submitfn()
	{
		document.printFrm.submit();
	}
</script>
<table bgcolor="#FFFFFF"  style="font:vetrdana; font-size:14;" width="100%" height="100%">
	<tr>
		<td width="100%" align="center" valign="middle"><img src="../images/ajax-loader.gif"></td> 
	</tr>
</table>

<body>
<form name="printFrm" action="../html2pdf/public_html/demo/html2ps.php"  method="post">
<input type="hidden" name="process_mode" value="single">
<input type="hidden" name="url" value="testPdf.html">
<input type="hidden" name="URL" value="<?php echo $URL; ?>">   
<input type="hidden" name="proxy" value="&&">
<input type="hidden" name="pixels" value="800">
<input type="hidden" name="scalepoints" value="1"> 
<input type="hidden" name="renderimages" value="1"> 
<input type="hidden" name="renderlinks" value="1"> 
<input type="hidden" name="renderfields" value="1"> 
<input type="hidden" name="media" value="Letter">
<input type="hidden" name="cssmedia" value="Handheld">
<input type="hidden" name="leftmargin" value="2">
<input type="hidden" name="rightmargin" value="2">
<input type="hidden" name="topmargin" value="2">
<input type="hidden" name="bottommargin" value="0">
<input type="hidden" name="toc-location" value="before">
<input type="hidden" name="smartpagebreak" value="0">
<input type="hidden" name="pslevel" value="1">
<input type="hidden" name="method" value="fpdf">
<input type="hidden" name="pdfversion" value="1.3">
<input type="hidden" name="output" value="0">
</form>		
<script type="text/javascript">
	//submitfn();
</script>
</body>