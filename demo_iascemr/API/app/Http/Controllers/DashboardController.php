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

class DashboardController extends Controller {

    public function api_dashboard(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $selected_date = $request->json()->get('selected_date') ? $request->json()->get('selected_date') : $request->input('selected_date');
        $showAllApptStatus = $request->json()->get('showAllApptStatus') ? $request->json()->get('showAllApptStatus') : $request->input('showAllApptStatus');
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
        $arrayRecord = [];
        if ($userId > 0) {
            if ($facility_id == "") {
                $message = " FacilityId is missing ";
                $status = 1;
                $requiredStatus = 0;
            } elseif ($iasc_facility_id == "") {
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
                $selected_dates = date("Y-m-d");
                if ($selected_date == '') {
                    $selected_date = date("Y-m-d");
                    $display_date = date("m/d/Y");
                }
                if ($showAllApptStatus != " ") {
                    $showAllApptStatus = $showAllApptStatus;
                } else {
                    $showAllApptStatus = 'Active';
                }

                $showAllApptStatusQry = "";
                if ($showAllApptStatus == "Active") {
                    $showAllApptStatusQry = " AND  patient_status NOT IN('Canceled','No Show')";
                } elseif ($showAllApptStatus == "Canceled") {
                    $showAllApptStatusQry = " AND  patient_status='Canceled'";
                } elseif ($showAllApptStatus == "No Show") {
                    $showAllApptStatusQry = " AND  patient_status='No Show'";
                }

                $fac_con = "";
                if ($iasc_facility_id != '') {
                    $fac_con = " and stub_tbl.iasc_facility_id='" . $iasc_facility_id . "'";
                }
                $status = 1;
                $message = " dashboard data";
                $sql_query = "SELECT fname, mname, lname,user_type,coordinator_type, practiceName FROM users WHERE usersId=$userId";
                $rows = DB::selectone($sql_query);
                $doss = "dos ='" . $selected_date . "'";
                $user_type = $rows->user_type;
                $coordinator_type = $rows->coordinator_type;
                $sql_query2 = "select surgeon_fname,surgeon_mname,surgeon_lname,surgery_time,pickup_time,arrival_time,stub_id,dos,patient_first_name,patient_middle_name,patient_last_name,patient_status,checked_in_time,checked_out_time,patient_dob,DATE_FORMAT(patient_dob,'%m/%d/%Y') as patient_dob_format,patient_confirmation_id,patient_id_stub,patient_primary_procedure,patient_secondary_procedure,patient_tertiary_procedure,comment,site,stub_secondary_site,stub_tertiary_site,chartSignedBySurgeon,chartSignedByNurse,chartSignedByAnes,recentChartSaved,imwPatientId,iasc_facility_id from stub_tbl where ";
                $sql_query2.= $doss;
                $sql_query2.= $fac_con;
                $sql_query2.= $showAllApptStatusQry;
                $sql_query2.= " order by iasc_facility_id, surgery_time";

                $chartFinalizeStatus = "false";
                $confPatientId = "0";
                $rows2 = DB::select($sql_query2);
                if ($rows2) {
                    foreach ($rows2 as $row) {
                        if ($row->patient_confirmation_id > 0) {
                            $getPtIdQry = "SELECT patientId,finalize_status  FROM patientconfirmation WHERE patientConfirmationId='" . $row->patient_confirmation_id . "'";
                            $getPtIdRow = DB::selectone($getPtIdQry);
                            $chartFinalizeStatus = $getPtIdRow->finalize_status;
                            if ($row->patient_confirmation_id > 0 && $row->patient_id_stub == "") {
                                $confPatientId = $getPtIdRow->patientId;
                            }
#imwPatientQry="SELECT patient_id,imwPatientId FROM patient_data_tbl WHERE patient_id =%s"
# cursor.execute(imwPatientQry,(row[16]))
# imwPatientRow=cursor.fetchall()
# imwPatientId=70025 #imwPatientRow[0][1]
                            $surgery_time = $row->surgery_time; //datetime.strptime(str(row[3]), "%H:%M:%S")
#checked_in_time=datetime.strptime(str(row[12]), "%H:%M:%S")
#checked_out_time=datetime.strptime(str(row[13]), "%H:%M:%S")
                            $text_color = "#000000";
                            if ($row->patient_status == "Checked-Out") {
                                $text_color = "#0000FF";
                            }
                            if ($row->patient_status == "Checked-In") {
                                $text_color = "#008000";
                            }
                            $data[] = [
                                'surgeon_fname' => $row->surgeon_fname,
                                'surgeon_mname' => $row->surgeon_mname,
                                'surgeon_lname' => $row->surgeon_lname,
                                'surgery_time' => date("h:i A", strtotime($surgery_time)),
                                'pickup_time' => $row->pickup_time, 'arrival_time' => $row->arrival_time, 'stub_id' => $row->stub_id, 'dos' => $row->dos, 'patient_first_name' => $row->patient_first_name,
                                'patient_middle_name' => $row->patient_middle_name, 'patient_last_name' => $row->patient_last_name, 'patient_status' => $row->patient_status, 'checked_in_time' => $row->checked_in_time,
                                'checked_out_time' => $row->checked_out_time, 'patient_dob' => $row->patient_dob, 'patient_dob_format' => $row->patient_dob_format, 'patient_confirmation_id' => $row->patient_confirmation_id,
                                'patient_id_stub' => $row->patient_id_stub, 'patient_primary_procedure' => $row->patient_primary_procedure, 'patient_secondary_procedure' => $row->patient_secondary_procedure,
                                'patient_tertiary_procedure' => $row->patient_tertiary_procedure, 'comment' => $row->comment, 'site' => $row->site, 'stub_secondary_site' => $row->stub_secondary_site, 'stub_tertiary_site' => $row->stub_tertiary_site,
                                'chartSignedBySurgeon' => $row->chartSignedBySurgeon, 'chartSignedByNurse' => $row->chartSignedByNurse, 'chartSignedByAnes' => $row->chartSignedByAnes, 'recentChartSaved' => $row->recentChartSaved, 'chartFinalizeStatus' => $chartFinalizeStatus, 'confPatientId' => $confPatientId,
                                'user_type' => $user_type, 'coordinator_type' => $coordinator_type,
                                'accessDeniedCoordinator' => 'Access denied to Coordinator', 'chartFinalizedAlert' => 'Chart Finalized', 'imwPatientId' => $row->imwPatientId, 'iasc_facility_id' => $row->iasc_facility_id, 'pro_status_text_color' => $text_color
                            ];
                            $requiredStatus = 0;
                            $message = " Dashboard list ";
                            $status = 1;
                        }
                    }
                }
                return response()->json([
                            'data' => $data,
                            'status' => $status,
                            'message' => $message,
                            'requiredStatus' => $requiredStatus,
                ]); // NOT_FOUND (404) being the HTTP response code 
            }
        }
        return response()->json([
                    'data' => $data,
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => $requiredStatus,
        ]); // NOT_FOUND (404) being the HTTP response code 
    }

