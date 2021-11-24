<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
//require_once('common/conDb.php'); 
require_once('conDb.php'); 
$qry_premed = "select * from preopmedicationorder";
$res_Premed = imw_query($qry_premed) or die(imw_error());
$totalRows = imw_num_rows($res_Premed);
?>
<script>
function getInnerHTMLmedOp(obj){
	var  val = obj.innerHTML;
	var obj2 = document.getElementById('preOpMediOrderAreaId');
		if(obj2.value==''){
			obj2.value = val;
			}else{
			obj2.value += '\n'+val;
			}
			
			
		
	
}
</script>
<div id="PreOpMedDiv" style="position:absolute;background-color:#E0E0E0;width:250px;height:150px;display:none;overflow:auto;">
<table border="0" cellpadding="0" cellspacing="0" width="100%" >
	<tr>
		<td align="right"><img src="images/left.gif" width="3" height="24"></td>
		<td align="right" bgcolor="#BCD2B0" width="98%"><img src="images/chk_off1.gif" onClick="document.getElementById('PreOpMedDiv').style.display='none';"></td>
		<td align="left" valign="top"><img src="images/right.gif" width="3" height="24"></td>
	</tr>
	<?php
	$rows = 5; 
		 $pre_opmed_seq=0;
		 while ($res_Premed = imw_fetch_assoc($res_Premed)){
			$pre_opmed_seq++;
			?>
			<tr height="25" style="cursor:hand;" id="opmed_tr<?php echo $pre_opmed_seq;//$seq; ?>" onMouseOver="return changeColorFn(this, '<?php echo $totalRows; ?>','opmed_tr')">
				<td></td>
					<td width="100%" style="padding-left:2px;" class="text_10" id="td<?php echo $row_rsNotes['medicationsId'];//$seq; ?>" onClick="return getInnerHTMLmedOp(this)"><?php echo $res_Premed['medicationName'].'';// $getRecordSetRows['name']; ?></td>
				<td></td>
			</tr>
			<?php
		}
?>
</table>
</div>
