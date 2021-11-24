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
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_cl_charges.js"></script>
<body>
    <input type="hidden" name="ord_by_field" id="ord_by_field" value="name">
	<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
	<div class="whtbox">
		<div class="table-responsive respotable adminnw">
			<table class="table table-bordered table-hover">
				<thead>
					<tr>
						<th onClick="LoadResultSet('','','','name',this);">Save Type<span></span></th>
						<th onClick="LoadResultSet('','','','cpt_practice_code',this);">CPT Practice Code<span></span></th>
						<th onClick="LoadResultSet('','','','dx_code',this);">ICD10 Code<span></span></th>
						<th onClick="LoadResultSet('','','','price',this);">Price<span></span></th>
						<th onClick="LoadResultSet('','','','del_status',this);">Status<span></span></th>
				  </tr>
				</thead>
				<tbody id="result_set"></tbody>
			</table>
		</div>
	</div>	
	<div id="myModal" class="modal" role="dialog">
		<form name="add_edit_frm" id="add_edit_frm" onSubmit="saveFormData();return false;">
			<div class="modal-dialog"> 
				<div class="modal-content">
					<div class="modal-header bg-primary">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title" id="modal_title">Modal Header</h4>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<input type="hidden" name="cl_charge_id" id="cl_charge_id" >
							<label for="name">Save Type</label>
							<input class="form-control" name="name" id="name" type="text">
						</div>
						<div class="form-group">
							<label for="color_code">CPT Practice Code</label>
							<input class="form-control cpt_prac" name="cpt_practice_code" id="cpt_practice_code" type="text">
							 <input name="cpt_fee_id" id="cpt_fee_id" type="hidden" />
						</div>
						<div class="form-group">
							<label for="dx_code_id">ICD10 Code</label>
							<select name="dx_code_id" id="dx_code_id" class="form-control minimal"></select>
						</div>
						<div class="form-group">
							<label for="price">Price</label>
							<input class="form-control" name="price" id="price"  type="text" />
						</div>
						<div class="form-group">
							<label for="del_status">Status</label>
							<select name="del_status" id="del_status" class="form-control minimal">
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
	require_once("../admin_footer.php");
?>   