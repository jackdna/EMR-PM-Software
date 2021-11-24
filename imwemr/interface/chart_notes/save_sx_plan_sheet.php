<?php
// file : save_sx_plan_sheet.php
require_once(dirname(__FILE__).'/../../config/globals.php');

require($GLOBALS['incdir']."/chart_notes/chart_globals.php");
require($GLOBALS['srcdir']."/classes/work_view/wv_functions.php");
require($GLOBALS['srcdir']."/classes/work_view/wv_functions_new.php");
require($GLOBALS['srcdir']."/classes/work_view/sx_plan.class.php");

$provider_id=$_SESSION["authId"];
$patient_id=$_SESSION["patient"];

$sx_plan_dos = '';
if(isset($_REQUEST['new_sx_dos']) && empty($_REQUEST['new_sx_dos']) == false){	//If date is new update record
	$sx_plan_dos = getDateFormatDB($_REQUEST['new_sx_dos']);
}

//Sx Class Object
$patient_id = $_SESSION['patient'];
$sx_pl_id = (isset($_REQUEST['elem_plan_dos']) && empty($_REQUEST['elem_plan_dos']) == false) ? getDateFormatDB($_REQUEST['elem_plan_dos']) : '';
$sxObj = New Sx_Plan($patient_id,$sx_pl_id);

$save_status = false;
//$_POST["elem_sx_plan_sheet_id"];
$exam_date= $_POST["elem_examDate"];
$form_id=$_POST["elem_form_id"];
//pre($_REQUEST);die;
$sx=$_POST["el_sx_type"];
$flomax=$_POST["el_flomax"];
$pt_choices=$_POST["el_pt_choice"];
$prv_sx_proc=$_POST["el_prv_sx"];
$mank_eye=$_POST["el_mank_eye"];
$refraction=$_POST["el_refraction"];
$domi_eye=$_POST["el_domi"];
$oth_eye_ref=$_POST["el_othr_eye_ref"];
$iol_mas_k_recommed=$_POST["el_iol_recomd"];
$iol_mas_k_comment=$_POST["el_iol_desc"];
$iol_mas_k_lens_summary=$_POST["el_lens_sle_summary"];
$surg_dt=getDateFormatDB($_POST["el_date_surgery"]);
$surg_time=$_POST["el_time_surgery"];
$k_flat=$_POST["el_k_flat"];
$k_steep=$_POST["el_k_steep"];
$k_axis=$_POST["el_k_axis"];
$k_cyl=$_POST["el_k_cyl"];
$oth_k_flat=$_POST["el_ok_flat"];
$oth_k_steep=$_POST["el_ok_steep"];
$oth_k_axis=$_POST["el_ok_axis"];
$oth_k_cyl=$_POST["el_ok_cyl"];
$surgeon_id=$_POST["el_surgeon_id"];
$prim_proc=$_POST["el_proc_prim"];
$sec_proc=$_POST["el_proc_sec"];
$pe_dt=getDateFormatDB($_POST["el_prev_eye_date"]);
$pe_lens=$_POST["el_prev_eye_lens"];
$pe_power=$_POST["el_prev_eye_power"];
$pe_cyl=$_POST["el_prev_eye_cyl"];
$pe_axis=$_POST["el_prev_eye_axis"];
$pe_va=$_POST["el_prev_eye_va"];
$pe_ora_res=$_POST["el_prev_eye_ora_res"];
$pe_toric_pos=$_POST["el_prev_eye_torpos"];
$pe_comments=$_POST["el_prev_eye_comm"];
$pe_method=$_POST["el_meth_lens"];
$pe_ora=$_POST["el_ora"];
$pe_version=$_POST["el_version"];
$pe_mbn=$_POST["el_mbn"];
$pe_prem_lens=$_POST["el_prem_lens"];
$pe_cci=$_POST["el_cci"];
$pe_pacy=$_POST["el_pachy"];
$pe_w2w=$_POST["el_w2w"];
$pe_pupil_mx=$_POST["el_pupilmx"];
$pe_pupil_dilated=$_POST["el_pupildilated"];
$pe_cap_mx=$_POST["el_cupmx"];
$ap_femto =$_POST["el_plan_femto"];
$ap_ak=$_POST["el_plan_ak"];
$ap_ak1_len=$_POST["el_plan_ak1_len"];
$ap_ak2_len=$_POST["el_plan_ak2_len"];
$ap_ak1_axis=$_POST["el_plan_ak1_axis"];
$ap_arc2_angel=$_POST["el_plan_arc2_axis"];
$ap_ak1_dpth=$_POST["el_plan_ak1_depth"];
$ap_arc2_dpth=$_POST["el_plan_ak2_depth"];
$ap_opt_zone=$_POST["el_plan_opt_zone"];
$ap_anterior=$_POST["el_plan_anterior"];
$ap_instratromal=$_POST["el_plan_insratromal"];
$ap_incision_axis=$_POST["el_plan_incision_axis"];
$sx_plan_hooks=$_POST["el_sx_pln_hook"];
$flomax_cocktail=$_POST["el_flomx_cocktail"];
$trypan_blue=$_POST["el_trypan_blue"];
$lri=$_POST["el_lri"];
$femto=$_POST["el_femto"];
$ecp=$_POST["el_ecp"];
$sx_pln_com=$_POST["el_sx_pln_com"];
$asti_com=$_POST["el_asti_com"];
$prev_sx_ocu=$_POST["el_prev_sx_ocu"];
$prev_sx_sys=$_POST["el_prev_sx_sys"];
$pe_site=$_POST["el_prev_eye_site"];
$mank_ref=$_POST["el_mank_ref"];
$po_procedure=$_POST['sch_proc'];
$po_option=implode(',',$_POST['op_option']);

