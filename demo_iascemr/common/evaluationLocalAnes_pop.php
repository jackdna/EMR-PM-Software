<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
require_once('conDb.php'); 
$qry_evaluation = "select * from evaluation where `name` not in('HTN', 'DM', 'Dyslipidemia', 'Arthritis', 'CAD', 'S/P CAGB', 'S/P PTCA' ) order by `name`";
$rsNotes = imw_query($qry_evaluation) or die(imw_error());
$totalRows_rsNotes = imw_num_rows($rsNotes);
?>
<script>
function getInnerHTMLEvalAnes(val){
	//var  val = obj.innerHTML;
	var obj2 = document.getElementById('local_anes_revaluation2_id');
	if(obj2.value==''){
		obj2.value = val;
	}else{
		obj2.value += ', '+val;
	}
	obj2.style.backgroundColor = '#FFFFFF';
	textAreaAdjust(obj2);
}
</script>
<div id="evaluationLocalAnesEvaluationDiv" onMouseOver="stopCloseEkg();" onMouseOut="closeEkg('evaluationLocalAnesEvaluationDiv');" class="class="col-md-5 col-lg-4 col-xs-5 col-sm-5"" style="position:absolute; background-color:#E0E0E0; width:350px; height:180px;display:none; z-index:3; overflow:hidden; border :solid 1px #DDD; ">

<div  class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%;  background:#d9534f;  padding-top:5px">
        <span onClick="document.getElementById('evaluationLocalAnesEvaluationDiv').style.display='none';" style="float:right; color:#FFF; cursor:pointer; font-family:Verdana;">X</span>
    </div>
    
    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="width:100%; overflow:hidden; overflow-y:auto; height:150px;"> 
    
    


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
				
               <div class="row hoverdiv" style="cursor:pointer; background:#FFF; padding:5px 0 5px 0 ; border-bottom:1px solid #CCC;" id="evalAnesFix_tr<?php echo $fixEval_seq; ?>" >
               		<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" onClick="return getInnerHTMLEvalAnes('<?php echo $fixName;?>');"> <?php echo $fixName;?> </div>
       			</div>
         
       	<?php
		
		 	}
		 }
		 //END CODE TO SET FIX ORDER OF EVALUATION NAME 
		 $eval_seq=0;
		 $defaultLocalAnesEvalArr = array();
		 while ($row_rsNotes = imw_fetch_assoc($rsNotes)){
			$eval_seq++;
			$defaultLocalAnesEvalArr[] = $row_rsNotes;
			?>
				<div class="row hoverdiv" style="cursor:pointer; background:#FFF; padding:5px 0 5px 0 ; border-bottom:1px solid #CCC;" id="evalAnes_tr<?php echo $eval_seq; ?>" >
               		<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" onClick="return getInnerHTMLEvalAnes('<?php echo stripslashes($row_rsNotes['name']); ?>');"><?php echo stripslashes($row_rsNotes['name']); ?></div>
       			</div>
                
                
            	
			<?php
		}
?>
</div>
</div>