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

class AmendmentController extends Controller {

    public function amendment_listing(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $data = [];
        $status = 0;
        $moveStatus = 0;
        // $requiredStatus = 1;
        $message = " unauthorized ";
        $requiredStatus = [];
        $userId = $this->checkToken($userToken);
        if ($userId > 0) {
            if ($pConfirmId == "") {
                $message = " ConfirmId is missing ";
                $status = 1;
                $requiredStatus = 0;
            } else {
                $status = 1;
                $message = " Patient amendment nots list ";
                $query_amendment = "SELECT * from amendment WHERE confirmation_id = '$pConfirmId'";
                $result_amendment = DB::select($query_amendment);
                foreach ($result_amendment as $key => $amendment) {
                    $amendmentId = $amendment->amendmentId;
                    $amendmentNotes = $amendment->notes;
                    $dateAmendment = $this->changeDateMDY($amendment->dateAmendment);
                    $timeAmendment = $amendment->timeAmendment;
                    $userIdAmendment = $amendment->userId;
                    $form_status = $amendment->form_status;
                    $getUserNameQry = "SELECT fname,mname,lname,user_type FROM users
                                                WHERE usersId = '$userIdAmendment'";
                    $getUserNameRow = DB::selectone($getUserNameQry); // or die(imw_error());
                    $getUserFname = $getUserNameRow->fname;
                    $getUserMname = $getUserNameRow->mname;
                    $getUserLname = $getUserNameRow->lname;
                    $getUserName = $getUserFname . " " . $getUserMname . " " . $getUserLname;
                    $getUserType = $getUserNameRow->user_type;
                    $getUserTypeLabel = ($getUserType == 'Anesthesiologist') ? 'Anesthesia Provider' : $getUserType;
                    $data[] = ['id' => $amendmentId, 'notes' => $amendmentNotes, 'Who' => $getUserTypeLabel, 'userId' => $userIdAmendment, 'CreatedBy' => stripslashes($getUserName), 'Date' => $dateAmendment, 'Time' => $timeAmendment];
                }
                //    $data = $result_amendment;
            }
        }
        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => '',
                    'data' => $data,
        ]); // NOT_FOUND (404) being the HTTP response code 
    }

    public function save_amendment(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $amendmentsText = $request->json()->get('amendmentsText') ? $request->json()->get('amendmentsText') : $request->input('amendmentsText');
        $loginUserType = $request->json()->get('UserType') ? $request->json()->get('UserType') : $request->input('UserType');
        $amendmentId = $request->json()->get('amendmentId') ? $request->json()->get('amendmentId') : $request->input('amendmentId');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $elem_signature = $request->json()->get('elem_signature') ? $request->json()->get('elem_signature') : $request->input('elem_signature');
        $data = [];
        $status = 0;
        $moveStatus = 0;
        $insertStatus = 0;
        // $requiredStatus = 1;
        $message = " unauthorized ";
        $requiredStatus = [];
        $userId = $this->checkToken($userToken);
        if ($userId > 0) {
            if ($pConfirmId == "") {
                $message = " ConfirmId is missing ";
                $status = 1;
                $requiredStatus = 0;
            } else if ($patient_id == "") {
                $message = " Patient Id is missing ";
                $status = 1;
                $requiredStatus = 0;
            } else if ($elem_signature == "") {
                $message = " Signature is missing ";
                $status = 1;
                $requiredStatus = 0;
            } else if ($loginUserType == "") {
                $message = " UserType is missing ";
                $status = 1;
                $requiredStatus = 0;
            } else if ($loginUserType != "Anesthesiologist" && $loginUserType != "surgeon" && $loginUserType != "Surgeon") {
                $message = " Access Denied ! ";
                $status = 1;
                $requiredStatus = 0;
            } else {
                $status = 1;
                $message = " Patient allergies list ";
                unset($arrayRecord);
                //CODE TO SET FORM STATUS 
                $form_status = 'completed';
                if ($amendmentsText == "") {
                    $form_status = "not completed";
                }
                //CODE TO SET FORM STATUS 
                $arrayRecord['notes'] = addslashes($amendmentsText);
                $arrayRecord['finalizeId'] = $pConfirmId;
                $arrayRecord['dateAmendment'] = date('Y-m-d');
                $arrayRecord['timeAmendment'] = date('H:i:s');
                $arrayRecord['userId'] = $userId;
                $arrayRecord['signUser'] = $elem_signature;
                $arrayRecord['confirmation_id'] = $pConfirmId;
                $arrayRecord['patient_id'] = $patient_id;
                $arrayRecord['form_status'] = $form_status;

                //MAKE AUDIT STATUS CRATED OR MODIFIED
                unset($arrayStatusRecord);
                $arrayStatusRecord['user_id'] = $userId;
                $arrayStatusRecord['patient_id'] = $patient_id;
                $arrayStatusRecord['confirmation_id'] = $pConfirmId;
                $arrayStatusRecord['form_name'] = 'physician_amendments_form';
                $arrayStatusRecord['action_date_time'] = date('Y-m-d H:i:s');
                //MAKE AUDIT STATUS CRATED OR MODIFIED

                if ($amendmentId) {
                    DB::table('amendment')->where('amendmentId', $amendmentId)->update($arrayRecord);
                    //MAKE AUDIT STATUS MODIFIED
                    $arrayStatusRecord['status'] = 'modified';
                    DB::table('chartnotes_change_audit_tbl')->insert($arrayStatusRecord);
                    //MAKE AUDIT STATUS MODIFIED
                } else {
                    if ($amendmentsText != '') {
                        DB::table('amendment')->insert($arrayRecord);
                    }
                    //MAKE AUDIT STATUS CREATED
                    $arrayStatusRecord['status'] = 'created';
                    DB::table('chartnotes_change_audit_tbl')->insert($arrayStatusRecord);
                    //MAKE AUDIT STATUS CREATED
                }
                $insertStatus = 1;
                $message = " Data Inserted successfully !";
                $data = [];
            }
        }
        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => '',
                    'data' => $data,
                    'insertStatus' => $insertStatus
        ]); // NOT_FOUND (404) being the HTTP response code 
    }

    public function del_amendment_notes(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $delId = $request->json()->get('amendmentId') ? $request->json()->get('amendmentId') : $request->input('amendmentId');
        $data = [];
        $status = 0;
        $moveStatus = 0;
        $delstatus = 0;
        // $requiredStatus = 1;
        $message = " unauthorized ";
        $requiredStatus = [];
        $userId = $this->checkToken($userToken);
        if ($userId > 0) {
            if ($delId == "") {
                $message = " delId is missing ";
                $status = 1;
                $requiredStatus = 0;
            } else {
                DB::select("delete from amendment where amendmentId=$delId");
                $status = 1;
                $delstatus = 1;
            }
        }
        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => '',
                    'data' => $data,
                    'delstatus' => $delstatus
        ]); // NOT_FOUND (404) being the HTTP response code 
    }

}
