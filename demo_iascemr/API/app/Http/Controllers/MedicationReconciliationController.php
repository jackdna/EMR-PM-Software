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

class MedicationReconciliationController extends Controller {

    public function MedicationReconciliation_form(Request $request) {
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
        $start_post_op_drops = '';
        $drop_schedule = '';
        $resume_med = '';
        $discontinue = '';
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
                $message = ' Medication Reconciliation Details ';
                //FETCH DATA FROM  TABLE
                $allergiesPreOp = DB::select('select allergy_name,reaction_name from patient_allergies_tbl where patient_confirmation_id=' . $pConfirmId);
                $healthQuestMed = DB::select("select prescription_medication_id,prescription_medication_reason,prescription_medication_last_dose_taken,prescription_medication_name,prescription_medication_desc,prescription_medication_sig FROM patient_prescription_medication_healthquest_tbl where confirmation_id='" . $pConfirmId . "' order by prescription_medication_name asc");
                $reconDetails = $this->getRowRecord('patient_medication_reconciliation_sheet', 'confirmation_id', $pConfirmId, "", "", " *, date_format(signSurgeon1DateTime,'%m-%d-%Y %h:%i %p') as signSurgeon1DateTimeFormat, date_format(signNurseDateTime,'%m-%d-%Y %h:%i %p') as signNurseDateTimeFormat ");
                $recon_sheet_id = $reconDetails->recon_sheet_id;
                $drop_schedule = $reconDetails->drop_schedule;
                $start_post_op_drops = $reconDetails->start_post_op_drops;
                $resume_med = $reconDetails->resume_med;
                $discontinue = stripslashes($reconDetails->discontinue);
                $surgeonSign = ['signSurgeon1DateTimeFormat' => $reconDetails->signSurgeon1DateTime, 'signSurgeon1Id' => $reconDetails->signSurgeon1Id, 'signSurgeon1FirstName' => $reconDetails->signSurgeon1FirstName, 'signSurgeon1LastName' => $reconDetails->signSurgeon1LastName, 'signSurgeon1Status' => $reconDetails->signSurgeon1Status];
                $nurseSign = ['signNurseDateTime' => $reconDetails->signNurseDateTime, 'signNurseId' => $reconDetails->signNurseId, 'signNurseFirstName' => $reconDetails->signNurseFirstName, 'signNurseLastName' => $reconDetails->signNurseLastName, 'signNurseStatus' => $reconDetails->signNurseStatus];
                $data = ['allergiesPreOp' => $allergiesPreOp ? $allergiesPreOp : [], 'healthQuestMed' => $healthQuestMed ? $healthQuestMed : []];
            }
        }
        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => '',
                    'data' => [$data],
                    'nurseSign' => $nurseSign, 'surgeonSign' => $surgeonSign,
                    'drop_schedule' => $drop_schedule, 'start_post_op_drops' => $start_post_op_drops, 'resume_med' => $resume_med, 'discontinue' => $discontinue,
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

    public function MedicationReconciliation_save(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $loginUserType = $request->json()->get('UserType') ? $request->json()->get('UserType') : $request->input('UserType');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $drop_schedule = $request->json()->get('drop_schedule') ? $request->json()->get('drop_schedule') : $request->input('drop_schedule');
        $start_post_op_drops = $request->json()->get('start_post_op_drops') ? $request->json()->get('start_post_op_drops') : $request->input('start_post_op_drops');
        $resume_med = $request->json()->get('resume_med') ? $request->json()->get('resume_med') : $request->input('resume_med');
        $discontinue = $request->json()->get('discontinue') ? $request->json()->get('discontinue') : $request->input('discontinue');
        $current_medication = $request->json()->get('current_medication') ? $request->json()->get('current_medication') : $request->input('current_medication');
        $signStatus = $request->json()->get('signStatus') ? $request->json()->get('signStatus') : $request->input('signStatus');
        $signdate = $request->json()->get('signDate') ? $request->json()->get('signDate') : $request->input('signDate');
        $data = [];
        $status = 0;
        $moveStatus = 0;
        $saveStatus=0;
        // $requiredStatus = 1;
        $message = " unauthorized ";
        $requiredStatus = [];
        $userId = $this->checkToken($userToken);
        $patient_instruction_id = 0;
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
                $formStatus = 'completed';
                $fieldToValidate = array($drop_schedule, $start_post_op_drops, $resume_med);

                //CHECK FORM STATUS AND SIGN-ACTIVATE
                $chkFormStatusDetails = $this->getRowRecord('patient_medication_reconciliation_sheet', 'confirmation_id', $pConfirmId);
                if ($chkFormStatusDetails) {
                    $chk_formStatus = $chkFormStatusDetails->form_status;
                    $chk_signSurgeon1Id = $chkFormStatusDetails->signSurgeon1Id;
                    $chk_signNurseId = $chkFormStatusDetails->signNurseId;
                    $recon_sheet_id = $chkFormStatusDetails->recon_sheet_id;
                }
                if (!($chk_signNurseId) || !($chk_signSurgeon1Id)) {
                    $formStatus = 'not completed';
                }
                // End Form Validation

                $arrayRecord['drop_schedule'] = $drop_schedule;
                $arrayRecord['start_post_op_drops'] = $start_post_op_drops;
                $arrayRecord['resume_med'] = $resume_med;
                $arrayRecord['discontinue'] = addslashes($discontinue);

                $arrayRecord['form_status'] = $formStatus;

                if (!$recon_sheet_id) {
                    $arrayRecord['confirmation_id'] = $pConfirmId;
                    $recon_sheet_id = DB::table('patient_medication_reconciliation_sheet')->insert($arrayRecord);
                } else {
                    DB::table('patient_medication_reconciliation_sheet')->where('confirmation_id', $pConfirmId)->update($arrayRecord);
                }

                /*                 * ******************************************************
                  Update Last Dose Taken for HEalth Questionnaire Medications
                 * ****************************************************** */

                $idHealthQuestArr = json_decode($current_medication);
                if (is_array($idHealthQuestArr) && count($idHealthQuestArr) > 0) {
                    foreach ($idHealthQuestArr as $idHealthQuestArrs) {
                        $idHQ = $idHealthQuestArrs->item_id;
                        $last_dose_taken = $idHealthQuestArrs->last_dose_taken;
                        $lastDoseTakenHQ = addslashes($last_dose_taken);
                        if ($idHQ) {
                            $updateQry = "Update patient_prescription_medication_healthquest_tbl Set prescription_medication_last_dose_taken = '" . $lastDoseTakenHQ . "' Where prescription_medication_id = '" . $idHQ . "' ";
                            DB::select($updateQry);
                        }
                    }
                }

                /*                 * **********************************************************
                  Update Last Dose Taken for HEalth Questionnaire Medications
                 * ********************************************************* */
                /*                 * *********************************************************
                  Creating Audit Status on Save
                 * ****************************** */

                //MAKE AUDIT STATUS CRATED OR MODIFIED
                unset($arrayStatusRecord);
                $arrayStatusRecord['user_id'] = $userId;
                $arrayStatusRecord['patient_id'] = $patient_id;
                $arrayStatusRecord['confirmation_id'] = $pConfirmId;
                $arrayStatusRecord['form_name'] = 'medication_reconciliation_sheet_form';
                $arrayStatusRecord['action_date_time'] = date('Y-m-d H:i:s');
                //MAKE AUDIT STATUS CRATED OR MODIFIED
                //CODE START TO SET AUDIT STATUS AFTER SAVE
                unset($conditionArr);
                $conditionArr['confirmation_id'] = $pConfirmId;
                $conditionArr['form_name'] = 'medication_reconciliation_sheet_form';
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
                //  if(!empty($signOn)){
                //GET USER NAME
                $ViewUserNameQry = "select fname,mname,lname,user_type from `users` where  usersId = '" . $userId . "'";
                $ViewUserNameRow = DB::selectone($ViewUserNameQry); // or die($ViewUserNameQry . imw_error());
                $loggedInUserFirstName = $ViewUserNameRow->fname;
                $loggedInUserMiddleName = $ViewUserNameRow->mname;
                $loggedInUserLastName = $ViewUserNameRow->lname;
                $user_type = $ViewUserNameRow->user_type;
                $signOnFileStatus = $signStatus != '' ? $signStatus : 'Yes'; // 'Yes';
                $signDateTime = $signdate != '' ? $signdate : date("Y-m-d H:i:s");

                $loggedInUserName = $ViewUserNameRow->lname . ", " . $ViewUserNameRow->fname . " " . $ViewUserNameRow->mname;
                $userIdentity = ucfirst($loginUserType);
                $signUserId = 'sign' . $userIdentity . '1Id';
                $signUserFirstName = 'sign' . $userIdentity . '1FirstName';
                $signUserMiddleName = 'sign' . $userIdentity . '1MiddleName';
                $signUserLastName = 'sign' . $userIdentity . '1LastName';
                $signUserStatus = 'sign' . $userIdentity . '1Status';
                $signUserDateTime = 'sign' . $userIdentity . '1DateTime';

                $tblName = 'patient_medication_reconciliation_sheet';
                //END GET FIELD NAME ACCORDING TO USER IDENTITY			
                $SaveSignQry = "Update " . $tblName . " Set 
                                    $signUserId= '" . $userId . "',
                                    $signUserFirstName= '" . addslashes($loggedInUserFirstName) . "', 
                                    $signUserMiddleName= '" . addslashes($loggedInUserMiddleName) . "',
                                    $signUserLastName= '" . addslashes($loggedInUserLastName) . "', 
                                    $signUserStatus= '" . $signOnFileStatus . "',
                                    $signUserDateTime= '" . $signDateTime . "'
                                    WHERE confirmation_id='" . $pConfirmId . "'";
                                    
                $SaveSignRes = DB::select($SaveSignQry);
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
                //CODE END TO SET AUDIT STATUS AFTER SAVE
                $status = 1;
                $saveStatus=1;
                $message = ' Medication Reconciliation saved successfully ! ';
                //FETCH DATA FROM  TABLE
            }
        }
        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'saveStatus'=>$saveStatus,
                    'requiredStatus' => '',
                    'data' => $data,
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

}