    public function api_search(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $selected_date = $request->json()->get('selected_date') ? $request->json()->get('selected_date') : $request->input('selected_date');
        $showAllApptStatus = $request->json()->get('showAllApptStatus') ? $request->json()->get('showAllApptStatus') : $request->input('showAllApptStatus');
        $searchType = $request->json()->get('searchType') ? $request->json()->get('searchType') : $request->input('searchType'); // request.form['searchType']
        $searchText = $request->json()->get('searchText') ? $request->json()->get('searchText') : $request->input('searchText'); // request.form['searchText']
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
        $arrayRecord = [];
        if ($userId > 0) {
            if ($facility_id == "") {
                $message = " FacilityId is missing ";
                $status = 1;
                $requiredStatus = 0;
            } elseif ($iasc_facility_id == "") {
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
                $selected_dates = date("Y-m-d");
                if ($selected_date == '') {
                    $selected_date = date("Y-m-d");
                    $display_date = date("m/d/Y");
                }
                if ($showAllApptStatus != " ") {
                    $showAllApptStatus = $showAllApptStatus;
                } else {
                    $showAllApptStatus = 'Active';
                }

                $SearchQry = "";
                if ($searchType == "ASC_ID") {
                    $SearchQry = " AND ascId='" . $searchText . "'";
                }
                if ($searchType == "Patient_Name") {
                    $SearchQry = " AND (patient_first_name='" . $searchText . "' OR patient_last_name='" . $searchText . "')";
                }
                if ($searchType == "External_MRN") {
                    $SearchQry = " AND imwPatientId='" . $searchText . "'";
                }

                $showAllApptStatusQry = "";
                if ($showAllApptStatus == "Active") {
                    $showAllApptStatusQry = " AND  patient_status NOT IN('Canceled','No Show')";
                } elseif ($showAllApptStatus == "Canceled") {
                    $showAllApptStatusQry = " AND  patient_status='Canceled'";
                } elseif ($showAllApptStatus == "No Show") {
                    $showAllApptStatusQry = " AND  patient_status='No Show'";
                }

                $fac_con = "";
                if ($iasc_facility_id != '') {
                    $fac_con = " and stub_tbl.iasc_facility_id='" . $iasc_facility_id . "'";
                }
                $status = 1;
                $message = " dashboard data";
                $sql_query = "SELECT fname, mname, lname,user_type,coordinator_type, practiceName FROM users WHERE usersId=$userId";
                $rows = DB::selectone($sql_query);
                $doss = "dos ='" . $selected_date . "'";
                $user_type = $rows->user_type;
                $coordinator_type = $rows->coordinator_type;
                $sql_query2 = "select surgeon_fname,surgeon_mname,surgeon_lname,surgery_time,pickup_time,arrival_time,stub_id,dos,patient_first_name,patient_middle_name,patient_last_name,patient_status,checked_in_time,checked_out_time,patient_dob,DATE_FORMAT(patient_dob,'%m/%d/%Y') as patient_dob_format,patient_confirmation_id,patient_id_stub,patient_primary_procedure,patient_secondary_procedure,patient_tertiary_procedure,comment,site,stub_secondary_site,stub_tertiary_site,chartSignedBySurgeon,chartSignedByNurse,chartSignedByAnes,recentChartSaved,imwPatientId,iasc_facility_id from stub_tbl where 1=1 ";
//  $sql_query2.= $doss;
#sql_query2 += doss
                $sql_query2.= $fac_con;
                $sql_query2.= $showAllApptStatusQry;
                $sql_query2.= $SearchQry;
                $sql_query2.= " order by iasc_facility_id, surgery_time";
                $chartFinalizeStatus = "false";
                $confPatientId = "0";
                $rows2 = DB::select($sql_query2);
                if ($rows2) {
                    foreach ($rows2 as $row) {
                        if ($row->patient_confirmation_id > 0) {
                            $getPtIdQry = "SELECT patientId,finalize_status  FROM patientconfirmation WHERE patientConfirmationId='" . $row->patient_confirmation_id . "'";
                            $getPtIdRow = DB::selectone($getPtIdQry);
                            $chartFinalizeStatus = $getPtIdRow->finalize_status;
                            if ($row->patient_confirmation_id > 0 && $row->patient_id_stub == "") {
                                $confPatientId = $getPtIdRow->patientId;
                            }
                            #imwPatientQry="SELECT patient_id,imwPatientId FROM patient_data_tbl WHERE patient_id =%s"
                            # cursor.execute(imwPatientQry,(row[16]))
                            # imwPatientRow=cursor.fetchall()
                            # imwPatientId=70025 #imwPatientRow[0][1]
                            $surgery_time = $row->surgery_time; //datetime.strptime(str(row[3]), "%H:%M:%S")
                            #checked_in_time=datetime.strptime(str(row[12]), "%H:%M:%S")
                            #checked_out_time=datetime.strptime(str(row[13]), "%H:%M:%S")
                            $text_color = "#000000";
                            if ($row->patient_status == "Checked-Out") {
                                $text_color = "#0000FF";
                            }
                            if ($row->patient_status == "Checked-In") {
                                $text_color = "#008000";
                            }
                            $data[] = [
                                'surgeon_fname' => $row->surgeon_fname,
                                'surgeon_mname' => $row->surgeon_mname,
                                'surgeon_lname' => $row->surgeon_lname,
                                'surgery_time' => date("h:i A", strtotime($surgery_time)),
                                'pickup_time' => $row->pickup_time, 'arrival_time' => $row->arrival_time, 'stub_id' => $row->stub_id, 'dos' => $row->dos, 'patient_first_name' => $row->patient_first_name,
                                'patient_middle_name' => $row->patient_middle_name, 'patient_last_name' => $row->patient_last_name, 'patient_status' => $row->patient_status, 'checked_in_time' => $row->checked_in_time,
                                'checked_out_time' => $row->checked_out_time, 'patient_dob' => $row->patient_dob, 'patient_dob_format' => $row->patient_dob_format, 'patient_confirmation_id' => $row->patient_confirmation_id,
                                'patient_id_stub' => $row->patient_id_stub > 0 ? $row->patient_id_stub : $confPatientId, 'patient_primary_procedure' => $row->patient_primary_procedure, 'patient_secondary_procedure' => $row->patient_secondary_procedure,
                                'patient_tertiary_procedure' => $row->patient_tertiary_procedure, 'comment' => $row->comment, 'site' => $row->site, 'stub_secondary_site' => $row->stub_secondary_site, 'stub_tertiary_site' => $row->stub_tertiary_site,
                                'chartSignedBySurgeon' => $row->chartSignedBySurgeon, 'chartSignedByNurse' => $row->chartSignedByNurse, 'chartSignedByAnes' => $row->chartSignedByAnes, 'recentChartSaved' => $row->recentChartSaved, 'chartFinalizeStatus' => $chartFinalizeStatus, 'confPatientId' => $confPatientId,
                                'user_type' => $user_type, 'coordinator_type' => $coordinator_type,
                                'accessDeniedCoordinator' => 'Access denied to Coordinator', 'chartFinalizedAlert' => 'Chart Finalized', 'imwPatientId' => $row->imwPatientId, 'iasc_facility_id' => $row->iasc_facility_id, 'pro_status_text_color' => $text_color
                            ];
                            $requiredStatus = 0;
                            $message = " Dashboard list ";
                            $status = 1;
                        }
                    }
                }
                return response()->json([
                            'data' => $data,
                            'status' => $status,
                            'message' => $message,
                            'requiredStatus' => $requiredStatus,
                ]); // NOT_FOUND (404) being the HTTP response code 
            }
        }
        return response()->json([
                    'data' => $data,
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => $requiredStatus,
        ]); // NOT_FOUND (404) being the HTTP response code 
    }

