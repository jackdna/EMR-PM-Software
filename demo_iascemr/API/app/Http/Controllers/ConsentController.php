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

class ConsentController extends Controller {

    public function consent_template(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $loginUserType = $request->json()->get('UserType') ? $request->json()->get('UserType') : $request->input('UserType');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $consent_template_id = $consentMultipleId = $request->json()->get('consentMultipleId') ? $request->json()->get('consentMultipleId') : $request->input('consentMultipleId');
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
        $consent_left_list = '';
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
                $status = 1;
                $message = " Consent template list ";
                $consent_content = '';
                $consent_left_list = [];
                if ($consentMultipleId == "") {
                  //  $consent_left_list = $this->left_slider_menu($request,$pConfirmId);
                    $status = 1;
                    $message='Consent MultipleId  is missing';
                }
                if ($consentMultipleId <> "") {
                    //GET PATIENT DETAIL
                    $qryStr = "SELECT fac_id, fac_name FROM facility_tbl WHERE fac_id = '" . $facility_id . "'";
                    $qryRow = DB::selectone($qryStr);
                    $loginUserFacilityName = $qryRow->fac_name;
                    $ascInfoArr = $this->getASCInfo($facility_id);
                    $genderArray = array("m" => "Male", "f" => "Female");
                    $Consent_patientName_tblQry = "SELECT * FROM `patient_data_tbl` WHERE `patient_id` = '" . $patient_id . "'";
                    $Consent_patientName_tblRow = DB::selectone($Consent_patientName_tblQry);

                    $Consent_patientName = $Consent_patientName_tblRow->patient_lname . ", " . $Consent_patientName_tblRow->patient_fname . " " . $Consent_patientName_tblRow->patient_mname;


                    $Consent_patientNameDobTemp = $Consent_patientName_tblRow->date_of_birth;
                    $Consent_patientNameDob_split = explode("-", $Consent_patientNameDobTemp);
                    $Consent_patientNameDob = $Consent_patientNameDob_split[1] . "-" . $Consent_patientNameDob_split[2] . "-" . $Consent_patientNameDob_split[0];


                    $Consent_patientConfirm_tblQry = "SELECT * FROM `patientconfirmation` WHERE `patientConfirmationId` = '" . $pConfirmId . "'";
                    $Consent_patientConfirm_tblRow = DB::selectone($Consent_patientConfirm_tblQry); // or die(imw_error());

                    $finalizeStatus = $Consent_patientConfirm_tblRow->finalize_status;
                    $Consent_patientConfirmDosTemp = $Consent_patientConfirm_tblRow->dos;
                    $Consent_patientConfirmDos_split = explode("-", $Consent_patientConfirmDosTemp);
                    $Consent_patientConfirmDos = $Consent_patientConfirmDos_split[1] . "-" . $Consent_patientConfirmDos_split[2] . "-" . $Consent_patientConfirmDos_split[0];

                    $Consent_patientConfirmSurgeon = $Consent_patientConfirm_tblRow->surgeon_name;
                    $Consent_patientConfirmSiteTemp = $Consent_patientConfirm_tblRow->site;
                    // APPLYING NUMBERS TO PATIENT SITE
                    if ($Consent_patientConfirmSiteTemp == 1) {
                        $Consent_patientConfirmSite = "Left Eye";  //OD
                    } else if ($Consent_patientConfirmSiteTemp == 2) {
                        $Consent_patientConfirmSite = "Right Eye";  //OS
                    } else if ($Consent_patientConfirmSiteTemp == 3) {
                        $Consent_patientConfirmSite = "Both Eye";  //OU
                    } else if ($Consent_patientConfirmSiteTemp == 4) {
                        $Consent_patientConfirmSite = "Left Upper Lid";  //OU
                    } else if ($Consent_patientConfirmSiteTemp == 5) {
                        $Consent_patientConfirmSite = "Left Lower Lid";  //OU
                    } else if ($Consent_patientConfirmSiteTemp == 6) {
                        $Consent_patientConfirmSite = "Right Upper Lid";  //OU
                    } else if ($Consent_patientConfirmSiteTemp == 7) {
                        $Consent_patientConfirmSite = "Right Lower Lid";  //OU
                    } else if ($Consent_patientConfirmSiteTemp == 8) {
                        $Consent_patientConfirmSite = "Bilateral Upper Lid";  //OU
                    } else if ($Consent_patientConfirmSiteTemp == 9) {
                        $Consent_patientConfirmSite = "Bilateral Lower Lid";  //OU
                    }
                    // END APPLYING NUMBERS TO PATIENT SITE
                    $Consent_patientConfirmPrimProc = $Consent_patientConfirm_tblRow->patient_primary_procedure;
                    $Consent_patientConfirmSecProc = $Consent_patientConfirm_tblRow->patient_secondary_procedure;
                    $Consent_patientConfirmTeriProc = $Consent_patientConfirm_tblRow->patient_tertiary_procedure;

                    //GET ASSIGNED SURGEON ID AND SURGEON NAME AND SURGEON TYPE
                    $consentAssignedSurgeonId = $Consent_patientConfirm_tblRow->surgeonId;
                    $consentAssignedSurgeonName = stripslashes($Consent_patientConfirm_tblRow->surgeon_name);
                    //END GET ASSIGNED SURGEON ID AND SURGEON NAME AND SURGEON TYPE

                    $Consent_patientConfirmAnes_NA = $Consent_patientConfirm_tblRow->anes_NA;
                    $signAnesthesiaIdBackColor = ''; // $chngBckGroundColor;
                    if ($Consent_patientConfirmAnes_NA == 'Yes') {
                        $signAnesthesiaIdBackColor = $whiteBckGroundColor;
                    }

                    //START GET ASSIGNED ANES NAME
                    $Consent_patientConfirmAnes = "";
                    $anesthesiologist_id_confirm = $Consent_patientConfirm_tblRow->anesthesiologist_id;
                    if ($anesthesiologist_id_confirm) {
                        $Consent_patientConfirmAnes = $this->getUserName($anesthesiologist_id_confirm, 'Anesthesiologist');
                    }
                    //END GET ASSIGNED ANES NAME
                    //END GET PATIENT DETAIL
                    //VIEW RECORD FROM DATABASE
                    $ViewConsentSurgeryQry = "select * from `consent_multiple_form` where  confirmation_id = '" . $pConfirmId . "'  AND consent_template_id='" . $consentMultipleId . "' AND consent_template_id!='0'";
                    $ViewConsentSurgeryRow = DB::selectone($ViewConsentSurgeryQry); // or die($imw_error());
                    if ($ViewConsentSurgeryRow) {
                        $consentSurgery_patient_sign = $ViewConsentSurgeryRow->surgery_consent_sign;
                        $surgery_consent_data = stripslashes($ViewConsentSurgeryRow->surgery_consent_data);
                        $surgery_consent_name = $ViewConsentSurgeryRow->surgery_consent_name;
                        $surgery_consent_alias = stripslashes($ViewConsentSurgeryRow->surgery_consent_alias);
                        $sigStatus = $ViewConsentSurgeryRow->sigStatus;

                        $signSurgeon1Activate = $ViewConsentSurgeryRow->signSurgeon1Activate;
                        $signSurgeon1Id = $ViewConsentSurgeryRow->signSurgeon1Id;
                        $signSurgeon1FirstName = $ViewConsentSurgeryRow->signSurgeon1FirstName;
                        $signSurgeon1MiddleName = $ViewConsentSurgeryRow->signSurgeon1MiddleName;
                        $signSurgeon1LastName = $ViewConsentSurgeryRow->signSurgeon1LastName;
                        $signSurgeon1Status = $ViewConsentSurgeryRow->signSurgeon1Status;
                        $signSurgeon1SignDate = $ViewConsentSurgeryRow->signSurgeon1DateTime;


                        $signNurseActivate = $ViewConsentSurgeryRow->signNurseActivate;
                        $signNurseId = $ViewConsentSurgeryRow->signNurseId;
                        $signNurseFirstName = $ViewConsentSurgeryRow->signNurseFirstName;
                        $signNurseMiddleName = $ViewConsentSurgeryRow->signNurseMiddleName;
                        $signNurseLastName = $ViewConsentSurgeryRow->signNurseLastName;
                        $signNurseStatus = $ViewConsentSurgeryRow->signNurseStatus;
                        $signNurseSignDate = $ViewConsentSurgeryRow->signNurseDateTime;

                        $signAnesthesia1Activate = $ViewConsentSurgeryRow->signAnesthesia1Activate;
                        $signAnesthesia1Id = $ViewConsentSurgeryRow->signAnesthesia1Id;
                        $signAnesthesia1FirstName = $ViewConsentSurgeryRow->signAnesthesia1FirstName;
                        $signAnesthesia1MiddleName = $ViewConsentSurgeryRow->signAnesthesia1MiddleName;
                        $signAnesthesia1LastName = $ViewConsentSurgeryRow->signAnesthesia1LastName;
                        $signAnesthesia1Status = $ViewConsentSurgeryRow->signAnesthesia1Status;
                        $signAnesthesia1SignDate = $ViewConsentSurgeryRow->signAnesthesia1DateTime;

                        $signWitness1Activate = $ViewConsentSurgeryRow->signWitness1Activate;
                        $signWitness1Id = $ViewConsentSurgeryRow->signWitness1Id;
                        $signWitness1FirstName = $ViewConsentSurgeryRow->signWitness1FirstName;
                        $signWitness1MiddleName = $ViewConsentSurgeryRow->signWitness1MiddleName;
                        $signWitness1LastName = $ViewConsentSurgeryRow->signWitness1LastName;
                        $signWitness1Status = $ViewConsentSurgeryRow->signWitness1Status;
                        $signWitness1SignDate = $ViewConsentSurgeryRow->signWitness1DateTime;

                        $form_status = $ViewConsentSurgeryRow->form_status;
                        //PURGE
                        $purge_status = $ViewConsentSurgeryRow->consent_purge_status;
                        $instructionSheetId = $ViewConsentSurgeryRow->surgery_consent_id;
                        $consent_template_id = $ViewConsentSurgeryRow->consent_template_id;
                        $surgery_consent_id = $ViewConsentSurgeryRow->surgery_consent_id;
                    }
                    //PURGE
                    // $saveLink = $saveLink . "&amp;form_status=" . $form_status;
                    //FIRST TIME FETCH DATA FROM 'CONSENT FORMS TEMPLATE' TABLE	
                    if (trim($surgery_consent_data) == "" && $consentMultipleId > 0) {
                        $ViewConsentTemplateQry = "select * from `consent_forms_template` where  consent_id = '" . $consentMultipleId . "'";
                        $ViewConsentTemplateRow = DB::selectone($ViewConsentTemplateQry); // or die(imw_error());
                        $surgery_consent_data = stripslashes($ViewConsentTemplateRow->consent_data);
                        $surgery_consent_name = $ViewConsentTemplateRow->consent_name;
                        $surgery_consent_alias = stripslashes($ViewConsentTemplateRow->consent_alias);
                        $consent_category_id = $ViewConsentTemplateRow->consent_category_id;
                    }

                    //FIRST TIME FETCH DATA FROM 'CONSENT FORMS TEMPLATE' TABLE		
                    //REPLACE FIELD IN PARENTHESIS WITH ACTUAL VALUE 			
                    $surgery_consent_data = str_ireplace("&#39;", "'", $surgery_consent_data);
                    $surgery_consent_data = str_ireplace("{PATIENT ID}", "<b>" . $Consent_patientName_tblRow->patient_id . "</b>", $surgery_consent_data);
                    $surgery_consent_data = str_ireplace("{PATIENT FIRST NAME}", $Consent_patientName_tblRow->patient_fname, $surgery_consent_data);
                    $surgery_consent_data = str_ireplace("{MIDDLE INITIAL}", $Consent_patientName_tblRow->patient_mname, $surgery_consent_data);
                    $surgery_consent_data = str_ireplace("{LAST NAME}", $Consent_patientName_tblRow->patient_lname, $surgery_consent_data);
                    $surgery_consent_data = str_ireplace("{DOB}", "<b>" . $Consent_patientNameDob . "</b>", $surgery_consent_data);
                    $surgery_consent_data = str_ireplace("{PATIENT GENDER}", "<b>" . @$genderArray[$Consent_patientName_tblRow->sex] . "</b>", $surgery_consent_data);
                    $surgery_consent_data = str_ireplace("{DOS}", "<b>" . $Consent_patientConfirmDos . "</b>", $surgery_consent_data);
                    $surgery_consent_data = str_ireplace("{SURGEON NAME}", "<b>" . $Consent_patientConfirmSurgeon . "</b>", $surgery_consent_data);
                    $surgery_consent_data = str_ireplace("{ANES NAME}", "<b>" . $Consent_patientConfirmAnes . "</b>", $surgery_consent_data);
                    $surgery_consent_data = str_ireplace("{SITE}", "<b>" . $Consent_patientConfirmSite . "</b>", $surgery_consent_data);
                    $surgery_consent_data = str_ireplace("{PROCEDURE}", "<b>" . $Consent_patientConfirmPrimProc . "</b>", $surgery_consent_data);
                    $surgery_consent_data = str_ireplace("{SECONDARY PROCEDURE}", "<b>" . $Consent_patientConfirmSecProc . "</b>", $surgery_consent_data);
                    $surgery_consent_data = str_ireplace("{TERTIARY PROCEDURE}", "<b>" . $Consent_patientConfirmTeriProc . "</b>", $surgery_consent_data);
                    $surgery_consent_data = str_ireplace("{DATE}", "<b>" . date('m-d-Y') . "</b>", $surgery_consent_data);
                    $surgery_consent_data = str_ireplace("{ASC NAME}", $loginUserFacilityName, $surgery_consent_data);
                    $surgery_consent_data = str_ireplace("{ASC ADDRESS}", $ascInfoArr[0], $surgery_consent_data);
                    $surgery_consent_data = str_ireplace("{ASC PHONE}", $ascInfoArr[1], $surgery_consent_data);
                    /*
                      $surgery_consent_data = str_ireplace('{TEXTBOX_XSMALL}',"<input type='text' name='xsmall' size='1' maxlength='1'>",$surgery_consent_data);
                      $surgery_consent_data = str_ireplace('{TEXTBOX_SMALL}',"<input type='text' name='small' size='30' maxlength='30'>",$surgery_consent_data);
                      $surgery_consent_data = str_ireplace('{TEXTBOX_MEDIUM}',"<input type='text' name='medium' size='60' maxlength='60'>",$surgery_consent_data);
                      $surgery_consent_data = str_ireplace('{TEXTBOX_LARGE}',"<textarea name='large' cols='80' rows='2'></textarea>",$surgery_consent_data);
                     */

                    $surgery_consent_data = str_ireplace('{TEXTBOX_XSMALL}', "{TEXTBOX_XSMALL}", $surgery_consent_data);
                    $surgery_consent_data = str_ireplace('{TEXTBOX_SMALL}', "{TEXTBOX_SMALL}", $surgery_consent_data);
                    $surgery_consent_data = str_ireplace('{TEXTBOX_MEDIUM}', "{TEXTBOX_MEDIUM}", $surgery_consent_data);
                    $surgery_consent_data = str_ireplace('{TEXTBOX_LARGE}', "{TEXTBOX_LARGE}", $surgery_consent_data);

                    preg_match_all("/{TEXTBOX_XSMALL}/", $surgery_consent_data, $TEXTBOX_XSMALL_matches);
                    for ($xsi = 1; $xsi <= count($TEXTBOX_XSMALL_matches[0]); $xsi++) {
                        $surgery_consent_data = preg_replace('/{TEXTBOX_XSMALL}/', "<input type='text' name='xsmall" . $xsi . "' size='1' maxlength='1'>", $surgery_consent_data, 1);
                    }
                    preg_match_all("/{TEXTBOX_SMALL}/", $surgery_consent_data, $TEXTBOX_SMALL_matches);
                    for ($xsi = 1; $xsi <= count($TEXTBOX_SMALL_matches[0]); $xsi++) {
                        $surgery_consent_data = preg_replace('/{TEXTBOX_SMALL}/', "<input type='text' name='small" . $xsi . "' size='30' maxlength='30'>", $surgery_consent_data, 1);
                    }preg_match_all("/{TEXTBOX_MEDIUM}/", $surgery_consent_data, $TEXTBOX_MEDIUM_matches);
                    for ($xsi = 1; $xsi <= count($TEXTBOX_MEDIUM_matches[0]); $xsi++) {
                        $surgery_consent_data = preg_replace('/{TEXTBOX_MEDIUM}/', "<input type='text' name='medium" . $xsi . "' size='60' maxlength='60'>", $surgery_consent_data, 1);
                    }preg_match_all("/{TEXTBOX_LARGE}/", $surgery_consent_data, $TEXTBOX_LARGE_matches);
                    for ($xsi = 1; $xsi <= count($TEXTBOX_LARGE_matches[0]); $xsi++) {
                        $surgery_consent_data = preg_replace('/{TEXTBOX_LARGE}/', "<textarea name='large" . $xsi . "' cols='80' rows='2'></textarea>", $surgery_consent_data, 1);
                    }


                    //CODE TO ACTIVATE,DEACTIVATE SURGEON'S SIGNATURE (AND REPLACE VARIABLES)
                    //START MAKE VALUE IN {} AS CASE SENSITIVE
                    $surgery_consent_data = str_ireplace("{Surgeon's Signature}", "{Surgeon's Signature}", $surgery_consent_data);
                    $surgery_consent_data = str_ireplace("{Surgeon's&nbsp;Signature}", "{Surgeon's&nbsp;Signature}", $surgery_consent_data);
                    //END MAKE VALUE IN {} AS CASE SENSITIVE
                    $chkSignSurgeon1Var = stristr($surgery_consent_data, "{Surgeon's Signature}");
                    $chkSignSurgeon1VarNew = stristr($surgery_consent_data, "{Surgeon's&nbsp;Signature}");

                    $chkSignSurgeon1Activate = '';
                    if ($chkSignSurgeon1Var || $chkSignSurgeon1VarNew) {
                        $chkSignSurgeon1Activate = 'yes';
                    }
                    $surgery_consent_data = str_ireplace("{Surgeon's Signature}", " ", $surgery_consent_data);
                    $surgery_consent_data = str_ireplace("{Surgeon's&nbsp;Signature}", " ", $surgery_consent_data);

                    //END CODE TO ACTIVATE,DEACTIVATE SURGEON'S AND NURSE'S SIGNATURE (AND REPLACE VARIABLES)
                    //CODE TO ACTIVATE,DEACTIVATE NURSE'S SIGNATURE (AND REPLACE VARIABLES)
                    //START MAKE VALUE IN {} AS CASE SENSITIVE
                    $surgery_consent_data = str_ireplace("{Nurse's Signature}", "{Nurse's Signature}", $surgery_consent_data);
                    $surgery_consent_data = str_ireplace("{Nurse's&nbsp;Signature}", "{Nurse's&nbsp;Signature}", $surgery_consent_data);
                    //END MAKE VALUE IN {} AS CASE SENSITIVE	
                    $chkSignNurseVar = stristr($surgery_consent_data, "{Nurse's Signature}");
                    $chkSignNurseVarNew = stristr($surgery_consent_data, "{Nurse's&nbsp;Signature}");
                    $chkSignNurseActivate = '';
                    if ($chkSignNurseVar || $chkSignNurseVarNew) {
                        $chkSignNurseActivate = 'yes';
                    }
                    $surgery_consent_data = str_ireplace("{Nurse's Signature}", " ", $surgery_consent_data);
                    $surgery_consent_data = str_ireplace("{Nurse's&nbsp;Signature}", " ", $surgery_consent_data);
                    //END CODE TO ACTIVATE,DEACTIVATE SURGEON'S AND NURSE'S SIGNATURE (AND REPLACE VARIABLES)
                    //CODE TO ACTIVATE,DEACTIVATE Anesthesiologist's SIGNATURE (AND REPLACE VARIABLES)
                    //START MAKE VALUE IN {} AS CASE SENSITIVE	
                    $surgery_consent_data = str_ireplace("{Anesthesiologist's Signature}", "{Anesthesiologist's Signature}", $surgery_consent_data);
                    $surgery_consent_data = str_ireplace("{Anesthesiologist's&nbsp;Signature}", "{Anesthesiologist's&nbsp;Signature}", $surgery_consent_data);
                    //END MAKE VALUE IN {} AS CASE SENSITIVE
                    $chkSignAnesthesia1Var = stristr($surgery_consent_data, "{Anesthesiologist's Signature}");
                    $chkSignAnesthesia1VarNew = stristr($surgery_consent_data, "{Anesthesiologist's&nbsp;Signature}");
                    $chkSignAnesthesia1Activate = '';
                    if ($chkSignAnesthesia1Var || $chkSignAnesthesia1VarNew) {
                        $chkSignAnesthesia1Activate = 'yes';
                    }
                    $surgery_consent_data = str_ireplace("{Anesthesiologist's Signature}", " ", $surgery_consent_data);
                    $surgery_consent_data = str_ireplace("{Anesthesiologist's&nbsp;Signature}", " ", $surgery_consent_data);
                    //END CODE TO ACTIVATE,DEACTIVATE SURGEON'S AND NURSE'S SIGNATURE (AND REPLACE VARIABLES)
                    //CODE TO ACTIVATE,DEACTIVATE Anesthesiologist's SIGNATURE (AND REPLACE VARIABLES)
                    //START MAKE VALUE IN {} AS CASE SENSITIVE
                    $surgery_consent_data = str_ireplace("{Witness Signature}", "{Witness Signature}", $surgery_consent_data);
                    $surgery_consent_data = str_ireplace("{Witness&nbsp;Signature}", "{Witness&nbsp;Signature}", $surgery_consent_data);
                    //END MAKE VALUE IN {} AS CASE SENSITIVE
                    $chkSignWitness1Var = stristr($surgery_consent_data, "{Witness Signature}");
                    $chkSignWitness1VarNew = stristr($surgery_consent_data, "{Witness&nbsp;Signature}");

                    $chkSignWitness1Activate = '';
                    if ($chkSignWitness1Var || $chkSignWitness1VarNew) {
                        $chkSignWitness1Activate = 'yes';
                    }
                    $surgery_consent_data = str_ireplace("{Witness Signature}", " ", $surgery_consent_data);
                    $surgery_consent_data = str_ireplace("{Witness&nbsp;Signature}", " ", $surgery_consent_data);
                    $surgery_consent_data = str_ireplace("{SIGNATURE}", "{SIGNATURE}", $surgery_consent_data);
                    $surgery_consent_data = str_ireplace("{ASSISTANT_SURGEON_SIGNATURE}", "{SIGNATURE}", $surgery_consent_data);
                    //END CODE TO ACTIVATE,DEACTIVATE Witness's AND NURSE'S SIGNATURE (AND REPLACE VARIABLES)
                    //END REPLACE FIELD IN PARENTHESIS WITH ACTUAL VALUE 
                    //START SIGNATURE CODE
                    $surgery_consent_data = str_ireplace('<img src="', '<img src="' . getenv('APP_URL') . '/' . getenv('APP_ROOT') . '/', $surgery_consent_data);
                    $surgery_consent_data = str_ireplace("{SIGNATURE}", ' <a href="' . $consent_template_id . "_" . $patient_id . "_" . $pConfirmId . '"><img src="' . getenv('APP_URL').'/'.getenv('APP_ROOT').'/API2/signpad.jpg" id="' . $consent_template_id . "_" . $patient_id . "_" . $pConfirmId . '" width="250px" height="100px" /></a><br />{SIGNATURE}', $surgery_consent_data);
                    $signature_shown = 0;
                    if (strpos($surgery_consent_data, '{SIGNATURE}')) {
                        $signature_shown = 1;
                    }

                    if ($consentMultipleId > 0) {
                        /* $ViewConsentSurgeryRow->signSurgeon1DateTimeFormat = '0000-00-00 00:00:00';
                          $ViewConsentSurgeryRow->signNurseDateTime = '0000-00-00 00:00:00';
                          $ViewConsentSurgeryRow->signWitness1DateTime = '0000-00-00 00:00:00';
                          $ViewConsentSurgeryRow->signSurgeon1Activate='no';
                          $ViewConsentSurgeryRow->signNurseActivate='no';
                          $ViewConsentSurgeryRow->signWitness1Activate='no'; */
                    }
                    if ($chkSignSurgeon1Activate == 'yes') {
                        $stat[] = ['type' => 'Surgeon', 'name' => $ViewConsentSurgeryRow ? ucfirst($ViewConsentSurgeryRow->signSurgeon1FirstName) . ' ' . ucfirst($ViewConsentSurgeryRow->signSurgeon1LastName) : '', 'datetime' => ((isset($ViewConsentSurgeryRow->signSurgeon1DateTimeFormat) && $ViewConsentSurgeryRow->signNurseDateTime != '0000-00-00 00:00:00') ? date("m-d-Y H:i:s", strtotime($ViewConsentSurgeryRow->signSurgeon1DateTimeFormat)) : ''), 'sugn_show_sts' => isset($ViewConsentSurgeryRow->signSurgeon1Activate) ? $ViewConsentSurgeryRow->signSurgeon1Activate : $chkSignSurgeon1Activate];
                    }
                    if ($chkSignNurseActivate == 'yes') {
                        $stat[] = ['type' => 'Nurse', 'name' => $ViewConsentSurgeryRow ? ucfirst($ViewConsentSurgeryRow->signNurseFirstName) . ' ' . ucfirst($ViewConsentSurgeryRow->signNurseLastName) : '', 'datetime' => ((isset($ViewConsentSurgeryRow->signNurseDateTime) && $ViewConsentSurgeryRow->signNurseDateTime != '0000-00-00 00:00:00') ? date("m-d-Y H:i:s", strtotime($ViewConsentSurgeryRow->signNurseDateTime)) : ''), 'sugn_show_sts' => isset($ViewConsentSurgeryRow->signNurseActivate) ? $ViewConsentSurgeryRow->signNurseActivate : $chkSignNurseActivate];
                    }
                    if ($chkSignWitness1Activate == 'yes') {
                        $stat[] = ['type' => 'Witness', 'name' => $ViewConsentSurgeryRow ? ucfirst($ViewConsentSurgeryRow->signWitness1FirstName) . ' ' . ucfirst($ViewConsentSurgeryRow->signWitness1LastName) : '', 'datetime' => ((isset($ViewConsentSurgeryRow->signWitness1DateTime) && $ViewConsentSurgeryRow->signWitness1DateTime != '0000-00-00 00:00:00') ? date("m-d-Y H:i:s", strtotime($ViewConsentSurgeryRow->signWitness1DateTime)) : ''), 'sugn_show_sts' => isset($ViewConsentSurgeryRow->signWitness1Activate) ? $ViewConsentSurgeryRow->signWitness1Activate : $chkSignWitness1Activate];
                    }

                    //END SIGNATURE CODE
                    $status = 1;
                    //GETTING SURGEONS DETAILS
                    $message = ' Consent Details ';
                    $consent_content = $surgery_consent_data;
                }
            }
        }
        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => '',
                    'data' => [],
                    'content' => $consent_content,
                    'content_id' => (int) $consent_template_id,
                    'surgery_content_id' =>(int) $surgery_consent_id,
                    'FormStatusDetails' => $stat,
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

    function saveconsent_template(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $loginUserType = $request->json()->get('UserType') ? $request->json()->get('UserType') : $request->input('UserType');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $electronic_signs = $request->json()->get('electronic_sign') ? $request->json()->get('electronic_sign') : $request->input('electronic_sign');
        $consent_template_id = $request->json()->get('content_id') ? $request->json()->get('content_id') : $request->input('content_id');
        $surgery_consent_id = $request->json()->get('surgery_content_id') ? $request->json()->get('surgery_content_id') : $request->input('surgery_content_id');
        $signStatus = $request->json()->get('signStatus') ? $request->json()->get('signStatus') : $request->input('signStatus');
        $signdate = $request->json()->get('signDate') ? $request->json()->get('signDate') : $request->input('signDate');
        $base64_img = $request->input($consent_template_id . "_" . $patient_id . "_" . $pConfirmId);
        $arrayRecord = [];
        $data = [];
        $status = 0;
        $moveStatus = 0;
        $d = '';
        $andConsentIdQry = '';
        $ampConsentAutoId = '';
        if ($surgery_consent_id > 0) {
            $andConsentIdQry = ' AND surgery_consent_id=' . $surgery_consent_id;
        }
        // $electronic_signs=json_encode($electronic_signs);

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
                $message = 'Data saved successfully !';
                //  $user
                //SAVE RECORD	
                if ($consent_template_id > 0) {
                    $sig_count = 1;
                    $modifyFormStatus = '';
                    //START
                    $getinstructionSheetQry = "select * from `consent_multiple_form` where  confirmation_id = '" . $pConfirmId . "' AND consent_template_id='" . $consent_template_id . "' AND consent_template_id!='0'" . $andConsentIdQry;
                    $getinstructionSheetRes = DB::selectone($getinstructionSheetQry);

                    if ($getinstructionSheetRes && $getinstructionSheetRes->surgery_consent_data != '') {
                        $instruction_sheet_data = stripslashes($getinstructionSheetRes->surgery_consent_data);
                        $modifyFormStatus = $getinstructionSheetRes->form_status;
                    } else {
                        $getInstructionSheetAdminDetails = $this->getRowRecord('consent_forms_template', 'consent_id', $consent_template_id);
                        if ($getInstructionSheetAdminDetails) {
                            $instruction_sheet_data = stripslashes($getInstructionSheetAdminDetails->consent_data);
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
                        $imgSrc = $this->convertBase64(trim($base64_img), $consent_template_id, $patient_id, $pConfirmId);
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
                    if (!empty($electronic_signs)) {
                        foreach ($electronic_signs as $electronic_signss) {
                            if (strtolower($electronic_signss->type) == 'surgeon' && strtolower($loginUserType) == 'surgeon') {
                                $arrayRecord['signSurgeon1Id'] = $userId;
                                $arrayRecord['signSurgeon1FirstName'] = $users->fname;
                                $arrayRecord['signSurgeon1MiddleName'] = $users->mname;
                                $arrayRecord['signSurgeon1LastName'] = $users->lname;
                                $arrayRecord['signSurgeon1Status'] = $electronic_signss->sign_sts; //$electronic_sign['surgeon']['status'];
                                $arrayRecord['signSurgeon1DateTime'] = ($electronic_signss->date_is != '') ? date("Y-m-d H:i:s", strtotime($electronic_signss->date_is)) : Date("Y-m-d H:i:s"); //$electronic_sign['surgeon']['dttime'];
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
                                $arrayRecord['signNurseDateTime'] = ($electronic_signss->date_is != '') ? date("Y-m-d H:i:s", strtotime($electronic_signss->date_is)) : Date("Y-m-d H:i:s"); //$electronic_sign['surgeon']['dttime'];
                                $arrayRecord['signNurseActivate'] = "yes";
                            }
                            if (strtolower($electronic_signss->type) == 'witness' && strtolower($loginUserType) == 'witness') {
                                $hidd_signWitness1Activate = 'yes';
                                $arrayRecord['signWitness1Id'] = $userId;
                                $arrayRecord['signWitness1FirstName'] = $users->fname;
                                $arrayRecord['signWitness1MiddleName'] = $users->mname;
                                $arrayRecord['signWitness1LastName'] = $users->lname;
                                $arrayRecord['signWitness1Status'] = $electronic_signss->sign_sts;
                                $arrayRecord['signWitness1DateTime'] = ($electronic_signss->date_is != '') ? date("Y-m-d H:i:s", strtotime($electronic_signss->date_is)) : Date("Y-m-d H:i:s"); //$electronic_sign['surgeon']['dttime'];
                                $arrayRecord['signWitness1Activate'] = "yes";
                            }
                        }
                    }
                    //END CODE TO SET FORM STATUS 
                    //MAKE AUDIT STATUS CRATED OR MODIFIED
                    unset($arrayStatusRecord);
                    /*
                     * surgery_consent_data = '".$_POST["surgery_consent_data"]."',
                      surgery_consent_sign = '".$_POST["consentSurgery_patient_sign"]."',
                      form_status ='".$form_status."',
                      surgery_consent_name='".addslashes($_POST["surgery_consent_name"])."',
                      surgery_consent_alias='".addslashes($_POST["surgery_consent_alias"])."',
                      consent_template_id='".$_POST["consentMultipleId"]."',
                      sigStatus='".$_POST["sigStatus"]."',
                      signSurgeon1Activate='".$_POST["hidd_signSurgeon1Activate"]."',
                      signNurseActivate='".$_POST["hidd_signNurseActivate"]."',
                      signAnesthesia1Activate='".$_POST["hidd_signAnesthesia1Activate"]."',
                      signWitness1Activate='".$_POST["hidd_signWitness1Activate"]."',
                      confirmation_id='".$_REQUEST["pConfId"]."'
                     */
                    $ViewConsentTemplateQry = "select * from `consent_forms_template` where  consent_id = '" . $consent_template_id . "'";
                    $ViewConsentTemplateRows = DB::selectone($ViewConsentTemplateQry); // or die(imw_error());
                    $consent_category_id = $ViewConsentTemplateRows->consent_category_id;
                    $arrayRecord['signSurgeon1Activate'] = $hidd_signSurgeon1Activate;
                    $arrayRecord['signNurseActivate'] = $hidd_signNurseActivate;
                    $arrayRecord['signWitness1Activate'] = $hidd_signWitness1Activate;
                    $arrayRecord['consent_template_id'] = $consent_template_id;

                    //MAKE AUDIT STATUS CRATED OR MODIFIED
                    $arrayRecord['surgery_consent_data'] = addslashes($instruction_sheet_data);
                    $arrayRecord['form_status'] = $form_status;
                    $arrayRecord['consent_template_id'] = $consent_template_id;
                    $arrayRecord['confirmation_id'] = $pConfirmId;
                    $arrayRecord['consent_category_id'] = $consent_category_id;
                    if (!$surgery_consent_id) {
                        $surgery_consent_id = DB::table('consent_multiple_form')->insertGetId($arrayRecord);
                    } else {
                        DB::table('consent_multiple_form')->where('surgery_consent_id', $surgery_consent_id)->update($arrayRecord);
                    }
                    //CODE START TO SET AUDIT STATUS AFTER SAVE
                    unset($conditionArr);
                    $conditionArr['confirmation_id'] = $pConfirmId;
                    $conditionArr['form_name'] = 'consent_multiple_form';
                    $conditionArr['status'] = 'created';
                    $chkAuditStatus = $this->getMultiChkArrayRecords('chartnotes_change_audit_tbl', $conditionArr);

                    $arrayStatusRecord['user_id'] = $userId;
                    $arrayStatusRecord['patient_id'] = $patient_id;
                    $arrayStatusRecord['confirmation_id'] = $pConfirmId;
                    $arrayStatusRecord['action_date_time'] = date('Y-m-d H:i:s');
                    if ($chkAuditStatus) {
                        //MAKE AUDIT STATUS MODIFIED
                        $arrayStatusRecord['status'] = 'modified';
                    } else {
                        //MAKE AUDIT STATUS CREATED
                        $arrayStatusRecord['status'] = 'created';
                    }
                    DB::table('chartnotes_change_audit_tbl')->insertGetId($arrayStatusRecord);
                    //CODE TO CHECK SURGEON ALL SIGNATURE AND SET VALUE IN STUB TABLE
                    $chartSignedBySurgeon = $this->chkSurgeonSignNew($pConfirmId);
                    $updateStubTblQry = "UPDATE stub_tbl SET chartSignedBySurgeon='" . $chartSignedBySurgeon . "' WHERE patient_confirmation_id='" . $pConfirmId . "'";
                    $updateStubTblRes = DB::select($updateStubTblQry);
                    //END CODE TO CHECK SURGEON SIGNATURE AND SET VALUE IN STUB TABLE
                    //CODE TO CHECK ANESTHESIOLOGIST ALL SIGNATURE AND SET VALUE IN STUB TABLE
                    $chartSignedByAnes = $this->chkAnesSignNew($pConfirmId);
                    $updateAnesStubTblQry = "UPDATE stub_tbl SET chartSignedByAnes='" . $chartSignedByAnes . "' WHERE patient_confirmation_id='" . $pConfirmId . "'";
                    $updateAnesStubTblRes = DB::select($updateAnesStubTblQry); // or die(imw_error());
                    //END CODE TO CHECK ANESTHESIOLOGIST SIGNATURE AND SET VALUE IN STUB TABLE
                    //CODE TO CHECK NURSE ALL SIGNATURE AND SET VALUE IN STUB TABLE
                    $chartSignedByNurse = $this->chkNurseSignNew($pConfirmId);
                    $updateNurseStubTblQry = "UPDATE stub_tbl SET chartSignedByNurse='" . $chartSignedByNurse . "' WHERE patient_confirmation_id='" . $pConfirmId . "'";
                    $updateNurseStubTblRes = DB::select($updateNurseStubTblQry); // or die(imw_error());
                    //END CODE TO CHECK NURSE SIGNATURE AND SET VALUE IN STUB TABLE
                    $data = $instruction_sheet_data;
                    $message = 'Data saved successfully !';
                    //CODE END TO SET AUDIT STATUS AFTER SAVE
                } //Save Code
            }
        }

        return response()->json([
                    'status' => 1,
                    'message' => $message,
                    'requiredStatus' => $instruction_sheet_data,
                    'data' => '',
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

  

}
