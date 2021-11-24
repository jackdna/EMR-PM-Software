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

include_once($GLOBALS['srcdir']."/classes/medical_hx/immunization.class.php");
include_once($GLOBALS['srcdir']."/classes/CLSAlerts.php");
include_once($GLOBALS['srcdir']."/classes/audit_common_function.php");

//Immunization Object
$imm_obj = New Immunization($medical->current_tab);

//CLS common function object
global $cls_common;

//--- Saving Immunization Data
if(isset($_REQUEST['save_action']) && $_REQUEST['save_action'] == 'save_data'){
	$save_status = $imm_obj->save_immunizations($_REQUEST);
}

//--- Get Immunization data and registry info ---
$imm_data = $imm_obj->get_immunization_data($_REQUEST);
extract($imm_data);
#----------------------------#
# Audit trail for view only  #
#----------------------------#
if($policyStatus == 1 and isset($_SESSION['Patient_Viewed']) === true and $pkIdAuditTrailID != ''){
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
	
	$arrAuditTrailView_Immunizations = array();
	$arrAuditTrailView_Immunizations[0]['Pk_Id'] = $pkIdAuditTrailID;
	$arrAuditTrailView_Immunizations[0]['Table_Name'] = 'immunizations';
	$arrAuditTrailView_Immunizations[0]['Action'] = 'view';
	$arrAuditTrailView_Immunizations[0]['Operater_Id'] = $opreaterId;
	$arrAuditTrailView_Immunizations[0]['Operater_Type'] = getOperaterType($opreaterId);
	$arrAuditTrailView_Immunizations[0]['IP'] = $ip;
	$arrAuditTrailView_Immunizations[0]['MAC_Address'] = $_REQUEST['macaddrs'];
	$arrAuditTrailView_Immunizations[0]['URL'] = $URL;
	$arrAuditTrailView_Immunizations[0]['Browser_Type'] = $browserName;
	$arrAuditTrailView_Immunizations[0]['OS'] = $os;
	$arrAuditTrailView_Immunizations[0]['Machine_Name'] = $machineName;
	$arrAuditTrailView_Immunizations[0]['Category'] = 'patient_info-medical_history';
	$arrAuditTrailView_Immunizations[0]['Filed_Label'] = 'Patient Immunizations Data';
	$arrAuditTrailView_Immunizations[0]['Category_Desc'] = 'Immunizations';
	$arrAuditTrailView_Immunizations[0]['Old_Value'] = $pkIdAuditTrail;
	$arrAuditTrailView_Immunizations[0]['pid'] = $_SESSION['patient'];

	$patientViewed = $_SESSION['Patient_Viewed'];
	if($patientViewed["Medical History"]["Immunizations"] == 0){
		auditTrail($arrAuditTrailView_Immunizations,$mergedArray,0,0,0);
		$patientViewed["Medical History"]["Immunizations"] = 1;
		$_SESSION['Patient_Viewed'] = $patientViewed;
	}
}

//--- SET SEARCH BY DROP DOWN --
$search_by_select = $_REQUEST['searchby'];

//--- SET PROVIDER DROP DOWN ---
$provider_val = $cls_common->drop_down_providers('','','');

//CLS ALerts
echo $imm_obj->set_cls_alerts($_REQUEST['nxtFreqSave']);


//Contains variables to be used in JS file
$global_js_vars = array();
$global_js_vars['routest_code_arr'] = $routest_code_str;
$global_js_vars['bodysite_codes_arr'] = $bodysite_codes_str;
$global_js_vars['vfc_codes_arr'] = $vfc_codes_str;
$global_js_vars['nip1_txt_arr'] = $nip1_txt_str;
$global_js_vars['nip2_txt_arr'] = $nip2_txt_str;
$global_js_vars['manufacture_txt_arr'] = $manufacture_txt_str;
$global_js_vars['arr_info_alert'] = $arr_info_alert;
$global_js_variable = json_encode($global_js_vars);
//JS variable containing php variables
?>
	<script>
		var global_js_var = '<?php echo $global_js_variable; ?>';
		global_js_var = $.parseJSON(global_js_var);
	</script>
	
<!-- Html -->
<head>
	<script src="<?php echo $library_path; ?>/js/med_immunization.js" type="text/javascript"></script>
