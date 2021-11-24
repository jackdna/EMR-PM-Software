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

class TransferFollowupsController extends Controller {

    public function TransferFollowups_template(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
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
                $status = 1;
                //GET USER NAME
                $ViewUserNameQry = "select fname,mname,lname,user_type,signature from `users` where  usersId = '" . $userId . "'";
                $ViewUserNameRow = DB::selectone($ViewUserNameQry); // or die($ViewUserNameQry . imw_error());
                $loggedInUserFirstName = $ViewUserNameRow->fname;
                $loggedInUserMiddleName = $ViewUserNameRow->mname;
                $loggedInUserLastName = $ViewUserNameRow->lname;
                $user_type = $ViewUserNameRow->user_type;
                $user_signature = $ViewUserNameRow->signature;
                //GETTING SURGEONS DETAILS
                $message = ' Consent Details ';
                // GETTING FINALIZE STATUS
                $detailConfirmationFinalize = $this->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfirmId);
                $finalize_status = $detailConfirmationFinalize->finalize_status;
                // GETTING FINALIZE STATUS
                // Get Transfer Followps Details 
                $transferFollowpDetails = DB::selectone("select * from transfer_followups where confirmation_id='" . $pConfirmId . "'");
                $vitalSignGridStatus = $this->loadVitalSignGridStatus($transferFollowpDetails->form_status, $transferFollowpDetails->vitalSignGridStatus, 'transferFollowup');

                $signNurseName = $transferFollowpDetails->signNurseLastName . ", " . $transferFollowpDetails->signNurseFirstName . " " . $transferFollowpDetails->signNurseMiddleName;

                $signNurse1Name = $transferFollowpDetails->signNurse1LastName . ", " . $transferFollowpDetails->signNurse1FirstName . " " . $transferFollowpDetails->signNurse1MiddleName;

                $signSurgeon1Name = $transferFollowpDetails->signSurgeon1LastName . ", " . $transferFollowpDetails->signSurgeon1FirstName . " " . $transferFollowpDetails->signSurgeon1MiddleName;

                $loggedInUserDetail = $this->getRowRecord('users', 'usersId', $userId);
                $loggedInUserName = $loggedInUserLastName . ", " . $loggedInUserFirstName . " " . $loggedInUserMiddleName;
                $loggedInUserType = $user_type;
                $loggedInUserSig = $user_signature;

                if ($loggedInUserType <> "Nurse") {
                    $loginUserName = $loggedInUserName;
                } else {
                    $loginUserId = $userId;
                }
                // Nurse Signature
                $signOnFileStatusNurse = "Yes";
                $TDnurseNameIdDisplay = "";
                $TDnurseSignatureIdDisplay = "";
                $showNameNurse = $loggedInUserName;
                $chngBckGroundColor = '';
                $signBackColorNurse = $chngBckGroundColor;
                $whiteBckGroundColor = '';
                $signNurseDateTimeNew = $this->getFullDtTmFormat(date("Y-m-d H:i:s"));
                if ($transferFollowpDetails->signNurseId <> 0 && $transferFollowpDetails->signNurseId <> "") {
                    $showNameNurse = $signNurseName;
                    $signOnFileStatusNurse = $transferFollowpDetails->signNurseStatus;
                    //$signNurseDateTime = date("m-d-Y h:i A",strtotime($signNurseDateTime));
                    $signNurseDateTimeNew = $this->getFullDtTmFormat($transferFollowpDetails->signNurseDateTime);
                    $TDnurseNameIdDisplay = "none";
                    $TDnurseSignatureIdDisplay = "in";
                    $signBackColorNurse = $whiteBckGroundColor;
                }
                // End Nurse Signature
                // Nurse1 Signature
                $signOnFileStatusNurse1 = "Yes";
                $TDnurse1NameIdDisplay = "";
                $TDnurse1SignatureIdDisplay = "";
                $showNameNurse1 = $loggedInUserName;
                $signBackColorNurse1 = $chngBckGroundColor;
                $signNurse1DateTimeNew = $this->getFullDtTmFormat(date("Y-m-d H:i:s"));
                if ($transferFollowpDetails->signNurse1Id <> 0 && $transferFollowpDetails->signNurse1Id <> "") {
                    $showNameNurse1 = $signNurse1Name;
                    $signOnFileStatusNurse1 = $transferFollowpDetails->signNurse1Status;
                    //$signNurse1DateTime = date("m-d-Y h:i A",strtotime($signNurse1DateTime));
                    $signNurse1DateTimeNew = $this->getFullDtTmFormat($transferFollowpDetails->signNurse1DateTime);
                    $TDnurse1NameIdDisplay = "none";
                    $TDnurse1SignatureIdDisplay = "in";
                    $signBackColorNurse1 = $whiteBckGroundColor;
                }

