<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
//require_once('common/conDb.php'); 
require_once('conDb.php'); 
$qry_model = "select * from model order by `name`";
$rsNotes = imw_query($qry_model) or die(imw_error());
$totalRows_rsNotes = imw_num_rows($rsNotes);
?>
<script>
function getInnerHTMLmodel(obj){
	var  val = obj;
	//alert ();
	//var obj1 = top.frames[0].frames[1].document.getElementById('perop_diag_area_id');
	var obj2 = document.getElementById('textareaModelId');
	//var len = obj1.length;
	
	//for(i=0; i<len; i++){		
		if(obj2.value==''){
			obj2.value = val;
		}else{
			obj2.value += '\n'+val;
		}
		textAreaAdjust(obj2);
		obj2.focus();
/*		obj2.style.backgroundColor = '#FFFFFF';
		alert(document.getElementById('iol_na_id').style.backgroundColor);
		if(document.getElementById('iol_na_id')){
			document.getElementById('iol_na_id').style.backgroundColor = '#FFFFFF';
		}
		if(document.getElementById('manufacture_id')){
			document.getElementById('manufacture_id').style.backgroundColor = '#FFFFFF';
		}
		if(document.getElementById('textareaModelId')){
			document.getElementById('textareaModelId').style.backgroundColor = '#FFFFFF';
		}
		if(document.getElementById('bp_temp3')){
			document.getElementById('bp_temp3').style.backgroundColor = '#FFFFFF';
		}
*/
}
</script>
<div id="evaluationPreDefineModelDiv" onMouseOver="stopCloseEkg();" onMouseOut="closeEkg('evaluationPreDefineModelDiv');" style="position:absolute; background:#FFF; display:none; overflow:auto; padding:0px;margin:235px 0;border:1px solid #CCC;border-radius:2px;z-index:999;" class="col-md-5 col-lg-4 col-xs-5 col-sm-5">
    <div  class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%;  background:#d9534f;  padding-top:5px">
        <span onClick="document.getElementById('evaluationPreDefineModelDiv').style.display='none';" style="float:right; color:#FFF; cursor:pointer; font-family:Verdana;">X</span>
    </div>
    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:150px;overflow:auto;cursor:pointer;margin:0px;padding:0px; background:#FFF;"> 
	<?php
	$rows = 5;
	$model_seq=0;
	 while ($row_rsNotes = imw_fetch_assoc($rsNotes)){
		$modelId = $row_rsNotes['modelId'];
		$modelName = $row_rsNotes['name'];
		
		$model_seq++;
		?>
		<!--<tr style="cursor:pointer; height:25px;" id="model_tr<?php echo $model_seq; ?>" onMouseOver="return changeColorFn(this, '<?php echo $totalRows_rsNotes; ?>','model_tr')">
			<td colspan="3" style=" width:100%;padding-left:5px;padding-right:5px;text-align:left; cursor:pointer;" class="text_10 alignLeft" onClick="return getInnerHTMLmodel('<?php echo $modelName; ?>')"><?php echo $modelName.''; ?></td>
		</tr>-->
        
        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 hoverdiv" style="padding:5px; border-bottom:1px solid #CCC;" onClick="return getInnerHTMLmodel('<?php echo stripslashes($modelName); ?>')"> 
			<?php echo stripslashes($modelName); ?>
        </div>
		<?php
	}
?>
</div>
</div>