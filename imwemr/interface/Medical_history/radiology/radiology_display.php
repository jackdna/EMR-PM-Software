<head>
	<script src="<?php echo $library_path; ?>/js/radiology.js" type="text/javascript"></script>	
</head>
<body onkeydown=("top.fmain.save_medical_history()"); id="rad_div" onLoad="setChkChangeDefault();">
	<div class="ml10">
		<form name="save_rad_test_form" id="save_rad_test_form" action="index.php?showpage=<?php echo $radiology->current_tab; ?>&action=saveFormData" method="post" autocomplete="off">
			<input type="hidden" name="curr_tab" id="curr_tab" value="<?php echo $radiology->current_tab;?>">
			<input type="hidden" name="info_alert" id="info_alert" value="<?php echo $ARR_INFO_ALERT_SERIALIZED; ?>">
			<input type="hidden" name="preObjBack" id="preObjBack" value=""/>
			<input type="hidden" name="next_tab" id="next_tab" value=""/>
			<input type="hidden" name="next_dir" id="next_dir" value=""/>
			<input type="hidden" name="buttons_to_show" id="buttons_to_show" value=""/>
			
			<!-- Heading -->
			<div class="radtop">
				<div class="row">
					<div class="col-sm-2 col-sm-offset-10">
						<div class="allflter">
							<div class="row">
								<div class="col-sm-4 text-right">
									<label for="exampleInputName2">FILTER :</label>
								</div>	
								<div class="col-sm-8">
									<select name="filter_val" class="selectpicker" data-width="100%" onChange="form_submit(this.value)" >
										<option value="">All</option>
										<?php 
											foreach($status_val as $key => $val){
												$sel = '';
												if($key == $filter_select){
													$sel = 'selected';
												}
												echo '<option value="'.$key.'" '.$sel.'>'.$val.'</option>';
											}
										?>
									</select>
								</div>	
							</div>
						</div>	
					</div>	
				</div>
			</div>	
			<div class="clearfix"></div>
			<!-- Lab input fields -->
			<div class="col-sm-12">
				<div class="row" id="lab_display_table">
				<?php
					$i = 1;
					foreach($radDataArr as $obj){
					?>
						<div id="rad_tr<?php echo $i; ?>" class="<?php echo $obj['TR_CLASS']; ?> col-sm-12 whitebox bordbx">  
							<div class="row">
								<div class="col-sm-2">
									<label for="rad_name<?php echo $i; ?>">Test Name</label>
									<input type="text" id="rad_name<?php echo $i; ?>" tabindex="<?php echo $i; ?>" name="rad_name<?php echo $i; ?>" onKeyUp="chk_change('<?php echo $obj['RAD_NAME']; ?>',this,event);" value="<?php echo $obj['RAD_NAME']; ?>" class="form-control pull-left">	
									<input type="hidden" name="rad_id<?php echo $i; ?>" value="<?php echo $obj['RAD_ID']; ?>">
								</div>	
								<?php $display_class_type = ''; if($obj['RAD_TYPE_DIS'] == 'none'){$display_class_type = 'hide';} ?>
								<div class="col-sm-2 <?php echo $display_class_type; ?>" id="rad_type_td_<?php echo $i; ?>" >
									<label for="rad_type<?php echo $i; ?>">Type</label>
									<select id="rad_type<?php echo $i; ?>" tabindex="<?php echo $i; ?>" name="rad_type<?php echo $i; ?>" class="selectpicker" data-width="100%" data-size="5"  onChange="chk_change('',this,event); getOtherType(this,'<?php echo $i; ?>');" data-title="Select">
										<?php echo $obj['RAD_TYPE']; ?>
									</select>
								</div>
								<?php $display_class = ''; if($obj['RAD_TYPE_OTHER_DIS'] == 'none'){$display_class = 'hide';} ?>
								<div class="col-sm-2 <?php echo $display_class; ?>" id="rad_type_td_other_<?php echo $i; ?>" >
									<label for="rad_type_other<?php echo $i; ?>">Other</label>
									<div class="input-group">
										<input type="text" id="rad_type_other<?php echo $i; ?>" tabindex="<?php echo $i; ?>" name="rad_type_other<?php echo $i; ?>" onKeyUp="chk_change('<?php echo $obj['RAD_TYPE_OTHER']; ?>',this,event);" value="<?php echo $obj['RAD_TYPE_OTHER']; ?>" class="form-control"  onBlur="getOtherType(this,'<?php echo $i; ?>');">
										<label for="" class="input-group-addon" onClick="toggle_other_dropdown('rad_type_td_other_<?php echo $i; ?>','rad_type_td_<?php echo $i; ?>')">
											<span class="glyphicon glyphicon glyphicon-arrow-left"></span>
										</label>	
									</div>
								</div>	
								
								<div class="col-sm-2">
									<label for="rad_loinc<?php echo $i; ?>">LOINC</label>
									<input type="text" id="rad_loinc<?php echo $i; ?>" tabindex="<?php echo $i; ?>" name="rad_loinc<?php echo $i; ?>" onKeyUp="chk_change('<?php echo $obj['RAD_LOINC']; ?>',this,event);" value="<?php echo $obj['RAD_LOINC']; ?>" class="form-control" >	
								</div>	
								
								<div class="col-sm-2">
									<label for="rad_fac_name<?php echo $i; ?>">Contact Name</label>
									<input type="text" id="rad_fac_name<?php echo $i; ?>" tabindex="<?php echo $i; ?>" name="rad_fac_name<?php echo $i; ?>" onKeyUp="chk_change('<?php echo $obj['RAD_FAC_NAME']; ?>',this,event);" value="<?php echo $obj['RAD_FAC_NAME']; ?>" class="form-control">	
								</div>
								
								<div class="col-sm-2">
									<label for="rad_address<?php echo $i; ?>">Contact Address</label>
									<textarea id="rad_address<?php echo $i; ?>" tabindex="<?php echo $i; ?>" name="rad_address<?php echo $i; ?>" class="form-control" rows="1" onKeyUp="chk_change('<?php echo $obj['RAD_ADDRESS']; ?>',this,event);"><?php echo $obj['RAD_ADDRESS']; ?></textarea>	
								</div>
									
								<div class="col-sm-2">
									<div class="row">
										<div class="col-sm-10">
											<label for="rad_indication<?php echo $i; ?>">Indication</label>
											<input type="text" id="rad_indication<?php echo $i; ?>" tabindex="<?php echo $i; ?>" name="rad_indication<?php echo $i; ?>" onKeyUp="chk_change('<?php echo $obj['RAD_INDICATION']; ?>',this,event);" value="<?php echo $obj['RAD_INDICATION']; ?>" class="form-control">	
										</div>
										<div class="col-sm-2 text-center">
										<?php if($obj['RAD_ID'] != ''){?>
											<label class="blank_label">&nbsp;</label>
											<span class="glyphicon glyphicon-hdd pointer" title="Scan lab document" onClick="javascript:openScan(<?php echo $obj['RAD_ID']; ?>);"></span>
										<?php }	?>	
										</div>	
									</div>	
								</div>		
							</div>
							<div class="row pt10">
								<div class="col-sm-2">
									<label for="rad_results<?php echo $i; ?>">Results</label>
									<input type="text" id="rad_results<?php echo $i; ?>" tabindex="<?php echo $i; ?>" name="rad_results<?php echo $i; ?>" onKeyUp="chk_change('<?php echo $obj['RAD_RESULTS']; ?>',this,event);" value="<?php echo $obj['RAD_RESULTS']; ?>" class="form-control">	
								</div>	
								<div class="col-sm-1">
									<label for="rad_order_date<?php echo $i; ?>">Order Date</label>
									<div class="input-group">
										<input type="text" id="rad_order_date<?php echo $i; ?>" tabindex="<?php echo $i; ?>" name="rad_order_date<?php echo $i; ?>" onChange="chk_change('<?php echo $obj['RAD_ORDER_DATE']; ?>',this,event);" value="<?php echo $obj['RAD_ORDER_DATE']; ?>" maxlength="10" class="datepicker form-control order_dt_rad" title="<?php echo $date_format; ?>">	
										<label for="rad_order_date<?php echo $i; ?>" class="input-group-addon">
											<span class="glyphicon glyphicon-calendar"></span>
										</label>	
									</div>
									
								</div>
								<div class="col-sm-1">
									<label for="rad_order_time<?php echo $i; ?>">Order Time</label>
									<input type="text" id="rad_results<?php echo $i; ?>" tabindex="<?php echo $i; ?>" name="rad_order_time<?php echo $i; ?>" onKeyUp="chk_change('<?php echo $obj['RAD_ORDER_TIME']; ?>',this,event);" value="<?php echo $obj['RAD_ORDER_TIME']; ?>" class="form-control">
								</div>
								<div class="col-sm-1">
									<label for="rad_results_date<?php echo $i; ?>">Result Date</label>	
									<div class="input-group">
										<input type="text" id="rad_results_date<?php echo $i; ?>" tabindex="<?php echo $i; ?>" name="rad_results_date<?php echo $i; ?>" onChange="chk_change('<?php echo $obj['RAD_RESULTS_DATE']; ?>',this,event);" value="<?php echo $obj['RAD_RESULTS_DATE']; ?>" maxlength="10" class="datepicker form-control result_dt_rad" title="<?php echo $date_format; ?>">
										<label for="rad_results_date<?php echo $i; ?>" class="input-group-addon">
											<span class="glyphicon glyphicon-calendar"></span>
										</label>	
									</div>
								</div>
								<div class="col-sm-1">
									<label for="rad_results_time<?php echo $i; ?>">Result Time</label>	
										<input type="text" id="rad_results<?php echo $i; ?>" tabindex="<?php echo $i; ?>" name="rad_results_time<?php echo $i; ?>" onKeyUp="chk_change('<?php echo $obj['RAD_RESULTS_TIME']; ?>',this,event);" value="<?php echo $obj['RAD_RESULTS_TIME']; ?>" class="form-control">
								</div>
								<div class="col-sm-2">
									<div class="row">
									<div class="col-sm-5">
										<label for="rad_status<?php echo $i; ?>">Status</label>
										<select name="rad_status<?php echo $i; ?>" id="rad_status<?php echo $i; ?>" tabindex="<?php echo $i; ?>" onChange="chk_change('',this,event);" class="selectpicker" data-width="100%" data-title="Select">
											<option value="1" <?php echo $obj['RAD_STATUS_ORDERED']; ?>>Pending</option>
											<option value="2" <?php echo $obj['RAD_STATUS_COMPLETE']; ?>>Completed</option>
										</select>
									</div>
									<div class="col-sm-7">
										<label for="rad_snowmed<?php echo $i; ?>">SNOWMED CT</label>
										<input type="text" id="rad_snowmed<?php echo $i; ?>" tabindex="<?php echo $i; ?>" name="rad_snowmed<?php echo $i; ?>" onChange="chk_change('<?php echo $obj['RAD_RESULTS_SNOWMED']; ?>',this,event);" value="<?php echo $obj['RAD_RESULTS_SNOWMED'] ?>" maxlength="10" class="form-control">
									</div>
								</div>
									
									
									
									
									  
								</div>	
								<div class="col-sm-2">
									<label for="rad_instuctions<?php echo $i; ?>">Instructions</label>
									<textarea id="rad_instuctions<?php echo $i; ?>" tabindex="<?php echo $i; ?>" name="rad_instuctions<?php echo $i; ?>" rows="1"  class="form-control" onKeyUp="chk_change('<?php echo $obj['RAD_INSTUCTIONS']; ?>',this,event);"><?php echo $obj['RAD_INSTUCTIONS']; ?></textarea>
								</div>	
								<div class="col-sm-2">
									<div class="row">
										<div class="col-sm-7">
											<label for="rad_order_by<?php echo $i; ?>">Order By</label>	
											<select name="rad_order_by<?php echo $i; ?>" id="rad_order_by<?php echo $i; ?>" tabindex="<?php echo $i; ?>" onChange="chk_change('',this,event);" class="selectpicker" data-width="100%" data-size="5">
												<?php echo $obj['RAD_ORDERED_BY']; ?>
											</select>  	
										</div>
										<div class="col-sm-3 text-center">
											<label for="refusal<?php echo $i; ?>">Refusal</label>	
											<div class="checkbox">
												<input type="checkbox" class="checkbox" name="refusal<?php echo $i; ?>" id="refusal<?php echo $i; ?>" tabindex="<?php echo $i; ?>" <?php if ($obj['REFUSAL'] == 1) echo 'checked'; ?> onChange="check_refusal(<?php echo $i; ?>);" value="<?php echo $obj['REFUSAL']; ?>">
												<label for="refusal<?php echo $i; ?>">&nbsp;</label>
											</div>
										</div>
										<div class="col-sm-2 text-center">
											<label class="blank_label">&nbsp;</label>
											<span class="glyphicon glyphicon-remove pointer" onClick="removeTableRow('<?php echo $obj['RAD_ID']; ?>','<?php echo $i; ?>');"></span>	
										</div>	
									</div>
								</div>
								<input type="hidden" name="refusal_reason<?php echo $i; ?>" id="refusal_reason<?php echo $i; ?>" value="<?php echo $obj['REFUSAL_REASON']; ?>">
								<input type="hidden" name="refusal_snomed<?php echo $i; ?>" id="refusal_snomed<?php echo $i; ?>" value="<?php echo $obj['REFUSAL_SNOMED']; ?>">		
							</div>	
						</div>
				<?php
						$i++;
					}
				?>	
					
				<div class="modal fade" id="myModal" role="dialog">
					<div class="modal-dialog">
					<!-- Modal content-->
						<div class="modal-content">
							<div class="modal-header bg-primary">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title" id="modal_title">Refusal Reason</h4>
							</div>
							<div class="modal-body">
								<input type="hidden" name="refusal_row" id="refusal_row" value="" >
								<input type="hidden" name="rowID" id="rowID" value="" >
								<div class="form-group">
									<label for="usrname">Refusal Reason</label>
									<textarea type="text" class="form-control" id="refusal_reason" name="refusal_reason"></textarea>
								</div>
								<div class="form-group">
									<label for="psw">Refusal Snomed</label>
									<input type="text" class="form-control" id="refusal_snomed" name="refusal_snomed">
								</div>
							</div>
							<div id="module_buttons" class="ad_modal_footer modal-footer">
								<button type="button" class="btn btn-success" onclick="check_refusal_values();">Save</button>
								<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
							</div>
						</div>
					</div>
				</div>
					
					<div id="rad_tr<?php echo $i; ?>" class="<?php echo $obj['TR_CLASS']; ?> col-sm-12 whitebox bordbx">  
						<div class="row">
							<div class="col-sm-2">
								<label for="rad_name<?php echo $i; ?>">Test Name</label>
								<input type="text" id="rad_name<?php echo $i; ?>" tabindex="<?php echo $i; ?>" name="rad_name<?php echo $i; ?>" onKeyUp="chk_change('',this,event);" value=""  class="form-control">
							</div>	
							<?php $display_class_type = ''; if($obj['RAD_TYPE_DIS'] == 'none'){$display_class_type = 'hide';} ?>
							<div class="col-sm-2 <?php echo $display_class_type; ?>" id="rad_type_td_<?php echo $i; ?>">
								<label for="rad_type<?php echo $i; ?>">Type</label>
								<select id="rad_type<?php echo $i; ?>" tabindex="<?php echo $i; ?>" name="rad_type<?php echo $i; ?>" class="selectpicker" data-width="100%" data-size="5" onChange="chk_change('',this,event); getOtherType(this,'<?php echo $i; ?>');">
									<?php echo $obj['RAD_TYPE']; ?>
								</select>
							</div>
							<?php $display_class = ''; if($obj['RAD_TYPE_OTHER_DIS'] == 'none'){$display_class = 'hide';} ?>
							<div class="col-sm-2 <?php echo $display_class;  ?>" id="rad_type_td_other_<?php echo $i; ?>" >
								<label for="rad_type_other<?php echo $i; ?>">Other</label>
								<div class="input-group">
									<input type="text" id="rad_type_other<?php echo $i; ?>" tabindex="<?php echo $i; ?>" name="rad_type_other<?php echo $i; ?>" onKeyUp="chk_change('<?php echo $obj['RAD_TYPE_OTHER']; ?>',this,event);" value="<?php echo $obj['RAD_TYPE_OTHER']; ?>" class="form-control"  onBlur="getOtherType(this,'<?php echo $i; ?>');">
									<label for="" class="input-group-addon" onClick="toggle_other_dropdown('rad_type_td_other_<?php echo $i; ?>','rad_type_td_<?php echo $i; ?>')">
										<span class="glyphicon glyphicon glyphicon-arrow-left"></span>
									</label>	
								</div>
							</div>	
							
							<div class="col-sm-2">
								<label for="rad_loinc<?php echo $i; ?>">LOINC</label>
								<input type="text" id="rad_loinc<?php echo $i; ?>" tabindex="<?php echo $i; ?>" name="rad_loinc<?php echo $i; ?>" onKeyUp="chk_change('<?php echo $obj['RAD_LOINC']; ?>',this,event);" value="" class="form-control" >	
							</div>	
							
							<div class="col-sm-2">
								<label for="rad_fac_name<?php echo $i; ?>">Contact Name</label>
								<input type="text" id="rad_fac_name<?php echo $i; ?>" tabindex="<?php echo $i; ?>" name="rad_fac_name<?php echo $i; ?>" onKeyUp="chk_change('<?php echo $obj['RAD_FAC_NAME']; ?>',this,event);" value="" class="form-control">	
							</div>
							
							<div class="col-sm-2">
								<label for="rad_address<?php echo $i; ?>">Contact Address</label>
								<textarea id="rad_address<?php echo $i; ?>" tabindex="<?php echo $i; ?>" name="rad_address<?php echo $i; ?>" class="form-control" rows="1" onKeyUp="chk_change('<?php echo $obj['RAD_ADDRESS']; ?>',this,event);"></textarea>	
							</div>
								
							<div class="col-sm-2">
								<div class="row">
									<div class="col-sm-10">
										<label for="rad_indication<?php echo $i; ?>">Indication</label>
										<input type="text" id="rad_indication<?php echo $i; ?>" tabindex="<?php echo $i; ?>" name="rad_indication<?php echo $i; ?>" onKeyUp="chk_change('<?php echo $obj['RAD_INDICATION']; ?>',this,event);" value="" class="form-control">	
									</div>
									<div class="col-sm-2">
									<?php if($obj['RAD_ID'] != ''){?>
										<br />
										 <span class="glyphicon glyphicon-hdd" title="Scan lab document" onClick="javascript:openScan(<?php echo $obj['RAD_ID']; ?>);"></span>
									<?php }	?>	
									</div>	
								</div>	
							</div>		
						</div>
						<div class="row pt10">
							<div class="col-sm-2">
								<label for="rad_results<?php echo $i; ?>">Results</label>
								<input type="text" id="rad_results<?php echo $i; ?>" tabindex="<?php echo $i; ?>" name="rad_results<?php echo $i; ?>" onKeyUp="chk_change('<?php echo $obj['RAD_RESULTS']; ?>',this,event);" value="" class="form-control">	
							</div>	
							<div class="col-sm-1">
								<label for="rad_order_date<?php echo $i; ?>">Order Date</label>	
								<div class="input-group">
									<input type="text" id="rad_order_date<?php echo $i; ?>" tabindex="<?php echo $i; ?>" name="rad_order_date<?php echo $i; ?>" onChange="chk_change('<?php echo $obj['RAD_ORDER_DATE']; ?>',this,event);" value="" maxlength="10" class="datepicker form-control order_dt_rad" title="<?php echo $date_format; ?>">	
									<label for="rad_order_date<?php echo $i; ?>" class="input-group-addon">
										<span class="glyphicon glyphicon-calendar"></span>
									</label>	
								</div>
							</div>
							<div class="col-sm-1">
									<label for="rad_order_time<?php echo $i; ?>">Order Time</label>
									<input type="text" id="rad_results<?php echo $i; ?>" tabindex="<?php echo $i; ?>" name="rad_order_time<?php echo $i; ?>" onKeyUp="chk_change('<?php echo $obj['RAD_ORDER_TIME']; ?>',this,event);" value="<?php echo $obj['RAD_ORDER_TIME']; ?>" class="form-control">
								</div>
							<div class="col-sm-1">
								<label for="rad_results_date<?php echo $i; ?>">Result Date</label>	
								<div class="input-group">
									<input type="text" id="rad_results_date<?php echo $i; ?>" tabindex="<?php echo $i; ?>" name="rad_results_date<?php echo $i; ?>" onChange="chk_change('<?php echo $obj['RAD_RESULTS_DATE']; ?>',this,event);" value="" maxlength="10" class="datepicker form-control result_dt_rad" title="<?php echo $date_format; ?>">
									<label for="rad_results_date<?php echo $i; ?>" class="input-group-addon">
										<span class="glyphicon glyphicon-calendar"></span>
									</label>	
								</div>
							</div>
							<div class="col-sm-1">
									<label for="rad_results_time<?php echo $i; ?>">Result Time</label>	
										<input type="text" id="rad_results<?php echo $i; ?>" tabindex="<?php echo $i; ?>" name="rad_results_time<?php echo $i; ?>" onKeyUp="chk_change('<?php echo $obj['RAD_RESULTS_TIME']; ?>',this,event);" value="<?php echo $obj['RAD_RESULTS_TIME']; ?>" class="form-control">
								</div>
							<div class="col-sm-2">
								<div class="row">
									<div class="col-sm-5">
										<label for="rad_status<?php echo $i; ?>">Status</label>
										<select name="rad_status<?php echo $i; ?>" id="rad_status<?php echo $i; ?>" tabindex="<?php echo $i; ?>" onChange="chk_change('',this,event);" class="selectpicker" data-width="100%" data-title="Select">
											<?php 
												foreach($status_val as $key => $val){
													echo '<option value="'.$key.'">'.$val.'</option>';
												}
											?>
										</select> 
									</div>
									<div class="col-sm-7">
										<label for="">SNOWMED CT</label>
										<input type="text" id="rad_results_snowmed<?php echo $i; ?>" tabindex="<?php echo $i; ?>" name="rad_results_snowmed<?php echo $i; ?>" onChange="chk_change('<?php echo $obj['RAD_RESULTS_SNOWMED']; ?>',this,event);" value="" maxlength="10" class="form-control">
									</div>
								</div>
							</div>	
							<div class="col-sm-2">
								<label for="rad_instuctions<?php echo $i; ?>">Instructions</label>
								<textarea id="rad_instuctions<?php echo $i; ?>" tabindex="<?php echo $i; ?>" name="rad_instuctions<?php echo $i; ?>" rows="1"  class="form-control" onKeyUp="chk_change('<?php echo $obj['RAD_INSTUCTIONS']; ?>',this,event);"></textarea>
							</div>	
							<div class="col-sm-2">
								<div class="row">
									<div class="col-sm-7">
										<label for="rad_order_by<?php echo $i; ?>">Order By</label>	
										<select name="rad_order_by<?php echo $i; ?>" id="rad_order_by<?php echo $i; ?>" tabindex="<?php echo $i; ?>" onChange="chk_change('',this,event);" class="selectpicker" data-width="100%" data-size="5">
											<?php echo $phyOption; ?>
										</select>  	
									</div>	
									<div class="col-sm-3 text-center">
										<label for="refusal<?php echo $i; ?>">Refusal</label>	
										<div class="checkbox">
											<input type="checkbox" class="checkbox" name="refusal1<?php echo $i; ?>" id="refusal<?php echo $i; ?>" tabindex="<?php echo $i; ?>" <?php if ($obj['REFUSAL'] == 1) echo 'checked'; ?> onChange="check_refusal(<?php echo $i; ?>);" value="<?php echo $obj['REFUSAL']; ?>">
											<label for="refusal<?php echo $i; ?>">&nbsp;</label>
										</div>
									</div>
									<div class="col-sm-2 text-center">
										<label class="blank_label">&nbsp;</label>
										<span id="add_row_<?php echo $i; ?>" class="glyphicon glyphicon-plus pointer" onClick="addNewRow('<?php echo $i; ?>');"></span> 	
									</div>
									<input type="hidden" name="refusal_reason<?php echo $i; ?>" id="refusal_reason<?php echo $i; ?>" value="<?php echo $obj['REFUSAL_REASON']; ?>">
									<input type="hidden" name="refusal_snomed<?php echo $i; ?>" id="refusal_snomed<?php echo $i; ?>" value="<?php echo $obj['REFUSAL_SNOMED']; ?>">
								</div>
							</div>	
						</div>	
					</div>
				</div>
			</div>
			
			<div class="col-sm-12 pt10 hide">
				<input type="submit" name="save_test" id="save_test" value="save">
			</div>
			<input type="hidden" name="last_cnt" id="last_cnt" value="<?php echo $i; ?>">	
		</form>	
	</div>
<script type="text/javascript">
	var lim = '<?php echo $i; ?>';
	$('[id^=rad_name]').each(function(id,elem){
		if($(elem).length > 0){
			$(elem).typeahead({source:global_js_var.typeahead_title_arr});
		}
	});
	
	$('document').ready(function(){
		$(":input").each(function (i) { $(this).attr('tabindex', i + 1); });     						
	});
</script>
<?php echo $getAdminAlert; ?>
<?php echo$getPatSpecificAlert; ?>
<?php echo$autoSetDivLeftMargin; ?>
<?php echo$autoSetDivTopMargin; ?>
<?php echo$writeJS; ?>
</body>
</html>