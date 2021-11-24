<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
	require_once('conDb.php'); 
$qry_medication = "select * from medications  order by `name`";
$res_medication = imw_query($qry_medication) or die(imw_error());
$totalRows_medication = imw_num_rows($res_medication);
?>
<script>
function getInnerHTMLmedication(obj){
	var  val = obj.innerHTML;
	var obj2 = top.frames[0].frames[0].document.getElementById('txtarea_medications');
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
		if(top.frames[0].frames[0].document.getElementById('txtarea_present_illness_hx')){
			top.frames[0].frames[0].document.getElementById('txtarea_present_illness_hx').style.backgroundColor = '#FFFFFF';
		}
//new highlight	end		
}
</script>

<div id="evaluationmedication" onMouseOver="stopCloseEkg();" onMouseOut="closeEkg('evaluationmedication');" style="position:absolute; background:#FFF; display:none; overflow:auto; padding:0px;margin:235px 0;border:1px solid #CCC;border-radius:2px;z-index:999;" class="col-md-5 col-lg-4 col-xs-5 col-sm-5">
    <div  class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%;  background:#d9534f;  padding-top:5px;color:#FFF; text-align:left;font-weight:bold;padding-left:5px;">Ocular Medication & Dosage <span onClick="document.getElementById('evaluationmedication').style.display='none';" style="float:right; color:#FFF; cursor:pointer; font-family:Verdana;">X</span>
    </div>
    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:150px; overflow:auto">
		
<?php
	$rows = 5; 
	$counter_medication=0;
	while ($res_medication_row = imw_fetch_assoc($res_medication)){
		$counter_medication++;
?>
		<div class="row hoverdiv" style="cursor:pointer; background:#FFF; padding:5px 0 5px 0 ; border-bottom:1px solid #CCC;" id="medication_tr<?php echo $counter_medication; ?>">
            	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" onClick="return getInnerHTMLmedication(this)"><?php echo stripslashes($res_medication_row['name']).''; ?></div>
        </div>
<?php
	}
?>
	</div>
</div>