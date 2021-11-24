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

$css="<style>
.text_b_w{
		font-size:12px;
		font-family:Arial, Helvetica, sans-serif;
		font-weight:bold;
}
.paddingLeft{
	padding-left:5px;
}
.paddingTop{
	padding-top:5px;
}
.tb_subheading{
	font-size:12px;
	font-family:Arial, Helvetica, sans-serif;
	font-weight:bold;
	color:#000000;
	background-color:#f3f3f3;;
}
.tb_heading{
	font-size:11px;
	font-family:Arial, Helvetica, sans-serif;
	font-weight:bold;
	color:#FFFFFF;
	background-color:#999999;
	margin-top:10px;
}
.tb_headingHeader{
	font-size:12px;
	font-family:Arial, Helvetica, sans-serif;
	font-weight:bold;
	color:#FFFFFF;
	background-color:#4684ab;
}
.tb_dataHeader{
	font-size:12px;
	font-family:Arial, Helvetica, sans-serif;
	font-weight:bold;
	color:#000;
	background-color:#9a9a9a;
}
.text_lable{
		font-size:12px;
		font-family:Arial, Helvetica, sans-serif;
		background-color:#FFFFFF;
		font-weight:bold;
}
.text_value{
		font-size:12px;
		font-family:Arial, Helvetica, sans-serif;
		font-weight:100;
		background-color:#FFFFFF;
	}
.text_blue{
		font-size:12px;
		font-family:Arial, Helvetica, sans-serif;
		color:#0000CC;
	font-weight:bold;
	}
.text_green{
		font-size:12px;
		font-family:Arial, Helvetica, sans-serif;
		color:#006600;
		font-weight:bold;
}
.hylight{background-color:lightblue; }

table{
			font-size:14px;
		}
		.fheader{
			padding:5px 0px 5px 0px;
			font-weight:bold;
			font-size:16px;
			text-decoration:underline;
			text-align:center;
		}
		.bold{
			font-weight:bold;
		}
		.pt5{
			padding-top:5px;	
		}
		.pd{
			padding:4px;		
		}
		.pl5{
			padding-left:5px;
		}
		.bgcolor{
			background:#C0C0C0;
		}
		.cbold{
			text-align:center;
			font-weight:bold;		
		}
		.bdrbtm{
			border-bottom:1px solid #C0C0C0;
			height:20px;	
			vertical-align:baseline;
		}
		.bdrtop{
			border-top:1px solid #C0C0C0;
			//height:10px;	
		}
		.bdrrght{
			border-right:1px solid #C0C0C0;
			//height:20px;
			vertical-align:baseline;
		}
		
		.bdrlft{
			border-left:1px solid #C0C0C0;
			//height:20px;
			vertical-align:baseline;
		}
		.bdrbtm_new{
			border-bottom:1px solid #C0C0C0;
			vertical-align:baseline;
		}
		.bdrrght_new{
			border-right:1px solid #C0C0C0;
			vertical-align:baseline;
		}
		.bdrBtmRght{
			border-bottom:1px solid #C0C0C0;
			border-right:1px solid #C0C0C0;
			vertical-align:baseline;
		}
		.tb_headingHeader{
			font-weight:bold;
			color:#FFFFFF;
			background-color:#4684ab;
		}

</style>";

require_once(dirname(__FILE__).'/../../../config/globals.php');

require($GLOBALS['incdir']."/chart_notes/chart_globals.php");

//require_once($GLOBALS['srcdir']."/classes/work_view/wv_functions.php");
//require_once($GLOBALS['srcdir']."/classes/work_view/wv_functions_new.php");

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
//pre($_SESSION);
//echo 'finalize_id =>'. $_SESSION["finalize_id"];

$patient_id = $_SESSION['patient'];
$sx_pl_id = (isset($_REQUEST['get_sx_id']) && empty($_REQUEST['get_sx_id']) == false) ? getDateFormatDB($_REQUEST['get_sx_id']) : '';
$sx_obj = New Sx_Plan($patient_id,$sx_pl_id,$finalize_flag);

//Sx Plan Data
$sx_plan_data = '';
$sx_plan_data = $sx_obj->get_sx_plan_data($sx_obj->patient_id,$sx_obj->sx_plan_id);
if(!isset($_REQUEST['get_frm'])){
	extract($sx_plan_data);
}


//Sx Plan DOS data
$sx_plan_dos_arr = $sx_obj->get_pt_sx_dos_arr($sx_obj->patient_id);

$el_date_surgery = ($el_date_surgery == '00-00-0000') ? '' : $el_date_surgery;
$el_prev_eye_date = ($el_prev_eye_date == '00-00-0000') ? '' : $el_prev_eye_date;


//Previous Test Data dropdown
$data_dv_drop_down_tests = $sx_obj->sps_getTestsDropDown($sx_obj->patient_id,$el_ids_iol, $el_ids_ascan, $el_ids_oct, $el_ids_topo, $el_ids_vf);

