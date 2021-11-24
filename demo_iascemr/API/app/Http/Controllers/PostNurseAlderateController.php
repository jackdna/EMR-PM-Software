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

class PostNurseAlderateController extends Controller {

    public function PostNurseAlderate_template(Request $request) {
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

                $scoringTable = $this->getRowRecord('post_nurse_alderate', 'confirmation_id', $pConfirmId);
                $scoringDetails = $this->getMultiChkArrayRecords('post_nurse_alderate_data', array("confirmation_id" => $pConfirmId, 'is_deleted' => 0));
                if ($scoringTable) {
                    $form_status = $scoringTable->form_status;
                }
                $totalPostRecord = (is_array($scoringDetails) && count($scoringDetails) > 0 ) ? count($scoringDetails) : 1;

                // Getting Details From table
                $ScoringCategories = DB::select('select * from alderate_scoring_categories');

                for ($loop = 0; $loop < $totalPostRecord; $loop++) {
                    if ($ScoringCategories) {
                        $ScoringData = [];
                        $TotalPoints = 0;
                        foreach ($ScoringCategories as $key => $cats) {

                            $scoreID = $scoringDetails[$loop]->id;
                            $pointsDetail = $scoringDetails[$loop]->points_detail;
                            $pointsDetailArr = explode(",", $pointsDetail);

                            $recorded_at = $this->getFullDtTmFormat($scoringDetails[$loop]->created_on);
                            $recorded_by = $this->getUsrNm($scoringDetails[$loop]->created_by, true);

                            $data[$loop]['score_id'] = $scoreID;
                            $data[$loop]['recorded_at'] = $recorded_at;
                            $data[$loop]['recorded_by'] = $recorded_by;

                            $ScoringQuestions = DB::select("select * from alderate_scoring_questions where category_id='" . $cats->id . "'");

                            if ($ScoringQuestions) {
                                foreach ($ScoringQuestions as $ScoringQuestionss) {
                                    $strmatch = $cats->id . "-" . $ScoringQuestionss->id;
                                    $ScoringData[$cats->id][] = ['id' => $ScoringQuestionss->id, 'category_id' => $ScoringQuestionss->category_id, 'question' => $ScoringQuestionss->question, 'assessment_point' => $ScoringQuestionss->assessment_point, 'checked_save_id' => $strmatch, 'checked' => in_array($strmatch, $pointsDetailArr) ? true : false];
                                }

                                $data[$loop]['item'][] = ['name' => $cats->categoryName, 'sub_array' => $ScoringData[$cats->id]];
                            }
                        }
                    }
                }

                $status = 1;
                $message = 'Post Nurse Alderate List';
            }
        }
        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => '',
                    'form_status' => $form_status,
                    'data' => $data,
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

    public function PostNurseAlderate_save(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');

        $data = [];
        $status = 0;
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

                $del_records = $request->json()->get('del_records') ? $request->input('del_records') : '';
                if ($del_records) {
                    $this->postNurseAlderate_del($del_records);
                }

                $form_status = 'completed';
                $currDtTime = date('Y-m-d H:i:s');

                $catCount = $request->json()->get('cat_count') ? $request->input('cat_count') : "";
                $scoreId = $request->json()->get('score_id') ? $request->input('score_id') : "";
                $scoreVal = $request->json()->get('score_value') ? $request->input('score_value') : "";

                $scoreIdArr = explode(",", $scoreId);
                $scoreArr = array_filter(explode(",", $scoreVal));

                if (is_array($scoreIdArr) && count($scoreIdArr) > 0) {
                    foreach ($scoreIdArr as $key => $scoreID) {

                        $points = $scoreArr[$key];
                        $pointsArr = array_unique(array_filter(explode("#~#", $points)));

                        // update form status variable
                        if ($form_status == 'completed' && count($pointsArr) <> $catCount)
                            $form_status = "not completed";

                        // insert/update records
                        $arrayRecord = array();
                        $arrayRecord['points_detail'] = implode(",", $pointsArr);
                        if ($scoreID) {
                            $arrayRecord['modified_on'] = $currDtTime;
                            $arrayRecord['modified_by'] = $userId;
                            DB::table('post_nurse_alderate_data')->where('id', $scoreID)->update($arrayRecord);
                        } else {
                            $arrayRecord['confirmation_id'] = $pConfirmId;
                            $arrayRecord['created_on'] = $currDtTime;
                            $arrayRecord['created_by'] = $userId;
                            DB::table('post_nurse_alderate_data')->insert($arrayRecord);
                        }
                    }
                    // End $scoreIdArr
                }

                // update chart status
                DB::table('post_nurse_alderate')->where('confirmation_id', $pConfirmId)->update(['form_status' => $form_status]);

                $status = 1;
                $message = " Saved Successfully !";
            }
        }
        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => '',
                    'data' => $data,
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

    private function postNurseAlderate_del($del_ids = '') {
        if ($del_ids) {

            $del_records_arr = array_filter(explode(",", $del_ids));

            if (is_array($del_records_arr) && count($del_records_arr) > 0) {
                DB::table('post_nurse_alderate_data')
                        ->whereIn('id', $del_records_arr)
                        ->update([ 'is_deleted' => 1]);
            }
        }
    }

}
