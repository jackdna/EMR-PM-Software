<?php
/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
*/
?>
<?php
/*
File: ChartAP.php
Coded in PHP7
Purpose: This is a class file for PT Forms slider
Access Type : Include file
*/
?>
<?php
//PtForms
class PtForms{
	private $pid, $fid, $uid;
	public function __construct($pid,$fid=""){
		$this->pid=$pid; 
		$this->fid=$fid;
		$this->uid=$_SESSION["authId"];
	}
	
	function retrieveExamDetailsSearch($exam_name,$fndByVal,$tstExmSearch) {
		$patient_id = $this->pid;
		$arrPlansNme='';
		$exam_name = strtolower(htmlspecialchars($exam_name));
		//START INITIALIZE VARIABLE FOR THIS FUNCTION
		$releaseNumFld = ",chart_master_table.releaseNumber";
		//END INITIALIZE VARIABLE FOR THIS FUNCTION
		if($fndByVal=='Exam') {
			if($tstExmSearch=='yes') {//INCASE TO SEARCH TEST-EXAMS
				switch($exam_name){
					case "vf":
					case "VF":
						$qry = "SELECT vf_id AS tId, DATE_FORMAT(examDate, '%Y-%m-%d') AS date_of_service,
								performedBy AS prfBy, phyName as phy, purged
								FROM vf WHERE patientId='".$patient_id."'
								AND formId='0'
								ORDER BY examDate DESC, examTime DESC, vf_id DESC " ;
					break;
					
					case "vf-gl":
					case "VF-GL":
						$qry = "SELECT vf_gl_id AS tId, DATE_FORMAT(examDate, '%Y-%m-%d') AS date_of_service,
								performedBy AS prfBy, phyName as phy, purged
								FROM vf_gl WHERE patientId='".$patient_id."'
								AND formId='0'
								ORDER BY examDate DESC, examTime DESC, vf_gl_id DESC " ;
					break;

					case "hrt":
					case "HRT":
						$qry = "SELECT nfa_id AS tId, DATE_FORMAT(examDate, '%Y-%m-%d') AS date_of_service,
								performBy AS prfBy, phyName as phy, purged
								FROM nfa WHERE patient_id='".$patient_id."'
								AND form_id='0'
								ORDER BY examDate DESC, examTime DESC, nfa_id DESC " ;
					break;

					case "oct":
					case "OCT":
						$qry = "SELECT oct_id AS tId, DATE_FORMAT(examDate, '%Y-%m-%d') AS date_of_service,
								performBy AS prfBy, phyName as phy, purged
								FROM oct WHERE patient_id='".$patient_id."'
								AND form_id = '0'
								ORDER BY examDate DESC, examTime DESC, oct_id DESC " ;
					break;
					
					case "oct_rnfl":
					case "OCT_RNFL":
						$qry = "SELECT oct_rnfl_id AS tId, DATE_FORMAT(examDate, '%Y-%m-%d') AS date_of_service,
								performBy AS prfBy, phyName as phy, purged
								FROM oct_rnfl WHERE patient_id='".$patient_id."'
								AND form_id = '0'
								ORDER BY examDate DESC, examTime DESC, oct_rnfl_id DESC " ;
					break;
					
					case "gdx":
					case "GDX":
						$qry = "SELECT gdx_id AS tId, DATE_FORMAT(examDate, '%Y-%m-%d') AS date_of_service,
								performBy AS prfBy, phyName as phy, purged
								FROM test_gdx WHERE patient_id='".$patient_id."'
								AND form_id = '0'
								ORDER BY examDate DESC, examTime DESC, gdx_id DESC " ;
					break;				

					case "Pachy":
						$qry = "SELECT pachy_id AS tId, DATE_FORMAT(examDate, '%Y-%m-%d') AS date_of_service,
								performedBy AS prfBy, phyName as phy, purged
								FROM pachy WHERE patientId='".$patient_id."'
								AND formId='0'
								ORDER BY examDate DESC, examTime DESC, pachy_id DESC " ;
					break;

					case "ivfa":
					case "IVFA":
						$qry = "SELECT vf_id AS tId, DATE_FORMAT(exam_date, '%Y-%m-%d') AS date_of_service,
								performed_by AS prfBy, phy as phy, purged
								FROM ivfa WHERE patient_id='".$patient_id."'
								AND form_id = '0'
								ORDER BY exam_date DESC, examTime DESC, vf_id DESC " ;
					break;

					case "icg":
					case "ICG":
						$qry = "SELECT icg_id AS tId, DATE_FORMAT(exam_date, '%Y-%m-%d') AS date_of_service,
								performed_by AS prfBy, phy as phy, purged
								FROM icg WHERE patient_id='".$patient_id."'
								AND form_id = '0'
								ORDER BY exam_date DESC, examTime DESC, icg_id DESC " ;
					break;

					case "fundus":
					case "Fundus":
						$qry = "SELECT disc_id AS tId, DATE_FORMAT(examDate, '%Y-%m-%d') AS date_of_service,
								performedBy AS prfBy, phyName as phy, purged
								FROM disc WHERE patientId='".$patient_id."'
								AND formId = '0'
								ORDER BY examDate DESC, examTime DESC, disc_id DESC " ;
					break;

					case "external/anterior":
					case "External/Anterior":
						$qry = "SELECT disc_id AS tId, DATE_FORMAT(examDate, '%Y-%m-%d') AS date_of_service,
								performedBy AS prfBy, phyName as phy, purged
								FROM disc_external WHERE patientId='".$patient_id."'
								AND formId='0'
								ORDER BY examDate DESC, examTime DESC, disc_id DESC " ;
					break;

					case "topography":
					case "Topography":
						$qry = "SELECT topo_id AS tId, DATE_FORMAT(examDate, '%Y-%m-%d') AS date_of_service,
								performedBy AS prfBy, phyName as phy, purged
								FROM topography WHERE patientId='".$patient_id."'
								AND formId='0'
								ORDER BY examDate DESC, examTime DESC, topo_id DESC " ;
					break;

					case "ophthalmoscopy":
					case "Ophthalmoscopy":
						$qry = "SELECT ophtha_id AS tId, DATE_FORMAT(exam_date, '%Y-%m-%d') AS date_of_service,
								performedBy AS prfBy, phyName as phy, purged
								FROM ophtha WHERE patient_id='".$patient_id."'
								AND form_id = '0'
								ORDER BY exam_date DESC, examTime DESC, ophtha_id DESC " ;
					break;

					case "other":
					case "Other":
						$qry = "SELECT test_other_id AS tId, DATE_FORMAT(examDate, '%Y-%m-%d') AS date_of_service,
								performedBy AS prfBy, phyName as phy, test_other AS subcat, purged
								FROM test_other WHERE patientId='".$patient_id."'
								AND formId='0'
								ORDER BY test_other ASC, examDate DESC, examTime DESC, test_other_id DESC " ;
					break;

					case "laboratories":
					case "Laboratories":
						$qry = "SELECT test_labs_id AS tId, DATE_FORMAT(examDate, '%Y-%m-%d') AS date_of_service,
								performedBy AS prfBy, phyName as phy, test_labs AS subcat, purged
								FROM test_labs WHERE patientId='".$patient_id."'
								AND formId='0'
								ORDER BY test_labs ASC, examDate DESC, examTime DESC, test_labs_id DESC " ;
					break;
					case "ascan":
					case "Ascan":
					case "a/scan":
					case "A/Scan":
						$qry = "SELECT surgical_id AS tId, DATE_FORMAT(examDate, '%Y-%m-%d') AS date_of_service,
								DATE_FORMAT(examDate, '".get_sql_date_format()."') AS eDt,
								DATE_FORMAT(examDate, '".get_sql_date_format('','y')."') AS dt,
								performedByOD AS prfBy, signedById as phy, purged
								FROM surgical_tbl WHERE patient_id ='".$patient_id."'
								AND formId='0'
								ORDER BY examDate DESC, examTime DESC, surgical_id DESC " ;
					break;
					
					case "IOL_Master":
						$qry = "SELECT iol_master_id AS tId, DATE_FORMAT(examDate, '%Y-%m-%d') AS date_of_service,
								DATE_FORMAT(examDate, '%m-%d-%Y') AS eDt,
								DATE_FORMAT(examDate, '%m-%d-%y') AS dt,
								performedByOD AS prfBy, signedById as phy, purged
								FROM iol_master_tbl WHERE patient_id ='".$patient_id."'
								AND formId='0'
								ORDER BY examDate DESC, examTime DESC, iol_master_id DESC " ;
					break;
					
					case "b-scan":
					case "B-Scan":
						$qry = "SELECT test_bscan_id AS tId, DATE_FORMAT(examDate, '%Y-%m-%d') AS date_of_service,
								performedBy AS prfBy, phyName as phy, purged
								FROM test_bscan WHERE patientId='".$patient_id."'
								AND formId='0'
								ORDER BY examDate DESC, examTime DESC, test_bscan_id DESC " ;
					break;

					case "cell count":
					case "Cell Count":
						$qry = "SELECT test_cellcnt_id AS tId, DATE_FORMAT(examDate, '%Y-%m-%d') AS date_of_service,
								performedBy AS prfBy, phyName as phy, purged
								FROM test_cellcnt WHERE patientId='".$patient_id."'
								AND formId='0'
								ORDER BY examDate DESC, examTime DESC, test_cellcnt_id DESC " ;
					break;

				}
			}else {
				switch($exam_name)
				{
					case "pupil":
						$arrPlansNme = "Pupil";
						$qry = "SELECT finalize,chart_master_table.date_of_service,chart_master_table.id ".$releaseNumFld." from
									chart_master_table,chart_pupil,chart_left_cc_history
									WHERE
										chart_master_table.patient_id = $patient_id
										and chart_left_cc_history.form_id = chart_master_table.id
										and chart_pupil.formId = chart_master_table.id  AND chart_pupil.purged='0'  
									ORDER by chart_master_table.id DESC
									";
					break;
					case "eom":
						$arrPlansNme = "EOM";
						$qry = "SELECT finalize, chart_master_table.date_of_service,chart_master_table.id ".$releaseNumFld." from
									chart_master_table,chart_eom,chart_left_cc_history
									WHERE
										chart_master_table.patient_id = $patient_id
										and chart_left_cc_history.form_id = chart_master_table.id
										and chart_eom.form_id = chart_master_table.id  AND chart_eom.purged='0' 
									ORDER by chart_master_table.id DESC
									";
					break;
					case "external":
						$arrPlansNme = "External";
						$qry = "SELECT finalize,chart_master_table.date_of_service ,chart_master_table.id ".$releaseNumFld." from
									chart_master_table,chart_external_exam,chart_left_cc_history
									WHERE
										chart_master_table.patient_id = $patient_id
										and chart_left_cc_history.form_id = chart_master_table.id
										and chart_external_exam.form_id = chart_master_table.id AND chart_external_exam.purged='0' 
									ORDER by chart_master_table.id DESC
									";
					break;
					case "l_a":
					case "l and a":
						$arrPlansNme = "L&A";
						$qry = "SELECT finalize,chart_master_table.date_of_service ,chart_master_table.id ".$releaseNumFld." from
									chart_master_table,chart_lids,chart_lesion,chart_lid_pos,chart_lac_sys,chart_drawings,chart_left_cc_history
									WHERE
										chart_master_table.patient_id = $patient_id
										and chart_left_cc_history.form_id = chart_master_table.id
										and chart_lids.form_id = chart_master_table.id AND chart_lids.purged='0' 
										and chart_lesion.form_id = chart_master_table.id AND chart_lesion.purged='0' 
										and chart_lid_pos.form_id = chart_master_table.id AND chart_lid_pos.purged='0' 
										and chart_lac_sys.form_id = chart_master_table.id AND chart_lac_sys.purged='0' 
										and chart_drawings.form_id = chart_master_table.id AND chart_drawings.purged='0' AND chart_drawings.exam_name='LA' 
									ORDER by chart_master_table.id DESC
									";
					break;

					case "gonio":
						$arrPlansNme = "Gonio";
						$qry = "SELECT finalize,chart_master_table.date_of_service ,chart_master_table.id ".$releaseNumFld." from
									chart_master_table,chart_gonio,chart_left_cc_history
									WHERE
										chart_master_table.patient_id = $patient_id
										and chart_left_cc_history.form_id = chart_master_table.id
										and chart_gonio.form_id = chart_master_table.id AND chart_gonio.purged='0' 
									ORDER by chart_master_table.id DESC
									";
					case "iop":
						$arrPlansNme = "IOP/Gonio";
						$qry = "SELECT finalize,chart_master_table.date_of_service ,chart_master_table.id ".$releaseNumFld." from
									chart_master_table,chart_iop,chart_left_cc_history
									WHERE
										chart_master_table.patient_id = $patient_id
										and chart_left_cc_history.form_id = chart_master_table.id
										and chart_iop.form_id = chart_master_table.id AND chart_iop.purged='0'
									ORDER by chart_master_table.id DESC
									";
					break;
					case "sle":
						$arrPlansNme = "SLE";
						$qry = "SELECT finalize,chart_master_table.date_of_service ,chart_master_table.id ".$releaseNumFld." from
									chart_master_table,chart_conjunctiva, chart_cornea, chart_ant_chamber, chart_iris, chart_lens, chart_drawings ,chart_left_cc_history
									WHERE
										chart_master_table.patient_id = $patient_id
										and chart_left_cc_history.form_id = chart_master_table.id
										and chart_conjunctiva.form_id = chart_master_table.id AND chart_conjunctiva.purged='0' 
										and chart_cornea.form_id = chart_master_table.id AND chart_cornea.purged='0' 
										and chart_ant_chamber.form_id = chart_master_table.id AND chart_ant_chamber.purged='0' 
										and chart_iris.form_id = chart_master_table.id AND chart_iris.purged='0' 
										and chart_lens.form_id = chart_master_table.id AND chart_lens.purged='0' 
										and chart_drawings.form_id = chart_master_table.id AND chart_drawings.purged='0' AND chart_drawings.exam_name='SLE'
									ORDER by chart_master_table.id DESC
									";
					break;
					case "optic":
						$arrPlansNme = "Opt.Nev/Disc";
						$qry = "SELECT finalize,chart_master_table.date_of_service ,chart_master_table.id ".$releaseNumFld." from
									chart_master_table,chart_optic,chart_left_cc_history
									WHERE
										chart_master_table.patient_id = $patient_id
										and chart_left_cc_history.form_id = chart_master_table.id
										and chart_optic.form_id = chart_master_table.id AND chart_optic.purged='0' 
									ORDER by chart_master_table.id DESC
									";
					break;
					case "disc":	
						$arrPlansNme = "Fundus";
						$qry1 = "SELECT finalize,chart_master_table.date_of_service ,chart_master_table.id ".$releaseNumFld." from
									chart_master_table,disc,chart_left_cc_history
									WHERE
										chart_master_table.patient_id = $patient_id
										and chart_left_cc_history.form_id = chart_master_table.id
										and disc.formId = chart_master_table.id
										and disc.purged = '0'
									ORDER by chart_master_table.id DESC
									";
					break;
					case "r_v":
					case "r and v":
						$arrPlansNme = "Fundus Exam";
						$qry = "SELECT finalize,chart_master_table.date_of_service ,chart_master_table.id ".$releaseNumFld." from
									chart_master_table,chart_vitreous, chart_retinal_exam, chart_blood_vessels, chart_periphery, chart_macula,chart_drawings, chart_left_cc_history
									WHERE
										chart_master_table.patient_id = $patient_id
										and chart_left_cc_history.form_id = chart_master_table.id
										
										and chart_vitreous.form_id = chart_master_table.id AND chart_vitreous.purged='0' 
										and chart_retinal_exam.form_id = chart_master_table.id AND chart_retinal_exam.purged='0' 
										and chart_blood_vessels.form_id = chart_master_table.id AND chart_blood_vessels.purged='0' 
										and chart_periphery.form_id = chart_master_table.id AND chart_periphery.purged='0' 
										and chart_macula.form_id = chart_master_table.id AND chart_macula.purged='0' 
										and chart_drawings.form_id = chart_master_table.id AND chart_drawings.purged='0' AND chart_drawings.exam_name='FundusExam' 
										
									ORDER by chart_master_table.id DESC
									";
					break;
					case "amsler":
						$arrPlansNme = "Amsler Grid";
						$qry = "SELECT finalize,chart_master_table.date_of_service ,chart_master_table.id ".$releaseNumFld." from
									chart_master_table,amsler_grid,chart_left_cc_history
									WHERE
										chart_master_table.patient_id = $patient_id
										and chart_left_cc_history.form_id = chart_master_table.id
										and amsler_grid.form_id = chart_master_table.id
									ORDER by chart_master_table.id DESC
									";
					break;
					case "cvf":
						$arrPlansNme = "CVF";
						$qry = "SELECT finalize,chart_master_table.date_of_service ,chart_master_table.id ".$releaseNumFld." from
									chart_master_table,chart_cvf,chart_left_cc_history
									WHERE
										chart_master_table.patient_id = $patient_id
										and chart_left_cc_history.form_id = chart_master_table.id
										and chart_cvf.formId = chart_master_table.id
									ORDER by chart_master_table.id DESC
									";
					break;
					case "diplopia":
						$arrPlansNme = "Diplopia";
						$qry = "SELECT finalize,chart_master_table.date_of_service ,chart_master_table.id ".$releaseNumFld." from
									chart_master_table,chart_diplopia,chart_left_cc_history
									WHERE
										chart_master_table.patient_id = $patient_id
										and chart_left_cc_history.form_id = chart_master_table.id
										and chart_diplopia.formId = chart_master_table.id
									ORDER by chart_master_table.id DESC
									";
					break;
					case "vf":
						$arrPlansNme = "VF";
						$qry = "SELECT finalize,chart_master_table.date_of_service ,chart_master_table.id ".$releaseNumFld." from
									chart_master_table,vf,chart_left_cc_history
									WHERE
										chart_master_table.patient_id = $patient_id
										and chart_left_cc_history.form_id = chart_master_table.id
										and vf.formId = chart_master_table.id
										and vf.purged = '0' AND vf.del_status='0'
									ORDER by chart_master_table.id DESC
									";
					break;
					case "vf-gl":
						$arrPlansNme = "VF-GL";
						$qry = "SELECT finalize,chart_master_table.date_of_service ,chart_master_table.id ".$releaseNumFld." from
									chart_master_table,vf_gl,chart_left_cc_history
									WHERE
										chart_master_table.patient_id = $patient_id
										and chart_left_cc_history.form_id = chart_master_table.id
										and vf_gl.formId = chart_master_table.id
										and vf_gl.purged = '0' AND vf_gl.del_status='0'
									ORDER by chart_master_table.id DESC
									";
					break;
					case "hrt":
						$arrPlansNme = "HRT";
						$qry = "SELECT finalize,chart_master_table.date_of_service ,chart_master_table.id ".$releaseNumFld." from
									chart_master_table,nfa,chart_left_cc_history
									WHERE
										chart_master_table.patient_id = $patient_id
										and chart_left_cc_history.form_id = chart_master_table.id
										and nfa.form_id = chart_master_table.id
										and nfa.purged = '0' AND nfa.del_status='0'
									ORDER by chart_master_table.id DESC
									";
					break;
					case "oct":
						$arrPlansNme = "OCT";
						$qry = "SELECT finalize,chart_master_table.date_of_service ,chart_master_table.id ".$releaseNumFld." from
									chart_master_table,oct,chart_left_cc_history
									WHERE
										chart_master_table.patient_id = $patient_id
										and chart_left_cc_history.form_id = chart_master_table.id
										and oct.form_id = chart_master_table.id
										and oct.purged = '0' AND oct.del_status='0'
									ORDER by chart_master_table.id DESC
									";
					break;
					case "oct_rnfl":
						$arrPlansNme = "OCT-RNFL";
						$qry = "SELECT finalize,chart_master_table.date_of_service ,chart_master_table.id ".$releaseNumFld." from
									chart_master_table,oct_rnfl,chart_left_cc_history
									WHERE
										chart_master_table.patient_id = $patient_id
										and chart_left_cc_history.form_id = chart_master_table.id
										and oct_rnfl.form_id = chart_master_table.id
										and oct_rnfl.purged = '0' AND oct_rnfl.del_status='0'
									ORDER by chart_master_table.id DESC
									";
					break;
					
					case "gdx":
						$arrPlansNme = "GDX";
						$qry = "SELECT finalize,chart_master_table.date_of_service ,chart_master_table.id ".$releaseNumFld." from
									chart_master_table, test_gdx ,chart_left_cc_history
									WHERE
										chart_master_table.patient_id = $patient_id
										and chart_left_cc_history.form_id = chart_master_table.id
										and test_gdx.form_id = chart_master_table.id
										and test_gdx.purged = '0' AND test_gdx.del_status='0'
									ORDER by chart_master_table.id DESC
									";
					break;
					
					case "nfa":
						$arrPlansNme = "NFA";
						$qry = "SELECT finalize,chart_master_table.date_of_service ,chart_master_table.id ".$releaseNumFld." from
									chart_master_table,nfa,chart_left_cc_history
									WHERE
										chart_master_table.patient_id = $patient_id
										and chart_left_cc_history.form_id = chart_master_table.id
										and nfa.form_id = chart_master_table.id
										and nfa.purged = '0' AND nfa.del_status='0'
									ORDER by chart_master_table.id DESC
									";
					break;
					case "pachy":
						$arrPlansNme = "Pachy";
						$qry = "SELECT finalize,chart_master_table.date_of_service,chart_master_table.id,chart_master_table.id  ".$releaseNumFld." from
									chart_master_table,pachy,chart_left_cc_history
									WHERE
										chart_master_table.patient_id = $patient_id
										and chart_left_cc_history.form_id = chart_master_table.id
										and pachy.formId = chart_master_table.id
										and pachy.purged = '0' AND pachy.del_status='0'
									ORDER by chart_master_table.id DESC
									";
					break;
					case "ivfa":
						$arrPlansNme = "IVFA";
						$qry = "SELECT finalize,chart_master_table.date_of_service ,chart_master_table.id ".$releaseNumFld." from
									chart_master_table,ivfa,chart_left_cc_history
									WHERE
										chart_master_table.patient_id = $patient_id
										and chart_left_cc_history.form_id = chart_master_table.id
										and ivfa.form_id = chart_master_table.id
										and ivfa.purged = '0' AND ivfa.del_status='0'
									ORDER by chart_master_table.id DESC
									";
					break;
					case "icg":
						$arrPlansNme = "ICG";
						$qry = "SELECT finalize,chart_master_table.date_of_service ,chart_master_table.id ".$releaseNumFld." from
									chart_master_table,icg,chart_left_cc_history
									WHERE
										chart_master_table.patient_id = $patient_id
										and chart_left_cc_history.form_id = chart_master_table.id
										and icg.form_id = chart_master_table.id
										and icg.purged = '0' AND icg.del_status='0'
									ORDER by chart_master_table.id DESC
									";
					break;
					case "ophtha":
					case "ophthalmoscopy":
						$arrPlansNme = "Ophthalmoscopy";
						$qry = "SELECT finalize,chart_master_table.date_of_service ,chart_master_table.id ".$releaseNumFld." from
									chart_master_table,ophtha,chart_left_cc_history
									WHERE
										chart_master_table.patient_id = $patient_id
										and chart_left_cc_history.form_id = chart_master_table.id
										and ophtha.form_id = chart_master_table.id
										and ophtha.purged = '0'
									ORDER by chart_master_table.id DESC
									";
					break;
					case "a/scan":
					case "ascan":
						$arrPlansNme = "Ascan";
						$qry = "SELECT finalize,chart_master_table.date_of_service ,chart_master_table.id ".$releaseNumFld." from
									chart_master_table,surgical_tbl,chart_left_cc_history
									WHERE
										chart_master_table.patient_id = $patient_id
										and chart_left_cc_history.form_id = chart_master_table.id
										and surgical_tbl.form_id = chart_master_table.id
										and surgical_tbl.purged = '0' AND surgical_tbl.del_status='0'
									ORDER by chart_master_table.id DESC
									";
					break;
					
					case "iol_master":
						$arrPlansNme = "IOL_Master";
						$qry = "SELECT finalize,chart_master_table.date_of_service ,chart_master_table.id ".$releaseNumFld." from
									chart_master_table,iol_master_tbl,chart_left_cc_history
									WHERE
										chart_master_table.patient_id = $patient_id
										and chart_left_cc_history.form_id = chart_master_table.id
										and iol_master_tbl.form_id = chart_master_table.id
										and iol_master_tbl.purged = '0' AND iol_master_tbl.del_status='0'
									ORDER by chart_master_table.id DESC
									";
					break;
					
					case "vision":
						$arrPlansNme = "Vision";
						$qry = "SELECT finalize,chart_master_table.date_of_service ,chart_master_table.id ".$releaseNumFld." from
									chart_master_table,chart_vis_master,chart_left_cc_history
									WHERE
										chart_master_table.patient_id = $patient_id
										and chart_left_cc_history.form_id = chart_master_table.id
										and chart_vis_master.form_id = chart_master_table.id
									ORDER by chart_master_table.id DESC
									";
					break;

					case "external/anterior":
						$arrPlansNme = "External/Anterior";
						$qry = "SELECT finalize,chart_master_table.date_of_service ,chart_master_table.id ".$releaseNumFld." from
									chart_master_table,disc_external,chart_left_cc_history
									WHERE
										chart_master_table.patient_id = $patient_id
										and chart_left_cc_history.form_id = chart_master_table.id
										and disc_external.formId = chart_master_table.id
										and disc_external.purged = '0' AND disc_external.del_status='0'
									ORDER by chart_master_table.id DESC
									";
					break;
					case "topography":
						$arrPlansNme = "Topography";
						$qry = "SELECT finalize,chart_master_table.date_of_service ,chart_master_table.id ".$releaseNumFld." from
									chart_master_table,topography,chart_left_cc_history
									WHERE
										chart_master_table.patient_id = $patient_id
										and chart_left_cc_history.form_id = chart_master_table.id
										and topography.formId = chart_master_table.id
										and topography.purged = '0' AND topography.del_status='0'
									ORDER by chart_master_table.id DESC
									";
					break;

					case "bscan":
					case "b-scan":
					case "B-Scan":
						$arrPlansNme = "B-Scan";
						$qry = "SELECT finalize,chart_master_table.date_of_service ,chart_master_table.id ".$releaseNumFld." from
									chart_master_table,test_bscan,chart_left_cc_history
									WHERE
										chart_master_table.patient_id = $patient_id
										and chart_left_cc_history.form_id = chart_master_table.id
										and test_bscan.formId = chart_master_table.id
										and test_bscan.purged = '0' AND test_bscan.del_status='0'
									ORDER by chart_master_table.id DESC
									";
					break;

					case "cellcount":
					case "cell count":
					case "Cell Count":
						$arrPlansNme = "Cell Count";
						$qry = "SELECT finalize,chart_master_table.date_of_service ,chart_master_table.id ".$releaseNumFld." from
									chart_master_table,test_cellcnt,chart_left_cc_history
									WHERE
										chart_master_table.patient_id = $patient_id
										and chart_left_cc_history.form_id = chart_master_table.id
										and test_cellcnt.formId = chart_master_table.id
										and test_cellcnt.purged = '0' AND test_cellcnt.del_status='0'
									ORDER by chart_master_table.id DESC
									";
					break;

					default:
						$arrPlansNme = "Amendment";
						$qry = "SELECT finalize,chart_master_table.date_of_service ,chart_master_table.id ".$releaseNumFld." from
									chart_master_table,chart_assessment_plans,chart_left_cc_history
									WHERE
										chart_master_table.patient_id = $patient_id
										and chart_left_cc_history.form_id = chart_master_table.id
										and chart_assessment_plans.form_id = chart_master_table.id
										and (assessment_1  LIKE '%$exam_name%' or assessment_2 LIKE '%$exam_name%' or assessment_3 LIKE '%$exam_name%' or assessment_4 LIKE '%$exam_name%')
									ORDER by chart_master_table.id DESC
									";
						//echo '<center>Wrong keyword</center>';
						//return false;
					break;
				}
			}
		}else if($fndByVal=='Provider') {
			$searchKeywordArr = explode(",", $exam_name);
			$providerLastName = trim($searchKeywordArr[0]);
			$providerFirstName = trim($searchKeywordArr[1]);
			$qry = "SELECT finalize,chart_master_table.date_of_service ,chart_master_table.id ".$releaseNumFld." from
						users,chart_left_cc_history,chart_master_table ".
						//" LEFT JOIN users ON users.id = chart_master_table.providerId ".
						" WHERE
							chart_master_table.patient_id = $patient_id
							AND chart_left_cc_history.form_id = chart_master_table.id
							AND users.lname LIKE '".$providerLastName."%'
							AND users.fname LIKE '".$providerFirstName."%'
							AND users.id = chart_master_table.providerId
						ORDER by chart_master_table.id DESC
						";
		}else if($fndByVal=='DxCode') {
			/*
			$qry = "SELECT chart_master_table.finalize,chart_left_cc_history.date_of_service ,chart_master_table.id,superbill.formId FROM
						superbill,procedureinfo,chart_left_cc_history, chart_master_table ".
						" WHERE
							chart_master_table.patient_id = '".$patient_id."'
							AND procedureinfo.idSuperBill = superbill.idSuperBill
							AND (procedureinfo.dx1 	 = '".$exam_name."'
								OR procedureinfo.dx2 = '".$exam_name."'
								OR procedureinfo.dx3 = '".$exam_name."'
								OR procedureinfo.dx4 = '".$exam_name."')
							AND superbill.formId 	 = chart_master_table.id
							AND chart_left_cc_history.form_id = chart_master_table.id
						ORDER by chart_master_table.id DESC
						";
			*/
			$qry= "SELECT c1.date_of_service, c2.formId, c1.finalize,c1.id
					FROM chart_master_table c1
					LEFT JOIN superbill c2 ON c2.formId = c1.id AND c2.delete_status = 0
					LEFT JOIN procedureinfo c3 ON c3.idSuperBill = c2.idSuperBill AND c3.delete_status = 0
					LEFT JOIN chart_left_cc_history c4 ON c4.form_id = c1.id
					WHERE c1.patient_id = '".$patient_id."'
					AND (
					c3.dx1 	  =	'".$exam_name."'
					OR c3.dx2 = '".$exam_name."'
					OR c3.dx3 = '".$exam_name."'
					OR c3.dx4 = '".$exam_name."'
					)
					ORDER BY `c4`.`date_of_service` DESC
				  ";
		}else if($fndByVal=='CCHx'){
			$qry = "
					SELECT c1.date_of_service, c1.finalize,c1.id
					FROM chart_master_table c1				
					LEFT JOIN chart_left_cc_history c2 ON c2.form_id = c1.id
					WHERE c1.patient_id = '".$patient_id."'
					AND (
						reason LIKE '%".$exam_name."%' OR
						ccompliant LIKE '%".$exam_name."%'
					)
					ORDER BY `c1`.`date_of_service` DESC
			";		
		}
		
		$exmDtArr=array();	
		if(!empty($qry)){
		$res = sqlStatement($qry) ; //or die($qry)
		$num_rows = imw_num_rows($res);
		$exam_info='';
		
		$i=0;
		if($num_rows>0) {
			while($row = sqlFetchArray($res)) {
				if($row['date_of_service']!='0000-00-00') {
					$exmDtArr[]=$row['date_of_service'];
				}
			}
		}
		}
		
		return $exmDtArr;
	}
	
