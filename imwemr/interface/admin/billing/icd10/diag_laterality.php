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
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_diag_laterality.js"></script>
<body>
    <input type="hidden" name="ord_by_field" id="ord_by_field" value="title">
	<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
	<div class="whtbox">
		<div class="table-responsive respotable adminnw">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th style="width:20px; padding-left:8px;"><div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value=""><label for="chk_sel_all"></label></div></th>
						<th onClick="LoadResultSet('','','','title',this);">Laterality<span></span></th>
						<th onClick="LoadResultSet('','','','abbr',this);">Abbr<span></span></th>
						<th onClick="LoadResultSet('','','','code',this);">Code<span></span></th>
						<th onClick="LoadResultSet('','','','under',this);">Under<span></span></th>
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
					<input type="hidden" name="id" id="id" value="">	
					<div class="modal-body">
						<div class="form-group">
							<label for="title">Name</label>
							<input type="text" name="title" id="title" class="form-control" />
						</div>
						<div class="form-group">
							<label for="abbr">Abbr</label>
							<input type="text" name="abbr" id="abbr" class="form-control" />
						</div>
						<div class="form-group">
							<label for="code">Code</label>
							<input type="text" name="code" id="code" class="form-control" />
						</div>
						<div class="form-group">
							<label for="under">Under</label>
							<select type="text" name="under" id="under" class="form-control minimal"></select>
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
<?php require_once("../../admin_footer.php"); ?>    