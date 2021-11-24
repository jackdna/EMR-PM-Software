<?php

ob_start();
include_once(dirname(__FILE__) . "/../../../config/globals.php");
include_once(dirname(__FILE__) . "/class.mur_reports.php");
include(dirname(__FILE__) . "/../../../library/classes/AES.class.php");

$library_path = $GLOBALS['webroot'] . '/library';
$objMUR = new MUR_Reports;

$selectedNQF = explode(',', $_REQUEST['selectedNQF']);

$inc_all_nqf = "yes";
$currentDate = date("YmdHisO");
$dtfrom = $_REQUEST['dtfrom'];
$dtupto = $_REQUEST['dtupto'];
$dtfrom1 = getDateFormatDB($dtfrom);
$dtupto1 = getDateFormatDB($dtupto);
if ($_REQUEST['provider'] != "") {
    $pro_id = $_REQUEST['provider'];
} else {
    $pro_id = $_SESSION['authId'];
}
destroy("qrda_xml/qrda_cat1");
$ext_counter = "100";
//$all_nqf_arr = array("NQF0018", "NQF0022", "NQF0421", "CMS50v2", "NQF0028", "NQF0052", "NQF0055", "NQF0086", "NQF0088", "NQF0089");
$all_nqf_arr = array("NQF0018", "NQF0022", "NQF0419", "NQF0565", "NQF0028", "NQF0564", "NQF0055", "NQF0086", "NQF0088", "NQF0089");
$all_nqf_arr = $selectedNQF;


