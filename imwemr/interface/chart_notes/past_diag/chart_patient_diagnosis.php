<?php
//Get chartDrawing still pending in this section
require_once(dirname(__FILE__).'/../../../config/globals.php');

$library_path = $GLOBALS['webroot'].'/library';
include_once $GLOBALS['srcdir']."/classes/pt_at_glance.class.php";
include_once($GLOBALS['srcdir']."/classes/class.tests.php");
include_once($GLOBALS['srcdir']."/classes/SaveFile.php");

if(isset($_REQUEST["p_id"]) && !empty($_REQUEST["p_id"])){
	$pid = trim($_REQUEST["p_id"]);
}else{
	$pid = $_SESSION["patient"];
}

if(empty($pid)){ //close if empty pid
	//Check patient session and closing popup if no patient in session
	$window_popup_mode = true;
	require_once($GLOBALS['srcdir']."/patient_must_loaded.php");
}

$authUser=$_SESSION['authUser'];

//Pt glance obj.
$pt_glance = new Pt_at_glance($pid,$authUser,$_REQUEST);

//Savefile
$oSaveFile = new SaveFile($pid);

//req ppl
if(isset($_GET["get_Pt_Prob_list"])){
	if(!empty($_GET["get_Pt_Prob_list"])){echo $pt_glance->pt_active_prob_list($pt_glance->patient_id," AND status = '".$_GET["get_Pt_Prob_list"]."' ");}
	exit();
}
//End ppl

//req test
if(isset($_GET["get_test_info"])){
	if(!empty($_GET["get_test_info"])){
		
		$table_name = strtolower($_GET["test"]);
		$test_id = $_GET["get_test_info"];	
		$scnid = $_GET["scnid"];
		$objTests				= new Tests;
		$this_test_images = $objTests->get_test_images_by_id($scnid);
		
		if(stripos($this_test_images["scan_uploads"]["extension"],"pdf") !== false){
			$str_test_doc = "<iframe src=\"".$this_test_images["scan_uploads"]["original"]."\" alt=\"test image\" width=\"100%\" height=\"500\"></iframe>";
		}else{
			$str_test_doc = "<img src=\"".$this_test_images["scan_uploads"]["original"]."\" alt=\"test image\">";
		}
		
		//--
		$htm="<!-- Modal -->
		<div id=\"scnModal".$scnid."\" class=\"modal fade\" role=\"dialog\">
		<div class=\"modal-dialog\">

			<!-- Modal content-->
			<div class=\"modal-content\">
				<div class=\"modal-header\">
				<button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button>
				<h4 class=\"modal-title\">".$this_test_images["image_name"]."</h4>
				</div>
				
				<div class=\"modal-body\">
				<p>".$str_test_doc."</p>
				</div>
			</div>

		</div>
		</div>";
		//--
		
		echo $htm;
		
	}
	exit();
}
//End test

//get Test
if(isset($_GET["get_pt_test"])){
	if(!empty($_GET["get_pt_test"])){		
		echo $pt_glance->pt_active_test($pt_glance->patient_id, $_GET["get_pt_test"]);		
	}
	exit();
}
//End get Test

//Getting pt. data
	//-->Pt. data
	//-->Ins. case
$patient_data = $pt_glance->get_patient_data($pt_glance->patient_id);

//Get provider data
$provider_data = $pt_glance->get_provider_data($pt_glance->auth_user);

//Get Ocular hx data
$ocular_hx = $pt_glance->get_ocular_hx_data($pt_glance->patient_id);

//Get medical hx data
$medical_hx_data = $pt_glance->get_medical_hx_data($pt_glance->patient_id);

//Get test medication data
$test_medication_data = $pt_glance->get_test_medications_data($pt_glance->patient_id);

//Get allergies data
$allergies_data = $pt_glance->get_allergies_data($pt_glance->patient_id);

//Get surgeries data
$surgeries_data = $pt_glance->get_surgeries_data($pt_glance->patient_id);

//Get Site target values
$site_vals = $pt_glance->get_target_vals($pt_glance->patient_id);

