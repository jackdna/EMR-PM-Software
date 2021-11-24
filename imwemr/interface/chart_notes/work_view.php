<?php
//header("location: main_page.html");exit();
require_once(dirname(__FILE__).'/../../config/globals.php');

//
//error_reporting(E_ALL & ~E_NOTICE);
//ini_set("display_errors",1);
//

require($GLOBALS['incdir']."/chart_notes/chart_globals.php");
require($GLOBALS['srcdir']."/classes/work_view/wv_functions.php");
require($GLOBALS['srcdir']."/classes/work_view/wv_functions_new.php");
require($GLOBALS['srcdir']."/classes/class.tests.php");
//Testing with default patient ID
/*
$_SESSION["form_id"]=""; unset($_SESSION["form_id"]);
$_SESSION["finalize_id"]=""; unset($_SESSION["finalize_id"]);
$_SESSION["patient"]= "67894"; //"135789"; //
*/

//check chart db
$osv = new SaveFile();
$osv->cr_wvexams_db();

$objTests				= new Tests;

//Chart Pt Lock variables
$oChartPtLock = new ChartPtLock();
$scva_flgWorkView = 1;
list($elem_per_vo,$lockUsrId, $chart_showLock, $scva_lock_exam)=$oChartPtLock->get_view_access($scva_flgWorkView);
$htm_pt_chart_lock=($chart_showLock) ? $oChartPtLock->getPtChartLockHtml($lockUsrId, $scva_lock_exam) : "";

//Check if patient is empty and redirect it patient search
wv_check_session();

//Unset Session of encounter for super bill
$_SESSION['cn_enc']="";

//Defaults --
$ado_display = "NO";
$elem_ptVisit = "";
$elem_ptTesting = "Empty";

$isExistingPatient=true;
$elem_masterFinalize = 0;
$elem_masterRecordValidity = 1;
$elem_masterIsSuperBilled = 0;
$elem_masterPtMedHxReviewed = 0;
$elem_masterPtMedHxShowed = 0;
$elem_masterFinalizerId = 0;
$elem_chartTemplateId = 0;
$elem_chartTempName = "Comprehensive";
$elem_masterpurge_status = 0;
$elem_masterFinalDate = "";
//Chart Notes Default
//$elem_dos = $elem_dmDate = date("m-d-Y");
$curDate = $elem_dos = wv_formatDate(date("Y-m-d"));
$curDate_ymd = date("Y-m-d");
$elem_masterUpdateDate = date('Y-m-d H:i:s');
$elem_activeFormId = 0;
$isMemo = 0;
$isAutoFinalized=0;
$elem_masterProviderId=0;
$enc_icd10="";
$el_visit="";
$dt_old_activ_cn="";
$z_js_dt_frmt=wv_dt_format_js();
$titleRxHxRvd = "I personally interviewed and examined the patient and reviewed resident's or fellow's note.  I agree with the history, exam,  assessment and plan as detailed in the resident's or fellow's note.";

//Defaults --

//Admn
$oAdmn = new Admn();

//----User Info --
	$elem_chartOprtrId = $providerId = $check_authId = $_SESSION['authId'];
	$oUsrfun = new User($check_authId);
	$user_type = (isset($_SESSION["user_role"]) && !empty($_SESSION["user_role"])) ? $_SESSION["user_role"] : $_SESSION["logged_user_type"];
	$user_type_cn = $oUsrfun->getUType_cn($user_type);
	$user_name = $oUsrfun->getName(4);
	$user_name_short = trim($oUsrfun->getName(1));
	$usr_img="";


//---- User Info --

//---- Patient - Info --
	$patient_id = $_SESSION["patient"];
	$oPatient=new Patient($patient_id);
	$patientName = $elem_curPatientName = $oPatient->getName(4); //Name

	$ar_pt_chart_info = $oPatient->getChartNoteInfo();
	extract($ar_pt_chart_info);

	//Future Appointment
	$oPtSch = new PtSchedule($patient_id);
	$data_future_appointment=$oPtSch->getFutureAppointments();
	$futureSchTesExist = $oPtSch->getFutureSchTestCount();

	//schedule Recalls
	$data_sched_recalls=$oPatient->getSchRecalls();

	//Insuarnce Info
	$oIns = new Insurance();
	$ar_pt_ins_info = $oIns->wv_getInsInfo($caseId);
	extract($ar_pt_ins_info);

	//Has Pt Active Charts
	$hasActChart = $oPatient->getPtLastChart("active");

	//Referring phy
	$strPtRefPhy = $oPatient->getMultiPhy();

	//Advance directive
	$oAdvanceDirective = new AdvanceDirective($patient_id);
	$strPtAdvDir = $oAdvanceDirective->getADInfo();

	//Pt Alert
	$strPtAlert = $oPatient->getPtAlert();

	//is Patient Est
	$oSuperBill = new SuperBill($patient_id);
	$flgPtEst = $oSuperBill->isPatientEstablish($form_id);

