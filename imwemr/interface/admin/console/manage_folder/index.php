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
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_manage_folder.js"></script>
<body>
    <input type="hidden" name="ord_by_field" id="ord_by_field" value="folder_name">
	<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
	<div class="whtbox">
		<div class="table-responsive respotable adminnw">
			<table class="table table-bordered table-hover">
				<thead>
					<tr>
						<th style="width:20px; padding-left:8px;"><div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value=""><label for="chk_sel_all"></label></div></th>
						<th onClick="LoadResultSet('','','','folder_name',this);">Folder Name<span></span></th>
						<th onClick="LoadResultSet('','','','alertPhysician',this);">Alert Physician<span></span></th>
						<th onClick="LoadResultSet('','','','folder_status',this);">Status<span></span></th>
						<th onClick="LoadResultSet('','','','favourite',this);">is Favorite?<span></span></th>
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
						<input type="hidden" name="folder_categories_id" id="folder_categories_id" >
							<div class="form-group">
								<label for="optName">Folder Name</label>
								<input class="form-control" name="folder_name" id="folder_name" type="text">
							</div>
							<div class="form-group">
								<label for="sub_folder_1">Is Subfolder</label><br />
								<div class="checkbox checkbox-inline">
									<input type="checkbox" name="sub_folder" id="sub_folder_1" value="1"><label for="sub_folder_1"></label>
								</div>
							</div>
							<div class="form-group">
								<label for="parent_id">Parent Folder</label>
								<select disabled name="parent_id" id="parent_id"  class="form-control minimal"></select>
							</div>
							<div class="form-group">
								<label for="alertPhysician_1">Alert Physician</label><br />
								<div class="checkbox checkbox-inline">
									<input type="checkbox" name="alertPhysician" id="alertPhysician_1" value="1"><label for="alertPhysician_1"></label>
								</div>
							</div>
							<div class="form-group">
								<label for="folder_status">Status</label>
								<select name="folder_status" id="folder_status" class="form-control minimal">
									<option value="active">Active</option>
									<option value="inactive">Inactive</option>
								</select>
							</div>
							<div class="form-group">
								<label for="favourite_1">Is Favorite?</label><br />
								<div class="checkbox checkbox-inline">
									<input type="checkbox" name="favourite" id="favourite_1" value="1"><label for="favourite_1"></label>
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