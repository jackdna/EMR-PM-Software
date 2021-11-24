<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include_once("common/conDb.php");
include("common/linkfile.php");
include_once("admin/classObjectFunction.php");
$objectMenageData = new manageData;
$patient_id = $_REQUEST['patient_id'];
$ascId = $_REQUEST['ascId'];
$pConfId = $_REQUEST['pConfId'];
$sprdMedication = $_REQUEST["sprdMedication"];

if($_REQUEST['submitMe']){
	$medicationName = $_REQUEST['medicationName'];
	$medicationDetail = $_REQUEST['medicationDetail'];
	$medicationId = $_REQUEST['medicationId'];
	foreach($medicationName as $Key => $medications){
		if($medications!=''){
			$medicationsArr['patient_confirmation_id'] = $pConfId;
			$medicationsArr['asc_id'] = $ascId;
			$medicationsArr['patient_id'] = $patient_id;
			$medicationsArr['prescription_medication_name'] = $medications;
			$medicationsArr['prescription_medication_desc'] = $medicationDetail[$Key];
			//$medicationsArr['operator_name'] = $_SESSION['loginUserName'];
			//$medicationsArr['operator_id'] = $_SESSION['loginUserId'];
			if($medicationId[$Key]){
				$objectMenageData->updateRecords($medicationsArr, 'patient_medication_tbl', 'medication_id ', $medicationId[$Key]);
			}else{
				$objectMenageData->addRecords($medicationsArr, 'patient_medication_tbl');
			}
		}else if($medicationId[$Key]){
			$objectMenageData->delRecord('patient_medication_tbl', 'medication_id', $medicationId[$Key]);
		}
	}
}
//GETTING ALLERGIES REACTIONS TO DISPLAY
	$getMedicationDetails = $objectMenageData->getArrayRecords('patient_medication_tbl', 'patient_confirmation_id', $pConfId);
	if(count($getMedicationDetails)>0){
		foreach($getMedicationDetails as $medicationName){
			++$pre_nursing_seq;
			$medication_id[$pre_nursing_seq] = $medicationName->medication_id;
			$medication_name[$pre_nursing_seq] = $medicationName->prescription_medication_name;
			$medicationDetails[$pre_nursing_seq] = $medicationName->prescription_medication_desc;
		}
	}
//GETTING ALLERGIES REACTIONS TO DISPLAY

?>
<form  action="health_quest_medication_spreadsheet.php?submitMe=true" name="frm_health_quest_medication_spreadsheet" enctype="multipart/form-data" method="post" style="margin:0px;">
	<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
	<input type="hidden" name="pConfId" value="<?php echo $pConfId; ?>">
	<input type="hidden" name="ascId" value="<?php echo $ascId; ?>">	
	<table align="left" width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#F1F4F0">
		<?php for($i_healthquest_med=1;$i_healthquest_med<=10;$i_healthquest_med++) { 
				$medicNameWidth = $_REQUEST["medicNameWidth"];
					if($medicNameWidth=="") { $medicNameWidth = 218; }
				
				$medicDetailWidth = $_REQUEST["medicDetailWidth"];
					if($medicDetailWidth=="") { $medicDetailWidth = 218; }
					if(trim($medication_name[$i_healthquest_med])=="") {$txtBorderName="0px"; } else { $txtBorderName="1px"; }
					if(trim($medicationDetails[$i_healthquest_med])=="") {$txtBorderDetail="0px"; } else { $txtBorderDetail="1px"; }
					
		?> 			
			<input type="hidden" name="medicationId[]" value="<?php echo $medication_id[$i_healthquest_med]; ?>">
			<tr bgcolor="#FFFFFF"  style="padding-left:0; "> <!--  bgcolor="#F1F4F0"-->
				<td height="22">&nbsp;</td>
			 	 <td colspan="5" height="22" class="text_10b" align="left">
				 	<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td><input type="text" value="<?php echo $medication_name[$i_healthquest_med]; ?>" name="medicationName[]" id="medication_name<?php echo $i_healthquest_med;?>" class="field text" style=" border:<?php echo $txtBorderName;?> solid #ccccc; width:<?php echo $medicNameWidth;?>px; height:22px; "   tabindex="1"/></td>
							<td><input type="text" value="<?php echo $medicationDetails[$i_healthquest_med]; ?>" name="medicationDetail[]" id="medication_detail<?php echo $i_healthquest_med;?>" class="field text" style=" border:<?php echo $txtBorderDetail;?> solid #ccccc; width:<?php echo $medicDetailWidth;?>px; height:22px;" tabindex="1"  /></td>
						</tr>
					</table>			  		
				</td>
			</tr>
		<?php } ?>
	</table>
</form>

<?php
	if($sprdMedication=='No'){
		?>
		<script>
			
			obj = document.getElementsByName('medicationName[]');
			obj1 = document.getElementsByName('medicationDetail[]');
			var len = obj.length;
			for(i=0;i<len;i++){
				obj[i].disabled = true;
				obj[i].value='';
				obj1[i].disabled = true;
				obj1[i].value='';
			}
		</script>
		<?php
	}
?>