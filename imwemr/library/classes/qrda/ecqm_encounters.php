<?php

require_once(dirname(__FILE__) . "/ecqm_data.php");

class ECQMEncounters extends ECQMData {

    public function __construct($patient_id = false, $pro_id = false, $CMS_ID = false) {
        
        $this->patient_id = $patient_id;
        $this->CMS_ID = $CMS_ID;
        $this->pro_id = $pro_id;
        
        parent::__construct();
        
    }

    public function get_pt_encounter_cpt_codes($sa_app_start_date='',$sa_app_starttime='',$ecqm_v8_data=array(),$appt_id='') {
        $query="select procedureinfo.description as cpt_desc, procedureinfo.cptCode as cpt_Code 
                from chart_master_table 
                join superbill on chart_master_table.id=superbill.formId 
                join procedureinfo on superbill.idSuperBill=procedureinfo.idSuperBill 
                where 
                superbill.del_status  = '0' 
                and procedureinfo.delete_status  = '0' 
                and chart_master_table.date_of_service='$sa_app_start_date' 
                and chart_master_table.time_of_service='$sa_app_starttime' 
                and superbill.patientId='".$this->patient_id."' 
                order by procedureinfo.porder,procedureinfo.id asc";
        $qry_sup = imw_query($query);
        if(imw_num_rows($qry_sup) == 0) {
            $query="select procedureinfo.description as cpt_desc, procedureinfo.cptCode as cpt_Code 
                    from chart_master_table 
                    join superbill on chart_master_table.id=superbill.formId 
                    join procedureinfo on superbill.idSuperBill=procedureinfo.idSuperBill 
                    where 
                    superbill.del_status  = '0'
                    and procedureinfo.delete_status  = '0'
                    and chart_master_table.date_of_service='$sa_app_start_date' 
                    and superbill.patientId='".$this->patient_id."' 
                    order by procedureinfo.porder,procedureinfo.id asc";
            $qry_sup = imw_query($query);
        }
        $cpt_Code_arr=array();
        if($qry_sup && imw_num_rows($qry_sup)>0) {
            while($cpt_Code_row = imw_fetch_assoc($qry_sup)) {
                $enc_row=array();
                $cpt_Code = trim($cpt_Code_row['cpt_Code']);
                if(isset($ecqm_v8_data['CPT'][$cpt_Code])) {
                    $enc_row['Value_Set_OID']=$ecqm_v8_data['CPT'][$cpt_Code]['Value_Set_OID'];
                    $enc_row['Code_System']=$ecqm_v8_data['CPT'][$cpt_Code]['Code_System'];
                    $enc_row['Code']=$ecqm_v8_data['CPT'][$cpt_Code]['Code'];
                    $enc_row['Description']=$ecqm_v8_data['CPT'][$cpt_Code]['Description'];
                    $enc_row['Code_System_OID']=$ecqm_v8_data['CPT'][$cpt_Code]['Code_System_OID'];
                    $enc_row['CMS_ID']=$ecqm_v8_data['CPT'][$cpt_Code]['CMS_ID'];
                    $enc_row['Value_Set_Name']=$ecqm_v8_data['CPT'][$cpt_Code]['Value_Set_Name'];
                    $enc_row['cpt_Code']=$cpt_Code;
                }
                if(isset($ecqm_v8_data['SNOMEDCT'][$cpt_Code])) {
                    $enc_row['Value_Set_OID']=$ecqm_v8_data['SNOMEDCT'][$cpt_Code]['Value_Set_OID'];
                    $enc_row['Code_System']=$ecqm_v8_data['SNOMEDCT'][$cpt_Code]['Code_System'];
                    $enc_row['Code']=$ecqm_v8_data['SNOMEDCT'][$cpt_Code]['Code'];
                    $enc_row['Description']=$ecqm_v8_data['SNOMEDCT'][$cpt_Code]['Description'];
                    $enc_row['Code_System_OID']=$ecqm_v8_data['SNOMEDCT'][$cpt_Code]['Code_System_OID'];
                    $enc_row['CMS_ID']=$ecqm_v8_data['SNOMEDCT'][$cpt_Code]['CMS_ID'];
                    $enc_row['Value_Set_Name']=$ecqm_v8_data['SNOMEDCT'][$cpt_Code]['Value_Set_Name'];
                    $enc_row['cpt_Code']=$cpt_Code;
                }
                if(isset($ecqm_v8_data['HCPCS'][$cpt_Code])) {
                    $enc_row['Value_Set_OID']=$ecqm_v8_data['HCPCS'][$cpt_Code]['Value_Set_OID'];
                    $enc_row['Code_System']=$ecqm_v8_data['HCPCS'][$cpt_Code]['Code_System'];
                    $enc_row['Code']=$ecqm_v8_data['HCPCS'][$cpt_Code]['Code'];
                    $enc_row['Description']=$ecqm_v8_data['HCPCS'][$cpt_Code]['Description'];
                    $enc_row['Code_System_OID']=$ecqm_v8_data['HCPCS'][$cpt_Code]['Code_System_OID'];
                    $enc_row['CMS_ID']=$ecqm_v8_data['HCPCS'][$cpt_Code]['CMS_ID'];
                    $enc_row['Value_Set_Name']=$ecqm_v8_data['HCPCS'][$cpt_Code]['Value_Set_Name'];
                    $enc_row['cpt_Code']=$cpt_Code;
                }
                
                if( isset($ecqm_v8_data['CPT'][$cpt_Code]) || isset($ecqm_v8_data['SNOMEDCT'][$cpt_Code]) || isset($ecqm_v8_data['HCPCS'][$cpt_Code]) ) {
                    $enc_diag_arr=$this->get_pt_encounter_diagnosis($sa_app_start_date,$sa_app_starttime,$ecqm_v8_data);
                    $enc_row['enc_diag_arr']=$enc_diag_arr;
                    /* Get inpatient fields data */
                    $inp_data=$this->get_enc_in_patient_data($ecqm_v8_data,$appt_id);
                    $enc_row['ENC_IN_PATIENT']=$inp_data;
                    
                    $cpt_Code_arr[]=$enc_row;
                }
            }
        }
        return $cpt_Code_arr;
    }
    
    
    public function get_enc_in_patient_data($ecqm_v8_data=array(),$appt_id='') {
        /* Start inpatient fields data */
        $inp_data = array();
        $inp_qry = "Select * From inpatient_fields Where appt_id = '".$appt_id."' Order By field_type ";
        $inp_sql = imw_query($inp_qry);
        if( $inp_sql && imw_num_rows($inp_sql) > 0 ) {
            
            while( $inp_row = imw_fetch_assoc($inp_sql) ) {
                $dxCode=$inp_row['field_code'];
                if(isset($ecqm_v8_data['ICD10CM'][$dxCode])) {
                    $inp_row['Value_Set_OID']=$ecqm_v8_data['ICD10CM'][$dxCode]['Value_Set_OID'];
                    $inp_row['Code_System']=$ecqm_v8_data['ICD10CM'][$dxCode]['Code_System'];
                    $inp_row['Code']=$ecqm_v8_data['ICD10CM'][$dxCode]['Code'];
                    $inp_row['Description']=$ecqm_v8_data['ICD10CM'][$dxCode]['Description'];
                    $inp_row['Code_System_OID']=$ecqm_v8_data['ICD10CM'][$dxCode]['Code_System_OID'];
                    $inp_row['CMS_ID']=$ecqm_v8_data['ICD10CM'][$dxCode]['CMS_ID'];
                    $inp_row['Value_Set_Name']=$ecqm_v8_data['ICD10CM'][$dxCode]['Value_Set_Name'];
                } else if(isset($ecqm_v8_data['SNOMEDCT'][$dxCode])) {
                    $inp_row['Value_Set_OID']=$ecqm_v8_data['SNOMEDCT'][$dxCode]['Value_Set_OID'];
                    $inp_row['Code_System']=$ecqm_v8_data['SNOMEDCT'][$dxCode]['Code_System'];
                    $inp_row['Code']=$ecqm_v8_data['SNOMEDCT'][$dxCode]['Code'];
                    $inp_row['Description']=$ecqm_v8_data['SNOMEDCT'][$dxCode]['Description'];
                    $inp_row['Code_System_OID']=$ecqm_v8_data['SNOMEDCT'][$dxCode]['Code_System_OID'];
                    $inp_row['CMS_ID']=$ecqm_v8_data['SNOMEDCT'][$dxCode]['CMS_ID'];
                    $inp_row['Value_Set_Name']=$ecqm_v8_data['SNOMEDCT'][$dxCode]['Value_Set_Name'];
                }

                $inp_data[$inp_row['appt_id']][$inp_row['field_type']] = $inp_row;
            }
        }
        
        return $inp_data;
        /* End inpatient fields data */
    }
    
