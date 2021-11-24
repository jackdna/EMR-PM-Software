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
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_icd10.js"></script>
<style> #myModal .table>tbody>tr>td{ border-top: 0px; } #myModal .table{ margin-bottom: 0px; } </style>
<style type="text/css">
	.num_cnt{ margin-left:3px;border:1px solid #CCC; font-size:12px; font-weight:bold; cursor:pointer; color:#666; background:#F9F8F6; font-family:Verdana, Geneva, sans-serif; padding:2px 5px 2px 5px;}
	.num_cnt.selected{ font-size:14px; color:#FFF !important; cursor:text;  background:#5c2a79 !important;}
	.grpbox { height: 315px; }
	
</style>
<body>
    <input type="hidden" name="pg_aplhabet" id="pg_aplhabet" value="A">
	<input type="hidden" name="page" id="page" value="1">
<!--	<input type="hidden" name="status" id="status" value="">-->
	<input type="hidden" name="ord_by_field" id="ord_by_field" value="icd10">
	<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
	<div class="whtbox">
		<div class="table-responsive respotable adminnw">
			<span id="select_pos" style="position:absolute;"></span>
			<table class="table table-bordered">
				<thead>
					<tr>
						<th style="width:20px; padding-left:8px;"><div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value=""><label for="chk_sel_all"></label></div></th>
						<th><span class="cptcatgr">Category <select id="dx_catagory" multiple data-actions-box="true" class="selectpicker content_box" size="1" data-container="#select_pos" onChange="LoadResultSet('','','','cat_id',this);"></select></span></th>
						<th onClick="LoadResultSet('','','','icd9',this);">ICD9<span></span></th>
						<th onClick="LoadResultSet('','','','icd9_desc',this);">ICD9 Description<span></span></th>
						<th onClick="LoadResultSet('','','','icd10',this);">ICD10<span></span></th>
						<th onClick="LoadResultSet('','','','icd10_desc',this);">ICD10 Description<span></span></th>
						<th onClick="LoadResultSet('','','','laterality',this);">Site<span></span></th>
						<th onClick="LoadResultSet('','','','staging',this);">Encounter<span></span></th>
						<th onClick="LoadResultSet('','','','severity',this);">Stage<span></span></th>
						<th>Status<span></span></th>
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
					<option value="100">100</option>
					<option value="200">200</option>
					<option value="300">300</option>
				</select>
			</div>
			<div class="clearfix"></div>
			<div class="col-sm-9 text-center">
				<ul class="pagination" id="pagenation_alpha_order"></ul>
			</div>
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
					<input type="hidden" name="node_count" id="node_count" value="0">
					<input type="hidden" name="id" id="id" value="">
					<input type="hidden" name="breadCrumb" id="breadCrumb" value="">
					<div class="modal-body" style="height:480px;overflow-y:scroll;">
						<div class="form-group" id="span_breadCrumb" name="span_breadCrumb">
						</div>
						<div class="form-group">
							<label for="cat_id">Category</label>
							<select type="text" name="cat_id" id="cat_id" class="form-control minimal"></select>
						</div>
						<div class="form-group">
							<label for="icd10_desc">ICD10 Description</label>
							<div class="row">
								<div class="col-sm-11">
									<textarea type="text" name="icd10_desc" id="icd10_desc" class="form-control minimal"></textarea>
								</div>
								<div class="col-sm-1">
									<span><img src="../../../../library/images/nodes_deactive.png" onclick="showAddNode(1);" id="node_de"><img src="../../../../library/images/nodes_active.png" onclick="showAddNode(0);" id="node_ac" class="hide"></span>
								</div>
							</div>
						</div>
						<div class="clear-fix"></div>
						<div class="form-group">
							<table id="add_node_form" class="table hide"></table>
						</div>
						<div class="form-group">
							<label for="icd10">ICD10</label>
							<input type="text" name="icd10" id="icd10" class="form-control" />
						</div>
						<div class="form-group">
							<label for="icd9_desc">ICD09 Description</label>
							<textarea type="text" name="icd9_desc" id="icd9_desc" class="form-control minimal"></textarea>
						</div>
						<div class="form-group">
							<label for="icd9">ICD9</label>
							<input type="text" name="icd9" id="icd9" class="form-control" />
						</div>
						<div class="form-group">
							<label for="laterality">Site</label>
							<select type="text" name="laterality" id="laterality" class="form-control minimal"></select>
						</div>
						<div class="form-group">
							<label for="staging">Encounter</label>
							<select type="text" name="staging" id="staging" class="form-control minimal"></select>
						</div>
						<div class="form-group">
							<label for="severity">Stage</label>
							<select type="text" name="severity" id="severity" class="form-control minimal"></select>
						</div>
						<div class="form-group">
							<label for="master_codes">Parent Code(s)</label>
							<input type="text" name="master_codes" id="master_codes" class="form-control" />
						</div>
						<div class="form-group">
							<label for="icd9">Group Heading</label>
							<input type="text" name="group_heading" id="group_heading" class="form-control" />
						</div>
						<div class="form-group">
							<label for="no_bilateral">No Bilateral</label><br />
							<div class="checkbox checkbox-inline">
								<input type="checkbox" name="no_bilateral" id="no_bilateral" value="1"><label for="no_bilateral"></label>
							</div>
						</div>
						<div class="form-group">
							<label for="status">Status</label>
							<select type="text" name="status" id="status" class="form-control minimal"></select>
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