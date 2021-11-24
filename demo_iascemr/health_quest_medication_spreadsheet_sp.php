<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include("common/linkfile.php");
?>
<form name="frm_health_quest_medication_spreadsheet" class="wufoo topLabel" enctype="multipart/form-data" method="post" style="margin:0px; " action="">
	<table align="left" width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#F1F4F0">
		<?php for($i_healthquest_med=1;$i_healthquest_med<=10;$i_healthquest_med++) { ?> 
			<tr bgcolor="#F1F4F0" style="padding-left:8; ">
				<td height="22">&nbsp;</td>
			  <td colspan="5" height="22" class="text_10b" align="left"><input type="text" id="medication_name<?php echo $i_healthquest_med;?>" class="field text" style=" border:1px solid #ccccc; width:210px; height:22px; "   tabindex="1" value=""  /><input type="text" id="medication_detail<?php echo $i_healthquest_med;?>" class="field text" style=" border:1px solid #ccccc; width:210px; height:22px;" tabindex="1" value=""  /></td>
			</tr>
		<?php } ?>
	</table>
</form>