$rqfileName_arr = array();
//$rqfileName_arr[] = 'CDA.xsl';
$query = "SELECT group_concat(nqf_id) as con_nqf_id,patient_id from cqm_patients where patient_id>0 group by patient_id";
$main_qry = imw_query($query);
while ($main_row = imw_fetch_array($main_qry)) {
    $con_nqf_id_arr = explode(',', $main_row['con_nqf_id']);
    $pid = $main_row['patient_id'];

    $XMLpatient_data = "";
    $XML_author_data = "";
    $XML_custodian_data = "";
    $XML_leagalauth_data = "";
    $XML_documentationof_data = "";
    $XML_encouter_entry = "";
    $XML_pro_comm_entry = "";
    $XML_test_entry = "";
    $XML_intervention_entry = "";
    $XML_procedure_entry = "";
    $XML_smoking_status_entry = "";
    $XML_problem_section = "";
    $XML_problem_entry = "";
    $XML_physical_exams = "";
    $XML_CLUSTER_section = "";
    $XML_patient_payer = "";

    $qry = "select patient_data.*,users.fname as ptProviderFName,users.mname as ptProviderMName,users.lname as ptProviderLName,users.user_npi as ptProviderNPI,
	refferphysician.Title as ptRefferPhyTitle,refferphysician.FirstName as ptRefferPhyFName,refferphysician.MiddleName as ptRefferPhyMName,
	refferphysician.LastName as ptRefferPhyLName,refferphysician.physician_phone as ptRefferPhyPhone
	from patient_data LEFT JOIN users on users.id = patient_data.providerID
	LEFT JOIN refferphysician ON refferphysician.physician_Reffer_id = patient_data.primary_care_id 
	where patient_data.id = '" . $pid . "'";

    $rsPatient = imw_query($qry);
    $rowPatient = imw_fetch_assoc($rsPatient);

    /* BEGIN PATIENT DATA */
    $XMLpatient_data = '<!-- reported patient --><recordTarget>';
    $XMLpatient_data .= '<patientRole>';
    if ($rowPatient['ss'] != "") {
        $XMLpatient_data .= '<id extension="' . $rowPatient['ss'] . '" root="2.16.840.1.113883.4.572"/>';
        $XMLpatient_data .= '<id extension="' . $rowPatient['ss'] . '" root="1.3.6.1.4.1.115"/>';
    } else {
        $XMLpatient_data .= '<id root="2.16.840.1.113883.4.572"/>';
        $XMLpatient_data .= '<id root="1.3.6.1.4.1.115"/>';
    }
    $XMLpatient_data .= '<addr use="HP">';
    if ($rowPatient['street'] != "") {
        $XMLpatient_data .= '<streetAddressLine>' . $rowPatient['street'] . '</streetAddressLine>';
    } else {
        $XMLpatient_data .= '<streetAddressLine nullFlavor="NI"/>';
    }
    //if ($rowPatient['street2'] != "") {
    //     $XMLpatient_data .= '<streetAddressLine>' . $rowPatient['street2'] . '</streetAddressLine>';
    // }
    //else
    //$XMLpatient_data .= '<streetAddressLine nullFlavor="NI"/>';

    if ($rowPatient['city'] != "") {
        $XMLpatient_data .= '<city>' . $rowPatient['city'] . '</city>';
    } else {
        $XMLpatient_data .= '<city nullFlavor="NI"/>';
    }
    if ($rowPatient['state'] != "") {
        $XMLpatient_data .= '<state>' . $rowPatient['state'] . '</state>';
    } else {
        $XMLpatient_data .= '<state nullFlavor="NI"/>';
    }
    if ($rowPatient['postal_code'] != "") {
        $XMLpatient_data .= '<postalCode>' . $rowPatient['postal_code'] . '</postalCode>';
    } else {
        $XMLpatient_data .= '<postalCode nullFlavor="NI"/>';
    }
    $XMLpatient_data .= '<country>US</country>';

    $XMLpatient_data .= '</addr>';

    if ($rowPatient['phone_home'] != "") {
        $XMLpatient_data .= '<telecom value="tel:+1-' . core_phone_format($rowPatient['phone_home']) . '" use="HP"/>';
    } else {
        $XMLpatient_data .= '<telecom nullFlavor="NI" use="HP"/>';
    }
    if ($rowPatient['phone_biz'] != "") {
        $XMLpatient_data .= '<telecom value="tel:+1-' . core_phone_format($rowPatient['phone_biz']) . '" use="WP"/>';
    }
    if ($rowPatient['phone_cell'] != "") {
        $XMLpatient_data .= '<telecom value="tel:+1-' . core_phone_format($rowPatient['phone_cell']) . '" use="MC"/>';
    }

    $XMLpatient_data .= '<patient>';
    $XMLpatient_data .= '<name>
							<given>' . $rowPatient['fname'] . '</given>	
							<family>' . $rowPatient['lname'] . '</family>
						</name>';

    $arrGender = array();
    $arrGender = gender_srh(strtolower($rowPatient['sex']));
    if ($arrGender['code'] != "" && $arrGender['display_name'] != "") {
        $XMLpatient_data .= '<administrativeGenderCode code="' . $arrGender['code'] . '" codeSystem="2.16.840.1.113883.5.1" codeSystemName="HL7 AdministrativeGender" displayName="' . $arrGender['display_name'] . '"/>';
    } else {
        $XMLpatient_data .= '<administrativeGenderCode nullFlavor="NI"/>';
    }

    $dob = str_replace("-", "", $rowPatient['DOB']);
    if ($dob != "00000000") {
        $XMLpatient_data .= '<birthTime value="' . $dob . '"/>';
    } else {
        $XMLpatient_data .= '<birthTime nullFlavor="NI"/>';
    }

    $arrRace = array();
    $arrRace = race_srh(strtolower($rowPatient['race']));
    if ($arrRace['code'] != "" && $arrRace['display_name'] != "") {
        $XMLpatient_data .= '<raceCode code="' . $arrRace['code'] . '"  codeSystemName="CDC Race and Ethnicity" codeSystem="2.16.840.1.113883.6.238" displayName="' . $arrRace['display_name'] . '"/>';
    } else {
        $XMLpatient_data .= '<raceCode nullFlavor="NI"/>';
    }

    $arrEthnicity = array();
    $arrEthnicity = ethnicity_srh(strtolower($rowPatient['ethnicity']));
    if ($arrEthnicity['code'] != "" && $arrEthnicity['display_name'] != "") {
        $XMLpatient_data .= '<ethnicGroupCode code="' . $arrEthnicity['code'] . '"  codeSystemName="CDC Race and Ethnicity" codeSystem="2.16.840.1.113883.6.238" displayName="' . $arrEthnicity['display_name'] . '"/>';
    } else {
        $XMLpatient_data .= '<ethnicGroupCode nullFlavor="NI"/>';
    }

    $arrLanguage = array();
    $arrLanguage = language_srh(strtolower($rowPatient['language']));
    if ($arrLanguage['code'] != "" && $arrLanguage['display_name'] != "") {
        $XMLpatient_data .= '<languageCommunication>
                    <templateId root="2.16.840.1.113883.3.88.11.83.2" assigningAuthorityName="HITSP/C83"/>
                    <templateId root="1.3.6.1.4.1.19376.1.5.3.1.2.1" assigningAuthorityName="IHE/PCC"/>
                    <languageCode code="' . $arrLanguage['code'] . '"/>
                </languageCommunication>';
    }

    /*
      $arrMarried = array();
      $arrMarried = marr_status_srh(strtolower($rowPatient['status']));
      if ($arrMarried['code'] != "" && $arrMarried['display_name'] != "") {
      $XMLpatient_data .= '<maritalStatusCode code="' . $arrMarried['code'] . '" displayName="' . $arrMarried['display_name'] . '"
      codeSystem="2.16.840.1.113883.5.2"
      codeSystemName="MaritalStatusCode"/>';
      } */

    $XMLpatient_data .= '</patient>';
    $XMLpatient_data .= '</patientRole>';
    $XMLpatient_data .= '</recordTarget>';
    /* END PATIENT DATA */

    /* BEGIN AUTHOR DATA */
    $qry_user = "select * from users where id = '" . $pro_id . "'";
    $res_user = imw_query($qry_user);
    $row_user = imw_fetch_assoc($res_user);
    $XML_author_data = '<author>';
    $XML_author_data .= '<time value="' . $currentDate . '"/>';
    $XML_author_data .= '<assignedAuthor>';
    if ($row_user['user_npi'] != "")
        $XML_author_data .= '<id extension="' . $row_user['user_npi'] . '" root="2.16.840.1.113883.4.6"/>';
    else
        $XML_author_data .= '<id root="2.16.840.1.113883.4.6"/>';

    if ($row_user['facility'] > 0) {
        $qry_facility = "select name,phone,street,city,state,postal_code from facility where id = '" . $row_user['facility'] . "'";
    } else {
        $qry_facility = "select name,phone,street,city,state,postal_code from facility where facility_type = '1'";
    }
    $res_facility = imw_query($qry_facility);
    $row_facility = imw_fetch_assoc($res_facility);

    $XML_author_data .= '<addr use="WP">';
    if ($row_facility['street'] != "")
        $XML_author_data .= '<streetAddressLine>' . $row_facility['street'] . '</streetAddressLine>';
    if ($row_facility['city'] != "")
        $XML_author_data .= '<city>' . $row_facility['city'] . '</city>';
    if ($row_facility['state'] != "")
        $XML_author_data .= '<state>' . $row_facility['state'] . '</state>';
    if ($row_facility['postal_code'] != "")
        $XML_author_data .= '<postalCode>' . $row_facility['postal_code'] . '</postalCode>';

    $XML_author_data .= '<country>US</country>';
    $XML_author_data .= '</addr>';

    if ($row_facility['phone'] != "")
        $XML_author_data .= '<telecom use="WP" value="tel:+1-' . core_phone_format($row_facility['phone']) . '"/>';

    $XML_author_data .= '<assignedAuthoringDevice>
			<manufacturerModelName>imwemr</manufacturerModelName>
			<softwareName>imwemr</softwareName>
		  </assignedAuthoringDevice>';
    $XML_author_data .= '</assignedAuthor>';
    $XML_author_data .= '</author>';
    /* END AUTHOR DATA   */

    /* BEGIN CUSTODIAN (FACILITY) DATA */
    if ($facility > 0) {
        $qry_facility = "select name,phone,street,city,state,postal_code from facility where id = '" . $row_user['facility'] . "'";
    } else {
        $qry_facility = "select name,phone,street,city,state,postal_code from facility where facility_type = '1'";
    }
    $res_facility = imw_query($qry_facility);
    $row_facility = imw_fetch_assoc($res_facility);

    $XML_custodian_data = '<custodian>';
    $XML_custodian_data .= '<assignedCustodian>';
    $XML_custodian_data .= '<representedCustodianOrganization>';
    $XML_custodian_data .= '<id root="2.16.840.1.113883.19.5"/>';
    if ($row_facility['name'] != "")
        $XML_custodian_data .= '<name>' . $row_facility['name'] . '</name>';

    if ($row_facility['phone'] != "")
        $XML_custodian_data .= '<telecom use="WP" value="tel:+1-' . core_phone_format($row_facility['phone']) . '"/>';
    else
        $XML_custodian_data .= '<telecom nullFlavor="NI"/>';

    $XML_custodian_data .= '<addr>';
    if ($row_facility['street'] != "")
        $XML_custodian_data .= '<streetAddressLine>' . $row_facility['street'] . '</streetAddressLine>';
    if ($row_facility['city'] != "")
        $XML_custodian_data .= '<city>' . $row_facility['city'] . '</city>';
    if ($row_facility['state'] != "")
        $XML_custodian_data .= '<state>' . $row_facility['state'] . '</state>';
    if ($row_facility['postal_code'] != "")
        $XML_custodian_data .= '<postalCode>' . $row_facility['postal_code'] . '</postalCode>';
    $XML_custodian_data .= '<country>US</country>';
    $XML_custodian_data .= '</addr>';

    $XML_custodian_data .= '</representedCustodianOrganization>';
    $XML_custodian_data .= '</assignedCustodian>';
    $XML_custodian_data .= '</custodian>';
    /* END CUSTODIAN (FACILITY) DATA */

    $XML_leagalauth_data .= '<!-- This needs to take reporting program into account EH/EP-->
    
    <informationRecipient>
        <intendedRecipient>
            <id root="2.16.840.1.113883.3.249.7" extension="PQRS_MU_INDIVIDUAL"/>
        </intendedRecipient>
    </informationRecipient>';


    $XML_leagalauth_data .= '<legalAuthenticator>
    <time value="' . $currentDate . '"/>
    <signatureCode code="S"/>
    <assignedEntity>
      <id root="bc01a5d1-3a34-4286-82cc-43eb04c972a7"/>
      <addr>
        <streetAddressLine>' . $row_facility['street'] . '</streetAddressLine>
        <city>' . $row_facility['city'] . '</city>
        <state>' . $row_facility['state'] . '</state>
        <postalCode>' . $row_facility['postal_code'] . '</postalCode>
        <country>US</country>
      </addr>
      <telecom use="WP" value="tel:+1-' . core_phone_format($row_facility['phone']) . '"/>
      <assignedPerson>
        <name>
           <given>Arun</given>
           <family>Kapur</family>
        </name>
     </assignedPerson>
      <representedOrganization>
        <id root="2.16.840.1.113883.19.5"/>
        <name>imwemr</name>
      </representedOrganization>
    </assignedEntity>
  </legalAuthenticator>';

    $XML_leagalauth_data .= '<participant typeCode="DEV">
        <associatedEntity classCode="RGPR">
            <id root="2.16.840.1.113883.3.2074.1" extension="0014ABC1D1EFG1H"/>
        </associatedEntity>
    </participant>';

    /* BEGIN CARE TEAM MEMBERS */

    $XML_documentationof_data = '<documentationOf typeCode="DOC">';
    $XML_documentationof_data .= '<serviceEvent classCode="PCPR">';
    $XML_documentationof_data .= '<effectiveTime>
								 <low value="' . $currentDate . '"/>
								 </effectiveTime>';

    $XML_documentationof_data .= '<performer typeCode="PRF">';
    $XML_documentationof_data .= '<time>
								 <low value="' . $currentDate . '"/>
								 </time>';

    $XML_documentationof_data .= '<assignedEntity>';
    if ($row_user['user_npi'] != "")
        $XML_documentationof_data .= '<id extension="' . $row_user['user_npi'] . '" root="2.16.840.1.113883.4.6"/>';
    else
        $XML_documentationof_data .= '<id nullFlavor="NI"/>';

    $XML_documentationof_data .= '<id root="2.16.840.1.113883.4.336" extension="54321" /> ';
    $XML_documentationof_data .= ' <representedOrganization>
		<!-- This is the organization TIN -->
		<id root="2.16.840.1.113883.4.2" extension="1234567" /> 
		<!-- This is the organization CCN -->
		
	  </representedOrganization>';

    $XML_documentationof_data .= '</assignedEntity>';
    $XML_documentationof_data .= '</performer>';

    $XML_documentationof_data .= '</serviceEvent>';
    $XML_documentationof_data .= '</documentationOf>';
    /* END CARE TEAM MEMBERS */

    /* BEGIN ENOCUNTERS SECTION */
    $sql = "SELECT appt.id, appt.sa_app_start_date,sa_app_end_date,appt.sa_app_starttime,appt.sa_app_endtime, appt.is_inpatient
                    FROM schedule_appointments appt 
                    WHERE appt.sa_patient_id in('" . $pid . "')
			AND appt.sa_patient_app_status_id NOT IN(203,201,18,19,20,3)";
    $enc_qry = imw_query($sql);
    while ($enc_row = imw_fetch_array($enc_qry)) {
        $sa_app_start_date = $enc_row['sa_app_start_date'];
        $sa_app_starttime = $enc_row['sa_app_starttime'];
        $sa_app_end_date = $enc_row['sa_app_end_date'];
        $is_inpatient = $enc_row['is_inpatient'];
        
        /* Start inpatient fields data */
        $inp_data = array();
        $inp_qry = "Select * From inpatient_fields Where appt_id = '".$enc_row['id']."' Order By field_type ";
        $inp_sql = imw_query($inp_qry);
        $inp_cnt = imw_num_rows($inp_sql);
        if( $inp_cnt ) {
            while( $inp_row = imw_fetch_assoc($inp_sql) ) {
               $inp_data[$enc_row['id']][$inp_row['field_type']] = $inp_row;
            }
        }
        /* End inpatient fields data */
        
        $qry_sup = imw_query("select 
			procedureinfo.description as cpt_desc,
			procedureinfo.cptCode as cpt_Code
			 from 
			chart_master_table join superbill on chart_master_table.id=superbill.formId
			join procedureinfo on superbill.idSuperBill=procedureinfo.idSuperBill
			 where 
			superbill.del_status  = '0'
			and procedureinfo.delete_status  = '0'
			and chart_master_table.date_of_service='$sa_app_start_date'
			and chart_master_table.time_of_service='$sa_app_starttime'
			and superbill.patientId='$pid'
			order by procedureinfo.porder,procedureinfo.id asc");
            if(imw_num_rows($qry_sup) == 0) {
                $qry_sup = imw_query("select 
                procedureinfo.description as cpt_desc,
                procedureinfo.cptCode as cpt_Code
                 from 
                chart_master_table join superbill on chart_master_table.id=superbill.formId
                join procedureinfo on superbill.idSuperBill=procedureinfo.idSuperBill
                 where 
                superbill.del_status  = '0'
                and procedureinfo.delete_status  = '0'
                and chart_master_table.date_of_service='$sa_app_start_date'
                and superbill.patientId='$pid'
                order by procedureinfo.porder,procedureinfo.id asc");
            }
        while ($fet_sup = imw_fetch_array($qry_sup)) {
            $ext_counter++;
            $cpt_Code = $fet_sup['cpt_Code'];
            $XML_encouter_entry .= '<entry>';
            $XML_encouter_entry .= '<act classCode="ACT" moodCode="EVN" >
            <!--Encounter performed Act -->
            <templateId root="2.16.840.1.113883.10.20.24.3.133"/>
            <id root="1.3.6.1.4.1.115"  extension="5a1b8c7dcde4a30025d3d'.$ext_counter.'"/>
            <code code="ENC" codeSystem="2.16.840.1.113883.5.6" displayName="Encounter" codeSystemName="ActClass"/>
            
            <entryRelationship typeCode="SUBJ">';
            $XML_encouter_entry .= '<encounter classCode="ENC" moodCode="EVN">';
            /* BEGIN ENCOUNTER ACTIVITIES */
            $XML_encouter_entry .= '<!-- Encounter Activities -->';
            $XML_encouter_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.49" extension="2015-08-01"/>';
            /* Encounter performed template */
            $XML_encouter_entry .= '<templateId root="2.16.840.1.113883.10.20.24.3.23" extension="2016-02-01"/>';

            //$XML_encouter_entry .= '<id nullFlavor="NI"/>';
            $XML_encouter_entry .= '<id root="1.3.6.1.4.1.115" extension="5a1b8c7dcde4a30025d3d'.$ext_counter.'"/>';

            $valueset_arr = array();
            $valueset_arr = fetchValueSet($cpt_Code,$pid,'Encounter');
            if(isset($valueset_arr['value_set']) && $valueset_arr['value_set'] != '') {
                $valueset = $valueset_arr['value_set'];
                $valueset_text = $valueset_arr['valueset_text'];
                $code_system = $valueset_arr['code_system'];
                $XML_encouter_entry .= '<code code="' . $cpt_Code . '" codeSystem="'.$code_system.'" sdtc:valueSet="'.$valueset.'">
				<originalText>'.$valueset_text.'</originalText></code>
				<text>'.$valueset_text.'</text>
				<statusCode code="completed"/>';
            } else {
                $XML_encouter_entry .= '<code code="' . $cpt_Code . '" codeSystem="2.16.840.1.113883.6.12" sdtc:valueSet="2.16.840.1.113883.3.464.1003.101.12.1001">
				<originalText>Encounter, Performed: Office Visit (Code List: 2.16.840.1.113883.3.464.1003.101.12.1001)</originalText></code>
				<text>Encounter, Performed: Office Visit (Code List: 2.16.840.1.113883.3.464.1003.101.12.1001)</text>
				<statusCode code="completed"/>';
            }

            $exp_sa_app_start_date = str_replace('-', '', $enc_row['sa_app_start_date']);
            $exp_sa_app_end_date = str_replace('-', '', $enc_row['sa_app_end_date']);
            $low_val = $exp_sa_app_start_date . str_replace(':', '', $enc_row['sa_app_starttime']);
            $high_val = $exp_sa_app_end_date . str_replace(':', '', $enc_row['sa_app_endtime']);
            
            if ($enc_row['sa_app_start_date'] != "") {
                $XML_encouter_entry .= '<effectiveTime>';
                $XML_encouter_entry .= '<low value="' . $low_val . '"/>';
                $XML_encouter_entry .= '<high value="' . $high_val . '"/></effectiveTime>';
            } else {
                $XML_encouter_entry .= '<effectiveTime nullFlavor="NI"/>';
            }

            foreach($inp_data[$enc_row['id']] as $keyType => $inpTmpData )
            {
                if( $keyType == 'DischargeCode' && $inpTmpData['field_code'] != '') {
                   $XML_encouter_entry .= '<sdtc:dischargeDispositionCode code="'.$inpTmpData['field_code'].'" codeSystem="'.$inpTmpData['field_codesystem'].'"/>'; 
                }
                elseif( $keyType == 'PrincipalDiag' && $inpTmpData['field_code'] != '') {
                   $XML_encouter_entry .= '
                    <entryRelationship typeCode="REFR">
                        <observation classCode="OBS" moodCode="EVN">
                            <code code="8319008" codeSystem="2.16.840.1.113883.6.96" displayName="Principal Diagnosis" codeSystemName="SNOMED CT"/>
                            <value code="'.$inpTmpData['field_code'].'" xsi:type="CD" codeSystem="'.$inpTmpData['field_codesystem'].'"/>
                        </observation>
                    </entryRelationship>'; 
                }
            }
            /* END ENCOUNTER ACTIVITIES */
            $XML_encouter_entry .= '</encounter>';
            $XML_encouter_entry .= '</entryRelationship>';
            $XML_encouter_entry .= '</act>';
            $XML_encouter_entry .= '</entry>';
        }
    }

    /* END ENOCUTERS SECTION */

    /* BEGIN Communication from provider to provider SECTION * /

    $query3 = "SELECT patient_consult_letter_tbl.cur_date,ut.user_type_name as user_type,
				patient_consult_letter_tbl.patient_consult_letter_to,patient_consult_letter_tbl.templateData
				FROM patient_consult_letter_tbl
				join chart_master_table on chart_master_table.id=patient_consult_letter_tbl.patient_form_id
				JOIN users usr ON usr.id = chart_master_table.providerId
			    JOIN user_type ut ON usr.user_type = ut.user_type_id
				WHERE
				patient_consult_letter_tbl.patient_id IN ($pid) 
				AND patient_consult_letter_tbl.status = '0'
				AND LOWER(patient_consult_letter_tbl.templateData) LIKE '%macula%' 
				AND LOWER(patient_consult_letter_tbl.templateData) LIKE '%edema%'";
    $query3_run = imw_query($query3);
    while ($query3_row = imw_fetch_array($query3_run)) {
        $XML_pro_comm_entry .= '<entry>';
        $XML_pro_comm_entry .= '<act classCode="ACT" moodCode="EVN" >';
        /* Communication from provider to provider * /
        $XML_pro_comm_entry .= '<templateId root="2.16.840.1.113883.10.20.24.3.4"/>';
        $XML_pro_comm_entry .= ' <id nullFlavor="NI"/>';

        $arrProviderType = get_provider_code($query3_row['user_type']);

        /* DYNAMIC ENCOUNTER TYPE CODE TO GET FROM CHART ENTRY * /
        $XML_pro_comm_entry .= '<code code="193349004" codeSystem="2.16.840.1.113883.6.96" sdtc:valueSet="2.16.840.1.113883.3.526.3.1324">';
        $XML_pro_comm_entry .= '<originalText>Communication: From Provider to Provider: Level of Severity of Retinopathy Findings (Code List: 2.16.840.1.113883.3.526.3.1324)
									</originalText>
									</code>
									<text> Communication: From Provider to Provider: Level of Severity of Retinopathy Findings (Code List: 2.16.840.1.113883.3.526.3.1324)</text>
									<statusCode code="completed"/>';

        $low_val = str_replace(' ', '', str_replace(':', '', str_replace('-', '', $query3_row['cur_date'])));

        if ($query3_row['cur_date'] != "") {
            $XML_pro_comm_entry .= '<effectiveTime>';
            $XML_pro_comm_entry .= '<low value="' . $low_val . '"/></effectiveTime>';
        } else {
            $XML_pro_comm_entry .= '<effectiveTime nullFlavor="NI"/>';
        }


        /* $XML_pro_comm_entry .='<participant typeCode="AUT">
          <participantRole classCode="ASSIGNED">
          <code code="'.$arrProviderType['code'].'" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED CT" displayName="'.$arrProviderType['display_name'].'"/>
          </participantRole>
          </participant>'; * /


        $XML_pro_comm_entry .= '<participant typeCode="AUT">
		  <participantRole classCode="ASSIGNED">
			<code code="158965000" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED CT" displayName="Medical Practitioner"/>
		  </participantRole>
		</participant>
	
		<participant typeCode="IRCP">
		  <participantRole classCode="ASSIGNED">
			<code code="158965000" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED CT" displayName="Medical Practitioner"/>
		  </participantRole>
		</participant>';

        $XML_pro_comm_entry .= '</act></entry>';

        $XML_pro_comm_entry .= '<entry>';
        $XML_pro_comm_entry .= '<act classCode="ACT" moodCode="EVN" >';
        /* Communication from provider to provider * /
        $XML_pro_comm_entry .= '<templateId root="2.16.840.1.113883.10.20.24.3.4"/>';
        $XML_pro_comm_entry .= ' <id nullFlavor="NI"/>';

        $arrProviderType = get_provider_code($query3_row['user_type']);

        /* DYNAMIC ENCOUNTER TYPE CODE TO GET FROM CHART ENTRY * /
        $XML_pro_comm_entry .= '<code code="428341000124108" codeSystem="2.16.840.1.113883.6.96" sdtc:valueSet="2.16.840.1.113883.3.526.3.1286">';
        $XML_pro_comm_entry .= '<originalText>Communication: From Provider to Provider: Macular Edema Findings Absent (Code List: 2.16.840.1.113883.3.526.3.1286)
									</originalText>
									</code>
									<text> Communication: From Provider to Provider: Macular Edema Findings Absent (Code List: 2.16.840.1.113883.3.526.3.1286)</text>
									<statusCode code="completed"/>';

        $low_val = str_replace(' ', '', str_replace(':', '', str_replace('-', '', $query3_row['cur_date'])));

        if ($query3_row['cur_date'] != "") {
            $XML_pro_comm_entry .= '<effectiveTime>';
            $XML_pro_comm_entry .= '<low value="' . $low_val . '"/></effectiveTime>';
        } else {
            $XML_pro_comm_entry .= '<effectiveTime nullFlavor="NI"/>';
        }


        /* $XML_pro_comm_entry .='<participant typeCode="AUT">
          <participantRole classCode="ASSIGNED">
          <code code="'.$arrProviderType['code'].'" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED CT" displayName="'.$arrProviderType['display_name'].'"/>
          </participantRole>
          </participant>'; * /


        $XML_pro_comm_entry .= '<participant typeCode="AUT">
		  <participantRole classCode="ASSIGNED">
			<code code="158965000" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED CT" displayName="Medical Practitioner"/>
		  </participantRole>
		</participant>
	
		<participant typeCode="IRCP">
		  <participantRole classCode="ASSIGNED">
			<code code="158965000" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED CT" displayName="Medical Practitioner"/>
		  </participantRole>
		</participant>';

        $XML_pro_comm_entry .= '</act></entry>';
    }

    $query3 = "SELECT patient_consult_letter_tbl.cur_date,ut.user_type_name as user_type,
				patient_consult_letter_tbl.patient_consult_letter_to,patient_consult_letter_tbl.templateData
				FROM patient_consult_letter_tbl
				join chart_master_table on chart_master_table.id=patient_consult_letter_tbl.patient_form_id
				JOIN users usr ON usr.id = chart_master_table.providerId
			    JOIN user_type ut ON usr.user_type = ut.user_type_id
				join consulttemplate on consulttemplate.consultLeter_id=patient_consult_letter_tbl.templateId
				WHERE
				patient_consult_letter_tbl.patient_id IN ($pid) 
				AND patient_consult_letter_tbl.status = '0'
				and consulttemplate.complete_consult_report='1'";
    $query3_run = imw_query($query3);
    while ($query3_row = imw_fetch_array($query3_run)) {
        $XML_pro_comm_entry .= '<entry>';
        $XML_pro_comm_entry .= '<act classCode="ACT" moodCode="EVN" >';
        /* Communication from provider to provider * /
        $XML_pro_comm_entry .= '<templateId root="2.16.840.1.113883.10.20.24.3.4"/>';
        $XML_pro_comm_entry .= ' <id nullFlavor="NI"/>';

        $arrProviderType = get_provider_code($query3_row['user_type']);

        /* DYNAMIC ENCOUNTER TYPE CODE TO GET FROM CHART ENTRY * /
        $XML_pro_comm_entry .= '<code code="371530004" codeSystem="2.16.840.1.113883.6.96" sdtc:valueSet="2.16.840.1.113883.3.464.1003.121.12.1006">';
        $XML_pro_comm_entry .= '<originalText>Communication: From Provider to Provider: Consultant Report (Code List: 2.16.840.1.113883.3.464.1003.121.12.1006)
									</originalText>
									</code>
									<text>Communication: From Provider to Provider: Consultant Report (Code List: 2.16.840.1.113883.3.464.1003.121.12.1006)</text>
									<statusCode code="completed"/>';

        $low_val = str_replace(' ', '', str_replace(':', '', str_replace('-', '', $query3_row['cur_date'])));

        if ($query3_row['cur_date'] != "") {
            $XML_pro_comm_entry .= '<effectiveTime>';
            $XML_pro_comm_entry .= '<low value="' . $low_val . '"/></effectiveTime>';
        } else {
            $XML_pro_comm_entry .= '<effectiveTime nullFlavor="NI"/>';
        }


        /* $XML_pro_comm_entry .='<participant typeCode="AUT">
          <participantRole classCode="ASSIGNED">
          <code code="'.$arrProviderType['code'].'" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED CT" displayName="'.$arrProviderType['display_name'].'"/>
          </participantRole>
          </participant>'; * /


        $XML_pro_comm_entry .= '<participant typeCode="AUT">
		  <participantRole classCode="ASSIGNED">
			<code code="158965000" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED CT" displayName="Medical Practitioner"/>
		  </participantRole>
		</participant>
	
		<participant typeCode="IRCP">
		  <participantRole classCode="ASSIGNED">
			<code code="158965000" codeSystem="2.16.840.1.113883.6.96" codeSystemName="SNOMED CT" displayName="Medical Practitioner"/>
		  </participantRole>
		</participant>';

        $XML_pro_comm_entry .= '</act></entry>';
    }*/

    /* END Communication from provider to provider SECTION */
		$query3 = "SELECT * FROM communication Where patient_id IN (".$pid.") ";
		$query3_run = imw_query($query3);	
		while ($row = imw_fetch_array($query3_run)) {
			$row['description'] = str_replace('sdtcvalueSet=','sdtc:valueSet=',$row['description']);
			$row['description'] = str_replace('xsitype=','xsi:type=',$row['description']);
			$XML_pro_comm_entry .= $row['description'];
		}
	
    /* BEGIN Diagnostic Study, Result SECTION */
    $qry = "SELECT * FROM rad_test_data WHERE rad_patient_id = '" . $pid . "' and rad_status = 2
            AND (LOWER(rad_results) like '%cup to disc%' OR LOWER(rad_name) like '%cup to disc%' OR LOWER(rad_results) like '%optic disc%' OR LOWER(rad_name) like '%optic disc%' 
            OR LOWER(rad_name) like '%cup%' OR LOWER(rad_name) like '%optic%' OR LOWER(rad_name) like '%disc%') ";
    $res = imw_query($qry);
    if (imw_num_rows($res) > 0) {
        while ($row = imw_fetch_assoc($res)) {
            $ext_counter++;

            $refusal = ($row['refusal'] && $row['refusal_snomed']) ? 'true' : 'false';

            $XML_test_entry .= '<entry>
                <!-- Diagnostic Study, Rad cup disc optic disc -->
              <observation classCode="OBS" moodCode="EVN" ' . ($refusal ? 'negationInd="' . $refusal . '"' : '' ) . ' >
                                <!-- Consolidated Procedure Activity Observation templateId 
                                (Implied Template) -->
                                <templateId root="2.16.840.1.113883.10.20.22.4.13" extension="2014-06-09"/>
                                <!-- Diagnostic Study, Performed template -->
                                <templateId root="2.16.840.1.113883.10.20.24.3.18" extension="2016-02-01"/>
                                <id root="1.3.6.1.4.1.115" extension="5a23958ecde4a30022ccf' . $ext_counter . '"/>';

            if ($refusal == 'true') {
                $XML_test_entry .= '<code nullFlavor="NA"
                                      sdtc:valueSet="2.16.840.1.113883.3.526.3.1334">
                                    <originalText>Diagnostic Study, Performed: Cup to Disc Ratio</originalText>
                                </code>
                                <text>Diagnostic Study, Performed: Optic Disc Exam for Structural Abnormalities</text>';
            } else {
                $valueset_arr = array();
                $valueset_arr = fetchValueSet($row['rad_loinc'],$pid);
                if (!empty($valueset_arr) && $valueset_arr['value_set'] != '') {
                    $valueset = $valueset_arr['value_set'];
                    $valueset_text = $valueset_arr['valueset_text'];
                    $code_system = $valueset_arr['code_system'];
                    $XML_test_entry .= '<code code="' . $row['rad_loinc'] . '" codeSystem="'.$code_system.'" sdtc:valueSet="'.$valueset.'">
                                    <originalText>'.$valueset_text.'</originalText>
                                </code>
                                <text>Diagnostic Study, Performed: ' . $row['rad_name'] . ' (Code List: '.$valueset.')</text>';
                } else {
                    $XML_test_entry .= '<code code="' . $row['rad_loinc'] . '" codeSystem="2.16.840.1.113883.6.1" sdtc:valueSet="2.16.840.1.113883.3.526.3.1251">
                                    <originalText>Diagnostic Study, Performed: ' . $row['rad_name'] . ' (Code List: 2.16.840.1.113883.3.464.1003.113.12.1033)</originalText>
                                </code>
                                <text>Diagnostic Study, Performed: ' . $row['rad_name'] . ' (Code List: 2.16.840.1.113883.3.464.1003.113.12.1033)</text>';
                }
            }

            $XML_test_entry .= '<statusCode code="completed"/>
                                <effectiveTime>
                                    <low value="' . str_replace('-', '', $row['rad_order_date']) . str_replace(':', '', $row['rad_order_time']) . '"/>
                                    <high value="' . str_replace('-', '', $row['rad_results_date']) . str_replace(':', '', $row['rad_results_time']) . '"/>
                                </effectiveTime>';
            if ($refusal == 'true') {
                $XML_test_entry .= '<value xsi:type="CD" nullFlavor="UNK"/>';
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
            if ($refusal == 'true') {
                $XML_test_entry .= '<entryRelationship typeCode="RSON">
                                    <observation classCode="OBS" moodCode="EVN">
                                        <templateId root="2.16.840.1.113883.10.20.24.3.88" extension="2014-12-01"/>
                                        <id root="1.3.6.1.4.1.115" extension="18269332A3DC7C2486E70FC1DC871149"/>
                                        <code code="77301-0" codeSystem="2.16.840.1.113883.6.1" displayName="reason" codeSystemName="LOINC"/>
                                        <statusCode code="completed"/>
                                        <effectiveTime>
                                            <low value="' . str_replace('-', '', $row['rad_order_date']) . str_replace(':', '', $row['rad_order_time']) . '"/>
                                            <high value="' . str_replace('-', '', $row['rad_results_date']) . str_replace(':', '', $row['rad_results_time']) . '"/>
                                        </effectiveTime>
                                        <value xsi:type="CD" code="' . $row['refusal_snomed'] . '" codeSystem="2.16.840.1.113883.6.96" sdtc:valueSet="2.16.840.1.113883.3.526.3.1007"/>
                                    </observation>
                                </entryRelationship>';
            }
            $XML_test_entry .= '</observation>
                        </entry>';
        }
    } else {
        /* END Diagnostic Study SECTION */

//-------BEGIN DIAGNOSTICS RAD TESTS SECTION --------------//
        $qry = "SELECT * FROM rad_test_data WHERE rad_patient_id = '" . $pid . "' and rad_status = 2";
        $res = imw_query($qry);
				$tmpExtArr = array();
        while ($row = imw_fetch_assoc($res)) {
            $ext_counter++;
						$id_extention = '5a1e6cc4cde4a364e87b0' . $ext_counter;
						$refusal = ($row['refusal'] && $row['refusal_snomed']) ? 'true' : '';
						// Use same extention if values exists in array 
						// based on Patient id, Radiology Test Name, Order Date, Result Date and LOINC Code
						if( $tmpExtArr[$pid][$row['rad_name']][$row['rad_order_date']][$row['rad_results_date']][$row['rad_loinc']] ){
							$id_extention = $tmpExtArr[$pid][$row['rad_name']][$row['rad_order_date']][$row['rad_results_date']][$row['rad_loinc']];
						}
						else {
							$tmpExtArr[$pid][$row['rad_name']][$row['rad_order_date']][$row['rad_results_date']][$row['rad_loinc']] = $id_extention;
						}
						
            $XML_test_entry .= '<entry typeCode="DRIV" >
                <!-- Diagnostic Study, Rad section -->
                             <observation classCode="OBS" moodCode="EVN" ' . ($refusal ? 'negationInd="' . $refusal . '"' : '' ) . ' >
        <!-- Consolidated Procedure Activity Observation templateId 
           (Implied Template) -->
        <templateId root="2.16.840.1.113883.10.20.22.4.13" extension="2014-06-09"/>
        <!-- Diagnostic Study, Performed template -->
        <templateId root="2.16.840.1.113883.10.20.24.3.18" extension="2016-02-01"/>
        <id root="1.3.6.1.4.1.115" extension="'.$id_extention. '"/>';
            $valueset_arr = array();
            $valueset_arr = fetchValueSet($row['rad_loinc'],$pid);
						if ($refusal == 'true') {
                $XML_test_entry .= '<code nullFlavor="NA" sdtc:valueSet="2.16.840.1.113883.3.526.3.1251">
                                    	<originalText>Diagnostic Study, Performed: Macular Exam</originalText>
                                		</code>
                                		<text>Diagnostic Study, Performed: Macular Exam</text>';
            }
						else
						{
							if (!empty($valueset_arr) && $valueset_arr['value_set'] != '') {
									$valueset = $valueset_arr['value_set'];
									$valueset_text = $valueset_arr['valueset_text'];
									$code_system = $valueset_arr['code_system'];
								
									$XML_test_entry .= '<code code="' . $row['rad_loinc'] . '" codeSystem="'.$code_system.'" sdtc:valueSet="' . $valueset . '">
											<originalText>' . $valueset_text . '</originalText>
									</code>
									<text>' . $valueset_text . '</text>';
							} else {
									$XML_test_entry .= '<code code="' . $row['rad_loinc'] . '" codeSystem="2.16.840.1.113883.6.1" sdtc:valueSet="2.16.840.1.113883.3.526.3.1251">
							<originalText>Diagnostic Study, Performed: ' . $row['rad_name'] . ' (Code List: 2.16.840.1.113883.3.526.3.1251)</originalText>
					</code>
									<text>Diagnostic Study, Performed: ' . $row['rad_name'] . ' (Code List: 2.16.840.1.113883.3.526.3.1251)</text>';
							}
						}

            $XML_test_entry .= '<statusCode code="completed"/>
        		<effectiveTime>
          	<!-- Attribute: Start Datetime -->
            <low value="' . str_replace('-', '', $row['rad_order_date']) . str_replace(':', '', $row['rad_order_time']) . '"/>
            <high value="' . str_replace('-', '', $row['rad_results_date']) . str_replace(':', '', $row['rad_results_time']) . '"/>
            </effectiveTime>';
            
						if ($refusal == 'true') {
                $XML_test_entry .= '<value xsi:type="CD" nullFlavor="UNK"/>';
            } else {
                if ($row['snowmedCode']) {
										$XML_test_entry .= '<value code="' . $row['snowmedCode'] . '" codeSystem="2.16.840.1.113883.6.96" xsi:type="CD">
												<originalText>Diagnostic Study, Performed: ' . $row['rad_name'] . ' </originalText>
										</value>';
								} else {
										$XML_test_entry .= '<value xsi:type="CD" nullFlavor="UNK"/>';
								}
            }
						
						 if ($refusal == 'true') {
							 	$valueSetRefArr = array();
            		$valueSetRefArr = fetchValueSet($row['refusal_snomed'],$pid);
							 	
								$XML_test_entry .= '<entryRelationship typeCode="RSON">
									<observation classCode="OBS" moodCode="EVN">
											<templateId root="2.16.840.1.113883.10.20.24.3.88" extension="2014-12-01"/>
											<id root="1.3.6.1.4.1.115" extension="18269332A3DC7C2486E70FC1DC871149"/>
											<code code="77301-0" codeSystem="2.16.840.1.113883.6.1" displayName="reason" codeSystemName="LOINC"/>
											<statusCode code="completed"/>
											<effectiveTime>
													<low value="' . str_replace('-', '', $row['rad_order_date']) . str_replace(':', '', $row['rad_order_time']) . '"/>
											</effectiveTime>
											<value xsi:type="CD" code="' . $row['refusal_snomed'].'" codeSystem="'.$valueSetRefArr['code_system'].'" sdtc:valueSet="'.$valueSetRefArr['value_set'].'"/>
									</observation>
							</entryRelationship>';
            }
					
						
            $XML_test_entry .= '</observation></entry>';
        }
    }
//-------END Diagnostic Study RAD TESTS SECTION --------------//
//-------BEGIN Interventions SECTION --------------//
    $qry = "SELECT * FROM lists WHERE pid = '" . $pid . "' and type = '5' and allergy_status='Active' and proc_type='intervention'";
    $res = imw_query($qry);
    while ($row = imw_fetch_assoc($res)) {
        $ext_counter++;
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
					
				$valueSet_int = fetchValueSet($row['ccda_code'],$pid);
				$int_cs_db = ($valueSet_int['code_system']) ? $valueSet_int['code_system'] : '2.16.840.1.113883.6.96';
				$int_vs_db = ($valueSet_int['value_set']) ? $valueSet_int['value_set'] : '2.16.840.1.113762.1.4.1108.15';
			
        $XML_intervention_entry .= '<entry>
				<act classCode="ACT" moodCode="' . $moodCode . '" '.($refusal ? 'negationInd="'.$refusal.'"' : '' ).' >
				<!-- Consolidation CDA: Procedure Activity Act template -->
				<templateId root="' . $template_id1 . '" extension="2014-06-09"/>
				<templateId root="' . $template_id2 . '" extension="2016-02-01"/>
				<id root="1.3.6.1.4.1.115" extension="5a23952dcde4a3001567c' . $ext_counter++ . '" />';
			
				if( $refusal ) {
					$XML_intervention_entry .= '
					<code nullFlavor="NA" sdtc:valueSet="2.16.840.1.113883.3.526.3.509">
						<originalText>Intervention, Performed: ' . $row['title'] . ' </originalText>
					</code>';
				}
				else {
					$XML_intervention_entry .= '
					<code code="' . $row['ccda_code'] . '" codeSystem="'.$int_cs_db.'" sdtc:valueSet="'.$int_vs_db.'">
						<originalText>Intervention, ' . $inter_type . ': ' . $row['title'] . ' (Code List: '.$int_vs_db.')</originalText>
					</code>';
				}
				
				$XML_intervention_entry .= '
				<statusCode code="' . $status . '"/>
				<effectiveTime>
					<low value="' . $start_date . '"/>
					<high value="' . $end_date . '"/>
				</effectiveTime>
				<author>
				<templateId root="2.16.840.1.113883.10.20.22.4.119"/>
      	<time value="' . $start_date . '"/>
      	<assignedAuthor><id nullFlavor="NI"/></assignedAuthor>
				</author>';
				
				if( $refusal ) {
					$valueSetArr_ref = fetchValueSet($row['refusal_snomed'],$pid);
					$ref_int_cs_db = ($valueSetArr_ref['code_system']) ? $valueSetArr_ref['code_system'] : '2.16.840.1.113883.6.96';
					$ref_int_vs_db = ($valueSetArr_ref['value_set']) ? $valueSetArr_ref['value_set'] : '2.16.840.1.113883.3.526.3.1007';
					
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
							<value xsi:type="CD" code="'.$row['refusal_snomed'].'" codeSystem="'.$ref_int_cs_db.'" sdtc:valueSet="'.$ref_int_vs_db.'"/>
						</observation>
					</entryRelationship>';
					
				}
			
			$XML_intervention_entry .= '
			</act>
			</entry>';
    }
//-------END Interventions SECTION --------------//
//-------BEGIN Procedure SECTION --------------//
		$tmpExtArr = array();
    $qry = "SELECT * FROM lists WHERE pid = '" . $pid . "' and type = '5' and allergy_status='Active' and proc_type='procedure' order by if(translation_code = '' or translation_code is null,1,0),translation_code";
    $res = imw_query($qry);
    while ($row = imw_fetch_assoc($res)) {
        $ext_counter++;
			$proc_extention = '5a23958bcde4a3001848b' . $ext_counter;
        
			
			$refusal = ($row['refusal'] && $row['refusal_snomed']) ? 'true' : '';
			if( $row['translation_code'] ) {
				$tmpExtArr[$row['translation_code']] = $proc_extention;
			}
			
			if( $tmpExtArr[$row['ccda_code']]) {
				$proc_extention = $tmpExtArr[$row['ccda_code']];
			}
		$XML_procedure_entry .= '<entry>
		  <procedure classCode="PROC" moodCode="EVN" '.($refusal ? 'negationInd="'.$refusal.'"' : '' ).' >
			<!--  Procedure performed template -->
			<templateId root="2.16.840.1.113883.10.20.24.3.64" extension="2016-02-01"/>
			<!-- Procedure Activity Procedure-->
    	<templateId root="2.16.840.1.113883.10.20.22.4.14" extension="2014-06-09"/>
			<id root="1.3.6.1.4.1.115" extension="' . $proc_extention . '"/>';
      $date = date('YmdHi',strtotime($row['begdate'].' '.$row['begtime']));
				
			if( $refusal )
			{
				$XML_procedure_entry .= '<code nullFlavor="NA" sdtc:valueSet="2.16.840.1.113883.3.600.1.462">
																	<originalText>Procedure, Performed: ' . $row['title'] . '</originalText>';
			}
			else
			{
				
				$valueset_arr = array();
        $valueset_arr = fetchValueSet($row['ccda_code'],$pid);
        $proc_code_cs_db = ($valueset_arr['code_system']) ? $valueset_arr['code_system'] : '2.16.840.1.113883.6.96';
				$proc_code_vs_db = ($valueset_arr['value_set']) ? $valueset_arr['value_set'] : '2.16.840.1.113883.3.600.1.462';
				
				$XML_procedure_entry .= '<code code="' . $row['ccda_code'] . '" codeSystem="'.$proc_code_cs_db.'" sdtc:valueSet="'.$proc_code_vs_db.'">
                    <originalText>Procedure, Performed: ' . $row['title'] . ' (Code List: '.$proc_code_vs_db.')</originalText>';
			}

        /* <translation code="90920" codeSystem="2.16.840.1.113883.6.12"/>
          <translation code="G0257" codeSystem="2.16.840.1.113883.6.285"/> */
        $XML_procedure_entry .= '</code>
				<text>Procedure, Performed: ' . $row['title'] . ' </text>
				<statusCode code="completed"/>
				<effectiveTime>
					<low value="' . $date . '"/>
					<high value="' . $date . '"/>
				</effectiveTime>';
			
			if( $refusal  == 'true' )
			{
				$valueSetArr_ref = fetchValueSet($row['refusal_snomed'],$pid);
				$proc_int_cs_db = ($valueSetArr_ref['code_system']) ? $valueSetArr_ref['code_system'] : '2.16.840.1.113883.6.96';
				$proc_int_vs_db = ($valueSetArr_ref['value_set']) ? $valueSetArr_ref['value_set'] : '2.16.840.1.113883.3.526.3.1007';
				
			 	$XML_procedure_entry .= '<entryRelationship typeCode="RSON">
				<observation classCode="OBS" moodCode="EVN">
					<templateId root="2.16.840.1.113883.10.20.24.3.88" extension="2014-12-01"/>
					<id root="1.3.6.1.4.1.115" extension="237EC4944F035A60404997BF467BC66E"/>
						<code code="77301-0" codeSystem="2.16.840.1.113883.6.1" displayName="reason" codeSystemName="LOINC"/>
						<statusCode code="completed"/>
						<effectiveTime>
							<low value="'.$date.'"/>
						</effectiveTime>
						<value xsi:type="CD" code="'.$row['refusal_snomed'].'" codeSystem="'.$proc_int_cs_db.'" sdtc:valueSet="'.$proc_int_vs_db.'"/>
				</observation>
			</entryRelationship>';
			}
			
		   $XML_procedure_entry .= '</procedure>
		</entry>';
    }
//-------END Procedure SECTION --------------//

    /* BEGIN Patient Characteristics SECTION */
//    $qry = imw_query("SELECT date_format(modified_on,'%Y-%m-%d') as modified_on_new,smoking_status,smoke_years_months FROM social_history WHERE patient_id = '" . $pid . "'");
//    if (imw_num_rows($qry) > 0) {
//        $row_social = imw_fetch_array($qry);
//        $arrTmp = explode('/', $row_social['smoking_status']);
//        $smoking_type = $arrTmp[0];
//        $smoking_code = trim($arrTmp[1]);
//        /* BEGIN SMOKING STATUS ENTRY */
//        if (trim($smoking_code) == '449868002') {
//            //$smoke_code_list="2.16.840.1.113883.3.600.2390";
//            $smoke_code_list = "2.16.840.1.113883.3.526.3.1170";
//        } else {
//            $smoke_code_list = "2.16.840.1.113883.3.526.3.1170";
//        }
//        $modified_on_exp = explode('-', $row_social['modified_on_new']);
//        $cut_year = $modified_on_exp[0];
//        $cut_month = $modified_on_exp[1];
//        $cut_day = $modified_on_exp[2];
//        if ($row_social['smoke_years_months'] == "Years") {
//            $cut_year = $cut_year - $row_social['number_of_years_with_smoke'];
//        }
//        if ($row_social['smoke_years_months'] == "Months") {
//            $cut_month = $cut_month - $row_social['number_of_years_with_smoke'];
//        }
//        $smoke_start_date = date('Ymd', mktime(0, 0, 0, $cur_month, $cur_day, $cur_year));
//
//        $XML_smoking_status_entry = '<entry>';
//        $XML_smoking_status_entry .= '<observation classCode="OBS" moodCode="EVN">';
//        $XML_smoking_status_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.85"/>
//									<id nullFlavor="NI"/>';
//        $XML_smoking_status_entry .= '<code code="ASSERTION" displayName="Assertion"  codeSystemName="ActCode" codeSystem="2.16.840.1.113883.5.4"/>';
//        $XML_smoking_status_entry .= '<statusCode code="completed"/>';
//        $XML_smoking_status_entry .= '<effectiveTime>';
//        $XML_smoking_status_entry .= '<low value="' . $smoke_start_date . '"/><high nullFlavor="UNK"/>';
//        $XML_smoking_status_entry .= '</effectiveTime>';
//        $XML_smoking_status_entry .= ' <value code="' . $smoking_code . '" codeSystem="2.16.840.1.113883.6.96" xsi:type="CD" sdtc:valueSet="' . $smoke_code_list . '">
//									  <originalText>Patient Characteristic: ' . $smoking_type . ' (Code List: ' . $smoke_code_list . ')</originalText>
//									  </value>';
//        $XML_smoking_status_entry .= '</observation>';
//        $XML_smoking_status_entry .= '</entry>';
//
//        /* END SMOKING STATUS ENTRY */
//    }
    /* END Patient Characteristics SECTION */

//-------BEGIN Medication SECTION --------------//
    $qry = "SELECT * FROM lists WHERE pid = '" . $pid . "' and type in(1,4) and (allergy_status='Active' OR allergy_status='Order')";
    $res = imw_query($qry);
    while ($row = imw_fetch_assoc($res)) {
        if ($row['allergy_status'] == 'Active') {
            $med_value_set = "2.16.840.1.113883.3.526.3.1190";
            $med_code_system = "2.16.840.1.113883.6.88";
        } else {
            $med_value_set = "2.16.840.1.113883.3.464.1003.196.12.1429";
            $med_code_system = "2.16.840.1.113883.6.88";
        }
        $codeSystemArr = fetchValueSet($row['ccda_code'],$pid);
        $med_value_set = $codeSystemArr['value_set'] ? $codeSystemArr['value_set'] : $med_value_set;
		$med_code_system = $codeSystemArr['code_system'] ? $codeSystemArr['code_system'] : $med_code_system;
        
        $refusal_med = ($row['refusal'] && $row['refusal_snomed'] ) ? 'true' : '';
				
        $period_value = '';
        $period_val_unit = '';
        if(trim($row['sig'])) {
            $period = explode(';', trim($row['sig']));
            $period_value = $period[0];
            $period_val_unit = $period[1];
        }
        
        $doseQuantity = trim($row['destination']) ? trim($row['destination']) : '';
        
        $XML_procedure_entry .= '<entry>';
                if ($row['allergy_status'] == 'Active') {
        $XML_procedure_entry .= '<substanceAdministration classCode="SBADM"  moodCode="EVN" '.($refusal_med ? 'negationInd="'.$refusal_med.'"' : '' ).' >
                            <templateId root="2.16.840.1.113883.10.20.22.4.16" extension="2014-06-09"/>
                            <!-- Medication, Active template -->
                            <templateId root="2.16.840.1.113883.10.20.24.3.41" extension="2016-02-01"/>';
                } else {
        $XML_procedure_entry .= '<!--Medication Order --><substanceAdministration classCode="SBADM" moodCode="RQO" '.($refusal_med ? 'negationInd="'.$refusal_med.'"' : '' ).' >
            <templateId root="2.16.840.1.113883.10.20.22.4.42" extension="2014-06-09"/>
				<!-- Medication, Order template -->
				<templateId root="2.16.840.1.113883.10.20.24.3.47" extension="2016-02-01"/>';
                }
		$XML_procedure_entry .= '<!-- <templateId root="2.16.840.1.113883.10.20.22.4.42" extension="2014-06-09"/> -->
				<!-- Medication, Order template -->
				<!-- <templateId root="2.16.840.1.113883.10.20.24.3.47" extension="2016-02-01"/> -->
                

				<id root="1.3.6.1.4.1.115" extension="5a1e6cbacde4a364e6dca' . $ext_counter++ . '"/>
				<text>Medication, Order: ' . $row['title'] . ' (Code List: 2.16.840.1.113883.3.526.3.1190)</text>
				<statusCode code="active" />
				<effectiveTime xsi:type="IVL_TS">
					<low value="' . date('YmdHi', strtotime($row['begdate'].' '.$row['begtime'])) . '"/>
					<high value="' . date('YmdHi', strtotime($row['begdate'].' '.$row['begtime'])) . '"/>
				</effectiveTime>
		
				<effectiveTime xsi:type="PIVL_TS" institutionSpecified="true" operator="A">';
        if ($period_value)
            $XML_procedure_entry .= '<period value="' . $period_value . '" ' . ($period_val_unit ? 'unit="' . $period_val_unit . '"' : '') . ' />';
        else
            $XML_procedure_entry .= '<period nullFlavor="NI" />';

        $XML_procedure_entry .= '</effectiveTime>';
			
        if ($doseQuantity) {
            $XML_procedure_entry .= '<doseQuantity value="'.$doseQuantity.'" />';
        } else {
            $XML_procedure_entry .= '<repeatNumber value="1" />';
        }
        $XML_procedure_entry .= '	
		
				<consumable>
					<manufacturedProduct classCode="MANU">
						<templateId root="2.16.840.1.113883.10.20.22.4.23" extension="2014-06-09"/>
						<id nullFlavor="NA" />
						<manufacturedMaterial>';
				
						if( $row['ccda_code'] ) {
								$XML_procedure_entry .= '	
								<code code="' . $row['ccda_code'] . '" codeSystem="'.$med_code_system.'" sdtc:valueSet="' . $med_value_set . '">
									<originalText>Medication, Order: ' . $row['title'] . ' (Code List: ' . $med_value_set . ')</originalText>
								</code>';
							}
							else {
								$XML_procedure_entry .= '
								<code nullFlavor="NA" sdtc:valueSet="2.16.840.1.113883.3.526.3.1190">
									<originalText>Medication, Order: ' . $row['title'] . '</originalText>
								</code>';
							}
							
				$XML_procedure_entry .= '			
						</manufacturedMaterial>
					</manufacturedProduct>
				</consumable>
		
				<author>
					<templateId root="2.16.840.1.113883.10.20.22.4.119"/>
					<time value="' . date('YmdHis', strtotime($row['begdate'].' '.$row['begtime'])) . '"/>
					<assignedAuthor>
						<id nullFlavor="NA" />
					</assignedAuthor>
				</author>';
				
			if( $refusal_med ) {
				$valueSetArr_ref = fetchValueSet($row['refusal_snomed'],$pid);
				$ref_med_cs_db = ($valueSetArr_ref['code_system']) ? $valueSetArr_ref['code_system'] : '2.16.840.1.113883.6.96';
				$ref_med_vs_db = ($valueSetArr_ref['value_set']) ? $valueSetArr_ref['value_set'] : '2.16.840.1.113883.3.526.3.1007';
				
				$XML_procedure_entry .= '
				<entryRelationship typeCode="RSON">
					<observation classCode="OBS" moodCode="EVN">
						<templateId root="2.16.840.1.113883.10.20.24.3.88" extension="2014-12-01"/>
						<id root="1.3.6.1.4.1.115" extension="E5A081CE9D99EEF322632A5D3D6AF1D1"/>
						<code code="77301-0" codeSystem="2.16.840.1.113883.6.1" displayName="reason" codeSystemName="LOINC"/>
						<statusCode code="completed"/>
						<effectiveTime>
								<low value="'.date('YmdHi', strtotime($row['begdate'].' '.$row['begtime'])).'"/>
						</effectiveTime>
						<value xsi:type="CD" code="'.$row['refusal_snomed'].'" codeSystem="'.$ref_med_cs_db.'" sdtc:valueSet="'.$ref_med_vs_db.'"/>
					</observation>
				</entryRelationship>';	
			}
				
			$XML_procedure_entry .= '
	  </substanceAdministration>
	</entry>';
    }
//-------END Medication SECTION --------------//


    /* BEGIN PROBLEM SECTION */
    $arrProblemList = get_pt_problem_list($pid, $dtfrom1, $dtupto1);
    $res_prob_list = imw_query($qry);
    if (count($arrProblemList) > 0) {
        foreach ($arrProblemList as $problemList) {
            $problem_name_exp = explode('-', $problemList['problem_name']);
            
            $prob_code = trim($problem_name_exp[1]);
            $prob_name = trim($problem_name_exp[0]);
            
            if((strpos($prob_code, '.') === false) || strpos($prob_code, '.')+1 == strlen($prob_code)){
                $prob_code = '';
            }
            //If code is there in array
            if(count($problem_name_exp) > 2 && (strpos($prob_code, '.') === false)){
                $prob_code = trim(array_pop($problem_name_exp));
                $prob_name = trim(implode('-', $problem_name_exp));
            }

            $prob_valueSet = '';
						if ($problemList['ccda_code'] == "4855003") {
                $code_list = 'Diagnosis, Active: Diabetic Retinopathy (Code List: 2.16.840.1.113883.3.526.3.327)';
                $prob_valueSet = "2.16.840.1.113883.3.526.3.327";
            }
            if ($problemList['ccda_code'] == "161894002") {
                $code_list = 'Diagnosis, Active: Low Back Pain (Code List: 2.16.840.1.113883.3.464.1003.113.12.1001)';
                $prob_valueSet = "2.16.840.1.113883.3.464.1003.113.12.1001";
            }
            if ($problemList['ccda_code'] == "10725009") {
                $code_list = 'Diagnosis, Active: Hypertension (Code List: 2.16.840.1.113883.3.464.1003.104.12.1016)';
                $prob_valueSet = "2.16.840.1.113883.3.464.1003.104.12.1016";
            }
            if ($problemList['ccda_code'] == "190330002") {
                $code_list = 'Diagnosis, Active: Diabetes (Code List: 2.16.840.1.113883.3.464.1003.103.12.1001)';
                $prob_valueSet = "2.16.840.1.113883.3.464.1003.103.12.1001";
            }
            if ($problemList['ccda_code'] == "162607003") {
                $code_list = 'Diagnosis, Active: Limited Life Expectancy (Code List: 2.16.840.1.113883.3.526.3.1259)';
                $prob_valueSet = "2.16.840.1.113883.3.526.3.1259";
            }
            if ($problemList['ccda_code'] == "109267002") {
                $code_list = 'Diagnosis, Active: All Cancer (Code List: 2.16.840.1.113883.3.464.1003.108.12.1011)';
                $prob_valueSet = "2.16.840.1.113883.3.464.1003.108.12.1011";
            }
            if ($problemList['ccda_code'] == "10725009") {
                $code_list = 'Diagnosis, Active: Essential Hypertension (Code List: 2.16.840.1.113883.3.464.1003.104.12.1011)';
                $prob_valueSet = "2.16.840.1.113883.3.464.1003.104.12.1011";
            }

            if ($problemList['ccda_code'] == "111513000" || stripos($problemList['problem_name'],'primary open angle glaucoma')!== false || stripos($problemList['problem_name'],'poag')!== false ) {
                $code_list = 'Diagnosis, Active: Primary Open Angle Glaucoma (POAG) (Code List: 2.16.840.1.113883.3.526.3.326)';
                $prob_valueSet = "2.16.840.1.113883.3.526.3.326";
                $problemList['ccda_code'] = "111513000";
            }
            if ($problemList['ccda_code'] == "59621000") {
                $code_list = 'Diagnosis: Diagnosis of hypertension (Code List: 2.16.840.1.113883.3.464.1003.104.12.1011)';
                $prob_valueSet = "2.16.840.1.113883.3.464.1003.104.12.1011";
            }
            if ($problemList['ccda_code'] == "47200007") {
                $code_list = 'Diagnosis: Pregnancy (Code List: 2.16.840.1.113883.3.526.3.378)';
                $prob_valueSet = "2.16.840.1.113883.3.526.3.378";
            }
            if ($problemList['ccda_code'] == "1201005") {
                $code_list = 'Diagnosis: Pregnancy (Code List: 2.16.840.1.113883.3.464.1003.104.12.1011)';
                $prob_valueSet = "2.16.840.1.113883.3.464.1003.104.12.1011";
            }
            if ($problemList['ccda_code'] == "" || $prob_name == "Pregnancy Dx") {
                $code_list = 'Diagnosis, Active: Pregnancy Dx (Code List: 2.16.840.1.113883.3.600.1.1623)';
                $prob_valueSet = "2.16.840.1.113883.3.600.1.1623";
            }
           	//$prob_valueSet = $prob_valueSet ? $prob_valueSet : '2.16.840.1.113883.3.464.1003.196.12.1399';
						
						$XML_problem_section .= '<entry>';
            $XML_problem_section .= '<act classCode="ACT" moodCode="EVN">
                            <!-- Conforms to C-CDA 2.1 Problem Concern Act (V3) -->
                            <templateId root="2.16.840.1.113883.10.20.22.4.3" extension="2015-08-01" />
                            <!-- Diagnosis Concern Act -->
                            <templateId root="2.16.840.1.113883.10.20.24.3.137" extension="2016-08-01" />
                            <id root="c4fb0717-92f7-4f42-b98e-622ffb818c1a" />
                            <code code="CONC" codeSystem="2.16.840.1.113883.5.6" displayName="Concern" />
    
                            <statusCode code="active" />';

            if ($problemList['onset_date'] != "") {
                $XML_problem_section .= ' <effectiveTime>';
                $XML_problem_section .= ' <low value="' . str_replace('-', '', $problemList['onset_date']) . str_replace(':', '', $problemList['OnsetTime']) .'"/>';
                $XML_problem_section .= '<high nullFlavor="UNK"/></effectiveTime>';
            } else {
                $XML_problem_section .= ' <effectiveTime nullFlavor="NI"/>';
            }
            $XML_problem_section .= '<entryRelationship typeCode="SUBJ">';
            $XML_problem_section .= '<observation classCode="OBS" moodCode="EVN">';
            $XML_problem_section .= '<!-- Problem Observation template -->';

            $XML_problem_section .= '<templateId root="2.16.840.1.113883.10.20.22.4.4" extension="2015-08-01" />';
            //$XML_problem_section .= '<templateId root="2.16.840.1.113883.10.20.24.3.11"/>';
            $XML_problem_section .= '<templateId root="2.16.840.1.113883.10.20.24.3.135"/>';
            //$XML_problem_section .= '<id nullFlavor="NI"/>';
            $XML_problem_section .= '<id root="1.3.6.1.4.1.115" extension="5a23958bcde4a3001848b'.++$ext_counter.'"/>';

            $arrProbType = problem_type_srh($problemList['prob_type']);

            if ($arrProbType['code'] != "" && $arrProbType['display_name'] != "") {
                $XML_problem_section .= '<code code="29308-4" codeSystem="2.16.840.1.113883.6.1" codeSystemName="SNOMED-CT" displayName="' . $arrProbType['display_name'] . '">
                                        <translation code="' . $arrProbType['code'] . '" codeSystem="2.16.840.1.113883.6.96"/>
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
								}
								else {
                	$XML_problem_section .= '<high nullFlavor="UNK"/>';
								}
								
								$XML_problem_section .= '</effectiveTime>';
            } else {
                $XML_problem_section .= ' <effectiveTime nullFlavor="NI"/>';
            }
						
						$codeSystemArr = fetchValueSet($problemList['ccda_code'],$pid);//pre($codeSystemArr);// echo $pid.'<br>';
						$codeSystem_db = $codeSystemArr['code_system'];
						$valueSet_db = $codeSystemArr['value_set'];
						if( !$codeSystem_db ) $codeSystem_db = '2.16.840.1.113883.6.96';
						if( !$valueSet_db ) $valueSet_db = '2.16.840.1.113883.3.526.3.327';
						//echo $problemList['ccda_code'].'--'. $codeSystem_db .'--' . $valueSet_db .'--'.$pid.'<br>';
            // DYNAMIC PROBLEM VALUE //
            if ($problemList['ccda_code'] != "" && $prob_valueSet != '' && $code_list != '') { //echo 'INN -- '.$pid.'--';
                $XML_problem_section .= '<value xsi:type="CD" code="' . $problemList['ccda_code'] . '" codeSystem="'.$codeSystem_db.'" 
								sdtc:valueSet="' . $prob_valueSet . '">
							   		<originalText>' . $code_list . '</originalText>';
            } else {
                if($problemList['ccda_code'] != "") { //echo 'OUT -- '.$pid;
										$XML_problem_section .= '<value xsi:type="CD" code="' . $problemList['ccda_code'] . '" codeSystem="'.$codeSystem_db.'" sdtc:valueSet="'.$valueSet_db.'">
							   		<originalText>Diagnosis, Active: Diabetic  (Code List: '.$valueSet_db.')</originalText>';
                } else {
                    $XML_problem_section .= '<value xsi:type="CD" nullFlavor="UNK">';
                }
            }
            if ($prob_code != "") {
                $XML_problem_section .= '<translation code="' . $prob_code . '" codeSystem="2.16.840.1.113883.6.103"/>';
            }
            $XML_problem_section .= '</value>';

//            $XML_problem_section .= '<entryRelationship typeCode = "REFR">
//				<observation classCode = "OBS" moodCode = "EVN">
//					<!--Problem status observation template -->
//					<templateId root = "2.16.840.1.113883.10.20.22.4.6"/>
//					<templateId root="2.16.840.1.113883.10.20.24.3.94"/>
//					<id nullFlavor="NI"/>
//					
//					<code code = "33999-4" codeSystem = "2.16.840.1.113883.6.1" codeSystemName = "LOINC" displayName = "Status"/>
//					<statusCode code = "completed"/>
//					<value xsi:type = "CD" code = "55561003" codeSystem = "2.16.840.1.113883.6.96" 
//					displayName = "' . $problemList['status'] . '" codeSystemName = "SNOMED CT"/>
//				</observation>
//			</entryRelationship>';
            $XML_problem_section .= '</observation>';
            $XML_problem_section .= '</entryRelationship>';
            $XML_problem_section .= '</act>';
            $XML_problem_section .= '</entry>';
        }
    }

    /* END PROBLEM SECTION */

    /* BEGIN PHYSICAL EXAMS */
    $sql_vital = "SELECT vsp.*,vsl.vital_sign,vsm.date_vital FROM vital_sign_master vsm 
					JOIN vital_sign_patient vsp ON vsm.id = vsp.vital_master_id 
					JOIN  vital_sign_limits vsl ON vsl.id = vsp.vital_sign_id 
					WHERE vsm.patient_id = '" . $pid . "' AND  vsm.status = 0 and vsp.range_vital!='' ORDER BY vsp.id ASC";
    $result_vital = imw_query($sql_vital);
    while ($row_vital = imw_fetch_assoc($result_vital)) {
        $ext_counter++;
        $arr_vs_result_type = vs_result_type_srh($row_vital['vital_sign']);
        $code_list = "";
        $vs_valueSet = "";
        if ($row_vital['vital_sign'] == "B/P - Systolic") {
            $code_list = 'Physical Exam, Finding: Systolic Blood Pressure (Code List: 2.16.840.1.113883.3.526.3.1032)';
            $vs_valueSet = "2.16.840.1.113883.3.526.3.1032";
            $vs_code_system = "2.16.840.1.113883.6.1";
        }
        if ($row_vital['vital_sign'] == "B/P - Diastolic") {
            $code_list = 'Physical Exam, Finding: Diastolic Blood Pressure (Code List: 2.16.840.1.113883.3.526.3.1033)';
            $vs_valueSet = "2.16.840.1.113883.3.526.3.1033";
            $vs_code_system = "2.16.840.1.113883.6.1";
        }
        if ($row_vital['vital_sign'] == "BMI") {
            $code_list = 'Physical Exam, Finding: BMI LOINC Value (Code List: 2.16.840.1.113883.3.600.1.681)';
            $vs_valueSet = "2.16.840.1.113883.3.600.1.681";
            $vs_code_system = "2.16.840.1.113883.6.1";
        }

        if ($code_list != "") {
            $valueSetArr_obs = fetchValueSet($arr_vs_result_type['code'],$pid);
            if($valueSetArr_obs['value_set'] != '') {
                $vs_valueSet = $valueSetArr_obs['value_set'];
                $vs_code_system = $valueSetArr_obs['code_system'];
                $code_list = $valueSetArr_obs['valueset_text'];
            }
            $XML_physical_exams .= '
                
			<entry>
			  <!-- Physical Exam Finding -->
			  <observation classCode="OBS" moodCode="EVN">
				<!--  Result observation template -->
				<!-- <templateId root="2.16.840.1.113883.10.20.22.4.2"/> -->
				<!-- Physical Exam, Finding template -->
				<!-- <templateId root="2.16.840.1.113883.10.20.24.3.57"/> -->
                <!-- Procedure Activity Procedure (Consolidation) template -->
                <templateId root="2.16.840.1.113883.10.20.22.4.13" extension="2014-06-09"/>
                <!-- Physical Exam, Performed template -->
                <templateId root="2.16.840.1.113883.10.20.24.3.59" extension="2016-02-01"/>
    
				 <id root="1.3.6.1.4.1.115" extension="5a1e6cb7cde4a364e87af'.$ext_counter.'"/>
										
				<code code="' . $arr_vs_result_type['code'] . '" codeSystem="'.$vs_code_system.'" sdtc:valueSet="' . $vs_valueSet . '">
				<originalText>' . $code_list . '</originalText>
				</code>    
				<statusCode code="completed"/>
			
				<effectiveTime>
				  <low value="' . str_replace('-', '', $row_vital['date_vital']) . date('His', $row_vital['inhale_O2']) .'"/>
				  <high value="' . str_replace('-', '', $row_vital['date_vital']) . date('His', $row_vital['inhale_O2']) .'"/>
				</effectiveTime>';
            if ($row_vital['range_vital'] != "") {
                $XML_physical_exams .= '<value xsi:type="PQ" value="' . trim($row_vital['range_vital']) . '" unit="' . html_entity_decode(preg_replace('/\s/', '', trim($row_vital['unit']))) . '"/>';
            } else {
                $XML_physical_exams .= '<value xsi:type="PQ" nullFlavor="NI"/>';
            }
            $XML_physical_exams .= '</observation></entry>';
        }
    }

//    $sql_vital = "SELECT date_format(exam_date,'%Y-%m-%d') as exam_date_new FROM  chart_dialation 
//					WHERE patient_id = '" . $pid . "' and eyeSide!=''";
//    $result_vital = imw_query($sql_vital);
//    while ($row_vital = imw_fetch_assoc($result_vital)) {
//        $XML_physical_exams .= '
//		<entry>
//		  <!-- Physical Exam Finding -->
//		  <observation classCode="OBS" moodCode="EVN">
//			<!--  Result observation template -->
//			<templateId root="2.16.840.1.113883.10.20.22.4.13"/>
//			<!-- Physical Exam, Finding template -->
//			<templateId root="2.16.840.1.113883.10.20.24.3.59"/>
//			 <id root="1.3.6.1.4.1.115" extension="5a1e6cb7cde4a364e87aff2d"/>
//									
//			<code code="252779009" codeSystem="2.16.840.1.113883.6.96" sdtc:valueSet="2.16.840.1.113883.3.464.1003.115.12.1088">
//				<originalText>Physical Exam, Performed: Retinal or Dilated Eye Exam (Code List: 2.16.840.1.113883.3.464.1003.115.12.1088)</originalText>
//			</code>
//    			<text>Physical Exam, Performed: Retinal or Dilated Eye Exam (Code List: 2.16.840.1.113883.3.464.1003.115.12.1088)</text>
//			<statusCode code="completed"/>
//			<effectiveTime>
//			  <low value="' . str_replace('-', '', $row_vital['exam_date_new']) . '"/>
//			  <high value="' . str_replace('-', '', $row_vital['exam_date_new']) . '"/>
//			</effectiveTime>
//			<value xsi:type="CD" nullFlavor="UNK"/>';
//        $XML_physical_exams .= '</observation></entry>';
//    }
    //Health Observations.
    $sql = "SELECT *, hc_observations.snomed_code as snomed_code, hc_rel_observations.snomed_code AS scode FROM  hc_observations
            LEFT JOIN hc_concerns ON hc_concerns.observation_id = hc_observations.id  
            LEFT JOIN hc_rel_observations ON hc_rel_observations.observation_id = hc_observations.id 
            WHERE pt_id = '" . $pid . "' AND type=0 AND hc_observations.del_status=0";
    $result_hc = imw_query($sql);
    while ($row_hc = imw_fetch_assoc($result_hc)) {
			$ext_counter++;
			
			$valueSetArr_obs = fetchValueSet($row_hc['snomed_code'],$pid);
			$obs_cs_db = ($valueSetArr_obs['code_system']) ? $valueSetArr_obs['code_system'] : '2.16.840.1.113883.6.96';
			$obs_vs_db = ($valueSetArr_obs['value_set']) ? $valueSetArr_obs['value_set'] : '2.16.840.1.113883.3.526.3.1488';
			
    $XML_physical_exams .= '<entry>
                        <observation classCode="OBS" moodCode="EVN" >
                            <!-- Procedure Activity Procedure (Consolidation) template -->
                            <templateId root="2.16.840.1.113883.10.20.22.4.13" extension="2014-06-09"/>
                            <!-- Physical Exam, Performed template -->
                            <templateId root="2.16.840.1.113883.10.20.24.3.59" extension="2016-02-01"/>
                            <id root="1.3.6.1.4.1.115" extension="5a23958bcde4a3001848b'.$ext_counter.'"/>
                            <code code="'.$row_hc['snomed_code'].'" codeSystem="'.$obs_cs_db.'" sdtc:valueSet="'.$obs_vs_db.'">
                                <originalText>'.$row_hc['observation'].'</originalText>
                            </code>
                            <text>Physical Exam, Performed: Best Corrected Visual Acuity</text>
                            <statusCode code="completed"/>
                            <effectiveTime>
                                <low value="' . str_replace('-', '', $row_hc['observation_date']) . str_replace(':', '', $row_hc['observation_time']) .'"/>
                                <high value="' . str_replace('-', '', $row_hc['observation_date']) . str_replace(':', '', $row_hc['observation_time']) .'"/>
                            </effectiveTime>';
                            if($row_hc['scode']) {
    $XML_physical_exams .= '<value code="' . $row_hc['scode'] . '" codeSystem="2.16.840.1.113883.6.96" xsi:type="CD">
                                <originalText>' . $row_hc['observation'] . '</originalText>
                            </value>';
                            } else {
    $XML_physical_exams .= '<value xsi:type="CD" nullFlavor="UNK"/>';     
                            }
    $XML_physical_exams .= '</observation>
                    </entry>';
    }



    /* END PHYSICAL EXAMS */


    /* Start Assessments */
    $sql = "SELECT *, hc_observations.snomed_code as lionic_code, hc_rel_observations.snomed_code AS scode FROM  hc_observations
            LEFT JOIN hc_rel_observations ON hc_rel_observations.observation_id = hc_observations.id 
            WHERE pt_id = '" . $pid . "' AND type=1 AND hc_observations.del_status=0";
    $result_hc = imw_query($sql);
    while ($row_hc = imw_fetch_assoc($result_hc)) {
			$refusal = ($row_hc['refusal'] && $row_hc['refusal_snomed']) ? 'true' : '';
        $XML_physical_exams .= '<entry>
				<observation classCode="OBS" moodCode="EVN" '.($refusal ? 'negationInd="'.$refusal.'"' : '' ).' >
        <!-- Assessment Performed -->
				<templateId root="2.16.840.1.113883.10.20.24.3.144" extension="2016-08-01" />
				<id root="1.3.6.1.4.1.115" extension="5a23952fcde4a30022ccf' . $ext_counter++ . '"/>';
				if( $refusal ) {
					$XML_physical_exams .= '
					<code nullFlavor="NA" sdtc:valueSet="2.16.840.1.113883.3.526.3.1278">
						<originalText>Assessment, Performed: ' . $row_hc['observation'] . '</originalText>
					</code>';
				}
				else {
					$XML_physical_exams .= '
					<code code="' . $row_hc['lionic_code'] . '" codeSystem="2.16.840.1.113883.6.1" sdtc:valueSet="2.16.840.1.113883.3.526.3.1278">
						<originalText>Assessment, Performed: ' . $row_hc['observation'] . '</originalText>
					</code>';
				}
				$XML_physical_exams .= '
				<text>Assessment, Performed: Tobacco Use Screening</text>
        <statusCode code="completed"/>
				<effectiveTime>
					<low value="' . str_replace('-', '', $row_hc['observation_date']) . str_replace(':', '', $row_hc['observation_time']) .'"/>
					<high value="' . str_replace('-', '', $row_hc['observation_date']) . str_replace(':', '', $row_hc['observation_time']) .'"/>
				</effectiveTime>';
			
			$valueSetArr_hc = fetchValueSet($row_hc['scode'],$pid);
			$hc_cs_db = ($valueSetArr_hc['code_system']) ? $valueSetArr_hc['code_system'] : '2.16.840.1.113883.6.96';
			$hc_vs_db = ($valueSetArr_hc['value_set']) ? $valueSetArr_hc['value_set'] : '2.16.840.1.113883.3.526.3.1007';
			
			if( $refusal ) {
				$XML_physical_exams .= '<value xsi:type="CD" nullFlavor="UNK"/>';
			}
			else {
				//$row_hc['scode']
				/*$XML_physical_exams .= '
				<value code="160603005" codeSystem="2.16.840.1.113883.6.96" xsi:type="CD" sdtc:valueSet="2.16.840.1.113883.3.526.3.1170">
					<originalText>Assessment, Performed: ' . $row_hc['observation'] . '</originalText>
				</value>';*/
							
			$XML_physical_exams .= '
				<value code="'.$row_hc['scode'].'" codeSystem="'.$hc_cs_db.'" xsi:type="CD" sdtc:valueSet="'.$hc_vs_db.'">
					<originalText>Assessment, Performed: ' . $row_hc['observation'] . '</originalText>
				</value>';
			}
			
			if( $refusal ) {
				$valueSetArr_ref = fetchValueSet($row_hc['refusal_snomed'],$pid);
				$ref_cs_db = ($valueSetArr_ref['code_system']) ? $valueSetArr_ref['code_system'] : '2.16.840.1.113883.6.96';
				$ref_vs_db = ($valueSetArr_ref['value_set']) ? $valueSetArr_ref['value_set'] : '2.16.840.1.113883.3.526.3.1007';
				$XML_physical_exams .= '
				<entryRelationship typeCode="RSON">
					<observation classCode="OBS" moodCode="EVN">
						<templateId root="2.16.840.1.113883.10.20.24.3.88" extension="2014-12-01"/>
						<id root="1.3.6.1.4.1.115" extension="76008024C6FE667D7BB03E61C8825FC8"/>
						<code code="77301-0" codeSystem="2.16.840.1.113883.6.1" displayName="reason" codeSystemName="LOINC"/>
						<statusCode code="completed"/>
							<effectiveTime>
								<low value="' . str_replace('-', '', $row_hc['observation_date']) . str_replace(':', '', $row_hc['observation_time']) .'" />
							</effectiveTime>
						<value xsi:type="CD" code="'.$row_hc['refusal_snomed'].'" codeSystem="'.$ref_cs_db.'" sdtc:valueSet="'.$ref_vs_db.'"/>
					</observation>
				</entryRelationship>';
			}
			else {
				$XML_physical_exams .= '
				<entryRelationship typeCode="REFR">
					<observation classCode="OBS" moodCode="EVN">
					<!-- Conforms to C-CDA R2.1 Result Observation (V3) -->
					<templateId root="2.16.840.1.113883.10.20.22.4.2" extension="2015-08-01" />
					<!-- Result (V3) -->
					<templateId root="2.16.840.1.113883.10.20.24.3.87" extension="2016-02-01" />
					<id root="1.3.6.1.4.1.115" extension="091C9215035F50D11CA5C001B8AEEE44"/>
					<code code="' . $row_hc['lionic_code'] . '" codeSystem="2.16.840.1.113883.6.1" sdtc:valueSet="2.16.840.1.113883.3.526.3.1278">
						<originalText>Assessment, Performed: ' . $row_hc['rel_observation'] . '</originalText>
					</code>
					<statusCode code="completed"/>
					<effectiveTime>
						<low value="' . str_replace('-', '', $row_hc['observation_date']) . str_replace(':', '', $row_hc['observation_time']) .'"/>
						<high value="' . str_replace('-', '', $row_hc['observation_date']) . str_replace(':', '', $row_hc['observation_time']) .'"/>
					</effectiveTime>
					<value code="' . $row_hc['scode'] . '" codeSystem="'.$hc_cs_db.'" xsi:type="CD" sdtc:valueSet="'.$hc_vs_db.'">
						<originalText>Assessment, Performed: ' . $row_hc['rel_observation'] . '</originalText>
					</value>
					</observation>
				</entryRelationship>';
			}
			
			$XML_physical_exams .= '
				</observation>
			</entry>';
    }
    /* End Assessments */
    
    /*
     * Start Patient Characteristic Payer
     */
    
    $payerQry = "Select * From patientPayer Where pid = '".$pid."'";
    $payerSql = imw_query($payerQry);
    $payerCnt = imw_num_rows($payerSql);
    if( $payerCnt ) {
        while( $payerRow = imw_fetch_object($payerSql) ) {
            $payerStartDate = (getNumber($payerRow->EffStart) == '00000000000000') ? '' : getNumber($payerRow->EffStart);
            $payerEndDate = (getNumber($payerRow->EffEnd) == '00000000000000') ? '' : getNumber($payerRow->EffEnd);
            
            $payerValCodeSet = ($payerRow->valCodeSet) ? $payerRow->valCodeSet : '2.16.840.1.113883.3.221.5';
            $payerValValueSet = ($payerRow->valValueSet) ? $payerRow->valValueSet : '2.16.840.1.114222.4.11.3591';
            
            $XML_patient_payer .= '
            <entry>
                <!-- Patient Characteristic Payer -->
                <observation classCode="OBS" moodCode="EVN">
                    <templateId root="2.16.840.1.113883.10.20.24.3.55"/>
                    <id root="1.3.6.1.4.1.115" extension="5a2567364210bb04f2a3789a"/>
                    <code code="'.$payerRow->payer.'" codeSystemName="LOINC" codeSystem="2.16.840.1.113883.6.1" displayName="Payment source"/> 
                    <statusCode code="completed"/>
                    <effectiveTime>
                      <!-- Attribute: Start Datetime -->
                      <low value="'.$payerStartDate.'"/>';
                      if( $payerEndDate )
                          $XML_patient_payer .= '<high value="'.$payerEndDate.'"/>';
            $XML_patient_payer .= '
                    </effectiveTime>
                    <value code="'.$payerRow->valueCode.'" codeSystem="'.$payerValCodeSet.'" xsi:type="CD" sdtc:valueSet="'.$payerValValueSet.'">
                        <originalText></originalText>
                    </value>
                </observation>
            </entry>';
        }
    }
    
    /*
     * End Patient Characteristic Payer
     */
    
    /* START Measure SECTION */
    $XML_measure_section = '<component>
        <section>
          <!-- 
            *****************************************************************
            Measure Section
            *****************************************************************
          -->
          <!-- This is the templateId for Measure Section -->
          <templateId root="2.16.840.1.113883.10.20.24.2.2"/>
          <!-- This is the templateId for Measure Section QDM -->
          <templateId root="2.16.840.1.113883.10.20.24.2.3"/>
          <!-- This is the LOINC code for "Measure document". This stays the same for all measure section required by QRDA standard -->
          <code code="55186-1" codeSystem="2.16.840.1.113883.6.1"/>
          <title>Measure Section</title>
          <text>
            <table border="1" width="100%">
              <thead>
                 <tr>
					<th>eMeasure Title</th>
					<th>Version neutral identifier</th>
					<th>eMeasure Version Number</th>
					<th>NQF eMeasure Number</th>
					<th>Version specific identifier</th>
				</tr>
			   </thead>
				<tbody>';
    if (in_array("NQF0089", $con_nqf_id_arr) || $inc_all_nqf == "yes") {
        $XML_measure_section .= '<tr>
							<td>Diabetic Retinopathy: Communication with the Physician Managing Ongoing
							Diabetes Care</td>
							<td>53D6D7C3-43FB-4D24-8099-17E74C022C05</td>
							<td>2</td>
							<td>0089</td>
							<td>40280382-5971-4EED-015A-04D538502DAE</td>
						</tr>';

        $XML_CLUSTER_section .= '<!-- 1..* Organizers, each containing a reference to an eMeasure -->
					<entry>
                        <organizer classCode="CLUSTER" moodCode="EVN">
                            <!-- This is the templateId for Measure Reference -->
                            <templateId root="2.16.840.1.113883.10.20.24.3.98"/>
                            <!-- This is the templateId for eMeasure Reference QDM -->
                            <templateId root="2.16.840.1.113883.10.20.24.3.97"/>
                            <id root="1.3.6.1.4.1.115" extension="40280382-5971-4EED-015A-04D538502DAE"/>
                            <statusCode code="completed"/>
                            <!-- Containing isBranch external references -->
                            <reference typeCode="REFR">
                                <externalDocument classCode="DOC" moodCode="EVN">
                                    <!-- SHALL: This is the version specific identifier for eMeasure: QualityMeasureDocument/id it is a GUID-->
                                    <id root="2.16.840.1.113883.4.738" extension="40280382-5971-4EED-015A-04D538502DAE"/>
                                    <!-- SHOULD This is the title of the eMeasure -->
                                    <text>Diabetic Retinopathy: Communication with the Physician Managing Ongoing Diabetes Care</text>
                                    <!-- SHOULD: setId is the eMeasure version neutral id  -->
                                    <setId root="53D6D7C3-43FB-4D24-8099-17E74C022C05"/>
                                </externalDocument>
                            </reference>
                        </organizer>
                    </entry>';
    }

    if (in_array("NQF0086", $con_nqf_id_arr) || $inc_all_nqf == "yes") {
        $XML_measure_section .= '<tr>
							<td>Primary Open Angle Glaucoma (POAG): Optic Nerve Evaluation</td>
							<td>db9d9f09-6b6a-4749-a8b2-8c1fdb018823</td>
							<td>2</td>
							<td>0086</td>
							<td>40280382-5971-4EED-015A-04E1EAE72DC3</td>
						</tr>';

        $XML_CLUSTER_section .= '<!-- 1..* Organizers, each containing a reference to an eMeasure -->
					  <entry>
						<organizer classCode="CLUSTER" moodCode="EVN">
						<!-- This is the templateId for Measure Reference -->
                            <templateId root="2.16.840.1.113883.10.20.24.3.98"/>
						<!-- This is the templateId for eMeasure Reference QDM -->
                            <templateId root="2.16.840.1.113883.10.20.24.3.97"/>
                            <id root="1.3.6.1.4.1.115" extension="40280382-5971-4EED-015A-04E1EAE72DC3"/>
                            <statusCode code="completed"/>
                            <!-- Containing isBranch external references -->
						<reference typeCode="REFR">
						<externalDocument classCode="DOC" moodCode="EVN">
						<!-- SHALL: This is the version specific identifier for eMeasure: QualityMeasureDocument/id it is a GUID-->
                                    <id root="2.16.840.1.113883.4.738" extension="40280382-5971-4EED-015A-04E1EAE72DC3"/>
						<!-- SHOULD This is the title of the eMeasure -->
                                    <text>Primary Open-Angle Glaucoma (POAG): Optic Nerve Evaluation</text>
                                    <!-- SHOULD: setId is the eMeasure version neutral id  -->
                                    <setId root="DB9D9F09-6B6A-4749-A8B2-8C1FDB018823"/>
						</externalDocument>
						</reference>
						</organizer>
						</entry>';
    }

    if (in_array("NQF0088", $con_nqf_id_arr) || $inc_all_nqf == "yes") {
        $XML_measure_section .= '<tr>
							<td>Diabetic Retinopathy: Documentation of Presence or Absence of Macular Edema
							and Level of Severity of Retinopathy</td>
							<td>50164228-9d64-4efc-af67-da0547ff61f1</td>
							<td>2</td>
							<td>0088</td>
							<td>40280382-5971-4EED-015A-04EEF5FE2DD3</td>
						</tr>';

        $XML_CLUSTER_section .= '<!-- 1..* Organizers, each containing a reference to an eMeasure -->
					  <entry>
							<organizer classCode="CLUSTER" moodCode="EVN">
								<!-- This is the templateId for Measure Reference -->
								<templateId root="2.16.840.1.113883.10.20.24.3.98"/>
								<!-- This is the templateId for eMeasure Reference QDM -->
								<templateId root="2.16.840.1.113883.10.20.24.3.97"/>
								<id root="1.3.6.1.4.1.115" extension="40280382-5971-4EED-015A-04EEF5FE2DD3"/>
								<statusCode code="completed"/>
								<!-- Containing isBranch external references -->
								<reference typeCode="REFR">
									<externalDocument classCode="DOC" moodCode="EVN">
										<!-- SHALL: This is the version specific identifier for eMeasure: QualityMeasureDocument/id it is a GUID-->
										<id root="2.16.840.1.113883.4.738" extension="40280382-5971-4EED-015A-04EEF5FE2DD3"/>
										<!-- SHOULD This is the title of the eMeasure -->
										<text>Diabetic Retinopathy: Documentation of Presence or Absence of Macular Edema and Level of Severity of Retinopathy</text>
										<!-- SHOULD: setId is the eMeasure version neutral id  -->
										<setId root="50164228-9D64-4EFC-AF67-DA0547FF61F1"/>
									</externalDocument>
								</reference>
							</organizer>
						</entry>';
    }

    if (in_array("NQF0055", $con_nqf_id_arr) || $inc_all_nqf == "yes") {
        $XML_measure_section .= '<tr>
							<td>Diabetes: Eye Exam</td>
                            <td>D90BDAB4-B9D2-4329-9993-5C34E2C0DC66</td>
							<td>0</td>
							<td>0055</td>
							<td>40280382-5ABD-FA46-015B-49956E7C383A</td>
						</tr>';

        $XML_CLUSTER_section .= '<!-- 1..* Organizers, each containing a reference to an eMeasure -->
					  <entry>
                        <organizer classCode="CLUSTER" moodCode="EVN">
                            <!-- This is the templateId for Measure Reference -->
                            <templateId root="2.16.840.1.113883.10.20.24.3.98"/>
                            <!-- This is the templateId for eMeasure Reference QDM -->
                            <templateId root="2.16.840.1.113883.10.20.24.3.97"/>
                            <id root="1.3.6.1.4.1.115" extension="40280382-5ABD-FA46-015B-49956E7C383A"/>
                            <statusCode code="completed"/>
                            <!-- Containing isBranch external references -->
                            <reference typeCode="REFR">
                                <externalDocument classCode="DOC" moodCode="EVN">
                                    <!-- SHALL: This is the version specific identifier for eMeasure: QualityMeasureDocument/id it is a GUID-->
                                    <id root="2.16.840.1.113883.4.738" extension="40280382-5ABD-FA46-015B-49956E7C383A"/>
                                    <!-- SHOULD This is the title of the eMeasure -->
                                    <text>Diabetes: Eye Exam</text>
                                    <!-- SHOULD: setId is the eMeasure version neutral id  -->
                                    <setId root="D90BDAB4-B9D2-4329-9993-5C34E2C0DC66"/>
                                </externalDocument>
                            </reference>
                        </organizer>
                    </entry>';
    }

    if (in_array("NQF0018", $con_nqf_id_arr) || $inc_all_nqf == "yes") {
        $XML_measure_section .= '<tr>
							 <td>Controlling High Blood Pressure</td>
							  <td>ABDC37CC-BAC6-4156-9B91-D1BE2C8B7268</td>
							  <td>2</td>
							  <td>0018</td>
							  <td>40280381-3D61-56A7-013E-66BC02DA4DEE</td>
						</tr>';

        $XML_CLUSTER_section .= '<!-- 1..* Organizers, each containing a reference to an eMeasure -->
					  <entry>
						<organizer classCode="CLUSTER" moodCode="EVN">
						  <!-- This is the templateId for Measure Reference -->
						  <templateId root="2.16.840.1.113883.10.20.24.3.98"/>
						  <!-- This is the templateId for eMeasure Reference QDM -->
						  <templateId root="2.16.840.1.113883.10.20.24.3.97"/>
                          <id root="1.3.6.1.4.1.115" extension="40280382-5ABD-FA46-015B-49ABB28D38B2"/>
						  <statusCode code="completed"/>
						  <!-- Containing isBranch external references -->
						  <reference typeCode="REFR">
							<externalDocument classCode="DOC" moodCode="EVN">
                              <!-- SHALL: This is the version specific identifier for eMeasure: QualityMeasureDocument/id it is a GUID-->
                              <id root="2.16.840.1.113883.4.738" extension="40280382-5ABD-FA46-015B-49ABB28D38B2"/>
							  <!-- SHOULD This is the title of the eMeasure -->
							  <text>Controlling High Blood Pressure</text>
							  <!-- SHOULD: setId is the eMeasure version neutral id  -->
							  <setId root="ABDC37CC-BAC6-4156-9B91-D1BE2C8B7268"/>
							</externalDocument>
						  </reference>
						</organizer>
					 </entry>';
    }

    if (in_array("NQF0022", $con_nqf_id_arr) || in_array("NQF0022a", $con_nqf_id_arr) || in_array("NQF0022b", $con_nqf_id_arr) || $inc_all_nqf == "yes") {
        $XML_measure_section .= '<tr>
            <td>Use of High-Risk Medications in the Elderly</td>
            <td>A3837FF8-1ABC-4BA9-800E-FD4E7953ADBD</td>
            <td>0</td>
            <td>40280382-5ABD-FA46-015B-49B5B1E638E3</td>
            <td></td>
          </tr>';

        $XML_CLUSTER_section .= '<!-- 1..* Organizers, each containing a reference to an eMeasure -->
					  <entry>
						<organizer classCode="CLUSTER" moodCode="EVN">
							<!-- This is the templateId for Measure Reference -->
							<templateId root="2.16.840.1.113883.10.20.24.3.98"/>
							<!-- This is the templateId for eMeasure Reference QDM -->
							<templateId root="2.16.840.1.113883.10.20.24.3.97"/>
							<id root="1.3.6.1.4.1.115" extension="40280382-5ABD-FA46-015B-49B5B1E638E3"/>
							<statusCode code="completed"/>
							<!-- Containing isBranch external references -->
							<reference typeCode="REFR">
								<externalDocument classCode="DOC" moodCode="EVN">
									<!-- SHALL: This is the version specific identifier for eMeasure: QualityMeasureDocument/id it is a GUID-->
									<id root="2.16.840.1.113883.4.738" extension="40280382-5ABD-FA46-015B-49B5B1E638E3"/>
									<!-- SHOULD This is the title of the eMeasure -->
									<text>Use of High-Risk Medications in the Elderly</text>
									<!-- SHOULD: setId is the eMeasure version neutral id  -->
									<setId root="A3837FF8-1ABC-4BA9-800E-FD4E7953ADBD"/>
								</externalDocument>
							</reference>
						</organizer>
					 </entry>';
    }

    if (in_array("NQF0028a", $con_nqf_id_arr) || in_array("NQF0028b", $con_nqf_id_arr) || in_array("NQF0028", $con_nqf_id_arr) || $inc_all_nqf == "yes") {
        $XML_measure_section .= '<tr>
                                <td>Preventive Care and Screening: Tobacco Use: Screening and Cessation Intervention</td>
                                <td>E35791DF-5B25-41BB-B260-673337BC44A8</td>
                                <td>2</td>
                                <td>0028</td>
                                <td>40280382-5ABD-FA46-015B-1B7C6BB929D0</td>
						</tr>';

        $XML_CLUSTER_section .= '<!-- 1..* Organizers, each containing a reference to an eMeasure -->
					  <entry>
                        <organizer classCode="CLUSTER" moodCode="EVN">
                            <!-- This is the templateId for Measure Reference -->
                            <templateId root="2.16.840.1.113883.10.20.24.3.98"/>
                            <!-- This is the templateId for eMeasure Reference QDM -->
                            <templateId root="2.16.840.1.113883.10.20.24.3.97"/>
                            <id root="1.3.6.1.4.1.115" extension="40280382-5ABD-FA46-015B-1B7C6BB929D0"/>
                            <statusCode code="completed"/>
                            <!-- Containing isBranch external references -->
                            <reference typeCode="REFR">
                                <externalDocument classCode="DOC" moodCode="EVN">
                                    <!-- SHALL: This is the version specific identifier for eMeasure: QualityMeasureDocument/id it is a GUID-->
                                    <id root="2.16.840.1.113883.4.738" extension="40280382-5ABD-FA46-015B-1B7C6BB929D0"/>
                                    <!-- SHOULD This is the title of the eMeasure -->
                                    <text>Preventive Care and Screening: Tobacco Use: Screening and Cessation Intervention</text>
                                    <!-- SHOULD: setId is the eMeasure version neutral id  -->
                                    <setId root="E35791DF-5B25-41BB-B260-673337BC44A8"/>
                                </externalDocument>
                            </reference>
                        </organizer>
                    </entry>';
    }

    if (in_array("NQF0419", $con_nqf_id_arr) || $inc_all_nqf == "yes") {
        $XML_measure_section .= '<tr>
            <td>Documentation of Current Medications in the Medical Record</td>
            <td>9A032D9C-3D9B-11E1-8634-00237D5BF174</td>
            <td>0</td>
            <td>40280382-5ABD-FA46-015B-1AFE205E2890</td>
            <td></td>
          </tr>';

        $XML_CLUSTER_section .= '<!-- 1..* Organizers, each containing a reference to an eMeasure -->
						<entry>
							<organizer classCode="CLUSTER" moodCode="EVN">
								<!-- This is the templateId for Measure Reference -->
								<templateId root="2.16.840.1.113883.10.20.24.3.98"/>
								<!-- This is the templateId for eMeasure Reference QDM -->
								<templateId root="2.16.840.1.113883.10.20.24.3.97"/>
								<id root="1.3.6.1.4.1.115" extension="40280382-5ABD-FA46-015B-1AFE205E2890"/>
								<statusCode code="completed"/>
								<!-- Containing isBranch external references -->
								<reference typeCode="REFR">
									<externalDocument classCode="DOC" moodCode="EVN">
										<!-- SHALL: This is the version specific identifier for eMeasure: QualityMeasureDocument/id it is a GUID-->
										<id root="2.16.840.1.113883.4.738" extension="40280382-5ABD-FA46-015B-1AFE205E2890"/>
										<!-- SHOULD This is the title of the eMeasure -->
										<text>Documentation of Current Medications in the Medical Record</text>
										<!-- SHOULD: setId is the eMeasure version neutral id  -->
										<setId root="9A032D9C-3D9B-11E1-8634-00237D5BF174"/>
									</externalDocument>
								</reference>
							</organizer>
						</entry>';
    }
    if (in_array("NQF0564", $con_nqf_id_arr) || $inc_all_nqf == "yes") {
        $XML_measure_section .= '<tr>
							  <td>Cataracts: Complications within 30 Days Following Cataract Surgery Requiring Additional Surgical Procedures</td>
                              <td>9A0339C2-3D9B-11E1-8634-00237D5BF174</td>
							  <td>0</td>
							  <td>564</td>
							  <td>40280382-5A66-EAB9-015A-866C5ADD0C0B</td>
						</tr>';

        $XML_CLUSTER_section .= '<entry>
                        <organizer classCode="CLUSTER" moodCode="EVN">
                            <!-- This is the templateId for Measure Reference -->
                            <templateId root="2.16.840.1.113883.10.20.24.3.98"/>
                            <!-- This is the templateId for eMeasure Reference QDM -->
                            <templateId root="2.16.840.1.113883.10.20.24.3.97"/>
                            <id root="1.3.6.1.4.1.115" extension="40280382-5A66-EAB9-015A-866C5ADD0C0B"/>
                            <statusCode code="completed"/>
                            <!-- Containing isBranch external references -->
                            <reference typeCode="REFR">
                                <externalDocument classCode="DOC" moodCode="EVN">
                                    <!-- SHALL: This is the version specific identifier for eMeasure: QualityMeasureDocument/id it is a GUID-->
                                    <id root="2.16.840.1.113883.4.738" extension="40280382-5A66-EAB9-015A-866C5ADD0C0B"/>
                                    <!-- SHOULD This is the title of the eMeasure -->
                                    <text>Cataracts: Complications within 30 Days Following Cataract Surgery Requiring Additional Surgical Procedures</text>
                                    <!-- SHOULD: setId is the eMeasure version neutral id  -->
                                    <setId root="9A0339C2-3D9B-11E1-8634-00237D5BF174"/>
                                </externalDocument>
                            </reference>
                        </organizer>
                    </entry>';
    }

    if (in_array("NQF0565", $con_nqf_id_arr) || $inc_all_nqf == "yes") {
        $XML_measure_section .= '<tr>
							  <td>Cataracts: 20/40 or Better Visual Acuity within 90 Days Following Cataract Surgery</td>
                              <td>39E0424A-1727-4629-89E2-C46C2FBB3F5F</td>
							  <td>0</td>
							  <td>565</td>
							  <td>40280382-5971-4EED-015A-4E28D6184BC4</td>
						</tr>';

        $XML_CLUSTER_section .= '<entry>
                        <organizer classCode="CLUSTER" moodCode="EVN">
                            <!-- This is the templateId for Measure Reference -->
                            <templateId root="2.16.840.1.113883.10.20.24.3.98"/>
                            <!-- This is the templateId for eMeasure Reference QDM -->
                            <templateId root="2.16.840.1.113883.10.20.24.3.97"/>
                            <id root="1.3.6.1.4.1.115" extension="40280382-5971-4EED-015A-4E28D6184BC4"/>
                            <statusCode code="completed"/>
                            <!-- Containing isBranch external references -->
                            <reference typeCode="REFR">
                                <externalDocument classCode="DOC" moodCode="EVN">
                                    <!-- SHALL: This is the version specific identifier for eMeasure: QualityMeasureDocument/id it is a GUID-->
                                    <id root="2.16.840.1.113883.4.738" extension="40280382-5971-4EED-015A-4E28D6184BC4"/>
                                    <!-- SHOULD This is the title of the eMeasure -->
                                    <text>Cataracts: 20/40 or Better Visual Acuity within 90 Days Following Cataract Surgery</text>
                                    <!-- SHOULD: setId is the eMeasure version neutral id  -->
                                    <setId root="39E0424A-1727-4629-89E2-C46C2FBB3F5F"/>
                                </externalDocument>
                            </reference>
                        </organizer>
                    </entry>';
    }

    $XML_measure_section .= '</tbody>
            </table>
          </text>';


    $XML_measure_section .= $XML_CLUSTER_section;
    $XML_measure_section .= '</section></component>';

    /* END Measure SECTION */

    $dtfrom1_exp = explode('-', $dtfrom1);
    $dtupto1_exp = explode('-', $dtupto1);
    $rep_dtfrom1 = date('j F Y', mktime(0, 0, 0, $dtfrom1_exp[1], $dtfrom1_exp[2], $dtfrom1_exp[0]));
    $rep_dtto1 = date('j F Y', mktime(0, 0, 0, $dtupto1_exp[1], $dtupto1_exp[2], $dtupto1_exp[0]));

    $patient_data_section = ' <component>
        <section>
          <!-- This is the templateId for Patient Data section -->
          <templateId root="2.16.840.1.113883.10.20.17.2.4"/>
          <!-- This is the templateId for Patient Data QDM section -->
          <templateId root="2.16.840.1.113883.10.20.24.2.1" extension="2016-08-01" />
          <templateId root="2.16.840.1.113883.10.20.24.2.1.1" extension="2017-07-01"/>
          <code code="55188-7" codeSystem="2.16.840.1.113883.6.1"/>
          <title>Patient Data</title>
          <text></text>';

    $XML_reporting_section = '<component>
        <section>
          <!-- This is the templateId for Reporting Parameters section -->
          <templateId root="2.16.840.1.113883.10.20.17.2.1"/>
          <templateId root="2.16.840.1.113883.10.20.17.2.1.1" extension="2016-03-01" />
          <code code="55187-9" codeSystem="2.16.840.1.113883.6.1"/>
          <title>Reporting Parameters</title>
          <text>
            <list>
              <item>Reporting period: ' . $rep_dtfrom1 . ' - ' . $rep_dtto1 . '</item>
            </list>
          </text>
          <entry typeCode="DRIV">
            <act classCode="ACT" moodCode="EVN">
              <!-- This is the templateId for Reporting Parameteres Act -->
              <templateId root="2.16.840.1.113883.10.20.17.3.8"/>
              <templateId root="2.16.840.1.113883.10.20.17.3.8.1"/>
              <id root="1.3.6.1.4.1.115" />
              <code code="252116004" codeSystem="2.16.840.1.113883.6.96" displayName="Observation Parameters"/>
              <effectiveTime>
                <low value="' . $dtfrom1_exp[0] . $dtfrom1_exp[1] . $dtfrom1_exp[2] . '000000"/>
                <high value="' . $dtupto1_exp[0] . $dtupto1_exp[1] . $dtupto1_exp[2] . '000000"/>
              </effectiveTime>
            </act>
          </entry>
        </section>
      </component>';

    /* BEGIN XML BODY */
    $XML_cda_body = '<component>';
    $XML_cda_body .= '<structuredBody>';

    $XML_cda_body .= $XML_measure_section;
    $XML_cda_body .= $XML_reporting_section;
    $XML_cda_body .= $patient_data_section;

    $XML_cda_body .= $XML_encouter_entry;
    $XML_cda_body .= $XML_physical_exams;
    $XML_cda_body .= $XML_test_entry;
    $XML_cda_body .= $XML_smoking_status_entry;
    $XML_cda_body .= $XML_pro_comm_entry;
    $XML_cda_body .= $XML_problem_section;
    $XML_cda_body .= $XML_procedure_entry;
    $XML_cda_body .= $XML_intervention_entry;
    $XML_cda_body .= $XML_patient_payer;
    
    $XML_cda_body .= ' </section></component></structuredBody>';
    $XML_cda_body .= '</component>';
    /* END XML BODY */

    $xml = '<?xml version="1.0" encoding="utf-8"?>';
    /* $xml .= '<?xml-stylesheet type="text/xsl" href="CDA.xsl"?>'; */
    $xml .= '<ClinicalDocument xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 xmlns="urn:hl7-org:v3"
 xmlns:voc="urn:hl7-org:v3/voc"
 xmlns:sdtc="urn:hl7-org:sdtc">
  <!-- QRDA Header -->
  <realmCode code="US"/>
  <typeId root="2.16.840.1.113883.1.3" extension="POCD_HD000040"/>
  <!-- US Realm Header Template Id -->
  <templateId root="2.16.840.1.113883.10.20.22.1.1" extension="2015-08-01" />
  <!-- QRDA templateId -->
  <templateId root="2.16.840.1.113883.10.20.24.1.1" extension="2016-02-01" />
  <!-- QDM-based QRDA templateId -->
  <templateId root="2.16.840.1.113883.10.20.24.1.2" extension="2016-08-01"/>
  
    <!-- CMS QRDA templateId -->
    <templateId root="2.16.840.1.113883.10.20.24.1.3" extension="2017-07-01" />
  
  <!-- This is the globally unique identifier for this QRDA document -->
  <id root="5477045d-f94e-4c7d-9072-72757ba42db0"/>
  <!-- QRDA document type code -->
  <code code="55182-0" codeSystem="2.16.840.1.113883.6.1" codeSystemName="LOINC" displayName="Quality Measure Report"/>
  <title>QRDA Incidence Report</title>
  <!-- This is the document creation time -->
  <effectiveTime value="' . $currentDate . '"/>
  <confidentialityCode code="N" codeSystem="2.16.840.1.113883.5.25"/>
  <languageCode code="en"/>';

    $xml .= $XMLpatient_data;
    $xml .= $XML_author_data;
    $xml .= $XML_custodian_data;
    $xml .= $XML_leagalauth_data;
    $xml .= $XML_documentationof_data; // CARE TEAM MEMBERS
    $xml .= $XML_cda_body;
    $xml .= '</ClinicalDocument>';

    $XML_file_name = "qrda_xml/qrda_cat1/" .$pid . '_' . $rowPatient['fname'] . '_' . $rowPatient['lname'] . ".xml";
    $rqfileName_arr[] = $pid . '_' . $rowPatient['fname'] . '_' . $rowPatient['lname'] . ".xml";

    for ($i = 0; $i <= count($con_nqf_id_arr); $i++) {
        if ($con_nqf_id_arr[$i] != "") {
            $con_nqf_id_name = $con_nqf_id_arr[$i];
            if ($con_nqf_id_arr[$i] == "NQF0421a" || $con_nqf_id_arr[$i] == "NQF0421b" || $con_nqf_id_arr[$i] == "NQF0421") {
                $con_nqf_id_name = "NQF0421";
            }
            if ($con_nqf_id_arr[$i] == "NQF0022a" || $con_nqf_id_arr[$i] == "NQF0022b" || $con_nqf_id_arr[$i] == "NQF0022") {
                $con_nqf_id_name = "NQF0022";
            }
            if ($con_nqf_id_arr[$i] == "NQF0028a" || $con_nqf_id_arr[$i] == "NQF0028b" || $con_nqf_id_arr[$i] == "NQF0028") {
                $con_nqf_id_name = "NQF0028";
            }
            $pat_nqf_arr[$con_nqf_id_name][] = $pid . '_' . $rowPatient['fname'] . '_' . $rowPatient['lname'] . ".xml";
        }
    }

    file_put_contents($XML_file_name, $xml);
}

if ($XML_file_name != "") {
    $rqZipfileName_arr = array();
    $file_path = 'qrda_xml/qrda_cat1/';
    for ($i = 0; $i < count($all_nqf_arr); $i++) {
        if ($all_nqf_arr[$i] !== "") {
            if (count($pat_nqf_arr[$all_nqf_arr[$i]]) > 0) {
                $file_names = $pat_nqf_arr[$all_nqf_arr[$i]];
                $archive_file_name = $all_nqf_arr[$i] . '.zip';
                zipFilesAndDownload($file_names, $archive_file_name, $file_path);
                $rqZipfileName_arr[] = $archive_file_name;
            }
        }
    }

    $archive_file_name = 'qrda_cat1.zip';
    $file_names = $rqZipfileName_arr;
    zipFilesAndDownload($file_names, $archive_file_name, $file_path, 'yes');
}
?>

<?php

//  FUNCTIONS
function destroy($dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $mydir = @opendir($dir);
    while (false !== ($file = readdir($mydir))) {
        if ($file != "." && $file != "..") {
            @unlink($dir . '/' . $file);
        }
    }

    $file_hcfa = 'CDA.xsl';
    $newfile_hcfa = 'qrda_xml/qrda_cat1/CDA.xsl';
    @copy($file_hcfa, $newfile_hcfa);
}

function zipFilesAndDownload($file_names, $archive_file_name, $file_path, $download_status) {

    $zip = new ZipArchive();
    //create the file and throw the error if unsuccessful
    if ($zip->open($file_path . $archive_file_name, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE) !== TRUE) {
        exit("cannot open <$archive_file_name>\n");
    }
    //add each files of $file_name array to archive

    foreach ($file_names as $files) {
        $zip->addFile($file_path . $files, $files);
        //echo $file_path.$files."<br>";
    }//die();
    $zip->close();
    //then send the headers to foce download the zip file
    if ($download_status == "yes") {
	ob_end_clean();
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private", false);
        header("Content-type: application/zip");
        header("Content-Disposition: attachment; filename=$archive_file_name");
        @readfile("$file_path" . "$archive_file_name") or die("File not found.");
        exit;
    }
}

function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= pow(1024, $pow);

    return round($bytes, $precision) . ' ' . $units[$pow];
}

function set_attach_dir_path() {
    $upload_dir = dirname(__FILE__) . "/../../main/uploaddir/users";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0700);
    }
    $uDir = $upload_dir . "/UserId_" . $_SESSION['authId'];
    if (!is_dir($uDir)) {
        mkdir($uDir, 0700);
    }
    $uDirMailAttach = $upload_dir . "/UserId_" . $_SESSION['authId'] . "/mails";
    if (!is_dir($uDirMailAttach)) {
        mkdir($uDirMailAttach, 0700);
    }
    $save_directory = $uDir . "/";
    return $save_directory;
}

