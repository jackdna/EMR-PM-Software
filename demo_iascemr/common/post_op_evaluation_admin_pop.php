<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
//require_once('common/conDb.php'); 
require_once('conDb.php'); 
$qry_evaluation = "select * from postopevaluation order by `name`";
$rsNotes = imw_query($qry_evaluation) or die(imw_error());
$totalRows_rsNotes = imw_num_rows($rsNotes);
?>
<script>
function getInnerHTMLEvalAdmin(obj){
	var val = obj.innerHTML;
	
	top.frames[0].frames[0].frames[0].document.getElementById('selected_frame_name_id').value='';
	var obj2 = top.frames[0].frames[0].frames[0].document.getElementById('local_anes_revaluation1_admin_id');
	if(obj2.value==''){
		obj2.value = val;
	}else{
		obj2.value += ', '+val;
	}
}
var tOutAdminPostOPEvl; 
function closeAdminPostOPEvl(){
	if(top.frames[0].frames[0].frames[0].document.getElementById("hiddPreDefineId").value=="preDefineOpenYes") {
		if(top.frames[0].frames[0].document.getElementById('postop_evaluationEvaluationAdminDiv').style.display == "block"){
			top.frames[0].frames[0].document.getElementById('postop_evaluationEvaluationAdminDiv').style.display = "none";
			
			//top.frames[0].frames[0].frames[0].document.getElementById("hiddPreDefineId").value = "";
		}
	}	
	
}
function closeAdminTimePostOPEvl(){
	tOutAdminPostOPEvl = setTimeout("closeAdminPostOPEvl()", 500);
}
function stopClosePostOPEvl() {
	clearTimeout(tOutAdminPostOPEvl);
}

</script>

<div id="largeContent" style="display:none;overflow-y:auto;">
    <ul class="list-group">
    <?php
		$rows = 5;
		 $postop_eval_seq=0;
		 while ($row_rsNotes = imw_fetch_assoc($rsNotes)){
			$postop_eval_seq++;
			?> 
            <li class="list-group-item"  onClick="return getInnerHTMLEvalAdmin(document.getElementById('<?php echo 'post_op_'.$postop_eval_seq;?>'))"><a href="javascript:void(0)" id="<?php echo 'post_op_'.$postop_eval_seq;?>"> <?php echo stripslashes($row_rsNotes['name']).''; ?></a></li>
			<?php
		}
	?>
    </ul>
</div>	
      <!--                          
<div id="postop_evaluationEvaluationAdminDiv" onMouseOver="stopClosePostOPEvl();" onMouseOut="closeAdminTimePostOPEvl('postop_evaluationEvaluationAdminDiv');" style="position:absolute;background-color:#E0E0E0;width:250px;height:100px;display:none;overflow:auto;"> 
<table class="table_collapse" style="border:none;" >
    <tr >
        <td class="alignRight" style=" background-color:#BCD2B0;"><img src="../images/left.gif" style=" width:3px; height:24px;"></td>
        <td class="alignRight" style=" background-color:#BCD2B0; width:100%; height:15px;"><img src="../images/chk_off1.gif" style="cursor:pointer;" onClick="document.getElementById('postop_evaluationEvaluationAdminDiv').style.display='none';"></td>
        <td class="alignLeft valignTop" style=" background-color:#BCD2B0;"><img src="../images/right.gif" style=" width:3px; height:24px; "></td>
    </tr> 
	<?php
		 $rows = 5;
		 $postop_eval_seq=0;
		 while ($row_rsNotes = imw_fetch_assoc($rsNotes)){
			$postop_eval_seq++;
			?>
            <tr style="cursor:pointer; height:25px;" id="postop_eval_tr<?php echo $postop_eval_seq; ?>" onMouseOver="return changeColorFn(this, '<?php echo $totalRows_rsNotes; ?>','postop_eval_tr');">
                <td colspan="3" class="text_10 alignLeft" style=" width:100%;padding-left:2px; cursor:pointer;"  onClick="return getInnerHTMLEvalAdmin(this)"><?php echo stripslashes($row_rsNotes['name']).''; ?></td>
            </tr> 
			<?php
		}
?>
</table>
</div>-->