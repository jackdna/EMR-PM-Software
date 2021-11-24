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
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_lab_display.js"></script>
<body>
    <input type="hidden" name="ord_by_field" id="ord_by_field" value="lab_radiology_name">
	<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
        <div class="whtbox">
			<div class="table-responsive respotable adminnw">
				<table class="table table-bordered table-hover">
					<thead>
						<tr>
                            <th style="width:20px; padding-left:8px;"><div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value=""><label for="chk_sel_all"></label></div></th>
                            <th onClick="LoadResultSet('','','','lab_radiology_name',this);" >Test Name<span></span></th>
                            <th onClick="LoadResultSet('','','','lab_indication',this);" >Indication<span></span></th>
                            <th onClick="LoadResultSet('','','','lab_loinc',this);" >LOINC<span></span></th>
                            <th onClick="LoadResultSet('','','','lab_instructions',this);">Instructions<span></span></th>
                            <th onClick="LoadResultSet('','','','lab_contact_name',this);" >Lab Name<span></span></th>
                            <th onClick="LoadResultSet('','','','lab_radiology_phone',this);">Phone #<span></span></th>
                            <th onClick="LoadResultSet('','','','lab_radiology_fax',this);" >Fax #<span></span></th>
                            <th onClick="LoadResultSet('','','','lab_radiology_address',this);" >Address<span></span></th>
                            <th onClick="LoadResultSet('','','','lab_radiology_zip',this);" ><?php getZipPostalLabel(); ?><span></span></th>
                            <th onClick="LoadResultSet('','','','lab_radiology_city',this);" >City<span></span></th>
                            <th onClick="LoadResultSet('','','','lab_radiology_state',this);">State<span></span></th>
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
				<form name="add_edit_frm" id="add_edit_frm" onSubmit="saveFormData();return false;">
				<div class="modal-body">
					<div class="form-group">
						<input type="hidden" name="lab_radiology_tbl_id" id="lab_radiology_tbl_id">
						<div class="row">
							<div class="col-sm-4">
								<label for="lab_radiology_name">Test Name</label>
								<input class="form-control" name="lab_radiology_name" id="lab_radiology_name" type="text">
							</div>
							<div class="col-sm-4">
								<label for="lab_type">Test Type</label>
								<select class="selectpicker" name="lab_type" id="lab_type" data-width="100%" data-title="Select">
									<option value="Lab">Lab</option>
									<option value="Radiology">Radiology</option>
								</select>
							</div>
							<div class="col-sm-4">
								<label for="lab_loinc">LOINC</label>
								<input class="form-control" name="lab_loinc" id="lab_loinc" type="text">
							</div>
						</div><br />
						<div class="row">
							<div class="col-sm-4">
								<label for="lab_indication">Indication</label>
								<input class="form-control" name="lab_indication" id="lab_indication" type="text">
							</div>
							<div class="col-sm-4">
								<label for="lab_instructions">Instructions</label>
								<textarea class="form-control"  name="lab_instructions" id="lab_instructions" rows="1"></textarea>
							</div>
							<div class="col-sm-4">
								<label for="lab_contact_name">Lab Name</label>
								<?php 
								if(isDssEnable()) {
									include_once( $GLOBALS['srcdir'].'/dss_api/dss_medical_hx.php' );
        							$obj = new Dss_medical_hx();
        							$postArray = array(
										"from" => "0",
										"direction" => "1",
										"xRef" => "C.LAB"
        							);
        							$results = $obj->LabGetOrderableLabs($postArray);
        							$arr = array();
									foreach ($results as $key => $n) {
									    $arr[$n['code']] = $n['name'];
									}
									$select = '<input type="hidden" name="dss_lab_id" id="dss_lab_id" value="">';
        							$select .= '<select name="lab_contact_name" id="lab_contact_name" class="form-control minimal" onchange="setDssLabId()"><option></option>';
									foreach ($arr as $key => $x) {
									    $select .= '<option value="'.$x.'" data-value="'.$key.'">'.$x.'</option>';
									}								
    								$select .= '</select>';
    								?>
    								<script>
    									function setDssLabId() {
    										var id = $('#lab_contact_name option:selected').attr('data-value');
    										top.fmain.$('#dss_lab_id').val(id);
    									}
    								</script>
    								<?php
    								echo $select;
								} else { ?>
									<input class="form-control" name="lab_contact_name" id="lab_contact_name" type="text">
								<?php } ?>
							</div>
						</div><br />
						<div class="row">
							<div class="col-sm-4">
								<label for="lab_radiology_phone">Phone</label>
								<input class="form-control" name="lab_radiology_phone" id="lab_radiology_phone" type="text" onBlur="set_phone_format(this,'<?php echo $GLOBALS['phone_format'] ?>');">
							</div>
							<div class="col-sm-4">
								<label for="lab_radiology_fax">Fax</label>
								<input class="form-control" name="lab_radiology_fax" id="lab_radiology_fax" type="text" onBlur="set_phone_format(this,'<?php echo $GLOBALS['phone_format'] ?>');">
							</div>
							<div class="col-sm-4">
								<label for="lab_radiology_address">Address</label>
								<input class="form-control" name="lab_radiology_address" id="lab_radiology_address" type="text">
							</div>
						</div><br />
						<div class="row">
							<div class="col-sm-4">
								<label for="lab_radiology_zip"><?php getZipPostalLabel(); ?></label>
								<input class="form-control" name="lab_radiology_zip" id="lab_radiology_zip" type="text" onBlur="getCityName(this.value);">
							</div>
							<div class="col-sm-4">
								<label for="ab_radiology_city">City</label>
								<input class="form-control" name="lab_radiology_city" id="lab_radiology_city" type="text">
							</div>
							<div class="col-sm-4">
								<label for="lab_radiology_state">State</label>
								<input class="form-control" name="lab_radiology_state" id="lab_radiology_state" type="text">
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