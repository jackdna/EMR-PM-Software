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

include_once($GLOBALS['srcdir']."/classes/medical_hx/vital_signs.class.php");
include_once($GLOBALS['srcdir']."/classes/CLSAlerts.php");
include_once($GLOBALS['srcdir']."/classes/audit_common_function.php");

$vs_obj = new Vital_Sign($medical->current_tab);

//Vocabulary Array
$arr_info_alert = $vs_obj->vital_vocabulary;
$patient_id = $_SESSION['patient'];

//--- Saving vital sign details
if(isset($_REQUEST['save_data']) && $_REQUEST['save_data'] == 'vs'){
	$save_status = $vs_obj->save_vs_details($_REQUEST);
	if(trim($save_status) != '' && $save_status > 0){
		?>
		<script>
			top.show_loading_image("show", 100);
			if(top.document.getElementById('medical_tab_change')) {
				if(top.document.getElementById('medical_tab_change').value!='yes') {
					top.alert_notification_show('<?php echo $arr_info_alert["save"];?>');
				}
				if(top.document.getElementById('medical_tab_change').value=='yes') {
					top.chkConfirmSave('yes','set');		
				}
				top.document.getElementById('medical_tab_change').value='';
			}
			top.fmain.location.href = top.JS_WEB_ROOT_PATH + '/interface/Medical_history/index.php?showpage=vs';
			top.show_loading_image("hide");
		</script>
		<?php
	}
}

//--- GET POLICY STATUS FOR AUDIT TRAIL ----
$policyStatus = (int)$_SESSION['AUDIT_POLICIES']['Patient_record_Created_Viewed_Updated'];

//--- GET VITAL SIGN FIELDS STATUS ---
$vital_dis_arr = $vs_obj->get_vs_field_status();

//--- SET COMMENT TEXT BOX TYPE SIZE ---
$comment_box_details = $vs_obj->get_comment_box_size();
$comment_size = $comment_box_details['comment_size'];
$heading_row_display = $comment_box_details['heading_row_display'];

//--- Pain array
$arrPain = $vs_obj->arrPain;

/*--REVIEWING (UNREVIEWED) VITAL SING IF LOGGED USER IS PHYSICIAN--*/
	if($_SESSION['logged_user_type']=='1'){
		$rvw_operator = $_SESSION["authId"];
		$vs_obj->review_logged_user_vs($rvw_operator);
	}

//--- GET VITAL SIGN DATA FOR A SINGLE PATIENT ---
$vs_pat_detail = $vs_obj->get_single_pat_vs();

//--- GET VITAL SIGN DETAILS -----
$detail_data_arr = $vs_obj->get_vs_details();

//--- MERGE VITAL SIGN DATA ----
$vsDisplayDataArr = $vs_obj->merge_vital_sign_arr($vs_pat_detail,$detail_data_arr);


if(is_array($arr_info_alert) && count($arr_info_alert) > 0){
	$ARR_INFO_ALERT =  $arr_info_alert;
	$ARR_INFO_ALERT_SERIALIZED = urlencode(serialize($arr_info_alert));
}else{
	$ARR_INFO_ALERT =  '';
	$ARR_INFO_ALERT_SERIALIZED = '';
}


//--- SET PAIN DROP DOWN FOR NEW ROW ---
$painDropData = '';
reset($arrPain);
foreach($arrPain as $key => $val){
	$sel = '';	
	if($key == '0'){
		$sel = 'selected';
	}
	$painDropData .= "<option value=\"$key\" $sel>$val</option>";
}

//--- AUDIT function skipped ---
$pkIdAuditTrail = '';
$pkIdAuditTrailID = '';
if(!empty($vs_pat_detail)) {
	foreach($vs_pat_detail as $key => $val) {
		//--- AUDIT TRAIL ID ---
		$pkIdAuditTrail .= $val['vital_id'].'-';
		if($pkIdAuditTrailID == ''){
			$pkIdAuditTrailID = $val['vital_id'];
		}
	}
}