                // End Nurse 1 Signature
                // Surgeon Sign
                $signOnFileStatusSurgeon1 = "Yes";
                $TDsurgeonNameIdDisplay = "";
                $TDsurgeonSignatureIdDisplay = "";
                $showNameSurgeon1 = $loggedInUserName;
                $signBackColorSurgeon1 = $chngBckGroundColor;
                $signSurgeon1DateTimeNew = $this->getFullDtTmFormat(date("Y-m-d H:i:s"));
                if ($transferFollowpDetails->signSurgeon1Id <> 0 && $transferFollowpDetails->signSurgeon1Id <> "") {
                    $showNameSurgeon1 = $signSurgeon1Name;
                    $signOnFileStatusSurgeon1 = $transferFollowpDetails->signSurgeon1Status;
                    //$signSurgeon1DateTime = date("m-d-Y h:i A",strtotime($signSurgeon1DateTime));
                    $signSurgeon1DateTimeNew = $this->getFullDtTmFormat($transferFollowpDetails->signSurgeon1DateTime);
                    $TDsurgeon1NameIdDisplay = "none";
                    $TDsurgeon1SignatureIdDisplay = "in";
                    $signBackColorSurgeon1 = $whiteBckGroundColor;
                }
                // End Surgeon Sign
                // End Get Transfer Followups Details
                $condArr = array();
                $condArr['confirmation_id'] = $pConfirmId;
                $condArr['chartName'] = 'transfer_and_followups_form';

