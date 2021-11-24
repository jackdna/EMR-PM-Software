<?php

require_once(dirname(__FILE__) . "/ecqm_data.php");

class ECQMMedicalHx extends ECQMData {

    public function __construct($patient_id = false, $pro_id = false, $CMS_ID = false) {
        
        $this->patient_id = $patient_id;
        $this->CMS_ID = $CMS_ID;
        $this->pro_id = $pro_id;
        
        parent::__construct();
        
    }

    
    public function get_pt_problem_list() {
        
        $dataFinal = $pt_prb_data= array();
        $data=' id, pt_id, user_id,	problem_name, comments,	onset_date,	status,	signerId, coSignerId, OnsetTime, prob_type,	form_id, timestamp,	ccda_code, end_datetime ';
        $sql = "SELECT ".$data." FROM pt_problem_list WHERE pt_id IN('".$this->patient_id."') AND status='Active' AND prob_type!='Condition' AND form_id=0 ";
        $res = imw_query($sql);
        if($res && imw_num_rows($res)>0) {
            while ($row = imw_fetch_assoc($res)) {
                $pt_prb_data[] = $row;
            }
        }

        $ecqm_v8_data=$this->ecqm_v8_data($this->CMS_ID);
        
        foreach($pt_prb_data as $item) {
            $test=$test1=$test2=array();
            if($item['ccda_code']!='') {
                $snomed=$item['ccda_code'];
                if(isset($ecqm_v8_data['SNOMEDCT'][$snomed])) {
                    $test['Value_Set_OID']=$ecqm_v8_data['SNOMEDCT'][$snomed]['Value_Set_OID'];
                    $test['Code_System']=$ecqm_v8_data['SNOMEDCT'][$snomed]['Code_System'];
                    $test['Code']=$ecqm_v8_data['SNOMEDCT'][$snomed]['Code'];
                    $test['Description']=$ecqm_v8_data['SNOMEDCT'][$snomed]['Description'];
                    $test['Code_System_OID']=$ecqm_v8_data['SNOMEDCT'][$snomed]['Code_System_OID'];
                    $test['CMS_ID']=$ecqm_v8_data['SNOMEDCT'][$snomed]['CMS_ID'];
                    $test['Value_Set_Name']=$ecqm_v8_data['SNOMEDCT'][$snomed]['Value_Set_Name'];
                }
            }
            $item['problem_snomed']=$test;
            $pt_problem_data=$this->filterProblemDx($item['problem_name']);
            $item['dx_problem_name']=$pt_problem_data['title'];
            $dxCode=trim($pt_problem_data['dxCode'][0]);
            if($dxCode!='' && isset($ecqm_v8_data['ICD10CM'][$dxCode])) {
                $test1['Value_Set_OID']=$ecqm_v8_data['ICD10CM'][$dxCode]['Value_Set_OID'];
                $test1['Code_System']=$ecqm_v8_data['ICD10CM'][$dxCode]['Code_System'];
                $test1['Code']=$ecqm_v8_data['ICD10CM'][$dxCode]['Code'];
                $test1['Description']=$ecqm_v8_data['ICD10CM'][$dxCode]['Description'];
                $test1['Code_System_OID']=$ecqm_v8_data['ICD10CM'][$dxCode]['Code_System_OID'];
                $test1['CMS_ID']=$ecqm_v8_data['ICD10CM'][$dxCode]['CMS_ID'];
                $test1['Value_Set_Name']=$ecqm_v8_data['ICD10CM'][$dxCode]['Value_Set_Name'];
                $test1['dx_problem_name']=$pt_problem_data['problem'];
            }
            $item['problem_icd10']=$test1;
            $icd9Code=trim($pt_problem_data['icd9Code'][0]);
            if($icd9Code!='' && isset($ecqm_v8_data['ICD9CM'][$icd9Code])) {
                $test2['Value_Set_OID']=$ecqm_v8_data['ICD9CM'][$icd9Code]['Value_Set_OID'];
                $test2['Code_System']=$ecqm_v8_data['ICD9CM'][$icd9Code]['Code_System'];
                $test2['Code']=$ecqm_v8_data['ICD9CM'][$icd9Code]['Code'];
                $test2['Description']=$ecqm_v8_data['ICD9CM'][$icd9Code]['Description'];
                $test2['Code_System_OID']=$ecqm_v8_data['ICD9CM'][$icd9Code]['Code_System_OID'];
                $test2['CMS_ID']=$ecqm_v8_data['ICD9CM'][$icd9Code]['CMS_ID'];
                $test2['Value_Set_Name']=$ecqm_v8_data['ICD9CM'][$icd9Code]['Value_Set_Name'];
            }
            $item['problem_icd9']=$test2;
            
            $dataFinal[]=$item;
            
        }
        
        return $dataFinal;
    }
    
    
    //filter icd10 code from problem name
    private function filterProblemDx($data)
	{
		$data = trim($data);
		$returnData = array('dxCode'=>'', 'problem'=>'');
		
		$lastChar = substr($data, -1);
		if($lastChar == ")"){

            $icd10DxCode=$icd9DxCode=$cptCode=array();
			$icd10Dx = preg_replace('/^(?:.*\()(.*)\)/D', '$1', $data);	/*Capture ICD10 codes from end*/
			$icd10Dx = preg_replace('/\s+/', '', $icd10Dx);	/*Replace space*/
			$icd10Dx = explode(',', $icd10Dx);	/*Split by dx Codes separator*/
            foreach($icd10Dx as $item) {
                if(strpos($item,'ICD-10-CM')!==false){
                    $icd10DxCo=str_ireplace('ICD-10-CM', '', $item);
                    $icd10DxCode[]= trim($icd10DxCo);
                }
                if(strpos($item,'ICD-9-CM')!==false){
                    $icd9DxCo=str_ireplace('ICD-9-CM', '', $item);
                    $icd9DxCode[]= trim($icd9DxCo);
                }
                if(strpos($item,'CPT-CM')!==false){
                    $cpt_code=str_ireplace('CPT-CM', '', $item);
                    $cptCode[]= trim($cpt_code);
                }
            }
			$returnData['dxCode'] = $icd10DxCode;
			$returnData['icd9Code'] = $icd9DxCode;
			$returnData['cptCode'] = $cptCode;
			$returnData['title'] = preg_replace('/\([^\(]*$/', '', $data);
            
		}
		return($returnData);
	}
    
    
    //-------BEGIN Medication SECTION --------------//
    public function pt_medications_data() {
        $pt_medications_data=array();
        $qry = "SELECT * FROM lists WHERE pid = '".$this->patient_id."' and type in(1,4) and (allergy_status='Active' OR allergy_status='Order')";
        $res = imw_query($qry);
        if ($res && imw_num_rows($res)>0) {
            
            $ecqm_v8_data=$this->ecqm_v8_data($this->CMS_ID);
            
            while ($row = imw_fetch_assoc($res)) {
                $refusal_med = ($row['refusal'] && $row['refusal_snomed'] ) ? true : false;
                
                $ccda_code=trim($row['ccda_code']);
                if(isset($ecqm_v8_data['SNOMEDCT'][$ccda_code])) {
                    $row['Value_Set_OID']=$ecqm_v8_data['SNOMEDCT'][$ccda_code]['Value_Set_OID'];
                    $row['Code_System']=$ecqm_v8_data['SNOMEDCT'][$ccda_code]['Code_System'];
                    $row['Code']=$ecqm_v8_data['SNOMEDCT'][$ccda_code]['Code'];
                    $row['Description']=$ecqm_v8_data['SNOMEDCT'][$ccda_code]['Description'];
                    $row['Code_System_OID']=$ecqm_v8_data['SNOMEDCT'][$ccda_code]['Code_System_OID'];
                    $row['CMS_ID']=$ecqm_v8_data['SNOMEDCT'][$ccda_code]['CMS_ID'];
                    $row['Value_Set_Name']=$ecqm_v8_data['SNOMEDCT'][$ccda_code]['Value_Set_Name'];
                } else if(isset($ecqm_v8_data['RXNORM'][$ccda_code])) {
                    $row['Value_Set_OID']=$ecqm_v8_data['RXNORM'][$ccda_code]['Value_Set_OID'];
                    $row['Code_System']=$ecqm_v8_data['RXNORM'][$ccda_code]['Code_System'];
                    $row['Code']=$ecqm_v8_data['RXNORM'][$ccda_code]['Code'];
                    $row['Description']=$ecqm_v8_data['RXNORM'][$ccda_code]['Description'];
                    $row['Code_System_OID']=$ecqm_v8_data['RXNORM'][$ccda_code]['Code_System_OID'];
                    $row['CMS_ID']=$ecqm_v8_data['RXNORM'][$ccda_code]['CMS_ID'];
                    $row['Value_Set_Name']=$ecqm_v8_data['RXNORM'][$ccda_code]['Value_Set_Name'];
                }
                $row['med_refusal']=array();
                $row['refusal_med']=$refusal_med;
                if($refusal_med) {
                    $refusal_snomed=$row['refusal_snomed'];
                    if(isset($ecqm_v8_data['SNOMEDCT'][$refusal_snomed])) {
                        $temp4=array();
                        $temp4['Value_Set_OID']=$ecqm_v8_data['SNOMEDCT'][$refusal_snomed]['Value_Set_OID'];
                        $temp4['Code_System']=$ecqm_v8_data['SNOMEDCT'][$refusal_snomed]['Code_System'];
                        $temp4['Code']=$ecqm_v8_data['SNOMEDCT'][$refusal_snomed]['Code'];
                        $temp4['Description']=$ecqm_v8_data['SNOMEDCT'][$refusal_snomed]['Description'];
                        $temp4['Code_System_OID']=$ecqm_v8_data['SNOMEDCT'][$refusal_snomed]['Code_System_OID'];
                        $temp4['CMS_ID']=$ecqm_v8_data['SNOMEDCT'][$refusal_snomed]['CMS_ID'];

                        $row['med_refusal']=$temp4;
                    }
                }
                $pt_medications_data[]=$row;
            }
        }
        
        return $pt_medications_data;

    }
    //-------END Medication SECTION --------------//
    
    
    //-------BEGIN Procedure SECTION --------------//
    public function pt_procedure_data() {
		$pt_procedure_data = array();
        $qry = "SELECT * FROM lists WHERE pid = '".$this->patient_id."' and type = '5' and allergy_status='Active' and proc_type='procedure' ";
        $res = imw_query($qry);
        if ($res && imw_num_rows($res) > 0) {
            $ecqm_v8_data=$this->ecqm_v8_data($this->CMS_ID);
            
            while ($row = imw_fetch_assoc($res)) {
                $temp=$temp1=$temp4=array();
                $refusal_proc = ($row['refusal'] && $row['refusal_snomed'] ) ? true : false;
                $row['procedure_name']=$row['title'];
                $pt_proc_data=$this->filterProblemDx($row['title']);
                if($pt_proc_data['title'] && $pt_proc_data['title']!='')$row['procedure_name']=$pt_proc_data['title'];
                $cptCode=$pt_proc_data['cptCode'][0];
                $ccda_code=trim($row['ccda_code']);
                if(isset($ecqm_v8_data['SNOMEDCT'][$ccda_code])) {
                    $temp1['Value_Set_OID']=$ecqm_v8_data['SNOMEDCT'][$ccda_code]['Value_Set_OID'];
                    $temp1['Code_System']=$ecqm_v8_data['SNOMEDCT'][$ccda_code]['Code_System'];
                    $temp1['Code']=$ecqm_v8_data['SNOMEDCT'][$ccda_code]['Code'];
                    $temp1['Description']=$ecqm_v8_data['SNOMEDCT'][$ccda_code]['Description'];
                    $temp1['Code_System_OID']=$ecqm_v8_data['SNOMEDCT'][$ccda_code]['Code_System_OID'];
                    $temp1['CMS_ID']=$ecqm_v8_data['SNOMEDCT'][$ccda_code]['CMS_ID'];
                    $temp1['Value_Set_Name']=$ecqm_v8_data['SNOMEDCT'][$ccda_code]['Value_Set_Name'];
                }
                $row['proc_snomed']=$temp1;
                if(isset($ecqm_v8_data['CPT'][$cptCode])) {
                    $temp['Value_Set_OID']=$ecqm_v8_data['CPT'][$cptCode]['Value_Set_OID'];
                    $temp['Code_System']=$ecqm_v8_data['CPT'][$cptCode]['Code_System'];
                    $temp['Code']=$ecqm_v8_data['CPT'][$cptCode]['Code'];
                    $temp['Description']=$ecqm_v8_data['CPT'][$cptCode]['Description'];
                    $temp['Code_System_OID']=$ecqm_v8_data['CPT'][$cptCode]['Code_System_OID'];
                    $temp['CMS_ID']=$ecqm_v8_data['CPT'][$cptCode]['CMS_ID'];
                    $temp['Value_Set_Name']=$ecqm_v8_data['CPT'][$cptCode]['Value_Set_Name'];
                }
                $row['cpt_code']=$temp;
                
                $row['refusal_proc']=$refusal_proc;
                if($refusal_proc) {
                    $refusal_snomed=$row['refusal_snomed'];
                    if(isset($ecqm_v8_data['SNOMEDCT'][$refusal_snomed])) {
                        $temp4['Value_Set_OID']=$ecqm_v8_data['SNOMEDCT'][$refusal_snomed]['Value_Set_OID'];
                        $temp4['Code_System']=$ecqm_v8_data['SNOMEDCT'][$refusal_snomed]['Code_System'];
                        $temp4['Code']=$ecqm_v8_data['SNOMEDCT'][$refusal_snomed]['Code'];
                        $temp4['Description']=$ecqm_v8_data['SNOMEDCT'][$refusal_snomed]['Description'];
                        $temp4['Code_System_OID']=$ecqm_v8_data['SNOMEDCT'][$refusal_snomed]['Code_System_OID'];
                        $temp4['CMS_ID']=$ecqm_v8_data['SNOMEDCT'][$refusal_snomed]['CMS_ID'];
                        $temp4['Value_Set_Name']=$ecqm_v8_data['SNOMEDCT'][$refusal_snomed]['Value_Set_Name'];
                    }
                }
                $row['proc_refusal']=$temp4;
                
                $pt_procedure_data[]=$row;
            }
        }
        return $pt_procedure_data;
        
    }
    //-------END Procedure SECTION --------------//
    
    
    //-------BEGIN Interventions SECTION --------------//
    public function pt_interventions_data() {
        $pt_interventions_data=array();
        $qry = "SELECT * FROM lists WHERE pid = '".$this->patient_id."' and type = '5' and allergy_status='Active' and proc_type='intervention'";
        $res = imw_query($qry);
        if ($res && imw_num_rows($res) > 0) {
            $ecqm_v8_data=$this->ecqm_v8_data($this->CMS_ID);

            while ($row = imw_fetch_assoc($res)) {
                $temp=$temp1=$temp4=array();
                $refusal_inter = ($row['refusal'] && $row['refusal_snomed'] ) ? true : false;
                $row['intervention_name']=$row['title'];
                $pt_inter_data=$this->filterProblemDx($row['title']);
                if($pt_inter_data['title'] && $pt_inter_data['title']!='')$row['intervention_name']=$pt_inter_data['title'];
                $cpt_code=$pt_inter_data['cptCode'][0];
                $ccda_code=trim($row['ccda_code']);
                if(isset($ecqm_v8_data['SNOMEDCT'][$ccda_code])) {
                    $temp1['Value_Set_OID']=$ecqm_v8_data['SNOMEDCT'][$ccda_code]['Value_Set_OID'];
                    $temp1['Code_System']=$ecqm_v8_data['SNOMEDCT'][$ccda_code]['Code_System'];
                    $temp1['Code']=$ecqm_v8_data['SNOMEDCT'][$ccda_code]['Code'];
                    $temp1['Description']=$ecqm_v8_data['SNOMEDCT'][$ccda_code]['Description'];
                    $temp1['Code_System_OID']=$ecqm_v8_data['SNOMEDCT'][$ccda_code]['Code_System_OID'];
                    $temp1['CMS_ID']=$ecqm_v8_data['SNOMEDCT'][$ccda_code]['CMS_ID'];
                    $temp1['Value_Set_Name']=$ecqm_v8_data['SNOMEDCT'][$ccda_code]['Value_Set_Name'];
                }
                $row['inter_snomed']=$temp1;
                if(isset($ecqm_v8_data['CPT'][$cpt_code])) {
                    $temp['Value_Set_OID']=$ecqm_v8_data['CPT'][$cpt_code]['Value_Set_OID'];
                    $temp['Code_System']=$ecqm_v8_data['CPT'][$cpt_code]['Code_System'];
                    $temp['Code']=$ecqm_v8_data['CPT'][$cpt_code]['Code'];
                    $temp['Description']=$ecqm_v8_data['CPT'][$cpt_code]['Description'];
                    $temp['Code_System_OID']=$ecqm_v8_data['CPT'][$cpt_code]['Code_System_OID'];
                    $temp['CMS_ID']=$ecqm_v8_data['CPT'][$cpt_code]['CMS_ID'];
                    $temp['Value_Set_Name']=$ecqm_v8_data['CPT'][$cpt_code]['Value_Set_Name'];
                }
                $row['cpt_code']=$temp;

                $row['refusal_inter']=$refusal_inter;
                if($refusal_inter) {
                    $refusal_snomed=$row['refusal_snomed'];
                    if(isset($ecqm_v8_data['SNOMEDCT'][$refusal_snomed])) {
                        $temp4['Value_Set_OID']=$ecqm_v8_data['SNOMEDCT'][$refusal_snomed]['Value_Set_OID'];
                        $temp4['Code_System']=$ecqm_v8_data['SNOMEDCT'][$refusal_snomed]['Code_System'];
                        $temp4['Code']=$ecqm_v8_data['SNOMEDCT'][$refusal_snomed]['Code'];
                        $temp4['Description']=$ecqm_v8_data['SNOMEDCT'][$refusal_snomed]['Description'];
                        $temp4['Code_System_OID']=$ecqm_v8_data['SNOMEDCT'][$refusal_snomed]['Code_System_OID'];
                        $temp4['CMS_ID']=$ecqm_v8_data['SNOMEDCT'][$refusal_snomed]['CMS_ID'];
                        $temp4['Value_Set_Name']=$ecqm_v8_data['SNOMEDCT'][$refusal_snomed]['Value_Set_Name'];
                    }
                }
                $row['inter_refusal']=$temp4;
                
                $pt_interventions_data[]=$row;
            }
        }
        return $pt_interventions_data;
    }
    //-------END Interventions SECTION --------------//
    