//---- Patient - Info --

//---- Chart - Info --

	$oWv = new WorkView(); //chart notes
	$oCn = new ChartNote($patient_id, $form_id);


	//Sx Plan Status
	$btn_sx_plan_stat = '';
	$dt_qry = imw_query("SELECT * FROM chart_sx_plan_sheet WHERE patient_id='".$patient_id."' AND form_id='".$form_id."' AND del_status='0'");
	if(imw_num_rows($dt_qry) > 0){
		$btn_sx_plan_stat = 'active_class';
	}


	//Check FormId and PatientId misMatch
	if(empty($form_id)||empty($patient_id)||empty($_SESSION['authId'])||
		(!empty($form_id)&&!empty($patient_id)&&$oWv->checkMisMatchedPatient($form_id,$patient_id)) || (empty($_SESSION['finalize_id']) && empty($_SESSION['form_id'])) ){

		if(!empty($patient_id)){
			header("location: ".$GLOBALS["web_root"]."/interface/core/set_session.php?set_pid=".$patient_id."&rd2=../chart_notes/work_view.php");

			//trigger_error("Session is not correct.", E_USER_ERROR);

		}else{
			echo  "<html><body>
					<script>top.document.getElementById('fmain').src='".$GLOBALS["web_root"]."/interface/core/index.php?pg=default-page'</script>
				  </body></html>";
		}
		exit;
	}

	//--

	//Hack for HCCS
	$_SESSION['form_id_backup'] = $form_id."-".$finalize_flag;




	//--
	//set patient Id
	$oCn->setPtId($patient_id);

	//DOS - YMD
	$dos_ymd = wv_formatDate($elem_dos,$syr=0,$tm=0, $op="insert");

	//UPDATE entry for iMedicMonitor.
	if($dos_ymd==date('Y-m-d') && $_SESSION['sess_user_switched'] != "no"){
		$objTests->patient_whc_room(1,$dos_ymd);
		patient_monitor_daily("CHART_OPEN");
	}
	//GET PrevId and Next Id --
	$arNxtPrevFId = $oCn->getNxtPrevFid($dos_ymd);
	$varPrevFId = $arNxtPrevFId["Prev"];
	$varNxtFId = $arNxtPrevFId["Nxt"];
	//GET PrevId and Next Id --

	//ICD 10 level --
	if(empty($enc_icd10)){
		$enc_icd10=$oWv->getDefaultICDCode($caseId, $form_id, $patient_id);
	}
	//--

	//Visit type : menu
	$arrPtVisit=$arrPtTesting=array();
	$arrPtVisit=$oAdmn->wv_getPtVisit(0,1);
	//$arrPtTesting=array("Empty", "Gonio", "Disc Photo", "Pachy","VF", "NFA/HRT", "Color Plates", "Other" );
	//$data_visit_testing = wv_getMenuHtmlHidden(array("VISIT"=>$arrPtVisit, "TESTING"=>$arrPtTesting)," id=\"divMenuVisitTest\" ", 2 );


	// chart_left_cc_history ---------
	$oCcHx = new CcHx($patient_id, $form_id);
	$arr_cchx_info = $oCcHx->getFormInfo();
	extract($arr_cchx_info);

	// chart_left_cc_history ---------

	//chart_left_provider_issue ------
	$arr_chart_ocuhx_info = $oCcHx->getChartOcuHxInfo();
	extract($arr_chart_ocuhx_info);

	//Oculer Hx
	$oMedHx =  new MedHx($patient_id);
	$oMedHx->setFormId($form_id);
	$oMedHx->processOcuMeds($finalize_flag);
	//Allergies
	//$pt_allergy_cls = $oMedHx->getAllergies("title",2);
	//chart_left_provider_issue ------

	//Chart Assessment and plan --
	$arr_vis_func = array("NE","0","10","20","30","40","50","60","70","80","90","100");

	//Signature ----------
	$oSign = new Signature($form_id);
	$arrSigns = $oSign->getSignInfo();

	//

	$oChartap = new ChartAP($patient_id, $form_id);
	$ar_chart_ap_info = $oChartap->getFormInfo($finalize_flag, $enc_icd10);
	extract($ar_chart_ap_info);

	//Chart Assessment and plan --

	//Attestation --
	$htm_scribe_attes = $oSign->getAttestationDiv();

	//Follow Up ------------
	//$oFu = new Fu($patient_id, $form_id);
	//list($lenFu, $arrFuVals) = $oFu->getFormInfo();

	//--

	//Care giver color -----
	$ar_care_giver_colors = $oCn->getCareGiverColors();

	//Get Template Procedures ---
	$oChartTemp = new ChartTemp();
	$arr_chart_temp_info = $oChartTemp->getChartTemplateSettings($elem_chartTemplateId);
	$chrttemp_MenuHtmlHidden = $oChartTemp->getChartTemplateOptions($hasActChart);
	extract($arr_chart_temp_info);

	//---

	//Patient Visit - Test
	$dataPtVisitTest = "";
	if(!empty($elem_masterPtVisit)){ $dataPtVisitTest .= $elem_masterPtVisit; }
	if(!empty($elem_masterTesting)){ if(!empty($dataPtVisitTest)){ $dataPtVisitTest .=" - "; }  $dataPtVisitTest .= $elem_masterTesting; }
	$el_visit = $dataPtVisitTest;
	//--

	// Old Active Chart Note --
	if($finalize_flag == 0 && $curDate_ymd > $dos_ymd){
		$dt_old_activ_cn = $elem_dos;
	}
	// Old Active Chart Note --

	//Vision
	$flg_temp_vision=0;
	if(in_array("Vision",$arrTempProc)||in_array("Distance",$arrTempProc)||in_array("Near",$arrTempProc)||in_array("AR",$arrTempProc)||
		in_array("AK",$arrTempProc)||in_array("PC 1",$arrTempProc)||in_array("PC 2",$arrTempProc)||in_array("PC 3",$arrTempProc)||
		in_array("MR 1",$arrTempProc)||in_array("MR 2",$arrTempProc)||in_array("MR 3",$arrTempProc)||in_array("BAT",$arrTempProc)||
		in_array("ICP Color Plate",$arrTempProc)||in_array("Stereopsis",$arrTempProc)||in_array("Diplopia",$arrTempProc)||in_array("W4Dot",$arrTempProc)||
		in_array("Retinoscopy",$arrTempProc)||in_array("Exophthalmometer",$arrTempProc)||in_array("Cycloplegic Retinoscopy",$arrTempProc) ||in_array("Lasik",$arrTempProc)){
		$oVision = new Vision($patient_id, $form_id);
		$data_vision_section = $oVision->getVisionSection();
		$flg_temp_vision=1;
	}

	//CVF
	if(in_array("CVF",$arrTempProc)){
		$oCVF = new CVF($patient_id, $form_id);
		$data_cvf_section = $oCVF->getWorkViewSummery();
		$flg_temp_vision=1;
	}

	//AmslerGrid
	if(in_array("Amsler Grid",$arrTempProc)){
		$oAmslerGrid = new AmslerGrid($patient_id, $form_id);
		$data_amsler_section = $oAmslerGrid->getWorkViewSummery();
		$flg_temp_vision=1;
	}

	//Contact lens
	$show_contactLens = (in_array("Contact Lens",$arrTempProc)) ? true : false;

	//Memo -----
	$datamemo="";
	if(!empty($isMemo)){
		$oChartMemo = new ChartMemo($patient_id, $form_id);
		$datamemo = $oChartMemo->getChartMemo();
	}
	//Memo -----

	//Objective Note
	$flg_obj_note = 0;
	if(in_array("Objective Notes",$arrTempProc)){
		$oObjectiveNote = new ObjectiveNote($patient_id, $form_id);
		$elem_objNotes = $oObjectiveNote->get_objective_note();
		$flg_obj_note = 1;
	}

	//physician notes->
	$ophynote = new PhyPtNotes($patient_id);
	$phy_note_exists = $ophynote->isPtPnoteExists();

	//Pt Test to form id
	$oPtTest = new PtTest($patient_id, $form_id);
	$is_test_uninterpreted = $oPtTest->pt_test_uninterpreted();
	if(!empty($isReviewable) || empty($finalize_flag)){
		$oPtTest->call_attachFormId2Test($dos_ymd);
	}