                $gridData = $this->getMultiChkArrayRecords('vital_sign_grid', $condArr, 'gridRowId', 'Asc');
                $gCounter = 1;
                $gridDataFilled = [];
                $cnt = 0;
                if (is_array($gridData)) {
                    foreach ($gridData as $gridRow) {
                        //$fieldId1 = 'vitalSignGrid_'.$gCounter.'_1' ;	$fieldValue1= date('h:i A', strtotime($gridRow->start_time));
                        $fieldId1 = 'vitalSignGrid_' . $gCounter . '_1';
                        $fieldValue1 = $this->getTmFormat($gridRow->start_time);
                        $fieldId2 = 'vitalSignGrid_' . $gCounter . '_2';
                        $fieldValue2 = $gridRow->systolic;
                        $fieldId3 = 'vitalSignGrid_' . $gCounter . '_3';
                        $fieldValue3 = $gridRow->diastolic;
                        $fieldId4 = 'vitalSignGrid_' . $gCounter . '_4';
                        $fieldValue4 = $gridRow->pulse;
                        $fieldId5 = 'vitalSignGrid_' . $gCounter . '_5';
                        $fieldValue5 = $gridRow->rr;
                        $fieldId6 = 'vitalSignGrid_' . $gCounter . '_6';
                        $fieldValue6 = $gridRow->temp;
                        $fieldId7 = 'vitalSignGrid_' . $gCounter . '_7';
                        $fieldValue7 = $gridRow->etco2;
                        $fieldId8 = 'vitalSignGrid_' . $gCounter . '_8';
                        $fieldValue8 = $gridRow->osat2;
                        $gridDataFilled[] = array('id' => $gridRow->gridRowId, 'start_time' => $fieldValue1, 'systolic' => $fieldValue2, 'diastolic' => $fieldValue3, 'pulse' => $fieldValue4, 'rr' => $fieldValue5, 'temp' => $fieldValue6, 'etco2' => $fieldValue7, 'osat2' => $fieldValue8);
                        $cnt++;
                    }
                    if ($cnt < 15) {
                        for ($i = $cnt; $i < 15; $i++) {
                            $gridDataFilled[] = array('id' =>0,'start_time' => '', 'systolic' => '', 'diastolic' => '', 'pulse' => '', 'rr' => '', 'temp' => '', 'etco2' => '', 'osat2' => '');
                        }
                    }
                    for ($i = $cnt; $i < ($cnt + 15); $i++) {
                        $gridDataFilled[] = array('id' =>0,'start_time' => '', 'systolic' => '', 'diastolic' => '', 'pulse' => '', 'rr' => '', 'temp' => '', 'etco2' => '', 'osat2' => '');
                    }
                } else {
                    for ($i = $cnt; $i < 15; $i++) {
                        $gridDataFilled[] = array('id' =>0,'start_time' => '', 'systolic' => '', 'diastolic' => '', 'pulse' => '', 'rr' => '', 'temp' => '', 'etco2' => '', 'osat2' => '');
                    }
                }
                $dateDischarge = '';
                $backgroundColor1 = '#F6C67A';
                if ($transferFollowpDetails->date_discharge_from_hospital && $transferFollowpDetails->date_discharge_from_hospital <> '0000-00-00') {
                    $dateDischarge = date('m-d-Y', strtotime($transferFollowpDetails->date_discharge_from_hospital));
                    $backgroundColor1 = '#FFF';
                }
                $followupDate = '';
                $backgroundColor2 = '#F6C67A';

