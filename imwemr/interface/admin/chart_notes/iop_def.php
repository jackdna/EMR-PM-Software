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
require_once($GLOBALS['fileroot'].'/library/classes/work_view/User.php');

$ouser = new User();
?>
<style>
  .form-horizontal .control-label{padding-top:0px;}
  .ui-autocomplete { z-index:1051; }
</style>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/work_view/typeahead.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_iop_def.js"></script>
<body>
    <input type="hidden" name="ord_by_field" id="ord_by_field" value="def_mthd">
	<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
	<div class="whtbox">
		<div class="table-responsive respotable adminnw">
			<table class="table table-bordered table-hover">
				<thead>
					<tr>
						<th style="width:20px; padding-left:8px;"><div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value=""><label for="chk_sel_all"></label></div></th>
						<th style="width:20%;" onClick="LoadResultSet('','','','def_mthd',this);" class="link_cursor">Default IOP Method<span></span></th>
						<th onClick="LoadResultSet('','','','phy_id',this);" class="link_cursor">Provider<span></span></th>
					</tr>
				</thead>
				<tbody id="result_set"></tbody>
			</table>
		</div>
	</div>

	<div id="myModal" class="modal" role="dialog">
		<div class="modal-dialog">
				<div class="modal-content">
				<div class="modal-header bg-primary">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title" id="modal_title">Modal Header</h4>
				</div>
				<form name="add_edit_frm" id="add_edit_frm" onSubmit="saveFormData();return false;">
					<div class="modal-body form-horizontal">
						<div class="form-group ">
							<input type="hidden" class="form-control" name="id" id="adm_epostId" >
							<label class="control-label col-sm-4" for="def_mthd">Default IOP Method</label>
              <div class="col-sm-8 " id="divIopElem">
							   <input class="form-control iop_method" name="def_mthd" id="def_mthd" type="text">
              </div>
						</div>
						<div class="form-group">
              <label class="control-label col-sm-4" for="phy_id">Provider</label>
              <div class="col-sm-8">
                <?php echo $ouser->getUsersDropDown("phy_id", "", "", "", "minimal", 0, "All Providers", 3 );?>
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
	require_once("../admin_footer.php");
?>
