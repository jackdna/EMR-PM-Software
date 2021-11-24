<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
require_once('conDb.php'); 
$qry_med = "select * from medications order by `name`";
$rsNotes = imw_query($qry_med) or die(imw_error());
$totalRows_rsNotes = imw_num_rows($rsNotes);
?>
<script>
function getInnerHTMLNurseFn(obj,selectedId) {
	document.getElementById(selectedId).value = obj.innerHTML;
}
</script>
<div id="evaluationPreDefineMedNurseDiv" style="position:absolute;background-color:#E0E0E0;width:250px;height:150px;display:none;overflow:auto;">
<table class="tableCollapse">
	<tr>
		<td class="alignRight"><img src="images/left.gif" style="width:3px; height:24px;"></td>
		<td class="alignRight" style="background-color:#BCD2B0; width:98%"><img src="images/chk_off1.gif" onClick="document.getElementById('evaluationPreDefineMedNurseDiv').style.display='none';"></td>
		<td class="alignLeft valignTop"><img src="images/right.gif" style="width:3px; height:24px;"></td>
	</tr>
	<?php
	$rows = 5;
		 $med_counter = 0;
		 while ($row_rsNotes = imw_fetch_assoc($rsNotes)){
			$med_counter +=1;
			?>
			<tr style="cursor:hand; height:25px;" id="medi_tr<?php echo $med_counter;//$seq; ?>" onMouseOver="return changeColorFn(this, '<?php echo $totalRows_rsNotes; ?>','medi_tr')">
				<td></td>
					<td style="padding-left:2px; width:100%" class="text_10" id="td_<?php echo $row_rsNotes['medicationsId'];//$seq; ?>" onClick="return getInnerHTMLNurseFn(this, top.frames[0].document.getElementById('selected_frame_name_id').value)"><?php echo stripslashes($row_rsNotes['name']).'';// $getRecordSetRows['name']; ?></td>
				<td></td>
			</tr>
			<?php
		}
?>
</table>
</div>