    public function api_saveComment(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $comment = $request->json()->get('comment') ? $request->json()->get('comment') : $request->input('comment');
        $stubID = $request->json()->get('stubID') ? $request->json()->get('stubID') : $request->input('stubID');
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
        $arrayRecord = [];
        if ($userId > 0) {
            if ($facility_id == "") {
                $message = " FacilityId is missing ";
                $status = 1;
                $requiredStatus = 0;
            } elseif (trim($comment) == "") {
                $status = 1;
                $message = " Text is missing ";
            } elseif (trim($stubID) == "") {
                $status = 1;
                $message = " stubID is missing ";
            } else {
                $ViewUserNameQry = "select fname,mname,lname,user_type from `users` where  usersId = '" . $userId . "'";
                $ViewUserNameRow = DB::selectone($ViewUserNameQry); // or die($ViewUserNameQry . imw_error());
                $loggedInUserFirstName = $ViewUserNameRow->fname;
                $loggedInUserMiddleName = $ViewUserNameRow->mname;
                $loggedInUserLastName = $ViewUserNameRow->lname;
                $user_type = $ViewUserNameRow->user_type;
                $signOnFileStatus = 'Yes';
                $loggedInUserName = $ViewUserNameRow->lname . ", " . $ViewUserNameRow->fname . " " . $ViewUserNameRow->mname;
                $updtCommentQry = "UPDATE stub_tbl SET
						comment ='" . addslashes($comment) . "',
						comment_modified_status = '1',
						comment_modified_datetime = now(),
						comment_modified_by_operator =$userId
						WHERE stub_id='" . trim($stubID) . "'";
                DB::select($updtCommentQry);
                $status = 1;
                $message = 'Saved successfully !';
            }
        }
        return response()->json([
                    'data' => $data,
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => $requiredStatus,
        ]); // NOT_FOUND (404) being the HTTP response code 
    }

    public function api_changePassword(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $elem_currentPassword = $request->json()->get('elem_currentPassword') ? $request->json()->get('elem_currentPassword') : $request->input('elem_currentPassword');
        $elem_newPassword1 = $request->json()->get('elem_newPassword') ? $request->json()->get('elem_newPassword') : $request->input('elem_newPassword');
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
        $password_status = 0;
        $arrayRecord = [];
        if ($userId > 0) {
            if ($facility_id == "") {
                $message = " FacilityId is missing ";
                $status = 1;
                $requiredStatus = 0;
            } elseif (trim($elem_currentPassword) == "") {
                $status = 1;
                $message = " Current Password is missing ";
            } elseif (trim($elem_newPassword1) == "") {
                $status = 1;
                $message = " New Password is missing ";
            } else {
                $loginUser = $userId;
                $getUserDetails = $this->getRowRecord('users', 'usersId', $userId);
                $elem_usersId = $userId;
                $elem_database_currentPassword = $getUserDetails->user_password;
                $passCreatedDate = date("Y-m-d");
                $getEncryptedCurrentPassStr = "SELECT PASSWORD('$elem_currentPassword') as newpassword";
                $getEncryptedCurrentPassRow = DB::selectone($getEncryptedCurrentPassStr);
                $encryptedCurrentPassword = $getEncryptedCurrentPassRow->newpassword;
                if ($elem_database_currentPassword <> $encryptedCurrentPassword) {
                    $password_status = 0;
                    $message = "Invalid Current Password !";
                } else {
                    $getEncryptedNewPassStr = "SELECT PASSWORD('$elem_newPassword1') as newpassword";
                    $getEncryptedNewPassRow = DB::selectone($getEncryptedNewPassStr);
                    $elem_newPassword = $getEncryptedNewPassRow->newpassword;
                    $getPasswordListStr = "SELECT * FROM lasusedpassword 
								WHERE user_id = '" . $loginUser . "' AND  
								(password1 = '" . $elem_newPassword . "'
								 OR password2 = '" . $elem_newPassword . "'
								 OR password3 = '" . $elem_newPassword . "'
								 OR password4 = '" . $elem_newPassword . "'
								 OR password5 = '" . $elem_newPassword . "'
								 OR password6 = '" . $elem_newPassword . "'
								 OR password7 = '" . $elem_newPassword . "'
								 OR password8 = '" . $elem_newPassword . "'
								 OR password9 = '" . $elem_newPassword . "'
								 OR password10 = '" . $elem_newPassword . "'
								)";

                    $getPasswordListQry = DB::select($getPasswordListStr);

                    if ($getPasswordListQry) {
                        $message = "Password Recently Used";
                        $password_status = 0;
                    } else {
                        $updateNewPaswordQry = "update users SET user_password='" . $elem_newPassword . "',locked='0',loginAttempts='0',
									 passCreatedOn ='" . $passCreatedDate . "'
									 WHERE usersId = '" . $loginUser . "'";
                        $updateNewPaswordRes = DB::select($updateNewPaswordQry);
// CHANGE STATUS FOR PASSWORD TO AUDIT				
                        unset($arrayStatusRecord);
                        $arrayStatusRecord['user_id'] = $loginUser;
                        $arrayStatusRecord['status'] = 'reset';
                        $arrayStatusRecord['password_status_date'] = date('Y-m-d H:i:s');
                        $arrayStatusRecord['operator_id'] = $loginUser;
                        $arrayStatusRecord['operator_date_time'] = date('Y-m-d H:i:s');
                        $arrayStatusRecord['comments'] = 'Change Password';
                        DB::table('password_change_reset_audit_tbl')->insertGetId($arrayStatusRecord);
// CHANGE STATUS FOR PASSWORD TO AUDIT  
//SAVE NEW PASSWORD IN lasusedpassword TABLE
                        $setLastPasswordQry = "SELECT * FROM `lasusedpassword` 
								WHERE user_id = '$loginUser'";
                        $setLastPasswordRow = DB::selectone($setLastPasswordQry);
                        if ($setLastPasswordRow) {
                            if ($setLastPasswordRow->password1 == '') {
                                $passwordFieldName = 'password1';
                            } else if ($setLastPasswordRow->password2 == '') {
                                $passwordFieldName = 'password2';
                            } else if ($setLastPasswordRow->password3 == '') {
                                $passwordFieldName = 'password3';
                            } else if ($setLastPasswordRow->password4 == '') {
                                $passwordFieldName = 'password4';
                            } else if ($setLastPasswordRow->password5 == '') {
                                $passwordFieldName = 'password5';
                            } else if ($setLastPasswordRow->password6 == '') {
                                $passwordFieldName = 'password6';
                            } else if ($setLastPasswordRow->password7 == '') {
                                $passwordFieldName = 'password7';
                            } else if ($setLastPasswordRow->password8 == '') {
                                $passwordFieldName = 'password8';
                            } else if ($setLastPasswordRow->password9 == '') {
                                $passwordFieldName = 'password9';
                            } else if ($setLastPasswordRow->password10 == '') {
                                $passwordFieldName = 'password10';
                            }

                            if ($passwordFieldName) {
                                $saveLastPasswordQry = "update `lasusedpassword` set $passwordFieldName = '$elem_newPassword' WHERE user_id = '$loginUser'";
                            } else {
                                $saveLastPasswordQry = "update `lasusedpassword` set 
											password1 = '" . $setLastPasswordRow->password2 . "', 
											password2 = '" . $setLastPasswordRow->password3 . "', 
											password3 = '" . $setLastPasswordRow->password4 . "', 
											password4 = '" . $setLastPasswordRow->password5 . "', 
											password5 = '" . $setLastPasswordRow->password6 . "', 
											password6 = '" . $setLastPasswordRow->password7 . "', 
											password7 = '" . $setLastPasswordRow->password8 . "', 
											password8 = '" . $setLastPasswordRow->password9 . "', 
											password9 = '" . $setLastPasswordRow->password10 . "', 
											password10 = '" . $elem_newPassword . "' 
											WHERE user_id = '$loginUser'";
                            }
                            $saveLastPasswordRes = DB::select($saveLastPasswordQry);
                        } else {
                            $saveLastPasswordQry = "insert into `lasusedpassword` set password1 = '$elem_newPassword', user_id = '$loginUser'";
                            $saveLastPasswordRes = DB::select($saveLastPasswordQry);
                        }
                        $message = "Password updated successfully !";
                        $password_status = 1;
                    }
                }
                $status = 1;
            }
        }
        return response()->json([
                    'data' => $data,
                    'status' => $status,
                    'password_status' => $password_status,
                    'message' => $message,
                    'requiredStatus' => $requiredStatus,
        ]); // NOT_FOUND (404) being the HTTP response code 
    }

