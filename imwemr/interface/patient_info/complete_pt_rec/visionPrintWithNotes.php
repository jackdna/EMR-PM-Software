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
 * 
 * File: visionPrintWithNotes.php
 * Purpose: This file provides print data of a patient with Notes.
 * Access Type : Include file
 * 
 */

//-------print_r($_REQUEST['pt_allergy_exclusion']);die();
//-------print_r($_REQUEST['chart_exclusion']);die();

//-------IF zFormId IS SET----------------------------

//-------CPR REPRESENTS AS COMPLETE PATIENT RECORD----
//-------CPR FUNCTIONS CALLS FROM HERE----------------
if(isset($zFormId)&&!empty($zFormId))
{
	$form_id=$zFormId;
}
else
{
	$zFormId = '';
}

//-------if zFormId is set ---------------------------
//-------DOS------------------------------------------
$chartDetails = $cpr->print_getChartDetails($pid,$form_id);
$date_of_service = $chartDetails["date_of_service"];
$date_of_service_ymd = getDateFormatDB($date_of_service);
$elem_chartTemplateId = $chartDetails["templateId"];

//-------GET TEMPLATE PROCEDURES---------------------
if(!empty($elem_chartTemplateId))
{
	$oChartTemp = new ChartTemp();
	$tmp = $oChartTemp->getTempInfo($elem_chartTemplateId);
	if(!empty($tmp[1]))
	{
		$elem_chartTempName = $tmp[1];
		$arrTempProc = (!empty($tmp[2])) ? explode(",", stripslashes($tmp[2])) : array();
	}
}

//-------PRINT HEADER TOP BAR-------------------------
$patientDetailsNew = $cpr->get_pt_data($patient_id);

if(empty($date_of_service) && empty($patient_id) == false && empty($form_id) == false)
{
	$chartDetails = $cpr->print_getChartDetails($patient_id,$form_id);
	if($chartDetails['date_of_service']) $date_of_service = $chartDetails['date_of_service'];
}
//-------HEADER FUNCTION------------------------------
$cpr->print_hdrTopbar($patientDetailsNew,$date_of_service,$_REQUEST["chart_nopro"],$_REQUEST['chart_exclusion']);

//-------PRINT MAIN GROUP INFO INTO HEADER------------
$cpr->print_mainGroup($patientDetailsNew,$chartDetails,$_REQUEST["chart_nopro"],$_REQUEST['chart_exclusion']);

//-------PRINT REPORT NAME BAR
$reportName="Visit Notes";
if(!in_array("Chart Notes",$_REQUEST["chart_nopro"]))
{
	$reportName="Clinical Summary";
}
$cpr->print_reportName($reportName,$date_of_service,$_REQUEST["chart_nopro"],$_REQUEST['chart_exclusion']);

//-------PRINT MAIN HEADER----------------------------
$cpr->print_mainHeader($patientDetailsNew,$chartDetails,$_REQUEST["chart_nopro"],$_REQUEST['chart_exclusion'],$patient_id,$form_id,$date_of_service,$chartDetails["providerId"]);


//-------PRINT PRIMARY PHYSICIANS INTO TOP OF CPR PRINTING
$cpr->print_PhysIntoCPRHeader($patient_id);

//-------PRINT CCHx - CHIEF COMPLAINT AND HISTORY AT TOP OF PAGE
if(@in_array("Chart Notes",$_REQUEST["chart_nopro"]) && !in_array("cc_and_history",$_REQUEST['chart_exclusion']))
{
	$cpr->print_getCCHx($patient_id,$form_id);
}

//-------RECORD RELEASE-------------------------------
if(@in_array("Record Release",$_REQUEST["chart_nopro"]))
{
	$cpr->print_getDisclosedDetails($patient_id,$_REQUEST);
}

