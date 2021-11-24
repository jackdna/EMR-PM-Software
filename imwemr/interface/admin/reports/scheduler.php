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
 
 File: index.php
 Purpose: A router to route to selected report section
 Access Type: Indirect Access.
*/
require_once("../admin_header.php");
$temp_id = $_REQUEST['edit_id'];
if($temp_id){
	$sql_query = imw_query("SELECT * FROM `custom_reports` WHERE id='$temp_id' and `delete_status` = 0 and `report_type` = 'scheduler'");
	if(imw_num_rows($sql_query) > 0){
		$row = imw_fetch_assoc($sql_query);
		$dbtemp_id  = $row['id'];
		$dbtemp_name  = $row['template_name'];
		$default_report  = $row['default_report'];
		$dbtemplate_fields  = unserialize($row['template_fields']);
	}
	$filter_arr = array();
	foreach($dbtemplate_fields as $obj){
		$filter_arr[$obj['name']] = $obj['value'];
	}
}
?>
<script type="text/javascript">
function addReport(){
	var msg = '';
	var msg4checkboes = "";
	if(jQuery('#templateName').val() == ''){
      msg += "<b>Please Enter the following fields.</b><br> - Template Name can not be left blank <br />";
	}
	if(msg.length){
		fAlert(msg);
		return false;
	}
	//Practice
	var prac_arr = new Array;
	$('#practice_filter').find('[type=checkbox]').each(function(id,elem){
		if($(elem).prop('checked') == true){
			prac_arr.push($(elem).val());
		}
	});
	if(!prac_arr.length){
		msg4checkboes += '- Practice Filter <br />';
	}
	//Appointment
	var app_arr = new Array;
	$('#appointment_filter').find('[type=checkbox]').each(function(id,elem){
		if($(elem).prop('checked') == true){
			app_arr.push($(elem).val());
		}
	});
	if(!app_arr.length){
		msg4checkboes += '- Appointment Filter <br />';
	}
	//Group By
	var group_arr = new Array;
	$('#group_by').find('[type=checkbox]').each(function(id,elem){
		if($(elem).prop('checked') == true){
			group_arr.push($(elem).val());
		}
	});
	if(!group_arr.length){
		msg4checkboes += '- Group By <br />';
	}
	//Include
	var include_arr = new Array;
	$('#include').find('[type=checkbox]').each(function(id,elem){
		if($(elem).prop('checked') == true){
			include_arr.push($(elem).val());
		}
	});
	if(!include_arr.length){
		msg4checkboes += '- Include <br />';
	}
	//Format
	var format_arr = new Array;
	$('#format').find('[type=checkbox]').each(function(id,elem){
		if($(elem).prop('checked') == true){
			format_arr.push($(elem).val());
		}
	});
	if(!format_arr.length){
		msg4checkboes += '- Format <br />';
	}
	if(prac_arr.length || app_arr.length || group_arr.length || include_arr.length || format_arr.length){
			save_custom_report_data();
		}else{
			msg4checkboes = '<b>Please select atlest one checkbox from:-</b><br>'+msg4checkboes;
			fAlert(msg4checkboes);
			return false;
		}
	}
	
	function save_custom_report_data(){
		var templateName = $('#templateName').val();
		var form_data = $('#scheduler_form').serializeArray();
		var edit_id = $('#edit_id').val();
		$.ajax({
			type: 'POST',
			url: 'schedulerAjax.php',
			data: {ajaxReq:'report_data', templateName:templateName,form_data:form_data,edit_id:edit_id},
			success : function(data) {
				top.alert_notification_show(data);
				window.location.reload();
			}
		});
	}
	
	function retrive_data(id,default_report){
		$('#edit_id').val(id);
		$('#disable').val(default_report);
		document.scheduler_form.submit();
	}
	
	function delTemplate(del_id){
		$.ajax({
			type: 'POST',
			url: 'schedulerAjax.php',
			data: {ajaxReq:'del_template', del_id:del_id},
			success : function(data) {
				top.alert_notification_show(data);
				window.location.reload();
			}
		});
	}
	
	function reset_page(){
		$('#edit_id').val('');
		window.location.href = top.JS_WEB_ROOT_PATH+"/interface/admin/reports/scheduler.php";
	}
	
	function setDisabled(default_report){
		if(default_report !== '' && default_report == 1){
			$("input[type=text]").prop("disabled", true);	
			$("input[type=checkbox]").prop("disabled", true);
			$("select").prop("disabled", true).selectpicker('refresh');
		}
	}
