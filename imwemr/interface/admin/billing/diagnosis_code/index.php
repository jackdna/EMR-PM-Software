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
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_diagnosis_code.js"></script>
<body onload="LoadResultSet();">
    <input type="hidden" name="ord_by_field" id="ord_by_field" value="category">
	<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
    <div class="whtbox">
		<div class="table-responsive respotable adminnw">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th style="width:20px; padding-left:8px;"><div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value=""><label for="chk_sel_all"></label></div></th>
						<th><span class="cptcatgr" >Category <select id="dx_catagory" multiple class="selectpicker content_box" size="1" data-container="#select_pos" onChange="LoadResultSet('','','','diagnosis_category.category',this);"></select></span></th>
						<th onClick="LoadResultSet('','','','dx_code',this);">Dx&nbsp;Code<span></span></th>
						<th onClick="LoadResultSet('','','','d_prac_code',this);">Practice&nbsp;Code<span></span></th>
						<th onClick="LoadResultSet('','','','pqriCode',this);">PQRI<span></span></th>
						<th onClick="LoadResultSet('','','','recall',this);">Recall<span></span></th>
						<th onClick="LoadResultSet('','','','diag_description',this);" class="form-inline"><strong>Description<span></span></strong> <input type="text" id="search" class="form-control" style="width:50%;"></th>
						<th onClick="LoadResultSet('','','','snowmed_ct',this);">SNOMED&nbsp;CT<span></span></th>
					</tr>
				</thead>
				<tbody id="result_set"></tbody>
			</table>
			<span id="select_pos" style="position:absolute;"></span>	
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
				<input type="hidden" name="diagnosis_id" id="diagnosis_id" value="">	
					<div class="modal-body">
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label for="category">Category</label>
									<input type="text" name="category" id="category" class="dx_cat form-control" />
									<input type="hidden" id="diag_cat_id" name="diag_cat_id">
								</div>
								<div class="form-group">
									<label for="d_prac_code">Practice Code</label>
									<input type="text" name="d_prac_code" id="d_prac_code" class="form-control" />
								</div>
								<div class="form-group">
									<label for="recall">Recall</label>
									<input type="text" name="recall" id="recall" class="form-control" />
								</div>
								<div class="form-group">
									<label for="snowmed_ct">SNOMED CT</label>
									<input type="text" name="snowmed_ct" id="snowmed_ct" class="form-control" />
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label for="dx_code">Dx Code</label>
									<input type="text" name="dx_code" id="dx_code" class="form-control" />
								</div>
								<div class="form-group">
									<label for="dx_code">PQRI</label>
									<textarea name="pqriCode" id="pqriCode" class="form-control" rows="1"></textarea>
								</div>
								<div class="form-group">
									<label for="diag_description">Description</label>
									<textarea name="diag_description" id="diag_description" class="form-control" rows="1"></textarea>
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
	<iframe name="export_csv" height="0" width="0" frameborder="0" style="display:none;" id="export_csv"></iframe>
<?php 
require_once("../../admin_footer.php");
?>