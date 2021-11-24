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
$oExamXml = new ExamXml();
//check chart db
$osv = new SaveFile();
$osv->cr_wvexams_db();
?>
<style>
	
	.adminnw tr:hover{background-color:transparent!important;}
	#result_set tr td:nth-child(1){background-color: #fafafa; width:20%; border-right:1px solid lightgrey; }
	#result_set .opts{margin-right:10px;}
	#result_set .sub{ padding:10px;}
	.glyphicon-remove{font-size:14px;margin-left:10px;}
	.checkbox{margin:5px 0px;}
	.tr_exm_ext, #result_set tr.tr_exm_ext td{background-color:#94D094;}
	
	
	
</style>
<script>
var zPath = '<?php echo $GLOBALS['rootdir'] ;?>';
</script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_clinical.js"></script>
<body>
    <div style="position:absolute" id="scontainer"></div>
	<div class="whtbox">
		<div class="table-responsive respotable adminnw">
			<table class="table ">
				<thead>
					<tr>	
						<th >
							<div class="form-group"><p class="form-control-static">Clinical Exam Extensions</p></div>
						</th>
						<th >								
							<div class="form-group">
								<label for="el_exm_name">Select Exam:</label>
								<select id="el_exm_name" name="el_exm_name" class="selectpicker" title="Please Select" onchange="load_exam(this)" data-container="#scontainer">
									<option value=""></option>
									<?php 
										$arr_exams=$oExamXml->get_all_exam_options();
										foreach($arr_exams as $exm => $ar_ex_op){
											echo "<optgroup label=\"".$exm."\">";
											foreach($ar_ex_op as $k => $v){
												echo "<option value=\"".$exm."--".$v."\">".$v."</option>";
											}
											echo "</optgroup>";
										}//
									?>									
								</select>
							</div>
						</th>
					</tr>
				</thead>
				<tbody id="result_set"></tbody>
			</table>
		</div>
	</div>
	
	<!-- Form -->
	<div id="myModal" class="modal" role="dialog">
		<div class="modal-dialog"> 
			<div class="modal-content">
			<div class="modal-header bg-primary">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title" id="modal_title">Exam Extension</h4>
			</div>
			<form name="add_edit_frm" id="add_edit_frm" >
			<input type="hidden" name="el_edid" id="el_edid" >	
			<input type="hidden" name="el_exam" id="el_exam" >
			<input type="hidden" name="task" id="task" value="save" >
			<div class="modal-body">
				<div class="row ">
					<div class="col-sm-3">						
							<strong>Observation</strong>
					</div>
					<div class="col-sm-4">	
							<strong >Grade</strong>
					</div>
					<div class="col-sm-3">
							<strong >Location</strong>
					</div>
					<div class="col-sm-2">
						<div class="checkbox">								
							<input  name="el_comnt" id="el_comnt" type="checkbox" value="Comments"><label for="el_comnt"><strong>Comments</strong></label>					
						</div>
					</div>
				</div>
				<div class="row" id="col_opts">
					<div class="col-sm-3" >
						<div class="form-group">
							<select class="form-control" name="el_obsrv_wh" id="el_obsrv">
								<option value="Main">Main</option>
							</select>	
						</div>
						<div class="form-group">
							<input type="text" value="" id="el_obsrv_name" name="el_obsrv_name" class="form-control">
						</div>	
					</div>
					<div class="col-sm-3" >
						<div class="checkbox">
							<input type="checkbox" value="Absent" id="el_grd_abs" name="el_grd_abs"><label for="el_grd_abs">Absent</label>
						</div>
						<div class="checkbox">	
							<input type="checkbox" value="Present" id="el_grd_pre" name="el_grd_pre"><label for="el_grd_pre">Present</label>
						</div>
						<div class="checkbox">		
							<input type="checkbox" value="T" id="el_grd_t" name="el_grd_t"><label for="el_grd_t">T</label>
						</div>
						<div class="checkbox">		
							<input type="checkbox" value="1+" id="el_grd_1" name="el_grd_1"><label for="el_grd_1">1+</label>
						</div>
						<div class="checkbox">		
							<input type="checkbox" value="2+" id="el_grd_2" name="el_grd_2"><label for="el_grd_2">2+</label>
						</div>
						<div class="checkbox">		
							<input type="checkbox" value="3+" id="el_grd_3" name="el_grd_3"><label for="el_grd_3">3+</label>
						</div>
						<div class="checkbox">		
							<input type="checkbox" value="4+" id="el_grd_4" name="el_grd_4"><label for="el_grd_4">4+</label>							
						</div>
						<div class="input-group">
							<input id="el_grd_othr1" type="text" class="form-control" name="el_grd_othr1" placeholder="Other">
							<span class="input-group-addon"><i class="glyphicon glyphicon-remove"></i></span>   
						</div>	
					</div>
					<div class="col-sm-4" >
						<div class="checkbox">
							<input type="checkbox" value="Superotemporal" id="el_loc_st" name="el_loc_st"><label for="el_loc_st">Superotemporal</label>
						</div>
						<div class="checkbox">
							<input type="checkbox" value="Inferotemporal" id="el_loc_it" name="el_loc_it"><label for="el_loc_it">Inferotemporal</label>
						</div>
						<div class="checkbox">
							<input type="checkbox" value="Superonasal" id="el_loc_sn" name="el_loc_sn"><label for="el_loc_sn">Superonasal</label>
						</div>
						<div class="checkbox">
							<input type="checkbox" value="Inferonasal" id="el_loc_in" name="el_loc_in"><label for="el_loc_in">Inferonasal</label>
						</div>
						<div class="input-group">
							<input id="el_loc_othr1" type="text" class="form-control" name="el_loc_othr1" placeholder="Other">
							<span class="input-group-addon"><i class="glyphicon glyphicon-remove"></i></span>   
						</div>
					</div>
					
					<div class="col-sm-2">
						
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