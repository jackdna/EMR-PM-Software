<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php 
//require_once('common/conDb.php'); 
//require_once('conDb.php'); 
$qry_opdrops = "select * from medications order by `name`";
$rsNotes = imw_query($qry_opdrops) or die(imw_error());
$totalRows_rsNotes = imw_num_rows($rsNotes);
?>
<script>
function getInnerHTMLMedicationIolink(data){
	for(i=0; i<20; i++){
		var str='medicationName'+i;
		if(document.getElementById(str)){
			if(document.getElementById(str).value==''){
				document.getElementById(str).value=data;
				break;			
			}
		}
		//START CODE FOR HEALT QUESTIONNAIRE MEDICATION Questionnaire
		var strHealthQuestMed = 'medication_name'+i;
		if(document.getElementById(strHealthQuestMed)){
			if(document.getElementById(strHealthQuestMed).value==''){
				document.getElementById(strHealthQuestMed).value=data;
				break;			
			}
		}
		//END CODE FOR HEALT  QUESTIONNAIRE MEDICATION Questionnaire
		
	}
}
	 
</script>
<div id="iolinkPreDefineMedAdminDiv" onMouseOver="stopCloseEkg();" onMouseOut="closeEkg('iolinkPreDefineMedAdminDiv');" style="position:absolute;background-color:#E0E0E0;width:250px;height:130px; left:85px;display:none;overflow:auto; z-index:900;">
<table class="table_collapse" style="border:none;">
	<tr>
		<td class="alignRight" style=" background-color:#BCD2B0;"><img src="images/left.gif" alt="" style=" width:3px; height:24px;"></td>
		<td class="alignRight" style=" background-color:#BCD2B0; width:100%; height:15px; text-align:right;"><img src="images/chk_off1.gif"  alt="Close" style="cursor:pointer;" onClick="document.getElementById('iolinkPreDefineMedAdminDiv').style.display='none';"></td>
		<td class="alignLeft valignTop" style=" background-color:#BCD2B0;"><img src="images/right.gif"  alt="" style=" width:3px; height:24px; "></td>
	</tr>
	<?php
	$rows = 5;
	$med_counter = 0;
	while ($row_rsNotes = imw_fetch_assoc($rsNotes)){
		$med_seq +=1;
		?>
		<tr style="cursor:pointer; height:25px;" id="MedAdmin_tr<?php echo $med_seq;//$seq; ?>" onMouseOver="return changeColorFn(this, '<?php echo $totalRows_rsNotes; ?>','MedAdmin_tr')">
			<td colspan="3" style=" width:100%;padding-left:5px;padding-right:5px;text-align:left; cursor:pointer;" class="text_10" onClick="getInnerHTMLMedicationIolink(this.innerText); "><?php echo stripslashes($row_rsNotes['name']).'';?></td>
		</tr>
	<?php
	}
?>
</table>

</div>