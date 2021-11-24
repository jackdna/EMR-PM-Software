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

class GenAnesRecController extends Controller {

    public function GenAnesRecController_form(Request $request) {
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
                $ViewUserNameQry = "select fname,mname,lname,user_type,user_sub_type from `users` where  usersId = '" . $userId . "'";
                $ViewUserNameRow = DB::selectone($ViewUserNameQry); // or die($ViewUserNameQry . imw_error());
                $loggedInUserFirstName = $ViewUserNameRow->fname;
                $loggedInUserMiddleName = $ViewUserNameRow->mname;
                $loggedInUserLastName = $ViewUserNameRow->lname;
                $user_type = $ViewUserNameRow->user_type;
                $logInUserSubType = $ViewUserNameRow->user_sub_type;

                $signOnFileStatus = 'Yes';
                $loggedInUserName = $ViewUserNameRow->lname . ", " . $ViewUserNameRow->fname . " " . $ViewUserNameRow->mname;
                $allergy1 = "select * from allergies ORDER BY `allergies`.`name` ASC";
                $allergic = DB::select($allergy1);
                $detailConfirmation = $this->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfirmId);
                $finalizeStatus = $detailConfirmation->finalize_status;
                $allergiesNKDA_patientconfirmation_status = $detailConfirmation->allergiesNKDA_status;
                $noMedicationStatus = $detailConfirmation->no_medication_status;
                $noMedicationComments = $detailConfirmation->no_medication_comments;
                $dos = $detailConfirmation->dos;
                $surgeonId = $detailConfirmation->surgeonId;
                $primary_procedure_id = $detailConfirmation->patient_primary_procedure_id;
                $primary_procedure_name = $detailConfirmation->patient_primary_procedure;
                $secondary_procedure_id = $detailConfirmation->patient_secondary_procedure_id;
                $tertiary_procedure_id = $detailConfirmation->patient_tertiary_procedure_id;
                $primary_procedure_is_inj_misc = $detailConfirmation->prim_proc_is_misc;
                $surgeonId = $detailConfirmation->surgeonId;
                $anesthesiologist_id = $detailConfirmation->anesthesiologist_id;
                $ascId = $detailConfirmation->ascId;

                $patient_primary_procedure = $detailConfirmation->patient_primary_procedure;
                $patient_secondary_procedure = $detailConfirmation->patient_secondary_procedure;

