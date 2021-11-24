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

class InjectionMiscellaneousController extends Controller {

    public function InjectionMiscellaneous_form(Request $request) {
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
        $consent_content = '';
        $left_list = '';
        $stat = [];
        $surgery_consent_id = 0;

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
                $ViewUserNameQry = "select fname,mname,lname,user_type from `users` where  usersId = '" . $userId . "'";
                $ViewUserNameRow = DB::selectone($ViewUserNameQry); // or die($ViewUserNameQry . imw_error());
                $loggedInUserFirstName = $ViewUserNameRow->fname;
                $loggedInUserMiddleName = $ViewUserNameRow->mname;
                $loggedInUserLastName = $ViewUserNameRow->lname;
                $user_type = $ViewUserNameRow->user_type;
                $signOnFileStatus = 'Yes';
                $loggedInUserName = $ViewUserNameRow->lname . ", " . $ViewUserNameRow->fname . " " . $ViewUserNameRow->mname;

// GETTING CONFIRMATION DETAILS for signatures
                $detailConfirmation = $this->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfirmId);

                $surgeonId = $detailConfirmation->surgeonId;
                $patientConfirmSiteTempSite = $detailConfirmation->site;
                $patient_primary_procedure_id = $detailConfirmation->patient_primary_procedure_id;
                $patient_primary_procedure_cat_misc = $detailConfirmation->prim_proc_is_misc;
                $patient_primary_procedure_name = $detailConfirmation->patient_primary_procedure;
                $ascIdConfirm = $detailConfirmation->ascId;

//START GET PATIENT DETAIL
                $imwPatientIdInjection = "";
                $injectionPatientName_tblQry = "SELECT * FROM `patient_data_tbl` WHERE `patient_id` = '" . $patient_id . "'";
                $injectionPatientName_tblRow = DB::selectone($injectionPatientName_tblQry); // or die(imw_error());

                $imwPatientIdInjection = $injectionPatientName_tblRow->imwPatientId;
//END GET PATIENT DETAIL
                //check whether procedure is injection/Misc
                $str_procedure_category = "SELECT P.catId, PC.isMisc FROM procedures P Join procedurescategory PC on P.catId = PC.proceduresCategoryId  WHERE P.procedureId  = '" . $patient_primary_procedure_id . "'";
                $fetchRows_procedure_category = DB::selectone($str_procedure_category);
                $patient_primary_procedure_categoryID = $fetchRows_procedure_category->catId;
                //check whether procedure is injection/Misc
                //GET ASSIGNED SURGEON ID AND SURGEON NAME
                $assignedSurgeonId = $detailConfirmation->surgeonId;
                $assignedSurgeonName = stripslashes($detailConfirmation->surgeon_name);
                //END GET ASSIGNED SURGEON ID AND SURGEON NAME

                $surgeonsDetails = DB::selectone("select * from users where usersId=$surgeonId"); //$this->getMultiChkArrayRecords('users', $conditionArr);
                if ($surgeonsDetails) {
                    $signatureOfSurgeon = $surgeonsDetails->signature;
                }
                //get site from header
                // APPLYING NUMBERS TO PATIENT SITE
                $patientConfirmSiteName = '';
                if ($patientConfirmSiteTempSite == 1) {
                    $patientConfirmSiteTempSite = "Left Eye";  //OD
                    $patientConfirmSiteName = "left eye";  //OD
                } else if ($patientConfirmSiteTempSite == 2) {
                    $patientConfirmSiteTempSite = "Right Eye";  //OS
                    $patientConfirmSiteName = "right eye";  //OS
                } else if ($patientConfirmSiteTempSite == 3) {
                    $patientConfirmSiteTempSite = "Both Eye";  //OU
                    $patientConfirmSiteName = "both eye";  //OU
                } else {
                    $patientConfirmSiteTempSite = "Operative Eye";  //OU
                }
                // END APPLYING NUMBERS TO PATIENT SITE
                //**************************************GET LOGGED IN USER TYPE
                $surgeonsDetails = DB::selectone("select * from users where usersId=$userId");
                if ($surgeonsDetails) {
                    $loggedUserType = $surgeonsDetails->user_type;
                }
                /*                 * ************************************END GET LOGGED IN USER TYPE************************************* */