function marr_status_srh($val) {
    $val = trim($val);
    $arrMartitalStatus = array(
        array("imw" => 'married', "code" => "M", "display_name" => "Married"),
        array("imw" => 'single', "code" => "S", "display_name" => "Never Married"),
        array("imw" => 'divorced', "code" => "D", "display_name" => "Divorced"),
        array("imw" => 'widowed,widow', "code" => "W", "display_name" => "Widowed"),
        array("imw" => 'separated', "code" => "L", "display_name" => "Legally Separated"),
        array("imw" => 'domestic partner', "code" => "T", "display_name" => "Domestic Partner")
    );
    $arr = array();
    if ($val != "") {
        foreach ($arrMartitalStatus as $row) {
            $arr = explode(',', $row['imw']);
            if (in_array($val, $row)) {
                $arr['code'] = $row['code'];
                $arr['display_name'] = $row['display_name'];
                break;
            }
        }
    }
    return $arr;
}

function gender_srh($val) {
    $val = trim($val);
    $arrGender = array(
        array("imw" => 'male', "code" => "M", "display_name" => "Male"),
        array("imw" => 'female', "code" => "F", "display_name" => "Female")
    );
    $arr = array();
    if ($val != "") {
        foreach ($arrGender as $row) {
            if (in_array($val, $row)) {
                $arr['code'] = $row['code'];
                $arr['display_name'] = $row['display_name'];
                break;
            } else {
                $arr['code'] = "UN";
                $arr['display_name'] = "Undifferentiated";
            }
        }
    }
    return $arr;
}

