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
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_write_code_list.js"></script>
<body>
    <input type="hidden" name="ord_by_field" id="ord_by_field" value="w_code">
	<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
	<div class="whtbox">
		<div class="table-responsive respotable adminnw">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th style="width:20px; padding-left:8px;"><div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value=""><label for="chk_sel_all"></label></div></th>
						<th onClick="LoadResultSet('','','','w_code',this);">Write Off Code<span></span></th>
						<th onClick="LoadResultSet('','','','w_desc',this);">Description<span></span></th>
						<th onClick="LoadResultSet('','','','w_default',this);">Default<span></span></td>
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
					<div class="form-group">
                    	<input type="hidden" name="w_id" id="w_id" >
						<label for="w_code">Write Off Code</label>
						<input class="form-control" name="w_code" id="w_code" required type="text">
					</div>
					<div class="form-group">
						<label for="proc_code_description">Description</label>
						<input class="form-control" name="w_desc" id="w_desc" required type="text">
					</div>
					<div class="form-group">
						<div class="checkbox">
							<input type="checkbox" name="w_default" id="w_default_yes" value="yes"><label for="w_default_yes">Default</label>
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