<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
require_once('conDb.php'); 
$qry_exposure = "select * from laserpredefine_exposure_tbl  order by `name`";
$res_exposure = imw_query($qry_exposure) or die(imw_error());
$totalRows_exposure = imw_num_rows($res_exposure);
?>
<script>
function getInnerHTMLexposure(obj){
	var  val = obj.innerHTML;
	var obj2 = document.getElementById('txtarea_exposure');
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
	if(document.getElementById('txtarea_power')){
		document.getElementById('txtarea_power').style.backgroundColor = '#FFFFFF';
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
<div id="evaluationexposure" onMouseOver="stopCloseEkg();" onMouseOut="closeEkg('evaluationexposure');" style="position:absolute; background:#FFF; display:none; overflow:auto; padding:0px;margin:235px 0;border:1px solid #CCC;border-radius:2px;z-index:999;" class="col-md-5 col-lg-4 col-xs-5 col-sm-5">
    <div  class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%;  background:#d9534f;  padding-top:5px;color:#FFF; text-align:left;font-weight:bold;padding-left:5px;">Chief Conplaint<span onClick="document.getElementById('evaluationexposure').style.display='none';" style="float:right; color:#FFF; cursor:pointer; font-family:Verdana;">X</span>
    </div>
    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:150px; overflow:auto">
<?php
	$rows = 5;
	$counter_exposure=0;
	while ($res_exposure_row = imw_fetch_assoc($res_exposure)){
		$counter_exposure++;
?>
		<div class="row hoverdiv" style="cursor:pointer; background:#FFF; padding:5px 0 5px 0 ; border-bottom:1px solid #CCC;" id="exposure_tr<?php echo $counter_exposure; ?>">
            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" onClick="return getInnerHTMLexposure(this)"><?php echo stripslashes($res_exposure_row['name']).''; ?></div>
        </div>
<?php
	}
?>
	</div>
</div>