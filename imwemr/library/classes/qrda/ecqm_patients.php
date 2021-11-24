<?php


require_once(dirname(__FILE__) . "/ecqm_data.php");

class ECQMPatients extends ECQMData {

    public function __construct($patient_id = false, $pro_id = false, $CMS_ID = false) {
        
        $this->patient_id = $patient_id;
        $this->CMS_ID = $CMS_ID;
        $this->pro_id = $pro_id;
        
        parent::__construct();
        
    }

    public function patient_data() {
        $rowPatient=array();
        $qry = "select patient_data.*,users.fname as ptProviderFName,users.mname as ptProviderMName,users.lname as ptProviderLName,users.user_npi as ptProviderNPI,
        refferphysician.Title as ptRefferPhyTitle,refferphysician.FirstName as ptRefferPhyFName,refferphysician.MiddleName as ptRefferPhyMName,
        refferphysician.LastName as ptRefferPhyLName,refferphysician.physician_phone as ptRefferPhyPhone
        from patient_data LEFT JOIN users on users.id = patient_data.providerID
        LEFT JOIN refferphysician ON refferphysician.physician_Reffer_id = patient_data.primary_care_id 
        where patient_data.id = '".$this->patient_id."' ";
        $rsPatient = imw_query($qry);
        
        if($rsPatient && imw_num_rows($rsPatient)==1) {
            $rowPatient = imw_fetch_assoc($rsPatient);
            
            $ecqm_v8_data=$this->ecqm_v8_data($this->CMS_ID);
            
            if($rowPatient['sex']!='') {
                $rowPatient['Gender']=$this->get_gender_ECQM_data($rowPatient['sex'],$ecqm_v8_data);
            }
            
            if($rowPatient['race']!='') {
                $rowPatient['Race']=$this->get_race_ECQM_data($rowPatient['race'],$ecqm_v8_data);
            }
            
            if($rowPatient['ethnicity']!='') {
                $rowPatient['Ethnicity']=$this->get_ethnicity_ECQM_data($rowPatient['ethnicity'],$ecqm_v8_data);
            }
            
            if($rowPatient['language']!='') {
                $rowPatient['Language']=$this->get_language_ECQM_data($rowPatient['language'],$ecqm_v8_data);
            }
            
        }
        return $rowPatient;
    }
    
    
    public function get_gender_ECQM_data($val,$ecqm_v8_data) {
        $val = trim($val);
        $arrGender = array(
            array("imw" => 'male', "code" => "M", "display_name" => "Male"),
            array("imw" => 'female', "code" => "F", "display_name" => "Female")
        );
        $arr = array();
        if ($val != "") {
            foreach ($arrGender as $row) {
                if (in_array($val, $row)) {
                    $code=$row['code'];
                    if( isset($ecqm_v8_data['AdministrativeGender'][$code]) ) {
                        $arr['Value_Set_OID']=$ecqm_v8_data['AdministrativeGender'][$code]['Value_Set_OID'];
                        $arr['Code_System']=$ecqm_v8_data['AdministrativeGender'][$code]['Code_System'];
                        $arr['Code']=$ecqm_v8_data['AdministrativeGender'][$code]['Code'];
                        $arr['Description']=$ecqm_v8_data['AdministrativeGender'][$code]['Description'];
                        $arr['Code_System_OID']=$ecqm_v8_data['AdministrativeGender'][$code]['Code_System_OID'];
                        $arr['CMS_ID']=$ecqm_v8_data['AdministrativeGender'][$code]['CMS_ID'];
                    }
                    break;
                } else {
                    $arr['Code'] = "UN";
                    $arr['Description'] = "Undifferentiated";
                }
            }
        }
        return $arr;
    }
    
    public function get_race_ECQM_data($race,$ecqm_v8_data) {
        $common_only=1;
        $race_arr=explode(',',$race);
        $arr = $data = array();
        foreach($race_arr as $race_row) {
            $race_row=trim($race_row);
            $qry = "select * From race WHERE `race_name` LIKE '$race_row' and ".($common_only ? "common_use = 1 " : "h_code <> '' ")."Order By if(h_code = '' or h_code is null,1,0),h_code";
            $sql = imw_query($qry);
            if( $sql && imw_num_rows($sql) > 0 ) {
                while( $row = imw_fetch_assoc($sql) ) {
                    if($row['race_name']=="Latin American"){ $row['cdc_code']='2178-2'; }
                    $data[$row['cdc_code']] = $row['race_name'];
                }
            } else {
                $data['2131-1'] = "Other Race";
            }
        }

        $temp=array();
        foreach($data as $code => $val) {
            if( isset($ecqm_v8_data['CDCREC'][$code]) && $ecqm_v8_data['CDCREC'][$code]['Value_Set_Name'] == 'Race' ) {
                $temp['Value_Set_OID']=$ecqm_v8_data['CDCREC'][$code]['Value_Set_OID'];
                $temp['Code_System']=$ecqm_v8_data['CDCREC'][$code]['Code_System'];
                $temp['Code']=$ecqm_v8_data['CDCREC'][$code]['Code'];
                $temp['Description']=$ecqm_v8_data['CDCREC'][$code]['Description'];
                $temp['Code_System_OID']=$ecqm_v8_data['CDCREC'][$code]['Code_System_OID'];
                $temp['CMS_ID']=$ecqm_v8_data['CDCREC'][$code]['CMS_ID'];
                
                //$arr[$temp['Code']]=$temp;
                $arr=$temp;
                break;
            }
        }

        return $arr;
    }
    
