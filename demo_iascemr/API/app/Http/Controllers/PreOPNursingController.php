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

class PreOPNursingController extends Controller {

    public function PreOPNursing_form(Request $request) {
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
        $Time_grid = [];
        $consent_content = '';
        $left_list = '';
        $stat = [];
        $surgery_consent_id = 0;
        $Medication_grid = [];
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

                // GETTING IF PRE OP PHYSICIAN RECORD IS SAVED OR NOT
                //VIEW RECORD FROM DATABASE
                $allergy1 = "select * from allergies ORDER BY `allergies`.`name` ASC";
                $allergic = DB::select($allergy1);
                $detailConfirmation = $this->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfirmId);
                $finalizeStatus = $detailConfirmation->finalize_status;
                $allergiesNKDA_patientconfirmation_status = $detailConfirmation->allergiesNKDA_status;
                $noMedicationStatus = $detailConfirmation->no_medication_status;
                $noMedicationComments = $detailConfirmation->no_medication_comments;
                $ascId = $detailConfirmation->ascId;
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
                        $patient_prescriptions[] = ['prescription_medication_id' => $rows->prescription_medication_id, 'prescription_medication_name' => $rows->prescription_medication_name, 'prescription_medication_desc' => $rows->
                            prescription_medication_desc, 'prescription_medication_sig' => $rows->prescription_medication_sig];
                        $cnt++;
                    }
                    for ($i = $cnt; $i < ($cnt + 20); $i++) {
                        $patient_prescriptions [] = ['prescription_medication_id' => 0, 'prescription_medication_name' => '', 'prescription_medication_desc' => '', 'prescription_medication_sig' => ''];
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
                $PreopnursingQry = "select * from `preopnursingrecord` where  confirmation_id = '" . $pConfirmId . "'";
                $PreopnursingRes = DB::selectone($PreopnursingQry);

                $preOpPhysicianOrderRow = DB::selectone("select * from preopphysicianorders where patient_confirmation_id='" . $pConfirmId . "' order by preOpPhysicianOrdersId,prefilMedicationStatus");
                //END VIEW RECORD FROM DATABASE
                $preOpMedicationDetails = DB::select("select * from patientpreopmedication_tbl where patient_confirmation_id='" . $pConfirmId . "' and preOpPhyOrderId='" . $preOpPhysicianOrderRow->preOpPhysicianOrdersId . "'");
                $qry_med = DB::select("select * from patient_prescription_medication_healthquest_tbl WHERE confirmation_id = '" . $pConfirmId . "' order by `prescription_medication_name`");
                $relivedNurseQry = "select usersId,concat(fname,' ',lname) as name from users where user_type='Nurse' ORDER BY lname";
                $relivedNurseRes = DB::select($relivedNurseQry);
                $ViewPreopNurseVitalSignQry = "select * from `preopnursing_vitalsign_tbl` where  confirmation_id = '" . $pConfirmId . "' order by vitalsign_id";
                $ViewPreopNurseVitalSignRows = DB::select($ViewPreopNurseVitalSignQry); // or die(imw_error());
                $BP_P_R_O2SAT_Temp = [];
                if ($ViewPreopNurseVitalSignRows) {
                    foreach ($ViewPreopNurseVitalSignRows as $ViewPreopNurseVitalSignRow) {
                        $vitalsign_id = $ViewPreopNurseVitalSignRow->vitalsign_id;
                        $vitalSignBp = $ViewPreopNurseVitalSignRow->vitalSignBp;
                        $vitalSignP = $ViewPreopNurseVitalSignRow->vitalSignP;
                        $vitalSignR = $ViewPreopNurseVitalSignRow->vitalSignR;
                        $vitalSignO2SAT = $ViewPreopNurseVitalSignRow->vitalSignO2SAT;
                        $vitalSignTemp = $ViewPreopNurseVitalSignRow->vitalSignTemp;
                        //$vitalSignTime = $ViewPreopNurseVitalSignRow["vitalSignTime"];
                        $BP_P_R_O2SAT_Temp[] = ['vitalsign_id' => $vitalsign_id, 'vitalSignBp' => $vitalSignBp, 'vitalSignP' => $vitalSignP, 'vitalSignR' => $vitalSignR, 'vitalSignO2SAT' => $vitalSignO2SAT, 'vitalSignTemp' => $vitalSignTemp];
                    }
                }
                if (($PreopnursingRes->form_status != 'completed' && $PreopnursingRes->form_status != 'not completed') || $PreopnursingRes->saveFromChart == '1') {
                    $preopnursecategoryQry = "SELECT * FROM preopnursecategory ORDER BY categoryId";
                    $preopnursecategoryRows = DB::select($preopnursecategoryQry);

                    if ($preopnursecategoryRows) {
                        foreach ($preopnursecategoryRows as $preopnursecategoryRow) {
                            $categoryId = $preopnursecategoryRow->categoryId;
                            $categoryName = $preopnursecategoryRow->categoryName;
                            $preopnursequestionQry = "SELECT * FROM preopnursequestion WHERE preOpNurseCatId='" . $categoryId . "'";
                            $preopnursequestionRes = DB::select($preopnursequestionQry);
                            $Questionairs[] = ['categoryName' => $categoryName, 'preopnursequestionRes' => $preopnursequestionRes];
                        }
                    }
                } else if ($PreopnursingRes->form_status == 'completed' || $PreopnursingRes->form_status == 'not completed') {
                    $preopnursecategoryQry = "SELECT * FROM preopnursequestionadmin WHERE confirmation_id='" . $pConfirmId . "' GROUP BY categoryName ORDER BY id";
                    $preopnursecategoryRes = DB::select($preopnursecategoryQry);
                    if ($preopnursecategoryRes) {
                        $k = 0;
                        foreach ($preopnursecategoryRes as $preopnursecategoryRow) {
                            $categoryName = $preopnursecategoryRow->categoryName;
                            $k++;
                            $preopnursequestionQry = "SELECT * FROM preopnursequestionadmin WHERE categoryName='" . $categoryName . "' AND confirmation_id='" . $pConfirmId . "' ORDER BY id";
                            $preopnursequestionRes = DB::select($preopnursequestionQry);
                            if ($preopnursequestionRes) {
                                $Questionairs[] = ['categoryName' => $categoryName, 'preopnursequestionRes' => $preopnursequestionRes];
                            }
                        }
                    }
                }
                $data = [
                    "version_num" => isset($PreopnursingRes->version_num) ? $PreopnursingRes->version_num : 0,
                    "Allergy_data" => ['allergies_drug_reaction' => $allergic, 'patientAllergiesGrid' => $patientAllergies, 'allergiesNKDA_patientconfirmation_status' => $allergiesNKDA_patientconfirmation_status],
                    "Medications_data" => ['meds_taken_today' => $medication, 'patient_prescriptions' => $patient_prescriptions, 'medsfromhealthquest' => $qry_med],
                    "List_of_PreOP_Medication_Orders" => $preOpMedicationDetails,
                    "PreopnursingRecord" => $PreopnursingRes,
                    'ReliefNurse' => $relivedNurseRes,
                    'BP_P_R_O2SAT_Temp' => $BP_P_R_O2SAT_Temp,
                    "listfoodtaken" => DB::select("SELECT * FROM `fooddrinkslist` order by name asc"),
                    "PreoperativeComments" => DB::select('select preOpCommentsId,comments from preopcomments order by `preOpCommentsId` ASC'),
                    "Questionairs" => $Questionairs,
                    "signature" => [
                        "surgeon_signature" => ["name" => $PreopnursingRes->signSurgeon1FirstName . " " . $PreopnursingRes->signSurgeon1LastName, "signed_status" => $PreopnursingRes->signSurgeon1Status, "sign_date" => $PreopnursingRes->signSurgeon1DateTime != '0000-00-00 00=>00=>00' ? date("m-d-Y h=>i A", strtotime($PreopnursingRes->signSurgeon1DateTime)) : date("m-d-Y h:i A")],
                        "nurse1_signature" => ["name" => $PreopnursingRes->signNurseFirstName . " " . $PreopnursingRes->signNurseLastName, "signed_status" => $PreopnursingRes->signNurseStatus, "sign_date" => $PreopnursingRes->signNurseDateTime != '0000-00-00 00=>00=>00' ? date("m-d-Y h:i A", strtotime($PreopnursingRes->signNurseDateTime)) : date("m-d-Y h:i A")],
                    ]
                ];
                $requiredStatus = 0;
                $message = " status updated ";
                $status = 1;
            }
        }
        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => $requiredStatus,
                    'data' => $data,
                    'preOpNursingRecordId' => isset($PreopnursingRes->preOpNursingRecordId) ? $PreopnursingRes->preOpNursingRecordId : 0
        ]); // NOT_FOUND (404) being the HTTP response code 
    }

    public function PreOPNursing_save(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $loginUserType = $request->json()->get('UserType') ? $request->json()->get('UserType') : $request->input('UserType');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $preOpNursingRecordId = $request->json()->get('preOpNursingRecordId') ? $request->json()->get('preOpNursingRecordId') : $request->input('preOpNursingRecordId');
        $jsondata = $request->json()->get('jsondata') ? $request->json()->get('jsondata') : $request->input('jsondata');
        $json = json_decode($jsondata);

        $data = [];
        $status = 0;
        $message = " unauthorized ";
        $requiredStatus = [];
        $userId = $this->checkToken($userToken);
        $savedStatus = 0;
        $Medication_grid = [];
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

                // print '<pre>';print_r($json->data);
                $ViewUserNameQry = "select fname,mname,lname,user_type from `users` where  usersId = '" . $userId . "'";
                $ViewUserNameRow = DB::selectone($ViewUserNameQry); // or die($ViewUserNameQry . imw_error());
                $loggedInUserFirstName = $ViewUserNameRow->fname;
                $loggedInUserMiddleName = $ViewUserNameRow->mname;
                $loggedInUserLastName = $ViewUserNameRow->lname;
                $user_type = $ViewUserNameRow->user_type;
                $signOnFileStatus = 'Yes';
                $loggedInUserName = $ViewUserNameRow->lname . ", " . $ViewUserNameRow->fname . " " . $ViewUserNameRow->mname;
                $detailConfirmation = $this->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfirmId);
                $finalizeStatus = $detailConfirmation->finalize_status;
                $allergiesNKDA_patientconfirmation_status = $detailConfirmation->allergiesNKDA_status;
                $noMedicationStatus = $detailConfirmation->no_medication_status;
                $noMedicationComments = $detailConfirmation->no_medication_comments;
                $ascId = $detailConfirmation->ascId;

                $fieldName = "pre_op_nursing_form";
                $pageName = "pre_op_nursing_record.php?patient_id=$patient_id&amp;pConfId=$pConfirmId&amp;ascId=$ascId";
                //CODE FOR DYNAMIC OPTIONS FROM ADMIN	
                $chkPreOpNurseQry = "select * from `preopnursingrecord` where confirmation_id = '" . $pConfirmId . "'";
                $chkPreOpNurseFormStatusRow = DB::selectone($chkPreOpNurseQry); // or die(imw_error());
                if ($chkPreOpNurseFormStatusRow) {
                    //CODE START TO CHECK FORM STATUS
                    $chkPreOpNurseFormStatus = $chkPreOpNurseFormStatusRow->form_status;
                    $chkPreOpNurseVersionNum = $chkPreOpNurseFormStatusRow->version_num;
                    $chkPreOpNurseVersionDateTime = $chkPreOpNurseFormStatusRow->version_date_time;
                    //CODE START TO CHECK FORM STATUS
                }
                $preOpPhysicianOrderRow = DB::selectone("select version_num,form_status from preopphysicianorders where patient_confirmation_id='" . $pConfirmId . "' order by preOpPhysicianOrdersId,prefilMedicationStatus");
                $preOpPhyVersionNum = $preOpPhysicianOrderRow->version_num;
                $preOpPhyFormStatus = $preOpPhysicianOrderRow->form_status;
                if (!($preOpPhyVersionNum) && ($preOpPhyFormStatus == 'completed' || $preOpPhyFormStatus == 'not completed')) {
                    $preOpPhyVersionNum = 1;
                } else if (!($preOpPhyVersionNum) && $preOpPhyFormStatus <> 'completed' && $preOpPhyFormStatus <> 'not completed') {
                    $preOpPhyVersionNum = 2;
                }
                $Questionairs = $json->data->Questionairs; //[0]->preopnursequestionRes
                //  print_r($Questionairs);
                if ($chkPreOpNurseFormStatus != 'completed' && $chkPreOpNurseFormStatus != 'not completed') {
                    $chkpreopnursequestionadminQry = "SELECT * FROM preopnursequestionadmin WHERE confirmation_id ='" . $pConfirmId . "'";
                    $chkpreopnursequestionadminRes = DB::select($chkpreopnursequestionadminQry);
                    if ($chkpreopnursequestionadminRes) {
                        //DO NOTHING
                    } else {
                        foreach ($Questionairs as $Questionair) {
                            $preopnursequestionRes = $Questionair->preopnursequestionRes;
                            foreach ($preopnursequestionRes as $preOpNurseSavequestionRow) {
                                $categoryName = $preOpNurseSavequestionRow->categoryName;
                                $preOpNurseSaveId = $preOpNurseSavequestionRow->id;
                                $preOpNurseSaveQuestionName = stripslashes($preOpNurseSavequestionRow->preOpNurseQuestionName);
                                $preOpNurseSaveChkBoxQuestionName = str_replace(' ', '~', $preOpNurseSaveQuestionName);
                                $preOpNurseSaveChkBoxQuestionName = str_replace('.', 'SXD', $preOpNurseSaveChkBoxQuestionName);
                                $preOpNurseSaveChkBoxQuestionName = str_replace('[', 'SXOSB', $preOpNurseSaveChkBoxQuestionName);
                                $preOpNurseSaveOption = $preOpNurseSavequestionRow->preOpNurseOption;
                                $showTxtBoxStatus = $preOpNurseSavequestionRow->showTxtBoxStatus;
                                $chkpreopnursequestionadminQry = "SELECT * FROM preopnursequestionadmin WHERE confirmation_id ='" . $pConfirmId . "' AND id='" . $preOpNurseSaveId . "'";
                                $chkpreopnursequestionadminRes = DB::select($chkpreopnursequestionadminQry); // or die(imw_error());
                                if ($chkpreopnursequestionadminRes) {
                                    $updatePreOpNurseAdminQry = "UPDATE preopnursequestionadmin SET 
                                                                        preOpNurseOption='" . addslashes($preOpNurseSaveOption) . "'
                                                                        WHERE id='" . $preOpNurseSaveId . "'
                                                                        AND confirmation_id	='" . $pConfirmId . "'
                                                                      ";
                                    $updatePreOpNurseAdminRes = DB::select($updatePreOpNurseAdminQry); // or die(imw_error());
                                } else {
                                    //DO NOTHING
                                    $inspreOpNurseAdminQry = "INSERT INTO preopnursequestionadmin SET 
                                                                    categoryName='" . addslashes($categoryName) . "',
                                                                    preOpNurseQuestionName='" . addslashes($preOpNurseSaveQuestionName) . "',
                                                                    preOpNurseOption='" . addslashes($preOpNurseSaveOption) . "',
                                                                    showTxtBoxStatus	='" . $showTxtBoxStatus . "',
                                                                    confirmation_id	='" . $pConfirmId . "',
                                                                    patient_id	='" . $patient_id . "'
                                                                  ";
                                    $inspreOpNurseAdminRes = DB::select($inspreOpNurseAdminQry);
                                }
                            }
                        }
                    }//END ELSE PART		
                } else if ($chkPreOpNurseFormStatus == 'completed' || $chkPreOpNurseFormStatus == 'not completed') {
                    foreach ($Questionairs as $Questionair) {
                        $preopnursequestionRes = $Questionair->preopnursequestionRes;
                        foreach ($preopnursequestionRes as $preOpNurseSavequestionRow) {
                            $categoryName = $preOpNurseSavequestionRow->categoryName;
                            $preOpNurseSaveId = $preOpNurseSavequestionRow->id;
                            $preOpNurseSaveQuestionName = stripslashes($preOpNurseSavequestionRow->preOpNurseQuestionName);
                            $preOpNurseSaveChkBoxQuestionName = str_replace(' ', '~', $preOpNurseSaveQuestionName);
                            $preOpNurseSaveChkBoxQuestionName = str_replace('.', 'SXD', $preOpNurseSaveChkBoxQuestionName);
                            $preOpNurseSaveChkBoxQuestionName = str_replace('[', 'SXOSB', $preOpNurseSaveChkBoxQuestionName);
                            $preOpNurseSaveOption = $preOpNurseSavequestionRow->preOpNurseOption;
                            $showTxtBoxStatus = $preOpNurseSavequestionRow->showTxtBoxStatus;
                            $chkpreopnursequestionadminQry = "SELECT * FROM preopnursequestionadmin WHERE confirmation_id ='" . $pConfirmId . "' AND id='" . $preOpNurseSaveId . "'";
                            $chkpreopnursequestionadminRes = DB::select($chkpreopnursequestionadminQry); // or die(imw_error());
                            if ($chkpreopnursequestionadminRes) {
                                $updatePreOpNurseAdminQry = "UPDATE preopnursequestionadmin SET 
                                                                        preOpNurseOption='" . addslashes($preOpNurseSaveOption) . "'
                                                                        WHERE id='" . $preOpNurseSaveId . "'
                                                                        AND confirmation_id	='" . $pConfirmId . "'
                                                                      ";
                                $updatePreOpNurseAdminRes = DB::select($updatePreOpNurseAdminQry); // or die(imw_error());
                            } else {
                                //DO NOTHING
                                $inspreOpNurseAdminQry = "INSERT INTO preopnursequestionadmin SET 
                                                                    categoryName='" . addslashes($categoryName) . "',
                                                                    preOpNurseQuestionName='" . addslashes($preOpNurseSaveQuestionName) . "',
                                                                    preOpNurseOption='" . addslashes($preOpNurseSaveOption) . "',
                                                                    showTxtBoxStatus	='" . $showTxtBoxStatus . "',
                                                                    confirmation_id	='" . $pConfirmId . "',
                                                                    patient_id	='" . $patient_id . "'
                                                                  ";
                                 $inspreOpNurseAdminRes = DB::select($inspreOpNurseAdminQry);
                            }
                        }
                    }
                }
                //END CODE FOR DYNAMIC QUESTION FROM ADMIN
                $version2Query = "";
                $version_num = $chkPreOpNurseVersionNum;
                if (!$chkPreOpNurseVersionNum) {
                    $version_date_time = $chkPreOpNurseVersionDateTime;
                    if ($version_date_time == '' || $version_date_time == '0000-00-00 00:00:00') {
                        $version_date_time = date('Y-m-d H:i:s');
                    }
                    if ($chkPreOpNurseFormStatus == 'completed' || $chkPreOpNurseFormStatus == 'not completed') {
                        $version_num = 1;
                    } else {
                        $version_num = 2;
                    }
                    $version2Query .= ", version_num =	'" . $version_num . "', version_date_time	=	'" . $version_date_time . "' ";
                }

                if (($version_num > 1 && $preOpPhyVersionNum <> 1 ) || $preOpPhyVersionNum > 1) {
                    $comments = addslashes($json->data->PreopnursingRecord->preOpComments);
                    $chbx_saline_lockStart = $json->data->PreopnursingRecord->chbx_saline_lockStart;
                    $chbx_saline_lock = $json->data->PreopnursingRecord->chbx_saline_lock;
                    $ivSelection = $json->data->PreopnursingRecord->ivSelection;
                    $ivSelectionOther = $json->data->PreopnursingRecord->ivSelectionOther;
                    $ivSelectionSide = $json->data->PreopnursingRecord->ivSelectionSide;

                    $gauge = $gauge_other = $txtbox_other_new = $chbx_KVO = $chbx_rate = $txtbox_rate = $chbx_flu = $txtbox_flu = '';
                    if ($chbx_saline_lock == 'iv') {
                        $chbx_KVO = $json->data->PreopnursingRecord->chbx_KVO;
                        $chbx_rate = $json->data->PreopnursingRecord->chbx_rate;
                        $txtbox_rate = addslashes($json->data->PreopnursingRecord->txtbox_rate);
                        $chbx_flu = $json->data->PreopnursingRecord->chbx_flu;
                        $txtbox_flu = addslashes($json->data->PreopnursingRecord->txtbox_flu);
                    }
                    if (($chbx_saline_lock == 'iv' || $chbx_saline_lockStart == 'saline') && $ivSelection <> '' && $ivSelection <> 'other') {
                        $gauge = $json->data->PreopnursingRecord->gauge;
                        $gauge_other = ($gauge == 'other') ? addslashes($json->data->PreopnursingRecord->gauge_other) : '';
                        $txtbox_other_new = addslashes($json->data->PreopnursingRecord->txtbox_other_new);
                    }
                    $version2Query .= ", comments = '" . $comments . "', chbx_saline_lockStart = '" . $chbx_saline_lockStart . "', chbx_saline_lock = '" . $chbx_saline_lock . "', ivSelection = '" . $ivSelection . "', ivSelectionOther = '" . $ivSelectionOther . "', ivSelectionSide =	'" . $ivSelectionSide . "', chbx_KVO	=	'" . $chbx_KVO . "', chbx_rate = '" . $chbx_rate . "', txtbox_rate = '" . $txtbox_rate . "', chbx_flu =	'" . $chbx_flu . "', txtbox_flu	= '" . $txtbox_flu . "', gauge = '" . $gauge . "', txtbox_other_new = '" . $txtbox_other_new . "', gauge_other	=	'" . $gauge_other . "'";
                }

                $chkBoxNSChk = $json->data->PreopnursingRecord->NA;
                $bsvalueChk = ($chkBoxNSChk) ? '' : $json->data->PreopnursingRecord->bsValue;
                $allergies_status_reviewed = $json->data->PreopnursingRecord->allergies_status_reviewed;
                //$preopNurseTime = trim($_POST['preopNurseTime']);
                $preopNurseTime = $this->setTmFormat(trim($json->data->PreopnursingRecord->preopNurseTime), 'static');
                $foodDrinkToday = $json->data->PreopnursingRecord->foodDrinkToday;
                $listFoodTake = addslashes($json->data->PreopnursingRecord->listFoodTake);
                $labTest = $json->data->PreopnursingRecord->labTest;
                $ekg = $json->data->PreopnursingRecord->ekg;
                $consentSign = $json->data->PreopnursingRecord->consentSign;
                $hp = $json->data->PreopnursingRecord->hp;
                $admitted2Hospital = $json->data->PreopnursingRecord->admitted2Hospital;
                $reason = addslashes($json->data->PreopnursingRecord->reason);
                if ($admitted2Hospital == "" || $admitted2Hospital == "No") {
                    $reason = "";
                }
                $healthQuestionnaire = $json->data->PreopnursingRecord->healthQuestionnaire;
                $standingOrders = $json->data->PreopnursingRecord->standingOrders;
                $patVoided = $json->data->PreopnursingRecord->patVoided;

                $hearingAids = $json->data->PreopnursingRecord->hearingAids;
                $hearingAidsRemoved = $json->data->PreopnursingRecord->hearingAidsRemoved;
                if ($hearingAids == "" || $hearingAids == "No") {
                    $hearingAidsRemoved = "";
                }
                $denture = $json->data->PreopnursingRecord->denture;
                $dentureRemoved = $json->data->PreopnursingRecord->dentureRemoved;
                if ($denture == "" || $denture == "No") {   
                    $dentureRemoved = "";
                }
                $anyPain = $json->data->PreopnursingRecord->anyPain;
                $painLevel = $json->data->PreopnursingRecord->painLevel;
                $painLocation = addslashes($json->data->PreopnursingRecord->painLocation);
                $doctorNotified = $json->data->PreopnursingRecord->doctorNotified;
                $patientHeight = $json->data->PreopnursingRecord->patientHeight;
                $patientWeight = $json->data->PreopnursingRecord->patientWeight;
                $patientBMI = $json->data->PreopnursingRecord->patientBmi;

                $preOpComments = addslashes(trim($json->data->PreopnursingRecord->preOpComments));
                $relivedNurseId = $json->data->PreopnursingRecord->relivedNurseId;

                //START CODE TO CHECK NURSE SIGN IN DATABASE
                $chkNurseSignDetails = $this->getRowRecord('preopnursingrecord', 'confirmation_id', $pConfirmId);
                if ($chkNurseSignDetails) {
                    $chk_signNurseId = $chkNurseSignDetails->signNurseId;
                }
                //END CODE TO CHECK NURSE SIGN IN DATABASE 
                $chkdata = false;
                if ($chkBoxNSChk == "" && $bsvalueChk != "") {
                    $chkdata = true;
                } elseif ($chkBoxNSChk != "" && $bsvalueChk == "") {
                    $chkdata = true;
                }
                $vitalSignChk = false;
                // print_r($json->data->BP_P_R_O2SAT_Temp);
                //START SAVE VITAL SIGN ENTRIES IN vitalsign_tbl
                $postVitalSignBp = trim(substr($json->data->BP_P_R_O2SAT_Temp[0]->vitalSignBp, 0, 7));
                $postVitalSignP = trim(substr($json->data->BP_P_R_O2SAT_Temp[0]->vitalSignP, 0, 3));
                $postVitalSignR = trim(substr($json->data->BP_P_R_O2SAT_Temp[0]->vitalSignR, 0, 3));
                $postVitalSignO2SAT = trim(substr($json->data->BP_P_R_O2SAT_Temp[0]->vitalSignO2SAT, 0, 3));
                $postVitalSignTemp = trim(substr($json->data->BP_P_R_O2SAT_Temp[0]->vitalSignTemp, 0, 3));
                $postVitalSignTime = date('Y-m-d H:i:s');


                if ($postVitalSignBp != '' || $postVitalSignP != '' || $postVitalSignR != '' || $postVitalSignO2SAT != '' || $postVitalSignTemp != '') {
                    $Chkqry = "select patient_id from `preopnursing_vitalsign_tbl` where 
                                                    vitalSignBp = '" . substr($json->data->BP_P_R_O2SAT_Temp[0]->vitalSignBp, 0, 7) . "' and
                                                    vitalSignP = '" . substr($json->data->BP_P_R_O2SAT_Temp[0]->vitalSignP, 0, 3) . "' and 
                                                    vitalSignR = '" . substr($json->data->BP_P_R_O2SAT_Temp[0]->vitalSignR, 0, 3) . "' and
                                                    vitalSignO2SAT = '" . substr($json->data->BP_P_R_O2SAT_Temp[0]->vitalSignO2SAT, 0, 3) . "' and
                                                    vitalSignTemp = '" . substr($json->data->BP_P_R_O2SAT_Temp[0]->vitalSignTemp, 0, 3) . "' and
                                                    ascId='" . $ascId . "' and 
                                                    confirmation_id='" . $pConfirmId . "' and
                                                    patient_id = '" . $patient_id . "'";
                    if (!DB::select($Chkqry)) {
                        $SavePostVSignQry = "insert into `preopnursing_vitalsign_tbl` set 
                                                    vitalSignBp = '" . substr($json->data->BP_P_R_O2SAT_Temp[0]->vitalSignBp, 0, 7) . "',
                                                    vitalSignP = '" . substr($json->data->BP_P_R_O2SAT_Temp[0]->vitalSignP, 0, 3) . "', 
                                                    vitalSignR = '" . substr($json->data->BP_P_R_O2SAT_Temp[0]->vitalSignR, 0, 3) . "',
                                                    vitalSignO2SAT = '" . substr($json->data->BP_P_R_O2SAT_Temp[0]->vitalSignO2SAT, 0, 3) . "',
                                                    vitalSignTemp = '" . substr($json->data->BP_P_R_O2SAT_Temp[0]->vitalSignTemp, 0, 3) . "',
                                                    vitalSignTime='" . date("Y-m-d H:i:s") . "',
                                                    ascId='" . $ascId . "', 
                                                    confirmation_id='" . $pConfirmId . "',
                                                    patient_id = '" . $patient_id . "'";
                        $SavePostVitalSignRes = DB::select($SavePostVSignQry); // or die(imw_error());
                        if ($SavePostVitalSignRes) {
                            $vitalSignChk = true;
                        }
                    }
                }
                if ($vitalSignChk == false) {
                    $qryChckVitalSignRes = "SELECT vitalsign_id from preopnursing_vitalsign_tbl where ascId='" . $ascId . "'
                                            and	confirmation_id='" . $pConfirmId . "' and patient_id = '" . $patient_id . "'";
                    $resChckVitalSignRes = DB::select($qryChckVitalSignRes);
                    if ($resChckVitalSignRes) {
                        $vitalSignChk = true;
                    }
                }
                //END SAVE VITAL SIGN ENTRIES IN vitalsign_tbl
                //SET FORM STATUS ACCORDING TO MANDATORY FIELD
                $form_status = "completed";
                if ($foodDrinkToday == "" || $labTest == "" || $ekg == "" || $consentSign == "" || $hp == "" || $admitted2Hospital == "" || $healthQuestionnaire == "" || $standingOrders == "" || $patVoided == "" || $anyPain == "" || $doctorNotified == "" || $hearingAids == "" || $denture == "" || $chk_signNurseId == "0" || $chkdata === false || $vitalSignChk == false) {
                    $form_status = "not completed";
                }
                //END SET FORM STATUS ACCORDING TO MANDATORY FIELD
                $chkPreopnursingQry = "select * from `preopnursingrecord` where  confirmation_id = '" . $pConfirmId . "'";
                $chkPreopnursingRes = DB::selectone($chkPreopnursingQry); // or die(imw_error());

                if ($chkPreopnursingRes) {
                    //CODE START TO CHECK FORM STATUS (IF EMPTY THEN REFRESH SLIDER ON SAVE)
                    $chk_form_status = $chkPreopnursingRes->form_status;
                    //CODE START TO CHECK FORM STATUS (IF EMPTY THEN REFRESH SLIDER ON SAVE)
                    //CODE TO MAKE preOpComments FIELD EMPTY 
                    DB::select("update `preopnursingrecord` set preOpComments='' WHERE confirmation_id='" . $pConfirmId . "'");

                    //CODE TO MAKE preOpComments FIELD EMPTY 		  
                    $SavePreopnursingQry = "update `preopnursingrecord` set 
                                            preopNurseTime ='" . $json->data->PreopnursingRecord->preopNurseTime . "',
                                            foodDrinkToday ='" . $json->data->PreopnursingRecord->foodDrinkToday . "',
                                            allergies_status_reviewed ='" . $json->data->PreopnursingRecord->allergies_status_reviewed . "',
                                            listFoodTake ='" . $json->data->PreopnursingRecord->listFoodTake . "', 
                                            labTest ='" . $json->data->PreopnursingRecord->labTest . "',
                                            ekg ='" . $json->data->PreopnursingRecord->ekg . "',
                                            consentSign ='" . $json->data->PreopnursingRecord->consentSign . "',
                                            hp ='" . $json->data->PreopnursingRecord->hp . "',
                                            admitted2Hospital ='" . $json->data->PreopnursingRecord->admitted2Hospital . "',
                                            reason ='" . $json->data->PreopnursingRecord->reason . "',
                                            healthQuestionnaire ='" . $json->data->PreopnursingRecord->healthQuestionnaire . "',
                                            standingOrders ='" . $json->data->PreopnursingRecord->standingOrders . "',
                                            patVoided ='" . $json->data->PreopnursingRecord->patVoided . "',
                                            hearingAids ='" . $json->data->PreopnursingRecord->hearingAids . "',
                                            hearingAidsRemoved = '" . $json->data->PreopnursingRecord->hearingAidsRemoved . "',
                                            denture = '" . $json->data->PreopnursingRecord->denture . "',
                                            anyPain = '" . $json->data->PreopnursingRecord->anyPain . "',
                                            painLevel ='" . $json->data->PreopnursingRecord->painLevel . "',
                                            painLocation ='" . $json->data->PreopnursingRecord->painLocation . "',
                                            doctorNotified ='" . $json->data->PreopnursingRecord->doctorNotified . "',
                                            dentureRemoved ='" . $json->data->PreopnursingRecord->dentureRemoved . "',
                                            patientHeight ='" . addslashes($json->data->PreopnursingRecord->patientHeight) . "',
                                            patientWeight ='" . $json->data->PreopnursingRecord->patientWeight . "',
                                            patientBmi	='" . $json->data->PreopnursingRecord->patientBmi . "',
                                            vitalSignBp  = '" . $json->data->PreopnursingRecord->vitalSignBp . "',
                                            vitalSignP  = '" . $json->data->PreopnursingRecord->vitalSignP . "',
                                            vitalSignR  ='" . $json->data->PreopnursingRecord->vitalSignR . "',
                                            vitalSignO2SAT ='" . $json->data->PreopnursingRecord->vitalSignO2SAT . "',
                                            vitalSignTemp  ='" . $json->data->PreopnursingRecord->vitalSignTemp . "',
                                            preOpComments = '" . $json->data->PreopnursingRecord->preOpComments . "', 
                                            relivedNurseId ='" . $json->data->PreopnursingRecord->relivedNurseId . "', 
                                            preopnursingSaveDateTime ='" . date("Y-m-d H:i:s", strtotime($json->data->signature->nurse1_signature->sign_date)) . "',
                                            form_status ='" . $chk_form_status . "',
                                            confirmation_id='" . $pConfirmId . "',
                                            NA ='" . $json->data->PreopnursingRecord->NA . "',
                                            bsValue ='" . $json->data->PreopnursingRecord->bsValue . "',
                                            saveFromChart =0
                                            " . $version2Query . "
                                            WHERE confirmation_id='" . $pConfirmId . "'";
                } else {
                    $SavePreopnursingQry = "insert into `preopnursingrecord` set 
                                            preopNurseTime ='" . $json->data->PreopnursingRecord->preopNurseTime . "',
                                            foodDrinkToday ='" . $json->data->PreopnursingRecord->foodDrinkToday . "',
                                            allergies_status_reviewed ='" . $json->data->PreopnursingRecord->allergies_status_reviewed . "',
                                            listFoodTake ='" . $json->data->PreopnursingRecord->listFoodTake . "', 
                                            labTest ='" . $json->data->PreopnursingRecord->labTest . "',
                                            ekg ='" . $json->data->PreopnursingRecord->ekg . "',
                                            consentSign ='" . $json->data->PreopnursingRecord->consentSign . "',
                                            hp ='" . $json->data->PreopnursingRecord->hp . "',
                                            admitted2Hospital ='" . $json->data->PreopnursingRecord->admitted2Hospital . "',
                                            reason ='" . $json->data->PreopnursingRecord->reason . "',
                                            healthQuestionnaire ='" . $json->data->PreopnursingRecord->healthQuestionnaire . "',
                                            standingOrders ='" . $json->data->PreopnursingRecord->standingOrders . "',
                                            patVoided ='" . $json->data->PreopnursingRecord->patVoided . "',
                                            hearingAids ='" . $json->data->PreopnursingRecord->hearingAids . "',
                                            hearingAidsRemoved = '" . $json->data->PreopnursingRecord->hearingAidsRemoved . "',
                                            denture = '" . $json->data->PreopnursingRecord->denture . "',
                                            anyPain = '" . $json->data->PreopnursingRecord->anyPain . "',
                                            painLevel ='" . $json->data->PreopnursingRecord->painLevel . "',
                                            painLocation ='" . $json->data->PreopnursingRecord->painLocation . "',
                                            doctorNotified ='" . $json->data->PreopnursingRecord->doctorNotified . "',
                                            dentureRemoved ='" . $json->data->PreopnursingRecord->dentureRemoved . "',
                                            patientHeight ='" . $json->data->PreopnursingRecord->patientHeight . "',
                                            patientWeight ='" . $json->data->PreopnursingRecord->patientWeight . "',
                                            patientBmi	='" . $json->data->PreopnursingRecord->patientBmi . "',
                                            vitalSignBp  = '" . $json->data->PreopnursingRecord->vitalSignBp . "',
                                            vitalSignP  = '" . $json->data->PreopnursingRecord->vitalSignP . "',
                                            vitalSignR  ='" . $json->data->PreopnursingRecord->vitalSignR . "',
                                            vitalSignO2SAT ='" . $json->data->PreopnursingRecord->vitalSignO2SAT . "',
                                            vitalSignTemp  ='" . $json->data->PreopnursingRecord->vitalSignTemp . "',
                                            preOpComments = '" . $json->data->PreopnursingRecord->preOpComments . "', 
                                            relivedNurseId ='" . $json->data->PreopnursingRecord->relivedNurseId . "', 
                                            preopnursingSaveDateTime ='" . date("Y-m-d H:i:s", strtotime($json->data->signature->nurse1_signature->sign_date)) . "',
                                            form_status ='" . $chk_form_status . "',
                                            confirmation_id='" . $pConfirmId . "',
                                            NA ='" . $json->data->PreopnursingRecord->NA . "',
                                            bsValue ='" . $json->data->PreopnursingRecord->bsValue . "',
                                            saveFromChart =0
                                                " . $version2Query . "
                                                ";
                }
                // echo $SavePreopnursingQry;
                $SavePreopnursingRes = DB::select($SavePreopnursingQry); // or die($SavePreopnursingQry . imw_error());
                //SAVE ENTRY IN chartnotes_change_audit_tbl 

                $Medication_data = isset($json->data->Medications_data->patient_prescriptions) ? $json->data->Medications_data->patient_prescriptions : [];
                if (!empty($Medication_data)) {
                    $this->patient_medication_save($Medication_data, $pConfirmId, $patient_id, $userId, $loggedInUserName);
                }
                $Allergies_data = isset($json->data->Allergy_data->patientAllergiesGrid) ? $json->data->Allergy_data->patientAllergiesGrid : [];
                if (!empty($Allergies_data)) {
                    $this->patient_allergy_save($Allergies_data, $pConfirmId, $patient_id, $userId, $loggedInUserName);
                }

                $List_of_PreOP_Medication_Orders = $json->data->List_of_PreOP_Medication_Orders;
                if (!empty($List_of_PreOP_Medication_Orders)) {
                    $this->patient_pre_op_medication_save($List_of_PreOP_Medication_Orders, $pConfirmId, $patient_id);
                }
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
                //delete allregy(if chbx_drug_react==yes) when save button clicked and set allergies status in patient confirmation
                if ($allergies_status_reviewed == 'Yes') {
                    DB::select("delete from patient_allergies_tbl where patient_confirmation_id = '$pConfirmId'");
                }
                $updateNKDAstatusQry = "update patientconfirmation set allergiesNKDA_status = '" . $allergies_status_reviewed . "' where patientConfirmationId = '$pConfirmId'";
                $updateNKDAstatusRes = DB::select($updateNKDAstatusQry);
                //end delete(if chbx_drug_react==yes) when save button clicked and set allergies status in patient confirmation

                $save = 'true';
                //CODE TO CHECK NURSE ALL SIGNATURE AND SET VALUE IN STUB TABLE
                $recentChartSaved = "";
                if (trim($preopNurseTime)) {
                    $recentChartSavedQry = ", recentChartSaved = 'preopnursingrecord' ";
                }
                $chartSignedByNurse = $this->chkNurseSignNew($pConfirmId);
                $updateNurseStubTblQry = "UPDATE stub_tbl SET chartSignedByNurse='" . $chartSignedByNurse . "' " . $recentChartSavedQry . " WHERE patient_confirmation_id='" . $pConfirmId . "' AND patient_confirmation_id!='0'";

                $updateNurseStubTblRes = DB::select($updateNurseStubTblQry);
                //END CODE TO CHECK NURSE SIGNATURE AND SET VALUE IN STUB TABLE
                $status = 1;
                $message = ' Saved successfully !';
                $requiredStatus = 0;
                $savedStatus = 1;
            }
        }
        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => $requiredStatus,
                    'savedStatus' => $savedStatus,
                    'data' => [],]); // NOT_FOUND (404) being the HTTP response code 
    }

    public function PreOPNursing_delete(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $vitalsign_idPrimary = $request->json()->get('vitalsign_idPrimary') ? $request->json()->get('vitalsign_idPrimary') : $request->input('vitalsign_idPrimary');
        $data = [];
        $status = 0;
        $delStatus = 0;
        $message = " unauthorized ";
        $requiredStatus = [];
        $userId = $this->checkToken($userToken);
        $consent_content = '';
        $savedStatus = 0;
        if ($userId > 0) {
            if ($vitalsign_idPrimary == "") {
                $message = " vitalsign idPrimary is missing ";
                $status = 1;
                $requiredStatus = 0;
            } else {
                $message = " Record deleted successfully ! ";
                $delStatus = 1;
                $status = 1;
                DB::table('preopnursing_vitalsign_tbl')->where('vitalsign_id', $vitalsign_idPrimary)->delete();
            }
        }
        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => $requiredStatus,
                    'delStatus' => $delStatus,
                    'data' => $data,]); // NOT_FOUND (404) being the HTTP response code 
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

    public function patient_pre_op_medication_save($PreOPMedication_data, $pConfirmId, $patient_id) {
        if (!empty($PreOPMedication_data)) {
            foreach ($PreOPMedication_data as $medications) {
                if ($medications->medicationName != '') {
                    $medicationsArr['patient_confirmation_id'] = $pConfirmId;
                    $medicationsArr['preOpPhyOrderId'] = addslashes($medications->preOpPhyOrderId);
                    $medicationsArr['medicationName'] = addslashes($medications->medicationName);
                    $medicationsArr['strength'] = addslashes($medications->strength);
                    $medicationsArr['timemeds'] = $medications->timemeds;
                    $medicationsArr['timemeds1'] = $medications->timemeds1;
                    $medicationsArr['timemeds2'] = $medications->timemeds2;
                    $medicationsArr['timemeds3'] = $medications->timemeds3;
                    $medicationsArr['timemeds4'] = $medications->timemeds4;
                    $medicationsArr['timemeds5'] = $medications->timemeds5;
                    $medicationsArr['timemeds6'] = $medications->timemeds6;
                    $medicationsArr['timemeds7'] = $medications->timemeds7;
                    $medicationsArr['timemeds8'] = $medications->timemeds8;
                    $medicationsArr['timemeds9'] = $medications->timemeds9;
                    if ($medications->patientPreOpMediId > 0) {
                        DB:: table('patientpreopmedication_tbl')->where('patientPreOpMediId', $medications->patientPreOpMediId)->update($medicationsArr);
                    } else {
                        DB::table('patientpreopmedication_tbl')->insert($medicationsArr);
                    }
                }
            }
        }
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

}