function race_srh($val) {
    $val = trim(strtolower($val));
    $arrRace = array(
        array("imw" => 'american indian or alaska native', "code" => "1002-5", "display_name" => "American Indian or Alaska Native"),
        array("imw" => 'asian', "code" => "2028-9", "display_name" => "Asian"),
        array("imw" => 'black or african american', "code" => "2054-5", "display_name" => "Black or African American"),
        array("imw" => 'native hawaiian or other pacific islander', "code" => "2076-8", "display_name" => "Native Hawaiian or Other Pacific Islander"),
        array("imw" => 'latin american', "code" => "2178-2", "display_name" => "Latin American"),
        array("imw" => 'white', "code" => "2106-3", "display_name" => "White"),
    );
    $arr = array();
    if ($val != "") {
        foreach ($arrRace as $row) {
            $arr = explode(',', $row['imw']);
            if (in_array($val, $row)) {
                $arr['code'] = $row['code'];
                $arr['display_name'] = $row['display_name'];
                break;
            } else {
                $arr['code'] = "2131-1";
                $arr['display_name'] = "Other Race";
            }
        }
    }
    return $arr;
}

function ethnicity_srh($val) {
    $val = trim($val);
    $arrRace = array(
        array("imw" => 'hispanic or latino', "code" => "2135-2", "display_name" => "Hispanic or Latino")
    );
    $arr = array();
    if ($val != "") {
        foreach ($arrRace as $row) {
            if (in_array($val, $row)) {
                $arr['code'] = $row['code'];
                $arr['display_name'] = $row['display_name'];
                break;
            } else {
                $arr['code'] = "2186-5";
                $arr['display_name'] = "Not Hispanic or Latino";
            }
        }
    }
    return $arr;
}

