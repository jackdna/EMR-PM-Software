<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\ImageController;
//
//Intra Op Record

class IntraOpRecordController extends Controller {

    public function IntraOpRecord_save(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $loginUserType = $request->json()->get('UserType') ? $request->json()->get('UserType') : $request->input('UserType');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $operatingRoomRecordsId = $request->json()->get('operatingRoomRecordsId') ? $request->json()->get('operatingRoomRecordsId') : $request->input('operatingRoomRecordsId');
        $iol_ScanUpload = $request->json()->get('iol_ScanUpload') ? $request->json()->get('iol_ScanUpload') : $request->input('iol_ScanUpload');
        $iol_ScanUpload2 = $request->json()->get('iol_ScanUpload2') ? $request->json()->get('iol_ScanUpload2') : $request->input('iol_ScanUpload2');
        $jsondata = $request->json()->get('jsondata') ? $request->json()->get('jsondata') : $request->input('jsondata');
        $json = json_decode($jsondata);

        $data = [];
        $status = 0;
        $moveStatus = 0;
        // $requiredStatus = 1;
        $message = " unauthorized ";
        $requiredStatus = [];
        $userId = $this->checkToken($userToken);
        $patient_instruction_id = 0;
        $andConsentIdQry = '';
        $ampConsentAutoId = '';
        $surgery_consent_data = "";
        $signStatus = [];
        $consent_content = '';
        $left_list = '';
        $stat = [];
        $surgery_consent_id = 0;

        if ($userId > 0) {
            if ($pConfirmId == "") {
                $message = " ConfirmId is missing ";
                $status = 1;
                $requiredStatus = 0;
            } elseif ($facility_id == "") {
                $message = " FacilityId is missing ";
                $status = 1;
                $requiredStatus = 0;
            } else if ($loginUserType == "") {
                $message = " UserType is missing ";
                $status = 1;
                $requiredStatus = 0;
            } else if ($iasc_facility_id == "") {
                $status = 1;
                $message = " IASC Id is missing ";
            } else {
                $current_form_version = 2;
                $ViewUserNameQry = "select fname,mname,lname,user_type from `users` where  usersId = '" . $userId . "'";
                $ViewUserNameRow = DB::selectone($ViewUserNameQry); // or die($ViewUserNameQry . imw_error());
                $loggedInUserFirstName = $ViewUserNameRow->fname;
                $loggedInUserMiddleName = $ViewUserNameRow->mname;
                $loggedInUserLastName = $ViewUserNameRow->lname;
                $user_type = $ViewUserNameRow->user_type;
                $signOnFileStatus = 'Yes';
                $loggedInUserName = $ViewUserNameRow->lname . ", " . $ViewUserNameRow->fname . " " . $ViewUserNameRow->mname;
                //SAVE RECORD TO DATABASE
                //print '<pre>';print_r($_REQUEST);die;
                $OpRoom_patientName_tblQry = "SELECT patient_fname,patient_mname,patient_lname FROM `patient_data_tbl` WHERE `patient_id` = '" . $patient_id . "'";
                $OpRoom_patientName_tblRow = DB::selectone($OpRoom_patientName_tblQry); // or die(imw_error());
                $OpRoom_patientName = $OpRoom_patientName_tblRow->patient_fname . " " . $OpRoom_patientName_tblRow->patient_mname . " " . $OpRoom_patientName_tblRow->patient_lname;
                $OpRoom_patientConfirm_tblQry = "SELECT * FROM `patientconfirmation` WHERE `patientConfirmationId` = '" . $pConfirmId . "'";
                $OpRoom_patientConfirm_tblRow = DB::selectone($OpRoom_patientConfirm_tblQry); // or die(imw_error());

                $OpRoom_patientConfirmDosTemp = $OpRoom_patientConfirm_tblRow->dos;
                $OpRoom_patientConfirmDos_split = explode("-", $OpRoom_patientConfirmDosTemp);
                $OpRoom_patientConfirmDos = $OpRoom_patientConfirmDos_split[1] . "-" . $OpRoom_patientConfirmDos_split[2] . "-" . $OpRoom_patientConfirmDos_split[0];
                $OpRoom_patientConfirmSurgeon = $OpRoom_patientConfirm_tblRow->surgeon_name;
                $OpRoom_patientConfirmSiteTemp = $OpRoom_patientConfirm_tblRow->site;
                $OpRoom_patientConfirmAnes_NA = $OpRoom_patientConfirm_tblRow->anes_NA;
                // APPLYING NUMBERS TO PATIENT SITE
                if ($OpRoom_patientConfirmSiteTemp == 1) {
                    $OpRoom_patientConfirmSite = "Left Eye";  //OD
                } else if ($OpRoom_patientConfirmSiteTemp == 2) {
                    $OpRoom_patientConfirmSite = "Right Eye";  //OS
                } else if ($OpRoom_patientConfirmSiteTemp == 3) {
                    $OpRoom_patientConfirmSite = "Both Eye";  //OU
                } else if ($OpRoom_patientConfirmSiteTemp == 4) {
                    $OpRoom_patientConfirmSite = "Left Upper Lid";
                } else if ($OpRoom_patientConfirmSiteTemp == 5) {
                    $OpRoom_patientConfirmSite = "Left Lower Lid";
                } else if ($OpRoom_patientConfirmSiteTemp == 6) {
                    $OpRoom_patientConfirmSite = "Right Upper Lid";
                } else if ($OpRoom_patientConfirmSiteTemp == 7) {
                    $OpRoom_patientConfirmSite = "Right Lower Lid";
                } else if ($OpRoom_patientConfirmSiteTemp == 8) {
                    $OpRoom_patientConfirmSite = "Bilateral Upper Lid";
                } else if ($OpRoom_patientConfirmSiteTemp == 9) {
                    $OpRoom_patientConfirmSite = "Bilateral Lower Lid";
                }
                // END APPLYING NUMBERS TO PATIENT SITE
                $OpRoom_patientConfirmPrimProc = $OpRoom_patientConfirm_tblRow->patient_primary_procedure;
                $OpRoom_patientConfirmSecProc = $OpRoom_patientConfirm_tblRow->patient_secondary_procedure;
                if ($OpRoom_patientConfirmSecProc != "N/A") {
                    $OpRoom_patientConfirmSecProcTemp = "Yes";
                } else {
                    $OpRoom_patientConfirmSecProcTemp = " ";
                }
                $OpRoom_patientConfirmAnesthesiologistId = $OpRoom_patientConfirm_tblRow->anesthesiologist_id;
                $OpRoom_patientConfirmNurseId = $OpRoom_patientConfirm_tblRow->nurseId;
                $OpRoom_patientConfirmSurgeonId = $OpRoom_patientConfirm_tblRow->surgeonId;
                $OpRoom_patientConfirmAnesthesiologistName = trim(stripslashes($OpRoom_patientConfirm_tblRow->anesthesiologist_name));

                //GET ANESTHESIOLOGIST NAME, NURSE NAME, SURGEON NAME 
                //$OpRoomAnesthesiologistName = getUserName($OpRoom_patientConfirmAnesthesiologistId,'Anesthesiologist');
                $OpRoomAnesthesiologistName = $this->getUserName($userId, 'Anesthesiologist');
                $OpRoomNurseName = $this->getUserName($userId, 'Nurse');
                $OpRoomSurgeonName = $this->getUserName($OpRoom_patientConfirmSurgeonId, 'Surgeon');
                $tablename = "operatingroomrecords";
                $verifiedbyNurse = $json->TimeOut->Nurse->checked;
                $verifiedbyNurseName = trim(addslashes($json->TimeOut->Nurse->name));

                $verifiedbySurgeon = addslashes($json->TimeOut->Surgeon->name);
                $verifiedbyAnesthesiologist = $json->TimeOut->AnesthesiaProvider->checked; //$_POST["chbx_vbya"];
                $sxPlanReviewedBySurgeon = $json->TimeOut->SxPlanSheetReviewedBySurgeon->checked; // $_POST["chbx_sx_rbys"];
                $verifiedbyAnesthesiologistName = trim(addslashes($json->TimeOut->AnesthesiaProvider->name));
                $verifiedbyNurseTime = "";
                if (strtotime($json->TimeOut->Time)) {
                    $verifiedbyNurseTimeExplode = explode(" ", $json->TimeOut->Time); // $_REQUEST['verifiedbyNurseTime']
                    if ((!$verifiedbyNurseTimeExplode[1] && !stristr($json->TimeOut->Time, 'P') && !stristr($json->TimeOut->Time, 'A')) || strtoupper($verifiedbyNurseTimeExplode[1]) == 'AM' || strtoupper($verifiedbyNurseTimeExplode[1]) == 'PM') {
                        $verifiedbyNurseTime = $this->setTmFormat($json->TimeOut->Time, 'static');
                    }
                }
                $hidd_verifiedbyNurse = $verifiedbyNurse;  //$_POST["hidd_chbx_vbyn"];
                $hidd_verifiedbySurgeon = $verifiedbySurgeon; // $_POST["hidd_chbx_vbys"];
                $hidd_sxPlanReviewedBySurgeon = $json->TimeOut->SxPlanSheetReviewedBySurgeon->checked; // $_POST["hidd_chbx_sx_rbys"];
                $hidd_sxPlanRvwBySrgnDtm = ''; // $_POST["hidd_sxPlanRvwBySrgnDtm"];
                $hidd_verifiedbyAnesthesiologist = $verifiedbyAnesthesiologist; // $_POST["hidd_chbx_vbya"];
                if ($verifiedbyNurse == "") {
                    $verifiedbyNurse = $hidd_verifiedbyNurse;
                }
                if ($verifiedbyAnesthesiologist == "") {
                    $verifiedbyAnesthesiologist = $hidd_verifiedbyAnesthesiologist;
                }
                //IF STILL NURSE CHECKBOX IS BLANK THEN LEAVE verifiedbyNurseName, verifiedbyAnesthesiologist FIELD BLANK
                if ($verifiedbyNurse == "") {
                    $verifiedbyNurseName = "";
                }
                if ($verifiedbyAnesthesiologist == "") {
                    $verifiedbyAnesthesiologistName = "";
                }
                //IF STILL NURSE CHECKBOX IS BLANK THEN LEAVE verifiedbyNurseName, verifiedbyAnesthesiologist FIELD BLANK

                if ($verifiedbySurgeon == "") {
                    $verifiedbySurgeon = $hidd_verifiedbySurgeon;
                }
                if ($sxPlanReviewedBySurgeon == "") {
                    $sxPlanReviewedBySurgeon = $hidd_sxPlanReviewedBySurgeon;
                }

                $preOpDiagnosis = addslashes($json->TimeOut->PreopDiagnosis->detail);
                $operativeProcedures = addslashes($json->TimeOut->OperativeProcedure->detail); //$_POST["operativeProcedures"]
                $product_control_na = $json->Product_Control->N_A; // $_POST["product_control_na"];
                $bssValue = $json->Product_Control->N_A; // $_POST["chbx_bss"];
                $iol_na = $json->IOL->n_a; // $_POST['iol_na'];
                $Epinephrine03 = $json->Product_Control->Added_To_Infusion_Bottle->Epinephrine; //$_POST["Epinephrine03"];
                $Vancomycin01 = $json->Product_Control->Added_To_Infusion_Bottle->Vancomycin_01; // $_POST["Vancomycin01"];
                $Vancomycin02 = $json->Product_Control->Added_To_Infusion_Bottle->Vancomycin_02; // $_POST["Vancomycin02"];
                $omidria = $json->Product_Control->Added_To_Infusion_Bottle->Omidria; // $_POST['omidria'];
                $InfusionOtherChk = $json->Product_Control->Added_To_Infusion_Bottle->Other; // $_POST["InfusionOtherChk"];  //part of supplies
                if ($InfusionOtherChk == "Yes") {
                    $infusionBottleOther = addslashes($json->Product_Control->Added_To_Infusion_Bottle->Other_Box); //$_POST["infusionBottleOther"]
                } else {
                    $infusionBottleOther = "";
                }
                $OtherSuppliesUsed = isset($json->Product_Control->OtherSuppliesUsed) ? addslashes($json->Product_Control->OtherSuppliesUsed) : ""; //$_POST["OtherSuppliesUsed"]

                $percent_txt = ''; // $_POST["percent_txt"];
                $percent = ''; // $_POST["percent"];
                $XylocaineMPF = $json->IntraOpInj1->XylocaineMPF->text; //$_POST["XylocaineMPF"];
                $manufacture = $json->IOL_Manufacturer->Man->selected; // $_POST["manufacture"];
                $lensBrand = $json->IOL_Manufacturer->LensBrand->selected; //$_POST["lensBrand"];
                $iol_comments = addslashes($json->IOL_Manufacturer->IOLComments); //$_POST["iol_comments"]
                //$post2DischargeSummary = $_POST["post2DischargeSummary"];
                //$post2OperativeReport = $_POST["post2OperativeReport"];
                //$model = addslashes($_REQUEST["model"]);
                $model = addslashes($json->IOL_Manufacturer->Model->text); //$_REQUEST["model"]
                $Diopter = $json->IOL_Manufacturer->Diopter; //$_POST["Diopter"];
                $iolConfirmedSurgeonSignOnFile = ''; // $_POST["iolConfirmedSurgeonSignOnFile"];
                $prep_solution_na = $json->IOLConfirmed->Prep_Solutions->N_A; //$_POST["prep_solution_na"];
                $Betadine = $json->IOLConfirmed->Prep_Solutions->Betadine_10; // $_POST["Betadine"];
                $Saline = $json->IOLConfirmed->Prep_Solutions->Saline; // $_POST["Saline"];
                $Alcohol = $json->IOLConfirmed->Prep_Solutions->Alcohol; //  $_POST["Alcohol"];
                $Prcnt5Betadinegtts = $json->IOLConfirmed->Prep_Solutions->Betadine_gtts_5; // $_POST["Prcnt5Betadinegtts"];
                $proparacaine = $json->IOLConfirmed->Prep_Solutions->Proparacaine; //  $_POST["proparacaine"];
                $tetracaine = $json->IOLConfirmed->Prep_Solutions->Tetracaine; //$_POST["tetracaine"];
                $tetravisc = $json->IOLConfirmed->Prep_Solutions->Tetravisc; //$_POST["tetravisc"];
                $prepSolutionsOther = addslashes($json->IOLConfirmed->Prep_Solutions->Other); //$_POST["prepSolutionsOther"]
                $surgeryORNumber = $json->Surgery->Room; //$_REQUEST['surgeryORNumber'];
                $surgeryTimeIn = "";
                $surgeryTimeIn = $this->setTmFormat($json->Surgery->InRoomTime, 'static');
                /*
                  $anesStart = $_REQUEST['anesStartTime'];
                  $anesEnd = $_REQUEST['anesEndTime'];
                  //code to set anes time in DB
                  $anesStarttime = $anesStart;
                  $spilt_startTime = explode(" ", $anesStarttime);
                  if ($spilt_startTime[1] == "PM" || $spilt_startTime[1] == "pm") {
                  $spilt_anesStarttime = explode(":", $spilt_startTime[0]);
                  $spilt_anesStarttimeIncr = $spilt_anesStarttime[0] + 12;
                  $anesStartTime = $spilt_anesStarttimeIncr . ":" . $spilt_anesStarttime[1] . ":.00";
                  } elseif ($spilt_startTime[1] == "AM" || $spilt_startTime[1] == "am") {
                  $spilt_anesStarttime = explode(":", $spilt_startTime[0]);
                  $anesStartTime = $spilt_anesStarttime[0] . ":" . $spilt_anesStarttime[1] . ":.00";
                  }

                  $anesEndtime = $anesEnd;
                  $spilt_EndTime = explode(" ", $anesEndtime);
                  if ($spilt_EndTime[1] == "PM" || $spilt_EndTime[1] == "pm") {
                  $spilt_anesEndtime = explode(":", $spilt_EndTime[0]);
                  $spilt_anesEndtimeIncr = $spilt_anesEndtime[0] + 12;
                  $anesEndTime = $spilt_anesEndtimeIncr . ":" . $spilt_anesEndtime[1] . ":.00";
                  } elseif ($spilt_EndTime[1] == "AM" || $spilt_EndTime[1] == "am") {
                  $spilt_anesEndtime = explode(":", $spilt_EndTime[0]);
                  $anesEndTime = $spilt_anesEndtime[0] . ":" . $spilt_anesEndtime[1] . ":.00";
                  } */

                //end code to set anes time in DB

                $surgeryStartTime = "";
                $surgeryEndTime = "";
                $surgeryTimeOut = "";
                $surgeryStartTime = $this->setTmFormat($json->Surgery->SurgeryStartTime);
                $surgeryEndTime = $this->setTmFormat($json->Surgery->SurgeryEndTime);
                $surgeryTimeOut = $this->setTmFormat($json->Surgery->OutofRoom, 'static');

                $pillow_under_knees = $json->PatientPosition->PillowUnderKnee; //$_POST["pillow_under_knees"];
                $head_rest = $json->PatientPosition->HeadRest; // $_POST["head_rest"];
                $safetyBeltApplied = $json->PatientPosition->SafetyBeltApplied; // $_POST["safetyBeltApplied"];
                $other_position = addslashes($json->PatientPosition->Other); //$_POST["other_position"]
                if ($other_position == "Yes") {
                    $surgeryPatientPositionOther = isset($json->PatientPosition->Othertext) ? addslashes($json->PatientPosition->Othertext) : ""; //$_POST["surgeryPatientPositionOther"]
                } else {
                    $surgeryPatientPositionOther = "";
                }

                $Solumedrol = $json->IntraOpInj1->Solumedrol->checked; //$_POST["Solumedrol"];
                $Dexamethasone = $json->IntraOpInj1->Dexamethasone->checked; //$_POST["Dexamethasone"];
                $Kenalog = $json->IntraOpInj1->Kenalog->checked; // $_POST["Kenalog"];
                $Vancomycin = $json->IntraOpInj1->Vancomycin->checked; // $_POST["Vancomycin"];
                $Trimaxi = $json->IntraOpInj1->TriMoxi->checked; // $_POST["Trimaxi"];
                $injXylocaineMPF = $json->IntraOpInj1->XylocaineMPF->checked; //  $_POST["injXylocaineMPF"];
                $injMiostat = $json->IntraOpInj1->Miostat->checked; // $_POST["injMiostat"];
                $PhenylLido = $json->IntraOpInj1->PhenylLido->checked; // $_POST["PhenylLido"];
                $Ancef = $json->IntraOpInj1->Ancef->checked; // $_POST["Ancef"];
                $Gentamicin = $json->IntraOpInj1->Gentamicin->checked; //$_POST["Gentamicin"];
                $Depomedrol = $json->IntraOpInj1->Depomedrol->checked; // $_POST["Depomedrol"];
                $postOpInjOther = addslashes($json->IntraOpInj2->Other); //$_POST["postOpInjOther"]

                $SolumedrolList = $json->IntraOpInj1->Solumedrol->text; // $_POST["SolumedrolList"];
                $DexamethasoneList = $json->IntraOpInj1->Dexamethasone->text; // $_POST["DexamethasoneList"];
                $KenalogList = $json->IntraOpInj1->Kenalog->text; //$_POST["KenalogList"];
                $VancomycinList = $json->IntraOpInj1->Vancomycin->text; //$_POST["VancomycinList"];
                $TrimaxiList = $json->IntraOpInj1->TriMoxi->text; //$_POST["TrimaxiList"];
                $injXylocaineMPFList = $json->IntraOpInj1->XylocaineMPF->text; // $_POST["injXylocaineMPFList"];
                $injMiostatList = $json->IntraOpInj1->Miostat->text; //$_POST["injMiostatList"];

                $PhenylLidoList = $json->IntraOpInj1->PhenylLido->text; // $_POST["PhenylLidoList"];
                $AncefList = $json->IntraOpInj1->Ancef->text; // $_POST["AncefList"];
                $GentamicinList = $json->IntraOpInj1->Gentamicin->text; // $_POST["GentamicinList"];
                $DepomedrolList = $json->IntraOpInj1->Depomedrol->text; //  $_POST["DepomedrolList"];
                $anesthesia_service = '';
                if ($json->AnesthesiaService->FullAnesthesiaserviceprovided <> "") {
                    $anesthesia_service = 'full_anesthesia';
                } else if ($json->AnesthesiaService->NoAnesthesiaserviceprovided <> "") {
                    $anesthesia_service = 'no_anesthesia';
                }
                $anesthesia_service = $anesthesia_service; //$_POST["anesthesia_service"];
                $TopicalBlock = '';
                if ($json->AnesthesiaService->Local <> "") {
                    $TopicalBlock = 'Local';
                } else if ($json->AnesthesiaService->Block <> "") {
                    $TopicalBlock = 'Block';
                } elseif ($json->AnesthesiaService->Topical <> "") {
                    $TopicalBlock = 'Topical';
                }
                // $TopicalBlock = $TopicalBlock; // $_POST["TopicalBlock"];

                $patch = $json->IntraOpInj2->Patch; //$_POST["patch"];
                $shield = $json->IntraOpInj2->ShieldNeedleSuturecount; // $_POST["shield"];
                $correct = '';
                if ($json->IntraOpInj2->Correct_Yes <> "") {
                    $correct = 'Yes';
                } else if ($json->IntraOpInj2->Correct_No <> "") {
                    $correct = 'No';
                }
                $needleSutureCount = $correct; //$_POST["needleSutureCount"];
                $needleSutureCountNA = $json->IntraOpInj2->Correct_NA; // $_POST["needleSutureCountNA"];

                $collagenShield = $json->AnesthesiaService->CollagenShield; // $_POST["chbx_collagen_shield"];

                $Econopred = $json->AnesthesiaService->Soakedin->Econopred; // $_POST["Econopred"];
                $Zymar = $json->AnesthesiaService->Soakedin->Zymar; // $_POST["Zymar"];
                $Tobradax = $json->AnesthesiaService->Soakedin->Tobradax; // $_POST["Tobradax"];
                $soakedInOtherChk = $json->AnesthesiaService->Soakedin->Other; //  $_POST["soakedInOtherChk"];
                $soakedInOther = addslashes($json->AnesthesiaService->Soakedin->Othertext); //$_POST["soakedInOther"]
                if ($collagenShield <> "Yes") {
                    //$soakedIn = "";
                    $Econopred = "";
                    $Zymar = "";
                    $Tobradax = "";
                    $soakedInOtherChk = "";
                    $soakedInOther = "";
                }

                if ($collagenShield == "Yes" && $soakedInOtherChk <> "Yes") {
                    $soakedInOther = "";
                }
                $postOpDiagnosis = addslashes($json->TimeOut->PostopDiagnosis->detail); //$_POST["postOpDiagnosis"]
                $other_remain = addslashes($json->AnesthesiaService->Comments); //$_POST["other_remain"]
                $postOpDrops = addslashes($json->IntraOpInj2->PostOpOrder->text); //$_POST["postOpDrops"]
                $nurseNotes = addslashes($json->IntraOpInj2->NurseNotes->text); //$_POST["nurseNotes"]
                $intraOpPostOpOrder = addslashes($json->IntraOpInj2->PostOpOrder->text); //$_POST["intraOpPostOpOrder"]

                $surgeonId1 = ''; // $_POST["GroupSurgeonList"];
                $anesthesiologistId = $OpRoom_patientConfirmAnesthesiologistId;
                $scrubTechId1 = isset($json->ElectronicallySigned1->ScrubTech1->selected) ? $json->ElectronicallySigned1->ScrubTech1->selected : ""; //$_POST["scrub_techList"];
                $scrubTechOther1 = addslashes($json->ElectronicallySigned1->ScrubTech1->othertext); //$_POST["scrubTechOther1"]
                if ($scrubTechId1 <> "other") {
                    $scrubTechOther1 = "";
                }
                $scrubTechId2 = isset($json->ElectronicallySigned2->ScrubTech2->selected) ? $json->ElectronicallySigned2->ScrubTech2->selected : ""; //$json->ElectronicallySigned1->ScrubTech1->;// $_POST["scrub_techList1"];
                $scrubTechOther2 = addslashes($json->ElectronicallySigned2->ScrubTech2->scrubTechOther2); //$_POST["scrubTechOther2"]
                if ($scrubTechId2 <> "other") {
                    $scrubTechOther2 = "";
                }
                $circulatingNurseId = $json->ElectronicallySigned1->Nurse->selectedNurseId; //$_POST["circulating_nurseList"];
                $NurseTitle = $json->ElectronicallySigned1->Nurse->nurseTitle; //$_POST["nurseTitle"];
                $NurseId = $json->ElectronicallySigned1->Nurse->selectedNurseId; //$_POST["nurseList"];
                $signOnFileSurgeon1 = ''; //$_POST["chbx_op_surg1"];
                $iol_serial_number = trim(addslashes($json->IOL->s_n)); //$_POST['iol_serial_number']
                //$iol_ScanUpload
                //START CODE TO CHECK NURSE,SURGEON, ANESTHESIOLOGIST SIGN IN DATABASE
                $chkUserSignDetails = $this->getRowRecord('operatingroomrecords', 'confirmation_id', $pConfirmId);
                if ($chkUserSignDetails) {
                    $chk_signNurseId = $chkUserSignDetails->signNurseId;
                    $chk_signNurse1Id = $chkUserSignDetails->signNurse1Id;
                    $chk_signAnesthesia2Id = $chkUserSignDetails->signAnesthesia2Id;

                    $chk_form_status = $chkUserSignDetails->form_status;
                    $chk_vitalSignGridStatus = $chkUserSignDetails->vitalSignGridStatus;

                    $chk_surgeryStartTime = $chkUserSignDetails->surgeryStartTime;
                    $chk_surgeryEndTime = $chkUserSignDetails->surgeryEndTime;

                    //CHECK IOL SCAN
                    $chk_iol_ScanUpload = $chkUserSignDetails->iol_ScanUpload;
                    $chk_iol_ScanUpload2 = $chkUserSignDetails->iol_ScanUpload2;
                    //CHECK IOL SCAN

                    $chk_opRoomVersionNum = $chkUserSignDetails->version_num;
                    $chk_opRoomVersionDate = $chkUserSignDetails->version_date_time;
                    $field_iol_ScanUpload = $chkUserSignDetails->iol_ScanUpload;
                    $field_iol_ScanUpload2 = $chkUserSignDetails->iol_ScanUpload2;
                }
                $procedureSecondaryVerified = '';
                //END CODE TO CHECK NURSE,SURGEON, ANESTHESIOLOGIST SIGN IN DATABASE
                //START SET VERSION NUMBER
                $versionNumQry = "";
                $version_num = $chk_opRoomVersionNum;
                if (!$chk_opRoomVersionNum) {
                    $version_date_time = $chk_opRoomVersionDate;
                    if ($version_date_time == '' || $version_date_time == '0000-00-00 00:00:00') {
                        $version_date_time = date('Y-m-d H:i:s');
                    }

                    if ($chk_form_status == 'completed' || $chk_form_status == 'not completed') {
                        $version_num = 1;
                    } else {
                        $version_num = $current_form_version;
                    }

                    $versionNumQry .= ", version_num =	'" . $version_num . "', version_date_time	=	'" . $version_date_time . "' ";
                }
                //END SET VERSION NUMBER

                $vitalSignGridStatus = $this->loadVitalSignGridStatus($chk_form_status, $chk_vitalSignGridStatus, 'oproom');
                $vitalSignGridQuery = '';
                if ($chk_form_status <> 'completed' && $chk_form_status <> 'not completed') {
                    $vitalSignGridQuery = ", vitalSignGridStatus = '" . $vitalSignGridStatus . "'  ";
                }

                // Check if surgery start time or surgery end time field values changed
                $start_time_staus = '0';
                if (($surgeryStartTime && $surgeryStartTime <> $this->getTmFormat($chk_surgeryStartTime)) || ($surgeryEndTime && $surgeryEndTime <> $this->getTmFormat($chk_surgeryEndTime))
                ) {
                    $start_time_staus = '1';
                }

                //SET FORM STATUS ACCORDING TO MANDATORY FIELD

                if (($verifiedbyNurse != "" && $verifiedbySurgeon != "") && ($verifiedbyAnesthesiologist != "" || $OpRoom_patientConfirmAnes_NA == "Yes") && (trim($preOpDiagnosis) != "" || trim($postOpDiagnosis) != "") && ( trim($operativeProcedures) != "" || (trim($postOpDrops) != "" && defined("DISABLE_OPROOM_POSTOP_MED") && constant("DISABLE_OPROOM_POSTOP_MED") != "YES")) && ($product_control_na != "" || $bssValue != "" || $Epinephrine03 != "" || $Vancomycin01 != "" || $Vancomycin02 != "" || $omidria != '' || $InfusionOtherChk != "" || $suppCompStatus || $OtherSuppliesUsed != "" ) && ($manufacture != "" || $model != "" || $Diopter != "" || $chk_iol_ScanUpload || $chk_iol_ScanUpload2 || $iol_na == "Yes") && ($prep_solution_na != "" || $Betadine != "" || $Saline != "" || $Alcohol != "" || $Prcnt5Betadinegtts != "" || trim($prepSolutionsOther) != "" || $proparacaine != '' || $tetracaine != '' || $tetravisc != '') && ($surgeryORNumber != "" || $surgeryTimeIn != "" || $surgeryStartTime != "" || $surgeryEndTime != "" || $surgeryTimeOut != "" ) && ($pillow_under_knees != "" || $head_rest != "" || $safetyBeltApplied != "" || $other_position != "" || $surgeryPatientPositionOther != "") && ($anesthesia_service != "" || $TopicalBlock != "" ) //|| $collagenShield != ""
                        && ($scrubTechId1 != "" || $scrubTechId2 != "") && ($chk_signNurseId <> "0" || $iol_na == "Yes") && ($chk_signNurse1Id <> "0")
                ) {
                    $form_status = "completed";
                } else {
                    $form_status = "not completed";
                }

                //END SET FORM STATUS ACCORDING TO MANDATORY FIELD

                $ViewUserTypeQry = "select * from `users` where  usersId = '" . $userId . "'";
                $ViewUserTypeRow = DB::selectone($ViewUserTypeQry); // or die(imw_error());
                $vrfyBySrgn = "";
                if ($ViewUserTypeRow->user_type == "Surgeon") {
                    if ($sxPlanReviewedBySurgeon == "") {
                        $hidd_sxPlanRvwBySrgnDtm = "";
                    }
                    $vrfyBySrgn = "verifiedbySurgeon = '" . $json->TimeOut->Surgeon->name . "', sxPlanReviewedBySurgeon = '" . $sxPlanReviewedBySurgeon . "', sxPlanReviewedBySurgeonDateTime = '" . $hidd_sxPlanRvwBySrgnDtm . "', ";
                }
                $vrfyByAns = "";
                $vrfyByAnsName = "";
                if ($ViewUserTypeRow->user_type == "Anesthesiologist") {
                    $vrfyByAns = "verifiedbyAnesthesiologist='" . $verifiedbyAnesthesiologist . "',";
                    $vrfyByAnsName = "verifiedbyAnesthesiologistName='" . $verifiedbyAnesthesiologistName . "', ";
                }
                $vrfyByNurse = "";
                $vrfyByNurseName = "";
                if ($ViewUserTypeRow->user_type == "Nurse") {
                    $vrfyByNurse = "verifiedbyNurse='" . $json->TimeOut->Nurse->checked . "',";
                    $vrfyByNurseName = "verifiedbyNurseName='" . $json->TimeOut->Nurse->name . "',";
                }

                $chkOpRoomRecordQry = "select form_status, iol_serial_number from `operatingroomrecords` where  confirmation_id = '" . $pConfirmId . "'";
                $chkFormStatusRow = DB::selectone($chkOpRoomRecordQry); // or die(imw_error());
                if ($chkFormStatusRow) {
                    //CODE START TO CHECK FORM STATUS (IF EMPTY THEN REFRESH SLIDER ON SAVE)
                    $chk_form_status = $chkFormStatusRow->form_status;
                    $previous_iol_serial_number = trim($chkFormStatusRow->iol_serial_number);
                    //CODE START TO CHECK FORM STATUS (IF EMPTY THEN REFRESH SLIDER ON SAVE)
                    //START CODE FOR ITEM DETAILS IN SUPPLIES
                    if ($iol_serial_number) {
                        $itemDetailQry = "UPDATE predefine_suppliesused_item_detail SET used_status = '1' WHERE TRIM(serial_number) ='" . $iol_serial_number . "' AND serial_number!='' AND used_status = '0'";
                        $itemDetailRes = DB::select($itemDetailQry); // or die(imw_error());
                    }
                    if ($previous_iol_serial_number && $previous_iol_serial_number != $iol_serial_number) {
                        //RESET PREVIOUS SERIAL NUMBER IN Admin (IF PREVIOUS SERIAL NUMBER IS DIFFERENT FROM CURRENT SERIAL NUMBER)
                        $prevItemDetailQry = "UPDATE predefine_suppliesused_item_detail SET used_status = '0' WHERE TRIM(serial_number) ='" . $previous_iol_serial_number . "' AND serial_number!='' AND used_status = '1'";
                        $prevItemDetailRes = DB::select($prevItemDetailQry); // or die(imw_error());
                    }
                    //END CODE FOR ITEM DETAILS IN SUPPLIES

                    $SaveOpRoomRecordQry = "update `operatingroomrecords` set 									
									procedureSecondaryVerified = '$procedureSecondaryVerified',
									$vrfyByNurse
									$vrfyByNurseName
									$vrfyBySrgn
									$vrfyByAns
									$vrfyByAnsName
									verifiedbyNurseTime = '$verifiedbyNurseTime',
									preOpDiagnosis = '$preOpDiagnosis', 
									operativeProcedures = '$operativeProcedures', 
									product_control_na = '$product_control_na',
									bssValue = '$bssValue',
									iol_na = '$iol_na',
									Epinephrine03 = '$Epinephrine03',
									Vancomycin01 = '$Vancomycin01',
									Vancomycin02 = '$Vancomycin02',
									omidria	=	'$omidria',
									InfusionOtherChk = '$InfusionOtherChk',
									infusionBottleOther = '$infusionBottleOther',
									OtherSuppliesUsed = '$OtherSuppliesUsed', 
									percent_txt = '$percent_txt',
									percent = '$percent',
									manufacture = '$manufacture',
									lensBrand 	= '$lensBrand',
									iol_comments 	= '$iol_comments',
									model = '$model', 
									Diopter = '$Diopter',
									iolConfirmedSurgeonSignOnFile = '$iolConfirmedSurgeonSignOnFile', 
									prep_solution_na = '$prep_solution_na',
									Betadine = '$Betadine', 
									Saline = '$Saline',
									Alcohol = '$Alcohol',
									Prcnt5Betadinegtts = '$Prcnt5Betadinegtts', 
									proparacaine = '$proparacaine',
									tetracaine = '$tetracaine',
									tetravisc = '$tetravisc',
									prepSolutionsOther = '$prepSolutionsOther', 
									surgeryORNumber ='" . $json->Surgery->Room . "', 
									surgeryTimeIn = '" . $json->Surgery->InRoomTime . "', 
									surgeryStartTime = '" . $json->Surgery->SurgeryStartTime . "',
									surgeryEndTime = '" . $json->Surgery->SurgeryEndTime . "',
									surgeryTimeOut = '" . $json->Surgery->OutofRoom . "',
									anesStartTime='',
									anesEndTime ='',
									pillow_under_knees = '$pillow_under_knees',
									head_rest = '$head_rest',
									safetyBeltApplied = '$safetyBeltApplied',
									other_position = '$other_position',
									surgeryPatientPositionOther = '$surgeryPatientPositionOther', 
									Solumedrol = '$Solumedrol', 
									Dexamethasone = '$Dexamethasone', 
									Kenalog = '$Kenalog',
									Vancomycin = '$Vancomycin', 
									Trimaxi = '$Trimaxi', 
									injXylocaineMPF = '$injXylocaineMPF',
									injMiostat = '$injMiostat',
									PhenylLido = '$PhenylLido', 
									Ancef = '$Ancef',
									Gentamicin = '$Gentamicin',
									Depomedrol = '$Depomedrol', 
									postOpInjOther = '$postOpInjOther', 
									SolumedrolList = '$SolumedrolList',
									DexamethasoneList = '$DexamethasoneList',
									KenalogList = '$KenalogList',
									VancomycinList = '$VancomycinList',
									TrimaxiList = '$TrimaxiList',
									injXylocaineMPFList = '$injXylocaineMPFList',
									injMiostatList = '$injMiostatList',
									PhenylLidoList = '$PhenylLidoList',
									AncefList = '$AncefList',
									GentamicinList = '$GentamicinList',
									DepomedrolList = '$DepomedrolList',
									anesthesia_service = '$anesthesia_service', 
									TopicalBlock = '$TopicalBlock', 
									patch = '$patch', 
									shield = '$shield',
									needleSutureCount = '$needleSutureCount',
									needleSutureCountNA = '$needleSutureCountNA',
									collagenShield = '$collagenShield', 
									Econopred = '$Econopred',
									Zymar = '$Zymar',
									Tobradax = '$Tobradax',
									soakedInOtherChk = '$soakedInOtherChk',
									soakedInOther = '$soakedInOther',
									postOpDiagnosis = '$postOpDiagnosis',
									other_remain = '$other_remain', 
									postOpDrops = '$postOpDrops',
									nurseNotes = '$nurseNotes',
									intraOpPostOpOrder = '$intraOpPostOpOrder',
									surgeonId1 = '$surgeonId1', 
									anesthesiologistId = '$anesthesiologistId', 
									scrubTechId1 = '$scrubTechId1',
									scrubTechOther1 = '$scrubTechOther1',
									scrubTechId2 = '$scrubTechId2',
									scrubTechOther2 = '$scrubTechOther2',
									circulatingNurseId = '$circulatingNurseId',
									nurseTitle = '$NurseTitle',
									nurseId = '$NurseId',
									iol_serial_number='$iol_serial_number', ";

                    // if ($iolImg) {
                    // $SaveOpRoomRecordQry .= "iol_ScanUpload = '$iolImg',iol_type = '$iol_type',";
                    //  }
                    $SaveOpRoomRecordQry .= "form_status ='" . $form_status . "',
									save_manual = '1',									
									confirmation_id = '" . $pConfirmId . "', 
									patient_id = '" . $patient_id . "',
									start_time_status = '" . $start_time_staus . "'
									" . $vitalSignGridQuery . "
									" . $versionNumQry . "
									WHERE confirmation_id='" . $pConfirmId . "'";
                } else {
                    $SaveOpRoomRecordQry = "insert into `operatingroomrecords` set 									
								    procedureSecondaryVerified = '$procedureSecondaryVerified',
									$vrfyByNurse
									$vrfyByNurseName
									$vrfyBySrgn
									$vrfyByAns
									$vrfyByAnsName
									verifiedbyNurseTime = '$verifiedbyNurseTime',
									preOpDiagnosis = '$preOpDiagnosis', 
									operativeProcedures = '$operativeProcedures', 
									product_control_na = '$product_control_na',
									bssValue = '$bssValue',
									iol_na = '$iol_na',
									Epinephrine03 = '$Epinephrine03',
									Vancomycin01 = '$Vancomycin01',
									Vancomycin02 = '$Vancomycin02',
									omidria	=	'$omidria',
									InfusionOtherChk = '$InfusionOtherChk',
									infusionBottleOther = '$infusionBottleOther',
									OtherSuppliesUsed = '$OtherSuppliesUsed', 
									percent_txt = '$percent_txt',
									percent = '$percent',
									manufacture = '$manufacture',
									lensBrand 	= '$lensBrand',
									iol_comments 	= '$iol_comments',
									model = '$model', 
									Diopter = '$Diopter',
									iolConfirmedSurgeonSignOnFile = '$iolConfirmedSurgeonSignOnFile', 
									prep_solution_na = '$prep_solution_na',
									Betadine = '$Betadine', 
									Saline = '$Saline',
									Alcohol = '$Alcohol',
									Prcnt5Betadinegtts = '$Prcnt5Betadinegtts', 
									proparacaine = '$proparacaine',
									tetracaine = '$tetracaine',
									tetravisc = '$tetravisc',
									prepSolutionsOther = '$prepSolutionsOther', 
									surgeryORNumber ='" . $json->Surgery->Room . "', 
									surgeryTimeIn = '" . $json->Surgery->InRoomTime . "', 
									surgeryStartTime = '" . $json->Surgery->SurgeryStartTime . "',
									surgeryEndTime = '" . $json->Surgery->SurgeryEndTime . "',
									surgeryTimeOut = '" . $json->Surgery->OutofRoom . "',
									anesStartTime='$anesStartTime',
									anesEndTime ='$anesEndTime',
									pillow_under_knees = '$pillow_under_knees',
									head_rest = '$head_rest',
									safetyBeltApplied = '$safetyBeltApplied',
									other_position = '$other_position',
									surgeryPatientPositionOther = '$surgeryPatientPositionOther', 
									Solumedrol = '$Solumedrol', 
									Dexamethasone = '$Dexamethasone', 
									Kenalog = '$Kenalog',
									Vancomycin = '$Vancomycin', 
									Trimaxi = '$Trimaxi', 
									injXylocaineMPF = '$injXylocaineMPF',
									injMiostat = '$injMiostat',
									PhenylLido = '$PhenylLido', 
									Ancef = '$Ancef',
									Gentamicin = '$Gentamicin',
									Depomedrol = '$Depomedrol', 
									postOpInjOther = '$postOpInjOther', 
									SolumedrolList = '$SolumedrolList',
									DexamethasoneList = '$DexamethasoneList',
									KenalogList = '$KenalogList',
									VancomycinList = '$VancomycinList',
									TrimaxiList = '$TrimaxiList',
									injXylocaineMPFList = '$injXylocaineMPFList',
									injMiostatList = '$injMiostatList',
									PhenylLidoList = '$PhenylLidoList',
									AncefList = '$AncefList',
									GentamicinList = '$GentamicinList',
									DepomedrolList = '$DepomedrolList',
									anesthesia_service = '$anesthesia_service', 
									TopicalBlock = '$TopicalBlock', 
									patch = '$patch', 
									shield = '$shield',
									needleSutureCount = '$needleSutureCount',
									needleSutureCountNA = '$needleSutureCountNA',
									collagenShield = '$collagenShield', 
									Econopred = '$Econopred',
									Zymar = '$Zymar',
									Tobradax = '$Tobradax',
									soakedInOtherChk = '$soakedInOtherChk',
									soakedInOther = '$soakedInOther',
									postOpDiagnosis = '$postOpDiagnosis',
									other_remain = '$other_remain', 
									postOpDrops = '$postOpDrops',
									nurseNotes = '$nurseNotes',
									intraOpPostOpOrder = '$intraOpPostOpOrder',
									surgeonId1 = '$surgeonId1', 
									anesthesiologistId = '$anesthesiologistId', 
									scrubTechId1 = '$scrubTechId1',
									scrubTechOther1 = '$scrubTechOther1',
									scrubTechId2 = '$scrubTechId2',
									scrubTechOther2 = '$scrubTechOther2',
									circulatingNurseId = '$circulatingNurseId',
									nurseTitle = '$NurseTitle',
									nurseId = '$NurseId',
									iol_serial_number='$iol_serial_number',";
                    // if ($iolImg) {
                    //$SaveOpRoomRecordQry .= "iol_ScanUpload = '$iolImg', 
                    //												iol_type = '$iol_type', ";
                    // }
                    $SaveOpRoomRecordQry .= "form_status ='" . $form_status . "',									
									save_manual = '1',
									confirmation_id = '" . $pConfirmId . "', 
									patient_id = '" . $patient_id . "',
									start_time_status = '" . $start_time_staus . "'
									" . $vitalSignGridQuery . "
									" . $versionNumQry . "
									";
                }

                /*  print '<pre>';
                  print_r($json);
                  echo $SaveOpRoomRecordQry; */
                $SaveOpRoomRecordRes = DB::select($SaveOpRoomRecordQry); // or die(imw_error());
                //SAVE ENTRY IN chartnotes_change_audit_tbl 
                $Allergies_data = isset($json->Allergies_data) ? $json->Allergies_data : [];
                $this->patient_allergy_save($Allergies_data, $pConfirmId, $patient_id, $userId, $loggedInUserName);
                $Medication_data = isset($json->Medication_data) ? $json->Medication_data : [];
                $this->patient_medication_save($Medication_data, $pConfirmId, $patient_id, $userId, $loggedInUserName);
                $Supplies_data = isset($json->Product_Control->supplies) ? $json->Product_Control->supplies : [];
                $this->supplies_data_save($Supplies_data, $pConfirmId, $patient_id, $userId, $loggedInUserName);
                if ($operatingRoomRecordsId > 0) {
                    $ImageController=new ImageController;
                    $ImageController->saveImageOperatingRoomRecord($operatingRoomRecordsId, $iol_ScanUpload, $iol_ScanUpload2, $pConfirmId, $patient_id);
                }
                $fieldName = "intra_op_record_form";
                $chkAuditChartNotesQry = "select * from `chartnotes_change_audit_tbl` where 
									user_id='" . $userId . "' AND
									patient_id='" . $patient_id . "' AND
									confirmation_id='" . $pConfirmId . "' AND
									form_name='" . $fieldName . "' AND
									status = 'created'";

                $chkAuditChartNotesRes = DB::select($chkAuditChartNotesQry); // or die(imw_error());

                if ($chkAuditChartNotesRes) {
                    $SaveAuditChartNotesQry = "insert into `chartnotes_change_audit_tbl` set 
										user_id='" . $userId . "',
										patient_id='" . $patient_id . "',
										confirmation_id='" . $pConfirmId . "',
										form_name='$fieldName',
										status='modified',
										action_date_time='" . date("Y-m-d H:i:s") . "'";
                } else {
                    $SaveAuditChartNotesQry = "insert into `chartnotes_change_audit_tbl` set 
										user_id='" . $userId . "',
										patient_id='" . $patient_id . "',
										confirmation_id='" . $pConfirmId . "',
										form_name='$fieldName',
										status='created',
										action_date_time='" . date("Y-m-d H:i:s") . "'";
                }
                $SaveAuditChartNotesRes = DB::select($SaveAuditChartNotesQry); // or die(imw_error());
                //END SAVE ENTRY IN chartnotes_change_audit_tbl
                //CODE TO CHECK SIGNATURE OF SURGEON,ANESTHESIOLOGIST,NURSE IN ALL CHARTS AND SET VALUE(red,green,blank) IN STUB TABLE
                $recentChartSaved = "";
                $recentChartSavedQry = "";

                if (trim($surgeryTimeIn)) {
                    $recentChartSavedQry = ", recentChartSaved = 'operatingroomrecords' ";
                }

                $chartSignedBySurgeon = $this->chkSurgeonSignNew($pConfirmId);
                $chartSignedByAnes = $this->chkAnesSignNew($pConfirmId);
                $chartSignedByNurse = $this->chkNurseSignNew($pConfirmId);
                $updateStubTblQry = "UPDATE stub_tbl SET chartSignedBySurgeon='" . $chartSignedBySurgeon . "', chartSignedByAnes='" . $chartSignedByAnes . "', chartSignedByNurse='" . $chartSignedByNurse . "' " . $recentChartSavedQry . " WHERE patient_confirmation_id='" . $pConfirmId . "' AND patient_confirmation_id!='0'";
                $updateStubTblRes = DB::select($updateStubTblQry); // or die(imw_error());
                //END CODE TO CHECK SIGNATURE OF SURGEON,ANESTHESIOLOGIST,NURSE IN ALL CHARTS AND SET VALUE(red,green,blank) IN STUB TABLE

                if ($vitalSignGridStatus) {
                    // Code start here to save vital sign grid data 
                    $vitalSignGridRecordIdArr = $json->Vital_Signs; // $_POST['vitalSignGridRecordId'];
                    if (is_array($vitalSignGridRecordIdArr) && count($vitalSignGridRecordIdArr) > 0) {
                        foreach ($vitalSignGridRecordIdArr as $gridRowId) {
                            $vTime = $gridRowId->TimeB;
                            $vSystolic = $gridRowId->Systolic;
                            $vDiastolic = $gridRowId->Diastolic;
                            $vPulse = $gridRowId->Pulse;
                            $vRR = $gridRowId->RR;
                            $vTemp = $gridRowId->Temp;
                            $vEtco2 = $gridRowId->EtCO2;
                            $vosat2 = $gridRowId->OSat;
                            $gridId = $gridRowId->gridId;
                            $vTime = $this->setTmFormat($vTime);
                            if ($vTime <> "" && ($vSystolic <> "" || $vDiastolic <> "" || $vPulse <> "" || $vRR <> "" || $vTemp <> "" || $vEtco2 <> "" || $vosat2 <> "")) {
                                $dataArray = array();
                                $dataArray['chartName'] = 'intra_op_record_form';
                                $dataArray['confirmation_id'] = $pConfirmId;
                                $dataArray['start_time'] = $vTime;
                                $dataArray['systolic'] = $vSystolic;
                                $dataArray['diastolic'] = $vDiastolic;
                                $dataArray['pulse'] = $vPulse;
                                $dataArray['rr'] = $vRR;
                                $dataArray['temp'] = $vTemp;
                                $dataArray['etco2'] = $vEtco2;
                                $dataArray['osat2'] = $vosat2;
                                if ($gridId > 0) {
                                    DB::table('vital_sign_grid')->where('gridRowId', $gridId)->update($dataArray);
                                } else {
                                    $chkRecords = $this->getMultiChkArrayRecords('vital_sign_grid', $dataArray);
                                    if (!$chkRecords)
                                        DB::table('vital_sign_grid')->insertGetId($dataArray);
                                }
                            }
                            else {
                                if ($gridId) {
                                    DB::table('vital_sign_grid')->where('gridRowId', $gridId)->delete();
                                }
                            }
                        }
                    }
                    // Code end here to save vital sign grid data 
                }
                //END SAVE RECORD TO DATABASE
                $status = 1;
                $savedStatus = 1;
                $data=[];
                $message = " Operating Room Record /Intra OP Saved Successfully ! ";
            }
        }
        return response()->json(['status' => $status, 'savedStatus' => $savedStatus, 'message' => $message, 'requiredStatus' => '', 'data' => $data,
                    'operatingRoomRecordsId' => isset($chkUserSignDetails->operatingRoomRecordsId) ? $chkUserSignDetails->operatingRoomRecordsId : 0,
                    "version_num" => $chkUserSignDetails->version_num,
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

    public function IntraOpRecord_form(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $loginUserType = $request->json()->get('UserType') ? $request->json()->get('UserType') : $request->input('UserType');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $data = [];
        $status = 0;
        $moveStatus = 0;
        // $requiredStatus = 1;
        $message = " unauthorized ";
        $requiredStatus = [];
        $userId = $this->checkToken($userToken);
        $patient_instruction_id = 0;
        $andConsentIdQry = '';
        $ampConsentAutoId = '';
        $surgery_consent_data = "";
        $signStatus = [];
        $consent_content = '';
        $left_list = '';
        $stat = [];
        $surgery_consent_id = 0;

        if ($userId > 0) {
            if ($pConfirmId == "") {
                $message = " ConfirmId is missing ";
                $status = 1;
                $requiredStatus = 0;
            } elseif ($facility_id == "") {
                $message = " FacilityId is missing ";
                $status = 1;
                $requiredStatus = 0;
            } else if ($loginUserType == "") {
                $message = " UserType is missing ";
                $status = 1;
                $requiredStatus = 0;
            } else if ($iasc_facility_id == "") {
                $status = 1;
                $message = " IASC Id is missing ";
            } else {
                $tablename = "operatingroomrecords";
                $configurationDetail = $this->getExtractRecord('surgerycenter', 'surgeryCenterId', '1', 'suppliesHostName, suppliesUsername, suppliesPassword');
                $suppliesHostName = $configurationDetail[0]->suppliesHostName;
                $suppliesUsername = $configurationDetail[0]->suppliesUsername;
                $suppliesPassword = $configurationDetail[0]->suppliesPassword;
                $ftpSupp = false;
                if (trim($suppliesHostName) && trim($suppliesUsername) && trim($suppliesPassword)) {
                    $ftpSupp = true;
                }
                $res = DB::selectone("select *, date_format(signNurseDateTime,'%m-%d-%Y %h:%i %p') as signNurseDateTimeFormat, date_format(signNurse1DateTime,'%m-%d-%Y %h:%i %p') as signNurse1DateTimeFormat from `operatingroomrecords` where  confirmation_id='" . $pConfirmId . "'");
                // print '<pre>';
                //print_r($res);
                $allergy1 = "select * from allergies ORDER BY `allergies`.`name` ASC";
                $allergic = DB::select($allergy1);
                $detailConfirmation = $this->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfirmId);
                $finalizeStatus = $detailConfirmation->finalize_status;
                $allergiesNKDA_patientconfirmation_status = $detailConfirmation->allergiesNKDA_status;
                $noMedicationStatus = $detailConfirmation->no_medication_status;
                $noMedicationComments = $detailConfirmation->no_medication_comments;

                $patientAllergies = [];
                $cntHlt = 0;
                $patient_allergies_tblQry = "SELECT pre_op_allergy_id,allergy_name,reaction_name FROM `patient_allergies_tbl` WHERE `patient_confirmation_id` = '$pConfirmId'";
                $patient_allergies_tblRes = DB::select($patient_allergies_tblQry);
                if ($patient_allergies_tblRes) {
                    foreach ($patient_allergies_tblRes as $patient_allergies_tblRow) {
                        $patientAllergies[] = ['pre_op_allergy_id' => $patient_allergies_tblRow->pre_op_allergy_id, 'name' => $patient_allergies_tblRow->allergy_name, 'reaction' => $patient_allergies_tblRow->reaction_name];
                        $cntHlt++;
                    }
                    for ($i = $cntHlt; $i < ($cntHlt + 20); $i++) {
                        $patientAllergies[] = ['pre_op_allergy_id' => 0, 'name' => '', 'reaction' => ''];
                    }
                } else {
                    for ($i = 0; $i < 20; $i++) {
                        $patientAllergies[] = ['pre_op_allergy_id' => 0, 'name' => '', 'reaction' => ''];
                    }
                }

                $patient_prescription_medication_healthquest_qry = "select prescription_medication_id,prescription_medication_name,prescription_medication_desc,prescription_medication_sig from patient_prescription_medication_tbl where confirmation_id= '$pConfirmId' order by prescription_medication_name ASC";
                $patient_prescription_medication_healthquest_res = DB::select($patient_prescription_medication_healthquest_qry);
                $patient_prescriptions = [];
                if ($patient_prescription_medication_healthquest_res) {
                    $cnt = 0;
                    foreach ($patient_prescription_medication_healthquest_res as $rows) {
                        $patient_prescriptions[] = ['prescription_medication_id' => $rows->prescription_medication_id, 'prescription_medication_name' => $rows->prescription_medication_name, 'prescription_medication_desc' => $rows->prescription_medication_desc, 'prescription_medication_sig' => $rows->prescription_medication_sig];
                        $cnt++;
                    }
                    for ($i = $cnt; $i < ($cnt + 20); $i++) {
                        $patient_prescriptions[] = ['prescription_medication_id' => 0, 'prescription_medication_name' => '', 'prescription_medication_desc' => '', 'prescription_medication_sig' => ''];
                    }
                } else {
                    for ($i = 0; $i < 20; $i++) {
                        $patient_prescriptions[] = ['prescription_medication_id' => 0, 'prescription_medication_name' => '', 'prescription_medication_desc' => '', 'prescription_medication_sig' => ''];
                    }
                }
                $medication_query = "SELECT medicationsId,name,isDefault FROM `medications` ORDER BY `medications`.`name` ASC ";
                $medication_res = DB::select($medication_query);
                $medication = [];
                foreach ($medication_res as $row) {
                    $medication[] = ['medicationsId' => $row->medicationsId, 'name' => $row->name, 'isDefault' => $row->isDefault];
                }
                $High_Cholesterol = '';
                $Thyroid = '';
                $visible1 = 0;
                $visible = 1;
                $visible2 = 0;
                $visible3 = 0;
                $str = '';
                $str2 = '';
                $str3 = '';
                $condArr = array();
                $condArr['confirmation_id'] = $pConfirmId;
                $condArr['chartName'] = 'intra_op_record_form';
                $gridData = $this->getMultiChkArrayRecords('vital_sign_grid', $condArr, 'gridRowId', 'Asc');
                $gCounter = 1;
                $gridDataFilled = [];
                $cnt = 0;
                if (is_array($gridData)) {
                    foreach ($gridData as $gridRow) {
                        //$fieldId1 = 'vitalSignGrid_'.$gCounter.'_1' ;	$fieldValue1= date('h:i A', strtotime($gridRow->start_time));
                        $fieldId1 = 'vitalSignGrid_' . $gCounter . '_1';
                        $fieldValue1 = $this->getTmFormat($gridRow->start_time);
                        $fieldId2 = 'vitalSignGrid_' . $gCounter . '_2';
                        $fieldValue2 = $gridRow->systolic;
                        $fieldId3 = 'vitalSignGrid_' . $gCounter . '_3';
                        $fieldValue3 = $gridRow->diastolic;
                        $fieldId4 = 'vitalSignGrid_' . $gCounter . '_4';
                        $fieldValue4 = $gridRow->pulse;
                        $fieldId5 = 'vitalSignGrid_' . $gCounter . '_5';
                        $fieldValue5 = $gridRow->rr;
                        $fieldId6 = 'vitalSignGrid_' . $gCounter . '_6';
                        $fieldValue6 = $gridRow->temp;
                        $fieldId7 = 'vitalSignGrid_' . $gCounter . '_7';
                        $fieldValue7 = $gridRow->etco2;
                        $fieldId8 = 'vitalSignGrid_' . $gCounter . '_8';
                        $fieldValue8 = $gridRow->osat2;
                        $gridDataFilled[] = array('id' => $gridRow->gridRowId, 'start_time' => $fieldValue1, 'systolic' => $fieldValue2, 'diastolic' => $fieldValue3, 'pulse' => $fieldValue4, 'rr' => $fieldValue5, 'temp' => $fieldValue6, 'etco2' => $fieldValue7, 'osat2' => $fieldValue8);
                        $cnt++;
                    }
                    if ($cnt < 15) {
                        for ($i = $cnt; $i < 15; $i++) {
                            $gridDataFilled[] = array('start_time' => '', 'systolic' => '', 'diastolic' => '', 'pulse' => '', 'rr' => '', 'temp' => '', 'etco2' => '', 'osat2' => '');
                        }
                    }
                    for ($i = $cnt; $i < ($cnt + 15); $i++) {
                        $gridDataFilled[] = array('start_time' => '', 'systolic' => '', 'diastolic' => '', 'pulse' => '', 'rr' => '', 'temp' => '', 'etco2' => '', 'osat2' => '');
                    }
                } else {
                    for ($i = $cnt; $i < 15; $i++) {
                        $gridDataFilled[] = array('start_time' => '', 'systolic' => '', 'diastolic' => '', 'pulse' => '', 'rr' => '', 'temp' => '', 'etco2' => '', 'osat2' => '');
                    }
                }
                $OpRoom_patientName_tblQry = "SELECT patient_fname,patient_mname,patient_lname FROM `patient_data_tbl` WHERE `patient_id` = '" . $patient_id . "'";
                $OpRoom_patientName_tblRow = DB::selectone($OpRoom_patientName_tblQry); // or die(imw_error());
                $OpRoom_patientName = $OpRoom_patientName_tblRow->patient_fname . " " . $OpRoom_patientName_tblRow->patient_mname . " " . $OpRoom_patientName_tblRow->patient_lname;
                $OpRoom_patientConfirm_tblQry = "SELECT * FROM `patientconfirmation` WHERE `patientConfirmationId` = '" . $pConfirmId . "'";
                $OpRoom_patientConfirm_tblRow = DB::selectone($OpRoom_patientConfirm_tblQry); // or die(imw_error());

                $OpRoom_patientConfirmDosTemp = $OpRoom_patientConfirm_tblRow->dos;
                $OpRoom_patientConfirmDos_split = explode("-", $OpRoom_patientConfirmDosTemp);
                $OpRoom_patientConfirmDos = $OpRoom_patientConfirmDos_split[1] . "-" . $OpRoom_patientConfirmDos_split[2] . "-" . $OpRoom_patientConfirmDos_split[0];
                $OpRoom_patientConfirmSurgeon = $OpRoom_patientConfirm_tblRow->surgeon_name;
                $OpRoom_patientConfirmSiteTemp = $OpRoom_patientConfirm_tblRow->site;
                $OpRoom_patientConfirmAnes_NA = $OpRoom_patientConfirm_tblRow->anes_NA;
                // APPLYING NUMBERS TO PATIENT SITE
                if ($OpRoom_patientConfirmSiteTemp == 1) {
                    $OpRoom_patientConfirmSite = "Left Eye";  //OD
                } else if ($OpRoom_patientConfirmSiteTemp == 2) {
                    $OpRoom_patientConfirmSite = "Right Eye";  //OS
                } else if ($OpRoom_patientConfirmSiteTemp == 3) {
                    $OpRoom_patientConfirmSite = "Both Eye";  //OU
                } else if ($OpRoom_patientConfirmSiteTemp == 4) {
                    $OpRoom_patientConfirmSite = "Left Upper Lid";
                } else if ($OpRoom_patientConfirmSiteTemp == 5) {
                    $OpRoom_patientConfirmSite = "Left Lower Lid";
                } else if ($OpRoom_patientConfirmSiteTemp == 6) {
                    $OpRoom_patientConfirmSite = "Right Upper Lid";
                } else if ($OpRoom_patientConfirmSiteTemp == 7) {
                    $OpRoom_patientConfirmSite = "Right Lower Lid";
                } else if ($OpRoom_patientConfirmSiteTemp == 8) {
                    $OpRoom_patientConfirmSite = "Bilateral Upper Lid";
                } else if ($OpRoom_patientConfirmSiteTemp == 9) {
                    $OpRoom_patientConfirmSite = "Bilateral Lower Lid";
                }
                // END APPLYING NUMBERS TO PATIENT SITE
                $OpRoom_patientConfirmPrimProc = $OpRoom_patientConfirm_tblRow->patient_primary_procedure;
                $OpRoom_patientConfirmSecProc = $OpRoom_patientConfirm_tblRow->patient_secondary_procedure;
                if ($OpRoom_patientConfirmSecProc != "N/A") {
                    $OpRoom_patientConfirmSecProcTemp = "Yes";
                } else {
                    $OpRoom_patientConfirmSecProcTemp = " ";
                }
                $OpRoom_patientConfirmAnesthesiologistId = $OpRoom_patientConfirm_tblRow->anesthesiologist_id;
                $OpRoom_patientConfirmNurseId = $OpRoom_patientConfirm_tblRow->nurseId;
                $OpRoom_patientConfirmSurgeonId = $OpRoom_patientConfirm_tblRow->surgeonId;
                $OpRoom_patientConfirmAnesthesiologistName = trim(stripslashes($OpRoom_patientConfirm_tblRow->anesthesiologist_name));

                //GET ANESTHESIOLOGIST NAME, NURSE NAME, SURGEON NAME 
                //$OpRoomAnesthesiologistName = getUserName($OpRoom_patientConfirmAnesthesiologistId,'Anesthesiologist');
                $OpRoomAnesthesiologistName = $this->getUserName($userId, 'Anesthesiologist');
                $OpRoomNurseName = $this->getUserName($userId, 'Nurse');
                $OpRoomSurgeonName = $this->getUserName($OpRoom_patientConfirmSurgeonId, 'Surgeon');
                $OperativeProcedure = DB::select("select proceduresCategoryId, name from procedurescategory where del_status != 'yes' order by `name`");
                $OperativeProcedures = [];
                foreach ($OperativeProcedure as $OperativeProcedre) {
                    $qry_procedure = "select procedureId, name, catid from procedures where procedures.catid =" . $OperativeProcedre->proceduresCategoryId . " and del_status!='yes' order by `name` ";
                    $subres = DB::select($qry_procedure);
                    $OperativeProcedures[] = ["proceduresCategoryId" => $OperativeProcedre->proceduresCategoryId, 'name' => $OperativeProcedre->name, "sublist" => $subres];
                }
                $scrubTechstr = "";
                $scrubTecharr = array();
                $scrubNameQry = "select * from `users` where user_type ='Nurse' OR user_type ='Scrub Technician' ORDER BY lname";
                $scrubNameRes = DB::select($scrubNameQry); // or die(imw_error());
                $scrubNameRows = [];
                $scrubNameRows2 = [];
                $scrubTecharr = [];
                foreach ($scrubNameRes as $scrubNameRow) {
                    $scrubTechID = $scrubNameRow->usersId;
                    $scrubName = $scrubNameRow->lname . ", " . $scrubNameRow->fname . " " . $scrubNameRow->mname;
                    $scrubsign = $scrubNameRow->signature;
                    $scrubisSigned = (!empty($scrubsign)) ? "Yes" : "No";
                    $scrubTechstr .= "\"" . $scrubTechID . "\":\"" . $scrubisSigned . "\",";
                    //$scrubTecharr[$scrubTechID] = $scrubisSigned;
                    $scrubTecharr[$scrubTechID] = $scrubName;
                    if ($scrubNameRow->deleteStatus <> 'Yes' || $res->scrubTechId1 == $scrubTechID) {
                        $scrubNameRows[] = ["scrubName" => $scrubName, "scrubTechID" => $scrubTechID, 'status' => $scrubisSigned];
                    }
                    if ($scrubNameRow->deleteStatus <> 'Yes' || $res->scrubTechId2 == $scrubTechID) {
                        $scrubNameRows2[] = ["scrubName" => $scrubName, "scrubTechID" => $scrubTechID, 'status' => $scrubisSigned];
                    }
                }
                $Nursestr = "";
                $Nursearr = array();
                $Nursearrs = [];
                $NurseNameQry = "select * from `users` where user_type ='Nurse' ORDER BY lname";
                $NurseNameRes = DB::select($NurseNameQry); // or die(imw_error());
                foreach ($NurseNameRes as $NurseNameRow) {
                    $getNurseID = $NurseNameRow->usersId;
                    $NurseName = $NurseNameRow->lname . ", " . $NurseNameRow->fname . " " . $NurseNameRow->mname;
                    $Nursesign = $NurseNameRow->signature;
                    $NurseisSigned = (!empty($Nursesign)) ? "Yes" : "No";
                    $Nursestr .= "\"" . $getNurseID . "\":\"" . $NurseisSigned . "\",";
                    //$Nursearr[$getNurseID] = $NurseisSigned;
                    if ($NurseNameRow->deleteStatus <> 'Yes' || $NurseId == $getNurseID) {
                        $Nursearrs[$getNurseID] = $NurseName;
                        $Nursearr[] = ['name' => $NurseName, 'nurseid' => $getNurseID, 'status' => $NurseisSigned]; //,'selectedNurse'=>(($res->nurseId==$getNurseID)?$NurseName:"")
                    }
                }
                $supplyArr = array("X1", "X2", "X3", "X4", "X5");
                $supplies = [];
                $subcat = [];
                $qry = "Select * From supply_categories";
                $res1 = DB::select($qry);
                foreach ($res1 as $ress) {
                    $res2 = DB::select("select name,suppliesUsedId from predefine_suppliesused where cat_id='" . $ress->id . "' order by name asc");
                    foreach ($res2 as $res23) {
                        $res3 = DB::select("select suppRecordId as operatingroomrecords_supplies_id,suppName from operatingroomrecords_supplies where predefine_supp_id='" . $res23->suppliesUsedId . "'");
                        $subcat[] = ['name' => $res23->name, 'suppliesUsedId' => $res23->suppliesUsedId, 'selected' => $res3 ? 1 : 0];
                    }
                    $supplies[] = ['cat_id' => $ress->id, 'cat_name' => $ress->name, 'subcat' => $subcat];
                }
                //print_r()
                // Code to get & list all in use supplies from operatingroomrecords_supplies table
                $condArray = array();
                $condArray['confirmation_id'] = $pConfirmId;
                $condArray['displayStatus'] = 1;
                $suppliesUsed = $this->getMultiChkArrayRecords('operatingroomrecords_supplies', $condArray, 'suppName', 'Asc');
                $suppliesCounter = 0;
                $selectedData = [];
                if (is_array($suppliesUsed) && count($suppliesUsed) > 0) {
                    foreach ($suppliesUsed as $supply) {
                        $suppliesCounter++;
                        $chkBoxId = 'suppChkBox_' . $suppliesCounter;
                        $listBoxId = 'suppListBox_' . $suppliesCounter;
                        $supp_name = stripslashes($supply->suppName);
                        $div_name = preg_replace('#[ -]+#', '-', strtolower($supp_name));
                        $div_name = preg_replace('/[^A-Za-z0-9-]+/', '', $div_name);
                        //  echo $div_name.',';
                        $selectedData[] = ['suppRecordId' => $supply->suppRecordId, 'predefine_supp_id' => $supply->predefine_supp_id, 'title' => $div_name, 'xseries_sel' => $supply->suppList, 'checked' => $supply->suppChkStatus];
                    }
                }

                $data = [
                    "Allergy_data" => ['dropdown' => $allergic, 'patientAllergiesGrid' => $patientAllergies],
                    "Medications_data" => ['dropdown' => $medication, 'patient_prescriptions' => $patient_prescriptions],
                    "Surgery" => ["Room" => $res->surgeryORNumber, "InRoomTime" => $res->surgeryTimeIn, "SurgeryStartTime" => $res->surgeryStartTime, "SurgeryEndTime" => $res->surgeryEndTime, "OutofRoom" => $res->surgeryTimeOut],
                    "TimeOut" => [
                        "PatientIdentificationVerified" => $OpRoom_patientName,
                        "Nurse" => ["name" => stripslashes($res->verifiedbyNurseName), "checked" => $res->verifiedbyNurse],
                        "SiteVerified" => $OpRoom_patientConfirmSite,
                        "Surgeon" => ["name" => stripslashes($OpRoomSurgeonName), "checked" => $res->verifiedbySurgeon],
                        "ProcedureVerified" => wordwrap($OpRoom_patientConfirmPrimProc, 31, "\n", 1),
                        "AnesthesiaProvider" => ["name" => stripslashes($res->verifiedbyAnesthesiologistName), "checked" => $res->verifiedbyAnesthesiologist],
                        "SecondaryVerified" => wordwrap($OpRoom_patientConfirmSecProc, 31, "\n", 1),
                        "Time" => $res->verifiedbyNurseTime,
                        "SxPlanSheetReviewedBySurgeon" => ["name" => stripslashes($OpRoomSurgeonName), "checked" => $res->sxPlanReviewedBySurgeon],
                        "PreopDiagnosis" => ["dropdown" => DB::select("SELECT icd10_desc as name FROM icd10_data WHERE deleted ='0' AND icd10_desc !='' ORDER BY icd10_desc"), "detail" => stripslashes($res->preOpDiagnosis)],
                        "OperativeProcedure" => ["dropdown" => $OperativeProcedures, "detail" => stripslashes($res->operativeProcedures)],
                        "PostopDiagnosis" => ["dropdown" => DB::select("SELECT icd10_desc as name FROM icd10_data WHERE deleted ='0' AND icd10_desc !='' ORDER BY icd10_desc"), "detail" => stripslashes($res->postOpDiagnosis)],
                        "PostopOrders" => ["dropdown" => DB::select("select * from postopdrops order by `name`"), "detail" => stripslashes($res->postOpDrops)],
                    ],
                    "ProductControl" => [
                        "n_a" => $res->product_control_na,
                        "bss" => $res->bssValue == 'bss' ? 'bss' : '',
                        "bssplus" => $res->bssValue == 'bssPlus' ? 'bssPlus' : '',
                        "addedtoinfusion" => ["Epinephrine" => $res->Epinephrine03, "Vancomycin01" => $res->Vancomycin01, "Vancomycin02" => $res->Vancomycin02, "Omidria" => $res->omidria, "Other" => $res->InfusionOtherChk, "othertext" => stripslashes($res->infusionBottleOther)],
                        "supplies" => [
                            "supplyArr" => ["selectedData" => $selectedData],
                            "supplyUsed" => ["dropdown" => $supplies],
                            'xseries' => $supplyArr,
                        ],
                        "OtherSuppliesUsed" => $res->OtherSuppliesUsed,
                    ],
                    "PatientPosition" => ["PillowUnderKnee" => $res->pillow_under_knees, "HeadRest" => $res->head_rest, "SafetyBeltApplied" => $res->safetyBeltApplied, "Other" => $res->other_position, "othertext" => $res->surgeryPatientPositionOther],
                    "IntraOpInj1" => [
                        "Solumedrol" => ["checked" => $res->Solumedrol, "text" => $res->SolumedrolList],
                        "Dexamethasone" => ["checked" => $res->Dexamethasone, "text" => $res->DexamethasoneList],
                        "Kenalog" => ["checked" => $res->Kenalog, "text" => $res->KenalogList],
                        "Vancomycin" => ["checked" => $res->Vancomycin, "text" => $res->VancomycinList],
                        "XylocaineMPF" => ["checked" => $res->injXylocaineMPF, "text" => $res->injXylocaineMPFList],
                        "PhenylLido" => ["checked" => $res->PhenylLido, "text" => $res->PhenylLidoList],
                        "Ancef" => ["checked" => $res->Ancef, "text" => $res->AncefList],
                        "Gentamicin" => ["checked" => $res->Gentamicin, "text" => $res->GentamicinList],
                        "Depomedrol" => ["checked" => $res->Depomedrol, "text" => $res->DepomedrolList],
                        "TriMoxi" => ["checked" => $res->Trimaxi, "text" => $res->TrimaxiList],
                        "Miostat" => ["checked" => $res->injMiostat, "text" => $res->injMiostatList],
                    ],
                    "IntraOpInj2" => [
                        "Other" => stripslashes($res->postOpInjOther),
                        "Patch" => ["dropdown" => [["key" => "X1", "value" => "X1"], ["key" => "X2", "value" => "X2"], ["key" => "X3", "value" => "X3"]], "selected" => $res->patch],
                        "ShieldNeedleSuturecount" => $res->shield,
                        "Correct_Yes" => $res->needleSutureCount == 'Yes' ? 'Yes' : "",
                        "Correct_No" => $res->needleSutureCount == 'No' ? 'Yes' : "",
                        "Correct_NA" => $res->needleSutureCountNA == 'Yes' ? 'Yes' : "",
                        "PostOpOrder" => ["dropdown" => DB::select("SELECT intraOpId,name FROM `intra_op_post_op_order`"), "text" => stripslashes($res->intraOpPostOpOrder)],
                        "NurseNotes" => ["dropdown" => DB::select("select * from oproomnursenotes order by `notes` ASC"), "text" => stripslashes($res->nurseNotes)],
                    ],
                    "ElectronicallySigned1" => [
                        "ScrubTech1" => ["dropdown" => $scrubNameRows, "selectedId" => $res->scrubTechId1, 'selectedname' => @$scrubTecharr[$res->scrubTechId1], "othertext" => $res->scrubTechOther1],
                        "Nurse" => ["nurseTitle" => [["key" => "Supervising", "value" => "Supervising Nurse"], ["key" => "Circulating", "value" => "Circulating Nurse"], ["key" => "Relief", "value" => "Relief Nurse"]], 'dropdown' => $Nursearr, "selectedNurseName" => !empty($Nursearrs[$res->nurseId]) ? $Nursearrs[$res->nurseId] : "", "selectedNurseId" => $res->nurseId],
                        "surgeon_signature" => ["name" => $res->signSurgeon1FirstName . " " . $res->signSurgeon1LastName, "signed_status" => $res->signSurgeon1Status, "sign_date" => $res->signSurgeon1DateTime != '0000-00-00 00:00:00' ? date("m-d-Y h:i A", strtotime($res->signSurgeon1DateTime)) : ""],
                    ],
                    "IOL" => [
                        "n_a" => $res->iol_na,
                        "s_n" => $res->iol_serial_number,
                        "image" => ['iol_ScanUpload' => $res->iol_ScanUpload <> "" ? base64_encode($res->iol_ScanUpload) : "", 'iol_ScanUpload2' => $res->iol_ScanUpload2 <> "" ? base64_encode($res->iol_ScanUpload2) : ""],
                    ],
                    "IOLManufacturer" => [
                        "Man" => ['dropdown' => DB::select("SELECT replace(`name`,'&','~') as name FROM manufacturer_lens_category ORDER BY `name`"), "selected" => str_replace('&', '~', $res->manufacture)],
                        "LensBrand" => ["dropdown" => DB::select("SELECT mlb.name as lensName, mlc.name as catName FROM manufacturer_lens_brand mlb,manufacturer_lens_category mlc 
																	WHERE mlc.name='" . $res->manufacture . "' 
																	AND mlc.name!='' 
																	AND mlc.manufacturerLensCategoryId= mlb.catId
																	ORDER BY mlb.name"), "selected" => $res->lensBrand], //additional api require
                        "Model" => ["dropdown" => DB::select("SELECT modelId,name FROM `model`"), "text" => stripslashes($res->model)],
                        "Diopter" => $res->Diopter,
                        "IOLComments" => stripslashes($res->iol_comments),
                    ],
                    "IOLConfirmed" => [
                        "nurse_signature" => ["name" => $res->signNurseFirstName . " " . $res->signNurseLastName, "signed_status" => $res->signNurseStatus, "sign_date" => $res->signNurseDateTime != '0000-00-00 00:00:00' ? date("m-d-Y h:i A", strtotime($res->signNurseDateTime)) : ''],
                        "PrepSolutions" => [
                            "n_a" => $res->prep_solution_na,
                            "Betadine10" => $res->Betadine,
                            "Saline" => $res->Saline,
                            "Alcohol" => $res->Alcohol,
                            "5Betadine" => $res->Prcnt5Betadinegtts,
                            "Proparacaine" => $res->proparacaine,
                            "Tetracaine" => $res->tetracaine,
                            "Tetravisc" => $res->tetravisc,
                            "Other" => stripslashes($res->prepSolutionsOther),
                        ],
                    ],
                    "AnesthesiaService" => [
                        "FullAnesthesiaserviceprovided" => $res->anesthesia_service == 'full_anesthesia' ? 'Yes' : 'No',
                        "NoAnesthesiaserviceprovided" => $res->anesthesia_service == 'no_anesthesia' ? 'Yes' : 'No',
                        "Block" => $res->TopicalBlock == "Block" ? "Yes" : 'No',
                        "Local" => $res->TopicalBlock == "Local" ? "Yes" : 'No',
                        "Topical" => $res->TopicalBlock == "Topical" ? "Yes" : 'No',
                        "CollagenShield" => $res->collagenShield,
                        "Soakedin" => ["Econopred" => $res->Econopred, "Zymar" => $res->Zymar, "Tobradax" => $res->Tobradax, "Other" => $res->soakedInOtherChk, "Othertext" => $res->soakedInOther],
                        "Comments" => stripslashes($res->other_remain),
                    ],
                    "VitalSigns" => $gridDataFilled,
                    "ElectronicallySigned2" => [
                        "ScrubTech2" => ["dropdown" => $scrubNameRows2, 'selectedId' => $res->scrubTechId2, 'selectedname' => @$scrubTecharr[$res->scrubTechId2], 'scrubTechOther2' => $res->scrubTechOther2],
                        "nurse_signature" => ["name" => $res->signNurseFirstName . " " . $res->signNurseLastName, "signed_status" => $res->signNurseStatus, "sign_date" => $res->signNurseDateTime != '0000-00-00 00:00:00' ? date("m-d-Y h:i A", strtotime($res->signNurseDateTime)) : ""],
                        "surgeon_signature" => ["name" => $res->signSurgeon1FirstName . " " . $res->signSurgeon1LastName, "signed_status" => $res->signSurgeon1Status, "sign_date" => $res->signSurgeon1DateTime != '0000-00-00 00:00:00' ? date("m-d-Y h:i A", strtotime($res->signSurgeon1DateTime)) : ""],
                    ],
                ];
                $status = 1;
                $message = " Operating Room Record /Intra OP Record ";
            }
        }
        return response()->json(['status' => $status, 'message' => $message, 'requiredStatus' => '', 'data' => $data,
                    'operatingRoomRecordsId' => isset($res->operatingRoomRecordsId) ? $res->operatingRoomRecordsId : 0,
                    "version_num" => $res->version_num,
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

    public function Alternativequestioner($pConfId, $form_status) {
        $data = [];
        if ($form_status == 'completed' || $form_status == 'not completed') {
            $getAddQuestions = DB::select("select id,ques,ques_desc,ques_status from history_physical_ques where confirmation_id ='" . $pConfId .
                            "' order by ques ASC");
            if ($getAddQuestions) {
                foreach ($getAddQuestions as $Questions) {
                    $data[] = ['QuestionId' => $Questions->id, 'Question' => stripslashes($Questions->ques), "text_note" => stripslashes($Questions->ques_desc), 'yes_no_sts' => $Questions->ques_status,];
                }
            } else {
                $getAddQuestions = DB::select('select id,name from predefine_history_physical where deleted=0 order by name asc');
                if ($getAddQuestions) {
                    foreach ($getAddQuestions as $Questions) {
                        // $data[] = ['QuestionId' => $Questions->id, 'Question' => $Questions->name, "text_note" => '', 'yes_no_sts' => '',];
                    }
                }
            }
        } else {
            $getAddQuestions = DB::select('select id,name from predefine_history_physical where deleted=0 ord er by name asc');
            if ($getAddQuestions) {
                foreach ($getAddQuestions as $Questions) {
                    $data[] = [ 'QuestionId' => $Questions->id, 'Question' => $Questions->name, "text_note" => '', 'yes_no_sts' => '',];
                }
            }
        }
        return $data;
    }

    public function Alternativequestionersave($data, $pConfId, $patient_id) {
        if ($data) {
            foreach ($data as $Questions) {
                if ($Questions->QuestionId > 0) {
                    $data_arr = [ "ques_desc" => addslashes($Questions->text_note), 'ques_status' => $Questions->yes_no_sts
                    ];
                    DB::table('history_physical_ques')->where('id', $Questions->QuestionId)->update($data_arr);
                } else {
                    $data_arr = ["ques_desc" => addslashes($Questions->text_note), 'ques_status' => $Questions->yes_no_sts, 'confirmation_id' => $pConfId, 'patient_id' => $patient_id];
                    DB::table('history_physical_ques')->insert($data_arr);
                }
            }
            return $data;
        }
        return $data;
    }

    public function patient_allergy_save($Allergies_data, $pConfId, $patient_id, $userId, $loggedInUserName) {
        if ((is_array($Allergies_data)) && (!empty($Allergies_data))) {
            foreach ($Allergies_data as $allergiesArrValue) {
                $allergiesReactionArr['patient_confirmation_id'] = $pConfId;
                $allergiesReactionArr['patient_id'] = $patient_id;
                $allergiesReactionArr['allergy_name'] = addslashes($allergiesArrValue->name);
                $allergiesReactionArr['reaction_name'] = isset($allergiesArrValue->reaction) ? addslashes($allergiesArrValue->reaction) : "";
                $allergiesReactionArr['operator_name'] = $loggedInUserName;
                $allergiesReactionArr ['operator_id'] = $userId;
                if ($allergiesArrValue->name != '') {
                    if ($allergiesArrValue->pre_op_allergy_id > 0) {
                        DB::table('patient_allergies_tbl')->where('pre_op_allergy_id', $allergiesArrValue->pre_op_allergy_id)->update($allergiesReactionArr);
                    } else {
                        DB::table('patient_allergies_tbl')->insert($allergiesReactionArr);
                    }
                } else if ($allergiesArrValue->name == '' && $allergiesArrValue->reaction == '') {
                    DB::table('patient_allergies_tbl')->where('pre_op_allergy_id', $allergiesArrValue->
                            pre_op_allergy_id)->delete();
                }
            }
        }
    }

    public function patient_medication_save($Medication_data, $pConfirmId, $patient_id, $userId, $loggedInUserName) {
        if (!empty($Medication_data)) {
            foreach ($Medication_data as $medications) {
                if ($medications->prescription_medication_name != '') {
                    $medicationsArr['confirmation_id'] = $pConfirmId;
                    $medicationsArr['patient_id'] = $patient_id;
                    $medicationsArr['prescription_medication_name'] = addslashes($medications->prescription_medication_name);
                    $medicationsArr['prescription_medication_desc'] = addslashes($medications->prescription_medication_desc);
                    $medicationsArr['prescription_medication_sig'] = addslashes($medications->prescription_medication_sig);
                    $medicationsArr['operator_name'] = $loggedInUserName;
                    $medicationsArr['operator_id'] = $userId;
                    if ($medications->prescription_medication_id > 0) {
                        DB:: table('patient_prescription_medication_tbl')->where('prescription_medication_id', $medications->prescription_medication_id)->update($medicationsArr);
                    } else {
                        DB::table('patient_prescription_medication_tbl')->insert($medicationsArr);
                    }
                }
            }
        }
    }

    public function LensBrand(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $manufacture = $request->json()->get('manufacture') ? $request->json()->get('manufacture') : $request->input('manufacture');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $manufacture = str_ireplace('~', '&', $manufacture);
        $data = [];
        $status = 0;
        $message = " unauthorized ";
        $requiredStatus = [];
        $userId = $this->checkToken($userToken);
        if ($userId > 0) {
            if ($pConfirmId == "") {
                $message = " ConfirmId is missing ";
                $status = 1;
                $requiredStatus = 0;
            } elseif ($patient_id == "") {
                $message = " PatientId is missing ";
                $status = 1;
                $requiredStatus = 0;
            } else {
                $manLensQry = "SELECT mlb.name as lensName, mlc.name as catName 
				FROM manufacturer_lens_brand mlb,manufacturer_lens_category mlc 
				WHERE mlc.name='" . addslashes($manufacture) . "' 
				AND mlc.name!='' 
				AND mlc.manufacturerLensCategoryId= mlb.catId
				ORDER BY mlb.name";
                $data = DB::select($manLensQry);
                $status = 1;
                $message = 'Lens Brand List';
            }
        }
        return response()->json(['status' => $status, 'message' => $message, 'requiredStatus' => '', 'data' => $data,
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

    public function supplies_data_save($Supplies_data, $pConfirmId, $patient_id, $userId, $loggedInUserName) {
        $suppCompStatus = false;
        if (is_array($Supplies_data) && count($Supplies_data) > 0) {
            foreach ($Supplies_data as $Supplies_datas) {
                $suppName = addslashes($Supplies_datas->title);
                $suppQtyDisplay = 1; //$Supplies_datas->title;
                $suppChkBox = $Supplies_datas->checked;
                $suppListBox = $Supplies_datas->xseries_sel;
                $predefine_supp_id = $Supplies_datas->predefine_supp_id;
                // Checking if supply name does not exist in predefine table 
                $whereArray = array('name =' => $suppName);
                $chkPredefSuppCount = $this->getRowCount('predefine_suppliesused', $whereArray);
                $insertUpdateArray = array();
                $insertUpdateArray['name'] = $suppName;
                $insertUpdateArray['qtyChkBox'] = $suppQtyDisplay;
                $insertUpdateArray['deleted'] = 0;

                if (!$chkPredefSuppCount) {
                    $whereArray = array('name =' => 'Other');
                    $chkPredefSuppCatCount = $this->getRowCount('supply_categories', $whereArray);
                    if (!$chkPredefSuppCatCount) {
                        $$insertSupplyCat = array();
                        $insertSupplyCat['name'] = 'Other';
                        $insertSupplyCat['date_created'] = date('Y-m-d H:i:s');
                        DB::table('supply_categories')->insertGetId($insertSupplyCat);
                    }
                    $getSuppCatData = $this->getRowRecord('supply_categories', 'name', 'Other', '', '', 'id');
                    $supp_cat_id = $getSuppCatData->id;
                    $insertUpdateArray['cat_id'] = $supp_cat_id;
                    $predefine_supp_id = DB::table('predefine_suppliesused')->insertGetId($insertUpdateArray);
                }
                // End checking if supply name does not exist in predefine table
                // If Qty Drop Down is set to appear and Checkbox is selected
                // and no any qty selected from dropdown then set to default X1
                if ($suppQtyDisplay && $suppChkBox && empty($suppListBox)) {
                    $suppListBox = 'X1';
                }

                $insertArray['suppName'] = $suppName;
                $insertArray['suppQtyDisplay'] = $suppQtyDisplay;
                $insertArray['suppChkStatus'] = $suppChkBox;
                $insertArray['suppList'] = $suppListBox;
                $insertArray['templateId'] = 0;
                $insertArray['confirmation_id'] = $pConfirmId;
                $insertArray['displayStatus'] = 1;
                $insertArray['predefine_supp_id'] = $predefine_supp_id;
                $chkIfExist = DB::selectone("select * from operatingroomrecords_supplies where suppName='" . $suppName . "' And confirmation_id = '" . $pConfirmId . "'");
                if ($chkIfExist && $chkIfExist->displayStatus == 0) {
                    $insertArray['suppQtyDisplay'] = $chkIfExist->suppQtyDisplay;
                    DB::table('operatingroomrecords_supplies')->where('suppRecordId', $chkIfExist->suppRecordId)->update($insertArray);
                } elseif (!$chkIfExist) {
                    DB::table('operatingroomrecords_supplies')->insertGetId($insertArray);
                }
            }
        }
    }


}