$str_predict_sel = "";
if(isset($_POST['predict_sel_val']) && count($_POST['predict_sel_val']) > 0){
	$str_predict_sel = implode(',',$_POST['predict_sel_val']);
}
/* if(!empty($_POST["el_predict_brt"])){ $str_predict_sel .= $_POST["el_predict_brt"].",";  }
if(!empty($_POST["el_predict_srk"])){ $str_predict_sel .= $_POST["el_predict_srk"].",";  }
if(!empty($_POST["el_predict_holi"])){ $str_predict_sel .= $_POST["el_predict_holi"].",";  } */

$k_given = "";
if(!empty($_POST["el_k_given_manual"])){ $k_given = $_POST["el_k_given_manual"];  }
if(!empty($_POST["el_k_given_auto"])){ $k_given = $_POST["el_k_given_auto"];  }
if(!empty($_POST["el_k_given_oct"])){ $k_given = $_POST["el_k_given_oct"];  }
if(!empty($_POST["el_k_given_iol"])){ $k_given = $_POST["el_k_given_iol"];  }

//selected images
$sx_imgs = "";
$ar_img_checked = $_POST["el_img_checked"];
if(count($ar_img_checked)>0){
	$sx_imgs = implode(",", $ar_img_checked);
}

//selected dd ids
$dd_id_iol = $_POST["el_ids_iol"];
$dd_id_ascan= $_POST["el_ids_ascan"];
$dd_id_oct= $_POST["el_ids_oct"];
$dd_id_topo= $_POST["el_ids_topo"];
$dd_id_vf= $_POST["el_ids_vf"];

// chart_sx_plan_sheet
$id_chart_sx_plan_sheet="";
$sql = "SELECT id FROM chart_sx_plan_sheet WHERE patient_id='".$patient_id."' AND id = '".$_POST['elem_sx_plan_sheet_id']."' AND del_status='0' ";
$row = sqlQuery($sql);
if($row != false){
	$id_chart_sx_plan_sheet=$row["id"];
}


//--

$sql_in = "INSERT INTO chart_sx_plan_sheet  SET 
		provider_id='".$provider_id."',
		patient_id='".$patient_id."',
		form_id='0',
		exam_date='".$exam_date."',
	";
