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
//Check patient session and closing popup if no patient in session
$window_popup_mode = true; 
require_once($GLOBALS['srcdir']."/patient_must_loaded.php");
include_once($GLOBALS['fileroot'].'/interface/chart_notes/chart_globals.php');
include_once($GLOBALS['srcdir']."/classes/complete_pt_record.class.php");
$pid = $_SESSION['patient'];
$_SESSION["PHI_Audit"] = "Noo";
$call_from = 'wv';
if(isset($_REQUEST['call_from']) && trim($_REQUEST['call_from']) != ''){
	$call_from = $_REQUEST['call_from'];
}
$cpr = New CPR($pid,$call_from);
$call_from = $cpr->call_from;
$library_path = $GLOBALS['webroot'].'/library';

//Typeahead Arr
$typeahead_arr = $cpr->get_reff_phy_typeahead();

if($call_from == 'wv'){
	$electronic_function = 'getElectronicDataWV';
}else{
	$electronic_function = 'getElectronicData';
}

$disclosed_data = $cpr->get_disclosed_data();

//PHP variables to be used in JS File
$global_js_arr = array();
$global_js_php_var['chk_arr'] = array('cbkDemographics','cbkMedHx','cbkInsurance');
$global_js_php_var['call_from'] = $call_from;
$global_js_php_var['typeahead_arr'] = str_replace(array('\'','\"'),array('',''),$typeahead_arr);
$global_js_arr = json_encode($global_js_php_var);
$ptInfoOnHeader = core_get_patient_name($pid);