function language_srh($val) {
    $val = trim($val);
    $arrLang = array(
        array("imw" => 'english', "code" => "eng", "display_name" => "English"),
        array("imw" => 'spanish', "code" => "spa", "display_name" => "Spanish"),
        array("imw" => 'japanese', "code" => "jpn", "display_name" => "Japanese"),
        array("imw" => 'french', "code" => "fre", "display_name" => "French"),
        array("imw" => 'italian', "code" => "ita", "display_name" => "Italian"),
        array("imw" => 'portuguese', "code" => "por", "display_name" => "Portuguese"),
        array("imw" => 'german', "code" => "gem", "display_name" => "Germanic languages"),
        array("imw" => 'russian', "code" => "rus", "display_name" => "Russian")
    );
    $arr = array();
    if ($val != "") {
        foreach ($arrLang as $row) {
            if (in_array($val, $row)) {
                $arr['code'] = $row['code'];
                $arr['display_name'] = $row['display_name'];
                break;
            } else {
                $arr['code'] = "";
                $arr['display_name'] = "";
            }
        }
    }
    return $arr;
}

function smoking_status_srh($val) { // 	 SNOMED CT
    $val = trim($val);
    $arrSmoking = array(
        array("imw" => 'current every day smoker', "code" => "449868002", "display_name" => "Current every day smoker"),
        array("imw" => 'current some day smoker', "code" => "428041000124106", "display_name" => "Current some day smoker"),
        array("imw" => 'former smoker', "code" => "8517006", "display_name" => "Former smoker"),
        array("imw" => 'never smoked', "code" => "266919005", "display_name" => "Never smoker"),
        array("imw" => 'smoker, current status unknown', "code" => "77176002", "display_name" => "Smoker, current status unknown"),
        array("imw" => 'unknown if ever smoked', "code" => "266927001", "display_name" => "Unknown if ever smoked"),
        array("imw" => 'heavy tobacco smoke', "code" => "428071000124103", "display_name" => "Heavy tobacco smoker"),
        array("imw" => 'light tobacco smoker', "code" => "428061000124105", "display_name" => "Light tobacco smoker")
    );
    $arr = array();
    foreach ($arrSmoking as $row) {
        if (in_array($val, $row)) {
            $arr['code'] = $row['code'];
            $arr['display_name'] = $row['display_name'];
            break;
        } else {
            $arr['code'] = "266927001";
            $arr['display_name'] = "Unknown if ever smoked";
        }
    }
    return $arr;
}

