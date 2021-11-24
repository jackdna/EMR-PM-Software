<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
	session_start();
	include_once("common/conDb.php");
	include_once("common/commonFunctions.php");
	include_once("admin/classObjectFunction.php");
		
function getPatientDetail($patientId){
	if($patientId){
		$qryPatientData="SELECT patient_fname,patient_mname,patient_lname FROM patient_data_tbl where patient_id=".$patientId."";
		$resultPatientData=imw_query($qryPatientData)or die(imw_error());
		$rowPatientData=imw_fetch_assoc($resultPatientData);
		$patientName=$rowPatientData['patient_lname'].", ".$rowPatientData['patient_fname']." ".$rowPatientData['patient_mname'];
		return $patientName;
	}
}	
function getAllNarcoticsMeds(){
	$naroticsMedicationArr=array();
	$catgoryName="Narcotics";
	$qryAllMedCatgory=imw_query("SELECT categoryId FROM preopmedicationcategory where categoryName='".$catgoryName."'");
	$rowCategoryId=imw_fetch_assoc($qryAllMedCatgory);
	$narcoMedsID=$rowCategoryId['categoryId'];
	$qryNarcoMeds="SELECT preOpMedicationOrderId,medicationName FROM preopmedicationorder WHERE mediCatId =".$narcoMedsID." group by medicationName";
	$resNarcoMeds=imw_query($qryNarcoMeds)or die(imw_error());
	if(imw_num_rows($resNarcoMeds)>0){
		$narcotics_medication_name="";
		$narcotics_medication_id="";
		while($rowNarcoMeds=imw_fetch_assoc($resNarcoMeds)){
			$narcotics_medication_id=$rowNarcoMeds['preOpMedicationOrderId'];
			$narcotics_medication_name=$rowNarcoMeds['medicationName'];
			$naroticsMedicationArr[$narcotics_medication_id]=$narcotics_medication_name;
		}	
	}
	return $naroticsMedicationArr;
}
$narcoticsMedsArr=getAllNarcoticsMeds();	
?>
<html>
	<head>
		<title>Surgerycenter EMR</title>
	</head>
	<?php
		$objManageData 		= new manageData;
		$getDateOfService	= $_REQUEST['dateOS'];
		$dateOfService 		= $objManageData->changeDateYMD($getDateOfService);
		$printdateOfService = $objManageData->changeDateMDY($dateOfService);
		$qryNarcoticsLogForCheck="select log_id from narcotics_log_tbl where date_of_service='".$dateOfService ."' and delete_status!='1'";
		$resNarcoticsLogCheck=imw_query($qryNarcoticsLogForCheck)or die(imw_error());
		$numofRowNarcoticsLog=imw_num_rows($resNarcoticsLogCheck);
		$surgerycenter_name=getData("name","surgerycenter","surgeryCenterId","1");//GET SURGERY CENTER NAME
		if($numofRowNarcoticsLog>0){
			$qryNarcoticsTbl="select * from narcotics_log_tbl where date_of_service='".$dateOfService."' and delete_status!='1' order by patient_name";
			$rowNarcoticsTbl=imw_query($qryNarcoticsTbl);
			while($rowNorticsTbl=imw_fetch_assoc($rowNarcoticsTbl)){
				$patient_Id= $rowNorticsTbl['patient_id'];
				$patientConfirmationId= $rowNorticsTbl['patient_confirmation_id'];
				$med_id=$rowNorticsTbl['narotics_medication_id'];
				$patient_md_crna=$rowNorticsTbl['md_crna'];
				$log_id_Id= $rowNorticsTbl['log_id'];
				$detail_arr[$patient_Id][$med_id]=$rowNorticsTbl;
				$detail_pat_arr[$patient_Id]=$patient_Id;
				$detail_conf_arr[$patient_Id]=$patientConfirmationId;
			}
			$counter=1;
			$table='<table cellpadding="0" cellspacing="0" style="width:700px;border-left:1px solid #000000; border-top:1px solid #000000; font-size:10px;">';
			$table.='
					<tr>
						<td colspan="2" style="width:700px;text-align:center;height:25px;border-right:1px solid #000000; border-bottom:1px solid #000000;font-size:14px;"><strong><u>Narcotics Log Sheet</u></strong></td>
					</tr>	
					<tr>
						<td colspan="2" style="vertical-align:middle;height:22px;text-align:center;border-right:1px solid #000000;font-size:12px;border-bottom:1px solid #000000; width:700px;"><b>'.$surgerycenter_name.' - ANESTHESIA DEPARTMENT CONTROLLED DRUG ADMINISTRATOR RECORD</b></td>
					</tr>
					<tr>	
						<td style="width:300px;height:20px;vertical-align:middle;text-align:center;font-size:12px;border-bottom:1px solid #000000;">MD&nbsp;/&nbsp;CRNA : '.$patient_md_crna.'</td>
						<td style="width:300px;height:20px;vertical-align:middle;text-align:center;font-size:12px;border-right:1px solid #000000;border-bottom:1px solid #000000;">DATE : '.$printdateOfService.'</td>	
					</tr>
					<tr>
						<td colspan="2" style="width:700px;vertical-align:top;text-align:left;border-right:1px solid #000000;border-bottom:1px solid #000000;" >
							<table style="font-size:10px;" cellspacing="0" cellpadding="0">	
								<tr>
									<td style="width:10px;vertical-align:top;height:20px;border-bottom:1px solid #000000;border-right:1px solid #000000;font-weight:bold;">No.</td>
									<td style="width:50px;vertical-align:top;height:20px;border-bottom:1px solid #000000;border-right:1px solid #000000;font-weight:bold;">Patient Name</td>';
								foreach($narcoticsMedsArr as  $narcoMedsTitleName){
									$table.='<td style="vertical-align:top;width:60px;font-weight:bold;height:20px;border-bottom:1px solid #000000;border-right:1px solid #000000;">'.$narcoMedsTitleName.'</td>';
								}	
									$table.='<td style="width:50px;vertical-align:top; font-weight:bold;height:20px;border-bottom:1px solid #000000;border-right:1px solid #000000;">Signature</td>
					  						 <td  style="width:50px;vertical-align:top;font-weight:bold;height:20px;border-bottom:1px solid #000000;border-right:1px solid #000000;">Co-Signature</td>';
					$table.='  </tr>';
						foreach($detail_pat_arr as $pat_det => $pat_id_val){
							$getPatientName="";
							$patient_Id	= $pat_id_val;
							$getPatientName=getPatientDetail($patient_Id);
							$patient_Conf_Id=$detail_conf_arr[$patient_Id];

						$table.='
								<tr>
									<td style="border-right:1px solid #000000;vertical-align:middle;text-align:center;height:20px;border-bottom:1px solid #000000;">'.$counter.'</td>
									<td style="vertical-align:middle;font-size:10px;height:20px;border-bottom:1px solid #000000;border-right:1px solid #000000; ">'.$getPatientName.' </td>';
								foreach($narcoticsMedsArr as $keyMedId => $narcoMedsTitleName){
									$patient_Narcotics_Medication_Id=$detail_arr[$patient_Id][$keyMedId]['narotics_medication_id'];
									$patient_Narcotics_Medication_Descripation=$detail_arr[$patient_Id][$keyMedId]['narcotics_medication_descripation'];
									$patient_Narcotics_Log_Id=$detail_arr[$patient_Id][$keyMedId]['log_id'];
									if(($patient_Narcotics_Medication_Id)==$keyMedId){
										$table.='<td style="border-right:1px solid #000000;height:20px;vertical-align:middle;border-bottom:1px solid #000000;padding-left:5px;">'.$patient_Narcotics_Medication_Descripation.'</td>';
									}else{
										$table.='<td style="border-right:1px solid #000000;height:20px;border-bottom:1px solid #000000;">&nbsp;</td>';	
									}	
								}	
									$table.='<td style="border-right:1px solid #000000;height:20px;border-bottom:1px solid #000000;">&nbsp;</td>
											 <td style="height:20px;border-bottom:1px solid #000000;border-right:1px solid #000000;">&nbsp;</td>';							
						$table.='</tr>';		
						$counter++;
						}	
						$table.='<tr>
									<td colspan="2" style="font-size:10px;height:25px;vertical-align:middle;border-bottom:1px solid #000000;border-right:1px solid #000000;font-weight:bold;">Total Amount <br>Issued</td>';
									foreach($narcoticsMedsArr as $keyMedId => $narcoMedsTitleName){
										$narcoticsMedicationId=$detail_arr[$patient_Id][$keyMedId]['narotics_medication_id'];
										$totalAmountIssuedMed=$detail_arr[$patient_Id][$keyMedId]['total_amount_issued'];
										$table.='<td  style="border-bottom:1px solid #000000;border-right:1px solid #000000;padding-left:5px; vertical-align:middle;">';
												 if($keyMedId==$narcoticsMedicationId){$table.=$totalAmountIssuedMed;}else{$table.="";}
										$table.='</td>';
									}
										$table.='<td style="border-bottom:1px solid #000000;border-right:1px solid #000000;">&nbsp;</td>	
												 <td style="border-bottom:1px solid #000000;border-right:1px solid #000000;">&nbsp;</td>						
								</tr>';
					
					$table.='<tr>
									<td colspan="2" style="font-size:10px;height:25px;vertical-align:middle;border-bottom:1px solid #000000;border-right:1px solid #000000;font-weight:bold;">Amount<br> Administered</td>';
									foreach($narcoticsMedsArr as $keyMedId => $narcoMedsTitleName){
										$narcoticsMedId=$detail_arr[$patient_Id][$keyMedId]['narotics_medication_id'];
										$totalAmountAdministered=$detail_arr[$patient_Id][$keyMedId]['amount_administered'];
										$table.='<td  style="border-bottom:1px solid #000000;border-right:1px solid #000000;padding-left:5px; vertical-align:middle;">';
												 if($keyMedId==$narcoticsMedId){$table.=$totalAmountAdministered;}else{$table.="";}
										$table.='</td>';
									}
										$table.='<td style="border-bottom:1px solid #000000;border-right:1px solid #000000;">&nbsp;</td>	
												 <td style="border-bottom:1px solid #000000;border-right:1px solid #000000;">&nbsp;</td>						
								</tr>';
					
					$table.='<tr>
									<td colspan="2" style="font-size:10px;height:25px;vertical-align:middle;border-bottom:1px solid #000000;border-right:1px solid #000000;font-weight:bold;">Amount Wasted</td>';
									foreach($narcoticsMedsArr as $keyMedId => $narcoMedsTitleName){
										$narcoMedId=$detail_arr[$patient_Id][$keyMedId]['narotics_medication_id'];
										$totalAmountWasted=$detail_arr[$patient_Id][$keyMedId]['amount_wasted'];
										$table.='<td  style="border-bottom:1px solid #000000;border-right:1px solid #000000;padding-left:5px; vertical-align:middle;">';
												 if($keyMedId==$narcoMedId){$table.=$totalAmountWasted;}else{$table.="";}
										$table.='</td>';
									}
										$table.='<td style="border-bottom:1px solid #000000;border-right:1px solid #000000;">&nbsp;</td>	
												 <td style="border-bottom:1px solid #000000;border-right:1px solid #000000;">&nbsp;</td>						
								</tr>';
					
					
					
					$table.='<tr>
									<td colspan="2" style="font-size:10px;height:25px;vertical-align:middle;border-bottom:1px solid #000000;border-right:1px solid #000000;font-weight:bold;">Amount Returned <br>(Full)</td>';
									foreach($narcoticsMedsArr as $keyMedId => $narcoMedsTitleName){
										$returnedMedId=$detail_arr[$patient_Id][$keyMedId]['narotics_medication_id'];
										$totalAmountReturned=$detail_arr[$patient_Id][$keyMedId]['amount_returned'];
										$table.='<td  style="border-bottom:1px solid #000000;border-right:1px solid #000000; padding-left:5px; vertical-align:middle;">';
												 if($keyMedId==$returnedMedId){$table.=$totalAmountReturned;}else{$table.="";}
										$table.='</td>';
									}
										$table.='<td style="border-bottom:1px solid #000000;border-right:1px solid #000000;">&nbsp;</td>	
												 <td style="border-bottom:1px solid #000000;border-right:1px solid #000000;">&nbsp;</td>						
								</tr>';
					
					$table.='<tr>
									<td colspan="2" style="font-size:10px;height:25px;vertical-align:middle;border-bottom:1px solid #000000;border-right:1px solid #000000;font-weight:bold;">Amount<br> Accounted For</td>';
									foreach($narcoticsMedsArr as $keyMedId => $narcoMedsTitleName){
										$accountedMedId=$detail_arr[$patient_Id][$keyMedId]['narotics_medication_id'];
										$totalAmountAccounted=$detail_arr[$patient_Id][$keyMedId]['amount_accounted_for'];
										$table.='<td  style="border-bottom:1px solid #000000;border-right:1px solid #000000;padding-left:5px; vertical-align:middle; vertical-align:middle;">';
												 if($keyMedId==$accountedMedId){$table.=$totalAmountAccounted;}else{$table.="";}
										$table.='</td>';
									}
										$table.='<td style="border-bottom:1px solid #000000;border-right:1px solid #000000;">&nbsp;</td>	
												 <td style="border-bottom:1px solid #000000;border-right:1px solid #000000;">&nbsp;</td>						
								</tr>';
					$table.='</table>
						</td>
					</tr>';
			$table.='</table>';
			$table_print=$head_table.$table;
			$fp = fopen('new_html2pdf/pdffile.html','w+');
			$intBytes = fputs($fp,$table_print);
			fclose($fp);
			
		?>
		<script language="javascript">
			window.focus();
			function submitfn(){
				document.printFrm.submit();
			}
		</script>
		<table  bgcolor="#FFFFFF"  style="font:vetrdana; font-size:14;" width="100%" height="100%">
			<tr>
				<td width="100%" align="center" valign="middle"><img src="images/ajax-loader.gif"></td> 
			</tr>
		</table>
		<form name="printFrm" action="new_html2pdf/createPdf.php?op=l" method="post">
		</form> 
		<script type="text/javascript">
			submitfn();
		</script>
	<?php
	}else {
		echo "<center>Please verify/save this form before print</center>";
	}
	?>	
	
</html>
