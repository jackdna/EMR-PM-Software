<?php 
$fax_name = $fax_numb = '';
$fax_name = (!empty($def_ref_phy_name)) ? $def_ref_phy_name : '';
$fax_numb = (!empty($def_ref_phy_fax)) ? $def_ref_phy_fax : '';
?>
<div class="common_wrapper">
	<!-- Encyption key modal -->
	<div id="div_enckey" class="modal fade" role="dialog">
		<div class="modal-dialog" style="position:relative">
			<div class="modal-content" style="z-index:99999999">
				<div class="modal-header bg-primary">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Encryption Key</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-sm-12">
							<label>Please enter encryption key to proceed</label>
						</div>
						<div class="col-sm-12 pt10">
							<div class="row">
								<div class="col-sm-4">
									<label>Encryption Key:</label>
								</div>
								<div class="col-sm-8">
									<input type="text" name="enc_key" id="enc_key" class="form-control">
								</div>	
							</div>	
						</div>	
					</div>	
				</div>
				<div id="module_buttons" class="ad_modal_footer modal-footer">
					<input type="button" name="submit_" id="submit_"  value="Submit" onClick="<?php echo $electronic_function; ?>('<?php echo $pid; ?>');" class="btn btn-success">	
				</div>	
			</div>
			<iframe class="fake_modal_iframe" src=""></iframe>	
		</div>
	</div> 
	
	<!-- Exclusion Modal -->
	<div id="exclusion_div" class="modal fade" role="dialog">
		<div class="modal-dialog" style="position:relative">
			<div class="modal-content" style="z-index:9;box-shadow: 1px 3px 5px #000;">
				<div class="modal-header bg-primary">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Exclusion</h4>
				</div>
				
				<div class="modal-body">
					<div id="faxDiv" class="row">
						<div class="col-sm-12">
							<fieldset>
								<legend>
									Common MU Data Set
								</legend>
							</fieldset>	
						</div>
						<div class="col-sm-12">
							<div class="row">
								<div class="col-sm-4">
									<div class="checkbox checkbox-inline">
										<input type="checkbox" title="Medications" id="med" name="chart_exclusion[]" value="mu_data_set_medications" >
										<label for="med" data-toggle="collapse" data-target="#pt_med_div">Medications</label>	
									</div>	
								</div>

								<div class="col-sm-4">
									<div class="checkbox checkbox-inline">
										<input type="checkbox" title="Problem List" id="problem" name="chart_exclusion[]" value="mu_data_set_problem_list"  >
										<label for="problem" data-toggle="collapse" data-target="#pt_problem_div">Problem List</label>	
									</div>	
								</div>

								<div class="col-sm-4">
									<div class="checkbox checkbox-inline">
										<input type="checkbox" title="Allergies" id="Allergies" name="chart_exclusion[]" value="mu_data_set_allergies"  >
										<label for="Allergies" data-toggle="collapse" data-target="#pt_allergy_div">Allergies List</label>	
									</div>	
								</div>	
							</div>
							<div class="row" style="position:relative;z-index:999">
								<div id="pt_med_div" class="col-sm-6 panel-collapse collapse" style="position:absolute">
									<div class="panel panel-default">
										<div class="panel-heading">Select medication to exclude</div>
										<div class="panel-body">
											<div class="row">
												<?php $m=0;
												$qryMedication="SELECT title,type FROM lists WHERE pid='".$cpr->patient_id."' AND type in(1,4) AND allergy_status='Active' ";
												$resMedication=imw_query($qryMedication);
												if(imw_num_rows($resMedication)>0){ $sysMed=$ocuMed="";
													while($rowMedication=imw_fetch_assoc($resMedication)){
														if($rowMedication['type']==4){
															$ocuMed.="&nbsp;<div class='checkbox checkbox-inline'><input type='checkbox' name='pt_med_exclusion[]' id='pt_med_exclusion_".$m."' value='".$rowMedication['title']."'><label for='pt_med_exclusion_".$m."'>&nbsp;".$rowMedication['title']."</label></div><br>";	
														}
														if($rowMedication['type']==1){
															$sysMed.="&nbsp;<div class='checkbox checkbox-inline'><input type='checkbox' name='pt_med_exclusion[]' id='pt_med_exclusion".$m."' value='".$rowMedication['title']."'><label for='pt_med_exclusion".$m."'>&nbsp;".$rowMedication['title']."</label></div><br>";	
														}
														$m++;
													}    
												}else{
													echo "No Record";	
												}
												if($ocuMed){
													echo "<span class=\"text_13b\" style=\"margin-top:5px;text-decoration:underline\" >Ocular Med</span><br>".$ocuMed;
												}
												if($sysMed){
													echo "<span class=\"text_13b\" style=\"margin-top:10px;text-decoration:underline\" >Systmic Med</span><br>".$sysMed;
												}
											?>	
												<div class="col-sm-12 pt10 text-center">
													<button type="button" class="btn btn-xs btn-success" onClick="$('#pt_med_div').removeClass('in')">Done</button>	
												</div>
											</div>
										</div>
											
									</div>	
								</div>
								<div id="pt_problem_div" class="col-sm-6 panel-collapse collapse"  style="position:absolute">
									<div class="panel panel-default">
										<div class="panel-heading">Select Problem to exclude</div>
										<div class="panel-body">
											<div class="row">
												<div class="col-sm-12">
													<?php
														$qryProblem="SELECT problem_name FROM pt_problem_list WHERE pt_id='".$cpr->patient_id."' AND status='Active'";
														$resProblem=imw_query($qryProblem);
															if(imw_num_rows($resProblem)>0){
															$counter = 0;
															  while($rowProblem=imw_fetch_assoc($resProblem)){
																	echo "&nbsp;<div class='checkbox checkbox-inline'><input type='checkbox' name='pt_prob_exclusion[]' id='pt_prob_exclusion_".$counter."' value='".$rowProblem['problem_name']."'><label for='pt_prob_exclusion_".$counter."'>&nbsp;".$rowProblem['problem_name']."</label></div><br>";	
																$counter++;	
																}    
														  }else{
																echo "No Record";	
															}
													?>	
												</div>
												<div class="text-center col-sm-12 pt10">
													<button type="button" class="btn btn-success btn-xs" onClick="$('#pt_problem_div').removeClass('in')">Done</button>	
												</div>
											</div>
										</div>
									</div>
								</div>
								<div id="pt_allergy_div" class="col-sm-6 panel-collapse collapse"  style="position:absolute">
									<div class="panel panel-default">
										<div class="panel-heading">Select Allergies to exclude</div>
										<div class="panel-body">
											<div class="row">
												<?php
													$qryMedicationAllegies="SELECT title FROM lists WHERE pid='".$_SESSION['patient']."' AND type=7 AND allergy_status='Active' ";
													$resMedicationAllegies=imw_query($qryMedicationAllegies);
													if(imw_num_rows($resMedicationAllegies)>0){
														$counter = 0;
														while($rowMedicationAllegies=imw_fetch_assoc($resMedicationAllegies)){
															echo "&nbsp;<div class='checkbox checkbox-inline'><input type='checkbox' name='pt_allergy_exclusion[]' id='pt_allergy_exclusion_".$counter."' value='".$rowMedicationAllegies['title']."'><label for='pt_allergy_exclusion_".$counter."'>&nbsp;".$rowMedicationAllegies['title']."</label></div><br>";	
															$counter++;
														}    
													}else{
														echo "No Record";	
													}
												?>
												<div class="col-sm-12 text-center pt10">
													<button type="button" class="btn btn-success btn-xs" onClick="$('#pt_allergy_div').removeClass('in')">Done</button>	
												</div>	
											</div>
											
										</div>
									</div>	
								</div>	
							</div>	
							<div class="row">
								<div class="col-sm-4">
									<div class="checkbox">
										<input type="checkbox" title="Smoking Status" id="smoke" name="chart_exclusion[]" value="mu_data_set_smoking" >
										<label for="smoke">Smoking Status</label>	
									</div>	
								</div>
								<div class="col-sm-4">
									<div class="checkbox">
										<input type="checkbox" title="Care Plan Field" id="care_plan" name="chart_exclusion[]" value="mu_data_set_ap" >
										<label for="care_plan">Care Plan Field</label>	
									</div>	
								</div>
								<div class="col-sm-4">
									<div class="checkbox">
										<input type="checkbox" title="Procedures" id="procedure" name="chart_exclusion[]" value="mu_data_set_superbill" >
										<label for="procedure">Procedures</label>	
									</div>	
								</div>
							</div>
							<div class="row">
								<div class="col-sm-4">
									<div class="checkbox">
										<input type="checkbox" id="vital_sign" title="Vital Sign" name="chart_exclusion[]" value="mu_data_set_vs" >
										<label for="vital_sign">Vital Sign</label>
									</div>	
								</div>	
								
								<div class="col-sm-4">
									<div class="checkbox">
										<input type="checkbox" id="care_team_members" title="Care Team Members" name="chart_exclusion[]" value="mu_data_set_care_team_members" >
										<label for="care_team_members">Care Team Members</label>
									</div>	
								</div>	

								<div class="col-sm-4">
									<div class="checkbox">
										<input type="checkbox" id="lab" title="Lab" name="chart_exclusion[]" value="mu_data_set_lab" >
										<label for="lab">Lab</label>
									</div>	
								</div>

								<div class="col-sm-4">
									<div class="checkbox">
										<input type="checkbox" id="superbillexc" title="Superbill" name="chart_exclusion[]" value="mu_data_set_superbill" >
										<label for="superbillexc">Superbill</label>
									</div>	
								</div>
								
							</div>	
						</div>
						<div class="col-sm-12 pt10">
							<fieldset>
								<legend>
									Other
								</legend>
							</fieldset>	
						</div>	
						<div class="col-sm-12">
							<div class="row">
								<div class="col-sm-6">
									<div class="checkbox checkbox-inline">
										<input type="checkbox" title="Provider Name and Contact" id="chart_exclusion_prov" name="chart_exclusion[]" value="provider_name_and_contact" >
										<label for="chart_exclusion_prov">Provider Name and Contact</label>	
									</div>	
								</div>	
								<div class="col-sm-6">
									<div class="checkbox checkbox-inline">
										<input type="checkbox" name="chart_exclusion[]" title="Date and Location of Visit" id="dos_facility_date" value="dos_facility" >
										<label for="dos_facility_date">Date and Location of Visit</label>	
									</div>	
								</div>		
							</div>
							<div class="row pt10">
								<div class="col-sm-6">
									<div class="checkbox checkbox-inline">
										<input type="checkbox" name="chart_exclusion[]" title="Reason of Visit" value="cc_and_history" id="cc_and_history_exclusion">
										<label for="cc_and_history_exclusion">Reason of Visit</label>	
									</div>	
								</div>
								<div class="col-sm-6">
									<div class="checkbox checkbox-inline">
										<input type="checkbox" name="chart_exclusion[]" title="Referrals to Other Providers" value="referrals_to_other_providers" id="referrals_to_other_providers_exclusion">
										<label for="referrals_to_other_providers_exclusion">Referrals to other Providers</label>	
									</div>	
								</div>	
							</div>
							<div class="row pt10">
								<div class="col-sm-6">
									<div class="checkbox checkbox-inline">
										<input type="checkbox" name="chart_exclusion[]" title="Diagnostic Tests Pending" id="diagnostic_tests_pending_exclusion" value="diagnostic_tests_pending">
										<label for="diagnostic_tests_pending_exclusion">Diagnostic Tests Pending</label>	
									</div>	
								</div>
								<div class="col-sm-6">
									<div class="checkbox checkbox-inline">
										<input type="checkbox" name="chart_exclusion[]" title="Clinical Instructions" id="clinical_instructions_exclusion" value="clinical_instructions">
										<label for="clinical_instructions_exclusion">Clinical Instructions</label>	
									</div>	
								</div>	
							</div>
							<div class="row pt10">
								<div class="col-sm-6">
									<div class="checkbox checkbox-inline">
										<input type="checkbox" name="chart_exclusion[]" title="Future Scheduled Tests" id="future_sch_test_exclusion" value="future_sch_test">
										<label for="future_sch_test_exclusion">Future Scheduled Tests</label>	
									</div>	
								</div>
								<div class="col-sm-6">
									<div class="checkbox checkbox-inline">
										<input type="checkbox" name="chart_exclusion[]" title="Future Appointment" value="future_appt" id="future_appt_exclusion">
										<label for="future_appt_exclusion">Future Appointment</label>	
									</div>	
								</div>	
							</div>
							<div class="row">
								<div class="col-sm-12 pt10">
									<div class="checkbox checkbox-inline">
										<input type="checkbox" name="chart_exclusion[]" title="Recommended patient decision aids" id="recommended_patient_decision_aids_exclusion"  value="recommended_patient_decision_aids">
										<label for="recommended_patient_decision_aids_exclusion">Recommended Patient Decision Aids</label>	
									</div>	
								</div>	
								<div class="col-sm-12 pt10">
									<div class="checkbox checkbox-inline">
										<input type="checkbox" name="chart_exclusion[]" title="Immunization and Medications Administered" value="visit_medication_immu" id="visit_medication_immu_exclusion">
										<label for="visit_medication_immu_exclusion">Immunization and Medications Administered</label>	
									</div>	
								</div>	
							</div>	
						</div>	
					</div>	
				</div>
				<div id="module_buttons" class="ad_modal_footer modal-footer">
					<div class="row">
						<div class="col-sm-12 text-center">
							<input type="button" class="btn btn-success" value="Done" onClick="check_exclusion('done');" >&nbsp;&nbsp;<input type="button" class="btn btn-danger" value="Cancel" onClick="check_exclusion('');" >	
						</div>	
					</div>
				</div>
			</div>
			<iframe class="fake_modal_iframe" src=""></iframe>
		</div>
	</div>
	
	
	<!-- Print disclosed modal -->
	<div id="divDisclosed" class="modal fade" role="dialog">
		<div class="modal-dialog" style="position:relative">
			<div class="modal-content" style="z-index:99999999">
				<div class="modal-header bg-primary">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Disclosed Details</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-sm-3">
							<label>Disclosed By</label>
							<select id="DisclosedBy" name="DisclosedBy" class="selectpicker" data-width="100%" data-size="5">
								<?php echo $cpr->get_disclosed_dos('dropdown','yes'); ?>
							</select>
						</div>
						<div class="col-sm-3">
							<label>Disclosed To</label>
							<input type="text" value="" name="DisclosedTo" id="DisclosedTo" class="form-control"/>	
						</div>
						<div class="col-sm-3">
							<label>Specialty</label>
							<input type="text" value="" name="Specialty" class="form-control"/>	
						</div>
						<div class="col-sm-3">
							<label>Reason</label>
							<input type="text" value="" name="Reason" class="form-control"/>	
						</div>
						<div class="col-sm-12 mt10">
						<?
							//get patient email  $_SESSION['patient']
							$ptEmailQ=imw_query("select email,concat(lname,', ',fname, ' ',UPPER(SUBSTRING(mname,1,1))) as patient_name from patient_data where id='$_SESSION[patient]'")or die(imw_error());
							$ptEmailD=imw_fetch_object($ptEmailQ);
							$pt_email=trim($ptEmailD->email);
							$patientNameID=$ptEmailD->patient_name." - ".$_SESSION[patient];
							?>
							<div class="checkbox">
								<input type="checkbox" name="mail2pt" value="1" id="mail2pt" <? echo ($pt_email)?'':' disabled'?>>
								<label for="mail2pt">Send mail to patient on </label>	
							</div>
													 
							<input type="text" name="ptmailid" id="ptmailid" value="<? echo ($pt_email)?$pt_email:'Email not registered.';?>" <? echo ($pt_email)?'readonly':'disabled';?> class="form-control">
							<input type="hidden" name="ptmailname" id="ptmailname" value="<? echo $patientNameID;?>">
						</div>
					</div>
				</div>
				<div id="module_buttons" class="ad_modal_footer modal-footer">
				
					<input type="hidden" value="0" name="faxEmail" id="faxEmail">
					<input type="submit" name="btncontinueprinting" value="Continue Printing"  onClick="return submitPrintRequest(true);" class="btn btn-primary">
				</div>
			</div>
			<iframe class="fake_modal_iframe" src=""></iframe>
		</div>
	</div>
	
	
	<!-- Send Fax modal -->
	<div id="send_fax_div" class="modal fade" role="dialog">
		<div class="modal-dialog" style="position:relative">
			<div class="modal-content" style="z-index:99999999">
				<div class="modal-header bg-primary">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Send Fax</h4>
				</div>
				<div class="modal-body">
					<div id="faxDiv" class="row">
						<div class="col-sm-6">
							<label>Referring Physician :</label>
							
							<!-- 
								
								Updating code to remove the typeahead issues (Multiple address popup comes again and again) on chart view.
								So the following code is removed from the input:text field
							 -->
							<input type="text" name="selectReferringPhy" id="selectReferringPhy" class="form-control" 
								onkeyup="loadPhysicians(this,'hiddselectReferringPhy','','send_fax_number','','','send_fax_number','2','',top.fmain);" value="<?php echo $fax_name; ?>">
							<!-- <input type="text" name="selectReferringPhy" id="selectReferringPhy" class="form-control" onfocus="loadPhysicians(this,'hiddselectReferringPhy','','','','','send_fax_number');" onkeyup="loadPhysicians(this,'hiddselectReferringPhy','','','','','send_fax_number');" onBlur="loadPhysicians(this,'hiddselectReferringPhy','','','','','send_fax_number');"> -->
							
							<input type="hidden" name="hiddselectReferringPhy" id="hiddselectReferringPhy">
						</div>
						<div class="col-sm-6">
							<label>Fax Number :</label>
							<input type="text"  name="send_fax_number" id="send_fax_number" class="form-control" value="<?php echo $fax_numb; ?>">	
						</div>	
					</div>
				</div>
				<div id="module_buttons" class="ad_modal_footer modal-footer">
					<button type="button" id="send_close_btn" class="btn btn-success">Send Fax</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</div>
			<iframe class="fake_modal_iframe" src=""></iframe>		
		</div>
	</div>
</div>
