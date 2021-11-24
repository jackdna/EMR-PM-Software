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
	include_once($GLOBALS['srcdir']."/classes/admin/class.auto_responder.php");
	
	$autoResp_obj = New AutoRespond($_SESSION['authId']);
	
	//If a request is made i.e [ Add, Update, Delete ]
	if(isset($_REQUEST['autoresp_action']) && empty($_REQUEST['autoresp_action']) == false){
		$save_status = $autoResp_obj->manipulate_data($_REQUEST);
	}
	
	$template_data = $autoResp_obj->get_template_data();
	$variable_data = $autoResp_obj->get_variable_tags();
	$js_array = $autoResp_obj->get_js_arr();
	
	echo '
	<script>
		var js_php_arr = '.$js_array.';'.$save_status.'
	</script>';
?>
<script src="<?php echo $library_path ?>/js/admin/admin_iportal_auto_resp.js"></script>
<body>
<div class="whtbox">
	<div class="tblBg">
		<div class="row pt10">
			<!-- Left Panel -->
			<div class="col-sm-3 lft_panel">
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
										<div class="panel">
										<?php 
											$temp_str = '';
											foreach($template_data as $key => $val){
												$temp_names = '<ul class="list-group">';
												$temp_type = $autoResp_obj->type_arr[$key];
												//Template names
												if(count($val) > 0){
													foreach($val as $val_key => $value_val){
														$temp_names .= '<li class="list-group-item pointer '.(($value_val['status'])?'text-success':'').'" onClick="loadTemplate('.$value_val['type'].','.$val_key.')">'.$value_val['name'].' <span class="delImg glyphicon glyphicon-remove pull-right" onclick="deleteTemp(event, '.$value_val['type'].','.$val_key.')"></span></li>';
													}
												}else{
													$temp_names .= '<li class="list-group-item text-danger">No record</li>';
												}
												
												$temp_names .= '</ul>';
												
												$toggle_name = $autoResp_obj->get_toggle_nm($key);
												//Template Section
												$temp_str .= '
												<div class="panel panel-default">
													<div class="panel-heading pointer" data-toggle="collapse" data-parent="#auto_resp_temp" href="#'.$toggle_name.'_div">
													  <h4 class="panel-title">
														<span class="glyphicon glyphicon-menu-right"></span>
														<span>
															'.$key.'
														</span>
													  </h4>
													</div>
												</div>
												<div id="'.$toggle_name.'_div" class="panel-collapse collapse" data-temp-type="'.$temp_type.'">
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
										<div class="panel">
										<?php 
											$var_str = '';
											foreach($variable_data as $key => $val){
												$variable_names = '<ul class="variable_list list-group">';
												
												//Variable names
												foreach($val as $var_key => $var_val){
													$variable_names .= '<li title="'.trim(str_replace(':','',$var_key)).'" class="list-group-item"><span>'.$var_val.'</span></li>';
												}
												$variable_names .= '</ul>';
												
												//Variable section
												$var_str .= '
												<div class="panel panel-default">
													<div class="panel-heading pointer" data-toggle="collapse" data-parent="#auto_resp_acc" href="#'.$key.'_div">
													  <h4 class="panel-title">
														<span class="glyphicon glyphicon-menu-right"></span>
														<span>
															'.$key.' Variables
														</span>
													  </h4>
													</div>
												</div>
												<div id="'.$key.'_div" class="panel-collapse collapse">
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
			
			<!-- Right Panel -->
			<div class="col-sm-9 rht_panel">
				<div class="adminbox">
					<div class="head">
						<span>Create New Template</span>	
					</div>
					<div class="tblBg">
						<form id="autoresp" name="autoresp" method="POST">
							<div class="row">
								<div class="col-sm-4">
									<div class="form-group">
										<label>Template Name</label>
										<input name="auroresp_temp_name" id="auroresp_temp_name" class="form-control" type="text" value="">	
									</div>	
								</div>
								<div class="col-sm-4">
									<div class="form-group">
										<label>Category</label>
										<select name="autoresp_catg" id="autoresp_catg" class="selectpicker" data-width="100%" data-title="Select">
											<option value="1">Appointment</option>
											<option value="2">Practice Message</option>
											<option value="4">Email- Account Registration</option>
											<option value="5">Email- Account Verificaton</option>
										</select>
									</div>		
								</div>
								<div class="col-sm-4">
									<div class="row">
										<div class="col-sm-9" id="forwardDiv">
											<div class="form-group">
												<label>Forwarder Address</label>
												<input name="auroresp_forward_email" id="auroresp_forward_email" class="form-control" type="text" value="">
											</div>
										</div>	
										<div class="col-sm-3 pull-right">
											<div class="form-group">
												<label>&nbsp;</label>
												<div class="checkbox">
													<input name="auroresp_temp_status" id="auroresp_temp_status" type="checkbox" value="1">
													<label for="auroresp_temp_status">Active</label>	
												</div>	
											</div>
										</div>	
									</div>	
								</div>
							</div>
							<div class="row pt10">
								<div class="col-sm-12">
									<textarea name="tempCont" id="tempCont" data-height=""></textarea>	
								</div>	
							</div>	
							<input type="hidden" name="autoresp_action" id="autoresp_action" value="addNew" />
							<input type="hidden" name="autoresp_tempId" id="autoresp_tempId" value="" />	
						</form>	
					</div>	
				</div>
			</div>
		</div>
	</div>
</div>
<?php 
	require_once("../admin_footer.php");
?>