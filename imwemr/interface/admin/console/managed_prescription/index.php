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
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_managed_prescription.js"></script>
	<body>
    <input type="hidden" name="ord_by_field" id="ord_by_field" value="pres_key">
	<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
	<div class="whtbox">
		<div class="table-responsive respotable adminnw">
			<table class="table table-bordered table-hover">
				<thead>
					<tr>
						<th style="width:20px; padding-left:8px;"><div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value=""><label for="chk_sel_all"></label></div></th>
						<th onClick="LoadResultSet('','','','pres_key',this);">Key<span></span></th>
						<th onClick="LoadResultSet('','','','drug',this);">Drug<span></span></th>
						<th onClick="LoadResultSet('','','','dosage',this);">Dosage<span></span></th>
						<th onClick="LoadResultSet('','','','qty',this);">Qty<span></span></th>
						<th onClick="LoadResultSet('','','','direction',this);">Direction<span></span></th>
						<th onClick="LoadResultSet('','','','usage_1',this);">Refill<span></span></th>
						<th onClick="LoadResultSet('','','','substitute',this);">Substitution<span></span></th>
						<th onClick="LoadResultSet('','','','chk_generic_drug_class',this);">Generic Drug Class<span></span></th>
						<th onClick="LoadResultSet('','','','chk_high_risk_medicine',this);">High Risk Medicine<span></span></th>
					</tr>
				</thead>
				<tbody id="result_set"></tbody>
			</table>
		</div>
	</div>
	<div id="myModal" class="modal fade" role="dialog">
		<form name="add_edit_frm" id="add_edit_frm" onSubmit="saveFormData();return false;">
			<div class="modal-dialog modal-lg"> 
				<div class="modal-content">
					<div class="modal-header bg-primary">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title" id="modal_title">Modal Header</h4>
					</div>
					<div class="modal-body">
						<input type="hidden" name="presc_id" id="presc_id" >
						<div class="row">
							<div class="col-sm-10">
								<div class="row">
									<div class="col-sm-3">
										<label for="optName">Key</label>
										<input name="pres_key" id="pres_key" type="text" class="form-control">
									</div>
									<div class="col-sm-3">
										<label for="drug">Drug</label>
										<input name="drug" id="drug" type="text" class="form-control">
									</div>
									<div class="col-sm-3">
										<label for="optName">Dosage</label>
										<div class="row">
											<div class="col-sm-6">
												<input name="dosage" id="dosage" type="text" class="form-control">
											</div>
											<div class="col-sm-6">
												<select name="dosage_unit" id="dosage_unit" type="text" class="form-control minimal">
													<option value=""></option>
													<option value="mg">mg</option>
													<option value="%">%</option>
												</select>
											</div>
										</div>
										
									</div>
									<div class="col-sm-3">
										<label for="drug">Quantity</label>
										<div class="row">
											<div class="col-sm-6">
												<input name="qty" id="qty" type="text" class="form-control">
											</div>
											<div class="col-sm-6">
												<select name="qty_unit" id="qty_unit" type="text" class="form-control minimal">
													<option value=""></option>
													<option value="cc">cc</option>
													<option value="ml">ml</option>
													<option value="Tabs">Tabs</option>
												</select>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-2">
								<label for="chk_generic_drug_class_Yes">Generic Drug Class</label>
								<div class="checkbox">
									<input type="checkbox" name="chk_generic_drug_class" id="chk_generic_drug_class_Yes" value="1">
									<label for="chk_generic_drug_class_Yes"></label>
								</div>
							</div>
						</div>
						<div class="row pt10">
							<div class="col-sm-10">
								<div class="row">
									<div class="col-sm-3">
										<label for="direction">Direction</label>
										<input name="direction" id="direction" type="text" class="form-control">
									</div>
									<div class="col-sm-3">
										<label for="drug">In</label>
										<select name="eye" id="eye" type="text" class="form-control minimal"></select>
									</div>
									<div class="col-sm-3">
										<label for="usage_2">Refill</label>
										<div class="row">
											<div class="col-sm-6">
												<input type="text" id="usage_2" name="usage_2" class="form-control" style="display:none">
												<select name="usage_1" id="usage_1" type="select" class="form-control minimal"></select>
											</div>
											<div class="col-sm-6">
												<select name="refill" id="refill" type="text" class="form-control minimal"></select>
											</div>
										</div>
									</div>
									<div class="col-sm-3">
										<label for="substitute">Substitution</label>
										<div class="row">
											<div class="col-sm-12">
												<select name="substitute" id="substitute" type="text" class="form-control minimal"></select>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-2">
								<label for="chk_high_risk_medicine_Yes">High Risk Medicine</label>
								<div class="checkbox">
									<input type="checkbox" name="chk_high_risk_medicine" id="chk_high_risk_medicine_Yes" value="1">
									<label for="chk_high_risk_medicine_Yes"></label>
								</div>
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