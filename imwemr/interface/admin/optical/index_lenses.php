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
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_lenses.js"></script>
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
						<th onClick="LoadResultSet('','','','Tab_val',this);">Vision<span></span></th>
						<th onClick="LoadResultSet('','','','lens_type',this);">Lens Type<span></span></th>
						<th onClick="LoadResultSet('','','','lens_material',this);">Lens Material<span></span></th>
						<th onClick="LoadResultSet('','','','vendor_name',this);">Lab<span></span></th>
						<th onClick="LoadResultSet('','','','Cost_Price',this);">Cost Price<span></span></th>
						<th onClick="LoadResultSet('','','','Retail_Price',this);">Retail Price<span></span></th>
						<th onClick="LoadResultSet('','','','pos_facility_id',this);">Facility<span></span></th>
					</tr>
				</thead>
				<tbody id="result_set"></tbody>
			</table>
		</div>
	</div>
	<div class="pgn_prnt">
		<div class="text-center">
			<ul class="pagination" id="pagenation_alpha_order"></ul>
		</div>
	</div>
	
	<div class="modal" id="myModal" role="dialog">
		<form name="add_edit_frm" id="add_edit_frm" onSubmit="saveFormData();return false;">
			<div class="modal-dialog modal_90">
				<div class="modal-content">
				<div class="modal-header bg-primary">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Modal Header</h4>
				</div>
				<div class="modal-body">
					<input type="hidden" name="optical_lenses_id" id="optical_lenses_id" >
						<div class="row">
							<div class="col-sm-4">
								<div class="adminbox">
									<div class="section formlabel">
										<div class="head">
											<span>Vision</span>
										</div>
									</div>
									<div class="tblBg">
									<div class="row">
										<div class="col-sm-12">
											<label for="Tab_val">Vision</label>
											<select name="Tab_val" id="Tab_val" class="form-control minimal" onChange="show_lense_type(this.value);">												
												<option value="BiFocal" selected="selected">BiFocal</option>
												<option value="Deluxe Progressive">Deluxe Progressive </option>											
												<option value="Progresive">Progresive</option>
												<option value="Single Vision">Single Vision</option>
												<option value="TriFocal">TriFocal</option>
											</select>					
										</div>
									</div>
									<div class="row" id="single_vision_val" style="display:none;">
										<div class="col-sm-12">
											<label for="lens_type">Lens Type</label>
											<select name="lens_type" id="lens_type" class="form-control minimal">
												<option value="DV">DV</option>
												<option value="NV">NV</option>
											</select>
										</div>
									</div>
									<div class="row" id="BiFocal_val" style="display:none;">
										<div class="col-sm-12">
											<label for="Bifocal">BiFocal</label>
											<select name="Bifocal" id="Bifocal" class="form-control minimal" >
												<option value="Blended">Blended</option>
												<option value="FT 22">FT 22</option>
												<option value="FT 28">FT 28</option>			
												<option value="FT 35">FT 35</option>												
											</select>
										</div>
									</div>
									<div class="row" id="TriFocal_val" style="display:none;">
										<div class="col-sm-12">
											<label for="Trifocal">TriFocal</label>
											<select name="Trifocal" id="Trifocal" class="form-control minimal">
												<option value="FT 7 x 28">FT 7 x 28</option>
												<option value="FT 8 x 35">FT 8 x 35</option>
											</select>                                
										</div>
									</div>
									<div class="row" id="Progresive_val" style="display:none;">
										<div class="col-sm-12">
											<label for="progresive_text">Progresive</label>
											<select name="progresive_text" id="progresive_text" class="form-control minimal" onChange="change_progresive(this.value);">
												<option value="Creation">Creation</option>
												<option value="Varilux">Varilux</option>
												<option value="Other">Other</option>
											</select>
										</div>
									</div>
									<div class="row" id="progresive_txt" style="display:none;">
										<div class="col-sm-12">
											<label for="progresive_text1">Progresive</label>
											<input type="text" class="form-control" name="progresive_text1" id="progresive_text1"  value="" onBlur="checkProgField();" >
										</div>
									</div>
									<div class="row" id="lens_material1">
										<div class="col-sm-12">
											<label for="lens_material">Lens Material</label>
											<select name="lens_material" id="lens_material" class="form-control minimal" onChange="check_lens_material();" >
												<option value="Glass">Glass</option>
												<option value="Hi Index">Hi Index</option>
												<option value="Plastic">Plastic</option>
												<option value="Polycarbonate">Polycarbonate</option>
												<option value="Trivax">Trivax</option>
												<option value="Other">Other</option>
											</select>
										</div>
									 </div>
									 <div class="row" id="lens_material2" style="display:none;">
										<div class="col-sm-12">
											<label for="lens_material3">Lens Material</label>
											<input type="text" name="lens_material1" id="lens_material3" class="form-control" value="" onBlur="checkField();" >
										</div>
									</div>
									<div class="row">
										<div class="col-sm-12">
											<label for="vendor_name">Lab Name</label>
											<input name="vendor_name" id="vendor_name" type="text" class="form-control" />
										</div>
									</div>
									</div>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="adminbox">
									<div class="section formlabel">
										<div class="head">
											<span>Lens Info</span>
										</div>
									</div>
									<div class="tblBg">
									<div class="row">
										<div class="col-sm-6">
											<label for="cost_price">Cost Price</label>
											<input id="cost_price" name="Cost_Price" type="text"  class="form-control" onKeyDown="setCurrSign(this)" onChange="priceValid(this); checkFieldData(this.name);">
											
										</div>
										<div class="col-sm-6">
											<label for="retail_price">Retail Price</label>
											<input id="retail_price" name="Retail_Price" type="text"  class="form-control" onKeyDown="setCurrSign(this)"   onChange="priceValid(this); checkFieldData(this.name);">
										</div>
									</div>    
									<div class="row">
										 <div class="col-sm-12">
											<label for="comments">Comments</label>
											<textarea name="lens_cooment" id="comments" rows="3" class="form-control"></textarea>
										 </div>
									</div>
									<div class="row">
										<div class="col-sm-6">
											<label for="pos_facility_id">Facility</label>
											<select name="pos_facility_id" id="pos_facility_id" class="form-control minimal"></select>						
										</div>
										<div class="col-sm-6">
											<label for="bar_code_id">Bar Code ID</label>
											<input name="bar_code_id" id="bar_code_id" type="text"  class="form-control">
										</div>
									</div>
									</div>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="adminbox">
									<div class="section formlabel">
										<div class="head">
											<span>More Info</span> 
										</div>
									</div>
									<div class="tblBg">
									<div class="row">
										<div class="col-sm-12">
											<label for="patient_discount_actual">Patient Discount($-%)</label>
											<input id="patient_discount_actual" name="patient_discount_actual" type="text"  class="form-control" onBlur="discountOpt(this)" />
										</div>
									 </div>
									 <div class="row">
										 <div class="col-sm-12">
											<label for="family_discount_actual">Family Friend Disc.($-%)</label>
											<input id="family_discount_actual" name="family_discount_actual" type="text"  class="form-control" onBlur="discountOpt(this)">
										</div>
									</div>
									</div>
								</div>
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