//---- Chart - Info --


//Med Hx Status + Other Icons status
/*$o_core_notifications = new core_notifications();
$noti_status_all = $o_core_notifications->get_notification_status();
$noti_genhealth = $o_core_notifications->get_genhealth_noti();*/
$oiconbar = new IconBar($patient_id, $form_id);
$ar_icon_st = $oiconbar->get_icons_status();

//Show Pt. Med Hx Reviewed
//&& ( $elem_masterPtMedHxReviewed != "1" ):: gewirtz request
$htm_gen_health="";
if( (!empty($_SESSION['res_fellow_sess']) || $user_type_cn == 1) && ( $elem_masterPtMedHxShowed != "1" ) &&
	( $elem_masterPtMedHxReviewed != "1" || $web_RootDirectoryName=="theeyecenter" ) &&
	(($finalize_flag != 1) || ( $isReviewable == true))  ){
	$flg_genHealthDivList=1;
	$htm_gen_health= general_health_div($patient_id);

	if($elem_per_vo != "1" && (!isset($_SESSION['res_fellow_sess']) || empty($_SESSION['res_fellow_sess']))){
		$oCn->setPtMedHxReviewd();
	}
}


//EPost ------------
$oEpost = new Epost($patient_id);
$htm_epost = $oEpost->getEposts();
//EPost ------------

