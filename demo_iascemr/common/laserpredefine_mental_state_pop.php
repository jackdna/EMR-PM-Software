<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
require_once('conDb.php'); 
$qry_mentalstate= "select * from laserpredefine_mentalstate_tbl   order by `name`";
$res_mentalstate = imw_query($qry_mentalstate) or die(imw_error());
$totalRows_mentalstate = imw_num_rows($res_mentalstate);
?>
<script>
function getInnerHTMLmentalstate(obj){
	var  val = obj.innerHTML;
	var obj2 = document.getElementById('txt_mental_state');
		if(obj2.value==''){
			obj2.value = val;
			}else{
			obj2.value += ', '+val;
			}
		obj2.style.backgroundColor = '#FFFFFF';
		if(document.getElementById('bp_temp')){
			document.getElementById('bp_temp').style.backgroundColor = '#FFFFFF';
		}
		if(document.getElementById('bp_temp2')){
			document.getElementById('bp_temp2').style.backgroundColor = '#FFFFFF';
		}
		if(document.getElementById('txtSLE')){
			document.getElementById('txtSLE').style.backgroundColor = '#FFFFFF';
		}
		if(document.getElementById('txtarea_Fundus_Exam')){
			document.getElementById('txtarea_Fundus_Exam').style.backgroundColor = '#FFFFFF';
		}
}
</script>
<div id="evaluationmental_state" onMouseOver="stopCloseEkg();" onMouseOut="closeEkg('evaluationmental_state');" style="position:absolute; background:#FFF; display:none; overflow:auto; padding:0px;border:1px solid #CCC;border-radius:2px;z-index:999;" class="col-md-5 col-lg-4 col-xs-5 col-sm-5">
    <div  class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%;  background:#d9534f;  padding-top:5px;color:#FFF; text-align:left;font-weight:bold;padding-left:5px;">Mental State <span onClick="document.getElementById('evaluationmental_state').style.display='none';" style="float:right; color:#FFF; cursor:pointer; font-family:Verdana;">X</span>
    </div>
    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:150px; overflow:auto">
<?php
	$rows = 5; 
	$counter_mentalstate=0;
	while ($res_mentalstate_row = imw_fetch_assoc($res_mentalstate)){
		$counter_mentalstate++;
?>
		<div class="row hoverdiv" style="cursor:pointer; background:#FFF; padding:5px 0 5px 0 ; border-bottom:1px solid #CCC;" id="mentalstate_tr<?php echo $counter_mentalstate; ?>">
            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" onClick="return getInnerHTMLmentalstate(this)"><?php echo stripslashes($res_mentalstate_row['name']).''; ?></div>
        </div>
<?php
	}
?>
	</div>
</div>