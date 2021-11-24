<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include("common/linkfile.php");

$count_patientconf_allerg_sprd = 10;
?>
<!-- <form name="frm_patient_confirm_spreadsheet" id="frm_patient_confirm_spreadsheet" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px; " action="patient_confirm_spreadsheet.php"> -->
	<table align="left" width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#F1F4F0">
		<?php for($i_patientconf_allerg=1;$i_patientconf_allerg<=$count_patientconf_allerg_sprd;$i_patientconf_allerg++) { ?> 
				<tr bgcolor="#F1F4F0" style="padding-left:8; ">
					<td height="22">&nbsp;</td>
				  <td colspan="5" height="22" class="text_10b" align="left"><input type="text" name="Allergies_conf<?php echo $i_patientconf_allerg;?>" id="Allergies_conf<?php echo $i_patientconf_allerg;?>" class="field text" style=" border:1px solid #ccccc; width:265px; height:22px; "   tabindex="1" value=""  /><input type="text" name="Reaction_conf<?php echo $i_patientconf_allerg;?>" id="Reaction_conf<?php echo $i_patientconf_allerg;?>" class="field text" style=" border:1px solid #ccccc; width:265px; height:22px;" tabindex="1" value=""  /></td>
				</tr>
		<?php } ?>
	</table>
	<input type="hidden" name="count_patientconf_allerg_sprd" value="<?php echo $count_patientconf_allerg_sprd;?>">
	<input type="hidden" name="patient_sprd_submit" value="yes">
<!-- </form> -->
