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

//Education and instructions uses same page
//Setting variables depending on $doc_obj->current_tab
$layout_arr = array();
switch($doc_obj->current_tab){
	case 'education';
		$select_from = 'status=0 and pt_edu > 0';
		$pg_hidden_field = 'pt_edu';
	break;
	
	case 'instructions';
		$select_from = 'status=0 and pt_test > 0';
		$pg_hidden_field = 'pt_test';
	break;
}

//Data variables
$template_data = $doc_obj->get_template_data($doc_obj->cur_tab_table,$select_from,'','name','','ASC');
$variable_data = $doc_obj->get_variable_tags('education');

//Dropdown arrays
$cpt_arr = $doc_obj->common_array['cpt'];
$type_visit_arr = $doc_obj->common_array['type_visit'];
$medication_arr = $doc_obj->common_array['medication'];
$test_exam_arr = $doc_obj->common_array['test'];
$icd_10_arr = $doc_obj->common_array['icd_10'];
$lab_arr = $doc_obj->common_array['lab'];
$lab_criteria_arr = $doc_obj->common_array['lab_criteria'];

//Updating values and documents
if(isset($_REQUEST['saveBtn']) && empty($_REQUEST['saveBtn']) == false){
	$save_status = $doc_obj->manage_education($_REQUEST,$_FILES);
	if(count($save_status) > 0){
		echo '<script>
			top.alert_notification_show("'.$save_status['msg'].'");
			window.location.href = "'.$save_status['url'].'"
		</script>';
	}
}

//If Edit id exists
$template_preview = '';
if(isset($_REQUEST['edit_id']) && empty($_REQUEST['edit_id']) == false){
	$temp_edit_id = $_REQUEST['edit_id'];
	$edit_temp_data = $doc_obj->get_template_data($doc_obj->cur_tab_table,'id',$temp_edit_id,'name');
	$edit_data = $edit_temp_data[0];
	$template_preview = $doc_obj->get_template_preview($edit_data['id']);
	
	$name					=	$edit_data['name'];
	$ccda_code 				=	$edit_data['ccda_code'];
	$visit					=	$edit_data['visit'];
	$tests					=	$edit_data['tests'];
	$txt_lab_name			=	stripslashes($edit_data['txt_lab_name']);
	$lab_criteria 			= 	stripslashes($edit_data['lab_criteria']);
	$lab_result 			= 	stripslashes($edit_data['lab_result']);
	$dx						=	$edit_data['dx'];
	$cpt					=	$edit_data['cpt'];
	$medications			=	$edit_data['medications'];
	$content				=	$edit_data['content'];
	$old_name				=	$edit_data['name'];
	$scan_id				=	$edit_data['scan_id'];
	$pt_edu					=	($doc_obj->current_tab == 'education') ? $edit_data['pt_edu'] : '';
	$pt_test				=	($doc_obj->current_tab == 'instructions') ? $edit_data['pt_test'] : '';
	$andOrCondition 		= 	$edit_data['andOrCondition'];									
	$doc_from 				= 	$edit_data['doc_from'];
	$scan_doc_file_path		=	$edit_data['scan_doc_file_path'];
	$upload_doc_file_path	=	$edit_data['upload_doc_file_path'];
	$upload_doc_type		=	$edit_data['upload_doc_type'];
	
	//Setting documents path
	list($thmb_path,$thmb_path_upload,$tempImgWH,$upload_dir_web) = $doc_obj->get_scan_upload_path($scan_doc_file_path,$upload_doc_file_path,$upload_doc_type);
}

//Js Variable Array
$js_php_arr['icd10Arr'] = json_encode(array_keys($icd_10_arr));

// ICD 10 array selected
$docId = $edit_data['id'];
$icd_10_selected = array();
$js_php_arr['icd10ArrSelected'] = '';