    //-------BEGIN Surgery SECTION --------------//
    public function pt_surgery_data() {
        $pt_surgery_data=array();
        $qry = "SELECT * FROM lists WHERE pid = '".$this->patient_id."' and type = '9' and (implant_status='order' or implant_status='applied') ";
        $res = imw_query($qry);
        if ($res && imw_num_rows($res) > 0) {
            $ecqm_v8_data=$this->ecqm_v8_data($this->CMS_ID);

            while ($row = imw_fetch_assoc($res)) {
                $ccda_code=trim($row['ccda_code']);
                $comments_arr = explode('-(',$row['comments']);
                $hcpcs_code = substr($comments_arr[1], 0, -1);
                $hcpcs_code=trim($hcpcs_code);
                $temp1=$temp2=array();
                if(isset($ecqm_v8_data['HCPCS'][$hcpcs_code])) {
                    $temp1['Value_Set_OID']=$ecqm_v8_data['HCPCS'][$hcpcs_code]['Value_Set_OID'];
                    $temp1['Code_System']=$ecqm_v8_data['HCPCS'][$hcpcs_code]['Code_System'];
                    $temp1['Code']=$ecqm_v8_data['HCPCS'][$hcpcs_code]['Code'];
                    $temp1['Description']=$ecqm_v8_data['HCPCS'][$hcpcs_code]['Description'];
                    $temp1['Code_System_OID']=$ecqm_v8_data['HCPCS'][$hcpcs_code]['Code_System_OID'];
                    $temp1['CMS_ID']=$ecqm_v8_data['HCPCS'][$hcpcs_code]['CMS_ID'];
                    $temp1['Value_Set_Name']=$ecqm_v8_data['HCPCS'][$hcpcs_code]['Value_Set_Name'];
                }
                $row['implant_hcpcs']=$temp1;

                $pt_surgery_data[]=$row;
            }
        }
        
        return $pt_surgery_data;
    }
    //-------END Surgery SECTION --------------//
    
    
    /* BEGIN Diagnostic Study, Result SECTION cup to disc or optic disc*/
    public function pt_optic_diagnostic_data() {
        $pt_optic_diagnostic_data=array();
        $qry = "SELECT * FROM rad_test_data WHERE rad_patient_id = '".$this->patient_id."' and rad_status = 2
            AND (LOWER(rad_results) like '%cup to disc%' OR LOWER(rad_name) like '%cup to disc%' OR LOWER(rad_results) like '%optic disc%' OR LOWER(rad_name) like '%optic disc%' 
            OR LOWER(rad_name) like '%cup%' OR LOWER(rad_name) like '%optic%' OR LOWER(rad_name) like '%disc%') ";
        $res = imw_query($qry);
        if ($res && imw_num_rows($res) > 0) {
            $ecqm_v8_data=$this->ecqm_v8_data($this->CMS_ID);
            while ($row = imw_fetch_assoc($res)) {
                $refusal_optic = ($row['refusal'] && $row['refusal_snomed'] ) ? true : false;

                $rad_loinc=trim($row['rad_loinc']);
                if(isset($ecqm_v8_data['LOINC'][$rad_loinc])) {
                    $row['Value_Set_OID']=$ecqm_v8_data['LOINC'][$rad_loinc]['Value_Set_OID'];
                    $row['Code_System']=$ecqm_v8_data['LOINC'][$rad_loinc]['Code_System'];
                    $row['Code']=$ecqm_v8_data['LOINC'][$rad_loinc]['Code'];
                    $row['Description']=$ecqm_v8_data['LOINC'][$rad_loinc]['Description'];
                    $row['Code_System_OID']=$ecqm_v8_data['LOINC'][$rad_loinc]['Code_System_OID'];
                    $row['CMS_ID']=$ecqm_v8_data['LOINC'][$rad_loinc]['CMS_ID'];
                    $row['Value_Set_Name']=$ecqm_v8_data['LOINC'][$rad_loinc]['Value_Set_Name'];
                }

                $temp4=array();
                if($refusal_optic) {
                    $refusal_snomed=$row['refusal_snomed'];
                    if(isset($ecqm_v8_data['SNOMEDCT'][$refusal_snomed])) {
                        $temp4['Value_Set_OID']=$ecqm_v8_data['SNOMEDCT'][$refusal_snomed]['Value_Set_OID'];
                        $temp4['Code_System']=$ecqm_v8_data['SNOMEDCT'][$refusal_snomed]['Code_System'];
                        $temp4['Code']=$ecqm_v8_data['SNOMEDCT'][$refusal_snomed]['Code'];
                        $temp4['Description']=$ecqm_v8_data['SNOMEDCT'][$refusal_snomed]['Description'];
                        $temp4['Code_System_OID']=$ecqm_v8_data['SNOMEDCT'][$refusal_snomed]['Code_System_OID'];
                        $temp4['CMS_ID']=$ecqm_v8_data['SNOMEDCT'][$refusal_snomed]['CMS_ID'];
                        $temp4['Value_Set_Name']=$ecqm_v8_data['SNOMEDCT'][$refusal_snomed]['Value_Set_Name'];
                    }
                }
                $row['optic_refusal']=$temp4;
                
                $pt_optic_diagnostic_data[]=$row;
            }
        } 
        
        return $pt_optic_diagnostic_data;
    } 
    /* END Diagnostic Study SECTION */
    

