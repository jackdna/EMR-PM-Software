<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
require_once('conDb.php'); 
$qry_med = "select * from patient_prescription_medication_healthquest_tbl WHERE confirmation_id = '".$_REQUEST['pConfId']."' order by `prescription_medication_name`";
$rsNotes = imw_query($qry_med) or die(imw_error());
$totalRows_rsNotes = imw_num_rows($rsNotes);

if($_REQUEST['refresh_meds'] == "yes") {
	while ($row_rsNotes = imw_fetch_assoc($rsNotes)){
		$med_counter +=1;
	?>
		<div class="row hoverdiv" style="cursor:pointer; background:#FFF; padding:5px 0 5px 0 ; border-bottom:1px solid #CCC;border-right:1px solid #CCC;" id="med_tr<?php echo $med_counter; ?>">
				<div class="col-md-5 col-lg-5 col-xs-5 col-sm-5" onClick="return getInnerHTMLFn('<?php echo stripslashes($row_rsNotes['prescription_medication_name']).' - '.stripslashes($row_rsNotes['prescription_medication_desc']).' - '.stripslashes($row_rsNotes['prescription_medication_sig']);?>', '6',top.frames[0].frames[0].document.getElementById('selected_frame_name_id').value,'1')"><?php echo stripslashes($row_rsNotes['prescription_medication_name']);?></div>
				<div class="col-md-3 col-lg-3 col-xs-3 col-sm-3" onClick="return getInnerHTMLFn('<?php echo stripslashes($row_rsNotes['prescription_medication_name']).' - '.stripslashes($row_rsNotes['prescription_medication_desc']).' - '.stripslashes($row_rsNotes['prescription_medication_sig']);?>', '6',top.frames[0].frames[0].document.getElementById('selected_frame_name_id').value,'1')"><?php echo stripslashes($row_rsNotes['prescription_medication_desc']);?></div>
                <div class="col-md-4 col-lg-4 col-xs-4 col-sm-4" onClick="return getInnerHTMLFn('<?php echo stripslashes($row_rsNotes['prescription_medication_name']).' - '.stripslashes($row_rsNotes['prescription_medication_desc']).' - '.stripslashes($row_rsNotes['prescription_medication_sig']);?>', '6',top.frames[0].frames[0].document.getElementById('selected_frame_name_id').value,'1')"><?php echo stripslashes($row_rsNotes['prescription_medication_sig']);?></div>
		</div>
	<?php	
	}
}else {
?>
	<div id="preDefineSavedHealthQuestMedDiv" onMouseOver="stopCloseEkg();" onMouseOut="closeEkg('preDefineSavedHealthQuestMedDiv');" style="position:absolute; background:#FFF; display:none;overflow:auto; padding:0px; margin:0; border:1px solid #CCC;border-radius:2px; z-index:9999 !important;" class="col-md-5 col-lg-4 col-xs-5 col-sm-5">
		<div  class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%;  background:#d9534f;  padding-top:5px">
			<span onClick="document.getElementById('preDefineSavedHealthQuestMedDiv').style.display='none';" style="float:right; color:#FFF; cursor:pointer; font-family:Verdana;">X</span>
		</div>
		<div  id="preDefineSavedHealthQuestMedSubDiv" class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:150px; overflow:auto"> 
			<?php
			$rows = 5;
			$med_counter = 0;
			while ($row_rsNotes = imw_fetch_assoc($rsNotes)){
				$med_counter +=1;
				?>
				<div class="row hoverdiv" style="cursor:pointer; background:#FFF; padding:5px 0 5px 0 ; border-bottom:1px solid #CCC;border-right:1px solid #CCC;" id="med_tr<?php echo $med_counter; ?>">
						<div class="col-md-5 col-lg-5 col-xs-5 col-sm-5" onClick="return getInnerHTMLFn('<?php echo stripslashes($row_rsNotes['prescription_medication_name']).' - '.stripslashes($row_rsNotes['prescription_medication_desc']).' - '.stripslashes($row_rsNotes['prescription_medication_sig']);?>', '6',top.frames[0].frames[0].document.getElementById('selected_frame_name_id').value,'1')"><?php echo stripslashes($row_rsNotes['prescription_medication_name']);?></div>
						<div class="col-md-3 col-lg-3 col-xs-3 col-sm-3" onClick="return getInnerHTMLFn('<?php echo stripslashes($row_rsNotes['prescription_medication_name']).' - '.stripslashes($row_rsNotes['prescription_medication_desc']).' - '.stripslashes($row_rsNotes['prescription_medication_sig']);?>', '6',top.frames[0].frames[0].document.getElementById('selected_frame_name_id').value,'1')"><?php echo stripslashes($row_rsNotes['prescription_medication_desc']);?></div>
                        <div class="col-md-4 col-lg-4 col-xs-4 col-sm-4" onClick="return getInnerHTMLFn('<?php echo stripslashes($row_rsNotes['prescription_medication_name']).' - '.stripslashes($row_rsNotes['prescription_medication_desc']).' - '.stripslashes($row_rsNotes['prescription_medication_sig']);?>', '6',top.frames[0].frames[0].document.getElementById('selected_frame_name_id').value,'1')"><?php echo stripslashes($row_rsNotes['prescription_medication_sig']);?></div>
				</div>
			<?php
			}
		?>
		</div>
	</div>
<?php
}
?>