                $res = DB::selectone("select *, date_format(signSurgeon1DateTime,'%m-%d-%Y %h:%i %p') as signSurgeon1DateTimeFormat, date_format(signNurse1DateTime,'%m-%d-%Y %h:%i %p') as signNurse1DateTimeFormat, date_format(signNurse2DateTime,'%m-%d-%Y %h:%i %p') as signNurse2DateTimeFormat from `injection` where  confirmation_id = '" . $pConfirmId . "'");
                if (!$res->form_status) {
                    //Get Default Profile
                    $fields = 'timeoutReq,preOpMeds,intravitrealMeds,postOpMeds,consentTemplateId';
                    $defaultProfile = $this->injectionProfile($patient_primary_procedure_id, $assignedSurgeonId, $fields);
                    //print_r($defaultProfile);
                    if ($defaultProfile['profileFound']) {
                        extract($defaultProfile['data']);
                        // Check if consent forms signed
                        if ($consentTemplateId) {
                            $consentChkQry = "Select * From consent_multiple_form Where consent_template_id IN (" . $consentTemplateId . ") And confirmation_id = '" . $pConfirmId . "' And (form_status = 'not completed' OR form_status = '') And consent_purge_status <> 'true' ";
                            $consentChkSql = DB::select($consentChkQry); // or die($consentChkQry . imw_error());
                            $chkConsentSigned = 1;
                            if ($consentChkSql) {
                                $chkConsentSigned = 0;
                            }
                        }
                        // End Check if consent forms signed
                    }
                }

                $preVitalTime = ($res->preVitalTime <> '00:00:00') ? $this->getTmFormat($res->preVitalTime) : '';
                $timeoutTime = ($res->timeoutTime <> '00:00:00') ? $this->getTmFormat($res->timeoutTime) : '';
                $startTime = ($res->startTime <> '00:00:00') ? $this->getTmFormat($res->startTime) : '';
                $endTime = ($res->endTime <> '00:00:00') ? $this->getTmFormat($res->endTime) : '';
                $postVitalTime = ($res->postVitalTime <> '00:00:00') ? $this->getTmFormat($res->postVitalTime) : '';
                $postIopTime = ($res->postIopTime <> '00:00:00') ? $this->getTmFormat($res->postIopTime) : '';

                $preVitalGroupArr = array($preVitalTime, $res->preVitalBp, $res->preVitalPulse, $res->preVitalResp, $res->preVitalSpo);
              
                $complicationsArr = array($res->complications, $res->comments);
                // $complicationsBgColor = ($this->validateGroupOR($complicationsArr)) ? $whiteBckGroundColor : $chngBckGroundColor;
                $complicationsGrpFun = "changeDiffChbxColorNew('1','.complications');";

                $postVitalGroupArr = array($postVitalTime, $res->postVitalBp, $res->postVitalPulse, $res->postVitalResp, $res->postVitalSpo);
                
                $surgeon1SignOnFileStatus = "Yes";
                $TDsurgeon1NameIdDisplay = "block";
                $TDsurgeon1SignatureIdDisplay = "none";
                $Surgeon1NameShow = $loggedInUserName;
                $signSurgeon1DateTimeFormatNew = date("m-d-Y h:i A");

                if ($res->signSurgeon1Id <> 0 && $res->signSurgeon1Id <> "") {
                    $Surgeon1NameShow = $res->signSurgeon1LastName . ", " . $res->signSurgeon1FirstName . " " . $res->signSurgeon1MiddleName;
                    $surgeon1SignOnFileStatus = $res->signSurgeon1Status;
                    $TDsurgeon1NameIdDisplay = "none";
                    $TDsurgeon1SignatureIdDisplay = "block";
                    $signSurgeon1DateTimeFormatNew = $res->signSurgeon1DateTimeFormat;
                }


                $nurse1SignOnFileStatus = "Yes";
                $TDnurse1NameIdDisplay = "block";
                $TDnurse1SignatureIdDisplay = "none";
                $Nurse1NameShow = $loggedInUserName;

                $signNurse1DateTimeFormatNew = date("m-d-Y h:i A");

