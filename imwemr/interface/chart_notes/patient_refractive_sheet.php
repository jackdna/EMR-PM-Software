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

include_once(dirname(__FILE__)."/../../config/globals.php");
require($GLOBALS['srcdir']."/classes/work_view/wv_functions.php");
require($GLOBALS['incdir']."/chart_notes/chart_globals.php");
//Check patient session and closing popup if no patient in session
$window_popup_mode = true;
require_once($GLOBALS['srcdir']."/patient_must_loaded.php");


$library_path = $GLOBALS['webroot'].'/library';
$zPath = $GLOBALS['webroot'].'/interface';
include_once $GLOBALS['srcdir']."/classes/pt_refractive_sheet.php";

$patient_id = $_SESSION['patient'];
$form_id = $_SESSION['form_id'];
$authId = $_SESSION['authId'];
//Pt. refractive Sheet obj.
$pt_ref_obj = new Pt_ref_sheet($patient_id);

/* Get  Data for Glasses */
$glass_data = $pt_ref_obj->get_glasses_data();

/* Get data for SCL Contact Lens */
$scl_cont_lens_data = $pt_ref_obj->get_scl_contact_lens_data();

/* Get data for Custom RGB lens */
$custom_rgb_lens_data = $pt_ref_obj->get_custom_rgp_lens_data();

if(strtolower($billing_global_server_name)=='precision'){
	//For Glasses
	$glasses_precision_data = $pt_ref_obj->get_precision_for_glasses();
	
	//For SCL Contact lens
	$scl_contact_precision_data = $pt_ref_obj->get_precision_for_scl_contact_lens();
}
/* Returns Modal data on ajax request */
if(isset($_POST['get_modal']) && isset($_POST['request_id'])){
	$request_id = $_POST['request_id'];
	$modal_str = $pt_ref_obj->get_modal($request_id);
	echo $modal_str;
	exit();
}

/* GET EXTERNAL MR DATA */
$get_external_mr_data = $pt_ref_obj->get_externalMR_data();