//Prev. Data
$data_div_sx_pop_up = $sx_obj->sps_getPrvSxPlanSheet($sx_obj->patient_id,$sx_plan_dos);

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
	
	//
	
	$sql = "
		SELECT 
		c0.status_elements,
		c1.mr_none_given, c1.ex_number,
		c2.sph as sph_r, c2.cyl as cyl_r, c2.axs as axs_r, c2.ad as ad_r, 
		c2.txt_1 as txt_1_r, c2.txt_2 as txt_2_r, c2.sel_2 as sel_2_r, c2.sel2v as sel2v_r,
		c3.sph as sph_l, c3.cyl as cyl_l, c3.axs as axs_l, c3.ad as ad_l, 
		c3.txt_1 as txt_1_l, c3.txt_2 as txt_2_l, c3.sel_2 as sel_2_l, c3.sel2v as sel2v_l
		FROM chart_vis_master c0
		LEFT JOIN chart_pc_mr c1 ON c1.id_chart_vis_master = c0.id AND c1.ex_type='MR' AND c1.delete_by='0'	
		LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
		LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'
		WHERE  c0.patient_id = '".$patient_id."' AND c0.form_id='".$form_id."' 
		ORDER BY c1.ex_number DESC			
	";
	$res = sqlStatement($sql);
	for($i=1; $row=sqlFetchArray($res);$i++){
		$rse = $row["status_elements"];
		$cc = $row["ex_number"];
		
		$indx1 = $indx2 = "";
		if($cc>1){
			$indx1 = "Other";
			if($cc>2){
				$indx2 = "_".$cc;
			}
		}
		$flg=0;
		if( (strpos($rse,"elem_visMr".$indx1."OdS".$indx2."=1")!==false) || (strpos($rse,"elem_visMr".$indx1."OdC".$indx2."=1")!==false) || (strpos($rse,"elem_visMr".$indx1."OdA".$indx2."=1")!==false) || (strpos($rse,"elem_visMr".$indx1."OdTxt1".$indx2."=1")!==false) ||
			(strpos($rse,"elem_visMr".$indx1."OdAdd".$indx2."=1")!==false) || (strpos($rse,"elem_visMr".$indx1."OdTxt2".$indx2."=1")!==false) || (strpos($rse,"elem_visMr".$indx1."OdSel2".$indx2."=1")!==false) || (strpos($rse,"elem_visMr".$indx1."OdSel2Vision".$indx2."=1")!==false)		
		){    
			//Mr1
			if(!empty($row["sph_r"])) { $ref_od.="S ".$row["sph_r"].", "; }
			if(!empty($row["cyl_r"])) { $ref_od.="C ".$row["cyl_r"].", "; }
			if(!empty($row["axs_r"])) { $ref_od.="A ".$row["axs_r"].", "; }			
			if(!empty($row["txt_1_r"])) { $ref_od.=" ".$row["txt_1_r"]." "; }
			if(!empty($row["ad_r"])) { $ref_od.="Add ".$row["ad_r"]." "; }
			if(!empty($row["txt_2_r"])) { $ref_od.=" ".$row["txt_2_r"]." "; }
			if(!empty($row["sel_2_r"])) { $ref_od.=" ".$row["sel_2_r"]." "; }
			if(!empty($row["sel2v_r"])) { $ref_od.=" ".$row["sel2v_r"]." "; }
			$flg=1;
		}
		
		if( (strpos($rse,"elem_visMr".$indx1."OsS".$indx2."=1")!==false) || (strpos($rse,"elem_visMr".$indx1."OsC".$indx2."=1")!==false) || (strpos($rse,"elem_visMr".$indx1."OsA".$indx2."=1")!==false) || (strpos($rse,"elem_visMr".$indx1."OsTxt1".$indx2."=1")!==false) ||
			(strpos($rse,"elem_visMr".$indx1."OsAdd".$indx2."=1")!==false) || (strpos($rse,"elem_visMr".$indx1."OsTxt2".$indx2."=1")!==false) || (strpos($rse,"elem_visMr".$indx1."OsSel2".$indx2."=1")!==false) || (strpos($rse,"elem_visMr".$indx1."OsSel2Vision".$indx2."=1")!==false)		
		){    
			//Mr1
			if(!empty($row["sph_l"])) { $ref_os.="S ".$row["sph_l"].", "; }
			if(!empty($row["cyl_l"])) { $ref_os.="C ".$row["cyl_l"].", "; }
			if(!empty($row["axs_l"])) { $ref_os.="A ".$row["axs_l"].", "; }			
			if(!empty($row["txt_1_l"])) { $ref_os.=" ".$row["txt_1_l"]." "; }
			if(!empty($row["ad_l"])) { $ref_os.="Add ".$row["ad_l"]." "; }
			if(!empty($row["txt_2_l"])) { $ref_os.=" ".$row["txt_2_l"]." "; }
			if(!empty($row["sel_2_l"])) { $ref_os.=" ".$row["sel_2_l"]." "; }
			if(!empty($row["sel2v_l"])) { $ref_os.=" ".$row["sel2v_l"]." "; }
			$flg=1;
		}
		
		//
		$ar_ret["ref"]=($_GET["eye"]=="OD") ? $ref_od : $ref_os ;
		$ar_ret["oref"]=($_GET["eye"]=="OD") ? $ref_os : $ref_od ;
		
		if($flg){break;}
	}
	
	$sql = "
		SELECT 
		c0.status_elements,
		c5.k_od, c5.slash_od, c5.x_od, c5.k_os, c5.slash_os, c5.x_os, c5.k_type
		FROM chart_vis_master c0
		LEFT JOIN chart_ak c5 ON c5.id_chart_vis_master = c0.id
		WHERE  c0.patient_id = '".$patient_id."' AND c0.form_id='".$form_id."' 
		
	";
	$row=sqlQuery($sql);
	if($row!=false){
		$rse = $row["status_elements"];
		if( (strpos($rse,"elem_visAkOdK=1")!==false) || (strpos($rse,"elem_visAkOdSlash=1")!==false) || (strpos($rse,"elem_visAkOdX=1")!==false) ||
			(strpos($rse,"elem_visAkOsK=1")!==false) || (strpos($rse,"elem_visAkOsSlash=1")!==false) || (strpos($rse,"elem_visAkOsX=1")!==false) 
		){ 
			$ar_ret["kflat"]=($_GET["eye"]=="OD") ? $row["k_od"] : $row["k_os"] ;
			$ar_ret["ksteep"]=($_GET["eye"]=="OD") ? $row["slash_od"] : $row["slash_os"] ;
			$ar_ret["kaxis"]=($_GET["eye"]=="OD") ? $row["x_od"] : $row["x_os"] ;
			$ar_ret["kcyl"]="";
			
			$ar_ret["okflat"]=($_GET["eye"]=="OD") ? $row["k_os"] : $row["k_od"] ;
			$ar_ret["oksteep"]=($_GET["eye"]=="OD") ? $row["slash_os"] : $row["slash_od"] ;
			$ar_ret["okaxis"]=($_GET["eye"]=="OD") ? $row["x_os"] : $row["x_od"] ;
			$ar_ret["okcyl"]="";
		}
	}
	
	
	//Pachy
	//Correction Values
	$sql = "SELECT * FROM chart_correction_values WHERE form_id='".$form_id."' AND patient_id='".$patient_id."' ";
	$row = sqlQuery($sql);
	if($row != false){
		$elem_od_readings = $row["reading_od"];
		$elem_od_average = $row["avg_od"];
		$elem_od_correction_value = $row["cor_val_od"];		
		$elem_os_readings = $row["reading_os"];
		$elem_os_average = $row["avg_os"];
		$elem_os_correction_value = $row["cor_val_os"];
		if(!empty($row["cor_date"]) && ($row["cor_date"] != "0000-00-00")) $elem_cor_date = get_date_format($row["cor_date"]);
		//
		if($_GET["eye"]=="OD"){
			if(!empty($elem_od_average)){ $elem_od_average=$elem_od_average." "; }else{ $elem_od_average=""; }
			$ar_ret["pachy"]=$elem_od_readings." ".$elem_od_average."".$elem_od_correction_value;
		}else if($_GET["eye"]=="OS"){
			if(!empty($elem_os_average)){ $elem_os_average=$elem_os_average." "; }else{ $elem_os_average=""; }
			$ar_ret["pachy"]=$elem_os_readings." ".$elem_os_average."".$elem_os_correction_value;
		}
	}
	
	//
	echo json_encode($ar_ret);	
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
?>
<!DOCTYPE html>
<html>
	<head>
		<!--<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		<!--<title>Sx Planning Sheet</title>
		<!-- Bootstrap -->
		<!--<link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
		<!-- Bootstrap Selctpicker CSS -->
		<!--<link href="<?php echo $library_path; ?>/css/bootstrap-select.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/workview.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/wv_landing.css" rel="stylesheet" type="text/css">
		<!-- Messi Plugin for fancy alerts CSS -->
			<link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet" type="text/css">
		<!-- DateTime Picker CSS -->
		<!--<link rel="stylesheet" type="text/css" href="<?php echo $library_path; ?>/css/jquery.datetimepicker.min.css"/>
		
		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
			  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]--> 
		
		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<!--<script src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js" type="text/javascript" ></script>
		<!-- jQuery's Date Time Picker -->
		<!--<script src="<?php echo $library_path; ?>/js/jquery.datetimepicker.full.min.js" type="text/javascript" ></script>
		<!-- Bootstrap -->
		<!--<script src="<?php echo $library_path; ?>/js/bootstrap.js" type="text/javascript" ></script>
		<!-- Bootstrap Selectpicker -->
		<!--<script src="<?php echo $library_path; ?>/js/bootstrap-select.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/js/common.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/messi/messi.js" type="text/javascript"></script>
	</head> -->
	<!--<body onload="fullScreen();">-->
	<?php
	
	// Main Content
	$pdf_html = '';	
$pdf_html .='<page backtop="5mm" backbottom="5mm">
	<page_header>
		<table style="width:100%;border-collapse:collapse" border="0" cellspacing="0"  cellpadding="0">
				<tr>
					<td style="width:40%" class="tb_headingHeader">'.$pt_data['pt_name'].'-'.$pt_data['ptId'].'</td>
					<td style="width:30%" class="tb_headingHeader">'.$pt_data['pt_gender'].'.('.$pt_data['pt_age'].')'.$pt_data['pt_dob'].'&nbsp;</td>
				    <td style="width:30%; text-align:right" class="tb_headingHeader">Date of Service:&nbsp;'.$dos.'&nbsp;</td>
				</tr>
		</table>
	</page_header>';  