// POE ALERT ---------
$str_call_poe="";
$oPoe = new Poe($patient_id);
$str_poe_html = $oPoe->showAlert("2","1");
if(!empty($str_poe_html) && strpos($str_poe_html,"hidden")===false){
	$str_call_poe=" $(\"#poeModal\").modal('show'); ";
}
// POE ALERT ---------

//Alerts --
$htm_pt_alerts = $oWv->get_pt_alert($patient_id, $form_id);
//---

//role change
$str_role_change="";
if($user_type=="3" || $user_type=="13"){$str_role_change= "$(\"#legendDiv .clickable\").bind(\"click\",function(){ role_change_options(this); })";}

//Pt iportal
$pt_iprtl_alert = $oWv->get_iportal_req_changes_alert($patient_id);

//STANDARDS OF CARE
$ar_soc = $oAdmn->get_standrad_of_care($el_soc);

//--hidden --
$arr_hidden_vals=array();
//htm
$chrttemp_HtmlHidden="";
$chrttemp_HtmlHidden.=$chrttemp_MenuHtmlHidden;
$chrttemp_HtmlHidden.=$elem_insuranceCaseName_id_alt;
$chrttemp_HtmlHidden.=$htm_epost;
$chrttemp_HtmlHidden.=$str_poe_html;
$chrttemp_HtmlHidden.=$htm_pt_alerts;

//values
$elem_isFormReviewable=($isReviewable) ? "1" : "0" ;
$elem_per_vo=($elem_per_vo == "1") ? "1" : "";
if(!empty($memo)){ $elem_get_memo = $memo; }else if(!empty($isMemo)){ $elem_get_memo = $isMemo; }

