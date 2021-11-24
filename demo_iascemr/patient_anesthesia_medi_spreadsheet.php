<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include_once("common/conDb.php");
//include_once("admin/classObjectFunction.php");
$objectMenageData = new manageData;
if(!$patient_id) {
	$patient_id = $_REQUEST['patient_id'];
}
if(!$pConfId) {
	$pConfId = $_REQUEST['pConfId'];
}
if(!$sprdMedication) {
	$sprdMedication = $_REQUEST["sprdMedication"];
}

if($_REQUEST['submitMe']=='true' || $_REQUEST['saveRecord']=='true'){
	$medicationName = $_REQUEST['medicationName'];
	$medicationDetail = $_REQUEST['medicationDetail'];
	$medicationSig = $_REQUEST['medicationSig'];
	$medicationId = $_REQUEST['medicationId'];
	foreach($medicationName as $Key => $medications){
		if($medications!=''){
			$medicationsArr['confirmation_id'] = $pConfId;
			$medicationsArr['patient_id'] = $patient_id;
			$medicationsArr['prescription_medication_name'] = addslashes($medications);
			$medicationsArr['prescription_medication_desc'] = addslashes($medicationDetail[$Key]);
			$medicationsArr['prescription_medication_sig'] = addslashes($medicationSig[$Key]);
			$medicationsArr['operator_name'] = $_SESSION['loginUserName'];
			$medicationsArr['operator_id'] = $_SESSION['loginUserId'];
			
			
			if($medicationId[$Key]){
				$chkMedicationDetails = $objectMenageData->getArrayRecords('patient_anesthesia_medication_tbl', 'prescription_medication_id', $medicationId[$Key]);
				if(count($chkMedicationDetails)>0) {
					$objectMenageData->updateRecords($medicationsArr, 'patient_anesthesia_medication_tbl', 'prescription_medication_id', $medicationId[$Key]);
				}else {
					$objectMenageData->addRecords($medicationsArr, 'patient_anesthesia_medication_tbl');
				}
			}else{
				$objectMenageData->addRecords($medicationsArr, 'patient_anesthesia_medication_tbl');
			}
		}else if($medicationId[$Key]){
			$objectMenageData->delRecord('patient_anesthesia_medication_tbl', 'prescription_medication_id', $medicationId[$Key]);
		}
	}
}
//GETTING ALLERGIES REACTIONS TO DISPLAY
	$getMedicationDetails = $objectMenageData->getArrayRecords('patient_anesthesia_medication_tbl', 'confirmation_id', $pConfId,'prescription_medication_name','ASC');

	//GET PREFIL VALUES FOR MAC REGIONAL ANESTHESIA IF RECORD YET TO SAVE AND MEDICATION FOR PATIENT (SAVED RECORD) IS BLANK 
	if($tablename == "localanesthesiarecord" && $localanesFormStatus=="" && count($getMedicationDetails)=='0') {
		if($logInUserType=='Anesthesiologist') {
			$userIdLocal = $_SESSION['loginUserId'];
		}else {
			$userIdLocal = $patientConfirm_anesthesiologist_id;
		}
		$medicationAnesthesiaProfile = $objectMenageData->getRowRecord('anesthesia_profile_tbl', 'anesthesiologistId ', $userIdLocal);
		if($medicationAnesthesiaProfile) {	
			$anesthesiaProfileSignMed = $medicationAnesthesiaProfile->anesthesia_profile_sign;
			$anesthesiaProfileSignMedPath = $medicationAnesthesiaProfile->anesthesia_profile_sign_path;
			if($anesthesiaProfileSignMed || $anesthesiaProfileSignMedPath) {
				$medsTakeTodayAdmin = $medicationAnesthesiaProfile->medsTakeToday;
				$ptMedicationAdmin = $medicationAnesthesiaProfile->ptMedication;
				if($medsTakeTodayAdmin=='Yes' && $ptMedicationAdmin=='Yes') {
					$getMedicationDetail1 = $objectMenageData->getArrayRecords('patient_prescription_medication_tbl', 'confirmation_id', $pConfId,'prescription_medication_name','ASC');
					$getMedicationDetail2 = $objectMenageData->getArrayRecords('patient_prescription_medication_healthquest_tbl', 'confirmation_id', $pConfId,'prescription_medication_name','ASC');
				}
				elseif($medsTakeTodayAdmin=='Yes') {//PRE-FILL VALUES FROM PRE-OP NURSING SPREADSHEET
					$getMedicationDetails = $objectMenageData->getArrayRecords('patient_prescription_medication_tbl', 'confirmation_id', $pConfId,'prescription_medication_name','ASC');
				}else if($ptMedicationAdmin=='Yes') {//PRE-FILL VALUES FROM PRE-OP HEALTH QUESTIONNAIRE SPREADSHEET
					$getMedicationDetails = $objectMenageData->getArrayRecords('patient_prescription_medication_healthquest_tbl', 'confirmation_id', $pConfId,'prescription_medication_name','ASC');
				}
			}
		}	
			
	}
	
	//START GET PREFIL VALUES FOR HISTORY AND PHYSICIAL IF RECORD YET TO SAVE AND MEDICATION FOR PATIENT (SAVED RECORD) IS BLANK 
	if($tablename == "history_physicial_clearance" && $form_status=="" && count($getMedicationDetails)=='0') {
		$getMedicationDetail1 = $objectMenageData->getArrayRecords('patient_prescription_medication_tbl', 'confirmation_id', $pConfId,'prescription_medication_name','ASC');
		$getMedicationDetail2 = $objectMenageData->getArrayRecords('patient_prescription_medication_healthquest_tbl', 'confirmation_id', $pConfId,'prescription_medication_name','ASC');
	}
	//END GET PREFIL VALUES FOR HISTORY AND PHYSICIAL IF RECORD YET TO SAVE AND MEDICATION FOR PATIENT (SAVED RECORD) IS BLANK 
	
	/*
	if(count($getMedicationDetails)<=0){
		$getMedicationDetails = $objectMenageData->getArrayRecords('patient_prescription_medication_tbl', 'confirmation_id', $pConfId,'prescription_medication_id','ASC');
	}*/
	//END GET PREFIL VALUES FOR MAC REGIONAL ANESTHESIA IF RECORD YET TO SAVE
	/*if($medsTakeTodayAdmin=='Yes' && $ptMedicationAdmin=='Yes') {
		$getMedicationDetailMerge = array_merge($getMedicationDetail1,$getMedicationDetail2);
		$getMedicationDetails = array_unique($getMedicationDetailMerge);
	}*/
	$medication_name=array();
	if(count($getMedicationDetail1)>0){
		foreach($getMedicationDetail1 as $medicationName){
			++$med_seq;
			$medication_id[$med_seq] = $medicationName->prescription_medication_id;
			$medication_name[$med_seq] = $medicationName->prescription_medication_name;
			$medicationDetails[$med_seq] = $medicationName->prescription_medication_desc;
			$medication_sig[$med_seq] = $medicationName->prescription_medication_sig;
		}
	}
	if(count($getMedicationDetail2)>0){
		foreach($getMedicationDetail2 as $medicationName){
			if(!in_array($medicationName->prescription_medication_name,$medication_name)){
				++$med_seq;
				$medication_id[$med_seq] = $medicationName->prescription_medication_id;
				$medication_name[$med_seq] = $medicationName->prescription_medication_name;
				$medicationDetails[$med_seq] = $medicationName->prescription_medication_desc;
				$medication_sig[$med_seq] = $medicationName->prescription_medication_sig;
			}
		}
	}
	
	if(count($getMedicationDetails)>0){
		foreach($getMedicationDetails as $medicationName){
			++$med_seq;
			$medication_id[$med_seq] = $medicationName->prescription_medication_id;
			$medication_name[$med_seq] = $medicationName->prescription_medication_name;
			$medicationDetails[$med_seq] = $medicationName->prescription_medication_desc;
			$medication_sig[$med_seq] = $medicationName->prescription_medication_sig;
		}
	}
