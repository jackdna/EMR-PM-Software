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
require($GLOBALS['incdir']."/chart_notes/chart_globals.php");
require($GLOBALS['srcdir']."/classes/work_view/wv_functions.php");
$ohpi = new HPI();

?>
<style>
	
	.adminnw tr:hover{background-color:transparent!important;}	
	.glyphicon-remove{font-size:14px;margin-left:10px;}
	.checkbox{margin:5px 0px;}
	.tr_exm_ext, #result_set tr.tr_exm_ext td{background-color:#94D094;}
	.adminnw th:first-child, .adminnw td:first-child{width:5%;}
	.adminnw th:nth-child(3), .adminnw th:nth-child(2){width:25%;}
	
	
	
</style>
<script>
var zPath = '<?php echo $GLOBALS['rootdir'] ;?>';
</script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_hpi_ext.js"></script>
<body>
    
	<div class="whtbox">
		<div class="table-responsive respotable adminnw">
			<table class="table ">
				<thead>
					<tr>	
						<th >
							Sr.							
						</th>
						<th>
							Category
						</th>
						<th>
							Sub-Category
						</th>
						<th>
							HPI
						</th>	
					</tr>
				</thead>
				<tbody id="result_set">					
				</tbody>
			</table>
		</div>
	</div>
	
	<!-- Form -->
	<div id="myModal" class="modal" role="dialog">
		<div class="modal-dialog"> 
			<div class="modal-content">
			<div class="modal-header bg-primary">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title" id="modal_title">Custom HPI</h4>
			</div>
			<form name="add_edit_frm" id="add_edit_frm" >			
			<input type="hidden" name="task" id="task" value="save" >			
			<div class="modal-body">				
				<div class="row" id="col_opts">
					<div class="col-sm-6" >
						<div class="form-group">
						<label for="el_cat_name">Category:</label>
						<select id="el_cat_name" name="el_cat_name" onchange="load_sub(this)" class="form-control">
							<option value=""></option>
							<?php 
								$arr_exams=$ohpi->get_hpi_categories();
								foreach($arr_exams as $cid => $arexm){									
									echo "<option value=\"".$arexm["id"]."\">".$arexm["hpi"]."</option>";
								}//
							?>
						</select>
						</div>
					</div>
					<div class="col-sm-6" >
						<div class="form-group">
						<label for="el_subcat_name">Sub-Category:</label>
						<select id="el_subcat_name" name="el_subcat_name" class="form-control">
							<option value=""></option>
						</select>
						</div>
					</div>					
				</div>
				<div class="row">
					<div class="col-sm-6" >
						<div class="input-group">
						    <input type="text" class="form-control" name="el_hpi1" id="el_hpi1" placeholder="HPI">
						    <input type="hidden" name="el_edid1" id="el_edid1" value="" >	
						    <span class="input-group-addon"><i class="glyphicon glyphicon-remove"></i></span> 						    
						</div>
					</div>
					<div class="col-sm-6" >
						<div class="input-group">
						    <input type="text" class="form-control" name="el_hpi2" id="el_hpi2" placeholder="HPI">
						    <input type="hidden" name="el_edid2" id="el_edid2" value="" >
						    <span class="input-group-addon"><i class="glyphicon glyphicon-remove"></i></span> 						    
						</div>
					</div>
				</div>	
			</div>
			<div id="module_buttons" class="ad_modal_footer modal-footer">
				<button type="button" class="btn btn-success" onclick="saveFormData()">Save</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
			</div>
			</form>
			</div>
		</div>
	</div>
	<!-- Form -->
	
<?php	
	require_once("../admin_footer.php");
?>      