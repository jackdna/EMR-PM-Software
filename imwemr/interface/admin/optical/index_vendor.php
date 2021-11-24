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
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_vendor.js"></script>
<body>
    <input type="hidden" name="ord_by_field" id="ord_by_field" value="vendor_name">
	<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
    <input type="hidden" name="alphabet" id="alphabet" value="A">
	<div class="whtbox">
		<div class="table-responsive respotable adminnw">
			<table class="table table-bordered table-hover">
				<thead>
					<tr>
						<th style="width:20px; padding-left:8px;">
							<div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value="">
								<label for="chk_sel_all"></label>
							</div>
						</th>
						<th onClick="LoadResultSet('','','','vendor_name',this);">Manufacturer<span></span></th>
						<th onClick="LoadResultSet('','','','contact_name',this);">Contact Name<span></span></th>
						<th onClick="LoadResultSet('','','','vendor_address',this);">Address<span></span></th>
						<th onClick="LoadResultSet('','','','tel_num',this);">Telephone#<span></span></th>
						<th onClick="LoadResultSet('','','','mobile',this);">Mobile#<span></span></th>
						<th onClick="LoadResultSet('','','','fax',this);">Fax#<span></span></th>
						<th onClick="LoadResultSet('','','','email',this);">Email<span></span></th>
					</tr>
				</thead>
				<tbody id="result_set"></tbody>
			</table>
		</div>
	</div>
	<div class="pgn_prnt">
		<div class="text-center">
			<ul id="pagenation_alpha_order" class="pagination">
			</ul>
		</div>
	</div>
	<div id="myModal" class="modal fade" role="dialog">
		<form class="form-horizontal" name="add_edit_frm" id="add_edit_frm" onSubmit="saveFormData(); return false;">
			<div class="modal-dialog"> 
				<div class="modal-content">
					<div class="modal-header bg-primary">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title" id="modal_title">Modal Header</h4>
					</div>
					<input type="hidden" name="vendor_id" id="vendor_id" >
					<div class="modal-body">
						<div class="form-group">
							<label class="control-label col-sm-3" for="vendor_name">Manufacturer/Lab</label>
							<div class="col-sm-9">
								<input name="vendor_name" id="vendor_name" type="text" class="form-control" />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-3" for="contact_name">Contact Name</label>
							<div class="col-sm-9">
								<input name="contact_name" id="contact_name" type="text" class="form-control" />
							</div>	
						</div>
						<div class="form-group">
							<label class="control-label col-sm-3" for="contact_name">Address</label>	
							<div class="col-sm-9">
								<textarea name="vendor_address" id="vendor_address"  rows="1" class="form-control" onBlur="changeClass(this)"><?php echo $row['vendor_address']; ?></textarea>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-3" for="tel_num">Tel</label>
							<div class="col-sm-9">
								<input name="tel_num" id="tel_num"  type="text" class="form-control" onBlur="set_phone_format(this,'<?php echo $GLOBALS['phone_format'] ?>');"  />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-3" for="mobile">Mobile</label>
							<div class="col-sm-9">
								<input name="mobile" id="mobile"  type="text"  class="form-control" onBlur="set_phone_format(this,'<?php echo $GLOBALS['phone_format'] ?>');" />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-3" for="fax">Fax</label>
							<div class="col-sm-9">
								<input name="fax" id="fax" type="text" class="form-control" onBlur="set_phone_format(this,'<?php echo $GLOBALS['phone_format'] ?>');"  />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-3" for="email">Email</label>
							<div class="col-sm-9">	
								<input name="email" id="email" type="text" class="form-control" onBlur="emailvalidation(this,'Email Id is incorrect')" />
							</div>
						</div>
					</div>
					<div id="module_buttons" class="ad_modal_footer modal-footer">
						<button type="submit" class="btn btn-success">Save</button>
						<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</form>	
	</div>
<?php 	
	require_once("../admin_footer.php");
?>   