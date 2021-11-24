<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
require_once('conDb.php'); 
$qry_opdrops = "select * from postopdrops order by `name`";
$rsNotes = imw_query($qry_opdrops) or die(imw_error());
$totalRows_rsNotes = imw_num_rows($rsNotes);
?>
<script>
function getInnerHTMLOpDrops(obj){
	var  val = obj;
	var obj2 = document.getElementById('postop_drop_area_id');
	if(obj2.value==''){
		obj2.value = val;
	}else{
		obj2.value += ', '+val;
	}
	obj2.style.backgroundColor = '#FFFFFF';
	if(document.getElementById('op_proced_area_id')){
		document.getElementById('op_proced_area_id').style.backgroundColor = '#FFFFFF';
	}
	textAreaAdjust(obj2);
} 
</script>
<div id="evaluationPostOpDropsDiv" onMouseOver="stopCloseEkg();" onMouseOut="closeEkg('evaluationPostOpDropsDiv');"  style="position:absolute; background:#FFF; display:none;overflow:auto; padding:0px;border:1px solid #CCC;border-radius:2px;z-index:999;" class="col-md-4 col-lg-3 col-xs-4 col-sm-4">

	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%; background:#d9534f;  padding-top:5px;color:#FFF; text-align:left;font-weight:bold;padding-left:5px;">Post-Op Orders<span onClick="document.getElementById('evaluationPostOpDropsDiv').style.display='none';" style="float:right; color:#FFF; cursor:pointer; ">X</span></div>
	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:150px;overflow:auto;cursor:pointer;margin:0px;padding:0px; background:#FFF;"> 		
	<?php
	$rows = 5;
	$drops_seq=0;
	while ($row_rsNotes = imw_fetch_assoc($rsNotes)){
		$drops_seq++;
		?>
		<!--<tr style="cursor:pointer; height:25px;" id="drops_tr<?php echo $drops_seq; ?>" onMouseOver="return changeColorFn(this, '<?php echo $totalRows_rsNotes; ?>','drops_tr')">
            <td colspan="3" style="padding-left:5px;padding-right:5px;text-align:left; cursor:pointer;" class="text_10" onClick="return getInnerHTMLOpDrops(this)"><?php echo stripslashes($row_rsNotes['name']).''; ?></td>
		</tr>-->
        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 hoverdiv" style="padding:5px; border-bottom:1px solid #CCC;"  onClick="return getInnerHTMLOpDrops('<?php echo stripslashes($row_rsNotes['name']);?>')">  
			<?php echo stripslashes($row_rsNotes['name']); ?>
        </div>
	<?php
	}
?>
</div>
</div>