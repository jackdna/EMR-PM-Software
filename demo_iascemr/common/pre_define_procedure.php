<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
require_once('conDb.php'); 
$qry_category = "select proceduresCategoryId, name from procedurescategory where del_status != 'yes' order by `name`";

$rsNotes = imw_query($qry_category) or die(imw_error());
$totalRows_rsNotes = imw_num_rows($rsNotes);
?>
<script>
function getInnerHTMLpro(obj){
	var  valpro = obj;
	var objpro = document.getElementById('op_proced_area_id');
	if(objpro.value==''){
		objpro.value = valpro;
	}else{
		objpro.value += '\n'+valpro;
	}
	objpro.style.backgroundColor = '#FFFFFF';
	if(document.getElementById('postop_drop_area_id')){
		document.getElementById('postop_drop_area_id').style.backgroundColor = '#FFFFFF';
	}
	textAreaAdjust(objpro);
}
</script>
<div id="evaluationProceduresDiv" onMouseOver="stopCloseEkg();" onMouseOut="closeEkg('evaluationProceduresDiv');" style="position:absolute; background:#FFF; display:none;overflow:auto; padding:0px;margin:80px 220px;border:1px solid #CCC;border-radius:2px;z-index:999;" class="col-md-4 col-lg-3 col-xs-4 col-sm-4">

	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%; background:#d9534f;  padding-top:5px;color:#FFF; text-align:left;font-weight:bold;padding-left:5px;">Operative Procedures<span onClick="document.getElementById('evaluationProceduresDiv').style.display='none';" style="float:right; color:#FFF; cursor:pointer; ">X</span></div>
	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:150px;overflow:auto;cursor:pointer;margin:0px;padding:0px; background:#FFF;"> 		
	
	<?php
	$rows = 5;
		$procedure_seq=0;
		 while ($row_rsNotes = imw_fetch_assoc($rsNotes)){
			?>
			<!--<tr style="height:25px;"><!--  id="tr<?php echo $row_rsNotes['proceduresCategoryId ']; ?>">
				<td colspan="3" style="padding-left:3px;padding-right:3px;text-align:left;" class="text_10b"><?php echo $row_rsNotes['name'].'';?></td>
			</tr>-->
            	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 hoverdiv" style="padding:5px; border-bottom:1px solid #CCC; font-weight:bold; cursor:not-allowed;"> 
	                <?php echo stripslashes($row_rsNotes['name']); ?>
                </div>
			<?php
			 echo subCategory($row_rsNotes['proceduresCategoryId']);
		}
	
	function subCategory($id)
	{
		  $qry_procedure = "select procedureId, name, catid from procedures where procedures.catid =".$id." and del_status!='yes' order by `name` ";
			$sub_category = imw_query($qry_procedure) or die(imw_error());
			$total_row = imw_num_rows($sub_category);
			
			while($cat_fetch = imw_fetch_array($sub_category)){
				 $procedure_seq++;
			?>
			<!--<tr style="cursor:pointer; height:25px;"  id="procedure_tr<?php echo trim($cat_fetch['catid'].'_'.$procedure_seq); ?>" onMouseOver="return changeColorFn(this, '<?php echo $total_row; ?>','procedure_tr<?php echo trim($cat_fetch['catid'].'_');?>')">
				<td colspan="3" style="padding-left:5px;padding-right:5px;text-align:left; cursor:pointer;" class="text_10" onClick="return getInnerHTMLpro(this)"><?php echo stripslashes($cat_fetch['name']).'';?></td>
			</tr>-->
            	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 hoverdiv" style="padding:5px; border-bottom:1px solid #CCC;" onClick="return getInnerHTMLpro('<?php echo stripslashes($cat_fetch['name']); ?>')"> 
	                <?php echo stripslashes($cat_fetch['name']); ?>
                </div>
	<?php 	}	
	}
?>
	</div>
</div>