	function retrieveExamDetailsSearch_v2($fndByVal){
		$pid = $this->pid;
		if($fndByVal == "Finalized"){
			$sql = "SELECT
					c1.date_of_service, c2.form_id, c1.finalize,c1.id
					FROM chart_master_table c1
					LEFT JOIN chart_left_cc_history c2 ON c2.form_id = c1.id
					WHERE c1.patient_id = '".$pid."'
					AND c1.finalize = '1'
					AND c1.purge_status = '0'
					AND c1.delete_status='0'
					";

		}else if($fndByVal == "Purged"){
			$sql = "SELECT
					c1.date_of_service, c2.form_id, c1.finalize,c1.id
					FROM chart_master_table c1
					LEFT JOIN chart_left_cc_history c2 ON c2.form_id = c1.id
					WHERE c1.patient_id = '".$pid."'
					AND c1.purge_status = '1'
					AND c1.delete_status='0'
					";

		}else if($fndByVal == "Active"){ //Active
			$sql = "SELECT
					c1.date_of_service, c2.form_id, c1.finalize,c1.id
					FROM chart_master_table c1
					LEFT JOIN chart_left_cc_history c2 ON c2.form_id = c1.id
					WHERE c1.patient_id = '".$pid."'
					AND c1.purge_status = '0'
					AND c1.finalize = '0'
					AND c1.delete_status='0'
					";
		}else{ //All
			$sql = "SELECT
					c1.date_of_service, c2.form_id, c1.finalize,c1.id
					FROM chart_master_table c1
					LEFT JOIN chart_left_cc_history c2 ON c2.form_id = c1.id
					WHERE c1.patient_id = '".$pid."'
					AND c1.delete_status='0'
					";
		}

		$arr=array();
		$rez = sqlStatement($sql);
		for($i=0;$row=sqlFetchArray($rez);$i++){
			if(!empty($row['date_of_service']) && ($row['date_of_service']!='0000-00-00')) {
				$arr["id"][] = $row['id'];
				$arr["dos"][] = $row['date_of_service'];
			}
		}

		return $arr;
	}
	
