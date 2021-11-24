<?php
class TestInfo{
	public $arrName;
	public function __construct(){
		$this->arrName = array("VF","VF-GL","Topography","Other","Labs","Pachy","OCT","OCT-RNFL","GDX", "HRT","IVFA","ICG","Fundus",
						 "External/Anterior","A/Scan","B-Scan","Cell Count","IOL Master","TemplateTests","CustomTests");
	}
	
	function getTestNames(){
		return $this->arrName;
	}
	
	function getTestFormFields($testName,$fAssc="0"){
		switch($testName){
			case "VF":
				$field_FormId = "formId";
				$tbl_test = "vf";
				$field_Key = "vf_id";
				$f_ptId = "patientId";
				$f_edt = "examDate";

			break;
			case "VF-GL":
				$field_FormId = "formId";
				$tbl_test = "vf_gl";
				$field_Key = "vf_gl_id";
				$f_ptId = "patientId";
				$f_edt = "examDate";

			break;
			case "NFA":
			case "HRT":
				$field_FormId = "form_id";
				$tbl_test = "nfa";
				$field_Key = "nfa_id";
				$testName = "NFA"; //tst name in scan
				$f_ptId = "patient_id";
				$f_edt = "examDate";

			break;
			case "OCT":
				$field_FormId = "form_id";
				$tbl_test = "oct";
				$field_Key = "oct_id";
				$f_ptId = "patient_id";
				$f_edt = "examDate";

			break;
			
			case "OCT-RNFL":
				$field_FormId = "form_id";
				$tbl_test = "oct_rnfl";
				$field_Key = "oct_rnfl_id";
				$f_ptId = "patient_id";
				$f_edt = "examDate";

			break;

			case "Pacchy":
			case "Pachy":
				$field_FormId = "formId";
				$tbl_test = "pachy";
				$field_Key = "pachy_id";
				$testName = "Pacchy";  //tst name in scan
				$f_ptId = "patientId";
				$f_edt = "examDate";

			break;
			case "IVFA":
				$field_FormId = "form_id";
				$tbl_test = "ivfa";
				$field_Key = "vf_id";
				$f_ptId = "patient_id";
				$f_edt = "exam_date";

			break;
			case "ICG":
				$field_FormId = "form_id";
				$tbl_test = "icg";
				$field_Key = "icg_id";
				$f_ptId = "patient_id";
				$f_edt = "exam_date";

			break;		
			case "Disc":
			case "Fundus":
				$field_FormId = "formId";
				$tbl_test = "disc";
				$field_Key = "disc_id";
				$testName = "Disc"; //tst name in scan
				$f_ptId = "patientId";
				$f_edt = "examDate";

			break;

			case "discExternal":
			case "External/Anterior":
			case "External":
				$field_FormId = "formId";
				$tbl_test = "disc_external";
				$field_Key = "disc_id";
				$testName = "discExternal"; //tst name in scan
				$f_ptId = "patientId";
				$f_edt = "examDate";

			break;
			case "Topogrphy":
			case "Topography":
				$field_FormId = "formId";
				$tbl_test = "topography";
				$field_Key = "topo_id";
				$testName = "Topogrphy"; //tst name in scan
				$f_ptId = "patientId";
				$f_edt = "examDate";

			break;
			case "TestOther":
			case "Other":
			case "TemplateTests":
				$field_FormId = "formId";
				$tbl_test = "test_other";
				$field_Key = "test_other_id";
				$testName = "testOther"; //tst name in scan
				$f_ptId = "patientId";
				$f_edt = "examDate";

			break;
			case "CustomTests":
				$field_FormId = "formId";
				$tbl_test = "test_custom_patient";
				$field_Key = "test_id";
				$testName = "testOther"; //tst name in scan
				$f_ptId = "patientId";
				$f_edt = "examDate";

			break;
			case "Laboratories":
			case "TestLabs":
			case "Labs":
				$field_FormId = "formId";
				$tbl_test = "test_labs";
				$field_Key = "test_labs_id";
				$testName = "testLabs"; //tst name in scan
				$f_ptId = "patientId";
				$f_edt = "examDate";

			break;
			case "A/Scan":
			case "Ascan":
			case "A-Scan":
				$field_FormId = "form_id";
				$tbl_test = "surgical_tbl";
				$field_Key = "surgical_id";
				$testName = "Ascan"; //tst name in scan
				$f_ptId = "patient_id";
				$f_edt = "examDate";

			break;
			
			case "iOLMaster":
			case "IOL Master":
			case "IOL_Master":
				$field_FormId = "form_id";
				$tbl_test = "iol_master_tbl";
				$field_Key = "iol_master_id";
				$testName = "IOL_Master"; //tst name in scan
				$f_ptId = "patient_id";
				$f_edt = "examDate";

			break;

			case "B-Scan":
			case "BScan":
				$field_FormId = "formId";
				$tbl_test = "test_bscan";
				$field_Key = "test_bscan_id";
				$testName = "BScan"; //tst name in scan
				$f_ptId = "patientId";
				$f_edt = "examDate";
			break;

			case "Cell Count":
			case "CellCount":
				$field_FormId = "formId";
				$tbl_test = "test_cellcnt";
				$field_Key = "test_cellcnt_id";
				$testName = "CellCount"; //tst name in scan
				$f_ptId = "patientId";
				$f_edt = "examDate";
			break;
			case "GDX":
				$field_FormId = "form_id";
				$tbl_test = "test_gdx";
				$field_Key = "gdx_id";
				$f_ptId = "patient_id";
				$f_edt = "examDate";
			break;		
			
			default:
				exit("NOT Defined: ".$testName);
			break;

		}

		//Test
		if($fAssc == 1){
			return array("formId"=>$field_FormId,"tbl"=>$tbl_test,"keyId"=>$field_Key,"testNm"=>$testName,"ptId"=>$f_ptId,"eDt"=>$f_edt);
		}else{
			return array($field_FormId,$tbl_test,$field_Key,$testName,$f_ptId,$f_edt);
		}
	}
	
	function get_active_tests(){
		$ar=array();
		$sql = " SELECT test_name FROM `tests_name` where status = '1' ";
		$rez = sqlStatement($sql);
		for($i=1;$row = sqlFetchArray($rez);$i++){
			if(!empty($row["test_name"])){
				$ar[] = $row["test_name"];
			}
		}
		return $ar;
	}
	
	function get_tests_names_show(){
		$ar=array();
		$sql = " SELECT test_table, temp_name, test_name  FROM `tests_name` where status = '1' AND del_status ='0' ";
		$rez = sqlStatement($sql);
		for($i=1;$row = sqlFetchArray($rez);$i++){
			if(!empty($row["temp_name"]) && !empty($row["test_table"])){
				$ar[$row["test_table"]] = $row["temp_name"];
				$ar[$row["test_name"]] = $row["temp_name"];
			}
		}
		return $ar;	
	}	
}


?>