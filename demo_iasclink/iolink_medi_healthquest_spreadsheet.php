<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
include_once("common/conDb.php");
//include_once("admin/classObjectFunction.php");
$objectMenageData = new manageData;

$patient_id = $_REQUEST['patient_id'];
$patient_in_waiting_id = $_REQUEST['patient_in_waiting_id'];
$pConfId = $_REQUEST['pConfId'];
if(!$sprdMedication) {
	$sprdMedication = $_REQUEST["sprdMedication"];
}	
if($_REQUEST['submitMe']=='true' || $_REQUEST['saveRecord']=='true'){
	$medicationName = $_REQUEST['medicationName'];
	$medicationDetail = $_REQUEST['medicationDetail'];
	$medicationSig = $_REQUEST['medicationSig'];
	$medicationId = $_REQUEST['medicationId'];
	foreach($medicationId as $Key => $medicationsId){
	//foreach($medicationName as $Key => $medications){
		if($medicationName[$Key]!=''){
			//$medicationsArr['confirmation_id'] = $pConfId;
			$medicationsArr['patient_id'] = $patient_id;
			$medicationsArr['patient_in_waiting_id'] = $patient_in_waiting_id;
			$medicationsArr['prescription_medication_name'] = addslashes($medicationName[$Key]);
			$medicationsArr['prescription_medication_desc'] = addslashes($medicationDetail[$Key]);
			$medicationsArr['prescription_medication_sig'] = addslashes($medicationSig[$Key]);
			$medicationsArr['operator_name'] = $_SESSION['iolink_loginUserName'];
			$medicationsArr['operator_id'] = $_SESSION['iolink_loginUserId'];
			if($medicationId[$Key]){
				$objectMenageData->updateRecords($medicationsArr, 'iolink_patient_prescription_medication', 'prescription_medication_id', $medicationId[$Key]);
			}else{
				$objectMenageData->addRecords($medicationsArr, 'iolink_patient_prescription_medication');
			}
		}else if($medicationId[$Key]){
			$objectMenageData->delRecord('iolink_patient_prescription_medication', 'prescription_medication_id', $medicationId[$Key]);
		}
	}
}
//GETTING ALLERGIES REACTIONS TO DISPLAY
	$getMedicationDetails = $objectMenageData->getArrayRecords('iolink_patient_prescription_medication', 'patient_in_waiting_id', $patient_in_waiting_id);
	if(count($getMedicationDetails)>0){
		foreach($getMedicationDetails as $medicationName){
			++$iolink_med_seq;
			$medication_id[$iolink_med_seq] = $medicationName->prescription_medication_id;
			$medication_name[$iolink_med_seq] = $medicationName->prescription_medication_name;
			$medicationDetails[$iolink_med_seq] = $medicationName->prescription_medication_desc;
			$medication_sig[$iolink_med_seq] = $medicationName->prescription_medication_sig;
		}
	}
//GETTING ALLERGIES REACTIONS TO DISPLAY

?>
<!-- <form  action="patient_prescription_medi_spreadsheet.php?submitMe=true" name="frm_health_quest_medication_spreadsheet" method="post" style="margin:0px;"> -->
	<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
	<input type="hidden" name="pConfId" value="<?php echo $pConfId; ?>">
	<input type="hidden" name="patient_in_waiting_id" value="<?php echo $patient_in_waiting_id; ?>">
	<table class="table_collapse alignLeft" style="border:none; background-color:#F1F4F0;">
		<?php for($i_healthquest_med=1;$i_healthquest_med<=20;$i_healthquest_med++) { 
				if(!$medicNameWidth) {
					$medicNameWidth = $_REQUEST["medicNameWidth"];
				}	
				if($medicNameWidth=="") { $medicNameWidth = 218; }
				
				if(!$medicDetailWidth) {
					$medicDetailWidth = $_REQUEST["medicDetailWidth"];
				}	
				if($medicDetailWidth=="") { $medicDetailWidth = 218; }
				$txtBorderName = $txtBorderDetail = $txtBorderSig = "";
				if(trim($medication_name[$i_healthquest_med])=="") {$txtBorderName="0px"; /*if($i_healthquest_med==1){$txtBorderName="1px";}*/ } else { $txtBorderName="1px"; }
				if(trim($medicationDetails[$i_healthquest_med])=="") {$txtBorderDetail="0px"; /*if($i_healthquest_med==1){$txtBorderDetail="1px";}*/ } else { $txtBorderDetail="1px"; }
				if(trim($medication_sig[$i_healthquest_med])=="") {$txtBorderSig="0px"; /*if($i_healthquest_med==1){$txtBorderDetail="1px";}*/ } else { $txtBorderDetail="1px"; }
		?> 			
			<tr style="padding-left:0; background-color:#FFFFFF; "> <!--  bgcolor="#F1F4F0"-->
				<td style="height:22px; padding-left:4px;"><input type="hidden" name="medicationId[]" value="<?php echo $medication_id[$i_healthquest_med]; ?>"></td>
			 	 <td class="text_10b alignLeft" style="height:22px;">
                    <table style="border:none; border-collapse:collapse;">
						<tr>
							<td><input type="text" value="<?php echo stripslashes($medication_name[$i_healthquest_med]); ?>" name="medicationName[]" id="medication_name<?php echo $i_healthquest_med;?>" class="field text noMedOn" style=" border:<?php echo $txtBorderName;?>; border-color:#CCCCCC; border-style:solid; width:<?php echo $medicNameWidth;?>px; height:22px; "   tabindex="1"/></td>
							<td><input type="text" value="<?php echo stripslashes($medicationDetails[$i_healthquest_med]); ?>" name="medicationDetail[]" id="medication_detail<?php echo $i_healthquest_med;?>" class="field text noMedOn" style=" border:<?php echo $txtBorderDetail;?>; border-color:#CCCCCC; border-style:solid; width:<?php echo $medicDetailWidth;?>px; height:22px;" tabindex="1"  /></td>
                            <td><input type="text" value="<?php echo stripslashes($medication_sig[$i_healthquest_med]); ?>" name="medicationSig[]" id="medication_sig<?php echo $i_healthquest_med;?>" class="field text noMedOn" style=" border:<?php echo $txtBorderSig;?>; border-color:#CCCCCC; border-style:solid; width:<?php echo $medicDetailWidth;?>px; height:22px;" tabindex="1"  /></td>
						</tr>
					</table>			  		
				</td>
			</tr>
		<?php } ?>
	</table>
<!-- </form> -->
<?php
	if($sprdMedication=='No'){
		?>
		<script>
			obj = document.getElementsByName('medicationName[]');
			obj1 = document.getElementsByName('medicationDetail[]');
			obj2 = document.getElementsByName('medicationSig[]');
			var len = obj.length;
			for(i=0;i<len;i++){
				obj[i].disabled = true;
				obj1[i].disabled = true;
				obj2[i].disabled = true;
			}
		</script>
		<?php
	}
?>