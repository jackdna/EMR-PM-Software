<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
require_once('conDb.php'); 
$qry_notes = "select * from oproomnursenotes order by `notes` ASC";
$rsNotes = imw_query($qry_notes) or die(imw_error());
$totalRows_rsNotes = imw_num_rows($rsNotes);
$adminNurseNotesArr = array();
if(imw_num_rows($rsNotes)>0) {
	while ($row_rsNotes = imw_fetch_assoc($rsNotes)){
		$adminNurseNotesArr[] = stripslashes($row_rsNotes['notes']);
	}
	natsort($adminNurseNotesArr);
}
?>
<script>
function getInnerHTMLnotes(obj){
	var  val = obj;
	var obj2 = document.getElementById('nursenotes_area_id');
	if(obj2.value==''){
		obj2.value = val;
	}else{
		obj2.value += '\n'+val;
	}
	textAreaAdjust(obj2);
}

</script>
<div id="evaluationNurseNotesDiv" onMouseOver="stopCloseEkg();" onMouseOut="closeEkg('evaluationNurseNotesDiv');" style="position:absolute; background:#FFF; display:none;overflow:auto; padding:0px;border:1px solid #CCC;border-radius:2px;z-index:9999;" class="col-md-4 col-lg-3 col-xs-4 col-sm-4"> 
	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%; background:#d9534f;  padding-top:5px;color:#FFF; text-align:left;font-weight:bold;padding-left:5px;">Nurse Notes<span onClick="document.getElementById('evaluationNurseNotesDiv').style.display='none';" style="float:right; color:#FFF; cursor:pointer; ">X</span></div>
	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:150px;overflow:auto;cursor:pointer;margin:0px;padding:0px; background:#FFF;"> 
	<?php
	$rows = 5;
	$nursenotes_seq =0;
	foreach($adminNurseNotesArr as $adminNurseNotesVal) {
		$nursenotes_seq++;
		?>
         <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 hoverdiv" style="padding:5px; border-bottom:1px solid #CCC;" onClick="return getInnerHTMLnotes('<?php echo $adminNurseNotesVal; ?>')"> 
			<?php echo $adminNurseNotesVal; ?>
        </div>
	<?php
	}
?>
</div>
</div>