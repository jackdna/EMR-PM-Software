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

class PrePostOPController extends Controller {

    public function nursePrePostOP(Request $request) {
        $userToken = $request->input('user_token');
        $iasc_facility_id = $request->input('iasc_facility_id');
        $selected_date = $request->input('dos');
        if ($iasc_facility_id) {
            $fac_con = " and st.iasc_facility_id='$iasc_facility_id'";
        }
        $andstubIdQry = "";
        $data = [];
        $status = 0;
        $requiredStatus = 1;
        $message = " unauthorized ";
        $res = '';
        $patient_confirmation_id_arr = [];
        $pendingStubRowArr = [];
        $stubRowArr = [];

        if ($this->checkToken($userToken)) {
            //END CODE TO SET PATIENT APPOINTMENT STATUS IN imwemr
            $stubQry = "SELECT 
                            TRIM(CONCAT(st.patient_last_name,', ',st.patient_first_name,' ',st.patient_middle_name)) as patient_name , 
                            DATE_FORMAT(st.patient_dob,'%m/%d/%Y') as dob, st.patient_primary_procedure, st.patient_confirmation_id,  
                            pr.signSurgeon1Id AS preOpPhySignSurgeonId,  pr.signNurse1Id AS preOpPhySignNurse1Id, pr.notedByNurse as preOpOrdersNoted,
                            ps.notedByNurse as postOpOrdersNoted, ps.version_num as postOpPhysicianVersionNum, 
                            ps.signSurgeon1Id AS postOpPhySignSurgeonId,  ps.signNurse1Id AS postOpPhySignNurse1Id,
                            lp.signSurgeon1Id AS laserProcedureSignSurgeonId,  lp.signNurseId AS laserProcedureSignNurseId,
                            pcr.catId as procedureCatId,stub_id,DATE_FORMAT(st.dos,'%m/%d/%Y') as dos
                    FROM stub_tbl st
                    LEFT JOIN patientconfirmation pc ON (pc.patientConfirmationId = st.patient_confirmation_id)
                    LEFT JOIN preopphysicianorders pr ON (pr.patient_confirmation_id = st.patient_confirmation_id AND pr.patient_confirmation_id !='0')
                    LEFT JOIN postopphysicianorders ps ON (ps.patient_confirmation_id = st.patient_confirmation_id AND ps.patient_confirmation_id !='0')
                    LEFT JOIN laser_procedure_patient_table lp ON (lp.confirmation_id = st.patient_confirmation_id AND pr.patient_confirmation_id !='0')
                    LEFT JOIN procedures pcr ON (pc.patient_primary_procedure_id = pcr.procedureId )
                    WHERE st.dos = '" . $selected_date . "' 
                                            AND st.patient_status != 'Canceled'
                                            AND pc.prim_proc_is_misc = ''
                                            AND 
                                            (
                                                    (pr.signSurgeon1Id > 0 AND ps.signSurgeon1Id > 0 AND pr.form_status <> '' AND ps.form_status <> '') 
                                                     OR
                                                    (lp.signSurgeon1Id > 0 AND lp.form_status <> '' AND pcr.catId = '2' )
                                            ) 
                                            $fac_con
            
                    ORDER BY st.surgery_time, st.surgeon_fname";
            // echo $stubQry;
            $res = DB::select($stubQry);
            $medicationNameArr = array();
            $strengthArr = array();
            $directionArr = array();
            $postMedicationNameArr = array();

            foreach ($res as $stubRow) {
                $preOpPhySignSurgeonId = $stubRow->preOpPhySignSurgeonId;
                $postOpPhySignSurgeonId = $stubRow->postOpPhySignSurgeonId;
                $laserProcSignSurgeonId = $stubRow->laserProcedureSignSurgeonId;
                $preOpPhySignNurse1Id = $stubRow->preOpPhySignNurse1Id;
                $postOpPhySignNurse1Id = $stubRow->postOpPhySignNurse1Id;
                $laserProcSignNurseId = $stubRow->laserProcedureSignNurseId;
                $procedureCatId = $stubRow->procedureCatId;
                $preOpOrdersNoted = $stubRow->preOpOrdersNoted;
                $postOpOrdersNoted = $stubRow->postOpOrdersNoted;
                // $preOpPhysicianVersionNum = $stubRow->preOpPhysicianVersionNum;
                $postOpPhysicianVersionNum = $stubRow->postOpPhysicianVersionNum;
                // Start Getting Pre Op Medications
                $medicationQry = "SELECT medicationName, strength, direction, patient_confirmation_id, sourcePage FROM patientpreopmedication_tbl WHERE patient_confirmation_id IN(" . $stubRow->patient_confirmation_id . ")";
                $medicationRes = DB::select($medicationQry);
                unset($medicationNameArr);
                unset($strengthArr);
                unset($directionArr);
                if ($medicationRes) {
                    foreach ($medicationRes as $medicationRow) {
                        $pConfIdMed = $medicationRow->patient_confirmation_id;
                        $source = $medicationRow->sourcePage;
                        $medicationNameArr[] = $medicationRow->medicationName;
                        $strengthArr[] = $medicationRow->strength;
                        $directionArr[] = $medicationRow->direction;
                    }
                }
                // Start Getting Post Op Medications
                $medicationQry = "SELECT physician_order_name, confirmation_id, physician_order_type, physician_order_time FROM patient_physician_orders WHERE confirmation_id IN(" . $stubRow->patient_confirmation_id . ") AND  chartName = 'post_op_physician_order_form' ";
                $medicationRes = DB::select($medicationQry);
                if ($medicationRes) {
                    unset($postMedicationNameArr);
                    foreach ($medicationRes as $medicationRow) {
                        $pConfIdMed = $medicationRow->confirmation_id;
                        $physician_order_type = $medicationRow->physician_order_type;
                        $physician_order_time = $medicationRow->physician_order_time;
                        if (strtolower($physician_order_type) == "medication" || (trim($physician_order_type) == "" && $physician_order_time != "00:00:00")) {
                            $postMedicationNameArr[] = $medicationRow->physician_order_name;
                        }
                    }
                    //$postMedicationNameArr=  array_unique($postMedicationNameArr);
                }
                if (isset($medicationNameArr)) {
                    $j = 0;
                    for ($i = 0; $i < count($medicationNameArr); $i++) {
                        if (isset($postMedicationNameArr[$i]) && $postMedicationNameArr[$i] <> "") {
                            $j++;
                        }
                    }
                    for ($k = $j; $k < count($medicationNameArr); $k++) {
                        if (isset($postMedicationNameArr)) {
                            if (count($postMedicationNameArr) < count($medicationNameArr)) {
                                //  $postMedicationNameArr[] = "";
                            }
                        }
                    }
                }
                if (($preOpPhySignSurgeonId > 0 && $postOpPhySignSurgeonId > 0) || ($procedureCatId == '2' && $laserProcSignSurgeonId > 0 )) {
                    if (($preOpPhySignNurse1Id == 0 || $preOpOrdersNoted == 0 || (($postOpPhySignNurse1Id == 0 || $postOpOrdersNoted == 0) && $postOpPhysicianVersionNum > 2) ) && $procedureCatId <> '2') {
                        $stubRow->Medication = isset($medicationNameArr) ? $medicationNameArr : [""];
                        $stubRow->Strength = isset($strengthArr) ? $strengthArr : [""];
                        $stubRow->Direction = isset($directionArr) ? $directionArr : [""];
                        $stubRow->postMedicationNameAr = isset($postMedicationNameArr) ? $postMedicationNameArr : [""];
                        $pendingStubRowArr[] = $stubRow;
                    } elseif ($laserProcSignNurseId == 0 && $procedureCatId == '2') {
                        $stubRow->Medication = isset($medicationNameArr) ? $medicationNameArr : [""];
                        $stubRow->Strength = isset($strengthArr) ? $strengthArr : [""];
                        $stubRow->Direction = isset($directionArr) ? $directionArr : [""];
                        $stubRow->postMedicationNameAr = isset($postMedicationNameArr) ? $postMedicationNameArr : [""];
                        $pendingStubRowArr[] = $stubRow;
                    } else {
                        $stubRow->Medication = isset($medicationNameArr) ? $medicationNameArr : [""];
                        $stubRow->Strength = isset($strengthArr) ? $strengthArr : [""];
                        $stubRow->Direction = isset($directionArr) ? $directionArr : [""];
                        $stubRow->postMedicationNameAr = isset($postMedicationNameArr) ? $postMedicationNameArr : [""];
                        $stubRowArr[] = $stubRow;
                    }
                }
            }
            $requiredStatus = 0;
            $message = " status updated ";
            $status = 1;
        }

        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => $requiredStatus,
                    'data' => [['pendingStub' => $pendingStubRowArr, 'Stub' => $stubRowArr]],
        ]); // NOT_FOUND (404) being the HTTP response code 
    }

    public function nursePrePostOPMove(Request $request) {
        $userToken = $request->json()->get('user_token');
        $pConfirmId = $request->json()->get('pConfirmId');
        $postOpDropExists_pConfirmId = $request->json()->get('postMedicationNameArr'); //when
        $selected_date = $request->json()->get('dos');
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
            } else if ($postOpDropExists_pConfirmId == "") {
                $message = " PostMedicationName is missing ";
                $status = 1;
                $requiredStatus = 0;
            } else {
                $status = 1;
                $loggedInUserId = $user_id;
                //GET USER NAME
                $ViewUserNameQry = "select * from `users` where  usersId = '" . $loggedInUserId . "'";
                $ViewUserNameRow = DB::select($ViewUserNameQry); // or die($ViewUserNameQry . imw_error());

                $loggedInUserFirstName = $ViewUserNameRow[0]->fname;
                $loggedInUserMiddleName = $ViewUserNameRow[0]->mname;
                $loggedInUserLastName = $ViewUserNameRow[0]->lname;

                $signOnFileStatus = 'Yes';
                $signDateTime = date("Y-m-d H:i:s");

                $loggedInUserName = $ViewUserNameRow[0]->lname . ", " . $ViewUserNameRow[0]->fname . " " . $ViewUserNameRow[0]->mname;
                //END GET USER  NAME
                $laserSignCatIdArr = array();
                $getConfirmationDetailQry = "SELECT patientConfirmationId,patient_primary_procedure_id FROM `patientconfirmation` WHERE  dos='" . $selected_date . "'";
                $getConfirmationDetailRes = DB::select($getConfirmationDetailQry); // or die($getConfirmationDetailQry . imw_error());
                if ($getConfirmationDetailRes) {
                    foreach ($getConfirmationDetailRes as $getConfirmationDetailRow) {
                        $ptConfId = $getConfirmationDetailRow->patientConfirmationId;
                        $patient_primary_procedure_id = $getConfirmationDetailRow->patient_primary_procedure_id;
                        $getLaserCatIdDetailQry = "SELECT catId FROM `procedures` WHERE procedureId='" . $patient_primary_procedure_id . "'  AND procedureId!='0'";
                        $getLaserCatIdDetailRes = DB::select($getLaserCatIdDetailQry); // or die($getLaserCatIdDetailQry . imw_error());
                        if ($getLaserCatIdDetailRes) {
                            $laserSignCatId = $getLaserCatIdDetailRes[0]->catId;
                            $laserSignCatIdArr[$ptConfId] = $laserSignCatId;
                        }
                    }
                }

                //CODE TO MAKE SIGNATURES OF SURGEON IN PRE-OP ORDER CHART NOTES OF PATIENT(IN OPENED DOS)
                $laserConfIdArr = $phyConfIdArr = array();
                $patientConfirmationIdArr = explode(",", $pConfirmId);
                foreach ($patientConfirmationIdArr as $key => $patientConfirmationIdVal) {
                    $catId = isset($laserSignCatIdArr[$patientConfirmationIdVal]) ? $laserSignCatIdArr[$patientConfirmationIdVal] : 0;
                    if ($catId == 2) {
                        $laserConfIdArr[] = $patientConfirmationIdVal;
                    } else {
                        $phyConfIdArr[] = $patientConfirmationIdVal;
                    }
                }
                $laserConfId = implode(",", $laserConfIdArr);
                $phyConfId = implode(",", $phyConfIdArr);

                if (!trim($phyConfId)) {
                    $phyConfId = 0;
                }
                if (!trim($laserConfId)) {
                    $laserConfId = 0;
                }

                $SaveSignArr = array('preopphysicianorders', 'postopphysicianorders', 'laser_procedure_patient_table');
                $affectedRows = 0;
                foreach ($SaveSignArr as $SaveSignArrTableName) {
                    $signId = ($SaveSignArrTableName == 'preopphysicianorders' || $SaveSignArrTableName == 'postopphysicianorders') ? '1' : '';
                    $signUserId = 'signNurse' . $signId . 'Id';
                    $signUserFirstName = 'signNurse' . $signId . 'FirstName';
                    $signUserMiddleName = 'signNurse' . $signId . 'MiddleName';
                    $signUserLastName = 'signNurse' . $signId . 'LastName';
                    $signUserStatus = 'signNurse' . $signId . 'Status';
                    $signUserDateTime = 'signNurse' . $signId . 'DateTime';
                    $signUserconfirmation_id = 'patient_confirmation_id';

                    if ($SaveSignArrTableName == "laser_procedure_patient_table") {
                        $signUserconfirmation_id = 'confirmation_id';
                    }

                    $inFields = $phyConfId;
                    if ($SaveSignArrTableName == 'laser_procedure_patient_table') {
                        $inFields = $laserConfId;
                    }
                    $andQry = " AND $signUserconfirmation_id IN(" . $inFields . ")";

                    $ordersNotedQry = '';
                    if ($SaveSignArrTableName == 'preopphysicianorders' || $SaveSignArrTableName == 'postopphysicianorders') {
                        $ordersNotedQry = ', notedByNurse = 1 ';
                    }

                    $SaveSignQry = "update $SaveSignArrTableName set 
										$signUserFirstName = IF($signUserId = 0, '" . addslashes($loggedInUserFirstName) . "',$signUserFirstName), 
										$signUserMiddleName = IF($signUserId = 0, '" . addslashes($loggedInUserMiddleName) . "',$signUserMiddleName),
										$signUserLastName = IF($signUserId = 0, '" . addslashes($loggedInUserLastName) . "',$signUserLastName), 
										$signUserStatus = IF($signUserId = 0, '$signOnFileStatus',$signUserStatus),
										$signUserDateTime = IF($signUserId = 0, '$signDateTime',$signUserDateTime),
										$signUserId = IF($signUserId = 0, '$loggedInUserId',$signUserId)
										$ordersNotedQry
										WHERE (form_status = 'not completed' OR form_status = 'completed')
													" . $andQry;


                    $SaveSignRes = DB::select($SaveSignQry); // or die('Error occured at line no.' . (__LINE__) . $SaveSignQry . imw_error());
                    //echo 'Rows Affected : '.$affectedRows .'<br>'	;


                    if ($inFields <> 0) {
                        //SET FORM STATUS TO SHOW RED OR GREEN FLAG) IN LEFT FORM SLIDER	
                        $tableFormStatusQry = "SELECT * FROM $SaveSignArrTableName WHERE " . str_replace('AND', '', $andQry);
                        $tableFormStatusRes = DB::select($tableFormStatusQry); // or die($tableFormStatusQry . imw_error());

                        if ($tableFormStatusRes) {
                            foreach ($tableFormStatusRes as $tableFormStatusRow) {
                                $confirmationId = '';
                                $confirmationId = $tableFormStatusRow->$signUserconfirmation_id;
                                //update form status 
                                $tableFormStatus = 'completed';
                                $laserSignCatId = isset($laserSignCatIdArr->$confirmationId) ? $laserSignCatIdArr->$confirmationId : 0;

                                if ($SaveSignArrTableName == "preopphysicianorders") {
                                    $leftNaviFieldName = "pre_op_physician_order_form";
                                    $version_num = $tableFormStatusRow->version_num;

                                    $signNurseIdPreOpPhy = $tableFormStatusRow->signNurseId;
                                    $signNurse1IdPreOpPhy = $tableFormStatusRow->signNurse1Id;
                                    $signSurgeon1IdPreOpPhy = $tableFormStatusRow->signSurgeon1Id;
                                    $notedByNurse = $tableFormStatusRow->notedByNurse;

                                    if ($signSurgeon1IdPreOpPhy == "0" || $signNurse1IdPreOpPhy == "0" || $notedByNurse == 0) {
                                        $tableFormStatus = 'not completed';
                                    }

                                    // Start Validate chart if form is on version no. 2
                                    if ($version_num == 1 && $tableFormStatus == 'completed' && $signNurseIdPreOpPhy == "0") {
                                        $tableFormStatus = 'not completed';
                                    }
                                    // End Validate chart if form is on version no. 2 
                                } else if ($SaveSignArrTableName == "postopphysicianorders") {

                                    $leftNaviFieldName = "post_op_physician_order_form";
                                    $post_op_version_num = $tableFormStatusRow->version_num;

                                    $chbx_pa = $tableFormStatusRow->patientAssessed;
                                    $chbx_vs = $tableFormStatusRow->vitalSignStable;
                                    $chbx_ec = $tableFormStatusRow->postOpEvalDone;
                                    $chbx_wr = $tableFormStatusRow->postOpInstructionMethodWritten;
                                    $chbx_vbl = $tableFormStatusRow->postOpInstructionMethodVerbal;
                                    $chbx_waar = $tableFormStatusRow->patientAccompaniedSafely;
                                    $chk_signNurseId = $tableFormStatusRow->signNurseId;
                                    $chk_signNurse1Id = $tableFormStatusRow->signNurse1Id;
                                    $chk_signSurgeon1Id = $tableFormStatusRow->signSurgeon1Id;
                                    $postOpDropExist = isset($postOpDropExists_pConfirmId[$confirmationId])?$postOpDropExists_pConfirmId[$confirmationId]:""; //$_POST['postOpDropExist_' . $confirmationId];
                                    $postOpNotedByNurse = $tableFormStatusRow->notedByNurse;

                                    if (($chbx_pa <> 'Yes') || ($chbx_vs <> 'Yes') || ($chbx_ec <> 'Yes') || ($chbx_wr <> 'Yes') || ($chbx_vbl <> 'Yes') || ($chbx_waar <> 'Yes') || ($chk_signNurseId == "0") || ($chk_signSurgeon1Id == "0") || ($chk_signNurse1Id == "0" && $post_op_version_num > 2) || ($postOpNotedByNurse == "0" && $post_op_version_num > 2) || ($postOpDropExist != 'Yes')
                                    ) {
                                        $tableFormStatus = 'not completed';
                                    }
                                } else if ($SaveSignArrTableName == "laser_procedure_patient_table" && $laserSignCatId == '2') {

                                    $leftNaviFieldName = "laser_procedure_form";

                                    $chk_laser_chief_complaint = $tableFormStatusRow->chk_laser_chief_complaint;
                                    $laser_chief_complaint = $tableFormStatusRow->laser_chief_complaint;

                                    $chk_laser_past_med_hx = $tableFormStatusRow->chk_laser_past_med_hx;
                                    $laser_past_med_hx = $tableFormStatusRow->laser_past_med_hx;

                                    $chk_laser_present_illness_hx = $tableFormStatusRow->chk_laser_present_illness_hx;
                                    $laser_present_illness_hx = $tableFormStatusRow->laser_present_illness_hx;

                                    $chk_laser_medication = $tableFormStatusRow->chk_laser_medication;
                                    $laser_medication = $tableFormStatusRow->laser_medication;

                                    $allergies_status_reviewed = $tableFormStatusRow->allergies_status_reviewed;

                                    $verified_nurse_name = $tableFormStatusRow->verified_nurse_name;
                                    $verified_surgeon_Name = $tableFormStatusRow->verified_surgeon_Name;

                                    $best_correction_vision_R = $tableFormStatusRow->best_correction_vision_R;
                                    $best_correction_vision_L = $tableFormStatusRow->best_correction_vision_L;
                                    $laser_sle = $tableFormStatusRow->laser_sle;
                                    $laser_mental_state = $tableFormStatusRow->laser_mental_state;
                                    $pre_laser_IOP_R = $tableFormStatusRow->pre_laser_IOP_R;
                                    $pre_laser_IOP_L = $tableFormStatusRow->pre_laser_IOP_L;
                                    $pre_iop_na = $tableFormStatusRow->pre_iop_na;
                                    $laser_fundus_exam = $tableFormStatusRow->laser_fundus_exam;
                                    $laser_comments = $tableFormStatusRow->laser_comments;
                                    $laser_other = $tableFormStatusRow->laser_other;


                                    $chk_laser_pre_op_diagnosis = $tableFormStatusRow->chk_laser_pre_op_diagnosis;
                                    $pre_op_diagnosis = $tableFormStatusRow->pre_op_diagnosis;
                                    $laser_other_pre_medication = $tableFormStatusRow->laser_other_pre_medication;

                                    $prelaserVitalSignBP = $tableFormStatusRow->prelaserVitalSignBP;
                                    $prelaserVitalSignP = $tableFormStatusRow->prelaserVitalSignP;
                                    $prelaserVitalSignR = $tableFormStatusRow->prelaserVitalSignR;
                                    $laser_spot_duration = $tableFormStatusRow->laser_spot_duration;
                                    $laser_spot_size = $tableFormStatusRow->laser_spot_size;
                                    $laser_power = $tableFormStatusRow->laser_power;
                                    $laser_shots = $tableFormStatusRow->laser_shots;
                                    $laser_total_energy = $tableFormStatusRow->laser_total_energy;
                                    $laser_degree_of_opening = $tableFormStatusRow->laser_degree_of_opening;
                                    $laser_exposure = $tableFormStatusRow->laser_exposure;
                                    $laser_count = $tableFormStatusRow->laser_count;
                                    $postlaserVitalSignBP = $tableFormStatusRow->postlaserVitalSignBP;
                                    $postlaserVitalSignP = $tableFormStatusRow->postlaserVitalSignP;
                                    $postlaserVitalSignR = $tableFormStatusRow->postlaserVitalSignR;
                                    $post_op_operative_comment = $tableFormStatusRow->post_op_operative_comment;
                                    $laser_post_progress = $tableFormStatusRow->laser_post_progress;
                                    $laser_post_operative = $tableFormStatusRow->laser_post_operative;
                                    $iop_pressure_l = $tableFormStatusRow->iop_pressure_l; //
                                    $iop_pressure_r = $tableFormStatusRow->iop_pressure_r; //

                                    $iop_na = $tableFormStatusRow->iop_na;

                                    $signNurseIdlaser_procedure = $tableFormStatusRow->signNurseId;
                                    $signSurgeon1Idlaser_procedure = $tableFormStatusRow->signSurgeon1Id;

                                    if (( ($laser_chief_complaint == '') && ($chk_laser_chief_complaint == 'on') &&
                                            ($laser_past_med_hx == '') && ($chk_laser_past_med_hx == 'on') &&
                                            ($laser_present_illness_hx == '') && ($chk_laser_present_illness_hx == 'on') &&
                                            ($laser_medication == '') && ($chk_laser_medication == 'on')
                                            ) ||
                                            ($verified_nurse_name == '' || $verified_surgeon_Name == '' ) ||
                                            ( ($pre_op_diagnosis == '') && ($chk_laser_pre_op_diagnosis == 'on')) ||
                                            ( ($prelaserVitalSignBP == '') && ($prelaserVitalSignP == '' ) &&
                                            ($prelaserVitalSignR == '') && ($laser_spot_duration == '') &&
                                            ($laser_spot_size == '') && ($laser_power == '' ) &&
                                            ($laser_shots == '') && ($laser_total_energy == '') &&
                                            ($laser_degree_of_opening == '') && ($laser_exposure == '' ) &&
                                            ($laser_count == '') && ($postlaserVitalSignBP == '') &&
                                            ($postlaserVitalSignP == '') && ($postlaserVitalSignR == '') &&
                                            ($laser_post_progress == '')
                                            ) ||
                                            (($pre_laser_IOP_R == '') && ($pre_laser_IOP_L == '' ) && ($pre_iop_na == '' )) ||
                                            (($iop_pressure_l == '') && ($iop_pressure_r == '') && ($iop_na == '')) ||
                                            ($signNurseIdlaser_procedure == "0") || ($signSurgeon1Idlaser_procedure == "0")
                                    ) {
                                        $tableFormStatus = 'not completed';
                                    }
                                }

                                if ($SaveSignArrTableName == "laser_procedure_patient_table" && $laserSignCatId <> '2') {
                                    //DO NOT UPDATE FORM STATUS FOR Laser Chartnote if procedure is not laser
                                } else if ($laserSignCatId == '2' && ($SaveSignArrTableName == "preopphysicianorders" || $SaveSignArrTableName == "postopphysicianorders")) {
                                    //DO NOT UPDATE FORM STATUS FOR preopphysicianorders  Chartnote if procedure is laser
                                } else {
                                    $updateTableFormStatusQry = "update $SaveSignArrTableName SET form_status='$tableFormStatus' WHERE $signUserconfirmation_id='" . $tableFormStatusRow->$signUserconfirmation_id . "' ";
                                    DB::select($updateTableFormStatusQry); // or die('Error occured at line no. ' . (__LINE__) . ': ' . imw_error());
                                }
                                if (isset($tableFormStatusRow->$signUserconfirmation_id)) {
                                    //CODE TO CHECK SURGEON ALL SIGNATURE AND SET VALUE IN STUB TABLE
                                    $chartSignedBySurgeon = $this->chkSurgeonSignNew($tableFormStatusRow->$signUserconfirmation_id);
                                    $updateStubTblQry = "UPDATE stub_tbl SET chartSignedBySurgeon='" . $chartSignedBySurgeon . "' WHERE patient_confirmation_id='" . $tableFormStatusRow->$signUserconfirmation_id . "'";
                                    $updateStubTblRes = DB::select($updateStubTblQry); // or die($updateStubTblQry . imw_error());
                                    //END CODE TO CHECK SURGEON ALL SIGNATURE AND SET VALUE IN STUB TABLE
                                    //CODE TO CHECK ANESTHESIOLOGIST ALL SIGNATURE AND SET VALUE IN STUB TABLE
                                    $chartSignedByAnes = $this->chkAnesSignNew($tableFormStatusRow->$signUserconfirmation_id);
                                    $updateAnesStubTblQry = "UPDATE stub_tbl SET chartSignedByAnes='" . $chartSignedByAnes . "' WHERE patient_confirmation_id='" . $tableFormStatusRow->$signUserconfirmation_id . "'";
                                    $updateAnesStubTblRes = DB::select($updateAnesStubTblQry); // or die($updateAnesStubTblQry . imw_error());
                                    //END CODE TO CHECK ANESTHESIOLOGIST SIGNATURE AND SET VALUE IN STUB TABLE
                                    //CODE TO CHECK NURSE ALL SIGNATURE AND SET VALUE IN STUB TABLE
                                    $chartSignedByNurse = $this->chkNurseSignNew($tableFormStatusRow->$signUserconfirmation_id);
                                    $updateNurseStubTblQry = "UPDATE stub_tbl SET chartSignedByNurse='" . $chartSignedByNurse . "' WHERE patient_confirmation_id='" . $tableFormStatusRow->$signUserconfirmation_id . "'";
                                    $updateNurseStubTblRes = DB::select($updateNurseStubTblQry); // or die($updateNurseStubTblQry . imw_error());
                                    //END CODE TO CHECK NURSE SIGNATURE AND SET VALUE IN STUB TABLE	
                                }
                            }
                        }

                        //END SET FORM STATUS(TO SHOW RED OR GREEN FLAG) IN LEFT FORM SLIDER	
                    }
                }
                $status = 1;
                $message = "Record(s) saved successfully !";
                $requiredStatus = 0;
            }
        }

        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => $requiredStatus,
                    'data' => [],
        ]); // NOT_FOUND (404) being the HTTP response code 
    }

    public function surgeonPrePostOP(Request $request) {
        $userToken = $request->input('user_token');
        $iasc_facility_id = $request->input('iasc_facility_id');
        $selected_date = $request->input('dos');
        if ($iasc_facility_id) {
            $fac_con = " and st.iasc_facility_id='$iasc_facility_id'";
        }
        $andstubIdQry = "";
        $data = [];
        $status = 0;
        $requiredStatus = 1;
        $message = " unauthorized ";
        $res = '';
        $patient_confirmation_id_arr = [];
        $pendingStubRowArr = [];
        $stubRowArr = [];
        $user_id = $this->checkToken($userToken);
        if ($user_id) {
            $ViewUserNameQry = "select fname,mname,lname from `users` where  usersId = '" . $user_id . "'";
            $ViewUserNameRes = DB::select($ViewUserNameQry);
            $loggedInUserFirstName = $ViewUserNameRes[0]->fname;
            $loggedInUserMiddleName = $ViewUserNameRes[0]->mname;
            $loggedInUserLastName = $ViewUserNameRes[0]->lname;
            $laserSignCatIdArr = array();
            $getConfirmationDetailQry = "SELECT * FROM `patientconfirmation` WHERE  dos='" . $selected_date . "'";
            $getConfirmationDetailRes = DB::select($getConfirmationDetailQry); // or die($getConfirmationDetailQry . imw_error());
            $primary_procedure_is_inj_misc_arr = $patient_primary_procedure_id_arr = array();
            if ($getConfirmationDetailRes) {
                foreach ($getConfirmationDetailRes as $getConfirmationDetailRow) {
                    $ptConfId = $getConfirmationDetailRow->patientConfirmationId;
                    $primary_procedure_is_inj_misc_arr[$ptConfId] = $getConfirmationDetailRow->prim_proc_is_misc;
                    $patient_primary_procedure_id = $getConfirmationDetailRow->patient_primary_procedure_id;
                    $patient_primary_procedure_id_arr[$ptConfId] = $patient_primary_procedure_id;
                    $getLaserCatIdDetailQry = "SELECT * FROM `procedures` WHERE procedureId='" . $patient_primary_procedure_id . "'  AND procedureId!='0'";
                    $getLaserCatIdDetailRes = DB::select($getLaserCatIdDetailQry); // or die($getLaserCatIdDetailQry . imw_error());
                    if ($getLaserCatIdDetailRes) {
                        $laserSignCatId = $getLaserCatIdDetailRes[0]->catId;
                        $laserSignCatIdArr[$ptConfId] = $laserSignCatId;
                    }
                }
            }
            //END CODE TO SET PATIENT APPOINTMENT STATUS IN imwemr
            $stubQry = "SELECT 
                        TRIM(CONCAT(st.patient_last_name,', ',st.patient_first_name,' ',st.patient_middle_name)) as patient_name , 
                        DATE_FORMAT(st.patient_dob,'%m/%d/%Y') as dob, st.patient_primary_procedure, st.patient_confirmation_id,st.stub_id ,  
                        pr.form_status as pre_op_phy_form_status, lp.form_status as laser_procedure_form_status,pr.preOpPhysicianOrdersId,
                        po.form_status as post_op_phy_form_status,
                        pc.patient_primary_procedure_id, pc.patient_secondary_procedure_id, pc.patient_tertiary_procedure_id,
                        pr.signSurgeon1Id AS preOpPhySignSurgeonId , lp.signSurgeon1Id AS laserProcSignSurgeonId, po.signSurgeon1Id AS postOpPhySignSurgeonId ,
                        pcr.catId as procedureCatId , pc.surgeonId, 
                        pr.version_num as preOpPhysicianVersionNum,
                        pr.prefilMedicationStatus as pre_op_prefill_status, lp.prefilMedicationStatus as laser_prefill_status
                        FROM stub_tbl st
                        LEFT JOIN patientconfirmation pc ON (pc.patientConfirmationId = st.patient_confirmation_id)
                        LEFT JOIN preopphysicianorders pr ON (pr.patient_confirmation_id = st.patient_confirmation_id AND pr.patient_confirmation_id !='0')
                        LEFT JOIN postopphysicianorders po ON (po.patient_confirmation_id = st.patient_confirmation_id AND po.patient_confirmation_id !='0')
                        LEFT JOIN laser_procedure_patient_table lp ON (lp.confirmation_id = st.patient_confirmation_id AND pr.patient_confirmation_id !='0')
                        LEFT JOIN procedures pcr ON (pc.patient_primary_procedure_id = pcr.procedureId )
                    WHERE st.dos = '" . $selected_date . "' 
							AND st.surgeon_fname = '" . addslashes($loggedInUserFirstName) . "'
							AND st.surgeon_lname = '" . addslashes($loggedInUserLastName) . "'
							AND st.patient_status != 'Canceled'
							AND pc.prim_proc_is_misc = ''
							$fac_con
					GROUP BY st.stub_id
					ORDER BY st.surgery_time, st.surgeon_fname";

            //echo $stubQry;
            $res = DB::select($stubQry);
            $medicationNameArr = array();
            $strengthArr = array();
            $directionArr = array();
            $postMedicationNameArr = [];
            $medicationPostOpNameArr = array();
            $patientMedicationConfId = $defaultMedicationConfId = $patientMedicationPostOpConfId = $defaultMedicationPostOpConfId = array();
            $patientMedicationConfId_implode = $defaultMedicationConfId_implode = $patientMedicationPostOpConfId_implode = $defaultMedicationPostOpConfId_implode = 0;
            if ($res) {
                foreach ($res as $stubRow) {
                    $preOpPhySignSurgeonId = $stubRow->preOpPhySignSurgeonId;
                    $laserProcSignSurgeonId = $stubRow->laserProcSignSurgeonId;
                    $preOpPhysicianVersionNum = $stubRow->preOpPhysicianVersionNum;
                    $postOpPhySignSurgeonId = $stubRow->postOpPhySignSurgeonId;
                    $procedureCatId = $stubRow->procedureCatId;

                    $stubRow->dos = date("m/d/Y", strtotime($selected_date));

                    $surgeon_arr[$stubRow->patient_confirmation_id] = $stubRow->surgeonId;
                    $primary_procedure_id_arr[$stubRow->patient_confirmation_id] = $stubRow->patient_primary_procedure_id;
                    $secondary_procedure_id[$stubRow->patient_confirmation_id] = $stubRow->patient_secondary_procedure_id;
                    $tertiary_procedure_id[$stubRow->patient_confirmation_id] = $stubRow->patient_tertiary_procedure_id;
                    $defaultMedTable[$stubRow->patient_confirmation_id] = 'preopphysicianorders'; // $stubRow->MedTable;
                    $patientProcCatId[$stubRow->patient_confirmation_id] = $procedureCatId;
                    $preOpPrefillStatus[$stubRow->patient_confirmation_id] = $stubRow->pre_op_prefill_status;
                    $laserPrefillStatus[$stubRow->patient_confirmation_id] = $stubRow->laser_prefill_status;
                    $postOpFormStatus[$stubRow->patient_confirmation_id] = $stubRow->post_op_phy_form_status;

                    $medicationQry = "SELECT medicationName, strength, direction, patient_confirmation_id FROM patientpreopmedication_tbl WHERE patient_confirmation_id IN(" . $stubRow->patient_confirmation_id . ")";
                    $medicationRes = DB::select($medicationQry);
                    unset($medicationNameArr);
                    unset($strengthArr);
                    unset($directionArr);
                    if ($medicationRes) {
                        foreach ($medicationRes as $medicationRow) {
                            $medicationNameArr[] = $medicationRow->medicationName;
                            $strengthArr[] = $medicationRow->strength;
                            $directionArr[] = $medicationRow->direction;
                        }
                    }
                    $ptOtherPreOpOrderQry = "SELECT * FROM preopphysicianorders WHERE patient_confirmation_id IN(" . $stubRow->patient_confirmation_id . ") ";
                    $ptOtherPreOpOrderRes = DB::select($ptOtherPreOpOrderQry);
                    if ($ptOtherPreOpOrderRes) {
                        foreach ($ptOtherPreOpOrderRes as $ptOtherPreOpOrderRow) {
                            $pConfIdOtherPreOpOrder = $ptOtherPreOpOrderRow->patient_confirmation_id;
                            $otherPreOpOrdersArr[$pConfIdOtherPreOpOrder] = $ptOtherPreOpOrderRow->preOpOrdersOther;
                        }
                    }
                    $defaultMedArr = array();
                    $medications = array();
                    $pConfId = $stubRow->patient_confirmation_id;
                    $defaultMedArr = @$this->signAllDefautlMedications($pConfId, $laserSignCatIdArr[$pConfId], $surgeon_arr[$pConfId], $primary_procedure_id_arr[$pConfId], $secondary_procedure_id[$pConfId], $tertiary_procedure_id[$pConfId], $defaultMedTable[$pConfId]);
                    $medications = $defaultMedArr[0];
                    $otherPreOpOrders = $defaultMedArr[1];
                    if ($otherPreOpOrders) {
                        $otherPreOpOrdersArr[$pConfId] = $otherPreOpOrders;
                    }

                    if (is_array($medications) && count($medications) > 0) {
                        foreach ($medications as $medicationRow) {
                            $medicationNameArr[] = $medicationRow->medicationName;
                            $strengthArr[] = $medicationRow->strength;
                            $directionArr[] = $medicationRow->direction;
                        }
                    }
                    $medicationPostOpCountQry = "SELECT Pc.patientConfirmationId, count(Ppo.confirmation_id) as recordsFoundPostOp FROM patientconfirmation Pc Left Join patient_physician_orders Ppo on (Pc.patientConfirmationId = Ppo.confirmation_id AND Ppo.chartName = 'post_op_physician_order_form')  WHERE Pc.patientConfirmationId IN(" . $stubRow->patient_confirmation_id . ") ";
                    $medicationPostOpCountRes = DB::select($medicationPostOpCountQry);
                    if ($medicationPostOpCountRes) {
                        foreach ($medicationPostOpCountRes as $medicationPostOpCountRow) {
                            $pConfIdPostOp = $medicationPostOpCountRow->patientConfirmationId;
                            $recordsFoundPostOp = $medicationPostOpCountRow->recordsFoundPostOp;
                            if ($recordsFoundPostOp > 0 || ($recordsFoundPostOp == 0 && ($postOpFormStatus[$pConfIdPostOp] == 'completed' || $postOpFormStatus[$pConfIdPostOp] == 'not completed' )))
                                array_push($patientMedicationPostOpConfId, $pConfIdPostOp);
                            else
                                array_push($defaultMedicationPostOpConfId, $pConfIdPostOp);
                        }
                    }
                    $patientMedicationPostOpConfId_implode = implode(",", $patientMedicationPostOpConfId);
                    $defaultMedicationPostOpConfId_implode = implode(",", $defaultMedicationPostOpConfId);
                    /* medicationPostOpNameArr */
                    unset($postMedicationNameArr);
                    // if ($patientMedicationPostOpConfId_implode != 0) {
                    $medicationPostOpQry = "SELECT physician_order_name, confirmation_id FROM patient_physician_orders WHERE confirmation_id IN(" . $stubRow->patient_confirmation_id . ")";
                    $medicationPostOpRes = DB::select($medicationPostOpQry);
                    if ($medicationPostOpRes) {
                        foreach ($medicationPostOpRes as $medicationPostOpRow) {
                            $pConfIdPostOpMed = $medicationPostOpRow->confirmation_id;
                            if ($medicationPostOpRow->physician_order_name <> "") {
                                $postMedicationNameArr[] = $medicationPostOpRow->physician_order_name; //."::".$stubRow->stub_id."::".$patientMedicationPostOpConfId_implode."::".$medicationPostOpQry;
                            }
                        }
                    }
//                    }else{
//                        $postMedicationNameArr=[];
//                    }
                    /* END here medicationPostOpNameArr */
                    if ($defaultMedicationPostOpConfId_implode != 0) {
                        foreach ($defaultMedicationPostOpConfId as $pConfIdPostOp) {
                            $medicationsPostOp = $this->signAllDefaultPostOpMedications($pConfIdPostOp, $laserSignCatIdArr[$pConfIdPostOp], $surgeon_arr[$pConfIdPostOp], $primary_procedure_id_arr[$pConfIdPostOp], $secondary_procedure_id[$pConfIdPostOp], $tertiary_procedure_id[$pConfIdPostOp], $defaultMedTable[$pConfIdPostOp]);
                            if (is_array($medicationsPostOp) && count($medicationsPostOp) > 0) {
                                foreach ($medicationsPostOp as $medicationPostOpName) {
                                    if ($medicationPostOpName <> "") {
                                        $postMedicationNameArr[] = $medicationPostOpName;
                                    }
                                }
                            }
                        }
                    }

                    $stubRow->Medication = isset($medicationNameArr) ? $medicationNameArr : [""];
                    $stubRow->Strength = isset($strengthArr) ? $strengthArr : [""];
                    $stubRow->Direction = isset($directionArr) ? $directionArr : [""];
                    $stubRow->postMedicationNameAr = isset($postMedicationNameArr) ? $postMedicationNameArr : [""];
                    $preOpCheckMark = $postOpCheckMark = 'yes';
                    if ($preOpPhySignSurgeonId == 0 && $laserProcSignSurgeonId == 0) {
                        $preOpCheckMark = 'no';
                    }
                    if ($postOpPhySignSurgeonId == 0 && $procedureCatId != '2') {
                        $postOpCheckMark = 'no';
                    }
                    if (($preOpPhySignSurgeonId == 0 && $laserProcSignSurgeonId == 0) || ($postOpPhySignSurgeonId == 0 && $procedureCatId != '2')) {
                        $stubRow->preOpCheckMark = $preOpCheckMark;
                        $stubRow->postOpCheckMark = $postOpCheckMark;
                        $pendingStubRowArr[] = $stubRow;
                    } else {
                        $stubRow->preOpCheckMark = "no";
                        $stubRow->postOpCheckMark = "no";
                        $stubRowArr[] = $stubRow;
                    }
                }
            }
            $requiredStatus = 0;
            $message = " status updated ";
            $status = 1;
        }

        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => $requiredStatus,
                    'data' => [['pendingStub' => $pendingStubRowArr, 'Stub' => $stubRowArr]],
        ]); // NOT_FOUND (404) being the HTTP response code 
    }

    public function surgeonPrePostOPMove(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $chbxPreOpOrder = $request->json()->get('chbxPreOpOrder') ? $request->json()->get('chbxPreOpOrder') : $request->input('chbxPreOpOrder');
        $chbxPostOpOrder = $request->json()->get('chbxPostOpOrder') ? $request->json()->get('chbxPostOpOrder') : $request->input('chbxPostOpOrder');
        $selected_date = $request->json()->get('dos') ? $request->json()->get('dos') : $request->input('dos');
        $postopMedArray = $request->json()->get('postop_medication') ? $request->json()->get('postop_medication') : $request->input('postop_medication');

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
                $loggedInUserId = $userId;

                //GET USER NAME
                $ViewUserNameQry = "select * from `users` where  usersId = '" . $loggedInUserId . "'";
                $ViewUserNameRow = DB::select($ViewUserNameQry); // or die($ViewUserNameQry . imw_error());

                $loggedInUserFirstName = $ViewUserNameRow[0]->fname;
                $loggedInUserMiddleName = $ViewUserNameRow[0]->mname;
                $loggedInUserLastName = $ViewUserNameRow[0]->lname;

                $signOnFileStatus = 'Yes';
                $signDateTime = date("Y-m-d H:i:s");

                $loggedInUserName = $ViewUserNameRow[0]->lname . ", " . $ViewUserNameRow[0]->fname . " " . $ViewUserNameRow[0]->mname;
                //END GET USER  NAME
                $laserSignCatIdArr = array();
                $getConfirmationDetailQry = "SELECT patientConfirmationId,prim_proc_is_misc,patient_primary_procedure_id FROM `patientconfirmation` WHERE  dos='" . $selected_date . "'";
                $getConfirmationDetailRes = DB::select($getConfirmationDetailQry);
                $primary_procedure_is_inj_misc_arr = $patient_primary_procedure_id_arr = array();
                if ($getConfirmationDetailRes) {
                    foreach ($getConfirmationDetailRes as $getConfirmationDetailRow) {
                        $ptConfId = $getConfirmationDetailRow->patientConfirmationId;
                        $primary_procedure_is_inj_misc_arr[$ptConfId] = $getConfirmationDetailRow->prim_proc_is_misc;
                        $patient_primary_procedure_id = $getConfirmationDetailRow->patient_primary_procedure_id;
                        $patient_primary_procedure_id_arr[$ptConfId] = $patient_primary_procedure_id;
                        $getLaserCatIdDetailQry = "SELECT catId FROM `procedures` WHERE procedureId='" . $patient_primary_procedure_id . "'  AND procedureId!='0'";
                        $getLaserCatIdDetailRes = DB::select($getLaserCatIdDetailQry); // or die($getLaserCatIdDetailQry . imw_error());
                        if ($getLaserCatIdDetailRes) {
                            $laserSignCatId = $getLaserCatIdDetailRes[0]->catId;
                            $laserSignCatIdArr[$ptConfId] = $laserSignCatId;
                        }
                    }
                }

                //CODE TO MAKE SIGNATURES OF SURGEON IN PRE-OP ORDER CHART NOTES OF PATIENT(IN OPENED DOS)
                $laserConfIdArr = $phyConfIdArr = array();
                $patientConfirmationIdArr = @explode(",", $pConfirmId);
                //print_r($patientConfirmationIdArr); exit;
                foreach ($patientConfirmationIdArr as $key => $patientConfirmationIdVal) {
                    $catId = isset($laserSignCatIdArr[$patientConfirmationIdVal]) ? $laserSignCatIdArr[$patientConfirmationIdVal] : 0;
                    if ($catId == 2) {
                        $laserConfIdArr[] = $patientConfirmationIdVal;
                    } else {
                        $phyConfIdArr[] = $patientConfirmationIdVal;
                    }
                }
                $laserConfId = implode(",", $laserConfIdArr);
                $phyConfId = implode(",", $phyConfIdArr);

                if (!trim($laserConfId)) {
                    $laserConfId = 0;
                }
                if (!trim($phyConfId)) {
                    $phyConfId = 0;
                }
                $SaveSignArr = array('preopphysicianorders', 'postopphysicianorders', 'laser_procedure_patient_table');
                $dis_sumry_filled = ''; //Initialize Discharge Summary filled Satus
                $affectedRows = 0;
                foreach ($SaveSignArr as $SaveSignArrTableName) {

                    if ($chbxPreOpOrder != "yes" && $SaveSignArrTableName == "preopphysicianorders") {
                        continue; //DO NO SIGN PRE-OP ORDER IF ITS CHECKBOX IS UNCHECKED
                    }
                    if ($chbxPostOpOrder != "yes" && $SaveSignArrTableName == "postopphysicianorders") {
                        continue; //DO NO SIGN POST-OP ORDER IF ITS CHECKBOX IS UNCHECKED
                    }
                    $signUserId = 'signSurgeon1Id';
                    $signUserFirstName = 'signSurgeon1FirstName';
                    $signUserMiddleName = 'signSurgeon1MiddleName';
                    $signUserLastName = 'signSurgeon1LastName';
                    $signUserStatus = 'signSurgeon1Status';
                    $signUserDateTime = 'signSurgeon1DateTime';
                    $signUserconfirmation_id = 'confirmation_id';

                    if ($SaveSignArrTableName == "preopphysicianorders" || $SaveSignArrTableName == "postopphysicianorders") {
                        $signUserconfirmation_id = 'patient_confirmation_id';
                    }

                    $inFields = $phyConfId;
                    if ($SaveSignArrTableName == 'laser_procedure_patient_table') {
                        $inFields = $laserConfId;
                    }
                    $andQry = " AND $signUserconfirmation_id IN(" . $inFields . ")";

                    $surgeonLaserVerifyFields = '';
                    if ($SaveSignArrTableName == 'laser_procedure_patient_table') {
                        $surgeonLaserVerifyFields = ", verified_surgeon_Id = '" . $loggedInUserId . "',
											 verified_surgeon_Name = '" . $loggedInUserName . "',
											 chk_laser_patient_examined = 'Yes',
											 chk_laser_patient_evaluated = 'Yes'";
                    }
                    $surgeonPreOpPhyFields = '';
                    if ($SaveSignArrTableName == 'preopphysicianorders') {
                        $surgeonPreOpPhyFields = ", evaluatedPatient = '1'";
                    }
                    $SaveSignQry = "update $SaveSignArrTableName set 
                                    $signUserId = '$loggedInUserId',
                                    $signUserFirstName = '$loggedInUserFirstName', 
                                    $signUserMiddleName = '$loggedInUserMiddleName',
                                    $signUserLastName = '$loggedInUserLastName', 
                                    $signUserStatus = '$signOnFileStatus',
                                    $signUserDateTime = '$signDateTime'
                                    $surgeonLaserVerifyFields
                                    $surgeonPreOpPhyFields
                                    WHERE ($signUserId='0' OR $signUserId='')  " . $andQry;
                    //AND (form_status = 'not completed' OR form_status = 'completed')
                    $requiredStatus[] = $SaveSignQry;
                    $SaveSignRes = DB::select($SaveSignQry); // or die('Error occured at line no.' . (__LINE__) . $SaveSignQry . imw_error());
                    //echo 'Rows Affected : '.$affectedRows .'<br>'	;
                    if ($inFields <> 0) {
                        //SET FORM STATUS TO SHOW RED OR GREEN FLAG) IN LEFT FORM SLIDER	
                        $tableFormStatusQry = "SELECT * FROM $SaveSignArrTableName WHERE " . str_replace('AND', '', $andQry);
                        $requiredStatus[] = $tableFormStatusQry;
                        $tableFormStatusRes = DB::select($tableFormStatusQry); // or die($tableFormStatusQry . imw_error());

                        if ($tableFormStatusRes) {
                            foreach ($tableFormStatusRes as $tableFormStatusRow) {
                                //$confirmationId = '';
                                $confirmationId = $tableFormStatusRow->$signUserconfirmation_id;
                                $primary_procedure_is_inj_misc = isset($primary_procedure_is_inj_misc_arr[$confirmationId]) ? $primary_procedure_is_inj_misc_arr : 0;
                                $pt_primary_procedure_id = isset($patient_primary_procedure_id_arr[$confirmationId]) ? $patient_primary_procedure_id_arr[$confirmationId] : 0;
                                if ($primary_procedure_is_inj_misc == '' && $pt_primary_procedure_id) {
                                    $primary_procedure_is_inj_misc = $this->verifyProcIsInjMisc($pt_primary_procedure_id);
                                }
                                $preOpPhysicianOrdersres = DB::selectone("select preOpPhysicianOrdersId,form_status from preopphysicianorders where patient_confirmation_id='" . $confirmationId . "'");
                                $preOpPhysicianOrdersId = $preOpPhysicianOrdersres->preOpPhysicianOrdersId; //$_POST['preOpPhysicianOrderId_' . $confirmationId];

                                $laserformstatus = DB::selectone("select form_status from laser_procedure_patient_table where confirmation_id='" . $confirmationId . "'");
                                $currentFormStatus = $preOpPhysicianOrdersres->form_status ? $preOpPhysicianOrdersres->form_status : $laserformstatus->form_status;
                                $medicationArray = DB::select("SELECT medicationName, strength, direction, patient_confirmation_id FROM patientpreopmedication_tbl WHERE patient_confirmation_id='" . $confirmationId . "'");
                                $otherPreopOrderres = DB::selectone("SELECT preOpOrdersOther FROM preopphysicianorders WHERE patient_confirmation_id='" . $confirmationId . "'");
                                $otherPreOpOrder = addslashes($otherPreopOrderres->preOpOrdersOther);

                                //START PRE-FILL OTHER PRE-OP ORDERS
                                if ($SaveSignArrTableName == 'preopphysicianorders') {
                                    $requiredStatus[] = $updtOtherPreOpOrderQry = "UPDATE $SaveSignArrTableName SET preOpOrdersOther = IF(preOpOrdersOther!='', preOpOrdersOther,'" . $otherPreOpOrder . "') WHERE patient_confirmation_id = '" . $confirmationId . "'";
                                    $updtOtherPreOpOrderRes = DB::select($updtOtherPreOpOrderQry); // or die(imw_error());
                                }
                                //END PRE-FILL OTHER PRE-OP ORDERS

                                if ($SaveSignArrTableName == 'preopphysicianorders' || $SaveSignArrTableName == 'laser_procedure_patient_table') {
                                    if (is_array($medicationArray) && count($medicationArray) > 0) {
                                        foreach ($medicationArray as $medicationDetails) {
                                            $strength = $medicationDetails->strength;
                                            $direction = $medicationDetails->direction;
                                            $medicationName = $medicationDetails->medicationName;
                                            $source = isset($laserSignCatIdArr[$medicationDetails->patient_confirmation_id]) ? $laserSignCatIdArr[$medicationDetails->patient_confirmation_id] == '2' ? 1 : 0 : 0;
                                            $chkPatientPreOpMediQry = "Select * From patientpreopmedication_tbl Where
                                                            patient_confirmation_id = '$confirmationId'  And
                                                            medicationName = '$medicationName'  And
                                                            strength = '$strength' And
                                                            direction = '$direction' And
                                                            sourcePage = '$source'
                                                    ";
                                            $requiredStatus[] = $chkPatientPreOpMediQry;
                                            $chkPatientPreOpMediSql = DB::select($chkPatientPreOpMediQry);

                                            if (!$chkPatientPreOpMediSql) {
                                                $insPatientPreOpMediQry = "Insert Into patientpreopmedication_tbl Set
                                                                                preOpPhyOrderId = '$preOpPhysicianOrdersId',
                                                                                patient_confirmation_id = '$confirmationId',
                                                                                medicationName = '$medicationName',
                                                                                strength = '$strength',
                                                                                direction = '$direction',
                                                                                sourcePage = '$source'
                                                                        ";
                                                $requiredStatus[] = $insPatientPreOpMediQry;
                                                $insPatientPreOpMediRes = DB::select($insPatientPreOpMediQry); // or die(imw_error());
                                            }
                                        }
                                    }

                                    $prefilMedicationStatusSource = $saveFromChart = 1;
                                    if ($currentFormStatus == 'not completed' || $currentFormStatus == 'completed') {
                                        $prefilMedicationStatusSource = $saveFromChart = 0;
                                    }
                                    $updatePrefilMedicationStatusQry = " Update $SaveSignArrTableName Set
                                                                                prefilMedicationStatus = 'true', 
                                                                                prefilMedicationStatusSource = $prefilMedicationStatusSource,
                                                                                saveFromChart = $saveFromChart
                                                                                WHERE $signUserconfirmation_id = '$confirmationId'";
                                    $requiredStatus[] = $updatePrefilMedicationStatusQry;
                                    $updatePrefilMedicationStatusRes = DB::select($updatePrefilMedicationStatusQry); // or die(imw_error());
                                }

                                // Prefilling of Post Op Medications 
                                if ($SaveSignArrTableName == 'postopphysicianorders') {
                                    if (is_array($postopMedArray)) {
                                        foreach ($postopMedArray as $key => $medicationName) {
                                            if (isset($medicationName[$confirmationId]) && $medicationName[$confirmationId] <> "") {
                                                $dataArray = array();
                                                $dataArray['confirmation_id'] = $confirmationId;
                                                $dataArray['chartName'] = 'post_op_physician_order_form';
                                                $dataArray['physician_order_name'] = $medicationName[$confirmationId];
                                                $chkRecords = $this->getMultiChkArrayRecords('patient_physician_orders', $dataArray);
                                                if (!$chkRecords) {
                                                    $dataArray['physician_order_location'] = 'sign_all_pre_op_order';
                                                    $dataArray['physician_order_date_time'] = date("Y-m-d H:i:s");
                                                    $dataArray['physician_order_type'] = 'medication';
                                                    DB::table('patient_physician_orders')->insert($dataArray); //, 'patient_physician_orders');
                                                }
                                            }
                                        }
                                    }
                                }

                                //update form status 
                                $tableFormStatus = 'completed';
                                $laserSignCatId = isset($laserSignCatIdArr[$confirmationId]) ? $laserSignCatIdArr[$confirmationId] : 0;
                                $leftNaviFieldName = "";
                                if ($SaveSignArrTableName == "preopphysicianorders" && $laserSignCatId != '2') {
                                    $leftNaviFieldName = "pre_op_physician_order_form";

                                    $chkPreOpPhysicianFormStatus = $tableFormStatusRow->form_status;
                                    $chkPreOpPhysicianVersionNum = $tableFormStatusRow->version_num;
                                    $chkPreOpPhysicianVersionDateTime = $tableFormStatusRow->version_date_time;
                                    $version_num = $chkPreOpPhysicianVersionNum;

                                    // Check & update Form's Version if form not saved once.
                                    if (!$chkPreOpPhysicianVersionNum) {
                                        $version_date_time = $chkPreOpPhysicianVersionDateTime;
                                        if ($version_date_time == '' || $version_date_time == '0000-00-00 00:00:00') {
                                            $version_date_time = date('Y-m-d H:i:s');
                                        }

                                        if ($chkPreOpPhysicianFormStatus == 'completed' || $chkPreOpPhysicianFormStatus == 'not completed') {
                                            $version_num = 1;
                                        } else {
                                            $version_num = 3;
                                        }
                                        $updtQry = "Update " . $SaveSignArrTableName . " Set version_num = '" . $version_num . "', version_date_time = '" . $version_date_time . "' Where $signUserconfirmation_id ='" . $confirmationId . "' ";
                                        $requiredStatus[] = $updtQry;
                                        DB::select($updtQry);
                                    }
                                    //End Check & update Form's Version if form not saved once.


                                    $signNurseIdPreOpPhy = $tableFormStatusRow->signNurseId;
                                    $signNurse1IdPreOpPhy = $tableFormStatusRow->signNurse1Id;
                                    $signSurgeon1IdPreOpPhy = $tableFormStatusRow->signSurgeon1Id;
                                    $notedByNurse = $tableFormStatusRow->notedByNurse;

                                    if ($signSurgeon1IdPreOpPhy == "0" || $signNurse1IdPreOpPhy == "0" || $notedByNurse == 0) {
                                        $tableFormStatus = 'not completed';
                                    }

                                    // Start Validate chart if form is on version no. 2 or greater and surgeon signature not on chart
                                    if ($version_num == 1 && $tableFormStatus == 'completed' && $signNurseIdPreOpPhy == "0") {
                                        $tableFormStatus = 'not completed';
                                    }
                                    // End Validate chart if form is on version no. 2 or greater and surgeon signature not on chart
                                } else if ($SaveSignArrTableName == "postopphysicianorders" && $laserSignCatId != '2') {
                                    $leftNaviFieldName = "post_op_physician_order_form";
                                    $chkPostOpPhysicianFormStatus = $tableFormStatusRow->form_status;
                                    $chkPostOpPhysicianVersionNum = $tableFormStatusRow->version_num;
                                    $chkPostOpPhysicianVersionDateTime = $tableFormStatusRow->version_date_time;
                                    $version_num = $chkPostOpPhysicianVersionNum;

                                    // Check & update Form's Version if form not saved once.
                                    if (!$chkPostOpPhysicianVersionNum) {
                                        $version_date_time = $chkPostOpPhysicianVersionDateTime;
                                        if ($version_date_time == '' || $version_date_time == '0000-00-00 00:00:00') {
                                            $version_date_time = date('Y-m-d H:i:s');
                                        }

                                        if ($chkPostOpPhysicianFormStatus == 'completed' || $chkPostOpPhysicianFormStatus == 'not completed') {
                                            $version_num = 1;
                                        } else {
                                            $version_num = 3;
                                        }
                                        $updtQry = "Update " . $SaveSignArrTableName . " Set version_num = '" . $version_num . "', version_date_time = '" . $version_date_time . "' Where $signUserconfirmation_id='" . $confirmationId . "' ";
                                        $requiredStatus[] = $updtQry;
                                        DB::select($updtQry);
                                    }
                                    //End Check & update Form's Version if form not saved once.

                                    $postOpDropExist = "";
                                    $chkPostOpDropQry = "SELECT recordId,physician_order_time FROM patient_physician_orders 
																 WHERE confirmation_id = '" . $confirmationId . "' 
																 AND chartName = 'post_op_physician_order_form'";
                                    $chkPostOpDropRes = DB::select($chkPostOpDropQry);
                                    if ($chkPostOpDropRes) {
                                        $postOpDropExist = "Yes";
                                    }
                                    $patientAssessed = $tableFormStatusRow->patientAssessed;
                                    $vitalSignStable = $tableFormStatusRow->vitalSignStable;
                                    $postOpEvalDone = $tableFormStatusRow->postOpEvalDone;
                                    $postOpInstructionMethodWritten = $tableFormStatusRow->postOpInstructionMethodWritten;
                                    $postOpInstructionMethodVerbal = $tableFormStatusRow->postOpInstructionMethodVerbal;
                                    $patientAccompaniedSafely = $tableFormStatusRow->patientAccompaniedSafely;
                                    $signNurseIdPostOpPhy = $tableFormStatusRow->signNurseId;
                                    $signNurse1IdPostOpPhy = $tableFormStatusRow->signNurse1Id;
                                    $signSurgeon1IdPostOpPhy = $tableFormStatusRow->signSurgeon1Id;
                                    $postOpNotedByNurse = $tableFormStatusRow->notedByNurse;
                                    if (($patientAssessed != 'Yes') || ($vitalSignStable != 'Yes') || ($postOpEvalDone != 'Yes') || ($postOpInstructionMethodWritten != 'Yes') || ($postOpInstructionMethodVerbal != 'Yes') || ($patientAccompaniedSafely != 'Yes') || ($signNurseIdPostOpPhy == "0") || ($signSurgeon1IdPostOpPhy == "0") || ($signNurse1IdPostOpPhy == "0" && $version_num > 2) || ($postOpNotedByNurse == "0" && $version_num > 2) || ($postOpDropExist != 'Yes')) {
                                        $tableFormStatus = 'not completed';
                                    }
                                } else if ($SaveSignArrTableName == "laser_procedure_patient_table" && $laserSignCatId == '2') {

                                    $leftNaviFieldName = "laser_procedure_form";

                                    $chk_laser_chief_complaint = $tableFormStatusRow->chk_laser_chief_complaint;
                                    $laser_chief_complaint = $tableFormStatusRow->laser_chief_complaint;

                                    $chk_laser_past_med_hx = $tableFormStatusRow->chk_laser_past_med_hx;
                                    $laser_past_med_hx = $tableFormStatusRow->laser_past_med_hx;

                                    $chk_laser_present_illness_hx = $tableFormStatusRow->chk_laser_present_illness_hx;
                                    $laser_present_illness_hx = $tableFormStatusRow->laser_present_illness_hx;

                                    $chk_laser_medication = $tableFormStatusRow->chk_laser_medication;
                                    $laser_medication = $tableFormStatusRow->laser_medication;

                                    $allergies_status_reviewed = $tableFormStatusRow->allergies_status_reviewed;

                                    $verified_nurse_name = $tableFormStatusRow->verified_nurse_name;
                                    $verified_surgeon_Name = $tableFormStatusRow->verified_surgeon_Name;

                                    $best_correction_vision_R = $tableFormStatusRow->best_correction_vision_R;
                                    $best_correction_vision_L = $tableFormStatusRow->best_correction_vision_L;
                                    $laser_sle = $tableFormStatusRow->laser_sle;
                                    $laser_mental_state = $tableFormStatusRow->laser_mental_state;
                                    $pre_laser_IOP_R = $tableFormStatusRow->pre_laser_IOP_R;
                                    $pre_laser_IOP_L = $tableFormStatusRow->pre_laser_IOP_L;
                                    $pre_iop_na = $tableFormStatusRow->pre_iop_na;
                                    $laser_fundus_exam = $tableFormStatusRow->laser_fundus_exam;
                                    $laser_comments = $tableFormStatusRow->laser_comments;
                                    $laser_other = $tableFormStatusRow->laser_other;


                                    $chk_laser_pre_op_diagnosis = $tableFormStatusRow->chk_laser_pre_op_diagnosis;
                                    $pre_op_diagnosis = $tableFormStatusRow->pre_op_diagnosis;
                                    $laser_other_pre_medication = $tableFormStatusRow->laser_other_pre_medication;

                                    $prelaserVitalSignBP = $tableFormStatusRow->prelaserVitalSignBP;
                                    $prelaserVitalSignP = $tableFormStatusRow->prelaserVitalSignP;
                                    $prelaserVitalSignR = $tableFormStatusRow->prelaserVitalSignR;
                                    $laser_spot_duration = $tableFormStatusRow->laser_spot_duration;
                                    $laser_spot_size = $tableFormStatusRow->laser_spot_size;
                                    $laser_power = $tableFormStatusRow->laser_power;
                                    $laser_shots = $tableFormStatusRow->laser_shots;
                                    $laser_total_energy = $tableFormStatusRow->laser_total_energy;
                                    $laser_degree_of_opening = $tableFormStatusRow->laser_degree_of_opening;
                                    $laser_exposure = $tableFormStatusRow->laser_exposure;
                                    $laser_count = $tableFormStatusRow->laser_count;
                                    $postlaserVitalSignBP = $tableFormStatusRow->postlaserVitalSignBP;
                                    $postlaserVitalSignP = $tableFormStatusRow->postlaserVitalSignP;
                                    $postlaserVitalSignR = $tableFormStatusRow->postlaserVitalSignR;
                                    $post_op_operative_comment = $tableFormStatusRow->post_op_operative_comment;
                                    $laser_post_progress = $tableFormStatusRow->laser_post_progress;
                                    $laser_post_operative = $tableFormStatusRow->laser_post_operative;
                                    $iop_pressure_l = $tableFormStatusRow->iop_pressure_l; //
                                    $iop_pressure_r = $tableFormStatusRow->iop_pressure_r; //

                                    $iop_na = $tableFormStatusRow->iop_na;

                                    $signNurseIdlaser_procedure = $tableFormStatusRow->signNurseId;
                                    $signSurgeon1Idlaser_procedure = $tableFormStatusRow->signSurgeon1Id;

                                    if (( ($laser_chief_complaint == '') && ($chk_laser_chief_complaint == 'on') &&
                                            ($laser_past_med_hx == '') && ($chk_laser_past_med_hx == 'on') &&
                                            ($laser_present_illness_hx == '') && ($chk_laser_present_illness_hx == 'on') &&
                                            ($laser_medication == '') && ($chk_laser_medication == 'on')
                                            ) ||
                                            ($verified_nurse_name == '' || $verified_surgeon_Name == '' ) ||
                                            ( ($pre_op_diagnosis == '') && ($chk_laser_pre_op_diagnosis == 'on')) ||
                                            ( ($prelaserVitalSignBP == '') && ($prelaserVitalSignP == '' ) &&
                                            ($prelaserVitalSignR == '') && ($laser_spot_duration == '') &&
                                            ($laser_spot_size == '') && ($laser_power == '' ) &&
                                            ($laser_shots == '') && ($laser_total_energy == '') &&
                                            ($laser_degree_of_opening == '') && ($laser_exposure == '' ) &&
                                            ($laser_count == '') && ($postlaserVitalSignBP == '') &&
                                            ($postlaserVitalSignP == '') && ($postlaserVitalSignR == '') &&
                                            ($laser_post_progress == '')
                                            ) ||
                                            (($pre_laser_IOP_R == '') && ($pre_laser_IOP_L == '' ) && ($pre_iop_na == '' )) ||
                                            (($iop_pressure_l == '') && ($iop_pressure_r == '') && ($iop_na == '')) ||
                                            ($signNurseIdlaser_procedure == "0") || ($signSurgeon1Idlaser_procedure == "0")
                                    ) {
                                        $tableFormStatus = 'not completed';
                                    }
                                }

                                if ($SaveSignArrTableName == "laser_procedure_patient_table" && $laserSignCatId != '2') {
                                    //DO NOT UPDATE FORM STATUS FOR Laser Chartnote if procedure is not laser
                                } else if ($laserSignCatId == '2' && ($SaveSignArrTableName == "preopphysicianorders" || $SaveSignArrTableName == "postopphysicianorders" )) {
                                    //DO NOT UPDATE FORM STATUS FOR preopphysicianorders/postopphysicianorders Chartnote if procedure is laser
                                } else if ($laserSignCatId <> '2' && $primary_procedure_is_inj_misc && ($SaveSignArrTableName == "preopphysicianorders" || $SaveSignArrTableName == "postopphysicianorders")) {
                                    //DO NOT UPDATE FORM STATUS FOR preopphysicianorders AND postopphysicianorders Chartnote if procedure is injection
                                } else {
                                    $updateTableFormStatusQry = "update $SaveSignArrTableName SET form_status='$tableFormStatus' WHERE $signUserconfirmation_id='" . $tableFormStatusRow->$signUserconfirmation_id . "' ";
                                    $requiredStatus[] = $updateTableFormStatusQry;
                                    DB::select($updateTableFormStatusQry); // or die('Error occured at line no. ' . (__LINE__) . ': ' . imw_error());
                                    //START SHIFTING LEFT LINK TO RIGHT SLIDER 
                                    if (trim($leftNaviFieldName)) {
                                        $requiredStatus[] = $updateLeftNavigationTableQry = "update `left_navigation_forms` set $leftNaviFieldName = 'false' WHERE confirmationId='" . $tableFormStatusRow->$signUserconfirmation_id . "'";
                                        $updateLeftNavigationTableRes = DB::select($updateLeftNavigationTableQry); // or die($updateLeftNavigationTableQry . imw_error());
                                    }
                                    //END SHIFTING LEFT LINK TO RIGHT SLIDER
                                }

                                //CODE TO CHECK SURGEON ALL SIGNATURE AND SET VALUE IN STUB TABLE
                                $chartSignedBySurgeon = $this->chkSurgeonSignNew($tableFormStatusRow->$signUserconfirmation_id);
                                $updateStubTblQry = "UPDATE stub_tbl SET chartSignedBySurgeon='" . $chartSignedBySurgeon . "' WHERE patient_confirmation_id='" . $tableFormStatusRow->$signUserconfirmation_id . "'";
                                $requiredStatus[] = $updateStubTblQry;
                                $updateStubTblRes = DB::select($updateStubTblQry); // or die($updateStubTblQry . imw_error());
                                //END CODE TO CHECK SURGEON ALL SIGNATURE AND SET VALUE IN STUB TABLE
                                //CODE TO CHECK ANESTHESIOLOGIST ALL SIGNATURE AND SET VALUE IN STUB TABLE
                                $chartSignedByAnes = $this->chkAnesSignNew($tableFormStatusRow->$signUserconfirmation_id);
                                $updateAnesStubTblQry = "UPDATE stub_tbl SET chartSignedByAnes='" . $chartSignedByAnes . "' WHERE patient_confirmation_id='" . $tableFormStatusRow->$signUserconfirmation_id . "'";
                                $requiredStatus[] = $updateAnesStubTblQry;
                                $updateAnesStubTblRes = DB::select($updateAnesStubTblQry); // or die($updateAnesStubTblQry . imw_error());
                                //END CODE TO CHECK ANESTHESIOLOGIST SIGNATURE AND SET VALUE IN STUB TABLE
                                //CODE TO CHECK NURSE ALL SIGNATURE AND SET VALUE IN STUB TABLE
                                $chartSignedByNurse = $this->chkNurseSignNew($tableFormStatusRow->$signUserconfirmation_id);
                                $updateNurseStubTblQry = "UPDATE stub_tbl SET chartSignedByNurse='" . $chartSignedByNurse . "' WHERE patient_confirmation_id='" . $tableFormStatusRow->$signUserconfirmation_id . "'";
                                $requiredStatus[] = $updateNurseStubTblQry;
                                $updateNurseStubTblRes = DB::select($updateNurseStubTblQry); // or die($updateNurseStubTblQry . imw_error());
                                //END CODE TO CHECK NURSE SIGNATURE AND SET VALUE IN STUB TABLE	
                            }
                        }
                        //END SET FORM STATUS(TO SHOW RED OR GREEN FLAG) IN LEFT FORM SLIDER	
                    }
                }
                $status = 1;
                $message = "Record(s) saved successfully !";
                $moveStatus = 1;
                //$requiredStatus = 0;
            }
        }

        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => '',
                    'data' => [],
                    'moveStatus' => $moveStatus
        ]); // NOT_FOUND (404) being the HTTP response code 
    }

}
