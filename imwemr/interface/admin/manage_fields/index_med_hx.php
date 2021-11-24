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
$MedTab	= isset($_REQUEST['MedTab']) ? trim($_REQUEST['MedTab']) : '';
if($MedTab == "Ocular"){
	$strComboMedTab = "Ocular";
	$strComboMedTab1 = "Medical Hx -> Ocular";
} elseif ($MedTab == "General"){
	$strComboMedTab = "General Health";
	$strComboMedTab1 = "Medical Hx -> General Health";
}
?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_med_hx.js"></script>
<script type="text/javascript">
	var strComboMedTab = '<?php echo $strComboMedTab;?>';
</script>
<style>
	.cls_btn { padding: 0 7px 1px 7px !important; }
</style>
<body>
<input type="hidden" name="ord_by_field" id="ord_by_field" value="ques">
<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
<input type="hidden" name="strComboMedTab" id="strComboMedTab" value="<?php echo $strComboMedTab;?>">
<input type="hidden" name="strComboMedTab1" id="strComboMedTab1" value="<?php echo $strComboMedTab1;?>">
	<div class="whtbox">
		<div class="table-responsive respotable adminnw">
			<table class="table table-bordered table-hover">
				<thead>
					<tr>
						<th style="width:20px; padding-left:8px;"><div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value=""><label for="chk_sel_all"></label></div></th>
						<th style="width:280px;" onClick="LoadResultSet('','','','spl_id',this);">Specialty<span></span></th>
						<th onClick="LoadResultSet('','','','ques',this);"><?php echo $strComboMedTab;?> Question<span></span></th>
				  </tr>
				</thead>
				<tbody id="result_set"></tbody>
			</table>
		</div>
	</div>
	<div id="myModal" class="modal" role="dialog">
		<div class="modal-dialog modal-lg"> 
			<div class="modal-content">
				<div class="modal-header bg-primary">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title" id="modal_title">Modal Header</h4>
				</div>
			<form name="add_edit_frm" id="add_edit_frm" style="margin:0px;" onSubmit="saveFormData();return false;">
			<div class="modal-body">
				<div class="row">
					<input type="hidden" name="id" id="id" value="">
					<div class="col-sm-4">
						<div class="row">
							<label for="spl_id">Specialty</label><br />
							<select name="spl_id" id="spl_id" class="form-control minimal"></select>
						</div><br />
						<div class="row">
							<div class="radio radio-inline">
								<input type="radio" name="answer_type" id="answer_type_0" value="0" onClick="showDivShoOp('hide'); showOptionAns('hide');">
								<label for="answer_type_0"> Free Text </label>
							</div>
							<div class="radio radio-inline">
								<input type="radio" name="answer_type" id="answer_type_1" value="1"  onClick="showDivShoOp('show'); showOptionAns('show');">
								<label for="answer_type_1"> Multiple Options </label>
							</div>
						</div>
						<div id="divShoOp" class="label ml10" style="display:block" ><a class="clr1" href="javaScript:void(0);" onClick="showOptionAns('show');">View Options</a></div>
						
					</div>
					<div class="col-sm-8">
						<label for="ques"><?php echo $strComboMedTab;?> Question</label><br />
						<textarea rows="4" name="ques" id="ques" class="form-control"></textarea>
					</div>
				</div><br />


				<!-- ANSWER DIV -->
				<div id="divAnsOpContainer" style="display:none;">
					<div class="adminbox">
						<div class="section formlabel">
							<div class="head">
								<div class="row">
									<div class="col-sm-9">Answer Options</div>
									<div class="col-sm-3"><button type="button" class="close cls_btn" onClick="showOptionAns('hide');">&times;</button></div>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-1 text-center">
									<label>Sr No.</label>
								</div>
								<div class="col-sm-11">
									<label>Answer Options Text<label>
								</div>
							</div>
							<div id="divAnsOp" style="height:100px;  overflow: auto; overflow-x: hidden;">
								<div id="divAnswerOpts"></div>
								<input type="hidden" name="totAnswerOpts" id="totAnswerOpts" value="1">
							</div>
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
	</div>
<?php
	require_once("../admin_footer.php");
?>