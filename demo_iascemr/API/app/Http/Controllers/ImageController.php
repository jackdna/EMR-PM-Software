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

//
//Intra Op Record

class ImageController extends Controller {

    //OperatingRoom Save Image    
    public function saveImageOperatingRoomRecord($operatingRoomRecordsId, $iolImg1, $iolImg2, $pConfirmId, $patient_id) {
        $iolImg1 = str_ireplace(' ', '+', $iolImg1);
        $iolImg2 = str_ireplace(' ', '+', $iolImg2);

        $imageName1 = $operatingRoomRecordsId . "_" . time() . "_" . $pConfirmId . '.' . 'jpg';
        $imageName2 = $operatingRoomRecordsId . "_" . time() . "_" . $pConfirmId . '_2.' . 'jpg';
        if (trim($iolImg1) <> "" || trim($iolImg2) <> "") {

            if (!file_exists('../../SigPlus_images/PatientId_' . $patient_id)) {
                mkdir('../../SigPlus_images/PatientId_' . $patient_id, 0777);
            }
            if (!file_exists('../../SigPlus_images/PatientId_' . $patient_id . "/tmp/")) {
                mkdir('../../SigPlus_images/PatientId_' . $patient_id . "/tmp/", 0777);
            }
            if ($iolImg1 <> "") {
                $newfile1 = explode(",", $iolImg1);
                $file1 = '../../SigPlus_images/PatientId_' . $patient_id . "/tmp/" . $imageName1;
                $success1 = @file_put_contents('../../SigPlus_images/PatientId_' . $patient_id . "/tmp/" . $imageName1, base64_decode($newfile1[1]));
                $h = fopen($file1, 'r');
                $iolImg1 = fread($h, filesize($file1));
                if ($success1) {
                    $arrayRecord['iol_ScanStatus'] = '';  //THIS INDICATE THAT FIRST IMAGE/FILE IS YET TO SCAN
                    $arrayRecord['iol_ScanUpload'] = $iolImg1;
                    DB::table('operatingroomrecords')->where('operatingRoomRecordsId', $operatingRoomRecordsId)->update($arrayRecord);
                    @unlink($file1);
                }
            }
            if ($iolImg2 <> "") {
                $newfile2 = explode(",", $iolImg2);
                $file2 = '../../SigPlus_images/PatientId_' . $patient_id . "/tmp/" . $imageName2;
                $success2 = @file_put_contents($file2, base64_decode($newfile2[1]));
                $h = fopen($file2, 'r');
                $iolImg2 = fread($h, filesize($file2));
                if ($success2) {
                    $arrayRecord['iol_ScanStatus2'] = ''; //THIS INDICATE THAT SECOND IMAGE/FILE IS YET TO SCAN
                    $arrayRecord['iol_ScanUpload2'] = $iolImg2;
                    DB::table('operatingroomrecords')->where('operatingRoomRecordsId', $operatingRoomRecordsId)->update($arrayRecord);
                    @unlink($file2);
                }
            }
        }
    }