	// Functions	
	
	function getChartNotesInfo($exmDtNewArr="",$purgeFlg="0")
	{
		$patientId = $this->pid;
		//START CODE FOR SELECTED RECORDS
		$exmDtQry='';
		if($exmDtNewArr) {
			$exmDtNewImplode = $exmDtNewArr[0];
			if(count($exmDtNewArr>1)) {
				$exmDtNewImplode = implode("','",$exmDtNewArr);
			}
			$exmDtQry = " AND chart_master_table.date_of_service in('".$exmDtNewImplode."') ";

		}
		//END CODE FOR SELECTED RECORDS

		//Purge Stop
		if($purgeFlg == "1"){
			$phrsPurge = " AND chart_master_table.purge_status = '0' ";
		}else{
			$phrsPurge = "";
		}
		//Purge Stop

		//chart_left_cc_history.date_of_service
		$sql = "SELECT ".
			 //"amsler_grid.id AS amslerId, ".
			 "chart_assessment_plans.id AS assessId, ".
			 "chart_assessment_plans.cosigner_id, ".
			 //"chart_cvf.cvf_id,  ".
			 //"chart_diplopia.dip_id, ".
			 //"chart_eom.eom_id, ".
			 //"chart_external_exam.ee_id, ".
			 //"chart_iop.iop_id, ".
			 //"chart_la.la_id, ".
			 "chart_left_cc_history.cc_id, ".
			 //"chart_left_cc_history.date_of_service, DATE_FORMAT(chart_left_cc_history.date_of_service,'%m-%d-%y') AS date_of_service2, ".
			 //"chart_left_provider_issue.pr_is_id, ".
			 //"chart_optic.optic_id, ".
			 //"chart_pupil.pupil_id, ".
			 //"chart_rv.rv_id, ".
			 //"chart_slit_lamp_exam.sle_id, ".
			 //"chart_vision.vis_id, ".
			 "disc.disc_id, ".
			 "disc.examDate AS examDateDisc, ".
			 "ivfa.vf_id AS ivfaId, ".
			 "ivfa.exam_date AS examDateIvfa, ".
			 "icg.icg_id AS icgId, ".
			 "icg.exam_date AS examDateIcg, ".
			 "nfa.nfa_id, ".
			 "nfa.examDate AS examDateNfa, ".
			 //"ophtha.ophtha_id, ".
			 "pachy.pachy_id, ".
			 "pachy.examDate AS examDatePachy, ".
			 
			 "vf.vf_id, ".
			 "vf.examDate As examDateVF, ".
			 
			 "vf_gl.vf_gl_id, ".
			 "vf_gl.examDate As examDateVF_GL, ".
			 
			 "topography.topo_id, ".
			 "topography.examDate AS examDateTopo, ".
			 "disc_external.disc_id AS discExId, ".
			 "disc_external.examDate AS examDateDiscEx, ".
			 "oct.oct_id AS octId, ".
			 "oct.examDate AS examDateOct, ".
			 
			 "oct_rnfl.oct_rnfl_id, ".
			 "oct_rnfl.examDate AS examDateOct_rnfl, ".
			 
			 "test_gdx.gdx_id AS gdxId, ".
			 "test_gdx.examDate AS examDateGdx, ".

			 "test_bscan.test_bscan_id, ".
			 "test_bscan.examDate AS examDateBS, ".

			 "test_cellcnt.test_cellcnt_id, ".
			 "test_cellcnt.examDate AS examDateCC, ".

			 //"special_dgn_contact_lens.sp_dgn_id AS contact_lens_id,".
			 //"vf_nfa.vf_nfa_id, ".
			 "surgical_tbl.surgical_id AS ascan_id, ".
			 "iol_master_tbl.iol_master_id, ".
			 "memo_tbl.memo_id, ".
			 "DATE_FORMAT(chart_master_table.date_of_service,'".get_sql_date_format('','y')."') AS date_of_service2, ".
			 "chart_master_table.* ".
			 "FROM chart_master_table  ".
			 //"LEFT JOIN amsler_grid ON amsler_grid.form_id = chart_master_table.id  ".
			 "LEFT JOIN chart_assessment_plans ON chart_assessment_plans.form_id = chart_master_table.id ".
			 //"LEFT JOIN chart_cvf ON chart_cvf.formId = chart_master_table.id  ".
			 //"LEFT JOIN chart_diplopia ON chart_diplopia.formId = chart_master_table.id  ".
			 //"LEFT JOIN chart_eom ON chart_eom.form_id = chart_master_table.id  ".
			 //"LEFT JOIN chart_external_exam ON chart_external_exam.form_id = chart_master_table.id  ".
			 //"LEFT JOIN chart_iop ON chart_iop.form_id = chart_master_table.id  ".
			 //"LEFT JOIN chart_la ON chart_la.form_id = chart_master_table.id  ".
			 "LEFT JOIN chart_left_cc_history ON chart_left_cc_history.form_id = chart_master_table.id  ".
			 //"LEFT JOIN chart_left_provider_issue ON chart_left_provider_issue.form_id = chart_master_table.id  ".
			 //"LEFT JOIN chart_optic ON chart_optic.form_id = chart_master_table.id  ".
			 //"LEFT JOIN chart_pupil ON chart_pupil.formId = chart_master_table.id  ".
			 //"LEFT JOIN chart_rv ON chart_rv.form_id = chart_master_table.id  ".
			 //"LEFT JOIN chart_slit_lamp_exam ON chart_slit_lamp_exam.form_id = chart_master_table.id  ".
			 //"LEFT JOIN chart_vision ON chart_vision.form_id = chart_master_table.id  ".
			 "LEFT JOIN disc ON disc.formId = chart_master_table.id AND disc.purged='0' AND disc.del_status='0' ".
			 "LEFT JOIN ivfa ON ivfa.form_id = chart_master_table.id AND ivfa.purged='0' AND ivfa.del_status='0' ".
			 "LEFT JOIN icg ON icg.form_id = chart_master_table.id AND icg.purged='0' AND icg.del_status='0' ".
			 "LEFT JOIN nfa ON nfa.form_id = chart_master_table.id AND nfa.purged='0' AND nfa.del_status='0' ".
			 //"LEFT JOIN ophtha ON ophtha.form_id = chart_master_table.id  ".
			 "LEFT JOIN pachy ON pachy.formId = chart_master_table.id AND pachy.purged='0' AND pachy.del_status='0' ".
			 "LEFT JOIN vf ON vf.formId = chart_master_table.id AND vf.purged='0' AND vf.del_status='0' ".
			 "LEFT JOIN vf_gl ON vf_gl.formId = chart_master_table.id AND vf_gl.purged='0' AND vf_gl.del_status='0' ".
			 "LEFT JOIN topography ON topography.formId = chart_master_table.id AND topography.purged='0' AND topography.del_status='0' ".
			 "LEFT JOIN disc_external ON disc_external.formId = chart_master_table.id AND disc_external.purged='0' AND disc_external.del_status='0' ".
			 "LEFT JOIN oct ON oct.form_id = chart_master_table.id AND oct.purged='0' AND oct.del_status='0' ".
			 "LEFT JOIN oct_rnfl ON oct_rnfl.form_id = chart_master_table.id AND oct_rnfl.purged='0' AND oct_rnfl.del_status='0' ".
			 "LEFT JOIN test_gdx ON test_gdx.form_id = chart_master_table.id AND test_gdx.purged='0' AND test_gdx.del_status='0' ".
			 "LEFT JOIN test_bscan ON test_bscan.formId = chart_master_table.id AND test_bscan.purged='0' AND test_bscan.del_status='0' ".
			 "LEFT JOIN test_cellcnt ON test_cellcnt.formId = chart_master_table.id AND test_cellcnt.purged='0' AND test_cellcnt.del_status='0' ".
			 //"LEFT JOIN vf_nfa ON vf_nfa.form_id = chart_master_table.id  ".
			 //"LEFT JOIN special_dgn_contact_lens ON special_dgn_contact_lens.form_id = chart_master_table.id  ".
			 "LEFT JOIN surgical_tbl ON surgical_tbl.form_id = chart_master_table.id AND surgical_tbl.purged='0' AND surgical_tbl.del_status='0' ". // ASCAN
			 "LEFT JOIN iol_master_tbl ON iol_master_tbl.form_id = chart_master_table.id AND iol_master_tbl.purged='0' AND iol_master_tbl.del_status='0' ". // IOL_Master
			 "LEFT JOIN memo_tbl ON memo_tbl.form_id = chart_master_table.id ".
			 "WHERE chart_master_table.patient_id = '".$patientId."' ".
			 "AND chart_master_table.delete_status = '0' ".
			 $exmDtQry.
			 $phrsPurge.
			 "ORDER BY chart_master_table.date_of_service ASC, chart_master_table.id ASC ".
			 //"ORDER BY IFNULL(chart_left_cc_history.date_of_service,chart_master_table.create_dt) ASC, chart_master_table.id ASC ".
			 "";
		
		$res = sqlStatement($sql);
		
		return $res;
	}
	
