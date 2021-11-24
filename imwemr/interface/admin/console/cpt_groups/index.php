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
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_cpt_group.js"></script>
<body>
    <input type="hidden" name="ord_by_field" id="ord_by_field" value="cpt_group_name">
	<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
	<div class="whtbox">
		<div class="table-responsive respotable adminnw">
			<table class="table table-bordered table-hover">
				<thead>
					<tr>
						<th onClick="LoadResultSet('','','','cpt_group_name',this);">Group Name<span></span></th>
						<th onClick="LoadResultSet('','','','cpt_code_name',this);">CPT Code (Please enter Comma separated values e.g. 92014, 92015, 99231)<span></span></th>
						<th onClick="LoadResultSet('','','','cpt_group_status',this);" >Status<span></span></th>
				  </tr>
				</thead>
				<tbody id="result_set"></tbody>
			</table>
		</div>
	</div>
	<div id="myModal" class="modal fade" role="dialog">
		<form name="add_edit_frm" id="add_edit_frm" onSubmit="saveFormData();return false;">
			<div class="modal-dialog"> 
				<div class="modal-content">
					<div class="modal-header bg-primary">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title" id="modal_title">Modal Header</h4>
					</div>
					<div class="modal-body">
						<input type="hidden" name="cpt_group_id" id="cpt_group_id">
						<div class="form-group">
							<label for="cpt_group_name">Group Name</label>
							<input class="form-control" name="cpt_group_name" id="cpt_group_name" type="text">
						</div>
						<div class="form-group">
							<label for="cpt_code_name">CPT Code</label>
							<input class="form-control" name="cpt_code_name" id="cpt_code_name" type="text" data-provide="multiple" data-seperator=",">
							(Please enter Comma separated values e.g. 92014, 92015, 99231)
						</div>
						<div class="form-group">
							<label for="optName">Status</label>
							<select name="cpt_group_status" id="cpt_group_status" class="form-control minimal">
								<option value="0">Active</option>
								<option value="1">In-Active</option>
							</select>
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