                if ($res->signNurse1Id <> 0 && $res->signNurse1Id <> "") {
                    $Nurse1NameShow = $res->signNurse1LastName . ", " . $res->signNurse1FirstName . " " . $res->signNurse1MiddleName;
                    $nurse1SignOnFileStatus = $res->signNurse1Status;
                    $TDnurse1NameIdDisplay = "none";
                    $TDnurse1SignatureIdDisplay = "block";
                    $signNurse1DateTimeFormatNew = $res->signNurse1DateTimeFormat;
                }

                // Nurse 2 Signature
                $nurse2SignOnFileStatus = "Yes";
                $TDnurse2NameIdDisplay = "block";
                $TDnurse2SignatureIdDisplay = "none";
                $Nurse2NameShow = $loggedInUserName;

                $signNurse2DateTimeFormatNew = date("m-d-Y h:i A");

                if ($res->signNurse2Id <> 0 && $res->signNurse2Id <> "") {
                    $Nurse2NameShow = $res->signNurse2LastName . ", " . $res->signNurse2FirstName . " " . $res->signNurse2MiddleName;
                    $nurse2SignOnFileStatus = $res->signNurse2Status;
                    $TDnurse2NameIdDisplay = "none";
                    $TDnurse2SignatureIdDisplay = "block";
                    $signNurse2DateTimeFormatNew = $res->signNurse2DateTimeFormat;
                }

                //$surgeon1SignBackColor = ($signSurgeon1Id > 0 ) ? $whiteBckGroundColor : $chngBckGroundColor;
                //$nurse1SignBackColor = ($signNurse1Id > 0 ) ? $whiteBckGroundColor : $chngBckGroundColor;
                //timeoutProcVerified$nurse2SignBackColor = ($signNurse2Id > 0 ) ? $whiteBckGroundColor : $chngBckGroundColor;

