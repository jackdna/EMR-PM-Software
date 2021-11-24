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
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_make.js"></script>
<body>
    <input type="hidden" name="ord_by_field" id="ord_by_field" value="manufacturer">
	<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
	<div class="whtbox">
		<div class="table-responsive respotable adminnw">
			<table class="table table-bordered table-hover">
				<thead>
					<tr>
						<th style="width:20px; padding-left:8px;"><div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value=""><label for="chk_sel_all"></label></div></th>
						<th onClick="LoadResultSet('','','','manufacturer',this);" >Manufacturer<span></span></th>
						<th onClick="LoadResultSet('','','','style',this);" >Brand<span></span></th>
						<th onClick="LoadResultSet('','','','type',this);" >Type<span></span></th>
						<th onClick="LoadResultSet('','','','base_curve',this);" >B/C<span></span></th>
						<th onClick="LoadResultSet('','','','diameter',this);" >Diameter<span></span></th>
						<th onClick="LoadResultSet('','','','cpt4_code',this);" >CPT Code<span></span></th>
						<th onClick="LoadResultSet('','','','cpt_practice_code',this);" >Practice Code<span></span></th>
						<th onClick="LoadResultSet('','','','cpt_fee',this);" >Price<span></span></th>
					</tr>
				</thead>
				<tbody id="result_set"></tbody>
			</table>
		</div>
	</div>
	<div id="myModal" class="modal" role="dialog">
		<form class="form-horizontal" name="add_edit_frm" id="add_edit_frm" onSubmit="saveFormData(); return false;">
			<div class="modal-dialog"> 
				<div class="modal-content">
					<div class="modal-header bg-primary">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title" id="modal_title">Modal Header</h4>
					</div>
					<input type="hidden" name="make_id" id="make_id" >
					<div class="modal-body">
						<div class="form-group">
							<label class="control-label col-sm-3" for="manufacturer">Manufacturer</label>
							<div class="col-sm-9">
								<input name="manufacturer" id="manufacturer" type="text" class="form-control manufac_vender" />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-3" for="style">Brand</label>
							<div class="col-sm-9">
								<input name="style" id="style" type="text" class="form-control" />
							</div>	
						</div>
						<div class="form-group">
							<label class="control-label col-sm-3" for="type">Type</label>	
							<div class="col-sm-9">
								<input name="type" id="type" type="text" class="form-control" />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-3" for="base_curve">B/C</label>
							<div class="col-sm-9">
								<input name="base_curve" id="base_curve"  type="text" class="form-control" />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-3" for="mobile">Diameter</label>
							<div class="col-sm-9">
								<input name="diameter" id="diameter"  type="text" class="form-control" />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-3" for="cpt_practice_code">CPT Practice Code</label>
							<div class="col-sm-9">
								<input name="cpt_practice_code" id="cpt_practice_code" type="text" class="form-control cpt_prac" />
								<input name="cpt_fee_id" id="cpt_fee_id" type="hidden" />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-3" for="cpt4_code">CPT Code</label>
							<div class="col-sm-9">	
								<input name="cpt4_code" id="cpt4_code" type="text" class="form-control" />
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-3" for="price">Price</label>
							<div class="col-sm-9">	
								<input name="price" id="price" type="text" class="form-control" onKeyDown="setCurrSign(this);" onChange="priceValid(this); checkFieldData(this.name)" />
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