<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include_once("pre_define_medication.php"); //PRE OP  HEALTH QUEST, LOCAL ANES
include_once("patient_health_quest_saved_medication.php"); //FOR PRE-OP-NURSING)
include_once("patient2takehome.php"); //POST OP  PHYSICIAN

include("laserpredefine_chief_complaint_pop.php"); //LASER PROCEDURE
include_once("laserpredefine_past_medicalHX_pop.php"); //LASER PROCEDURE
include_once("laserpredefine_present_illness_hx_pop.php"); //LASER PROCEDURE
include_once("laserpredefine_medication_pop.php"); //LASER PROCEDURE

/*if($tablename=="preophealthquestionnaire") { 
	include_once("pre_define_medication.php"); //PRE OP  HEALTH QUEST, LOCAL ANES
}else */

if($tablename=="preopnursingrecord") {
	include_once("food_list_pop.php"); //PRE OP  NURSING
	include_once("pre_comments.php"); //PRE OP  NURSING
}else if($tablename=="postopnursingrecord") {
	include_once("post_site.php");  //POST OP  NURSING
	include_once("nourishment_kind.php"); //POST OP  NURSING
	include_once("recovery_comments_pop.php"); //POST OP  NURSING
}else if($tablename=="preopphysicianorders") {
	include_once("pre_define_opmed.php"); //PRE OP  PHYSICIAN
}else if($tablename == "postopphysicianorders") {
	//include_once("patient2takehome.php"); //POST OP  PHYSICIAN
}else if($tablename=="localanesthesiarecord") {
	include_once("ekgLocalAnes_pop.php");  //LOCAL ANES
	include_once("evaluationLocalAnes_pop.php"); //LOCAL ANES
	include_once("post_op_evaluation_pop.php"); //LOCAL ANES
}else if($tablename=="genanesthesiarecord") {
	include_once("evaluation_pop.php"); //GEN ANES RECORD
}else if($tablename=="laser_procedure_patient_table"){
	include_once("laserpredefine_sle_pop.php");
	include_once("laserpredefine_mental_state_pop.php");
	include_once("laserpredefine_fundus_exam_pop.php");
	include_once("pre_define_opmed.php"); //PRE OP  PHYSICIAN.
	include_once("laserpredefine_exposure_pop.php");
	include_once("laserpredefine_spot_size_pop.php");
	include_once("laserpredefine_power_pop.php");
	include_once("laserpredefine_count_pop.php");
	include_once("laserpredefine_anesthesia_pop.php");
	include_once("laserpredefine_post_progressnote_pop.php");
	include_once("laserpredefine_post_operative_status_pop.php");
}/*else if($tablename=="operatingroomrecords") {
	include_once("pre_define_diagnosis.php");
	include_once("post_define_diagnosis.php");
	include_once("pre_define_procedure.php");
	include_once("post_op_drops_pop.php");
	include_once("nurse_notes_pop.php");
}*/
?>