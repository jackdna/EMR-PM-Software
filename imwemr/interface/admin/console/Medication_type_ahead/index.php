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
//function copied from optical function file
function get_upc_name_id($type=0)
{
	if($type>0)
	{
		$whr = " and module_type_id='$type'";
	}
	$sel_item_rec = imw_query("select id,upc_code,name from in_item where del_status='0' $whr");
	if(imw_num_rows($sel_item_rec)>0){
		while($sel_item_row = imw_fetch_array($sel_item_rec)){
			$upc_code_name =$sel_item_row["upc_code"]."-:".$sel_item_row["name"]; 
			$stringAllUpc[$sel_item_row['id']]=str_replace("'","",$upc_code_name);		
		}
	}
	return $stringAllUpc;	
}
if(constant("connect_optical")==1){
	$stringAllUpc = get_upc_name_id('6');//getting upc for medician
}
$AllUpcArray=array();
$AllUpcIdArray=array();
$stringAllmeds="";
foreach($stringAllUpc as $key=>$value){
	$exp = explode('-:',$value);
	$AllNameArray[] = $exp[1].":-".$exp[0];
	$AllUpcIdArrays[] = $key."~".$exp[1].":-".$exp[0];
	$stringAllmeds.="'".str_replace("'","",$exp[1].":-".$exp[0])."',";	
}

$AllNameArray = implode(',',$AllNameArray);
$stringAllmeds = substr($stringAllmeds,0,-1);

$js_arr = array();
if(count($AllUpcIdArrays) > 0){
	$js_arr['upc_arr'] = $AllUpcIdArrays;
}
?>
<style type="text/css">
	.conf{ background:url(../../../../library/images/confirm.gif) center no-repeat;} 
</style>
<script type="text/javascript">
	var temp_med_js_arr=[];
	var temp_js_arr = <?php echo json_encode($js_arr); ?>;
	<?php if($stringAllmeds!=""){?>
		temp_med_js_arr= new Array(<?php echo remLineBrk($stringAllmeds); ?>);
	<?php } ?>
