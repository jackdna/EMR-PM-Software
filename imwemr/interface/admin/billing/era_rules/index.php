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
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_era_rules.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/work_view/js_gen.js"></script>
<style>
	#historyResult td{vertical-align:top !important}
</style>
<body>
    <input type="hidden" name="ord_by_field" id="ord_by_field" value="era_trans_method">
	<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
	<div class="whtbox">
		<div class="table-responsive respotable adminnw">
			<table class="table table-bordered table-hover">
				<thead>
					<tr>
						<th style="width:20px; padding-left:8px;"><div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value=""><label for="chk_sel_all"></label></div></th>
						<th onClick="LoadResultSet('','','','era_trans_method',this);" class="link_cursor">Method<span></span></th>
						<th colspan="2" onClick="LoadResultSet('','','','era_cas_code',this);" class="link_cursor">CAS Codes<span></span></th>
				  </tr>
				</thead>
				<tbody id="result_set"></tbody>
			</table>
		</div>
	</div>

	<!-- History -->
	<div id="historyModal" class="modal" role="dialog">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">

				<div class="modal-header bg-primary">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title" id="modal_title">ERA Rule Log</h4>
				</div>

				<div class="modal-body">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th style="width:10%">Method</th>
								<th style="width:40%">CAS Codes</th>
								<th style="width:20%">Modified On</th>
								<th style="width:30%">Modified By</th>
							</tr>
						</thead>
						<tbody id="historyResult"></tbody>
					</table>
				</div>

				<div id="module_buttons" class="ad_modal_footer modal-footer">
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>

			</div>
		</div>
	</div>
	
	<div id="myModal" class="modal" role="dialog" data-backdrop="static" data-keyboard="false">
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
								<input type="hidden"  name="era_rule_id" id="adm_epostId" >
								
								<label for="ptVisit"><strong>Method</strong></label>
								<select name="era_trans_method" id="era_trans_method" class="form-control minimal">
									<option value="">- Select -</option>									
									<?php
										$trans_arr=array("Denied","Deductible","Write Off","Adjustment","Co-Insurance","Co-Payment");
										sort($trans_arr);
										foreach($trans_arr as $k => $v){
											echo "<option value='".$v."'>".$v."</option>";										
										}
									?>
									
								</select>
							</div>
						</div>
						
						<div class="form-group">
							<div>								
								<label for="ptVisit"><strong>CAS Code</strong></label>
								<select id="era_cas_code_nw" name="era_cas_code[]" class="selectpicker" data-live-search="true" data-width="100%" multiple="multiple" data-actions-box="true">
									
									<?php
										$str = "";
										$sql = "SELECT * FROM cas_reason_code ORDER BY cas_code, cas_desc  ";
										$res = sqlStatement($sql);
										for($i=1; $row=sqlFetchArray($res);$i++){
											if(!empty($row["cas_code"])){
												$tmp = $row["cas_code"]." - ".$row["cas_desc"]; $tmp = (strlen($tmp) > 100) ? substr($tmp, 0, 100) : $tmp;
												$str .= "<option value='".$row["cas_code"]."' title='".$tmp."' >".$tmp."</option>";
											}	
										}
										echo $str;
									?>
									
								</select>
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