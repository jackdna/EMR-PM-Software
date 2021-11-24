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
File: saveCharts.php
Purpose: This file contains Save process for most of work view sections.
Access Type : Direct
*/
?>
<?php

require_once(dirname(__FILE__).'/../../config/globals.php');

//
//error_reporting(E_ALL & ~E_NOTICE);
//ini_set("display_errors",1);
//


//--
require($GLOBALS['incdir']."/chart_notes/chart_globals.php");
require($GLOBALS['srcdir']."/classes/work_view/wv_functions.php");
include_once($GLOBALS['srcdir']."/classes/audit_common_function.php");
require($GLOBALS['incdir']."/chart_notes/cl_functions.php");

set_error_handler("usr_err_handler");
register_shutdown_function("fat_err_handler");

Class Saver{
	public $pid, $fid;	
	public function __construct($pid, $fid){
		//parent::__construct($pid);
		$this->fid = $fid;
		$this->pid = $pid; 
	}

	function check_session_match_with_form(){
		//Pt Id
		$a =0;
		if(isset($_POST["sess_pt_id"]) && !empty($_POST["sess_pt_id"])){
			if($_POST["sess_pt_id"]!=$this->pid){
				$a =1;
			}
		}
		
		//form Id
		$b =0;
		if(isset($_POST["sess_form_id"]) && !empty($_POST["sess_form_id"])){
			if($_POST["sess_form_id"]!=$this->fid){
				$b =1;
			}
		}
		
		if(!empty($a) || !empty($b)){
			usr_err_handler(4096, "CHECK Error in session: ".print_r($_SESSION,1)." - ".session_id(), "saveCharts.php", "60");
			exit("Current session experiencing a change, Please reload the chart.");
		}		
	}
	
	function main(){
		
		switch($_REQUEST["elem_saveForm"]){
			case "Change Chart Notes Template":
				$elem_templateId = !empty($_REQUEST["elem_templateId"]) ? $_REQUEST["elem_templateId"] : "0";
				//$elem_tempFormId = this->fid; //$_REQUEST["elem_tempFormId"];
				$oCN = new ChartNoteSaver($this->pid, $this->fid);	
				$oCN->setTemplateId($elem_templateId);
				header("Location: work_view.php");
			break;
			case "Make New Chart Notes":				
				$oCN = new ChartNoteSaver($this->pid, 0);
				$oCN->mkNewChart();
				header("Location: work_view.php");
			break;
			case "MainTable":
				//Check PatientId change in session and in form --
				$this->check_session_match_with_form();
				$oCN = new ChartNoteSaver($this->pid, $this->fid);
				$oCN->saveWV();				
			break;
			case "Close this patient work view":
				$oCN = new ChartNoteSaver($this->pid, $this->fid);
				$oCN->closeThisPatientWorkView();
			break;
			case "Show Prev Chart Notes":				
				$ow = new WorkView();
				$ow->showAnotherChartNote();
			break;
			case "ChartNoteEdit":
				$oCN = new ChartNoteSaver($this->pid, $this->fid);
				$oCN->editNote();
			break;
			case "ChartNoteUnfinalize":
				$oCN = new ChartNoteSaver($this->pid, $this->fid);
				$oCN->unfinalizeChartNote();
			break;
			case "PurgeCN":
				$oCN = new ChartNoteSaver($this->pid, $this->fid);
				$oCN->purge_note();
			break;
			case "DeleteCN":
				$oCN = new ChartNoteSaver($this->pid, $this->fid);
				$oCN->delete_note();
			break;
			case "SET_DIABETES_IN_ASMNT":
				$as = urldecode($_GET["as"]);
				$dx = urldecode($_GET["dx"]);
				$fid = $_GET["fid"];
				if(!empty($this->pid)&&!empty($fid)){
					$oChartAp = new ChartAP($this->pid,$fid);
					$oChartAp->resetDiabetes($as, $dx);
				}
			break;
			case "SaveSuperbill":
				$oSuperbillSaver = new SuperbillSaver($this->pid);
				$oSuperbillSaver->saveHandler();
			break;
			case "SaveOrdersDetails":
				$oChartOrders = new ChartOrders($this->pid, $this->fid);
				$oChartOrders->saveOrdersDetails();
			break;
			case "DelSignature":				
				$oSign = new Signature($this->fid);
				$oSign->delSignHandler();	
			break;
			case "WNL":				
				$oCN = new ChartNoteSaver($this->pid, $this->fid);
				$oCN->save_wnl();	
			break;
			case "NoChange":
				$oCN = new ChartNoteSaver($this->pid, $this->fid);
				$oCN->save_no_change();
			break;
			case "SavePeriphery":
			case "saveCDRV":				
				$oFE=new FundusExam($this->pid, $this->fid);
				$oFE->save_peri_cd();
			break;
			case "setResetValues":
				$oCN = new ChartNoteSaver($this->pid, $this->fid);
				$oCN->set_reset_values();
			break;
			case "EPOST":
				$oEpost = new Epost($this->pid);
				$oEpost->savehandler();
			break;
			case "EOM" :
				$oEom = new EOMSaver($this->pid, $this->fid);
				$oEom->save_form();
			break;
			case "Pupil" :
				$oPupil = new PupilSaver($this->pid, $this->fid);
				$oPupil->save_form();
			break;
			case "External Exam" :
				$oExternalExamSaver = new ExternalExamSaver($this->pid, $this->fid);
				$oExternalExamSaver->save_form();
			break;
			case "l_and_atable1":
				$oLA = new LASaver($this->pid, $this->fid);
				$oLA->save_form();
			break;
			case "sletable1":
				$oSLE = new SLESaver($this->pid, $this->fid);
				$oSLE->save_form();
			break;			
			case "rvtable1":
				$oFundusExam = new FundusExamSaver($this->pid, $this->fid);
				$oFundusExam->save_form();
			break;
			case "ref_surg":
				$oRefSurg = new RefSurgSaver($this->pid, $this->fid);
				$oRefSurg->save_form();
			break;
			case "ioptable1":
				$oIGS = new IopGonioSaver($this->pid, $this->fid);
				$oIGS->save_form();
			break;
			case "updateIOPTime":
				$oIGS = new IopGonioSaver($this->pid, $this->fid);
				if($_POST["w"]=="Anes"){  $oIGS->update_anes_time(); }
				else if($_POST["w"]=="Dial"){  $oIGS->update_dial_time(); }
				else if($_POST["w"]=="OOD"){  $oIGS->update_ood_time(); }				
			break;
			case "Smart Charting":
				$oSmartChartSaver = new SmartChartSaver($this->pid, $this->fid);
				$oSmartChartSaver->save_v5();
			break;
			case "Smart Charting_v6":
				$oSmartChartSaver = new SmartChartSaver($this->pid, $this->fid);
				$oSmartChartSaver->save_v6();
			break;
			case "showPrvSynthesis":				
				$oPtTest = new PtTest($this->pid, $this->fid);
				$oPtTest->showPrvSynthesis_handler();
			break;
			case "procedures_save":
				$oProcedures = new Procedures($this->pid, $this->fid);
				$oProcedures->save();
			break;
			case "uploadScan_AR":
				global $finalize_flag;
				$oload = new Uploader($this->pid, $this->fid);
				$oload->save_ar($finalize_flag);
			break;
			case "SaveDrawingPane":
				global $finalize_flag;
				$oPtDrawings = new PtDrawings($this->pid, $this->fid, $finalize_flag);
				$oPtDrawings->saveDrawings();
			break;
			case "Amendments":
				$oAmendments = new ChartAmendment($this->pid,$this->fid);
				$oAmendments->saveAmendments();
			break;
			case "physician_notes":
				$ophynotes = new PhyPtNotes($this->pid);
				$ophynotes->savePhyNote();
			break;
			case "pnReports":
				$opnt = new OperativeNote($this->pid,$this->fid);
				$opnt->saveOpNote();
			break;
			case "save_cvf":
				$oCvf = new CVF($this->pid, $this->fid);
				$oCvf->save_form();
			break;
			case "save_ams_grid":
				$oAg = new AmslerGrid($this->pid, $this->fid);
				$oAg->save_form();
			break;			
			case "upload_idoc_drawings":
				$oPt =  new PtDrawings($this->pid, $this->fid);
				$oPt->upload_idoc_drawings();
			break;
			case "rem_scan_upload_doc":
				$oPt =  new PtDrawings($this->pid, $this->fid);
				$oPt->rem_scan_upload_doc();
			break;
			case "setAttestation":
				$osig = new Signature($this->fid, $this->pid);
				$osig->setAttestation();
			break;
			case "save_advance_directive":
				$oAdvanceDirective = new AdvanceDirective($this->pid);
				$oAdvanceDirective->save();
			break;
			case "DeleteDrawing":
				$oPt =  new PtDrawings($this->pid, $this->fid);
				$oPt->del_idoc_drawings();
			break;
			case "save_sur_ocu":
				$oChartAp = new ChartAP($this->pid, $this->fid);
				$oChartAp->save_sur_ocu();
			break;
			case "del_inter_report":
				$oChartDrw = new ChartDraw($this->pid, $this->fid,"");
				$oChartDrw->del_inter_report();
			break;
			case "proc_amedment_save":
				$oProcedures = new Procedures($this->pid, $this->fid);
				$oProcedures->save_amend();
			break;
			
			default:
				print_r("Error::");
				print_r($_REQUEST);
			break;
	
		}		
	}

}

//Start -----------------------

//Check if patient is empty and redirect it patient search
wv_check_session("save");

//
//$patient_id
$patient_id = $_SESSION["patient"];
$form_id=0;
$finalize_flag=0;
//$form_id
if(isset($_SESSION["finalize_id"]) && !empty($_SESSION["finalize_id"])){
	$form_id = $_SESSION["finalize_id"];
	$finalize_flag = 1;

}else if(isset($_SESSION["form_id"]) && !empty($_SESSION["form_id"])){
	$form_id = $_SESSION["form_id"];
	$finalize_flag = 0;
}else{ 
	
	if(!empty($_REQUEST["sess_form_id"])){
		$form_id = $_REQUEST["sess_form_id"];
		$finalize_flag = $_REQUEST["hidd_final_flag"];		
	}else if(!empty($_REQUEST["elem_formId"])){
		$form_id = $_REQUEST["elem_formId"];
	}
	
	if(!empty($form_id)){
		if(empty($finalize_flag)){$_SESSION["form_id"]=$form_id;}elseif(!empty($finalize_flag)){$_SESSION["finalize_id"]=$form_id;}
	}
}

//Loader
$osave = new Saver($patient_id, $form_id);
$osave->main();

?>