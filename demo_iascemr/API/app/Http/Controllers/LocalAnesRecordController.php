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

class LocalAnesRecordController extends Controller {

    public function LocalAnesRecordController_form(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $loginUserType = $request->json()->get('UserType') ? $request->json()->get('UserType') : $request->input('UserType');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
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
                $detailConfirmation = $this->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfirmId);
                $finalizeStatus = $detailConfirmation->finalize_status;
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
                /*                 * *******************Fetching Record******************************* */

                //START DISPLAY FP EXAM PERFORMED BY DEFAULT
                $fpExamPerformedVisibility = 'visible';
                //END DISPLAY FP EXAM PERFORMED BY DEFAULT
                // GETTTING LOCAL ANES RECORD IF EXISTS
                $localAnesRecordDetails = DB::selectone("select *,date_format(signAnesthesia1DateTime,'%m-%d-%Y %h:%i %p') as signAnesthesia1DateTimeFormat , date_format(signAnesthesia2DateTime,'%m-%d-%Y %h:%i %p') as signAnesthesia2DateTimeFormat , date_format(signAnesthesia3DateTime,'%m-%d-%Y %h:%i %p') as signAnesthesia3DateTimeFormat, date_format(signAnesthesia4DateTime,'%m-%d-%Y %h:%i %p') as signAnesthesia4DateTimeFormat from localanesthesiarecord where confirmation_id=$pConfirmId");
                $localanesFormStatus = '';
                $localAnesRecordDetailsMedGrid = DB::selectone("select * from localanesthesiarecordmedgrid where confirmation_id=$pConfirmId");
                $localAnesRecordDetailsMedGridSec = DB::selectone("select * from localanesthesiarecordmedgridsec where confirmation_id=$pConfirmId");

                if ($localAnesRecordDetails) {
                    $form_status = $localAnesRecordDetails->form_status;
                    $vitalSignGridStatus = $this->loadVitalSignGridStatus($form_status, $localAnesRecordDetails->vitalSignGridStatus, 'macAnes');
                    $version_num = $localAnesRecordDetails->version_num;
                    $reliefNurseId = $localAnesRecordDetails->reliefNurseId;
                    if (!($version_num) && ($form_status == 'completed' || $form_status == 'not completed')) {
                        $version_num = 1;
                    } else if (!($version_num) && $form_status <> 'completed' && $form_status <> 'not completed') {
                        $version_num = 4;
                    }
                    $orStartTime = $this->calculate_timeFun($localAnesRecordDetails->orStartTime); //CODE TO DISPLAY OR START TIME
                    $orStopTime = $this->calculate_timeFun($localAnesRecordDetails->orStopTime); //CODE TO DISPLAY OR STOP TIME
                    $startTime = $this->calculate_timeFun($localAnesRecordDetails->startTime); //CODE TO DISPLAY START TIME
                    $stopTime = $this->calculate_timeFun($localAnesRecordDetails->stopTime); //CODE TO DISPLAY STOP TIME
                    $newStartTime1 = $this->calculate_timeFun($localAnesRecordDetails->newStartTime1); //CODE TO DISPLAY New START TIME 1
                    $newStopTime1 = $this->calculate_timeFun($localAnesRecordDetails->newStopTime1); //CODE TO DISPLAY New STOP TIME 1
                    $newStartTime2 = $this->calculate_timeFun($localAnesRecordDetails->newStartTime2); //CODE TO DISPLAY New START TIME 2
                    $newStopTime2 = $this->calculate_timeFun($localAnesRecordDetails->newStopTime2); //CODE TO DISPLAY New STOP TIME 2
                    $newStartTime3 = $this->calculate_timeFun($localAnesRecordDetails->newStartTime3); //CODE TO DISPLAY New START TIME 3
                    $newStopTime3 = $this->calculate_timeFun($localAnesRecordDetails->newStopTime3); //CODE TO DISPLAY New STOP TIME 3
                    $localanesFormStatus = $form_status;

                    $med_grid_id = $localAnesRecordDetailsMedGrid->med_grid_id;
                    $blank1_label = $localAnesRecordDetailsMedGrid->blank1_label;
                    $blank2_label = $localAnesRecordDetailsMedGrid->blank2_label;
                    $blank3_label = $localAnesRecordDetailsMedGrid->blank3_label;
                    $blank4_label = $localAnesRecordDetailsMedGrid->blank4_label;
                    $mgPropofol_label = $localAnesRecordDetailsMedGrid->mgPropofol_label;
                    $mgMidazolam_label = $localAnesRecordDetailsMedGrid->mgMidazolam_label;
                    $mgKetamine_label = $localAnesRecordDetailsMedGridSec->mgKetamine_label;
                    $mgLabetalol_label = $localAnesRecordDetailsMedGridSec->mgLabetalol_label;
                    $mcgFentanyl_label = $localAnesRecordDetailsMedGridSec->mcgFentanyl_label;
                }
                // GETTTING LOCAL ANES RECORD IF EXISTS  
                //GET DEFUALT VALUES OF ASSIGNED ANESTHEOLOGIST FROM ADMIN PANEL
                if ($form_status == "") {
                    //START GET EKG MEDICATION 
                    $getEkgAdminTblDetails = DB::selectone("select * from anes_ekg_admin_tbl where anes_ekg_admin_id=1");
                    $mgPropofol_label = $getEkgAdminTblDetails->mgPropofol_label;
                    $mgMidazolam_label = $getEkgAdminTblDetails->mgMidazolam_label;
                    $mgKetamine_label = $getEkgAdminTblDetails->mgKetamine_label;
                    $mgLabetalol_label = $getEkgAdminTblDetails->mgLabetalol_label;
                    $mcgFentanyl_label = $getEkgAdminTblDetails->mcgFentanyl_label;
                    //END GET EKG MEDICATIOM
                    $detailAnesthesiaProfile = $this->getRowRecord('anesthesia_profile_tbl', 'anesthesiologistId ', $patientConfirm_anesthesiologist_id);
                    if ($detailAnesthesiaProfile) {
                        //$anesthesia_profile_sign = $detailAnesthesiaProfile->anesthesia_profile_sign;
                        $anesthesia_profile_sign_path = $detailAnesthesiaProfile->anesthesia_profile_sign_path;

                        if (trim($anesthesia_profile_sign_path)) {
                            $patientInterviewed = $detailAnesthesiaProfile->patientInterviewed;
                            $chartNotesReviewed = $detailAnesthesiaProfile->chartNotesReviewed;
                            $npo = $detailAnesthesiaProfile->npo;
                            $procedurePrimaryVerified = $detailAnesthesiaProfile->procedurePrimaryVerified;
                            $procedureSecondaryVerified = $detailAnesthesiaProfile->procedureSecondaryVerified;
                            $siteVerified = $detailAnesthesiaProfile->siteVerified;
                            $evaluation2 = $detailAnesthesiaProfile->evaluation2;
                            $dentation = $detailAnesthesiaProfile->dentation;
                            $stableCardiPlumFunction = $detailAnesthesiaProfile->stableCardiPlumFunction;
                            $planAnesthesia = $detailAnesthesiaProfile->planAnesthesia;
                            $allQuesAnswered = $detailAnesthesiaProfile->allQuesAnswered;

                            $routineMonitorApplied = $detailAnesthesiaProfile->routineMonitorApplied;
                            $hide_anesthesia_grid = $detailAnesthesiaProfile->hide_anesthesia_grid;
                            $o2lpm_count = $detailAnesthesiaProfile->o2lpm_count;
                            if ($o2lpm_count > 0) {
                                for ($t = 1; $t <= $o2lpm_count; $t++) {
                                    $o2lpm_ = "o2lpm_" . $t;
                                    $$o2lpm_ = $detailAnesthesiaProfile->o2lpm_1;
                                }
                            }
                            //echo '<br>'.$o2lpm_1;
                            $ekgBigRowValue = $detailAnesthesiaProfile->ekgBigRowValue;
                            $anyKnowAnestheticComplication = $detailAnesthesiaProfile->anyKnowAnestheticComplication;
                            $stableCardiPlumFunction2 = $detailAnesthesiaProfile->stableCardiPlumFunction2;
                            $satisfactoryCondition4Discharge = $detailAnesthesiaProfile->satisfactoryCondition4Discharge;
                            $evaluation = $detailAnesthesiaProfile->evaluation;
                            $chbx_enable_postop_desc = $detailAnesthesiaProfile->chbx_enable_postop_desc;
                            $chbx_vss = $detailAnesthesiaProfile->chbx_vss;
                            $chbx_atsf = $detailAnesthesiaProfile->chbx_atsf;
                            $chbx_pa = $detailAnesthesiaProfile->chbx_pa;
                            $chbx_nausea = $detailAnesthesiaProfile->chbx_nausea;
                            $chbx_vomiting = $detailAnesthesiaProfile->chbx_vomiting;
                            $chbx_dizziness = $detailAnesthesiaProfile->chbx_dizziness;
                            $chbx_rd = $detailAnesthesiaProfile->chbx_rd;
                            $chbx_aao = $detailAnesthesiaProfile->chbx_aao;
                            $chbx_ddai = $detailAnesthesiaProfile->chbx_ddai;
                            $chbx_pv = $detailAnesthesiaProfile->chbx_pv;
                            $chbx_rtpog = $detailAnesthesiaProfile->chbx_rtpog;
                            $chbx_pain = $detailAnesthesiaProfile->chbx_pain;

                            $remarks = $detailAnesthesiaProfile->remarks;

                            $honanballon = $detailAnesthesiaProfile->honanballon;
                            $honanBallonAnother = $detailAnesthesiaProfile->honanBallonAnother;
                            $none = $detailAnesthesiaProfile->NoneHonanBalloon;
                            $digital = $detailAnesthesiaProfile->digital;
                            $copyBaseLineVitalSigns = $detailAnesthesiaProfile->copyBaseLineVitalSigns;

                            $fpExamPerformed = $detailAnesthesiaProfile->fpExamPerformed;
                            if ($fpExamPerformed != 'Yes') {
                                $fpExamPerformedVisibility = 'hidden';
                            }
                            $ansComment = $detailAnesthesiaProfile->ansComment;

                            $Block1Aspiration = $detailAnesthesiaProfile->Block1Aspiration;
                            $Block1Full = $detailAnesthesiaProfile->Block1Full;
                            $Block1BeforeInjection = $detailAnesthesiaProfile->Block1BeforeInjection;
                            $Block1RockNegative = $detailAnesthesiaProfile->Block1RockNegative;
                            $Block2Aspiration = $detailAnesthesiaProfile->Block2Aspiration;
                            $Block2Full = $detailAnesthesiaProfile->Block2Full;
                            $Block2BeforeInjection = $detailAnesthesiaProfile->Block2BeforeInjection;
                            $Block2RockNegative = $detailAnesthesiaProfile->Block2RockNegative;

                            $txtInterOpDrugs1 = $detailAnesthesiaProfile->txtInterOpDrugs1;
                            $txtInterOpDrugs2 = $detailAnesthesiaProfile->txtInterOpDrugs2;

                            $confirmIPPSC_signin = stripslashes($detailAnesthesiaProfile->confirmIPPSC_signin);
                            $siteMarked = stripslashes($detailAnesthesiaProfile->siteMarked);
                            $patientAllergies = stripslashes($detailAnesthesiaProfile->patientAllergies);
                            $difficultAirway = stripslashes($detailAnesthesiaProfile->difficultAirway);
                            $anesthesiaSafety = stripslashes($detailAnesthesiaProfile->anesthesiaSafety);
                            $allMembersTeam = stripslashes($detailAnesthesiaProfile->allMembersTeam);
                            $riskBloodLoss = stripslashes($detailAnesthesiaProfile->riskBloodLoss);
                            $bloodLossUnits = stripslashes($detailAnesthesiaProfile->bloodLossUnits);
                        }
                    }

                    //CHECK FOR HONAN BALLON FOR LOGGED IN ANESTHESIOLOGIST

                    if ($logInUserType == 'Anesthesiologist') {
                        $detailAnesthesiaProfileHonanBallon = $this->getRowRecord('anesthesia_profile_tbl', 'anesthesiologistId ', $userId);
                        if ($detailAnesthesiaProfileHonanBallon) {
                            //$anesthesia_profile_signHonanBallon = $detailAnesthesiaProfileHonanBallon->anesthesia_profile_sign;
                            $anesthesia_profile_sign_path_HonanBallon = $detailAnesthesiaProfileHonanBallon->anesthesia_profile_sign_path;
                            if (trim($anesthesia_profile_sign_path_HonanBallon)) {

                                $patientInterviewed = $detailAnesthesiaProfileHonanBallon->patientInterviewed;
                                $chartNotesReviewed = $detailAnesthesiaProfileHonanBallon->chartNotesReviewed;
                                $npo = $detailAnesthesiaProfileHonanBallon->npo;
                                $procedurePrimaryVerified = $detailAnesthesiaProfileHonanBallon->procedurePrimaryVerified;
                                $procedureSecondaryVerified = $detailAnesthesiaProfileHonanBallon->procedureSecondaryVerified;
                                $siteVerified = $detailAnesthesiaProfileHonanBallon->siteVerified;
                                $evaluation2 = $detailAnesthesiaProfileHonanBallon->evaluation2;
                                $dentation = $detailAnesthesiaProfileHonanBallon->dentation;
                                $stableCardiPlumFunction = $detailAnesthesiaProfileHonanBallon->stableCardiPlumFunction;
                                $planAnesthesia = $detailAnesthesiaProfileHonanBallon->planAnesthesia;
                                $allQuesAnswered = $detailAnesthesiaProfileHonanBallon->allQuesAnswered;

                                $routineMonitorApplied = $detailAnesthesiaProfileHonanBallon->routineMonitorApplied;
                                $hide_anesthesia_grid = $detailAnesthesiaProfileHonanBallon->hide_anesthesia_grid;

                                $o2lpm_count = $detailAnesthesiaProfileHonanBallon->o2lpm_count;
                                if ($o2lpm_count > 0) {
                                    for ($t = 1; $t <= $o2lpm_count; $t++) {
                                        $o2lpm_ = "o2lpm_" . $t;
                                        $$o2lpm_ = $detailAnesthesiaProfileHonanBallon->o2lpm_1;
                                    }
                                }

                                $ekgBigRowValue = $detailAnesthesiaProfileHonanBallon->ekgBigRowValue;

                                $anyKnowAnestheticComplication = $detailAnesthesiaProfileHonanBallon->anyKnowAnestheticComplication;
                                $stableCardiPlumFunction2 = $detailAnesthesiaProfileHonanBallon->stableCardiPlumFunction2;
                                $satisfactoryCondition4Discharge = $detailAnesthesiaProfileHonanBallon->satisfactoryCondition4Discharge;
                                $evaluation = $detailAnesthesiaProfileHonanBallon->evaluation;
                                $chbx_enable_postop_desc = $detailAnesthesiaProfileHonanBallon->chbx_enable_postop_desc;
                                $chbx_vss = $detailAnesthesiaProfileHonanBallon->chbx_vss;
                                $chbx_atsf = $detailAnesthesiaProfileHonanBallon->chbx_atsf;
                                $chbx_pa = $detailAnesthesiaProfileHonanBallon->chbx_pa;
                                $chbx_nausea = $detailAnesthesiaProfileHonanBallon->chbx_nausea;
                                $chbx_vomiting = $detailAnesthesiaProfileHonanBallon->chbx_vomiting;
                                $chbx_dizziness = $detailAnesthesiaProfileHonanBallon->chbx_dizziness;
                                $chbx_rd = $detailAnesthesiaProfileHonanBallon->chbx_rd;
                                $chbx_aao = $detailAnesthesiaProfileHonanBallon->chbx_aao;
                                $chbx_ddai = $detailAnesthesiaProfileHonanBallon->chbx_ddai;
                                $chbx_pv = $detailAnesthesiaProfileHonanBallon->chbx_pv;
                                $chbx_rtpog = $detailAnesthesiaProfileHonanBallon->chbx_rtpog;
                                $chbx_pain = $detailAnesthesiaProfileHonanBallon->chbx_pain;
                                $remarks = $detailAnesthesiaProfileHonanBallon->remarks;

                                $honanballon = $detailAnesthesiaProfileHonanBallon->honanballon;
                                $honanBallonAnother = $detailAnesthesiaProfileHonanBallon->honanBallonAnother;
                                $none = $detailAnesthesiaProfileHonanBallon->NoneHonanBalloon;
                                $digital = $detailAnesthesiaProfileHonanBallon->digital;
                                $copyBaseLineVitalSigns = $detailAnesthesiaProfileHonanBallon->copyBaseLineVitalSigns;
                                $fpExamPerformed = $detailAnesthesiaProfileHonanBallon->fpExamPerformed;
                                if ($fpExamPerformed != 'Yes') {
                                    $fpExamPerformedVisibility = 'hidden';
                                }
                                $ansComment = $detailAnesthesiaProfileHonanBallon->ansComment;
                                /*
                                  $TopicalAspiration		 				= $detailAnesthesiaProfileHonanBallon->TopicalAspiration ;
                                  $TopicalFull			 				= $detailAnesthesiaProfileHonanBallon->TopicalFull ;
                                  $TopicalBeforeInjection	 				= $detailAnesthesiaProfileHonanBallon->TopicalBeforeInjection ;
                                  $TopicalRockNegative					= $detailAnesthesiaProfileHonanBallon->TopicalRockNegative ;
                                 */
                                $Block1Aspiration = $detailAnesthesiaProfileHonanBallon->Block1Aspiration;
                                $Block1Full = $detailAnesthesiaProfileHonanBallon->Block1Full;
                                $Block1BeforeInjection = $detailAnesthesiaProfileHonanBallon->Block1BeforeInjection;
                                $Block1RockNegative = $detailAnesthesiaProfileHonanBallon->Block1RockNegative;
                                $Block2Aspiration = $detailAnesthesiaProfileHonanBallon->Block2Aspiration;
                                $Block2Full = $detailAnesthesiaProfileHonanBallon->Block2Full;
                                $Block2BeforeInjection = $detailAnesthesiaProfileHonanBallon->Block2BeforeInjection;
                                $Block2RockNegative = $detailAnesthesiaProfileHonanBallon->Block2RockNegative;

                                $txtInterOpDrugs1 = $detailAnesthesiaProfileHonanBallon->txtInterOpDrugs1;
                                $txtInterOpDrugs2 = $detailAnesthesiaProfileHonanBallon->txtInterOpDrugs2;

                                $confirmIPPSC_signin = stripslashes($detailAnesthesiaProfileHonanBallon->confirmIPPSC_signin);
                                $siteMarked = stripslashes($detailAnesthesiaProfileHonanBallon->siteMarked);
                                $patientAllergies = stripslashes($detailAnesthesiaProfileHonanBallon->patientAllergies);
                                $difficultAirway = stripslashes($detailAnesthesiaProfileHonanBallon->difficultAirway);
                                $anesthesiaSafety = stripslashes($detailAnesthesiaProfileHonanBallon->anesthesiaSafety);
                                $allMembersTeam = stripslashes($detailAnesthesiaProfileHonanBallon->allMembersTeam);
                                $riskBloodLoss = stripslashes($detailAnesthesiaProfileHonanBallon->riskBloodLoss);
                                $bloodLossUnits = stripslashes($detailAnesthesiaProfileHonanBallon->bloodLossUnits);
                            }
                        }
                    }
                    //END CHECK FOR HONAN BALLON FOR LOGGED IN ANESTHESIOLOGIST
                }