    //OperatingRoom add Image    
    public function addImageapiOperatingRoomRecord(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $operatingRoomRecordsId = $request->json()->get('operatingRoomRecordsId') ? $request->json()->get('operatingRoomRecordsId') : $request->input('operatingRoomRecordsId');
        $data = [];
        $status = 0;
        // $requiredStatus = 1;
        $message = " unauthorized ";
        $requiredStatus = [];
        $userId = $this->checkToken($userToken);
        if ($userId > 0) {
            if ($operatingRoomRecordsId == 0 || $operatingRoomRecordsId == '') {
                $status = 1;
                $message = " operatingRoomRecordsId is missing !";
            } else {
                //  print_r($_FILES);
                $message = " uploading.. ";
                $iolName = $_FILES["surgery_img"]["name"];
                $iolTmp = $_FILES["surgery_img"]["tmp_name"];
                $iolSize = $_FILES["surgery_img"]["size"];
                $iolTempFile = $_FILES["surgery_img"]["tmp_name"]; // fopen($_FILES["surgery_img"]["tmp_name"], "r");
                $iolImg = addslashes(file_get_contents($iolTempFile));
                $iol_type = $_FILES["surgery_img"]["type"];
                $arrayRecord = [];
                $arrayRecord['image_type'] = $iol_type;
                $arrayRecord['img_content'] = $iolImg;
                $arrayRecord['document_name'] = $iolName;
                $arrayRecord['document_size'] = $iolSize;
                $arrayRecord['confirmation_id'] = $pConfirmId;
                $IOLoperatingRoomRecordDetails = $this->getRowRecord('operatingroomrecords', 'operatingRoomRecordsId', $operatingRoomRecordsId);
                $field_iol_ScanUpload = $IOLoperatingRoomRecordDetails->iol_ScanUpload;
                $field_iol_ScanUpload2 = $IOLoperatingRoomRecordDetails->iol_ScanUpload2;
                if ($field_iol_ScanUpload == '') {
                    $arrayRecord['iol_ScanStatus'] = '';  //THIS INDICATE THAT FIRST IMAGE/FILE IS YET TO SCAN
                    $arrayRecord['iol_ScanUpload'] = $iolImg;
                } else if ($field_iol_ScanUpload2 == '') {
                    $arrayRecord['iol_ScanStatus2'] = ''; //THIS INDICATE THAT SECOND IMAGE/FILE IS YET TO SCAN
                    $arrayRecord['iol_ScanUpload2'] = $iolImg;
                }
                if (!empty($arrayRecord)) {
                    DB::table('operatingroomrecords')->where('operatingRoomRecordsId', $operatingRoomRecordsId)->update($arrayRecord);
                }
                $status = 1;
                $message = " Image uploaded successfully !";
            }
        }
        return response()->json(['status' => $status, 'message' => $message, 'requiredStatus' => '', 'data' => $data,
                    'operatingRoomRecordsId' => $operatingRoomRecordsId,
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

    //discharge summary
    public function dischargeSummaryImageUpload(Request $request) {
        $userToken = $request->json()->get('user_token') ? $request->json()->get('user_token') : $request->input('user_token');
        $pConfirmId = $request->json()->get('pConfirmId') ? $request->json()->get('pConfirmId') : $request->input('pConfirmId');
        $patient_id = $request->json()->get('patient_id') ? $request->json()->get('patient_id') : $request->input('patient_id');
        $iasc_facility_id = $request->json()->get('iasc_facility_id') ? $request->json()->get('iasc_facility_id') : $request->input('iasc_facility_id');
        $facility_id = $request->json()->get('facility') ? $request->json()->get('facility') : $request->input('facility');
        $dischargeSummarySheetId = $request->json()->get('dischargeSummarySheetId') ? $request->json()->get('dischargeSummarySheetId') : $request->input('dischargeSummarySheetId');
        $dis_ScanUpload = $request->json()->get('dis_ScanUpload') ? $request->json()->get('dis_ScanUpload') : $request->input('dis_ScanUpload');
        $dis_ScanUpload2 = $request->json()->get('dis_ScanUpload2') ? $request->json()->get('dis_ScanUpload2') : $request->input('dis_ScanUpload2');
        $dis_ScanUpload3 = $request->json()->get('dis_ScanUpload3') ? $request->json()->get('dis_ScanUpload3') : $request->input('dis_ScanUpload3');
        $dis_ScanUpload4 = $request->json()->get('dis_ScanUpload4') ? $request->json()->get('dis_ScanUpload4') : $request->input('dis_ScanUpload4');
        $stub_id = $request->json()->get('stub_id') ? $request->json()->get('stub_id') : $request->input('stub_id');
        $dis_ScanUpload = str_ireplace(' ', '+', $dis_ScanUpload);
        $dis_ScanUpload2 = str_ireplace(' ', '+', $dis_ScanUpload2);
        $dis_ScanUpload3 = str_ireplace(' ', '+', $dis_ScanUpload3);
        $dis_ScanUpload4 = str_ireplace(' ', '+', $dis_ScanUpload4);
        $imageName = "dis_ScanUpload1_" . $patient_id . "_" . $pConfirmId . '.' . 'jpg';
        $imageName2 = "dis_ScanUpload2_" . $patient_id . "_" . $pConfirmId . '.' . 'jpg';
        $imageName3 = "dis_ScanUpload3_" . $patient_id . "_" . $pConfirmId . '.' . 'jpg';
        $imageName4 = "dis_ScanUpload4_" . $patient_id . "_" . $pConfirmId . '.' . 'jpg';
        $data = [];
        $status = 0;
        $uploadStatus = 0;
        // $requiredStatus = 1;
        $message = " unauthorized ";
        $requiredStatus = [];
        $userId = $this->checkToken($userToken);
        if ($userId > 0) {
            if ($dischargeSummarySheetId == '') {
                $status = 1;
                $message = " dischargeSummarySheetId is missing !";
            } else {
                $status = 1;
                $message = " uploading.. ";
                if (trim($dis_ScanUpload) <> "") {
                    $newfile = explode(",", $dis_ScanUpload);
                    if (!file_exists('../../SigPlus_images/PatientId_' . $patient_id)) {
                        mkdir('../../SigPlus_images/PatientId_' . $patient_id, 0777);
                    }
                    if (!file_exists('../../SigPlus_images/PatientId_' . $patient_id . "/tmp/")) {
                        mkdir('../../SigPlus_images/PatientId_' . $patient_id . "/tmp/", 0777);
                    }
                    $success = @file_put_contents('../../SigPlus_images/PatientId_' . $patient_id . "/tmp/" . $imageName, base64_decode($newfile[1]));
                    $filename = '../../SigPlus_images/PatientId_' . $patient_id . "/tmp/" . $imageName;
                    $h = fopen($filename, 'r');
                    $content = fread($h, filesize($filename));
                    unset($arrayRecord);
                    $arrayRecord['dis_ScanStatus'] = '';  //THIS INDICATE THAT FIRST IMAGE/FILE IS YET TO SCAN
                    $arrayRecord['dis_ScanUpload'] = $content;
                    if (!empty($arrayRecord)) {
                        DB::table('dischargesummarysheet')->where('dischargeSummarySheetId', $dischargeSummarySheetId)->update($arrayRecord);
                    }
                    @unlink($filename);
                }
                if (trim($dis_ScanUpload2) <> "") {
                    $newfile2 = explode(",", $dis_ScanUpload2);
                    if (!file_exists('../../SigPlus_images/PatientId_' . $patient_id)) {
                        mkdir('../../SigPlus_images/PatientId_' . $patient_id, 0777);
                    }
                    if (!file_exists('../../SigPlus_images/PatientId_' . $patient_id . "/tmp/")) {
                        mkdir('../../SigPlus_images/PatientId_' . $patient_id . "/tmp/", 0777);
                    }
                    $success = @file_put_contents('../../SigPlus_images/PatientId_' . $patient_id . "/tmp/" . $imageName2, base64_decode($newfile2[1]));
                    $filename2 = '../../SigPlus_images/PatientId_' . $patient_id . "/tmp/" . $imageName2;
                    $h = fopen($filename2, 'r');
                    $content2 = fread($h, filesize($filename2));
                    unset($arrayRecord);
                    $arrayRecord['dis_ScanStatus2'] = '';  //THIS INDICATE THAT FIRST IMAGE/FILE IS YET TO SCAN
                    $arrayRecord['dis_ScanUpload2'] = $content2;
                    if (!empty($arrayRecord)) {
                        DB::table('dischargesummarysheet')->where('dischargeSummarySheetId', $dischargeSummarySheetId)->update($arrayRecord);
                    }
                    @unlink($filename2);
                }
                $status = 1;
                $uploadStatus = 1;
                $message = " Image uploaded successfully !";
            }
        }
        return response()->json(['status' => $status, 'message' => $message, 'uploadStatus' => $uploadStatus, 'requiredStatus' => '', 'data' => $data,
                    'dischargeSummarySheetId' => $dischargeSummarySheetId,
                        ], 200, ['Content-type' => 'application/json;charset=utf-8'], JSON_UNESCAPED_UNICODE); // NOT_FOUND (404) being the HTTP response code 
    }

}
