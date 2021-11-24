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
		$dbreport_sub_type = $row['report_sub_type'];
		$dbtemplate_fields  = unserialize($row['template_fields']);
		
	}
	$filter_arr = array();
	foreach($dbtemplate_fields as $obj){
		$filter_arr[$obj['name']] = $obj['value'];
	}
}

$f_type = $temp_id ? $dbreport_sub_type : 'daily';
// load data
$f_data = array();
$sql = imw_query("SELECT * FROM `custom_reports` WHERE `report_type` = 'financial' and `delete_status` = 0"); 
$cnt = imw_num_rows($sql);
if( $cnt > 0 ) {
	while( $row = imw_fetch_assoc($sql) )
	{
		$row['report_sub_type'] = trim($row['report_sub_type']);
		if( $row['report_sub_type'] )
			$f_data[$row['default_report']][$row['report_sub_type']][] = $row;
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
		
		var report_sub_type=$('#report_sub_type').val();
		
		var msg = '';
		var msg4checkboes = "";
		var msg4GroupBycheckboes='';
		
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
			msg4GroupBycheckboes= '- Group By <br />';
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
		if(report_sub_type=='analytics'){
			if(!group_arr.length){
				msg4GroupBycheckboes = '<b>Please select atlest one checkbox from:-</b><br>'+msg4GroupBycheckboes;
				fAlert(msg4GroupBycheckboes);
				return false;
			}
		}
		
		if(prac_arr.length || app_arr.length || group_arr.length || include_arr.length || format_arr.length){
			save_custom_report_data();
		}else{
			msg4checkboes = '<b>Please select atlest one checkbox from:-</b><br>'+msg4checkboes;
			fAlert(msg4checkboes);
			return false;
		}
		//save_custom_report_data();
	}
	function save_custom_report_data(){
		var templateName = $('#templateName').val();
		var reportSubType = $('#report_sub_type').val();
		var form_data = $('#financial_form').serializeArray();
		var edit_id = $('#edit_id').val();
		$.ajax({
			type: 'POST',
			url: 'financialAjax.php',
			data: {ajaxReq:'report_data', templateName:templateName, reportSubType:reportSubType, form_data:form_data,edit_id:edit_id},
			success : function(data) {
				top.alert_notification_show(data);
				window.location.reload();
			}
		});
	}
	
	function retrive_data(id,default_report){
		$('#edit_id').val(id);
		$('#disable').val(default_report);
		document.financial_form.submit();
	}
	
	function delTemplate(del_id){
		$.ajax({
			type: 'POST',
			url: 'financialAjax.php',
			data: {ajaxReq:'del_template', del_id:del_id},
			success : function(data) {
				top.alert_notification_show(data);
				window.location.reload();
			}
		});
	}
	
	function reset_page(){
		$('#edit_id').val('');
		window.location.href = top.JS_WEB_ROOT_PATH+"/interface/admin/reports/financials.php";
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
				<div class="savedtem"><h2>Financial templates </h2></div>
				<div class="col-sm-12">
					<div class="tab-content">
						<div class="tab-pane fade in active selection_box" id="temp_data">
							<div class="row">
								<div class="panel-group" id="auto_resp_temp">
									<div class="panel panel-default">
										<ul class="list-group">    
										<?php 
												$default_reports = $f_data['1'];
												$practice_reports = $f_data['0'];
												
												if( is_array($default_reports) && count($default_reports) > 0 ){
													
													foreach( $default_reports as $sub_type => $data)
													{
														if(isset($practice_reports[$sub_type]) && count($practice_reports[$sub_type]) > 0){
															foreach($practice_reports[$sub_type] as &$objVal){
																$data[] = $objVal;
															}
														}
														
														$temp_names .='<li class="list-group-item f-bold">'.ucwords(str_replace("_", " ", $sub_type)).'</li>';
														if( is_array($data) && count($data) > 0)
														{
															foreach( $data as $data_row)
															{
																$id = $data_row['id'];
																$temp_name = $data_row['template_name'];
																$default_report = $data_row['default_report'];
																$del_link = "";
																if(!$default_report == 1){
																	$del_link = '<span class="glyphicon glyphicon-remove pull-right pointer" onclick="delTemplate('.$id.')"></span>';
																}
																$href = '<span class="pointer" onClick="retrive_data('.$id.','.$default_report.')">'.$temp_name.'</span>';
																$temp_names .= '<li class="list-group-item pd5 pdl_10">'.$href.''.$del_link.'</li>';
															}
														}
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
				<form name="financial_form" id="financial_form" method="post">
					<input type="hidden" id="edit_id" name="edit_id" value="<?php echo $dbtemp_id; ?>">
					<input type="hidden" id="disable" name="disable" value="<?php echo $_REQUEST['disable']; ?>">
					<div class="tblBg">
						<div class="row">
							<div class="col-sm-12">
								<div class="row">
									<div class="col-sm-3">
										<label for="report_sub_type">Report Type</label>
										<select class="selectpicker" name="report_sub_type" id="report_sub_type" title="Select" data-header="Select" data-width="100%">
											<option value="daily" <?php echo ($f_type == 'daily' ? 'selected' : '');?> >Daily</option>
											<option value="analytics" <?php echo ($f_type == 'analytics' ? 'selected' : '');?>>Analytics</option>
											<option value="account_receivable" <?php echo ($f_type == 'account_receivable' ? 'selected' : '');?>>Account Receivable</option>
											<!--<option value="claims" <?php echo ($f_type == 'claims' ? 'selected' : '');?>>Claims</option> -->
										</select>
									</div>
									<div class="col-sm-9">
										<div class="form-group">
										<label>Template Name</label>
											<input class="form-control" value="<?php echo $dbtemp_name; ?>" id="templateName" type="text">	
										</div>	
									</div>    
								</div>
							</div>
							<div class="col-sm-12 pt10" id="filters_div">
							<?php include 'fin_filters_'.$f_type.'.php'; ?>
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
	set_header_title('Financial');	
	show_loading_image('none');
	$(document).ready(function(){
		var elemVal = $('#disable').val();
		setDisabled(elemVal);
	});
</script>
<?php 
	require_once("../admin_footer.php");
?>