$arr_hidden_vals["getText"]=array();
$arr_hidden_vals["getSelValue"]=array();
$arr_hidden_vals["elem_saveForm"]=array("MainTable");
$arr_hidden_vals["elem_masterId"]=array($form_id);
$arr_hidden_vals["elem_masterFinalize"]=array($elem_masterFinalize);
$arr_hidden_vals["elem_masterRecordValidity"]=array($elem_masterRecordValidity);
$arr_hidden_vals["elem_masterCaseId"]=array($caseId);
$arr_hidden_vals["elem_masterProviderId"]=array($elem_masterProviderId, "ev"=>"onChange='setThisChangeStatus(this);'");
$arr_hidden_vals["elem_masterEncounterId"]=array($encounterId);
$arr_hidden_vals["elem_masterPtVisit"]=array($elem_masterPtVisit, "ev"=>"onChange='setThisChangeStatus(this);getTechMandatory(this);'");
$arr_hidden_vals["elem_masterTesting"]=array($elem_masterTesting, "ev"=>"onChange='setThisChangeStatus(this);'");
$arr_hidden_vals["elem_masterUpdateDate"]=array($elem_masterUpdateDate, "ev"=>"data-date-show='".date('m-d-Y')."'");
$arr_hidden_vals["elem_isFormFinalized"]=array($finalize_flag);
$arr_hidden_vals["elem_isR1Form"]=array($relNum);
$arr_hidden_vals["elem_isFormReviewable"]=array($elem_isFormReviewable);
$arr_hidden_vals["elem_masterIsSuperBilled"]=array($elem_masterIsSuperBilled);
$arr_hidden_vals["elem_per_vo"]=array($elem_per_vo);
$arr_hidden_vals["elem_masterFinalizerId"]=array($elem_masterFinalizerId);
$arr_hidden_vals["elem_masterpurge_status"]=array($elem_masterpurge_status);
$arr_hidden_vals["elem_masterFinalDate"]=array($elem_masterFinalDate);
$arr_hidden_vals["elem_masterPatientId"]=array($patient_id);
$arr_hidden_vals["elem_masterProvIds"]=array($elem_masterProvIds);
$arr_hidden_vals["elem_closePtChart"]=array(0);
$arr_hidden_vals["elem_ptGo2"]=array();
$arr_hidden_vals["elem_ptSrchPatient"]=array();
$arr_hidden_vals["elem_ptSrchFindBy"]=array();
$arr_hidden_vals["elem_clPopUpSaved"]=array();
$arr_hidden_vals["elem_activeFormId"]=array($elem_activeFormId);
$arr_hidden_vals["closeWorkView"]=array();
$arr_hidden_vals["elem_chartOprtrId"]=array($elem_chartOprtrId);//$check_authId
$arr_hidden_vals["elem_ischartEdited"]=array($elem_ischartEdited);
$arr_hidden_vals["elem_get_memo"]=array($elem_get_memo);
$arr_hidden_vals["elem_dos"]=array($elem_dos); //, "ev"=>"onChange='onChangeDos(this)'"
$arr_hidden_vals["elem_ptVisit"]=array($elem_masterPtVisit, "ev"=>"onChange='prcsVisit(this);'","id"=>"elem_ptVisit_chk");
$arr_hidden_vals["elem_ptTesting"]=array($elem_masterTesting,"ev"=>"onChange='top.fmain.setPtTesting(this.value);'");
$arr_hidden_vals["elem_ptTemplate"]=array($elem_chartTempName, "ev"=>"onChange='setPtTemplate(this);'");
$arr_hidden_vals["hidd_print_plan_id"]=array();
$arr_hidden_vals["hidd_final_flag"]=array($finalize_flag);
$arr_hidden_vals["hidd_formId"]=array($form_id);
$arr_hidden_vals["hidd_print_plan_obj_id"]=array();
$arr_hidden_vals["strPtRefPhy"]=array($strPtRefPhy);
$arr_hidden_vals["elem_curPhysicianId"]=array($elem_curPhysicianId);
$arr_hidden_vals["elem_signatureNum"]=array($elem_signatureNum);
$arr_hidden_vals["el_commentsForPatient_prv"]=array($commentsForPatient);
$arr_hidden_vals["el_commentsForPatient_nm_prv"]=array($commentsForPatient_nm_prv);
$arr_hidden_vals["el_commentsForPatient_Dt_prv"]=array($commentsForPatient_Dt);
$arr_hidden_vals["el_elem_notes_prv"]=array($elem_notes);
$arr_hidden_vals["el_elem_notes_nm_prv"]=array($el_elem_notes_nm_prv);
$arr_hidden_vals["el_elem_notes_Dt_prv"]=array($elem_notes_Dt);
$arr_hidden_vals["el_elem_transition_notes_prv"]=array($elem_transition_notes);
$arr_hidden_vals["el_elem_transition_notes_nm_prv"]=array($el_elem_transition_notes_nm_prv);
$arr_hidden_vals["el_elem_transition_notes_Dt_prv"]=array($elem_transition_notes_Dt);
$arr_hidden_vals["sess_pt_id"]=array($patient_id);
$arr_hidden_vals["sess_form_id"]=array($form_id);
$arr_hidden_vals["cryfwd_form_id"]=array($cryfwd_form_id);
$arr_hidden_vals["el_chronicProbs"]=array($el_chronicProbs);
$arr_hidden_vals["elem_asmt_dxcode_id"]=array();

