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
use App\Http\Controllers\PoeController;
class DishchargeSummarySheetController extends Controller {

    public function DishchargeSummarySheet_form(Request $request) {
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
                $allergy1 = "select * from allergies ORDER BY `allergies`.`name` ASC";
                $allergic = DB::select($allergy1);
                $detailConfirmation = $this->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfirmId);
                $finalizeStatus = $detailConfirmation->finalize_status;
                $allergiesNKDA_patientconfirmation_status = $detailConfirmation->allergiesNKDA_status;
                $noMedicationStatus = $detailConfirmation->no_medication_status;
                $noMedicationComments = $detailConfirmation->no_medication_comments;
                $dos = $detailConfirmation->dos;
                $surgeonId = $detailConfirmation->surgeonId;
                $primary_procedure_id = $detailConfirmation->patient_primary_procedure_id;
                $primary_procedure_name = $detailConfirmation->patient_primary_procedure;
                $secondary_procedure_id = $detailConfirmation->patient_secondary_procedure_id;
                $tertiary_procedure_id = $detailConfirmation->patient_tertiary_procedure_id;
                $primary_procedure_is_inj_misc = $detailConfirmation->prim_proc_is_misc;
                $surgeonId = $detailConfirmation->surgeonId;
                $anesthesiologist_id = $detailConfirmation->anesthesiologist_id;
                $ascId = $detailConfirmation->ascId;
                /*                 * ***********dischargesummarysheet***************** */
                $dischargeDetails = DB::selectone("select * from dischargesummarysheet where confirmation_id='" . $pConfirmId . "'");
                $procedures_idDBExplodeArr = array();
                $diag_idDBExplodeArr = array();
                $dischargeSummarySheetIdDB = $dischargeDetails->dischargeSummarySheetId;
                $procedures_nameDB = $dischargeDetails->procedures_name;
                $procedures_codeDB = $dischargeDetails->procedures_code_name;
                $disAttached = $dischargeDetails->disAttached;
                $otherMiscellaneousDB = $dischargeDetails->otherMiscellaneous;
                $comment = $dischargeDetails->comment;
                $dis_ScanUpload = $dischargeDetails->dis_ScanUpload;
                $dis_ScanUpload2 = $dischargeDetails->dis_ScanUpload2;
                $other1DB = $dischargeDetails->other1;
                $other2DB = $dischargeDetails->other2;
                $surgeon_knowledgeDB = $dischargeDetails->surgeon_knowledge;
                $surgeonSignDB = $dischargeDetails->surgeonSign;
                $procedures_IdDB = $dischargeDetails->procedures_code;
                $diag_idDB = $dischargeDetails->diag_ids;
                $diag_nameDB = $dischargeDetails->diag_names;

                $disclaimer_txt = stripslashes($dischargeDetails->disclaimer_txt);
                $icd10_idDB = $dischargeDetails->icd10_id;
                $icd10_codeDB = $dischargeDetails->icd10_code;
                $icd10_nameDB = $dischargeDetails->icd10_name;
                $form_status = $dischargeDetails->form_status;

                $signSurgeon1Id = $dischargeDetails->signSurgeon1Id;
                $signSurgeon1FirstName = $dischargeDetails->signSurgeon1FirstName;
                $signSurgeon1MiddleName = $dischargeDetails->signSurgeon1MiddleName;
                $signSurgeon1LastName = $dischargeDetails->signSurgeon1LastName;
                $signSurgeon1Status = $dischargeDetails->signSurgeon1Status;
                $signSurgeon1DateTime = $dischargeDetails->signSurgeon1DateTime;
                $procNameExplode = array_filter(explode("!,!", $procedures_nameDB));
                $procCodeNameExplode = array_filter(explode("##", $procedures_codeDB));
                $procedures_idDBExplodeArr = array_filter(explode(",", $procedures_IdDB));

                $procedureDBExists = false;
                if (is_array($procedures_idDBExplodeArr) && count($procedures_idDBExplodeArr) > 0) {
                    $procedureDBExists = true;
                    foreach ($procedures_idDBExplodeArr as $_key => $_val) {
                        $procNameArray[$_val] = @trim($procNameExplode[$_key]);
                        $procCodeNameArray[$_val] = @trim($procCodeNameExplode[$_key]);
                    }
                }

                $icd10_idDBExplodeArr = array_filter(explode(",", $icd10_idDB));
                $icd10_codeDBExplodeArrTemp = array_filter(explode(",", $icd10_codeDB));
                $icd10_nameDBExplodeArrTemp = array_filter(explode("@@", $icd10_nameDB));
                for ($ix = 0; $ix <= sizeof($icd10_idDBExplodeArr); $ix++) {
                    if (isset($icd10_idDBExplodeArr[$ix])) {
                        $icd10_codeDBExplodeArr[$icd10_idDBExplodeArr[$ix]] = $icd10_codeDBExplodeArrTemp[$ix];
                        $icd10_nameDBExplodeArr[$icd10_idDBExplodeArr[$ix]] = $icd10_nameDBExplodeArrTemp[$ix];
                    }
                }

                /* for($ix=0;$ix<=sizeof($diag_idDBExplodeArr);$ix++)
                  {
                  $icd10_codeDBExplodeArr[$diag_idDBExplodeArr[$ix]]=$icd10_codeDBExplodeArrTemp[$ix];
                  } */

                if ($signSurgeon1DateTime <> '0000-00-00 00:00:00') {
                    $date_surgeon = explode(' ', $signSurgeon1DateTime);
                    $date_sign = explode('-', $date_surgeon[0]);
                    $date_surgeon_sign = $date_sign[1] . '/' . $date_sign[2] . '/' . $date_sign[0];
                }
                if ($form_status == 'completed') {
                    $display_tr = "block";
                } else {
                    $display_tr = "none";
                }
                /*                 * *************************** */
                //GET ASSIGNED SURGEON ID AND SURGEON NAME
                $dischargeSummaryAssignedSurgeonId = $detailConfirmation->surgeonId;
                $dischargeSummaryAssignedSurgeonName = stripslashes($detailConfirmation->surgeon_name);
                $surgeryTime = $detailConfirmation->surgery_time;
                $dos = $detailConfirmation->dos;
                $import_status = $detailConfirmation->import_status;
                $surgerySite = $detailConfirmation->site;
                $secondarySite = $detailConfirmation->secondary_site;
                $secondarySite = ($secondarySite) ? $secondarySite : $surgerySite;
                $tertiarySite = $detailConfirmation->tertiary_site;
                $tertiarySite = ($tertiarySite) ? $tertiarySite : $surgerySite;
                // Get Modifiers to autofill based upon Site and Procedure POE Period
                $sitePoeMod = $this->proc_site_modifiers($patient_id, $dos, $surgerySite, $primary_procedure_id, $secondarySite, $secondary_procedure_id, $tertiarySite, $tertiary_procedure_id);

                if ($surgerySite == 1) {
                    $surgerySite = "Left Eye";  //OS
                } else if ($surgerySite == 2) {
                    $surgerySite = "Right Eye";  //OD
                } else if ($surgerySite == 3) {
                    $surgerySite = "Both Eye";  //OU
                } else if ($surgerySite == 4) {
                    $surgerySite = "Left Upper Lid";
                } else if ($surgerySite == 5) {
                    $surgerySite = "Left Lower Lid";
                } else if ($surgerySite == 6) {
                    $surgerySite = "Right Upper Lid";
                } else if ($surgerySite == 7) {
                    $surgerySite = "Right Lower Lid";
                } else if ($surgerySite == 8) {
                    $surgerySite = "Bilateral Upper Lid";
                } else if ($surgerySite == 9) {
                    $surgerySite = "Bilateral Lower Lid";
                }

                if ($secondarySite == 1) {
                    $secondarySite = "Left Eye";  //OS
                } else if ($secondarySite == 2) {
                    $secondarySite = "Right Eye";  //OD
                } else if ($secondarySite == 3) {
                    $secondarySite = "Both Eye";  //OU
                } else if ($secondarySite == 4) {
                    $secondarySite = "Left Upper Lid";
                } else if ($secondarySite == 5) {
                    $secondarySite = "Left Lower Lid";
                } else if ($secondarySite == 6) {
                    $secondarySite = "Right Upper Lid";
                } else if ($secondarySite == 7) {
                    $secondarySite = "Right Lower Lid";
                } else if ($secondarySite == 8) {
                    $secondarySite = "Bilateral Upper Lid";
                } else if ($secondarySite == 9) {
                    $secondarySite = "Bilateral Lower Lid";
                }

                if ($tertiarySite == 1) {
                    $tertiarySite = "Left Eye";  //OS
                } else if ($tertiarySite == 2) {
                    $tertiarySite = "Right Eye";  //OD
                } else if ($tertiarySite == 3) {
                    $tertiarySite = "Both Eye";  //OU
                } else if ($tertiarySite == 4) {
                    $tertiarySite = "Left Upper Lid";
                } else if ($tertiarySite == 5) {
                    $tertiarySite = "Left Lower Lid";
                } else if ($tertiarySite == 6) {
                    $tertiarySite = "Right Upper Lid";
                } else if ($tertiarySite == 7) {
                    $tertiarySite = "Right Lower Lid";
                } else if ($tertiarySite == 8) {
                    $tertiarySite = "Bilateral Upper Lid";
                } else if ($tertiarySite == 9) {
                    $tertiarySite = "Bilateral Lower Lid";
                }
                $patientAllergies = [];
                $cntHlt = 0;
                $patient_allergies_tblQry = "SELECT pre_op_allergy_id,allergy_name,reaction_name FROM `patient_allergies_tbl` WHERE `patient_confirmation_id` = '$pConfirmId'";
                $patient_allergies_tblRes = DB::select($patient_allergies_tblQry);
                if ($patient_allergies_tblRes) {
                    foreach ($patient_allergies_tblRes as $patient_allergies_tblRow) {
                        $patientAllergies[] = ['pre_op_allergy_id' => $patient_allergies_tblRow->pre_op_allergy_id, 'name' => $patient_allergies_tblRow->allergy_name, 'reaction' => $patient_allergies_tblRow->reaction_name];
                        $cntHlt++;
                    }
                }

                $printSuperBills = array('Surgeon_S_2', 'Facility_F_3', 'Anesthesia_A_1');
                $superBillHtml = '';
                $superBills = array();
                foreach ($printSuperBills as $object) {
                    $arr = explode('_', $object);
                    $buTitle = $arr[0];
                    $buStr = $arr[1];
                    $buType = $arr[2];
                    $qrySuperbill = "SELECT sb.* FROM superbill_tbl sb 
                                        INNER JOIN procedures pr ON(pr.procedureId = sb.cpt_id)
                                        INNER JOIN procedurescategory prc ON(prc.proceduresCategoryId = pr.catId)
                                        WHERE sb.confirmation_id = '" . $pConfirmId . "'
                                        AND sb.deleted = '0'
                                        And bill_user_type = '" . $buType . "'
                                        ORDER BY prc.name = 'G-Codes' DESC, sb.cpt_code";
                    $resSuperbill = DB::select($qrySuperbill);
                    if ($resSuperbill) {
                        foreach ($resSuperbill as $rowSuperbill) {
                            $superBills[$buTitle][] = $rowSuperbill;
                        }
                    } else {
                        $superBills[$buTitle] = [];
                    }
                }


                $chk_signSurgeon1Id = isset($dischargeDetails->signSurgeon1Id) ? $dischargeDetails->signSurgeon1Id : 0;
                //CHECK FORM STATUS
                $chk_form_status = isset($dischargeDetails->form_status) ? $dischargeDetails->form_status : '';
                //CHECK FORM STATUS
                // $chk_dis_ScanUpload = isset($res->dis_ScanUpload) ? $res->dis_ScanUpload : '';
                // $chk_dis_ScanUpload2 = isset($res->dis_ScanUpload2) ? $res->dis_ScanUpload2 : "";
                $diag_idDB = isset($dischargeDetails->diag_ids) ? $dischargeDetails->diag_ids : 0;
                $diag_nameDB = isset($dischargeDetails->diag_names) ? $dischargeDetails->diag_names : '';
                $icd10_idDB = isset($dischargeDetails->icd10_id) ? $dischargeDetails->icd10_id : 0;
                $icd10_codeDB = isset($dischargeDetails->icd10_code) ? $dischargeDetails->icd10_code : '';
                $icd10_nameDB = isset($dischargeDetails->icd10_name) ? $dischargeDetails->icd10_name : '';

                $iol_ScanUpload = '';
                $iol_ScanUpload2 = '';
                $operatingRoomRecordsId = 0;
                $ViewOpRoomRecordQry = "select operatingRoomRecordsId,iol_ScanUpload,iol_ScanUpload2,post2DischargeSummary from `operatingroomrecords` where  confirmation_id = '" . $pConfirmId . "'";
                $ViewOpRoomRecordRes = DB::selectone($ViewOpRoomRecordQry); // or die('Error @ Line :' . (__LINE__) . ':-' . imw_error());
                if ($ViewOpRoomRecordRes) {
                    $ViewOpRoomRecordRow = $ViewOpRoomRecordRes;
                    $operatingRoomRecordsId = $ViewOpRoomRecordRow->operatingRoomRecordsId;
                    // $iol_ScanUpload = $ViewOpRoomRecordRow->iol_ScanUpload;
                    //  $iol_ScanUpload2 = $ViewOpRoomRecordRow->iol_ScanUpload2;
                    $post2DischargeSummary = $ViewOpRoomRecordRow->post2DischargeSummary;
                }
                $siteArr = [];
                //get site code
                $getSiteCodeQ = DB::select("SELECT id FROM `icd10_laterality` where title in ('Site','Site with Lid')");
                $siteCatIdArr = array();
                foreach ($getSiteCodeQ as $getSiteCodeD) {
                    $siteCatIdArr[] = $getSiteCodeD->id;
                }
                $siteCatIdImp = '1';
                if (count($siteCatIdArr) > 0) {
                    $siteCatIdImp = implode(",", $siteCatIdArr);
                }
                //get site under codes
                $getSiteCodeUQ = DB::select("SELECT code,title FROM  `icd10_laterality` where under in (" . $siteCatIdImp . ") and deleted=0");
                foreach ($getSiteCodeUQ as $getSiteCodeUD) {
                    $siteCode = $getSiteCodeUD->code;
                    $siteCodeTitle = strtolower($getSiteCodeUD->title);

                    $siteArr[strtolower($getSiteCodeUD->title)] = $siteCode;
                }

                //get typ of dx codes
                $dataDxTyp = DB::selectone("select `diagnosis_code_type`, discharge_disclaimer, autofill_modifiers from `surgerycenter` Where surgeryCenterId = 1"); //or die('Error @ Line :' . (__LINE__) . ':-' . imw_error());
                $autofill_modifiers = $dataDxTyp->autofill_modifiers;
                if ($dataDxTyp->diagnosis_code_type) {
                    $dx_code_type = $dataDxTyp->diagnosis_code_type;
                } else {
                    $dx_code_type = 'icd9';
                }
                $defaultDischargeTxt = stripslashes($dataDxTyp->discharge_disclaimer);

