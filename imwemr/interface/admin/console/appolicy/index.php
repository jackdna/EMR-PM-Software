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
require($GLOBALS['incdir']."/chart_notes/chart_globals.php");
require($GLOBALS['srcdir']."/classes/work_view/wv_functions.php");
require_once($GLOBALS['fileroot'] . '/library/classes/work_view/exam_sev_loc_options.php');
require_once($GLOBALS['fileroot'] . '/library/classes/work_view/exam_options.php');
$oAdmn = new Admn;
$oOrders = new Orders;
$dx = new Dx();
$fu = new Fu();
$lenFu = 1;

//$final_data_arr = array();
//$final_data_arr = $msgConsoleObj->get_ap_policies();

//exm findings
$oExmXml = new ExamXml();
list($ar_ee_find_full, $ar_ee_finding) = $oExmXml->get_exm_ext_findings('','All');
if(count($ar_ee_find_full) > 0){ $arrMain = array_merge($arrMain, $ar_ee_find_full); }

//Get Speciality
$str_spec_opts = $oAdmn->getSpecialityOpts();


list($str_order_type_htm, $str_order_type_js)=$oOrders->get_order_types_htm();


// Get Dx Codes Str
list($strTHDesc, $strTHPracCode, $strTHDesc2) = $dx->getDxTHs();
// Get DX Codes
list($arrDxCodes, $arrDxCodesAndDesc, $strDxCodesAndDesc) = $dx->getDXCodes4Menu();

// Get ICD-10 Codes
//$z_flg_uniq_desc=1;
list($arrDxCodes10, $arrDxCodesAndDesc10, $strDxCodesAndDesc10, $strTHDesc10, $strTHPracCode10, $strTHDesc2_10) = $dx->getDXCodes4Menu10();
//Get CPT Codes
list($arrCptCats4Menu, $arrCptCodeAndDesc, $arrActiveCptCodes, $strCptCodeAndDesc, $strCptCodeDescActive) = $dx->getCptCodes4Menu_console();

//FU
$arrFuNum_menu = $fu->get_fu_menu("n");
$arrFuVist_menu = $fu->get_fu_menu("v");
?>