</script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_medication_type_ahead.js"></script>
<body>
    <div id="div_disable" style="display:none;">
	</div>
 	<div id="div_umls" style="position:absolute;top:150px;left:500px;width:400px;max-height:550px; background-color:white; margin:0px; display:none; z-index:9999;" class="section">
        <div id="divHeader" class="section_header"><span class="closeBtn" onclick="close_uml_div();"></span>UMLS and FDB Results</div>
        <div class="text12b">UMLS Results</div>
        <div id="umls_content" style="margin:0px;overflow:auto;max-height:150px;margin-bottom:10px"></div>
        <div class="text12b">FDB Results</div>
        <div id="fdb_content" style="margin:0px;overflow:auto;max-height:150px;margin-bottom:10px"></div>
        <div style="margin:0px;max-height:100px;margin-bottom:10px; text-align:center;">
            <input name="sbmtFrm" id="sbmtFrm" type="button" class="dff_button" onMouseOver="button_over('sbmtFrm')" onMouseOut="button_over('sbmtFrm','')" style="width:80px;" value="Close" onClick="close_uml_div();">
        </div>
    </div>
    <input type="hidden" name="ord_by_field" id="ord_by_field" value="medicine_name">
		<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
		<input type="hidden" name="pg_aplhabet" id="pg_aplhabet" value="A">
		<input type="hidden" name="page" id="page" value="1">
	<div class="whtbox">
		<div class="table-responsive respotable adminnw">
			<table class="table table-bordered table-hover">
				<thead>
					<tr>
						<th style="width:20px; padding-left:8px;"><div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value=""><label for="chk_sel_all"></label></div></th>
						<th onClick="LoadResultSet('','','','medicine_name',this);">Medicine<span></span></th>
						<th onClick="LoadResultSet('','','','ocular',this);">Ocular<span></span></th>
						<th onClick="LoadResultSet('','','','glucoma',this);">Glaucoma<span></span></th>
						<th onClick="LoadResultSet('','','','ret_injection',this);">Ret Inj<span></span></th>
						<th onClick="LoadResultSet('','','','alias',this);">Alias<span></span></th>
						<th onClick="LoadResultSet('','','','description',this);">Description<span></span></th>
						<th onClick="LoadResultSet('','','','prescription',this);">Rx Req.<span></span></th>
						<th onClick="LoadResultSet('','','','alert',this);">Alert<span></span></th>
						<th onClick="LoadResultSet('','','','ccda_code',this);">RxNorm Code<span></span></th>
						<th onClick="LoadResultSet('','','','fdb_id',this);">FDB Id<span></span></th>
					</tr>
				</thead>
				<tbody id="result_set"></tbody>
			</table>
		</div>
	</div>
	
	<div class="pgn_prnt">
		<div class="row ">
			<div class="col-sm-9 pagingcs text-center">
				<ul class="pagination" id="div_pages"></ul>
			</div>
			<div class="col-sm-3 form-inline recodpag" >Records per page 
				<select class="form-control minimal" name="record_limit" id="record_limit" onChange="LoadResultSet()">
					<option value="20">20</option>
					<option value="50">50</option>
					<option value="100">100</option>
					<option value="200">200</option>
				</select>
			</div>
			<div class="clearfix"></div>
			<div class="col-sm-9 text-center">
				<ul class="pagination" id="pagenation_alpha_order"></ul>
			</div>
			<div class="col-sm-3 form-inline activuser">
				<div class="input-group">
					<input type="text" class="form-control"  name="search" id="search" >
					<div class="input-group-addon pointer" onClick="srh_records();"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></div>
				</div>
				<!--<select name="srchStatus" id="srchStatus"  class="form-control minimal" onChange="javascript:LoadResultSet(this.value);" >
					<option value="0">Active</option>
					<option value="1">Inactive</option>
					<option value="all">All</option>
				</select>-->
			</div>
		</div>
	</div>
	
	<div id="myModal" class="modal fade" role="dialog">
		<form name="add_edit_frm" id="add_edit_frm" onSubmit="saveFormData(); return false;">
			<div class="modal-dialog modal-lg"> 
				<div class="modal-content">
					<div class="modal-header bg-primary">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title" id="modal_title">Modal Header</h4>
					</div>
					<input type="hidden" name="id" id="id" >
					<div class="modal-body">
						<div class="row">
							<div class="col-sm-4">
								<label for="medicine_name">Name</label>
								<input name="medicine_name" id="medicine_name" type="text" class="form-control" />
							</div>	
							<div class="col-sm-4">
								<label for="alias">Alias</label>
								<input name="alias" id="alias" type="text" class="form-control" />
							</div>	
							<div class="col-sm-4">
								<label for="dosage">Dosage</label>
								<input name="dosage" id="dosage"  type="text" class="form-control" />
							</div>	
						</div>
						<div class="row pt10">
							<div class="col-sm-4">
								<label for="qty">Quantity</label>
								<input name="qty" id="qty" type="text" class="form-control" />
							</div>	
							<div class="col-sm-4">
								<label for="sig">Sig</label>
								<input name="sig" id="sig" type="text" class="form-control" />
							</div>
							<div class="col-sm-4">
								<label for="recall_code">Recall Code</label>
								<input name="recall_code" id="recall_code" type="text" class="form-control" />
							</div>	
						</div>
						<div class="row pt10">
							<div class="col-sm-4">
								<label for="dosage">Procedure</label>
								<input name="med_procedure" id="med_procedure"  type="text" class="form-control" />
							</div>	
							<div class="col-sm-4">
								<label for="description">Description</label>
								<textarea name="description" rows="1" id="description" type="text" class="form-control"></textarea>
							</div>	
							<div class="col-sm-4">
								<label for="prescription_1">Rx Req</label><br />
								<div class="checkbox checkbox-inline">
									<input type="checkbox" name="prescription" id="prescription_1" value="1"><label for="prescription_1"></label>
								</div>	
							</div>	
						</div>
						<div class="row pt10">
							<div class="col-sm-4">
								<label for="alertmsg">Alert Message</label>
								<div class="row">
									<div class="col-sm-1">
										<div class="checkbox checkbox-inline">
											<input type="checkbox" name="alert" id="alert_1" value="1"><label for="alert_1"></label>
										</div>
									</div>
									<div class="col-sm-11">
										<input type="text" name="alertmsg" id="alertmsg" class="form-control" />
									</div>	
								</div>	
							</div>
							<div class="col-sm-4">
								<label for="ccda_code">RxNorm Code</label>
								<input name="ccda_code" id="ccda_code" type="text" class="form-control" />
							</div>	
							<div class="col-sm-4">
								<label for="fdb_id">FDB Id</label>
								<input name="fdb_id" id="fdb_id" type="text" class="form-control" />
							</div>
						</div>
						<div class="row pt10">
							<div class="col-sm-12">
								<label>Type</label>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-4">
								<div class="checkbox checkbox-inline">
									<input type="checkbox" name="ocular" id="ocular_1" value="1" onClick="toggle_order_div();"><label for="ocular_1">Ocular</label>
								</div>	
							</div>	
							<div class="col-sm-4">
								<div class="checkbox checkbox-inline">
									<input type="checkbox" name="glucoma" id="glucoma_1" value="1"><label for="glucoma_1">Glaucoma</label>
								</div>	
							</div>	
							<div class="col-sm-4">
								<div class="checkbox checkbox-inline">
									<input type="checkbox" name="ret_injection" id="ret_injection_1" value="1"><label for="ret_injection_1">Retinal Injection</label>
								</div>	
							</div>	
						</div>
						<div class="row pt10">
							<div class="col-sm-4" id="divMedOrder">
								<label for="refill">Refill</label>
								<input name="refill" id="refill"  type="text" class="form-control" />
							</div>
							<div class="col-sm-4" id="divMedOrder1">
								<label for="ndccode">NDC Code</label>
								<input name="ndccode" id="ndccode" type="text" class="form-control" />
							</div>
							<div class="col-sm-4">
							 <?php  if(constant("connect_optical")==1){?>
								<label for="opt_med_name">Tracked/Inventory</label>
								<div class="row">
									<div class="col-sm-1">
										<div class="checkbox checkbox-inline">
											<input type="checkbox" name="tracked_inventory" id="tracked_inventory_1" value="1" onChange="checkUPC(this);"><label for="tracked_inventory_1"></label>
										</div>
									</div>
									<div class="col-sm-11">
										<input type="text" name="opt_med_name" id="opt_med_name" value="" onChange="getTrioVal();"  class="form-control" />
									</div>	
								</div>
								<input type="hidden" name="opt_med_id" id="opt_med_id" value="">
								<input type="hidden" name="opt_med_upc" id="opt_med_upc" value="">
							 <?php } ?>	
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
	<div id="import_div" class="modal fade" role="dialog">
		<form name="import_frm" id="import_frm" action = "export_import.php" enctype="multipart/form-data" method="post" onSubmit="export_import_csv('import');return false">
			<div class="modal-dialog"> 
				<div class="modal-content">
					<div class="modal-header bg-primary">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Import CSV</h4>
					</div>
					<div class="modal-body">
						<input name="mode" id="mode" type="hidden" value="import">
						<div class="form-group">
							<label for="csv_file">CSV File</label>
							<input name="csv_file" id="csv_file" type="file" class="form-control">
						</div>
						<br />
						<div class="form-group">
							<progress style="width:100%"></progress>
						</div>
					</div>
					<div id="module_buttons" class="ad_modal_footer modal-footer">
						<button type="submit" class="btn btn-success">Import</button>
						<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</form>	
	</div>
<?php
	require_once("../../admin_footer.php");
?>     