    public function templateList(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $data = [];
        $status = 0;
        $moveStatus = 0;
// $requiredStatus = 1;
        $message = " unauthorized ";
        $requiredStatus = [];
        $userId = $this->checkToken($userToken);
        $stat = [];
        $arrayRecord = [];
        if ($userId > 0) {
            if ($facility_id == "") {
                $message = " FacilityId is missing ";
                $status = 1;
                $requiredStatus = 0;
            } elseif (trim($iasc_facility_id) == "") {
                $status = 1;
                $message = " Iasc facility id is missing ";
            } else {
                $Qry = "select consent_id,consent_name from `consent_forms_template` where consent_delete_status!='true' order by consent_id";
                $rows = DB::select($Qry);


                foreach ($rows as $row) {
                    $consent_list[] = ['consent_id' => $row->consent_id, 'consent_name' => $row->consent_name];
                }
                $chartNotes[] = ['alert' => 'Alert', 'consent_list' => $consent_list,
                    'chart_list' => [
                        'surgical_check_list' => 'Check List',
                        'preophealthquestionnaire' => 'Pre-Op Health Questionnaire',
                        'history_physicial_clearance' => 'H &amp; P Clearance',
                        'preopnursingrecord' => 'Pre-Op Nursing Record',
                        'postopnursingrecord' => 'Post-Op Nursing Record',
                        'preopphysicianorders' => 'Pre-Op Physician Orders',
                        'postopphysicianorders' => 'Post-Op Physician Orders',
                        'localanesthesiarecord' => 'MAC/Local/Regional Anesthesia Record',
                        'preopgenanesthesiarecord' => 'Pre-Op General Anesthesia Record',
                        'genanesthesiarecord' => 'General Anesthesia Record',
                        'genanesthesianursesnotes' => 'General Anesthesia Nurses Notes',
                        'operatingroomrecords' => 'Operating Room Record',
                        'laser_procedure_patient_table' => 'Laser Procedure',
                        'operativereport' => 'Operative Report',
                        'dischargesummarysheet' => 'Discharge Summary Sheet',
                        'patient_instruction_sheet' => 'Instruction Sheet/'
                    ]
                ];

                $status = 1;
                $message = "Consent Chart List";
            }
        }
        return response()->json([
                    'data' => $chartNotes,
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => $requiredStatus,
        ]); // NOT_FOUND (404) being the HTTP response code 
    }

