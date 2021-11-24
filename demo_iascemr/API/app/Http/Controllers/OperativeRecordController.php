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

class OperativeRecordController extends Controller {

    public function operative_report(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $loginUserType = $request->json()->get('UserType') ? $request->json()->get('UserType') : $request->input('UserType');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $seltemplate_id = $request->json()->get('template_id') ? $request->json()->get('template_id') : $request->input('template_id');
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
        $operativeTemplateId = 0;
        $oprativeReportId = 0;
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
                $message = " Operative Report";
                //START GET VOCABULARY OF ASC
                $ascInfoArr = $this->getASCInfo($facility_id);
                $qryStr = "SELECT fac_id, fac_name FROM facility_tbl WHERE fac_id = '" . $facility_id . "'";
                $qryRow = DB::selectone($qryStr);
                $loginUserFacilityName = $qryRow->fac_name;
                //END GET VOCABULARY OF ASC
                //GET PATIENT DETAIL
                $Operative_patientName_tblQry = "SELECT * FROM `patient_data_tbl` WHERE `patient_id` = '" . $patient_id . "'";
                $Operative_patientName_tblRow = DB::selectone($Operative_patientName_tblQry);
                $Operative_patientName = $Operative_patientName_tblRow->patient_fname . " " . $Operative_patientName_tblRow->patient_mname . " " . $Operative_patientName_tblRow->patient_lname;
                $imwPatientId = $Operative_patientName_tblRow->imwPatientId;


                $Operative_patientNameDobTemp = $Operative_patientName_tblRow->date_of_birth;
                $Operative_patientNameDob_split = explode("-", $Operative_patientNameDobTemp);
                $Operative_patientNameDob = $Operative_patientNameDob_split[1] . "-" . $Operative_patientNameDob_split[2] . "-" . $Operative_patientNameDob_split[0];


                $Operative_patientConfirm_tblQry = "SELECT * FROM `patientconfirmation` WHERE `patientConfirmationId` = '" . $pConfirmId . "'";
                $Operative_patientConfirm_tblRow = DB::selectone($Operative_patientConfirm_tblQry);


                $ascId = $Operative_patientConfirm_tblRow->ascId; //GET ASCID
                //GET ASSIGNED SURGEON ID AND SURGEON NAME
                $operativeRecordAssignedSurgeonId = $Operative_patientConfirm_tblRow->surgeonId;
                $operativeRecordAssignedSurgeonName = stripslashes($Operative_patientConfirm_tblRow->surgeon_name);
                //END GET ASSIGNED SURGEON ID AND SURGEON NAME

                $Operative_patientConfirmDosTemp = $Operative_patientConfirm_tblRow->dos;
                $Operative_patientConfirmDos_split = explode("-", $Operative_patientConfirmDosTemp);
                $Operative_patientConfirmDos = $Operative_patientConfirmDos_split[1] . "-" . $Operative_patientConfirmDos_split[2] . "-" . $Operative_patientConfirmDos_split[0];

                $Operative_patientConfirmSurgeon = $Operative_patientConfirm_tblRow->surgeon_name;
                $Operative_patientConfirmSiteTemp = $Operative_patientConfirm_tblRow->site;
                $surgeonId = $Operative_patientConfirm_tblRow->surgeonId;

                // APPLYING NUMBERS TO PATIENT SITE
                if ($Operative_patientConfirmSiteTemp == 1) {
                    $Operative_patientConfirmSite = "Left Eye";  //OD
                } else if ($Operative_patientConfirmSiteTemp == 2) {
                    $Operative_patientConfirmSite = "Right Eye";  //OS
                } else if ($Operative_patientConfirmSiteTemp == 3) {
                    $Operative_patientConfirmSite = "Both Eye";  //OU
                } else if ($Operative_patientConfirmSiteTemp == 4) {
                    $Operative_patientConfirmSite = "Left Upper Lid";
                } else if ($Operative_patientConfirmSiteTemp == 5) {
                    $Operative_patientConfirmSite = "Left Lower Lid";
                } else if ($Operative_patientConfirmSiteTemp == 6) {
                    $Operative_patientConfirmSite = "Right Upper Lid";
                } else if ($Operative_patientConfirmSiteTemp == 7) {
                    $Operative_patientConfirmSite = "Right Lower Lid";
                } else if ($Operative_patientConfirmSiteTemp == 8) {
                    $Operative_patientConfirmSite = "Bilateral Upper Lid";
                } else if ($Operative_patientConfirmSiteTemp == 9) {
                    $Operative_patientConfirmSite = "Bilateral Lower Lid";
                }
                // END APPLYING NUMBERS TO PATIENT SITE
                $patient_primary_procedure_id = $Operative_patientConfirm_tblRow->patient_primary_procedure_id;
                $patient_secondary_procedure_id = $Operative_patientConfirm_tblRow->patient_secondary_procedure_id;
                $patient_tertiary_procedure_id = $Operative_patientConfirm_tblRow->patient_tertiary_procedure_id;
                $Operative_patientConfirmPrimProc = $Operative_patientConfirm_tblRow->patient_primary_procedure;
                $Operative_patientConfirmSecProc = $Operative_patientConfirm_tblRow->patient_secondary_procedure;
                $Operative_patientConfirmTeriProc = $Operative_patientConfirm_tblRow->patient_tertiary_procedure;
                $primary_procedure_is_inj_misc = $Operative_patientConfirm_tblRow->prim_proc_is_misc;

                if ($Operative_patientConfirmSecProc == "N/A") {
                    $Operative_patientConfirmSecProc = "";
                }
                //END GET PATIENT DETAIL
                //GETTING SURGEON PROFILE FOR PRIMARY PROCEDURE
                $templateFound = '';
                if ($surgeonId <> "") {
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
                    $selectSurgeonProcedureQry = "select * from surgeonprofileprocedure where profileId in ($surgeonProfileIdImplode) order by procedureName";
                    $selectSurgeonProcedureRes = DB::select($selectSurgeonProcedureQry);

                    if ($selectSurgeonProcedureRes) {
                        foreach ($selectSurgeonProcedureRes as $selectSurgeonProcedureRow) {
                            $surgeonProfileProcedureId = $selectSurgeonProcedureRow->procedureId;
                            if ($patient_primary_procedure_id == $surgeonProfileProcedureId) {
                                $templateFound = "true";
                                $operativeTemplateId = $selectSurgeonProcedureRow->operativeTemplateId;
                            }
                        }
                    }
                }
                // Start Operative Template ID From Procedure Preference Card
                if ($templateFound <> "true") {
                    $proceduresArr = array($patient_primary_procedure_id, $patient_secondary_procedure_id, $patient_tertiary_procedure_id);
                    foreach ($proceduresArr as $procedureId) {
                        if ($procedureId) {
                            $procPrefCardQry = "Select * From procedureprofile Where procedureId = '" . $procedureId . "' ";
                            $procPrefCardRow = DB::selectone($procPrefCardQry);
                            if ($procPrefCardRow) {
                                $operativeTemplateId = $procPrefCardRow->operativeTemplateId;
                                break;
                            }
                        }
                    }
                }
                // End Operative Template ID From Procedure Preference Card
                // Check If Procedure is Injection Procedure
                $primProcDetails = $this->getRowRecord('procedures', 'procedureId', $patient_primary_procedure_id, '', '', 'catId');
                if ($primProcDetails->catId <> '2') {
                    if ($primary_procedure_is_inj_misc == '') {
                        //$chkprocedurecatDetails = $objManageData->getRowRecord('procedurescategory', 'proceduresCategoryId', $primProcDetails->catId);
                        $primary_procedure_is_inj_misc = $this->verifyProcIsInjMisc($patient_primary_procedure_id);
                        //($chkprocedurecatDetails->isMisc) ?	'true'	:	'';
                    }
                } else {
                    $primary_procedure_is_inj_misc = '';
                }
                // End Check If Procedure is Injection Procedure
                /*                 * ****************************************
                  Start Injection/Misc. Procedure Template
                 * **************************************** */
                if ($primProcDetails->catId <> '2' && $primary_procedure_is_inj_misc) {
                    $procedureDetails = array($patient_primary_procedure_id, $patient_secondary_procedure_id, $patient_tertiary_procedure_id);
                    if (is_array($procedureDetails) && count($procedureDetails) > 0) {
                        $injMiscOperativeTemplateId = '';
                        foreach ($procedureDetails as $procedureID) {
                            $fields = 'operativeReportID';
                            $defaultProfile = $this->injectionProfile($procedureID, $surgeonId, $fields);
                            if ($defaultProfile['profileFound']) {
                                $injMiscOperativeTemplateId = $defaultProfile['data']['operativeReportID'];
                                break;
                            }
                        }
                        $operativeTemplateId = ($injMiscOperativeTemplateId) ? $injMiscOperativeTemplateId : $operativeTemplateId;
                    }
                }
                /*                 * ****************************************
                  End Injection/Misc. Procedure Template
                 * **************************************** */
                //VIEW RECORD FROM DATABASE
                if ($seltemplate_id <> "") {
                    $ViewoperativeQry = "select oprativeReportId,template_id,signature,reportTemplate,patientId,form_status,signSurgeon1Id,signSurgeon1FirstName,signSurgeon1MiddleName,signSurgeon1LastName,signSurgeon1Status,signSurgeon1DateTime,date_format(signSurgeon1DateTime,'%m-%d-%Y %h:%i %p') as signSurgeon1DateTimeFormat from `operativereport` where template_id='" . $seltemplate_id . "'";
                    $ViewOperativeTemplateRow = DB::selectone($ViewoperativeQry);
                    if ($ViewOperativeTemplateRow) {
                        $operative_data = stripslashes($ViewOperativeTemplateRow->reportTemplate);
                        $template_id = $ViewOperativeTemplateRow->template_id;
                    }
                    if (!$ViewOperativeTemplateRow) {
                        $ViewOperativeTemplateQry = "select template_id,template_name,template_data from operative_template where template_id = '$seltemplate_id'";
                        $ViewOperativeTemplateRow = DB::selectone($ViewOperativeTemplateQry);
                        $operative_data = stripslashes($ViewOperativeTemplateRow->template_data);
                        $template_id = $seltemplate_id;
                        $template_name = trim($ViewOperativeTemplateRow->template_name);
                    }
                } else {
                    $ViewoperativeQry = "select oprativeReportId,template_id,signature,reportTemplate,patientId,form_status,signSurgeon1Id,signSurgeon1FirstName,signSurgeon1MiddleName,signSurgeon1LastName,signSurgeon1Status,signSurgeon1DateTime,date_format(signSurgeon1DateTime,'%m-%d-%Y %h:%i %p') as signSurgeon1DateTimeFormat from `operativereport` where confirmation_id='" . $pConfirmId . "'";
                    $ViewoperativeRow = DB::selectone($ViewoperativeQry); // or die(imw_error());
                    if ($ViewoperativeRow) {
                        $operative_surgeon_sign = $ViewoperativeRow->signature;
                        $operative_data = stripslashes($ViewoperativeRow->reportTemplate);
                        $operative_data_check = $operative_data;
                        $form_status = $ViewoperativeRow->form_status;

                        $signSurgeon1Id = $ViewoperativeRow->signSurgeon1Id;
                        $signSurgeon1DateTime = $ViewoperativeRow->signSurgeon1DateTime;
                        $signSurgeon1DateTimeFormat = $ViewoperativeRow->signSurgeon1DateTimeFormat;
                        $signSurgeon1FirstName = $ViewoperativeRow->signSurgeon1FirstName;
                        $signSurgeon1MiddleName = $ViewoperativeRow->signSurgeon1MiddleName;
                        $signSurgeon1LastName = $ViewoperativeRow->signSurgeon1LastName;
                        $signSurgeon1Status = $ViewoperativeRow->signSurgeon1Status;
                        $template_id = $ViewoperativeRow->template_id;
                        $oprativeReportId = $ViewoperativeRow->oprativeReportId;
                        $template_id = !($template_id) ? $operativeTemplateId : $template_id;
                        if ($signSurgeon1Id == '0') {
                            $form_status = "not completed";
                        }
                    }
                    $ViewOperativeTemplateQry = "select template_id,template_name,template_data from operative_template where template_id = '$template_id'";
                    $ViewOperativeTemplateRow = DB::selectone($ViewOperativeTemplateQry);
                    if ($ViewOperativeTemplateRow) {
                        $template_name = $ViewOperativeTemplateRow->template_name;
                        //FIRST TIME FETCH DATA FROM 'operative_template' TABLE	
                        if (trim($operative_data) == "") {
                            if ($operativeTemplateId) {
                                $operative_data = stripslashes($ViewOperativeTemplateRow->template_data);
                            }
                        }
                    }
                }

                //FIRST TIME FETCH DATA FROM 'operative_template' TABLE		
                //FETCH DATA FROM OPERATINGROOMRECORD TABLE
                $preopdiagnosis = $postopdiagnosis = "";
                $diagnosisQry = DB::selectone("select preOpDiagnosis,postOpDiagnosis from operatingroomrecords where patient_id='$patient_id' and confirmation_id='$pConfirmId'");
                if ($diagnosisQry) {
                    $preopdiagnosis = $diagnosisQry->preOpDiagnosis;
                    $postopdiagnosis = $diagnosisQry->postOpDiagnosis;
                }
                if (trim($postopdiagnosis) == "") {
                    $postopdiagnosis = $preopdiagnosis;
                }
                // END FETCH DATA FROM OPEARINGROOMRECORD TABLE
                //REPLACE FIELD IN PARENTHESIS WITH ACTUAL VALUE 			
                $operative_data = str_ireplace("{PATIENT ID}", "<b>" . $Operative_patientName_tblRow->patient_id . "</b>", $operative_data);
                $operative_data = str_ireplace("{PATIENT FIRST NAME}", "<b>" . $Operative_patientName_tblRow->patient_fname . "</b>", $operative_data);
                $operative_data = str_ireplace("{MIDDLE INITIAL}", "<b>" . $Operative_patientName_tblRow->patient_mname . "</b>", $operative_data);
                $operative_data = str_ireplace("{LAST NAME}", "<b>" . $Operative_patientName_tblRow->patient_lname . "</b>", $operative_data);
                $operative_data = str_ireplace("{DOB}", "<b>" . $Operative_patientNameDob . "</b>", $operative_data);
                $operative_data = str_ireplace("{DOS}", "<b>" . $Operative_patientConfirmDos . "</b>", $operative_data);
                $operative_data = str_ireplace("{SURGEON NAME}", "<b>" . $Operative_patientConfirm_tblRow->surgeon_name . "</b>", $operative_data);
                $operative_data = str_ireplace("{SITE}", "<b>" . $Operative_patientConfirmSite . "</b>", $operative_data);
                $operative_data = str_ireplace("{PROCEDURE}", "<b>" . $Operative_patientConfirmPrimProc . "</b>", $operative_data);
                $operative_data = str_ireplace("{SECONDARY PROCEDURE}", "<b>" . $Operative_patientConfirmSecProc . "</b>", $operative_data);
                $operative_data = str_ireplace("{TERTIARY PROCEDURE}", "<b>" . $Operative_patientConfirmTeriProc . "</b>", $operative_data);

                $operative_data = str_ireplace("{PRE-OP DIAGNOSIS}", "<b>" . $preopdiagnosis . "</b>", $operative_data);
                $operative_data = str_ireplace("{POST-OP DIAGNOSIS}", "<b>" . $postopdiagnosis . "</b>", $operative_data);
                $operative_data = str_ireplace("{DATE}", "<b>" . date('m-d-Y') . "</b>", $operative_data);
                $operative_data = str_ireplace("{TIME}", "<b>" . $this->getTmFormat(date('H:i:s')) . "</b>", $operative_data);
                $operative_data = str_ireplace("{ASC NAME}", $loginUserFacilityName, $operative_data);
                $operative_data = str_ireplace("{ASC ADDRESS}", $ascInfoArr[0], $operative_data);
                $operative_data = str_ireplace("{ASC PHONE}", $ascInfoArr[1], $operative_data);

                //file_put_contents('test.txt',$operative_data);
                //END REPLACE FIELD IN PARENTHESIS WITH ACTUAL VALUE 	
                //END VIEW RECORD FROM DATABASE
                //SELECT SIGNATURE FROM OPERATINGRECORD TABLE
                // $signQry = DB::selectone("select signature from operativereport where patientId='" . $patient_id . "' AND confirmation_id='" . $pConfirmId . "'");
                $signatureVar = "";
                if (isset($ViewoperativeRow->patientId) && $ViewoperativeRow->patientId == $patient_id) {
                    $signatureVar = $ViewoperativeRow->signature;
                }
                //END SELECT SIGNATURE FROM OPERATIVEREPORT TABLE
                //UPLOAD SCANNED IMAGE FROM OPERATINGROOMRECORD TABLE

                $ViewOpRoomRecordQry = "select operatingRoomRecordsId,iol_ScanUpload,iol_ScanUpload2,post2OperativeReport from `operatingroomrecords` where  confirmation_id = '" . $pConfirmId . "'";
                $ViewOpRoomRecordRow = DB::selectone($ViewOpRoomRecordQry);
                $OpRoomRecords=[];
                if ($ViewOpRoomRecordRow) {
                    $operatingRoomRecordsId = $ViewOpRoomRecordRow->operatingRoomRecordsId;
                    $iol_ScanUpload = $ViewOpRoomRecordRow->iol_ScanUpload;
                    $iol_ScanUpload2 = $ViewOpRoomRecordRow->iol_ScanUpload2;
                    $post2OperativeReport = $ViewOpRoomRecordRow->post2OperativeReport;
                    if($iol_ScanUpload!="" || $iol_ScanUpload2!=""){
                        $OpRoomRecords=[['key'=>'iol_ScanUpload','value'=>$iol_ScanUpload?base64_encode($iol_ScanUpload):""],['key'=>'iol_ScanUpload2','value'=>$iol_ScanUpload2?base64_encode($iol_ScanUpload2):""]];
                    }
                }
                //UPLOAD SCANNED IMAGE FROM OPERATINGROOMRECORD TABLE
                $templateListsDetail = DB::select("select template_id,template_name from operative_template where surgeonId='" . $surgeonId . "' order by template_name ASC");
                $communityTemplateLists = DB::select("select template_id,template_name from operative_template where surgeonId='0' order by template_name ASC");

                $data = ['surgeontemplate' => $templateListsDetail,'surgeon_name'=>isset($Operative_patientConfirm_tblRow->surgeon_name)?$Operative_patientConfirm_tblRow->surgeon_name:'','signSurgeon1DateTimeFormat'=>(isset($signSurgeon1DateTimeFormat)?$signSurgeon1DateTimeFormat:Date("m-d-Y H:i:s")),'signSurgeon1Status'=>(isset($signSurgeon1Status)?$signSurgeon1Status:"No"), 'communityTemplateLists' => $communityTemplateLists, 'operative_data' => $operative_data, 'IOL' => $OpRoomRecords];
            }
        }
        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => '',
                    'data' => $data,
                    'template_id' => isset($template_id) ? (int) $template_id : 0,
                    'template_name' => isset($template_name) ?(string) trim($template_name) : "",
                    'oprativeReportId' => (int) $oprativeReportId,
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

    public function operative_reportsave(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $loginUserType = $request->json()->get('UserType') ? $request->json()->get('UserType') : $request->input('UserType');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $seltemplate_id = $request->json()->get('template_id') ? $request->json()->get('template_id') : $request->input('template_id');
        $oprativeReportId = $request->json()->get('oprativeReportId') ? $request->json()->get('oprativeReportId') : $request->input('oprativeReportId');
        $textHTML = $request->json()->get('textHTML') ? $request->json()->get('textHTML') : $request->input('textHTML');
        $imwPatientId = $request->json()->get('imwPatientId') ? $request->json()->get('imwPatientId') : $request->input('imwPatientId');
        $ascId = $request->json()->get('ascId') ? $request->json()->get('ascId') : $request->input('ascId');
        $electronic_signs = $request->json()->get('electronic_signs') ? $request->json()->get('electronic_signs') : $request->input('electronic_signs');
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
        $oprativeReportId = 0;
        $operativeTemplateId = 0;
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
                $users = DB::selectone("select usersId,userTitle,fname,mname,lname,initial,address,address2,phone,concat(fname,' ',lname) as fullname,locked,user_privileges,admin_privileges"
                                . " hippaReviewedStatus,admin_privileges,hippaReviewedStatus,user_type,session_timeout from scemr.users where usersId='" . $userId . "'");

                $fieldName = "surgical_operative_record_form";
                $tablename = "operativereport";
                //START CODE TO CHECK SURGEON SIGN IN DATABASE
                $chkSurgeonSignDetails = $this->getRowRecord('operativereport', 'confirmation_id', $pConfirmId);
                if ($chkSurgeonSignDetails) {
                    $chk_signSurgeon1Id = $chkSurgeonSignDetails->signSurgeon1Id;
                }
                //END CODE TO CHECK SURGEON SIGN IN DATABASE 
                $temp_data = $textHTML;
                $temp_data = addslashes($temp_data);
                //SET FORM STATUS ACCORDING TO MANDATORY FIELD
                $form_status = "completed";
                if ($chk_signSurgeon1Id == "0" || !$temp_data) {
                    $form_status = "not completed";
                }
                //END SET FORM STATUS ACCORDING TO MANDATORY FIELD
                $electronic_signs = $electronic_signs;

                $arrayRecord = [];
                $str = "";
                if (strtolower($loginUserType) == 'surgeon') {
                    $arrayRecord['signSurgeon1Id'] = $userId;
                    $arrayRecord['signSurgeon1FirstName'] = $users->fname;
                    $arrayRecord['signSurgeon1MiddleName'] = $users->mname;
                    $arrayRecord['signSurgeon1LastName'] = $users->lname;
                    $arrayRecord['signSurgeon1Status'] = 'yes'; //$electronic_sign['surgeon']['status'];
                    $arrayRecord['signSurgeon1DateTime'] = (isset($electronic_signss) && $electronic_signss != '') ? date("Y-m-d H:i:s", strtotime($electronic_signss)) : Date("Y-m-d H:i:s"); //$electronic_sign['surgeon']['dttime'];
                    $arrayRecord['signSurgeon1Activate'] = "yes";
                    $str = ",";
                }


                if ($chkSurgeonSignDetails) {
                    //CODE START TO CHECK FORM STATUS (IF EMPTY THEN REFRESH SLIDER ON SAVE)
                    $chk_form_status = $chkSurgeonSignDetails->form_status;
                    //CODE START TO CHECK FORM STATUS (IF EMPTY THEN REFRESH SLIDER ON SAVE)
                    $SaveoperativeQry = "update `operativereport` set 
                                            reportTemplate = '" . addslashes($temp_data) . "',
                                            userId='" . $userId . "',
                                            form_status ='" . $form_status . "',
                                            template_id = '" . $seltemplate_id . "',
                                            opreport_inte_sync_status='0',
                                            patientId='" . $patient_id . "'
                                                $str
                                           " . @implode(",", $arrayRecord) . "
                                            WHERE 
                                            confirmation_id='" . $pConfirmId . "'";
                } else {
                    $SaveoperativeQry = "insert into `operativereport` set 
                                            reportTemplate = '" . addslashes($temp_data) . "',
                                            form_status ='" . $form_status . "',
                                            template_id = '" . $templateList . "',
                                            patientId='" . $patient_id . "',									
                                            userId='" . $userId . "',
                                            opreport_inte_sync_status='0',
                                            patientId='" . $patient_id . "'
                                                $str
                                           " . @implode(",", $arrayRecord) . "
                                            confirmation_id='" . $pConfirmId . "'";
                }
                $SaveoperativeRes = DB::select($SaveoperativeQry);

                //SAVE ENTRY IN chartnotes_change_audit_tbl 
                $chkAuditChartNotesQry = "select * from `chartnotes_change_audit_tbl` where 
                                        user_id='" . $userId . "' AND
                                        patient_id='" . $patient_id . "' AND
                                        confirmation_id='" . $pConfirmId . "' AND
                                        form_name='" . $fieldName . "' AND
                                        status = 'created'";
                $chkAuditChartNotesRes = DB::select($chkAuditChartNotesQry);
                if ($chkAuditChartNotesRes) {
                    $SaveAuditChartNotesQry = "insert into `chartnotes_change_audit_tbl` set 
                                                    user_id='" . $userId . "',
                                                    patient_id='" . $patient_id . "',
                                                    confirmation_id='" . $pConfirmId . "',
                                                    form_name='$fieldName',
                                                    status='modified',
                                                    action_date_time='" . date("Y-m-d H:i:s") . "'";
                } else {
                    $SaveAuditChartNotesQry = "insert into `chartnotes_change_audit_tbl` set 
                                                    user_id='" . $userId . "',
                                                    patient_id='" . $patient_id . "',
                                                    confirmation_id='" . $pConfirmId . "',
                                                    form_name='$fieldName',
                                                    status='created',
                                                    action_date_time='" . date("Y-m-d H:i:s") . "'";
                }
                $SaveAuditChartNotesRes = DB::select($SaveAuditChartNotesQry);
                //END SAVE ENTRY IN chartnotes_change_audit_tbl
                //CODE TO CHECK SURGEON ALL SIGNATURE AND SET VALUE IN STUB TABLE
                $chartSignedBySurgeon = $this->chkSurgeonSignNew($pConfirmId);
                $updateStubTblQry = "UPDATE stub_tbl SET chartSignedBySurgeon='" . $chartSignedBySurgeon . "' WHERE patient_confirmation_id='" . $pConfirmId . "'";
                $updateStubTblRes = DB::select($updateStubTblQry);
                //END CODE TO CHECK SURGEON SIGNATURE AND SET VALUE IN STUB TABLE
                //REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)
                //START SENDING OPNOTE TO iDOC
                if (trim($ascId) <> "" && trim($ascId) <> 0 && $form_status == "completed" && $imwPatientId && $pConfirmId) {
                    $reportTemplate = "";
                    $opnote_qry_sc = "SELECT opr.oprativeReportId,opr.reportTemplate,opt.template_name,stb.appt_id  FROM `operativereport` opr 
					LEFT JOIN operative_template opt ON (opt.template_id = opr.template_id)
					LEFT JOIN stub_tbl stb ON (stb.patient_confirmation_id = opr.confirmation_id)
					WHERE opr.confirmation_id = '" . $pConfirmId . "' AND opr.form_status = 'completed'";
                    $opnote_row_sc = DB::selectone($opnote_qry_sc);
                    if ($opnote_row_sc) {
                        $oprativeReportId = $opnote_row_sc->oprativeReportId;
                        $reportTemplate = stripslashes($opnote_row_sc->reportTemplate);
                        $template_name = stripslashes($opnote_row_sc->template_name);
                        $sc_emr_iasc_appt_id = $opnote_row_sc->appt_id;
                    }
                    // imwemr connection
                    if (trim($reportTemplate) && $oprativeReportId) {
                        $chkOpnoteQry = "SELECT pn_rep_id FROM pn_reports WHERE patient_id = '" . $imwPatientId . "' AND sc_emr_operative_report_id = '" . $oprativeReportId . "'";
                        $chkOpnoteRes = DB::connection('DB_REGISTER_CONNECTION')->select($chkOpnoteQry);
                        $insUpdtOpQry = " INSERT INTO ";
                        $insUpdtOpWhereQry = " ";
                        if (@imw_num_rows($chkOpnoteRes) > 0) {
                            $insUpdtOpQry = " UPDATE ";
                            $insUpdtOpWhereQry = " WHERE patient_id = '" . $imwPatientId . "' AND sc_emr_operative_report_id = '" . $oprativeReportId . "' ";
                        }
                        $setOpnoteQry = $insUpdtOpQry . " pn_reports SET  
							patient_id= '" . $imwPatientId . "',
							txt_data= '" . addslashes($reportTemplate) . "',
							pn_rep_date= '" . date("Y-m-d H:i:s") . "',
							sc_emr_template_name= '" . addslashes($template_name) . "',
							sc_emr_operative_report_id= '" . $oprativeReportId . "',
							sc_emr_iasc_appt_id= '" . $sc_emr_iasc_appt_id . "',
							status 						= '0'
							" . $insUpdtOpWhereQry;
                        $setOpnoteRes = DB::connection('DB_REGISTER_CONNECTION')->select($setOpnoteQry);
                    }
                }
                if (trim($ascId) <> "" && trim($ascId) <> 0 && $form_status == "completed" && getenv("INTE_SYNC") == "YES" && $pConfirmId) {
                    $syncExternalPdf = "yes";
                    // include_once("operative_recordPdf.php");
                }
                $status = 1;
                $addstatus = 1;
                $message = 'Data has been saved Successfully !';
                //END SENDING OPNOTE TO BILLING SOFTWARE iDOC
            }
        }
        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => '',
                    'data2' => '',
                    'addstatus' => $addstatus,
                    'template_id' => isset($template_id) ? (int) $template_id : 0,
                    'oprativeReportId' => (int) $oprativeReportId,
                    'data' => [],
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

    public function html_code(Request $request) {
        $data = $request->json()->get('html') ? $request->json()->get('html') : $request->input('html');
        return response()->json([
                    'data' => html_entity_decode($data, ENT_QUOTES | ENT_XHTML | ENT_HTML5, 'ISO-8859-1'),
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

}