                //IF RECORD NOT SAVED BY ANESTHESIOLOGIST AT LEAST ONCE THEN GET VALUE FROM ADMIN 
                if (($form_status == "completed" || $form_status == "not completed") && @$saveByAnes == 'Yes') {
                    //DO NOTHING
                } else {

                    if ($logInUserType == 'Anesthesiologist') {
                        $chkAnsId = $userId;
                    } else {
                        $chkAnsId = $patientConfirm_anesthesiologist_id;
                    }
                    $detailAnesthesiaTopicalBlockProfile = $this->getRowRecord('anesthesia_profile_tbl', 'anesthesiologistId ', $chkAnsId);
                    if ($detailAnesthesiaTopicalBlockProfile) {

                        //$anesthesia_topical_block_profile_sign = $detailAnesthesiaTopicalBlockProfile->anesthesia_profile_sign;
                        $anesthesia_topical_block_profile_sign_path = $detailAnesthesiaTopicalBlockProfile->anesthesia_profile_sign_path;

                        if (trim($anesthesia_topical_block_profile_sign_path)) {
                            $Topicaltopical4PercentLidocaine = $detailAnesthesiaTopicalBlockProfile->Topicaltopical4PercentLidocaine;
                            $TopicalIntracameral = $detailAnesthesiaTopicalBlockProfile->TopicalIntracameral;
                            $TopicalIntracameral1percentLidocaine = $detailAnesthesiaTopicalBlockProfile->TopicalIntracameral1percentLidocaine;
                            $TopicalPeribulbar = $detailAnesthesiaTopicalBlockProfile->TopicalPeribulbar;
                            $TopicalPeribulbar2percentLidocaine = $detailAnesthesiaTopicalBlockProfile->TopicalPeribulbar2percentLidocaine;
                            $TopicalRetrobulbar = $detailAnesthesiaTopicalBlockProfile->TopicalRetrobulbar;
                            $TopicalRetrobulbar4percentLidocaine = $detailAnesthesiaTopicalBlockProfile->TopicalRetrobulbar4percentLidocaine;
                            $TopicalHyalauronidase4percentLidocaine = $detailAnesthesiaTopicalBlockProfile->TopicalHyalauronidase4percentLidocaine;
                            $TopicalVanLindr = $detailAnesthesiaTopicalBlockProfile->TopicalVanLindr;
                            $TopicalVanLindrHalfPercentLidocaine = $detailAnesthesiaTopicalBlockProfile->TopicalVanLindrHalfPercentLidocaine;
                            $topical_bupivacaine75 = $detailAnesthesiaTopicalBlockProfile->topical_bupivacaine75;
                            $topical_marcaine75 = $detailAnesthesiaTopicalBlockProfile->topical_marcaine75;
                            $TopicallidTxt = $detailAnesthesiaTopicalBlockProfile->TopicallidTxt;
                            $Topicallid = $detailAnesthesiaTopicalBlockProfile->Topicallid;
                            $TopicallidEpi5ug = $detailAnesthesiaTopicalBlockProfile->TopicallidEpi5ug;
                            $TopicalotherRegionalAnesthesiaTxt1 = $detailAnesthesiaTopicalBlockProfile->TopicalotherRegionalAnesthesiaTxt1;
                            $TopicalotherRegionalAnesthesiaDrop = $detailAnesthesiaTopicalBlockProfile->TopicalotherRegionalAnesthesiaDrop;
                            $TopicalotherRegionalAnesthesiaWydase15u = $detailAnesthesiaTopicalBlockProfile->TopicalotherRegionalAnesthesiaWydase15u;
                            $TopicalotherRegionalAnesthesiaTxt2 = $detailAnesthesiaTopicalBlockProfile->TopicalotherRegionalAnesthesiaTxt2;

                            $Block1topical4PercentLidocaine = $detailAnesthesiaTopicalBlockProfile->Block1topical4PercentLidocaine;
                            $Block1Intracameral = $detailAnesthesiaTopicalBlockProfile->Block1Intracameral;
                            $Block1Intracameral1percentLidocaine = $detailAnesthesiaTopicalBlockProfile->Block1Intracameral1percentLidocaine;
                            $Block1Peribulbar = $detailAnesthesiaTopicalBlockProfile->Block1Peribulbar;
                            $Block1Peribulbar2percentLidocaine = $detailAnesthesiaTopicalBlockProfile->Block1Peribulbar2percentLidocaine;
                            $Block1Retrobulbar = $detailAnesthesiaTopicalBlockProfile->Block1Retrobulbar;
                            $Block1Retrobulbar4percentLidocaine = $detailAnesthesiaTopicalBlockProfile->Block1Retrobulbar4percentLidocaine;
                            $Block1Hyalauronidase4percentLidocaine = $detailAnesthesiaTopicalBlockProfile->Block1Hyalauronidase4percentLidocaine;
                            $Block1VanLindr = $detailAnesthesiaTopicalBlockProfile->Block1VanLindr;
                            $Block1VanLindrHalfPercentLidocaine = $detailAnesthesiaTopicalBlockProfile->Block1VanLindrHalfPercentLidocaine;
                            $block1_bupivacaine75 = $detailAnesthesiaTopicalBlockProfile->block1_bupivacaine75;
                            $block1_marcaine75 = $detailAnesthesiaTopicalBlockProfile->block1_marcaine75;
                            $Block1lidTxt = $detailAnesthesiaTopicalBlockProfile->Block1lidTxt;
                            $Block1lid = $detailAnesthesiaTopicalBlockProfile->Block1lid;
                            $Block1lidEpi5ug = $detailAnesthesiaTopicalBlockProfile->Block1lidEpi5ug;
                            $Block1otherRegionalAnesthesiaTxt1 = $detailAnesthesiaTopicalBlockProfile->Block1otherRegionalAnesthesiaTxt1;
                            $Block1otherRegionalAnesthesiaDrop = $detailAnesthesiaTopicalBlockProfile->Block1otherRegionalAnesthesiaDrop;
                            $Block1otherRegionalAnesthesiaWydase15u = $detailAnesthesiaTopicalBlockProfile->Block1otherRegionalAnesthesiaWydase15u;
                            $Block1otherRegionalAnesthesiaTxt2 = $detailAnesthesiaTopicalBlockProfile->Block1otherRegionalAnesthesiaTxt2;
                            $Block1Aspiration = $detailAnesthesiaTopicalBlockProfile->Block1Aspiration;
                            $Block1Full = $detailAnesthesiaTopicalBlockProfile->Block1Full;
                            $Block1BeforeInjection = $detailAnesthesiaTopicalBlockProfile->Block1BeforeInjection;
                            $Block1RockNegative = $detailAnesthesiaTopicalBlockProfile->Block1RockNegative;


                            $Block2topical4PercentLidocaine = $detailAnesthesiaTopicalBlockProfile->Block2topical4PercentLidocaine;
                            $Block2Intracameral = $detailAnesthesiaTopicalBlockProfile->Block2Intracameral;
                            $Block2Intracameral1percentLidocaine = $detailAnesthesiaTopicalBlockProfile->Block2Intracameral1percentLidocaine;
                            $Block2Peribulbar = $detailAnesthesiaTopicalBlockProfile->Block2Peribulbar;
                            $Block2Peribulbar2percentLidocaine = $detailAnesthesiaTopicalBlockProfile->Block2Peribulbar2percentLidocaine;
                            $Block2Retrobulbar = $detailAnesthesiaTopicalBlockProfile->Block2Retrobulbar;
                            $Block2Retrobulbar4percentLidocaine = $detailAnesthesiaTopicalBlockProfile->Block2Retrobulbar4percentLidocaine;
                            $Block2Hyalauronidase4percentLidocaine = $detailAnesthesiaTopicalBlockProfile->Block2Hyalauronidase4percentLidocaine;
                            $Block2VanLindr = $detailAnesthesiaTopicalBlockProfile->Block2VanLindr;
                            $Block2VanLindrHalfPercentLidocaine = $detailAnesthesiaTopicalBlockProfile->Block2VanLindrHalfPercentLidocaine;
                            $block2_bupivacaine75 = $detailAnesthesiaTopicalBlockProfile->block2_bupivacaine75;
                            $block2_marcaine75 = $detailAnesthesiaTopicalBlockProfile->block2_marcaine75;
                            $Block2lidTxt = $detailAnesthesiaTopicalBlockProfile->Block2lidTxt;
                            $Block2lid = $detailAnesthesiaTopicalBlockProfile->Block2lid;
                            $Block2lidEpi5ug = $detailAnesthesiaTopicalBlockProfile->Block2lidEpi5ug;
                            $Block2otherRegionalAnesthesiaTxt1 = $detailAnesthesiaTopicalBlockProfile->Block2otherRegionalAnesthesiaTxt1;
                            $Block2otherRegionalAnesthesiaDrop = $detailAnesthesiaTopicalBlockProfile->Block2otherRegionalAnesthesiaDrop;
                            $Block2otherRegionalAnesthesiaWydase15u = $detailAnesthesiaTopicalBlockProfile->Block2otherRegionalAnesthesiaWydase15u;
                            $Block2otherRegionalAnesthesiaTxt2 = $detailAnesthesiaTopicalBlockProfile->Block2otherRegionalAnesthesiaTxt2;
                            $Block2Aspiration = $detailAnesthesiaTopicalBlockProfile->Block2Aspiration;
                            $Block2Full = $detailAnesthesiaTopicalBlockProfile->Block2Full;
                            $Block2BeforeInjection = $detailAnesthesiaTopicalBlockProfile->Block2BeforeInjection;
                            $Block2RockNegative = $detailAnesthesiaTopicalBlockProfile->Block2RockNegative;

                            $confirmIPPSC_signin = stripslashes($detailAnesthesiaTopicalBlockProfile->confirmIPPSC_signin);
                            $siteMarked = stripslashes($detailAnesthesiaTopicalBlockProfile->siteMarked);
                            $patientAllergies = stripslashes($detailAnesthesiaTopicalBlockProfile->patientAllergies);
                            $difficultAirway = stripslashes($detailAnesthesiaTopicalBlockProfile->difficultAirway);
                            $anesthesiaSafety = stripslashes($detailAnesthesiaTopicalBlockProfile->anesthesiaSafety);
                            $allMembersTeam = stripslashes($detailAnesthesiaTopicalBlockProfile->allMembersTeam);
                            $riskBloodLoss = stripslashes($detailAnesthesiaTopicalBlockProfile->riskBloodLoss);
                            $bloodLossUnits = stripslashes($detailAnesthesiaTopicalBlockProfile->bloodLossUnits);
                        }
                    }
                }
                //END IF RECORD NOT SAVED BY ANESTHESIOLOGIST AT LEAST ONCE THEN GET VALUE FROM ADMIN
                $siteTemp = $site; // FROM PATIENT CONFIRMATION TABLE
                // APPLYING NUMBERS TO PATIENT SITE
                if ($siteTemp == 1) {
                    $siteShow = "Left Eye";  //OS
                } else if ($siteTemp == 2) {
                    $siteShow = "Right Eye";  //OD
                } else if ($siteTemp == 3) {
                    $siteShow = "Both Eye";  //OU
                }
                // END APPLYING NUMBERS TO PATIENT SITE