?>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		<title>Patient Refractive Sheet</title>
		<!-- Bootstrap -->
		<link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/bootstrap-dropdownhover.min.css" rel="stylesheet" type="text/css">
		<!-- Bootstrap Selctpicker CSS -->
		<link href="<?php echo $library_path; ?>/css/bootstrap-select.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/core.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/medicalhx.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/jquery.mCustomScrollbar.css" rel="stylesheet" type=
		"text/css">
		<link href="<?php echo $library_path; ?>/css/jquery-ui.min.css" rel="stylesheet" type="text/css">
	
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
		<!-- Bootstrap -->
		<script src="<?php echo $library_path; ?>/js/bootstrap.js" type="text/javascript" ></script>
		
		<!-- Bootstrap Selectpicker -->
		<script src="<?php echo $library_path; ?>/js/bootstrap-select.js" type="text/javascript"></script>
		<!-- Bootstrap typeHead -->
		<script src="<?php echo $library_path; ?>/js/bootstrap-typeahead.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/js/common.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/messi/messi.js" type="text/javascript"></script>
		<!--zPath variable to send variable value to callPopup function in work_view.js  -->
		<script type="text/javascript">var zPath = '<?php echo $zPath; ?>';</script>
		<script src="<?php echo $library_path; ?>/js/work_view/work_view.js" type="text/javascript"></script>
		<!-- jQuery's Date Time Picker -->
		<script src="<?php echo $library_path; ?>/js/jquery.datetimepicker.full.min.js" type="text/javascript" ></script>
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
		</style>
		<?php
			//====DELETE THE EXTERNAL MR RECORDS====
			if(!empty($_REQUEST['expelId']))
			{ 
				//$delId = base64_decode(urldecode($_REQUEST['expelId']));	
				$delId = $_REQUEST['expelId'];
				$updateQry = "UPDATE `chart_vis_ext_mr` SET del_status='1', del_date_time='".date('Y-m-d H:i:s')."', del_by ='".$authId."' WHERE id='".$delId."'";
				$exeQry = imw_query($updateQry);
				exit(0);
			}	
		?>
		
	</head>
	<body>
		<!---BELOW STYLE OF class mainwhtbox IS COMMENTED DUE TO POP IS COMING UP WITH TWO SCROLLS.. 1 WITH POP UP WINDOW, 2 WITH DATA WINDOW
		ms-overflow-y:scroll;-ms-overflow-style:scrollbar;-->
		<div class="mainwhtbox" style="max-height:auto;">
			<div class="row">
				<!-- Heading Row -->
				<div class="col-sm-12 purple_bar">
					<div class="row">
						<div class="col-sm-3">
							<label>Patient Refractive Sheet (PRS)</label>	
						</div>
						
						<div class="col-sm-5 text-center">
							<label><?php echo $patientNameID; ?></label>	
						</div>
						
						<div class="col-sm-2 text-right">
							<label><?php echo $patient_dob_age; ?></label>	
						</div>
						<div class="col-sm-1 text-right">
						<!--External VA Work Button Added -->
						<?php $indx=1; ?>	
						<button id="h2_mr_id<?php echo $indx; ?>" class="btn btn-success" onClick="callPopup('','popExtMR<?php echo $indx; ?>','1345','685','PRS');">Add External VA</button>	
						</div>	
						<div class="col-sm-1 text-right">
							<button class="btn btn-success" onClick="window.open('contact_lens_order_history.php','OrderHistory','location=0,status=1,resizable=1,left=10,top=80,scrollbars=no,width=1255,height=550');">CL Order HX</button>	
						</div>	
					</div>
				</div>

				<!-- Content -->
				<div class="col-sm-12">
					<div class="row adminbox">
						<!-- Left Side -->
						<div class="col-sm-6">
							<div class="row">
								<div class="col-sm-12 headinghd">
									<h4>Glasses (Refractive Rx)</h4>	
								</div>
								<div class="col-sm-12">
									<table class="table table-bordered table-striped">
										<tr class="grythead">
											<th>Date</th>
											<th class="text-center">Site</th>
											<th >S</th>
											<th>C</th>
											<th>A</th>
											<th>DV<sub>A</sub></th>
											<th>Add</th>
											<th>NV<sub>A</sub></th>
											<th >Prism</th>
											<th >Type</th>
											<th class="printCol"></th>
										</tr>
										<?php
											//Glasses Record
											foreach($glass_data as $key => $val){
												$counter = 0;
												foreach($val as $obj){
													$glasses_data_str_firsttd = $glasses_data_str_lasttd = "";
													if($obj['row_function']){
														$on_click_function = 'onclick="'.$obj['row_function'].'"';
													}
												
													if($obj['date_of_service']){
														$dos_arr = explode('~~',$obj['date_of_service']);
														$dos_rowspan = $dos_arr[0];
														$date_of_service = $dos_arr[1];
														
														//Only append in first iteration
														if($counter == 0){
															$glasses_data_str_firsttd = '<td rowspan="'.$dos_rowspan.'" class="text-nowrap">'.$date_of_service.'</td>';
														}
													}
													
													$print_func_arr = explode('~~',$obj['print_function']);													
													//Only append in first iteration
													if($counter == 0){
														if(count($print_func_arr) > 1){
														//if(!empty($obj['mrGivenPrint'])){
															$print_function = $print_func_arr[1];
															$glasses_data_str_lasttd = '<td rowspan='.$print_func_arr[0].' class="pointer" onclick=\''.$print_function.'\'><span class="glyphicon glyphicon-print"></span></td>';
														}else{
															$glasses_data_str_lasttd = '<td rowspan='.$print_func_arr[0].'></td>';
														}
													}
													
													$tmp_row = $tmp_type = "";
													$tmp_row = (count($obj['od']) > 0 && count($obj['os']) > 0) ? 2 : 1;
													if(!empty($obj['od']['mr_type'])){$tmp_type=$obj['od']['mr_type'];}elseif(!empty($obj['os']['mr_type'])){$tmp_type=$obj['os']['mr_type'];}
													$glasses_data_str_type = '<td class="'.$obj['class'].'" rowspan="'.$tmp_row.'">'.ucfirst($tmp_type).'</td>' ;
													
													if(count($obj['od']) > 0){													
													$glasses_data_str .= '<tr '.$on_click_function.'>';
													$glasses_data_str .= $glasses_data_str_firsttd; $glasses_data_str_firsttd="";
													$glasses_data_str .= '<td class="text-center"><span class="od">OD<span></td>';
													$glasses_data_str .= '<td class="'.$obj['class'].'">'.$obj['od']['vis_mr_od_s'].'</td>';
													$glasses_data_str .= '<td class="'.$obj['class'].'">'.$obj['od']['vis_mr_od_c'].'</td>';
													$glasses_data_str .= '<td class="'.$obj['class'].'">'.$obj['od']['vis_mr_od_a'].'</td>';
													$glasses_data_str .= '<td class="'.$obj['class'].'">'.$obj['od']['vis_mr_od_txt_1'].'</td>';
													$glasses_data_str .= '<td class="'.$obj['class'].'">'.$obj['od']['vis_mr_od_add'].'</td>';
													$glasses_data_str .= '<td class="'.$obj['class'].'">'.$obj['od']['vis_mr_od_txt_2'].'</td>';
													$glasses_data_str .= '<td class="'.$obj['class'].' text-nowrap"><span>'.$obj['od']['prism_od'].'</span></td>';
													$glasses_data_str .= $glasses_data_str_type; $glasses_data_str_type="";
													$glasses_data_str .= $glasses_data_str_lasttd; $glasses_data_str_lasttd="";
													$glasses_data_str .= '</tr>';
													}
													if(count($obj['os']) > 0){
														$glasses_data_str .= '<tr '.$on_click_function.'>';
														$glasses_data_str .= $glasses_data_str_firsttd; $glasses_data_str_firsttd="";
														$glasses_data_str .= '<td class="text-center"><span class="os">OS<span></td>';
														$glasses_data_str .= '<td class="'.$obj['class'].'">'.$obj['os']['vis_mr_os_s'].'</td>';	
														$glasses_data_str .= '<td class="'.$obj['class'].'">'.$obj['os']['vis_mr_os_c'].'</td>';	
														$glasses_data_str .= '<td class="'.$obj['class'].'">'.$obj['os']['vis_mr_os_a'].'</td>';	
														$glasses_data_str .= '<td class="'.$obj['class'].'">'.$obj['os']['vis_mr_os_txt_1'].'</td>';	
														$glasses_data_str .= '<td class="'.$obj['class'].'">'.$obj['os']['vis_mr_os_add'].'</td>';	
														$glasses_data_str .= '<td class="'.$obj['class'].'">'.$obj['os']['vis_mr_os_txt_2'].'</td>';	
														$glasses_data_str .= '<td class="'.$obj['class'].' text-nowrap"><span>'.$obj['os']['prism_os'].'</span></td>';
														$glasses_data_str .= $glasses_data_str_type; $glasses_data_str_type="";
														$glasses_data_str .= $glasses_data_str_lasttd; $glasses_data_str_lasttd="";														
														$glasses_data_str . '</tr>';
													}
													$counter++;
												}
											} 
											echo $glasses_data_str;
											
											//For precision 
											if($glasses_precision_data && count($glasses_precision_data) > 0){
												foreach($glasses_precision_data as $obj){
													if(count($obj['OD']) > 0){
														$glasses_precision_str .= '<tr>';
														if($obj['OD']['DATE']){
															$glasses_precision_str .= '<td rowspan="2">'.$obj['OD']['DATE'].'</td>';
														}
														$glasses_precision_str .= '<td class="text-center"><span class="od">OD<span></td>';
														$glasses_precision_str .= "<td>".$obj['OD']['S']."</td>";
														$glasses_precision_str .= "<td>".$obj['OD']['C']."</td>";
														$glasses_precision_str .= "<td>".$obj['OD']['AX'].((trim($obj['OD']['AX'])!="")?"<span class=\"degree\">&deg;</span>":"")."</td>";
														$glasses_precision_str .= "<td>".$obj['OD']['VD']."</td>";
														$glasses_precision_str .= "<td>".$obj['OD']['P']."</td>";
														$glasses_precision_str .= "<td>".$obj['OD']['AD']."</td>";
														$glasses_precision_str .= "<td>&nbsp;</td>";
														if($obj['OD']['no_print']){
															$glasses_precision_str .= '<td class="printCol" rowspan="2">&nbsp;</td>';
														}
														$glasses_precision_str .= '</tr>';
													}
													
													if(count($obj['OS']) > 0){
														$glasses_precision_str .= '<tr>';
														if($obj['OS']['DATE']){
															$glasses_precision_str .= '<td rowspan="2">'.$obj['OS']['DATE'].'</td>';
														}
														$glasses_precision_str .= '<td class="text-center"><span class="od">OS<span></td>';
														$glasses_precision_str .= "<td>".$obj['OS']['S']."</td>";
														$glasses_precision_str .= "<td>".$obj['OS']['C']."</td>";
														$glasses_precision_str .= "<td>".$obj['OS']['AX'].((trim($obj['OS']['AX'])!="")?"<span class=\"degree\">&deg;</span>":"")."</td>";
														$glasses_precision_str .= "<td>".$obj['OS']['VD']."</td>";
														$glasses_precision_str .= "<td>".$obj['OS']['P']."</td>";
														$glasses_precision_str .= "<td>".$obj['OS']['AD']."</td>";
														$glasses_precision_str .= "<td>&nbsp;</td>";
														if($obj['OS']['no_print']){
															$glasses_precision_str .= '<td class="printCol" rowspan="2">&nbsp;</td>';
														}	
														$glasses_precision_str .= '</tr>';
													}	
													
												}	
											}
											
											if($pt_ref_obj->noDataGlasses && !$pt_ref_obj->glasses_precision_flag){
												echo '<tr><td colspan="11"><span class="noData">No Data Found</span></td></tr>';
											}else{
												echo $glasses_precision_str;
											}	
										?>
									</table>	
								</div>	
							</div>	
							<!-- EXTERNAL MR WORK STARTS HERE--->
							<div class="row">
								<div class="col-sm-12 headinghd">
									<h4>Glasses (External Rx)</h4>	
								</div>
								<div class="col-sm-12">
									<table id="external_glasses_rx" class="table table-bordered table-striped">
										<tr class="grythead">
											<th>Date</th>
											<th class="text-center">Site</th>
											<th >S</th>
											<th>C</th>
											<th>A</th>
											<th>DV<sub>A</sub></th>
											<th>Add</th>
											<th>NV<sub>A</sub></th>
											<th>Prism</th>
											<th>Physician</th>
											<th class="printCol"></th>
										</tr>
										<?php
											echo $get_external_mr_data;
										?>
									</table>	
								</div>	
							</div>	
							<!--WORK ENDS HERE-->
						</div>
						
						<!-- Right Side -->
						<div class="col-sm-6">
							<div class="row">
								<!-- SCL - Contact Lens (Rx) -->
								<div class="col-sm-12">
									<div class="row">
										<div class="col-sm-12 headinghd">
											<h4>SCL - Contact Lens (Rx)</h4>	
										</div>
										<div class="col-sm-12">
											<table class="table table-bordered table-striped">
												<tr class="grythead">
													<th>Date</th>
													<th class="text-center">Site</th>
													<th>BC</th>
													<th>DI</th>
													<th>S</th>
													<th>C</th>
													<th>A</th>
													<th>ADD</th>
													<th>DV<sub>A</sub></th>
													<th>NV<sub>A</sub></th>
													<th>Type</th>
													<th class="printCol"></th>
												</tr>
												<?php 

													// For SCL Contact lens
													foreach($scl_cont_lens_data as $key => $val){
														$counter = 0;
														foreach($val as $obj){
																if($obj['bg_color']){
																$bg_color = $obj['bg_color'];
															}
															if($obj['date_of_service']){
															    $dos_arr = explode('~~',$obj['date_of_service']);
															    if($dos_arr[2] != "Final"){
															        $bg_color = "#FFFFFF";
															    }
															}
															$scl_contact_lens_str .= '<tr '.$bg_color.'>';
															if($obj['date_of_service']){
																$dos_arr = explode('~~',$obj['date_of_service']);
																$rowspan = $dos_arr[0];
																$date_of_service = $dos_arr[1];
																$clws_type_part = $dos_arr[2];
																//Only appends on first iteration
																if($counter ==0){
																	$scl_contact_lens_str .= '<td rowspan="'.$rowspan.'" >'.$date_of_service.'<br>'.$clws_type_part.'</td>';
																}
															}
															$scl_contact_lens_str .= '<td class="text-center"><span class="'.strtolower($obj['site']).'">'.$obj['site'].'</span></td>';
															$scl_contact_lens_str .= '<td>'.$obj['BC'].'</td>';
															$scl_contact_lens_str .= '<td>'.$obj['DI'].'</td>';
															$scl_contact_lens_str .= '<td>'.$obj['sp'].'</td>';
															$scl_contact_lens_str .= '<td>'.$obj['cy'].'</td>';
															$scl_contact_lens_str .= '<td>'.$obj['ax'].'</td>';
															$scl_contact_lens_str .= '<td>'.$obj['ad'].'</td>';
															$scl_contact_lens_str .= '<td>'.$obj['dva'].'</td>';
															$scl_contact_lens_str .= '<td>'.$obj['nva'].'</td>';
															$scl_contact_lens_str .= '<td>'.$obj['type'].'</td>';
															if($counter == 0){
																//Only appends on first iteration	
																if($obj['print_function'] && $dos_arr[2] == "Final"){
																	$scl_contact_lens_str .= '<td  class="printCol" rowspan="'.$rowspan.'"><span class="printicon glyphicon glyphicon-print pointer" onclick='.$obj['print_function'].'></span></td>';
																}	
															}
															$scl_contact_lens_str .= '</tr>';
															$counter++;
														}
													}
													echo $scl_contact_lens_str;

													//For Precision
													if($scl_contact_precision_data && count($scl_contact_precision_data) > 0){
														foreach($scl_contact_precision_data as $obj){
															if(count($obj['OD']) > 0){
																$scl_precision_str .= '<tr>';
																if($obj['OD']['DATE']){
																	$scl_precision_str .= '<td rowspan="2">'.$obj['OD']['DATE'].'</td>';
																}
																$scl_precision_str .= '<td class="text-center"><span class="od">OD<span></td>';
																$scl_precision_str .= "<td>".$obj['OD']['BC']."</td>";
																$scl_precision_str .= "<td>".$obj['OD']['DI']."</td>";
																$scl_precision_str .= "<td>".$obj['OD']['S']."</td>";
																$scl_precision_str .= "<td>".$obj['OD']['C']."</td>";
																$scl_precision_str .= "<td>".$obj['OD']['AX'].((trim($obj['OD']['AX'])!="")?"<span class=\"degree\">&deg;</span>":"")."</td>";
																$scl_precision_str .= "<td>".$obj['OD']['AD']."</td>";
																$scl_precision_str .= "<td>&nbsp;</td>";
																$scl_precision_str .= "<td>&nbsp;</td>";
																$scl_precision_str .= "<td>".$obj['OD']['LT']."</td>";
																$scl_precision_str .= "<td>&nbsp;</td>";
																if($obj['OD']['no_print']){
																	$scl_precision_str .= '<td class="printCol" rowspan="2">&nbsp;</td>';
																}
																$scl_precision_str .= '</tr>';
															}
															
															if(count($obj['OS']) > 0){
																$scl_precision_str .= '<tr>';
																if($obj['OS']['DATE']){
																	$scl_precision_str .= '<td rowspan="2">'.$obj['OS']['DATE'].'</td>';
																}
																$scl_precision_str .= '<td class="text-center"><span class="od">OS<span></td>';
																$scl_precision_str .= "<td>".$obj['OS']['BC']."</td>";
																$scl_precision_str .= "<td>".$obj['OS']['DI']."</td>";
																$scl_precision_str .= "<td>".$obj['OS']['S']."</td>";
																$scl_precision_str .= "<td>".$obj['OS']['C']."</td>";
																$scl_precision_str .= "<td>".$obj['OS']['AX'].((trim($obj['OS']['AX'])!="")?"<span class=\"degree\">&deg;</span>":"")."</td>";
																$scl_precision_str .= "<td>".$obj['OS']['AD']."</td>";
																$scl_precision_str .= "<td>&nbsp;</td>";
																$scl_precision_str .= "<td>&nbsp;</td>";
																$scl_precision_str .= "<td>".$obj['OS']['LT']."</td>";
																$scl_precision_str .= "<td>&nbsp;</td>";
																if($obj['OS']['no_print']){
																	$scl_precision_str .= '<td class="printCol" rowspan="2">&nbsp;</td>';
																}
																$scl_precision_str .= '</tr>';
															}
														}	
													}

													if($pt_ref_obj->noDataSCL && !$pt_ref_obj->scl_precision_flag){
														echo '<tr><td colspan="12"><span class="noData">No Data Found</span></td></tr>';
													}else{
														echo $scl_precision_str;
													}	
												?>
											</table>	
										</div>	
									</div>	
								</div>
								
								<!-- Custom/RGP - Contact Lens (Rx) -->
								<div class="col-sm-12">
									<div class="row">
										<div class="col-sm-12 headinghd">
											<h4>Custom/RGP - Contact Lens (Rx)</h4>
										</div>
										<div class="col-sm-12">
											<table class="table table-bordered table-striped">
												<tr class="grythead">
													<th>Date</th>
													<th class="text-center">Site</th>
													<th>BC</th>
													<th>DI</th>
													<th>P</th>
                                                    <th>C</th>
                                                    <th>A</th>
													<th>Desc</th>
													<th>Col</th>
													<th>ADD</th>
													<th>DV<sub>A</sub></th>
													<th>NV<sub>A</sub></th>
													<th>Type</th>
													<th class="printCol"></th>
												</tr>
												<?php
												    $bg_colorRGP = "";
													foreach($custom_rgb_lens_data as $key => $val){
														$counter = 0;
														foreach($val as $obj){
															if($obj['bgColorRGP']){
															    $bg_colorRGP = $obj['bgColorRGP'];
															}
															$scl_custom_lens_str .= '<tr '.$bg_colorRGP.'>';
															if($obj['date_of_service']){
																$dos_arr = explode('~~',$obj['date_of_service']);
																$rowspan = $dos_arr[0];
																$date_of_service = $dos_arr[1];
																$clws_type_part = $dos_arr[2];
																//Only append on first iteration
																if($counter == 0){
																	$scl_custom_lens_str .= '<td rowspan="'.$rowspan.'" class="text-nowrap">'.$date_of_service.'<br>'.$clws_type_part.'</td>';
																}
															}
															if($obj['site']){
																$scl_custom_lens_str .= '<td class="text-center"><span class="'.strtolower($obj['site']).'">'.$obj['site'].'</span></td>';
															}
															$scl_custom_lens_str .= "<td>".$obj['BC']."</td>";
															$scl_custom_lens_str .= "<td>".$obj['DI']."</td>";
															$scl_custom_lens_str .= "<td>".$obj['sp']."</td>";
															$scl_custom_lens_str .= "<td>".$obj['cyl']."</td>";
															$scl_custom_lens_str .= "<td>".$obj['axis'].((trim($obj['axis'])!="")?"<span class=\"degree\">&deg;</span>":"")."</td>";
															$scl_custom_lens_str .= "<td>".$obj['cy']."</td>";
															$scl_custom_lens_str .= "<td>".$obj['ax']."</td>";
															$scl_custom_lens_str .= "<td>".$obj['ad']."</td>";
															$scl_custom_lens_str .= "<td>".$obj['dva']."</td>";
															$scl_custom_lens_str .= "<td>".$obj['nva']."</td>";
															$scl_custom_lens_str .= "<td class=\"noRight\">".$obj['type']."</td>";
															//Only append on first iteration
															if($counter == 0){
																if($obj['print_function'] && $dos_arr[2] == "Final"){
																	$scl_custom_lens_str .= '<td  class="printCol" rowspan="'.$rowspan.'"><span class="glyphicon glyphicon-print printicon" onclick='.$obj['print_function'].'></span></td>';
																}
															}
															$scl_custom_lens_str .= '</tr>';	
															$counter++;
														}
													}
													echo $scl_custom_lens_str;
												?>	
											</table>	
										</div>	
									</div>	
								</div>	
							</div>	
						</div>
					</div>	
				</div>	
			</div>	
		</div>
		
		<script>
			$(document).ready(function(){
				$(".del_ext_mr").click(function(){
					var extMRId = 0;
					extMRId = $(this).attr("id");
					$.ajax({
                    type:'POST',
                    data:'expelId=' + extMRId,
						success:function(response){
							$("#tr_"+extMRId+"_OD").remove();
							$("#tr_"+extMRId+"_OS").remove();
							$("#tr_"+extMRId+"_DESC").remove();
							top.fAlert("Record deleted successfully!");
							if($("#external_glasses_rx tbody tr").length == 1){
								$("#external_glasses_rx tbody").append("<tr><td colspan='11'><span class='noData'>No Data Found</span></td></tr>");
							}
						}
                	});
				});
			});
			function printMrPRS(givenMr,e,fId){
				var parWidth = parent.document.body.clientWidth/2;
				var parHeight = parent.document.body.clientHeight/2;
				
				window.open('requestHandler.php?printType=1&elem_formAction=print_mr&chartIdPRS='+fId+'&givenMr='+givenMr+'&sectionName=fromPRS','printPatientMr','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+',top=0,left=0');
				//window.open('print_patient_mr.php?printType=1&chartIdPRS='+fId+'&givenMr='+givenMr,'printPatientMr','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+',top=0,left=0');
				e.stopPropagation();
			}
			function printRxPRS(workSheetId){
				var parWidth = parent.document.body.clientWidth/2;
				var parHeight = parent.document.body.clientHeight/2;
				window.open('print_patient_contact_lenses.php?printType=2&method=1&workSheetId='+workSheetId+'&sectionName=fromPRS','printPatientContact','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+',top=0,left=0');
			}
			
			function detailMr(mrId,obj,e){
				e.stopPropagation();
				if(mrId != '' || typeof(mrId) != 'undefined'){
					var data = 'get_modal=yes&request_id='+mrId;
					$.ajax({
						url:'patient_refractive_sheet.php',
						data:data,
						type:'POST',
						success:function(response){
							show_modal('glasses_values','GLASSES (REFRACTIVE RX)',response,'','500','modal-lg','false');
							return false;
						}
					});
				}
				return false;
			}

			$(window).load(function(){
				if(typeof(window.opener.top.innerDim)=='function'){
					var innerDim = window.opener.top.innerDim();
					if(innerDim['w'] > 1600) innerDim['w'] = 1600;
					if(innerDim['h'] > 900) innerDim['h'] = 900;
					window.resizeTo(innerDim['w'],innerDim['h']);
					brows	= get_browser();
					if(brows!='ie') innerDim['h'] = innerDim['h']-35;
					var result_div_height = innerDim['h']-210;
					//$('.mainwhtbox').height(result_div_height+'px');
				}
			});
		</script>	
	</body>
</html>