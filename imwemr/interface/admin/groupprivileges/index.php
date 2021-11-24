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

echo '<script>top.show_loading_image("show");</script>
	<style>		
		.adminnw tbody td{ text-align:justify; }
		#result_set .checkbox label::before, #result_set .checkbox label::after{top:-2px;}
		#add_edit_frm .modal-body .row{ margin-bottom:2px; }
	</style>';
?>
<body>
	<input type="hidden" id="page" value="grp_prvlgs">
	<input type="hidden" name="ord_by_field" id="ord_by_field" value="gr_name">
	<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
	<div class="whtbox">
		<div class="table-responsive respotable adminnw">
			<table class="table table-bordered table-hover">
				<thead>
					<tr>
						<th style="width:20px; padding-left:8px;"><div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value=""><label for="chk_sel_all"></label></div></th>
						<th nowrap>Privilege Name<span></span></th>
						<th  >Group Privileges<span></span></th>
				  </tr>
				</thead>
				<tbody id="result_set"></tbody>
			</table>
		</div>
	</div>
	
	<form name="add_edit_frm" id="add_edit_frm" onSubmit="saveFormData();return false;">
	<?php 
	$zflg_in_users=2;
	include(dirname(__FILE__)."/permission.nic.php"); 
	?>
	</form>
	
	<script src="<?php echo $library_path ?>/js/admin/admin_grp_prevlgs.js"></script>
	<script src="<?php echo $library_path ?>/js/admin/admin_prvlgs_popup.js"></script>
<?php 
	require_once("../admin_footer.php");
?>