<style>
.ui-autocomplete{z-index:99999;}
#result_set td{vertical-align:top!important;}
.div_shadow{display:none; width:750px; top:60px;left:10px;z-index:1002; position:absolute;border:#CCC 1px solid;background:#FFF;}
#dvordermedlist{border:1px solid #ccc;width:350px; height:200px;overflow:auto;}
select.border{width:350px; height:200px!important;}
.fubox{min-height:100px; vertical-align:top;}
th, td{vertical-align:top;}
#dvordermedlist .checkbox, .relorders .checkbox {margin:0px!important;}
.pt10{padding-top: 0px;}
.relorders{border:1px solid black; width:200px; height:100px; display:none; background-color:white;position:absolute;overflow:auto;overflow-x:hidden; text-align:left; padding:2px; }
.relorders label{border:0px solid yellow;margin-left:2px;}
.modal-dialog {margin-top:10px;}
</style>
<script>
var zPath = '<?php echo $GLOBALS['rootdir'] ;?>';
var imgPath = "<?php echo $GLOBALS['webroot']; ?>";
//Type Ahead
var arrTHDesc = new Array(<?php echo $strTHDesc; ?>);
var arrTHPracCode = new Array(<?php echo $strTHPracCode; ?>);
var arrTHDesc2 = new Array(<?php echo $strTHDesc2; ?>);
var arrTHSym = new Array(<?php echo "'" . implode("','", $arrMain) . "'"; ?>);
var tmpTHDx = arrTHPracCode.concat(arrTHDesc2);

var arrTHDesc10 = new Array(<?php echo $strTHDesc10; ?>);
var arrTHPracCode10 = new Array(<?php echo $strTHPracCode10; ?>);
var arrTHDesc2_10 = new Array(<?php echo $strTHDesc2_10; ?>);
var tmpTHDx10 = arrTHPracCode10.concat(arrTHDesc2_10);
<?php echo $str_order_type_js; ?>

arrFuNum_menu = <?php echo json_encode($arrFuNum_menu); ?>;
arrFuVist_menu = <?php echo json_encode($arrFuVist_menu); ?>;
var menu1 = $('<menu1>').text("<?php echo addslashes(get_simple_menu($arrFuNum_menu,"menufunum", 'elem_followUpNumber_#dynID#')); ?>");
var menu2 = $('<menu2>').text("<?php echo addslashes(get_simple_menu($arrFuVist_menu,"menufuvisit", 'elem_followUpVistType_#dynID#')); ?>");

</script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/ap_policies.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/work_view/js_gen.js"></script>
<body>	
	<div class="whtbox">
		<div class="table-responsive respotable adminnw">
			<input type="hidden" name="ord_by_field" id="ord_by_field" value="assessment_dx">
			<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
			<table class="table table-bordered table-hover">
				<thead>
					<tr>
						<th><div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value=""><label for="chk_sel_all"></label></div></th>
						<th onClick="load_main('','','','task',this);">Findings</th>
						<th onClick="load_main('','','','assessment_dx',this);">Assessment</th>
						<th onClick="load_main('','','','tb.plan',this);">Plan</th>
						<th onClick="load_main('','','','order_set_name',this);">Order Set</th>
						<th onClick="load_main('','','','order_name',this);">Orders</th>
						<th onClick="load_main('','','','assessment_dx9',this);">ICD-9</th>
						<th onClick="load_main('','','','assessment_dx10',this);">ICD-10</th>
						<th onClick="load_main('','','','strCptCd',this);">CPT Code</th>
					</tr>
				</thead>
				<tbody id="result_set"></tbody>
			</table>
		</div>
	</div>	
	<!-- myModal -->
	<div id="myModal" class="modal fade" role="dialog">
		<form name="add_edit_frm" id="add_edit_frm"  onSubmit="saveFormData();return false;">
			<div class="modal-dialog modal-lg"> 
				<div class="modal-content">
					<div class="modal-header bg-primary">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title" id="modal_title">Add AP Policy</h4>
					</div>
					<div class="modal-body">
						<!-- test -->
						<form name="add_edit_frm" id="add_edit_frm"  onSubmit="saveFormData();return false;">
						<input type="hidden" name="to_do_id" id="to_do_id" >
						<table class="  ">
							<tr class="form-inline">
								<th>Findings</th>
								<td colspan="2"><input type="text" id="task" name="task" value="" class="form-control" onfocus="checkSevLocOpts(this, 1)" onblur="checkSevLocOpts(this)" autocomplete="off"></td>
								<td colspan="2">									
									<div class="form-group">										
										<label for="severity">Severity</label>
										<select id="severity" name="severity[]" multiple="multiple" class="selectpicker minimal selecicon" size="1">
											<option value="">Please Select</option>
										</select>
									</div>									
								</td>
								<td colspan="2" align="center">
									<div class="form-group">
										<label for="elem_speciality">Specialty</label>
										<select name="elem_speciality" id="elem_speciality" class="form-control">
											<option value=""></option>
											<?php echo $str_spec_opts; ?>
										</select>
									</div>
								</td>
							</tr>
							
							<tr>
								<th>Assessment</th>
								<td colspan="6"><input type="text" name="elem_assessment" id="elem_assessment"  value="" class="form-control" onChange="checkCode(this);"/></td>
							</tr>
							
							<tr>
								<th>Site Type</th>
								<td colspan="2"><div class="radio"><input name="site_type" id="site_type_0" type="radio" value="0" checked><label for="site_type_0">Normal Site</label></div></td>
								<td colspan="4"><div class="radio"><input name="site_type" id="site_type_1" type="radio" value="1"><label for="site_type_1">Site with lids</label></div></td>
							</tr>
							
							<tr>	
								<th></th>
								<td colspan="2">
									<div class="input-group">
										<span class="input-group-addon">ICD-9</span>
										<input type="text" name="elem_assocDx" id="elem_assocDx"  value="" class="form-control" onChange="setConsoleDxCode(this, 'getDxCode');" autocomplete="off">
										<input type="hidden" name="elem_assocDxStr" id="elem_assocDxStr" value="" onChange="addConsoleDxCode(this, 'elem_assocDx');">
										<?php
										echo get_simple_menu($arrDxCodes, "menu_DxCodes", "elem_assocDxStr");
										?>
									</div>
								</td>
								<td colspan="2">
									<div class="input-group">
										<span class="input-group-addon">ICD-10</span>
										<input type="text" name="elem_assocDx10" id="elem_assocDx10"  value="" class="form-control" onChange="setConsoleDxCode(this, 'getDxCode10');"  autocomplete="off">
										<input type="hidden" name="elem_assocDxStr10" id="elem_assocDxStr10" value="" onChange="addConsoleDxCode(this, 'elem_assocDx10');">
										<?php
										echo get_simple_menu($arrDxCodes10, "menu_DxCodes10", "elem_assocDxStr10");
										?>
									</div>
								</td>
								<td colspan="2">
									<div class="input-group">
										<span class="input-group-addon">CPT Code</span>
										<input type="text" name="elem_assocCpt" id="elem_assocCpt"  value="" class="form-control" onChange="setConsoleCptCode(this);"  autocomplete="off">
										<input type="hidden" name="elem_assocCptStr" id="elem_assocCptStr" value="" onChange="addConsoleCptCode(this, 'elem_assocCpt');">
										<?php
										echo get_simple_menu($arrCptCats4Menu, "menu_CptCats4Menu", "elem_assocCptStr");
										?>
									</div>
								</td>
							</tr>
							
							<tr>
								<th>Orders</th>								
								<?php echo $str_order_type_htm;?>								
							</tr>
							
							<tr>
								<th>Plan</th>
								<td colspan="6"><textarea name="plan" id="plan" class="form-control" rows="8" onkeyup="setTaPlanHgt()"></textarea></td>
							</tr>
							
							<tr>
								<th>F/U</th>
								<td colspan="6">
									<div id="listFU" class="fubox " data-cntrfu="<?php echo $lenFu+1; ?>" data-fuembedin="work_view">
									<?php
										$arrOpFu = array("Days", "Weeks", "Months", "Year");
										
										//$arrFuVals[$i]["time_options"];
										
										for($j=0,$i=0;$i<$lenFu;$i++){
											$j = $i+1;
											
											$elem_followUpNumber = $elem_followUp = $elem_followUpVistType = $elem_fuProName = "";
											$elem_followUpNumber = "" . $arrFuVals[$i]["number"];
											$elem_followUp = "" . $arrFuVals[$i]["time"];
											$elem_followUpVistType = "" . $arrFuVals[$i]["visit_type"];
											$elem_fuProName = "" . $arrFuVals[$i]["provider"];
											
											//elem_followUpNumber
											$str_elem_followUpNumber = $fu->getFuSelNum($j, $elem_followUpNumber, '', '', '');

											//folowup
											$str_elem_followUp="";
											foreach ($arrOpFu as $key => $val) {
												$sel = ($elem_followUp == $val) ? "selected" : "";
												$str_elem_followUp .= "<option value=\"" . $val . "\" " . $sel . " >" . $val . "</option>";
											}
											
											//elem_followUpVistType
											$str_elem_followUpVistType = $fu->getSelectFuHtml($j, $elem_followUpVistType);											
											
									?>	
											<div class="row pt10" >
												
												<div class='col-sm-3'>
												<?php echo $str_elem_followUpNumber; ?>
												</div>
												<div class='col-sm-3'>	
												<select name="elem_followUp[]" id="elem_followUp_<?php echo $j; ?>" class="form-control minimal" 
															onchange="fu_move(this)"   >							
															<option value=""></option>
															<?php echo $str_elem_followUp; ?>
												</select>
												</div>	
												<div class='col-sm-3'>
												<?php echo $str_elem_followUpVistType;	?>
												</div>
												
												<div class='col-sm-3'>
												<?php if($j==1){ ?>				
												<span class="glyphicon glyphicon-plus pdl_10 pt5" data-toggle="tooltip" title="Add F/U" onclick="fu_add()"></span>
												<?php }else{ ?>
												<span class="glyphicon glyphicon-remove pdl_10 pt5" data-toggle="tooltip" title="Remove F/U" id=\"fu_del<?php echo $j;?>\" onclick="fu_del('<?php echo $j;?>')"></span>
												<?php } ?>
												</div>
												
											</div>
											<div class="clearfix"></div>
									<?php	
										}
									?>
									<input type="hidden" name="elem_fuCntr" value="<?php echo $j;?>">
									</div>
								</td>
							</tr>
							
							
							
						</table>
						
						
						</form>
						<!-- test -->
					</div>
					<div id="module_buttons" class="ad_modal_footer modal-footer">
						<button type="submit" class="btn btn-success">Save</button>
						<button type="button" class="btn btn-danger closeBtn" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</form>
	</div>
	<!-- myModal -->
	
	<!-- settingsModal -->
	<div id="settingsModal" class="modal fade" role="dialog">
		<form name="frmset" id="frmset"  onSubmit="saveFormDataSettings();return false;">
			<div class="modal-dialog"> 
				<div class="modal-content">
					<div class="modal-header bg-primary">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title" id="modal_title">AP Settings</h4>
					</div>
					<div class="modal-body text-center">
						<!-- test -->
						
						<div class="checkbox"><input type="checkbox" id="elem_eCAWV" name="elem_enableCommApWV" value="Enable Community A&P in Work View"> <label for="elem_eCAWV">Enable Community A&P in Work View</label></div>
						<br/><br/>
						<div class="checkbox"><input type="checkbox" id="elem_eDAWV" name="elem_enableDynApWV" value="Enable Dynamic A&P in Work View"> <label for="elem_eDAWV">Enable Dynamic A&P in Work View</label></div>
						
						<!-- test -->
					</div>
					<div id="module_buttons" class="ad_modal_footer modal-footer">
						<button type="submit" class="btn btn-success">Save</button>
						<button type="button" class="btn btn-danger closeBtn" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</form>
	</div>
	<!-- settingsModal -->
	
<?php
	require_once("../../admin_footer.php");
?>      