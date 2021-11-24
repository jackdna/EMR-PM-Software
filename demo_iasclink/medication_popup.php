<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php 
error_reporting(0);
session_start();
$loginUser = $_SESSION['iolink_loginUserId'];
include_once("common/conDb.php");
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Surgerycenter EMR</title>
<script type="text/javascript" src="js/jquery-1.11.3.js"></script>
<script>
window.focus();
	function abc(data){
//		alert(data);
		for(i=0; i<20; i++){
		var str='medicationName'+i;
		//alert(str)
			if(document.getElementById(str)){
			//alert(document.getElementById(str).value);
			if(document.getElementById(str).value==''){
				document.getElementById(str).value=data;
				break;			
			}
			}
		}
}
function closeDivMed(){
	if(document.getElementById('preDefineDivOpenClose').value==""){
		if(document.getElementById('iolinkPreDefineMedAdminDiv').style.display=="block"){
			document.getElementById('iolinkPreDefineMedAdminDiv').style.display="none"
		}
	}
	if(document.getElementById('preDefineDivOpenClose').value=="open"){
		document.getElementById('preDefineDivOpenClose').value="";
	}
	
}

function clearMedVal(obj)
{
	if(obj.checked==true)
	{
		//clear	all values
		$('.noMedOn').val('');
		$('.noMedOn').attr('disabled',true);
		$('.noMedOn').css("background-color", "#F0F0F0");
		
		$('#iolink_no_medication_comments').attr('disabled',false);
		$('#iolink_no_medication_comments').css("background-color", "");
	}	
	else 
	{
		$('.noMedOn').attr('disabled',false);
		$('.noMedOn').css("background-color", "");
		
		$('#iolink_no_medication_comments').val('');
		$('#iolink_no_medication_comments').attr('disabled',true);
		$('#iolink_no_medication_comments').css("background-color", "#F0F0F0");
		
	}
}

$(document).ready(function(e) {
    var objMed = document.getElementById('iolink_no_medication_status');
	clearMedVal(objMed);
	
	var saveData = '<?php echo $_REQUEST['saveData'];?>';
	if(saveData !='') {
		alert('Data saved');
	}
});

</script>
<?php
$spec= "
</head>
<body>";
include_once("common/link_new_file.php");
include_once("common/functions.php");
include_once("admin/classObjectFunction.php");
include("common/iOLinkCommonFunction.php");
include("iolinkPreDefineMed.php");

$objManageData = new manageData;
$practiceName = getPracticeName($loginUser,'Coordinator');
$patient_in_waiting_id = $_REQUEST['patient_in_waiting_id'];
$patient_id = $_REQUEST['patient_id'];

if($_REQUEST['saveData']<>""){
	
	//START SAVE NO MEDICATION STATUS	
	unset($arrayNoMedData);
	$arrayNoMedData['iolink_no_medication_status'] 		= $_REQUEST["iolink_no_medication_status"];
	$arrayNoMedData['iolink_no_medication_comments'] 	= $_REQUEST["iolink_no_medication_comments"];
	$objManageData->updateRecords($arrayNoMedData, "patient_in_waiting_tbl", "patient_in_waiting_id", $patient_in_waiting_id);
	//END SAVE NO MEDICATION STATUS
	
	unset($arrayPatientMedData);
	for($intCountMedNameTextBoxSingle = 0; $intCountMedNameTextBoxSingle < 20; $intCountMedNameTextBoxSingle++){
		//if($_REQUEST["medicationName".$intCountMedNameTextBoxSingle]){			
				$medicationName = $_REQUEST["medicationName".$intCountMedNameTextBoxSingle];
				$medicationDosage = $_REQUEST["medicationDosage".$intCountMedNameTextBoxSingle];
				$medicationSig = $_REQUEST["medicationSig".$intCountMedNameTextBoxSingle];
				$medicationIdHidden = $_REQUEST["medicationIdHidden".$intCountMedNameTextBoxSingle];
				$medicationNameHidden = $_REQUEST["medicationNameHidden".$intCountMedNameTextBoxSingle];
				$medicationDosageHidden = $_REQUEST["medicationDosageHidden".$intCountMedNameTextBoxSingle];
				$medicationSigHidden = $_REQUEST["medicationSigHidden".$intCountMedNameTextBoxSingle];
				
				$arrayPatientMedData['prescription_medication_name'] = addslashes($medicationName);
				$arrayPatientMedData['prescription_medication_desc'] = addslashes($medicationDosage);
				$arrayPatientMedData['prescription_medication_sig'] = addslashes($medicationSig);
				$arrayPatientMedData['patient_id'] = $patient_id;
				$arrayPatientMedData['patient_in_waiting_id'] = $patient_in_waiting_id;
				if($medicationIdHidden=="" && $medicationNameHidden=="" && $medicationDosageHidden=="" && $medicationSigHidden=="" && $medicationName!=""){
					$resourceIdInsert = $objManageData->addRecords($arrayPatientMedData, 'iolink_patient_prescription_medication');	
				}
				else{
					
					if(!$medicationName) {
						$queryUpdateMed = "DELETE FROM iolink_patient_prescription_medication WHERE
											patient_id = '$patient_id' and
											patient_in_waiting_id = '$patient_in_waiting_id' and
											prescription_medication_id = '$medicationIdHidden'";
					}else {
						$queryUpdateMed = "Update iolink_patient_prescription_medication set prescription_medication_name='".addslashes($medicationName)."',
											prescription_medication_desc='".addslashes($medicationDosage)."',
											prescription_medication_sig='".addslashes($medicationSig)."' WHERE
											patient_id = '$patient_id' and
											patient_in_waiting_id = '$patient_in_waiting_id' and
											prescription_medication_id = '$medicationIdHidden'";
					}
					$queryUpdateMedSts = imw_query($queryUpdateMed) or die(imw_error());
				}
		//}
	}
	
}

