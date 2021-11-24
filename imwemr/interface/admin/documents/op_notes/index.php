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
$template_data = $doc_obj->get_template_data($doc_obj->cur_tab_table,'0','0','temp_name','=','ASC');
$variable_data = $doc_obj->get_variable_tags('op_notes');

//If Edit id exists
$template_preview = '';
if(isset($_REQUEST['edit_id']) && empty($_REQUEST['edit_id']) == false){
	$temp_edit_id = $_REQUEST['edit_id'];
	$edit_temp_data = $doc_obj->get_template_data($doc_obj->cur_tab_table,'temp_id',$temp_edit_id,'temp_name','=','ASC');
	$edit_data = $edit_temp_data[0];
	$template_preview = $doc_obj->get_template_preview($edit_data['id']);
}
?>
<div class="tblBg">
	<div class="row pt10">
		<input type="hidden" name="edit_id" id="edit_id" value="<?php echo $edit_data['temp_id']; ?>">
		<input type="hidden" name="elem_formAction" id="elem_formAction" value="pnAdmin">
		<input type="hidden" name="elem_edit_id" id="elem_edit_id" value="<?php echo $edit_data['temp_id']; ?>">
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
															<span onClick="selectTemplate('.$val['temp_id'].',\''.addslashes($val['temp_name']).'\',\''.$doc_obj->current_tab.'\')">'.$val['temp_name'].'</span>
														</div>
														<div class="col-sm-1 text-center">
															<span class="delImg glyphicon glyphicon-remove pull-right" onclick="delTemplate('.$val['temp_id'].')"></span>
														</div>
													</div>
												</li>';
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
					<span>New Template</span>	
					<span class="pull-right"><?php echo $template_preview; ?></span>
				</div>
				<div class="tblBg">
					<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label>Template Name</label>
								<input class="form-control" data-preview-template="<?php echo $edit_data['temp_name']; ?>" value="<?php echo $edit_data['temp_name']; ?>" name="templateName" id="templateName" type="text">	
							</div>	
						</div>
						<div class="col-sm-12 pt10">
							<textarea id="content" name="content" class="ckeditor_textarea"><?php echo stripslashes($edit_data['temp_data']); ?></textarea>	
						</div>	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>