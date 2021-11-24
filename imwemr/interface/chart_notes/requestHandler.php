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
?>
<?php
/*
File: requestHandler.php
Purpose: This file provides general processing functions in work view and other parts in work view.
Access Type : Direct
*/
?>
<?php			
//header("location: main_page.html");exit();
require_once(dirname(__FILE__).'/../../config/globals.php');

//
//error_reporting(E_ALL & ~E_NOTICE);
//ini_set("display_errors",1);
//

require($GLOBALS['incdir']."/chart_notes/chart_globals.php");
require($GLOBALS['srcdir']."/classes/work_view/ChartAP.php");
require($GLOBALS['srcdir']."/classes/work_view/wv_functions.php");



class RequestHandler{
	public $pid, $fid;
	public function __construct($pid, $fid){
		$this->pid = $pid;
		$this->fid = $fid;
	}
	
	function main($finalize_flag){		
		switch($_REQUEST["elem_formAction"]){
			case "CaptureSign":
				$form_id = $_POST["elem_form_id"];
				$oSign = new Signature($form_id, $this->pid);
				$arrSigns = $oSign->captureSign();
			break;
			case "GetSign":
				$form_id=$_POST["fid"];
				$oSign = new Signature($form_id, $this->pid);
				$arrSigns = $oSign->saveWvSign();					
			break;
			case "EmptyEncounterID":
				//Empty EncounterId session for accounting section --
				$_SESSION['encounter_id']="";
				unset($_SESSION['encounter_id']);
			break;
			case "TypeAhead":
				$ota = new TypeAhead();
				$ota->main();
			break;
			case "GetAPDataofAsmt":			
				$srch = urldecode($_GET["srch"]);
				$oUserAp = new UserAp();	
				$arr = $oUserAp->getAssessmentAndPolicies($flgmode="Assessment2", $srch,'','1');
				echo json_encode($arr);
			break;
			case "GetAssessOption":
				$symp = xss_rem($_GET["symp"]);
				if(empty($symp)&&!empty($_GET["symp"])){$symp = $_GET["symp"];}
				$symp = rawurldecode($symp);
				$icd_code_10 = $_GET["icd_code_10"];
				$eye = $_GET["eye"];
				$cnslid = $_GET["cnslid"];
				$dos = $_GET["dos"];
				$oUserAp = new UserAp();	
				$arrRet = $oUserAp->getAssessmentAndPolicies_from_Symp($symp, $icd_code_10, $eye, $cnslid, $dos);			
				echo json_encode($arrRet);				
			break;
			case "setApPolFU":
				$oChartOrders = new ChartOrders($this->pid, $this->fid);
				$oChartOrders->attachOrder2Chart_handler();
				$oUserAp = new UserAp();
				$oUserAp->setApPolFU();
			break;
			case "Cpt":
				$oAdmn = new CPT();
				$oAdmn->find_cpt();
			break;
			
			case "Dx":
				$oSbInfo = new SbInfo($this->pid, $this->fid);
				$oSbInfo->find_dx();
			break;
			case "Md":
				$oAdmn = new Modifier();
				$oAdmn->find_md();
			break;
			
			case "getDxAP";
				$oUserAp = new UserAp();
				$oUserAp->getDxAP();
			break;
			case "todayCharges":
				$oSbInfo = new SbInfo($this->pid, $this->fid);
				$oSbInfo->calc_today_charges();
			break;			
			case "PQRI":				
				$oSbInfo = new SbInfo($this->pid, $this->fid);
				$oSbInfo->autocode();
			break;
			case "getDxFrmAses":				
				$oUserAp = new UserAp();
				$oUserAp->getDxFrmAses();
			break;
			case "sb_getVisitCodeCPTCost":
				$ocpt = new CPT();
				$ocpt->sb_getVisitCodeCPTCost($this->pid);
			break;
			case "checkMultiVisitCode":
				$oSbInfo = new SbInfo($this->pid, $this->fid);
				$oSbInfo->checkMultiVisitCode();
			break;
			case "GetTestCptCodes":
				$opttst = new PtTest($this->pid);
				$opttst->get_sb_codes();
			break;
			case "CHECK_DIABETES":				
				$oFundusExam = new FundusExam($this->pid, $this->fid);
				$oFundusExam->check_diabetese();
			break;
			case "SuperBill_Print":				
				require($GLOBALS['srcdir']."/classes/acc_functions.php");				
				$oSuperBillPrint = new SuperBillPrint($this->pid);
				$oSuperBillPrint->main();
			break;			
			case "GetEmdeonWarnings":
				$oOrders = new Orders();
				$oOrders->get_emdeon_warnings();
			break;
			case "GetPlansofAsmt":
				$oChartAP =  new ChartAP($this->pid, $this->fid);
				$oChartAP->get_plansof_asmt_handler();
			break;
			case "MedAdmnsrd":
				$oChartAP =  new ChartAP($this->pid, $this->fid);
				$oChartAP->med_admnsrd();
			break;
			case "showOrdersInAP":
				$oOrders = new Orders();
				$oOrders->showOrdersInAP();
			break;
			//case "showOrderDetail":				
			//	$this->showOrderDetail();				
			//break;
			//case "GetOrderDetail":
			//	$oOrders = new Orders();
			//	$oOrders->getOrderDetail_Popup($this->pid, $this->fid);
			//break;
			case "updateOrderDetail":
				$oChartAP =  new ChartAP($this->pid, $this->fid);
				$oChartAP->updateOrderDetail();
			break;
			case "showAsHxNew":
				$oChartAP =  new ChartAP($this->pid, $this->fid);
				$oChartAP->showAsHxNew();
			break;
			case "chart_plan_print":
				$oCcHxPrint =  new CcHxPrint($this->pid, $this->fid);
				$oCcHxPrint->chart_plan_print($finalize_flag);
			break;
			case "getTagOptions":
				$oSmartTags =  new SmartTags($this->pid);
				$oSmartTags->getTagOptions($_REQUEST['is_return']);
			break;
			case "print_patient_rx":
				require($GLOBALS['srcdir']."/classes/Functions.php");
				$oVisionPrint =  new VisionPrint($this->pid, $this->fid);
				$oVisionPrint->print_rx($finalize_flag);
			break;
			case "print_mr":
				require($GLOBALS['srcdir']."/classes/Functions.php");
				$oVisionPrint =  new VisionPrint($this->pid, $this->fid);
				$oVisionPrint->print_mr($finalize_flag);
			break;
			case "print_pc":
				require($GLOBALS['srcdir']."/classes/Functions.php");
				$oVisionPrint =  new VisionPrint($this->pid, $this->fid);
				$oVisionPrint->print_pc($finalize_flag);
			break;
			case "addMoreSigns":
				$oSignature =  new Signature($this->fid);
				$oSignature->addMoreSign();
			break;
			case "attach_order_2_chart":
				$oChartOrders = new ChartOrders($this->pid, $this->fid);
				$oChartOrders->attach_order_2_chart_handler2();
			break;
			case "add_future_sch_tests_appoints":				
				$oPtSchedule = new PtSchedule($this->pid);
				$oPtSchedule->add_future_sch_tests_appoints($this->fid);
			break;
			case "checkAdminPass4Lock":
				$oChartPtLock = new ChartPtLock();//with current user id and patientid
				$oChartPtLock->unlock_handler();
			break;
			case "chartNoteTree":					
				$oPtForms = new PtForms($this->pid, $this->fid);
				$oPtForms->main();
			break;
			case "get_cn_progess_notes":					
				$oPtNote = new PrognessNote($this->pid, $this->fid);
				$oPtNote->get_cn_progess_notes_handler();
			break;
			case "loadDraw":
				$oClsDrw = new CLSDrawingData();
				$oClsDrw->load_drawing_data();
			break;
			case "show_med_list6":
				require($GLOBALS['srcdir']."/classes/work_view/wv_functions_new.php");
				echo show_med_list6($this->pid);
			break;
			case "PtAllergy":
				require($GLOBALS['srcdir']."/classes/work_view/wv_functions_new.php");
				echo show_allergies($this->pid);
			break;
			case "PatientChartSearch":
				require($GLOBALS['srcdir']."/classes/work_view/wv_functions_new.php");
				$term = trim($_GET["term"]);
				$html = getPatient_Chart_Search($_SESSION["patient"], $term);
				echo $html;
			break;
			case "setPtMonitorStatus":
				patient_monitor_daily($_GET["stts"]);
			break;
			case "SmartChartDetail":
				$oSmartChartSaver = new SmartChartSaver($this->pid, $this->fid);
				$oSmartChartSaver->get_chart_detail();
			break;
			case "pt_comm":
				require($GLOBALS['srcdir']."/classes/work_view/wv_functions_new.php");
				echo patient_communication($this->pid,$_REQUEST['view'],$_REQUEST['mode'],$_REQUEST);
			break;
			case "recalculateRefSurg":
				$oRefSurg = new RefSurg($this->pid, $this->fid);
				$oRefSurg->recalculateRefSurg_handler();
			break;
			case "getGlucomaGraphAm":
				$oic = new PtIop($this->pid);
				$oic->getGraph();
			break;
			
			case "GetAmendmentInfo":
				$oAmendments = new ChartAmendment($this->pid,$this->fid);
				$oAmendments->getAmendmentInfo();
			break;
			case "add_memo":				
				$oChartMemo = new ChartMemo($this->pid,$this->fid);
				$oChartMemo->add_memo();
			break;
			case "get_refer_phy":
				$oPatient = new Patient($this->pid);
				$strPtRefPhy = $oPatient->getMultiPhy();
				echo $strPtRefPhy;
			break;
			case "sendfax":
				$oConLtr = new ConsultLetter($this->pid,$this->fid);
				$oConLtr->sendfax();
			break;
			case "fax_pdf_creater":
				$oConLtr = new ConsultLetter($this->pid,$this->fid);
				$oConLtr->fax_pdf_creater();
			break;
			case "pt_consult_letters":
				$oConLtr = new ConsultLetter($this->pid,$this->fid);
				$oConLtr->pt_consult_letters();
			break;
			case "sendemail":
				$oConLtr = new ConsultLetter($this->pid,$this->fid);
				$oConLtr->send_email();
			break;
			case "send_fax_log":
				$oConLtr = new ConsultLetter($this->pid,$this->fid);
				$oConLtr->send_fax_log();
			break;
			case "VisPopUp":
				$oVis = new Vision($this->pid,$this->fid);
				$oVis->vision_pop_up();
			break;
			case "showcCChxpop":
				$oCcHx =  new CcHx($this->pid, $this->fid);
				$oCcHx->get_cc_hx_popup($finalize_flag);				
			break;
			case "show_phy_note":
				$ophynote =  new PhyPtNotes($this->pid);
				$ophynote->get_notes_popup();				
			break;
			case "load_Operative_note":				
				$opnote =  new OperativeNote($this->pid, $this->fid);
				$opnote->load_file();				
			break;
			case "addVisionPCMR":				
				$oVis = new Vision($this->pid,$this->fid);
				$oVis->add_vision();			
			break;
			case "show_gen_health":
				require($GLOBALS['srcdir']."/classes/work_view/wv_functions_new.php");
				echo general_health_div($this->pid);
			break;
			case "saveGHSocialData":
				require($GLOBALS['srcdir']."/classes/work_view/wv_functions_new.php");
				echo json_encode(saveGHSocialData($this->pid));
			break;
			case "get_reff_address_v2":
			$phy_ref_name=$_GET["refnm"];
			$str="";			
			//	
			if( !empty($phy_ref_name) ){
				$arr_ref_nm = explode(",", $phy_ref_name);
				$lnm = trim($arr_ref_nm[0]);
				$arr_ref_nm_2 = explode(" ", trim($arr_ref_nm[1]));
				$fnm = trim($arr_ref_nm_2[0]);
				$mnm = trim($arr_ref_nm_2[1]);		
				if(!empty($lnm) && !empty($fnm)){
					$phrse_sql="";
					if(!empty($fnm)){ $phrse_sql.="FirstName='".$fnm."' ";}
					if(!empty($mnm)){ if(!empty($phrse_sql)){$phrse_sql.=" AND ";}  $phrse_sql.="MiddleName='".$mnm."' ";}
					if(!empty($lnm)){ if(!empty($phrse_sql)){$phrse_sql.=" AND ";} $phrse_sql.="LastName='".$lnm."' ";}
					if(!empty($phrse_sql)){
					$sql = "SELECT physician_Reffer_id, Address1, Address2, City, State, ZipCode, physician_phone, physician_fax FROM refferphysician WHERE ".$phrse_sql." LIMIT 0, 1 ";			
					
					$res = imw_query($sql);					
					if(imw_num_rows($res) > 0){
						$row = imw_fetch_assoc($res);
						$physician_Reffer_id = $row['physician_Reffer_id'];
						$str .= $row['Address1'];
						$str .= ($row['Address2'] != "")?", ".$row['Address2']:"";
						$str .= ($row['City'] != "")?", ".$row['City']:"";
						$str .= ($row['State'] != "")?", ".$row['State']:"";
						$str .= ($row['ZipCode'] != "")?" ".$row['ZipCode']:"";
						$str .= ($row['physician_phone'] != "")?"\nPhone:".$row['physician_phone']:"";
						$str .= ($row['physician_fax'] != "")?"\nFax:".$row['physician_fax']:"";						
					}
					}
				}
			}
			echo $str;
			break;
			case "genHealthReviewd":
				$oCcHx =  new CcHx($this->pid, $this->fid);
				$oCcHx->genHealthReviewd($finalize_flag);
			break;
			case "ShowPhyView":
				$owv = new WorkView();
				$owv->enter_phy_view();	
			break;
			case "getTechMandtory":
				$oAdmn = new Admn();
				$oAdmn->get_tech_mandatory($_GET["elem_visitCode"], 1);
			break;
			case "del_goal":
				echo del_pt_goals($_POST['record_id']);
			break;
			case "save_goal":
				echo save_pt_goals($_REQUEST);
			break;
			case "save_health_status":
				echo save_pt_health_status($_REQUEST);
			break;
			case "del_pt_health_status":
				echo del_pt_health_status($_POST['record_id']);
			break;
			case "del_hc":
				echo del_health_concern($_POST['type'],$_POST['record_id']);
			break;
			case "save_hc":
				echo save_health_concern($_REQUEST);
			break;
			case "save_inpatient":
				echo save_inpatient($_REQUEST);
			break;
			case "show_goals_hc"	:
				echo show_goals_hc();
			break;
			case "show_health_status"	:
				echo show_health_status();
			break;
			case "show_inpatient_data"	:
				echo show_inpatient_data();
			break;
			case "CLSAJAXTestDrawing":
				$ocdd =  new PtDrawings($this->pid, $this->fid, $finalize_flag);
				$ocdd->get_test_drawings();		
			break;
			case 'getPtPayer':
				echo print_patient_payer($this->pid, $this->fid);
			break;
			case 'savePtPayer':
				echo save_patient_payer($this->pid, $this->fid, $_REQUEST);
			break;
			case "ChartPrevImage":
				$ow = new WorkView();
				$ow->get_prv_chart_img($this->pid);
			break;
			case "update_icons":
				$ow = new IconBar($this->pid, $this->fid);
				$ow->get_icons_status(1);
			break;
			case "get_adv_directive":
				$oAdvanceDirective = new AdvanceDirective($this->pid);
				$oAdvanceDirective->get_adv_directive();
			break;
			case "getDxAses":
				$oDx = new Dx();
				$oDx->getDxAses();
			break;
			case "get_valid_dx_codes":
				$ocpt = new CPT();
				$ocpt->getValidDxAses();
			break;
			case "get_procedure_dx_codes":
				$oproc = new Procedures($this->pid, $this->fid);
				$oproc->get_procedure_dx_codes();
			break;
			case "load_pvc":
				require($GLOBALS['srcdir']."/classes/work_view/wv_functions_new.php");
				$ow = new WorkView();
				$ow->load_pvc($this->pid);
			break;
			case "PtAtAGlancePopUp":
				require($GLOBALS['srcdir']."/classes/work_view/wv_functions_new.php");
				echo json_encode(ptGlancePopUp($_REQUEST));
			break;
			case "get_dos_prompt":
				$oPatient = new Patient($this->pid);
				$oPatient->prompt_pt_dos();
			break;
			case "set_crfrwd_id":
				$oCN = new ChartNoteSaver($this->pid, $this->fid);
				$oCN->set_crfrwd_id();
			break;
			case "ibra_case":
				$o = new IBRA($this->pid, $this->fid);
				$o->main();
			break;
			case "load_dd_dos":
				$oP = new PtChart($this->pid);
				$oP->get_dd_pt_dos();
			break;
			case "load_dos_plans":
				$oChartAP =  new ChartAP($this->pid, $_REQUEST["fid"]);
				$oChartAP->load_dos_plans();
			break;
			case "showMedReview":
				$oMedHx =  new MedHx($this->pid);
				$oMedHx->setFormId($this->fid);
				$general_health_title = $oMedHx->get_medHx_RevwdBy();
			break;
			
			default:
			break;
		}
	}	
	
