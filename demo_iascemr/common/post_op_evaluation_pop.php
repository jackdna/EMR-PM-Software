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
function getInnerHTMLEval(obj){
	var  val = obj.innerHTML;
	//alert (val);
	var obj2 = document.getElementById('local_anes_revaluation1_id');
	if(obj2.value==''){
		obj2.value = val;
	}else{
		obj2.value += ', '+val;
	}
	obj2.style.backgroundColor = '#FFFFFF';
	textAreaAdjust(obj2);
}
</script>
<div id="postop_evaluationEvaluationDiv" onMouseOver="stopCloseEkg();" onMouseOut="closeEkg('postop_evaluationEvaluationDiv');" style="position:absolute; background-color:#FFF; width:250px;height:180px; display:none; z-index:3; border:solid 1px #DDD;"> 
	<div  class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%;  background:#d9534f;  padding-top:5px">
        <span onClick="document.getElementById('postop_evaluationEvaluationDiv').style.display='none';" style="float:right; color:#FFF; cursor:pointer; font-family:Verdana;">X</span>
    </div>
    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:150px; overflow:auto"> 
    
	
	<?php
	$rows = 5;
		 $postop_eval_seq=0;
		 while ($row_rsNotes = imw_fetch_assoc($rsNotes)){
			$postop_eval_seq++;
			?>
            <div class="row hoverdiv" style="cursor:pointer; background:#FFF; padding:5px 0 5px 0 ; border-bottom:1px solid #CCC;" id="postop_eval_tr<?php echo $postop_eval_seq; ?>">
                    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" onClick="return getInnerHTMLEval(this);"><?php echo stripslashes($row_rsNotes['name']);?></div>
            </div>
            
			
			<?php
		}
?>
	</div>
    
</div>