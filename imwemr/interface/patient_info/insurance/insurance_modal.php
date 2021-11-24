<!-- 
	Insurance History Modal
-->
<div id="ins_history_modal" class="modal" role="dialog" >
	<div class="modal-dialog modal-lg">
  	<!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header bg-primary">
      	<button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title" id="modal_title">Patient All Insurance History</h4>
     	</div>
      
      <div class="modal-body"></div>
      
      <div id="module_buttons" class="modal-footer ad_modal_footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
      
    </div>
  </div>
</div>
<!-- Insurance History Modal -->

<!-- 
	Start Search Patient for policy holder
-->
<div id="search_patient_result" class="modal" role="dialog" >
	<div class="modal-dialog modal-md">
  	<!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header bg-primary">
      	<button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title col-xs-4 col-sm-5 col-md-3" id="modal_title">Select Patient</h4>
        <span class="col-xs-6 col-md-5 input-group">
        	<input type="text" id="sp_ajax" class="form-control" title="Search Patient (by last name) " placeholder="Search Patient (by last name)" data-action="search_patient" data-fld="Active" data-grid="" data-i-key="1" />
          <label class="input-group-addon btn" id="sp_ajax_btn">
          	<span class="glyphicon glyphicon-search"></span>
        	</label>
       	</span>
     	</div>
      
      <div class="modal-body pointer" style="min-height:380px; max-height:380px; overflow:hidden; overflow-y:auto;">
      	<div class="loader"></div>
      </div>
      
      <div id="module_buttons" class="modal-footer ad_modal_footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
      
    </div>
  </div>
</div>
<!-- Search Patient for policy holder -->

<!-- 
	Start Search Physician
-->
<div id="search_physician_result" class="modal" role="dialog" >
	<div class="modal-dialog modal-lg">
  	<!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header bg-primary">
      	<button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title col-xs-4 col-sm-3" id="modal_title">Select Physician</h4>
        <div class="col-xs-7 col-sm-8 form-inline">
        <select class="selectpicker col-xs-4" id="search_by" title="Search By">
          	<option value="LastName" selected="selected">Last Name</option>
						<option value="FirstName">First Name</option>
						<option value="Address1">Street Address</option>
                        <option value="PractiseName">Practice Name</option>
						<option value="physician_phone">Phone Number</option>
						<option value="physician_fax">Fax Number</option>
       	</select>&nbsp;For&nbsp;
        <span class="col-xs-7 input-group">
        	<input type="text" id="phy_ajax" class="form-control" title="Search Physician" placeholder="Search Physician" data-action="search_physician"data-text-box="" data-id-box="" />
          <label class="input-group-addon btn search_physician" id="phy_ajax_btn" title="Click to Search" data-source="phy_ajax">
          	<span class="glyphicon glyphicon-search"></span>
        	</label>
       	</span>
        </div>
     	</div>
      
      <div class="modal-body" style="max-height:350px; overflow:hidden; overflow-y:auto;">
      	<div class="loader"></div>
      </div>
      
      <div id="module_buttons" class="modal-footer ad_modal_footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
      
    </div>
  </div>
</div>
<!-- End Search Physician -->


<!-- 
	Start Confirm Ask Sep Account
-->
<div id="divAskSepAccount" class="modal" role="dialog" >
	<div class="modal-dialog modal-sm">
  	<!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header bg-primary">
      	<button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title col-xs-4 col-sm-3" id="modal_title">imwemr</h4>
     	</div>
      
      <div class="modal-body" style="max-height:350px; overflow:hidden; overflow-y:auto;">
      	<b>Would you like to create an account for these Insurance Subscriber(s)?</b>
        <div class="clearfix"></div>
        <div class="checkbox" id="divAskSepAccountPri">
        	<input type="checkbox" name="cbkAskSepAccountPri" checked id="cbkAskSepAccountPri" />
          <label for="cbkAskSepAccountPri">Primary Insurance Subscriber?</label>
        </div>
        
        <div class="checkbox" id="divAskSepAccountSec">
        	<input type="checkbox" name="cbkAskSepAccountSec" checked id="cbkAskSepAccountSec" />
          <label for="cbkAskSepAccountSec">Secondary Insurance Subscriber?</label>
        </div>
        
        <div class="checkbox" id="divAskSepAccountTer">
        	<input type="checkbox" name="cbkAskSepAccountTer" checked id="cbkAskSepAccountTer" />
          <label for="cbkAskSepAccountTer">Tertiary Insurance Subscriber?</label>
        </div>
      </div>
      
      <div id="module_buttons" class="modal-footer ad_modal_footer">
        <input type="button" value="OK" name="btAskSepAccountButtonOK" id="btAskSepAccountButtonOK" class="btn btn-success" onClick="setSepAccHidVal('ok');">
        <input type="button" value="Cancel" name="btAskSepAccountButtonCancel" id="btAskSepAccountButtonCancel" class="btn btn-danger" onClick="setSepAccHidVal('cancel');">
      </div>
      
    </div>
  </div>
</div>
<!-- End Confirm Ask Sep Account -->