    public function api_saveEpost(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $epost_data = $request->json()->get('epost_data') ? $request->json()->get('epost_data') : $request->input('epost_data');
        $patient_conf_id = $request->json()->get('patient_conf_id') ? $request->json()->get('patient_conf_id') : $request->input('patient_conf_id');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $stub_id = $request->json()->get('stub_id') ? $request->json()->get('stub_id') : $request->input('stub_id');
        $consent_template_id = $request->json()->get('consent_template_id') ? $request->json()->get('consent_template_id') : $request->input('consent_template_id');
        $table_name = $request->json()->get('table_name') ? $request->json()->get('table_name') : $request->input('table_name');
        $data = [];
        $status = 0;
        $moveStatus = 0;
// $requiredStatus = 1;
        $message = " unauthorized ";
        $requiredStatus = [];
        $userId = $this->checkToken($userToken);
        $epost_status = 0;
        $arrayRecord = [];
        $insertId = 0;
        if ($userId > 0) {
            if ($facility_id == "") {
                $message = " FacilityId is missing ";
                $status = 1;
                $requiredStatus = 0;
            } elseif (trim($stub_id) == "") {
                $status = 1;
                $message = " Stub Id is missing ";
            } elseif (trim($patient_conf_id) == "") {
                $status = 1;
                $message = " Patient ConfId is missing ";
            } else {

                if ($table_name != "") {
                    $table_namess = explode(",", $table_name);
                    foreach ($table_namess as $table_names) {
                        if ($table_names != "") {
                            $consentAutoIncId = 0;
                            $Qry = "INSERT INTO eposted SET epost_data ='" . $epost_data . "', dtdate ='" . date("Y-m-d") . "', T_time ='" . date("H:i:s") . "', `table_name` ='" . $table_names . "',"
                                    . " patient_conf_id ='" . $patient_conf_id . "',patient_id ='" . $patient_id . "', consent_template_id =0, consentAutoIncId =0, stub_id ='" . $stub_id . "',"
                                    . " created_operator_id ='" . $userId . "',epost_consent_purge_status='',modified_operator_id=0, created_date_time ='" . date("Y-m-d H:i:s") . "' ";
                            DB::select($Qry);
                        }
                    }
                    $message = "Data saved Successfully !";
                    $epost_status = 1;
                    $status = 1;
                }
                if ($consent_template_id != "") {
                    $consent_template_idss = explode(",", $consent_template_id);
                    $i = 0;
                    foreach ($consent_template_idss as $consent_template_ids) {
                        if ($consent_template_ids != "") {
                            $consentFormSelectQry = "select surgery_consent_id from `consent_multiple_form` where confirmation_id ='" . $patient_conf_id . "' AND consent_template_id='" . $consent_template_ids . "' AND consent_purge_status!='true'";
                            $res = DB::selectone($consentFormSelectQry);
                            $consentAutoIncId = 0;
                            if ($res) {
                                $consentAutoIncId = $res->surgery_consent_id;
                            }
                            $arr = [
                                'epost_data' => $epost_data,
                                'dtdate' => date("Y-m-d"),
                                'T_time' => date("H:i:s"),
                                'table_name' => 'consent_multiple_form',
                                'patient_conf_id' => $patient_conf_id,
                                'patient_id' => $patient_id,
                                'consent_template_id' => $consent_template_id,
                                'consentAutoIncId' => $consentAutoIncId,
                                'stub_id' => $stub_id,
                                'created_operator_id' => $userId,
                                'epost_consent_purge_status' => '',
                                'modified_operator_id' => 0,
                                'created_date_time' => date("Y-m-d H:i:s")
                            ];
                            $insertId = DB::table('eposted')->insertGetId($arr);
                        }
                        $i = $i + 1;
                    }
                    $message = "Data saved Successfully !";
                    $epost_status = 1;
                    $status = 1;
                }
            }
            $Qry = "SELECT ep.epost_id,ep.epost_data,ep.dtdate,ep.T_time,ep.consent_template_id,ep.table_name,ep.created_operator_id,IFNULL(concat(usr1.lname,', ',usr1.fname),'') AS created_operator_name, IFNULL(concat(usr2.lname,', ',usr2.fname),'') AS modified_operator_name ";
            $Qry.= " FROM eposted ep LEFT JOIN users AS usr1 ON (usr1.usersId=ep.created_operator_id)";
            $Qry.= " LEFT JOIN users AS usr2 ON (usr2.usersId=ep.modified_operator_id)";
            $Qry.= " WHERE epost_id='" . $insertId . "'";
        }
        return response()->json([
                    'data' => $Qry != "" ? DB::selectone($Qry) : [],
                    'status' => $status,
                    'insertId' => $insertId,
                    'epost_status' => $epost_status,
                    'message' => $message,
                    'requiredStatus' => $requiredStatus,
        ]); // NOT_FOUND (404) being the HTTP response code 
    }

    public function api_listEpost(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $patient_conf_id = $request->json()->get('patient_conf_id') ? $request->json()->get('patient_conf_id') : $request->input('patient_conf_id');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $table_name = $request->json()->get('table_name') ? $request->json()->get('table_name') : $request->input('table_name');
        $data = [];
        $status = 0;
// $requiredStatus = 1;
        $message = " unauthorized ";
        $requiredStatus = [];
        $userId = $this->checkToken($userToken);
        $epost_status = 0;
        $eAlertData = [];
        $ePostData = [];
        $arrayRecord = [];
        if ($userId > 0) {
            if ($facility_id == "") {
                $message = " FacilityId is missing ";
                $status = 1;
                $requiredStatus = 0;
            } elseif (trim($patient_conf_id) == "") {
                $status = 1;
                $message = " Confirmation Id is missing ";
            } elseif (trim($patient_id) == "") {
                $status = 1;
                $message = " Patient Id is missing ";
            } else {
                $message = " Epost list ";
                $Qry = "SELECT ep.epost_id,ep.epost_data,ep.dtdate,ep.T_time,ep.consent_template_id,ep.table_name,ep.created_operator_id,"
                        . "IFNULL(concat(usr1.lname,', ',usr1.fname),'') AS created_operator_name, "
                        . "IFNULL(concat(usr2.lname,', ',usr2.fname),'') AS modified_operator_name ";
                $Qry.= " FROM eposted ep LEFT JOIN users AS usr1 ON (usr1.usersId=ep.created_operator_id)";
                $Qry.= " LEFT JOIN users AS usr2 ON (usr2.usersId=ep.modified_operator_id)";
                $Qry.= " WHERE patient_id ='" . $patient_id . "' AND patient_id != '' AND patient_conf_id ='" . $patient_conf_id . "' AND epost_consent_purge_status !='true'";
                if ($table_name <> "") {
                    $Qry.=" AND `table_name`='" . $table_name . "'";
                }
                $Qry.= " ORDER by dtdate desc,T_time desc";
                $rows = DB::select($Qry);
                if ($rows) {
                    foreach ($rows as $row) {
                        $edata = [
                            'epost_id' => $row->epost_id, 'content' => $row->epost_data, 'eDateTime' => $row->dtdate,
                            'consent_template_id' => $row->consent_template_id, 'table_name' => $row->table_name, 'operator_id' => $row->created_operator_id,
                            'created_operator_name' => $row->created_operator_name,
                            'modified_operator_name' => $row->modified_operator_name, 'current_user_id' => $userId
                        ];
                        if ($row->table_name == 'alert') {
                            $eAlertData[] = $edata;
                        } else {
                            $ePostData[] = $edata;
                        }
                    }
                }
                $status = 1;
                $epost_status = 1;
            }
        }
        return response()->json([
                    'data' => [['eAlertData' => $eAlertData, 'ePostData' => $ePostData]],
                    'status' => $status,
                    'epost_status' => $epost_status,
                    'message' => $message,
                    'requiredStatus' => $requiredStatus,
        ]); // NOT_FOUND (404) being the HTTP response code 
    }

    public function api_deleteEpost(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $epost_id = $request->json()->get('epost_id') ? $request->json()->get('epost_id') : $request->input('epost_id');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $data = [];
        $status = 0;
// $requiredStatus = 1;
        $message = " unauthorized ";
        $requiredStatus = [];
        $userId = $this->checkToken($userToken);
        $epost_status = 0;
        $eAlertData = [];
        $ePostData = [];
        $arrayRecord = [];
        if ($userId > 0) {
            if ($facility_id == "") {
                $message = " FacilityId is missing ";
                $status = 1;
                $requiredStatus = 0;
            } elseif (trim($epost_id) == "") {
                $status = 1;
                $message = " Epost Id is missing ";
            } else {
                $Qry = " DELETE FROM eposted where epost_id=$epost_id ";
                DB::select($Qry);
                $delete_status = 1;
                $status = 1;
                $message = ' Record removed successfully !';
            }
        }
        return response()->json([
                    'data' => [],
                    'delete_status' => $delete_status,
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => $requiredStatus,
        ]); // NOT_FOUND (404) being the HTTP response code 
    }

