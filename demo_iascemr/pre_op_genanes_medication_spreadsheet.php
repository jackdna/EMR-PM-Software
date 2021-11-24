<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include("common/linkfile.php");
?>
  <form name="frm_pre_op_genanes_medication_spreadsheet" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px; " action=""> 	

	<table  align="left" width="90%" border="0" cellpadding="0" cellspacing="0" bgcolor="<?php echo $bglight_blue_local_anes; ?>">
	 <?php for($i_preopgenanes_med=1;$i_preopgenanes_med<=10;$i_preopgenanes_med++) { ?> 
			 <tr  style="padding-left:4; ">
				<td height="22">&nbsp;</td>
			  <td colspan="5" height="22" class="text_10b" align="left"><input type="text" id="medication_preopgenanes_name<?php echo $i_preopgenanes_med; ?>" class="field text" style=" border:1px solid #ccccc; width:155px; height:22px; "   tabindex="1" value=""  /><input type="text" id="medication_preopgenanes_reaction<?php echo $i_preopgenanes_med; ?>" class="field text" style=" border:1px solid #ccccc; width:155px; height:22px;" tabindex="1" value=""  /></td>
			</tr>
	 <?php } ?>
	   
	</table>
  </form>	
