<!-- Modal -->
<div id="ibraModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" onclick="cnct_ibra(2);">&times;</button>
        <h4 class="modal-title">imwemr to IBRA</h4>
      </div>
      <div class="modal-body">
	
	<!-- Inner -->
		<div class="checkbox"><input type="checkbox" id="no_case_created" name="no_case_created" value="1"> <label for="no_case_created">No Case Create</label></div>
		<button onclick="cnct_ibra(1)">IBRA</button>
		<div id="ibra_output"></div>
	<!-- Inner -->
	
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal" onclick="cnct_ibra(2);">Close</button>
      </div>
    </div>

  </div>
</div>