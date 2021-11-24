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

class PreNurseAlderateController extends Controller {

    public function PreNurseAlderate_template(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
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
                $primary_procedure_id = $confirmationDetails->patient_primary_procedure_id;
                $primary_procedure_name = $confirmationDetails->patient_primary_procedure;
                $secondary_procedure_id = $confirmationDetails->patient_secondary_procedure_id;
                $surgeonId = $confirmationDetails->surgeonId;
                $ascId = $confirmationDetails->ascId;
                $finalizeStatus = $confirmationDetails->finalize_status;
                //Getting Details From table
                $scoringDetails = $this->getRowRecord('pre_nurse_alderate', "confirmation_id", $pConfirmId);
                if ($scoringDetails) {
                    $scoreID = $scoringDetails->id;
                    $pointsDetail = $scoringDetails->points_detail;
                    $formStatus = $scoringDetails->form_status;
                    $pointsDetailArr = explode(",", $pointsDetail);
                }
                $ScoringData = [];
                // Getting Details From table
                $datas = [];
                $ScoringCategories = DB::select('select * from alderate_scoring_categories');
                if ($ScoringCategories) {
                    $TotalPoints = 0;
                    foreach ($ScoringCategories as $key => $cats) {
                        $ScoringQuestions = DB::select("select * from alderate_scoring_questions where category_id='" . $cats->id . "'");
                        $checked = false;
                        foreach ($ScoringQuestions as $ScoringQuestionss) {
                            $strmatch = $cats->id . "-" . $ScoringQuestionss->id;
                            $ScoringData[$cats->id][] = ['id' => $ScoringQuestionss->id, 'category_id' => $ScoringQuestionss->category_id, 'question' => $ScoringQuestionss->question, 'assessment_point' => $ScoringQuestionss->assessment_point, 'checked_save_id' => $strmatch, 'checked' => in_array($strmatch, $pointsDetailArr) ? true : false];
                        }
                        if ($ScoringQuestions) {
                            $data['item'][] = ['name' => $cats->categoryName, 'sub_array' => $ScoringData[$cats->id]];
                        }
                    }
                }
                // $data=$datas;
                $status = 1;
                $message = 'Pre Nurse Alderate List';
            }
        }
        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => '',
                    'data' => $data,
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

    public function PreNurseAlderate_save(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $checked_save_ids = $request->json()->get('checked_save_ids') ? $request->json()->get('checked_save_ids') : $request->input('checked_save_ids');
        $data = [];
        $status = 0;
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
            } else {
                $ViewUserNameQry = "select fname,mname,lname,user_type from `users` where  usersId = '" . $userId . "'";
                $ViewUserNameRow = DB::selectone($ViewUserNameQry); // or die($ViewUserNameQry . imw_error());
                $loggedInUserFirstName = $ViewUserNameRow->fname;
                $loggedInUserMiddleName = $ViewUserNameRow->mname;
                $loggedInUserLastName = $ViewUserNameRow->lname;
                $user_type = $ViewUserNameRow->user_type;
                $signOnFileStatus = 'Yes';
                $loggedInUserName = $ViewUserNameRow->lname . ", " . $ViewUserNameRow->fname . " " . $ViewUserNameRow->mname;
                DB::table('pre_nurse_alderate')->where('confirmation_id', $pConfirmId)->delete();
                $data_arr = ['confirmation_id' => $pConfirmId, 'points_detail' => $checked_save_ids, 'form_status' => 'not completed']; //form status is pending for now
                DB::table('pre_nurse_alderate')->insertGetId($data_arr);
                $status=1;
                $message=" Saved Successfully !";
            }
        }
        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => '',
                    'data' => $data,
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

}