	function main_wo_pt(){		
		switch($_REQUEST["elem_formAction"]){
			case "refer_phy_modal":
				$usr = new User();
				$usr->show_refer_phy_modal();
			break;
			case "GetOrderDetail":
				$oOrders = new Orders();
				$oOrders->getOrderDetail_Popup($this->pid, $this->fid);
			break;
			case "saveOrder":
				$oOrders = new Orders();
				$oOrders->save_admin_order();
			break;
			case "TypeAhead":
				$ota = new TypeAhead();
				$ota->main();
			break;
			case "Show Prev Chart Notes":
				$ow = new WorkView();
				$ow->showAnotherChartNote(1);
			break;
			case "ruCommentSave":
				$ow = new WorkView();
				$ow->break_glass();
			break;
			case "get_sb_menu":
				$ow = new WorkView();
				$ow->get_sb_menu();
			break;
			case "showImg2":
				require($GLOBALS['srcdir']."/classes/pt_at_glance.class.php");
				$oPtGlnc = new Pt_at_glance($_REQUEST["ptid"], $_SESSION["authId"], $_REQUEST);
				$oPtGlnc->show_drawing_img();
			break;
			case "change_role":
				$ow = new RoleAs($_SESSION["authId"]);
				$ow->change_role();
			break;
			case "LogError":
				usr_err_handler(4096, $_POST["msg"],'saveCharts.php','onsave');
			break;
			case "getVisionGraphAm":
				$o = new Vision($_REQUEST["ptid"]);
				$o->getVisionGraphAm();
			break;
			case "get_dx_titles":
				$oDx = new Dx();
				$oDx->get_dx_titles();
			break;
			case "getUserSign":				
				$usr = new User();
				$tmp = $usr->getSign(3);
				echo json_encode($tmp);	
			break;
			default:
			break;
		}
	}
}


//--- Start -------

//Patient
$patient_id = (isset($_SESSION["patient"]) && !empty($_SESSION["patient"])) ? $_SESSION["patient"] : 0 ;

//Get Form ID
if(isset($_SESSION["form_id"]) && !empty($_SESSION["form_id"])){
	$elem_formId = $_SESSION["form_id"];
	$finalize_flag = 0;
}else if(isset($_SESSION["finalize_id"]) && !empty($_SESSION["finalize_id"])){
	$elem_formId = $_SESSION["finalize_id"];
	$finalize_flag = 1;
}else{
	$elem_formId = 0;
}

//obj
//if(isset($_REQUEST['chartIdPRS']) && empty($_REQUEST['chartIdPRS']) == false) $elem_formId = $_REQUEST['chartIdPRS'];
$oRequestHandler = new RequestHandler($patient_id, $elem_formId);

//No Pt Requests 
if(isset($_REQUEST["req_ptwo"])&&!empty($_REQUEST["req_ptwo"])){		
	$oRequestHandler->main_wo_pt();		
}else{

//Check if patient is empty and redirect it patient search
wv_check_session();

$oRequestHandler->main($finalize_flag);

}
?>