    public function get_ethnicity_ECQM_data($ethnicity,$ecqm_v8_data) {
        $common_only=1;
        $ethnicity_arr=explode(',',$ethnicity);
        $arr = $data = array();
        foreach($ethnicity_arr as $ethnicity_row) {
            $ethnicity_row=trim($ethnicity_row);
            $qry = "select * From ethnicity WHERE `ethnicity_name` LIKE '$ethnicity_row' and ".($common_only ? "common_use = 1 " : "h_code <> '' ")."Order By if(h_code = '' or h_code is null,1,0),h_code";
            $sql = imw_query($qry);
            if( $sql && imw_num_rows($sql) > 0 ) {
                while( $row = imw_fetch_assoc($sql) ) {
                    $data[$row['cdc_code']] = $row['ethnicity_name'];
                }
            } 
        }

        $temp=array();
        foreach($data as $code => $val) {
            if( isset($ecqm_v8_data['CDCREC'][$code]) && $ecqm_v8_data['CDCREC'][$code]['Value_Set_Name'] == 'Ethnicity' ) {
                $temp['Value_Set_OID']=$ecqm_v8_data['CDCREC'][$code]['Value_Set_OID'];
                $temp['Code_System']=$ecqm_v8_data['CDCREC'][$code]['Code_System'];
                $temp['Code']=$ecqm_v8_data['CDCREC'][$code]['Code'];
                $temp['Description']=$ecqm_v8_data['CDCREC'][$code]['Description'];
                $temp['Code_System_OID']=$ecqm_v8_data['CDCREC'][$code]['Code_System_OID'];
                $temp['CMS_ID']=$ecqm_v8_data['CDCREC'][$code]['CMS_ID'];
                
                //$arr[$temp['Code']]=$temp;
                $arr=$temp;
                break;
            }
        }

        return $arr;
    }
    
    public function get_language_ECQM_data($language,$ecqm_v8_data) {
        $val = trim($language);
        $common_only=1;
        $arr = array();
		$qry = "select lang_name, if(iso_639_1_code = '', iso_639_2_B_code, iso_639_1_code) as lang_code  From languages Where lang_name LIKE '$language' ".($common_only ? "and common_use = 1 " : "")."Order By lang_name='Other' Asc, lang_name='Declined to Specify' Asc, lang_name ASc ";
		if( $qry) {
			$sql = imw_query($qry);
			$cnt = imw_num_rows($sql);
			if( $cnt > 0 ){
				while( $row = imw_fetch_assoc($sql) ){
					$arr['lang_code'] = $row['lang_code'];
                    $arr['lang_name'] = $row['lang_name'];
                    break;
				}
			} else {
                $arr['lang_code'] = "";
                $arr['lang_name'] = "";
            }
		}
        
        return $arr;
    }
    
    /*
     * Start Patient Characteristic Payer
     */
    public function ptCharacteristicPayer() {
        $payerArr=array();
        $payerQry = "Select * From patientPayer Where pid = '".$this->patient_id."' ";
        $payerSql = imw_query($payerQry);
        if( $payerSql && imw_num_rows($payerSql)> 0 ) {
            
            $ecqm_v8_data=$this->ecqm_v8_data($this->CMS_ID);
            
            while( $payerRow = imw_fetch_assoc($payerSql) ) {
                $code=trim($payerRow['valueCode']);
                if( isset($ecqm_v8_data['SOP'][$code]) && $ecqm_v8_data['SOP'][$code]['Value_Set_Name'] == 'Payer' ) {
                    $payerRow['Value_Set_OID']=$ecqm_v8_data['SOP'][$code]['Value_Set_OID'];
                    $payerRow['Code_System']=$ecqm_v8_data['SOP'][$code]['Code_System'];
                    $payerRow['Code']=$ecqm_v8_data['SOP'][$code]['Code'];
                    $payerRow['Description']=$ecqm_v8_data['SOP'][$code]['Description'];
                    $payerRow['Code_System_OID']=$ecqm_v8_data['SOP'][$code]['Code_System_OID'];
                    $payerRow['CMS_ID']=$ecqm_v8_data['SOP'][$code]['CMS_ID'];
                    $payerRow['Value_Set_Name']=$ecqm_v8_data['SOP'][$code]['Value_Set_Name'];
                    
                    $payerArr[$payerRow['Code']]=$payerRow;
                }
            }
        }
        
        return $payerArr;
    }
    /*
     * End Patient Characteristic Payer
     */ 
    
    
        
