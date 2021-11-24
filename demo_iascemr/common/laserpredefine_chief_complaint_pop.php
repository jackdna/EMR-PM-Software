<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php //require_once('common/conDb.php'); 
	require_once('conDb.php'); 
$qry_cheifcomplant = "select * from laserpredefine_chiefcomplaint_tbl order by `name`";
$res_complaint = imw_query($qry_cheifcomplant) or die(imw_error());
$totalRows_complaint = imw_num_rows($res_complaint);
?>
<script>
function getInnerHTMLcheif(obj){
	var  val = obj.innerHTML;
	var obj2 = top.frames[0].frames[0].document.getElementById('txtarea_chief_complaint');
		if(obj2.value==''){
			obj2.value = val;
			}else{
			obj2.value += ', '+val;
			}
//new highlight			
		obj2.style.backgroundColor = '#FFFFFF';
		if(top.frames[0].frames[0].document.getElementById('txtarea_past_medicalhx')){
			top.frames[0].frames[0].document.getElementById('txtarea_past_medicalhx').style.backgroundColor = '#FFFFFF';
		}
		if(top.frames[0].frames[0].document.getElementById('txtarea_present_illness_hx')){
			top.frames[0].frames[0].document.getElementById('txtarea_present_illness_hx').style.backgroundColor = '#FFFFFF';
		}
		if(top.frames[0].frames[0].document.getElementById('txtarea_medications')){
			top.frames[0].frames[0].document.getElementById('txtarea_medications').style.backgroundColor = '#FFFFFF';
		}
//new highlight	end		
}
</script>
<div id="evaluationChiefComplaint" onMouseOver="stopCloseEkg();" onMouseOut="closeEkg('evaluationChiefComplaint');" style="position:absolute; background:#FFF; display:none; overflow:auto; padding:0px;margin:235px 0;border:1px solid #CCC;border-radius:2px;z-index:999;" class="col-md-5 col-lg-4 col-xs-5 col-sm-5">
    <div  class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%;  background:#d9534f;  padding-top:5px;color:#FFF; text-align:left;font-weight:bold;padding-left:5px;">Chief Conplaint<span onClick="document.getElementById('evaluationChiefComplaint').style.display='none';" style="float:right; color:#FFF; cursor:pointer; font-family:Verdana;">X</span>
    </div>
    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:150px; overflow:auto">
<?php
	$rows = 5; 
	$counter=0;
	 while ($res_complaint_row = imw_fetch_assoc($res_complaint)){
		$counter++;
?>
		<div class="row hoverdiv" style="cursor:pointer; background:#FFF; padding:5px 0 5px 0 ; border-bottom:1px solid #CCC;" id="complaint_tr<?php echo $counter; ?>">
            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" onClick="return getInnerHTMLcheif(this)"><?php echo stripslashes($res_complaint_row['name']).''; ?></div>
        </div>
<?php
	}
?>
</div>
</div>