if($policyStatus == 1 and $pkIdAuditTrailID != '' and isset($_SESSION['Patient_Viewed']) === true){	
	$opreaterId = $_SESSION["authId"];												 
	$ip = getRealIpAddr();
	$URL = $_SERVER['PHP_SELF'];													 
	//$os = get_os_($_SERVER['HTTP_USER_AGENT']);
	$os = getOS();
	$browserInfoArr = array();
	$browserInfoArr = _browser();
	$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
	$browserName = str_replace(";","",$browserInfo);	 
	$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);
	$arrAuditTrailView_VS = array();
	$arrAuditTrailView_VS[0]['Pk_Id'] = $pkIdAuditTrailID;
	$arrAuditTrailView_VS[0]['Table_Name'] = 'vital_sign_patient';
	$arrAuditTrailView_VS[0]['Action'] = 'view';
	$arrAuditTrailView_VS[0]['Operater_Id'] = $opreaterId;
	$arrAuditTrailView_VS[0]['Operater_Type'] = getOperaterType($opreaterId);
	$arrAuditTrailView_VS[0]['IP'] = $ip;
	$arrAuditTrailView_VS[0]['MAC_Address'] = $_REQUEST['macaddrs'];
	$arrAuditTrailView_VS[0]['URL'] = $URL;
	$arrAuditTrailView_VS[0]['Browser_Type'] = $browserName;
	$arrAuditTrailView_VS[0]['OS'] = $os;
	$arrAuditTrailView_VS[0]['Machine_Name'] = $machineName;
	$arrAuditTrailView_VS[0]['Category'] = 'patient_info-medical_history';
	$arrAuditTrailView_VS[0]['Filed_Label'] = 'Patient Vital Sign Data';
	$arrAuditTrailView_VS[0]['Category_Desc'] = 'VS';
	$arrAuditTrailView_VS[0]['Old_Value'] = $pkIdAuditTrail;
	$arrAuditTrailView_VS[0]['pid'] = $patient_id;

	$patientViewed = $_SESSION['Patient_Viewed'];
	if(is_array($patientViewed) && $patientViewed["Medical History"]["VS"] == 0){
		auditTrail($arrAuditTrailView_VS,$mergedArray,0,0,0);
		$patientViewed["Medical History"]["VS"] = 1;
		$_SESSION['Patient_Viewed'] = $patientViewed;
	}
}

//--- ALERTS BY RAVI MANTRA ---
if(trim($_SESSION['alertShowForThisSession']) != "Cancel"){
	echo $vs_obj->set_cls_alerts();
}

$bp_drop_down = '';
foreach($vs_obj->bp_data as $key => $val){
	$bp_drop_down .= '<option value="'.$val.'">'.$val.'</option>';
}

$bmi_drop_down = '';
foreach($vs_obj->bmi_data as $key => $val){
	$bmi_drop_down .= '<option value="'.$val.'">'.$val.'</option>';
}


//-- Js array which contains php values to be used in js file
$global_js_arr = array();
$global_js_arr['vital_dis_array'] = $vs_obj->get_vs_field_status();
$global_js_arr['current_date'] = date('m-d-Y');
$global_js_arr['pain_drop_data'] = $arrPain;
$global_js_arr['bp_drop_data'] = $vs_obj->bp_data;
$global_js_arr['bmi_drop_data'] = $vs_obj->bmi_data;
$js_arr = json_encode($global_js_arr);

// --- Js variable which contains Js array
?>
<script>
	var pain_dropdown = '';
	var global_js_arr = '<?php echo $js_arr; ?>';
	global_js_arr = $.parseJSON(global_js_arr);
	$.each(global_js_arr.pain_drop_data,function(id,val){
		var sel = '';
		if(id == 0){
			sel = 'selected';
		}
		pain_dropdown += '<option value="'+id+'" '+sel+'>'+val+'</option>';
	});
