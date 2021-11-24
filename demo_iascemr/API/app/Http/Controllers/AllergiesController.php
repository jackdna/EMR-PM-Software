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

class AllergiesController extends Controller {

    public function patient_alergies_details(Request $request) {
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
                $message = " Patient allergies list ";
                $query_allergiespop = "SELECT pre_op_allergy_id,allergy_name,reaction_name from patient_allergies_tbl WHERE patient_confirmation_id = '$pConfirmId'";
                $result_allergiespop = DB::select($query_allergiespop);
                $data = $result_allergiespop;
            }
        }
        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => '',
                    'data' => $data,
        ]); // NOT_FOUND (404) being the HTTP response code 
    }

}