	function getChartNotesInfoSearch($exmDtNewArr,$finalizeVal,$tstExmSearch="")
	{
		$patientId = $this->pid;
		//START CODE FOR SELECTED RECORDS
		$exmDtQry='';
		if($exmDtNewArr) {
			$exmDtNewImplode = $exmDtNewArr[0];
			if(count($exmDtNewArr>1)) {
				$exmDtNewImplode = implode("','",$exmDtNewArr);
			}
			$exmDtQry = " AND c23.date_of_service in('".$exmDtNewImplode."')";

		}
		//END CODE FOR SELECTED RECORDS

		//START CODE FOR FINALIZE STATUS
		$finalizeQry='';
		if($finalizeVal=='Finalized') 	 {$finalizeQry = " AND c1.finalize ='1' AND c1.purge_status='0'";
		}else if($finalizeVal=='Purged') {$finalizeQry = " AND c1.purge_status='1'";
		}else if($finalizeVal=='Active') {$finalizeQry = " AND c1.finalize ='0' AND c1.purge_status='0'";
		}
		//END CODE FOR FINALIZE STATUS

			$sql = "SELECT ".

				 "c30.id AS assessId, c30.cosigner_id,  ".
				 "c23.cc_id, ".
				 //"c23.date_of_service, DATE_FORMAT(c23.date_of_service,'%m-%d-%y') AS date_of_service2, ".
				 //"chart_left_provider_issue.pr_is_id, ".
				 "c2.pupil_id, ".
				 "c3.eom_id, ".
				 "c4.ee_id, ".
				 "c5.id as lidsId, c31.id as lesionId, c32.id as lidPosId, c33.id as lacSysId, c34.id as laDrawId, ".
				 "c6.gonio_id, ".
				 "c7.iop_id, ".
				 "c8.id as conjId, c40.id as cornId, c41.id as antId, c42.id as irisId, c43.id as lensId, c44.id as sle_drw_Id, ".
				 "c9.optic_id, ".
				 "c10.id as vit_id, c35.id as ret_id, c36.id as bv_id, c37.id as peri_id, c38.id as mac_id, c39.id as rv_drw_id, ".
				 "c11.id AS amslerId, ".
				 "c12.cvf_id, ".
				 "c13.dip_id, ".
				 "c21.id AS vis_id, ".
				 "c19.ophtha_id, ".
				 
				 "c29.memo_id, ".
				 "DATE_FORMAT(c1.date_of_service,'".get_sql_date_format('','y')."') AS date_of_service2, ".
				 "c1.* ".
				 "FROM chart_master_table c1 ".
				 
				 "LEFT JOIN chart_pupil c2 ON c2.formId = c1.id  AND c2.purged='0'   ".
				 "LEFT JOIN chart_eom c3 ON c3.form_id = c1.id  AND c3.purged='0'  ".
				 "LEFT JOIN chart_external_exam c4 ON c4.form_id = c1.id  AND c4.purged='0'  ".
				 
				 "LEFT JOIN chart_lids c5 ON c5.form_id = c1.id  AND c5.purged='0'  ".
				 "LEFT JOIN chart_lesion c31 ON c31.form_id = c1.id  AND c31.purged='0'  ".
				 "LEFT JOIN chart_lid_pos c32 ON c32.form_id = c1.id  AND c32.purged='0'  ".
				 "LEFT JOIN chart_lac_sys c33 ON c33.form_id = c1.id  AND c33.purged='0'  ".
				 "LEFT JOIN chart_drawings c34 ON c34.form_id = c1.id  AND c34.purged='0' AND c34.exam_name='LA'  ".
				 
				 "LEFT JOIN chart_gonio c6 ON c6.form_id = c1.id AND c6.purged='0'  ".
				 "LEFT JOIN chart_iop c7 ON c7.form_id = c1.id  AND c7.purged='0'  ".
				 
				 "LEFT JOIN chart_conjunctiva c8 ON c8.form_id = c1.id  AND c8.purged='0'  ".
				 "LEFT JOIN chart_cornea c40 ON c40.form_id = c1.id  AND c40.purged='0'  ".
				 "LEFT JOIN chart_ant_chamber c41 ON c41.form_id = c1.id  AND c41.purged='0'  ".
				 "LEFT JOIN chart_iris c42 ON c42.form_id = c1.id  AND c42.purged='0'  ".
				 "LEFT JOIN chart_lens c43 ON c43.form_id = c1.id  AND c43.purged='0'  ".
				 "LEFT JOIN chart_drawings c44 ON c44.form_id = c1.id  AND c44.purged='0' AND c44.exam_name='SLE' ".
				 
				 "LEFT JOIN chart_optic c9 ON c9.form_id = c1.id  AND c9.purged='0'  ".
				 
				 "LEFT JOIN chart_vitreous c10 ON c10.form_id = c1.id  AND c10.purged='0'  ".
				 "LEFT JOIN chart_retinal_exam c35 ON c35.form_id = c1.id  AND c35.purged='0'  ".
				 "LEFT JOIN chart_blood_vessels c36 ON c36.form_id = c1.id  AND c36.purged='0'  ".
				 "LEFT JOIN chart_periphery c37 ON c37.form_id = c1.id  AND c37.purged='0'  ".
				 "LEFT JOIN chart_macula c38 ON c38.form_id = c1.id  AND c38.purged='0'  ".
				 "LEFT JOIN chart_drawings c39 ON c39.form_id = c1.id  AND c39.purged='0' AND c39.exam_name='FundusExam'  ".
				 
				 "LEFT JOIN amsler_grid c11 ON c11.form_id = c1.id  ".
				 "LEFT JOIN chart_cvf c12 ON c12.formId = c1.id  ".
				 "LEFT JOIN chart_diplopia c13 ON c13.formId = c1.id  ".
				 "LEFT JOIN chart_vis_master c21 ON c21.form_id = c1.id  ".
				 "LEFT JOIN chart_left_cc_history c23 ON c23.form_id = c1.id  ".
				 "LEFT JOIN ophtha c19 ON c19.form_id = c1.id  ".
				 
				 "LEFT JOIN memo_tbl c29 ON c29.form_id = c1.id ".
				 "LEFT JOIN chart_assessment_plans c30 ON c30.form_id = c1.id ".
				 "WHERE c1.patient_id = '".$patientId."' ".
				 "AND c1.delete_status = '0' ".
				 $exmDtQry." ".$finalizeQry.
				 "ORDER BY c1.date_of_service ASC, c1.id ASC ".
				 //"ORDER BY IFNULL(c23.date_of_service,c1.create_dt) ASC, c1.id ASC ".
				 "";
		//}
		//echo $sql."<br><br>";
		//exit;
		$res = sqlStatement($sql);// or die(mysql_error());
		return $res;
	}
	