//GETTING ALLERGIES REACTIONS TO DISPLAY
$total_rows_in_medication = count($medication_name)+20;

?>
<!-- <form action="patient_prescription_medi_spreadsheet.php?submitMe=true" name="frm_health_quest_medication_spreadsheet" method="post" style="margin:0px;"> -->
    <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
    <input type="hidden" name="pConfId" value="<?php echo $pConfId; ?>">
    <input type="hidden" name="hidd_count_rows_in_medication_table" id="hidd_count_rows_in_medication_table" value="<?php echo($total_rows_in_medication); ?>">	
	<table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped" style="background-color:#F1F4F0;" >
		<?php for($i_healthquest_med=1;$i_healthquest_med<=$total_rows_in_medication;$i_healthquest_med++) { 
				if(!$medicNameWidth) {
					$medicNameWidth = $_REQUEST["medicNameWidth"];
				}	
				if($medicNameWidth=="") { $medicNameWidth = 218; }
				
				if(!$medicDetailWidth) {
					$medicDetailWidth = $_REQUEST["medicDetailWidth"];
				}	
				if($medicDetailWidth=="") { $medicDetailWidth = 218; }
					
				if(trim($medication_name[$i_healthquest_med])=="") {$txtBorderName="0px"; /*if($i_healthquest_med==1){$txtBorderName="1px";}*/ } else { $txtBorderName="1px"; }
				if(trim($medicationDetails[$i_healthquest_med])=="") {$txtBorderDetail="0px"; /*if($i_healthquest_med==1){$txtBorderDetail="1px";}*/ } else { $txtBorderDetail="1px"; }
		?> 			
			
			<tr style="padding-left:0; background-color:#FFFFFF; "> <!--  bgcolor="#F1F4F0"-->
                <td class="col-md-5 col-lg-5 col-sm-5 col-xs-5">
                <input type="hidden" name="medicationId[]" value="<?php echo $medication_id[$i_healthquest_med]; ?>">
                <input type="text" class="form-control" value="<?php echo stripslashes($medication_name[$i_healthquest_med]); ?>" name="medicationName[]" id="medication_name<?php echo $i_healthquest_med;?>"  tabindex="1"/>
                <td class="col-md-3 col-lg-3 col-sm-3 col-xs-3"><input type="text" class="form-control" value="<?php echo stripslashes($medicationDetails[$i_healthquest_med]); ?>" name="medicationDetail[]" id="medication_detail<?php echo $i_healthquest_med;?>" tabindex="1"/></td>
                <td class="col-md-4 col-lg-4 col-sm-4 col-xs-4"><input type="text" class="form-control" value="<?php echo stripslashes($medication_sig[$i_healthquest_med]); ?>" name="medicationSig[]" id="medication_sig<?php echo $i_healthquest_med;?>"  tabindex="1"  /></td>
            </tr>
			
            
            <!--<tr style="background-color:#FFF;padding-left:0px; "> 
				<td style="height:22px; padding-left:4px;"><input type="hidden" name="medicationId[]" value="<?php echo $medication_id[$i_healthquest_med]; ?>"></td>
			 	 <td style="height:22px;" class="text_10b alignLeft">
				 	<table class="table_pad_bdr">
						<tr>
							<td class="text_10b"><input type="text" value="<?php echo stripslashes($medication_name[$i_healthquest_med]); ?>" name="medicationName[]" id="medication_name<?php echo $i_healthquest_med;?>" class="field text" style=" border:<?php echo $txtBorderName;?>; border-color:#CCCCCC; border-style:solid; width:<?php echo $medicNameWidth;?>px; height:22px; "   tabindex="1"/></td>
							<td class="text_10b"><input type="text" value="<?php echo stripslashes($medicationDetails[$i_healthquest_med]); ?>" name="medicationDetail[]" id="medication_detail<?php echo $i_healthquest_med;?>" class="field text" style=" border:<?php echo $txtBorderDetail;?>; border-color:#CCCCCC; border-style:solid; width:<?php echo $medicDetailWidth;?>px; height:22px;" tabindex="1"  /></td>
						</tr>
					</table>			  		
				</td>
			</tr>-->
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
	//start print emr
	$healthHeight='10';
	if($sect=="print_emr") {
		if(count($medication_name)>0) {
			$healthHeight=(count($medication_name)*24);
		}?>
		<script>
            var healthHeight = '<?php echo $healthHeight;?>';
            if(document.getElementById('iframe_medication_pre_op_gen_anes')) {
                document.getElementById('iframe_medication_pre_op_gen_anes').style.height=healthHeight+'px';
            }
			if(document.getElementById('iframe_medication_local_anes_rec')) {
                document.getElementById('iframe_medication_local_anes_rec').style.height=healthHeight+'px';
            }
			if(document.getElementById('iframe_medication_gen_anes_rec')) {
                document.getElementById('iframe_medication_gen_anes_rec').style.height=healthHeight+'px';
            }
        </script>
	<?php	
	}
	//end print emr	
	
?>
<script>

</script>