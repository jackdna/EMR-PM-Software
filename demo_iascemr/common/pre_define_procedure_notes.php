<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
require_once('conDb.php'); 
$qry_procedure_notes = "select * from laserpredefine_procedure_notes";
$rsNotes = imw_query($qry_procedure_notes) or die(imw_error());
$totalRows_rsNotes = imw_num_rows($rsNotes);
?>
<script>
function getInnerHTMLProc(obj){
	var  val = obj.innerHTML;
	var obj2 = document.getElementById('laser_procedure_notes_id');
	if(obj2.value==''){
		obj2.value = val;
	}else{
		obj2.value += ', '+val;
	}
	//obj2.style.backgroundColor = '#FFFFFF';
}
</script>
<div id="evaluationProcedureNotesDiv" onMouseOver="stopCloseEkg();" onMouseOut="closeEkg('evaluationProcedureNotesDiv');" style="position:absolute;background-color:#E0E0E0;width:250px;height:150px;display:none;overflow:auto;">
<table class="table_collapse" style="border:none;">
	<tr>
		<td class="alignRight" style=" background-color:#BCD2B0;"><img src="images/left.gif" style=" width:3px; height:24px;"></td>
		<td class="alignRight" style=" background-color:#BCD2B0; width:100%; height:15px; text-align:right;"><img src="images/chk_off1.gif" style="cursor:pointer;" onClick="document.getElementById('evaluationProcedureNotesDiv').style.display='none';"></td>
		<td class="alignLeft valignTop" style=" background-color:#BCD2B0;"><img src="images/right.gif" style=" width:3px; height:24px; "></td>
	</tr>
	<?php
	$rows = 5; 
		$procedure_notes_seq=0;
		 while ($row_rsNotes = imw_fetch_assoc($rsNotes)){
			$procedure_notes = $row_rsNotes['name'];
			$procedure_notes_seq++;
			?>
			<tr style="cursor:pointer; height:25px;" id="procedure_notes_tr<?php echo $procedure_notes_seq; ?>" onMouseOver="return changeColorFn(this, '<?php echo $totalRows_rsNotes; ?>','procedure_notes_tr')" >
				<td colspan="3" style=" width:100%;padding-left:5px;padding-right:5px;text-align:left; cursor:pointer;" class="text_10" onClick="return getInnerHTMLProc(this)"><?php echo stripslashes($procedure_notes); ?></td>
			</tr>
			<?php
		}
?>
</table>
</div>