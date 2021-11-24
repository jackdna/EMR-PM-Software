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


$template_data = $doc_obj->get_template_data($doc_obj->cur_tab_table);
$variable_data = $doc_obj->get_variable_tags('prescriptions');

//If Edit id exists
$template_preview = '';
if(isset($_REQUEST['edit_id']) && empty($_REQUEST['edit_id']) == false){
	$temp_edit_id = $_REQUEST['edit_id'];
	$edit_temp_data = $doc_obj->get_template_data($doc_obj->cur_tab_table,'id',$temp_edit_id,'');
	$edit_data = $edit_temp_data[0];
	$template_preview = $doc_obj->get_template_preview($edit_data['id']);
}
?>
<div class="tblBg">
	<div class="row pt10">
		<input type="hidden" name="edit_id" id="edit_id" value="<?php echo $edit_data['id']; ?>">
		<input type="hidden" name="preObjBack" id="preObjBack"/>
		<input type="hidden" name="saveBtn" id="saveBtn">
		<div class="col-sm-3 lft_pnl">
			<div class="row">
				<div class="col-sm-12">
					<div class="row">
						<ul class="nav nav-tabs auto_resp_dp">
							<!-- <li class="active pointer btn btn-success head" href="#temp_data" data-toggle="tab"><span >Saved template</span></li> -->
							<li class="active pointer btn btn-success head" href="#variable_data" data-toggle="tab"><span >Variables</span></li>
						</ul>
					</div>
				</div>

				<div class="col-sm-12">
					<div class="tab-content">
						<!-- <div class="tab-pane fade in active selection_box" id="temp_data">
							<div class="row">
								<div class="panel-group" id="auto_resp_temp">
									<div class="panel panel-default">
									<?php 
										$temp_names = '<ul class="list-group">';
										if(count($template_data) > 0){
											foreach($template_data as $key => $val){
												$temp_names .= '<li class="list-group-item pointer "><span onClick="selectTemplate('.$val['id'].',\''.addslashes($val['prescription_template_name']).'\',\''.$doc_obj->current_tab.'\')">'.$val['prescription_template_name'].'</span> <span class="delImg glyphicon glyphicon-remove pull-right" onclick="delTemplate('.$val['id'].')"></span></li>';
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
						</div> -->
						<div class="tab-pane fade active in" id="variable_data">
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
					<span>Prescriptions Template Data</span>	
					<span class="pull-right"><?php echo $template_preview; ?></span>
				</div>
				<div class="tblBg">
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label>Prescription Template</label>
								<select class="selectpicker" data-width="100%" id="prescriptionType" name="prescriptionType" onChange="selectTemplate(this.value,'','<?php echo $doc_obj->current_tab ?>')">
									<option value="">Please select</option>
									<?php
										$tempName = '';
										$optStr = '';
										if(count($template_data) > 0){
											foreach($template_data as $obj){
												$objId = $obj['id'];
												$objName = $obj['prescription_template_name'];
												
												$selID = (isset($edit_data['id']) && empty($edit_data['id']) == false) ? $edit_data['id'] : '';
												$selected = ($selID == $objId) ? 'selected' : '';
												if($objId ==2){ $objName = "Contact Lens (SCL)"; }
												if($objId ==4){ $objName = "Contact Lens (RGP)"; }
												
												if(empty($selected) == false && strtolower($selected) == 'selected') $tempName = $objName;
												
												if(empty($objName) == false && empty($objId) == false) $optStr .= '<option value="'.$objId.'" '.$selected.'>'.$objName.'</option>';
											}
										}
										echo $optStr;
									?>
								</select>
								<?php 
									if(empty($tempName) == false) echo '<input type="hidden" name="prescription_template_name" value="'.$tempName.'">';
								?>
							</div>
						</div>
						<div class="col-sm-offset-3 col-sm-3 pull-right">
							<div class="form-group">
								<label>Print Option</label>
								<div class="row">
									<div class="radio radio-inline">
										<input type="radio" name="printoption" id="landscape" value="0" class="css-checkbox" autocomplete="off" <?php if($edit_data['printOption'] == '0' || !isset($edit_data['printOption'])) echo 'checked'; ?>>
										<label for="landscape">Landscape</label>
									</div>
									&nbsp;&nbsp;&nbsp;&nbsp;
									<div class="radio radio-inline">
										<input type="radio" name="printoption" id="portrait" value="1" class="css-checkbox" autocomplete="off" <?php if($edit_data['printOption'] == '1') echo 'checked'; ?>>
										<label for="portrait">Portrait</label>
									</div>	
								</div>
								
							</div>	
						</div>
						<div class="col-sm-12 pt10">
							<textarea id="content" name="content" class="ckeditor_textarea"><?php echo stripslashes($edit_data['prescription_template_content']); ?></textarea>	
						</div>	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>