function problem_type_srh($val) {   // 	 SNOMED CT
    $val = trim(strtolower($val));
    $arrProbType = array(
        array("imw" => 'finding', "code" => "404684003", "display_name" => "Finding"),
        array("imw" => 'complaint', "code" => "409586006", "display_name" => "Complaint"),
        array("imw" => 'diagnosis', "code" => "282291009", "display_name" => "Diagnosis"),
        array("imw" => 'condition', "code" => "64572001", "display_name" => "Condition"),
        array("imw" => 'smoker, current status unknown', "code" => "248536006", "display_name" => "Finding of functional performance and activity"),
        array("imw" => 'symptom', "code" => "418799008", "display_name" => "Symptom"),
        array("imw" => 'problem', "code" => "55607006", "display_name" => "Problem"),
        array("imw" => 'cognitive function finding', "code" => "373930000", "display_name" => "Cognitive function finding")
    );
    $arr = array();
    if ($val != "") {
        foreach ($arrProbType as $row) {
            if (in_array($val, $row)) {
                $arr['code'] = $row['code'];
                $arr['display_name'] = $row['display_name'];
                break;
            } else {
                $arr['code'] = '';
                $arr['display_name'] = '';
            }
        }
    }
    return $arr;
}

function problem_status_srh($val) {   // 	 SNOMED CT
    $val = trim($val);
    $arrProbStatus = array(
        array("imw" => 'active', "code" => "active", "display_name" => "active"),
        array("imw" => 'suspended', "code" => "suspended", "display_name" => "suspended"),
        array("imw" => 'aborted', "code" => "aborted", "display_name" => "aborted"),
        array("imw" => 'completed', "code" => "completed", "display_name" => "completed")
    );
    $arr = array();
    if ($val != "") {
        foreach ($arrProbStatus as $row) {
            if (in_array($val, $row)) {
                $arr['code'] = $row['code'];
                $arr['display_name'] = $row['display_name'];
                break;
            }
        }
    }
    return $arr;
}