if(!empty($docId) && $docId != 0) {
	$icd_10s_qry = imw_query("select dx from document where id = ".$docId." ");
	$str_data = '';
	if(imw_num_rows($icd_10s_qry) > 0){
		while($icd_rows = imw_fetch_assoc($icd_10s_qry)){
			$str_data = $icd_rows['dx'];
		}
	}
	if($str_data != '') {
		$arr = explode(',', $str_data);
		foreach ($arr as $key => $code) {
			$icd_10_selected[trim($code)] = trim($code);
		}
	}
	$js_php_arr['icd10ArrSelected'] = json_encode(array_keys($icd_10_selected));
}


?>
<div class="tblBg">
	<div class="row pt10">
		<input type="hidden" name="edit_id" id="edit_id" value="<?php echo $edit_data['id']; ?>">
		<input type="hidden" name="<?php echo $pg_hidden_field; ?>" id="<?php echo $pg_hidden_field; ?>" value="1">
		<input type="hidden" value="<?php echo $edit_data['id']; ?>" name="eid">
		<input type="hidden" value="<?php echo $old_name;?>" name="old_name">
		<input type="hidden" value="<?php echo $scan_id;?>" name="scan_id">
		<input type="hidden" value="<?php echo $scan_doc_file_path;?>" name="hidd_scn_path">
		<input type="hidden" value="<?php echo $upload_doc_file_path;?>" name="hidd_upld_path">
		<input type="hidden" value="<?php echo $doc_from;?>" name="hidd_prev_doc_from">
		
		<input type="hidden" name="preObjBack" id="preObjBack"/>
		<input type="hidden" name="saveBtn" id="saveBtn">
		<div class="col-sm-3 lft_pnl">
			<div class="row">
				<div class="col-sm-12">
					<div class="row">
						<ul class="nav nav-tabs auto_resp_dp">
							<li class="active pointer btn btn-success head" href="#temp_data" data-toggle="tab"><span >Saved template</span></li>
							<li class="pointer btn btn-success head" href="#variable_data" data-toggle="tab"><span >Variables</span></li>
						</ul>
					</div>
				</div>

				<div class="col-sm-12">
					<div class="tab-content">
						<div class="tab-pane fade in active selection_box" id="temp_data">
							<div class="row">
								<div class="panel-group" id="auto_resp_temp">
									<div class="panel panel-default">
									<?php 
										$temp_names = '<ul class="list-group">';
										if(count($template_data) > 0){
											foreach($template_data as $key => $val){
												$temp_names .= '<li class="list-group-item pointer ">
													<div class="row">
														<div class="col-sm-11">
															<span onClick="selectTemplate('.$val['id'].',\''.addslashes($val['name']).'\',\''.$doc_obj->current_tab.'\')">'.$val['name'].'</span>
														</div>
														<div class="col-sm-1 text-center">
															<span class="delImg glyphicon glyphicon-remove pull-right" onclick="delTemplate('.$val['id'].')"></span>	
														</div>	
													</div>
												</li>';
											}
										}else{
											$temp_names .= '<li class="list-group-item text-center text-danger">No Record</li>';
										}
										
										$temp_names .= '<ul class="list-group">';
										echo $temp_names;
									?>
									</div>
								</div>	
							</div>
						</div>
						<div class="tab-pane fade" id="variable_data">
							<div class="row">
								<div class="panel-group selection_box" id="auto_resp_acc">
									<div class="panel">
									<?php 
										$var_str = '';
										foreach($variable_data as $key => $val){
											$toggle_names = $doc_obj->get_toggle_nm($key);
											$variable_names = '<ul class="variable_list list-group">';
											
											//Variable names
											foreach($val as $var_key => $var_val){
												$variable_names .= '<li title="'.trim(str_replace(':','',$var_key)).'" class="list-group-item"><span>'.$var_key.'</span></li>';
											}
											$variable_names .= '</ul>';
											
											//Variable section
											$var_str .= '
											<div class="panel panel-default">
												<div class="panel-heading pointer" data-toggle="collapse" data-parent="#auto_resp_acc" href="#'.$toggle_names.'_div">
												  <h4 class="panel-title">
													<span class="glyphicon glyphicon-menu-right"></span>
													<span>
														'.$key.' Variables
													</span>
												  </h4>
												</div>
											</div>
											<div id="'.$toggle_names.'_div" class="panel-collapse collapse">
												'.$variable_names.'
											</div>';
										}
										echo $var_str;
									?>
									</div>	
								</div>	
							</div>
						</div>
					</div>	
				</div>
			</div>
		</div>
		<div class="col-sm-9 rght_pnl">
			<div class="adminbox">
				<div class="head">
					<span>Document Form</span>	
					<span class="pull-right"><?php echo $template_preview; ?></span>
				</div>
				<div class="tblBg">
					<div class="row">
						<?php 
							include_once($doc_obj->get_base_path('file'). '/education/'.$doc_obj->current_tab.'_top_content.php');
						?>	
					</div>
					<div class="row">	
						<!-- Content Box -->
						<div id="divWriteId" class="col-sm-12 pt10 hide">
							<textarea id="content" name="content" class="ckeditor_textarea"><?php echo stripslashes($content); ?></textarea>	
						</div>

						<!-- Upload Box -->
						<div id="uploadDocId" class="col-sm-12 pt10 hide">
							<label>Please click Browse button to upload image</label>
							<div class="row">
								<div class="col-sm-4">
									<div class="input-group">
										<label class="input-group-btn">
											<span class="btn btn-primary">
												Browse <input type="file" class="hide" name="upld_doc" id="upld_doc">
											</span>
										</label>
										<input type="text" class="form-control" readonly>
									</div>	
								</div>

								<div class="col-sm-2 col-sm-offset-6 text-center">
									<?php
										if($upload_doc_file_path && $doc_from=='uploadDoc'){
											if($upload_doc_type=='pdf'){ ?>
												<span class="glyphicon glyphicon-file pointer" style="font-size:4em" onClick="javascript:window.open('<?php echo $upload_dir_web.$upload_doc_file_path; ?>','','width=800,height=650,resizable=yes,scrollbars=yes')" <?php echo show_tooltip('Click to view','top'); ?>></span>
											<?php }else{ ?>
												<img title="click here to view large" class="pointer" name="thumbImgUpld" style="<?php echo $tempImgWH; ?>"  src="<?php echo $thmb_path_upload; ?>" onClick="javascript:window.open('<?php echo $upload_dir_web.$upload_doc_file_path; ?>','','width=800,height=650,resizable=yes,scrollbars=yes');" <?php echo show_tooltip('Click here to view','top'); ?>>
											<?php }
										}
									?>	
								</div>	
								
							</div>
						</div>
						
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<!-- Education Page Dx Modal  -->
	<div id="dxModal" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header bg-primary">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Select Dx Code</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-sm-5">
							<label>Dx Code</label>
							<select class="form-control" size="10" multiple id="sourceEle"></select>
						</div>
						<div class="col-sm-2 text-center">
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<button type="button" class="btn btn-default btn-md glyphicon glyphicon-triangle-right" onClick="moveValues(this);" data-direction="target" data-size="1"></button>
									</div>
								</div>
								
								<div class="col-sm-12">
									<div class="form-group">
										<button type="button" class="btn btn-default btn-md glyphicon glyphicon-triangle-left" onClick="moveValues(this);" data-direction="source" data-size="1"></button>
									</div>
								</div>

								<div class="col-sm-12">
									<div class="form-group">
										<button type="button" class="btn btn-default btn-md glyphicon glyphicon-backward" onClick="moveValues(this);" data-direction="source" data-size="all"></button>
									</div>
								</div>

								<div class="col-sm-12">
									<div class="form-group">
										<button type="button" class="btn btn-default btn-md glyphicon glyphicon-forward" onClick="moveValues(this);" data-direction="target" data-size="all"></button>
									</div>
								</div>	
							</div>
						</div>
						<div class="col-sm-5">
							<label>Selected Dx Code</label>
							<select class="form-control" size="10" multiple id="targetEle"></select>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-success" id="HideDxModal" data-element="" onClick="hideDxModal(this);">OK</button>
				</div>
			</div>
		</div>
	</div>
	
	
</div>