    /* Start Assessments */
    public function pt_assessment_data() {
        $pt_assessment_data=array();
        $sql = "SELECT *, hc_observations.snomed_code as lionic_code, hc_rel_observations.snomed_code AS scode FROM  hc_observations
            LEFT JOIN hc_rel_observations ON hc_rel_observations.observation_id = hc_observations.id 
            WHERE pt_id = '".$this->patient_id."' AND type=1 AND hc_observations.del_status=0";
        $result_hc = imw_query($sql);
        if ($result_hc && imw_num_rows($result_hc)>0) {
            $ecqm_v8_data=$this->ecqm_v8_data($this->CMS_ID);
            
            while ($row_hc = imw_fetch_assoc($result_hc)) {
                $refusal = ($row_hc['refusal'] && $row_hc['refusal_snomed']) ? true : false;
                
                $temp1=$temp2=$temp3=array();
                $lionic_code=trim($row_hc['lionic_code']);
                if(isset($ecqm_v8_data['LOINC'][$lionic_code])) {
                    $temp2['Value_Set_OID']=$ecqm_v8_data['LOINC'][$lionic_code]['Value_Set_OID'];
                    $temp2['Code_System']=$ecqm_v8_data['LOINC'][$lionic_code]['Code_System'];
                    $temp2['Code']=$ecqm_v8_data['LOINC'][$lionic_code]['Code'];
                    $temp2['Description']=$ecqm_v8_data['LOINC'][$lionic_code]['Description'];
                    $temp2['Code_System_OID']=$ecqm_v8_data['LOINC'][$lionic_code]['Code_System_OID'];
                    $temp2['CMS_ID']=$ecqm_v8_data['LOINC'][$lionic_code]['CMS_ID'];
                    $temp2['Value_Set_Name']=$ecqm_v8_data['LOINC'][$lionic_code]['Value_Set_Name'];
                }
                $row_hc['assess_loinic']=$temp2;
                
                $scode=trim($row_hc['scode']);
                if(isset($ecqm_v8_data['SNOMEDCT'][$scode])) {
                    $temp3['Value_Set_OID']=$ecqm_v8_data['SNOMEDCT'][$scode]['Value_Set_OID'];
                    $temp3['Code_System']=$ecqm_v8_data['SNOMEDCT'][$scode]['Code_System'];
                    $temp3['Code']=$ecqm_v8_data['SNOMEDCT'][$scode]['Code'];
                    $temp3['Description']=$ecqm_v8_data['SNOMEDCT'][$scode]['Description'];
                    $temp3['Code_System_OID']=$ecqm_v8_data['SNOMEDCT'][$scode]['Code_System_OID'];
                    $temp3['CMS_ID']=$ecqm_v8_data['SNOMEDCT'][$scode]['CMS_ID'];
                    $temp3['Value_Set_Name']=$ecqm_v8_data['SNOMEDCT'][$scode]['Value_Set_Name'];
                }
                $row_hc['assess_snomed']=$temp3;
                    
                if($refusal) {
                    $refusal_snomed=trim($row_hc['refusal_snomed']);
                    if(isset($ecqm_v8_data['SNOMEDCT'][$refusal_snomed])) {
                        $temp1['Value_Set_OID']=$ecqm_v8_data['SNOMEDCT'][$refusal_snomed]['Value_Set_OID'];
                        $temp1['Code_System']=$ecqm_v8_data['SNOMEDCT'][$refusal_snomed]['Code_System'];
                        $temp1['Code']=$ecqm_v8_data['SNOMEDCT'][$refusal_snomed]['Code'];
                        $temp1['Description']=$ecqm_v8_data['SNOMEDCT'][$refusal_snomed]['Description'];
                        $temp1['Code_System_OID']=$ecqm_v8_data['SNOMEDCT'][$refusal_snomed]['Code_System_OID'];
                        $temp1['CMS_ID']=$ecqm_v8_data['SNOMEDCT'][$refusal_snomed]['CMS_ID'];
                        $temp1['Value_Set_Name']=$ecqm_v8_data['SNOMEDCT'][$refusal_snomed]['Value_Set_Name'];
                    }
                }
                $row_hc['assess_refusal']=$temp1;

                $pt_assessment_data[]=$row_hc;
            }
        }
        return $pt_assessment_data;
        
    }
    /* End Assessments */
    
    
    
    
    
    
    /* BEGIN PATIENT DATA  xml content*/
    function get_patient_data_xml($patient_data=array()) {
        $XMLpatient_data = '<!-- reported patient --><recordTarget>';
        $XMLpatient_data .= '<patientRole>';
        if ($patient_data['ss'] != "") {
            $XMLpatient_data .= '<id extension="' . $patient_data['ss'] . '" root="2.16.840.1.113883.4.572"/>';
            $XMLpatient_data .= '<id extension="' . $patient_data['ss'] . '" root="1.3.6.1.4.1.115"/>';
        } else {
            $XMLpatient_data .= '<id root="2.16.840.1.113883.4.572"/>';
            $XMLpatient_data .= '<id root="1.3.6.1.4.1.115"/>';
        }
        $XMLpatient_data .= '<addr use="HP">';
        if ($patient_data['street'] != "") {
            $XMLpatient_data .= '<streetAddressLine>' . $patient_data['street'] . '</streetAddressLine>';
        } else {
            $XMLpatient_data .= '<streetAddressLine nullFlavor="NI"/>';
        }
        if ($patient_data['city'] != "") {
            $XMLpatient_data .= '<city>' . $patient_data['city'] . '</city>';
        } else {
            $XMLpatient_data .= '<city nullFlavor="NI"/>';
        }
        if ($patient_data['state'] != "") {
            $XMLpatient_data .= '<state>' . $patient_data['state'] . '</state>';
        } else {
            $XMLpatient_data .= '<state nullFlavor="NI"/>';
        }
        if ($patient_data['postal_code'] != "") {
            $XMLpatient_data .= '<postalCode>' . $patient_data['postal_code'] . '</postalCode>';
        } else {
            $XMLpatient_data .= '<postalCode nullFlavor="NI"/>';
        }
        $XMLpatient_data .= '<country>' . $patient_data['country_code'] . '</country>';
        $XMLpatient_data .= '</addr>';

        if ($patient_data['phone_home'] != "") {
            $XMLpatient_data .= '<telecom value="tel:+1-' . core_phone_format($patient_data['phone_home']) . '" use="HP"/>';
        } else {
            $XMLpatient_data .= '<telecom nullFlavor="NI" use="HP"/>';
        }
        if ($patient_data['phone_biz'] != "") {
            $XMLpatient_data .= '<telecom value="tel:+1-' . core_phone_format($patient_data['phone_biz']) . '" use="WP"/>';
        }
        if ($patient_data['phone_cell'] != "") {
            $XMLpatient_data .= '<telecom value="tel:+1-' . core_phone_format($patient_data['phone_cell']) . '" use="MC"/>';
        }

        $XMLpatient_data .= '<patient>';
        $XMLpatient_data .= '<name>
                                <given>' . $patient_data['fname'] . '</given>	
                                <family>' . $patient_data['lname'] . '</family>
                            </name>';

        if (isset($patient_data['Gender']) && empty($patient_data['Gender'])==false) {
            $Value_Set_OID=$patient_data['Gender']['Value_Set_OID'];
            $Code_System=$patient_data['Gender']['Code_System'];
            $Code=$patient_data['Gender']['Code'];
            $Description=$patient_data['Gender']['Description'];
            $Code_System_OID=$patient_data['Gender']['Code_System_OID'];

            $XMLpatient_data .= '<administrativeGenderCode code="'.$Code.'" codeSystem="'.$Code_System_OID.'" codeSystemName="'.$Code_System.'"  displayName="'.$Description.'"/>';
        } else {
            $XMLpatient_data .= '<administrativeGenderCode nullFlavor="NI"/>';
        }

        $dob = str_replace("-", "", $patient_data['DOB']);
        if ($dob != "00000000") {
            $XMLpatient_data .= '<birthTime value="' . $dob . '"/>';
        } else {
            $XMLpatient_data .= '<birthTime nullFlavor="NI"/>';
        }

        if (isset($patient_data['Race']) && empty($patient_data['Race'])==false) {
            $Value_Set_OID=$patient_data['Race']['Value_Set_OID'];
            $Code_System=$patient_data['Race']['Code_System'];
            $Code=$patient_data['Race']['Code'];
            $Description=$patient_data['Race']['Description'];
            $Code_System_OID=$patient_data['Race']['Code_System_OID'];

            $XMLpatient_data .= '<raceCode code="'.$Code.'"  codeSystemName="'.$Code_System.'" codeSystem="'.$Code_System_OID.'" displayName="'.$Description.'"/>';
        } else {
            $XMLpatient_data .= '<raceCode nullFlavor="NI"/>';
        }

        if (isset($patient_data['Ethnicity']) && empty($patient_data['Ethnicity'])==false) {
            $Value_Set_OID=$patient_data['Ethnicity']['Value_Set_OID'];
            $Code_System=$patient_data['Ethnicity']['Code_System'];
            $Code=$patient_data['Ethnicity']['Code'];
            $Description=$patient_data['Ethnicity']['Description'];
            $Code_System_OID=$patient_data['Ethnicity']['Code_System_OID'];

            $XMLpatient_data .= '<ethnicGroupCode code="'.$Code.'"  codeSystemName="'.$Code_System.'" codeSystem="'.$Code_System_OID.'" displayName="'.$Description.'"/>';
        } else {
            $XMLpatient_data .= '<ethnicGroupCode nullFlavor="NI"/>';
        }

        if (isset($patient_data['Language']) && empty($patient_data['Language'])==false) {
            $Code=$patient_data['Language']['lang_code'];
            $Description=$patient_data['Language']['lang_name'];
            $XMLpatient_data .= '<languageCommunication>
                        <templateId root="2.16.840.1.113883.3.88.11.83.2" assigningAuthorityName="HITSP/C83"/>
                        <templateId root="1.3.6.1.4.1.19376.1.5.3.1.2.1" assigningAuthorityName="IHE/PCC"/>
                        <languageCode code="' . $Code . '"/>
                    </languageCommunication>';
        }

        $XMLpatient_data .= '</patient>';
        $XMLpatient_data .= '</patientRole>';
        $XMLpatient_data .= '</recordTarget>';

        return $XMLpatient_data;
    }
    /* END PATIENT DATA */
        
    
    //Patient characteristic payer xml data
    public function get_ptCharacteristicPayer_xml($ptCharacteristicPayer) {
        $XML_patient_payer='';
        foreach( $ptCharacteristicPayer as $payerRow ) {
            if(isset($payerRow['Value_Set_OID']) && $payerRow['Value_Set_OID']!='') {
                $Value_Set_OID=$payerRow['Value_Set_OID'];
                $Code_System=$payerRow['Code_System'];
                $Code=$payerRow['Code'];
                $Description=$payerRow['Description'];
                $Code_System_OID=$payerRow['Code_System_OID'];
                $CMS_ID=$payerRow['CMS_ID'];

                $payerStartDate = (getNumber($payerRow['EffStart']) == '00000000000000') ? '' : getNumber($payerRow['EffStart']);
                $payerEndDate = (getNumber($payerRow['EffEnd']) == '00000000000000') ? '' : getNumber($payerRow['EffEnd']);

                $XML_patient_payer .= '
                    <entry>
                    <!-- Patient Characteristic Payer -->
                    <observation classCode="OBS" moodCode="EVN">
                    <templateId root="2.16.840.1.113883.10.20.24.3.55"/>
                    <id root="1.3.6.1.4.1.115" extension="5a2567364210bb04f2a3789a"/>
                    <code code="'.$payerRow['payer'].'" codeSystemName="LOINC" codeSystem="2.16.840.1.113883.6.1" displayName="Payment source"/> 
                    <statusCode code="completed"/>
                    <effectiveTime>
                    <!-- Attribute: Start Datetime -->
                    <low value="'.$payerStartDate.'"/>';
                    if( $payerEndDate ) {
                        $XML_patient_payer .= '<high value="'.$payerEndDate.'"/>';
                    } else {
                        $XML_patient_payer .='<high nullFlavor="UNK"/>';
                    }
                $XML_patient_payer .= '</effectiveTime>
                        <value code="'.$Code.'" codeSystem="'.$Code_System_OID.'" codeSystemName="'.$Code_System.'" xsi:type="CD" />
                    </observation>
                </entry>';
            }

        }

        return $XML_patient_payer;
    }
    //END Patient characteristic payer xml data
    
    
    
