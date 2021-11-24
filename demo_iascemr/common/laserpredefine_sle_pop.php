<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
require_once('conDb.php'); 
$qry_sle = "select * from laserpredefine_sle_tbl  order by `name`";
$res_sle = imw_query($qry_sle) or die(imw_error());
$totalRows_sle = imw_num_rows($res_sle);
?>
<script>
function getInnerHTMLsle(obj){
	var  val = obj.innerHTML;
	var obj2 = document.getElementById('txtSLE');
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
		if(document.getElementById('txt_mental_state')){
			document.getElementById('txt_mental_state').style.backgroundColor = '#FFFFFF';
		}
		if(document.getElementById('txtarea_Fundus_Exam')){
			document.getElementById('txtarea_Fundus_Exam').style.backgroundColor = '#FFFFFF';
		}
}
</script>

<div id="evaluationSLE" onMouseOver="stopCloseEkg();" onMouseOut="closeEkg('evaluationSLE');" style="position:absolute; background:#FFF; display:none; overflow:auto; padding:0px;border:1px solid #CCC;border-radius:2px;z-index:999;" class="col-md-5 col-lg-4 col-xs-5 col-sm-5">
    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%;  background:#d9534f;  padding-top:5px;color:#FFF; text-align:left;font-weight:bold;padding-left:5px;">SLE <span onClick="document.getElementById('evaluationSLE').style.display='none';" style="float:right; color:#FFF; cursor:pointer; font-family:Verdana;">X</span>
    </div>
    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:150px; overflow:auto">
<?php
	$rows = 5; 
	$counter_sle=0;
	while ($res_sle_row = imw_fetch_assoc($res_sle)){
		$counter_sle++;
?>
		<div class="row hoverdiv" style="cursor:pointer; background:#FFF; padding:5px 0 5px 0 ; border-bottom:1px solid #CCC;" id="sle_tr<?php echo $counter_sle; ?>">
            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" onClick="return getInnerHTMLsle(this)"><?php echo stripslashes($res_sle_row['name']).''; ?></div>
        </div>
<?php
	}
?>
	</div>
</div>