<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
require_once('conDb.php'); 
$qry_preorder = "select * from medications order by `name`";
$rsNotes = imw_query($qry_preorder) or die(imw_error());
$totalRows_rsNotes = imw_num_rows($rsNotes);
?>
<script>
function getInnerHTML(obj){
	var  val = obj;
	var obj2 = document.getElementById('pre_op_phy_area_id');
	if(obj2.value==''){
		obj2.value = val;
	}else{
		obj2.value += ', '+val;
	}
	obj2.style.backgroundColor = '#FFFFFF';
	if(document.getElementById('perop_diag_area_id')){
		document.getElementById('perop_diag_area_id').style.backgroundColor = '#FFFFFF';
	}
	textAreaAdjust(obj2);
}

</script>
<style>
.hoverdiv:hover{/*background:#FFFFFF;*/
color:#06C;}
</style>
<div id="evaluationPreOpMedDiv" onMouseOver="stopCloseEkg();" onMouseOut="closeEkg('evaluationPreOpMedDiv');" style="position:absolute; background:#FFF; display:none;overflow:auto; padding:0px; z-index:999; margin:0px;;border:1px solid #CCC;border-radius:2px;" class="col-md-3 col-lg-3 col-xs-3 col-sm-3">  
	
   	<div  class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%; background:#d9534f;  padding-top:5px"><span onClick="document.getElementById('evaluationPreOpMedDiv').style.display='none';" style="float:right; color:#FFF; cursor:pointer; ">X</span></div>
	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:150px;overflow:auto;cursor:pointer;margin:0px;padding:0px; background:#FFF;"> 
    
	<?php
	$opmed_seq=0;
	while ($row_rsNotes = imw_fetch_assoc($rsNotes)){
		$opmed_seq++;
		?>
        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 hoverdiv" style="padding:5px; border-bottom:1px solid #CCC;" onClick="return getInnerHTML('<?php echo stripslashes($row_rsNotes['name']); ?>')">
			<?php echo stripslashes($row_rsNotes['name']); ?>
        </div>
		<?php
	}
?>
	</div>	
</div>