    /* Start Assessments */
    public function get_pt_assessment_xml($pt_assessment_data) {
        $XML_physical_exams='';
        $ext_counter=950;
        foreach ($pt_assessment_data as $row_hc) {
            if( (isset($row_hc['assess_loinic']) && empty($row_hc['assess_loinic'])==false && 
                    isset($row_hc['assess_snomed']) && empty($row_hc['assess_snomed'])==false) || 
                    isset($row_hc['assess_refusal']) && empty($row_hc['assess_refusal'])==false ) {
                $refusal = ($row_hc['refusal'] && $row_hc['refusal_snomed']) ? 'true' : '';

                if(isset($row_hc['assess_loinic']) && empty($row_hc['assess_loinic'])==false) {
                    $loinic_Value_Set_OID=$row_hc['assess_loinic']['Value_Set_OID'];
                    $loinic_Code_System=$row_hc['assess_loinic']['Code_System'];
                    $loinic_Code=$row_hc['assess_loinic']['Code'];
                    $loinic_Description=$row_hc['assess_loinic']['Description'];
                    $loinic_Code_System_OID=$row_hc['assess_loinic']['Code_System_OID'];
                    $loinic_CMS_ID=$row_hc['assess_loinic']['CMS_ID'];
                    $loinic_Value_Set_Name=$row_hc['assess_loinic']['Value_Set_Name'];
                }

                $XML_physical_exams .= '<entry>
                    <observation classCode="OBS" moodCode="EVN" ' . ($refusal ? 'negationInd="' . $refusal . '"' : '' ) . ' >
                    <!-- Assessment Performed -->
                    <templateId root="2.16.840.1.113883.10.20.24.3.144" extension="2017-08-01" />
                    <id root="1.3.6.1.4.1.115" extension="5a23952fcde4a30022ccf' . $ext_counter++ . '"/>';
                if ($refusal) {
                    $XML_physical_exams .= '
                        <code nullFlavor="NA" sdtc:valueSet="2.16.840.1.113883.3.526.3.1278"/>';
                } else {
                    $XML_physical_exams .= '<code code="'.$loinic_Code.'" codeSystem="' . $loinic_Code_System_OID . '" codeSystemName="'.$loinic_Code_System.'" />';
                }
                $XML_physical_exams .= '
                    <text>Tobacco Use Screening</text>
                    <statusCode code="completed"/>';

                if(isset($row_hc['assess_snomed']) && empty($row_hc['assess_snomed'])==false) {
                    $snomed_Value_Set_OID=$row_hc['assess_snomed']['Value_Set_OID'];
                    $snomed_Code_System=$row_hc['assess_snomed']['Code_System'];
                    $snomed_Code=$row_hc['assess_snomed']['Code'];
                    $snomed_Description=$row_hc['assess_snomed']['Description'];
                    $snomed_Code_System_OID=$row_hc['assess_snomed']['Code_System_OID'];
                    $snomed_CMS_ID=$row_hc['assess_snomed']['CMS_ID'];
                    $snomed_Value_Set_Name=$row_hc['assess_snomed']['Value_Set_Name'];
                }

                if ($refusal) {
                    $XML_physical_exams .= '<value xsi:type="CD" nullFlavor="UNK"/>';
                } else {
                    $XML_physical_exams .= '<value code="'.$snomed_Code.'" codeSystem="' . $snomed_Code_System_OID . '" xsi:type="CD" codeSystemName="'.$snomed_Code_System.'" />';
                }
                
                $XML_physical_exams .= '<!-- QDM Attribute: Author dateTime -->
                            <author>
                                <templateId root="2.16.840.1.113883.10.20.24.3.155" extension="2017-08-01"/>
                                <time value="'.str_replace('-', '', $row_hc['observation_date']).str_replace(':', '', $row_hc['observation_time']).'"/>
                                <assignedAuthor>
                                    <id nullFlavor="NA"/>
                                </assignedAuthor>
                            </author>';

                if ($refusal) {
                    if(isset($row_hc['assess_refusal']) && empty($row_hc['assess_refusal'])==false) {
                        $refusal_Value_Set_OID=$row_hc['assess_refusal']['Value_Set_OID'];
                        $refusal_Code_System=$row_hc['assess_refusal']['Code_System'];
                        $refusal_Code=$row_hc['assess_refusal']['Code'];
                        $refusal_Description=$row_hc['assess_refusal']['Description'];
                        $refusal_Code_System_OID=$row_hc['assess_refusal']['Code_System_OID'];
                        $refusal_CMS_ID=$row_hc['assess_refusal']['CMS_ID'];
                        $refusal_Value_Set_Name=$row_hc['assess_refusal']['Value_Set_Name'];
                    }
                    $XML_physical_exams .= '
                    <entryRelationship typeCode="RSON">
                        <observation classCode="OBS" moodCode="EVN">
                            <templateId root="2.16.840.1.113883.10.20.24.3.88" extension="2017-08-01"/>
                            <id root="1.3.6.1.4.1.115" extension="76008024C6FE667D7BB03E61C8825FC8"/>
                            <code code="77301-0" codeSystem="2.16.840.1.113883.6.1" displayName="reason" codeSystemName="LOINC"/>
                            <statusCode code="completed"/>
                            <value code="'.$refusal_Code.'" codeSystem="'.$refusal_Code_System_OID.'" codeSystemName="'.$refusal_Code_System.'" xsi:type="CD"/>
                        </observation>
                    </entryRelationship>';
                }

                $XML_physical_exams .= '
                    </observation>
                </entry>';
            }
        }
        
        return $XML_physical_exams;
    }
    /* End Assessments */
    
    
    /* Start Referral Loop */
    public function get_referral_loop_data() {
        $pt_referral_data= $temp1 = $temp2 = array();
        $q1 = "SELECT cmt.id as form_id,cmt.patient_id as patient_id,cap.refer_to_id as refer_to_id,cap.doctorName_id as doctorName_id, 
                cap.refer_to_code as refer_to_code, cmt.date_of_service as date_of_service,cmt.time_of_service as time_of_service 
                FROM chart_master_table cmt 
                JOIN chart_assessment_plans cap ON (cmt.id=cap.form_id) 
                WHERE cap.refer_to_id != '0' 
				AND cmt.delete_status='0' AND cmt.purge_status='0' 
				AND cmt.patient_id = '".$this->patient_id."' ";
		$res1 = imw_query($q1);
		if($res1 && imw_num_rows($res1)){
            $ecqm_v8_data=$this->ecqm_v8_data($this->CMS_ID);
            
            $form_ID_arr = array();
			while($rs1 = imw_fetch_assoc($res1)){
				$form_ID_arr[] 	= $rs1['form_id'];
                $temp=array();
                if(isset($rs1['refer_to_code']) && empty($rs1['refer_to_code'])==false) {
                    $refer_to_code_arr = explode('-(',$rs1['refer_to_code']);
                    $refer_to_code = substr($refer_to_code_arr[1], 0, -1);
                    if(isset($ecqm_v8_data['SNOMEDCT'][$refer_to_code])) {
                        $temp['Value_Set_OID']=$ecqm_v8_data['SNOMEDCT'][$refer_to_code]['Value_Set_OID'];
                        $temp['Code_System']=$ecqm_v8_data['SNOMEDCT'][$refer_to_code]['Code_System'];
                        $temp['Code']=$ecqm_v8_data['SNOMEDCT'][$refer_to_code]['Code'];
                        $temp['Description']=$ecqm_v8_data['SNOMEDCT'][$refer_to_code]['Description'];
                        $temp['Code_System_OID']=$ecqm_v8_data['SNOMEDCT'][$refer_to_code]['Code_System_OID'];
                        $temp['CMS_ID']=$ecqm_v8_data['SNOMEDCT'][$refer_to_code]['CMS_ID'];
                        $temp['Value_Set_Name']=$ecqm_v8_data['SNOMEDCT'][$refer_to_code]['Value_Set_Name'];
                    }
                }
                $rs1['inter_snomed']=$temp;
                
                $temp1[$rs1['form_id']] = $rs1;
			}
            $total_formIDs = implode(',',$form_ID_arr);
			          
			$q2="SELECT dma.form_id, DATE_FORMAT(dml.entered_date_time, '%Y-%m-%d') as dos, DATE_FORMAT(dml.entered_date_time, '%H:%i:%s') as tos
                FROM direct_messages dm 
                JOIN direct_messages_attachment dma ON (dm.id=dma.direct_message_id) 
                JOIN direct_messages_log dml ON (dml.updox_message_id = dm.MID) 
                WHERE dm.folder_type = '3' 
                AND dma.patient_id = '".$this->patient_id."'
                AND dma.form_id IN ($total_formIDs) 
                AND LOWER(dml.status)='dispatched'";
            $res2 = imw_query($q2);
            if($res2 && imw_num_rows($res2)){
                while($rs2 = imw_fetch_assoc($res2)){
                    $temp1[$rs2['form_id']]['consultant_report'] = $rs2;
                    $temp1[$rs2['form_id']]['dos'] = $rs2['dos'];
                    $temp1[$rs2['form_id']]['tos'] = $rs2['tos'];
                }
            }
        }
        $pt_referral_data['referral_data']=$temp1;

        return $pt_referral_data;
    }
    
    
    