//TechMandatory --
if($user_type_cn == 3){
	$tmp = $oAdmn->get_tech_mandatory($elem_masterPtVisit);
	$arr_hidden_vals = array_merge($arr_hidden_vals, $tmp);
}
//End TechMandatory --

if($GLOBALS["showResHxRevwd"] == "1"){
	$tmp = ($elem_resiHxReviewd==1) ? "1" : "0" ;
	$arr_hidden_vals["elem_resiHxReviewd"]=array($tmp);
}

$str_hidden_vals=wv_getHtmlHiddenFields($arr_hidden_vals);


//--hidden --

//inline js and css --
$ProClr = User::getProviderColors();//	getProviderColors();
$flgRefDig = Facility::getRefDigSetting();
$isReviewable_bin = ($isReviewable) ? "1" : "0";
$isEditable_bin = ($isEditable) ? "1" : "0";
$iscur_user_vphy_bin = ($iscur_user_vphy) ? "1" : "0";
$show_finalize_btn = ($user_type==11 && core_check_privilege(array("priv_chart_finalize")) == false) ? 1 : $finalize_flag ;

//Dss
$str_dss = isDssEnable();

$str_head_data="
	<style>
        .contact_lens_save_box
        {
            left:480px;
            top:230px;
            width:312px;
            position:absolute;
            background-color:white;
            border:solid 1px #81A2C9;
            z-index:1005px;
            display:none;
            z-index:100;
            color:black;
            min-height:100px;
            max-height:450px;
        }
        _:-ms-fullscreen, :root .contact_lens_save_box
        {
            max-height:435px;
        }
    </style>
	<script>
	var z_hpi_bullet_format=\"".(!empty($GLOBALS['HPI_FORMAT']) ? 1:0)."\"; //Test
	var z_js_dt_frmt = \"".$z_js_dt_frmt."\";
	var REF_PHY_FORMAT = \"".$GLOBALS['REF_PHY_FORMAT']."\";
	var allergytimer = '';
	var zPath = \"".$GLOBALS['rootdir']."\";
	var elem_per_vo = \"".$elem_per_vo."\", per_prgdel=\"".$_SESSION["sess_privileges"]["priv_purge_del_chart"]."\";
	var sess_pt = \"".$_SESSION['patient']."\";
	var rootdir = \"".$GLOBALS['rootdir']."\";
	var mywindow;
	var isExistingPatient = \"".($isExistingPatient ? '1':'0')."\";
	var user_type = \"".$user_type_cn."\"; // CN User Type
	var logged_user_type = \"".$user_type."\"; //Real User Type
	var finalize_flag = \"".(($finalize_flag) ? 1:0)."\";
	var isReviewable = \"".(($isReviewable) ? 1 : 0)."\";
	var flg_printMr = \"".$_POST['defaultValsVis']."\";
	var authUserID = \"".$check_authId."\";
	var authUserNM = \"".$user_name_short."\";
	var varPrevFId=".$varPrevFId.";
	var varNxtFId=".$varNxtFId.";
	var phy_note_exists='".$phy_note_exists."';
	var asmt_sep_multi_dx_code = \"".$GLOBALS['asmt_sep_multi_dx_code']."\";
	var flg_phy_view=\"".$_SESSION['flg_phy_view']."\", show_finalize_btn=\"".$show_finalize_btn."\";

	var imgPath = \"".$GLOBALS['webroot']."\";
	var lenAssess = ".( (empty($lenAssess) || ($lenAssess < "5")) ? "5" : $lenAssess ).";
	var isFormChanged=0;

	var alwaysDocFU=\"".((!empty($arrMrPersonnal[$GLOBALS['alwaysDocFU']]))? $GLOBALS['alwaysDocFU']:"")."\";
	var def_clindr_sign=\"".$GLOBALS["def_cylinder_sign"]."\";
	var ar_ld_exm_js='".base64_encode(gzcompress($strLoadExams, 9))."';
	var arrTempProc_js='".base64_encode(gzcompress($arrTempProc_js, 9))."';

	var ProClr=".$ProClr.";
	var flgPhyRefSet=\"".(($flgPhyRefSet) ? "1":"0")."\";
	var flgRefGivenOnly=\"".(($flgRefGivenOnly) ? "1":"0")."\";

	var flgRefDig=\"".$flgRefDig."\";
	var ssFollowPhy=\"".$_SESSION['res_fellow_sess']."\";
	var z_printbtnRx=\"".$GLOBALS['PrintBtnRx']."\";

	var arrPtTemplateTmp = ".json_encode($arrPtTemplateTmp).";
	var arrPtVisit = ".json_encode(array_keys($arrPtVisit)).";
	var hasActChart = '".$hasActChart."';
	var flg_genHealthDivList='".$flg_genHealthDivList."';
	var is_test_uninterpreted ='".$is_test_uninterpreted."';
	var isERPPortalEnabled = '".isERPPortalEnabled()."';
	var postpone_pghd=\"".$_SESSION['POSTPONEPGHD']."\";

	//RVS --
	var complaintHead = new Array(".(($complaintHeadDB)? "'".str_replace(",","','",$complaintHeadDB)."'" : "").");
	var selectedHead = new Array(".( ($selectedHeadDB)? "'".str_replace(",","','",$selectedHeadDB)."'" : "" ).");
	var complaint1 = new Array(".( ($complaint1StrDB)? "'".str_replace(",","','",$complaint1StrDB)."'" : "" ).");
	var complaint2 = new Array(".( ($complaint2StrDB)? "'".str_replace(",","','",$complaint2StrDB)."'" : "" ).");
	var complaint3 = new Array(".( ($complaint3StrDB)? "'".str_replace(",","','",$complaint3StrDB)."'" : "" ).");
	var titleHeadArr = new Array(".( "'".str_replace(",","','",$titleHeadDB)."'").");
	var usersArr = ".json_encode(get_operators()).";
	var isDssEnable = '".$str_dss."';
	//jq ---
	$(document).ready(function () {

		//load Exams
		$.get('onload_wv.php',
						{ 'elem_action':'GetExamSummary','oe':'".$oe_prms."',
							'allexm': ar_ld_exm_js,
							'artemp': arrTempProc_js,
							'enc_icd10':'".$enc_icd10."',
							},
						function(data){

							//document.writeln(data);
							//console.log(data);

							$('#pupil').html('');
							var a='', b='';
							for(var x in data){
								a+=x+',';
								$('#'+x).html(data[x]);
								//console.log(x);
								//console.log(data[x]);
							}

							//
							setExamsLoaded(a);

							//
							$(\"[data-toggle=\\\"tooltip\\\"]\").tooltip();

						},
				'json');

		//capture--

		$(\"body\").bind(\"click keyup change mousedown touchstart\", function(e){ handlerMainPageEvents(e) ;});

		//capture--

		//
		//Buttons
		if(top.fmain&&typeof(top.fmain.hideButtons)!='undefined'){top.fmain.hideButtons('".$finalize_flag."','".$form_id."','".$isReviewable_bin."',".
									"'".$isEditable_bin."','".$iscur_user_vphy_bin."','".$elem_masterpurge_status."');}
		//set focus--
		if($(\"#elem_buttonOldChart\").length>0){
			$(\"#elem_buttonOldChart\")[0].focus();
		}else if($(\"#elem_buttonPoe\").length>0){
			$(\"#elem_buttonPoe\")[0].focus();
		}

		setTimeout(function(){

			if(flg_genHealthDivList=='1'){ $(\"#genHealthDiv_wv\").modal('show'); if(isERPPortalEnabled && !postpone_pghd){setTimeout(function(){ load_pghd_chart_reqs(); }, 1000);} } //general health div list

			//make cchx readonly
			if(z_hpi_bullet_format==1){
				rvs_mk_cchx_readonly();
			}//End if

			//
			cn_typeahead();
			cl_ajax_type_ahd_all();

			//vision menu
			vision_event_handler();

			//User Type Color: Color coding ------------
			utElem_setBgColor();

			//Date picker
			$(\".date-pick\" ).datepicker({dateFormat:z_js_dt_frmt});


			".$pt_iprtl_alert."

			".$el_soc_alert."


		},150);

		//load wv title bar
			chrt_showChartInfo();
		//--

		//ICD - 10 DM coding
		sb_checkDiabetes();

		//model
		".$str_call_poe."



		".$str_role_change."

		".$strPtAlert."

		if(finalize_flag=='1'){	if(isReviewable==1){setReviewableFunction();}else{setfinalizedFunction();}	}

	});
	//jq ---

</script>
";
//inline js and css --

##
header('Content-Type: text/html; charset=utf-8');
##
$z_ob_get_clean=dirname(__FILE__)."/view/work_view.php";
include(dirname(__FILE__)."/minfy_inc.php");
?>
