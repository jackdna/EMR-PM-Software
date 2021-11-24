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

class GenAnesNursesNotesController extends Controller {

    public function GenAnesNursesNotesController_form(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $loginUserType = $request->json()->get('UserType') ? $request->json()->get('UserType') : $request->input('UserType');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $dos = $request->json()->get('dos') ? $request->json()->get('dos') : $request->input('dos');
        $data = [];
        $status = 0;
        $message = " unauthorized ";
        $requiredStatus = [];
        $userId = $this->checkToken($userToken);
        $copyBaseLineVitalSigns = '';
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
                $list_ampm = 'AM';
                $txtInterOpDrugs1 = '';
                $txtInterOpDrugs2 = '';
                $txtInterOpDrugs3 = '';
                $txtInterOpDrugs4 = '';
                $ekgBigRowValue = '';
                $routineMonitorApplied = '';
                $ViewUserNameQry = "select fname,mname,lname,user_type,user_sub_type from `users` where  usersId = '" . $userId . "'";
                $ViewUserNameRow = DB::selectone($ViewUserNameQry); // or die($ViewUserNameQry . imw_error());
                $loggedInUserFirstName = $ViewUserNameRow->fname;
                $loggedInUserMiddleName = $ViewUserNameRow->mname;
                $loggedInUserLastName = $ViewUserNameRow->lname;
                $logInUserType = $user_type = $ViewUserNameRow->user_type;
                $logInUserSubType = $ViewUserNameRow->user_sub_type;
                $signOnFileStatus = 'Yes';
                $loggedInUserName = $ViewUserNameRow->lname . ", " . $ViewUserNameRow->fname . " " . $ViewUserNameRow->mname;

                $allergy1 = "select * from allergies ORDER BY `allergies`.`name` ASC";
                $allergic = DB::select($allergy1);


                //GET CURRENTLY LOGGED IN NURSE NAME
                $detailConfirmation = $this->getRowRecord('patientconfirmation', 'patientConfirmationId', $pConfirmId);
                $finalizeStatus = $detailConfirmation->finalize_status;
                $currentLoggedinNurseId = $detailConfirmation->nurseId;
                $allergiesNKDA_patientconfirmation_status = $detailConfirmation->allergiesNKDA_status;
                $noMedicationStatus = $detailConfirmation->no_medication_status;
                $noMedicationComments = $detailConfirmation->no_medication_comments;
                $confimDOS = $dos = $detailConfirmation->dos;
                $surgeonId = $detailConfirmation->surgeonId;
                $primary_procedure_id = $detailConfirmation->patient_primary_procedure_id;
                $patient_primary_procedure = $primary_procedure_name = $detailConfirmation->patient_primary_procedure;
                $secondary_procedure_id = $detailConfirmation->patient_secondary_procedure_id;
                $tertiary_procedure_id = $detailConfirmation->patient_tertiary_procedure_id;
                $primary_procedure_is_inj_misc = $detailConfirmation->prim_proc_is_misc;
                $surgeonId = $detailConfirmation->surgeonId;
                $patientConfirm_anesthesiologist_id = $anesthesiologist_id = $detailConfirmation->anesthesiologist_id;
                $ascId = $detailConfirmation->ascId;
                $site = $detailConfirmation->site;
                $patientConfirm_assist_by_translator = $detailConfirmation->assist_by_translator;

                $ViewNurseNameQry = "select lname,fname,mname,signature from `users` where  usersId = '" . $currentLoggedinNurseId . "'";
                $ViewNurseNameRow = DB::selectone($ViewNurseNameQry);
                $currentLoggedinNurseName = $ViewNurseNameRow->lname . ", " . $ViewNurseNameRow->fname . " " . $ViewNurseNameRow->mname;
                $currentLoggedinNurseSignature = $ViewNurseNameRow->signature;

                /*                 * *******************Fetching Record******************************* */
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

                $ViewgenAnesNurseQry = "select *, date_format(signNurseDateTime,'%m-%d-%Y %h:%i %p') as signNurseDateTimeFormat from `genanesthesianursesnotes` where  confirmation_id = '" . $pConfirmId . "'";
                $ViewgenAnesNurseRow = DB::selectone($ViewgenAnesNurseQry); // or die(imw_error());

                $anesthesiaGeneral = $ViewgenAnesNurseRow->anesthesiaGeneral;
                $anesthesiaRegional = $ViewgenAnesNurseRow->anesthesiaRegional;
                $anesthesiaEpidural = $ViewgenAnesNurseRow->anesthesiaEpidural;
                $anesthesiaMAC = $ViewgenAnesNurseRow->anesthesiaMAC;
                $anesthesiaLocal = $ViewgenAnesNurseRow->anesthesiaLocal;
                $anesthesiaSpinal = $ViewgenAnesNurseRow->anesthesiaSpinal;
                $anesthesiaSensationAt = $ViewgenAnesNurseRow->anesthesiaSensationAt;
                $anesthesiaSensationAtDesc = $ViewgenAnesNurseRow->anesthesiaSensationAtDesc;

                $genNotesSignApplet = $ViewgenAnesNurseRow->genNotesSignApplet;
                $temp = $ViewgenAnesNurseRow->temp;
                $o2Sat = $ViewgenAnesNurseRow->o2Sat;
                $painScale = $ViewgenAnesNurseRow->painScale;

                $intake_site1 = $ViewgenAnesNurseRow->intake_site1;
                $intake_site2 = $ViewgenAnesNurseRow->intake_site2;
                $intake_site3 = $ViewgenAnesNurseRow->intake_site3;
                $intake_site4 = $ViewgenAnesNurseRow->intake_site4;
                $intake_site5 = $ViewgenAnesNurseRow->intake_site5;
                $intake_site6 = $ViewgenAnesNurseRow->intake_site6;
                $intake_site7 = $ViewgenAnesNurseRow->intake_site7;

                $intake_solution1 = $ViewgenAnesNurseRow->intake_solution1;
                $intake_solution2 = $ViewgenAnesNurseRow->intake_solution2;
                $intake_solution3 = $ViewgenAnesNurseRow->intake_solution3;
                $intake_solution4 = $ViewgenAnesNurseRow->intake_solution4;
                $intake_solution5 = $ViewgenAnesNurseRow->intake_solution5;
                $intake_solution6 = $ViewgenAnesNurseRow->intake_solution6;
                $intake_solution7 = $ViewgenAnesNurseRow->intake_solution7;

                $intake_credit1 = $ViewgenAnesNurseRow->intake_credit1;
                $intake_credit2 = $ViewgenAnesNurseRow->intake_credit2;
                $intake_credit3 = $ViewgenAnesNurseRow->intake_credit3;
                $intake_credit4 = $ViewgenAnesNurseRow->intake_credit4;
                $intake_credit5 = $ViewgenAnesNurseRow->intake_credit5;
                $intake_credit6 = $ViewgenAnesNurseRow->intake_credit6;
                $intake_credit7 = $ViewgenAnesNurseRow->intake_credit7;


                $alterTissuePerfusionTime1 = $ViewgenAnesNurseRow->alterTissuePerfusionTime1;
                $hidd_alterTissuePerfusionTime1 = $ViewgenAnesNurseRow->alterTissuePerfusionTime1;

                $alterTissuePerfusionTime2 = $ViewgenAnesNurseRow->alterTissuePerfusionTime2;
                $hidd_alterTissuePerfusionTime2 = $ViewgenAnesNurseRow->alterTissuePerfusionTime2;

                $alterTissuePerfusionTime3 = $ViewgenAnesNurseRow->alterTissuePerfusionTime3;
                $hidd_alterTissuePerfusionTime3 = $ViewgenAnesNurseRow->alterTissuePerfusionTime3;

                $alterTissuePerfusionInitials1 = $ViewgenAnesNurseRow->alterTissuePerfusionInitials1;
                $alterTissuePerfusionInitials2 = $ViewgenAnesNurseRow->alterTissuePerfusionInitials2;
                $alterTissuePerfusionInitials3 = $ViewgenAnesNurseRow->alterTissuePerfusionInitials3;

                $alterGasExchangeTime1 = $ViewgenAnesNurseRow->alterGasExchangeTime1;
                $hidd_alterGasExchangeTime1 = $ViewgenAnesNurseRow->alterGasExchangeTime1;

                $alterGasExchangeTime2 = $ViewgenAnesNurseRow->alterGasExchangeTime2;
                $hidd_alterGasExchangeTime2 = $ViewgenAnesNurseRow->alterGasExchangeTime2;

                $alterGasExchangeTime3 = $ViewgenAnesNurseRow->alterGasExchangeTime3;
                $hidd_alterGasExchangeTime3 = $ViewgenAnesNurseRow->alterGasExchangeTime3;


                $alterGasExchangeInitials1 = $ViewgenAnesNurseRow->alterGasExchangeInitials1;
                $alterGasExchangeInitials2 = $ViewgenAnesNurseRow->alterGasExchangeInitials2;
                $alterGasExchangeInitials3 = $ViewgenAnesNurseRow->alterGasExchangeInitials3;


                $alterComfortTime1 = $ViewgenAnesNurseRow->alterComfortTime1;
                $hidd_alterComfortTime1 = $ViewgenAnesNurseRow->alterComfortTime1;

                $alterComfortTime2 = $ViewgenAnesNurseRow->alterComfortTime2;
                $hidd_alterComfortTime2 = $ViewgenAnesNurseRow->alterComfortTime2;

                $alterComfortTime3 = $ViewgenAnesNurseRow->alterComfortTime3;
                $hidd_alterComfortTime3 = $ViewgenAnesNurseRow->alterComfortTime3;

                $alterComfortInitials1 = $ViewgenAnesNurseRow->alterComfortInitials1;
                $alterComfortInitials2 = $ViewgenAnesNurseRow->alterComfortInitials2;
                $alterComfortInitials3 = $ViewgenAnesNurseRow->alterComfortInitials3;



                $monitorTissuePerfusion = $ViewgenAnesNurseRow->monitorTissuePerfusion;
                $implementComplicationMeasure = $ViewgenAnesNurseRow->implementComplicationMeasure;
                $assessPlus = $ViewgenAnesNurseRow->assessPlus;
                $telemetry = $ViewgenAnesNurseRow->telemetry;
                $otherTPNurseInter = $ViewgenAnesNurseRow->otherTPNurseInter;
                $otherTPNurseInterDesc = $ViewgenAnesNurseRow->otherTPNurseInterDesc;
                $assessRespiratoryStatus = $ViewgenAnesNurseRow->assessRespiratoryStatus;
                $positionOptimalChestExcusion = $ViewgenAnesNurseRow->positionOptimalChestExcusion;
                $monitorOxygenationGE = $ViewgenAnesNurseRow->monitorOxygenationGE;
                $otherGENurseInter = $ViewgenAnesNurseRow->otherGENurseInter;
                $otherGENurseInterDesc = $ViewgenAnesNurseRow->otherGENurseInterDesc;
                $assessPain = $ViewgenAnesNurseRow->assessPain;
                $usePharmacology = $ViewgenAnesNurseRow->usePharmacology;
                $monitorOxygenationComfort = $ViewgenAnesNurseRow->monitorOxygenationComfort;
                $otherComfortNurseInter = $ViewgenAnesNurseRow->otherComfortNurseInter;
                $otherComfortNurseInterDesc = $ViewgenAnesNurseRow->otherComfortNurseInterDesc;

                $goalTPAchieveTime1 = $ViewgenAnesNurseRow->goalTPAchieveTime1;
                $hidd_goalTPAchieveTime1 = $ViewgenAnesNurseRow->goalTPAchieveTime1;
                $goalTPArchieve1 = $ViewgenAnesNurseRow->goalTPArchieve1;
                $goalTPAchieveInitial1 = $ViewgenAnesNurseRow->goalTPAchieveInitial1;

                $goalTPAchieveTime2 = $ViewgenAnesNurseRow->goalTPAchieveTime2;
                $hidd_goalTPAchieveTime2 = $ViewgenAnesNurseRow->goalTPAchieveTime2;
                $goalTPAchieve2 = $ViewgenAnesNurseRow->goalTPAchieve2;
                $goalTPAchieveInitial2 = $ViewgenAnesNurseRow->goalTPAchieveInitial2;

                $goalGEAchieveTime1 = $ViewgenAnesNurseRow->goalGEAchieveTime1;
                $hidd_goalGEAchieveTime1 = $ViewgenAnesNurseRow->goalGEAchieveTime1;
                $goalGEAchieve1 = $ViewgenAnesNurseRow->goalGEAchieve1;
                $goalGEAchieveInitial1 = $ViewgenAnesNurseRow->goalGEAchieveInitial1;

                $goalGEAchieveTime2 = $ViewgenAnesNurseRow->goalGEAchieveTime2;
                $hidd_goalGEAchieveTime2 = $ViewgenAnesNurseRow->goalGEAchieveTime2;
                $goalGEAchieve2 = $ViewgenAnesNurseRow->goalGEAchieve2;
                $goalGEAchieveInitial2 = $ViewgenAnesNurseRow->goalGEAchieveInitial2;

                $goalCRPAchieveTime1 = $ViewgenAnesNurseRow->goalCRPAchieveTime1;
                $hidd_goalCRPAchieveTime1 = $ViewgenAnesNurseRow->goalCRPAchieveTime1;
                $goalCRPAchieve1 = $ViewgenAnesNurseRow->goalCRPAchieve1;
                $goalCRPAchieveInitial1 = $ViewgenAnesNurseRow->goalCRPAchieveInitial1;

                $goalCRPAchieveTime2 = $ViewgenAnesNurseRow->goalCRPAchieveTime2;
                $hidd_goalCRPAchieveTime2 = $ViewgenAnesNurseRow->goalCRPAchieveTime2;
                $goalCRPAchieve2 = $ViewgenAnesNurseRow->goalCRPAchieve2;
                $goalCRPAchieveInitial2 = $ViewgenAnesNurseRow->goalCRPAchieveInitial2;

                $dischargeSummaryTemp = $ViewgenAnesNurseRow->dischargeSummaryTemp;
                $dischangeSummaryBp = $ViewgenAnesNurseRow->dischangeSummaryBp;
                $dischargeSummaryP = $ViewgenAnesNurseRow->dischargeSummaryP;
                $dischangeSummaryRR = $ViewgenAnesNurseRow->dischangeSummaryRR;

                $alert = $ViewgenAnesNurseRow->alert;
                $hasTakenNourishment = $ViewgenAnesNurseRow->hasTakenNourishment;
                $nauseasVomiting = $ViewgenAnesNurseRow->nauseasVomiting;
                $voidedQs = $ViewgenAnesNurseRow->voidedQs;
                $panv = $ViewgenAnesNurseRow->panv;
                $dressing = $ViewgenAnesNurseRow->dressing;
                $dischargeSummaryOther = $ViewgenAnesNurseRow->dischargeSummaryOther;
                $dischargeAt = $ViewgenAnesNurseRow->dischargeAt;
                $comments = $ViewgenAnesNurseRow->comments;
                $form_status = $ViewgenAnesNurseRow->form_status;
                list($list_hour, $list_min, $secnd) = explode(":", $dischargeAt);
                $hhCnt = 12;
                $dispAmPM = "inline-block";
                if (getenv("SHOW_MILITARY_TIME") == "YES") {
                    $hhCnt = 24;
                    $dispAmPM = "none";
                }
                if ($list_hour > $hhCnt) {
                    $list_hour = $list_hour - $hhCnt;
                    $list_ampm = "PM";
                }
                $nurseId = $ViewgenAnesNurseRow->nurseId;
                $nurseSign = $ViewgenAnesNurseRow->nurseSign;

                $whoUserType = $ViewgenAnesNurseRow->whoUserType;
                $createdByUserId = $ViewgenAnesNurseRow->createdByUserId;
                $relivedNurseId = $ViewgenAnesNurseRow->relivedNurseId;


                //$signNurseDateTime =  $ViewPreopnursingRow["signNurseDateTime"];
                $recovery_room_na = $ViewgenAnesNurseRow->recovery_room_na;
                $recovery_new_drugs = array();
                $recovery_new_dose = array();
                $recovery_new_route = array();
                $recovery_new_time = array();
                $recovery_new_initial = array();
                /* for ($i = 1; $i <= 4; $i++) {
                  $recovery_new_drugs[$i] = $ViewgenAnesNurseRow->recovery_new_drugs.$i;
                  $recovery_new_dose[$i] = $ViewgenAnesNurseRow->recovery_new_dose.$i;
                  $recovery_new_route[$i] = $ViewgenAnesNurseRow->recovery_new_route.$i;
                  $recovery_new_time[$i] = $this->get_timeValue($ViewgenAnesNurseRow->recovery_new_time.$i);
                  $recovery_new_initial[$i] = $ViewgenAnesNurseRow->recovery_new_initial . $i;
                  }
                 */
                $intake_new_fluids = array();
                $intake_new_amount_given = array();
                for ($j = 1; $j <= 3; $j++) {
                    //$intake_new_fluids[$j] = $ViewgenAnesNurseRow->intake_new_fluids . $j;
                    //$intake_new_amount_given[$j] = $ViewgenAnesNurseRow->intake_new_amount_given . $j;
                }

                $ascId = $ViewgenAnesNurseRow->ascId;
                $confirmation_id = $ViewgenAnesNurseRow->confirmation_id;
                $patient_id = $ViewgenAnesNurseRow->patient_id;

                //CODE TO SET gennurseNotes TISSUE TIME
                if ($alterTissuePerfusionTime1 == "00:00:00" || $alterTissuePerfusionTime1 == "") {
                    $hidd_alterTissuePerfusionTime1 = ""; //date("H:i:s");
                } else {
                    $hidd_alterTissuePerfusionTime1 = $alterTissuePerfusionTime1;
                }

                if ($alterTissuePerfusionTime2 == "00:00:00" || $alterTissuePerfusionTime2 == "") {
                    $hidd_alterTissuePerfusionTime2 = ""; //date("H:i:s");
                } else {
                    $hidd_alterTissuePerfusionTime2 = $alterTissuePerfusionTime2;
                }

                if ($alterTissuePerfusionTime3 == "00:00:00" || $alterTissuePerfusionTime3 == "") {
                    $hidd_alterTissuePerfusionTime3 = ""; //date("H:i:s");
                } else {
                    $hidd_alterTissuePerfusionTime3 = $alterTissuePerfusionTime3;
                }

                $alterTissuePerfusionTime1 = $this->get_timeValue($hidd_alterTissuePerfusionTime1);
                $alterTissuePerfusionTime2 = $this->get_timeValue($hidd_alterTissuePerfusionTime2);
                $alterTissuePerfusionTime3 = $this->get_timeValue($hidd_alterTissuePerfusionTime3);
                //END CODE TO SET gennurseNotes TISSUE TIME
                //CODE TO SET gennurseNotes GAS EXCHANGE TIME
                if ($alterGasExchangeTime1 == "00:00:00" || $alterGasExchangeTime1 == "") {
                    $hidd_alterGasExchangeTime1 = ""; //date("H:i:s");
                } else {
                    $hidd_alterGasExchangeTime1 = $alterGasExchangeTime1;
                }

                if ($alterGasExchangeTime2 == "00:00:00" || $alterGasExchangeTime2 == "") {
                    $hidd_alterGasExchangeTime2 = ""; //date("H:i:s");
                } else {
                    $hidd_alterGasExchangeTime2 = $alterGasExchangeTime2;
                }

                if ($alterGasExchangeTime3 == "00:00:00" || $alterGasExchangeTime3 == "") {
                    $hidd_alterGasExchangeTime3 = ""; //date("H:i:s");
                } else {
                    $hidd_alterGasExchangeTime3 = $alterGasExchangeTime3;
                }

                $alterGasExchangeTime1 = $this->get_timeValue($hidd_alterGasExchangeTime1);
                $alterGasExchangeTime2 = $this->get_timeValue($hidd_alterGasExchangeTime2);
                $alterGasExchangeTime3 = $this->get_timeValue($hidd_alterGasExchangeTime3);
                //END CODE TO SET gennurseNotes GAS EXCHANGE TIME
                //CODE TO SET gennurseNotes CONFORT TIME
                if ($alterComfortTime1 == "00:00:00" || $alterComfortTime1 == "") {
                    $hidd_alterComfortTime1 = ""; //date("H:i:s");
                } else {
                    $hidd_alterComfortTime1 = $alterComfortTime1;
                }

                if ($alterComfortTime2 == "00:00:00" || $alterComfortTime2 == "") {
                    $hidd_alterComfortTime2 = ""; //date("H:i:s");
                } else {
                    $hidd_alterComfortTime2 = $alterComfortTime2;
                }

                if ($alterComfortTime3 == "00:00:00" || $alterComfortTime3 == "") {
                    $hidd_alterComfortTime3 = ""; //date("H:i:s");
                } else {
                    $hidd_alterComfortTime3 = $alterComfortTime3;
                }

                $alterComfortTime1 = $this->get_timeValue($hidd_alterComfortTime1);
                $alterComfortTime2 = $this->get_timeValue($hidd_alterComfortTime2);
                $alterComfortTime3 = $this->get_timeValue($hidd_alterComfortTime3);
                //END CODE TO SET gennurseNotes CONFORT TIME
                //CODE TO SET gennurseNotes GOAL TP ACHIVE TIME
                if ($goalTPAchieveTime1 == "00:00:00" || $goalTPAchieveTime1 == "") {
                    $hidd_goalTPAchieveTime1 = ""; //date("H:i:s");
                } else {
                    $hidd_goalTPAchieveTime1 = $goalTPAchieveTime1;
                }

                if ($goalTPAchieveTime2 == "00:00:00" || $goalTPAchieveTime2 == "") {
                    $hidd_goalTPAchieveTime2 = ""; //date("H:i:s"); 
                } else {
                    $hidd_goalTPAchieveTime2 = $goalTPAchieveTime2;
                }
                $goalTPAchieveTime1 = $this->get_timeValue($hidd_goalTPAchieveTime1);
                $goalTPAchieveTime2 = $this->get_timeValue($hidd_goalTPAchieveTime2);
                //END CODE TO SET gennurseNotes GOAL TP ACHIVE TIME
                //CODE TO SET gennurseNotes GOAL GE ACHIVE TIME
                if ($goalGEAchieveTime1 == "00:00:00" || $goalGEAchieveTime1 == "") {
                    $hidd_goalGEAchieveTime1 = ""; //date("H:i:s");
                } else {
                    $hidd_goalGEAchieveTime1 = $goalGEAchieveTime1;
                }

                if ($goalGEAchieveTime2 == "00:00:00" || $goalGEAchieveTime2 == "") {
                    $hidd_goalGEAchieveTime2 = ""; //date("H:i:s");
                } else {
                    $hidd_goalGEAchieveTime2 = $goalGEAchieveTime2;
                }
                $goalGEAchieveTime1 = $this->get_timeValue($hidd_goalGEAchieveTime1);
                $goalGEAchieveTime2 = $this->get_timeValue($hidd_goalGEAchieveTime2);
                //END TO SET gennurseNotes GOAL GE ACHIVE TIME
                //CODE TO SET gennurseNotes GOAL CRP ACHIVE TIME
                if ($goalCRPAchieveTime1 == "00:00:00" || $goalCRPAchieveTime1 == "") {
                    $hidd_goalCRPAchieveTime1 = ""; //date("H:i:s");
                } else {
                    $hidd_goalCRPAchieveTime1 = $goalCRPAchieveTime1;
                }

                if ($goalCRPAchieveTime2 == "00:00:00" || $goalCRPAchieveTime2 == "") {
                    $hidd_goalCRPAchieveTime2 = ""; //date("H:i:s");
                } else {
                    $hidd_goalCRPAchieveTime2 = $goalCRPAchieveTime2;
                }
                $goalCRPAchieveTime1 = $this->get_timeValue($hidd_goalCRPAchieveTime1);
                $goalCRPAchieveTime2 = $this->get_timeValue($hidd_goalCRPAchieveTime2);
                //END CODE TO SET gennurseNotes GOAL CRP ACHIVE TIME
                $NURSARR = DB::select("select usersId,concat(lname,', ',fname,mname) as name from users where user_type='Nurse' ORDER BY lname");
                $RecoveryMeds = [
                    ["recovery_new_drugs" => $ViewgenAnesNurseRow->recovery_new_drugs1, "recovery_new_dose" => $ViewgenAnesNurseRow->recovery_new_dose1, "recovery_new_route" => ['drop' => [["key" => "IV", "value" => "IV"], ["key" => "IM", "value" => "IM"], ["key" => "PO", "value" => "PO"]], 'selected' => $ViewgenAnesNurseRow->recovery_new_route1], "recovery_new_time" => $ViewgenAnesNurseRow->recovery_new_time1, "recovery_new_initial" => ['drop' => $NURSARR, 'selected' => $ViewgenAnesNurseRow->recovery_new_initial1, 'selectedname' => $this->getName($ViewgenAnesNurseRow->recovery_new_initial1)]],
                    ["recovery_new_drugs" => $ViewgenAnesNurseRow->recovery_new_drugs2, "recovery_new_dose" => $ViewgenAnesNurseRow->recovery_new_dose2, "recovery_new_route" => ['drop' => [["key" => "IV", "value" => "IV"], ["key" => "IM", "value" => "IM"], ["key" => "PO", "value" => "PO"]], 'selected' => $ViewgenAnesNurseRow->recovery_new_route2], "recovery_new_time" => $ViewgenAnesNurseRow->recovery_new_time2, "recovery_new_initial" => ['drop' => $NURSARR, 'selected' => $ViewgenAnesNurseRow->recovery_new_initial2, 'selectedname' => $this->getName($ViewgenAnesNurseRow->recovery_new_initial2)]],
                    ["recovery_new_drugs" => $ViewgenAnesNurseRow->recovery_new_drugs3, "recovery_new_dose" => $ViewgenAnesNurseRow->recovery_new_dose3, "recovery_new_route" => ['drop' => [["key" => "IV", "value" => "IV"], ["key" => "IM", "value" => "IM"], ["key" => "PO", "value" => "PO"]], 'selected' => $ViewgenAnesNurseRow->recovery_new_route3], "recovery_new_time" => $ViewgenAnesNurseRow->recovery_new_time3, "recovery_new_initial" => ['drop' => $NURSARR, 'selected' => $ViewgenAnesNurseRow->recovery_new_initial3, 'selectedname' => $this->getName($ViewgenAnesNurseRow->recovery_new_initial3)]],
                    ["recovery_new_drugs" => $ViewgenAnesNurseRow->recovery_new_drugs4, "recovery_new_dose" => $ViewgenAnesNurseRow->recovery_new_dose4, "recovery_new_route" => ['drop' => [["key" => "IV", "value" => "IV"], ["key" => "IM", "value" => "IM"], ["key" => "PO", "value" => "PO"]], 'selected' => $ViewgenAnesNurseRow->recovery_new_route4], "recovery_new_time" => $ViewgenAnesNurseRow->recovery_new_time4, "recovery_new_initial" => ['drop' => $NURSARR, 'selected' => $ViewgenAnesNurseRow->recovery_new_initial4, 'selectedname' => $this->getName($ViewgenAnesNurseRow->recovery_new_initial4)]],
                ];
                $Intake = [
                    ["intake_new_fluids" => $ViewgenAnesNurseRow->intake_new_fluids1, 'intake_new_amount_given' => $ViewgenAnesNurseRow->intake_new_amount_given1],
                    ["intake_new_fluids" => $ViewgenAnesNurseRow->intake_new_fluids2, 'intake_new_amount_given' => $ViewgenAnesNurseRow->intake_new_amount_given2],
                    ["intake_new_fluids" => $ViewgenAnesNurseRow->intake_new_fluids3, 'intake_new_amount_given' => $ViewgenAnesNurseRow->intake_new_amount_given3],
                ];
                $ViewUserNameQry = "select fname,lname,mname,user_type,signature from `users` where  usersId = '" . $userId . "'";
                $ViewUserNameRow = DB::selectone($ViewUserNameQry);
                $loggedInUserName = $ViewUserNameRow->lname . ", " . $ViewUserNameRow->fname . " " . $ViewUserNameRow->mname;
                $loggedInUserType = $ViewUserNameRow->user_type;
                $loggedInSignatureOfNurse = $ViewUserNameRow->signature;

                if ($loggedInUserType <> "Nurse") {
                    
                } else {
                    
                }
                // print '<pre>';print_r($ViewgenAnesNurseRow);
                $signNurseId = $ViewgenAnesNurseRow->signNurseId;
                $signNurseDateTime = $ViewgenAnesNurseRow->signNurseDateTime;
                $signNurseDateTimeFormat = $ViewgenAnesNurseRow->signNurseDateTimeFormat;
                $signNurseFirstName = $ViewgenAnesNurseRow->signNurseFirstName;
                $signNurseMiddleName = $ViewgenAnesNurseRow->signNurseMiddleName;
                $signNurseLastName = $ViewgenAnesNurseRow->signNurseLastName;
                $signNurseStatus = $ViewgenAnesNurseRow->signNurseStatus;

                $signNurseName = $signNurseLastName . ", " . $signNurseFirstName . " " . $signNurseMiddleName;
                $NurseNameShow = '';
                $signOnFileStatus = "Yes";
                //$signNurseDateTimeFormatNew = date("m-d-Y h:i A");
                $signNurseDateTimeFormatNew = $this->getFullDtTmFormat(date("Y-m-d H:i:s"));
                if ($signNurseId <> 0 && $signNurseId <> "") {
                    $NurseNameShow = $signNurseName;
                    $signOnFileStatus = $signNurseStatus;
                    $signNurseDateTimeFormatNew = $this->getFullDtTmFormat($signNurseDateTimeFormat);
                }

                $data = [
                    "Allergy_data" => ['dropdown' => $allergic, 'patientAllergiesGrid' => $patientAllergies],
                    "Anesthesia" => ["General" => $anesthesiaGeneral, "Regional" => $anesthesiaRegional, "Epidural" => $anesthesiaEpidural,
                        "MAC" => $anesthesiaMAC, "Local" => $anesthesiaLocal, "Spinal" => $anesthesiaSpinal, "Sensationat" => $anesthesiaSensationAt, "text" => $anesthesiaSensationAtDesc],
                    "Applet" => $genNotesSignApplet,
                    "RecoveryRoomMeds" => ["N_A" => $recovery_room_na, "Medication" => $RecoveryMeds],
                    "Intake" => $Intake,
                    "Time_NurseNotes" => DB::select("select * from `genanesthesianursesnewnotes` where  confirmation_id =$pConfirmId"),
                    "NURSING_DIAGNOSIS" => [
                        "Alterationintissueperfusion" => [["Time1" => $alterTissuePerfusionTime1, "NurseDrop1" => $NURSARR, "NurseSelect1" => $alterTissuePerfusionInitials1], ["Time2" => $alterTissuePerfusionTime2, "NurseDrop2" => $NURSARR, "NurseSelect2" => $alterTissuePerfusionInitials2], ["Time3" => $alterTissuePerfusionTime3, "NurseDrop3" => $NURSARR, "NurseSelect3" => $alterTissuePerfusionInitials3]],
                        "Alterationingasexchange" => [["Time1" => $alterGasExchangeTime1, "NurseDrop1" => $NURSARR, "NurseSelect1" => $alterGasExchangeInitials1], ["Time2" => $alterGasExchangeTime2, "NurseDrop2" => $NURSARR, "NurseSelect2" => $alterGasExchangeInitials2], ["Time3" => $alterGasExchangeTime3, "NurseDrop3" => $NURSARR, "NurseSelect3" => $alterGasExchangeInitials3]],
                        "Alterationincomfortrelatedpain" => [["Time1" => $alterComfortTime1, "NurseDrop1" => $NURSARR, "NurseSelect1" => $alterComfortInitials1], ["Time2" => $alterComfortTime2, "NurseDrop2" => $NURSARR, "NurseSelect2" => $alterComfortInitials2], ["Time3" => $alterComfortTime3, "NurseDrop3" => $NURSARR, "NurseSelect3" => $alterComfortInitials3]],
                    ],
                    "NURSINGINTERVENTION" => [
                        "MonitorthepatientforchangesinSystemicandperipheraltissueperfusion" => $monitorTissuePerfusion,
                        "Implementmeasuretominimizecomplicationandondimishedperfusion" => $implementComplicationMeasure,
                        "Assesspulse" => $assessPlus,
                        "Telemetry" => ["checked" => $telemetry, "Other" => ["checked" => $otherTPNurseInter, "textdetails" => stripslashes($otherTPNurseInterDesc)]],
                        "Assessrespiratorystatus" => $assessRespiratoryStatus,
                        "PositionthepatientforoptimalchestExcursionanditsexchange" => $positionOptimalChestExcusion,
                        "Monitoroxygenation1" => ["checked" => $monitorOxygenationGE, "Other" => ["checked" => $otherGENurseInter, "textdetails" => stripslashes($otherGENurseInterDesc)]],
                        "Assesspain" => $assessPain,
                        "Usepharmacologyinterventionstorelievepain" => $usePharmacology,
                        "Monitoroxygenation2" => ["checked" => $monitorOxygenationComfort, "Other" => ["checked" => $otherComfortNurseInter, "textdetails" => stripslashes($otherComfortNurseInterDesc)]],
                    ],
                    "GOALEVALUATION" => [
                        "Patientwillshowsignsofadequatetissueperfusion" => [
                            "Achieve1" => ["Yes_No" => ["checked" => $goalTPArchieve1, "time1" => $goalTPAchieveTime1, "drop" => $NURSARR, "selected" => $goalTPAchieveInitial1]],
                            "Achieve2" => ["Yes_No" => ["checked" => $goalTPAchieve2, "time2" => $goalTPAchieveTime2, "drop" => $NURSARR, "selected" => $goalTPAchieveInitial2]],
                        ],
                        "Maintainpatientairwayswithadequateexchange" => [
                            "Achieve1" => [["Yes" => ["checked" => $goalGEAchieve1, "time1" => $goalGEAchieveTime1, "drop" => $NURSARR, "selected" => $goalGEAchieveInitial1]]],
                            "Achieve2" => [["Yes" => ["checked" => $goalGEAchieve2, "time2" => $goalGEAchieveTime2, "drop" => $NURSARR, "selected" => $goalGEAchieveInitial2]]],
                        ],
                        "Patientdeniesorshowsnosignofexcessivepain" => [
                            "Achieve1" => [["Yes" => ["checked" => $goalCRPAchieve1, "time1" => $goalCRPAchieveTime1, "drop" => $NURSARR, "selected" => $goalCRPAchieveInitial1]]],
                            "Achieve2" => [["Yes" => ["checked" => $goalCRPAchieve2, "time2" => $goalCRPAchieveTime2, "drop" => $NURSARR, "selected" => $goalCRPAchieveInitial2]]],
                        ],
                    ],
                    "DischargeSummary" => [
                        "Temp" => $dischargeSummaryTemp, "BP" => $dischangeSummaryBp, "P" => $dischargeSummaryP, "RR" => $dischangeSummaryRR,
                        "Dressing" => $dressing,
                        "Other" => $dischargeSummaryOther,
                        "DischargeAt" => ["hr" => (String) $list_hour, "min" => $list_min, "AM_PM" => $list_ampm],
                        "Comments" => stripslashes($comments),
                    ],
                    "Alert_YES_NO" => [
                        "Alert" => ["Yes_No" => $alert],
                        "Hastakennourishment" => ["Yes_No" => $hasTakenNourishment],
                        "NauseaVomiting" => ["Yes_No" => $nauseasVomiting],
                        "VoidedQS" => ["Yes_No" => $voidedQs],
                        "Pain" => ["Yes_No" => $panv],
                    ],
                    "Who_Created" => [
                        "Who" => ["drop" => [["key" => "Who", "value" => ""], ["key" => "Anesthesiologist", "value" => "Anesthesia Provider"], ["key" => "Nurse", "value" => "Nurse"]], "selected" => $whoUserType],
                        "CreatedBy" => ["drop" => DB::select("select usersId,concat(lname,', ',fname,mname) as name from users where user_type = 'Anesthesiologist' and deleteStatus!='Yes' ORDER BY lname"), "selected" => $createdByUserId, "selectedname" => $this->getName($createdByUserId)],
                        "ReliefNurse" => ["drop" => DB::select("select usersId,concat(lname,', ',fname,mname) as name from users where user_type = 'Nurse' and deleteStatus!='Yes' ORDER BY lname"), "selected" => $createdByUserId, "selectedname" => $this->getName($createdByUserId)],
                        "Nursesignature" => ["name" => $NurseNameShow, "signed_status" => $signOnFileStatus, "sign_date" => $signNurseDateTimeFormatNew],
                    ],
                    "arrDrawIcon_main" => [getenv('APP_URL') . '/' . getenv('APP_ROOT') . '/sc-grid/images/CDr.png', getenv('APP_URL') . '/' . getenv('APP_ROOT') . '/sc-grid/images/CFill.bak.png', getenv('APP_URL') . '/' . getenv('APP_ROOT') . '/sc-grid/images/TDn.png', getenv('APP_URL') . '/' . getenv('APP_ROOT') . '/sc-grid/images/TUp.png']
                ];

                $status = 1;
                $message = " General Anesthesia Nurses Notes ";
            }
        }

        return response()->json(['status' => $status, 'message' => $message, 'data' => $data,
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

    public function Alternativequestioner($pConfId, $form_status) {
        $data = [];
        if ($form_status == 'completed' || $form_status == 'not completed') {
            $getAddQuestions = DB::select("select id,ques,ques_desc,ques_status from history_physical_ques where confirmation_id ='" . $pConfId . "' order by ques ASC");
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
            $getAddQuestions = DB::select('select id,name from predefine_history_physical where deleted=0 order by name asc');
            if ($getAddQuestions) {
                foreach ($getAddQuestions as $Questions) {
                    $data[] = ['QuestionId' =>
                        $Questions->id, 'Question' => $Questions->name, "text_note" => '', 'yes_no_sts' => '',];
                }
            }
        }
        return $data;
    }

    public function Alternativequestionersave($data, $pConfId, $patient_id) {
        if ($data) {
            foreach ($data as $Questions) {
                if ($Questions->QuestionId > 0) {
                    $data_arr = ["ques_desc" => addslashes($Questions->text_note), 'ques_status' => $Questions->yes_no_sts];
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

    public function calculate_timeFun($MainTime) {
        $time_split = explode(":", $MainTime);
        if ($time_split[0] == '24') { //to correct previously saved records
            $MainTime = "12" . ":" . $time_split[1] . ":" . $time_split[2];
        }
        if ($MainTime == "00:00:00") {
            $MainTime = "";
        } else {
            $MainTime = $this->getTmFormat($MainTime); //date('h:iA',strtotime($MainTime));
        }
        return $MainTime;
    }

    // GETTING LOGGED IN USER SUB TYPE
    public function getUserSubTypeFun($subUserId) {
        $getUserSubType = '';
        $getUserSubTypeQry = "SELECT  * FROM `users` WHERE usersId = '" . $subUserId . "'";
        $getUserSubTypeRes = DB::selectone($getUserSubTypeQry);
        if ($getUserSubTypeRes) {
            $getUserSubType = $getUserSubTypeRes->user_sub_type;
        }
        return $getUserSubType;
    }

    //FUNCTION TO GET USER NAME FROM USER TABLE
    public function getName($UserId) {
        $ViewUserNameQry = "select * from `users` where  usersId = '" . $UserId . "'";
        $ViewUserNameRow = DB :: selectone($ViewUserNameQry);
        $UserName = "";
        if ($ViewUserNameRow) {
            if ($ViewUserNameRow->lname) {

                $UserName = trim(stripslashes($ViewUserNameRow->lname . ", " . $ViewUserNameRow->fname . " " . $ViewUserNameRow->mname));
            }
        }
        return $UserName;
    }

    public function patient_allergy_save($Allergies_data, $pConfId, $patient_id, $userId, $loggedInUserName) {
        if ((is_array($Allergies_data)) && (!empty($Allergies_data) )) {
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
                        DB:: table('patient_anesthesia_medication_tbl')->where('prescription_medication_id', $medications->prescription_medication_id)->update($medicationsArr);
                    } else {

                        DB::table('patient_anesthesia_medication_tbl')->insert($medicationsArr);
                    }
                }
            }
        }
    }

    function get_timeValue($hidd_timeValue) {
        if ($hidd_timeValue <> '00:00:00' && $hidd_timeValue <> '') {
            $mainTime = $this->getTmFormat($hidd_timeValue);
            return $mainTime;
        }
    }

    public function GenAnesNursesNotesController_save(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $loginUserType = $request->json()->get('UserType') ? $request->json()->get('UserType') : $request->input('UserType');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $jsondata = $request->json()->get('jsondata') ? $request->json()->get('jsondata') : $request->input('jsondata');
        $json = json_decode($jsondata);
        $json = $json->data;
        $data = [];
        $status = 0;
        $savedStatus = 0;
        $message = " unauthorized ";
        $requiredStatus = [];
        $userId = $this->checkToken($userToken);
        $copyBaseLineVitalSigns = '';
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
            } else if ($jsondata == '') {
                $status = 1;
                $message = " Data is missing ";
            } else {
                $list_ampm = 'AM';
                $txtInterOpDrugs1 = '';
                $txtInterOpDrugs2 = '';
                $txtInterOpDrugs3 = '';
                $txtInterOpDrugs4 = '';
                $ekgBigRowValue = '';
                $routineMonitorApplied = '';
                $ViewUserNameQry = "select fname,mname,lname,user_type,user_sub_type from `users` where  usersId = '" . $userId . "'";
                $ViewUserNameRow = DB::selectone($ViewUserNameQry); // or die($ViewUserNameQry . imw_error());
                $loggedInUserFirstName = $ViewUserNameRow->fname;
                $loggedInUserMiddleName = $ViewUserNameRow->mname;
                $loggedInUserLastName = $ViewUserNameRow->lname;
                $logInUserType = $user_type = $ViewUserNameRow->user_type;
                $logInUserSubType = $ViewUserNameRow->user_sub_type;
                $signOnFileStatus = 'Yes';
                $loggedInUserName = $ViewUserNameRow->lname . ", " . $ViewUserNameRow->fname . " " . $ViewUserNameRow->mname;
                $currentLoggedinNurseQry = "select * from `patientconfirmation` where  patientConfirmationId = '" . $pConfirmId . "'";
                $currentLoggedinNurseRes = DB::selectone($currentLoggedinNurseQry);
                $currentLoggedinNurseId = $currentLoggedinNurseRes->nurseId;

                $tablename = "genanesthesianursesnotes";

                $anesthesiaGeneral = $json->Anesthesia->General; //$_POST["chbx_anesthesiaGeneral"];
                $anesthesiaRegional = $json->Anesthesia->Regional; // $_POST["chbx_anesthesiaRegional"];
                $anesthesiaEpidural = $json->Anesthesia->Epidural; //$_POST["chbx_anesthesiaEpidural"];
                $anesthesiaMAC = $json->Anesthesia->MAC; //$_POST["chbx_anesthesiaMAC"];
                $anesthesiaLocal = $json->Anesthesia->Local; // $_POST["chbx_anesthesiaLocal"];
                $anesthesiaSpinal = $json->Anesthesia->Spinal; // $_POST["chbx_anesthesiaSpinal"];
                $anesthesiaSensationAt = $json->Anesthesia->Sensationat; // $_POST["chbx_anesthesiaSensationAt"];
                $anesthesiaSensationAtDesc = $json->Anesthesia->text; //  $_POST["txt_anesthesiaSensationAt"];
                if ($anesthesiaSensationAt <> "Yes") {
                    $anesthesiaSensationAtDesc = "";
                }
                $genNotesSignApplet = $json->Applet; // $_POST["elem_signs"];

                $temp = $json->DischargeSummary->Temp; //$_POST["txt_temp"];
                $o2Sat = ''; // $_POST["txt_o2Sat"];
                $painScale = ''; // $_POST["txt_painScale"];

                $intake_site1 = ''; // $_POST["txt_site1"];
                $intake_site2 = ''; // $_POST["txt_site2"];
                $intake_site3 = ''; // $_POST["txt_site3"];
                $intake_site4 = ''; // $_POST["txt_site4"];
                $intake_site5 = ''; // $_POST["txt_site5"];
                $intake_site6 = ''; // $_POST["txt_site6"];
                $intake_site7 = ''; // $_POST["txt_site7"];

                $intake_solution1 = ''; //  $_POST["txt_solution1"];
                $intake_solution2 = ''; //  $_POST["txt_solution2"];
                $intake_solution3 = ''; //  $_POST["txt_solution3"];
                $intake_solution4 = ''; //  $_POST["txt_solution4"];
                $intake_solution5 = ''; //  $_POST["txt_solution5"];
                $intake_solution6 = ''; //  $_POST["txt_solution6"];
                $intake_solution7 = ''; //  $_POST["txt_solution7"];

                $intake_credit1 = ''; //  $_POST["txt_credit1"];
                $intake_credit2 = ''; //  $_POST["txt_credit2"];
                $intake_credit3 = ''; //  $_POST["txt_credit3"];
                $intake_credit4 = ''; //  $_POST["txt_credit4"];
                $intake_credit5 = ''; //  $_POST["txt_credit5"];
                $intake_credit6 = ''; //  $_POST["txt_credit6"];
                $intake_credit7 = ''; //  $_POST["txt_credit7"];

                $alterTissuePerfusionTime1 = $this->SaveTime($json->NURSING_DIAGNOSIS->Alterationintissueperfusion[0]->Time1); //$_POST["alterTissuePerfusionTime1"]
                $alterTissuePerfusionTime2 = $this->SaveTime($json->NURSING_DIAGNOSIS->Alterationintissueperfusion[0]->Time2); //$_POST["alterTissuePerfusionTime2"]);
                $alterTissuePerfusionTime3 = $this->SaveTime($json->NURSING_DIAGNOSIS->Alterationintissueperfusion[0]->Time3); //$_POST["alterTissuePerfusionTime3"]);

                $alterTissuePerfusionInitials1 = $json->NURSING_DIAGNOSIS->Alterationintissueperfusion[0]->NurseSelect1; //$json->RecoveryRoomMeds;// $_POST["alterTissuePerfusionInitials1_list"];
                $alterTissuePerfusionInitials2 = $json->NURSING_DIAGNOSIS->Alterationintissueperfusion[0]->NurseSelect2; // $_POST["alterTissuePerfusionInitials2_list"];
                $alterTissuePerfusionInitials3 = $json->NURSING_DIAGNOSIS->Alterationintissueperfusion[0]->NurseSelect3; // $_POST["alterTissuePerfusionInitials3_list"];
                $alterGasExchangeTime1 = $this->SaveTime($json->NURSING_DIAGNOSIS->Alterationingasexchange[0]->Time1); //$this->SaveTime($_POST["alterGasExchangeTime1"]);
                $alterGasExchangeTime2 = $this->SaveTime($json->NURSING_DIAGNOSIS->Alterationingasexchange[0]->Time2); //$this->SaveTime($_POST["alterGasExchangeTime2"]);
                $alterGasExchangeTime3 = $this->SaveTime($json->NURSING_DIAGNOSIS->Alterationingasexchange[0]->Time3); //$this->SaveTime($_POST["alterGasExchangeTime3"]);

                $alterGasExchangeInitials1 = $json->NURSING_DIAGNOSIS->Alterationingasexchange[0]->NurseSelect1; //$_POST["alterGasExchangeInitials1_list"];
                $alterGasExchangeInitials2 = $json->NURSING_DIAGNOSIS->Alterationingasexchange[0]->NurseSelect2; // $_POST["alterGasExchangeInitials2_list"];
                $alterGasExchangeInitials3 = $json->NURSING_DIAGNOSIS->Alterationingasexchange[0]->NurseSelect3; //$_POST["alterGasExchangeInitials3_list"];

                $alterComfortTime1 = $this->SaveTime($json->NURSING_DIAGNOSIS->Alterationincomfortrelatedpain[0]->Time1); //$this->SaveTime($_POST["alterComfortTime1"]);
                $alterComfortTime2 = $this->SaveTime($json->NURSING_DIAGNOSIS->Alterationincomfortrelatedpain[0]->Time2); //$this->SaveTime($_POST["alterComfortTime2"]);
                $alterComfortTime3 = $this->SaveTime($json->NURSING_DIAGNOSIS->Alterationincomfortrelatedpain[0]->Time3); //$this->SaveTime($_POST["alterComfortTime3"]);

                $alterComfortInitials1 = $json->NURSING_DIAGNOSIS->Alterationincomfortrelatedpain[0]->NurseSelect1; //$_POST["alterComfortInitials1_list"];
                $alterComfortInitials2 = $json->NURSING_DIAGNOSIS->Alterationincomfortrelatedpain[0]->NurseSelect2; //$_POST["alterComfortInitials2_list"];
                $alterComfortInitials3 = $json->NURSING_DIAGNOSIS->Alterationincomfortrelatedpain[0]->NurseSelect3; //$_POST["alterComfortInitials3_list"];
                $monitorTissuePerfusion = $json->NURSINGINTERVENTION->MonitorthepatientforchangesinSystemicandperipheraltissueperfusion; // $_POST["chbx_monitorTissuePerfusion"];
                $implementComplicationMeasure = $json->NURSINGINTERVENTION->Implementmeasuretominimizecomplicationandondimishedperfusion; //  $_POST["chbx_implementComplicationMeasure"];
                $assessPlus = $json->NURSINGINTERVENTION->Assesspulse; // $_POST["chbx_assessPlus"];
                $telemetry = $json->NURSINGINTERVENTION->Telemetry->checked; //$_POST["chbx_telemetry"];
                $otherTPNurseInter = $json->NURSINGINTERVENTION->Telemetry->Other->checked; //$_POST["chbx_otherTPNurseInter"];
                $otherTPNurseInterDesc = addslashes($json->NURSINGINTERVENTION->Telemetry->Other->textdetails); // addslashes($_POST["txt_otherTPNurseInterDesc"]);
                if ($otherTPNurseInter <> "Yes") {
                    $otherTPNurseInterDesc = "";
                }
                $assessRespiratoryStatus = $json->NURSINGINTERVENTION->Assessrespiratorystatus; //$_POST["chbx_assessRespiratoryStatus"];
                $positionOptimalChestExcusion = $json->NURSINGINTERVENTION->PositionthepatientforoptimalchestExcursionanditsexchange; // $_POST["chbx_positionOptimalChestExcusion"];
                $monitorOxygenationGE = $json->NURSINGINTERVENTION->Monitoroxygenation1->checked; // $_POST["chbx_monitorOxygenationGE"];
                $otherGENurseInter = $json->NURSINGINTERVENTION->Monitoroxygenation1->Other->checked; //  $_POST["chbx_otherGENurseInter"];
                $otherGENurseInterDesc = addslashes($json->NURSINGINTERVENTION->Monitoroxygenation1->Other->textdetails); //$_POST["txt_otherGENurseInterDesc"]
                if ($otherGENurseInter <> "Yes") {
                    $otherGENurseInterDesc = "";
                }
                $assessPain = $json->NURSINGINTERVENTION->Assesspain; //$_POST["chbx_assessPain"];
                $usePharmacology = $json->NURSINGINTERVENTION->Usepharmacologyinterventionstorelievepain; // $_POST["chbx_usePharmacology"];
                $monitorOxygenationComfort = $json->NURSINGINTERVENTION->Monitoroxygenation2->checked; //$_POST["chbx_monitorOxygenationComfort"];
                $otherComfortNurseInter = $json->NURSINGINTERVENTION->Monitoroxygenation2->Other->checked; //$_POST["chbx_otherComfortNurseInter"];
                $otherComfortNurseInterDesc = addslashes($json->NURSINGINTERVENTION->Monitoroxygenation2->Other->textdetails); // addslashes($_POST["txt_otherComfortNurseInterDesc"]);
                if ($otherComfortNurseInter <> "Yes") {
                    $otherComfortNurseInterDesc = "";
                }
                //  print '<pre>';print_r($json->GOALEVALUATION);
                $goalTPAchieveTime1 = $this->SaveTime($json->GOALEVALUATION->Patientwillshowsignsofadequatetissueperfusion->Achieve1->Yes_No->time1); //$_POST["goalTPAchieveTime1"]
                $goalTPArchieve1 = $json->GOALEVALUATION->Patientwillshowsignsofadequatetissueperfusion->Achieve1->Yes_No->checked; //$_POST["chbx_tiss_archive1"];
                $goalTPAchieveInitial1 = $json->GOALEVALUATION->Patientwillshowsignsofadequatetissueperfusion->Achieve1->Yes_No->selected; // $_POST["goalTPAchieveInitial1_list"];

                $goalTPAchieveTime2 = $this->SaveTime($json->GOALEVALUATION->Patientwillshowsignsofadequatetissueperfusion->Achieve2->Yes_No->time2); //$this->SaveTime($_POST["goalTPAchieveTime2"]);
                $goalTPAchieve2 = $json->GOALEVALUATION->Patientwillshowsignsofadequatetissueperfusion->Achieve2->Yes_No->checked; //$_POST["chbx_tiss_archive2"];
                $goalTPAchieveInitial2 = $json->GOALEVALUATION->Patientwillshowsignsofadequatetissueperfusion->Achieve1->Yes_No->selected; // $_POST["goalTPAchieveInitial2_list"];

                $goalGEAchieveTime1 = $this->SaveTime($json->GOALEVALUATION->Maintainpatientairwayswithadequateexchange->Achieve1[0]->Yes->time1); // $this->SaveTime($_POST["goalGEAchieveTime1"]);
                $goalGEAchieve1 = $json->GOALEVALUATION->Maintainpatientairwayswithadequateexchange->Achieve1[0]->Yes->checked; // $_POST["chbx_adq_exc1"];
                $goalGEAchieveInitial1 = $json->GOALEVALUATION->Maintainpatientairwayswithadequateexchange->Achieve1[0]->Yes->selected; // $_POST["goalGEAchieveInitial1_list"];

                $goalGEAchieveTime2 = $this->SaveTime($json->GOALEVALUATION->Maintainpatientairwayswithadequateexchange->Achieve2[0]->Yes->time2); // $this->SaveTime($_POST["goalGEAchieveTime2"]);
                $goalGEAchieve2 = $json->GOALEVALUATION->Maintainpatientairwayswithadequateexchange->Achieve2[0]->Yes->checked; //  $_POST["chbx_adq_exc2"];
                $goalGEAchieveInitial2 = $json->GOALEVALUATION->Maintainpatientairwayswithadequateexchange->Achieve2[0]->Yes->selected; //  $_POST["goalGEAchieveInitial2_list"];

                $goalCRPAchieveTime1 = $this->SaveTime($json->GOALEVALUATION->Patientdeniesorshowsnosignofexcessivepain->Achieve1[0]->Yes->time1); //  $this->SaveTime($_POST["goalCRPAchieveTime1"]);
                $goalCRPAchieve1 = $json->GOALEVALUATION->Patientdeniesorshowsnosignofexcessivepain->Achieve1[0]->Yes->checked; //  $_POST["chbx_excs_pain1"];
                $goalCRPAchieveInitial1 = $json->GOALEVALUATION->Patientdeniesorshowsnosignofexcessivepain->Achieve1[0]->Yes->selected; //  $_POST["goalCRPAchieveInitial1_list"];

                $goalCRPAchieveTime2 = $this->SaveTime($json->GOALEVALUATION->Patientdeniesorshowsnosignofexcessivepain->Achieve2[0]->Yes->time2); //  $this->SaveTime($_POST["goalCRPAchieveTime2"]);
                $goalCRPAchieve2 = $json->GOALEVALUATION->Patientdeniesorshowsnosignofexcessivepain->Achieve2[0]->Yes->checked; //  $_POST["chbx_excs_pain2"];
                $goalCRPAchieveInitial2 = $json->GOALEVALUATION->Patientdeniesorshowsnosignofexcessivepain->Achieve2[0]->Yes->selected; //$_POST["goalCRPAchieveInitial2_list"];
                $dischargeSummaryTemp = $json->DischargeSummary->Temp; //$_POST["txt_dischargeSummaryTemp"];
                $dischangeSummaryBp = $json->DischargeSummary->BP; // $_POST["txt_dischangeSummaryBp"];
                $dischargeSummaryP = $json->DischargeSummary->P; //$_POST["txt_dischargeSummaryP"];
                $dischangeSummaryRR = $json->DischargeSummary->RR; // $_POST["txt_dischangeSummaryRR"];
                $alert = $json->Alert_YES_NO->Alert->Yes_No; //$_POST["chbx_alrt"];
                $hasTakenNourishment = $json->Alert_YES_NO->Hastakennourishment->Yes_No; // $_POST["chbx_htn"];
                $nauseasVomiting = $json->Alert_YES_NO->NauseaVomiting->Yes_No; //$_POST["chbx_nau_vom"];
                $voidedQs = $json->Alert_YES_NO->VoidedQS->Yes_No; // $_POST["chbx_voi_qs"];
                $panv = $json->Alert_YES_NO->Pain->Yes_No; // $_POST["chbx_pan"];
                $dressing = $json->DischargeSummary->Dressing; //$_POST["txt_dressing"];
                $dischargeSummaryOther = $json->DischargeSummary->Other; // $_POST["txt_dischargeSummaryOther"];
                $nurseId = $currentLoggedinNurseId; // $_POST["hidd_currentLoggedinNurseId"];
                $nurseSign = ''; // $_POST["nurseSign"];

                $whoUserType = $json->Who_Created->Who->selected; //$_POST["whoUserTypeList"];
                if ($whoUserType == "") {
                    $createdByUserIdList = $json->Who_Created->CreatedBy->selected; // $_POST['nurseNotesBlankId_list'];
                } else if ($whoUserType == "Anesthesiologist") {
                    $createdByUserIdList = $json->Who_Created->CreatedBy->selected; // $_POST['nurseNotesAnesthesiologistId_list'];
                } else if ($whoUserType == "Nurse") {
                    $createdByUserIdList = $json->Who_Created->CreatedBy->selected; // $_POST['nurseNotesNurseId_list'];
                }
                $createdByUserId = $createdByUserIdList;
                $relivedNurseId = $json->Who_Created->ReliefNurse->selected; // $_POST["relivedNurseIdList"];

                $hhCnt = 12;
                $dispAmPM = "inline-block";
                if (getenv("SHOW_MILITARY_TIME") == "YES") {
                    $hhCnt = 24;
                    $dispAmPM = "none";
                }

                $list_hour = $json->DischargeSummary->DischargeAt->hr; // $_POST["list_hour"];
                $list_min = $json->DischargeSummary->DischargeAt->min; // $_POST["list_min"];
                $list_ampm = $json->DischargeSummary->DischargeAt->AM_PM; // $_POST["list_ampm"];
                if ($list_hour <> "" && $list_min <> "") {
                    if ($list_ampm == "PM") {
                        $list_hour = $list_hour + $hhCnt;
                    }
                    $dischargeAt = $list_hour . ":" . $list_min;
                }
                $comments = addslashes(trim($json->DischargeSummary->Comments)); //$_POST["txtarea_comments"]
                //    "RecoveryRoomMeds" => ["N_A" => $recovery_room_na, "Medication" => $RecoveryMeds],
                //RECOVERY ROOM MEDS
                $recovery_room_na = isset($json->RecoveryRoomMeds->N_A) ? $json->RecoveryRoomMeds->N_A : ""; //$_POST["recovery_room_na"];
                $recovery_new_drugs1 = isset($json->RecoveryRoomMeds->Medication[0]->recovery_new_drugs) ? $json->RecoveryRoomMeds->Medication[0]->recovery_new_drugs : ""; // $_POST["txt_recovery_new_drugs1"];
                $recovery_new_drugs2 = isset($json->RecoveryRoomMeds->Medication[1]->recovery_new_drugs) ? $json->RecoveryRoomMeds->Medication[1]->recovery_new_drugs : ""; //  $_POST["txt_recovery_new_drugs2"];
                $recovery_new_drugs3 = isset($json->RecoveryRoomMeds->Medication[2]->recovery_new_drugs) ? $json->RecoveryRoomMeds->Medication[2]->recovery_new_drugs : ""; //  $_POST["txt_recovery_new_drugs3"];
                $recovery_new_drugs4 = isset($json->RecoveryRoomMeds->Medication[3]->recovery_new_drugs) ? $json->RecoveryRoomMeds->Medication[3]->recovery_new_drugs : ""; //  $_POST["txt_recovery_new_drugs4"];

                $recovery_new_dose1 = isset($json->RecoveryRoomMeds->Medication[0]->recovery_new_dose) ? $json->RecoveryRoomMeds->Medication[0]->recovery_new_dose : ""; //  $_POST["txt_recovery_new_dose1"];
                $recovery_new_dose2 = isset($json->RecoveryRoomMeds->Medication[1]->recovery_new_dose) ? $json->RecoveryRoomMeds->Medication[1]->recovery_new_dose : ""; // $_POST["txt_recovery_new_dose2"];
                $recovery_new_dose3 = isset($json->RecoveryRoomMeds->Medication[2]->recovery_new_dose) ? $json->RecoveryRoomMeds->Medication[2]->recovery_new_dose : ""; // $_POST["txt_recovery_new_dose3"];
                $recovery_new_dose4 = isset($json->RecoveryRoomMeds->Medication[3]->recovery_new_dose) ? $json->RecoveryRoomMeds->Medication[3]->recovery_new_dose : ""; // $_POST["txt_recovery_new_dose4"];

                $recovery_new_route1 = isset($json->RecoveryRoomMeds->Medication[0]->recovery_new_route->selected) ? $json->RecoveryRoomMeds->Medication[0]->recovery_new_route->selected : ""; // $_POST["txt_recovery_new_route1"];
                $recovery_new_route2 = isset($json->RecoveryRoomMeds->Medication[1]->recovery_new_route->selected) ? $json->RecoveryRoomMeds->Medication[1]->recovery_new_route->selected : ""; // $_POST["txt_recovery_new_route2"];
                $recovery_new_route3 = isset($json->RecoveryRoomMeds->Medication[2]->recovery_new_route->selected) ? $json->RecoveryRoomMeds->Medication[2]->recovery_new_route->selected : ""; // $_POST["txt_recovery_new_route3"];
                $recovery_new_route4 = isset($json->RecoveryRoomMeds->Medication[3]->recovery_new_route->selected) ? $json->RecoveryRoomMeds->Medication[3]->recovery_new_route->selected : ""; // $_POST["txt_recovery_new_route4"];

                $recovery_new_time1 = isset($json->RecoveryRoomMeds->Medication[0]->recovery_new_time) ? $this->SaveTime($json->RecoveryRoomMeds->Medication[0]->recovery_new_time) : ""; //$_POST["txt_recovery_new_time1"]);
                $recovery_new_time2 = isset($json->RecoveryRoomMeds->Medication[1]->recovery_new_time) ? $this->SaveTime($json->RecoveryRoomMeds->Medication[1]->recovery_new_time) : ""; //$_POST["txt_recovery_new_time2"]);
                $recovery_new_time3 = isset($json->RecoveryRoomMeds->Medication[2]->recovery_new_time) ? $this->SaveTime($json->RecoveryRoomMeds->Medication[2]->recovery_new_time) : ""; //$_POST["txt_recovery_new_time3"]);
                $recovery_new_time4 = isset($json->RecoveryRoomMeds->Medication[3]->recovery_new_time) ? $this->SaveTime($json->RecoveryRoomMeds->Medication[3]->recovery_new_time) : ""; //$_POST["txt_recovery_new_time4"]);


                $recovery_new_initial1 = isset($json->RecoveryRoomMeds->Medication[0]->recovery_new_initial->selected) ? $json->RecoveryRoomMeds->Medication[0]->recovery_new_initial->selected : ""; // $_POST["txt_recovery_new_initial1"];
                $recovery_new_initial2 = isset($json->RecoveryRoomMeds->Medication[1]->recovery_new_initial->selected) ? $json->RecoveryRoomMeds->Medication[1]->recovery_new_initial->selected : ""; // $_POST["txt_recovery_new_initial2"];
                $recovery_new_initial3 = isset($json->RecoveryRoomMeds->Medication[2]->recovery_new_initial->selected) ? $json->RecoveryRoomMeds->Medication[2]->recovery_new_initial->selected : ""; // $_POST["txt_recovery_new_initial3"];
                $recovery_new_initial4 = isset($json->RecoveryRoomMeds->Medication[3]->recovery_new_initial->selected) ? $json->RecoveryRoomMeds->Medication[3]->recovery_new_initial->selected : ""; // $_POST["txt_recovery_new_initial4"];
                //RECOVERY ROOM MEDS
                //INTAKE
                $intake_new_fluids1 = isset($json->Intake[0]->intake_new_fluids) ? $json->Intake[0]->intake_new_fluids : ""; // $_POST["txt_intake_new_fluids1"];
                $intake_new_fluids2 = isset($json->Intake[1]->intake_new_fluids) ? $json->Intake[1]->intake_new_fluids : ""; // $_POST["txt_intake_new_fluids2"];
                $intake_new_fluids3 = isset($json->Intake[2]->intake_new_fluids) ? $json->Intake[2]->intake_new_fluids : ""; // $_POST["txt_intake_new_fluids3"];

                $intake_new_amount_given1 = isset($json->Intake[0]->intake_new_amount_given) ? $json->Intake[0]->intake_new_amount_given : ""; // $_POST["txt_intake_new_amount_given1"];
                $intake_new_amount_given2 = isset($json->Intake[1]->intake_new_amount_given) ? $json->Intake[1]->intake_new_amount_given : ""; // $_POST["txt_intake_new_amount_given2"];
                $intake_new_amount_given3 = isset($json->Intake[2]->intake_new_amount_given) ? $json->Intake[2]->intake_new_amount_given : ""; // $_POST["txt_intake_new_amount_given3"];
                //INTAKE
                //START CODE TO CHECK NURSE,SURGEON, ANESTHESIOLOGIST SIGN IN DATABASE
                $chkUserSignDetails = $this->getRowRecord('genanesthesianursesnotes', 'confirmation_id', $pConfirmId);
                if ($chkUserSignDetails) {
                    $chk_signNurseId = $chkUserSignDetails->signNurseId;
                }
                //END CODE TO CHECK NURSE,SURGEON, ANESTHESIOLOGIST SIGN IN DATABASE
                //SET FORM STATUS ACCORDING TO MANDATORY FIELD
                $form_status = "completed";
                if (($anesthesiaGeneral == "" && $anesthesiaRegional == "" && $anesthesiaEpidural == "" && $anesthesiaMAC == "" && $anesthesiaLocal == "" && $anesthesiaSpinal == "" && $anesthesiaSensationAt == ""
                        ) || ($recovery_room_na == "" && $recovery_new_drugs1 == "" && $recovery_new_drugs2 == "" && $recovery_new_drugs3 == "" && $recovery_new_drugs4 == "") || ($intake_new_fluids1 == "" && $intake_new_fluids2 == "" && $intake_new_fluids3 == "") || ($alterTissuePerfusionTime1 == "" && $alterTissuePerfusionTime2 == "" && $alterTissuePerfusionTime3 == "" && $alterTissuePerfusionInitials1 == "" && $alterTissuePerfusionInitials2 == "" && $alterTissuePerfusionInitials3 == ""
                        ) || ($alterGasExchangeTime1 == "" && $alterGasExchangeTime2 == "" && $alterGasExchangeTime3 == "" && $alterGasExchangeInitials1 == "" && $alterGasExchangeInitials2 == "" && $alterGasExchangeInitials3 == ""
                        ) || ($alterComfortTime1 == "" && $alterComfortTime2 == "" && $alterComfortTime3 == "" && $alterComfortInitials1 == "" && $alterComfortInitials2 == "" && $alterComfortInitials3 == ""
                        ) || ($monitorTissuePerfusion == "" && $implementComplicationMeasure == "" && $assessPlus == "" && $telemetry == "" && $otherTPNurseInter == "") || ($assessRespiratoryStatus == "" && $positionOptimalChestExcusion == "" && $monitorOxygenationGE == "" && $otherGENurseInter == "") || ($assessPain == "" && $usePharmacology == "" && $monitorOxygenationComfort == "" && $otherComfortNurseInter == "") || $goalTPArchieve1 == "" || $goalTPAchieve2 == "" || $goalGEAchieve1 == "" || $goalGEAchieve2 == "" || $goalCRPAchieve1 == "" || $goalCRPAchieve2 == "" || $alert == "" || $hasTakenNourishment == "" || $nauseasVomiting == "" || $voidedQs == "" || $panv == "" || ($dischargeSummaryTemp == "" && $dischangeSummaryBp == "" && $dischargeSummaryP == "" && $dischangeSummaryRR == "" && trim($dressing) == "" && $dischargeSummaryOther == ""
                        ) || $dischargeAt == "" || $createdByUserId == "" || $chk_signNurseId == "0"
                ) {
                    $form_status = "not completed";
                }


                //END SET FORM STATUS ACCORDING TO MANDATORY FIELD
                //START CODE TO RESET THE RECORD
                $resetRecordQry = '';
                //END CODE TO RESET THE RECORD

                $chkNurseNotesQry = "select * from `genanesthesianursesnotes` where  confirmation_id = '" . $pConfirmId . "'";
                $chkNurseNotesRes = DB::selectone($chkNurseNotesQry); // or die(imw_error());
                if ($chkNurseNotesRes) {
                    //CODE START TO CHECK FORM STATUS (IF EMPTY THEN REFRESH SLIDER ON SAVE)
                    $chkFormStatusRow = $chkNurseNotesRes; //imw_fetch_array($chkNurseNotesRes);
                    $chk_form_status = $chkFormStatusRow->form_status;
                    //CODE START TO CHECK FORM STATUS (IF EMPTY THEN REFRESH SLIDER ON SAVE)

                    $SaveNurseNotesQry = "update `genanesthesianursesnotes` set 
									anesthesiaGeneral = '$anesthesiaGeneral',
									anesthesiaRegional = '$anesthesiaRegional',
									anesthesiaEpidural = '$anesthesiaEpidural',
									anesthesiaMAC = '$anesthesiaMAC',
									anesthesiaLocal = '$anesthesiaLocal',
									anesthesiaSpinal = '$anesthesiaSpinal',
									anesthesiaSensationAt = '$anesthesiaSensationAt',
									anesthesiaSensationAtDesc = '$anesthesiaSensationAtDesc',
									genNotesSignApplet = '$genNotesSignApplet',
									temp = '$temp',
									o2Sat = '$o2Sat',
									painScale = '$painScale',
									intake_site1 = '$intake_site1',
									intake_site2 = '$intake_site2',
									intake_site3 = '$intake_site3',
									intake_site4 = '$intake_site4',
									intake_site5 = '$intake_site5',
									intake_site6 = '$intake_site6',
									intake_site7 = '$intake_site7',
									intake_solution1 = '$intake_solution1',
									intake_solution2 = '$intake_solution2',
									intake_solution3 = '$intake_solution3',
									intake_solution4 = '$intake_solution4',
									intake_solution5 = '$intake_solution5',
									intake_solution6 = '$intake_solution6',
									intake_solution7 = '$intake_solution7',
									intake_credit1 = '$intake_credit1',
									intake_credit2 = '$intake_credit2',
									intake_credit3 = '$intake_credit3',
									intake_credit4 = '$intake_credit4',
									intake_credit5 = '$intake_credit5',
									intake_credit6 = '$intake_credit6',
									intake_credit7 = '$intake_credit7',
									alterTissuePerfusionTime1 = '$alterTissuePerfusionTime1',
									alterTissuePerfusionTime2 = '$alterTissuePerfusionTime2',
									alterTissuePerfusionTime3 = '$alterTissuePerfusionTime3',
									alterTissuePerfusionInitials1 = '$alterTissuePerfusionInitials1',
									alterTissuePerfusionInitials2 = '$alterTissuePerfusionInitials2',
									alterTissuePerfusionInitials3 = '$alterTissuePerfusionInitials3',
									alterGasExchangeTime1 = '$alterGasExchangeTime1',
									alterGasExchangeTime2 = '$alterGasExchangeTime2',
									alterGasExchangeTime3 = '$alterGasExchangeTime3',
									alterGasExchangeInitials1 = '$alterGasExchangeInitials1',
									alterGasExchangeInitials2 = '$alterGasExchangeInitials2',
									alterGasExchangeInitials3 = '$alterGasExchangeInitials3',
									alterComfortTime1 = '$alterComfortTime1',
									alterComfortTime2 = '$alterComfortTime2',
									alterComfortTime3 = '$alterComfortTime3',
									alterComfortInitials1 = '$alterComfortInitials1',
									alterComfortInitials2 = '$alterComfortInitials2',
									alterComfortInitials3 = '$alterComfortInitials3',
									monitorTissuePerfusion = '$monitorTissuePerfusion',
									implementComplicationMeasure = '$implementComplicationMeasure',
									assessPlus = '$assessPlus',
									telemetry = '$telemetry',
									otherTPNurseInter = '$otherTPNurseInter',
									otherTPNurseInterDesc = '$otherTPNurseInterDesc',
									assessRespiratoryStatus = '$assessRespiratoryStatus',
									positionOptimalChestExcusion = '$positionOptimalChestExcusion',
									monitorOxygenationGE = '$monitorOxygenationGE',
									otherGENurseInter = '$otherGENurseInter',
									otherGENurseInterDesc = '$otherGENurseInterDesc',
									assessPain = '$assessPain',
									usePharmacology = '$usePharmacology',
									monitorOxygenationComfort = '$monitorOxygenationComfort',
									otherComfortNurseInter = '$otherComfortNurseInter',
									otherComfortNurseInterDesc = '$otherComfortNurseInterDesc',
									goalTPArchieve1 = '$goalTPArchieve1',
									goalTPAchieveTime1 = '$goalTPAchieveTime1',
									goalTPAchieveInitial1  = '$goalTPAchieveInitial1',
									goalTPAchieve2 = '$goalTPAchieve2',
									goalTPAchieveTime2 = '$goalTPAchieveTime2',
									goalTPAchieveInitial2  = '$goalTPAchieveInitial2',
									goalGEAchieve1 = '$goalGEAchieve1',
									goalGEAchieveTime1 = '$goalGEAchieveTime1',
									goalGEAchieveInitial1  = '$goalGEAchieveInitial1',
									goalGEAchieve2 = '$goalGEAchieve2',
									goalGEAchieveTime2 = '$goalGEAchieveTime2',
									goalGEAchieveInitial2  = '$goalGEAchieveInitial2',
									goalCRPAchieve1 = '$goalCRPAchieve1',
									goalCRPAchieveTime1 = '$goalCRPAchieveTime1',
									goalCRPAchieveInitial1  = '$goalCRPAchieveInitial1',
									goalCRPAchieve2 = '$goalCRPAchieve2',
									goalCRPAchieveTime2 = '$goalCRPAchieveTime2',
									goalCRPAchieveInitial2  = '$goalCRPAchieveInitial2',
									dischargeSummaryTemp = '$dischargeSummaryTemp',
									dischangeSummaryBp = '$dischangeSummaryBp',
									dischargeSummaryP = '$dischargeSummaryP',
									dischangeSummaryRR = '$dischangeSummaryRR',
									alert = '$alert',
									hasTakenNourishment = '$hasTakenNourishment',
									nauseasVomiting = '$nauseasVomiting',
									voidedQs = '$voidedQs',
									panv = '$panv',
									dressing = '$dressing',
									dischargeSummaryOther = '$dischargeSummaryOther',
									dischargeAt = '$dischargeAt',
									comments = '$comments',
									nurseId = '$nurseId',
									nurseSign = '$nurseSign',
									whoUserType = '$whoUserType',
									createdByUserId = '$createdByUserId',
									relivedNurseId = '$relivedNurseId',
									recovery_room_na='$recovery_room_na',
									recovery_new_drugs1 = '$recovery_new_drugs1',
									recovery_new_drugs2 = '$recovery_new_drugs2',
									recovery_new_drugs3 = '$recovery_new_drugs3',
									recovery_new_drugs4 = '$recovery_new_drugs4',
									recovery_new_dose1 = '$recovery_new_dose1',
									recovery_new_dose2 = '$recovery_new_dose2',
									recovery_new_dose3 = '$recovery_new_dose3',
									recovery_new_dose4 = '$recovery_new_dose4',
									recovery_new_route1 = '$recovery_new_route1',
									recovery_new_route2 = '$recovery_new_route2',
									recovery_new_route3 = '$recovery_new_route3',
									recovery_new_route4 = '$recovery_new_route4',
									recovery_new_time1 = '$recovery_new_time1',
									recovery_new_time2 = '$recovery_new_time2',
									recovery_new_time3 = '$recovery_new_time3',
									recovery_new_time4 = '$recovery_new_time4',
									recovery_new_initial1 = '$recovery_new_initial1',
									recovery_new_initial2 = '$recovery_new_initial2',
									recovery_new_initial3 = '$recovery_new_initial3',
									recovery_new_initial4 = '$recovery_new_initial4',
									intake_new_fluids1 = '$intake_new_fluids1',
									intake_new_fluids2 = '$intake_new_fluids2',
									intake_new_fluids3 = '$intake_new_fluids3',
									intake_new_amount_given1 = '$intake_new_amount_given1',
									intake_new_amount_given2 = '$intake_new_amount_given2',
									intake_new_amount_given3 = '$intake_new_amount_given3',
									$resetRecordQry
									form_status ='" . $form_status . "'
									WHERE confirmation_id='" . $pConfirmId . "'";
                } else {
                    $SaveNurseNotesQry = "insert into `genanesthesianursesnotes` set 
									anesthesiaGeneral = '$anesthesiaGeneral',
									anesthesiaRegional = '$anesthesiaRegional',
									anesthesiaEpidural = '$anesthesiaEpidural',
									anesthesiaMAC = '$anesthesiaMAC',
									anesthesiaLocal = '$anesthesiaLocal',
									anesthesiaSpinal = '$anesthesiaSpinal',
									anesthesiaSensationAt = '$anesthesiaSensationAt',
									anesthesiaSensationAtDesc = '$anesthesiaSensationAtDesc',
									genNotesSignApplet = '$genNotesSignApplet',
									temp = '$temp',
									o2Sat = '$o2Sat',
									painScale = '$painScale',
									intake_site1 = '$intake_site1',
									intake_site2 = '$intake_site2',
									intake_site3 = '$intake_site3',
									intake_site4 = '$intake_site4',
									intake_site5 = '$intake_site5',
									intake_site6 = '$intake_site6',
									intake_site7 = '$intake_site7',
									intake_solution1 = '$intake_solution1',
									intake_solution2 = '$intake_solution2',
									intake_solution3 = '$intake_solution3',
									intake_solution4 = '$intake_solution4',
									intake_solution5 = '$intake_solution5',
									intake_solution6 = '$intake_solution6',
									intake_solution7 = '$intake_solution7',
									intake_credit1 = '$intake_credit1',
									intake_credit2 = '$intake_credit2',
									intake_credit3 = '$intake_credit3',
									intake_credit4 = '$intake_credit4',
									intake_credit5 = '$intake_credit5',
									intake_credit6 = '$intake_credit6',
									intake_credit7 = '$intake_credit7',
									alterTissuePerfusionTime1 = '$alterTissuePerfusionTime1',
									alterTissuePerfusionTime2 = '$alterTissuePerfusionTime2',
									alterTissuePerfusionTime3 = '$alterTissuePerfusionTime3',
									alterTissuePerfusionInitials1 = '$alterTissuePerfusionInitials1',
									alterTissuePerfusionInitials2 = '$alterTissuePerfusionInitials2',
									alterTissuePerfusionInitials3 = '$alterTissuePerfusionInitials3',
									alterGasExchangeTime1 = '$alterGasExchangeTime1',
									alterGasExchangeTime2 = '$alterGasExchangeTime2',
									alterGasExchangeTime3 = '$alterGasExchangeTime3',
									alterGasExchangeInitials1 = '$alterGasExchangeInitials1',
									alterGasExchangeInitials2 = '$alterGasExchangeInitials2',
									alterGasExchangeInitials3 = '$alterGasExchangeInitials3',
									alterComfortTime1 = '$alterComfortTime1',
									alterComfortTime2 = '$alterComfortTime2',
									alterComfortTime3 = '$alterComfortTime3',
									alterComfortInitials1 = '$alterComfortInitials1',
									alterComfortInitials2 = '$alterComfortInitials2',
									alterComfortInitials3 = '$alterComfortInitials3',
									monitorTissuePerfusion = '$monitorTissuePerfusion',
									implementComplicationMeasure = '$implementComplicationMeasure',
									assessPlus = '$assessPlus',
									telemetry = '$telemetry',
									otherTPNurseInter = '$otherTPNurseInter',
									otherTPNurseInterDesc = '$otherTPNurseInterDesc',
									assessRespiratoryStatus = '$assessRespiratoryStatus',
									positionOptimalChestExcusion = '$positionOptimalChestExcusion',
									monitorOxygenationGE = '$monitorOxygenationGE',
									otherGENurseInter = '$otherGENurseInter',
									otherGENurseInterDesc = '$otherGENurseInterDesc',
									assessPain = '$assessPain',
									usePharmacology = '$usePharmacology',
									monitorOxygenationComfort = '$monitorOxygenationComfort',
									otherComfortNurseInter = '$otherComfortNurseInter',
									otherComfortNurseInterDesc = '$otherComfortNurseInterDesc',
									goalTPArchieve1 = '$goalTPArchieve1',
									goalTPAchieveTime1 = '$goalTPAchieveTime1',
									goalTPAchieveInitial1  = '$goalTPAchieveInitial1',
									goalTPAchieve2 = '$goalTPAchieve2',
									goalTPAchieveTime2 = '$goalTPAchieveTime2',
									goalTPAchieveInitial2  = '$goalTPAchieveInitial2',
									goalGEAchieve1 = '$goalGEAchieve1',
									goalGEAchieveTime1 = '$goalGEAchieveTime1',
									goalGEAchieveInitial1  = '$goalGEAchieveInitial1',
									goalGEAchieve2 = '$goalGEAchieve2',
									goalGEAchieveTime2 = '$goalGEAchieveTime2',
									goalGEAchieveInitial2  = '$goalGEAchieveInitial2',
									goalCRPAchieve1 = '$goalCRPAchieve1',
									goalCRPAchieveTime1 = '$goalCRPAchieveTime1',
									goalCRPAchieveInitial1  = '$goalCRPAchieveInitial1',
									goalCRPAchieve2 = '$goalCRPAchieve2',
									goalCRPAchieveTime2 = '$goalCRPAchieveTime2',
									goalCRPAchieveInitial2  = '$goalCRPAchieveInitial2',
									dischargeSummaryTemp = '$dischargeSummaryTemp',
									dischangeSummaryBp = '$dischangeSummaryBp',
									dischargeSummaryP = '$dischargeSummaryP',
									dischangeSummaryRR = '$dischangeSummaryRR',
									alert = '$alert',
									hasTakenNourishment = '$hasTakenNourishment',
									nauseasVomiting = '$nauseasVomiting',
									voidedQs = '$voidedQs',
									panv = '$panv',
									dressing = '$dressing',
									dischargeSummaryOther = '$dischargeSummaryOther',
									dischargeAt = '$dischargeAt',
									comments = '$comments',
									nurseId = '$nurseId',
									nurseSign = '$nurseSign',
									whoUserType = '$whoUserType',
									createdByUserId = '$createdByUserId',
									relivedNurseId = '$relivedNurseId',
									recovery_room_na='$recovery_room_na',
									recovery_new_drugs1 = '$recovery_new_drugs1',
									recovery_new_drugs2 = '$recovery_new_drugs2',
									recovery_new_drugs3 = '$recovery_new_drugs3',
									recovery_new_drugs4 = '$recovery_new_drugs4',
									recovery_new_dose1 = '$recovery_new_dose1',
									recovery_new_dose2 = '$recovery_new_dose2',
									recovery_new_dose3 = '$recovery_new_dose3',
									recovery_new_dose4 = '$recovery_new_dose4',
									recovery_new_route1 = '$recovery_new_route1',
									recovery_new_route2 = '$recovery_new_route2',
									recovery_new_route3 = '$recovery_new_route3',
									recovery_new_route4 = '$recovery_new_route4',
									recovery_new_time1 = '$recovery_new_time1',
									recovery_new_time2 = '$recovery_new_time2',
									recovery_new_time3 = '$recovery_new_time3',
									recovery_new_time4 = '$recovery_new_time4',
									recovery_new_initial1 = '$recovery_new_initial1',
									recovery_new_initial2 = '$recovery_new_initial2',
									recovery_new_initial3 = '$recovery_new_initial3',
									recovery_new_initial4 = '$recovery_new_initial4',
									intake_new_fluids1 = '$intake_new_fluids1',
									intake_new_fluids2 = '$intake_new_fluids2',
									intake_new_fluids3 = '$intake_new_fluids3',
									intake_new_amount_given1 = '$intake_new_amount_given1',
									intake_new_amount_given2 = '$intake_new_amount_given2',
									intake_new_amount_given3 = '$intake_new_amount_given3',
									$resetRecordQry
									form_status ='" . $form_status . "', 
									confirmation_id='" . $pConfirmId . "',
									patient_id = '" . $patient_id . "'";
                }
                $SaveNurseNotesRes = DB::select($SaveNurseNotesQry); // or die(imw_error());
                $NurseNotes = $json->Time_NurseNotes;
                $this->saveNurseNotes($NurseNotes, $pConfirmId);
                //SAVE ENTRY IN chartnotes_change_audit_tbl 
                $fieldName = "genral_anesthesia_nurses_notes_form";
                $chkAuditChartNotesQry = "select * from `chartnotes_change_audit_tbl` where 
									user_id='" . $userId . "' AND
									patient_id='" . $patient_id . "' AND
									confirmation_id='" . $pConfirmId . "' AND
									form_name='" . $fieldName . "' AND
									status = 'created'";

                $chkAuditChartNotesRes = DB::select($chkAuditChartNotesQry); // or die(imw_error());
                //$chkAuditChartNotesNumRow = imw_num_rows($chkAuditChartNotesRes);
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
                //CODE TO CHECK NURSE ALL SIGNATURE AND SET VALUE IN STUB TABLE
                $chartSignedByNurse = $this->chkNurseSignNew($pConfirmId);
                $updateNurseStubTblQry = "UPDATE stub_tbl SET chartSignedByNurse='" . $chartSignedByNurse . "' WHERE patient_confirmation_id='" . $pConfirmId . "'";
                $updateNurseStubTblRes = DB::select($updateNurseStubTblQry); // or die(imw_error());
                $savedStatus = 1;
                $status = 1;
                $message = ' Saved Successfully ! ';
            }
        }
        return response()->json(['status' => $status, 'savedStatus' => $savedStatus, 'message' => $message, 'data' => $data,
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

    function SaveTime($txt_NurseTime) {
        if ($txt_NurseTime <> "") {
            $txt_NurseTime = $this->setTmFormat($txt_NurseTime);
            return $txt_NurseTime;
        }
    }

    function saveNurseNotes($notes, $pconfirmId) {
        if (!empty($notes)) {
            foreach ($notes as $note) {
                $ajax_newNotesTime = $this->getTmFormat(date("H:i:s"));
                $ajax_newNotesDesc = addslashes($note->newnotes_desc);
                $patient_id = $note->patient_id;
                $confirmation_id = $pconfirmId;
                $arr = [
                    'newnotes_time' => $ajax_newNotesTime,
                    'newnotes_desc' => $ajax_newNotesDesc,
                    'confirmation_id' => $confirmation_id,
                    'patient_id' => $patient_id,
                ];
                if ($note->newnotes_id <= 0) {
                    DB::table('genanesthesianursesnewnotes')->insert($arr);
                } else if ($note->newnotes_id) {
                    DB::table('genanesthesianursesnewnotes')->where('newnotes_id', $note->newnotes_id)->update($arr);
                }
            }
        }
    }

    function updateNurseNotesAPI(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $loginUserType = $request->json()->get('UserType') ? $request->json()->get('UserType') : $request->input('UserType');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $ascId = $request->json()->get('ascId') ? $request->json()->get('ascId') : $request->input('ascId');
        $note = $request->json()->get('notes') ? $request->json()->get('notes') : $request->input('notes');
        $note_id = $request->json()->get('note_id') ? $request->json()->get('note_id') : $request->input('note_id');
        $data = [];
        $status = 0;
        $savedStatus = 0;
        $message = " unauthorized ";
        $requiredStatus = [];
        $userId = $this->checkToken($userToken);
        $copyBaseLineVitalSigns = '';
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
                $savedStatus = 1;
                $status = 1;
                $notes = ['note'];
                $ajax_newNotesTime = $this->getTmFormat(date("H:i:s"));
                $ajax_newNotesDesc = addslashes($note);
                $patient_id = $patient_id;
                $confirmation_id = $pConfirmId;
                $arr = [
                    'newnotes_time' => $ajax_newNotesTime,
                    'newnotes_desc' => $ajax_newNotesDesc,
                    'confirmation_id' => $confirmation_id,
                    'patient_id' => $patient_id,
                    'ascId' => $ascId
                ];
                if ($note_id <= 0) {
                    DB::table('genanesthesianursesnewnotes')->insert($arr);
                } else if ($note_id) {
                    DB::table('genanesthesianursesnewnotes')->where('newnotes_id', $note_id)->update($arr);
                }
                $message = 'Saved Successfully !';
            }
        }
        return response()->json(['status' => $status, 'savedStatus' => $savedStatus, 'message' => $message, 'data' => $data,
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

    function deleteNurseNotesAPI(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $loginUserType = $request->json()->get('UserType') ? $request->json()->get('UserType') : $request->input('UserType');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $ascId = $request->json()->get('ascId') ? $request->json()->get('ascId') : $request->input('ascId');
        $note_id = $request->json()->get('note_id') ? $request->json()->get('note_id') : $request->input('note_id');
        $data = [];
        $status = 0;
        $savedStatus = 0;
        $message = " unauthorized ";
        $requiredStatus = [];
        $userId = $this->checkToken($userToken);
        $copyBaseLineVitalSigns = '';
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
                $savedStatus = 1;
                $status = 1;
                DB::table('genanesthesianursesnewnotes')->where('newnotes_id', $note_id)->delete();
                $message = 'Removed Successfully !';
            }
        }
        return response()->json(['status' => $status, 'savedStatus' => $savedStatus, 'message' => $message, 'data' => $data,
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

}
