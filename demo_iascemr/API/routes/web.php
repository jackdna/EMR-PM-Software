<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

$router->post('login', 'UserController@login');
$router->get('generateUserSession/{user_token}', 'UserController@generateUserSession');
$router->post('scandocList', 'ScanDocumentController@scandocList');

$router->post('addFolder', 'ScanDocumentController@addFolder');
$router->post('removeFolder', 'ScanDocumentController@removeFolder');

$router->post('scanDocumentList', 'ScanDocumentController@scanDocumentList');
$router->post('scanDocumentUpload', 'ScanDocumentController@scanDocumentUpload');
$router->post('scanDocumentDelete', 'ScanDocumentController@scanDocumentDelete');

$router->post('patientInfoForm', 'PatientController@patientInfoForm');
$router->post('savePatientDetails', 'PatientController@savePatientDetails');
$router->post('patientStatusUpdate', 'PatientController@patientStatusUpdate');
$router->post('patient_details', 'PatientController@patient_details');

$router->post('nametagformElement', 'NametagController@nametagformElement');

$router->post('nurseprepostOP', 'PrePostOPController@nursePrePostOP');
$router->post('nurseprepostOPMove', 'PrePostOPController@nursePrePostOPMove');

$router->post('surgeonprepostOP', 'PrePostOPController@surgeonPrePostOP');

$router->post('surgeonprepostOPMove', 'PrePostOPController@surgeonPrePostOPMove');

$router->post('addprogressNotes', 'ProgressNotesController@addeditprogressNotes');
$router->post('editprogressNotes', 'ProgressNotesController@addeditprogressNotes');
$router->post('delprogressNotes', 'ProgressNotesController@addeditprogressNotes');

$router->post('listprogressNotes', 'ProgressNotesController@listprogressNotes');

$router->post('patient_alergies_details', 'AllergiesController@patient_alergies_details');

$router->post('patient_checklist_retrieve', 'ChecklistController@patient_checklist_retrieve');

$router->post('patient_checklist_save', 'ChecklistController@patient_checklist_save');

$router->post('amendment_listing', 'AmendmentController@amendment_listing');
$router->post('save_amendment', 'AmendmentController@save_amendment');
$router->post('del_amendment_notes', 'AmendmentController@del_amendment_notes');

$router->post('instruction_template', 'InstructionsheetController@instruction_template');

$router->post('saveInstructionsheet', 'InstructionsheetController@saveInstructionsheet');

$router->post('medicationReconciliationFormdata', 'MedicationReconciliationController@MedicationReconciliation_form');

$router->post('MedicationReconciliation_save', 'MedicationReconciliationController@MedicationReconciliation_save');

$router->post('consent_template', 'ConsentController@consent_template');
$router->post('consent_template_save', 'ConsentController@saveconsent_template');
$router->post('patient_header_details', 'Controller@patient_header_details');
$router->post('assist_by_translator_ajax', 'Controller@assist_by_translator_ajax');
$router->post('update_procedure_ajax', 'Controller@update_procedure_ajax');
$router->post('left_slider_menu', 'Controller@left_slider_menu');
$router->post('preop_healthquest', 'PreOPHealthQuestController@preop_healthquest');
$router->post('preop_healthquest_save', 'PreOPHealthQuestController@preop_healthquest_save');
$router->post('PreOPHealthQuest_reset', 'PreOPHealthQuestController@PreOPHealthQuest_reset');

$router->post('operative_report', 'OperativeRecordController@operative_report');
$router->post('operative_reportsave', 'OperativeRecordController@operative_reportsave');
$router->post('html_code', 'OperativeRecordController@html_code');

$router->post('TransferFollowups_template', 'TransferFollowupsController@TransferFollowups_template');
$router->post('TransferFollowups_save', 'TransferFollowupsController@TransferFollowups_save');
$router->post('TransferFollowups_reset', 'TransferFollowupsController@TransferFollowups_reset');

$router->post('PreNurseAlderate_template', 'PreNurseAlderateController@PreNurseAlderate_template');
$router->post('PreNurseAlderate_save', 'PreNurseAlderateController@PreNurseAlderate_save');

$router->post('history_physicial_clearance', 'HistoryPhysicalClearanceController@history_physicial_clearance');

$router->post('history_physicial_clearance_save', 'HistoryPhysicalClearanceController@history_physicial_clearance_save');
$router->post('history_physicial_clearance_reset', 'HistoryPhysicalClearanceController@history_physicial_clearance_reset');

$router->post('PreOPPhysicianOrders_form', 'PreOPPhysicianOrdersController@PreOPPhysicianOrders_form');
$router->post('PreOPPhysicianOrders_save', 'PreOPPhysicianOrdersController@PreOPPhysicianOrders_save');
$router->post('PreOPPhysician_medication_del', 'PreOPPhysicianOrdersController@PreOPPhysician_medication_del');