$pdf_html .='<table style="width:100%;border-collapse:collapse" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="text_b_w" style="width:30%;" align="left"><strong>Sx Plan Sheet</strong></td>
				<td class="text_b_w" style="width:1%;"></td>
				<td class="text_value" style="width:69%;" align="right">Printed by:'.$opertator_name.'&nbsp;on&nbsp;'.get_date_format(date("Y-m-d"))." ".date("H:i:s").'</td>
			</tr>
			<tr>
				<td class="text_b_w" style="width:100%;" colspan="3"><hr/></td>
			</tr>
		</table>
'; 


$pdf_html .='<table style="width:100%;border-collapse:collapse" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td style="width:40%" align="left" valign="top"> 
					<table style="width:100%;" border="0" cellspacing="0"  cellpadding="0">
						<tr>
							<td style="width:100%" class="text_lable">'.$pt_data['pt_name'].'-'.$pt_data['ptId'].'</td>
						</tr>';
		 	if($pt_data['pt_gender'] != ''){		
				
				$ptAge = ""; $ptDOB = "";
				if(!empty($pt_data['pt_age'])){ $ptAge = '('.$pt_data['pt_age'].')'; }
				if(!empty($pt_data['pt_age'])){ $ptDOB = $pt_data['pt_dob']; }
				$pdf_html .='<tr>
							<td style="width:100%" class="text_value">'.$pt_data['pt_gender'].'.('.$pt_data['pt_age'].')'.$pt_data['pt_dob'].'&nbsp;</td>
						</tr>';
		 	}
		 
		 	if($pt_data['street'] != ''){ 
				$pdf_html .='<tr>
							<td style="width:100%" class="text_value">'.$pt_data['street'].'&nbsp;</td>
						</tr>';
		 	}
			if($pt_data['street2'] != ''){		
				$pdf_html .='<tr>
							<td style="width:100%" class="text_value">'.$pt_data['street2'].'&nbsp; </td>
						</tr>';
			}
			if($pt_data['pt_city'] != '' || $pt_data['pt_state'] != '' || $pt_data['pt_postal_code'] != ''){	
				$patientAddress="";
				if($pt_data['pt_city'] != '' && $pt_data['pt_state'] != ''){
						$patientAddress=$pt_data['pt_city'].','.$pt_data['pt_state'].'&nbsp;'.$pt_data['pt_postal_code'];
				}else{
						$patientAddress=$pt_data['pt_city'].$pt_data['pt_state'].'&nbsp;'.$pt_data['pt_postal_code'];
				}
										
				$pdf_html .='<tr>
							<td style="width:100%" class="text_value">'.$patientAddress.'</td>
						</tr>';
			}
			
			$pdf_html .='<tr>
							<td style="width:100%" class="text_value">Ph.: '.core_phone_format($pt_data['pt_phone_home']).'&nbsp; </td>
						</tr>
					</table>
			  </td>';
			$pdf_html .='<td style="width:20%"  valign="top">&nbsp;</td>';
			$pdf_html .='<td style="width:40%" align="right" valign="top">
					<table style="width:100%;" border="0" cellspacing="0"  cellpadding="0">
						<tr>
							<td style="width:100%" class="text_lable">'.$groupDetails['name'].'</td>
						</tr>';
			if($groupDetails['group_Address1'] != ''){			
				$pdf_html .='<tr>
							<td style="width:100%" class="text_value">'.ucwords($groupDetails['group_Address1']).'</td>
						</tr>';
			}
			if($groupDetails['group_Address2'] != ''){	
				$pdf_html .='<tr>
							<td style="width:100%" class="text_value">'.ucwords($groupDetails['group_Address2']).'&nbsp;</td>
						</tr>';
			}
			
				$pdf_html .='<tr>
							<td style="width:100%" class="text_value">'.$groupDetails['group_City'].', '.$groupDetails['group_State'].' '.$groupDetails['group_Zip'].'</td>
						</tr>';
			if($groupDetails['group_Telephone'] != ''){			
				$pdf_html .='<tr>
							<td style="width:100%" class="text_value">Ph.:&nbsp;'.$groupDetails['group_Telephone'].'</td>
						</tr>';
			}
				$pdf_html .='<tr>
							<td style="width:100%" class="text_value">Fax:&nbsp;'.$groupDetails['group_Fax'].'</td>
						</tr>
					</table>
				</td>';  
			$pdf_html .='</tr>
		</table>'; 
				

	
	// Following Values can be used in $pdf_print_type
		# only border = onlyBdr;
		# full border = fullBdr;

		$pdf_print_type = 'onlyBdr'; 

		if($pdf_print_type == 'onlyBdr'){
			$pdf_border_class = '';
		}else if($pdf_print_type == 'fullBdr'){
			$pdf_border_class = 'pd bdrBtmRght';
		}

		$pdf_html .= '<table id="ptinfo" style="width:100%;font-size:12px;border-collapse:collapse;border:1px solid #C0C0C0">
			<tr>
				<td colspan="4" class="bdrbtm" style="width:100%;height:5px"></td>
			</tr>
			<tr>
				<td class="tb_dataHeader pd bgcolor" style="width:23%;vertical-align:baseline"><strong>Patient Name:</strong>  '.$pt_data['pt_name'].'</td>
				<td class="tb_dataHeader pd bgcolor" style="width:26%;vertical-align:baseline"><strong>DOB:</strong> '.$pt_data['pt_dob'].'</td>
				<td class="tb_dataHeader pd bgcolor" style="width:25%;vertical-align:baseline"><strong>Account:</strong> '.$sx_obj->patient_id.'</td>
				<td class="tb_dataHeader pd bgcolor" style="width:26%;vertical-align:baseline"><strong>Age:</strong> '.$pt_data['pt_age'].'</td>
			</tr>
		</table>';
	?>
		<!--<form id="frm_sx_plan_sheet" name="frm_sx_plan_sheet" action="save_sx_plan_sheet.php" method="post" onSubmit="return check_form();"> <!-- saveCharts.php -->
		<!--	<div class="whtbox">	
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
															$dos_opt .= '<li onclick="get_sx_dos(this);" data-id="'.$obj['id'].'"><a href="javascript:void(0);">'.$obj['sx_pl_dos'].'</a></li>';
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
					
					<?php
						$pdf_html .= '<table id="tblsx" style="width:100%;font-size:12px;border-collapse:collapse;border:1px solid #C0C0C0">
						<tr>
							<td class="bdrlft pd '.$pdf_border_class.'" style="width:10%;vertical-align:baseline"><strong>Sx:</strong> '.$el_sx_type.'</td>
							<td class="pd '.$pdf_border_class.'" style="width:13%;vertical-align:baseline"><strong>Allergies:</strong> '.$ptAllergies.'</td>
							<td class="pd '.$pdf_border_class.'" style="width:13%;vertical-align:baseline"><strong>Diabetic:</strong> '.$ptDiabetic.'</td>	
							<td class="pd '.$pdf_border_class.'" style="width:13%;vertical-align:baseline"><strong>Flomax:</strong> '.$pt_flomax.'</td>	
							<td class="pd '.$pdf_border_class.'" style="width:10%;vertical-align:baseline"><strong>PCP:</strong> '.$ptPcp.'</td>
							<td class="pd '.$pdf_border_class.'" style="width:15%;vertical-align:baseline"><strong>Referring:</strong> '.$ptRefer.'</td>	
							<td class="pd '.$pdf_border_class.' bdrRght" style="width:26%;vertical-align:baseline"><strong>Co Managed:</strong> '.$ptCoManage.'</td>
						</tr>
						</table>';
						
						if($el_k_given != ''){
							$pdf_el_k_given = '<tr>
									<td colspan="4" style="width:100%;" class="tb_dataHeader bgcolor pd">'.$el_k_given."'s".'</td>
								</tr>';
						}else{
							$pdf_el_k_given = '';
						}

							$pdf_html .= '
							<table id="ptchoice" style="width:100%;border-collapse:collapse;border:1px solid #C0C0C0;font-size:12px">
								<tr>
									<td class="bdrlft pd '.$pdf_border_class.'" style="width:23%;vertical-align:baseline"><strong>Parent Choices:</strong> '.$patient_choice_title_pdf.'</td>
									<td class="pd '.$pdf_border_class.'" style="width:26%;vertical-align:baseline" ><strong>Previous Sx/Procedures</strong></td>
									<td class="pd '.$pdf_border_class.'" style="width:25%;vertical-align:baseline"><strong>Ocular:</strong> '.$ocular_title_pdf.'</td>
									<td class="pd '.$pdf_border_class.' bdrrght" style="width:26%;vertical-align:baseline"><strong>Systemic:</strong> '.$systemetic_title_pdf.'</td>
								</tr>'.$pdf_el_k_given.'
								
							</table>';
					?>
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
													<option value=""></option>
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
													<select class="form-control minimal" name="el_surgeon_id" id="el_surgeon_id">
														<option value=""></option>
														<?php
														$pdf_selected_surgeon = '';
														$tmp = $el_surgeon_id;
														$phyArray = $sx_obj->getMrPersonnal(2,"cn2");
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
													<textarea id="el_refraction" name="el_refraction" class="form-control" rows="1">
														<?php echo trim($el_refraction); ?>
													</textarea>
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
													<h2><input type="checkbox" id="el_k_given_oct" name="el_k_given_oct" class="frcb" value="OCT" <?php echo ($el_k_given=="OCT") ? "CHECKED" : "" ;?>><label for="el_k_given_oct" class="frcb">OCT K's</label></h2>
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
									<div class="sxhed">
										<h2><input type="checkbox" id="el_k_given_iol" name="el_k_given_iol" class="frcb" value="IOL Master" <?php echo ($el_k_given=="IOL Master") ? "CHECKED" : "" ;?>><label for="el_k_given_iol" class="frcb">IOL Master K's</h2>
									</div>
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
										<textarea id="el_iol_desc" name="el_iol_desc" class="form-control" rows="5" placeholder="Comments"><?php echo $el_iol_desc;?></textarea>
									</div>
								</div>
							</div>
						</div>
	<?php
		$pdf_html .='<table id="tbl_ks" border="0" style="width:100%;font-size:12px;border-collapse:collapse">
					<tr>
						<td class="bdrlft pd '.$pdf_border_class.'" style="width:49%;vertical-align:baseline;">
							<table style="width:100%;border-collapse:collapse;font-size:12px">
								<tr>
									<td style="50%;vertical-align:baseline"><strong>Eye:</strong>'.$el_mank_eye.'</td>
									<td style="50%;vertical-align:baseline"><strong>Ref:</strong>'.$el_mank_ref.'</td>
								</tr>
							</table>
						</td>
						<td class="pd '.$pdf_border_class.' bdrRght" style="width:51%;padding-left:0;vertical-align:baseline">
							<table style="width:100%;border-collapse:collapse;font-size:12px">
								<tr>
									<td style="50%"></td>
									<td style="50%;vertical-align:baseline"><strong>Dominant Eye:</strong>'.$el_domi.'</td>
								</tr>
							</table>
						</td>
					</tr>
					
					<tr>
						<td class="bdrlft pd '.$pdf_border_class.'" style="width:49%;vertical-align:baseline">
							<table style="width:100%;border-collapse:collapse;font-size:12px">
								<tr>
									<td style="width:100%;vertical-align:baseline"><strong>Refraction:</strong></td>
								</tr>
								<tr>
									<td style="width:100%;vertical-align:baseline">'.$el_refraction.'</td>
								</tr>
							</table>
						</td>
						<td class="pd '.$pdf_border_class.' bdrRght" style="width:51%;padding-left:2px;vertical-align:baseline">
							<table style="width:100%;border-collapse:collapse;font-size:12px">
								<tr>
									<td class="p15" style="width:100%;vertical-align:baseline"><strong>Other Eye Refraction:</strong></td>
								</tr>
								<tr>
									<td style="width:100%;vertical-align:baseline">'.$el_othr_eye_ref.'</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td class="bdrlft pd '.$pdf_border_class.' bdrRght" colspan="2" style="width:100%;vertical-align:baseline"><strong>Recommendations: </strong>'.$el_iol_recomd_pdf.'</td>
					</tr>
					<tr>
						<td class="bdrlft pd bdrBtmRght" colspan="2" style="width:100%;vertical-align:baseline"><strong>Comments: </strong>'.$el_iol_desc.'</td>
					</tr>
					</table>';
					
	if($el_date_surgery != '' && $el_date_surgery != '00-00-0000'){$pdf_el_date_surgery = $el_date_surgery;}else{$pdf_el_date_surgery = '';}
		$pdf_html .='<table style="width:100%;border-collapse:collapse;font-size:12px">
						<tr>
							<td style="width:33%;border-right:1px solid #fff;vertical-align:baseline" class="tb_dataHeader bgcolor pd">Surgery</td>
							<td style="width:33.5%;border-right:1px solid #fff;vertical-align:baseline" class="tb_dataHeader bgcolor pd">Final K\'s</td>
							<td style="width:33.5%;vertical-align:baseline" class="tb_dataHeader bgcolor pd">Other Eye K\'s</td>
						</tr>
						<tr>
							<td class="bdrlft pd bdrBtmRght" style="width:33%;vertical-align:baseline"> 
								<table style="width:100%;border-collapse:collapse;font-size:12px">
									<tr>
										<td style="width:50%"><strong>Date : </strong>'.$pdf_el_date_surgery.'</td>
										<td style="width:50%"><strong>Time : </strong>'.trim($el_time_surgery).'</td>
									</tr>
								</table>
							</td>
							
							<td class="pd bdrBtmRght" style="width:33.5%;vertical-align:baseline"> 
								<table style="width:100%;font-size:12px">
									<tr>
										<td style="width:25%"><strong>Flat</strong></td>
										<td style="width:25%"><strong>Steep</strong></td>
										<td style="width:25%"><strong>Axis</strong></td>
										<td style="width:25%"><strong>Cyl</strong></td>
									</tr>
									<tr>
										<td style="width:25%">'.trim($el_k_flat).'</td>
										<td style="width:25%">'.trim($el_k_steep).'</td>
										<td style="width:25%">'.trim($el_k_axis).'</td>
										<td style="width:25%">'.trim($el_k_cyl).'</td>
									</tr>
								</table>
							</td>
							
							<td class="pd bdrBtmRght" style="width:33.5%;vertical-align:baseline"> 
								<table style="width:100%;font-size:12px">
									<tr>
										<td style="width:25%"><strong>Flat</strong></td>
										<td style="width:25%"><strong>Steep</strong></td>
										<td style="width:25%"><strong>Axis</strong></td>
										<td style="width:25%"><strong>Cyl</strong></td>
									</tr>
									<tr>
										<td style="width:25%">'.trim($el_ok_flat).'</td>
										<td style="width:25%">'.trim($el_ok_steep).'</td>
										<td style="width:25%">'.trim($el_ok_axis).'</td>
										<td style="width:25%">'.trim($el_ok_cyl).'</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
					<table style="width:100%;border-collapse:collapse;font-size:12px">
						<tr>
							<td style="width:50%;border-right:1px solid #fff;vertical-align:baseline" class="tb_dataHeader pd bgcolor">Surgeon</td>
							<td style="width:50%;margin-left:1px;vertical-align:baseline" class="tb_dataHeader pd bgcolor">Procedure</td>
						</tr>
						<tr>
							<td rowspan="2" style="width:50%;vertical-align:baseline" class="pd bdrlft '.$pdf_border_class.'">'.$pdf_selected_surgeon.'</td>
							<td style="width:50%;vertical-align:baseline" class="pd '.$pdf_border_class.' bdrRght">'.$el_proc_prim.'</td>
						</tr>
						<tr>
							<td style="width:50%;vertical-align:baseline" class="pd '.$pdf_border_class.' bdrRght">'.$el_proc_sec.'</td>
						</tr>
					</table>';		
	?>	
						<div class=" clearfix"></div>
						<div class="sxdtbx">
							<div class="sxhed">
								<h2>Planning</h2>
							</div>
							<div class=" clearfix"></div>
							<div class="plr5">
								<div class="row">
									<div class="col-sm-10">
										<div class="row">
											<div class="col-sm-4">
												<div class="row">
													<div class="col-sm-6">
														<label id="link_prev_eye" class="text_purple pointer" data-toggle="modal" data-target="#div_sx_pop_up">Previous Eye</label>
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
											
											<div class="col-sm-4">
												<div class="row">
													<div class="col-sm-3">
														<label>VA </label>
														<div class="input-group">
															<input type="text" id="el_prev_eye_va" name="el_prev_eye_va" value="<?php echo $el_prev_eye_va;?>" class="form-control">
															<?php echo get_simple_menu($sx_obj->arrAcuitiesMrDis,"menu_acuitiesMrDis","el_prev_eye_va");?>	
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
													<div class="col-sm-6">
														<label>Pupil Max </label>
														<input type="text" id="el_pupilmx" name="el_pupilmx" value="<?php echo $el_pupilmx;?>" class="form-control">
													</div>
													<div class="col-sm-6">
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
						<?php
							$pdf_html .= '
								<table style="width:100%;font-size:12px;border-collapse:collapse">
									<tr>
										<td class="bdrlft pt5" style="width:15%;vertical-align:baseline"> <strong> Previous Eye: </strong></td>
										<td class="pt5" style="width:3%;vertical-align:baseline">'.$el_prev_eye_site.'</td>
										<td class="pt5" style="width:15%;vertical-align:baseline"><strong>Date: </strong>'.$el_prev_eye_date.'</td>
										<td class="pt5" style="width:15%;vertical-align:baseline"><strong>Lens: </strong>'.$el_prev_eye_lens.'</td>
										<td class="pt5" style="width:13%;vertical-align:baseline"><strong>Power: </strong>'.$el_prev_eye_power.'</td>
										<td class="pt5" style="width:10%;vertical-align:baseline"><strong>Cyl: </strong>'.$el_prev_eye_cyl.'</td>
										<td class="pt5" style="width:15%;vertical-align:baseline"><strong>Axis : </strong>'.$el_prev_eye_axis.'</td>
										<td class="bdrrght pt5" style="width:14%;vertical-align:baseline"><strong>VA  : </strong>'.$el_prev_eye_va.'</td>
									</tr>
								</table>
								<table style="width:100%;font-size:12px;border-collapse:collapse">		
									<tr>
										<td class="bdrlft pt5" style="width:15%;vertical-align:baseline"></td>
										<td class="pt5" style="width:18%;vertical-align:baseline"><strong>ORA Results:</strong>'.$el_prev_eye_ora_res.'</td>
										<td class="pt5" style="width:18%;vertical-align:baseline"><strong>Toric Position:</strong>'.$el_prev_eye_torpos.'</td>
										<td class="pt5" style="width:15%;vertical-align:baseline">'.$el_prev_eye_comm.'</td>
										<td class="pt5" style="width:20%;vertical-align:baseline"><strong>Method-Lens SX: </strong>'.$el_meth_lens.'</td>
										<td class="pt5 bdrrght" style="width:14%;vertical-align:baseline"><strong>ORA: </strong>'.$el_ora.'</td>
									</tr>	
								</table>
								<table style="width:100%;font-size:12px;border-collapse:collapse">		
									<tr>
										<td class="bdrlft pt5" style="width:15%;vertical-align:baseline"></td>
										<td class="pt5" style="width:18%;vertical-align:baseline"><strong>Version :</strong>'.$el_version.'</td>
										<td class="pt5" style="width:18%;vertical-align:baseline"><strong>MBN :</strong>'.$MBN_pdf.'</td>
										<td class="pt5 bdrRght" style="width:49%;vertical-align:baseline"><strong>Premium Lens: </strong>'.$premium_lens_pdf.'</td>
									</tr>		
								</table>
								<table id="tbl_cci" style="width:100%;border-collapse:collapse;font-size:12px">
									<tr>
										<td class="bdrlft pd bdrbtm" style="width:20%;vertical-align:baseline"><strong>CCI:</strong>'.$el_cci.'</td>
										<td class="pd bdrbtm" style="width:20%;vertical-align:baseline"><strong>Pachymetry</strong>'.$el_pachy.'</td>
										<td class="pd bdrbtm" style="width:20%;vertical-align:baseline"><strong>White to White</strong>'.$el_w2w.'</td>
										<td class="pd bdrbtm" style="width:20%;vertical-align:baseline"><strong>Pupil Max</strong>'.$el_pupilmx.'</td>
										<td class="pd bdrBtmRght" style="width:20%;vertical-align:baseline"><strong>Cap Max</strong>'.$el_cupmx.'</td>
									</tr>
								</table>';
								
								//PDF Code
								$pdf_predict_sel = '';
								if(strpos($el_predict_sel, "Barret")!==false){
									$pdf_predict_sel .= "<strong>Barret</strong><br>";
								}

								if(strpos($el_predict_sel, "SRK-T /HQ")!==false){
									$pdf_predict_sel .= "<strong>SRK-T /HQ</strong><br>";
								}

								if(strpos($el_predict_sel, "Holiday- I / II")!==false){
									$pdf_predict_sel .= "<strong>Holiday- I / II</strong><br>";
								}
								$pdf_html .= '
									<table id="tbl_lens" style="width:100%;border-collapse:collapse;font-size:12px">
										<tr>
											<td style="width:10%;text-align:center;border-right:1px solid #fff" class="tb_dataHeader bgcolor pd">Lens</td>
											<td colspan="4" style="width:45%;text-align:center;font-size:11px;border-right:1px solid #fff" class="tb_dataHeader bgcolor pd">PrePlan Lens (Traditional IOL) differs from Primary Lens</td>
											<td style="width:5%;text-align:center;border-right:1px solid #fff" class="tb_dataHeader bgcolor pd"></td>
											<td style="width:10%;text-align:center;border-right:1px solid #fff" class="tb_dataHeader bgcolor pd">Target</td>
											<td style="width:10%;text-align:center;border-right:1px solid #fff" class="tb_dataHeader bgcolor pd">Predicted</td>
											<td style="width:10%;text-align:center;border-right:1px solid #fff" class="tb_dataHeader bgcolor pd">ACD/AL(%)</td>
											<td style="width:10%;text-align:center" class="tb_dataHeader bgcolor pd">S/P CRS</td>
										</tr>
										
										<tr>
											<td class="bdrlft '.$pdf_border_class.'" style="width:10%;"></td>
											<td class="'.$pdf_border_class.'" style="width:11%;text-align:left;padding-left:5px"><strong>Lens</strong></td>
											<td class="'.$pdf_border_class.'" style="width:11%;text-align:left;padding-left:5px"><strong>Power</strong></td>
											<td class="'.$pdf_border_class.'" style="width:11%;text-align:left;padding-left:5px"><strong>Cyl</strong></td>
											<td class="'.$pdf_border_class.'" style="width:12%;text-align:left;padding-left:5px"><strong>Axis</strong></td>
											<td class="'.$pdf_border_class.'" style="width:5%;text-align:left;padding-left:5px"><strong>Used</strong></td>
											<td class="'.$pdf_border_class.'" style="width:10%;text-align:left;padding-left:5px"></td>
											<td class="'.$pdf_border_class.'" style="width:10%;text-align:left;font-size:11px;padding-left:5px">'.$pdf_predict_sel.'</td>
											<td class="'.$pdf_border_class.'" style="width:10%;text-align:left;padding-left:5px"></td>
											<td class="'.$pdf_border_class.' bdrRght" style="width:10%;padding-left:5px"></td>
										</tr>'.$pdf_arra_lens.'	
									</table>';		
						?>
						<div class="sxdtbx">
							<div class="table-responsive">
								<table id="tbl_lens" class="table">
									<tr>
										<td class="bluhds">Lens</td>
										<td colspan="9"><strong>PREPLAN LENS (TRADITIONAL IOL) DIFFERS FROM PRIMARY LENS</strong></td>
									</tr>
									<tr>
										<td class="blutab">&nbsp;</td>
										<td><strong>LENS</strong></td>
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
														<option value="Holiday- I / II"  <?php echo (strpos($el_predict_sel, "Holiday- I / II")!==false) ? "selected" : "" ;?>>Holiday- I / II</option>
													</select>
												</div>	
											</div>
											
										</td>
										<td><strong>ACD/AL(%) </strong></td>	
										<td><strong>S/P CRS </strong></td>	
									</tr>
									<?php 
										//Printing Lens rows
										$pdf_arra_lens = '';
										$pdf_arr_lens_count = 1;
										foreach($sx_obj->arr_lens as $k => $lens_type){
											$lens = "el_lens".$lens_type;
											$power = "el_power".$lens_type;
											$cyl = "el_cyl".$lens_type;
											$axis = "el_axis".$lens_type;
											$used = "el_used".$lens_type;
											
											$targt = "el_targt".$lens_type;
											$acd = "el_acd".$lens_type;
											$sp = "el_sp".$lens_type;
											$crs = "el_crs".$lens_type;
											
											if(!empty($id_chart_sx_plan_sheet)){
												$sql= " SELECT * FROM chart_sps_lens WHERE id_chart_sx_plan_sheet ='".$id_chart_sx_plan_sheet."' AND lens_type='".$lens_type."' ";
												$row=sqlQuery($sql);
												if($row!=false){				
													$$lens = $row["lens_name"];
													$$power = $row["lens_pwr"];
													$$cyl = $row["lens_cyl"];
													$$axis = $row["lens_axis"];
													$$used = $row["lens_used"];
													
													$$targt = $row["lens_target"];
													$$acd = $row["lens_acd"];
													$$sp = $row["lens_sp"];
													$$crs = $row["lens_crs"];
												}
											}
											
											//bg color row
											$css_tr_bgc =  !empty($$used) ? " class=\"hylight\" " : "";
												
										?>
										
											<tr <?php echo $css_tr_bgc; ?>>
												<td class="blutab"><?php echo $lens_type;?></td>
												<td><input type="text" id="<?php echo $lens;?>" name="<?php echo $lens;?>" value="<?php echo $$lens;?>" class="form-control"></td>
												<td><input type="text" id="<?php echo $power;?>" name="<?php echo $power;?>" value="<?php echo $$power;?>" class="form-control"></td>
												<td><input type="text" id="<?php echo $cyl;?>" name="<?php echo $cyl;?>" value="<?php echo $$cyl;?>" class="form-control"></td>
												<td><input type="text" id="<?php echo $axis;?>" name="<?php echo $axis;?>" value="<?php echo $$axis;?>" class="form-control"></td>
												<td align="center">
													<div class="checkbox">
														<input type="checkbox" id="<?php echo $used;?>" name="<?php echo $used;?>" value="1" <?php echo !empty($$used) ? "checked" : "";?>>
														<label for="<?php echo $used;?>"></label>	
													</div>
												</td>
												<td><input type="text" id="<?php echo $targt;?>" name="<?php echo $targt;?>" value="<?php echo $$targt;?>" class="form-control"></td>
												<td><input type="text" id="<?php echo $acd;?>" name="<?php echo $acd;?>" value="<?php echo $$acd;?>" class="form-control"></td>
												<td><input type="text" id="<?php echo $sp;?>" name="<?php echo $sp;?>" value="<?php echo $$sp;?>" class="form-control"></td>
												<td><input type="text" id="<?php echo $crs;?>" name="<?php echo $crs;?>" value="<?php echo $$crs;?>" class="form-control"></td>				
											</tr>

										<?php
											//PDF Code
											if(!empty($$used))
											{
												$pdf_used_val = 'Yes';
												$pdf_used_bg = ' hylight';
											}else{
												$pdf_used_val = '';
												$pdf_used_bg = '';
											}
											
											//If want to show border on every side of the column use this code
											$pdf_arr_lens_classname = $pdf_border_class;
									
											$pdf_arra_lens .= '
											<tr>
												<td class="pd pl5 bdrlft '.$pdf_arr_lens_classname.$pdf_used_bg.'">'.$lens_type.'</td>
												<td class="pd pl5 '.$pdf_arr_lens_classname.'">'.$$lens.'</td>
												<td class="pd pl5 '.$pdf_arr_lens_classname.'">'.$$power.'</td>
												<td class="pd pl5 '.$pdf_arr_lens_classname.'">'.$$cyl.'</td>
												<td class="pd pl5 '.$pdf_arr_lens_classname.'">'.$$axis.'</td>
												<td class="pd pl5 '.$pdf_arr_lens_classname.$pdf_used_bg.'">'.$pdf_used_val.'</td>
												<td class="pd pl5 '.$pdf_arr_lens_classname.'">'.$$targt.'</td>
												<td class="pd pl5 '.$pdf_arr_lens_classname.'">'.$$acd.'</td>
												<td class="pd pl5 '.$pdf_arr_lens_classname.'">'.$$sp.'</td>
												<td class="pd pl5 '.$pdf_arr_lens_classname.' bdrRght">'.$$crs.'</td>				
											</tr>';
											$pdf_arr_lens_count++;
										}
									?>	
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
													
													//PDF Code
													$pdf_arr_asti_as_classname = $pdf_border_class;
													
													$pdf_arr_asti_as .= '
														<tr>
															<td class="bdrlft pd '.$pdf_arr_asti_as_classname.'" style="width:20%;vertical-align:baseline">'.$asti_source.'</td>
															<td class="pd '.$pdf_arr_asti_as_classname.'" style="width:20%;vertical-align:baseline">'.$$magni.'</td>';
															if($asti_source=="Coma Max (u)" || $asti_source=="CCT (u)" || $asti_source=="OCTM FT (u)"){
																if($asti_source=="Coma Max (u)"){
																	$pdf_arr_asti_as .= '<td class="pd '.$pdf_arr_asti_as_classname.' bdrRght" colspan="3" rowspan="3" style="width:60%;border-top:1px solid #C0C0C0;vertical-align:baseline">'.$el_asti_com.'</td>';
																}
															}else{
															if(!empty($$magni_used))
															{
																$pdf_magni_used = 'Yes';
															}else{
																$pdf_magni_used = '';
															}
															
															if(!empty($$axis_used))
															{
																$pdf_axis_used = 'Yes';
															}else{
																$pdf_axis_used= '';
															}
															
															
															$pdf_arr_asti_as .='<td class="pd '.$pdf_arr_asti_as_classname.'" style="width:20%;vertical-align:baseline">'.$pdf_magni_used.'</td>
															<td class="pd '.$pdf_arr_asti_as_classname.'" style="width:20%;vertical-align:baseline">'.$$axis.'</td>
															<td class="pd '.$pdf_arr_asti_as_classname.' bdrRght" style="width:20%;vertical-align:baseline">'.$pdf_axis_used.'</td>';
														
													} 
													$pdf_arr_asti_as .= '</tr>'; 
													$pdf_arr_asti_as_count++;
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
												<td colspan="3">
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
												<td >Arc 2 Angel(&deg;)</td>
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
												<td >
													<div class="checkbox">
														<input type="checkbox" id="el_plan_anterior" name="el_plan_anterior" value="Anterior" <?php echo !empty($el_plan_anterior) ? "checked" : "" ;?>>
														<label for="el_plan_anterior">Anterior</label>	
													</div>
												 </td>
												<td >
													<div class="checkbox">
														<input type="checkbox" id="el_plan_insratromal" name="el_plan_insratromal" value="Insratromal" <?php echo !empty($el_plan_insratromal) ? "checked" : "" ;?>>
														<label for="el_plan_insratromal">Insratromal</label>	
													</div>
												</td>
											</tr>

											<tr>
												<td >Incision Axis</td>
												<td colspan="3"><input type="text" id="el_plan_incision_axis" name="el_plan_incision_axis" value="<?php echo $el_plan_incision_axis;?>" class="form-control"></td>
											</tr>	
										</table>
										
										<div class="toric_wrapper">
											<table id="tbl_toric" align="center" cellpadding="0" cellspacing="0" class="table assplntabl">
												<tr>
													<td>IOL Model</td>
													<td>Power</td>
													<td>Axis</td>
													<td></td>
												</tr>
												<?php
													$pdf_chart_sx_plan_sheet = '';
													if(!empty($id_chart_sx_plan_sheet)){
														$sql = "SELECT * FROM chart_sps_ast_plan_tpa where id_chart_sx_plan_sheet='".$id_chart_sx_plan_sheet."' ";
														$rez =  sqlStatement($sql);					
														for($i=1;$row=sqlFetchArray($rez);$i++){
															
															$fun = ($i==1) ?  "addToric()" : " delToric('".$i."') " ;
															$fun_s = ($i==1) ? "plu.png" : "min.png";
															
															$el_toric=$row["toric_model"];
															$el_power=$row["power"];
															$el_axis=$row["axis"];
															echo"
															<tr>
																<td><input type=\"text\" id=\"el_toric".$i."\" name=\"el_toric".$i."\" value=\"".$el_toric."\" class=\"form-control\"></td>
																<td><input type=\"text\" id=\"el_power".$i."\" name=\"el_power".$i."\" value=\"".$el_power."\" class=\"form-control\"></td>
																<td><input type=\"text\" id=\"el_axis".$i."\" name=\"el_axis".$i."\" value=\"".$el_axis."\" class=\"form-control\"></td>
																<td onclick=\"".$fun."\"><img src=\"".$library_path."/images/".$fun_s."\" /></td>
															</tr>
															";
															$pdf_chart_sx_plan_sheet .='<tr>
																<td class="bdrlft pd '.$pdf_border_class.'">'.$el_toric.'</td>
																<td class="'.$pdf_border_class.' pd">'.$el_power.'</td>
																<td class="'.$pdf_border_class.' pd">'.$el_axis.'</td>
																<td class="'.$pdf_border_class.' pd bdrRght"></td>
															</tr>'; 
														}
													}
														
														//
													if($i==1||empty($id_chart_sx_plan_sheet)){	
												?>
														<tr>
															<td><input type="text" id="el_toric1" name="el_toric1" value="" class="form-control"></td>
															<td><input type="text" id="el_power1" name="el_power1" value="" class="form-control"></td>
															<td><input type="text" id="el_axis1" name="el_axis1" value="" class="form-control"></td>
															<td onclick="addToric()"><img src="<?php echo $library_path ?>/images/plu.png" alt=""/></td>
														</tr>
												<?php
													}
												
													//PDF Code
													//First table is the Astigmatism Assessment Section and second table is Astigmatism Plan Section in PDF
													if(!empty($el_plan_anterior))
													{
														$pdf_plan_anterior = 'Yes';
													}else{
														$pdf_plan_anterior = '';
													}
													
													if(!empty($el_plan_insratromal))
													{
														$pdf_plan_insratromal = 'Yes';
													}else{
														$pdf_plan_insratromal = '';
													}
													
													$pdf_html .='
														<table id="Astigmatism Assessment" style="width:100%;border-collapse:collapse;font-size:12px">
															<tr>
																<td colspan="5" style="wdith:100%;vertical-align:baseline" class="tb_dataHeader pd bgcolor">Astigmatism Assessment</td>
															</tr>
															<tr>
																<td class="bdrlft pd '.$pdf_border_class.'" style="width:20%;vertical-align:baseline"><strong>Astigmatism Source</strong></td>
																<td class="pd '.$pdf_border_class.'" style="width:20%;vertical-align:baseline"><strong>Magnitude (Diopters)</strong></td>
																<td class="pd '.$pdf_border_class.'" style="width:20%;vertical-align:baseline"><strong>Magnitude Used</strong></td>
																<td class="pd '.$pdf_border_class.'" style="width:20%;vertical-align:baseline"><strong>Axis (Degrees)</strong></td>
																<td class="pd '.$pdf_border_class.' bdrRght" style="width:20%;vertical-align:baseline"><strong>Axis Used</strong></td>
															</tr>'.$pdf_arr_asti_as.'
														</table>
														
														<table id="con_tbl_asti_plan" style="width:100%;border-collapse:collapse;font-size:12px">
															<tr>
																<td colspan="4" style="width:100%;vertical-align:baseline" class="tb_dataHeader pd bgcolor">Astigmatism Plan</td>
															</tr>
															<tr>
																<td class="bdrlft pd '.$pdf_border_class.'" style="width:25%;vertical-align:baseline"><strong>Femto</strong></td>
																<td class="'.$pdf_border_class.' bdrRght pd" colspan="3" style="width:75%;vertical-align:baseline">'.$el_plan_femto.'</td>
															</tr>
															<tr>
																<td class="bdrlft pd '.$pdf_border_class.'" style="width:25%;vertical-align:baseline"><strong>AK#</strong></td>
																<td class="'.$pdf_border_class.' pd" style="width:25%;vertical-align:baseline">'.$el_plan_ak.'</td>
																<td class="'.$pdf_border_class.' pd" style="width:25%;vertical-align:baseline"></td>
																<td class="'.$pdf_border_class.' pd bdrRght" style="width:25%;vertical-align:baseline"></td>
															</tr>
															<tr>
																<td class="bdrlft '.$pdf_border_class.' pd" style="width:25%;vertical-align:baseline"><strong>AK# 1 Length</strong></td>
																<td class="'.$pdf_border_class.' pd" style="width:25%;vertical-align:baseline">'.$el_plan_ak1_len.'</td>
																<td class="'.$pdf_border_class.' pd" style="width:25%;vertical-align:baseline"><strong>AK# 2 Length</strong></td>
																<td class="'.$pdf_border_class.' pd bdrRght" style="width:25%;vertical-align:baseline">'.$el_plan_ak2_len.'</td>
															</tr>
															<tr>
																<td class="bdrlft '.$pdf_border_class.' pd" style="width:25%;vertical-align:baseline"><strong>AK# 1 Axis(&deg;)</strong></td>
																<td class="'.$pdf_border_class.' pd" style="width:25%;vertical-align:baseline">'.$el_plan_ak1_axis.'</td>
																<td class="'.$pdf_border_class.' pd" style="width:25%;vertical-align:baseline"><strong>Arc 2 Angle(&deg;)</strong></td>
																<td class="'.$pdf_border_class.' pd bdrRght" style="width:25%;vertical-align:baseline">'.$el_plan_arc2_axis.'</td>
															</tr>
															<tr>
																<td class="bdrlft '.$pdf_border_class.' pd" style="width:25%;vertical-align:baseline"><strong>AK# 1 Depth(%)</strong></td>
																<td class="'.$pdf_border_class.' pd" style="width:25%;vertical-align:baseline">'.$el_plan_ak1_depth.'</td>
																<td class="'.$pdf_border_class.' pd" style="width:25%;vertical-align:baseline"><strong>Arc 2 Depth(%)</strong></td>
																<td class="'.$pdf_border_class.' pd bdrRght" style="width:25%;vertical-align:baseline">'.$el_plan_ak2_depth.'</td>
															</tr>
															<tr>
																<td class="bdrlft '.$pdf_border_class.' pd" style="width:25%;vertical-align:baseline"><strong>Optical Zone</strong></td>
																<td class="'.$pdf_border_class.' pd" style="width:25%;vertical-align:baseline">'.$el_plan_opt_zone.'</td>
																<td class="'.$pdf_border_class.' pd" style="width:25%;vertical-align:baseline"><strong>Anterior </strong> '.$pdf_plan_anterior.'</td>
																<td class="'.$pdf_border_class.' pd bdrRght" style="width:25%;vertical-align:baseline"><strong>Insratromal </strong> '.$pdf_plan_insratromal.'</td>
															</tr>
															<tr>
																<td class="bdrlft '.$pdf_border_class.' pd" style="width:25%;vertical-align:baseline"><strong>IOL Model</strong></td>
																<td class="'.$pdf_border_class.' pd" style="width:25%;vertical-align:baseline"><strong>Power</strong></td>
																<td class="'.$pdf_border_class.' pd" style="width:25%;vertical-align:baseline"><strong>Axis</strong></td>
																<td class="'.$pdf_border_class.' pd bdrRght" style="width:25%;vertical-align:baseline"><strong></strong></td>
															</tr>'.$pdf_chart_sx_plan_sheet.'
														</table>
													';
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
										$pdf_html .='
											<table id="tbl_sx_plan" style="width:100%;font-size:12px;border-collapse:collapse">
												<tr>
													<td class="bdrlft '.$pdf_border_class.'" style="width:23%;vertical-align:baseline"><strong>Sx Planning - Hooks:</strong> '.$el_sx_pln_hook.'</td>
													<td class="'.$pdf_border_class.'" style="width:21%;vertical-align:baseline"><strong>Flomax Cocktail:</strong> '.$el_flomx_cocktail.'</td>
													<td class="'.$pdf_border_class.'" style="width:18%;vertical-align:baseline"><strong>Trypan Blue:</strong> '.$el_trypan_blue.'</td>
													<td class="'.$pdf_border_class.'" style="width:12%;vertical-align:baseline"><strong>LRI:</strong> '.$el_lri.'</td>
													<td class="'.$pdf_border_class.'" style="width:14%;vertical-align:baseline"><strong>FEMTO:</strong> '.$el_femto.'</td>
													<td class="'.$pdf_border_class.' bdrRght" style="width:12%;vertical-align:baseline"><strong>ECP:</strong> '.$pdf_ecp.'</td>
												</tr>
												<tr>
													<td class="bdrlft pd bdrBtmRght" colspan="6" style="width:100%;vertical-align:baseline"><strong>Comments:</strong> '.$el_sx_pln_com.'</td>
												</tr>
											</table>';
											
											//All Previous Tests PDF
											$pdf_dos_IOL_MASTER = $pdf_dos_ASCAN = $pdf_dos_OCT = $pdf_dos_TOPOGRAPHY = $pdf_dos_VF = '';
											
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
											
											
											$pdf_html .='	
												<table id="dv_drop_down_tests" style="width:100%;border-collapse:collapse;font-size:12px">
													<tr>
														<td style="width:100%" colspan="5" class="tb_dataHeader pd bgcolor"><strong>All Previous Tests</strong></td>
													</tr>
													
													<tr>
														<td class="bdrlft '.$pdf_border_class.' " style="width:20%;vertical-align:baseline">'.$pdf_dos_IOL_MASTER.'</td>
														<td class="'.$pdf_border_class.' " style="width:20%;vertical-align:baseline">'.$pdf_dos_ASCAN.'</td>
														<td class="'.$pdf_border_class.' " style="width:20%;vertical-align:baseline">'.$pdf_dos_OCT.'</td>
														<td class="'.$pdf_border_class.' " style="width:20%;vertical-align:baseline">'.$pdf_dos_TOPOGRAPHY.'</td>
														<td class="'.$pdf_border_class.' bdrRght" style="width:20%;vertical-align:baseline">'.$pdf_dos_VF.'</td>
													</tr>
													
													<tr>
														<td class="bdrlft '.$pdf_border_class.' bdrbtm" style="width:20%;vertical-align:baseline"><strong> IOL Master</strong> </td>
														<td class="'.$pdf_border_class.' bdrbtm" style="width:20%;vertical-align:baseline"><strong> A-scan</strong></td>
														<td class="'.$pdf_border_class.' bdrbtm" style="width:20%;vertical-align:baseline"><strong>OCT</strong></td>
														<td class="'.$pdf_border_class.' bdrbtm" style="width:20%;vertical-align:baseline"><strong>Topography</strong></td>
														<td class="'.$pdf_border_class.' bdrBtmRght" style="width:20%;vertical-align:baseline"><strong>VF</strong></td>
													</tr>	
												</table>
										';
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
								<input type="submit" name="el_btn_save" id="form_save_btn" value="Save" class="btn btn-success">
							<?php } ?>	
								<input type="button" name="el_btn_print" value="Print" class="btn btn-success" id="printSxBtnId" onClick="print_sx_plan('<?php echo $sx_obj->sx_plan_id; ?>');">
								<input type="button" name="el_btn_cancel" value="Cancel" class="btn btn-danger" onclick="window.close();">	
						</div>
					</div>
				</div>
				<!-- Prev. plan sheet modal -->
			<!--	<div id="div_sx_pop_up" class="modal fade" role="dialog">
				  <div class="modal-dialog">

					<!-- Modal content-->
				<!--	<div class="modal-content">
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
				<input type="hidden" name="get_html" value="<?php echo htmlspecialchars($pdf_html); ?>">
			</div>
		</form>  -->	
