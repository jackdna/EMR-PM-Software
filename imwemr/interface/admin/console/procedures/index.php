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
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_procedures.js"></script>
<body>
    <input type="hidden" name="ord_by_field" id="ord_by_field" value="procedure_name">
	<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
    <div class="whtbox">
		<div class="table-responsive respotable adminnw">
			<table class="table table-bordered table-hover">
				<thead>
					<tr>
						<th style="width:20px; padding-left:8px;"><div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value=""><label for="chk_sel_all"></label></div></th>
						<th onClick="LoadResultSet('','','','procedure_name',this);">Procedure<span></span></th>
						<th onClick="LoadResultSet('','','','ret_gl',this);">Type<span></span></th>
						<th onClick="LoadResultSet('','','','dx_code',this);">Dx Code<span></span></th>
						<th onClick="LoadResultSet('','','','cpt_code',this);">CPT Code<span></span></th>
						<th onClick="LoadResultSet('','','','time_out_request',this);">Time Out Req<span></span></th>
						<th onClick="LoadResultSet('','','','pre_op_meds',this);">Pre-Op Meds<span></span></th>
						<th onClick="LoadResultSet('','','','intraviteral_meds',this);">Intravitreal Meds<span></span></th>
						<th onClick="LoadResultSet('','','','post_op_meds',this);">Post-Op Med<span></span></th>
						<th onClick="LoadResultSet('','','','consent_form_id',this);">Consent Form<span></span></th>
						<th onClick="LoadResultSet('','','','op_report_id',this);">Op Report<span></span></th>
					</tr>
				</thead>
				<tbody id="result_set"></tbody>
			</table>
		</div>
	</div>
<form name="add_edit_frm" id="add_edit_frm" onSubmit="saveFormData();return false;">
	<input type="hidden" name="procedure_id" id="procedure_id" >
	<input type="hidden" id="last_cnt" name="last_cnt" value="">	
	<div id="myModal" class="modal fade" role="dialog">
		<div class="modal-dialog" style="width:80%;">  
			<div class="modal-content">
				<div class="modal-header bg-primary">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title" id="modal_title">Modal Header</h4>
				</div>
				
				<div class="modal-body">
					<div class="row">
						<div class="col-sm-4">
							<label for="procedure_name">Procedure</label>
							<input name="procedure_name" id="procedure_name" required type="text"  class="form-control">
						</div>
						<div class="col-sm-4">
							<label for="">Procedure Type</label><br />
							<div class="radio radio-inline">
									<input type="radio" id="ret_gl_1" value="1" name="ret_gl">
									<label for="ret_gl_1"> Ret </label>
								</div>
								<div class="radio radio-inline">
									<input type="radio" id="ret_gl_2" value="2" name="ret_gl">
									<label for="ret_gl_2"> GL </label>
								</div>
								<div class="radio radio-inline">
									<input type="radio" id="ret_gl_3" value="3" name="ret_gl" checked>
									<label for="ret_gl_3"> Other </label>
								</div>
						</div>
						<div class="col-sm-4">
							<label for="dx_code">Dx Code</label>
							<input name="dx_code" id="dx_code" type="text" class="form-control" data-provide="multiple" data-seperator="semicolon">
							<input name="dx_code_id" id="dx_code_id" type="hidden" >
						</div>
					</div>
					<div class="row pt10"> 
						<div class="col-sm-4">
							<label for="consent_form_id">Consent Form</label>
							<select name="consent_form_id[]" id="consent_form_id" class="selectpicker" data-width="100%" multiple ></select>
							<br><span style="display:inline-block" id="consent_form_id_span">
						</div>
						<div class="col-sm-4">
							<label for="">Time Out Request</label><br />
							<div class="radio radio-inline">
								<input type="radio" id="time_out_request_yes" value="yes" name="time_out_request">
								<label for="time_out_request_yes"> Yes </label>
							</div>
							<div class="radio radio-inline">
								<input type="radio" id="time_out_request_no" value="no" name="time_out_request">
								<label for="time_out_request_no"> No </label>
							</div>
						</div>
						<div class="col-sm-4">
							<label for="op_report_id">Operative Note</label>
							<select name="op_report_id[]" id="op_report_id" class="selectpicker" data-width="100%" multiple ></select>
						</div>
					</div>
					<div class="row pt10">
						<div class="col-sm-4">
							<label for="consent_form_id_sel">Default Consent Form</label>
							<select name="consent_form_id_sel" id="consent_form_id_sel" class="form-control minimal" ></select>
						</div>
						<div class="col-sm-4">
							<div class="checkbox">
								 <input type="checkbox" id="laser_procedure_note_1" name="laser_procedure_note" value="1">&nbsp;<label id="laser_procedure_note_label" for="laser_procedure_note_1"><a style="cursor:pointer; font-weight:bold;">Laser Procedure Note</a></label>
							</div>
						</div>
						<div class="col-sm-4">
							<label for="op_report_id_sel">Default Operative Note</label>
							<select name="op_report_id_sel" id="op_report_id_sel" class="form-control minimal" ></select>
						</div>
					</div>
					<div class="row pt10">
						<div class="col-sm-4">
							<label for="pre_op_meds">Pre-Op Meds</label>
							<textarea name="pre_op_meds" id="pre_op_meds" class="med_atypehead form-control" data-provide="multiple" data-seperator="semicolon"></textarea>
						</div>
						<div class="col-sm-4">
							<label for="intraviteral_meds">Intravitreal Meds</label>
							<textarea name="intraviteral_meds" id="intraviteral_meds" class="med_atypehead form-control" data-provide="multiple" data-seperator="semicolon"></textarea>
						</div>
						<div class="col-sm-4">
							<label for="post_op_meds">Post-Op Meds</label>
							<textarea name="post_op_meds" id="post_op_meds" class="med_atypehead form-control" data-provide="multiple" data-seperator="semicolon"></textarea>
						</div>
					</div>
					<div class="row pt10">
						<div class="col-sm-12">
							<div class="table-responsive respotable adminnw">
								<table class="table table-bordered table-hover" id="auth_main_tbl" style="overflow-x:hidden;">
									<thead>
										<tr>
											<th style="width:15%;">CPT Code</th>
											<th style="width:15%;">Modifier</th>
											<th style="width:15%;">CPT Code</th>
											<th style="width:15%;">Modifier</th>
											<th style="width:15%;">CPT Code</th>
											<th style="width:15%;">Modifier</th>
											<th style="width:10%;"></th>
										</tr>
									</thead>
									<tbody></tbody>
								</table>
							</div>
						</div>
					</div>
					<div id="second_form_col" class="hide row pt10"></div>	
				</div>
				<div id="module_buttons" class="ad_modal_footer modal-footer">
					<button type="submit" class="btn btn-success">Save</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