function allergy_type_srh($val) {  // 	 SNOMED CT
    $val = trim($val);
    $arrAllerType = array(
        /* array("imw"=>'',"code"=>"420134006","display_name"=> "Propensity to adverse reactions (disorder)"),
          array("imw"=>'',"code"=>"418038007","display_name"=> "Propensity to adverse reactions to substance (disorder)"),
          array("imw"=>'',"code"=>"419511003","display_name"=> "Propensity to adverse reactions to drug (disorder)"),
          array("imw"=>'',"code"=>"418471000","display_name"=> "Propensity to adverse reactions to food (disorder)"), */
        array("imw" => 'fdbATAllergenGroup', "code" => "419199007", "display_name" => "Allergy to substance (disorder)"),
        array("imw" => 'fdbATDrugName', "code" => "416098002", "display_name" => "Drug allergy (disorder)"),
        array("imw" => 'fdbATIngredient', "code" => "414285001", "display_name" => "Food allergy (disorder)")
            /* array("imw"=>'',"code"=>"59037007","display_name"=> "Drug intolerance (disorder)"),
              array("imw"=>'',"code"=>"235719002","display_name"=> "Food intolerance (disorder)") */
    );
    $arr = array();
    if ($val != "") {
        foreach ($arrAllerType as $row) {
            if (in_array($val, $row)) {
                $arr['code'] = $row['code'];
                $arr['display_name'] = $row['display_name'];
                break;
            }
        }
    }
    return $arr;
}

