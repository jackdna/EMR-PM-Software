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

include_once("../../../config/globals.php");
include_once($GLOBALS['srcdir']."/classes/merge_patients_class.php");
//include_once($GLOBALS['srcdir']."/html_to_pdf/fpdi/fpdi.php");

//$pdf = New FPDI;
$merge_obj = New Merge_patient($_SESSION['patient']);
if(!$merge_obj->patient_id) die;
$library_path = $GLOBALS['webroot'].'/library';
$data_arr = array();


//All values are loaded from class contructor and variables are extracted here
extract($merge_obj->extract_arr);
?>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		<title>Medical History:: imwemr ::</title>
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
        <?php if(constant('DEFAULT_PRODUCT') == "imwemr") { ?>
            <link href="<?php echo $library_path; ?>/css/imw_css.css" rel="stylesheet">
        <?php } ?>
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
		<script> 
			//var global_php_var = $.parseJSON('<?php echo $global_js_arr; ?>');
			var innerDim = window.opener.top.innerDim();
			if(innerDim['w'] < 1100) innerDim['w'] = 1100;
			if(innerDim['h'] < 700) innerDim['h'] = 700;
			window.onload=function(){
				window.resizeTo(innerDim['w'],innerDim['h']);
			}
		</script>	
		<script src="<?php echo $library_path; ?>/js/merge_patient.js" type="text/javascript"></script>	
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
			.nav-tabs > li.active > a, .nav-tabs > li.active > a:hover, .nav-tabs > li.active > a:focus{
				background-color:#727272 !important;
				color: #fff;
				font-weight:bold;
			}
			
			p{margin:0px!important}
			.nav-tabs.medical_import{border-bottom:none!important}
			
			
		</style>
	</head>
	<body>
		<div class="mainwhtbox">
			<!-- Extra Divs -->
			<div id="margeAlert" style="position:absolute;z-index:1000;width:495px; height:auto; top:80;left:170; display:none; border: 2px solid; background-color:#f3f3f3;" class="confirmTable3"></div>
			
			<div id="div_loading_image" class="col-sm-12 text-center" style="top:50%;margin-top: 0px; display: none;position:absolute;z-index:9999">
				<div class="loading_container">
					<div class="process_loader"></div>
					<div id="div_loading_text" class="text-info"></div>
				</div>
			</div>
			
			<!-- Main Div -->
			<div  id="merge_patient_form">
				<form name="frm" id="frm" action="" method="post" onSubmit="return merge_patient()">
					<input type="hidden" name="merge_patient_id" id="merge_patient_id" value="" />
					<input type="hidden" name="this_patient_id" id="this_patient_id" value="" />
					<input type="hidden" name="this_patient_name" id="this_patient_name" value="" />
					<input type="hidden" name="hid_action" id="hid_action" value="save" />
					<input type="hidden" name="medHXStatusPatient1" id="medHXStatusPatient1" value="<?php echo $intMedHxPerform; ?>" />
					<input type="hidden" name="medHXStatusPatient2" id="medHXStatusPatient2" value="" />
					<input type="hidden" name="patinetAccountTransactionStatus1" id="patinetAccountTransactionStatus1" value="<?php echo $patinetAccountTransaction; ?>"/>
					<input type="hidden" name="patinetAccountTransactionStatus2" id="patinetAccountTransactionStatus2" value=""/>
					<div class="row">
						<!-- Heading Row -->
						<div class="col-sm-12 purple_bar">
							<img src="<?php echo $library_path; ?>/images/merge_icon.png">&nbsp;<label>Merge Patient</label>	
						</div>

						<!-- Merging Columns -->
						<div class="col-sm-12 pt10">
							<div class="row">
								<div class="col-sm-5">
									<legend><label>Patient 1:</label></legend>
									<div class="row">
										<div class="col-sm-5">
											<label>Name</label>
											<input type="text" class="form-control"  name="merge_patient_name1" onKeyPress="searchPatientonPressEnter(event,this);" onBlur="searchPatient('1') "id="merge_patient_name1" value="<?php echo $full_name;?>">	
										</div>

										<div class="col-sm-3">
											<label>Patient Id:</label>
											<input type="text" value="<?php echo $merge_obj->patient_id;?>" id="merge_patient_id1" name="merge_patient_id1"  class="form-control" readonly >
										</div>

										<div class="col-sm-4">
											<label>&nbsp;</label>
											<select class="selectpicker" name="findBy1" id="findBy1" data-width="100%">
												 <option value="Active">Active</option>
												 <option value="Inactive">Inactive</option>
												 <option value="Deceased">Deceased</option>
												 <option value="Resp.LN">Resp.LN</option>
												 <option value="Ins.Policy">Ins.Policy</option>
											</select>	
										</div>	
									</div>
									<div class="clearfix"></div>
									<div class="row pt10">
										<div class="col-sm-5">
											<label id="pt_fac_patient1_label"><?php echo ($patientFacility != "" ? "Facility" : ""); ?> </label>
										</div>	
										
										<div class="col-sm-7">
											<span id="pt_fac_patient1">&nbsp;<?php echo $patientFacility;?></span>	
										</div>		
									</div>
									<div class="clearfix"></div>
									<div class="row">
										<div class="col-sm-5">
											<label id="dob_patient1_label">D.O.B.</label>
										</div>	
										
										<div class="col-sm-7">
											<span id="dob_patient1">&nbsp;<?php echo $dob;?></span>	
										</div>		
									</div>
									<div class="clearfix"></div>
									<div class="row">
										<div class="col-sm-5">
											<label id="contact_patient1_label">Contact No.</label>
										</div>	
										
										<div class="col-sm-7">
											<span id="contact_patient1">&nbsp;<?php echo $phone_home;?></span>	
										</div>		
									</div>
									<div class="clearfix"></div>
									<div class="row">
										<div class="col-sm-5">
											<label id="address_patient1_label">Address</label>
										</div>	
										
										<div class="col-sm-7">
											<span id="address_patient1">&nbsp;<?php echo $address;?></span>	
										</div>		
									</div>
									<div class="clearfix"></div>
									<div class="row">
										<div class="col-sm-5">
											<label id="pat_last_appt1_label">Last Appointment on</label>
										</div>	
										
										<div class="col-sm-7">
											<span id="pat_last_appt1">&nbsp;<?php echo $ptLastAddtDate." at ".$ptLastAddtTime;?></span>	
										</div>		
									</div>
									<div class="clearfix"></div>
									<div class="row">
										<div class="col-sm-5">
											<label id="pat_last_appt_proc1_label">Procedure</label>
										</div>	
										
										<div class="col-sm-7">
											<span id="pat_last_appt_proc1">&nbsp;<?php echo $ptLastAddtProcedure;?></span>	
										</div>		
									</div>
									<div class="clearfix"></div>
									<div class="row">
										<div class="col-sm-5">
											<label id="pat_last_appt_comment1_label">Comments</label>
										</div>	
										
										<div class="col-sm-7">
											<span id="pat_last_appt_comment1">&nbsp;<?php echo $ptLastAddtComments;?></span>	
										</div>		
									</div>	
									<div class="clearfix"></div>
									<div class="row">
										<div class="col-sm-5">
											<label id="primary_ins_patient1_label">Primary Ins.</label>
										</div>	
										
										<div class="col-sm-7 text-nowrap">
											<span id="primary_ins_patient1">&nbsp;<?php echo $insPriCompName.'-'.$insPriPolicyNo;?></span>	
										</div>		
									</div>
									<div class="clearfix"></div>
									<div class="row">
										<div class="col-sm-5">
											<label id="secondary_ins_patient1_label">Secondary Ins.</label>
										</div>	
										
										<div class="col-sm-7 text-nowrap">
											<span id="secondary_ins_patient1">&nbsp;<?php echo $insSecCompName.'-'.$insSecPolicyNo;?></span>	
										</div>		
									</div>
									<div class="clearfix"></div>
									<div class="row">
										<div class="col-sm-5">
											<label id="pat_outStanding_patient1_label">Patient outstanding</label>
										</div>	
										
										<div class="col-sm-7">
											<span id="pat_outStanding_patient1">&nbsp;<?php echo $pat_Due;?></span>	
										</div>		
									</div>
									<div class="clearfix"></div>
									<div class="row">
										<div class="col-sm-5">
											<label id="pat_ins_outStanding_patient1_label">Insurance outstanding</label>
										</div>	
										
										<div class="col-sm-7">
											<span id="pat_ins_outStanding_patient1">&nbsp;<?php echo $insurance_Due;?></span>	
										</div>		
									</div>	
								</div>
								
								<!-- Merge Buttons -->
								<div class="col-sm-2 text-center">
									<button type="button" id="merge_patient_button_right_to_left" class="btn btn-primary" onClick="merge_patient(this, 'merge_patient_id1', 'merge_patient_name1', 'merge_patient_id2','RTL')">&larr;</button>
									<button type="button" id="merge_patient_button_left_to_right" class="btn btn-primary" onClick="merge_patient(this, 'merge_patient_id2', 'merge_patient_name2', 'merge_patient_id1','LTR')">&rarr;</button>	
								</div>	
								
								<!-- Patient 2 block -->
								<div class="col-sm-5">
									<legend><label>Patient 2:</label></legend>	
									<div class="row">
										<div class="col-sm-5">
											<label>Name</label>
											<input type="text" class="form-control" name="merge_patient_name2" onBlur="searchPatient('2') "id="merge_patient_name2" onKeyPress="searchPatientonPressEnter(event,this);">
										</div>

										<div class="col-sm-3">
											<label>Patient Id:</label>
											<input type="text" value="" id="merge_patient_id2" name="merge_patient_id2"  class="form-control" readonly >
										</div>

										<div class="col-sm-4">
											<label>&nbsp;</label>
											<select class="selectpicker" name="findBy2" id="findBy2" data-width="100%">
												 <option value="Active">Active</option>
												 <option value="Inactive">Inactive</option>
												 <option value="Deceased">Deceased</option>
												 <option value="Resp.LN">Resp.LN</option>
												 <option value="Ins.Policy">Ins.Policy</option>
											</select>
										</div>	
									</div>
									<div class="clearfix"></div>
									<div class="row pt10">
										<div class="col-sm-5">
											<label id="pt_fac_patient2_label"></label>
										</div>	
										
										<div class="col-sm-7">
											<span id="pt_fac_patient2"></span>	
										</div>		
									</div>
									<div class="clearfix"></div>
									<div class="row">
										<div class="col-sm-5">
											<label id="dob_patient2_label"></label>
										</div>	
										
										<div class="col-sm-7">
											<span id="dob_patient2"></span>	
										</div>		
									</div>
									<div class="clearfix"></div>
									<div class="row">
										<div class="col-sm-5">
											<label id="contact_patient2_label"></label>
										</div>	
										
										<div class="col-sm-7">
											<span id="contact_patient2"></span>	
										</div>		
									</div>
									<div class="clearfix"></div>
									<div class="row">
										<div class="col-sm-5">
											<label id="address_patient2_label"></label>
										</div>	
										
										<div class="col-sm-7">
											<span id="address_patient2"></span>	
										</div>		
									</div>
									<div class="clearfix"></div>
									<div class="row">
										<div class="col-sm-5">
											<label id="pat_last_appt2_label"></label>
										</div>	
										
										<div class="col-sm-7">
											<span id="pat_last_appt2"></span>	
										</div>		
									</div>
									<div class="clearfix"></div>
									<div class="row">
										<div class="col-sm-5">
											<label id="pat_last_appt_proc2_label"></label>
										</div>	
										
										<div class="col-sm-7">
											<span id="pat_last_appt_proc2"></span>	
										</div>		
									</div>
									<div class="clearfix"></div>
									<div class="row">
										<div class="col-sm-5">
											<label id="pat_last_appt_comment2_label"></label>
										</div>	
										
										<div class="col-sm-7">
											<span id="pat_last_appt_comment2"></span>	
										</div>		
									</div>
									<div class="clearfix"></div>
									<div class="row">
										<div class="col-sm-5">
											<label id="primary_ins_patient2_label"></label>
										</div>	
										
										<div class="col-sm-7">
											<span id="primary_ins_patient2"></span>	
										</div>		
									</div>
									<div class="clearfix"></div>
									<div class="row">
										<div class="col-sm-5">
											<label id="secondary_ins_patient2_label"></label>
										</div>	
										
										<div class="col-sm-7">
											<span id="secondary_ins_patient2"></span>	
										</div>		
									</div>
									<div class="clearfix"></div>
									<div class="row">
										<div class="col-sm-5">
											<label id="pat_outStanding_patient2_label"></label>
										</div>	
										
										<div class="col-sm-7">
											<span id="pat_outStanding_patient2"></span>	
										</div>		
									</div>
									<div class="clearfix"></div>
									<div class="row">
										<div class="col-sm-5">
											<label id="pat_ins_outStanding_patient2_label"></label>
										</div>	
										
										<div class="col-sm-7">
											<span id="pat_ins_outStanding_patient2"></span>	
										</div>		
									</div>	
								</div>	
							</div>	
						</div>

						<!-- Buttons -->
						<div class="col-sm-12 pt10 text-center">
							<input class="btn btn-danger" type="button" value="Cancel" id="cancle_button" name="cancle_button" onClick="javascript:window.close()">					
						</div>	
					</div>
				</form>	
			</div>	
		</div>
	</body>	
</html>
<?php 

?>
