<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php //require_once('common/conDb.php'); 

require_once('conDb.php'); 

$qry_total_energy = "select * from laserpredefine_total_energy_tbl  order by `name`";
$res_total_energy = imw_query($qry_total_energy) or die(imw_error());
$totalRows_total_energy = imw_num_rows($res_total_energy);
?>
<script>
function getInnerHTMLtotal_energy(obj){
	var  val = obj.innerHTML;
	//alert (val)
	//var obj1 = top.frames[0].frames[1].document.getElementById('perop_diag_area_id');
	var obj2 = document.getElementById('txtarea_total_energy');
	//var len = obj1.length;
	
	//for(i=0; i<len; i++){		
		if(obj2.value==''){
			obj2.value = val;
			}else{
			obj2.value += ', '+val;
			}
//new highlight			
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
//new highlight	end		
}
</script>
<div id="evaluationtotal_energy" onMouseOver="stopCloseEkg();" onMouseOut="closeEkg('evaluationtotal_energy');" style="position:absolute; background:#FFF; display:none; overflow:auto; padding:0px;margin:235px 0;border:1px solid #CCC;border-radius:2px;z-index:999;" class="col-md-5 col-lg-4 col-xs-5 col-sm-5">
    <div  class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%;  background:#d9534f;  padding-top:5px;color:#FFF; text-align:left;font-weight:bold;padding-left:5px;">Total Energy<span onClick="document.getElementById('evaluationtotal_energy').style.display='none';" style="float:right; color:#FFF; cursor:pointer; font-family:Verdana;">X</span>
    </div>
    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:150px; overflow:auto">
<?php
	$rows = 5;
	$counter_total_energy=0;
	while ($res_total_energy_row = imw_fetch_assoc($res_total_energy)){
		$counter_total_energy++;
?>
		<div class="row hoverdiv" style="cursor:pointer; background:#FFF; padding:5px 0 5px 0 ; border-bottom:1px solid #CCC;" id="total_energy_tr<?php echo $counter_total_energy; ?>">
            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" onClick="return getInnerHTMLtotal_energy(this)"><?php echo stripslashes($res_total_energy_row['name']).''; ?></div>
        </div>
<?php
	}
?>
	</div>
</div>