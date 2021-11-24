<?php
require_once("../../admin_header.php");
?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_modifers.js"></script>
<body>
    <input type="hidden" name="ord_by_field" id="ord_by_field" value="modifier_code">
	<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
	<div class="mainwhtbox">
		<div class="table-responsive respotable adminnw">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th style="width:20px; padding-left:8px;"><div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value=""><label for="chk_sel_all"></label></div></th>
						<th onClick="LoadResultSet('','','','modifier_code',this);">Modifier Code<span></span></th>
						<th onClick="LoadResultSet('','','','mod_prac_code',this);">Practice Code <span></span></th>
						<th onClick="LoadResultSet('','','','mod_description',this);">Description <span></span></th>
						<th onClick="LoadResultSet('','','','status',this);">Status<span></span></th>
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
				<div class="modal-body">
					<input type="hidden" name="modifiers_id" id="modifiers_id" value="">
                	<div class="form-group">
						<label for="mod_prac_code">Modifier Code</label>
						<input name="modifier_code" id="modifier_code" class="form-control" onBlur="document.getElementById('mod_prac_code').value=this.value;"/>
					</div>
					<div class="form-group">
						<label for="mod_prac_code">Practice Code</label>
						<input name="mod_prac_code" id="mod_prac_code" class="form-control" />
					</div>
                    <div class="form-group">
						<label for="mod_description">Description</label>
						<textarea name="mod_description" id="mod_description" rows="1" class="form-control"></textarea>
					</div>
					<div class="form-group">
						<label for="status">Status</label>
						<select name="status" id="status" class="form-control minimal"></select>
					</div>
				</div>
				<div id="module_buttons" class="ad_modal_footer modal-footer">
					<button type="submit" class="btn btn-success">Save</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</form>
			</div>
		</div>
	</div>
 	<?php 
		require_once('../../admin_footer.php');
	?>