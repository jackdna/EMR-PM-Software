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

require_once('../admin_header.php');
$sql_cvx = "SELECT cpt_fee_id,cpt_desc,cvx_code,cpt_cat_id,cpt4_code FROM cpt_fee_tbl WHERE cvx_code != '' AND delete_status = '0' order by cpt_desc";
$rs=imw_query($sql_cvx);
while($res=imw_fetch_array($rs)){
	if($res["cpt4_code"]!="" && $res["cpt_desc"]!="" && $res["cpt_cat_id"]!=""){
		$arrCVXCodes[] = array($res["cvx_code"]."-".$res["cpt_desc"], $arrEmpty, $res["cpt_cat_id"]."~~".$res["cvx_code"]."~~".$res["cpt_desc"]."~~".$res["cpt4_code"]);
	}
}
?>
<script src="<?php echo $library_path ?>/js/admin/admin_immunization.js"></script>
<body>
<input type="hidden" name="ord_by_field" id="ord_by_field" value="imnzn_name">
<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
<div class="whtbox">
    <div class="table-responsive respotable">
		<table class="table table-bordered adminnw resultset">
			<thead>
			  <tr>
				<th style="width:20px; padding-left:10px;"><div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value=""><label for="chk_sel_all"></label></div></th>
				<th onClick="LoadResultSet('','','','imnzn_name',this);" class="link_cursor">Name<span></span></th>
				<th onClick="LoadResultSet('','','','imunz_cvx_coe',this);" class="link_cursor">CVX Code<span></span></th>
				<th onClick="LoadResultSet('','','','imnzn_type',this);" class="link_cursor">Type<span></span></th>
				<th onClick="LoadResultSet('','','','imnzn_numberofdoses',this);" class="link_cursor">No. of Dose<span></span></th>
				<th onClick="LoadResultSet('','','','imnzn_manufacturer',this);" class="link_cursor">Manufacturer<span></span></th>
				<th onClick="LoadResultSet('','','','imnzn_ptalerts',this);" class="link_cursor">Pt. Alerts<span></span></th>
				<th onClick="LoadResultSet('','','','imnzn_ptinstruction',this);" class="link_cursor">Pt. Instruction<span></span></th>
				<th onClick="LoadResultSet('','','','register_immunization',this);" class="link_cursor">SCP<span></span></th>
			  </tr>
			</thead>
			<tbody id="result_set"></tbody>
		</table>
    </div>
</div>
<div id="myModal" class="modal fade" role="dialog">
	<form name="add_edit_frm" id="add_edit_frm" method="post" onSubmit="saveFormData(); return false;">
		<input type="hidden" name="txtSave" id="txtSave" value="1" />
		<input type="hidden" name="imnzn_id" id="imnzn_id" value=""/>	
		<div class="modal-dialog modal-lg">
		<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header bg-primary">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title" id="modal_title">Modal Header</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-3">
							<div class="form-group">
								<label class="text_purple" for="imnzn_name"<?php echo show_tooltip("You can enter new immunization/vaccine from \"Admin -> Billing -> CPT\" panel. Please enter a new Procedure with \"CVX Code\"."); ?>>Name&nbsp;</label>
								<div class="input-group">
									<input class="form-control" type="text" id="imnzn_name" name="imnzn_name" tabindex="1"  value="" onChange="setImmunizationsValue(this.value);">
									<label class='input-group-addon'>
										<div class='dropdown'>
											<span class='dropdown-toggle' type='button' data-toggle='dropdown'><span class='caret'></span></span>
											<ul id="dlDropDown" class="dropdown-menu">
												<?php
													foreach($arrCVXCodes as $key => $val){
														$optionLabel = $val[0];
														$optionSubMenu = $val[1];
														$optionValue = $val[2];
														echo "<li ><a href='#'>".$optionLabel."</a></li>";
													}								
												?>	
											</ul>
										</div>
									</label>
								</div>
							</div>
						</div>
						<div class="col-xs-3">
							<div class="form-group">
								<label>Type</label>
								<select class="selectpicker" data-width="100%" id="imnzn_type" name="imnzn_type"></select>
							</div>
						</div>
						<div class="col-xs-3">
							<div class="form-group">
								<label>CVX Code</label>
								<input class="form-control" type="text" name="imunz_cvx_coe" id="imunz_cvx_coe" value="" tabindex="3">
							</div>
						</div>
						<div class="col-xs-3">
							<div class="form-group">
								<label>Category</label>
								<select class="form-control minimal" name="imunz_cpt_id" id="imunz_cpt_id" tabindex="4" data-width="100%" data-title="Select"></select>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-3">
							<div class="form-group">
								<label>Manufacturer</label>
								<input type="text" name="imnzn_manufacturer" id="imnzn_manufacturer" tabindex="5" value="" class="form-control" onChange="setManufacturerCode(this.value);" />
							</div>
						</div>
						<div class="col-xs-3">
							<div class="form-group">
								<label>Manu. Code</label>
								<input type="text"  tabindex="6" name="imunz_mfr_code" id="imunz_mfr_code" value="" class="form-control" />
							</div>
						</div>
						<div class="col-xs-3">
							<div class="form-group">
								<label><a href="javascript:setImmunizationOptions();" onClick="javascript:setImmunizationOptions();" style="color:#9900df;">No. of Dose</a></label>
								<input type="text" name="imnzn_numberofdoses"  id="imnzn_numberofdoses" maxlength="3" tabindex="7" value="" class="form-control" onBlur=	"setImmunizationOptions();" onChange="setImmunizationOptions();" data-call="no"/>
							</div>
						</div>
						<div class="col-xs-3">
							<div class="form-group">
								<label>&nbsp;</label>
								<div class="checkbox">
									<input type="checkbox" name="register_immunization" id="register_immunization" tabindex="8" value="1">
									<label for="register_immunization">Register with SCP</label>
								</div>	
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-3">
							<div class="form-group">
								<label>CPT4Code</label>
								<input class="form-control" type="text" tabindex="9" name="cpt4_code"  id="cpt4_code" onChange="changeClass(this);" value="" />
							</div>
						</div>
						<div class="col-xs-3">
							<div class="form-group">
								<label>Pt. Instruction</label>
								<textarea name="imnzn_ptinstruction" id="imnzn_ptinstruction" tabindex="10" class="form-control"></textarea>
							</div>
						</div>
						<div class="col-xs-3">
							<div class="form-group">
								<label>Pt. Alerts</label>
								<textarea class="form-control" name="imnzn_ptalerts" id="imnzn_ptalerts" tabindex="11"></textarea>
							</div>
						</div>
						<div class="col-xs-3">
							<div class="form-group">
								<label>Description</label>
								<textarea name="CPT_description" id="CPT_description" class="form-control" tabindex="12"></textarea>
							</div>
						</div>
					</div>
					<div class="row pt10" id="showImmunizationOptions"></div>	
				</div>
				<div id="module_buttons" class="modal-footer ad_modal_footer">
					<button type="submit" class="btn btn-success">Save</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</form>
</div>
<?php 
	include_once('../admin_footer.php');
?>