//-------PROBLEM LIST FUNCTION------------------------
if(@in_array("Medical History",$_REQUEST["chart_nopro"]) && ( !in_array("mu_data_set_problem_list",$_REQUEST['chart_exclusion'])))
{ 
	//|| @in_array("Medical History",$_REQUEST["chart_nopro"])
	$cpr->print_getProbList($patient_id,"",$strDosToPrint1,$zFormId,$_REQUEST['pt_prob_exclusion']);
}

//-------ALLERGIES PRINT FUNCTION---------------------
//-------$_REQUEST["allergies_testActive"]
if(@in_array("Medical History",$_REQUEST["chart_nopro"])&& (!in_array("mu_data_set_allergies",$_REQUEST['chart_exclusion'])))
{
	$cpr->print_getAllergies($patient_id,$_REQUEST["allergies_testActive"],$_REQUEST["chart_nopro"],$strDosToPrint1,$zFormId,$_REQUEST['pt_allergy_exclusion']);
}

//-------Medication-----------------------------------
$arrTmp = array();
if(@in_array("Medical History",$_REQUEST["chart_nopro"]))
{
	if(( !in_array("mu_data_set_medications",$_REQUEST['chart_exclusion']))){ $arrTmp[1] = "Active"; }
	if(( !in_array("mu_data_set_medications",$_REQUEST['chart_exclusion']))){ $arrTmp[4] = "Active"; }
//-------print_getMeds($patient_id,$arrTmp,$_REQUEST["chart_nopro"],$strDosToPrint1);
}
else 
{
	if(@in_array("Medical History",$_REQUEST["chart_nopro"]) && ( !in_array("mu_data_set_medications",$_REQUEST['chart_exclusion'])))
		$arrTmp[4] = "Active";
	
	if(@in_array("Medical History",$_REQUEST["chart_nopro"]) && ( !in_array("mu_data_set_medications",$_REQUEST['chart_exclusion'])))
		$arrTmp[1] = "Active";	
}
	if(count($arrTmp)>0)
	{
		$cpr->print_getMeds($patient_id,$arrTmp,$_REQUEST["chart_nopro"],$strDosToPrint1,$zFormId,$_REQUEST['pt_med_exclusion']);
	}
	if(!in_array("visit_medication_immu",$_REQUEST['chart_exclusion'])){$cpr->print_getMeds_administer($patient_id,$arrTmp,$_REQUEST["chart_nopro"],$strDosToPrint1,$zFormId);}

//-------SX PROCEDURE PRINT FUNCTION CALL------------
if(@in_array("Medical History",$_REQUEST["chart_nopro"]) && (!in_array("mu_data_set_superbill",$_REQUEST["chart_exclusion"])))
{
	$cpr->print_getSx($patient_id,$_REQUEST["allergies_testActive"],$_REQUEST["chart_nopro"],$strDosToPrint1,$zFormId);
}

if(@in_array("Medical History",$_REQUEST["chart_nopro"]))
{
	if(!in_array("mu_data_set_radiology",$_REQUEST['chart_exclusion']))
	{
		$cpr->print_getRadiology($patient_id,$_REQUEST['chart_exclusion']);	
	}
	//---GET GENERAL MEDICATION + IMMUNIZATION INFORMATION
	$cpr->print_getGenMed($patient_id,$_REQUEST["chart_nopro"],$strDosToPrint1,$_REQUEST['chart_exclusion']);
	$cpr->print_getImmunization($patient_id,$strDosToPrint1,$_REQUEST['chart_exclusion']);
	
	if(!in_array("mu_data_set_vs",$_REQUEST['chart_exclusion'])){ $cpr->print_vital_signs($patient_id);	}
	if(!in_array("mu_data_set_lab",$_REQUEST['chart_exclusion'])){ $cpr->print_getLabResults($patient_id,$_REQUEST['chart_exclusion']); }
}

//-------OBJECTIVE NOTES PRINT FUNCTION INCLUSION---
if(in_array("Chart Notes",$_REQUEST["chart_nopro"]))
{
	echo $cpr->print_objective_notes($form_id);
}
//-------AMENDMENTS PRINT FUNCTION------------------
if(@in_array("Patient Amendment",$_REQUEST["chart_nopro"]))
{
	$cpr->print_getAmendments($patient_id);
}

