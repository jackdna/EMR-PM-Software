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

class LaserProcedureController extends Controller {

    public function LaserProcedure_form(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $loginUserType = $request->json()->get('UserType') ? $request->json()->get('UserType') : $request->input('UserType');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $data = [];
        $status = 0;
        $savedResponse = [];
        // $requiredStatus = 1;
        $message = " unauthorized ";
        $requiredStatus = [];
        $userId = $this->checkToken($userToken);
        $patient_instruction_id = 0;
        $scoringDetails = [];
        if ($userId > 0) {
            if ($pConfirmId == "") {
                $message = " ConfirmId is missing ";
                $status = 1;
                $requiredStatus = 0;
            } else {
                $ViewUserNameQry = "select fname,mname,lname,user_type from `users` where  usersId = '" . $userId . "'";
                $ViewUserNameRow = DB::selectone($ViewUserNameQry); // or die($ViewUserNameQry . imw_error());
                $loggedInUserFirstName = $ViewUserNameRow->fname;
                $loggedInUserMiddleName = $ViewUserNameRow->mname;
                $loggedInUserLastName = $ViewUserNameRow->lname;
                $user_type = $ViewUserNameRow->user_type;
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
                $laser_chk_spot_duration ='';
                $laser_chk_spot_size='';
                $laser_chk_power='';
                $laser_chk_shots='';
                $laser_chk_total_energy='';
                $laser_chk_degree_of_opening='';
                $laser_chk_exposure='';
                $laser_chk_count='';
                $laser_chk_chief_complaint='';
                $laser_present_illness_hx_detail='';
                $laser_past_med_hx_detail='';
                $laser_medication_detail='';
                $laser_chk_sle='';
                $laser_chk_fundus_exam='';
                $laser_chk_mental_state='';

                $allergy1 = "select * from allergies ORDER BY `allergies`.`name` ASC";
                $allergic = DB::select($allergy1);
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

                //START CODE TO CHECK NURSE,SURGEON SIGN IN DATABASE(169)
                $chkNurseSignDetails = $this->getRowRecord('laser_procedure_patient_table', 'confirmation_id', $pConfirmId, "", "", " *, date_format(signSurgeon1DateTime,'%m-%d-%Y %h:%i %p') as signSurgeon1DateTimeFormat, date_format(signNurseDateTime,'%m-%d-%Y %h:%i %p') as signNurseDateTimeFormat");
                if ($chkNurseSignDetails) {
                    $signNurseId = $chkNurseSignDetails->signNurseId;
                    $signSurgeon1Id = $chkNurseSignDetails->signSurgeon1Id;
                    $verified_surgeonName = $chkNurseSignDetails->verified_surgeon_Name;
                    $chk_laser_patient_evaluated = $chkNurseSignDetails->chk_laser_patient_evaluated;

                    //CHECK FORM STATUS
                    $chk_form_status = $chkNurseSignDetails->form_status;
                    //CHECK FORM STATUS

                    $signSurgeon1Id = $chkNurseSignDetails->signSurgeon1Id;
                    $signSurgeon1DateTime = $chkNurseSignDetails->signSurgeon1DateTime;
                    $signSurgeon1DateTimeFormat = date("m-d-Y h:i A", strtotime($chkNurseSignDetails->signSurgeon1DateTime));
                    $signSurgeon1FirstName = $chkNurseSignDetails->signSurgeon1FirstName;
                    $signSurgeon1MiddleName = $chkNurseSignDetails->signSurgeon1MiddleName;
                    $signSurgeon1LastName = $chkNurseSignDetails->signSurgeon1LastName;
                    $signSurgeon1Status = $chkNurseSignDetails->signSurgeon1Status;

                    $signNurseId = $chkNurseSignDetails->signNurseId;
                    $signNurseDateTime = $chkNurseSignDetails->signNurseDateTime;
                    $signNurseDateTimeFormat = date("m-d-Y h:i A", strtotime($chkNurseSignDetails->signNurseDateTime));
                    $signNurseFirstName = $chkNurseSignDetails->signNurseFirstName;
                    $signNurseMiddleName = $chkNurseSignDetails->signNurseMiddleName;
                    $signNurseLastName = $chkNurseSignDetails->signNurseLastName;
                    $signNurseStatus = $chkNurseSignDetails->signNurseStatus;
                }
//END CODE TO CHECK NURSE SIGN IN DATABASE
                $str_prelaserprocedure_templete = "SELECT * FROM laser_procedure_patient_table WHERE confirmation_id='$pConfirmId' ";

                $fetchRows_preprocedure1 = DB::selectone($str_prelaserprocedure_templete);
                // $prelaserprocedure_templete_tblNumRow = imw_num_rows($qry_prelaserprocedure_templete);
                //$fetchRows_preprocedure1 = imw_fetch_array($qry_prelaserprocedure_templete);
                $laserprocedurePatientRecordid = $fetchRows_preprocedure1->patient_id;
                $laserprocedureSaveFromChart = $fetchRows_preprocedure1->saveFromChart;
                $prefilMedicationStatus = $fetchRows_preprocedure1->prefilMedicationStatus;

                if ($laserprocedureSaveFromChart == 1 || $laserprocedurePatientRecordid == 0) {
                    // GETTING CONFIRMATION DETAILS
                    $detailConfirmation_procedure = $this->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfirmId);
                    $laserprocedure_Id = $detailConfirmation_procedure->patient_primary_procedure_id;

                    $detailConfirmation_procedure->surgeonId = $detailConfirmation_procedure->surgeonId ? $detailConfirmation_procedure->surgeonId : 0;
                    // GETTING laser procedure templete detail
                    $str_procedure_templete = "SELECT * FROM laser_procedure_template WHERE laser_procedureID = '" . $laserprocedure_Id . "' and laser_surgeonID = '" . $detailConfirmation_procedure->surgeonId . "' Order By laser_templateID Desc ";

                    $qry_procedure_templete = DB::select($str_procedure_templete);

                    if (!$qry_procedure_templete) {
                        $str_procedure_templete = "SELECT * FROM laser_procedure_template WHERE laser_procedureID = '" . $laserprocedure_Id . "' and FIND_IN_SET(" . $detailConfirmation_procedure->surgeonId . ",laser_surgeonID) Order By laser_templateID Desc ";
                        $qry_procedure_templete = DB::select($str_procedure_templete);
                        //$procedure_templete_tblNumRow = imw_num_rows($qry_procedure_templete);

                        if (!$qry_procedure_templete) {
                            $str_procedure_templete = "SELECT * FROM laser_procedure_template WHERE laser_procedureID = '" . $laserprocedure_Id . "' and laser_surgeonID = 'all' Order By laser_templateID Desc ";

                            $qry_procedure_templete = DB::select($str_procedure_templete);
                            //$procedure_templete_tblNumRow = imw_num_rows($qry_procedure_templete);
                        }
                    }
                    foreach ($qry_procedure_templete as $fetchRows_procedure) {
                        $procedure_surgeonId = $detailConfirmation_procedure->surgeonId;
                        $surgeon_select_explode = $fetchRows_procedure->laser_surgeonID;

                        if ($surgeon_select_explode != "all") {
                            $surgeon_select = explode(",", $surgeon_select_explode);
                            $count_surgeon = count($surgeon_select);

                            if ($count_surgeon == 1) {
                                if ($procedure_surgeonId == $surgeon_select_explode) {

                                    $laser_chk_chief_complaint = $fetchRows_procedure->laser_chk_chief_complaint;
                                    $laser_chief_complaint_detail = stripslashes($fetchRows_procedure->laser_chief_complaint);

                                    $laser_chk_present_illness_hx = $fetchRows_procedure->laser_chk_present_illness_hx;
                                    $laser_present_illness_hx_detail = stripslashes($fetchRows_procedure->laser_present_illness_hx);

                                    $laser_chk_past_med_hx = $fetchRows_procedure->laser_chk_past_med_hx;
                                    $laser_past_med_hx_detail = stripslashes($fetchRows_procedure->laser_past_med_hx);

                                    $laser_chk_medication = $fetchRows_procedure->laser_chk_medication;
                                    $laser_medication_detail = stripslashes($fetchRows_procedure->laser_medication);

                                    $laser_chk_sle = $fetchRows_procedure->laser_chk_sle;
                                    $laser_sle_detail = stripslashes($fetchRows_procedure->laser_sle);

                                    $allergies_status_reviewed ='';// $fetchRows_procedure->chbx_drug_react_reviewed;

                                    $laser_chk_fundus_exam = $fetchRows_procedure->laser_chk_fundus_exam;
                                    $laser_fundus_exam_detail = stripslashes($fetchRows_procedure->laser_fundus_exam);

                                    $laser_chk_mental_state = $fetchRows_procedure->laser_chk_mental_state;
                                    $laser_mental_state_detail = stripslashes($fetchRows_procedure->laser_mental_state);

                                    $laser_chk_pre_op_diagnosis = $fetchRows_procedure->laser_chk_pre_op_diagnosis;
                                    $laser_pre_op_diagnosis = stripslashes($fetchRows_procedure->laser_pre_op_diagnosis);

                                    $laser_chk_spot_duration = $fetchRows_procedure->laser_chk_spot_duration;
                                    $laser_spot_duration_detail = stripslashes($fetchRows_procedure->laser_spot_duration);

                                    $laser_chk_spot_size = $fetchRows_procedure->laser_chk_spot_size;
                                    $laser_spot_size_detail = stripslashes($fetchRows_procedure->laser_spot_size);

                                    $laser_chk_power = $fetchRows_procedure->laser_chk_power;
                                    $laser_power_detail = stripslashes($fetchRows_procedure->laser_power);

                                    $laser_chk_shots = $fetchRows_procedure->laser_chk_shots;
                                    $laser_shots_detail = stripslashes($fetchRows_procedure->laser_shots);

                                    $laser_chk_total_energy = $fetchRows_procedure->laser_chk_total_energy;
                                    $laser_total_energy_detail = stripslashes($fetchRows_procedure->laser_total_energy);

                                    $laser_chk_degree_of_opening = $fetchRows_procedure->laser_chk_degree_of_opening;
                                    $laser_degree_of_opening_detail = stripslashes($fetchRows_procedure->laser_degree_of_opening);

                                    $laser_chk_exposure = $fetchRows_procedure->laser_chk_exposure;
                                    $laser_exposure_detail = stripslashes($fetchRows_procedure->laser_exposure);

                                    $laser_chk_count = $fetchRows_procedure->laser_chk_count;
                                    $laser_count_detail = stripslashes($fetchRows_procedure->laser_count);

                                    $laser_chk_post_progress = $fetchRows_procedure->laser_chk_post_progress;
                                    $laser_post_progress_detail = stripslashes($fetchRows_procedure->laser_post_progress);

                                    $laser_chk_post_operative = $fetchRows_procedure->laser_chk_post_operative;
                                    $laser_post_operative_detail = stripslashes($fetchRows_procedure->laser_post_operative);

                                    $laser_preop_medication = $fetchRows_procedure->laser_preop_medication;

                                    $laser_chk_procedure_image = $fetchRows_procedure->laser_chk_procedure_image;
                                    $laser_procedure_image ='';// $fetchRows_procedure->laser_procedure_image;

                                    break;
                                }
                            }
                            $matchedSurgeon = false;
                            if ($count_surgeon > 1) {
                                for ($i = 0; $i < $count_surgeon; $i++) {
                                    $match_surgeonid = $procedure_surgeonId;
                                    $surgeon = $surgeon_select[$i];
                                    if ($surgeon == $match_surgeonid) {
                                        $matchedSurgeon = true;
                                        $laser_chk_chief_complaint = $fetchRows_procedure->laser_chk_chief_complaint;
                                        $laser_chief_complaint_detail = stripslashes($fetchRows_procedure->laser_chief_complaint);

                                        $laser_chk_present_illness_hx = $fetchRows_procedure->laser_chk_present_illness_hx;
                                        $laser_present_illness_hx_detail = stripslashes($fetchRows_procedure->laser_present_illness_hx);

                                        $laser_chk_past_med_hx = $fetchRows_procedure->laser_chk_past_med_hx;
                                        $laser_past_med_hx_detail = stripslashes($fetchRows_procedure->laser_past_med_hx);

                                        $laser_chk_medication = $fetchRows_procedure->laser_chk_medication;
                                        $laser_medication_detail = stripslashes($fetchRows_procedure->laser_medication);

                                        $laser_chk_sle = $fetchRows_procedure->laser_chk_sle;
                                        $laser_sle_detail = stripslashes($fetchRows_procedure->laser_sle);

                                        $allergies_status_reviewed ='';// $fetchRows_procedure->chbx_drug_react_reviewed;

                                        $laser_chk_fundus_exam = $fetchRows_procedure->laser_chk_fundus_exam;
                                        $laser_fundus_exam_detail = stripslashes($fetchRows_procedure->laser_fundus_exam);

                                        $laser_chk_mental_state = $fetchRows_procedure->laser_chk_mental_state;
                                        $laser_mental_state_detail = stripslashes($fetchRows_procedure->laser_mental_state);

                                        $laser_chk_pre_op_diagnosis = $fetchRows_procedure->laser_chk_pre_op_diagnosis;
                                        $laser_pre_op_diagnosis = stripslashes($fetchRows_procedure->laser_pre_op_diagnosis);

                                        $laser_chk_spot_duration = $fetchRows_procedure->laser_chk_spot_duration;
                                        $laser_spot_duration_detail = stripslashes($fetchRows_procedure->laser_spot_duration);

                                        $laser_chk_spot_size = $fetchRows_procedure->laser_chk_spot_size;
                                        $laser_spot_size_detail = stripslashes($fetchRows_procedure->laser_spot_size);

                                        $laser_chk_power = $fetchRows_procedure->laser_chk_power;
                                        $laser_power_detail = stripslashes($fetchRows_procedure->laser_power);

                                        $laser_chk_shots = $fetchRows_procedure->laser_chk_shots;
                                        $laser_shots_detail = stripslashes($fetchRows_procedure->laser_shots);

                                        $laser_chk_total_energy = $fetchRows_procedure->laser_chk_total_energy;
                                        $laser_total_energy_detail = stripslashes($fetchRows_procedure->laser_total_energy);

                                        $laser_chk_degree_of_opening = $fetchRows_procedure->laser_chk_degree_of_opening;
                                        $laser_degree_of_opening_detail = stripslashes($fetchRows_procedure->laser_degree_of_opening);

                                        $laser_chk_exposure = $fetchRows_procedure->laser_chk_exposure;
                                        $laser_exposure_detail = stripslashes($fetchRows_procedure->laser_exposure);

                                        $laser_chk_count = $fetchRows_procedure->laser_chk_count;
                                        $laser_count_detail = stripslashes($fetchRows_procedure->laser_count);

                                        $laser_chk_post_progress = $fetchRows_procedure->laser_chk_post_progress;
                                        $laser_post_progress_detail = stripslashes($fetchRows_procedure->laser_post_progress);

                                        $laser_chk_post_operative = $fetchRows_procedure->laser_chk_post_operative;
                                        $laser_post_operative_detail = stripslashes($fetchRows_procedure->laser_post_operative);

                                        $laser_preop_medication = $fetchRows_procedure->laser_preop_medication;

                                        $laser_chk_procedure_image = $fetchRows_procedure->laser_chk_procedure_image;
                                        $laser_procedure_image = '';//$fetchRows_procedure->laser_procedure_image;
                                    }
                                }
                            }
                            if ($matchedSurgeon == true) {
                                break;
                            }
                        } else {
                            $laser_chk_chief_complaint = $fetchRows_procedure->laser_chk_chief_complaint;
                            $laser_chief_complaint_detail = stripslashes($fetchRows_procedure->laser_chief_complaint);

                            $laser_chk_present_illness_hx = $fetchRows_procedure->laser_chk_present_illness_hx;
                            $laser_present_illness_hx_detail = stripslashes($fetchRows_procedure->laser_present_illness_hx);

                            $laser_chk_past_med_hx = $fetchRows_procedure->laser_chk_past_med_hx;
                            $laser_past_med_hx_detail = stripslashes($fetchRows_procedure->laser_past_med_hx);

                            $laser_chk_medication = $fetchRows_procedure->laser_chk_medication;
                            $laser_medication_detail = stripslashes($fetchRows_procedure->laser_medication);

                            $laser_chk_sle = $fetchRows_procedure->laser_chk_sle;
                            $laser_sle_detail = stripslashes($fetchRows_procedure->laser_sle);

                            $allergies_status_reviewed ='';// $fetchRows_procedure->chbx_drug_react_reviewed;

                            $laser_chk_fundus_exam = $fetchRows_procedure->laser_chk_fundus_exam;
                            $laser_fundus_exam_detail = stripslashes($fetchRows_procedure->laser_fundus_exam);

                            $laser_chk_mental_state = $fetchRows_procedure->laser_chk_mental_state;
                            $laser_mental_state_detail = stripslashes($fetchRows_procedure->laser_mental_state);

                            $laser_chk_pre_op_diagnosis = $fetchRows_procedure->laser_chk_pre_op_diagnosis;
                            $laser_pre_op_diagnosis = stripslashes($fetchRows_procedure->laser_pre_op_diagnosis);

                            $laser_chk_spot_duration = $fetchRows_procedure->laser_chk_spot_duration;
                            $laser_spot_duration_detail = stripslashes($fetchRows_procedure->laser_spot_duration);

                            $laser_chk_spot_size = $fetchRows_procedure->laser_chk_spot_size;
                            $laser_spot_size_detail = stripslashes($fetchRows_procedure->laser_spot_size);

                            $laser_chk_power = $fetchRows_procedure->laser_chk_power;
                            $laser_power_detail = stripslashes($fetchRows_procedure->laser_power);

                            $laser_chk_shots = $fetchRows_procedure->laser_chk_shots;
                            $laser_shots_detail = stripslashes($fetchRows_procedure->laser_shots);

                            $laser_chk_total_energy = $fetchRows_procedure->laser_chk_total_energy;
                            $laser_total_energy_detail = stripslashes($fetchRows_procedure->laser_total_energy);

                            $laser_chk_degree_of_opening = $fetchRows_procedure->laser_chk_degree_of_opening;
                            $laser_degree_of_opening_detail = stripslashes($fetchRows_procedure->laser_degree_of_opening);

                            $laser_chk_exposure = $fetchRows_procedure->laser_chk_exposure;
                            $laser_exposure_detail = stripslashes($fetchRows_procedure->laser_exposure);

                            $laser_chk_count = $fetchRows_procedure->laser_chk_count;
                            $laser_count_detail = stripslashes($fetchRows_procedure->laser_count);

                            $laser_chk_post_progress = $fetchRows_procedure->laser_chk_post_progress;
                            $laser_post_progress_detail = stripslashes($fetchRows_procedure->laser_post_progress);

                            $laser_chk_post_operative = $fetchRows_procedure->laser_chk_post_operative;
                            $laser_post_operative_detail = stripslashes($fetchRows_procedure->laser_post_operative);

                            $laser_preop_medication = $fetchRows_procedure->laser_preop_medication;

                            $laser_chk_procedure_image = $fetchRows_procedure->laser_chk_procedure_image;
                            $laser_procedure_image ='';// $fetchRows_procedure->laser_procedure_image;
                        }
                    }
                }
//End prefill for if the patient chart note if NOT SAVED and is to be saved
                else {
                    $str_prelaserprocedure_templete11 = "SELECT *, date_format(signSurgeon1DateTime,'%m-%d-%Y %h:%i %p') as signSurgeon1DateTimeFormat, date_format(signNurseDateTime,'%m-%d-%Y %h:%i %p') as signNurseDateTimeFormat FROM laser_procedure_patient_table WHERE patient_id  = '$patient_id' AND confirmation_id='$pConfirmId'";
                    // echo $str_prelaserprocedure_templete11;
                    $fetchRows_preprocedure = DB::selectone($str_prelaserprocedure_templete11);
                    //print '<pre>';print_r($fetchRows_preprocedure);die;
                    //$prelaserprocedure_templete_tblNumRow11 = imw_num_rows($qry_prelaserprocedure_templete11);
                    //   $fetchRows_preprocedure = imw_fetch_array($qry_prelaserprocedure_templete11);
                    /*
                      $signSurgeon1DateTimeFormat = date("m-d-Y h:i A",strtotime($chkNurseSignDetails->signSurgeon1DateTime));
                      $signSurgeon1FirstName = $chkNurseSignDetails->signSurgeon1FirstName;
                      $signSurgeon1MiddleName = $chkNurseSignDetails->signSurgeon1MiddleName;
                      $signSurgeon1LastName = $chkNurseSignDetails->signSurgeon1LastName;
                      $signSurgeon1Status = $chkNurseSignDetails->signSurgeon1Status;

                      $signNurseId = $chkNurseSignDetails->signNurseId;
                      $signNurseDateTime = $chkNurseSignDetails->signNurseDateTime;
                      $signNurseDateTimeFormat = date("m-d-Y h:i A",strtotime($chkNurseSignDetails->signNurseDateTime));
                     *  */
                    if ($fetchRows_preprocedure) {
                        $laserprocedureRecordpostedID = $fetchRows_preprocedure->laser_procedureRecordID;
                        $form_status = $fetchRows_preprocedure->form_status;
                        $laser_chk_chief_complaint = $fetchRows_preprocedure->chk_laser_chief_complaint;
                        $laser_chief_complaint_detail = stripslashes($fetchRows_preprocedure->laser_chief_complaint);

                        $laser_chk_present_illness_hx = $fetchRows_preprocedure->chk_laser_present_illness_hx;
                        $laser_present_illness_hx_detail = stripslashes($fetchRows_preprocedure->laser_present_illness_hx);

                        $laser_chk_past_med_hx = $fetchRows_preprocedure->chk_laser_past_med_hx;
                        $laser_past_med_hx_detail = stripslashes($fetchRows_preprocedure->laser_past_med_hx);

                        $laser_chk_medication = $fetchRows_preprocedure->chk_laser_medication;
                        $laser_medication_detail = stripslashes($fetchRows_preprocedure->laser_medication);

                        $laser_chk_sle = $fetchRows_preprocedure->chk_laser_sle;
                        $laser_sle_detail = stripslashes($fetchRows_preprocedure->laser_sle);

                        $laser_other_detail = stripslashes($fetchRows_preprocedure->laser_other);

                        $allergies_status_reviewed = $fetchRows_preprocedure->allergies_status_reviewed;

                        $laser_chk_fundus_exam = $fetchRows_preprocedure->chk_laser_fundus_exam;
                        $laser_fundus_exam_detail = stripslashes($fetchRows_preprocedure->laser_fundus_exam);

                        $laser_chk_mental_state = $fetchRows_preprocedure->chk_laser_mental_state;
                        $laser_mental_state_detail = stripslashes($fetchRows_preprocedure->laser_mental_state);

                        $laser_chk_pre_op_diagnosis = $fetchRows_preprocedure->chk_laser_pre_op_diagnosis;
                        $laser_pre_op_diagnosis = stripslashes($fetchRows_preprocedure->pre_op_diagnosis); //

                        $laser_chk_spot_duration = $fetchRows_preprocedure->chk_laser_spot_duration;
                        $laser_spot_duration_detail = stripslashes($fetchRows_preprocedure->laser_spot_duration);

                        $laser_chk_spot_size = $fetchRows_preprocedure->chk_laser_spot_size;
                        $laser_spot_size_detail = stripslashes($fetchRows_preprocedure->laser_spot_size);

                        $laser_chk_power = $fetchRows_preprocedure->chk_laser_power;
                        $laser_power_detail = stripslashes($fetchRows_preprocedure->laser_power);

                        $laser_chk_shots = $fetchRows_preprocedure->chk_laser_shots;
                        $laser_shots_detail = stripslashes($fetchRows_preprocedure->laser_shots);

                        $laser_chk_total_energy = $fetchRows_preprocedure->chk_laser_total_energy;
                        $laser_total_energy_detail = stripslashes($fetchRows_preprocedure->laser_total_energy);

                        $laser_chk_degree_of_opening = $fetchRows_preprocedure->chk_laser_degree_of_opening;
                        $laser_degree_of_opening_detail = stripslashes($fetchRows_preprocedure->laser_degree_of_opening);

                        $laser_chk_exposure = $fetchRows_preprocedure->chk_laser_exposure;
                        $laser_exposure_detail = stripslashes($fetchRows_preprocedure->laser_exposure);

                        $laser_chk_count = $fetchRows_preprocedure->chk_laser_count;
                        $laser_count_detail = stripslashes($fetchRows_preprocedure->laser_count);

                        $laser_chk_post_progress = $fetchRows_preprocedure->chk_laser_post_progress;
                        $laser_post_progress_detail = stripslashes($fetchRows_preprocedure->laser_post_progress);
                        $laser_medical_evaluation = stripslashes($fetchRows_preprocedure->laser_medical_evaluation);

                        $laser_chk_post_operative = $fetchRows_preprocedure->chk_laser_post_operative;
                        $laser_post_operative_detail = stripslashes($fetchRows_preprocedure->laser_post_operative);

                        $laser_preop_medication = isset($fetchRows_preprocedure->laser_preop_medication) ? $fetchRows_preprocedure->laser_preop_medication : "";

                        $best_correction_vision_R = $fetchRows_preprocedure->best_correction_vision_R;
                        $best_correction_vision_L = $fetchRows_preprocedure->best_correction_vision_L;

                        $glare_acuity_R = $fetchRows_preprocedure->glare_acuity_R;
                        $glare_acuity_L = $fetchRows_preprocedure->glare_acuity_L;

                        $pre_laser_IOP_R = $fetchRows_preprocedure->pre_laser_IOP_R;
                        $pre_laser_IOP_L = $fetchRows_preprocedure->pre_laser_IOP_L;
                        $pre_iop_na = $fetchRows_preprocedure->pre_iop_na;

                        $laser_comments = stripslashes($fetchRows_preprocedure->laser_comments);
                        $laser_other_pre_medication = stripslashes($fetchRows_preprocedure->laser_other_pre_medication);



                        $laser_procedure_notes = stripslashes($fetchRows_preprocedure->laser_procedure_notes);
                        $stable_chbx = stripslashes($fetchRows_preprocedure->stable_chbx);
                        $stable_other_chbx = stripslashes($fetchRows_preprocedure->stable_other_chbx);
                        $stable_other_txtbx = stripslashes($fetchRows_preprocedure->stable_other_txtbx);
                        $chk_laser_patient_evaluated = stripslashes($fetchRows_preprocedure->chk_laser_patient_evaluated);
                        $prelaserVitalSignBP = stripslashes($fetchRows_preprocedure->prelaserVitalSignBP);
                        $prelaserVitalSignP = stripslashes($fetchRows_preprocedure->prelaserVitalSignP);
                        $prelaserVitalSignR = stripslashes($fetchRows_preprocedure->prelaserVitalSignR);

                        $postlaserVitalSignBP = stripslashes($fetchRows_preprocedure->postlaserVitalSignBP);
                        $postlaserVitalSignP = stripslashes($fetchRows_preprocedure->postlaserVitalSignP);
                        $postlaserVitalSignR = stripslashes($fetchRows_preprocedure->postlaserVitalSignR);


                        $iop_pressure_l = stripslashes($fetchRows_preprocedure->iop_pressure_l); //
                        $iop_pressure_r = stripslashes($fetchRows_preprocedure->iop_pressure_r); //
                        $iop_na = stripslashes($fetchRows_preprocedure->iop_na); //

                        $post_comment = stripslashes($fetchRows_preprocedure->post_op_operative_comment); //

                        $laser_os = stripslashes($fetchRows_preprocedure->laser_os);
                        $laser_od = stripslashes($fetchRows_preprocedure->laser_od);

                        $prefilMedicationStatus = $fetchRows_preprocedure->prefilMedicationStatus;

                        $signSurgeon1Id = $fetchRows_preprocedure->signSurgeon1Id;
                        $signSurgeon1DateTime = $fetchRows_preprocedure->signSurgeon1DateTime;
                        $signSurgeon1DateTimeFormat = date("m-d-Y h:i A", strtotime($fetchRows_preprocedure->signSurgeon1DateTimeFormat));
                        $signSurgeon1FirstName = $fetchRows_preprocedure->signSurgeon1FirstName;
                        $signSurgeon1MiddleName = $fetchRows_preprocedure->signSurgeon1MiddleName;
                        $signSurgeon1LastName = $fetchRows_preprocedure->signSurgeon1LastName;
                        $signSurgeon1Status = $fetchRows_preprocedure->signSurgeon1Status;

                        $signNurseId = $fetchRows_preprocedure->signNurseId;
                        $signNurseDateTime = $fetchRows_preprocedure->signNurseDateTime;
                        $signNurseDateTimeFormat = date("m-d-Y h:i A", strtotime($fetchRows_preprocedure->signNurseDateTimeFormat));
                        $signNurseFirstName = stripslashes($fetchRows_preprocedure->signNurseFirstName);
                        $signNurseMiddleName = stripslashes($fetchRows_preprocedure->signNurseMiddleName);
                        $signNurseLastName = stripslashes($fetchRows_preprocedure->signNurseLastName);
                        $signNurseStatus = $fetchRows_preprocedure->signNurseStatus;

                        $laser_chk_procedure_image = $fetchRows_preprocedure->chk_laser_procedure_image;
                        $laser_procedure_image = $fetchRows_preprocedure->laser_procedure_image;
                        $laser_procedure_image_path = $fetchRows_preprocedure->laser_procedure_image_path;

                        $verified_nurseID = $fetchRows_preprocedure->verified_nurse_Id;
                        $verified_nurseName = stripslashes($fetchRows_preprocedure->verified_nurse_name);
                        $verified_nurseStatus = $fetchRows_preprocedure->verified_nurse_Status;
                        $verified_nurseTimeout = $fetchRows_preprocedure->verified_nurse_timeout;
                        if ($verified_nurseTimeout <> '0000-00-00 00:00:00' && !empty($verified_nurseTimeout)) {
                            //$verified_nurseTimeout=date('h:i A',strtotime($verified_nurseTimeout));
                            $verified_nurseTimeout = $this->getTmFormat($verified_nurseTimeout);
                        } else {
                            $verified_nurseTimeout = '';
                        }

                        $verified_surgeonID = $fetchRows_preprocedure->verified_surgeon_Id;
                        $verified_surgeonName = stripslashes($fetchRows_preprocedure->verified_surgeon_Name);
                        $verified_surgeonStatus = $fetchRows_preprocedure->verified_surgeon_Status;
                        $verified_surgeonTimeout = $fetchRows_preprocedure->verified_surgeon_timeout;
                        if ($verified_surgeonTimeout <> '0000-00-00 00:00:00' && !empty($verified_surgeonTimeout)) {
                            //$verified_surgeonTimeout=date('h:i A',strtotime($verified_surgeonTimeout));
                            $verified_surgeonTimeout = $this->getTmFormat($verified_surgeonTimeout);
                        } else {
                            $verified_surgeonTimeout = '';
                        }

                        $asa_status = $fetchRows_preprocedure->asa_status;
                        /* $prelaserVitalSignTime	=	$fetchRows_preprocedure['prelaserVitalSignTime'];
                          if($prelaserVitalSignTime <> '0000-00-00 00:00:00' && !empty($prelaserVitalSignTime)){
                          //$prelaserVitalSignTime=date('h:i A',strtotime($prelaserVitalSignTime));
                          $prelaserVitalSignTime=$objManageData->getTmFormat($prelaserVitalSignTime);
                          }else{
                          $prelaserVitalSignTime	=	'';
                          } */
                        if ($fetchRows_preprocedure->prelaserVitalSignTime == "0000-00-00 00:00:00" || $fetchRows_preprocedure->prelaserVitalSignTime == "") {
                            $prelaserVitalSignTime = "";
                        } else {
                            $prelaserVitalSignTime = $this->getTmFormat($fetchRows_preprocedure->prelaserVitalSignTime);
                        }

                        $postlaserVitalSignTime = $fetchRows_preprocedure->postlaserVitalSignTime;
                        if ($postlaserVitalSignTime <> '0000-00-00 00:00:00' && !empty($postlaserVitalSignTime)) {
                            //$postlaserVitalSignTime=date('h:i A',strtotime($postlaserVitalSignTime));
                            $postlaserVitalSignTime = $this->getTmFormat($postlaserVitalSignTime);
                        } else {
                            $postlaserVitalSignTime = '';
                        }

                        $proc_start_time = $fetchRows_preprocedure->proc_start_time;
                        if ($proc_start_time <> '0000-00-00 00:00:00' && !empty($proc_start_time)) {
                            //$proc_start_time=date('h:i A',strtotime($proc_start_time));
                            $proc_start_time = $this->getTmFormat($proc_start_time);
                        } else {
                            $proc_start_time = '';
                        }
                        $proc_end_time = $fetchRows_preprocedure->proc_end_time;
                        if ($proc_end_time <> '0000-00-00 00:00:00' && !empty($proc_end_time)) {
                            //$proc_end_time=date('h:i A',strtotime($proc_end_time));
                            $proc_end_time = $this->getTmFormat($proc_end_time);
                        } else {
                            $proc_end_time = '';
                        }

                        $discharge_home = (int) $fetchRows_preprocedure->discharge_home;
                        $patients_relation = stripslashes($fetchRows_preprocedure->patients_relation);
                        $patients_relation_other = stripslashes($fetchRows_preprocedure->patients_relation_other);
                        $patient_transfer = (int) $fetchRows_preprocedure->patient_transfer;
                        $discharge_time = $fetchRows_preprocedure->discharge_time;
                        $discharge_time = ($discharge_time && $discharge_time <> '0000-00-00 00:00:00') ? $this->getTmFormat($discharge_time) : '';
                    }
                }


                //PRE OP ORDER PREFILL
                $laserProcDetailsQry = "select * from preopphysicianorders where patient_confirmation_id = '$pConfirmId' ";
                $laserProcDetailsRes = DB::selectone($laserProcDetailsQry); // or die((__LINE__) . '__' . imw_error());
                //$laserProcDetailsNumRow = imw_num_rows($laserProcDetailsRes);
                if ($laserProcDetailsRes) {
                    //$laserProcDetailsRow = imw_fetch_array($laserProcDetailsRes);
                    //$prefilMedicationStatus = $laserProcDetailsRow['prefilMedicationStatus'];
                    $preOpPhysicianOrdersId = $laserProcDetailsRes->preOpPhysicianOrdersId;
                }

                //GETTING SURGEON PROFILE TO SHOW FIRST VIEW OF SURGEONID

                $selectSurgeonQry = "select * from surgeonprofile where surgeonId = '$surgeonId' and del_status=''";
                $selectSurgeonRes = DB::select($selectSurgeonQry); // or die(imw_error());
                foreach ($selectSurgeonRes as $selectSurgeonRow) {
                    $surgeonProfileIdArrLsr[] = $selectSurgeonRow->surgeonProfileId;
                }
                if (is_array($surgeonProfileIdArrLsr)) {
                    $surgeonProfileIdImplode = implode(',', $surgeonProfileIdArrLsr);
                } else {
                    $surgeonProfileIdImplode = 0;
                }
                $selectSurgeonProcedureQry = "select * from surgeonprofileprocedure where profileId in ($surgeonProfileIdImplode) order by procedureName";
                $selectSurgeonProcedureRes = DB::select($selectSurgeonProcedureQry); // or die(imw_error());

                if ($selectSurgeonProcedureRes) {
                    $surgeonProfileIdFound = 0;
                    foreach ($selectSurgeonProcedureRes as $selectSurgeonProcedureRow) {
                        $surgeonProfileProcedureId = $selectSurgeonProcedureRow->procedureId;
                        if ($patient_primary_procedure_id == $surgeonProfileProcedureId) {

                            $surgeonProfileIdFound = $selectSurgeonProcedureRow->profileId;
                        }
                    }

                    /* if($surgeonProfileIdFound) { */
                    $selectSurgeonProfileFoundQry = "select * from surgeonprofile where surgeonProfileId = '$surgeonProfileIdFound' and del_status=''";
                    /* }else {	//ELSE SELECT DEFAULT PROFILE OF SURGOEN
                      $selectSurgeonProfileFoundQry = "select * from surgeonprofile where surgeonId = '$surgeonId' AND defaultProfile = '1'";
                      } */
                    //echo $selectSurgeonProfileFoundQry;
                    $selectSurgeonProfileFoundRow = DB::selectone($selectSurgeonProfileFoundQry); // or die(imw_error());
                    //$selectSurgeonProfileFoundNumRow = imw_num_rows($selectSurgeonProfileFoundRes);
                    if ($selectSurgeonProfileFoundRow) {

                        $postOpDropSurgeonProfile = stripslashes($selectSurgeonProfileFoundRow->postOpDrop);
                        $medicalEvaluationSurgeonProfile = stripslashes($selectSurgeonProfileFoundRow->medicalEvaluation);
                        $preOpOrdersFound = $selectSurgeonProfileFoundRow->preOpOrders;
                        //$preOpOrdersFoundExplode = explode(',',$preOpOrdersFound);
                    }
                }

                $medication_query = "SELECT medicationsId,name,isDefault FROM `medications` ORDER BY `medications`.`name` ASC ";
                $medication_res = DB::select($medication_query);
                $medication = [];
                foreach ($medication_res as $row) {
                    $medication[] = ['medicationsId' => $row->medicationsId, 'name' => $row->name, 'isDefault' => $row->isDefault];
                }
                $preopmedicationorder = "select * from preopmedicationorder where medicationName!='' order by medicationName asc";
                $preopmedicationorder_res = DB::select($preopmedicationorder);
                $Medication_Dropdown = [];
                foreach ($preopmedicationorder_res as $rows) {
                    $Medication_Dropdown[] = ['preOpMedicationOrderId' => $rows->preOpMedicationOrderId, 'medicationName' => $rows->medicationName, 'strength' => $rows->strength, 'directions' => $rows->directions];
                }
                $SpotDuration = "";
                $Count = "";
                $Exposure = "";
                $DegreeofOpening = "";
                $TotalEnergy = "";
                $ofShots = "";
                $Power = "";
                $SpotSize = "";
                //"Allergy_data" => ['allergies_drug_reaction' => $allergic, 'patientAllergiesGrid' => $patientAllergies, 'allergiesNKDA_patientconfirmation_status' => $allergiesNKDA_patientconfirmation_status],

                $SpotDuration = ["visible" => $laser_chk_spot_duration == 'on' ? 1 : 0, "title" => "Spot Duration", "dropdown" => DB::select('SELECT spot_durationID,name FROM `laserpredefine_spot_duration_tbl`'), "textdesc" => isset($laser_spot_duration_detail) ? $laser_spot_duration_detail : "", "titlekey" => "laser_spot_duration",];

                //if($laser_chk_spot_size=='on'){
                $SpotSize = ["visible" => $laser_chk_spot_size == 'on' ? 1 : 0, "title" => "Spot Size", "dropdown" => DB::select("SELECT spot_sizeID,name FROM `laserpredefine_spot_size_tbl`"), "textdesc" => isset($laser_spot_size_detail) ? $laser_spot_size_detail : "", "titlekey" => "laser_spot_size_detail"];
                //}
                //if($laser_chk_power=='on'){
                $Power = ["visible" => $laser_chk_power == 'on' ? 1 : 0, "title" => "Power", "dropdown" => DB::select("SELECT powerID,name FROM `laserpredefine_power_tbl`"), "textdesc" => isset($laser_power_detail) ? $laser_power_detail : "", "titlekey" => "laser_power_detail"];
                //}
                //if($laser_chk_shots=='on'){
                $ofShots = ["visible" => $laser_chk_shots == 'on' ? 1 : 0, "title" => "Of Shots", "dropdown" => DB::select("SELECT shots_ID,name FROM `laserpredefine_shots_tbl`"), "textdesc" => isset($laser_shots_detail) ? $laser_shots_detail : "", "titlekey" => "laser_shots_detail"];
                //}
                //if($laser_chk_total_energy=='on'){
                $TotalEnergy = ["visible" => $laser_chk_total_energy == 'on' ? 1 : 0, "title" => "Total Energy", "dropdown" => DB::select("SELECT total_energyID,name FROM laserpredefine_total_energy_tbl"), "textdesc" => isset($laser_total_energy_detail) ? $laser_total_energy_detail : "", "titlekey" => "laser_total_energy_detail"];
                //}
                //if($laser_chk_degree_of_opening=='on'){
                $DegreeofOpening = ["visible" => $laser_chk_degree_of_opening == 'on' ? 1 : 0, "title" => "Degree of Opening", "dropdown" => DB::select("SELECT degree_openingID,name FROM laserpredefine_degree_opening_tbl"), "textdesc" => isset($laser_degree_of_opening_detail) ? $laser_degree_of_opening_detail : "", "titlekey" => "laser_degree_of_opening_detail"];
                //}
                //if($laser_chk_exposure=='on'){
                $Exposure = ["visible" => $laser_chk_exposure == 'on' ? 1 : 0, "title" => "Exposure", "dropdown" => DB::select("SELECT exposureID,name FROM laserpredefine_exposure_tbl"), "textdesc" => isset($laser_exposure_detail) ? $laser_exposure_detail : "", "titlekey" => "laser_exposure_detail"];
                //}
                //if($laser_chk_count=='on'){
                $Count = ["visible" => $laser_chk_count == 'on' ? 1 : 0, "title" => "Count", "dropdown" => DB::select("SELECT countID,name FROM laserpredefine_count_tbl"), "textdesc" => isset($laser_count_detail) ? $laser_count_detail : "", "titlekey" => "laser_count_detail"];
                //}//select * from laserpredefine_postprogressnotes_tbl order by `name`
                $progressNote = ["visible" => 1, "title" => "Progress Note", "dropdown" => DB::select("select postprogressnotesID,name from laserpredefine_postprogressnotes_tbl order by `name`"), "textdesc" => isset($laser_post_operative_detail) ? $laser_post_operative_detail : "", "titlekey" => "laser_post_operative"];
                $postOPOrder = ["visible" => 1, "title" => "Post OP Order", "dropdown" => DB::select("select postoperativestatusID,name from laserpredefine_postoperativestatus_tbl order by `name`"), "textdesc" => isset($laser_post_progress_detail) ? $laser_post_progress_detail : "", "titlekey" => "laser_post_progress"];
                $data = [
                    'history' => [
                        /* 'cheifcomplaint' => */['visible' => $laser_chk_chief_complaint <> "" ? 1 : 0, 'titlekey' => 'laser_chief_complaint', 'dropdown' => DB::select('SELECT chiefcomplaintID,name FROM `laserpredefine_chiefcomplaint_tbl`'), 'textdesc' => isset($laser_chief_complaint_detail) ? stripslashes($laser_chief_complaint_detail) : "", 'title' => 'Chief Complaint'],
                        /* 'hxpresentillness' => */ ['visible' => $laser_present_illness_hx_detail <> "" ? 1 : 0, 'titlekey' => 'chk_laser_present_illness_hx', 'dropdown' => DB::select('SELECT Hx_Present_illnessID,name FROM `laserpredefine_hx_present_illness_tbl`'), 'textdesc' => isset($laser_present_illness_hx_detail) ? stripslashes($laser_present_illness_hx_detail) : "", 'title' => 'Hx. of Present Illness'],
                        /* 'PastMedicalHx' => */ ['visible' => $laser_past_med_hx_detail <> "" ? 1 : 0, 'titlekey' => 'laser_past_med_hx', 'dropdown' => DB::select('SELECT past_medical_hxID,name FROM `laserpredefine_past_medical_hx_tbl`'), 'textdesc' => isset($laser_past_med_hx_detail) ? stripslashes($laser_past_med_hx_detail) : "", 'title' => 'Past Medical Hx'],
                        /* 'OcularMedicationDosage' => */ ['visible' => $laser_medication_detail <> "" ? 1 : 0, 'titlekey' => 'laser_medication_detail', 'dropdown' => $medication, 'textdesc' => isset($laser_medication_detail) ? stripslashes($laser_medication_detail) : "", 'title' => 'Ocular Medication and Dosage'],
                    ],
                    "Allergy_data" => ['allergies_drug_reaction' => $allergic, 'patientAllergiesGrid' => $patientAllergies, 'allergiesNKDA_patientconfirmation_status' => $allergiesNKDA_patientconfirmation_status, 'allergiesReviewed' => isset($allergies_status_reviewed) ? $allergies_status_reviewed : ""],
                    "Timeout" => [
                        "nursesign" => ["verified_nurse_Id" => isset($verified_nurseID) ? $verified_nurseID : 0, "name" => isset($verified_nurseName) ? $verified_nurseName : "", "checked" => isset($verified_nurseName) && $verified_nurseName != '' ? 'Yes' : 'No', 'verified_nurse_timeout' => isset($verified_nurse_timeout) ? $verified_nurse_timeout : ""],
                        "surgeon" => ["verified_surgeon_Id" => isset($verified_surgeonID) ? $verified_surgeonID : 0, "name" => $verified_surgeonName, "checked" => $verified_surgeonName != '' ? 'Yes' : 'No', 'verified_nurse_timeout' => isset($verified_surgeonTimeout) ? $verified_surgeonTimeout : ""],
                        "SurgeryStartTime" => isset($proc_start_time) ? $proc_start_time : "",
                        "SurgeryEndTime" => isset($proc_end_time) ? $proc_end_time : ""],
                    "medication_evaluation" => ["Stable-illness" => isset($stable_chbx) ? $stable_chbx : "", "Other" => isset($stable_other_chbx) ? $stable_other_chbx : "", "OtherTextbox" => isset($stable_other_txtbx) ? $stable_other_txtbx : ""],
                    "BestCorrectedVision" => ["R20" => isset($best_correction_vision_R) ? $best_correction_vision_R : "", "L20" => isset($best_correction_vision_L) ? $best_correction_vision_L : ""],
                    "GlareAcuity" => ["R20" => isset($glare_acuity_R) ? $glare_acuity_R : "", "L20" => isset($glare_acuity_L) ? $glare_acuity_L : ""],
                    "MentalState_FundusExam_SLE" => [
                        /* "SLE" => */['visible' => $laser_chk_sle == 'on' ? 'Yes' : 'No', 'titlekey' => 'laser_sle_detail', 'dropdown' => DB::select('SELECT sle_ID,name FROM `laserpredefine_sle_tbl`'), 'text_details' => isset($laser_sle_detail) ? stripslashes($laser_sle_detail) : "", 'title' => 'SLE'],
                        /* "FundusExam" => */ ['visible' => $laser_chk_fundus_exam == 'on' ? 'Yes' : 'No', 'titlekey' => 'laser_fundus_exam_detail', 'dropdown' => DB::select('SELECT fundus_exam_ID,name FROM `laserpredefine_fundus_exam_tbl`'), 'text_details' => isset($laser_fundus_exam_detail) ? stripslashes($laser_fundus_exam_detail) : "", 'title' => 'Fundus Exam'],
                        /* "MentalState" => */ ['visible' => $laser_chk_mental_state == 'on' ? 'Yes' : 'No', 'titlekey' => 'laser_mental_state_detail', 'dropdown' => DB::select('SELECT mentalstateID,name FROM `laserpredefine_mentalstate_tbl`'), 'text_details' => isset($laser_mental_state_detail) ? stripslashes($laser_mental_state_detail) : "", 'title' => 'Mental State'],
                    ],
                    "ASA" => ["I" => (isset($asa_status) && $asa_status == 'I') ? 'Yes' : "No", "II" => (isset($asa_status) && $asa_status == 'II') ? 'Yes' : "No", "III" => (isset($asa_status) && $asa_status == 'III') ? "Yes" : "No", "Others" => isset($laser_other_detail) ? $laser_other_detail : "No"],
                    "PreOPOrders" => [
                        "ListofPre_OPMedicationOrders" => DB::select("select * from patientpreopmedication_tbl where patient_confirmation_id='" . $pConfirmId . "' order by patientPreOpMediId ASC"), // 'title' => 'List of Pre-OP Medication Orders'],
                        "ADDMedication_Dropdown" => $Medication_Dropdown,
                        "OtherPreopOrders_PreOPDiagnosis" => [
                            /* "OtherPreopOrders" => */["drop" => $medication, 'titlekey' => 'laser_other_pre_medication', "text_details" => isset($laser_other_pre_medication) ? stripslashes($laser_other_pre_medication) : "", "title" => "Other Pre-Op Orders"],
                            /* "PreOPDiagnosis" => */ ["drop" => DB::select("SELECT icd10_desc FROM icd10_data WHERE deleted ='0' AND icd10_desc !='' ORDER BY icd10_desc"), 'titlekey' => 'laser_pre_op_diagnosis', "text_details" => isset($laser_pre_op_diagnosis) ? $laser_pre_op_diagnosis : "", "title" => "Pre-Op Diagnosis"],
                        ],
                        "otherPreop_comments" => isset($laser_comments) ? stripslashes($laser_comments) : ""
                    ],
                    "ProcedureNotes" => [
                        "Patient_in_satisfactory_condition_for_proposed_laser_procedure" => $chk_laser_patient_evaluated,
                        "Pre_Laser_Vital_Signs" => ["BP" => isset($prelaserVitalSignBP) ? $prelaserVitalSignBP : "", "P" => isset($prelaserVitalSignP) ? $prelaserVitalSignP : "", "R" => isset($prelaserVitalSignR) ? $prelaserVitalSignR : "", "Time" => isset($prelaserVitalSignTime) ? $prelaserVitalSignTime : ""],
                        "Laser_Notes_for_left_eye" => [
                            $SpotDuration, $SpotSize, $Power, $ofShots, $TotalEnergy, $DegreeofOpening, $Exposure, $Count, $progressNote//,$postOPOrder
                        ],
                        "Post_Laser_Vital_Signs" => ["BP" => isset($postlaserVitalSignBP) ? $postlaserVitalSignBP : "", "P" => isset($postlaserVitalSignP) ? $postlaserVitalSignP : "", "R" => isset($postlaserVitalSignR) ? $postlaserVitalSignR : "", "Time" => isset($postlaserVitalSignTime) ? $postlaserVitalSignTime : ""],
                        "Post_OP_Order_Progress_Note" => [$progressNote, $postOPOrder],
                        "Pre_Laser_IOP" => ["R" => (isset($pre_iop_na) && $pre_iop_na == 'Yes') ? '' : isset($pre_laser_IOP_R) ? $pre_laser_IOP_R : "", "L" => (isset($pre_iop_na) && $pre_iop_na == 'Yes') ? '' : isset($pre_laser_IOP_L) ? $pre_laser_IOP_L : "", "N/A" => isset($pre_iop_na) ? $pre_iop_na : ""],
                        "Patient_Discharged_to_Home_With" => (isset($discharge_home) && $discharge_home == 1) ? 'Yes' : "No",
                        "Patient_Transferred_to_Hospital" => (isset($patient_transfer) && $patient_transfer == 1) ? 'Yes' : "No",
                        "Relationship" => [["key" => "Family", "value" => "Family"], ["key" => "Husband", "value" => "Husband"], ["key" => "Wife", "value" => "Wife"], ["key" => "Son", "value" => "Son"], ["key" => "Daughter", "value" => "Daughter"], ["key" => "Sister", "value" => "Sister"], ["key" => "Brother", "value" => "Brother"], ["key" => "Mother", "value" => "Mother"], ["key" => "Father", "value" => "Father"], ['key' => "Friend", "value" => "Friend"], ["key" => "Transportation Driver", "value" => "Transportation Driver"], ["key" => "other", "value" => "Other"]],
                        "Other" => isset($patients_relation_other) ? $patients_relation_other : "", "DischargeTime" => isset($discharge_time) ? $discharge_time : "",
                        "app_Laser_Image" => (isset($laser_procedure_image) && $laser_procedure_image <> "") ? $laser_procedure_image : getenv('APP_URL') . '/' . getenv('APP_ROOT') . "/images/laser_image.jpg",
                        "IOP_Pressure" => ["R" => (isset($iop_na) && $iop_na == 'Yes') ? '' : isset($iop_pressure_r) ? $iop_pressure_r : "", "L" => (isset($iop_na) && $iop_na == 'Yes') ? '' : isset($iop_pressure_l) ? $iop_pressure_l : "", "N/A" => isset($iop_na) ? $iop_na : ""],
                        "Comments" => isset($post_comment) ? $post_comment : "",
                        "signature" => [
                            "surgeon_signature" => ["surgeonId" => $signSurgeon1Id, "name" => $signSurgeon1LastName . ", " . $signSurgeon1FirstName . " " . $signSurgeon1MiddleName, "signed_status" => isset($surgeon1SignOnFileStatus) ? $surgeon1SignOnFileStatus : "", "sign_date" => isset($signSurgeon1DateTimeFormatNew) ? $signSurgeon1DateTimeFormatNew : ""],
                            "nurse1_signature" => ["nurseId" => $signNurseId, "name" => $signNurseLastName . ", " . $signNurseFirstName . " " . $signNurseMiddleName, "signed_status" => $signOnFileStatus, "sign_date" => isset($signNurseDateTimeFormatNew) ? $signNurseDateTimeFormatNew : ""],
                        ]
                    ],
                ];
                $status = 1;
                $message = ' Laser Procedure ';
            }
        }
        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'data' => $data,
                    'requiredStatus' => '',
                    'laserprocedureRecordpostedID' => isset($laserprocedureRecordpostedID) ? $laserprocedureRecordpostedID : 0,
                    'laserdata' => $chkNurseSignDetails,
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