    public function get_pt_encounter_list() {
        $encounters_data = array();
        $sql = "SELECT appt.id, appt.sa_app_start_date,sa_app_end_date,appt.sa_app_starttime,appt.sa_app_endtime, appt.is_inpatient
                FROM schedule_appointments appt 
                WHERE appt.sa_patient_id IN('".$this->patient_id."')
                AND appt.sa_patient_app_status_id NOT IN(203,201,18,19,20,3)";
        $enc_qry = imw_query($sql);
        if($enc_qry && imw_num_rows($enc_qry)>0) {
            //Get CMS Version-8 DATA 
            $ecqm_v8_data=$this->ecqm_v8_data($this->CMS_ID);
            
            while ($enc_row = imw_fetch_assoc($enc_qry)) {
                $sa_app_start_date = $enc_row['sa_app_start_date'];
                $sa_app_starttime = $enc_row['sa_app_starttime'];
                $sa_app_end_date = $enc_row['sa_app_end_date'];
                $is_inpatient = $enc_row['is_inpatient'];
                
                $enc_row['enc_patient_id']=$this->patient_id;
                
                $cpt_Code_arr=$this->get_pt_encounter_cpt_codes($sa_app_start_date,$sa_app_starttime,$ecqm_v8_data,$enc_row['id']);
                $enc_row['enc_cpt_Code']=$cpt_Code_arr;
                
                $encounters_data[]=$enc_row;
            }
        }

        return $encounters_data;
    }
    
    
    public function get_pt_encounter_diagnosis($sa_app_start_date='',$sa_app_starttime='',$ecqm_v8_data=array()) {
        $pt_prb_data['enc_diag_arr'] = array();
        $data=' id, pt_id, user_id,	problem_name, comments,	onset_date,	status,	OnsetTime, prob_type, form_id, ccda_code ';
        $sql = "SELECT ".$data." FROM pt_problem_list 
                    WHERE pt_id IN('".$this->patient_id."') 
                    AND status='Active' 
                    AND prob_type='Condition' 
                    AND onset_date = '".$sa_app_start_date."' 
                    AND OnsetTime = '".$sa_app_starttime."' 
                    AND form_id=0 ";
        $res = imw_query($sql);
        if($res && imw_num_rows($res)>0) {
            $pt_prb_data = imw_fetch_assoc($res);
            $ccda_code=$pt_prb_data['ccda_code'];
            if(isset($ecqm_v8_data['SNOMEDCT'][$ccda_code])) {
                $pt_prb_data['Value_Set_OID']=$ecqm_v8_data['SNOMEDCT'][$ccda_code]['Value_Set_OID'];
                $pt_prb_data['Code_System']=$ecqm_v8_data['SNOMEDCT'][$ccda_code]['Code_System'];
                $pt_prb_data['Code']=$ecqm_v8_data['SNOMEDCT'][$ccda_code]['Code'];
                $pt_prb_data['Description']=$ecqm_v8_data['SNOMEDCT'][$ccda_code]['Description'];
                $pt_prb_data['Code_System_OID']=$ecqm_v8_data['SNOMEDCT'][$ccda_code]['Code_System_OID'];
                $pt_prb_data['CMS_ID']=$ecqm_v8_data['SNOMEDCT'][$ccda_code]['CMS_ID'];
                $pt_prb_data['Value_Set_Name']=$ecqm_v8_data['SNOMEDCT'][$ccda_code]['Value_Set_Name'];
            }
        }

        return $pt_prb_data;
    }
    