    public function api_updateEpost(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $epost_data = $request->json()->get('epost_data') ? $request->json()->get('epost_data') : $request->input('epost_data');
        $patient_conf_id = $request->json()->get('patient_conf_id') ? $request->json()->get('patient_conf_id') : $request->input('patient_conf_id');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $stub_id = $request->json()->get('stub_id') ? $request->json()->get('stub_id') : $request->input('stub_id');
        $consent_template_id = $request->json()->get('consent_template_id') ? $request->json()->get('consent_template_id') : $request->input('consent_template_id');
        $table_name = $request->json()->get('table_name') ? $request->json()->get('table_name') : $request->input('table_name');
        $pConfirmId = $request->json()->get('patient_conf_id') ? $request->json()->get('patient_conf_id') : $request->input('patient_conf_id');
        $consent_template_id = $request->json()->get('consent_template_id') ? $request->json()->get('consent_template_id') : $request->input('consent_template_id');
        $table_name = $request->json()->get('table_name') ? $request->json()->get('table_name') : $request->input('table_name');
        $tbl_pri_id = $request->json()->get('tbl_pri_id') ? $request->json()->get('tbl_pri_id') : $request->input('tbl_pri_id');
        $message = " unauthorized ";
        $requiredStatus = [];
        $userId = $this->checkToken($userToken);
        $epost_status = 0;
        $update_status=0;
        $eAlertData = [];
        $ePostData = [];
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
            } else if ($iasc_facility_id == "") {
                $status = 1;
                $message = " IASC Id is missing ";
            } else {
                $arr = [
                    'epost_data' => $epost_data,
                    'dtdate' => date("Y-m-d"),
                    'T_time' => date("H:i:s"),
                    'table_name' => 'consent_multiple_form',
                    'patient_conf_id' => $patient_conf_id,
                    'patient_id' => $patient_id,
                    'consent_template_id' => $consent_template_id,
                    //'consentAutoIncId' => $consentAutoIncId,
                    'stub_id' => $stub_id,
                    'created_operator_id' => $userId,
                    'epost_consent_purge_status' => '',
                    'modified_operator_id' => 0,
                    'created_date_time' => date("Y-m-d H:i:s")
                ];
                DB::table('eposted')->where('epost_id', $tbl_pri_id)->update($arr);
                $update_status = 1;
                $status = 1;
                $message = ' Record updated successfully ';
            }
        }
        return response()->json([
                    'data' => [],
                    'epost_status' => $update_status,
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => $requiredStatus,
        ]); // NOT_FOUND (404) being the HTTP response code 
    }

    public function api_nametagformelement(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $pConfirmId = $request->json()->get('patient_conf_id') ? $request->json()->get('patient_conf_id') : $request->input('patient_conf_id');
        $showAllApptStatus = $request->json()->get('showAllApptStatus') ? $request->json()->get('showAllApptStatus') : $request->input('showAllApptStatus');
        $selected_date = $request->json()->get('selected_date') ? $request->json()->get('selected_date') : $request->input('selected_date');
        $message = " unauthorized ";
        $requiredStatus = [];
        $userId = $this->checkToken($userToken);
        $epost_status = 0;
        $eAlertData = [];
        $ePostData = [];
        $arrayRecord = [];
        if ($userId > 0) {
            if ($facility_id == "") {
                $message = " FacilityId is missing ";
                $status = 1;
                $requiredStatus = 0;
            } else if ($iasc_facility_id == "") {
                $status = 1;
                $message = " IASC Id is missing ";
            } else {
                $selected_dates = date("Y-m-d");
                if ($selected_date == '') {
                    $selected_date = date("Y-m-d");
                    $display_date = date("m/d/Y");
                }
                if ($showAllApptStatus != " ") {
                    $showAllApptStatus = $showAllApptStatus;
                } else {
                    $showAllApptStatus = 'Active';
                }

                $showAllApptStatusQry = "";
                if ($showAllApptStatus == "Active") {
                    $showAllApptStatusQry = " AND  patient_status NOT IN('Canceled','No Show')";
                } elseif ($showAllApptStatus == "Canceled") {
                    $showAllApptStatusQry = " AND  patient_status='Canceled'";
                } elseif ($showAllApptStatus == "No Show") {
                    $showAllApptStatusQry = " AND  patient_status='No Show'";
                }
                $fac_con = "";
                if ($iasc_facility_id != '') {
                    $fac_con = " and stub_tbl.iasc_facility_id='" . $iasc_facility_id . "'";
                }
                $status = 1;
                $sql_query = "SELECT fname, mname, lname,user_type,coordinator_type, practiceName FROM users WHERE usersId=$userId";
                $rows = DB::selectone($sql_query);
                $doss = "dos ='" . $selected_date . "'";
                $user_type = $rows->user_type;
                $coordinator_type = $rows->coordinator_type;
                $sql_query2 = "select surgeon_fname,surgeon_mname,surgeon_lname,surgery_time,pickup_time,arrival_time,stub_id,dos,patient_first_name,patient_middle_name,patient_last_name,patient_status,checked_in_time,checked_out_time,patient_dob,DATE_FORMAT(patient_dob,'%m/%d/%Y') as patient_dob_format,patient_confirmation_id,patient_id_stub,patient_primary_procedure,patient_secondary_procedure,patient_tertiary_procedure,comment,site,stub_secondary_site,stub_tertiary_site,chartSignedBySurgeon,chartSignedByNurse,chartSignedByAnes,recentChartSaved,imwPatientId,iasc_facility_id from stub_tbl where ";
                $sql_query2.= $doss;
                $sql_query2.= $fac_con;
                $sql_query2.= $showAllApptStatusQry;
                $sql_query2.= " order by iasc_facility_id, surgery_time";

                $chartFinalizeStatus = "false";
                $confPatientId = "0";
                $rows2 = DB::select($sql_query2);

# print(sql_query2)
                $chartFinalizeStatus = "false";
                $confPatientId = "0";
                if ($rows2) {
                    foreach ($rows2 as $row) {
                        if ($row->patient_confirmation_id > 0) {
                            $getPtIdQry = "SELECT patientId,finalize_status  FROM patientconfirmation WHERE patientConfirmationId='" . $row->patient_confirmation_id . "'";
                            $getPtIdRow = DB::selectone($getPtIdQry);
                            $chartFinalizeStatus = $getPtIdRow->finalize_status;
                            if ($row->patient_confirmation_id > 0 && $row->patient_id_stub == "") {
                                $confPatientId = $getPtIdRow->patientId;
                            }
#imwPatientQry="SELECT patient_id,imwPatientId FROM patient_data_tbl WHERE patient_id =%s"
# cursor.execute(imwPatientQry,(row[16]))
# imwPatientRow=cursor.fetchall()
# imwPatientId=70025 #imwPatientRow[0][1]
                            $surgery_time = $row->surgery_time; //datetime.strptime(str(row[3]), "%H:%M:%S")
#checked_in_time=datetime.strptime(str(row[12]), "%H:%M:%S")
#checked_out_time=datetime.strptime(str(row[13]), "%H:%M:%S")
                            $text_color = "#000000";
                            if ($row->patient_status == "Checked-Out") {
                                $text_color = "#0000FF";
                            }
                            if ($row->patient_status == "Checked-In") {
                                $text_color = "#008000";
                            }
                            $data[] = [
                                'stub_id' => $row->stub_id, 'dos' => $row->dos, 'patient_name' => $row->patient_last_name . "," . $row->patient_first_name . " " . $row->patient_middle_name,
                                'patient_dob' => $row->patient_dob, 'patient_id_stub' => $row->patient_id_stub,
                            ];
                        }
                    }
                    $requiredStatus = 0;
                    $message = " ";
                    $status = 1;
                }
            }
        }

        return response()->json([
                    'data' => $data,
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => $requiredStatus,
        ]); // NOT_FOUND (404) being the HTTP response code 
    }

    public function api_nametagformPDF(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $pConfirmId = $request->json()->get('patient_conf_id') ? $request->json()->get('patient_conf_id') : $request->input('patient_conf_id');
        $showAllApptStatus = $request->json()->get('showAllApptStatus') ? $request->json()->get('showAllApptStatus') : $request->input('showAllApptStatus');
        $selected_datess = $request->json()->get('selected_date') ? $request->json()->get('selected_date') : $request->input('selected_date');
        $pt_stub_id_list = $request->json()->get('pt_stub_id_list') ? $request->json()->get('pt_stub_id_list') : $request->input('pt_stub_id_list');
        $label_type = $request->json()->get('label_type') ? $request->json()->get('label_type') : $request->input('label_type');
        $label_range = $request->json()->get('label_range') ? $request->json()->get('label_range') : $request->input('label_range');
        $message = " unauthorized ";
        $requiredStatus = [];
        $userId = $this->checkToken($userToken);
        $arrayRecord = [];
        $pdfStatus = 0;
        $url = "";
        if ($userId > 0) {
            if ($facility_id == "") {
                $message = " FacilityId is missing ";
                $status = 1;
                $requiredStatus = 0;
            } else if ($iasc_facility_id == "") {
                $status = 1;
                $message = " IASC Id is missing ";
            } else if ($label_type == "") {
                $status = 1;
                $message = " Select Label ";
            } else {
                if (trim($selected_datess) <> "") {
                    $selected_dates = explode("-", $selected_datess);
                    if (!empty($selected_dates)) {
                        $selected_date = $selected_dates[1] . "-" . $selected_dates[2] . "-" . $selected_dates[0];
                    } else {
                        $selected_date = date("m-d-Y");
                    }
                } else {
                    $selected_date = date("m-d-Y");
                }
                $status = 1;
                $message = " Nametag PDF data";
                $url = getenv('APP_URL') . '/' . getenv('APP_ROOT') . '/day_labelpop_app.php?label=' . $label_type;
                $url.= '&range=' . $label_range;
                $url.= '&date12=' . $selected_date;
                $url.= '&facility=' . $facility_id;
                $url.= '&iasc_facility_id=' . $iasc_facility_id;
                $url.= '&showAllApptStatus=' . $showAllApptStatus;
                $url.= '&loginUser=' . $userId;
                $url.= '&pt_stub_id_list=' . $pt_stub_id_list;
                $pdfStatus = 1;
//return jsonify({"data": output, "status": status, 'message': message, 'pdfStatus': pdfStatus, 'pdf': url})
            }
        }
        return response()->json([
                    'data' => [],
                    'status' => $status,
                    'message' => $message,
                    'pdfStatus' => $pdfStatus,
                    'pdf' => $url,
                    'requiredStatus' => $requiredStatus,
        ]); // NOT_FOUND (404) being the HTTP response code 
    }

    public function api_prepost(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $selected_date = $request->json()->get('dos') ? $request->json()->get('dos') : $request->input('dos');
        $message = " unauthorized ";
        $requiredStatus = [];
        $userId = $this->checkToken($userToken);
        $arrayRecord = [];
        $pdfStatus = 0;
        $url = "";
        $stubRowArr = [];
        $pendingStubRowArr = [];
        $pendingStub = [];
        $Stub = [];
        if ($userId > 0) {
            if ($facility_id == "") {
                $message = " FacilityId is missing ";
                $status = 1;
                $requiredStatus = 0;
            } else if ($iasc_facility_id == "") {
                $status = 1;
                $message = " IASC Id is missing ";
            } else if ($selected_date == "") {
                $status = 1;
                $message = " DOS is missing ";
            } else {
                $fac_con = "";
                if ($iasc_facility_id != '') {
                    $fac_con = " and st.iasc_facility_id='" . $iasc_facility_id . "'";
                }

                $sql_query = "SELECT fname, mname, lname,user_type,coordinator_type, practiceName FROM users WHERE usersId='" . $userId . "'";
                $rows1 = DB::selectone($sql_query);
                $user_type = $rows1->user_type;
                $coordinator_type = $rows1->coordinator_type;
                $Qry = " SELECT GROUP_CONCAT(medicationName,'\n') as medicationName, GROUP_CONCAT(strength,'\n') as strength,GROUP_CONCAT(direction,'\n') as direction,GROUP_CONCAT(ppo.physician_order_name,'\n') as physician_order_name, GROUP_CONCAT(ppo.confirmation_id,'\n') as confirmation_id,";
                $Qry.= " DATE_FORMAT(st.patient_dob,'%m/%d/%Y') as dob, st.patient_primary_procedure, ";
                $Qry.= " st.patient_confirmation_id, sourcePage ,TRIM(CONCAT(st.patient_last_name,', ',st.patient_first_name,' ',st.patient_middle_name)) as patient_name ,";
                $Qry.= " DATE_FORMAT(st.patient_dob,'%m/%d/%Y') as dob, st.patient_primary_procedure, st.patient_confirmation_id,pc.patient_primary_procedure_id, pc.patient_secondary_procedure_id, pc.patient_tertiary_procedure_id,";
                $Qry.= " pr.form_status as pre_op_phy_form_status, pr.preOpPhysicianOrdersId,pr.signSurgeon1Id AS preOpPhySignSurgeonId, pr.signNurse1Id AS preOpPhySignNurse1Id, pr.notedByNurse as preOpOrdersNoted,";
                $Qry.= " ps.form_status as post_op_phy_form_status, ps.postOpPhysicianOrdersId, ps.notedByNurse as postOpOrdersNoted,ps.version_num as postOpPhysicianVersionNum,";
                $Qry.= " ps.signSurgeon1Id AS postOpPhySignSurgeonId, ps.signNurse1Id AS postOpPhySignNurse1Id,lp.signSurgeon1Id AS laserProcedureSignSurgeonId, lp.signNurseId AS laserProcedureSignNurseId,pcr.catId as procedureCatId,stub_id,DATE_FORMAT(st.dos,'%m/%d/%Y') as dos ";
                $Qry.= " FROM stub_tbl st";
                $Qry.= " LEFT JOIN patientconfirmation pc ON (pc.patientConfirmationId = st.patient_confirmation_id)";
                $Qry.= " LEFT JOIN preopphysicianorders pr ON (pr.patient_confirmation_id = st.patient_confirmation_id AND pr.patient_confirmation_id !=0)";
                $Qry.= " LEFT JOIN postopphysicianorders ps ON (ps.patient_confirmation_id = st.patient_confirmation_id AND ps.patient_confirmation_id !=0)";
                $Qry.= " LEFT JOIN laser_procedure_patient_table lp ON (lp.confirmation_id = st.patient_confirmation_id AND pr.patient_confirmation_id !=0)";
                $Qry.= " LEFT JOIN procedures pcr ON (pc.patient_primary_procedure_id = pcr.procedureId AND pr.patient_confirmation_id !=0)";
                $Qry.= " LEFT JOIN patientpreopmedication_tbl ptm ON (ptm.patient_confirmation_id=st.patient_confirmation_id AND ptm.patient_confirmation_id !=0)";
                $Qry.= " LEFT JOIN patient_physician_orders ppo ON (ppo.confirmation_id=st.patient_confirmation_id AND ppo.confirmation_id !=0)";
                $Qry.= " WHERE st.dos= '" . $selected_date . "'";
                $Qry.= $fac_con;
                $Qry.= " AND ((pr.signSurgeon1Id > 0 AND ps.signSurgeon1Id > 0 AND pr.form_status!='' AND ps.form_status!='') OR (lp.signSurgeon1Id > 0 AND lp.form_status!='' AND pcr.catId = '2' ))";
                $Qry.= " GROUP BY st.stub_id";
                $Qry.= " ORDER BY st.surgery_time,st.surgeon_fname";
                $rows2 = DB::select($Qry);
                if ($rows2) {
                    foreach ($rows2 as $rows) {
                        if (($rows->preOpPhySignSurgeonId > 0 and $rows->postOpPhySignSurgeonId > 0) or ( $rows->procedureCatId == '2' and $rows->laserProcSignSurgeonId > 0)) {
                            if (($rows->preOpPhySignNurse1Id == 0 or $rows->preOpOrdersNoted == 0 or ( ($rows->postOpPhySignNurse1Id == 0 or $rows->postOpOrdersNoted == 0) and $rows->postOpPhysicianVersionNum > 2)) and $rows->procedureCatId != '2') {
                                $pendingStubRowArr = [
                                    'stub_id' => $rows->stub_id,
                                    'confirmation_id' => $rows->confirmation_id,
                                    "Medication" => $rows->medicationName,
                                    "Strength" => $rows->strength,
                                    "Direction" => $rows->direction,
                                    "patientname" => $rows->patient_name,
                                    "dob" => $rows->dob,
                                    'dos' => $rows->dos,
                                    'procedure' => $rows->patient_primary_procedure
                                ];
                                $pendingStub[] = $pendingStubRowArr;
                            } elseif (laserProcSignNurseId == 0 and procedureCatId == '2') {
                                $pendingStubRowArr = [
                                    'stub_id' => $rows->stub_id,
                                    'confirmation_id' => $rows->confirmation_id,
                                    "Medication" => $rows->medicationName,
                                    "Strength" => $rows->strength,
                                    "Direction" => $rows->direction,
                                    "patientname" => $rows->patient_name,
                                    "dob" => $rows->dob,
                                    'dos' => $rows->dos,
                                    'procedure' => $rows->patient_primary_procedure
                                ];
                                $pendingStub[] = $pendingStubRowArr;
                            } else {
                                $stubRowArr = [
                                    'stub_id' => $rows->stub_id,
                                    'confirmation_id' => $rows->confirmation_id,
                                    "Medication" => $rows->medicationName,
                                    "Strength" => $rows->strength,
                                    "Direction" => $rows->direction,
                                    "patientname" => $rows->patient_name,
                                    "dob" => $rows->dob,
                                    'dos' => $rows->dos,
                                    'procedure' => $rows->patient_primary_procedure
                                ];
                                $Stub[] = $stubRowArr;
                            }
                        }
                    }
                }
            }
            $data = ['PendingPreOpPost' => $pendingStub, 'PreOpPostOp' => $Stub];
            $status = 1;
            $message = " Pre-Op/Post-Op Orders Noted By Nurse ";
        }

        return response()->json([
                    'data' => [],
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => $requiredStatus,
        ]); // NOT_FOUND (404) being the HTTP response code 
    }

    public function generatePDF(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $selected_date = $request->json()->get('selected_date') ? $request->json()->get('selected_date') : $request->input('selected_date');
        $dType = $request->json()->get('dType') ? $request->json()->get('dType') : $request->input('dType');
        $message = " unauthorized ";
        $requiredStatus = [];
        $userId = $this->checkToken($userToken);
        $pdfStatus = 0;
        $status = 0;
        $url = "";
        if ($userId > 0) {
            if ($facility_id == "") {
                $message = " FacilityId is missing ";
                $status = 1;
                $requiredStatus = 0;
            } else if ($iasc_facility_id == "") {
                $status = 1;
                $message = " IASC Id is missing ";
            } else if ($selected_date == "") {
                $status = 1;
                $message = " DOS is missing ";
            } else {
                $message = " PDF Generated Successfully !";
                $url = getenv('APP_URL') . '/' . getenv('APP_ROOT') . '/day_reportpop_app.php?date12=' . $selected_date . '&facility=' . $facility_id . '&iasc_facility_id=' . $iasc_facility_id . '&showAllApptStatus=Active&dType=' . $dType . '&loginUser=' . $userId . '&hidd_report_format=';
                $pdfStatus = 1;
                $status = 1;
            }
        }
        return response()->json([
                    'data' => [],
                    'status' => $status,
                    'message' => $message,
                    'pdfStatus' => (int) $pdfStatus,
                    'dType' => strtoupper($dType),
                    'pdf' => $url,
                    'requiredStatus' => $requiredStatus,
        ]); // NOT_FOUND (404) being the HTTP response code
    }

}
