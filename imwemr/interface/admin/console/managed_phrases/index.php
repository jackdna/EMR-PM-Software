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
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_managed_phrases.js"></script>
<body>
<input type="hidden" name="ord_by_field" id="ord_by_field" value="phrase">
<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
<div class="whtbox">
	<div class="table-responsive respotable adminnw">
		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<th style="width:20px; padding-left:8px;"><div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value=""><label for="chk_sel_all"></label></div></th>
					<th onClick="LoadResultSet('','','','phrase',this);">Phrases<span></span></th>
					<th style="width:250px;" onClick="LoadResultSet('','','','exam',this);">Exam<span></span></th>
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
			<div class="modal-body">
				<div class="form-group">
					<input type="hidden" class="form-control" name="phrase_id" id="phrase_id" >
					<label for="name">Phrases</label>
					<textarea name="phrase" id="phrase" rows="5" class="form-control"></textarea>
				</div>
				<div class="form-group">
					<label for="">Exam</label>
					<div class="row">
						 <div class="col-sm-2">
							<div class="checkbox"><input type="checkbox" name="exam_Pupil" id="exam_Pupil_Pupil" value="Pupil"><label for="exam_Pupil_Pupil">Pupil</label></div>
						</div>
						<div class="col-sm-2">
							<div class="checkbox"><input type="checkbox" name="exam_EOM" id="exam_EOM_EOM" value="EOM"><label for="exam_EOM_EOM">EOM</label></div>
						</div>
						<div class="col-sm-2">
							<div class="checkbox"><input type="checkbox" name="exam_Gonio" id="exam_Gonio_Gonio" value="Gonio"><label for="exam_Gonio_Gonio">Gonio</label></div>
						</div>
						<div class="col-sm-2">
							<div class="checkbox"><input type="checkbox" name="exam_Fundus" id="exam_Fundus_Fundus" value="Fundus"><label for="exam_Fundus_Fundus">Fundus</label></div>
						</div>
						<div class="col-sm-2">
							<div class="checkbox"><input type="checkbox" name="exam_SLE" id="exam_SLE_SLE" value="SLE"><label for="exam_SLE_SLE">SLE</label></div>
						</div>
					</div>
				</div>
			</div>
			<div id="module_buttons" class="ad_modal_footer modal-footer">
				<button type="submit" class="btn btn-success">Save</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
			</div>
			</div>
			</form>
		</div>
	</div>
<?php
	require_once("../../admin_footer.php");
?> 