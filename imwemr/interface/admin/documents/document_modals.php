<!-- Manage Category Modal  -->	
<div id="cat_show_modal" class="modal" role="dialog" data-backdrop="static">
	<div class="modal-dialog">
	<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header bg-primary">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Manage Categories</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div id="add_edt_cat" class="adminbox">
						<div class="head">
							<span>Add New Category</span>	
						</div>
						<div class="tblBg">
							<div class="row">
								<form id="category_management" name="category_management">
									<input type="hidden" id="category_edit_id" name="category_edit_id" value="">
									<div class="col-sm-8">
										<div class="row">
											<div class="form-group">
												<div class="col-sm-2">
													<label>Name</label>
												</div>	
												<div class="col-sm-10">
													<input id="manage_cat_name" type="text" name="cat_name" value="" class="form-control">	
												</div>	
											</div>
										</div>
									</div>
									<?php 
										if($doc_obj->current_tab == 'consent'){
									?>
											<div class="col-sm-2">
												<div class="checkbox">
													<input type="checkbox" id="cat_check_in" name="check_in" value="">	
													<label for="cat_check_in">Check In</label>
												</div>	
											</div>
											<div class="col-sm-2">
												<div class="checkbox">
													<input type="checkbox" id="cat_iportal" name="iportal" value="1">	
													<label for="cat_iportal">iPortal</label>
												</div>	
											</div>
									<?php 
										}
									?>	
								</form>	
							</div>	
						</div>	
					</div>	
				</div>
				<div id="manage_categories" class="row">
					<?php 
						$doc_obj->get_section_categories($doc_obj->current_tab);
					?>		
				</div>
			</div>
			<div id="module_buttons" class="modal-footer ad_modal_footer">
				<button type="button" class="btn btn-success" id="perform_btn" onclick="edit_category();">Add</button>
				<button type="button" class="btn btn-primary hide" id="reset_btn" onclick="reset_modal();">Reset</button>
				<button type="button" class="btn btn-danger hide" id="modal_del_btn" onclick="check_delete_stat(this);">Delete</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>


<!-- Package modal -->
<div id="package_show_modal" class="modal" role="dialog" data-backdrop="static">
	<div class="modal-dialog">
	<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header bg-primary">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Add New Record</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<form id="package_modal_form" name="package_modal_form">
						 <input type="hidden" name="package_category_id" id="package_category_id" >
						<div class="col-sm-12">
							<div class="form-group">
								<label>Package Name</label>	
								<input name="package_category_name" id="package_category_name" type="text" class="form-control">	
							</div>	
						</div>

						<div class="col-sm-12">
							<div class="form-group">
								<label>Consent Form</label>
								<select name="package_consent_form[]" multiple id="package_consent_form" class="selectpicker" data-width="100%" data-size="6" data-title="Select" data-actions-box="true" data-live-search="true">
									<?php 
										$consent_form_data = $doc_obj->common_array['consent_form'];
										$opt_str = '';
										foreach($consent_form_data as $obj){
											$opt_str .= '<option value="'.$obj['consent_form_id'].'">'.$obj['consent_form_name'].'</option>';
										}
										echo $opt_str;
									?>
								</select>	
							</div>	
						</div>		
					</form>
				</div>	
			</div>
			<div id="module_buttons" class="modal-footer ad_modal_footer"> 
				<button type="button" class="btn btn-success" onclick="manage_packages('update');">Save</button>	
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>	
			</div>
		</div>
	</div>
</div>