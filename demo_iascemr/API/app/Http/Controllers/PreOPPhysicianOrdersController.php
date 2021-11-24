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

class PreOPPhysicianOrdersController extends Controller {

    public function PreOPPhysicianOrders_form(Request $request) {
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
                $getPreOpPhyDetails = $this->getRowRecord('preopphysicianorders', 'patient_confirmation_id', $pConfirmId);

                $form_status = $getPreOpPhyDetails->form_status;
                $saveFromChart = $getPreOpPhyDetails->saveFromChart;
                $chbx_noted_by_nurse = $getPreOpPhyDetails->notedByNurse;
                $versionDateTime = $getPreOpPhyDetails->version_date_time;
                $evaluatedPatient = $getPreOpPhyDetails->evaluatedPatient;
                $preOpOrdersOther = $getPreOpPhyDetails->preOpOrdersOther;

                $version_num = $getPreOpPhyDetails->version_num;
                $versionDateTime = $getPreOpPhyDetails->version_date_time;

                if (!($version_num) && ($form_status == 'completed' || $form_status == 'not completed')) {
                    $version_num = 1;
                } else if (!($version_num) && $form_status <> 'completed' && $form_status <> 'not completed') {
                    $version_num = 3;
                }
                // GETTING IF PRE OP PHYSICIAN RECORD IS SAVED OR NOT
                //GETTING SURGEON PROFILE TO SHOW FIRST VIEW OF SURGEONID
                $detailConfirmation = $this->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfirmId);
                $surgeonId = $detailConfirmation->surgeonId;
                $otherPreOpOrdersFound = "";
                $surgeonProfileIdFound = "";
                $selectSurgeonQry = "select * from surgeonprofile where surgeonId = '$surgeonId' and del_status=''";
                $selectSurgeonRes = DB::select($selectSurgeonQry);
                foreach ($selectSurgeonRes as $selectSurgeonRow) {
                    $surgeonProfileIdArr[] = $selectSurgeonRow->surgeonProfileId;
                }
                if (is_array($surgeonProfileIdArr)) {
                    $surgeonProfileIdImplode = implode(',', $surgeonProfileIdArr);
                } else {
                    $surgeonProfileIdImplode = 0;
                }
                //GETTING SURGEON PROFILE TO SHOW FIRST VIEW OF SURGEONID
                //SHOW DETAIL OF PATIENT PRE OP MEDICATION

