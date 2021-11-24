<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php 
require_once('common/conDb.php'); 
//require_once('conDb.php'); 
$qry_model = "select * from model order by `name`";
$rsNotes = imw_query($qry_model) or die(imw_error());
$totalRows_rsNotes = imw_num_rows($rsNotes);
?>
<script>
function getInnerHTMLmodel(obj){
	var  val = obj.innerHTML;
	var obj2 = document.getElementById('textareaModelId');
		if(obj2.value==''){
			obj2.value = val;
		}else{
			obj2.value += '\n'+val;
		}
		obj2.focus();
}
</script>
<div id="evaluationPreDefineModelDiv" onMouseOver="stopCloseEkg();" onMouseOut="closeEkg('evaluationPreDefineModelDiv');" style="position:absolute;background-color:#E0E0E0; left:500px; top:70px;width:250px;height:200px;display:none;overflow:auto;">  
<table class="table_collapse" style="border:none;">
	<tr>
		<td class="alignRight" style=" background-color:#BCD2B0;"><img src="images/left.gif" alt="" style=" width:3px; height:24px;"></td>
		<td class="alignRight" style=" background-color:#BCD2B0; width:100%; height:15px; text-align:right;"><img src="images/chk_off1.gif"  alt="Close" style="cursor:pointer;" onClick="document.getElementById('evaluationPreDefineModelDiv').style.display='none';"></td>
		<td class="alignLeft valignTop" style=" background-color:#BCD2B0;"><img src="images/right.gif"  alt="" style=" width:3px; height:24px; "></td>
	</tr>
	<?php
		$rows = 5; 
		$model_seq=0;
		 while ($row_rsNotes = imw_fetch_assoc($rsNotes)){
			$modelId = $row_rsNotes['modelId'];
			$modelName = $row_rsNotes['name'];
			
			$model_seq++;
			?>
            <tr style="cursor:pointer; height:25px;" id="model_tr<?php echo $model_seq;//$seq; ?>" onMouseOver="return changeColorFn(this, '<?php echo $totalRows_rsNotes; ?>','model_tr')">
                <td colspan="3" style="font-size:12px; width:100%;padding-left:5px;padding-right:5px;text-align:left; cursor:pointer;" class="text_10" onClick="return getInnerHTMLmodel(this)"><?php echo $modelName.''; ?></td>
            </tr>
			
            
			<?php
		}
?>
</table>
</div>