<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(500);
include_once("common/conDb.php");
//include_once("common/commonFunctions.php");

$tdate = date('Y-m-d');
if(!$surgeonId) {
	$surgeonId = $_REQUEST['surgeonId'];
}
$getConfirmationDetailQry = "SELECT * FROM `patientconfirmation` WHERE surgeonId='".$surgeonId."' AND surgeonId!='' AND dos <= '".$tdate."' ORDER BY dos DESC";

$getConfirmationDetailRes = imw_query($getConfirmationDetailQry) or die(imw_error()); 
$getConfirmationDetailNumRow = imw_num_rows($getConfirmationDetailRes);
?>
<style>
.text_10 { font-family:"verdana"; font-size:14px; color:#000000; font-weight:normal;}
.text_10b { font-family:"verdana"; font-size:14px; color:#000000; font-weight:bold;  }

</style>
<form name="frmShowNotSign" method="post" action="test_show_not_sign.php">
	<center><span class="text_10" style="white-space:nowrap ">Select Surgeon&nbsp;&nbsp;
		<select name="surgeonId" class="text_10" onChange="javascript:document.frmShowNotSign.submit();">
			<option value="">Select Surgeon</option>
			<?php
			$getSurgeonQry = "SELECT * FROM `users` WHERE user_type='Surgeon' ORDER BY lname";
			
			$getSurgeonRes = imw_query($getSurgeonQry) or die(imw_error()); 
			$getSurgeonNumRow = imw_num_rows($getSurgeonRes);
			if($getSurgeonNumRow>0) {
				while($getSurgeonRow = imw_fetch_array($getSurgeonRes)) {
					$userSurgeonId = $getSurgeonRow['usersId'];
					$userSurgeonFName = $getSurgeonRow['fname'];
					$userSurgeonMName = $getSurgeonRow['mname'];
					$userSurgeonLName = $getSurgeonRow['lname'];
					if($userSurgeonMName) {
						$userSurgeonMName = ' '.$userSurgeonMName;
					}
					$userSurgeonName = $userSurgeonLName.', '.$userSurgeonFName.$userSurgeonMName;
					
				?>
					<option value="<?php echo $userSurgeonId; ?>" <?php if($surgeonId==$userSurgeonId) { echo "selected";}?>><?php echo stripslashes($userSurgeonName); ?></option>
				<?php
				}
			}
			?>
		</select>
	</span>	</center>
</form> 	
<?php
echo "<h3 class='text_10b'><center>List of record not signed by surgeon</center></h3>";
if($getConfirmationDetailNumRow>0) {
	$cntr=0;
	while($getConfirmationDetailRow = imw_fetch_array($getConfirmationDetailRes)) { 
		$patient_confID = $getConfirmationDetailRow['patientConfirmationId'];
		$patient_primary_procedure_id =  $getConfirmationDetailRow['patient_primary_procedure_id'];
		$patient_primary_procedure =  $getConfirmationDetailRow['patient_primary_procedure'];
		$ascId = $getConfirmationDetailRow['ascId'];
		
		//GET CATEGORYID OF PROCEDURE
		$getProcedureDetailsQry = "select * from  procedures WHERE procedureId='".$patient_primary_procedure_id."' AND procedureId!=''";
		$getProcedureDetailsRes = imw_query($getProcedureDetailsQry) or die(imw_error()); 
		$getProcedureDetailsNumRow = imw_num_rows($getProcedureDetailsRes);
		if($getProcedureDetailsNumRow>0) {
			$getProcedureDetailsRow = imw_fetch_array($getProcedureDetailsRes);
			$procedureCatId = $getProcedureDetailsRow['catId'];
		}
		//END GET CATEGORYID OF PROCEDURE
		
		if($procedureCatId!='2') { //IF PROCEDURE CATEGORY IS NOT 'LASER PROCEDURE' THEN DISPLAY RECORD
			$stubTblQry = "SELECT * FROM `stub_tbl` WHERE patient_confirmation_id='".$patient_confID."' AND chartSignedBySurgeon!='Yes' AND patient_status!='Canceled'";
			$stubTblRes = imw_query($stubTblQry) or die(imw_error()); 
			$stubTblNumRow = imw_num_rows($stubTblRes);
		
			if($stubTblNumRow>0) {
				$cntr++;
				$stubTblRow = imw_fetch_array($stubTblRes);
				$stub_patient_confirmation_id = $stubTblRow['patient_confirmation_id'];
				$patient_dosTemp = $stubTblRow['dos'];
				$patient_dos = date("m-d-y",strtotime($patient_dosTemp));
				$patientFName = $stubTblRow['patient_first_name'];
				$patientMName = $stubTblRow['patient_middle_name'];
				$patientLName = $stubTblRow['patient_last_name'];
				if($patientMName) {
					$patientMName = ' '.$patientMName;
				}
				$patientName = $patientLName.', '.$patientFName.$patientMName;
	
				$surgeonFName = $stubTblRow['surgeon_fname'];
				$surgeonMName = $stubTblRow['surgeon_mname'];
				$surgeonLName = $stubTblRow['surgeon_lname'];
				if($surgeonMName) {
					$surgeonMName = ' '.$surgeonMName;
				}
				$surgeonName = $surgeonLName.', '.$surgeonFName.$surgeonMName;
			?>
				<table border="0" cellpadding="1" cellspacing="1" width="60%" class="text_10">
					<tr >
						<td width="10%"><?php echo $cntr;?></td>
						<td width="20%"><?php echo $patient_dos;?></td>
						<td width="30%" nowrap><?php echo $surgeonName;?></td>
						<td width="50%"></td>
					</tr>
					
					<tr>
						<td></td>
						<td><?php if($ascId) { echo $ascId; }?></td>
						<td class="text_10b" nowrap><?php echo $patientName;?></td>
						<td><?php echo $patient_primary_procedure;?></td>
					</tr>
					<tr>
						<td colspan="4" height="5">&nbsp;</td>
					</tr>
				</table>
			<?php	
			}
		}	
	}	
}

?>