                /*                 * **************************************************************** */
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
                $condArr['chartName'] = 'mac_regional_anesthesia_form';

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
                $signAnesthesia4Id = $localAnesRecordDetails->signAnesthesia4Id;
                $Anesthesia4Name = $loggedInUserName;
                $Anesthesia4SubType = $logInUserSubType;
                $anesthesia4SignOnFileStatus = '';
                $Anesthesia4PreFix = 'Dr.';
                $signAnesthesia4DateTimeFormatNew = $this->getFullDtTmFormat(date("Y-m-d H:i:s"));
                if ($localAnesRecordDetails->signAnesthesia4Id <> 0 && $localAnesRecordDetails->signAnesthesia4Id <> "") {
                    $Anesthesia4Name = $localAnesRecordDetails->signAnesthesia4LastName . ", " . $localAnesRecordDetails->signAnesthesia4FirstName . " " . $localAnesRecordDetails->signAnesthesia4MiddleName;
                    $anesthesia4SignOnFileStatus = $localAnesRecordDetails->signAnesthesia4Status;
                    $signAnesthesia4DateTimeFormatNew = $this->getFullDtTmFormat($localAnesRecordDetails->signAnesthesia4DateTime);
                    $Anesthesia4SubType = $this->getUserSubTypeFun($localAnesRecordDetails->signAnesthesia4Id); //FROM common/commonFunctions.php
                }

                if ($Anesthesia4SubType == 'CRNA') {
                    $Anesthesia4PreFix = '';
                }
                $preOpNursingVitalSignBp = '';
                $preOpNursingVitalSignP = '';
                $preOpNursingVitalSignR = '';
                $preOpNursingVitalSignO2SAT = '';
                $settings = $this->loadSettings('asa_4,anes_mallampetti_score');
                if ($copyBaseLineVitalSigns == "Yes") {
                    $getPreOpNursingDetails = $this->getRowRecord('preopnursingrecord', 'confirmation_id', $pConfirmId);
                    if ($getPreOpNursingDetails) {
                        $preopnursing_vitalsign_id = $getPreOpNursingDetails->preopnursing_vitalsign_id;
                        if ($preopnursing_vitalsign_id) {
                            $ViewPreopNurseVitalHeaderSignQry = "select * from `preopnursing_vitalsign_tbl` where  vitalsign_id = '" . $preopnursing_vitalsign_id . "'";
                            $ViewPreopNurseVitalHeaderSignRes = DB::selectone($ViewPreopNurseVitalHeaderSignQry);
                            if ($ViewPreopNurseVitalHeaderSignRes) {
                                $ViewPreopNurseVitalHeaderSignRow = $ViewPreopNurseVitalHeaderSignRes;
                                $preOpNursingVitalSignBp = $ViewPreopNurseVitalHeaderSignRow->vitalSignBp;
                                $preOpNursingVitalSignP = $ViewPreopNurseVitalHeaderSignRow->vitalSignP;
                                $preOpNursingVitalSignR = $ViewPreopNurseVitalHeaderSignRow->vitalSignR;
                                $preOpNursingVitalSignO2SAT = $ViewPreopNurseVitalHeaderSignRow->vitalSignO2SAT;
                            }
                        }
                    }
                    if ($form_status == '') {
                        $bp = $preOpNursingVitalSignBp;
                        $P = $preOpNursingVitalSignP;
                        $rr = $preOpNursingVitalSignR;
                        $sao = $preOpNursingVitalSignO2SAT;
                    }
                }

                if ($localAnesRecordDetails->bp_p_rr_time <> '00:00:00' && !empty($localAnesRecordDetails->bp_p_rr_time)) {
                    $bp_p_rr_time = $this->getTmFormat($localAnesRecordDetails->bp_p_rr_time); //date('h:i A',strtotime($bp_p_rr_time));
                } else {
                    $bp_p_rr_time = '';
                }

                $NA = $localAnesRecordDetails->NA == 1 ? 'Yes' : 'No';
                $bsValue = $localAnesRecordDetails->bsValue;
                //CODE END TO GET BP,P,RR,SAO2 FROM 'PREOP-NURSING RECORD' IF THIS CHARTNOTE IS YET TO SAVE FIRST TIME
                if (($localAnesRecordDetails->bsValue == "" || $NA == "") && $form_status == "") {
                    $getPreOpNursingDetails = $this->getRowRecord('preopnursingrecord', 'confirmation_id', $pConfirmId);
                    $bsValue = $getPreOpNursingDetails->bsValue;
                    $NA = $getPreOpNursingDetails->NA;
                }
                $anesthesia1SignOnFileStatus = '';
                $Anesthesia1Name = $loggedInUserName;
                $Anesthesia1SubType = $logInUserSubType;
                $Anesthesia1PreFix = 'Dr.';
                $signAnesthesia1DateTimeFormatNew = $this->getFullDtTmFormat(date("Y-m-d H:i:s"));
                if ($localAnesRecordDetails->signAnesthesia1Id <> 0 && $localAnesRecordDetails->signAnesthesia1Id <> "") {
                    $Anesthesia1Name = $localAnesRecordDetails->signAnesthesia1LastName . ", " . $localAnesRecordDetails->signAnesthesia1FirstName . " " . $localAnesRecordDetails->signAnesthesia1MiddleName;
                    $anesthesia1SignOnFileStatus = $localAnesRecordDetails->signAnesthesia1Status;
                    $signAnesthesia1DateTimeFormatNew = $this->getFullDtTmFormat($localAnesRecordDetails->signAnesthesia1DateTime);
                    $Anesthesia1SubType = $this->getUserSubTypeFun($localAnesRecordDetails->signAnesthesia1Id); //FROM common/commonFunctions.php
                }
                if ($Anesthesia1SubType == 'CRNA') {
                    $Anesthesia1PreFix = '';
                }
                $questions = [];
                if ($version_num > 3) {
                    $query = "Select id as pat_ques_id,question, f_type, d_type, replace(list_options,'\r\n','~') as options, answer From patient_mac_regional_questions Where confirmation_id ='" . $pConfirmId . "' Order By id Asc";
                    $questions = DB::select($query);
                    if (!$questions) {
                        $query = "Select id,question, f_type, d_type, replace(options,'\r\n','~') as options,'' as answer From predefine_mac_regional_questions Where deleted = 0 Order By sort_id Asc";
                        $questions = DB::select($query);
                    }
                }
                if ($blank1_label) {
                    $blank1_label = htmlentities($blank1_label);
                } elseif ($blank1_label == '' && $txtInterOpDrugs1 != '') {
                    $blank1_label = htmlentities($txtInterOpDrugs1);
                }
                if ($blank2_label) {
                    $blank2_label = htmlentities($blank2_label);
                } elseif ($blank2_label == '' && $txtInterOpDrugs2 != '') {
                    $blank2_label = htmlentities($txtInterOpDrugs2);
                }
                if ($blank3_label) {
                    $blank3_label = htmlentities($blank3_label);
                } elseif ($blank3_label == '' && $txtInterOpDrugs3 != '') {
                    $blank3_label = htmlentities($txtInterOpDrugs3);
                }
                if ($blank4_label) {
                    $blank4_label = htmlentities($blank4_label);
                } elseif ($blank4_label == '' && $txtInterOpDrugs4 != '') {
                    $blank4_label = htmlentities($txtInterOpDrugs4);
                }
                $ekgBigRowArr = array('NSR', 'AFIB', 'PACING', 'PVC�s', 'SVT', 'APC', 'Bigeminy', 'Couplet', 'SB');
                //CODE TO CALCULATE TIME INTERVAL OF 15 MINUTES EACH
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
                $Intracameral_series = ''; // [];
                for ($i = 0.5; $i <= 10; $i+=0.5) {
                    $Intracameral_series.= $i . ',';
                }
                $Peribulbar_series = '';
                for ($i = 1; $i <= 20; $i+=0.5) {
                    $Peribulbar_series.= $i . ',';
                }
                $Peribulbar_series = rtrim($Peribulbar_series, ",");
                $honanballon_serires = ''; // [];
                for ($i = 10; $i <= 50; $i+=10) {
                    $honanballon_serires.= $i . ',';
                }
                $honanballon_serires = rtrim($honanballon_serires, ",");
                $honanBallonAnother_series = ''; // [];
                for ($i = 1; $i <= 10; $i++) {
                    $honanBallonAnother_series.= $i . ',';
                }
                $honanBallonAnother_series = rtrim($honanBallonAnother_series, ",");
                //END CODE TO CALCULATE TIME INTERVAL OF 15 MINUTES EACH
                $interval = @[$intervalTimeMin1, $intervalTimeMin2, $intervalTimeMin3, $intervalTimeMin4, $intervalTimeMin5, $intervalTimeMin6, $intervalTimeMin7, $intervalTimeMin8, $intervalTimeMin9, $intervalTimeMin10];
                $fixDateToDisplayOldApplet = '2009-06-14';
                if ($confimDOS < $fixDateToDisplayOldApplet) {
                    $interval = [$intervalTimeMin1, $intervalTimeMin2, $intervalTimeMin3, $intervalTimeMin4, $intervalTimeMin5, $intervalTimeMin6, $intervalTimeMin7, $intervalTimeMin8, $intervalTimeMin9, $intervalTimeMin10];
                }
                $anesthesia2SignOnFileStatus = "Yes";
                $Anesthesia2Name = $loggedInUserName;
                $Anesthesia2SubType = $logInUserSubType;
                $Anesthesia2PreFix = 'Dr.';
                $signAnesthesia2DateTimeFormatNew = $this->getFullDtTmFormat(date("Y-m-d H:i:s"));
                if ($localAnesRecordDetails->signAnesthesia2Id <> 0 && $localAnesRecordDetails->signAnesthesia2Id <> "") {
                    $Anesthesia2Name = $localAnesRecordDetails->signAnesthesia2LastName . ", " . $localAnesRecordDetails->signAnesthesia2FirstName . " " . $localAnesRecordDetails->signAnesthesia2MiddleName;
                    $anesthesia2SignOnFileStatus = $localAnesRecordDetails->signAnesthesia2Status;
                    $signAnesthesia2DateTimeFormatNew = $this->getFullDtTmFormat($localAnesRecordDetails->signAnesthesia2DateTime);
                    $Anesthesia2SubType = $this->getUserSubTypeFun($localAnesRecordDetails->signAnesthesia2Id); //FROM common/commonFunctions.php
                }
                if ($Anesthesia2SubType == 'CRNA') {
                    $Anesthesia2PreFix = '';
                }
                $anesthesia3SignOnFileStatus = "Yes";
                $Anesthesia3Name = $loggedInUserName;
                $Anesthesia3SubType = $logInUserSubType;
                $Anesthesia3PreFix = 'Dr.';
                $signAnesthesia3DateTimeFormatNew = $this->getFullDtTmFormat(date("Y-m-d H:i:s"));
                if ($localAnesRecordDetails->signAnesthesia3Id <> 0 && $localAnesRecordDetails->signAnesthesia3Id <> "") {
                    $Anesthesia3Name = $localAnesRecordDetails->signAnesthesia3LastName . ", " . $localAnesRecordDetails->signAnesthesia3FirstName . " " . $localAnesRecordDetails->signAnesthesia3MiddleName;
                    $anesthesia3SignOnFileStatus = $localAnesRecordDetails->signAnesthesia3Status;
                    $TDanesthesia3NameIdDisplay = "none";
                    $TDanesthesia3SignatureIdDisplay = "block";
                    $signAnesthesia3DateTimeFormatNew = $this->getFullDtTmFormat($localAnesRecordDetails->signAnesthesia3DateTime);
                    $Anesthesia3SubType = $this->getUserSubTypeFun($localAnesRecordDetails->signAnesthesia3Id); //FROM common/commonFunctions.php
                }

                if ($Anesthesia3SubType == 'CRNA') {
                    $Anesthesia3PreFix = '';
                }
                $userTypeLabel = "Anesthesia Provider";
                $userTypeQry = "Anesthesiologist";
                $userTypeChk = '';
                if ($localAnesRecordDetails->relivedPostNurseId) {
                    $relivedPostQry = "select user_type from users where usersId='" . $localAnesRecordDetails->relivedPostNurseId . "' ORDER BY lname";
                    $relivedPostRes = DB::selectone($relivedPostQry);
                    $userTypeChk = $relivedPostRes->user_type;
                }
                if ($userTypeChk == "Nurse") {
                    $userTypeLabel = "Relief Nurse";
                    $userTypeQry = "Nurse";
                }