	function getChartNotesTree($arrParam){
		
		if(count($arrParam)>0){
			$search=1;
			$srchValue=$arrParam[0];
			$finalizeVal=$arrParam[1];
			$fndByVal=$arrParam[2];
			$tstExmSearch=$arrParam[3];

		}else{
			$search=0;
		}
		
		$oAdmn = new Admn();
		$oPt = new Patient($this->pid);
		
		//Get Remote server Abbre --
		if(constant("REMOTE_SYNC") == 1 || constant("SHOW_SERVER_LOCATION") == 1){
			$arrRemoteServerAbbr = $oAdmn->getServerAbbr();
		}
		//Get Remote server Abbre --	
		$oTests = new TestInfo();
		$arrTestNm = $oTests->getTestNames();
		$arrTestNmShow = $oTests->get_tests_names_show();
		
		
		$ret = "";
		
		//get search form
		//$ret= $this->getSRSrchForm();
		
		//$retTests="<label id=\"sec_tests_hdr\" class=\"div_tr\">Tests</label>";
		//$retTests.="<div id=\"sec_tests\" >";		
		//$retExam="<div id=\"sec_exams\" >";
		
		//
		if(isset($this->pid) && !empty($this->pid)){
		//GET Exams -----------------------------------
		$arrAllCharts=array();
		if($search==1){
			if(!empty($srchValue)){
				$exmDtNewArr = $this->retrieveExamDetailsSearch($srchValue,$fndByVal,$tstExmSearch);
			}else{
				$tmp = $this->retrieveExamDetailsSearch_v2($finalizeVal);
				$exmDtNewArr = $tmp["dos"];
				$fIdNewArr = $tmp["id"];
			}
			
			if(!$exmDtNewArr) {
				$retExam.="No record found";
				$rez = false;
			}else{					
				$rez = $this->getChartNotesInfoSearch($exmDtNewArr,$finalizeVal);					
			}
		}else{
			$rez = $this->getChartNotesInfo("","0");
		}
		
		//LOOP
		//echo mysql_num_rows($rez);
		for($i=1;$rez != false && $row = sqlFetchArray($rez);$i++){
			$id = $row["id"];
			$releaseNum = $row["releaseNumber"];
			$dbDos=$row["date_of_service"];
			if(empty($row["date_of_service"]) || $row["date_of_service"]=="0000-00-00"){$dbDos=wv_formatDate($row["create_dt"],0,0,'insert');}
			$dos2 = wv_formatDate($dbDos);
			$dos = wv_formatDate($dbDos,1); //$row["date_of_service2"] ; //FormatDate_show($row["date_of_service"]);
			$memoId = $row["memo_id"];
			$purge_status=$row["purge_status"];
			$contactLensId = $row["contact_lens_id"];
			$providerId = $row["providerId"];
			if($row["finalize"]==1 && !empty($row["finalizerId"])){ 
				$oUsr = new User($row["finalizerId"]);
				if($oUsr->getUType(1) == 1){
					$providerId = $row["finalizerId"];
				}	
			}
			
			$arrProviderIntials = array();
			if(!empty($providerId)){
				$oUsr = new User($providerId);
				$arrProviderIntials = $oUsr->getName(2);
			}
			
			//ConsignerId 
			$cosigner_id = $row["cosigner_id"];
			$serverId = $row["serverId"]; //serverId
			$chartfacilityid = $row["facilityid"];
			
			$chartStatus = ($row["finalize"] == "1") ? "Final" : "Active"; //"Finalized"
			$chartStatus2 = (($row["finalize"] == "1") || (trim($chartStatus) == "Final")) ? "" : $chartStatus ;
			$chartStatus2 = (trim($chartStatus2) == "Final") ? "" : $chartStatus2;
				
			//Chart Notes type
			$typeChart = (!empty($memoId)) ? "Memo Chart Note" : "Chart Note";
			$ptVisitTestTmp = $ptVisitTestTmp2 = "";
			if($row["finalize"] == "1"){
				if(!empty($row["ptVisit"])){
					$ptVisitTestTmp = addslashes($row["ptVisit"]);
				}else if(!empty($row["testing"])){
					$ptVisitTestTmp = addslashes($row["testing"]);
				}else{
					$ptVisitTestTmp = $typeChart;
				}

			}else{
				$ptVisitTestTmp = ($row["ptVisit"] == "CEE") ? "CEE" : "".$ptVisitTestTmp;
			}
			$ptVisitTest = (strlen($ptVisitTestTmp) > 10) ? substr($ptVisitTestTmp,0,10).".." : $ptVisitTestTmp;
			//echo $ptVisitTestTmp." : ".$ptVisitTest;

			//Title
			$ptVisitTestTmp2 = (!empty($row["ptVisit"])) ? addslashes("".$row["ptVisit"]) : "";
			if((!empty($row["testing"]))){
				$ptVisitTestTmp2 .= ( !empty($ptVisitTestTmp2) ) ? addslashes(" - ".$row["testing"]) : addslashes($row["testing"]);
			}
			
			//Chart Note Server Abbr.---			
			$serverAbbr=$initProvId="";
			if(constant("REMOTE_SYNC") == 1 || constant("SHOW_SERVER_LOCATION") == 1){//tufts					
				if(!empty($arrProviderIntials[1])){ $initProvId = "-".$arrProviderIntials[1]; }
				if(!empty($arrRemoteServerAbbr[$serverId])){	
					$serverAbbr = "-".$arrRemoteServerAbbr[$serverId]."";	
				}else{ 
					
					$chartfacilityid_sc =  $oPt->getChartFacilityFromSchApp($dbDos, $providerId);
					if(!empty($chartfacilityid_sc)){$chartfacilityid=$chartfacilityid_sc;}
					
					if(!empty($chartfacilityid)){
						$ofac=new Facility($chartfacilityid);
						$serverAbbr = $ofac->getFacilityAbbr();
					}else{ $serverAbbr = "";  }
					if(!empty($serverAbbr)){  $serverAbbr="-".$serverAbbr; }
					
					/*
					if(!empty($chartfacilityid)){
						$serverAbbr = getFacilityAbbr($chartfacilityid);
						if(!empty($serverAbbr)){  $serverAbbr="-".$serverAbbr; }
					}else{
						//if server id and facility id is empty, get from scheduler appointment if exists
						
						
					}*/
				}
			//}else if(!empty($chartfacilityid)){ //facility id
			}else{
				
				//if server id and facility id is empty, get from scheduler appointment if exists
				$chartfacilityid_sc =  $oPt->getChartFacilityFromSchApp($dbDos, $providerId);					
				if(!empty($chartfacilityid_sc)){$chartfacilityid=$chartfacilityid_sc;}
				if(!empty($chartfacilityid)){
					$ofac=new Facility($chartfacilityid);
					$serverAbbr = $ofac->getFacilityAbbr();
				}else{ $serverAbbr = "";  }
				if(!empty($serverAbbr)){  $serverAbbr="-".$serverAbbr; }	
				
				/*
				$serverAbbr = getFacilityAbbr($chartfacilityid);
				if(!empty($serverAbbr)){  $serverAbbr="-".$serverAbbr; }
				*/
			}
			//Chart Note Server Abbr.---
			
			
			
			$arrPlans = array();
			$oChartAmendment = new ChartAmendment($this->pid, $id);
			$isAmended = $oChartAmendment->hasAmendments();
			if($isAmended == true){
				$arrPlans[] = "Amendment";
			}
			
			if($releaseNum == "1"){
				if($search==1){
					if(!empty($row["vis_id"])){
						$arrPlans[] = "Vision";
					}
					if(!empty($row["pupil_id"])){
						$arrPlans[] = "Pupil";
					}
					if(!empty($row["eom_id"])){
						$arrPlans[] = "EOM";
					}
					if(!empty($row["ee_id"])){
						$arrPlans[] = "External";
					}
					if(!empty($row["la_id"])){
						$arrPlans[] = "L&A";
					}
					if(!empty($row["iop_id"])){
						$arrPlans[] = "IOP/Gonio";
					}
					if(!empty($row["sle_id"])){
						$arrPlans[] = "SLE";
					}
					if(!empty($row["optic_id"])){
						$arrPlans[] = "Opt.Nev/Disc";
					}
					if(!empty($row["rv_id"])){
						$arrPlans[] = "Fundus Exam";
					}
					if(!empty($row["assessId"])){
						$arrPlans[] = "Assessment & Plan";
					}
					if(!empty($row["amslerId"])){
						$arrPlans[] = "Amsler Grid";
					}												
					if(!empty($row["cvf_id"])){
						$arrPlans[] = "CVF";
					}
					if(!empty($row["dip_id"])){
						$arrPlans[] = "Diplopia";
					}
				}
				
				//Get Other Tests related to same date

				$arrDosTest = array();
				//$arrTestNm = array("VF","HRT","Pachy","IVFA","Fundus","Topography","External/Anterior","OCT");
				//for($k=0;$k<8;$k++){
				//pttest
				$oPtTest = new PtTest($this->pid, $id);
				$arrDosTest = $oPtTest->getAllTestofDos($dbDos);
			
			}
			
			$idActive = (($releaseNum == "1")) ? "id=\"ulActive$id\" " : "";
			//Purge ---
			if($purge_status==1){
				$purge_statusStrike= "purged";
				$purge_user = "<span class=\"purge_user\">".$arrProviderIntials[1]."</span>";
			}else{
				$purge_statusStrike=$purge_user ="";
			}
			//Purge ---
			
			//is Chart Edit ---
			$oChartRecArc = new ChartRecArc($this->pid,$id,$this->uid);
			$lenEd = $oChartRecArc->getArcRecId();
			if(count($lenEd)>0){
				$clrVisit = "editdchart";
				$lastModDt = "(LM:".wv_formatDate($row["update_date"],1).")";
			}else{
				$clrVisit = "";
				$lastModDt = "";
			}
			//is Chart Edit ---
			
			// SELECTED Chart Notes
			$loadedChart="";
			if($id==$this->fid){
				$loadedChart = "loadedChart";
			}				
			// SELECTED Chart Notes
			
			//Highlight all DOS of Current user providerId + consignerId --
			if($providerId==$this->uid||$cosigner_id==$this->uid){
				$highlighted = "dosCurUsr";
			}else{
				$highlighted="";
			}				
			//Highlight all DOS of Current user providerId + consignerId --
			
			$idActiveLabel = "id=\"liActiveLabel$id\" "; // (($i == "1")) ? "id=\"liActiveLabel$id\" " : "";
			//$len = count($arrPlans);
			
			$arr_html_exam = array();
			
			$arr_html_exam["loadedChart"] = $loadedChart;
			$arr_html_exam["purge_statusStrike"] = $purge_statusStrike;
			$arr_html_exam["highlighted"] = $highlighted;
			$arr_html_exam["dos"] = $dos;
			$arr_html_exam["idActiveLabel"] = $idActiveLabel;
			$arr_html_exam["clrVisit"] = $clrVisit;
			
			$arr_html_exam["dos2"] = $dos2;
			$arr_html_exam["chartStatus"] = $chartStatus;
			$arr_html_exam["ptVisitTestTmp2"] = $ptVisitTestTmp2;
			$arr_html_exam["lastModDt"] = $lastModDt;
			$arr_html_exam["temp"] = $temp;
			$arr_html_exam["typeChart"] = $typeChart;
			$arr_html_exam["form_id"] = $id;
			
			$arr_html_exam["chartStatus"] = $chartStatus;
			$arr_html_exam["releaseNum"] = $releaseNum;
			$arr_html_exam["chartStatus2"] = $chartStatus2;
			$arr_html_exam["ptVisitTest"] = $ptVisitTest;
			$arr_html_exam["initProvId"] = $initProvId;
			$arr_html_exam["serverAbbr"] = $serverAbbr;
			$arr_html_exam["purge_user"] = $purge_user;
			$arr_html_exam["arrPlans"] = $arrPlans;
			//$arr_html_exam["x"] = $x;			
			
			/*
			$echo="";
			
			$echo.="<ul class=\"list-unstyled\" >".
						"<li class=\"".$loadedChart."\">".
						"<span class=\"".$purge_statusStrike."\">".
						"<span class=\"".$highlighted."\" onclick=\"sl_showopts(this);\" >".$dos."</span>".
						"<span ".$idActiveLabel." class=\"vInfo ".$clrVisit."\" ".
						"title=\"".$dos2." ".$chartStatus." ".
						"".$ptVisitTestTmp2." ".$lastModDt."\" ".
					"onclick=\"".$temp."showFinalize('".$typeChart."','".$id."','".trim($chartStatus)."','".$releaseNum."');\" > ".
						"".$chartStatus2." ".$ptVisitTest."".$initProvId."".$serverAbbr."</span>".
						"</span>".
						$purge_user.
						"";
			//Amendments --
			if(in_array("Amendment", $arrPlans)){
				$clk = $temp = "";
				$clk = "onclick=\"".$temp."showFinalize('Amendment','".$id."','".trim($chartStatus)."','".$releaseNum."')\"";

				$echo.="<div class=\"li_amend\" ".$clk.">".
						"Amendment</div>";

			}
			//Amendments --

			$echo.="<ul $idActive class=\"list-unstyled\">";

			// FormId Exams --
			if(count($arrPlans)>0){
				foreach($arrPlans as $keyPln=>$valPln){
					if($valPln=="Amendment")continue;
					$clk = $temp = "";
					$clk = "onclick=\"".$temp."showFinalize('".$valPln."','".$id."','".trim($chartStatus)."','".$releaseNum."')\"";
					$echo.="<li ".$clk.">".$valPln."</li>";
				}
			}
			*/
			// FormId Exams --
			
			//Show Tests
			$arr_exm_test = array();
			$len = count($arrTestNm);
			for($j=0;$j<$len;$j++){
				//--
				//Show Other Tests related to same date

				if(isset($arrDosTest[$arrTestNm[$j]])){

					if($arrTestNm[$j] != "Other"){

						$tmpArr = $arrDosTest[$arrTestNm[$j]];
						$ln_dt = count($tmpArr);
						//echo "CHK: ".$arrDosTest[$arrTestNm[$j]];
						//continue;

						for($k=0;$k<$ln_dt;$k++){

							$tmpTestId = !empty($tmpArr[$k]) ? $tmpArr[$k] : 0 ;
							/*
							$clk = "onclick=\""."showFinalize('".$arrTestNm[$j]."','0','".trim($chartStatus)."','".$releaseNum."',0,'".
								$tmpTestId."')\"";

							$echo.="<li ".$clk.">".
									$arrTestNm[$j].
									"</li>";
							*/		
							$arr_exm_test[] = array("name"=>$arrTestNm[$j], "testid" =>$tmpTestId, "chartStatus"=>$chartStatus,"releaseNum"=>$releaseNum );		
									
						}

					}else{

						$tmpArr = $arrDosTest[$arrTestNm[$j]];

						//echo "CHK: ".$arrDosTest[$arrTestNm[$j]];
						//continue;
						if(count($tmpArr) > 0){

							foreach($tmpArr as $key => $val){

								//echo "CHK: ".$tmpArr;
								//print_r($val);
								//continue;

								$tmpSubArrNM = $key;
								$tmpSubArr = $val;
								$ln_dt = count($tmpSubArr);
								for($k=0;$k<$ln_dt;$k++){
									$tmpTestId = !empty($tmpSubArr[$k]) ? $tmpSubArr[$k] : 0 ;
									/*
									$clk = "onclick=\""."showFinalize('".$arrTestNm[$j]."','0','".trim($chartStatus)."','".$releaseNum."',0,'".
										$tmpTestId."')\"";

									$echo.="<li ".$clk." >".
											$tmpSubArrNM.
											"</li>";
									*/		
									$arr_exm_test[] = array("name"=>$arrTestNm[$j], "id" =>$tmpTestId, "chartStatus"=>$chartStatus,"releaseNum"=>$releaseNum,"tmpSubArrNM"=>$tmpSubArrNM );		
								}
							}
						}
					}
				}
				
				$arr_html_exam["arr_exm_test"] = $arr_exm_test;

				//Show Other Tests related to same date
				//--			
			
			}
			
			/*
			$echo.="</ul>".
						"</li>".
						"</ul>";
			*/			
			$arrAllCharts[$dbDos.$i."0"] = $arr_html_exam;			
		
		}
		
		//Charts  Image
		$oPtScan = new PtScan($this->pid);
		$rez = $oPtScan->getFinalizedChartImagesRecords();
		for(;$row = sqlFetchArray($rez);$i++){

			$id = $row["scan_doc_id"];
			$dos=$row["prev_finalized_date"];
			$dos2=$row["prev_finalized_date_FullYear"];
			$dbDos = $row["chart_note_date"];				
			$docTitle = !empty($row["doc_title"]) ? $row["doc_title"] : $row["pdf_url"] ;				
			$docTitle2 = $docTitle;
			$docTitle = (strlen($docTitle)>22) ? substr($docTitle,0,20).".." : $docTitle;
			$idActive = "";
			$idActiveLabel = "";
			$chartStatus = "Final";//"Finalized";
			$chartStatus2 = "".$docTitle;
			$ptVisitTest="";
			$arrPlans = array();
			$arrPlans[] = "Chart Note";
			$len = count($arrPlans);
			
			$chartStatus2 = preg_replace('/[^A-Za-z0-9\-\_]/', '', $chartStatus2);
			$docTitle2 = preg_replace('/[^A-Za-z0-9\-\_]/', '', $docTitle2);

			if(empty($dos)){
				$dbDos = date("Y-m-d");
				$dos =  wv_formatDate($dbDos);
				$dos2 = $dos;
			}
			
			$clk = $temp = "";
			/*
			$clk = "onclick=\"".$temp."showPrevChartsImags('ChartImg".$i."', '".$id."')\"";
			
			$echo="";
			$echo.="<ul class=\"list-unstyled\" >".
					"<li title=\"".$dos2."\">&nbsp;<span onclick=\"sl_showopts(this);\">".$dos."</span>".
					"<span ".$idActiveLabel." class=\"vInfo\" title=\"".$docTitle2."\" ".$clk."> ".$chartStatus2."".$ptVisitTest."</span>";
			*/		
					//"<ul $idActive class=\"ulFL\">";
			//for($j=0;$j<$len;$j++){
				//$clk = $temp = "";
				//if(($row["finalize"] == "1") || (!empty($_SESSION["finalize_id"]))){
				/*if((!empty($_SESSION["finalize_id"]) && ($id == $_SESSION["finalize_id"]))){
				$temp = "return checkPCN();";
				}*/
				//$clk = "onclick=\"".$temp."showPrevChartsImags('ChartImg".$i."', '".$id."')\"";
				//}
				//$echo.="<li ".$clk." >".$arrPlans[$j]."</li>";
			//}

			//$echo.="</ul>";
			/*$echo.="</li>".
				   "</ul>";*/
				   
				   
			$arr_html_exam = array();			
			$arr_html_exam["ChartImg"] = "ChartImg".$i;
			$arr_html_exam["dos2"] = $dos2;
			$arr_html_exam["scan_id"] = $id;
			$arr_html_exam["dos"] = $dos;
			$arr_html_exam["idActiveLabel"] = $idActiveLabel;
			$arr_html_exam["docTitle2"] = $docTitle2;
			$arr_html_exam["chartStatus2"] = $chartStatus2;
			$arr_html_exam["ptVisitTest"] = $ptVisitTest;			   
			

			$arrAllCharts[$dbDos.$i."1"] = $arr_html_exam;
		}
		//Sort Chart
		krsort($arrAllCharts);
		//$retExam .= implode("", $arrAllCharts);		
		//GET Exams -----------------------------------
		
		//GET Tests -----------------------------------
		$arPtTests=array();
		$oPtTest = new PtTest($this->pid);
		if($search==1){
			if(!empty($srchValue)){
				$arrAllTests = $this->getAllTestofPtSearch($srchValue,$fndByVal,1);
			}else{
				$arrAllTests = $this->getAllTestofPtSearch_v2($finalizeVal,$fIdNewArr,1);
			}

		}else{
			$arrAllTests = $oPtTest->getAllTestofPt(1);
		}
		
		// is pt test uninterpreted
		$is_pt_test_uninterpreted = $oPtTest->pt_test_uninterpreted();
		
		$cTests=0;
		if(count($arrAllTests) > 0){
			//$retTests = print_r($arrAllTests,1);
			krsort($arrAllTests);
			
			/* -- ByDate of Test --*/
			foreach($arrAllTests as $keyAll => $valAll){
				$tmpDt = $keyAll;
				$tmpShowDt = 1;
				/*
				$retTests.="<ul id=\"treemenu_test".++$cTests."\" class=\"list-unstyled\" >".
							"<li >".wv_formatDate($tmpDt).
							"<ul ".$idActive." class=\"list-unstyled\" >";
				*/			
				$dtTest = wv_formatDate($tmpDt);			
				$arPtTests[$dtTest] = array();			
				/* -- ByName of Test --*/
				
				foreach($arrTestNm as $key => $val){

					if(isset($arrAllTests[$tmpDt][$val])){

						//if othr
						if($val == "Other" || $val=='TemplateTests' || $val=='CustomTests'){

							if(count($arrAllTests[$tmpDt][$val]) > 0){
								foreach($arrAllTests[$tmpDt][$val] as $key_othr => $val_othr ){
									$ln_othr = count($val_othr);
									$tst_name = $key_othr;

									//echo $tst_name." - ".$ln_othr;

									for($q=0;$q<$ln_othr;$q++){
										$tst_id = $arrAllTests[$tmpDt][$val][$tst_name][$q]["id"];
										$tst_dt = trim(wv_formatDate($arrAllTests[$tmpDt][$val][$tst_name][$q]["dt"]));
										$tst_phy = $arrAllTests[$tmpDt][$val][$tst_name][$q]["phy"];
										$tst_prfBy = $arrAllTests[$tmpDt][$val][$tst_name][$q]["prfBy"];
										$tst_purgd = $arrAllTests[$tmpDt][$val][$tst_name][$q]["purged"];
										/*
										if($q == 0){ //First item then show name of test first
											$retTests.="<div style=\"background-color:#ccd6e0;padding-left:10px;margin-bottom:1px;\" >";
											$retTests.="".
													"<span style=\"font-weight:bold; color:#CC3300;cursor:hand;\" >".
														$tst_name.
													"</span>";
											$retTests.="</div>";
										}
										*/
										//Flags
										$flgClass ="";

										if(!empty($tst_phy)){
											$flgClass = "examTechFlagGreen";
										}else if(!empty($tst_prfBy)){
											$flgClass = "examTechFlag";
										}

										//Show Dt Once
										if($tmpShowDt == 1){
											$tmpShowDt = 0;
										}else{
											$tst_dt = "";
										}
										//Test
										//$clk = "onclick=\""."showFinalize('".addslashes($val)."','0','0','0','0','".$tst_id."')\"";
										
										//Purge ---
										if(!empty($tst_purgd)){
											$oUsr = new User($tst_purgd);											
											$tmpUsr = $oUsr->getName(2);
											$purge_statusStrike= "purged";
											$purge_user = "<span class=\"purge_user\">".$tmpUsr[1]."</span>";
										}else{
											$purge_statusStrike=$purge_user ="";
										}
										//Purge ---
										
										$ar_tmp_info = array();
										$ar_tmp_info["val"] = $val;
										$ar_tmp_info["tst_id"] = $tst_id;
										$ar_tmp_info["purge_statusStrike"] = $purge_statusStrike;
										$ar_tmp_info["purge_user"] = $purge_user;
										$ar_tmp_info["tst_name"] = $tst_name;
										$ar_tmp_info["flgClass"] = $flgClass;
										//$ar_tmp_info["x"] = $x;
										$arPtTests[$dtTest][] = $ar_tmp_info;
										
										
										//$retTests.="<li >";
										//$retTests.="<div style=\"background-color:#ccd6e0;padding-left:10px;margin-bottom:1px;\" >";
										/*
										$retTests.="<span class=\"hand_cur\" style=\"width:60px;\" >".
													$tst_dt."</span>";
										*/
										/*
										$retTests.="<span class=\"".$purge_statusStrike."\" >";
										$retTests.="<span ".$clk." >".
														$tst_name.
													"</span>";
										$retTests.="".
										"".
										"<span id=\""."flgTest_".$tst_id."\" class=\"".$flgClass."\" ></span>".
										"";
										$retTests.="</span>";
										$retTests.= $purge_user;
										//$retTests.="</div>";
										$retTests.="</li>";
										*/
										
									}
								}
							}

						}else{
							//else
							$lm_tst = count($arrAllTests[$tmpDt][$val]);
							$tst_name = !empty($arrTestNmShow[$val]) ? $arrTestNmShow[$val] : $val ;
							if($tst_name == "External/Anterior"){
								$tst_name =	"Ext/Ant";
							}						

							for($p=0;$p<$lm_tst;$p++){
								$tst_id = $arrAllTests[$tmpDt][$val][$p]["id"];
								$tst_dt = trim(wv_formatDate($arrAllTests[$tmpDt][$val][$p]["dt"]));
								$tst_phy = $arrAllTests[$tmpDt][$val][$p]["phy"];
								$tst_prfBy = $arrAllTests[$tmpDt][$val][$p]["prfBy"];
								$tst_purgd = $arrAllTests[$tmpDt][$val][$p]["purged"];
								$tst_type = $arrAllTests[$tmpDt][$val][$p]["test_type"];
								if(!empty($tst_type)) $tst_name = $tst_name." - ".$tst_type;
								/*
								if($p == 0){ //First item then show name of test first
									$retTests.="<div style=\"background-color:#ccd6e0;padding-left:10px;margin-bottom:1px;\" >";
									$retTests.="".
											"<span style=\"font-weight:bold; color:#CC3300;cursor:hand;\" >".
												$tst_name.
											"</span>";
									$retTests.="</div>";
								}*/

								//Flags
								$flgClass ="";

								if(!empty($tst_phy)){
									$flgClass = "examTechFlagGreen";
								}else if(!empty($tst_prfBy)){
									$flgClass = "examTechFlag";
								}

								//Show Dt Once
								if($tmpShowDt == 1){
									$tmpShowDt = 0;
								}else{
									$tst_dt = "";
								}
								
								//Purge ---
								if(!empty($tst_purgd)){
									$oUsr = new User($tst_purgd);									
									$tmpUsr = $oUsr->getName(2);
									$purge_statusStrike= "purged";
									$purge_user = "<span class=\"purge_user\">".$tmpUsr[1]."</span>";
								}else{
									$purge_statusStrike=$purge_user ="";
								}
								//Purge ---
								
								$ar_tmp_info = array();
								$ar_tmp_info["val"] = $val;
								$ar_tmp_info["tst_id"] = $tst_id;
								$ar_tmp_info["purge_statusStrike"] = $purge_statusStrike;
								$ar_tmp_info["purge_user"] = $purge_user;
								$ar_tmp_info["tst_name"] = $tst_name;
								$ar_tmp_info["flgClass"] = $flgClass;
								//$ar_tmp_info["x"] = $x;
								$arPtTests[$dtTest][] = $ar_tmp_info;
								
								/*
								//Test
								$clk = "onclick=\""."showFinalize('".addslashes($val)."','0','0','0','0','".$tst_id."')\"";
								$retTests.="<li >";
								//$retTests.="<div style=\"background-color:#ccd6e0;padding-left:10px;margin-bottom:1px;\" >";
								//$retTests.="<span class=\"hand_cur\" style=\"width:60px;\" >".$tst_dt."</span>";
								$retTests.="<span class=\"".$purge_statusStrike."\" >";
								$retTests.="".
										   "<span ".$clk." >".
												$tst_name.
										   "</span>";
								$retTests.="".
								"".
								"<span id=\""."flgTest_".$tst_id."\" class=\"".$flgClass."\" ></span>".
								"";
								$retTests.="</span>";
								$retTests.= $purge_user;
								//$retTests.="</div>";
								$retTests.="</li>";
								*/
								
							}
							// end else
						}
					}
				}
				/* -- ByName of Test --*/			
				
				/*
				$retTests.="</ul>".
						   "</li>".
						   "</ul>";
				*/		   
			
			}
			/* -- ByDate of Test --*/
			
		}		
		//GET Tests -----------------------------------
		}//block patient id
		
		//$retTests .="</div>";
		//$retExam .="</div>";
		//$ret .=$retExam.$retTests;
		//return array($ret,$i-1,$retTests,$cTests);
		
		//--
		$ret="";
		ob_start();
		include($GLOBALS['incdir']."/chart_notes/view/pt_forms.php");
		$out2 = ob_get_contents();
		ob_end_clean();
		$ret = $out2;
		//--
		
		return array($ret, $is_pt_test_uninterpreted);
		
	}	
	