</script>
<script type="text/javascript" src="<?php echo $library_path;?>/amcharts/amcharts.js"></script>
<script type="text/javascript" src="<?php echo $library_path;?>/amcharts/serial.js"></script>
<script type="text/javascript" src="<?php echo $library_path;?>/js/general_health.js"></script>
<script src="<?php echo $library_path; ?>/js/med_vital_sign.js" type="text/javascript"></script>
<div class="">
		<!-- Graph show div -->
		<div class="common_wrapper">
			<div id="myModal" class="modal fade" role="dialog">
				<div class="modal-dialog modal-lg">
					<!-- Modal content-->
					<div class="modal-content">
						<div class="modal-header bg-primary">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title"></h4>
						</div>
						<div id="shw_graph_file" class="modal-body graphs"></div>
						<div id="module_buttons" class="modal-footer ad_modal_footer">
							<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="table-responsive">
			<form action="index.php?showpage=vs&save_data=vs" method="post" name="vs_form" id="vs_form">
				<input type="hidden" name="info_alert" id="info_alert" value="<?php echo $ARR_INFO_ALERT_SERIALIZED; ?>">
				<input type="hidden" name="curr_tab" id="curr_tab" value="<?php echo $medical->current_tab;?>">
				<input type="hidden" name="preObjBack" id="preObjBack" value="">
				<input type="hidden" name="next_tab" id="next_tab" value="">
				<input type="hidden" name="next_dir" id="next_dir" value="">
				<input type="hidden" name="buttons_to_show" id="buttons_to_show" value="">
				<table id="vs_table" class="table table-striped table-bordered table-condensed" style="overflow: hidden;">
					<?php if($heading_row_display != ''){ ?>
						<tr class="grythead valign-top">
							<td align="left" style="width:10%; padding-right:2px!important; padding-left:3px!important;">Date</td>
							<?php if($vital_dis_arr['BP_SYS'] == '' || $vital_dis_arr['BP_DIS'] == ''){ ?>
								<td align="left" style="width:10%; padding-right:2px!important; padding-left:3px!important;">
									<div class="pull-left">B/P - S/D</div>
                  <div class="pull-right">
									<img src="../../library/images/chart_flipped.png" class="pointer" alt="" align="right" onClick="top.fmain.graph_show_med_hx('1','<?php echo $patient_id; ?>');" data-toggle="tooltip" title="Vital Sign Flow Sheet" data-placement="right" />
									<img id="info_prob" src="../../library/images/info_button.png" class="pointer mlr5" align="right" onclick='javascript: var medInfoVsWin = window.open("https://apps.nlm.nih.gov/medlineplus/services/mpconnect.cfm?mainSearchCriteria.v.c=38341003&mainSearchCriteria.v.cs=2.16.840.1.113883.6.96&mainSearchCriteria.v.dn=&informationRecipient.languageCode.c=en","VsList","height=700,width=1000,top=50,left=50,scrollbars=yes");medInfoVsWin.focus();' data-toggle="tooltip" title="Info Button" data-placement="left" >
                  </div>
								</td>
							<?php }  if($vital_dis_arr['PULSE'] == ''){ ?>
								<td align="left" style="width:6%; padding-right:2px!important; padding-left:3px!important;">
									Pulse
									<div class="pull-right">
                  <img src="../../library/images/chart_flipped.png" class="pointer" alt="" align="right" onClick="top.fmain.graph_show_med_hx('3','<?php echo $patient_id; ?>');" data-toggle="tooltip" title="Vital Sign Flow Sheet" data-placement="right" />
                  </div>
								</td>	
							<?php } if($vital_dis_arr['RESP'] == ''){ ?>
								<td align="left" style="width:5%; padding-right:2px!important; padding-left:3px!important;">
									Resp
								</td>
							<?php } if($vital_dis_arr['O2SAT'] == ''){ ?>
								<td align="left" style="width:7%; padding-right:2px!important; padding-left:3px!important;">
									O2SAT
								</td>
							<?php } if($vital_dis_arr['TEMP'] == ''){ ?>
								<td align="left" style="width:7%; padding-right:2px!important; padding-left:3px!important;">
									Temp
									<div class="pull-right">
                  <img src="../../library/images/chart_flipped.png" class="pointer" alt="" align="right" onClick="top.fmain.graph_show_med_hx('6','<?php echo $patient_id; ?>');" data-toggle="tooltip" title="Vital Sign Flow Sheet" data-placement="right" />
                  </div>							
              	</td>
							<?php } ?>
								<td align="left" style="width:9%; padding-right:2px!important; padding-left:3px!important;">
									Comment
								</td>
							<?php	
							if($vital_dis_arr['HEIGHT'] == ''){ ?>
								<td align="left" style="width:9%; padding-right:2px!important; padding-left:3px!important;">
									Height
                  <div class="pull-right">	
									<img src="../../library/images/chart_flipped.png" class="pointer" alt="" align="right" onClick="top.fmain.graph_show_med_hx('7','<?php echo $patient_id; ?>');" data-toggle="tooltip" title="Vital Sign Flow Sheet" data-placement="right" />
                  </div>	
								</td>
							<?php } if($vital_dis_arr['WIEGHT'] == ''){ ?>
								<td align="left" style="width:8%;padding-right:2px!important; padding-left:3px!important;">
									Weight
                  <div class="pull-right">
									<img src="../../library/images/chart_flipped.png" class="pointer" alt="" align="right" onClick="top.fmain.graph_show_med_hx('8','<?php echo $patient_id; ?>');" data-toggle="tooltip" title="Vital Sign Flow Sheet" data-placement="right" />
                  </div>	
								</td>
							<?php } if($vital_dis_arr['BMI'] == ''){ ?>
								<td align="left" class="text-nowrap" style="width:6%; padding-right:2px!important; padding-left:3px!important;">
									BMI
                  <div class="pull-right">
									<img src="../../library/images/chart_flipped.png" class="pointer" alt="" align="right" onClick="top.fmain.graph_show_med_hx('9','<?php echo $patient_id; ?>');" data-toggle="tooltip" title="Vital Sign Flow Sheet" data-placement="right" />
                  </div>	
								</td>
							<?php } if($vital_dis_arr['PAIN'] == ''){ ?>
								<td align="left" style="width:7%; padding-right:2px!important; padding-left:3px!important;">
									Pain
								</td>
							<?php } ?>
								<td align="left" style="width:8%; padding-right:2px!important; padding-left:3px!important;">
									Comment	
								</td>
								<td align="left" style="padding-right:2px!important; padding-left:3px!important;">
									Inhale O2	
								</td>
								<td align="left" width="23"></td>
						</tr>
						<?php 
							$i = 1;
							foreach($vsDisplayDataArr as $obj){
								?>
								<tr id="tbl_vs_data_row_<?php echo $i; ?>" class="data_row_<?php echo $obj['vital_id']; ?>">
									<td>
										<div class="input-group">
											<input type="hidden" name="vital_main_id_<?php echo $i; ?>" value="<?php echo $obj['vital_id']; ?>">
											<input type="text" class="datepicker form-control" name="new_range_dat<?php echo $i; ?>" id="new_range_dat<?php echo $i; ?>" onKeyUp="top.fmain.chk_change('<?php echo $obj['date_vital']; ?>',this,event);" onChange="top.fmain.chk_change('<?php echo $obj['date_vital']; ?>',this,event);" value="<?php echo $obj['date_vital']; ?>" onBlur="checkdate(this);">
											<label for="new_range_dat<?php echo $i; ?>" class="input-group-addon">
												<span class="glyphicon glyphicon-calendar"></span>
											</label>	
										</div>
									</td>
									<?php 
										$j = 1;
										if($vital_dis_arr['BP_SYS'] == '' || $vital_dis_arr['BP_DIS'] == ''){
										?>
											<td>
												<div class="row">
													<div class="col-sm-5">
														<input type="text" maxlength="7" onChange="calculateBmi('<?php echo $i; ?>');" onBlur="isNumeric('<?php echo $i; ?>','<?php echo $j; ?>');" class="form-control" name="new_range<?php echo $j; ?>_<?php echo $i; ?>" id="new_range<?php echo $j; ?>_<?php echo $i; ?>" onKeyUp="top.fmain.chk_change('<?php echo $obj['VS_SYS']; ?>',this,event); " value="<?php echo $obj['VS_SYS']; ?>" style="<?php $obj['VS_SYS_CLR']; ?>">
														<input type="hidden" name="new_unit<?php echo $j; ?>_<?php echo $i; ?>" id="new_unit<?php echo $j; ?>_<?php echo $i; ?>" value="mmHg">	
													</div>
													<div class="col-sm-2 text-center">
														<?php $j++; ?>
														/	
													</div>	
													<div class="col-sm-5">
														<input type="text" maxlength="7" onChange="calculateBmi('<?php echo $i; ?>');" onBlur="isNumeric('<?php echo $i; ?>','<?php echo $j; ?>');" class="form-control" name="new_range<?php echo $j; ?>_<?php echo $i; ?>" id="new_range<?php echo $j; ?>_<?php echo $i; ?>" onKeyUp="top.fmain.chk_change('<?php echo $obj['VS_DIS'];?>',this,event);" value="<?php echo $obj['VS_DIS']; ?>" style="<?php echo $obj['VS_DIS_CLR']; ?>">
														<input type="hidden" name="new_unit<?php echo $j; ?>_<?php echo $i; ?>" id="new_unit<?php echo $j; ?>_<?php echo $i; ?>" value="mmHg">
													</div>
												</div>
											</td>
										<?php
										}
										$j++;
										if($vital_dis_arr['BP_SYS'] != '' && $vital_dis_arr['BP_DIS'] != ''){
											$j++;
										}
										if($vital_dis_arr['PULSE'] == ''){
										?>
											<td>
												 <input type="text" onChange="calculateBmi('<?php echo $i; ?>');"  onBlur="isNumeric('<?php echo $i; ?>','<?php echo $j; ?>');" class="form-control" name="new_range<?php echo $j; ?>_<?php echo $i; ?>" id="new_range<?php echo $j; ?>_<?php echo $i; ?>" onKeyUp="top.fmain.chk_change('<?php echo $obj['VS_PULSE']; ?>',this,event);" value="<?php echo $obj['VS_PULSE']; ?>" style="<?php echo $obj['VS_PULSE_CLR']; ?>">
												<input type="hidden" name="new_unit<?php echo $j; ?>_<?php echo $i; ?>" id="new_unit<?php echo $j; ?>_<?php echo $i; ?>" value="beats/minute">                 	
											</td>
										<?php
										}
										$j++;

										if($vital_dis_arr['RESP'] == ''){
										?>
											<td>
												<input type="text" onChange="calculateBmi('<?php echo $i; ?>');" onBlur="isNumeric('<?php echo $i; ?>','<?php echo $j; ?>');" class="form-control" name="new_range<?php echo $j; ?>_<?php echo $i; ?>" id="new_range<?php echo $j; ?>_<?php echo $i; ?>" onKeyUp="top.fmain.chk_change('<?php echo $obj['VS_RESP']; ?>',this,event);" value="<?php echo $obj['VS_RESP']; ?>" style="<?php echo $obj['VS_RESP_CLR']; ?>">
												<input type="hidden" name="new_unit<?php echo $j; ?>_<?php echo $i; ?>" id="new_unit<?php echo $j; ?>_<?php echo $i; ?>" value="breaths/minute">            	
											</td>
										<?php
										}
										$j++;

										if($vital_dis_arr['O2SAT'] == ''){
										?>
											<td>
												<div class="input-group">
													<input type="text" onChange="calculateBmi('<?php echo $i; ?>');" onBlur="isNumeric('<?php echo $i; ?>','<?php echo $j; ?>');" class="form-control" name="new_range<?php echo $j; ?>_<?php echo $i; ?>" id="new_range<?php echo $j; ?>_<?php echo $i; ?>" onKeyUp="top.fmain.chk_change('<?php echo $obj['VS_O2SAT']; ?>',this,event);" value="<?php echo $obj['VS_O2SAT']; ?>" style="<?php echo $obj['VS_O2SAT_CLR']; ?>">
													<label for="new_range<?php echo $j; ?>_<?php echo $i; ?>" class="input-group-addon">
														<span><strong>%</strong></span>
													</label>	
												</div>
												<input type="hidden" name="new_unit<?php echo $j; ?>_<?php echo $i; ?>" id="new_unit<?php echo $j; ?>_<?php echo $i; ?>" value="ml/l">       	
											</td>
										<?php
										}
										$j++;

										if($vital_dis_arr['TEMP'] == ''){
										?>
											<td>
												<div class="row">
													<div class="col-sm-6">
														<input type="text" onChange="calculateBmi('<?php echo $i; ?>');" onBlur="isNumeric('<?php echo $i; ?>','<?php echo $j; ?>');" style="<?php echo $obj['VS_TEMP_CLR']; ?>;"  class="form-control" name="new_range<?php echo $j; ?>_<?php echo $i; ?>" id="new_range<?php echo $j; ?>_<?php echo $i; ?>" onKeyUp="top.fmain.chk_change('<?php echo $obj['VS_TEMP']; ?>',this,event);"  value="<?php echo $obj['VS_TEMP']; ?>" >
													</div>
													<div class="col-sm-6">
                          	<div class="row">
														<select name="new_unit<?php echo $j; ?>_<?php echo $i; ?>" id="new_unit<?php echo $j; ?>_<?php echo $i; ?>" onChange="top.fmain.chk_change('',this,event);" class="minimal form-control">
															<option value="°f" <?php echo $obj['VS_TEMP_F']; ?>>&deg;f</option> 
															<option value="°c" <?php echo $obj['VS_TEMP_C']; ?>>&deg;c</option> 
														</select>
                            </div>
													</div>	
												</div>
											</td>
										<?php
										}
										// BP dropdown
										?>
											<td>
												<select name="new_bp_type<?php echo $i; ?>" id="new_bp_type<?php echo $i; ?>" onChange="top.fmain.chkChange('',this,event);" class="form-control minimal selecicon">
													<?php echo $obj['bp_type']; ?>
												</select>
											</td>
										<?php
										$j++;	
										if($vital_dis_arr['HEIGHT'] == ''){
										?>
											<td>
												<div class="row">
													<div class="col-sm-6">
                          	<input type="text" onChange="calculateBmi('<?php echo $i; ?>');" onBlur="isNumeric('<?php echo $i; ?>','<?php echo $j; ?>');" style="<?php echo $obj['VS_HEIGHT_CLR']; ?>" class="form-control" name="new_range<?php echo $j; ?>_<?php echo $i; ?>" id="new_range<?php echo $j; ?>_<?php echo $i; ?>" onKeyUp="top.fmain.chk_change('<?php echo $obj['VS_HEIGHT']; ?>',this,event);" value="<?php echo $obj['VS_HEIGHT']; ?>" > 
                         	</div>
													<div class="col-sm-6">
                          	<div class="row">
														<select name="new_unit<?php echo $j; ?>_<?php echo $i; ?>" onChange="top.fmain.chk_change('',this,event);  convert_height_weight('<?php echo $i; ?>',this.value);calculateBmi('<?php echo $i; ?>');" onclick="update_old_unit(this.value)" onfocus="update_old_unit(this.value)" id="new_unit<?php echo $j; ?>_<?php echo $i; ?>" class="minimal form-control">
															<option value="inch" <?php echo $obj['VS_HEIGHT_INCH']; ?>>inch</option>
															<option value="m" <?php echo $obj['VS_HEIGHT_M']; ?> >m</option>
															<option value="cm" <?php echo $obj['VS_HEIGHT_CM']; ?>>cm</option>
														</select>
                            </div>  
													</div>	
												</div>
											</td>
										<?php
										}
										$j++;	
											
										if($vital_dis_arr['WIEGHT'] == ''){
										?>
											<td>
												<div class="row">
													<div class="col-sm-6">
                          
														<input type="text" onChange="calculateBmi('<?php echo $i; ?>'); " onBlur="isNumeric('<?php echo $i; ?>','<?php echo $j; ?>');" style="<?php echo $obj['VS_WIEGHT_CLR']; ?>" class="form-control" name="new_range<?php echo $j; ?>_<?php echo $i; ?>" id="new_range<?php echo $j; ?>_<?php echo $i; ?>" onKeyUp="top.fmain.chk_change('<?php echo $obj['VS_WIEGHT']; ?>',this,event);"   value="<?php echo $obj['VS_WIEGHT']; ?>"> 
													</div>
													<div class="col-sm-6">
                          	<div class="row">
														<select name="new_unit<?php echo $j; ?>_<?php echo $i; ?>" onChange="top.fmain.chk_change('',this,event); convert_height_weight('<?php echo $i; ?>',this.value); calculateBmi('<?php echo $i; ?>');" onclick="update_old_unit(this.value)" onfocus="update_old_unit(this.value)" id="new_unit<?php echo $j; ?>_<?php echo $i; ?>" class="minimal form-control">
															<option value="lbs" <?php echo $obj['VS_WIEGHT_LBS']; ?>>lbs</option>
															<option value="kg" <?php echo $obj['VS_WIEGHT_KG']; ?>>kg</option>  
														</select>
                            </div>     
													</div>	
												</div>
											</td>
										<?php
										}
										$j++;

										if($vital_dis_arr['BMI'] == ''){
										?>
											<td>
												<input type="text" onChange="calculateBmi('<?php echo $i; ?>');" class="form-control" name="new_range<?php echo $j; ?>_<?php echo $i; ?>" id="new_range<?php echo $j; ?>_<?php echo $i; ?>" onKeyUp="top.fmain.chk_change('<?php echo $obj['VS_BMI']; ?>',this,event);" style="<?php echo $obj['VS_BMI_CLR']; ?>" value="<?php echo $obj['VS_BMI']; ?>">
												<input type="hidden" name="new_unit<?php echo $j; ?>_<?php echo $i; ?>" id="new_unit<?php echo $j; ?>_<?php echo $i; ?>" value="kg/sqr. m">
											</td>
										<?php
										}
										$j++;	
										
										if($vital_dis_arr['PAIN'] == ''){
										?>
											<td>
												<select name="new_range<?php echo $j; ?>_<?php echo $i; ?>" id="new_range<?php echo $j; ?>_<?php echo $i; ?>" onChange="top.fmain.chk_change('',this,event);" style="<?php echo $obj['VS_PAIN_CLR']; ?>" class="minimal form-control">
													<?php echo $obj['VS_PAIN']; ?>
												</select>
											</td>
										<?php
										}
										$j++;		
									?>
										<td>
											<select name="new_comment<?php echo $i; ?>" id="new_comment<?php echo $i; ?>" onChange="top.fmain.chkChange('',this,event);" class="minimal form-control selecicon">
												<?php echo $obj['comment']; ?>
											</select>
										</td>
										<td>
											<input type="text" class="form-control" name="inhale_O2<?php echo $i; ?>" id="inhale_O2<?php echo $i; ?>" value="<?php echo $obj['inhale_O2']; ?>">
										</td>
										<td>
											<span class="glyphicon glyphicon-remove pointer" title="Delete" onclick="javascript:top.fancyConfirm('<?php echo $arr_info_alert['delete']; ?>','Delete Record','top.fmain.removeTableRow(\'<?php echo $obj['vital_id']; ?>\',\'<?php echo $i; ?>\')');"></span>
										</td>
								</tr> 
								<?php
								$i++;
							} ?>
							<tr id="tbl_vs_data_row_<?php echo $i; ?>">
								<td>
									<div class="input-group">
										<input type="hidden" name="start_new_vs" value="1" >
										<input type="hidden" name="end_new_vs" value="<?php echo $i; ?>">
										<input type="text" class="datepicker form-control vs_dt" name="new_range_dat<?php echo $i; ?>" id="new_range_dat<?php echo $i; ?>" onKeyUp="top.fmain.chk_change(this.value,this,event);" onChange="top.fmain.chk_change(this.value,this,event);" value="<?php echo date('m-d-Y'); ?>" onBlur="checkdate(this);">
										<label for="new_range_dat<?php echo $i; ?>" class="input-group-addon">
											<span class="glyphicon glyphicon-calendar"></span>
										</label>	
									</div>
								</td>
								<?php 
									$j = 1;
									if($vital_dis_arr['BP_SYS'] == '' || $vital_dis_arr['BP_DIS'] == ''){
									?>
										<td>
											<div class="row">
												<div class="col-sm-5">
													<input type="text" maxlength="7" onChange="calculateBmi('<?php echo $i; ?>');" onBlur="isNumeric('<?php echo $i; ?>','<?php echo $j; ?>');" class="form-control" name="new_range<?php echo $j; ?>_<?php echo $i; ?>" id="new_range<?php echo $j; ?>_<?php echo $i; ?>" onKeyUp="top.fmain.chk_change('',this,event); " value="">
													<input type="hidden" name="new_unit<?php echo $j; ?>_<?php echo $i; ?>" id="new_unit<?php echo $j; ?>_<?php echo $i; ?>" value="mmHg">
												</div>
												<div class="col-sm-2 text-center" >
													<?php $j++; ?>
													/
												</div>
												<div class="col-sm-5">
													<input type="text" maxlength="7" onChange="calculateBmi('<?php echo $i; ?>');" onBlur="isNumeric('<?php echo $i; ?>','<?php echo $j; ?>');" class="form-control" name="new_range<?php echo $j; ?>_<?php echo $i; ?>" id="new_range<?php echo $j; ?>_<?php echo $i; ?>" onKeyUp="top.fmain.chk_change('',this,event); " value="">
													<input type="hidden" name="new_unit<?php echo $j; ?>_<?php echo $i; ?>" id="new_unit<?php echo $j; ?>_<?php echo $i; ?>" value="mmHg">
												</div>	
											</div>
										</td>
									<?php
									}
									$j++;
									if($vital_dis_arr['BP_SYS'] != '' && $vital_dis_arr['BP_DIS'] != ''){
										$j++;
									}
									
									if($vital_dis_arr['PULSE'] == ''){
									?>
										<td>
											 <input type="text" onChange="calculateBmi('<?php echo $i; ?>');"  onBlur="isNumeric('<?php echo $i; ?>','<?php echo $j; ?>');" class="form-control" name="new_range<?php echo $j; ?>_<?php echo $i; ?>" id="new_range<?php echo $j; ?>_<?php echo $i; ?>" onKeyUp="top.fmain.chk_change('',this,event);" value="" >
											<input type="hidden" name="new_unit<?php echo $j; ?>_<?php echo $i; ?>" id="new_unit<?php echo $j; ?>_<?php echo $i; ?>" value="beats/minute">                 	
										</td>
									<?php
									}
									$j++;

									if($vital_dis_arr['RESP'] == ''){
									?>
										<td>
											<input type="text" onChange="calculateBmi('<?php echo $i; ?>');" onBlur="isNumeric('<?php echo $i; ?>','<?php echo $j; ?>');" class="form-control" name="new_range<?php echo $j; ?>_<?php echo $i; ?>" id="new_range<?php echo $j; ?>_<?php echo $i; ?>" onKeyUp="top.fmain.chk_change('',this,event);" value="" >
											<input type="hidden" name="new_unit<?php echo $j; ?>_<?php echo $i; ?>" id="new_unit<?php echo $j; ?>_<?php echo $i; ?>" value="breaths/minute">            	
										</td>
									<?php
									}
									$j++;

									if($vital_dis_arr['O2SAT'] == ''){
									?>
										<td>
											<div class="input-group">
												<input type="text" onChange="calculateBmi('<?php echo $i; ?>');" onBlur="isNumeric('<?php echo $i; ?>','<?php echo $j; ?>');" class="form-control" name="new_range<?php echo $j; ?>_<?php echo $i; ?>" id="new_range<?php echo $j; ?>_<?php echo $i; ?>" onKeyUp="top.fmain.chk_change('',this,event);" value="">
												<label for="new_range<?php echo $j; ?>_<?php echo $i; ?>" class="input-group-addon">
													<span><strong>%</strong></span>
												</label>	
											</div>
											<input type="hidden" name="new_unit<?php echo $j; ?>_<?php echo $i; ?>" id="new_unit<?php echo $j; ?>_<?php echo $i; ?>" value="ml/l">       	
										</td>
									<?php
									}
									$j++;

									if($vital_dis_arr['TEMP'] == ''){
									?>
										<td>
											<div class="row">
												<div class="col-sm-6">
													<input type="text" onChange="calculateBmi('<?php echo $i; ?>');" onBlur="isNumeric('<?php echo $i; ?>','<?php echo $j; ?>');"  class="form-control" name="new_range<?php echo $j; ?>_<?php echo $i; ?>" id="new_range<?php echo $j; ?>_<?php echo $i; ?>" onKeyUp="top.fmain.chk_change('',this,event);"  value="">
												</div>
												<div class="col-sm-6">
                        	<div class="row">
													<select name="new_unit<?php echo $j; ?>_<?php echo $i; ?>" id="new_unit<?php echo $j; ?>_<?php echo $i; ?>" onChange="top.fmain.chk_change('',this,event);" class="minimal form-control">
														<option value="°f">&deg;f</option> 
														<option value="°c">&deg;c</option> 
													</select>
                          </div>
												</div>	
											</div>
										</td>
									<?php
									}
									$j++; ?>
									<td>
										<select name="new_bp_type<?php echo $i; ?>" id="new_bp_type<?php echo $i; ?>" onChange="top.fmain.chkChange('',this,event);" class="form-control minimal selecicon">
											<?php 
												foreach($vs_obj->bp_data as $key => $val){
													?>
													<option value="<?php echo $val; ?>"><?php echo $val; ?></option>
													<?php 
												}
											?>
										</select>
									</td>
									
									<?php
									if($vital_dis_arr['HEIGHT'] == ''){
									?>
										<td>
											<div class="row">
												<div class="col-sm-6">
                        	<input type="text" onChange="calculateBmi('<?php echo $i; ?>');" onBlur="isNumeric('<?php echo $i; ?>','<?php echo $j; ?>');" class="form-control" name="new_range<?php echo $j; ?>_<?php echo $i; ?>" id="new_range<?php echo $j; ?>_<?php echo $i; ?>" onKeyUp="top.fmain.chk_change('',this,event);" value="" > 
												</div>
												<div class="col-sm-6">
                        	<div class="row">
													<select name="new_unit<?php echo $j; ?>_<?php echo $i; ?>" onChange="top.fmain.chk_change('',this,event);  convert_height_weight('<?php echo $i; ?>',this.value);calculateBmi('<?php echo $i; ?>');" onclick="update_old_unit(this.value)" onfocus="update_old_unit(this.value)" id="new_unit<?php echo $j; ?>_<?php echo $i; ?>" class="minimal form-control">
														<option value="inch">inch</option>
														<option value="m">m</option>
														<option value="cm">cm</option>
													</select>
                          </div>  
												</div>	
											</div>
										</td>
									<?php
									}
									$j++;	
										
									if($vital_dis_arr['WIEGHT'] == ''){
									?>
										<td>
											<div class="row">
												<div class="col-sm-6">
													<input type="text" onChange="calculateBmi('<?php echo $i; ?>'); " onBlur="isNumeric('<?php echo $i; ?>','<?php echo $j; ?>');" class="form-control" name="new_range<?php echo $j; ?>_<?php echo $i; ?>" id="new_range<?php echo $j; ?>_<?php echo $i; ?>" onKeyUp="top.fmain.chk_change('',this,event);" value=""> 
												</div>
												<div class="col-sm-6">
                        	<div class="row">
													<select name="new_unit<?php echo $j; ?>_<?php echo $i; ?>" onChange="top.fmain.chk_change('',this,event); convert_height_weight('<?php echo $i; ?>',this.value); calculateBmi('<?php echo $i; ?>');" onclick="update_old_unit(this.value)" onfocus="update_old_unit(this.value)" id="new_unit<?php echo $j; ?>_<?php echo $i; ?>" class="minimal form-control">
														<option value="lbs">lbs</option>
														<option value="kg">kg</option>  
													</select> 
                          </div>    
												</div>	
											</div>
										</td>
									<?php
									}
									$j++;

									if($vital_dis_arr['BMI'] == ''){
									?>
										<td>
											<input type="text" onChange="calculateBmi('<?php echo $i; ?>');" class="form-control" name="new_range<?php echo $j; ?>_<?php echo $i; ?>" id="new_range<?php echo $j; ?>_<?php echo $i; ?>" onKeyUp="top.fmain.chk_change('',this,event);" value="">
											<input type="hidden" name="new_unit<?php echo $j; ?>_<?php echo $i; ?>" id="new_unit<?php echo $j; ?>_<?php echo $i; ?>" value="kg/sqr. m">
										</td>
									<?php
									}
									$j++;	
									
									if($vital_dis_arr['PAIN'] == ''){
									?>
										<td>
											<select name="new_range<?php echo $j; ?>_<?php echo $i; ?>" id="new_range<?php echo $j; ?>_<?php echo $i; ?>" onChange="top.fmain.chk_change('',this,event);" class="minimal form-control">
												<?php echo $painDropData; ?>
											</select>
										</td>
									<?php
									}
									$j++;		
								?>
								 <td>
									<select name="new_comment<?php echo $i; ?>"  name="new_comment<?php echo $i; ?>" onChange="top.fmain.chkChange('',this,event);" class="form-control minimal selecicon">
										<?php 
											foreach($vs_obj->bmi_data as $key => $val){
												?>
												<option value="<?php echo $val; ?>"><?php echo $val; ?></option>
												<?php
											}
										?>
									</select>
								</td>
								<td>
									<input type="text" class="form-control" name="inhale_O2<?php echo $i; ?>" id="inhale_O2<?php echo $i; ?>" value="">
								</td>
								<td>
									<span class="glyphicon glyphicon-plus pointer" title="Add More" id="add_row_<?php echo $i; ?>" onClick="show_next('<?php echo $i; ?>'); "></span>
								</td>
							</tr>
					<?php } ?>
				</table>	
				 <input type="hidden" name="last_cnt" id="last_cnt" value="<?php echo $i; ?>">
			</form>	
		</div>	
	</div>