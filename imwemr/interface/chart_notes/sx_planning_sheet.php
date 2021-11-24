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

File: sx_planning_sheet.php
Purpose: This file provides SX Planning Sheet in work view.
Access Type : Direct
*/
require_once(dirname(__FILE__).'/../../config/globals.php');

require($GLOBALS['incdir']."/chart_notes/chart_globals.php");
require($GLOBALS['srcdir']."/classes/work_view/wv_functions.php");
require($GLOBALS['srcdir']."/classes/work_view/wv_functions_new.php");

//Check patient session and closing popup if no patient in session
$window_popup_mode = true;
require_once($GLOBALS['srcdir']."/patient_must_loaded.php");

include_once $GLOBALS['srcdir']."/classes/work_view/sx_plan.class.php";
$library_path = $GLOBALS['webroot'].'/library';

//Setting form_id to get record
$form_id = '';
$finalize_flag = 0;
if(isset($_SESSION["finalize_id"]) && !empty($_SESSION["finalize_id"])){
	$finalize_flag = 1;
}
if(isset($_GET["finalize_id"]) && !empty($_GET["finalize_id"])){
	$finalize_flag = 1;
}

$patient_id = $_SESSION['patient'];
$sx_pl_id = (isset($_REQUEST['get_sx_id']) && empty($_REQUEST['get_sx_id']) == false) ? getDateFormatDB($_REQUEST['get_sx_id']) : '';
$sx_obj = New Sx_Plan($patient_id,$sx_pl_id,$finalize_flag);

//Sx Plan Data
$sx_plan_data = '';
$sx_plan_data = $sx_obj->get_sx_plan_data($sx_obj->patient_id,$sx_obj->sx_plan_id);
if(!isset($_REQUEST['get_frm'])){
	extract($sx_plan_data);
	if(empty($el_domi)) $el_domi = $domEye['dominantEye'];
	if(empty($el_pachy)) $el_pachy = $domEye['pachyValues'];
}


//Sx Plan DOS data
$sx_plan_dos_arr = $sx_obj->get_pt_sx_dos_arr($sx_obj->patient_id);

$el_date_surgery = ($el_date_surgery == '00-00-0000') ? '' : $el_date_surgery;
$el_prev_eye_date = ($el_prev_eye_date == '00-00-0000') ? '' : $el_prev_eye_date;


//Previous Test Data dropdown
$data_dv_drop_down_tests = $sx_obj->sps_getTestsDropDown($sx_obj->patient_id,$el_ids_iol, $el_ids_ascan, $el_ids_oct, $el_ids_topo, $el_ids_vf);

//Prev. Data
$data_div_sx_pop_up = $sx_obj->sps_getPrvSxPlanSheet($sx_obj->patient_id);

//Previous Eye Data
$prevEyeArr = $sx_obj->getPrevEyeValues($sx_obj->patient_id, $id_chart_sx_plan_sheet);

//Dominant Eye
$domEye = $sx_obj->getDomiEye($sx_obj->patient_id);

// Only replace dominant eye with prev dominant eye if, call if for wither new sheet or first load sheet
if(!$sx_obj->sx_plan_id){
	if(empty($el_domi)) $el_domi = $domEye['dominantEye'];
	if(empty($el_pachy)) $el_pachy = $domEye['pachyValues'];
}

// ------- Ajax Request --------
//Refresh Film Strip
if(isset($_GET["refresh_flim_strip"]) && !empty($_GET["refresh_flim_strip"])){
	$id_chart_sx_plan_sheet = $_GET["t_id_chart_sx_plan_sheet"];
	$el_img_checked = $_GET["t_el_img_checked"];
	
	$el_ids_iol = $_GET["t_el_ids_iol"];
	$el_ids_ascan = $_GET["t_el_ids_ascan"];
	$el_ids_oct = $_GET["t_el_ids_oct"];
	$el_ids_topo = $_GET["t_el_ids_topo"];
	$el_ids_vf = $_GET["t_el_ids_vf"];
	
	$data_fstrip = $sx_obj->getFStripData($id_chart_sx_plan_sheet,$patient_id,$el_img_checked,$el_ids_iol, $el_ids_ascan, $el_ids_oct, $el_ids_topo, $el_ids_vf);
	echo $data_fstrip;
	exit();
}


if(isset($_GET["ld_prv_val"]) && !empty($_GET["ld_prv_val"])){
	$sx_obj->sps_get_load_prv_val($_GET["ld_prv_val"]);
	exit();
}

if(isset($_GET["eye"]) && !empty($_GET["eye"])){
	$ar_ret=array();
	
	$masterValues = $sx_obj->getMasterDataIOL(trim($_GET["eye"]), trim($patient_id));
	echo $masterValues;
	exit();
}

//Used to get IOL Model values
if(isset($_REQUEST['getIOlModel']) && isset($_REQUEST['phyId'])){
	$returnArr = $valueArr = array();
	
	$valueArr = $sx_obj->getIolModelValues($_REQUEST['getIOlModel'], $_REQUEST['phyId']);
	echo json_encode($valueArr);
	exit();
}

//Used To Get IOL Lenses
if(isset($_GET["getProvIOL"]) && !empty($_GET["getProvIOL"])){
	$lensArr = $returnArr = array();
	//Get Provider Based IOL Lenses
	$selProvLens = $sx_obj->getIOLLens($_GET["getProvIOL"], true);
	
	//if(is_array($selProvLens) && count($selProvLens) > 0){
		//Get Lens Values
		$lensArr = $sx_obj->getLensType($_GET["getProvIOL"], $_GET['chartId'], $selProvLens);
	//}
	
	if(count($lensArr) > 0){
		for($i = 0; $i < 4; $i++){
			$lensVal = &$lensArr[$i];
			
			if($lensVal === NULL) {
				$lensVal = $sx_obj->lensFields;
				$lenId = '';
			}else{
				$lenId = (isset($lensVal['ID']) && empty($lensVal['ID']) == false) ? $lensVal['ID'] : '';
			}
			
			if(empty($lenId) == true) unset($lensVal['ID']);
			
			$returnArr[] = $lensVal;
		}
	}
	//pre($returnArr);
	echo json_encode($returnArr);
	exit();
}

//Film Strip Data
$data_fstrip = $sx_obj->getFStripData($id_chart_sx_plan_sheet,$patient_id,$el_img_checked,$el_ids_iol, $el_ids_ascan, $el_ids_oct, $el_ids_topo, $el_ids_vf);

//Allergies
$ptAllergies = "No";
$allergy = $sx_obj->getAllergies($sx_obj->patient_id,"title",1);
$checkAllergy = commonNoMedicalHistoryAddEdit($moduleName="Allergy",$moduleValue="",$mod="get");
if($checkAllergy == "checked"){
	$allergy = "2";	  // means NKA checkbox is checked
}
$ptAllergies=($allergy=="1") ? "Yes" : "No";

//Diabetes
$strPtDiabetic = $sx_obj->getPtDiabeticVal($sx_obj->patient_id);
$arrPtDiabetic =  explode(" -- ", $strPtDiabetic);
$ptDiabetic = $arrPtDiabetic[0];

//Flomax
$pt_flomax = "No"; //from latest ascan or IOL_master
$pt_flomax = $sx_obj->getPtFlomax($sx_obj->patient_id);

//Multi phy data
$phy_dt_str = $sx_obj->getMultiPhy($sx_obj->patient_id);
$arr_phyInfo = explode("!@!", $phy_dt_str);
$ptPcp = $arr_phyInfo[1];
$ptRefer = $arr_phyInfo[0];
$ptCoManage = $arr_phyInfo[2];

//Medication
$ar_pt_med = $sx_obj->sps_getPtMeds($sx_obj->patient_id);

//Pt choice arr
$ar_admn_pt_choices = $sx_obj->sps_get_pt_choices();
$ar_admn_mbn = $sx_obj->sps_get_mbn();
$ar_admn_toric_btn = $sx_obj->sps_get_toric_buttons();

//IOL Recommendations
$ar_admn_iol_recomd = $sx_obj->sps_get_iol_master_recomds();
$ar_admn_ecp = $sx_obj->sps_getECP();

//Pt. Data
$pt_data = $sx_obj->get_patient_details();

