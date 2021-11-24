<!-- 
	Start
  Scan License Image Modal 
-->
<div id="imageLicense" class="modal" role="dialog" >
	<div class="modal-dialog modal-lg">
  	<!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header bg-primary">
      	<button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title" id="modal_title">Scan License Image</h4>
     	</div>
      
      <div class="modal-body" style="max-height:450px; overflow:hidden; overflow-y:auto;">
      	<?php
        	if($data->patient_data->pt_license_image)
					{ 
						echo '<img src="'.$data->patient_data->pt_license_image.'" />';
					}
				?>
      </div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
      
    </div>
  </div>
</div>
<!-- Scan License Image Modal -->


<!-- 
	Start
  Responsible Party Driving License Image Modal 
-->
<div id="resp_party_license" class="modal" role="dialog" >
	<div class="modal-dialog modal-md">
  	<!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header bg-primary">
      	<button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title" id="modal_title">Driving License (Resp. Party)</h4>
     	</div>
      
      <div class="modal-body" style="max-height:450px; overflow:hidden; overflow-y:auto;">
      	<img src="<?php echo $resp_license_img; ?>" />
      </div>
      	
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
      
    </div>
  </div>
</div>
<!-- Responsible Party Driving License Image Modal -->

<!--
	Start Patient Override Div
-->
<div id="pt_override_div" class="modal" role="dialog" >
	<div class="modal-dialog modal-md">
  	<!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header bg-primary">
      	<button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title" id="modal_title">Pt. Override</h4>
     	</div>
      
      <div class="modal-body">
      	You do not have permission to patient override,<br>Please enter administrator password.
        <br><br>&nbsp;Password:&nbsp;<input type="password" id="user_password" />
     	</div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="done_btn_pt_override">Done</button>
        <button type="button" class="btn btn-danger" id="close_btn_pt_override" data-dismiss="modal">Close</button>
      </div>
      
    </div>
  </div>
</div>
<!-- End Patient Override Div -->


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
        	<input type="text" id="phy_ajax" class="form-control search_physician" title="Search Physician" data-source="phy_ajax" placeholder="Search Physician" data-action="search_physician"data-text-box="" data-id-box="" />
          <label class="input-group-addon btn search_physician" id="phy_ajax_btn" title="Click to Search" data-source="phy_ajax">
          	<span class="glyphicon glyphicon-search"></span>
        	</label>
       	</span>
        </div>
     	</div>
      
      <div class="modal-body">
      	<div class="loader"></div>
      </div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
      
    </div>
  </div>
</div>
<!-- End Search Physician -->

<!-- 
	Start Search Patient for family grid info AND Responsible Party
-->
<div id="search_patient_result" class="modal" role="dialog" >
	<div class="modal-dialog modal-md">
  	<!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header bg-primary">
      	<button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title col-xs-4 col-sm-5 col-md-3" id="modal_title">Select Patient</h4>
        <span class="col-xs-6 col-md-5 input-group">
        	<input type="text" id="sp_ajax" class="form-control" title="Search Patient" placeholder="Last Name, Full Name" data-action="search_patient" data-fld="Active" data-search-type="pname" data-grid="" />
          <label class="input-group-addon btn" id="sp_ajax_btn">
          	<span class="glyphicon glyphicon-search"></span>
        	</label>
       	</span>
     	</div>
      
      <div class="modal-body">
      	<div class="loader"></div>
      </div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
      
    </div>
  </div>
</div>
<!-- Search Patient for family grid info AND Responsible Party -->

<!-- 
	Start
 	Referring physician Modal 
-->
<div id="referringPhysician" class="modal" role="dialog" ></div>
<!-- Referring Physician Modal -->


<!-- 
	Start
 	Primary Care Physician Modal 
-->
<div id="primaryCarePhysician" class="modal" role="dialog" ></div>
<!-- Primary Care Physician Modal -->



<!-- 
	Start
 	Co-Managed Physician Modal 
-->
<div id="coManagedPhysician" class="modal" role="dialog" ></div>
<!-- Co-Managed Physician Modal -->


<!-- Start Race Modal -->
<div id="race_modal" class="modal" role="dialog" >
	<div class="modal-dialog modal-lg">
  	<!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header bg-primary">
			<button type="button" class="close" data-dismiss="modal">×</button>
			<h4 class="modal-title" id="modal_title">&nbsp;Race</h4>
     	</div>
		<div class="modal-body" style="max-height:450px; overflow:hidden; overflow:auto;">
    	<div class="loader-small"></div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-success" data-dismiss="modal">Done&nbsp;&&nbsp;Close</button>
		</div>
      
    </div>
  </div>
</div>
<!-- End Race Modal -->


<!-- Start Ethnicity Modal -->
<div id="ethnicity_modal" class="modal" role="dialog" >
	<div class="modal-dialog modal-lg">
  	<!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header bg-primary">
			<button type="button" class="close" data-dismiss="modal">×</button>
			<h4 class="modal-title" id="modal_title">&nbsp;Ethnicity</h4>
     	</div>
		<div class="modal-body" style="max-height:450px; overflow:hidden; overflow:auto;">
    	<div class="loader-small"></div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-success" data-dismiss="modal">Done&nbsp;&&nbsp;Close</button>
		</div>
      
    </div>
  </div>
</div>
<!-- End Ethnicity Modal -->


<!-- Start Language Modal -->
<div id="language_modal" class="modal" role="dialog" >
	<div class="modal-dialog modal-lg">
  	<!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header bg-primary">
			<button type="button" class="close" data-dismiss="modal">×</button>
			<h4 class="modal-title" id="modal_title">&nbsp;Language</h4>
     	</div>
		<div class="modal-body" style="max-height:450px; overflow:hidden; overflow:auto;">
			<div class="loader-small"></div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-success" data-dismiss="modal">Done&nbsp;&&nbsp;Close</button>
		</div>
      
    </div>
  </div>
</div>
<!-- End Language Modal -->
<!-- Start Type of Interpreter Modal -->
<div id="interpreter_modal" class="modal" role="dialog" >
	<div class="modal-dialog modal-lg">
  	<!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header bg-primary">
			<button type="button" class="close" data-dismiss="modal">×</button>
			<h4 class="modal-title" id="modal_title">&nbsp;Type of Interpreter</h4>
     	</div>
		<div class="modal-body" style="max-height:450px; overflow:hidden; overflow:auto;">
			<div class="loader-small"></div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-success" data-dismiss="modal">Done&nbsp;&&nbsp;Close</button>
		</div>
      
    </div>
  </div>
</div>
<!-- End Language Modal -->