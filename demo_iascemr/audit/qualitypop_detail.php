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
		    $nameSur= $SurgeryRecord['name'];
			$address= $SurgeryRecord['address'].' '.$SurgeryRecord['city'].' '.$SurgeryRecord['state'];
			$imgsur = $SurgeryRecord['logoName'];
			$surgeryCenterLogo=$SurgeryRecord['surgeryCenterLogo'];
			}
        $file=@fopen('../html2pdf/white.jpg','w+');
		@fputs($file,$surgeryCenterLogo);
		$size=getimagesize('../html2pdf/white.jpg');
	    $hig=$size[1];
	    $wid=$size[0];
$filename='../html2pdf/white.jpg';
 function showThumbImages($fileName='white.jpg',$targetWidth=500,$targetHeight=70)
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

	 $username=$name['lname'].", ".$name['fname'];
	}
?><html>


<?php 
							
								//for($b=0;$b<$idcount;$b++)
								if($patient!='' && $lnamest!='' && $lnamend!=''  && $month!='' && $year!='' && $number!='' )
								{	//echo "a";
									//$date1 = '01-'.$month.'-'.$year;
		                            //$date2 = '31-'.$month.'-'.$year;
									$date1 = $year.'-'.$month.'-'.'-01';
		                            $date2 = $year.'-'.$month.'-'.'-31';
									
									$qqr4=imw_query ("select * from patientconfirmation PC Join stub_tbl ST On PC.patientConfirmationId = ST.patient_confirmation_id where PC.dos between '$date1' and '$date2' ".$fac_con." limit 0,$number ");
									
									while($ascid=@imw_fetch_array($qqr4))
									{
									 $asc=imw_num_rows($qqr4);
									$qry=("select * from patient_data_tbl where patient_fname='$patient' and patient_lname between '$lnamest' and '$lnamend' limit 0,$number ");
									}
								}  
								elseif($patient!=''  && $month!='' && $year!='' && $number!='' )
								{	//echo "b";
									//$date1 = '01-'.$month.'-'.$year;
		                           // $date2 = '31-'.$month.'-'.$year;
									$date1 = $year.'-'.$month.'-'.'-01';
		                            $date2 = $year.'-'.$month.'-'.'-31';
									
									$qqr=imw_query ("select * from patientconfirmation PC Join stub_tbl ST On PC.patientConfirmationId = ST.patient_confirmation_id where  dos between '$date1' and '$date2' ".$fac_con." limit 0,$number ");
									
									while($ascid=@imw_fetch_array($qqr))
									{
									 $asc=imw_num_rows($qqr);
									$qry=("select * from patient_data_tbl where patient_fname='$patient' limit 0,$number ");
									}
								}  
								elseif($patient!=''  && $month!='' && $year!='' )
								{	//echo "a";
									//$date1 = '01-'.$month.'-'.$year;
		                           // $date2 = '31-'.$month.'-'.$year;
									$date1 = $year.'-'.$month.'-'.'-01';
		                            $date2 = $year.'-'.$month.'-'.'-31';
									
									$qqr=imw_query ("select * from patientconfirmation PC Join stub_tbl ST On PC.patientConfirmationId = ST.patient_confirmation_id where  dos between '$date1' and '$date2' ".$fac_con." ");
									
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
									 //$date1 = '01-'.$month.'-'.$year;
		                             //$date2 = '31-'.$month.'-'.$year;
									 $date1 = $year.'-'.$month.'-'.'-01';
		                             $date2 = $year.'-'.$month.'-'.'-31';
									
									$qry3 = "select * from patientconfirmation PC Join stub_tbl ST On PC.patientConfirmationId = ST.patient_confirmation_id where  dos between '$date1' and '$date2' ".$fac_con." ";
									$qqy=imw_query ($qry3);
									$id = array();
									while($ascid=@imw_fetch_array($qqy))
									{
									 	$id[]=$ascid['patientId'];
									}
									$asId = implode(',',$id);
									$qry=("select * from patient_data_tbl where patient_id in($asId)");
								}
								elseif($number!='')
								{
									$qry=("select * from patient_data_tbl limit 0,$number");
								}
								elseif($month!='' && $year!=''  )
								{
									 //$date1 = '01-'.$month.'-'.$year;
	                                 //$date2 = '31-'.$month.'-'.$year;
									 $date1 = $year.'-'.$month.'-'.'-01';
		                             $date2 = $year.'-'.$month.'-'.'-31';
									$qry12 = "select * from patientconfirmation PC Join stub_tbl ST On PC.patientConfirmationId = ST.patient_confirmation_id where  dos between '$date1' and '$date2' ".$fac_con." ";
									$qqy=imw_query ($qry12);
									$id = array();
									while($ascid=@imw_fetch_array($qqy))
									{
									 	$id[]=$ascid['patientId'];
									}
									$asId = implode(',',$id);
									$qry=("select * from patient_data_tbl where patient_id in($asId) ");
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
								//print_r($patientDetails[9]['patient_id']);
								$row=@imw_num_rows($qury);
								$rowBreak = 3;
								$rows= ceil($row /$rowBreak);
								$tdStart = 0;
								$coplete=0;
								$incomplete=0;
								$arr=array();
								$cnt="";
								$formnameArr =array('Name','Asc_id','DOS','surgery_consent_form', 'hippa_consent_form','benefit_consent_form','insurance_consent_form ','preophealthquestionnaire','preopnursingrecord','postopnursingrecord','preopphysicianorders','postopphysicianorders','localanesthesiarecord','preopgenanesthesiarecord','genanesthesiarecord','genanesthesianursesnotes','operatingroomrecords','operativereport','dischargesummarysheet','patient_instruction_sheet','amendment','','','','');
								$formnamedispArr =array('Name','Asc_id','DOS','Surgery-Consent-Form', 'Hippa-Consent-Form','Benefit-Consent-Form','Insurance-Consent-Form ','Pre-Op-Healthquestionnaire','Pre-Op-Nursing-Record','Post-Op-Nursing-Record','Pre_Op-Physician-Orders','Post-Op-Physician-Orders','Local-Anesthesia-Record','Pre-op-Gen-Anesthesia-Record','General-Anesthesia-Record','General-Anesthesia-Nurses-Notes','Operating-Room-Records','Operative-Report','Discharge-Summary-Sheet ','Patient-Instruction-Sheet','Amendment');
								
								for($c=0;$c<$rows;$c++){
									//$tdStart = $c * $rowBreak;
									$tdStart = $c * 3;
									if($tdStart > 0){
										$rowBreak = $rowBreak + $tdStart;
									}
									$rowsData = '';
									
									for($f=0;$f<count($formnameArr);$f++){			
										

										$tdData = '';
										for($t=$tdStart;$t < $rowBreak;$t++){
										//echo $t;
										//echo '<pre>';
								    	$dosAscId = $patientDetails[$t]['patient_id'];
										$confQry="select patientConfirmationId from patientconfirmation where patientId = '$dosAscId' ";
											   $confRes=imw_query($confQry);	
											   list($patientConfirmationId) = imw_fetch_array($confRes);
											$getRecAscId = $patientDetails[$t]['patient_id'];
											if($formnameArr[$f] == 'Name'){
												$patientData = $patientDetails[$t]['patient_lname'].', '.$patientDetails[$t]['patient_fname'];
											}
											else if($formnameArr[$f] == 'Asc_id'){
												$patientData = $patientDetails[$t]['asc_id'];
											}
											else if($formnameArr[$f] == 'DOS'){
												$dosAscId = $patientDetails[$t]['patient_id'];
												if($month!='' && $year!=''){ 
													//$date1 = '01-'.$month.'-'.$year;
		                                            //$date2 = '31-'.$month.'-'.$year;
													$date1 = $year.'-'.$month.'-'.'-01';
		                             				$date2 = $year.'-'.$month.'-'.'-31';
												}
												
												$qry = "select dos,patientConfirmationId  from patientconfirmation where patientId = '$dosAscId'";
												if($date1 != '' and $date2 != ''){
													$qry .=" and dos between '$date1' and '$date2' ";
													
												}
												$qry .= " order by patientconfirmation.dos asc";
												$qryId = imw_query($qry);
											(list($dos) = imw_fetch_array($qryId));
										    (list($patientConfirmationId) = imw_fetch_assoc($qryId));
												list($yy,$mm,$dd) = explode('-',$dos);
												$patientData = $mm.'-'.$dd.'-'.$yy;
												
											}
											else{
											$patient_confirmation_id_arr=array(11,12,17,18,19,20);
												$srr_string = ',10,11,19,';
												//print '/'.$f.'/'.$srr_string.'::';
											   if(preg_match('/,'.$f.',/',$srr_string))
											   {
											     $patient_confirmation_id='patient_confirmation_id';
												//print '<br>';
											   }
											   else
											   {
											     $patient_confirmation_id= 'confirmation_id';
											   }
												$qry = "select form_status from ".$formnameArr[$f]." where $patient_confirmation_id='$patientConfirmationId'";
												$qryst = @imw_query($qry);
												
												$n=@imw_num_rows($qryId);
												
												(list($form_status) = @imw_fetch_array($qryst));
												if($form_status == 'completed'){
													$img = 'check_mark16.jpg';
													 $m++;
													
												}
												
												elseif($form_status == 'not completed'){
													$img = 'red_flag.jpg';
													$u++;
												}
												else
												{
													$img = 'tpixelwhite.jpg';
												}
												
												$patientData = '
												<img  src="tpixelwhite.jpg" width="45" height="15">
											    <img  src="'.$img.'" width="15" height="15">';
												
												
											}			
											
											if($t<$row)//used for count of completed and uncompleted forms
											{			
										
														
											$tdData .= '<td width="20%" align="center">'.$patientData.'</td>';
											
												if($img=='check_mark16.jpg' && $formnameArr[$f]=='surgery_consent_form' )
												{
													$surgeryComplete++;
												}
												elseif($img=='red_flag.jpg' && $formnameArr[$f]=='surgery_consent_form')
												{
													$surgeryincomplete++;
													
												}
												elseif($img=='check_mark16.jpg' && $formnameArr[$f]=='hippa_consent_form' )
												{
													$hippaComplete++;
												}
												elseif($img=='red_flag.jpg' && $formnameArr[$f]=='hippa_consent_form')
												{
													$hippaincomplete++;
												}
												elseif($img=='check_mark16.jpg' && $formnameArr[$f]=='benefit_consent_form' )
												{
													$benefitComplete++;
												}
												elseif($img=='red_flag.jpg' && $formnameArr[$f]=='benefit_consent_form')
												{
													$benefitincomplete++;
												}
												elseif($img=='check_mark16.jpg' && $formnameArr[$f]=='insurance_consent_form ' )
												{
													$insuranceComplete++;
												}
												elseif($img=='red_flag.jpg' && $formnameArr[$f]=='insurance_consent_form ')
												{
													$insuranceincomplete++;
												}
												elseif($img=='check_mark16.jpg' && $formnameArr[$f]=='preophealthquestionnaire' )
												{
													$healthComplete++;
												}
												elseif($img=='red_flag.jpg' && $formnameArr[$f]=='preophealthquestionnaire')
												{
													$healthincomplete++;
												}
												elseif($img=='check_mark16.jpg' && $formnameArr[$f]=='preopnursingrecord' )
												{
													$prenurseComplete++;
												}
												elseif($img=='red_flag.jpg' && $formnameArr[$f]=='preopnursingrecord')
												{
													$prenurseincomplete++;
												}
												elseif($img=='check_mark16.jpg' && $formnameArr[$f]=='postopnursingrecord' )
												{
													$postnurseComplete++;
												}
												elseif($img=='images/red_flag.jpg' && $formnameArr[$f]=='postopnursingrecord')
												{
													$postnurseincomplete++;
												}
												elseif($img=='check_mark16.jpg' && $formnameArr[$f]=='preopphysicianorders')
												{
													$prephysicianComplete++;
												}
												elseif($img=='red_flag.jpg' && $formnameArr[$f]=='preopphysicianorders')
												{
													$prephysicianincomplete++;
												}
												elseif($img=='images/check_mark16.jpg' && $formnameArr[$f]=='postopphysicianorders')
												{
													$postphysicianComplete++;
												}
												elseif($img=='red_flag.jpg' && $formnameArr[$f]=='postopphysicianorders')
												{
													$postphysicianincomplete++;
												}
												elseif($img=='check_mark16.jpg' && $formnameArr[$f]=='localanesthesiarecord' )
												{
													 $localanesComplete++;
												}
												elseif($img=='red_flag.jpg' && $formnameArr[$f]=='localanesthesiarecord')
												{
													$localanesincomplete++;
												}
												elseif($img=='check_mark16.jpg' && $formnameArr[$f]=='preopgenanesthesiarecord ' )
												{
													$preopgenanesComplete++;
												}
												elseif($img=='red_flag.jpg' && $formnameArr[$f]=='preopgenanesthesiarecord')
												{
													$preopgenanesincomplete++;
												}
												elseif($img=='check_mark16.jpg' && $formnameArr[$f]=='genanesthesiarecord' )
												{
													$genanesComplete++;
												}
												elseif($img=='red_flag.jpg' && $formnameArr[$f]=='genanesthesiarecord')
												{
													 $genanesincomplete++;
												}
												elseif($img=='check_mark16.jpg' && $formnameArr[$f]=='genanesthesianursesnotes')
												{
													$nursesnoteComplete++;
												}
												elseif($img=='red_flag.jpg' && $formnameArr[$f]=='genanesthesianursesnotes')
												{	
													 $nursesnoteincomplete++;
												}
												elseif($img=='check_mark16.jpg' && $formnameArr[$f]=='operatingroomrecords')
												{
													$operatingroomComplete++;
												}
												elseif($img=='red_flag.jpg' && $formnameArr[$f]=='operatingroomrecords')
												{
													 $operatingroomincomplete++;
												}
												//operativereport 
												elseif($img=='check_mark16.jpg' && $formnameArr[$f]=='operativereport')
												{
													$operativereportComplete++;
												}
												elseif($img=='red_flag.jpg' && $formnameArr[$f]=='operativereport')
												{
													 $operativereportincomplete++;
												}
												elseif($img=='check_mark16.jpg' && $formnameArr[$f]=='dischargesummarysheet')
												{
													$dischargeComplete++;
												}
												elseif($img=='red_flag.jpg' && $formnameArr[$f]=='dischargesummarysheet')
												{
													$dischargeincomplete++;
												}
												elseif($img=='check_mark16.jpg' && $formnameArr[$f]=='patient_instruction_sheet')
												{
													$instructionComplete++;
												}
												elseif($img=='red_flag.jpg' && $formnameArr[$f]=='patient_instruction_sheet')
												{
													$instructionincomplete++;
												}
												elseif($img=='check_mark16.jpg' && $formnameArr[$f]=='amendment')
												{
													$amendmentComplete++;
												}
												elseif($img=='red_flag.jpg' && $formnameArr[$f]=='amendment')
												{
													$amendmentincomplete++;
												}
												
												
											
											}//endif
											
										}			
															
										$rowsData .= '
											<tr>
												<td height="35" style=" font-weight:bold;" width="40%"><b>'.$formnamedispArr[$f].'</b></td>
											'.$tdData.'
										
											</tr>';
									    	
									}
								$img_logo = showThumbImages('../html2pdf/white.jpg',170,50);
								 $imgheight= $img_logo[2]+8;
								 $imgwidth= $img_logo[1]+8;
									$tableRows.= '
									<table width="100%"  border="0" cellpadding="3" cellspacing="0" class="text_print" bgcolor="#FFFFFF">
	 									<tr height="30"  bgcolor="#cd532f" >
											<td  align="left"   valign="bottom" >
											 <font color="#FFFFFF"><b>'.$name.'<br>'.$address.'</b></font>
											</td>
											<td  align="right" height="'.$imgheight.'" width="'.$imgwidth.'">'.$img_logo[0].'</td>
										</tr>
										</table>
									<table width="100%">
									  <tr bgcolor="#FFFFFF"><td height="15">&nbsp;</td></tr>
											<tr height="22"  bgcolor="#F1F4F0">
											<td height="25"  valign="top" align="left"><b>Operator&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$username.'</td>
											<td colspan="2" width="20%">&nbsp;</td>
											<td  align="right" valign="top"><span >Date: </span><span class="red_txt">'.$curdate.'</b></td>
										</tr>
										</table>
												<table border="0" width="100%" cellspacing="4" cellpadding="4" align="center" >
													'.$rowsData.'
												</table>
											
										<tr><td colspan="4">&nbsp;</td></tr>
										<tr><td colspan="4">&nbsp;</td></tr>
										<tr height="400"><td colspan="4"></td></tr></table><newpage>
									';
									}
								
								
								$complete=$m;
								$uncomplete=$u;
								$completeForm=$surgeryComplete+$hippaComplete+$benefitComplete+$insuranceComplete+$healthComplete+$prenurseComplete+$postnurseComplete+$prephysicianComplete+$postphysicianComplete+$localanesComplete+$preopgenanesComplete+$genanesComplete+$nursesnoteComplete+$operatingroomComplete+$dischargeComplete+$instructionComplete+$amendmentComplete;
								$incompleteForm=$surgeryincomplete+$hippaincomplete+$benefitincomplete+$insuranceincomplete+$healthincomplete+$prenurseincomplete+$postnurseincomplete+$prephysicianincomplete+$postphysicianincomplete+$localanesincomplete+$preopgenanesincomplete+$genanesincomplete+$nursesnoteincomplete+$operatingroomincomplete+$dischargeincomplete+$instructionincomplete+$amendmentincomplete;
							//StartIf	
								if($row>0)
								{
								$img_logo = showThumbImages('../html2pdf/white.jpg',170,50);
								 $imgheight= $img_logo[2]+8;
								 $imgwidth= $img_logo[1]+8;
								 $tableRows.='
								 <table width="100%"  border="0" cellpadding="3" cellspacing="0" class="text_print" bgcolor="#FFFFFF">
	 
								<tr height="30"  bgcolor="#cd532f" >
									<td  align="left"   valign="bottom">
									 <font color="#FFFFFF"><b>'.$name.'<br>'.$address.'</b></font>
									</td>
									<td  align="right" height="'.$imgheight.'" width="'.$imgwidth.'">'.$img_logo[0].'</td>
								</tr>
								</table>
								<table width="100%">
								  <tr bgcolor="#FFFFFF"><td height="15">&nbsp;</td></tr>
											<tr height="22"  bgcolor="#F1F4F0">
											<td valign="top" height="25" colspan="2" align="left"><b>Operator&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$username.'</span></td>
											<td align="right"   valign="top">Date: '.$curdate.'</b></td>
										</tr>
											
											
												<tr style="font:vetrdana; font-weight:bold" >
													<td height="36" align="left" width="30%" ><b>Form-Name</td>
													<td align="center" width="30%">Complete</td>
													<td align="center" width="30%">Incomplete</b></td>
												</tr>
												<tr >
													<td height="36" align="left" ><b>Surgery-Consent-Form</b></td>
													<td align="center">'.$surgeryComplete.'</td>
													<td align="center">'.$surgeryincomplete.'</td>
												</tr>
												<tr >
													<td height="36" align="left" style="font-weight:bold"><b>Hippa-Consent-Form</b></td>
													<td align="center">'.$hippaComplete.'</td>
													<td align="center">'.$hippaincomplete.'</td>
												</tr>
												<tr >
													<td height="36" align="left" style="font-weight:bold"><b>Benefit-Consent-Form</b></td>
													<td align="center">'.$benefitComplete.'</td>
													<td align="center">'.$benefitincomplete.'</td>
												</tr>
												<tr >
													<td height="36" align="left" style="font-weight:bold"><b>Insurance-Consent-Form</b></td>
													<td align="center">'.$insuranceComplete.'</td>
													<td align="center">'.$insuranceincomplete.'</td>
												</tr>
												<tr >
													<td height="36" align="left" style="font-weight:bold"><b>Pre-Op-Healthquestionnaire</b></td>
													<td align="center">'.$healthComplete.'</td>
													<td align="center">'.$healthincomplete.'</td>
												</tr>
												<tr >
													<td height="36" align="left" style="font-weight:bold"><b>Pre-Op-Nursing-Record</b></td>
													<td align="center">'.$prenurseComplete.'</td>
													<td align="center">'.$prenurseincomplete.'</td>
												</tr>
												<tr >
													<td height="36" align="left" style="font-weight:bold"><b>Post-Op-Nursing-Record</b></td>
													<td align="center">'.$postnurseComplete.'</td>
													<td align="center">'.$postnurseincomplete.'</td>
												</tr>
												<tr >
													<td height="36" align="left" style="font-weight:bold"><b>Pre-Op-Physician-Orders</b></td>
													<td align="center">'.$prephysicianComplete.'</td>
													<td align="center">'.$prephysicianincomplete.'</td>
												</tr>
												<tr >
													<td height="36" align="left" style="font-weight:bold"><b>Post-Op-Physician-Orders</b></td>
													<td align="center">'.$postphysicianComplete.'</td>
													<td align="center">'.$postphysicianincomplete.'</td>
												</tr>
												<tr >
													<td height="36" align="left" style="font-weight:bold"><b>Local-Anesthesia-Record</b></td>
													<td align="center">'.$localanesComplete.'</td>
													<td align="center">'.$localanesincomplete.'</td>
												</tr>
												<tr >
													<td height="36" align="left" style="font-weight:bold"><b>Pre-Op-General-Anesthesia-Record</b></td>
													<td align="center">'.$preopgenanesComplete.'</td>
													<td align="center">'.$preopgenanesincomplete.'</td>
												</tr>
												<tr >
													<td height="36" align="left" style="font-weight:bold"><b>General-Anesthesia-Record</b></td>
													<td align="center">'.$genanesComplete.'</td>
													<td align="center">'.$genanesincomplete.'</td>
												</tr>
												<tr >
													<td height="36" align="left" style="font-weight:bold"><b>General-Anesthesia-Nurses-Notes</b></td>
													<td align="center">'.$nursesnoteComplete.'</td>
													<td align="center">'.$nursesnoteincomplete.'</td>
												</tr>
												<tr >
													<td height="36" align="left" style="font-weight:bold"><b>Operating-Room-Records</b></td>
													<td align="center">'.$operatingroomComplete.'</td>
													<td align="center">'.$operatingroomincomplete.'</td>
												</tr>
												operativereportComplete
												<tr >
													<td height="36" align="left" style="font-weight:bold"><b>Operative Report</b></td>
													<td align="center">'.$operativereportComplete.'</td>
													<td align="center">'.$operativereportincomplete.'</td>
												</tr>
												<tr >
													<td height="36" align="left" style="font-weight:bold"><b>Discharge-Summary-Sheet</b></td>
													<td align="center">'.$dischargeComplete.'</td>
													<td align="center">'.$dischargeincomplete.'</td>
												</tr>
												<tr >
													<td height="36" align="left" style="font-weight:bold"><b>Patient-Instruction-Sheet</b></td>
													<td align="center">'.$instructionComplete.'</td>
													<td align="center">'.$instructionincomplete.'</td>
												</tr>
												<tr >
													<td height="36" align="left" style="font-weight:bold"><b>Amendment</b></td>
													<td align="center">'.$amendmentComplete.'</td>
													<td align="center">'.$amendmentincomplete.'</td>
												</tr>
												<tr >
													<td height="36" align="left" style="font-weight:bold"><b>Total</b></td>
													<td align="center">'.$completeForm.'</td>
													<td align="center">'.$incompleteForm.'</td>
												</tr>
												<tr><td colspan="3">&nbsp;</td></tr>
												<tr><td colspan="3">&nbsp;</td></tr>
												<tr><td colspan="3">&nbsp;</td></tr>
												<tr><td colspan="3">&nbsp;</td></tr>
												<tr><td colspan="3">&nbsp;</td></tr>
												<tr><td colspan="3">&nbsp;</td></tr>
												<tr><td colspan="3">&nbsp;</td></tr>
												<tr><td colspan="3">&nbsp;</td></tr>
												<tr><td height="86" colspan="3">&nbsp;</td></tr>
												
											</table>
										</td>			
									</tr>	
								</table>';
							  }	
								
								if($row==0)
								{
							    $img_logo = showThumbImages('../html2pdf/white.jpg',170,50);
								 $imgheight= $img_logo[2]+8;
								 $imgwidth= $img_logo[1]+8;
								$msg="No Record Found!";
								$tableRows.='
								  <table width="100%"  border="0" cellpadding="3" cellspacing="0" class="text_print" bgcolor="#FFFFFF">
	 
								<tr height="30"  bgcolor="#cd532f" >
									<td  align="left"   valign="bottom">
									 <font color="#FFFFFF"><b>'.$name.'<br>'.$address.'</b></font>
									</td>
									<td  align="right" height="'.$imgheight.'" width="'.$imgwidth.'">'.$img_logo[0].'</td>
								</tr>
								</table>
								<table width="100%">
								  <tr bgcolor="#FFFFFF"><td height="15">&nbsp;</td></tr>
											<tr height="22"  bgcolor="#F1F4F0">
											<td valign="top" height="25" colspan="2" align="left"><b>Operator&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$username.'</span></td>
											<td align="right"  valign="top">Date: '.$curdate.'</b></td>
										</tr>
								 <tr><td height="15">&nbsp;</td></tr>
											<tr height="22">
											<td valign="top" height="125" colspan="2" align="right">'.$msg.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
											<td align="left"  valign="top">&nbsp;</b></td>
										</tr>	
										<tr><td>&nbsp;&nbsp;</td></tr>
										<tr><td>&nbsp;&nbsp;</td></tr>	
									
										</table>';
								}
								
$filename="../testPdf.html";
@unlink($filename);
$fileOpen = fopen($filename,'w+');
$filePut = fwrite($fileOpen,$tableRows);
fclose($fileOpen);
//$URL='http://'. $_SERVER['HTTP_HOST'].'/surgerycenter/testPdf.html';
//echo $tableRows;
?>
<script language="javascript">
	function submitfn()
	{
		document.printFrm.submit();
	}
</script>
<table bgcolor="#FFFFFF"  style="font:vetrdana; font-size:14;" width="100%" height="100%">
	<tr>
		<td width="100%" align="center" valign="middle"><img src="../../surgerycenter/images/ajax-loader.gif"></td> 
	</tr>
</table> 

<body>
<form name="printFrm" action="../html2pdf/index.php?AddPage=P"  method="post">

</form>		
<script type="text/javascript">
	submitfn();
</script>
</body>