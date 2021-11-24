<!-- Modal -->
<div id="opnoteModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
  
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>        
	<div class="row">
		<div class="col-sm-2">
			<strong>Operative Note</strong>
		</div>
		<div class="col-sm-5">
			<?php echo $ptName_Id;?>
		</div>
		<div class="col-sm-5">
			
		</div>
	</div>
      </div>
      <div class="modal-body">
		<!--Modal Content-->
		<form id="frmProgReports" method="post" >
		<!-- Hidden -->
		<input type="hidden" name="elem_saveForm" value="pnReports">
		<input type="hidden" name="elem_edit_mode" id="elem_edit_mode" value="<?php echo $elem_edit_mode; ?>">
		<input type="hidden" name="elem_edit_id" value="<?php echo $elem_edit_id; ?>">
		<input type="hidden" name="elem_patient_id" value="<?php echo $patient_id; ?>">
		<input type="hidden" name="elem_form_id" value="<?php echo $formId; ?>">
		<input type="hidden" name="elem_status" value="<?php echo (!empty($status)) ? $status : 0 ; ?>">
		<input type="hidden" name="elem_date" value="<?php echo $toDate; ?>">
		<input type="hidden" name="elem_tempId" value="<?php echo $tempId; ?>">
		<input type="hidden" name="smartTag_parentId" id="smartTag_parentId" value="">
		<!-- Hidden -->
		<div id="op_report">
			<div class="row">
				<div class="col-sm-3">
					<div class="form-group form-inline">
						<label for="elem_consentForm">Op. Note Template:</label>
						<?php echo $strSelect ;?>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group form-inline">
						<label for="elem_site">Site:</label>
						<select name="elem_site" id="elem_site" class="form-control minimal" onChange="opnt_loadTemp();" >
							<option value="">None</option>
							<option value="Right Eye" <?php if($elem_site=='Right Eye'){echo "selected"; }?>>Right Eye</option>
							<option value="Left Eye" <?php if($elem_site=='Left Eye'){echo "selected"; }?>>Left Eye</option>
							<option value="Both Eyes" <?php if($elem_site=='Both Eyes'){echo "selected"; }?>>Both Eyes</option>
						</select>
					</div>
				</div>
			</div>
			<div class="row text-center">
				<div class="col-sm-12">
				<!-- Data -->
				<textarea name="elem_pnData" id="elem_pnData" class="editr form-control" ><?php $elem_pnData; ?></textarea>
				<!-- Data -->
				</div>
			</div>
		</div>
		</form>
		<!--Modal Content-->
      </div>
      <div class="modal-footer">
      
	<button type="button" class="btn btn-success" onclick="inter_operative_note(1)">Patient Operative Notes</button>
	<button type="button" id="btn_opnote_done" class="btn btn-success" onclick="opnt_save()" >Done</button>
	<button type="button" id="btn_opnote_hold" class="btn btn-success" data-toggle="modal" data-target="#opnoteholdModal" >On Hold for:</button>
	<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
	
      </div>
    </div>    

  </div>
</div>
<!-- Modal -->

<!-- Hold -->
<div id="opnoteholdModal" class="modal fade" role="dialog">
<div class="modal-dialog">
  
<div class="div_popup bg1 border modal-content" id="hold_to_phy_div" >
	<div class="page_block_heading_patch pt4 pl5 modal-header">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h4 class="modal-title">Select Physician for Hold</h4>
	</div>
	<div class="m10 alignCenter modal-body">
		<p>
		<?php echo $select_usr_dropdown;?>		
		<input type="hidden" name="hidd_hold_to_physician" id="hidd_hold_to_physician" value="">
		</p>
	</div>
	<div class="m10 alignCenter modal-footer">
		<input type="button" class="btn btn-success hold" value="Done" onclick="opnt_hold()">
		<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
	</div>
</div>

</div>
</div>
<!-- Hold -->

<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/ckeditor/ckeditor.js"></script>
<script>
function opnt_loadTemp(){
	var s = $("#elem_tempId").val()||"";
	var e = $("#elem_site").val()||"";
	if(s!=""){
		if(e==""){ e = "OU"; $("#elem_site").val("Both Eyes");   }else{ e=e.toLowerCase();  }		
		var url = JS_WEB_ROOT_PATH+"/interface/chart_notes/onload_wv.php?elem_action=Procedures"+"&elem_opNoteId="+s+"&elem_opNoteEye="+e;				
		$.get(url, function(data){
			if(data!=""){						
				CKEDITOR.instances.elem_pnData.setData( ''+data );
			}
		});
	}
}

function opnt_save(){
	var s = $("#elem_tempId").val()||"";		
	if(typeof s != "undefined" && s != ""){			
		//document.frmProgReports.submit();
		var pd = CKEDITOR.instances['elem_pnData'].getData();
		var url = top.JS_WEB_ROOT_PATH+"/interface/chart_notes/saveCharts.php";
		var strsave=$("#frmProgReports").serialize(); 							
		strsave+="&elem_pnData="+escape(pd);
		strsave+="&savedby=ajax";
		
		
		top.show_loading_image('show', '', 'Processing. Please hold a moment...');							
		$.post(url, strsave, function(data) {				
				console.log(data);				
				top.show_loading_image('hide');
				if(data=="0"||data=="1"){
					$("#opnoteModal").modal("hide");
				}
				else{console.log(data);}
			});
	}else{
		alert("Please select operative note.");
	}
}

function opnt_hold(){
	if($('#hold_to_physician').val()==''){		
		$("<div class=\"alert alert-danger\"> Please select a physician.</div>").insertBefore("#hold_to_phy_div .modal-body");
	}else{
		$('#hidd_hold_to_physician').val($('#hold_to_physician').val());
		$("#btn_opnote_done").trigger("click");		
		$('#hold_to_phy_div .close').trigger("click");
	}
}
</script>