<?php
$pdf_html .= "</page>";
$rand=rand(0,500);
$htmlFlName = 'Sx_Plan_Sheet_'.$_SESSION['authId'].'_'.$rand;
file_put_contents(data_path().'iOLink/'.$htmlFlName.'.html',$css.$pdf_html);

$iolinkDirPath = data_path().'iOLink/';	
$patientDir = "/PatientId_".$pid;
//Create patient directory
if(!is_dir($iolinkDirPath.$patientDir)){		
	mkdir($iolinkDirPath.$patientDir);
}
$pdfFileName = 'Sx_Plan_Sheet.pdf';
$pdfFilePath = urldecode($iolinkDirPath.$patientDir.'/'.$pdfFileName);
$arrProtocol = (explode("/",$_SERVER['SERVER_PROTOCOL']));
$arrPathPart = pathinfo($_SERVER['PHP_SELF']);
$arrPathPart = explode("/",($arrPathPart['dirname']));

$dir = explode('/',$_SERVER['HTTP_REFERER']);
//print_r($_SERVER);
//exit;
$httpPro = $dir[0];
$httpHost = $dir[2];
$httpfolder = $dir[3];
$ip = $_SERVER['REMOTE_ADDR'];


$myHTTPAddress = $httpPro.'//'.$myInternalIP.'/'.$web_RootDirectoryName.'/library/html_to_pdf/iolinkMakePdf.php';

$data1 = "";
$curNew = curl_init();
$urlPdfFile = $myHTTPAddress."?copyPathIolink=$pdfFilePath&pdf_name=$pdfFilePath&name=$htmlFlName";

curl_setopt($curNew,CURLOPT_URL,$urlPdfFile);
curl_setopt ($curNew, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt ($curNew, CURLOPT_SSL_VERIFYPEER, false); 
curl_setopt($curNew, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curNew, CURLOPT_FOLLOWLOCATION, true); 
$data1 = curl_exec($curNew);
curl_close($curNew); 
?>