$router->post('dashboard', 'DashboardController@api_dashboard');
$router->post('search', 'DashboardController@api_search');
$router->post('saveComment', 'DashboardController@api_saveComment');
$router->post('changePassword', 'DashboardController@api_changePassword');
$router->post('template_list', 'DashboardController@templateList');
$router->post('saveEpost', 'DashboardController@api_saveEpost');
$router->post('listEpost', 'DashboardController@api_listEpost');
$router->post('updateEpost','DashboardController@api_updateEpost');
$router->post('deleteEpost', 'DashboardController@api_deleteEpost');
$router->post('namtagformElement', 'DashboardController@api_nametagformelement');
$router->post('namtagformPDF', 'DashboardController@api_nametagformPDF');
$router->post('prepost', 'DashboardController@api_prepost');
$router->post('generatePDF', 'DashboardController@generatePDF');

$router->post('PreOPNursing_form','PreOPNursingController@PreOPNursing_form');
$router->post('PreOPNursing_save','PreOPNursingController@PreOPNursing_save');
$router->post('PreOPNursing_delete','PreOPNursingController@PreOPNursing_delete');

$router->post('PostNurseAlderate_template','PostNurseAlderateController@PostNurseAlderate_template');
$router->post('PostNurseAlderate_save','PostNurseAlderateController@PostNurseAlderate_save');
 

$router->post('PostOpPhysician_data','PostOpPhysicianController@PostOpPhysician_data');
$router->post('PostOpPhysician_save','PostOpPhysicianController@PostOpPhysician_save');


$router->post('LaserProcedure_form','LaserProcedureController@LaserProcedure_form');
$router->post('LaserProcedure_save','LaserProcedureController@LaserProcedure_save');
$router->post('LaserPreOPMedication_delete','LaserProcedureController@LaserPreOPMedication_delete');


$router->post('PostOPNursing_form','PostOPNursingController@PostOPNursing_form');


$router->post('PatientForm_list','PatientFormController@PatientForm_list');
$router->post('PatientHistory','PatientFormController@Patient_History');
$router->post('unFinalizedPatient','PatientFormController@unFinalizedPatient');
$router->post('listing','PatientFormController@Listing');
$router->get('listing','PatientFormController@Listing');

$router->post('InjectionMiscellaneous_form','InjectionMiscellaneousController@InjectionMiscellaneous_form');
$router->post('InjectionMiscellaneous_save','InjectionMiscellaneousController@InjectionMiscellaneous_save');

$router->post('IntraOpRecord_form','IntraOpRecordController@IntraOpRecord_form');
$router->post('IntraOpRecord_save','IntraOpRecordController@IntraOpRecord_save');
$router->post('LensBrand','IntraOpRecordController@LensBrand');

$router->post('addImageapiOperatingRoomRecord','ImageController@addImageapiOperatingRoomRecord');

$router->post('DishchargeSummarySheet_form','DishchargeSummarySheetController@DishchargeSummarySheet_form');
$router->post('DishchargeSummarySheet_save','DishchargeSummarySheetController@DishchargeSummarySheet_save');
$router->post('superbill_del','DishchargeSummarySheetController@superbill_del');
$router->post('dischargeSummaryImageUpload','ImageController@dischargeSummaryImageUpload');

$router->post('PostNurseAlderate_template','PostNurseAlderateController@PostNurseAlderate_template');
$router->post('PostNurseAlderate_save','PostNurseAlderateController@PostNurseAlderate_save');

$router->post('PostOpPhysician_data','PostOpPhysicianController@PostOpPhysician_data');
$router->post('PostOpPhysician_save','PostOpPhysicianController@PostOpPhysician_save');

$router->post('PostOpNurse_data','PostOpNurseController@PostOpNurse_data');
$router->post('PostOpNurse_save','PostOpNurseController@PostOpNurse_save');

$router->post('VitalSignDelete','PostOpNurseController@VitalSignDelete');
$router->post('LocalAnesRecordController_form','LocalAnesRecordController@LocalAnesRecordController_form');


$router->post('GenAnesRecController_form','GenAnesRecController@GenAnesRecController_form');
$router->post('GenAnesRecController_save','GenAnesRecController@GenAnesRecController_save');

$router->post('LocalAnesRecordController_form','LocalAnesRecordController@LocalAnesRecordController_form');
$router->post('LocalAnesRecordController_save','LocalAnesRecordController@LocalAnesRecordController_save');

$router->post('GenAnesNursesNotesController_form','GenAnesNursesNotesController@GenAnesNursesNotesController_form');
$router->post('GenAnesNursesNotesController_save','GenAnesNursesNotesController@GenAnesNursesNotesController_save');

$router->post('updateNurseNotesAPI','GenAnesNursesNotesController@updateNurseNotesAPI');
$router->post('deleteNurseNotesAPI','GenAnesNursesNotesController@deleteNurseNotesAPI');
