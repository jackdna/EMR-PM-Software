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

class PreOPHealthQuestController extends Controller {

    public function preop_healthquest(Request $request) {
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

                $status = 1;
                $message = " Pre-Op Health Questionnaire ";
                $res = DB::selectone("SELECT * FROM `preophealthquestionnaire` where confirmation_id=$pConfirmId");
                $data = $res;
                $allergy1 = "select * from allergies ORDER BY `allergies`.`name` ASC";
                $allergic = DB::select($allergy1);
                $detailConfirmation = $this->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfirmId);
                $finalizeStatus = $detailConfirmation->finalize_status;
                $allergiesNKDA_patientconfirmation_status = $detailConfirmation->allergiesNKDA_status;
                $noMedicationStatus = $detailConfirmation->no_medication_status;
                $noMedicationComments = $detailConfirmation->no_medication_comments;
                //GETTING CONFIRMATION DETAILS
                $detailConfirmationAllergies = $this->getRowRecord('patientconfirmation', 'patientConfirmationId', $pConfirmId);
                if ($detailConfirmationAllergies) {
                    $Confirm_patientHeaderAllergiesNKDA_status = $detailConfirmationAllergies->allergiesNKDA_status;
                }

                $patientAllergies = [];
                $cntHlt = 0;
                $patient_allergies_tblQry = "SELECT pre_op_allergy_id,allergy_name,reaction_name FROM `patient_allergies_tbl` WHERE `patient_confirmation_id` = '$pConfirmId'";
                $patient_allergies_tblRes = DB::select($patient_allergies_tblQry);
                if ($patient_allergies_tblRes) {
                    foreach ($patient_allergies_tblRes as $patient_allergies_tblRow) {
                        $patientAllergies[] = ['pre_op_allergy_id' => $patient_allergies_tblRow->pre_op_allergy_id, 'name' => $patient_allergies_tblRow->allergy_name, 'reaction' => $patient_allergies_tblRow->reaction_name];
                        $cntHlt++;
                    }
                    for ($i = $cntHlt; $i < 20; $i++) {
                        $patientAllergies[] = ['pre_op_allergy_id' => 0, 'name' => '', 'reaction' => ''];
                    }
                } else {
                    for ($i = 0; $i < 20; $i++) {
                        $patientAllergies[] = ['pre_op_allergy_id' => 0, 'name' => '', 'reaction' => ''];
                    }
                }
                $patient_prescription_medication_healthquest_qry = "select prescription_medication_id,prescription_medication_name,prescription_medication_desc,prescription_medication_sig from patient_prescription_medication_healthquest_tbl where confirmation_id= '$pConfirmId' order by prescription_medication_name ASC";
                $patient_prescription_medication_healthquest_res = DB::select($patient_prescription_medication_healthquest_qry);
                $patient_prescriptions = [];
                if ($patient_prescription_medication_healthquest_res) {
                    $cnt = 0;
                    foreach ($patient_prescription_medication_healthquest_res as $rows) {
                        $patient_prescriptions[] = ['prescription_medication_id' => $rows->prescription_medication_id, 'prescription_medication_name' => $rows->prescription_medication_name, 'prescription_medication_desc' => $rows->prescription_medication_desc, 'prescription_medication_sig' => $rows->prescription_medication_sig];
                        $cnt++;
                    }
                    for ($i = $cnt; $i < 20; $i++) {
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
                $imgsrc = "";
                if ($res->patient_sign_image_path <> "" && strstr('SigPlus_images', $res->patient_sign_image_path)) {
                    $imgsrc = getenv('APP_URL') . '/' . getenv('APP_ROOT') . '/' . $res->patient_sign_image_path;
                } else {
                    $imgsrc = getenv('APP_URL') . '/' . getenv('APP_ROOT') . '/SigPlus_images/' . $res->patient_sign_image_path;
                }
                $data = [
                    "hearttroubal" => [
                        "heart_troubal" => ['yes_no_sts' => $res->heartTrouble, "text_note" => stripslashes($res->heartTroubleDesc),],
                        "Stroke" => ['yes_no_sts' => $res->stroke, "text_note" => stripslashes($res->strokeDesc),],
                        "HighBP" => ['yes_no_sts' => $res->HighBP, "text_note" => stripslashes($res->HighBPDesc),],
                        "Anticoagulationtherapy" => ['yes_no_sts' => $res->anticoagulationTherapy, "text_note" => stripslashes($res->anticoagulationTherapyDesc),],
                        "Asthma_Sleep_Apnea_Breathing_Problems" => ['yes_no_sts' => $res->asthma, "text_note" => stripslashes($res->asthmaDesc),],
                        "Tuberculosis" => ['yes_no_sts' => $res->tuberculosis, "text_note" => stripslashes($res->tuberculosisDesc),],
                        "Diabetes" => ['yes_no_sts' => $res->diabetes, "text_note" => stripslashes($res->diabetesDesc), "Insulin" => $res->insulinDependence == 'Yes' ? 1 : 0, 'Non-InsulinDependent' => $res->insulinDependence == 'No' ? 1 : 0],
                        "Epilepsy_Convulsions_ParkinsonVertigo" => ['yes_no_sts' => $res->epilepsy, "text_note" => stripslashes($res->epilepsyDesc),],
                        "RestlessLegSyndrome" => ['yes_no_sts' => $res->restlessLegSyndrome, "text_note" => stripslashes($res->restlessLegSyndromeDesc),],
                        "Hepatitis" => ['yes_no_sts' => $res->hepatitis, "text_note" => stripslashes($res->hepatitisDesc), "A" => ($res->hepatitisA == true) ? 1 : 0, "B" => ($res->hepatitisB == true) ? 1 : 0, "C" => ($res->hepatitisC == true) ? 1 : 0],
                        "KidneyDiseaseDialysis" => ['yes_no_sts' => $res->kidneyDisease, "text_note" => stripslashes($res->kidneyDiseaseDesc), "chk_flag" => 1, "DoyouhaveaShunt" => $res->shunt == 'Yes' ? 1 : 0, "Fistula" => $res->fistula == 'Yes' ? 1 : 0],
                        "HIVAutoimmuneDiseases" => ['yes_no_sts' => $res->hivAutoimmuneDiseases, "text_note" => stripslashes($res->hivTextArea),],
                        "Historyofcancer" => ['yes_no_sts' => $res->cancerHistory, "text_note" => stripslashes($res->cancerHistoryDesc), "chk_flag" => 1, "BreastCancerLeft" => $res->brestCancerLeft == 'Yes' ? 1 : 0, "BreastCancerRight" => $res->brestCancerLeft == 'No' ? 1 : 0],
                        "OrganTransplant" => ['yes_no_sts' => $res->organTransplant, "text_note" => stripslashes($res->organTransplantDesc),],
                        "ABadReactiontoLocalorGeneralAnesthesia" => ['yes_no_sts' => $res->anesthesiaBadReaction, "text_note" => stripslashes($res->anesthesiaBadReactionDesc),],
                        "others" => $res->otherTroubles,
                    ],
                    "AlternativeQuestion" => $this->Alternativehealthquestioner($pConfirmId),
                    "Allergy_data" => ['dropdown' => $allergic, 'NKA' => $allergiesNKDA_patientconfirmation_status == 'Yes' ? 1 : 0, 'AllergiesReviewed' => $res->allergies_status_reviewed, 'patientAllergiesGrid' => $patientAllergies],
                    "Medications_data" => ['dropdown' => $medication, 'PrescriptionGrid' => $patient_prescriptions, "NoMedications" => $noMedicationStatus, "comments" => $noMedicationComments],
                    "do_you" => [ "UseaWheelChairWalkerorCane" => ['yes_no_sts' => $res->walker, "text_note" => stripslashes($res->walkerDesc),],
                        "WearContactlenses" => ['yes_no_sts' => $res->contactLenses, "text_note" => $res->contactLensesDesc,],
                        "Smoke" => ['yes_no_sts' => $res->smoke, "text_note" => stripslashes($res->smokeHowMuch), "PatientadvisednottosmokeHpriortosurgery" => $res->smokeAdvise],
                        "DrinkAlcohol" => ['yes_no_sts' => $res->alchohol, "text_note" => stripslashes($res->alchoholHowMuch), "PatientadvisednottodrinkHpriortosurgery" => $res->alchoholAdvise],
                        "Haveanautomaticinternaldefibrillator" => ['yes_no_sts' => $res->autoInternalDefibrillator, "text_note" => stripslashes($res->autoInternalDefibrillatorDesc),],
                        "HaveanyMetalProsthetics" => ['yes_no_sts' => $res->metalProsthetics, "text_note" => stripslashes($res->notes),],
                        "Contact" => ["EmergencyContactPerson" => $res->emergencyContactPerson, "Telephone" => $res->emergencyContactPhone, "WitnessName" => $res->witnessname, 'WitnessStatus' => $res->signWitness1Status, 'signWitness1DateTime' => date("m-d-Y", strtotime($res->signWitness1DateTime)), 'witnessElectronicName' => $res->signWitness1FirstName . " " . $res->signWitness1LastName, "patientsign" => $imgsrc, "date" => $res->dateQuestionnaire != '0000-00-00' ? date("m-d-Y", strtotime($res->dateQuestionnaire)) : date("m-d-Y"), "witnesssign" => $res->witness_sign_image_path],
                    ]
                ];
            }
        }
        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => '',
                    'data' => $data,
                    'preOpHealthQuesId' => isset($res->preOpHealthQuesId) ? $res->preOpHealthQuesId : 0,
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

