<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
?>
<?php
include_once(dirname(__FILE__).'/patient_app.php');
class pag_service extends patient_app{	
	var $reqModule;
	public function __construct($patient){
		parent::__construct($patient);
	}
	public function medical_history(){
		$arrMedHx = array();
		//$arrMedHx['active_problem_list'] = $this->active_problem_list();
		$count_ocu_hx = count($this->ocular_hx());
		$count_ocu_med = count($this->ocular_meds());
		$count_sur = count($this->ocular_surgeries());
		$count_aller = count($this->allergies());
		$count_med_hx = count($this->medical_hx());
		$count_gen_med = count($this->general_meds());
		$total = max($count_ocu_hx, $count_ocu_med, $count_sur,$count_aller, $count_med_hx, $count_gen_med);
		$arrOcularHx = $this->ocular_hx();
		for($i = $count_ocu_hx; $i<$total;$i++){
			array_push($arrOcularHx, array("problem_title"=>"-1"));
		}
		$arrOcularMeds = $this->ocular_meds();
		for($i = $count_ocu_med; $i<$total;$i++){
			array_push($arrOcularMeds, array("ocular_medi_title"=>"-1"));
		}
		$arrOcularSur = $this->ocular_surgeries();
		for($i = $count_sur; $i<$total;$i++){
			array_push($arrOcularSur, array("ocular_surgery_title"=>"-1"));
		}
		$arrAller = $this->allergies();
		for($i = $count_aller; $i<$total;$i++){
			array_push($arrAller, array("allergy_title"=>"-1"));
		}
		$arrMediHx = $this->medical_hx();
		for($i = $count_med_hx; $i<$total;$i++){
			array_push($arrMediHx, array("problem_title"=>"-1"));
		}
		$arrGenMed = $this->general_meds();
		for($i = $count_gen_med; $i<$total;$i++){
			array_push($arrGenMed, array("general_medi_title"=>"-1"));
		}
		$arrMedHx['ocular_hx'] = $arrOcularHx;
		$arrMedHx['ocular_meds'] = $arrOcularMeds;
		$arrMedHx['ocular_surgeries'] = $arrOcularSur;
		$arrMedHx['allergies'] = $arrAller;
		$arrMedHx['medical_hx'] = $arrMediHx;
		$arrMedHx['general_meds'] = $arrGenMed;
		//$arrMedHx['general_meds'] = $this->general_meds();
		return $arrMedHx;
	}
	public function ocular_hx(){
		$this->db_obj->qry = "SELECT any_conditions_you, OtherDesc FROM ocular WHERE patient_id='".$this->patient."'";
		$result = $this->db_obj->get_resultset_array();	
		$arrReturn = array();
		
		foreach($result as $arrResult){
			$strAnyConditionsYou = $this->get_set_pat_rel_values_retrive($arrResult["any_conditions_you"],"pat","~|~");
			$arrConYou = explode(",",$strAnyConditionsYou);
			$i = 0;
			foreach($arrConYou as $key => $val){
				switch($val){
					case "1":
					$arrReturn[]['problem_title'] = "Dry Eyes";
					break;
					case "2":
					$arrReturn[]['problem_title'] = "Macula Degeneration";
					break;
					case "3":
					$arrReturn[]['problem_title'] = "Glaucoma";
					break;
					case "4":
					$arrReturn[]['problem_title'] = "Retinal Detachment";
					break;
					case "5":
					$arrReturn[]['problem_title'] = "Cataracts";
					break;
					case "6":
					$arrReturn[]['problem_title'] = "Keratoconus";
					break;
				}
				$i++;
			}
			$arrReturn[]['problem_title'] = $this->get_set_pat_rel_values_retrive($arrResult["OtherDesc"],"pat","~|~");
		}
		
		return $arrReturn;		
	}
	public function ocular_meds(){
		$this->db_obj->qry = "SELECT title AS ocular_medi_title
							  FROM lists 
							  WHERE pid='".$this->patient."' 
							  		AND (type='4')
									AND allergy_status='Active' 
							";
		$result = $this->db_obj->get_resultset_array();	
		return $result;		
	}
	public function ocular_surgeries(){
		$this->db_obj->qry = "SELECT title AS ocular_surgery_title
							  FROM lists 
							  WHERE pid='".$this->patient."' 
							  		AND (type='6')
									AND allergy_status='Active' 
							";
		$result = $this->db_obj->get_resultset_array();	
		return $result;		
	}
	public function allergies(){
		$this->db_obj->qry = "SELECT title AS allergy_title
							  FROM lists 
							  WHERE pid='".$this->patient."'
							  		AND (type='3' or type='7')
									AND allergy_status='Active' 
							";
		$result = $this->db_obj->get_resultset_array();	
		return $result;		
	}
	public function medical_hx(){
		$this->db_obj->qry = "SELECT any_conditions_you 
							  FROM general_medicine 
							  WHERE patient_id='".$this->patient."'";
		$result = $this->db_obj->get_resultset_array();	
		$arrReturn = array();
		foreach($result as $arrResult){
			$arrConMedHx = explode(",",$arrResult["any_conditions_you"]);
			$i = 0;
			foreach($arrConMedHx as $key => $val)
			{
				switch($val){
					case "1":
					$arrReturn[]['problem_title'] = "High Blood Pressure";
					break;
					case "3":
					$arrReturn[]['problem_title'] = "Diabetes";
					break;
					case "4":
					$arrReturn[]['problem_title'] = "Lung Problems";
					break;
					case "5":
					$arrReturn[]['problem_title'] = "Stroke";
					break;
					case "6":
					$arrReturn[]['problem_title'] = "Thyroid Problems";
					break;
				}
				$i++;
			}
		}
		return $arrReturn;		
	}
	public function general_meds(){
		$this->db_obj->qry = "SELECT title AS general_medi_title
							  FROM lists 
							  WHERE pid='".$this->patient."'
							  		AND (type='1')
									AND allergy_status='Active' 
							";
		$result = $this->db_obj->get_resultset_array();	
		return $result;		
	}
	function get_set_pat_rel_values_retrive($dbValue,$methodFor,$delimiter = "~|~",$hifenOptional= ""){
		$dbValue 	= trim($dbValue);		
		$methodFor 	= trim($methodFor);
		$delimiter	= trim($delimiter);
		if($methodFor == "pat"){
			//echo '<br>dbv='.$dbValue;
			if(stristr($dbValue,$delimiter)){
				list($strTxtPat,$strTxtRel) = explode($delimiter,$dbValue);
				$valueToShow = $strTxtPat;
				//echo '<br>'.$valueToShow;
			}
			else{
				$valueToShow = $dbValue;
			}
		}
		elseif($methodFor == "rel"){
			if(stristr($dbValue,$delimiter)){
				list($strTxtPat,$strTxtRel) = explode($delimiter,$dbValue);
				$valueToShow = $strTxtRel;
			}
			else{				
				$valueToShow = "";
			}
		}
		
		if($valueToShow) { $valueToShow = $hifenOptional.$valueToShow; }//FOR FACESHEET PDF
		
		return $valueToShow;
	}
	public function problem_history(){
		
		$this->db_obj->qry = "SELECT GROUP_CONCAT(problem_name SEPARATOR ', ') AS problem_name, date_format(onset_date, '%m-%d-%y') AS onset_date
							  FROM pt_problem_list ppl
							  WHERE pt_id = '".$this->patient."'
							  AND status = 'Active'
							  GROUP BY onset_date
							  ORDER BY ppl.onset_date DESC
							";
		$result['active_problem_list'] = $this->db_obj->get_resultset_array();	
		return $result;		
	}
	public function orders_history(){
		
		$this->db_obj->qry = "SELECT date_format(oscn.created_date, '%m-%d-%y') AS created_date_show,
								os.orderset_name,
								GROUP_CONCAT(od.name SEPARATOR ', ') as order_name
								FROM order_set_associate_chart_notes oscn
								LEFT JOIN order_sets os ON os.id = oscn.order_set_id
								LEFT JOIN order_set_associate_chart_notes_details oscnd ON oscnd.order_set_associate_id = oscn.order_set_associate_id
								LEFT JOIN order_details od ON od.id = oscnd.order_id
								WHERE oscn.patient_id = '".$this->patient."'
								AND oscn.form_id > 0
								AND oscn.logged_provider_id = '".$this->authId."'
								AND oscn.delete_status = '0'
								GROUP BY created_date_show
								ORDER BY oscn.created_date desc
							";
		$result['active_orders'] = $this->db_obj->get_resultset_array();	
		return $result;		
	}
	public function tests_history(){
		$arrActiveTests = array();
		//1 --- A/Scan TEST ----
		$this->db_obj->qry = "select date_format(examDate,'%m-%d-%y') as examDate from surgical_tbl
				where patient_id = '".$this->patient."'  AND purged='0' AND (signedById =0 OR signedById ='' OR signedById IS NULL) ORDER BY surgical_tbl.examDate DESC";
		$qryRes = $this->db_obj->get_resultset_array();
		if(count($qryRes) > 0){
			foreach($qryRes as $test){
				$arrActiveTests[] = array("test_name"=>'A/Scan',"test_dos"=>$test['examDate']);
			}
		}
	
		//2 --- B-Scan TEST ----
		$this->db_obj->qry = "select date_format(examDate,'%m-%d-%y') as examDate from test_bscan
				where patientId = '".$this->patient."'  AND purged='0' AND del_status ='0' AND (phyName =0 OR phyName ='' OR phyName IS NULL) ORDER BY test_bscan.examDate DESC";
		$qryRes = $this->db_obj->get_resultset_array();
		if(count($qryRes) > 0){
			foreach($qryRes as $test){
				$arrActiveTests[] = array("test_name"=>'B-Scan',"test_dos"=>$test['examDate']);
			}
		}
		
		//4 --- Other TEST ----
		$this->db_obj->qry = "select date_format(examDate,'%m-%d-%y') as examDate from test_other
				where patientId = '".$this->patient."'  AND purged='0' AND del_status ='0' AND (phyName =0 OR phyName ='' OR phyName IS NULL) ORDER BY test_other.examDate DESC";
		$qryRes = $this->db_obj->get_resultset_array();
		if(count($qryRes) > 0){
			foreach($qryRes as $test){
				$arrActiveTests[] = array("test_name"=>'Other',"test_dos"=>$test['examDate']);
			}
		}	
		//5 --- Cell Count TEST ----
		$this->db_obj->qry = "select date_format(examDate,'%m-%d-%y') as examDate from test_cellcnt
				where patientId = '".$this->patient."'  AND purged='0' AND del_status ='0' AND (phyName =0 OR phyName ='' OR phyName IS NULL) ORDER BY test_cellcnt.examDate DESC";
		$qryRes = $this->db_obj->get_resultset_array();
		if(count($qryRes) > 0){
			foreach($qryRes as $test){
				$arrActiveTests[] = array("test_name"=>'Cell Count',"test_dos"=>$test['examDate']);
			}
		}
		//6 --- ICG TEST ----
		$this->db_obj->qry = "select date_format(exam_date,'%m-%d-%y') as examDate from icg 
				where patient_id = '".$this->patient."'  AND purged='0' AND del_status ='0' AND (phy =0 OR phy ='' OR phy IS NULL) ORDER BY icg.exam_date DESC";
		$qryRes = $this->db_obj->get_resultset_array();
		if(count($qryRes) > 0){
			foreach($qryRes as $test){
				$arrActiveTests[] = array("test_name"=>'ICG',"test_dos"=>$test['examDate']);
			}
		}			
		//7 --- HRT TEST ----
		$this->db_obj->qry = "select date_format(examDate,'%m-%d-%y') as examDate from nfa
				where patient_id = '".$this->patient."' AND purged='0' AND (phyName =0 OR phyName ='' OR phyName IS NULL) ORDER BY nfa.examDate DESC";
		$qryRes = $this->db_obj->get_resultset_array();
		if(count($qryRes) > 0){
			foreach($qryRes as $test){
				$arrActiveTests[] = array("test_name"=>'HRT',"test_dos"=>$test['examDate']);
			}
		}

	
		//8 --- VF TEST ----
		$this->db_obj->qry = "select date_format(examDate,'%m-%d-%y') as examDate from vf
				where patientId = '".$this->patient."' AND purged='0' AND (phyName =0 OR phyName ='' OR phyName IS NULL) ORDER BY vf.examDate DESC";
		$qryRes = $this->db_obj->get_resultset_array();
		if(count($qryRes) > 0){
			foreach($qryRes as $test){
				$arrActiveTests[] = array("test_name"=>'VF',"test_dos"=>$test['examDate']);
			}
		}
		//9 --- VF-GL TEST ----
		$this->db_obj->qry = "select date_format(examDate,'%m-%d-%y') as examDate from vf_gl
				where patientId = '".$this->patient."' AND purged='0' AND (phyName =0 OR phyName ='' OR phyName IS NULL) ORDER BY vf_gl.examDate DESC";
		$qryRes = $this->db_obj->get_resultset_array();
		if(count($qryRes) > 0){
			foreach($qryRes as $test){
				$arrActiveTests[] = array("test_name"=>'VF-GL',"test_dos"=>$test['examDate']);
			}
		}
		//10 --- OCT TEST ----
		$this->db_obj->qry = "select date_format(examDate,'%m-%d-%y') as examDate from oct
				where patient_id = '".$this->patient."' AND purged='0' AND (phyName =0 OR phyName ='' OR phyName IS NULL) ORDER BY oct.examDate DESC";
		$qryRes = $this->db_obj->get_resultset_array();
		if(count($qryRes) > 0){
			foreach($qryRes as $test){
				$arrActiveTests[] = array("test_name"=>'OCT',"test_dos"=>$test['examDate']);
			}
		}
		//11 --- OCT RNFL ----
		$this->db_obj->qry = "select date_format(examDate,'%m-%d-%y') as examDate from oct_rnfl
				where patient_id = '".$this->patient."' AND purged='0' AND (phyName =0 OR phyName ='' OR phyName IS NULL) ORDER BY oct_rnfl.examDate DESC";
		$qryRes = $this->db_obj->get_resultset_array();
		if(count($qryRes) > 0){
			foreach($qryRes as $test){
				$arrActiveTests[] = array("test_name"=>'OCT-RNFL',"test_dos"=>$test['examDate']);
			}
		}
		//12 --- GDX ----
		$this->db_obj->qry = "select date_format(examDate,'%m-%d-%y') as examDate from test_gdx
				where patient_id = '".$this->patient."' AND purged='0' AND (phyName =0 OR phyName ='' OR phyName IS NULL) ORDER BY test_gdx.examDate DESC";
		$qryRes = $this->db_obj->get_resultset_array();
		if(count($qryRes) > 0){
			foreach($qryRes as $test){
				$arrActiveTests[] = array("test_name"=>'GDX',"test_dos"=>$test['examDate']);
			}
		}	
		//13 --- PACHY TEST ----
		$this->db_obj->qry = "select date_format(examDate,'%m-%d-%y') as examDate from pachy
				where patientId = '".$this->patient."' AND purged='0' AND (phyName =0 OR phyName ='' OR phyName IS NULL) ORDER BY pachy.examDate DESC";
		$qryRes = $this->db_obj->get_resultset_array();
		if(count($qryRes) > 0){
			foreach($qryRes as $test){
				$arrActiveTests[] = array("test_name"=>'PACHY',"test_dos"=>$test['examDate']);
			}
		}

		//14 --- IVFA TEST ----
		$this->db_obj->qry = "select date_format(exam_date,'%m-%d-%y') as examDate from ivfa
				where patient_id = '".$this->patient."' AND purged='0' AND (phy =0 OR phy ='' OR phy IS NULL) ORDER BY ivfa.exam_date DESC";
		$qryRes = $this->db_obj->get_resultset_array();
		if(count($qryRes) > 0){
			foreach($qryRes as $test){
				$arrActiveTests[] = array("test_name"=>'IVFA',"test_dos"=>$test['examDate']);
			}
		}

		//15 --- FUNDUS TEST ----
		$this->db_obj->qry = "select date_format(examDate,'%m-%d-%y') as examDate from disc
				where patientId = '".$this->patient."' AND purged='0' AND (phyName =0 OR phyName ='' OR phyName IS NULL) ORDER BY disc.examDate DESC";
		$qryRes = $this->db_obj->get_resultset_array();
		if(count($qryRes) > 0){
			foreach($qryRes as $test){
				$arrActiveTests[] = array("test_name"=>'FUNDUS',"test_dos"=>$test['examDate']);
			}
		}

		//16 --- EXTERNAL TEST ----
		$this->db_obj->qry = "select date_format(examDate,'%m-%d-%y') as examDate from disc_external
				where patientId = '".$this->patient."'	AND purged='0' AND (phyName =0 OR phyName ='' OR phyName IS NULL) ORDER BY disc_external.examDate DESC";
		$qryRes = $this->db_obj->get_resultset_array();
		if(count($qryRes) > 0){
			foreach($qryRes as $test){
				$arrActiveTests[] = array("test_name"=>'EXTERNAL_ANTERIOR',"test_dos"=>$test['examDate']);
			}
		}

		//17 --- TOPOGRAPHY TEST ----
		$this->db_obj->qry = "select date_format(examDate,'%m-%d-%y') as examDate from topography
				where patientId = '".$this->patient."'	AND purged='0' AND (phyName =0 OR phyName ='' OR phyName IS NULL) ORDER BY topography.examDate DESC";
		$qryRes = $this->db_obj->get_resultset_array();
		if(count($qryRes) > 0){
			foreach($qryRes as $test){
				$arrActiveTests[] = array("test_name"=>'TOPOGRAPHY',"test_dos"=>$test['examDate']);
			}
		}
		//18 --- IOL Master TEST ----
		$this->db_obj->qry = "select date_format(examDate,'%m-%d-%y') as examDate from iol_master_tbl
				where patient_id = '".$this->patient."' AND purged='0' AND (signedById =0 OR signedById ='' OR signedById IS NULL) ORDER BY iol_master_tbl.examDate DESC";
		$qryRes = $this->db_obj->get_resultset_array();
		if(count($qryRes) > 0){
			foreach($qryRes as $test){
				$arrActiveTests[] = array("test_name"=>'IOL Master',"test_dos"=>$test['examDate']);
			}
		}
		//3 --- Laboratories  TEST ----
		$this->db_obj->qry = "select date_format(examDate,'%m-%d-%y') as examDate from test_labs 
				where patientId = '".$this->patient."'  AND purged='0' AND del_status ='0' AND (phyName =0 OR phyName ='' OR phyName IS NULL) ORDER BY test_labs.examDate DESC";
		$qryRes = $this->db_obj->get_resultset_array();
		if(count($qryRes) > 0){
			foreach($qryRes as $test){
				$arrActiveTests[] = array("test_name"=>'Labs',"test_dos"=>$test['examDate']);
			}
		}
		$arrActiveTests = array_reverse($this->msort($arrActiveTests,'test_dos'));
		$arrActiveTests = $this->group_assoc($arrActiveTests, 'test_dos','test_name');
		$return = array();
		foreach($arrActiveTests as $key=>$arr){
			$return[] = array("test_name"=>$key,"test_dos"=>implode(",",$arr));
		}
		//array_multisort($arrActiveTests[1], SORT_DESC, SORT_STRING);
		$arrReturn['active_tests'] = $return;
		return $arrReturn;	
	}
	function group_assoc($array, $key, $key2) {
		$return = array();
		foreach($array as $v) {
			$return[$v[$key]][] = $v[$key2];
		}
		return $return;
	}
	public function msort($array, $key, $sort_flags = SORT_REGULAR) {
		if (is_array($array) && count($array) > 0) {
			if (!empty($key)) {
				$mapping = array();
				foreach ($array as $k => $v) {
					$sort_key = '';
					if (!is_array($key)) {
						$sort_key = $v[$key];
					} else {
						// @TODO This should be fixed, now it will be sorted as string
						foreach ($key as $key_key) {
							$sort_key .= $v[$key_key];
						}
						$sort_flags = SORT_STRING;
					}
					$mapping[$k] = $sort_key;
				}
				asort($mapping, $sort_flags);
				$sorted = array();
				foreach ($mapping as $k => $v) {
					$sorted[] = $array[$k];
				}
				return $sorted;
			}
		}
		return $array;
	}
	public function comments(){
		
		$this->db_obj->qry = "SELECT comments FROM chart_ptPastDiagnosis WHERE patient_id='".$this->patient."' ";
		$result['comments'] = $this->db_obj->get_resultset_array();	
		return $result;		
	}
	public function getVisInfo($vis_id, $vis_statusElements, $releaseNumber, $dtdos){
		$vision = $visionOD = $visionOS = "";
		$sql = "SELECT sel_od, txt_od, sel_os, txt_os, sec_indx  FROM chart_acuity where id_chart_vis_master = '".$vis_id."' AND sec_name='Distance' AND sec_indx IN (1,2) ORDER BY sec_indx ";
		$rez = sqlStatement($sql);
		for($i=1; $row = sqlFetchArray($rez); $i++){
			$cc = $row["sec_indx"];
			$fEdit_od = $fEdit_os = true;
			if(($releaseNumber == "1")){
				if( (strpos($vis_statusElements,"elem_visDisOdSel".$cc."=1") !== false) || (strpos($vis_statusElements,"elem_visDisOdTxt".$cc."=1") !== false) 
				  ){
					$fEdit_od = true;
				  }
				  else{
					$fEdit_od = false;
				  }
				  
				 if( (strpos($vis_statusElements,"elem_visDisOsSel".$cc."=1") !== false) || (strpos($vis_statusElements,"elem_visDisOsTxt".$cc."=1") !== false)  	
				  ){
					$fEdit_os = true;
				  }
				  else{
					$fEdit_os = false;
				  }
			}
			
			if($fEdit_od == true){  
				$visionOD = ($row["sel_od"] != "") ? $row["sel_od"]." " : "";
				$visionOD .= ($row["txt_od"] != "" && $row["txt_od"] != "20/") ? "(".$row["txt_od"].")" : "";
			}
			if($fEdit_os == true){  	
				$visionOS = ($row["sel_os"] != "") ? $row["sel_os"]." " : "";
				$visionOS .= ($row["txt_os"] != "" && $row["txt_os"] != "20/") ? "(".$row["txt_os"].")" : "";
			}
			
			if(!empty($visionOD)){
				if(!empty($vision)){ $vision .= "<br/>";  }
				$vision .= "<font color=\"Blue\"><b>OD</b></font>:".$visionOD."";
			}
			if(!empty($visionOS)){	
				if(!empty($vision)){ $vision .= "<br/>";  }
				$vision .= "<font color=\"Green\"><b>OS</b></font>:".$visionOS;					
			}
		}
		
		//------------
		$arrMrDone = array();
		$arrMrDone_give = $arrMrDone_nogive = array();
		$flgElse_nogive=true;
		$visionMr_First="";$vision_nogive="";$vision_give="";			

		$sql = "
			SELECT
			c1.exam_date, c1.mr_none_given,  c1.provider_id, c1.ex_number, c1.ex_desc, 
			c2.sph as sph_r, c2.cyl as cyl_r, c2.axs as axs_r, c2.ad as ad_r, c2.txt_1 as txt_1_r, c2.prsm_p AS prsm_p_r, c2.prism as prism_r, c2.slash as slash_r, c2.sel_1 as sel_1_r,
			c3.sph as sph_l, c3.cyl as cyl_l, c3.axs as axs_l, c3.ad as ad_l, c3.txt_1 as txt_1_l, c3.prsm_p AS prsm_p_l, c3.prism as prism_l, c3.slash as slash_l, c3.sel_1 as sel_1_l
			FROM chart_pc_mr c1
			LEFT JOIN chart_pc_mr_values c2 ON c1.id = c2.chart_pc_mr_id AND c2.site='OD'
			LEFT JOIN chart_pc_mr_values c3 ON c1.id = c3.chart_pc_mr_id AND c3.site='OS'
			WHERE c1.ex_type='MR' AND c1.id_chart_vis_master='".$vis_id."' AND c1.delete_by='0'
			ORDER BY c1.ex_number
		";
		$rez = sqlStatement($sql);
		for($i=1; $row=sqlFetchArray($rez); $i++){
			
			$cc = $row["ex_number"];
			$indx1 = $indx2 = "";
			if($cc>1){
				$indx1 = "Other";
				if($cc>2){
					$indx2 = "_".$cc;
				}
			}	
			
			
			$visionMr = $visionMrOD = $visionMrOS = $mrAdd = "";
			$elem_visMrDesc=$elem_examDateMR=$elem_examDateDistance=$elem_examDateARAK=$elem_examDatePC=$elem_examDateCR="";
			$flagMrDate=true;
			if(!empty($row["ex_desc"]) && ($releaseNumber == "0"))
			{	
				list($elem_visMrDesc,$elem_examDateMR) = $this->extractDate($row["ex_desc"]);
				
				if(!empty($elem_examDateMR))
				{
					$elem_examDateMR = get_date_format($elem_examDateMR);
					$flagMrDate = (!empty($elem_examDateMR) && ($elem_examDateMR >= $dtdos)) ? true : false;// Stoped in R2
				}
			}
			
			
			if( 
				!empty($row["sph_r"]) || !empty($row["cyl_r"]) || !empty($row["axs_r"]) || !empty($row["ad_r"]) || 
				!empty($row["sph_l"]) || !empty($row["cyl_l"]) || !empty($row["axs_l"]) || !empty($row["ad_l"])
			)
			{
				$fEdit_od = $fEdit_os = true;
				if(($releaseNumber == "1")){
					if(((strpos($vis_statusElements,"elem_visMr".$indx1."OdS".$indx2."=1") !== false) || (strpos($vis_statusElements,"elem_visMr".$indx1."OdC".$indx2."=1") !== false) ||
						(strpos($vis_statusElements,"elem_visMr".$indx1."OdA".$indx2."=1") !== false) || (strpos($vis_statusElements,"elem_visMr".$indx1."OdTxt1".$indx2."=1") !== false) ||
						(strpos($vis_statusElements,"elem_visMr".$indx1."OdAdd".$indx2."=1") !== false)
						) 
					){
						$fEdit_od = true;
					}else{
						$fEdit_od = false;
					}
					
					if(((strpos($vis_statusElements,"elem_visMr".$indx1."OsS".$indx2."=1") !== false) || (strpos($vis_statusElements,"elem_visMr".$indx1."OsC".$indx2."=1") !== false) ||
						(strpos($vis_statusElements,"elem_visMr".$indx1."OsA".$indx2."=1") !== false) || (strpos($vis_statusElements,"elem_visMr".$indx1."OsTxt1".$indx2."=1") !== false) ||
						(strpos($vis_statusElements,"elem_visMr".$indx1."OsAdd".$indx2."=1") !== false)	) 
					){
						$fEdit_os = true;
					}else{
						$fEdit_os = false;
					}
				}
				
				if( $fEdit_od == true ){
					$visionMrOD = "";
					$visionMrOD .= "".$row["sph_r"]."";
					$visionMrOD .= " ".$row["cyl_r"]."";
					$visionMrOD .= ((strpos($row["axs_r"],"x") === false) && 
									(strpos($row["axs_r"],"X") === false)) ? " x" : " ";							
					$visionMrOD .= $row["axs_r"]."&#176;";
					if(strlen($row["txt_1_r"])>3){
						$visionMrOD .= " (".$row["txt_1_r"].")";
					}
					$mrAdd = (!empty($row["ad_r"])) ? "".$row["ad_r"]."" : "" ;
				}
				if( $fEdit_os == true ){	
					$visionMrOS = "";
					$visionMrOS .= "".$row["sph_l"]."";
					$visionMrOS .= " ".$row["cyl_l"]."";
					$visionMrOS .= ((strpos($row["axs_l"],"x") === false) && 
									(strpos($row["axs_l"],"X") === false)) ? " x" : " ";
					$visionMrOS .= $row["axs_l"]."&#176;";
					if(strlen($row["txt_1_l"])>3){
						$visionMrOS .= " (".$row["txt_1_l"].")";
					}
					$mrAdd .= (!empty($row["ad_l"])) ? "/".$row["ad_l"]."" : "" ;
					$mrAdd = !empty($mrAdd) ? "<br>ADD ".$mrAdd : "";
				}
			}
			
			$mrGiven = "MR(Given=None)";
			if(( ( (!empty($visionMrOD)) && (trim($visionMrOD) != "x&#176;") ) || 
				( (!empty($visionMrOS)) && (trim($visionMrOS) != "x&#176;") ) ) && 
				($flagMrDate==true) && (strpos($row["mr_none_given"],"MR ".$cc)!==false && strpos($vis_statusElements,"elem_mrNoneGiven".$cc."=1") !== false))
			{
				$visionMr .= "MR ".$cc;
				//$mrGiven = (($row["vis_mr_none_given"] == "MR 1")) ? "MR(Given=MR 1)" : $mrGiven ;
				$mrGiven = (strpos($row["mr_none_given"],"MR ".$cc)!==false && (strpos($vis_statusElements,"elem_mrNoneGiven".$cc."=1") !== false)) ? "MR(Given=MR ".$cc.")," : "" ;
				//$visionMr .= (($row["vis_mr_none_given"] == "MR 1")) ? " - Given<br>" : "<br>" ;							
				$visionMr .= (strpos($row["mr_none_given"], "MR ".$cc)!==false  && (strpos($vis_statusElements,"elem_mrNoneGiven".$cc."=1") !== false)) ? " - Given<br>" : "<br>" ;
				if(( (!empty($visionMrOD)) && (trim($visionMrOD) != "x&#176;") )){$visionMr .= "<font color=\"Blue\"><b>OD</b></font>:".$visionMrOD."<br>";}
				if( (!empty($visionMrOS)) && (trim($visionMrOS) != "x&#176;") ){$visionMr .= "<font color=\"Green\"><b>OS</b></font>:".$visionMrOS;}
				$visionMr .= $mrAdd;
				
				if($row["prsm_p_r"] || $row["prsm_p_l"] || $row["slash_r"] || $row["slash_l"] ){
					$visionMr.="<br>Prism<br>";					$visionOD=$visionOS="";
					if(( (!empty($visionMrOD)) && (trim($visionMrOD) != "x&#176;") )){
						$visionOD=($row["prsm_p_r"]!= "") ? " P&nbsp;".$row["prsm_p_r"]." " : "";
						$visionOD.=($row["sel_1_r"]!= "") ? "&#9650; ".$row["sel_1_r"]: "";
						$visionOD.=($row["slash_r"]!= "") ? "  / ".$row["slash_r"]: "";
						$visionOD.=($row["prism_r"]!= "") ? " ".$row["prism_r"]: "";
						if($row["prsm_p_r"] || $row["slash_r"] ){
							$visionOD = "<font color=\"Blue\"><b>OD</b></font>:".$visionOD;
						}else{ $visionOD = ""; }
					}
					if( (!empty($visionMrOS)) && (trim($visionMrOS) != "x&#176;") ){
						$visionOS=($row["prsm_p_l"]!= "") ? " P&nbsp;".$row["prsm_p_l"]." " : "";
						$visionOS.=($row["sel_1_l"]!= "") ? "&#9650; ".$row["sel_1_l"]: "";
						$visionOS.=($row["slash_l"]!= "") ? "  / ".$row["slash_l"]: "";
						$visionOS.=($row["prism_l"]!= "") ? " ".$row["prism_l"]: "";
						if($row["prsm_p_l"] || $row["slash_l"] ){							
							$visionOS = "<font color=\"Green\"><b>OS</b></font>:".$visionOS;
							if(!empty($visionOD)) { $visionOS = "<br/>".$visionOS; }
						}else{$visionOS = ""; }						
					}
					$visionMr.="".$visionOD."".$visionOS;						
				}
			}
			
			//MR first
			if($cc == 1){
				$visionMr_First = $visionMr;
			}
			
			//Show Given Mr				
			if( !empty($mrGiven) && ($mrGiven != "MR(Given=None)") ){ //mr is given
				if(strpos($mrGiven, "MR(Given=MR ".$cc.")")!==false){
					if(!empty($visionMr)){
						$vision_give .= (!empty($vision_give)) ? "<br>".$visionMr : $visionMr ;
						$arrMrDone_give[]="MR ".$cc;
					}
				}
			}else{					
				//check dr. mr else mr 1
				if(!empty($visionMr)){
					$vision_nogive .= (!empty($vision_nogive)) ? "<br>".$visionMr : $visionMr ;
					$flgElse_nogive=false;
					$arrMrDone_nogive[]="MR ".$cc;
				}
			}				
		}
		
		//if no given is done: give first Mr 1 done
		if(empty($vision_give) && $flgElse==true){
			if(!empty($visionMr_First)){
				$vision_nogive .= (!empty($vision_nogive)) ? "<br>".$visionMr_First : $visionMr_First ;
				$arrMrDone_nogive[]="MR 1";
			}
		}
		
		if(!empty($vision_give)){
			if(!empty($vision)){ $vision .= "<br>"; }
			$vision .= $vision_give;
			$arrMrDone = $arrMrDone_give;
		}else if(!empty($vision_nogive)){
			if(!empty($vision)){ $vision .= "<br>"; }
			$vision .= $vision_nogive;
			$arrMrDone = $arrMrDone_nogive;
		}
		
		return array($vision, $arrMrDone);
	}
	