                $dosageArr = array('blank3', 'blank4', 'blank1', 'blank2', 'propofol', 'midazolam', 'ketamine', 'labetalol', 'Fentanyl', 'spo2', 'o2lpm');
                $dosageArrs = array('blank3' => 'blank3', 'blank4' => 'blank4', 'blank1' => 'blank1', 'blank2' => 'blank2',
                    'propofol' => 'mgPropofol', 'midazolam' => 'mgMidazolam', 'ketamine' => 'mgKetamine', 'labetalol' => 'mgLabetalol',
                    'Fentanyl' => 'mcgFentanyl', 'spo2' => 'spo2', 'o2lpm' => 'o2lpm');

                $gridCounter = 7;
                $blank1 = [];
                $blank2 = [];
                $blank3 = [];
                $blank4 = [];
                // print_r($localAnesRecordDetailsMedGrid);
                foreach ($dosageArr as $dosage) {
                    for ($L = 1; $L <= 20; $L++) {
                        $varName = $dosage . '_' . $L;
                        //$labelName = $dosage. '_label';
                        if (!in_array($dosage, array('ketamine', 'labetalol', 'Fentanyl', 'spo2', 'o2lpm'))) {
                            //$labelName=>isset($localAnesRecordDetailsMedGrid->$labelName)?$localAnesRecordDetailsMedGrid->$labelName:"",
                            $$dosage[] = ['key' => $varName, 'value' => $localAnesRecordDetailsMedGrid->$varName]; //isset($localAnesRecordDetailsMedGrid->$varName)?$localAnesRecordDetailsMedGrid->$varName:"";
                        } else {
                            $$dosage[] = ['key' => $varName, 'value' => $localAnesRecordDetailsMedGridSec->$varName];
                        }
                    }
                }

