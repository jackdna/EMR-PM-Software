<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
require_once('conDb.php'); 
$qry_fooddrinks = "select * from fooddrinkslist order by `name`";
$rsNotes = imw_query($qry_fooddrinks) or die(imw_error());
$totalRows_food = imw_num_rows($rsNotes);
?>
<script>
function getInnerHTMLfood(obj){
	var  val = obj.innerHTML;
	var obj2 = document.getElementById('txtarea_list_food_take');
	if(obj2.value==''){
		obj2.value = val;
	}else{
		obj2.value += ', '+val;
	}
}
</script>
<div id="evaluationFoodListDiv" onMouseOver="stopCloseEkg();" onMouseOut="closeEkg('evaluationFoodListDiv');" style="position:absolute; background:#FFF; display:none; overflow:auto; padding:0px;margin:235px 0;border:1px solid #CCC;border-radius:2px;z-index:999;" class="col-md-3 col-lg-3 col-xs-4 col-sm-4">
    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%;  background:#d9534f;  padding-top:5px;color:#FFF; text-align:left;font-weight:bold;padding-left:5px;">Food List<span onClick="document.getElementById('evaluationFoodListDiv').style.display='none';" style="float:right; color:#FFF; cursor:pointer; font-family:Verdana;">X</span>
    </div>
    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:150px; overflow:auto">
<?php
	$rows = 5;
	$food_counter=0;
	while ($row_rsNotes = imw_fetch_assoc($rsNotes)){
		$food_counter++;
?>
		<div class="row hoverdiv" style="cursor:pointer; background:#FFF; padding:5px 0 5px 0 ; border-bottom:1px solid #CCC;" id="food_tr<?php echo $food_counter; ?>">
            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" onClick="return getInnerHTMLfood(this)"><?php echo stripslashes($row_rsNotes['name']).''; ?></div>
        </div>
<?php
	}
?>
	</div>
</div>