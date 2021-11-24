<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 

session_start();
require_once('conDb.php'); 
$patient_id = $_SESSION['patient_id'];

//START GET PATIENT DETAIL
	$addressPatientDataTblQry = "SELECT * FROM `patient_data_tbl` WHERE `patient_id` = '".$patient_id."'";
	$addressPatientDataTblRes = imw_query($addressPatientDataTblQry) or die(imw_error());
	$addressPatientDataTblNumRow = imw_num_rows($addressPatientDataTblRes);
	if($addressPatientDataTblNumRow>0) {
		$addressPatientDataTblRow = imw_fetch_array($addressPatientDataTblRes);
		$addressPatientstreet1 	= addslashes($addressPatientDataTblRow['street1']);
		$addressPatientstreet2 	= addslashes($addressPatientDataTblRow['street2']);	
		$addressPatientcity 	= addslashes($addressPatientDataTblRow['city']);	
		$addressPatientstate 	= $addressPatientDataTblRow['state'];	
		$addressPatientzip 		= $addressPatientDataTblRow['zip'];	
	}
//END GET PATIENT DETAIL

?>
	
<div id="headerAddressId" style="position:absolute;left:80px;top:60px;background-color:#FFFF99;height:1px; display:none;">
	<table class="table_pad_bdr" style="border:none; width:190px; background-color:#FFFF99;">
		<tr style="height:15px;">	
			<td class="text_printb alignLeft valignMiddle">
				<?php echo $addressPatientstreet1;?>
			</td>
		</tr>
		<?php
		if($addressPatientstreet2) {
		?>
		<tr style="height:15px;">	
			<td class="text_printb alignLeft valignMiddle">
				<?php echo $addressPatientstreet2;?>
			</td>
		</tr>
		<?php
		}
		?>
		<tr style="height:15px;">	
			<td class="text_printb alignLeft valignMiddle">
				<?php echo stripslashes($addressPatientcity.",".$addressPatientstate." ".$addressPatientzip);?>
			</td>	
		</tr>
	</table>
</div>

<?php
//START CODE TO DISPLAY FULL PRIMARY AN SECONDARY PROCEDURE NAME IN DIV ON-MOUSE-OVER AND OUT 
if($primProcFullNameForDiv) {
?>
<div id="headerPrimProcId" style="position:absolute;left:300px;top:90px;background-color:#FFFF99; display:none;">
	<table class="table_pad_bdr" style="border:none; width:230px; background-color:#FFFF99;">
		
		<tr style="height:15px;">	
			<td class="text_printb alignLeft valignMiddle">
				<?php echo $primProcFullNameForDiv;?>
			</td>
		</tr>
	</table>
</div>
<?php
}
if($secProcFullNameForDiv) {
?>

<div id="headerSecProcId" style="position:absolute;left:580px;top:90px;background-color:#FFFF99;height:1px; display:none;">
	<table class="table_pad_bdr" style="border:none; width:230px; background-color:#FFFF99;">
		
		<tr style="height:15px;">	
			<td class="text_printb alignLeft valignMiddle">
				<?php echo $secProcFullNameForDiv;?>
			</td>
		</tr>
	</table>
</div>
<?php
}
//END CODE TO DISPLAY FULL PRIMARY AN SECONDARY PROCEDURE NAME IN DIV ON-MOUSE-OVER AND OUT 
?>