</script>
<div class="whtbox">
	<div class="tblBg">
	<div class="row pt10">
		<div class="col-sm-3 lft_pnl col-lg-2 col-md-2">
			<div class="row">
				<div class="savedtem"><h2>Scheduler templates </h2></div>
				<div class="col-sm-12">
					<div class="tab-content">
						<div class="tab-pane fade in active selection_box" id="temp_data">
							<div class="row">
								<div class="panel-group" id="auto_resp_temp">
									<div class="panel panel-default">
										<ul class="list-group">
											<?php 
											$default_report = "";
											$sql = imw_query("SELECT * FROM `custom_reports` WHERE `report_type` = 'scheduler' and `delete_status` = 0"); 
											if(imw_num_rows($sql) > 0){
													while($row_fet = imw_fetch_assoc($sql)){
														$id = $row_fet['id'];
														$temp_name = $row_fet['template_name'];
														$default_report = $row_fet['default_report'];
														$del_link = "";
														if($default_report <> 1){
															$del_link = '<span class="glyphicon glyphicon-remove pull-right pointer" onclick="delTemplate('.$id.')"></span>';
														} 
														$href = '<span class="pointer" onClick="retrive_data('.$id.','.$default_report.')">'.$temp_name.'</span>';
																										
														$temp_names .= '<li class="list-group-item pd5 pdl_10">
															'.$href.'
															'.$del_link.'
														</li>';
													}
											}
											$temp_names .= '</ul>';
											echo $temp_names;
											?>
									</div>
								</div>	
							</div>
						</div>
					</div>	
				</div>
			</div>
		</div>
		<div class="col-sm-9 rght_pnl col-lg-10 col-md-10">
			<div class="adminbox">
				<div class="head">
					<span>New Report Template</span>	
				</div>
				<form name="scheduler_form" id="scheduler_form" method="post">
				<input type="hidden" id="edit_id" name="edit_id" value="<?php echo $dbtemp_id; ?>">
				<input type="hidden" id="disable" name="disable" value="<?php echo $_REQUEST['disable']; ?>">
				<div class="tblBg">
					<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<label>Template Name</label>
								<input class="form-control" value="<?php echo $dbtemp_name; ?>" id="templateName" type="text">	
							</div>	
						</div>
						<div class="col-sm-12 pt10">
							<div class="row">
                                <div class="col-lg-3 col-md-6 col-sm-6" id="practice_filter">
									<div class="grpbox">
										<div class="head"><span>Practice Filter</span></div>
											<div class="clearfix"></div>
											<div class="tblBg">
                                            <div class="row">
												<div class="col-sm-12">
                                                	<div class="checkbox checkbox-inline pointer">
                                                    	<input type="checkbox" name="facility" id="facility" value="1" <?php if ($filter_arr['facility'] == '1') echo 'CHECKED'; ?>/> 
                                                        <label for="facility">Facility</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                	<div class="checkbox checkbox-inline pointer">
                                                    	<input type="checkbox" name="physician" id="physician" value="1" <?php if ($filter_arr['physician'] == '1') echo 'CHECKED'; ?>/> 
                                                        <label for="physician">Physician</label>
                                                    </div>
                                                </div>                                                
                                                <div class="col-sm-12">
                                                	<div class="checkbox checkbox-inline pointer">
                                                    	<input type="checkbox" name="operators" id="operators" value="1" <?php if ($filter_arr['operators'] == '1') echo 'CHECKED'; ?>/> 
                                                        <label for="operators">Operators</label>
                                                    </div>
                                                </div>  
                                                <div class="col-sm-12">
                                                	<div class="checkbox checkbox-inline pointer">
                                                    	<input type="checkbox" name="date_range" id="date_range" value="1" <?php if ($filter_arr['date_range'] == '1') echo 'CHECKED'; ?>/> 
                                                        <label for="date_range">Date Range</label>
                                                    </div>
                                                </div>  
                                                <div class="col-sm-12">
                                                	<div class="checkbox checkbox-inline pointer">
                                                    	<input type="checkbox" name="day" id="day" value="1" <?php if ($filter_arr['day'] == '1') echo 'CHECKED'; ?>/> 
                                                        <label for="day">Day</label>
                                                    </div>
                                                </div>  
											</div>
										</div>
									</div>
								</div>
								<div class="col-lg-3 col-md-6 col-sm-6" id="appointment_filter">
									<div class="grpbox">
										<div class="head"><span>Appointment Filter</span></div>
										<div class="clearfix"></div>
										<div class="tblBg">
											<div class="row">
												<div class="col-sm-6">
													<div class="checkbox checkbox-inline pointer">
														<input type="checkbox" name="procedures" id="procedures" value="1" <?php if ($filter_arr['procedures'] == '1') echo 'CHECKED'; ?>/> 
														<label for="procedures">Procedures</label>
													</div>	
												</div>
												<div class="col-sm-6">
													<div class="checkbox checkbox-inline pointer">
														<input type="checkbox" name="appt_status" id="appt_status" value="1" <?php if ($filter_arr['appt_status'] == '1') echo 'CHECKED'; ?>/> 
														<label for="appt_status">Appt Status</label>
													</div>
												</div>
												<div class="col-sm-6">
													<div class="checkbox checkbox-inline pointer">
														<input type="checkbox" name="date_made" id="date_made" value="1" <?php if ($filter_arr['date_made'] == '1') echo 'CHECKED'; ?>/> 
														<label for="date_made">Date Made</label>
													</div>
												</div>
												<div class="col-sm-6">
													<div class="checkbox checkbox-inline pointer">
														<input type="checkbox" name="ins_carriers" id="ins_carriers" value="1" <?php if ($filter_arr['ins_carriers'] == '1') echo 'CHECKED'; ?>/> 
														<label for="ins_carriers">Ins. Carriers</label>
													</div>
												</div>  
												<div class="col-sm-6">
													<div class="checkbox checkbox-inline pointer">
														<input type="checkbox" name="ins_types" id="ins_types" value="1" <?php if ($filter_arr['ins_types'] == '1') echo 'CHECKED'; ?>/> 
														<label for="ins_types">Ins. Types</label>
													</div>
												</div>
												<div class="col-sm-6">
													<div class="checkbox checkbox-inline pointer">
														<input type="checkbox" name="icd10_codes" id="icd10_codes" value="1" <?php if ($filter_arr['icd10_codes'] == '1') echo 'CHECKED'; ?>/> 
														<label for="icd10_codes">ICD10 Codes</label>
													</div>
												</div>                                                                                                
												<div class="col-sm-6">
													<div class="checkbox checkbox-inline pointer">
														<input type="checkbox" name="registered_fac" id="registered_fac" value="1" <?php if ($filter_arr['registered_fac'] == '1') echo 'CHECKED'; ?>/> 
														<label for="registered_fac">Registered Facility</label>
													</div>
												</div> 
												<div class="col-sm-6">
													<div class="checkbox checkbox-inline pointer">
														<input type="checkbox" name="age_search" id="age_search" value="1" <?php if ($filter_arr['age_search'] == '1') echo 'CHECKED'; ?>/> 
														<label for="age_search">Age Search</label>
													</div>
												</div> 
												<div class="col-sm-6">
													<div class="checkbox checkbox-inline pointer">
														<input type="checkbox" name="rte" id="rte" value="1" <?php if ($filter_arr['rte'] == '1') echo 'CHECKED'; ?>/> 
														<label for="rte">RTE</label>
													</div>
												</div> 
												<div class="col-sm-6">
													<div class="checkbox checkbox-inline pointer">
														<input type="checkbox" name="pre_auth" id="pre_auth" value="1" <?php if ($filter_arr['pre_auth'] == '1') echo 'CHECKED'; ?>/> 
														<label for="pre_auth">Pre-Auth</label>
													</div>
												</div>                                                 
											</div>
										</div>
									</div>
								</div>
								<div class="col-lg-2 col-md-6 col-sm-6" id="group_by">
									<div class="grpbox">
										<div class="head"><span>Group By</span></div>
										<div class="clearfix"></div>
										<div class="tblBg">
											<div class="row">
												<div class="col-sm-12">
													<div class="checkbox checkbox-inline pointer">
														<input type="checkbox" name="grpby_groups" id="grpby_groups" value="1" <?php if ($filter_arr['grpby_groups'] == '1') echo 'CHECKED'; ?>/> 
														<label for="grpby_groups">Groups</label>
													</div>
												</div>
												<div class="col-sm-12">
													<div class="checkbox checkbox-inline pointer">
														<input type="checkbox" name="grpby_facility" id="grpby_facility" value="1" <?php if ($filter_arr['grpby_facility'] == '1') echo 'CHECKED'; ?>/> 
														<label for="grpby_facility">Facility</label>
													</div>
												</div>
												<div class="col-sm-12">
													<div class="checkbox checkbox-inline pointer">
														<input type="checkbox" name="grpby_physician" id="grpby_physician" value="1" <?php if ($filter_arr['grpby_physician'] == '1') echo 'CHECKED'; ?>/> 
														<label for="grpby_physician">Physician</label>
													</div>
												</div>                                                
												<div class="col-sm-12">
													<div class="checkbox checkbox-inline pointer">
														<input type="checkbox" name="grpby_operators" id="grpby_operators" value="1" <?php if ($filter_arr['grpby_operators'] == '1') echo 'CHECKED'; ?>/> 
														<label for="grpby_operators">Operators</label>
													</div>
												</div>  
											</div>
										</div>
									</div>
								</div>
								<div class="col-lg-2 col-md-6 col-sm-6" id="include">
									<div class="grpbox">
										<div class="head"><span>Include</span></div>
										<div class="clearfix"></div>
											<div class="tblBg">
												<div class="row">
													<div class="col-sm-12">
														<div class="checkbox checkbox-inline pointer">
															<input type="checkbox" name="inc_appt_detail" id="inc_appt_detail" value="1" <?php if ($filter_arr['inc_appt_detail'] == '1') echo 'CHECKED'; ?>/> 
															<label for="inc_appt_detail">Appt Detail</label>
														</div>
													</div>
													<div class="col-sm-12">
														<div class="checkbox checkbox-inline pointer">
															<input type="checkbox" name="inc_appt_status" id="inc_appt_status" value="1" <?php if ($filter_arr['inc_appt_status'] == '1') echo 'CHECKED'; ?>/> 
															<label for="inc_appt_status">Appt Status</label>
														</div>
													</div>                                                
													<div class="col-sm-12">
														<div class="checkbox checkbox-inline pointer">
															<input type="checkbox" name="inc_demographics" id="inc_demographics" value="1" <?php if ($filter_arr['inc_demographics'] == '1') echo 'CHECKED'; ?>/> 
															<label for="inc_demographics">Demographics</label>
														</div>
													</div>  
													<div class="col-sm-12">
														<div class="checkbox checkbox-inline pointer">
															<input type="checkbox" name="inc_insurance" id="inc_insurance" value="1" <?php if ($filter_arr['inc_insurance'] == '1') echo 'CHECKED'; ?>/> 
															<label for="inc_insurance">Insurance</label>
														</div>
													</div>  
													<div class="col-sm-12">
														<div class="checkbox checkbox-inline pointer">
															<input type="checkbox" name="inc_pt_documents" id="inc_pt_documents" value="1" <?php if ($filter_arr['inc_pt_documents'] == '1') echo 'CHECKED'; ?>/> 
															<label for="inc_pt_documents">Pt. Documents</label>
														</div>
													</div>  
													<div class="col-sm-12">
														<div class="checkbox checkbox-inline pointer">
															<input type="checkbox" name="inc_portal_key" id="inc_portal_key" value="1" <?php if ($filter_arr['inc_portal_key'] == '1') echo 'CHECKED'; ?>/> 
															<label for="inc_portal_key">Portal Key</label>
														</div>
													</div>  
													<div class="col-sm-12">
														<div class="checkbox checkbox-inline pointer">
															<input type="checkbox" name="inc_recalls" id="inc_recalls" value="1" <?php if ($filter_arr['inc_recalls'] == '1') echo 'CHECKED'; ?>/> 
															<label for="inc_recalls">Recalls</label>
														</div>
													</div>  
													<div class="col-sm-12">
														<div class="checkbox checkbox-inline pointer">
															<input type="checkbox" name="inc_appt_made" id="inc_appt_made" value="1" <?php if ($filter_arr['inc_appt_made'] == '1') echo 'CHECKED'; ?>/> 
															<label for="inc_appt_made">Appt. Made Info</label>
														</div>
													</div>
													<div class="col-sm-12">
														<div class="checkbox checkbox-inline pointer">
															<input type="checkbox" name="inc_pcp" id="inc_pcp" value="1" <?php if ($filter_arr['inc_pcp'] == '1') echo 'CHECKED'; ?>/> 
															<label for="inc_pcp">PCP</label>
														</div>
													</div>
													<div class="col-sm-12">
														<div class="checkbox checkbox-inline pointer">
															<input type="checkbox" name="inc_ref_phy" id="inc_ref_phy" value="1" <?php if ($filter_arr['inc_ref_phy'] == '1') echo 'CHECKED'; ?>/> 
															<label for="inc_ref_phy">Referral Dr.</label>
														</div>
													</div>		
												</div>
											</div>
										</div>
									</div>
									<div class="col-lg-2 col-md-6 col-sm-6" id="format">
										<div class="grpbox">
											<div class="head"><span>Format</span></div>
											<div class="clearfix"></div>
											<div class="tblBg">
												<div class="row">
													<div class="col-sm-12">
														<div class="checkbox checkbox-inline pointer">
															<input type="checkbox" name="output_view_only" id="output_view_only" value="1" <?php if ($filter_arr['output_view_only'] == '1') echo 'CHECKED'; ?>/> 
															<label for="output_view_only">View Only</label>
														</div>
													</div>

													<div class="col-sm-12">
														<div class="checkbox checkbox-inline pointer">
															<input type="checkbox" name="output_pdf" id="output_pdf" value="1" <?php if ($filter_arr['output_pdf'] == '1') echo 'CHECKED'; ?>/> 
															<label for="output_pdf">PDF</label>
														</div>
													</div>                                                
													<div class="col-sm-12">
														<div class="checkbox checkbox-inline pointer">
															<input type="checkbox" name="output_csv" id="output_csv" value="1" <?php if ($filter_arr['output_csv'] == '1') echo 'CHECKED'; ?>/> 
															<label for="output_csv">CSV</label>
														</div>
													</div>  
													<div class="col-sm-12">
														<div class="checkbox checkbox-inline pointer">
															<input type="checkbox" name="output_consult_letters" id="output_consult_letters" value="1" <?php if ($filter_arr['output_consult_letters'] == '1') echo 'CHECKED'; ?>/> 
															<label for="output_consult_letters">Consult Letters</label>
														</div>
													</div>  
													<div class="col-sm-12">
														<div class="checkbox checkbox-inline pointer">
															<input type="checkbox" name="output_face_sheet" id="output_face_sheet" value="1" <?php if ($filter_arr['output_face_sheet'] == '1') echo 'CHECKED'; ?>/> 
															<label for="output_face_sheet">Face Sheet</label>
														</div>
													</div>  
													<div class="col-sm-12">
														<div class="checkbox checkbox-inline pointer">
															<input type="checkbox" name="output_sx_planning_sheet" id="output_sx_planning_sheet" value="1" <?php if ($filter_arr['output_sx_planning_sheet'] == '1') echo 'CHECKED'; ?>/> 
															<label for="output_sx_planning_sheet">SX Planning Sheet</label>
														</div>
													</div>  
												</div>
											</div>
										</div>
									</div>                                      
                                </div>
							</form> 
						</div>	
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
<script>
var ar = [["add_new","Save","top.fmain.addReport();"],["reset_page","Cancel","top.fmain.reset_page();"]];
	top.btn_show("ADMN",ar);
	set_header_title('Scheduler');	
	show_loading_image('none');
	$(document).ready(function(){
		var elemVal = $('#disable').val();
		setDisabled(elemVal);
	});
</script>
<?php 
	require_once("../admin_footer.php");
?>