                $detailConfirmation = $this->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfirmId);
                $finalizeStatus = $detailConfirmation->finalize_status;
                $allergiesNKDA_patientconfirmation_status = $detailConfirmation->allergiesNKDA_status;
                $noMedicationStatus = $detailConfirmation->no_medication_status;
                $noMedicationComments = $detailConfirmation->no_medication_comments;

                $patientAllergies = [];
                $cntHlt = 0;

                $medication_query = "SELECT medicationsId,name,isDefault FROM `medications` ORDER BY `medications`.`name` ASC ";
                $medication_res = DB::select($medication_query);
                $medication = [];
                foreach ($medication_res as $row) {
                    $medication[] = ['medicationsId' => $row->medicationsId, 'name' => $row->name, 'isDefault' => $row->isDefault];
                }

                $IOP = [["key" => "TA", "Value" => "A"], ["key" => "TP", "Value" => "P"], ["key" => "TT", "Value" => "T"], ["key" => "TX", "Value" => "X"]];
                $data = [
                    "VitalSignsPreOp" => ["Time" => $res->preVitalTime, "BP" => $res->preVitalBp, "P" => $res->preVitalPulse, "R" => $res->preVitalResp, "Spo2" => $res->preVitalSpo],
                    "Timeout" => ["Siteverified" => $res->timeoutSiteVerified, "Eye" => $patientConfirmSiteTempSite, "ProcedureVerified" => $res->timeoutProcVerified, "ProcedureValue" => $patient_primary_procedure_name, "Time" => $timeoutTime, "Nurse" => ["name" => $Nurse1NameShow, "signed_status" => $nurse1SignOnFileStatus, "sign_date" => $res->signNurse1DateTime != '0000-00-00 00:00:00' ? date("m-d-Y h:i A", strtotime($res->signNurse1DateTime)) : date("m-d-Y h:i A")]],
                    "Procedure" => ["Site" => $patientConfirmSiteTempSite, "Procedure" => $patient_primary_procedure_name, "ConsentSigned" => $res->chkConsentSigned, "StartTime" => $startTime, "EndTime" => $endTime, "Comments" => $res->procedureComments],
                    "PreOPMed" => ['dropdown' => $medication, 'patient_prescriptions' => $res->preOpMeds], //$patient_prescriptions],
                    "IntravitrealMed" => ['dropdown' => $medication, 'patient_prescriptions' => $res->intravitrealMeds], //$patient_prescriptions],
                    "PostOPMed" => ['dropdown' => $medication, 'patient_prescriptions' => $res->postOpMeds], //$patient_prescriptions],Intravitreal
                    "Complication" => ["Complications" => $res->complications, "Comments" => $res->comments],
                    "VitalSignsPostOp" => ["Time" => $res->postVitalTime, "BP" => $res->postVitalBp, "P" => $res->postVitalPulse, "R" => $res->postVitalResp, "Spo2" => $res->postVitalSpo, "IOP" => $IOP, "IOPVal" => $res->postIop, "OD_OS" => $res->postIopSite, "Time2" => $res->postIopTime],
                    "signature" => [
                        "surgeon_signature" => ["name" => $Surgeon1NameShow, "signed_status" => $surgeon1SignOnFileStatus, "sign_date" => $res->signSurgeon1DateTime != '0000-00-00 00:00:00' ? date("m-d-Y h:i A", strtotime($res->signSurgeon1DateTime)) : date("m-d-Y h:i A")],
                        "nurse_signature" => ["name" => $Nurse2NameShow, "signed_status" => $nurse2SignOnFileStatus, "sign_date" => $res->signNurse2DateTime != '0000-00-00 00:00:00' ? date("m-d-Y h:i A", strtotime($res->signNurse2DateTime)) : date("m-d-Y h:i A")]
                    ]
                ];
                $status = 1;
                $message = " Injection/Miscellaneous ";
            }
        }
        return response()->json(['status' => $status, 'message' => $message, 'requiredStatus' => '', 'data' => $data,
                    'injId' => isset($res->injId) ? $res->injId : 0,
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

    public function InjectionMiscellaneous_save(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $loginUserType = $request->json()->get('UserType') ? $request->json()->get('UserType') : $request->input('UserType');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $injId = $request->json()->get('injId') ? $request->json()->get('injId') : $request->input('injId');
        $jsondata = $request->json()->get('jsondata') ? $request->json()->get('jsondata') : $request->input('jsondata');
        $json = json_decode($jsondata);
       // print '<pre>';
        //echo $json->VitalSignsPreOp->Time;
        //print_r($json);
        //die;
        $data = [];
        $status = 0;
        $moveStatus = 0;
        $arrayRecord = [];
        $message = " unauthorized ";
        $requiredStatus = [];
        $userId = $this->checkToken($userToken);
        $patient_instruction_id = 0;
        $andConsentIdQry = '';
        $ampConsentAutoId = '';
        $surgery_consent_data = "";
        $signStatus = [];
        $consent_content = '';
        $left_list = '';
        $stat = [];
        $arrayRecord = [];
        $arrayStatusRecord = [];
        $surgery_consent_id = 0;
        $savedStatus = 0;
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
            } else if ($patient_id == "") {
                $status = 1;
                $message = " PatientId is missing ";
            } else {
                //GETTING CONFIRNATION DETAILS
                /* Sorting Pre Op Medication */
                $tablename = "injection";
                // GETTING CONFIRMATION DETAILS for signatures
                $detailConfirmation = $this->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfirmId);

                $surgeonId = $detailConfirmation->surgeonId;
                $patientConfirmSiteTempSite = $detailConfirmation->site;
                $patient_primary_procedure_id = $detailConfirmation->patient_primary_procedure_id;
                $patient_primary_procedure_cat_misc = $detailConfirmation->prim_proc_is_misc;
                $patient_primary_procedure_name = $detailConfirmation->patient_primary_procedure;
                $ascIdConfirm = $detailConfirmation->ascId;
                $assignedSurgeonId = $detailConfirmation->surgeonId;
                $assignedSurgeonName = stripslashes($detailConfirmation->surgeon_name);
                //START GET PATIENT DETAIL
                $imwPatientIdInjection = "";
                $injectionPatientName_tblQry = "SELECT imwPatientId FROM `patient_data_tbl` WHERE `patient_id` = '" . $patient_id . "'";
                $injectionPatientName_tblRow = DB::selectone($injectionPatientName_tblQry); // or die(imw_error());

                $imwPatientIdInjection = $injectionPatientName_tblRow->imwPatientId;
                //END GET PATIENT DETAIL
                //check whether procedure is injection/Misc
                $str_procedure_category = "SELECT P.catId, PC.isMisc FROM procedures P Join procedurescategory PC on P.catId = PC.proceduresCategoryId  WHERE P.procedureId  = '" . $patient_primary_procedure_id . "'";
                $fetchRows_procedure_category = DB::selectone($str_procedure_category);
                $patient_primary_procedure_categoryID = $fetchRows_procedure_category->catId;
                //Start Getting Already Saved values 
                $dataRow = $this->getRowRecord($tablename, 'confirmation_id', $pConfirmId);
                $chk_injectionId = $dataRow->injId;
                $chk_surgeon1Id = $dataRow->signSurgeon1Id;
                $chk_nurse1Id = $dataRow->signNurse1Id;
                $chk_nurse2Id = $dataRow->signNurse2Id;
                $chk_formStatus = $dataRow->form_status;
                $chk_timeoutReq = $dataRow->timeoutReq;
                $chk_startTime = $dataRow->startTime;
                $chk_endTime = $dataRow->endTime;
                if ($chk_formStatus <> 'completed' || $chk_formStatus <> 'not completed') {  // If Form is not saved for once then load default profile
                    // for timeout required or not
                    $fields = 'timeoutReq';
                    $defaultProfile = $this->injectionProfile($patient_primary_procedure_id, $assignedSurgeonId, $fields);
                    if ($defaultProfile['profileFound']) {
                        $chk_timeoutReq = $defaultProfile['data']->timeoutReq;
                    }
                }
                //End Getting Already Saved values 
                //Start Chart Validation Here
                $formStatus = 'completed';
                
                $preVital =isset($json->VitalSignsPreOp)?array($json->VitalSignsPreOp->Time, $json->VitalSignsPreOp->BP, $json->VitalSignsPreOp->P, $json->VitalSignsPreOp->R, $json->VitalSignsPreOp->Spo2):[];
                $postVital =isset($json->VitalSignsPreOp)?array($json->VitalSignsPostOp->Time, $json->VitalSignsPostOp->BP, $json->VitalSignsPostOp->P, $json->VitalSignsPostOp->R, $json->VitalSignsPostOp->Spo2):[];
                $compValid =isset($json->Complications)? array($json->Complication->Complications, $json->Complication->Comments):[];
                $andArray =isset($json->Procedure->StartTime)?array($json->Procedure->StartTime, $json->Procedure->EndTime, $json->Procedure->ConsentSigned, $json->Procedure->Procedure, $json->VitalSignsPostOp->IOPVal, $json->VitalSignsPostOp->OD_OS, $json->VitalSignsPostOp->Time2):[];

                if (!($this->validateGroupOR($preVital)) || !($this->validateGroupOR($postVital)) || !($this->validateGroupOR($compValid)) || !($this->validateGroupAND($andArray)) || !($chk_nurse1Id) || !($chk_nurse2Id) || !($chk_surgeon1Id)
                ) {
                    $formStatus = 'not completed';
                }

                //Start Validate timeout fields if timeout required is checked from default profile/saved value
                if ($chk_timeoutReq && $formStatus == 'completed') {
                    $timeoutArray = array($json->Timeout->Time, $json->Timeout->ProcedureVerified, $json->Timeout->SiteVerified);
                    if (!($this->validateGroupAND($timeoutArray))) {
                        $formStatus = 'not completed';
                    }
                }
                //End Validate timeout fields if timeout required is checked from default profile/saved value
                //End Chart Validation Here
                // Check if surgery start time or surgery end time field values changed
                $start_time_staus = '0';
                if ((isset($json->Procedure->StartTime) && $json->Procedure->StartTime <> date('H:i:s', strtotime($chk_startTime)) ) || (isset($json->Procedure->EndTime) && $json->Procedure->EndTime <> date('H:i:s', strtotime($chk_endTime)) )
                ) {
                    $start_time_staus = '1';
                }

                $arrayRecord['preVitalTime'] = isset($json->VitalSignsPreOp->Time) ? $json->VitalSignsPreOp->Time : "";
                $arrayRecord['preVitalBp'] = isset($json->VitalSignsPreOp->BP) ? addslashes($json->VitalSignsPreOp->BP) : "";
                $arrayRecord['preVitalPulse'] = isset($json->VitalSignsPreOp->P) ? addslashes($json->VitalSignsPreOp->P) : ""; // addslashes($_POST['preVitalPulse']);
                $arrayRecord['preVitalResp'] = isset($json->VitalSignsPreOp->R) ? addslashes($json->VitalSignsPreOp->R) : ""; //addslashes($_POST['preVitalResp']);
                $arrayRecord['preVitalSpo'] = isset($json->VitalSignsPreOp->Spo2) ? addslashes($json->VitalSignsPreOp->Spo2) : ""; //addslashes($_POST['preVitalSpo']);

                $arrayRecord['timeoutReq'] = $chk_timeoutReq;
                $arrayRecord['timeoutTime'] = isset($json->Timeout->Time) ? $json->Timeout->Time : ""; // $_POST['timeoutTime'];
                $arrayRecord['timeoutProcVerified'] = isset($json->Timeout->ProcedureVerified) ? addslashes($json->Timeout->ProcedureVerified) : ""; //  addslashes($_POST['timeoutProcVerified']);
                $arrayRecord['timeoutSiteVerified'] = isset($json->Timeout->Siteverified) ? addslashes($json->Timeout->Siteverified) : ""; // addslashes($_POST['timeoutSiteVerified']);

                $arrayRecord['startTime'] = isset($json->Procedure->StartTime) ? addslashes($json->Procedure->StartTime) : ""; //$_POST['startTime'];
                $arrayRecord['endTime'] = isset($json->Procedure->EndTime) ? addslashes($json->Procedure->EndTime) : ""; //$_POST['endTime'];
                $arrayRecord['chkConsentSigned'] = isset($json->Procedure->ConsentSigned) ? addslashes($json->Procedure->ConsentSigned) : ""; // addslashes($_POST['chkConsentSigned']);
                $arrayRecord['procedureComments'] = isset($json->Procedure->Comments) ? addslashes($json->Procedure->Comments) : ""; //  addslashes($_POST['procedureComments']);

                $arrayRecord['preOpMeds'] = isset($json->PreOPMed->patient_prescriptions) ? addslashes($json->PreOPMed->patient_prescriptions) : ""; // addslashes($preOpMedsDB);
                $arrayRecord['intravitrealMeds'] = isset($json->IntravitrealMed->patient_prescriptions) ? addslashes($json->IntravitrealMed->patient_prescriptions) : ""; //  addslashes($intravitrealMedsDB);
                $arrayRecord['postOpMeds'] = isset($json->PostOPMed->patient_prescriptions) ? addslashes($json->PostOPMed->patient_prescriptions) : ""; // addslashes($postOpMedsDB);

                $arrayRecord['complications'] = isset($json->Complication->Complications) ? $json->Complication->Complications : ""; // addslashes($_POST['complications']);
                $arrayRecord['comments'] = isset($json->Complication->Comments) ? $json->Complication->Comments : ""; // addslashes($_POST['comments']);

                $arrayRecord['postVitalTime'] = isset($json->VitalSignsPostOp->Time) ? $json->VitalSignsPostOp->Time : ""; // $_POST['postVitalTime'];
                $arrayRecord['postVitalBp'] = isset($json->VitalSignsPostOp->BP) ? $json->VitalSignsPostOp->BP : ""; //  addslashes($_POST['postVitalBp']);
                $arrayRecord['postVitalPulse'] = isset($json->VitalSignsPostOp->P) ? $json->VitalSignsPostOp->P : ""; //  addslashes($_POST['postVitalPulse']);
                $arrayRecord['postVitalResp'] = isset($json->VitalSignsPostOp->R) ? $json->VitalSignsPostOp->R : ""; //   addslashes($_POST['postVitalResp']);
                $arrayRecord['postVitalSpo'] = isset($json->VitalSignsPostOp->Spo2) ? $json->VitalSignsPostOp->Spo2 : ""; //  addslashes($_POST['postVitalSpo']);

                $arrayRecord['postIop'] = isset($json->VitalSignsPostOp->IOPVal) ? $json->VitalSignsPostOp->IOPVal : ""; // addslashes($_POST['postIop']);
                $arrayRecord['postIopSite'] = isset($json->VitalSignsPostOp->OD_OS) ? $json->VitalSignsPostOp->OD_OS : ""; // addslashes($_POST['postIopSite']);
                $arrayRecord['postIopTime'] = isset($json->VitalSignsPostOp->Time2) ? $json->VitalSignsPostOp->Time2 : ""; //  $_POST['postIopTime'];
                $arrayRecord['form_status'] = $formStatus;
                $arrayRecord['start_time_status'] = $start_time_staus;
                if ($injId > 0) {
                    //$objManageData->UpdateRecord($arrayRecord, 'injection', 'confirmation_id', $pConfirmId);
                    DB::table('injection')->where('injId', $injId)->update($arrayRecord);
                } else {
                    //$objManageData->addRecords($arrayRecord, 'injection');
                    $injId = DB::table('injection')->insertGetId($arrayRecord);
                }

                // Update patientconfirmation table on only first save of chart
                if ($chk_formStatus <> 'completed' && $chk_formStatus <> 'not completed') {
                    unset($arrayConfirmationRecord);
                    $arrayConfirmationRecord['prim_proc_is_misc'] = $this->verifyProcIsInjMisc($patient_primary_procedure_id);
                    DB::table('patientconfirmation')->where('patientConfirmationId', $pConfirmId)->update($arrayConfirmationRecord);
                }
                // End Update patientconfirmation table on only first save of chart

                /*                 * *****************************
                  Creating Audit Status on Save
                 * ****************************** */

                //MAKE AUDIT STATUS
                unset($arrayStatusRecord);
                $arrayStatusRecord['user_id'] = $userId;
                $arrayStatusRecord['patient_id'] = $patient_id;
                $arrayStatusRecord['confirmation_id'] = $pConfirmId;
                $arrayStatusRecord['form_name'] = 'injection_procedure_form';
                $arrayStatusRecord['action_date_time'] = date('Y-m-d H:i:s');
                //MAKE AUDIT STATUS
                //CODE START TO SET AUDIT STATUS AFTER SAVE
                unset($conditionArr);
                $conditionArr['confirmation_id'] = $pConfirmId;
                $conditionArr['form_name'] = 'injection_misc_form';
                $conditionArr['status'] = 'created';
                $chkAuditStatus = $this->getMultiChkArrayRecords('chartnotes_change_audit_tbl', $conditionArr);
                if ($chkAuditStatus) {
                    $arrayStatusRecord['status'] = 'modified';
                } else {
                    $arrayStatusRecord['status'] = 'created';
                }
                DB::table('chartnotes_change_audit_tbl')->insert($arrayStatusRecord);
                //$objManageData->addRecords($arrayStatusRecord, 'chartnotes_change_audit_tbl');
                //CODE END TO SET AUDIT STATUS AFTER SAVE
                /*                 * **********************************
                  End Creating Audit Status on Save
                 * *********************************** */

                //CODE TO CHECK SIGNATURE OF SURGEON,ANESTHESIOLOGIST,NURSE IN ALL CHARTS AND SET VALUE(red,green,blank) IN STUB TABLE
                $chartSignedBySurgeon = $this->chkSurgeonSignNew($pConfirmId);
                $chartSignedByNurse = $this->chkNurseSignNew($pConfirmId);
                $updateStubTblQry = "UPDATE stub_tbl SET chartSignedBySurgeon='" . $chartSignedBySurgeon . "', chartSignedByNurse='" . $chartSignedByNurse . "' WHERE patient_confirmation_id='" . $pConfirmId . "' AND patient_confirmation_id!='0'";
                $updateStubTblRes = DB::select($updateStubTblQry); // or die(imw_error());
                //END CODE TO CHECK SIGNATURE OF SURGEON,ANESTHESIOLOGIST,NURSE IN ALL CHARTS AND SET VALUE(red,green,blank) IN STUB TABLE
                //REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)
                //REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)
                //START SEND LASER CHART TO IDOC OPERATIVE NOTE
                $iDocOpNoteSave = "";
                if (trim($ascIdConfirm) && $formStatus == "completed" && $imwSwitchFile == "sync_imwemr.php" && $imwPatientIdInjection && $pConfirmId) {
                    $iDocOpNoteSave = "yes";
                    //include("injection_misc_pdf.php");
                }
                //END SEND LASER CHART TO IDOC OPERATIVE NOTE
                $message = "Saved successfully!";
                $status = 1;
                $savedStatus = 1;
            }
        }
        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'injId' => $injId,
                    'requiredStatus' => [],
                    'savedStatus' => $savedStatus,
                    'data' => [],
                        ], 200, [ 'Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

}