$sql_up = "UPDATE chart_sx_plan_sheet  SET ";

$sql_c="
sx_plan_dos='".imw_real_escape_string($sx_plan_dos)."',
sx='".imw_real_escape_string($sx)."',
flomax='".imw_real_escape_string($flomax)."',
pt_choices='".imw_real_escape_string($pt_choices)."',
prv_sx_proc='".imw_real_escape_string($prv_sx_proc)."',
mank_eye='".imw_real_escape_string($mank_eye)."',
surgery='".imw_real_escape_string($surgery)."',
surg_dt='".imw_real_escape_string($surg_dt)."',
surg_time='".imw_real_escape_string($surg_time)."',
surgeon_id='".imw_real_escape_string($surgeon_id)."',
".(isset($_POST['iol_lock'])?"iol_lock='".imw_real_escape_string($_POST['iol_lock'])."',":"")."
refraction='".imw_real_escape_string($refraction)."',
domi_eye='".imw_real_escape_string($domi_eye)."',
oth_eye_ref='".imw_real_escape_string($oth_eye_ref)."',
k_flat='".imw_real_escape_string($k_flat)."',
k_steep='".imw_real_escape_string($k_steep)."',
k_axis='".imw_real_escape_string($k_axis)."',
k_cyl='".imw_real_escape_string($k_cyl)."',
oth_k_flat='".imw_real_escape_string($oth_k_flat)."',
oth_k_steep='".imw_real_escape_string($oth_k_steep)."',
oth_k_axis='".imw_real_escape_string($oth_k_axis)."',
oth_k_cyl='".imw_real_escape_string($oth_k_cyl)."',
prim_proc='".imw_real_escape_string($prim_proc)."',
sec_proc='".imw_real_escape_string($sec_proc)."',
iol_mas_k_recommed='".imw_real_escape_string($iol_mas_k_recommed)."',
iol_mas_k_comment='".imw_real_escape_string($iol_mas_k_comment)."',
lens_sle_summary='".imw_real_escape_string($iol_mas_k_lens_summary)."',
pe_dt='".imw_real_escape_string($pe_dt)."',
pe_lens='".imw_real_escape_string($pe_lens)."',
pe_power='".imw_real_escape_string($pe_power)."',
pe_cyl='".imw_real_escape_string($pe_cyl)."',
pe_axis='".imw_real_escape_string($pe_axis)."',
pe_va='".imw_real_escape_string($pe_va)."',
pe_ora_res='".imw_real_escape_string($pe_ora_res)."',
pe_toric_pos='".imw_real_escape_string($pe_toric_pos)."',
pe_comments='".imw_real_escape_string($pe_comments)."',
pe_method='".imw_real_escape_string($pe_method)."',
pe_ora='".imw_real_escape_string($pe_ora)."',
pe_version='".imw_real_escape_string($pe_version)."',
pe_mbn='".imw_real_escape_string($pe_mbn)."',
pe_prem_lens='".imw_real_escape_string($pe_prem_lens)."',
pe_cci='".imw_real_escape_string($pe_cci)."',
pe_pacy='".imw_real_escape_string($pe_pacy)."',
pe_w2w='".imw_real_escape_string($pe_w2w)."',
pe_pupil_mx='".imw_real_escape_string($pe_pupil_mx)."',
pe_pupil_dilated='".imw_real_escape_string($pe_pupil_dilated)."',
pe_cap_mx='".imw_real_escape_string($pe_cap_mx)."',
ap_femto='".imw_real_escape_string($ap_femto)."',
ap_ak='".imw_real_escape_string($ap_ak)."',
ap_ak1_len='".imw_real_escape_string($ap_ak1_len)."',
ap_ak2_len='".imw_real_escape_string($ap_ak2_len)."',
ap_ak1_axis='".imw_real_escape_string($ap_ak1_axis)."',
ap_arc2_angel='".imw_real_escape_string($ap_arc2_angel)."',
ap_ak1_dpth='".imw_real_escape_string($ap_ak1_dpth)."',
ap_arc2_dpth='".imw_real_escape_string($ap_arc2_dpth)."',
ap_opt_zone='".imw_real_escape_string($ap_opt_zone)."',
ap_anterior='".imw_real_escape_string($ap_anterior)."',
ap_instratromal='".imw_real_escape_string($ap_instratromal)."',
ap_incision_axis='".imw_real_escape_string($ap_incision_axis)."',
sx_plan_hooks='".imw_real_escape_string($sx_plan_hooks)."',
flomax_cocktail='".imw_real_escape_string($flomax_cocktail)."',
trypan_blue='".imw_real_escape_string($trypan_blue)."',
lri='".imw_real_escape_string($lri)."',
femto='".imw_real_escape_string($femto)."',
ecp='".imw_real_escape_string($ecp)."',
sx_pln_com='".imw_real_escape_string($sx_pln_com)."',
asti_com='".imw_real_escape_string($asti_com)."',
prev_sx_ocu='".imw_real_escape_string($prev_sx_ocu)."',
prev_sx_sys='".imw_real_escape_string($prev_sx_sys)."',
predict_sel='".imw_real_escape_string($str_predict_sel)."',
pe_site='".imw_real_escape_string($pe_site)."',
k_given='".imw_real_escape_string($k_given)."',
mank_ref='".imw_real_escape_string($mank_ref)."',
sx_imgs ='".imw_real_escape_string($sx_imgs)."',
dd_id_iol='".imw_real_escape_string($dd_id_iol)."',
dd_id_ascan='".imw_real_escape_string($dd_id_ascan)."',
dd_id_oct='".imw_real_escape_string($dd_id_oct)."',
dd_id_topo='".imw_real_escape_string($dd_id_topo)."',
dd_id_vf='".imw_real_escape_string($dd_id_vf)."',
po_proc_id='$po_procedure',
po_eva_map='$po_option'
";

