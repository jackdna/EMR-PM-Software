<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
require_once('conDb.php'); 
$qry_power = "select * from laserpredefine_power_tbl  order by `name`";
$res_power = imw_query($qry_power) or die(imw_error());
$totalRows_power = imw_num_rows($res_power);
?>
<script>
function getInnerHTMLpower(obj){
	var  val = obj.innerHTML;
	var obj2 = document.getElementById('txtarea_power');
		if(obj2.value==''){
			obj2.value = val;
			}else{
			obj2.value += ', '+val;
			}
	obj2.style.backgroundColor = '#FFFFFF';
	if(document.getElementById('bp_temp5')){
		document.getElementById('bp_temp5').style.backgroundColor = '#FFFFFF';
	}
	if(document.getElementById('bp_temp6')){
		document.getElementById('bp_temp6').style.backgroundColor = '#FFFFFF';
	}
	if(document.getElementById('bp_temp7')){
		document.getElementById('bp_temp7').style.backgroundColor = '#FFFFFF';
	}
	if(document.getElementById('txtarea_spot_duration')){
		document.getElementById('txtarea_spot_duration').style.backgroundColor = '#FFFFFF';
	}
	if(document.getElementById('txtarea_spot_size')){
		document.getElementById('txtarea_spot_size').style.backgroundColor = '#FFFFFF';
	}
	if(document.getElementById('txtarea_shots')){
		document.getElementById('txtarea_shots').style.backgroundColor = '#FFFFFF';
	}
	if(document.getElementById('txtarea_total_energy')){
		document.getElementById('txtarea_total_energy').style.backgroundColor = '#FFFFFF';
	}
	if(document.getElementById('txtarea_degree_of_opening')){
		document.getElementById('txtarea_degree_of_opening').style.backgroundColor = '#FFFFFF';
	}
	if(document.getElementById('txtarea_exposure')){
		document.getElementById('txtarea_exposure').style.backgroundColor = '#FFFFFF';
	}
	if(document.getElementById('txtarea_count')){
		document.getElementById('txtarea_count').style.backgroundColor = '#FFFFFF';
	}
	if(document.getElementById('bp_temp8')){
		document.getElementById('bp_temp8').style.backgroundColor = '#FFFFFF';
	}
	if(document.getElementById('bp_temp9')){
		document.getElementById('bp_temp9').style.backgroundColor = '#FFFFFF';
	}
	if(document.getElementById('bp_temp10')){
		document.getElementById('bp_temp10').style.backgroundColor = '#FFFFFF';
	}
	if(document.getElementById('txtarea_Post_ProgressNote')){
		document.getElementById('txtarea_Post_ProgressNote').style.backgroundColor = '#FFFFFF';
	}
	if(document.getElementById('txtarea_Post_Operative_Status')){
		document.getElementById('txtarea_Post_Operative_Status').style.backgroundColor = '#FFFFFF';
	}
}
</script>
<div id="evaluationpower" onMouseOver="stopCloseEkg();" onMouseOut="closeEkg('evaluationpower');" style="position:absolute; background:#FFF; display:none; overflow:auto; padding:0px;margin:235px 0;border:1px solid #CCC;border-radius:2px;z-index:999;" class="col-md-5 col-lg-4 col-xs-5 col-sm-5">
    <div  class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%;  background:#d9534f;  padding-top:5px;color:#FFF; text-align:left;font-weight:bold;padding-left:5px;">Power<span onClick="document.getElementById('evaluationpower').style.display='none';" style="float:right; color:#FFF; cursor:pointer; font-family:Verdana;">X</span>
    </div>
    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:150px; overflow:auto">
<?php
	$rows = 5;
	$counter_power=0;
	while ($res_power_row = imw_fetch_assoc($res_power)){
		$counter_power++;
?>
		<div class="row hoverdiv" style="cursor:pointer; background:#FFF; padding:5px 0 5px 0 ; border-bottom:1px solid #CCC;" id="power_tr<?php echo $counter_power; ?>">
            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" onClick="return getInnerHTMLpower(this)"><?php echo stripslashes($res_power_row['name']).''; ?></div>
        </div>
<?php
	}
?>
	</div>
</div>