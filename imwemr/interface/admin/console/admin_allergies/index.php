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
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_allergies.js"></script>
<body onload="LoadResultSet();">
    <input type="hidden" name="ord_by_field" id="ord_by_field" value="allergie_name">
	<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
	<input type="hidden" name="pg_aplhabet" id="pg_aplhabet" value="A">
	<input type="hidden" name="page" id="page" value="1">
	<input type="hidden" name="status" id="status" value="">
	<div class="whtbox">
		<div class="table-responsive respotable adminnw">
			<table class="table table-bordered table-hover">
				<thead>
					<tr>
						<th style="width:20px; padding-left:8px;"><div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value=""><label for="chk_sel_all"></label></div></th>
						<th onClick="LoadResultSet('','','','allergie_name',this);">Allergies<span></span></th>
						<th onClick="LoadResultSet('','','','alias',this);">Alias<span></span></th>
						<th onClick="LoadResultSet('','','','recall_code',this);">Recall Code<span></span></th>
						<th onClick="LoadResultSet('','','','procedure',this);">Procedure<span></span></th>
						<th onClick="LoadResultSet('','','','description',this);">Description<span></span></th>
					</tr>
				</thead>
				<tbody id="result_set"></tbody>
			</table>
		</div>
	</div>
	<div class="pgn_prnt">
		<div class="row ">
			<div class="col-sm-9 pagingcs text-center">
				<ul class="pagination">
					<li id="div_pages"></li>
				</ul>
			</div>
			<div class="col-sm-3 form-inline recodpag" >Records per page 
				<select class="form-control minimal" name="record_limit" id="record_limit" onChange="LoadResultSet()">
					<option value="20">20</option>
					<option value="50">50</option>
					<option value="100">100</option>
					<option value="200">200</option>
				</select>
			</div>
			<div class="clearfix"></div>
			<div class="col-sm-9 text-center">
				<ul class="pagination" id="pagenation_alpha_order"></ul>
			</div>
			<div class="col-sm-3 form-inline activuser">
				<div class="input-group">
					<input type="text" class="form-control"  name="search" id="search" >
					<div class="input-group-addon pointer" onClick="srh_records();"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></div>
				</div>
			</div>
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
						<input type="hidden" name="allergies_id" id="allergies_id" >
						<div class="form-group">
							<label for="allergie_name">Name</label>
							<input class="form-control" name="allergie_name" id="allergie_name" required type="text">
						</div>
						<div class="form-group">
							<label for="alias">Alias</label>
							<input class="form-control" name="alias" id="alias" type="text">
						</div>
						<div class="form-group">
							<label for="recall_code">Recall Code</label>
							<input class="form-control" name="recall_code" id="recall_code" type="text">
						</div>
						<div class="form-group">
							<label for="procedure">Procedure</label>
							<input class="form-control" name="procedure" id="procedure" type="text">
						</div>
						<div class="form-group">
							<label for="description">Description</label>
							<textarea class="form-control" name="description" id="description" rows='2' type="text"></textarea>
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