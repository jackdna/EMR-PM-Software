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

class ProgressNotesController extends Controller {

    public function addeditprogressNotes(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $text_note = $request->json()->get('text_note') ? $request->json()->get('text_note') : $request->input('text_note');
        $intProgressID = $request->json()->get('intProgressID') ? $request->json()->get('intProgressID') : $request->input('intProgressID');
        $DelID = $request->json()->get('DelID') ? $request->json()->get('DelID') : $request->input('DelID');
        $data = [];
        $status = 0;
        $requiredStatus = 1;
        $delstatus=0;
        $message = " unauthorized ";
        $user_id = $this->checkToken($userToken);
        $addSubmit=0;
        if ($user_id > 0) {
            if ($pConfirmId == "") {
                $message = " ConfirmId is missing ";
                $status = 1;
                $requiredStatus = 0;
            } else if ($text_note == "" && $DelID=="") {
                $message = " Text is missing ";
                $status = 1;
                $requiredStatus = 0;
            } else {
                $status = 1;
                $loggedInUserId = $user_id;
                if ($DelID > 0) {
                    DB::table('tblprogress_report')->where('intProgressID', $DelID)->delete();
                    $message = " Record deleted Successfully ! ";
                    $delstatus=1;
                } else {
                    //GET USER NAME
                    $ViewUserNameQry = "select fname,mname,lname,user_type from `users` where  usersId = '" . $loggedInUserId . "'";
                    $ViewUserNameRow = DB::selectone($ViewUserNameQry); // or die($ViewUserNameQry . imw_error());
                    $loggedInUserFirstName = $ViewUserNameRow->fname;
                    $loggedInUserMiddleName = $ViewUserNameRow->mname;
                    $loggedInUserLastName = $ViewUserNameRow->lname;
                    $user_type = $ViewUserNameRow->user_type;
                    $signOnFileStatus = 'Yes';
                    $signDateTime = date("Y-m-d H:i:s");

                    $loggedInUserName = $ViewUserNameRow->lname . ", " . $ViewUserNameRow->fname . " " . $ViewUserNameRow->mname;

                    $insertRecords = array();
                    $insertRecords ['txtNote'] = addslashes(nl2br($text_note));
                    $insertRecords ['usersId'] = $user_id;
                    $insertRecords ['asc_id'] = $iasc_facility_id;
                    $insertRecords ['dtDateTime'] = date('Y-m-d');
                    $insertRecords ['tTime'] = date('H:i:s');
                    $insertRecords ['confirmation_id'] = $pConfirmId;
                    $insertRecords ['userType'] = $user_type;
                    if ($intProgressID > 0) {
                        DB::table('tblprogress_report')->where('intProgressID', $intProgressID)->update($insertRecords);
                        $message = " Record updated Successfully ! ";
                        $addSubmit=1;
                    } else {
                        $insert_id = DB::table('tblprogress_report')->insertGetId($insertRecords);
                        $message = " Record inserted Successfully ! ";
                        $addSubmit=1;
                    }
                    
                }
            }
        }
        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => $requiredStatus,
                    'data' => [],
                    'SubmitStatue'=>$addSubmit,
                    'delstatus'=>$delstatus
        ]); // NOT_FOUND (404) being the HTTP response code 
    }

    public function listprogressNotes(Request $request) {
        $userToken = $request->input('user_token');
        $iasc_facility_id = $request->input('iasc_facility_id');
        $pConfirmId = $request->input('pConfirmId');
        $data = [];
        $status = 0;
        $requiredStatus = 1;
        $message = " unauthorized ";
        $user_id = $this->checkToken($userToken);
        if ($user_id > 0) {
            if ($pConfirmId == "") {
                $message = " ConfirmId is missing ";
                $status = 1;
                $requiredStatus = 0;
            } else {
                $status = 1;
                $loggedInUserId = $user_id;
                //GET USER NAME
                $ViewUserNameQry = "select fname,mname,lname,user_type from `users` where  usersId = '" . $loggedInUserId . "'";
                $ViewUserNameRow = DB::selectone($ViewUserNameQry); // or die($ViewUserNameQry . imw_error());
                $loggedInUserFirstName = $ViewUserNameRow->fname;
                $loggedInUserMiddleName = $ViewUserNameRow->mname;
                $loggedInUserLastName = $ViewUserNameRow->lname;
                $user_type = $ViewUserNameRow->user_type;

                $signOnFileStatus = 'Yes';
                $signDateTime = date("Y-m-d H:i:s");
                $loggedInUserName = $ViewUserNameRow->lname . ", " . $ViewUserNameRow->fname . " " . $ViewUserNameRow->mname;
                $query_rsNotes = "SELECT tbl_reprt.intProgressID, tbl_reprt.txtNote, tbl_reprt.confirmation_id, concat(U.lname,',', U.fname,U.mname) as name "
                        . ", U.user_type, DATE_FORMAT(tbl_reprt.dtDateTime,'%m/%d/%Y') as dtDateTime, DATE_FORMAT(tbl_reprt.tTime,'%h:%i %p') as tTime "
                        . " FROM tblprogress_report tbl_reprt"
                        . " LEFT join users U on U.usersId=tbl_reprt.usersId"
                        . " WHERE tbl_reprt.confirmation_id = '" . $pConfirmId . "' ORDER BY dtDateTime DESC, tTime DESC";
                //echo $query_rsNotes;
                $rsNotes = DB::select($query_rsNotes); /* Progress Notes */
                if ($rsNotes) {
                    $data = $rsNotes;
                }
                $message = " Progress Note Listing ";
                return response()->json([
                            'status' => $status,
                            'message' => $message,
                            'requiredStatus' => $requiredStatus,
                            'data' => $data,
                            'loggedInUserName' => $loggedInUserName
                ]); // NOT_FOUND (404) being the HTTP response code 
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
