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

class ChecklistController extends Controller {

    public function patient_checklist_retrieve(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
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
                $qery="select *,date_format(signNurse1DateTime,'%m-%d-%Y %h:%i %p') as signNurse1DateTime,date_format(signNurse2DateTime,'%m-%d-%Y %h:%i %p') as signNurse2DateTime,"
                        . "date_format(signNurse3DateTime,'%m-%d-%Y %h:%i %p') as signNurse3DateTime,date_format(signNurse4DateTime,'%m-%d-%Y %h:%i %p') as signNurse4DateTime from surgical_check_list where confirmation_id='" . $pConfirmId . "'";
                $chkNurseSignDetails = DB::selectone($qery);
                $relivedNurseQry = "select usersId,lname,fname,mname from users where (user_type IN('Nurse','Anesthesiologist') or (user_type='Anesthesiologist' and user_sub_type='CRNA')) and deleteStatus!='Yes' ORDER BY lname";
                $relivedNurseRes = DB::select($relivedNurseQry);
                foreach ($relivedNurseRes as $relivedNurseRow) {
                    $relivedSelectNurseID = $relivedNurseRow->usersId;
                    $relivedNurseName = trim($relivedNurseRow->lname . ", " . $relivedNurseRow->fname . " " . $relivedNurseRow->mname);
                    //$arr_users_nurse[$relivedSelectNurseID] = $relivedNurseName;
                    $arr_users_nurse[] = ['key' => $relivedSelectNurseID, 'value' => $relivedNurseName];
                }
                $fire_risk_score = [['key' => 1, 'value' => '1(Low Risk)'], ['key' => 2, 'value' => '2(Low Risk w/Potential to convert'], ['key' => 3, 'value' => '3(High Risk)']];
                $data = ['relief_nurse_or_anesthesia' => $arr_users_nurse, 'fire_risk_score' => $fire_risk_score, 'surgical_details' => $chkNurseSignDetails];
                $message = " Safety Check List ";
            }
        }
        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' =>date("Y-m-d H:i:s",strtotime("02-11-2019 11:56PM")),
                    'data' => $data,
        ]); // NOT_FOUND (404) being the HTTP response code 
    }

    public function patient_checklist_save(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $relivedNurse1IdList = $request->json()->get('relivedNurse1IdList') ? $request->json()->get('relivedNurse1IdList') : $request->input('relivedNurse1IdList'); //Relief Nurse / Anesthesia
        $chbx_ind = $request->json()->get('chbx_ind_yes') ? $request->json()->get('chbx_ind_yes') : $request->input('chbx_ind_yes'); //Identity
        $chbx_smbp = $request->json()->get('chbx_smbp') ? $request->json()->get('chbx_smbp') : $request->input('chbx_smbp'); //Site Marked and Verified
        $chbx_pro = $request->json()->get('chbx_pro') ? $request->json()->get('chbx_pro') : $request->input('chbx_pro'); //Procedure and procedure site
        $chbx_const = $request->json()->get('chbx_const') ? $request->json()->get('chbx_const') : $request->input('chbx_const'); //Consent(s)
        $chbx_hp = $request->json()->get('chbx_hp') ? $request->json()->get('chbx_hp') : $request->input('chbx_hp'); //History and physical
        $chbx_edi = $request->json()->get('chbx_edi') ? $request->json()->get('chbx_edi') : $request->input('chbx_edi'); //Any special equipment, devices, implants
        $chbx_preth = $request->json()->get('chbx_preth') ? $request->json()->get('chbx_preth') : $request->input('chbx_preth'); //Preanesthesia assessment
        $chbx_jm = $request->json()->get('chbx_jm') ? $request->json()->get('chbx_jm') : $request->input('chbx_jm'); //Normothermia measures
        $relivedNurse2IdList = $request->json()->get('relivedNurse2IdList') ? $request->json()->get('relivedNurse2IdList') : $request->input('relivedNurse2IdList'); //Relief Nurse / Anesthesia
        $chbx_ipp = $request->json()->get('chbx_ipp') ? $request->json()->get('chbx_ipp') : $request->input('chbx_ipp'); //Confirmation of: identify, procedure, procedure site and consent(s)
        $chbx_pa = $request->json()->get('chbx_pa') ? $request->json()->get('chbx_pa') : $request->input('chbx_pa'); //Patient allergies
        $chbx_smpp = $request->json()->get('chbx_smpp') ? $request->json()->get('chbx_smpp') : $request->input('chbx_smpp'); //Site marked by person performing the procedure
        $chbx_dar = $request->json()->get('chbx_dar') ? $request->json()->get('chbx_dar') : $request->input('chbx_dar'); //Difficult airway or aspiration risk? 
        $chbx_asc = $request->json()->get('chbx_asc') ? $request->json()->get('chbx_asc') : $request->input('chbx_asc'); //Anesthesia safety check completed 
        $chbx_adcpc = $request->json()->get('chbx_adcpc') ? $request->json()->get('chbx_adcpc') : $request->input('chbx_adcpc'); //All members of the team have discussed care plan and addressed concerns 
        $chbx_ssx = $request->json()->get('chbx_ssx') ? $request->json()->get('chbx_ssx') : $request->input('chbx_ssx'); //Surgical Site Above Xiphoid (incision above waist)
        $fire_risk_score = $request->json()->get('fire_risk_score') ? $request->json()->get('fire_risk_score') : $request->input('fire_risk_score'); //Fire Risk Score
        $chbx_oos = $request->json()->get('chbx_oos') ? $request->json()->get('chbx_oos') : $request->input('chbx_oos'); //Open Oxygen Source (nasal cannula, oxygen face mask)
        $chbx_ais = $request->json()->get('chbx_ais') ? $request->json()->get('chbx_ais') : $request->input('chbx_ais'); //Available Ignition Source (cautery, laser, fiber optic light source)
        $relivedNurse3IdList = $request->json()->get('relivedNurse3IdList') ? $request->json()->get('relivedNurse3IdList') : $request->input('relivedNurse3IdList'); //Relief Nurse / Anesthesia 
        $chbx_itm = $request->json()->get('chbx_itm') ? $request->json()->get('chbx_itm') : $request->input('chbx_itm'); //Introduction of team member
        $chbx_coip = $request->json()->get('chbx_coip') ? $request->json()->get('chbx_coip') : $request->input('chbx_coip'); //Confirmation of: identify, procedure, procedure site and consent(s)
        $chbx_smv = $request->json()->get('chbx_smv') ? $request->json()->get('chbx_smv') : $request->input('chbx_smv'); //Site is marked and visible
        $chbx_api = $request->json()->get('chbx_api') ? $request->json()->get('chbx_api') : $request->input('chbx_api'); //Antibiotic prophylaxis within one hour before incision 
        $chbx_sinc = $request->json()->get('chbx_sinc') ? $request->json()->get('chbx_sinc') : $request->input('chbx_sinc'); //Sterilization Class 5 indicators have been confirmed
        $relivedNurse4IdList = $request->json()->get('relivedNurse4IdList') ? $request->json()->get('relivedNurse4IdList') : $request->input('relivedNurse4IdList'); //Relief Nurse / Anesthesia
        $chbx_sil = $request->json()->get('chbx_sil') ? $request->json()->get('chbx_sil') : $request->input('chbx_sil'); //Specimens identified and labeled
        $comments = $request->json()->get('comments') ? $request->json()->get('comments') : $request->input('comments'); //comments
        $signNurse1DateTime = $request->json()->get('signNurse1DateTime') ? $request->json()->get('signNurse1DateTime') : $request->input('signNurse1DateTime'); //signNurse1DateTime
        $signNurse2DateTime = $request->json()->get('signNurse2DateTime') ? $request->json()->get('signNurse2DateTime') : $request->input('signNurse2DateTime'); //signNurse2DateTime
        $signNurse3DateTime = $request->json()->get('signNurse3DateTime') ? $request->json()->get('signNurse3DateTime') : $request->input('signNurse3DateTime'); //signNurse3DateTime
        $signNurse4DateTime = $request->json()->get('signNurse4DateTime') ? $request->json()->get('signNurse4DateTime') : $request->input('signNurse4DateTime'); //signNurse4DateTime
        $check_list_id = $request->json()->get('check_list_id') ? $request->json()->get('check_list_id') : $request->input('check_list_id'); //check_list_id
        $rbl_no_of_units = $request->json()->get('rbl_no_of_units') ? $request->json()->get('rbl_no_of_units') : $request->input('rbl_no_of_units'); //Risk of blood loss
        $chbx_epa = $request->json()->get('anyEquipmentProblem') ? $request->json()->get('anyEquipmentProblem') : $request->input('anyEquipmentProblem'); //Any Equipment Problem
        $chbx_nops = $request->json()->get('nameOperativeProcedure') ? $request->json()->get('nameOperativeProcedure') : $request->input('nameOperativeProcedure'); //nameOperativeProcedure
        $chbx_adcn = $request->json()->get('nurseAdditionalConcerns') ? $request->json()->get('nurseAdditionalConcerns') : $request->input('nurseAdditionalConcerns'); //nurseAdditionalConcerns
        $chbx_adicon = $request->json()->get('anesthesiaAdditionalConcerns') ? $request->json()->get('anesthesiaAdditionalConcerns') : $request->input('anesthesiaAdditionalConcerns'); //nurseAdditionalConcerns
        $chbx_abl = $request->json()->get('anticipatedBloodLoss') ? $request->json()->get('anticipatedBloodLoss') : $request->input('anticipatedBloodLoss'); //anticipatedBloodLoss
        $chbx_cns = $request->json()->get('criticalStep') ? $request->json()->get('criticalStep') : $request->input('criticalStep'); //criticalStep
        $chbx_ec = $request->json()->get('anyEquipmentConcern') ? $request->json()->get('anyEquipmentConcern') : $request->input('anyEquipmentConcern'); //anyEquipmentConcern
        $chbx_rip = $request->json()->get('relevantImages') ? $request->json()->get('relevantImages') : $request->input('relevantImages'); //relevantImages
        $chbx_rbl = $request->json()->get('riskBloodLoss') ? $request->json()->get('riskBloodLoss') : $request->input('riskBloodLoss'); //riskBloodLoss
        $chbx_bbm = $request->json()->get('betaBlockerMedication') ? $request->json()->get('betaBlockerMedication') : $request->input('betaBlockerMedication'); //betaBlockerMedication
        $chbx_bldpro = $request->json()->get('bloodProduct') ? $request->json()->get('bloodProduct') : $request->input('bloodProduct'); //bloodProduct
        $chbx_vtpo = $request->json()->get('venousThromboembolism') ? $request->json()->get('venousThromboembolism') : $request->input('venousThromboembolism'); //venousThromboembolism
        $chbx_drts = $request->json()->get('diagnosticAndRadiologic') ? $request->json()->get('diagnosticAndRadiologic') : $request->input('diagnosticAndRadiologic'); //diagnosticAndRadiologic
        $chbx_cd = $request->json()->get('caseDuration') ? $request->json()->get('caseDuration') : $request->input('caseDuration'); //caseDuration
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
                $chkNurseSignDetails = DB::selectone("select * from surgical_check_list where confirmation_id='" . $pConfirmId . "'");
                unset($arrayRecord); //unset the array
                //START CODE TO CHECK NURSE SIGN IN DATABASE
                $chk_versionNum = 0;
                $chk_versionDateTime = '';
                $chk_form_status = '';
                $chk_fire_risk_active_status = '';
                $surgerycenter_fire_risk_analysis = '';
                $chk_checklist_old_new = '';
                if ($chkNurseSignDetails) {
                    $chk_signNurse1Id = $chkNurseSignDetails->signNurse1Id;
                    $chk_signNurse2Id = $chkNurseSignDetails->signNurse2Id;
                    $chk_signNurse3Id = $chkNurseSignDetails->signNurse3Id;
                    $chk_signNurse4Id = $chkNurseSignDetails->signNurse4Id;
                    $chk_versionNum = $chkNurseSignDetails->version_num;
                    $chk_versionDateTime = $chkNurseSignDetails->version_date_time;
                    $chk_checklist_old_new = trim($chkNurseSignDetails->checklist_old_new);
                    $chk_fire_risk_active_status = trim($chkNurseSignDetails->fire_risk_active_status);
                    $chk_form_status = $chkNurseSignDetails->form_status;
                }
                //END CODE TO CHECK NURSE SIGN IN DATABASE 
                $version_num = $chk_versionNum;
                if (!$chk_versionNum) {
                    $version_date_time = $chk_versionDateTime;
                    if ($version_date_time == '' || $version_date_time == '0000-00-00 00:00:00') {
                        $version_date_time = date('Y-m-d H:i:s');
                    }
                    if ($chk_form_status == 'completed' || $chk_form_status == 'not completed') {
                        $version_num = 1;
                    } else {
                        $version_num = 2;
                    }
                    $arrayRecord['version_num'] = $version_num;
                    $arrayRecord['version_date_time'] = $version_date_time;
                }
                if ($chk_fire_risk_active_status <> 'Yes' && $surgerycenter_fire_risk_analysis == 'Y' && $chk_form_status == '') {
                    $chk_fire_risk_active_status = 'Yes';
                    $arrayRecord['fire_risk_active_status'] = $chk_fire_risk_active_status;
                }
                $tablename = "surgical_check_list";
                if (($chbx_ind != '') && ($chbx_pro != '') && ($chbx_smbp != '') && ($chbx_const != '') && ($chbx_hp != '') && ($chbx_preth != '') && ($chbx_edi != '') && ($chbx_jm != '') && ($chbx_itm != '') && ($chbx_coip != '') && ($chbx_smv != '') && ($chbx_api != '') && ($chbx_sinc != '') && ($chbx_sil != '')
                        /*
                          && ($_POST['chbx_drts']!='') 	&& ($_POST['chbx_bldpro']!='')
                          && ($_POST['chbx_bbm']!='')  	&& ($_POST['chbx_vtpo']!='')
                          && ($_POST['chbx_rbl']!='') 	&& ($_POST['chbx_rip']!='')
                          && ($_POST['chbx_ec']!='') 		&& ($_POST['chbx_cns']!='')
                          && ($_POST['chbx_cd']!='') 		&& ($_POST['chbx_abl']!='')
                          && ($_POST['chbx_adicon']!='') 	&& ($_POST['chbx_adcn']!='')
                          && ($_POST['chbx_nops']!='') 	&& ($_POST['chbx_epa']!='')
                         */ && ($chk_signNurse1Id != '0') && ($chk_signNurse3Id != '0') && ($chk_signNurse4Id != '0')
                ) {
                    $formStatus = 'completed';
                } else {
                    $formStatus = 'not completed';
                }
                if ($chk_checklist_old_new == 'old' && $formStatus == 'completed') {
                    if (($chbx_epa != '')) {
                        $formStatus = 'completed';
                    } else {
                        $formStatus = 'not completed';
                    }
                }
                if ($version_num < 2 && $formStatus == 'completed') {
                    if (($chbx_ipp != '') && ($chbx_asc != '') && ($chbx_smpp != '') && ($chbx_pa != '') && ($chbx_dar != '') && ($chbx_adcpc != '') && ($chk_signNurse2Id != '0')) {
                        $formStatus = 'completed';
                    } else {
                        $formStatus = 'not completed';
                    }
                }
                //START CODE TO CHECK FIRE RISK ANALYSIS
                if ($chk_fire_risk_active_status == 'Yes' && $formStatus == 'completed') {
                    if (($chbx_ssx != '') && ($fire_risk_score != '') && ($chbx_oos != '') && ($chbx_ais != '')) {
                        $formStatus = 'completed';
                    } else {
                        $formStatus = 'not completed';
                    }
                }
                //END CODE TO CHECK FIRE RISK ANALYSIS
                if ($chk_fire_risk_active_status == 'Yes') {
                    $arrayRecord['surgical_xiphoid'] = addslashes($chbx_ssx);
                    $arrayRecord['fire_risk_score'] = addslashes($fire_risk_score);
                    $arrayRecord['oxygen_source'] = addslashes($chbx_oos);
                    $arrayRecord['ignition_source'] = addslashes($chbx_ais);
                }
                $arrayRecord['form_status'] = $formStatus;
                $arrayRecord['patient_id'] = $patient_id;
                $arrayRecord['confirmation_id'] = $pConfirmId;
                $arrayRecord['user_id'] = $userId;
                $arrayRecord['save_date_time'] = date('Y-m-d H:i:s');
                $arrayRecord['identity'] = addslashes($chbx_ind);
                $arrayRecord['procedureAndProcedureSite'] = addslashes($chbx_pro);
                $arrayRecord['siteMarkedByPerson'] = addslashes($chbx_smbp);
                $arrayRecord['consent'] = addslashes($chbx_const);
                $arrayRecord['historyAndPhysical'] = addslashes($chbx_hp);
                $arrayRecord['preanesthesiaAssessment'] = addslashes($chbx_preth);
                $arrayRecord['diagnosticAndRadiologic'] = addslashes($chbx_drts);
                $arrayRecord['bloodProduct'] = addslashes($chbx_bldpro);
                $arrayRecord['anySpecialEquipment'] = addslashes($chbx_edi);
                $arrayRecord['betaBlockerMedication'] = addslashes($chbx_bbm);
                $arrayRecord['venousThromboembolism'] = addslashes($chbx_vtpo);
                $arrayRecord['jormothermiaMeasures'] = addslashes($chbx_jm);
                $arrayRecord['confirmIPPSC_signin'] = addslashes($chbx_ipp);
                $arrayRecord['siteMarked'] = addslashes($chbx_smpp);
                $arrayRecord['patientAllergies'] = addslashes($chbx_pa);
                $arrayRecord['difficultAirway'] = addslashes($chbx_dar);
                $arrayRecord['riskBloodLoss'] = addslashes($chbx_rbl);
                $arrayRecord['bloodLossUnits'] = "";

                if (addslashes($chbx_rbl == "Yes")) {
                    $arrayRecord['bloodLossUnits'] = addslashes($rbl_no_of_units);
                }
                $arrayRecord['anesthesiaSafety'] = addslashes($chbx_asc);
                $arrayRecord['allMembersTeam'] = addslashes($chbx_adcpc);
                $arrayRecord['introducationTeamMember'] = addslashes($chbx_itm);
                $arrayRecord['confirmIPPSC'] = addslashes($chbx_coip);
                $arrayRecord['siteMarkedAndVisible'] = addslashes($chbx_smv);
                $arrayRecord['relevantImages'] = addslashes($chbx_rip);
                $arrayRecord['anyEquipmentConcern'] = addslashes($chbx_ec);
                $arrayRecord['criticalStep'] = addslashes($chbx_cns);
                $arrayRecord['caseDuration'] = addslashes($chbx_cd);
                $arrayRecord['anticipatedBloodLoss'] = addslashes($chbx_abl);
                $arrayRecord['antibioticProphylaxis'] = addslashes($chbx_api);
                $arrayRecord['anesthesiaAdditionalConcerns'] = addslashes($chbx_adicon);
                $arrayRecord['sterilizationIndicators'] = addslashes($chbx_sinc);
                $arrayRecord['nurseAdditionalConcerns'] = addslashes($chbx_adcn);
                $arrayRecord['nameOperativeProcedure'] = addslashes($chbx_nops);
                $arrayRecord['specimensIdentified'] = addslashes($chbx_sil);
                $arrayRecord['anyEquipmentProblem'] = addslashes($chbx_epa);
                $arrayRecord['comments'] = addslashes($comments);
                $arrayRecord['reliefNurse1'] = addslashes($relivedNurse1IdList);
                $arrayRecord['reliefNurse2'] = addslashes($relivedNurse2IdList);
                $arrayRecord['reliefNurse3'] = addslashes($relivedNurse3IdList);
                $arrayRecord['reliefNurse4'] = addslashes($relivedNurse4IdList);

                if ($signNurse1DateTime <> "") { //m-d-Y 
                    $arrayRecord['signNurse1DateTime'] =$signNurse1DateTime!=''?date("Y-m-d H:i:s",strtotime($signNurse1DateTime)):'';
                }
                if ($signNurse2DateTime <> "") {
                    $arrayRecord['signNurse2DateTime'] =$signNurse2DateTime!=''?date("Y-m-d H:i:s",strtotime($signNurse2DateTime)):'';
                }
                if ($signNurse3DateTime <> "") {
                    $arrayRecord['signNurse3DateTime'] =$signNurse3DateTime!=''?date("Y-m-d H:i:s",strtotime($signNurse3DateTime)):'';
                }
                if ($signNurse4DateTime <> "") {
                    $arrayRecord['signNurse4DateTime'] =$signNurse4DateTime!=''?date("Y-m-d H:i:s",strtotime($signNurse4DateTime)):'';
                }
                if (!$chk_checklist_old_new) {
                    $arrayRecord['checklist_old_new'] = 'new';
                }
                //MAKE AUDIT STATUS CRATED OR MODIFIED
                unset($arrayStatusRecord);
                $arrayStatusRecord['user_id'] = $userId;
                $arrayStatusRecord['patient_id'] = $patient_id;
                $arrayStatusRecord['confirmation_id'] = $pConfirmId;
                $arrayStatusRecord['form_name'] = 'surgical_check_list_form';
                $arrayStatusRecord['action_date_time'] = date('Y-m-d H:i:s');
                //MAKE AUDIT STATUS CRATED OR MODIFIED
                if (isset($check_list_id) && $check_list_id > 0) {
                    DB::table('surgical_check_list')->where('check_list_id', $check_list_id)->update($arrayRecord);
                } else {
                    $check_list_id = DB::table('surgical_check_list')->insertGetId($arrayRecord);
                }
                //CODE START TO SET AUDIT STATUS AFTER SAVE
                unset($conditionArr);
                $conditionArr['confirmation_id'] = $pConfirmId;
                $conditionArr['form_name'] = 'surgical_check_list_form';
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
                //CODE TO CHECK NURSE ALL SIGNATURE AND SET VALUE IN STUB TABLE
                $chartSignedByNurse = $this->chkNurseSignNew($pConfirmId);
                $updateNurseStubTblQry = "UPDATE stub_tbl SET chartSignedByNurse='" . $chartSignedByNurse . "' WHERE patient_confirmation_id='" . $pConfirmId . "'";
                $updateNurseStubTblRes = DB::select($updateNurseStubTblQry); // or die(imw_error());
                //END CODE TO CHECK NURSE SIGNATURE AND SET VALUE IN STUB TABLE
                $message = " Record saved successfully ! ";
                return response()->json([
                            'status' => $status,
                            'message' => $message,
                            'requiredStatus' => $arrayRecord,
                            'data' => $request->json()->all(),
                            'check_list_id' => $check_list_id
                ]); // NOT_FOUND (404) being the HTTP response code 
            }
        }
        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => '',
                    'data' => $request->json()->all(),
        ]); // NOT_FOUND (404) being the HTTP response code 
    }

}
