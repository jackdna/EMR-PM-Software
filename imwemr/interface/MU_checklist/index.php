<?php 
	include_once(dirname(__FILE__)."/../../config/globals.php");
	
	
	//Check patient session and closing popup if no patient in session
	$window_popup_mode = true;
	require_once($GLOBALS['srcdir']."/patient_must_loaded.php");
	
	$library_path = $GLOBALS['webroot'].'/library';
	include_once $GLOBALS['srcdir']."/classes/MUR_class.php";
	
	
	$pid = $_SESSION['patient'];
	$mur_obj = new MUR($pid);
	
	//Retrives all MUR data
	$mur_data_arr = $mur_obj->get_all_mur_data();
	//pre($mur_data_arr);
	extract($mur_data_arr);
?>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		<title>MUR Checklist</title>
		<!-- Bootstrap -->
		<link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
		<!-- Bootstrap Selctpicker CSS -->
		<link href="<?php echo $library_path; ?>/css/bootstrap-select.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/medicalhx.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css">
		<!-- Messi Plugin for fancy alerts CSS -->
			<link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet" type="text/css">
		<!-- DateTime Picker CSS -->
		<link rel="stylesheet" type="text/css" href="<?php echo $library_path; ?>/css/jquery.datetimepicker.min.css"/>
		
		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
			  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]--> 
		
		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js" type="text/javascript" ></script>
		<!-- jQuery's Date Time Picker -->
		<script src="<?php echo $library_path; ?>/js/jquery.datetimepicker.full.min.js" type="text/javascript" ></script>
		<!-- Bootstrap -->
		<script src="<?php echo $library_path; ?>/js/bootstrap.js" type="text/javascript" ></script>
		
		<!-- Bootstrap Selectpicker -->
		<script src="<?php echo $library_path; ?>/js/bootstrap-select.js" type="text/javascript"></script>
		<!-- Bootstrap typeHead -->
		<script src="<?php echo $library_path; ?>/js/bootstrap-typeahead.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/js/common.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/messi/messi.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/js/mur_func.js" type="text/javascript"></script>
		<style>
			.process_loader {
				border: 16px solid #f3f3f3;
				border-radius: 50%;
				border-top: 16px solid #3498db;
				width: 80px;
				height: 80px;
				-webkit-animation: spin 2s linear infinite;
				animation: spin 2s linear infinite;
				display: inline-block;
			}
			.adminbox{min-height:inherit}
			.adminbox label{overflow:initial;}
			.adminbox .panel-body{padding:5px}
		</style>
	</head>
	<body>
  	
    <div id="genHealthDiv_wv" class="modal" role="dialog"></div>
  
		<div class="mainwhtbox pd10">
			<div class="row">
				<div class="col-sm-12 purple_bar">
					<div class="row">
						<div class="col-sm-2">
							<label>MUR Checklist</label>	
						</div>
            
            <div class="col-sm-4 text-right">
							<label><?php echo $patient_name; ?></label>	
						</div>
            
						<div class="col-sm-6 text-right">
							<label>(Data is calculated assuming today as current DOS)</label>
						</div>	
					</div>
					
				</div>
				<div class="col-sm-12 pt10 headinghd">
					<h4>CORE &amp; MENU MEASURES</h4>
				</div>

				<div class="col-sm-12">
					<div class="row">
						<div class="col-sm-12">
							<ul id="accordion" class="list-group" role="tablist" aria-multiselectable="true">
								<li class="list-group-item">
									<?php
										$MeasureStatus = 'text-info';
										if($ptCPOE['numerator'] > 0)$MeasureStatus = 'text-success';
										else if($ptCPOE['denominator'] > 0 && $ptCPOE['numerator'] == 0) $MeasureStatus = 'text-danger';				
									?>
									<span class="<?php echo $MeasureStatus;?>">CPOE - Medication</span>	
								</li>
								<li class="list-group-item">
									<?php
										$MeasureStatus = 'text-info';
										if($ptCPOE2['numerator'] > 0)$MeasureStatus = 'text-success';
										else if($ptCPOE2['denominator'] > 0 && $ptCPOE2['numerator'] == 0) $MeasureStatus = 'text-danger';				
									?>	
									<span class="<?php echo $MeasureStatus;?>">CPOE - Imaging</span>	
								</li>
								<li class="list-group-item">
									<?php
										$MeasureStatus = 'text-info';
										if($ptCPOE3['numerator'] > 0)$MeasureStatus = 'text-success';
										else if($ptCPOE3['denominator'] > 0 && $ptCPOE3['numerator'] == 0) $MeasureStatus = 'text-danger';
									?>
									<span class="<?php echo $MeasureStatus;?>">CPOE - Laboratory</span>	
								</li>
								<li class="list-group-item">
									<?php $MeasureStatus = 'text-info';
										if($ptCPOE['denominator']>0 && $ptCPOE['denominator']-$ptCPOE['numerator']>0){ $MeasureStatus = 'text-danger';}
									?>	
									<span id="erx_tick" class="<?php echo $MeasureStatus;?> pointer" data-toggle="collapse" data-parent="#accordion" href="#erx_box">e-Prescribing (eRx)</span>
									<div id="erx_box" class="panel-collapse collapse">
										<div class="panel-body">
											
										</div>
									</div>
								</li>
								<li class="list-group-item">
									<?php
									$MeasureStatus = 'text-info';
									$toggle_var = '';	
									if(constant('IPORTAL')=='1'){
										if(count($ptAccess['denominator'])>0 && $ptAccess['denominator'] != $ptAccess['numerator']){$MeasureStatus='text-danger pointer'; $toggle_var = 'data-toggle="collapse" data-parent="#accordion" href="#pt_elec_access"';}
										else if(count($ptAccess['denominator'])>0 && $ptAccess['denominator'] == $ptAccess['numerator']){$MeasureStatus = 'text-success';}
									}?>
									<span class="<?php echo $MeasureStatus;?>" <?php echo $toggle_var; ?> >Patient Electronic Access - Provide Patient Access to PHI</span>
									<div id="pt_elec_access" class="panel-collapse collapse">
										<div class="panel-body">
											<?php echo $mur_obj->get_pt_elec_access(); ?>
										</div>
									</div>	
								</li>
								<li class="list-group-item">
									<?php $MeasureStatus = 'text-info'; ?>
									<span class="<?php echo $MeasureStatus;?>">Patient Electronic Access - Patient View Their PHI</span>	
								</li>
								<li class="list-group-item">
									 <?php
										$MeasureStatus = 'text-info';
										$toggle_var = '';
										if(count($ptEduRes['denominator'])>0 && $ptEduRes['denominator'] != $ptEduRes['numerator']){$MeasureStatus='text-danger pointer'; $toggle_var = 'data-toggle="collapse" data-parent="#accordion" href="#pt_spec_edu"';}
										else if(count($ptEduRes['denominator'])>0 && $ptEduRes['denominator'] == $ptEduRes['numerator']){$MeasureStatus = 'text-success';}?>
									<span class="<?php echo $MeasureStatus;?>" <?php echo $toggle_var; ?>>Use certified EHR technology to identify patient-specific education resources and provide to patient, if appropriate</span>
									<?php if(strpos($MeasureStatus,'anger pointer')>0){?>
										<input name="button" type="button"  class="pull-right btn btn-primary btn-xs" onClick="window.opener.$('.icon24_edu_doc').click();" value="Documents" />
									<?php }?>
									<div id="pt_spec_edu" class="panel-collapse collapse">
										<div class="panel-body">
											<b><i>HINT:</i></b> Patient Education material is not given for today's visit. Use the button provided right side.
										</div>
									</div>
								</li>
								
								<li class="list-group-item">
									<?php
										$MeasureStatus = 'text-info';
										$toggle_var = '';
										if(count($ptMedRecon['denominator'])>0 && $ptMedRecon['denominator'] != $ptMedRecon['numerator']){$MeasureStatus='text-danger pointer'; $toggle_var ='data-toggle="collapse" data-parent="#accordion" href="#med_reconcil"';}
										else if(count($ptMedRecon['denominator'])>0 && $ptMedRecon['denominator'] == $ptMedRecon['numerator']){$MeasureStatus = 'text-success';}
									?>
									<span class="<?php echo $MeasureStatus;?>" <?php echo $toggle_var; ?>>Medication reconciliation</span>
									<?php if(strpos($MeasureStatus,'anger pointer')>0){?><input name="button" type="button"  class="pull-right btn btn-xs btn-primary" id="reviewd" onClick="loadGenHealth();" value="Review Now" /><?php }?>
									<div id="med_reconcil" class="panel-collapse collapse">
										<div class="panel-body">
											<b><i>HINT:</i></b> Patient Medical History need to be reviwed. Use the button provided right side.
										</div>
									</div>	
								</li>
								
								<li class="list-group-item">
									<?php
										/*---NEW NUMERATOR LOGIC----*/
										$Q3 = "SELECT DISTINCT(patient_id) FROM patient_consult_letter_tbl 
												WHERE patient_id IN ($mur_obj->patient_id) AND status = '0' AND (date BETWEEN '$mur_obj->dos' AND '$mur_obj->dos')";
										$ptSumCare1['numerator'] 	= count($mur_obj->getPtIdFun($Q3,'patient_id'));echo imw_error();
										if($ptSumCare1['numerator']==0){
											$ptSumCare1['numerator'] = $ptSumCare2['numerator'];
										}
										$toggle_var = '';
										$MeasureStatus = 'text-info';
										if($ptSumCare1['numerator'] < $ptSumCare1['denominator']){$MeasureStatus='text-danger pointer'; $toggle_var ='data-toggle="collapse" data-parent="#accordion" href="#summary_care_phy"';}
										else if($ptSumCare1['numerator']>0 && $ptSumCare1['numerator'] == $ptSumCare1['denominator']){$MeasureStatus = 'text-success';}
									?>
									<span class="<?php echo $MeasureStatus;?>" <?php echo $toggle_var; ?>>Summary of Care - Provide Summary of Care Record to referring physician</span>
									<div id="summary_care_phy" class="panel-collapse collapse">
										<div class="panel-body">
											<b><i>HINT:</i></b> Patient Medical History need to be reviwed. Use the button provided right side.
										</div>
									</div>	
								</li>
								
								<li class="list-group-item">
									<span class="text-success">Patients use of secure electronic messaging</span>	
								</li>
								
								<li class="list-group-item">
									<span class="text-info">Security Risk Analysis performed</span>	
								</li>
								<li class="list-group-item">	
									<?php
										$toggle_var = '';
										$MeasureStatus = 'text-info';
										$q1 = "SELECT scp_status FROM scp_access WHERE LOWER(scp_status)='active' LIMIT 0,1";
										$res1 = imw_query($q1);
										if($res1 && imw_num_rows($res1)==1){
											$q = "SELECT * FROM alert_tbl where alert_created_console = '0' AND is_deleted='0' AND site_care_plan_name != '' AND status='1'";
											$res = imw_query($q);
											if(imw_num_rows($res)>=5) {$MeasureStatus = 'text-success';}
											else{$MeasureStatus='text-danger pointer'; $toggle_var = 'data-toggle="collapse" data-parent="#accordion" href="#pt_elec_message"';}
										}else{$MeasureStatus='text-danger pointer'; $toggle_var = 'data-toggle="collapse" data-parent="#accordion" href="#pt_elec_message"';}
									?>
									<span class="<?php echo $MeasureStatus;?>" <?php echo $toggle_var; ?>>Patients use of secure electronic messaging</span>
									<div id="pt_elec_message" class="panel-collapse collapse">
										<div class="panel-body">
											<b><i>HINT:</i></b> Patient Medical History need to be reviwed. Use the button provided right side.
										</div>
									</div>		
								</li>	
							</ul>
						</div>
					</div>	
				</div>

				<div class="col-sm-12 pt10 headinghd">
					<h4>SYSTEMIC CLINICAL QUALITY MEASURES</h4>	
				</div>
				
				<div class="col-sm-12">
					<div class="row">
						<div class="col-sm-12">
							<ul id="accordian2" class="list-group">
								<li class="list-group-item">
									<?php $MeasureStatus = 'text-info pointer'; ?>
									<span class="<?php echo $MeasureStatus;?>" data-toggle="collapse" data-parent="#accordion2" href="#NQF0018_HBP">NQF 0018 - Controlling High Blood Pressure</span>
									<div id="NQF0018_HBP" class="panel-collapse collapse">
										<div class="panel-body">
											<b><i>DENOMINATOR:</i></b><Br>&bull; Patient age 18-85.<br>
											&bull; Active Diagnosis of "Essential Hypertension" (401.0|401.1|401.9). [Medical Hx > Problem Lists]<br>
											&bull; Encounter performed/Visit recorded [Chartnote created]<br>
											<br>
											<b><i>EXCLUSION:</i></b><Br>&bull; Active diagnosis of any of these (pregnancy|end stage renal disease|chronic kidney diesease).<br>
											&bull; Procedure Performed any of these (kidney transplant|dialysis service|dialysis procedure|vascular access for dialysis)<br>
											<br>
											<b><i>NUMERATOR:</i></b><Br>&bull; Diastolic B/P recorded as result &lt; 90mmHG and Systolic B/P recorded as result &lt; 140mmHG.
										</div>
									</div>		
								</li>

								<li class="list-group-item"> 	
									<?php $MeasureStatus = 'text-info pointer'; if($NQF0022Pt==$mur_obj->patient_id){$MeasureStatus = 'text-success pointer';}?>
									<span class="<?php echo $MeasureStatus;?>" data-toggle="collapse" data-parent="#accordion2" href="#NQF0022_HRM">NQF 0022 - Use of High-Risk Medications in the Elderly: Measure I(1+)</span>
									<div id="NQF0022_HRM" class="panel-collapse collapse">
										<div class="panel-body">
											<b><i>DENOMINATOR:</i></b><Br>&bull; Patient age >= 66 yrs.<br>
											&bull; Encounter performed/Visit recorded with any of these procedures (99201|99202|99203|99204|99204|99211|99212|99213|99214|99215) [Chartnote created > Superbill procedure]<br>
											<br>
											<b><i>NUMERATOR:</i></b><Br>&bull; Medication given - High Risk Medications for Elderly [Medical Hx > Medications].
										</div>
									</div>	
								</li>

								<li class="list-group-item">
									<?php $MeasureStatus = 'text-info pointer'; if($NQF0022Pt==$mur_obj->patient_id){$MeasureStatus = 'text-success pointer';}?>
									<span class="<?php echo $MeasureStatus;?>" data-toggle="collapse" data-parent="#accordion2" href="#NQF0022_HRM_II">NQF 0022 - Use of High-Risk Medications in the Elderly: Measure II(2+)</span>
									<div id="NQF0022_HRM_II" class="panel-collapse collapse">
										<div class="panel-body">
											<b><i>DENOMINATOR:</i></b><Br>&bull; Patient age >= 66 yrs.<br>
											&bull; Encounter performed/Visit recorded with any of these procedures (99201|99202|99203|99204|99204|99211|99212|99213|99214|99215) [Chartnote created > Superbill procedure]<br>
											<br>
											<b><i>NUMERATOR:</i></b><Br>&bull; 2 or more Medication given - High Risk Medications for Elderly [Medical Hx > Medications].
										</div>
									</div>	
								</li>
									
								<li class="list-group-item">	
									 <?php
										$MeasureStatus = 'text-info pointer';
										if($NQF0421A['denominator']>0 && $NQF0421A['denominator'] == $NQF0421A['numerator']){$MeasureStatus = 'text-success pointer';}
									?>
									<span class="<?php echo $MeasureStatus;?>" data-toggle="collapse" data-parent="#accordion2" href="#NQF0421_BMI_65">NQF 0421 - Preventive Care and Screening: Body Mass Index (BMI) Screening and Follow-Up: 65+</span>
									<div id="NQF0421_BMI_65" class="panel-collapse collapse">
										<div class="panel-body">
											<b><i>DENOMINATOR:</i></b><Br>&bull; Patient age >= 66 yrs.<br>
											&bull; Encounter performed/Visit recorded with any of these procedures (90791 | 90792 | 90832 | 90834 | 90837 | 90839 | 96150 | 96151 | 96152 | 97001 | 97003 | 97802 | 97803 | 98960 | 99201 | 99202 | 99203 | 99204 | 99205 | 99212 | 99213 | 99214 | 99215 | D7140 | D7210 | G0101 | G0108 | G0270 | G0271 | G0402 | G0438 | G0439 | G0447) [Chartnote created > Superbill procedure]<br>
											&bull; Should not procedure performed: palliative care [Medical Hx > Sx/Procedures]<br>
											<br>
											<b><i>EXCLUSION:</i></b><Br>&bull; Active diagnosis of pregnancy.<br>
											<br>
											<b><i>NUMERATOR:</i></b><Br>&bull; If BMI is >=23 and < 30 Kg/m<sup>2</sup> [Medical Hx > VS]. <b>OR</b><br>
											&bull; If BMI is >=30 Kg/m<sup>2</sup>, Intervention performed 'Above Normal Follow-up' [Medical Hx > VS, Medical Hx > Sx/Proc.] <b>OR</b><br>
											&bull; If BMI is <23 Kg/m<sup>2</sup>, Intervention performed 'Below Normal Follow-up' [Medical Hx > VS, Medical Hx > Sx/Proc.] 
										</div>
									</div>	
								</li>

								<li class="list-group-item"> 	
									<?php
										$MeasureStatus = 'text-info pointer';
										if($NQF0421B['denominator']>0 && $NQF0421B['denominator'] == $NQF0421B['numerator']){$MeasureStatus = 'text-success pointer';}
									?>
									<span class="<?php echo $MeasureStatus;?>" data-toggle="collapse" data-parent="#accordion2" href="#NQF0421_BMI_18_65">NQF 0421 - Preventive Care and Screening: Body Mass Index (BMI) Screening and Follow-Up: 18-65</span>
									<div id="NQF0421_BMI_18_65" class="panel-collapse collapse">
										<div class="panel-body">
											<b><i>DENOMINATOR:</i></b><Br>&bull; Patient age >= 18 yrs and <=65 yrs.<br>
											&bull; Encounter performed/Visit recorded with any of these procedures (90791 | 90792 | 90832 | 90834 | 90837 | 90839 | 96150 | 96151 | 96152 | 97001 | 97003 | 97802 | 97803 | 98960 | 99201 | 99202 | 99203 | 99204 | 99205 | 99212 | 99213 | 99214 | 99215 | D7140 | D7210 | G0101 | G0108 | G0270 | G0271 | G0402 | G0438 | G0439 | G0447) [Chartnote created > Superbill procedure]<br>
											&bull; Should not procedure performed: palliative care [Medical Hx > Sx/Procedures]<br>
											<br>
											<b><i>EXCLUSION:</i></b><Br>&bull; Active diagnosis of pregnancy.<br>
											<br>
											<b><i>NUMERATOR:</i></b><Br>&bull; If BMI is >=18.5 and < 23 Kg/m<sup>2</sup> [Medical Hx > VS]. <b>OR</b><br>
											&bull; If BMI is >=25 Kg/m<sup>2</sup>, Intervention performed 'Above Normal Follow-up' [Medical Hx > VS, Medical Hx > Sx/Proc.] <b>OR</b><br>
											&bull; If BMI is <18.5 Kg/m<sup>2</sup>, Intervention performed 'Below Normal Follow-up' [Medical Hx > VS, Medical Hx > Sx/Proc.] 
										</div>
									</div>	
								</li>

								<li class="list-group-item">
									<?php
										$MeasureStatus = 'text-info pointer';
										if($CMS50V2['numerator']==0){
											$Q3 = "SELECT DISTINCT(patient_id) FROM patient_consult_letter_tbl 
													WHERE patient_id IN ($mur_obj->patient_id) AND status = '0' AND (date BETWEEN '$mur_obj->dos' AND '$mur_obj->dos')";
											$CMS50V2['numerator'] 	= count($mur_obj->getPtIdFun($Q3,'patient_id'));echo imw_error();
										}		
										if(count($CMS50V2['denominator'])>0 && $CMS50V2['denominator'] != $CMS50V2['numerator']){$MeasureStatus='text-danger pointer';}
										else if(count($CMS50V2['denominator'])>0 && $CMS50V2['denominator'] == $CMS50V2['numerator']){$MeasureStatus = 'text-success pointer';}
									?>
									<span class="<?php echo $MeasureStatus;?>" data-toggle="collapse" data-parent="#accordion2" href="#CMS50v2_ref_loop">CMS50v2 - Closing the Referral Loop: Receipt of specialist report</span>
									<div id="CMS50v2_ref_loop" class="panel-collapse collapse">
										<div class="panel-body">
											<b><i>DENOMINATOR:</i></b><Br>&bull; Patient having Referring Physician.<br>
											&bull; Intervention performed 'REFERRAL'.<br>
											&bull; Encounter performed/Patient visit recorded <br>
											<br>
											<b><i>NUMERATOR:</i></b><Br>&bull; Communication from provider to provider (Consult letter) done after Intervention performed 'REFERRAL'.
										</div>
									</div>	
								</li>
								
								<li class="list-group-item">	
									<?php
										$MeasureStatus = 'text-info pointer';
										if(count($NQF0028a['denominator'])>0 && $NQF0028a['denominator'] != $NQF0028a['numerator']){$MeasureStatus='text-danger pointer';}
										else if(count($NQF0028a['denominator'])>0 && $NQF0028a['denominator'] == $NQF0028a['numerator']){$MeasureStatus = 'text-sucess pointer';}
									?>
									<span class="<?php echo $MeasureStatus;?>" data-toggle="collapse" data-parent="#accordion2" href="#NQF0028_tobacco_use">NQF 0028 - Tobacco Use: Screening and Cessation Intervention</span>
									<div id="NQF0028_tobacco_use" class="panel-collapse collapse">
										<div class="panel-body">
											<b><i>DENOMINATOR:</i></b><Br>&bull; Patient age >= 18 yrs.<br>
											&bull; 2 or more Encounter performed/Patient visit recorded with superbill<br>
											<br>
											<b><i>EXCEPTIONS:</i></b><Br>&bull; Active diagnosis like <i>Terminal Illness</i> or <i>Limited Life Expectancy</i>.<br>
											<br>
											<b><i>NUMERATOR:</i></b><Br>&bull; Patient identified as 'Non-Smoker'. <b>OR</b><br>
											&bull; Patient identified as Smoker and cessation counseling given.
										</div>
									</div>	
								</li>
								
								<li class="list-group-item">
									<?php $MeasureStatus = 'text-info pointer'; ?>
									<span class="<?php echo $MeasureStatus;?>" data-toggle="collapse" data-parent="#accordion2" href="#NQF0052_imaging_studies">NQF 0052 - Use of Imaging Studies for Low Back Pain</span>
									<div id="NQF0052_imaging_studies" class="panel-collapse collapse">
										<div class="panel-body">
											<b><i>DENOMINATOR:</i></b><Br>&bull; Patient age >= 18 yrs and <= 50 yrs.<br>
											&bull; Active diagnosis of 'low back pain'.<br>
											<br>
											<b><i>EXCLUSIONS:</i></b><Br>&bull; Active/Inactive/Resolved diagnosis of any type of 'Cancer'. <b>OR</b><br>
											&bull; Active diagnosis of <i>Trauma</i> OR <i>IV Drug Abuse</i> OR <i>Neurologic Impairment</i> within 365 days before start of MU period.<br>
											<br>
											<b><i>NUMERATOR:</i></b><Br>&bull; Not performed 'X-RAY OF LOWER SPINE'. <b>OR</b><br>
											&bull; Not performed 'MRI OF LOWER SPINE'. <b>OR</b><br>
											&bull; Not performed 'CT-SCAN OF LOWER SPINE'.
										</div>
									</div>	
								</li>

								<li class="list-group-item">
									<?php 
										$MeasureStatus = 'text-info pointer';
										if(count($NQF0419['denominator'])>0 && $NQF0419['denominator'] != $NQF0419['numerator']){$MeasureStatus='text-danger';}else if(count($NQF0419['denominator'])>0 && $NQF0419['denominator'] == $NQF0419['numerator']){$MeasureStatus = 'text-success';}	
									?>
									<span class="<?php echo $MeasureStatus;?>">NQF 0419 - Medication Reconciliation</span>	
								</li>
								
								<li class="list-group-item">
									<?php 
										$MeasureStatus = 'text-info pointer';
										if(count($NQF0101['denominator'])>0 && $NQF0101['denominator'] != $NQF0101['numerator']){$MeasureStatus='text-danger';}else if(count($NQF0101['denominator'])>0 && $NQF0101['denominator'] == $NQF0101['numerator']){$MeasureStatus = 'text-success';}
									?>
									<span class="<?php echo $MeasureStatus;?>">NQF 0101 - Screening for Future Falls Risk</span>
								</li>	
							</ul>	
						</div>	
					</div>
				</div>
				
				<div class="col-sm-12 pt10 headinghd">
					<h4>Clinical Quality Measure - Ophthalmology</h4>	
				</div>
				
				<div class="col-sm-12">
					<div class="row">
						<div class="col-sm-12">
							<ul class="list-group">
								<li class="list-group-item">
									<?php $MeasureStatus = 'text-info';
									if(count($NQF0566['denominator'])>0 && $NQF0566['denominator'] != $NQF0566['numerator']){$MeasureStatus='text-danger';}else if(count($NQF0566['denominator'])>0 && $NQF0566['denominator'] == $NQF0566['numerator']){$MeasureStatus = 'text-success';}?>
									<span class="<?php echo $MeasureStatus;?>">NQF 0566 - ARMD - Antioxidant Counseling</span>	
								</li>

								<li class="list-group-item"> 	
									<?php
										$MeasureStatus = 'text-info pointer';
										if(count($NQF0086['denominator'])>0 && $NQF0086['denominator'] != $NQF0086['numerator']){$MeasureStatus='text-danger pointer';}else if(count($NQF0086['denominator'])>0 && $NQF0086['denominator'] == $NQF0086['numerator']){$MeasureStatus = 'text-success';}
									?>
									<span class="<?php echo $MeasureStatus;?>" data-toggle="collapse" data-parent="#accordion2" href="#NQF0086_POAG">NQF 0086 - Primary Open Angle Glaucoma (POAG): Optic Nerve Evaluation</span>
									<div id="NQF0086_POAG" class="panel-collapse collapse">
										<div class="panel-body">
											<b><i>DENOMINATOR:</i></b><Br>&bull; Patient age >= 18 yrs.<br>
											&bull; At least two office visits during MU period (<i>*Ignored here in checklist</i>).<br>
											&bull; Having active diagnosis of 'Primary Open Angle Glaucoma (POAG)'.<br>
											<br>
											<b><i>NUMERATOR:</i></b><Br>&bull; Having Optic-Nerve exam performed with C:D% recorded.
										</div>
									</div>
								</li>

								<li class="list-group-item">
									<?php
										$MeasureStatus = 'text-info';
										if(count($NQF0087['denominator'])>0 && $NQF0087['denominator'] != $NQF0087['numerator']){$MeasureStatus='text-danger';}else if(count($NQF0087['denominator'])>0 && $NQF0087['denominator'] == $NQF0087['numerator']){$MeasureStatus = 'text-success';}
									?>
									<span class="<?php echo $MeasureStatus;?>">NQF 0087 - ARMD - Dilated Macular Exam</span>	
								</li>

								<li class='list-group-item'>
									<?php
										$MeasureStatus = 'text-info pointer';
										if(count($NQF0088['denominator'])>0 && $NQF0088['denominator'] != $NQF0088['numerator']){$MeasureStatus='text-danger pointer';}
										else if(count($NQF0088['denominator'])>0 && $NQF0088['denominator'] == $NQF0088['numerator']){$MeasureStatus = 'text-success';}
									?>
									<span class="<?php echo $MeasureStatus;?>" data-toggle="collapse" data-parent="#accordion2" href="#NQF0088_PAMELSR">NQF 0088 - Diabetic Retinopathy: Documentation of Presence or Absence of Macular Edema and Level of Severity of Retinopathy</span>
									<div id="NQF0088_PAMELSR" class="panel-collapse collapse">
										<div class="panel-body">
											<b><i>DENOMINATOR:</i></b><Br>&bull; Patient age >= 18 yrs.<br>
											&bull; At least two office visits during MU period (<i>*Ignored here in checklist</i>).<br>
											&bull; Having active diagnosis of 'Diabetic Retinopathy' (250.50|250.51|362.01|362.02|362.03|362.04|362.05|362.06|362.07).<br>
											<br>
											<b><i>NUMERATOR:</i></b><Br>&bull; Having Retinal exam performed with 'Macular Edema' and 'NPDR' recorded under 'DR' (Diabetic Retinopathy) section.
										</div>
									</div>	
								</li>

								<li class="list-group-item"> 	
									<?php
										$MeasureStatus = 'text-info pointer';
										if(count($NQF0089['denominator'])>0 && $NQF0089['denominator'] != $NQF0089['numerator']){
											$MeasureStatus='text-danger pointer';
											if(count($NQF0089['denominator'])>0 && $NQF0089['denominator'] == $NQF0089['exclusion']){
												if($ptSumCare2['numerator']==$patient_id){$MeasureStatus = 'text-success pointer';}
											}
										}
										else if(count($NQF0089['denominator'])>0 && $NQF0089['denominator'] == $NQF0089['numerator']){$MeasureStatus = 'text-success pointer';}
									?>
									<span class="<?php echo $MeasureStatus;?>" data-toggle="collapse" data-parent="#accordion2" href="#NQF0089_DRCWPMODC">NQF 0089 - Diabetic Retinopathy: Communication with the Physician Managing Ongoing Diabetes Care</span>
									<div id="NQF0089_DRCWPMODC" class="panel-collapse collapse">
										<div class="panel-body">
											<b><i>DENOMINATOR:</i></b><Br>&bull; Patient age >= 18 yrs.<br>
											&bull; At least two office visits during MU period (<i>*Ignored here in checklist</i>).<br>
											&bull; Having active diagnosis of 'Diabetic Retinopathy' (250.50|250.51|362.01|362.02|362.03|362.04|362.05|362.06|362.07).<br>
											<br>
											<b><i>NUMERATOR:</i></b><Br>&bull; Having Retinal exam performed with 'Macular Edema' recorded under 'DR' (Diabetic Retinopathy) section.<br>
											&bull; Consult letter generated regarding findings of Macular Edema withing last 12 months.
										</div>
									</div>	
								</li>

								<li class="list-group-item">
									<?php
										$MeasureStatus = 'text-info pointer';
										if(count($NQF0055['denominator'])>0 && $NQF0055['denominator'] != $NQF0055['numerator']){$MeasureStatus='text-danger pointer';}
										else if(count($NQF0055['denominator'])>0 && $NQF0055['denominator'] == $NQF0055['numerator']){$MeasureStatus = 'text-success pointer';}
									?>
									<span class="<?php echo $MeasureStatus;?>" data-toggle="collapse" data-parent="#accordion2" href="#NQF0055_diabetes_eye_exam">NQF 0055 - Diabetes: Eye Exam</span>
									<div id="NQF0055_diabetes_eye_exam" class="panel-collapse collapse">
										<div class="panel-body">
											<b><i>DENOMINATOR:</i></b><Br>&bull; Patient age between 18-75 yrs.<br>
											&bull; At least one office visits during MU period.<br>
											&bull; Having active diagnosis of 'Diabetese' (250.00|250.01|250.02) from within 2 years from start of MU period.<br>
											<br>
											<b><i>EXCLUSION:</i></b><Br>&bull; Having active diagnosis of 'Gestational Diabetes'.<br>
											<br>
											<b><i>NUMERATOR:</i></b><Br>&bull; Having any of the Eye Exam done.<br>
										</div>
									</div>		
								</li>	
							</ul>	
						</div>	
					</div>	
				</div>

				<div class="col-sm-12 pt10 text-center ad_modal_footer" id="module_buttons">
					<button class="btn btn-danger" onClick="window.close()">Close</button>
				</div>	
			</div>	
		</div>
	</body>
</html>	
