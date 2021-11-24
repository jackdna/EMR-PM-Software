<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php 
//require_once('common/conDb.php'); 
//require_once('conDb.php'); 
$qry_opdrops = "select * from allergies order by `name`";
$rsNotes = imw_query($qry_opdrops) or die(imw_error());
$totalRows_rsNotes = imw_num_rows($rsNotes);
?>
<script>
function getInnerHTMLAllergryIolink(data){
	for(i=0; i<20; i++){
		var str='allergy_name'+i;
		if(document.getElementById(str)){
			if(document.getElementById(str).value==''){
				document.getElementById(str).value=data;
				break;			
			}
		}
		//START CODE FOR HEALT  QUESTIONNAIRE Questionnaire
		var strHealthQuest = 'Allergies_quest'+i;
		if(document.getElementById(strHealthQuest)){
			if(document.getElementById(strHealthQuest).value==''){
				document.getElementById(strHealthQuest).value=data;
				break;			
			}
		}
		//END CODE FOR HEALT  QUESTIONNAIRE Questionnaire
	}
}
	
</script>
<div id="iolinkPreDefineAllergyAdminDiv" onMouseOver="stopCloseEkg();" onMouseOut="closeEkg('iolinkPreDefineAllergyAdminDiv');" style="position:absolute;background-color:#E0E0E0;width:250px;height:130px; left:130px; display:none;overflow:auto; z-index:998;">
<table class="table_collapse" style="border:none;">
	<tr>
		<td class="alignRight" style=" background-color:#BCD2B0;"><img src="images/left.gif" alt="" style=" width:3px; height:24px;"></td>
		<td class="alignRight" style=" background-color:#BCD2B0; width:100%; height:15px; text-align:right;"><img src="images/chk_off1.gif"  alt="Close" style="cursor:pointer;" onClick="document.getElementById('iolinkPreDefineAllergyAdminDiv').style.display='none';"></td>
		<td class="alignLeft valignTop" style=" background-color:#BCD2B0;"><img src="images/right.gif"  alt="" style=" width:3px; height:24px; "></td>
	</tr>
	<?php
	$rows = 5; 
		$drops_seq=0;
		 while ($row_rsNotes = imw_fetch_assoc($rsNotes)){
			$drops_seq++;
			?>
            <tr style="cursor:pointer; height:25px;" id="DropsAdmin_tr<?php echo $drops_seq; ?>" onMouseOver="return changeColorFn(this, '<?php echo $totalRows_rsNotes; ?>','DropsAdmin_tr')">
                <td colspan="3" style=" width:100%;padding-left:5px;padding-right:5px;text-align:left; cursor:pointer;" class="text_10" onClick="getInnerHTMLAllergryIolink(this.innerText); "><?php echo stripslashes($row_rsNotes['name']).''; ?></td>
            </tr>
			
            
			<?php
		}
?>
</table>
</div>