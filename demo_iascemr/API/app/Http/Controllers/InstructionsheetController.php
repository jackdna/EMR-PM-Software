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

class InstructionsheetController extends Controller {

    public function instruction_template(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $loginUserType = $request->json()->get('UserType') ? $request->json()->get('UserType') : $request->input('UserType');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $patient_instruction_id = $request->json()->get('patient_instruction_id') ? $request->json()->get('patient_instruction_id') : $request->input('patient_instruction_id');
        $hidd_signSurgeon1Activate = $request->json()->get('hidd_signSurgeon1Activate') ? $request->json()->get('hidd_signSurgeon1Activate') : $request->input('hidd_signSurgeon1Activate');
        $hidd_signNurseActivate = $request->json()->get('hidd_signNurseActivate') ? $request->json()->get('hidd_signNurseActivate') : $request->input('hidd_signNurseActivate');
        $hidd_signWitness1Activate = $request->json()->get('hidd_signWitness1Activate') ? $request->json()->get('hidd_signWitness1Activate') : $request->input('hidd_signWitness1Activate');
        $instructionSheetId = $instruction_id = $request->json()->get('instruction_id') ? $request->json()->get('instruction_id') : $request->input('instruction_id');
        $data = [];
        $status = 0;
        $moveStatus = 0;
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
                $status = 1;
                $message = " Instruction template list ";
                $dropdownList = DB::select("select instruction_id,instruction_name from instruction_template order by instruction_name asc");

                /*                 * **************************************************************************** */

                $status = 1;
                $browserPlatform = 'iPad';
                $instructionData = '';
                //GET USER DETAIL(FOR USER SIGNATURE)
                $ViewUserNameQry = "select fname,mname,lname,signature,user_type,user_sub_type from `users` where  usersId = '" . $userId . "'";
                $ViewUserNameRes = DB::selectone($ViewUserNameQry);

                $loggedInUserName = $ViewUserNameRes->lname . ", " . $ViewUserNameRes->fname . " " . $ViewUserNameRes->mname;
                $loggedInUserType = $ViewUserNameRes->user_type;
                $loggedInSignatureOfUser = $ViewUserNameRes->signature;
                $logInUserSubType = $ViewUserNameRes->user_sub_type;
                //END GET USER DETAIL(FOR USER SIGNATURE)
                //START GET VOCABULARY OF ASC
                $ascInfoArr = $this->getASCInfo($facility_id);

                $qryStr = "SELECT fac_id, fac_name FROM facility_tbl WHERE fac_id = '" . $facility_id . "'";
                $qryRow = DB::selectone($qryStr);
                $loginUserFacilityName = $qryRow->fac_name;
                //END GET VOCABULARY OF ASC
                //GETTING PATIENT CONFIRMATION DETAILS
                $confirmationDetails = $this->getExtractRecord('patientconfirmation', 'patientConfirmationId', $pConfirmId);

                if ($confirmationDetails) {
                    $primary_procedure_is_inj_misc = $confirmationDetails[0]->prim_proc_is_misc;
                    $patient_primary_procedure_id = $confirmationDetails[0]->patient_primary_procedure_id;
                    $patient_primary_procedure = $confirmationDetails[0]->patient_primary_procedure;
                    $patient_secondary_procedure_id = $confirmationDetails[0]->patient_secondary_procedure_id;
                    $patient_secondary_procedure = $confirmationDetails[0]->patient_secondary_procedure;
                    $patient_tertiary_procedure_id = $confirmationDetails[0]->patient_tertiary_procedure_id;
                    $patient_tertiary_procedure = $confirmationDetails[0]->patient_tertiary_procedure;
                }
                //GETTING SURGEON PROFILE FOR PRIMARY PROCEDURE
                // $instructionSheetId = $_REQUEST['show_td'];
                if ($userId <> "" && !$instructionSheetId) {
                    $selectSurgeonQry = "select * from surgeonprofile where surgeonId = '$userId' and del_status=''";
                    $selectSurgeonRes = DB::select($selectSurgeonQry);
                    foreach ($selectSurgeonRes as $selectSurgeonRow) {
                        $surgeonProfileIdArr[] = $selectSurgeonRow->surgeonProfileId;
                    }
                    if (!empty($surgeonProfileIdArr)) {
                        $surgeonProfileIdImplode = implode(',', $surgeonProfileIdArr);
                    } else {
                        $surgeonProfileIdImplode = 0;
                    }
                    $selectSurgeonProcedureQry = "select * from surgeonprofileprocedure where profileId in ($surgeonProfileIdImplode) order by procedureName";
                    $selectSurgeonProcedureRes = DB::select($selectSurgeonProcedureQry); // or die(imw_error());
                    if ($selectSurgeonProcedureRes) {
                        foreach ($selectSurgeonProcedureRes as $selectSurgeonProcedureRow) {
                            $surgeonProfileProcedureId = $selectSurgeonProcedureRow->procedureId;
                            if (isset($patient_primary_procedure_id) && ($patient_primary_procedure_id == $surgeonProfileProcedureId)) {
                                $instructionSheetFound = "true";
                                $instructionSheetId = $selectSurgeonProcedureRow->instructionSheetId;
                            }
                        }
                    }
                }

                // Start Instruction Sheet ID From Procedure Preference Card		
                if (!$instructionSheetId && isset($patient_primary_procedure_id)) {
                    $proceduresArr = array($patient_primary_procedure_id, $patient_secondary_procedure_id, $patient_tertiary_procedure_id);
                    foreach ($proceduresArr as $procedureId) {
                        if ($procedureId) {
                            $procPrefCardQry = "Select * From procedureprofile Where procedureId = '" . $procedureId . "' ";
                            $procPrefCardSql = DB::selectone($procPrefCardQry);
                            if ($procPrefCardSql) {
                                $instructionSheetId = $procPrefCardSql->instructionSheetId;

                                break;
                            }
                        }
                    }
                }
                // End  Instruction Sheet ID From Procedure Preference Card		
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
                        $injMiscInstructionSheetId = '';
                        foreach ($procedureDetails as $procedureID) {
                            $fields = 'instructionSheetID';
                            $defaultProfile = $this->injectionProfile($procedureID, $userId, $fields);
                            if (isset($defaultProfile[0]->profileFound)) {
                                $injMiscInstructionSheetId = $defaultProfile['data']['instructionSheetID'];
                                break;
                            }
                        }
                        $instructionSheetId = ($injMiscInstructionSheetId) ? $injMiscInstructionSheetId : $instructionSheetId;
                    }
                }
                /*                 * ****************************************
                  End Injection/Misc. Procedure Template
                 * **************************************** */

                /*                 * ****************************************
                  Start Laser Procedure Template
                 * **************************************** */

                $primProcDetails = $this->getRowRecord('procedures', 'procedureId', $patient_primary_procedure_id, '', '', 'catId');
                if ($primProcDetails->catId == '2') {
                    unset($condArr);
                    $condArr['1'] = '1';
                    $xtraCondition = " And catId = '2'  And procedureId IN (" . $patient_primary_procedure_id . "";
                    $xtraCondition .= isset($patient_secondary_procedure_id) ? ',' . $patient_secondary_procedure_id : '';
                    $xtraCondition .= isset($patient_tertiary_procedure_id) ? ',' . $patient_tertiary_procedure_id : '';
                    $xtraCondition .= ") ";

                    $orderBy = " FIELD(procedureId, " . $patient_primary_procedure_id . "";
                    $orderBy .= ($patient_secondary_procedure_id) ? ',' . $patient_secondary_procedure_id : '';
                    $orderBy .= ($patient_tertiary_procedure_id) ? ',' . $patient_tertiary_procedure_id : '';
                    $orderBy .= ")";

                    $procedureDetails = $this->getMultiChkArrayRecords('procedures', $condArr, $orderBy, '', $xtraCondition);

                    if (is_array($procedureDetails) && count($procedureDetails) > 0) {
                        $laserInstructionSheetId = '';
                        foreach ($procedureDetails as $key => $procData) {
                            $laserProcTempQry = "SELECT * FROM laser_procedure_template WHERE laser_procedureID = '" . $procData->procedureId . "' And (FIND_IN_SET(" . $userId . ",laser_surgeonID))   ";
                            $laserProcTempSql = DB::select($laserProcTempQry);
                            if ($laserProcTempSql) {
                                $laserProcTempQry = "SELECT * FROM laser_procedure_template WHERE laser_procedureID = '" . $procData->procedureId . "' And laser_surgeonID = 'all'  Order by laser_templateID Desc Limit 1";
                                $laserProcTempSql = DB::select($laserProcTempQry);
                            }

                            if ($laserProcTempSql) {
                                foreach ($laserProcTempSql as $laserProcTempRow) {
                                    $procSurgeonId = $userId;
                                    $laserSurgeon = $laserProcTempRow->laser_surgeonID;

                                    if ($laserSurgeon != "all") {
                                        $laserSurgeonExplode = explode(",", $laserSurgeon);
                                        $laserSurgeonCount = count($laserSurgeonExplode);

                                        if ($laserSurgeonCount == 1) {
                                            if ($procSurgeonId == $laserSurgeon) {
                                                $laserInstructionSheetId = $laserProcTempRow->instructionSheetId;
                                                break;
                                            }
                                        }
                                        $matchedSurgeon = false;
                                        if ($laserSurgeonCount > 1) {
                                            for ($i = 0; $i < $laserSurgeonCount; $i++) {
                                                $match_surgeonid = $procSurgeonId;
                                                $surgeon = $laserSurgeonExplode[$i];
                                                if ($surgeon == $match_surgeonid) {
                                                    $matchedSurgeon = true;
                                                    $laserInstructionSheetId = $laserProcTempRow->instructionSheetId;
                                                }
                                            }
                                        }
                                        if ($matchedSurgeon == true) {
                                            break;
                                        }
                                    } else {
                                        $laserInstructionSheetId = $laserProcTempRow->instructionSheetId;
                                    }
                                }
                            }

                            if ($laserInstructionSheetId)
                                break;
                        }

                        $instructionSheetId = ($laserInstructionSheetId) ? $laserInstructionSheetId : $instructionSheetId;
                    }
                }


                /*                 * ****************************************
                  End Laser Procedure Template
                 * **************************************** */

                //CHECK FORM STATUS AND SIGN-ACTIVATE
                $chkFormStatusDetails = $this->getRowRecord('patient_instruction_sheet', 'patient_confirmation_id', $pConfirmId);
                if ($chkFormStatusDetails) {
                    $chk_form_status = $chkFormStatusDetails->form_status;
                    $chk_signSurgeon1Id = $chkFormStatusDetails->signSurgeon1Id;
                    $chk_signNurseId = $chkFormStatusDetails->signNurseId;
                    $chk_signWitness1Id = $chkFormStatusDetails->signWitness1Id;
                    $patient_instruction_id = $chkFormStatusDetails->patient_instruction_id;
                    $instructionSheetId = $chkFormStatusDetails->template_id;
                }

                // GETTING INSTRUCTION SHEET DETAILS	
                //GETTING IF ALREADY EXISIS
                if ($instruction_id == 0) {
                    $instDetails = $this->getRowRecord('patient_instruction_sheet', 'patient_confirmation_id', $pConfirmId, "", "", " *, date_format(signSurgeon1DateTime,'%m-%d-%Y %h:%i %p') as signSurgeon1DateTimeFormat, date_format(signNurseDateTime,'%m-%d-%Y %h:%i %p') as signNurseDateTimeFormat, date_format(signWitness1DateTime,'%m-%d-%Y %h:%i %p') as signWitness1DateTimeFormat");
                    if ($instDetails) {
                        $patient_instruction_id = $instDetails->patient_instruction_id;
                        if ($instructionData == '') {
                            $instructionData = stripslashes($instDetails->instruction_sheet_data);
                        }
                        $signSurgeon1Activate = $instDetails->signSurgeon1Activate;
                        $signSurgeon1Id = $instDetails->signSurgeon1Id;
                        $signSurgeon1DateTime = $instDetails->signSurgeon1DateTime;
                        $signSurgeon1DateTimeFormat = isset($instDetails->signSurgeon1DateTimeFormat) ? $instDetails->signSurgeon1DateTimeFormat : '0000-00-00 00:00:00';
                        $signSurgeon1FirstName = $instDetails->signSurgeon1FirstName;
                        $signSurgeon1MiddleName = $instDetails->signSurgeon1MiddleName;
                        $signSurgeon1LastName = $instDetails->signSurgeon1LastName;
                        $signSurgeon1Status = $instDetails->signSurgeon1Status;

                        $signNurseActivate = $instDetails->signNurseActivate;
                        $signNurseDateTime = $instDetails->signNurseDateTime;
                        $signNurseDateTimeFormat = isset($instDetails->signNurseDateTimeFormat) ? $instDetails->signNurseDateTimeFormat : '0000-00-00 00:00:00';
                        $signNurseId = $instDetails->signNurseId;
                        $signNurseFirstName = $instDetails->signNurseFirstName;
                        $signNurseMiddleName = $instDetails->signNurseMiddleName;
                        $signNurseLastName = $instDetails->signNurseLastName;
                        $signNurseStatus = $instDetails->signNurseStatus;

                        $signWitness1Activate = $instDetails->signWitness1Activate;
                        $signWitness1DateTime = $instDetails->signWitness1DateTime;
                        $signWitness1DateTimeFormat = isset($instDetails->signWitness1DateTimeFormat) ? $instDetails->signWitness1DateTimeFormat : '0000-00-00 00:00:00';
                        $signWitness1Id = $instDetails->signWitness1Id;
                        $signWitness1FirstName = $instDetails->signWitness1FirstName;
                        $signWitness1MiddleName = $instDetails->signWitness1MiddleName;
                        $signWitness1LastName = $instDetails->signWitness1LastName;
                        $signWitness1Status = $instDetails->signWitness1Status;
                        $template_id = $instDetails->template_id;
                        $instructionSheetId = $template_id;
                    }

                    //GETTING IF ALREADY EXISIS	
                    //GETTING IF instructionData DOES NOT ALREADY EXISIS	
                    if (!$instructionData) {
                        //echo $instructionSheetId;
                        $instructionDetails = $this->getRowRecord('instruction_template', 'instruction_id', $instructionSheetId);
                        $instructionData = isset($instructionDetails->instruction_desc) ? stripslashes($instructionDetails->instruction_desc) : "";
                    }
                } else {
                    $instructionSheetId = $instruction_id;
                    $instructionDetails = $this->getRowRecord('instruction_template', 'instruction_id', $instructionSheetId);
                    $instructionData = isset($instructionDetails->instruction_desc) ? stripslashes($instructionDetails->instruction_desc) : "";
                }
                if (!$instructionData) {
                    $instructionDataStatus = 'false';
                }
                //GETTING IF instructionData DOES NOT ALREADY EXISIS
                // GETTING INSTRUCTION SHEET DETAILS
                //GETTING PATIENT CONFIRMATION DETAILS
                //GETTING PATIENT DETAILS

                $patientDetails = $this->getExtractRecord('patient_data_tbl', 'patient_id', $patient_id);
                $instruction_patientNameDobTemp = isset($patientDetails[0]->date_of_birth) ? $patientDetails[0]->date_of_birth : '0000:00:00';
                $instruction_patientNameDob_split = explode("-", $instruction_patientNameDobTemp);
                $instruction_patientNameDob = $instruction_patientNameDob_split[1] . "-" . $instruction_patientNameDob_split[2] . "-" . $instruction_patientNameDob_split[0];

                //SET DOS FROM patientConfirmation TABLE
                $instruction_patientConfirmDosTemp = $confirmationDetails[0]->dos;
                $instruction_patientConfirmDos_split = explode("-", $instruction_patientConfirmDosTemp);
                $instruction_patientConfirmDos = $instruction_patientConfirmDos_split[1] . "-" . $instruction_patientConfirmDos_split[2] . "-" . $instruction_patientConfirmDos_split[0];
                //END SET DOS FROM patientConfirmation TABLE
                //FETCH DATA FROM OPEARINGROOMRECORD TABLE
                $preopdiagnosis = '';
                $postopdiagnosis = '';
                $diagnosisQry = DB::selectone("select preOpDiagnosis,postOpDiagnosis from operatingroomrecords where patient_id='" . $patient_id . "' and confirmation_id='" . $pConfirmId . "'");
                if ($diagnosisQry) {
                    $preopdiagnosis = $diagnosisQry->preOpDiagnosis;
                    $postopdiagnosis = $diagnosisQry->postOpDiagnosis;
                    if (trim($postopdiagnosis) == "") {
                        $postopdiagnosis = $preopdiagnosis;
                    }
                }
                // END FETCH DATA FROM OPEARINGROOMRECORD TABLE
                $instructionData = str_ireplace("&#39;", "'", $instructionData);
                $instructionData = str_ireplace('{PATIENT ID}', '<b>' . ucfirst($patient_id) . '</b>', $instructionData);
                $instructionData = str_ireplace('{PATIENT FIRST NAME}', '<b>' . ucfirst($patientDetails[0]->patient_fname) . '</b>', $instructionData);
                $instructionData = str_ireplace('{MIDDLE INITIAL}', '<b>' . ucfirst(substr($patientDetails[0]->patient_mname, 0, 1)) . '</b>', $instructionData);
                $instructionData = str_ireplace('{LAST NAME}', '<b>' . ucfirst($patientDetails[0]->patient_lname) . '</b>', $instructionData);
                $instructionData = str_ireplace('{DOB}', '<b>' . $instruction_patientNameDob . '</b>', $instructionData);
                $instructionData = str_ireplace('{DOS}', '<b>' . $instruction_patientConfirmDos . '</b>', $instructionData);
                $instructionData = str_ireplace('{SURGEON NAME}', '<b>' . ucfirst($confirmationDetails[0]->surgeon_name) . '</b>', $instructionData);
                $site = $confirmationDetails[0]->site;
                //GETTING SITE, PRIMARY, SECONDARY PROCEDURE DETAILS AND CURRENT DATE
                if ($site == '1') {
                    $site = 'left';
                }
                if ($site == '2') {
                    $site = 'right';
                }
                if ($site == '3') {
                    $site = 'both';
                }
                if ($site == '4') {
                    $site = 'left upper lid';
                }
                if ($site == '5') {
                    $site = 'left lower lid';
                }
                if ($site == '6') {
                    $site = 'right upper lid';
                }
                if ($site == '7') {
                    $site = 'right lower lid';
                }
                if ($site == '8') {
                    $site = 'bilateral upper lid';
                }
                if ($site == '9') {
                    $site = 'bilateral lower lid';
                }
                $instructionData = str_ireplace('{SITE}', '<b>' . ucwords($site . ' Site') . '</b>', $instructionData);
                $instructionData = str_ireplace('{PROCEDURE}', '<b>' . ucfirst($patient_primary_procedure) . '</b>', $instructionData);
                $instructionData = str_ireplace('{SECONDARY PROCEDURE}', '<b>' . ucfirst($patient_secondary_procedure) . '</b>', $instructionData);
                $instructionData = str_ireplace('{TERTIARY PROCEDURE}', '<b>' . ucfirst($patient_tertiary_procedure) . '</b>', $instructionData);
                $instructionData = str_ireplace('{PRE-OP DIAGNOSIS}', '<b>' . $preopdiagnosis . '</b>', $instructionData);
                $instructionData = str_ireplace('{POST-OP DIAGNOSIS}', '<b>' . $postopdiagnosis . '</b>', $instructionData);
                $instructionData = str_ireplace('{DATE}', '<b>' . date('m-d-Y') . '</b>', $instructionData);

                //GETTING SITE DETAILS

                $instructionData = str_ireplace('{TEXTBOX_XSMALL}', "{TEXTBOX_XSMALL}", $instructionData);
                $instructionData = str_ireplace('{TEXTBOX_SMALL}', "{TEXTBOX_SMALL}", $instructionData);
                $instructionData = str_ireplace('{TEXTBOX_MEDIUM}', "{TEXTBOX_MEDIUM}", $instructionData);
                $instructionData = str_ireplace('{TEXTBOX_LARGE}', "{TEXTBOX_LARGE}", $instructionData);

                preg_match_all("/{TEXTBOX_XSMALL}/", $instructionData, $TEXTBOX_XSMALL_matches);
                for ($xsi = 1; $xsi <= count($TEXTBOX_XSMALL_matches[0]); $xsi++) {
                    $instructionData = preg_replace('/{TEXTBOX_XSMALL}/', "<input type='text' class='manageinput' name='xsmall" . $xsi . "' size='1' maxlength='1'>", $instructionData, 1);
                }
                preg_match_all("/{TEXTBOX_SMALL}/", $instructionData, $TEXTBOX_SMALL_matches);
                for ($xsi = 1; $xsi <= count($TEXTBOX_SMALL_matches[0]); $xsi++) {
                    $instructionData = preg_replace('/{TEXTBOX_SMALL}/', "<input type='text' class='manageinput' name='small" . $xsi . "' size='30' maxlength='30'>", $instructionData, 1);
                }
                preg_match_all("/{TEXTBOX_MEDIUM}/", $instructionData, $TEXTBOX_MEDIUM_matches);
                for ($xsi = 1; $xsi <= count($TEXTBOX_MEDIUM_matches[0]); $xsi++) {
                    $instructionData = preg_replace('/{TEXTBOX_MEDIUM}/', "<input type='text' class='manageinput' name='medium" . $xsi . "' size='60' maxlength='60'>", $instructionData, 1);
                }
                preg_match_all("/{TEXTBOX_LARGE}/", $instructionData, $TEXTBOX_LARGE_matches);
                for ($xsi = 1; $xsi <= count($TEXTBOX_LARGE_matches[0]); $xsi++) {
                    $instructionData = preg_replace('/{TEXTBOX_LARGE}/', "<textarea class='manageinput' name='large" . $xsi . "' cols='80' rows='2'></textarea>", $instructionData, 1);
                }

                /*
                  $instructionData = str_ireplace('{TEXTBOX_XSMALL}',"<input type='text' name='xsmall' size='1' maxlength='1'>",$instructionData);
                  $instructionData = str_ireplace('{TEXTBOX_SMALL}',"<input type='text' name='small' size='30' maxlength='30'>",$instructionData);
                  $instructionData = str_ireplace('{TEXTBOX_MEDIUM}',"<input type='text' name='medium' size='60' maxlength='60'>",$instructionData);
                  $instructionData = str_ireplace('{TEXTBOX_LARGE}',"<textarea name='large' cols='80' rows='2'></textarea>",$instructionData);
                 */

                //CODE TO ACTIVATE,DEACTIVATE SURGEON'S SIGNATURE (AND REPLACE VARIABLES)
                //START MAKE VALUE IN {} AS CASE SENSITIVE
                $instructionData = str_ireplace("{Surgeon's Signature}", "{Surgeon's Signature}", $instructionData);
                $instructionData = str_ireplace("{Surgeon's&nbsp;Signature}", "{Surgeon's&nbsp;Signature}", $instructionData);
                //END MAKE VALUE IN {} AS CASE SENSITIVE
                $chkSignSurgeon1Var = stristr($instructionData, "{Surgeon's Signature}");
                $chkSignSurgeon1VarNew = stristr($instructionData, "{Surgeon's&nbsp;Signature}");

                $chkSignSurgeon1Activate = '';
                if ($chkSignSurgeon1Var || $chkSignSurgeon1VarNew) {
                    $chkSignSurgeon1Activate = 'yes';
                }
                $instructionData = str_ireplace("{Surgeon's Signature}", " ", $instructionData);
                $instructionData = str_ireplace("{Surgeon's&nbsp;Signature}", " ", $instructionData);
                $instructionData = str_ireplace("{Surgeon&#39;s Signature}", " ", $instructionData);
                $instructionData = str_ireplace("{Surgeon&#39;s&nbsp;Signature}", " ", $instructionData);

                //END CODE TO ACTIVATE,DEACTIVATE SURGEON'S AND NURSE'S SIGNATURE (AND REPLACE VARIABLES)
                //CODE TO ACTIVATE,DEACTIVATE NURSE'S SIGNATURE (AND REPLACE VARIABLES)
                //START MAKE VALUE IN {} AS CASE SENSITIVE
                $instructionData = str_ireplace("{Nurse's Signature}", "{Nurse's Signature}", $instructionData);
                $instructionData = str_ireplace("{Nurse's&nbsp;Signature}", "{Nurse's&nbsp;Signature}", $instructionData);
                //END MAKE VALUE IN {} AS CASE SENSITIVE	
                $chkSignNurseVar = stristr($instructionData, "{Nurse's Signature}");
                $chkSignNurseVarNew = stristr($instructionData, "{Nurse's&nbsp;Signature}");
                $chkSignNurseActivate = '';
                if ($chkSignNurseVar || $chkSignNurseVarNew) {
                    $chkSignNurseActivate = 'yes';
                }
                $instructionData = str_ireplace("{Nurse's Signature}", " ", $instructionData);
                $instructionData = str_ireplace("{Nurse's&nbsp;Signature}", " ", $instructionData);
                $instructionData = str_ireplace("{Nurse&#39;s Signature}", " ", $instructionData);
                $instructionData = str_ireplace("{Nurse&#39;s&nbsp;Signature}", " ", $instructionData);

                //END CODE TO ACTIVATE,DEACTIVATE SURGEON'S AND NURSE'S SIGNATURE (AND REPLACE VARIABLES)
                //CODE TO ACTIVATE,DEACTIVATE Witness SIGNATURE (AND REPLACE VARIABLES)
                //START MAKE VALUE IN {} AS CASE SENSITIVE
                $instructionData = str_ireplace("{Witness Signature}", "{Witness Signature}", $instructionData);
                $instructionData = str_ireplace("{Witness&nbsp;Signature}", "{Witness&nbsp;Signature}", $instructionData);
                //END MAKE VALUE IN {} AS CASE SENSITIVE
                $chkSignWitness1Var = stristr($instructionData, "{Witness Signature}");
                $chkSignWitness1VarNew = stristr($instructionData, "{Witness&nbsp;Signature}");

                $chkSignWitness1Activate = '';
                if ($chkSignWitness1Var || $chkSignWitness1VarNew) {
                    $chkSignWitness1Activate = 'yes';
                }
                $instructionData = str_ireplace("{Witness Signature}", " ", $instructionData);
                $instructionData = str_ireplace("{Witness&nbsp;Signature}", " ", $instructionData);
                $instructionData = str_ireplace("{Witness&#39;s Signature}", " ", $instructionData);
                $instructionData = str_ireplace("{Witness&#39;s&nbsp;Signature}", " ", $instructionData);

                //END CODE TO ACTIVATE,DEACTIVATE Witness SIGNATURE (AND REPLACE VARIABLES)

                $instructionData = str_ireplace("{ASC NAME}", $loginUserFacilityName, $instructionData);
                $instructionData = str_ireplace("{ASC ADDRESS}", $ascInfoArr[0], $instructionData);
                $instructionData = str_ireplace("{ASC PHONE}", $ascInfoArr[1], $instructionData);
                $instructionData = str_ireplace('<img src="', '<img src="' . getenv('APP_URL') . '/' . getenv('APP_ROOT') . '/', $instructionData);
                $instructionData = str_ireplace("{SIGNATURE}", ' <a href="' . $instructionSheetId . "_" . $patient_id . "_" . $pConfirmId . '"><img src="' . getenv('APP_URL') . '/iascemr-dev/API2/signpad.jpg" id="' . $instructionSheetId . "_" . $patient_id . "_" . $pConfirmId . '" width="250px" height="100px" /></a><br />{SIGNATURE}', $instructionData);
                $signature_shown = 0;
                if (strpos($instructionData, '{SIGNATURE}')) {
                    $signature_shown = 1;
                }
                $stat = [];
                if ($instruction_id > 0) {
                    $chkFormStatusDetails->signSurgeon1DateTimeFormat = '0000-00-00 00:00:00';
                    $chkFormStatusDetails->signNurseDateTime = '0000-00-00 00:00:00';
                    $chkFormStatusDetails->signWitness1DateTime = '0000-00-00 00:00:00';
                    $chkFormStatusDetails->signSurgeon1Activate='no';
                    $chkFormStatusDetails->signNurseActivate='no';
                    $chkFormStatusDetails->signWitness1Activate='no';        
                }
                if ($chkSignSurgeon1Activate == 'yes') {
                    $stat[] = ['type' => 'Surgeon', 'name' => ucfirst($chkFormStatusDetails->signSurgeon1FirstName) . ' ' . ucfirst($chkFormStatusDetails->signSurgeon1LastName), 'datetime' => ((isset($chkFormStatusDetails->signSurgeon1DateTimeFormat) && $chkFormStatusDetails->signNurseDateTime != '0000-00-00 00:00:00') ? date("m-d-Y H:i:s", strtotime($chkFormStatusDetails->signSurgeon1DateTimeFormat)) : ''), 'sugn_show_sts' => $chkFormStatusDetails->signSurgeon1Activate ? $chkFormStatusDetails->signSurgeon1Activate : $chkSignSurgeon1Activate];
                }
                if ($chkSignNurseActivate == 'yes') {
                    $stat[] = ['type' => 'Nurse', 'name' => ucfirst($chkFormStatusDetails->signNurseFirstName) . ' ' . ucfirst($chkFormStatusDetails->signNurseLastName), 'datetime' => ((isset($chkFormStatusDetails->signNurseDateTime) && $chkFormStatusDetails->signNurseDateTime != '0000-00-00 00:00:00') ? date("m-d-Y H:i:s", strtotime($chkFormStatusDetails->signNurseDateTime)) : ''), 'sugn_show_sts' => $chkFormStatusDetails->signNurseActivate ? $chkFormStatusDetails->signNurseActivate : $chkSignNurseActivate];
                }
                if ($chkSignWitness1Activate == 'yes') {
                    $stat[] = ['type' => 'Witness', 'name' => ucfirst($chkFormStatusDetails->signWitness1FirstName) . ' ' . ucfirst($chkFormStatusDetails->signWitness1LastName), 'datetime' => ((isset($chkFormStatusDetails->signWitness1DateTime) && $chkFormStatusDetails->signWitness1DateTime != '0000-00-00 00:00:00') ? date("m-d-Y H:i:s", strtotime($chkFormStatusDetails->signWitness1DateTime)) : ''), 'sugn_show_sts' => $chkFormStatusDetails->signWitness1Activate ? $chkFormStatusDetails->signWitness1Activate : $chkSignWitness1Activate];
                }
                $signStatus = $stat;
                //--- get all content of instruction sheet -------	
                //END SIGNATURE CODE
                $status = 1;
                //GETTING SURGEONS DETAILS
                $message = ' Instruction Details ';
                $data = $dropdownList;
            }
        } else {
            $status = 1;
            $message = " no list ";
        }
        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => '',
                    'data' => $data,
                    'content' => $instructionData,
                    'instruction_id' => (int) $instructionSheetId,
                    'content_id' => (int) $instructionSheetId,
                    'patient_instruction_id' => (int) $patient_instruction_id,
                    'FormStatusDetails' => $signStatus,
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

    function saveInstructionsheet(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $loginUserType = $request->json()->get('UserType') ? $request->json()->get('UserType') : $request->input('UserType');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $electronic_signs = $request->json()->get('electronic_sign') ? $request->json()->get('electronic_sign') : $request->input('electronic_sign');
        $instructionSheetId = $instruction_id = $request->json()->get('instruction_id') ? $request->json()->get('instruction_id') : $request->input('instruction_id');
        $patient_instruction_id = $instruction_id = $request->json()->get('patient_instruction_id') ? $request->json()->get('patient_instruction_id') : $request->input('patient_instruction_id');
        $base64_img = $request->input($instructionSheetId . "_" . $patient_id . "_" . $pConfirmId);
        $arrayRecord = [];
        $data = [];
        $status = 0;
        $moveStatus = 0;
        $d = '';
        // $electronic_signs=json_encode($electronic_signs);
        $imgsrc='';
        $electronic_sign = 'yes';
        // $requiredStatus = 1;
        $message = " unauthorized ";
        $requiredStatus = [];
        $userId = $this->checkToken($userToken);
        if ($userId > 0) {
            $users = DB::selectone("select usersId,userTitle,fname,mname,lname,initial,address,address2,phone,concat(fname,' ',lname) as fullname,locked,user_privileges,admin_privileges"
                            . " hippaReviewedStatus,admin_privileges,hippaReviewedStatus,user_type,session_timeout from scemr.users where usersId='" . $userId . "'");

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
                //  $user
                //SAVE RECORD	
                if ($instructionSheetId > 0) {
                    $sig_count = 1;
                    $tablename = "patient_instruction_sheet";
                    $templtIdQry = '';
                    if ($instructionSheetId) {
                        $templtIdQry = " AND template_id='" . $instructionSheetId . "'";
                    }
                    $modifyFormStatus = '';
                    //START
                    $getinstructionSheetQry = "select * from `patient_instruction_sheet` where  patient_confirmation_id = '" . $pConfirmId . "' AND (instruction_sheet_data!=' ' OR instruction_sheet_data!='') $templtIdQry";
                    $getinstructionSheetRes = DB::selectone($getinstructionSheetQry);

                    if ($getinstructionSheetRes) {
                        $instruction_sheet_data = stripslashes($getinstructionSheetRes->instruction_sheet_data);
                        $modifyFormStatus = $getinstructionSheetRes->form_status;
                    } else {

                        $getInstructionSheetAdminDetails = $this->getRowRecord('instruction_template', 'instruction_id', $instructionSheetId);
                        if ($getInstructionSheetAdminDetails) {
                            $instruction_sheet_data = stripslashes($getInstructionSheetAdminDetails->instruction_desc);
                        }
                    }

                    //END
                    //START MAKE VALUE IN {} AS CASE SENSITIVE	
                    $instruction_sheet_data = str_ireplace("{TEXTBOX_XSMALL}", "{TEXTBOX_XSMALL}", $instruction_sheet_data);
                    $instruction_sheet_data = str_ireplace("{TEXTBOX_SMALL}", "{TEXTBOX_SMALL}", $instruction_sheet_data);
                    $instruction_sheet_data = str_ireplace("{TEXTBOX_MEDIUM}", "{TEXTBOX_MEDIUM}", $instruction_sheet_data);
                    $instruction_sheet_data = str_ireplace("{TEXTBOX_LARGE}", "{TEXTBOX_LARGE}", $instruction_sheet_data);
                    //END MAKE VALUE IN {} AS CASE SENSITIVE

                    $arrStr = array("{TEXTBOX_XSMALL}", "{TEXTBOX_SMALL}", "{TEXTBOX_MEDIUM}", "{TEXTBOX_LARGE}");
                    for ($j = 0; $j < count($arrStr); $j++) {

                        if ($arrStr[$j] == '{TEXTBOX_XSMALL}') {
                            $name = 'xsmall';
                            $size = 1;
                        } else if ($arrStr[$j] == '{TEXTBOX_SMALL}') {
                            $name = 'small';
                            $size = 30;
                        } else if ($arrStr[$j] == '{TEXTBOX_MEDIUM}') {
                            $name = 'medium';
                            $size = 60;
                        } else if ($arrStr[$j] == '{TEXTBOX_LARGE}') {
                            $name = 'large';
                            $size = 120;
                        }
                        $repVal = '';
                        if (substr_count($instruction_sheet_data, $arrStr[$j]) >= 1) {

                            if ($arrStr[$j] == '{TEXTBOX_XSMALL}' || $arrStr[$j] == '{TEXTBOX_SMALL}' || $arrStr[$j] == '{TEXTBOX_MEDIUM}') {
                                $c = 1;
                                $arrExp = explode($arrStr[$j], $instruction_sheet_data);
                                for ($p = 0; $p < count($arrExp) - 1; $p++) {
                                    $repVal .= $arrExp[$p] . '<input type="text"  name="' . $name . $c . '" value="' . $request->input($name . $c) . '" size="' . $size . '"  maxlength="' . $size . '">';
                                    $c++;
                                }
                                $repVal .= end($arrExp);
                                $instruction_sheet_data = $repVal;
                            } else if ($arrStr[$j] == '{TEXTBOX_LARGE}') {
                                $c = 1;
                                $arrExp = explode($arrStr[$j], $instruction_sheet_data);

                                for ($p = 0; $p < count($arrExp) - 1; $p++) {
                                    $repVal .= $arrExp[$p] . '<textarea rows="2" cols="80" name="' . $name . $c . '"> ' . $request->input($name . $c) . ' </textarea>';
                                    $c++;
                                }
                                $repVal .= end($arrExp);
                                $instruction_sheet_data = $repVal;
                            }
                        }
                    }

                    //START MODIFY TEXTBOXES AFTER SAVED ATLEAST ONCE	
                    if ($modifyFormStatus == 'completed' || $modifyFormStatus == 'not completed') {
                        $arrModifyStr = array('name="xsmall', 'name="small', 'name="medium', 'name="large');
                        for ($j = 0; $j < count($arrModifyStr); $j++) {

                            if ($arrModifyStr[$j] == 'name="xsmall') {
                                $name = 'xsmall';
                                $size = 1;
                            } else if ($arrModifyStr[$j] == 'name="small') {
                                $name = 'small';
                                ;
                                $size = 30;
                            } else if ($arrModifyStr[$j] == 'name="medium') {
                                $name = 'medium';
                                $size = 60;
                            } else if ($arrModifyStr[$j] == 'name="large') {
                                $name = 'large';
                                $size = 120;
                            }
                            $repModifyVal = '';
                            if (substr_count($instruction_sheet_data, $arrModifyStr[$j]) >= 1) {
                                $cntSubstr = substr_count($instruction_sheet_data, $arrModifyStr[$j]);
                                if ($arrModifyStr[$j] == 'name="xsmall' || $arrModifyStr[$j] == 'name="small' || $arrModifyStr[$j] == 'name="medium') {
                                    $c = 1;
                                    for ($p = 0; $p < $cntSubstr; $p++) {

                                        $txtBoxReplace = str_ireplace('<input type="text"  name="' . $name . $c . '" value="', '<input type="text"  name="' . $name . $c . '" value="' . $request->input($name . $c) . '"', $instruction_sheet_data);
                                        $instruction_sheet_data = $txtBoxReplace;
                                        $txtBoxExplode = explode('<input type="text"  name="' . $name . $c . '" value="' . $request->input($name . $c) . '"', $instruction_sheet_data);
                                        $txtBoxFurtherExplode = explode(' size="' . $size . '"', $txtBoxExplode[1]);
                                        $getpos = strpos($txtBoxFurtherExplode[0], '"');
                                        $txtBoxFurtherExplodeSubStr = substr($txtBoxExplode[1], $getpos + 1);
                                        //if($_POST[$name.$c]) {
                                        $instruction_sheet_data = $txtBoxExplode[0] . '<input type="text"  name="' . $name . $c . '" value="' . $request->input($name . $c) . '"' . $txtBoxFurtherExplodeSubStr;
                                        //}
                                        $c++;
                                    }
                                } else if ($arrModifyStr[$j] == 'name="large') {
                                    $c = 1;
                                    for ($p = 0; $p < $cntSubstr; $p++) {
                                        $instruction_sheet_data = str_ireplace("\n", "", $instruction_sheet_data);
                                        $instruction_sheet_data = preg_replace('/<textarea rows="2" cols="80" name="' . $name . $c . '"> (.*?) <\/textarea>/', '<textarea rows="2" cols="80" name="' . $name . $c . '"> ' . $request->input($name . $c) . ' </textarea>', $instruction_sheet_data);
                                        $c++;
                                    }
                                }
                            }
                            /*
                              else if(substr_count($instruction_sheet_data,$arrModifyStr[$j]) == 1)
                              {
                              if($arrModifyStr[$j] == 'name="xsmall' || $arrModifyStr[$j] == 'name="small' || $arrModifyStr[$j] == 'name="medium')
                              {
                              $txtBoxExplode = explode('<input type="text" name="'.$name.'" value="',$instruction_sheet_data);
                              $txtBoxSizeExplode = explode('" size="'.$size.'" >',$instruction_sheet_data);

                              $repModifyValTemp = '<input type="text" name="'.$name.'" value="'.$_POST[$name].'" size="'.$size.'" >';
                              $repModifyVal = $txtBoxExplode[0].$repModifyValTemp.$txtBoxSizeExplode[1];
                              $instruction_sheet_data = $repModifyVal;
                              }
                              else if($arrModifyStr[$j] == 'name="large')
                              {
                              $txtAreaExplode = explode('<textarea rows="2" cols="80" name="'.$name.'"> ',$instruction_sheet_data);
                              $txtAreaSizeExplode = explode(' </textarea>',$instruction_sheet_data);

                              $repModifyValTemp = '<textarea rows="2" cols="80" name="'.$name.'"> '.$_POST[$name].' </textarea>';
                              $repModifyVal = $txtAreaExplode[0].$repModifyValTemp.$txtAreaSizeExplode[1];
                              $instruction_sheet_data = $repModifyVal;
                              }
                              }
                             */
                        }
                    }

                    //MODIFY TEXTBOXES AFTER SAVED ATLEAST ONCE		

                    $form_status = 'completed'; //BY DEFAULT VALUE
                    //SAVE SIGNATURE
                    //CODE TO SET FORM STATUS 
                    $instruction_sheet_data = str_ireplace('{DATE}', '<b>' . date('m-d-Y') . '</b>', $instruction_sheet_data);
                    if (isset($base64_img) && trim($base64_img) <> "") {
                        $imgSrc = $this->convertBase64(trim($base64_img), $instructionSheetId, $patient_id, $pConfirmId);
                        $imgsrc = preg_replace('/<img src=\"(.*?)\">/', "\\1", 'SigPlus_images/' . $imgSrc);
                        $sigDtTmSave = '<br /><div style="font-weight:normal;"><b>Signature Date:</b>&nbsp;' . $this->getFullDtTmFormat(date("Y-m-d H:i:s")) . '</div>';
                        if ($imgSrc <> "") {
                            $instruction_sheet_data1 = '<img src="SigPlus_images/' . $imgSrc . '" width="150" height="63" />' . $sigDtTmSave;
                        } else {
                            $instruction_sheet_data1 = '';
                        }
                        // $instruction_sheet_data1 = $this->crawl_page($instruction_sheet_data, $imgsrc);
                        $instruction_sheet_data = str_ireplace("{SIGNATURE}", $instruction_sheet_data1, $instruction_sheet_data);
                    } else {
                        $instruction_sheet_data = str_ireplace("{SIGNATURE}", "{SIGNATURE}", $instruction_sheet_data);
                    }

                    $form_status = 'completed';
                    if (!$instruction_sheet_data) {
                        $form_status = "not completed";
                    }
                    $hidd_signWitness1Activate = 'no';
                    $hidd_signNurseActivate = 'no';
                    $hidd_signSurgeon1Activate = 'no';
                    /*
                      [{"sign_sts":"no","date_is":"0000-00-00 00:00:00","type":"Surgeon"},{"sign_sts":"yes","date_is":"2019-10-23 05:26:40","type":"Nurse"}
                     *          */
                    $electronic_signs = json_decode($electronic_signs);
                    foreach ($electronic_signs as $electronic_signss) {
                        if (strtolower($electronic_signss->type) == 'surgeon' && strtolower($loginUserType) == 'surgeon') {
                            $arrayRecord['signSurgeon1Id'] = $userId;
                            $arrayRecord['signSurgeon1FirstName'] = $users->fname;
                            $arrayRecord['signSurgeon1MiddleName'] = $users->mname;
                            $arrayRecord['signSurgeon1LastName'] = $users->lname;
                            $arrayRecord['signSurgeon1Status'] = $electronic_signss->sign_sts; //$electronic_sign['surgeon']['status'];
                            $arrayRecord['signSurgeon1DateTime'] = ($electronic_signss->date_is!='')?date("Y-m-d H:i:s", strtotime($electronic_signss->date_is)):Date("Y-m-d H:i:s"); //$electronic_sign['surgeon']['dttime'];
                            $hidd_signSurgeon1Activate = 'yes';
                            $arrayRecord['signSurgeon1Activate'] = "yes";
                        }
                        if (strtolower($electronic_signss->type) == 'nurse' && strtolower($loginUserType) == 'nurse') {
                            $hidd_signNurseActivate = 'yes';
                            $arrayRecord['signNurseId'] = $userId;
                            $arrayRecord['signNurseFirstName'] = $users->fname;
                            $arrayRecord['signNurseMiddleName'] = $users->mname;
                            $arrayRecord['signNurseLastName'] = $users->lname;
                            $arrayRecord['signNurseStatus'] = $electronic_signss->sign_sts;
                            $arrayRecord['signNurseDateTime'] = ($electronic_signss->date_is!='')?date("Y-m-d H:i:s", strtotime($electronic_signss->date_is)):Date("Y-m-d H:i:s"); //$electronic_sign['surgeon']['dttime'];
                            $arrayRecord['signNurseActivate'] = "yes";
                        }
                        if (strtolower($electronic_signss->type) == 'witness' && strtolower($loginUserType) == 'witness') {
                            $hidd_signWitness1Activate = 'yes';
                            $arrayRecord['signWitness1Id'] = $userId;
                            $arrayRecord['signWitness1FirstName'] = $users->fname;
                            $arrayRecord['signWitness1MiddleName'] = $users->mname;
                            $arrayRecord['signWitness1LastName'] = $users->lname;
                            $arrayRecord['signWitness1Status'] = $electronic_signss->sign_sts;
                            $arrayRecord['signWitness1DateTime'] = ($electronic_signss->date_is!='')?date("Y-m-d H:i:s", strtotime($electronic_signss->date_is)):Date("Y-m-d H:i:s"); //$electronic_sign['surgeon']['dttime'];
                            $arrayRecord['signWitness1Activate'] = "yes";
                        }
                    }

                    //END CODE TO SET FORM STATUS 
                    $arrayRecord['patient_confirmation_id'] = $pConfirmId;
                    $arrayRecord['form_status'] = $form_status;

                    $arrayRecord['signSurgeon1Activate'] = $hidd_signSurgeon1Activate;
                    $arrayRecord['signNurseActivate'] = $hidd_signNurseActivate;
                    $arrayRecord['signWitness1Activate'] = $hidd_signWitness1Activate;
                    $arrayRecord['template_id'] = $instructionSheetId;

                    //MAKE AUDIT STATUS CRATED OR MODIFIED
                    unset($arrayStatusRecord);
                    $arrayStatusRecord['user_id'] = $userId;
                    $arrayStatusRecord['patient_id'] = $patient_id;
                    $arrayStatusRecord['confirmation_id'] = $pConfirmId;
                    $arrayStatusRecord['form_name'] = 'post_op_instruction_sheet_form';
                    $arrayStatusRecord['action_date_time'] = date('Y-m-d H:i:s');
                    //MAKE AUDIT STATUS CRATED OR MODIFIED
                    $arrayRecord['instruction_sheet_data'] = addslashes($instruction_sheet_data);
                    $arrayRecord['form_status'] = $form_status;
                    $arrayRecord['template_id'] = $instructionSheetId;

                    if (!$patient_instruction_id) {
                        $patient_instruction_id = DB::table('patient_instruction_sheet')->insertGetId($arrayRecord);
                    } else {
                        DB::table('patient_instruction_sheet')->where('patient_instruction_id', $patient_instruction_id)->update($arrayRecord);
                    }
                    //CODE START TO SET AUDIT STATUS AFTER SAVE
                    unset($conditionArr);
                    $conditionArr['confirmation_id'] = $pConfirmId;
                    $conditionArr['form_name'] = 'post_op_instruction_sheet_form';
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
                    $data = $instruction_sheet_data;
                    $message = 'Data saved successfully !';
                    //CODE END TO SET AUDIT STATUS AFTER SAVE
                } //Save Code
            }
        }

        return response()->json([
                    'status' => 1,
                    'message' => $message,
                    'requiredStatus' => @$imgsrc,
                    'data' => '',
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

    function crawl_page($html, $path) {
        $html = str_replace('<p>', '', $html);
        $html = str_replace('</p>', '', $html);
        $dom = new \DOMDocument('1.0', 'UTF-8');
        //libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        $anchors = $dom->getElementsByTagName('img');
        foreach ($anchors as $element) {
            $src = $element->getAttribute('src');
            $alt = $element->getAttribute('alt');
            $height = $element->getAttribute('height');
            $width = $element->getAttribute('width');
            return '<img src="' . $path . '" alt="' . $alt . '" height="'
                    . $height . '" width="' . $width . '"/>';
        }
    }

}
