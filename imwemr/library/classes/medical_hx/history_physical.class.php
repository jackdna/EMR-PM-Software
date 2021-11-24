<?php
/*
 The MIT License (MIT)
 Distribute, Modify and Contribute under MIT License
 Use this software under MIT License
 
 Coded in PHP7
 Purpose: Class File Related to History Physical
 Access Type: Indirect Access.
 
*/
include_once 'medical_history.class.php';
class HistoryPhysical extends MedicalHistory
{
	public $vocabulary = false;
	public $data = false;
	public $custom_ques = false;
	public $pt_custom_ques = false;	

	public function __construct($tab = 'ocular')
	{
		parent::__construct($tab);
		$this->vocabulary = $this->get_vocabulary("medical_hx", "history_physical");
		$this->data = $this->load_history_physical();
		$this->custom_ques = $this->load_hp_custom_ques();
		$this->pt_custom_ques = $this->get_hp_pt_custom_ques();
        
	}
	
	public function load_history_physical()
	{
		$return = array();
		$qryHP = "select hp.*, PD.ado_option as ptAdoOption, PD.desc_ado_other_txt as ptDescAdoOtherTxt from surgerycenter_pt_history_physical hp INNER JOIN 
									patient_data PD on PD.id = hp.patient_id where patient_id='".$this->patient_id."' ";
		$rsQryHP = imw_query($qryHP);
		$loop_count = imw_num_rows($rsQryHP);
		if($rsQryHP && imw_num_rows($rsQryHP) > 0)
		{
			$return = imw_fetch_assoc($rsQryHP);
      $return['record_count']=$loop_count;
		}
		
		$qry = "Select ";
		return $return;
	}
    
	public function load_hp_custom_ques()
	{
		$return = array();
		$qryHP = "select * from surgerycenter_history_physical_ques where deleted='0' order by id ASC ";
		$rsQryHP = imw_query($qryHP);
		if($rsQryHP && imw_num_rows($rsQryHP) > 0)
		{
			while($row = imw_fetch_assoc($rsQryHP)) {
                $return[]=$row;
            }
            
		}
		
		return $return;
	}
    
	public function get_hp_pt_custom_ques()
	{
		$return = array();
		$qryHP = "select * from surgerycenter_pt_history_physical_ques where patient_id='".$this->patient_id."' order by id ASC ";
		$rsQryHP = imw_query($qryHP);
		if($rsQryHP && imw_num_rows($rsQryHP) > 0)
		{
			while($row = imw_fetch_assoc($rsQryHP)) {
                $return[$row['ques_id']]=$row;
            }
            
		}
		
		return $return;
	}
	
	
	/*
	* Save No Known Medical Conditions of patient 
	* From H&P Chart in General Health Chart.
	*/
	
