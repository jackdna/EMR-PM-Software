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
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_cpt_fee_tbl.js"></script>
<style>
	.table td { padding-left: 13px!important; }
	.cptcatgr { width: 225px!important; display: inline-block!important; }
</style>
<body>
<input type="hidden" name="ord_by_field" id="ord_by_field" value="cpt_category_tbl.cpt_category">
<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
	<div class="whtbox">
		<div class="adminnw">
			<table class="table table-bordered table-hover">
				<thead>
					<tr class="form-inline">
						<th style="width:20px; padding-left:0px;"><div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value=""><label for="chk_sel_all"></label></div></th>
						<th><span class="form-inline cptcatgr">Category <select onChange="LoadResultSet('','','','cpt_category_tbl.cpt_category',this);" id="cpt_categories" multiple class="selectpicker content_box" size="1" data-actions-box="true"></select></span></th>
						<th onClick="LoadResultSet('','','','cpt_fee_tbl.cpt_category2',this);">Category2<span></span></th>
						<th onClick="LoadResultSet('','','','cpt_fee_tbl.cpt4_code',this);">Cpt4&nbsp;Code<span></span></th>
						<th onClick="LoadResultSet('','','','cpt_fee_tbl.not_covered',this);">Ins.&nbsp;Billed<span></span></th>
						<th onClick="LoadResultSet('','','','cpt_fee_tbl.cpt_prac_code',this);">Prac&nbsp;Code<span></span></th>
						<th><strong onClick="LoadResultSet('','','','cpt_fee_tbl.cpt_desc',this);" class="form-inline">Description<span></span></strong> <input type="text" id="search" class="form-control content_box" style="width:50%;"></th>
						<th onClick="LoadResultSet('','','','cpt_fee_tbl.cpt_comments',this);">NDC#/Comments<span></span></th>
						<th onClick="LoadResultSet('','','','cpt_fee_tbl.cvx_code',this);">CVX<span></span></th>
						<th onClick="LoadResultSet('','','','cpt_fee_tbl.rev_code',this);">Rev<span></span></th>
						<th onClick="LoadResultSet('','','','cpt_fee_tbl.tos_id',this);">TOS<span></span></th>
						<th onClick="LoadResultSet('','','','cpt_fee_tbl.units',this);">Units<span></span></th>
						<th onClick="LoadResultSet('','','','cpt_fee_tbl.mod1',this);">Mod1<span></span></th>
						<th onClick="LoadResultSet('','','','cpt_fee_tbl.mod2',this);">Mod2<span></span></th>
						<th onClick="LoadResultSet('','','','cpt_fee_tbl.mod3',this);">Mod3<span></span></th>
                        <th onClick="LoadResultSet('','','','cpt_fee_tbl.mod4',this);">Mod4<span></span></th>
						<th onClick="LoadResultSet('','','','cpt_fee_tbl.departmentId',this);">Department<span></span></th>
						<th onClick="LoadResultSet('','','','cpt_fee_tbl.elem_poe',this);">POE<span></span></th>
						<th>
							<select class="form-control minimal content_box" onChange="LoadResultSet('','','','',this,this.value);">
								<option value="">All</option>
								<option value="Active">Active</option>	
								<option value="Inactive">Inactive</option>	
							</select>
						</th>
					</tr>
				</thead>
				<tbody id="result_set"></tbody>
			</table>
		</div>
	</div>
	<div id="myModal" class="modal fade" role="dialog">
		<div class="modal-dialog" style="width:70%;"> 
			<div class="modal-content">
				<div class="modal-header bg-primary">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title" id="modal_title">Modal Header</h4>
				</div>
				<form name="add_edit_frm" id="add_edit_frm" onSubmit="saveFormData();return false;">
					<input type="hidden" name="cpt_fee_id" id="cpt_fee_id">
					<input type="hidden" name="last_cnt" id="last_cnt" value="0">
					<div class="modal-body">
						<div class="form-group">
							<div class="row">
								<div class="col-sm-3">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <label for="cpt_cat_id">Category</label><select class="form-control minimal" name="cpt_cat_id" id="cpt_cat_id"></select>
                                        </div>
                                        <div class="col-sm-6">	
                                            <label for="cpt_category2">Category 2</label>
                                            <select name="cpt_category2" id="cpt_category2"  class="form-control minimal">
                                                <option value="0"></option>
                                                <option value="1">Service</option>
                                                <option value="2">Material</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
								<div class="col-sm-3">
									<div class="row">
										<div class="col-sm-6">
											<label for="cpt4_code">Cpt4 Code</label><input class="form-control" name="cpt4_code" id="cpt4_code" type="text">
										</div>
										<div class="col-sm-6">	
											<label for="not_covered">Insurance Billed</label>
											<select name="not_covered" id="not_covered"  class="form-control minimal">
												<option value="0">Yes</option>
												<option value="1">No</option>
											</select>
										</div>
									</div>	
								</div>	
								<div class="col-sm-3"><label for="cpt_prac_code">Practice Code</label><input class="form-control" name="cpt_prac_code" id="cpt_prac_code" type="text"></div>
								<div class="col-sm-3"><label for="cpt_desc">Description</label><textarea rows="1" class="form-control" name="cpt_desc" id="cpt_desc"></textarea></div>
							</div>
							<div class="row pt10">
								<div class="col-sm-3"><label for="units">Units</label><input class="form-control" name="units" id="units" type="text"></div>
								<div class="col-sm-3">
									<div class="row">
										<div class="col-sm-6">
											<label for="cvx_code">CVX Code</label>
											<input class="form-control" name="cvx_code" id="cvx_code" type="text">
										</div>
										<div class="col-sm-6">
											<label for="rev_code_v">Rev Code</label>
											<input class="form-control" name="rev_code_v" id="rev_code_v" type="text">
											<input name="rev_code" id="rev_code" type="hidden">
										</div>
									</div>
								</div>
                                <div class="col-sm-3">
									<div class="row">
										<div class="col-sm-5">
											<label for="units">Departments</label>
                                            <input class="form-control" name="departmentId_v" id="departmentId_v" type="text">
                                            <input name="departmentId" id="departmentId" type="hidden">
										</div>
										<div class="col-sm-7">
											 <label for="cpt_comments">NDC#/Comments</label>
											<textarea class="form-control" name="cpt_comments" id="cpt_comments" rows="1"></textarea>
										</div>
									</div>
								</div>
                                <div class="col-sm-3">
									<div class="row">
										<div class="col-sm-6">
											<label for="unit_of_measure">Unit of Measure</label>
                                            <select name="unit_of_measure" id="unit_of_measure" class="form-control minimal">
                                            	<option value=""></option>
                                                <option value="F2">F2</option>
                                                <option value="GR">GR</option>
                                                <option value="ML">ML</option>
                                                <option value="ME">ME</option>
                                                <option value="UN">UN</option>
                                            </select>
										</div>
										<div class="col-sm-6">
											 <label for="measurement">Measurement</label>
                                			<input class="form-control" name="measurement" id="measurement" type="text">
										</div>
									</div>
								</div>
							</div>
							<div class="row pt10">
								<div class="col-sm-3">
									<div class="row">
										<div class="col-sm-6">
											<label for="tos_id_v">TOS</label>
											<input name="tos_id_v" id="tos_id_v" class="form-control" type="text">
											<input name="tos_id" id="tos_id" type="hidden">
										</div>
										<div class="col-sm-6">
											<label for="elem_poe_v">POE</label>
											<input name="elem_poe_v" id="elem_poe_v" class="form-control" type="text">
											<input name="elem_poe" id="elem_poe" type="hidden">
											<input type="hidden" name="commonlyUsed" id="commonlyUsed" value="1">
										</div>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="row">
										<div class="col-sm-3">
											<label for="mod1">Mod1</label>
											<input class="form-control mod_atypehead" name="mod1" id="mod1" type="text">
										</div>
										<div class="col-sm-3">
											<label for="mod2">Mod2</label>
											<input class="form-control mod_atypehead" name="mod2" id="mod2" type="text">
										</div>
										<div class="col-sm-3">
											<label for="mod3">Mod3</label>
											<input class="form-control mod_atypehead" name="mod3" id="mod3" type="text">
										</div>
                                        <div class="col-sm-3">
											<label for="mod4">Mod4</label>
											<input class="form-control mod_atypehead" name="mod4" id="mod4" type="text">
										</div>
									</div>
								</div>
                                <div class="col-sm-3">
									<div class="row">
										<div class="col-sm-6">
											<label for="status">Status</label>
                                            <select name="status" id="status"  class="form-control minimal">
                                                <option value="Active">Active</option>
                                                <option value="Inactive">Inactive</option>
                                            </select>
										</div>
										<div class="col-sm-6">	
											<label for="cpt_tax">Tax</label>
                                            <select name="cpt_tax" id="cpt_tax"  class="form-control minimal">
                                                <option value="0">No</option>
                                                <option value="1">Yes</option>
                                            </select>
										</div>
									</div>	
								</div>	
								<div class="col-sm-3">
									<label for="valueSet">Value Set</label>
									<input class="form-control" name="valueSet" id="valueSet" type="text">
								</div>
							</div>
							<div class="row pt10">
								<div class="col-sm-12" id="dx_tbl">
									<div id="top_row_id"></div>
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
<?php
	require_once("../../admin_footer.php");
?>