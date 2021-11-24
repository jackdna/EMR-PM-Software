<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
require_once('conDb.php'); 
$qry_diagnosis = "select * from recoverycomments order by `recoveryComments`";
$rsNotes = imw_query($qry_diagnosis) or die(imw_error());
$totalRows_rsNotes = imw_num_rows($rsNotes);
?>
<script>
function getInnerHTMLrecovery(obj){
	var  val = obj.innerHTML;
	var obj2 = document.getElementById('recv_comm_area_id');
	if(obj2.value==''){
		obj2.value = val;
	}else{
		obj2.value += '\n'+val;
	}
}
</script>

<div id="evaluationRecoveryDiv" onMouseOver="stopCloseEkg();" onMouseOut="closeEkg('evaluationRecoveryDiv');" style="position:absolute; background:#FFF; display:none;overflow:auto; padding:0px; margin:0px;;border:1px solid #CCC;border-radius:2px;z-index:999;" class="col-md-3 col-lg-2 col-xs-8 col-sm-5">
<div  class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%;  background:#d9534f;  padding-top:5px"><span onClick="document.getElementById('evaluationRecoveryDiv').style.display='none';" style="float:right; color:#FFF; cursor:pointer; font-family:Verdana;">X</span>
</div>
<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:150px; overflow:auto"> 
	<?php
	$rows = 5; 
	$recovery_counter=0;
	while ($row_rsNotes = imw_fetch_assoc($rsNotes)){
		$recovery_counter++;
		?>
		<div class="row hoverdiv" style="cursor:pointer; background:#FFF; padding:5px 0 5px 0 ; border-bottom:1px solid #CCC;" id="recovery_tr<?php echo $recovery_counter;?>">
            	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" onClick="return getInnerHTMLrecovery(this)"><?php echo stripslashes($row_rsNotes['recoveryComments']).''; ?></div>
        </div>
		
<?php
	}
?>
</div>
</div>