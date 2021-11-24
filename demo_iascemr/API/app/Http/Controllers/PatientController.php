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

class PatientController extends Controller {

    /**
     * Save the Patient infoDetails.
     * @return Response
     */
    public function savePatientDetails(Request $request) {
        $updateStatus = 0;
        $message = "";
        $userToken = $request->input('user_token');
        $pConfirmId = $request->input('pConfirmId');
        $patient_id = $request->input('patient_id');
        $reConfirmId = $request->input('reConfirmId');
        $imwPatientId = $request->input('imwPatientId');
        $stub_id = $request->input('ptStubId');
        $patient_fname = $request->input('patient_fname');
        $patient_mname = $request->input('patient_mname');
        $patient_lname = $request->input('patient_lname');
        $patient_dob = $request->input('patient_dob');
        $patient_gender = $request->input('patient_gender');
        $patient_status = $request->input('patient_status');
        $chkInOutTime = $request->input('checked_in_out_time');
        $advance_directive = $request->input('advance_directive');
        $patient_street1 = $request->input('patient_street1');
        $patient_street2 = $request->input('patient_street2');
        $patient_city = $request->input('patient_city');
        $patient_state = $request->input('patient_state');
        $patient_zip = $request->input('patient_zip');
        $home_phone = $request->input('home_phone');
        $work_phone = $request->input('work_phone');
        $discharge_status = $request->input('discharge_status');
        $no_publicity = $request->input('no_publicity');
        $surgeon_id = $request->input('surgeon_id');
        $surgeon_name = $request->input('surgeon_name');
        $dos = $request->input('dos');
        $assisted_by_translator = $request->input('assisted_by_translator');
        $PrimaryProcedure_id = $request->input('primary_procedure_id');
        $primary_procedure = $request->input('primary_procedure');
        $primary_site = $request->input('primary_site');
        $SecondaryProcedure_id = $request->input('secondary_procedure_id');
        $secondary_procedure = $request->input('secondary_procedure');
        $secondary_site = $request->input('secondary_site');
        $TertiaryProcedure_id = $request->input('tertiary_procedure_id');
        $tertiary_procedure = $request->input('tertiary_procedure');
        $tertiary_site = $request->input('tertiary_site');
        $anes_id = $request->input('anes_id');
        $anes_name = $request->input('anes_name');
        $checked_in_by = $request->input('checked_in_by');
        $checked_in_by_id = $request->input('checked_in_by_id');
        $language = $request->input('language');
        $ascId_hidden = $request->input('ascId_hidden');
        if ($language == 'Other') {
            $other_language = $request->input('other_box_language');
            $language = "Other -- " . $other_language;
        }
        $race = $request->input('race');
        $ethnicity = $request->input('ethnicity');
        $religion = $request->input('religion');

        $data = [];
        $message = " unauthorized ";
        $docFolderRecordsStr = "";
        $status = 0;
        ini_set("display_errors", 1);
        if ($this->checkToken($userToken)) {
            $andstubIdQry = " ";
            if (!$pConfirmId) {
                $andstubIdQry = " AND stub_id = '" . $stub_id . "' AND stub_id != '0' ";
            }
            $ptOverride = false;
            if ($surgeon_id == 0) {
                $message = " Surgeon is required ";
                $status = 1;
                $requiredStatus = 0;
            } elseif ($patient_id == 0 || trim($patient_id) == "") {
                $message = " PatientId is required ";
                $status = 1;
                $requiredStatus = 0;
            } elseif ($stub_id == 0 || trim($stub_id) == "") {
                $message = " StubId is required ";
                $status = 1;
                $requiredStatus = 0;
            } elseif ($patient_status == "Checked-In" && $chkInOutTime == "") {
                $message = " Checked-In time is required ";
                $status = 1;
                $requiredStatus = 0;
            } elseif ($patient_status == "Checked-Out" && $discharge_status == "") {
                $message = " Discharge status is required ";
                $status = 1;
                $requiredStatus = 0;
            } elseif ($anes_id == "" || $anes_id == 0) {
                $message = " Anesthesiologist name is required ";
                $status = 1;
                $requiredStatus = 0;
            } elseif ($PrimaryProcedure_id == "") {
                $message = " Primary procedure is required ";
                $status = 1;
                $requiredStatus = 0;
            } elseif ($primary_site == "") {
                $message = " Primary site is required ";
                $status = 1;
                $requiredStatus = 0;
            } elseif ($checked_in_by_id == 0 || $checked_in_by_id == "") {
                $message = " Checked-In By is required ";
                $status = 1;
                $requiredStatus = 0;
            } else {
                $patient_dob_split = explode("-", $patient_dob);
                $patient_dob = $patient_dob_split[2] . "-" . $patient_dob_split[0] . "-" . $patient_dob_split[1];
                $dos_split = explode("-", $dos);
                $dos = $dos_split[2] . "-" . $dos_split[0] . "-" . $dos_split[1];
                $anes_NA = '';
                if ($anes_id == '0' || $anes_id == '') {
                    $anes_name = 'N/A';
                    $anes_NA = 'Yes';
                }

                // INSERT PATIENT DATA TABLE
                //$arrayPatientRecord['asc_id'] = $ascId;
                $arrayPatientRecord['patient_fname'] = trim(addslashes($patient_fname));
                $arrayPatientRecord['patient_mname'] = trim(addslashes($patient_mname));
                $arrayPatientRecord['patient_lname'] = trim(addslashes($patient_lname));
                $arrayPatientRecord['street1'] = trim(addslashes($patient_street1));
                $arrayPatientRecord['street2'] = trim(addslashes($patient_street2));
                $arrayPatientRecord['city'] = trim(addslashes($patient_city));
                $arrayPatientRecord['state'] = trim($patient_state);
                $arrayPatientRecord['zip'] = trim($patient_zip);
                $arrayPatientRecord['date_of_birth'] = $patient_dob;
                $arrayPatientRecord['sex'] = $patient_gender;
                $arrayPatientRecord['homePhone'] = trim(addslashes($home_phone));
                $arrayPatientRecord['workPhone'] = trim(addslashes($work_phone));
                $arrayPatientRecord['imwPatientId'] = $imwPatientId;
                $arrayPatientRecord["language"] = trim(addslashes($language));
                $arrayPatientRecord["race"] = $race;
                $arrayPatientRecord["ethnicity"] = $ethnicity;
                $arrayPatientRecord["religion"] = addslashes($religion);

                if (trim($patient_id) > 0) {
                    $patientMatchStr = "SELECT patient_id FROM patient_data_tbl WHERE patient_id = '" . trim($patient_id) . "'";
                } else {
                    $patientMatchStr = "SELECT patient_id FROM patient_data_tbl WHERE imwPatientId = '" . $imwPatientId . "' and imwPatientId!=''";
                }
                $patientMatchQry = DB::select($patientMatchStr);
                if (!$patientMatchQry) {
                    $patientMatchStr = "SELECT patient_id FROM patient_data_tbl WHERE patient_fname = '" . trim(addslashes($patient_fname)) . "' AND patient_lname 	= '" . trim(addslashes($patient_lname)) . "' AND zip 		  	= '" . trim(addslashes($patient_zip)) . "' AND date_of_birth 	= '" . $patient_dob . "'";
                    $patientMatchQry = DB::select($patientMatchStr);
                }
                if ($patientMatchQry) {
                    DB::table('patient_data_tbl')->where('patient_id', $patientMatchQry[0]->patient_id)->update($arrayPatientRecord);
                    $insertPatientDataId = $patientMatchQry[0]->patient_id;
                }
                $insertPatientDataId = (isset($insertPatientDataId) && $insertPatientDataId > 0) ? $insertPatientDataId : $patient_id;
                //END GET PATIENT-ID IF PREVIOUSLY EXIST IN STUB-TABLE
                //END CODE TO ADD PATIENT PHOTO
                // Chk if patient already confirmed 
                if (!$reConfirmId) {
                    $chkPatientAlreadyConfirmedQry = "SELECT pc.patientConfirmationId FROM patientconfirmation pc
                  INNER JOIN stub_tbl st ON(st.patient_confirmation_id=pc.patientConfirmationId AND st.stub_id = '" . $stub_id . "')
                  WHERE pc.patientId='$insertPatientDataId'";
                    $chkPatientAlreadyConfirmedRes = DB::select($chkPatientAlreadyConfirmedQry);
                    if ($chkPatientAlreadyConfirmedRes) {
                        $reConfirmId = $chkPatientAlreadyConfirmedRes[0]->patientConfirmationId;
                        $ptOverride = true;
                    }
                }
                //START CODE TO UPDATE PATIENT-ID IN STUB-TABLE
                unset($arrayStubRecord);
                $arrayStubRecord['patient_id_stub'] = $insertPatientDataId;
                $arrayStubRecord['patient_first_name'] = trim(addslashes($patient_fname));
                $arrayStubRecord['patient_middle_name'] = trim(addslashes($patient_mname));
                $arrayStubRecord['patient_last_name'] = trim(addslashes($patient_lname));
                $arrayStubRecord['patient_street1'] = trim(addslashes($patient_street1));
                $arrayStubRecord['patient_street2'] = trim(addslashes($patient_street2));
                $arrayStubRecord['patient_city'] = trim(addslashes($patient_city));
                $arrayStubRecord['patient_state'] = trim($patient_state);
                $arrayStubRecord['patient_zip'] = trim($patient_zip);
                $arrayStubRecord['patient_dob'] = $patient_dob;
                $arrayStubRecord['patient_sex'] = $patient_gender;
                $arrayStubRecord['patient_home_phone'] = trim(addslashes($home_phone));
                $arrayStubRecord['patient_work_phone'] = trim(addslashes($work_phone));
                $arrayStubRecord["patient_language"] = trim(addslashes(str_replace("Other -- ", "", $language)));
                DB::table('stub_tbl')->where('stub_id', $stub_id)->update($arrayStubRecord);
                $stub_qry = "select * from stub_tbl where stub_id='" . $stub_id . "'";
                $stubTableDetailss = DB::select($stub_qry);

                foreach ($stubTableDetailss as $stubTableDetails) {
                    $patient_dos_temp = $dos; //$stubTableDetails->dos;
                    $patient_site = $primary_site; //$stubTableDetails->site;
                    $sec_patient_site = $secondary_site; //$stubTableDetails->stub_secondary_site;
                    $ter_patient_site = $tertiary_site; // $stubTableDetails->stub_tertiary_site;
                    $iolink_patient_in_waiting_id = $stubTableDetails->iolink_patient_in_waiting_id;

                    $arrayRecord['patientId'] = $insertPatientDataId;
                    $arrayRecord['dos'] = $patient_dos_temp; //date("Y-m-d", strtotime($dos));
                    $arrayRecord['surgery_time'] = $stubTableDetails->surgery_time;
                    $arrayRecord['pickup_time'] = $stubTableDetails->pickup_time;
                    $arrayRecord['arrival_time'] = $stubTableDetails->arrival_time;
                    $arrayRecord['ascId'] = $ascId_hidden;
                    $arrayRecord['assist_by_translator'] = $assisted_by_translator;
                    $arrayRecord['advanceDirective'] = $advance_directive;
                    $arrayRecord['discharge_status'] = (int) $discharge_status;
                    $arrayRecord['no_publicity'] = $no_publicity;
                    $arrayRecord['patient_primary_procedure'] = addslashes($primary_procedure);
                    $arrayRecord['patient_primary_procedure_id'] = $PrimaryProcedure_id ? $PrimaryProcedure_id : 0;
                    $arrayRecord['patient_secondary_procedure'] = addslashes($secondary_procedure);
                    $arrayRecord['patient_secondary_procedure_id'] = $SecondaryProcedure_id ? $SecondaryProcedure_id : 0;
                    $arrayRecord['patient_tertiary_procedure'] = addslashes($tertiary_procedure);
                    $arrayRecord['patient_tertiary_procedure_id'] = $TertiaryProcedure_id ? $TertiaryProcedure_id : 0;
                    $arrayRecord['site'] = $primary_site ? $primary_site : 0;
                    $arrayRecord['secondary_site'] = $secondary_site ? $secondary_site : 0;
                    $arrayRecord['secondary_site_description'] = $secondary_site;
                    $arrayRecord['tertiary_site'] = $tertiary_site ? $tertiary_site : 0;

                    $arrayRecord['tertiary_site_description'] = $tertiary_site;
                    $arrayRecord['zip'] = trim($patient_zip);
                    $arrayRecord['surgeonId'] = $surgeon_id ? $surgeon_id : 0;
                    $arrayRecord['surgeon_name'] = addslashes(trim($surgeon_name));
                    $arrayRecord['anes_NA'] = trim($anes_NA);
                    $arrayRecord['anesthesiologist_name'] = addslashes(trim($anes_name));
                    $arrayRecord['anesthesiologist_id'] = (int) $anes_id;
                    $arrayRecord['confirm_nurse'] = addslashes(trim($checked_in_by));
                    $arrayRecord['nurseId'] = (int) $checked_in_by_id;
                    $arrayRecord['patientStatus'] = $patient_status;
                    $arrayRecord['dateConfirmation'] = date("Y-m-d H:i:s");
                    $arrayRecord['imwPatientId'] = $imwPatientId;
                    if ($reConfirmId > 0) {
                        DB::table('patientconfirmation')->where('patientConfirmationId', $reConfirmId)->update($arrayRecord);
                        $insertConfirmationId = $reConfirmId;
                        /* Code to update procedure category if update */
                        $isInjMiscProc = $this->verifyProcIsInjMisc($PrimaryProcedure_id);
                        $procCatUpdateQry = "update patientconfirmation set prim_proc_is_misc = '" . $isInjMiscProc . "' where patientConfirmationId = '" . $insertConfirmationId . "' And prim_proc_is_misc <> '' ";
                        DB::select($procCatUpdateQry);
                        /* End Code to update procedure category if update */
                    } else {
                        //CHECK IF PATIENT ALREADY CONFIRMED (IF NOT THEN INSERT NEW ENTRY TO CONFIRM PATIENT)
                        $chkPatientAlreadyConfirmedQry = "SELECT pc.patientConfirmationId FROM patientconfirmation pc INNER JOIN stub_tbl st ON(st.patient_confirmation_id=pc.patientConfirmationId AND st.stub_id = '" . $stub_id . "') WHERE pc.patientId='$insertPatientDataId'";
                        $chkPatientAlreadyConfirmedNumRow = DB::select($chkPatientAlreadyConfirmedQry);
                        if ($chkPatientAlreadyConfirmedNumRow) {
                            //DO NOTHING
                        } else { //(IF NOT ALREADY CONFIRMED THEN INSERT NEW ENTRY TO CONFIRM PATIENT)
                            $insertConfirmationId = DB::table('patientconfirmation')->insertGetId($arrayRecord);
                        }
                        //CHECK IF PATIENT ALREADY CONFIRMED
                    }
                    $stub_patient_primary_procedure = addslashes($primary_procedure);
                    $stub_patient_secondary_procedure = addslashes($secondary_procedure);
                    $stub_patient_tertiary_procedure = addslashes($tertiary_procedure);
                    if (trim($stub_patient_primary_procedure) <> "") {
                        $updtpatient_primary_procedure = "patient_primary_procedure = '$stub_patient_primary_procedure',";
                    }
                    //if(trim($stub_patient_secondary_procedure)<>"") {
                    $updtpatient_secondary_procedure = "patient_secondary_procedure = '$stub_patient_secondary_procedure',";
                    //}
                    //if(trim($stub_patient_tertiary_procedure)<>"") {
                    $updtpatient_tertiary_procedure = "patient_tertiary_procedure = '$stub_patient_tertiary_procedure',";
                    //}
                    //END UPDATE PRIMARY PROCEDURE IN STUB TABLE 
                    //SET PATIENT SITE IN STUB TABLE 
                    $updtSecPatientSite = "";
                    $updtPatientSite = "";
                    $updtTerPatientSite = "";
                    if ($patient_site) {
                        $updtPatientSite = "site = '$patient_site',";
                    }
                    if ($sec_patient_site) {
                        $updtSecPatientSite = " stub_secondary_site = '$sec_patient_site', ";
                    }
                    if ($ter_patient_site) {
                        $updtTerPatientSite = " stub_tertiary_site = '$ter_patient_site', ";
                    }
                    //END SET PATIENT SITE IN STUB TABLE 
                    $updtChkInTime = '';
                    $updtChkOutTime = '';
                    //  $chkInOutTime = date("H:i:s");
                    if (trim($chkInOutTime)) {
                        //$chkInOutTime = $_REQUEST['checkInOutTime'];
                        $chkInOutTime = $chkInOutTime;
                    }
                    if ($patient_status == 'Checked-In') {
                        $updtChkInTime = "checked_in_time = '" . $chkInOutTime . "', ";
                    } else if ($patient_status == 'Checked-Out') {
                        $updtChkOutTime = "checked_out_time = '" . $chkInOutTime . "', recentChartSaved = '', ";
                    }
                    $firstTimeCheckIn = false;
                    if (isset($ascId_hidden) && $ascId_hidden <> "") { //SET PATIENT STATUS TO CHECKED-IN AT ALLOCATION OF ASC-ID
                        // UPDATE STATUS IN STUB TABLE 
                        $update_stub_status_qry = "update `stub_tbl` set 
                                                    patient_status = '" . $patient_status . "', 
                                                    $updtChkInTime
                                                    $updtChkOutTime
                                                    $updtpatient_primary_procedure
                                                    $updtpatient_secondary_procedure
                                                    $updtpatient_tertiary_procedure
                                                    $updtPatientSite
                                                    $updtSecPatientSite
                                                    $updtTerPatientSite
                                                    patient_confirmation_id = '$insertConfirmationId'
                                                    WHERE stub_id = '" . $stub_id . "'";
                        $update_stub_status_res = DB::select($update_stub_status_qry);
                        $firstTimeCheckIn = true;
                        // END UPDATE SCAN DOCUMENTS, SCAN UPLOAD 
                    } else {
                        $update_stub_status_qry = "update `stub_tbl` set 
                                                    patient_status = '" . $patient_status . "', 
                                                    $updtChkInTime
                                                    $updtChkOutTime
                                                    $updtpatient_primary_procedure
                                                    $updtpatient_secondary_procedure
                                                    $updtpatient_tertiary_procedure
                                                    $updtPatientSite
                                                    $updtSecPatientSite
                                                    $updtTerPatientSite
                                                    patient_confirmation_id = '$insertConfirmationId'
                                                    WHERE stub_id = '" . $stub_id . "'";
                    }

                    $update_stub_status_res = DB::select($update_stub_status_qry);
                    //update cheklist form show status at first check in 

                    if ($firstTimeCheckIn) {
                        $querys = "SELECT  safety_check_list FROM `surgerycenter` limit 1";
                        $re = DB::select($querys);
                        $adminShowCheckList = $re[0]->safety_check_list;
                        //get current saved status from check list 
                        $checklistShowStatus = $this->getChartShowStatus($insertConfirmationId, 'checklist');
                        if (!$checklistShowStatus) {

                            //get current form status for checklist page
                            $checklistStatusQry = "Select form_status From surgical_check_list where confirmation_id = '" . (int) $insertConfirmationId . "' ";
                            $checklistStatusRes = DB::select($checklistStatusQry);
                            if ($checklistStatusRes)
                            //    $checklistStatusRes = imw_fetch_assoc($checklistStatusSql);
                                $checklistFormStatus = $checklistStatusRes[0]->form_status;

                            //1 to show checklist page
                            //2 to hide checklist page
                            $checklistStatus = ($adminShowCheckList == 1) ? 1 : 2;
                            $checkliststatus = ($checklistStatus <> 1 && ($checklistFormStatus == 'completed' || $checklistFormStatus == 'not completed' )) ? 1 : $checklistStatus;

                            // Add check list show status into confirmation table
                            $checklistShowStatusUpQry = "Update patientconfirmation Set show_checklist = '" . $checkliststatus . "' Where patientConfirmationId = '" . (int) $insertConfirmationId . "' ";
                            $checklistShowStatusUpSql = DB::select($checklistShowStatusUpQry);
                        }
                    }
                    //update cheklist form show status at first check in

                    /*                     * *********ADDING CODE TO SEND SIU FOR CHECK-IN EVENT******** */
                    if ($firstTimeCheckIn && $update_stub_status_res) {

                        if (getenv('HL7_SIU_GENERATION') === true && getenv('imwVer') === 'R8' && getenv('imwPracticeName')) {
                            $patient_in_waiting_id_bk = $iolink_patient_in_waiting_id;

                            $sqlIdocApptId = "SELECT `appt_id`,`imwPatientId` FROM `stub_tbl` WHERE `stub_id`='" . $stub_id . "' LIMIT 1";
                            $respIdocApptId = DB::select($sqlIdocApptId);
                            if ($respIdocApptId) {
                                $idocApptId = $respIdocApptId[0]->appt_id;
                                $idocPatId = $respIdocApptId[0]->imwPatientId;
                                $URIbk = $_SERVER['REQUEST_URI'];
                                $_SERVER['REQUEST_URI'] = getenv('imwPracticeURL');
                                $ignoreAuth = true;
                                // include('connect_imwemr.php');

                                $curlFields = array();
                                $curlFields['MsgType'] = 'SIU';
                                $curlFields['PatId'] = $idocPatId;
                                $curlFields['SchId'] = $idocApptId;
                                $curlFields['SubMsgType'] = 13;

                                $url = getenv('imwPracticeURL') . '/hl7sys/api/index.php';
                                $cur = curl_init();
                                curl_setopt($cur, CURLOPT_URL, $url);
                                curl_setopt($cur, CURLOPT_SSL_VERIFYHOST, false);
                                curl_setopt($cur, CURLOPT_SSL_VERIFYPEER, false);
                                curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true);
                                curl_setopt($cur, CURLOPT_POSTFIELDS, $curlFields);
                                $data = curl_exec($cur);
                                if (curl_errno($cur)) {
                                    //	die("Curl Error (HL7): " . curl_error($cur));
                                }
                                curl_close($cur);
                                $_SERVER['REQUEST_URI'] = $URIbk;
                                unset($URIbk, $makeHL7);
                                //  include('common/conDb.php');
                            }
                        }

                        /* END HL7 SIU message for the added Patient */
                        $patient_in_waiting_id = $patient_in_waiting_id_bk; /* Backup waiting ID */
                        unset($patient_in_waiting_id_bk);
                    }
                    /*                     * *************HL7 CODE END*********************************** */

                    if (!$reConfirmId || ($reConfirmId && $ptOverride = true )) { // IF PATIENT IS NOT RE-CONFIRMING THEN RUN THIS CODE
                        $update_scan_upload_qry = "update `scan_upload_tbl` set 
                                                    confirmation_id = '$insertConfirmationId' 
                                                    WHERE patient_id = '$insertPatientDataId'
                                                    AND confirmation_id = '0'
                                                    AND dosOfScan = '$patient_dos_temp'
                                                    " . $andstubIdQry;
                        $update_scan_upload_res = DB::select($update_scan_upload_qry);
                        //END UPDATE SCAN DOCUMENTS, SCAN UPLOAD
                        //INSERT NEW ENTRY OF SCAN DOCUMENT WITH PATIENT ID 

                        $chk_insert_scan_document_qry1 = "select document_id from scan_documents where document_name = 'Pt. Info' AND patient_id = '$insertPatientDataId' AND confirmation_id = '0' AND dosOfScan = '$patient_dos_temp' " . $andstubIdQry;
                        $chk_insert_scan_document_res1 = DB::select($chk_insert_scan_document_qry1);
                        //$chk_insert_scan_document_numrow1 = imw_num_rows($chk_insert_scan_document_res1);
                        if ($chk_insert_scan_document_res1) {

                            $update_scan_document_qry1 = "update `scan_documents` set 
                                                            confirmation_id = '$insertConfirmationId' 
                                                            WHERE patient_id = '$insertPatientDataId'
                                                            AND document_name = 'Pt. Info'
                                                            AND confirmation_id = '0'
                                                            AND dosOfScan = '$patient_dos_temp'
                                                            " . $andstubIdQry;
                            $update_scan_document_res1 = DB::select($update_scan_document_qry1);
                        } else {
                            $chk_update_scan_document_qry1 = "select document_id from scan_documents where document_name = 'Pt. Info' AND patient_id = '" . $insertPatientDataId . "' AND confirmation_id = '" . $insertConfirmationId . "'";
                            $chk_update_scan_document_res1 = DB::select($chk_update_scan_document_qry1);

                            if ($chk_update_scan_document_res1) {
                                $arr = array("document_name" => 'Pt. Info',
                                    "patient_id" => $insertPatientDataId,
                                    "dosOfScan" => $patient_dos_temp,
                                    "confirmation_id" => $insertConfirmationId,
                                    "stub_id" => $stub_id);
                                $insert_scan_document_res1 = DB::table('scan_documents')->insertGetId($arr);

                                //TEMPRARY INSERT LOG OF SCAN FOLDER WITH DATETIME
                                $insert_scan_log_qry1 = "insert into `scan_log_tbl` set 
                                                            document_id = '" . $insert_scan_document_res1 . "',
                                                            document_name = 'Pt. Info',
                                                            patient_id = '$insertPatientDataId',
                                                            confirmation_id = '$insertConfirmationId',
                                                            document_date_time = '" . date('Y-m-d H:i:s') . "',
                                                            document_file_name = 'patient_confirm.php',
                                                            document_encounter = 'pt_info_1',
                                                            stub_id = '" . $stub_id . "'
                                                            ";
                                $insert_scan_log_res1 = DB::select($insert_scan_log_qry1);
                                //TEMPRARY INSERT LOG OF SCAN FOLDER WITH DATETIME
                            }
                        }
                        $chk_insert_scan_document_qry2 = "select document_id from scan_documents where document_name = 'Clinical' AND patient_id = '$insertPatientDataId' AND confirmation_id = '0' AND dosOfScan = '$patient_dos_temp'";
                        $chk_insert_scan_document_res2 = DB::select($chk_insert_scan_document_qry2);
                        // $chk_insert_scan_document_numrow2 = imw_num_rows($chk_insert_scan_document_res2);
                        if ($chk_insert_scan_document_res2) {

                            $update_scan_document_qry2 = "update `scan_documents` set 
                                                            confirmation_id = '$insertConfirmationId' 
                                                            WHERE patient_id = '$insertPatientDataId'
                                                            AND document_name = 'Clinical'
                                                            AND confirmation_id = '0'
                                                            AND dosOfScan = '$patient_dos_temp'
                                                            " . $andstubIdQry;
                            $update_scan_document_res2 = DB::select($update_scan_document_qry2);
                        } else {

                            $chk_update_scan_document_qry2 = "select document_id from scan_documents where document_name = 'Clinical' AND patient_id = '" . $insertPatientDataId . "' AND confirmation_id = '" . $insertConfirmationId . "' " . $andstubIdQry;
                            $chk_update_scan_document_res2 = DB::select($chk_update_scan_document_qry2);
                            if (!$chk_update_scan_document_res2) {
                                $arr2 = array("document_name" => 'Clinical',
                                    "patient_id" => $insertPatientDataId,
                                    "dosOfScan" => $patient_dos_temp,
                                    "confirmation_id" => $insertConfirmationId,
                                    "stub_id" => $stub_id);
                                $insert_scan_document_res2 = DB::table('scan_documents')->insertGetId($arr2);
                                //TEMPRARY INSERT LOG OF SCAN FOLDER WITH DATETIME
                                $insert_scan_log_qry2 = "insert into `scan_log_tbl` set 
                                                            document_id = '" . $insert_scan_document_res2 . "',
                                                            document_name = 'Clinical',
                                                            patient_id = '$insertPatientDataId',
                                                            confirmation_id = '$insertConfirmationId',
                                                            document_date_time = '" . date('Y-m-d H:i:s') . "',
                                                            document_file_name = 'patient_confirm.php',
                                                            document_encounter = 'clinical_1',
                                                            stub_id = '" . $stub_id . "'
                                                            ";
                                $insert_scan_log_res2 = DB::select($insert_scan_log_qry2);
                                //TEMPRARY INSERT LOG OF SCAN FOLDER WITH DATETIME
                            }
                        }

                        $chk_insert_scan_document_qry3 = "select document_id from scan_documents where document_name = 'IOL' AND patient_id = '$insertPatientDataId' AND confirmation_id = '0' AND dosOfScan = '$patient_dos_temp'";
                        $chk_insert_scan_document_res3 = DB::select($chk_insert_scan_document_qry3);
                        if ($chk_insert_scan_document_res3) {
                            $update_scan_document_qry3 = "update `scan_documents` set 
                                                                confirmation_id = '$insertConfirmationId' 
                                                                WHERE patient_id = '$insertPatientDataId'
                                                                AND document_name = 'IOL'
                                                                AND confirmation_id = '0'
                                                                AND dosOfScan = '$patient_dos_temp'
                                                                " . $andstubIdQry;
                            DB::select($update_scan_document_qry3);
                            //$update_scan_document_res3 = imw_query($update_scan_document_qry3) or die(imw_error());
                        } else {

                            $chk_update_scan_document_qry3 = "select document_id from scan_documents where document_name = 'IOL' AND patient_id = '" . $insertPatientDataId . "' AND confirmation_id = '" . $insertConfirmationId . "' " . $andstubIdQry;
                            $chk_update_scan_document_res3 = DB::select($chk_update_scan_document_qry3);
                            if (!$chk_update_scan_document_res3) {
                                $arr3 = array("document_name" => 'IOL',
                                    "patient_id" => $insertPatientDataId,
                                    "dosOfScan" => $patient_dos_temp,
                                    "confirmation_id" => $insertConfirmationId,
                                    "stub_id" => $stub_id);
                                $insert_scan_document_res3 = DB::table('scan_documents')->insertGetId($arr3);
                                //TEMPRARY INSERT LOG OF SCAN FOLDER WITH DATETIME
                                $insert_scan_log_qry3 = "insert into `scan_log_tbl` set 
                                                        document_id = '" . $insert_scan_document_res3 . "',
                                                        document_name = 'IOL',
                                                        patient_id = '$insertPatientDataId',
                                                        confirmation_id = '$insertConfirmationId',
                                                        document_date_time = '" . date('Y-m-d H:i:s') . "',
                                                        document_file_name = 'patient_confirm.php',
                                                        document_encounter = 'iol_1',
                                                        stub_id = '" . $stub_id . "'
                                                        ";
                                $insert_scan_log_res3 = DB::select($insert_scan_log_qry3);
                                //TEMPRARY INSERT LOG OF SCAN FOLDER WITH DATETIME
                            }
                        }

                        $scnFolderArr = array('H&P', 'EKG', 'Health Questionnaire', 'Ocular Hx', 'Consent');
                        foreach ($scnFolderArr as $scnFldNme) {
                            $update_scan_document_qry3 = "update `scan_documents` set 
                                                                confirmation_id = '" . $insertConfirmationId . "' 
                                                                WHERE patient_id = '" . $insertPatientDataId . "'
                                                                AND document_name = '" . $scnFldNme . "'
                                                                AND confirmation_id = '0'
                                                                AND dosOfScan = '" . $patient_dos_temp . "'
                                                                " . $andstubIdQry;
                            $update_scan_document_res3 = DB::select($update_scan_document_qry3);
                        }


                        //END INSERT NEW ENTRY OF SCAN DOCUMENT WITH PATIENT ID
                        // UPDATE EPOST-IT
                        $update_epost_qry = "update `eposted` set 
                                                patient_conf_id = '$insertConfirmationId' 
                                                WHERE patient_id = '$insertPatientDataId'
                                                AND patient_conf_id = '0'
                                                " . $andstubIdQry;
                        $update_epost_res = DB::select($update_epost_qry);
                        //END UPDATE EPOST-IT
                    }
                    $message = "Record updated success fully ";
                    $status = 1;
                    $updateStatus = 1;
                    //patientconfirmation table
                    //END REDIRECTS TO SCHEDULER PAGE
                }
            }
        }
        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'data' => $data,
                    'RequestParams' => '',
                    'updateStatus' => $updateStatus,
                    'RequestParams' => $_REQUEST
        ]); // NOT_FOUND (404) being the HTTP response code */
    }

    public function getChartShowStatus($confId, $form) {
        $form = trim($form);
        $confId = (int) $confId;

        if ($form && $confId) {
            $arrTbl = array('checklist' => 'show_checklist');

            $fld = $arrTbl[$form];
            $qry = "SELECT " . $fld . " FROM patientconfirmation WHERE patientConfirmationId = " . $confId;
            $sql = DB::select($qry);
            if ($sql) {
                return (int) $sql[0]->$fld;
            }
        }
        return false;
    }

    /**
     * Patient infoDetails.
     * 
     * @return Response
     */
    public function patientInfoForm(Request $request) {
        if (isset($_REQUEST['RequestStatus']) && $_REQUEST['RequestStatus'] == 1) {
            return response()->json([
                        'status' => 1,
                        'message' => 'there is error',
                        'data' => '',
                        'RequestParams' => $_REQUEST
            ]); // NOT_FOUND (404) being the HTTP response code */
        }
        $userToken = $request->input('user_token');
        $pConfirmId = $request->input('pConfirmId');
        $patient_id = $request->input('patient_id');
        $scanIOL = $request->input('reConfirmId');
        $stub_id = $request->input('ptStubId');
        $imwPatientId = $request->input('imwPatientId');
        $andstubIdQry = "";

        $data = [];
        $status = 0;
        $requiredStatus = 1;
        $message = " unauthorized ";
        if ($this->checkToken($userToken)) {
            if ($patient_id = 0 || trim($patient_id) == "") {
                $message = " PatientId is required ";
                $status = 1;
                $requiredStatus = 0;
            } else if ($stub_id == 0 || trim($stub_id) == "") {
                $message = " StubId is required ";
                $status = 1;
                $requiredStatus = 0;
            } else {
                if (!$pConfirmId) {
                    $andstubIdQry = " AND stub_id = '" . $stub_id . "' AND stub_id != '0' ";
                }
                $qry = "select pt.patient_fname,pt.patient_mname,pt.patient_lname,DATE_FORMAT(pt.date_of_birth,'%m-%d-%Y') as patient_dob,pt.sex ,"
                        . "pt.homePhone,pt.workPhone,pt.language,pt.patient_image_path,pt.street1,pt.street2,pt.race,pt.religion,pt.ethnicity,"
                        . "concat(st.surgeon_lname,',',st.surgeon_fname,' ',st.surgeon_mname) as surgeon_name,st.surgery_time,st.pickup_time,st.arrival_time,st.patient_home_phone,"
                        . "st.patient_work_phone,st.patient_city,st.patient_state,st.patient_zip,st.imwPatientId,st.patient_language,st.iolink_patient_in_waiting_id,st.patient_primary_procedure,"
                        . "st.anesthesiologist_fname,st.anesthesiologist_mname,st.anesthesiologist_lname,DATE_FORMAT(st.dos,'%m-%d-%Y') as dos, "
                        . "st.assisted_by_translator,st.patient_secondary_procedure,st.patient_tertiary_procedure,st.confirming_nurse_fname,st.confirming_nurse_mname,st.confirming_nurse_lname,"
                        . "st.patient_status,TIME_FORMAT(st.checked_in_time, '%l:%i %p') as checked_in_time,TIME_FORMAT(st.checked_out_time, '%l:%i %p') as checked_out_time,pc.patientConfirmationId,pc.patientId,pc.ascId,pc.site as primary_site,pc.secondary_site,pc.tertiary_site,pc.surgeonId,pc.patient_primary_procedure,pc.patient_primary_procedure_id,"
                        . " case when pc.patient_secondary_procedure='' then '0' else pc.patient_secondary_procedure end as patient_secondary_procedure,"
                        . "pc.patient_secondary_procedure_id,pc.patient_tertiary_procedure,"
                        . " case when pc.patient_tertiary_procedure_id='' then '0' else pc.patient_tertiary_procedure_id end as patient_tertiary_procedure_id,"
                        . "pc.anes_NA,pc.anesthesiologist_id,"
                        . "pc.nurseId as checked_in_by_id,pc.assist_by_translator,pc.advanceDirective,pc.discharge_status,pc.no_publicity,pc.surgery_time,pc.pickup_time,pc.arrival_time"
                        . " FROM stub_tbl st "
                        . " LEFT JOIN patientconfirmation pc ON st.patient_confirmation_id=pc.patientConfirmationId "
                        . " LEFT JOIN patient_data_tbl pt ON pt.patient_id=st.patient_id_stub "
                        . " WHERE st.stub_id='" . $stub_id . "'";
                $patientDetails = DB::select($qry);

                foreach ($patientDetails as $patientDetail) {
                    $data['patientInfoDetails'] = $patientDetail;
                    if ($patientDetail->ascId == 0) {
                        $querys = "SELECT  ascId_present,safety_check_list FROM `surgerycenter` limit 1";
                        $re = DB::select($querys);
                        $patientDetail->ascId = $re[0]->ascId_present + 1;
                    }
                }

                $query_rsNotes = "SELECT epost_data,T_time, TIME_FORMAT(T_time, '%l:%i %p') as ePostTime,consent_template_id, consentAutoIncId FROM eposted WHERE table_name='alert' AND patient_id='" . $patient_id . "' AND patient_conf_id = '" . $pConfirmId . "' " . $andstubIdQry;
                $rsNotes = DB::select($query_rsNotes);
                $data['patientalertNotes'] = $rsNotes;
                $qry = "SELECT usersId,concat(lname,',',fname,' ',mname) as fullname FROM users WHERE user_type='Surgeon' ORDER BY lname ASC";
                $Surgeondetails = DB::select($qry);
                $qry2 = "SELECT usersId,concat(lname,',',fname,' ',mname) as fullname FROM users WHERE user_type='Anesthesiologist' ORDER BY lname ASC";
                $Anesthesiologist = DB::select($qry2);
                $qry3 = "SELECT usersId,concat(lname,',',fname,' ',mname) as fullname FROM users WHERE user_type IN('Nurse','Staff','Coordinator') ORDER BY `lname`";
                $getStaffNurseRows = DB::select($qry3);
                $getProcedureDetails = "select procedureId,name from  procedures order by `name`";
                $PrimaryProcedureList = DB::select($getProcedureDetails);
                $arrLanguage = array(["key" => 'English', "value" => 'English'], ["key" => 'Spanish', "value" => 'Spanish'], ["key" => 'French', "value" => 'French'], ["key" => 'German', "value" => 'German'], ["key" => 'Russian', "value" => 'Russian'], ["key" => 'Japanese', "value" => 'Japanese'], ["key" => 'Portuguese', "value" => 'Portuguese'], ["key" => 'Italian', "value" => 'Italian']);
                sort($arrLanguage);
                $arrLanguage[] = ["key" => 'Declined to Specify', 'value' => 'Declined to Specify'];
                $arrLanguage[] = ["key" => 'Other', 'value' => 'Other'];
                $arrRace = array(
                    ["key" => "American Indian or Alaska Native", "value" => "American Indian or Alaska Native"],
                    ["key" => "Asian", "value" => "Asian"],
                    ["key" => "Black or African American", "value" => "Black or African American"],
                    ["key" => "Native Hawaiian or Other Pacific Islander", "value" => "Native Hawaiian or Other Pacific Islander"],
                    ["key" => "Latin American", "value" => "Latin American"],
                    ["key" => "White", "value" => "White"],
                    ["key" => "Declined to Specify", "value" => "Declined to Specify"]);
                $arrEthnicity = array(
                    ["key" => "African Americans", "value" => "African Americans"],
                    ["key" => "American", "value" => "American"],
                    ["key" => "American Indians", "value" => "American Indians"],
                    ["key" => "Chinese", "value" => "Chinese"],
                    ["key" => "European Americans", "value" => "European Americans"],
                    ["key" => "Hispanic or Latino", "value" => "Hispanic or Latino"],
                    ["key" => "Jewish", "value" => "Jewish"],
                    ["key" => "Not Hispanic or Latino", "value" => "Not Hispanic or Latino"],
                    ["key" => "Unknown", "value" => "Unknown"],
                    ["key" => "Declined to Specify", "value" => "Declined to Specify"]);
                $patientFormElement = [
                    'sex' => [['value' => 'm', 'key' => 'Male'], ['value' => 'f', 'key' => 'Female']],
                    'Patient_Status' => [['key' => "Checked-In", 'value' => "Checked-In"], ['key' => "Scheduled", 'value' => "Scheduled"], ['key' => "Checked-Out", 'value' => "Checked-Out"], ['key' => "Canceled", "value" => "Canceled"], ['key' => "No Show", "value" => "No Show"]],
                    'Advanced_Directive' => [['key' => "Yes", 'value' => "Yes"], ['key' => "No", "value" => "No"]],
                    'Discharge_Status' => [['value' => 1, "key" => "Discharged to Home"], ['value' => 2, "key" => "Transferred to Hospital"], ['value' => 3, "key" => "Discharged to Nursing Home"], ['value' => 50, "key" => "Discharged to Hospice"], ['value' => 21, "key" => "Discharged to Law Enforcement"]],
                    "Surgeon" => $Surgeondetails,
                    "Assisted_By_Translator" => [["key" => "Yes", "value" => "yes"], ["key" => "No", "value" => "no"]],
                    "Primary_Procedure" => $PrimaryProcedureList,
                    'Primary_Site' => [
                        ["key" => "LEFT", "value" => 1],
                        ["key" => "RIGHT", "value" => 2],
                        ["key" => "Bilateral", "value" => 3],
                        ["key" => "Left Upper Lid", "value" => 4],
                        ["key" => "Left Lower Lid", "value" => 5],
                        ["key" => "Right Upper Lid", "value" => 6],
                        ["key" => "Right Lower Lid", "value" => 7],
                        ["key" => "Bilateral Upper Lid", "value" => 8],
                        ["key" => "Bilateral Lower Lid", "value" => 9],
                    ],
                    "Anesthesiologist" => $Anesthesiologist, //Anesthesiologist
                    "Checked_in_By" => $getStaffNurseRows,
                    "Language" => $arrLanguage,
                    "Race" => $arrRace,
                    "Ethnicity" => $arrEthnicity,
                ];
                $data['patientFormElement'] = $patientFormElement;
                $message = " Patient Details ";
                $status = 1;
            }
        }
        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => $requiredStatus,
                    'data' => [$data],
        ]); // NOT_FOUND (404) being the HTTP response code 
    }

    public function patientStatusUpdate(Request $request) {
        $userToken = $request->input('user_token');
        $pConfirmId = $request->input('pConfirmId');
        $patient_id = $request->input('patient_id');
        $scanIOL = $request->input('reConfirmId');
        $stub_id = $request->input('ptStubId');
        $imwPatientId = $request->input('imwPatientId');
        $patient_status = $request->input('patient_status');
        $checked_in_time = $request->input('checked_in_time');
        $selected_date = $request->input('selected_date');
        $loggedInUserName = $request->input('loggedInUserName');
        $andstubIdQry = "";
        $data = [];
        $status = 0;
        $requiredStatus = 1;
        $message = " unauthorized ";
        if ($this->checkToken($userToken)) {
            if ($patient_id = 0 || trim($patient_id) == "") {
                $message = " PatientId is required ";
                $status = 1;
                $requiredStatus = 0;
            } else if ($stub_id == 0 || trim($stub_id) == "") {
                $message = " StubId is required ";
                $status = 1;
                $requiredStatus = 0;
            } else {
                $changeStatusid = $stub_id;
                $status = trim($patient_status);
                if ($patient_status == 'No Show') {
                    $statusNow = 'No Show';
                }
                if ($status == 'Checked-In') {
                    $statusNow = 'Checked-Out';
                } else if ($status == 'Checked-Out') {
                    $statusNow = 'Checked-In';
                } else if ($status == 'Scheduled') {
                    $statusNow = 'Canceled';
                } else if ($status == 'Canceled') {
                    $statusNow = 'Scheduled';
                } else {
                    $chkPtStubQry = "select patient_confirmation_id from stub_tbl where stub_id = '$changeStatusid'";
                    $chkPtStubRes = DB::select($chkPtStubQry);
                    //CHECK IF PATIENT CONFIRMATION ID EXIST TABLE THEN SET STATUS FROM CANCEL TO CHECKED-IN ELSE SET STATUS CANCEL TO SCHEDULED
                    if ($chkPtStubRes) {
                        $chk_Patient_Confirmation_id = $chkPtStubRes[0]->patient_confirmation_id;
                        if ($chk_Patient_Confirmation_id <> '0') {
                            $statusNow = 'Checked-In';
                        } else {
                            $statusNow = 'Scheduled';
                        }
                    }
                    //END CHECK IF PATIENT CONFIRMATION ID EXIST TABLE THEN SET STATUS FROM CANCEL TO CHECKED-IN ELSE SET STATUS CANCEL TO SCHEDULED
                }

                unset($arrayRecord);
                $arrayRecord['patient_status'] = $statusNow;
                if ($status == 'Checked-In') {
                    $arrayRecord['checked_out_time'] = date("h:i A");
                    $arrayRecord['recentChartSaved'] = '';
                } else if ($status == 'Checked-Out') {
                    $arrayRecord['checked_in_time'] = $checked_in_time;
                } elseif ($patient_status == 'No Show') {
                    $arrayRecord['patient_status'] = 'Canceled';
                }
                DB::table('stub_tbl')->where('stub_id', $changeStatusid)->update($arrayRecord);

                //START CODE TO SET PATIENT APPOINTMENT STATUS IN imwemr
                if ($statusNow == 'Canceled' || $statusNow == 'Scheduled' || $statusNow == 'Checked-In' || $statusNow == 'Checked-Out' || $statusNow == 'No Show') {
                    $connectionFileName = '';
                    $closeConnectionFileName = '';
                    if (getenv('imwSwitchFile') == 'sync_imwemr.php') {
                        $connectionFileName = 'connect_imwemr.php';
                        $closeConnectionFileName = '';
                    } else if (getenv('imwSwitchFile') == 'sync_imwemr_remote.php') {
                        $connectionFileName = 'connect_imwemr_remote.php';
                        $closeConnectionFileName = '';
                    }
                    if ($connectionFileName) {
                        $getStubImwApptIdQry = "SELECT appt_id,comment,iolink_patient_in_waiting_id FROM stub_tbl WHERE stub_id = '" . $changeStatusid . "'";
                        $getStubImwApptIdRow = DB::select($getStubImwApptIdQry);
                        $stubImwApptId = $getStubImwApptIdRow[0]->appt_id;
                        $stubComment = $getStubImwApptIdRow[0]->comment;
                        $stubIolinkPatientInWaitingId = trim($getStubImwApptIdRow[0]->iolink_patient_in_waiting_id);
                        $imwApptStatusId = '202';       //INITIALIZE VARIABLE
                        $imwApptComments = 'Rescheduled by ' . $loggedInUserName;  //INITIALIZE VARIABLE
                        if ($statusNow == 'Canceled') {
                            $imwApptComments = 'Cancelled by ' . $loggedInUserName;
                            $imwApptStatusId = '18';
                        } else if ($statusNow == 'No Show') {
                            $imwApptComments = 'No Show by ' . $loggedInUserName;
                            $imwApptStatusId = '3';
                        } else if ($statusNow == 'Scheduled') {
                            $imwApptComments = 'Rescheduled by ' . $loggedInUserName;
                            $imwApptStatusId = '202';
                        } else if ($statusNow == 'Checked-In') {
                            $imwApptComments = 'Checked-In by ' . $loggedInUserName;
                            $imwApptStatusId = '13';
                        } else if ($statusNow == 'Checked-Out') {
                            $imwApptComments = 'Checked-Out by ' . $loggedInUserName;
                            $imwApptStatusId = '11';
                        }
                        $blUpdateNew = false;
                        $stubComment = addslashes($stubComment);
                        $imwComments = trim($stubComment . ' ' . $imwApptComments);
                        $this->logApptChangedStatus($stubImwApptId, $selected_date, '', '', $imwApptStatusId, '', '', 'surgerycenter', $imwComments, '', $blUpdateNew, $connectionFileName, $closeConnectionFileName);
                        if ($statusNow == 'Canceled') {//CALL FUNCTION TO RESTORE LABELS IN IDOC
                            //restore_imw_appt_label('', '', $stubImwApptId, $constantImwSlotMinute,$connectionFileName,$closeConnectionFileName);
                        }
                        $updateImwApptStatusQry = "UPDATE schedule_appointments 
                                                        SET sa_patient_app_status_id='" . $imwApptStatusId . "',
                                                        sa_comments='" . addslashes($imwComments) . "' 
                                                        WHERE sa_app_start_date='" . $selected_date . "' 
                                                        AND id='" . $stubImwApptId . "'";

                        $updateImwApptStatusRes = DB::connection('DB_REGISTER_CONNECTION')->select($updateImwApptStatusQry);
                        //START UPDATE PATIENT STATUS IN IOLINK
                        if (($statusNow == 'Canceled' || $statusNow == 'Scheduled') && $stubIolinkPatientInWaitingId && $stubIolinkPatientInWaitingId != '0') {
                            $updtIolinkWaitingTblQry = "UPDATE patient_in_waiting_tbl SET patient_status='" . $statusNow . "',comment='" . addslashes($imwComments) . "'
											WHERE  patient_in_waiting_id = '" . $stubIolinkPatientInWaitingId . "'";
                            $updtIolinkWaitingTblRes = DB::select($updtIolinkWaitingTblQry);
                        }
                        //END UPDATE PATIENT STATUS IN IOLINK
                    }
                }
                //END CODE TO SET PATIENT APPOINTMENT STATUS IN imwemr
                $requiredStatus = 0;
                $message = " status updated ";
                $status = 1;
            }
        }


        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => $requiredStatus,
                    'data' => $data,
        ]); // NOT_FOUND (404) being the HTTP response code 
    }

    //START FUNCTION TO MAINTAIN LOG IN imwemr WHEN CANCEL THE PATIENT
    private function logApptChangedStatus($intApptId, $dtNewApptDate, $tmNewApptStartTime, $tmNewApptEndTime, $intNewApptStatusId = '18', $intNewApptProviderId, $intNewApptFacilityId, $strNewApptOpUsername = 'surgercenter', $strNewApptComments, $intNewApptProcedureId, $blUpdateNew = false, $connectionFileName, $closeConnectionFileName) {

        $strQry = " SELECT procedureid , sa_patient_app_status_id, sa_patient_id, sa_app_start_date, sa_app_starttime, sa_app_endtime, sa_comments, sa_facility_id, sa_madeby, sa_doctor_id, sa_comments  
				FROM schedule_appointments WHERE id = '" . $intApptId . "'";
        $arrData = DB::connection('DB_REGISTER_CONNECTION')->select($strQry);
        if ($arrData) {

            $intPatientId = $arrData[0]->sa_patient_id;   //patient id

            $dtOldApptDate = $arrData[0]->sa_app_start_date;  //old_appt_date
            $tmOldApptStartTime = $arrData[0]->sa_app_starttime;   //old_appt_start_time
            $tmOldApptEndTime = $arrData[0]->sa_app_endtime;   //old_appt_end_time
            $intOldApptStatusId = $arrData[0]->sa_patient_app_status_id; //old_status
            $intOldApptProviderId = $arrData[0]->sa_doctor_id;    //old_provider
            $intOldApptFacilityId = $arrData[0]->sa_facility_id;   //old_facility
            $strOldApptOpUsername = $arrData[0]->sa_madeby;    //oldMadeBy
            $intOldApptProcedureId = $arrData[0]->procedureid;    //oldMadeBy
            $strOldApptComments = $arrData[0]->sa_comments;    //oldMadeBy

            if ($blUpdateNew == false) {
                $dtNewApptDate = $arrData[0]->sa_app_start_date;  //New_appt_date
                $tmNewApptStartTime = $arrData[0]->sa_app_starttime;   //New_appt_start_time
                $tmNewApptEndTime = $arrData[0]->sa_app_endtime;   //New_appt_end_time
                $intNewApptProviderId = $arrData[0]->sa_doctor_id;    //New_provider
                $intNewApptFacilityId = $arrData[0]->sa_facility_id;   //New_facility
                $intNewApptProcedureId = $arrData[0]->procedureid;    //NewMadeBy
            }

            //making log
            $strInsQry = "INSERT INTO previous_status SET
						sch_id 				= '" . $intApptId . "',
						patient_id 			= '" . $intPatientId . "',
						status_time 		= TIME(NOW()),
						status_date 		= CURDATE(),
						status 				= '" . $intNewApptStatusId . "',
						old_date 			= '" . $dtOldApptDate . "',
						old_time 			= '" . $tmOldApptStartTime . "',
						old_provider 		= '" . $intOldApptProviderId . "',
						old_facility 		= '" . $intOldApptFacilityId . "',
						statusComments 		= CONCAT('" . addslashes($strNewApptComments) . "',statusComments),
						oldStatusComments 	= '" . $strOldApptComments . "',
						oldMadeBy 			= '" . $strOldApptOpUsername . "',
						statusChangedBy 	= '" . $strNewApptOpUsername . "',
						dateTime 			= '" . date("Y-m-d H:i:s") . "',
						new_facility 		= '" . $intNewApptFacilityId . "',
						new_provider 		= '" . $intNewApptProviderId . "',
						old_status 			= '" . $intOldApptStatusId . "',
						old_appt_end_time 	= '" . $tmOldApptEndTime . "',
						new_appt_date 		= '" . $dtNewApptDate . "',
						new_appt_start_time	= '" . $tmNewApptStartTime . "',
						new_appt_end_time 	= '" . $tmNewApptEndTime . "',
						old_procedure_id 	= '" . $intOldApptProcedureId . "',
						new_procedure_id 	= '" . $intNewApptProcedureId . "'";

            DB::connection('DB_REGISTER_CONNECTION')->select($strInsQry);
        }
    }

    public function patient_details(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $patientId = $request->json()->get('patientId') ? $request->json()->get('patientId') : $request->input('patientId');
        $stub_id = $request->json()->get('stub_id') ? $request->json()->get('stub_id') : $request->input('stub_id');
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
            } else if ($patientId == "") {
                $message = " PatientId is missing ";
                $status = 1;
                $requiredStatus = 0;
            } else {
                $message = " Patient Detail Information ";
                $status = 1;
                $patient_confirmation_query = "select ascId,finalize_status,advanceDirective,date_format(dos,'%m/%d/%Y') as dos,surgeon_name,surgeonId,anesthesiologist_name,"
                        . "prim_proc_is_misc,site,patient_primary_procedure,patient_primary_procedure_id,patient_secondary_procedure,assist_by_translator,"
                        . "patient_secondary_procedure_id,allergiesNKDA_status"
                        . " from patientconfirmation where patientConfirmationId='" . $pConfirmId . "'";
                $Confirm_patientHeaderInfo = DB::selectone($patient_confirmation_query);
                
                $Confirm_patientHeaderPrimProcIsMisc = $Confirm_patientHeaderInfo->prim_proc_is_misc;
                $Confirm_patientHeaderSiteTemp = $Confirm_patientHeaderInfo->site;
                // APPLYING NUMBERS TO PATIENT SITE
                if ($Confirm_patientHeaderSiteTemp == 1) {
                    $Confirm_patientHeaderInfo->Confirm_patientHeaderSite = "Left Eye";  //OS
                } else if ($Confirm_patientHeaderSiteTemp == 2) {
                    $Confirm_patientHeaderInfo->Confirm_patientHeaderSite = "Right Eye";  //OD
                } else if ($Confirm_patientHeaderSiteTemp == 3) {
                    $Confirm_patientHeaderInfo->Confirm_patientHeaderSite = "Both Eye";  //OU
                } else if ($Confirm_patientHeaderSiteTemp == 4) {
                    $Confirm_patientHeaderInfo->Confirm_patientHeaderSite = "Left Upper Lid";
                } else if ($Confirm_patientHeaderSiteTemp == 5) {
                    $Confirm_patientHeaderInfo->Confirm_patientHeaderSite = "Left Lower Lid";
                } else if ($Confirm_patientHeaderSiteTemp == 6) {
                    $Confirm_patientHeaderInfo->Confirm_patientHeaderSite = "Right Upper Lid";
                } else if ($Confirm_patientHeaderSiteTemp == 7) {
                    $Confirm_patientHeaderInfo->Confirm_patientHeaderSite = "Right Lower Lid";
                } else if ($Confirm_patientHeaderSiteTemp == 8) {
                   $Confirm_patientHeaderInfo->Confirm_patientHeaderSite = "Bilateral Upper Lid";
                } else if ($Confirm_patientHeaderSiteTemp == 9) {
                    $Confirm_patientHeaderInfo->Confirm_patientHeaderSite = "Bilateral Lower Lid";
                }
                // END APPLYING NUMBERS TO PATIENT SITE

                $Confirm_patientHeaderPrimProc = stripslashes($Confirm_patientHeaderInfo->patient_primary_procedure);
                $primProcFullNameForDiv = $Confirm_patientHeaderPrimProc;
                $primProcId = $Confirm_patientHeaderInfo->patient_primary_procedure_id;
                if (strlen($Confirm_patientHeaderPrimProc) > 17) {
                    $Confirm_patientHeaderInfo->Confirm_patientHeaderPrimProc = substr($Confirm_patientHeaderPrimProc, 0, 17) . "... ";
                }

                $Confirm_patientHeaderSecProc = stripslashes($Confirm_patientHeaderInfo->patient_secondary_procedure);
                $Confirm_patientHeaderInfo->secProcFullNameForDiv = $Confirm_patientHeaderSecProc;
                $secProcId = $Confirm_patientHeaderInfo->patient_secondary_procedure_id;
                if (strlen($Confirm_patientHeaderSecProc) > 16) {
                    $Confirm_patientHeaderInfo->Confirm_patientHeaderSecProc = substr($Confirm_patientHeaderSecProc, 0, 16) . "... ";
                }

                $Confirm_patientHeaderAssist_by_translator = $Confirm_patientHeaderInfo->assist_by_translator;
                $patient_qry = "select patient_id,date_format(date_of_birth,'%m-%d-%Y') as dob,concat(round(DATEDIFF(current_date,date_of_birth)/365),' years') as age,"
                        . "patient_fname,patient_mname,patient_lname,street1,street2,city,state,zip,imwPatientId,homePhone,workPhone"
                        . " from patient_data_tbl where patient_id='" . $patientId . "'";
                $patient_res = DB::selectone($patient_qry);
                $data = ['patientDetails' => $patient_res,'confirmed_patientDetails'=>$Confirm_patientHeaderInfo];
            }
        }
        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'requiredStatus' => '',
                    'data' => $data,
        ]); // NOT_FOUND (404) being the HTTP response code 
    }

}
