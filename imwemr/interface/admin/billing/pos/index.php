<?php
require_once("../../admin_header.php");
?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_pos.js"></script>	
<body>
<input type="hidden" name="ord_by_field" id="ord_by_field" value="pos_code">
<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
<div class="whtbox">
		<div class="table-responsive respotable">
			<table class="table table-bordered table-hover adminnw">
				<thead>
					<tr>
						<th style="width:20px; padding-left:8px;"><div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value=""><label for="chk_sel_all"></label></div></th>
						<th onClick="LoadResultSet('','','','pos_code',this);">POS Code<span></span></th>
						<th onClick="LoadResultSet('','','','pos_prac_code',this);">Practice Code <span></span></th>
						<th onClick="LoadResultSet('','','','pos_description',this);">Description <span></span></th>
						<th onClick="LoadResultSet('','','','facility',this);">Facility<span></span></th>
						<th onClick="LoadResultSet('','','','admit_discharge',this);">Admit/Disch.<span></span></th>
						<th onClick="LoadResultSet('','','','nocopay',this);">No Copay<span></span></th>
						<th onClick="LoadResultSet('','','','category_code',this);">Category Code<span></span></th>
						<th onClick="LoadResultSet('','','','status',this);">Status<span></span></th>
					</tr>
				</thead>
			<tbody id="result_set"></tbody>
		</table>
	</div>
</div>
<div id="myModal" class="modal fade" role="dialog">
	<form name="add_edit_frm" id="add_edit_frm" onSubmit="saveFormData();return false;">
		<div class="modal-dialog modal-lg"> 
			<div class="modal-content">
			<div class="modal-header bg-primary">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title" id="modal_title">Modal Header</h4>
			</div>
			
				<div class="modal-body">
					<input type="hidden" name="pos_id" id="pos_id" value="">
					<div class="row">
						<div class="col-sm-6">
							<label for="pos_code">POS Code</label>
							<input name="pos_code" id="pos_code" class="form-control" />
						</div>
						<div class="col-sm-6">
							<label for="pos_prac_code">Practice Code</label>
							<input name="pos_prac_code" id="pos_prac_code" class="form-control" />
						</div>
					</div>
					<div class="row pt10">
						<div class="col-sm-6">
							<label for="pos_description">Description</label>
							<input name="pos_description" id="pos_description" class="form-control" />
						</div>
						<div class="col-sm-6">
							<label for="facility">Facility</label>
							<select name="facility" id="facility" class="form-control minimal"></select>
						</div>
					</div>
					<div class="row pt10">
						<div class="col-sm-3">
							<label for="pos_description">Category Code</label>
							<select name="category_code" id="category_code" class="form-control minimal">
								<option value="Standard">Standard</option>
								<option value="Outside Lab">Outside Lab</option>
							</select>
						</div>
						<div class="col-sm-3">
							<label for="">Admin/Discharge</label><br />
							<div class="radio radio-inline">
								<input type="radio" id="admit_discharge_Yes" name="admit_discharge" value="Yes" >
								<label for="admit_discharge_Yes"> Yes </label>
							</div>
							<div class="radio radio-inline">
								<input type="radio" id="admit_discharge_No" name="admit_discharge" value="No" >
								<label for="admit_discharge_No"> No </label>
							</div>
						</div>
						
						<div class="col-sm-3">
							<label for="">No Copay</label><br />
							<div class="radio radio-inline">
								<input type="radio" id="nocopay_Yes" name="nocopay" value="Yes" >
								<label for="nocopay_Yes"> Yes </label>
							</div>
							<div class="radio radio-inline">
								<input type="radio" id="nocopay_No" name="nocopay" value="No" >
								<label for="nocopay_No"> No </label>
							</div>
						</div>
						<div class="col-sm-3">
							<label for="">Status</label><br />
							<div class="radio radio-inline">
								<input type="radio" id="status_Active" name="status" value="Active" >
								<label for="status_Active"> Yes </label>
							</div>
							<div class="radio radio-inline">
								<input type="radio" id="status_Inactive" name="status" value="Inactive" >
								<label for="status_Inactive"> Inactive </label>
							</div>
						</div>
					</div>
				</div>
				<div id="module_buttons" class="ad_modal_footer modal-footer">
					<button type="submit" class="btn btn-success">Save</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</div>	
		</div>
	</form>	
</div>
<?php	
	require_once("../../admin_footer.php");
?>