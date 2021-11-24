<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Controller extends BaseController {

    //
    protected function checkToken($userToken) {
        $qery = "SELECT user_id FROM user_log where user_token='" . $userToken . "' and active=1";
        $res = DB::selectone($qery);
        if ($res) {
            return $res->user_id;
        }
        return 0;
    }

    function getFullDtTmFormat($dtTimeValue = '') {
        if (!trim($dtTimeValue) || trim($dtTimeValue) == "0000-00-00 00:00:00" || trim($dtTimeValue) == "0000-00-00 00:00") {
            return;
        }
        $dtTimeValueShow = date("m-d-Y h:i A", strtotime($dtTimeValue));
        if (getenv("SHOW_MILITARY_TIME") == "YES") {
            $dtTimeValueShow = date("m-d-Y H:i", strtotime($dtTimeValue));
        }
        return $dtTimeValueShow;
    }

    //FUNCTION TO CHECK ALL SIGN OF SURGEON
    public function chkSurgeonSignNew($chkSurgeonConfId) {
        $chkSurgeonSignColor = 'green';

//START CODE TO CHECK CATEGORY ID (2->Laser Procedure) OF ASSIGNED PROCEDURE
        $getLaserConfirmationDetailQry = "SELECT PC.* FROM `patientconfirmation` PC WHERE  PC.patientConfirmationId='" . $chkSurgeonConfId . "' ";
        $getLaserConfirmationDetailRes = DB::select($getLaserConfirmationDetailQry); // or die('Error Found at line no. ' . (__LINE__) . ': ' . imw_error());

        if ($getLaserConfirmationDetailRes) {

            $laserConfirmationPrimaryProcedureId = $getLaserConfirmationDetailRes[0]->patient_primary_procedure_id;
            $laserCatIdDetailQry = "SELECT * FROM `procedures` WHERE procedureId='" . $laserConfirmationPrimaryProcedureId . "'";
            $laserCatIdDetailRes = DB::select($laserCatIdDetailQry); // or die('Error Found at line no. ' . (__LINE__) . ': ' . imw_error());

            if ($laserCatIdDetailRes) {

                $laserCatId = $laserCatIdDetailRes[0]->catId;
            }

            // Start Code to check if procedure is injection/Misc
            $primary_procedure_is_inj_misc = $getLaserConfirmationDetailRes[0]->prim_proc_is_misc;
            if ($laserCatId <> '2') {
                if ($primary_procedure_is_inj_misc == '') {
                    $chkProcedureCatQry = "Select isMisc,isInj From procedurescategory Where proceduresCategoryId = '" . $laserCatId . "'  ";
                    $chkProcedureCatSql = DB::select($chkProcedureCatQry); // or die('Error Found at line no. ' . (__LINE__) . ': ' . imw_error());
                    $primary_procedure_is_inj_misc = '';
                    if ($chkProcedureCatSql[0]->isInj)
                        $primary_procedure_is_inj_misc = 'injection';
                    elseif ($chkProcedureCatSql[0]->isMisc)
                        $primary_procedure_is_inj_misc = 'misc';
                }
            }else {
                $primary_procedure_is_inj_misc = '';
            }
            //End Code to check if procedure is injection/Misc
        }
//END CODE TO CHECK CATEGORY ID (2->Laser Procedure) OF ASSIGNED PROCEDURE


        $chkSignArr = array('preopphysicianorders', 'postopphysicianorders', 'operativereport', 'dischargesummarysheet');

        if ($laserCatId == '2') { //IF CATEGORY OF PROCEDURE IS 'LASER PROCEDURE' THEN
            $chkLaserSignArr = array('laser_procedure_patient_table', 'dischargesummarysheet');
            foreach ($chkLaserSignArr as $chkLaserSignArrTableName) {
                $chkAndLaserChartRecordQry = '';
                if ($chkLaserSignArrTableName == 'laser_procedure_patient_table') {//CHECK VERIFIED BY SURGEON ALSO
                    $chkAndLaserChartRecordQry = " AND verified_surgeon_Id!='0' AND verified_surgeon_Id!=''";
                }
                $chkLaserChartRecordQry = "SELECT * FROM $chkLaserSignArrTableName WHERE confirmation_id='" . $chkSurgeonConfId . "' AND signSurgeon1Id!='0' AND signSurgeon1Id!='' $chkAndLaserChartRecordQry";
                $chkLaserChartRecordRes = DB::select($chkLaserChartRecordQry); // or die($chkPatientChartRecordQry . 'Error Found at line no. ' . (__LINE__) . ': ' . imw_error());
                if ($chkLaserChartRecordRes) {
                    $chkLaserChartSignSurgeon1Id = $chkLaserChartRecordRes[0]->signSurgeon1Id;
                    $chkLaserChartFormStatus = $chkLaserChartRecordRes[0]->form_status;
                    if (($chkLaserChartFormStatus == 'not completed' || $chkLaserChartFormStatus == '') && $chkSurgeonSignColor == '') {
                        $chkSurgeonSignColor = '';
                    } else if ($chkLaserChartFormStatus == 'not completed' || $chkLaserChartFormStatus == '') {
                        $chkSurgeonSignColor = 'red';
                    } else if ($chkLaserChartFormStatus == 'completed' && $chkSurgeonSignColor == '') {
                        $chkSurgeonSignColor = '';
                    }
                } else {
                    $chkSurgeonSignColor = '';
                }
            }
        } else if ($laserCatId <> '2' && $primary_procedure_is_inj_misc) { //ELSE
            $chkInjectionSignArr = array('injection', 'operativereport', 'dischargesummarysheet');

            foreach ($chkInjectionSignArr as $chkSignArrTableName) {
                $signUserconfirmation_id = 'confirmation_id';
                //CHECK IF PATIENT RECORD EXIST IN DATABASE OR NOT-->(SET  SURGEON SIGN COLOR FOR STUB TABLE)
                $chkPatientChartRecordQry = "SELECT * FROM $chkSignArrTableName WHERE $signUserconfirmation_id='" . $chkSurgeonConfId . "' AND signSurgeon1Id!='0' AND signSurgeon1Id!=''";
                $chkPatientChartRecordRes = DB::select($chkPatientChartRecordQry); // or die($chkPatientChartRecordQry . 'Error Found at line no. ' . (__LINE__) . ': ' . imw_error());


                $chkPatientChartFormStatus = '';
                if ($chkPatientChartRecordRes) {
                    $chkPatientChartFormStatus = $chkPatientChartRecordRes[0]->form_status;
                    if (($chkPatientChartFormStatus == 'not completed' || $chkPatientChartFormStatus == '') && $chkSurgeonSignColor == '') {
                        $chkSurgeonSignColor = '';
                    } else if ($chkPatientChartFormStatus == 'not completed' || $chkPatientChartFormStatus == '') {
                        $chkSurgeonSignColor = 'red';
                    } else if ($chkPatientChartFormStatus == 'completed' && $chkSurgeonSignColor == '') {
                        $chkSurgeonSignColor = '';
                    }
                } else {
                    $chkSurgeonSignColor = '';
                }
                //END CHECK IF PATIENT RECORD EXIST IN DATABASE OR NOT-->(SET  SURGEON SIGN COLOR FOR STUB TABLE)
            }
        } else { //ELSE
            foreach ($chkSignArr as $chkSignArrTableName) {

                if ($chkSignArrTableName == "preopphysicianorders" || $chkSignArrTableName == "postopphysicianorders") {
                    $signUserconfirmation_id = 'patient_confirmation_id';
                } else if ($chkSignArrTableName == "operativereport" || $chkSignArrTableName == "dischargesummarysheet") {
                    $signUserconfirmation_id = 'confirmation_id';
                }
                //CHECK IF PATIENT RECORD EXIST IN DATABASE OR NOT-->(SET  SURGEON SIGN COLOR FOR STUB TABLE)

                $chkPatientChartRecordQry = "SELECT * FROM $chkSignArrTableName WHERE $signUserconfirmation_id='" . $chkSurgeonConfId . "' AND signSurgeon1Id!='0' AND signSurgeon1Id!=''";
                $chkPatientChartRecordRes = DB::select($chkPatientChartRecordQry); // or die($chkPatientChartRecordQry . 'Error Found at line no. ' . (__LINE__) . ': ' . imw_error());


                $chkPatientChartFormStatus = '';
                if ($chkPatientChartRecordRes) {
                    $chkPatientChartFormStatus = $chkPatientChartRecordRes[0]->form_status;
                    if (($chkPatientChartFormStatus == 'not completed' || $chkPatientChartFormStatus == '') && $chkSurgeonSignColor == '') {
                        $chkSurgeonSignColor = '';
                    } else if ($chkPatientChartFormStatus == 'not completed' || $chkPatientChartFormStatus == '') {
                        $chkSurgeonSignColor = 'red';
                    } else if ($chkPatientChartFormStatus == 'completed' && $chkSurgeonSignColor == '') {
                        $chkSurgeonSignColor = '';
                    }
                } else {
                    $chkSurgeonSignColor = '';
                }
                //END CHECK IF PATIENT RECORD EXIST IN DATABASE OR NOT-->(SET  SURGEON SIGN COLOR FOR STUB TABLE)
            }

            //CHECK IF SURGEON VARIFIED THE OPERATING ROOM RECORD OR NOT  
            if ($chkSurgeonSignColor != '') {
                $chkOproomSurgeonCheckMarkQry = "SELECT * FROM operatingroomrecords WHERE confirmation_id='" . $chkSurgeonConfId . "' AND verifiedbySurgeon='Yes'";
                $chkOproomSurgeonCheckMarkRes = DB::select($chkOproomSurgeonCheckMarkQry); // or die('Error Found at line no. ' . (__LINE__) . ': ' . imw_error());
                if ($chkOproomSurgeonCheckMarkRes) {
                    $chkOproomSignVerifybySurgeon = $chkOproomSurgeonCheckMarkRes[0]->verifiedbySurgeon;
                    $chkOproomSignFormStatus = $chkOproomSurgeonCheckMarkRes[0]->form_status;
                    if ($chkOproomSignFormStatus == 'not completed') {
                        $chkSurgeonSignColor = 'red';
                    }
                } else {
                    $chkSurgeonSignColor = '';
                }
            }
            //END CHECK IF SURGEON VARIFIED THE OPERATING ROOM RECORD OR NOT
        }

//START CHECK SURGEON SIGN FOR H & P (HEALTH AND PHYSICAL) CLEARANCE FORM (OPTIONAL) 
        $dirName = 'H&P';
        $scanDirQry = "Select sut.scan_upload_id From  scan_upload_tbl sut, scan_documents sd WHERE sd.confirmation_id = '" . $chkSurgeonConfId . "' And sut.confirmation_id 	= '" . $chkSurgeonConfId . "' And sd.document_name = '" . $dirName . "' And sd.document_id = sut.document_id Order By sd.document_id, sut.document_id ";
        $scanDirSql = DB::select($scanDirQry); // or die('Error Found in function ' . (__FUNCTION__) . ' at line no. ' . (__LINE__) . imw_error());
        $ekgHpCount = $scanDirSql;

        if (!$ekgHpCount) {
            if ($chkSurgeonSignColor != '') {
                $chkSurgeonSignHistoryPhysicalQry = "SELECT * FROM history_physicial_clearance WHERE confirmation_id='" . $chkSurgeonConfId . "'";
                $chkSurgeonSignHistoryPhysicalRes = DB::select($chkSurgeonSignHistoryPhysicalQry); // or die($chkSurgeonSignHistoryPhysicalQry . 'Error Found at line no. ' . (__LINE__) . ': ' . imw_error());
                if ($chkSurgeonSignHistoryPhysicalRes) {
                    $chkHistoryPhysicalSurgeonFormStatus = $chkSurgeonSignHistoryPhysicalRes[0]->form_status;
                    $chkHistoryPhysicalSignSurgeon1Id = $chkSurgeonSignHistoryPhysicalRes[0]->signSurgeon1Id;
                    if ($chkHistoryPhysicalSignSurgeon1Id && ($chkHistoryPhysicalSurgeonFormStatus == 'not completed')) {// CHECK ONLY IF RECORD IS SAVED ATLEAST ONCE
                        $chkSurgeonSignColor = 'red';
                    } else if (!$chkHistoryPhysicalSignSurgeon1Id && ($chkHistoryPhysicalSurgeonFormStatus == 'not completed' || $chkHistoryPhysicalSurgeonFormStatus == 'completed')) {
                        $chkSurgeonSignColor = '';
                    }
                }
            }
        }
        //END CHECK SURGEON SIGN FOR H & P (HEALTH AND PHYSICAL) CLEARANCE FORM (OPTIONAL)
// Start Common Checking of surgeon signature in Transfer & Followup Chart
        if ($chkSurgeonSignColor != '') {
            $chkSurgeonSignTFQry = "SELECT * FROM transfer_followups WHERE confirmation_id='" . $chkSurgeonConfId . "'";
            $chkSurgeonSignTFRes = DB::select($chkSurgeonSignTFQry); // or die($chkSurgeonSignTFQry . 'Error Found at line no. ' . (__LINE__) . ': ' . imw_error());
            if ($chkSurgeonSignTFRes) {
                $chkTFSurgeonFormStatus = $chkSurgeonSignTFRes[0]->form_status;
                $chkTFSignSurgeon1Id = $chkSurgeonSignTFRes[0]->signSurgeon1Id;
                if ($chkTFSignSurgeon1Id && ($chkTFSurgeonFormStatus == 'not completed')) {// CHECK ONLY IF RECORD IS SAVED ATLEAST ONCE
                    $chkSurgeonSignColor = 'red';
                } else if (!$chkTFSignSurgeon1Id && ($chkTFSurgeonFormStatus == 'not completed' || $chkTFSurgeonFormStatus == 'completed')) {
                    $chkSurgeonSignColor = '';
                }
            }
        }
// End  Common Checking of surgeon signature in Transfer & Followup Chart
//START COMMON CHECKING OF 'SURGEON SIGNATURE' ON CONSENT FORMS FOR LASER AND OTHER CHARTNOTES
        if ($chkSurgeonSignColor != '') {
            $chkConsentSignAllQry = "SELECT * FROM consent_multiple_form WHERE confirmation_id='" . $chkSurgeonConfId . "' AND signSurgeon1Activate='yes' AND consent_purge_status!='true' AND (form_status ='completed' OR form_status ='not completed') ";
            $chkConsentSignAllRes = DB::select($chkConsentSignAllQry); // or die($chkConsentSignAllQry . 'Error Found at line no. ' . (__LINE__) . ': ' . imw_error());
            if ($chkConsentSignAllRes) {
                $chkConsentSignSurgeon1Activate = '';
                foreach ($chkConsentSignAllRes as $chkConsentSignRow) {
                    $chkConsentSignSurgeon1Activate = $chkConsentSignRow->signSurgeon1Activate;
                    $chkConsentSignSurgeon1Id = $chkConsentSignRow->signSurgeon1Id;
                    $chkConsentFrmFormStatus = $chkConsentSignRow->form_status;

                    if ($chkConsentSignSurgeon1Id == '0' || $chkConsentSignSurgeon1Id == '') {
                        $chkSurgeonSignColor = '';
                    } else if (($chkConsentSignSurgeon1Id != '0' && $chkConsentSignSurgeon1Id != '') && $chkConsentFrmFormStatus == 'completed' && $chkSurgeonSignColor == '') {
                        $chkSurgeonSignColor = '';
                    } else if (($chkConsentSignSurgeon1Id != '0' && $chkConsentSignSurgeon1Id != '') && $chkConsentFrmFormStatus == 'not completed' && $chkSurgeonSignColor == '') {
                        $chkSurgeonSignColor = '';
                    } else if (($chkConsentSignSurgeon1Id != '0' && $chkConsentSignSurgeon1Id != '') && $chkConsentFrmFormStatus == 'completed' && $chkSurgeonSignColor == 'red') {
                        $chkSurgeonSignColor = 'red';
                    } else if (($chkConsentSignSurgeon1Id != '0' && $chkConsentSignSurgeon1Id != '') && $chkConsentFrmFormStatus == 'not completed') {
                        $chkSurgeonSignColor = 'red';
                    }
                }
            }
        }
//END COMMON CHECKING OF 'SURGEON SIGNATURE' ON CONSENT FORMS FOR LASER AND OTHER CHARTNOTES

        return $chkSurgeonSignColor;
    }

    //END FUNCTION TO CHECK ALL SIGN OF SURGEON
    //FUNCTION TO CHECK ALL SIGN OF Anes
    public function chkAnesSignNew($chkAnesConfId) {
        $chkSignAnesArr = array('operatingroomrecords');
        $chkAnesSignColor = 'green';
        $chkAnesRecordExist = false;

//START CODE TO CHECK CATEGORY ID (2->Laser Procedure) OF ASSIGNED PROCEDURE
        $getAnesLaserConfirmationDetailQry = "SELECT patient_primary_procedure_id,anes_NA,prim_proc_is_misc FROM `patientconfirmation` WHERE  patientConfirmationId='" . $chkAnesConfId . "'";
        $getAnesLaserConfirmationDetailRes = DB::select($getAnesLaserConfirmationDetailQry); // or die(imw_error());
        if ($getAnesLaserConfirmationDetailRes) {
            $laserAnesConfirmationPrimaryProcedureId = $getAnesLaserConfirmationDetailRes[0]->patient_primary_procedure_id;
            $anesNA = $getAnesLaserConfirmationDetailRes[0]->anes_NA;
            $laserCatIdAnesDetailQry = "SELECT * FROM `procedures` WHERE procedureId='" . $laserAnesConfirmationPrimaryProcedureId . "'";
            $laserCatIdAnesDetailRes = DB::select($laserCatIdAnesDetailQry); // or die('Error Found at line no. ' . (__LINE__) . ': ' . imw_error());
            if ($laserCatIdAnesDetailRes > 0) {
                $laserCatIdAnes = $laserCatIdAnesDetailRes[0]->catId;
            }

            // Start Code to check if procedure is injection/Misc
            $primary_procedure_is_inj_misc = $getAnesLaserConfirmationDetailRes[0]->prim_proc_is_misc;
            if ($laserCatIdAnes <> '2') {
                if ($primary_procedure_is_inj_misc == '') {
                    $chkProcedureCatQry = "Select isMisc, isInj From procedurescategory Where proceduresCategoryId = '" . $laserCatIdAnes . "'  ";
                    $chkProcedureCatSql = DB::select($chkProcedureCatQry); // or die('Error Found at line no. ' . (__LINE__) . ': ' . imw_error());


                    $primary_procedure_is_inj_misc = '';
                    if ($chkProcedureCatSql[0]->isInj)
                        $primary_procedure_is_inj_misc = 'injection';
                    elseif ($chkProcedureCatSql[0]->isMisc)
                        $primary_procedure_is_inj_misc = 'misc';
                }
            }else {
                $primary_procedure_is_inj_misc = '';
            }
            //End Code to check if procedure is injection/Misc
        }
//END CODE TO CHECK CATEGORY ID (2->Laser Procedure) OF ASSIGNED PROCEDURE

        if ($laserCatIdAnes == '2' || $anesNA == 'Yes') { //IF CATEGORY OF PROCEDURE IS 'LASER PROCEDURE' OR IF ANES. NOT REQUIRED THEN  
            $chkAnesSignColor = ''; //NOT REQUIRED FOR ANESTHESIOLOGIST
        } elseif (($laserCatIdAnes <> '2' && $primary_procedure_is_inj_misc) || $anesNA == 'Yes') { //IF CATEGORY OF PROCEDURE IS 'INJECTION PROCEDURE' OR IF ANES. NOT REQUIRED THEN  
            $chkAnesSignColor = ''; //NOT REQUIRED FOR ANESTHESIOLOGIST
        } else { //ELSE	
            foreach ($chkSignAnesArr as $chkSignAnesArrTableName) {

                $chkOproomAnesCheckMarkQry = "SELECT * FROM operatingroomrecords WHERE confirmation_id='" . $chkAnesConfId . "' AND verifiedbyAnesthesiologist='Yes'";
                $chkOproomAnesCheckMarkRes = DB::select($chkOproomAnesCheckMarkQry); // or die('Error Found at line no. ' . (__LINE__) . ': ' . imw_error());


                $chkOproomSignVerifybyAnes = '';
                $chkOproomSignAnesFormStatus = '';

                if ($chkOproomAnesCheckMarkRes) {
                    //$chkAnesRecordExist = true;
                    $chkOproomSignVerifybyAnes = $chkOproomAnesCheckMarkRes[0]->verifiedbyAnesthesiologist;
                    $chkOproomSignAnesFormStatus = $chkOproomAnesCheckMarkRes[0]->form_status;

                    if ($chkOproomSignAnesFormStatus == 'not completed') {
                        $chkAnesSignColor = 'red';
                    }
                } else {
                    $chkAnesSignColor = '';
                }
            }

            //END CHECK IF Anes VERIFIED THE OPERATING ROOM RECORD OR NOT
            //CHECK EITHER OF 'LOCAL ANESTHESIA' OR 'GENERAL ANESTHESIA' REOCRD HAS SIGN OF ANESTHESIOLOGIST (IF NOT THEN SET $chkAnesSignColor TO '')
            if ($chkAnesSignColor != '') {
                $chkSignLocalAnesQry = "SELECT * FROM localanesthesiarecord WHERE confirmation_id='" . $chkAnesConfId . "'";
                $chkSignLocalAnesRes = DB::select($chkSignLocalAnesQry); // or die($chkSignLocalAnesQry . 'Error Found at line no. ' . (__LINE__) . ': ' . imw_error());

                $chkSignGeneralAnesQry = "SELECT * FROM genanesthesiarecord WHERE confirmation_id='" . $chkAnesConfId . "'";
                $chkSignGeneralAnesRes = DB::select($chkSignGeneralAnesQry); // or die($chkSignGeneralAnesQry . 'Error Found at line no. ' . (__LINE__) . ': ' . imw_error());

                if ($chkSignLocalAnesRes || $chkSignGeneralAnesRes) {
                    if ($chkSignLocalAnesRes) {
                        $LocalsignAnesthesia1Id = $chkSignLocalAnesRes[0]->signAnesthesia1Id;
                        $LocalsignAnesthesia2Id = $chkSignLocalAnesRes[0]->signAnesthesia2Id;
                        $LocalsignAnesthesia4Id = $chkSignLocalAnesRes[0]->signAnesthesia4Id;
                        $anes_ScanUploadPath = $chkSignLocalAnesRes[0]->anes_ScanUploadPath;
                        $anes_ScanUpload = $chkSignLocalAnesRes[0]->anes_ScanUpload;
                        $form_status_localAnes = $chkSignLocalAnesRes[0]->form_status;
                        $version_num_local_anes = $chkSignLocalAnesRes[0]->version_num;

                        $allLocalSignAnesthsia = '';
                        $localAnesFormNotInUse = '';

                        if ($version_num_local_anes == '2' || ($version_num_local_anes = 0 && $form_status_localAnes == '')) {
                            if ($anes_ScanUploadPath || $anes_ScanUpload || ($LocalsignAnesthesia1Id && $LocalsignAnesthesia2Id && $LocalsignAnesthesia4Id)) {
                                $allLocalSignAnesthsia = 'true';
                            }
                        } else {
                            if ($anes_ScanUploadPath || $anes_ScanUpload || ($LocalsignAnesthesia1Id && $LocalsignAnesthesia2Id)) {
                                $allLocalSignAnesthsia = 'true';
                            }
                        }

                        if ($allLocalSignAnesthsia == 'true' && ($form_status_localAnes == 'not completed' || $form_status_localAnes == '')) {
                            $chkAnesSignColor = 'red';
                        } else if ($allLocalSignAnesthsia != 'true' && ($form_status_localAnes == 'not completed' || $form_status_localAnes == 'completed')) {
                            $chkAnesSignColor = '';
                        } else if ($allLocalSignAnesthsia != 'true' && $form_status_localAnes != 'completed' && $form_status_localAnes != 'not completed') {
                            $localAnesFormNotInUse = true;
                        }
                    }
                    if ($chkSignLocalAnesRes) {

                        $form_status_GeneralAnes = $chkSignLocalAnesRes[0]->form_status;
                        $GeneralsignAnesthesia1Id = $chkSignLocalAnesRes[0]->signAnesthesia1Id;
                        $GeneralsignAnesthesia2Id = $chkSignLocalAnesRes[0]->signAnesthesia2Id;
                        $GeneralsignAnesVersionNum = $chkSignLocalAnesRes[0]->version_num;

                        $allGeneralSignAnesthsia = '';
                        if ($GeneralsignAnesVersionNum == '2' || ($GeneralsignAnesVersionNum = 0 && $form_status_GeneralAnes == '')) {
                            if ($GeneralsignAnesthesia1Id && $GeneralsignAnesthesia2Id) {
                                $allGeneralSignAnesthsia = 'true';
                            }
                        } else {
                            if ($GeneralsignAnesthesia1Id) {
                                $allGeneralSignAnesthsia = 'true';
                            }
                        }


                        if ($allGeneralSignAnesthsia == 'true' && ($form_status_GeneralAnes == 'not completed' || $form_status_GeneralAnes == '')) {
                            $chkAnesSignColor = 'red';
                        } else if ($allGeneralSignAnesthsia != 'true' && ($form_status_GeneralAnes == 'not completed' || $form_status_GeneralAnes == 'completed')) {
                            $chkAnesSignColor = '';
                        } else if ($allGeneralSignAnesthsia != 'true' && $form_status_GeneralAnes != 'completed' && $form_status_GeneralAnes != 'not completed' && $localAnesFormNotInUse == true) {
                            $chkAnesSignColor = '';
                        }
                    }
                } else {
                    $chkAnesSignColor = '';
                }
            }
        }

//START COMMON CHECK Anesthesiologist SIGN FOR H & P (HEALTH AND PHYSICAL) CLEARANCE FORM (OPTIONAL) 
        $dirName = 'H&P';
        $scanDirQry = "Select sut.scan_upload_id From  scan_upload_tbl sut, scan_documents sd WHERE sd.confirmation_id = '" . $chkAnesConfId . "' And sut.confirmation_id 	= '" . $chkAnesConfId . "' And sd.document_name = '" . $dirName . "' And sd.document_id = sut.document_id Order By sd.document_id, sut.document_id ";
        $scanDirSql = DB::select($scanDirQry); // or die('Error Found in function ' . (__FUNCTION__) . ' at line no. ' . (__LINE__) . imw_error());
        $ekgHpCount = $scanDirSql;

        if (!$scanDirSql) {
            if ($chkAnesSignColor != '') {
                $chkAnesthesiaSignHistoryPhysicalQry = "SELECT * FROM history_physicial_clearance WHERE confirmation_id='" . $chkAnesConfId . "'";
                $chkAnesthesiaSignHistoryPhysicalRes = DB::select($chkAnesthesiaSignHistoryPhysicalQry); // or die($chkAnesthesiaSignHistoryPhysicalQry . 'Error Found at line no. ' . (__LINE__) . ': ' . imw_error());
                if ($chkAnesthesiaSignHistoryPhysicalRes) {
                    $chkHistoryPhysicalAnesthesiaFormStatus = $chkAnesthesiaSignHistoryPhysicalRes[0]->form_status;
                    $chkHistoryPhysicalSignAnesthesia1Id = $chkAnesthesiaSignHistoryPhysicalRes[0]->signAnesthesia1Id;
                    if ($chkHistoryPhysicalSignAnesthesia1Id && ($chkHistoryPhysicalAnesthesiaFormStatus == 'not completed')) {// CHECK ONLY IF RECORD IS SAVED ATLEAST ONCE
                        $chkAnesSignColor = 'red';
                    } else if (!$chkHistoryPhysicalSignAnesthesia1Id && ($chkHistoryPhysicalAnesthesiaFormStatus == 'not completed' || $chkHistoryPhysicalAnesthesiaFormStatus == 'completed')) {
                        $chkAnesSignColor = '';
                    }
                }
            }
        }
//END CHECK SURGEON SIGN FOR H & P (HEALTH AND PHYSICAL) CLEARANCE FORM (OPTIONAL)
//START COMMON CHECKING OF 'ANESTHESIOLOGIST SIGNATURE' ON CONSENT FORMS FOR LASER AND OTHER CHARTNOTES
        if ($chkAnesSignColor != '') {
            $chkConsentSignAllAnesthesiaQry = "SELECT * FROM consent_multiple_form WHERE confirmation_id='" . $chkAnesConfId . "' AND signAnesthesia1Activate='yes' AND consent_purge_status!='true' AND (form_status ='completed' OR form_status ='not completed') ";
            $chkConsentSignAllAnesthesiaRes = DB::select($chkConsentSignAllAnesthesiaQry); // or die($chkConsentSignAllAnesthesiaQry . 'Error Found at line no. ' . (__LINE__) . ': ' . imw_error());
            if ($chkConsentSignAllAnesthesiaRes) {
                $chkConsentSignAnesthesia1Activate = '';
                foreach ($chkConsentSignAllAnesthesiaRes as $chkConsentSignAnesthesiaRow) {
                    $chkConsentSignAnesthesia1Activate = $chkConsentSignAnesthesiaRow->signAnesthesia1Activate;
                    $chkConsentSignAnesthesia1Id = $chkConsentSignAnesthesiaRow->signAnesthesia1Id;
                    $chkConsentFrmFormAnesthesiaStatus = $chkConsentSignAnesthesiaRow->form_status;

                    if ($chkConsentSignAnesthesia1Id == '0' || $chkConsentSignAnesthesia1Id == '') {
                        $chkAnesSignColor = '';
                    } else if (($chkConsentSignAnesthesia1Id != '0' && $chkConsentSignAnesthesia1Id != '') && $chkConsentFrmFormAnesthesiaStatus == 'completed' && $chkAnesSignColor == '') {
                        $chkAnesSignColor = '';
                    } else if (($chkConsentSignAnesthesia1Id != '0' && $chkConsentSignAnesthesia1Id != '') && $chkConsentFrmFormAnesthesiaStatus == 'not completed' && $chkAnesSignColor == '') {
                        $chkAnesSignColor = '';
                    } else if (($chkConsentSignAnesthesia1Id != '0' && $chkConsentSignAnesthesia1Id != '') && $chkConsentFrmFormAnesthesiaStatus == 'completed' && $chkAnesSignColor == 'red') {
                        $chkAnesSignColor = 'red';
                    } else if (($chkConsentSignAnesthesia1Id != '0' && $chkConsentSignAnesthesia1Id != '') && $chkConsentFrmFormAnesthesiaStatus == 'not completed') {
                        $chkAnesSignColor = 'red';
                    }
                }
            }
        }
//END COMMON CHECKING OF 'ANESTHESIOLOGIST SIGNATURE' ON CONSENT FORMS FOR LASER AND OTHER CHARTNOTES

        return $chkAnesSignColor;
    }

//END FUNCTION TO CHECK ALL SIGN OF Anes
    public function chkNurseSignNew($chkNurseConfId) {
        $chkSignNurseArr = array('preopnursingrecord', 'postopnursingrecord', 'preopphysicianorders', 'postopphysicianorders');
        $chkNurseSignColor = 'green';
        $chkNurseRecordExist = false;


//START CODE TO CHECK CATEGORY ID (2->Laser Procedure) OF ASSIGNED PROCEDURE
        $getNurseLaserConfirmationDetailQry = "SELECT * FROM `patientconfirmation` WHERE  patientConfirmationId='" . $chkNurseConfId . "'";
        $getNurseLaserConfirmationDetailRes = DB::select($getNurseLaserConfirmationDetailQry); // or die('Error Found at line no. ' . (__LINE__) . ': ' . imw_error());

        if ($getNurseLaserConfirmationDetailRes) {

            $laserNurseConfirmationPrimaryProcedureId = $getNurseLaserConfirmationDetailRes[0]->patient_primary_procedure_id;
            $laserCatIdNurseDetailQry = "SELECT * FROM `procedures` WHERE procedureId='" . $laserNurseConfirmationPrimaryProcedureId . "'";
            $laserCatIdNurseDetailRes = DB::select($laserCatIdNurseDetailQry); // or die('Error Found at line no. ' . (__LINE__) . ': ' . imw_error());

            if ($laserCatIdNurseDetailRes) {
                $laserCatIdNurse = $laserCatIdNurseDetailRes[0]->catId;
            }

            // Start Code to check if procedure is injection/Misc
            $primary_procedure_is_inj_misc = $getNurseLaserConfirmationDetailRes[0]->prim_proc_is_misc;
            if ($laserCatIdNurse <> '2') {
                if ($primary_procedure_is_inj_misc == '') {
                    $chkProcedureCatQry = "Select isMisc, isInj From procedurescategory Where proceduresCategoryId = '" . $laserCatIdNurse . "'  ";
                    $chkProcedureCatSql = DB::select($chkProcedureCatQry); // or die('Error Found at line no. ' . (__LINE__) . ': ' . imw_error());
                    $primary_procedure_is_inj_misc = '';
                    if ($chkProcedureCatSql[0]->isInj)
                        $primary_procedure_is_inj_misc = 'injection';
                    elseif ($chkProcedureCatSql[0]->isMisc)
                        $primary_procedure_is_inj_misc = 'misc';
                }
            }else {
                $primary_procedure_is_inj_misc = '';
            }
            //End Code to check if procedure is injection/Misc
        }
//END CODE TO CHECK CATEGORY ID (2->Laser Procedure) OF ASSIGNED PROCEDURE

        if ($laserCatIdNurse == '2') { //IF CATEGORY OF PROCEDURE IS 'LASER PROCEDURE' THEN (CHECK SIGN AND VERIFIED BY NURSE IN QUERY)
            $chkLaserChartNurseRecordQry = "SELECT * FROM laser_procedure_patient_table WHERE confirmation_id='" . $chkNurseConfId . "' AND signNurseId!='0' AND signNurseId!='' AND verified_nurse_name!=''";
            $chkLaserChartNurseRecordRes = DB::select($chkLaserChartNurseRecordQry); // or die($chkLaserChartNurseRecordQry . 'Error Found at line no. ' . (__LINE__) . ': ' . imw_error());

            if ($chkLaserChartNurseRecordRes) {
                $chkLaserChartNurseSignNurse1Id = $chkLaserChartNurseRecordRes[0]->signNurseId;
                $chkLaserChartNurseFormStatus = $chkLaserChartNurseRecordRes[0]->form_status;
                if ($chkLaserChartNurseFormStatus == 'not completed' || $chkLaserChartNurseFormStatus == '') {
                    $chkNurseSignColor = 'red';
                }
            } else {
                $chkNurseSignColor = '';
            }
        } elseif ($laserCatIdNurse <> '2' && $primary_procedure_is_inj_misc) { //IF CATEGORY OF PROCEDURE IS 'INJECTION PROCEDURE' THEN (CHECK SIGN AND VERIFIED BY NURSE IN QUERY)
            $chkLaserChartNurseRecordQry = "SELECT * FROM injection WHERE confirmation_id='" . $chkNurseConfId . "' AND signNurse2Id!='0' AND signNurse2Id!='' AND  signNurse1Id!='0' AND signNurse1Id!='' ";
            $chkLaserChartNurseRecordRes = DB::selectone($chkLaserChartNurseRecordQry); // or die($chkLaserChartNurseRecordQry . 'Error Found at line no. ' . (__LINE__) . ': ' . imw_error());

            if ($chkLaserChartNurseRecordRes) {
                $chkLaserChartNurseSignNurse1Id = $chkLaserChartNurseRecordRes->signNurse1Id;
                $chkLaserChartNurseFormStatus = $chkLaserChartNurseRecordRes->form_status;
                if ($chkLaserChartNurseFormStatus == 'not completed' || $chkLaserChartNurseFormStatus == '') {
                    $chkNurseSignColor = 'red';
                }
            } else {
                $chkNurseSignColor = '';
            }
        } else { //ELSE
            foreach ($chkSignNurseArr as $chkSignNurseArrTableName) {

                if ($chkSignNurseArrTableName == "preopphysicianorders" || $chkSignNurseArrTableName == "postopphysicianorders") {
                    $signNurseconfirmation_id = 'patient_confirmation_id';
                } else if ($chkSignNurseArrTableName == "preopnursingrecord" || $chkSignNurseArrTableName == "postopnursingrecord") {
                    $signNurseconfirmation_id = 'confirmation_id';
                }
                $nurse2check = "";
                $chartVersionNum = '';
                if ($chkSignNurseArrTableName == "preopphysicianorders" || $chkSignNurseArrTableName == "postopphysicianorders") {


                    $tblQry = "Select version_num From " . $chkSignNurseArrTableName . " Where $signNurseconfirmation_id='" . $chkNurseConfId . "' ";
                    $tblSql = DB::select($tblQry);
                    $chartVersionNum = $tblSql[0]->version_num;
                    if ($chartVersionNum > 1 && $chkSignNurseArrTableName == "preopphysicianorders") {
                        $nurse2check = " AND signNurse1Id!='0' AND signNurse1Id!=''";
                    } else if ($chartVersionNum > 2 && $chkSignNurseArrTableName == "postopphysicianorders") {
                        $nurse2check = " AND signNurse1Id!='0' AND signNurse1Id!=''";
                    }
                }

                //CHECK IF PATIENT RECORD EXIST IN DATABASE OR NOT-->(SET  NURSE SIGN COLOR FOR STUB TABLE)
                $chkPatientChartNurseRecordQry = "SELECT * FROM $chkSignNurseArrTableName WHERE $signNurseconfirmation_id='" . $chkNurseConfId . "' AND signNurseId!='0' AND signNurseId!='' $nurse2check";

                if ($chkSignNurseArrTableName == "preopphysicianorders" && $chartVersionNum > 1) {
                    $chkPatientChartNurseRecordQry = "SELECT * FROM $chkSignNurseArrTableName WHERE $signNurseconfirmation_id='" . $chkNurseConfId . "' $nurse2check";
                }

                $chkPatientChartNurseRecordRes = DB::select($chkPatientChartNurseRecordQry); // or die($chkPatientChartNurseRecordQry . 'Error Found at line no. ' . (__LINE__) . ': ' . imw_error());

                $chkPatientChartNurseFormStatus = '';
                if ($chkPatientChartNurseRecordRes) {
                    $chkNurseRecordExist = true;
                    $chkPatientChartNurseFormStatus = $chkPatientChartNurseRecordRes[0]->form_status;

                    if (($chkPatientChartNurseFormStatus == 'not completed' || $chkPatientChartNurseFormStatus == '') && $chkNurseSignColor == '') {
                        $chkNurseSignColor = '';
                    } else if ($chkPatientChartNurseFormStatus == 'not completed' || $chkPatientChartNurseFormStatus == '') {
                        $chkNurseSignColor = 'red';
                    } else if ($chkPatientChartNurseFormStatus == 'completed' && $chkNurseSignColor == '') {
                        $chkNurseSignColor = '';
                    }
                } else {
                    $chkNurseSignColor = '';
                }
                //END CHECK IF PATIENT RECORD EXIST IN DATABASE OR NOT-->(SET  NURSE SIGN COLOR FOR STUB TABLE)
            }

            //START CHECK SIGN FOR PRE-OP HEALTH QUESTIONAIRE (OPTIONAL) 
            /*
              if($chkNurseSignColor!='') {
              $chkNurseSignHealthQuestQry = "SELECT * FROM preophealthquestionnaire WHERE confirmation_id='".$chkNurseConfId."'";
              $chkNurseSignHealthQuestRes = imw_query($chkNurseSignHealthQuestQry) or die($chkNurseSignHealthQuestQry.imw_error());
              $chkNurseSignHealthQuestNumRow = imw_num_rows($chkNurseSignHealthQuestRes);
              if($chkNurseSignHealthQuestNumRow>0) {
              $chkNurseSignHealthQuestRow = imw_fetch_array($chkNurseSignHealthQuestRes);
              $chkHealthQuestNurseFormStatus=$chkNurseSignHealthQuestRow['form_status'];
              $chkHealthQuestSignNurseId=$chkNurseSignHealthQuestRow['signNurseId'];
              if($chkHealthQuestSignNurseId && ($chkHealthQuestNurseFormStatus=='not completed' || $chkHealthQuestNurseFormStatus=='')) {
              $chkNurseSignColor='red';
              }else if(!$chkHealthQuestSignNurseId && ($chkHealthQuestNurseFormStatus=='not completed' || $chkHealthQuestNurseFormStatus=='completed')) {
              $chkNurseSignColor='';
              }
              }
              } */
            //END CHECK SIGN FOR PRE-OP HEALTH QUESTIONAIRE (OPTIONAL)
            //START CHECK MAC REGIONAL FORM IS IN USE OR NOT
            if ($chkNurseSignColor != '') {


                $chkLocalAnesFormInUseQry = "SELECT * FROM localanesthesiarecord WHERE ((signAnesthesia1Id!='0' AND signAnesthesia2Id!='0' AND signAnesthesia3Id!='0') OR form_status='not completed' OR form_status='completed') AND confirmation_id='" . $chkNurseConfId . "'";
                $chkLocalAnesFormInUseRes = DB::select($chkLocalAnesFormInUseQry); // or die('Error Found at line no. ' . (__LINE__) . ': ' . imw_error());

                if ($chkLocalAnesFormInUseRes) {
                    //DO NOTHING
                } else {
                    //MAKE 'FOR LOOP' TO CHECK 'PRE-OPGENRAL','GENERAL' AND 'GENERAL NURSE NOTES' IS IN USE

                    $chkSignAnesAllGeneralArr = array('preopgenanesthesiarecord ', 'genanesthesiarecord', 'genanesthesianursesnotes');
                    $preOpGenAnesFormInUse = '';
                    foreach ($chkSignAnesAllGeneralArr as $chkSignAnesAllGeneralArrTableName) {

                        $chkPatientAnesAllGeneralRecordQry = "SELECT * FROM $chkSignAnesAllGeneralArrTableName WHERE confirmation_id='" . $chkNurseConfId . "'";
                        $chkPatientAnesAllGeneralRecordRes = DB::select($chkPatientAnesAllGeneralRecordQry); // or die('Error Found at line no. ' . (__LINE__) . ': ' . imw_error());

                        if ($chkPatientAnesAllGeneralRecordRes) {
                            $chkPatientAnesAllGeneralFormStatus = $chkPatientAnesAllGeneralRecordRes[0]->form_status;

                            if ($chkSignAnesAllGeneralArrTableName == 'preopgenanesthesiarecord') {
                                if ($chkPatientAnesAllGeneralFormStatus == 'not completed' || $chkPatientAnesAllGeneralFormStatus == 'completed') {
                                    $chkPreOpGenAnesFormInUse = true;
                                }
                            }
                            if ($chkSignAnesAllGeneralArrTableName == 'genanesthesiarecord') {
                                $chkPatientAnesAllSignAnesthesia1Id = $chkPatientAnesAllGeneralRecordRow['signAnesthesia1Id'];
                                if ($chkPatientAnesAllSignAnesthesia1Id || $chkPatientAnesAllGeneralFormStatus == 'not completed' || $chkPatientAnesAllGeneralFormStatus == 'completed') {
                                    $chkGenAnesFormInUse = true;
                                }
                            }
                            if ($chkSignAnesAllGeneralArrTableName == 'genanesthesianursesnotes') {
                                $chkPatientAnesAllSignNurseId = $chkPatientAnesAllGeneralRecordRow['signNurseId'];
                                if ($chkPatientAnesAllSignNurseId || $chkPatientAnesAllGeneralFormStatus == 'not completed' || $chkPatientAnesAllGeneralFormStatus == 'completed') {
                                    if ($chkPatientAnesAllSignNurseId && ($chkPatientAnesAllGeneralFormStatus == 'not completed' || $chkPatientAnesAllGeneralFormStatus == '')) {
                                        $chkNurseSignColor = 'red';
                                    } else if (!$chkPatientAnesAllSignNurseId && ($chkPatientAnesAllGeneralFormStatus == 'not completed' || $chkPatientAnesAllGeneralFormStatus == 'completed')) {
                                        $chkNurseSignColor = '';
                                    }
                                    /* else if(!$chkPatientAnesAllSignNurseId && $chkPatientAnesAllGeneralFormStatus!='not completed' && $chkPatientAnesAllGeneralFormStatus!='completed' && ($chkPreOpGenAnesFormInUse==true || $chkGenAnesFormInUse==true)) {
                                      $chkNurseSignColor='';
                                      } */
                                }
                            }
                        }
                    }
                }
            }
            //END CHECK MAC REGIONAL FORM IS IN USE OR NOT
            //CHECK IF NURSE VARIFIED THE OPERATING ROOM RECORD OR NOT
            if ($chkNurseSignColor != '') {
                $chkOproomNurseCheckMarkQry = "SELECT * FROM operatingroomrecords WHERE  (signNurse1Id!='0' AND signNurse1Id!='') AND verifiedbyNurse='Yes' AND confirmation_id='" . $chkNurseConfId . "'";
                $chkOproomNurseCheckMarkRes = DB::select($chkOproomNurseCheckMarkQry); // or die('Error Found at line no. ' . (__LINE__) . ': ' . imw_error());
                if ($chkOproomNurseCheckMarkRes) {

                    $chkOproomSignVerifybyNurse = $chkOproomNurseCheckMarkRes[0]->verifiedbyNurse;
                    $chkOproomSignNurseFormStatus = $chkOproomNurseCheckMarkRes[0]->form_status;
                    $chkOproom_iol_na = $chkOproomNurseCheckMarkRes[0]->iol_na;

                    $chkOproomSignNurseId = $chkOproomNurseCheckMarkRes[0]->signNurseId;
                    $chkOproomSignNurse1Id = $chkOproomNurseCheckMarkRes[0]->signNurse1Id;

                    if (!$chkOproom_iol_na && !$chkOproomSignNurseId) {
                        $chkNurseSignColor = '';
                    } else if ($chkOproomSignNurseFormStatus == 'not completed') {
                        $chkNurseSignColor = 'red';
                    }
                } else {
                    $chkNurseSignColor = '';
                }
            }
            //END CHECK IF NURSE VARIFIED THE OPERATING ROOM RECORD OR NOT
        }


//START CHECK SIGN FOR H & P (HEALTH AND PHYSICAL) CLEARANCE FORM (OPTIONAL) 
        $dirName = 'H&P';
        $scanDirQry = "Select sut.scan_upload_id From  scan_upload_tbl sut, scan_documents sd WHERE sd.confirmation_id = '" . $chkNurseConfId . "' And sut.confirmation_id 	= '" . $chkNurseConfId . "' And sd.document_name = '" . $dirName . "' And sd.document_id = sut.document_id Order By sd.document_id, sut.document_id ";
        $scanDirSql = DB::select($scanDirQry); // or die('Error Found in function ' . (__FUNCTION__) . ' at line no. ' . (__LINE__) . imw_error());
        $ekgHpCount = $scanDirSql;

        if (!$ekgHpCount) {
            if ($chkNurseSignColor != '') {
                $chkNurseSignHistoryPhysicalQry = "SELECT * FROM history_physicial_clearance WHERE confirmation_id='" . $chkNurseConfId . "'";
                $chkNurseSignHistoryPhysicalRes = DB::select($chkNurseSignHistoryPhysicalQry); // or die($chkNurseSignHistoryPhysicalQry . 'Error Found at line no. ' . (__LINE__) . ': ' . imw_error());

                if ($chkNurseSignHistoryPhysicalRes) {
                    $chkHistoryPhysicalNurseFormStatus = $chkNurseSignHistoryPhysicalRes[0]->form_status;
                    $chkHistoryPhysicalSignNurseId = $chkNurseSignHistoryPhysicalRes[0]->signNurseId;
                    if ($chkHistoryPhysicalSignNurseId && ($chkHistoryPhysicalNurseFormStatus == 'not completed')) {
                        $chkNurseSignColor = 'red';
                    } else if (!$chkHistoryPhysicalSignNurseId && ($chkHistoryPhysicalNurseFormStatus == 'not completed' || $chkHistoryPhysicalNurseFormStatus == 'completed')) {
                        $chkNurseSignColor = '';
                    }
                }
            }
        }
//END CHECK SIGN FOR H & P (HEALTH AND PHYSICAL) CLEARANCE FORM (OPTIONAL)
// Start Common Checking of nurse signatures in Transfer & Followup Chart
        if ($chkNurseSignColor != '') {
            $chkNurseSignTFQry = "SELECT * FROM transfer_followups WHERE confirmation_id='" . $chkNurseConfId . "'";
            $chkNurseSignTFRes = DB::select($chkNurseSignTFQry); // or die($chkNurseSignTFQry . 'Error Found at line no. ' . (__LINE__) . ': ' . imw_error());

            if ($chkNurseSignTFRes) {
                $chkTFNurseFormStatus = $chkNurseSignTFRes[0]->form_status;
                $chkTFSignNurseId = $chkNurseSignTFRes[0]->signNurseId;
                $chkTFSignNurse1Id = $chkNurseSignTFRes[0]->signNurse1Id;

                $checkTFSignNurseStatus = false;
                if ($chkTFSignNurseId && $chkTFSignNurse1Id) {
                    $checkTFSignNurseStatus = true;
                }

                if ($checkTFSignNurseStatus && ($chkTFNurseFormStatus == 'not completed')) {// CHECK ONLY IF RECORD IS SAVED ATLEAST ONCE
                    $chkNurseSignColor = 'red';
                } else if (!$checkTFSignNurseStatus && ($chkTFNurseFormStatus == 'not completed' || $chkTFNurseFormStatus == 'completed')) {
                    $chkNurseSignColor = '';
                }
            }
        }
// End  Common Checking of nurse signatures in Transfer & Followup Chart
//START COMMON CHECKING OF 'NURSE SIGNATURE' ON CONSENT FORMS FOR LASER AND OTHER CHARTNOTES
        $qrySC = "Select safety_check_list from surgerycenter Where surgeryCenterId = 1 LIMIT 0,1";
        $sqlSC = DB::select($qrySC); // or die(imw_error());
// Get Form Status of check list page
        $qryCHK = "SELECT form_status FROM surgical_check_list WHERE confirmation_id='" . $chkNurseConfId . "'";
        $sqlCHK = DB::select($qryCHK); // or die(imw_error());

        $showCheckListAdmin = $sqlSC[0]->safety_check_list ? true : false;
        $showCheckList = (!$showCheckListAdmin && isset($sqlCHK[0]->form_status) && ($sqlCHK[0]->form_status == 'completed' || $sqlCHK[0]->form_status == 'not completed') ) ? true : $showCheckListAdmin;
        $showCheckListStatus = $this->getChartShowStatus($chkNurseConfId, 'checklist');
        $showCheckList = $showCheckListStatus ? ($showCheckListStatus == 1 ? true : ($showCheckListStatus == 2 ? false : $showCheckList)) : $showCheckList;

        if ($chkNurseSignColor != '' && $showCheckList) {
            if (getenv('CHECKLIST_DATE')) {
                $chkConfirmDateQry = "SELECT patientConfirmationId FROM patientconfirmation WHERE patientConfirmationId='" . $chkNurseConfId . "' AND dos >= '" . getenv('CHECKLIST_DATE') . "'";
                $chkConfirmDateRes = DB::select($chkConfirmDateQry); // or die($chkConfirmDateQry . 'Error Found at line no. ' . (__LINE__) . ': ' . imw_error());
            }
            if ($chkConfirmDateRes || !getenv('CHECKLIST_DATE')) {
                $version2Query = " AND (CASE version_num WHEN '1' THEN signNurse2Id!='0' AND signNurse2Id!='' ELSE 1=1 END )";
                $chkSrgChkListNurseRecordQry = "SELECT form_status FROM surgical_check_list WHERE confirmation_id='" . $chkNurseConfId . "' AND signNurse1Id!='0' AND signNurse1Id!='' AND signNurse3Id!='0' AND signNurse3Id!=''  AND signNurse4Id!='0' AND signNurse4Id!='' $version2Query ";
                $chkSrgChkListNurseRecordRes = DB::select($chkSrgChkListNurseRecordQry); // or die($chkSrgChkListNurseRecordQry . 'Error Found at line no. ' . (__LINE__) . ': ' . imw_error());

                if ($chkSrgChkListNurseRecordRes) {
                    $chkSrgChkListNurseFormStatus = $chkSrgChkListNurseRecordRes[0]->form_status;
                    if ($chkSrgChkListNurseFormStatus == 'not completed' || $chkSrgChkListNurseFormStatus == '') {
                        $chkNurseSignColor = 'red';
                    }
                } else {
                    $chkNurseSignColor = '';
                }
            }
        }
        if ($chkNurseSignColor != '') {
            $chkConsentSignAllNurseQry = "SELECT * FROM consent_multiple_form WHERE confirmation_id='" . $chkNurseConfId . "' AND signNurseActivate='yes' AND consent_purge_status!='true' AND (form_status ='completed' OR form_status ='not completed') ";
            $chkConsentSignAllNurseRes = DB::select($chkConsentSignAllNurseQry); // or die($chkConsentSignAllNurseQry . 'Error Found at line no. ' . (__LINE__) . ': ' . imw_error());
            if ($chkConsentSignAllNurseRes) {
                $chkConsentSignNurse1Activate = '';
                foreach ($chkConsentSignAllNurseRes as $chkConsentSignNurseRow) {
                    $chkConsentSignNurse1Activate = $chkConsentSignNurseRow->signNurseActivate;
                    $chkConsentSignNurse1Id = $chkConsentSignNurseRow->signNurseId;
                    $chkConsentFrmFormNurseStatus = $chkConsentSignNurseRow->form_status;

                    if ($chkConsentSignNurse1Id == '0' || $chkConsentSignNurse1Id == '') {
                        $chkNurseSignColor = '';
                    } else if (($chkConsentSignNurse1Id != '0' && $chkConsentSignNurse1Id != '') && $chkConsentFrmFormNurseStatus == 'completed' && $chkNurseSignColor == '') {
                        $chkNurseSignColor = '';
                    } else if (($chkConsentSignNurse1Id != '0' && $chkConsentSignNurse1Id != '') && $chkConsentFrmFormNurseStatus == 'not completed' && $chkNurseSignColor == '') {
                        $chkNurseSignColor = '';
                    } else if (($chkConsentSignNurse1Id != '0' && $chkConsentSignNurse1Id != '') && $chkConsentFrmFormNurseStatus == 'completed' && $chkNurseSignColor == 'red') {
                        $chkNurseSignColor = 'red';
                    } else if (($chkConsentSignNurse1Id != '0' && $chkConsentSignNurse1Id != '') && $chkConsentFrmFormNurseStatus == 'not completed') {
                        $chkNurseSignColor = 'red';
                    }
                }
            }
        }
//END COMMON CHECKING OF 'NURSE SIGNATURE' ON CONSENT FORMS FOR LASER AND OTHER CHARTNOTES


        return $chkNurseSignColor;
    }

    public function getChartShowStatus($confId, $form) {
        $form = trim($form);
        $confId = (int) $confId;

        if ($form && $confId) {
            $arrTbl = array('checklist' => 'show_checklist');

            $fld = $arrTbl[$form];
            $qry = "SELECT " . $fld . " FROM patientconfirmation WHERE patientConfirmationId = " . $confId;
            $sql = DB::select($qry); // or die($qry . imw_error());

            if ($sql > 0) {
                return $result = $sql[0]->$fld;
            }
        }
        return false;
    }

    public function signAllDefautlMedications($pConfId, $primaryProcedureCatId, $surgeon_id, $procedureId, $secProcedureId, $terProcedureId, $preOpTableName = 'preopphysicianorders') {
        $otherPreOpOrdersFound = "";
        $preOpOrdersFoundExplode = 0;
        $preOpOrdersFound = "";
        $preOpConfirmationField = 'patient_confirmation_id';
        $preOpPrimaryKeyField = 'preOpPhysicianOrdersId';

        $getPreOpOrderDetails = $this->getRowRecord($preOpTableName, $preOpConfirmationField, $pConfId, 'prefilMedicationStatus,' . $preOpPrimaryKeyField . '');
        $preOpPrefilMedicationStatus = $getPreOpOrderDetails->prefilMedicationStatus;
        $preOpOrdersId = $getPreOpOrderDetails->$preOpPrimaryKeyField;

        $str_prelaserprocedure_templete = "SELECT * FROM laser_procedure_patient_table WHERE confirmation_id='$pConfId' ";
        $qry_prelaserprocedure_templete = DB::selectone($str_prelaserprocedure_templete);
        $laserprocedurePatientRecordid = $qry_prelaserprocedure_templete->patient_id;
        $laserPrefilMedicationStatus = $qry_prelaserprocedure_templete->prefilMedicationStatus;

        $prefilMedicationStatus = ($primaryProcedureCatId == 2 ) ? $laserPrefilMedicationStatus : $preOpPrefilMedicationStatus;

        //GETTING SURGEON PROFILE TO SHOW FIRST VIEW OF SURGEONID
        $selectSurgeonQry = "Select * From surgeonprofile Where surgeonId = '" . $surgeon_id . "' and del_status='' ";
        $selectSurgeonRes = DB::select($selectSurgeonQry); // or die(imw_error());
        foreach ($selectSurgeonRes as $selectSurgeonRow) {
            $surgeonProfileIdArr[] = $selectSurgeonRow->surgeonProfileId;
        }
        if (is_array($surgeonProfileIdArr)) {
            $surgeonProfileIdImplode = implode(',', $surgeonProfileIdArr);
        } else {
            $surgeonProfileIdImplode = 0;
        }
        $selectSurgeonProcedureQry = "Select * From surgeonprofileprocedure Where profileId In ($surgeonProfileIdImplode) Order By procedureName";
        $selectSurgeonProcedureRes = DB::select($selectSurgeonProcedureQry);
        if ($selectSurgeonProcedureRes) {
            foreach ($selectSurgeonProcedureRes as $selectSurgeonProcedureRow) {
                $surgeonProfileProcedureId = $selectSurgeonProcedureRow->procedureId;
                if ($procedureId == $surgeonProfileProcedureId) {
                    $surgeonProfileIdFound = $selectSurgeonProcedureRow->profileId;
                }
            }
            if (isset($surgeonProfileIdFound) && $surgeonProfileIdFound > 0) {
                $selectSurgeonProfileFoundQry = "select * from surgeonprofile where surgeonProfileId = '$surgeonProfileIdFound' and del_status=''";
                $selectSurgeonProfileFoundRes = DB::select($selectSurgeonProfileFoundQry);
                if ($selectSurgeonProfileFoundRes) {
                    $postOpDropSurgeonProfile = stripslashes($selectSurgeonProfileFoundRes[0]->postOpDrop);
                    $medicalEvaluationSurgeonProfile = stripslashes($selectSurgeonProfileFoundRes[0]->medicalEvaluation);
                    $preOpOrdersFound = $selectSurgeonProfileFoundRes[0]->preOpOrders;
                    $otherPreOpOrdersFound = $selectSurgeonProfileFoundRes[0]->otherPreOpOrders;
                }
            }
        }

        //GETTING SURGEON PROFILE TO SHOW FIRST VIEW OF SURGEONID

        /*         * ***
         * Start Procedure Preference Card to show first view
         * *** */
        if (isset($selectSurgeonProfileFoundRes) && !$selectSurgeonProfileFoundRes) {
            $proceduresArr = array($procedureId, $secProcedureId, $terProcedureId);
            foreach ($proceduresArr as $procId) {
                if ($procId) {
                    $procPrefCardQry = "Select * From procedureprofile Where procedureId = '" . $procId . "' ";
                    $procPrefCardSql = DB::select($procPrefCardQry);
                    if ($procPrefCardSql) {
                        $preOpOrders = $procPrefCardSql[0]->preOpOrders;
                        $preOpOrdersFound = $preOpOrders;
                        $otherPreOpOrdersFound = $procPrefCardSql[0]->otherPreOpOrders;
                        break;
                    }
                }
            }
        }
        /*         * ***
         * End Procedure Preference Card to show first view
         * *** */

        $laser_pre_op_medication = '';

        if ($primaryProcedureCatId == 2) {
            if ($laserprocedurePatientRecordid == 0) {
                // GETTING CONFIRMATION DETAILS
                $laserprocedure_Id = $procedureId;
                // GETTING laser procedure templete detail
                $str_procedure_templete = "SELECT * FROM laser_procedure_template WHERE laser_procedureID = '$laserprocedure_Id'  ";
                $qry_procedure_templete = DB::select($str_procedure_templete);
                $laser_preop_medication = '';
                foreach ($qry_procedure_templete as $fetchRows_procedure) {
                    $procedure_surgeonId = $surgeon_id;
                    $surgeon_select_explode = $fetchRows_procedure->laser_surgeonID;
                    if ($surgeon_select_explode != "all") {
                        $surgeon_select = explode(",", $surgeon_select_explode);
                        $count_surgeon = count($surgeon_select);
                        if ($count_surgeon == 1) {
                            if ($procedure_surgeonId == $surgeon_select_explode) {
                                $laser_preop_medication = $fetchRows_procedure->laser_preop_medication;
                                break;
                            }
                        }
                        $matchedSurgeon = false;
                        if ($count_surgeon > 1) {
                            for ($i = 0; $i < $count_surgeon; $i++) {
                                $match_surgeonid = $procedure_surgeonId;
                                $surgeon = $surgeon_select[$i];
                                if ($surgeon == $match_surgeonid) {
                                    $matchedSurgeon = true;
                                    $laser_preop_medication = $fetchRows_procedure->laser_preop_medication;
                                }
                            }
                        }
                        if ($matchedSurgeon == true) {
                            break;
                        }
                    } else {
                        $laser_preop_medication = $fetchRows_procedure->laser_preop_medication;
                    }
                }

                $tempLaser_preop_medication = str_replace(',', '', $laser_preop_medication);

                if ($tempLaser_preop_medication)
                    $preOpOrdersFound = $laser_preop_medication;
            }
        }
        $medicationLists = array();
        if ($prefilMedicationStatus <> 'true') {
            $preOpOrdersFoundExplode = explode(',', $preOpOrdersFound);
            for ($k = 0; $k <= count($preOpOrdersFoundExplode); $k++) {
                if (isset($preOpOrdersFoundExplode[$k]) && $preOpOrdersFoundExplode[$k] <> '') {
                    $selectPreOpmedicationOrderQry = "select * from preopmedicationorder where preOpMedicationOrderId = '$preOpOrdersFoundExplode[$k]'";
                    $selectPreOpmedicationOrderRes = DB::select($selectPreOpmedicationOrderQry);
                    $selectMedicationName = $selectPreOpmedicationOrderRes[0]->medicationName;
                    $selectStrength = $selectPreOpmedicationOrderRes[0]->strength;
                    $selectDirections = $selectPreOpmedicationOrderRes[0]->directions;
                    $data = array();
                    $data['medicationName'] = $selectMedicationName;
                    $data['strength'] = $selectStrength;
                    $data['direction'] = $selectDirections;

                    array_push($medicationLists, $data);
                }
            }
        }

        $medicationLists = array_map("unserialize", array_unique(array_map("serialize", $medicationLists)));
        return array($medicationLists, $otherPreOpOrdersFound);
    }

    public function signAllDefaultPostOpMedications($pConfId, $primaryProcedureCatId, $surgeon_id, $procedureId, $secProcedureId, $terProcedureId, $preOpTableName = 'postopphysicianorders') {
        $medicationPostOpLists = array();
        $surgeonProfileQry = "
			SELECT a.postOpDrop FROM surgeonprofile a,surgeonprofileprocedure b
			WHERE a.surgeonId			=	'" . $surgeon_id . "'
			AND   b.procedureId			=	'" . $procedureId . "'
			AND   a.surgeonProfileId	=	b.profileId
			AND   a.del_status=''
		";
        $surgeonProfileRes = DB::select($surgeonProfileQry);
        if ($surgeonProfileRes) {
            $patientToTakeHome = stripslashes($surgeonProfileRes[0]->postOpDrop);
        } else {

            /* Start Procedure Preference Card if surgeon's profile/Default  Not found */
            $proceduresArr = array($procedureId, $secProcedureId, $terProcedureId);
            foreach ($proceduresArr as $procedureIdMain) {
                if ($procedureIdMain) {
                    $procPrefCardQry = "Select postOpDrop From procedureprofile Where procedureId = '" . $procedureIdMain . "' ";
                    $procPrefCardSql = DB::select($procPrefCardQry);
                    if ($procPrefCardSql > 0) {
                        $patientToTakeHome = isset($procPrefCardSql[0]->postOpDrop) ? $procPrefCardSql[0]->postOpDrop : "";
                        break;
                    }
                }
            }
            /* End Procedure Preference Card if surgeon's profile/Default  Not found */
        }
        //Start Get Default Post Op Order
        $defaultPostOpOrder = '';
        if ($patientToTakeHome == '') {
            $defaultPostOpOrder = $this->getDefault('postopdrops', 'name', "@@");
            $explodeDefault = true;
            $patientToTakeHome = $defaultPostOpOrder;
        }
        //End Get Default Post Op Order
        $pOrderData = array();
        if (isset($explodeDefault) && $explodeDefault) {
            $pOrderData = explode('@@', $patientToTakeHome);
        } else {
            $pOrderData = explode(',', $patientToTakeHome);
        }
        //print'<pre>';print_r($pOrderData);
        //array_push($medicationPostOpLists,$pOrderData);
        $medicationPostOpLists = $pOrderData;
        return $medicationPostOpLists;
    }

    public function getDefault($table = "", $field = "", $replaceWith = ", ") {
        $table = trim($table);
        $field = trim($field);
        $return = '';
        $where = "";
        if ($table == 'intra_op_post_op_order' || $table == 'predefine_suppliesused') {
            $where = " Where deleted = '0' ";
        }
        if ($table && $field) {
            $query = "Select group_concat(TBL." . $field . " SEPARATOR  '@@') as " . $field . " From (Select * From " . $table . " " . $where . " Order By " . $field . ") TBL Where TBL.isDefault = 1 ";
            $sql = DB::select($query);
            $return = $sql[0]->$field;
            $returnArr = explode("@@", $return);
            natsort($returnArr);
            $return = implode($replaceWith, $returnArr);
        }

        return $return;
    }

    public function getRowRecord($table, $conditionId, $value, $orderBy = 0, $sortOrder = 0) {
        if ($conditionId && $orderBy && $sortOrder) {
            $qryStr = "SELECT * FROM $table WHERE $conditionId = '$value' ORDER BY $orderBy $sortOrder";
        } else if ($orderBy) {
            $qryStr = "SELECT * FROM $table WHERE $conditionId = '$value' ORDER BY $orderBy DESC";
        } else {
            $qryStr = "SELECT * FROM $table WHERE $conditionId = '$value'";
        }

        $qryQry = DB::selectone($qryStr);
        if ($qryQry) {
            return $qryQry;
        }
    }

    public function verifyProcIsInjMisc($procedureId = '') {
        $return = '';
        if ($procedureId) {
            $qry = "Select PC.isMisc, PC.isInj From procedures P Join procedurescategory PC on P.catId = PC.proceduresCategoryId Where procedureId = '" . $procedureId . "' ";
            $sql = DB::selectone($qry); // or die('Error Found in function ' . (__FUNCTION__) . ' at line no. ' . (__LINE__) . imw_error());
            $isMisc = $sql->isMisc;
            $isInj = $sql->isInj;
            if ($isInj)
                $return = 'injection';
            elseif ($isMisc)
                $return = 'misc';
        }
        return $return;
    }

    // GET OBJECT ON MULTIPLE CONDITIONS
    function getMultiChkArrayRecords($table = '', $conditionArr = array(), $orderBy = 0, $sortOrder = 0, $extraCondition = '') {
        $elements = count($conditionArr);
        $qry = "SELECT * FROM $table WHERE";
        $counter = 0;
        foreach ($conditionArr as $keyEle => $keyValue) {
            ++$counter;
            $qry .= " $keyEle = '$keyValue'";
            if ($counter < $elements) {
                $qry .= " AND";
            }
        }
        if ($elements > 0) {
            $qry .= $extraCondition;
        }
        if ($qry) {
            if ($orderBy) {
                $qry .=" ORDER BY $orderBy $sortOrder";
            }
        }
        $qryQry = DB::select($qry);
        if ($qryQry) {
            return $qryQry;
        }
    }

    function left_slider_menu(Request $request, $pConfirmId = '') {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        if ($pConfirmId == '') {
            $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        }
        $basicgreen = "#ECF1EA";
        $dark_green = "#BCD2B0";
        $light_green = "#D1E0C9";
        $title1_color = "#FFFFFF"; //white
        $title2_color = "#000000"; //black
//Pre_op_physician_order (2 Forms)
        $bgdark_orange_physician = "#C06E2D";
        $bgmid_orange_physician = "#DEA068";
        $border_color_physician = "#BB5E00";
        $bglight_orange_physician = "#FFE6CC";
        $row1color_physician = "#FFF2E6";
//Local/gen_anes_record(4 forms)
//$border_blue_local_anes="#080E4C";
        $border_blue_local_anes = "#323CC0";
        $bgdark_blue_local_anes = "#3232F0";
        $bgmid_blue_local_anes = "#80AFEF";
        $bglight_blue_local_anes = "#EAF4FD";
        $tablebg_local_anes = "#C5D8FD";
        $white = "#FFFFFF";
//End edit by mamta
// Done BY Munisha
//Post_op_nursing_order
        $title_post_op_nursing_order = "#C0AA1E";
        $bgcolor_post_op_nursing_order = "#F5EEBD";
        $border_post_op_nursing_order = "#C0AA1E";
        $heading_post_op_nursing_order = "#EFE492";
        $rowcolor_post_op_nursing_order = "#FAF6DC";
//pre_op_nursing_order
        $title_pre_op_nursing_order = "#C0AA1E";
        $bgcolor_pre_op_nursing_order = "#F5EEBD";
        $border_pre_op_nursing_order = "#C0AA1E";
        $heading_pre_op_nursing_order = "#EFE492";
//$rowcolor_pre_op_nursing_order="#FAF6DC";
        $rowcolor_pre_op_nursing_order = "#F7F3DC";

//laser procedure
        $bgcolor_laser_procedure = "#F5EEBD";

//op_room_record 
        $title_op_room_record = "#004587";
        $bgcolor_op_room_record = "#CFE1F7";
        $border_op_room_record = "#004587";
        $heading_op_room_record = "#80A7D6";
        $rowcolor_op_room_record = "#E2EDFB";
//discharge summary sheet
        $title_discharge_summary_sheet = "#FF950E";
        $bgcolor_discharge_summary_sheet = "#FBE8D2";
        $border_discharge_summary_sheet = "#FF950E";
        $heading_discharge_summary_sheet = "#FCBE6F";
        $rowcolor_discharge_summary_sheet = "#FBF5EE";
//Amendments_notes
        $title_Amendments_notes = "#A0A0C8";
        $bgcolor_Amendments_notes = "#EEEEFA";
        $border_Amendments_notes = "#A0A0C8";
        $heading_Amendments_notes = "#D0D0ED";
        $rowcolor_Amendments_notes = "#F0F0FA";
        $LeftOpLaserSlider = 'Operating Room';
        $procedureChkSliderLeftQry = "SELECT * FROM patientconfirmation WHERE patientConfirmationId = '" . $pConfirmId . "'";
        $procedureChkSliderLeftQryRow = DB::selectone($procedureChkSliderLeftQry);
        if ($procedureChkSliderLeftQryRow) {
            $SliderLeft_patientPrimProc = $procedureChkSliderLeftQryRow->patient_primary_procedure;
            $SliderLeft_patientPrimProcId = $procedureChkSliderLeftQryRow->patient_primary_procedure_id;
            $primary_procedureSliderLeftQry = "SELECT * FROM procedures WHERE (name = '" . addslashes($SliderLeft_patientPrimProc) . "' OR procedureAlias = '" . addslashes($SliderLeft_patientPrimProc) . "')";
            $primary_procedureSliderLeftRow = DB::selectone($primary_procedureSliderLeftQry);
            if ($primary_procedureSliderLeftRow) {
                $primary_procedureSliderLeftQry = "SELECT * FROM procedures WHERE procedureId = '" . $SliderLeft_patientPrimProcId . "'";
                $primary_procedureSliderLeftRow = DB::selectone($primary_procedureSliderLeftQry);
            }
            $patient_primary_procedure_categoryLeftID = '';
            if ($primary_procedureSliderLeftRow) {
                $patient_primary_procedure_categoryLeftID = $primary_procedureSliderLeftRow->catId;
                if ($patient_primary_procedure_categoryLeftID == 2) {
                    $LeftOpLaserSlider = 'Laser Procedure';
                }
            }
        }

        $consentCategory = "SELECT category_id,category_name FROM `consent_category` where category_status!='true' ORDER BY `consent_category`.`category_id` ASC";
        $consentCategoryRow = DB::select($consentCategory);
        if ($consentCategoryRow) {
            foreach ($consentCategoryRow as $consentCategoryRows) {
                $subkey = $consentCategoryRows->category_name;
                $Subcategory[] = ['category_id' => $consentCategoryRows->category_id, 'category_name' => $consentCategoryRows->category_name, 'background-color' => '#00c0ef'];
                $consentFormTemplateSelectQry = "select consent_id,consent_alias,consent_delete_status from `consent_forms_template` WHERE consent_delete_status!='true' and consent_category_id='" . $consentCategoryRows->category_id . "'  order by consent_id";
                $consentFormTemplateSelectRows = DB::select($consentFormTemplateSelectQry);
                if ($consentFormTemplateSelectRows) {
                    foreach ($consentFormTemplateSelectRows as $consentFormTemplateSelectRow) {
                        $consentFormTemplateSelectConsentAlias = stripslashes($consentFormTemplateSelectRow->consent_alias);
                        $consentFormTemplateDeleteStatus = $consentFormTemplateSelectRow->consent_delete_status;
                        if ($consentFormTemplateSelectConsentAlias != '') {
                            $patientconfirmation_id = 'confirmation_id';
                            $consentchkQry = "SELECT form_status, consent_purge_status FROM consent_multiple_form  WHERE $patientconfirmation_id = '" . $pConfirmId . "' and consent_template_id='" . $consentFormTemplateSelectRow->consent_id . "'";
                            $consentchkRow = DB::selectone($consentchkQry);
                            if ($consentchkRow) {
                                $status_consent = $consentchkRow->form_status;
                                $consent_purge_status_chk = $consentchkRow->consent_purge_status;
                            }
                            if ($consentchkRow && $status_consent == 'completed') {
                                $chkMrkImage_consent = "green";
                            } else if ($consentchkRow && $status_consent == 'not completed') {
                                $chkMrkImage_consent = "red";
                            } else {
                                $chkMrkImage_consent = "";
                            }
                            $consentFormAliasArr[$subkey][] = ['id' => $consentFormTemplateSelectRow->consent_id, 'value' => $consentFormTemplateSelectConsentAlias, 'color' => '#aaa', 'flag' => $chkMrkImage_consent];
                            $consentFormTemplateSelectConsentId[] = $consentFormTemplateSelectRow->consent_id;
                        }
                    }
                }
            }
        }
        //END GET MULTIPLE CONSENT FORMS 
        $checkListFormsStatusArr = $this->getTblFormStatus('surgical_check_list', 'confirmation_id', $pConfirmId);
        $checkListFormsStatus = $checkListFormsStatusArr[0];
        if ($checkListFormsStatus == 'completed') {
            $chkMrkImageChkLst = "green";
        } else if ($checkListFormsStatus == 'not completed') {
            $chkMrkImageChkLst = "red";
        } else {
            $chkMrkImageChkLst = "";
        }
        $menuListArr = array('Consent Form', 'Pre-Op Health', 'Nursing Record', 'Physician Orders', 'Anesthesia', $LeftOpLaserSlider, 'Surgical', 'Discharge Summary', 'Post Op Inst. Sheet', 'Transfer & Follow-up', 'Physician Notes', 'ePostIt');
        $subMenuListArr[0] = $consentFormAliasArr;
        $subMenuListArr[1] = [$light_green, 'Health Questionnaire', 'H & P Clearance'];
        $subMenuListArr[2] = array($heading_post_op_nursing_order, 'Pre-Op', 'Pre-Op Aldrete', 'Post-Op', 'Post-Op Aldrete');
        $subMenuListArr[3] = array($bgmid_orange_physician, 'Pre-Op ', 'Post-Op ');
        $subMenuListArr[4] = array($bgmid_blue_local_anes, 'MAC/Regional', 'Pre-Op General', 'General', 'General Nurse Notes');
        $subMenuListArr[5] = array($heading_op_room_record, 'Intra-Op Record', 'Laser Procedure', 'Injection/Miscellaneous');
//$subMenuListArr[6]  = array($light_yellow, 'Laser Procedure');
        $subMenuListArr[6] = array($light_green, 'Operative Report');
//$subMenuListArr[6]  = array($light_green,'QA Check List');
        $subMenuListArr[7] = array($heading_discharge_summary_sheet, 'Discharge Summary');
        $subMenuListArr[8] = array($light_green, 'Instruction Sheet', 'Medication Reconciliation Sheet');
        $subMenuListArr[9] = array($light_green, 'Transfer & Follow-up');
        $subMenuListArr[10] = array($heading_Amendments_notes, 'Amendments');
        $subMenuListArr[11] = array($light_green, 'ePostIt');

        $consent_left_list = array('chkMrkImageChkLst' => $chkMrkImageChkLst, $menuListArr[0] => $subMenuListArr[0], 'Subcategory' => $Subcategory, $menuListArr[1] => $subMenuListArr[1], $menuListArr[2] => $subMenuListArr[2], $menuListArr[3] => $subMenuListArr[3], $menuListArr[4] => $subMenuListArr[4], $menuListArr[5] => $subMenuListArr[5], $menuListArr[6] => $subMenuListArr[6], $menuListArr[7] => $subMenuListArr[8], $menuListArr[9] => $subMenuListArr[9], $menuListArr[10] => $subMenuListArr[10], $menuListArr[11] => $subMenuListArr[11]);
        return $consent_left_list;
    }

    function getASCInfo($fac_id = '') {
        $ascAddr = $ascPhoneNum = "";
        $qryStr = "SELECT fac_id, fac_name,fac_address1,fac_address2,fac_city,fac_state,fac_zip,fac_contact_phone FROM facility_tbl WHERE fac_id = '" . $fac_id . "'";
        $qryRow = DB::selectone($qryStr);
        if ($qryRow) {
            if (trim($qryRow->fac_address1)) {
                $ascAddr .= '<table style="border:none;" cellpadding="0" cellspacing="0">';
                $ascAddr .= '	<tr><td>' . trim(stripslashes($qryRow->fac_address1)) . '</td></tr>';
                if (trim($qryRow->fac_address2)) {
                    $ascAddr .= '	<tr><td>' . trim(stripslashes($qryRow->fac_address2)) . '</td></tr>';
                }
                if (trim($qryRow->fac_city) && trim($qryRow->fac_zip)) {
                    $ascAddr .= '	<tr><td>' . trim(stripslashes($qryRow->fac_city)) . ', ' . trim(stripslashes($qryRow->fac_state)) . ' ' . trim(stripslashes($qryRow->fac_zip)) . '</td></tr>';
                }
                $ascAddr .= '</table>';
            }
            if (trim($qryRow->fac_contact_phone)) {
                $ascPhoneNum = trim($qryRow->fac_contact_phone);
            }
        }
        return array($ascAddr, $ascPhoneNum);
    }

    // UPDATE TABLE ROW
    function updateRecords($arrayRecord = array(), $table = '', $condId = '', $condValue = '', $extraCondition = '') {
        $seq = 0;
        if (is_array($arrayRecord)) {
            $countFields = count($arrayRecord);
            $updateStr = "UPDATE $table SET ";
            foreach ($arrayRecord as $field => $value) {
                ++$seq;
                $updateStr .= "$field = '$value'";
                if ($seq < $countFields) {
                    $updateStr .= ", ";
                }
            }
            $updateStr .= " WHERE $condId = '$condValue' " . $extraCondition;
            //echo '<br>'.$updateStr;
            $updateQry = DB::select($updateStr);
        }
    }

    // FETCH ROW TO EXTRACT
    function getExtractRecord($table = '', $conditionId = '', $value = '', $fieldName = '') {
        $field = "*";
        if ($fieldName) {
            $field = $fieldName;
        }
        $qryStr = "SELECT " . $field . " FROM $table WHERE $conditionId = '$value'";
        $qryQry = DB::select($qryStr);
        if ($qryQry) {
            return $qryQry;
        }
    }

    public function injectionProfile($procedureID = '', $surgeonID = '', $fields = '*') {
        $data = array();
        $data['profileFound'] = false;
        $fields = (trim($fields)) ? $fields : '*';
        $profileDataQry = "Select " . $fields . " From inj_misc_procedure_template Where procedureID = '" . $procedureID . "' And FIND_IN_SET('" . $surgeonID . "',surgeonID) Order By templateID Desc Limit 1 ";
        $profileDataSql = DB::selectone($profileDataQry);

        if (!$profileDataSql) {
            $profileDataQry = "Select " . $fields . " From inj_misc_procedure_template Where procedureID='" . $procedureID . "' And surgeonID = 'all' Order By templateID Desc Limit 1 ";
            $profileDataSql = DB::selectone($profileDataQry);
        }
        if ($profileDataSql) {
            $data['profileFound'] = $profileDataSql;
            $data['data'] = $profileDataSql;
        }
        return $data;
    }

    public function patient_header_details(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $loginUserType = $request->json()->get('UserType') ? $request->json()->get('UserType') : $request->input('UserType');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $dos = $request->json()->get('dos') ? $request->json()->get('dos') : $request->input('dos');
        $epost_table = $request->json()->get('epost_table') ? $request->json()->get('epost_table') : $request->input('epost_table');
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
                $patientHeaderInfo = $this->getRowRecord('patient_data_tbl', 'patient_id', $patient_id);
                $patientHeaderFname = $patientHeaderInfo->patient_fname;
                $patientHeaderMname = $patientHeaderInfo->patient_mname;
                $patientHeaderNname = $patientHeaderInfo->patient_lname;
                $patientHeaderImwPatientId = $patientHeaderInfo->imwPatientId;
                //$patientHeaderName = $patientHeaderFname." ".$patientHeaderMname." ".$patientHeaderNname;
                $patientHeaderID = $patientHeaderInfo->patient_id;
                $imwPatientHeaderID = "";
                $patientHeader_age = '';
                if ($patientHeaderInfo->date_of_birth != "" && $patientHeaderInfo->date_of_birth != "0000-00-00") {
                    $tmpHeader_date = $patientHeaderInfo->date_of_birth;
                    $patientHeader_age = $this->dob_calc($tmpHeader_date);
                }

                $patientHeadersexTemp = $patientHeaderInfo->sex;
                $patientHeadersex = '';
                if ($patientHeadersexTemp == "m") {
                    $patientHeadersex = "Male";
                } else if ($patientHeadersexTemp == "f") {
                    $patientHeadersex = "Female";
                }
                $patientHeaderhomePhone = $patientHeaderInfo->homePhone;
                $patientHeaderworkPhone = $patientHeaderInfo->workPhone;
                if ($patientHeaderhomePhone <> "") {
                    $patientHeaderPhone = $patientHeaderhomePhone;
                } else {
                    $patientHeaderPhone = $patientHeaderworkPhone;
                }


                $patientHeaderID = $patientHeaderInfo->patient_id;

                if (getenv("SHOW_IMW_PATIENT_ID") == "YES" && trim($patientHeaderImwPatientId)) {
                    $imwPatientHeaderID = " / " . $patientHeaderImwPatientId;
                }
                $patientHeaderName = $patientHeaderNname . ", " . $patientHeaderFname . ' - ' . $patientHeaderID . $imwPatientHeaderID;

                $addressPatientstreet1 = addslashes($patientHeaderInfo->street1);
                $addressPatientstreet2 = addslashes($patientHeaderInfo->street2);
                $addressPatientcity = addslashes($patientHeaderInfo->city);
                $addressPatientstate = $patientHeaderInfo->state;
                $addressPatientzip = $patientHeaderInfo->zip;
                //if ($addressPatientstreet2)
                //echo $addressPatientstreet2 . '<br />';
                $address = stripslashes($addressPatientcity . "," . $addressPatientstate . " " . $addressPatientzip);
                $Confirm_patientHeaderInfo = $this->getRowRecord('patientconfirmation', 'patientConfirmationId', $pConfirmId);
                $Confirm_patientHeaderAscID = $Confirm_patientHeaderInfo->ascId;
                $finalized_status = $Confirm_patientHeaderInfo->finalize_status;
                $Confirm_patientHeaderAdvanceDirective = $Confirm_patientHeaderInfo->advanceDirective;
                $Confirm_patientHeaderDosTemp = $Confirm_patientHeaderInfo->dos;
                $Confirm_patientHeaderDos_split = explode("-", $Confirm_patientHeaderDosTemp);
                $Confirm_patientHeaderDos = $Confirm_patientHeaderDos_split[1] . "-" . $Confirm_patientHeaderDos_split[2] . "-" . $Confirm_patientHeaderDos_split[0];
                $Confirm_patientHeaderSurgeon_name = stripslashes($Confirm_patientHeaderInfo->surgeon_name);
                $Confirm_patientHeaderSurgeon_id = stripslashes($Confirm_patientHeaderInfo->surgeonId);
                $Confirm_patientHeaderAnesthesiologist_name = stripslashes($Confirm_patientHeaderInfo->anesthesiologist_name);
                if (strlen($Confirm_patientHeaderAnesthesiologist_name) <= 17) {
                    $anesHeaderNowrap = "nowrap";
                } else {
                    $anesHeaderNowrap = "";
                }

                $Confirm_patientHeaderPrimProcIsMisc = $Confirm_patientHeaderInfo->prim_proc_is_misc;
                $Confirm_patientHeaderSiteTemp = $Confirm_patientHeaderInfo->site;
                // APPLYING NUMBERS TO PATIENT SITE
                if ($Confirm_patientHeaderSiteTemp == 1) {
                    $Confirm_patientHeaderSite = "Left Eye";  //OS
                } else if ($Confirm_patientHeaderSiteTemp == 2) {
                    $Confirm_patientHeaderSite = "Right Eye";  //OD
                } else if ($Confirm_patientHeaderSiteTemp == 3) {
                    $Confirm_patientHeaderSite = "Both Eye";  //OU
                } else if ($Confirm_patientHeaderSiteTemp == 4) {
                    $Confirm_patientHeaderSite = "Left Upper Lid";
                } else if ($Confirm_patientHeaderSiteTemp == 5) {
                    $Confirm_patientHeaderSite = "Left Lower Lid";
                } else if ($Confirm_patientHeaderSiteTemp == 6) {
                    $Confirm_patientHeaderSite = "Right Upper Lid";
                } else if ($Confirm_patientHeaderSiteTemp == 7) {
                    $Confirm_patientHeaderSite = "Right Lower Lid";
                } else if ($Confirm_patientHeaderSiteTemp == 8) {
                    $Confirm_patientHeaderSite = "Bilateral Upper Lid";
                } else if ($Confirm_patientHeaderSiteTemp == 9) {
                    $Confirm_patientHeaderSite = "Bilateral Lower Lid";
                }
                // END APPLYING NUMBERS TO PATIENT SITE
                //$Confirm_patientHeaderSite = $Confirm_patientHeaderInfo->site_description;
                $Confirm_patientHeaderPrimProc = stripslashes($Confirm_patientHeaderInfo->patient_primary_procedure);
                $primProcFullNameForDiv = $Confirm_patientHeaderPrimProc;
                $primProcId = $Confirm_patientHeaderInfo->patient_primary_procedure_id;
                if (strlen($Confirm_patientHeaderPrimProc) > 17) {
                    $Confirm_patientHeaderPrimProc = substr($Confirm_patientHeaderPrimProc, 0, 17) . "... ";
                }

                $Confirm_patientHeaderSecProc = stripslashes($Confirm_patientHeaderInfo->patient_secondary_procedure);
                $secProcFullNameForDiv = $Confirm_patientHeaderSecProc;
                $secProcId = $Confirm_patientHeaderInfo->patient_secondary_procedure_id;
                if (strlen($Confirm_patientHeaderSecProc) > 16) {
                    $Confirm_patientHeaderSecProc = substr($Confirm_patientHeaderSecProc, 0, 16) . "... ";
                }
                $Confirm_patientHeaderAssist_by_translator = $Confirm_patientHeaderInfo->assist_by_translator;
                //SET ALLERGIES VALUE
                $Confirm_NKDA = $this->getRowRecord('patientconfirmation', 'patientConfirmationId', $pConfirmId);
                $Confirm_patientHeaderAllergiesNKDA_status = $Confirm_NKDA->allergiesNKDA_status;
                $patient_allergies_tblQry = "SELECT `allergy_name` FROM `patient_allergies_tbl` WHERE `patient_confirmation_id` = '" . $pConfirmId . "'";
                $patient_allergies_tblRow = DB::selectone($patient_allergies_tblQry);
                if ($patient_allergies_tblRow) {
                    $patientHeaderAllergyName = $patient_allergies_tblRow->allergy_name;
                    if (trim(strtoupper($patientHeaderAllergyName)) == 'NKA' && $patient_allergies_tblRow) {
                        $allergiesValue = 'NKA';
                    } else {
                        $allergiesValue = '<img src="images/Interface_red_image003.gif" style="width:17px; height:15px;vertical-align:middle;" onClick="showAllergiesPopUpFn(' . $pConfirmId . ');">';
                    }
                } else if ($Confirm_patientHeaderAllergiesNKDA_status == "Yes") {
                    $allergiesValue = 'NKA';
                } else {
                    $allergiesValue = '';
                }
                //END SET ALLERGIES VALUE
                //check whether procedure is laser procedure or not
                $str_procedure = "SELECT * FROM procedures WHERE name = '" . $primProcFullNameForDiv . "'";
                $fetchRows_procedure = DB::selectone($str_procedure);

                $patient_consent_categoryID = $fetchRows_procedure->catId;
                $patient_primary_procedure_id = $fetchRows_procedure->procedureId;

                // Check Whether Procedure is Injection Procedure
                if ($patient_consent_categoryID <> '2') {
                    if ($Confirm_patientHeaderPrimProcIsMisc == '') {
                        $Confirm_patientHeaderPrimProcIsMisc = $this->verifyProcIsInjMisc($patient_primary_procedure_id);
                    }
                } else {
                    $Confirm_patientHeaderPrimProcIsMisc = '';
                }
                // Check Whether Procedure is Injection Procedure
                if ($patient_consent_categoryID != 2) {
                    if ($Confirm_patientHeaderPrimProcIsMisc) {
                        $Confirm_patientHeaderAnesthesiologist_name_ad = "N/A";
                    } else {
                        $Confirm_patientHeaderAnesthesiologist_name_ad = $Confirm_patientHeaderAnesthesiologist_name;
                    }
                } else {
                    $Confirm_patientHeaderAnesthesiologist_name_ad = "N/A";
                }

                //end check whether procedure is laser procedure or not

                $query_rsNotes = "SELECT txtNote FROM tblprogress_report, users WHERE tblprogress_report.confirmation_id = '" . $pConfirmId . "' AND users.usersId = tblprogress_report.usersId ";
                $rsNotes = DB::select($query_rsNotes); // or die(imw_error());
                $a = '';
                $b = '#336699';
                if (!$rsNotes) {
                    $a = "#DFF4FF";
                    $srcImage = "images/progress_notes.gif";
                } else {
                    $a = "red";
                    $b = "white";
                    $srcImage = "images/progress_notes_hover.gif";
                }
                //GET BASE LINE VITAL SIGN FROM PREOP NURSING RECORD
                if ($patient_consent_categoryID != 2) {
                    if ($Confirm_patientHeaderPrimProcIsMisc) {
                        $vitalSignBp_NursingHeader = "";
                        $vitalSignP_NursingHeader = "";
                        $vitalSignR_NursingHeader = "";
                        $vitalSignO2SAT_NursingHeader = "";
                        $vitalSignTemp_NursingHeader = "";
                        $vitalSignHeight_NursingHeader = "";
                        $vitalSignWeight_NursingHeader = "";
                        $vitalSignBmi_NursingHeader = "";

                        $selectVitalSignCopyHeaderQry = "SELECT * FROM `injection` WHERE `confirmation_id` = '" . $pConfirmId . "'";
                        $selectVitalSignCopyHeaderRow = DB::selectone($selectVitalSignCopyHeaderQry); // or die($selectVitalSignCopyHeaderQry . imw_error());
                        if ($selectVitalSignCopyHeaderRow) {
                            $vitalSignBp_NursingHeader = $selectVitalSignCopyHeaderRow->preVitalBp;
                            $vitalSignP_NursingHeader = $selectVitalSignCopyHeaderRow->preVitalPulse;
                            $vitalSignR_NursingHeader = $selectVitalSignCopyHeaderRow->preVitalResp;
                            $vitalSignO2SAT_NursingHeader = $selectVitalSignCopyHeaderRow->preVitalSpo;
                            $vitalSignTemp_NursingHeader = 'N/A';
                            $vitalSignHeight_NursingHeader = "N/A";
                            $vitalSignWeight_NursingHeader = "N/A";
                            $vitalSignBmi_NursingHeader = "N/A";
                        }
                    } else {
                        $selectPreOpNursingHeaderQry = "SELECT * FROM `preopnursingrecord` WHERE `confirmation_id` = '" . $pConfirmId . "'";
                        $selectPreOpNursingHeaderRow = DB::selectone($selectPreOpNursingHeaderQry);
                        $vitalSignBp_NursingHeader = "";
                        $vitalSignP_NursingHeader = "";
                        $vitalSignR_NursingHeader = "";
                        $vitalSignO2SAT_NursingHeader = "";
                        $vitalSignTemp_NursingHeader = "";
                        $vitalSignHeight_NursingHeader = "";
                        $vitalSignWeight_NursingHeader = "";
                        $vitalSignBmi_NursingHeader = "";
                        if ($selectPreOpNursingHeaderRow) {
                            $preopnursing_vitalsign_id = $selectPreOpNursingHeaderRow->preopnursing_vitalsign_id;
                            $vitalSignHeight_NursingHeader = $selectPreOpNursingHeaderRow->patientHeight;
                            $vitalSignHeight_NursingHeader .=!empty($vitalSignHeight_NursingHeader) ? '"' : '';
                            $vitalSignWeight_NursingHeader = $selectPreOpNursingHeaderRow->patientWeight;
                            $vitalSignWeight_NursingHeader .=!empty($vitalSignWeight_NursingHeader) ? ' lbs' : '';
                            $vitalSignBmi_NursingHeader = $selectPreOpNursingHeaderRow->patientBmi;


                            if ($preopnursing_vitalsign_id) {
                                $ViewPreopNurseVitalHeaderSignQry = "select * from `preopnursing_vitalsign_tbl` where  vitalsign_id = '" . $preopnursing_vitalsign_id . "'";
                                $ViewPreopNurseVitalHeaderSignRow = DB::selectone($ViewPreopNurseVitalHeaderSignQry); // or die(imw_error());
                                if ($ViewPreopNurseVitalHeaderSignRow) {
                                    $vitalSignBp_NursingHeader = $ViewPreopNurseVitalHeaderSignRow->vitalSignBp;
                                    $vitalSignP_NursingHeader = $ViewPreopNurseVitalHeaderSignRow->vitalSignP;
                                    $vitalSignR_NursingHeader = $ViewPreopNurseVitalHeaderSignRow->vitalSignR;
                                    $vitalSignO2SAT_NursingHeader = $ViewPreopNurseVitalHeaderSignRow->vitalSignO2SAT;
                                    $vitalSignTemp_NursingHeader = $ViewPreopNurseVitalHeaderSignRow->vitalSignTemp;
                                }
                            }
                            /* $vitalSignBp_NursingHeader = $selectPrmeOpNursingHeaderRow->vitalSignBp;
                              $vitalSignP_NursingHeader = $selectPreOpNursingHeaderRow->vitalSignP;
                              $vitalSignR_NursingHeader = $selectPreOpNursingHeaderRow->vitalSignR;
                              $vitalSignO2SAT_NursingHeader = $selectPreOpNursingHeaderRow->vitalSignO2SAT;
                              $vitalSignTemp_NursingHeader = $selectPreOpNursingHeaderRow->vitalSignTemp;
                             */
                            if ($vitalSignTemp_NursingHeader <> "") {
                                $vitalSignTemp_NursingHeader = $vitalSignTemp_NursingHeader;
                            }
                        }
                    }
                } else {
                    $selectLaserProedureHeaderQry = "SELECT * FROM `laser_procedure_patient_table` WHERE `confirmation_id` = '" . $pConfirmId . "'";
                    $selectlaserProcedureRow = DB::selectone($selectLaserProedureHeaderQry); // or die(imw_error());
                    $vitalSignBp_NursingHeader = "";
                    $vitalSignP_NursingHeader = "";
                    $vitalSignR_NursingHeader = "";
                    $vitalSignO2SAT_NursingHeader = "";
                    $vitalSignTemp_NursingHeader = "";
                    $vitalSignHeight_NursingHeader = "";
                    $vitalSignWeight_NursingHeader = "";
                    if ($selectlaserProcedureRow) {
                        $vitalSignBp_NursingHeader = $selectlaserProcedureRow->prelaserVitalSignBP;
                        $vitalSignP_NursingHeader = $selectlaserProcedureRow->prelaserVitalSignP;
                        $vitalSignR_NursingHeader = $selectlaserProcedureRow->prelaserVitalSignR;
                        $vitalSignO2SAT_NursingHeader = 'N/A';
                        $vitalSignTemp_NursingHeader = 'N/A';
                        $vitalSignHeight_NursingHeader = "N/A";
                        $vitalSignWeight_NursingHeader = "N/A";
                        $vitalSignBmi_NursingHeader = "N/A";
                    }
                }
                //END GET BASE LINE VITAL SIGN FROM PREOP NURSING RECORD
                $sxAlertDiv = $this->getPtSxAlert($patient_id, $patientHeaderName);
                $sxAlertOnClick = $sxAlertStyle = "";
                if ($sxAlertDiv) {// && $_SESSION['loginUserType']=="Surgeon"
                    //echo $sxAlertDiv; //show div of sx alert on click on patient name
                    $sxAlertOnClick = "sxAlertDivFun('divPtSxAlert','inline-block','" . $patient_id . "','','','','clicked','disableCancelBtnId');";
                    $sxAlertHover = "sxAlertDivFun('divPtSxAlert','inline-block','','','','','','disableCancelBtnId');";
                    $sxAlertStyle = "color:#FF0000; cursor:pointer;";
                }

                // Get Surgeon Practice Match Result if Peer Review Option is on
                $surgeryCenterSettings = $this->loadSettings('peer_review');
                $surgeryCenterPeerReview = $surgeryCenterSettings->peer_review;
                $practiceNameMatch = '';
                if ($surgeryCenterPeerReview == 'Y' && $loginUserType == 'Surgeon') {
                    $practiceNameMatch = $this->getPracMatchUserId($userId, $Confirm_patientHeaderSurgeon_id);
                }

                $status = 1;
                $message = " Patient Header Details ";
                $userProcedureDetails = DB::select("select procedureId,name from `procedures` order by name asc");
                $patient_info = [
                    'dob' => ($patientHeaderInfo->date_of_birth != "0000-00-00") ? date("m-d-Y", strtotime($patientHeaderInfo->date_of_birth)) : '',
                    'patientname' => $patientHeaderName,
                    'address' => $address,
                    'Site' => $Confirm_patientHeaderSite,
                    'A/D' => $Confirm_patientHeaderAdvanceDirective,
                    'age' => $patientHeader_age,
                    'tel' => $patientHeaderPhone,
                    'primary-procedure' => $Confirm_patientHeaderPrimProc,
                    'surgerydate' => $Confirm_patientHeaderDos,
                    'sex' => $patientHeadersex,
                    'allergies' => $allergiesValue,
                    'sec-proc' => $Confirm_patientHeaderSecProc,
                    'surgeon' => $Confirm_patientHeaderSurgeon_name,
                    'Anesthesia' => $Confirm_patientHeaderAnesthesiologist_name_ad,
                    'Translator' => $Confirm_patientHeaderAssist_by_translator,
                    'ASC' => $Confirm_patientHeaderAscID,
                    'finalized_status' => $finalized_status,
                    'priProcedureList' => $userProcedureDetails,
                    'secProcedureList' => $userProcedureDetails,
                ];
                $qry = "SELECT ep.`table_name`,count(*) as total,ep.`table_name`
                        FROM eposted ep LEFT JOIN users AS usr1 ON (usr1.usersId=ep.created_operator_id)
                                         LEFT JOIN users AS usr2 ON (usr2.usersId=ep.modified_operator_id)
                                         WHERE patient_conf_id ='" . $pConfirmId . "' and patient_id='" . $patient_id . "' AND epost_consent_purge_status !='true'
                         group by ep.table_name";
                $result = DB::select($qry);
                $epostDetailsCount = [];
                if ($result) {
                    foreach ($result as $res) {
                        $epostDetailsCount[$res->table_name] = $res->total;
                    }
                }
                $BaseLineVitalSigns = ['B/P' => $vitalSignBp_NursingHeader, 'P' => $vitalSignP_NursingHeader, 'R' => $vitalSignR_NursingHeader, 'O2SAT' => $vitalSignO2SAT_NursingHeader, 'Temp' => $vitalSignTemp_NursingHeader, 'Height' => $vitalSignHeight_NursingHeader, 'Weight' => $vitalSignWeight_NursingHeader, 'BMI' => $vitalSignBmi_NursingHeader];
                $data = ['Patientdetails' => $patient_info, 'BaseLineVitalSigns' => $BaseLineVitalSigns, 'epostDetailCount' => $epostDetailsCount];
            }
        }
        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => '',
                    'data' => $data,
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

    public function assist_by_translator_ajax(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $chkBxAssist = $request->json()->get('chkBxAssist') ? $request->json()->get('chkBxAssist') : $request->input('chkBxAssist');
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
                $message = " Record Updated Successfully ! ";
                if ($chkBxAssist) {
                    $updateChbxAssistByTransQry = "update patientconfirmation set assist_by_translator = '$chkBxAssist' where patientConfirmationId = '$pConfirmId'";
                    $updateChbxAssistByTransRes = DB::select($updateChbxAssistByTransQry);
                }
            }
        }
        return response()->json([
                    'status' => 1,
                    'message' => $message,
                    'requiredStatus' => '',
                    'data' => '',
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

    public function update_procedure_ajax(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $field = $request->json()->get('field') ? $request->json()->get('field') : $request->input('field');
        $procedure_id = $request->json()->get('procedure_id') ? $request->json()->get('procedure_id') : $request->input('procedure_id');
        $text = $request->json()->get('text') ? $request->json()->get('text') : $request->input('text');
        $stub_id = $request->json()->get('stub_id') ? $request->json()->get('stub_id') : $request->input('stub_id');
        $message = " unauthorized ";
        $requiredStatus = [];
        $userId = $this->checkToken($userToken);
        $query1 = $query2 = $query3 = "";
        if ($userId > 0) {
            if ($pConfirmId == "") {
                $message = " ConfirmId is missing ";
                $status = 1;
                $requiredStatus = 0;
            } else {
                $status = 1;
                $message = " Record Updated Successfully ! ";

                if ($field) {

                    if ($field == 1) {

                        $procCatQry = "Select PC.isMisc,isInj From procedures P JOIN procedurescategory PC On P.catId = PC.proceduresCategoryId Where P.procedureId = '" . $procedure_id . "' ";
                        $procCatSql = DB::selectone($procCatQry); // or die('Error Found: ' . imw_error());
                        $isMiscProc = '';
                        if ($procCatSql->isInj)
                            $isMiscProc = 'injection';
                        elseif ($procCatSql->isMisc)
                            $isMiscProc = 'misc';

                        $query1 = "update stub_tbl set patient_primary_procedure = '$text' where stub_id = '$stub_id'";
                        $query2 = "update patientconfirmation set patient_primary_procedure = '$text',
				 patient_primary_procedure_id= '$procedure_id' where patientConfirmationId = '$pConfirmId'";
                        $query3 = "update patientconfirmation set prim_proc_is_misc = '" . $isMiscProc . "' where patientConfirmationId = '$pConfirmId' And prim_proc_is_misc <> '' ";
                    }
                    else {
                        $query1 = "update stub_tbl set patient_secondary_procedure = '$text' where stub_id = '$stub_id'";
                        $query2 = "update patientconfirmation set patient_secondary_procedure = '$text',
				 patient_secondary_procedure_id= '$procedure_id' where patientConfirmationId = '$pConfirmId'";
                    }
                    if ($query1 <> "")
                        DB::select($query1);
                    if ($query2 <> "")
                        DB::select($query2);
                    if ($query3 <> "")
                        DB::select($query3);
                }
            }
        }
        return response()->json([
                    'status' => 1,
                    'message' => $message,
                    'requiredStatus' => '',
                    'data' => '',
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

    function dob_calc($dob = '') {
        $dob_yy = substr($dob, 0, 4);
        $dob_rem = substr($dob, 4);
        $dob_rem = str_replace("-", "", $dob_rem);
        $dob_curr = date("Y") . $dob_rem;
        $age = date("Y") - $dob_yy;
        if ($dob_curr > date("Ymd")) {
            $age = $age - 1;
        }
        return $age;
    }

    public function loadSettings($fields = '*') {
        $result = DB::selectone("SELECT $fields FROM `surgerycenter` where surgeryCenterId=1");
        return $result;
    }

    function getPtSxAlert($alertPtId, $ptName) {
        $alertQry = "SELECT * FROM iolink_patient_alert_tbl WHERE patient_id = '" . $alertPtId . "' AND iosync_status='Syncronized' AND alert_disabled!='yes'";
        $alertRes = DB::select($alertQry);
        $alrtVal = '';
        if ($alertRes) {
            $bgCol = '#BCD2B0';
            $borderCol = '#BCD2B0';
            $rowcolor = '#F1F4F0';
            $alrtVal .= '<div id="divPtSxAlert" onMouseDown="drag_div_move(this, event);" style="position:absolute; display:none; left:300px; top:80px; z-index:1; border:solid 1px ' . $borderCol . '; width:350px" class="row">
							<div  class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%;  background:#d9534f;  padding-top:5px;color:#FFF; text-align:left;font-weight:bold;padding-left:5px;">
								Patient Sx Alert
								<span onClick="sxAlertDivFun(\'divPtSxAlert\',\'none\',\'' . $alertPtId . '\',\'\',\'\',\'\',\'\',\'disableCancelBtnId\');" style="float:right; color:#FFF; cursor:pointer; font-family:Verdana;">X</span>
							</div>
							
							<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:90px; background: #FFF; overflow:auto">
						';
            $i = 0;
            foreach ($alertRes as $alertRow) {
                if ($i % 2 == 0) {
                    $a = "#F1F4F0";
                } else {
                    $a = "#FFFFFF";
                }

                $alrtVal .= '	
                            <div class="row hoverdiv" style="background-color:' . $a . '; border-bottom : solid 1px #DDD; padding-top:5px; padding-bottom:5px; " >
                                    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" >' . $alertRow->alert_content . '</div>
                            </div>
                        ';
                $i++;
            }

            $alrtVal .= '</div>
					    
                        <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" id="disableCancelBtnId"  style=" width:100%;display:none; border-top:solid 1px #EEE; background: #FFF; ">

                                <a href="#" class="btn btn-success" id="alertDisableBtn" style="border:none;" title="Disable" onClick="sxAlertDivFun(\'divPtSxAlert\',\'none\',\'' . $alertPtId . '\',\'' . $ptName . '\',\'ptNameId\',\'' . $userId . '\',\'\',\'disableCancelBtnId\');">Disable</a>

                                <a href="#" class="btn btn-danger" id="alertCancelBtn" onClick="sxAlertDivFun(\'divPtSxAlert\',\'none\',\'' . $alertPtId . '\',\'\',\'\',\'\',\'\',\'disableCancelBtnId\');">Cancel</a>

                  </div>

                </div>';
        }
        return $alrtVal;
    }

    function getPracMatch($loginUsrPracName = '', $srgPracName = '') {
        $pracMatch = "";
        if ($loginUsrPracName && $srgPracName) {
            $loginUsrPracNameArr = explode(",", $loginUsrPracName);
            $srgPracNameArr = explode(",", $srgPracName);
            if (array_intersect($loginUsrPracNameArr, $srgPracNameArr)) {
                $pracMatch = "yes";
            }
        }
        return $pracMatch;
    }

    function getPracMatchUserId($userId1 = '', $userId2 = '') {
        $pracIdMatch = '';
        if ($userId1 && $userId2 && ($userId1 != $userId2)) {
            $userIdQry = "SELECT practiceName FROM users WHERE  usersId in('$userId1','$userId2')";
            $userIdRes = DB::select($userIdQry);
            $practiceNameArr = array();
            if ($userIdRes) {
                foreach ($userIdRes as $userIdRows) {
                    $practiceNameArr[] = $userIdRows->practiceName;
                }
                $pracIdMatch = $this->getPracMatch($practiceNameArr[0], $practiceNameArr[1]);
            }
        }
        return $pracIdMatch;
    }

    //FUNCTION TO GET USER NAME FROM USER TABLE
    function getUserName($UserId = '', $UserType = '') {
        $ViewUserNameQry = "select * from `users` where  usersId = '" . $UserId . "' and user_type ='" . $UserType . "'";
        $ViewUserNameRow = DB::selectone($ViewUserNameQry);
        $UserName = "";
        if ($ViewUserNameRow) {
            if ($ViewUserNameRow->lname) {
                $UserName = trim(stripslashes($ViewUserNameRow->lname . ", " . $ViewUserNameRow->fname . " " . $ViewUserNameRow->mname));
            } else {
                $UserName = "";
            }
        }
        return $UserName;
    }

    function getTblFormStatus($tablename, $fieldName, $pConfId) {
        $chkdFormStatusQry = "SELECT form_status FROM $tablename WHERE $fieldName = '" . $pConfId . "' ";
        $chkFormStatusRes = DB::selectone($chkdFormStatusQry); // or die($chkdFormStatusQry.imw_error());	
        $formStatus = $chkFormStatusRes->form_status;
        $frmStatusArr = array($formStatus);
        return $frmStatusArr;
    }

    function convertBase64($imagesrc, $instructionSheetId, $patient_id, $pConfirmId) {
        $imagesrc = str_ireplace(' ', '+', $imagesrc);
        $imageName = $instructionSheetId . "_" . $patient_id . "_" . $pConfirmId . '.' . 'jpg';
        if (trim($imagesrc) <> "") {
            $newfile = explode(",", $imagesrc);
            if (!file_exists('../../SigPlus_images/PatientId_' . $patient_id)) {
                mkdir('../../SigPlus_images/PatientId_' . $patient_id, 0777);
            }
            if (!file_exists('../../SigPlus_images/PatientId_' . $patient_id . "/sign/")) {
                mkdir('../../SigPlus_images/PatientId_' . $patient_id . "/sign/", 0777);
            }
            $success = @file_put_contents('../../SigPlus_images/PatientId_' . $patient_id . "/sign/" . $imageName, base64_decode($newfile[1]));

            if ($success) {
                return 'PatientId_' . $patient_id . "/sign/" . $imageName;
            }
        }
        return '';
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

    function getTmFormat($timeValue = '') {
        if (!trim($timeValue) || $timeValue == "00:00:00" || $timeValue == "00:00") {
            return;
        }
        if (strtotime($timeValue) === false)
            return $timeValue;
        $timeValueShow = date("h:i A", strtotime($timeValue));
        if (getenv("SHOW_MILITARY_TIME") == "YES") {
            $timeValueShow = date("H:i", strtotime($timeValue));
        }
        if (trim(substr($timeValueShow, 0, 5)) == '00:00') {
            return;
        }
        return $timeValueShow;
    }

    public function loadVitalSignGridStatus($currentFormStatus = '', $vitalSignGridStatus = '', $pageName = '') {
        $pagesAllowed = array('oproom', 'macAnes', 'genAnes', 'transferFollowup');
        if (in_array($pageName, $pagesAllowed) && ($currentFormStatus <> 'completed' && $currentFormStatus <> 'not completed')) {
            $fieldName = 'vital_sign_' . $pageName;
            $settings = $this->loadSettings($fieldName);
            $vitalSignGridStatus = ($settings->$fieldName == 'Y') ? 1 : 0;
        }
        return $vitalSignGridStatus;
    }

    function getDirContentStatus($pConfId = '', $return = 3, $returnType = 3, $dirName = 'H&P') {
        $dirName = trim($dirName);
        $dirName = ($dirName) ? $dirName : 'H&P';

        $return = (int) $return;
        $returnType = (int) $returnType;
        $isDirCreated = false;
        $dirListCount = 0;
        $dirListContent = '';

        $scanDirQry = "Select sut.scan_upload_id, sut.image_type, sut.pdfFilePath From  scan_upload_tbl sut, scan_documents sd WHERE sd.confirmation_id = '" . $pConfId . "' And sut.confirmation_id 	= '" . $pConfId . "' And sd.document_name = '" . $dirName . "' And sd.document_id = sut.document_id Order By sd.document_id, sut.document_id ";

        if ($dirName == 'Sx Planning Sheet') {
            $scanDirQry = "Select sut.scan_upload_id, sut.image_type, sut.pdfFilePath From  scan_upload_tbl sut, scan_documents sd WHERE sd.confirmation_id = '" . $pConfId . "' And sut.confirmation_id 	= '" . $pConfId . "' And sd.document_name = 'Clinical' And sd.document_id = sut.document_id AND sut.pdfFilePath LIKE '%Sx_Planing_Sheet_%' Order By sd.document_id, sut.document_id ";
        }
        $scanDirNum = 0;
        //return $scanDirQry;							
        $scanDirSql = DB::select($scanDirQry); // or die('Error Found in function ' . (__FUNCTION__) . ' at line no. ' . (__LINE__) . imw_error());
        if ($scanDirSql) {
            foreach ($scanDirSql as $scanDirSqls) {
                $scanDirNum++;
            }
            $isDirCreated = true;
            $dirListCount = $scanDirNum;

            if ($return === 3) {
                $listArray = array();
                foreach ($scanDirSql as $scanDirRow) {
                    $scan_upload_id = $scanDirRow->scan_upload_id;
                    $image_type = $scanDirRow->image_type;
                    $pdfFilePath = $scanDirRow->pdfFilePath;

                    if ($image_type == 'application/pdf')
                        $image_type = 'pdf';

                    $data = array();
                    $data['imageType'] = $image_type;
                    $data['scanUploadId'] = $scan_upload_id;
                    $data['pdfFilePath'] = $pdfFilePath;

                    array_push($listArray, $data);
                }

                if ($returnType === 1 || $returnType === 2) {
                    $onClick = array();
                    foreach ($listArray as $key => $link) {
                        $on = " top.openImage(\'" . $link['scanUploadId'] . "\',\'" . $link['imageType'] . "\',\'" . $link['pdfFilePath'] . "\')";
                        array_push($onClick, $on);
                    }

                    $dirName = str_ireplace('H&P', 'H&amp;P', $dirName);
                    $dirName = str_ireplace('Ocular Hx', 'OCX', $dirName);
                    $dirName = str_ireplace('Health Questionnaire', 'HQ', $dirName);
                    $dirName = str_ireplace('Sx Planning Sheet', 'SxP', $dirName);

                    if ($returnType === 1) {
                        $dirListContent = '<a href="#" class="btn-sm" onclick="' . implode(";", $onClick) . '">' . $dirName . '</a>&nbsp;';
                        return $dirListContent;
                    } else {
                        $dirListContent = array();
                        foreach ($onClick as $on) {
                            $html = '';
                            $html = '<a href="#" class="btn-sm" onclick="return ' . $on . '>' . $dirName . '</a>&nbsp;';
                            array_push($dirListContent, $html);
                        }
                    }
                } elseif ($returnType === 3)
                    $dirListContent = $listArray;
            }
        }

        if ($return === 1)
            return $isDirCreated;
        elseif ($return === 2)
            return $dirListCount;
        elseif ($return === 3)
            return $dirListContent;
    }

    function changeDateYMD($dateStr = '') {
        list($mmDate, $ddDate, $yyDate) = explode('-', $dateStr);
        $showDate = $yyDate . '-' . $mmDate . '-' . $ddDate;
        return $showDate;
    }

    function getUsrNm($id, $returnString = false) {
        $nurseName = "";
        if ($id) {
            $userNurseQry = "select * from users where usersId=" . (int) $id;
            $userNurseRow = DB::selectone($userNurseQry);
            $nurseName = $userNurseRow->lname . ", " . $userNurseRow->fname . " " . $userNurseRow->mname;
        }
        $usrArr = $returnString ? $nurseName : array($nurseName);
        return $usrArr;
    }

    function setTmFormat($timeValue = '', $tmType = '') {
        //$timeValue = obj.value;
        $tFlag = false;
        $tmpArr = explode(':', $timeValue);
        if (strlen($timeValue) == 8 || !$timeValue) {
            if (!$timeValue) {
                return;
            }

            $HH = substr($tmpArr[0], 0, 2);
            //$HH = $timeValue.explode(':')[0].substr(0,2);
            $MM = (int) (substr($tmpArr[1], 0, 2));
            //$MM = (int)($timeValue.explode(':')[1].substr(0,2));
            if ($HH > 24) {
                $tFlag = true;
            }
            if ($MM > 59) {
                $tFlag = true;
            }
        }
        $MM = '00';
        if (strlen($timeValue) >= 1) {
            $HH = substr($timeValue, 0, 2);
            if (stristr($timeValue, ':')) {
                $HH = substr($tmpArr[0], 0, 2);
                //$HH = $timeValue.explode(':')[0].substr(0,2);
                $MM = (int) (substr($tmpArr[1], 0, 2));
                //$MM = (int)($timeValue.explode(':')[1].substr(0,2));
            }
            if ($HH > 24 || strlen($timeValue) <= 3) {
                $HH = substr($timeValue, 0, 1);
                //$HH = $timeValue.substr(0,1); 
                $MM = (int) (substr($timeValue, 1, 2));
                //$MM = (int)($timeValue.substr(1,2));

                if ((int) substr($timeValue, 0, 2) <= 24) {
                    $HH = substr($timeValue, 0, 2);
                    $MM = (int) (substr($timeValue, 2, 1));
                }

                if (stristr($timeValue, ':')) {
                    $HH = substr($tmpArr[0], 0, 2);
                    //$HH = $timeValue.explode(':')[0].substr(0,2);
                    if ($HH > 24) {
                        $HH = substr($tmpArr[0], 0, 1);
                    }
                    //if($HH>24) { $HH = $timeValue.explode(':')[0].substr(0,1); }
                    $MM = (int) (substr($tmpArr[1], 0, 2));
                    //$MM = (int)($timeValue.explode(':')[1].substr(0,2));
                }
                if ($HH == 0) {
                    $HH = substr($timeValue, 0, 2);
                    //$HH = $timeValue.substr(0,2); 
                    $MM = (int) (substr($timeValue, 2, 2));
                    //$MM = (int)($timeValue.substr(2,2)); 
                }
                if ($MM <= 9 && $MM != 0) {
                    $MM = '0' . $MM;
                }
                if ($MM == '' || $MM == 0) {
                    $MM = '00';
                }
            }
            if ($HH == '') {
                $HH = '00';
            }
            if ($HH <= 9 && strlen($HH) == 1) {
                $HH = '0' . (int) ($HH);
            }
        } else {
            $HH = '00';
        }
        if (strlen($timeValue) > 3) {
            if ($MM == '00') {
                $MM = (int) (substr($timeValue, 2, 2));
                //$MM = (int)($timeValue.substr(2,2));
            }
            if (stristr($timeValue, ':')) {
                $MM = (int) (substr($tmpArr[1], 0, 2));
                //$MM = (int)($timeValue.explode(':')[1].substr(0,2));
            }
            if ($MM <= 9 && strlen($MM) == 2) {
                $MM = (int) (substr($MM, 1, 1));
            }
            if (($MM <= 9 && $MM != 0) || strlen($MM) == 1) {
                $MM = '0' . $MM;
            }
            if ($MM == '' || $MM == 0) {
                $MM = '00';
            }
        } else {
            //$MM = '00';
        }
        $tFlagPM = true;
        if ($HH > 12) {
            $tFlagPM = false;
            $HH = $HH - 12;
            if ($HH <= 9 && $HH != 0) {
                $HH = '0' . $HH;
            }
        }
        //if(($HH >= 7 && $HH <= 11 && $tFlagPM==true))
        if (($HH <= 11 && $tFlagPM == true)) {
            $Suffix = 'AM';
        } else {
            $Suffix = 'PM';
        }
        if (stristr($timeValue, 'A')) {
            $Suffix = 'AM';
        }
        //if($timeValue.search('a')>=0 || $timeValue.search('A')>=0) {$Suffix = 'AM'; }
        if (stristr($timeValue, 'P')) {
            $Suffix = 'PM';
        }
        //if($timeValue.search('p')>=0 || $timeValue.search('P')>=0) {$Suffix = 'PM'; }
        if (!is_numeric($HH)) {
            $HH = '00';
        }
        if (!is_numeric($MM)) {
            $MM = '00';
        }

        if ($MM > 59 || $HH == '00' || $tFlag == true) {
            $timeValue = '';
            return;
        }
        $timeValue = $HH . ':' . $MM . ' ' . $Suffix;
        $timeValue = date("H:i:s", strtotime($timeValue));
        if ($tmType == 'static') {
            $timeValue = $HH . ':' . $MM . ' ' . $Suffix;
        }
        if ($HH == '00' && $MM == '00') {
            return;
        }
        return $timeValue;
    }

    public function validateGroupOR($fieldsArr = array()) {
        $status = false;
        if (is_array($fieldsArr) && count($fieldsArr) > 0) {
            foreach ($fieldsArr as $key => $val) {
                if ($val) {
                    $status = true;
                }
            }
        }

        return $status;
    }

    public function validateGroupAND($fieldsArr = array()) {
        $status = true;
        if (is_array($fieldsArr) && count($fieldsArr) > 0) {
            foreach ($fieldsArr as $key => $val) {
                if (!$val) {
                    $status = false;
                }
            }
        }
        return $status;
    }

    function getAllRecords($table, $fields = array(), $where = array(), $groupBy = array(), $orderBy = array(), $returnKey = '') {
        $return = array();
        $matchString = '';
        $orderString = '';

        if (empty($table))
            return $return;

        $fields = implode(",", $fields);
        $fields = empty($fields) ? '*' : $fields;

        $groupBy = @implode(",", $groupBy);

        if (is_array($where) && count($where) > 0) {
            $matchString .= ' Where ';
            $whereCount = count($where);
            $counter = 0;
            foreach ($where as $key => $value) {
                $counter++;
                $matchString .= $key . " '" . $value . "' ";
                $matchString .= ($counter < $whereCount ) ? ' AND ' : '';
            }
        }

        if (is_array($orderBy) && count($orderBy) > 0) {
            $orderString = ' Order By ';
            $counter = 0;
            $orderCount = count($orderBy);
            foreach ($orderBy as $key => $value) {
                $counter++;
                $orderString .= $key . ' ' . $value;
                $orderString .= ($counter < $orderCount ) ? ', ' : '';
            }
        }

        $query = "Select " . $fields . " From " . $table . " " . $matchString . " " . $groupBy . $orderString;
        $sql = DB::select($query);
        //return $query; 
        if ($sql) {
            foreach ($sql as $row) {
                $return[] = $row;
            }
        }

        return $return;
    }

// FETCH ROW TO EXTRACT
    function getRowCount($table, $where = array()) {
        $return = 0;
        $matchString = '';
        if (empty($table))
            return $return;

        if (is_array($where) && count($where) > 0) {

            $matchString .= ' WHERE ';
            $counter = 0;
            foreach ($where as $key => $value) {
                $counter++;
                $matchString .= $key . "'" . $value . "'";
                $matchString .= ($counter < count($where) ) ? ' AND ' : '';
            }
        }

        $query = "SELECT count(*) as resultCount FROM " . $table . " " . $matchString;

        $sql = DB::selectone($query);

        if ($sql) {
            $return = $sql->resultCount;
        }

        return $return;
    }

    // FETCH ARRAY OBJECT
    function getArrayRecords($table, $conditionId = 0, $value = 0, $orderBy = 0, $sortOrder = 0, $extraCondition = '') {
        if ($orderBy) {
            if ($conditionId && !$value && $sortOrder) {
                $qryStr = "SELECT * FROM $table WHERE $conditionId <> '' " . $extraCondition . " ORDER BY $orderBy $sortOrder";
            } else if ($conditionId && $sortOrder) {
                $qryStr = "SELECT * FROM $table WHERE $conditionId = '" . trim($value) . "' " . $extraCondition . " ORDER BY $orderBy $sortOrder";
            } else if ($conditionId && !$sortOrder) {
                $qryStr = "SELECT * FROM $table WHERE $conditionId = '" . trim($value) . "' " . $extraCondition . " ORDER BY $orderBy DESC";
            } else if ($sortOrder) {
                $qryStr = "SELECT * FROM $table ORDER BY $orderBy $sortOrder";
            } else {
                $qryStr = "SELECT * FROM $table ORDER BY $orderBy DESC";
            }
        } else {
            if ($conditionId) {
                $qryStr = "SELECT * FROM $table WHERE $conditionId = '$value' " . $extraCondition . " ";
            } else {
                $qryStr = "SELECT * FROM $table";
            }
        }
        $qryQry = DB::select($qryStr);
        if ($qryQry) {
            return $qryQry;
        }
    }

    // FETCH ARRAY OBJECT
    function proc_site_modifiers($pid = '', $dos = '', $pri_site_id = '', $pri_proc_id = '', $sec_site_id = '', $sec_proc_id = '', $ter_site_id = '', $ter_proc_id = '') {

        $pri_proc_id = (int) $pri_proc_id;
        $sec_proc_id = (int) $sec_proc_id;
        $ter_proc_id = (int) $ter_proc_id;
        $sec_proc_id = 18;
        $qryProc = "Select procedureId, code, poe_enable, poe_days From procedures Where procedureId IN ($pri_proc_id,$sec_proc_id, $ter_proc_id) ";
        $sqlProc = DB::select($qryProc); // or die($qryProc . imw_error());
        $return = array();
        foreach ($sqlProc as $row) {

            $mod1 = $mod2 = $mod3 = '';
            $priArr = $secArr = $terArr = array();
            $in_poe_period = false;
            $pri_mod = false;
            $proc_id = $row->procedureId;
            if ($proc_id == $pri_proc_id) {
                $pri_mod = true;
                $priArr = $this->filter_site_modifiers($pri_site_id);
            }

            if ($proc_id == $sec_proc_id) {
                $secArr = $this->filter_site_modifiers($sec_site_id);
            }

            if ($proc_id == $ter_proc_id) {
                $terArr = $this->filter_site_modifiers($ter_site_id);
            }

            $mergeArr = array_merge($priArr, $secArr, $terArr);
            $mergeArr = array_unique($mergeArr);
            foreach ($mergeArr as $mod) {
                if (empty($mod1))
                    $mod1 = $mod;
                else if (empty($mod2))
                    $mod2 = $mod;
                else if (empty($mod3))
                    $mod3 = $mod;
            }

            // Fill Modifier value if within poe period
            if ($row->poe_enable) {
                $in_poe_period = $this->chk_poe_period_appt($pid, $dos, $row->poe_days, $row->code);
            }

            if ($in_poe_period && empty($mod1))
                $mod1 = '79';
            else if ($in_poe_period && empty($mod2))
                $mod2 = '79';
            else if ($in_poe_period && empty($mod3))
                $mod3 = '79';

            if ($pri_mod) {
                $return['pri']['mod1'] = $mod1;
                $return['pri']['mod2'] = $mod2;
                $return['pri']['mod3'] = $mod3;
            }

            $return[$row->code]['mod1'] = $mod1;
            $return[$row->code]['mod2'] = $mod2;
            $return[$row->code]['mod3'] = $mod3;
        }
        return $return;
    }

    function filter_site_modifiers($site_id = '') {
        $mod1 = $mod2 = '';
        // Fill modifier value based upon site
        if ($site_id == 1) {
            $mod1 = 'LT';
        } else if ($site_id == 2) {
            $mod1 = 'RT';
        } else if ($site_id == 3) {
            $mod1 = '50';
        } else if ($site_id == 4) {
            $mod1 = 'E1';
        } else if ($site_id == 5) {
            $mod1 = 'E2';
        } else if ($site_id == 6) {
            $mod1 = 'E3';
        } else if ($site_id == 7) {
            $mod1 = 'E4';
        } else if ($site_id == 8) {
            $mod1 = 'E1';
            $mod2 = 'E3';
        } else if ($site_id == 9) {
            $mod1 = 'E2';
            $mod2 = 'E4';
        }
        // return modifier values
        return array($mod1, $mod2);
    }

    function chk_poe_period_appt($pid = '', $dos = '', $poe_days = '', $cpt_code = '') {
        $pid = (int) $pid;
        $date_from = date('Y-m-d', strtotime($dos . '-' . $poe_days . 'days'));
        $date_to = date('Y-m-d', strtotime($dos . '-1day'));
        $qry = "Select pc.patientConfirmationId From patientconfirmation pc
                    Join stub_tbl st on st.patient_confirmation_id = pc.patientConfirmationId
                    Left Join procedures p1 on pc.patient_primary_procedure_id = p1.procedureId
                    Left Join procedures p2 on pc.patient_secondary_procedure_id = p2.procedureId
                    Left Join procedures p3 on pc.patient_tertiary_procedure_id = p3.procedureId
                    Where pc.patientId = '" . $pid . "' 
                    And st.patient_status Not In ('Canceled','No Show')
                    And pc.dos Between '" . $date_from . "' And '" . $date_to . "'
                    And (p1.code = '" . $cpt_code . "' Or p2.code = '" . $cpt_code . "' Or p3.code = '" . $cpt_code . "' )";
        $sql = DB::select($qry); // or die($qry . imw_error());
        return ($sql) ? true : false;
    }

    function getSelectedOption($laterality, $severity, $staging, $icd10CodeOrg, $icd10CodeDB, $icdLater) {
        $posToFindFrom = 0; //start search from 0 index
        //get selected values for icd 10
        if (trim($laterality)) {
            //get posttition of first '-'
            $position = $posVal = '';
            $posToFindFromTMP = ($posToFindFrom) ? $posToFindFrom + 1 : 0;
            $position = strpos($icd10CodeOrg, '-', $posToFindFromTMP);
            $laterSelId = @substr($icd10CodeDB, $position, 1);
            //$laterSelTxt=$icdLater[$laterality][$laterSelId];
            //list($laterSelTxt,$laterSelCode)=explode('~:~',$laterSelTxt);
            $posToFindFrom = $position;
        }

        if (trim($severity)) {
            //get posttition of first '-'
            $position = $posVal = '';
            $posToFindFromTMP = ($posToFindFrom) ? $posToFindFrom + 1 : 0;
            $position = strpos($icd10CodeOrg, '-', $posToFindFromTMP);
            $severitySelId = substr($icd10CodeDB, $position, 1);
            //$severitySelTxt=$icdLater[$severity][$severitySelId];
            //list($severitySelTxt,$severitySelCode)=explode('~:~',$severitySelTxt);
            $posToFindFrom = $position;
        }

        if (trim($staging)) {
            //get posttition of first '-'
            $position = $posVal = '';
            $posToFindFromTMP = ($posToFindFrom) ? $posToFindFrom + 1 : 0;
            $position = strpos($icd10CodeOrg, '-', $posToFindFromTMP);
            $stagingSelId = substr($icd10CodeDB, $position, 1);
            //$stagingSelTxt=$icdLater[$staging][$stagingSelId];
            //list($stagingSelTxt,$stagingSelCode)=explode('~:~',$stagingSelTxt);
            $posToFindFrom = $position;
        }

        $return = array();
        $return['laterality'] = @$laterSelId;
        $return['severity'] = @$severitySelId;
        $return['staging'] = @$stagingSelId;

        return $return;
    }

    function changeDateMDY($dateStr = '') {
        list($yyDate, $mmDate, $ddDate) = explode('-', $dateStr);
        $showDate = $mmDate . '-' . $ddDate . '-' . $yyDate;
        return $showDate;
    }

}