    //-------BEGIN DIAGNOSTICS RAD TESTS SECTION MACULAR--------------//
    public function pt_macular_diagnostic_data() {
        $pt_macular_diagnostic_data=array();
        $qry = "SELECT * FROM rad_test_data WHERE rad_patient_id = '".$this->patient_id."' and rad_status = 2";
        $res = imw_query($qry);
        $tmpExtArr = array();
        if ($res && imw_num_rows($res) > 0) {
            $ecqm_v8_data=$this->ecqm_v8_data($this->CMS_ID);
            
            while ($row = imw_fetch_assoc($res)) {
                $refusal_macular = ($row['refusal'] && $row['refusal_snomed'] ) ? true : false;
                
                $rad_loinc=trim($row['rad_loinc']);
                if(isset($ecqm_v8_data['LOINC'][$rad_loinc])) {
                    $temp3=array();
                    $temp3['Value_Set_OID']=$ecqm_v8_data['LOINC'][$rad_loinc]['Value_Set_OID'];
                    $temp3['Code_System']=$ecqm_v8_data['LOINC'][$rad_loinc]['Code_System'];
                    $temp3['Code']=$ecqm_v8_data['LOINC'][$rad_loinc]['Code'];
                    $temp3['Description']=$ecqm_v8_data['LOINC'][$rad_loinc]['Description'];
                    $temp3['Code_System_OID']=$ecqm_v8_data['LOINC'][$rad_loinc]['Code_System_OID'];
                    $temp3['CMS_ID']=$ecqm_v8_data['LOINC'][$rad_loinc]['CMS_ID'];
                    $temp3['Value_Set_Name']=$ecqm_v8_data['LOINC'][$rad_loinc]['Value_Set_Name'];
                    
                    $row['macular_loinc']=$temp3;
                }
                
                $snowmedCode=trim($row['snowmedCode']);
                if(isset($ecqm_v8_data['SNOMEDCT'][$snowmedCode])) {
                    $temp4=array();
                    $temp4['Value_Set_OID']=$ecqm_v8_data['SNOMEDCT'][$snowmedCode]['Value_Set_OID'];
                    $temp4['Code_System']=$ecqm_v8_data['SNOMEDCT'][$snowmedCode]['Code_System'];
                    $temp4['Code']=$ecqm_v8_data['SNOMEDCT'][$snowmedCode]['Code'];
                    $temp4['Description']=$ecqm_v8_data['SNOMEDCT'][$snowmedCode]['Description'];
                    $temp4['Code_System_OID']=$ecqm_v8_data['SNOMEDCT'][$snowmedCode]['Code_System_OID'];
                    $temp4['CMS_ID']=$ecqm_v8_data['SNOMEDCT'][$snowmedCode]['CMS_ID'];
                    $temp4['Value_Set_Name']=$ecqm_v8_data['SNOMEDCT'][$snowmedCode]['Value_Set_Name'];
                    
                    $row['macular_snomed']=$temp4;
                }
                
                $row['refusal_macular']=$refusal_macular;
                $row['macular_refusal']=array();
                if($refusal_macular) {
                    $refusal_snomed=$row['refusal_snomed'];
                    if(isset($ecqm_v8_data['SNOMEDCT'][$refusal_snomed])) {
                        $temp5=array();
                        $temp5['Value_Set_OID']=$ecqm_v8_data['SNOMEDCT'][$refusal_snomed]['Value_Set_OID'];
                        $temp5['Code_System']=$ecqm_v8_data['SNOMEDCT'][$refusal_snomed]['Code_System'];
                        $temp5['Code']=$ecqm_v8_data['SNOMEDCT'][$refusal_snomed]['Code'];
                        $temp5['Description']=$ecqm_v8_data['SNOMEDCT'][$refusal_snomed]['Description'];
                        $temp5['Code_System_OID']=$ecqm_v8_data['SNOMEDCT'][$refusal_snomed]['Code_System_OID'];
                        $temp5['CMS_ID']=$ecqm_v8_data['SNOMEDCT'][$refusal_snomed]['CMS_ID'];
                        $temp5['Value_Set_Name']=$ecqm_v8_data['SNOMEDCT'][$refusal_snomed]['Value_Set_Name'];
                        
                        $row['macular_refusal']=$temp5;
                    }
                }
                $pt_macular_diagnostic_data[]=$row;
                
            }
        }
        
        return $pt_macular_diagnostic_data;
    }
    //-------END Diagnostic Study RAD TESTS SECTION --------------//
    
    
    /* BEGIN PHYSICAL EXAMS */
    public function pt_physical_exam() {
        $ecqm_v8_data=$this->ecqm_v8_data($this->CMS_ID);

        $pt_physical_exam=array();
        //Vital Sign
        $sql_vital = "SELECT vsp.*,vsl.vital_sign,vsm.date_vital,vsm.phy_reviewed_date,vsm.phy_reviewed FROM vital_sign_master vsm 
                        JOIN vital_sign_patient vsp ON vsm.id = vsp.vital_master_id 
                        JOIN  vital_sign_limits vsl ON vsl.id = vsp.vital_sign_id 
                        WHERE vsm.patient_id = '".$this->patient_id."' AND  vsm.status = 0 and vsp.range_vital!='' ORDER BY vsp.id ASC";
        $result_vital = imw_query($sql_vital);
        if ($result_vital && imw_num_rows($result_vital)>0) {
            while ($row_vital = imw_fetch_assoc($result_vital)) {
                $arr_vs_result_type = $this->vs_result_type_srh($row_vital['vital_sign']);
                $code_list = "";
                $vs_valueSet = "";
                $vs_code_system = "";
                $Value_Set_Name = "";
                if ($row_vital['vital_sign'] == "B/P - Systolic") {
                    $code_list = 'Physical Exam, Performaed: Systolic Blood Pressure (LOINC Code 8480-6)';
                    $vs_valueSet = "2.16.840.1.113883.3.526.3.1032";
                    $vs_code_system = "2.16.840.1.113883.6.1";
                    $Value_Set_Name= "Systolic Blood Pressure";
                }
                if ($row_vital['vital_sign'] == "B/P - Diastolic") {
                    $code_list = 'Physical Exam, Performaed: Diastolic Blood Pressure (LOINC Code 8462-4)';
                    $vs_valueSet = "2.16.840.1.113883.3.526.3.1033";
                    $vs_code_system = "2.16.840.1.113883.6.1";
                    $Value_Set_Name= "Diastolic Blood Pressure";
                }
                if ($row_vital['vital_sign'] == "BMI") {
                    $code_list = 'Physical Exam, Performaed: BMI LOINC Value (LOINC Code 39156-5)';
                    $vs_valueSet = "2.16.840.1.113883.3.600.1.681";
                    $vs_code_system = "2.16.840.1.113883.6.1";
                    $Value_Set_Name= "BMI";
                }

                $row_vital['Value_Set_OID']=$vs_valueSet;
                $row_vital['Code_System']='LOINC';
                $row_vital['Code']=$arr_vs_result_type['code'];
                $row_vital['Description']=$code_list;
                $row_vital['Code_System_OID']=$vs_code_system;
                $row_vital['CMS_ID']=$this->CMS_ID;
                $row_vital['Value_Set_Name']=$Value_Set_Name;

                $pt_physical_exam['VS'][]=$row_vital;
            }
        }

        //Health Observations.
        $sql = "SELECT *, hc_observations.snomed_code as snomed_code, hc_rel_observations.snomed_code AS scode FROM  hc_observations
                LEFT JOIN hc_concerns ON hc_concerns.observation_id = hc_observations.id  
                LEFT JOIN hc_rel_observations ON hc_rel_observations.observation_id = hc_observations.id 
                WHERE pt_id = '".$this->patient_id."' AND type=0 AND hc_observations.del_status=0";
        $result_hc = imw_query($sql);
        if ($result_hc && imw_num_rows($result_hc)>0) {
            while ($row_hc = imw_fetch_assoc($result_hc)) {
                $snomedCode=$row_hc['snomed_code'];
                $scode=$row_hc['scode'];
                if(isset($ecqm_v8_data['SNOMEDCT'][$snomedCode]) || $snomedCode=='419775003') {
                    $row_hc['Value_Set_OID']=$ecqm_v8_data['SNOMEDCT'][$snomedCode]['Value_Set_OID'];
                    $row_hc['Code_System']=$ecqm_v8_data['SNOMEDCT'][$snomedCode]['Code_System'];
                    $row_hc['Code']=$ecqm_v8_data['SNOMEDCT'][$snomedCode]['Code'];
                    if($snomedCode=='419775003')$row_hc['Code']=$snomedCode;
                    $row_hc['Description']=$ecqm_v8_data['SNOMEDCT'][$snomedCode]['Description'];
                    $row_hc['Code_System_OID']=$ecqm_v8_data['SNOMEDCT'][$snomedCode]['Code_System_OID'];
                    $row_hc['CMS_ID']=$ecqm_v8_data['SNOMEDCT'][$snomedCode]['CMS_ID'];
                    $row_hc['Value_Set_Name']=$ecqm_v8_data['SNOMEDCT'][$snomedCode]['Value_Set_Name'];

                    $tempArr=array();
                    if(isset($ecqm_v8_data['SNOMEDCT'][$scode])) {
                        $tempArr['Value_Set_OID']=$ecqm_v8_data['SNOMEDCT'][$scode]['Value_Set_OID'];
                        $tempArr['Code_System']=$ecqm_v8_data['SNOMEDCT'][$scode]['Code_System'];
                        $tempArr['Code']=$ecqm_v8_data['SNOMEDCT'][$scode]['Code'];
                        $tempArr['Description']=$ecqm_v8_data['SNOMEDCT'][$scode]['Description'];
                        $tempArr['Code_System_OID']=$ecqm_v8_data['SNOMEDCT'][$scode]['Code_System_OID'];
                        $tempArr['CMS_ID']=$ecqm_v8_data['SNOMEDCT'][$scode]['CMS_ID'];
                        $tempArr['Value_Set_Name']=$ecqm_v8_data['SNOMEDCT'][$scode]['Value_Set_Name'];
                    }
                    $row_hc['scode']=$tempArr;
                    $pt_physical_exam['HealthObs'][]=$row_hc;
                }

            }
        }

        return $pt_physical_exam;
        
    }
    /* END PHYSICAL EXAMS */
    
    
    public function vs_result_type_srh($val) {
        $val = trim($val);
        $arrVSType = array(
            array("imw" => 'Respiration', "code" => "9279-1", "display_name" => "Respiratory Rate"),
            array("imw" => 'O2Sat', "code" => "2710-2", "display_name" => "O2 % BldC Oximetry"),
            array("imw" => 'B/P - Systolic', "code" => "8480-6", "display_name" => "BP Systolic"),
            array("imw" => 'B/P - Diastolic', "code" => "8462-4", "display_name" => "BP Diastolic"),
            array("imw" => 'Temperature', "code" => "8310-5", "display_name" => "Body Temperature"),
            array("imw" => 'Height', "code" => "8302-2", "display_name" => "Height"),
            array("imw" => 'Weight', "code" => "3141-9", "display_name" => "Weight Measured"),
            array("imw" => 'BMI', "code" => "39156-5", "display_name" => "BMI (Body Mass Index)")
        );
        $arr = array();
        if ($val != "") {
            foreach ($arrVSType as $row) {
                if (in_array($val, $row)) {
                    $arr['code'] = $row['code'];
                    $arr['display_name'] = $row['display_name'];
                    break;
                }
            }
        }
        return $arr;
    }
    
    
    
    
    
    
    