</head>
<div>
	<!-- Immunization registry info modal -->
		<div class="commom_wrapper">
			<div id="div_imm_reg_info" class="modal" role="dialog">
				<div class="modal-dialog modal-lg">
					<!-- Modal content-->
					<div class="modal-content">
						<div class="modal-header bg-primary">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title">Immunization Registry Information</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-sm-4">
									<label>Imm. Registry Status</label>
									<input type="text" class="form-control" name="reg_status" id="reg_status" value="<?php echo $reg_status; ?>">	
								</div>	
								<div class="col-sm-4">
									<label>Publicity Code</label>
									<select class="selectpicker" name="publicity_code" id="publicity_code" data-width="100%" data-title="Select">
										<?php echo $publicityOptions; ?>
									</select>
								</div>	
								<div class="col-sm-4">
									<label>Protection Indicator</label>
									<input type="text" class="form-control" name="protection_indicator" id="protection_indicator" value="<?php echo $protection_indicator; ?>">	
								</div>
							</div>
							<div class="row pt10">
								<div class="col-sm-4">
									<label class="text_purple" <?php echo show_tooltip('Protection Indicator Effective Date','top'); ?>>Protection Indicator Eff. Date</label>
									<div class="input-group">
										<input type="text" class="datepicker form-control" onBlur="checkdate(this)" name="indicator_eff_date" id="indicator_eff_date" value="<?php if($indicator_eff_date != '00-00-0000'){echo $indicator_eff_date;} ?>">
										<label for="indicator_eff_date" class="input-group-addon pointer">
											<span class="glyphicon glyphicon-calendar"></span>	
										</label>	
									</div>
								</div>	
								<div class="col-sm-4">
									<label class="text_purple" <?php echo show_tooltip('Publicity Code Effective Date','top'); ?>>Publicity Code Eff. Date</label>
									<div class="input-group">
										<input type="text" class="datepicker form-control" onBlur="checkdate(this)" name="publicity_code_eff_date" id="publicity_code_eff_date" value="<?php if($publicity_code_eff_date != '00-00-0000'){echo $publicity_code_eff_date;} ?>">
										<label for="publicity_code_eff_date" class="input-group-addon pointer">
											<span class="glyphicon glyphicon-calendar"></span>	
										</label>	
									</div>
								</div>
								<div class="col-sm-4">
									<label class="text_purple" <?php echo show_tooltip('Imm. Registry Status Effective Date','top'); ?>>Imm. Registry Status Eff. Date</label>
									<div class="input-group">
										<input type="text" class="datepicker form-control" onBlur="checkdate(this)" name="imm_reg_status_eff_date" id="imm_reg_status_eff_date" value="<?php if($imm_reg_status_eff_date != '00-00-0000'){echo $imm_reg_status_eff_date;} ?>">
										<label for="imm_reg_status_eff_date" class="input-group-addon pointer">
											<span class="glyphicon glyphicon-calendar"></span>	
										</label>	
									</div>
								</div>	
							</div>
						</div>
						<div class="modal-footer">
							<input type="hidden" name="reg_id" id="reg_id" value="<?php echo $reg_id; ?>">
							<input type="button" class="btn btn-success" name="btnImmRegSave" value="Save" onClick="javascript:saveImmRegInfo();">
							<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
						</div>
					</div>
				</div>
			</div>	
		</div>

		<!-- HL7 import/export div  -->
		<div id="lab_hl7_upload_div" class="modal hide" role="dialog">
			<div class="modal-dialog modal-lg">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header bg-primary">
						<button type="button" class="close" data-dismiss="modal" onClick="close_lab_order();">×</button>
						<h4 class="modal-title" id="modal_title">UPLOAD LAB DATA HL7</h4>
					</div>
					<div class="div_shadow_container">
						<iframe id="lab_im_ex_iframe" name="lab_im_ex_iframe" style="border:0px; margin:0px; width:100%; height:<?php echo $_SESSION['wn_height']-520; ?>px;" src="about:blank"></iframe>
					</div>
					<div class="clearfix"></div>
					<div class="modal-footer pd0 panel-footer">
						<div class="row">
							<div class="col-sm-12 text-center pt5 pdb5" id="module_buttons">
								<button type="button" class="btn btn-danger" data-dismiss="modal" onClick="close_lab_order();">Close</button>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
		
	<?php $i = 1; ?>
	<form action="index.php?showpage=immunizations&save_action=save_data" method="post" name="immunizations_form" id="immunizations_form">
		<input type="hidden" name="curr_tab" id="curr_tab" value="<?php echo $imm_obj->current_tab;?>">
		<input type="hidden" name="info_alert" id="info_alert" value="<?php echo $ARR_INFO_ALERT_SERIALIZED; ?>">
		<input type="hidden" value="<?php echo $arrCVXCodes_json; ?>">
		<input type="hidden" name="preObjBack" id="preObjBack" value="">
		<input type="hidden" name="next_tab" id="next_tab" value="">
		<input type="hidden" name="next_dir" id="next_dir" value="">
		<input type="hidden" name="buttons_to_show" id="buttons_to_show" value="">
		<input type="hidden" name="hidImmIdVizChange" id="hidImmIdVizChange" value="">
		<!-- Header -->
		<div class="oculartop">
			<div class="row">
				<div class="col-lg-3 col-sm-4">
					<div class="eyetst eyetstname medicat">
						<figure>
							<div class="checkbox checkbox-inline">
								<input type="checkbox" style="cursor:pointer;" name="commonNoImmunizations" id="commonNoImmunizations" value="NoImmunizations" onClick="top.fmain.chk_change('<?php if($checkImmunizations == 'checked'){echo $checkImmunizations;}?>',this,event);" <?php echo $is_disable.' '.$checkImmunizations; ?>>
								<label for="commonNoImmunizations">
									<span>No Immunizations</span>
								</label>	
							</div>
						</figure>
						<h2>&nbsp;</h2>
					</div>
				</div>
				<div class="col-lg-7 col-sm-5 text-center pt10">
					<a class="immregbtn" href="#" role="button" data-toggle="modal" data-target="#div_imm_reg_info">IMM. REGISTRY INFO.</a>
				</div>
				<div class="col-lg-2 col-sm-3">
					<div class="form-group allflter pt10">
						<div class="row">
							<div class="col-sm-6 text-right">
								<label for="searchby">FILTER :</label>
							</div>
							<div class="col-sm-6">
								<select name="searchby" id="searchby" class="selectpicker" data-width="100%" onChange="top.fmain.get_selected_imm_data()">
									<?php 
										$opt = '';
										foreach($searchByData as $key => $val){
											$sel = '';
											if($key == $search_by_select){
												$sel = 'selected';
											}
											$opt .= '<option value="'.$key.'" '.$sel.'>'.$val.'</option>';
										}
										echo $opt;
									?>
								</select>
							</div>	
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Imm. Rows -->
		<div class="immfrmgrp">
			<div class="row" id="immunization_table">
				<?php
					foreach($immunDataArr as $obj){
					?>
					<div id="immunization_row_<?php echo $i;?>" class="col-sm-12 whitebox bordbx">
						<input type="hidden" name="imnzn_main<?php echo $i; ?>" id="imnzn_main<?php echo $i; ?>" value="<?php echo $obj['imnzn_main_id']; ?>">
						<input type="hidden" name="im_name<?php echo $i; ?>" id="im_name<?php echo $i; ?>" value="<?php echo $obj['immunization_id']; ?>" />
						
						<!-- First row -->
						<div class="row">
							<div class="col-sm-2">
								<div class="row">
									<div class="col-sm-8">
										<label for="immunization_name<?php echo $i; ?>">Administered Text</label>
										<div class="input-group">
											<input type="text" readonly name="immunization_name<?php echo $i; ?>" id="immunization_name<?php echo $i; ?>" class="form-control fl" onKeyUp="top.fmain.chk_change('<?php echo $obj['immunization_id'];?>',this,event); insertImmIdVizChange('<?php echo $obj['immunization_id'];?>',this,event, document.getElementById('imnzn_main<?php echo $i; ?>'));" value="<?php echo $obj['immunization_id'];?>" onChange="top.fmain.chk_change('<?php echo $obj['immunization_id'];?>',this,event); setImmunizationAutoFill(<?php echo $i; ?>,this,'<?php echo ($i + 25); ?>'); setValueInHiddenField('im_name<?php echo $i; ?>', this.value); insertImmIdVizChange('<?php echo $obj['immunization_id'];?>',this,event, document.getElementById('imnzn_main<?php echo $i; ?>'));"/>
											<?php echo get_simple_menu($arrCVXCodes,"menuCVXCode$i","immunization_name$i");?>
										</div>
									</div>
									<div class="col-sm-4">
										<label for="immunization_cvx_code<?php echo $i; ?>">CVX Code</label>
										<input type="text" name="immunization_cvx_code<?php echo $i; ?>" id="immunization_cvx_code<?php echo $i; ?>" class="form-control fl" onKeyUp="top.fmain.chk_change('<?php echo $obj['immunization_id']; ?>',this,event); insertImmIdVizChange('<?php echo $obj['immunization_id']; ?>',this,event, document.getElementById('imnzn_main<?php echo $i; ?>'));" value="<?php echo $obj['immunization_cvx_code']; ?>" onChange="setValueInHiddenField('im_cvx_code<?php echo $i; ?>', this.value)"/>
										<input type="hidden" name="im_cvx_code<?php echo $i; ?>" value="" />
									</div>
								</div>
							</div>
							
							<div class="col-sm-2">
								<div class="row">
									<div class="col-sm-5 text-center pointer">
										<label class="blank_label" for="immunization_child<?php echo $i; ?>">Child Imm.</label>
										<div class="checkbox">
											<input type="checkbox" name="immunization_child<?php echo $i; ?>" id="immunization_child<?php echo $i; ?>" class="fl" value="1" onClick="top.fmain.chk_change('<?php echo $obj['child_immunization']; ?>',this,event); insertImmIdVizChange('<?php echo $obj['child_immunization']; ?>',this,event, document.getElementById('imnzn_main<?php echo $i; ?>'));" <?php echo $obj['child_immunization']; ?> onChange="setValueInHiddenField('im_child<?php echo $i; ?>', this.checked)"/>
											<label for="immunization_child<?php echo $i; ?>"></label>
										</div>
										<input type="hidden" name="im_child<?php echo $i; ?>" value="<?php echo $obj['chk_child_immunization']; ?>" />
									</div>
									<div class="col-sm-4">
										<label for="immunization_dose<?php echo $i; ?>">Amount</label>
										<input type="text" name="immunization_dose<?php echo $i; ?>" id="immunization_dose<?php echo $i; ?>" class="form-control fl" onKeyUp="top.fmain.chk_change('<?php echo $obj['immzn_dose']; ?>',this,event); insertImmIdVizChange('<?php echo $obj['immzn_dose']; ?>',this,event, document.getElementById('imnzn_main<?php echo $i; ?>'));" value="<?php echo $obj['immzn_dose']; ?>" onChange="setValueInHiddenField('im_dose<?php echo $i; ?>', this.value)"/>
										<input type="hidden" name="im_dose<?php echo $i; ?>" value="<?php echo $obj['immzn_dose']; ?>" />
									</div>
									<div class="col-sm-3">
										<label for="immunization_dose_unit<?php echo $i; ?>">Unit</label>
										<input type="text" name="immunization_dose_unit<?php echo $i; ?>" id="immunization_dose_unit<?php echo $i; ?>" class="form-control fl" onKeyUp="top.fmain.chk_change('<?php echo $obj['immzn_dose_unit']; ?>',this,event); insertImmIdVizChange('<?php echo $obj['immzn_dose_unit']; ?>',this,event, document.getElementById('imnzn_main<?php echo $i; ?>'));" value="<?php echo $obj['immzn_dose_unit']; ?>" onChange="setValueInHiddenField('im_dose_unit<?php echo $i; ?>', this.value)"/>
										<input type="hidden" name="im_dose_unit<?php echo $i; ?>" value="<?php echo $obj['immzn_dose_unit']; ?>" />
									</div>	
								</div>	
							</div>
							
							<div class="col-sm-2">
								<div class="row">
									<div class="col-sm-6">
										<label for="immunization_Route_and_site<?php echo $i; ?>">Route</label>
										<input type="text" name="immunization_Route_and_site<?php echo $i; ?>" id="immunization_Route_and_site<?php echo $i; ?>" class="form-control fl immunization_Route_and_site" onKeyUp="top.fmain.chk_change('<?php echo $obj['immzn_route_site']; ?>',this,event); insertImmIdVizChange('<?php echo $obj['immzn_route_site']; ?>',this,event, document.getElementById('imnzn_main<?php echo $i; ?>'));" value="<?php echo $obj['immzn_route_site']; ?>" />
										<input type="hidden" class="im_route" name="im_route<?php echo $i; ?>" value="<?php echo $obj['immzn_route_site_code']; ?>" />
									</div>
									<div class="col-sm-6">
										<label for="immunization_site<?php echo $i; ?>">Site</label>
										<input type="text" name="immunization_site<?php echo $i; ?>" id="immunization_site<?php echo $i; ?>" class="form-control fl immunization_site" onKeyUp="top.fmain.chk_change('<?php echo $obj['immzn_site'];?>',this,event); insertImmIdVizChange('<?php echo $obj['immzn_site'];?>',this,event, document.getElementById('imnzn_main<?php echo $i; ?>'));" value="<?php echo $obj['immzn_site'];?>" />
										<input type="hidden" class="im_site" name="im_site<?php echo $i; ?>" value="<?php echo $obj['site_code'];?>" />
									</div>
								</div>
							</div>
							
							<div class="col-sm-2">
								<div class="row">
									<div class="col-sm-6">
										<label for="immunization_Lot<?php echo $i; ?>">Substance Lot#</label>
										<input type="text" name="immunization_Lot<?php echo $i; ?>" id="immunization_Lot<?php echo $i; ?>" class="form-control fl" onKeyUp="top.fmain.chk_change('<?php echo $obj['lot_number']; ?>',this,event); insertImmIdVizChange('<?php echo $obj['lot_number']; ?>',this,event, document.getElementById('imnzn_main<?php echo $i; ?>'));" value="<?php echo $obj['lot_number']; ?>" onChange="setValueInHiddenField('im_lot<?php echo $i; ?>', this.value)"/>
										<input type="hidden" name="im_lot<?php echo $i; ?>" value="<?php echo $obj['lot_number']; ?>" />
									</div>
									<div class="col-sm-6">
										<label for="immunization_Expiration_Date<?php echo $i; ?>">Expiration Date</label>
										<div class="input-group">
											<input type="text" name="immunization_Expiration_Date<?php echo $i; ?>" id="immunization_Expiration_Date<?php echo $i; ?>" class="datepicker form-control fl" onBlur="checkdate(this)" onKeyUp="top.fmain.chk_change('<?php echo $obj['expiration_date']; ?>',this,event); insertImmIdVizChange('<?php echo $obj['expiration_date']; ?>',this,event, document.getElementById('imnzn_main<?php echo $i; ?>'));" value="<?php echo $obj['expiration_date']; ?>" onChange="top.fmain.chk_change('<?php echo $obj['expiration_date']; ?>',this,event); insertImmIdVizChange('<?php echo $obj['expiration_date']; ?>',this,event, document.getElementById('imnzn_main<?php echo $i; ?>')); setValueInHiddenField('im_expiration_date<?php echo $i; ?>', this.value)"/>
											<label for="immunization_Expiration_Date<?php echo $i; ?>" class="input-group-addon pointer">
												<span class="glyphicon glyphicon-calendar"></span>	
											</label>	
										</div>	
										<input type="hidden" name="imnzn_id[<?php echo $i; ?>]" id="imnzn_id<?php echo $i; ?>" value="<?php echo $obj['immzn_dose_id']; ?>">
										<input type="hidden" name="immzn_dose_id[<?php echo $i; ?>]" value="<?php echo $obj['immzn_dose_id']; ?>" id="immzn_dose_id<?php echo $i; ?>">
										<input type="hidden" name="primary_key_id<?php echo $i; ?>" value=""/>
										<input type="hidden" name="im_expiration_date<?php echo $i; ?>" value="<?php echo $obj['immzn_dose_id']; ?>" />
									</div>	
								</div>	
							</div>
				
							<div class="col-sm-4">
								<div class="row">
									<div class="col-sm-6">
										<label for="immunization_Manufacturer<?php echo $i; ?>">Manufacturer Name</label>
										<input type="text" name="immunization_Manufacturer<?php echo $i; ?>" id="immunization_Manufacturer<?php echo $i; ?>" class="form-control fl immunization_Manufacturer" onKeyUp="top.fmain.chk_change('<?php echo $obj['manufacturer']; ?>',this,event); insertImmIdVizChange('<?php echo $obj['manufacturer']; ?>',this,event, document.getElementById('imnzn_main<?php echo $i; ?>'));" value="<?php echo $obj['manufacturer']; ?>" onChange="setValueInHiddenField('im_manufacture<?php echo $i; ?>', this.value)"/>
										<input type="hidden" name="im_manufacture<?php echo $i; ?>" value="<?php echo $obj['manufacturer']; ?>" />
									</div>
									<div class="col-sm-2">
										<label for="immunization_Mfr_Code<?php echo $i; ?>">Code</label>
										<input type="text" name="immunization_Mfr_Code<?php echo $i; ?>" id="immunization_Mfr_Code<?php echo $i; ?>" class="form-control fl immunization_Mfr_Code" onKeyUp="top.fmain.chk_change('<?php echo $obj['manufacturer_code']; ?>',this,event); insertImmIdVizChange('<?php echo $obj['manufacturer_code']; ?>',this,event, document.getElementById('imnzn_main<?php echo $i; ?>'));" value="<?php echo $obj['manufacturer_code']; ?>" onChange="setValueInHiddenField('im_mfr_code<?php echo $i; ?>', this.value)"/>
										<input type="hidden" name="im_mfr_code<?php echo $i; ?>" value="<?php echo $obj['manufacturer_code']; ?>" />
									</div>	
									<div class="col-sm-4">
										<label for="immunization_funding_program<?php echo $i; ?>">Funding Program</label>
										<input type="text" name="immunization_funding_program<?php echo $i; ?>" id="immunization_funding_program<?php echo $i; ?>" class="form-control fl immunization_funding_program" onKeyUp="top.fmain.chk_change('',this,event);" value="<?php echo $obj['funding_program']; ?>" />
										<input type="hidden" class="im_funding_program" name="im_funding_program<?php echo $i; ?>" value="<?php echo $obj['funding_program']; ?>" />
									</div>	
								</div>	
							</div>	
						</div>	
						
						<div class="clearfix"></div>
						
						<!-- Second row -->
						<div class="pt10 row">
							<div class="col-sm-2">
								<div class="row">
									<div class="col-sm-7">
										<label for="immunization_Admin_date<?php echo $i; ?>">Administration Date</label>
										<div class="input-group">
											<input type="text" name="immunization_Admin_date<?php echo $i; ?>" id="immunization_Admin_date<?php echo $i; ?>" class="datepicker form-control fl im_adm_dt" onBlur="checkdate(this)" <?php if($obj['administered_date'] == ''){?> onClick="getDate_and_setToField('immunization_Admin_date<?php echo $i; ?>', 'immunization_Admin_time<?php echo $i; ?>')"<?php }?> onKeyUp="top.fmain.chk_change('<?php echo $obj['administered_date']; ?>',this,event); insertImmIdVizChange('<?php echo $obj['administered_date']; ?>',this,event, document.getElementById('imnzn_main<?php echo $i; ?>'));" value="<?php echo $obj['administered_date']; ?>" onChange="top.fmain.chk_change('<?php echo $obj['administered_date']; ?>',this,event); insertImmIdVizChange('<?php echo $obj['administered_date']; ?>',this,event, document.getElementById('imnzn_main<?php echo $i; ?>')); setValueInHiddenField('im_admin_date<?php echo $i; ?>', this.value)"/>
											<label for="immunization_Admin_date<?php echo $i; ?>" class="input-group-addon pointer pointer">
												<span class="glyphicon glyphicon-calendar"></span>
											</label>	
										</div>	
										<input type="hidden" name="im_admin_date<?php echo $i; ?>" value="<?php echo $obj['administered_date']; ?>" />	
									</div>
									<div class="col-sm-5">
										<label for="immunization_Admin_time<?php echo $i; ?>">Time</label>
										<div class="input-group">
											<input type="text" name="immunization_Admin_time<?php echo $i; ?>" id="immunization_Admin_time<?php echo $i; ?>" class="form-control fl" <?php if($obj['administered_time'] == ''){?> onClick="getDate_and_setToField('immunization_Admin_date<?php echo $i; ?>', 'immunization_Admin_time<?php echo $i; ?>')" <?php } ?> onKeyUp="top.fmain.chk_change('<?php echo $obj['administered_time']; ?>',this,event); insertImmIdVizChange('<?php echo $obj['administered_time']; ?>',this,event, document.getElementById('imnzn_main<?php echo $i; ?>'));" value="<?php echo $obj['administered_time']; ?>" onChange="setValueInHiddenField('im_admin_time<?php echo $i; ?>', this.value)" />
										</div>
										<input type="hidden" name="im_admin_time<?php echo $i; ?>" value="<?php echo $obj['administered_time']; ?>" />
									</div>
								</div>
							</div>
							
							<div class="col-sm-2">
								<div class="row">
									<div class="col-sm-6">
										<label for="administered_by_id<?php echo $i; ?>">Administered By</label>
										<select name="administered_by_id<?php echo $i; ?>" id="administered_by_id<?php echo $i; ?>" class="selectpicker fl" onChange="setValueInHiddenField('im_administrated_by<?php echo $i; ?>', this.value)" data-width="100%" data-title="Select" data-size="10">
											<?php echo $obj['provider_data']; ?>
										</select>
										<input type="hidden" name="im_administrated_by<?php echo $i; ?>" value="<?php echo $obj['administered_by_id']; ?>" />
									</div>
									<div class="col-sm-6">
										<label for="immunization_consent_date<?php echo $i; ?>">Consent Date</label>	
										<div class="input-group">
											<input type="text" name="immunization_consent_date<?php echo $i; ?>" id="immunization_consent_date<?php echo $i; ?>" class="datepicker form-control fl im_consent_dt" onBlur="checkdate(this)" <?php if($obj['consent_date'] == ''){?> onClick="getDate_and_setToField('immunization_consent_date<?php echo $i; ?>', '')"<?php }?> onKeyUp="top.fmain.chk_change('<?php echo $obj['consent_date']; ?>',this,event); insertImmIdVizChange('<?php echo $obj['consent_date']; ?>',this,event, document.getElementById('imnzn_main<?php echo $i; ?>'));" value="<?php echo $obj['consent_date']; ?>" onChange="top.fmain.chk_change('<?php echo $obj['consent_date']; ?>',this,event); insertImmIdVizChange('<?php echo $obj['consent_date']; ?>',this,event, document.getElementById('imnzn_main<?php echo $i; ?>')); setValueInHiddenField('im_consent_date<?php echo $i; ?>', this.value)"/>
											<input type="hidden" name="im_consent_date<?php echo $i; ?>" value="<?php echo $obj['consent_date']; ?>" />
											<label for="immunization_consent_date<?php echo $i; ?>" class="input-group-addon pointer">
												<span class="glyphicon glyphicon-calendar"></span>	
											</label> 
										</div>			
									</div>
								</div>
							</div>
							
							<div class="col-sm-2">
								<label for="immunization_reaction<?php echo $i; ?>">Reaction</label>
								<input type="text" name="immunization_reaction<?php echo $i; ?>" id="immunization_reaction<?php echo $i; ?>" class="form-control fl" onKeyUp="top.fmain.chk_change('<?php echo $obj['adverse_reaction']; ?>',this,event); insertImmIdVizChange('<?php echo $obj['adverse_reaction']; ?>',this,event, document.getElementById('imnzn_main<?php echo $i; ?>'));" value="<?php echo $obj['adverse_reaction']; ?>" onChange="setValueInHiddenField('im_reaction<?php echo $i; ?>', this.value)"/>
								<input type="hidden" name="im_reaction<?php echo $i; ?>" value="<?php echo $obj['adverse_reaction']; ?>" />
							</div>
							
							<div class="col-sm-2">
								<div class="row">
									<div class="col-sm-6">
										<label for="immunization_reaction_date<?php echo $i; ?>">Reaction Date</label>
										<div class="input-group">
											<input type="text" name="immunization_reaction_date<?php echo $i; ?>" id="immunization_reaction_date<?php echo $i; ?>" class="datepicker form-control fl" onBlur="checkdate(this)" <?php if($obj['adverse_reaction_date'] == ''){?> onClick="getDate_and_setToField('immunization_reaction_date<?php echo $i; ?>', 'immunization_reaction_time<?php echo $i; ?>')" <?php } ?> onKeyUp="top.fmain.chk_change('<?php echo $obj['adverse_reaction_date']; ?>',this,event); insertImmIdVizChange('<?php echo $obj['adverse_reaction_date']; ?>',this,event, document.getElementById('imnzn_main<?php echo $i; ?>'));" value="<?php echo $obj['adverse_reaction_date']; ?>" onChange="top.fmain.chk_change('<?php echo $obj['adverse_reaction_date']; ?>',this,event); insertImmIdVizChange('<?php echo $obj['adverse_reaction_date']; ?>',this,event, document.getElementById('imnzn_main<?php echo $i; ?>')); setValueInHiddenField('im_reaction_date<?php echo $i; ?>', this.value)"/>
											<input type="hidden" name="im_reaction_date<?php echo $i; ?>" value="<?php echo $obj['adverse_reaction_date']; ?>" />
											<label for="immunization_reaction_date<?php echo $i; ?>" class="input-group-addon pointer">
												<span class="glyphicon glyphicon-calendar"></span>
											</label>	
										</div>	
									</div>
									<div class="col-sm-6">
										<label for="immunization_reaction_time<?php echo $i; ?>">Time</label>
										<div class="input-group">
											<input type="text" name="immunization_reaction_time<?php echo $i; ?>" id="immunization_reaction_time<?php echo $i; ?>" class="form-control fl" <?php if($obj['adverse_reaction_time'] == ''){?> onClick="getDate_and_setToField('immunization_reaction_date<?php echo $i; ?>', 'immunization_reaction_time<?php echo $i; ?>')" <?php } ?> onKeyUp="top.fmain.chk_change('<?php echo $obj['adverse_reaction_time']; ?>',this,event); insertImmIdVizChange('<?php echo $obj['adverse_reaction_time']; ?>',this,event, document.getElementById('imnzn_main<?php echo $i; ?>'));" value="<?php echo $obj['adverse_reaction_time']; ?>" onChange="setValueInHiddenField('im_reaction_time<?php echo $i; ?>', this.value)"/>
											<input type="hidden" name="im_reaction_time<?php echo $i; ?>" value="<?php echo $obj['adverse_reaction_time']; ?>" />
										</div>	
									</div>	
								</div>
							</div>
							
							<div class="col-sm-4">
								<div class="row"> 	
									<div class="col-sm-4">
										<label for="immunization_comments<?php echo $i; ?>">Administration Notes</label>
										<input type="text" name="immunization_comments<?php echo $i; ?>" id="immunization_comments<?php echo $i; ?>"  class="form-control fl immunization_comments" onChange="setValueInHiddenField('im_comments<?php echo $i; ?>', this.value)"  onKeyUp="top.fmain.chk_change('<?php echo $obj['note']; ?>',this,event); insertImmIdVizChange('<?php echo $obj['note']; ?>',this,event, document.getElementById('imnzn_main<?php echo $i; ?>'));" value="<?php echo $obj['note']; ?>">
										<input type="hidden" class="im_comments" name="im_comments<?php echo $i; ?>" value="<?php echo $obj['note']; ?>" />
										<input type="hidden" class="im_comments_code" name="im_comments_code<?php echo $i; ?>" value="<?php echo $obj['nip001']; ?>" />
									</div>
									<div class="col-sm-4">
										<label for="immunization_refusal_reason<?php echo $i; ?>">Refusal Reason</label>
										<input type="text" name="immunization_refusal_reason<?php echo $i; ?>" id="immunization_refusal_reason<?php echo $i; ?>" class="form-control fl immunization_refusal_reason" onChange="setValueInHiddenField('im_refusal_reason<?php echo $i; ?>', this.value)" onKeyUp="top.fmain.chk_change('',this,event);" value="<?php echo $obj['refusal_reason']; ?>">
										<input type="hidden" name="im_refusal_reason<?php echo $i; ?>" class="im_refusal_reason" value="<?php echo $obj['refusal_reason']; ?>" />
										<input type="hidden" name="im_refusal_reason_code<?php echo $i; ?>" class="im_refusal_reason_code" value="<?php echo $obj['nip002']; ?>" />	
									</div>	
									<div class="col-sm-4">
										<label for="scp_status<?php echo $i; ?>">Status</label>
										<select name="scp_status<?php echo $i; ?>" id="scp_status<?php echo $i; ?>" class="selectpicker" data-width="100%">
											<?php 
												$opt = '';
												foreach($searchByData as $key => $val){
													$sel = '';
													if($key == $obj['scp_status']){
														$sel = 'selected';
													}
													if($val == 'All') {
														$val = 'Select';
													}
													$opt .= '<option value="'.$key.'" '.$sel.'>'.$val.'</option>';
												}
												echo $opt;
											?>
										</select>
									</div>	
								</div>	
							</div>	
						</div>	
						
						<div class="clearfix"></div>
						
						<?php $pub_imnzn_main_id = $obj['imnzn_main_id']; ?>
						
						<!-- Third row -->
						<div class="pt10 row">
							<div class="col-sm-2 bg-success pb10">
								<div class="row">
									<div class="col-sm-6">
										<label for="immunization_type<?php echo $i; ?>_1">Vaccine Type</label>
										<input type="hidden" name="imnzn_pub_id<?php echo $i; ?>_1" value="<?php echo $immunPubDataArr[$pub_imnzn_main_id][1]['id']; ?>" />
										<input type="text" name="immunization_type<?php echo $i; ?>_1" id="immunization_type<?php echo $i; ?>_1" class="form-control fl" onKeyUp="top.fmain.chk_change('<?php echo $obj['immunization_id']; ?>',this,event); insertImmIdVizChange('<?php echo $obj['immunization_id']; ?>',this,event, document.getElementById('imnzn_main<?php echo $i; ?>_1'));" value="<?php echo $immunPubDataArr[$pub_imnzn_main_id][1]['immzn_type']; ?>" onChange="setValueInHiddenField('im_type<?php echo $i; ?>_1', this.value)"/>
										<input type="hidden" name="im_type<?php echo $i; ?>_1" value="<?php echo $immunPubDataArr[$pub_imnzn_main_id][1]['immzn_type']; ?>" />
									</div>
									<div class="col-sm-6">
										<label for="immunization_cvx_code<?php echo $i; ?>_1">CVX Code</label>
										<input type="text" name="immunization_cvx_code<?php echo $i; ?>_1" id="immunization_cvx_code<?php echo $i; ?>_1" class="form-control fl" onKeyUp="top.fmain.chk_change('<?php echo $obj['immunization_id']; ?>',this,event); insertImmIdVizChange('<?php echo $obj['immunization_id']; ?>',this,event, document.getElementById('imnzn_main<?php echo $i; ?>_1'));" value="<?php echo $immunPubDataArr[$pub_imnzn_main_id][1]['immunization_cvx_code']; ?>" onChange="setValueInHiddenField('im_cvx_code<?php echo $i; ?>_1', this.value)"/>
										<input type="hidden" name="im_cvx_code<?php echo $i; ?>_1" value="" />
									</div>
								</div>
							</div>
							
							<div class="col-sm-2 bg-success pb10">
								<div class="row">
									<div class="col-sm-6">
										<label for="immunization_published_date<?php echo $i; ?>_1">Published Date</label>
										<div class="input-group">
											<input type="text" name="immunization_published_date<?php echo $i; ?>_1" id="immunization_published_date<?php echo $i; ?>_1" class="datepicker form-control fl immunization_published_date" onBlur="checkdate(this)" onClick="getDate_and_setToField('immunization_published_date<?php echo $i; ?>_1', '')" onKeyUp="top.fmain.chk_change('',this,event);" value="<?php echo $immunPubDataArr[$pub_imnzn_main_id][1]['published_date']; ?>" onChange="top.fmain.chk_change('',this,event); setValueInHiddenField('im_published_date<?php echo $i; ?>_1', this.value)"/>
											<label for="immunization_published_date<?php echo $i; ?>_1" class="input-group-addon pointer">
												<span class="glyphicon glyphicon-calendar"></span>
											</label>
										</div>
										<input type="hidden" name="im_published_date<?php echo $i; ?>_1" value="<?php echo $immunPubDataArr[$pub_imnzn_main_id][1]['published']; ?>" />
									</div>
									<div class="col-sm-6">
										<label for="immunization_presented_date<?php echo $i; ?>_1">Presented Date</label>
										<div class="input-group">
											<input type="text" name="immunization_presented_date<?php echo $i; ?>_1" id="immunization_presented_date<?php echo $i; ?>_1" class="datepicker form-control fl immunization_presented_date" onBlur="checkdate(this)" onClick="getDate_and_setToField('immunization_presented_date<?php echo $i; ?>_1', '')" onKeyUp="top.fmain.chk_change('',this,event);" value="<?php echo $immunPubDataArr[$pub_imnzn_main_id][1]['presented_date']; ?>" onChange="top.fmain.chk_change('',this,event); setValueInHiddenField('im_presented_date<?php echo $i; ?>_1', this.value)"/>
											<input type="hidden" name="im_presented_date<?php echo $i; ?>_1" value="<?php echo $immunPubDataArr[$pub_imnzn_main_id][1]['presented_date']; ?>" />
											<label for="immunization_presented_date<?php echo $i; ?>_1" class="input-group-addon pointer">
												<span class="glyphicon glyphicon-calendar"></span>
											</label>	
										</div>		
									</div>
								</div>	
							</div>
							
							<div class="col-sm-2">
								<div class="row">
									<div class="col-sm-2 bg-success pb10">
										<label class="blank_label">&nbsp;</label>
										<span class="glyphicon glyphicon-plus pointer" id="add_row_pub_<?php echo $i; ?>" alt="Add More" onClick="top.fmain.addNewPubRow('<?php echo $i; ?>')"></span>
										<br/>
									</div>
									<div class="col-sm-10">
										<label for="immunization_ordered_by<?php echo $i; ?>">Ordered By</label>
										<select name="immunization_ordered_by<?php echo $i; ?>" id="immunization_ordered_by<?php echo $i; ?>" class="selectpicker fl" onKeyUp="top.fmain.chk_change('',this,event);" value="" onChange="setValueInHiddenField('im_ordered_by<?php echo $i; ?>', this.value)" data-width="100%" data-title="Select" data-size="10">
											<?php echo $obj['ordered_by']; ?>
										</select>
										<input type="hidden" name="im_ordered_by<?php echo $i; ?>" value="<?php echo $obj['ordered_by_id']; ?>" />
									</div>
								</div>
							</div>
							
							<div class="col-sm-2">
								<label for="immunization_entered_by<?php echo $i; ?>">Entered By</label>
								<input type="text" readonly name="immunization_entered_by<?php echo $i; ?>" id="immunization_entered_by<?php echo $i; ?>" class="form-control fl" value="<?php echo $obj['entered_by']; ?>"/>	
							</div>
							
							<div class="col-sm-4">
								<div class="row">	
									<div class="col-sm-6">
										<label for="disease_with_immunity<?php echo $i; ?>">Disease With Presumed Immunity</label>	
										<textarea name="disease_with_immunity<?php echo $i; ?>" id="disease_with_immunity<?php echo $i; ?>" rows="1" class="form-control fl" onChange="setValueInHiddenField('im_disease<?php echo $i; ?>', this.value)" onKeyUp="top.fmain.chk_change('<?php echo $obj['disease_with_immunity']; ?>',this,event); insertImmIdVizChange('<?php echo $obj['disease_with_immunity']; ?>',this,event, document.getElementById('imnzn_main<?php echo $i; ?>'));" onBlur="setSnomedVal(this,'immunization_snomed<?php echo $i; ?>')"><?php echo $obj['disease_with_immunity']; ?></textarea>
										<input type="hidden" name="im_disease<?php echo $i; ?>" value="<?php echo $obj['disease_with_immunity']; ?>" />
									</div>
									<div class="col-sm-5">
										<label for="immunization_snomed<?php echo $i; ?>">SNOMED CT</label>
										<input type="text" name="immunization_snomed<?php echo $i; ?>" id="immunization_snomed<?php echo $i; ?>" class="form-control" value="<?php echo $obj['snomed']; ?>" />
									</div>	
									<div class="col-sm-1">
										<label class="blank_label">&nbsp;</label>
										<span class="glyphicon glyphicon-remove pointer pull-right" alt="Delete Row" onClick="immunization_remove_tr('<?php echo $obj['imnzn_main_id']; ?>', '<?php echo $i; ?>')"></span>
									</div>	
								</div>	
							</div>	
						</div>
						
						<div class="clearfix"></div>
						
						<!-- Multi row loop -->
						<div class="row">
							<div class="col-sm-12">
								
								<div id="addPubDivId_<?php echo $i; ?>" class="addPubDivCls row">	
								<?php
									$j = 2;
									foreach($immunPubDataArr[$pub_imnzn_main_id] as $key => $val){
										if($key > 1){ ?>
											<div id="addPubDivId_<?php echo $i; ?>_<?php echo $key; ?>" class="addPubDivCls col-sm-12">	
												<input type="hidden" name="imnzn_pub_id<?php echo $i; ?>_<?php echo $key; ?>" value="<?php echo $immunPubDataArr[$pub_imnzn_main_id][$key]['id']; ?>" />
												<div class="row">
													
													<div class="col-sm-2 bg-success pb10">
														<div class="row">
															<div class="col-sm-6">
																<label for="immunization_type<?php echo $i; ?>_<?php echo $key; ?>">Vaccine Type</label>
																<input type="text" name="immunization_type<?php echo $i; ?>_<?php echo $key; ?>" id="immunization_type<?php echo $i; ?>_<?php echo $key; ?>" class="form-control fl" onKeyUp="top.fmain.chk_change('<?php echo $obj['immunization_id']; ?>',this,event); insertImmIdVizChange('<?php echo $obj['immunization_id']; ?>',this,event, document.getElementById('imnzn_main<?php echo $i; ?>_<?php echo $key; ?>'));" value="<?php echo $immunPubDataArr[$pub_imnzn_main_id][$key]['immzn_type']; ?>" onChange="setValueInHiddenField('im_type<?php echo $i; ?>_<?php echo $key; ?>', this.value)"/>
																<input type="hidden" name="im_type<?php echo $i; ?>_<?php echo $key; ?>" value="<?php echo $immunPubDataArr[$pub_imnzn_main_id][$key]['immzn_type']; ?>" />
															</div>
															<div class="col-sm-6">
																<label for="immunization_cvx_code<?php echo $i; ?>_<?php echo $key; ?>">CVX Code</label>
																<input type="text" name="immunization_cvx_code<?php echo $i; ?>_<?php echo $key; ?>" id="immunization_cvx_code<?php echo $i; ?>_<?php echo $key; ?>" class="form-control fl" onKeyUp="top.fmain.chk_change('<?php echo $obj['immunization_id']; ?>',this,event); insertImmIdVizChange('<?php echo $obj['immunization_id']; ?>',this,event, document.getElementById('imnzn_main<?php echo $i; ?>_<?php echo $key; ?>'));" value="<?php echo $immunPubDataArr[$pub_imnzn_main_id][$key]['immunization_cvx_code']; ?>" onChange="setValueInHiddenField('im_cvx_code<?php echo $i; ?>_<?php echo $key; ?>', this.value)"/>
																<input type="hidden" name="im_cvx_code<?php echo $i; ?>_<?php echo $key; ?>" value="" />
															</div>
														</div>
													</div>
													
													<div class="col-sm-2 bg-success pb10">
														<div class="row">
															<div class="col-sm-6">
																<label for="immunization_published_date<?php echo $i; ?>_<?php echo $key; ?>">Published Date</label>
																<div class="input-group">
																	<input type="text" name="immunization_published_date<?php echo $i; ?>_<?php echo $key; ?>" id="immunization_published_date<?php echo $i; ?>_<?php echo $key; ?>" class="datepicker form-control fl immunization_published_date" onBlur="checkdate(this)" onClick="getDate_and_setToField('immunization_published_date<?php echo $i; ?>_<?php echo $key; ?>', '')" onKeyUp="top.fmain.chk_change('',this,event);" value="<?php echo $immunPubDataArr[$pub_imnzn_main_id][$key]['published_date']; ?>" onChange="top.fmain.chk_change('',this,event); setValueInHiddenField('im_published_date<?php echo $i; ?>_<?php echo $key; ?>', this.value)"/>
																	<label for="immunization_published_date<?php echo $i; ?>_1" class="input-group-addon pointer">
																		<span class="glyphicon glyphicon-calendar"></span>
																	</label>	
																</div>
																<input type="hidden" name="im_published_date<?php echo $i; ?>_<?php echo $key; ?>" value="<?php echo $immunPubDataArr[$pub_imnzn_main_id][$key]['published']; ?>" />
															</div>
															<div class="col-sm-6">
																<label for="immunization_presented_date<?php echo $i; ?>_<?php echo $key; ?>">Presented Date</label>
																<div class="input-group">
																	<input type="text" name="immunization_presented_date<?php echo $i; ?>_<?php echo $key; ?>" id="immunization_presented_date<?php echo $i; ?>_<?php echo $key; ?>" class="datepicker form-control fl immunization_presented_date" onBlur="checkdate(this)" onClick="getDate_and_setToField('immunization_presented_date<?php echo $i; ?>_<?php echo $key; ?>', '')" onKeyUp="top.fmain.chk_change('',this,event);" value="<?php echo $immunPubDataArr[$pub_imnzn_main_id][$key]['presented_date']; ?>" onChange="top.fmain.chk_change('',this,event); setValueInHiddenField('im_presented_date<?php echo $i; ?>_<?php echo $key; ?>', this.value)"/>
																	<input type="hidden" name="im_presented_date<?php echo $i; ?>_<?php echo $key; ?>" value="<?php echo $immunPubDataArr[$pub_imnzn_main_id][$key]['presented_date']; ?>" />
																	<label for="immunization_presented_date<?php echo $i; ?>_1" class="input-group-addon pointer">
																		<span class="glyphicon glyphicon-calendar"></span>
																	</label>
																</div>
															</div>
														</div>
													</div>
													<div class="col-sm-2">
														<div class="row">
															<div class="col-sm-2 bg-success pb10" style="height:61px;"></div>
														</div>
													</div>
												</div>
											</div>
											<div class="clearfix"></div>
									<?php }
										$j++;
									}
								?>	
								</div>
								
							</div>   
						</div> 
						<input type="hidden" name="last_cnt_pub_<?php echo $i; ?>" id="last_cnt_pub_<?php echo $i; ?>" value="<?php echo $j; ?>">	
					</div>
					<?php
						$i++;
					}
				?>
				
				<!-- Empty Imm. Row -->
				<div id="immunization_row_<?php echo $i;?>" class="col-sm-12 whitebox bordbx">
					<input type="hidden" name="imnzn_main<?php echo $i; ?>" id="imnzn_main<?php echo $i; ?>" value="">
					<input type="hidden" name="im_name<?php echo $i; ?>" id="im_name<?php echo $i; ?>" value="" />
					<!-- First row -->
					<div class="row">
						<div class="col-sm-2">
							<div class="row">
								<div class="col-sm-8">
									<label for="immunization_name<?php echo $i; ?>">Administered Text</label>
									<div class="input-group">
										<input type="text" readonly name="immunization_name<?php echo $i; ?>" id="immunization_name<?php echo $i; ?>" class="form-control fl" onKeyUp="top.fmain.chk_change('',this,event); insertImmIdVizChange('',this,event, document.getElementById('imnzn_main<?php echo $i; ?>'));" value="" onChange="top.fmain.chk_change('',this,event); setImmunizationAutoFill(<?php echo $i; ?>,this,'<?php echo ($i + 25); ?>'); setValueInHiddenField('im_name<?php echo $i; ?>', this.value); insertImmIdVizChange('',this,event, document.getElementById('imnzn_main<?php echo $i; ?>'));"/>
										<?php  echo get_simple_menu($arrCVXCodes,"menuCVXCode$i","immunization_name$i");?>
									</div>	
								</div>
								<div class="col-sm-4">
									<label for="immunization_cvx_code<?php echo $i; ?>">CVX Code</label>
									<input type="text" name="immunization_cvx_code<?php echo $i; ?>" id="immunization_cvx_code<?php echo $i; ?>" class="form-control fl" onKeyUp="top.fmain.chk_change('',this,event); insertImmIdVizChange('',this,event, document.getElementById('imnzn_main<?php echo $i; ?>'));" value="" onChange="setValueInHiddenField('im_cvx_code<?php echo $i; ?>', this.value)"/>
									<input type="hidden" name="im_cvx_code<?php echo $i; ?>" value="" />
								</div>
							</div>
						</div>
						
						<div class="col-sm-2">
							<div class="row">
								<div class="col-sm-5 text-center pointer">
									<label class="blank_label " for="immunization_child<?php echo $i; ?>">Child Imm.</label>
									<div class="checkbox">
										<input type="checkbox" name="immunization_child<?php echo $i; ?>" id="immunization_child<?php echo $i; ?>" class="fl" value="1" onClick="top.fmain.chk_change('',this,event); insertImmIdVizChange('',this,event, document.getElementById('imnzn_main<?php echo $i; ?>'));" onChange="setValueInHiddenField('im_child<?php echo $i; ?>', this.checked)"/>	
										<label for="immunization_child<?php echo $i; ?>"></label>	
									</div>	
									<input type="hidden" name="im_child<?php echo $i; ?>" value="" />	
								</div>
								<div class="col-sm-4">
									<label for="immunization_dose<?php echo $i; ?>">Amount</label>
									<input type="text" name="immunization_dose<?php echo $i; ?>" id="immunization_dose<?php echo $i; ?>" class="form-control fl" onKeyUp="top.fmain.chk_change('',this,event); insertImmIdVizChange('',this,event, document.getElementById('imnzn_main<?php echo $i; ?>'));" value="" onChange="setValueInHiddenField('im_dose<?php echo $i; ?>', this.value)"/>
									<input type="hidden" name="im_dose<?php echo $i; ?>" value="" />	
								</div>
								<div class="col-sm-3">
									<label for="immunization_dose_unit<?php echo $i; ?>">Unit</label>
									<input type="text" name="immunization_dose_unit<?php echo $i; ?>" id="immunization_dose_unit<?php echo $i; ?>" class="form-control fl" onKeyUp="top.fmain.chk_change('',this,event); insertImmIdVizChange('',this,event, document.getElementById('imnzn_main<?php echo $i; ?>'));" value="" onChange="setValueInHiddenField('im_dose_unit<?php echo $i; ?>', this.value)"/>
									<input type="hidden" name="im_dose_unit<?php echo $i; ?>" value="" />	
								</div>	
							</div>	
						</div>
						
						<div class="col-sm-2">
							<div class="row">
								<div class="col-sm-6">
									<label for="immunization_Route_and_site<?php echo $i; ?>">Route</label>
									<input type="text" name="immunization_Route_and_site<?php echo $i; ?>" id="immunization_Route_and_site<?php echo $i; ?>" class="form-control fl immunization_Route_and_site" onKeyUp="top.fmain.chk_change('',this,event); insertImmIdVizChange('',this,event, document.getElementById('imnzn_main<?php echo $i; ?>'));" value="" />
									<input type="hidden" class="im_route" name="im_route<?php echo $i; ?>" value="" />	
								</div>
								<div class="col-sm-6">
									<label for="immunization_site<?php echo $i; ?>">Site</label>
									<input type="text" name="immunization_site<?php echo $i; ?>" id="immunization_site<?php echo $i; ?>" class="form-control fl immunization_site" onKeyUp="top.fmain.chk_change('',this,event); insertImmIdVizChange('',this,event, document.getElementById('imnzn_main<?php echo $i; ?>'));" value="" />
									<input type="hidden" class="im_site" name="im_site<?php echo $i; ?>" value="" />	
								</div>
							</div>
						</div>
						
						<div class="col-sm-2">
							<div class="row">
								<div class="col-sm-6">
									<label for="immunization_Lot<?php echo $i; ?>">Substance Lot#</label>
									<input type="text" name="immunization_Lot<?php echo $i; ?>" id="immunization_Lot<?php echo $i; ?>" class="form-control fl" onKeyUp="top.fmain.chk_change('',this,event); insertImmIdVizChange('',this,event, document.getElementById('imnzn_main<?php echo $i; ?>'));" value="" onChange="setValueInHiddenField('im_lot<?php echo $i; ?>', this.value)"/>
									<input type="hidden" name="im_lot<?php echo $i; ?>" value="" />	
								</div>
								<div class="col-sm-6">
									<label for="immunization_Expiration_Date<?php echo $i; ?>">Expiration Date</label>
									<div class="input-group">
										<input type="text" name="immunization_Expiration_Date<?php echo $i; ?>" id="immunization_Expiration_Date<?php echo $i; ?>" class="datepicker form-control fl" onBlur="checkdate(this)" onKeyUp="top.fmain.chk_change('',this,event); insertImmIdVizChange('',this,event, document.getElementById('imnzn_main<?php echo $i; ?>'));" value="" onChange="top.fmain.chk_change('',this,event); insertImmIdVizChange('',this,event, document.getElementById('imnzn_main<?php echo $i; ?>')); setValueInHiddenField('im_expiration_date<?php echo $i; ?>', this.value)"/>	
										<label for="immunization_Expiration_Date<?php echo $i; ?>" class="input-group-addon pointer">
											<span class="glyphicon glyphicon-calendar"></span>	
										</label>	
									</div>	
									<input type="hidden" name="imnzn_id[<?php echo $i; ?>]" id="imnzn_id<?php echo $i; ?>" value="">
									<input type="hidden" name="immzn_dose_id[<?php echo $i; ?>]" value="" id="immzn_dose_id<?php echo $i; ?>">
									<input type="hidden" name="primary_key_id<?php echo $i; ?>" value=""/>
									<input type="hidden" name="im_expiration_date<?php echo $i; ?>" value="" />	
								</div>	
							</div>	
						</div>

						<div class="col-sm-4">
							<div class="row">
								<div class="col-sm-6">
									<label for="immunization_Manufacturer<?php echo $i; ?>">Manufacturer Name</label>
									<input type="text" name="immunization_Manufacturer<?php echo $i; ?>" id="immunization_Manufacturer<?php echo $i; ?>" class="form-control fl immunization_Manufacturer" onKeyUp="top.fmain.chk_change('',this,event); insertImmIdVizChange('',this,event, document.getElementById('imnzn_main<?php echo $i; ?>'));" value="" onChange="setValueInHiddenField('im_manufacture<?php echo $i; ?>', this.value)"/>
									<input type="hidden" name="im_manufacture<?php echo $i; ?>" value="" />	
								</div>
								<div class="col-sm-2">
									<label for="immunization_Mfr_Code<?php echo $i; ?>">Code</label>
									<input type="text" name="immunization_Mfr_Code<?php echo $i; ?>" id="immunization_Mfr_Code<?php echo $i; ?>" class="form-control fl immunization_Mfr_Code" onKeyUp="top.fmain.chk_change('',this,event); insertImmIdVizChange('',this,event, document.getElementById('imnzn_main<?php echo $i; ?>'));" value="" onChange="setValueInHiddenField('im_mfr_code<?php echo $i; ?>', this.value)"/>
									<input type="hidden" name="im_mfr_code<?php echo $i; ?>" value="" />	
								</div>	
								<div class="col-sm-4">
									<label for="immunization_funding_program<?php echo $i; ?>">Funding Program</label>
									<input type="text" name="immunization_funding_program<?php echo $i; ?>" id="immunization_funding_program<?php echo $i; ?>" class="form-control fl immunization_funding_program" onKeyUp="top.fmain.chk_change('',this,event);" value="" />
									<input type="hidden" class="im_funding_program" name="im_funding_program<?php echo $i; ?>" value="" /> 
								</div>	
							</div>	
						</div>	
					</div>	
					<div class="clearfix"></div>
					<!-- Second row -->
					<div class="pt10 row">
						<div class="col-sm-2">
							<div class="row">
								<div class="col-sm-7">
									<label for="immunization_Admin_date<?php echo $i; ?>">Administration Date</label>
									<div class="input-group">
										<input type="text" name="immunization_Admin_date<?php echo $i; ?>" id="immunization_Admin_date<?php echo $i; ?>" class="datepicker form-control fl im_adm_dt" onBlur="checkdate(this)"  onClick="getDate_and_setToField('immunization_Admin_date<?php echo $i; ?>', 'immunization_Admin_time<?php echo $i; ?>')" onKeyUp="top.fmain.chk_change('',this,event); insertImmIdVizChange('',this,event, document.getElementById('imnzn_main<?php echo $i; ?>'));" value="" onChange="top.fmain.chk_change('',this,event); insertImmIdVizChange('',this,event, document.getElementById('imnzn_main<?php echo $i; ?>')); setValueInHiddenField('im_admin_date<?php echo $i; ?>', this.value)"/>
										<label for="immunization_Admin_date<?php echo $i; ?>" class="input-group-addon pointer pointer">
											<span class="glyphicon glyphicon-calendar"></span>
										</label>	
									</div>	
									<input type="hidden" name="im_admin_date<?php echo $i; ?>" value="" />	
								</div>
								<div class="col-sm-5">
									<label for="immunization_Admin_time<?php echo $i; ?>">Time</label>
									<div class="input-group">
										 <input type="text" name="immunization_Admin_time<?php echo $i; ?>" id="immunization_Admin_time<?php echo $i; ?>" class="form-control fl" onClick="getDate_and_setToField('immunization_Admin_date<?php echo $i; ?>', 'immunization_Admin_time<?php echo $i; ?>')" onKeyUp="top.fmain.chk_change('',this,event); insertImmIdVizChange('',this,event, document.getElementById('imnzn_main<?php echo $i; ?>'));" value="" onChange="setValueInHiddenField('im_admin_time<?php echo $i; ?>', this.value)" />
									</div>
									<input type="hidden" name="im_admin_time<?php echo $i; ?>" value="" />	
								</div>
							</div>
						</div>
						
						<div class="col-sm-2">
							<div class="row">
								<div class="col-sm-6">
									<label for="administered_by_id<?php echo $i; ?>">Administered By</label>
									<select name="administered_by_id<?php echo $i; ?>" id="administered_by_id<?php echo $i; ?>" class="selectpicker fl" onChange="setValueInHiddenField('im_administrated_by<?php echo $i; ?>', this.value)" data-width="100%" data-title="Select" data-size="10">
										<?php echo $provider_val; ?>
									</select>
									<input type="hidden" name="im_administrated_by<?php echo $i; ?>" value="" />
								</div>
								<div class="col-sm-6">
									<label for="immunization_consent_date<?php echo $i; ?>">Consent Date</label>	
									<div class="input-group">
										 <input type="text" name="immunization_consent_date<?php echo $i; ?>" id="immunization_consent_date<?php echo $i; ?>" class="datepicker form-control fl im_consent_dt" onBlur="checkdate(this)" onClick="getDate_and_setToField('immunization_consent_date<?php echo $i; ?>', '')" onKeyUp="top.fmain.chk_change('',this,event); insertImmIdVizChange('',this,event, document.getElementById('imnzn_main<?php echo $i; ?>'));" value="" onChange="top.fmain.chk_change('',this,event); insertImmIdVizChange('',this,event, document.getElementById('imnzn_main<?php echo $i; ?>')); setValueInHiddenField('im_consent_date<?php echo $i; ?>', this.value)"/>
										 <input type="hidden" name="im_consent_date<?php echo $i; ?>" value="" />
										<label for="immunization_consent_date<?php echo $i; ?>" class="input-group-addon pointer">
											<span class="glyphicon glyphicon-calendar"></span>	
										</label> 
									</div>			
								</div>
							</div>
						</div>
						
						<div class="col-sm-2">
							<label for="immunization_reaction<?php echo $i; ?>">Reaction</label>
							<input type="text" name="immunization_reaction<?php echo $i; ?>" id="immunization_reaction<?php echo $i; ?>" class="form-control fl" onKeyUp="top.fmain.chk_change('',this,event); insertImmIdVizChange('',this,event, document.getElementById('imnzn_main<?php echo $i; ?>'));" value="" onChange="setValueInHiddenField('im_reaction<?php echo $i; ?>', this.value)"/>
							<input type="hidden" name="im_reaction<?php echo $i; ?>" value="" />	
						</div>
						
						<div class="col-sm-2">
							<div class="row">
								<div class="col-sm-6">
									<label for="immunization_reaction_date<?php echo $i; ?>">Reaction Date</label>
									<div class="input-group">
										<input type="text" name="immunization_reaction_date<?php echo $i; ?>" id="immunization_reaction_date<?php echo $i; ?>" class="datepicker form-control fl" onBlur="checkdate(this)"  onClick="getDate_and_setToField('immunization_reaction_date<?php echo $i; ?>', 'immunization_reaction_time<?php echo $i; ?>')"  onKeyUp="top.fmain.chk_change('',this,event); insertImmIdVizChange('',this,event, document.getElementById('imnzn_main<?php echo $i; ?>'));" value="" onChange="top.fmain.chk_change('',this,event); insertImmIdVizChange('',this,event, document.getElementById('imnzn_main<?php echo $i; ?>')); setValueInHiddenField('im_reaction_date<?php echo $i; ?>', this.value)"/>
										<input type="hidden" name="im_reaction_date<?php echo $i; ?>" value="" />
										<label for="immunization_reaction_date<?php echo $i; ?>" class="input-group-addon pointer">
											<span class="glyphicon glyphicon-calendar"></span>
										</label>	
									</div>	
								</div>
								<div class="col-sm-6">
									<label for="immunization_reaction_time<?php echo $i; ?>">Time</label>
									<div class="input-group">
										<input type="text" name="immunization_reaction_time<?php echo $i; ?>" id="immunization_reaction_time<?php echo $i; ?>" class="form-control fl" onClick="getDate_and_setToField('immunization_reaction_date<?php echo $i; ?>', 'immunization_reaction_time<?php echo $i; ?>')"  onKeyUp="top.fmain.chk_change('',this,event); insertImmIdVizChange('',this,event, document.getElementById('imnzn_main<?php echo $i; ?>'));" value="" onChange="setValueInHiddenField('im_reaction_time<?php echo $i; ?>', this.value)"/>
										<input type="hidden" name="im_reaction_time<?php echo $i; ?>" value="" />
									</div>	
								</div>	
							</div>
						</div>
						
						<div class="col-sm-4">
							<div class="row"> 	
								<div class="col-sm-4">
									<label for="immunization_comments<?php echo $i; ?>">Administration Notes</label>
									<input type="text" name="immunization_comments<?php echo $i; ?>" id="immunization_comments<?php echo $i; ?>"  class="form-control fl immunization_comments" onChange="setValueInHiddenField('im_comments<?php echo $i; ?>', this.value)"  onKeyUp="top.fmain.chk_change('',this,event); insertImmIdVizChange('',this,event, document.getElementById('imnzn_main<?php echo $i; ?>'));" value="">
									<input type="hidden" class="im_comments" name="im_comments<?php echo $i; ?>" value="" />
									<input type="hidden" class="im_comments_code" name="im_comments_code<?php echo $i; ?>" value="" />
								</div>
								<div class="col-sm-4">
									<label for="immunization_refusal_reason<?php echo $i; ?>">Refusal Reason</label>
									<input type="text" name="immunization_refusal_reason<?php echo $i; ?>" id="immunization_refusal_reason<?php echo $i; ?>" class="form-control fl immunization_refusal_reason" onChange="setValueInHiddenField('im_refusal_reason<?php echo $i; ?>', this.value)" onKeyUp="top.fmain.chk_change('',this,event);" value="">
									<input type="hidden" name="im_refusal_reason<?php echo $i; ?>" class="im_refusal_reason" value="" />
									<input type="hidden" name="im_refusal_reason_code<?php echo $i; ?>" class="im_refusal_reason_code" value="" />	
								</div>	
								<div class="col-sm-4">
									<label for="scp_status<?php echo $i; ?>">Status</label>
									<select name="scp_status<?php echo $i; ?>" id="scp_status<?php echo $i; ?>" class="selectpicker" data-width="100%">
										<?php 
											$opt = '';
											foreach($searchByData as $key => $val){
												if($val == 'All') {
													$val = 'Select';
												}
												$opt .= '<option value="'.$key.'">'.$val.'</option>';
											}
											echo $opt;
										?>
									</select>
								</div>	
							</div>	
						</div>	
					</div>	
					<div class="clearfix"></div>
					<!-- Third row -->
					<div class="pt10 row">
						<div class="col-sm-2 bg-success pb10">
							<div class="row">
								<div class="col-sm-6">
									<label for="immunization_type<?php echo $i; ?>_1">Vaccine Type</label>
									<input type="text" name="immunization_type<?php echo $i; ?>_1" id="immunization_type<?php echo $i; ?>_1" class="form-control fl" onKeyUp="top.fmain.chk_change('',this,event); insertImmIdVizChange('',this,event, document.getElementById('imnzn_main<?php echo $i; ?>_1'));" value="" onChange="setValueInHiddenField('im_type<?php echo $i; ?>_1', this.value)"/>
									<input type="hidden" name="im_type<?php echo $i; ?>_1" value="" />	
								</div>
								<div class="col-sm-6">
									<label for="immunization_cvx_code<?php echo $i; ?>_1">CVX Code</label>
									<input type="text" name="immunization_cvx_code<?php echo $i; ?>_1" id="immunization_cvx_code<?php echo $i; ?>_1" class="form-control fl" onKeyUp="top.fmain.chk_change('',this,event); insertImmIdVizChange('',this,event, document.getElementById('imnzn_main<?php echo $i; ?>_1'));" value="" onChange="setValueInHiddenField('im_cvx_code<?php echo $i; ?>_1', this.value)"/>
									<input type="hidden" name="im_cvx_code<?php echo $i; ?>_1" value="" />	
								</div>
							</div>
						</div>
						
						<div class="col-sm-2 bg-success pb10">
							<div class="row">
								<div class="col-sm-6"> 
									<label for="immunization_published_date<?php echo $i; ?>_1">Published Date</label>
									<div class="input-group">
										 <input type="text" name="immunization_published_date<?php echo $i; ?>_1" id="immunization_published_date<?php echo $i; ?>_1" class="datepicker form-control fl immunization_published_date" onBlur="checkdate(this)" onClick="getDate_and_setToField('immunization_published_date<?php echo $i; ?>_1', '')" onKeyUp="top.fmain.chk_change('',this,event);" value="" onChange="top.fmain.chk_change('',this,event); setValueInHiddenField('im_published_date<?php echo $i; ?>_1', this.value)"/>
										<label for="immunization_published_date<?php echo $i; ?>_1" class="input-group-addon pointer">
											<span class="glyphicon glyphicon-calendar"></span>
										</label>	
									</div>
									<input type="hidden" name="im_published_date<?php echo $i; ?>_1" value="" />	
								</div>	
								<div class="col-sm-6"> 	
									<label for="immunization_presented_date<?php echo $i; ?>_1">Presented Date</label>
									<div class="input-group">
									<input type="text" name="immunization_presented_date<?php echo $i; ?>_1" id="immunization_presented_date<?php echo $i; ?>_1" class="datepicker form-control fl immunization_presented_date" onBlur="checkdate(this)" onClick="getDate_and_setToField('immunization_presented_date<?php echo $i; ?>_1', '')" onKeyUp="top.fmain.chk_change('',this,event);" value="" onChange="top.fmain.chk_change('',this,event); setValueInHiddenField('im_presented_date<?php echo $i; ?>_1', this.value)"/>
									<input type="hidden" name="im_presented_date<?php echo $i; ?>_1" value="" />
										<label for="immunization_presented_date<?php echo $i; ?>_1" class="input-group-addon pointer">
											<span class="glyphicon glyphicon-calendar"></span>
										</label>	
									</div>		
								</div>
							</div>	
						</div>
						
						<div class="col-sm-2">
							<div class="row">
								<div class="col-sm-2 bg-success pb10">
									<label class="blank_label">&nbsp;</label>
									<span class="glyphicon glyphicon-plus pointer" id="add_row_pub_<?php echo $i; ?>" alt="Add More" onClick="top.fmain.addNewPubRow('<?php echo $i; ?>')"></span>
									<br/>
								</div>
								<div class="col-sm-10">
									<label for="immunization_ordered_by<?php echo $i; ?>">Ordered By</label>
									<select name="immunization_ordered_by<?php echo $i; ?>" id="immunization_ordered_by<?php echo $i; ?>" class="selectpicker fl" onKeyUp="top.fmain.chk_change('',this,event);" value="" onChange="setValueInHiddenField('im_ordered_by<?php echo $i; ?>', this.value)" data-width="100%" data-title="Select" data-size="10">
										<?php echo $provider_val; ?>
									</select>
									<input type="hidden" name="im_ordered_by<?php echo $i; ?>" value="" />
								</div>
							</div>
						</div>
						
						<div class="col-sm-2">
							<label for="immunization_entered_by<?php echo $i; ?>">Entered By</label>
							<input type="text" readonly name="immunization_entered_by<?php echo $i; ?>" id="immunization_entered_by<?php echo $i; ?>" class="form-control fl" value="<?php echo $entered_by; ?>"/>	
						</div>
						
						<div class="col-sm-4">
							<div class="row">	
								<div class="col-sm-6">
									<label for="disease_with_immunity<?php echo $i; ?>">Disease With Presumed Immunity</label>	
									 <textarea name="disease_with_immunity<?php echo $i; ?>" id="disease_with_immunity<?php echo $i; ?>" rows="1" class="form-control fl" onChange="setValueInHiddenField('im_disease<?php echo $i; ?>', this.value)" onKeyUp="top.fmain.chk_change('',this,event); insertImmIdVizChange('',this,event, document.getElementById('imnzn_main<?php echo $i; ?>'));" onBlur="setSnomedVal(this,'immunization_snomed<?php echo $i; ?>')"></textarea>
									 <input type="hidden" name="im_disease<?php echo $i; ?>" value="" />	
								</div>
								<div class="col-sm-5">
									<label for="immunization_snomed<?php echo $i; ?>">SNOMED CT</label>
									<input type="text" name="immunization_snomed<?php echo $i; ?>" id="immunization_snomed<?php echo $i; ?>" class="form-control" value=""   />
								</div>	
								<div class="col-sm-1">
									<label class="blank_label">&nbsp;</label>
									<span id="add_row_<?php echo $i; ?>" class="glyphicon glyphicon-plus pointer pull-right" alt="Add More" onClick="addNewRow(this, '<?php echo $i; ?>')"></span>
								</div>	
							</div>	
						</div>	
					</div>
					<div class="clearfix"></div>
					<!-- Appending multiple rows here -->
					<div class="row">
						<div class="col-sm-12">
							<div id="addPubDivId_<?php echo $i; ?>" class="addPubDivCls row"></div> 
						</div>
					</div>
					
					<div class="clearfix"></div>
                    <input type="hidden" name="last_cnt_pub_<?php echo $i; ?>" id="last_cnt_pub_<?php echo $i; ?>" value="2">
				</div>
			</div>
		</div>	
		<input type="hidden" name="last_cnt" id="last_cnt" value="<?php echo $i; ?>">
	</form>	
</div>
<script>
	top.show_loading_image("hide");
</script>
</body>
</html>	