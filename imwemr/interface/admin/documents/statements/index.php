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
$template_data = $doc_obj->get_template_data($doc_obj->cur_tab_table);
$template_data = $template_data[0];
$variable_data = $doc_obj->get_variable_tags('statements');

//If Edit id exists
$template_preview = $save_status = '';
if(isset($_REQUEST['edit_id']) && empty($_REQUEST['edit_id']) == false && $_REQUEST['saveBtn']){
	$temp_edit_id = (isset($_REQUEST['edit_id']) && empty($_REQUEST['edit_id']) == false) ? $_REQUEST['edit_id'] : '';
	$arrayRecord['statement_data'] = $_REQUEST['content'];
	$arrayRecord['email_subject'] = $_REQUEST['email_subject'];
	$arrayRecord['email_body'] = $_REQUEST['email_body'];
	if($temp_edit_id != ''){
		$save_stat = $doc_obj->updateRecords($arrayRecord, $doc_obj->cur_tab_table, 'id', $temp_edit_id);
		if($save_stat > 0){
			$save_status = 'Record updated successfully';
		}
	}else{		
		$save_stat = $doc_obj->addRecords($arrayRecord, $doc_obj->cur_tab_table);
		if($save_stat > 0){
			$save_status = 'Record added successfully';
		}
	}
}
$template_preview = $doc_obj->get_template_preview($template_data['id']);
if(empty($save_status) == false){
	echo '<script>top.alert_notification_show("'.$save_status.'");window.location.href = "'.$doc_base_path.'/index.php?showpage='.$doc_obj->current_tab.'"</script>';
}
?>
<div class="tblBg">
	<div class="row pt10">
		<input type="hidden" name="edit_id" id="edit_id" value="<?php echo $template_data['id']; ?>">
		<input type="hidden" name="statement_id" id="statement_id" value="<?php echo $template_data['id']; ?>">
		<input type="hidden" name="preObjBack" id="preObjBack"/>
		<input type="hidden" name="saveBtn" id="saveBtn">
		<div class="col-sm-3 lft_pnl">
			<div class="row">
				<div class="col-sm-12">
					<div class="row">
						<ul class="nav nav-tabs auto_resp_dp single_li">
							<li class="active pointer btn btn-success head" href="#variable_data" data-toggle="tab"><span >Variables</span></li>
						</ul>
					</div>
				</div>

				<div class="col-sm-12">
					<div class="tab-content">
						<div class="tab-pane fade in active" id="variable_data">
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
					<span>Statement</span>	
					<span class="pull-right"><?php echo $template_preview; ?></span>
				</div>
				<div class="tblBg">
					<div class="row">
                        <div class="col-sm-12">
                        	<div class="col-sm-4">
                                <div class="form-group">
                                    <label>Email Subject</label>
                                    <input class="form-control" type="text" name="email_subject" id="email_subject" value="<?php echo $template_data['email_subject']; ?>" >	
                                </div>
                            </div>
                            <div class="col-sm-8">
                               <div class="form-group">
                                    <label>Email Body</label>
                                    <input class="form-control" type="text" name="email_body" id="email_body" value="<?php echo $template_data['email_body']; ?>" >	
                                </div>
                            </div>
                        </div>
						<div class="col-sm-12 pt10">
							<textarea id="content" name="content" class="ckeditor_textarea"><?php echo $template_data['statement_data']; ?></textarea>	
						</div>	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>