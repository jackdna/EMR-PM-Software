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

class ScanDocumentController extends Controller {

    /**
     * Retrieve the scandoc list details.
     * 
     * @return Response
     */
    function scandocList(Request $request) {
        $userToken = $request->input('user_token');
        $pConfirmId = $request->input('pConfirmId');
        $patient_id = $request->input('patient_id');
        $scanIOL = $request->input('scanIOL');
        $IOLScan = $request->input('IOLScan');
        $dosScan = $request->input('dosScan');
        $stub_id = $request->input('ptStubId');
        if ($dosScan <> "") {
            $dosScan = date("Y-m-d", strtotime($dosScan));
        }

        $data = [];
        $message = " unauthorized ";
        $docFolderRecordsStr = "";
        if ($this->checkToken($userToken)) {
            //?patient_id=3&pConfirmId=464&ptStubId=533&dosScan=2019-05-05
            //INSERT FOLDERS IF NOT ALREADY EXIST
            if ($patient_id && !$pConfirmId) {
                $formFolderExistArr = array('Pt. Info', 'Clinical', 'IOL');
                $arrayRecord = [];
                foreach ($formFolderExistArr as $formExistFolder) {
                    $qry = "select document_id from scan_documents where document_name='" . $formExistFolder . "' and patient_id='" . $patient_id . "' and confirmation_id=0 and dosOfScan='" . $dosScan . "' and stub_id='" . $stub_id . "'";
                    $res = DB::select($qry);
                    if (!$res) {
                        unset($arrayRecord);
                        $arrayRecord['patient_id'] = $patient_id;
                        $arrayRecord['document_name'] = $formExistFolder;
                        $arrayRecord['dosOfScan'] = $dosScan;
                        $arrayRecord['stub_id'] = $stub_id;
                        $arrayRecord['confirmation_id'] = 0;
                        $inserExistId = DB::table('scan_documents')->insertGetId($arrayRecord);
                        //TEMPRARY INSERT LOG OF SCAN FOLDER WITH DATETIME
                        $document_encounter = '';
                        if ($formExistFolder == 'Pt. Info') {
                            $document_encounter = 'pt_info_1';
                        } else if ($formExistFolder == 'Clinical') {
                            $document_encounter = 'clinical_1';
                        }
                        unset($arrayRecord);
                        $arrayRecord['patient_id'] = $patient_id;
                        $arrayRecord['document_name'] = $formExistFolder;
                        $arrayRecord['document_id'] = $inserExistId;
                        $arrayRecord['document_date_time'] = date('Y-m-d H:i:s');
                        $arrayRecord['document_file_name'] = 'scanPopUp.php';
                        $arrayRecord['document_encounter'] = $document_encounter;
                        $arrayRecord['stub_id'] = $stub_id;
                        $inserIdScanLogTbl = DB::table('scan_log_tbl')->insertGetId($arrayRecord);
                    }
                }
            }
            $stubIdQry = (!$pConfirmId) ? " AND stub_id = '" . $stub_id . "' AND stub_id != '0' " : '';
            $formNameArr = array('Pt. Info', 'Clinical', 'IOL');
            $message = 'Scan Doc List !';

            if ($pConfirmId > 0) {
                //$docFolderRecordsStr = "SELECT document_name, document_id FROM scan_documents WHERE confirmation_id = '$pConfirmId' AND dosOfScan = '$dosScan'";
                $docFolderRecordsStr = "SELECT document_name, document_id,confirmation_id FROM scan_documents WHERE confirmation_id = '$pConfirmId'";
            } else if ($patient_id > 0) {
                $docFolderRecordsStr = "SELECT document_name, document_id,confirmation_id FROM scan_documents WHERE patient_id = '$patient_id' AND confirmation_id = '0' AND dosOfScan = '$dosScan' " . $stubIdQry;
            } else {
                //$docFolderRecordsStr = "SELECT document_name, document_id FROM scan_documents WHERE confirmation_id = '$pConfirmId'";
            }


            if ($docFolderRecordsStr <> "") {
                $seq = 0;
                $res = DB::select($docFolderRecordsStr);
                if ($res) {
                    return response()->json([
                                'status' => 1,
                                'message' => $message,
                                'data' => $res,
                    ]); // NOT_FOUND (404) being the HTTP response code 
                } else {
                    $message = "Something went wrong !";
                    return response()->json([
                                'status' => 1,
                                'message' => $message,
                                'query' => '',
                                'data' => $data,
                    ]);
                }
            } else {
                $message = "Parameters are missing !";
                return response()->json([
                            'status' => 1,
                            'message' => $message,
                            'data' => $data,
                ]);
            }
        }
        return response()->json([
                    'status' => 0,
                    'message' => $message,
                    'data' => $data,
        ]); // NOT_FOUND (404) being the HTTP response code 
    }

