<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
require_once('common/conDb.php'); 
$qry_preorder = "SELECT * FROM preopmedicationorder GROUP BY medicationName, strength, directions ORDER BY medicationName ASC ";
$rsNotes = imw_query($qry_preorder) or die(imw_error());
$totalRows_rsNotes = imw_num_rows($rsNotes);
?>
<script>
	function getInnerHTMLmedOp(obj,streng,direc){
		var obj2 = document.getElementById('preOpMediOrder');
		var str= document.getElementById('strength');
		var directions= document.getElementById('direction');
		obj2.value=obj;
		str.value= streng;	
		directions.value=direc;
	}
</script>
<style>
.hoverdiv:hover{background:#FFF;
color:#06C;}
</style>

<div id="PreOpMedicationDiv" onMouseOver="stopCloseEkg();" onMouseOut="closeEkg('PreOpMedicationDiv');" style="position:absolute; background:#FFF; display:none;overflow:auto; padding:0px; margin:0px;;border:1px solid #CCC;border-radius:2px; z-index:999;" class="col-md-5 col-lg-6 col-xs-5 col-sm-5">  
<div  class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%;  background:#d9534f;  padding-top:5px"><span onClick="document.getElementById('PreOpMedicationDiv').style.display='none';" style="float:right; color:#FFF; cursor:pointer; font-family:Verdana;	">X</span></div>
<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:150px; overflow:auto"> 
	
	<?php
	$rows = 5;
		 $opmed_seq=0;
		 while ($row_rsNotes = imw_fetch_assoc($rsNotes)){
			$opmed_seq++;
			?>
            <div class="row hoverdiv" style="cursor:pointer; background:#FFF; padding:5px 0 5px 0 ; border-bottom:1px solid #CCC;" onClick="javascript:return getInnerHTMLmedOp('<?php echo $row_rsNotes['medicationName'];?>','<?php echo $row_rsNotes['strength'];?>','<?php echo $row_rsNotes['directions'];?>');">
            	<div class="col-md-4 col-lg-5 col-xs-4 col-sm-4"><?php echo stripslashes($row_rsNotes['medicationName']).''; ?></div>
                <div class="col-md-4 col-lg-2 col-xs-4 col-sm-4"><?php echo stripslashes($row_rsNotes['strength']).''; ?></div>
                <div class="col-md-4 col-lg-5 col-xs-4 col-sm-4"><?php echo stripslashes($row_rsNotes['directions']).''; ?></div>
            </div>
			<?php
		}
?>
</div>
</div>