$sql_w=" WHERE id='".$id_chart_sx_plan_sheet."' ";

//*
if(!empty($id_chart_sx_plan_sheet)){ //UPDATE
	$r = sqlQuery($sql_up.$sql_c.$sql_w);
		$save_status = true;
}else{//INSERT	
	$id_chart_sx_plan_sheet = sqlInsert($sql_in.$sql_c);	
	$save_status = true;
}
//*/

//Others --  chart_sps_lens
/*
$_POST["el_lensPrimary"]
$_POST["el_powerPrimary"]
$_POST["el_cylPrimary"]
$_POST["el_axisPrimary"]
$_POST["el_targtPrimary"]
$_POST["el_acdPrimary"]
$_POST["el_spPrimary"]
$_POST["el_crsPrimary"]
$_POST["el_lensBackup1"]
$_POST["el_powerBackup1"]
$_POST["el_cylBackup1"]
$_POST["el_axisBackup1"]
$_POST["el_targtBackup1"]
$_POST["el_acdBackup1"]
$_POST["el_spBackup1"]
$_POST["el_crsBackup1"]
$_POST["el_lensBackup2"]
$_POST["el_powerBackup2"]
$_POST["el_cylBackup2"]
$_POST["el_axisBackup2"]
$_POST["el_targtBackup2"]
$_POST["el_acdBackup2"]
$_POST["el_spBackup2"]
$_POST["el_crsBackup2"]
$_POST["el_lensBackup3"]
$_POST["el_powerBackup3"]
$_POST["el_cylBackup3"]
$_POST["el_axisBackup3"]
$_POST["el_targtBackup3"]
$_POST["el_acdBackup3"]
$_POST["el_spBackup3"]
$_POST["el_crsBackup3"]
*/