	public function chart_history(){
		$_SESSION['patient'] = $this->patient;
		$this->db_obj->qry = "SELECT
							DATE_FORMAT(chart_master_table.date_of_service,'%m-%d-%y') AS date_of_service ,".
							"chart_assessment_plans.hrt,
							chart_assessment_plans.hrtEye,
							chart_assessment_plans.oct,
							chart_assessment_plans.octEye,
							chart_assessment_plans.avf,
							chart_assessment_plans.avfEye,
							chart_assessment_plans.ivfa,
							chart_assessment_plans.ivfaEye,
							chart_assessment_plans.dfe,
							chart_assessment_plans.dfeEye,
							chart_assessment_plans.photos,
							chart_assessment_plans.photosEye,
							chart_assessment_plans.pachy,
							chart_assessment_plans.pachyEye,
							chart_assessment_plans.cat_iol,
							chart_assessment_plans.catIolEye,
							chart_assessment_plans.yag_cap,
							chart_assessment_plans.yagCapEye,
							chart_assessment_plans.altp,
							chart_assessment_plans.altpEye,
							chart_assessment_plans.pi,
							chart_assessment_plans.piEye,
							chart_assessment_plans.ratinal_laser,
							chart_assessment_plans.ratinalLaserEye,
							chart_assessment_plans.follow_up,
							chart_assessment_plans.follow_up_numeric_value,
							chart_assessment_plans.followUpVistType,
							chart_assessment_plans.followup,
							chart_assessment_plans.retina,
							chart_assessment_plans.neuro_ophth,
							chart_assessment_plans.monitor_ag,
							chart_assessment_plans.id_precation,
							chart_assessment_plans.lid_scrubs_oint,
							chart_assessment_plans.continue_meds,
							chart_assessment_plans.doctor_name,
							chart_assessment_plans.doctorId,
							chart_assessment_plans.sign_coords,
							chart_assessment_plans.cosigner_id,
							chart_assessment_plans.sign_coordsCosigner,
							chart_assessment_plans.assess_plan,
							chart_assessment_plans.plan_notes,
							chart_assessment_plans.id AS chart_AP_id, ".							
							
							"chart_vis_master.status_elements AS vis_statusElements, 
							chart_vis_master.id AS id_chart_vis_master,
							".
				
							"chart_iop.puff,
							chart_iop.applanation,
							chart_iop.puff_od,
							chart_iop.puff_os_1,
							chart_iop.app_od,
							chart_iop.app_os_1,
							chart_iop.multiple_pressure,
							chart_iop.trgtOd,
							chart_iop.trgtOs,
							chart_iop.tx,
							chart_iop.tx_od,
							chart_iop.tx_os,
							".
							//Gonio
							"
							chart_gonio.gonio_id,
							chart_gonio.gonio_od_summary,
							chart_gonio.gonio_os_summary,
							".
							//Optic
							"chart_optic.od_text,
							chart_optic.os_text, 
							chart_optic.cd_val_od, 
							chart_optic.cd_val_os, 
							".
							//IVFA
							/*"ivfa.ivfa AS ivfaExam,
							ivfa.ivfa_od,
							ivfa.disc_fundus,
							ivfa.disc_os_od, ".
							//VF NFA Pacy this is required for old data only
							"vf_nfa.vis_fil,
							vf_nfa.vis_rad,
							vf_nfa.scan_laser,
							vf_nfa.sca_rad,
							vf_nfa.pachymeter,
							vf_nfa.pac_rad, ".
							//VF
							"vf.vf_id As vfId, ".
							//NFA
							"nfa.nfa_id,
							nfa.scanLaserEye, ".
							//Pachy
							"pachy.pachy_id ,
							pachy.pachyMeterEye, ".
							//Disc
							"disc.disc_id,
							disc.fundusDiscPhoto,
							disc.photoEye, ".
							//OCT
							"oct.oct_id,
							 oct.scanLaserEye as octEye, ".
							//Topography
							"topography.topo_id,
							 topography.topoMeterEye, ".
							//disc_external
							"disc_external.disc_id AS external_id,
							 disc_external.photoEye AS external_eye, ".
							 //Opth
							 "ophtha.ophtha_id,
							  ophtha.ophtha_os,
							  ophtha.ophtha_od,
							 ".*/
				
							"chart_master_table.id AS formID,
							chart_master_table.ptVisit,
							chart_master_table.releaseNumber ".
				
							"FROM chart_master_table ".
							//"LEFT JOIN chart_left_cc_history ON chart_master_table.id = chart_left_cc_history.form_id ".
							"LEFT JOIN chart_assessment_plans ON chart_master_table.id = chart_assessment_plans.form_id
							LEFT JOIN chart_vis_master ON chart_master_table.id = chart_vis_master.form_id
							".
							//"LEFT JOIN vf_nfa ON chart_master_table.id = vf_nfa.form_id ".
							" LEFT JOIN chart_iop ON chart_master_table.id = chart_iop.form_id AND chart_iop.purged='0'".
							" LEFT JOIN chart_optic ON chart_master_table.id = chart_optic.form_id AND chart_optic.purged='0'".
							" LEFT JOIN chart_gonio ON chart_master_table.id = chart_gonio.form_id AND chart_gonio.purged='0'".
				
							/*" LEFT JOIN disc ON chart_master_table.id = disc.formId AND disc.purged='0'
							LEFT JOIN nfa ON chart_master_table.id = nfa.form_id AND nfa.purged='0'
							LEFT JOIN pachy ON chart_master_table.id = pachy.formId AND pachy.purged='0'
							LEFT JOIN vf ON chart_master_table.id = vf.formId AND vf.purged='0'
							LEFT JOIN ivfa ON chart_master_table.id = ivfa.form_id AND ivfa.purged='0'
							LEFT JOIN disc_external ON chart_master_table.id = disc_external.formId AND disc_external.purged='0'
							LEFT JOIN oct ON chart_master_table.id = oct.form_id AND oct.purged='0'
							LEFT JOIN topography ON chart_master_table.id = topography.formId AND topography.purged='0'
							LEFT JOIN ophtha ON chart_master_table.id = ophtha.form_id AND ophtha.purged='0'".*/
				
							" WHERE chart_master_table.patient_id = '".$this->patient."'
							AND chart_master_table.finalize = '1'
							AND chart_master_table.record_validity = '1'
							AND chart_master_table.delete_status = '0'
							AND chart_master_table.purge_status = '0'
							ORDER BY chart_master_table.date_of_service DESC, chart_master_table.id DESC ";
		//return $this->db_obj->qry;					
		$result = $this->db_obj->get_resultset_array();
		$arrReturn = array();
		include_once(dirname(__FILE__)."/../../interface/chart_notes/common/ChartApXml.php");
		include_once(dirname(__FILE__)."/../../interface/main/main_functions.php");	
		include_once(dirname(__FILE__)."/../../interface/chart_notes/fu_functions.php");	
		include_once(dirname(__FILE__)."/../../interface/chart_notes/common/functions.php");
		include_once(dirname(__FILE__)."/../../interface/chart_notes/past_diag/pt_diag_inc.php");
		foreach($result as $i=>$row){			
				$formID = $row["formID"];
				
				//----- BEGIN DATE--------------
				$dbDos = $date = $row["date_of_service"];
				$date_suff = ($row["ptVisit"] != "") ? "".$row["ptVisit"] : "";
				if(!empty($date_suff)){
					$date .= "<br/>".$date_suff;
				}				
				//----- END DATE--------------
				
				//-------------BEGIN ASSESSMENT AND PLAN ------------------------------------------------------------
				$assessment = $plan = $strAp= "";
				$strXml = stripslashes($row["assess_plan"]);
				
				$chartApXml = new ChartApXml($patient_id,$formID);
				$arrTmp = $chartApXml->getVal_Str($strXml);
				$len = count($arrTmp["data"]["ap"]);
				for($m=0;$m<$len;$m++){
					$k=$m+1;
					$tmpPlan = $arrTmp["data"]["ap"][$m]["plan"];
					//if NE is checked then do not include//
					if($arrTmp["data"]["ap"][$m]["ne"]==0){
						$tmpAP = getAPDisplayOpt_mApp($arrTmp["data"]["ap"][$m]["assessment"], $tmpPlan, $arrTmp["data"]["ap"][$m]["resolve"], $arrTmp["data"]["ap"][$m]["ne"], $elem_displayAP,$arrTmp["data"]["ap"][$m]["eye"]);
						$assessment .= $tmpAP[0];
						$plan .= $tmpAP[1];
						$strAp.= $tmpAP[2];
						
						if(!empty($arrTmp["data"]["ap"][$m]["assessment"])) $flgAsm = 1;
						if(!empty($tmpPlan)) $flgPln = 1;

					}
					//if NE is checked then do not include//
				}
				//-------------END ASSESSMENT AND PLAN ------------------------------------------------------------
				
				
				//Signature ---------------------
				$sign = "";
				$signNm="";
				$cosign = "";
				$cosignNm = "";

				if(!empty($row["doctorId"])){
					list($signNm,$signNU,$sign) = getUserFirstName($row["doctorId"],2);
				}
				
				if(!empty($row["cosigner_id"])){
					list($cosignNm, $cosignNU,$cosign) = getUserFirstName($row["cosigner_id"],2);
				}
				//Signature ---------------------
				
				
				//-------------BEGIN VISION------------------------------------------------------------
				$releaseNumber = $row["releaseNumber"];
				list($vision, $arrMrDone) = $this->getVisInfo($row["id_chart_vis_master"], $row["vis_statusElements"], $releaseNumber, $dbDos);				
				
				//-------------END VISION--------------------------------------------------------------
				
				$tests = "";
				//VF -----------
				if(($releaseNumber == "0") && ($row["vis_fil"] == "1"))
				{
					$tests .= "VF";
					$tests .= ($row["vis_rad"] == "1") ? "(OU)" : "";
					$tests .= ($row["vis_rad"] == "3") ? "(OD)" : "";
					$tests .= ($row["vis_rad"] == "2") ? "(OS)" : "";
					$tests .= "<br>";
				}else if(($releaseNumber == "1") && (!empty($row["vfId"]))){
					$tests .= "VF(OU)";
					$tests .= "<br>";
				}
				//VF -----------
				//HRT -----------
				if(($releaseNumber == "0") && ($row["scan_laser"] == "1"))
				{
					$tests .= "HRT";
					$tests .= ($row["sca_rad"] == "1") ? "(OU)" : "";
					$tests .= ($row["sca_rad"] == "3") ? "(OD)" : "";
					$tests .= ($row["sca_rad"] == "2") ? "(OS)" : "";
					$tests .= "<br>";
				}else if(($releaseNumber == "1") && (!empty($row["scanLaserEye"]))){
					$tests .= "HRT";
					$tests .= ($row["scanLaserEye"] == "OU") ? "(OU)" : "";
					$tests .= ($row["scanLaserEye"] == "OD") ? "(OD)" : "";
					$tests .= ($row["scanLaserEye"] == "OS") ? "(OS)" : "";
					$tests .= "<br>";			
				}
				//HRT -----------
				//OCT -----------
				if(!empty($row["oct_id"])){
					$tests .= "OCT";
					$tests .= ($row["octEye"] == "OU") ? "(OU)" : "";
					$tests .= ($row["octEye"] == "OD") ? "(OD)" : "";
					$tests .= ($row["octEye"] == "OS") ? "(OS)" : "";
					$tests .= "<br>";
				}						
				//OCT -----------
				//Pachy -----------
				if(($releaseNumber == "0") && ($row["pachymeter"] == "1"))
				{
					$tests .= "Pachy";
					$tests .= ($row["pac_rad"] == "1") ? "(OU)" : "";
					$tests .= ($row["pac_rad"] == "3") ? "(OD)" : "";
					$tests .= ($row["pac_rad"] == "2") ? "(OS)" : "";
					$tests .= "<br>";							
				}else if(($releaseNumber == "1") && (!empty($row["pachyMeterEye"]))){
					$tests .= "Pachy";
					$tests .= ($row["pachyMeterEye"] == "OU") ? "(OU)" : "";
					$tests .= ($row["pachyMeterEye"] == "OD") ? "(OD)" : "";
					$tests .= ($row["pachyMeterEye"] == "OS") ? "(OS)" : "";
					$tests .= "<br>";
				}
				//Pachy -----------
				//IVFA -----------
				if((($releaseNumber == "0") && ($row["ivfaExam"] == "on")) || (($releaseNumber == "1") && (!empty($row["ivfa_od"]))))
				{
					$tests .= "IVFA";
					$tests .= ($row["ivfa_od"] == "1") ? "(OU)" : "";
					$tests .= ($row["ivfa_od"] == "2") ? "(OD&gt;OS)" : "";
					$tests .= ($row["ivfa_od"] == "3") ? "(OD&lt;OS)" : "";
					$tests .= "<br>";
				}
				//IVFA -----------
				//Fundus Photo -----------
				if(($releaseNumber == "0") && ($row["disc_fundus"] == "1"))
				{
					$tests .= "Fundus Photo";
					$tests .= ($row["disc_os_od"] == "1") ? "(OU)": "";
					$tests .= ($row["disc_os_od"] == "2") ? "(OD)": "";
					$tests .= ($row["disc_os_od"] == "3") ? "(OS)": "";
					$tests .= "<br>";										
				}else if(($releaseNumber == "1") && ($row["fundusDiscPhoto"] == "1")){
					$tests .= "Fundus Photo";
					$tests .= ($row["photoEye"] == "OU") ? "(OU)": "";
					$tests .= ($row["photoEye"] == "OD") ? "(OD)": "";
					$tests .= ($row["photoEye"] == "OS") ? "(OS)": "";
					$tests .= "<br>";							
				}
				//Fundus Photo -----------
				//External -----------
				if(!empty($row["external_id"])){
					$tests .= "External";
					$tests .= ($row["external_eye"] == "OU") ? "(OU)" : "";
					$tests .= ($row["external_eye"] == "OD") ? "(OD)" : "";
					$tests .= ($row["external_eye"] == "OS") ? "(OS)" : "";
					$tests .= "<br>";
				}
				//External -----------
				//Topo -----------
				if(!empty($row["topo_id"])){
					$tests .= "Topography";
					$tests .= ($row["topoMeterEye"] == "OU") ? "(OU)" : "";
					$tests .= ($row["topoMeterEye"] == "OD") ? "(OD)" : "";
					$tests .= ($row["topoMeterEye"] == "OS") ? "(OS)" : "";
					$tests .= "<br>";
				}
				//Topo -----------
				//Ophth -----------
				if(!empty($row["ophtha_id"])){
					
					if((!empty($row["ophtha_od"]) && ($row["ophtha_od"] != "0-0-0:;")) &&
						(!empty($row["ophtha_os"]) && ($row["ophtha_os"] != "0-0-0:;"))
					   ){
						$tEye = "(OU)";
					}else if((!empty($row["ophtha_od"]) && ($row["ophtha_od"] != "0-0-0:;"))){
						$tEye = "(OD)";
					}else if((!empty($row["ophtha_os"]) && ($row["ophtha_os"] != "0-0-0:;"))){
						$tEye = "(OS)";
					}
					
					$tests .= "Ophth";							 
					$tests .= $tEye;														
					$tests .= "<br>";
				}
				//Ophth -----------

				//Gonio -----------
				if(!empty($row["gonio_id"])){
					if(!empty($row["gonio_od_summary"]) && !empty($row["gonio_os_summary"])){
						$tEye = "(OU)";	
					}else if(!empty($row["gonio_od_summary"])){
						$tEye = "(OD)";
					}else if(!empty($row["gonio_os_summary"])){
						$tEye = "(OS)";
					}
					
					$tests .= "Gonio";							 
					$tests .= $tEye;														
					$tests .= "<br>";
				}
				//Gonio -----------
				
				$prescription="";
				$prescription=getPrescriptionFromDate($patient_id,$row["date_of_service"],$prevDos);
				$prevDos = $row["date_of_service"];
				
				//-----------------------BEGIN IOP --------------------------------------------------------------
				{
				$iop = "";
				if(!empty($row["multiple_pressure"])){
					$arrMiop = getMAppIop($row["multiple_pressure"]);
					if(!empty($arrMiop["iop"])){
						$iop .= $arrMiop["iop"];
					}
				}else{						
				
					if(($row["applanation"] == "1") && (!empty($row["app_od"]) || !empty($row["app_os_1"])))
					{							
						
						$iop .= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"font-size:11px;\">";
						$iop .= "<tr><td class=\"text\" width=\"17\" valign=\"top\">"; 
						$iop .= "T<sub><b>A</b></sub>:</td>";
						$iop .= "<td align=\"center\" class=\"text\" width=\"23\" valign=\"top\">".$row["app_od"].",</td>";
						$iop .= "<td align=\"center\" class=\"text\" width=\"23\" valign=\"top\">".$row["app_os_1"]."</td></tr>";
						$iop .= "</table>";

					}
					
					if(($row["puff"] == "1") && (!empty($row["puff_od"]) || !empty($row["puff_os_1"])))
					{
						$iop .= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"font-size:11px;\">";
						$iop .= "<tr><td class=\"text\" width=\"17\" valign=\"top\">"; 
						$iop .= "T<sub><b>P</b></sub>:</td>";
						$iop .= "<td align=\"center\" class=\"text\" width=\"23\" valign=\"top\">".$row["puff_od"].",</td>";
						$iop .= "<td align=\"center\" class=\"text\" width=\"23\" valign=\"top\">".$row["puff_os_1"]."</td></tr>";
						$iop .= "</table>";
					}

					if(($row["tx"] == "1") && (!empty($row["tx_od"]) || !empty($row["tx_os"])))
					{
						$iop .= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"font-size:11px;\">";
						$iop .= "<tr><td class=\"text\" width=\"17\" valign=\"top\">"; 
						$iop .= "T<sub><b>x</b></sub>:</td>";
						$iop .= "<td align=\"center\" class=\"text\" width=\"23\" valign=\"top\">".$row["tx_od"].",</td>";
						$iop .= "<td align=\"center\" class=\"text\" width=\"23\" valign=\"top\">".$row["tx_os"]."</td></tr>";
						$iop .= "</table>";
					}
				}
				if(!empty($row["trgtOd"])&&!empty($row["trgtOs"])){
					$tmpod=(!empty($row["trgtOd"]))?$row["trgtOd"].",":"&nbsp;";
					$tmpos=(!empty($row["trgtOs"]))?$row["trgtOs"]:"&nbsp;";
					$iop .= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"font-size:11px;\"><tr><td valign=\"top\">Trgt: </td><td style=\"width:23px;\" valign=\"top\">".$tmpod.", </td><td style=\"width:23px;\" valign=\"top\">".$tmpos."</td>";
				}
				}
				//-----------------------END IOP --------------------------------------------------------------
				
				//-----------------------BEGIN CD --------------------------------------------------------------
				$cd = "";
				$cd .= (($row["od_text"] != "0.") && (!empty($row["od_text"])))||!empty($row["cd_val_od"]) ? "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"font-size:11px;\"><tr><td valign=\"top\"><font color=\"Blue\"><b>OD</b></font>:&nbsp;</td><td valign=\"top\">".trim($row["cd_val_od"]." ".$row["od_text"])."</td></tr></table>" : "";
				$cd .= (($row["os_text"]  != "0.") && (!empty($row["os_text"])))||!empty($row["cd_val_os"]) ? "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"font-size:11px;\"><tr><td valign=\"top\"><font color=\"Green\"><b>OS</b></font>:&nbsp;</td><td valign=\"top\">".trim($row["cd_val_os"]." ".$row["os_text"])."</td></tr></table>" : "";
				//-----------------------END CD --------------------------------------------------------------	
				
								
				//---------------------BEGIN Follow Up--------------------------------
				{
				$follow_up = "";
				if(!empty($row["followup"])){
					list($len_arrFu,$arrFu) = fu_getXmlValsArr($row["followup"]);
					if(count($arrFu) > 0){
						foreach($arrFu as $val){
							$tmpPro = (!empty($val["provider"])) ? getUserFirstName($val["provider"],3) : "";
							$tmp = trim($val["number"]." ".$val["time"]." ".$val["visit_type"]." ".$tmpPro);
							if(!empty($tmp)){
								$follow_up .= $tmp."<br>";
							}
						}
					}
					
				}else if(!empty($row['follow_up_numeric_value']) || !empty($row['follow_up']) ){
					if($row['follow_up_numeric_value']=='13')
						$row['follow_up_numeric_value'] = 'PRN';
					if($row['follow_up_numeric_value']=='14')
						$row['follow_up_numeric_value'] = 'PMD';
				
					$follow_up.=$row['follow_up_numeric_value']." ".$row['follow_up'];
					$follow_up.= (!empty($row["followUpVistType"])) ? " ".$row["followUpVistType"] : "";
					$follow_up .= "<br>";
				}
				
				$retina = $row['retina'];
				$neuro_ophth = $row['neuro_ophth'];
				$doctor_name = $row['doctor_name'];								
				if(!empty($retina) || !empty($neuro_ophth) || !empty($doctor_name)){							
					$follow_up .= ($retina == "1") ? "Retina<BR>" : "" ;
					$follow_up .= ($neuro_ophth == "1") ? "Neuro ophth<BR>" : "" ;
					$follow_up .= (!empty($doctor_name)) ? $doctor_name."<br>" : "";							
				}
				
				$continue_meds = $row['continue_meds'];
				$monitor_ag = $row['monitor_ag'];
				$id_precation = $row['id_precation'];
				$rd_precation = $row['rd_precation'];
				$lid_scrubs_oint = $row['lid_scrubs_oint'];					
				
				if(!empty($continue_meds) || !empty($monitor_ag) || !empty($id_precation) || !empty($rd_precation) || !empty($lid_scrubs_oint)){
					$follow_up .= ($continue_meds == "1") ? "Continue Meds.<BR>" : "" ;
					$follow_up .= ($monitor_ag == "1") ? "Monitor AG<BR>" : "" ;
					$follow_up .= ($id_precation == "1") ? "ID Precautions<BR>" : "" ;
					$follow_up .= ($rd_precation == "1") ? "RD Precautions<BR>" : "" ;
					$follow_up .= ($lid_scrubs_oint == "1") ? "Lid Scrubs & oint " : "" ;
				}
				}
				//---------------------END Follow Up----------------------------------------------------------
				
				//---------------------BEGIN Concate Plan and F/U--------------------------------------------------
				if(!empty($follow_up)){
					$plan .= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"font-size:11px;\"><tr><td valign=\"top\"><b>F/U:&nbsp;</b></td><td valign=\"top\">".$follow_up."</td></tr></table>"; 
					$flgPln = 1;
				}
				//---------------------END Concate Plan and F/U--------------------------------------------------
				/* Testing */
				$testing = "";
				$sx = "";
				$active_tests = ""; //Active Tests
				if($releaseNumber == "0"){
					$hrt = $row["hrt"];
					$hrtEye = $row["hrtEye"];
					$oct = $row["oct"];
					$octEye = $row["octEye"];
					$avf = $row["avf"];
					$avfEye = $row["avfEye"];								
					$ivfa = $row["ivfa"];
					$ivfaEye = $row["ivfaEye"];								
					$dfe = $row["dfe"];
					$dfeEye = $row["dfeEye"];								
					$photos = $row["photos"];
					$photosEye = $row["photosEye"];								
					$pachy = $row["pachy"];
					$pachyEye = $row["pachyEye"];
					//sx
					$cat_iol = $row["cat_iol"];
					$catIolEye = $row["catIolEye"];
					$yag_cap = $row["yag_cap"];
					$yagCapEye = $row["yagCapEye"];
					$slt = $row["altp"];
					$sltEye = $row["altpEye"];
					$pi = $row["pi"];
					$piEye = $row["piEye"];
					$ratinal_laser = $row["ratinal_laser"];
					$ratinalLaserEye = $row["ratinalLaserEye"];							
					
					if(!empty($hrt)){
						$testing .= ($hrt == "1") ? "HRT" : "";		
						$testing .= (!empty($hrtEye)) ? "($hrtEye)" : "";		
						$testing .= "<BR>";
					}
					
					if(!empty($oct)){
						$testing .= "OCT:<BR>";
						$testing .= $oct;
						$testing .= (!empty($octEye)) ? "($octEye)" : "";		
						$testing .= "<BR>";
					}
					
					if(!empty($avf)){
						$testing .= "AVF:<BR>";
						$testing .= $avf;
						$testing .= (!empty($avfEye)) ? "($avfEye)" : "";		
						$testing .= "<BR>";
					}
					
					if(!empty($ivfa)){
						$testing .= ($ivfa == "1") ? "IVFA" : "";		
						$testing .= (!empty($ivfaEye)) ? "($ivfaEye)" : "";		
						$testing .= "<BR>";
					}
					
					if(!empty($dfe)){
						$testing .= ($dfe == "1") ? "DFE" : "";		
						$testing .= (!empty($dfeEye)) ? "($dfeEye)" : "";		
						$testing .= "<BR>";
					}
					
					if(!empty($photos)){
						$testing .= "Photos:<BR>";
						$testing .= $photos;
						$testing .= (!empty($photosEye)) ? "($photosEye)" : "";		
						$testing .= "<BR>";	
					}
					
					if(!empty($pachy)){
						$testing .= ($pachy == "1") ? "Pachy" : "";		
						$testing .= (!empty($pachyEye)) ? "($pachyEye)" : "";		
						$testing .= "<BR>";
					}
					
					//Sx
					if(!empty($cat_iol)){
						$sx .= "Cat.IOL";
						$sx.= (!empty($catIolEye)) ? "($catIolEye)" : ""; 
						$sx.= "<br>";
					}
					if(!empty($yag_cap)){
						$sx .= "Yag cap.";
						$sx.= (!empty($yagCapEye)) ? "($yagCapEye)" : ""; 
						$sx.= "<br>";
					}
					if(!empty($slt)){
						$sx .= "SLT";
						$sx.= (!empty($sltEye)) ? "($sltEye)" : ""; 
						$sx.= "<br>";
					}
					if(!empty($pi)){
						$sx .= "PI";
						$sx.= (!empty($piEye)) ? "($piEye)" : ""; 
						$sx.= "<br>";
					}
					if(!empty($ratinal_laser)){
						$sx .= "R.Laser";
						$sx.= (!empty($ratinalEye)) ? "($ratinalLaserEye)" : ""; 
						$sx.= "<br>";
					}
					
					$testing = "<div>".$testing."</div>";
					
				}else if($releaseNumber == "1"){
					list($testing,$sx) = getTestingR2($formID,0);
				}					
				

				//-------------------BEGIN comments: assessment & plan-------------------------------------------
				if(!empty($row["plan_notes"]) && trim($row["plan_notes"])!="Comments:"){
					$plan .= "<b>Comments:</b> ".$row["plan_notes"];
					$flgPln = 1;
				}
				//-------------------END comments: assessment & plan-------------------------------------------
				
				//Make ap table

				$formID = $row["formID"];
				$pendingOrderStr = @join('<br>',$pendingOrderArr[$formID]);
				$completedOrderStr = @join('<br>',$completedOrderArr[$formID]);
				if(!empty($completedOrderStr)) $flgOrdr = 1;
				
				//Draw
				$draw="";
				//echo "t:".getChartDrawing($formID,"check").";";
				if(getChartDrawing($formID,"check")){
					/*
					$draw="<img src=\"".$GLOBALS['webroot'] ."/interface/chart_notes/common/requestHandler.php?elem_formAction=showImg2&formId=".$formID."\" 
								alt=\"img\"  ";
					*/
					if($_SERVER["SERVER_PORT"] == 80){
						$phpHTTPProtocol="http://";
					}
					if($phpServerIP != $_SERVER['HTTP_HOST']){
						$phpServerIP = $_SERVER['HTTP_HOST'];
						//$GLOBALS['php_server'] = $phpHTTPProtocol.$phpServerIP.$phpServerPort.$web_root;
					}
					//$draw = getChartDrawing($formID,$mode="","full");
					$draw = $GLOBALS['php_server']."/app_services/?reqModule=pag&pag_service=getChartDrawing&patId=".$this->patient."&formId=".$formID."";
					//$draw = $GLOBALS['php_server']."/interface/chart_notes/common/requestHandler.php?ignoreAuth=1&elem_formAction=showImg2&pid=".$this->patient."&formId=".$formID."&filetype=full"; 
/*					if($pdg_showpop==0) { 
						$draw="<input type=\"button\" name=\"elm_btndrw\" value=\"Drawing\" class=\"dff_button_smll dff_button\" align=\"center\" ";
						$draw.= "onclick=\"showBigImage('".$formID."')\" class=\"hand_cur\" title=\"Click to See Drawing\" >" ; 
					}else{
						$draw.="<span class=\"btnddrw\">Drawing</span>";
					}
*/				}
				
				//-----------------BEGIN RX-------------------------------------------------------
				$rx="";		
				$rxArr = getChartRx($formID);
				if(count($rxArr)>0){
					$rx="";
					$rx.="<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"font-size:11px;\">";
					foreach($rxArr as $keyrx=> $valrx){
						$rx .= "<tr><td valign=\"top\">&bull;&nbsp;</td><td valign=\"top\">".$valrx."</td></tr>";
					}
					$rx.="</table>";
				}
				//-----------------END RX------------------------------------------------------------
				// Make Array
				$arrValues[$i] = array( "date"=>$date, "vision"=>$vision, "iop"=>$iop, "cd"=>$cd,
								"tests"=>$completedOrderStr,"assessment"=>$assessment, "plan"=>preg_replace('/<p>&bull;<\/p>/','&bull;',$plan),
								"follow_up"=>$follow_up, "testing"=>$pendingOrderStr, "prescription"=>$prescription,
								"date2"=>$date2, "sx"=>$sx, 
								"sign"=>$sign, "cosign"=>$cosign,
								"signNm"=>$signNm, "cosignNm"=>$cosignNm,"draw"=>$draw,
								"rx"=>$rx
								);
				// Check previous
				if($i>0){
					if($vision == $visionChk) {
						//$arrValues[$i-1]["vision"] = "";
					}else{
						$visionChk = $vision ;
					}
					if($iop == $iopChk){
						//$arrValues[$i-1]["iop"] = "";
					}else{
						$iopChk = $iop ;
					}
					if($cd == $cdChk){
						//$arrValues[$i-1]["cd"] = "";
					}else{
						$cdChk = $cd ;
					}
					if($tests == $testsChk){
						//$arrValues[$i-1]["tests"] = "";
					}else{
						$testsChk = $tests;
					}
				}
				else
				{
					$visionChk = $vision;
					$iopChk = $iop;
					$cdChk = $cd;
					$testsChk = $tests;
					$assessmentChk = $assessment;
					$planChk = $plan;
					$follow_upChk = $follow_up;
					$testingChk = $testing;
				}
				
		}
		$len = count($arrValues);
		for($i=0;$i<$len;$i++){
			$arrReturn[] = array("Date"=>$arrValues[$i]["date"],
								 "vision"=>preg_replace('/\s{1,}/',' ',$arrValues[$i]["vision"]),
								 "iop"=>preg_replace('/\s{1,}/',' ',$arrValues[$i]["iop"]),
								 "cd"=>preg_replace('/\s{1,}/',' ',$arrValues[$i]["cd"]),
								 "assessment"=>preg_replace('/\s{1,}/',' ',$arrValues[$i]["assessment"]),
								 "plan"=>preg_replace('/\s{1,}/',' ',$arrValues[$i]["plan"]),
								 "rx"=>preg_replace('/\s{1,}/',' ',$arrValues[$i]["rx"]),
								 "draw"=>$arrValues[$i]["draw"]
								);
		
		}
		$return['patient_chart_diagnosis'] = $arrReturn;
		return $return;						
	}
	public function chart_history_html(){
		include_once $GLOBALS['srcdir']."/classes/pt_at_glance.class.php";
		include_once($GLOBALS['srcdir']."/classes/class.tests.php");
		include_once($GLOBALS['srcdir']."/classes/SaveFile.php");
		
		$pt_glance = new Pt_at_glance($_REQUEST['patId'],$_REQUEST['phyId'],$_REQUEST);
		
		echo $pt_glance->get_pt_diagnostic($pt_glance->patient_id,$pdg_showpop=0,$pdg_hiderow1=0,$st_index,true,true);
		//echo draw_pt_diagnostic_ipadApp($this->patient,0,0,1,$this->authId);
		
		/*if($_REQUEST['width'] == "320px" || $_REQUEST['width'] == "320" || $_REQUEST['width'] == "568px" || $_REQUEST['width'] == "568" || $_REQUEST['width'] == "480px" || $_REQUEST['width'] == "480" || $_REQUEST['width'] == "900px" || $_REQUEST['width'] == "900")
		echo draw_pt_diagnostic_iphoneApp($this->patient,0,0,1,$this->authId); 
		else
		echo draw_pt_diagnostic_ipadApp($this->patient,0,0,1,$this->authId);*/
		die();
		
	}
	//Function --
	function getChartDrawing(){
		$formId = $_REQUEST['formId'];
		$filetype = 'full';
		$pid = $_REQUEST['patId'];
		include_once(dirname(__FILE__)."/../../interface/chart_notes/common/functions.php");
		require_once(dirname(__FILE__)."/../../interface/chart_notes/common/SaveFile.php");
		$formId=$formId;
		$filetype=$filetype;
		$pid = (isset($pid) && $pid!="")?$pid:"";
		
		$pth = getChartDrawing($formId,$mode="",$filetype,$pid);
		if(constant("REMOTE_SYNC") == 1 && !empty($zOnParentServer)){
			if(!empty($pth)){
				if(strpos($pth,".gif")!==false){
					$zRemoteServerData["header_content_type"] = 'Content-type: image/gif';
				}else{
					$zRemoteServerData["header_content_type"] = 'Content-type: image/png';
				}
			}else{
				$pth=$GLOBALS['incdir']."/chart_notes/images/tpixel.gif";
				$zRemoteServerData["header_content_type"] = 'Content-type: image/gif';
			}
		
		}else{
			if(!empty($pth)){
				if(strpos($pth,".gif")!==false){
				header('Content-type: image/gif');
				}else{
				header('Content-type: image/png');
				}
			}else{
				$pth=$GLOBALS['incdir']."/chart_notes/images/tpixel.gif";
				header('Content-type: image/gif');
			}
		}
		
		readfile($pth);/**/
	}
}

?>