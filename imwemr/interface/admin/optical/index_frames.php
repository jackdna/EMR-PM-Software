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
<script type="text/javascript">
	var img_path = '<?php echo $GLOBALS['webroot']."/data/".constant('PRACTICE_PATH')."/"; ?>';
</script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_frames.js"></script>
<body>
    <input type="hidden" name="ord_by_field" id="ord_by_field" value="vendor_name">
	<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
    <input type="hidden" name="alphabet" id="alphabet" value="A">
	<div class="whtbox">
		<div class="table-responsive respotable adminnw">
			<table class="table table-bordered table-hover">
				<thead>
					<tr>
						<th style="width:20px; padding-left:10px;">
							<div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value="">
								<label for="chk_sel_all"></label>
							</div>
						</th>
						<th onClick="LoadResultSet('','','','vendor_name',this);">Manufacturer<span></span></th>
						<th onClick="LoadResultSet('','','','make_frame',this);">Make<span></span></th>
						<th onClick="LoadResultSet('','','','frame_style',this);">Style<span></span></th>
						<th onClick="LoadResultSet('','','','frame_color',this);">Color<span></span></th>
						<th onClick="LoadResultSet('','','','cost_price',this);">Cost Price<span></span></th>
						<th onClick="LoadResultSet('','','','retail_price',this);">Retail Price<span></span></th>
						<th onClick="LoadResultSet('','','','qty_left',this);">Qty. on hand<span></span></th>
						<th onClick="LoadResultSet('','','','qty_ordered',this);">Qty. Ord<span></span></th>
						<th onClick="LoadResultSet('','','','bar_code_id',this);">Bar Code<span></span></th>
						<th onClick="LoadResultSet('','','','facilityPracCode',this);">Facility<span></span></th>
						<th onClick="LoadResultSet('','','','vender_picture',this);">Picture<span></span></th>
					</tr>
				</thead>
				<tbody id="result_set"></tbody>
			</table>
		</div>
	</div>
	<div class="pgn_prnt">
		<div class="text-center">
			<ul id="pagenation_alpha_order" class="pagination"></ul>
		</div>
	</div>
	<div class="modal" id="myModal" role="dialog">
		<form name="add_edit_frm" id="add_edit_frm" onSubmit="saveFormData();return false;">
			<div class="modal-dialog modal_90">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header bg-primary">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Modal Header</h4>
					</div>
					<input type="hidden" name="optical_frames_id" id="optical_frames_id" >
					<div class="modal-body">
						<div class="row">
							<div class="col-sm-4" >
								<div class="adminbox">
									<div class="section formlabel">
										<div class="head">
											<span>Manufacturer</span>
										</div>
									</div>
									<div class="tblBg">
										<div class="row">
											<div class="col-sm-12">
												<label for="vendor_name">Manufacturer</label>
												<input type="text" class="form-control manufac_vender" name="vendor_name" id="vendor_name" required />					
											</div>
											
											<div class="col-sm-12">
												<label for="make_frame">Make</label>
												<input name="make_frame" id="make_frame" type="text" class="form-control" onKeyUp="setFrame('vendor_name','make_frame')"  onKeyDown="return setVal('make_frame')" />
											</div>
											
											<div class="col-sm-6">
												<label for="frame_style">Style</label>
												<input name="frame_style" id="frame_style" type="text" class="form-control" />
											</div>
											<div class="col-sm-6">
												<label for="frame_color">Enter option</label>
												<input class="form-control" name="frame_color" id="frame_color" type="text" />					
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="adminbox">
									<div class="section formlabel">
										<div class="head">
											<span>Frame Info</span>
										</div>
									</div>
									<div class="tblBg">
										<div class="row">
											<div class="col-sm-4">
												<label for="horizontal">Horizontal</label>
												<input class="form-control" type="text" name="horizontal" id="horizontal" onChange="checkFieldData(this.name)" />
											</div>
											<div class="col-sm-4">
												<label for="bridge">Bridge</label>
												<input class="form-control" name="bridge" id="bridge" type="text" onChange="checkFieldData(this.name)" />
											</div>
											<div class="col-sm-4">
												<label for="vertical">Vertical</label>
												<input class="form-control" name="vertical" id="vertical" type="text" onChange="checkFieldData(this.name)" />
											</div>
										</div>
										<div class="row">
											<div class="col-sm-4">
												<label for="diagonal">Diagonal</label>
												<input class="form-control" name="diagonal" id="diagonal" type="text">
											</div>
											<div class="col-sm-4">
												<label for="date_received">Recieve Date</label>
												<div class="input-group">
													<input type="text" class="form-control datepicker" name="date_received" id="date_received" autocomplete="off">
													<label class="input-group-addon btn" for="date_received">
														<span class="glyphicon glyphicon-calendar"></span>
													</label>
												</div>
											</div>
											<div class="col-sm-4">
												<label for="date_sold">Sold Date</label>
												<div class="input-group">
													<input type="text" class="form-control datepicker" name="date_sold" id="date_sold" autocomplete="off">
													<label class="input-group-addon btn" for="date_sold">
														<span class="glyphicon glyphicon-calendar"></span>
													</label>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-sm-4">
												<label for="comments">Comments</label>
												<textarea name="comments" id="comments" rows="1" class="form-control"></textarea>
											</div>
											<div class="col-sm-4">
												<label for="pos_facility_id">Facility</label>
												<select name="pos_facility_id" id="pos_facility_id" class="form-control minimal"></select>	
											</div>
											<div class="col-sm-4">
												<label for="bar_code_id">Bar Code Id</label>
												<input class="form-control" name="bar_code_id" id="bar_code_id" type="text">
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="adminbox">
									<div class="section formlabel">
										<div class="head">
											<span>Price & Quanity</span> 
										</div>
									</div>
									<div class="tblBg">
										<div class="row">
											<div class="col-sm-4">
												<label for="cost_price">Cost Price</label>
												<input id="cost_price" name="cost_price" type="text"  class="form-control" onKeyDown="setCurrSign(this)" onChange="priceValid(this); checkFieldData(this.name)">
											</div>
											<div class="col-sm-4">
												<label for="retail_price">Retail Price</label>
												 <input id="retail_price" name="retail_price" type="text" class="form-control" onKeyDown="setCurrSign(this)" onChange="priceValid(this); checkFieldData(this.name)">
											</div>
											<div class="col-sm-4">
												<label for="off_price">Off Price</label>
												<input id="off_price" name="off_price" type="text" class="form-control" onKeyDown="setCurrSign(this)"  onChange="priceValid(this); checkFieldData(this.name)">
											</div>
										</div>
										<div class="row">
											<div class="col-sm-4">
												<label for="qty_left">Current Qty</label>
												<input class="form-control" name="qty_left" id="qty_left" type="text" onChange="checkFieldData(this.name)" />
											</div>
											<div class="col-sm-4">
												<label for="qty_ordered">Qty Ordered</label>
												<input class="form-control" name="qty_ordered" id="qty_ordered" type="text" onChange="checkFieldData(this.name)" />
											</div>
											<div class="col-sm-4">
												<label for="patient_discount_actual">Patient Discount($-%)</label>
												<input class="form-control" name="patient_discount_actual" id="patient_discount_actual" type="text" onBlur="discountOpt(this)" />
											</div>
										</div>
										<div class="row">
											<div class="col-sm-4">
												<label for="family_discount_actual">Family Friend Disc.($-%)</label>
												<input class="form-control" name="family_discount_actual" id="family_discount_actual" type="text" onBlur="discountOpt(this)" />
											</div>
											<div class="col-sm-4">
												<label for="divFacLogo"></label>
												<div id="divLogoLink" style="display:none;">
													<input type="hidden" name="MAX_FILE_SIZE" id="MAX_FILE_SIZE" value="2000000">
													<div id="divFacLogo" class="pointer" 
													onClick="top.popup_win(top.JS_WEB_ROOT_PATH+'/interface/admin/optical/upload_win.php');"><strong class="text_purple">Upload Picture</strong></div>
												</div> 
											</div>
											<div class="col-sm-4"></div>
											<input type="hidden" name="picture_vendor" id="picture_vendor" value="">   
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