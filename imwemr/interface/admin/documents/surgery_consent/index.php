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

//Data variables
$sur_consent_dt = $doc_obj->get_surgery_consent_data();
$template_data = $sur_consent_dt['data'];
$cat_arr = $sur_consent_dt['cat_arr'];
$variable_data = $doc_obj->get_variable_tags('surger_consent');

//Loading requested form data
 if(empty($_REQUEST['edit_id']) == false){
	$temp_edit_id = $_REQUEST['edit_id'];
	$edit_temp_data = $doc_obj->get_template_data($doc_obj->cur_tab_table,'consent_id',$temp_edit_id);
	$edit_data = $edit_temp_data[0];
}
?>
<div class="tblBg">
	<div class="row pt10">
		<input type="hidden" name="edit_id" id="edit_id" value="<?php print $edit_data['consent_id'];?>">
		<input type="hidden" name="consent_form_id" id="consent_form_id" value="<?php print $edit_data['consent_id'];?>">
		<input type="hidden" name="delId" id="delId" value="">
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
															if($obj['consent_category_id'] == $edit_data['consent_category_id']){
															$selected_tab = 'in';
														}
														$temp_names .= '<li class="list-group-item pointer ">
															<div class="row">
																<div class="col-sm-10">
																	<span onClick="selectTemplate('.$obj['consent_id'].',\''.addslashes(stripslashes($obj['consent_name'])).'\',\''.$doc_obj->current_tab.'\')">'.$obj['consent_name'].'</span>
																</div>	
																<div class="col-sm-2">
																	<span class="delImg glyphicon glyphicon-remove pull-right" onclick="delTemplate('.$obj['consent_id'].')"></span>
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
					<span>Surgery Consent Form</span>	
				</div>
				<div class="tblBg">
					<div class="row">
						<div class="col-sm-12">
							<div class="row">
								<div class="col-sm-4">
									<div class="form-group">
										<label>Name</label>
										<input name="consent_form_name" id="consent_form_name" type="text" class="form-control" value="<?php print $edit_data['consent_name']; ?>">	
									</div>
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label>Category</label>
										
										<select class="selectpicker" data-width="100%" id="consent_cat" name="consent_cat" data-title="Select">
											<?php 
												$dp_str = '';
												$surgery_cat_arr = array();
												foreach($cat_arr as $key => $val){
													$sel ='';
													if($key == $edit_data['consent_category_id']){
														$sel = 'selected';
													}
													$dp_str .= '<option value="'.$key.'" '.$sel.'>'.$val.'</option>';
												}
												echo $dp_str;
											?>
										</select>
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-12 pt10">
							<textarea id="content" name="consentForm" class="ckeditor_textarea">
								<?php echo stripslashes($edit_data['consent_data']); ?>
							</textarea>
						</div>	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>