    /* BEGIN Diagnostic Study, Result SECTION */
    public function get_optic_diagnostic_data_xml($pt_optic_diagnostic_data=array()) {
        $XML_test_entry="";
        $ext_counter=200;
        foreach($pt_optic_diagnostic_data as $row) {
            $ext_counter++;
            $refusal = ($row['refusal'] && $row['refusal_snomed']) ? 'true' : 'false';

            $XML_test_entry .= '<entry>
                <!-- Diagnostic Study, Rad cup disc optic disc -->
                <observation classCode="OBS" moodCode="EVN" ' . ($refusal ? 'negationInd="' . $refusal . '"' : '' ) . ' >
                <!-- Consolidated Procedure Activity Observation templateId 
                (Implied Template) -->
                <templateId root="2.16.840.1.113883.10.20.22.4.13" extension="2014-06-09"/>
                <!-- Diagnostic Study, Performed template -->
                <templateId root="2.16.840.1.113883.10.20.24.3.18" extension="2017-08-01"/>
                <id root="1.3.6.1.4.1.115" extension="5d3ee4f3dfe4bd0398b29' . $ext_counter . '"/>';
            $valueSet="2.16.840.1.113883.3.526.3.1334";
            if(strpos(strtolower($row['rad_name']), 'cup to disc') !== false || strpos(strtolower($row['rad_name']), 'cup') !== false)$valueSet="2.16.840.1.113883.3.526.3.1333";
            if(strpos(strtolower($row['rad_name']), 'optic disc exam') !== false || strpos(strtolower($row['rad_name']), 'optic') !== false)$valueSet="2.16.840.1.113883.3.526.3.1334";
            if ($refusal == 'true') {
                $XML_test_entry .= '<code nullFlavor="NA" sdtc:valueSet="'.$valueSet.'" />
                                <text> '.$row['rad_name'].'</text>';
            } else {
                if(isset($row['Value_Set_OID']) && $row['Value_Set_OID']!='') {
                    $Value_Set_OID=$row['Value_Set_OID'];
                    $Code_System=$row['Code_System'];
                    $Code=$row['Code'];
                    $Description=$row['Description'];
                    $Code_System_OID=$row['Code_System_OID'];
                    $CMS_ID=$row['CMS_ID'];

                    $XML_test_entry .= '<code code="'.$Code.'" codeSystem="'.$Code_System_OID.'" codeSystemName="'.$Code_System.'" />
                                    <text> '.$row['rad_name'].' </text>';

                }
            }
            $low_value=str_replace('-', '', $row['rad_order_date']) . str_replace(':', '', $row['rad_order_time']);
            $high_value=str_replace('-', '', $row['rad_results_date']) . str_replace(':', '', $row['rad_results_time']);
            $XML_test_entry .= '<statusCode code="completed"/>
                                <effectiveTime>
                                <low value="' .$low_value. '"/>';
                            if($high_value=='00000000000000') {
                                $XML_test_entry .='<high nullFlavor="UNK"/>';
                            }else {
                                $XML_test_entry .='<high value="' .$high_value. '"/>';
                            }
            $XML_test_entry .= '</effectiveTime>';
            if ($refusal == 'true' || empty($row['rad_results']) ) {
                $XML_test_entry .= '<value nullFlavor="NA" xsi:type="CD" />';
            } else {
                //alphabetic text
                if (ctype_alpha($row['rad_results']) !== false) {
                    $XML_test_entry .= '<value xsi:type="ST" >' . $row['rad_results'] . '</value>';
                } else {
                    $rad_result = explode(';', $row['rad_results']);
                    $unit = explode(':', $rad_result[1]);
                    $XML_test_entry .= '<value xsi:type="PQ" value="'.$rad_result[0].'" unit="'.$unit[1].'"/>';
                }
            }
            
            $XML_test_entry .= '<!-- QDM Attribute: Author dateTime -->
                            <author>
                                <templateId root="2.16.840.1.113883.10.20.24.3.155" extension="2017-08-01"/>
                                <time value="'.$low_value.'"/>
                                <assignedAuthor>
                                    <id nullFlavor="NA"/>
                                </assignedAuthor>
                            </author>';
            
            if ($refusal == 'true') {
                if(isset($row['optic_refusal']) && empty($row['optic_refusal'])==false) {
                    $Value_Set_OID=$row['optic_refusal']['Value_Set_OID'];
                    $Code_System=$row['optic_refusal']['Code_System'];
                    $Code=$row['optic_refusal']['Code'];
                    $Description=$row['optic_refusal']['Description'];
                    $Code_System_OID=$row['optic_refusal']['Code_System_OID'];
                    $CMS_ID=$row['optic_refusal']['CMS_ID'];
                    $XML_test_entry .= '<!-- QDM Attribute: Negation Rationale -->
                            <entryRelationship typeCode="RSON">
                                <observation classCode="OBS" moodCode="EVN">
                                    <templateId root="2.16.840.1.113883.10.20.24.3.88" extension="2017-08-01"/>
                                    <id root="1.3.6.1.4.1.115" extension="1aca'.$ext_counter.'0-cd54-0137-82a0-0eca209bc306" />
                                    <code code="77301-0" codeSystem="2.16.840.1.113883.6.1" displayName="reason" codeSystemName="LOINC"/>
                                    <statusCode code="completed"/>
                                    <effectiveTime>
                                        <low value="' .$low_value. '"/>';
                                        if($high_value=='00000000000000') {
                                            $XML_test_entry .='<high nullFlavor="UNK"/>';
                                        }else {
                                            $XML_test_entry .='<high value="' .$high_value. '"/>';
                                        }
                            $XML_test_entry .= '</effectiveTime>
                                    <value code="'.$Code.'" codeSystem="'.$Code_System_OID.'" codeSystemName="'.$Code_System.'" xsi:type="CD"/>
                                </observation>
                            </entryRelationship>';
                }
            } else if(empty($row['rad_results']) && $refusal == 'false' && strpos(strtolower($row['rad_name']), 'cup to disc') !== false || strpos(strtolower($row['rad_name']), 'cup') !== false) {
                $XML_test_entry .= '<!-- QDM Attribute: Result -->
                            <entryRelationship typeCode="REFR">
                                <observation classCode="OBS" moodCode="EVN">
                                    <!-- Conforms to C-CDA R2 Result Observation (V2) -->
                                    <templateId root="2.16.840.1.113883.10.20.22.4.2" extension="2015-08-01"/>
                                    <id root="1.3.6.1.4.1.115" extension="92ade5b0-d2d2-0137-b737-0eca209bc306"/>
                                    <code code="71485-7" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC"/>
                                    <statusCode code="completed"/>
                                    <effectiveTime value="'.$low_value.'"/>
                                    <value xsi:type="PQ" value="0.8" unit="1"/>
                                </observation>
                            </entryRelationship>';
            
            } else if(empty($row['rad_results']) && $refusal == 'false' && strpos(strtolower($row['rad_name']), 'optic disc exam') !== false || strpos(strtolower($row['rad_name']), 'optic') !== false) {
                $XML_test_entry .= '<!-- QDM Attribute: Result -->
                            <entryRelationship typeCode="REFR">
                                <observation classCode="OBS" moodCode="EVN">
                                    <!-- Conforms to C-CDA R2 Result Observation (V2) -->
                                    <templateId root="2.16.840.1.113883.10.20.22.4.2" extension="2015-08-01"/>
                                    <id root="1.3.6.1.4.1.115" extension="92adbd10-d2d2-0137-b737-0eca209bc306"/>
                                    <code code="71487-3" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC"/>
                                    <statusCode code="completed"/>
                                    <effectiveTime value="'.$low_value.'"/>
                                    <value xsi:type="CD" code="71486-5" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC"/>
                                </observation>
                            </entryRelationship>';
            }
            $XML_test_entry .= '</observation> </entry>';
        }
        
        return $XML_test_entry;
    }

    
    public function get_macular_diagnostic_data_xml($pt_macular_diagnostic_data=array()) {
        $tmpExtArr = array();
        $XML_test_entry='';
        $ext_counter=300;
        foreach ($pt_macular_diagnostic_data as $row) {
            $ext_counter++;
            $id_extention = '5a1e6cc4cde4a364e87b0' . $ext_counter;
            $refusal = ($row['refusal'] && $row['refusal_snomed']) ? 'true' : '';

            // Use same extention if values exists in array 
            // based on Patient id, Radiology Test Name, Order Date, Result Date and LOINC Code
            if( $tmpExtArr[$pid][$row['rad_name']][$row['rad_order_date']][$row['rad_results_date']][$row['rad_loinc']] ){
                $id_extention = $tmpExtArr[$pid][$row['rad_name']][$row['rad_order_date']][$row['rad_results_date']][$row['rad_loinc']];
            }else {
                $tmpExtArr[$pid][$row['rad_name']][$row['rad_order_date']][$row['rad_results_date']][$row['rad_loinc']] = $id_extention;
            }

            $XML_test_entry .= '<entry>
                <!-- Diagnostic Study, Rad section -->
                <observation classCode="OBS" moodCode="EVN" ' . ($refusal ? 'negationInd="' . $refusal . '"' : '' ) . ' >
                <!-- Consolidated Procedure Activity Observation templateId
                (Implied Template) -->
            <templateId root="2.16.840.1.113883.10.20.22.4.13" extension="2014-06-09"/>
                <!-- Diagnostic Study, Performed template -->
                <templateId root="2.16.840.1.113883.10.20.24.3.18" extension="2017-08-01"/>
            <id root="1.3.6.1.4.1.115" extension="'.$id_extention. '"/>';



            if ($refusal == 'true') {
                $XML_test_entry .= '<code nullFlavor="NA" sdtc:valueSet="2.16.840.1.113883.3.526.3.1251" />
                                    <text>Diagnostic Study, Performed: Macular Exam</text>';
            } else {
                if(isset($row['macular_loinc']) && empty($row['macular_loinc'])==false) {
                    $Value_Set_OID=$row['macular_loinc']['Value_Set_OID'];
                    $Code_System=$row['macular_loinc']['Code_System'];
                    $Code=$row['macular_loinc']['Code'];
                    $Description=$row['macular_loinc']['Description'];
                    $Code_System_OID=$row['macular_loinc']['Code_System_OID'];
                    $CMS_ID=$row['macular_loinc']['CMS_ID'];
                    $Value_Set_Name=$row['macular_loinc']['Value_Set_Name'];
                    
                    $XML_test_entry .= '<code code="'.$Code.'" codeSystem="'.$Code_System_OID.'" codeSystemName="'.$Code_System.'" />';
                    $XML_test_entry .= '<text>'.$Value_Set_Name.'</text>';
                }
            }

            $low_value=str_replace('-', '', $row['rad_order_date']) . str_replace(':', '', $row['rad_order_time']);
            $high_value=str_replace('-', '', $row['rad_results_date']) . str_replace(':', '', $row['rad_results_time']);
            $XML_test_entry .= '<statusCode code="completed"/>
                                <effectiveTime>
                                <low value="' .$low_value. '"/>';
                            if($high_value=='00000000000000') {
                                $XML_test_entry .='<high nullFlavor="UNK"/>';
                            }else {
                                $XML_test_entry .='<high value="' .$high_value. '"/>';
                            }
            $XML_test_entry .= '</effectiveTime>';
            
            
            $XML_test_entry .= '<value nullFlavor="NA" xsi:type="CD" />';
            
            // Results attribute started here
            if($row['snowmedCode'] && isset($row['macular_snomed']) && empty($row['macular_snomed'])==false) {
                $Value_Set_OID=$row['macular_snomed']['Value_Set_OID'];
                $Code_System=$row['macular_snomed']['Code_System'];
                $Code=$row['macular_snomed']['Code'];
                $Description=$row['macular_snomed']['Description'];
                $Code_System_OID=$row['macular_snomed']['Code_System_OID'];
                $CMS_ID=$row['macular_snomed']['CMS_ID'];
                $Value_Set_Name=$row['macular_snomed']['Value_Set_Name'];
                    
                $XML_test_entry .= '<!-- QDM Attribute: Result -->
                    <entryRelationship typeCode="REFR">
                        <observation classCode="OBS" moodCode="EVN">
                            <!-- Conforms to C-CDA R2 Result Observation (V2) -->
                            <templateId root="2.16.840.1.113883.10.20.22.4.2" extension="2015-08-01"/>
                            <id root="1.3.6.1.4.1.115" extension="6b5ddc50-e1b1-0137-c67a-0eca209bc'.$ext_counter.'"/>
                            <code code="32451-7" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC"/>
                            <statusCode code="completed"/>
                            <effectiveTime value="'.$low_value.'"/>
                            <value xsi:type="CD" code="'.$Code.'" codeSystem="'.$Code_System_OID.'" codeSystemName="'.$Code_System.'"/>
                        </observation>
                    </entryRelationship>';

                
            }
            

            if ($refusal == 'true') {
                if(isset($row['macular_refusal']) && empty($row['macular_refusal'])==false) {
                    $Value_Set_OID=$row['macular_refusal']['Value_Set_OID'];
                    $Code_System=$row['macular_refusal']['Code_System'];
                    $Code=$row['macular_refusal']['Code'];
                    $Description=$row['macular_refusal']['Description'];
                    $Code_System_OID=$row['macular_refusal']['Code_System_OID'];
                    $CMS_ID=$row['macular_refusal']['CMS_ID'];
                    $Value_Set_Name=$row['macular_refusal']['Value_Set_Name'];
                    
                    $XML_test_entry .= '<entryRelationship typeCode="RSON">
                        <observation classCode="OBS" moodCode="EVN">
                            <templateId root="2.16.840.1.113883.10.20.24.3.88" extension="2017-08-01"/>
                            <id root="1.3.6.1.4.1.115" extension="18269332A3DC7C2486E70FC1DC871149"/>
                            <code code="77301-0" codeSystem="2.16.840.1.113883.6.1" displayName="reason" codeSystemName="LOINC"/>';
                    $XML_test_entry .= '<statusCode code="completed"/>
                                <effectiveTime>
                                <low value="' .$low_value. '"/>';
                            if($high_value=='00000000000000') {
                                $XML_test_entry .='<high nullFlavor="UNK"/>';
                            }else {
                                $XML_test_entry .='<high value="' .$high_value. '"/>';
                            }
                    $XML_test_entry .= '</effectiveTime>
                            <value code="'.$Code.'" codeSystem="'.$Code_System_OID.'" codeSystemName="'.$Code_System.'" xsi:type="CD"/>
                        </observation>
                    </entryRelationship>';
                }
            }
            $XML_test_entry .= '</observation></entry>';
        }
        
        return $XML_test_entry;
    }
    //-------END Diagnostic Study RAD TESTS SECTION --------------//
    
    
    