    /**
     * Creating the folder.
     * 
     * @return Response
     */
    function addFolder(Request $request) {
        $userToken = $request->input('user_token');
        $pConfirmId = $request->input('pConfirmId');
        $patient_id = $request->input('patient_id');
        $scanIOL = $request->input('scanIOL');
        $IOLScan = $request->input('IOLScan');
        $dosScan = $request->input('dosScan');
        $foldername = $request->input('foldername');
        $stub_id = $request->input('ptStubId');
        if ($dosScan <> "") {
            $dosScan = date("Y-m-d", strtotime($dosScan));
        }
        $stub_id = $request->input('ptStubId');
        $data = [];
        $message = " unauthorized ";
        $docFolderRecordsStr = "";
        $arrayRecord = [];
        if ($this->checkToken($userToken)) {
            $qry = "select document_id from scan_documents where document_name='" . trim($foldername) . "' and patient_id='" . $patient_id . "' and confirmation_id='" . $pConfirmId . "' and dosOfScan='" . $dosScan . "' and stub_id='" . $stub_id . "'";
            $res = DB::select($qry);
            if (!$res) {
                $arrayRecord['patient_id'] = $patient_id;
                $arrayRecord['document_name'] = trim($foldername);
                $arrayRecord['dosOfScan'] = $dosScan;
                $arrayRecord['stub_id'] = $stub_id;
                $arrayRecord['confirmation_id'] = $pConfirmId;
                $inserExistId = DB::table('scan_documents')->insertGetId($arrayRecord);
                $message = " Scan Folder has been created successfully !";
                return response()->json([
                            'status' => 1,
                            'folderStatus' => 1,
                            'message' => $message,
                            'data' => $data,
                ]); // NOT_FOUND (404) being the HTTP response code 
            }
            $message = " Already exits !";
            return response()->json([
                        'status' => 1,
                        'folderStatus' => 0,
                        'message' => $message,
                        'data' => $data,
            ]); // NOT_FOUND (404) being the HTTP response code 
        }
        return response()->json([
                    'status' => 0,
                    'message' => $message,
                    'data' => $data,
        ]); // NOT_FOUND (404) being the HTTP response code 
    }

    /**
     * Delete the folder.
     * 
     * @return Response
     */
    function removeFolder(Request $request) {
        $userToken = $request->input('user_token');
        $doc_id = $request->input('doc_id');
        $data = [];
        $message = " unauthorized ";
        $docFolderRecordsStr = "";
        $arrayRecord = [];
        if ($this->checkToken($userToken)) {
            $doc_ids = @explode(",", $doc_id);
            if (!empty($doc_ids)) {
                foreach ($doc_ids as $doc) {
                    if ($doc <> "") {
                        DB::table('scan_documents')->where('document_id', '=', $doc)->delete();
                    }
                }
                $message = " Folder has been removed successfully !";
                $delStatus = 1;
            } else {
                $message = " Error in deleting folder !";
                $delStatus = 0;
            }
            return response()->json([
                        'status' => 1,
                        'delStatus' => $delStatus,
                        'message' => $message,
                        'data' => $data,
            ]);
        }
        return response()->json([
                    'status' => 0,
                    'message' => $message,
                    'data' => $data,
        ]);
    }

    /**
     * scan document listing
     * 
     * @return Response
     */
    function scanDocumentList(Request $request) {
        $userToken = $request->input('user_token');
        $folderId = $request->input('folderId');
        $pConfirmId = $request->input('pConfirmId');
        $patient_id = $request->input('patient_id');
        $dosScan = $request->input('dosScan');
        $stub_id = $request->input('ptStubId');
        if ($dosScan <> "") {
            $dosScan = date("Y-m-d", strtotime($dosScan));
        }
        $stubIdQry = (!$pConfirmId) ? " AND stub_id = '" . $stub_id . "' AND stub_id != '0' " : '';
        $data = [];
        $message = " unauthorized ";
        $docFolderRecordsStr = "";
        $arrayRecord = [];
        $folderFilesQry = '';
        $url=getenv('APP_URL').'/'.getenv('surgeryCenterDirectoryName').'/admin/';
        if ($this->checkToken($userToken)) {
            $documentstatus = 0;
            if ($pConfirmId) {
                $folderFilesQry = "SELECT scan_upload_id,image_type,document_type,img_content,document_name,document_size,confirmation_id,patient_id,form_name,scan_upload_form_id,document_id,parent_sub_doc_id,pdfFilePath,iolink_scan_consent_id,dosOfScan,stub_id,pdf_external_id,concat('".$url."',pdfFilePath) as pdfFilePath FROM scan_upload_tbl WHERE confirmation_id = '$pConfirmId' AND document_id = '$folderId'";
            } else if ($patient_id > 0) {
                $folderFilesQry = "SELECT scan_upload_id,image_type,document_type,img_content,document_name,document_size,confirmation_id,patient_id,form_name,scan_upload_form_id,document_id,parent_sub_doc_id,pdfFilePath,iolink_scan_consent_id,dosOfScan,stub_id,pdf_external_id,concat('".$url."',pdfFilePath) as pdfFilePath FROM scan_upload_tbl WHERE patient_id = '$patient_id' AND confirmation_id = '0' AND document_id = '$folderId' AND dosOfScan = '$dosScan' " . $stubIdQry;
            } else {
                //$folderFilesQry = "SELECT * FROM scan_upload_tbl WHERE document_id = '$folderId'";
            }
            $res = DB::select($folderFilesQry);
            if ($res) {
                $message = "Scanned Document List !";
                $data = $res;
                $documentstatus = 1;
            } else {
                $message = "No Record Found !";
                $documentstatus = 0;
            }
            return response()->json([
                        'status' => 1,
                        'documentstatus' => $documentstatus,
                        'message' => $message,
                        'data' => $data,
            ]);
        }
        return response()->json([
                    'status' => 0,
                    'message' => $message,
                    'data' => $data,
        ]);
    }

