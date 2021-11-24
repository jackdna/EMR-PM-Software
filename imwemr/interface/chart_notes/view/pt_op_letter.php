<!-- Modal -->
<div id="opnoteModal" class="modal fade" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Patient Operative Notes - <small><?php echo $ptName_Id; ?></small></h4>
			</div>
			<div class="modal-body">
				<div  class="row">
					<div id="list_div_pon" class="col-sm-4" >
						<!-- List -->
						<?php echo $html_left_pane; ?>
					</div>
					<div class="col-sm-8">
						<!-- pdf -->
						<iframe name="frm_operative_notes"  id="frm_operative_notes" width="100%" height="100%" frameborder="1" ></iframe>
					</div>
				</div>
			</div>
			<div class="modal-footer">	
				<button type="button" class="btn btn-success" onclick="inter_operative_note()">Add Operative Notes</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
			</div>
		</div>

	</div>
</div>
<script>
	//showFile
	function opnt_showFile(id,media_id){
		if(typeof id != "undefined" && id != ""){
			var oifrm = $("#frm_operative_notes")[0];
			var browserIpad = "<?php echo $browserIpad;?>";
			var url = top.JS_WEB_ROOT_PATH+"/interface/chart_notes/requestHandler.php?elem_formAction=load_Operative_note&elem_pnRepId="+id;
			if(browserIpad=="yes") {
				window.open(''+url,'_blank','');	
			}else {
				if(typeof(media_id)=='undefined' || media_id=='' || media_id=='0') media_id = 0;
				else media_id = id;
				oifrm.src = ""+url+"&media_id="+media_id;
			}
			
		}
	}
	
	function opnt_delPnRec(id){	
		if(typeof id != "undefined" && id != ""){
			var c = confirm("Do you want to delete this report?");
			if(c){
			inter_operative_note(1, "&op=dlRprt&elem_delId="+id);
			}
		}
	}
	
	function opnt_actPnRec(id){
		if(typeof id != "undefined" && id != ""){						
			inter_operative_note(1, "&op=ActivateReport&elem_delId="+id);
		}	
	}
	
</script>