	//tests
	//START FUNCTION TO SEARCH TESTS
	function getAllTestofPtSearch($exam_name,$fndByVal, $flgRemSync=0){

		$sql = "";
		$arr = array();
		$pId = $this->pid;
		if(empty($pId)){
			return "";
		}
		
		$oTestinfo = new TestInfo();
		$active = $oTestinfo->get_active_tests();

		//TEST
		$andProvdQry='';
		if($fndByVal=='Exam') {
			switch($exam_name) {
				case "VF":
				case "vf":
					if(in_array("VF", $active)){
					$sql = "SELECT vf_id AS tId,
							DATE_FORMAT(examDate, '%Y-%m-%d') AS eDt,
							DATE_FORMAT(examDate, '".get_sql_date_format('','y')."') AS dt,
							performedBy AS prfBy, phyName as phy,
							purged,formId
							FROM vf WHERE patientId='".$pId."' AND del_status='0' 
							ORDER BY examDate DESC, examTime DESC, vf_id DESC " ;
					$rez = sqlStatement($sql);
					for($a=0;$row=sqlFetchArray($rez);$a++){
						if(!empty($row["tId"])){
						
							$arr[$row["eDt"]]["VF"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
																"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
																"purged"=>$row["purged"]);
						}
					}
					}
				break;
					
				case "VF-GL":
				case "vf-gl":
					if(in_array("VF-GL", $active)){
					$sql = "SELECT vf_gl_id AS tId,
							DATE_FORMAT(examDate, '%Y-%m-%d') AS eDt,
							DATE_FORMAT(examDate, '".get_sql_date_format('','y')."') AS dt,
							performedBy AS prfBy, phyName as phy,
							purged,formId
							FROM vf_gl WHERE patientId='".$pId."' AND del_status='0' 
							ORDER BY examDate DESC, examTime DESC, vf_gl_id DESC " ;
					$rez = sqlStatement($sql);
					for($a=0;$row=sqlFetchArray($rez);$a++){
						if(!empty($row["tId"])){
						
							$arr[$row["eDt"]]["VF-GL"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
																"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
																"purged"=>$row["purged"]);
						}
					}
					}
				break;
					
				case "HRT":
				case "hrt":
					if(in_array("HRT", $active)){
					$sql = "SELECT nfa.nfa_id AS tId,
							DATE_FORMAT(examDate, '%Y-%m-%d') AS eDt,
							DATE_FORMAT(nfa.examDate, '".get_sql_date_format('','y')."') AS dt,
							nfa.performBy AS prfBy, nfa.phyName as phy, nfa.purged,form_id
							FROM nfa WHERE nfa.patient_id='".$pId."' AND del_status='0' 
							ORDER BY examDate DESC, examTime DESC, nfa_id DESC " ;
					$rez = sqlStatement($sql);
					for($a=0;$row=sqlFetchArray($rez);$a++){
						if(!empty($row["tId"])){
						
							$arr[$row["eDt"]]["HRT"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
																"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
																"purged"=>$row["purged"]);
						}
					}
					}
				break;
			//print_r($arr);
				  case "OCT":
				  case "oct":
					if(in_array("OCT", $active)){
						$sql = "SELECT oct_id AS tId,
								DATE_FORMAT(examDate, '%Y-%m-%d') AS eDt,
								DATE_FORMAT(examDate, '".get_sql_date_format('','y')."') AS dt,
								performBy AS prfBy, phyName as phy, purged,scanLaserOct,form_id
								FROM oct WHERE patient_id='".$pId."' AND del_status='0' 
								ORDER BY examDate DESC, examTime DESC, oct_id DESC " ;
						$rez = sqlStatement($sql);
						for($a=0;$row=sqlFetchArray($rez);$a++){
							if(!empty($row["tId"])){
								
								if(!empty($row["scanLaserOct"])){
									if($row["scanLaserOct"]=="3"){
										$row["scanLaserOct"]="AS";
									}else if($row["scanLaserOct"]=="2"){
										$row["scanLaserOct"]="ON";	
									}else if($row["scanLaserOct"]=="1"){
										$row["scanLaserOct"]="R";
									}					
								}

								$arr[$row["eDt"]]["OCT"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
																	"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
																	"purged"=>$row["purged"],"test_type"=>$row["scanLaserOct"]);
							}
						}
					}	
				 break;
				 
				 case "OCT-RNFL":
				  case "oct-rnfl":
					if(in_array("OCT-RNFL", $active)){
						$sql = "SELECT oct_rnfl_id AS tId,
								DATE_FORMAT(examDate, '%Y-%m-%d') AS eDt,
								DATE_FORMAT(examDate, '".get_sql_date_format('','y')."') AS dt,
								performBy AS prfBy, phyName as phy, purged,scanLaserOct_rnfl,form_id
								FROM oct_rnfl WHERE patient_id='".$pId."' AND del_status='0' 
								ORDER BY examDate DESC, examTime DESC, oct_rnfl_id DESC " ;
						$rez = sqlStatement($sql);
						for($a=0;$row=sqlFetchArray($rez);$a++){
							if(!empty($row["tId"])){

								$arr[$row["eDt"]]["OCT-RNFL"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
																	"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
																	"purged"=>$row["purged"]);
							}
						}
					}	
				 break;
				 
				 case "GDX":
				 case "gdx":
					if(in_array("GDX", $active)){
						$sql = "SELECT gdx_id AS tId,
								DATE_FORMAT(examDate, '%Y-%m-%d') AS eDt,
								DATE_FORMAT(examDate, '".get_sql_date_format('','y')."') AS dt,
								performBy AS prfBy, phyName as phy, purged, form_id
								FROM test_gdx WHERE patient_id='".$pId."' AND del_status='0' 
								ORDER BY examDate DESC, examTime DESC, gdx_id DESC " ;
						$rez = sqlStatement($sql);
						for($a=0;$row=sqlFetchArray($rez);$a++){
							if(!empty($row["tId"])){
							
								$arr[$row["eDt"]]["GDX"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
																	"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
																	"purged"=>$row["purged"]);
							}
						}
					}	
				 break;
				 
				 case "Pachy":
				 case "pachy":
					if(in_array("Pachy", $active)){
						$sql = "SELECT pachy_id AS tId,
								DATE_FORMAT(examDate, '%Y-%m-%d') AS eDt,
								DATE_FORMAT(examDate, '".get_sql_date_format('','y')."') AS dt,
								performedBy AS prfBy, phyName as phy, purged, formId
								FROM pachy WHERE patientId='".$pId."' AND del_status='0' 
								ORDER BY examDate DESC, examTime DESC, pachy_id DESC " ;
						$rez = sqlStatement($sql);
						for($a=0;$row=sqlFetchArray($rez);$a++){
							if(!empty($row["tId"])){
							
								$arr[$row["eDt"]]["Pachy"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
																		"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
																		"purged"=>$row["purged"]);
							}
						}
					}	
				break;

				case "IVFA":
				case "ivfa":
					if(in_array("IVFA", $active)){
						$sql = "SELECT vf_id AS tId,
								DATE_FORMAT(exam_date, '%Y-%m-%d') AS eDt,
								DATE_FORMAT(exam_date, '".get_sql_date_format('','y')."') AS dt,
								performed_by AS prfBy, phy as phy, purged, form_id
								FROM ivfa WHERE patient_id='".$pId."' AND del_status='0' 
								ORDER BY exam_date DESC, examTime DESC, vf_id DESC " ;
						$rez = sqlStatement($sql);
						for($a=0;$row=sqlFetchArray($rez);$a++){
							if(!empty($row["tId"])){
							
								$arr[$row["eDt"]]["IVFA"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
																		"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
																		"purged"=>$row["purged"]);
							}
						}
					}	

				break;

				case "ICG":
				case "icg":
					if(in_array("ICG", $active)){
						$sql = "SELECT icg_id AS tId,
								DATE_FORMAT(exam_date, '%Y-%m-%d') AS eDt,
								DATE_FORMAT(exam_date, '".get_sql_date_format('','y')."') AS dt,
								performed_by AS prfBy, phy as phy, purged, form_id
								FROM icg WHERE patient_id='".$pId."' AND del_status='0' 
								ORDER BY exam_date DESC, examTime DESC, icg_id DESC " ;
						$rez = sqlStatement($sql);
						for($a=0;$row=sqlFetchArray($rez);$a++){
							if(!empty($row["tId"])){
							
								$arr[$row["eDt"]]["ICG"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
																		"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
																		"purged"=>$row["purged"]);
							}
						}
					}	

				break;

				case "Fundus":
				case "fundus":	
				case "disc":	
					if(in_array("Fundus", $active)){	
					$sql = "SELECT disc_id AS tId,
								DATE_FORMAT(examDate, '%Y-%m-%d') AS eDt,
								DATE_FORMAT(examDate, '".get_sql_date_format('','y')."') AS dt,
								performedBy AS prfBy, phyName as phy, purged,fundusDiscPhoto, formId
								FROM disc WHERE patientId='".$pId."' AND del_status='0' 
								ORDER BY examDate DESC, examTime DESC, disc_id DESC " ;
						$rez = sqlStatement($sql);
						for($a=0;$row=sqlFetchArray($rez);$a++){
							if(!empty($row["tId"])){								
							
								if(!empty($row["fundusDiscPhoto"])){
									if($row["fundusDiscPhoto"]=="1"){
										$row["fundusDiscPhoto"]="DP";
									}else if($row["fundusDiscPhoto"]=="2"){
										$row["fundusDiscPhoto"]="MP";
									}else if($row["fundusDiscPhoto"]=="3"){
										$row["fundusDiscPhoto"]="RP";
									}
								}
								
								$arr[$row["eDt"]]["Fundus"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
																		"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
																		"purged"=>$row["purged"],
																		"test_type"=>$row["fundusDiscPhoto"]);
							}
						}
					}

				break;
				case "External/Anterior":
				case "external/anterior":
					if(in_array("External/Anterior", $active)){
						$sql = "SELECT disc_id AS tId,
								DATE_FORMAT(examDate, '%Y-%m-%d') AS eDt,
								DATE_FORMAT(examDate, '".get_sql_date_format('','y')."') AS dt,
								performedBy AS prfBy, phyName as phy, purged,fundusDiscPhoto, formId
								FROM disc_external WHERE patientId='".$pId."' AND del_status='0' 
								ORDER BY examDate DESC, examTime DESC, disc_id DESC " ;
						$rez = sqlStatement($sql);
						for($a=0;$row=sqlFetchArray($rez);$a++){
							if(!empty($row["tId"])){
							
								if(!empty($row["fundusDiscPhoto"])){
									if($row["fundusDiscPhoto"]=="1"){
										$row["fundusDiscPhoto"]="ES";
									}else if($row["fundusDiscPhoto"]=="2"){
										$row["fundusDiscPhoto"]="ASP";
									}
								}

								$arr[$row["eDt"]]["External/Anterior"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
																			"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
																			"purged"=>$row["purged"],"test_type"=>$row["fundusDiscPhoto"]);
							}
						}
					}	

				break;
				case "Topography":
				case "topography":
					if(in_array("Topography", $active)){
						$sql = "SELECT topo_id AS tId,
								DATE_FORMAT(examDate, '%Y-%m-%d') AS eDt,
								DATE_FORMAT(examDate, '".get_sql_date_format('','y')."') AS dt,
								performedBy AS prfBy, phyName as phy, purged, formId
								FROM topography WHERE patientId='".$pId."' AND del_status='0' 
								ORDER BY examDate DESC, examTime DESC, topo_id DESC " ;
						$rez = sqlStatement($sql);
						for($a=0;$row=sqlFetchArray($rez);$a++){
							if(!empty($row["tId"])){
								
								$arr[$row["eDt"]]["Topography"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
																			"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
																			"purged"=>$row["purged"]);
							}
						}
					}	

				break;				
				
				case "Laboratories":
				case "laboratories":
					if(in_array("Laboratories", $active)){
						$sql = "SELECT test_labs_id AS tId,
								DATE_FORMAT(examDate, '%Y-%m-%d') AS eDt,
								DATE_FORMAT(examDate, '".get_sql_date_format('','y')."') AS dt,
								performedBy AS prfBy, phyName as phy, test_labs AS subcat, purged, formId
								FROM test_labs WHERE patientId='".$pId."' AND del_status='0' 
								ORDER BY test_labs ASC, examDate DESC, examTime DESC, test_labs_id DESC " ;
						$rez = sqlStatement($sql);
						for($a=0;$row=sqlFetchArray($rez);$a++){

							if(!empty($row["tId"])){
							
								$arr[$row["eDt"]]["Labs"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
																			"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
																			"purged"=>$row["purged"]);
							}
						}
					}
				break;
				case "A/Scan":
				case "a/scan":
					if(in_array("A/Scan", $active)){
						$sql = "SELECT surgical_id AS tId,
								DATE_FORMAT(examDate, '%Y-%m-%d') AS eDt,
								DATE_FORMAT(examDate, '".get_sql_date_format('','y')."') AS dt,
								performedByOD AS prfBy, signedById as phy, purged, form_id
								FROM surgical_tbl WHERE patient_id ='".$pId."' AND del_status='0' 
								ORDER BY examDate DESC, examTime DESC, surgical_id DESC " ;
						$rez = sqlStatement($sql);
						for($a=0;$row=sqlFetchArray($rez);$a++){

							if(!empty($row["tId"])){
							
								$arr[$row["eDt"]]["A/Scan"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
																	"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
																	"purged"=>$row["purged"]);
							}
						}
					}	

				break;
				
				case "IOL Master":
				case "iol master":
					if(in_array("IOL Master", $active)){
						$sql = "SELECT iol_master_id AS tId,
								DATE_FORMAT(examDate, '%Y-%m-%d') AS eDt,
								DATE_FORMAT(examDate, '".get_sql_date_format('','y')."') AS dt,
								performedByOD AS prfBy, signedById as phy, purged, form_id
								FROM iol_master_tbl WHERE patient_id ='".$pId."' AND del_status='0' 
								ORDER BY examDate DESC, examTime DESC, iol_master_id DESC " ;
						$rez = sqlStatement($sql);
						for($a=0;$row=sqlFetchArray($rez);$a++){

							if(!empty($row["tId"])){
							
								$arr[$row["eDt"]]["IOL Master"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
																	"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
																	"purged"=>$row["purged"]);
							}
						}
					}	

				break;

				case "B-Scan":
				case "b-scan":
					if(in_array("B-Scan", $active)){
						$sql = "SELECT test_bscan_id AS tId,
								DATE_FORMAT(examDate, '%Y-%m-%d') AS eDt,
								DATE_FORMAT(examDate, '".get_sql_date_format('','y')."') AS dt,
								performedBy AS prfBy, phyName as phy, purged, formId
								FROM test_bscan WHERE patientId='".$pId."' AND del_status='0' 
								ORDER BY  examDate DESC, examTime DESC, test_bscan_id DESC " ;
						$rez = sqlStatement($sql);
						for($a=0;$row=sqlFetchArray($rez);$a++){

							if(!empty($row["tId"])){
							
								$arr[$row["eDt"]]["B-Scan"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
																			"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
																			"purged"=>$row["purged"]);
							}
						}
					}
						//print_r($arr["Other"]);
						//exit;
				break;

				case "Cell Count":
				case "cell count":
					if(in_array("Cell Count", $active)){
						$sql = "SELECT test_cellcnt_id AS tId,
								DATE_FORMAT(examDate, '%Y-%m-%d') AS eDt,
								DATE_FORMAT(examDate, '".get_sql_date_format('','y')."') AS dt,
								performedBy AS prfBy, phyName as phy, purged, formId
								FROM test_cellcnt WHERE patientId='".$pId."' AND del_status='0' 
								ORDER BY examDate DESC, examTime DESC, test_cellcnt_id DESC " ;
						$rez = sqlStatement($sql);
						for($a=0;$row=sqlFetchArray($rez);$a++){

							if(!empty($row["tId"])){

								$arr[$row["eDt"]]["Cell Count"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
																			"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
																			"purged"=>$row["purged"]);
							}
						}
					}	

						//print_r($arr["Other"]);
						//exit;
				break;
				
				
				//case "Other":
				//case "other":
				default:
						$sql = "SELECT test_other_id AS tId,
								DATE_FORMAT(examDate, '%Y-%m-%d') AS eDt,
								DATE_FORMAT(examDate, '".get_sql_date_format('','y')."') AS dt,
								performedBy AS prfBy, phyName as phy, test_other AS subcat, purged, formId
								FROM test_other WHERE patientId='".$pId."' AND del_status='0' 
								ORDER BY test_other ASC, examDate DESC, examTime DESC, test_other_id DESC " ;
						$rez = sqlStatement($sql);
						for($a=0;$row=sqlFetchArray($rez);$a++){
						
							$test_other = ucfirst(strtolower($row["subcat"]));
							if(in_array($row["subcat"], $active)){
							if(!empty($row["tId"]) && stripos($test_other,$exam_name)!==false){							
							
								if(!isset($arr[$row["eDt"]]["Other"][$test_other])){
									$arr[$row["eDt"]]["Other"][$test_other] = array();
								}

								$arr[$row["eDt"]]["Other"][$test_other][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
																			"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
																			"purged"=>$row["purged"]);
							}
							}
						}

						//print_r($arr["Other"]);
						//exit;
				//break;
				//case "TemplateTests":
				//case "templatetests":
						$sql = "SELECT test_other_id AS tId,
								DATE_FORMAT(examDate, '%Y-%m-%d') AS eDt,
								DATE_FORMAT(examDate, '".get_sql_date_format('','y')."') AS dt,
								performedBy AS prfBy, phyName as phy, tn.temp_name AS subcat, tn.test_name AS subcat_tn, purged, formId
								FROM test_other JOIN tests_name tn ON (tn.id=test_other.test_template_id) 
								WHERE patientId='".$pId."' AND test_other.del_status='0' 
								ORDER BY test_other ASC, examDate DESC, examTime DESC, test_other_id DESC " ;
						$rez = sqlStatement($sql);
						for($a=0;$row=sqlFetchArray($rez);$a++){
						
							$test_template = ucfirst(strtolower($row["subcat"]));
							
							if(in_array($row["subcat_tn"], $active)){
							if(!empty($row["tId"]) && stripos($test_template,$exam_name)!==false){
							
								if(!isset($arr[$row["eDt"]]["TemplateTests"][$test_template])){
									$arr[$row["eDt"]]["TemplateTests"][$test_template] = array();
								}

								$arr[$row["eDt"]]["TemplateTests"][$test_template][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
																			"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
																			"purged"=>$row["purged"]);
							}
							}
						}
						
						//custom tests
						$sql = "SELECT test_id AS tId,
								DATE_FORMAT(examDate, '%Y-%m-%d') AS eDt,
								DATE_FORMAT(examDate, '".get_sql_date_format('','y')."') AS dt,
								performedBy AS prfBy, phyName as phy, tn.temp_name AS subcat, tn.test_name AS subcat_tn, purged, formId
								FROM test_custom_patient JOIN tests_name tn ON (tn.id=test_custom_patient.test_template_id) 
								WHERE patientId='".$pId."' AND test_custom_patient.del_status='0' AND test_template_id>0 
								ORDER BY test_other ASC, examDate DESC, examTime DESC, test_id DESC";
						$rez = sqlStatement($sql);
						for($a=0;$row=sqlFetchArray($rez);$a++){

							$test_template = ucfirst(strtolower($row["subcat"]));
							if(in_array($row["subcat_tn"], $active)){
							if(!empty($row["tId"]) && stripos($test_template,$exam_name)!==false ){
								if(!isset($arr[$row["eDt"]]["TemplateTests"][$test_template])){
									$arr[$row["eDt"]]["TemplateTests"][$test_template] = array();
								}

								$arr[$row["eDt"]]["TemplateTests"][$test_template][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
																					"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
																					"purged"=>$row["purged"]);
							}
							}
						}

						//print_r($arr["Other"]);
						//exit;
				break;

				//print_r($arr);

			}
		}else if($fndByVal=='Provider') {
			$searchKeywordArr = explode(",", $exam_name);
			$providerLastName = trim($searchKeywordArr[0]);
			$providerFirstName = trim($searchKeywordArr[1]);

			$andProvdQry = " AND users.lname LIKE '".$providerLastName."%'
							 AND users.fname LIKE '".$providerFirstName."%'";


			//TEST
			//	case "VF":
					if(in_array("VF", $active)){
					$sql = "SELECT vf.vf_id AS tId,
							DATE_FORMAT(vf.examDate, '%Y-%m-%d') AS eDt,
							DATE_FORMAT(vf.examDate, '".get_sql_date_format('','y')."') AS dt,
							vf.performedBy AS prfBy, vf.phyName as phy, vf.purged, vf.formId
							FROM vf, users WHERE vf.patientId='".$pId."' AND vf.del_status='0' 
							AND users.id = vf.performedBy
							".$andProvdQry."
							ORDER BY vf.examDate DESC, vf.examTime DESC, vf.vf_id DESC " ;
					$rez = sqlStatement($sql);
					for($a=0;$row=sqlFetchArray($rez);$a++){
						if(!empty($row["tId"])){
						
							$arr[$row["eDt"]]["VF"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
																"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
																"purged"=>$row["purged"]);
						}
					}
					}
			//	case "HRT":
					if(in_array("HRT", $active)){
					$sql = "SELECT nfa.nfa_id AS tId,
							DATE_FORMAT(nfa.examDate, '%Y-%m-%d') AS eDt,
							DATE_FORMAT(nfa.examDate, '".get_sql_date_format('','y')."') AS dt,
							nfa.performBy AS prfBy, nfa.phyName as phy, nfa.purged, nfa.form_id
							FROM nfa, users WHERE nfa.patient_id='".$pId."' AND nfa.del_status='0' 
							AND users.id = nfa.performBy
							".$andProvdQry."
							ORDER BY nfa.examDate DESC, nfa.examTime DESC, nfa.nfa_id DESC " ;
					$rez = sqlStatement($sql);
					for($a=0;$row=sqlFetchArray($rez);$a++){
						if(!empty($row["tId"])){
						
							$arr[$row["eDt"]]["HRT"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
																"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
																"purged"=>$row["purged"]);
						}
					}
					}
			//	case "OCT":
					if(in_array("OCT", $active)){
					$sql = "SELECT oct.oct_id AS tId,
							DATE_FORMAT(oct.examDate, '%Y-%m-%d') AS eDt,
							DATE_FORMAT(oct.examDate, '".get_sql_date_format('','y')."') AS dt,
							oct.performBy AS prfBy, oct.phyName as phy, oct.purged,oct.scanLaserOct, oct.form_id
							FROM oct, users WHERE oct.patient_id='".$pId."' AND oct.del_status='0' 
							AND users.id = oct.performBy
							".$andProvdQry."
							ORDER BY oct.examDate DESC, oct.examTime DESC, oct.oct_id DESC " ;
					$rez = sqlStatement($sql);
					for($a=0;$row=sqlFetchArray($rez);$a++){
						if(!empty($row["tId"])){						
						
							if(!empty($row["scanLaserOct"])){
								if($row["scanLaserOct"]=="3"){
									$row["scanLaserOct"]="AS";
								}else if($row["scanLaserOct"]=="2"){
									$row["scanLaserOct"]="ON";	
								}else if($row["scanLaserOct"]=="1"){
									$row["scanLaserOct"]="R";
								}					
							}
							
							$arr[$row["eDt"]]["OCT"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
																"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
																"purged"=>$row["purged"],"test_type"=>$row["scanLaserOct"]);
						}
					}
					}	
			//	case "OCT-RNFL":
					if(in_array("OCT-RNFL", $active)){
					$sql = "SELECT oct_rnfl.oct_rnfl_id AS tId,
							DATE_FORMAT(oct_rnfl.examDate, '%Y-%m-%d') AS eDt,
							DATE_FORMAT(oct_rnfl.examDate, '".get_sql_date_format('','y')."') AS dt,
							oct_rnfl.performBy AS prfBy, oct_rnfl.phyName as phy, 
							oct_rnfl.purged,oct_rnfl.scanLaserOct, oct_rnfl.form_id
							FROM oct_rnfl, users WHERE oct_rnfl.patient_id='".$pId."' AND oct_rnfl.del_status='0' 
							AND users.id = oct_rnfl.performBy
							".$andProvdQry."
							ORDER BY oct_rnfl.examDate DESC, oct_rnfl.examTime DESC, oct_rnfl.oct_rnfl_id DESC " ;
					$rez = sqlStatement($sql);
					for($a=0;$row=sqlFetchArray($rez);$a++){
						if(!empty($row["tId"])){
							
							$arr[$row["eDt"]]["OCT-RNFL"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
																"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
																"purged"=>$row["purged"]);
						}
					}		
					}
			//	case "GDX":
					if(in_array("GDX", $active)){
					$sql = "SELECT test_gdx.gdx_id AS tId,
							DATE_FORMAT(test_gdx.examDate, '%Y-%m-%d') AS eDt,
							DATE_FORMAT(test_gdx.examDate, '".get_sql_date_format('','y')."') AS dt,
							test_gdx.performBy AS prfBy, test_gdx.phyName as phy, test_gdx.purged, test_gdx.form_id
							FROM test_gdx, users WHERE test_gdx.patient_id='".$pId."' AND test_gdx.del_status='0' 
							AND users.id = test_gdx.performBy
							".$andProvdQry."
							ORDER BY test_gdx.examDate DESC, test_gdx.examTime DESC, test_gdx.gdx_id DESC " ;
					$rez = sqlStatement($sql);
					for($a=0;$row=sqlFetchArray($rez);$a++){
						if(!empty($row["tId"])){
						
							$arr[$row["eDt"]]["GDX"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
																"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
																"purged"=>$row["purged"]);
						}
					}
					}

			//	case "Pachy":
					if(in_array("Pachy", $active)){
					$sql = "SELECT pachy.pachy_id AS tId,
							DATE_FORMAT(pachy.examDate, '%Y-%m-%d') AS eDt,
							DATE_FORMAT(pachy.examDate, '".get_sql_date_format('','y')."') AS dt,
							pachy.performedBy AS prfBy, pachy.phyName as phy, pachy.purged, pachy.formId
							FROM pachy, users WHERE pachy.patientId='".$pId."' AND pachy.del_status='0' 
							AND users.id = pachy.performedBy
							".$andProvdQry."
							ORDER BY pachy.examDate DESC, pachy.examTime DESC, pachy.pachy_id DESC " ;
					$rez = sqlStatement($sql);
					for($a=0;$row=sqlFetchArray($rez);$a++){
						if(!empty($row["tId"])){
						
							$arr[$row["eDt"]]["Pachy"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
																	"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
																	"purged"=>$row["purged"]);
						}
					}
					}
					
			//	case "IVFA":
					if(in_array("IVFA", $active)){
					$sql = "SELECT ivfa.vf_id AS tId,
							DATE_FORMAT(ivfa.exam_date, '%Y-%m-%d') AS eDt,
							DATE_FORMAT(ivfa.exam_date, '".get_sql_date_format('','y')."') AS dt,
							ivfa.performed_by AS prfBy, ivfa.phy as phy, ivfa.purged, ivfa.form_id
							FROM ivfa, users WHERE ivfa.patient_id='".$pId."' AND ivfa.del_status='0' 
							AND users.id = ivfa.performed_by
							".$andProvdQry."
							ORDER BY ivfa.exam_date DESC, ivfa.examTime DESC, ivfa.vf_id DESC " ;
					$rez = sqlStatement($sql);
					for($a=0;$row=sqlFetchArray($rez);$a++){
						if(!empty($row["tId"])){
						
							$arr[$row["eDt"]]["IVFA"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
																 "prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
																 "purged"=>$row["purged"]);
						}
					}
					}

			//	case "ICG":
					if(in_array("ICG", $active)){
					$sql = "SELECT icg.icg_id AS tId,
							DATE_FORMAT(icg.exam_date, '%Y-%m-%d') AS eDt,
							DATE_FORMAT(icg.exam_date, '".get_sql_date_format('','y')."') AS dt,
							icg.performed_by AS prfBy, icg.phy as phy, icg.purged, icg.form_id
							FROM icg, users WHERE icg.patient_id='".$pId."' AND icg.del_status='0' 
							AND users.id = icg.performed_by
							".$andProvdQry."
							ORDER BY icg.exam_date DESC, icg.examTime DESC, icg.icg_id DESC " ;
					$rez = sqlStatement($sql);
					for($a=0;$row=sqlFetchArray($rez);$a++){
						if(!empty($row["tId"])){
						
							$arr[$row["eDt"]]["ICG"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
																"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
																"purged"=>$row["purged"]);
						}
					}
					}

			//	case "Fundus":
					if(in_array("Fundus", $active)){
					$sql = "SELECT disc.disc_id AS tId,
							DATE_FORMAT(disc.examDate, '%Y-%m-%d') AS eDt,
							DATE_FORMAT(disc.examDate, '".get_sql_date_format('','y')."') AS dt,
							disc.performedBy AS prfBy, disc.phyName as phy, disc.purged,disc.fundusDiscPhoto, disc.formId
							FROM disc, users WHERE disc.patientId='".$pId."' AND disc.del_status='0' 
							AND users.id = disc.performedBy
							".$andProvdQry."
							ORDER BY disc.examDate DESC, disc.examTime DESC, disc.disc_id DESC " ;
					$rez = sqlStatement($sql);
					for($a=0;$row=sqlFetchArray($rez);$a++){
						if(!empty($row["tId"])){						
						
							if(!empty($row["fundusDiscPhoto"])){
								if($row["fundusDiscPhoto"]=="1"){
									$row["fundusDiscPhoto"]="DP";
								}else if($row["fundusDiscPhoto"]=="2"){
									$row["fundusDiscPhoto"]="MP";
								}else if($row["fundusDiscPhoto"]=="3"){
									$row["fundusDiscPhoto"]="RP";
								}
							}	


							$arr[$row["eDt"]]["Fundus"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
																	"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
																	"purged"=>$row["purged"],"test_type"=>$row["fundusDiscPhoto"]);
						}
					}
					}
					

			//	case "External/Anterior":
					if(in_array("External/Anterior", $active)){
					$sql = "SELECT disc_external.disc_id AS tId,
							DATE_FORMAT(disc_external.examDate, '%Y-%m-%d') AS eDt,
							DATE_FORMAT(disc_external.examDate, '".get_sql_date_format('','y')."') AS dt,
							disc_external.performedBy AS prfBy, disc_external.phyName as phy, disc_external.purged,disc_external.fundusDiscPhoto, disc_external.formId
							FROM disc_external, users WHERE disc_external.patientId='".$pId."' AND disc_external.del_status='0' 
							AND users.id = disc_external.performedBy
							".$andProvdQry."
							ORDER BY disc_external.examDate DESC, disc_external.examTime DESC, disc_external.disc_id DESC " ;
					$rez = sqlStatement($sql);
					for($a=0;$row=sqlFetchArray($rez);$a++){
						if(!empty($row["tId"])){
						
							if(!empty($row["fundusDiscPhoto"])){
								if($row["fundusDiscPhoto"]=="1"){
									$row["fundusDiscPhoto"]="ES";
								}else if($row["fundusDiscPhoto"]=="2"){
									$row["fundusDiscPhoto"]="ASP";
								}
							}
							
							$arr[$row["eDt"]]["External/Anterior"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
																				"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
																				"purged"=>$row["purged"],
																				"test_type"=>$row["fundusDiscPhoto"]);
						}
					}
					}	

			//	case "Topography":
					if(in_array("Topography", $active)){
					$sql = "SELECT topography.topo_id AS tId,
							DATE_FORMAT(topography.examDate, '%Y-%m-%d') AS eDt,
							DATE_FORMAT(topography.examDate, '".get_sql_date_format('','y')."') AS dt,
							topography.performedBy AS prfBy, topography.phyName as phy, topography.purged, topography.formId
							FROM topography, users WHERE topography.patientId='".$pId."' AND topography.del_status='0' 
							AND users.id = topography.performedBy
							".$andProvdQry."
							ORDER BY topography.examDate DESC, topography.examTime DESC, topography.topo_id DESC " ;
					$rez = sqlStatement($sql);
					for($a=0;$row=sqlFetchArray($rez);$a++){
						if(!empty($row["tId"])){
						
							$arr[$row["eDt"]]["Topography"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
																			"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
																			"purged"=>$row["purged"]);
						}
					}
					}

			//	case "Other":
					if(in_array("Other", $active)){
					$sql = "SELECT test_other.test_other_id AS tId,
							DATE_FORMAT(test_other.examDate, '%Y-%m-%d') AS eDt,
							DATE_FORMAT(test_other.examDate, '".get_sql_date_format('','y')."') AS dt,
							test_other.performedBy AS prfBy, test_other.phyName as phy, test_other.test_other AS subcat, test_other.purged, test_other.formId
							FROM test_other, users WHERE test_other.patientId='".$pId."' AND test_other.del_status='0' 
							AND users.id = test_other.performedBy
							".$andProvdQry."
							ORDER BY test_other.test_other ASC, test_other.examDate DESC, test_other.examTime DESC, test_other.test_other_id DESC " ;
					$rez = sqlStatement($sql);
					for($a=0;$row=sqlFetchArray($rez);$a++){

						$test_other = ucfirst(strtolower($row["subcat"]));

						if(!empty($row["tId"])){
						
							if(!isset($arr[$row["eDt"]]["Other"][$test_other])){
								$arr[$row["eDt"]]["Other"][$test_other] = array();
							}

							$arr[$row["eDt"]]["Other"][$test_other][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
																				"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
																				"purged"=>$row["purged"]);
						}
					}
					}
					
			//custom tests
					$sql = "SELECT test_id AS tId,
							DATE_FORMAT(examDate, '%Y-%m-%d') AS eDt,
							DATE_FORMAT(examDate, '".get_sql_date_format('','y')."') AS dt,
							performedBy AS prfBy, phyName as phy, tn.temp_name AS subcat, tn.test_name AS subcat_tn, purged, formId
							FROM test_custom_patient JOIN tests_name tn ON (tn.id=test_custom_patient.test_template_id) 
							WHERE patientId='".$pId."' AND test_custom_patient.del_status='0' AND test_template_id>0 
							ORDER BY test_other ASC, examDate DESC, examTime DESC, test_id DESC";
					$rez = sqlStatement($sql);
					for($a=0;$row=sqlFetchArray($rez);$a++){

						$test_template = ucfirst(strtolower($row["subcat"]));
						if(in_array($row["subcat_tn"], $active)){
						if(!empty($row["tId"])){
							if(!isset($arr[$row["eDt"]]["TemplateTests"][$test_template])){
								$arr[$row["eDt"]]["TemplateTests"][$test_template] = array();
							}

							$arr[$row["eDt"]]["TemplateTests"][$test_template][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
																				"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
																				"purged"=>$row["purged"]);
						}
						}
					}
					
			//	case "Laboratories":
					if(in_array("Laboratories", $active)){
					$sql = "SELECT test_labs.test_labs_id AS tId,
							DATE_FORMAT(test_labs.examDate, '%Y-%m-%d') AS eDt,
							DATE_FORMAT(test_labs.examDate, '".get_sql_date_format('','y')."') AS dt,
							test_labs.performedBy AS prfBy, test_labs.phyName as phy, test_labs.test_labs AS subcat, test_labs.purged, test_labs.formId
							FROM test_labs, users WHERE test_labs.patientId='".$pId."' AND test_labs.del_status='0' 
							AND users.id = test_labs.performedBy
							".$andProvdQry."
							ORDER BY test_labs.test_labs ASC, test_labs.examDate DESC, test_labs.examTime DESC, test_labs.test_labs_id DESC " ;
					$rez = sqlStatement($sql);
					for($a=0;$row=sqlFetchArray($rez);$a++){

						if(!empty($row["tId"])){
						
							$arr[$row["eDt"]]["Labs"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
																	"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
																	"purged"=>$row["purged"]);
						}
					}
					}

			//	case "A/Scan":
					if(in_array("A/Scan", $active)){
					$sql = "SELECT surgical_tbl.surgical_id AS tId,
								DATE_FORMAT(examDate, '%Y-%m-%d') AS eDt,
								DATE_FORMAT(examDate, '".get_sql_date_format('','y')."') AS dt,
								performedByOD AS prfBy, signedById as phy, surgical_tbl.purged, surgical_tbl.form_id
								FROM surgical_tbl, users WHERE patient_id ='".$pId."' AND surgical_tbl.del_status='0' 
								AND users.id = surgical_tbl.performedByOD
								".$andProvdQry."
								ORDER BY surgical_tbl.examDate DESC, surgical_tbl.examTime DESC, surgical_tbl.surgical_id DESC " ;
					$rez = sqlStatement($sql);
					for($a=0;$row=sqlFetchArray($rez);$a++){

						if(!empty($row["tId"])){
						
							$arr[$row["eDt"]]["A/Scan"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
																	"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
																	"purged"=>$row["purged"]);
						}
					}
					}
					
			//	case "IOL_Master":
					if(in_array("IOL Master", $active)){
					$sql = "SELECT iol_master_tbl.iol_master_id AS tId,
								DATE_FORMAT(examDate, '%Y-%m-%d') AS eDt,
								DATE_FORMAT(examDate, '".get_sql_date_format('','y')."') AS dt,
								performedByOD AS prfBy, signedById as phy, iol_master_tbl.purged, iol_master_tbl.form_id
								FROM iol_master_tbl, users WHERE patient_id ='".$pId."' AND iol_master_tbl.del_status='0' 
								AND users.id = iol_master_tbl.performedByOD
								".$andProvdQry."
								ORDER BY iol_master_tbl.examDate DESC, iol_master_tbl.examTime DESC, iol_master_tbl.iol_master_id DESC " ;
					$rez = sqlStatement($sql);
					for($a=0;$row=sqlFetchArray($rez);$a++){

						if(!empty($row["tId"])){
						
							$arr[$row["eDt"]]["IOL Master"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
																	"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
																	"purged"=>$row["purged"]);
						}
					}
					}

			//	case "B-Scan":
					if(in_array("B-Scan", $active)){
					$sql = "SELECT test_bscan.test_bscan_id AS tId,
							DATE_FORMAT(test_bscan.examDate, '%Y-%m-%d') AS eDt,
							DATE_FORMAT(test_bscan.examDate, '".get_sql_date_format('','y')."') AS dt,
							test_bscan.performedBy AS prfBy, test_bscan.phyName as phy, test_bscan.purged, test_bscan.formId
							FROM test_bscan, users WHERE test_bscan.patientId='".$pId."' AND test_bscan.del_status='0' 
							AND users.id = test_bscan.performedBy
							".$andProvdQry."
							ORDER BY test_bscan.examDate DESC, test_bscan.examTime DESC, test_bscan.test_bscan_id DESC " ;
					$rez = sqlStatement($sql);
					for($a=0;$row=sqlFetchArray($rez);$a++){

						if(!empty($row["tId"])){
							
							$arr[$row["eDt"]]["B-Scan"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
																	"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
																	"purged"=>$row["purged"]);
						}
					}
					}

			//	case "Cell Count":
					if(in_array("Cell Count", $active)){
					$sql = "SELECT test_cellcnt.test_cellcnt_id AS tId,
							DATE_FORMAT(test_cellcnt.examDate, '%Y-%m-%d') AS eDt,
							DATE_FORMAT(test_cellcnt.examDate, '".get_sql_date_format('','y')."') AS dt,
							test_cellcnt.performedBy AS prfBy, test_cellcnt.phyName as phy, test_cellcnt.purged, test_cellcnt.formId
							FROM test_cellcnt, users WHERE test_cellcnt.patientId='".$pId."' AND test_cellcnt.del_status='0' 
							AND users.id = test_cellcnt.performedBy
							".$andProvdQry."
							ORDER BY test_cellcnt.examDate DESC, test_cellcnt.examTime DESC, test_cellcnt.test_cellcnt_id DESC " ;
					$rez = sqlStatement($sql);
					for($a=0;$row=sqlFetchArray($rez);$a++){

						if(!empty($row["tId"])){
						
							$arr[$row["eDt"]]["Cell Count"][] = array("id"=>$row["tId"],"dt"=>$row["dt"],
																			"prfBy"=>$row["prfBy"],"phy"=>$row["phy"],
																			"purged"=>$row["purged"]);
						}
					}
					}



		}
		return $arr;
	}
	//END FUNCTION TO SEARCH TEST
	
