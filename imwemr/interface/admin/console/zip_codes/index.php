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
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_zip_codes.js"></script>
<style type="text/css">
	.num_cnt{ margin-left:3px;border:1px solid #CCC; font-size:12px; font-weight:bold; cursor:pointer; color:#666; background:#F9F8F6; font-family:Verdana, Geneva, sans-serif; padding:2px 5px 2px 5px;}
	.num_cnt:hover{color:#FFF; background:#666; }
	.num_cnt.selected{ font-size:14px; color:#FFF; cursor:text;  background:#666;}
	.paging{height:19px; margin-top:3px; text-align:center; width:100%;border-bottom:1px solid #CCC;border-top:1px solid #CCC;}
</style>
<body>
   <input type="hidden" name="ord_by_field" id="ord_by_field" value="zip_id">
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
						<th onClick="LoadResultSet('','','','zip_code',this);"><?php getZipPostalLabel(); ?><span></span></th>
						<th style="width:250px;">
							<div class="row">
								<div class="col-sm-2 pt10" onClick="LoadResultSet('','','','city',this);">
									City
								</div>
								<div class="col-sm-10">
									<div class="input-group">
										<input type="text" class="form-control" id="zip_search">
										<div class="input-group-addon">
											<font class="glyphicon glyphicon-search" aria-hidden="true"></font>
										</div>
									</div>
								</div>	
							</div>
						</th>
						<th onClick="LoadResultSet('','','','state_abb',this);">State Abb<span></span></th>
						<th onClick="LoadResultSet('','','','state',this);">State<span></span></th>
						<th onClick="LoadResultSet('','','','county',this);">County<span></span></th>
						<th onClick="LoadResultSet('','','','country',this);">Country<span></span></th>
					</tr>
				</thead>
				<tbody id="result_set"></tbody>
			</table>
		</div>
	</div>
	<div class="pgn_prnt">
		<div class="text-center">
			<ul class="pagination" style="margin:0px;">
				<li id="num_count"></li>
			</ul><br />
			<ul class="pagination" id="pagenation_alpha_order">
			</ul>
		</div>
	</div> 
	<div id="myModal" class="modal fade" role="dialog">
		<form name="add_edit_frm" id="add_edit_frm" onSubmit="saveFormData();return false;">
			<div class="modal-dialog"> 
				<div class="modal-content">
					<div class="modal-header bg-primary">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title" id="modal_title">Modal Header</h4>
					</div>
					<div class="modal-body">
						<input type="hidden" name="zip_id" id="zip_id" >
						<div class="row">
							<div class="col-sm-6">
								<label for="zip_code"><?php getZipPostalLabel(); ?></label>
								<div class="row">
								<?php 
									$zip_display = inter_zip_ext() ; 
									if($zip_display == 1){
										$class = 'class="col-sm-8"';
									} else {
										$class = 'class="col-sm-12"';
									}
								?>
								<div <?php echo $class; ?> >
									<input class="form-control" name="zip_code" id="zip_code" type="text" maxlength="<?php echo inter_zip_length();?>">
								</div>
								<?php if(inter_zip_ext()){?>
								<div class="col-sm-4">
									<input class="form-control" name="zip_ext" id="zip_ext" type="text">
								</div>
								<?php }?>
								</div>	
							</div>
							<div class="col-sm-6">
								<label for="city">City</label>
								<input class="form-control" name="city" id="city" type="text">
							</div>
						</div>
						<div class="row pt10">
							<div class="col-sm-6">
								<label for="state_abb">State Abb</label>
								<input class="form-control" name="state_abb" id="state_abb" type="text">
							</div>
							<div class="col-sm-6">
								<label for="state">State</label>
								<input class="form-control" name="state" id="state" type="text">
							</div>
						</div>
						<div class="row pt10">
							<div class="col-sm-6">
								<label for="county">County</label>
								<input class="form-control" name="county" id="county" type="text">
							</div>
							<div class="col-sm-6">
								<label for="country">Country</label>
								<input class="form-control" name="country" id="country" type="text">
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
	require_once("../../admin_footer.php");
?>      