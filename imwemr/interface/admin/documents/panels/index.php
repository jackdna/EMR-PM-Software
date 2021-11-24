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
$panel_dt = $doc_obj->get_template_data($doc_obj->cur_tab_table);
$variable_data = $doc_obj->get_variable_tags('panels');
?>
<div class="wtbox">
<div class="tblBg">
	<div class="row pt10">
		<input type="hidden" name="preObjBack" id="preObjBack"/>
		<input type="hidden" name="saveBtn" id="saveBtn">
		<input type="hidden" name="panel_id" id="panel_id" value="<?php echo $panel_dt[0]['id']; ?>">
		<div class="col-sm-2 lft_pnl">
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
		<div class="col-sm-10 rght_pnl">
			<div class="row">
				<div class="col-sm-3">
					<div class="adminbox">
						<div class="head">
							<span>Left Panel</span>
						</div>
						<div class="tblBg">
							<div class="row">
								<div class="col-sm-12">
									<textarea id="leftpanel" name="leftpanel" class="ckeditor_textarea"><?php echo stripslashes($panel_dt[0]['leftpanel']); ?></textarea>	
								</div>	
								
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-9">
					<div class="row">
						<div class="col-sm-12">
							<div class="adminbox">
								<div class="head"><span>Header</span></div>
								<div class="tblBg">
									<div class="row">
										<div class="col-sm-12">
											<textarea id="header" name="header" class="ckeditor_textarea extra_textarea"><?php echo stripslashes($panel_dt[0]['header']); ?></textarea>	
										</div>	
									</div>
								</div>
							</div>	
						</div>	
					</div>
					<div class="row">
						<div class="col-sm-12">
							<div class="adminbox">
								<div class="head"><span>Footer</span></div>
								<div class="tblBg">
									<div class="row">
										<div class="col-sm-12">
											<textarea id="footer" name="footer" class="ckeditor_textarea extra_textarea"><?php echo stripslashes($panel_dt[0]['footer']); ?></textarea>	
										</div>	
									</div>
								</div>
							</div>	
						</div>	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>