    public function LaserProcedure_save(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $loginUserType = $request->json()->get('UserType') ? $request->json()->get('UserType') : $request->input('UserType');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $app_Laser_Image = $request->json()->get('app_Laser_Image') ? $request->json()->get('app_Laser_Image') : $request->input('app_Laser_Image');
        $laserprocedureRecordpostedID = $request->json()->get('laserprocedureRecordpostedID') ? $request->json()->get('laserprocedureRecordpostedID') : $request->input('laserprocedureRecordpostedID');
        $jsondata = $request->json()->get('jsondata') ? $request->json()->get('jsondata') : $request->input('jsondata');
        $json = json_decode($jsondata);
        $data = [];
        $status = 0;
        $savedStatus = 0;
        $savedResponse = [];
        // $requiredStatus = 1;
        $message = " unauthorized ";
        $requiredStatus = [];
        $userId = $this->checkToken($userToken);
        $patient_instruction_id = 0;
        $scoringDetails = [];
        if ($userId > 0) {
            if ($pConfirmId == "") {
                $message = " ConfirmId is missing ";
                $status = 1;
                $requiredStatus = 0;
            } else if ($laserprocedureRecordpostedID == "") {
                $message = " laserprocedureRecordpostedID is missing ";
                $status = 1;
                $requiredStatus = 0;
            } else {
                $ViewUserNameQry = "select fname,mname,lname,user_type from `users` where  usersId = '" . $userId . "'";
                $ViewUserNameRow = DB::selectone($ViewUserNameQry); // or die($ViewUserNameQry . imw_error());
                $loggedInUserFirstName = $ViewUserNameRow->fname;
                $loggedInUserMiddleName = $ViewUserNameRow->mname;
                $loggedInUserLastName = $ViewUserNameRow->lname;
                $user_type = $ViewUserNameRow->user_type;
                $signOnFileStatus = 'Yes';
                $loggedInUserName = $ViewUserNameRow->lname . ", " . $ViewUserNameRow->fname . " " . $ViewUserNameRow->mname;
                unset($arrayRecord);

                $medicationTime = time();

                $str_sign = "SELECT * FROM laser_procedure_patient_table WHERE confirmation_id='$pConfirmId'";
                $res_sign = DB::selectone($str_sign);
                $nurse_sign_id = $res_sign->signNurseId;
                $surgeon_sign_id = $res_sign->signSurgeon1Id;
                $chk_verified_surgeon_Id = $res_sign->verified_surgeon_Id;
                $chk_verified_surgeon_Name = $res_sign->verified_surgeon_Name;
                $chk_verified_surgeon_date = date('Y-m-d', strtotime($res_sign->verified_surgeon_timeout));
                $chk_proc_start_time = $res_sign->proc_start_time;
                $chk_proc_end_time = $res_sign->proc_end_time;
                $chk_formStatus = $res_sign->form_status;
                // check for form status
                $form_status = "completed";
                //end check fr form status
                $laserprocedureRecordpostedID = $laserprocedureRecordpostedID;
                $arrayRecord['patient_id'] = $patient_id;
                $arrayRecord['confirmation_id'] = $pConfirmId;
                $arrayRecord['saveFromChart'] = 0; //$_POST['saveFromChart'];

                $arrayRecord['allergies_status_reviewed'] = $json->allergiesReviewed; // $_POST["chbx_drug_react_reviewed"];
                $History_data = $json->History_data;
                foreach ($History_data as $History_datas) {
                    $titlekey = $History_datas->titlekey;
                    switch ($titlekey) {
                        case 'laser_chief_complaint':
                            $arrayRecord[$titlekey] = isset($History_datas->desc) ? addslashes($History_datas->desc) : "";
                            $arrayRecord['chk_laser_chief_complaint'] = 'on';
                            break;
                        case 'chk_laser_present_illness_hx':
                            $arrayRecord[$titlekey] = isset($History_datas->desc) ? addslashes($History_datas->desc) : "";
                            $arrayRecord['chk_laser_present_illness_hx'] = 'on';
                            break;
                        case 'laser_past_med_hx':
                            $arrayRecord[$titlekey] = isset($History_datas->desc) ? addslashes($History_datas->desc) : "";
                            $arrayRecord['chk_laser_past_med_hx'] = 'on';
                            break;
                        case 'laser_medication_detail':
                            $arrayRecord['laser_medication'] = isset($History_datas->desc) ? addslashes($History_datas->desc) : "";
                            $arrayRecord['chk_laser_medication'] = 'on';
                            break;
                    }
                }
                $arrayRecord['best_correction_vision_R'] = (isset($json->Best_Corrected_Vision->Best_Corrected_VisionR20) && $json->Best_Corrected_Vision->Best_Corrected_VisionR20 <> "") ? addslashes($json->Best_Corrected_Vision->Best_Corrected_VisionR20) : "";
                $arrayRecord['best_correction_vision_L'] = (isset($json->Best_Corrected_Vision->Best_Corrected_VisionL20) && $json->Best_Corrected_Vision->Best_Corrected_VisionL20 <> "") ? addslashes($json->Best_Corrected_Vision->Best_Corrected_VisionL20) : "";

                $arrayRecord['glare_acuity_R'] = (isset($json->Best_Corrected_Vision->Glare_Acuity_R20) && $json->Best_Corrected_Vision->Glare_Acuity_R20 <> "") ? addslashes($json->Best_Corrected_Vision->Glare_Acuity_R20) : "";
                $arrayRecord['glare_acuity_L'] = (isset($json->Best_Corrected_Vision->Glare_Acuity_L20) && $json->Best_Corrected_Vision->Glare_Acuity_L20 <> "") ? addslashes($json->Best_Corrected_Vision->Glare_Acuity_L20) : "";

                $Medical_Evaluation = $json->Medical_Evaluation->MentalState_FundusExam_SLE;
                foreach ($Medical_Evaluation as $Medical_Evaluations) {
                    $key_name = $Medical_Evaluations->key_name;
                    switch ($key_name) {
                        case 'laser_sle_detail':
                            $arrayRecord['chk_laser_sle'] = "on";
                            $arrayRecord['laser_sle'] = (isset($Medical_Evaluations->$key_name) && $Medical_Evaluations->$key_name <> "") ? addslashes($Medical_Evaluations->$key_name) : "";
                            break;
                        case 'laser_fundus_exam_detail':
                            $arrayRecord['chk_laser_mental_state'] = "on";
                            $arrayRecord['laser_mental_state'] = (isset($Medical_Evaluations->$key_name) && $Medical_Evaluations->$key_name <> "") ? addslashes($Medical_Evaluations->$key_name) : "";
                            break;
                        case 'laser_mental_state_detail':
                            $arrayRecord['chk_laser_fundus_exam'] = "on";
                            $arrayRecord['laser_fundus_exam'] = (isset($Medical_Evaluations->$key_name) && $Medical_Evaluations->$key_name <> "") ? addslashes($Medical_Evaluations->$key_name) : "";
                            break;
                    }
                }

                $laser_notes = $json->laser_notes;
                //  print '<pre>';
                //  print_r($json);//->PreLaserIOP
                //print_r($json->laser_notes->PreLaserIOP);//->PreLaserIOP
                $Post_OP_Order_Progress_Note = $laser_notes->Post_OP_Order_Progress_Note;
                foreach ($Post_OP_Order_Progress_Note as $Post_OP_Order_Progress_Notes) {
                    $title_keys = $Post_OP_Order_Progress_Notes->titlekey;
                    switch ($title_keys) {
                        case 'laser_post_progress' :
                            $arrayRecord['chk_laser_post_progress'] = 'on'; //$_POST['hiddchk_laser_post_progress'];
                            $arrayRecord['laser_post_progress'] = isset($Post_OP_Order_Progress_Notes->$title_keys) ? $Post_OP_Order_Progress_Notes->$title_keys : "";
                            break;
                        case 'laser_post_operative':
                            $arrayRecord['chk_laser_post_operative'] = 'on';
                            $arrayRecord['laser_post_operative'] = isset($Post_OP_Order_Progress_Notes->$title_keys) ? $Post_OP_Order_Progress_Notes->$title_keys : "";
                            break;
                    }
                }
                $arrayRecord['chk_laser_patient_evaluated'] = $laser_notes->Patient_in_satisfactory;
                //Laser_Notes_for_left_eye
                $Laser_Notes_for_left_eye = $laser_notes->Laser_Notes_for_left_eye;
                foreach ($Laser_Notes_for_left_eye as $Laser_Notes_for_left_eyes) {
                    $title_key = $Laser_Notes_for_left_eyes->title_key;
                    switch ($title_key) {
                        case 'laser_spot_duration_detail':
                            $arrayRecord['chk_laser_spot_duration'] = 'on';
                            $arrayRecord['laser_spot_duration'] = isset($Laser_Notes_for_left_eye->$title_key) ? $Laser_Notes_for_left_eye->$title_key : "";
                            break;
                        case 'laser_spot_size_detail':
                            $arrayRecord['chk_laser_spot_duration'] = 'on';
                            $arrayRecord['laser_spot_duration'] = isset($Laser_Notes_for_left_eye->$title_key) ? $Laser_Notes_for_left_eye->$title_key : "";
                            break;
                        case 'laser_power_detail':
                            $arrayRecord['chk_laser_power'] = 'on';
                            $arrayRecord['laser_power'] = isset($Laser_Notes_for_left_eye->$title_key) ? $Laser_Notes_for_left_eye->$title_key : "";
                            break;
                        case 'laser_shots_detail':
                            $arrayRecord['chk_laser_shots'] = 'on';
                            $arrayRecord['laser_shots'] = isset($Laser_Notes_for_left_eye->$title_key) ? $Laser_Notes_for_left_eye->$title_key : "";
                            break;
                        case 'laser_total_energy_detail':
                            $arrayRecord['chk_laser_total_energy'] = 'on';
                            $arrayRecord['laser_total_energy'] = isset($Laser_Notes_for_left_eye->$title_key) ? $Laser_Notes_for_left_eye->$title_key : "";
                            break;
                        case 'laser_degree_of_opening_detail':
                            $arrayRecord['chk_laser_degree_of_opening'] = 'on';
                            $arrayRecord['laser_degree_of_opening'] = isset($Laser_Notes_for_left_eye->$title_key) ? $Laser_Notes_for_left_eye->$title_key : "";
                            break;
                        case 'laser_exposure_detail':
                            $arrayRecord['chk_laser_exposure'] = 'on';
                            $arrayRecord['laser_exposure'] = isset($Laser_Notes_for_left_eye->$title_key) ? $Laser_Notes_for_left_eye->$title_key : "";
                            break;
                        case 'laser_count_detail':
                            $arrayRecord['chk_laser_count'] = 'on';
                            $arrayRecord['laser_count'] = isset($Laser_Notes_for_left_eye->$title_key) ? $Laser_Notes_for_left_eye->$title_key : "";
                            break;
                        case 'laser_post_operative':
                            $arrayRecord['chk_laser_post_operative'] = 'on';
                            $arrayRecord['laser_post_operative'] = isset($Laser_Notes_for_left_eye->$title_key) ? $Laser_Notes_for_left_eye->$title_key : "";
                            break;
                    }
                }

                //Laser_Notes_for_left_eye
                $PreLaserIOP = $json->PreLaser_IOP;

                $arrayRecord['pre_laser_IOP_R'] = $PreLaserIOP->R;
                $arrayRecord['pre_laser_IOP_L'] = $PreLaserIOP->L;
                $arrayRecord['discharge_home'] = $json->Relationship->PatientDischarge_to_home;
                $arrayRecord['discharge_time'] = $json->Relationship->Discharge_time;
                $arrayRecord['patient_transfer'] = $json->Relationship->Patienttransfered_to_hospital;
                $arrayRecord['patients_relation_other'] = $json->Relationship->other;
                //$pre_vital_signs = $json->PreLaserVitalSigns;
                //print_r($pre_vital_signs);
                $arrayRecord['prelaserVitalSignBP'] = isset($json->Relationship->PreLaserSigns->BP) ? addslashes($json->Relationship->PreLaserSigns->BP) : "";
                $arrayRecord['prelaserVitalSignP'] = isset($json->Relationship->PreLaserSigns->P) ? addslashes($json->Relationship->PreLaserSigns->P) : "";
                $arrayRecord['prelaserVitalSignR'] = isset($json->Relationship->PreLaserSigns->R) ? addslashes($json->Relationship->PreLaserSigns->R) : "";
                if (isset($json->Relationship->PreLaserSigns->Times) && $json->Relationship->PreLaserSigns->Times <> "") {
                    $arrayRecord['prelaserVitalSignTime'] = date('Y-m-d') . ' ' . $this->setTmFormat($json->Relationship->PreLaserSigns->Times);
                }
                $IOP_Pressure = $json->IOP_Pressure;
                $arrayRecord['iop_pressure_l'] = isset($IOP_Pressure->L) ? addslashes($IOP_Pressure->L) : "";
                $arrayRecord['iop_pressure_r'] = isset($IOP_Pressure->R) ? addslashes($IOP_Pressure->R) : "";
                $arrayRecord['iop_na'] = isset($IOP_Pressure->N_A) ? addslashes($IOP_Pressure->N_A) : "";

                $arrayRecord['post_op_operative_comment'] = isset($IOP_Pressure->Comments) ? addslashes($IOP_Pressure->Comments) : ""; // addslashes($_POST['post_op_comments']);

                $arrayRecord['laser_os'] = ''; // addslashes($_POST['laser_od']);
                $arrayRecord['laser_od'] = ''; // addslashes($_POST['laser_os']);

                $arrayRecord['laser_anesthesia'] = ''; // addslashes($_POST['txtarea_anesthesia']);
                $postal_vital_signs = $json->Post_Vital_Signs_Laser;
                $arrayRecord['postlaserVitalSignBP'] = isset($postal_vital_signs->BP) ? addslashes($postal_vital_signs->BP) : "";
                $arrayRecord['postlaserVitalSignP'] = isset($postal_vital_signs->P) ? addslashes($postal_vital_signs->P) : "";
                $arrayRecord['postlaserVitalSignR'] = isset($postal_vital_signs->R) ? addslashes($postal_vital_signs->R) : "";
                if ($postal_vital_signs->Time <> "") {
                    $arrayRecord['postlaserVitalSignTime'] = date('Y-m-d') . ' ' . $this->setTmFormat($postal_vital_signs->Time);
                }
                $arrayRecord['pre_iop_na'] = isset($PreLaserIOP->N_A) ? addslashes($PreLaserIOP->N_A) : "";
                $arrayRecord['stable_chbx'] = (isset($json->Best_Corrected_Vision->Patient_reported_sts_Stable) && $json->Best_Corrected_Vision->Patient_reported_sts_Stable <> "") ? ($json->Best_Corrected_Vision->Patient_reported_sts_Stable) : "";
                $arrayRecord['stable_other_chbx'] = (isset($json->Best_Corrected_Vision->Other) && $json->Best_Corrected_Vision->Other <> "") ? ($json->Best_Corrected_Vision->Other) : ""; //addslashes($_POST['stableChkBoxOther']);
                $arrayRecord['stable_other_txtbx'] = "";
                if ($json->Best_Corrected_Vision->med_other_sts == 'Yes') {
                    $arrayRecord['stable_other_txtbx'] = (isset($json->Best_Corrected_Vision->Patient_reported_sts_Stable) && $json->Best_Corrected_Vision->Patient_reported_sts_Stable <> "") ? ($json->Best_Corrected_Vision->Patient_reported_sts_Stable) : ""; //addslashes($_POST['stableChkBoxOther']); addslashes($_POST['stableTxtBoxOther']);
                }

                $arrayRecord['laser_comments'] = isset($json->OtherPreopOrders_PreOPDiagnosis->otherPreop_comments) ? addslashes($json->OtherPreopOrders_PreOPDiagnosis->otherPreop_comments) : "";
                $arrayRecord['laser_other'] = ''; //isset($json->OtherPreopOrders_PreOPDiagnosis->otherPreop_comments)? addslashes($json->OtherPreopOrders_PreOPDiagnosis->otherPreop_comments):"";// addslashes($_POST['txtarea_other']);

                $arrayRecord['chk_laser_pre_op_diagnosis'] = isset($json->OtherPreopOrders_PreOPDiagnois->data[1]->laser_pre_op_diagnosis) ? 'on' : '';
                $arrayRecord['pre_op_diagnosis'] = isset($json->OtherPreopOrders_PreOPDiagnois->data[1]->laser_pre_op_diagnosis) ? addslashes($json->OtherPreopOrders_PreOPDiagnois->data[1]->laser_pre_op_diagnosis) : "";

                $arrayRecord['laser_other_pre_medication'] = isset($json->OtherPreopOrders_PreOPDiagnois->data[0]->laser_other_pre_medication) ? addslashes($json->OtherPreopOrders_PreOPDiagnois->data[0]->laser_other_pre_medication) : ""; // $_POST['otherPreOpOrders'];


                $arrayRecord['laser_procedure_notes'] = ''; // addslashes($_POST['txtarea_laser_procedure_notes']);



                $arrayRecord['laser_medical_evaluation'] = ''; // addslashes($_POST['laser_medical_evaluation']);




                $arrayRecord['medicationStartTime'] = ''; // addslashes($_REQUEST['startTimeVal'][0]);

                $arrayRecord['laserprocedurePhysicianOrdersTime'] = $medicationTime;

                $arrayRecord['form_status'] = $form_status;

                $arrayRecord['prefilMedicationStatus'] = 'true'; // $_POST['hidd_prefilMedicationStatus'];

                $Timeout = $json->Timeout;
                if ($Timeout) {
                    $arrayRecord['verified_nurse_Id'] = isset($json->Timeout->verified_nurse_Id) ? $json->Timeout->verified_nurse_Id : 0;
                    $arrayRecord['verified_nurse_name'] = isset($json->Timeout->verified_nurse_name) ? addslashes($json->Timeout->verified_nurse_name) : "";
                    if ($json->Timeout->Patient_IdentificationTime <> "") {
                        //$arrayRecord['verified_nurse_timeout'] = date('Y-m-d H:i:s',strtotime($_REQUEST['verified_nurse_timeout']));
                        $arrayRecord['verified_nurse_timeout'] = date('Y-m-d') . ' ' . $this->setTmFormat($json->Timeout->Patient_IdentificationTime);
                    }
                    if ($json->Timeout->Patient_Verified_Time <> "") {
                        $verified_surgeon_timeout = $this->setTmFormat($json->Timeout->Patient_Verified_Time);
                        //$verified_surgeon_timeout = date('Y-m-d H:i:s',strtotime($chk_verified_surgeon_date. ' '. $verified_surgeon_timeout));
                        $verified_surgeon_timeout = date('Y-m-d', strtotime($chk_verified_surgeon_date)) . ' ' . $verified_surgeon_timeout;
                        if ($json->Timeout->verified_surgeon_Id <= 0) {
                            $verified_surgeon_timeout = '';
                        }
                        if ($verified_surgeon_timeout)
                            $arrayRecord['verified_surgeon_timeout'] = $verified_surgeon_timeout;
                    }
                }

                $arrayRecord['asa_status'] = isset($json->Best_Corrected_Vision->ASA) ? $json->Best_Corrected_Vision->ASA : "";


                $surgery_start_time = (isset($json->Timeout->Sugery_Start_Time) && $json->Timeout->Sugery_Start_Time <> "") ? $json->Timeout->Sugery_Start_Time : "";
                if ($surgery_start_time <> "") {
                    $arrayRecord['proc_start_time'] = date('Y-m-d') . ' ' . $this->setTmFormat($surgery_start_time);
                }

                $surgery_end_time = (isset($json->Timeout->Sugery_End_Time) && $json->Timeout->Sugery_End_Time <> "") ? $json->Timeout->Sugery_End_Time : "";
                if ($surgery_end_time <> "") {
                    $arrayRecord['proc_end_time'] = date('Y-m-d') . ' ' . $this->setTmFormat($surgery_end_time);
                }

                // Check if surgery start time or surgery end time field values changed
                $start_time_status = '0';
                /*
                  if(	($arrayRecord['proc_start_time'] && date('H:i:s', strtotime($arrayRecord['proc_start_time'])) <> date('H:i:s', strtotime($chk_proc_start_time)) )
                  || ($arrayRecord['proc_end_time'] && date('H:i:s', strtotime($arrayRecord['proc_end_time'])) <> date('H:i:s', strtotime($chk_proc_end_time)))
                  ) */
                if (($surgery_start_time && $this->setTmFormat($surgery_start_time) <> $this->getTmFormat($chk_proc_start_time) ) || ($surgery_end_time && $this->setTmFormat($surgery_end_time) <> $this->getTmFormat($chk_proc_end_time) )
                ) {
                    $start_time_status = '1';
                }

                $arrayRecord['start_time_status'] = $start_time_status;

                /* $arrayRecord['chk_laser_procedure_image'] = $_POST['hiddchk_laser_procedure_image'];
                  if ($_POST['hiddchk_laser_procedure_image'] == 'on') {
                  if ($isHTML5OK) {
                  $arrImagesNew = merge_images($_REQUEST, $pConfId, $patient_id);
                  $arrayRecord['laser_procedure_image_path'] = $arrImagesNew;
                  } else {
                  $arrayRecord['laser_procedure_image'] = addslashes($_POST['laserProcedure_image']);
                  }
                  } */
                $arrayRecord['laser_procedure_image_path'] = $this->convertBase64_Laser($app_Laser_Image, $laserprocedureRecordpostedID, $patient_id, $pConfirmId);
                $arrayRecord['sign_all_pre_op_order_status'] = '1';
                $arrayRecord['sign_all_post_op_order_status'] = '1';
                //$arrayRecord['surgeonSign'] = $_REQUEST['elem_signature1'];
                /*                 * pdfFiles/laser_drawing_images/laser_image_57_6_20180130153506.jpg
                 */
                if ($laserprocedureRecordpostedID <= 0) {
                    $laserprocedureRecordpostedID = DB::table('laser_procedure_patient_table')->insertGetId($arrayRecord); //$this->addRecords($arrayRecord, 'laser_procedure_patient_table');
                } else {
                    DB::table('laser_procedure_patient_table')->where('laser_procedureRecordID', $laserprocedureRecordpostedID)->update($arrayRecord);
                    //$this->updateRecords($arrayRecord, 'laser_procedure_patient_table', 'laser_procedureRecordID', $laserprocedureRecordpostedID);
                }

                $Allergies_data = isset($json->Allergy_data) ? $json->Allergy_data : [];
                if (!empty($Allergies_data)) {
                    $this->patient_allergy_save($Allergies_data, $pConfirmId, $patient_id, $userId, $loggedInUserName);
                }
                //$Medication_data = isset($json->MedicationOrder) ? $json->MedicationOrder : [];
                if (!empty($Medication_data)) {
                    //$this->patient_medication_save($Medication_data, $pConfirmId, $patient_id, $userId, $loggedInUserName);
                }

                $List_of_PreOP_Medication_Orders = $json->MedicationOrder;
                if (!empty($List_of_PreOP_Medication_Orders)) {
                    $this->patient_pre_op_medication_save($List_of_PreOP_Medication_Orders, $pConfirmId, $patient_id);
                }
                //MAKE AUDIT STATUS
                $chkAuditChartNotesQry = "select * from `chartnotes_change_audit_tbl` where 
                                                user_id='" . $userId . "' AND
                                                patient_id='" . $patient_id . "' AND
                                                confirmation_id='" . $pConfirmId . "' AND
                                                form_name='laser_procedure_form' AND
                                                status = 'created'";

                $chkAuditChartNotesRes = DB::select($chkAuditChartNotesQry); // or die(imw_error());
                if ($chkAuditChartNotesRes) {
                    $SaveAuditChartNotesQry = "insert into `chartnotes_change_audit_tbl` set 
                                                    user_id='" . $userId . "',
                                                    patient_id='" . $patient_id . "',
                                                    confirmation_id='" . $pConfirmId . "',
                                                    form_name='laser_procedure_form',
                                                    status='modified',
                                                    action_date_time='" . date("Y-m-d H:i:s") . "'";
                } else {
                    $SaveAuditChartNotesQry = "insert into `chartnotes_change_audit_tbl` set 
                                                                                            user_id='" . $userId . "',
                                                                                            patient_id='" . $patient_id . "',
                                                                                            confirmation_id='" . $pConfirmId . "',
                                                                                            form_name='laser_procedure_form',
                                                                                            status='created',
                                                                                            action_date_time='" . date("Y-m-d H:i:s") . "'";
                }
                $SaveAuditChartNotesRes = DB::select($SaveAuditChartNotesQry); // or die(imw_error());
                //CODE TO CHECK SURGEON ALL SIGNATURE AND SET VALUE IN STUB TABLE
                $chartSignedBySurgeon = $this->chkSurgeonSignNew($pConfirmId);
                $updateStubTblQry = "UPDATE stub_tbl SET chartSignedBySurgeon='" . $chartSignedBySurgeon . "' WHERE patient_confirmation_id='" . $pConfirmId . "'";
                $updateStubTblRes = DB::select($updateStubTblQry); // or die(imw_error());
                //END CODE TO CHECK SURGEON SIGNATURE AND SET VALUE IN STUB TABLE
                //CODE TO CHECK NURSE ALL SIGNATURE AND SET VALUE IN STUB TABLE
                $chartSignedByNurse = $this->chkNurseSignNew($pConfirmId);
                $updateNurseStubTblQry = "UPDATE stub_tbl SET chartSignedByNurse='" . $chartSignedByNurse . "' WHERE patient_confirmation_id='" . $pConfirmId . "'";
                $updateNurseStubTblRes = DB::select($updateNurseStubTblQry); // or die(imw_error());
                //END CODE TO CHECK NURSE SIGNATURE AND SET VALUE IN STUB TABLE
                //End Add or update full record
                //ALLERGIES
                $allergies_status_reviewed = $json->allergiesReviewed;
                //END ALLERGIES
                //PRE OP ORDERS
                if ($allergies_status_reviewed == 'Yes') {
                    DB::select("delete from patient_allergies_tbl where patient_confirmation_id = '$pConfirmId'");
                }
                $updateNKDAstatusQry = "update patientconfirmation set allergiesNKDA_status = '" . $json->allergiesNKDA_patientconfirmation_status . "' where patientConfirmationId = '$pConfirmId'";
                $updateNKDAstatusRes = DB::select($updateNKDAstatusQry);
                $savedStatus = 1;
                $status = 1;
                $message = " Saved successfully !";
            }
        }
        return response()->json([
                    'status' => $status,
                    'savedStatus' => $savedStatus,
                    'message' => $message,
                    'data' => $data,
                    'requiredStatus' => '',
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

    public function LaserPreOPMedication_delete(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $delId = $request->json()->get('delId') ? $request->json()->get('delId') : $request->input('delId');
        $data = [];
        $status = 0;
        $delStatus = 0;
        $message = " unauthorized ";
        $requiredStatus = [];
        $userId = $this->checkToken($userToken);
        $consent_content = '';
        $savedStatus = 0;
        if ($userId > 0) {
            if ($delId == "") {
                $message = " vitalsign idPrimary is missing ";
                $status = 1;
                $requiredStatus = 0;
            } else {
                $message = " Record deleted successfully ! ";
                $delStatus = 1;
                $status = 1;
                DB::table('patientpreopmedication_tbl')->where('patientPreOpMediId', $delId)->delete();
            }
        }
        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => $requiredStatus,
                    'delStatus' => $delStatus,
                    'data' => $data,]); // NOT_FOUND (404) being the HTTP response code 
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
                    DB::table('patient_allergies_tbl')->where('pre_op_allergy_id', $allergiesArrValue->pre_op_allergy_id)->delete();
                }
            }
        }
    }

    public function patient_pre_op_medication_save($PreOPMedication_data, $pConfirmId, $patient_id) {
        if (!empty($PreOPMedication_data)) {
            foreach ($PreOPMedication_data as $medications) {
                if ($medications->Medication != '') {
                    $medicationsArr['patient_confirmation_id'] = $pConfirmId;
                    $medicationsArr['preOpPhyOrderId'] = addslashes($medications->preOpPhyOrderId);
                    $medicationsArr['medicationName'] = addslashes($medications->Medication);
                    $medicationsArr['strength'] = addslashes($medications->Strength);
                    $medicationsArr['direction'] = addslashes($medications->Direction);
                    $medicationsArr['timemeds'] = isset($medications->timemeds) ? $medications->timemeds : "";
                    $medicationsArr['timemeds1'] = isset($medications->timemeds1) ? $medications->timemeds1 : "";
                    $medicationsArr['timemeds2'] = isset($medications->timemeds2) ? $medications->timemeds2 : "";
                    $medicationsArr['timemeds3'] = isset($medications->timemeds3) ? $medications->timemeds3 : "";
                    $medicationsArr['timemeds4'] = isset($medications->timemeds4) ? $medications->timemeds4 : "";
                    $medicationsArr['timemeds5'] = isset($medications->timemeds5) ? $medications->timemeds5 : "";
                    $medicationsArr['timemeds6'] = isset($medications->timemeds6) ? $medications->timemeds6 : "";
                    $medicationsArr['timemeds7'] = isset($medications->timemeds7) ? $medications->timemeds7 : "";
                    $medicationsArr['timemeds8'] = isset($medications->timemeds8) ? $medications->timemeds8 : "";
                    $medicationsArr['timemeds9'] = isset($medications->timemeds9) ? $medications->timemeds9 : "";
                    if ($medications->patientPreOpMediId > 0) {
                        DB:: table('patientpreopmedication_tbl')->where('patientPreOpMediId', $medications->patientPreOpMediId)->update($medicationsArr);
                    } else {
                        DB::table('patientpreopmedication_tbl')->insert($medicationsArr);
                    }
                }
            }
        }
    }

    function convertBase64_Laser($imagesrc, $instructionSheetId, $patient_id, $pConfirmId) {
        $imagesrc = str_ireplace(' ', '+', $imagesrc);
        $imageName = $instructionSheetId . "_" . $patient_id . "_" . $pConfirmId . '.' . 'jpg';
        if (trim($imagesrc) <> "") {
            $newfile = explode(",", $imagesrc);
            if (!file_exists('../../SigPlus_images/PatientId_' . $patient_id)) {
                mkdir('../../SigPlus_images/PatientId_' . $patient_id, 0777);
            }
            if (!file_exists('../../SigPlus_images/PatientId_' . $patient_id . "/sign/")) {
                mkdir('../../SigPlus_images/PatientId_' . $patient_id . "/sign/", 0777);
            }
            if (!file_exists('../../SigPlus_images/PatientId_' . $patient_id . "/laser_drawing_images/")) {
                mkdir('../../SigPlus_images/PatientId_' . $patient_id . "/laser_drawing_images/", 0777);
            }
            $success = @file_put_contents('../../SigPlus_images/PatientId_' . $patient_id . "/laser_drawing_images/" . $imageName, base64_decode($newfile[1]));

            if ($success) {
                return 'PatientId_' . $patient_id . "/laser_drawing_images/" . $imageName;
            }
        }
        return '';
    }

}