                if ($transferFollowpDetails->fDate && $transferFollowpDetails->fDate <> '0000-00-00') {
                    $followupDate = date('m-d-Y', strtotime($transferFollowpDetails->fDate));
                    $backgroundColor2 = '#FFF';
                }
                //$contactedTime = date('h:i A', strtotime($transferFollowpDetails->contacted_time));
                $contactedTime = $this->getTmFormat($transferFollowpDetails->contacted_time);
                if (($transferFollowpDetails->form_status <> 'completed' || $transferFollowpDetails->form_status <> 'not completed') && $transferFollowpDetails->contacted_time == '00:00:00') {
                    $contactedTime = '';
                }
                $summaryCareTime = $this->getTmFormat($transferFollowpDetails->summary_of_care_time);
                if (($transferFollowpDetails->form_status <> 'completed' || $transferFollowpDetails->form_status <> 'not completed') && $transferFollowpDetails->summary_of_care_time == '00:00:00') {
                    $summaryCareTime = '';
                }
                $contactedTimeBackColor = ($contactedTime) ? '#FFF' : '#F6C67A';
                $documents = array(
                    'transfer_forms' => ['Sent' => $transferFollowpDetails->transfer_forms == 3 ? 1 : 0, 'NotSent' => $transferFollowpDetails->transfer_forms == 2 ? 1 : 0, 'N/A' => $transferFollowpDetails->transfer_forms == 1 ? 1 : 0],
                    'demographics' => ['Sent' => $transferFollowpDetails->demographics == 3 ? 1 : 0, 'NotSent' => $transferFollowpDetails->demographics == 2 ? 1 : 0, 'N/A' => $transferFollowpDetails->demographics == 1 ? 1 : 0],
                    'chart_note' => ['Sent' => $transferFollowpDetails->chart_note == 3 ? 1 : 0, 'NotSent' => $transferFollowpDetails->chart_note == 2 ? 1 : 0, 'N/A' => $transferFollowpDetails->chart_note == 1 ? 1 : 0],
                    'lab_work' => ['Sent' => $transferFollowpDetails->lab_work == 3 ? 1 : 0, 'NotSent' => $transferFollowpDetails->lab_work == 2 ? 1 : 0, 'N/A' => $transferFollowpDetails->lab_work == 1 ? 1 : 0],
                    'ekg' => ['Sent' => $transferFollowpDetails->ekg == 3 ? 1 : 0, 'NotSent' => $transferFollowpDetails->ekg == 2 ? 1 : 0, 'N/A' => $transferFollowpDetails->ekg == 1 ? 1 : 0],
                    'advance_directive' => ['Sent' => $transferFollowpDetails->advance_directive == 3 ? 1 : 0, 'NotSent' => $transferFollowpDetails->advance_directive == 2 ? 1 : 0, 'N/A' => $transferFollowpDetails->advance_directive == 1 ? 1 : 0],
                    'cpr_report' => ['Sent' => $transferFollowpDetails->cpr_report == 3 ? 1 : 0, 'NotSent' => $transferFollowpDetails->cpr_report == 2 ? 1 : 0, 'N/A' => $transferFollowpDetails->cpr_report == 1 ? 1 : 0],
                    'patient_belongings' => $transferFollowpDetails->patient_belongings,
                    'additional_comments' => $transferFollowpDetails->additional_comments,
                );
                $data = [
                    'ReasonforTransfer' => ['Emergency' => $transferFollowpDetails->transfer_reason == 'Emergency' ? 1 : 0, 'Non-Emergency' => $transferFollowpDetails->transfer_reason == 'Non-Emergency' ? 1 : 0, 'ReasonforTransferDetails' => $transferFollowpDetails->transfer_reason_detail],
                    'HospitalDetails' => ['hospital' => $transferFollowpDetails->hospital_contacted, 'HospitalName' => $transferFollowpDetails->hospital_name, 'Time' => $contactedTime],
                    'Taxidetail' => ['Taxi' => ($transferFollowpDetails->transfer_method == 'Taxi' ? 1 : 0), 'PrivateCar' => ($transferFollowpDetails->transfer_method == 'Private-Car' ? 1 : 0), 'Ambulance' => $transferFollowpDetails->transfer_method == 'Ambulance' ? 1 : 0, 'AmbulanceProvider' => $transferFollowpDetails->ambulance_provider, 'Nurse1Sign' => ['stat' => $signOnFileStatusNurse, 'name' => $showNameNurse, 'Nurse1Signdatetime' => $signNurseDateTimeNew]],
                    'Airway' => ['IVRunning_yes' => $transferFollowpDetails->lv_running == 2 ? 1 : 0, 'IVRunning_no' => $transferFollowpDetails->lv_running == 1 ? 1 : 0, 'AirwaySupport_yes' => $transferFollowpDetails->airway_support == 2 ? 1 : 0, 'AirwaySupport_No' => $transferFollowpDetails->airway_support == 1 ? 1 : 0, 'O2@' => $transferFollowpDetails->o2at],
                    'VitalSign' => $gridDataFilled,
                    'SummaryofCare' => ['notes_text' => $transferFollowpDetails->summary_of_care_notes, 'SurgeonReassessment' => (int) $transferFollowpDetails->surgeon_reassessment, 'SurgeonSign' => ['stat' => $signOnFileStatusNurse1, 'name' => $showNameSurgeon1, 'SurgeonSigndatetime' => $signSurgeon1DateTimeNew], 'Time' => $summaryCareTime],
                    'HospitalTransferFollowUp' => ['dateDischarge' => $dateDischarge, 'date' => $followupDate, 'Nurse2Sign' => ['stat' => $signOnFileStatusNurse1, 'name' => $showNameNurse1, 'signature' => $signNurse1DateTimeNew], 'DischargeComments' => $transferFollowpDetails->discharge_comments],
                    'DocumentCheckList' => $documents,
                    'transferFollowupIdPrimary' => (isset($transferFollowpDetails->transferFollowupId) ? (int) $transferFollowpDetails->transferFollowupId : 0),
                    'form_status' => $transferFollowpDetails->form_status
                ];
            }
        }
        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => '',
                    'data' => $data,
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

    public function TransferFollowups_save(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $jsondata = $request->json()->get('jsondata') ? $request->json()->get('jsondata') : $request->input('jsondata');
        $transferFollowupIdPrimary = $request->json()->get('transferFollowupIdPrimary') ? $request->json()->get('transferFollowupIdPrimary') : $request->input('transferFollowupIdPrimary');
        $data = json_decode($jsondata);
        $status = 0;
        // $requiredStatus = 1;----
        $message = " unauthorized ";
        $requiredStatus = [];
        $userId = $this->checkToken($userToken);
        $patient_instruction_id = 0;
        $savedStatus = 0;
        if ($userId > 0) {
            if ($pConfirmId == "") {
                $message = " ConfirmId is missing ";
                $status = 1;
                $requiredStatus = 0;
            } else {
                $transferFollowpDetails = DB::selectone("select * from transfer_followups where confirmation_id='" . $pConfirmId . "'");
                $vitalSignGridStatus = $this->loadVitalSignGridStatus(isset($transferFollowpDetails->form_status) ? $transferFollowpDetails->form_status : 'not completed', isset($transferFollowpDetails->vitalSignGridStatus) ? $transferFollowpDetails->vitalSignGridStatus : 0, 'transferFollowup');
                $data_insert = [
                    'confirmation_id' => $pConfirmId,
                    'transfer_reason' => $data->transfer_reason->Emergency_Non_Emergency,
                    'transfer_reason_detail' => $data->transfer_reason->Transfer_Detail,
                    'hospital_contacted' => $data->Hospital_Contacted->Hospital == 'yes' ? 1 : 0,
                    'hospital_name' => $data->Hospital_Contacted->Hospital_Name,
                    'form_status' => isset($transferFollowpDetails->form_status) ? $transferFollowpDetails->form_status : "not completed",
                    'contacted_time' => $data->Hospital_Contacted->Time,
                    'signNurseId' => isset($transferFollowpDetails->signNurseId) ? $transferFollowpDetails->signNurseId : 0,
                    'signNurseFirstName' => isset($transferFollowpDetails->signNurseFirstName) ? $transferFollowpDetails->signNurseFirstName : "",
                    'signNurseMiddleName' => isset($transferFollowpDetails->signNurseMiddleName) ? $transferFollowpDetails->signNurseMiddleName : "",
                    'signNurseLastName' => isset($transferFollowpDetails->signNurseLastName) ? $transferFollowpDetails->signNurseLastName : "",
                    'signNurseStatus' => $data->Method_of_transfer->signNurseStatus,
                    'signNurseDateTime' => ($data->Method_of_transfer->signNurseDateTime != '0000-00-00 00:00:00') ? date("Y-m-d H:i:s", strtotime($data->Method_of_transfer->signNurseDateTime)) : '0000-00-00 00:00:00',
                    'transfer_method' => $data->Method_of_transfer->transfer_method,
                    'ambulance_provider' => $data->Method_of_transfer->AmbulanceProvider,
                    'lv_running' => $data->IV_Running->lv_running == 'yes' ? 2 : 1,
                    'airway_support' => $data->Airway_Support->airway_support == 'yes' ? 2 : 1,
                    'o2at' => $data->Airway_Support->O2,
                    'transfer_forms' => $data->Document_Check_List->Transfer_Forms,
                    'demographics' => $data->Document_Check_List->Demographics,
                    'chart_note' => $data->Document_Check_List->Chart_Note,
                    'lab_work' => $data->Document_Check_List->Lab_Work,
                    'ekg' => $data->Document_Check_List->EKG,
                    'advance_directive' => $data->Document_Check_List->Advance_Directive,
                    'cpr_report' => $data->Document_Check_List->CPR_Report,
                    'patient_belongings' => $data->Document_Check_List->Patient_Belongings,
                    'additional_comments' => $data->Document_Check_List->Additional_Comments,
                    'surgeon_reassessment' => $data->Summary_of_Care->surgeon_reassessment == 'yes' ? 1 : 0,
                    'signSurgeon1Id' => isset($transferFollowpDetails->signSurgeon1Id) ? $transferFollowpDetails->signSurgeon1Id : 0,
                    'signSurgeon1FirstName' => isset($transferFollowpDetails->signSurgeon1FirstName) ? $transferFollowpDetails->signSurgeon1FirstName : "",
                    'signSurgeon1MiddleName' => isset($transferFollowpDetails->signSurgeon1MiddleName) ? $transferFollowpDetails->signSurgeon1MiddleName : "",
                    'signSurgeon1LastName' => isset($transferFollowpDetails->signSurgeon1LastName) ? $transferFollowpDetails->signSurgeon1LastName : "",
                    'signSurgeon1Status' => $data->Summary_of_Care->signSurgeon1Status,
                    'signSurgeon1DateTime' => ($data->Summary_of_Care->signSurgeon1DateTime != '0000-00-00 00:00:00') ? date("Y-m-d H:i:s", strtotime($data->Summary_of_Care->signSurgeon1DateTime)) : '0000-00-00 00:00:00',
                    'summary_of_care_time' => $data->Summary_of_Care->summary_of_care_time,
                    'summary_of_care_notes' => $data->Summary_of_Care->summery_note_text,
                    'followup_status_filled' => 0,
                    'date_discharge_from_hospital' => ($data->Hospital_Transfer_Follow_Up->Date_Discharged_from_Hospital != '0000-00-00 00:00:00') ? date("Y-m-d H:i:s", strtotime($data->Hospital_Transfer_Follow_Up->Date_Discharged_from_Hospital)) : '0000-00-00 00:00:00',
                    'fDate' => ($data->Hospital_Transfer_Follow_Up->Date != '0000-00-00 00:00:00') ? date("Y-m-d H:i:s", strtotime($data->Hospital_Transfer_Follow_Up->Date)) : '0000-00-00 00:00:00',
                    'signNurse1Id' => isset($transferFollowpDetails->signNurse1Id) ? $transferFollowpDetails->signNurse1Id : 0,
                    'signNurse1FirstName' => isset($transferFollowpDetails->signNurse1FirstName) ? $transferFollowpDetails->signNurse1FirstName : "",
                    'signNurse1MiddleName' => isset($transferFollowpDetails->signNurse1MiddleName) ? $transferFollowpDetails->signNurse1MiddleName : "",
                    'signNurse1LastName' => isset($transferFollowpDetails->signNurse1LastName) ? $transferFollowpDetails->signNurse1LastName : "",
                    'signNurse1Status' => $data->Hospital_Transfer_Follow_Up->signNurse1Status,
                    'signNurse1DateTime' => ($data->Hospital_Transfer_Follow_Up->signNurse1DateTime != '0000-00-00 00:00:00') ? date("Y-m-d H:i:s", strtotime($data->Hospital_Transfer_Follow_Up->signNurse1DateTime)) : '0000-00-00 00:00:00',
                    'discharge_comments' => $data->Hospital_Transfer_Follow_Up->discharge_comments,
                    'vitalSignGridStatus' => $vitalSignGridStatus,
                    'resetDateTime' => date('Y-m-d H:i:s'),
                    'resetBy' => $userId
                ];
                // print_r($data_insert);die;
                if ($transferFollowupIdPrimary > 0) {
                    DB::table('transfer_followups')->where('transferFollowupId', $transferFollowupIdPrimary)->update($data_insert);
                } else {
                    $transferFollowupIdPrimary = DB::table('transfer_followups')->insertGetId($data_insert);
                }

                if (!empty($data->vital_sign)) {
                    DB::table('vital_sign_grid')->where('confirmation_id', $pConfirmId)->delete();
                    foreach ($data->vital_sign as $key => $val) {
                        $dataArray = array();
                        $dataArray['chartName'] = 'transfer_and_followups_form';
                        $dataArray['confirmation_id'] = $pConfirmId;
                        $dataArray['start_time'] = $val->start_time;
                        $dataArray['systolic'] = $val->systolic;
                        $dataArray['diastolic'] = $val->diastolic;
                        $dataArray['pulse'] = $val->pulse;
                        $dataArray['rr'] = $val->rr;
                        $dataArray['temp'] = $val->temp;
                        $dataArray['etco2'] = $val->etco2;
                        $dataArray['osat2'] = $val->osat2;
                        DB::table('vital_sign_grid')->insertGetId($dataArray);
                    }
                }
                $chkAuditStatus = 1;
                //CODE START TO SET AUDIT STATUS AFTER SAVE
                unset($conditionArr);
                $conditionArr['confirmation_id'] = $pConfirmId;
                $conditionArr['form_name'] = 'transfer_and_followups_form';
                $conditionArr['status'] = 'created';
                $chkAuditStatus = $this->getMultiChkArrayRecords('chartnotes_change_audit_tbl', $conditionArr);
                if ($chkAuditStatus) {
                    //MAKE AUDIT STATUS MODIFIED
                    $arrayStatusRecord['status'] = 'modified';
                } else {
                    //MAKE AUDIT STATUS CREATED
                    $arrayStatusRecord['status'] = 'created';
                }

                DB::table('chartnotes_change_audit_tbl')->insertGetId($arrayStatusRecord);
                //CODE END TO SET AUDIT STATUS AFTER SAVE
                //CODE TO CHECK SURGEON ALL SIGNATURE AND SET VALUE IN STUB TABLE
                $chartSignedBySurgeon = $this->chkSurgeonSignNew($pConfirmId);
                $updateStubTblQry = "UPDATE stub_tbl SET chartSignedBySurgeon='" . $chartSignedBySurgeon . "' WHERE patient_confirmation_id='" . $pConfirmId . "'";
                $updateStubTblRes = DB::select($updateStubTblQry);
                //END CODE TO CHECK SURGEON SIGNATURE AND SET VALUE IN STUB TABLE
                //CODE TO CHECK NURSE ALL SIGNATURE AND SET VALUE IN STUB TABLE
                $chartSignedByNurse = $this->chkNurseSignNew($pConfirmId);
                $updateNurseStubTblQry = "UPDATE stub_tbl SET chartSignedByNurse='" . $chartSignedByNurse . "' WHERE patient_confirmation_id='" . $pConfirmId . "'";
                $updateNurseStubTblRes = DB::select($updateNurseStubTblQry);
                //END CODE TO CHECK NURSE SIGNATURE AND SET VALUE IN STUB TABLE
                $status = 1;
                $message = 'Data has been saved successfully !';
                $savedStatus = 1;
            }
        }
        return response()->json([
                    'status' => $status,
                    'savedStatus' => $savedStatus,
                    'message' => $message,
                    'requiredStatus' => '',
                    'transferFollowupIdPrimary' => $transferFollowupIdPrimary,
                    'data' => [],
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

    public function TransferFollowups_reset(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        //$jsondata = $request->json()->get('jsondata') ? $request->json()->get('jsondata') : $request->input('jsondata');
        $transferFollowupIdPrimary = $request->json()->get('transferFollowupIdPrimary') ? $request->json()->get('transferFollowupIdPrimary') : $request->input('transferFollowupIdPrimary');
        
        $status = 0;
        // $requiredStatus = 1;----
        $message = " unauthorized ";
        $requiredStatus = [];
        $userId = $this->checkToken($userToken);
        $patient_instruction_id = 0;
        $savedStatus = 0;
        if ($userId > 0) {
            if ($pConfirmId == "") {
                $message = " ConfirmId is missing ";
                $status = 1;
                $requiredStatus = 0;
            } else if ($transferFollowupIdPrimary == "") {
                $message = " PrimaryId is missing ";
                $status = 1;
                $requiredStatus = 0;
            } else {
                $transferFollowpDetails = DB::selectone("select * from transfer_followups where confirmation_id='" . $pConfirmId . "'");
                $vitalSignGridStatus = $this->loadVitalSignGridStatus(isset($transferFollowpDetails->form_status) ? $transferFollowpDetails->form_status : 'not completed', isset($transferFollowpDetails->vitalSignGridStatus) ? $transferFollowpDetails->vitalSignGridStatus : 0, 'transferFollowup');
                $data_insert = [
                    'confirmation_id' => $pConfirmId,
                    'transfer_reason' => '',
                    'transfer_reason_detail' => '',
                    'hospital_contacted' => '',
                    'hospital_name' => '',
                    'form_status' => '',
                    'contacted_time' => '',
                    'signNurseId' => '',
                    'signNurseFirstName' => "",
                    'signNurseMiddleName' => "",
                    'signNurseLastName' => "",
                    'signNurseStatus' => '',
                    'signNurseDateTime' => '',
                    'transfer_method' => '',
                    'ambulance_provider' => '',
                    'lv_running' => '',
                    'airway_support' => '',
                    'o2at' => '',
                    'transfer_forms' => '',
                    'demographics' => '',
                    'chart_note' => '',
                    'lab_work' => '',
                    'ekg' => '',
                    'advance_directive' => '',
                    'cpr_report' => '',
                    'patient_belongings' => '',
                    'additional_comments' => '',
                    'surgeon_reassessment' => '',
                    'signSurgeon1Id' => '',
                    'signSurgeon1FirstName' => "",
                    'signSurgeon1MiddleName' => "",
                    'signSurgeon1LastName' => "",
                    'signSurgeon1Status' => '',
                    'signSurgeon1DateTime' => '',
                    'summary_of_care_time' => '',
                    'summary_of_care_notes' => '',
                    'followup_status_filled' => 0,
                    'date_discharge_from_hospital' => '',
                    'fDate' => '',
                    'signNurse1Id' => '',
                    'signNurse1FirstName' => "",
                    'signNurse1MiddleName' => "",
                    'signNurse1LastName' => "",
                    'signNurse1Status' => '',
                    'signNurse1DateTime' => '',
                    'discharge_comments' => '',
                    'vitalSignGridStatus' => '',
                    'resetDateTime' => date('Y-m-d H:i:s'),
                    'resetBy' => $userId
                ];
                // print_r($data_insert);die;
                if ($transferFollowupIdPrimary > 0) {
                    DB::table('transfer_followups')->where('transferFollowupId', $transferFollowupIdPrimary)->update($data_insert);
                } else {
                    //$transferFollowupIdPrimary = DB::table('transfer_followups')->insertGetId($data_insert);
                }
                DB::table('vital_sign_grid')->where('confirmation_id', $pConfirmId)->delete();
                $chkAuditStatus = 1;
                //CODE START TO SET AUDIT STATUS AFTER SAVE
                unset($conditionArr);
                $conditionArr['confirmation_id'] = $pConfirmId;
                $conditionArr['form_name'] = 'transfer_and_followups_form';
                $conditionArr['status'] = 'created';
                $chkAuditStatus = $this->getMultiChkArrayRecords('chartnotes_change_audit_tbl', $conditionArr);
                if ($chkAuditStatus) {
                    //MAKE AUDIT STATUS MODIFIED
                    $arrayStatusRecord['status'] = 'modified';
                } else {
                    //MAKE AUDIT STATUS CREATED
                    $arrayStatusRecord['status'] = 'created';
                }

                DB::table('chartnotes_change_audit_tbl')->insertGetId($arrayStatusRecord);
                //CODE END TO SET AUDIT STATUS AFTER SAVE
                //CODE TO CHECK SURGEON ALL SIGNATURE AND SET VALUE IN STUB TABLE
                $status = 1;
                $message = 'Data has been saved successfully !';
                $savedStatus = 1;
            }
        }
        return response()->json([
                    'status' => $status,
                    'savedStatus' => $savedStatus,
                    'message' => $message,
                    'requiredStatus' => '',
                    'transferFollowupIdPrimary' => $transferFollowupIdPrimary,
                    'data' => [],
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

}