//START GET Patient Data
$ptDataArr = $objManageData->get_patient_data($patient_id,"Concat(patient_lname,', ',patient_fname,' ',patient_mname) as patientName, date_of_birth");
$patientName= trim($ptDataArr["patientName"]);
$patientDOB = $objManageData->getDateFormat($ptDataArr["date_of_birth"],'/');
//END GET NO MEDICATION STATUS

//START GET NO MEDICATION STATUS
$noMedStatusQry = "SELECT iolink_no_medication_status,iolink_no_medication_comments from patient_in_waiting_tbl WHERE patient_in_waiting_id = '".$patient_in_waiting_id."'";
$noMedStatusRes = imw_query($noMedStatusQry) or die(imw_error());
if(imw_num_rows($noMedStatusRes)){
	$noMedStatusRow = imw_fetch_array($noMedStatusRes);
	$iolinkNoMedicationStatus 	= $noMedStatusRow["iolink_no_medication_status"];
	$iolinkNoMedicationComments = $noMedStatusRow["iolink_no_medication_comments"];
}
//END GET NO MEDICATION STATUS

$queryGetMed = "SELECT prescription_medication_id,prescription_medication_name,prescription_medication_desc,prescription_medication_sig from iolink_patient_prescription_medication WHERE patient_id = '$patient_id' and patient_in_waiting_id = '$patient_in_waiting_id'";
$queryGetMedRes = imw_query($queryGetMed) or die(imw_error());
$queryGetMedResNumRow = imw_num_rows($queryGetMedRes);
$intCount = 0;
$intCountNew = 0;


?>