	function getAllTestofPtSearch_v2($fVal,$fIdArr, $flgRemSync=0){
		if($fVal == "All" || 1==1){
			$oPtTest = new PtTest($this->pid);
			$arr = $oPtTest->getAllTestofPt($flgRemSync);
			krsort($arr);
			return $arr;
		}else{
			
		}
	}	
	
	function main(){
		//Get Requests
		$formAction = $_POST["elem_formAction_ptforms"];
		switch($formAction){
			case "chartNoteTree":
				$echo = $this->getChartNotesTree(array());
				//print_r($echo);
				//exit;

				//$strMsg = getPtVrblComm();
				/*
				$arr = array('innerHtml'=>$echo[0],
							 'len'=>$echo[1],
							 'Tests'=>$echo[2],
							 'lentest'=>$echo[3],
							 'PtVrblComm'=>$strMsg);
				*/
				$arr = array('innerHtml'=>$echo[0], 'is_test_uninterpreted' =>$echo[1]);
				echo json_encode($arr);
				
			break;

			case "Search":				
				$srchVal = $_REQUEST['srchVal'];
				$fndStatus = $_REQUEST['fndStatus'];
				$fndPhy = $_REQUEST['fndPhy'];
				$testExamSearch = $_REQUEST['testExamSearch'];
				$echo = $this->getChartNotesTree(array($srchVal,$fndStatus,$fndPhy,$testExamSearch));
				$arr = array('innerHtml'=>$echo[0], 'is_test_uninterpreted' =>$echo[1]);
				echo json_encode($arr);

			break;
			default:
				print_r($_POST);
			break;
			
		}
	
	}
}
?>