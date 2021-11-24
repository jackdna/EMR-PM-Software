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
	$sql_query = imw_query("SELECT * FROM `custom_reports` WHERE id='".$temp_id."' and `delete_status` = 0");
	if(imw_num_rows($sql_query) > 0){
		$row = imw_fetch_assoc($sql_query);
		$dbtemp_id  = $row['id'];
		$dbtemp_name  = $row['template_name'];
		$default_report  = $row['default_report'];
		$dbreport_sub_type = $row['report_sub_type'];
		$dbtemplate_fields  = unserialize($row['template_fields']);
		
	}
	$filter_arr = array();
	foreach($dbtemplate_fields as $obj){
		$filter_arr[$obj['name']] = $obj['value'];
	}
}

// load data
$f_data = array();
$qry = "SELECT * FROM `custom_reports` WHERE report_type in ('practice_analytic') And `delete_status` = 0";	 
$sql = imw_query($qry);
$cnt = imw_num_rows($sql);
if( $cnt > 0 ) {
	while( $row = imw_fetch_assoc($sql) )
	{
		$row['report_sub_type'] = trim($row['report_sub_type']);
		$f_data[$row['default_report']][] = $row;	
	}
}

?>
<script type="text/javascript">
$(function(){
	
	$('body').on('change','#report_sub_type',function(){
		var _v = $(this).val();
		var _file = 'fin_filters_' + _v + '.php';
		$("#filters_div").load(_file,function(response){
			$(this).html(response);
		});
	});
	
});
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
		
		//Analytics
		var app_arr = new Array;
		$('#analytic_filter').find('[type=checkbox]').each(function(id,elem){
			if($(elem).prop('checked') == true){
				app_arr.push($(elem).val());
			}
		});
		
		if(!app_arr.length && !prac_arr.length){
			msg4checkboes += '- Practice Filter <br />';
			msg4checkboes += '- Analytic Filter <br />';
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
		
		//If any one is more than 0 
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
		var reportSubType = $('#report_sub_type').val();
		var form_data = $('#analytics_form').serializeArray();
		var edit_id = $('#edit_id').val();
		$.ajax({
			type: 'POST',
			url: 'pracAnalyticsAjax.php',
			data: {ajaxReq:'report_data', templateName:templateName, form_data:form_data,edit_id:edit_id},
			success : function(data) {
				top.alert_notification_show(data);
				window.location.reload();
			}
		});
	}
	
	function retrive_data(id,default_report){
		$('#edit_id').val(id);
		$('#disable').val(default_report);
		document.analytics_form.submit();
	}
	
	function delTemplate(del_id){
		$.ajax({
			type: 'POST',
			url: 'pracAnalyticsAjax.php',
			data: {ajaxReq:'del_template', del_id:del_id},
			success : function(data) {
				top.alert_notification_show(data);
				window.location.reload();
			}
		});
	}
	
	function reset_page(){
		$('#edit_id').val('');
		window.location.href = top.JS_WEB_ROOT_PATH+"/interface/admin/reports/prac_analytics.php";
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
				<div class="savedtem"><h2>Practice Analytics </h2></div>
				<div class="col-sm-12">
					<div class="tab-content">
						<div class="tab-pane fade in active selection_box" id="temp_data">
							<div class="row">
								<div class="panel-group" id="auto_resp_temp">
									<div class="panel panel-default">
                  	<ul class="list-group">    
                      <?php 
												$default_reports = $f_data['1'];
												if( is_array($default_reports) && count($default_reports) > 0 )
												{
													foreach( $default_reports as $data)
													{
														
														$id = $data['id'];
														$temp_name = $data['template_name'];
														$default_report = $data['default_report'];
														$del_link = "";
														$href = '<span class="pointer" onClick="retrive_data('.$id.','.$default_report.')">'.$temp_name.'</span>';
														if(!$default_report == 1){
															$del_link = '<span class="glyphicon glyphicon-remove pull-right pointer" onclick="delTemplate('.$id.')"></span>';
														}
														$temp_names .= '<li class="list-group-item pd5 pdl_10">'.$href.''.$del_link.'</li>';
													}
												}
												
												$practice_reports = $f_data['0'];
												if( is_array($practice_reports) && count($practice_reports) > 0 )
												{
													foreach( $practice_reports as $data)
													{
														$id = $data['id'];
														$default_report = $data['default_report'];
														$temp_name = $data['template_name'];
														//ml10
														$href = '<span class=" pointer" onClick="retrive_data('.$id.','.$default_report.')">'.$temp_name.'</span>';
														$del_link = '<span class="glyphicon glyphicon-remove pull-right pointer" onclick="delTemplate('.$id.')"></span>';
														$temp_names .= '<li class="list-group-item pd5 pdl_10 ">'.$href.''.$del_link.'</li>';
															
														
													}
												}
												
												echo $temp_names;
												
											?>
                  	</ul>    
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
					<span><?php echo ($temp_id ? 'Edit' : 'New');?> Report Template</span>	
				</div>
				<form name="analytics_form" id="analytics_form" method="post">
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
            
            <div class="col-sm-12 pt10" id="filters_div">
            	<div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6" id="practice_filter">
                  <div class="grpbox">
                    <div class="head"><span>Practice Filter</span></div>
                    <div class="clearfix"></div>
                    <div class="tblBg">
                      <div class="row">
                        <div class="col-sm-6">
                          <div class="checkbox checkbox-inline pointer">
                            <input type="checkbox" name="groups" id="groups" value="1" <?php if ($filter_arr['groups'] == '1') echo 'CHECKED'; ?>/>
                            <label for="groups">Groups</label>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="checkbox checkbox-inline pointer">
                            <input type="checkbox" name="facility" id="facility" value="1" <?php if ($filter_arr['facility'] == '1') echo 'CHECKED'; ?>/>
                            <label for="facility">Facility</label>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="checkbox checkbox-inline pointer">
                            <input type="checkbox" name="physician" id="physician" value="1" <?php if ($filter_arr['physician'] == '1') echo 'CHECKED'; ?>/>
                            <label for="physician">Physician</label>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="checkbox checkbox-inline pointer">
                            <input type="checkbox" name="operators" id="operators" value="1" <?php if ($filter_arr['operators'] == '1') echo 'CHECKED'; ?>/>
                            <label for="operators">Operators</label>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="checkbox checkbox-inline pointer">
                            <input type="checkbox" name="date_range" id="date_range" value="1" <?php if ($filter_arr['date_range'] == '1') echo 'CHECKED'; ?>/>
                            <label for="date_range">Date Range</label>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="checkbox checkbox-inline pointer">
                            <input type="checkbox" name="summary_detail" id="summary_detail" value="1" <?php if ($filter_arr['summary_detail'] == '1') echo 'CHECKED'; ?>/>
                            <label for="summary_detail">Summary/Detail</label>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6" id="analytic_filter">
                  <div class="grpbox">
                    <div class="head"><span>Analytic Filter</span></div>
                    <div class="clearfix"></div>
                    <div class="tblBg">
                      <div class="row">
                        <div class="col-sm-6">
                          <div class="checkbox checkbox-inline pointer">
                            <input type="checkbox" name="heard_about" id="heard_about" value="1" <?php if ($filter_arr['heard_about'] == '1') echo 'CHECKED'; ?>/>
                            <label for="heard_about">Heard About</label>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="checkbox checkbox-inline pointer">
                            <input type="checkbox" name="new_patient" id="new_patient" value="1" <?php if ($filter_arr['new_patient'] == '1') echo 'CHECKED'; ?>/>
                            <label for="new_patient">New Patient</label>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="checkbox checkbox-inline pointer">
                            <input type="checkbox" name="lost_patient" id="lost_patient" value="1" <?php if ($filter_arr['lost_patient'] == '1') echo 'CHECKED'; ?>/>
                            <label for="lost_patient">Lost Patient</label>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="checkbox checkbox-inline pointer">
                            <input type="checkbox" name="recall_fulfilment" id="recall_fulfilment" value="1" <?php if ($filter_arr['recall_fulfilment'] == '1') echo 'CHECKED'; ?>/>
                            <label for="recall_fulfilment">Recall Fulfillment</label>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="checkbox checkbox-inline pointer">
                            <input type="checkbox" name="vip" id="vip" value="1" <?php if ($filter_arr['vip'] == '1') echo 'CHECKED'; ?>/>
                            <label for="vip">VIP</label>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="checkbox checkbox-inline pointer">
                            <input type="checkbox" name="deferred" id="deferred" value="1" <?php if ($filter_arr['deferred'] == '1') echo 'CHECKED'; ?>/>
                            <label for="deferred">Deferred</label>
                          </div>
                        </div>
                        
                        <div class="col-sm-6">
                          <div class="checkbox checkbox-inline pointer">
                            <input type="checkbox" name="utilization" id="utilization" value="1" <?php if ($filter_arr['utilization'] == '1') echo 'CHECKED'; ?>/>
                            <label for="utilization">Utilization</label>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="checkbox checkbox-inline pointer">
                            <input type="checkbox" name="audit" id="audit" value="1" <?php if ($filter_arr['audit'] == '1') echo 'CHECKED'; ?>/>
                            <label for="audit">Audit</label>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="checkbox checkbox-inline pointer">
                            <input type="checkbox" name="unfinalized_charts" id="unfinalized_charts" value="1" <?php if ($filter_arr['unfinalized_charts'] == '1') echo 'CHECKED'; ?>/>
                            <label for="unfinalized_charts">Unfinalized Charts</label>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="checkbox checkbox-inline pointer">
                            <input type="checkbox" name="unfinalized_tests" id="unfinalized_tests" value="1" <?php if ($filter_arr['unfinalized_tests'] == '1') echo 'CHECKED'; ?>/>
                            <label for="unfinalized_tests">Unfinalized Tests</label>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="checkbox checkbox-inline pointer">
                            <input type="checkbox" name="flow_analysis" id="flow_analysis" value="1" <?php if ($filter_arr['flow_analysis'] == '1') echo 'CHECKED'; ?>/>
                            <label for="flow_analysis">Patient Flow Analysis</label>
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
                            <input type="checkbox" name="inc_acc_details" id="inc_acc_details" value="1" <?php if ($filter_arr['inc_acc_details'] == '1') echo 'CHECKED'; ?>/>
                            <label for="inc_acc_details">Account Details</label>
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
                            <input type="checkbox" name="inc_ref_physician" id="inc_ref_physician" value="1" <?php if ($filter_arr['inc_ref_physician'] == '1') echo 'CHECKED'; ?>/>
                            <label for="inc_ref_physician">Referring Physician</label>
                          </div>
                        </div>
                        
                        
                        <div class="col-sm-12">
                          <div class="checkbox checkbox-inline pointer">
                            <input type="checkbox" name="inc_contact_lens" id="inc_contact_lens" value="1" <?php if ($filter_arr['inc_contact_lens'] == '1') echo 'CHECKED'; ?>/>
                            <label for="inc_contact_lens">Contact Lens</label>
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
                            <input type="checkbox" name="output_actvity_summary" id="output_actvity_summary" value="1" <?php if ($filter_arr['output_actvity_summary'] == '1') echo 'CHECKED'; ?>/>
                            <label for="output_actvity_summary">Activity Summary</label>
                          </div>
                        </div>
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
                      </div>
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
<script>
	var ar = [["add_new","Save","top.fmain.addReport();"],["reset_page","Cancel","top.fmain.reset_page();"]];
	top.btn_show("ADMN",ar);
	set_header_title('Practice Analytics');	
	show_loading_image('none');
	$(document).ready(function(){
		var elemVal = $('#disable').val();
		setDisabled(elemVal);
	});
</script>
<?php 
	require_once("../admin_footer.php");
?>