//---Print Patient Record Send Fax Modal Pre-filled With Reff Physician Info---
$def_ref_phy_id = $def_ref_phy_name = $def_ref_phy_fax = "";
if($pid)
{
	$qry_reff_id = "SELECT primary_care_id FROM patient_data WHERE id = '".$pid."'";
	$res_reff_id = imw_query($qry_reff_id);
	if(imw_num_rows($res_reff_id)> 0)
	{
		$row_reff_id = imw_fetch_assoc($res_reff_id);	
		$def_ref_phy_id= trim($row_reff_id['primary_care_id']);
		if($def_ref_phy_id && $def_ref_phy_id!= 0 && $def_ref_phy_id!='')
		{	
			$qry_reff_info = "SELECT FirstName, LastName, physician_fax FROM refferphysician WHERE physician_Reffer_id = '".$def_ref_phy_id."'";
			$res_reff_info = imw_query($qry_reff_info);
			if(imw_num_rows($res_reff_info)> 0)
			{
				$row_reff_info	= imw_fetch_assoc($res_reff_info);	
				$ref_phy_fname	= trim($row_reff_info['FirstName']);
				$ref_phy_lname	= trim($row_reff_info['LastName']);
				$ref_phy_fax	= trim($row_reff_info['physician_fax']);
			
				$def_ref_phy_name = $ref_phy_lname.', '.$ref_phy_fname;
				$def_ref_phy_fax  = $ref_phy_fax;
			}
			else
			{
				$ref_fax_details = $typeahead_arr['stringAllPhyFax'][$def_ref_phy_id];	
				$ref_fax_info 	 = explode('@@',$ref_fax_details); 
				$def_ref_phy_name= $ref_fax_info[0];
				$def_ref_phy_fax = $ref_fax_info[1];
			}	
		}
	}		
}	
?>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		<title>Print Complete Patient Record</title>
		<!-- Bootstrap -->
		<link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
		<!-- Bootstrap Selctpicker CSS -->
		<link href="<?php echo $library_path; ?>/css/bootstrap-select.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/report.css" rel="stylesheet" type="text/css">
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
        <script src="<?php echo $library_path; ?>/js/core_main.js" type="text/javascript"></script>
		<script> 
			var global_php_var =<?php echo $global_js_arr; ?>;
		</script>
		<script src="<?php echo $library_path; ?>/js/print_func.js?<?php echo filemtime('../../../library/js/print_func.js'); ?>" type="text/javascript"></script>	
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
			form{margin-bottom:0px;}
			.adminbox{min-height:inherit}
			.adminbox label{overflow:initial;}
			.fltimg {
				position: absolute;
				left: 28px
			}
			.fltimg span.glyphicon {
				position: absolute;
				background:#49265d;
				color: #fff;
			}
			.pd5.report-content {
				position: relative;
				background-color: #EAEFF5;
			}
			.vertical-text{
				transform: rotate(90deg);
				transform-origin: left top 0;
				text-transform: uppercase;
				font-family: robotobold;
				font-size: 15px;
				position: absolute;
				letter-spacing: 1px;
				top: 20px;
				z-index:999;
			}
			
			.vertical-text .filter_block{
				background: #77548a;
				color: #fff;
				padding: 10px;
				border-radius: 5px 0px 0px 0px;
			}
			
			.fltimg span.glyphicon {
				position: absolute;
				background: #49265d;
				left: 100%;
				color: #fff;
				padding: 12px;
				top: -10px;
				border-radius: 0px 5px 0px 0px;
			}
			#content_main_box{
				position:relative;
			}
			.ad_modal_footer{
				border-bottom-left-radius: 0px;
				border-bottom-right-radius: 0px;
			}
			#fake_iframe{
				z-index: 2;
				top: 17px;
				background:transparent;
				position: absolute;
				width: 41px;
				height: 27%;	
				border:1px solid transparent;
			}
			
			.fake_modal_iframe{
				border: 1px solid transparent;
				position: absolute;
				width: 100%;
				top: 0px;
				z-index: 3;
				height: 100%;
			}
			.modal-backdrop{
				background-color:transparent!important;	
			}
			.visitDropDown{
				width:99%!important;	
			}
			.tests .dropdown-menu ul>li {
            	min-width: 135px!important;
			}
		</style>
		<script>
			$('#div_loading_image').show();
		</script>
	</head>
	<body>
		<div class="container-fluid">
				<div class="anatreport">
					<div class="row purple_bar">
						<div class="col-sm-3">
							<label>Print Patient Record</label>
						</div>
						<div class="col-sm-3 text-center">
							<?php echo $ptInfoOnHeader[4]; ?>
						</div>
						<div class="col-sm-6 text-right">
							<?php 
								if(count($disclosed_data) > 0)
									echo '<button type="button" class="btn btn-success" data-toggle="modal" data-target="#myModal">Disclosed Information</button>';
							?>
						</div>	
					</div>
					<div class="row" id="row-main">
						<!--<form name="print_function" id="print_function" action="process_pt_print_req.php" method="get">-->
						
							<div class="col-sm-3" id="sidebar">
							<form name="print_function" id="print_function" action="process_pt_print_req.php" method="POST">
							<input type="hidden" value="<?php echo $_REQUEST['print_form_id']; ?>" name="print_form_id">
							<input type="hidden" value="" id="hidexport_report" name="hidexport_report">
							<input type="hidden" value="<?php echo $cpr->facesheetTemplateExist; ?>" name="facesheetTemplateExist" id="facesheetTemplateExist">
							<input type="hidden" value="0" name="faxSubmit" id="faxSubmit">
							<input type="hidden" value="0" name="emailSubmit" id="emailSubmit">
							<input type="hidden" name="submit_request" value="true" id="submit_request">
								<div class="reportlft">
									<div class="practbox">
										<div class="clearfix"></div>
										<div class="pd5">
											<div class="row">
												<div class="col-sm-6">
													<div class="checkbox">
														<input type="checkbox" name="glaucoma" value="1" id="glaucoma" onClick="checkAll(false); vis1(false); vis2(false); AscanOption(false);">
														<label for="glaucoma">Glaucoma Flow Sheet</label>	
													</div>
												</div>
												<div class="col-sm-6">
													<div class="checkbox">
														<input type="checkbox" name="special_all[]"  id="chkspecial_all" onClick="checkAll(false); vis1(false); vis2(false); GlaucomaOption(false);" value="../main/ascanPrint.php">
														<label for="chkspecial_all">A/Scan</label>	
													</div>	
												</div>
											</div>
										</div>
									</div>
									<!-- Clinical Block -->
									<div class="practbox">
										<div class="anatreport">
											<h2>
											<?php if($call_from == 'wv'){ ?>
												Clinical Summary
											<?php }else{ ?>
												Chart Notes
											<?php } ?>
											</h2>	
										</div>
										<div class="clearfix"></div>
										<div class="pd5">
											<div class="row">
												<div class="col-sm-6">
													<div class="checkbox">
														<input type="checkbox"  name="chart_nopro[]" value="All" onClick="checkAll(this.checked)" id="chkChartAll">
														<label for="chkChartAll">All</label>	
													</div>	
												</div>
												<div class="col-sm-6">
													<div class="checkbox">
														<input type="checkbox"  name="chart_nopro[]" value="Record Release" id="chkRecordRelease" onClick="clinical_selected()" <?php ($_SESSION['sess_privileges']['priv_record_release']== true) ? "" : print "disabled"; ?>>
														<label for="chkRecordRelease">Record Release</label>	
													</div>		
												</div>
											</div>
											<div class="row">
												<div class="col-sm-6">
													<div class="checkbox checkbox-inline">
														<input type="checkbox"  name="chart_nopro[]" value="Modification H." id="chkPatientModiHx" onClick="clinical_selected()">
														<label for="chkPatientModiHx">Modification History</label>
													</div>	
												</div>
												<div class="col-sm-6">
													<div class="checkbox checkbox-inline">
														<input type="checkbox"  name="chart_nopro[]" value="HIPPA" id="chkChartHippa" onClick="clinical_selected()">
														<label for="chkChartHippa">HIPAA</label>	
													</div>
												</div>
											</div>	
											<div class="row">
												<div class="col-sm-6">
													<div class="checkbox">
														<input type="checkbox"  name="chart_nopro[]" value="Chart Notes" id="chkChartNotes" onClick="clinical_selected()">
														<label for="chkChartNotes">Chart Notes</label>	
													</div>
												</div>
												<div class="col-sm-6">
													<label>This Visit:</label>
													<select class="selectpicker visitDropDown" data-size="4" name='formIdToPrint[]'  id='formIdToPrint'  multiple data-actions-box="true" style="width:85%!important; ">
														<?php echo $cpr->get_form_dos(); ?>
													</select>
												</div>	
											</div>
											<div class="row">
												<div class="col-sm-6">
													<div class="checkbox">
														<input type="checkbox"  name="chart_nopro[]" value="Medical History" id="chkMedicalHistory" onClick="unCheckMedicationAllergies(this.checked); clinical_selected()">
														<label for="chkMedicalHistory">Medical History</label>	
													</div>	
												</div>
												<div class="col-sm-6">
													<div class="checkbox">
														<input type="checkbox"  name="chart_nopro[]" value="Include Provider Notes" id="chkIncludeNotes" onClick="clinical_selected()">
														<label for="chkIncludeNotes">Include Provider Notes</label>	
													</div>	
												</div>	
											</div>	
											<div class="row">
												<div class="col-sm-6">
													<div class="checkbox">
														<input type="checkbox"  name="chart_nopro[]" value="Diagnostic Tests" id="chkDiagnosticTests" onClick="clinical_selected()">
														<label for="chkDiagnosticTests">Diagnostic Tests</label>	
													</div>	
												</div>
												<div class="col-sm-6">
													<div class="checkbox">
														<input type="checkbox"  name="chart_nopro[]" value="Patient LegalForms" id="chkPatientLegalforms" onClick="clinical_selected()">
														<label for="chkPatientLegalforms">Include Legal Forms</label>	
													</div>
												</div>	
											</div>
											<div class="row">
												<div class="col-sm-6">
													<div class="checkbox">
														<input type="checkbox"  name="chart_nopro[]" value="Patient Amendment" id="chkPatientAmendment" onClick="clinical_selected()">
														<label for="chkPatientAmendment">Include Patient Amendment</label>	
													</div>
												</div>
												<div class="col-sm-6">
													<div class="checkbox">
														<input type="checkbox"  name="chart_nopro[]" value="Patient Demographics" id="chkPatientDemographics" onClick="clinical_selected()">
														<label for="chkPatientDemographics">Include Demographics</label>	
													</div>
												</div>	
											</div>
											
											<div class="row">
												<div class="col-sm-12">
													<div class="checkbox">
														<input type="checkbox"  name="chart_nopro[]" value="Patient Communication" id="chkPatientCommunication" onClick="clinical_selected()">
														<label for="chkPatientCommunication">Include Patient Communication</label>	
													</div>
												</div>
												<div class="col-sm-6">
												</div>	
											</div>	
										</div>
									</div>
									
									<!-- Exclusion Block -->
									<?php if($call_from == 'wv'){ ?>
										<div class="practbox">
											<div class="anatreport">
												<h2 class="text_purple pointer" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#exclusion_div" data-controls-modal="#exclusion_div">Exclusion</h2>
												<div id="exlude_innerHTML" class="col-sm-12"></div>		
											</div>
											<div class="clearfix"></div>
											<div class="pd5">
												<div class="row">
													<?php echo $cpr->get_test_data('dropdown',4); ?>
												</div>
											</div>
										</div>
									<?php } ?>
									
									<!-- Summary Block -->
									<div class="practbox">
										<div class="anatreport">
											<h2>Summary</h2>
										</div>
										<div class="clearfix"></div>
										<div class="pd5 summary_box">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="checkbox">
                                                        <input type="checkbox" id="chk_summary_box" name="chk_summary_box[]" value="All" onclick="summary_box_change();">
                                                        <label for="chk_summary_box">All</label>	
                                                    </div>	
                                                </div>
                                            </div>
											<div class="row">
												<div class="col-sm-5">
													<div class="checkbox checkbox-inline">
														<input type="checkbox" name="chart_nopro[]" value="Problem List" id="chkProblemList" class="checkActive" linked-radio="problem_testActive">
														<label for="chkProblemList">Problem List</label>	
													</div>
												</div>
												<div class="col-sm-4">
													<div class="radio radio-inline">
														<input type="radio" name="problem_testActive" value="Active" id="radioProblemActive" disabled="disabled">
														<label for="radioProblemActive">Active only</label>	
													</div>
												</div>
												<div class="col-sm-3">
													<div class="radio radio-inline">
														<input type="radio" name="problem_testActive" value="All" id="radioProblemAll" disabled="disabled">
														<label for="radioProblemAll">All</label>	
													</div>
												</div>
											</div>
											<?php if(isset($call_from) && $call_from != 'wv'){ ?>
												<div class="row">
													<div class="col-sm-5">
														<div class="checkbox checkbox-inline">
															<input type="checkbox" name="chart_nopro[]" value="Medication List" id="chkMedicationList" class="checkActive" linked-radio="medicationActive">
																<label for="chkMedicationList">Medication List</label>		
														</div>
													</div>
													<div class="col-sm-4">
														<div class="radio radio-inline">
															<input type="radio" name="medicationActive" value="Active" id="radioMedicationActive" disabled="disabled">
																<label for="radioMedicationActive">Active only</label>		
														</div>
													</div>
													<div class="col-sm-3">
														<div class="radio radio-inline">
															<input type="radio" name="medicationActive" value="All"  id="radioMedicationAll" disabled="disabled">
															<label for="radioMedicationAll">All</label>	
														</div>
													</div>
												</div>
											<?php  }  ?>
											<div class="row">
												<div class="col-sm-5">
													<div class="checkbox checkbox-inline">
														<input type="checkbox" name="chart_nopro[]" value="Allergies List" id="chkAllergiesList" class="checkActive" linked-radio="allergies_testActive">
														<label for="chkAllergiesList">Allergies</label>	
													</div>
												</div>
												<div class="col-sm-4">
													<div class="radio radio-inline">
														<input type="radio" name="allergies_testActive" value="Active" id="radioallergiesActive" disabled="disabled">
														<label for="radioallergiesActive">Active only</label>		
													</div>
												</div>
												<div class="col-sm-3">
													<div class="radio radio-inline">
														<input type="radio" name="allergies_testActive" value="All"  id="radioallergiesAll" disabled="disabled">
														<label for="radioallergiesAll">All</label>
													</div>
												</div>
											</div>
											
											<?php if(isset($call_from) && $call_from == 'wv'){ ?>
												<div class="row">
													<div class="col-sm-5">
														<div class="checkbox checkbox-inline">
															<input type="checkbox" name="chart_nopro[]" id="chkOcuMeds" value="Ocular Meds" class="checkActive" linked-radio="ocularAction">
															<label for="chkOcuMeds">Ocular Meds</label>		
														</div>
													</div>
													<div class="col-sm-4">
														<div class="radio radio-inline">
															<input type="radio" name="ocularAction" id="radio" value="Active" disabled="disabled">
															<label for="radio">Active only</label>		
														</div>
													</div>
													<div class="col-sm-3">
														<div class="radio radio-inline">
															<input type="radio" name="ocularAction" id="radio2" value="All" disabled="disabled">
															<label for="radio2">All</label>	
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-sm-5">
														<div class="checkbox checkbox-inline">
															<input type="checkbox" name="chart_nopro[]" id="chkSysMeds" value="Sys Meds" class="checkActive" linked-radio="sysAction">
															<label for="chkSysMeds">Systemic Meds</label>			
														</div>
													</div>
													<div class="col-sm-4">
														<div class="radio radio-inline">
															<input type="radio" name="sysAction" id="radio3" value="Active" disabled="disabled">
															<label for="radio3">Active only</label>		
														</div>
													</div>
													<div class="col-sm-3">
														<div class="radio radio-inline">
															<input type="radio" name="sysAction" id="radio4" value="All" disabled="disabled">
															<label for="radio4">All</label>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-sm-5">
														<div class="checkbox checkbox-inline">
															<input type="checkbox" name="chart_nopro[]" value="general_health" id="chkgeneralhealth">
															<label for="chkgeneralhealth">General Health</label>			
														</div>
													</div>
													<div class="col-sm-5">
														<div class="checkbox checkbox-inline">
															<input type="checkbox" name="chart_nopro[]" value="ocular" id="chkocular">
															<label for="chkocular">Ocular Health</label>			
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-sm-5">
														<?php
															$consult_letter=false;
															$str="";
															$qry = "SELECT patient_consult_id, date, ".
																		/*"patient_form_id,templateData,
																		cur_date ,templateId,templateName,operator_id,".*/
																		"DATE_FORMAT(date,'".get_sql_date_format()."') as DOC
																		from patient_consult_letter_tbl 
																		where patient_id = '".$_SESSION['patient']."' 
																		AND	
																		status != '1' and fax_status = '0' ORDER BY date, status desc" ;
															$rez=imw_query($qry);
															while($row=imw_fetch_assoc($rez)){

																$str .="<option  value='".$row["patient_consult_id"]."'> ".$row["DOC"]." </option>";

															}

															if(!empty($str)){
																$str="<label>Consult Letters:</label>
																		<select class=\"selectpicker\" name='consultLetterToPrint[]'  id='consultLetterToPrint' multiple=\"multiple\" data-size=\"5\" data-actions-box=\"true\" data-width=\"100%\">
																		$str
																		</select>";
																echo $str;
																$consult_letter=true;
															}

															?>
													</div>
													<div class="col-sm-5">
														<?php
													$str="";
													$op_report=false;	
													$oPnRep = new PnReports;
													list($arrTemp,$arrTrash) = $oPnRep->getPtReports($pid);
													if(count($arrTemp)>0){
														foreach($arrTemp as $key => $val){		
															$tDate = $key;
															if(count($val) > 0){
																foreach($val as $key2 => $val2){
																	$pnId = $val2[0];
																	$tId = $val2[1];
																	$opid = $val2[2];
																	$tTm = trim($val2[3]);
																	$tNameScEMR = trim($val2[4]);
																	$tIdScEMR = $val2[5];					
																	
																	
																	$str .="<option  value='".$pnId."'> ".$tDate." </option>";	
																}																
															}
														}													
													}
													
													if(!empty($str)){
														$str="<label>Operative Note:</label>
																<select class=\"selectpicker\" name='opNoteToPrint[]' id='opNoteToPrint' multiple=\"multiple\" data-size=\"5\" data-actions-box=\"true\" data-width=\"100%\">
																$str
																</select>";
														echo $str;
														$op_report=true;	
													}	
																									
												?>
													</div>
												</div>
												
											<?php } ?>
										</div>
									</div>
									
									<div class="practbox">
										<div class="anatreport">
											<h2>Patient Information</h2>
										</div>
										<div class="clearfix"></div>
										<div class="pd5">
											<div class="row">
												<div class="col-sm-6">
													<div class="checkbox">
														<input type="checkbox" name="patient_info[]" value="all" onClick="vis1(this.checked); GlaucomaOption(false); AscanOption(false); enableElectronicBT(this);" id="patientInfoAll">
														<label for="patientInfoAll">All</label>	
													</div>
												</div>
											</div>
											
											<div class="row">
												<div class="col-sm-6">
													<div class="checkbox">
														<input type="checkbox" name="cbkElectronicDos" id="cbkElectronicDos">
														<label for="cbkElectronicDos">This Visit Electronics</label>	
													</div>	
												</div>
												<div class="col-sm-6">
													<select class="selectpicker" name='cmbxElectronicDOS' id='cmbxElectronicDOS' data-width="100%" data-size="5">
														<?php echo $cpr->get_pt_dos('dropdown'); ?>	
													</select>
												</div>	
											</div>
											
											<div class="row">
												<div class="col-sm-6">
													<div class="checkbox">
														<input type="checkbox" name="patient_info[]" id="cbkDemographics" value="print_demographics2.php" onClick="enableElectronicBT(this); checkAll(false); GlaucomaOption(false); AscanOption(false);">
														<label for="cbkDemographics">Demographics</label>	
													</div>	
												</div>
												
												<div class="col-sm-6">
													<div class="checkbox">
														<input type="checkbox" id="legal_form" name="patient_info[]" onClick="checkAll(false); GlaucomaOption(false); AscanOption(false);" value="print_legal.php">
														<label for="legal_form">Legal Forms</label>	
													</div>
												</div>
											</div>
											
											<div class="row">
												<div class="col-sm-4">
													<div class="checkbox">
														<input type="checkbox" name="patient_info[]" id="face_sheet" value="face_sheet" onClick="vis2(this.checked); GlaucomaOption(false); AscanOption(false);">
														<label for="face_sheet">Face Sheet</label>	
													</div>
												</div>
												<div class="col-sm-8">
													<div class="row">
														<div class="col-sm-6 hide" id="face_sheet_scan_id">
															<div class="checkbox">
																<input type="checkbox" id="face_sheet_scan" name="face_sheet_scan" value="1">
																<label for="face_sheet_scan">With scan card</label>	
															</div>	
														</div>
														<div class="col-sm-6 hide" id="face_sheet_detail_id">
															<div class="checkbox">
																<input type="checkbox" id="face_sheet_detail" name="face_sheet_detail" value="1">
																<label for="face_sheet_detail">Detail</label>	
															</div>	
														</div>
													</div>
												</div>	
											</div>
											
											<div class="row">
												<div class="col-sm-6">
													<div class="checkbox">
														<input type="checkbox" name="patient_info[]" id="cbkMedHx" value="printMedicalHistory.php" onClick="enableElectronicBT(this); checkAll(false); GlaucomaOption(false); AscanOption(false);">
														<label for="cbkMedHx">Medical History</label>	
													</div>	
												</div>
											</div>
											<div class="row">
												<div class="col-sm-6">
													<div class="checkbox">
														<input type="checkbox" name="patient_info[]" id="cbkInsurance" value="insurance_print.php" onClick="ins_scan(this.checked); enableElectronicBT(this); checkAll(false); GlaucomaOption(false); AscanOption(false);">
														<label for="cbkInsurance">Insurance</label>	
													</div>	
												</div>
												<div class="col-sm-6 hide" id="insurance_scan_id">
													<div class="checkbox">
														<input type="checkbox" name="insurance_scan"  id="insurance_scan" value="1">
														<label for="insurance_scan">With scan card</label>	
													</div>	
												</div>	
											</div>
										</div>	
									</div>
									
									<div class="practbox">
										<div class="anatreport">
											<h2>Electronic</h2>
										</div>
										<div class="clearfix"></div>
										<div class="pd5">
											<div class="row">
												<div id="trElectronicData" class="col-sm-12">
													<div class="row">
														<div class="col-sm-6">
															<label>SHA 2 for Encrypted Format</label>
														</div>
														<div class="col-sm-6">
															<div id="linkToDownloadENC" class="text-right"></div>	
														</div>	
													</div>
													<div class="row">
														<div class="col-sm-12">
															<textarea id="txtAreaHashValue" class="form-control" name="txtAreaHashValue" rows="3" readonly onClick="this.focus(); this.select();"></textarea>	
														</div>
													</div>
													<?php  if(isset($call_from) && $call_from != 'wv'){ ?>
													<div class="row pt10">
														<div class="col-sm-6">
															<label>SHA 2 for Encrypted Format</label>
														</div>
														<div class="col-sm-6">
															<div id="linkToDownloadENC" class="text-right"></div>	
														</div>	
													</div>
													<div class="row">
														<div class="col-sm-12">
															<textarea id="txtAreaHashValue" class="form-control" name="txtAreaHashValue" rows="3" readonly onClick="this.focus(); this.select();"></textarea>	
														</div>
													</div>
													<?php } ?>
												</div>
											</div>
										</div>
									</div>	
								</div>
								<div class="patreport">
								<div id="module_buttons" class="ad_modal_footer" style="margin-left:1px!important;padding:0px !important;">
									<?php if(isset($call_from) && $call_from != 'wv'){ ?>
										<input type="submit" class="btn btn-success" id="btnExportReport" name="btnExportReport" title="Export Report"  value="Export Report" onClick="executeExportReport();" >	
									<?php }else{
										echo '&nbsp;';
									} ?>
									<input type="submit" class="btn btn-success" id="directPrint" name="directPrint" title="Print"  value="Print" onClick="ResetFaxStatus('');javascript: return submitPrintRequest();">
									<?php if(is_updox('fax') || is_interfax()){?><input type="button" class="btn btn-success" id="butIdCancel" title="Send Fax"   value="Send Fax" onClick="sendFaxFun();">
									
									<input type="button" class="btn btn-success" name="send_fax_Log" id="send_fax_Log" onClick="window.open('../../chart_notes/send_fax_log.php', 'fax_log', 'width=800,height=500,scrollbars=yes');" class="dff_button" value="Fax Log">
									
									<?php }?>	
									<input type="button" class="btn btn-success" id="butIdElectronic" title="Electronic" disabled value="Electronic" onClick="<?php echo $electronic_function; ?>('<?php echo $pid; ?>');">	
									<input type="button" class="btn btn-danger" id="butIdCancel" title="Cancel"   value="Cancel" onClick="window.close();">	
								</div>
								</div>
								
						<?php
								//Including printing modal
								include_once('print_modals.php');	
							?>	
								</form>
							</div>
							
						
						<div class="col-sm-9" id="content_main_box">
							<div class="btn-group fltimg toggle-sidebar vertical-text pointer" role="group" aria-label="Controls">
								<span class="filter_block">Patient Filter</span>
								<span class="glyphicon glyphicon-menu-down"></span>
                            </div>
							<iframe id="fake_iframe" src="about:blank"></iframe>
							
							<div id="content_box" class="pd5 report-content">
								<div class="alert alert-info text-center">
								  <strong>Nothing to Show</strong>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<form name="electronic" id="electronic" method="post" action="../reports/ccd/create_liaka_xml.php">
					<input type="hidden" name="pId" id="pId" value="<?php echo $cpr->patient_id; ?>" />
					<input type="hidden" name="option" id="option" />
				</form>
			<?php if(count($disclosed_data) > 0){ ?>
			<!-- Modal -->
				<div id="myModal" class="modal" role="dialog">
				  <div class="modal-dialog modal_90" style="position:relative">

					<!-- Modal content-->
					<div class="modal-content" style="z-index:9999999">
					  <div class="modal-header bg-primary">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Disclosed Information</h4>
					  </div>
					  <div class="modal-body">
						<div class="row">
							<table class="table table-bordered table-striped">
								<tr class="grythead">
									<th class="text-nowrap">Date Requested</th>
									<th class="text-nowrap">Disclosed By</th> 
									<th class="text-nowrap">Disclosed To</th>
									<th class="text-nowrap">Disclosed</th>
									<th class="text-nowrap">Specialty </th>
									<th class="text-nowrap">Reason</th>
								</tr>
								<?php 
									$counter = 0;
									foreach($disclosed_data as $obj){
										$str .= '<tr>';
										foreach($obj as $key => $val){
											$cls_wrap = '';
											if($key != 'Disclosed'){
												$cls_wrap = 'text-nowrap';
											}
											$str .= '<td style=\"vertical-align: top!important;\" class="'.$cls_wrap.'">'.$val.'</td>';	
										}
										$counter++;
										$str .= '</tr>';	
									}
									echo $str;
								?>
							</table>	
						</div>	
					  </div>
					  <div id="module_buttons" class="modal-footer ad_modal_footer">
						<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
					  </div>
					</div>
					<iframe class="fake_modal_iframe" src=""></iframe>	
				  </div>
				</div>	
			<?php   } ?>	
			</div>
			<!-- frame to send fax -->
			<iframe name="hiddIframe1" style="height:100px; width:300px; display:none;"></iframe>
			<!-- Loading div -->
			<div id="div_loading_image" class="col-sm-12 text-center" style="top: 0;margin-top: 0px;position: absolute;width: 99%;height: 100%;z-index: 9999;background: rgba(0,0,0,0.3);overflow: hidden!important;">
				<div class="loading_container" style="position:absolute;top:50%;left:50%">
					<div class="process_loader"></div>
					<div id="div_loading_text" class="text-info">
						Please wait....	
					</div>
				</div>
			</div>
	</body>
	<script>
		$(document).ready(function () {
			$(".toggle-sidebar").click(function () {
				$("#sidebar").toggleClass("collapsed");
				$("#content_main_box").toggleClass("col-sm-12 col-sm-9");

				if ($('.fltimg').find('span.glyphicon').hasClass('glyphicon glyphicon-menu-down')) {
					$('.fltimg').find('span.glyphicon').removeClass('glyphicon glyphicon-menu-down').addClass('glyphicon glyphicon-menu-up');
				} else {
					$('.fltimg').find('span.glyphicon').removeClass('glyphicon glyphicon-menu-up').addClass('glyphicon glyphicon-menu-down');
				}
				return false;
			});
		});
        
        function summary_box_change() {
            if($('#chk_summary_box').is(':checked')==true) {
                $('.summary_box').find('input[type="checkbox"]').prop('checked', true);
                $('.summary_box').find('input[type="radio"]').prop('disabled', false);
                $('.summary_box').find('input[type="radio"][value="Active"]').prop('checked', true);
            }else {
                $('.summary_box').find('input[type="checkbox"]').prop('checked', false);
                $('.summary_box').find('input[type="radio"]').prop('disabled', true);
                $('.summary_box').find('input[type="radio"][value="Active"]').prop('checked', false);
            }
        }

		function set_container_height(){
			$('.reportlft').css({
				'height':(window.innerHeight - ($('.purple_bar').height() + $('#module_buttons').height() + 34)),
				'max-height':(window.innerHeight - ($('.purple_bar').height() + $('#module_buttons').height() + 34)),
				'overflow-x':'hidden',
				'overflowY':'auto'
			});
			if($('#dynamiciFrame').length){
				$('#dynamiciFrame').css({
					'height':($('.reportlft').height() + ($('#module_buttons').height() - 20))+'px'
				});
			}
		} 
		
		$('#dynamiciFrame').on('ready', function(){
			alert('asdasdasd');
		});

		$(window).load(function(){
			set_container_height();
			$('#div_loading_image').hide();
		});

		$(window).resize(function(){
			set_container_height();
		});

	</script>
</html>
<?php if($_REQUEST['print_form_id']){ ?>
<script>
	vis(true);
	print_function.submit();
</script>
<?php
	}
?>