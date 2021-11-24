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

require_once(dirname(__FILE__)."/../admin_header.php");
require_once(dirname(__FILE__)."/../../../library/classes/work_view/User.php");

$ousr = new User();
$list_users = $ousr->getUsersDropDown("el_user", "", "", "all_usrs", "", 1, 0, 1);

?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_med_proc_dis_settings.js"></script>
<body>
    <input type="hidden" name="ord_by_field" id="ord_by_field" value="usr_name">
	<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
	<div class="whtbox">
		<div class="table-responsive respotable adminnw">
			<table class="table table-bordered table-hover">
				<thead>
					<tr>
						<th style="width:20px; padding-left:8px;"><div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value=""><label for="chk_sel_all"></label></div></th>
						<th onClick="LoadResultSet('','','','usr_name',this);" class="link_cursor">User<span></span></th>
						<th onClick="LoadResultSet('','','','proc_display',this);" class="link_cursor">Default Sx/Procedure View<span></span></th>
				  </tr>
				</thead>
				<tbody id="result_set"></tbody>
			</table>
		</div>
	</div>
	<div id="myModal" class="modal" role="dialog">
		<div class="modal-dialog"> 
		<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header bg-primary">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title" id="modal_title">Modal Header</h4>
				</div>
				<form name="add_edit_frm" id="add_edit_frm" onSubmit="saveFormData(); return false;">
				<div class="modal-body">
					<div class="row">
					<div class="form-group form-inline">
						<input type="hidden" class="form-control" name="id" id="adm_epostId" >
						<label for="uid" class="col-sm-4" >User</label>
						<select name="uid" id="uid" class="form-control" >
						<option value="0">- All Users -</option>
						<?php echo $list_users;?>
						</select >
					</div>
					<div class="form-group">
						<label for="reason_desc" class="col-sm-4" >Default Sx/Procedure View</label>
						<div class="radio radio-inline">
						<input type="radio" name="proc_display" id="proc_display_All" value="All" ><label for="proc_display_All">All</label>
						</div>
						<div class="radio radio-inline">
						<input type="radio" name="proc_display" id="proc_display_Ret" value="Ret" ><label for="proc_display_Ret">Ret</label>
						</div>
						<div class="radio radio-inline">
						<input type="radio" name="proc_display" id="proc_display_GL" value="GL" ><label for="proc_display_GL">GL</label>
						</div>
						<div class="radio radio-inline">
						<input type="radio" name="proc_display" id="proc_display_Other" value="Other" ><label for="proc_display_Other">Other</label>
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
	require_once("../admin_footer.php");
?>