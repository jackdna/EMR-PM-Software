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

require_once("../admin_header.php");
?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_lasik_option.js"></script>
<body>
    <input type="hidden" name="ord_by_field" id="ord_by_field" value="op_name">
	<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
	<div class="whtbox">
		<div class="table-responsive respotable adminnw">
			<table class="table table-bordered table-hover">
				<thead>
					<tr>
						<th style="width:20px; padding-left:8px;"><div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value=""><label for="chk_sel_all"></label></div></th>
						<th onClick="LoadResultSet('','','','op_name',this);" class="link_cursor">Option<span></span></th>
						<th onClick="LoadResultSet('','','','op_type',this);" class="link_cursor">Type<span></span></th>
					</tr>
				</thead>
				<tbody id="result_set"></tbody>
			</table>
		</div>
	</div>
	<div id="myModal" class="modal" role="dialog">
		<div class="modal-dialog"> 
			<div class="modal-content">
			<div class="modal-header bg-primary">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title" id="modal_title">Modal Header</h4>
			</div>
			<form name="add_edit_frm" id="add_edit_frm" onSubmit="saveFormData();return false;">
			<div class="modal-body">
				<div class="form-group">
					<div>						
						<label for="op_type">Type</label>
						<select class="form-control" name="op_type" id="op_type" >
							<option></option>
							<option value="Laser Excimer">Laser Excimer</option>
							<option value="Laser Mode">Laser Mode</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<div>
						<input type="hidden" class="form-control" name="id" id="adm_epostId" >
						<label for="op_name">Option</label>
						<input class="form-control" name="op_name" id="op_name" type="text">
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
	require_once("../admin_footer.php");
?>      