<form name="medication" action="medication_popup.php" method="post" onClick="closeDivMed();">
	<input type="hidden" name="patient_id" id="patient_id" value="<?php echo $patient_id; ?>">
	<input type="hidden" name="patient_in_waiting_id" id="patient_in_waiting_id" value="<?php echo $patient_in_waiting_id; ?>">
	<input type="hidden" name="preDefineDivOpenClose" id="preDefineDivOpenClose" value="">
	<input type="hidden" name="saveData" id="saveData" value="Save">
	<table class="table_collapse alignCenter" style="background-color:#F1F4F0;">
		<tr>
			<td style="background-color: #cfe0e7; font-size: 13px; font-weight: 600; padding: 10px 5px;"><?php echo $patientName.($patientDOB?' - <small>' .$patientDOB.'</small>':'');?></td>
		</tr>
        <tr>
			<td>
            	<table class="table_collapse alignCenter" style="background-color:#F1F4F0;">
                	<tr>
                    	<td class="text_10b alignCenter" style="color:#800080;cursor:pointer;"  onClick="document.getElementById('iolinkPreDefineMedAdminDiv').style.display='block'; document.getElementById('preDefineDivOpenClose').value='open';">Medication</td>
                        <td class="alignCenter text_10" style="height:22px;"><input type="checkbox" name="iolink_no_medication_status" id="iolink_no_medication_status" value="Yes" onClick="clearMedVal(this)" <?php if($iolinkNoMedicationStatus == "Yes") { echo "checked"; }?>>No Medications</td>
                        <td class="alignCenter text_10" style="height:22px; white-space:nowrap;">Comments<span style="padding-left:5px;vertical-align:middle;"><textarea name="iolink_no_medication_comments" id="iolink_no_medication_comments" class="field text1" style="font-family:verdana; border:1px solid #B9B9B9;  height:22px; width:220px; "><?php echo $iolinkNoMedicationComments;?></textarea></span></td>
                    </tr>
                </table>
            </td>
		</tr>
		<tr>
			<td >
				<div style="height:400px; overflow-y:scroll; overflow-x:hidden;">
                <table class="table_collapse alignCenter" style="background-color:#F1F4F0;">
					<tr > 
						<td class="alignCenter text_10" style="height:22px;">Name</td>
						<td class="alignCenter text_10" style="height:22px;">Dosage</td>
                        <td class="alignCenter text_10" style="height:22px;">Sig</td>
					</tr>
						<?php if($queryGetMedResNumRow){
								while($queryGetMedRow = imw_fetch_array($queryGetMedRes)){
									$intPatientMedId = $queryGetMedRow['prescription_medication_id'];
									$strPatientMedName = $queryGetMedRow['prescription_medication_name'];
									$strPatientMedDosage = $queryGetMedRow['prescription_medication_desc'];
									$strPatientMedSig = $queryGetMedRow['prescription_medication_sig'];
								?>
								<tr>
									<td class="alignCenter">
                                        <input type="hidden" value="<?php echo stripslashes($intPatientMedId); ?>" name="medicationIdHidden<?php echo $intCountNew;?>" id="medication_IdHidden<?php echo $intCountNew;?>" />
                                        <input type="hidden" value="<?php echo stripslashes($strPatientMedName); ?>" name="medicationNameHidden<?php echo $intCountNew;?>" id="medication_nameHidden<?php echo $intCountNew;?>" />
                                        <input type="hidden" value="<?php echo stripslashes($strPatientMedDosage); ?>" name="medicationDosageHidden<?php echo $intCountNew;?>" id="medication_DosageHidden<?php echo $intCountNew;?>" />
                                        <input type="hidden" value="<?php echo stripslashes($strPatientMedSig); ?>" name="medicationSigHidden<?php echo $intCountNew;?>" id="medication_SigHidden<?php echo $intCountNew;?>" />
                                        <input type="text"  value="<?php echo stripslashes($strPatientMedName); ?>" name="medicationName<?php echo $intCountNew;?>" id="medication_name<?php echo $intCountNew;?>" class="field text1 noMedOn" style=" border:1px solid #B9B9B9; width:180px; white-space:nowrap; height:18px; font-size:12px;"   tabindex="1"/>
                                    </td>
									<td class="alignCenter"><input type="text" value="<?php echo stripslashes($strPatientMedDosage); ?>" name="medicationDosage<?php echo $intCountNew;?>" id="medication_Dosage<?php echo $intCountNew;?>" class="field text1 noMedOn" style=" border:1px solid #B9B9B9; width:180px; white-space:nowrap; height:18px; font-size:12px;" tabindex="1"  /></td>
                                    <td class="alignCenter"><input type="text" value="<?php echo stripslashes($strPatientMedSig); ?>" 	 name="medicationSig<?php echo $intCountNew;?>"    id="medication_Sig<?php echo $intCountNew;?>" 	class="field text1 noMedOn" style=" border:1px solid #B9B9B9; width:180px; white-space:nowrap; height:18px; font-size:12px;" tabindex="1"  /></td>
								</tr>
								<?php
								$intCountNew++;
								} 
							//}
							$intCountAfter = $intCountNew;
							if($intCountAfter < 20){
							for($intCount = $intCountAfter; $intCount < 20; $intCount++){			
								?>
									<tr>
										<td class="alignCenter">
                                            <input type="hidden" name="medicationIdHidden<?php echo $intCount;?>" id="medication_IdHidden<?php echo $intCount;?>" />
                                            <input type="hidden" name="medicationNameHidden<?php echo $intCount;?>" id="medication_nameHidden<?php echo $intCount;?>" />
                                            <input type="hidden" name="medicationDosageHidden<?php echo $intCount;?>" id="medication_DosageHidden<?php echo $intCount;?>" />
                                            <input type="hidden" name="medicationSigHidden<?php echo $intCount;?>" id="medication_SigHidden<?php echo $intCount;?>" />
                                            <input type="text"  name="medicationName<?php echo $intCount;?>" id="medication_name<?php echo $intCount;?>" class="field text noMedOn" style=" border:1px solid #B9B9B9; width:180px; white-space:nowrap; height:18px; font-size:12px;"   tabindex="1"/>
                                        </td>
										<td class="alignCenter"><input type="text"  name="medicationDosage<?php echo $intCount;?>" id="medication_Dosage<?php echo $intCount;?>" class="field text noMedOn" style=" border:1px solid #B9B9B9;  width:180px; white-space:nowrap; height:18px; font-size:12px;" tabindex="1"  /></td>
                                        <td class="alignCenter"><input type="text"  name="medicationSig<?php echo $intCount;?>"    id="medication_Sig<?php echo $intCount;?>" 	 class="field text noMedOn" style=" border:1px solid #B9B9B9;  width:180px; white-space:nowrap; height:18px; font-size:12px;" tabindex="1"  /></td>
									</tr>
								<?php 
								}
							}
						}
						elseif($intCountAfter < 20){
							for($intCount = 0; $intCount < 20; $intCount++){			
								?>
								<tr>
									<td class="alignCenter">
                                        <input type="hidden" name="medicationIdHidden<?php echo $intCount;?>" id="medication_IdHidden<?php echo $intCount;?>" />
                                        <input type="hidden" name="medicationNameHidden<?php echo $intCount;?>" id="medication_nameHidden<?php echo $intCount;?>" />
                                        <input type="hidden" name="medicationDosageHidden<?php echo $intCount;?>" id="medication_DosageHidden<?php echo $intCount;?>" />
                                        <input type="hidden" name="medicationSigHidden<?php echo $intCount;?>" id="medication_SigHidden<?php echo $intCount;?>" />
                                        <input type="text" name="medicationName<?php echo $intCount;?>" id="medication_name<?php echo $intCount;?>" class="field text noMedOn" style=" border:1px solid #B9B9B9;  width:180px; white-space:nowrap; height:18px;  font-size:12px;"   tabindex="1"/>
                                    </td>
									<td class="alignCenter"><input type="text" name="medicationDosage<?php echo $intCount;?>" id="medication_Dosage<?php echo $intCount;?>" class="field text noMedOn" style=" border:1px solid #B9B9B9; width:180px; white-space:nowrap; height:18px;  font-size:12px;" tabindex="1"  /></td>
                                    <td class="alignCenter"><input type="text" name="medicationSig<?php echo $intCount;?>" 	  id="medication_Sig<?php echo $intCount;?>" 	class="field text noMedOn" style=" border:1px solid #B9B9B9; width:180px; white-space:nowrap; height:18px;  font-size:12px;" tabindex="1"  /></td>
								</tr>
								<?php 
							}
						}
					?>
					
				</table>
                </div>
                
                <table class="table_collapse" >
                    <tr>
                        <td class="alignRight nowrap" style="width:250px; padding-top:10px;">
                            <a id="anchorShow" href="#" style="display:block;" onClick="MM_swapImage('saveBtn','','images/save_onclick1.jpg',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('saveBtn','','images/save_hover1.jpg',1)"><img src="images/save.jpg" id="saveBtn" style="border:none;" alt="save" onClick="document.medication.submit();"></a>
                            <!-- <input type="submit" value="Save" name="saveData" id="saveData"> -->
                        </td>
                        <td class="alignLeft" style=" padding-top:10px;" >
                            <a href="#" onClick="MM_swapImage('closeButton','','images/close_onclick1.gif',1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('closeButton','','images/close_hover.gif',1)"><img src="images/close.gif" id="closeButton" style="border:none;"  alt="Close" onClick="if(opener) { opener.top.iframeHome.iOLinkBookSheetFrameId.location.reload();}window.close();"/></a>
                        </td>
                    </tr>
                </table>
            
                
			</td>
		</tr>
	</table>				
</form>
</body>
</html>