    public function get_referral_loop_xml($pt_referral_data=array(), $pt_interventions_data=array() ) {

        $XML_referral_loop=$refer_to_id_hash='';
        if(empty($pt_referral_data['referral_data'])==false) {
            $counter=0;
            $ext_counter=180;
            foreach($pt_referral_data['referral_data'] as $form_id => $row) {
                $ext_counter++;
                $exp_sa_app_start_date = str_replace('-', '', $row['dos']);
                $low_val = $exp_sa_app_start_date . str_replace(':', '', $row['tos']);
                
                $refer_to_id_hash=($row['refer_to_id']>0)?md5($row['refer_to_id']):'';
                $consultant_report=(isset($row['consultant_report']) && empty($row['consultant_report'])==false)?count($row['consultant_report']):0;
                //Clinical consultation report
                if($consultant_report > 0) {
                    $XML_referral_loop.='
                    <entry>
                        <act classCode="ACT" moodCode="EVN" >
                            <templateId root="2.16.840.1.113883.10.20.24.3.156" extension="2018-10-01"/>
                            <id root="1.3.6.1.4.1.115" extension="5d95cfaedfe4bd034835f'.$ext_counter.'"/>
                            <!-- QDM Attribute: Category -->
                            <code nullFlavor="UNK"/>
                            <statusCode code="completed"/>
                            <!-- QDM Attribute: Relevant Period -->
                            <effectiveTime>
                                <low value="'.$low_val.'"/>
                                <high nullFlavor="UNK"/>
                            </effectiveTime>
                            <!-- QDM Attribute: Author dateTime -->
                            <author>
                                <templateId root="2.16.840.1.113883.10.20.24.3.155" extension="2017-08-01"/>
                                <time value="'.$low_val.'"/>
                                <assignedAuthor>
                                    <id nullFlavor="NA"/>
                                </assignedAuthor>
                            </author>                    
                            <entryRelationship typeCode="REFR">
                                <observation classCode="OBS" moodCode="EVN">
                                    <templateId root="2.16.840.1.113883.10.20.24.3.88" extension="2014-12-01"/>
                                    <id root="1.3.6.1.4.1.115" extension="5d95cfaedfe4bd034835f'.$ext_counter.'" />
                                    <code code="77301-0" codeSystem="2.16.840.1.113883.6.1" displayName="reason" codeSystemName="LOINC"/>
                                    <statusCode code="completed"/>
                                    <!-- QDM Attribute: Code -->
                                    <value xsi:type="CD" code="11488-4" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC">
                                        <translation code="371530004" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMEDCT"/>
                                    </value>
                                </observation>
                            </entryRelationship>
                            <!-- QDM Attribute: relatedTo -->
                            <sdtc:inFulfillmentOf1 typeCode="FLFS">
                                <sdtc:templateId root="2.16.840.1.113883.10.20.24.3.150" extension="2017-08-01"/>
                                <sdtc:actReference classCode="ACT" moodCode="EVN">
                                    <sdtc:id root="1.3.6.1.4.1.115" extension="'.$refer_to_id_hash.'"/>
                                </sdtc:actReference>
                            </sdtc:inFulfillmentOf1>                
                        </act>
                    </entry>
                    ';
                }
                
                /* BEGIN intervention SECTION */
                if($refer_to_id_hash!='' && empty($row['inter_snomed'])==false) {
                    //$XML_referral_loop.=$this->get_referral_intervention_data_xml($pt_interventions_data,$refer_to_id_hash);
                    $ext=($refer_to_id_hash!='')?$refer_to_id_hash:'5a23952dcde4a3001567c'.$ext_counter++;
                    $template_id1 = '2.16.840.1.113883.10.20.22.4.12';
                    $template_id2 = '2.16.840.1.113883.10.20.24.3.32';
                    $moodCode = 'EVN';
                    $status = 'completed';
                    
                    $start_date = date('YmdHis', strtotime($row['date_of_service'] . ' ' . $row['time_of_service']));
                    $end_date = date('YmdHis', strtotime($row['date_of_service'] . ' ' . $row['time_of_service']));
                    

                    $inter_snomed=$row['inter_snomed'];
                    $snomed_Value_Set_OID=$inter_snomed['Value_Set_OID'];
                    $snomed_Code_System=$inter_snomed['Code_System'];
                    $snomed_Code=$inter_snomed['Code'];
                    $snomed_Description=$inter_snomed['Description'];
                    $snomed_Code_System_OID=$inter_snomed['Code_System_OID'];
                    $snomed_CMS_ID=$inter_snomed['CMS_ID'];
                    $snomed_Value_Set_Name=$inter_snomed['Value_Set_Name'];

                    $XML_referral_loop .= '<entry>
                    <act classCode="ACT" moodCode="' . $moodCode . '" >
                    <!-- Consolidation CDA: Procedure Activity Act template -->
                    <templateId root="' . $template_id1 . '" extension="2014-06-09"/>
                    <templateId root="' . $template_id2 . '" extension="2017-08-01"/>
                    <id root="1.3.6.1.4.1.115" extension="'.$ext.'" />
                    ';

                    if(empty($row['inter_snomed'])==false){
                        $XML_referral_loop .= '<code code="'.$snomed_Code.'" codeSystem="'.$snomed_Code_System_OID.'" codeSystemName="'.$snomed_Code_System.'" />';
                    } else {
                        $XML_referral_loop .= '<code nullFlavor="NA" />';
                    }

                    $XML_referral_loop .='
                    <text>'.$snomed_Value_Set_Name.'</text>
                    <statusCode code="' . $status . '"/>
                    <effectiveTime>
                        <low value="' . $start_date . '"/>
                        <high value="' . $end_date . '"/>
                    </effectiveTime>
                    <author>
                        <templateId root="2.16.840.1.113883.10.20.24.3.155" extension="2017-08-01"/>
                        <time value="' . $start_date . '"/>
                        <assignedAuthor><id nullFlavor="NI"/></assignedAuthor>
                    </author>';

                    $XML_referral_loop .= '</act></entry>';
                    
                }
                /* END intervention SECTION */
                
                $counter++;
            }

        }

        return $XML_referral_loop;
    }
    /* End Referral Loop */

    
    //filter code from message communication -: CMS142v8
    private function filterCommCode($data)
	{
		$data = trim($data);
		$returnData = array('commCode'=>'', 'message'=>'');
		
		$lastChar = substr($data, -1);
		if($lastChar == ")"){

            $commCode=$ircpCode=$autCode=$RefusalCode=array();
			$allCodes = preg_replace('/^(?:.*\()(.*)\)/D', '$1', $data);	/*Capture all codes from end*/
			$allCodes = preg_replace('/\s+/', '', $allCodes);	/*Replace space*/
			$allCodes = explode(',', $allCodes);	/*Split by Codes separator*/
            foreach($allCodes as $item) {
                if(strpos($item,'Communication-CM')!==false){
                    $comm_code=str_ireplace('Communication-CM', '', $item);
                    $commCode[]= trim($comm_code);
                }
                if(strpos($item,'IRCP-CM')!==false){
                    $ircp_code=str_ireplace('IRCP-CM', '', $item);
                    $ircpCode[]= trim($ircp_code);
                }
                if(strpos($item,'AUT-CM')!==false){
                    $aut_code=str_ireplace('AUT-CM', '', $item);
                    $autCode[]= trim($aut_code);
                }
                if(strpos($item,'Refusal-CM')!==false){
                    $refusal_code=str_ireplace('Refusal-CM', '', $item);
                    $RefusalCode[]= trim($refusal_code);
                }
            }
			$returnData['commCode'] = $commCode;
			$returnData['ircpCode'] = $ircpCode;
			$returnData['autCode'] = $autCode;
			$returnData['RefusalCode'] = $RefusalCode;
			$returnData['message'] = preg_replace('/\([^\(]*$/', '', $data);
            
		}
		return $returnData;
	}
       
    
    
