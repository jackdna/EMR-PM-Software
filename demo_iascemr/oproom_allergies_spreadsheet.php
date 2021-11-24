<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include("common/linkfile.php");
?>
<form name="frm_oproom_allergies_spreadsheet" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px; " action=""> 	

	<table  align="left" width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="<?php echo $bglight_blue_local_anes; ?>">
		<?php for($i_oproom=1;$i_oproom<=10;$i_oproom++) { ?> 
		 <tr  style="padding-left:1; ">
			<td height="22">&nbsp;</td>
		  <td colspan="5" height="22" class="text_10b" align="left"><input type="text" id="allergies_oproom_name<?php echo $i_oproom;?>" class="field text" style=" border:1px solid #ccccc; width:228px; height:22px; "   tabindex="1" value=""  /><input type="text" id="allergies_oproom_reaction<?php echo $i_oproom;?>" class="field text" style=" border:1px solid #ccccc; width:228px; height:22px;" tabindex="1" value=""  /></td>
		</tr>
		<?php } ?>
	</table>
</form>	
