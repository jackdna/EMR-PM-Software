<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include_once("common/conDb.php");
include("common_functions.php");
//include("common/linkfile.php");
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;

function dateDiffHeader($dformat, $endDate, $beginDate){
	$date_parts1=explode($dformat, $beginDate);
	$date_parts2=explode($dformat, $endDate);
	$start_date=gregoriantojd($date_parts1[0], $date_parts1[1], $date_parts1[2]);
	$end_date=gregoriantojd($date_parts2[0], $date_parts2[1], $date_parts2[2]);
	return $end_date - $start_date;
}
function showThumbImages($fileName='white.jpg',$targetWidth=500,$targetHeight=70)
{ 
	if(file_exists($fileName))
	{ 
		$img_size=getimagesize('html2pdf/white.jpg');
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

/*
$pConfId = $_REQUEST['pConfId'];
if(!$pConfId) {
	$pConfId = $_SESSION['pConfId']; 
}
*/
$surgeonId = $_REQUEST['surgeonId'];
$surgeryDateTemp = $_REQUEST['surgery_date'];
list($surgeryMonth,$surgeryDay,$surgeryYear) = explode('-',$surgeryDateTemp);
$surgeryDate = $surgeryYear.'-'.$surgeryMonth.'-'.$surgeryDay;
//$surgeonId = '177';
//$surgeryDate = '2009-04-02';
$get_patientConfirm_tblQry = "SELECT * FROM `patientconfirmation` WHERE `surgeonId` = '".$surgeonId."' AND dos='".$surgeryDate."' ORDER BY patientConfirmationId";
$get_patientConfirm_tblRes = imw_query($get_patientConfirm_tblQry) or die(imw_error());
$get_patientConfirm_tblNumRow = imw_num_rows($get_patientConfirm_tblRes);
if($get_patientConfirm_tblNumRow>0) {
	$table='';
	while($get_patientConfirm_tblRow = imw_fetch_array($get_patientConfirm_tblRes)) {
		$pConfId = $get_patientConfirm_tblRow["patientConfirmationId"];
		$patient_id = $get_patientConfirm_tblRow["patientId"];

		//$pConfId='928';
		//$patient_id='858';
		include "test_header_print.php";
		
		//VIEW RECORD FROM DATABASE
			$ViewoperativeQry = "select * from `operativereport` where confirmation_id='".$pConfId."'";
			$ViewoperativeRes = imw_query($ViewoperativeQry) or die(imw_error()); 
			$ViewoperativeNumRow = imw_num_rows($ViewoperativeRes);
			$ViewoperativeRow = imw_fetch_array($ViewoperativeRes); 
			$operative_surgeon_sign = $ViewoperativeRow["signature"];
			$operative_data = stripslashes($ViewoperativeRow["reportTemplate"]);
			$form_status = $ViewoperativeRow["form_status"];
			
			$signSurgeon1Id = $ViewoperativeRow["signSurgeon1Id"];
			$signSurgeon1FirstName = $ViewoperativeRow["signSurgeon1FirstName"];
			$signSurgeon1MiddleName = $ViewoperativeRow["signSurgeon1MiddleName"];
			$signSurgeon1LastName = $ViewoperativeRow["signSurgeon1LastName"];
			$signSurgeon1Status = $ViewoperativeRow["signSurgeon1Status"];
			
			//FETCH DATA FROM OPEARINGROOMRECORD TABLE
			$diagnosisQry=imw_query("select preOpDiagnosis , postOpDiagnosis from operatingroomrecords where confirmation_id=$pConfId");
			$diagnosisRes=@imw_fetch_array($diagnosisQry);	
			$preopdiagnosis= $diagnosisRes["preOpDiagnosis"];
			$postopdiagnosis= $diagnosisRes["postOpDiagnosis"];
			if(trim($postopdiagnosis)=="") {
				$postopdiagnosis = $preopdiagnosis;
			}
			// END FETCH DATA FROM OPEARINGROOMRECORD TABLE
			
			//REPLACE FIELD IN PARENTHESIS WITH ACTUAL VALUE 			
				$operative_data= str_replace("{PATIENT FIRST NAME}","<b>".$Operative_patientName_tblRow["patient_fname"]."</b>",$operative_data);
				$operative_data= str_replace("{MIDDLE INITIAL}","<b>".$Operative_patientName_tblRow["patient_mname"]."</b>",$operative_data);
				$operative_data= str_replace("{LAST NAME}","<b>".$Operative_patientName_tblRow["patient_lname"]."</b>",$operative_data);
				$operative_data= str_replace("{DOB}","<b>".$Operative_patientNameDob."</b>",$operative_data);
				$operative_data= str_replace("{DOS}","<b>".$Operative_patientConfirmDos."</b>",$operative_data);
				$operative_data= str_replace("{SURGEON NAME}","<b>".$Operative_patientConfirm_tblRow["surgeon_name"]."</b>",$operative_data);
				$operative_data= str_replace("{SITE}","<b>".$Operative_patientConfirmSite."</b>",$operative_data);
				$operative_data= str_replace("{PROCEDURE}","<b>".$Operative_patientConfirmPrimProc."</b>",$operative_data);
				$operative_data= str_replace("{SECONDARY PROCEDURE}","<b>".$Operative_patientConfirmSecProc."</b>",$operative_data);
				$operative_data= str_replace("{PRE-OP DIAGNOSIS}","<b>".$preopdiagnosis."</b>",$operative_data);
				$operative_data= str_replace("{POST-OP DIAGNOSIS}","<b>".$postopdiagnosis."</b>",$operative_data);
				$operative_data= str_replace("{DATE}","<b>".date('m-d-Y')."</b>",$operative_data);
				$operative_data= str_replace("{TIME}","<b>".date('m-d-Y')."</b>",$operative_data);
			//END REPLACE FIELD IN PARENTHESIS WITH ACTUAL VALUE 	
		//END VIEW RECORD FROM DATABASE
		
		if(trim($operative_data) || $signSurgeon1Id!='0') {
			$table.=$head_table."<br>";	
			$table.='<table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
										<tr>
											<td colspan="3"><b>Operative Report</b></td>
										</tr>';
								if($operative_data) {
									$table.='<tr height="300" bgcolor="#FFFFFF">
											<td colspan="3" height="22"  class="text_10" valign="top">
												 '.strip_tags(nl2br($operative_data),'<br> <img>').'
											</td>
										</tr>';
								}			
									$table.='<tr><td height="70" colspan="3">&nbsp;</td></tr>
								</table>';
			if($signSurgeon1Id!='0') {		 
					 //if($signSurgeon1LastName!="" || $signSurgeon1FirstName!='') {		  
						$table.='<table width="97%" border="0" cellpadding="0" cellspacing="0">
								   <tr valign="top">
										<td colspan="3">
											<b>Surgeon:</b>: Dr.&nbsp;&nbsp; '.$signSurgeon1LastName.' '.$signSurgeon1FirstName.' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<b>Electronically Signed</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :'.$signSurgeon1Status.'
										</td>
									</tr>
								</table>';	
					// }
			}
			//$table.='</table>';		
			$table.='<newpage>';
		}
		
		//start discharge summary	
		$Qry="select * from dischargesummarysheet where confirmation_id= '".$pConfId."'";
		$recordQry=imw_query($Qry);
		$i = 0;
		$DischargeResult=imw_fetch_array($recordQry);
		
		$dischargeSummaryFormStatus = $DischargeResult["form_status"];
		
		$date= $DischargeResult['signSurgeon1DateTime']; 
		$date_surgeon=explode(' ',$date);
		$date_sign=explode('-',$date_surgeon[0]);
		$date_surgeon_sign= $date_sign[1].'/'.$date_sign[2].'/'.$date_sign[0];
		
		$procedures_code_list= $DischargeResult['procedures_code'];
		$diag_ids_list=$DischargeResult['diag_ids'];
		$diag_ids=explode(',',$diag_ids_list);
		$diagids_length=count($diag_ids);
		$procedures_codes= explode(',',$procedures_code_list); 
		$procedures_list= $DischargeResult['procedures_name'];
		$procedures =explode(',',$procedures_list);
		$procedures_length= count($procedures);
		//print_r($DischargeResult);
		$surgeon=$DischargeResult['signSurgeon1LastName'].','.$DischargeResult['signSurgeon1FirstName'];
		$table2=$head_table."<br>";
		$table2.='<table>
				<tr>
				  
					<td colspan="3" align="center"><b>Discharge Summary Sheet</b></td>
				</tr>
				<tr>
					
					<td colspan="3"><b>Procedures</b></td>
				</tr>
				';
		for($len=0;$len<$procedures_length;$len++)
		{
		$procedureQry="select * from procedures where procedureId = $procedures_codes[$len]";
		$proced= imw_query($procedureQry);
		$procedurelisting=@imw_fetch_array($proced);
		//echo "<pre>";
		//print_r($procedurelisting);
		$procedurecode[] =$procedurelisting['code'];
		$procedurename[]= $procedurelisting['name'];
		
		$table2.=' 
				   <tr>
					  
					   <td width="20%" align="left">'.$procedurecode[$len].'</td>
					   <td width="45%" align="left">'.$procedurename[$len].'</td>
					   <td width="30%" align="center">Yes</td>
				   </tr>
				   
				   ';
				   }
		$table2.='<tr>
					<td height="5" colspan="3">&nbsp;&nbsp;</td>
				  </tr>
				
				';	
		if($diag_ids_list!='')
		{ 
		  $table2.='<tr>
						<td colspan="3"><b>Diagnosis</b></td>
					</tr>
				   <tr>';
		}		   			   
		for($c=0;$c<$diagids_length;$c++)
		{
		  $diagQry ="select * from diagnosis_tbl where diag_id= $diag_ids[$c]";
		  $resDiag=imw_query( $diagQry);
		  $diagnosis=@imw_fetch_array($resDiag);
		  $diagcodes=$diagnosis['diag_code'];
		  $diagcodeslist=explode(',',$diagcodes);
		  $diagcode[]= $diagcodeslist[0];
		  $diagdesc[]= $diagcodeslist[1];
		if($diagcodes!='')
		{ 
		  $table2.='<tr>
					   <td width="20%" align="left">'.$diagcode[$c].'</td>
					   <td width="58%" align="left">'.$diagdesc[$c].'</td>
					   <td width="20%" align="center">Yes</td>
				   </tr>';
		}		   
				   
		}	
		$table2.='<tr>
					<td height="5" colspan="3">&nbsp;&nbsp;</td>
				  </tr>';   
		if($DischargeResult['otherMiscellaneous']!='')
		{
		$table2.='<tr>
				  
				 <td>Other</td>
				 <td>'.stripslashes($DischargeResult['otherMiscellaneous']).'</td>
				 <td></td>
				 </tr>';
				} 
		$table2.='<tr>
					<td height="5" colspan="3">&nbsp;&nbsp;</td>
				  </tr>';	
		if($DischargeResult['other1']!='')
		
		{		
		$table2.='<tr>
				 <td>Other1</td>
				 <td>'.stripslashes($DischargeResult['other1']).'</td>
				 <td></td>
				 </tr>';
		}
		$table2.='<tr>
					<td height="5" colspan="3">&nbsp;&nbsp;</td>
				  </tr>';
		if($DischargeResult['other2']!='')
		{		 
		$table2.='<tr>
				 <td>Other2</td>
				 <td>'.stripslashes($DischargeResult['other2']).'</td>
				 <td></td>
				 </tr>';
		}
		if($DischargeResult['other1']!='')
		{		
		$table2.='<tr>
				 <td>Comment</td>
				 <td>'.stripslashes($DischargeResult['comment']).'</td>
				 <td></td>
				 </tr>';
		}
		$table2.='<tr>
					<td height="5" colspan="3">&nbsp;&nbsp;</td>
				  </tr>';
		 
		
		$table2.='<tr>
					<td height="5" colspan="3">&nbsp;&nbsp;</td>
				  </tr>';		
		 if($DischargeResult['surgeon_knowledge']!='')
		{
		$table2.='<tr>
				  <td colspan="2">I certify that the diagnosis and procedures performed are accurate and complete to the best of my knowledge</td>
				  <td align="center">'.$DischargeResult['surgeon_knowledge'].'</td>
				 </tr>
				  ';
		}
		$table2.='<tr>
					<td height="20" colspan="3">&nbsp;&nbsp;</td>
				  </tr>';
		if($DischargeResult['signSurgeon1Status'])
		{	
		$table2.='<tr>	 
					<td>Surgeon:'.$surgeon.'</td>
					<td>Surgeon Signature  &nbsp;&nbsp;'.$DischargeResult['signSurgeon1Status'].'</td>
					<td>Date:'.$date_surgeon_sign.' </td>
					
				 </tr>';
		}
				 
		$table2.='</table>';
		$table2.='<newpage>';
		if($dischargeSummaryFormStatus=='completed' || $dischargeSummaryFormStatus=='not completed') {
			$table.=$table2;
		}	
		/******************************End Discharge Summary Sheet***************************/
		
	}	
	//CODE TO PRINT PDF
		
		$fileOpen = fopen('testPdf.html','w+');
		$filePut = fputs(fopen('testPdf.html','w+'),$table);
		fclose($fileOpen);
		?>
		<script language="javascript">
			function submitfn()
			{
				document.printFrm.submit();
			}
		</script>
		<table bgcolor="#FFFFFF"  style="font:vetrdana; font-size:14;" width="100%" height="100%">
			<tr>
				<td width="100%" align="center" valign="middle"><img src="images/ajax-loader.gif"></td> 
			</tr>
		</table>
		
		
		<form name="printFrm"  action="html2pdfnew/index.php?AddPage=P" method="post">
		</form>		
		<script type="text/javascript">
			submitfn();
		</script>
	
	<?php
	
	//END CODE TO PRINT PDF
}	else {
?>
		<table bgcolor="#FFFFFF"  style="font:vetrdana; font-size:14;" width="100%" height="100%">
			<tr>
				<td width="100%" align="center" valign="middle">No Record Found</td> 
			</tr>
		</table>

<?php
}
?>