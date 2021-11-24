<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
require_once("../../admin_header.php");
?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_reason_code_list.js"></script>
<body>
    <input type="hidden" name="ord_by_field" id="ord_by_field" value="cas_code">
	<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
	<div class="whtbox">
		<div class="table-responsive respotable adminnw">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th style="width:20px; padding-left:8px;"><div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value=""><label for="chk_sel_all"></label></div></th>
						<th style="width:150px;" onClick="LoadResultSet('','','','cas_code',this);">Reason Code<span></span></th>
						<th onClick="LoadResultSet('','','','cas_desc',this);">Description<span></span></th>
                        <th style="width:250px;" onClick="LoadResultSet('','','','cas_action_type',this);">Action Type<span></span></th>
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
					<input type="hidden" name="cas_id" id="cas_id" value="">
					<div class="form-group">
						<label for="cas_code">Reason Code</label>
						<input class="form-control" name="cas_code" required id="cas_code" type="text">
					</div>
					<div class="form-group">
						<label for="cas_desc">Description</label>
						<input class="form-control" name="cas_desc" id="cas_desc" type="text">
					</div>
                    <div class="form-group">
						<label for="cas_action_type">Action Type</label>
                        <select name="cas_action_type" id="cas_action_type" class="form-control minimal" onChange="show_write_off_div();">
                        	<option value="">Action Type</option>
                        	<option value="Adjustment">Adjustment</option>
                            <option value="Co-Insurance">Co-Insurance</option>
                            <option value="Co-Payment">Co-Payment</option>
                            <option value="Deductible">Deductible</option>
                            <option value="Denied">Denied</option>
                            <option value="Write Off">Write Off</option>
                        </select>
					</div>
                    <div id="write_off_div">
                        <div class="form-group">
                            <div class="checkbox checkbox-inline">
                                <input type="checkbox" name="cas_update_allowed" id="cas_update_allowed_1" value="1">
                                <label for="cas_update_allowed_1">Update Allowed Amount</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="checkbox checkbox-inline">
                                <input type="checkbox" name="cas_adjustment_negative" id="cas_adjustment_negative_1" value="1">
                                <label for="cas_adjustment_negative_1">Adjustment (If Negative Amount)</label>
                            </div>
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