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
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_add_insurance_case.js"></script>
<body>
    <input type="hidden" name="ord_by_field" id="ord_by_field" value="case_name">
	<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
	<div class="whtbox">
		<div class="table-responsive respotable adminnw">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th style="width:20px; padding-left:8px;"><div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value=""><label for="chk_sel_all"></label></div></th>
						<th onClick="LoadResultSet('','','','case_name',this);">Case Type Name<span></span></th>
						<th onClick="LoadResultSet('','','','vision',this);">Vision<span></span></th>
						<th onClick="LoadResultSet('','','','normal',this);">Normal<span></span></th>
						<th onClick="LoadResultSet('','','','default_selected',this);">Default<span></span></th>
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
					<input type="hidden" name="case_id" id="case_id" value="">	
					<div class="modal-body">
						<div class="form-group">
							<label for="case_name">Case Type Name</label>
							<input type="text" name="case_name" id="case_name" class="form-control" />
						</div>
						<div class="form-group pt10">
							<label for="">Case Type</label><br />
							<div class="checkbox checkbox-inline">
								<input onClick="chkboxvalue('normal_Yes')" type="checkbox" value="1" id="vision_Yes" name="vision">
								<label for="vision_Yes">Vision</label>
							</div>
							<div class="checkbox checkbox-inline">
								<input onClick="chkboxvalue('vision_Yes')" type="checkbox" value="1" id="normal_Yes" name="normal">
								<label for="normal_Yes">Normal</label>
							</div>
						</div>
						<div class="form-group pt10">
							<label for=""></label>
							<div class="checkbox checkbox-inline">
								<input type="checkbox" value="1" id="default_selected_Yes" name="default_selected">
								<label for="default_selected_Yes">Default</label>
							</div>
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
	require_once("../../admin_footer.php");
?>	
       