if(@in_array("Chart Notes",$_REQUEST["chart_nopro"])) {

	
	//---VISION + LASIK PRINT FUNCTION INCLUSION--
	echo $cpr->print_getVision($patient_id,$form_id);
	echo $cpr->print_getLasik($patient_id,$form_id);
	//if(!isset($arrTempProc)||in_array("Pupil",$arrTempProc)){}

	//---CONTACT LENS------------------------------
	$cpr->print_contectLens($patient_id,$form_id);
	
	//---WORK VIEW PUPIL EXAM PRINT FUNCTION INCLUSION
	if(!isset($arrTempProc)||in_array("Pupil",$arrTempProc))
	{
		
		$cpr->print_Pupil($patient_id,$form_id);
	}
	
	if(!isset($arrTempProc)||in_array("EOM",$arrTempProc))
	{
		$cpr->print_EOM($patient_id,$form_id);
	}
	if(!isset($arrTempProc)||in_array("External",$arrTempProc))
	{		
		$cpr->print_EE($patient_id,$form_id);
	}
	
	//---WORK VIEW L&A EXAM PRINT WORK--------------
	if(!isset($arrTempProc)||in_array("L&A",$arrTempProc))
	{
		$cpr->print_LA($patient_id,$form_id);
	} 
	
	//---WORK VIEW IOP/GONIO EXAM PRINT WORK---------
	if(!isset($arrTempProc)||in_array("IOP/Gonio",$arrTempProc))
	{
		$cpr->print_IOPGonio($patient_id,$form_id);
	}
	
	//---WORK VIEW SLE, CORNEA, IRIS ETC. EXAM PRINT WORK
	if(	!isset($arrTempProc) ||
		(
			in_array("SLE",$arrTempProc) || in_array("Conjunctiva",$arrTempProc) || 
			in_array("Cornea",$arrTempProc) || in_array("Ant. Chamber",$arrTempProc) || 
			in_array("Iris & Pupil",$arrTempProc) || in_array("Lens",$arrTempProc) || in_array("DrawSLE",$arrTempProc) 
		)
	  )
	{
		$cpr->print_SLE($patient_id,$form_id);
	}
	
	//---WORK VIEW FUNDUS EXAM PRINT WORK----------
	if(!isset($arrTempProc) || 
		(
			in_array("Fundus Exam",$arrTempProc) || in_array("Opt. Nev",$arrTempProc) ||
			in_array("Macula",$arrTempProc) || in_array("Fundus Exam",$arrTempProc) ||
			in_array("Vitreous",$arrTempProc) || in_array("Periphery",$arrTempProc) ||
			in_array("Blood Vessels",$arrTempProc) || in_array("DrawFundus",$arrTempProc) ||
			in_array("Retinal Exam",$arrTempProc)
		)
	 )
	{
		$cpr->print_RV($patient_id,$form_id);
		
		/*if(!isset($arrTempProc) || (in_array("Fundus Exam",$arrTempProc) || in_array("Opt. Nev",$arrTempProc) )){
			print_OpticNerve($patient_id,$form_id);
		 }*/
		
	}

	if(!isset($arrTempProc)||in_array("Refractive Surgery",$arrTempProc))
	{
		$cpr->print_RefractSurgery($patient_id,$form_id);
	}
	
	//---PROGRESS NOTES----------------------------
	$cpr->print_cn_progress_notes($patient_id,$form_id);
}

if(@in_array("AmslerGrid",$_REQUEST["chart_nopro"]) || @in_array("Chart Notes",$_REQUEST["chart_nopro"]))
{
	$cpr->print_Ophtha($patient_id,$form_id);
	/*if(!isset($arrTempProc) || in_array("Amsler Grid",$arrTempProc))
	{
		print_AmslerGrid($patient_id,$form_id);
	}*/
}

