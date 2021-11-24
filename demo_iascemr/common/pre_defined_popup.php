<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php require_once('common/conDb.php'); 

$qryAllergies = "select * from allergies order by `name`";
$rsNotes1 = imw_query($qryAllergies) or die(imw_error());
$totalRows_rsNotes1 = imw_num_rows($rsNotes1);
?>
<div id="evaluationPreDefineDiv" onMouseOver="stopCloseEkg();" onMouseOut="closeEkg('evaluationPreDefineDiv');" style="position:absolute; background:#FFF; display:none; overflow:auto; padding:0px;margin:235px 0;border:1px solid #CCC;border-radius:2px;z-index:999;" class="col-md-5 col-lg-4 col-xs-5 col-sm-5">
    <div  class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%;  background:#d9534f;  padding-top:5px">
        <span onClick="document.getElementById('evaluationPreDefineDiv').style.display='none';" style="float:right; color:#FFF; cursor:pointer; font-family:Verdana;">X</span>
    </div>
    
    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:150px; overflow:auto"> 


<!--<div id="evaluationPreDefineDiv" onMouseOver="stopCloseEkg();" onMouseOut="closeEkg('evaluationPreDefineDiv');"  style="position:absolute;background-color:#E0E0E0;display:none;width:250px;height:185px;z-index:99999;border:1px solid #CCC;border-radius:5px; background:#FFF; margin:80px 220px;" class="my_table_Checkall adj_tp_tabl panel panel-default bg_panel_green"> 
  <div class="" style="width:100%; height:30px;  background:#d9534f;  padding-top:5px">
  	<span onClick="document.getElementById('evaluationPreDefineDiv').style.display='none';" style="float:right; padding-right:5px; color:#FFF; cursor:pointer; font-family:Verdana;	">X</span></div>
  <div style="width:250px;height:150px;overflow:auto; ">
-->
<!--	<tr>
		<td class="alignRight" style=" background-color:#BCD2B0;"><img src="images/left.gif" style="width:3px; height:24px;"></td>
		<td class="alignRight" style=" background-color:#BCD2B0; width:100%; height:15px; text-align:right;"></td>
		<td class="alignLeft valignTop" style=" background-color:#BCD2B0;"><img src="images/right.gif" style="width:3px; height:24px;"></td>
	</tr>-->
	<?php
	$counter = 0;
	$rows = 5;
	while ($row_rsNotes = imw_fetch_assoc($rsNotes1)){
		$counter +=1;
		?>
			<!--<td colspan="3" style=" width:100%;padding-left:5px;padding-right:5px;text-align:left; cursor:pointer;" class="text_10 alignLeft" onClick="return getInnerHTMLFn(this, '6',top.frames[0].frames[0].document.getElementById('selected_frame_name_id').value)"><?php echo stripslashes($row_rsNotes['name']).'';// $getRecordSetRows['name']; ?></td>-->
		<div class="row hoverdiv" style="cursor:pointer; background:#FFF; padding:5px 0 5px 0 ; border-bottom:1px solid #CCC;" id="opmed_tr<?php echo $opmed_seq; ?>">
            	<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" onClick="return getInnerHTMLFn('<?php echo stripslashes($row_rsNotes['name']);?>', '6',top.frames[0].frames[0].document.getElementById('selected_frame_name_id').value)"> <?php echo stripslashes($row_rsNotes['name']);?></div>
        </div>
		
        <!--<div id="opmed_tr<?php echo $opmed_seq; ?>" style=" padding:5px; background:#FFF; cursor:pointer;" onClick="return getInnerHTMLFn(this, '6',top.frames[0].frames[0].document.getElementById('selected_frame_name_id').value)"> <?php echo stripslashes($row_rsNotes['name']);?></div>-->
        
<?php
	}
?>
</div>
</div>