</form>
<form name="laser_add_edit" id="laser_add_edit" method="post">
	<div id="laser_div" class="modal fade" role="dialog">
		<div class="modal-dialog">  
		<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header bg-primary">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Procedure Notes</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-sm-6">
							<label for="spot_duration">Spot Duration</label>
							<textarea id="spot_duration" name="spot_duration" class="form-control"></textarea>
						</div>
						<div class="col-sm-6">
							<label for="spot_size">Spot Size</label>
							<textarea id="spot_size" name="spot_size" class="form-control"></textarea>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<label for="power">Power</label>
							<textarea id="power" name="power" class="form-control"></textarea>
						</div>
						<div class="col-sm-6">
							<label for="shots"># of Shots</label>
							<textarea id="shots" name="shots" class="form-control"></textarea>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<label for="total_energy">Total Energy</label>
							<textarea id="total_energy" name="total_energy" class="form-control"></textarea>
						</div>
						<div class="col-sm-6">
							<label for="degree_of_opening">Degree of opening</label>
							<textarea id="degree_of_opening" name="degree_of_opening" class="form-control"></textarea>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<label for="exposure">Exposure</label>
							<textarea id="exposure" name="exposure" class="form-control"></textarea>
						</div>
						<div class="col-sm-6">
							<label for="count">Count</label>
							<textarea id="count" name="count" class="form-control"></textarea>
						</div>
					</div>
				</div>
				<div id="module_buttons" class="ad_modal_footer modal-footer">
					<button type="button" class="btn btn-success" onClick="get_input_fields('laser_div','myModal','second_form_col')">Save</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
</form>		
<?php
	require_once("../../admin_footer.php");
?>      