    /**
     * scan document delete
     * 
     * @return Response
     */
    function scanDocumentDelete(Request $request) {
        $userToken = $request->input('user_token');
        $scan_upload_id = $request->input('scan_upload_id');
        $data = [];
        $status = 0;
        $message = " unauthorized ";
        if ($this->checkToken($userToken)) {
            $status = 1;
            $scan_upload_ids = @explode(",", $scan_upload_id);
            if (!empty($scan_upload_ids)) {
                foreach ($scan_upload_ids as $scan_upload_idss) {
                    if ($scan_upload_idss > 0) {
                        DB::table('scan_upload_tbl')->where('scan_upload_id', '=', $scan_upload_idss)->delete();
                    }
                }
                $message = " Document(s) has been removed successfully !";
                $delStatus = 1;
            } else {
                $message = " Error in deleting Document !";
                $delStatus = 0;
            }
            return response()->json([
                        'status' => 1,
                        'delStatus' => $delStatus,
                        'message' => $message,
                        'data' => $data,
            ]);
        }
        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'data' => $data,
        ]);
    }

    /**
     * scan document upload
     * 
     * @return Response
     */
    function scanDocumentUpload(Request $request) {
        $userToken = $request->input('user_token');
        $folderId = $request->input('folderId');
        $pConfirmId = $request->input('pConfirmId');
        $patient_id = $request->input('patient_id');
        $dosScan = $request->input('dosScan');
        $stub_id = $request->input('ptStubId');
        $image = $request->input('image');
        if ($dosScan <> "") {
            $dosScan = date("Y-m-d", strtotime($dosScan));
        }
        $rootServerPath = $_SERVER['DOCUMENT_ROOT'] . '/' . env('APP_ROOT') . '/admin/';
        $pdfJpgFileFullPath = '';
        $status = 0;
        $message = " unauthorized ";
        $fileuploadstatus = 0;
        if ($this->checkToken($userToken)) {
            if (!empty($_FILES['surgery_img'])) {
                $fileinfo = $_FILES['surgery_img'];
                $tempPath = $fileinfo['tmp_name'];
                $imageType = $fileinfo['type'];
                $imageName = $fileinfo['name'];

                $extn = explode('.', $imageName);
                $extn = end($extn);
                $imageName = urldecode($imageName);
                $imageName = str_ireplace(" ", "-", $imageName);
                $imageName = str_ireplace(",", "-", $imageName);
                $imageName = str_ireplace("'", "-", $imageName);
                if ($extn == 'gif') {
                    $imageType = "image/gif";
                } else if ($extn == 'jpg' || $extn == 'jpeg') {
                    $imageType = "image/jpeg";
                } else if ($extn == 'png') {
                    $imageType = "image/png";
                } else if ($extn == 'pdf') {
                    $imageType = "application/pdf";
                }
                //fwrite($aa,$extn." @@ ".$imageType." @@ ".$imageName." @@ ".$PSize." @@ ".$pConfirmId." @@ ".$patient_id." <br> "); //For Debugging
                if ($imageType == "image/gif" || $imageType == "image/jpeg" || $imageType == "image/png" || $imageType == "application/pdf") {
                    $PSize = $fileinfo['size'];
                    unset($arrayRecord);
                    $arrayRecord['image_type'] = $imageType;
                    $arrayRecord['document_name'] = $imageName;
                    $arrayRecord['document_size'] = $PSize;
                    $arrayRecord['confirmation_id'] = $pConfirmId;
                    $arrayRecord['patient_id'] = $patient_id;
                    $arrayRecord['document_id'] = $folderId;
                    $arrayRecord['dosOfScan'] = $dosScan;
                    $arrayRecord['stub_id'] = $stub_id;
                    $arrayRecord['scan_upload_save_date_time'] = date('Y-m-d H:i:s');
                    $arrayRecord['img_content'] = '';
                    $arrayRecord['parent_sub_doc_id'] = 0;
                    $arrayRecord['pdfFilePath'] = '';
                    $arrayRecord['iolink_scan_consent_id'] = 0;
                    $arrayRecord['pdf_external_id'] = 0;
                    $inserIdScanUpload = DB::table('scan_upload_tbl')->insertGetId($arrayRecord);

                    //START CODE FOR PDF FILE
                    if ($pConfirmId) {
                        // GET SURGEON NAME FOR GIVEN CONFIRMATION ID 
                        $qry = "select surgeon_name from patientconfirmation where patientConfirmationId=$pConfirmId";
                        $surgeonData = DB::select($qry);
                        $surgeonName = $surgeonData[0]->surgeon_name;
                        // END GET SURGEON NAME FOR GIVEN CONFIRMATION ID 
                    } else {
                        //GET SURGEON NAME FROM STUB TABLE
                        $qry = "select surgeon_fname,surgeon_mname,surgeon_lname,concat(surgeon_fname,surgeon_lname) as surgeon_name from stub_tbl where stub_id=$stub_id";
                        $stubTblSurgeonData = DB::select($qry);
                        $stubTblSurgeonFname = $stubTblSurgeonData[0]->surgeon_fname;
                        $stubTblSurgeonMname = $stubTblSurgeonData[0]->surgeon_mname;
                        $stubTblSurgeonLname = $stubTblSurgeonData[0]->surgeon_lname;
                        if ($stubTblSurgeonMname) {
                            $stubTblSurgeonMname = ' ' . $stubTblSurgeonMname;
                        }
                        $surgeonName = $stubTblSurgeonFname . $stubTblSurgeonMname . ' ' . $stubTblSurgeonLname;
                        //END SURGEON NAME FROM STUB TABLE
                    }

                    $surgeonName = str_replace(" ", "_", $surgeonName);
                    $surgeonName = str_replace(",", "", $surgeonName);
                    $surgeonName = str_replace("!", "", $surgeonName);
                    $surgeonName = str_replace("@", "", $surgeonName);
                    $surgeonName = str_replace("%", "", $surgeonName);
                    $surgeonName = str_replace("^", "", $surgeonName);
                    $surgeonName = str_replace("$", "", $surgeonName);
                    $surgeonName = str_replace("'", "", $surgeonName);
                    $surgeonName = str_replace("*", "", $surgeonName);

                    $pdfFolderName = 'pdfFiles/' . $surgeonName;
                    $pdfFolderNameSave = 'pdfFiles/' . $surgeonName;

                    if (is_dir($rootServerPath . '/' . $pdfFolderName)) {
                        //DO NOT CREATE FOLDER AGAIN
                    } else {
                        mkdir($rootServerPath . '/' . $pdfFolderName, 0777);
                    }
                    if (strtolower($imageType) == 'application/pdf') {
                        $pdfJpgFilePathDatabaseSave = $pdfFolderNameSave . "/" . $inserIdScanUpload . ".pdf";
                    } else {
                        $pdfJpgFilePathDatabaseSave = $pdfFolderNameSave . "/image_" . $inserIdScanUpload . ".jpg";
                    }
                    $arrayRecord['pdfFilePath'] = $pdfJpgFilePathDatabaseSave;
                    $pdfJpgFileFullPath = $rootServerPath . '/' . $pdfJpgFilePathDatabaseSave;
                    move_uploaded_file($tempPath, $pdfJpgFileFullPath);
                    unset($arrayRecord);
                    $arrayRecord['pdfFilePath'] = $pdfJpgFilePathDatabaseSave;
                    DB::table('scan_upload_tbl')->where('scan_upload_id', $inserIdScanUpload)->update($arrayRecord);
                    $message = " File uploaded successfully !";
                    $status = 1;
                    $fileuploadstatus = 1;
                    return response()->json([
                                'status' => $status,
                                'message' => $message,
                                'data' => [],
                                'fileuploadstatus' => $fileuploadstatus
                    ]);
                } else {
                    $message = " Allowed extensions are jpeg,gif,png,pdf ";
                    $fileuploadstatus = 0;
                    return response()->json([
                                'status' => $status,
                                'message' => $message,
                                'data' => [],
                                'fileuploadstatus' => $fileuploadstatus
                    ]);
                }
            }
        }
        return response()->json([
                    'status' => $status,
                    'message' => $message,
                    'data' => [],
                    'fileuploadstatus' => $fileuploadstatus
        ]);
    }
    
    
}
