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

require_once($GLOBALS['srcdir']."/ckeditor/ckeditor.php");
//Data variables
$template_data = $doc_obj->get_consent_data();
$variable_data = $doc_obj->get_variable_tags('consent');
$old_data = $doc_obj->get_consent_data('old_data');
$cat_dropdown_arr = $doc_obj->get_consent_data('dropdown');

$old_ver_id = (isset($_REQUEST['old_version_id']) && empty($_REQUEST['old_version_id']) == false) ? $_REQUEST['old_version_id'] : $_REQUEST['edit_id'];
if(isset($_REQUEST['old_version_id']) && empty($_REQUEST['old_version_id']) == false){
	$js_php_arr['old_ver_id'] = $old_ver_id;
}

//Loading requested form data
if(empty($old_ver_id) == false){
	$temp_edit_id = $old_ver_id;
	$edit_temp_data = $doc_obj->get_template_data($doc_obj->cur_tab_table,'consent_form_id',$temp_edit_id);
	$edit_data = $edit_temp_data[0];
	if(isset($edit_data['consent_form_id'])){
		$old_ver_id = $edit_data['consent_form_id'];
	}
	$template_preview = $doc_obj->get_template_preview($edit_data['id']);
}
?>
<div class="tblBg">
	<div class="row pt10">
		<input type="hidden" name="consent_form_id" id="consent_form_id" value="<?php print $old_ver_id;?>">
		<input type="hidden" name="edit_id" id="edit_id" value="<?php print $old_ver_id;?>">
		<input type="hidden" name="consent_form_cat_id" id="consent_form_cat_id" value="<?php print $edit_data['cat_id']; ?>">
		<input type="hidden" name="delId" id="delId" value="">
		<input type="hidden" name="version_form_id" id="version_form_id" value="<?php print $edit_data['consent_form_version']; ?>">
		<input type="hidden" name="change_form_id" id="change_form_id" value="">
		
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
											$temp_str = '';
											foreach($template_data as $key => $val){
												//Show tab containing edit id open
												$selected_tab = '';
												$temp_names = '<ul class="list-group">';
												$temp_type = $doc_obj->type_arr[$key];
												//Template names
												if(count($val) > 0){
													foreach($val as $obj){
														if($obj['consent_form_id'] == $edit_data['consent_form_id']){
															$selected_tab = 'in';
														}
														$temp_names .= '<li class="list-group-item pointer ">
															<div class="row">
																<div class="col-sm-10">
																	<span onClick="selectTemplate('.$obj['consent_form_id'].',\''.addslashes($obj['consent_form_name']).'\',\''.$doc_obj->current_tab.'\')">'.$obj['consent_form_name'].'</span>
																</div>	
																<div class="col-sm-2">
																	<span class="delImg glyphicon glyphicon-remove pull-right" onclick="delTemplate('.$obj['consent_form_id'].')"></span>
																</div>	
															</div>
														</li>';
													}
												}else{
													$temp_names .= '<li class="list-group-item text-danger">No record</li>';
												}
												
												$temp_names .= '</ul>';
												
												//Menu arrow icons
												$menu_icon = 'glyphicon-menu-right';
												if($selected_tab != ''){
													$menu_icon = 'glyphicon-menu-down';
												}
												
												$toggle_name = $doc_obj->get_toggle_nm($key);
												//Template Section
												$temp_str .= '
												<div class="panel panel-default">
													<div class="panel-heading pointer" data-toggle="collapse" data-parent="#auto_resp_temp" href="#'.$toggle_name.'_div">
													  <h4 class="panel-title">
														<span class="glyphicon '.$menu_icon.'"></span>
														<span>
															'.$key.'
														</span>
													  </h4>
													</div>
												</div>
												<div id="'.$toggle_name.'_div" class="panel-collapse collapse '.$selected_tab.'" data-temp-type="'.$temp_type.'">
													'.$temp_names.'
												</div>';
											}
											echo $temp_str;
										?>
									</div>
								</div>	
							</div>
						</div>
						<div class="tab-pane fade" id="variable_data">
							<div class="row">
								<div class="panel-group selection_box" id="auto_resp_acc">
									<div class="panel panel-default">
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
					<span>Consent Form</span>	
					<span class="pull-right"><?php echo $template_preview; ?></span>
				</div>
				<div class="tblBg">
					<div class="row">
						<div class="col-sm-12">
							<div class="row">
								<div class="col-sm-4">
									<div class="form-group">
										<label>Name</label>
										<input name="consent_form_name" data-preview-template="<?php echo $edit_data['consent_form_name']; ?>" id="consent_form_name" type="text" class="form-control" value="<?php print $edit_data['consent_form_name']; ?>">	
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label>Category</label>
										<select class="selectpicker" data-width="100%" id="consent_cat" name="consent_cat" onChange="check_yreview(this);" data-title="Select">
											<?php 
												$dp_str = '';
												$consent_cat_arr = array();
												foreach($cat_dropdown_arr as $key => $val){
													$sel ='';
													if($key == $edit_data['cat_id']){
														$sel = 'selected';
													}
													$dp_str .= '<option value="'.$key.'" '.$sel.'>'.$val['category_name'].'</option>';
													
													if($val['section'] != ''){
														$consent_cat_arr[] = $val['category_name'];
													}
												}
												echo $dp_str;
												$js_php_arr['consent_cat_arr'] = $consent_cat_arr;	//Used in js file
											?>
										</select>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="row">
										<div class="col-sm-8">
											<label>Old Data</label>
											<?php 
												echo $old_data['old_data'];
											?>	
										</div>
										<div class="col-sm-4 hide" id="td_yreview">
											<label>&nbsp;</label>
											<?php
												$yrview_chkd = '';
												if($edit_data['yearly_review'] == 1){
													$yrview_chkd = ' checked="checked"';
												}
											?>
											<div class="checkbox">
												<input type="checkbox" name="yreview" id="yreview" value="1"<?php echo $yrview_chkd;?>>
												<label for="yreview">Yearly Review</label>	
											</div>	
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-12 pt10">
							<textarea id="content" name="consentForm" class="ckeditor_textarea">
								<?php echo stripslashes($edit_data['consent_form_content']); ?>
							</textarea>
							<?php 
								//$CKEditor = new CKEditor();
								//$CKEditor->basePath = $GLOBALS['webroot'].'/library/ckeditor/';
								// Create a textarea element and attach CKEditor to it.
								//$CKEditor->editor("consentForm", stripslashes($edit_data['consent_form_content']));
							?>
						</div>	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>