                $anesthesiologist_id = $detailConfirmation->anesthesiologist_id;
                //START CODE TO SET ANES. SIGN/COLOR MANDATORY OR NOT
                $confirmAnes_NA = $detailConfirmation->anes_NA;
                $patientAllergies = [];
                $cntHlt = 0;
                $patient_allergies_tblQry = "SELECT pre_op_allergy_id,allergy_name,reaction_name FROM `patient_allergies_tbl` WHERE `patient_confirmation_id` = '$pConfirmId'";
                $patient_allergies_tblRes = DB::select($patient_allergies_tblQry);
                if ($patient_allergies_tblRes) {
                    foreach ($patient_allergies_tblRes as $patient_allergies_tblRow) {
                        $patientAllergies[] = ['pre_op_allergy_id' => $patient_allergies_tblRow->pre_op_allergy_id, 'name' => $patient_allergies_tblRow->allergy_name, 'reaction' => $patient_allergies_tblRow->reaction_name];
                        $cntHlt++;
                    }
                }
                $patient_prescription_medication_healthquest_qry = "select prescription_medication_id,prescription_medication_name,prescription_medication_desc,prescription_medication_sig from patient_anesthesia_medication_tbl where confirmation_id= '$pConfirmId' order by prescription_medication_name ASC";
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
                $condArr = array();
                $condArr['confirmation_id'] = $pConfirmId;
                $condArr['chartName'] = 'genral_anesthesia_form';

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
                            $gridDataFilled[] = array('id' => 0, 'start_time' => '', 'systolic' => '', 'diastolic' => '', 'pulse' => '', 'rr' => '', 'temp' => '', 'etco2' => '', 'osat2' => '');
                        }
                    }
                    for ($i = $cnt; $i < ($cnt + 15); $i++) {
                        $gridDataFilled[] = array('id' => 0, 'start_time' => '', 'systolic' => '', 'diastolic' => '', 'pulse' => '', 'rr' => '', 'temp' => '', 'etco2' => '', 'osat2' => '');
                    }
                } else {
                    for ($i = $cnt; $i < 15; $i++) {
                        $gridDataFilled[] = array('id' => 0, 'start_time' => '', 'systolic' => '', 'diastolic' => '', 'pulse' => '', 'rr' => '', 'temp' => '', 'etco2' => '', 'osat2' => '');
                    }
                }
                $genAnesthesiaRecordId = 0;
                $ViewgenAnesQry = "select *, date_format(signAnesthesia1DateTime,'%m-%d-%Y %h:%i %p') as signAnesthesia1DateTimeFormat, date_format(signAnesthesia2DateTime,'%m-%d-%Y %h:%i %p') as signAnesthesia2DateTimeFormat from `genanesthesiarecord` where  confirmation_id = '" . $pConfirmId . "'";
                $ViewgenAnesRow = DB::selectone($ViewgenAnesQry);
                if ($ViewgenAnesRow) {
                    $genAnesthesiaRecordId = $ViewgenAnesRow->genAnesthesiaRecordId;
                    $alertOriented = $ViewgenAnesRow->alertOriented;
                    $assistedByTranslator = $ViewgenAnesRow->assistedByTranslator;
                    $bp = $ViewgenAnesRow->bp;
                    $P = $ViewgenAnesRow->P;
                    $rr = $ViewgenAnesRow->rr;
                    $sao = $ViewgenAnesRow->sao;
                    $anesthesiaClass = $ViewgenAnesRow->anesthesiaClass;
                    $o2n2oavailable = $ViewgenAnesRow->o2n2oavailable;
                    $patientVerified = $ViewgenAnesRow->patientVerified;
                    $BMIvalue = stripslashes(trim($ViewgenAnesRow->BMIvalue));
                    $PatientReassessed = $ViewgenAnesRow->PatientReassessed;
                    $MachineEquipment = $ViewgenAnesRow->MachineEquipment;
                    $reserveTanksChecked = $ViewgenAnesRow->reserveTanksChecked;
                    $positivePressureAvailable = $ViewgenAnesRow->positivePressureAvailable;
                    $maskTubingPresent = $ViewgenAnesRow->maskTubingPresent;
                    $vaporizorFilled = $ViewgenAnesRow->vaporizorFilled;
                    $absorberFunctional = $ViewgenAnesRow->absorberFunctional;
                    $gasEvacuatorFunctional = $ViewgenAnesRow->gasEvacuatorFunctional;
                    $o2AnalyzerFunctional = $ViewgenAnesRow->o2AnalyzerFunctional;
                    $ekgMonitor = $ViewgenAnesRow->ekgMonitor;
                    $endoTubes = $ViewgenAnesRow->endoTubes;
                    $laryngoscopeBlades = $ViewgenAnesRow->laryngoscopeBlades;
                    $others = $ViewgenAnesRow->others;
                    $othersDesc = $ViewgenAnesRow->othersDesc;

                    $startTime = $ViewgenAnesRow->startTime;
                    $startTime = $this->calculate_timeFun($startTime); //CODE TO DISPLAY START TIME
                    $stopTime = $ViewgenAnesRow->stopTime;
                    $stopTime = $this->calculate_timeFun($stopTime); //CODE TO DISPLAY STOP TIME

                    $mac = $ViewgenAnesRow->mac;
                    $macValue = $ViewgenAnesRow->macValue;
                    $millar = $ViewgenAnesRow->millar;
                    $millarValue = $ViewgenAnesRow->millarValue;
                    $etTube = $ViewgenAnesRow->etTube;
                    $etTubeSize = $ViewgenAnesRow->etTubeSize;
                    $lma = $ViewgenAnesRow->lma;
                    $lmaSize = $ViewgenAnesRow->lmaSize;
                    $mask = $ViewgenAnesRow->mask;

                    $teethUnchanged = $ViewgenAnesRow->teethUnchanged;
                    $Monitor_ekg = $ViewgenAnesRow->Monitor_ekg;
                    $Monitor_etco2 = $ViewgenAnesRow->Monitor_etco2;
                    $Monitor_etco2Sat = $ViewgenAnesRow->Monitor_etco2Sat;
                    $Monitor_o2Temp = $ViewgenAnesRow->Monitor_o2Temp;
                    $Monitor_PNS = $ViewgenAnesRow->Monitor_PNS;


                    $genAnesSignApplet = $ViewgenAnesRow->genAnesSignApplet;

                    $armsTuckedLeft = $ViewgenAnesRow->armsTuckedLeft;
                    $armsTuckedRight = $ViewgenAnesRow->armsTuckedRight;
                    $armsArmboardsLeft = $ViewgenAnesRow->armsArmboardsLeft;
                    $armsArmboardsRight = $ViewgenAnesRow->armsArmboardsRight;

                    $eyeTapedLeft = $ViewgenAnesRow->eyeTapedLeft;
                    $eyeLubedLeft = $ViewgenAnesRow->eyeLubedLeft;
                    /*
                      $eyeTapedLeft 					= $ViewgenAnesRow["eyeTapedLeft"];
                      $eyeLubedLeft 					= $ViewgenAnesRow["eyeLubedLeft"];
                     */
                    $pressurePointsPadded = $ViewgenAnesRow->pressurePointsPadded;
                    $bss = $ViewgenAnesRow->bss;
                    $warning = $ViewgenAnesRow->warning;

                    $temp = $ViewgenAnesRow->temp;
                    $StableCardioRespiratory = $ViewgenAnesRow->StableCardioRespiratory;
                    $graphComments = $ViewgenAnesRow->graphComments;
                    $evaluation = $ViewgenAnesRow->evaluation;
                    $comments = $ViewgenAnesRow->comments;
                    $anesthesiologistId = $ViewgenAnesRow->anesthesiologistId;
                    $anesthesiologistSign = $ViewgenAnesRow->anesthesiologistSign;
                    $relivedNurseId = $ViewgenAnesRow->relivedNurseId;

                    $signAnesthesia1Id = $ViewgenAnesRow->signAnesthesia1Id;
                    $signAnesthesia1DateTime = $ViewgenAnesRow->signAnesthesia1DateTime;
                    $signAnesthesia1DateTimeFormat = $ViewgenAnesRow->signAnesthesia1DateTimeFormat;
                    $signAnesthesia1FirstName = $ViewgenAnesRow->signAnesthesia1FirstName;
                    $signAnesthesia1MiddleName = $ViewgenAnesRow->signAnesthesia1MiddleName;
                    $signAnesthesia1LastName = $ViewgenAnesRow->signAnesthesia1LastName;
                    $signAnesthesia1Status = $ViewgenAnesRow->signAnesthesia1Status;

                    $signAnesthesia2Id = $ViewgenAnesRow->signAnesthesia2Id;
                    $signAnesthesia2DateTime = $ViewgenAnesRow->signAnesthesia2DateTime;
                    $signAnesthesia2DateTimeFormat = $ViewgenAnesRow->signAnesthesia2DateTimeFormat;
                    $signAnesthesia2FirstName = $ViewgenAnesRow->signAnesthesia2FirstName;
                    $signAnesthesia2MiddleName = $ViewgenAnesRow->signAnesthesia2MiddleName;
                    $signAnesthesia2LastName = $ViewgenAnesRow->signAnesthesia2LastName;
                    $signAnesthesia2Status = $ViewgenAnesRow->signAnesthesia2Status;

                    $reliefNurseId = $ViewgenAnesRow->reliefNurseId;
                    $confirmIPPSC_signin = $ViewgenAnesRow->confirmIPPSC_signin;
                    $siteMarked = $ViewgenAnesRow->siteMarked;
                    $patientAllergiess = $ViewgenAnesRow->patientAllergies;
                    $difficultAirway = $ViewgenAnesRow->difficultAirway;
                    $anesthesiaSafety = $ViewgenAnesRow->anesthesiaSafety;
                    $allMembersTeam = $ViewgenAnesRow->allMembersTeam;
                    $riskBloodLoss = $ViewgenAnesRow->riskBloodLoss;
                    $bloodLossUnits = $ViewgenAnesRow->bloodLossUnits;
                    $version_num = $ViewgenAnesRow->version_num;
                    $vitalSignGridStatus = $ViewgenAnesRow->vitalSignGridStatus;

                    $form_status = $ViewgenAnesRow->form_status;
                    $ascId = $ViewgenAnesRow->ascId;
                    $confirmation_id = $ViewgenAnesRow->confirmation_id;
                    $patient_id = $ViewgenAnesRow->patient_id;
                    $elem_cnvs_anes_drw_file = $ViewgenAnesRow->drawing_path;
                    $elem_cnvs_anes_drw_coords = json_decode($ViewgenAnesRow->drawing_coords);
                }
                //}

                $vitalSignGridStatus = $this->loadVitalSignGridStatus($form_status, $vitalSignGridStatus, 'genAnes');

                /* if (!($version_num) && ($form_status == 'completed' || $form_status == 'not completed')) {
                  $version_num = 1;
                  } else if (!($version_num) && $form_status <> 'completed' && $form_status <> 'not completed') {
                  $version_num = 2;
                  } */
                //GET PATIENT HEIGHT AND WEIGHT FROM PREOP NURSING RECORD
                $selectPreOpNursingQry = "SELECT * FROM `preopnursingrecord` WHERE `confirmation_id` = '" . $pConfirmId . "'";
                $selectPreOpNursingRes = DB::selectone($selectPreOpNursingQry);
                if ($selectPreOpNursingRes) {
                    $selectPreOpNursingRow = $selectPreOpNursingRes;
                    $patientHeight = stripslashes($selectPreOpNursingRow->patientHeight);
                    if (trim($patientHeight) <> "" || $patientHeight <> "") {
                        $patientHeightsplit = explode("'", $patientHeight);
                        $patientHeight = $patientHeightsplit[0] . "' " . $patientHeightsplit[1] . '"';
                        $patientHeightInches = ($patientHeightsplit[0] * 12) + $patientHeightsplit[1];
                    } else {
                        $patientHeight = "";
                    }
                    $patientWeight = $selectPreOpNursingRow->patientWeight;
                    if ($patientWeight <> "") {
                        $patientWeight = $patientWeight; // . " lbs";
                    }
                    //CODE TO CALCULATE BMI VALUE
                    if (!$BMIvalue) {
                        if ((trim($patientHeight) <> "" || $patientHeight <> "") && $patientWeight <> "") {
                            $patientWeight . "::" . $patientHeightInches . "::" . $patientHeightInches;
                            $BMIvalueTemp = $patientWeight * 703 / ($patientHeightInches * $patientHeightInches);
                            $BMIvalue = number_format($BMIvalueTemp, 2, ".", "");
                        }
                    }
                    //END CODE TO CALCULATE BMI VALUE 
                }

                //END GET PATIENT HEIGHT AND WEIGHT FROM PREOP NURSING RECORD
                //END VIEW RECORD FROM DATABASE
                $anesthesia2SignOnFileStatus = "Yes";
                $TDanesthesia2NameIdDisplay = "block";
                $TDanesthesia2SignatureIdDisplay = "none";
                $Anesthesia2Name = $loggedInUserName;
                $Anesthesia2SubType = $logInUserSubType;
                $Anesthesia2PreFix = 'Dr.';
                //$signAnesthesia2DateTimeFormatNew = date("m-d-Y h:i A");
                $signAnesthesia2DateTimeFormatNew = $this->getFullDtTmFormat(date("Y-m-d H:i:s"));
                //$signAnesthesia2DateTimeFormatNew = $objManageData->getFullDtTmFormat($signDateTime);
                if ($signAnesthesia2Id <> 0 && $signAnesthesia2Id <> "") {
                    $Anesthesia2Name = $signAnesthesia2LastName . ", " . $signAnesthesia2FirstName . " " . $signAnesthesia2MiddleName;
                    $anesthesia2SignOnFileStatus = $signAnesthesia2Status;
                    $TDanesthesia2NameIdDisplay = "none";
                    $TDanesthesia2SignatureIdDisplay = "block";
                    $signAnesthesia2DateTimeFormatNew = $this->getFullDtTmFormat($signAnesthesia2DateTime);
                    $Anesthesia2SubType = $this->getUserSubTypeFun($signAnesthesia2Id); //FROM common/commonFunctions.php
                }
                if ($startTime <> "") {
                    $intervalTimeSplit = explode(":", $startTime);
                    $intervalTimeSplitPlusOne = $intervalTimeSplit[0] + 1;

                    if ($intervalTimeSplitPlusOne > 12) {
                        $intervalTimeSplitPlusOne = $intervalTimeSplitPlusOne - 12;
                    }
                    if (strlen($intervalTimeSplitPlusOne) == 1) {
                        $intervalTimeSplitPlusOne = "0" . $intervalTimeSplitPlusOne;
                    }

                    $intervalTimeSplitPlusTwo = $intervalTimeSplit[0] + 2;
                    if ($intervalTimeSplitPlusTwo > 12) {
                        $intervalTimeSplitPlusTwo = $intervalTimeSplitPlusTwo - 12;
                    }
                    if (strlen($intervalTimeSplitPlusTwo) == 1) {
                        $intervalTimeSplitPlusTwo = "0" . $intervalTimeSplitPlusTwo;
                    }

                    $intervalTimeSplitPlusThree = $intervalTimeSplit[0] + 3;
                    if ($intervalTimeSplitPlusThree > 12) {
                        $intervalTimeSplitPlusThree = $intervalTimeSplitPlusThree - 12;
                    }
                    if (strlen($intervalTimeSplitPlusThree) == 1) {
                        $intervalTimeSplitPlusThree = "0" . $intervalTimeSplitPlusThree;
                    }


                    if ($intervalTimeSplit[1] < 15) {
                        $intervalTimeMin1 = $intervalTimeSplit[0] . ":00";
                        $intervalTimeMin2 = $intervalTimeSplit[0] . ":15";
                        $intervalTimeMin3 = $intervalTimeSplit[0] . ":30";
                        $intervalTimeMin4 = $intervalTimeSplit[0] . ":45";
                        $intervalTimeMin5 = $intervalTimeSplitPlusOne . ":00";
                        $intervalTimeMin6 = $intervalTimeSplitPlusOne . ":15";
                        $intervalTimeMin7 = $intervalTimeSplitPlusOne . ":30";
                        $intervalTimeMin8 = $intervalTimeSplitPlusOne . ":45";
                        $intervalTimeMin9 = $intervalTimeSplitPlusTwo . ":00";
                        $intervalTimeMin10 = $intervalTimeSplitPlusTwo . ":15";
                    } else if ($intervalTimeSplit[1] >= 15 && $intervalTimeSplit[1] < 30) {
                        $intervalTimeMin1 = $intervalTimeSplit[0] . ":15";
                        $intervalTimeMin2 = $intervalTimeSplit[0] . ":30";
                        $intervalTimeMin3 = $intervalTimeSplit[0] . ":45";
                        $intervalTimeMin4 = $intervalTimeSplitPlusOne . ":00";
                        $intervalTimeMin5 = $intervalTimeSplitPlusOne . ":15";
                        $intervalTimeMin6 = $intervalTimeSplitPlusOne . ":30";
                        $intervalTimeMin7 = $intervalTimeSplitPlusOne . ":45";
                        $intervalTimeMin8 = $intervalTimeSplitPlusTwo . ":00";
                        $intervalTimeMin9 = $intervalTimeSplitPlusTwo . ":15";
                        $intervalTimeMin10 = $intervalTimeSplitPlusTwo . ":30";
                    } else if ($intervalTimeSplit[1] >= 30 && $intervalTimeSplit[1] < 45) {
                        $intervalTimeMin1 = $intervalTimeSplit[0] . ":30";
                        $intervalTimeMin2 = $intervalTimeSplit[0] . ":45";
                        $intervalTimeMin3 = $intervalTimeSplitPlusOne . ":00";
                        $intervalTimeMin4 = $intervalTimeSplitPlusOne . ":15";
                        $intervalTimeMin5 = $intervalTimeSplitPlusOne . ":30";
                        $intervalTimeMin6 = $intervalTimeSplitPlusOne . ":45";
                        $intervalTimeMin7 = $intervalTimeSplitPlusTwo . ":00";
                        $intervalTimeMin8 = $intervalTimeSplitPlusTwo . ":15";
                        $intervalTimeMin9 = $intervalTimeSplitPlusTwo . ":30";
                        $intervalTimeMin10 = $intervalTimeSplitPlusTwo . ":45";
                    } else if ($intervalTimeSplit[1] >= 45) {
                        $intervalTimeMin1 = $intervalTimeSplit[0] . ":45";
                        $intervalTimeMin2 = $intervalTimeSplitPlusOne . ":00";
                        $intervalTimeMin3 = $intervalTimeSplitPlusOne . ":15";
                        $intervalTimeMin4 = $intervalTimeSplitPlusOne . ":30";
                        $intervalTimeMin5 = $intervalTimeSplitPlusOne . ":45";
                        $intervalTimeMin6 = $intervalTimeSplitPlusTwo . ":00";
                        $intervalTimeMin7 = $intervalTimeSplitPlusTwo . ":15";
                        $intervalTimeMin8 = $intervalTimeSplitPlusTwo . ":30";
                        $intervalTimeMin9 = $intervalTimeSplitPlusTwo . ":45";
                        $intervalTimeMin10 = $intervalTimeSplitPlusThree . ":00";
                    }
                }
                $Anesthesia1Name = $loggedInUserName;
                $Anesthesia1SubType = $logInUserSubType;
                $Anesthesia1PreFix = 'Dr.';
                $anesthesia1SignOnFileStatus = '';
                //$signAnesthesia1DateTimeFormatNew = date("m-d-Y h:i A");
                //$signAnesthesia1DateTimeFormatNew = $objManageData->getFullDtTmFormat($signDateTime);
                $signAnesthesia1DateTimeFormatNew = $this->getFullDtTmFormat(date("Y-m-d H:i:s"));
                if ($signAnesthesia1Id <> 0 && $signAnesthesia1Id <> "") {
                    $Anesthesia1Name = $signAnesthesia1LastName . ", " . $signAnesthesia1FirstName . " " . $signAnesthesia1MiddleName;
                    $anesthesia1SignOnFileStatus = $signAnesthesia1Status;
                    $TDanesthesia1NameIdDisplay = "none";
                    $TDanesthesia1SignatureIdDisplay = "block";
                    $signAnesthesia1DateTimeFormatNew = $this->getFullDtTmFormat($signAnesthesia1DateTime);
                    $Anesthesia1SubType = $this->getUserSubTypeFun($signAnesthesia1Id); //FROM common/commonFunctions.php
                }
                if ($Anesthesia1SubType == 'CRNA') {
                    $Anesthesia1PreFix = '';
                }
                
                $data = [
                    "ReliefNurse_Anesthesia" => ["drop" => DB::select("select usersId,lname,fname,mname, deleteStatus from users where (user_type IN('Nurse','Anesthesiologist') or (user_type='Anesthesiologist' And user_sub_type='CRNA')) ORDER BY lname"), 'selected' => $relivedNurseId],
                    "Confirmationofidentifyprocedureproceduresiteandconsent" => $confirmIPPSC_signin,
                    "Sitemarkedbypersonperformingtheprocedure" => $siteMarked,
                    "Patientallergies_Check" => $patientAllergiess,
                    "Difficultairwayoraspirationrisk" => $difficultAirway,
                    "Riskofbloodloss" => ["yes_no_na" => $riskBloodLoss, 'bloodLossUnits' => $bloodLossUnits],
                    "Anesthesiasafetycheckcompleted" => $anesthesiaSafety,
                    "Allmembersoftheteamhavediscussedcareplanandaddressedconcerns" => $allMembersTeam,
                    "Anesthesia1" => ["name" => $Anesthesia2PreFix . " " . $Anesthesia2Name, "signed_status" => $anesthesia2SignOnFileStatus, "sign_date" => $signAnesthesia2DateTimeFormatNew],
                    "Patient_check" => ["checkbox" => $patientVerified, "height" => $patientHeight, "weight" => $patientWeight, "bmi" => $BMIvalue],
                    "Procedure" => ["verified" => $patient_primary_procedure, "Secondary_verified" => $patient_secondary_procedure],
                    "Allergy_data" => ['dropdown' => $allergic, 'patientAllergiesGrid' => $patientAllergies],
                    "Medications_data" => ['dropdown' => $medication, 'patient_prescriptions' => $patient_prescriptions],
                    "PatientReassessed" => $PatientReassessed,
                    "MachineEquipmentCompleted" => $MachineEquipment,
                    "AnesthesiaClass" => ["I_II_III" => $anesthesiaClass],
                    "TimeInterval" => @[$intervalTimeMin1, $intervalTimeMin2, $intervalTimeMin3, $intervalTimeMin4, $intervalTimeMin5, $intervalTimeMin6, $intervalTimeMin7, $intervalTimeMin8, $intervalTimeMin9, $intervalTimeMin10],
                    "StartTime" => $startTime,
                    "StopTime" => $stopTime,
                    "Arms" => ["Tucked_L" => $armsTuckedLeft, "Tucked_R" => $armsTuckedRight, "Armboards_L" => $armsArmboardsLeft, "Armboards_R" => $armsArmboardsRight],
                    "Eyes" => ["Taped_Yes_No" => $eyeTapedLeft, "Lubed_Yes_No" => $eyeLubedLeft, "PressurePointsPadded" => $pressurePointsPadded, "BSS" => $bss, "Warming" => $warning, "Devicetemp" => $temp],
                    "MAC" => ["checked" => $mac, "series_checked" => $macValue],
                    "Miller" => ["checked" => $millar, "series_checked" => $millarValue],
                    "ETTubeSize" => ["checked" => $etTube, "series_checked" => $etTubeSize],
                    "LMA" => ["checked" => $lma, "series_checked" => $lmaSize],
                    "MASK" => ["checked" => $mask],
                    "TeethUnch" => ["checked" => $teethUnchanged],
                    "Monitor" => ["EKG" => $Monitor_ekg, "ETCO2" => $Monitor_etco2, "O2Sat" => $Monitor_etco2Sat, "Temp" => $Monitor_o2Temp, "PNS" => $Monitor_PNS],
                    "Applet" => $genAnesSignApplet,
                    "drawing_path" => getenv('APP_URL') . '/' . getenv('APP_ROOT') . '/admin/' . $elem_cnvs_anes_drw_file,
                    "drawing_coords" => $elem_cnvs_anes_drw_coords,
                    "VitalSigns" => $gridDataFilled,
                    "Evaluation" => ["drop" => DB::select("SELECT evaluationId,name from evaluation"), "textdetails" => trim(stripslashes($evaluation))],
                    "graphComments" => $graphComments,
                    "comments" => stripslashes($comments),
                    "PACU" => ["BP" => $bp, "P" => $P, "RR" => $rr, "SaO2" => $sao, "StableCardioRespiratory" => $StableCardioRespiratory],
                    "Anesthesia2" => ["name" => $Anesthesia1PreFix . " " . $Anesthesia1Name, "signed_status" => $anesthesia1SignOnFileStatus, "sign_date" => $signAnesthesia1DateTimeFormatNew],
                    "ReliefNurlse" => ['drop' => DB::select("select usersId,lname,fname from users where user_type='Nurse' and deleteStatus!='Yes' ORDER BY lname"), 'selected' => $relivedNurseId],
                    "arrDrawIcon_main" => [getenv('APP_URL') . '/' . getenv('APP_ROOT') . '/sc-grid/images/CDr.png', getenv('APP_URL') . '/' . getenv('APP_ROOT') . '/sc-grid/images/CFill.bak.png', getenv('APP_URL') . '/' . getenv('APP_ROOT') . '/sc-grid/images/TDn.png', getenv('APP_URL') . '/' . getenv('APP_ROOT') . '/sc-grid/images/TUp.png']
                ];
                $status = 1;
                $message = " General Anesthesia Record ";
            }
        }
        return response()->json(['status' => $status, 'message' => $message, 'requiredStatus' => '', 'data' => $data, 'version_num' => (int) $version_num,
                    'genAnesthesiaRecordId' => $genAnesthesiaRecordId], 200, [ 'Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

    public function patient_allergy_save($Allergies_data, $pConfId, $patient_id, $userId, $loggedInUserName) {
        if ((is_array($Allergies_data)) && (!empty($Allergies_data) )) {
            foreach ($Allergies_data as $allergiesArrValue) {
                $allergiesReactionArr['patient_confirmation_id'] = $pConfId;
                $allergiesReactionArr['patient_id'] = $patient_id;
                $allergiesReactionArr['allergy_name'] = addslashes($allergiesArrValue->name);
                $allergiesReactionArr['reaction_name'] = isset($allergiesArrValue->reaction) ? addslashes($allergiesArrValue->reaction) : "";
                $allergiesReactionArr

                        ['operator_name'] = $loggedInUserName;
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
                if ($medications->name != '') {
                    $medicationsArr['confirmation_id'] = $pConfirmId;
                    $medicationsArr['patient_id'] = $patient_id;
                    $medicationsArr['prescription_medication_name'] = addslashes($medications->name);
                    $medicationsArr['prescription_medication_desc'] = addslashes($medications->dosage);
                    $medicationsArr['prescription_medication_sig'] = addslashes($medications->sign);
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

    public function calculate_timeFun($MainTime) {
        $time_split = explode(":", $MainTime);
        if ($time_split[0] == '24') { //to correct previously saved records
            $MainTime = "12" . ":" . $time_split[1] . ":" . $time_split[2];
        }
        if ($MainTime == "00:00:00") {
            $MainTime = "";
        } else {
            $MainTime = $this->getTmFormat($MainTime); //date('h:iA',strtotime($MainTime));
            //$MainTime = date('h:iA',strtotime($MainTime));
            //$MainTime = substr($MainTime,0,-1);
        }
        return $MainTime;
    }

    public function getUserSubTypeFun($subUserId) {
        $getUserSubType = '';
        $getUserSubTypeQry = "SELECT  * FROM `users` WHERE usersId = '" . $subUserId . "'";
        $getUserSubTypeRes = DB::selectone($getUserSubTypeQry); // or die('Error Found at line no. ' . (__LINE__) . ': ' . imw_error());
        if ($getUserSubTypeRes) {
            $getUserSubTypeRow = $getUserSubTypeRes;
            $getUserSubType = $getUserSubTypeRow->user_sub_type;
        }
        return $getUserSubType;
    }

    public function GenAnesRecController_save(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $loginUserType = $request->json()->get('UserType') ? $request->json()->get('UserType') : $request->input('UserType');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $jsondata = $request->json()->get('jsondata') ? $request->json()->get('jsondata') : $request->input('jsondata');
        $drawing_path = $request->json()->get('drawing_path') ? $request->json()->get('drawing_path') : $request->input('drawing_path');
        $json = json_decode($jsondata);
        $genAnesthesiaRecordId =$json->genAnesthesiaRecordId;
        $json=$json->data;
        $data = [];
        $status = 0;
        $savedStatus=0;
        $message = " unauthorized ";
        $requiredStatus = [];
        $userId = $this->checkToken($userToken);
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
            } else if (empty($json)) {
                $status = 1;
                $message = " Jsondata is missing ";
            } else {
                $detailConfirmation = $this->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfirmId);
                $finalizeStatus = $detailConfirmation->finalize_status;
                $allergiesNKDA_patientconfirmation_status = $detailConfirmation->allergiesNKDA_status;
                $noMedicationStatus = $detailConfirmation->no_medication_status;
                $noMedicationComments = $detailConfirmation->no_medication_comments;
                $dos = $detailConfirmation->dos;
                $surgeonId = $detailConfirmation->surgeonId;
                $primary_procedure_id = $detailConfirmation->patient_primary_procedure_id;
                $primary_procedure_name = $detailConfirmation->patient_primary_procedure;
                $secondary_procedure_id = $detailConfirmation->patient_secondary_procedure_id;
                $tertiary_procedure_id = $detailConfirmation->patient_tertiary_procedure_id;
                $primary_procedure_is_inj_misc = $detailConfirmation->prim_proc_is_misc;
                $surgeonId = $detailConfirmation->surgeonId;
                $anesthesiologist_id = $detailConfirmation->anesthesiologist_id;
                $ascId = $detailConfirmation->ascId;

                $patient_primary_procedure = $detailConfirmation->patient_primary_procedure;
                $patient_secondary_procedure = $detailConfirmation->patient_secondary_procedure;

                $anesthesiologist_id = $detailConfirmation->anesthesiologist_id;
                //START CODE TO SET ANES. SIGN/COLOR MANDATORY OR NOT
                $confirmAnes_NA = $detailConfirmation->anes_NA;
                $tablename = "genanesthesiarecord";
                $alertOriented = ''; // $_POST["chbx_gen_anes_alert"];
                $assistedByTranslator = ''; // $_POST["chbx_assistedByTranslator"];
                $bp = $json->PACU->BP; //$_POST["txt_bp"];
                $P = $json->PACU->P; // $_POST["txt_P"];
                $rr = $json->PACU->RR; // $_POST["txt_rr"];
                $sao = $json->PACU->SaO2; // $_POST["txt_sao"];
                $anesthesiaClass = $json->AnesthesiaClass->I_II_III; // $_POST["chbx_gen_anes_class"];
                $patientVerified = $json->Patient_check->checkbox; // $_POST["chbx_patientVerified"];
                $BMIvalue = $json->Patient_check->bmi; //  $_POST["txt_BMIvalue"];
                $PatientReassessed = $json->PatientReassessed; // $_POST["chbx_PatientReassessed"];
                $MachineEquipment = $json->MachineEquipmentCompleted; //  $_POST["chbx_MachineEquipment"];
                $o2n2oavailable = ''; //  $_POST["chbx_o2n2oavailable"];
                $reserveTanksChecked = ''; // $_POST["chbx_reserveTanksChecked"];
                $positivePressureAvailable = ''; // $_POST["chbx_positivePressureAvailable"];
                $maskTubingPresent = ''; // $_POST["chbx_maskTubingPresent"];
                $vaporizorFilled = ''; // $_POST["chbx_vaporizorFilled"];
                $absorberFunctional = ''; // $_POST["chbx_absorberFunctional"];
                $gasEvacuatorFunctional = ''; // $_POST["chbx_gasEvacuatorFunctional"];
                $o2AnalyzerFunctional = ''; // $_POST["chbx_o2AnalyzerFunctional"];
                $ekgMonitor = ''; // $_POST["chbx_ekgMonitor"];
                $endoTubes = ''; // $_POST["chbx_endoTubes"];
                $laryngoscopeBlades = ''; // $_POST["chbx_laryngoscopeBlades"];
                $others = ''; // $_POST["chbx_others"];
                $othersDesc = ''; //$_POST["othersDesc"];


                $startTime = $this->setTmFormat($json->StartTime); //$_POST["startTime"]
                $stopTime = $this->setTmFormat($json->StopTime); //$_POST["stopTime"]
                $mac = $json->MAC->checked; // $_POST["gen_anes_mac"];
                $miller = $json->Miller->checked; // $_POST["gen_anes_mac"];
                if ($mac <> "") {
                    $mac = "mac";
                    $macValue = $json->MAC->series_checked; // $_POST["gen_anes_submac"];
                    $millar = "";
                    $millarValue = "";
                } else if ($miller <> "") {
                    $mac = "";
                    $macValue = "";
                    $millar = "millar";
                    $millarValue = $json->Miller->series_checked; // $_POST["gen_anes_miller"];
                } else {
                    $mac = "";
                    $macValue = "";
                    $millar = "";
                    $millarValue = "";
                }

                $Tube_lma_mask = $json->ETTubeSize->checked; // $_POST["chbx_et_lm_msk"];
                $LMA = $json->LMA->checked; // $_POST["chbx_et_lm_msk"];
                $MASK = $json->MASK->checked; // $_POST["chbx_et_lm_msk"];
                if ($Tube_lma_mask <> "") {
                    $etTube = "etTube";
                    $etTubeSize = $json->ETTubeSize->series_checked; // $_POST["chbx_et_tubeSize"];
                    $lma = "";
                    $lmaSize = "";
                    $mask = "";
                } else if ($LMA <> "") {
                    $etTube = "";
                    $etTubeSize = "";
                    $lma = "lma";
                    $lmaSize = $json->LMA->series_checked; //$_POST["lma_sub"];
                    $mask = "";
                } else if ($MASK <> "") {
                    $etTube = "";
                    $etTubeSize = "";
                    $lma = "";
                    $lmaSize = "";
                    $mask = "mask";
                } else {
                    $etTube = "";
                    $etTubeSize = "";
                    $lma = "";
                    $lmaSize = "";
                    $mask = "";
                }

                $teethUnchanged = $json->TeethUnch->checked; // $_POST["chbx_teethUnchanged"];
                $Monitor_ekg = $json->Monitor->EKG; // $_POST["chbx_ekg"];
                $Monitor_etco2 = $json->Monitor->ETCO2; // $_POST["chbx_etco2"];
                $Monitor_etco2Sat = $json->Monitor->O2Sat; // $_POST["chbx_etco2Sat"];
                $Monitor_o2Temp = $json->Monitor->Temp; // $_POST["chbx_o2Temp"];
                $Monitor_PNS = $json->Monitor->PNS; // $_POST["chbx_PNS"];

                $genAnesSignApplet = ''; // $_POST["elem_signs"];
                //Start Canvas Drawing --
                $cnvs_anes_drw_coords = json_encode($json->drawing_coords); //$_POST["elem_cnvs_anes_drw_coords"];
                $cnvs_anes_drw = $drawing_path; //$_POST["elem_cnvs_anes_drw"];
                $up_img = $this->convertBase64_GenAnesRec($cnvs_anes_drw, $genAnesthesiaRecordId, $patient_id, $pConfirmId);
                $armsTuckedLeft = $json->Arms->Tucked_L; // $_POST["chbx_armsTuckedLeft"];
                $armsTuckedRight = $json->Arms->Tucked_R; // $_POST["chbx_armsTuckedRight"];
                $armsArmboardsLeft = $json->Arms->Armboards_L; // $_POST["chbx_armsArmboardsLeft"];
                $armsArmboardsRight = $json->Arms->Armboards_R; // $_POST["chbx_armsArmboardsRight"];

                $eyeTapedLeft = $json->Eyes->Taped_Yes_No; // $_POST["chbx_eyeTapedLeft"];
                $eyeLubedLeft = $json->Eyes->Lubed_Yes_No; //$_POST["chbx_eyeLubedLeft"];
                /*
                  $eyeTapedRight = $_POST["chbx_eyeTapedRight"];
                  $eyeLubedRight = $_POST["chbx_eyeLubedRight"];
                 */
                $pressurePointsPadded = $json->Eyes->PressurePointsPadded; // $_POST["chbx_pressurePointsPadded"];
                $bss = $json->Eyes->BSS; //$_POST["chbx_bss"];
                $warning = $json->Eyes->Warming; //$_POST["chbx_warning"];
                $temp = $json->Eyes->Devicetemp; // $_POST["txt_temp"];
                $StableCardioRespiratory = $json->PACU->StableCardioRespiratory; // $_POST["chbx_StableCardioRespiratory"];

                $graphComments = addslashes($json->graphComments); //$_POST["graphComments"]
                if (trim($graphComments) == 'comment') {
                    $graphComments = '';
                }

                $evaluation = addslashes($json->Evaluation->textdetails); //$_POST["txtarea_evaluation"]
                $comments = addslashes(trim($json->comments)); //$_POST["txtarea_comments"]
                $anesthesiologistId = $anesthesiologist_id; // $_POST["anesthesiologistId"];
                $anesthesiologistSign = ''; // $_POST["anesthesiologistSign"];
                $relivedNurseId = $json->ReliefNurlse->selected; // $_POST["relivedNurseIdList"];
                //START CODE TO CHECK NURSE,SURGEON, ANESTHESIOLOGIST SIGN IN DATABASE
                $chkUserSignDetails = $this->getRowRecord('genanesthesiarecord', 'confirmation_id', $pConfirmId);
                if ($chkUserSignDetails) {
                    $chk_signAnesthesia1Id = $chkUserSignDetails->signAnesthesia1Id;
                    $chk_signAnesthesia2Id = $chkUserSignDetails->signAnesthesia2Id;
                    $chk_form_status = $chkUserSignDetails->form_status;
                    $chk_vitalSignGridStatus = $chkUserSignDetails->vitalSignGridStatus;
                    $chk_versionNum = $chkUserSignDetails->version_num;
                    $chk_versionDateTime = $chkUserSignDetails->version_date_time;
                }
                //END CODE TO CHECK NURSE,SURGEON, ANESTHESIOLOGIST SIGN IN DATABASE
                $vitalSignGridStatus = $this->loadVitalSignGridStatus($chk_form_status, $chk_vitalSignGridStatus, 'genAnes');
                $vitalSignGridQuery = '';
                if ($chk_form_status <> 'completed' && $chk_form_status <> 'not completed') {
                    $vitalSignGridQuery = ", vitalSignGridStatus = '" . $vitalSignGridStatus . "'  ";
                }

                $version_num = $chk_versionNum;
                $versionNumQry = '';
                if (!$chk_versionNum) {
                    $version_date_time = $chk_versionDateTime;
                    if ($version_date_time == '' || $version_date_time == '0000-00-00 00:00:00') {
                        $version_date_time = date('Y-m-d H:i:s');
                    }
                    if ($chk_form_status == 'completed' || $chk_form_status == 'not completed') {
                        $version_num = 1;
                    } else {
                        $version_num = 2;
                    }
                    $versionNumQry = " version_num = '" . $version_num . "', version_date_time = '" . $version_date_time . "',  ";
                }

                if ($version_num > 1) {
                    $reliefNurseId = $json->ReliefNurse_Anesthesia->selected; //$_POST['reliefNurseId'];
                    $confirmIPPSC_signin = addslashes($json->Confirmationofidentifyprocedureproceduresiteandconsent); //$_POST['chbx_ipp']
                    $siteMarked = addslashes($json->Sitemarkedbypersonperformingtheprocedure); //$_POST['chbx_smpp']
                    $patientAllergies = addslashes($json->Patientallergies_Check); //$_POST['chbx_pa']
                    $difficultAirway = addslashes($json->Difficultairwayoraspirationrisk); //$_POST['chbx_dar']
                    $anesthesiaSafety = addslashes($json->Anesthesiasafetycheckcompleted); //$_POST['chbx_asc']
                    $allMembersTeam = addslashes($json->Allmembersoftheteamhavediscussedcareplanandaddressedconcerns); //$_POST['chbx_adcpc']
                    $riskBloodLoss = addslashes($json->Riskofbloodloss->yes_no_na); //$_POST['chbx_rbl']
                    $bloodLossUnits = ($json->Riskofbloodloss->yes_no_na == 'Yes') ? addslashes($json->Riskofbloodloss->bloodLossUnits) : '';
                }

                $PatientReassessed = $json->PatientReassessed; //$_POST["chbx_PatientReassessed"];
                $MachineEquipment = $json->MachineEquipmentCompleted; //$_POST["chbx_MachineEquipment"];
                //SET FORM STATUS ACCORDING TO MANDATORY FIELD
                $form_status = "completed";

                if ($anesthesiaClass == "" || ($PatientReassessed == "" && $MachineEquipment == "") || ($startTime == "") || ($stopTime == "")
                        //|| ($macMiller=="")
                        || ($Tube_lma_mask == "") || ($Monitor_ekg == "" && $Monitor_etco2 == "" && $Monitor_etco2Sat == "" && $Monitor_o2Temp == "" && $Monitor_PNS == "") || ($armsTuckedLeft == "" && $armsTuckedRight == "" && $armsArmboardsLeft == "" && $armsArmboardsRight == "")
                        //|| ($eyeTapedLeft=="" && $eyeTapedRight=="" && $eyeLubedLeft=="" && $eyeLubedRight=="")
                        || ($pressurePointsPadded == "" && $bss == "" && $warning == "") || trim($evaluation) == "" || ($bp == "" && $P == "" && $rr == "" && $sao == "" && $StableCardioRespiratory == "") || ($chk_signAnesthesia1Id == "0" && $confirmAnes_NA != 'Yes')
                ) {
                    $form_status = "not completed";
                }

                if ($version_num > 1 && $form_status == "completed") {
                    if ($confirmIPPSC_signin == '' || $siteMarked == '' || $patientAllergies == '' || $difficultAirway == '' || $anesthesiaSafety == '' || $allMembersTeam == '' || $riskBloodLoss == '' || ($chk_signAnesthesia2Id == "0" && $confirmAnes_NA != 'Yes')
                    ) {
                        $form_status = "not completed";
                    }
                }

                //END SET FORM STATUS ACCORDING TO MANDATORY FIELD
                //START CODE TO RESET THE RECORD
                $resetRecordQry = '';
                /*if ($_REQUEST['hiddResetStatusId'] == 'Yes') {
                    $form_status = '';
                    $genAnesSignApplet = '';
                    $resetRecordQry = "signAnesthesia1Id			='',
								 signAnesthesia1FirstName	='', 
								 signAnesthesia1MiddleName	='',
								 signAnesthesia1LastName	='', 
								 signAnesthesia1Status		='',
								 signAnesthesia1DateTime	='',
								 signAnesthesia2Id			='',
								 signAnesthesia2FirstName	='', 
								 signAnesthesia2MiddleName	='',
								 signAnesthesia2LastName	='', 
								 signAnesthesia2Status		='',
								 signAnesthesia2DateTime	='',
								 version_num				='0',
								 version_date_time			='0000-00-00',
								 resetDateTime 				= '" . date('Y-m-d H:i:s') . "',
								 resetBy 					= '" . $userId. "',
								";
                }*/
                //END CODE TO RESET THE RECORD

                $chkgenAnesQry = "select * from `genanesthesiarecord` where  confirmation_id = '" . $pConfirmId . "'";
                $chkgenAnesRes = DB::selectone($chkgenAnesQry); // or die(imw_error());
                if ($chkgenAnesRes) {
                    //CODE START TO CHECK FORM STATUS (IF EMPTY THEN REFRESH SLIDER ON SAVE)
                    $chk_form_status = $chkgenAnesRes->form_status;
                    //CODE START TO CHECK FORM STATUS (IF EMPTY THEN REFRESH SLIDER ON SAVE)

                    $SavegenAnesQry = "update `genanesthesiarecord` set
									drawing_path='" . addslashes($up_img) . "',
									drawing_coords='" . addslashes($cnvs_anes_drw_coords) . "',
									alertOriented = '$alertOriented',
									assistedByTranslator = '$assistedByTranslator',
									bp = '$bp',
									P = '$P',
									rr = '$rr',
									sao = '$sao',
									anesthesiaClass = '$anesthesiaClass',
									patientVerified = '$patientVerified',
									BMIvalue = '$BMIvalue',
									PatientReassessed = '$PatientReassessed',
									MachineEquipment = '$MachineEquipment',
									o2n2oavailable = '$o2n2oavailable',
									reserveTanksChecked = '$reserveTanksChecked',
									positivePressureAvailable = '$positivePressureAvailable',
									maskTubingPresent = '$maskTubingPresent',
									vaporizorFilled = '$vaporizorFilled',
									absorberFunctional = '$absorberFunctional',
									gasEvacuatorFunctional = '$gasEvacuatorFunctional',
									o2AnalyzerFunctional = '$o2AnalyzerFunctional',
									ekgMonitor = '$ekgMonitor',
									endoTubes = '$endoTubes',
									laryngoscopeBlades = '$laryngoscopeBlades',
									others = '$others',
									othersDesc = '$othersDesc',
									startTime = '$startTime',
									stopTime = '$stopTime',
									mac = '$mac',
									macValue = '$macValue', 
									millar = '$millar',
									millarValue = '$millarValue', 
									etTube = '$etTube',
									etTubeSize = '$etTubeSize', 
									lma = '$lma',
									lmaSize = '$lmaSize',
									mask = '$mask',
									teethUnchanged = '$teethUnchanged',
									Monitor_ekg = '$Monitor_ekg', 
									Monitor_etco2 = '$Monitor_etco2', 
									Monitor_etco2Sat = '$Monitor_etco2Sat',
									Monitor_o2Temp = '$Monitor_o2Temp', 
									Monitor_PNS = '$Monitor_PNS', 
									genAnesSignApplet = '$genAnesSignApplet',
									armsTuckedLeft = '$armsTuckedLeft', 
									armsTuckedRight = '$armsTuckedRight', 
									armsArmboardsLeft = '$armsArmboardsLeft',
									armsArmboardsRight = '$armsArmboardsRight', 
									eyeTapedLeft = '$eyeTapedLeft',
									eyeLubedLeft = '$eyeLubedLeft',
									pressurePointsPadded = '$pressurePointsPadded',
									bss = '$bss',
									warning = '$warning',
									temp = '$temp',
									StableCardioRespiratory = '$StableCardioRespiratory',
									graphComments = '$graphComments',
									evaluation = '$evaluation',
									comments = '$comments',
									anesthesiologistId = '$anesthesiologistId',
									anesthesiologistSign = '$anesthesiologistSign',
									relivedNurseId = '$relivedNurseId',
									reliefNurseId					= '$reliefNurseId',
									confirmIPPSC_signin		= '$confirmIPPSC_signin',
									siteMarked 						= '$siteMarked',
									patientAllergies 			= '$patientAllergies',
									difficultAirway				= '$difficultAirway',
									anesthesiaSafety			= '$anesthesiaSafety',
									allMembersTeam 				= '$allMembersTeam',
									riskBloodLoss 				= '$riskBloodLoss',
									bloodLossUnits				=	'$bloodLossUnits',	
									$resetRecordQry
									$versionNumQry
									form_status ='" . $form_status . "',
									ascId='" . $ascId . "'
									" . $vitalSignGridQuery . " 
									WHERE confirmation_id='" . $pConfirmId . "'";
                } else {
                    $SavegenAnesQry = "insert into `genanesthesiarecord` set 
									drawing_path='" . addslashes($up_img) . "',
									drawing_coords='" . addslashes($cnvs_anes_drw_coords) . "',
									alertOriented = '$alertOriented',
									assistedByTranslator = '$assistedByTranslator',
									bp = '$bp',
									P = '$P',
									rr = '$rr',
									sao = '$sao',
									anesthesiaClass = '$anesthesiaClass',
									patientVerified = '$patientVerified',
									BMIvalue = '$BMIvalue',
									PatientReassessed = '$PatientReassessed',
									MachineEquipment = '$MachineEquipment',
									o2n2oavailable = '$o2n2oavailable',
									reserveTanksChecked = '$reserveTanksChecked',
									positivePressureAvailable = '$positivePressureAvailable',
									maskTubingPresent = '$maskTubingPresent',
									vaporizorFilled = '$vaporizorFilled',
									absorberFunctional = '$absorberFunctional',
									gasEvacuatorFunctional = '$gasEvacuatorFunctional',
									o2AnalyzerFunctional = '$o2AnalyzerFunctional',
									ekgMonitor = '$ekgMonitor',
									endoTubes = '$endoTubes',
									laryngoscopeBlades = '$laryngoscopeBlades',
									others = '$others',
									othersDesc = '$othersDesc',
									startTime = '$startTime',
									stopTime = '$stopTime',
									mac = '$mac',
									macValue = '$macValue', 
									millar = '$millar',
									millarValue = '$millarValue', 
									etTube = '$etTube',
									etTubeSize = '$etTubeSize', 
									lma = '$lma',
									lmaSize = '$lmaSize',
									mask = '$mask',
									teethUnchanged = '$teethUnchanged',
									Monitor_ekg = '$Monitor_ekg', 
									Monitor_etco2 = '$Monitor_etco2', 
									Monitor_etco2Sat = '$Monitor_etco2Sat',
									Monitor_o2Temp = '$Monitor_o2Temp', 
									Monitor_PNS = '$Monitor_PNS', 
									genAnesSignApplet = '$genAnesSignApplet',
									armsTuckedLeft = '$armsTuckedLeft', 
									armsTuckedRight = '$armsTuckedRight', 
									armsArmboardsLeft = '$armsArmboardsLeft',
									armsArmboardsRight = '$armsArmboardsRight', 
									eyeTapedLeft = '$eyeTapedLeft',
									eyeLubedLeft = '$eyeLubedLeft',
									pressurePointsPadded = '$pressurePointsPadded',
									bss = '$bss',
									warning = '$warning',
									temp = '$temp',
									StableCardioRespiratory = '$StableCardioRespiratory',
									graphComments = '$graphComments',
									evaluation = '$evaluation',
									comments = '$comments',
									anesthesiologistId = '$anesthesiologistId',
									anesthesiologistSign = '$anesthesiologistSign',
									relivedNurseId = '$relivedNurseId',
									reliefNurseId					= '$reliefNurseId',
									confirmIPPSC_signin		= '$confirmIPPSC_signin',
									siteMarked 						= '$siteMarked',
									patientAllergies 			= '$patientAllergies',
									difficultAirway				= '$difficultAirway',
									anesthesiaSafety			= '$anesthesiaSafety',
									allMembersTeam 				= '$allMembersTeam',
									riskBloodLoss 				= '$riskBloodLoss',
									bloodLossUnits				=	'$bloodLossUnits',
									$resetRecordQry
									$versionNumQry
									form_status ='" . $form_status . "',									
									confirmation_id='" . $pConfirmId . "',
									patient_id = '" . $patient_id . "'
									" . $vitalSignGridQuery . " 
									";
                }
                $SavegenAnesRes = DB::select($SavegenAnesQry);
                	$fieldName = "genral_anesthesia_form";
                //SAVE ENTRY IN chartnotes_change_audit_tbl 

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
                $SaveAuditChartNotesRes = DB::select($SaveAuditChartNotesQry);
                //END SAVE ENTRY IN chartnotes_change_audit_tbl
                //CODE TO CHECK ANESTHESIOLOGIST ALL SIGNATURE AND SET VALUE IN STUB TABLE
                $chartSignedByAnes = $this->chkAnesSignNew($pConfirmId);
                $updateAnesStubTblQry = "UPDATE stub_tbl SET chartSignedByAnes='" . $chartSignedByAnes . "' WHERE patient_confirmation_id='" . $pConfirmId . "'";
                $updateAnesStubTblRes = DB::select($updateAnesStubTblQry);
                //END CODE TO CHECK ANESTHESIOLOGIST SIGNATURE AND SET VALUE IN STUB TABLE
                //REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)
                //REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)
            }

            $status = 1;
            $savedStatus=1;
            $message = 'Saved Successfully !';
        }

        return response()->json(['status' => $status,'savedStatus'=>$savedStatus, 'message' => $message, 'requiredStatus' => '', 'data' => $data,
                        ], 200, [ 'Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

    function convertBase64_GenAnesRec($imagesrc, $instructionSheetId, $patient_id, $pConfirmId) {
        $imagesrc = str_ireplace(' ', '+', $imagesrc);
        $imageName = $instructionSheetId . "_" . $patient_id . "_" . $pConfirmId . '.' . 'png';
        if (trim($imagesrc) <> "") {
            //admin\pdfFiles\gen_anes_detail
            $newfile = explode(",", $imagesrc);
            if (!file_exists('../../SigPlus_images/PatientId_' . $patient_id)) {
                mkdir('../../SigPlus_images/PatientId_' . $patient_id, 0777);
            }
            if (!file_exists('../../SigPlus_images/PatientId_' . $patient_id . "/gen_anes_detail/")) {
                mkdir('../../SigPlus_images/PatientId_' . $patient_id . "/gen_anes_detail/", 0777);
            }
            $success = @file_put_contents('../../SigPlus_images/PatientId_' . $patient_id . "/gen_anes_detail/" . $imageName, base64_decode($newfile[1]));
            if ($success) {
                return '../../SigPlus_images/PatientId_' . $patient_id . "/gen_anes_detail/" . $imageName;
            }
        }
        return '';
    }

}
