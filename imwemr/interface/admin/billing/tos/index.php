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
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_tos.js"></script>
<body>
    <input type="hidden" name="ord_by_field" id="ord_by_field" value="tos_code">
	<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
		<div class="whtbox">
			<div class="table-responsive respotable adminnw">
				<table class="table table-bordered table-hover">
					<thead>
						<tr>
							<th style="width:20px; padding-left:8px;"><div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value=""><label for="chk_sel_all"></label></div></th>
							<th onClick="LoadResultSet('','','','tos_category',this);">Category<span></span></th>
							<th onClick="LoadResultSet('','','','tos_category_description',this);">Category Description<span></span></th>
							<th onClick="LoadResultSet('','','','tos_code',this);">TOS Code<span></span></th>
							<th onClick="LoadResultSet('','','','tos_prac_cod',this);">Practice Code<span></span></th>
							<th onClick="LoadResultSet('','','','tos_description',this);">Description<span></span></th>
							<th onClick="LoadResultSet('','','','status',this);">Status<span></span></th>
							<th onClick="LoadResultSet('','','','headquarter',this);">HQ<span></span></th>
						</tr>
					</thead>
					<tbody id="result_set"></tbody>
				</table>
			</div>
		</div>
		<div id="myModal" class="modal fade" role="dialog">
		<div class="modal-dialog modal-lg"> 
			<div class="modal-content">
				<div class="modal-header bg-primary">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title" id="modal_title">Modal Header</h4>
				</div>
				<form name="add_edit_frm" id="add_edit_frm" onSubmit="saveFormData();return false;">
					<input type="hidden" name="tos_id" id="tos_id" value="">	
					<div class="modal-body">
						<div class="row">
							<div class="col-sm-6">
								<label for="tos_category">Category</label>
								<input type="text" name="tos_category" id="tos_category" class="tos_cat form-control">
								<input type="hidden" id="tos_cat_id" name="tos_cat_id">
							</div>
							<div class="col-sm-6">
								<label for="tos_category_description">Category Description</label>
								<textarea name="tos_category_description" id="tos_category_description" class="form-control" rows="1"></textarea>
							</div>
						</div>
						<div class="row pt10">
							<div class="col-sm-6">
								<label for="tos_code">TOS Code</label>
								<input type="text" name="tos_code" id="tos_code" class="form-control">
							</div>
							<div class="col-sm-6">
								<label for="tos_prac_cod">Practice Code</label>
								<input type="text" name="tos_prac_cod" id="tos_prac_cod" class="form-control">
							</div>
						</div>
						
						<div class="row pt10">
							<div class="col-sm-6">
								<label for="tos_description">Description</label>
								<textarea name="tos_description" id="tos_description" class="form-control" rows="1"></textarea>
							</div>
							<div class="col-sm-6">
								<label for="">Status</label>
								<select name="status" id="status" class="form-control minimal"></select>
							</div>
						</div>
						<div class="row pt10">
							<div class="col-sm-12">
								<label for="">Headquarter</label>
								<div class="checkbox">
									<input type="checkbox" value="1" id="headquarter_Yes" name="headquarter">
									<label for="headquarter_Yes"></label>
								</div>
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