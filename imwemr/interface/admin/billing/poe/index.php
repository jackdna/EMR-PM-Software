<?php
require_once("../../admin_header.php");
?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_poe.js"></script>	
<body>
    <input type="hidden" name="ord_by_field" id="ord_by_field" value="poe_name">
	<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
	<div class="whtbox">
		<div class="table-responsive respotable adminnw">
			<table class="table table-bordered table-hover">
				<thead>
					<tr>
						<th style="width:20px; padding-left:8px;"><div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value=""><label for="chk_sel_all"></label></div></th>
						<th onClick="LoadResultSet('','','','poe_name',this);">Name<span></span></th>
						<th onClick="LoadResultSet('','','','poe_days',this);">Days<span></span></th>
						<th onClick="LoadResultSet('','','','poe_pat_message',this);">Message<span></span></th>
						<th onClick="LoadResultSet('','','','poe_scheduler,poe_medical,poe_billing',this);">POE alerts<span></span></th>
					</tr>
				</thead>
				<tbody id="result_set"></tbody>
			</table>
		</div>
	</div>
	<div id="myModal" class="modal fade" role="dialog">
		<div class="modal-dialog"> 
			<div class="modal-content">
				<div class="modal-header bg-primary">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title" id="modal_title">Modal Header</h4>
				</div>
				<form name="add_edit_frm" id="add_edit_frm" onSubmit="saveFormData();return false;">
					<input type="hidden" name="poe_messages_id" id="poe_messages_id" value="">	
					<div class="modal-body">
						<div class="form-group">
							<label for="poe_name">Name</label>
							<input type="text" name="poe_name" id="poe_name" class="form-control" />
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-sm-7" id="poe_days1">
									<label for="poe_days">Days</label>
									<select type="text" name="poe_days" id="poe_days" class="form-control minimal"></select>
								</div>
								<div class="col-sm-5" id="poe_other_days1">
									<label for="">&nbsp;</label>
									<input type="text" name="poe_other_days" id="poe_other_days" class="form-control" />
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="poe_name">Message</label>
							<textarea type="text" name="poe_pat_message" id="poe_pat_message" rows="2" class="form-control"></textarea>
						</div>
						<div class="form-group">
							<label for="">POE alerts</label><br />
							<div class="checkbox checkbox-inline">
								<input type="checkbox" value="2" id="poe_scheduler_2" name="poe_scheduler">
								<label for="poe_scheduler_2">Scheduler</label>
							</div>
							<div class="checkbox checkbox-inline">
								<input type="checkbox" value="2" id="poe_medical_2" name="poe_medical">
								<label for="poe_medical_2">Medical</label>
							</div>
							<div class="checkbox checkbox-inline">
								<input type="checkbox" value="2" id="poe_billing_2" name="poe_billing">
								<label for="poe_billing_2">Billing</label>
							</div>
						</div>
					</div>		
					<div id="module_buttons" class="modal-footer ad_modal_footer">
						<button type="submit" class="btn btn-success">Save</button>
						<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
					</div>
				</form>
			</div>
		</div>
	</div>	
<?php	
	require_once("../../admin_footer.php");
?>     