    public function Alternativehealthquestioner($pConfId) {
        $qery = "select * from healthquestionadmin where confirmation_id='$pConfId'";
        $questions = DB::select($qery);
        $data = [];
        if ($questions) {
            foreach ($questions as $Questions) {
                $data[] = ['QuestionId' => $Questions->id, 'Question' => $Questions->adminQuestion, "text_note" => stripslashes($Questions->adminQuestionDesc), 'yes_no_sts' => $Questions->adminQuestionStatus,];
            }
            return $data;
        } else {
            $selectAdminQuestionsQry = "select * from healthquestioner";
            $selectAdminQuestions = DB::select($selectAdminQuestionsQry);
            foreach ($selectAdminQuestions as $selectAdminQuestionss) {
                $data[] = ['QuestionId' => 0, 'healthQuestionerId' => $selectAdminQuestionss->healthQuestioner, "text_note" => '', 'Question' => $selectAdminQuestionss->question, 'yes_no_sts' => '',];
            }
            return $data;
        }
    }

    public function patient_allergy_save($Allergies_data, $pConfId, $patient_id, $userId, $loggedInUserName) {
        if (( is_array($Allergies_data)) && (!empty($Allergies_data) )) {
            foreach ($Allergies_data as $allergiesArrValue) {
                $allergiesReactionArr['patient_confirmation_id'] = $pConfId;
                $allergiesReactionArr['patient_id'] = $patient_id;
                $allergiesReactionArr['allergy_name'] = addslashes($allergiesArrValue->name);
                $allergiesReactionArr['reaction_name'] = isset($allergiesArrValue->reaction) ? addslashes($allergiesArrValue->reaction) : "";
                $allergiesReactionArr['operator_name'] = $loggedInUserName;
                $allergiesReactionArr['operator_id'] = $userId;
                if ($allergiesArrValue->name != '') {
                    if ($allergiesArrValue->pre_op_allergy_id > 0) {
                        DB::table('patient_allergies_tbl')->where('pre_op_allergy_id', $allergiesArrValue->pre_op_allergy_id)->update($allergiesReactionArr);
                        //$objectMenageData->updateRecords($allergiesReactionArr, 'patient_allergies_tbl', 'pre_op_allergy_id', $allergiesArrValue->pre_op_allergy_id);
                    } else {
                        DB::table('patient_allergies_tbl')->insert($allergiesReactionArr);
                        //$objectMenageData->addRecords($allergiesReactionArr, 'patient_allergies_tbl');
                    }
                } else if ($allergiesArrValue->name == '' && $allergiesArrValue->reaction == '') {
                    //$objectMenageData->delRecord('patient_allergies_tbl', 'pre_op_allergy_id', $all ergiesArrValue->pre_op_allergy_id);
                    DB::table('patient_allergies_tbl')->where('pre_op_allergy_id', $allergiesArrValue->pre_op_allergy_id)->delete();
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
                        //$objectMenageData->updateRecords($medicationsArr, 'patient_prescription_medication_healthquest_tbl', 'prescription_medication_id', $medicationId[$Key]);
                        DB::table('patient_prescription_medication_healthquest_tbl')->where('prescription_medication_id', $medications->prescription_medication_id)->update($medicationsArr);
                    } else {
                        // $objectMenageData->addRecords($medicationsArr, 'patient_prescription_medication_healthquest_tbl');
                        DB::table('patient_prescription_medication_healthquest_tbl')->insert($medicationsArr);
                    }
                }
            }
        }
    }

    public function Alternativehealthquestionersave($data, $pConfId, $patient_id) {
        $data_arr = [];
        if ($data) {
            foreach ($data as $Questions) {
                if ($Questions->QuestionId > 0) {
                    $data_arr = ["adminQuestionDesc" => addslashes($Questions->text_note), 'adminQuestionStatus' => $Questions->yes_no_sts];
                    DB::table('healthquestionadmin')->where('id', $Questions->QuestionId)->update($data_arr);
                } else {
                    $data_arr = ['adminQuestion' => $Questions->Question, 'confirmation_id' => $pConfId, 'patient_id' => $patient_id, "adminQuestionDesc" => addslashes($Questions->text_note), 'adminQuestionStatus' => $Questions->yes_no_sts];
                    DB::table('healthquestionadmin')->insert($data_arr);
                }
            }
            return $data;
        }
    }

    public function preop_healthquest_save(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $loginUserType = $request->json()->get('UserType') ? $request->json()->get('UserType') : $request->input('UserType');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $jsondata = $request->json()->get('jsondata') ? $request->json()->get('jsondata') : $request->input('jsondata');
        $preOpHealthQuesId = $request->json()->get('preOpHealthQuesId') ? $request->json()->get('preOpHealthQuesId') : $request->input('preOpHealthQuesId');
        $sign = $request->json()->get('sign') ? $request->json()->get('sign') : $request->input('sign');
        $json_data = json_decode($jsondata);
       // print_r($json_data);die;
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
        $savedStatus = 0;
        $arrayRecord = [];
        if ($userId > 0) {
            if ($pConfirmId == "") {
                $message = " ConfirmId is missing ";
                $status = 1;
                $requiredStatus = 0;
            } elseif ($facility_id == "") {
                $message = " FacilityId is missing ";
                $status = 1;
                $requiredStatus = 0;
            } elseif ($patient_id == "") {
                $message = " patientId is missing ";
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
                if ($json_data->Heart_Troubal[1]->Stroke->yes_no_sts <> "" && $json_data->Heart_Troubal[2]->HighBP->yes_no_sts <> "" &&
                        $json_data->Heart_Troubal[1]->Stroke->yes_no_sts <> "" &&
                        $json_data->Heart_Troubal[3]->Anticoagulationtherapy->yes_no_sts <> "" && $json_data->Heart_Troubal[5]->Tuberculosis->yes_no_sts <> "" &&
                        $json_data->Do_You[6]->signature->Emergency_Contact_Person <> "" &&
                        $json_data->Do_You[6]->signature->Telephone <> "" && $json_data->Heart_Troubal[4]->Asthma_Sleep_Apnea_Breathing_Problems->yes_no_sts <> "" &&
                        $json_data->Heart_Troubal[14]->ABadReactiontoLocalorGeneralAnesthesia->yes_no_sts <> "" &&
                        $json_data->Heart_Troubal[10]->KidneyDiseaseDialysis->yes_no_sts <> "" &&
                        $json_data->Heart_Troubal[10]->KidneyDiseaseDialysis->yes_no_sts <> "" &&
                        $json_data->Heart_Troubal[8]->RestlessLegSyndrome->yes_no_sts <> ""
                ) {
                    $formStatus = 'completed';
                }

                $brestCancer = '';
                if ($json_data->Heart_Troubal[12]->Historyofcancer->BreastCancerLeft == 1) {
                    $brestCancer = 'Yes';
                } else if ($json_data->Heart_Troubal[12]->Historyofcancer->BreastCancerRight == 1) {
                    $brestCancer = 'No';
                }
                $arrayRecord['heartTrouble'] = $json_data->Heart_Troubal[0]->heart_troubal->yes_no_sts;
                $arrayRecord['heartTroubleDesc']= $json_data->Heart_Troubal[0]->heart_troubal->text_note;
                $arrayRecord['form_status'] = $formStatus;
                $arrayRecord['stroke'] = $json_data->Heart_Troubal[1]->Stroke->yes_no_sts;
                $arrayRecord['strokeDesc'] = addslashes($json_data->Heart_Troubal[1]->Stroke->text_note);
                $arrayRecord['HighBP'] = $json_data->Heart_Troubal[2]->HighBP->yes_no_sts;
                $arrayRecord['HighBPDesc'] = addslashes($json_data->Heart_Troubal[2]->HighBP->text_note);
                $arrayRecord['heartAttack'] = $json_data->Heart_Troubal[1]->Stroke->yes_no_sts;
                $arrayRecord['anticoagulationTherapy'] = $json_data->Heart_Troubal[3]->Anticoagulationtherapy->yes_no_sts;
                $arrayRecord['anticoagulationTherapyDesc'] = addslashes($json_data->Heart_Troubal[3]->Anticoagulationtherapy->text_note);
                $arrayRecord['asthma'] = $json_data->Heart_Troubal[4]->Asthma_Sleep_Apnea_Breathing_Problems->yes_no_sts;
                $arrayRecord['asthmaDesc'] = addslashes($json_data->Heart_Troubal[4]->Asthma_Sleep_Apnea_Breathing_Problems->text_note);
                $arrayRecord['tuberculosis'] = $json_data->Heart_Troubal[5]->Tuberculosis->yes_no_sts;
                $arrayRecord['tuberculosisDesc'] = addslashes($json_data->Heart_Troubal[5]->Tuberculosis->text_note);
                $arrayRecord['sleepApnea'] = $json_data->Heart_Troubal[4]->Asthma_Sleep_Apnea_Breathing_Problems->yes_no_sts;
                $arrayRecord['breathingProbs'] = $json_data->Heart_Troubal[4]->Asthma_Sleep_Apnea_Breathing_Problems->yes_no_sts;
                $arrayRecord['TB'] = $json_data->Heart_Troubal[4]->Asthma_Sleep_Apnea_Breathing_Problems->yes_no_sts;
                $arrayRecord['diabetes'] = $json_data->Heart_Troubal[6]->Diabetes->yes_no_sts;
                $arrayRecord['diabetesDesc'] = addslashes($json_data->Heart_Troubal[6]->Diabetes->text_note);
                $arrayRecord['insulinDependence'] = $json_data->Heart_Troubal[6]->Diabetes->Insulin;
                $arrayRecord['epilepsy'] = $json_data->Heart_Troubal[7]->Epilepsy_Convulsions_ParkinsonVertigo->yes_no_sts;
                $arrayRecord['epilepsyDesc'] = addslashes($json_data->Heart_Troubal[7]->Epilepsy_Convulsions_ParkinsonVertigo->text_note);
                $arrayRecord['convulsions'] = $json_data->Heart_Troubal[7]->Epilepsy_Convulsions_ParkinsonVertigo->yes_no_sts;
                $arrayRecord['parkinsons'] = $json_data->Heart_Troubal[7]->Epilepsy_Convulsions_ParkinsonVertigo->yes_no_sts;
                $arrayRecord['vertigo'] = $json_data->Heart_Troubal[7]->Epilepsy_Convulsions_ParkinsonVertigo->yes_no_sts;
                $arrayRecord['restlessLegSyndrome'] = $json_data->Heart_Troubal[8]->RestlessLegSyndrome->yes_no_sts;
                $arrayRecord['restlessLegSyndromeDesc'] = addslashes($json_data->Heart_Troubal[8]->RestlessLegSyndrome->text_note);
                $arrayRecord['hepatitis'] = $json_data->Heart_Troubal[9]->Hepatitis->yes_no_sts;
                $arrayRecord['hepatitisDesc'] = addslashes($json_data->Heart_Troubal[9]->Hepatitis->text_note);
                $arrayRecord['hepatitisA'] = isset($json_data->Heart_Troubal[9]->Hepatitis->A) ? $json_data->Heart_Troubal[9]->Hepatitis->A : "";
                $arrayRecord['hepatitisB'] = isset($json_data->Heart_Troubal[9]->Hepatitis->B) ? $json_data->Heart_Troubal[9]->Hepatitis->B : "";
                $arrayRecord['hepatitisC'] = isset($json_data->Heart_Troubal[9]->Hepatitis->C) ? $json_data->Heart_Troubal[9]->Hepatitis->C : "";
                $arrayRecord['kidneyDisease'] = $json_data->Heart_Troubal[10]->KidneyDiseaseDialysis->yes_no_sts;
                $arrayRecord['kidneyDiseaseDesc'] = addslashes($json_data->Heart_Troubal[10]->KidneyDiseaseDialysis->text_note);
                $arrayRecord['shunt'] = $json_data->Heart_Troubal[10]->KidneyDiseaseDialysis->DoyouhaveaShunt;
                $arrayRecord['fistula'] = $json_data->Heart_Troubal[10]->KidneyDiseaseDialysis->Fistula;
                $arrayRecord['hivAutoimmuneDiseases'] = $json_data->Heart_Troubal[11]->HIVAutoimmuneDiseases->yes_no_sts;
                $arrayRecord['hivTextArea'] = addslashes($json_data->Heart_Troubal[11]->HIVAutoimmuneDiseases->text_note);
                $arrayRecord['cancerHistory'] = $json_data->Heart_Troubal[12]->Historyofcancer->yes_no_sts;
                $arrayRecord['brest_cancer'] = $json_data->Heart_Troubal[12]->Historyofcancer->yes_no_sts;
                $arrayRecord['brestCancerLeft'] = $brestCancer;
                $arrayRecord['cancerHistoryDesc'] = addslashes($json_data->Heart_Troubal[12]->Historyofcancer->text_note);
                $arrayRecord['organTransplant'] = $json_data->Heart_Troubal[13]->OrganTransplant->yes_no_sts;
                $arrayRecord['organTransplantDesc'] = addslashes($json_data->Heart_Troubal[13]->OrganTransplant->text_note);
                $arrayRecord['anesthesiaBadReaction'] = $json_data->Heart_Troubal[14]->ABadReactiontoLocalorGeneralAnesthesia->yes_no_sts; // $_POST['chbx_bad_react'];
                $arrayRecord['anesthesiaBadReactionDesc'] = addslashes($json_data->Heart_Troubal[14]->ABadReactiontoLocalorGeneralAnesthesia->text_note);
                $arrayRecord['otherTroubles'] = isset($json_data->Heart_Troubal[14]->others->yes_no_sts) ? addslashes($json_data->Heart_Troubal[14]->others->yes_no_sts) : "";
                //$arrayRecord['allergies_status'] 			= $_POST['chbx_drug_react'];//nkda now nkda will read from patientconfirmation table
                $arrayRecord['allergies_status_reviewed'] = $json_data->Allergies_Reviewed; // $_POST['chbx_drug_react_reviewed'];
                $arrayRecord['walker'] = $json_data->Do_You[0]->Wheel_Chair->yes_no_sts;
                $arrayRecord['walkerDesc'] = addslashes($json_data->Do_You[0]->Wheel_Chair->text_note);
                $arrayRecord['contactLenses'] = isset($json_data->Do_You[1]->WearContact_lenses->yes_no_sts) ? $json_data->Do_You[1]->WearContact_lenses->yes_no_sts : ""; //$_POST['chbx_wear_cont'];
                $arrayRecord['contactLensesDesc'] = isset($json_data->Do_You[1]->WearContact_lenses->text_note) ? addslashes($json_data->Do_You[1]->WearContact_lenses->text_note) : "";
                $arrayRecord['smoke'] = isset($json_data->Do_You[2]->Smoke->yes_no_sts) ? $json_data->Do_You[2]->Smoke->yes_no_sts : "";
                $arrayRecord['smokeHowMuch'] = isset($json_data->Do_You[2]->Smoke->text_note) ? addslashes($json_data->Do_You[2]->Smoke->text_note) : "";
                $arrayRecord['smokeAdvise'] = isset($json_data->Do_You[2]->Smoke->PatientadvisednottosmokeHpriortosurgery) ? $json_data->Do_You[2]->Smoke->PatientadvisednottosmokeHpriortosurgery : ""; //$_POST['smokeAdvise'];
                $arrayRecord['alchohol'] = isset($json_data->Do_You[3]->Drink_Alcohol->yes_no_sts) ? $json_data->Do_You[3]->Drink_Alcohol->yes_no_sts : ""; //$_POST['chbx_drink'];
                $arrayRecord['alchoholHowMuch'] = addslashes($json_data->Do_You[3]->Drink_Alcohol->text_note); // addslashes($_POST['alchoholHowMuch']);
                $arrayRecord['alchoholAdvise'] = isset($json_data->Do_You[3]->Drink_Alcohol->PatientadvisednottodrinkHpriortosurgery) ? $json_data->Do_You[3]->Drink_Alcohol->PatientadvisednottodrinkHpriortosurgery : ""; //$_POST['alchoholAdvise'];
                $arrayRecord['autoInternalDefibrillator'] = isset($json_data->Do_You[4]->automatic_internal_defibrillator->yes_no_sts) ? $json_data->Do_You[4]->automatic_internal_defibrillator->yes_no_sts : ""; //$_POST['chbx_hav_auto_int'];
                $arrayRecord['autoInternalDefibrillatorDesc'] = isset($json_data->Do_You[4]->automatic_internal_defibrillator->text_note) ? addslashes($json_data->Do_You[4]->automatic_internal_defibrillator->text_note) : "";
                $arrayRecord['metalProsthetics'] = isset($json_data->Do_You[5]->Metal_Prosthetics->yes_no_sts) ? $json_data->Do_You[5]->Metal_Prosthetics->yes_no_sts : ""; //$_POST['chbx_hav_any_met'];
                $arrayRecord['notes'] = isset($json_data->Do_You[5]->Metal_Prosthetics->text_note) ? addslashes($json_data->Do_You[5]->Metal_Prosthetics->text_note) : "";
                //print'<pre>';print_r($arrayRecord);die;
                $imgSrc = $this->convertBase64(trim($sign), $preOpHealthQuesId, $patient_id, $pConfirmId);
                $arrayRecord['patientSign'] = '';
                $arrayRecord['patient_sign_image_path'] = $imgSrc; // addslashes($postSigDataPtImgSavePath);
                // $arrayRecord['witness_sign_image_path'] = ''; // addslashes($postSigDataWtImgSavePath);
                //$arrayRecord['nursefield'] = $_REQUEST['nurse'];
                $dateQuest = $json_data->Do_You[6]->signature->date; //$_POST['date'];
                $dateQuest = $this->changeDateYMD($dateQuest);
                $arrayRecord['dateQuestionnaire'] = $dateQuest;
                $arrayRecord['timeQuestionnaire'] = time();
                $arrayRecord['emergencyContactPerson'] = addslashes($json_data->Do_You[6]->signature->Emergency_Contact_Person);
                $arrayRecord['witnessname'] = addslashes($json_data->Do_You[6]->signature->witnesssign);

                //$arrayRecord['witnessSign']=$witnSign;
                $arrayRecord['emergencyContactPhone'] = $json_data->Do_You[6]->signature->Telephone;
                $arrayRecord['progressNotes'] = '';
                $arrayRecord['nurseId'] = $userId;

                //MAKE AUDIT STATUS CRATED OR MODIFIED
                unset($arrayStatusRecord);
                $arrayStatusRecord['user_id'] = $userId;
                $arrayStatusRecord['patient_id'] = $patient_id;
                $arrayStatusRecord['confirmation_id'] = $pConfirmId;
                $arrayStatusRecord['form_name'] = 'pre_op_health_ques_form';
                $arrayStatusRecord['action_date_time'] = date('Y-m-d H:i:s');
                //MAKE AUDIT STATUS CRATED OR MODIFIED

                if ($preOpHealthQuesId) {
                    //$objManageData->updateRecords($arrayRecord, 'preophealthquestionnaire', 'preOpHealthQuesId', $preOpHealthQuesId);
                    DB::table('preophealthquestionnaire')->where('preOpHealthQuesId', $preOpHealthQuesId)->update($arrayRecord);
                } else {
                    //$preOpHealthQuesId = $objManageData->addRecords($arrayRecord, 'preophealthquestionnaire');
                    DB::table('preophealthquestionnaire')->insert($arrayRecord);
                }
                $Allergies_data = $json_data->Allergies_data;
                $this->patient_allergy_save($Allergies_data, $pConfirmId, $patient_id, $userId, $loggedInUserName);
                $Medication_data = $json_data->Medication_data;
                $this->patient_medication_save($Medication_data, $pConfirmId, $patient_id, $userId, $loggedInUserName);
                $AlternativeQuestion_data = $json_data->AlternativeQuestion;
                $this->Alternativehealthquestionersave($AlternativeQuestion_data, $pConfirmId, $patient_id);
                //CODE START TO SET AUDIT STATUS AFTER SAVE
                unset($conditionArr);
                $conditionArr['confirmation_id'] = $pConfirmId;
                $conditionArr['form_name'] = 'pre_op_health_ques_form';
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
                //delete allregy when save button clicked
                if ($json_data->Allergies_NKA == 'Yes') {
                    DB::select("delete from patient_allergies_tbl where patient_confirmation_id = '$pConfirmId'");
                }

                //START SAVE NKDA ALLERGIES STATUS AND NO MEDICATION STATUS	
                unset($arrayNoMedData);
                $arrayNoMedData['allergiesNKDA_status'] = $json_data->Allergies_NKA;
                $arrayNoMedData['no_medication_status'] = $json_data->No_Medications;
                $arrayNoMedData['no_medication_comments'] = $json_data->Medication_Comments;
                // $objManageData->updateRecords($arrayNoMedData, "patientconfirmation", "patientConfirmationId", $pConfId);
                DB::table('patientconfirmation')->where('patientConfirmationId', $pConfirmId)->update($arrayNoMedData);
                //END SAVE NKDA ALLERGIES STATUS AND NO MEDICATION STATUS	
                //end delete when save button clicked
                //CODE TO CHECK NURSE ALL SIGNATURE AND SET VALUE IN STUB TABLE
                $chartSignedByNurse = $this->chkNurseSignNew($pConfirmId);
                $updateNurseStubTblQry = "UPDATE stub_tbl SET chartSignedByNurse='" . $chartSignedByNurse . "' WHERE patient_confirmation_id='" . $pConfirmId . "'";
                $updateNurseStubTblRes = DB::select($updateNurseStubTblQry);
                //END CODE TO CHECK NURSE SIGNATURE AND SET VALUE IN STUB TABLE
                $status = 1;
                $message = " Pre-Op Health Questionnaire saved succcessfully ! ";
                $savedStatus = 1;
            }
        }
        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => $jsondata,
                    'savedStatus' => $savedStatus,
                    'data' => [],
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

    public function PreOPHealthQuest_reset(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $loginUserType = $request->json()->get('UserType') ? $request->json()->get('UserType') : $request->input('UserType');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $preOpHealthQuesId = $request->json()->get('preOpHealthQuesId') ? $request->json()->get('preOpHealthQuesId') : $request->input('preOpHealthQuesId');
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
        $arrayRecord = [];
        if ($userId > 0) {
            if ($pConfirmId == "") {
                $message = " ConfirmId is missing ";
                $status = 1;
                $requiredStatus = 0;
            } elseif ($facility_id == "") {
                $message = " FacilityId is missing ";
                $status = 1;
                $requiredStatus = 0;
            } elseif ($patient_id == "") {
                $message = " patientId is missing ";
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
                $formStatus = '';
                $arrayRecord['form_status'] = $formStatus;
                $arrayRecord['stroke'] = '';
                $arrayRecord['heartTrouble'] = '';
                $arrayRecord['heartTroubleDesc']='';    
                $arrayRecord['strokeDesc'] = '';
                $arrayRecord['HighBP'] = '';
                $arrayRecord['HighBPDesc'] = '';
                $arrayRecord['heartAttack'] = '';
                $arrayRecord['anticoagulationTherapy'] = '';
                $arrayRecord['anticoagulationTherapyDesc'] = '';
                $arrayRecord['asthma'] = '';
                $arrayRecord['asthmaDesc'] = '';
                $arrayRecord['tuberculosis'] = '';
                $arrayRecord['tuberculosisDesc'] = '';
                $arrayRecord['sleepApnea'] = '';
                $arrayRecord['breathingProbs'] = '';
                $arrayRecord['TB'] = '';
                $arrayRecord['diabetes'] = '';
                $arrayRecord['diabetesDesc'] = '';
                $arrayRecord['insulinDependence'] = '';
                $arrayRecord['epilepsy'] = '';
                $arrayRecord['epilepsyDesc'] = '';
                $arrayRecord['convulsions'] = '';
                $arrayRecord['parkinsons'] = '';
                $arrayRecord['vertigo'] = '';
                $arrayRecord['restlessLegSyndrome'] = '';
                $arrayRecord['restlessLegSyndromeDesc'] = '';
                $arrayRecord['hepatitis'] = '';
                $arrayRecord['hepatitisDesc'] = '';
                $arrayRecord['hepatitisA'] = '';
                $arrayRecord['hepatitisB'] = "";
                $arrayRecord['hepatitisC'] = "";
                $arrayRecord['kidneyDisease'] = '';
                $arrayRecord['kidneyDiseaseDesc'] = '';
                $arrayRecord['shunt'] = '';
                $arrayRecord['fistula'] = '';
                $arrayRecord['hivAutoimmuneDiseases'] = '';
                $arrayRecord['hivTextArea'] = '';
                $arrayRecord['cancerHistory'] = '';
                $arrayRecord['brest_cancer'] = '';
                $arrayRecord['brestCancerLeft'] = '';
                $arrayRecord['cancerHistoryDesc'] = '';
                $arrayRecord['organTransplant'] = '';
                $arrayRecord['organTransplantDesc'] = '';
                $arrayRecord['anesthesiaBadReaction'] = '';
                $arrayRecord['anesthesiaBadReactionDesc'] = '';
                $arrayRecord['otherTroubles'] = "";
                //$arrayRecord['allergies_status'] 			= $_POST['chbx_drug_react'];//nkda now nkda will read from patientconfirmation table
                $arrayRecord['allergies_status_reviewed'] = ''; // $_POST['chbx_drug_react_reviewed'];
                $arrayRecord['walker'] = '';
                $arrayRecord['walkerDesc'] = '';
                $arrayRecord['contactLenses'] = ""; //$_POST['chbx_wear_cont'];
                $arrayRecord['contactLensesDesc'] = "";
                $arrayRecord['smoke'] = "";
                $arrayRecord['smokeHowMuch'] = "";
                $arrayRecord['smokeAdvise'] = ""; //$_POST['smokeAdvise'];
                $arrayRecord['alchohol'] = ""; //$_POST['chbx_drink'];
                $arrayRecord['alchoholHowMuch'] = ''; // addslashes($_POST['alchoholHowMuch']);
                $arrayRecord['alchoholAdvise'] = ""; //$_POST['alchoholAdvise'];
                $arrayRecord['autoInternalDefibrillator'] = ""; //$_POST['chbx_hav_auto_int'];
                $arrayRecord['autoInternalDefibrillatorDesc'] = "";
                $arrayRecord['metalProsthetics'] = ""; //$_POST['chbx_hav_any_met'];
                $arrayRecord['notes'] = "";
                //print'<pre>';print_r($arrayRecord);die;
                $arrayRecord['patientSign'] = '';
                $arrayRecord['patient_sign_image_path'] = ''; // addslashes($postSigDataPtImgSavePath);
                // $arrayRecord['witness_sign_image_path'] = ''; // addslashes($postSigDataWtImgSavePath);
                //$arrayRecord['nursefield'] = $_REQUEST['nurse'];
                $dateQuest = ''; //$_POST['date'];
                $dateQuest = '';
                $arrayRecord['dateQuestionnaire'] = '';
                $arrayRecord['timeQuestionnaire'] = '';
                $arrayRecord['emergencyContactPerson'] = '';
                $arrayRecord['witnessname'] = '';

                //$arrayRecord['witnessSign']=$witnSign;
                $arrayRecord['emergencyContactPhone'] = '';
                $arrayRecord['progressNotes'] = '';
                $arrayRecord['nurseId'] = '';

                //MAKE AUDIT STATUS CRATED OR MODIFIED
                unset($arrayStatusRecord);
                $arrayStatusRecord['user_id'] = $userId;
                $arrayStatusRecord['patient_id'] = $patient_id;
                $arrayStatusRecord['confirmation_id'] = $pConfirmId;
                $arrayStatusRecord['form_name'] = 'pre_op_health_ques_form';
                $arrayStatusRecord['action_date_time'] = date('Y-m-d H:i:s');
                //MAKE AUDIT STATUS CRATED OR MODIFIED

                if ($preOpHealthQuesId) {
                    //$objManageData->updateRecords($arrayRecord, 'preophealthquestionnaire', 'preOpHealthQuesId', $preOpHealthQuesId);
                    DB::table('preophealthquestionnaire')->where('preOpHealthQuesId', $preOpHealthQuesId)->update($arrayRecord);
                } else {
                    //$preOpHealthQuesId = $objManageData->addRecords($arrayRecord, 'preophealthquestionnaire');
                    DB::table('preophealthquestionnaire')->insert($arrayRecord);
                }
                DB::select("delete from patient_allergies_tbl where patient_confirmation_id = '$pConfirmId'");
                DB::select("delete from patient_prescription_medication_healthquest_tbl where confirmation_id = '$pConfirmId'");
                DB::select("delete from healthquestionadmin where confirmation_id= '$pConfirmId'");
                //CODE START TO SET AUDIT STATUS AFTER SAVE
                unset($conditionArr);
                $conditionArr['confirmation_id'] = $pConfirmId;
                $conditionArr['form_name'] = 'pre_op_health_ques_form';
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
                //START SAVE NKDA ALLERGIES STATUS AND NO MEDICATION STATUS	
                unset($arrayNoMedData);
                $arrayNoMedData['allergiesNKDA_status'] = '';
                $arrayNoMedData['no_medication_status'] = '';
                $arrayNoMedData['no_medication_comments'] = '';
                // $objManageData->updateRecords($arrayNoMedData, "patientconfirmation", "patientConfirmationId", $pConfId);
                DB::table('patientconfirmation')->where('patientConfirmationId', $pConfirmId)->update($arrayNoMedData);
                //END SAVE NKDA ALLERGIES STATUS AND NO MEDICATION STATUS	
                //end delete when save button clicked
                //CODE TO CHECK NURSE ALL SIGNATURE AND SET VALUE IN STUB TABLE
                $chartSignedByNurse = $this->chkNurseSignNew($pConfirmId);
                $updateNurseStubTblQry = "UPDATE stub_tbl SET chartSignedByNurse='" . $chartSignedByNurse . "' WHERE patient_confirmation_id='" . $pConfirmId . "'";
                $updateNurseStubTblRes = DB::select($updateNurseStubTblQry);
                //END CODE TO CHECK NURSE SIGNATURE AND SET VALUE IN STUB TABLE
                $status = 1;
                $message = " Pre-Op Health Questionnaire reset successfully ! ";
                $resetStatus = 1;
            }
        }
        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'resetStatus' => $resetStatus,
                    'requiredStatus' => [],
                    'data' => [],
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

}
