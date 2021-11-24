<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
require_once('conDb.php'); 
$qry_evaluation = "select * from evaluation order by `name`";
$rsNotes = imw_query($qry_evaluation) or die(imw_error());
$totalRows_rsNotes = imw_num_rows($rsNotes);
?>
<script>
function getInnerHTMLEval(val){
	//var  val = obj.innerHTML;
	
	var obj2 = document.getElementById('local_anes_revaluation1_id');
	var V = obj2.value.trim();
	if(V ==''){
		obj2.value = val;
	}else{
		obj2.value = V  + ', '+val;
	}
	obj2.style.backgroundColor = '#FFFFFF';		
}
</script>
<div id="evaluationEvaluationDiv" onMouseOver="stopCloseEkg();" onMouseOut="closeEkg('evaluationEvaluationDiv');" style="position:absolute; background-color:#FFF; width:250px;height:180px; display:none; z-index:3; border:solid 1px #DDD;">

	<div  class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%;  background:#d9534f;  padding-top:5px">
        <span onClick="document.getElementById('evaluationEvaluationDiv').style.display='none';" style="float:right; color:#FFF; cursor:pointer; font-family:Verdana;">X</span>
    </div>
    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:150px; overflow:auto"> 
    	
        <?php
					
					$rows		=		5	;
					$eval_seq	=		0	;
					
					while ($row_rsNotes = imw_fetch_assoc($rsNotes))
					{
							$eval_seq++;
		?>
        
        					<div class="row hoverdiv" style="cursor:pointer; background:#FFF; padding:5px 0 5px 0 ; border-bottom:1px solid #CCC;" id="eval_tr<?php echo $eval_seq; ?>" >
                    			<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" onClick="return getInnerHTMLEval('<?php echo stripslashes($row_rsNotes['name']);?>')"><?php echo stripslashes($row_rsNotes['name']);?></div>
            				</div>
            
        				
				
			
			<?php
		}
	//}
?>
	</div>
    
</div>