//Get pt. diag comments
$pt_comments = $pt_glance->get_pt_diag_comm($pt_glance->patient_id);

//Get pachy values
$arrPachy = $pt_glance->set_def_pachy_vals();

//Get RVS info
$str_rvs = $pt_glance->get_rvs_info($pt_glance->patient_id);

//get active test
$arrActiveTests = $pt_glance->get_active_o_test($pt_glance->patient_id);

//get Pt image
if(!empty($patient_data["patientImg"])){ 
	$tmp_img_path = $oSaveFile->getFilePath($patient_data["patientImg"], "i");	
	if(file_exists($tmp_img_path)){  $pt_img_path = $oSaveFile->getFilePath($patient_data["patientImg"], "w"); }	
}

$global_js_arr = array();
$global_js_arr['webroot'] = $GLOBALS['webroot'];
$global_js_arr['rootdir'] = $GLOBALS['rootdir'];
$global_js_array = json_encode($global_js_arr);
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		<title>Patient at a Glance</title>
		<!-- Bootstrap -->
		<link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
		<!-- Bootstrap Selctpicker CSS -->
		<link href="<?php echo $library_path; ?>/css/bootstrap-select.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/font-awesome.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/pt_glance.css" rel="stylesheet" type="text/css">
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
		<script src="<?php echo $library_path; ?>/js/jquery-ui.min.1.12.0.js" type="text/javascript" ></script>
		<!-- jQuery's Date Time Picker -->
		<script src="<?php echo $library_path; ?>/js/jquery.datetimepicker.full.min.js" type="text/javascript" ></script>
		<!-- Bootstrap -->
		<script src="<?php echo $library_path; ?>/js/bootstrap.js" type="text/javascript" ></script>
		
		<!-- Bootstrap Selectpicker -->
		<script src="<?php echo $library_path; ?>/js/bootstrap-select.js" type="text/javascript"></script>
		<!-- Bootstrap typeHead -->
		<script src="<?php echo $library_path; ?>/js/bootstrap-typeahead.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/js/common.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/js/core_main.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/messi/messi.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/js/jquery.mCustomScrollbar.concat.min.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/amcharts/amcharts.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/amcharts/serial.js" type="text/javascript"></script>
		<script>
			var global_js_vars = JSON.parse('<?php echo $global_js_array; ?>');
		</script>
		<script src="<?php echo $library_path; ?>/js/pt_glance.js" type="text/javascript"></script>
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
			.adminbox div:nth-child(odd) {padding-right: 1%;}
			.od{color:blue;}
			.os{color:green;}
			.ou{color:#9900cc;}
			.pad-left1{	padding-left:1px;}
			table#demograph td {color:Blue;}
			td#redBold { font-weight:bold; color:Red;}
			td#orangeBold { font-weight:bold; color:Orange;}
			.grpDiv {width:230px;}
			#div_displayApOptions{ position:absolute;display:none;background-color:white;border:1px solid black;padding:2px;}
			.dff_button_smll{font-size:11px;padding:1px;}
			.hand_cur{cursor:pointer;}

			td.pgd_apTbl{vertical-align:top;}
			td.pgd_apTbl div {position:relative;}
			td.pgd_apTbl .apClrRes{background-color:lightgreen;}
			td.pgd_apTbl div p:nth-child(1){margin:0px;width:5px;position:absolute;}
			td.pgd_apTbl div p:nth-child(2){margin:0px;padding-left:8px;}
			#divFImg ul{margin:10px;padding:10px;}
			.pag_iop{mi-width:25px; width:auto; display:inline-block; }
			.pag_iopT{width:45px;}
			#conPtGlance{overflow:auto;}
			.fr{margin-right:50%; }
			#paging_links label{  cursor:pointer; color:purple; }
			#paging_links label.inactive{ cursor:auto; color:black; }	
			footer::before {
				content: "";
				position: fixed;
				left: 0;
				width: 100%;
				height: 100%;
				-webkit-box-shadow: 0px 0px 10px rgba(0,0,0,.8);
				-moz-box-shadow: 0px 0px 10px rgba(0,0,0,.8);
				box-shadow: 0px 0px 6px rgba(0,0,0,.8);
				z-index: 100;
			}
			
			#top_align_tbl .table td {
				 vertical-align: top!important;
			}  
			body {
				font-size: 12px;
			}
			.scroll-content {
				height: 170px;
			}
			.mainwhtbox {
				margin-bottom:35px;
			}
			.tbl_tests table td{padding:4px 2px!important;}
			#btnSaveClose.btn{margin-right:8px!important;}
			table.tblap{background-color: transparent!important;height:100%; width:100%;}
			table.tblap tr td{border: 0px solid transparent!important;}
			table.tblap tr td{ vertical-align: top!important; }
			table.tblap tr td:nth-child(1){ width:55.8%; border-right: 1px solid #ddd!important;}
			span.soc{ white-space: normal; }
		</style>
		
	</head>
	<?php 
		if($pt_glance->cameFrom == 'print_window'){
			echo $pt_glance->get_print_html();
		}else{
	?>
	<body onload="window_width();">
		<div class="mainwhtbox">
			<div class="purple_bar">
				<div class="row">
					<div class="col-sm-4">
						Patient at a Glance
					</div>
					<div class="col-sm-4">
						<?php echo $patient_data['strPtinfo']; ?>
					</div>
					<div class="col-sm-4">
						<div class="row">
									<span class="col-xs-3 nowrap text-right pd0" for="elem_heardAbtUs">Heard about us</span>
                  <span class="col-xs-4">
                    <select name="elem_heardAbtUs" id="elem_heardAbtUs" class="form-control minimal" data-width="100%" onChange="onHeardAbtUsChnge(this);">
                        <option value=""><?php echo imw_msg('drop_sel'); ?></option>
                        <?php 
                          $display_img = "hidden";
													$selHeardAbtStr = "";
                          $forSelTypeAhed = "";
													$heardAbtUsArr = get_heard_about_list($patient_data['heard_abt_us']);
                          if(is_array($heardAbtUsArr) && count($heardAbtUsArr) > 0){
                          foreach($heardAbtUsArr as $rowHeardAbtUs){
                          $sel = "";
                          $h_id	=	trim($rowHeardAbtUs['heard_id']);
                          $h_opt=	$rowHeardAbtUs['heard_options'];
                          if(trim($patient_data['heard_abt_us']) == $h_id ){
                            $sel = "selected='selected'";
                            $forSelTypeAhed = str_ireplace("'","",stripslashes($rowHeardAbtUs['heard_options']));
                            $forSelTypeAhed = str_ireplace(":","",$forSelTypeAhed);
														$selHeardAbtStr = stripslashes($rowHeardAbtUs['heard_options']);
                          }
                        ?>
                          <option value="<?php echo $h_id."-".$h_opt; ?>" <?php echo $sel; ?>><?php echo stripslashes($h_opt); ?></option>
                          <?php		
                          }
                        }
                        ?>
                        <option value="Other">Other</option>
                   	</select>
               	 		
               	 		<div id="otherHeardAboutBox" class="hidden">
                    	<div class="input-group">
                      	<input class="form-control" id="heardAbtOther" type="text" name="heardAbtOther" data-prev-val="" />
                      	<label class="input-group-btn btn btn-xs btn-primary back_other" id="imgBackHeardAboutUs" data-tab-name="elem_heardAbtUs" onClick="backHerdAbtUs();">
                      		<span class="glyphicon glyphicon-arrow-left"></span>
                      	</label>
                      </div>
                      </div>
                	</span>
               		<?php
										$searchFieldShow = $txtFieldShow = false;
										$searchEvents = '';
										if( $patient_data['heard_abt_us'] ) {
											if( in_array($selHeardAbtStr,array('Family','Friends','Doctor','Previous Patient.','Previous Patient')) ) {
												$searchFieldShow = true;
												if( $selHeardAbtStr == 'Doctor' ){
													$searchEvents = 'onkeyup="top.loadPhysicians(this,\'heardAbtSearchId\')";onfocus="top.loadPhysicians(this,\'heardAbtSearchId\')";';
												}
												else {
													$searchEvents = 'onkeydown="if( event.keyCode == 13) { searchHeardAbout(); }";';
												}
											} else{
												$txtFieldShow = true;
											}
										}
									?>
                  <div class="col-xs-5 pd0">
                    <div id="tdHeardAboutDesc">
                    	<textarea class="form-control <?php echo ($txtFieldShow ? 'inline' : 'hidden');?>" id="heardAbtDesc" name="heardAbtDesc" rows="1" cols="28" data-provide="multiple" data-seperator="newline"><?php echo stripslashes($patient_data['heard_abt_desc']); ?></textarea>
                  	</div>
                  	<div id="tdHeardAboutSearch" class="<?php echo ($searchFieldShow ? 'inline' : 'hidden');?>">
                  		<div class="input-group">
                   			<input type="hidden" id="heardAbtSearchId" name="heardAbtSearchId" value="<?php echo stripslashes($patient_data['heard_abt_search_id']); ?>" />
                   			<input type="text" class="form-control " id="heardAbtSearch" name="heardAbtSearch" value="<?php echo stripslashes($patient_data['heard_abt_search']); ?>" autoComplete="off" <?php echo $searchEvents;?> />
                    		<label class="input-group-addon btn" onClick="searchHeardAbout();" >
                      		<span class="glyphicon glyphicon-search"></span>
                      	</label>
                  		</div>
										</div>		
                  </div> 	         
              	</div>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="row">
				<!-- Active Prob. List -->
				<div class="col-sm-4">
					<div class="whtbox">
					<div class="head">
					<div class="row">
						<div class="col-sm-7">
						<span onClick="openPtProbList()" class="pointer text_purple">Active Patient Problem List</span>
						</div>
						<div class="col-sm-5">	
							<select class="form-control pull-right col-sm-2" id="sel_appl">
							    <option>Active</option>
							    <option>Resolved</option>
							    <option>Inactive</option>
							    <option>Unobserved</option>
							    <option>External</option>
							</select>
						</div>
					</div>
					</div>
						<div class="scroll-content" style="overflow:auto;">
							<div class="table-responsive">
								<table class="table table-bordered table-striped table-hover">
									<thead>
									<tr class="grythead">
										<td class="text-nowrap">Date</td>
										<td>Patient Problem</td>
									</tr>
									</thead>
									<tbody id="ppl_res">
									<?php echo $pt_glance->pt_active_prob_list($pt_glance->patient_id," AND status != 'Deleted' AND status != 'Inactive' "); ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>	
				<!-- Active Orders/List -->
				<div class="col-sm-4">
					<div class="whtbox">
					<div class="head">
						<div class="row">
							<div class="col-sm-7">
								<span>Active Orders/Test</span>
							</div>
							<div class="col-sm-5">
								<div class="checkbox">
								  <input type="checkbox" value="1" id="el_test_comp"><label for="el_test_comp">Test Completed</label>
								</div>
							</div>
						</div>
					</div>
						<div class="scroll-content" style="overflow-x:hidden; overflow-y:auto;">
							<div class="row">
								<div class="col-sm-8">
									<div class="row">
										<div class="table-responsive">
											<div class="scroll-content">
												<table class="table table-bordered table-striped table-hover">
													<tr class="grythead">
														<td>Date</td>
														<td colspan="2">Orders</td>
													</tr>
													<?php echo $pt_glance->pt_active_order($pt_glance->patient_id); ?>	
												</table>
											</div>
										</div>
									</div>
								</div>	
								<div class="col-sm-4">
									<div class="row">
										<div class="table-responsive">
											<div class="scroll-content tbl_tests">
												<table class="table table-bordered table-striped table-hover ">
													<thead>
													<tr class="grythead"><td colspan="3">Tests</td></tr>
													</thead>
													<tbody id="test_res">
													<?php echo $pt_glance->pt_active_test($pt_glance->patient_id); ?>
													</tbody>													
												</table>
											</div>	
										</div>
									</div>
								</div>	
							</div>
						</div>		
					</div>
				</div>
				<!-- Procedures -->	
				<div class="col-sm-<?php echo (!empty($pt_img_path)) ? "3" : "4"; ?>">
					<div class="whtbox">
					<div class="head"><span>Procedure</span></div>
						<div class="scroll-content" style="overflow:auto;">
							<div class="table-responsive">
								<table class="table table-bordered table-striped table-hover">
									<?php echo $pt_glance->get_chart_procedures($pt_glance->patient_id); ?>
								</table>
							</div>
						</div>
					</div>
				</div>
				<?php if(!empty($pt_img_path)){ ?>
				<div class="col-sm-1">
					<div class="thumbnail">
					<img src="<?php echo $pt_img_path; ?>" alt="Patient IMG">
					</div>
				</div>
				<?php } ?>
			</div>
			<div class="clearfix"></div>
			<div class="head">
				<div class="row">
					<div class="col-sm-12">
						Medical History
					</div>
				</div>
			</div>
			<div class="table-responsive" id="top_align_tbl">
				<table class="table table-bordered table-striped table-hover">
				<tr class="grythead">
					<td>Ocular History</td>
					<td>Ocular Medicine</td>
					<td>Ocular Surgeries</td>
					<td>Allergies-Reactions</td>
					<td>Medical History</td>
					<td>General Medicine</td>
				</tr>
				<tr>
					<td id="redBold"><?php echo $ocular_hx;?></td>
					<td id="orangeBold"><?php echo $test_medication_data['strOcuMedication']; ?></td>
					<td id="orangeBold"><?php echo $surgeries_data;?></td>
					<td id="orangeBold"><?php echo $allergies_data;?></td>
					<td id="redBold"><?php echo $medical_hx_data;?></td>
					<td id="orangeBold"><?php echo $test_medication_data['strGenMedication']; ?></td>
				</tr>
				<tr>
					<td colspan="6" class="medhscont">
						<div class="form-group">
							<label for="">Comments :</label>
							<textarea id="elem_commentsta" name="elem_commentsta" class="form-control" onFocus="checkCommentsTa(this);" onChange="saveCommentsTa(this)" <?php echo ($elem_per_vo == "1") ? "readonly='readonly'" : "" ;?> ><?php echo $pt_comments?></textarea>
						</div>
					</td>
				</tr>
				</table>
			</div>
			<div class="row">
				<div id="" class="col-sm-12">
					<?php 
						$st_index = $pt_glance->st_index;
						echo $pt_glance->get_pt_diagnostic($pt_glance->patient_id,$pdg_showpop=0,$pdg_hiderow1=0,$st_index);
					?>
				</div>
			</div>
			<?php
				if($GLOBALS['SHOW_CL_IN_PAG'] && $GLOBALS['SHOW_CL_IN_PAG'] == "1"){
			?>
			<div class="clearfix"></div>
			<div class="head">
				<div class="row">
					<div class="col-sm-12">
						Contact lens (SCL)
					</div>
				</div>
			</div>
			<div class="row" style="margin-bottom:20px;">
				<div id="" class="col-sm-12">
					<div class="table-responsive">
						<?php
							$count = 0;
							$clArray = $pt_glance->getPtCLInfo();
							//pre($clArray);
						?>
						<table class='table table-bordered table-striped table-hover'>
							<tr class='grythead'>
								<td>Date of service</td>
								<td>Site</td>
								<td>CL visit type</td>
								<td>Make</td>
								<td>Base curve</td>
								<td>Diameter</td>
								<td>Sphere</td>
								<td>Cylinder</td>
								<td>Axis</td>
								<td>Color</td>
								<td>Add</td>
								<td>DV<sub>A</sub></td>
								<td>NV<sub>A</sub></td>
							</tr>
							<?php
								foreach($clArray as $dateOfService => $dos){
									$count = 1;
									foreach($dos as $lensDetails => $lArray){
										if($lArray['lens_type'] != 'scl'){
											continue;
										}
										echo "<tr>";
										if($count == 1){
											echo "<td rowspan='".count($dos)."'>".date("m-d-Y", strtotime($lArray['date_of_service']))."</td>";
										}
										$site = $lArray['cl_eye'];
										echo "<td>".$site."</td>";
										echo "<td>".$lArray['cl_visit_type']."</td>";
										echo "<td>".$lArray['make'.$site]."</td>";
										echo "<td>".$lArray['base_curve'.$site]."</td>";
										echo "<td>".$lArray['diameter'.$site]."</td>";
										echo "<td>".$lArray['sphere'.$site]."</td>";
										echo "<td>".$lArray['cylinder'.$site]."</td>";
										echo "<td>".$lArray['axis'.$site]."</td>";
										echo "<td>".$lArray['color'.$site]."</td>";
										echo "<td>".$lArray['add'.$site]."</td>";
										echo "<td>".$lArray['dva'.$site]."</td>";
										echo "<td>".$lArray['nva'.$site]."</td>";
										$count ++;
									}
									echo "</tr>";
								}
							?>
						</table>
					</div>
				</div>
			</div>
			<div class="head">
				<div class="row">
					<div class="col-sm-12">
						Contact lens (RGP/Custom RGP)
					</div>
				</div>
			</div>
			<div class="row" style="margin-bottom:20px;">
				<div id="" class="col-sm-12">
				<div class="table-responsive">
					<?php
						$count = 0;
						$clArray = $pt_glance->getPtCLInfo("rgp");
						//pre($clArray);
					?>
					<table class='table table-bordered table-striped table-hover'>
							<tr class='grythead'>
								<td>Date of service</td>
								<td>Site</td>
								<td>CL visit type</td>
								<td>Make</td>
								<td>Base curve</td>
								<td>Diameter</td>
								<td>Optical zone</td>
								<td>Center thickness</td>
								<td>Power</td>
								<td>Cylinder</td>
								<td>Axis</td>
								<td>Color</td>
								<td>Add</td>
								<td>DV<sub>A</sub></td>
								<td>NV<sub>A</sub></td>
							</tr>
							<?php
								foreach($clArray as $dateOfService => $dos){
									$count = 0;
									foreach($dos as $lensDetails => $lArray){
										if($lArray['lens_type'] == 'scl'){
											continue;
										}
										echo "<tr>";
										if($count == 0){
											echo "<td rowspan='".count($dos)."'>".date("m-d-Y", strtotime($lArray['date_of_service']))."</td>";
										}
										$site = $lArray['cl_eye'];
										echo "<td>".$site."</td>";
										echo "<td>".$lArray['cl_visit_type']."</td>";
										echo "<td>".$lArray['make'.$site]."</td>";
										echo "<td>".$lArray['base_curve'.$site]."</td>";
										echo "<td>".$lArray['diameter'.$site]."</td>";
										echo "<td>".$lArray['optical_zone'.$site]."</td>";
										echo "<td>".$lArray['center_thickness'.$site]."</td>";
										echo "<td>".$lArray['sphere'.$site]."</td>";
										echo "<td>".$lArray['cylinder'.$site]."</td>";
										echo "<td>".$lArray['axis'.$site]."</td>";
										echo "<td>".$lArray['color'.$site]."</td>";
										echo "<td>".$lArray['add'.$site]."</td>";
										echo "<td>".$lArray['dva'.$site]."</td>";
										echo "<td>".$lArray['nva'.$site]."</td>";
										$count ++;
									}
									echo "</tr>";
								}
							?>
						</table>
					</div>
				</div>
			</div>
		<?php } ?>
		</div>
		<footer id="module_buttons" class="footer navbar-fixed-bottom " style="padding: 0px;background: #fff;">
			<div  class="col-sm-4 pt10" style="z-index:10000000">
				<div class="row">
					<div class="col-sm-7">
						<h4><small><?php echo $pt_glance->str_dig_info; ?></small></h4>
					</div>	
					<div class="col-sm-5">
						<div class="input-group pt5">
						<span class="input-group-addon">Record(s) per page</span>
							<select id="el_shw_rec" name="el_shw_rec" onchange="set_shw_rec()" class="minimal form-control">
								<option value=""></option>
								<?php
									$a_opts=array("10","20","30","40","60","80","100");
									foreach($a_opts as $k => $v){
										$sel = ($pt_glance->el_shw_rec == $v) ? " selected " : "" ;
										echo "<option value=\"".$v."\" ".$sel." >".$v."</option>";
									}
								?>
							</select>
						</div>
					</div>	
				</div>	
			</div>
			<div class="col-sm-4 text-center pt10" style="z-index:10000000">
				 <?php if($pt_glance->cameFrom=='imedicmonitor'){?>
				<input type="button" class="btn btn-danger" id="btnClose" name="btnClose" value="CLOSE" onClick="top.removeMessi();">
				<?php }else{?>
				<input type="button" class="btn btn-success" id="btnSaveC" name="btnSaveC" value="Save" onClick="pag_save()">
				<input type="button" class="btn btn-success" id="btnSaveClose" name="btnSaveClose" value="Save & Close" onClick="pag_save('saveclose')">
				<input type="button" class="btn btn-success" id="btnPrint" name="print" value="Print" onClick="mkprint();">
				<?php }?>	
				<input type="hidden" id="elem_ptId" value="<?php echo $pt_glance->patient_id; ?>">
			</div>
			<div class="col-sm-4 text-right pt10" style="z-index:10000000">
				<div id="paging_links">
					<?php echo $pt_glance->paging_links ;?>
				</div>	
			</div>	
		</footer>
		
		
		<!-- Graph Modal -->
		<div id="myModal" class="modal fade" role="dialog">
		  <div class="modal-dialog modal-lg">

			<!-- Modal content-->
			<div class="modal-content">
			  <div class="modal-header bg-primary">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">IOP Graph</h4>
			  </div>
			  <div class="modal-body" id="IOPGraphChartAmMain" style="max-height:600px;height:500px">
			  </div>
			  <div id="module_buttons" class="ad_modal_footer modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
			  </div>
			</div>

		  </div>
		</div>
	</body>	
	<?php } ?>
<script type="text/javascript">
    window.onload = maxWindow;
	function maxWindow() {
       top.window.resizeTo(screen.availWidth, screen.availHeight);
	}
	
	//
	$(document).ready(function () {
		$("#sel_appl").bind("change", function(){			
			$.get("chart_patient_diagnosis.php?get_Pt_Prob_list="+$(this).find("option:selected").text(), function(d){ $("#ppl_res").html(d); });		
		});
		//
		var load_test_img = function(o){
			var wh = $(o).hasClass("imgnm") ? "img" : "test";
			var tst = $(o).parent().find('.itstnm').text();	
			var scnid = $(o).parent().data("test-scan-id");
			if(typeof(scnid)!="undefined" && scnid!=""){
				if($("#scnModal"+scnid).length<=0){
				$.get("chart_patient_diagnosis.php?get_test_info="+$(this).parent().data("test-id")+"&wh="+wh+'&test='+tst+'&scnid='+ scnid, function(d){ $('body').append(d); $("#scnModal"+scnid).draggable(); $("#scnModal"+scnid).modal({backdrop: false});  $('.modal').css({ 'right':'auto', 'bottom':'auto'});    });
				}else{
				$("#scnModal"+scnid).modal({backdrop: false});
				}
				
			}
		};
		
		$(".itstnm, .imgnm").bind("click", function(){ load_test_img(this); } );
		//
		$("#el_test_comp").bind("click", function(){			
			$.get("chart_patient_diagnosis.php?get_pt_test="+$("#el_test_comp").prop("checked"), function(d){ $("#test_res").html(d); $(".itstnm, .imgnm").bind("click", function(){ load_test_img(this); } ); });
		});
		
		<?php 
		if($pt_glance->cameFrom == 'print_window'){
			echo "window.print()";
		}
		?>
	});
	
</script> 
</html>	