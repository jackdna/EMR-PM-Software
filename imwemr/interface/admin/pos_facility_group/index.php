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
require_once($GLOBALS['fileroot'].'/library/classes/common_function.php');
?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/patient_info.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_pos_facility_group.js"></script>
<body>
	<input type="hidden" name="ord_by_field" id="ord_by_field" value="pos_facility_group">
	<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
    <div class="whtbox">
		<div class="table-responsive respotable adminnw">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th style="width:20px; padding-left:8px;"><div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value=""><label for="chk_sel_all"></label></div></th>
						<th onClick="LoadResultSet('','','','pos_facility_group',this);">POS Facility Group<span></span></th>
                        <th onClick="LoadResultSet('','','','fac_group_address',this);">Street<span></span></th>
						<th onClick="LoadResultSet('','','','fac_group_city',this);">City<span></span></th>
						<th onClick="LoadResultSet('','','','fac_group_state',this);">State<span></span></th>
						<th onClick="LoadResultSet('','','','fac_group_zip',this);"><?php getZipPostalLabel(); ?><span></span></th>
						<th onClick="LoadResultSet('','','','fac_phone',this);">Phone<span></span></th>
                        <th onClick="LoadResultSet('','','','fac_fax',this);">Fax<span></span></th>
                        <th onClick="LoadResultSet('','','','fac_tax_id',this);">Tax Id#<span></span></th>
					</tr>
				</thead>
				<tbody id="result_set"></tbody>
			</table>
		</div>
	</div>
	<div id="myModal" class="modal fade" role="dialog">
		<div class="modal-dialog modal-md"> 
			<div class="modal-content">
			<div class="modal-header bg-primary">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title" id="modal_title">Modal Header</h4>
			</div>
			<form name="add_edit_frm" id="add_edit_frm" onSubmit="saveFormData();return false;">
				<div class="modal-body">
					<input type="hidden" name="pos_fac_grp_id" id="pos_fac_grp_id" value="">
					<div class="row">
						<div class="col-sm-12">
							<label for="pos_facility_group">POS Facility Group</label>
							<input name="pos_facility_group" id="pos_facility_group" class="form-control" />
						</div>
					</div>
					<div class="row pt10">
                        <div class="col-sm-6">
							<label for="fac_group_address">Street 1 </label>
							<input name="fac_group_address" id="fac_group_address" class="form-control" />
						</div>
                        <div class="col-sm-6">
							<label for="fac_group_address2">Street 2 </label>
							<input name="fac_group_address2" id="fac_group_address2" class="form-control" />
						</div>
					</div>
                    <div class="row pt10">
                        <div class="col-sm-4">
                            <label for="fac_group_zip"><?php getZipPostalLabel(); ?></label>
                            <div class="row">
                                <div class="col-sm-8">
                                    <input name="fac_group_zip" id="fac_group_zip" type="text" size="<?php echo inter_zip_length();?>" maxlength="<?php echo inter_zip_length();?>" class="form-control"  onBlur="zip_vs_state(this.value,'PosFacilityGroup');">
                                </div>
                                <?php if(inter_zip_ext()){?>
                                <div class="col-sm-4"><input name="fac_zip_ext" id="fac_zip_ext" type="text" class="form-control"></div>
                                <?php }?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <label for="fac_group_city">City</label>
                            <input name="fac_group_city" id="fac_group_city" class="form-control" />
                        </div>
                        <div class="col-sm-4">
                            <label for="fac_group_state"><?php echo ucwords(inter_state_label());?></label>
                            <input name="fac_group_state" id="fac_group_state" maxlength="<?php if(inter_state_val()=="abb")echo '2';?>" class="form-control" />
                        </div>
                        
                    </div>
                    <div class="row pt10">
                        <div class="col-sm-4">
							<div class="row">
								<div class="col-sm-8">
									<label for="fac_phone">Phone</label>
									<input name="fac_phone" id="fac_phone" type="text" class="form-control" onBlur="set_phone_format(this,'<?php echo $GLOBALS['phone_format'] ?>');">
								</div>
								<div class="col-sm-4">
									<label for="phone_ext">Ext.</label>
									<input name="phone_ext" id="phone_ext" type="text" class="form-control">
								</div>
							</div>
						</div>
                        
                        <div class="col-sm-4">
                            <label for="fac_fax">Fax</label>
                            <input name="fac_fax" id="fac_fax" type="text" class="form-control" onBlur="set_phone_format(this,'<?php echo $GLOBALS['phone_format'] ?>');">
                        </div>
                        <div class="col-sm-4">
                            <label for="fac_tax_id">Tax Id#</label>
                            <input name="fac_tax_id" id="fac_tax_id" class="form-control" />
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