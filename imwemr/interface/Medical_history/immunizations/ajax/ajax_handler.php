<?php
include_once('../../../../config/globals.php');
include_once($GLOBALS['srcdir']."/classes/medical_hx/medical_history.class.php");
include_once($GLOBALS['srcdir']."/classes/medical_hx/immunization.class.php");
$medical = new MedicalHistory($_REQUEST['showpage']);
$imm_obj = New Immunization($medical->current_tab);

//CLS common function object
global $cls_common;

//Deleting Immunization Data
if(empty($_REQUEST['mode']) == false && empty($_REQUEST['del_id']) == false){
	$del_status = $imm_obj->delete_immunization($_REQUEST['del_id']);
	echo $del_status;
	exit();
}

//Saving Immunization registration info
if(isset($_REQUEST['save_reg']) && $_REQUEST['save_reg'] == 'yes'){
	$save_status = $imm_obj->save_imm_reg_info($_REQUEST);	
	echo $save_status;
	exit();	
}


//Adding New Immunization row
if(isset($_REQUEST['get_new_row']) && isset($_REQUEST['row_cnt']) && $_REQUEST['get_new_row'] == 'yes'){
	$i = $_REQUEST['row_cnt'];
	?>
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
							<?php  echo get_simple_menu($imm_obj->arrCVXCodes,"menuCVXCode$i","immunization_name$i");?>
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
						<label class="blank_label" for="immunization_child<?php echo $i; ?>">Child Imm.</label>
						<div class="checkbox checkbox-inline">
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
							<input type="text" name="immunization_Expiration_Date<?php echo $i; ?>" id="immunization_Expiration_Date<?php echo $i; ?>" class="datepicker form-control fl" onBlur="checkdate(this)" onKeyUp="top.fmain.chk_change('',this,event); " value="" onChange="top.fmain.chk_change('',this,event);  setValueInHiddenField('im_expiration_date<?php echo $i; ?>', this.value)"/>	
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
							<?php echo $cls_common->drop_down_providers('','',''); ?>
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
								foreach($imm_obj->filter_data_arr as $key => $val){
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
							<?php echo $cls_common->drop_down_providers('','',''); ?>
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
<?php	
	exit();	
}
?>