<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
$rowcolor_discharge_summary_sheet="#FBF5EE";
if(!$saveSuccessfullyMessage) {
	$saveSuccessfullyMessage = "Record Successfully Saved.";
}
?>
<script>
function hideSaveDeleteAlert() {
	if(document.getElementById('divSaveAlert')) {
		document.getElementById('divSaveAlert').style.display = 'none';
	}
	if(document.getElementById('divDeleteAlert')) {
		document.getElementById('divDeleteAlert').style.display = 'none';
	}
}
</script>
<table class="table_pad_bdr" style="width:250px;">
	<tr style="height:25px;">	
		<td class="text_10b1 alignCenter valignMiddle" style="width:229px; background-color:<?php echo $bgCol; ?>;">&nbsp;</td>
	</tr>
	<tr>
		<td class="alignCenter" style="width:100%;">
			<table class="table_collapse" style="border:0.5px solid; border-color:<?php echo $borderCol; ?>;">
				<tr>
					<td class="text_10 alignCenter" style="background-color:<?php echo $rowcolor_discharge_summary_sheet; ?>;">
						<?php echo $saveSuccessfullyMessage;?>
						<br><br>
						<img src="images/ok.jpg" alt="OK" onClick="javascript:hideSaveDeleteAlert();">
						<br><br>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
