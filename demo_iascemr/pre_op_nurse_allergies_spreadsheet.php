<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include("common/linkfile.php");
?>
 <form name="frm_pre_op_nurse_allergies_spreadsheet" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px; " action="">

<table  align="left" width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="<?php echo $rowcolor_pre_op_nursing_order; ?>">
	 <?php for($i_preopnurse_allerg=1;$i_preopnurse_allerg<=10;$i_preopnurse_allerg++) { ?> 
			 <tr  style="padding-left:0; ">
				<td height="22">&nbsp;</td>
			  <td colspan="5" height="22" class="text_10b" align="left"><input type="text" id="Allergies_preopnurse<?php echo $i_preopnurse_allerg;?>" class="field text" style=" border:1px solid #ccccc; width:240px; height:22px; "   tabindex="1" value=""  /><input type="text" id="Reaction_preopnurse<?php echo $i_preopnurse_allerg;?>" class="field text" style=" border:1px solid #ccccc; width:240px; height:22px;" tabindex="1" value=""  /></td>
			</tr>
	 <?php } ?>
	  
</table>
</form>