                $medGridData = [
                    "samplekey" => "blank3,blank4,blank1,blank2,propofol,midazolam,mgKetamine,mgLabetalol,mcgFentanyl,spo2,o2lpm",
                    "blank3" => ['label' => $blank3_label, 'blankdata' => $blank3],
                    'blank4' => ['label' => $blank4_label, 'blankdata' => $blank4],
                    "blank1" => ['label' => $blank1_label, 'blankdata' => $blank1],
                    "blank2" => ['label' => $blank2_label, 'blankdata' => $blank2],
                    'propofol' => ['label' => htmlentities($mgPropofol_label), 'blankdata' => $propofol],
                    'midazolam' => ['label' => htmlentities($mgMidazolam_label), 'blankdata' => $midazolam],
                    'mgKetamine' => ['label' => htmlentities($mgKetamine_label), 'blankdata' => $ketamine],
                    'mgLabetalol' => ['label' => htmlentities($mgLabetalol_label), 'blankdata' => $labetalol],
                    'mcgFentanyl' => ['label' => htmlentities($mcgFentanyl_label), 'blankdata' => $Fentanyl],
                    'spo2' => ['label' => 'SaO2', 'blankdata' => $spo2],
                    'o2lpm' => ['label' => 'O2l/m', 'blankdata' => $o2lpm],
                ];
                $graphdata = [];
                if ($localAnesRecordDetails->html_grid_data <> "") {
                    $html_grid_data = @explode("~", $localAnesRecordDetails->html_grid_data);
                    foreach ($html_grid_data as $html_grid_datas) {
                        if (strstr($html_grid_datas, 'funDrawDownTirangle')) {
                            $graphdata[] = ['key' => rtrim($html_grid_datas, ","), 'type' => 'funDrawDownTirangle'];
                        }
                        if (strstr($html_grid_datas, 'funDrawUpTirangle')) {
                            $graphdata[] = ['key' => rtrim($html_grid_datas, ","), 'type' => 'funDrawUpTirangle'];
                        }
                        if (strstr($html_grid_datas, 'funDrawReblock')) {
                            $graphdata[] = ['key' => rtrim($html_grid_datas, ","), 'type' => 'funDrawReblock'];
                        }
                        if (strstr($html_grid_datas, 'funCircleWtInterColor')) {
                            $graphdata[] = ['key' => rtrim($html_grid_datas, ","), 'type' => 'funCircleWtInterColor'];
                        }
                        if (strstr($html_grid_datas, 'funCircleWtOutInterColor')) {
                            $graphdata[] = ['key' => rtrim($html_grid_datas, ","), 'type' => 'funCircleWtOutInterColor'];
                        }
                        if (strstr($html_grid_datas, 'funDrawTextCan')) {
                            $graphdata[] = ['key' => rtrim($html_grid_datas, ","), 'type' => 'funDrawTextCan'];
                        }
                    }
                }
                $arra = array("1" => "Oriented x3", "2" => "Oriented x2", "3" => "Awake", "4" => "Confused", "5" => "Disoriented", "6" => "Combative");
                $data = [
                    "Top_Section" => ["ReliefNurse_Anesthesia" =>
                        [
                            "drop" => DB::select("select usersId,concat(lname,' ',fname,mname) as name from users where (user_type IN('Nurse','Anesthesiologist') or (user_type='Anesthesiologist' And user_sub_type='CRNA')) and deleteStatus!='Yes' ORDER BY lname"),
                            "selected" => isset($reliefNurseId) ? $reliefNurseId : 0, 'selectedName' => $this->getName($reliefNurseId)
                        ],
                        "Confirmationofidentifyprocedureproceduresiteandconsent" => isset($localAnesRecordDetails->confirmIPPSC_signin) ? $localAnesRecordDetails->confirmIPPSC_signin : "",
                        "Sitemarkedbypersonperformingtheprocedure" => isset($localAnesRecordDetails->siteMarked) ? $localAnesRecordDetails->siteMarked : "",
                        "Patientallergies_Check" => isset($localAnesRecordDetails->patientAllergies) ? $localAnesRecordDetails->patientAllergies : "",
                        "Difficultairwayoraspirationrisk" => isset($localAnesRecordDetails->difficultAirway) ? $localAnesRecordDetails->difficultAirway : "",
                        "Riskofbloodloss" => ["text" => isset($localAnesRecordDetails->bloodLossUnits) ? $localAnesRecordDetails->bloodLossUnits : "", "checkbox" => isset($localAnesRecordDetails->riskBloodLoss) ? $localAnesRecordDetails->riskBloodLoss : ""],
                        "Anesthesiasafetycheckcompleted" => isset($localAnesRecordDetails->anesthesiaSafety) ? $localAnesRecordDetails->anesthesiaSafety : "",
                        "Allmembersoftheteamhavediscussedcareplanandaddressedconcerns" => isset($localAnesRecordDetails->allMembersTeam) ? $localAnesRecordDetails->allMembersTeam : "",
                        "AnesthesiaProvider1" => ["name" => $Anesthesia4PreFix . $Anesthesia4Name, "signed_status" => $anesthesia4SignOnFileStatus, "sign_date" => $signAnesthesia4DateTimeFormatNew],
                    ],
                    "Pre_Operative" => [
                        "PatientInterviewed" => isset($localAnesRecordDetails->patientInterviewed) ? $localAnesRecordDetails->patientInterviewed : "",
                        "NochangeinHP" => isset($localAnesRecordDetails->chartNotesReviewed) ? $localAnesRecordDetails->chartNotesReviewed : "",
                        "Ptreassessedstableforanesthesiasurgery" => ['checked' => isset($localAnesRecordDetails->fpExamPerformed) ? $localAnesRecordDetails->fpExamPerformed : "", 'visible' => $fpExamPerformedVisibility],
                        "NPO" => ["checked" => isset($localAnesRecordDetails->npo) ? $localAnesRecordDetails->npo : "", 'visible' => $version_num > 2 ? 'visible' : 'hidden'],
                        "AlertandAwake" => ["drop" =>
                            [
                                ["key" => "1", "value" => "Oriented x3"],
                                ["key" => "2", "value" => "Oriented x2"],
                                //["key" => "3", "value" => "Awake"],
                                ["key" => "4", "value" => "Confused"],
                                ["key" => "5", "value" => "Disoriented"],
                                ["key" => "6", "value" => "Combative"]
                            ], "selected" => isset($localAnesRecordDetails->alertOriented) ? $localAnesRecordDetails->alertOriented : "", 'selectedData' => @$arra[$localAnesRecordDetails->alertOriented]],
                        "AssistedbyTranslator" => $patientConfirm_assist_by_translator,
                        "ProcedureVerified" => ['checked' => isset($localAnesRecordDetails->procedurePrimaryVerified) ? $localAnesRecordDetails->procedurePrimaryVerified : "", 'display_text' => wordwrap($patient_primary_procedure, 35, "<br>", 1)],
                        "SecondaryVerifiedAnesthesVitreoretinal" => isset($localAnesRecordDetails->procedureSecondaryVerified) ? $localAnesRecordDetails->procedureSecondaryVerified : "",
                        "SiteVerifiedLeftEye" => isset($localAnesRecordDetails->siteVerified) ? $localAnesRecordDetails->siteVerified : "", "MallampettiScore" => [
                            "drop" => [["key" => "Class 1"], ["key" => "Class 2"], ["key" => "Class 3"], ["key" => "Class 4"]],
                            "selected" => isset($localAnesRecordDetails->mallampetti_score) ? $localAnesRecordDetails->mallampetti_score : "", 'visible' => (isset($localAnesRecordDetails->version_num) && $localAnesRecordDetails->version_num > 2 && (isset($settings->anes_mallampetti_score) && $settings->anes_mallampetti_score || isset($localAnesRecordDetails->mallampetti_score) && trim($localAnesRecordDetails->mallampetti_score))) ? 'visible' : 'hidden'
                        ],
                    ],
                    "Allergy_data" => ['dropdown' => $allergic, 'patientAllergiesGrid' => $patientAllergies],
                    "Medications_data" => ['dropdown' => $medication, 'patient_prescriptions' => $patient_prescriptions],
                    "Evaluation_Section" => [
                        "Time" => $bp_p_rr_time, "BP" => isset($bp) ? $bp : "", "P" => isset($P) ? $P : "", "RR" => isset($rr) ? $rr : "", "SaO2" => isset($sao) ? $sao : "",
                        "Evaluation" => ["drop" => DB::select("select * from evaluation where `name` not in('HTN', 'DM', 'Dyslipidemia', 'Arthritis', 'CAD', 'S/P CAGB', 'S/P PTCA' ) order by `name`"), "textdetails" => isset($localAnesRecordDetails->dentation) ? str_ireplace('&', '&amp;', stripslashes($localAnesRecordDetails->dentation)) : ""],
                        "Dentition" => ["drop" => DB::select("select * from dentation order by `name`"), "textdetails" => str_ireplace('&', '&amp;', stripslashes($localAnesRecordDetails->dentation)), 'visible' => $version_num > 2 ? 'visible' : 'hidden'],
                        "StablecardiovascularandPulmonaryfunction" => isset($localAnesRecordDetails->stableCardiPlumFunction) ? $localAnesRecordDetails->stableCardiPlumFunction : "", "Blood_NA" => $NA, "Value" => $NA == 1 ? '' : $bsValue,
                        "PlanregionalanesthesiawithsedationRisksbenefitsandalternativesofanesthesiaplanhavebeendiscussed" => isset($localAnesRecordDetails->planAnesthesia) ? $localAnesRecordDetails->planAnesthesia : "",
                        "AllQuestionsAnswered" => ["visible" => $version_num > 3 ? 'visible' : 'hidden', 'checked' => $localAnesRecordDetails->allQuesAnswered, "questiondata" => $questions],
                        "ASAPhysicalStatus" => isset($localAnesRecordDetails->asaPhysicalStatus) ? $localAnesRecordDetails->asaPhysicalStatus : "",
                        "AnesthesiaProvider2" => ["name" => $Anesthesia1Name, "signed_status" => $anesthesia1SignOnFileStatus, "sign_date" => $signAnesthesia1DateTimeFormatNew],
                    ],
                    "HoldingareathroughIntra_Op" => [
                        "Graph" => $graphdata, // explode("~", $localAnesRecordDetails->html_grid_data),
                        "applet_data_disp" => ($graphdata == '') ? 2 : 1,
                        'applet_data' => $localAnesRecordDetails->applet_data,
                        "Start_Stop_Time" => [
                            ["title" => "ORStartTime", "key" => $orStartTime, 'visible' => getenv('ANES_OR_START_STOP_TIME') == 'YES' ? 'display' : 'hidden'],
                            ["title" => "ORStopTime", "key" => $orStopTime, 'visible' => getenv('ANES_OR_START_STOP_TIME') == 'YES' ? 'display' : 'hidden'],
                            ["title" => "AnesStartTime", "key" => $startTime, 'visible' => 'display'],
                            ["title" => "AnesStopTime", "key" => $stopTime, 'visible' => 'display'],
                            ["title" => "StartTime1", "key" => $newStartTime2, 'visible' => 'display'],
                            ["title" => "StopTime1", "key" => $newStopTime2, 'visible' => 'display'],
                            ["title" => "StartTime2", "key" => $newStartTime3, 'visible' => 'display'],
                            ["title" => "StopTime2", "key" => $newStopTime3, 'visible' => 'display'],
                        ],
                        "EKG" => ['drop' => $ekgBigRowArr, 'text' => isset($localAnesRecordDetails->ekgBigRowValue) ? stripslashes($localAnesRecordDetails->ekgBigRowValue) : ""],
                        "Time_Interval" => $interval,
                        "RoutineMonitorsApplied" => $routineMonitorApplied,
                        "IVCatheter" => [
                            "NoIV" => isset($localAnesRecordDetails->ivCatheter) ? $localAnesRecordDetails->ivCatheter : '',
                            "Hand" => ["Right" => isset($localAnesRecordDetails->hand_right) ? $localAnesRecordDetails->hand_right : "", "Left" => isset($localAnesRecordDetails->hand_left) ? $localAnesRecordDetails->hand_left : "", 'disabled' => (isset($localAnesRecordDetails->ivCatheter) && $localAnesRecordDetails->ivCatheter == "Yes") ? "disabled" : ""],
                            "Wrist" => ["Right" => isset($localAnesRecordDetails->wrist_right) ? $localAnesRecordDetails->wrist_right : "", "Left" => isset($localAnesRecordDetails->wrist_left) ? $localAnesRecordDetails->wrist_left : "", 'disabled' => (isset($localAnesRecordDetails->ivCatheter) && $localAnesRecordDetails->ivCatheter == "Yes") ? "disabled" : ""],
                            "Arm" => ["Right" => isset($localAnesRecordDetails->arm_right) ? $localAnesRecordDetails->arm_right : "", "Left" => isset($localAnesRecordDetails->arm_left) ? $localAnesRecordDetails->arm_left : "", 'disabled' => (isset($localAnesRecordDetails->ivCatheter) && $localAnesRecordDetails->ivCatheter == "Yes") ? "disabled" : ""],
                            "Antecubital" => ["Right" => isset($localAnesRecordDetails->anti_right) ? $localAnesRecordDetails->anti_right : "", "Left" => isset($localAnesRecordDetails->anti_left) ? $localAnesRecordDetails->anti_left : "", 'disabled' => (isset($localAnesRecordDetails->ivCatheter) && $localAnesRecordDetails->ivCatheter == "Yes") ? "disabled" : ""],
                            "Other" => ["checked" => isset($localAnesRecordDetails->ivCatheterOther) ? $localAnesRecordDetails->ivCatheterOther : "", "details" => isset($localAnesRecordDetails->ivCatheterOther) ? stripslashes($localAnesRecordDetails->ivCatheterOther) : "", 'disabled' => (isset($localAnesRecordDetails->ivCatheter) && $localAnesRecordDetails->ivCatheter == "Yes") ? "disabled" : ""],
                        ],
                        "MedGrid" => $medGridData, // DB::selectone("select * from localanesthesiarecordmedgrid where confirmation_id=$pConfirmId"),
                        "Local_Anesthesia" => [
                            "Topical_Block_Block2_N_A" => isset($localAnesRecordDetails->TopicalBlock1Block2) ? $localAnesRecordDetails->TopicalBlock1Block2 : "",
                            "Aspiration" => isset($localAnesRecordDetails->Block1Block2Aspiration) ? $localAnesRecordDetails->Block1Block2Aspiration : "", "FullEOM" => isset($localAnesRecordDetails->Block1Block2Full) ? $localAnesRecordDetails->Block1Block2Full : "",
                            "BeforeInjection" => isset($localAnesRecordDetails->Block1Block2BeforeInjection) ? $localAnesRecordDetails->Block1Block2BeforeInjection : "", "Comment" => isset($localAnesRecordDetails->Block1Block2Comment) ? stripslashes($localAnesRecordDetails->Block1Block2Comment) : "",
                            "ReBlock" => isset($localAnesRecordDetails->Reblock) ? $localAnesRecordDetails->Reblock : "",
                            "lidocaine4" => isset($localAnesRecordDetails->topical4PercentLidocaine) ? $localAnesRecordDetails->topical4PercentLidocaine : "",
                            "lidocaineMPF1" => [
                                'checked' => isset($localAnesRecordDetails->Intracameral1percentLidocaine) ? $localAnesRecordDetails->Intracameral1percentLidocaine : "",
                                "drop" => rtrim($Intracameral_series, ","), "selected" => isset($localAnesRecordDetails->Intracameral) ? $localAnesRecordDetails->Intracameral : ""
                            ],
                            "lidocaine2" => ['checked' => isset($localAnesRecordDetails->Peribulbar2percentLidocaine) ? $localAnesRecordDetails->Peribulbar2percentLidocaine : "",
                                "Peribulbar" => ["drop" => rtrim($Peribulbar_series, ","), "selected" => isset($localAnesRecordDetails->Peribulbar) ? $localAnesRecordDetails->Peribulbar : ""]
                            ],
                            "lidocaine3" => ["checked" => isset($localAnesRecordDetails->Retrobulbar4percentLidocaine) ? $localAnesRecordDetails->Retrobulbar4percentLidocaine : "", "Retrobulbar" => ["drop" => $Peribulbar_series, "selected" => isset($localAnesRecordDetails->Retrobulbar) ? $localAnesRecordDetails->Retrobulbar : ""]],
                            "lidocaine4" => isset($localAnesRecordDetails->Hyalauronidase4percentLidocaine) ? $localAnesRecordDetails->Hyalauronidase4percentLidocaine : "",
                            "Bupivacaine05" => ['checked' => isset($localAnesRecordDetails->VanLindrHalfPercentLidocaine) ? $localAnesRecordDetails->VanLindrHalfPercentLidocaine : "", "VanLindt" => ["drop" => $Peribulbar_series, "selected" => isset($localAnesRecordDetails->VanLindr) ? $localAnesRecordDetails->VanLindr : ""]],
                            "Bupivacaine075" => isset($localAnesRecordDetails->bupivacaine75) ? $localAnesRecordDetails->bupivacaine75 : "",
                            "Marcaine" => isset($localAnesRecordDetails->marcaine75) ? $localAnesRecordDetails->marcaine75 : "",
                            "Epi5ugml" => ['checked' => isset($localAnesRecordDetails->lidEpi5ug) ? $localAnesRecordDetails->lidEpi5ug : "", 'text' => isset($localAnesRecordDetails->lidTxt) ? $localAnesRecordDetails->lidTxt : "", 'drop' => $Peribulbar_series, 'selected' => isset($localAnesRecordDetails->lid) ? $localAnesRecordDetails->lid : ""],
                            "Wydase15uml" => ['checked' => isset($localAnesRecordDetails->otherRegionalAnesthesiaWydase15u) ? $localAnesRecordDetails->otherRegionalAnesthesiaWydase15u : "", 'text' => isset($localAnesRecordDetails->otherRegionalAnesthesiaTxt1) ? stripslashes($localAnesRecordDetails->otherRegionalAnesthesiaTxt1) : "", 'drop' => $Peribulbar_series, 'selected' => isset($localAnesRecordDetails->otherRegionalAnesthesiaDrop) ? $localAnesRecordDetails->otherRegionalAnesthesiaDrop : ""],
                            "Other" => isset($localAnesRecordDetails->otherRegionalAnesthesiaTxt2) ? stripslashes($localAnesRecordDetails->otherRegionalAnesthesiaTxt2) : "",
                        ],
                        "OcularPressure" => [
                            "N_A" => isset($localAnesRecordDetails->ocular_pressure_na) ? $localAnesRecordDetails->ocular_pressure_na : "",
                            "None" => isset($localAnesRecordDetails->none) ? $localAnesRecordDetails->none : "",
                            "Digital" => isset($localAnesRecordDetails->digital) ? $localAnesRecordDetails->digital : "",
                            "HonanBalloon" => ["drop1" => $honanballon_serires, "selected1" => isset($localAnesRecordDetails->honanballon) ? $localAnesRecordDetails->honanballon : "", 'drop2' => $honanBallonAnother_series, 'selected2' => isset($localAnesRecordDetails->honanBallonAnother) ? $localAnesRecordDetails->honanBallonAnother : ""],
                            "Comment" => isset($localAnesRecordDetails->ansComment) ? stripslashes($localAnesRecordDetails->ansComment) : "",
                            "AnesthesiaProvider3" => ["name" => $Anesthesia2PreFix . $Anesthesia2Name, "signed_status" => $anesthesia2SignOnFileStatus, "sign_date" => $signAnesthesia2DateTimeFormatNew, 'drop' => DB::select("select usersId,concat(lname,' ',fname,mname) as name from users where user_type='Anesthesiologist' and deleteStatus!='Yes' ORDER BY lname"), 'selected' => isset($localAnesRecordDetails->relivedIntraNurseId) ? $localAnesRecordDetails->relivedIntraNurseId : 0, 'selectedName' => $this->getName($localAnesRecordDetails->relivedIntraNurseId)],
                        ],
                    ],
                    "VitalSigns" => $gridDataFilled,
                    "PostOperative" => [
                        "Noknownanestheticcomplication" => isset($localAnesRecordDetails->anyKnowAnestheticComplication) ? $localAnesRecordDetails->anyKnowAnestheticComplication : "",
                        "Evaluation" => ["drop" => DB::select("SELECT name FROM `postopevaluation` WHERE 1=1"), "text" => isset($localAnesRecordDetails->evaluation) ? stripslashes($localAnesRecordDetails->evaluation) : ""],
                        "Stablecardiovascularandpulmonaryfunction" => isset($localAnesRecordDetails->stableCardiPlumFunction2) ? $localAnesRecordDetails->stableCardiPlumFunction2 : "",
                        "Satisfactoryconditionfordischarge" => isset($localAnesRecordDetails->satisfactoryCondition4Discharge) ? $localAnesRecordDetails->satisfactoryCondition4Discharge : "",
                        "Remarks" => isset($localAnesRecordDetails->remarks) ? stripslashes($localAnesRecordDetails->remarks) : "",
                    ],
                    "AnesthesiaProvider4" => ["name" => $Anesthesia3PreFix . $Anesthesia3Name, "signed_status" => $anesthesia3SignOnFileStatus, "sign_date" => $signAnesthesia3DateTimeFormatNew, 'drop' => DB::select("select usersId,concat(lname,' ',fname,mname) as name from users where user_type='" . $userTypeQry . "' ORDER BY lname"), 'selected' => isset($localAnesRecordDetails->relivedPostNurseId) ? $localAnesRecordDetails->relivedPostNurseId : 0, 'selectedName' => $this->getName($localAnesRecordDetails->relivedPostNurseId)],
                    "arrDrawIcon_main" => [getenv('APP_URL') . '/' . getenv('APP_ROOT') . '/sc-grid/images/bgTest.jpg', getenv('APP_URL') . '/' . getenv('APP_ROOT') . '/sc-grid/images/TDn.png', getenv('APP_URL') . '/' . getenv('APP_ROOT') . '/sc-grid/images/TUp.png', getenv('APP_URL') . '/' . getenv('APP_ROOT') . '/sc-grid/images/CFill.bak.png', getenv('APP_URL') . '/' . getenv('APP_ROOT') . '/sc-grid/images/CDr.png', getenv('APP_URL') . '/' . getenv('APP_ROOT') . '/sc-grid/images/eraser.gif', getenv('APP_URL') . '/' . getenv('APP_ROOT') . '/sc-grid/images/red_dot.png', getenv('APP_URL') . '/' . getenv('APP_ROOT') . '/sc-grid/images/undo-icon.png', getenv('APP_URL') . '/' . getenv('APP_ROOT') . '/sc-grid/images/Redo-icon.png']
                ];
                $status = 1;
                $message = " General Anesthesia Record ";
            }
        }

        return response()->json(['status' => $status, 'message' => $message, 'med_grid_id' => $localAnesRecordDetailsMedGrid->med_grid_id, 'med_grid_sec_id' => $localAnesRecordDetailsMedGridSec->med_grid_sec_id, 'requiredStatus' => '', 'localAnesthesiaRecordId' => $localAnesRecordDetails->localAnesthesiaRecordId, 'data' => $data,
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

    public function LocalAnesRecordController_save(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $loginUserType = $request->json()->get('UserType') ? $request->json()->get('UserType') : $request->input('UserType');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $localAnesthesiaRecordId = $request->json()->get('localAnesthesiaRecordId') ? $request->json()->get('localAnesthesiaRecordId') : $request->input('localAnesthesiaRecordId');
        $med_grid_sec_id = $request->json()->get('med_grid_sec_id') ? $request->json()->get('med_grid_sec_id') : $request->input('med_grid_sec_id');
        $med_grid_id = $request->json()->get('med_grid_id') ? $request->json()->get('med_grid_id') : $request->input('med_grid_id');
        $jsondata = $request->json()->get('jsondata') ? $request->json()->get('jsondata') : $request->input('jsondata');
        $drawing = $request->json()->get('drawing') ? $request->json()->get('drawing') : $request->input('drawing');
        $json = json_decode($jsondata);
        //print '<pre>';
        //print_r($json->Pre_Operative->ProcedureVerified);die;
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
                $ViewUserNameQry = "select fname,mname,lname,user_type,user_sub_type from `users` where  usersId = '" . $userId . "'";
                $ViewUserNameRow = DB::selectone($ViewUserNameQry); // or die($ViewUserNameQry . imw_error());
                $loggedInUserFirstName = $ViewUserNameRow->fname;
                $loggedInUserMiddleName = $ViewUserNameRow->mname;
                $loggedInUserLastName = $ViewUserNameRow->lname;
                $user_type = $ViewUserNameRow->user_type;
                $logInUserSubType = $ViewUserNameRow->user_sub_type;

                $signOnFileStatus = 'Yes';
                $loggedInUserName = $ViewUserNameRow->lname . ", " . $ViewUserNameRow->fname . " " . $ViewUserNameRow->mname;
                
                $confirmationDetails = $this->getRowRecord('patientconfirmation', 'patientConfirmationId', $pConfirmId);
                $patient_primary_procedure_id = $primary_procedure_id = $confirmationDetails->patient_primary_procedure_id;
                $primary_procedure_name = $confirmationDetails->patient_primary_procedure;
                $secondary_procedure_id = $confirmationDetails->patient_secondary_procedure_id;

                $surgeonId = $confirmationDetails->surgeonId;
                $ascId = $confirmationDetails->ascId;
                $finalizeStatus = $confirmationDetails->finalize_status;
                $allergiesNKDA_patientconfirmation_status = $confirmationDetails->allergiesNKDA_status;
                $noMedicationStatus = $confirmationDetails->no_medication_status;
                $noMedicationComments = $confirmationDetails->no_medication_comments;
                $ascId = $confirmationDetails->ascId;
                $tablename = "localanesthesiarecord";
                unset($arrayRecord);
                $arrayRecord['patientInterviewed'] = isset($json->Pre_Operative->PatientInterviewed) ? addslashes($json->Pre_Operative->PatientInterviewed) : "";
                $arrayRecord['chartNotesReviewed'] = isset($json->Pre_Operative->NochangeinHP) ? addslashes($json->Pre_Operative->NochangeinHP) : "";
                $arrayRecord['fpExamPerformed'] = isset($json->Pre_Operative->Ptreassessedstableforanesthesiasurgery->checked) ? addslashes($json->Pre_Operative->Ptreassessedstableforanesthesiasurgery->checked) : "";
                $arrayRecord['npo'] = isset($json->Pre_Operative->NPO->checked) ? addslashes($json->Pre_Operative->NPO->checked) : "";
                $arrayRecord['ansComment'] = isset($json->OcularPressure->Comment) ? addslashes($json->OcularPressure->Comment) : "";
                $alertOriented = isset($json->Pre_Operative->AlertandAwake->selected) ? addslashes($json->Pre_Operative->AlertandAwake->selected) : ""; //$_REQUEST['chbx_alert'];
                if (is_array($alertOriented)) {
                    $alertOriented = @implode(",", $alertOriented);
                }
                $arrayRecord['alertOriented'] = addslashes($alertOriented);

                $arrayRecord['assistedByTranslator'] = isset($json->Pre_Operative->AssistedbyTranslator) ? addslashes($json->Pre_Operative->AssistedbyTranslator) : ""; // addslashes($_REQUEST['chbx_assist']);
                //CODE TO ENABLE/DISABLE TIME INTERVAL

                $hiddActiveTimeInterval = "";

                //END CODE TO ENABLE/DISABLE TIME INTERVAL

                $arrayRecord['procedurePrimaryVerified'] = isset($json->Pre_Operative->ProcedureVerified->checked) ? addslashes($json->Pre_Operative->ProcedureVerified->checked) : ""; // addslashes($_REQUEST['chbx_proced']);
                $arrayRecord['procedureSecondaryVerified'] = isset($json->Pre_Operative->SecondaryVerifiedAnesthesVitreoretinal) ? addslashes($json->Pre_Operative->SecondaryVerifiedAnesthesVitreoretinal) : ""; //  addslashes($_REQUEST['chbx_sec_veri']);
                $arrayRecord['siteVerified'] = isset($json->Pre_Operative->SiteVerifiedLeftEye) ? addslashes($json->Pre_Operative->SiteVerifiedLeftEye) : ""; // addslashes($_REQUEST['chbx_site']);
                $arrayRecord['bp'] = htmlentities(addslashes($json->Evaluation_Section->BP)); //$_REQUEST['bp1']
                $arrayRecord['P'] = htmlentities(addslashes(($json->Evaluation_Section->P)));
                $arrayRecord['rr'] = htmlentities(addslashes($json->Evaluation_Section->RR));
                $arrayRecord['sao'] = htmlentities(addslashes($json->Evaluation_Section->SaO2));
                $arrayRecord['bp_p_rr_time'] = '';
                $txt_bp_p_rr_time = "";
                if ($json->Evaluation_Section->Time) {
                    $txt_bp_p_rr_time = $this->setTmFormat($json->Evaluation_Section->Time);
                    $arrayRecord['bp_p_rr_time'] = $txt_bp_p_rr_time;
                }

                $arrayRecord['evaluation2'] = addslashes($json->Evaluation_Section->Evaluation->textdetails);
                $arrayRecord['stableCardiPlumFunction'] = addslashes($json->Evaluation_Section->StablecardiovascularandPulmonaryfunction);
                $arrayRecord['planAnesthesia'] = addslashes($json->Evaluation_Section->PlanregionalanesthesiawithsedationRisksbenefitsandalternativesofanesthesiaplanhavebeendiscussed);
                $arrayRecord['allQuesAnswered'] = addslashes($json->Evaluation_Section->AllQuestionsAnswered->checked);
                $arrayRecord['asaPhysicalStatus'] = addslashes($json->Evaluation_Section->ASAPhysicalStatus);

                $arrayRecord['remarks'] = addslashes($json->PostOperative->Remarks);
                $arrayRecord['evaluation'] = addslashes($json->PostOperative->Evaluation->text);

                //$arrayRecord['chbx_enable_postop_desc'] = addslashes($_REQUEST['chbx_enable_postop_desc']);
                $arrayRecord['chbx_vss'] = ''; // addslashes($_REQUEST['chbx_vss']);
                $arrayRecord['chbx_atsf'] = ''; // addslashes($_REQUEST['chbx_atsf']);
                $arrayRecord['chbx_pa'] = addslashes($json->Top_Section->Patientallergies_Check);
                $arrayRecord['chbx_nausea'] = ''; // addslashes($_REQUEST['chbx_nausea']);
                $arrayRecord['chbx_vomiting'] = ''; // addslashes($_REQUEST['chbx_vomiting']);
                $arrayRecord['chbx_dizziness'] = ''; // addslashes($_REQUEST['chbx_dizziness']);
                $arrayRecord['chbx_rd'] = ''; // addslashes($_REQUEST['chbx_rd']);
                $arrayRecord['chbx_aao'] = ''; // addslashes($_REQUEST['chbx_aao']);
                $arrayRecord['chbx_ddai'] = ''; // addslashes($_REQUEST['chbx_ddai']);
                $arrayRecord['chbx_pv'] = ''; // addslashes($_REQUEST['chbx_pv']);
                $arrayRecord['chbx_rtpog'] = ''; // addslashes($_REQUEST['chbx_rtpog']);
                $arrayRecord['chbx_pain'] = ''; // addslashes($_REQUEST['chbx_pain']);
                $arrayRecord['relivedPreNurseId'] = ''; // addslashes($_REQUEST['relivedPreNurseIdList']);
                $relivedIntraNurseIdList = $json->HoldingareathroughIntra_Op->OcularPressure->AnesthesiaProvider3->selected;
                $arrayRecord['relivedIntraNurseId'] = addslashes($relivedIntraNurseIdList); // addslashes($_REQUEST['relivedIntraNurseIdList']);
                $arrayRecord['relivedPostNurseId'] = addslashes($json->AnesthesiaProvider4->selected); //$_REQUEST['relivedPostNurseIdList']
                $arrayRecord['routineMonitorApplied'] = addslashes($json->HoldingareathroughIntra_Op->RoutineMonitorsApplied); //$_REQUEST['chbx_routine']
                $arrayRecord['hide_anesthesia_grid'] = ''; // addslashes($_REQUEST['hide_anesthesia_grid']);
                $ekgBigRowValue = $json->HoldingareathroughIntra_Op->EKG->text; // $_REQUEST['ekgBigRowValue'];
                $arrayRecord['ekgBigRowValue'] = addslashes($ekgBigRowValue);
                $arrayRecord['orStartTime'] = addslashes($json->HoldingareathroughIntra_Op->Start_Stop_Time[0]->key);
                $arrayRecord['orStopTime'] = addslashes($json->HoldingareathroughIntra_Op->Start_Stop_Time[1]->key);
                $arrayRecord['startTime'] = addslashes($json->HoldingareathroughIntra_Op->Start_Stop_Time[2]->key);
                $arrayRecord['stopTime'] = addslashes($json->HoldingareathroughIntra_Op->Start_Stop_Time[3]->key);
                $arrayRecord['newStartTime1'] = addslashes($json->HoldingareathroughIntra_Op->Start_Stop_Time[4]->key);
                $arrayRecord['newStopTime1'] = addslashes($json->HoldingareathroughIntra_Op->Start_Stop_Time[5]->key);
                $arrayRecord['newStartTime2'] = addslashes($json->HoldingareathroughIntra_Op->Start_Stop_Time[6]->key);
                $arrayRecord['newStopTime2'] = addslashes($json->HoldingareathroughIntra_Op->Start_Stop_Time[7]->key);
                $arrayRecord['newStartTime3'] =addslashes($json->HoldingareathroughIntra_Op->Start_Stop_Time[6]->key);
                $arrayRecord['newStopTime3'] = addslashes($json->HoldingareathroughIntra_Op->Start_Stop_Time[7]->key);
                $arrayRecord['activeTimeInterval'] = 'Yes';
                //$json->Evaluation_Section->
                $arrayRecord['bsValue'] = htmlentities(addslashes($json->Evaluation_Section->Value)); //$_REQUEST['bsValue']
                $arrayRecord['NA'] = addslashes($json->Evaluation_Section->Blood_NA); //$_REQUEST['chkBoxNS']

                $arrayRecord['local_anes_revaluation2'] = addslashes($json->PostOperative->Evaluation->text); //$_REQUEST['local_anes_revaluation2']
                $arrayRecord['ivCatheter'] = addslashes($json->HoldingareathroughIntra_Op->IVCatheter->NoIV); //$_REQUEST['chbx_no']
                $arrayRecord['hand_right'] = addslashes($json->HoldingareathroughIntra_Op->IVCatheter->Hand->Right); //$_REQUEST['chbx_hand_right']
                $arrayRecord['hand_left'] = addslashes($json->HoldingareathroughIntra_Op->IVCatheter->Hand->Left); //$_REQUEST['chbx_hand_left']
                $arrayRecord['wrist_right'] = addslashes($json->HoldingareathroughIntra_Op->IVCatheter->Wrist->Right); //$_REQUEST['chbx_wrist_right']
                $arrayRecord['wrist_left'] = addslashes($json->HoldingareathroughIntra_Op->IVCatheter->Wrist->Left); //$_REQUEST['chbx_wrist_left']
                $arrayRecord['arm_right'] = addslashes($json->HoldingareathroughIntra_Op->IVCatheter->Arm->Right); //$_REQUEST['chbx_arm_right']
                $arrayRecord['arm_left'] = addslashes($json->HoldingareathroughIntra_Op->IVCatheter->Arm->Left); //$_REQUEST['chbx_arm_left']
                $arrayRecord['anti_right'] = addslashes($json->HoldingareathroughIntra_Op->IVCatheter->Antecubital->Right); //$_REQUEST['chbx_anti_right']
                $arrayRecord['anti_left'] = addslashes($json->HoldingareathroughIntra_Op->IVCatheter->Antecubital->Left); //$_REQUEST['chbx_anti_left']
                $arrayRecord['topi_peri_retro'] = ''; // addslashes($_REQUEST['chbx_topi_peri_retro']);//$_REQUEST['chbx_topi_peri_retro']
                //print_r($json->HoldingareathroughIntra_Op->Local_Anesthesia);
                $arrayRecord['topical'] = addslashes($json->HoldingareathroughIntra_Op->Local_Anesthesia->Topical_Block_Block2_N_A); //$_REQUEST['topical']

                $arrayRecord['ivCatheterOther'] = addslashes(trim($json->HoldingareathroughIntra_Op->IVCatheter->Other->details)); //$_REQUEST['other_reg_anes']
                $arrayRecord['lidocaine2'] = ''; // addslashes($_REQUEST['chbx_lido2']);//$_REQUEST['chbx_lido2']
                $arrayRecord['lidocaine3'] = ''; // addslashes($_REQUEST['chbx_lido3']);
                $arrayRecord['Peribulbar'] = addslashes($json->HoldingareathroughIntra_Op->Local_Anesthesia->lidocaine2->Peribulbar->selected); //$_REQUEST['Peribulbar']
                $arrayRecord['lidocaine4'] = ''; //addslashes($_REQUEST['chbx_lido4']);//$_REQUEST['chbx_lido4']
                $arrayRecord['Bupiyicaine5'] = ''; // addslashes($_REQUEST['chbx_bupi']);//$_REQUEST['chbx_bupi']
                $arrayRecord['Retrobulbar'] = addslashes($json->HoldingareathroughIntra_Op->Local_Anesthesia->lidocaine3->Retrobulbar->selected); //$_REQUEST['Retrobulbar']  
                $arrayRecord['ugcc'] = ''; // addslashes($_REQUEST['chbx_epi']);
                $arrayRecord['Hyalauronidase'] = ''; // addslashes($_REQUEST['Hyalauronidase']);
                $arrayRecord['vanlindt'] = addslashes($json->HoldingareathroughIntra_Op->Local_Anesthesia->Bupivacaine05->VanLindt->selected); //$_REQUEST['vanLindt']
                $arrayRecord['regionalAnesthesiaOther'] = ''; // addslashes($_REQUEST['otherRegionalAnesthesia']);//$_REQUEST['otherRegionalAnesthesia']
                $arrayRecord['topical4PercentLidocaine'] = addslashes($json->HoldingareathroughIntra_Op->Local_Anesthesia->lidocaine4); //$_REQUEST['chbx_topical4PercentLidocaine']
                $arrayRecord['Intracameral'] = addslashes($json->HoldingareathroughIntra_Op->Local_Anesthesia->lidocaineMPF1->selected); //$_REQUEST['Intracameral']
                $arrayRecord['Intracameral1percentLidocaine'] = addslashes($json->HoldingareathroughIntra_Op->Local_Anesthesia->lidocaineMPF1->checked); //$_REQUEST['chbx_Intracameral1percentLidocaine']
                $arrayRecord['Peribulbar2percentLidocaine'] = addslashes($json->HoldingareathroughIntra_Op->Local_Anesthesia->lidocaine2->checked); //$_REQUEST['chbx_Peribulbar2percentLidocaine']
                $arrayRecord['Retrobulbar4percentLidocaine'] = addslashes($json->HoldingareathroughIntra_Op->Local_Anesthesia->lidocaine3->Retrobulbar->selected); //$_REQUEST['chbx_Retrobulbar4percentLidocaine']
                $arrayRecord['Hyalauronidase4percentLidocaine'] = addslashes($json->HoldingareathroughIntra_Op->Local_Anesthesia->lidocaine4); //$_REQUEST['chbx_Hyalauronidase4percentLidocaine']
                $arrayRecord['bupivacaine75'] = addslashes($json->HoldingareathroughIntra_Op->Local_Anesthesia->Bupivacaine075); //$_REQUEST['bupivacaine75']
                $arrayRecord['marcaine75'] = addslashes($json->HoldingareathroughIntra_Op->Local_Anesthesia->Marcaine075); //$_REQUEST['marcaine75']
                $arrayRecord['VanLindr'] = addslashes($json->HoldingareathroughIntra_Op->Local_Anesthesia->Bupivacaine05->VanLindt->selected); //$_REQUEST['VanLindr']
                $arrayRecord['VanLindrHalfPercentLidocaine'] = addslashes($json->HoldingareathroughIntra_Op->Local_Anesthesia->Bupivacaine05->checked); //$_REQUEST['chbx_VanLindrHalfPercentLidocaine']
                $arrayRecord['lidTxt'] = addslashes($json->HoldingareathroughIntra_Op->Local_Anesthesia->Epi5ugml->text); //$_REQUEST['lidTxt']
                $arrayRecord['lid'] = addslashes($json->HoldingareathroughIntra_Op->Local_Anesthesia->Epi5ugml->selected); //$_REQUEST['lid']
                $arrayRecord['lidEpi5ug'] = addslashes($json->HoldingareathroughIntra_Op->Local_Anesthesia->Epi5ugml->checked); //$_REQUEST['chbx_lidEpi5ug']
                $arrayRecord['otherRegionalAnesthesiaTxt1'] = addslashes($json->HoldingareathroughIntra_Op->Local_Anesthesia->Wydase15uml->text); //$_REQUEST['otherRegionalAnesthesiaTxt1']
                $arrayRecord['otherRegionalAnesthesiaDrop'] = addslashes($json->HoldingareathroughIntra_Op->Local_Anesthesia->Wydase15uml->selected); //$_REQUEST['otherRegionalAnesthesiaDrop']
                $arrayRecord['otherRegionalAnesthesiaWydase15u'] = addslashes($json->HoldingareathroughIntra_Op->Local_Anesthesia->Wydase15uml->checked); //$_REQUEST['chbx_otherRegionalAnesthesiaWydase15u']
                $arrayRecord['otherRegionalAnesthesiaTxt2'] = htmlentities(addslashes($json->HoldingareathroughIntra_Op->Local_Anesthesia->Wydase15uml->Other)); //$_REQUEST['otherRegionalAnesthesiaTxt2']

                $arrayRecord['ocular_pressure_na'] = addslashes($json->HoldingareathroughIntra_Op->OcularPressure->N_A); //$_REQUEST['chbx_ocular_pressure_na']
                $arrayRecord['none'] = addslashes($json->HoldingareathroughIntra_Op->OcularPressure->None); //$_REQUEST['chbx_none']
                $arrayRecord['digital'] = addslashes($json->HoldingareathroughIntra_Op->OcularPressure->Digital); //$_REQUEST['chbx_digi']
                $arrayRecord['honanballon'] = addslashes($json->HoldingareathroughIntra_Op->OcularPressure->HonanBalloon->selected1); //$_REQUEST['honanBallon']
                $arrayRecord['honanBallonAnother'] = addslashes($json->HoldingareathroughIntra_Op->OcularPressure->HonanBalloon->selected2); //$_REQUEST['honanBallonAnother']

                $arrayRecord['anyKnowAnestheticComplication'] = addslashes($json->PostOperative->Noknownanestheticcomplication); //$_REQUEST['chbx_anes']
                $arrayRecord['stableCardiPlumFunction2'] = addslashes($json->PostOperative->Stablecardiovascularandpulmonaryfunction); //$_REQUEST['chbx_pulm']
                $arrayRecord['satisfactoryCondition4Discharge'] = addslashes($json->PostOperative->Satisfactoryconditionfordischarge); //$_REQUEST['chbx_dis']
                $arrayRecord['surgeonId'] = $surgeonId;
                $arrayRecord['ascId'] = $ascId;
                $arrayRecord['confirmation_id'] = $pConfirmId;
                $arrayRecord['patient_id'] = $patient_id;
                $arrayRecord['surgeonSign'] = ''; // addslashes($_REQUEST['elem_signature1']);
                $arrayRecord['anesthesiologistSign'] = ''; // addslashes($_REQUEST['elem_signature2']);
                $arrayRecord['TopicalBlock1Block2'] = addslashes($json->HoldingareathroughIntra_Op->Local_Anesthesia->Topical_Block_Block2_N_A); //$_REQUEST['chbx_TopicalBlock1Block2']
                $arrayRecord['Reblock'] = addslashes($json->HoldingareathroughIntra_Op->Local_Anesthesia->ReBlock); //$_REQUEST['chbx_Reblock']
                $arrayRecord['Block1Block2Aspiration'] = addslashes($json->HoldingareathroughIntra_Op->Local_Anesthesia->Aspiration); //$_REQUEST['Block1Block2Aspiration']
                $arrayRecord['Block1Block2Full'] = addslashes($json->HoldingareathroughIntra_Op->Local_Anesthesia->FullEOM); //$_REQUEST['Block1Block2Full']
                $arrayRecord['Block1Block2BeforeInjection'] = addslashes($json->HoldingareathroughIntra_Op->Local_Anesthesia->BeforeInjection); //$_REQUEST['Block1Block2BeforeInjection']
                $arrayRecord['Block1Block2RockNegative'] = ''; // addslashes($_REQUEST['Block1Block2RockNegative']);//$_REQUEST['Block1Block2RockNegative']
                $arrayRecord['Block1Block2Comment'] = addslashes($json->HoldingareathroughIntra_Op->Local_Anesthesia->Comment); //$_REQUEST['Block1Block2Comment']
                $saveByAnes = '';
                $getUserTypeQry = "select user_type from `users` where  usersId = '" . $userId . "'";
                $getUserTypeRes = DB::selectone($getUserTypeQry);
                if ($getUserTypeRes) {
                    $getUserTypeRow = $getUserTypeRes;
                    $loggedUserType = $getUserTypeRow->user_type;
                    if ($loggedUserType == 'Anesthesiologist') {
                        $saveByAnes = 'Yes';
                        $arrayRecord['saveByAnes'] = 'Yes';
                    }
                }
                // $arrayRecord['applet_data'] = $_REQUEST['applet_data'];
                /* if ($_REQUEST['applet_time_interval'] == "") {

                  } else {
                  $arrayRecord['applet_time_interval'] ='';// $_REQUEST['applet_time_interval'];
                  } */
                $strGridImagePath = "";
                //START CODE TO CHECK NURSE,SURGEON, ANESTHESIOLOGIST SIGN IN DATABASE
                $chkUserSignDetails = $this->getRowRecord('localanesthesiarecord', 'confirmation_id', $pConfirmId);
                if ($chkUserSignDetails) {
                    $chk_signAnesthesia1Id = $chkUserSignDetails->signAnesthesia1Id;
                    $chk_signAnesthesia2Id = $chkUserSignDetails->signAnesthesia2Id;
                    $chk_signAnesthesia3Id = $chkUserSignDetails->signAnesthesia3Id;
                    $chk_signAnesthesia4Id = $chkUserSignDetails->signAnesthesia4Id;

                    $chk_versionNum = $chkUserSignDetails->version_num;
                    $chk_versionDateTime = $chkUserSignDetails->version_date_time;
                    $chk_vitalSignGridStatus = $chkUserSignDetails->vitalSignGridStatus;
                    //CHECK FORM STATUS
                    $chk_form_status = $chkUserSignDetails->form_status;
                    //CHECK FORM STATUS
                    $strGridImagePath = $chkUserSignDetails->grid_image_path;

                    $chk_anes_ScanUpload = $chkUserSignDetails->anes_ScanUpload;
                    $chk_anes_ScanUploadPath = $chkUserSignDetails->anes_ScanUploadPath;
                }
                //END CODE TO CHECK NURSE,SURGEON, ANESTHESIOLOGIST SIGN IN DATABASE
                $vitalSignGridStatus = $this->loadVitalSignGridStatus($chk_form_status, $chk_vitalSignGridStatus, 'macAnes');
                if ($chk_form_status <> 'completed' && $chk_form_status <> 'not completed') {
                    $arrayRecord['vitalSignGridStatus'] = $vitalSignGridStatus;
                }

                $version_num = $chk_versionNum;
                if (!$chk_versionNum) {
                    $version_date_time = $chk_versionDateTime;
                    if ($version_date_time == '' || $version_date_time == '0000-00-00 00:00:00') {
                        $version_date_time = date('Y-m-d H:i:s');
                    }

                    if ($chk_form_status == 'completed' || $chk_form_status == 'not completed') {
                        $version_num = 1;
                    } else {
                        $version_num = 4;
                    }

                    $arrayRecord['version_num'] = $version_num;
                    $arrayRecord['version_date_time'] = $version_date_time;
                }

                if ($version_num > 1) {
                    $arrayRecord['reliefNurseId'] = $json->Top_Section->ReliefNurse_Anesthesia; // $_REQUEST['reliefNurseId'];
                    $arrayRecord['confirmIPPSC_signin'] = addslashes($json->Top_Section->Confirmationofidentifyprocedureproceduresiteandconsent); //$_REQUEST['chbx_ipp']
                    $arrayRecord['siteMarked'] = addslashes($json->Top_Section->Sitemarkedbypersonperformingtheprocedure); //$_REQUEST['chbx_smpp']
                    $arrayRecord['patientAllergies'] = addslashes($json->Top_Section->Patientallergies_Check); //$_REQUEST['chbx_pa']
                    $arrayRecord['difficultAirway'] = addslashes($json->Top_Section->Difficultairwayoraspirationrisk); //$_REQUEST['chbx_dar']
                    $arrayRecord['anesthesiaSafety'] = addslashes($json->Top_Section->Anesthesiasafetycheckcompleted); //$_REQUEST['chbx_asc']
                    $arrayRecord['allMembersTeam'] = addslashes($json->Top_Section->Allmembersoftheteamhavediscussedcareplanandaddressedconcerns); //$_REQUEST['chbx_adcpc']
                    $arrayRecord['riskBloodLoss'] = addslashes($json->Top_Section->Riskofbloodloss->checkbox); //$_REQUEST['chbx_rbl']
                    $arrayRecord['bloodLossUnits'] = ($json->Top_Section->Riskofbloodloss->checkbox == 'Yes') ? addslashes($json->Top_Section->Riskofbloodloss->text) : '';
                }
                if ($version_num > 2) {
                    $arrayRecord['mallampetti_score'] = addslashes($json->Pre_Operative->MallampettiScore->selected); //$_REQUEST['mallampetti_score']
                    $arrayRecord['dentation'] = addslashes($json->Evaluation_Section->Dentition->textdetails); //$_REQUEST['dentation']
                }

                //CODE TO SET FORM STATUS 
                //$arrayRecord['form_status'] = 'completed';
                $formStatus = 'completed';

                if ($chk_anes_ScanUploadPath || $chk_anes_ScanUpload) {//IF DOCUMENT IS SCANED OR UPLOADED THEN RECORD IS SAID TO BE COMPLETED.(No need to check below conditions)
                    $formStatus = 'completed';
                } else {
                    $formStatus = 'not completed';
                }

                //END CODE TO SET FORM STATUS
                $arrayRecord['form_status'] = $formStatus;

                $imagePath = $this->convertBase64_LocAnesRec($drawing, $localAnesthesiaRecordId, $patient_id, $pConfirmId);
                $graph = $json->HoldingareathroughIntra_Op->Graph;
                $graphdata = '';
                foreach ($graph as $graphs) {
                    $graphdata.=$graphs->key . '~';
                }
                $arrayRecord['html_grid_data'] = $graphdata; // $json->HoldingareathroughIntra_Op->Graph;//$_REQUEST["hidAnesthesiaGridData"];
                $arrayRecord['grid_image_path'] = $imagePath;
                if ($localAnesthesiaRecordId) {
                    DB::table('localanesthesiarecord')->where('localAnesthesiaRecordId', $localAnesthesiaRecordId)->update($arrayRecord);
                    //$this->updateRecords($arrayRecord, 'localanesthesiarecord', 'localAnesthesiaRecordId', $localAnesthesiaRecordId);
                } else {
                    DB::table('localanesthesiarecord')->insert($arrayRecord);
                    //$this->addRecords($arrayRecord, 'localanesthesiarecord');
                }
                $Allergies_data = $json->Allergy_data;
                $this->patient_allergy_save($Allergies_data, $pConfirmId, $patient_id, $userId, $loggedInUserName);
                //print_r($json);
                $Medication_data = $json->Medications_data;
                $this->patient_medication_save($Medication_data, $pConfirmId, $patient_id, $userId, $loggedInUserName);
                $vitalSignGridRecordIdArr = $json->VitalSigns; // $_POST['vitalSignGridRecordId'];
                $this->saveVitalSigns($vitalSignGridRecordIdArr, $pConfirmId, $patient_id, $userId, $loggedInUserName);
                // Start Saving Admin Additional Qustions
                if ($version_num > 3) { // Save Only if form version is greater than 3
                    $AllQuestionsAnswered = $json->Evaluation_Section->AllQuestionsAnswered->questiondata;
                    $this->saveAllQuestionsAnswered($AllQuestionsAnswered, $pConfirmId, $patient_id, $userId, $loggedInUserName);
                }
                // End Saving Admin Additional Qustions
                //START SAVE MED GRID OF ANES GRAPH IN SEPARATE TABLE
                $MedGrid_data = $json->HoldingareathroughIntra_Op->MedGrid;
                //MedGridSection_Save($MedGrid_data, $med_grid_sec_id, $pConfId, $patient_id, $userId, $loggedInUserName)
                $this->MedGridSection_Save($MedGrid_data, $med_grid_id, $med_grid_sec_id, $pConfirmId, $patient_id, $userId, $loggedInUserName);
                //END SAVE MED GRID OF ANES GRAPH IN SEPARATE TABLE
                //MAKE AUDIT STATUS REPORT
                unset($arrayStatusRecord);
                $arrayStatusRecord['user_id'] = $userId;
                $arrayStatusRecord['patient_id'] = $patient_id;
                $arrayStatusRecord['confirmation_id'] = $pConfirmId;
                $arrayStatusRecord['form_name'] = 'mac_regional_anesthesia_form';
                $arrayStatusRecord['action_date_time'] = date('Y-m-d H:i:s');
                //MAKE AUDIT STATUS REPORT
                //CODE START TO SET AUDIT STATUS AFTER SAVE
                unset($conditionArr);
                $conditionArr['confirmation_id'] = $pConfirmId;
                $conditionArr['form_name'] = 'mac_regional_anesthesia_form';
                $conditionArr['status'] = 'created';
                $chkAuditStatus = $this->getMultiChkArrayRecords('chartnotes_change_audit_tbl', $conditionArr);
                if ($chkAuditStatus) {
                    //MAKE AUDIT STATUS MODIFIED
                    $arrayStatusRecord['status'] = 'modified';
                } else {
                    //MAKE AUDIT STATUS CREATED
                    $arrayStatusRecord['status'] = 'created';
                }
                DB::table('chartnotes_change_audit_tbl')->insert($arrayStatusRecord);

                //CODE END TO SET AUDIT STATUS AFTER SAVE
                //START CODE TO CHANGE ANESTHESIOLOGIST NAME IN HEADER
                $boolChangeAnesName = 'false';
                if ($saveByAnes == 'Yes' && $loggedUserType == 'Anesthesiologist' && !$$relivedIntraNurseIdList) {
                    //IF RELIEVED ANESTHESIOLOGIST NOT EXISTS AND 
                    //IF RECORD SAVED FIRST TIME BY ANESTHESIOLOGIST THEN CHANGE IT IN HEADER ALSO
                    $boolChangeAnesName = 'true';
                    $changeAnesId = $userId;
                } else if ($relivedIntraNurseIdList) { //reliveNurseId change into relieveAnesId acc. to new feedback
                    //IF RELIEVED ANESTHESIOLOGIST EXISTS THEN CHANGE IT IN HEADER ALSO
                    $boolChangeAnesName = 'true';
                    $changeAnesId = $relivedIntraNurseIdList;
                }
                $changeAnesMiddleName='';
                if ($boolChangeAnesName == 'true') {
                    if ($changeAnesId) {
                        $detailChangeUser = $this->getRowRecord('users', 'usersId ', $changeAnesId);
                        if ($detailChangeUser) {
                            if ($detailChangeUser->mname) {
                                $changeAnesMiddleName = ' ' . $detailChangeUser->mname;
                            }
                            $changeAnesName = $detailChangeUser->fname . $changeAnesMiddleName . ' ' . $detailChangeUser->lname;
                            $updateChangeAnesNameQry = "update patientconfirmation set anesthesiologist_id = '" . $changeAnesId . "', anesthesiologist_name = '" . $changeAnesName . "', anes_NA = '' where patientConfirmationId = '" . $pConfirmId . "'";
                            $updateChangeAnesNameRes = DB::select($updateChangeAnesNameQry);
                        }
                    }
                }
                //START CODE TO CHANGE ANESTHESIOLOGIST NAME IN HEADER	
                //CODE START TO UPDATE ASSIST BY TRANSLATOR IN HEADER
                $chbx_assistUpdate = $json->Pre_Operative->AssistedbyTranslator; // $_POST['chbx_assist'];
                if ($chbx_assistUpdate == "") {
                    $chbx_assistUpdate = "no";
                }
                $updateAssistByTranslatorQry = "update patientconfirmation set assist_by_translator = '" . $chbx_assistUpdate . "' where patientConfirmationId = '$pConfirmId'";
                $updateAssistByTranslatorRes = DB::select($updateAssistByTranslatorQry);

                //CODE END TO UPDATE ASSIST BY TRANSLATOR IN HEADER
                //CODE TO DISPLAY FORM STATUS ON RIGHT SLIDER(AS RED FLAG OR TICK MARK) 	
                $patient_id = ($patient_id != 0) ? $patient_id : 11;

                //CODE TO CHECK ANESTHESIOLOGIST ALL SIGNATURE AND SET VALUE IN STUB TABLE
                $chartSignedByAnes = $this->chkAnesSignNew($pConfirmId);
                $updateAnesStubTblQry = "UPDATE stub_tbl SET chartSignedByAnes='" . $chartSignedByAnes . "' WHERE patient_confirmation_id='" . $pConfirmId . "'";
                $updateAnesStubTblRes = DB::select($updateAnesStubTblQry);
                //END CODE TO CHECK ANESTHESIOLOGIST SIGNATURE AND SET VALUE IN STUB TABLE
                //REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)
                //REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)
            }
            $status = 1;
            $savedStatus = 1;
            $message = 'Saved Successfully !';
        }
        return response()->json(['status' => $status, 'savedStatus' => $savedStatus, 'message' => $message, 'requiredStatus' => '', 'data' =>$data,
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
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

    public function saveVitalSigns($vitalSignGridRecordIdArr, $pConfirmId, $patient_id, $userId, $loggedInUserName) {
        if (is_array($vitalSignGridRecordIdArr) && count($vitalSignGridRecordIdArr) > 0) {
            foreach ($vitalSignGridRecordIdArr as $gridRowId) {
                $vTime = $gridRowId->start_time;
                $vSystolic = $gridRowId->systolic;
                $vDiastolic = $gridRowId->diastolic;
                $vPulse = $gridRowId->pulse;
                $vRR = $gridRowId->rr;
                $vTemp = $gridRowId->temp;
                $vEtco2 = $gridRowId->etco2;
                $vosat2 = $gridRowId->osat2;
                $gridId = $gridRowId->id;
                $vTime = $this->setTmFormat($vTime);
                if ($vTime <> "" && ($vSystolic <> "" || $vDiastolic <> "" || $vPulse <> "" || $vRR <> "" || $vTemp <> "" || $vEtco2 <> "" || $vosat2 <> "")) {
                    $dataArray = array();
                    $dataArray['chartName'] = 'mac_regional_anesthesia_form';
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
                        DB ::table('vital_sign_grid')->where('gridRowId', $gridId)->delete();
                    }
                }
            }
        }
        // Code end here to save vital sign grid data 
    }

    public function saveAllQuestionsAnswered($AllQuestionsAnswered, $pConfId, $patient_id, $userId, $loggedInUserName) {
        //print '<pre>';
        //print_r($AllQuestionsAnswered);
        if (!empty($AllQuestionsAnswered)) {
            foreach ($AllQuestionsAnswered as $patQues) {
                $ques = $patQues->question; // $_POST['ques'][$qkey];
                $ftype = $patQues->f_type; // $_POST['ftype'][$qkey];
                $dtype = $patQues->d_type; // $_POST['dtype'][$qkey];
                $listOptions = ($ftype == 4) ? $patQues->options : '';
                $answer = $patQues->answer; //$_POST['ques_fld'][$qkey];
                $answer = is_array($answer) ? implode(";", $answer) : $answer;
                $patQuesID=$patQues->pat_ques_id;
                $ques = addslashes($ques);
                $answer = addslashes($answer);
                $listOptions = addslashes($listOptions);

                $chkQuery = "Select * From patient_mac_regional_questions Where confirmation_id = " . (int) $pConfId . " And question = '" . $ques . "' " . ($patQuesID ? " And id <> " . (int) $patQuesID . " " : '');
                $chkSql = DB::select($chkQuery); // or die($chkQuery . ': ' . imw_error());
                if ($chkSql) {
                    // Skip this question 
                } else {
                    if ($patQuesID) {
                        $sQry = "Update patient_mac_regional_questions SET answer = '" . $answer . "', modified_on = '" . date('Y-m-d H:i:s') . "', modified_by = " . (int) $userId . " Where confirmation_id = " . (int) $pConfId . " And id = " . (int) $patQuesID;
                    } else {
                        $sQry = "Insert Into patient_mac_regional_questions Set confirmation_id = " . (int) $pConfId . ",  question = '" . $ques .
                                "', f_type = '" . $ftype . "', d_type = '" . $dtype . "', list_options = '" . $listOptions . "', answer = '" . $answer . "', created_on = '" . date('Y-m-d H:i:s') . "', created_by = " . (int) $userId . " ";
                    }
                    $r = DB::select($sQry); // or die('Error in query @ line n o. ' . (__LINE__) . ' - ' . $sQry . ': ' . imw_error());
                }
            }
        }
    }

    public function MedGridSection_Save($MedGrid_data, $med_grid_id, $med_grid_sec_id, $pConfId, $patient_id, $userId, $loggedInUserName) {
        $arrayRecordMedGrids = [];
        $arrayRecordMedSecGrids = [];
        foreach ($MedGrid_data as $MedGrid_datas) {
            if (!empty($MedGrid_datas->blank1)) {
                $label1 = $MedGrid_datas->blank1->lable;
                $arrayRecordMedGrids['blank1_label'] = addslashes($label1);
                $blank1_data = $MedGrid_datas->blank1->blankdata;
                foreach ($blank1_data as $blank1_datas) {
                    $arrayRecordMedGrids[$blank1_datas->key] = addslashes($blank1_datas->value);
                }
            }
            if (!empty($MedGrid_datas->blank2)) {
                $label2 = $MedGrid_datas->blank2->lable;
                $arrayRecordMedGrids['blank2_label'] = addslashes($label2);
                $blank2_data = $MedGrid_datas->blank2->blankdata;
                foreach ($blank2_data as $blank2_datas) {
                    $arrayRecordMedGrids[$blank2_datas->key] = addslashes($blank2_datas->value);
                }
            }
            if (!empty($MedGrid_datas->blank3)) {
                $label3 = $MedGrid_datas->blank3->lable;
                $arrayRecordMedGrids['blank3_label'] = addslashes($label3);
                $blank3_data = $MedGrid_datas->blank3->blankdata;
                foreach ($blank3_data as $blank3_datas) {
                    $arrayRecordMedGrids[$blank3_datas->key] = addslashes($blank3_datas->value);
                }
            }
            if (!empty($MedGrid_datas->blank4)) {
                $label4 = $MedGrid_datas->blank4->lable;
                $arrayRecordMedGrids['blank4_label'] = addslashes($label4);
                $blank4_data = $MedGrid_datas->blank4->blankdata;
                foreach ($blank4_data as $blank4_datas) {
                    $arrayRecordMedGrids[$blank4_datas->key] = addslashes($blank4_datas->value);
                }
            }
            if (!empty($MedGrid_datas->midazolam)) {
                $midazolam_label = $MedGrid_datas->midazolam->lable;
                $arrayRecordMedGrids['mgMidazolam_label'] = addslashes($midazolam_label);
                $midazolam_data = $MedGrid_datas->midazolam->blankdata;
                foreach ($midazolam_data as $midazolam_datas) {
                    $arrayRecordMedGrids[$midazolam_datas->key] = addslashes($midazolam_datas->value);
                }
            }
            if (!empty($MedGrid_datas->propofol)) {
                $propofol_label = $MedGrid_datas->propofol->lable;
                $arrayRecordMedGrids['mgPropofol_label'] = addslashes($propofol_label);
                $propofol_data = $MedGrid_datas->propofol->blankdata;
                foreach ($propofol_data as $propofol_datas) {
                    $arrayRecordMedGrids[$propofol_datas->key] = addslashes($propofol_datas->value);
                }
            }
            

            $arrayRecordMedGrids['confirmation_id'] = $pConfId;

            //'ketamine', 'labetalol', 'Fentanyl', 'spo2', 'o2lpm'
             if (!empty($MedGrid_datas->mcgFentanyl)) {
                $mcgFentanyl_label = $MedGrid_datas->mcgFentanyl->lable;
                $arrayRecordMedSecGrids['mcgFentanyl_label'] = addslashes($mcgFentanyl_label);
                $mcgFentanyl_data = $MedGrid_datas->mcgFentanyl->blankdata;
                foreach ($mcgFentanyl_data as $mcgFentanyl_datas) {
                    $arrayRecordMedSecGrids[$mcgFentanyl_datas->key] = addslashes($mcgFentanyl_datas->value);
                }
            }
            if (!empty($MedGrid_datas->mgKetamine)) {
                $mgKetamine_label = $MedGrid_datas->mgKetamine->lable;
                $arrayRecordMedSecGrids['mgKetamine_label'] = addslashes($mgKetamine_label);
                $mgKetamine_data = $MedGrid_datas->mgKetamine->blankdata;
                foreach ($mgKetamine_data as $mgKetamine_datas) {
                    $arrayRecordMedSecGrids[$mgKetamine_datas->key] = addslashes($mgKetamine_datas->value);
                }
            }
           
            if (!empty($MedGrid_datas->mgLabetalol)) {
                $mgLabetalol_label = $MedGrid_datas->mgLabetalol->lable;
                $arrayRecordMedSecGrids['mgLabetalol_label'] = addslashes($mgLabetalol_label);
                $mgLabetalol_data = $MedGrid_datas->mgLabetalol->blankdata;
                foreach ($mgLabetalol_data as $mgLabetalol_datas) {
                    $arrayRecordMedSecGrids[$mgLabetalol_datas->key] = addslashes($mgLabetalol_datas->value);
                }
            }
            if (!empty($MedGrid_datas->spo2)) {
                $spo2_data = $MedGrid_datas->spo2->blankdata;
                foreach ($spo2_data as $spo2_datas) {
                    $arrayRecordMedSecGrids[$spo2_datas->key] = addslashes($spo2_datas->value);
                }
            }
            if (!empty($MedGrid_datas->o2lpm)) {
                $o2lpm_data = $MedGrid_datas->o2lpm->blankdata;
                foreach ($o2lpm_data as $o2lpm_datas) {
                    $arrayRecordMedSecGrids[$o2lpm_datas->key] = addslashes($o2lpm_datas->value);
                }
            }
        }
        $arrayRecordMedSecGrids['confirmation_id'] = $pConfId;
        if ($med_grid_sec_id > 0) {
            DB::table('localanesthesiarecordmedgridsec')->where('confirmation_id', $pConfId)->update($arrayRecordMedSecGrids);
        } else {
            DB::table()->insert();
            $this->addRecords($arrayRecordMedSecGrids, 'localanesthesiarecordmedgridsec');
        }
        if ($med_grid_id > 0) {
            DB::table('localanesthesiarecordmedgrid')->where('confirmation_id',$pConfId)->update($arrayRecordMedGrids);
       } else {
            DB::table('localanesthesiarecordmedgrid')->insert($arrayRecordMedGrids);
        }
    }

    public function convertBase64_LocAnesRec($imagesrc, $instructionSheetId, $patient_id, $pConfirmId) {
        $imagesrc = str_ireplace(' ', '+', $imagesrc);
        $imageName = $instructionSheetId . "_" . $patient_id . "_" . $pConfirmId . '.' . 'png';
        if (trim($imagesrc) <> "") {
            //admin\pdfFiles\gen_anes_detail
            $newfile = explode(",", $imagesrc);
            if (!file_exists('../../SigPlus_images/PatientId_' . $patient_id)) {
                mkdir('../../SigPlus_images/PatientId_' . $patient_id, 0777);
            }
            if (!file_exists('../../SigPlus_images/PatientId_' . $patient_id . "/local_anes_detail/")) {
                mkdir('../../SigPlus_images/PatientId_' . $patient_id . "/local_anes_detail/", 0777);
            }
            $success = @file_put_contents('../../SigPlus_images/PatientId_' . $patient_id . "/local_anes_detail/" . $imageName, base64_decode($newfile[1]));
            if ($success) {
                return '../../SigPlus_images/PatientId_' . $patient_id . "/local_anes_detail/" . $imageName;
            }
        }
        return '';
    }

}