    //-------BEGIN Interventions SECTION --------------//
    public function get_intervention_data_xml($pt_interventions_data=array(),$refer_to_id_hash='') {
        $XML_intervention_entry='';
        $ext_counter=400;
        foreach ($pt_interventions_data as $row) {
            $ext=($refer_to_id_hash!='')?$refer_to_id_hash:'5a23952dcde4a3001567c'.$ext_counter++;
            $template_id1 = '2.16.840.1.113883.10.20.22.4.12';
            $template_id2 = '2.16.840.1.113883.10.20.24.3.32';
            $moodCode = 'EVN';
            $status = 'completed';
            $inter_type = 'Performed';
            $start_date = date('YmdHis', strtotime($row['begdate'] . ' ' . $row['begtime']));
            $end_date = date('YmdHis', strtotime($row['begdate'] . ' ' . $row['begtime']));

            if ($row['procedure_status'] == 'pending') {
                $template_id1 = '2.16.840.1.113883.10.20.22.4.39';
                $template_id2 = '2.16.840.1.113883.10.20.24.3.31';
                $moodCode = 'RQO';
                $status = 'active';
                $inter_type = 'Order';
            }

            $refusal = ($row['refusal'] && $row['refusal_snomed'] ) ? 'true' : '';	

            if(empty($row['inter_snomed'])==false){
                $inter_snomed=$row['inter_snomed'];
                $snomed_Value_Set_OID=$inter_snomed['Value_Set_OID'];
                $snomed_Code_System=$inter_snomed['Code_System'];
                $snomed_Code=$inter_snomed['Code'];
                $snomed_Description=$inter_snomed['Description'];
                $snomed_Code_System_OID=$inter_snomed['Code_System_OID'];
                $snomed_CMS_ID=$inter_snomed['CMS_ID'];
                $snomed_Value_Set_Name=$inter_snomed['Value_Set_Name'];
            }
            if(empty($row['cpt_code'])==false){
                $cpt_code=$row['cpt_code'];
                $cpt_Value_Set_OID=$cpt_code['Value_Set_OID'];
                $cpt_Code_System=$cpt_code['Code_System'];
                $cpt_Code=$cpt_code['Code'];
                $cpt_Description=$cpt_code['Description'];
                $cpt_Code_System_OID=$cpt_code['Code_System_OID'];
                $cpt_CMS_ID=$cpt_code['CMS_ID'];
                $cpt_Value_Set_Name=$cpt_code['Value_Set_Name'];
            }

            $XML_intervention_entry .= '<entry>
            <act classCode="ACT" moodCode="' . $moodCode . '" '.($refusal ? 'negationInd="'.$refusal.'"' : '' ).' >
            <!-- Consolidation CDA: Procedure Activity Act template -->
            <templateId root="' . $template_id1 . '" extension="2014-06-09"/>
            <templateId root="' . $template_id2 . '" extension="2017-08-01"/>
            <id root="1.3.6.1.4.1.115" extension="'.$ext.'" />
            ';
            
            if(empty($row['inter_snomed'])==false && empty($row['cpt_code'])==false){
                $XML_intervention_entry .= '<code code="'.$snomed_Code.'" codeSystem="'.$snomed_Code_System_OID.'" codeSystemName="'.$snomed_Code_System.'">
                    <translation code="'.$cpt_Code.'" codeSystem="'.$cpt_Code_System_OID.'" codeSystemName="'.$cpt_Code_System.'"/>
                </code>';
            } else if(empty($row['inter_snomed'])==false && (empty($row['cpt_code']) || $refer_to_id_hash) ){
                $XML_intervention_entry .= '<code code="'.$snomed_Code.'" codeSystem="'.$snomed_Code_System_OID.'" codeSystemName="'.$snomed_Code_System.'" />';
            } else if(empty($row['inter_snomed']) && empty($row['cpt_code'])==false){
                $XML_intervention_entry .= '<code code="'.$cpt_Code.'" codeSystem="'.$cpt_Code_System_OID.'" codeSystemName="'.$cpt_Code_System.'" />';
            } else if($refusal) {
                $XML_intervention_entry .= '<code nullFlavor="NA" sdtc:valueSet="2.16.840.1.113883.3.526.3.509"/>';
            } else {
                $XML_intervention_entry .= '<code nullFlavor="NA" />';
            }
            
            $XML_intervention_entry .='<text>'.$row['intervention_name'].'</text>
            <statusCode code="' . $status . '"/>
            <effectiveTime>
                <low value="' . $start_date . '"/>
                <high value="' . $end_date . '"/>
            </effectiveTime>';
            if ($row['procedure_status'] == 'pending' || $refusal) {
                $XML_intervention_entry .='<author>
                    <templateId root="2.16.840.1.113883.10.20.24.3.155" extension="2017-08-01"/>
                    <time value="' . $start_date . '"/>
                <assignedAuthor><id nullFlavor="NI"/></assignedAuthor>
                </author>';
            }

            if( $refusal ) {
                if(empty($row['inter_refusal'])==false) {
                    $Value_Set_OID=$row['inter_refusal']['Value_Set_OID'];
                    $Code_System=$row['inter_refusal']['Code_System'];
                    $Code=$row['inter_refusal']['Code'];
                    $Description=$row['inter_refusal']['Description'];
                    $Code_System_OID=$row['inter_refusal']['Code_System_OID'];
                    $CMS_ID=$row['inter_refusal']['CMS_ID'];

                    $XML_intervention_entry .= '
                    <entryRelationship typeCode="RSON">
                        <observation classCode="OBS" moodCode="EVN">
                            <templateId root="2.16.840.1.113883.10.20.24.3.88" extension="2014-12-01"/>
                            <id root="1.3.6.1.4.1.115" extension="7F67DA54D559F9626AFC95BFD5606491"/>
                                <code code="77301-0" codeSystem="2.16.840.1.113883.6.1" displayName="reason" codeSystemName="LOINC"/>
                                <statusCode code="completed"/>
                                <effectiveTime>
                                    <low value="'.$start_date.'" />
                                </effectiveTime>
                                <value code="'.$Code.'" codeSystem="'.$Code_System_OID.'" codeSystemName="'.$Code_System.'" xsi:type="CD"/>
                        </observation>
                    </entryRelationship>';

                }
            }

            $XML_intervention_entry .= '</act></entry>';
        }

        return $XML_intervention_entry;
    }
    //-------END Interventions SECTION --------------//
    
    
    //-------BEGIN Procedure SECTION --------------//
    public function get_procedure_data_xml($pt_procedure_data=array()) {
        $XML_procedure_entry='';
        $temp_XML_procedure_entry='';
        $tmpExtArr = array();
        $ext_counter=500;
        foreach($pt_procedure_data as $row) {
            $ext_counter++;
            $proc_extention = '5a23958bcde4a3001848b' . $ext_counter;

            $refusal = ($row['refusal'] && $row['refusal_snomed']) ? 'true' : '';
            if( $row['translation_code'] ) {
                $tmpExtArr[$row['translation_code']] = $proc_extention;
            }

            if( $tmpExtArr[$row['ccda_code']]) {
                $proc_extention = $tmpExtArr[$row['ccda_code']];
            }
            
            if(empty($row['proc_snomed'])==false){
                $proc_snomed=$row['proc_snomed'];
                $snomed_Value_Set_OID=$proc_snomed['Value_Set_OID'];
                $snomed_Code_System=$proc_snomed['Code_System'];
                $snomed_Code=$proc_snomed['Code'];
                $snomed_Description=$proc_snomed['Description'];
                $snomed_Code_System_OID=$proc_snomed['Code_System_OID'];
                $snomed_CMS_ID=$proc_snomed['CMS_ID'];
                $snomed_Value_Set_Name=$proc_snomed['Value_Set_Name'];
            }
            if(empty($row['cpt_code'])==false){
                $cpt_code=$row['cpt_code'];
                $cpt_Value_Set_OID=$cpt_code['Value_Set_OID'];
                $cpt_Code_System=$cpt_code['Code_System'];
                $cpt_Code=$cpt_code['Code'];
                $cpt_Description=$cpt_code['Description'];
                $cpt_Code_System_OID=$cpt_code['Code_System_OID'];
                $cpt_CMS_ID=$cpt_code['CMS_ID'];
                $cpt_Value_Set_Name=$cpt_code['Value_Set_Name'];
            }
            
            $date = date('YmdHis',strtotime($row['begdate'].' '.$row['begtime']));
            
            $XML_procedure_entry .= '<entry>
            <procedure classCode="PROC" moodCode="EVN" '.($refusal ? 'negationInd="'.$refusal.'"' : '' ).' >
            <!--  Procedure performed template -->
            <templateId root="2.16.840.1.113883.10.20.24.3.64" extension="2018-10-01"/>
            <!-- Procedure Activity Procedure-->
            <templateId root="2.16.840.1.113883.10.20.22.4.14" extension="2014-06-09"/>
            <id root="1.3.6.1.4.1.115" extension="' . $proc_extention . '"/>';

            if(empty($row['proc_snomed'])==false && empty($row['cpt_code'])==false){
                $XML_procedure_entry .= '<code code="'.$snomed_Code.'" codeSystem="'.$snomed_Code_System_OID.'" codeSystemName="'.$snomed_Code_System.'">
                    <translation code="'.$cpt_Code.'" codeSystem="'.$cpt_Code_System_OID.'" codeSystemName="'.$cpt_Code_System.'"/>
                </code>';
            } else if(empty($row['proc_snomed'])==false && empty($row['cpt_code'])){
                $XML_procedure_entry .= '<code code="'.$snomed_Code.'" codeSystem="'.$snomed_Code_System_OID.'" codeSystemName="'.$snomed_Code_System.'"></code>';
            } else if(empty($row['proc_snomed']) && empty($row['cpt_code'])==false){
                $XML_procedure_entry .= '<code code="'.$cpt_Code.'" codeSystem="'.$cpt_Code_System_OID.'" codeSystemName="'.$cpt_Code_System.'"></code>';
            } else {
                $XML_procedure_entry .= '<code nullFlavor="NA" />';
            }
            $XML_procedure_entry .= '<text>'.$row['procedure_name'].' </text>
            <statusCode code="'.$row['procedure_status'].'"/>
            <effectiveTime>
                <low value="' . $date . '"/>
                <high value="' . $date . '"/>
            </effectiveTime>';

            $XML_procedure_entry .= '<author>';
                $XML_procedure_entry .= '<templateId root="2.16.840.1.113883.10.20.24.3.155" extension="2017-08-01"/>';
                $XML_procedure_entry .= '<time value="' . $date . '"/>';
                $XML_procedure_entry .= '<assignedAuthor>';
                    $XML_procedure_entry .= '<id nullFlavor="NA"/>';
                $XML_procedure_entry .= '</assignedAuthor>';
            $XML_procedure_entry .= '</author> ';

            if( $refusal  == 'true' ) {
                if(empty($row['proc_refusal'])==false) {
                    $Value_Set_OID=$row['proc_refusal']['Value_Set_OID'];
                    $Code_System=$row['proc_refusal']['Code_System'];
                    $Code=$row['proc_refusal']['Code'];
                    $Description=$row['proc_refusal']['Description'];
                    $Code_System_OID=$row['proc_refusal']['Code_System_OID'];
                    $CMS_ID=$row['proc_refusal']['CMS_ID'];
                    $Value_Set_Name=$row['proc_refusal']['Value_Set_Name'];

                    $XML_procedure_entry .= '<entryRelationship typeCode="RSON">
                        <observation classCode="OBS" moodCode="EVN">
                            <templateId root="2.16.840.1.113883.10.20.24.3.88" extension="2017-08-01"/>
                            <id root="1.3.6.1.4.1.115" extension="2e5f3120-e0e8-0137-c67b-0eca209bc306" />
                            <code code="77301-0" codeSystem="2.16.840.1.113883.6.1" displayName="reason" codeSystemName="LOINC"/>
                            <statusCode code="completed"/>
                            <effectiveTime>
                                <low value="'.$date.'"/>
                                <high nullFlavor="UNK"/>
                            </effectiveTime>
                            <value code="'.$Code.'" codeSystem="'.$Code_System_OID.'" codeSystemName="'.$Code_System.'" xsi:type="CD"/>
                        </observation>
                    </entryRelationship>';
                }
            }

            $XML_procedure_entry .= '</procedure></entry>';
            $temp_XML_procedure_entry.=$XML_procedure_entry;
        }

        return $temp_XML_procedure_entry;
    }
    //-------END Procedure SECTION --------------//