    public function pt_communication_performed() {
        $pt_communication_performed=array();
        $sql = "SELECT user_message_id, message_subject, approved, message_to, message_text, message_sender_id, message_send_date, message_status, message_read_status, message_completed_date 
                FROM  user_messages
                WHERE patientId = '".$this->patient_id."' 
                AND Pt_Communication=1 
                AND del_status=0";
        $result_hc = imw_query($sql);
        if ($result_hc && imw_num_rows($result_hc)>0) {
            $ecqm_v8_data=$this->ecqm_v8_data($this->CMS_ID);

            while ($row = imw_fetch_assoc($result_hc)) {
                $temp1=$temp2=$temp3=$temp4=array();
                if($row['message_text']!='') {
                    $codesArr=$this->filterCommCode($row['message_text']);
                    $commCode=$codesArr['commCode'][0];
                    if(isset($ecqm_v8_data['SNOMEDCT'][$commCode])) {
                        $temp1['Value_Set_OID']=$ecqm_v8_data['SNOMEDCT'][$commCode]['Value_Set_OID'];
                        $temp1['Code_System']=$ecqm_v8_data['SNOMEDCT'][$commCode]['Code_System'];
                        $temp1['Code']=$ecqm_v8_data['SNOMEDCT'][$commCode]['Code'];
                        $temp1['Description']=$ecqm_v8_data['SNOMEDCT'][$commCode]['Description'];
                        $temp1['Code_System_OID']=$ecqm_v8_data['SNOMEDCT'][$commCode]['Code_System_OID'];
                        $temp1['CMS_ID']=$ecqm_v8_data['SNOMEDCT'][$commCode]['CMS_ID'];
                        $temp1['Value_Set_Name']=$ecqm_v8_data['SNOMEDCT'][$commCode]['Value_Set_Name'];
                    }
                    $row['comm_code']=$temp1;
                    
                    if($row['approved']=='accept'){
                        $ircpCode=$codesArr['ircpCode'][0];
                        if(isset($ecqm_v8_data['SNOMEDCT'][$ircpCode])) {
                            $temp2['Value_Set_OID']=$ecqm_v8_data['SNOMEDCT'][$ircpCode]['Value_Set_OID'];
                            $temp2['Code_System']=$ecqm_v8_data['SNOMEDCT'][$ircpCode]['Code_System'];
                            $temp2['Code']=$ecqm_v8_data['SNOMEDCT'][$ircpCode]['Code'];
                            $temp2['Description']=$ecqm_v8_data['SNOMEDCT'][$ircpCode]['Description'];
                            $temp2['Code_System_OID']=$ecqm_v8_data['SNOMEDCT'][$ircpCode]['Code_System_OID'];
                            $temp2['CMS_ID']=$ecqm_v8_data['SNOMEDCT'][$ircpCode]['CMS_ID'];
                            $temp2['Value_Set_Name']=$ecqm_v8_data['SNOMEDCT'][$ircpCode]['Value_Set_Name'];
                        }
                        $row['ircp_code']=$temp2;
                        
                        $autCode=$codesArr['autCode'][0];
                        if(isset($ecqm_v8_data['SNOMEDCT'][$autCode])) {
                            $temp3['Value_Set_OID']=$ecqm_v8_data['SNOMEDCT'][$autCode]['Value_Set_OID'];
                            $temp3['Code_System']=$ecqm_v8_data['SNOMEDCT'][$autCode]['Code_System'];
                            $temp3['Code']=$ecqm_v8_data['SNOMEDCT'][$autCode]['Code'];
                            $temp3['Description']=$ecqm_v8_data['SNOMEDCT'][$autCode]['Description'];
                            $temp3['Code_System_OID']=$ecqm_v8_data['SNOMEDCT'][$autCode]['Code_System_OID'];
                            $temp3['CMS_ID']=$ecqm_v8_data['SNOMEDCT'][$autCode]['CMS_ID'];
                            $temp3['Value_Set_Name']=$ecqm_v8_data['SNOMEDCT'][$autCode]['Value_Set_Name'];
                        }
                        $row['aut_code']=$temp3;
                        
                    }
                    if($row['approved']=='decline'){
                        $refusalCode=$codesArr['RefusalCode'][0];
                        if(isset($ecqm_v8_data['SNOMEDCT'][$refusalCode])) {
                            $temp4['Value_Set_OID']=$ecqm_v8_data['SNOMEDCT'][$refusalCode]['Value_Set_OID'];
                            $temp4['Code_System']=$ecqm_v8_data['SNOMEDCT'][$refusalCode]['Code_System'];
                            $temp4['Code']=$ecqm_v8_data['SNOMEDCT'][$refusalCode]['Code'];
                            $temp4['Description']=$ecqm_v8_data['SNOMEDCT'][$refusalCode]['Description'];
                            $temp4['Code_System_OID']=$ecqm_v8_data['SNOMEDCT'][$refusalCode]['Code_System_OID'];
                            $temp4['CMS_ID']=$ecqm_v8_data['SNOMEDCT'][$refusalCode]['CMS_ID'];
                            $temp4['Value_Set_Name']=$ecqm_v8_data['SNOMEDCT'][$refusalCode]['Value_Set_Name'];
                        }
                        $row['refusal_code']=$temp4;
                        
                    }
                    
                }
                
                $pt_communication_performed[]=$row;
            }
            
        }
        
        return $pt_communication_performed;
    }
    
    
    public function get_ptCommunicationPerformed_xml($pt_communication_performed) {
        $ptCommXml='';
        $ext_counter=970;
        foreach($pt_communication_performed as $row) {
            $ext_counter++;
            $message_send_date = date('YmdHis', strtotime($row['message_send_date']));
            
            $refusal = ($row['approved']=='decline') ? 'true' : '';
            
            $ptCommXml.='<entry>
                    <act classCode="ACT" moodCode="EVN" '.($refusal ? 'negationInd="'.$refusal.'"':'').'>
                        <templateId root="2.16.840.1.113883.10.20.24.3.156" extension="2018-10-01"/>
                        <id root="1.3.6.1.4.1.115" extension="5dc90434dfe4bd035949a'.$ext_counter.'"/>
                        <!-- QDM Attribute: Category -->
                        <code nullFlavor="NA"/>
                        <statusCode code="completed"/>';
            $ptCommXml.='<!-- QDM Attribute: Relevant Period -->
                    <effectiveTime>
                        <low value="'.$message_send_date.'"/>
                        <high nullFlavor="UNK"/>
                    </effectiveTime>';
            $ptCommXml.='<!-- QDM Attribute: Author dateTime -->
                    <author>
                        <templateId root="2.16.840.1.113883.10.20.24.3.155" extension="2017-08-01"/>
                        <time value="'.$message_send_date.'"/>
                        <assignedAuthor>
                            <id nullFlavor="NA"/>
                        </assignedAuthor>
                    </author>';

            if($refusal) {
                if(isset($row['refusal_code']) && empty($row['refusal_code'])==false) {
                    $refusal_code=$row['refusal_code'];
                    $ptCommXml.='<!-- QDM Attribute: Negation Rationale -->
                    <entryRelationship typeCode="RSON">
                        <observation classCode="OBS" moodCode="EVN">
                            <templateId root="2.16.840.1.113883.10.20.24.3.88" extension="2017-08-01"/>
                            <id root="1.3.6.1.4.1.115" extension="2d58b820-e67d-0137-2d1f-0eca209bc'.$ext_counter.'" />
                            <code code="77301-0" codeSystem="2.16.840.1.113883.6.1" displayName="reason" codeSystemName="LOINC"/>
                            <statusCode code="completed"/>
                            <effectiveTime>
                                <low value="'.$message_send_date.'"/>
                                <high nullFlavor="UNK"/>
                            </effectiveTime>
                            <value code="'.$refusal_code['Code'].'" codeSystem="'.$refusal_code['Code_System_OID'].'" codeSystemName="'.$refusal_code['Code_System'].'" xsi:type="CD"/>
                        </observation>
                    </entryRelationship>';
                }
            } else {
                if(isset($row['ircp_code']) && empty($row['ircp_code'])==false) {
                    $ircp_code=$row['ircp_code'];
                    $ptCommXml.='<participant typeCode="IRCP">
                        <participantRole>
                            <!-- QDM Attribute: Recipient -->
                            <code code="'.$ircp_code['Code'].'" codeSystem="'.$ircp_code['Code_System_OID'].'" codeSystemName="'.$ircp_code['Code_System'].'" displayName="'.$ircp_code['Value_Set_Name'].'"/>
                        </participantRole>
                    </participant>';
                }
                if(isset($row['aut_code']) && empty($row['aut_code'])==false) {
                    $aut_code=$row['aut_code'];
                    $ptCommXml.='<participant typeCode="AUT">
                        <participantRole>
                            <code code="'.$aut_code['Code'].'" codeSystem="'.$aut_code['Code_System_OID'].'" codeSystemName="'.$aut_code['Code_System'].'"/>
                        </participantRole>
                    </participant>';
                }
            }
            
            if(isset($row['comm_code']) && empty($row['comm_code'])==false) {
                $comm_code=$row['comm_code'];
                $ptCommXml.='<entryRelationship typeCode="REFR">
                            <observation classCode="OBS" moodCode="EVN">
                                <templateId root="2.16.840.1.113883.10.20.24.3.88" extension="2017-08-01"/>
                                <id root="1.3.6.1.4.1.115" extension="5dc90434dfe4bd035949a'.$ext_counter.'" />
                                <code code="77301-0" codeSystem="2.16.840.1.113883.6.1" displayName="reason" codeSystemName="LOINC"/>
                                <statusCode code="completed"/>
                                <!-- QDM Attribute: Code -->
                                <value xsi:type="CD" code="'.$comm_code['Code'].'" codeSystem="'.$comm_code['Code_System_OID'].'" codeSystemName="'.$comm_code['Code_System'].'"/>
                            </observation>
                        </entryRelationship>
                    </act>
                </entry>';
            }
        }
        return $ptCommXml;
    }
    
    
    
}

?>