	public function save_med_cond(){
		
		$HPGHMap = array(1=>'htnCP',2=>'cadMI',7=>'arthritis',4=>'respiratoryAsthma', 5=>'cvaTIA',6=>'thyroid',3=>'diabetes',13=>'highCholesterol',8=>'ulcer',14=>'historyCancer');
		
		// Get patient record from H&P Chart
		$qry = "Select * From surgerycenter_pt_history_physical Where patient_id = ".(int) $this->patient_id." ";
		$sql = imw_query($qry);
		$rowHP = imw_fetch_assoc($sql);
		
		// get patient record from general health table
		$fld = "any_conditions_others,desc_u,desc_high_bp, desc_arthrities, desc_lung_problem, desc_stroke, desc_thyroid_problems, desc_ulcers, desc_LDL, desc_cancer, desc_heart_problem";
		$qry = "Select ".$fld."  From general_medicine Where patient_id = ".(int)$this->patient_id." ";
		$sql = imw_query($qry);
		$cnt = imw_num_rows($sql);
		$row = imw_fetch_assoc($sql);
		
		
		$condYesArr = array();
		$condNoArr = array();
		
		foreach($HPGHMap as $k => $m ){
			if( $rowHP[$m] == 'Yes' ) array_push($condYesArr,$k);
			else if( $rowHP[$m] == 'No' ) array_push($condNoArr,$k);
		}
		
		$any_conditions_you = ",".implode(",",$condYesArr).",";
		$any_conditions_you_n = ",".implode(",",$condNoArr).",";
		
		// Set text field descriptions for patient
		$txtHighBloodPresher = $rowHP['htnCPDesc'];
		$txtHighBloodPresher = get_set_pat_rel_values_save($row["desc_high_bp"],$txtHighBloodPresher,"pat",$this->delimiter);

		$txtHeartProblem = $rowHP['cadMIDesc'];
		$txtHeartProblem = get_set_pat_rel_values_save($row["desc_heart_problem"],$txtHeartProblem,"pat",$this->delimiter);

		$txtArthrities = $rowHP['arthritisDesc'];
		$txtArthrities = get_set_pat_rel_values_save($row["desc_arthrities"],$txtArthrities,"pat",$this->delimiter);

		$txtLungProblem = $rowHP['respiratoryAsthmaDesc'];
		$txtLungProblem = get_set_pat_rel_values_save($row["desc_lung_problem"],$txtLungProblem,"pat",$this->delimiter);

		$txtStroke = $rowHP['cvaTIADesc'];
		$txtStroke = get_set_pat_rel_values_save($row["desc_stroke"],$txtStroke,"pat",$this->delimiter);

		$txtThyroidProblems = $rowHP['thyroidDesc'];
		$txtThyroidProblems = get_set_pat_rel_values_save($row["desc_thyroid_problems"],$txtThyroidProblems,"pat",$this->delimiter);

		$desc_u = $rowHP["diabetesDesc"];
		$desc_u = get_set_pat_rel_values_save($row["desc_u"],$desc_u,"pat",$this->delimiter);

		$txtLDL = $rowHP["highCholesterolDesc"];
		$txtLDL = get_set_pat_rel_values_save($row["desc_LDL"],$txtLDL,"pat",$this->delimiter);

		$txtUlcers = $rowHP['ulcerDesc'];
		$txtUlcers = get_set_pat_rel_values_save($row["desc_ulcers"],$txtUlcers,"pat",$this->delimiter);

		$txtCancer = $rowHP['historyCancerDesc'];
		$txtCancer = get_set_pat_rel_values_save($row["desc_cancer"],$txtCancer,"pat",$this->delimiter);

		$any_conditions_others1 = $rowHP["otherHistoryPhysical"];
		$any_conditions_others1 = get_set_pat_rel_values_save($row["any_conditions_others"],$any_conditions_others1,"pat",$this->delimiter);
		
		// Update in General Health Table
		$upQry = "Insert Into general_medicine Set ";
		$upQry.= "patient_id = ".(int)$this->patient_id.", ";
		
		$upWhere = '';
		if( $cnt > 0 ) {
			$upQry = "Update general_medicine Set ";
			$upWhere = "Where patient_id = ".(int)$this->patient_id."  ";
		} 
		
		$upQry.= "any_conditions_you = '".$any_conditions_you."', ";
		$upQry.= "any_conditions_you_n = '".$any_conditions_you_n."', ";
		$upQry.= "desc_high_bp = '".$txtHighBloodPresher."', ";
		$upQry.= "desc_heart_problem = '".$txtHeartProblem."', ";
		$upQry.= "desc_arthrities = '".$txtArthrities."', ";
		$upQry.= "desc_lung_problem = '".$txtLungProblem."', ";
		$upQry.= "desc_stroke = '".$txtStroke."', ";
		$upQry.= "desc_thyroid_problems = '".$txtThyroidProblems."', ";
		$upQry.= "desc_u = '".$desc_u."', ";
		$upQry.= "desc_LDL = '".$txtLDL."', ";
		$upQry.= "desc_ulcers = '".$txtUlcers."', ";
		$upQry.= "desc_cancer = '".$txtCancer."', ";
		$upQry.= "any_conditions_others = '".$any_conditions_others1."' ";
		$upQry.= $upWhere;
		
		$upSql = imw_query($upQry);
		
		return false;
	}
	
	public function save_advance_directive($ado_option_value,$ado_option_text_value){
		//--- UPDATE ADO OPTIONS ----
		$ado_patient_data_saveqry = "update patient_data set desc_ado_other_txt = '".imw_real_escape_string(htmlentities($ado_option_text_value))."' ,
									ado_option = '".imw_real_escape_string(htmlentities($ado_option_value))."' 
									where id = ".(int)$this->patient_id." ";
		$ado_saveSql = imw_query($ado_patient_data_saveqry);
		
	}
	
	public function load_sx_procedures(){
		
		$query = "select id,title,type,
								if((DAY(begdate)='00' OR DAY(begdate)='0') && YEAR(begdate)='0000' && (MONTH(begdate)='00' OR MONTH(begdate)='0'),'',
									if((DAY(begdate)='00' OR DAY(begdate)='0') && (MONTH(begdate)='00' OR MONTH(begdate)='0'),date_format(begdate, '%Y'),
										if(MONTH(begdate)='00' OR MONTH(begdate)='0',date_format(begdate,'%Y'),
											if(DAY(begdate)='00' or DAY(begdate)='0',date_format(begdate,'%m-%Y'),
											date_format(begdate,'".get_sql_date_format()."')
								))))as begdate1, comments,sites 
								from lists where pid = '".$this->patient_id."' and type in (5,6) and allergy_status != 'Deleted' order by begdate desc,id desc";
		$sql = imw_query($query);
		$cnt = imw_num_rows($sql);
		
		$sx_exists = commonNoMedicalHistoryAddEdit($moduleName="Surgery",$moduleValue="",$mod="get");
		
		$return = array();
		$ocuProc = array();
		$sysProc = array();
		
		while( $sxQryRes = imw_fetch_assoc($sql) ) 
		{
			$dataArr = array();
			if($sxQryRes['id'] != ''){
				$sx_exists = 'disabled';
			}
			$type = $sxQryRes['type'];
			
			$site = '';
			if( $sxQryRes['sites'] == '1') $site = 'OS';
			if( $sxQryRes['sites'] == '2') $site = 'OD';
			if( $sxQryRes['sites'] == '3') $site = 'OU';
			
			$dataArr['name'] = $sxQryRes['title'];
			$dataArr['site'] = $site;
			$dataArr['comment'] = stripslashes($sxQryRes['comments']);
			$dataArr['beg_date'] = $sxQryRes['begdate1'];
			
				
			if($type == 6) $ocuProc[] = $dataArr;
			else if($type == 5) $sysProc[] = $dataArr;
			
		}
		
		$return['NKDA'] = $sx_exists;
		$return['ocu'] = $ocuProc;
		$return['sys'] = $sysProc;
		
		return $return;
	}
	
}


?>