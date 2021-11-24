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
$objManageData = new manageData;
function getPatientDetail($patientId){
	if($patientId){
		$qryPatientData="SELECT patient_fname,patient_mname,patient_lname FROM patient_data_tbl where patient_id='".$patientId."'";
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
	$qryNarcoMeds="SELECT preOpMedicationOrderId,medicationName FROM preopmedicationorder WHERE mediCatId ='".$narcoMedsID."' group by medicationName";
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
function getMedicationVarified($narcoticsMedicationName){
	$catgoryName="Narcotics";
	$qryAllMedCatgory=imw_query("SELECT categoryId FROM preopmedicationcategory where categoryName='".$catgoryName."'");
	$rowCategoryId=imw_fetch_assoc($qryAllMedCatgory);
	$narcoticsID=$rowCategoryId['categoryId'];
	$retunVal=0;
	$qryCheckNarcoticsMed="Select preOpMedicationOrderId,medicationName from preopmedicationorder where mediCatId='".$narcoticsID."' and medicationName='".$narcoticsMedicationName."'";
	$resultCheckNarcoticsMed=imw_query($qryCheckNarcoticsMed);
	//echo(imw_num_rows($resultCheckNarcoticsMed));
	$preOpNarcoticsMedicationId=0;
	if(imw_num_rows($resultCheckNarcoticsMed)>0){
		$rowMedication=imw_fetch_assoc($resultCheckNarcoticsMed);
		$preOpNarcoticsMedicationId=$rowMedication['preOpMedicationOrderId'];
	}	
	return $preOpNarcoticsMedicationId;
}

function getNarcoticsMedicationDetail($getPatientConfId){
	$medicationArr=array();
	$qryPatientMedication	 = "Select patient_confirmation_id,medicationName,strength  from  patientpreopmedication_tbl where patient_confirmation_id='".$getPatientConfId."'";
	$resultPatientMedication = imw_query($qryPatientMedication)or die(imw_error());
	if(imw_num_rows($resultPatientMedication)>0){
		while($rowPatientMedication=imw_fetch_assoc($resultPatientMedication)){
			$medicationName	 = "";
			$medicationDosge = "";
			$medication	 	 = $rowPatientMedication['medicationName'];
			$strength	 	 = $rowPatientMedication['strength'];
			$medicationName	 = $medication;
			$medicationDosge = $strength;
			$narcoticsReport = "";
			$narcoticsReport=getMedicationVarified($medicationName);
			if($narcoticsReport>0){
				$medicationArr[] = $narcoticsReport."@@".$medicationDosge;
			}
		}
	}	
	return $medicationArr;
}
function adddtionOfMedication($medId,$medDescripation,$totalAmountCol=0,$matchMedTitleID){
	if($totalAmountCol==1){
		if($medId==$matchMedTitleID){
			if($medDescripation){
			
			}		
		}	
	}	
}
$narcoticsMedsArr=getAllNarcoticsMeds();
//print_r($narcoticsMedsArr);
$counter=1;
unset($conditionArr);
$dateOfService 		  = "";
$getDateOfService 	  = $_REQUEST['dateOS'];
$dateOfService 		  = $objManageData->changeDateYMD($getDateOfService);
$qryPatientConfDetail = "SELECT pc.* from patientconfirmation pc LEFT JOIN patient_data_tbl pdt ON pdt.patient_id = pc.patientId where pc.dos = '".$dateOfService."' order by pdt.patient_lname";
$resPatientConfDetail = imw_query($qryPatientConfDetail) or die(imw_error());
while($rowPatientConfDetail=imw_fetch_object($resPatientConfDetail)){
	$getPatientConfDetail[]=$rowPatientConfDetail;
}
$qryNarcoticsLog="select log_id from narcotics_log_tbl where date_of_service='".$dateOfService ."' and delete_status!='1'";
$resNarcoticsLog=imw_query($qryNarcoticsLog)or die(imw_error());
$numRowNarcoticsLog=imw_num_rows($resNarcoticsLog);
$save_record=$_REQUEST['saveRecord'];
$SaveForm_alert=$_REQUEST['SaveForm_alert'];
if($save_record=="true"){
	if($_POST['hiddRefresh']){
		$qryDeleteNarcoticsSaveRecord="update narcotics_log_tbl set delete_status='1' where date_of_service='".$dateOfService."'";
		$resDeleteNarcoticsSaveRecord=imw_query($qryDeleteNarcoticsSaveRecord)or die(imw_error());
	}else{
		$dateOfService=$objManageData->changeDateYMD($_REQUEST['dateOS']);
		$patient_Id=$_POST['patientId'];
		$patient_conf_Id=$_POST['patientConfId'];
		$patient_Name=$_POST['patientName'];
		$narcoMedId=$_POST['narcoMedId'];
		$narcoMedDescripation=$_POST['narcoMedDescripation'];
		$log_id_arr=$_POST['log_id_arr'];
		$mdCrna=$_POST['md_crna'];
		$dateTime=date('Y-m-d H:i:s');
		$totalAmountIssued=$_POST['total_amount_issued'];
		$amountAdministered=$_POST['amount_administered'];
		$amountWasted=$_POST['amount_wasted'];
		$amountReturned=$_POST['amount_returned'];
		$amountAccounted=$_POST['amount_accounted'];
		
		$loopCtr=count($patient_Id);
		for($i=0;$i<$loopCtr;$i++){
			$patient_IDS=$patient_Id[$i];
			$patient_Conf_IdS=$patient_conf_Id[$i];
			$patients_Name=$patient_Name[$i];
			$patientIDArr=$patient_Id[$i];
			$medIds="";
			$getTotalAmountIssued="";
			$narcoticsMedcationId="";
			for($j=0;$j<count($narcoMedId[$patient_IDS]);$j++){
				$narcoticsMedcationId=$narcoMedId[$patientIDArr][$j];
				$narcoticsMedcationDescripation=$narcoMedDescripation[$patientIDArr][$j];	
				$log_id_up=$log_id_arr[$patientIDArr][$j];
				$getTotalAmountIssued=$totalAmountIssued[$j];
				$getAmountAdministered=$amountAdministered[$j];
				$getAmountWasted=$amountWasted[$j];
				$getAmountReturned=$amountReturned[$j];
				$getAmountAccounted=$amountAccounted[$j];
				if($log_id_up>0 && $numRowNarcoticsLog>0){
					$qryInsertNarctocisTbl="update 
					narcotics_log_tbl set 
					patient_id='".$patient_IDS."', 
					patient_confirmation_id='".$patient_Conf_IdS."',
					patient_name='".$patients_Name."',
					date_of_service='".$dateOfService."',
					save_date_time='".$dateTime."',
					narotics_medication_id='".$narcoticsMedcationId."',
					narcotics_medication_descripation='".$narcoticsMedcationDescripation."',
					md_crna='".$mdCrna."',total_amount_issued='".$getTotalAmountIssued."',
					amount_administered='".$getAmountAdministered."',amount_wasted='".$getAmountWasted."',
					amount_returned='".$getAmountReturned."',amount_accounted_for='".$getAmountAccounted."'
					where log_id='".$log_id_up."'";
					$qryResult=imw_query($qryInsertNarctocisTbl)or die(imw_error());	
				}else if($patient_Conf_IdS){
					$qryInsertNarctocisTbl="insert into 
					narcotics_log_tbl set 
					patient_id='".$patient_IDS."', 
					patient_confirmation_id='".$patient_Conf_IdS."',
					patient_name='".$patients_Name."',
					date_of_service='".$dateOfService."',
					save_date_time='".$dateTime."',
					narotics_medication_id='".$narcoticsMedcationId."',
					narcotics_medication_descripation='".$narcoticsMedcationDescripation."',
					md_crna='".$mdCrna."',total_amount_issued='".$getTotalAmountIssued."',
					amount_administered='".$getAmountAdministered."',amount_wasted='".$getAmountWasted."',
					amount_returned='".$getAmountReturned."',amount_accounted_for='".$getAmountAccounted."'
					";
					$qryResult=imw_query($qryInsertNarctocisTbl)or die(imw_error());	
				}	
			}
		}
	}
}
$qryNarcoticsLogForCheck="select log_id from narcotics_log_tbl where date_of_service='".$dateOfService ."' and delete_status!='1'";
$resNarcoticsLogCheck=imw_query($qryNarcoticsLogForCheck)or die(imw_error());
$numofRowNarcoticsLog=imw_num_rows($resNarcoticsLogCheck);
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<link rel="stylesheet" href="css/style_surgery.css" type="text/css" />
<title>Narcotics Log Sheet</title>
<style>
	.drsElement {
		position: absolute;
		border: 1px solid #333;
	}
	.drsMoveHandle {
		height: 20px;
		background-color: #CCC;
		border-bottom: 1px solid #666;
	}
	form{margin:0px;}
	a.black:hover{color:"Red";	text-decoration:none;}
</style>
<script language="javascript">
   window.focus();
   function MM_swapImgRestore(obj,image_path) { //v3.0
	  document.getElementById(obj).src =  image_path;
	}
	function printNarcoticsLogSheet(){
		var date_of_service=document.getElementById("dateOS").value;
		window.open('narcotics_log_sheet_print_popup.php?dateOS='+date_of_service,'win_narcotics','width=900, height=800,resizable=1');
	}
	function SaveAndprintNarcoticsLogSheet(){
		narcoticslogFrm.submit();
		printNarcoticsLogSheet();
	}
	function confirmRefresh(){
		var con;
		con=confirm("Your saved logsheet will be lost. Are you sure to refresh the page.");
		if(con==true){
			document.getElementById("hiddRefresh").value="1";
			document.getElementById("SaveForm_alert").value="";
			narcoticslogFrm.submit();
		}else{
			return false;
		}
	}
  </script>
</head>
	<body bgcolor="#ECF1EA" style="margin:0px;">
		<div id="divSaveAlert" style="position:absolute;left:350px; top:220px; display:none; z-index:1000;">
			<?php 
				$bgCol = '#FBD78D';
				$borderCol = '#FBD78D';
				include('saveDivPopUp.php'); 
			?>
		</div>
		<form name="frmPrintNarcoticsLogSheet" action="narcotics_log_sheet_print_popup.php" method="post">
		</form>	
		<form name="narcoticslogFrm" action="narcotics_log_sheet.php?saveRecord=true" method="post">
			<?php
			$surgerycenter_name=getData("name","surgerycenter","surgeryCenterId","1");//GET SURGERY CENTER NAME
			if($narcoticsMedsArr){
				if($getPatientConfDetail){	
				?>
					<div style="width:100%; height:35px; text-align:center; font-size:18px; background:#FBD78D;  vertical-align:bottom;" class="text_homeb">Narcotics Log Sheet</div>
					<div class="text_homeb" style="width:100%; height:45px; text-align:center;  color:#CB6B43; font-size:14px;vertical-align:middle;" ><br><?php echo $surgerycenter_name ;?> - ANESTHESIA DEPARTMENT CONTROLLED DRUG ADMINISTRATOR RECORD</div>
					<div style="text-align:right; padding-right:10px; position:absolute; top:40px; left:1090px;"><?php if($_SESSION['loginUserType']=="Anesthesiologist" || $_SESSION['loginUserType']=="Nurse") {?><img onClick="return confirmRefresh()" onMouseOut="MM_swapImgRestore('RefreshBtn','images/refresh.jpg',1)" onMouseOver="MM_swapImgRestore('RefreshBtn','images/refresh_hover.jpg',1)" src="images/refresh.jpg" style="border:none;cursor:pointer;" id="RefreshBtn" alt="Refresh"/><?php } ?></div>
					<div style=" overflow:auto;" id="mainDiv">
				<?php 
				if($numofRowNarcoticsLog>0){
					$conditionNarcoticsArr	 = array();
					$dateOfService 	 = $objManageData->changeDateYMD($getDateOfService);
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
				}
				
				?>
				<div>
					<table  style="width:1000px;" cellpadding="2" cellspacing="2">
						<tr>
							<td style="width:350px; padding-left:190px;" class="text_homeb">MD&nbsp;/&nbsp;CRNA&nbsp;:&nbsp;<input type="text" name="md_crna" value="<?php echo $patient_md_crna; ?>" style="border:2px solid #E1E1E1;font-family:Verdana, Arial, Helvetica, sans-serif; font-size:14px;"></td>
							<td style="text-align:right; width:500px;font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px;" ><strong>DATE</strong>&nbsp;:&nbsp;<?php echo $getDateOfService;  ?> </td>
						</tr>
					</table>
				</div>
				<?php
				if($numofRowNarcoticsLog>0){ 
				?>
				<div style="width:100%; height:450px; overflow:auto;">	
				<table class="table_separate" style="width:100%;background-color:#FFFFFF;" border="1" bordercolor="#E1E1E1" cellpadding="0" cellspacing="0">
					<tr  class="alignCenter valignMiddle text_homeb" style=" height:23px; font-size:11px;background-color:#FFFFFF;">
						<th style="width:50px;vertical-align:middle; text-align:center;">No.</th>
						<td style="width:220px;vertical-align:middle; text-align:left; padding-left:5px;font-weight:bold;">Patient Name</td>
						<?php 
							foreach($narcoticsMedsArr as  $narcoMedsTitleName){?>
								<td  style="width:80px; padding-left:5px;vertical-align:middle;font-size:12px; font-weight:bold;"><?php echo $narcoMedsTitleName; ?></td>
						<?php 
							}
						?>
						<td style="width:80px;vertical-align:middle; font-weight:bold; padding-left:5px; padding-right:5px;">Signature</td>
						<td  style="width:100px;vertical-align:middle;  font-weight:bold; padding-left:5px; padding-right:5px;">Co-Signature</td>
					</tr>	
					<?php
						foreach($detail_pat_arr as $pat_det => $pat_id_val){
							$getPatientName="";
							$patient_Id	= $pat_id_val;
							$getPatientName=getPatientDetail($patient_Id);
							$patient_Conf_Id=$detail_conf_arr[$patient_Id];
					?>
							<tr>
								<td style="vertical-align:middle; text-align:center; font-size:12px;font-family:Verdana; margin-top:5px;" >
									<input type="hidden" name="patientId[]" value="<?php echo $patient_Id;  ?>">
									<input type="hidden" name="patientConfId[]" value="<?php echo $patient_Conf_Id; ?>">
									<?php echo $counter."."; ?>
								</td>	
								<td style="vertical-align:middle;font-size:14px;  height:30px;text-align:left; padding-left:5px;"><?php echo "<input type='text' name='patientName[]' style='border:none; background:none;overflow:hidden;  font-family:Verdana, Arial, Helvetica, sans-serif; font-size:14px; width:100px;' value='".$getPatientName."'>"; ?></td>
								<?php
								foreach($narcoticsMedsArr as $keyMedId => $narcoMedsTitleName){
									$patient_Narcotics_Medication_Id=$detail_arr[$patient_Id][$keyMedId]['narotics_medication_id'];
									$patient_Narcotics_Medication_Descripation=$detail_arr[$patient_Id][$keyMedId]['narcotics_medication_descripation'];
									$patient_Narcotics_Log_Id=$detail_arr[$patient_Id][$keyMedId]['log_id'];
								?>
                                    <td style="vertical-align:middle; height:30px; padding-left:5px;">
									<?php 
                                    echo '<input type="hidden" name="log_id_arr['.$patient_Id.'][]" value="'.$patient_Narcotics_Log_Id.'">';
									if(($patient_Narcotics_Medication_Id)==$keyMedId){$narMedication=$patient_Narcotics_Medication_Descripation;
                                    	echo '<input type="hidden" name="narcoMedId['.$patient_Id.'][]" value="'.$keyMedId.'">';
                                   		echo "<input type='text' name='narcoMedDescripation[".$patient_Id."][]' style='border:none; background:none; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:14px;width:70px;' value='".$narMedication."'>";
                                    }else{
										echo '<input type="hidden" name="narcoMedId['.$patient_Id.'][]" value="'.$keyMedId.'">';
                                   		echo "<input type='text' name='narcoMedDescripation[".$patient_Id."][]' style='border:none; background:none; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:14px;width:70px;'>";
									}
                                    ?>									
									</td>
								<?php			
								}
								?>
								<td>&nbsp;</td>	
								<td>&nbsp;</td>						
							</tr>	
						
						<?php
						$counter++;
						}
						?>	
						<tr style="height:35px;">
							<td colspan="2" style="font-size:12px; text-align:right; padding-right:10px; font-weight:bold; font-family:Verdana;">Total Amount Issued</td>
							<?php 
							foreach($narcoticsMedsArr as $keyMedId => $narcoMedsTitleName){ 
								$narcoticsMedicationId=$detail_arr[$patient_Id][$keyMedId]['narotics_medication_id'];
								$totalAmountIssuedMed=$detail_arr[$patient_Id][$keyMedId]['total_amount_issued'];
								
								?>
								<td  style="width:80px; padding-left:5px;vertical-align:middle;"><input type="text"  value="<?php if($keyMedId==$narcoticsMedicationId){ echo $totalAmountIssuedMed;} ?>" name="total_amount_issued[]" style="border:none; background:none; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:14px;width:70px;"></td>
							<?php 
								}
							?>
								
							<td>&nbsp;</td>	
							<td>&nbsp;</td>						
						</tr>
						<tr style="height:35px;">
							<td colspan="2" style="font-size:12px; text-align:right; padding-right:10px;font-weight:bold; font-family:Verdana;">Amount Administered</td>
							<?php 
							foreach($narcoticsMedsArr as $keyMedId => $narcoMedsTitleName){
								$narcoticsMedId=$detail_arr[$patient_Id][$keyMedId]['narotics_medication_id'];
								$totalAmountAdministered=$detail_arr[$patient_Id][$keyMedId]['amount_administered'];
								
							?>
								<td  style="width:80px; padding-left:5px;vertical-align:middle;"><input type="text"  value="<?php if($keyMedId==$narcoticsMedId){echo $totalAmountAdministered; } ?>" name="amount_administered[]" style="border:none; background:none;font-family:Verdana, Arial, Helvetica, sans-serif; font-size:14px;width:70px;"></td>
							<?php 
								}
							?>
								
							<td>&nbsp;</td>	
							<td>&nbsp;</td>						
						</tr>
						<tr style="height:35px;">
							<td colspan="2" style="font-size:12px; text-align:right; font-weight:bold; padding-right:10px; font-family:Verdana;">Amount Wasted</td>
							<?php 
							foreach($narcoticsMedsArr as $keyMedId => $narcoMedsTitleName){
								$wastedMedId=$detail_arr[$patient_Id][$keyMedId]['narotics_medication_id'];
								$totalAmountWasted=$detail_arr[$patient_Id][$keyMedId]['amount_wasted'];
							?>
								<td  style="width:80px; padding-left:5px;vertical-align:middle; "><input type="text" value="<?php if($keyMedId==$wastedMedId){echo $totalAmountWasted; } ?>" name="amount_wasted[]"  style="border:none; background:none;font-family:Verdana, Arial, Helvetica, sans-serif; font-size:14px; width:70px;"></td>
							<?php 
								}
							?>
							
							<td>&nbsp;</td>	
							<td>&nbsp;</td>						
						</tr>
						<tr style="height:35px;">
							<td colspan="2" style="font-size:12px; text-align:right; padding-right:10px; font-weight:bold; font-family:Verdana;">Amount Returned (Full)</td>
							<?php 
							foreach($narcoticsMedsArr as $keyMedId => $narcoMedsTitleName){
								$narcotiMedId=$detail_arr[$patient_Id][$keyMedId]['narotics_medication_id'];
								$totalAmountReturn=$detail_arr[$patient_Id][$keyMedId]['amount_returned'];
							?>
								<td  style="width:80px; padding-left:5px;vertical-align:middle;"><input type="text" value="<?php if($keyMedId==$narcotiMedId){ echo $totalAmountReturn;} ?>" name="amount_returned[]" style="border:none; background:none;font-family:Verdana, Arial, Helvetica, sans-serif; font-size:14px;width:70px;"></td>
							<?php 
								}
							?>
								
							<td>&nbsp;</td>	
							<td>&nbsp;</td>						
						</tr>
						<tr style="height:35px;">
							<td colspan="2" style="font-size:12px; text-align:right; padding-right:10px; font-weight:bold; font-family:Verdana;">Amount Accounted For</td>
							<?php 
							foreach($narcoticsMedsArr as $keyMedId => $narcoMedsTitleName){
								$narcotiMedsId=$detail_arr[$patient_Id][$keyMedId]['narotics_medication_id'];
								$totalAmountAccounted=$detail_arr[$patient_Id][$keyMedId]['amount_accounted_for'];
							?>
								<td  style="width:80px; padding-left:5px;vertical-align:middle;"><input type="text" value="<?php if($keyMedId==$narcotiMedsId){ echo $totalAmountAccounted;} ?>" name="amount_accounted[]"  style="border:none; background:none;font-family:Verdana, Arial, Helvetica, sans-serif; font-size:14px;width:70px;"></td>
							<?php 
								}
							?>
							<td>&nbsp;</td>	
							<td>&nbsp;</td>						
						</tr>
					</table>
                    </div>						
					
				<?php
				}else{
				?>
				
                <div style="width:100%; height:450px; overflow:auto;">	
				<table class="table_separate" style="width:100%;background-color:#FFFFFF;" border="1" bordercolor="#E1E1E1" cellpadding="0" cellspacing="0">
					<tr  class="alignCenter valignMiddle text_homeb" style=" height:23px; font-size:11px;background-color:#FFFFFF;">
						<th style="width:50px;vertical-align:middle; text-align:center;">No.</th>
						<td style="width:220px;vertical-align:middle; text-align:left; padding-left:5px;font-weight:bold;">Patient Name</td>
						<?php 
							foreach($narcoticsMedsArr as  $narcoMedsTitleName){?>
								<td  style="width:80px; padding-left:5px;vertical-align:middle;font-size:12px; font-weight:bold;"><?php echo $narcoMedsTitleName; ?></td>
						<?php 
							}
						?>
						
						<td style="width:80px;vertical-align:middle; font-weight:bold; padding-left:5px; padding-right:5px;">Signature</td>
						<td  style="width:100px;vertical-align:middle;  font-weight:bold; padding-left:5px; padding-right:5px;">Co-Signature</td>
					</tr>	
                
                
                	
					<?php 
						foreach($getPatientConfDetail as $patientConfDetail){
							$patient_Id=$patientConfDetail->patientId;
							$patient_Conf_Id=$patientConfDetail->patientConfirmationId;
							$returnMedicationAndDosageArr=getNarcoticsMedicationDetail($patient_Conf_Id);
							//print_r($returnMedicationAndDosageArr);
							$getPatientName="";
							$getPatientName=getPatientDetail($patient_Id);
					?>
							<tr>
								<td style="vertical-align:middle; text-align:center; font-size:12px;font-family:Verdana; margin-top:5px;" >
									<input type="hidden" name="patientId[]" value="<?php echo $patient_Id;  ?>">
									<input type="hidden" name="patientConfId[]" value="<?php echo $patient_Conf_Id; ?>">
									<?php echo $counter."."; ?>
								</td>	
								<td style="vertical-align:middle;font-size:14px;  height:30px;text-align:left; padding-left:5px;"><?php echo "<input type='text' name='patientName[]' style='border:none; background:none;overflow:hidden;  font-family:Verdana, Arial, Helvetica, sans-serif; font-size:14px; width:100px;' value='".$getPatientName."'>"; ?></td>
								
									<?php
										foreach($narcoticsMedsArr as $keyMedId => $narcoMedsTitleName){?>
											
                                            
                                            <td style="vertical-align:middle; height:30px; padding-left:5px;">
											<?php 
												if(!empty($returnMedicationAndDosageArr)){
													$medFound=false;
													foreach($returnMedicationAndDosageArr as $returnMedicationAndDosage){
														$narMedication="";
														$getMedicationName=explode("@@",$returnMedicationAndDosage);
														$returnMedication=$getMedicationName[0];
														$returnDosage=$getMedicationName[1];
														if(($returnMedication)==$keyMedId){$narMedication=$returnDosage;$medFound=true;
															echo '<input type="hidden" name="narcoMedId['.$patient_Id.'][]" value="'.$keyMedId.'">';
															echo "<input type='text' name='narcoMedDescripation[".$patient_Id."][]' style='border:none; background:none;font-weight:normal;font-family:Verdana, Arial, Helvetica, sans-serif; font-size:14px; overflow:hidden;width:70px;' value='".$narMedication."'>";
														}
													}
													if($medFound==false){
														echo '<input type="hidden" name="narcoMedId['.$patient_Id.'][]" value="'.$keyMedId.'">';
														echo "<input type='text' name='narcoMedDescripation[".$patient_Id."][]' style='border:none; background:none;font-weight:normal;font-family:Verdana, Arial, Helvetica, sans-serif; font-size:14px; overflow:hidden;width:70px;'>";
													}	
												}else{
													echo '<input type="hidden" name="narcoMedId['.$patient_Id.'][]" value="'.$keyMedId.'">';
													echo "<input type='text' name='narcoMedDescripation[".$patient_Id."][]' style='border:none; background:none;font-weight:normal; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:14px;overflow:hidden; width:70px;'>";
												
												}	
											?>
											</td>
									<?php } ?>	
								
								<td>&nbsp;</td>	
								<td>&nbsp;</td>						
						</tr>	
						
						<?php
						$counter++;
						}
						?>	
						<tr style="height:35px;">
							<td colspan="2" style="font-size:12px; text-align:right; padding-right:10px; font-weight:bold; font-family:Verdana;">Total Amount Issued</td>
							<?php 
							foreach($narcoticsMedsArr as $narcoMedsTitleName){?>
								<td  style="width:80px; padding-left:5px;vertical-align:middle;"><input type="text"  value="" name="total_amount_issued[]" style="border:none; background:none; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:14px;width:70px;"></td>
							<?php 
								}
							?>
							
							<td>&nbsp;</td>	
							<td>&nbsp;</td>						
						</tr>
						<tr style="height:35px;">
							<td colspan="2" style="font-size:12px; text-align:right; padding-right:10px;font-weight:bold; font-family:Verdana;">Amount Administered</td>
							<?php 
							foreach($narcoticsMedsArr as $narcoMedsTitleName){?>
								<td  style="width:80px; padding-left:5px;vertical-align:middle;"><input type="text"  value="" name="amount_administered[]" style="border:none; background:none;font-family:Verdana, Arial, Helvetica, sans-serif; font-size:14px;width:70px;"></td>
							<?php 
								}
							?>
							
							<td>&nbsp;</td>	
							<td>&nbsp;</td>						
						</tr>
						<tr style="height:35px;">
							<td colspan="2" style="font-size:12px; text-align:right; font-weight:bold; padding-right:10px; font-family:Verdana;">Amount Wasted</td>
							<?php 
							foreach($narcoticsMedsArr as $narcoMedsTitleName){?>
								<td  style="width:80px; padding-left:5px;vertical-align:middle; "><input type="text" value="" name="amount_wasted[]"  style="border:none; background:none;font-family:Verdana, Arial, Helvetica, sans-serif; font-size:14px; width:70px;"></td>
							<?php 
								}
							?>
							
							<td>&nbsp;</td>	
							<td>&nbsp;</td>						
						</tr>
						<tr style="height:35px;">
							<td colspan="2" style="font-size:12px; text-align:right; padding-right:10px; font-weight:bold; font-family:Verdana;">Amount Returned (Full)</td>
							<?php 
							foreach($narcoticsMedsArr as $narcoMedsTitleName){?>
								<td  style="width:80px; padding-left:5px;vertical-align:middle;"><input type="text" value="" name="amount_returned[]" style="border:none; background:none;font-family:Verdana, Arial, Helvetica, sans-serif; font-size:14px;width:70px;"></td>
							<?php 
								}
							?>
							
							<td>&nbsp;</td>	
							<td>&nbsp;</td>						
						</tr>
						<tr style="height:35px;">
							<td colspan="2" style="font-size:12px; text-align:right; padding-right:10px; font-weight:bold; font-family:Verdana;">Amount Accounted For</td>
							<?php 
							foreach($narcoticsMedsArr as $narcoMedsTitleName){?>
								<td  style="width:80px; padding-left:5px;vertical-align:middle;"><input type="text" value="" name="amount_accounted[]"  style="border:none; background:none;font-family:Verdana, Arial, Helvetica, sans-serif; font-size:14px;width:70px;"></td>
							<?php 
								}
							?>
							
							<td>&nbsp;</td>	
							<td>&nbsp;</td>						
						</tr>
					</table>
                    </div>
					<?php
					}	
					?>
					<div style="text-align:center; margin:10px;">
						<?php 
						if($_SESSION['loginUserType']=="Anesthesiologist" || $_SESSION['loginUserType']=="Nurse") {?>
                        <a style="margin:5px 5px 5px 5px;" href="#"  onMouseOut="MM_swapImgRestore('saveBtn','images/save.jpg',1)" onMouseOver="MM_swapImgRestore('saveBtn','images/save_hover1.jpg',1)"><img src="images/save.jpg" id="saveBtn" style="border:none;" alt="save" onClick="javascript:narcoticslogFrm.submit();"/></a>
						<a style="margin:5px 5px 5px 5px;" href="#" onMouseOut="MM_swapImgRestore('PrintBtnNew','images/print.jpg')" onMouseOver="MM_swapImgRestore('PrintBtnNew','images/print_hover1.jpg',1)"><img src="images/print.jpg" name="PrintBtnNew" width="70" height="25" border="0" id="PrintBtnNew" alt="Print" onClick="javascript:printNarcoticsLogSheet(<?php echo $dateOfService; ?>);"/></a>
						<a style="margin:5px 5px 5px 5px;" href="#" onMouseOut="MM_swapImgRestore('save_n_print_btn','images/save_n_print.jpg',1)" onMouseOver="MM_swapImgRestore('save_n_print_btn','images/save_n_print_hover1.jpg',1)"><img src="images/save_n_print.jpg" id="save_n_print_btn" style="border:none;" alt="save" onClick="javascript:SaveAndprintNarcoticsLogSheet()" /></a>
                        <?php
						}
						?>
                        <a style="margin:5px 5px 5px 5px;" href="#"  onMouseOut="MM_swapImgRestore('closeBtn','images/close.gif',1)" onMouseOver="MM_swapImgRestore('closeBtn','images/close_hover.gif',1)"><img src="images/close.gif" id="closeBtn" style="border:none;" alt="close" onClick="javascript:window.close();" /></a>
						
					</div>
				</div>	
				<?php	
				}else{
					echo "<br><div class='text_homeb' style='height:25px; font-weight:bold; border:1px solid #CCCCCC; font-size:14px; vartical-align:middle; text-align:center;'>No Record Found !</div>";
				}
			}else{
					echo "<br><div class='text_homeb' style='height:25px; font-weight:bold; border:1px solid #CCCCCC; font-size:14px; vartical-align:middle; text-align:center;'>No Narcotics Medication Exists In Admin</div>";
			}	
				?>
				<input type="hidden" id="dateOS" value="<?php echo($_REQUEST['dateOS']); ?>" name="dateOS" />
				<input type="hidden" id="hiddRefresh" name="hiddRefresh" />
				<input type="hidden" id="SaveForm_alert" name="SaveForm_alert" value="true" >
		</form>	
		 <?php 
			if($_POST['SaveForm_alert']==true){	?>	
			<script>document.getElementById('divSaveAlert').style.display='block';</script>
		<?php }	?>
	</body>
</html>