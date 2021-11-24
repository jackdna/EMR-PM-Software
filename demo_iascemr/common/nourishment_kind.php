<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
//require_once('common/conDb.php'); 
require_once('conDb.php'); 
$qry_Nkind = "select * from nourishmentkind order by `name`";
$rsNotes = imw_query($qry_Nkind) or die(imw_error());
$totalRows_rsNotes = imw_num_rows($rsNotes);
?>
<script>
function getInnerHTMLkind(obj){
	var  val = obj.innerHTML;
	var obj2 = document.getElementById('nour_kind_area_id');
	if(obj2.value==''){
		obj2.value = val;
	}else{
		obj2.value += ', '+val;
	}
	obj2.style.backgroundColor = '#FFFFFF';
}
</script>

<div id="evaluationNKindDiv" onMouseOver="stopCloseEkg();" onMouseOut="closeEkg('evaluationNKindDiv');" style="position:absolute; background:#FFF; display:none;overflow:auto; padding:0px; margin:0px;;border:1px solid #CCC;border-radius:2px;z-index:999;" class="col-md-3 col-lg-2 col-xs-8 col-sm-5">
<div  class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%;  background:#d9534f;  padding-top:5px"><span onClick="document.getElementById('evaluationNKindDiv').style.display='none';" style="float:right; color:#FFF; cursor:pointer; font-family:Verdana;">X</span>
</div>
<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:150px; overflow:auto"> 

	<?php
	$rows = 5; 
	$nourish_counter=0;
	while ($row_rsNotes = imw_fetch_assoc($rsNotes)){
		$rowNourishmentKindArr[] = $row_rsNotes;
		$nourish_counter++
		?>
		<div class="row hoverdiv" style="cursor:pointer; background:#FFF; padding:5px 0 5px 0 ; border-bottom:1px solid #CCC;" id="nourish_tr<?php echo $nourish_counter;?>">
            	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" onClick="return getInnerHTMLkind(this)"><?php echo stripslashes($row_rsNotes['name']).''; ?></div>
        </div>
		<?php
	}
?>
</div>
</div>