    /* BEGIN ENOCUNTERS SECTION */
    public function get_encounter_data_xml($encounters_data=array() ) {
        $XML_encouter_entry='';
        $ext_counter=100;
        foreach($encounters_data as $encounters) {
            $exp_sa_app_start_date = str_replace('-', '', $encounters['sa_app_start_date']);
            $exp_sa_app_end_date = str_replace('-', '', $encounters['sa_app_end_date']);
            $low_val = $exp_sa_app_start_date . str_replace(':', '', $encounters['sa_app_starttime']);
            $high_val = $exp_sa_app_end_date . str_replace(':', '', $encounters['sa_app_endtime']);

            foreach($encounters['enc_cpt_Code'] as $encounter) {
                $ext_counter++;
                $cpt_Code = $encounter['Code'];

                $XML_encouter_entry .= '<entry>';
                $XML_encouter_entry .= '<act classCode="ACT" moodCode="EVN" >
                <!--Encounter performed Act -->
                <templateId extension="2017-08-01" root="2.16.840.1.113883.10.20.24.3.133"/>
                <id root="1.3.6.1.4.1.115"  extension="5a1b8c7dcde4a30025d3d'.$ext_counter.'"/>
                <code code="ENC" codeSystem="2.16.840.1.113883.5.6" displayName="Encounter" codeSystemName="ActClass"/>
                <entryRelationship typeCode="SUBJ">';
                $XML_encouter_entry .= '<encounter classCode="ENC" moodCode="EVN">';

                /* BEGIN ENCOUNTER ACTIVITIES */
                $XML_encouter_entry .= '<!-- Encounter Activities -->';
                $XML_encouter_entry .= '<!--  Encounter activities template -->
                <templateId extension="2015-08-01" root="2.16.840.1.113883.10.20.22.4.49"/>
                <!-- Encounter performed template -->
                <templateId extension="2017-08-01" root="2.16.840.1.113883.10.20.24.3.23"/>';

                //$XML_encouter_entry .= '<id nullFlavor="NI"/>';
                $XML_encouter_entry .= '<id root="1.3.6.1.4.1.115" extension="5a1b8c7dcde4a30025d3d'.$ext_counter.'"/>';

                if (isset($encounter['Value_Set_OID']) && empty($encounter['Value_Set_OID'])==false) {
                    $Value_Set_OID=$encounter['Value_Set_OID'];
                    $Code_System=$encounter['Code_System'];
                    $Code=$encounter['Code'];
                    $Description=$encounter['Description'];
                    $Code_System_OID=$encounter['Code_System_OID'];
                    $Value_Set_Name=$encounter['Value_Set_Name'];

                    $XML_encouter_entry .= '<code code="'.$Code.'" codeSystem="'.$Code_System_OID.'" codeSystemName="'.$Code_System.'"/>
                    <text>'.$Value_Set_Name.'</text>
                    <statusCode code="completed"/>';
                }

                if ($encounters['sa_app_start_date'] != "") {
                    $XML_encouter_entry .= '<effectiveTime>';
                    $XML_encouter_entry .= '<low value="' . $low_val . '"/>';
                    if($high_val=='00000000000000') {
                        $XML_encouter_entry .='<high nullFlavor="UNK"/>';
                    }else {
                        $XML_encouter_entry .='<high value="' .$high_val. '"/>';
                    }
                    $XML_encouter_entry .= '</effectiveTime>';
                } else {
                    $XML_encouter_entry .= '<effectiveTime nullFlavor="NI"/>';
                }

                $XML_diag_entry='';
                if (isset($encounter['enc_diag_arr']) && empty($encounter['enc_diag_arr'])==false) {
                    $enc_diag_arr=$encounter['enc_diag_arr'];
                    if (isset($enc_diag_arr['Code']) && empty($enc_diag_arr['Code'])==false) {
                        $enc_Value_Set_OID=$enc_diag_arr['Value_Set_OID'];
                        $enc_Code_System=$enc_diag_arr['Code_System'];
                        $enc_Code=$enc_diag_arr['Code'];
                        $enc_Description=$enc_diag_arr['Description'];
                        $enc_Code_System_OID=$enc_diag_arr['Code_System_OID'];
                        $enc_CMS_ID=$enc_diag_arr['CMS_ID'];
                        $enc_Value_Set_Name=$enc_diag_arr['Value_Set_Name'];

                        $XML_diag_entry .= '
                        <!-- QDM Attribute: Diagnoses -->
                        <entryRelationship typeCode="REFR">
                            <act classCode="ACT" moodCode="EVN">
                                <templateId extension="2015-08-01" root="2.16.840.1.113883.10.20.22.4.80"/>
                                <code code="29308-4" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="Diagnosis"/>
                                <entryRelationship typeCode="SUBJ">
                                    <observation classCode="OBS" moodCode="EVN">
                                        <!--  Problem observation template -->
                                        <templateId extension="2015-08-01" root="2.16.840.1.113883.10.20.22.4.4"/>
                                        <id root="4a05cd20-d225-0137-b5bf-0eca209bc'.$ext_counter.'"/>
                                        <code code="29308-4" codeSystem="2.16.840.1.113883.6.1">
                                            <translation code="282291009" codeSystem="2.16.840.1.113883.6.96"/>
                                        </code>
                                        <statusCode code="completed"/>';
                                        if ($encounters['sa_app_start_date'] != "") {
                                            $XML_diag_entry .= '<effectiveTime>';
                                            $XML_diag_entry .= '<low value="' . $low_val . '"/>';
                                            if($high_val=='00000000000000') {
                                                $XML_diag_entry .='<high nullFlavor="UNK"/>';
                                            }else {
                                                $XML_diag_entry .='<high value="' .$high_val. '"/>';
                                            }
                                            $XML_diag_entry .= '</effectiveTime>';
                                        } else {
                                            $XML_diag_entry .= '<effectiveTime nullFlavor="NI"/>';
                                        }
                                        $XML_diag_entry .= '<value code="'.$enc_Code.'" codeSystem="'.$enc_Code_System_OID.'" codeSystemName="'.$enc_Code_System.'" xsi:type="CD"/>
                                    </observation>
                                </entryRelationship>
                            </act>
                        </entryRelationship>';
                    }
                }
                
                $XML_encouter_entry .=$XML_diag_entry;

                //in patient data in encounter
                if (isset($encounter['ENC_IN_PATIENT']) && empty($encounter['ENC_IN_PATIENT'])==false) {
                    foreach($encounter['ENC_IN_PATIENT'][$encounters['id']] as $keyType => $inpTmpData ) {
                        $Value_Set_OID=$inpTmpData['Value_Set_OID'];
                        $Code_System=$inpTmpData['Code_System'];
                        $Code=$inpTmpData['Code'];
                        $Description=$inpTmpData['Description'];
                        $Code_System_OID=$inpTmpData['Code_System_OID'];
                        $CMS_ID=$inpTmpData['CMS_ID'];
                        $Value_Set_Name=$inpTmpData['Value_Set_Name'];

                        if( $keyType == 'DischargeCode' && $inpTmpData['Code'] != '') {
                           $XML_encouter_entry .= '<sdtc:dischargeDispositionCode code="'.$inpTmpData['Code'].'" codeSystem="'.$inpTmpData['Code_System_OID'].'"/>'; 
                        }
                        elseif( $keyType == 'PrincipalDiag' && $inpTmpData['Code'] != '') {
                           $XML_encouter_entry .= '
                            <entryRelationship typeCode="REFR">
                                <observation classCode="OBS" moodCode="EVN">
                                    <code code="8319008" codeSystem="2.16.840.1.113883.6.96" displayName="Principal Diagnosis" codeSystemName="SNOMED CT"/>
                                    <value code="'.$inpTmpData['Code'].'" xsi:type="CD" codeSystem="'.$inpTmpData['Code_System_OID'].'"/>
                                </observation>
                            </entryRelationship>'; 
                        }
                    }
                }

                /* END ENCOUNTER ACTIVITIES */
                $XML_encouter_entry .= '</encounter>';
                $XML_encouter_entry .= '</entryRelationship>';
                $XML_encouter_entry .= '</act>';
                $XML_encouter_entry .= '</entry>';

            }
        }

        return $XML_encouter_entry;
    }
    /* END ENOCUTERS SECTION */
}

?>