//Saving IOL Lenses 
$iolLensArr = array();
for($i = 1; $i < 5; $i++){
	if(count($sxObj->lensFields) > 0){
		foreach($sxObj->lensFields as $lensKey => $lensVal){
			//Get Hidden Fields
			$hidField = 'EL_IOL_'.$lensKey.'_'.$i.'_hidden';
			$hidFieldValue = (isset($_REQUEST[$hidField]) && empty($_REQUEST[$hidField]) == false) ? $_REQUEST[$hidField] : '';
			
			if(empty($hidFieldValue) == false) $iolLensArr[$i]['ID'] = $hidFieldValue;
			
			$fieldsName = 'EL_IOL_'.$lensKey.'_'.$i;
			$fieldvalue = (isset($_REQUEST[$fieldsName]) && empty($_REQUEST[$fieldsName]) == false) ? $_REQUEST[$fieldsName] : '';
			
			$iolLensArr[$i][$lensKey] = $fieldvalue;
		}
	}
}

if(empty($id_chart_sx_plan_sheet) == false){
	$sql = imw_query("DELETE FROM chart_sps_lens WHERE id_chart_sx_plan_sheet='".$id_chart_sx_plan_sheet."' ");
	$sql = imw_query("DELETE FROM chart_sps_ast_plan_tpa WHERE id_chart_sx_plan_sheet='".$id_chart_sx_plan_sheet."' ");
}

if(count($iolLensArr) > 0){
	foreach($iolLensArr as &$obj){
		$type = $obj['Type'];
		$power = $obj['Pwr'];
		$cyl = $obj['Cyl'];
		$axis = $obj['Axis'];
		$used = (isset($obj['Used']) && strtolower($obj['Used']) == 'on') ? '1' : '';
		$targt = $obj['Target'];
		$acd = $obj['Acd'];
		$sp = $obj['Sp'];
		$crs = $obj['Crs'];
		$lensName = $sxObj->iol_lenses[$type]['lensType'];
		
		$id_chart_sps_lens = '';
		
		$sql = "SELECT id FROM chart_sps_lens where id_chart_sx_plan_sheet='".$id_chart_sx_plan_sheet."'  AND lens_type='".$lens_type."' ";
		$row =  sqlQuery($sql);
		if($row!=false){
			$id_chart_sps_lens = $row["id"];
		}
		
		$sql_in = " INSERT INTO chart_sps_lens SET id_chart_sx_plan_sheet='".$id_chart_sx_plan_sheet."', ";
		$sql_up = " UPDATE chart_sps_lens SET ";	
		$sql_w = " WHERE id='".$id_chart_sps_lens."' ";
		
		$sql_con="
			lens_type='".imw_real_escape_string($type)."',
			lens_name='".imw_real_escape_string($lensName)."',
			lens_pwr='".imw_real_escape_string($power)."',
			lens_cyl='".imw_real_escape_string($cyl)."',
			lens_axis='".imw_real_escape_string($axis)."',
			lens_used='".imw_real_escape_string($used)."',
			lens_target='".imw_real_escape_string($targt)."',
			lens_acd='".imw_real_escape_string($acd)."',
			lens_sp='".imw_real_escape_string($sp)."',
			lens_crs='".imw_real_escape_string($crs)."',
			prov_id='".imw_real_escape_string($_REQUEST['el_surgeon_id'])."'
		";	
		
		if(empty($id_chart_sps_lens)){		
			if(!empty($type)){
			$sql = $sql_in.$sql_con;
			$r = sqlInsert($sql);
			}
		}else{
			if(!empty($type)){		
			$sql = $sql_up.$sql_con.$sql_w;
			$r = sqlQuery($sql);
			}else{
				$sql = "DELETE FROM chart_sps_lens WHERE id='".$id_chart_sps_lens."' ";
				$r = sqlQuery($sql);
			}
		}
	}
}
//Other --chart_sps_ast_assess
/*
$_POST["el_magniGlassesOldest"]
$_POST["el_axisGlassesOldest"]
$_POST["el_magniManifestRefraction"]
$_POST["el_axisManifestRefraction"]
$_POST["el_magniIOLMPreop"]
$_POST["el_axisIOLMPreop"]
$_POST["el_magniIOLMRepeatorOld"]
$_POST["el_axisIOLMRepeatorOld"]
$_POST["el_magniTopographyConsult"]
$_POST["el_axisTopographyConsult"]
$_POST["el_magniTopographyPreop"]
$_POST["el_axisTopographyPreop"]
$_POST["el_magniVerion"]
$_POST["el_axisVerion"]
$_POST["el_magniComaMax(u)"]
$_POST["el_axisComaMax(u)"]
$_POST["el_magniCCT(u)"]
$_POST["el_axisCCT(u)"]
$_POST["el_magniOCTMFT(u)"]
$_POST["el_axisOCTMFT(u)"]
*/

