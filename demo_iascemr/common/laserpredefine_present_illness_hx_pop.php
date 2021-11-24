<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php //require_once('common/conDb.php'); 
	require_once('conDb.php'); 
$qry_present_illness_hx= "select * from  laserpredefine_hx_present_illness_tbl order by `name`";
$res_present_illness_hx= imw_query($qry_present_illness_hx) or die(imw_error());
$totalRows_present_illness_hx = imw_num_rows($res_present_illness_hx);
?>
<script>
function getInnerHTMLpremedHX(obj){
//alert("df");
	var  val = obj.innerHTML;
	//alert (val)
	//var obj1 = top.frames[0].frames[1].document.getElementById('perop_diag_area_id');
	var obj2 = top.frames[0].frames[0].document.getElementById('txtarea_present_illness_hx');
	//var len = obj1.length;
	
	//for(i=0; i<len; i++){		
		if(obj2.value==''){
			obj2.value = val;
			}else{
			obj2.value += ', '+val;
			}
//new highlight			
		obj2.style.backgroundColor = '#FFFFFF';
		if(top.frames[0].frames[0].document.getElementById('txtarea_chief_complaint')){
			top.frames[0].frames[0].document.getElementById('txtarea_chief_complaint').style.backgroundColor = '#FFFFFF';
		}
		if(top.frames[0].frames[0].document.getElementById('txtarea_past_medicalhx')){
			top.frames[0].frames[0].document.getElementById('txtarea_past_medicalhx').style.backgroundColor = '#FFFFFF';
		}
		if(top.frames[0].frames[0].document.getElementById('txtarea_medications')){
			top.frames[0].frames[0].document.getElementById('txtarea_medications').style.backgroundColor = '#FFFFFF';
		}
//new highlight	end		
}
</script>

<div id="evaluationpresent_illness_hx" onMouseOver="stopCloseEkg();" onMouseOut="closeEkg('evaluationpresent_illness_hx');" style="position:absolute; background:#FFF; display:none; overflow:auto; padding:0px;margin:235px 0;border:1px solid #CCC;border-radius:2px;z-index:999;" class="col-md-5 col-lg-4 col-xs-5 col-sm-5">
    <div  class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%;  background:#d9534f;  padding-top:5px;color:#FFF; text-align:left;font-weight:bold;padding-left:5px;">Hx. of Present Illness <span onClick="document.getElementById('evaluationpresent_illness_hx').style.display='none';" style="float:right; color:#FFF; cursor:pointer; font-family:Verdana;">X</span>
    </div>
    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:150px; overflow:auto"> 

<?php
	$rows = 5;
		$counter_pre_illnessHX=0;
		 while ($res_present_illness_hx_row = imw_fetch_assoc($res_present_illness_hx)){
			$counter_pre_illnessHX++;
			?>
			
			<div class="row hoverdiv" style="cursor:pointer; background:#FFF; padding:5px 0 5px 0 ; border-bottom:1px solid #CCC;" id="preillness_tr<?php echo $counter_pre_illnessHX; ?>">
            	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" onClick="return getInnerHTMLpremedHX(this)"> <?php echo stripslashes($res_present_illness_hx_row['name']);?></div>
        </div>
<?php
		}
?>
	</div>
</div>