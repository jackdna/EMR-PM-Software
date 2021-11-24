<?php 
	include_once("../admin_header.php");
	$library_path = $GLOBALS['webroot'].'/library';
	extract($_REQUEST);
	include_once $GLOBALS['srcdir']."/classes/class.language.php";
	
	$billing_attention = xss_rem($billing_attention, 1);
	$facility_color = xss_rem($facility_color, 1);
	$facility_npi = xss_rem($facility_npi, 1);

	$arrGroupAlerts = array();
	$objCore_lang = new core_lang();
	$arrGroupAlerts = $objCore_lang->get_vocabulary("admin", "facility");
?>
	<script>
		var php_js_arr = <?php echo json_encode($arrGroupAlerts) ?>;
	</script>
	<!--<script src='<?php echo $library_path; ?>/js/grid_color/spectrum.js'></script>
	<link rel='stylesheet' href='<?php echo $library_path; ?>/js/grid_color/spectrum.css' />-->
	<script src="<?php echo $library_path; ?>/js/admin/admin_facility.js?<?php echo filemtime('../../../library/js/admin/admin_facility.js');?>" type="text/javascript"></script>
	<style>
		.sp-preview{width:80%!important;}
	</style>
	<body>
		<div class="container-fluid">
			<div class="whtbox">
				<textarea id="hidd_reason_text" class="hide"></textarea>
				<input type="hidden" name="ord_by_field" id="ord_by_field" value="name">
				<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
				<div class="row" style="height:<?php echo ($_SESSION['wn_height']-330);?>px; overflow-x:hidden; overflow:auto;">
					<div class="col-sm-12">
						<table class="table table-bordered table-hover adminnw" id="table_color">
							<thead>
								<tr>
									<th>
										<div class="checkbox checkbox-inline">
											<input type="checkbox" name="chk_sel_all" id="chk_sel_all" value="">
											<label for="chk_sel_all"></label>	
										</div>
									</th>
									<th class="pointer link_cursor" onClick="LoadResultSet('','','','facility.name',this);">Facility Name<span></span></th>				
									<th class="pointer link_cursor" onClick="LoadResultSet('','','','location',this);">Location<span></span></th>
									<th class="pointer link_cursor" onClick="LoadResultSet('','','','groups_new.name',this);" >Group Information<span></span></th>	
									<th class="pointer link_cursor" onClick="LoadResultSet('','','','out_ofoffice',this);">Contact <span></span></th>	
									<th class="pointer link_cursor" onClick="LoadResultSet('','','','facility.phone',this);">Phone<span></span></th>
								</tr>
							</thead>
							<tbody id="result_set"></tbody>	
						</table>	
					</div>
				</div>
				

				<!-- Facility Modal Box -->
				<div class="common_modal_wrapper">
					<div id="addNew_div" class="modal">
						<div id="selectContainer" style="position: absolute;"></div>
						<div class="modal-dialog modal_95">
							<div class="modal-content">
								<div class="modal-header bg-primary">
								  <button type="button" class="close" data-dismiss="modal">×</button>
								  <h4 class="modal-title" id="modal_title">Edit Record</h4>
								</div>
								<div class="modal-body" style="min-height:385px;">
									<div class="form-group">
										<form name="add_edit_frm" id="add_edit_frm" onSubmit="saveFormData();return false;">
											<input type="hidden" name="id" id="id" value="">
											<div class="row">
												<!-- Facility Col. -->
												<div class="col-lg-3 col-md-6 col-sm-12" id="fac_div">
													<div class="adminbox">
														<div class="head">
															<span>Facility</span>	
														</div>
														<div class="tblBg" id="refer_div">
															<div class="row">
																<div class="col-sm-5">
																	<div class="form-group">
																		<label for="facility_type">Facility Type</label>
																		<select name="facility_type" id="facility_type" onChange="change_form(this);" class="selectpicker" data-width="100%" data-title="Select"></select>
																	</div>	
																</div>
																<div class="col-sm-7">
																	<div class="form-group">
																		<label for="fac_prac_code">POS Facility</label>
																		<select name="fac_prac_code" id="fac_prac_code" class="selectpicker" data-width="100%" data-title="Select" data-size="5"></select>
																	</div>
																</div>
																<div class="col-sm-6">
																	<div class="form-group">
																		<label for="name">Facility Name</label>
																		<input type="text" name="name" id="name" value="" class="form-control">
																	</div>	
																</div>
																<div class="col-sm-3">
																	<div class="form-group">
																		<label>Facility Color</label>
																		<!-- <div class="bfh-colorpicker" data-name="facility_color" data-color="#FF0000"></div> -->
                                                                        <input type="color" class="form-control" name="facility_color" id="facility_color" value="#FFFFFF" />
																	</div>
																</div>
                                                                <div class="col-sm-3">
																	<div class="form-group">
																		<label for="fac_tax">Tax</label>
																		<div class="input-group">
                                                                        	<input class="form-control" type="text" name="fac_tax" id="fac_tax" value="">
                                                                        	<label for="fac_tax" class="input-group-addon">%</label>	
                                                                    	</div>	
																	</div>
																</div>
																<div id="trServers" class="col-sm-5 hide">
																	<div class="form-group">
																		<label for="server_name">Server</label>
																		<select name="server_name" id="server_name" class="selectpicker" data-width="100%" data-title="Select" data-container="#selectContainer"></select>
																	</div>	
																</div>
																<div class="row">
																<?php 
																	if(constant("SHOW_SERVER_LOCATION") == "1"){
																		$default_group_class = 'col-sm-4';
																	}else{
																		$default_group_class = 'col-sm-8';
																	}
																?>
																<div class="<?php echo $default_group_class; ?>">
																	<div class="form-group">
																		<label>Default Group</label>
																		<select name="default_group" id="default_group" class="selectpicker" data-width="100%" data-title="Select" data-container="#selectContainer">
																			<?php 
																				$vquery_t = "select * from groups_new where del_status='0' order by name";
																				$vsql_t = imw_query($vquery_t);
																				$se="";
																				while($rs_t = imw_fetch_array($vsql_t))
																				{
																					if($rs_d['default_group']==$rs_t['gro_id']){
																						$se="selected";
																					}
																					echo("<option ".$se." value='".$rs_t['gro_id']."'>".stripslashes($rs_t['name'])."</option>");
																					$se="";
																				}
																			?>
																		</select>
																	</div>
																</div>
																<?php if(constant("SHOW_SERVER_LOCATION") == "1"){ ?>
																	<div class="col-sm-4">
																		<div class="form-group">
																			<label>Server Location</label>
																			<select name="server_location" class="selectpicker" data-width="100%" data-title="Select" data-container="#selectContainer">
																				<?php 
																					$ser_qry = "select * from server_location order by server_name";
																					$ser_run = imw_query($ser_qry);
																					$se="";
																					while($ser_row = imw_fetch_array($ser_run))
																					{
																						if($rs_d['server_location']==$ser_row['id'])
																						{
																							$se="selected";
																						}
																						echo("<option ".$se." value='".$ser_row['id']."'>".$ser_row['server_name'].' - '.$ser_row['abbre']."</option>");
																						$se="";
																					}
																				?>
																			</select>
																		</div>	
																	</div>
																<?php } ?>
																<div class="col-sm-4">
																	<div class="form-group">
																		<label>Facility NPI <?php getHashOrNo();?></label>
																		<input type="text" name="facility_npi" value="<?php if($facility_npi > 0) print $facility_npi;?>" class="form-control">	
																	</div>	
																</div>
															</div>
															</div>	
														</div>
													</div>	
												</div>

												<!-- Contact block -->
												<div class="col-lg-3 col-md-6 col-sm-12" id="contacts_div">
													<div class="adminbox">
														<div class="head">
															<span>Contacts</span>	
														</div>
														<div class="tblBg" id="refer_div">
															<div class="row">
																<div class="col-sm-12">
																	<div class="form-group">
																		<label for="out_ofoffice">Contact</label>
																		<input type="text" name="out_ofoffice" id="out_ofoffice" value="" class="form-control">
																	</div>
																</div>
																<div class="col-sm-12">
																	<div class="form-group">
																		<label>Mailing Address</label>
																		<input type="text" name="street" id="street" value="" class="form-control">	
																	</div>
																</div>
																<div class="col-sm-12">
																	<div class="row">
																		<div class="col-sm-6">
																			<div class="form-group">
																				<label for="postal_code"><?php getZipPostalLabel(); ?></label>
																				<div class="row">
																					<div class="col-sm-6">
																						<input maxlength="<?php echo inter_zip_length();?>" type="text" id="postal_code" name="postal_code" value="" class="form-control" onBlur="zip_vs_state(this,'zip_ext','city','state');">	
																					</div>
																					<div class="col-sm-1 text-center">
																						-	
																					</div>
																					<div class="col-sm-5">
																						<input maxlength="4" type="text" id="zip_ext" name="zip_ext" value="" class="form-control">	
																					</div>		
																				</div>
																			</div>
																		</div>	
																		<div class="col-sm-6">
																			<div class="row">
																				<div class="col-sm-6">
																					<div class="form-group">
																						<label for="city">City</label>
																						<input type="text" id="city" name="city" value="" class="form-control">
																					</div>
																				</div>	
																				<div class="col-sm-6">
																					<div class="form-group">
																						<label><?php echo ucwords(inter_state_label());?></label>
																						<input type="text" size="3" id="state" name="state" value="" maxlength="<?php if(inter_state_val() == "abb")echo '2';?>" class="form-control">
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
												
												<!-- Contact block -->
												<div class="col-lg-3 col-md-6 col-sm-12" id="Mailing_div">
													<div class="adminbox">
														<div class="head">
															<span>Phone Details</span>	
														</div>
														<div class="tblBg" id="refer_div">
															<div class="row">
																<div class="col-sm-12">
																	<div class="row">
																		<div class="col-sm-8">
																			<div class="form-group">
																				<label for="phone">Phone</label>
																				<input type="text" name="phone" id="phone" value="" onBlur="set_phone_format(this,'<?php echo $GLOBALS['phone_format'] ?>');" class="form-control">
																			</div>
																		</div>	
																		<div class="col-sm-4">
																			<div class="form-group">
																				<label for="phone_ext">Ext.</label>
																				<input type="text" name="phone_ext" id="phone_ext" value="" class="form-control">
																			</div>
																		</div>		
																	</div>	
																</div>
																<div class="col-sm-5">
																	<div class="form-group">
																		<label for="fax">Fax</label>
																		<input type="text" name="fax" id="fax" value="" onBlur="set_phone_format(this,'<?php echo $GLOBALS['phone_format'] ?>');" class="form-control">
																	</div>
																</div>
																<div class="col-sm-7">
																	<div class="form-group">
																		<label for="ele_txt_1">Email</label>
																		<input type="text" name="ele_txt_1" id="ele_txt_1" value="" onKeyUp="javascript:search_email(event,this,'div_email_section')" class="form-control">
																		<div id="div_email_section" class="input-group" style="position:absolute">
																			<span class="input-group-btn">
																				<select class="btn hide" size="5">
																					<option selected="" value="@aol.com">aol.com</option>
																					<option value="@msn.com">msn.com</option>
																					<option value="@gmail.com">gmail.com</option>
																					<option value="@hotmail.com">hotmail.com</option>
																				</select>
																			</span>
																		</div>	
																	</div>	
																</div>
                                                                <div class="col-sm-12">
																	<div class="row">
																		<div id="emdeon_facs" class="col-sm-5">
																			<div class="form-group">
																				<label for="erx_facility_id">eRx Facility</label>
																				<div class="input-group">
																					 <select name="erx_facility_id" id="erx_facility_id" class="selectpicker" data-width="100%" data-title="Select"></select>
																					<label for="" class="input-group-addon">
																						<span class="glyphicon glyphicon-refresh link_cursor" onClick="refresh_emdeon_facs(this);"></span>	
																					</label>	
																				</div>
																			</div>
																		</div>
																		<div id="divLogoLink" class="col-sm-7 pt10 hide">
																			<input type="hidden" name="MAX_FILE_SIZE" value="2000000">
																			<br />
																			<div id="divFacLogo" class="pointer" onClick="upload_facility_logo('<?php echo $scanUploadSrc; ?>');"><strong class="text_purple">Upload Facility Logo</strong></div>
																		</div>	
																	</div>
																</div>
															</div>
														</div>
													</div>	
												</div>
												
												<!-- group info block -->
												<div class="col-lg-3 col-md-6 col-sm-12" id="grpInfo_div">
													<div class="adminbox" id="grpInfo_div1">
														<div class="head">
															<div class="row">
																<div class="col-sm-12">
																	<span>Group Information</span>
																</div>
															</div>
														</div>
														<div class="pt10 tblBg " id="refer_div">
															<div class="row">
																<div class="col-sm-12">
																	<div class="form-group">
																		<label>MOD</label>
																		<textarea class="form-control" name="mess_of_day" id="mess_of_day" rows="1"><?php $arr_replace_mess = array("<br />"); print str_replace($arr_replace_mess,"",$mess_of_day);?></textarea>	
																	</div>
																</div>
																<?php 
																	$class ="col-sm-12";
																	$class1 ="hide";
																	if((boolean) constant("APP_FACILITY_INCLUDE_EXPORT") == true){
																		$class ="col-sm-6";
																		$class1 ="col-sm-6";
																	}	
																?>	
																<div id="divShowinPtPotral" class="<?php echo $class; ?>">
																		<div class="checkbox checkbox-inline">
																			<input type="checkbox" name="show_in_ptportal" id="show_in_ptportal" value="1" class="form-control">
																			<label for="show_in_ptportal">Show in Pt. Portal</label>	
																		</div>
																</div>
																<div  class="<?php echo $class1; ?>">
																	<?php 
																	if((boolean) constant("APP_FACILITY_INCLUDE_EXPORT") == true){
																		$cbkChk = "";
																		if((int)$dbCbkIncludeInAppExport == 1){
																			$cbkChk = "checked";
																		}
																	?>
																	<div class="checkbox checkbox-inline text-center">
																		<input type="checkbox" name="cbk_include_in_app_export" id="cbk_include_in_app_export_1" value="1" <?php echo $cbkChk; ?> />
																		<label for="cbk_include_in_app_export_1" data-html="true" title="Include This Facility in Appointment Export" data-toggle="tooltip">
																		<?php echo constant("APP_FACILITY_INCLUDE_EXPORT"); ?></label>	
																	</div>
																	<?php } ?>	
																</div>
																<div class="col-sm-6" id="divSchProviders" class="hide">
																	<div class="checkbox checkbox-inline">
																		<input type="checkbox" name="apply_providers" id="apply_providers" value="1" checked="checked" /> <label for="apply_providers">Sch. Facility for all providers</label>
																	</div>	
																</div>
															</div>
                                                            <div class="row">
                                                            	<div class="col-sm-6">
																	<label>TIN</label>
                                                                    <input class="form-control" type="text" name="fac_tin" id="fac_tin" value="">
																</div>
																<div class="col-sm-6">
																	<label>CLIA <?php getHashOrNo();?></label>
                                                                    <input class="form-control" type="text" name="clia" id="clia" value="">
																</div>
                                                            </div>
														</div>
													</div>		
												</div>	
											</div>
											<div id="idone" class="row">
												<div class="col-lg-3 col-md-6 col-sm-12" id="timers_div">
													<div class="adminbox">
														<div class="head">
															<span>Timers</span>
														</div>
														<div class="tblBg" id="refer_div">
															<div class="row">
																<div class="col-sm-6">
																	<div class="form-group">
																		<label for="waiting_timer">Waiting Timer</label>
																		<input type="text" name="waiting_timer" id="waiting_timer" value="" class="form-control">
																	</div>
																</div>
																<div class="col-sm-6">
																	<div class="form-group">
																		<label for="chart_timer">Chart Review Timer</label>
																		<div class="input-group">
																			<select name="chart_timer" id="chart_timer" class="selectpicker" data-width="100%" data-title="Select" data-size="5"></select>
																			<label for="chart_timer" class="input-group-addon">
																				Days
																			</label>	
																		</div>	
																	</div>
																</div>
																<div class="col-sm-12">
																	<div class="form-group">
																		<label for="chart_finalize">Chart Finalize Completion Time</label>
																		<div class="input-group">
																			<select name="chart_finalize" id="chart_finalize" class="selectpicker" data-width="100%" data-title="Select" data-size="5"></select>
																			<label for="chart_finalize" class="input-group-addon">
																				Days
																			</label>	
																		</div>
																	</div>
																</div>
																<!--below given field is no more in use so we are hiding it-->
																<div class="col-sm-12 hide">
																	<div class="form-group">
																		<label for="chart_finalize">Regular Timeslot</label>
																		<div class="input-group">
																			<select name="regular_time_slot" id="regular_time_slot" class="selectpicker" data-width="100%" data-title="Select" data-size="5" disabled ></select>
																			<label for="regular_time_slot" class="input-group-addon">
																				Minutes
																			</label>	
																		</div>
																	</div>	
																</div>	
															</div>
														</div>	
													</div>	
												</div>
												<div class="col-lg-3 col-md-6 col-sm-12" id="login_div">
													<div class="adminbox">
														<div class="head">
															<span>Login Settings</span>
														</div>
														<div class="tblBg" id="refer_div">
															<div class="row">
																<div class="col-sm-12">
																	<div class="form-group">
																		<label for="maxRecentlyUsedPass">Maximum recently used password</label>
																		<input class="form-control" name="maxRecentlyUsedPass" id="maxRecentlyUsedPass" type="text" value="<?php echo $maxrecentpass;?>">
																	</div>	
																</div>
																<div class="col-sm-12">
																	<div class="form-group">
																		<label for="maxLoginAttempts">Maximum Login Attempts</label>
																		<input class="form-control" type="text"  name="maxLoginAttempts" id="maxLoginAttempts" value="<?php echo $logattempt;?>">
																	</div>
																</div>
																<div class="col-sm-12">
																	<div class="form-group">
																		<label for="maxPassExpiresDays">Maximum days before password expires</label>
																		<input class="form-control" type="text"  name="maxPassExpiresDays" id="maxPassExpiresDays" value="<?php echo $passExpire;?>">
																	</div>
																</div>	
															</div>
														</div>		
													</div>		
												</div>
												<div class="col-lg-3 col-md-6 col-sm-12" id="Billing_div">
													<div class="adminbox" style="min-height:110px;">
														<div class="head">
															<span>Billing Settings</span>
														</div>
														<div class="tblBg" id="refer_div">
															<div class="row">
																<div class="col-sm-6">
																	<div class="form-group">
																		<label for="billing_location_1"><strong>Billing Location</strong></label>
																		<div class="row">
																			<div class="col-sm-12">
																				<div class="radio radio-inline">
																					<input type="radio" name="billing_location" id="billing_location_1" value="1">
																					<label for="billing_location_1">Yes</label>	
																				</div>
																				<div class="radio radio-inline">
																					<input type="radio" name="billing_location" id="billing_location_2" value="2">
																					<label for="billing_location_2">No</label>	
																				</div>	
																			</div>	
																		</div>
																	</div>	
																</div>
																<div class="col-sm-6">
																	<div class="form-group">
																		<label for="billing_attention">Billing Attention</label>
																		<input type="text" name="billing_attention" id="billing_attention" value="<?php print $billing_attention?>" class="form-control">	
																	</div>
																</div>	
															</div>
														</div>	
													</div>
													<div class="adminbox" style="min-height:109px;">
														<div class="head">
															<span>e/Rx Settings</span>
														</div>
														<div class="tblBg" id="refer_div">
															<div class="row">
																<div class="col-sm-5">
																	<div class="form-group">
																	<label for="">Allow Medicare e/Rx</label>
																	<div class="clearfix"></div>
																		<div class="radio radio-inline">
																			<input type="radio" name="Allow_erx_medicare" id="Allow_erx_medicare_1" value="Yes" <?php if($Allow_erx_medicare == 'Yes') print 'checked="checked"'; ?>>
																			<label for="Allow_erx_medicare_1"> Yes </label>
																		</div>
																		<div class="radio radio-inline">
																			<input type="radio" name="Allow_erx_medicare" id="Allow_erx_medicare_2" value="No" <?php if($Allow_erx_medicare == 'No') print 'checked="checked"'; ?>>
																			<label for="Allow_erx_medicare_2"> No </label>
																		</div>
																	</div>
																</div>
																<div class="col-sm-7">
																	<div class="form-group">
																		<label for="">Auto Register Patient for e/RX</label>
																		<div class="clearfix"></div>
																		<div class="radio radio-inline">
																			<input type="radio" name="erx_entry" id="erx_entry_1" value="1" <?php if($erx_entry == 1) print 'checked="checked"'; ?>>
																			<label for="erx_entry_1"> Yes </label>
																		</div>
																		<div class="radio radio-inline">
																			<input type="radio" name="erx_entry" id="erx_entry_2" value="0" <?php if($erx_entry == 0) print 'checked="checked"'; ?>>
																			<label for="erx_entry_2"> No </label>
																		</div>
																	</div>
																</div>
															</div>
														</div>	
													</div>	
												</div>
												<div class="col-lg-3 col-md-6 col-sm-12" id="other_div">
													<div class="adminbox" style="margin-top:-24px;">
														<div class="head">
															<span>Other Settings</span>
														</div>
														<div class="tblBg" id="refer_div">
															<div class="row">
																<div class="col-sm-12">
																	<div class="row">
																		<div class="col-sm-5">
																			<div class="form-group">
																				<div class="checkbox checkbox-inline">
																					<input type="checkbox" name="ptinfodiv" id="ptinfodiv" value="1">
																					<label for="ptinfodiv">Patient Summary</label>	
																				</div>
																			</div>
																		</div>	
																		<div class="col-sm-3">
																			<div class="form-group">
																				<div class="checkbox checkbox-inline">
																					<input type="checkbox" name="mur_audit" id="mur_audit" value="1">
																					<label for="mur_audit">MUR Audit</label>	
																				</div>
																			</div>
																		</div>
																		<div class="col-sm-4">
																			<div class="form-group">
																				<div class="checkbox checkbox-inline">
																					<input type="checkbox" name="enable_hp" id="enable_hp" value="1">
																					<label for="enable_hp">Enable H&amp;P</label>	
																				</div>
																			</div>
																		</div>	
																	</div>	
																</div>
																<div class="col-sm-12">
																	<div class="form-group">
																		<div class="checkbox checkbox-inline">
																			<input type="checkbox" name="fd_pc" id="fd_pc" value="1">
																			<label for="fd_pc">Front Desk - Enable Present Correction</label>	
																		</div>	
																	</div>	
																</div>
																<div class="col-sm-12">
																	<label for=""><strong>eRx : Access</strong></label>
																	<div class="row">
																		<div class="col-sm-5">
																			<div class="form-group">
																				<div class="radio radio-inline">
																					<input type="radio" name="EmdeonUrl" id="EmdeonUrl_1" <?php if($EmdeonUrl == "https://cli-cert.changehealthcare.com"){echo "checked"; }?>  value="https://cli-cert.changehealthcare.com">
																					<label for="EmdeonUrl_1">Test</label>	
																				</div>	
																			</div>	
																		</div>
																		<div class="col-sm-5">
																			<div class="form-group">
																				<div class="radio radio-inline">
																					<input type="radio" name="EmdeonUrl" id="EmdeonUrl_2" <?php if($EmdeonUrl == "https://clinician.changehealthcare.com"){ echo "checked";}?> value="https://clinician.changehealthcare.com">
																					<label for="EmdeonUrl_2">Production</label>	
																				</div>		
																			</div>		
																		</div>	
																	</div>	
																</div>
																
																<div class="col-sm-6">
																	<label for=""><strong>Refractive Diagnosis</strong></label>
																	<div class="row">
																		<div class="col-sm-5">
																			<div class="form-group">
																				<div class="radio radio-inline">
																					<input type="radio" name="refdig" id="refdig_y" <?php if($refdig == "1"){echo "checked"; }?>  value="1">
																					<label for="refdig_y">Yes</label>	
																				</div>	
																			</div>	
																		</div>
																		<div class="col-sm-5">
																			<div class="form-group">
																				<div class="radio radio-inline">
																					<input type="radio" name="refdig" id="refdig_n" <?php if($refdig != "1"){echo "checked"; }?>  value="0">
																					<label for="refdig_n">No</label>	
																				</div>		
																			</div>		
																		</div>		
																	</div>	
																</div>
																<div class="col-sm-6">
																	<div class="row">
																		<div class="col-sm-12">
																			<div class="form-group">
																				<label for="pam_code">PAM Code</label>
																				<input type="text" name="pam_code" id="pam_code" value="" class="form-control">
																			</div>
																		</div>
																	</div>	
																</div>

																<div class="col-sm-6">
																	<label for="Confidential_psw"><strong>Confidential Password</strong></label>
																	<div class="row">
																		<div class="col-sm-12">
																			<div class="form-group">
																				<input type="password" name="Confidential_psw" id="Confidential_psw" value="" class="form-control">
																				<input type="hidden" name="Confidential_psw1" value="" >
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
								<div class="modal-footer"></div>	
							</div>	
						</div>	
					</div>
				</div>
			</div>
		</div>
<?php 
	include_once('../admin_footer.php');
?>