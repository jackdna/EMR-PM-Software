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
function getAllProvHtml(){
	$str = "";
	$provQry = "select lname,fname,mname,id from users where user_type>0 and delete_status='0' AND locked = '0'
				order by fname, mname, lname";
	$res = imw_query($provQry);			
	for($i=1;$row=imw_fetch_array($res);$i++){
		$tmp = "";
		if(!empty($row["fname"])){ $tmp .= $row["fname"]." "; }
		if(!empty($row["mname"])){ $tmp .= $row["mname"]." "; }
		if(!empty($row["lname"])){ $tmp .= $row["lname"]." "; }
		$tmp = trim($tmp);
		$id = $row["id"];
		if(!empty($tmp) && !empty($id)){
			$str .= "<input type=\"checkbox\" name=\"el_prov_phy".$i."\" id=\"el_prov_phy".$i."\" class=\"checkbox_check\" value=\"".$id."\"><label for=\"el_prov_phy".$i."\">$tmp</label><br/>";			
		}		
	}
	return $str;
}
?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_prov_group.js"></script>
<body>
    <input type="hidden" name="ord_by_field" id="ord_by_field" value="group_name">
	<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
	<div class="whtbox">
		<div class="table-responsive respotable adminnw">
			<table class="table table-bordered table-hover">
				<thead>
					<tr>
						<th style="width:20px; padding-left:8px;"><div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value=""><label for="chk_sel_all"></label></div></th>
						<th onClick="LoadResultSet('','','','group_name',this);">Provider Group<span></span></th>
						<th>Providers<span></span></th>
				  </tr>
				</thead>
				<tbody id="result_set"></tbody>
			</table>
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
						<input type="hidden" name="id" id="id">
						<input type="hidden" name="phy" id="phy">
						<div class="form-group">
							<label for="group_name">Provider Group</label>
							<input class="form-control" name="group_name" id="group_name" type="text" required>
						</div>
						<div class="form-group" style="height:200px;overflow:auto;">
							<label for="Providers">Providers</label>
							<div class="checkbox" id="Providers">
								<?php echo getAllProvHtml(); ?>
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