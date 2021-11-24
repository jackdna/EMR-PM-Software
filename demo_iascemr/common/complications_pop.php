<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
require_once('conDb.php'); 
$qry_compl = "select * from complications order by `name` ASC";
$rsCompl = imw_query($qry_compl) or die(imw_error());
$totalRows_rsCompl = imw_num_rows($rsCompl);
$adminComplicationsArr = array();
if(imw_num_rows($rsCompl)>0) {
	while ($row_rsCompl = imw_fetch_assoc($rsCompl)){
		$adminComplicationsArr[] = stripslashes($row_rsCompl['name']);
	}
	natsort($adminComplicationsArr);
}
?>
<script>
function getInnerHTMLcompl(obj){
	var  val = obj;
	var obj2 = document.getElementById('complications_area_id');
	if(obj2.value==''){
		obj2.value = val;
	}else{
		obj2.value += '\n'+val;
	}
	textAreaAdjust(obj2);
}

</script>
<div id="evaluationComplicationsDiv" onMouseOver="stopCloseEkg();" onMouseOut="closeEkg('evaluationComplicationsDiv');" style="position:absolute; background:#FFF; display:none;overflow:auto; padding:0px;border:1px solid #CCC;border-radius:2px;z-index:9999;" class="col-md-4 col-lg-3 col-xs-4 col-sm-4"> 
	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%; background:#d9534f;  padding-top:5px;color:#FFF; text-align:left;font-weight:bold;padding-left:5px;">Complications<span onClick="document.getElementById('evaluationComplicationsDiv').style.display='none';" style="float:right; color:#FFF; cursor:pointer; ">X</span></div>
	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:150px;overflow:auto;cursor:pointer;margin:0px;padding:0px; background:#FFF;"> 
	<?php
	$rows = 5;
	$complications_seq =0;
	foreach($adminComplicationsArr as $adminComplicationsVal) {
		$complications_seq++;
		?>
         <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 hoverdiv" style="padding:5px; border-bottom:1px solid #CCC;" onClick="return getInnerHTMLcompl('<?php echo $adminComplicationsVal; ?>')"> 
			<?php echo $adminComplicationsVal; ?>
        </div>
	<?php
	}
?>
</div>
</div>