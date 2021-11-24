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

class PatientFormController extends Controller {

    public function PatientForm_list(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $loginUserType = $request->json()->get('UserType') ? $request->json()->get('UserType') : $request->input('UserType');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $ascId = $request->json()->get('ascId') ? $request->json()->get('ascId') : $request->input('ascId');
        $data = [];
        $data1 = [];
        $data2 = [];
        $status = 0;
        $ScanDocument = [];
        $hasAdminPrv = 0;
        $message = " unauthorized ";
        $requiredStatus = [];
        $userId = $this->checkToken($userToken);
        $getImagesOrNotDetails = [];
        $sampleKey = [];
        $flag = '';
        $flag2 = '';
        $flagPRE = '';
        $flagHST = '';
        $dischargeflag = '';
        $Instructionflag = '';
        $Transferflag = '';
        $ControlledKey = [];
        if ($userId > 0) {
            if ($pConfirmId == "") {
                $message = " ConfirmId is missing ";
                $status = 1;
                $requiredStatus = 0;
            } elseif ($ascId == "") {
                $message = " ASCID is missing ";
                $status = 1;
                $requiredStatus = 0;
            } elseif ($loginUserType == "") {
                $message = " UserType is missing ";
                $status = 1;
                $requiredStatus = 0;
            } else {
                $authenticationDetails = $this->getRowRecord('users', 'usersId', $userId);
                $userType = $authenticationDetails->user_type;
                $userPrivileges = $authenticationDetails->user_privileges;
                $userPrivilegesArr = explode(', ', $userPrivileges);
                $admin_privileges = $authenticationDetails->admin_privileges;
                if ($admin_privileges) {
                    $admin_privilegesArr = explode(',', $admin_privileges);
                } else {
                    $admin_privilegesArr = array();
                }
                $patientDosQry = "select * from patientconfirmation where patientId = '$patient_id' order by dos DESC";
                $patientDosRes = DB::select($patientDosQry); // or die(imw_error());
                $chkLatestDatepatientDosQry = "select dos from patientconfirmation where patientId = '$patient_id' order by dos DESC LIMIT 0,1";
                $chkLatestDatepatientDosRes = DB::selectone($chkLatestDatepatientDosQry); // or die(imw_error());
                if ($chkLatestDatepatientDosRes) {
                    $chkLatestDatepatientDosRow = $chkLatestDatepatientDosRes;
                    $chkLatestDatepatient_dos_temp = $chkLatestDatepatientDosRow->dos;
                }
                if ($patientDosRes) {
                    $counter = 0;
                    foreach ($patientDosRes as $patientDosRow) {
                        $counter++;
                        $patient_confID = $patientDosRow->patientConfirmationId;
                        $patient_dos_temp = $patientDosRow->dos;
                        $SliderRight_patientPrimProc = $patientDosRow->patient_primary_procedure;
                        $SliderRight_patientPrimaryProcedureId = $patientDosRow->patient_primary_procedure_id;
                        $patient_dos_split = explode("-", $patient_dos_temp);
                        $patient_dos = $patient_dos_split[1] . "/" . $patient_dos_split[2] . "/" . $patient_dos_split[0];
                        // laser
                        $RightOpLaserSlider = 'Operating Room';
                        $primary_procedureSliderRightQry = "SELECT * FROM procedures WHERE (name = '" . addslashes($SliderRight_patientPrimProc) . "' OR procedureAlias = '" . addslashes($SliderRight_patientPrimProc) . "')";
                        $primary_procedureSliderRightRes = DB::selectone($primary_procedureSliderRightQry);
                        //$primary_procedureSliderRightNumRow = imw_num_rows($primary_procedureSliderRightRes);
                        if ($primary_procedureSliderRightRes) {
                            $primary_procedureSliderRightRow = $primary_procedureSliderRightRes; // imw_fetch_array($primary_procedureSliderRightRes);
                            $patient_primary_procedure_categoryRightID = $primary_procedureSliderRightRow->catId;
                            if ($patient_primary_procedure_categoryRightID == 2) {
                                $RightOpLaserSlider = 'Laser Procedure';
                            }
                        } //lasers
                        //CODE TO DISPLAY LATEST VISIT DOS IN RIGHT SLIDER
                        if ($patient_dos_temp == $chkLatestDatepatient_dos_temp) {
                            //$display_visit = "open";  
                            $display_visit = "";
                        } else {
                            $display_visit = "";
                        }
                        //END CODE TO DISPLAY LATEST VISIT DOS IN RIGHT SLIDER
                        //CODE TO GET FORM STATUS
                        $surgicalCheckListFormStatus = $this->getFormStatus("surgical_check_list", $ascId, $patient_confID);
                        $surgeryConsentFormStatus = $this->getFormStatus("surgery_consent_form", $ascId, $patient_confID);
                        $HippaConsentFormStatus = $this->getFormStatus("hippa_consent_form", $ascId, $patient_confID);
                        $BenefitConsentFormStatus = $this->getFormStatus("benefit_consent_form", $ascId, $patient_confID);
                        $InsuranceConsentFormStatus = $this->getFormStatus("insurance_consent_form", $ascId, $patient_confID);
                        $dischargeSummaryFormStatus = $this->getFormStatus("dischargesummarysheet", $ascId, $patient_confID);
                        $preopHealthQuestFormStatus = $this->getFormStatus("preophealthquestionnaire", $ascId, $patient_confID);
                        $historyPhysicalFormStatus = $this->getFormStatus("history_physicial_clearance", $ascId, $patient_confID);
                        $preopNursingFormStatus = $this->getFormStatus("preopnursingrecord", $ascId, $patient_confID);
                        $preNurseAlderateFormStatus = $this->getFormStatus("pre_nurse_alderate", $ascId, $patient_confID);
                        $postopNursingFormStatus = $this->getFormStatus("postopnursingrecord", $ascId, $patient_confID);
                        $postNurseAlderateFormStatus = $this->getFormStatus("post_nurse_alderate", $ascId, $patient_confID);
                        $macRegionalAnesthesiaFormStatus = $this->getFormStatus("localanesthesiarecord", $ascId, $patient_confID);
                        $preopGenralAnesthesiaFormStatus = $this->getFormStatus("preopgenanesthesiarecord", $ascId, $patient_confID);
                        $GenralAnesthesiaFormStatus = $this->getFormStatus("genanesthesiarecord", $ascId, $patient_confID);
                        $genralAnesthesiaNursesNotesFormStatus = $this->getFormStatus("genanesthesianursesnotes", $ascId, $patient_confID);
                        $OpRoomRecordFormStatus = $this->getFormStatus("operatingroomrecords", $ascId, $patient_confID);
                        $laserProcedureFormStatus = $this->getFormStatus("laser_procedure_patient_table", $ascId, $patient_confID);
                        $injectionMiscFormStatus = $this->getFormStatus("injection", $ascId, $patient_confID);
                        $surgical_operative_record_form = $this->getFormStatus("operativereport", $ascId, $patient_confID);
                        $AmendmentsNotesFormStatus = $this->getFormStatus("amendment", $ascId, $patient_confID);
                        $preopPhysicianFormStatus = $this->getAnotherFormStatus("preopphysicianorders", $ascId, $patient_confID);
                        $postopPhysicianFormStatus = $this->getAnotherFormStatus("postopphysicianorders", $ascId, $patient_confID);
                        $InstructionSheetFormStatus = $this->getAnotherFormStatus("patient_instruction_sheet", $ascId, $patient_confID);
                        $TransferFollowupsFormStatus = $this->getFormStatus("transfer_followups", $ascId, $patient_confID);
                        //END CODE TO GET FORM STATUS
                        $patientIdDos = $patientDosRow->patientId;
                        $rightNavQry = "select * from left_navigation_forms where confirmationId = '$patient_confID'";
                        $rightNavRow = $rightNavRes = DB::selectone($rightNavQry); // or die(imw_error());
                        //$rightNavRow = imw_fetch_array($rightNavRes);
                        $right_surgical_check_list_form = $rightNavRow->surgical_check_list_form;
                        $right_surgery_form = $rightNavRow->surgery_form;
                        $right_hippa_form = $rightNavRow->hippa_form;
                        $right_assign_benifits_form = $rightNavRow->assign_benifits_form;
                        $right_insurance_card_form = $rightNavRow->insurance_card_form;
                        $right_pre_op_health_ques_form = $rightNavRow->pre_op_health_ques_form;
                        $right_history_physical_form = $rightNavRow->history_physical_form;
                        $right_pre_op_nursing_form = $rightNavRow->pre_op_nursing_form;
                        $right_pre_nurse_alderate_form = $rightNavRow->pre_nurse_alderate_form;
                        $right_post_op_nursing_form = $rightNavRow->post_op_nursing_form;
                        $right_post_nurse_alderate_form = $rightNavRow->post_nurse_alderate_form;
                        $right_pre_op_physician_order_form = $rightNavRow->pre_op_physician_order_form;
                        $right_post_op_physician_order_form = $rightNavRow->post_op_physician_order_form;
                        $right_mac_regional_anesthesia_form = $rightNavRow->mac_regional_anesthesia_form;
                        $right_pre_op_genral_anesthesia_form = $rightNavRow->pre_op_genral_anesthesia_form;
                        $right_genral_anesthesia_form = $rightNavRow->genral_anesthesia_form;
                        $right_genral_anesthesia_nurses_notes_form = $rightNavRow->genral_anesthesia_nurses_notes_form;
                        $right_intra_op_record_form = $rightNavRow->intra_op_record_form;
                        $right_laser_procedure_form = $rightNavRow->laser_procedure_form;
                        $right_surgical_operative_record_form = $rightNavRow->surgical_operative_record_form;
                        $right_qa_check_list_form = $rightNavRow->qa_check_list_form;
                        $right_discharge_summary_form = $rightNavRow->discharge_summary_form;
                        $right_post_op_instruction_sheet_form = $rightNavRow->post_op_instruction_sheet_form;
                        $right_transfer_and_followups_form = $rightNavRow->transfer_and_followups_form;
                        $right_physician_amendments_form = $rightNavRow->physician_amendments_form;
                        $right_injection_misc_form = $rightNavRow->injection_misc_form;
                        //END CODE TO DISPLAY LATEST VISIT DOS IN RIGHT SLIDER
                        //$finalizeStatusQry = "select * from patientconfirmation where ascId = '$ascId' and patientConfirmationId = '".$pConfId."'";
                        $stubD = $this->getRowRecord('stub_tbl', 'patient_confirmation_id', $patient_confID, 'stub_id', 'ASC', 'stub_id');
                        $unfinalizedClick = "";
                        $unfinalizedDisplay = ''; // 'displayNone';

                        $historyClick = "";
                        $historyDisplay = ''; // 'displayNone';

                        $finalizeStatusQry = "select finalize_status, surgeonId from patientconfirmation where (patientId = '$patient_id' and patientConfirmationId = '" . $patient_confID . "')";
                        $finalizeStatusRow = DB::selectone($finalizeStatusQry); // or die(imw_error());
                        //$finalizeStatusRow = imw_fetch_array($finalizeStatusRes);
                        $finalizeStatusName = $finalizeStatusRow->finalize_status;

                        $confirmationSurgeonID = $finalizeStatusRow->surgeonId;
                        if ($userId == $confirmationSurgeonID) {
                            $hasAdminPrv = 1; //OVERWRITE FROM mainpage.php
                        }
                        if ($finalizeStatusName == "true") {
                            $finalized = "Finalized";
                            if ($hasAdminPrv == 1) {
                                $unfinalizedClick = ''; //"javascript:top.unfinalize('" . $patient_confID . "','" . $patientIdDos . "', '" . $ascId . "', '" . $stubD->stub_id . "');";
                                $unfinalizedDisplay = ''; // 'displayInlineBlock';
                            } else {
                                $unfinalizedClick = '';
                                $unfinalizedDisplay = ''; // 'displayNone';
                            }
                        } else {
                            $finalized = '';
                            $unfinalizedClick = '';
                            $unfinalizedDisplay = ''; // 'displayNone';
                        }
                        $FHistory = DB::select("select * from finalize_history where patient_confirmation_id='" . $patient_confID . "' order by finalize_history_id DESC");
                        if ($FHistory && $hasAdminPrv == 1) {
                            // $historyClick = "javascript:top.unfinalizeHistory('" . $patient_confID . "','" . $patientIdDos . "','" . $ascId . "','" . $stubD->stub_id . "','" . $hasAdminPrv . "');";
                            // $historyDisplay = 'displayInlineBlock';
                        }
                        $surgeryCenterSettings = $this->loadSettings('peer_review');
                        $surgeryCenterPeerReview = $surgeryCenterSettings->peer_review;
                        $practiceNameMatchSlider = '';
                        if ($surgeryCenterPeerReview == 'Y' && $loginUserType == 'Surgeon') {
                            $practiceNameMatchSlider = $this->getPracMatchUserId($userId, $confirmationSurgeonID);
                        }
                        //"ScanDocuments,PhysicianNotes,CheckList,PreOPHealthQues,HPClearance,LaserProcedure,DischargeSummary,PostOPInstSheet,TransferandFollowup"
                        $scanFolderDetailLists = DB::select("select * from scan_documents where confirmation_id='" . $patient_confID . "' order by document_id desc");
                        if ($scanFolderDetailLists) {
                            $sampleKey = ["key" => 'ScanDocuments', 'date' => $patient_dos_temp];
                            foreach ($scanFolderDetailLists as $FolderList) {
                                $document_id = $FolderList->document_id;
                                $document_name = $FolderList->document_name;
                                if ($document_name == 'Anesthesia Consent') {
                                    $document_name = 'A.Consent';
                                }
                                $ControlledKey['ScanDocuments'][] = [
                                    'flag' => '',
                                    'id' => $document_id,
                                    'name' => $document_name,
                                    'date' => $patient_dos,
                                    "key" => 'ScanDocuments'
                                ];
                            }
                        }
                        if ($pConfirmId <> $patient_confID) {
                            $flag = '';
                            if ($right_physician_amendments_form == 'false') {
                                if (in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr) || (in_array("Admin", $userPrivilegesArr) && in_array("EMR", $admin_privileges))) {
                                    $href = "";
                                    $sampleKey = ["key" => 'PhysicianNotes', 'date' => $patient_dos_temp];
                                    $ControlledKey['PhysicianNotes'] = [
                                        'flag' => '',
                                        'id' => $patient_confID,
                                        'name' => '',
                                        'date' => $patient_dos,
                                        "key" => 'PhysicianNotes'
                                    ];
                                } else {
                                    $href = "";
                                }
                            }
                            $flag2 = '';
                            $showCheckListAdmin = false;
                            if ($right_surgical_check_list_form == 'false') {
                                $showCheckList = (!$showCheckListAdmin && ($surgicalCheckListFormStatus == 'completed' || $surgicalCheckListFormStatus == 'not completed')) ? true : $showCheckListAdmin;
                                $showCheckListStatus = $this->getChartShowStatus($patient_confID, 'checklist');
                                $showCheckList = $showCheckListStatus ? ($showCheckListStatus == 1 ? true : ($showCheckListStatus == 2 ? false : $showCheckList)) : $showCheckList;
                                if ($showCheckList) {
                                    $sampleKey = ["key" => 'Checklist', 'date' => $patient_dos];
                                    if (in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr) || (in_array("Admin", $userPrivilegesArr) && in_array("EMR", $admin_privileges))) {
                                        $ControlledKey['CheckList'] = ['flag' =>'',
                                            'id' => $patient_confID,
                                            'name' => '',
                                            'date' => $patient_dos,
                                            "key" => 'CheckList'
                                        ];
                                    } else {
                                        $href = ""; // "javascript:void(0);";
                                    }
                                }
                            }
                            $flagAnesthesia = '';
                            if ($right_mac_regional_anesthesia_form == 'false') {
                                $sampleKey[] = 'Anesthesia';
                                if (in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr) || (in_array("Admin", $userPrivilegesArr) && in_array("EMR", $admin_privileges)) || in_array("Billing", $userPrivilegesArr)) {
                                    // MAC/Regional
                                    $ControlledKey['Anesthesia'] = ['flag' =>'',
                                        'id' => $patient_confID,
                                        'name' => '',
                                        'date' => $patient_dos,
                                        "key" => 'Anesthesia'
                                    ];
                                }
                            }
                            $flagPRE = '';
                            $flagHST = '';
                            if ($right_pre_op_health_ques_form == 'false' || $right_history_physical_form == 'false') {
                                if ($preopHealthQuestFormStatus == 'completed') {
                                    $sampleKey = ["key" => 'PreOPHealthQues', 'date' => $patient_dos];
                                    $flagPRE = 'green';
                                }

                                if ($preopHealthQuestFormStatus == 'not completed') {
                                    $sampleKey = ["key" => 'PreOPHealthQues', 'date' => $patient_dos];
                                    $flagPRE = 'red';
                                }

                                if ($historyPhysicalFormStatus == 'completed') {
                                    $sampleKey = ["key" => 'HPClearance', 'date' => $patient_dos];
                                    $flagHST = 'green';
                                }

                                if ($historyPhysicalFormStatus == 'not completed') {
                                    $sampleKey = ["key" => 'HPClearance', 'date' => $patient_dos];
                                    $flagHST = 'red';
                                }
                                if (in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr) || (in_array("Admin", $userPrivilegesArr) && in_array("EMR", $admin_privileges))) {
                                    $hrefPRE = ""; // "javascript:right_link_click('pre_op_health_quest.php',1,'" . $seqArrTemp5 . "','#D1E0C9','" . $patientIdDos . "','" . $patient_confID . "','" . $ascId . "');";
                                    $ControlledKey['PreOPHealthQues'] = ['flag' =>'',
                                        'id' => $patient_confID,
                                        'name' => '',
                                        'date' => $patient_dos,
                                        "key" => 'PreOPHealthQues'
                                    ];
                                } else {
                                    $hrefPRE = ""; // "javascript:void(0);";
                                }
                            }


                            if ($right_discharge_summary_form == 'false') {
                                if (in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr) || (in_array("Admin", $userPrivilegesArr) && in_array("EMR", $admin_privileges))) {
                                    $href = ""; // "javascript:right_link_click('discharge_summary_sheet.php',7,'" . $seqArrTemp18 . "','#D1E0C9','" . $patientIdDos . "','" . $patient_confID . "','" . $ascId . "');";
                                    $sampleKey = ["key" => 'DischargeSummary', 'date' => $patient_dos];
                                    $ControlledKey['DischargeSummary'] = ['flag' =>'',
                                        'id' => $patient_confID,
                                        'name' => '',
                                        'date' => $patient_dos,
                                        "key" => 'DischargeSummary'
                                    ];
                                } else {
                                    $href = ""; // "javascript:void(0);";
                                }
                            }
                            if ($right_post_op_instruction_sheet_form == 'false') {
                                $sampleKey = ["key" => 'Post_Op_Inst_Sheet', 'date' => $patient_dos];
                                if (in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr) || (in_array("Admin", $userPrivilegesArr) && in_array("EMR", $admin_privileges)) || in_array("Billing", $userPrivilegesArr)) {
                                    $href = ""; // "javascript:right_link_click('instructionsheet.php',8,'" . $seqArrTemp19 . "','#D1E0C9','" . $patientIdDos . "','" . $patient_confID . "','" . $ascId . "');";
                                    $ControlledKey['PostOPInstSheet'] = ['flag' =>'',
                                        'id' => $patient_confID,
                                        'name' => '',
                                        'date' => $patient_dos,
                                        "key" => 'Post_Op_Inst_Sheet'
                                    ];
                                } else {
                                    $href = ""; // "javascript:void(0);";
                                }
                            }
                            //Transfer and Follow up
                            if ($right_transfer_and_followups_form == 'false') {
                                if (in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr) || (in_array("Admin", $userPrivilegesArr) && in_array("EMR", $admin_privileges)) || in_array("Billing", $userPrivilegesArr)) {
                                    $sampleKey = ["key" => 'Transfer_and_Followup', 'date' => $patient_dos];
                                    $ControlledKey['TransferandFollowup'] = ['flag' =>'',
                                        'id' => $patient_confID,
                                        'name' => '',
                                        'date' => $patient_dos,
                                        "key" => 'TransferandFollowup'
                                    ];
                                } else {
                                    $href = ""; // "javascript:void(0);";
                                }
                            }

                            //Intra-Op Record
                            if ($right_intra_op_record_form == 'false') {
                                $flagOP = '';
                                $sampleKey = ["key" => 'Intra-Op-Record', 'date' => $patient_dos];
                                if ($OpRoomRecordFormStatus == 'completed') {
                                    $flagOP = 'green';
                                }

                                if ($OpRoomRecordFormStatus == 'not completed') {
                                    $flagOP = 'red';
                                }
                                if (in_array("Super User", $userPrivilegesArr) || in_array("Surgeon", $userPrivilegesArr) || in_array("Anesthesia", $userPrivilegesArr) || in_array("Nursing Record", $userPrivilegesArr) || in_array("Staff", $userPrivilegesArr) || in_array("Coordinator", $userPrivilegesArr) || (in_array("Admin", $userPrivilegesArr) && in_array("EMR", $admin_privileges)) || in_array("Billing", $userPrivilegesArr)) {
                                    $href = ""; // "javascript:right_link_click('transfer_followups.php',9,'" . $seqArrTemp21 . "','#D1E0C9','" . $patientIdDos . "','" . $patient_confID . "','" . $ascId . "');";
                                    $ControlledKey['Intra-OP-Record'] = ['flag' =>'',
                                        'id' => $patient_confID,
                                        'name' => '',
                                        'date' => $patient_dos,
                                        "key" => 'Intra-Op-Record'
                                    ];
                                } else {
                                    $href = ""; // "javascript:void(0);";
                                }
                            }
                            //Physician Orders
                            //Nursing Record Pre-Op
                            if ($right_pre_op_nursing_form == 'false' || $right_post_op_nursing_form == 'false' || $right_pre_nurse_alderate_form == 'false' || $right_post_nurse_alderate_form == 'false') {
                                //Pre-Op,Pre-Op-Aldrete,Post-Op,Post-Op-Aldrete
                                if ($right_pre_op_nursing_form == 'false') {
                                    $ControlledKey['NursingRecord'] = ['flag' => '',
                                        'id' => $patient_confID,
                                        'name' => '',
                                        'date' => $patient_dos,
                                        "key" => 'Pre-Op'
                                    ];
                                }
                                if ($right_pre_nurse_alderate_form == 'false') {
                                    $ControlledKey['NursingRecord'] = ['flag' => '',
                                        'id' => $patient_confID,
                                        'name' => '',
                                        'date' => $patient_dos,
                                        "key" => 'Pre-Op-Aldrete'
                                    ];
                                }
                                if ($right_post_op_nursing_form == 'false') {
                                    $ControlledKey['NursingRecord'] = ['flag' =>'',
                                        'id' => $patient_confID,
                                        'name' => '',
                                        'date' => $patient_dos,
                                        "key" => 'Post-Op'
                                    ];
                                }
                                if ($right_post_nurse_alderate_form == 'false') {
                                    $ControlledKey['NursingRecord'] = ['flag' =>'',
                                        'id' => $patient_confID,
                                        'name' => '',
                                        'date' => $patient_dos,
                                        "key" => 'Post-Op-Aldrete'
                                    ];
                                }
                            }
                            //Physician Orders
                            if (($right_pre_op_physician_order_form == 'false') || ($right_post_op_physician_order_form == 'false')) {

                                if ($right_pre_op_physician_order_form == 'false') {
                                    $ControlledKey['PhysicianOrder'] = ['flag' =>'',
                                        'id' => $patient_confID,
                                        'name' => '',
                                        'date' => $patient_dos,
                                        "key" => 'Pre-Op-Order'
                                    ];
                                }
                                if ($right_post_op_physician_order_form == 'false') {
                                    $ControlledKey['PhysicianOrder'] = ['flag' =>'',
                                        'id' => $patient_confID,
                                        'name' => '',
                                        'date' => $patient_dos,
                                        "key" => 'Post-Op-Order'
                                    ];
                                }
                            }
                            if ($right_intra_op_record_form == 'false' || $right_laser_procedure_form == 'false' || $right_injection_misc_form == 'false') {
                                if ($right_laser_procedure_form == 'false') {
                                    $sampleKey = ["key" => 'LaserProcedure', 'date' => $patient_dos];
                                    $ControlledKey['LaserProcedure'] = ['flag' => '',
                                        'id' => $patient_confID,
                                        'name' => '',
                                        'date' => $patient_dos,
                                        "key" => 'LaserProcedure'
                                    ];
                                }

                                if ($right_injection_misc_form == 'false') {
                                    $sampleKey = ["key" => 'Injection_Miscellaneous', 'date' => $patient_dos];
                                    $ControlledKey['Injection_Miscellaneous'] = ['flag' => '',
                                        'id' => $patient_confID,
                                        'name' => '',
                                        'date' => $patient_dos,
                                        "key" => 'Injection_Miscellaneous'
                                    ];
                                }
                            }
                            $category = "select confirmation_id from `consent_multiple_form` WHERE  confirmation_id = '" . $patient_confID . "' AND left_navi_status = 'false'";
                            $categoryRow = DB::select($category);
                            if ($categoryRow > 0) {
                                $categoryRightSelectQry = "select * from `consent_category`  order by category_id";
                                $categoryRightSelectRows = DB::select($categoryRightSelectQry);
                                foreach ($categoryRightSelectRows as $categoryRightSelectRow) {
                                    $cat_id = $categoryRightSelectRow->category_id;
                                    $category_name = $categoryRightSelectRow->category_name;
                                    $consentFormTemplateRightSelectQry = "select consent_id,consent_alias,consent_delete_status from `consent_forms_template`"
                                            . " where consent_category_id='$cat_id' order by consent_id";
                                    $consentFormTemplateRightSelectRes = DB::select($consentFormTemplateRightSelectQry);
                                    $consentFormSelectRightConsentTemplateId = array();
                                    $consentFormRightSelectCategoryQry = "select surgery_consent_id from `consent_multiple_form` WHERE  confirmation_id = '" . $patient_confID . "' "
                                            . "AND consent_category_id ='" . $cat_id . "' AND left_navi_status='false'";
                                    $consentFormRightSelectCategoryRes = DB::select($consentFormRightSelectCategoryQry);
                                    if ($consentFormRightSelectCategoryRes) {
                                        if ($consentFormTemplateRightSelectRes) {
                                            //print_r($consentFormRightSelectCategoryRes);
                                            $ControlledKey['ConsentForm'] = ['flag' => '',
                                                'id' => $patient_confID,
                                                'name' => '',
                                                'date' => $patient_dos,
                                                "key" => 'ConsentForm'
                                            ];
                                        }
                                    }
                                }
                            }
                        }
                        //NursingRecordPreOp,NursingRecordPreOpAldrete,NursingRecordPostOp,NursingRecordPostOpAldrete
                        $samples = ['ScanDocuments' => 'Scan Documents', 'PhysicianNotes' => 'Physician Notes',
                            'CheckList' => 'Check List', 'PreOPHealthQues' => 'Pre OP Health Ques',
                            'HPClearance' => 'H and P Clearance', 'PhysicianOrders' => 'Physician Orders',
                            'NursingRecord' => 'Nursing Record', 'Anesthesia' => 'Anesthesia', 'Injection_Miscellaneous' => 'Injection/Miscellaneous',
                            'LaserProcedure' => 'Laser Procedure', 'DischargeSummary' => 'Discharge Summary',
                            'PostOPInstSheet' => 'Post OP Inst. Sheet', 'TransferandFollowup' => 'Transfer and Followup',
                            'Intra-OP-Record' => 'Operating Room', 'PhysicianOrder' => 'Physician Order',
                            'ConsentForm' => 'Consent Forms'];
                        $sample_keys = '';
                        $sample_title = '';
                        $myControlledKey = '';
                        ////Pre-Op,Pre-Op-Aldrete,Post-Op,Post-Op-Aldrete
                        if ($pConfirmId <> $patient_confID) {
                            $myControlledKey = $ControlledKey;
                            $i = 0;
                            $sample_title.="Scan Documents,";
                            $sample_keys.="ScanDocuments,";
                            foreach ($ControlledKey as $keys => $val) {
                                $keydate = isset($ControlledKey[$keys]['date']) ? $ControlledKey[$keys]['date'] : "";
                                $key = isset($ControlledKey[$keys]['key']) ? $ControlledKey[$keys]['key'] : "";
                                $keyId = isset($ControlledKey[$keys]['id']) ? $ControlledKey[$keys]['id'] : "";
                             //   echo $keyId . "==" . $patient_confID . "::" . $patient_dos . ":::" . $keydate . ":::" . $key . "::" . $patient_confID . "<br />";
                                if ($patient_dos == $keydate && $keyId == $patient_confID) {
                                    $sample_title.=$samples[$keys] . ',';
                                    $sample_keys.=$keys . ',';
                                    $i++;
                                }
                            }
                        } else {
                            $sample_title.="Scan Documents";
                            $sample_keys.="ScanDocuments";
                        }

                        // "ScanDocuments,PhysicianNotes,CheckList,PreOPHealthQues,HPClearance,LaserProcedure,DischargeSummary,PostOPInstSheet,TransferandFollowup"
                        $data2[] = [
                            'Date' => $patient_dos,
                            'hasAdminPrv' => $hasAdminPrv,
                            'finalize_status' => $finalizeStatusName,
                            'ParseDate' => $patient_dos_temp,
                            'pConfirmId' => $patient_confID,
                            'ascId' => $ascId,
                            //  'ControlledKey'=>$ControlledKey,
                            // 'ControlledKeyDate' => isset($ControlledKey[$keys]['date']) ? $ControlledKey[$keys]['date'] : "",
                            "sample_key" => rtrim($sample_keys, ","), //"ScanDocuments,PhysicianNotes,CheckList,PreOPHealthQues,HPClearance,LaserProcedure,DischargeSummary,PostOPInstSheet,TransferandFollowup",
                            'sample_title' => rtrim($sample_title, ","), //'Scan Documents,Physician Notes,Check List,Pre OP Health Ques,H and P Clearance,Laser Procedure,Discharge Summary,Post OP Inst. Sheet,Transfer and Followup',
                                //'patient_primary_procedure_categoryRightID' => $patient_primary_procedure_categoryRightID
                        ];
                    }
                }
                $status = 1;
                $message = ' Patient Form List ';
                $data = ["PatientForm" => $data2];
            }
        }

        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'data' => $data,
                    'requiredStatus' => '',
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

    public function getFormStatus($tablename, $ascId, $pConfId) {
        $formStatusQry = "select form_status from $tablename where confirmation_id = '" . $pConfId . "'";
        $formStatusRow = DB::selectone($formStatusQry); // or die(imw_error());
        if ($formStatusRow) {
            $formStatusName = $formStatusRow->form_status;
            return $formStatusName;
        }
        return '';
    }

    public function getAnotherFormStatus($tablename, $ascId, $pConfId) {
        $formStatusQry = "select form_status from $tablename where patient_confirmation_id = '" . $pConfId . "'";
        $formStatusRes = DB::selectone($formStatusQry); // or die(imw_error())
        if ($formStatusRes) {
            $formStatusName = $formStatusRes->form_status;
            return $formStatusName;
        }
        return '';
    }

    public function Patient_History(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $loginUserType = $request->json()->get('UserType') ? $request->json()->get('UserType') : $request->input('UserType');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $ascId = $request->json()->get('ascId') ? $request->json()->get('ascId') : $request->input('ascId');
        $finalize_date = $request->json()->get('finalize_date') ? $request->json()->get('finalize_date') : $request->input('finalize_date');
        $data = [];
        $status = 0;
        $ScanDocument = [];
        $hasAdminPrv = 0;
        $message = " unauthorized ";
        $requiredStatus = [];
        $userId = $this->checkToken($userToken);
        $getImagesOrNotDetails = [];
        $sampleKey = [];
        if ($userId > 0) {
            if ($pConfirmId == "") {
                $message = " ConfirmId is missing ";
                $status = 1;
                $requiredStatus = 0;
            } elseif ($ascId == "") {
                $message = " ASCID is missing ";
                $status = 1;
                $requiredStatus = 0;
            } elseif ($loginUserType == "") {
                $message = " UserType is missing ";
                $status = 1;
                $requiredStatus = 0;
            } elseif ($finalize_date == "") {
                $message = " Finalized date is missing ";
                $status = 1;
                $requiredStatus = 0;
            } else {
                $data = $this->unfinalizeHistory($pConfirmId); // 
                $status = 1;
                $message = 'Patient History';
            }
        }
        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'data' => $data,
                    'requiredStatus' => '',
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

    public function unFinalizedPatient(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $loginUserType = $request->json()->get('UserType') ? $request->json()->get('UserType') : $request->input('UserType');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $ascId = $request->json()->get('ascId') ? $request->json()->get('ascId') : $request->input('ascId');
        $data = [];
        $status = 0;
        $ScanDocument = [];
        $hasAdminPrv = 0;
        $unfinalizedStatus = 0;
        $message = " unauthorized ";
        $requiredStatus = [];
        $userId = $this->checkToken($userToken);
        $getImagesOrNotDetails = [];
        $sampleKey = [];
        if ($userId > 0) {
            if ($pConfirmId == "") {
                $message = " ConfirmId is missing ";
                $status = 1;
                $requiredStatus = 0;
            } elseif ($ascId == "") {
                $message = " ASCID is missing ";
                $status = 1;
                $requiredStatus = 0;
            } elseif ($loginUserType == "") {
                $message = " UserType is missing ";
                $status = 1;
                $requiredStatus = 0;
            } else {
                $rows = $this->getExtractRecord('patientconfirmation', 'patientConfirmationId', $pConfirmId, 'patientConfirmationId');
                foreach ($rows as $row) {
                    if ($row->patientConfirmationId == $pConfirmId) {
                        $insertRecords = array();
                        $insertRecords['patient_confirmation_id'] = $pConfirmId;
                        $insertRecords['finalize_action'] = 'unfinalize';
                        $insertRecords['finalize_action_script'] = 'manual';
                        $insertRecords['finalize_action_type'] = 'revised';
                        $insertRecords['finalize_action_user_id'] = $userId;
                        $insertRecords['finalize_action_datetime'] = date('Y-m-d H:i:s');
                        $insert_id = DB::table('finalize_history')->insertGetId($insertRecords);
                        if ($insert_id) {
                            $updateRecords = array();
                            $updateRecords['finalize_status'] = '';
                            if (getenv("VCNA_EXPORT_ENABLE") == 'YES') {
                                $updateRecords['vcna_export_status'] = '0';
                                $updateStatus = DB:: table('patientconfirmation')->where('patientConfirmationId', $pConfirmId)->update($updateRecords);
                                if ($updateStatus) {
//return true;	
                                } else {
                                    DB::table('finalize_history')->where('finalize_history_id', $insert_id)->delete();
                                }
                            }
                        }
                    }
                }
                $status = 1;
                $unfinalizedStatus = 1;
                $message = 'Unfinalized successfully !';
            }
        }

        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'data' => $data,
                    'unfinalizedStatus' => $unfinalizedStatus,
                    'requiredStatus' => '',
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

    public function unfinalizeHistory($pConfirmId) {
        $this->returnData = [];
        $pconf = $this->getRowRecord('patientconfirmation', 'patientConfirmationId', $pConfirmId, 'patientConfirmationId', 'DESC', 'patientId,finalize_status,dos');
        $patient = $this->getRowRecord('patient_data_tbl', 'patient_id', $pconf->patientId, 'patient_id', 'DESC', ' patient_fname,patient_mname,patient_lname ');
        $this->returnData['finalize_status'] = empty($pconf->finalize_status) ? (int) 1 : 0; // 'Unfinalized' : 'Finalized';
        $this->returnData['patient_name'] = '';
        $this->returnData['patient_name'] .=!empty($patient->patient_lname) ? $patient->patient_lname : '';
        $this->returnData['patient_name'] .= (!empty($patient->patient_lname) && !empty($patient->patient_fname) ) ? ', ' : '';
        $this->returnData['patient_name'] .=!empty($patient->patient_fname) ? $patient->patient_fname : '';
        $this->returnData['patient_name'] .=!empty($patient->patient_mname) ? ' ' . $patient->patient_mname : '';

        $this->returnData['dos'] = date('m/d/Y', strtotime($pconf->dos));
        $this->returnData['finalize_history'] = array();
        $FHistory = DB:: select("select * from finalize_history where patient_confirmation_id='" . $pConfirmId . "' order by finalize_history_id DESC");
        //$this->getArrayRecords('finalize_history', 'patient_confirmation_id', $pConfirmId, 'finalize_history_id', 'DESC');
        if ($FHistory) {
            foreach ($FHistory as $key => $row) {
                $userDetail = '';
                $user = $this->getRowRecord('users', 'usersId', $row->finalize_action_user_id, 'usersId', 'DESC', 'fname,mname,lname,user_type');
                //if ($user) {
                $userDetail .=!empty($user->lname) ? $user->lname : '';
                $userDetail .= (!empty($user->fname) && !empty($user->lname)) ? ", " : '';
                $userDetail .=!empty($user->fname) ? $user->fname : '';
                $userDetail .=!empty($user->mname) ? '&nbsp;' . $user->mname : '';

                $userDetail .=!empty($userDetail) ? '&nbsp;(' . @$user->user_type . ')' : @$user->user_type;
                $this->returnData['finalize_history'][] = array(
                    'id' => $row->finalize_history_id,
                    'action' => ucwords($row->finalize_action),
                    'action_mode' => ($row->finalize_action_script == 'auto' ? ' Auto ' . ucwords($row->finalize_action) : 'Manually ' . ucwords($row->finalize_action) . ' ' ),
                    'action_type' => ucwords($row->finalize_action_type == 'revised' ? 'yes' : 'no' ),
                    'user' => $userDetail,
                    'date' => date('m-d-Y', strtotime($row->finalize_action_datetime)),
                    'time' => date('h:i A', strtotime($row->finalize_action_datetime))
                );
                // }
            }
            // print_r($this->returnData);
            return $this->returnData;
        }
        return false;
    }

    public function Listing(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $patient_confID = $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $selecteddate = $request->json()->get('selecteddate') ? $request->json()->get('selecteddate') : $request->input('selecteddate');
        $datatype = $request->json()->get('datatype') ? $request->json()->get('datatype') : $request->input('datatype');
        $ascId = $request->json()->get('ascId') ? $request->json()->get('ascId') : $request->input('ascId');
        $data = [];
        $data1 = [];
        $data2 = [];
        $status = 0;
        $listStatus = 1;
        $ScanDocument = [];
        $hasAdminPrv = 0;
        $message = " unauthorized ";
        $requiredStatus = [];
        $userId = $this->checkToken($userToken);
        $getImagesOrNotDetails = [];
        $sampleKey = [];
        $title_key = [];

        $flag = '';
        $flag2 = '';
        $flagPRE = '';
        $flagHST = '';
        $dischargeflag = '';
        $Instructionflag = '';
        $Transferflag = '';
        $ControlledKey = [];
        $data = [];
        if ($userId > 0) {
            if ($pConfirmId == "") {
                $message = " ConfirmId is missing ";
                $status = 1;
                $requiredStatus = 0;
                $listStatus = 0;
            } else if ($selecteddate == '' && $datatype == 'ScanDocument') {
                $message = " Selected date is missing ";
                $status = 1;
                $requiredStatus = 0;
                $listStatus = 0;
            } else if ($datatype == "") {
                $message = " dataType is missing ";
                $status = 1;
                $requiredStatus = 0;
                $listStatus = 0;
            } else {
                $surgicalCheckListFormStatus = $this->getFormStatus("surgical_check_list", $ascId, $patient_confID);
                $surgeryConsentFormStatus = $this->getFormStatus("surgery_consent_form", $ascId, $patient_confID);
                $HippaConsentFormStatus = $this->getFormStatus("hippa_consent_form", $ascId, $patient_confID);
                $BenefitConsentFormStatus = $this->getFormStatus("benefit_consent_form", $ascId, $patient_confID);
                $InsuranceConsentFormStatus = $this->getFormStatus("insurance_consent_form", $ascId, $patient_confID);
                $dischargeSummaryFormStatus = $this->getFormStatus("dischargesummarysheet", $ascId, $patient_confID);
                $preopHealthQuestFormStatus = $this->getFormStatus("preophealthquestionnaire", $ascId, $patient_confID);
                $historyPhysicalFormStatus = $this->getFormStatus("history_physicial_clearance", $ascId, $patient_confID);
                $preopNursingFormStatus = $this->getFormStatus("preopnursingrecord", $ascId, $patient_confID);
                $preNurseAlderateFormStatus = $this->getFormStatus("pre_nurse_alderate", $ascId, $patient_confID);
                $postopNursingFormStatus = $this->getFormStatus("postopnursingrecord", $ascId, $patient_confID);
                $postNurseAlderateFormStatus = $this->getFormStatus("post_nurse_alderate", $ascId, $patient_confID);
                $macRegionalAnesthesiaFormStatus = $this->getFormStatus("localanesthesiarecord", $ascId, $patient_confID);
                $preopGenralAnesthesiaFormStatus = $this->getFormStatus("preopgenanesthesiarecord", $ascId, $patient_confID);
                $GenralAnesthesiaFormStatus = $this->getFormStatus("genanesthesiarecord", $ascId, $patient_confID);
                $genralAnesthesiaNursesNotesFormStatus = $this->getFormStatus("genanesthesianursesnotes", $ascId, $patient_confID);
                $OpRoomRecordFormStatus = $this->getFormStatus("operatingroomrecords", $ascId, $patient_confID);
                $laserProcedureFormStatus = $this->getFormStatus("laser_procedure_patient_table", $ascId, $patient_confID);
                $injectionMiscFormStatus = $this->getFormStatus("injection", $ascId, $patient_confID);
                $surgical_operative_record_form = $this->getFormStatus("operativereport", $ascId, $patient_confID);
                $AmendmentsNotesFormStatus = $this->getFormStatus("amendment", $ascId, $patient_confID);
                $preopPhysicianFormStatus = $this->getAnotherFormStatus("preopphysicianorders", $ascId, $patient_confID);
                $postopPhysicianFormStatus = $this->getAnotherFormStatus("postopphysicianorders", $ascId, $patient_confID);
                $InstructionSheetFormStatus = $this->getAnotherFormStatus("patient_instruction_sheet", $ascId, $patient_confID);
                $TransferFollowupsFormStatus = $this->getFormStatus("transfer_followups", $ascId, $patient_confID);
                //END CODE TO GET FORM STATUS

                $rightNavQry = "select * from left_navigation_forms where confirmationId = '$patient_confID'";
                $rightNavRow = $rightNavRes = DB::selectone($rightNavQry); // or die(imw_error());
                //$rightNavRow = imw_fetch_array($rightNavRes);
                $right_surgical_check_list_form = $rightNavRow->surgical_check_list_form;
                $right_surgery_form = $rightNavRow->surgery_form;
                $right_hippa_form = $rightNavRow->hippa_form;
                $right_assign_benifits_form = $rightNavRow->assign_benifits_form;
                $right_insurance_card_form = $rightNavRow->insurance_card_form;
                $right_pre_op_health_ques_form = $rightNavRow->pre_op_health_ques_form;
                $right_history_physical_form = $rightNavRow->history_physical_form;
                $right_pre_op_nursing_form = $rightNavRow->pre_op_nursing_form;
                $right_pre_nurse_alderate_form = $rightNavRow->pre_nurse_alderate_form;
                $right_post_op_nursing_form = $rightNavRow->post_op_nursing_form;
                $right_post_nurse_alderate_form = $rightNavRow->post_nurse_alderate_form;
                $right_pre_op_physician_order_form = $rightNavRow->pre_op_physician_order_form;
                $right_post_op_physician_order_form = $rightNavRow->post_op_physician_order_form;
                $right_mac_regional_anesthesia_form = $rightNavRow->mac_regional_anesthesia_form;
                $right_pre_op_genral_anesthesia_form = $rightNavRow->pre_op_genral_anesthesia_form;
                $right_genral_anesthesia_form = $rightNavRow->genral_anesthesia_form;
                $right_genral_anesthesia_nurses_notes_form = $rightNavRow->genral_anesthesia_nurses_notes_form;
                $right_intra_op_record_form = $rightNavRow->intra_op_record_form;
                $right_laser_procedure_form = $rightNavRow->laser_procedure_form;
                $right_surgical_operative_record_form = $rightNavRow->surgical_operative_record_form;
                $right_qa_check_list_form = $rightNavRow->qa_check_list_form;
                $right_discharge_summary_form = $rightNavRow->discharge_summary_form;
                $right_post_op_instruction_sheet_form = $rightNavRow->post_op_instruction_sheet_form;
                $right_transfer_and_followups_form = $rightNavRow->transfer_and_followups_form;
                $right_physician_amendments_form = $rightNavRow->physician_amendments_form;
                $right_injection_misc_form = $rightNavRow->injection_misc_form;
                switch ($datatype) {
                    case'PhysicianNotes':
                        if ($right_physician_amendments_form == 'false') {
                            $flag = '';
                            if ($AmendmentsNotesFormStatus == 'completed') {
                                $flag = 'green';
                            }
                            if ($AmendmentsNotesFormStatus == 'not completed') {
                                $flag = 'red';
                            }
                            $data[] = ["name" => "Amendments", "color" => $flag, 'pConfirmId' => $pConfirmId];
                        }
                        break;
                    case'CheckList':
                        if ($right_surgical_check_list_form == 'false') {
                            $showCheckListAdmin = false;
                            $showCheckList = (!$showCheckListAdmin && ($surgicalCheckListFormStatus == 'completed' || $surgicalCheckListFormStatus == 'not completed')) ? true : $showCheckListAdmin;
                            $showCheckListStatus = $this->getChartShowStatus($patient_confID, 'checklist');
                            $showCheckList = $showCheckListStatus ? ($showCheckListStatus == 1 ? true : ($showCheckListStatus == 2 ? false : $showCheckList)) : $showCheckList;
                            $flag = '';
                            if ($showCheckList) {
                                if ($surgicalCheckListFormStatus == 'completed') {
                                    $flag = 'green';
                                }

                                if ($surgicalCheckListFormStatus == 'not completed') {
                                    $flag = 'red';
                                }
                                $data[] = ["name" => "CheckList", "color" => $flag, 'pConfirmId' => $pConfirmId];
                            }
                        }
                        break;
                    case'PreOPHealthQues':
                        if ($right_pre_op_health_ques_form == 'false') {
                            $flagPRE = '';
                            if ($preopHealthQuestFormStatus == 'completed') {
                                $flagPRE = 'green';
                            }
                            if ($preopHealthQuestFormStatus == 'not completed') {
                                $flagPRE = 'red';
                            }
                            $data[] = ["name" => "Health Questionnaire", "color" => $flagPRE, 'pConfirmId' => $pConfirmId];
                        }
                        if ($right_history_physical_form == 'false') {
                            if ($historyPhysicalFormStatus == 'completed') {
                                $flagHST = 'green';
                            }
                            if ($historyPhysicalFormStatus == 'not completed') {
                                $flagHST = 'red';
                            }
                            $data[] = ["name" => "H & P Clearance", "color" => $flagHST, 'pConfirmId' => $pConfirmId];
                        }
                        break;
                    case'LaserProcedure':
                        if ($right_laser_procedure_form == 'false') {
                            $flagLP = '';
                            if ($laserProcedureFormStatus == 'completed') {
                                $flagLP = 'green';
                            }

                            if ($laserProcedureFormStatus == 'not completed') {
                                $flagLP = 'red';
                            }
                            $data[] = ["name" => "Laser Procedure", "color" => $flagLP, 'pConfirmId' => $pConfirmId];
                        }

                        break;
                    case'DischargeSummary':
                        if ($right_discharge_summary_form == 'false') {
                            $flag = '';
                            if ($dischargeSummaryFormStatus == 'completed') {
                                $flag = 'green';
                            }
                            if ($dischargeSummaryFormStatus == 'not completed') {
                                $flag = 'red';
                            }
                            $data[] = ["name" => "Discharge Summary", "color" => $flag, 'pConfirmId' => $pConfirmId];
                        }
                        break;
                    case'PostOPInstSheet':
                        if ($right_post_op_instruction_sheet_form == 'false') {
                            $flag = '';
                            if ($InstructionSheetFormStatus == 'completed') {
                                $flag = 'green';
                            }

                            if ($InstructionSheetFormStatus == 'not completed') {
                                $flag = 'red';
                            }

                            $data[] = ["name" => "Instruction Sheet", "color" => $flag, 'pConfirmId' => $pConfirmId]; //, ["name" => "Medication Reconciliation Sheet", "color" => "#D1E0C9"]];
                        }
                        break;
                    case'TransferandFollowup':
                        if ($right_transfer_and_followups_form == 'false') {
                            $flag = '';
                            if ($TransferFollowupsFormStatus == 'completed') {
                                $flag = 'green';
                            }
                            if ($TransferFollowupsFormStatus == 'not completed') {
                                $flag = 'red';
                            }
                            $data[] = ["name" => "Transfer and Followups", "color" => $flag, 'pConfirmId' => $pConfirmId]; //, ["name" => "Medication Reconciliation Sheet", "color" => "#D1E0C9"]];
                        }
                        break;
                    case'Anesthesia':
                        if ($right_mac_regional_anesthesia_form == 'false') {
                            $flag = '';
                            if ($macRegionalAnesthesiaFormStatus == 'completed') {
                                $flag = 'gree';
                            }

                            if ($macRegionalAnesthesiaFormStatus == 'not completed') {
                                $flag = 'red';
                            }

                            $data[] = ["name" => "MAC/Regional-" . $pConfirmId, "color" => $flag, 'pConfirmId' => $pConfirmId];
                        }

                        if ($right_pre_op_genral_anesthesia_form == 'false') {
                            $flag = '';
                            if ($preopGenralAnesthesiaFormStatus == 'completed') {
                                $flag = 'green';
                            }

                            if ($preopGenralAnesthesiaFormStatus == 'not completed') {
                                $flag = 'red"';
                            }

                            $data[] = ["name" => "Pre-Op General-" . $pConfirmId, "color" => $flag, 'pConfirmId' => $pConfirmId];
                        }
                        if ($right_genral_anesthesia_form == 'false') {
                            $flag = '';
                            if ($GenralAnesthesiaFormStatus == 'completed') {
                                $flag = 'green';
                            }
                            if ($GenralAnesthesiaFormStatus == 'not completed') {
                                $flag = 'red';
                            }
                            $data[] = ["name" => "General-" . $pConfirmId, "color" => $flag, 'pConfirmId' => $pConfirmId];
                        }
                        if ($right_genral_anesthesia_nurses_notes_form == 'false') {
                            $flag = '';
                            if ($genralAnesthesiaNursesNotesFormStatus == 'completed') {
                                $flag = 'green';
                            }
                            if ($genralAnesthesiaNursesNotesFormStatus == 'not completed') {
                                $flag = 'red';
                            }
                            $data[] = ["name" => "General Nurse Notes", "color" => $flag, 'pConfirmId' => $pConfirmId];
                        }
                        break;
                    case'NursingRecord':
                        if ($right_pre_op_nursing_form == 'false' || $right_post_op_nursing_form == 'false' || $right_pre_nurse_alderate_form == 'false' || $right_post_nurse_alderate_form == 'false') {
                            $flagPST = '';
                            $flagPRE = '';
                            $flagPRA = '';
                            $flagPSA = '';
                            $flagPSA = '';
                            if ($preopNursingFormStatus == 'completed') {
                                $flagPRE = 'green';
                            }

                            if ($preopNursingFormStatus == 'not completed') {
                                $flagPRE = 'red';
                            }

                            if ($preNurseAlderateFormStatus == 'completed') {
                                $flagPRA = 'green';
                            }

                            if ($preNurseAlderateFormStatus == 'not completed') {
                                $flagPRA = 'red';
                            }
                            if ($postopNursingFormStatus == 'completed') {
                                $flagPST = 'green';
                            }

                            if ($postopNursingFormStatus == 'not completed') {
                                $flagPST = 'red';
                            }

                            if ($postNurseAlderateFormStatus == 'completed') {
                                $flagPSA = 'green';
                            }

                            if ($postNurseAlderateFormStatus == 'not completed') {
                                $flagPSA = 'red';
                            }
                            //Pre-Op,Pre-Op-Aldrete,Post-Op,Post-Op-Aldrete
                            if ($right_pre_op_nursing_form == 'false') {
                                $data[] = ["name" => "Pre Op", "color" => $flagPRE, 'selecteddate' => $selecteddate, 'pConfirmId' => $pConfirmId];
                            }
                            if ($right_pre_nurse_alderate_form == 'false') {
                                $data[] = ["name" => "Pre-Op Aldrete", "color" => $flagPRA, 'selecteddate' => $selecteddate, 'pConfirmId' => $pConfirmId];
                            }
                            if ($right_post_op_nursing_form == 'false') {
                                $data[] = ["name" => "Post-Op", "color" => $flagPST, 'selecteddate' => $selecteddate, 'pConfirmId' => $pConfirmId];
                            }
                            if ($right_post_nurse_alderate_form == 'false') {
                                $data[] = ["name" => "Post-Op Aldrete", "color" => $flagPSA, 'selecteddate' => $selecteddate, 'pConfirmId' => $pConfirmId];
                            }
                        }
                        break;
                    case 'Intra-OP-Record':
                        $data[] = ["name" => "Intra-OP Record", "color" => "red", 'pConfirmId' => $pConfirmId];
                        break;
                    case 'PhysicianOrder':
                        if (($right_pre_op_physician_order_form == 'false') || ($right_post_op_physician_order_form == 'false')) {
                            $flagPRE = '';
                            if ($preopPhysicianFormStatus == 'completed') {
                                $flagPRE = 'green';
                            }
                            if ($preopPhysicianFormStatus == 'not completed') {
                                $flagPRE = 'red';
                            }
                            $flagPST = '';
                            if ($postopPhysicianFormStatus == 'completed') {
                                $flagPST = 'green';
                            }

                            if ($postopPhysicianFormStatus == 'not completed') {
                                $flagPST = 'red';
                            }
                        }
                        if ($right_pre_op_physician_order_form == 'false') {
                            $data[] = ["name" => "Pre-Op Order", "color" => $flagPRE, 'pConfirmId' => $pConfirmId];
                        }
                        if ($right_post_op_physician_order_form == 'false') {
                            $data[] = ["name" => "Post-Op Order", "color" => $flagPST, 'pConfirmId' => $pConfirmId];
                        }
                        break;
                    case 'ConsentForm':
                        $sampleKey = '';
                        $sampleTitle = '';
                        $category_title = [];
                        $category_names = [];
                        $flag = '';
                        $category = "select confirmation_id from `consent_multiple_form` WHERE  confirmation_id = '" . $pConfirmId . "' AND left_navi_status = 'false'";
                        $categoryRow = DB::select($category);
                        if ($categoryRow > 0) {
                            $categoryRightSelectQry = "select * from `consent_category`  order by category_id";
                            $categoryRightSelectRows = DB::select($categoryRightSelectQry);
                            foreach ($categoryRightSelectRows as $categoryRightSelectRow) {
                                $cat_id = $categoryRightSelectRow->category_id;
                                $category_name = str_replace(" ", "_", $categoryRightSelectRow->category_name);
                                $consentFormTemplateRightSelectQry = "select consent_id,consent_alias,consent_delete_status from `consent_forms_template` where consent_category_id='$cat_id' order by consent_id";
                                $consentFormTemplateRightSelectRes = DB::select($consentFormTemplateRightSelectQry);
                                $consentFormSelectRightConsentTemplateId = array();
                                $consentFormRightSelectCategoryQry = "select surgery_consent_id from `consent_multiple_form` WHERE  confirmation_id = '" . $pConfirmId . "' "
                                        . "AND consent_category_id ='" . $cat_id . "' AND left_navi_status='false'";
                                $consentFormRightSelectCategoryRes = DB::select($consentFormRightSelectCategoryQry);
                                if ($consentFormRightSelectCategoryRes) {
                                    if ($consentFormTemplateRightSelectRes) {
                                        foreach ($consentFormTemplateRightSelectRes as $consentFormTemplateRightSelectRow) {
                                            $consentFormSelectRightConsentTempleteId = $consentFormTemplateRightSelectRow->consent_id;
                                            $consentFormSelectRightConsentTempleteAlias = $consentFormTemplateRightSelectRow->consent_alias;
                                            $consentFormRightSelectQry = "select * from `consent_multiple_form` WHERE  confirmation_id = '" . $pConfirmId . "'
                                                				AND consent_template_id='" . $consentFormSelectRightConsentTempleteId . "'";
                                            $consentFormRightSelectRes = DB::select($consentFormRightSelectQry);
                                            if ($consentFormRightSelectRes) {
                                                $seqArrNew = 0;
                                                foreach ($consentFormRightSelectRes as $consentFormRightSelectRow) {
                                                    $seqArrNew++;
                                                    $consentFormSelectRightConsentAlias = $consentFormRightSelectRow->surgery_consent_alias;
                                                    $selectedConsentFormAutoIncrId = $consentFormRightSelectRow->surgery_consent_id;
                                                    $selectedConsentFormStatus = $consentFormRightSelectRow->form_status;
                                                    $selectedConsentFormleftStatus = $consentFormRightSelectRow->left_navi_status;
                                                    $selectedConsentFormPurgeStatus = $consentFormRightSelectRow->consent_purge_status;
                                                    if ($selectedConsentFormPurgeStatus) {
                                                        $strikeConsentPurgeRight = "background-image:url(images/strike_image.jpg); background-repeat:repeat-x; background-position:center;";
                                                    } else {
                                                        $strikeConsentPurgeRight = "";
                                                    }
                                                    if ($consentFormSelectRightConsentAlias == "") {
                                                        $consentFormSelectRightConsentAlias = $consentFormSelectRightConsentTempleteAlias;
                                                    }
                                                    if (($consentFormSelectRightConsentAlias) && ($selectedConsentFormleftStatus == 'false')) {
                                                        if ($selectedConsentFormStatus == 'completed') {
                                                            $flag = 'green';
                                                        }
                                                        if ($selectedConsentFormStatus == 'not completed') {
                                                            $flag = 'red';
                                                        }
                                                        $category_names[] = $category_name;
                                                        $sampleKey.=$category_name . ',';
                                                        $category_title[] = $categoryRightSelectRow->category_name;
                                                        $sampleTitle.=$categoryRightSelectRow->category_name . ',';
                                                        $ControlledKey[$category_name][] = ['id' => $selectedConsentFormAutoIncrId, "color" => $flag, "subcat" => stripslashes(ucfirst($consentFormSelectRightConsentAlias)), 'FormStatus' => $selectedConsentFormStatus];
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $category_names = array_unique($category_names);
                        $category_title = array_unique($category_title);
                        $data = ["name" => 'Consent Form', "sample_key" => implode(',', $category_names), 'sample_title' => implode(",", $category_title), "color" => $flag, 'chart_data' => $ControlledKey, 'pConfirmId' => $pConfirmId];
                        break;
                    default:
                        $document_name = '';
                        $scanFolderDetailLists = DB::select("select * from scan_documents where /*confirmation_id='" . $pConfirmId . "' and*/ dosOfScan='" . $selecteddate . "' order by document_id desc");
                        if ($scanFolderDetailLists) {
                            $url = getenv('APP_URL') . '/' . getenv('surgeryCenterDirectoryName') . '/admin/';
                            foreach ($scanFolderDetailLists as $FolderList) {
                                $document_id = $FolderList->document_id;
                                $document_name = $FolderList->document_name;
                                if ($document_name == 'Anesthesia Consent') {
                                    $document_name = 'A.Consent';
                                }
                                $document_name = str_replace('.', '_', $document_name);
                                $title_key[] = str_replace(' ', '_', $document_name);
                                $sampleKey[] = str_replace(' ', '_', $document_name);
                                $getImagesOrNotDetails = DB::select("select scan_upload_id as delId,document_name as `title`, concat('" . $url . "',pdfFilePath) as url,dosOfScan from scan_upload_tbl where /*confirmation_id='" . $pConfirmId . "' and*/ document_id='" . $document_id . "' order by dosOfScan desc");
                                //$data[]=['Date'=>date("m/d/Y",strtotime($FolderList->dosOfScan))]
                                $ControlledKey[str_replace(' ', '_', $document_name)][] = $getImagesOrNotDetails;
                            }
                            $sampleKey = array_unique($sampleKey);
                            $title_key = array_unique($title_key);
                        }

                        $data = ['sample_key' => @implode(",", $sampleKey), 'title_key' => @implode(",", $title_key), 'chart_data' => ['ScanDocuments' => $ControlledKey], 'pConfirmId' => $pConfirmId];
                }
                $status = 1;
                $message = " List ";
            }
        }
        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'data' => $data,
                    "listStatus" => $listStatus,
                    'requiredStatus' => '',
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

}