$arr_asti_as=array("Glasses Oldest","Manifest Refraction", "IOLM Preop", "IOLM Repeat or Old", "Topography Consult", "Topography Preop", "Verion", "Coma Max (u)", "CCT (u)", "OCTM FT (u)");
foreach($arr_asti_as as $k => $asti_source){

$asti_source_var=str_replace(" ","", $asti_source);		
$magni = $_POST["el_magni".$asti_source_var];
$magni_used = $_POST["el_magni_used".$asti_source_var];
$axis = $_POST["el_axis".$asti_source_var];
$axis_used = $_POST["el_axis_used".$asti_source_var];
	
	//
	$id_chart_sps_ast_assess=0;
	$sql = "SELECT id FROM chart_sps_ast_assess where id_chart_sx_plan_sheet='".$id_chart_sx_plan_sheet."'  AND ast_source='".imw_real_escape_string($asti_source)."' ";
	$row =  sqlQuery($sql);
	if($row!=false){
		$id_chart_sps_ast_assess=$row["id"];
	}

	$sql_in = " INSERT INTO chart_sps_ast_assess SET id_chart_sx_plan_sheet='".$id_chart_sx_plan_sheet."', ";
	$sql_up = " UPDATE chart_sps_ast_assess SET ";	
	$sql_w = " WHERE id='".$id_chart_sps_ast_assess."' ";

	$sql_con="ast_source='".imw_real_escape_string($asti_source)."',
			magni_diopter='".imw_real_escape_string($magni)."',
			magni_used='".imw_real_escape_string($magni_used)."',
			axis='".imw_real_escape_string($axis)."',
			axis_used='".imw_real_escape_string($axis_used)."'
			";
			
	if(empty($id_chart_sps_ast_assess)){
		if(!empty($magni) || !empty($axis)){
		$sql = $sql_in.$sql_con;
		$r = sqlInsert($sql);
		}
	}else{
		if(!empty($magni) || !empty($axis)){
		$sql = $sql_up.$sql_con.$sql_w;
		$r = sqlQuery($sql);
		}else{
			$sql = "DELETE FROM chart_sps_ast_assess WHERE id='".$id_chart_sps_ast_assess."' ";
			$r = sqlQuery($sql);
		}
	}

}

//Other -- //chart_sps_ast_plan_tpa
/*
$_POST["el_toric"]
$_POST["el_power"]
$_POST["el_axis"]
*/

foreach($sxObj->arr_lens as $iolKey => $iolVal){
	$lensID = $toric = $power = $axis = '';
	
	$lensID = $_REQUEST['iolPlan_'.$iolKey];
	$toric = $_REQUEST['el_toric_'.$iolKey];
	$power = $_REQUEST['el_power_'.$iolKey];
	$axis = $_REQUEST['el_axis_'.$iolKey];
	
	$sql_in = " INSERT INTO chart_sps_ast_plan_tpa SET id_chart_sx_plan_sheet='".$id_chart_sx_plan_sheet."', ";
	$sql_up = " UPDATE chart_sps_ast_plan_tpa SET ";
	
	$sql_con="			
			lens_type='".imw_real_escape_string($lensID)."',
			toric_model='".imw_real_escape_string($toric)."',
			power='".imw_real_escape_string($power)."',
			axis='".imw_real_escape_string($axis)."',
			indx='".imw_real_escape_string($iolKey)."',
			prov_id='".imw_real_escape_string($_REQUEST['el_surgeon_id'])."'
		";
		
	$chkQry = imw_query('SELECT id from chart_sps_ast_plan_tpa WHERE id_chart_sx_plan_sheet = "'.$id_chart_sx_plan_sheet.'" AND indx = '.$iolKey.' AND prov_id = '.$_REQUEST['el_surgeon_id'].' ');
	if($chkQry && imw_num_rows($chkQry) > 0){
		$rowFetch = imw_fetch_assoc($chkQry);
		$qry = $sql_up.$sql_con.' WHERE id = '.$rowFetch['id'].'';
	}else{
		$qry = $sql_in.$sql_con;
	}
	
	if(empty($lensID) == false) imw_query($qry);
}

