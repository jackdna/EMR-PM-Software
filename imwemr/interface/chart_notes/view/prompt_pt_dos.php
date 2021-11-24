<!-- Start Patient DOS Modal -->
<div id="sl_pt_dos_modal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
  	<!-- Modal content-->
	<div class="modal-content">
    	<div class="modal-header bg-primary "> 
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h4 class="modal-title" id="modal_title">Select DOS to carry forward</h4>
     	</div>
      
	<div class="modal-body"> 
		<div class="table-responsive dos_cf">
		<?php echo $htm_dos_list; ?>
		</div>	
     	</div>
      
	<div class="modal-footer ad_modal_footer" >		
		<button type="button" id="sl_pt_dos_sbmt" class="btn btn-success hidden" onClick="set_new_carry(1);">Done</button>		
     	</div>
      
    </div>
  </div>
</div>
<!-- End Patient Communication Modal -->