function result_status_srh($val) {  // 	 SNOMED CT
    $val = trim($val);
    $arrResultStatus = array(
        //array("imw"=>'3',"code"=>"aborted","display_name"=> "aborted"),
        array("imw" => '1', "code" => "active", "display_name" => "active"),
        array("imw" => '3', "code" => "cancelled", "display_name" => "cancelled"),
        array("imw" => '2', "code" => "completed", "display_name" => "completed"),
            //array("imw"=>'held',"code"=>"held","display_name"=> "held"),
            //array("imw"=>'suspended',"code"=>"suspended","display_name"=> "suspended")

            /* array("imw"=>'',"code"=>"59037007","display_name"=> "Drug intolerance (disorder)"),
              array("imw"=>'',"code"=>"235719002","display_name"=> "Food intolerance (disorder)") */
    );
    $arr = array();
    if ($val != "") {
        foreach ($arrResultStatus as $row) {
            if (in_array($val, $row)) {
                $arr['code'] = $row['code'];
                $arr['display_name'] = $row['display_name'];
                break;
            }
        }
    }
    return $arr;
}

function test_result_status_srh($val) {  // 	 SNOMED CT
    $val = trim($val);
    $arrResultStatus = array(
        array("imw" => 'active', "code" => "active", "display_name" => "active"),
        array("imw" => 'cancelled', "code" => "cancelled", "display_name" => "cancelled"),
        array("imw" => 'completed', "code" => "completed", "display_name" => "completed"),
        array("imw" => 'aborted', "code" => "aborted", "display_name" => "aborted"),
        array("imw" => 'held', "code" => "held", "display_name" => "held"),
        array("imw" => 'suspended', "code" => "suspended", "display_name" => "suspended")
            //array("imw"=>'held',"code"=>"held","display_name"=> "held"),
            //array("imw"=>'suspended',"code"=>"suspended","display_name"=> "suspended")
            /* array("imw"=>'',"code"=>"59037007","display_name"=> "Drug intolerance (disorder)"),
              array("imw"=>'',"code"=>"235719002","display_name"=> "Food intolerance (disorder)") */
    );
    $arr = array();
    if ($val != "") {
        foreach ($arrResultStatus as $row) {
            if (in_array($val, $row)) {
                $arr['code'] = $row['code'];
                $arr['display_name'] = $row['display_name'];
                break;
            }
        }
    }
    return $arr;
}

function vs_result_type_srh($val) {
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

function get_medical_data($form_id = '', $arrType, $pid) {
    $strType = implode(',', $arrType);
    $dataFinal = array();
    if (isset($form_id) && $form_id != '') {
        $sql_arc = "select lists 
				from  
				chart_genhealth_archive 
				where patient_id='" . $pid . "' and
				form_id = '" . $form_id . "'";
    } else {
        $sql_list = "select * ,
						date_format(begdate,'%m/%d/%y') as DateStart from lists where pid='" . $pid . "' and
						allergy_status = 'Active' and type in($strType) order by id";
    }
    if ($sql_list != "") {
        $res_list = imw_query($sql_list);
        while ($row_list = imw_fetch_assoc($res_list)) {
            $dataFinal[] = $row_list;
        }
    }
    if ($sql_arc != "") {
        $res_arc = imw_query($sql_arc);

        $dataFinal = array();
        while ($row_arc = imw_fetch_assoc($res_arc)) {
            $arrList = unserialize($row_arc['lists']);
            foreach ($arrList as $arrData) {
                foreach ($arrData as $data) {
                    if (in_array($data['type'], $arrType)) {
                        if ($data['allergy_status'] == 'Active') {
                            $dataFinal[] = $data;
                        }
                    }
                }
            }
        }
    }
    return $dataFinal;
}

function get_pt_problem_list($pid, $dtfrom1, $dtupto1) {
    $dataFinal = array();
    $sql = "SELECT * FROM pt_problem_list WHERE pt_id IN($pid) AND status='Active' AND form_id = 0";
    if ($sql != "") {
        $res = imw_query($sql);
        while ($row = imw_fetch_assoc($res)) {
            $dataFinal[] = $row;
        }
    }
    if ($sql_arc != "") {
        $res_arc = imw_query($sql_arc);

        $dataFinal = array();
        while ($row_arc = imw_fetch_assoc($res_arc)) {
            $arrList = unserialize($row_arc['pt_problem_list']);
            foreach ($arrList as $arrData) {
                if ($arrData['status'] == 'Active') {
                    $dataFinal[] = $arrData;
                }
            }
        }
    }
    return $dataFinal;
}

function getSite($val) {
    switch ($val) {
        case "1":
            $site = "OS";
            break;
        case "2":
            $site = "OD";
            break;
        case "3":
            $site = "OU";
            break;
        case "4":
            $site = "PO";
            break;
    }
    return $site;
}

function getRXNormCode($str) {
    $arr = array();
    $sql = "select RXCUI,STR from " . constant("UMLS_DB") . ".rxnconso where STR = '" . $str . "' and SAB='RXNORM'";
    $res = imw_query($sql);
    if (imw_num_rows($res) > 0) {
        $row = imw_fetch_assoc($res);
        $arr['ccda_code'] = $row['RXCUI'];
        $arr['ccda_display_name'] = $str;
    } else {
        $medNameTemp = "";
        $medNameTemp = trim($str);
        $medNameTemp = str_replace("-", " ", $medNameTemp);

        $arrMedictionName = explode(" ", $medNameTemp);
        $qryMore = "";
        if (count($arrMedictionName) > 1) {
            foreach ($arrMedictionName as $val) {
                $qryMore .= " `STR` LIKE '%$val%' and";
            }
        }
        $qryMore = substr(trim($qryMore), 0, -3);
        $sql = "select RXCUI,STR from " . constant("UMLS_DB") . ".rxnconso where '" . $qryMore . "' and SAB='RXNORM'  LIMIT 1";
        $res = imw_query($sql);
        $row = imw_fetch_assoc($res);
        $arr['ccda_code'] = $row['RXCUI'];
        $arr['ccda_display_name'] = $str;
    }
    return $arr;
}

function getRXNorm_by_code($ccda_code) {
    $arr = array();
    $sql = "select RXCUI,STR from  " . constant("UMLS_DB") . ".rxnconso where RXCUI = '" . $ccda_code . "' and SAB='RXNORM'";
    $res = imw_query($sql);
    $row = imw_fetch_assoc($res);
    $arr['ccda_code'] = $ccda_code;
    $arr['ccda_display_name'] = $row['STR'];
    return $arr;
}

function getRouteCode($route) {
    global $routeset_codes, $routeset_nci_codes;
    $arr = array();
    if ($routeset_codes[$route] != "") {
        $arr['ccda_code'] = $routeset_nci_codes[$route];
        $arr['ccda_display_name'] = $routeset_codes[$route];
    } else {
        $sql = "select code,term from  " . constant("UMLS_DB") . ".route_nci_thesaurus where LOWER(term) = '" . strtolower($route) . "' OR LOWER(code) = '" . strtolower($route) . "'";
        $res = imw_query($sql);
        $row = imw_fetch_assoc($res);
        $arr['ccda_code'] = $row['code'];
        $arr['ccda_display_name'] = $row['term'];
    }
    return $arr;
}

function getApproachSiteCode($site) {
    global $bodysite_codes, $bodysite_snomed_codes;
    $arr = array();
    if ($bodysite_codes[$site] != "") {
        $arr['ccda_code'] = $bodysite_snomed_codes[$route];
        $arr['ccda_display_name'] = $bodysite_codes[$route];
    }
    return $arr;
}

function getProblemCode($str) {
    $arr = array();
    $sql = "select Concept_Code,Preferred_Concept_Name from " . constant("UMLS_DB") . ".problem_list where (Concept_Name = '" . $str . "' or Preferred_Concept_Name ='" . $str . "') and Code_System_OID = '2.16.840.1.113883.6.96'";
    $res = imw_query($sql);
    if (imw_num_rows($res) > 0) {
        $row = imw_fetch_assoc($res);
        $arr['ccda_code'] = $row['Concept_Code'];
        $arr['ccda_display_name'] = $row['Preferred_Concept_Name'];
    } else {
        $tmp = trim($str);
        $tmp = str_replace("-", " ", $tmp);

        $arrTmp = explode(" ", $tmp);
        $qryMore = "";
        if (count($arrTmp) > 1) {
            foreach ($arrTmp as $val) {
                $qryMore .= "(`Concept_Name` LIKE '%$val%' or Preferred_Concept_Name LIKE '%$val%')";
            }
        }
        $qryMore = substr(trim($qryMore), 0, -3);
        $sql = "select Concept_Code,Preferred_Concept_Name from " . constant("UMLS_DB") . ".problem_list where '" . $qryMore . "' and and Code_System_OID = '2.16.840.1.113883.6.96' LIMIT 1";
        $res = imw_query($sql);
        $row = imw_fetch_assoc($res);
        $arr['ccda_code'] = $row['Concept_Code'];
        $arr['ccda_display_name'] = $row['Preferred_Concept_Name'];
    }
    return $arr;
}

function get_functional_status($val) {
    $arr = array();
    $val = trim($val);
    if ($val == "NE") {
        $arr['code'] = "";
        $arr['display_name'] = "Not Evaluated";
    } else if ($val == 0) {
        $arr['code'] = "66557003";
        $arr['display_name'] = "No Disability";
    } else if ($val >= 10 && $val <= 30) {
        $arr['code'] = "161043008";
        $arr['display_name'] = "Mild Disability";
    } else if ($val >= 40 && $val <= 70) {
        $arr['code'] = "161044002";
        $arr['display_name'] = "Moderate Disability";
    } else if ($val >= 80 && $val <= 100) {
        $arr['code'] = "161045001";
        $arr['display_name'] = "Severe Disability";
    }
    return $arr;
}

function get_cognitive_status($val) {
    $val = trim($val);
    $arrResultStatus = array(
        array("imw" => 'Alert', "code" => "248233002", "display_name" => "Alert"),
        array("imw" => 'Oriented X3', "code" => "426224004", "display_name" => "No Disability"),
        array("imw" => 'Confused', "code" => "162702000", "display_name" => "Slight Disability"),
        array("imw" => 'Agitated', "code" => "162721008", "display_name" => "Moderate Disability"),
        array("imw" => 'Flat Affect', "code" => "932006", "display_name" => "Severe Disability"),
        array("imw" => 'Uncooperative', "code" => "248042003", "display_name" => "Severe Disability"),
        array("imw" => 'Mentally Retarded', "code" => "419723007", "display_name" => "Severe Disability")
    );
    $arr = array();
    if ($val != "") {
        foreach ($arrResultStatus as $row) {
            if (in_array($val, $row)) {
                $arr['code'] = $row['code'];
                $arr['display_name'] = $row['display_name'];
                break;
            }
        }
    }
    return $arr;
}

function get_provider_code($val) {
    $val = trim($val);
    $arrResultStatus = array(
        array("imw" => 'Attending Physician', "code" => "405279007", "display_name" => "Attending physician"),
        array("imw" => 'Physician', "code" => "309343006", "display_name" => "Physician"),
        array("imw" => 'Resident', "code" => "405277009", "display_name" => "Resident physician"),
        array("imw" => 'Consultant', "code" => "158967008", "display_name" => "Consultant physician")
    );
    $arr = array();
    if ($val != "") {
        foreach ($arrResultStatus as $row) {
            if (in_array($val, $row)) {
                $arr['code'] = $row['code'];
                $arr['display_name'] = $row['display_name'];
                break;
            }
        }
    }
    return $arr;
}

function fetchValueSet($code,$pid,$pages = array()) {
	
	$page_qry = '';
	if( $pages )
	{
		if( is_array($pages) && count($pages) > 0)
			$pageString = "'".implode("','",$pages)."'";
		else
			$pageString = trim($pages);
				
		if( $pageString ) $page_qry = "And page = '".$pageString."' ";
	}
    $valuetype = '';
    $valueset = '';
    $valueset_text = '';
    $code_system = '';
    $page = '';
    $sql = "select * from snomed_valueset WHERE code='{$code}' AND pid='{$pid}'".$page_qry;
    $res = imw_query($sql);
    if (imw_num_rows($res) > 0) {
        while ($row = imw_fetch_assoc($res)) {
            $valuetype = $row['type'];
            $valueset = $row['value_set'];
            $valueset_text = $row['valueset_text'];
            $code_system = $row['code_system'];
            $page = $row['page'];
        }
    } else {
        $sql = "select * from snomed_valueset WHERE code='{$code}' AND pid='0' ";
        $res = imw_query($sql);
        
        while ($row = imw_fetch_assoc($res)) {
            $valuetype = $row['type'];
            $valueset = $row['value_set'];
            $valueset_text = $row['valueset_text'];
            $code_system = $row['code_system'];
            $page = $row['page'];
        }
    }
    $valueset_arr['value_code'] = $code;
    $valueset_arr['type'] = $valuetype;
    $valueset_arr['value_set'] = $valueset;
    $valueset_arr['valueset_text'] = $valueset_text;
    $valueset_arr['code_system'] = $code_system;
    $valueset_arr['page'] = $page;

    return $valueset_arr;
}
?>