//variables needed in JS File
$js_php_arr = array();
$js_php_arr['elem_per_vo'] = $elem_per_vo;
$js_php_arr['sess_pt'] = $sx_obj->patient_id;
$js_php_arr['finalize_flag'] = $finalize_flag;
//$js_php_arr['logged_user_type'] = $_SESSION["logged_user_type"];
$js_php_arr['logged_user_type'] = (isset($_SESSION["user_role"]) && !empty($_SESSION["user_role"])) ? $_SESSION["user_role"] : $_SESSION["logged_user_type"];
$js_php_arr['el_date_surgery'] = $el_date_surgery;
$js_php_arr['el_prev_eye_date'] = $el_prev_eye_date;
$js_php_arr['web_root'] = $GLOBALS['webroot'];
$js_php_arr['form_id'] = $sx_obj->sx_plan_id;
$js_php_arr['id_chart_sx_plan_sheet'] = $GLOBALS['id_chart_sx_plan_sheet'];
$js_php_arr['dos_arr'] = $sx_plan_dos_arr;
$js_php_arr['save_status'] = (isset($_REQUEST['save_status']) && empty($_REQUEST['save_status']) == false) ? $_REQUEST['save_status'] : '';
$js_php_arr['js_date_format'] = (isset($_REQUEST['get_date_format']) && empty($_REQUEST['get_date_format']) == false) ? $_REQUEST['get_date_format'] : 'm-d-Y';
$js_php_arr['iolLensArr'] = $sx_obj->iol_lenses;
$js_php_arr['staticIolArr'] = $sx_obj->lensFields;
$js_php_arr['staticIolModel'] = $sx_obj->arr_lens;
if(isset($_REQUEST['get_frm']) && empty($_REQUEST['get_frm']) == false) $js_php_arr['get_frm'] = $_REQUEST['get_frm'];
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		<title>Sx Planning Sheet</title>
		<!-- Bootstrap -->
		<link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
		<!-- Bootstrap Selctpicker CSS -->
		<link href="<?php echo $library_path; ?>/css/bootstrap-select.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/workview.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/wv_landing.css" rel="stylesheet" type="text/css">
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
		<script src="<?php echo $library_path; ?>/js/common.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/messi/messi.js" type="text/javascript"></script>
		<style>
			.dropdown-menu{max-height:200px!important;}
			#dv_fstrip .thumbnail{position: relative;padding: 3px;border:none;margin-bottom:0px}
			#dv_fstrip .thumbnail img{padding: 0;margin: 0;width: 100%;}
			#dv_fstrip .thumbnail .caption{padding: 1px;font-size: 10px;position: absolute;bottom: 0;background-color: rgba(0,0,0,0.5);width: 100%;color: #fff;left: 0px;border-radius:0px 0px 4px 4px}
			.readPrevEye{background: rgba(0,0,0,0.2);}
		</style>
	</head>
	<body onload="fullScreen();">
		<form name="printSxPlann" id="printSxPlann" method="POST" class="hide" action="sx_planning_sheet_print.php" target="_blank">
			<input type="hidden" name="chartId" value="<?php echo $id_chart_sx_plan_sheet; ?>">
			<input type="hidden" name="patientId" value="<?php echo $sx_obj->patient_id; ?>">
		</form>	
		<form id="frm_sx_plan_sheet" name="frm_sx_plan_sheet" action="save_sx_plan_sheet.php" method="post" onSubmit="return check_form();"> <!-- saveCharts.php -->
			<div class="whtbox">	
				<input type="hidden" id="elem_sx_plan_sheet_id" name="elem_sx_plan_sheet_id" value="<?php echo $id_chart_sx_plan_sheet;?>">
				<input type="hidden" id="elem_examDate" name="elem_examDate" value="<?php echo $sx_obj->elem_examDate;?>">
				<input type="hidden" id="elem_form_id" name="elem_form_id" value="0">
				<input type="hidden" id="elem_plan_dos" name="elem_plan_dos" value="<?php echo $sx_obj->sx_plan_id;?>">
				<input type="hidden" id="elem_provider_id" name="elem_provider_id" value="<?php echo $elem_provider_id;?>">
				<input type="hidden" id="el_img_checked_db" name="el_img_checked_db" value="<?php echo $sx_obj->el_img_checked;?>">
				<div class="purple_bar">
					<div class="row">
						<div class="col-sm-3">
							<div class="row">
								<div class="col-sm-5">
									<span>
										<strong>Sx Planning Sheet</strong>
									</span>	
								</div>	
								<div class="col-sm-7">
									<div class="row">
										<div class = "input-group">
											<?php 
												$date_curr = get_date_format($sx_obj->pt_dos);
												if(isset($sx_plan_dos) && empty($sx_plan_dos) == false){
													$date_curr = get_date_format($sx_plan_dos);
												} 
											?>
											<input type = "text" class ="form-control" id="sx_pl_date" name="new_sx_dos" value="<?php echo $date_curr ; ?>">
											<div class = "input-group-btn">
											  <button type = "button" class = "btn btn-default dropdown-toggle" 
												 data-toggle = "dropdown"> 
												 <span class = "caret"></span>
											  </button>
											  <ul class = "dropdown-menu">
												 <li><a href = "#">Previous DOS</a></li>
												 <li class = "divider"></li>
												 <?php 
													$dos_opt = '';
													if(count($sx_plan_dos_arr) > 0){
														foreach($sx_plan_dos_arr as $obj){
															$dos_opt .= '<li onclick="get_sx_dos(this);" data-id="'.$obj['id'].'"><a href="javascript:void(0);">'.$obj['sx_pl_dos'].' - <strong>'.$obj['mank_eye'].'</strong></a></li>';
														}
													}else{
														$dos_opt = '<li class="text-danger"><a href="javascript:void(0);" class="disabled_pointer" disabled>No DOS</a></li>';
													}
													echo $dos_opt; 
												 ?>
											  </ul>
										   </div>
										</div>
									</div>
								</div>	
							</div>
							
						</div>
						<div class="col-sm-6 text-center">
							<span><strong>Patient Name : </strong> <?php echo $pt_data['pt_name']; ?> - <?php echo $sx_obj->patient_id  ?></span>
						</div>
					</div>	
				</div>
				<div class="main_content_wrapper">
					<div class="sxtopbar">
						<div class="container-fluid">
							<div class="row">
								<div class="col-sm-6 sxflt">
									<ul>
										<li class="form-inline">
											<label for="el_sx_type">Sx</label>
											<select id="el_sx_type" name="el_sx_type" class="form-control minimal">
												<option value="">select</option>
												<option value="Cataract" <?php echo ($el_sx_type=="Cataract") ? "SELECTED" : "" ;?>>Cataract</option>
												<option value="Yag" <?php echo ($el_sx_type=="Yag") ? "SELECTED" : "" ;?>>Yag</option>
												<option value="LASIK/PRK" <?php echo ($el_sx_type=="LASIK/PRK") ? "SELECTED" : "" ;?>>LASIK/PRK</option>
											</select>
										</li>
										<li class="form-inline">
											<label for="el_pt_choice">Patient Choices</label>
											<select id="el_pt_choice" name="el_pt_choice" class="form-control minimal">
												<option value="">select</option>
												<?php
												$patient_choice_title_pdf = '';
													if(count($ar_admn_pt_choices)>0){
														foreach($ar_admn_pt_choices as $key => $val){
															if(!empty($val)){
																$sel = ($el_pt_choice == $val) ? "SELECTED" : "";
																if($el_pt_choice == $val)
																{
																	$patient_choice_title_pdf = $val;
																}
																echo "<option value=\"".$val."\" ".$sel." >".$val."</option>";
															}
														}
													}
												?>			
											</select>
										</li>
										
										<li class="form-inline">
											<label for="">Previous Sx/Procedures     Ocular:</label>
											<?php 
												$ocular_title_pdf = '';
												$str_ocu_meds="<option value=\"\">select</option>";
												if(count($ar_pt_med[6])>0){
													foreach($ar_pt_med[6] as $k=>$v){
														$title = $v[1];
														$id = $v[0];
														$tmp = ($el_prev_sx_ocu == $id) ? "selected" : "";
														if($el_prev_sx_ocu == $id)
														{
															$ocular_title_pdf = $title;
														}
														$str_ocu_meds .= "<option value=\"".$id."\" ".$tmp." >".$title."</option>";
													}
												}
												echo "<select id=\"el_prev_sx_ocu\" name=\"el_prev_sx_ocu\" class=\"form-control minimal\">".$str_ocu_meds."</select>";
											?>	
										</li>
										<li class="form-inline">
											<label for="">Systemic:</label>
											<?php 
												$systemetic_title_pdf = '';
												$str_ocu_meds="<option value=\"\">select</option>";
												if(count($ar_pt_med[5])>0){
													foreach($ar_pt_med[5] as $k=>$v){
														$title = $v[1];
														$id = $v[0];
														$tmp = ($el_prev_sx_sys == $id) ? "selected" : "";
														if($el_prev_sx_sys == $id)
														{
															$systemetic_title_pdf = $title;
														}
														$str_ocu_meds .= "<option value=\"".$id."\" ".$tmp." >".$title."</option>";
													}
												}
												echo "<select id=\"el_prev_sx_sys\" name=\"el_prev_sx_sys\" class=\"form-control minimal\">".$str_ocu_meds."</select>";
											?>	
										</li>	
									</ul>
								</div>
								
								<div class="col-sm-3 sxoptn">
									<div class="row">
										<ul>
											<li>Allergies : <?php echo $ptAllergies; ?></li>
											<li>Diabetic : <?php echo $ptDiabetic; ?> </li>
											<li>Flomax : <?php echo $pt_flomax; ?> </li>
										</ul>
									</div>
								</div>
								
								<div class="col-sm-3 sxptopt">
									<ul>
										<li class="sxpc">
											<span>PCP :</span>
											<?php 
												echo $ptPcp;
											?>
										</li>
										<li class="sxrefe">
											<span>Referring :</span>
											<?php 
												echo $ptRefer;
											?>
										</li>
										<li class="sxcomanage">
											<span>Co Managed :</span>
											<?php 
												echo $ptCoManage;
											?>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
					<div class=" clearfix"></div>
					<div class="container-fluid">
						<div id="tbl_ks" class="row">
							<div class="col-sm-3">
								<div class="sxdtbx sxboxhght">
									<div class="sxhed">
										<h2><input type="checkbox" id="el_k_given_manual" name="el_k_given_manual" value="Manual" class="frcb" <?php echo ($el_k_given=="Manual") ? "CHECKED" : "" ;?>><label for="el_k_given_manual" class="frcb">Manual K's</label></h2>
									</div>
									<div class="clearfix"></div>
									<div class="plr5">
										<div class="row">
											<div class="col-sm-6 eyopt ">
												<label for="el_mank_eye" class=" control-label">Eye</label>
												<select id="el_mank_eye" name="el_mank_eye" class="form-control minimal" onchange="get_eyes_details(this.value);">
													<option value="">Eye</option>
													<option value="OD" <?php echo ($el_mank_eye == "OD") ? "selected" : "" ;?>>OD</option>
													<option value="OS" <?php echo ($el_mank_eye == "OS") ? "selected" : "" ;?>>OS</option>
												</select>		
											</div>
											<div class="col-sm-6">
												<div>
													<label for="el_mank_ref">Ref</label>
													<input type="text" id="el_mank_ref" name="el_mank_ref" value="<?php echo $el_mank_ref;?>" class="form-control">
												</div>
												<div class="clearfix"></div>
												<div class="surgopt" >
													<h2>Surgery</h2>
													<input type="text" id="el_date_surgery" name="el_date_surgery" class="form-control" value="<?php echo $el_date_surgery;?>" onchange="$('#el_time_surgery')[0].click();" placeholder="Date">
													<input type="text" id="el_time_surgery" name="el_time_surgery" value="<?php echo $el_time_surgery;?>" onClick="this.value=currenttime();" placeholder="Time" class="form-control">
												</div>
												<div class="clearfix"></div>
												<div class="surgenopt">
													<h2>Surgeon</h2>
													<select class="form-control minimal" name="el_surgeon_id" id="el_surgeon_id" onChange="getLenses(this.value);">
														<option value=""></option>
														<?php
														$pdf_selected_surgeon = '';
														$tmp = $el_surgeon_id;
														$phyArray = $sx_obj->getMrPersonnal(2,"pro_only");
														foreach($phyArray as $pId => $physicianNameOS){
															if($tmp == $pId){
																$pdf_selected_surgeon = $physicianNameOS;
															}
															?>
															<option value="<?php echo $pId; ?>" <?php if($tmp==$pId) echo "SELECTED"; ?>><?php echo $physicianNameOS; ?></option>
															<?php
														}
														?>
													</select>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							
							<div class="col-sm-6">
								<div class="sxdtbx sxboxhght">
									<div class="row">
										<div class="col-sm-6">
											<div class="sxhed">
												<div class="row">
													<div class="col-sm-5">
														<h2><input type="checkbox" id="el_k_given_auto" name="el_k_given_auto" class="frcb" value="Auto" <?php echo ($el_k_given=="Auto") ? "CHECKED" : "" ;?>><label for="el_k_given_auto" class="frcb">Auto K's</label></h2>
													</div>
													<div class="col-sm-7 form-inline domi">
														<div class="form-group">
															<label for="exampleInputName2">Dominant Eye:</label>
															<select id="el_domi" name="el_domi" class="form-control minimal">
																<option value=""></option>
																<option value="OD" <?php echo ($el_domi == "OD") ? "selected" : "" ;?>>OD</option>
																<option value="OS" <?php echo ($el_domi == "OS") ? "selected" : "" ;?>>OS</option>
																<option value="OU" <?php echo ($el_domi == "OU") ? "selected" : "" ;?>>OU</option>
															</select>
														</div>
													</div>
												</div>
											</div>
											<div class="clearfix"></div>
											<div class="plr5">
												<div>
													<label>Refraction:</label>
													<textarea id="el_refraction" name="el_refraction" class="form-control" rows="1"><?php echo trim($el_refraction); ?></textarea>
												</div>
												<div class="clearfix"></div>
												<div>
													<label>Final K's</label>
													<div class="clearfix"></div>
													<div class="row">
														<div class="col-sm-3">
															<div class="input-group">
															  <div class="input-group-addon">Flat</div>
															  <input type="text" id="el_k_flat" name="el_k_flat" value="<?php echo $el_k_flat;?>" class="form-control">
															</div>
														</div>
														<div class="col-sm-3">
															<div class="input-group">
															  <div class="input-group-addon">Steep</div>
															  <input type="text" id="el_k_steep" name="el_k_steep" value="<?php echo $el_k_steep;?>" class="form-control">
															</div>
														</div>
														<div class="col-sm-3">
															<div class="input-group">
															  <div class="input-group-addon">Axis</div>
															  <input type="text" id="el_k_axis" name="el_k_axis" value="<?php echo $el_k_axis;?>" class="form-control">
															</div>
														</div>
														<div class="col-sm-3">
															<div class="input-group">
															  <div class="input-group-addon">Cyl</div>
															  <input type="text" id="el_k_cyl" name="el_k_cyl" value="<?php echo $el_k_cyl;?>" class="form-control">
															</div>
														</div>
													</div>
												</div>
												<div class="clearfix"></div>
											</div>	
										</div>
										
										<div class="col-sm-6 ">
											<div class="grnbg">
												<div class="sxhed">
													<h2><input type="checkbox" id="el_k_given_oct" name="el_k_given_oct" class="frcb" value="OCT" <?php echo ($el_k_given=="OCT") ? "CHECKED" : "" ;?>><label for="el_k_given_oct" class="frcb">IOL Master K's</label></h2>
												</div>
												<div class=" clearfix"></div>
												<div class="plr5">
													<div>
														<label>Other Eye Refraction:</label>
														<textarea id="el_othr_eye_ref"  name="el_othr_eye_ref" class="form-control" rows="1"><?php echo $el_othr_eye_ref;?></textarea>
													</div>
													<div class="clearfix"></div>
													<div>
														<label>Final K's</label>
														<div class="clearfix"></div>
														<div class="row">
															<div class="col-sm-3">
																<div class="input-group">
																  <div class="input-group-addon">Flat</div>
																  <input type="text" id="el_ok_flat" class="form-control" name="el_ok_flat" value="<?php echo $el_ok_flat;?>">
																</div>
															</div>
															<div class="col-sm-3">
																<div class="input-group">
																  <div class="input-group-addon">Steep</div><input type="text" id="el_ok_steep" class="form-control" name="el_ok_steep" value="<?php echo $el_ok_steep;?>">
																</div>
															</div>
															<div class="col-sm-3">
																<div class="input-group">
																  <div class="input-group-addon">Axis</div>
																  <input type="text" id="el_ok_axis" name="el_ok_axis" class="form-control" value="<?php echo $el_ok_axis;?>">
																</div>
															</div>
															<div class="col-sm-3">
																<div class="input-group">
																  <div class="input-group-addon">Cyl</div>
																  <input type="text" id="el_ok_cyl" name="el_ok_cyl" class="form-control" value="<?php echo $el_ok_cyl;?>">
																</div>
															</div>
														</div>
													</div>
													<div class="clearfix"></div>
												</div>
												<div class=" clearfix"></div>
											</div>
										</div>
									</div>
									
									<div class=" clearfix"></div>
										<div class="sxhed">
											<h2>Procedure</h2>
										</div>
									<div class=" clearfix"></div>
									<div class="plr5">
										<div class="row">
											<div class="col-sm-6">
												<label>Primary</label>
												<input type="text" id="el_proc_prim" name="el_proc_prim" value="<?php echo $el_proc_prim;?>" class="form-control">
											</div>
											<div class="col-sm-6">
												<label></label>
												Secondary
												<input type="text" id="el_proc_sec" name="el_proc_sec" value="<?php echo $el_proc_sec;?>" class="form-control">
											</div>
										</div>
									</div>
									<div class=" clearfix"></div>
								</div>	
							</div>	
							<div class="col-sm-3 ">
								<div class="sxdtbx sxboxhght">
									<!-- <div class="sxhed">
										<h2><input type="checkbox" id="el_k_given_iol" name="el_k_given_iol" class="frcb" value="IOL Master" <?php echo ($el_k_given=="IOL Master") ? "CHECKED" : "" ;?>><label for="el_k_given_iol" class="frcb">IOL Master K's</h2>
									</div> -->
									<!--post opevalution mapping block start here-->
									<?php if($pt_data[pt_primary_care_id]){?>
									<div class="sxhed grnbg">
										<div class="plr5 form-inline ">
										
										<div class="form-group">
											<label>Sch. Procedure: </label>
											<?php 
											$str_sch_proc="<option value=\"\">select</option>";
											$sqlRow = imw_query("SELECT sp1.id, sp1.proc, op.linked_op FROM slot_procedures sp1 LEFT JOIN slot_procedures_linked_op op ON sp1.id=op.proc_id WHERE sp1.times = '' AND sp1.proc != '' AND sp1.doctor_id = 0 AND sp1.active_status!='del' and sp1.source='' and op.ref_id='$pt_data[pt_primary_care_id]' ORDER BY sp1.proc")or die(imw_error());
											if($sqlRow && imw_num_rows($sqlRow) > 0){
												$selScProc="";
												$op_mapped="None";
												while($rowFet = imw_fetch_assoc($sqlRow))
												{
													if($po_proc_id==$rowFet['id']){
														$selScProc="selected";
														$op_mapped=$rowFet['linked_op'];
													}else{$selScProc="";}
													
													$str_sch_proc.="<option value=\"".$rowFet['id']."~".$rowFet['linked_op']."\" ".$selScProc." >".$rowFet['proc']."</option>";
												}
												echo "<select id=\"sch_proc\" name=\"sch_proc\" class=\"form-control minimal\" onChange=\"on_proc_change(this.value)\">".$str_sch_proc."</select>";
											}
											?>
										</div>
										<div id="po_eva_map">
											<label title="PO Evalution Mapping">PO Evalution: </label>
											<?php 
											//over write op_mapped with saved data
											if($po_proc_id || $po_eva_map)
											{
												$op_mapped=$po_eva_map;
											}
											$day=$week=$month=$saved_str='';
											if(strstr($op_mapped, 'Day'))$day='checked';
											if(strstr($op_mapped, 'Week'))$week='checked';
											if(strstr($op_mapped, 'Month'))$month='checked';
											echo '<div class="checkbox checkbox-inline">
											   <input type="checkbox" name="op_option[]" value="1 Day" id="1_Day" '.$day.'>
											   <label for="1_Day">1 Day</label>
											  </div>

											  <div class="checkbox checkbox-inline">
											   <input type="checkbox" name="op_option[]" value="1 Week" id="1_Week" '.$week.'>
											   <label for="1_Week">1 Week</label>
											  </div>

											  <div class="checkbox checkbox-inline">
											   <input type="checkbox" name="op_option[]" value="1 Month" id="1_Month" '.$month.'>
											   <label for="1_Month">1 Month Post Op</label>
											  </div>';
											?>
											
										</div>
										
									</div>
									</div><? }?>
									<!--post opevalution mapping block end here-->
									<div class="plr5">
										<label>Recommendations:</label>
										<select id="el_iol_recomd" name="el_iol_recomd"  class="form-control minimal">
											<option value="">select</option>
											<?php
											$el_iol_recomd_pdf = '';
												if(count($ar_admn_iol_recomd)>0){
													foreach($ar_admn_iol_recomd as $key => $val){
														if(!empty($val)){
															$sel = ($el_iol_recomd == $val) ? "SELECTED" : "";
															if($el_iol_recomd == $val){
																$el_iol_recomd_pdf = $val;
															}
															echo "<option value=\"".$val."\" ".$sel." >".$val."</option>";
														}
													}
												}
											?>					
										</select>	
										<label>Comments </label>
										<textarea id="el_iol_desc" name="el_iol_desc" class="form-control" rows="2" placeholder="Comments"><?php echo $el_iol_desc;?></textarea>
										<label>Lens – as of SLE Summary</label>
										<textarea id="el_lens_sle_summary" name="el_lens_sle_summary" class="form-control" rows="2" placeholder="SLE Summary"><?php echo $el_lens_sle_summary;?></textarea>
									</div>
								</div>
							</div>
						</div>
	<?php
		if($el_date_surgery != '' && $el_date_surgery != '00-00-0000'){$pdf_el_date_surgery = $el_date_surgery;}else{$pdf_el_date_surgery = '';}	
	?>	
						<div class=" clearfix"></div>
						<div class="sxdtbx">
							<div class="sxhed">
								<h2>Planning</h2>
							</div>
							<div class=" clearfix"></div>
							<div class="plr5">
								<!-- Previous Eye Values -->
								<div class="row readPrevEye pdb5 pt5">
									<div class="col-sm-10">
										<div class="row">
											<div class="col-sm-4">
												<div class="row">
													<div class="col-sm-6">
														<label id="link_prev_eye" class=""><strong>Previous Eye</strong></label>
														<input type="text" class="form-control" value="<?php echo $prevEyeArr['el_prev_eye_site'];?>" readonly>
														
														<!-- <select id="el_prev_eye_site" name="el_prev_eye_site" class="form-control minimal">
															<option value=""></option>
															<option value="OD" >OD</option>
															<option value="OS" <?php echo ($prevEyeArr['el_prev_eye_site'] == "OS") ? "selected" : "" ;?>>OS</option>
														</select> -->
													</div>
													<div class="col-sm-6">
														<label>Date</label>
														<div class="input-group">
															<input type="text" value="<?php echo $prevEyeArr['el_prev_eye_date'];?>" class="form-control" readonly>
															<label for="" class="input-group-addon">
																<span class="glyphicon glyphicon-calendar"></span>	
															</label>	
														</div>
													</div>
												</div>
											</div>
											
											<div class="col-sm-4">
												<div class="row">
													<div class="col-sm-3">
														<label>Lenses</label>
														<input type="text" value="<?php echo $prevEyeArr['el_prev_eye_lens'];?>" class="form-control" readonly>
													</div>
													<div class="col-sm-3">
														<label>Power</label>
														<input type="text" value="<?php echo $prevEyeArr['el_prev_eye_power'];?>" class="form-control" readonly>
													</div>
													<div class="col-sm-3">
														<label>Cyl </label>
														<input type="text" value="<?php echo $prevEyeArr['el_prev_eye_cyl'];?>" class="form-control" readonly>
													</div>
													<div class="col-sm-3">
														<label>Axis </label>
														<input type="text" value="<?php echo $prevEyeArr['el_prev_eye_axis'];?>" class="form-control" readonly>
													</div>
												</div>
											</div>
											
											<div class="col-sm-4">
												<div class="row">
													<div class="col-sm-3">
														<label>VA </label>
														<div class="input-group">
															<input type="text" value="<?php echo $prevEyeArr['el_prev_eye_va'];?>" class="form-control" readonly>
															<!-- <div class="input-group-btn">
																<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"> 
																	<span class="caret"></span>
																</button>
																<ul class="dropdown-menu">
																<?php 
																	$arrData = array_keys($sx_obj->arrAcuitiesMrDis);
																	
																	foreach($arrData as $val){
																		echo '<li class="dropdown-item" onclick="setVSVal(this);"><a href="javascript:void(0)" >'.$val.'</a></li>';
																	}
																	//echo get_simple_menu($sx_obj->arrAcuitiesMrDis,"menu_acuitiesMrDis","el_prev_eye_va");
																?>
																
																</ul>
															</div> -->
														</div>
													</div>
													<div class="col-sm-3">
														<label>ORA Res. </label>
														<input type="text" value="<?php echo $prevEyeArr['el_prev_eye_ora_res'];?>" class="form-control" readonly>
													</div>
													<div class="col-sm-3">
														<label>Toric Pos. </label>
														<input type="text" value="<?php echo $prevEyeArr['el_prev_eye_torpos'];?>" class="form-control" readonly>
													</div>
													<div class="col-sm-3">
														<label>Lens SX  </label>
														<input type="text" value="<?php echo $prevEyeArr['el_meth_lens'];?>" class="form-control" readonly>
													</div>
												</div>
											</div>
											
											<div class="col-sm-4">
												<div class="row">
													<div class="col-sm-3">
														<label>ORA  </label>
														<input type="text" value="<?php echo $prevEyeArr['el_ora'];?>" class="form-control" readonly>
													</div>
													<div class="col-sm-3">
														<label>Version </label>
														<input type="text" value="<?php echo $prevEyeArr['el_version'];?>" class="form-control" readonly>
													</div>
													<div class="col-sm-3">
														<label>MBN</label>
														<input type="text" value="<?php echo $prevEyeArr['el_version']; ?>" class="form-control" readonly>
														<!-- <select id="el_mbn" name="el_mbn" class="form-control minimal">
															<option value=""></option>
															<?php
																$MBN_pdf = '';
																$premium_lens_pdf = '';
																	if(count($ar_admn_mbn)>0){
																		foreach($ar_admn_mbn as $key => $val){
																			if(!empty($val)){
																				if($el_mbn == $val)
																				{
																					$MBN_pdf = $val;
																				}
																				$sel = ($el_mbn == $val) ? "SELECTED" : "";
																				echo "<option value=\"".$val."\" ".$sel." >".$val."</option>";
																			}
																		}
																	}
																	if($el_prem_lens == "1")
																	{
																		$premium_lens_pdf = 'Yes';
																	}else{
																		$premium_lens_pdf = 'No';
																	}
															?>				
														</select> -->
													</div>
													<div class="col-sm-3">
														<label>CCI </label>
														<input type="text" value="<?php echo $prevEyeArr['el_cci'];?>" class="form-control" readonly> 
													</div>
												</div>
											</div>									
											<div class="col-sm-4">
												<div class="row">
													<div class="col-sm-6">
														<label>Pachymetry  </label>
														<input type="text" value="<?php echo $prevEyeArr['el_pachy'];?>" class="form-control" readonly>
													</div>
													<div class="col-sm-6">
														<label>White to White  </label>
														<input type="text" value="<?php echo $prevEyeArr['el_w2w'];?>" class="form-control" readonly>
													</div>
												</div>
											</div>
											
											<div class="col-sm-4">
												<div class="row">
													<div class="col-sm-8">
														<div class="row">
															<div class="col-sm-6">
																<label>Pupil Max </label>
																<input type="text" value="<?php echo $prevEyeArr['el_pupilmx'];?>" class="form-control" readonly>
															</div>
															<div class="col-sm-6">
																<label>Pupil Dilated</label>
																<input type="text" value="<?php echo $prevEyeArr['el_pupildilated'];?>" class="form-control" readonly>
															</div>
														</div>
													</div>
													<div class="col-sm-4">
														<label>Cap Max  </label>
														<input type="text" value="<?php echo $prevEyeArr['el_cupmx'];?>" class="form-control" readonly>
													</div>
												</div>
											</div>
										</div>
									</div>
									
									<div class="col-sm-2">
										<div class="row">
											<div class="col-sm-12">
												<label>Comments</label>
												<textarea rows="1" class="form-control" placeholder="Comments" readonly><?php echo $prevEyeArr['el_prev_eye_comm'];?></textarea>	
											</div>	
											<div class="col-sm-12">
												<div class="row">
													<div class="col-sm-12">
														<label>Premium Lens</label>
														<?php 
															$labelval = ($prevEyeArr['el_prem_lens'] == 1) ? 'Yes' : 'No';
														?>
														<input type="text" class="form-control" value="<?php echo $labelval; ?>" readonly>
													</div>
												</div>
											</div>
										</div>	
									</div>
								</div>
								<!-- Previous Eye Values End -->
								
								<div class="row pt10">
									<div class="col-sm-10">
										<div class="row">
											<?php /* ?>
											<div class="col-sm-4">
												<div class="row">
													<div class="col-sm-6">
														<!-- <label id="link_prev_eye" class="text_purple pointer" data-toggle="modal" data-target="#div_sx_pop_up">Previous Eye</label> -->
														<label id="link_prev_eye" class="" >Eye</label>
														<select id="el_prev_eye_site" name="el_prev_eye_site" class="form-control minimal">
															<option value=""></option>
															<option value="OD" <?php echo ($el_prev_eye_site == "OD") ? "selected" : "" ;?>>OD</option>
															<option value="OS" <?php echo ($el_prev_eye_site == "OS") ? "selected" : "" ;?>>OS</option>
														</select>
													</div>
													<div class="col-sm-6">
														<label>Date</label>
														<div class="input-group">
															<input type="text" id="el_prev_eye_date" name="el_prev_eye_date" value="<?php echo $el_prev_eye_date;?>" class="datepicker form-control">
															<label for="el_prev_eye_date" class="input-group-addon pointer">
																<span class="glyphicon glyphicon-calendar"></span>	
															</label>	
														</div>
													</div>
												</div>
											</div>
											
											<div class="col-sm-4">
												<div class="row">
													<div class="col-sm-3">
														<label>Lenses</label>
														<input type="text" id="el_prev_eye_lens" name="el_prev_eye_lens" value="<?php echo $el_prev_eye_lens;?>" class="form-control">
													</div>
													<div class="col-sm-3">
														<label>Power</label>
														<input type="text" id="el_prev_eye_power" name="el_prev_eye_power" value="<?php echo $el_prev_eye_power;?>" class="form-control">
													</div>
													<div class="col-sm-3">
														<label>Cyl </label>
														<input type="text" id="el_prev_eye_cyl" name="el_prev_eye_cyl" value="<?php echo $el_prev_eye_cyl;?>" class="form-control">
													</div>
													<div class="col-sm-3">
														<label>Axis </label>
														<input type="text" id="el_prev_eye_axis" name="el_prev_eye_axis" value="<?php echo $el_prev_eye_axis;?>" class="form-control">
													</div>
												</div>
											</div>
											<?php */ ?>
											<div class="col-sm-12">
												<div class="row">
													<div class="col-sm-3">
														<label>VA </label>
														<div class="input-group">
															<input type="text" id="el_prev_eye_va" name="el_prev_eye_va" value="<?php echo $el_prev_eye_va;?>" class="form-control">
															<div class="input-group-btn">
																<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"> 
																	<span class="caret"></span>
																</button>
																<ul class="dropdown-menu">
																<?php 
																	$arrData = array_keys($sx_obj->arrAcuitiesMrDis);
																	
																	foreach($arrData as $val){
																		echo '<li class="dropdown-item" onclick="setVSVal(this);"><a href="javascript:void(0)" >'.$val.'</a></li>';
																	}
																	//echo get_simple_menu($sx_obj->arrAcuitiesMrDis,"menu_acuitiesMrDis","el_prev_eye_va");
																?>
																
																</ul>
															</div>
														</div>
													</div>
													<div class="col-sm-3">
														<label>ORA Res. </label>
														<input type="text" id="el_prev_eye_ora_res" name="el_prev_eye_ora_res" value="<?php echo $el_prev_eye_ora_res;?>" class="form-control">
													</div>
													<div class="col-sm-3">
														<label>Toric Pos. </label>
														<input type="text" id="el_prev_eye_torpos" name="el_prev_eye_torpos" value="<?php echo $el_prev_eye_torpos;?>" class="form-control">
													</div>
													<div class="col-sm-3">
														<label>Lens SX  </label>
														<input type="text" id="el_meth_lens" name="el_meth_lens" value="<?php echo $el_meth_lens;?>" class="form-control">
													</div>
												</div>
											</div>
											
											<div class="col-sm-4">
												<div class="row">
													<div class="col-sm-3">
														<label>ORA  </label>
														<input type="text" id="el_ora" name="el_ora" value="<?php echo $el_ora;?>" class="form-control">
													</div>
													<div class="col-sm-3">
														<label>Version </label>
														<input type="text" id="el_version" name="el_version" value="<?php echo $el_version;?>" class="form-control">
													</div>
													<div class="col-sm-3">
														<label>MBN</label>
														<select id="el_mbn" name="el_mbn" class="form-control minimal">
															<option value=""></option>
															<?php
																$MBN_pdf = '';
																$premium_lens_pdf = '';
																	if(count($ar_admn_mbn)>0){
																		foreach($ar_admn_mbn as $key => $val){
																			if(!empty($val)){
																				if($el_mbn == $val)
																				{
																					$MBN_pdf = $val;
																				}
																				$sel = ($el_mbn == $val) ? "SELECTED" : "";
																				echo "<option value=\"".$val."\" ".$sel." >".$val."</option>";
																			}
																		}
																	}
																	if($el_prem_lens == "1")
																	{
																		$premium_lens_pdf = 'Yes';
																	}else{
																		$premium_lens_pdf = 'No';
																	}
															?>				
														</select>
													</div>
													<div class="col-sm-3">
														<label>CCI </label>
														<input type="text" id="el_cci" name="el_cci" value="<?php echo $el_cci;?>" class="form-control"> 
													</div>
												</div>
											</div>									
											<div class="col-sm-4">
												<div class="row">
													<div class="col-sm-6">
														<label>Pachymetry  </label>
														<input type="text" id="el_pachy" name="el_pachy" value="<?php echo $el_pachy;?>" class="form-control">
													</div>
													<div class="col-sm-6">
														<label>White to White  </label>
														<input type="text" id="el_w2w" name="el_w2w" value="<?php echo $el_w2w;?>" class="form-control">
													</div>
												</div>
											</div>
											
											<div class="col-sm-4">
												<div class="row">
													<div class="col-sm-8">
														<div class="row">
															<div class="col-sm-6">
																<label>Pupil Max </label>
																<input type="text" id="el_pupilmx" name="el_pupilmx" value="<?php echo $el_pupilmx;?>" class="form-control">
															</div>
															<div class="col-sm-6">
																<label>Pupil Dilated </label>
																<input type="text" id="el_pupildilated" name="el_pupildilated" value="<?php echo $el_pupildilated;?>" class="form-control">
															</div>
														</div>
													</div>
													<div class="col-sm-4">
														<label>Cap Max  </label>
														<input type="text" id="el_cupmx" name="el_cupmx" value="<?php echo $el_cupmx;?>" class="form-control">
													</div>
												</div>
											</div>
										</div>
									</div>
									
									<div class="col-sm-2">
										<div class="row">
											<div class="col-sm-12">
												<label>Comments</label>
												<textarea id="el_prev_eye_comm" name="el_prev_eye_comm" rows="1" class="form-control" placeholder="Comments"><?php echo $el_prev_eye_comm;?></textarea>	
											</div>	
											<div class="col-sm-12">
												<div class="row">
													<div class="col-sm-12">
														<label>Premium Lens</label>
													</div>
													<div class="col-sm-12">
														<div class="radio radio-inline">
															<input type="radio" id="el_prem_lens_1" name="el_prem_lens" value="1" <?php echo ($el_prem_lens == "1") ? "checked" : "" ;?>>
															<label for="el_prem_lens_1">Yes</label>	
														</div>	
														<div class="radio radio-inline">
															<input type="radio" id="el_prem_lens_0" name="el_prem_lens" value="0" <?php echo ($el_prem_lens == "0") ? "checked" : "" ;?>>
															<label for="el_prem_lens_0">No</label>	
														</div>
													</div>
												</div>
											</div>
										</div>	
									</div>
								</div>
							</div>		
						</div>
						<div class="sxdtbx">
							<div class="table-responsive">
								<table id="tbl_lens" class="table">
									<thead>
										<tr>
											<td class="bluhds">Lens</td>
											<td colspan="9"><strong>PREPLAN LENS (TRADITIONAL IOL) DIFFERS FROM PRIMARY LENS</strong></td>
										</tr>
										<tr>
											<td class="blutab">&nbsp;</td>
											<td><strong>POWER </strong></td>
											<td><strong>CYL</strong></td>
											<td><strong>AXIS</strong></td>
											<td><strong>USED</strong></td>
											<td><strong>TARGET </strong></td>
											<td style="width:250px">
												<div class="row">
													<div class="col-sm-4">
														<strong>PREDICTED </strong>
													</div>	
													<div class="col-sm-8">
														<select class="selectpicker" id="predict_sel_val" name="predict_sel_val[]" data-width="100%" data-title="Select" multiple>
															<option value="Barret"  <?php echo (strpos($el_predict_sel, "Barret")!==false) ? "selected" : "" ;?>>Barret</option>
															<option value="SRK-T /HQ"  <?php echo (strpos($el_predict_sel, "SRK-T /HQ")!==false) ? "selected" : "" ;?>>SRK-T /HQ</option>
															<option value="Holladay- I / II"  <?php echo (strpos($el_predict_sel, "Holladay- I / II")!==false) ? "selected" : "" ;?>>Holladay- I / II</option>
														</select>
													</div>	
												</div>
												
											</td>
											<td><strong>ACD/AL(%) </strong></td>	
											<td><strong>S/P CRS </strong></td>	
										</tr>
									</thead>
									<tbody>
									<?php 
										/*
										$selProvId = $sx_obj->getIOLLens($el_surgeon_id);
										$lensArr = array();
										if(count($selProvId) > 0 && is_array($selProvId)){
											$lensArr = $sx_obj->getLensType($id_chart_sx_plan_sheet, $selProvId);
										}
										
										$provIDArr = array();
										foreach($lensArr as &$lensVal){
											if(isset($lensVal['ID']) && empty($lensVal['ID']) == false){
												$mystring = $lensVal['ID'];
												$findme   = '~~~~';
												$pos = strpos($mystring, $findme);
												
												if($pos === true){
													$explodeData = explode($findme, $mystring);
													$provIDArr[] = (isset($explodeData[1]) && empty($explodeData[1]) == false) ? $explodeData[1] : '';
												}else{
													$provIDArr[] = $lensVal['ID'];
												}
											}
										}
										
										$rowCounter = 1;
										if(count($lensArr) > 0){
											//foreach($lensArr as $lensNm => &$lensVal){
											for($i = 0; $i < 4; $i++){
												$lensVal = &$lensArr[$i];
												
												$css_tr_bgc = (isset($lensVal['Used']) && empty($lensVal['Used']) == false) ? " class=\"hylight\" " : "";
												$counter = 1;
												
												//Get Fields Values
												$staticInpFields = $pdfFields = '';
												
												$fieldDbID = '';		
												if($lensVal === NULL) {
													$lensVal = $sx_obj->lensFields;
													$lenId = '';
												}else{
													if(isset($lensVal['ID']) && empty($lensVal['ID']) == false){
														$mystring = $lensVal['ID'];
														$findme   = '~~~~';
														$pos = strpos($mystring, $findme);
														
														if($pos === true){
															$explodeData = explode($findme, $mystring);
															$lenId = (isset($explodeData[1]) && empty($explodeData[1]) == false) ? $explodeData[1] : '';
															$fieldDbID = (isset($explodeData[0]) && empty($explodeData[0]) == false) ? $explodeData[0] : '';
														}else{
															$lenId = (isset($lensVal['ID']) && empty($lensVal['ID']) == false) ? $lensVal['ID'] : '';
														}
													}
												}
												unset($lensVal['ID']);
												foreach($lensVal as $key => &$val){
													$fieldName = '';
													$bgClass = ($counter == 1) ? 'blutab' : '';
													
													//For PDF
													if(empty($css_tr_bgc) == false){
														$pdf_used_val = 'Yes';
														$pdf_used_bg = ' hylight';
													}else{
														$pdf_used_val = '';
														$pdf_used_bg = '';
													}
													
													//If want to show border on every side of the column use this code
													$pdf_arr_lens_classname = $pdf_border_class;
													
													//PDF Borders
													$brdrFirst = ($counter == 1) ? 'bdrlft' : '';
													$brdrlst = ($counter == count($val)) ? 'bdrRght' : '';	
													
													$fieldName = 'EL_IOL_'.$key.'_'.$rowCounter;
												
													//Master IOL lens Array
													$IOLArr = $sx_obj->iol_lenses;
													
													if($key == 'Used'){
														$chkSelected = (empty($css_tr_bgc) == false) ? 'checked' : '';
														$staticInpFields .= '
														<td class="'.$bgClass.'">
															<div class="checkbox">
																<input class="usedChkBox" type="checkbox" id="'.$fieldName.'" name="'.$fieldName.'" value="'.$val.'" autocomplete="off" '.$chkSelected.'>
																<label for="'.$fieldName.'"></label>	
															</div>
														</td>';
														
														$pdfFields .= '<td class="pd pl5 '.$pdf_arr_lens_classname.$pdf_used_bg.$brdrFirst.$brdrlst.'">'.$pdf_used_val.'</td>';
													}elseif($key == 'Type'){
														$lensValues = '';
														if(empty($lenId) == false){
															$lensValues = $IOLArr[$lenId]['lensType'];
															
															$strProvOpt = '';
															if(empty($strProvOpt)) $strProvOpt .= '<option value="">Please select</option>';
															foreach($provIDArr as $selId){
																$selected = ($selId == $lenId) ? 'selected' : '';
																$strProvOpt .= '<option value="'.$selId.'" '.$selected.'>'.$sx_obj->iol_lenses[$selId]['lensType'].'</option>';
															}
															
															$staticInpFields .= '
															<td class="'.$bgClass.'">
																<input type="hidden" name="'.$fieldName.'_hidden" value="'.$fieldDbID.'">
																<select id="'.$fieldName.'" name="'.$fieldName.'" class="form-control minimal iolNewAdd" data-value="" onChange="addthisOpt(this);">'.$strProvOpt.'</select>
																<!-- <input type="text" id="'.$fieldName.'" name="'.$fieldName.'" value="'.$lensValues.'" class="form-control iolNewAdd" data-value="'.$lenId.'" disabled> -->
															</td>';
															$pdfFields .= '<td class="pd pl5 '.$pdf_arr_lens_classname.$brdrFirst.$brdrlst.'">'.$lensValues.'</td>';
														}else{
															$strOpt = '';
															foreach($sx_obj->iol_lenses as $IOLkey => &$IOLobj){
																if(empty($strOpt)) $strOpt .= '<option value="">Please select</option>';
																$strOpt .= '<option value="'.$IOLkey.'">'.$IOLobj['lensType'].'</option>';
															}
															
															$staticInpFields .= '
															<td class="'.$bgClass.'">
																<select class="form-control minimal iolNewAdd" name="'.$fieldName.'" id="'.$fieldName.'" onChange="addthisOpt(this);" data-value="">
																	'.$strOpt.'
																</select>
															</td>';
															$pdfFields .= '<td class="pd pl5 '.$pdf_arr_lens_classname.$brdrFirst.$brdrlst.'">'.$lensValues.'</td>';
														}
														
														
													}else{
														$pdfFields .= '<td class="pd pl5 '.$pdf_arr_lens_classname.$brdrFirst.$brdrlst.'">'.$val.'</td>';
														$staticInpFields .= '
														<td class="'.$bgClass.'"><input type="text" id="'.$fieldName.'" name="'.$fieldName.'" value="'.$val.'" class="form-control"></td>';
													}
													$counter++;
												}
												
												$staticLensFields .= '<tr data-value="'.$lenId.'" class="'.$className.'">'.$staticInpFields.'</tr>';
												$pdf_arra_lens .= '<tr>'.$pdfFields.'</tr>';
												$rowCounter++;
											}
										}
										echo $staticLensFields;*/
									?>
									</tbody>
								</table>	
							</div>
						</div>
						
						<div class="clearfix"></div>
						<div class="row">
							<?php 
								//Toric Buttons
								if(count($ar_admn_toric_btn) > 0){
									$btn_toric_str = '
									<div class="plr5">
										<div class="row pt10">
											<div class="col-sm-12">';
									foreach($ar_admn_toric_btn as $btn_lbl => $btn_url){
										if(!empty($btn_lbl) && !empty($btn_url)){	
											$str_btn_lbl = str_replace(" ","",$btn_lbl);
											$tmp_toric_btn = "el_toric_btn_".$str_btn_lbl;
											$btn_toric_str .="<input type=\"button\" class=\"btn btn-info\" name=\"".$tmp_toric_btn."\" value=\"".$btn_lbl."\" onclick=\"openToricWin('".$btn_url."')\">&nbsp;";
										}
									}
									$btn_toric_str .= '
											</div>
										</div>
									</div>';
									
									echo '<div class="col-sm-12">
										<div class="sxdtbx">
											<div class="sxhed"><h2>Lens Calculators</h2></div>
											'.$btn_toric_str.'	
										</div></div>';
								}
							?>
							<div class="col-sm-7">
								<div class="sxdtbx astig_assess">
									<div class="sxhed"><h2>Astigmatism Assessment</h2></div>
									<div class="table-responsive">
										<table class="table table-striped">
											<tr class="tptabhead">
												<td><strong>Astigmatism Source</strong></td>
												<td><strong>Magnitude (Diopters) </strong></td>
												<td align="center"><strong>Magnitude Used </strong></td>
												<td><strong>Axis (Degrees)</strong></td>
												<td align="center"><strong>Axis Used </strong></td>
											</tr>
											<?php 
												$pdf_arr_asti_as = '';
												$pdf_arr_asti_as_count = 1;
												foreach($sx_obj->arr_asti_as as $k => $asti_source){
													$asti_source_var=str_replace(" ","", $asti_source);		
													$magni = "el_magni".$asti_source_var;
													$magni_used = "el_magni_used".$asti_source_var;
													$axis = "el_axis".$asti_source_var;
													$axis_used = "el_axis_used".$asti_source_var;
													
													if(!empty($id_chart_sx_plan_sheet)){
														$sql = "SELECT * FROM chart_sps_ast_assess where id_chart_sx_plan_sheet='".$id_chart_sx_plan_sheet."'  AND ast_source='".imw_real_escape_string($asti_source)."' ";
														$row =  sqlQuery($sql);
														if($row!=false){
															$$magni = $row["magni_diopter"];
															$$magni_used = $row["magni_used"];
															$$axis = $row["axis"];
															$$axis_used = $row["axis_used"];
														}
													}
													?>
													<tr>
														<td><?php echo $asti_source?></td>
														<td><input type="text" class="form-control" id="<?php echo $magni;?>" name="<?php echo $magni;?>" value="<?php echo $$magni;?>"></td>
														<?php 
															if($asti_source=="Coma Max (u)" || $asti_source=="CCT (u)" || $asti_source=="OCTM FT (u)"){
																if($asti_source=="Coma Max (u)"){
																	echo "<td align=\"center\" colspan=\"3\" rowspan=\"3\">
																			<textarea id=\"el_asti_com\" name=\"el_asti_com\" rows=\"4\" class=\"form-control\" placeholder=\"Comments\">".$el_asti_com."</textarea>
																		</td>";
																}
															}else{
														?>
														<td align="center">
															<div class="checkbox">
																<input type="checkbox" id="<?php echo $magni_used;?>" name="<?php echo $magni_used;?>" value="1" <?php echo (!empty($$magni_used)) ? "CHECKED" : "";?>>
																<label for="<?php echo $magni_used;?>"></label>	
															</div>
														</td>
														<td><input type="text" id="<?php echo $axis;?>" name="<?php echo $axis;?>" value="<?php echo $$axis;?>" class="form-control"></td>
														<td align="center">
															<div class="checkbox">
																<input type="checkbox" id="<?php echo $axis_used;?>" name="<?php echo $axis_used;?>" value="1" <?php echo (!empty($$axis_used)) ? "CHECKED"  : "";?> >
																<label for="<?php echo $axis_used;?>"></label>	
															</div>
														</td>
														<?php } ?>
													</tr>
													<?php
												}
											?>
										</table>
									</div>		
								</div>
							</div>

							<div class="col-sm-5">
								<div class="sxdtbx">
									<div class="sxhed"><h2>Astigmatism Plan</h2></div>
									<div class="table-responsive">
										<table class="table astig_plan">
											<tr>
												<td>Femto</td>
												<td colspan="3">
													<input type="text" id="el_plan_femto" name="el_plan_femto" class="form-control" value="<?php echo $el_plan_femto;?>" >
												</td>
											</tr>
											<tr>
												<td>AK#</td>
												<td>
													<div class="radio radio-inline">
														<input type="radio" id="el_plan_ak_2" name="el_plan_ak" value="2" <?php echo ($el_plan_ak == 2) ? "checked" : "" ; ?> >
														<label for="el_plan_ak_2">2</label>
													</div>
													<div class="radio radio-inline">
														<input type="radio" id="el_plan_ak_1" name="el_plan_ak" value="1" <?php echo ($el_plan_ak == 1) ? "checked" : "" ; ?> >
														<label for="el_plan_ak_1">1</label>
													</div>
													<div class="radio radio-inline">
														<input type="radio" id="el_plan_ak_0" name="el_plan_ak" value="0" <?php echo ($el_plan_ak == 0) ? "checked" : "" ; ?> >
														<label for="el_plan_ak_0">0</label>
													</div>
												</td>
												<td >
													<div class="checkbox">
														<input type="checkbox" id="el_plan_anterior" name="el_plan_anterior" value="Anterior" <?php echo !empty($el_plan_anterior) ? "checked" : "" ;?>>
														<label for="el_plan_anterior">Anterior</label>	
													</div>
												 </td>
												<td >
													<div class="checkbox">
														<input type="checkbox" id="el_plan_insratromal" name="el_plan_insratromal" value="Insratromal" <?php echo !empty($el_plan_insratromal) ? "checked" : "" ;?>>
														<label for="el_plan_insratromal">Intrastromal</label>	
													</div>
												</td>
											</tr>	
											
											<tr>
												<td >AK# 1 Length</td>
												<td ><input type="text" id="el_plan_ak1_len" name="el_plan_ak1_len" value="<?php echo $el_plan_ak1_len;?>" class="form-control"></td>
												<td >AK# 2 Length</td>
												<td ><input type="text" id="el_plan_ak2_len" name="el_plan_ak2_len" value="<?php echo $el_plan_ak2_len;?>" class="form-control"></td>
											</tr>

											<tr>
												<td >AK# 1 Axis(&deg;)</td>
												<td ><input type="text" id="el_plan_ak1_axis" name="el_plan_ak1_axis" value="<?php echo $el_plan_ak1_axis;?>" class="form-control"></td>
												<td >Arc 2 Angle(&deg;)</td>
												<td ><input type="text" id="el_plan_arc2_axis" name="el_plan_arc2_axis" value="<?php echo $el_plan_arc2_axis;?>" class="form-control"></td>
											</tr>

											<tr>
												<td >AK# 1 Depth(%)</td>
												<td ><input type="text" id="el_plan_ak1_depth" name="el_plan_ak1_depth" value="<?php echo $el_plan_ak1_depth;?>" class="form-control"></td>
												<td >Arc 2 Depth(%)</td>
												<td ><input type="text" id="el_plan_ak2_depth" name="el_plan_ak2_depth" value="<?php echo $el_plan_ak2_depth;?>" class="form-control"></td>
											</tr>

											<tr>
												<td >Optical Zone</td>
												<td ><input type="text" id="el_plan_opt_zone" name="el_plan_opt_zone" value="<?php echo $el_plan_opt_zone;?>" class="form-control"></td>
												<td >Incision Axis</td>
												<td><input type="text" id="el_plan_incision_axis" name="el_plan_incision_axis" value="<?php echo $el_plan_incision_axis;?>" class="form-control"></td>
											</tr>	
										</table>
										
										<div class="toric_wrapper">
											<table id="tbl_toric" align="center" cellpadding="0" cellspacing="0" class="table assplntabl">
												<thead>
													<tr>
														<td>Type</td>
														<td>Power</td>
														<td>Cyl</td>
														<td>Axis
														<span class="pull-right">
															<label class="switch"><input type="checkbox" name="iol_lock" id="iol_lock" value="1" <?php echo $iol_lock?'checked':''?> <?php echo (($iol_lock&& $_SESSION['authId']==$el_surgeon_id)||(!$iol_lock))?'':'disabled';?> /><span class="slider round"></span></label>
														</span>
														</td>
													</tr>
												</thead>
												<tbody></tbody>
												<?php
													/*
													$pdf_chart_sx_plan_sheet = '';
													if(!empty($id_chart_sx_plan_sheet)){
														foreach($sx_obj->arr_lens as $key => &$val){
															$sql = imw_query("SELECT * FROM chart_sps_ast_plan_tpa where id_chart_sx_plan_sheet='".$id_chart_sx_plan_sheet."' AND indx = '".$key."'");
															if($sql && imw_num_rows($sql) > 0){
																$row = imw_fetch_assoc($sql);
																//$fun = ($i==1) ?  "addToric()" : " delToric('".$i."') " ;
																//$fun_s = ($i==1) ? "plu.png" : "min.png";
																
																$el_toric=$row["toric_model"];
																$el_power=$row["power"];
																$el_axis=$row["axis"];
																
																$iolDropDown = '';
																$iolDropOpt = '';
																if(count($provIDArr) > 0){
																	if(empty($iolDropOpt)) $iolDropOpt .= '<option value="">Please select</option>';
																	foreach($provIDArr as $selId){
																		$iolDropOpt .= '<option value="'.$selId.'">'.$sx_obj->iol_lenses[$selId]['lensType'].'</option>';
																	}
																	$function = '';
																	if(strtolower($val) == 'primary') $function = "setThisPlan(this.value);";
																	$iolDropDown = '<div class="row"><div class="col-sm-4">'.$val.'</div><div class="col-sm-8"><select name="iolPlan" class="form-control minimal" id="iolPlanDrop" onChange="'.$function.'">'.$iolDropOpt.'<select></div></div>';
																}
																
																echo"
																<tr>
																	<td>
																		".$iolDropDown."
																	</td>
																	<td>
																		<input type=\"text\" id=\"el_toric".$key."\" name=\"el_toric".$key."\" value=\"".$el_toric."\" class=\"form-control\">
																	</td>
																	<td>
																		<input type=\"text\" id=\"el_power".$key."\" name=\"el_power".$key."\" value=\"".$el_power."\" class=\"form-control\">
																	</td>
																	<td>
																		<input type=\"text\" id=\"el_axis".$key."\" name=\"el_axis".$key."\" value=\"".$el_axis."\" class=\"form-control\">
																	</td>
																	<!-- <td onclick=\"".$fun."\"><img src=\"".$library_path."/images/".$fun_s."\" /></td> -->
																</tr>
																";
																$pdf_chart_sx_plan_sheet .='<tr>
																	<td class="bdrlft pd '.$pdf_border_class.'">'.$el_toric.'</td>
																	<td class="'.$pdf_border_class.' pd">'.$el_power.'</td>
																	<td class="'.$pdf_border_class.' pd">'.$el_axis.'</td>
																	<td class="'.$pdf_border_class.' pd bdrRght"></td>
																</tr>';
															}else{
																$iolDropDown = '';
																$iolDropOpt = '';
																
																if(count($provIDArr) > 0){
																	if(empty($iolDropOpt)) $iolDropOpt .= '<option value="">Please select</option>';
																	foreach($provIDArr as $selId){
																		$iolDropOpt .= '<option value="'.$selId.'">'.$sx_obj->iol_lenses[$selId]['lensType'].'</option>';
																	}
																	
																	$function = '';
																	if(strtolower($val) == 'primary') $function = "setThisPlan(this.value);";
																	
																	$iolDropDown = '<div class="row"><div class="col-sm-4">'.$val.'</div><div class="col-sm-8"><select name="iolPlan" class="form-control minimal" id="iolPlanDrop" onChange="'.$function.'">'.$iolDropOpt.'<select></div></div>';
																}
																
																echo"
																<tr>
																	<td>
																		".$iolDropDown."
																	</td>
																	<td>
																		<input type=\"text\" id=\"el_toric".$key."\" name=\"el_toric".$key."\" value=\"\" class=\"form-control\">
																	</td>
																	<td>
																		<input type=\"text\" id=\"el_power".$key."\" name=\"el_power".$key."\" value=\"\" class=\"form-control\">
																	</td>
																	<td>
																		<input type=\"text\" id=\"el_axis".$key."\" name=\"el_axis".$key."\" value=\"\" class=\"form-control\">
																	</td>
																	<!-- <td onclick=\"".$fun."\"><img src=\"".$library_path."/images/".$fun_s."\" /></td> -->
																</tr>
																";
															}
														}
													}else{
														foreach($sx_obj->arr_lens as $key => &$val){
															$iolDropDown = '';
															$iolDropOpt = '';
															
															if(count($provIDArr) > 0){
																if(empty($iolDropOpt)) $iolDropOpt .= '<option value="">Please select</option>';
																foreach($provIDArr as $selId){
																	$iolDropOpt .= '<option value="'.$selId.'">'.$sx_obj->iol_lenses[$selId]['lensType'].'</option>';
																}
															}else{
																foreach($sx_obj->iol_lenses as $k => $v){
																	if(empty($iolDropOpt)) $iolDropOpt .= '<option value="">Please select</option>';
																	$iolDropOpt .= '<option value="'.$k.'">'.$v['lensType'].'</option>';
																}
															}
															
															$function = '';
															if(strtolower($val) == 'primary') $function = "setThisPlan(this.value);";
															
															$iolDropDown = '<div class="row"><div class="col-sm-4">'.$val.'</div><div class="col-sm-8"><select name="iolPlan" class="form-control minimal" id="iolPlanDrop" onChange="'.$function.'">'.$iolDropOpt.'<select></div></div>';
															
															echo"
																<tr>
																	<td>
																		".$iolDropDown."
																	</td>
																	<td>
																		<input type=\"text\" id=\"el_toric".$key."\" name=\"el_toric".$key."\" value=\"\" class=\"form-control\">
																	</td>
																	<td>
																		<input type=\"text\" id=\"el_power".$key."\" name=\"el_power".$key."\" value=\"\" class=\"form-control\">
																	</td>
																	<td>
																		<input type=\"text\" id=\"el_axis".$key."\" name=\"el_axis".$key."\" value=\"\" class=\"form-control\">
																	</td>
																	<!-- <td onclick=\"".$fun."\"><img src=\"".$library_path."/images/".$fun_s."\" /></td> -->
																</tr>
																";
														}
														
														
													}
														
														*/
													/*if($i==1||empty($id_chart_sx_plan_sheet)){	
												?>
														<tr>
															<td><?php echo $iolDropDown; ?></td>
															<td><input type="text" id="el_toric1" name="el_toric1" value="" class="form-control"></td>
															<td><input type="text" id="el_power1" name="el_power1" value="" class="form-control"></td>
															<td><input type="text" id="el_axis1" name="el_axis1" value="" class="form-control"></td>
															<td onclick="addToric()"><img src="<?php echo $library_path ?>/images/plu.png" alt=""/></td>
														</tr>
												<?php
													}*/
												?>
											</table>	
										</div>
									</div>
								</div>		
							</div>
						</div>
						
						<div class=" clearfix"></div>
						<div class="sxdtbx">
							<div class="sxhed"><h2>Sx Planning</h2></div>
							<div class="pd5">
								<div class="row">
									<div class="col-sm-8">
										<div class="row">
											<div class="col-sm-4">
												<label>Hooks</label>
												<input type="text" id="el_sx_pln_hook" name="el_sx_pln_hook" value="<?php echo $el_sx_pln_hook; ?>" class="form-control">
											</div>
											
											<div class="col-sm-4">
												<label>Flomax Cocktail</label>
												<input type="text" id="el_flomx_cocktail" name="el_flomx_cocktail" value="<?php echo $el_flomx_cocktail; ?>" class="form-control">
											</div>	
											
											<div class="col-sm-4">
												<label>Trypan Blue </label>
												<input type="text" id="el_flomx_cocktail" name="el_flomx_cocktail" value="<?php echo $el_flomx_cocktail; ?>" class="form-control">
											</div>	
												
											<div class="col-sm-4">
												<label>LRI </label>
												<input type="text" id="el_lri" name="el_lri" value="<?php echo $el_lri; ?>" class="form-control">
											</div>

											<div class="col-sm-4">
												<label>Femto </label>
												<input type="text" id="el_femto" name="el_femto" value="<?php echo $el_femto; ?>" class="form-control">
											</div>

											<div class="col-sm-4">
												<label>ECP </label>
												<select id="el_ecp" name="el_ecp" class="form-control minimal"><option value="">select</option>
													<?php
														$pdf_ecp = '';
														if(count($ar_admn_ecp)>0){
															foreach($ar_admn_ecp as $key => $val){
																if(!empty($val)){
																	if($el_ecp == $val)
																	{
																		$pdf_ecp = $val;
																	}
																	$sel = ($el_ecp == $val) ? "SELECTED" : "";
																	echo "<option value=\"".$val."\" ".$sel." >".$val."</option>";
																}
															}
														}
													?>	
												</select>
											</div>	
										</div>
									</div>	

									<div class="col-sm-4">
										<label for="el_sx_pln_com">Comments</label>
										<textarea id="el_sx_pln_com" name="el_sx_pln_com" class="form-control" rows="3" placeholder="Comments"><?php echo $el_sx_pln_com; ?></textarea>
									</div>
									<?php
											//IOL_Master
											$str="";$id="";
											$sql = "SELECT iol_master_id as id, examDate FROM iol_master_tbl WHERE patient_id='".$patient_id."' AND del_status='0' AND purged='0' ORDER BY examDate DESC, iol_master_id DESC  ";
											$rez = sqlStatement($sql);
											for($i=1;$row=sqlFetchArray($rez);$i++){
												if(!empty($row["id"])){
													$id=$row["id"];
													$dos=wv_formatDate($row["examDate"]);
													if($id==$el_ids_iol){
														$pdf_dos_IOL_MASTER = $dos;
													}
												}	
											}
											
											//Ascan
											$str="";$id="";
											$sql = "SELECT surgical_id as id, examDate FROM surgical_tbl WHERE patient_id='".$patient_id."' AND del_status='0' AND purged='0' ORDER BY examDate DESC, surgical_id DESC   ";
											$rez = sqlStatement($sql);
											for($i=1;$row=sqlFetchArray($rez);$i++){
												if(!empty($row["id"])){
													$id=$row["id"];
													$dos=wv_formatDate($row["examDate"]);
													if($id==$el_ids_ascan){
														$pdf_dos_ASCAN = $dos;
													}
												}	
											}
											
											//OCT
											$str="";$id="";
											$sql = "SELECT oct_id as id, examDate FROM oct WHERE patient_id='".$patient_id."' AND del_status='0' AND purged='0' ORDER BY  examDate DESC, oct_id DESC   ";
											$rez = sqlStatement($sql);
											for($i=1;$row=sqlFetchArray($rez);$i++){
												if(!empty($row["id"])){
													$id=$row["id"];
													$dos=wv_formatDate($row["examDate"]);
													if($id==$el_ids_oct){
														$pdf_dos_OCT = $dos;
													}
												}	
											}
											
											//Topogrphy 
											$str="";$id="";
											$sql = "SELECT topo_id as id, examDate FROM topography WHERE patientId='".$patient_id."' AND del_status='0' AND purged='0' ORDER BY  examDate DESC, topo_id DESC  ";
											$rez = sqlStatement($sql);
											for($i=1;$row=sqlFetchArray($rez);$i++){
												if(!empty($row["id"])){
													$id=$row["id"];
													$dos=wv_formatDate($row["examDate"]);
													if($id==$el_ids_topo){
														$pdf_dos_TOPOGRAPHY = $dos;
													}
												}	
											}
											
											//VF
											$str="";$id="";
											$sql = "SELECT vf_id as id, examDate FROM vf WHERE patientId='".$patient_id."' AND del_status='0' AND purged='0' ORDER BY examDate DESC , vf_id DESC   ";
											$rez = sqlStatement($sql);
											for($i=1;$row=sqlFetchArray($rez);$i++){
												if(!empty($row["id"])){
													$id=$row["id"];
													$dos=wv_formatDate($row["examDate"]);
													if($id==$el_ids_vf){
														$pdf_dos_VF = $dos;
													}
												}	
											}
									?>
								</div>
							</div>
						</div>
						
						<div id="dv_fstrip" class="sxdtbx blutab <?php echo (empty($data_fstrip) == true) ? 'hide' : '' ?>">
							<div class="pd5">
								<div class="row">
									<?php echo $data_fstrip; ?>
								</div>
							</div>
						</div>
						
						<?php 
							//Previous test block
							if(strlen($data_dv_drop_down_tests) > 0 && empty($data_dv_drop_down_tests) == false){
								echo '<div id="dv_drop_down_tests" class="pt10">
									'.$data_dv_drop_down_tests.'
								</div>';
							}
						?>
						
						
					</div>
				</div>
				
				<div id="tbl_button">
					<div class="row">
						<div id="module_buttons" class="ad_modal_footer col-sm-12 text-center">
							<input type="button" name="el_btn_new" value="New" class="btn btn-success" onclick="new_sx_sheet();">	
							<?php if(($elem_per_vo != "1") && (($finalize_flag != 1))){   //?> 
								
							<?php } ?>	
								<input type="button" name="el_btn_save" id="form_save_btn" value="Save" class="btn btn-success" onClick="saveSxData();">
								<?php 
									if(!isset($_REQUEST['get_frm']) && empty($_REQUEST['get_frm'])){
								?>
								<input type="button" name="el_btn_print" value="Print" class="btn btn-success" id="printSxBtnId" onClick="$('#printSxPlann').submit();">
								<?php  } ?>
								<input type="button" name="el_btn_cancel" value="Cancel" class="btn btn-danger" onclick="window.close();">	
						</div>
					</div>
				</div>
				<!-- Prev. plan sheet modal -->
				<div id="div_sx_pop_up" class="modal fade" role="dialog">
				  <div class="modal-dialog">

					<!-- Modal content-->
					<div class="modal-content">
					  <div class="modal-header bg-primary">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Previous Sx Planning Sheets</h4>
					  </div>
					  <div class="modal-body">
						<?php echo $data_div_sx_pop_up; ?>
					  </div>
					  <div id="module_buttons" class="modal-footer ad_modal_footer">
						<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
					  </div>
					</div>
				  </div>
				</div>
				
				<!-- Images modal -->
				<div id="div_img_modal" class="modal fade" role="dialog">
				  <div class="modal-dialog">

					<!-- Modal content-->
					<div class="modal-content">
					  <div class="modal-header bg-primary">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">imwemr</h4>
					  </div>
					  <div class="modal-body">
					  </div>
					  <div id="module_buttons" class="modal-footer ad_modal_footer">
						<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
					  </div>
					</div>
				  </div>
				</div>
				<input type="hidden" name="get_html" value="<?php echo htmlspecialchars($pdf_html); ?>">
			</div>
		</form>
		<?php
			$js_php_arr = json_encode($js_php_arr);

			// jQuery variables
			$js_vars =  '
			<script>
			 var js_php_arr = '.$js_php_arr.';
			</script>';
			echo $js_vars;
		?>
		<script src="<?php echo $library_path; ?>/js/work_view/sx_plan.js"></script>
	</body>
</html>	