//-------TEST TAB ALL TESTS PRINT FUNCTIONS INCLUDES HERE
if((@in_array("Diagnostic Tests",$_REQUEST["chart_nopro"]) && (!in_array("mu_data_set_test",$_REQUEST['chart_exclusion']))) || ($_SESSION['printTestData'] && $_SESSION['printTestData']!=''))
{

	/*$_SESSION['printTestData'] temporary set parameter in create_chartnote_pdf.php in library/allscripts/ to print test data in pdf creating using create_chartnote_pdf.php file at back end
	include(dirname(__FILE__)."/leftForms_pdf_print.php");*/
	
	$cpr->print_vf($patient_id,$form_id,$_REQUEST["printTestRadioVF"]);
	$cpr->print_vf_gl_fun($patient_id,$form_id,$_REQUEST["printTestRadioVF_GL"]);
	$cpr->print_hrt($patient_id,$form_id,$_REQUEST["printTestRadioHRT"]);
	$cpr->print_oct($patient_id,$form_id,$_REQUEST["printTestRadioOCT"]);
	$cpr->print_oct_rnfl($patient_id,$form_id,$_REQUEST["printTestRadioOCT_RNFL"]);
	$cpr->print_gdx($patient_id,$form_id,$_REQUEST["printTestRadioGDX"]);
	$cpr->print_pachy($patient_id,$form_id,$_REQUEST["printTestRadioPachy"]);
	$cpr->print_ivfa($patient_id,$form_id,$_REQUEST["printTestRadioIVFA"]);
	$cpr->print_icg($patient_id,$form_id,$_REQUEST["printTestRadioICG"]);
	$cpr->print_disc($patient_id,$form_id,$_REQUEST["printTestRadioFundus"]);
	$cpr->print_external($patient_id,$form_id,$_REQUEST["printTestRadioExternal_Anterior"]);
	$cpr->print_topo($patient_id,$form_id,$_REQUEST["printTestRadioTopography"]);
	$cpr->print_cellcount($patient_id,$form_id,$_REQUEST["printTestRadioCellCount"]);
	
	if(!in_array("mu_data_set",$_REQUEST['chart_exclusion']))
	{
		//print_lab($patient_id,$form_id,$_REQUEST["printTestRadioLaboratories"]);
	}
	
	$cpr->print_bscan($patient_id,$form_id,$_REQUEST["printTestRadioBscan"]);	
	$cpr->print_testother($patient_id,$form_id,$_REQUEST["printTestRadioOther"]);	
	$cpr->print_testotherTemplate($patient_id,$form_id,$_REQUEST["printTestRadioTemplate_Type"]);
	$cpr->print_iol_master($patient_id,$form_id,$_REQUEST["printTestRadioIOL_Master"]);
	
	//print_vf_gl($patient_id,$form_id,$_REQUEST["printTestRadioOther"]);	
    
    //---GET ALL CUSTOM TESTS NAMES--------------
    //---WHERE SUB CONDITION---------------------
	$q_where = "AND
					status=1 
				AND 
					test_table='test_custom_patient' ";
    //---MAIN QUERY------------------------------
	$q_tests = "SELECT 
					* 
				FROM 
					`tests_name`
				WHERE 
					del_status=0 
					".$q_where." 
				ORDER BY 
					temp_name";
	$res_tests = imw_query($q_tests);
    
	if($res_tests && imw_num_rows($res_tests)>0)
	{
        while($rs_test=imw_fetch_assoc($res_tests))
		{
            $testNameID = str_replace(array('-','/',' '), '_', $rs_test['temp_name']);
            
			//---PATIENT CUSTOM TESTS PRINT FUNCTION INCLUDED HERE
			if(isset($_REQUEST["printTestRadio".$testNameID]) && $_REQUEST["printTestRadio".$testNameID]!='') 
			{
                $cpr->print_patient_custom_tests($patient_id,$form_id,$_REQUEST["printTestRadio".$testNameID],$rs_test['temp_name']);
            }
        }
    }
}

