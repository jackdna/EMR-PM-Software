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

class HistoryPhysicalClearanceController extends Controller {

    public function history_physicial_clearance(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $loginUserType = $request->json()->get('UserType') ? $request->json()->get('UserType') : $request->input('UserType');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $history_physicial_id = $request->json()->get('history_physicial_id') ? $request->json()->get('history_physicial_id') : $request->input('history_physicial_id');
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
                if ($history_physicial_id > 0) {
                    $res = DB::selectone("select *,if(date_format(date_of_h_p ,'%m-%d-%Y')='00-00-0000','',date_format(date_of_h_p ,'%m-%d-%Y')) as date_of_h_p_format, date_format(signNurseDateTime,'%m-%d-%Y %h:%i %p') as signNurseDateTimeFormat  from history_physicial_clearance where history_physicial_id='" . $history_physicial_id . "'");
                } else if ($pConfirmId) {
                    $res = DB::selectone("select *,if(date_format(date_of_h_p ,'%m-%d-%Y')='00-00-0000','',date_format(date_of_h_p ,'%m-%d-%Y')) as date_of_h_p_format, date_format(signNurseDateTime,'%m-%d-%Y %h:%i %p') as signNurseDateTimeFormat  from history_physicial_clearance where confirmation_id='" . $pConfirmId . "'");
                }

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
                $High_Cholesterol = '';
                $Thyroid = '';
                $visible1 = 0;
                $visible = 1;
                $visible2 = 0;
                $visible3 = 0;
                $str = '';
                $str2 = '';
                $str3 = '';
                if ($res->version_num > 3) {
                    $visible1 = 1;
                    $str = 'High_Cholesterol,Thyroid,Ulcers,';
                }
                if ($res->version_num > 2) {
                    $visible2 = 1;
                    $str2 = 'Discussed_Advanced_Directives_and_Patient_Rights_and_Responsibilities,';
                }
                if ($res->version_num > 1) {
                    $visible3 = 1;
                    $str3 = 'Heart_Exam_done_with_stethoscope_Normal,Lung_Exam_done_with_stethoscope_Normal';
                }
                $data = [
                    "history_left_data" => [
                        "sample_keys" => "CAD_MIN_W_WO_Stent_OR_CABG_PVD,CVA_TIA_Epilepsy_Neurological,HTN_CP_SOB_on_Exertion,Anticoagulation_therapy,Respiratory_Asthma_COPD_Sleep_Apnea,Arthritis,Diabetes,Recreational_Drug_Use,GI_GERD_PUD_Liver_Disease_Hepatitis,Ocular,Kidney_Disease_Dialysis_G_U,HIV_Autoimmune_Diseases_Contagious_Diseases,History_of_Cancer,Organ_Transplant,A_Bad_Reaction_to_Local_or_General_Anesthesia, $str Other,$str2 $str3",
                        'CAD_MIN_W_WO_Stent_OR_CABG_PVD' => ['yes_no_sts' => $res->cadMI, 'date' => '', "text_note" => stripslashes($res->cadMIDesc), 'box_status' => 1, 'title' => 'CAD/MIN(W/ WO Stent OR CABG)/PVD)', 'visible' => $visible, 'rightstatus' => 0],
                        'CVA_TIA_Epilepsy_Neurological' => ['yes_no_sts' => $res->cvaTIA, 'date' => '', "text_note" => stripslashes($res->cvaTIADesc), 'box_status' => 1, 'title' => 'CVA/TIA/ Epilepsy, Neurological', 'visible' => $visible, 'rightstatus' => 0],
                        'HTN_CP_SOB_on_Exertion' => ['yes_no_sts' => $res->htnCP, 'date' => '', "text_note" => stripslashes($res->htnCPDesc), 'box_status' => 1, 'title' => 'HTN/ +/- CP/SOB on Exertion', 'visible' => $visible, 'rightstatus' => 0],
                        'Anticoagulation_therapy' => ['yes_no_sts' => $res->anticoagulationTherapy, 'date' => '', "text_note" => stripslashes($res->anticoagulationTherapyDesc), 'box_status' => 1, 'title' => 'Anticoagulation therapy (i.e. Blood Thinners)', 'visible' => $visible, 'rightstatus' => 0],
                        'Respiratory_Asthma_COPD_Sleep_Apnea' => ['yes_no_sts' => $res->respiratoryAsthma, 'date' => '', "text_note" => stripslashes($res->respiratoryAsthmaDesc), 'box_status' => 1, 'title' => 'Respiratory - Asthma / COPD / Sleep Apnea', 'visible' => $visible, 'rightstatus' => 0],
                        'Arthritis' => ['yes_no_sts' => $res->arthritis, 'date' => '', "text_note" => stripslashes($res->arthritisDesc), 'box_status' => 1, 'title' => 'Arthritis', 'visible' => $visible, 'rightstatus' => 0],
                        'Diabetes' => ['yes_no_sts' => $res->diabetes, 'date' => '', "text_note" => stripslashes($res->diabetesDesc), 'box_status' => 1, 'title' => 'Diabetes', 'visible' => $visible, 'rightstatus' => 0],
                        'Recreational_Drug_Use' => ['yes_no_sts' => $res->recreationalDrug, 'date' => '', "text_note" => stripslashes($res->recreationalDrugDesc), 'box_status' => 1, 'title' => 'Recreational Drug Use', 'visible' => $visible, 'rightstatus' => 0],
                        'GI_GERD_PUD_Liver_Disease_Hepatitis' => ['yes_no_sts' => $res->giGerd, 'date' => '', "text_note" => stripslashes($res->giGerdDesc), 'box_status' => 1, 'title' => 'GI - GERD / PUD / Liver Disease / Hepatitis', 'visible' => $visible, 'rightstatus' => 0],
                        'Ocular' => ['yes_no_sts' => $res->ocular, "text_note" => stripslashes($res->ocularDesc), 'date' => '', 'box_status' => 1, 'title' => 'Ocular', 'visible' => $visible, 'rightstatus' => 0],
                        'Kidney_Disease_Dialysis_G_U' => ['yes_no_sts' => $res->kidneyDisease, "text_note" => stripslashes($res->kidneyDiseaseDesc), 'date' => '', 'box_status' => 1, 'title' => 'Kidney Disease, Dialysis, G-U', 'visible' => $visible, 'rightstatus' => 0],
                        'HIV_Autoimmune_Diseases_Contagious_Diseases' => ['yes_no_sts' => $res->hivAutoimmune, "text_note" => stripslashes($res->hivAutoimmuneDesc), 'date' => '', 'box_status' => 1, 'title' => 'HIV, Autoimmune Diseases, Contagious Diseases', 'visible' => $visible, 'rightstatus' => 0],
                        'History_of_Cancer' => ['yes_no_sts' => $res->historyCancer, "text_note" => stripslashes($res->historyCancerDesc), 'date' => '', 'box_status' => 1, 'title' => 'History of Cancer', 'visible' => $visible, 'rightstatus' => 0],
                        'Organ_Transplant' => ['yes_no_sts' => $res->organTransplant, "text_note" => stripslashes($res->organTransplantDesc), 'date' => '', 'box_status' => 1, 'title' => 'Organ Transplant', 'visible' => $visible, 'rightstatus' => 0],
                        'A_Bad_Reaction_to_Local_or_General_Anesthesia' => ['yes_no_sts' => $res->badReaction, "text_note" => stripslashes($res->badReactionDesc), 'date' => '', 'box_status' => 1, 'title' => 'A Bad Reaction to Local or General Anesthesia', 'visible' => $visible, 'rightstatus' => 0],
                        "High_Cholesterol" => ['yes_no_sts' => $res->highCholesterol, "text_note" => stripslashes($res->highCholesterolDesc), 'date' => '', 'box_status' => 1, 'title' => 'High Cholesterol', 'visible' => $visible1, 'rightstatus' => 0],
                        'Thyroid' => ['yes_no_sts' => $res->thyroid, "text_note" => stripslashes($res->thyroidDesc), 'date' => '', 'box_status' => 1, 'title' => 'Thyroid', 'visible' => $visible1, 'rightstatus' => 0],
                        'Ulcers' => ['yes_no_sts' => $res->ulcer, "text_note" => stripslashes($res->ulcerDesc), 'date' => '', 'box_status' => 1, 'title' => 'Ulcers', 'visible' => $visible1, 'rightstatus' => 0],
                        "Other" => ['yes_no_sts' => '', "text_note" => stripslashes($res->otherHistoryPhysical), 'date' => '', 'box_status' => 1, 'title' => 'Other', 'visible' => $visible, 'rightstatus' => 0],
                        "Heart_Exam_done_with_stethoscope_Normal" => ['yes_no_sts' => $res->heartExam, "text_note" => stripslashes($res->heartExamDesc), 'date' => '', 'box_status' => 1, 'title' => 'Heart Exam done with stethoscope - Normal', 'visible' => $visible3, 'rightstatus' => 1],
                        "Lung_Exam_done_with_stethoscope_Normal" => ['yes_no_sts' => $res->lungExam, "text_note" => stripslashes($res->lungExamDesc), 'date' => '', 'box_status' => 1, "title" => "Lung Exam done with stethoscope - Normal", 'visible' => $visible3, 'rightstatus' => 1],
                        "Discussed_Advanced_Directives_and_Patient_Rights_and_Responsibilities" => ['yes_no_sts' => $res->discussedAdvancedDirective, 'date' => '', "text_note" => '', 'box_status' => 0, "title" => "Discussed Advanced Directives and Patient Rights and Responsibilities", 'visible' => $visible2, 'rightstatus' => 0],
                    ],
                    "AlternativeQuestion" => $this->Alternativequestioner($pConfirmId, $res->form_status),
                    "Allergy_data" => ['dropdown' => $allergic, 'patientAllergiesGrid' => $patientAllergies],
                    "Medications_data" => ['dropdown' => $medication, 'patient_prescriptions' => $patient_prescriptions],
                    "Day_Of_Surgery_Notes" => [
                        "Date_of_Last_Menstrual_Cycle" => ['yes_no_sts' => '', "text_note" => '', 'date' => $res->date_of_h_p_format, 'box_status' => 0, "title" => "Date of Last Menstrual Cycle", 'visible' => $visible, 'rightstatus' => 0],
                        "Wear_Contact_Lenses" => ['yes_no_sts' => $res->wearContactLenses, 'date' => '', "text_note" => stripslashes($res->wearContactLensesDesc), 'box_status' => 1, "title" => "Wear Contact Lenses", 'visible' => $visible, 'rightstatus' => 0],
                        "Smoking" => ['yes_no_sts' => $res->smoking, 'date' => '', "text_note" => stripslashes($res->smokingDesc), 'box_status' => 1, "title" => "Smoking", 'visible' => $visible, 'rightstatus' => 0],
                        "Drink_Alcohol" => ['yes_no_sts' => $res->drinkAlcohal, 'date' => '', "text_note" => stripslashes($res->drinkAlcohalDesc), 'box_status' => 1, "title" => "Drink Alcohol", 'visible' => $visible, 'rightstatus' => 0],
                        "Have_an_automatic_internal_defibrillator" => ['yes_no_sts' => $res->haveAutomatic, 'date' => '', "text_note" => stripslashes($res->haveAutomaticDesc), 'box_status' => 0, "title" => "Have an automatic internal defibrillator", 'visible' => $visible, 'rightstatus' => 0],
                        "Medical_History_obtained_from" => ['yes_no_sts' => $res->medicalHistoryObtained, 'date' => '', "text_note" => stripslashes($res->medicalHistoryObtainedDesc), 'box_status' => 1, "title" => "Medical History obtained from", 'visible' => $visible, 'rightstatus' => 0],
                        "Notes" => ['yes_no_sts' => '', "text_note" => stripslashes($res->otherNotes), 'date' => '', 'box_status' => 1, "title" => "Notes", 'visible' => $visible, 'rightstatus' => 0]
                    ],
                   
                    "signature" => [
                        "surgeon_signature" => ["name" => $res->signSurgeon1FirstName . " " . $res->signSurgeon1LastName, "signed_status" => $res->signSurgeon1Status, "sign_date" => $res->signSurgeon1DateTime != '0000-00-00 00:00:00' ? date("m-d-Y h:i A", strtotime($res->signSurgeon1DateTime)) : date("m-d-Y h:i A")],
                        "aneshesia_signature" => ["name" => $res->signAnesthesia1FirstName . " " . $res->signAnesthesia1LastName, "signed_status" => $res->signAnesthesia1Status, "sign_date" => $res->signAnesthesia1DateTime != '0000-00-00 00:00:00' ? date("m-d-Y h:i A", strtotime($res->signAnesthesia1DateTime)) : date("m-d-Y h:i A")],
                        "nurse_signature" => ["name" => $res->signNurseFirstName . " " . $res->signNurseLastName, "signed_status" => $res->signNurseStatus, "sign_date" => $res->signNurseDateTime != '0000-00-00 00:00:00' ? date("m-d-Y h:i A", strtotime($res->signNurseDateTime)) : date("m-d-Y h:i A")]
                    ]
                ];
                $status = 1;
                $message = " H & P Clearance ";
            }
        }
        return response()->json(['status' => $status, 'message' => $message, 'requiredStatus' => '', 'data' => $data,
                    'history_physicial_id' => isset($res->history_physicial_id) ? $res->history_physicial_id : 0,
                        "version_num" => $res->version_num,
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
                    $data[] = ['QuestionId' => $Questions->id, 'Question' => $Questions->name, "text_note" => '', 'yes_no_sts' => '',];
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

    public function history_physicial_clearance_save(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $loginUserType = $request->json()->get('UserType') ? $request->json()->get('UserType') : $request->input('UserType');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $history_physicial_id = $request->json()->get('history_physicial_id') ? $request->json()->get('history_physicial_id') : $request->input('history_physicial_id');
        $version_num = $request->json()->get('version_num') ? $request->json()->get('version_num') : $request->input('version_num');
        $json = $request->json()->get('jsondata') ? $request->json()->get('jsondata') : $request->input('jsondata');
        $jsondata = json_decode($json);
        //print '<pre>';
        //print_r($json);
        //print_r($jsondata->history_left_data);
        $data = [];
        $status = 0;
        $moveStatus = 0;
        $arrayRecord = [];
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
        $arrayRecord = [];
        $arrayStatusRecord = [];
        $surgery_consent_id = 0;
        $savedStatus = 0;
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
            } else if ($patient_id == "") {
                $status = 1;
                $message = " PatientId is missing ";
            } else {
                //GETTING CONFIRNATION DETAILS
                $Confirm_patientPrimProc = "";
                $Confirm_patientPrimaryProcedureId = 0;
                $getConfirmationDetails = DB::selectone("select surgeonId,surgeon_name,patient_primary_procedure,patient_primary_procedure_id from patientconfirmation where patientConfirmationId='" . $pConfirmId . "'");
                if ($getConfirmationDetails) {
                    $hpAssignedSurgeonId = $getConfirmationDetails->surgeonId;
                    $hpAssignedSurgeonName = stripslashes($getConfirmationDetails->surgeon_name);
                    $Confirm_patientPrimProc = stripslashes($getConfirmationDetails->patient_primary_procedure);
                    $Confirm_patientPrimaryProcedureId = $getConfirmationDetails->patient_primary_procedure_id;
                }
                $primary_procedureQry = "SELECT * FROM procedures WHERE name = '" . addslashes($Confirm_patientPrimProc) . "' OR procedureAlias='" . addslashes($Confirm_patientPrimProc) . "'";
                $primary_procedureRow = DB::selectone($primary_procedureQry);
                if (!$primary_procedureRow) {
                    $primary_procedureQry = "SELECT * FROM procedures WHERE procedureId = '" . $Confirm_patientPrimaryProcedureId . "'";
                    $primary_procedureRow = DB::selectone($primary_procedureQry);
                }
                $patient_primary_procedure_categoryID = '';
                if ($primary_procedureRow) {
                    $patient_primary_procedure_categoryID = $primary_procedureRow->catId;
                }

                $ViewUserNameQry = "select fname,mname,lname,user_type from `users` where  usersId = '" . $userId . "'";
                $ViewUserNameRow = DB::selectone($ViewUserNameQry); // or die($ViewUserNameQry . imw_error());
                $loggedInUserFirstName = $ViewUserNameRow->fname;
                $loggedInUserMiddleName = $ViewUserNameRow->mname;
                $loggedInUserLastName = $ViewUserNameRow->lname;
                $user_type = $ViewUserNameRow->user_type;
                $signOnFileStatus = 'Yes';
                $loggedInUserName = $ViewUserNameRow->lname . ", " . $ViewUserNameRow->fname . " " . $ViewUserNameRow->mname;
                $ekgHp = $this->getDirContentStatus($pConfirmId, 2);
                $ekgHpLink = '';
                $formStatus = 'not completed';
                $tablename = "history_physicial_clearance";
                // unset($arrayRecord);
                $arrayRecord['confirmation_id'] = $pConfirmId;
                $arrayRecord['patient_id'] = $patient_id;

                //START CODE TO CHECK NURSE SIGN IN DATABASE
                $chkUserSignDetails = $this->getRowRecord('history_physicial_clearance', 'confirmation_id', $pConfirmId);
                if ($chkUserSignDetails) {
                    $chk_signSurgeon1Id = $chkUserSignDetails->signSurgeon1Id;
                    $chk_signAnesthesia1Id = $chkUserSignDetails->signAnesthesia1Id;
                    $chk_signNurseId = $chkUserSignDetails->signNurseId;
                    $chk_form_status = $chkUserSignDetails->form_status;
                    $chk_version_num = $chkUserSignDetails->version_num;
                    $chk_version_date_time = $chkUserSignDetails->version_date_time;
                }
                //END CODE TO CHECK NURSE SIGN IN DATABASE 
                // Check For chart version information
                $version_num = $chk_version_num;
                if (!$version_num) {
                    $version_date_time = $chk_version_date_time;
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

                $historys = isset($jsondata->history_left_data)?$jsondata->history_left_data:[];
                $sample_keys = "CAD_MIN_W_WO_Stent_OR_CABG_PVD,CVA_TIA_Epilepsy_Neurological,HTN_CP_SOB_on_Exertion,Anticoagulation_therapy,Respiratory_Asthma_COPD_Sleep_Apnea,"
                        . "Arthritis,Diabetes,Recreational_Drug_Use,GI_GERD_PUD_Liver_Disease_Hepatitis,Ocular,Kidney_Disease_Dialysis_G_U,HIV_Autoimmune_Diseases_Contagious_Diseases,"
                        . "History_of_Cancer,Organ_Transplant,A_Bad_Reaction_to_Local_or_General_Anesthesia,High_Cholesterol,Thyroid,Ulcers, Other,"
                        . "Discussed_Advanced_Directives_and_Patient_Rights_and_Responsibilities,Heart_Exam_done_with_stethoscope_Normal,Lung_Exam_done_with_stethoscope_Normal";
                $sample_keyss = explode(",", $sample_keys);
                foreach ($sample_keyss as $key => $sample_key) {
                    switch (trim($sample_key)) {
                        case 'CAD_MIN_W_WO_Stent_OR_CABG_PVD':
                            $arrayRecord['cadMI'] = isset($historys[0]->CAD_MIN_W_WO_Stent_OR_CABG_PVD->yes_no_sts) ? $historys[0]->CAD_MIN_W_WO_Stent_OR_CABG_PVD->yes_no_sts : "";
                            $arrayRecord['cadMIDesc'] = isset($historys[0]->CAD_MIN_W_WO_Stent_OR_CABG_PVD->text_note) ? addslashes($historys[0]->CAD_MIN_W_WO_Stent_OR_CABG_PVD->text_note) : "";
                            break;
                        case 'CVA_TIA_Epilepsy_Neurological':
                            $arrayRecord['cvaTIA'] = isset($historys[1]->CVA_TIA_Epilepsy_Neurological->yes_no_sts) ? $historys[1]->CVA_TIA_Epilepsy_Neurological->yes_no_sts : "";
                            $arrayRecord['cvaTIADesc'] = isset($historys[1]->CVA_TIA_Epilepsy_Neurological->text_note) ? addslashes($historys[1]->CVA_TIA_Epilepsy_Neurological->text_note) : "";
                            break;
                        case 'HTN_CP_SOB_on_Exertion':
                            $arrayRecord['htnCP'] = isset($historys[2]->HTN_CP_SOB_on_Exertion->yes_no_sts) ? $historys[2]->HTN_CP_SOB_on_Exertion->yes_no_sts : "";
                            $arrayRecord['htnCPDesc'] = isset($historys[2]->HTN_CP_SOB_on_Exertion->text_note) ? $historys[2]->HTN_CP_SOB_on_Exertion->text_note : "";
                            break;
                        case 'Anticoagulation_therapy':
                            $arrayRecord['anticoagulationTherapy'] = isset($historys[3]->Anticoagulation_therapy->yes_no_sts) ? $historys[3]->Anticoagulation_therapy->yes_no_sts : "";
                            $arrayRecord['anticoagulationTherapyDesc'] = isset($historys[3]->Anticoagulation_therapy->text_note) ? addslashes($historys[3]->Anticoagulation_therapy->text_note) : "";
                            break;
                        case 'Respiratory_Asthma_COPD_Sleep_Apnea':
                            $arrayRecord['respiratoryAsthma'] = isset($historys[4]->Respiratory_Asthma_COPD_Sleep_Apnea->yes_no_sts) ? $historys[4]->Respiratory_Asthma_COPD_Sleep_Apnea->yes_no_sts : "";
                            $arrayRecord['respiratoryAsthmaDesc'] = isset($historys[4]->Respiratory_Asthma_COPD_Sleep_Apnea->text_note) ? addslashes($historys[4]->Respiratory_Asthma_COPD_Sleep_Apnea->text_note) : "";
                            break;
                        case 'Arthritis':
                            $arrayRecord['arthritis'] = isset($historys[5]->Arthritis->yes_no_sts) ? $historys[5]->Arthritis->yes_no_sts : "";
                            $arrayRecord['arthritisDesc'] = isset($historys[5]->Arthritis->text_note) ? addslashes($historys[5]->Arthritis->text_note) : "";
                            break;
                        case 'Diabetes':
                            $arrayRecord['diabetes'] = isset($historys[6]->Diabetes->yes_no_sts) ? $historys[6]->Diabetes->yes_no_sts : "";
                            $arrayRecord['diabetesDesc'] = isset($historys[6]->Diabetes->text_note) ? addslashes($historys[6]->Diabetes->text_note) : "";
                            break;
                        case 'Recreational_Drug_Use':
                            $arrayRecord['recreationalDrug'] = isset($historys[7]->Recreational_Drug_Use->yes_no_sts) ? $historys[7]->Recreational_Drug_Use->yes_no_sts : "";
                            $arrayRecord['recreationalDrugDesc'] = isset($historys[7]->Recreational_Drug_Use->text_note) ? addslashes($historys[7]->Recreational_Drug_Use->text_note) : "";
                            break;
                        case 'GI_GERD_PUD_Liver_Disease_Hepatitis':
                            $arrayRecord['giGerd'] = isset($historys[8]->GI_GERD_PUD_Liver_Disease_Hepatitis->yes_no_sts) ? $historys[8]->GI_GERD_PUD_Liver_Disease_Hepatitis->yes_no_sts : "";
                            $arrayRecord['giGerdDesc'] = isset($historys[8]->GI_GERD_PUD_Liver_Disease_Hepatitis->text_note) ? addslashes($historys[8]->GI_GERD_PUD_Liver_Disease_Hepatitis->text_note) : "";
                            break;
                        case 'Ocular':
                            $arrayRecord['ocular'] = isset($historys[9]->Ocular->yes_no_sts) ? $historys[9]->Ocular->yes_no_sts : "";
                            $arrayRecord['ocularDesc'] = isset($historys[9]->Ocular->text_note) ? addslashes($historys[9]->Ocular->text_note) : "";
                            break;
                        case 'Kidney_Disease_Dialysis_G_U':
                            $arrayRecord['kidneyDisease'] = isset($historys[10]->Kidney_Disease_Dialysis_G_U->yes_no_sts) ? $historys[10]->Kidney_Disease_Dialysis_G_U->yes_no_sts : "";
                            $arrayRecord['kidneyDiseaseDesc'] = isset($historys[10]->Kidney_Disease_Dialysis_G_U->text_note) ? addslashes($historys[10]->Kidney_Disease_Dialysis_G_U->text_note) : "";
                            break;
                        case 'HIV_Autoimmune_Diseases_Contagious_Diseases':
                            $arrayRecord['hivAutoimmune'] = isset($historys[11]->HIV_Autoimmune_Diseases_Contagious_Diseases->yes_no_sts) ? $historys[11]->HIV_Autoimmune_Diseases_Contagious_Diseases->yes_no_sts : "";
                            $arrayRecord['hivAutoimmuneDesc'] = isset($historys[11]->HIV_Autoimmune_Diseases_Contagious_Diseases->text_note) ? addslashes($historys[11]->HIV_Autoimmune_Diseases_Contagious_Diseases->text_note) : "";
                            break;
                        case 'History_of_Cancer':
                            $arrayRecord['historyCancer'] = isset($historys[12]->History_of_Cancer->yes_no_sts) ? $historys[12]->History_of_Cancer->yes_no_sts : "";
                            $arrayRecord['historyCancerDesc'] = isset($historys[12]->History_of_Cancer->text_note) ? addslashes($historys[12]->History_of_Cancer->text_note) : "";
                            break;
                        case 'Organ_Transplant':
                            $arrayRecord['organTransplant'] = isset($historys[13]->Organ_Transplant->yes_no_sts) ? $historys[13]->Organ_Transplant->yes_no_sts : "";
                            $arrayRecord['organTransplantDesc'] = isset($historys[13]->Organ_Transplant->text_note) ? addslashes($historys[13]->Organ_Transplant->text_note) : "";
                            break;
                        case 'A_Bad_Reaction_to_Local_or_General_Anesthesia':
                            $arrayRecord['badReaction'] = isset($historys[14]->A_Bad_Reaction_to_Local_or_General_Anesthesia->yes_no_sts) ? $historys[14]->A_Bad_Reaction_to_Local_or_General_Anesthesia->yes_no_sts : "";
                            $arrayRecord['badReactionDesc'] = isset($historys[14]->A_Bad_Reaction_to_Local_or_General_Anesthesia->text_note) ? addslashes($historys[14]->A_Bad_Reaction_to_Local_or_General_Anesthesia->text_note) : "";
                            break;
                        case 'High_Cholesterol' :
                            if ($version_num > 3) {
                                $highCholesterol = isset($historys[15]->High_Cholesterol->yes_no_sts) ? $historys[15]->High_Cholesterol->yes_no_sts : "";
                                $highCholesterolDesc = isset($historys[15]->High_Cholesterol->text_note) ? addslashes($historys[15]->High_Cholesterol->text_note) : "";
                                $arrayRecord['highCholesterol'] = $highCholesterol;
                                $arrayRecord['highCholesterolDesc'] = ( 'yes' == strtolower($highCholesterol) ) ? $highCholesterolDesc : '';
                            }
                            break;
                        case 'Thyroid':
                            if ($version_num > 3) {
                                $thyroid = isset($historys[16]->Thyroid->yes_no_sts) ? $historys[16]->Thyroid->yes_no_sts : "";
                                $thyroidDesc = isset($historys[16]->Thyroid->text_note) ? addslashes($historys[16]->Thyroid->text_note) : "";
                                $arrayRecord['thyroid'] = $thyroid;
                                $arrayRecord['thyroidDesc'] = ( 'yes' == strtolower($thyroid) ) ? $thyroidDesc : '';
                            }
                            break;
                        case 'Ulcers':
                            if ($version_num > 3) {
                                $ulcer = isset($historys[17]->Ulcers->yes_no_sts) ? $historys[17]->Ulcers->yes_no_sts : "";
                                $ulcerDesc = isset($historys[17]->Ulcers->text_note) ? addslashes($historys[17]->Ulcers->text_note) : "";
                                $arrayRecord['ulcer'] = $ulcer;
                                $arrayRecord['ulcerDesc'] = ( 'yes' == strtolower($ulcer) ) ? $ulcerDesc : '';
                            }
                            break;
                        case 'Other':
                            $arrayRecord['otherHistoryPhysical'] = isset($historys[18]->Other->text_note) ? addslashes($historys[18]->Other->text_note) : "";
                            break;
                        case 'Discussed_Advanced_Directives_and_Patient_Rights_and_Responsibilities':
                            
                            if ($version_num > 2) {
                                 $arrayRecord['discussedAdvancedDirective'] = isset($historys[19]->Discussed_Advanced_Directives_and_Patient_Rights_and_Responsibilities->yes_no_sts) ? addslashes($historys[19]->Discussed_Advanced_Directives_and_Patient_Rights_and_Responsibilities->yes_no_sts) : "";
                            }
                            break;
                        case 'Heart_Exam_done_with_stethoscope_Normal' :
                            if ($version_num > 1) {
                                $heartExamDesc = isset($historys[20]->Heart_Exam_done_with_stethoscope_Normal->text_note) ? stripslashes($historys[20]->Heart_Exam_done_with_stethoscope_Normal->text_note) : "";
                                $heartExam = isset($historys[20]->Heart_Exam_done_with_stethoscope_Normal->yes_no_sts) ? $historys[20]->Heart_Exam_done_with_stethoscope_Normal->yes_no_sts : "";
                                $arrayRecord['heartExam'] = $heartExam;
                                $arrayRecord['heartExamDesc'] = ( 'no' == strtolower($heartExam) ) ? $heartExamDesc : '';
                            }
                            break;
                        case 'Lung_Exam_done_with_stethoscope_Normal' :
                            if ($version_num > 1) {
                                $lungExam = isset($historys[21]->Lung_Exam_done_with_stethoscope_Normal->yes_no_sts) ? stripslashes($historys[21]->Lung_Exam_done_with_stethoscope_Normal->yes_no_sts) : "";
                                $arrayRecord['lungExam'] = $lungExam;
                                $lungExamDesc = isset($historys[21]->Lung_Exam_done_with_stethoscope_Normal->text_note) ? stripslashes($historys[21]->Lung_Exam_done_with_stethoscope_Normal->text_note) : "";
                                $arrayRecord['lungExamDesc'] = ( 'no' == strtolower($lungExam) ) ? $lungExamDesc : '';
                            }
                            break;
                    }
                }
                $i = 0;
                $day_Of_Surgery_Notes = isset($jsondata->Day_Of_Surgery_Notes)?$jsondata->Day_Of_Surgery_Notes:[];
               
                if (!empty($day_Of_Surgery_Notes)) {
                    //foreach ($Day_Of_Surgery_Notes as $day_Of_Surgery_Notes) {
                    $arrayRecord['smoking'] = isset($day_Of_Surgery_Notes[2]->Smoking->yes_no_sts) ? $day_Of_Surgery_Notes[2]->Smoking->yes_no_sts : "";
                    $arrayRecord['smokingDesc'] = isset($day_Of_Surgery_Notes[2]->Smoking->text_note) ? addslashes($day_Of_Surgery_Notes[2]->Smoking->text_note) : "";
                    $arrayRecord['drinkAlcohal'] = isset($day_Of_Surgery_Notes[3]->Drink_Alcohol->yes_no_sts) ? $day_Of_Surgery_Notes[3]->Drink_Alcohol->yes_no_sts : "";
                    $arrayRecord['drinkAlcohalDesc'] = isset($day_Of_Surgery_Notes[3]->Drink_Alcohol->text_note) ? addslashes($day_Of_Surgery_Notes[3]->Drink_Alcohol->text_note) : "";
                    $arrayRecord['haveAutomatic'] = isset($day_Of_Surgery_Notes[4]->Have_an_automatic_internal_defibrillator->yes_no_sts) ? $day_Of_Surgery_Notes[4]->Have_an_automatic_internal_defibrillator->yes_no_sts : "";
                    $arrayRecord['haveAutomaticDesc'] = isset($day_Of_Surgery_Notes[4]->Have_an_automatic_internal_defibrillator->text_note) ? addslashes($day_Of_Surgery_Notes[4]->Have_an_automatic_internal_defibrillator->text_note) : "";
                    $arrayRecord['medicalHistoryObtained'] = isset($day_Of_Surgery_Notes[5]->Medical_History_obtained_from->yes_no_sts) ? $day_Of_Surgery_Notes[5]->Medical_History_obtained_from->yes_no_sts : "";
                    $arrayRecord['medicalHistoryObtainedDesc'] = isset($day_Of_Surgery_Notes[5]->Medical_History_obtained_from->text_note) ? addslashes($day_Of_Surgery_Notes[5]->Medical_History_obtained_from->text_note) : "";
                    $arrayRecord['otherNotes'] = isset($day_Of_Surgery_Notes[6]->Notes->text_note) ? addslashes($day_Of_Surgery_Notes[6]->Notes->text_note) : "";
                    $date1Post = '';
                    $date1 = isset($day_Of_Surgery_Notes[0]->Date_of_Last_Menstrual_Cycle->date) ? $day_Of_Surgery_Notes[0]->Date_of_Last_Menstrual_Cycle->date : "";
                    if (trim($date1)<> "") {
                        list($mm, $dd, $yy) = @explode("-", trim($date1));
                        $date1Post = $yy . "-" . $mm . "-" . $dd;
                    }
                    $arrayRecord['date_of_h_p'] = $date1Post;
                    $arrayRecord['wearContactLenses'] = isset($day_Of_Surgery_Notes[1]->Wear_Contact_Lenses->yes_no_sts) ? addslashes($day_Of_Surgery_Notes[1]->Wear_Contact_Lenses->yes_no_sts) : "";
                    $arrayRecord['wearContactLensesDesc'] = isset($day_Of_Surgery_Notes[1]->Wear_Contact_Lenses->text_note) ? addslashes($day_Of_Surgery_Notes[1]->Wear_Contact_Lenses->text_note) : "";
                }
                $patient_primary_procedure_categoryID = $patient_primary_procedure_categoryID; //$jsondata->Day_Of_Surgery_Notes[2]->HTN_CP_SOB_on_Exertion->yes_no_sts;
                $chk_signNurseId = $chk_signNurseId; //$jsondata->Day_Of_Surgery_Notes[2]->HTN_CP_SOB_on_Exertion->yes_no_sts;
                $formStatus = 'not completed';

                //START CODE TO RESET THE RECORD
                $save_date_time = date('Y-m-d H:i:s');
                $save_operator_id = $userId;
                //END CODE TO RESET THE RECORD
                $arrayRecord['form_status'] = $formStatus;
                $arrayRecord['save_date_time'] = date("Y-m-d H:i:s");
                $arrayRecord['save_operator_id'] = $userId;
                $loggedInUserFirstName = $ViewUserNameRow->fname;
                $loggedInUserMiddleName = $ViewUserNameRow->mname;
                $loggedInUserLastName = $ViewUserNameRow->lname;
                $user_type = $ViewUserNameRow->user_type;
                if (isset($jsondata->signature->surgeon_signature) && $jsondata->signature->surgeon_signature->signed_status == 'Yes') {
                    $arrayRecord['signSurgeon1Id'] = $userId;
                    $arrayRecord['signSurgeon1FirstName'] = $loggedInUserFirstName;
                    $arrayRecord['signSurgeon1MiddleName'] = $loggedInUserMiddleName;
                    $arrayRecord['signSurgeon1LastName'] = $loggedInUserLastName;
                    $arrayRecord['signSurgeon1Status'] = $jsondata->signature->surgeon_signature->signed_status;
                    $arrayRecord['signSurgeon1DateTime'] = $jsondata->signature->surgeon_signature->sign_date != '0000-00-00 00:00:00' ? date("Y-m-d H:i:s", strtotime($jsondata->signature->surgeon_signature->sign_date)) : "";
                }
                if (isset($jsondata->signature->aneshesia_signature) && $jsondata->signature->aneshesia_signature->signed_status == 'Yes') {
                    $arrayRecord['signAnesthesia1Id'] = $userId;
                    $arrayRecord['signAnesthesia1FirstName'] = $loggedInUserFirstName;
                    $arrayRecord['signAnesthesia1MiddleName'] = $loggedInUserMiddleName;
                    $arrayRecord['signAnesthesia1LastName'] = $loggedInUserLastName;
                    $arrayRecord['signAnesthesia1Status'] = $jsondata->signature->aneshesia_signature->signed_status;
                    $arrayRecord['signAnesthesia1DateTime'] = $jsondata->signature->aneshesia_signature->sign_date != '0000-00-00 00:00:00' ? date("Y-m-d H:i:s", strtotime($jsondata->signature->aneshesia_signature->sign_date)) : "";
                }
                if (isset($jsondata->signature->nurse_signature) && $jsondata->signature->nurse_signature->signed_status == 'Yes') {
                    $arrayRecord['signNurseId'] = $userId;
                    $arrayRecord['signNurseFirstName'] = $loggedInUserFirstName;
                    $arrayRecord['signNurseMiddleName'] = $loggedInUserMiddleName;
                    $arrayRecord['signNurseLastName'] = $loggedInUserLastName;
                    $arrayRecord['signNurseStatus'] = $jsondata->signature->nurse_signature->signed_status;
                    $arrayRecord['signNurseDateTime'] = ($jsondata->signature->nurse_signature->sign_date != '0000-00-00 00:00:00')? date("Y-m-d H:i:s", strtotime($jsondata->signature->nurse_signature->sign_date)) : "";
                }
                //MAKE AUDIT STATUS CRATED OR MODIFIED

                $arrayStatusRecord['user_id'] = $userId;
                $arrayStatusRecord['patient_id'] = $patient_id;
                $arrayStatusRecord['confirmation_id'] = $pConfirmId;
                $arrayStatusRecord['form_name'] = 'history_physical_form';
                $arrayStatusRecord['action_date_time'] = date('Y-m-d H:i:s');
                //MAKE AUDIT STATUS CRATED OR MODIFIED

                if ($history_physicial_id > 0) {
                    DB::table('history_physicial_clearance')->where('history_physicial_id', $history_physicial_id)->update($arrayRecord);
                } else {
                    $history_physicial_id = DB::table('history_physicial_clearance')->insertGetId($arrayRecord);
                }
                $AlternativeQuestion_data = isset($jsondata->AlternativeQuestion) ? $jsondata->AlternativeQuestion : [];
                $this->Alternativequestionersave($AlternativeQuestion_data, $pConfirmId, $patient_id);

                $Allergies_data = isset($jsondata->Allergies_data) ? $jsondata->Allergies_data : [];
                $this->patient_allergy_save($Allergies_data, $pConfirmId, $patient_id, $userId, $loggedInUserName);
                $Medication_data = isset($jsondata->Medication_data) ? $jsondata->Medication_data : [];
                $this->patient_medication_save($Medication_data, $pConfirmId, $patient_id, $userId, $loggedInUserName);

                //CODE START TO SET AUDIT STATUS AFTER SAVE
                unset($conditionArr);
                $conditionArr['confirmation_id'] = $pConfirmId;
                $conditionArr['form_name'] = 'history_physical_form';
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
                //CODE TO DISPLAY FORM STATUS ON RIGHT SLIDER(AS RED FLAG OR TICK MARK) 
                //CODE TO CHECK SURGEON ALL SIGNATURE AND SET VALUE IN STUB TABLE
                $chartSignedBySurgeon = $this->chkSurgeonSignNew($pConfirmId);
                $updateStubTblQry = "UPDATE stub_tbl SET chartSignedBySurgeon='" . $chartSignedBySurgeon . "' WHERE patient_confirmation_id='" . $pConfirmId . "'";
                $updateStubTblRes = DB::select($updateStubTblQry);
                //END CODE TO CHECK SURGEON SIGNATURE AND SET VALUE IN STUB TABLE
                //CODE TO CHECK ANESTHESIOLOGIST ALL SIGNATURE AND SET VALUE IN STUB TABLE
                $chartSignedByAnes = $this->chkAnesSignNew($pConfirmId);
                $updateAnesStubTblQry = "UPDATE stub_tbl SET chartSignedByAnes='" . $chartSignedByAnes . "' WHERE patient_confirmation_id='" . $pConfirmId . "'";
                $updateAnesStubTblRes = DB::select($updateAnesStubTblQry);
                //END CODE TO CHECK ANESTHESIOLOGIST SIGNATURE AND SET VALUE IN STUB TABLE
                //CODE TO CHECK NURSE ALL SIGNATURE AND SET VALUE IN STUB TABLE
                $chartSignedByNurse = $this->chkNurseSignNew($pConfirmId);
                $updateNurseStubTblQry = "UPDATE stub_tbl SET chartSignedByNurse='" . $chartSignedByNurse . "' WHERE patient_confirmation_id='" . $pConfirmId . "'";
                $updateNurseStubTblRes = DB::select($updateNurseStubTblQry);
                //END CODE TO CHECK NURSE SIGNATURE AND SET VALUE IN STUB TABLE
                //REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)
                $message = "Saved successfully!";
                $status = 1;
                $savedStatus = 1;
            }
        }
        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => [],
                    'savedStatus' => $savedStatus,
                    'data' => [],
                    'history_physicial_id' => isset($history_physicial_id) ? $history_physicial_id : 0,
                        ], 200, [ 'Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

    public function history_physicial_clearance_reset(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $loginUserType = $request->json()->get('UserType') ? $request->json()->get('UserType') : $request->input('UserType');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $history_physicial_id = $request->json()->get('history_physicial_id') ? $request->json()->get('history_physicial_id') : $request->input('history_physicial_id');
        $json = $request->json()->get('jsondata') ? $request->json()->get('jsondata') : $request->input('jsondata');
        $jsondata = json_decode($json);
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
        $resetStatus = 0;
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
            } else if ($patient_id == "") {
                $status = 1;
                $message = " PatientId is missing ";
            } else {

                //GETTING CONFIRNATION DETAILS
                $Confirm_patientPrimProc = "";
                $Confirm_patientPrimaryProcedureId = 0;
                $getConfirmationDetails = DB::selectone("select surgeonId,surgeon_name,patient_primary_procedure,patient_primary_procedure_id from patientconfirmation where patientConfirmationId='" . $pConfirmId . "'");
                if ($getConfirmationDetails) {
                    $hpAssignedSurgeonId = $getConfirmationDetails->surgeonId;
                    $hpAssignedSurgeonName = stripslashes($getConfirmationDetails->surgeon_name);
                    $Confirm_patientPrimProc = stripslashes($getConfirmationDetails->patient_primary_procedure);
                    $Confirm_patientPrimaryProcedureId = $getConfirmationDetails->patient_primary_procedure_id;
                }
                $primary_procedureQry = "SELECT * FROM procedures WHERE name = '" . addslashes($Confirm_patientPrimProc) . "' OR procedureAlias='" . addslashes($Confirm_patientPrimProc) . "'";
                $primary_procedureRow = DB::selectone($primary_procedureQry);
                if (!$primary_procedureRow) {
                    $primary_procedureQry = "SELECT * FROM procedures WHERE procedureId = '" . $Confirm_patientPrimaryProcedureId . "'";
                    $primary_procedureRow = DB::selectone($primary_procedureQry);
                }
                $patient_primary_procedure_categoryID = '';
                if ($primary_procedureRow) {
                    $patient_primary_procedure_categoryID = $primary_procedureRow->catId;
                }

                $ViewUserNameQry = "select fname,mname,lname,user_type from `users` where  usersId = '" . $userId . "'";
                $ViewUserNameRow = DB::selectone($ViewUserNameQry); // or die($ViewUserNameQry . imw_error());
                $loggedInUserFirstName = $ViewUserNameRow->fname;
                $loggedInUserMiddleName = $ViewUserNameRow->mname;
                $loggedInUserLastName = $ViewUserNameRow->lname;
                $user_type = $ViewUserNameRow->user_type;
                $signOnFileStatus = 'Yes';
                $loggedInUserName = $ViewUserNameRow->lname . ", " . $ViewUserNameRow->fname . " " . $ViewUserNameRow->mname;
                $ekgHp = $this->getDirContentStatus($pConfirmId, 2);
                $ekgHpLink = '';
                $formStatus = 'not completed';
                $tablename = "history_physicial_clearance";
                //unset($arrayRecord);
                $arrayRecord['confirmation_id'] = $pConfirmId;
                $arrayRecord['patient_id'] = $patient_id;

                //START CODE TO CHECK NURSE SIGN IN DATABASE
                $chkUserSignDetails = $this->getRowRecord('history_physicial_clearance', 'confirmation_id', $pConfirmId);
                if ($chkUserSignDetails) {
                    $chk_signSurgeon1Id = $chkUserSignDetails->signSurgeon1Id;
                    $chk_signAnesthesia1Id = $chkUserSignDetails->signAnesthesia1Id;
                    $chk_signNurseId = $chkUserSignDetails->signNurseId;
                    $chk_form_status = $chkUserSignDetails->form_status;
                    $chk_version_num = $chkUserSignDetails->version_num;
                    $chk_version_date_time = $chkUserSignDetails->version_date_time;
                }
                //END CODE TO CHECK NURSE SIGN IN DATABASE 
                // Check For chart version information
                $version_num = $chk_version_num;
                if (!$chk_version_num) {
                    $version_date_time = $chk_version_date_time;
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
                    $heartExam = '';
                    $heartExamDesc = '';
                    $lungExam = '';
                    $lungExamDesc = '';

                    $arrayRecord['heartExam'] = '';
                    $arrayRecord['heartExamDesc'] = ( 'no' == strtolower($heartExam) ) ? $heartExamDesc : '';
                    $arrayRecord['lungExam'] = $lungExam;
                    $arrayRecord['lungExamDesc'] = ( 'no' == strtolower($lungExam) ) ? $lungExamDesc : '';
                }
                $chbx_cad_mi = ''; // $jsondata->history_left_data[0]->CAD_MIN_W_WO_Stent_OR_CABG_PVD->yes_no_sts;
                $chbx_cva_tia = ''; // $jsondata->history_left_data[1]->CVA_TIA_Epilepsy_Neurological->yes_no_sts;
                $chbx_htn_cp = ''; // $jsondata->history_left_data[2]->HTN_CP_SOB_on_Exertion->yes_no_sts;
                $chbx_anticoagulation_therapy = ''; // $jsondata->history_left_data[3]->Anticoagulation_therapy->yes_no_sts;
                $chbx_respiratory_asthma = ''; // $jsondata->history_left_data[4]->Respiratory_Asthma_COPD_Sleep_Apnea->yes_no_sts;
                $chbx_diabetes = ''; // $jsondata->history_left_data[6]->Diabetes->yes_no_sts;
                $chbx_recreational_drug = ''; // $jsondata->history_left_data[7]->Recreational_Drug_Use->yes_no_sts;
                $chbx_gi_gerd = ''; // $jsondata->history_left_data[8]->GI_GERD_PUD_Liver_Disease_Hepatitis->yes_no_sts;
                $chbx_ocular = ''; // $jsondata->history_left_data[9]->Ocular->yes_no_sts;
                $chbx_kidney_disease = ''; // $jsondata->history_left_data[10]->Kidney_Disease_Dialysis_G_U->yes_no_sts;
                $chbx_hiv_autoimmune = ''; // $jsondata->history_left_data[11]->HIV_Autoimmune_Diseases_Contagious_Diseases->yes_no_sts;
                $chbx_history_cancer = ''; // $jsondata->history_left_data[12]->History_of_Cancer->yes_no_sts;
                $chbx_organ_transplant = ''; // $jsondata->history_left_data[13]->Organ_Transplant->yes_no_sts;
                $chbx_bad_reaction = ''; // $jsondata->history_left_data[14]->A_Bad_Reaction_to_Local_or_General_Anesthesia->yes_no_sts;
                $chbx_wear_contact_lenses = ''; // $jsondata->Day_Of_Surgery_Notes[1]->Day_Of_Surgery_Notes->yes_no_sts;
                $chbx_smoking = ''; // $jsondata->Day_Of_Surgery_Notes[2]->Smoking->yes_no_sts;
                $chbx_drink_alcohal = ''; // $jsondata->Day_Of_Surgery_Notes[3]->Drink_Alcohol->yes_no_sts;
                $chbx_have_automatic = ''; // $jsondata->Day_Of_Surgery_Notes[4]->Have_an_automatic_internal_defibrillator->yes_no_sts;
                $chbx_medical_history_obtained = ''; // $jsondata->Day_Of_Surgery_Notes[5]->Medical_History_obtained_from->yes_no_sts;
                $patient_primary_procedure_categoryID = ''; // $jsondata->Day_Of_Surgery_Notes[2]->HTN_CP_SOB_on_Exertion->yes_no_sts;
                $chk_signNurseId = ''; // $jsondata->Day_Of_Surgery_Notes[2]->HTN_CP_SOB_on_Exertion->yes_no_sts;
                $chbx_heart_exam = ''; // $jsondata->history_left_data[20]->Heart_Exam_done_with_stethoscope_Normal->yes_no_sts;
                $chbx_lung_exam = ''; // $jsondata->history_left_data[21]->Lung_Exam_done_with_stethoscope_Normal->yes_no_sts;
                $chbx_advance_directive = ''; // $jsondata->history_left_data[19]->Discussed_Advanced_Directives_and_Patient_Rights_and_Responsibilities->text_note;
                $date1 = ''; // $jsondata->Day_Of_Surgery_Notes[0]->Date_of_Last_Menstrual_Cycle->date;
                $chbx_arthritis = ''; // $jsondata->history_left_data[5]->Arthritis->yes_no_sts;
                if ($version_num > 2) {
                    $arrayRecord['discussedAdvancedDirective'] = ''; // addslashes($chbx_advance_directive);
                }
                if ($version_num > 3) {
                    $highCholesterol = ''; // $jsondata->history_left_data[15]->High_Cholesterol->yes_no_sts;
                    $highCholesterolDesc = ''; // addslashes($jsondata->history_left_data[15]->High_Cholesterol->text_note);
                    $thyroid = ''; // $jsondata->history_left_data[16]->Thyroid->yes_no_sts;
                    $thyroidDesc = ''; // addslashes($jsondata->history_left_data[16]->Thyroid->text_note);
                    $ulcer = ''; // $jsondata->history_left_data[17]->Ulcers->yes_no_sts;
                    $ulcerDesc = ''; // addslashes($jsondata->history_left_data[17]->Ulcers->text_note);

                    $arrayRecord['highCholesterol'] = $highCholesterol;
                    $arrayRecord['highCholesterolDesc'] = ( 'yes' == strtolower($highCholesterol) ) ? $highCholesterolDesc : '';
                    $arrayRecord['thyroid'] = $thyroid;
                    $arrayRecord['thyroidDesc'] = ( 'yes' == strtolower($thyroid) ) ? $thyroidDesc : '';
                    $arrayRecord ['ulcer'] = $ulcer;
                    $arrayRecord['ulcerDesc'] = ( 'yes' == strtolower($ulcer) ) ? $ulcerDesc : '';
                }

                if ($ekgHp && getenv('CHECK_H_AND_P') <> 'NO') {
                    $formStatus = 'completed';
                } elseif (($chbx_cad_mi != '') && ($chbx_cva_tia != '') && ($chbx_htn_cp != '') && ($chbx_anticoagulation_therapy != '') && ($chbx_respiratory_asthma != '') && ($chbx_arthritis != '') && ( $chbx_diabetes != '') && ($chbx_recreational_drug != '' ) && ($chbx_gi_gerd != '') && ($chbx_ocular != '') && ($chbx_kidney_disease != '') && ($chbx_hiv_autoimmune != '') && ($chbx_history_cancer != '') && ($chbx_organ_transplant != '') && ($chbx_bad_reaction != '') && ($chbx_wear_contact_lenses != '') && ($chbx_smoking != '') && ($chbx_drink_alcohal != '' ) && ($chbx_have_automatic != '' ) && ($chbx_medical_history_obtained != '') && ($chk_signSurgeon1Id != '0' ) && ($chk_signAnesthesia1Id != '0' || $patient_primary_procedure_categoryID == '2') && ($chk_signNurseId != '0')
                ) {
                    $formStatus = 'completed';
                } else {
                    $formStatus = 'not completed';
                }

                if ((!$ekgHp || ($ekgHp && getenv('CHECK_H_AND_P') == 'NO' ) ) && $formStatus == 'completed' && $version_num > 1 && ($chbx_heart_exam == '' || $chbx_lung_exam == '')) {
                    $formStatus = 'not completed';
                }
                if ((!$ekgHp || ($ekgHp && getenv('CHECK_H_AND_P') == 'NO')) && $formStatus == 'completed' && $version_num > 2 && $chbx_advance_directive == '') {
                    $formStatus = 'not completed';
                }
                if ((!$ekgHp || ($ekgHp && getenv('CHECK_H_AND_P') == 'NO' ) ) && $formStatus == 'completed' && $version_num > 3 && ($highCholesterol == '' || $thyroid == '' || $ulcer == '' )) {
                    $formStatus = 'not completed';
                }
                //START CODE TO RESET THE RECORD
                $save_date_time = date('Y-m-d H:i:s');
                $save_operator_id = $userId;
                //END CODE TO RESET THE RECORD
                if (trim($date1)) {
                    list($mm, $dd, $yy ) = explode("-", trim($date1));
                    $date1Post = $yy . "-" . $mm . "-" . $dd;
                }
                $arrayRecord['date_of_h_p'] = '';
                $arrayRecord['form_status'] = $formStatus;
                $arrayRecord['cadMI'] = '';
                $arrayRecord['cadMIDesc'] = '';
                $arrayRecord['cvaTIA'] = '';
                $arrayRecord['cvaTIADesc'] = '';
                $arrayRecord['htnCP'] = '';
                $arrayRecord['htnCPDesc'] = '';
                $arrayRecord['anticoagulationTherapy'] = '';
                $arrayRecord['anticoagulationTherapyDesc'] = '';
                $arrayRecord['respiratoryAsthma'] = '';
                $arrayRecord['respiratoryAsthmaDesc'] = '';
                $arrayRecord['arthritis'] = '';
                $arrayRecord['arthritisDesc'] = '';
                $arrayRecord['diabetes'] = '';
                $arrayRecord['diabetesDesc'] = '';
                $arrayRecord['recreationalDrug'] = '';
                $arrayRecord['recreationalDrugDesc'] = '';
                $arrayRecord['giGerd'] = '';
                $arrayRecord['giGerdDesc'] = '';
                $arrayRecord['ocular'] = '';
                $arrayRecord['ocularDesc'] = '';
                $arrayRecord['kidneyDisease'] = '';
                $arrayRecord['kidneyDiseaseDesc'] = '';
                $arrayRecord['hivAutoimmune'] = '';
                $arrayRecord['hivAutoimmuneDesc'] = '';
                $arrayRecord['historyCancer'] = '';
                $arrayRecord['historyCancerDesc'] = '';
                $arrayRecord['organTransplant'] = '';
                $arrayRecord['organTransplantDesc'] = '';
                $arrayRecord['badReaction'] = '';
                $arrayRecord['badReactionDesc'] = '';
                $arrayRecord['otherHistoryPhysical'] = '';
                $arrayRecord['wearContactLenses'] = '';
                $arrayRecord['wearContactLensesDesc'] = '';
                $arrayRecord['smoking'] = '';
                $arrayRecord['smokingDesc'] = '';
                $arrayRecord['drinkAlcohal'] = '';
                $arrayRecord['drinkAlcohalDesc'] = '';
                $arrayRecord['haveAutomatic'] = '';
                $arrayRecord['haveAutomaticDesc'] = '';
                $arrayRecord['medicalHistoryObtained'] = '';
                $arrayRecord['medicalHistoryObtainedDesc'] = '';
                $arrayRecord['otherNotes'] = '';
                $arrayRecord['save_date_time'] = date("Y-m-d H:i:s");
                $arrayRecord['save_operator_id'] = $userId;
                $arrayRecord['resetDateTime'] = date("Y-m-d H:i:s");
                $arrayRecord['resetBy'] = $userId;

                //MAKE AUDIT STATUS CRATED OR MODIFIED
                unset($arrayStatusRecord);
                $arrayStatusRecord['user_id'] = $userId;
                $arrayStatusRecord['patient_id'] = $patient_id;
                $arrayStatusRecord['confirmation_id'] = $pConfirmId;
                $arrayStatusRecord['form_name'] = 'history_physical_form';
                $arrayStatusRecord['action_date_time'] = date('Y-m-d H:i:s');
                //MAKE AUDIT STATUS CRATED OR MODIFIED

                if ($history_physicial_id > 0) {
                    DB::table('history_physicial_clearance')->where('history_physicial_id', $history_physicial_id)->update($arrayRecord);
                } else {
                    $history_physicial_id = DB::table('history_physicial_clearance')->insertGetId($arrayRecord);
                }
                DB::select("delete from patient_allergies_tbl where patient_confirmation_id = '$pConfirmId'");
                DB::select("delete from patient_anesthesia_medication_tbl where confirmation_id = '$pConfirmId'");
                DB::select("update history_physical_ques set ques_status='',ques_desc='' where confirmation_id = '$pConfirmId'");
                //CODE START TO SET AUDIT STATUS AFTER SAVE
                unset($conditionArr);
                $conditionArr['confirmation_id'] = $pConfirmId;
                $conditionArr['form_name'] = 'history_physical_form';
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
                //CODE TO DISPLAY FORM STATUS ON RIGHT SLIDER(AS RED FLAG OR TICK MARK) 
                //CODE TO CHECK SURGEON ALL SIGNATURE AND SET VALUE IN STUB TABLE
                $chartSignedBySurgeon = $this->chkSurgeonSignNew($pConfirmId);
                $updateStubTblQry = "UPDATE stub_tbl SET chartSignedBySurgeon='" . $chartSignedBySurgeon . "' WHERE patient_confirmation_id='" . $pConfirmId . "'";
                $updateStubTblRes = DB::select($updateStubTblQry);
                //END CODE TO CHECK SURGEON SIGNATURE AND SET VALUE IN STUB TABLE
                //CODE TO CHECK ANESTHESIOLOGIST ALL SIGNATURE AND SET VALUE IN STUB TABLE
                $chartSignedByAnes = $this->chkAnesSignNew($pConfirmId);
                $updateAnesStubTblQry = "UPDATE stub_tbl SET chartSignedByAnes='" . $chartSignedByAnes . "' WHERE patient_confirmation_id='" . $pConfirmId . "'";
                $updateAnesStubTblRes = DB::select($updateAnesStubTblQry);
                //END CODE TO CHECK ANESTHESIOLOGIST SIGNATURE AND SET VALUE IN STUB TABLE
                //CODE TO CHECK NURSE ALL SIGNATURE AND SET VALUE IN STUB TABLE
                $chartSignedByNurse = $this->chkNurseSignNew($pConfirmId);
                $updateNurseStubTblQry = "UPDATE stub_tbl SET chartSignedByNurse='" . $chartSignedByNurse . "' WHERE patient_confirmation_id='" . $pConfirmId . "'";
                $updateNurseStubTblRes = DB::select($updateNurseStubTblQry);
                //END CODE TO CHECK NURSE SIGNATURE AND SET VALUE IN STUB TABLE
                //REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)
                $status = 1;
                $message = "Reset successfully !";
                $resetStatus = 1;
            }
        }
        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'resetStatus' => $resetStatus,
                    'requiredStatus' => '',
                    'data' => $data,
                    'history_physicial_id' => isset($res->history_physicial_id) ? $res->history_physicial_id : 0,
                        ], 200, [ 'Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
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

}