                if (trim($diag_idDB) != '' || ($dos <= '2015-09-30' && trim($icd10_idDB) == '')) {
                    $dx_code_type = 'icd9';
                } else if (trim($icd10_idDB) != '') {
                    $dx_code_type = 'icd10';
                }

                if ($dx_code_type == 'icd10') {
                    $query = DB::select("SELECT code,title,under FROM `icd10_laterality` where under!='' and deleted=0 Order By code");
                    foreach ($query as $data) {
                        $icdLater[$data->under][$data->code] = $data->title . '~:~' . $data->code;
                    }
                }

                $cpt_id = '';
                $cpt_id_anes = '';
                $cpt_id_default = '';
                $cpt_id_anes_default = '';
                $dischargeSheetFound = "false";
                $primProcDxCodes = $secProcDxCodes = $terProcDxCodes = '';
                //GETTING SURGEON PROFILE FOR PRIMARY PROCEDURE
                //$cpt_id = $_REQUEST['show_td'];
                $cpt_id_arr = $cpt_id_default_arr = $cpt_id_anes_arr = $cpt_id_anes_default_arr = $dx_id_arr = $dx_id_default_arr = $dx_id_icd10_arr = $dx_id_default_icd10_arr = array();
                if ($surgeonId <> "") {
                    $selectSurgeonQry = "select * from surgeonprofile where surgeonId = '$surgeonId' and del_status=''";
                    $selectSurgeonRes = DB::select($selectSurgeonQry); // or die('Error @ Line :' . (__LINE__) . ':-' . imw_error());
                    foreach ($selectSurgeonRes as $selectSurgeonRow) {
                        $surgeonProfileIdArr[] = $selectSurgeonRow->surgeonProfileId;
                    }
                    if (is_array($surgeonProfileIdArr)) {
                        $surgeonProfileIdImplode = implode(',', $surgeonProfileIdArr);
                    } else {
                        $surgeonProfileIdImplode = 0;
                    }
                    $selectSurgeonProcedureQry = "select * from surgeonprofileprocedure where procedureId !=  '0' AND profileId in ($surgeonProfileIdImplode) ORDER BY procedureId =  '" . $primary_procedure_id . "' DESC , procedureId =  '" . $secondary_procedure_id . "' DESC , procedureId =  '" . $tertiary_procedure_id . "' DESC , procedureName";
                    $selectSurgeonProcedureRes = DB::select($selectSurgeonProcedureQry); // or die('Error @ Line :' . (__LINE__) . ':-' . imw_error());
                    if ($selectSurgeonProcedureRes) {
                        foreach ($selectSurgeonProcedureRes as $selectSurgeonProcedureRow) {
                            $surgeonProfileProcedureId = $selectSurgeonProcedureRow->procedureId;
                            if ($primary_procedure_id == $surgeonProfileProcedureId || $secondary_procedure_id == $surgeonProfileProcedureId || $tertiary_procedure_id == $surgeonProfileProcedureId) {
                                $dischargeSheetFound = "true";
                                $cpt_id_arr[] = $selectSurgeonProcedureRow->cpt_id;
                                $cpt_id_default_arr[] = $selectSurgeonProcedureRow->cpt_id_default;

                                $cpt_id_anes_arr[] = $selectSurgeonProcedureRow->cpt_id_anes;
                                $cpt_id_anes_default_arr[] = $selectSurgeonProcedureRow->cpt_id_anes_default;

                                $dx_id_arr[] = $selectSurgeonProcedureRow->dx_id;
                                $dx_id_default_arr[] = $selectSurgeonProcedureRow->dx_id_default;

                                $dx_id_icd10_arr[] = $selectSurgeonProcedureRow->dx_id_icd10;
                                $dx_id_default_icd10_arr[] = $selectSurgeonProcedureRow->dx_id_default_icd10;

                                if ($tertiary_procedure_id == $surgeonProfileProcedureId) {
                                    $terProcDxCodes = $selectSurgeonProcedureRow->dx_id_icd10;
                                }

                                if ($secondary_procedure_id == $surgeonProfileProcedureId) {
                                    $secProcDxCodes = $selectSurgeonProcedureRow->dx_id_icd10;
                                }

                                if ($primary_procedure_id == $surgeonProfileProcedureId) {
                                    $primProcDxCodes = $selectSurgeonProcedureRow->dx_id_icd10;
                                }
                            }
                        }
                    }
                    if (count($cpt_id_arr) > 0 || count($cpt_id_anes_arr) > 0 || count($dx_id_arr) > 0 || count($dx_id_icd10_arr) > 0) {
                        $cpt_id = implode(",", array_filter(array_unique(explode(",", implode(",", $cpt_id_arr)))));
                        $cpt_id_default = implode(",", array_filter(array_unique(explode(",", implode(",", $cpt_id_default_arr)))));
                        $cpt_id_anes = implode(",", array_filter(array_unique(explode(",", implode(",", $cpt_id_anes_arr)))));
                        $cpt_id_anes_default = implode(",", array_filter(array_unique(explode(",", implode(",", $cpt_id_anes_default_arr)))));
                        $dx_id = implode(",", array_filter(array_unique(explode(",", implode(",", $dx_id_arr)))));
                        $dx_id_default = implode(",", array_filter(array_unique(explode(",", implode(",", $dx_id_default_arr)))));
                        $dx_id_icd10 = implode(",", array_filter(array_unique(explode(",", implode(",", $dx_id_icd10_arr)))));
                        $dx_id_default_icd10 = implode(",", array_filter(array_unique(explode(",", implode(",", $dx_id_default_icd10_arr)))));
                    }

                    $cpt_id = $cpt_id . (($cpt_id && $cpt_id_anes) ? ',' : '') . $cpt_id_anes;
                    $cpt_id_default = $cpt_id_default . (($cpt_id_default && $cpt_id_anes_default) ? ',' : '') . $cpt_id_anes_default;
                    //IF PATIENT PRIMARY PROCEDURE DOES NOT EXISTS IN SURGEON PROFILE THEN SELECT OPERATIVE TEMPLATE
                    //FROM SURGEON'S DEFAULT PROFILE  
                }
                /*                 * ***
                 * Start Procedure Preference Card 
                 * *** */
                //$pro_cpt_id_arr = $pro_cpt_id_default_arr = $pro_cpt_id_anes_arr = $pro_cpt_id_anes_default_arr = $pro_dx_id_arr = $pro_dx_id_default_arr = $pro_dx_id_icd10_arr = $pro_dx_id_default_icd10_arr = array();
                $pro_cpt_id = $pro_cpt_id_default = $pro_cpt_id_anes = $pro_cpt_id_anes_default = $pro_dx_id = $pro_dx_id_default = $pro_dx_id_icd10 = $pro_dx_id_default_icd10 = '';
                if ($dischargeSheetFound <> "true") {
                    $proceduresArr = array($primary_procedure_id, $secondary_procedure_id, $tertiary_procedure_id);
                    foreach ($proceduresArr as $procedureId) {
                        if ($procedureId) {
                            $procPrefCardQry = "Select * From procedureprofile Where procedureId !=  '0' AND procedureId = '" . $procedureId . "' ";
                            $procPrefCardRow = DB::selectone($procPrefCardQry); // or die('Error at line no.' . (__LINE__) . ': ' . imw_error());
                            if ($procPrefCardRow) {
                                $pro_cpt_id .= ',' . $procPrefCardRow->cpt_id;
                                $pro_cpt_id_default .= ',' . $procPrefCardRow->cpt_id_default;
                                $pro_cpt_id_anes .= ',' . $procPrefCardRow->cpt_id_anes;
                                $pro_cpt_id_anes_default.= ',' . $procPrefCardRow->cpt_id_anes_default;
                                $pro_dx_id .= ',' . $procPrefCardRow->dx_id;
                                $pro_dx_id_default .= ',' . $procPrefCardRow->dx_id_default;
                                $pro_dx_id_icd10 .= ',' . $procPrefCardRow->dx_id_icd10;
                                $pro_dx_id_default_icd10.= ',' . $procPrefCardRow->dx_id_default_icd10;

                                if ($tertiary_procedure_id == $procPrefCardRow->procedureId) {
                                    $terProcDxCodes = $procPrefCardRow->dx_id_icd10;
                                }

                                if ($secondary_procedure_id == $procPrefCardRow->procedureId) {
                                    $secProcDxCodes = $procPrefCardRow->dx_id_icd10;
                                }

                                if ($primary_procedure_id == $procPrefCardRow->procedureId) {
                                    $primProcDxCodes = $procPrefCardRow->dx_id_icd10;
                                }
                            }
                        }
                    }

                    $cpt_id = implode(",", array_unique(array_filter(explode(",", $pro_cpt_id))));
                    $cpt_id_default = implode(",", array_unique(array_filter(explode(",", $pro_cpt_id_default))));
                    $cpt_id_anes = implode(",", array_unique(array_filter(explode(",", $pro_cpt_id_anes))));
                    $cpt_id_anes_default = implode(",", array_unique(array_filter(explode(",", $pro_cpt_id_anes_default))));
                    $dx_id = implode(",", array_unique(array_filter(explode(",", $pro_dx_id))));
                    $dx_id_default = implode(",", array_unique(array_filter(explode(",", $pro_dx_id_default))));
                    $dx_id_icd10 = implode(",", array_unique(array_filter(explode(",", $pro_dx_id_icd10))));
                    $dx_id_default_icd10 = implode(",", array_unique(array_filter(explode(",", $pro_dx_id_default_icd10))));

                    $cpt_id = $cpt_id . (($cpt_id && $cpt_id_anes) ? ',' : '') . $cpt_id_anes;
                    $cpt_id_default = $cpt_id_default . (($cpt_id_default && $cpt_id_anes_default) ? ',' : '') . $cpt_id_anes_default;
                }
                /*                 * ***
                 * End Procedure Preference Card 
                 * *** */
                // Check If Procedure is Injection Procedure
                $primProcDetails = $this->getRowRecord('procedures', 'procedureId', $primary_procedure_id, '', '', 'catId');
                if ($primProcDetails->catId <> '2') {
                    if ($primary_procedure_is_inj_misc == '') {
                //$chkprocedurecatDetails = $objManageData->getRowRecord('procedurescategory', 'proceduresCategoryId', $primProcDetails->catId);
                        $primary_procedure_is_inj_misc = $this->verifyProcIsInjMisc($primary_procedure_id);
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
                    $procedureDetails = array($primary_procedure_id, $secondary_procedure_id, $tertiary_procedure_id);
                    if (is_array($procedureDetails) && count($procedureDetails) > 0) {
                        $injMiscCptIdArr = $injMiscDefaultCptIdArr = $injMiscDxIdArr = $injMiscDefaultDxIdArr = $injMiscDxIcd10IdArr = $injMiscDefaultDxIcd10IdArr = array();
                        $injMiscCptId = $injMiscDefaultCptId = $injMiscDxId = $injMiscDefaultDxId = $injMiscDxIcd10Id = $injMiscDefaultDxIcd10Id = '';
                        foreach ($procedureDetails as $procedureID) {
                            $fields = 'procedureID,cpt_id,cpt_id_default,dx_id,dx_id_default,dx_id_icd10,dx_id_default_icd10';
                            $defaultProfile = $this->injectionProfile($procedureID, $detailConfirmation->surgeonId, $fields);

                            if ($defaultProfile['profileFound']) {
                                $injMiscCptIdArr[] = $defaultProfile['data']['cpt_id'];
                                $injMiscDefaultCptIdArr[] = $defaultProfile['data']['cpt_id_default'];
                                $injMiscDxIdArr[] = $defaultProfile['data']['dx_id'];
                                $injMiscDefaultDxIdArr[] = $defaultProfile['data']['dx_id_default'];
                                $injMiscDxIcd10IdArr[] = $defaultProfile['data']['dx_id_icd10'];
                                $injMiscDefaultDxIcd10IdArr[] = $defaultProfile['data']['dx_id_default_icd10'];

                                if ($tertiary_procedure_id == $defaultProfile['data']['procedureID']) {
                                    $terProcDxCodes = $defaultProfile['data']['dx_id_icd10'];
                                }
                                if ($secondary_procedure_id == $defaultProfile['data']['procedureID']) {
                                    $secProcDxCodes = $defaultProfile['data']['dx_id_icd10'];
                                }

                                if ($primary_procedure_id == $defaultProfile['data']['procedureID']) {
                                    $primProcDxCodes = $defaultProfile['data']['dx_id_icd10'];
                                }
                            }
                        }

                        if (count($injMiscCptIdArr) > 0 || count($injMiscDxIdArr) > 0 || count($injMiscDxIcd10IdArr) > 0) {
                            $injMiscCptId = implode(",", array_filter(array_unique(explode(",", implode(",", $injMiscCptIdArr)))));
                            $injMiscDefaultCptId = implode(",", array_filter(array_unique(explode(",", implode(",", $injMiscDefaultCptIdArr)))));
                            $injMiscDxId = implode(",", array_filter(array_unique(explode(",", implode(",", $injMiscDxIdArr)))));
                            $injMiscDefaultDxId = implode(",", array_filter(array_unique(explode(",", implode(",", $injMiscDefaultDxIdArr)))));
                            $injMiscDxIcd10Id = implode(",", array_filter(array_unique(explode(",", implode(",", $injMiscDxIcd10IdArr)))));
                            $injMiscDefaultDxIcd10Id = implode(",", array_filter(array_unique(explode(",", implode(",", $injMiscDefaultDxIcd10IdArr)))));
                        }

                        $cpt_id = ($injMiscCptId) ? $injMiscCptId : $cpt_id;
                        $cpt_id_default = ($injMiscDefaultCptId) ? $injMiscDefaultCptId : $cpt_id_default;
                        $dx_id = ($injMiscDxId) ? $injMiscDxId : $dx_id;
                        $dx_id_default = ($injMiscDefaultDxId) ? $injMiscDefaultDxId : $dx_id_default;
                        $dx_id_icd10 = ($injMiscDxIcd10Id) ? $injMiscDxIcd10Id : $dx_id_icd10;
                        $dx_id_default_icd10 = ($injMiscDefaultDxIcd10Id) ? $injMiscDefaultDxIcd10Id : $dx_id_default_icd10;
                    }
                }
                /*                 * ****************************************
                  End Injection/Misc. Procedure Template
                 * **************************************** */
                /*                 * ****************************************
                  Start Laser Procedure Template
                 * **************************************** */
                $xtraCondition = '';
                if ($primProcDetails->catId == '2') {
                    unset($condArr);
                    $condArr['1'] = '1';
                    $xtraCondition = " And catId = '2' And procedureId IN (" . $primary_procedure_id . "" .
                            $xtraCondition .= ($secondary_procedure_id) ? ',' . $secondary_procedure_id : '';
                    $xtraCondition .= ($tertiary_procedure_id) ? ',' . $tertiary_procedure_id : '';
                    $xtraCondition .= ")";
                    $procedureDetails = $this->getMultiChkArrayRecords('procedures', $condArr, '', '', $xtraCondition);

                    if (is_array($procedureDetails) && count($procedureDetails) > 0) {
                        $laserCptIdArr = $laserDefaultCptIdArr = $laserDxIdArr = $laserDefaultDxIdArr = $laserDxIcd10IdArr = $laserDefaultDxIcd10IdArr = array();
                        $laserCptId = $laserDefaultCptId = $laserDxId = $laserDefaultDxId = $laserDxIcd10Id = $laserDefaultDxIcd10Id = '';
                        foreach ($procedureDetails as $key => $procData) {
                            $laserProcTempQry = "SELECT * FROM laser_procedure_template WHERE laser_procedureID = '" . $procData->procedureId . "' And (FIND_IN_SET(" . $detailConfirmation->surgeonId . ",laser_surgeonID)) ORDER BY laser_templateID DESC LIMIT 0,1 ";
                            $laserProcTempSql = DB::select($laserProcTempQry); // or die('Error found at line no ' . (__LINE_) . ': ' . imw_error());

                            if (!$laserProcTempSql) {
                                $laserProcTempQry = "SELECT * FROM laser_procedure_template WHERE laser_procedureID = '" . $procData->procedureId . "' And laser_surgeonID = 'all' ORDER BY laser_templateID DESC LIMIT 0,1 ";
                                $laserProcTempSql = DB::select($laserProcTempQry); // or die('Error found at line no. ' . (__LINE_) . ': ' . imw_error());
                            }

                            if ($laserProcTempSql) {
                                foreach ($laserProcTempSql as $laserProcTempRow) {
                                    $procSurgeonId = $detailConfirmation->surgeonId;
                                    $laserSurgeon = $laserProcTempRow->laser_surgeonID;

                                    if ($laserSurgeon != "all") {
                                        $laserSurgeonExplode = explode(",", $laserSurgeon);
                                        $laserSurgeonCount = count($laserSurgeonExplode);

                                        if ($laserSurgeonCount == 1) {
                                            if ($procSurgeonId == $laserSurgeon) {
                                                $laserCptIdArr[] = $laserProcTempRow->cpt_id;
                                                $laserDefaultCptIdArr[] = $laserProcTempRow->cpt_id_default;
                                                $laserDxIdArr[] = $laserProcTempRow->dx_id;
                                                $laserDefaultDxIdArr[] = $laserProcTempRow->dx_id_default;
                                                $laserDxIcd10IdArr[] = $laserProcTempRow->dx_id_icd10;
                                                $laserDefaultDxIcd10IdArr[] = $laserProcTempRow->dx_id_default_icd10;

                                                if ($tertiary_procedure_id == $procData->procedureId) {
                                                    $terProcDxCodes = $laserProcTempRow->dx_id_icd10;
                                                }
                                                if ($secondary_procedure_id == $procData->procedureId) {
                                                    $secProcDxCodes = $laserProcTempRow->dx_id_icd10;
                                                }

                                                if ($primary_procedure_id == $procData->procedureId) {
                                                    $primProcDxCodes = $laserProcTempRow->dx_id_icd10;
                                                }

                                                continue;
                                            }
                                        }

                                        $matchedSurgeon = false;
                                        if ($laserSurgeonCount > 1) {
                                            for ($i = 0; $i < $laserSurgeonCount; $i++) {
                                                $match_surgeonid = $procSurgeonId;
                                                $surgeon = $laserSurgeonExplode[$i];
                                                if ($surgeon == $match_surgeonid) {
                                                    $matchedSurgeon = true;
                                                    $laserCptIdArr[] = $laserProcTempRow->cpt_id;
                                                    $laserDefaultCptIdArr[] = $laserProcTempRow->cpt_id_default;
                                                    $laserDxIdArr[] = $laserProcTempRow->dx_id;
                                                    $laserDefaultDxIdArr[] = $laserProcTempRow->dx_id_default;
                                                    $laserDxIcd10IdArr[] = $laserProcTempRow->dx_id_icd10;
                                                    $laserDefaultDxIcd10IdArr[] = $laserProcTempRow->dx_id_default_icd10;

                                                    if ($tertiary_procedure_id == $procData->procedureId) {
                                                        $terProcDxCodes = $laserProcTempRow->dx_id_icd10;
                                                    }
                                                    if ($secondary_procedure_id == $procData->procedureId) {
                                                        $secProcDxCodes = $laserProcTempRow->dx_id_icd10;
                                                    }

                                                    if ($primary_procedure_id == $procData->procedureId) {
                                                        $primProcDxCodes = $laserProcTempRow->dx_id_icd10;
                                                    }

                                                    continue;
                                                }
                                            }
                                        }
                                    } else {
                                        $laserCptIdArr[] = $laserProcTempRow->cpt_id;
                                        $laserDefaultCptIdArr[] = $laserProcTempRow->cpt_id_default;
                                        $laserDxIdArr[] = $laserProcTempRow->dx_id;
                                        $laserDefaultDxIdArr[] = $laserProcTempRow->dx_id_default;
                                        $laserDxIcd10IdArr[] = $laserProcTempRow->dx_id_icd10;
                                        $laserDefaultDxIcd10IdArr[] = $laserProcTempRow->dx_id_default_icd10;

                                        if ($tertiary_procedure_id == $procData->procedureId) {
                                            $terProcDxCodes = $laserProcTempRow->dx_id_icd10;
                                        }
                                        if ($secondary_procedure_id == $procData->procedureId) {
                                            $secProcDxCodes = $laserProcTempRow->dx_id_icd10;
                                        }

                                        if ($primary_procedure_id == $procData->procedureId) {
                                            $primProcDxCodes = $laserProcTempRow->dx_id_icd10;
                                        }
                                    }
                                }
                            }
                        }

                        if (count($laserCptIdArr) > 0 || count($laserDxIdArr) > 0 || count($laserDxIcd10IdArr) > 0) {
                            $laserCptId = implode(",", array_filter(array_unique(explode(",", implode(",", $laserCptIdArr)))));
                            $laserDefaultCptId = implode(",", array_filter(array_unique(explode(",", implode(",", $laserDefaultCptIdArr)))));
                            $laserDxId = implode(",", array_filter(array_unique(explode(",", implode(",", $laserDxIdArr)))));
                            $laserDefaultDxId = implode(",", array_filter(array_unique(explode(",", implode(",", $laserDefaultDxIdArr)))));
                            $laserDxIcd10Id = implode(",", array_filter(array_unique(explode(",", implode(",", $laserDxIcd10IdArr)))));
                            $laserDefaultDxIcd10Id = implode(",", array_filter(array_unique(explode(",", implode(",", $laserDefaultDxIcd10IdArr)))));
                        }

                        $cpt_id = ($laserCptId) ? $laserCptId : $cpt_id;
                        $cpt_id_default = ($laserDefaultCptId) ? $laserDefaultCptId : $cpt_id_default;
                        $dx_id = ($laserDxId) ? $laserDxId : $dx_id;
                        $dx_id_default = ($laserDefaultDxId) ? $laserDefaultDxId : $dx_id_default;
                        $dx_id_icd10 = ($laserDxIcd10Id) ? $laserDxIcd10Id : $dx_id_icd10;
                        $dx_id_default_icd10 = ($laserDefaultDxIcd10Id) ? $laserDefaultDxIcd10Id : $dx_id_default_icd10;
                    }
                }
                $diag_idDBExplodeArr = array_filter(explode(",", $diag_idDB));
                $diag_idDBExplodeArrTemp = array_filter(explode("@@", $diag_nameDB));
                $diag_nameDBExplodeArr = array();
                if (is_array($diag_idDBExplodeArr) && count($diag_idDBExplodeArr) > 0) {
                    foreach ($diag_idDBExplodeArr as $_key => $diagID) {
                        $diag_nameDBExplodeArr[$diagID] = $diag_idDBExplodeArrTemp[$_key];
                    }
                }
//start code to pre-select the diagnosis-icd9 code according to sugeon profile
                if ($dx_id_default && (count($diag_idDBExplodeArr) <= 0) || ($form_status != 'completed' && $form_status != 'not completed')) {
                    $diag_idDBExplodeArr = explode(",", $dx_id_default);
                }

//start code to pre-select the diagnosis-icd10 code according to sugeon profile
                if ($dx_id_default_icd10 && (count($icd10_idDBExplodeArr) <= 0) || ($form_status != 'completed' && $form_status != 'not completed')) {
                    $icd10_idDBExplodeArr = explode(",", $dx_id_default_icd10);
                    $default_icd_10 = true;
                } else {
                    $default_icd_10 = false;
                }
                unset($condArr);
                $condArr['1'] = '1';
                $condArr['deleted'] = 0;
                $primaryField = 'id';
                $ICD10CodesArray = [];
                if ($dx_id_icd10) {
                    $ICD10CodesData = $this->getMultiChkArrayRecords('icd10_data', $condArr, 'icd10,icd10_desc', 'Asc', " AND id NOT IN($dx_id_icd10)");
                    if ($ICD10CodesData) {
                        foreach ($ICD10CodesData as $ICD10) {
                            $dataArray = array();
                            foreach ($ICD10 as $fieldName => $fieldValue) {
                                $dataArray[$fieldName] = $fieldValue;
                            }
                            $ICD10CodesArray[$ICD10->id] = $dataArray;
                        }
                    }
                }
//print '<pre>';
// print_r($icdLater);
                /*                 * ****************************************
                  End Laser Procedure Template
                 * **************************************** */
                if ($dx_code_type == 'icd10') {
                    $icd10Qry = "";
                    if ($dx_id_icd10) {
                        $dx_id_icd10 = ($icd10_idDB) ? implode(',', array_filter(array_unique(explode(",", $dx_id_icd10 . ',' . $icd10_idDB)))) : $dx_id_icd10;
                        $icd10Qry = " AND id IN(" . $dx_id_icd10 . ") ";
                    }
//  unset($getDiagnosisDetailsTemp);
//
                    //code to get ICD10 related to ICD9
                    $icd10Query = DB::select("select * from icd10_data WHERE 1=1 AND icd10 !='' " . $icd10Qry . " order by icd10 asc");
                    foreach ($icd10Query as $icd10Data) {
                        $icd10_codes = explode(",", $dischargeDetails->icd10_code);
                        $diagnosisId = $icd10Data->id;
                        $icd10Data->laterality = (int) $icd10Data->laterality;
                        $icd10Data->staging = (int) $icd10Data->staging;
                        $icd10Data->severity = (int) $icd10Data->severity;
                        $diagnosisCode = trim($icd10Data->icd10);
                        $diagnosisDesc = trim($icd10Data->icd10_desc);
                        $icd10_org = $diagnosisCode;
                        $icd_10_win = $site_replaced = $allowMultiple = false;
                        $icd_10_win = stristr($diagnosisCode, '-');
                        $icd10_org = $diagnosisCode;
                        $allowMultiple = 0;
                        $icd10Data->allowMultiple = 0;
                        if ($icd_10_win && $icd10Data->laterality == 2) {
                            $allowMultiple = 1;
                            $icd10Data->allowMultiple = 1;
                        }
                        $icd10Sel = '';
                        if (in_array($diagnosisId, $icd10_idDBExplodeArr) && $default_icd_10 == false) {
                            $icd10Sel = $icd10_codeDBExplodeArr[$diagnosisId];
                        } else {
                            $icd10Sel = $diagnosisCode;
                        }
//                        if ($icd_10_win) {
//                            $icd10SelArr = explode('@@', $icd10Sel);
//                            if (is_array($icd10SelArr) && count($icd10SelArr) > 0) {
//                                foreach ($icd10SelArr as $index => $icd10Sel) {
                        if (in_array($icd10Sel, $icd10_codes)) {
                            $icd10Data->ICD10SelectedFlag = 'Yes';
                        } else {
                            $icd10Data->ICD10SelectedFlag = 'No';
                        }
                        if ($allowMultiple) {
                        //echo $diagnosisCode."^^";
                        }
                        //    echo $diagnosisId."::".implode(",",$procedures_codes);;

                        $icd10SelHtml = implode(', ', explode('@@', $icd10Sel));
                        $matchWith = $this->getSelectedOption($icd10Data->laterality, $icd10Data->severity, $icd10Data->staging, $icd10_org, $icd10Sel, $icdLater);
                        // $icd10Data->selectedflag = in_array($diagnosisId, $procedures_codes) ? 1 : 0;
                        $icd10Data->icd9 = trim($icd10Data->icd9);
                        $Icd10[$icd10Data->icd9]['icd10'] = trim($icd10Data->icd10);
                        $Icd10[$icd10Data->icd9]['laterality'] = (int) trim($icd10Data->laterality) ? $icd10Data->laterality : 0;
                        $Icd10[$icd10Data->icd9]['staging'] = (int) trim($icd10Data->staging) ? $icd10Data->staging : 0;
                        $Icd10[$icd10Data->icd9]['severity'] = (int) trim($icd10Data->severity) ? $icd10Data->severity : 0;
                        $test = '';
                        if (trim($icd10Data->laterality)) {
                            $siteToWork = $surgerySite;
                            if ($diagnosisId == $primProcDxCodes) {
                                $siteToWork = $surgerySite;
                            } elseif ($diagnosisId == $secProcDxCodes) {
                                $siteToWork = $secondarySite;
                            } elseif ($diagnosisId == $terProcDxCodes) {
                                $siteToWork = $tertiarySite;
                            }
                            if (strtolower($siteToWork) == 'left eye') {
                                $site = 'left';
                            } elseif (strtolower($siteToWork) == 'right eye') {
                                $site = 'right';
                            } elseif (strtolower($siteToWork) == 'both eye') {
                                $site = 'both';
                            } else {
                                $site = trim(strtolower($siteToWork));
                            }
                            if ($site) {
                                $under = 1;
                                if (strpos($site, 'lid'))
                                    $under = 2;

                                if ($codeToReplace = $siteArr[$site]) {
                                    if ($icd10Data->laterality == $under) {
                                        $diagnosisCode = preg_replace('/-/', $codeToReplace, $diagnosisCode, 1);
                                        $site_replaced = true;
                                    }
                                }
                            }//print'<pre>';print_r($siteArr);
                            $char_to_replace = 0; //get which '-' to replace
                            //echo'Site';
                            $siteCounter = 0;
                            //print'<pre>';print_r($siteArr);echo 'hi '.$site;
                            $site_title = [];
                            $Staging_title = [];
                            $cntr = 0;

                            foreach ($icdLater[$icd10Data->laterality] as $key => $value) {
                                $cntr++;
                                list($title, $code) = explode('~:~', $value);
                                $selClass = ($siteArr[$site] == $code && $icd10Data->laterality == $under) ? 1 : 0;
                                if (!$selClass) {
                                    $selClass = ($matchWith['laterality'] == $code) ? $code : 0;
                                }
                                $spanInit = ''; // $s . '_' . $index . '_site_';
                                $spanID = $spanInit . $siteCounter; //echo
                                $site_title['site_match'] = $matchWith['laterality'] . ',';
                                if ($icd10Data->allowMultiple) {
                                //if ($matchWith['laterality'] == $code) {
                                    $icd10Data->site_match_multiple['row' . $cntr] = $matchWith['laterality'] != '-' ? (int) $matchWith['laterality'] : 0;
                                //}
                                }
                                $siteCounter++;
                            }
                        }

                        $cntr = 0;
                        $icd10Data->site_match = $matchWith['laterality'] != '-' ? (int) $matchWith['laterality'] : 0;
                        $severity_title = [];
                        if (trim($icd10Data->severity)) {
                            $severityCounter = 0;
                            $char_to_replace = ($icd10Data->laterality) ? 1 : 0; //get which '-' to replace	
                            if ($icd10Data->laterality)
                                foreach ($icdLater[$icd10Data->severity] as $key => $value) {
                                    $cntr++;
                                    list($title, $code) = explode('~:~', $value);
                                    $selClass = ($matchWith['severity'] == $code) ? $code : 0;
                                    $spanID = $spanInit . $severityCounter;
                                    $severity_title['severity_match'] = $matchWith['laterality'] . ',';
                                    if ($icd10Data->allowMultiple) {
                                    //if ($matchWith['severity'] == $code) {
                                        $icd10Data->severity_match_multiple['row' . $cntr] = $matchWith['laterality'] != '-' ? (int) $matchWith['laterality'] : 0;
                                    // }
                                    }
                                    $severityCounter++;
                                }
                        }
                        $icd10Data->severity_match = $matchWith['laterality'] != '-' ? (int) $matchWith['laterality'] : 0;
                        if (trim($icd10Data->staging)) {
                            $stagingCounter = 0;
                            $cntr = 0;
                            $test = '';
                            $char_to_replace = ($icd10Data->laterality) ? 1 : 0; //get which '-' to replace
                            if ($icd10Data->laterality || $icd10Data->severity)
                                foreach ($icdLater[$icd10Data->staging] as $key => $value) {
                                    $cntr++;
                                    list($title, $code) = explode('~:~', $value);
                                    $selClass = ($matchWith['staging'] == $code) ? $code : 0;
                                    $spanID = $spanInit . $stagingCounter;
                                    $Staging_title['staging_match'] = $matchWith['laterality'] . ',';
                                    if ($icd10Data->allowMultiple) {
                                    //   if ($matchWith['staging'] == $code) {

                                        $icd10Data->staging_match_multiple['row' . $cntr] = $matchWith['laterality'] != '-' ? (int) $matchWith['laterality'] : 0;
                                    //  }
                                    }
                                    $stagingCounter++;
                                }
                        }
                        $icd10Data->staging_match = $matchWith['laterality'] != '-' ? (int) $matchWith['laterality'] : 0;
                        $getDiagnosisDetails[] = $icd10Data;
                    }
//                            }
//                        }
//                    }
                } else {
                    $andDiagQry = "";
                    if ($dx_id) {
                        $dx_id = (substr($dx_id, -1, 1) == ",") ? substr($dx_id, 0, (strlen($dx_id) - 1)) : $dx_id;
                        $dx_id = ($diag_idDB) ? implode(',', array_filter(array_unique(explode(",", $dx_id . ',' . $diag_idDB)))) : $dx_id;
                        $andDiagQry = " AND diag_id IN(" . $dx_id . ") ";
                    }
                    $getDiagnosisQry = "SELECT diag_id,diag_code,del_status FROM diagnosis_tbl WHERE 1=1 " . $andDiagQry . " ORDER BY diag_code ASC";
                    $getDiagnosisRes = DB::select($getDiagnosisQry); // or die($getDiagnosisQry . imw_error());
                    $getDiagnosisDetails = array();
                    if ($getDiagnosisRes) {
                        foreach ($getDiagnosisRes as $getDiagnosisRow) {
                            $getDiagnosisDetails[] = $getDiagnosisRow;
                        }
                    }
                }
                $andProcQry = "";
                if ($cpt_id) {
                    $cpt_id = (substr($cpt_id, -1, 1) == ",") ? substr($cpt_id, 0, (strlen($cpt_id) - 1)) : $cpt_id;
                    $cpt_id = ($procedures_IdDB) ? implode(',', array_filter(array_unique(explode(",", $cpt_id . ',' . $procedures_IdDB)))) : $cpt_id;
                    $andProcQry = " AND procedureId IN(" . $cpt_id . ") ";
                }
                $getProcedureRes = [];
                $countProcedureCat = [];

                // End Get All Procedures in array.
                $getProcedureCats = DB::select("select * from procedurescategory where del_status!='yes' order by name asc"); //$this->getArrayRecords('procedurescategory', '', '', 'name', 'Asc');
                $gCodeKey = '';
                foreach ($getProcedureCats as $keyCt => $categry) {
                    if ((strtolower($categry->name)) == 'g-codes') {
                        $gCodeKey = $keyCt;
                        break;
                    }
                }
                if ($gCodeKey) {
                    $getProcedureCatsTemp[$gCodeKey] = $getProcedureCats[$gCodeKey];
                    unset($getProcedureCats[$gCodeKey]);
                    array_unshift($getProcedureCats, $getProcedureCatsTemp[$gCodeKey]);
                }
                $catRes = [];
                foreach ($getProcedureCats as $categry) {
                    $procCategoryId = $categry->proceduresCategoryId;
                    $countProcedureCat[] = $procCategoryId;
                    $procCategory = $categry->name;
                    $getProcedureQry = "SELECT procedureId,name,code,del_status FROM procedures WHERE del_status!='yes' and catId='" . $procCategoryId . "' $andProcQry ORDER BY code ASC, name ASC";
                    $getProcedureRes1 = DB::select($getProcedureQry);
                    $getProcedureRes = [];
                    foreach ($getProcedureRes1 as $getProcedureRes2) {
                        if (isset($procNameArray[$getProcedureRes2->procedureId])) {
                            if ($procNameArray[$getProcedureRes2->procedureId] && $getProcedureRes2->name <> $procNameArray[$getProcedureRes2->procedureId]) {
                                $getProcedureRes2->name = $procNameArray[$getProcedureRes2->procedureId];
                            }
                            if ($procCodeNameArray[$getProcedureRes2->procedureId] && $getProcedureRes2->code <> $procCodeNameArray[$getProcedureRes2->procedureId]) {
                                $getProcedureRes2->code = $procCodeNameArray[$getProcedureRes2->procedureId];
                            }
                        }
                        $Procedures_id = stripslashes($getProcedureRes2->procedureId);
                        $Procedures_name = stripslashes($getProcedureRes2->name);
                        $Procedures_code = stripslashes($getProcedureRes2->code);
                        $Procedures_del_status = stripslashes($getProcedureRes2->del_status);
                        $icd10_ids = explode(",", $dischargeDetails->procedures_code);
                        if (in_array($getProcedureRes2->procedureId, $icd10_ids)) {
                            $procedureSelectedFlag = 'Yes';
                        } else {
                            $procedureSelectedFlag = 'No';
                        }

                        if ($Procedures_del_status != 'yes' || in_array($Procedures_id, $procedures_idDBExplodeArr)) {
                            $getProcedureRes[] = ['procedureId' => (int) $Procedures_id, 'name' => $Procedures_name, 'code' => $Procedures_code, 'del_status' => $Procedures_del_status, 'procedureSelectedFlag' => $procedureSelectedFlag];
                        }
                    }
                    if (!empty($getProcedureRes)) {
                        $catRes[] = ["procCategoryId" => (int) $procCategoryId, 'procCategory' => $procCategory, 'subcat' => $getProcedureRes];
                    }
                }

                // Start code to get unique procedure from DB & Template
                // End code to get unique procedure from DB & Template
                if (is_array($countProcedureCat)) {
                    $procedureCategoryImplode = implode(',', $countProcedureCat);
                }
                /* Procedure List Based on categories and filtered from DB & Template	 */
                if ($procedureCategoryImplode) {
                    $getProcedureQry = "select * from `procedures` where catId IN($procedureCategoryImplode) ";
                    $getProcedureRes = DB::select($getProcedureQry);
                    if ($getProcedureRes) {
                        foreach ($getProcedureRes as $getProcedureRow) {
                            $countProcedureId[] = $getProcedureRow->procedureId;
                        }
                        if (is_array($countProcedureId)) {
                            $procedureIdImplode = implode(',', $countProcedureId);
                        }
                    }
                }
                $CPTCodesArray = [];
                $condArr1['1'] = '1';
                $condArr1['del_status'] = '';
                $CPTCodesData = $this->getMultiChkArrayRecords('procedures', $condArr1, 'catId=20 Desc,name,code,catId', 'ASC', ' and  procedureId Not IN (' . $procedureIdImplode . ')');
                if ($CPTCodesData) {
                    foreach ($CPTCodesData as $CCA) {
                        $CPTCodesArray[$CCA->procedureId] = array('name' => $CCA->name, 'catId' => $CCA->catId, 'code' => $CCA->code,
                            'procedureAlias' => $CCA->procedureAlias, 'del_status' => $CCA->del_status, 'specialty_id' => $CCA->specialty_id);
                    }
                } else {
                    $CPTCodesArray = [];
                }

                $preDefineModQry = "SELECT modifierId,modifierCode,practiceCode,description FROM modifiers WHERE deleted = '0' ORDER BY modifierCode ASC";
                $preDefineMod = DB::select($preDefineModQry);
                $data = [
                    "CPTCodesArray" => $CPTCodesArray,
                    'ICD10CodesData' => $ICD10CodesArray,
                    "preDefineMod" => $preDefineMod,
                    "Category" => $catRes,
                    "Comments" => isset($dischargeDetails->comment) ? $dischargeDetails->comment : '',
                    "SuperBill" => $superBills,
                    "DXCodesICD10" => $getDiagnosisDetails,
                    "dis_ScanUpload" => (isset($dischargeDetails->dis_ScanUpload) && $dischargeDetails->dis_ScanUpload <> "") ? base64_encode($dischargeDetails->dis_ScanUpload) : '',
                    "dis_ScanUpload2" => (isset($dischargeDetails->dis_ScanUpload2) && $dischargeDetails->dis_ScanUpload <> "") ? base64_encode($dischargeDetails->dis_ScanUpload2) : '',
                    'Other1' => isset($dischargeDetails->other1) ? $dischargeDetails->other1 : '',
                    'Other2' => isset($dischargeDetails->other2) ? $dischargeDetails->other2 : '',
                    "iol_ScanUpload" =>(isset($iol_ScanUpload) && $iol_ScanUpload <> "") ? base64_encode($iol_ScanUpload) : '',
                    "iol_ScanUpload2" =>(isset($iol_ScanUpload2) && $iol_ScanUpload2 <> "") ? base64_encode($iol_ScanUpload2) : '',
                    "Allergy_data" => ['patientAllergiesGrid' => $patientAllergies],
                    "otherMiscellaneousDB" => $otherMiscellaneousDB,
                    "signature" => [
                        "surgeon_signature" => ["name" => $dischargeDetails->signSurgeon1FirstName . " " . $dischargeDetails->signSurgeon1LastName, "signed_status" => $dischargeDetails->signSurgeon1Status, "sign_date" => $dischargeDetails->signSurgeon1DateTime != '0000-00-00 00:00:00' ? date("m-d-Y h:i A", strtotime($dischargeDetails->signSurgeon1DateTime)) : ''],
                    ]
                ];
                $status = 1;
                $message = " Discharge Summary Sheet ";
            }
        }
        return response()->json(['status' => $status, 'message' => $message, 'requiredStatus' => '', 'data' => $data,
                    'dischargeSummarySheetId' => isset($dischargeDetails->dischargeSummarySheetId) ? $dischargeDetails->dischargeSummarySheetId : 0,
                    'operatingRoomRecordsId' => isset($operatingRoomRecordsId) ? $operatingRoomRecordsId : 0,
                    'dx_code_type' => $dx_code_type,
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

    public function Alternativequestioner($pConfId, $form_status) {
        $data = [];
        if ($form_status == 'completed' || $form_status == 'not completed') {
            $getAddQuestions = DB::select("select id,ques,ques_desc,ques_status from history_physical_ques where confirmation_id ='" . $pConfId . "' order by ques ASC");
            if ($getAddQuestions) {
                foreach ($getAddQuestions as $Questions) {
                    $data[] = ['QuestionId' => $Questions->id, 'Question' => stripslashes($Questions->ques), "text_note" => stripslashes($Questions->ques_desc), 'yes_no_sts' => $Questions->ques_status,];
                }
            } else {
                $getAddQuestions = DB::select('select id,name from predefine_history_physical where deleted=0 order by name asc');
                if ($getAddQuestions) {
                    foreach ($getAddQuestions as $Questions) {
// $data[] = ['QuestionId' => $Questions->id, 'Question' => $Questions->name, "text_note" => '', 'yes_no_sts' => '',];
                    }
                }
            }
        } else {
            $getAddQuestions = DB::select('select id,name from predefine_history_physical where deleted=0 order by name asc');
            if ($getAddQuestions) {
                foreach ($getAddQuestions as $Questions) {
                    $data[] = ['QuestionId' =>
                        $Questions->id, 'Question' => $Questions->name, "text_note" => '', 'yes_no_sts' => '',];
                }
            }
        }
        return $data;
    }

    public function Alternativequestionersave($data, $pConfId, $patient_id) {
        if ($data) {
            foreach ($data as $Questions) {
                if ($Questions->QuestionId > 0) {
                    $data_arr = ["ques_desc" => addslashes($Questions->text_note), 'ques_status' => $Questions->yes_no_sts];
                    DB::table('history_physical_ques')->where('id', $Questions->QuestionId)->update($data_arr);
                } else {
                    $data_arr = ["ques_desc" => addslashes($Questions->text_note), 'ques_status' => $Questions->yes_no_sts, 'confirmation_id' => $pConfId, 'patient_id' => $patient_id];
                    DB::table('history_physical_ques')->insert($data_arr);
                }
            }
            return $data;
        }
        return $data;
    }

    public function patient_allergy_save($Allergies_data, $pConfId, $patient_id, $userId, $loggedInUserName) {
        if ((is_array($Allergies_data)) && (!empty($Allergies_data) )) {
            foreach ($Allergies_data as $allergiesArrValue) {
                $allergiesReactionArr['patient_confirmation_id'] = $pConfId;
                $allergiesReactionArr['patient_id'] = $patient_id;
                $allergiesReactionArr['allergy_name'] = addslashes($allergiesArrValue->name);
                $allergiesReactionArr['reaction_name'] = isset($allergiesArrValue->reaction) ? addslashes($allergiesArrValue->reaction) : "";
                $allergiesReactionArr

                        ['operator_name'] = $loggedInUserName;
                $allergiesReactionArr ['operator_id'] = $userId;
                if ($allergiesArrValue->name != '') {
                    if ($allergiesArrValue->pre_op_allergy_id > 0) {
                        DB::table('patient_allergies_tbl')->where('pre_op_allergy_id', $allergiesArrValue->pre_op_allergy_id)->update($allergiesReactionArr);
                    } else {
                        DB::table('patient_allergies_tbl')->insert($allergiesReactionArr);
                    }
                } else if ($allergiesArrValue->name == '' && $allergiesArrValue->reaction == '') {
                    DB::table('patient_allergies_tbl')->where('pre_op_allergy_id', $allergiesArrValue->
                            pre_op_allergy_id)->delete();
                }
            }
        }
    }

    public function patient_medication_save($Medication_data, $pConfirmId, $patient_id, $userId, $loggedInUserName) {
        if (!empty($Medication_data)) {
            foreach ($Medication_data as $medications) {
                if ($medications->name != '') {
                    $medicationsArr['confirmation_id'] = $pConfirmId;
                    $medicationsArr['patient_id'] = $patient_id;
                    $medicationsArr['prescription_medication_name'] = addslashes($medications->name);
                    $medicationsArr['prescription_medication_desc'] = addslashes($medications->dosage);
                    $medicationsArr['prescription_medication_sig'] = addslashes($medications->sign);
                    $medicationsArr['operator_name'] = $loggedInUserName;
                    $medicationsArr['operator_id'] = $userId;
                    if ($medications->prescription_medication_id > 0) {
                        DB:: table('patient_anesthesia_medication_tbl')->where('prescription_medication_id', $medications->prescription_medication_id)->update($medicationsArr);
                    } else {
                        DB::table('patient_anesthesia_medication_tbl')->insert($medicationsArr);
                    }
                }
            }
        }
    }

    public function DishchargeSummarySheet_save(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $loginUserType = $request->json()->get('UserType') ? $request->json()->get('UserType') : $request->input('UserType');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $operatingRoomRecordsId = $request->json()->get('operatingRoomRecordsId') ? $request->json()->get('operatingRoomRecordsId') : $request->input('operatingRoomRecordsId');
        $dischargeSummarySheetIdDB = $request->json()->get('dischargeSummarySheetId') ? $request->json()->get('dischargeSummarySheetId') : $request->input('dischargeSummarySheetId');
        $dx_code_type = $request->json()->get('dx_code_type') ? $request->json()->get('dx_code_type') : $request->input('dx_code_type');
        $jsondata = $request->json()->get('jsondata') ? $request->json()->get('jsondata') : $request->input('jsondata');
        $json = json_decode($jsondata);
        $data = [];
        $status = 0;
        $moveStatus = 0;
        $message = " unauthorized ";
        $requiredStatus = [];
        $userId = $this->checkToken($userToken);
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
                $privilegesArr = explode(',', $users->user_privileges);
                $detailConfirmation = $this->getRowRecord('patientconfirmation', 'patientConfirmationId ', $pConfirmId);
                $finalizeStatus = $detailConfirmation->finalize_status;
                $allergiesNKDA_patientconfirmation_status = $detailConfirmation->allergiesNKDA_status;
                $noMedicationStatus = $detailConfirmation->no_medication_status;
                $noMedicationComments = $detailConfirmation->no_medication_comments;
                $ascId = $detailConfirmation->ascId;
                $dos = $detailConfirmation->dos;
                $surgeonId = $detailConfirmation->surgeonId;
                $tablename = "dischargesummarysheet";
                    //START CODE TO CHECK SURGEON SIGN NI DATABASE

                $chkSurgeonSigndischargeDetails = $this->getRowRecord('dischargesummarysheet', 'confirmation_id', $pConfirmId);
                if ($chkSurgeonSigndischargeDetails) {
                    $chk_signSurgeon1Id = $chkSurgeonSigndischargeDetails->signSurgeon1Id;
                    //CHECK FORM STATUS
                    $chk_form_status = $chkSurgeonSigndischargeDetails->form_status;
                    //CHECK FORM STATUS
                    $chk_dis_ScanUpload ='';// $chkSurgeonSigndischargeDetails->dis_ScanUpload;
                    $chk_dis_ScanUpload2 ='';// $chkSurgeonSigndischargeDetails->dis_ScanUpload2;
                }
                //END CODE TO CHECK SURGEON SIGN NI DATABASE 
                if (($json->Comments != '' || $json->Other1 != '' || $json->Other2 != '' || (($chk_dis_ScanUpload || $chk_dis_ScanUpload2))
                        ) && $chk_signSurgeon1Id <> "0") {
                    $form_status = 'completed';
                } else {
                    $form_status = 'not completed';
                }
                $DXCodesICD10 = $json->DXCodesICD10;
                $icd10_ids = '';
                $icd10_name = '';
                $icd10_code = '';
                if ($DXCodesICD10) {
                    foreach ($DXCodesICD10 as $DXCodesICD10s) {
                        if ($DXCodesICD10s->ICD10SelectedFlag == 'Yes') {
                            $icd10_ids.= $DXCodesICD10s->id . ",";
                            $icd10_name.= $DXCodesICD10s->icd10_desc . '@@';
                            $icd10_code.= $DXCodesICD10s->icd10 . ",";
                        }
                    }
                }
                $dischargeProceduresNames = '';
                $dischargeProceduresCodeNames = '';
                $dischargeProcdureIds = '';
                $dischargeCodes = $json->Category;
                foreach ($dischargeCodes as $dischargeCode) {
                    foreach ($dischargeCode->subcat as $subcats) {
                        if ($subcats->procedureSelectedFlag == "Yes") {
                            $dischargeProceduresNames.=$subcats->name . ',';
                            $dischargeProceduresCodeNames.=$subcats->code . '##';
                            $dischargeProcdureIds.=$subcats->procedureId . ',';
                        }
                    }
                }
                //END   IF ASSIGNED PROCEDURE NAME IS "UNLISTED" THEN USER CAN SAVE DIAGNOSIS  INDVIDUALLY
                $arrayRecord = [];
                $arrayRecord['surgeon_knowledge'] = ''; //$_REQUEST['chbx_h_p'];
                $arrayRecord['dischargeSummarySheetDate'] = date('Y-m-d');
                $arrayRecord['dischargeSummarySheetTime'] = date('H:i:s');
                $arrayRecord['procedures_name'] = addslashes(rtrim($dischargeProceduresNames, ","));
                $arrayRecord['procedures_code_name'] = addslashes(rtrim($dischargeProceduresCodeNames, "##"));
                $arrayRecord['procedures_code'] = rtrim($dischargeProcdureIds, ",");
                //$arrayRecord['disAttached'] = $_REQUEST['chbx_disAttached'];
                $arrayRecord['otherMiscellaneous'] = ''; // addslashes($_REQUEST['miscellaneousOther']);
                $arrayRecord['comment'] = addslashes($json->Comments);
                $arrayRecord['diag_ids'] = ''; // $diagnosisIds;
                $arrayRecord['diag_names'] = ''; // addslashes($diagnosisNames);
                $arrayRecord['icd10_code'] = addslashes(rtrim($icd10_code, ","));
                $arrayRecord['icd10_name'] = addslashes(rtrim($icd10_name, '@@'));
                $arrayRecord['icd10_id'] = rtrim($icd10_ids, ",");
                $arrayRecord['other1'] = addslashes($json->Other1);
                $arrayRecord['other2'] = addslashes($json->Other2);
                $arrayRecord['surgeonSign'] = ''; // $_REQUEST['elem_signature'];
                $arrayRecord['summarySaveDateTime'] = date('Y-m-d H:i:s');
                $arrayRecord['form_status'] = $form_status;
                $arrayRecord['surgeonId'] = $surgeonId;
                $arrayRecord['cpt_inte_sync_status'] = '0';
                $arrayRecord['confirmation_id'] = $pConfirmId;
                
                $cpt_inte_sync_flag = '0';
                if (getenv("INTE_SYNC") == 'YES') {
                    $cpt_inte_sync_flag = '1';
                }
                $arrayRecord['cpt_inte_sync_flag'] = $cpt_inte_sync_flag;
                $diag_ids = isset($diagnosisIds)?$diagnosisIds:''; //code for acc/superbill
                $proc_code = isset($dischargeProcdureIds)?$dischargeProcdureIds:''; //code for acc/superbill

                $isDischargeSummarySaved = false;
                if ($dischargeSummarySheetIdDB) {
                    if (($privilegesArr[0] == 'Staff' || $privilegesArr[0] == 'Billing') && ($privilegesArr[1] == 'Billing' || $privilegesArr[1] == 'Staff')) {
                        //UPDATE ONLY COMMENT BOX IF USER HAS ONLY STAFF AND BILLING PRIVILLIGES
                        unset($staffArrRecord);
                        $staffArrRecord['cpt_inte_sync_status'] = '0';
                        $staffArrRecord['cpt_inte_sync_flag'] = $cpt_inte_sync_flag;
                        $staffArrRecord['comment'] = addslashes($json->Comments);
                        DB::table('dischargesummarysheet')->where('dischargeSummarySheetId', $dischargeSummarySheetIdDB)->update($staffArrRecord);
                        //END UPDATE ONLY COMMENT BOX IF USER HAS ONLY STAFF AND BILLING PRIVILLIGES
                    } else {
                        //ELSE UPDATE WHOLE RECORD
                        DB::table('dischargesummarysheet')->where('dischargeSummarySheetId', $dischargeSummarySheetIdDB)->update($arrayRecord);
                        $isDischargeSummarySaved = true;
                    }
                    //set audit status created
                    //end set audit status created
                } else {
                    if (($privilegesArr[0] == 'Staff' || $privilegesArr[0] == 'Billing') && ($privilegesArr[1] == 'Billing' || $privilegesArr[1] == 'Staff')) {
                        //ADD ONLY COMMENT BOX IF USER HAS ONLY STAFF AND BILLING PRIVILLIGES
                        unset($staffArrRecord);
                        $staffArrRecord['comment'] = addslashes($json->Comments);
                        DB::table('dischargesummarysheet')->insertGetId($staffArrRecord);
                        //END ADD ONLY COMMENT BOX IF USER HAS ONLY STAFF AND BILLING PRIVILLIGES	
                    } else {
                        //ELSE ADD WHOLE RECORD
                        DB::table('dischargesummarysheet')->insertGetId($arrayRecord);
                        $isDischargeSummarySaved = true;
                    }
                }

                //CODE TO DISPLAY FORM STATUS ON RIGHT SLIDER(AS RED FLAG OR TICK MARK) 	
                //CODE START TO SET AUDIT STATUS AFTER SAVE
                unset($conditionArr);
                $conditionArr['confirmation_id'] = $pConfirmId;
                $conditionArr['form_name'] = 'discharge_summary_form';
                $conditionArr['status'] = 'created';
                $chkAuditStatus = $this->getMultiChkArrayRecords('chartnotes_change_audit_tbl', $conditionArr);
                if ($chkAuditStatus) {
                    //MAKE AUDIT STATUS MODIFIED
                    $chart_note_updateQry = DB::select("
                                            insert into chartnotes_change_audit_tbl set
                                            user_id ='" . $userId . "',
                                            patient_id =$patient_id,
                                            confirmation_id = $pConfirmId,
                                            form_name ='discharge_summary_form',
                                            status='modified',
                                            action_date_time = '" . date('Y-m-d H:i:s') . "'
                                            ");
                } else {
                    //MAKE AUDIT STATUS CREATED
                    $chart_note_createdQry = DB::select("
                                            insert into chartnotes_change_audit_tbl set
                                           user_id ='" . $userId . "',
                                            patient_id =$patient_id,
                                            confirmation_id = $pConfirmId,
                                            form_name ='discharge_summary_form',
                                            status='created',
                                            action_date_time = '" . date('Y-m-d H:i:s') . "'
                                            ");
                }

                //CODE END TO SET AUDIT STATUS AFTER SAVE

                /*                 * * Save Super Bill ** */
                $diagCodeType = $dx_code_type;
                $DxCodeFieldName = ($diagCodeType == 'icd10') ? 'dxcode_icd10' : 'dxcode_icd9';
                $SuperBill = $json->SuperBill;
                if ($SuperBill) {
                    foreach ($SuperBill as $SuperBills) {
                        $SurgeonRecord = isset($SuperBills->Surgeon)?$SuperBills->Surgeon:'';
                        if ($SurgeonRecord) {
                            foreach ($SurgeonRecord as $SurgeonRecords) {
                                unset($insertUpdateRecord);
                                $insertUpdateRecord['confirmation_id'] = $pConfirmId;
                                $insertUpdateRecord['bill_user_type'] = $SurgeonRecords->bill_user_type;
                                $insertUpdateRecord['cpt_id'] = $SurgeonRecords->cpt_id;
                                $insertUpdateRecord['cpt_code'] = $SurgeonRecords->cpt_code;
                                $insertUpdateRecord[$DxCodeFieldName] = $SurgeonRecords->dxcode_icd10;
                                $insertUpdateRecord['quantity'] = $SurgeonRecords->quantity;
                                $insertUpdateRecord['modifier1'] = $SurgeonRecords->modifier1;
                                $insertUpdateRecord['modifier2'] = $SurgeonRecords->modifier2;
                                $insertUpdateRecord['modifier3'] = $SurgeonRecords->modifier3;
                                $insertUpdateRecord['modified_by'] = $userId;
                                $insertUpdateRecord['modified_on'] = date('Y-m-d H:i:s');

                                unset($chkArray);
                                $chkArray['confirmation_id'] = $pConfirmId;
                                $chkArray['bill_user_type'] = $SurgeonRecords->bill_user_type;
                                $chkArray['cpt_id'] = $SurgeonRecords->cpt_id;
                                $chkArray['cpt_code'] = $SurgeonRecords->cpt_code;
                                $chkArray['deleted'] = 0;
                                $SB_recordId = $SurgeonRecords->superbill_id;
                                if ($SB_recordId) {
                                    //echo 'update'; print_r($insertUpdateRecord);
                                    DB::table('superbill_tbl')->where('superbill_id', $SB_recordId)->update($insertUpdateRecord);
                                } else {
                                    $chkRecords = $this->getMultiChkArrayRecords('superbill_tbl', $chkArray);
                                    if ($chkRecords) {
                                        
                                    } else {
                                        DB::table('superbill_tbl')->insertGetId($insertUpdateRecord);
                                    }
                                }
                            }
                        }
                        $Facility =isset($SuperBills->Facility)?$SuperBills->Facility:''; 
                        if ($Facility) {
                            foreach ($Facility as $Facilitys) {
                                unset($insertUpdateRecord);
                                $insertUpdateRecord['confirmation_id'] = $pConfirmId;
                                $insertUpdateRecord['bill_user_type'] = $Facilitys->bill_user_type;
                                $insertUpdateRecord['cpt_id'] = $Facilitys->cpt_id;
                                $insertUpdateRecord['cpt_code'] = $Facilitys->cpt_code;
                                $insertUpdateRecord[$DxCodeFieldName] = $Facilitys->dxcode_icd10;
                                $insertUpdateRecord['quantity'] = $Facilitys->quantity;
                                $insertUpdateRecord['modifier1'] = $Facilitys->modifier1;
                                $insertUpdateRecord['modifier2'] = $Facilitys->modifier2;
                                $insertUpdateRecord['modifier3'] = $Facilitys->modifier3;
                                $insertUpdateRecord['modified_by'] = $userId;
                                $insertUpdateRecord['modified_on'] = date('Y-m-d H:i:s');

                                unset($chkArray);
                                $chkArray['confirmation_id'] = $pConfirmId;
                                $chkArray['bill_user_type'] = $Facilitys->bill_user_type;
                                $chkArray['cpt_id'] = $Facilitys->cpt_id;
                                $chkArray['cpt_code'] = $Facilitys->cpt_code;
                                $chkArray['deleted'] = 0;
                                $SB_recordId = $Facilitys->superbill_id;
                                if ($SB_recordId) {
                                    //echo 'update'; print_r($insertUpdateRecord);
                                    DB::table('superbill_tbl')->where('superbill_id', $SB_recordId)->update($insertUpdateRecord);
                                } else {
                                    $chkRecords = $this->getMultiChkArrayRecords('superbill_tbl', $chkArray);
                                    if ($chkRecords) {
                                        
                                    } else {
                                        DB::table('superbill_tbl')->insertGetId($insertUpdateRecord);
                                    }
                                }
                            }
                        }
                        $Anesthesia = isset($SuperBills->Anesthesia)?$SuperBills->Anesthesia:'';
                        if ($Anesthesia) {
                            foreach ($Anesthesia as $Anesthesias) {
                                unset($insertUpdateRecord);
                                $insertUpdateRecord['confirmation_id'] = $pConfirmId;
                                $insertUpdateRecord['bill_user_type'] = $Anesthesias->bill_user_type;
                                $insertUpdateRecord['cpt_id'] = $Anesthesias->cpt_id;
                                $insertUpdateRecord['cpt_code'] = $Anesthesias->cpt_code;
                                $insertUpdateRecord[$DxCodeFieldName] = $Anesthesias->dxcode_icd10;
                                $insertUpdateRecord['quantity'] = $Anesthesias->quantity;
                                $insertUpdateRecord['modifier1'] = $Anesthesias->modifier1;
                                $insertUpdateRecord['modifier2'] = $Anesthesias->modifier2;
                                $insertUpdateRecord['modifier3'] = $Anesthesias->modifier3;
                                $insertUpdateRecord['modified_by'] = $userId;
                                $insertUpdateRecord['modified_on'] = date('Y-m-d H:i:s');

                                unset($chkArray);
                                $chkArray['confirmation_id'] = $pConfirmId;
                                $chkArray['bill_user_type'] = $Anesthesias->bill_user_type;
                                $chkArray['cpt_id'] = $Anesthesias->cpt_id;
                                $chkArray['cpt_code'] = $Anesthesias->cpt_code;
                                $chkArray['deleted'] = 0;
                                $SB_recordId = $Anesthesias->superbill_id;
                                if ($SB_recordId) {
                                    //echo 'update'; print_r($insertUpdateRecord);
                                    DB::table('superbill_tbl')->where('superbill_id', $SB_recordId)->update($insertUpdateRecord);
                                } else {
                                    $chkRecords = $this->getMultiChkArrayRecords('superbill_tbl', $chkArray);
                                    if ($chkRecords) {
                                        
                                    } else {
                                        DB::table('superbill_tbl')->insertGetId($insertUpdateRecord);
                                    }
                                }
                            }
                        }
                    }
                }
                /*                 * * End Save Super Bill ** */

                /**/
                //CODE TO CHECK SURGEON ALL SIGNATURE AND SET VALUE IN STUB TABLE
                $chartSignedBySurgeon = $this->chkSurgeonSignNew($pConfirmId);
                $updateStubTblQry = "UPDATE stub_tbl SET chartSignedBySurgeon='" . $chartSignedBySurgeon . "' WHERE patient_confirmation_id='" . $pConfirmId . "'";
                $updateStubTblRes = DB::select($updateStubTblQry);
                //END CODE TO CHECK SURGEON SIGNATURE AND SET VALUE IN STUB TABLE
                //REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)
                //REFRESH SLIDER (IF FORM STATUS IS EMPTY OR CHANGED IN DATABASE ON SAVE)
                //start
                if (trim($ascId) && $form_status == "completed" && $imwSwitchFile == "sync_imwemr.php" && $import_status == "false" && getenv('STOP_SYNC_SUPERBILL') != "YES") {
                    $this->superbill($patient_id, $facility_id, trim($ascId),$pConfirmId);//$patient_id, $facility, $ascId,$pConfId
                }

                if (trim($ascId) && $form_status == "completed") {
                    /**
                     * Log Rabbit Mq messages
                     */
                    //include_once(dirname(__FILE__).'/library/rabbitmq_integration/log_charges.php');
                }

                /*                 * *****HL7- DFT GENERATION********** */
                if ($isDischargeSummarySaved && getenv('DCS_DFT_GENERATION') == true && !in_array(strtolower(getenv("LOCAL_SERVER")), array('keywhitman', 'albany', 'waltham', 'gnysx', 'mackool', 'islandeye'))) {
                   /* include_once(dirname(__FILE__) . "/dft_hl7_generate.php");
                    if (in_array(strtolower(getenv("LOCAL_SERVER")), array('palisades'))) {
                        include_once(dirname(__FILE__) . "/dft_hl7_generate_doc_choice.php");
                    }*/
                    $message='HL7 pending from api........';
                }
                /*                 * *****DFT GENERATION END*********** */
                $message='HL7 pending from api........';
                $status=1;
                $savedStatus=1;
            }
        }
        return response()->json(['status' => $status, 'message' => $message, 'savedStatus' => $savedStatus, 'requiredStatus' => '', 'data' => $data,
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

    public function getHqFacility_imw_sc() {
        $sql = "SELECT id FROM facility WHERE facility_type = '1' LIMIT 0,1 ";
        $row = DB::selectone($sql); // or die($sql . imw_error());
        $rez = $row;
        if ($rez) {
            return $rez->id;
        } else {
// Fix if No Hq. is selected
            $sql = "SELECT id FROM facility LIMIT 0,1 ";
            $row = DB::selectone($sql); // or die($sql . imw_error());
            $rez = $row;
            if ($rez) {
                return $rez->id;
            }
        }
    }

    public function getEncounterId_imw_sc() {
        $facilityId = $this->getHqFacility_imw_sc();
        $qry = "SELECT encounterId FROM facility WHERE id='" . $facilityId . "' ";
        $sql = DB::selectone($qry);
        if ($sql) {
            $res = $sql;
            $encounterId = $res->encounterId;
        }

//get from policies
        $sql = "select Encounter_ID from copay_policies WHERE policies_id = '1' ";
        $sql = DB::selectone($qry);
        if ($sql) {
            $res =$sql;
            $encounterId_2 = $res->Encounter_ID;
        }

//bigg
        if ($encounterId < $encounterId_2) {
            $encounterId = $encounterId_2;
        }

//--		
        $counter = 0; //check only 100 times
        do {

            $flgbreak = 1;
//check in superbill
            if ($flgbreak == 1) {
                $qry = "select idSuperBill FROM superbill WHERE encounterId='" . $encounterId . "' ";
                $sql = DB::select($qry);
                if ($sql) {
                    $flgbreak = 0;
                }
            }

//check in chart_master_table--
            if ($flgbreak == 1) {
                $qry = "select id FROM chart_master_table WHERE encounterId='" . $encounterId . "' ";
                $sql = DB::select($qry);// or die($qry . imw_error());
                if ($sql) {
                    $flgbreak = 0;
                }
            }

//check in Accounting
            if ($flgbreak == 1) {
                $qry = "select charge_list_id FROM patient_charge_list WHERE encounter_id='" . $encounterId . "'";
                $sql = DB::select($qry);
                if ($sql) {
                    $flgbreak = 0;
                }
            }
            if ($flgbreak == 0) {
                $encounterId = $encounterId + 1;
            }
            $counter++;
        } while ($flgbreak == 0 && $counter < 100);
        if ($counter >= 100) {
            exit("Error: encounter Id counter needs to reset.");
        }
//--
        return array($encounterId, $facilityId);
    }

    public function getBillingGroup($facility) {
        $grp_arr = array();
        $sqlQry = "SELECT * FROM facility_tbl WHERE fac_id = '" . $facility . "' AND (fac_group_institution > '0' OR fac_group_anesthesia > '0' OR fac_group_practice > '0')";
        $sqlRes = DB::selectone($sqlQry);

        if ($sqlRes) {
            $sqlRow = $sqlRes;
            $grp_arr = array("inst_grp" => $sqlRow->fac_group_institution, "anes_grp" => $sqlRow->fac_group_anesthesia, "prac_grp" => $sqlRow->fac_group_practice);
        }
        return $grp_arr;
    }

    public function superbill($patient_id, $facility, $ascId,$pConfId) {
        $billing_groups_new = $this->getBillingGroup($facility);
        $billing_groups_arr = $billing_groups_new;
        $imwApptId = 0;
        $iASCConfirmationId = 0;
        $imwApptIdQry = "SELECT st.appt_id, pc.patientConfirmationId FROM patientconfirmation pc 
				INNER JOIN stub_tbl st ON (st.patient_confirmation_id = pc.patientConfirmationId AND st.patient_confirmation_id !='0') 
				WHERE pc.ascId = '" . $ascId . "' AND pc.ascId != '0' LIMIT 0,1";
        $imwApptIdRes = DB::selectone($imwApptIdQry); // or die($imwApptIdQry . imw_error());
        if ($imwApptIdRes) {
            $imwApptIdRow = $imwApptIdRes;
            $imwApptId = $imwApptIdRow->appt_id;
            $iASCConfirmationId = (int) $imwApptIdRow->patientConfirmationId;
        }
        $ptDtlQry = "SELECT imwPatientId,patient_fname,patient_mname,patient_lname,date_of_birth FROM patient_data_tbl WHERE patient_id='" . $patient_id . "' LIMIT 0,1 ";
        $ptDtlRes = DB::selectone($ptDtlQry); // or die($ptDtlQry . imw_error());
        if ($ptDtlRes) {
            $ptDtlRow = $ptDtlRes;
            $imwPatientId = $ptDtlRow->imwPatientId;
            $iascPtDataQry = "SELECT id FROM patient_data where id='" . $imwPatientId . "' LIMIT 0,1 ";
            $iascPtDataRes = DB::connection('DB_REGISTER_CONNECTION')->select($iascPtDataQry);
            if ($iascPtDataRes) {

//START GET ANES USER-ID FOR ANES BILLING
                $iascAnesUserIdTemp = "";
                $iascAnesUserAndQry = " AND fname='" . addslashes($fname_Anes) . "' AND mname='" . addslashes($mname_Anes) . "' AND lname='" . addslashes($lname_Anes) . "' ";
                if (getenv("CHECK_USER_NPI") == "YES") {
                    if ($npi_Anes) {
                        $iascAnesUserAndQry = " AND user_npi = '" . $npi_Anes . "'  AND user_npi != ''   AND user_npi != '0' ";
                    }
                }

                $iascAnesUserQry = "SELECT id FROM users WHERE delete_status='0' " . $iascAnesUserAndQry . " LIMIT 0,1 ";
                $iascAnesUserRes = DB::selectone($iascAnesUserQry);
                if ($iascAnesUserRes) {
                    $iascAnesUserRow = $iascAnesUserRes;
                    $iascAnesUserIdTemp = $iascAnesUserRow->id;
                }
                //END GET ANES USER-ID FOR ANES BILLING

                $iascUserAndQry = " AND fname='" . addslashes($fname_Surgeon) . "' AND mname='" . addslashes($mname_Surgeon) . "' AND lname='" . addslashes($lname_Surgeon) . "' ";
                if (getenv("CHECK_USER_NPI") == "YES") {
                    if ($npi_Surgeon) {
                        $iascUserAndQry = " AND user_npi = '" . $npi_Surgeon . "'  AND user_npi != ''   AND user_npi != '0' ";
                    }
                }

                $iascUserQry = "SELECT id FROM users WHERE delete_status='0' " . $iascUserAndQry . " LIMIT 0,1 ";
                $iascUserRes = DB::selectone($iascUserQry); // or die($iascUserQry . imw_error());
                if ($iascUserRes) {
                    $iascUserRow = $iascUserRes;
                    $iascUserIdTemp = $iascUserRow->id;

                    $insuranceCaseId = '0';
                    $refferingPhysicianId = '0';
                    $report_provider_id = $iascUserRow->id;

                    $iascRefPhyAndQry = " AND TRIM(FirstName)='" . addslashes(trim($fname_Surgeon)) . "' AND TRIM(MiddleName)='" . addslashes(trim($mname_Surgeon)) . "' AND TRIM(LastName)='" . addslashes(trim($lname_Surgeon)) . "' ";
                    if (getenv("CHECK_USER_NPI") == "YES") {
                        if ($npi_Surgeon) {
                            $iascRefPhyAndQry = " AND NPI = '" . $npi_Surgeon . "'  AND NPI != ''   AND NPI != '0' ";
                        }
                    }

                    $iascRefPhyQry = "SELECT physician_Reffer_id FROM refferphysician WHERE physician_Reffer_id != '0' " . $iascRefPhyAndQry . " And delete_status <> '1' LIMIT 0,1 ";
                    $iascRefPhyRes = DB::selectone($iascRefPhyQry);
                    if ($iascRefPhyRes) {
                        $iascRefPhyRow = $iascRefPhyRes;
                        $refferingPhysicianId = $iascRefPhyRow->physician_Reffer_id;
                    }

                    //=======================GET POS PRACTICE CODE===============================//
                    $pos_prac_code = "SC";
                    $qry_sch_appt = "SELECT pos_tbl.pos_prac_code FROM facility JOIN schedule_appointments  on
							schedule_appointments.sa_facility_id=facility.id
							JOIN pos_facilityies_tbl on pos_facilityies_tbl.pos_facility_id=facility.fac_prac_code
							JOIN pos_tbl on pos_tbl.pos_id=pos_facilityies_tbl.pos_id
							 WHERE schedule_appointments.id='" . $imwApptId . "' LIMIT 0,1";
                    $res_sch_appt = DB::selectone($qry_sch_appt);
                    if ($res_sch_appt) {
                        $row_sch_appt = $res_sch_appt;
                        $pos_prac_code = addslashes($row_sch_appt->pos_prac_code);
                    }

                    if (count($billing_groups_arr) > 0 && is_array($billing_groups_arr)) {
                        $billing_groups_imp = implode(',', $billing_groups_arr);
                        $grp_whr = "gro_id in($billing_groups_imp) ";
                    } else {
                        $grp_whr = "group_institution='1' LIMIT 0,1 ";
                    }

                    $groupsArr = array();
                    $sel_proc_qry = "select gro_id,group_institution,group_anesthesia from groups_new where $grp_whr";
                    $sel_proc_res = DB::select($sel_proc_qry);
                    foreach ($sel_proc_res as $sel_proc_row) {
                        $data = array();
                        $data['gro_id'] = $sel_proc_row->gro_id;
                        $data['group_institution'] = $sel_proc_row->group_institution;
                        $data['group_anesthesia'] = $sel_proc_row->group_anesthesia;

                        array_push($groupsArr, $data);
                    }

                    if (is_array($groupsArr) && count($groupsArr) == 0) {
                        $data = array();
                        $data['gro_id'] = 0;
                        $data['group_institution'] = 0;
                        $data['group_anesthesia'] = 0;

                        array_push($groupsArr, $data);
                    }

                    foreach ($groupsArr as $sel_proc_row) {
                        $log = "";
                        $gro_id = $sel_proc_row['gro_id'];
                        $group_institution = $sel_proc_row['group_institution'];
                        $group_anesthesia = $sel_proc_row['group_anesthesia'];

                        $enc_idArr = $this->getEncounterId_imw_sc();
                        $enc_id = $enc_idArr[0];
                        $sbfacilityId = $enc_idArr[1];
                        $iascUserId = $iascUserIdTemp;
                        if ($constantImwProviderId > 0 && $billing_groups_arr["inst_grp"] == $gro_id) {
                            $iascUserId = $constantImwProviderId;
                        } else if ($iascAnesUserIdTemp > 0 && $billing_groups_arr["anes_grp"] == $gro_id) {
                            $iascUserId = $iascAnesUserIdTemp;
                        }
                        $log .= "\n" . "ASCID - " . $ascId;
                        $log .= "\n" . "GROUP ID - " . $gro_id;
                        //============================================================================//
                        $ins_supper=array(
                            'patientId'=>$imwPatientId,
                            'physicianId'=>$iascUserId,
                            'insuranceCaseId'=>$insuranceCaseId,
                            'encounterId' =>$enc_id, 
                            'timeSuperBill'=>$surgeryTime,
                            'dateOfService'=>$dos,
                            'patientStatus'=>'Active',
                            'refferingPhysician'=>$refferingPhysicianId,
                            'financialStatus'=>'Self',
                            'methodOfPayment'=>'Cash',
                            'pos'=>'" . $pos_prac_code . "',
                            'tos'=>'2',
                            'ascId'=>$ascId,
                            'gro_id'=>$gro_id,
                            'primary_provider_id_for_reports'=>$report_provider_id,
                            'sch_app_id' =>$imwApptId 
                        );
                        $sup_ins_id = DB::connection('DB_REGISTER_CONNECTION')->table('superbill')->insertGetId($ins_supper);
                        //if(imw_num_rows($chkSupBillRes)<=0) {
                       
                        $log .= "\n" . "SUPERBILL ID IS - " . $sup_ins_id;
                        $sql = "UPDATE facility SET encounterId = '" . ($enc_id + 1) . "' WHERE id='" . $sbfacilityId . "' ";
                        $row = DB::connection('DB_REGISTER_CONNECTION')->select($sql);// or die($sql . imw_error());

                        $sql = "UPDATE copay_policies SET Encounter_ID = '" . ($enc_id + 1) . "' WHERE policies_id='1' ";
                        $row = DB::connection('DB_REGISTER_CONNECTION')->select($sql);// or die($sql . imw_error());
                        //}
                        //====>now data base is surgerycenter<=======
                        //SURGERYCENTER CONNECTION
                        // start get anesthesia start/stop time from local anesthesia record
                        $anes_start_time = $anes_stop_time = "";
                        if ($group_anesthesia) {
                            $anesQry = "Select startTime, stopTime From localanesthesiarecord Where confirmation_id = " . $iASCConfirmationId . " LIMIT 0,1";
                            $anesSql = DB::selectone($anesQry);
                            if ($anesSql) {
                                $anesRes =$anesSql;
                                $anes_start_time = $this->setTmFormat($anesRes->startTime);
                                $anes_stop_time = $this->setTmFormat($anesRes->stopTime);
                            }
                            //echo 'Start Time: '.$anes_start_time."<br>";
                            //echo 'Stop Time: '.$anes_stop_time."<br>";
                        }
                        // End get anesthesia start/stop time from local anesthesia record

                        /** Start Get Cpt/Dx/Modifiers Detail From Superbill */
                        unset($condArr);
                        $condArr['confirmation_id'] = $pConfId;
                        $condArr['deleted'] = 0;
                        $condArr['bill_user_type'] = 2;
                        if ($group_anesthesia > 0) {
                            $condArr ['bill_user_type'] = 1;
                        } else if ($group_institution > 0) {
                            $condArr ['bill_user_type'] = 3;
                        }

                        $procData = $this->getMultiChkArrayRecords('superbill_tbl sb 
												INNER JOIN procedures pr ON(pr.procedureId = sb.cpt_id)
												INNER JOIN procedurescategory prc ON(prc.proceduresCategoryId = pr.catId)', $condArr, "prc.name = 'G-Codes' DESC, sb.cpt_code", 'Asc');

                        $procDataArr = array();
                        $proc_code = '';
                        $procDetailArr = array();

                        if (is_array($procData) && count($procData) > 0) {
                            foreach ($procData as $key => $procedure) {
                                array_push($procDataArr, $procedure->cpt_id);
                                //$procDetailArr['cptId'][$procedure->cpt_code]= $procedure->cpt_id ;
                                $procDetailArr['cpt'][$procedure->cpt_id] = $procedure->cpt_code;
                                $procDetailArr['dx'][$procedure->cpt_code] = $procedure->dxcode_icd10;
                                $procDetailArr['mod1'][$procedure->cpt_code] = $procedure->modifier1;
                                $procDetailArr['mod2'][$procedure->cpt_code] = $procedure->modifier2;
                                $procDetailArr['mod3'][$procedure->cpt_code] = $procedure->modifier3;
                                $procDetailArr['unit'][$procedure->cpt_code] = $procedure->quantity;
                                $procDetailArr['isAnes'][$procedure->cpt_code] = $procedure->bill_user_type;
                            }
                            $proc_code = implode(',', $procDataArr);
                        }
                        /** End Get Cpt/Dx/Modifiers Detail From Superbill */
                        $proc_id_final = array();
                        $proc_id_final_imp = "";
                        $proc_id_final_chk_imp = "";
                        $all_proc_id_final_imp = "";
                        $all_proc_id_final_chk_imp = "";
                        $all_proc_id_final_array = array();
                        if ($proc_code) {
                            $sql_qry_proc = "SELECT procedureId, code,codeFacility,codePractice FROM procedures where procedureId in (" . $proc_code . ")";
                            $sql_qry_res = DB::select($sql_qry_proc); //$proc_code variable get from discharge_summary_sheet.php line no.157
                            if ($sql_qry_res) {
                                $proc_id_final = array();
                                foreach($sql_qry_res as $sqlRow_proc) {
                                    if ($procDetailArr['cpt'][$sqlRow_proc->procedureId] <> $sqlRow_proc->code) {
                                        $sqlRow_proc['code'] = $procDetailArr['cpt'][$sqlRow_proc->procedureId];
                                    }

                                    $proc_id_final['default'][] = "'" . $sqlRow_proc->code . "'";

                                    if ($group_institution > 0) {
                                        if ($sqlRow_proc['codeFacility'] != "") {
                                            $proc_id_final['code_inst'][] = "'" . $sqlRow_proc->codeFacility . "'";
                                            $all_proc_id_final_array[$sqlRow_proc['codeFacility']] = "'" . $sqlRow_proc->code . "'";
                                        } elseif (getenv("STOP_PARENT_SUPERBILL") == "YES") {
                                            //DO NOTHING
                                        } else {
                                            $proc_id_final['code_inst'][] = "'" . $sqlRow_proc->code . "'";
                                            $all_proc_id_final_array[$sqlRow_proc->code] = "'" . $sqlRow_proc->code . "'";
                                        }
                                    } else if ($group_anesthesia > 0) {

                                        if ($sqlRow_proc['codePractice']!= "") {
                                            $proc_id_final['code_anes'][] = "'" . $sqlRow_proc->codePractice . "'";
                                            $all_proc_id_final_array[$sqlRow_proc->codePractice] = "'".$sqlRow_proc->code. "'";
                                        } elseif (getenv("STOP_PARENT_SUPERBILL") == "YES") {
                                            //DO NOTHING
                                        } else {
                                            $proc_id_final['code_anes'][] = "'" .$sqlRow_proc->code. "'";
                                            $all_proc_id_final_array[$sqlRow_proc->code] = "'" .$sqlRow_proc->code. "'";
                                        }
                                    } else {
                                        if ($sqlRow_proc->codePractice != "") {
                                            $proc_id_final['code_prac'][] = "'" . $sqlRow_proc->codePractice."'";
                                            $all_proc_id_final_array[$sqlRow_proc->codePractice] = "'" . $sqlRow_proc->code. "'";
                                        } elseif (constant("STOP_PARENT_SUPERBILL") == "YES") {
                                            //DO NOTHING
                                        } else {
                                            $proc_id_final['code_prac'][] = "'" . $sqlRow_proc->code . "'";
                                            $all_proc_id_final_array[$sqlRow_proc->code] = "'" . $sqlRow_proc->code. "'";
                                        }
                                    }
                                }

                                if ($group_institution > 0) {
                                    $groupKey = 'code_inst';
                                } else if ($group_anesthesia > 0) {
                                    $groupKey = 'code_anes';
                                } else {
                                    $groupKey = 'code_prac';
                                }
                                $proc_id_final_imp = implode(",", $proc_id_final[$groupKey]);
                                $proc_id_final_chk_imp = $proc_id_final_imp;
                                $all_proc_id_final_imp = implode(",", $all_proc_id_final_array);
                                $all_proc_id_final_chk_imp = $all_proc_id_final_imp;
                            }
                        }
                        $log .= "\n" . "GROUP KEY -" . $groupKey;
                        $log .= "\n" . "PROCEDURE ID STRING VAR proc_id_final_chk_imp - " . $proc_id_final_chk_imp;
                        $log .= "\n" . "ALL PROCEDURE ID STRING VAR all_proc_id_final_chk_imp - " . $all_proc_id_final_chk_imp;
                        /*
                          print_r($proc_id_final);echo '<br>';
                          print_r($all_proc_id_final_array);echo '<br>';
                          echo 'FinalCheck:-'.$proc_id_final_chk_imp . '<br>Final All Check :-'.$all_proc_id_final_chk_imp;
                         */


                        //START IF SIGN ALL BY SURGEON AND DX CODE TYPE IS NOT SET THEN GET DX CODE TYPE FROM ADMIN
                        if (!$dx_code_type) {
                            $queryDxTyp = DB::selectone("select `diagnosis_code_type` from `surgerycenter`");//or die(imw_error());
                            $dataDxTyp =$queryDxTyp;
                            if ($dataDxTyp->diagnosis_code_type) {
                                $dx_code_type = $dataDxTyp->diagnosis_code_type;
                            }
                        }
                        //END IF SIGN ALL BY SURGEON AND DX CODE TYPE IS NOT SET THEN GET DX CODE TYPE FROM ADMIN

                        if ($diag_ids || ($dos <= '2015-09-30' && trim($icd10_code) == '')) {
                            if ($diag_ids) {
                                $sql_qry_dx = "SELECT diag_code  FROM diagnosis_tbl  where diag_id in (" . $diag_ids . ")";
                                $sql_qry_dx = DB::select($sql_qry_dx); //$proc_code variable get from discharge_summary_sheet.php line no.156
                                if ($sql_qry_dx) {
                                    $diag_code_arr = '';
                                    foreach ($sql_qry_dx as $sqlRow_dx) {
                                        $diag_code_exp = explode(',', $sqlRow_dx->diag_code);
                                        $diag_code_arr[] = $diag_code_exp[0];
                                    }
                                }
                            }
                            $dx_code_type = 'icd9';
                        } elseif ($icd10_code) {
                            $diag_code_arr = explode(',', str_replace('@@', ',', $icd10_code));
                            $dx_code_type = 'icd10';
                        }
                        if (trim($dx_code_type)) {
                            if (strtolower($dx_code_type) == 'icd9') {
                                $dx_code_type_imedic = 0;
                            } else if (strtolower($dx_code_type) == 'icd10') {
                                $dx_code_type_imedic = 1;
                            }
                        }

                        $update_conf_sc = "update patientconfirmation set import_status='true' where patientConfirmationId='" . $pConfId . "'";
                        $update_conf_run_sc = DB::select($update_conf_sc);
                        //====end database===//


                        // Match Procedures Code with iDoc
                        $matchResultPracCode = array();
                        if (trim($proc_id_final_chk_imp)) {
                            $match_qry_proc_imw = "Select cpt4_code, cpt_prac_code From cpt_fee_tbl where cpt_prac_code in (" . $proc_id_final_chk_imp . ") and status='Active' AND delete_status = '0'  ORDER BY cpt_prac_code ";
                            $match_res_proc_imw = DB::connection('DB_REGISTER_CONNECTION')->select($match_qry_proc_imw);
                            if ($match_res_proc_imw) {
                                foreach($match_res_proc_imw as $match_row_proc_imw) {
                                    $practiceCode = $match_row_proc_imw->cpt_prac_code;
                                    $cptCode = $match_row_proc_imw->cpt4_code;
                                    $matchResultPracCode[$practiceCode] = array('prac_code' => $practiceCode, 'cpt_code' => $cptCode);
                                }
                            }
                        }
                        //echo '<br><br>'; print_r($matchResultPracCode);
                        // End Match Procedures Code with iDoc
                        // Match CPT4 Code with iDoc
                        $matchResultCptCode = array();
                        if (trim($all_proc_id_final_chk_imp)) {
                            $match_qry_cpt_imw = "Select cpt4_code, cpt_prac_code From cpt_fee_tbl where cpt4_code in (" . $all_proc_id_final_chk_imp . ") and status='Active' AND delete_status = '0' Group By cpt4_code ORDER BY cpt_prac_code ";
                            $match_res_cpt_imw = DB::connection('DB_REGISTER_CONNECTION')->select($match_qry_cpt_imw);
                            if ($match_res_cpt_imw) {
                                foreach($match_res_cpt_imw as $match_row_cpt_imw) {
                                    $practiceCode = $match_row_cpt_imw->cpt_prac_code;
                                    $cptCode = $match_row_cpt_imw->cpt4_code;
                                    $matchResultCptCode[$cptCode] = array('prac_code' => $practiceCode, 'cpt_code' => $cptCode);
                                }
                            }
                        }
                        //echo '<br><br>'; print_r($matchResultCptCode);
                        // End Match CPT4 Code with iDoc

                        $log .= "\n" . "PROC ID FINAL ARR - " . json_encode($proc_id_final);
                        foreach ($proc_id_final[$groupKey] as $temp_key => $temp_prac_code) {
                            $temp_prac_code = str_replace("'", '', $temp_prac_code);
                            if (!array_key_exists($temp_prac_code, $matchResultPracCode)) { //in_array($temp_prac_code,$matchResultPracCode)
                                $temp_cpt_code = str_replace("'", '', $all_proc_id_final_array[$temp_prac_code]);
                                if (array_key_exists($temp_cpt_code, $matchResultCptCode)) {
                                    $proc_id_final[$groupKey][$temp_key] = "'" . $matchResultCptCode[$temp_cpt_code]['prac_code'] . "'";
                                    $all_proc_id_final_array[$matchResultCptCode[$temp_cpt_code]['prac_code']] = $matchResultCptCode[$temp_cpt_code]['cpt_code'];
                                } elseif ($temp_cpt_code) {
                                    $proc_id_final[$groupKey][$temp_key] = "'" . $temp_cpt_code . "'";
                                    $all_proc_id_final_array[$temp_cpt_code] = "'" . $temp_cpt_code . "'";
                                }
                            }
                        }
                        $proc_id_final_imp = implode(',', $proc_id_final[$groupKey]);
                        $log .= "\n" . "FINAL PROCEDURES- " . $proc_id_final_imp;
                        // End Procedures Code with iDoc
                        /*
                          echo '<br> After Match:';
                          print_r($proc_id_final);echo '<br>';
                          print_r($all_proc_id_final_array);echo '<br>';
                          echo 'After Implode:-'.$proc_id_final_imp ;
                         */

                        //update dx code type in superbill
                        DB::select("update superbill set sup_icd10='$dx_code_type_imedic', anes_start_time = '" . $anes_start_time . "', anes_stop_time = '" . $anes_stop_time . "' WHERE idSuperBill = '" . $sup_ins_id . "'");

                        if (trim($proc_id_final_imp)) {
                            
                            //POE Object
                            $oPoe =new PoeController;
                            $oPoe->construct($imwPatientId, $enc_id);
                            
                            $sql_qry_proc_imw = "SELECT * FROM cpt_fee_tbl where cpt_prac_code in (" . $proc_id_final_imp . ") and status='Active' AND delete_status = '0'  ORDER BY cpt_prac_code";
                            $sql_qry_res_imw = DB::select($sql_qry_proc_imw);
                            if ($sql_qry_res_imw) {
                                $sqlRow_proc_imw = $proc_with_gcode = $proc_without_gcode = array();
                                foreach ($sql_qry_res_imw as $sqlRow_proc_imw_tmp) {
                                    $prac_code = $sqlRow_proc_imw_tmp->cpt_prac_code;
                                    if (strtoupper($prac_code[0]) == "G") {
                                        $proc_with_gcode[] = $sqlRow_proc_imw_tmp;
                                    } else {
                                        $proc_without_gcode[] = $sqlRow_proc_imw_tmp;
                                    }
                                }
                                $sqlRow_proc_imw = array_merge($proc_without_gcode, $proc_with_gcode);
                                $proCnt = 0;
                                $dx_count = 0;
                                $proc_id_imw_final = $proc_id_imw_final_str = array();
                                for ($i = 0; $i < count($sqlRow_proc_imw); $i++) {
                                    $proCnt++;
                                    $proc_id_imw_final[] = $sqlRow_proc_imw[$i]['cpt_prac_code'];
                                    $proc_id_imw_final_str[] = "'" . $sqlRow_proc_imw[$i]['cpt_prac_code'] . "'";
                                    $proc_cptcode_id = $sqlRow_proc_imw[$i]['cpt_prac_code'];
                                    $org_proc_cptcode = str_replace("'", '', $all_proc_id_final_array[$proc_cptcode_id]);
                                    $proc_desc_imw = $sqlRow_proc_imw[$i]['cpt_desc'];
                                    $proc_id_imw = $sqlRow_proc_imw[$i]['cpt_prac_code'];
                                    $proc_dx_codesArr = explode(',', $procDetailArr['dx'][$org_proc_cptcode]);

                                    $oPoe->isPoeCode($proc_cptcode_id);

                                    /* if($group_anesthesia > 0 )
                                      {
                                      $proc_unit	=	$sqlRow_proc_imw[$i]['units'];
                                      $proc_mod1	=	$sqlRow_proc_imw[$i]['mod1'];
                                      $proc_mod2	=	$sqlRow_proc_imw[$i]['mod2'];
                                      $proc_mod3	=	$sqlRow_proc_imw[$i]['mod3'];
                                      }
                                      else */ {
                                        $proc_unit = $procDetailArr['unit'][$org_proc_cptcode];
                                        $proc_mod1 = $procDetailArr['mod1'][$org_proc_cptcode];
                                        $proc_mod2 = $procDetailArr['mod2'][$org_proc_cptcode];
                                        $proc_mod3 = $procDetailArr['mod3'][$org_proc_cptcode];
                                    }

                                    $isAnes = $procDetailArr['isAnes'][$org_proc_cptcode];

                                    $dx1 = $dx2 = $dx3 = $dx4 = $dx5 = $dx6 = $dx7 = $dx8 = $dx9 = $dx10 = $dx11 = $dx12 = '';
                                    for ($loop = 0; $loop < 12; $loop++) {
                                        $diagArrKey = array_search($proc_dx_codesArr[$loop], $diag_code_arr);
                                        if ($diagArrKey >= 0 && $proc_dx_codesArr[$loop] != '') {
                                            $diagArrKeyNew = $diagArrKey + 1;
                                            $varName = 'dx' . $diagArrKeyNew;
                                            $$varName = $proc_dx_codesArr[$loop];
                                        }
                                    }

                                    $insUpdtProQry = " INSERT INTO ";
                                    $insUpdtProWhrQry = "";

                                    $ins_pro_imw = $insUpdtProQry . " procedureinfo set cptCode ='" . $proc_cptcode_id . "',
											procedureName='" . addslashes($proc_desc_imw) . "',description ='" . addslashes($proc_desc_imw) . "',
											idSuperBill ='" . $sup_ins_id . "',units ='" . $proc_unit . "',
											dx1='" . $dx1 . "',dx2='" . $dx2 . "',dx3='" . $dx3 . "',dx4='" . $dx4 . "',dx5='" . $dx5 . "',dx6='" . $dx6 . "',
											dx7='" . $dx7 . "',dx8='" . $dx8 . "',dx9='" . $dx9 . "',dx10='" . $dx10 . "',dx11='" . $dx11 . "',dx12='" . $dx12 . "',
											modifier1='" . $proc_mod1 . "',modifier2='" . $proc_mod2 . "',modifier3='" . $proc_mod3 . "',
											porder='" . $proCnt . "' " . $insUpdtProWhrQry;

                                    $ins_pro_run_imw = DB::select($ins_pro_imw);
                                }

                                if (getenv("LOG_SYNC_SUPERBILL") == 'YES') {
                                    $diffArr = array();
                                    $diffArr = array_diff($proc_id_final[$groupKey], $proc_id_imw_final_str);
                                    if (count($diffArr) > 0) {
                                        $log .= "\n" . "CPT CODE not found in iDOC SIDE- " . json_encode($diffArr);
                                        $log .= "\n" . "==============================";
                                        $rootServerPath = getenv('ROOT_PATH');
                                        $logFolderPath = $rootServerPath.'/'.getenv('APP_ROOT')."/".getenv('surgeryCenterDirectoryName') . "/admin/pdfFiles/superbill_sync_log";
                                        if (!is_dir($logFolderPath)) {
                                            mkdir($logFolderPath, 0777);
                                        }
                                        $file_name = 'log_sync_superbill_' . date('Y_m_d') . '.txt';
                                        $file_path = $logFolderPath . '/' . $file_name;
                                        file_put_contents($file_path, $log, FILE_APPEND);
                                    }
                                }

                                $arrDxCodes = array();
                                $str_dx_codes = "";
                                for ($i = 1; $i <= 12; $i++) {
                                    $ic = $i - 1;
                                    $arrDxCodes[$i] = $diag_code_arr[$ic];
                                }
                                $str_dx_codes = serialize($arrDxCodes);
                                $proc_code_order = implode(',', $proc_id_imw_final);
                                $update_supper = "update superbill set procOrder ='" . $proc_code_order . "',
											arr_dx_codes='" . imw_real_escape_string($str_dx_codes) . "'
											where encounterId='" . $enc_id . "'";
                                $update_supper_run = DB::select($update_supper);

                                //Set POE DATE 
                                $oPoe->setPoeEnId();
                            }
                        } else {
                            if (getenv("LOG_SYNC_SUPERBILL") == 'YES') {
                                $log .= "\n" . "NO CPT CODE selected";
                                $log .= "\n" . "==============================";
                                $rootServerPath = getenv('imwPracticeURL');
                                $logFolderPath = $rootServerPath.'/'.getenv('APP_ROOT')."/".getenv('surgeryCenterDirectoryName') . "/admin/pdfFiles/superbill_sync_log";
                                if (!is_dir($logFolderPath)) {
                                    mkdir($logFolderPath, 0777);
                                }
                                $file_name = 'log_sync_superbill_' . date('Y_m_d') . '.txt';
                                $file_path = $logFolderPath . '/' . $file_name;
                                file_put_contents($file_path, $log, FILE_APPEND);
                            }
                        }
                    }
                    //===end====	
                }
            }

        }
    }

    public function sync_sx_procedure($pConfId) {
        $sxProcQry = "SELECT pdt.imwPatientId,pc.dos,pc.site,pr.name as proc_name, opr.manufacture, opr.lensBrand, opr.model, opr.Diopter 
				FROM patientconfirmation pc
				LEFT JOIN patient_data_tbl pdt ON (pdt.patient_id = pc.patientId)
				LEFT JOIN procedures pr ON (pr.procedureId = pc.patient_primary_procedure_id)
				LEFT JOIN operatingroomrecords opr ON (opr.confirmation_id = pc.patientConfirmationId)
				WHERE pc.patient_primary_procedure_id !='0' AND pc.patientConfirmationId = '" . $pConfId. "'";
        $sxProcRes = DB::selectone($sxProcQry);// or die($sxProcQry . imw_error());
        if ($sxProcRes) {
            $sxProcRow =$sxProcRes;
            $sxImwPatientId = $sxProcRow->imwPatientId;
            $sxDOS = $sxProcRow->dos;
            $sxSite = $sxProcRow->site;
            $sxMan = $sxProcRow->manufacture;
            $sxLensBrand = $sxProcRow->lensBrand;
            $sxModel = $sxProcRow->model;
            $sxDiopter = $sxProcRow->Diopter;
            $sxComments = "";
            if ($sxMan) {
                $sxComments.="Manufacturer:" . addslashes($sxMan) . '\n';
            }
            if ($sxLensBrand) {
                $sxComments.="LensBrand:" . addslashes($sxLensBrand) . '\n';
            }
            if ($sxModel) {
                $sxComments.="Model:" . addslashes($sxModel) . '\n';
            }
            if ($sxDiopter) {
                $sxComments.="Diopter:" . addslashes($sxDiopter) . '\n';
            }
            $chkListQry = "SELECT id FROM lists WHERE type = '6' AND allergy_status	= 'Active' AND begdate = '" . $sxDOS . "' AND scemr_confirmation_id = '" . $pConfId . "' AND pid = '" . $sxImwPatientId . "' AND pid !='0' ";
            $chkListRes = DB::connection('DB_REGISTER_CONNECTION')->selectone($chkListQry);
            $saveListQry = " INSERT INTO ";
            $saveListWhrQry = "";
            if ($chkListRes) {
                $saveListQry = " UPDATE ";
                $saveListWhrQry = " WHERE scemr_confirmation_id = '" .$pConfId."' ";
            }
            $sxSiteQry = "";
            if ($sxSite <= 3) {
                $sxSiteQry = " sites = '" . $sxSite . "', ";
            }
            $saveListQry .= " lists SET 
								title 					= '" . addslashes($sxProcRow->proc_name) . "',
								date					= '" . date("Y-m-d H:i:s") . "',
								type 					= '6',
								begdate					= '" . $sxDOS . "',
								pid 					= '" . $sxImwPatientId . "',
								allergy_status			= 'Active',
								comments				= '" . $sxComments . "',
								$sxSiteQry
								scemr_confirmation_id 	= '" .$pConfId. "'
								" . $saveListWhrQry;
            $saveListRes =DB::connection('DB_REGISTER_CONNECTION')->select($saveListQry);
            
        }
    }

    public function superbill_del(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $loginUserType = $request->json()->get('UserType') ? $request->json()->get('UserType') : $request->input('UserType');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $superbill_id = $request->json()->get('superbill_id') ? $request->json()->get('superbill_id') : $request->input('superbill_id');
        $data = [];
        $status = 0;
        $delStatus = 0;
        $message = " unauthorized ";
        $requiredStatus = [];
        $userId = $this->checkToken($userToken);
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
                /* Delete Super Bill Records */
                unset($insertUpdateRecord);
                $insertUpdateRecord['deleted'] = 1;
                $insertUpdateRecord['modified_by'] = $userId;
                $insertUpdateRecord['modified_on'] = date('Y-m-d H:i:s');
                DB::table('superbill_tbl')->where('superbill_id', $superbill_id)->update($insertUpdateRecord);
                $delStatus = 1;
                $status = 1;
                $message = "Record updated successfully !";
            }
            return response()->json(['status' => $status, 'message' => $message, 'delStatus' => $delStatus, 'requiredStatus' => '', 'data' => $data,
                            ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
        }
    }
    
    //public function del
    
}
