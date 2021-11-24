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
$currency = show_currency();
?>
<script type="text/javascript">
	var currency = "<?php echo $currency; ?>";
</script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_pre_auth_templates.js"></script>
<body>
    <input type="hidden" name="ord_by_field" id="ord_by_field" value="template_name">
	<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
	<div class="whtbox">
		<div class="table-responsive respotable adminnw">
			<table class="table table-bordered table-hover">
				<thead>
					<tr>
						<th style="width:20px; padding-left:8px;"><div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value=""><label for="chk_sel_all"></label></div></th>
						<th style="width:auto; text-align:left;" onClick="LoadResultSet('','','','template_name',this);">Pre Auth Templates<span></span></th>
					</tr>
				</thead>
				<tbody id="result_set"></tbody>
			</table>
		</div>
	</div>	
	<div id="myModal" class="modal fade" role="dialog">
		<div class="modal-dialog" style="width:80%;">  
			<div class="modal-content">
			<div class="modal-header bg-primary">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title" id="modal_title">Modal Header</h4>
			</div>
			<form name="add_edit_frm" id="add_edit_frm" onSubmit="saveFormData();return false;">
			<input type="hidden" name="id" id="id">
			<input type="hidden" id="last_cnt" name="last_cnt" value="0">
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-6">
						<label for="template_name">Template</label>
						<input name="template_name" id="template_name" required type="text"  class="form-control">
					</div>
					<div class="col-sm-6">
						<label for="medical_type">Request Category Code</label>
						<select name="medical_type" id="medical_type" class="form-control minimal">
							<option value="1 - Medical Care">1 - Medical Care</option>
							<option value="2 - Surgical">2 - Surgical</option>
							<option value="3 - Consulatation">3 - Consulatation</option>
						</select>
					</div>
				</div>
				<div class="row pt10">
					<div class="col-sm-1">
						<label for="t_wid">Dx1</label>
						<input class="t_wid form-control" id="dx_1" name="dx_1" type="text"  class="form-control">
					</div>
					<div class="col-sm-1">
						<label for="dx_2">Dx2</label>
						<input class="t_wid form-control" id="dx_2" name="dx_2" type="text"  class="form-control">
					</div>
					<div class="col-sm-1">
						<label for="dx_3">Dx3</label>
						<input class="t_wid form-control" id="dx_3" name="dx_3" type="text"  class="form-control">
					</div>
					<div class="col-sm-1">
						<label for="dx_4">Dx4</label>
						<input class="t_wid form-control" id="dx_4" name="dx_4" type="text"  class="form-control">
					</div>
					<div class="col-sm-1">
						<label for="dx_5">Dx5</label>
						<input class="t_wid form-control" id="dx_5" name="dx_5" type="text"  class="form-control">
					</div>
					<div class="col-sm-1">
						<label for="dx_6">Dx6</label>
						<input class="t_wid form-control" id="dx_6" name="dx_6" type="text"  class="form-control">
					</div>
					<div class="col-sm-1">
						<label for="dx_7">Dx7</label>
						<input class="t_wid form-control" id="dx_7" name="dx_7" type="text"  class="form-control">
					</div>
					<div class="col-sm-1">
						<label for="t_wid">Dx8</label>
						<input class="t_wid form-control" id="dx_8" name="dx_8" type="text"  class="form-control">
					</div>
					<div class="col-sm-1">
						<label for="t_wid">Dx9</label>
						<input class="t_wid form-control" id="dx_9" name="dx_9" type="text"  class="form-control">
					</div>
					<div class="col-sm-1">
						<label for="dx_10">Dx10</label>
						<input class="t_wid form-control" id="dx_10" name="dx_10" type="text"  class="form-control">
					</div>
					<div class="col-sm-1">
						<label for="dx_11">Dx11</label>
						<input class="t_wid form-control" id="dx_11" name="dx_11" type="text"  class="form-control">
					</div>
					<div class="col-sm-1">
						<label for="dx_12">Dx12</label>
						<input class="t_wid form-control" id="dx_12" name="dx_12" type="text"  class="form-control">
					</div>
				</div>
				<div class="row pt10">
					<div class="col-sm-12">
						<div class="table-responsive respotable adminnw">
							<table class="table table-bordered table-hover" id="auth_main_tbl" style="overflow-x:hidden;">
								<thead>
									<tr>
										<th style="width:10%;">Procedure</th>
										<th style="width:10%;">Rev. Code</th>
										<th style="width:10%;">Diagnosis</th>
										<th style="width:10%;">MOD1</th>
										<th style="width:10%;">MOD2</th>
										<th style="width:10%;">MOD3</th>
										<th style="width:10%;">Units</th>
										<th style="width:10%;">Charges</th>
										<th style="width:10%;">Comments</th>
										<th style="width:10%;"></th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
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