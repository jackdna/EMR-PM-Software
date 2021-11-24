<?php
require_once("../../admin_header.php");
require_once($GLOBALS['fileroot'].'/library/classes/common_function.php');
?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/patient_info.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_pos_facility.js"></script>
<body>
	<input type="hidden" name="ord_by_field" id="ord_by_field" value="headquarter">
	<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="DESC">
    <div class="whtbox">
		<div class="table-responsive respotable adminnw">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th style="width:20px; padding-left:8px;"><div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value=""><label for="chk_sel_all"></label></div></th>
						<th onClick="LoadResultSet('','','','facilityPracCode',this);">Practice Code<span></span></th>
						<th onClick="LoadResultSet('','','','facility_name',this);">Facility Name<span></span></th>
						<th onClick="LoadResultSet('','','','pos_id',this);">POS<span></span></th>
						<th onClick="LoadResultSet('','','','npiNumber',this);">NPI#<span></span></th>
						<th onClick="LoadResultSet('','','','taxId',this);">Tax Id#<span></span></th>
						<th onClick="LoadResultSet('','','','pos_facility_address',this);">Facility Street<span></span></th>
						<th onClick="LoadResultSet('','','','pos_facility_city',this);">City<span></span></th>
						<th onClick="LoadResultSet('','','','pos_facility_state',this);">State<span></span></th>
						<th onClick="LoadResultSet('','','','pos_facility_zip',this);"><?php getZipPostalLabel(); ?><span></span></th>
						<th onClick="LoadResultSet('','','','phone',this);">Phone<span></span></th>
						<?php if(verify_payment_method("MPAY")){?>
						<th id="mpay" onClick="LoadResultSet('','','','mpay_locid',this);">MpayLOC</th>
						<?php } ?>
					</tr>
				</thead>
				<tbody id="result_set"></tbody>
			</table>
		</div>
	</div>
	<div id="myModal" class="modal fade" role="dialog">
		<div class="modal-dialog modal-lg"> 
			<div class="modal-content">
			<div class="modal-header bg-primary">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title" id="modal_title">Modal Header</h4>
			</div>
			<form name="add_edit_frm" id="add_edit_frm" onSubmit="saveFormData();return false;">
				<div class="modal-body">
					<input type="hidden" name="pos_facility_id" id="pos_facility_id" value="">
					<div class="row">
                        <?php $col='col-sm-4';if( isPosFacGroupEnabled() ) { $col='col-sm-3';} ?>
						<div class="<?php echo $col;?>">
							<label for="pos_id">POS Code</label>
							<select name="pos_id" id="pos_id" class="form-control minimal"></select>
						</div>
                        <?php if( isPosFacGroupEnabled() ) { ?>
                            <div class="<?php echo $col;?>">
                                <label for="posfacilitygroup_id">POS Facility Group</label>
                                <select name="posfacilitygroup_id" id="posfacilitygroup_id" class="form-control minimal"></select>
                            </div>
                        <?php } ?>
						<div class="<?php echo $col;?>">
							<label for="facilityPracCode">Practice Code</label>
							<input name="facilityPracCode" id="facilityPracCode" class="form-control" />
						</div>
						<div class="<?php echo $col;?>">
							<label for="facility_name">Facility Name</label>
							<input name="facility_name" id="facility_name" class="form-control" />
						</div>
					</div>
					<div class="row pt10">
						<div class="col-sm-4">
							<label for="npiNumber">NPI#</label>
							<input name="npiNumber" id="npiNumber" class="form-control" />
						</div>
						<div class="col-sm-4">
							<label for="taxId">Tax Id#</label>
							<input name="taxId" id="taxId" class="form-control" />
						</div>
						<div class="col-sm-4">
							<label for="pos_facility_address">Facility Street</label>
							<input name="pos_facility_address" id="pos_facility_address" class="form-control" />
						</div>
					</div>
					<div class="row pt10">
						<div class="col-sm-4">
							<label for="pos_facility_zip"><?php getZipPostalLabel(); ?></label>
							<div class="row">
								<div class="col-sm-8">
									<input name="pos_facility_zip" id="pos_facility_zip" type="text" size="<?php echo inter_zip_length();?>" maxlength="<?php echo inter_zip_length();?>" class="form-control"  onBlur="zip_vs_state(this.value,'PosFacility');">
								</div>
								<?php if(inter_zip_ext()){?>
								<div class="col-sm-4"><input name="zip_ext" id="zip_ext" type="text" class="form-control"></div>
								<?php }?>
							</div>
						</div>
						<div class="col-sm-4">
							<label for="pos_facility_city">City</label>
							<input name="pos_facility_city" id="pos_facility_city" class="form-control" />
						</div>
						<div class="col-sm-4">
							<label for="pos_facility_state"><?php echo ucwords(inter_state_label());?></label>
							<input name="pos_facility_state" id="pos_facility_state" maxlength="<?php if(inter_state_val()=="abb")echo '2';?>" class="form-control" />
						</div>
					</div>
					<div class="row pt10">
						<div class="col-sm-4">
							<div class="row">
								<div class="col-sm-8">
									<label for="phone">Phone</label>
									<input name="phone" id="phone" type="text" class="form-control" onBlur="set_phone_format(this,'<?php echo $GLOBALS['phone_format'] ?>');">
								</div>
								<div class="col-sm-4">
									<label for="phone_ext">Ext.</label>
									<input name="phone_ext" id="phone_ext" type="text" class="form-control">
								</div>
							</div>
						</div>
						 <?php if(verify_payment_method("MPAY")){?>
						<div class="col-sm-4">
							<label for="mpay_locid">MpayLOC</label>
							<input name="mpay_locid" id="mpay_locid" class="form-control" />
						</div>
						<?php } ?>
						<div class="col-sm-1" id="hq_tr">
							<label for="headquarter_Yes">HQ</label>
							<div class="checkbox">
								<input type="checkbox" name="headquarter" id="headquarter_Yes" value="1" /><label for="headquarter_Yes"></label>
							</div>
						</div>
                        <!-- THCIC submitter ID -->
                        <div class="col-sm-3">
                            <div class="form-group" id="THCICID_col">
                                <label for="thcic_id">THCIC ID</label>
                                <input type="text" class="form-control" tabindex="13" name="thcic_id" id="thcic_id" value="" />
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
require_once("../../admin_footer.php");	
?>