    /* BEGIN PROBLEM SECTION */
    public function get_problems_data_xml($problems_data=array()) {
        $XML_problem_section='';
        $ext_counter=600;
        foreach ($problems_data as $problemList) {
            if(empty($problemList['problem_snomed'])==false){
                $problem_snomed=$problemList['problem_snomed'];
                $snomed_Value_Set_OID=$problem_snomed['Value_Set_OID'];
                $snomed_Code_System=$problem_snomed['Code_System'];
                $snomed_Code=$problem_snomed['Code'];
                $snomed_Description=$problem_snomed['Description'];
                $snomed_Code_System_OID=$problem_snomed['Code_System_OID'];
                $snomed_CMS_ID=$problem_snomed['CMS_ID'];
                $snomed_Value_Set_Name=$problem_snomed['Value_Set_Name'];
            }
            if(empty($problemList['problem_icd9'])==false){
                $problem_icd9=$problemList['problem_icd9'];
                $icd9_Value_Set_OID=$problem_icd9['Value_Set_OID'];
                $icd9_Code_System=$problem_icd9['Code_System'];
                $icd9_Code=$problem_icd9['Code'];
                $icd9_Description=$problem_icd9['Description'];
                $icd9_Code_System_OID=$problem_icd9['Code_System_OID'];
                $icd9_CMS_ID=$problem_icd9['CMS_ID'];
                $icd9_Value_Set_Name=$problem_icd9['Value_Set_Name'];
            }
            if(empty($problemList['problem_icd10'])==false){
                $problem_icd10=$problemList['problem_icd10'];
                $icd10_Value_Set_OID=$problem_icd10['Value_Set_OID'];
                $icd10_Code_System=$problem_icd10['Code_System'];
                $icd10_Code=$problem_icd10['Code'];
                $icd10_Description=$problem_icd10['Description'];
                $icd10_Code_System_OID=$problem_icd10['Code_System_OID'];
                $icd10_CMS_ID=$problem_icd10['CMS_ID'];
                $icd10_Value_Set_Name=$problem_icd10['Value_Set_Name'];
            }
            
            $templateId1='2.16.840.1.113883.10.20.24.3.137';
            $templateId2='2.16.840.1.113883.10.20.24.3.135';
            if($problemList['prob_type']=='Symptom') {
                $templateId1='2.16.840.1.113883.10.20.24.3.138';
                $templateId2='2.16.840.1.113883.10.20.24.3.136';
            }

            $XML_problem_section .= '<entry>';
            $XML_problem_section .= '<act classCode="ACT" moodCode="EVN">
            <!-- Conforms to C-CDA 2.1 Problem Concern Act (V3) -->
            <templateId root="2.16.840.1.113883.10.20.22.4.3" extension="2015-08-01" />
            <!-- Diagnosis Concern Act -->
            <templateId root="'.$templateId1.'" extension="2017-08-01"/>
            <id root="1.3.6.1.4.1.115" extension="5d3ee4f6dfe4bd0398b2a'.++$ext_counter.'"/>
            <code code="CONC" codeSystem="2.16.840.1.113883.5.6" displayName="Concern" />
            <statusCode code="active" />';

            if ($problemList['onset_date'] != "") {
                $XML_problem_section .= ' <effectiveTime>';
                $XML_problem_section .= ' <low value="' . str_replace('-', '', $problemList['onset_date']) . str_replace(':', '', $problemList['OnsetTime']) .'"/>';
                if( $problemList['end_datetime'] && $problemList['end_datetime'] <> '0000-00-00 00:00:00'  ){
                    $XML_problem_section .= '<high value="'.date('YmdHis',strtotime($problemList['end_datetime'])).'"/>';
                } else {
                    $XML_problem_section .= '<high nullFlavor="UNK"/>';
                }
                $XML_problem_section .= '</effectiveTime>';
            } else {
                $XML_problem_section .= ' <effectiveTime nullFlavor="NI"/>';
            }
            $XML_problem_section .= '<entryRelationship typeCode="SUBJ">';
            $XML_problem_section .= '<observation classCode="OBS" moodCode="EVN">';
            $XML_problem_section .= '<!-- Problem Observation template -->';

            $XML_problem_section .= '<templateId root="2.16.840.1.113883.10.20.22.4.4" extension="2015-08-01" />';
            $XML_problem_section .= '<templateId root="'.$templateId2.'" extension="2017-08-01" />';
            $XML_problem_section .= '<id root="1.3.6.1.4.1.115" extension="5a23958bcde4a3001848b'.++$ext_counter.'"/>';

            $arrProbType = $this->problem_type_srh($problemList['prob_type']);

            if ($arrProbType['code'] != "" && $arrProbType['display_name'] != "") {
                $XML_problem_section .= '<code code="'.$arrProbType['loinic'].'" codeSystem="2.16.840.1.113883.6.1" >
                                            <translation code="'.$arrProbType['code'].'" codeSystem="2.16.840.1.113883.6.96" displayName="'.$arrProbType['display_name'].'"/>
                                        </code>';
            } else {
                $XML_problem_section .= '<code nullFlavor="NI"/>';
            }

            $XML_problem_section .= '<statusCode code="completed"/>';

            if ($problemList['onset_date'] != "") {
                $XML_problem_section .= ' <effectiveTime>';
                $XML_problem_section .= ' <low value="' . str_replace('-', '', $problemList['onset_date']) . str_replace(':', '', $problemList['OnsetTime']) .'"/>';
                if( $problemList['end_datetime'] && $problemList['end_datetime'] <> '0000-00-00 00:00:00'  ){
                    $XML_problem_section .= '<high value="'.date('YmdHis',strtotime($problemList['end_datetime'])).'"/>';
                } else {
                    $XML_problem_section .= '<high nullFlavor="UNK"/>';
                }
                $XML_problem_section .= '</effectiveTime>';
            } else {
                $XML_problem_section .= ' <effectiveTime nullFlavor="NI"/>';
            }

            // DYNAMIC PROBLEM VALUE //
            if(empty($problemList['problem_snomed'])==false && empty($problemList['problem_icd10'])==false && empty($problemList['problem_icd9'])==false ) {
                $XML_problem_section .= '<value xsi:type="CD" code="'.$snomed_Code.'" codeSystem="'.$snomed_Code_System_OID.'" codeSystemName="'.$snomed_Code_System.'">';
                $XML_problem_section .= '<translation code="'.$icd9_Code.'" codeSystem="'.$icd9_Code_System_OID.'" codeSystemName="'.$icd9_Code_System.'"/>';
                $XML_problem_section .= '<translation code="'.$icd10_Code.'" codeSystem="'.$icd10_Code_System_OID.'" codeSystemName="'.$icd10_Code_System.'"/>';
                $XML_problem_section .= '</value>';
            } else if(empty($problemList['problem_snomed'])==false && empty($problemList['problem_icd10']) && empty($problemList['problem_icd9'])==false ) {
                $XML_problem_section .= '<value xsi:type="CD" code="'.$snomed_Code.'" codeSystem="'.$snomed_Code_System_OID.'" codeSystemName="'.$snomed_Code_System.'">';
                $XML_problem_section .= '<translation code="'.$icd9_Code.'" codeSystem="'.$icd9_Code_System_OID.'" codeSystemName="'.$icd9_Code_System.'"/>';
                $XML_problem_section .= '</value>';
            } else if(empty($problemList['problem_snomed'])==false && empty($problemList['problem_icd10'])==false && empty($problemList['problem_icd9']) ) {
                $XML_problem_section .= '<value xsi:type="CD" code="'.$snomed_Code.'" codeSystem="'.$snomed_Code_System_OID.'" codeSystemName="'.$snomed_Code_System.'">';
                $XML_problem_section .= '<translation code="'.$icd10_Code.'" codeSystem="'.$icd10_Code_System_OID.'" codeSystemName="'.$icd10_Code_System.'"/>';
                $XML_problem_section .= '</value>';
            } else if(empty($problemList['problem_snomed']) && empty($problemList['problem_icd10'])==false && empty($problemList['problem_icd9'])==false ) {
                $XML_problem_section .= '<value xsi:type="CD" code="'.$icd10_Code.'" codeSystem="'.$icd10_Code_System_OID.'" codeSystemName="'.$icd10_Code_System.'">';
                $XML_problem_section .= '<translation code="'.$icd9_Code.'" codeSystem="'.$icd9_Code_System_OID.'" codeSystemName="'.$icd9_Code_System.'"/>';
                $XML_problem_section .= '</value>';
            } else if(empty($problemList['problem_snomed'])==false && empty($problemList['problem_icd10']) && empty($problemList['problem_icd9']) ) {
                $XML_problem_section .= '<value xsi:type="CD" code="'.$snomed_Code.'" codeSystem="'.$snomed_Code_System_OID.'" codeSystemName="'.$snomed_Code_System.'"></value>';
            } else if(empty($problemList['problem_snomed']) && empty($problemList['problem_icd10'])==false && empty($problemList['problem_icd9']) ) {
                $XML_problem_section .= '<value xsi:type="CD" code="'.$icd10_Code.'" codeSystem="'.$icd10_Code_System_OID.'" codeSystemName="'.$icd10_Code_System.'"></value>';
            } else if(empty($problemList['problem_snomed']) && empty($problemList['problem_icd10']) && empty($problemList['problem_icd9'])==false ) {
                $XML_problem_section .= '<value xsi:type="CD" code="'.$icd9_Code.'" codeSystem="'.$icd9_Code_System_OID.'" codeSystemName="'.$icd9_Code_System.'"></value>';
            } else {
                $XML_problem_section .= '<value xsi:type="CD" nullFlavor="UNK"></value>';
            }

            $XML_problem_section .= '</observation>';
            $XML_problem_section .= '</entryRelationship>';
            $XML_problem_section .= '</act>';
            $XML_problem_section .= '</entry>';
        }
        

        return $XML_problem_section;
    }
    /* END PROBLEM SECTION */
    

    
    
