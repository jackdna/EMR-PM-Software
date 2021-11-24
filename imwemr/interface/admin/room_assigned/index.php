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
?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_room_assign.js"></script>
<body>
<input type="hidden" name="ord_by_field" id="ord_by_field" value="room_no">
<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
<div class="container-fluid">
<div class="whtbox">
    <div class="table-responsive respotable">
		<table class="table table-bordered adminnw tbl_fixed">
			<thead>
			  <tr>
				<th style="width:2%"><div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value=""><label for="chk_sel_all"></label></div></th>
				<th style="width:25%" onClick="LoadResultSet('','','','mac_address',this);" class="link_cursor">PC Name<span></span></th>
				<th style="width:25%" onClick="LoadResultSet('','','','room_no',this);" class="link_cursor">Room<span></span></th>
				<th style="width:25%" onClick="LoadResultSet('','','','descs',this);" class="link_cursor">Description <span></span></th>
				<th style="width:23%" onClick="LoadResultSet('','','','name',this);" class="link_cursor">Facility Name<span></span></th>
			  </tr>
			</thead>
			<tbody id="result_set"></tbody>
		</table>
    </div>
</div>
<div id="myModal" class="modal fade" role="dialog">
	<form name="add_edit_frm" id="add_edit_frm" onSubmit="check_form();return false;">
		<div class="modal-dialog"> 
		<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header bg-primary">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title" id="modal_title">Modal Header</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<input type="hidden" class="form-control" name="id" id="id" />
						<div class="col-sm-12">
							<div class="form-group">
								<label for="mac_address" >PC Name:</label>
								<input type="text" class="form-control" name="mac_address"  id="mac_address" value="">
							</div>
						</div>
						
						<div class="col-sm-12">
							<div class="form-group">
								<label for="epost_pre_defines" >Room#:</label>
								<input type="text" class="form-control" name="room_no" id="room_no" value="">
							</div>
						</div>
							
						<div class="col-sm-12">
							<div class="form-group">
								<label for="Description">Description:</label>
								<textarea class="form-control" name="descs" id="descs"></textarea>
							</div>
						</div>

						<div class="col-sm-12">
							<div class="form-group">
								<label for="Facility">Facility:</label>
								<select class="selectpicker" data-title="Select Facility"  data-width="100%" name="fac_id" id="fac_id"></select>
							</div>
						</div>	
					</div>
				</div>
				<div id="module_buttons" class="modal-footer ad_modal_footer">
					<button type="submit" class="btn btn-success">Save</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</form>
</div>
</div>
<?php 
	require_once('../admin_footer.php');
?>
