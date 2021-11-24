<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
//require_once('common/conDb.php'); 
require_once('conDb.php'); 

$qry_evaluation = "select * from evaluation where `name` not in('HTN', 'DM', 'Dyslipidemia', 'Arthritis', 'CAD', 'S/P CAGB', 'S/P PTCA' ) order by `name`";
$rsNotes = imw_query($qry_evaluation) or die(imw_error());
$totalRows_rsNotes = imw_num_rows($rsNotes);
?>
<script>
function getInnerHTMLEvalAnesAdmin(obj){
	var  val = obj.innerHTML;
	top.frames[0].frames[0].frames[0].document.getElementById('selected_frame_name_id').value='';
	var obj2 = top.frames[0].frames[0].frames[0].document.getElementById('local_anes_revaluation2_admin_id');
		if(obj2.value==''){
			obj2.value = val;
			}else{
			obj2.value += ', '+val;
			}
}

var tOutAdminLocalAnes; 
function closeAdminLocalAnes(){
	if(top.frames[0].frames[0].frames[0].document.getElementById("hiddPreDefineId").value=="preDefineOpenYes") {
		if(top.frames[0].frames[0].document.getElementById('evaluationLocalAnesEvaluationAdminDiv').style.display == "block"){
			top.frames[0].frames[0].document.getElementById('evaluationLocalAnesEvaluationAdminDiv').style.display = "none";
			//top.frames[0].frames[0].frames[0].document.getElementById("hiddPreDefineId").value = "";
		}
	}	
	
}
function closeAdminTimeLocalAnes(){
	tOutAdminLocalAnes = setTimeout("closeAdminLocalAnes()", 500);
}
function stopCloseAdminLocalAnes() {
	clearTimeout(tOutAdminLocalAnes);
}

</script>

<div id="listContent" style="display:none;" class="listContent" >
    <ul class="list-group">
    <?php
		 $rows = 5; 
		 $fixEval_seq=0;
		 //START CODE TO SET FIX ORDER OF EVALUATION NAME
		
		 $fixEvaluationArr = array('HTN', 'DM', 'Dyslipidemia', 'Arthritis', 'CAD', 'S/P CAGB', 'S/P PTCA');		 
		 foreach($fixEvaluationArr as $evaluationName) {
			
			$fixEvaluationQry = "select * from evaluation where `name` = '$evaluationName'";
			$fixEvaluationRes = imw_query($fixEvaluationQry) or die(imw_error());
			$fixEvaluationNumRow = imw_num_rows($fixEvaluationRes);
			$fixEvaluationRow = imw_fetch_array($fixEvaluationRes);
			$fixEvaluationId = $fixEvaluationRow['evaluationId'];
			$fixName = $fixEvaluationRow['name'];
		 	if($fixEvaluationNumRow>0) {
				$fixEval_seq++;
		
		 	}
		 }
		 
		 //END CODE TO SET FIX ORDER OF EVALUATION NAME 
		 $eval_seq=0;
		 while ($row_rsNotes = imw_fetch_assoc($rsNotes)){
			$eval_seq++;
			?>
           <li class="list-group-item" onClick="return getInnerHTMLEvalAnesAdmin(document.getElementById('<?php echo 'evaluation'.$eval_seq;?>'));"><a href="javascript:void(0)" id="<?php echo 'evaluation'.$eval_seq;?>"> <?php echo stripslashes($row_rsNotes['name']).''; ?> </a></li>
			<?php
		}
?>
    </ul>
</div>
                                            


<!--<div id="evaluationLocalAnesEvaluationAdminDiv" onMouseOver="stopCloseAdminLocalAnes();" onMouseOut="closeAdminTimeLocalAnes('evaluationLocalAnesEvaluationAdminDiv');" style="position:absolute;background-color:#E0E0E0;width:250px;height:130px;display:none;overflow:auto;">
    
<table class="table_collapse" style="border:none;">
    <tr >
        <td class="alignRight" style=" background-color:#BCD2B0;"><img src="../images/left.gif" style=" width:3px; height:24px;"></td>
        <td class="alignRight" style=" background-color:#BCD2B0; width:100%; height:15px;"><img src="../images/chk_off1.gif" style="cursor:pointer;" onClick="document.getElementById('evaluationLocalAnesEvaluationAdminDiv').style.display='none';"></td>
        <td class="alignLeft valignTop" style=" background-color:#BCD2B0;"><img src="../images/right.gif" style=" width:3px; height:24px; "></td>
    </tr>    
	<?php
		 $rows = 5; 
		 $fixEval_seq=0;
		 //START CODE TO SET FIX ORDER OF EVALUATION NAME
		
		 $fixEvaluationArr = array('HTN', 'DM', 'Dyslipidemia', 'Arthritis', 'CAD', 'S/P CAGB', 'S/P PTCA');		 
		 foreach($fixEvaluationArr as $evaluationName) {
			
			$fixEvaluationQry = "select * from evaluation where `name` = '$evaluationName'";
			$fixEvaluationRes = imw_query($fixEvaluationQry) or die(imw_error());
			$fixEvaluationNumRow = imw_num_rows($fixEvaluationRes);
			$fixEvaluationRow = imw_fetch_array($fixEvaluationRes);
			$fixEvaluationId = $fixEvaluationRow['evaluationId'];
			$fixName = $fixEvaluationRow['name'];
		 	if($fixEvaluationNumRow>0) {
				$fixEval_seq++;
		 ?>
				<tr style="cursor:pointer; height:25px;" id="evalAnesFix_tr<?php echo $fixEval_seq; ?>" onMouseOver="this.bgColor = '#ECF1EA';"  onMouseOut="this.bgColor = '#E0E0E0';" >
					<td colspan="3" class="text_10 alignLeft" style=" width:100%;padding-left:2px; cursor:pointer;"  onClick="return getInnerHTMLEvalAnesAdmin(this)"><?php echo $fixName.''; ?></td>
				</tr>                
		 <?php
		 	}
		 }
		 
		 //END CODE TO SET FIX ORDER OF EVALUATION NAME 
		 $eval_seq=0;
		 while ($row_rsNotes = imw_fetch_assoc($rsNotes)){
			$eval_seq++;
			?>
            <tr style="cursor:pointer; height:25px;" id="evalAnes_tr<?php echo $eval_seq; ?>" onMouseOver="return changeColorFn(this, '<?php echo $totalRows_rsNotes; ?>','evalAnes_tr');" onMouseOut="this.bgColor = '#E0E0E0';" >
                <td colspan="3" class="text_10 alignLeft" style=" width:100%;padding-left:2px; cursor:pointer;"  onClick="return getInnerHTMLEvalAnesAdmin(this);"><?php echo stripslashes($row_rsNotes['name']).''; ?></td>
            </tr> 
			<?php
		}
?>
</table>
</div>-->