if(@in_array("Chart Notes",$_REQUEST["chart_nopro"]))
{
	$cpr->print_Memo($patient_id,$form_id);
}
//------WORK VIEW PROCEDURE OP-NOTES FUNCTION CALLING-----
if(@in_array("Include Provider Notes",$_REQUEST["chart_nopro"]))
{
	$cpr->print_PNotes($patient_id,$form_id);
}

//------WORK VIEW CHART ASSESSMENT PLANS PRINT FUNCTION CALLING
if(@in_array("Chart Notes",$_REQUEST["chart_nopro"]) && (!in_array("mu_data_set_ap",$_REQUEST['chart_exclusion'])))
{
	$cpr->print_AssessPlan($patient_id,$form_id);
}
//------CHART SIGNATURES PRINT FUNCTION CALLING----------
$cpr->print_chartsignatures($patient_id,$form_id);

//------ATTESTATION PRINT FUNCTION CALLING---------------
$cpr->print_attestation($patient_id,$form_id);

if(@in_array("Patient Demographics",$_REQUEST["chart_nopro"])==true)
{
	include(dirname(__FILE__)."/print_demographics2.php");
	//include(dirname(__FILE__)."/../common/insurance_print.php");
}

if(@in_array("Patient LegalForms",$_REQUEST["chart_nopro"])==true)
{
	$cpr->printLegal_main();
}

//------12-02-2013: #171; ALSO SHE SHOWED THAT THE PATIENT COMMUNICATION NOTES ARE IN THE MIDDLE OF CHART NOTE VS BEING AT THE END WHICH WOULD BE MORE APPROPRIATE
//------PATIENT COMMUNICATION FUNCTION CALLING HERE-----
if(@in_array("Patient Communication",$_REQUEST["chart_nopro"]))
{
	$cpr->print_getPtComm($patient_id);
}

if(!in_array("recommended_patient_decision_aids",$_REQUEST['chart_exclusion'])){ $cpr->print_education_forms($patient_id,$form_id); }

if(!in_array("mu_data_set_care_team_members",$_REQUEST['chart_exclusion']))
{
	$cpr->print_provider($patient_id);
	//get_care_providers_chart_notes($patient_id);
}

if(!in_array("provider_name_and_contact",$_REQUEST['chart_exclusion']))
{
	$cpr->print_provider_info($patient_id,$form_id);
}
//------CPR END PRINT INFO ALL FUNCTION INCLUSION------
if(!in_array("mu_data_set_logged_user",$_REQUEST['chart_exclusion'])){ $cpr->userInfo($patient_id,$_REQUEST['chart_exclusion']); }
if(!in_array("mu_data_set_superbill",$_REQUEST['chart_exclusion'])){ $cpr->superbill_print($patient_id,$form_id); }
if(!in_array("future_appt",$_REQUEST['chart_exclusion'])) { $cpr->print_future_appointment_Int($patient_id,$form_id,$date_of_service_ymd);$cpr->print_future_appointment($patient_id,$form_id,$date_of_service_ymd);  }
if(!in_array("future_sch_test",$_REQUEST['chart_exclusion'])) { $cpr->print_test_future_appointments($patient_id,$form_id,$date_of_service_ymd); }
if(!in_array("referrals_to_other_providers",$_REQUEST['chart_exclusion'])){ $cpr->referrals_to_other_providers($patient_id,$form_id); }
if(!in_array("clinical_instructions",$_REQUEST['chart_exclusion'])){ $cpr->print_clicnic($patient_id,$form_id); }	
if(!in_array("dos_facility",$_REQUEST['chart_exclusion'])){ $cpr->pt_dos_facility($patient_id,$form_id,$date_of_service,$chartDetails["providerId"]); }
if(@in_array("Modification Hx.",$_REQUEST["chart_nopro"])==true){ $cpr->print_exam_hx($patient_id,$form_id); }
?>