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
 
 File: index.php
 Purpose: A router to route to selected document section
 Access Type: Indirect Access.
*/
require_once("../admin_header.php");

require_once($GLOBALS['srcdir']."/classes/admin/GroupPrevileges.php");
require_once($GLOBALS['srcdir']."/classes/work_view/User.php");

$oGroupPrevileges = new GroupPrevileges();

echo '<script>top.show_loading_image("show");</script>
	<style>
		th:nth-child(1){width:20%;}
		#logModal th:nth-child(1){width:12%;}
		#result_set td, #logModal td{ vertical-align:top!important;}
		#usr_acrdn .panel-heading .checkbox, #usr_acrdn .panel-heading h4{ display:inline; }
		#el_privileges{ min-width:100px; }
		#chk_sel_users~label{ font-size:16px!important; }
		.th_usrs .checkbox{ margin-left:16px; }
		.th_usrs .checkbox label{ padding-left:10px; }
		#dv_usrs .panel-heading .checkbox label::before, #dv_usrs .panel-heading .checkbox label::after  {top:-2px;}
	</style>
	';

?>
<body>
	<input type="hidden" id="page" value="change_prvlgs">
	<input type="hidden" name="ord_by_field" id="ord_by_field" value="gr_name">
	<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
	<div class="whtbox">
		<div class="table-responsive respotable ">
			<form name="add_edit_frm" id="add_edit_frm" onSubmit="return false;">
			<input type="hidden" id="uids" name="uids" value="">	
			<table class="table table-bordered">
				<thead>
					<tr>
						<th class="th_usrs" >
						<div class="checkbox"><input type="checkbox"  id="chk_sel_users" onclick="sel_all_usrs()"><label for="chk_sel_users">Users</label></div>
						</th>
						<th  >
							<ul class="nav nav-pills">
							    <li class="active"><a data-toggle="pill" href="#divPrevileges">Privileges</a></li>
							    <li><a data-toggle="pill" href="#divGroupPrevileges">Group Privileges</a></li>							     
							</ul>
						</th>
						
					</tr>
				</thead>
				<tbody id="result_set">
					<tr>
						<td>
							<div id="dv_usrs">
							<?php $oGroupPrevileges->get_users_list();?>
							</div>
						</td>
						<td>
							<div class="tab-content">
								<div id="divPrevileges" class="tab-pane fade in active">									
									<?php $flg_sel_all=1; include(dirname(__FILE__)."/permission.nic.php"); ?>
								</div>
								<div id="divGroupPrevileges" class="tab-pane fade form-inline">
									<div class="form-group">
										<label for="el_privileges" ><strong>Select Group Privileges: </strong></label>
										<select id="el_privileges" name="el_privileges" class="form-control minimal" onchange="get_prvlgs()">
										<option value="">-Select-</option>
										<?php echo $oGroupPrevileges->get_privileges_opts(); ?>
										</select>
									</div>
									<div id="dv_prvlgs"></div>
								</div>
							</div>
						</td>						
					</tr>
				</tbody>
			</table>
			</form>
		</div>
	</div>	
	
	<!-- log -->
	<!-- Modal -->
	<div class="modal fade" id="logModal" role="dialog">
		<div class="modal-dialog modal-lg">

			<!-- Modal content-->
			<div class="modal-content">
			<div class="modal-header">
			  <button type="button" class="close" data-dismiss="modal">&times;</button>
			  <h4 class="modal-title"><strong>Log: Change Privileges</strong></h4>
			</div>
			<div class="modal-body">
			  <!-- Modal content-->
			</div>
			<div class="modal-footer text-center">
			  <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
			</div>
			</div>

		</div>
	</div>
	<!-- log -->
	
	<script src="<?php echo $library_path ?>/js/admin/admin_grp_prevlgs.js"></script>
	<script src="<?php echo $library_path ?>/js/admin/admin_prvlgs_popup.js"></script>
<?php 
	require_once("../admin_footer.php");
?>