    public function problem_type_srh($val) {   //SNOMED CT
        $val = trim(strtolower($val));
        $arrProbType = array(
            array("imw" => 'finding', "code" => "404684003", "display_name" => "Finding", "loinic" => "29308-4"),
            array("imw" => 'complaint', "code" => "409586006", "display_name" => "Complaint", "loinic" => "29308-4"),
            array("imw" => 'diagnosis', "code" => "282291009", "display_name" => "Diagnosis", "loinic" => "29308-4"),
            array("imw" => 'condition', "code" => "64572001", "display_name" => "Disorder", "loinic" => "29308-4"),
            array("imw" => 'smoker, current status unknown', "code" => "248536006", "display_name" => "Finding of functional performance and activity", "loinic" => "29308-4"),
            array("imw" => 'symptom', "code" => "418799008", "display_name" => "Symptom", "loinic" => "75325-1"),
            array("imw" => 'problem', "code" => "55607006", "display_name" => "Problem", "loinic" => "29308-4"),
            array("imw" => 'cognitive function finding', "code" => "373930000", "display_name" => "Cognitive function finding", "loinic" => "29308-4")
        );
        $arr = array();
        if ($val != "") {
            foreach ($arrProbType as $row) {
                if (in_array($val, $row)) {
                    $arr['loinic'] = $row['loinic'];
                    $arr['code'] = $row['code'];
                    $arr['display_name'] = $row['display_name'];
                    break;
                } else {
                    $arr['code'] = '';
                    $arr['loinic'] = '';
                    $arr['display_name'] = '';
                }
            }
        }
        return $arr;
    }
    
    
    /* BEGIN PHYSICAL EXAMS */
    public function physical_exam_data_xml($pt_physical_exam=array()) {
        $XML_physical_exams='';
        $ext_counter=700;
        if(isset($pt_physical_exam['VS']) && empty($pt_physical_exam['VS'])==false) {
            foreach ($pt_physical_exam['VS'] as $row_vital) {
                if (isset($row_vital['Value_Set_OID']) && $row_vital['Value_Set_OID'] != "") {
                    $ext_counter++;
                    $XML_physical_exams .= '<entry>
                        <!-- Physical Exam Finding -->
                        <observation classCode="OBS" moodCode="EVN">
                        <!-- Procedure Activity Procedure (Consolidation) template -->
                        <templateId root="2.16.840.1.113883.10.20.22.4.13" extension="2014-06-09"/>
                        <!-- Physical Exam, Performed template -->
                        <templateId root="2.16.840.1.113883.10.20.24.3.59" extension="2017-08-01"/>
                        <id root="1.3.6.1.4.1.115" extension="5d3ee4f8dfe4bd0398b2a'.$ext_counter.'"/>
                        <!-- QDM Attribute: Code -->
                        <code code="'.$row_vital['Code'].'" codeSystem="'.$row_vital['Code_System_OID'].'" codeSystemName="'.$row_vital['Code_System'].'" />
                        <text>'.$row_vital['Value_Set_Name'].'</text>
                        <statusCode code="completed"/>
                        <!-- QDM Attribute: Relevant Period -->
                        <effectiveTime>
                          <low value="' . str_replace('-', '', $row_vital['date_vital']) . date('His', $row_vital['inhale_O2']) .'"/>
                          <high value="' . str_replace('-', '', $row_vital['date_vital']) . date('His', $row_vital['inhale_O2']) .'"/>
                        </effectiveTime>';
                    if ($row_vital['range_vital'] != "") {
                        $XML_physical_exams .= '<value xsi:type="PQ" value="'.trim($row_vital['range_vital']).'" unit="'.html_entity_decode(preg_replace('/\s/', '', trim($row_vital['unit']))).'"/>';
                    } else {
                        $XML_physical_exams .= '<value xsi:type="PQ" nullFlavor="NI"/>';
                    }
                    
                    if ($row_vital['phy_reviewed']>0 && $row_vital['phy_reviewed_date']!= "0000-00-00 00:00:0") {
                        $XML_physical_exams .= '<!-- QDM Attribute: Author dateTime -->
                            <author>
                                <templateId root="2.16.840.1.113883.10.20.24.3.155" extension="2017-08-01"/>
                                <time value="'.str_replace(array('-',':',' '), '', $row_vital['phy_reviewed_date']).'" />
                                <assignedAuthor>
                                    <id nullFlavor="NA"/>
                                </assignedAuthor>
                            </author> ';
                    }
                    $XML_physical_exams .= '</observation></entry>';
                }
            }

        }

        //Health Observations.
        if(isset($pt_physical_exam['HealthObs']) && empty($pt_physical_exam['HealthObs'])==false) {
            foreach($pt_physical_exam['HealthObs'] as $row_hc) {
                if (isset($row_hc['Code']) && $row_hc['Code'] != "") {
                    $ext_counter++;
                    
                    if($row_hc['Code']=='419775003'){
                        $row_hc['Code_System_OID']='2.16.840.1.113883.6.96';
                        $row_hc['Code_System']='SNOMEDCT';
                        $row_hc['Description']='Best corrected visual acuity (observable entity)';
                    }

                    $XML_physical_exams .= '<entry>
                    <observation classCode="OBS" moodCode="EVN" >
                    <!-- Procedure Activity Procedure (Consolidation) template -->
                    <templateId root="2.16.840.1.113883.10.20.22.4.13" extension="2014-06-09"/>
                    <!-- Physical Exam, Performed template -->
                    <templateId root="2.16.840.1.113883.10.20.24.3.59" extension="2017-08-01"/>
                    <id root="1.3.6.1.4.1.115" extension="5a23958bcde4a3001848b'.$ext_counter.'"/>
                    <code code="'.$row_hc['Code'].'" codeSystem="'.$row_hc['Code_System_OID'].'"  codeSystemName="'.$row_hc['Code_System'].'" />
                    <text>Best corrected visual acuity (observable entity)</text>
                    <statusCode code="completed"/>
                    <effectiveTime>
                        <low value="' . str_replace('-', '', $row_hc['observation_date']) . str_replace(':', '', $row_hc['observation_time']) .'"/>
                        <high value="' . str_replace('-', '', $row_hc['observation_date']) . str_replace(':', '', $row_hc['observation_time']) .'"/>
                    </effectiveTime>';
                    if($row_hc['scode'] && empty($row_hc['scode'])==false && isset($row_hc['scode']['Code']) && $row_hc['scode']['Code']!='') {
                        $Value_Set_OID=$row_hc['scode']['Value_Set_OID'];
                        $Code_System=$row_hc['scode']['Code_System'];
                        $Code=$row_hc['scode']['Code'];
                        $Description=$row_hc['scode']['Description'];
                        $Code_System_OID=$row_hc['scode']['Code_System_OID'];
                        $CMS_ID=$row_hc['scode']['CMS_ID'];

                        $XML_physical_exams .= '<value code="'.$Code.'" codeSystem="'.$Code_System_OID.'" xsi:type="CD" codeSystemName="'.$Code_System.'" />';
                    } else {
                        $XML_physical_exams .= '<value xsi:type="CD" nullFlavor="UNK"/>';     
                    }
                    $XML_physical_exams .= '</observation></entry>';
                }
            }
        }

        return $XML_physical_exams;

    }
    /* END PHYSICAL EXAMS */
    
    
    //-------BEGIN Medication SECTION --------------//
    public function pt_medications_data_xml($pt_medications_data=array()) {
        $XML_medication_entry='';
        $ext_counter=800;
        foreach( $pt_medications_data as $row ) {
                $refusal_med = ($row['refusal'] && $row['refusal_snomed'] ) ? 'true' : '';

                $period_value = '';
                $period_val_unit = '';
                if(trim($row['sig'])) {
                    $period = explode(';', trim($row['sig']));
                    $period_value = $period[0];
                    $period_val_unit = $period[1];
                }

                $doseQuantity = trim($row['destination']) ? trim($row['destination']) : '';

                $XML_medication_entry .= '<entry>';
                if ($row['allergy_status'] == 'Active') {
                    $XML_medication_entry .= '<substanceAdministration classCode="SBADM"  moodCode="EVN" '.($refusal_med ? 'negationInd="'.$refusal_med.'"' : '' ).' >
                                    <!-- Medication Activity (consolidation) template -->
                                    <templateId root="2.16.840.1.113883.10.20.22.4.16" extension="2014-06-09"/>
                                    <!-- Medication, Active template -->
                                    <templateId root="2.16.840.1.113883.10.20.24.3.41" extension="2017-08-01"/>';
                } else {
                    $XML_medication_entry .= '<!--Medication Order -->
                                <substanceAdministration classCode="SBADM" moodCode="RQO" '.($refusal_med ? 'negationInd="'.$refusal_med.'"' : '' ).' >
                                <templateId root="2.16.840.1.113883.10.20.22.4.42" extension="2014-06-09"/>
                                <!-- Medication, Order template -->
                                <templateId root="2.16.840.1.113883.10.20.24.3.47" extension="2018-10-01"/>';
                }
                    $XML_medication_entry .= '<id root="1.3.6.1.4.1.115" extension="5a1e6cbacde4a364e6dca' . $ext_counter++ . '"/>
                        <text>' . $row['title'] . '</text>
                        <statusCode code="active" />
                        <!-- QDM Attribute: Relevant Period -->
                        <effectiveTime xsi:type="IVL_TS">
                            <low value="' . date('YmdHis', strtotime($row['begdate'].' '.$row['begtime'])) . '"/>
                            <high value="' . date('YmdHis', strtotime($row['begdate'].' '.$row['begtime'])) . '"/>
                        </effectiveTime>
                        ';
                
                
                if ($period_value){
                    $XML_medication_entry .= '<!--  QDM Attribute: Frequency -->';
                    $XML_medication_entry .= '<effectiveTime xsi:type="PIVL_TS" institutionSpecified="true" operator="A"> ';
                    $XML_medication_entry .= '<period value="' . $period_value . '" ' . ($period_val_unit ? 'unit="' . $period_val_unit . '"' : '') . ' />';
                    $XML_medication_entry .= '</effectiveTime>';
                }
                
                $XML_medication_entry .= '
                    <!--  QDM Attribute: Dosage -->';
                if ($doseQuantity) {
                    $XML_medication_entry .= '<doseQuantity value="'.$doseQuantity.'" />';
                } else {
                    $XML_medication_entry .= '<doseQuantity nullFlavor="UNK"/>';
                }
                $XML_medication_entry .= '	

                <consumable>
                    <manufacturedProduct classCode="MANU">
                        <!-- Medication Information (consolidation) template -->
                        <templateId root="2.16.840.1.113883.10.20.22.4.23" extension="2014-06-09"/>
                        <id root="69c36d40-c02a-0137-3bba-0eca209bc306"/>
                        <manufacturedMaterial>
                        <!--  QDM Attribute: Code -->';
                        if ($row['ccda_code'] && isset($row['Value_Set_OID']) && empty($row['Value_Set_OID'])==false) {
                            $Value_Set_OID=$row['Value_Set_OID'];
                            $Code_System=$row['Code_System'];
                            $Code=$row['Code'];
                            $Description=$row['Description'];
                            $Code_System_OID=$row['Code_System_OID'];

                            $XML_medication_entry .= '<code code="' . $Code . '" codeSystem="'.$Code_System_OID.'" codeSystemName="'.$Code_System.'" />';
                        } else {
                            $XML_medication_entry .= '<code nullFlavor="NA" sdtc:valueSet="2.16.840.1.113883.3.526.3.1190"/>';
                        }

                $XML_medication_entry .= '			
                        </manufacturedMaterial>
                    </manufacturedProduct>
                </consumable>';
                
                if ($row['allergy_status'] != 'Active') {
                    $XML_medication_entry .= '			
                    <!-- QDM Attribute: Author dateTime -->
                    <author>
                        <templateId root="2.16.840.1.113883.10.20.24.3.155" extension="2017-08-01"/>
                        <time value="' . date('YmdHis', strtotime($row['begdate'].' '.$row['begtime'])) . '"/>
                        <assignedAuthor>
                            <id nullFlavor="NA" />
                        </assignedAuthor>
                    </author>';
                }

                if( $refusal_med && empty($row['med_refusal'])==false ) {
                    $Value_Set_OID=$row['med_refusal']['Value_Set_OID'];
                    $Code_System=$row['med_refusal']['Code_System'];
                    $Code=$row['med_refusal']['Code'];
                    $Description=$row['med_refusal']['Description'];
                    $Code_System_OID=$row['med_refusal']['Code_System_OID'];

                    $XML_medication_entry .= '
                    <!-- QDM Attribute: Negation Rationale -->
                    <entryRelationship typeCode="RSON">
                        <observation classCode="OBS" moodCode="EVN">
                            <templateId root="2.16.840.1.113883.10.20.24.3.88" extension="2017-08-01"/>
                            <id root="1.3.6.1.4.1.115" extension="b6375720-dd10-0137-0f71-0eca209bc306" />
                            <code code="77301-0" codeSystem="2.16.840.1.113883.6.1" displayName="reason" codeSystemName="LOINC"/>
                            <statusCode code="completed"/>
                                <effectiveTime>
                                <low value="'.date('YmdHis', strtotime($row['begdate'].' '.$row['begtime'])).'"/>
                            </effectiveTime>
                            <value code="'.$Code.'" codeSystem="'.$Code_System_OID.'" codeSystemName="'.$Code_System.'" xsi:type="CD"/>
                        </observation>
                    </entryRelationship>';	
                }

                $XML_medication_entry .= '
                </substanceAdministration>
            </entry>';

        }

        return $XML_medication_entry;
    }
    //-------END Medication SECTION --------------//
    
    
    public function get_surgery_data_xml($pt_surgery_data=array()) {
        $XML_surgery_entry='';
        $ext_counter=900;
        foreach($pt_surgery_data as $row) {
            if(empty($row['implant_hcpcs'])==false && $row['implant_status']=='order') {
                $implant_hcpcs=$row['implant_hcpcs'];
                $Value_Set_OID=$implant_hcpcs['Value_Set_OID'];
                $Code_System=$implant_hcpcs['Code_System'];
                $Code=$implant_hcpcs['Code'];
                $Description=$implant_hcpcs['Description'];
                $Code_System_OID=$implant_hcpcs['Code_System_OID'];
                $CMS_ID=$implant_hcpcs['CMS_ID'];
                $Value_Set_Name=$implant_hcpcs['Value_Set_Name'];
            
                $XML_surgery_entry.='<entry>
                    <act classCode="ACT" moodCode="RQO" >
                        <templateId root="2.16.840.1.113883.10.20.24.3.130" extension="2017-08-01"/>
                        <id root="1.3.6.1.4.1.115" extension="5d3ee4fcdfe4bd0398b2b'.$ext_counter++.'"/>
                        <code code="SPLY" codeSystem="2.16.840.1.113883.5.6" displayName="Supply" codeSystemName="ActClass"/>
                        <entryRelationship typeCode="SUBJ">
                            <supply classCode="SPLY" moodCode="RQO">
                                <!-- Plan of Care Activity Supply -->
                                <templateId root="2.16.840.1.113883.10.20.22.4.43" extension="2014-06-09"/>
                                <!-- Device, Order -->
                                <templateId root="2.16.840.1.113883.10.20.24.3.9" extension="2017-08-01"/>
                                <id root="1.3.6.1.4.1.115" extension="5d3ee4fcdfe4bd0398b2b'.$ext_counter++.'"/>
                                <text>'.$Value_Set_Name.'</text>
                                <statusCode code="active"/>
                                <!-- QDM Attribute: Author dateTime -->
                                <author>
                                    <templateId root="2.16.840.1.113883.10.20.24.3.155" extension="2017-08-01"/>
                                    <time value="'.date('YmdHis', strtotime($row['begdate'].' '.$row['begtime'])).'"/>
                                    <assignedAuthor>
                                        <id nullFlavor="NA"/>
                                    </assignedAuthor>
                                </author>                    
                                <participant typeCode="DEV">
                                    <participantRole classCode="MANU">
                                        <playingDevice classCode="DEV">
                                            <!-- QDM Attribute: Code -->
                                            <code code="'.$Code.'" codeSystem="'.$Code_System_OID.'" codeSystemName="'.$Code_System.'"/>
                                        </playingDevice>
                                    </participantRole>
                                </participant>
                            </supply>
                        </entryRelationship>
                    </act>
                </entry>';
            } else if(empty($row['implant_hcpcs'])==false && $row['implant_status']=='applied') {
                $implant_hcpcs=$row['implant_hcpcs'];
                $Value_Set_OID=$implant_hcpcs['Value_Set_OID'];
                $Code_System=$implant_hcpcs['Code_System'];
                $Code=$implant_hcpcs['Code'];
                $Description=$implant_hcpcs['Description'];
                $Code_System_OID=$implant_hcpcs['Code_System_OID'];
                $CMS_ID=$implant_hcpcs['CMS_ID'];
                $Value_Set_Name=$implant_hcpcs['Value_Set_Name'];
            
                $XML_surgery_entry.='<entry>
                    <procedure classCode="PROC" moodCode="EVN" >
                      <!-- Procedure Activity Procedure -->
                      <templateId root="2.16.840.1.113883.10.20.22.4.14" extension="2014-06-09"/>
                      <!-- Device Applied -->
                      <templateId root="2.16.840.1.113883.10.20.24.3.7" extension="2018-10-01"/>
                      <id root="1.3.6.1.4.1.115" extension="5d3ee4fedfe4bd0398b2b'.$ext_counter++.'"/>
                      <code code="360030002" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED CT" displayName="application of device"/>
                      <text>'.$Value_Set_Name.'</text>
                      <statusCode code="completed"/>
                        <!-- QDM Attribute: Relevant Period -->
                        <effectiveTime><low value="'.date('YmdHis', strtotime($row['begdate'].' '.$row['begtime'])).'"/><high nullFlavor="UNK"/></effectiveTime>
                      <participant typeCode="DEV">
                        <participantRole classCode="MANU">
                          <playingDevice classCode="DEV">
                            <!-- QDM Attribute: Code -->
                            <code code="'.$Code.'" codeSystem="'.$Code_System_OID.'" codeSystemName="'.$Code_System.'"/>
                          </playingDevice>
                        </participantRole>
                      </participant>
                    </procedure>
                  </entry>';
            }
            
        }
        
        return $XML_surgery_entry;
    }
}

?>