for($i=1;$i<20;$i++){
	if(isset($_POST["el_toric".$i]) && isset($_POST["el_power".$i]) && isset($_POST["el_axis".$i])){
		$tmp_toric = $_POST["el_toric".$i];
		$tmp_power = $_POST["el_power".$i];
		$tmp_axis = $_POST["el_axis".$i];
		
		//
		$id_chart_sps_ast_plan_tpa=0;
		$sql = "SELECT id FROM chart_sps_ast_plan_tpa where indx='".imw_real_escape_string($i)."' AND id_chart_sx_plan_sheet='".$id_chart_sx_plan_sheet."' ";
		$row =  sqlQuery($sql);
		if($row!=false){
			$id_chart_sps_ast_plan_tpa=$row["id"];
		}
		
		$sql_in = " INSERT INTO chart_sps_ast_plan_tpa SET id_chart_sx_plan_sheet='".$id_chart_sx_plan_sheet."', ";
		$sql_up = " UPDATE chart_sps_ast_plan_tpa SET ";	
		$sql_w = " WHERE id='".$id_chart_sps_ast_plan_tpa."' ";
		
		$sql_con="			
			toric_model='".imw_real_escape_string($tmp_toric)."',
			power='".imw_real_escape_string($tmp_power)."',
			axis='".imw_real_escape_string($tmp_axis)."',
			indx='".imw_real_escape_string($i)."'
		";
		
		if(empty($id_chart_sps_ast_plan_tpa)){
			if(!empty($toric_model) || !empty($tmp_power)|| !empty($tmp_axis)){
				$sql = $sql_in.$sql_con;
				$r = sqlInsert($sql);
			}
		}else{
			if(!empty($toric_model) || !empty($tmp_power)|| !empty($tmp_axis)){
				$sql = $sql_up.$sql_con.$sql_w;
				$r = sqlQuery($sql);
			}else{
				$sql = "DELETE FROM chart_sps_ast_plan_tpa WHERE id='".$id_chart_sps_ast_plan_tpa."' ";
				$r = sqlQuery($sql);
			}
		}
		
	}else{
		break;
	}
}

//update toric images folder --
$ar_toric_img_id = $_POST["el_toric_img_id"];
if(count($ar_toric_img_id)>0){
	$tmp = array_unique($ar_toric_img_id);
	$str_tmp = implode(", ", $tmp);	
	//
	$sql = "UPDATE toric_pt_images SET chart_sx_plan_sheet_id='".$id_chart_sx_plan_sheet."' WHERE id IN (".$str_tmp.") AND chart_sx_plan_sheet_id='0'  ";	 
	$row=sqlQuery($sql);
}
//--

// close
 $echo_win_redirect = "<html><head><script>window.location.href = 'sx_planning_sheet.php?get_sx_id=".$id_chart_sx_plan_sheet."'</script></head></html>";
echo $echo_win_redirect; exit();
/* 
if($save_status){
	echo '<script>top.fmain.location.href=\''.$GLOBALS['webroot'].'/interface/chart_notes/sx_planning_sheet.php?get_sx_dos='.$sx_plan_dos.'&save_status=Record updated successfully\'</script>';
}
 */
?>