                $preOpPatientDetails = DB::select("select * from patientpreopmedication_tbl where patient_confirmation_id='$pConfirmId' order by patientPreOpMediId ASC");
                if ($preOpPatientDetails) {
                    foreach ($preOpPatientDetails as $details) {
                        if ($version_num > 1) {
                            $Medication_grid[] = ['patientPreOpMediId' => $details->patientPreOpMediId, 'Medication' => $details->medicationName, 'Strength' => $details->strength, 'Direction' => $details->direction, 'timemeds' => $details->timemeds, 'timemeds1' => $details->timemeds1, 'timemeds2' => $details->timemeds2, 'timemeds3' => $details->timemeds3, 'timemeds4' => $details->timemeds4, 'timemeds5' => $details->timemeds5, 'timemeds6' => $details->timemeds6, 'timemeds7' => $details->timemeds7, 'timemeds8' => $details->timemeds8, 'timemeds9' => $details->timemeds9];
                        } elseif ($version_num < 2) {
                            $Medication_grid[] = ['patientPreOpMediId' => $details->patientPreOpMediId, 'Medication' => $details->medicationName, 'Strength' => $details->strength, 'Direction' => $details->direction, 'timemeds' => $details->timemeds, 'timemeds1' => $details->timemeds1, 'timemeds2' => $details->timemeds2, 'timemeds3' => $details->timemeds3, 'timemeds4' => $details->timemeds4, 'timemeds5' => $details->timemeds5, 'timemeds6' => $details->timemeds6, 'timemeds7' => $details->timemeds7, 'timemeds8' => $details->timemeds8, 'timemeds9' => $details->timemeds9];
                        }
                    }
                }
                $medication_query = "SELECT medicationsId,name,isDefault FROM `medications` ORDER BY `medications`.`name` ASC ";
                $medication_res = DB::select($medication_query);
                $OtherPreOPOrders = [];
                foreach ($medication_res as $row) {
                    $OtherPreOPOrders[] = ['medicationsId' => $row->medicationsId, 'name' => $row->name, 'isDefault' => $row->isDefault];
                }
                $preopmedicationorder = "select * from preopmedicationorder where medicationName!='' order by medicationName asc";
                $preopmedicationorder_res = DB::select($preopmedicationorder);
                $Medication_Dropdown = [];
                foreach ($preopmedicationorder_res as $rows) {
                    $Medication_Dropdown[] = ['preOpMedicationOrderId' => $rows->preOpMedicationOrderId, 'medicationName' => $rows->medicationName, 'strength' => $rows->strength, 'directions' => $rows->directions];
                }
                //SHOW DETAIL OF PATIENT PRE OP MEDICATION
                if ($form_status != "not completed" && $form_status != "completed") {
                    $preOpOrdersOther = $otherPreOpOrdersFound;
                }
                $Qry = "select usersId,concat(lname,fname) as name from users where user_type='Nurse' and deleteStatus<>'Yes' ORDER BY lname";
                $res = DB::select($Qry);
                $data = [
                    "version_num" => $version_num,
                    "Medication_grid" => $Medication_grid,
                    "ADDMedication_Dropdown" => $Medication_Dropdown,
                    "OtherPreOPOrders" => $OtherPreOPOrders,
                    "Pre-Op-orders-noted-by-nurse" => $chbx_noted_by_nurse,
                    "I-have-evaluated" => $evaluatedPatient,
                    "ReliefNurse" => $res,
                    "PreOpPhyDetails" => $getPreOpPhyDetails,
                    'postop_physician' => ["hand" => "Hand", "wrist" => "Wrist", "arm" => "Arm", "antecubital" => "Antecubital", "other" => "Other"],
                    "signature" => [
                        "surgeon_signature" => ["name" => $getPreOpPhyDetails->signSurgeon1FirstName . " " . $getPreOpPhyDetails->signSurgeon1LastName, "signed_status" => $getPreOpPhyDetails->signSurgeon1Status, "sign_date" => $getPreOpPhyDetails->signSurgeon1DateTime != '0000-00-00 00:00:00' ? date("m-d-Y h:i A", strtotime($getPreOpPhyDetails->signSurgeon1DateTime)) : date("m-d-Y h:i A")],
                        "nurse1_signature" => ["name" => $getPreOpPhyDetails->signNurseFirstName . " " . $getPreOpPhyDetails->signNurseLastName, "signed_status" => $getPreOpPhyDetails->signNurseStatus, "sign_date" => $getPreOpPhyDetails->signNurseDateTime != '0000-00-00 00:00:00' ? date("m-d-Y h:i A", strtotime($getPreOpPhyDetails->signNurseDateTime)) : date("m-d-Y h:i A")],
                        "nurse_signature" => ["name" => $getPreOpPhyDetails->signNurse1FirstName . " " . $getPreOpPhyDetails->signNurse1LastName, "signed_status" => $getPreOpPhyDetails->signNurse1Status, "sign_date" => $getPreOpPhyDetails->signNurse1DateTime != '0000-00-00 00:00:00' ? date("m-d-Y h:i A", strtotime($getPreOpPhyDetails->signNurse1DateTime)) : date("m-d-Y h:i A")]
                    ]
                ];
                $preOpPhysicianOrdersId = $getPreOpPhyDetails->preOpPhysicianOrdersId;
                $requiredStatus = 0;
                $message = " status updated ";
                $status = 1;
            }
        }
        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => $requiredStatus,
                    'preOpPhysicianOrdersId' => (isset($preOpPhysicianOrdersId) && $preOpPhysicianOrdersId > 0) ? $preOpPhysicianOrdersId : 0,
                    'data' => $data,
        ]); // NOT_FOUND (404) being the HTTP response code 
    }

    public function PreOPPhysicianOrders_save(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $loginUserType = $request->json()->get('UserType') ? $request->json()->get('UserType') : $request->input('UserType');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        //$preOpPhysicianOrdersId = $request->json()->get('preOpPhysicianOrdersId') ? $request->json()->get('preOpPhysicianOrdersId') : $request->input('preOpPhysicianOrdersId');
        $jsondata = $request->json()->get('jsondata') ? $request->json()->get('jsondata') : $request->input('jsondata');
        $json = json_decode($jsondata);
        $preOpPhysicianOrdersId = $json->PreOpPhyDetails->preOpPhysicianOrdersId;
        $data = [];
        $status = 0;
        $message = " unauthorized ";
        $requiredStatus = [];
        $userId = $this->checkToken($userToken);
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
                $detailConfirmation = $this->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfirmId);
                $surgeonId = $detailConfirmation->surgeonId;
                $anesthesiologist_id = $detailConfirmation->anesthesiologist_id;
                //GET ASSIGNED SURGEON ID AND SURGEON NAME
                $preOpAssignedSurgeonId = $detailConfirmation->surgeonId;
                $preOpAssignedSurgeonName = stripslashes($detailConfirmation->surgeon_name);
                $ascId = $detailConfirmation->ascId;
                $tablename = "preopphysicianorders";
                $formStatus = 'completed';
                unset($arrayRecord);
                //START CODE TO CHECK NURSE,SURGEON SIGN IN DATABASE
                $chkNurseSignDetails = $this->getRowRecord('preopphysicianorders', 'patient_confirmation_id', $pConfirmId);
                if ($chkNurseSignDetails) {
                    $chk_signNurseId = $chkNurseSignDetails->signNurseId;
                    $chk_signSurgeon1Id = $chkNurseSignDetails->signSurgeon1Id;
                    $chk_signNurseId1 = $chkNurseSignDetails->signNurse1Id;
                    //CHECK FORM STATUS
                    $chk_form_status = $chkNurseSignDetails->form_status;
                    //CHECK FORM STATUS
                    $chkVersionNum = $chkNurseSignDetails->version_num;
                    $chkVersionDateTime = $chkNurseSignDetails->version_date_time;
                }
                //END CODE TO CHECK NURSE SIGN IN DATABASE 
                // Add Into Save Array list 
                if ($chk_form_status <> 'completed' && $chk_form_status <> 'not completed') {
                    $arrayRecord['prefilMedicationStatus'] = 'true';
                }
                $arrayRecord['honanBallon'] = '0';
                $version_num = $chkVersionNum;
                if (!$chkVersionNum) {
                    $version_date_time = $chkVersionDateTime;
                    if ($version_date_time == '' || $version_date_time == '0000-00-00 00:00:00') {
                        $version_date_time = date('Y-m-d H:i:s');
                    }

                    if ($chk_form_status == 'completed' || $chk_form_status == 'not completed') {
                        $version_num = 1;
                    } else {
                        $version_num = 3;
                    }
                    $arrayRecord['version_num'] = $version_num;
                    $arrayRecord['version_date_time'] = $version_date_time;
                }

                // Start Validate Chart
                if ($chk_signSurgeon1Id == "0" || $chk_signNurseId1 == "0" || !$json->PreOpPhyDetails->notedByNurse) {
                    $formStatus = 'not completed';
                }

                if ($version_num == 1 && $formStatus == 'completed' && $chk_signNurseId == "0") {
                    $formStatus = 'not completed';
                }
                if ($formStatus == 'completed' && $version_num > 2 && !$json->PreOpPhyDetails->evaluatedPatient) {
                    $formStatus = 'not completed';
                }
                // End Validate Chart
                if ($version_num < 2) {
                    $arrayRecord['ivSelection'] = $json->PreOpPhyDetails->ivSelectionSide;
                    $arrayRecord['ivSelectionOther'] = $json->PreOpPhyDetails->ivSelectionOther; //$_REQUEST['otherHeparinLock'];
                    $arrayRecord['ivSelectionSide'] = $json->PreOpPhyDetails->ivSelection; //right/left
                    $arrayRecord['comments'] = addslashes($json->PreOpPhyDetails->comments);
                    //$arrayRecord['prefilMedicationStatus'] = 'true';
                    $arrayRecord['chbx_heparin_lockStart'] = $json->PreOpPhyDetails->chbx_heparin_lockStart; // $_REQUEST['chbx_heparin_lockStart'];
                    $arrayRecord['chbx_heparin_lock'] = $json->PreOpPhyDetails->chbx_heparin_lock; //$_REQUEST['chbx_heparin_lock'];
                    $chbx_KVO = '';
                    $chbx_rate = '';
                    $txtbox_rate = '';
                    $chbx_flu = '';
                    $txtbox_flu = '';
                    if ($json->PreOpPhyDetails->chbx_heparin_lock == 'iv') {
                        $chbx_KVO = $json->PreOpPhyDetails->chbx_KVO;
                        $chbx_rate = $json->PreOpPhyDetails->chbx_rate; //$_REQUEST['chbx_rate'];
                        $txtbox_rate = $json->PreOpPhyDetails->txtbox_rate; //$_REQUEST['txtbox_rate'];
                        $chbx_flu = $json->PreOpPhyDetails->chbx_flu; // $_REQUEST['chbx_flu'];
                        $txtbox_flu = $json->PreOpPhyDetails->txtbox_flu; //$_REQUEST['txtbox_flu'];
                    }
                    $arrayRecord['chbx_KVO'] = $chbx_KVO;
                    $arrayRecord['chbx_rate'] = $chbx_rate;
                    $arrayRecord['txtbox_rate'] = $txtbox_rate;
                    $arrayRecord['chbx_flu'] = $chbx_flu;
                    $arrayRecord['txtbox_flu'] = $txtbox_flu;
                }
                if ($version_num > 2) {
                    $arrayRecord['evaluatedPatient'] = $json->PreOpPhyDetails->evaluatedPatient; //$_REQUEST['chbx_evaluated_patient'];
                }
                $arrayRecord['preOpOrdersOther'] = addslashes($json->PreOpPhyDetails->preOpOrdersOther);
                $arrayRecord['surgeonId'] = $surgeonId;
                $arrayRecord['anesthesiologistId'] = $anesthesiologist_id;
                $arrayRecord['surgeonSign'] = isset($json->signature->surgeon_signature->signed_status) ? $json->signature->surgeon_signature->signed_status : "";
                $arrayRecord['anesthesiologistSign'] = '';
                $arrayRecord['preOpPhysicianOrdersTime'] = time();
                $arrayRecord['ascId'] = $ascId;
                $arrayRecord['patient_id'] = $patient_id;
                $arrayRecord['saveFromChart'] = 0; //saveFromChart
                $arrayRecord['patient_confirmation_id'] = $pConfirmId;
                $arrayRecord['form_status'] = $formStatus;
                $arrayRecord['relivednurse'] = $json->PreOpPhyDetails->relivednurse; //$_REQUEST['relived_nurse'];
                $arrayRecord['notedByNurse'] = isset($json->PreOpPhyDetails->notedByNurse) ? $json->PreOpPhyDetails->notedByNurse : "";
                $arrayRecord['medicationStartTime'] = ''; //$medicationStartTimeVal
                //MAKE AUDIT STATUS
                unset($arrayStatusRecord);
                $arrayStatusRecord['user_id'] = $userId;
                $arrayStatusRecord['patient_id'] = $patient_id;
                $arrayStatusRecord['confirmation_id'] = $pConfirmId;
                $arrayStatusRecord['form_name'] = 'pre_op_physician_order_form';
                $arrayStatusRecord['action_date_time'] = date('Y-m-d H:i:s');
                //MAKE AUDIT STATUS
                // UPDATE PATIENT STATUS POS
                unset($arrayPatientStatus);
                $arrayPatientStatus['patientStatus'] = 'POS';
                DB::table('patientconfirmation')->where('patientConfirmationId', $pConfirmId)->update($arrayPatientStatus);
                // UPDATE PATIENT STATUS POS

                if (!$preOpPhysicianOrdersId) {
                    $preOpPhysicianOrdersId = DB::table('preopphysicianorders')->insertGetId($arrayRecord);
                } else {
                    DB::table('preopphysicianorders')->where('preOpPhysicianOrdersId', $preOpPhysicianOrdersId)->update($arrayRecord);
                }
                //CODE START TO SET AUDIT STATUS AFTER SAVE
                unset($conditionArr);
                $conditionArr['confirmation_id'] = $pConfirmId;
                $conditionArr['form_name'] = 'pre_op_physician_order_form';
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
                $medication_data = $json->Medication_grid;
                $this->Medication_Save($medication_data, $preOpPhysicianOrdersId, $pConfirmId, $version_num);
                //echo '<pre>';print_r($_REQUEST);
                //CODE TO CHECK SURGEON ALL SIGNATURE AND SET VALUE IN STUB TABLE
                $chartSignedBySurgeon = $this->chkSurgeonSignNew($pConfirmId);
                $updateStubTblQry = "UPDATE stub_tbl SET chartSignedBySurgeon='" . $chartSignedBySurgeon . "' WHERE patient_confirmation_id='" . $pConfirmId . "'";
                $updateStubTblRes = DB::select($updateStubTblQry);
                //END CODE TO CHECK SURGEON SIGNATURE AND SET VALUE IN STUB TABLE
                //CODE TO CHECK NURSE ALL SIGNATURE AND SET VALUE IN STUB TABLE
                $chartSignedByNurse = $this->chkNurseSignNew($pConfirmId);
                $updateNurseStubTblQry = "UPDATE stub_tbl SET chartSignedByNurse='" . $chartSignedByNurse . "' WHERE patient_confirmation_id='" . $pConfirmId . "'";
                $updateNurseStubTblRes = DB::select($updateNurseStubTblQry); // or die(imw_error());
                //END CODE TO CHECK NURSE SIGNATURE AND SET VALUE IN STUB TABLE
                //REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)
                //REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)
                // SAVE SUBMITTED FORM
                $status = 1;
                $message = 'Pre-OP Physician Order has been successfully !';
            }
        }
        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => '',
                    'data' => $data,
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

    public function Medication_Save($preOpPatientMediOrderArr, $preOpPhysicianOrdersId, $pConfId, $version_num) {
        if ($preOpPatientMediOrderArr) {
            foreach ($preOpPatientMediOrderArr as $key => $medicationList) {
                //CHECK MEDICATION ALREADY PREDEFINED OR NOT					
                unset($condArr);
                $condArr['medicationName'] = $medicationList->Medication;
                $condArr['strength'] = $medicationList->Strength;
                $condArr['directions'] = $medicationList->Direction;
                $getExistsOrNot = $this->getMultiChkArrayRecords('preopmedicationorder', $condArr);
                if ($getExistsOrNot) {
                    DB::table('preopmedicationorder')->insert($condArr);
                }
                //CHECK MEDICATION ALREADY PREDEFINED OR NOT

                if ($medicationList != '') {
                    unset($arrayRecord);
                    $arrayRecord['preOpPhyOrderId'] = $medicationList->patientPreOpMediId;
                    $arrayRecord['patient_confirmation_id'] = $pConfId;
                    $arrayRecord['medicationName'] = $medicationList->Medication;
                    $arrayRecord['strength'] = $medicationList->Strength;
                    $arrayRecord['direction'] = $medicationList->Direction;
                    if ($version_num < 2) {
                        $arrayRecord['timemeds'] = $medicationList->timemeds;
                        $arrayRecord['timemeds1'] = $medicationList->timemeds1;
                        $arrayRecord['timemeds2'] = $medicationList->timemeds2;
                        $arrayRecord['timemeds3'] = $medicationList->timemeds3;
                        $arrayRecord['timemeds4'] = $medicationList->timemeds4;
                        $arrayRecord['timemeds5'] = $medicationList->timemeds5;
                        $arrayRecord['timemeds6'] = $medicationList->timemeds6;
                        $arrayRecord['timemeds7'] = $medicationList->timemeds7;
                        $arrayRecord['timemeds8'] = $medicationList->timemeds8;
                        $arrayRecord['timemeds9'] = $medicationList->timemeds9;
                    }
                    if (!$medicationList->patientPreOpMediId) {
                        DB::table('patientpreopmedication_tbl')->insert($arrayRecord);
                    } else {
                        DB::table('patientpreopmedication_tbl')->where('patientPreOpMediId', $medicationList->patientPreOpMediId)->update($arrayRecord);
                    }
                }
            }
        }
    }

    function PreOPPhysician_medication_del(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $patientPreOpMediId = $request->json()->get('patientPreOpMediId') ? $request->json()->get('patientPreOpMediId') : $request->input('patientPreOpMediId');
        $requiredStatus = 0;
        $userId = $this->checkToken($userToken);
        $status = 0;
        $message = " unauthorized ";
        if ($userId > 0) {
            if ($patientPreOpMediId == "") {
                $message = " patientPreOpMediId is missing ";
                $status = 1;
                $requiredStatus = 0;
            } else {
                if ($patientPreOpMediId > 0) {
                    DB::table('patientpreopmedication_tbl')->where('patientPreOpMediId', $patientPreOpMediId)->delete();
                    $status = 1;
                    $message = " Record deleted succcessfully !";
                }
            }
        }
        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => $requiredStatus,
                    'data' => [],
        ]); // NOT_FOUND (404) being the HTTP response code 
    }

}
