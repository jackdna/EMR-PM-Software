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
$template_data = $doc_obj->get_template_data($doc_obj->cur_tab_table,'0','0','consultTemplateName , consultLeter_id');
$variable_data = $doc_obj->get_variable_tags('consult_letter');

$panel_data = $doc_obj->get_template_data('document_panels');
$js_php_arr['temp_panels'] = $panel_data[0];	//Used in JS file

//If Edit id exists
$template_preview = $selheader = $selfooter = $sel_left_pnl = $sel_complete_rec = $selFax = '';
if(isset($_REQUEST['edit_id']) && empty($_REQUEST['edit_id']) == false){
	$temp_edit_id = $_REQUEST['edit_id'];
	$edit_temp_data = $doc_obj->get_template_data($doc_obj->cur_tab_table,'consultLeter_id',$temp_edit_id,'consultTemplateName , consultLeter_id');
	$edit_data = $edit_temp_data[0];
	$template_preview = $doc_obj->get_template_preview($edit_data['consultLeter_id']);
	
	if($edit_data['patient_header'] == 1){
		$selheader = 'checked';
	}
	
	if($edit_data['footer'] == 1){
		$selfooter = 'checked';
	}
	
	if($edit_data['leftpanel'] == 1){
		$sel_left_pnl = 'checked';
	}
	
	if($edit_data['complete_consult_report'] == "1") { 
		$sel_complete_rec = 'checked';
	}
	if($edit_data['consultTemplateType'] == "fax_cover_letter") {
		$selFax = "selected"; 
	}
}
?>
<div class="tblBg">
	<div class="row pt10">
		<input type="hidden" name="edit_id" id="edit_id" value="<?php echo $edit_data['consultLeter_id']; ?>">
		<input type="hidden" name="consultLeter_id" id="consultLeter_id" value="<?php echo $edit_data['consultLeter_id']; ?>">
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
												$temp_names .= '<li class="list-group-item pointer "><span onClick="selectTemplate('.$val['consultLeter_id'].',\''.addslashes($val['consultTemplateName']).'\',\''.$doc_obj->current_tab.'\')">'.$val['consultTemplateName'].'</span> <span class="delImg glyphicon glyphicon-remove pull-right" onclick="delTemplate('.$val['consultLeter_id'].')"></span></li>';
											}
										}else{
											$temp_names .= '<li class="list-group-item text-center text-danger">No Record</li>';
										}
										
										$temp_names .= '</ul>';
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
					<div class="row">
						<div class="col-sm-2">
							<span>New Template</span>
						</div>
						<div class="col-sm-4 content_box">
							<div class="checkbox checkbox-inline">
								<input type="checkbox" name="header_chk" id="header_chk" value="1" <?php echo $selheader;?>>
								<label for="header_chk">Header</label>	
							</div>

							<div class="checkbox checkbox-inline">
								<input type="checkbox" name="footer_chk" id="footer_chk" value="1" <?php echo $selfooter;?>>
								<label for="footer_chk">Footer</label>	
							</div>

							<div class="checkbox checkbox-inline">
								<input type="checkbox" name="leftpanel_chk" id="leftpanel_chk" value="1" <?php echo $sel_left_pnl;?>>
								<label for="leftpanel_chk">LeftPanel</label>	
							</div>	
						</div>
						
						<div class="col-sm-offset-1 col-sm-2 content_box">
							<div class="checkbox checkbox-inline">
								<input type="checkbox" name="complete_consult_report" id="complete_consult_report" value="1" <?php echo $sel_complete_rec;  ?>>
								<label for="complete_consult_report">Complete Consult Report</label>	
							</div>	
						</div>	
						
						<div class="col-sm-offset-1 col-sm-2 text-right">
							<span><?php echo $template_preview; ?></span>	
						</div>
					</div>
				</div>
				<div class="tblBg">
					<div class="row">
						<div class="col-sm-12">
							<div class="row">
								<div class="col-sm-4">
									<div class="form-group">
										<label>Template Name</label>
										<input class="form-control" data-preview-template="<?php echo $edit_data['consultTemplateName']; ?>" value="<?php echo $edit_data['consultTemplateName']; ?>" name="templateName" id="templateName" type="text">
									</div>	
								</div>
								
								<div class="col-sm-4">
									<div class="form-group">
										<label>Template Type</label>
										<select name="templateType" id="templateType" class="selectpicker" data-width="100%">
											<option>Default</option>
											<option value="fax_cover_letter" <?php echo $selFax; ?>>Fax Cover Letter</option>
										</select>	
									</div>	
								</div>
								<div class="col-sm-4">
									<div class="row">
										<div class="col-sm-6">
											<label>Margin(T):</label>
											<input type="text" value="<?php echo $edit_data['top_margin']; ?>" name="top_margin" class="form-control">	
										</div>	
										<div class="col-sm-6">
											<label>Margin(L):</label>
											<input type="text" value="<?php echo $edit_data['left_margin']; ?>" name="left_margin" class="form-control">
										</div>	
									</div>	
								</div>
							</div>
						</div>
						<div class="col-sm-12 pt10">
							<textarea id="content" name="content" class="ckeditor_textarea"><?php echo stripslashes($edit_data['consultTemplateData']); ?></textarea>	
						</div>	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>