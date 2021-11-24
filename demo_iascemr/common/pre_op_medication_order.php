<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
require_once('conDb.php'); 
$qry_preorder = "select * from preopmedicationorder order by `medicationName`";
 $rsNotes = imw_query($qry_preorder) or die(imw_error());
$totalRows_rsNotes = imw_num_rows($rsNotes);
?>
<script>
function getInnerHTMLmedOp(obj){
	var  val = obj.innerHTML;
	var obj2 = document.getElementById('preopmedId');
	obj2.value=val;
		
}
</script>

<div id="PreOpMedDiv" style="position:absolute;background-color:#E0E0E0;width:250px;height:150px;display:none;overflow:auto;">
<table class="tableCollapse">
	<tr>
		<td class="alignRight"><img src="images/left.gif" style="width:3px; height:24px;"></td>
		<td class="alignright" style="background-color:#BCD2B0; width:98%"><img src="images/chk_off1.gif" onClick="document.getElementById('PreOpMedDiv').style.display='none';"></td>
		<td class="alignLeft valignTop"><img src="images/right.gif" style="width:3px; height:24px;"></td>	</tr>
	<?php
	//$getRecordSetStr = "SELECT * FROM evaluation";
	//$getRecordSetQry = imw_query($getRecordSetStr);
	$rows = 5; //imw_num_rows($getRecordSetQry);
	//while($getRecordSetRows = imw_fetch_array($getRecordSetQry)){
		 $opmed_seq=0;
		 while ($row_rsNotes = imw_fetch_assoc($rsNotes)){
			$opmed_seq++;
			?>
			<tr style="cursor:hand; height:25px;" id="opmed_tr<?php echo $opmed_seq;//$seq; ?>" onMouseOver="return changeColorFn(this, '<?php echo $totalRows_rsNotes; ?>','opmed_tr')">
				<td></td>
					<td style="padding-left:2px; width:100%" class="text_10" id="td<?php echo $row_rsNotes['preOpMedicationOrderId'];//$seq; ?>" onClick="return getInnerHTMLmedOp(this)"><?php echo stripslashes($row_rsNotes['medicationName']).'';// $getRecordSetRows['name']; ?></td>
				<td></td>
			</tr>
			<?php
		}
	//}
?>
</table>
</div>