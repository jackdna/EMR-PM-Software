<div id="noRecordModal" class="modal fade in" style="top:20%"> <!--Common Alert Container-->
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header" style="padding:6px 12px;">
				<button style="color:#FFFFFF;opacity:0.9" ype="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 style="color:#FFFFFF;" class="modal-title">Alert</h4>
			</div>
			<div class="modal-body" style="min-height:auto;">
				<p style="padding: 10px;" class="text-center"></p>
			</div>
			<div class="modal-footer" style="text-align:center;margin-top:0;padding:4px;">
				<button style="margin-left:0;" id="missing_feilds" class="btn btn-primary hidden" data-dismiss="modal">Ok</button>
			</div>
		</div>
	</div>
</div>

<script>
function modalAlert(alertText){
	$('#noRecordModal .modal-body>p').addClass('text-center');
	$('#noRecordModal .modal-body>p').html(alertText);
	$("#missing_feilds").removeClass('hidden');
	//$('#noRecordModal').modal('show');
	$('#noRecordModal').modal({backdrop: "static"});
}
</script>				   
