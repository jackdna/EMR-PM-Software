<?php
include_once(dirname(__FILE__) . "/../../../config/globals.php");
include_once(dirname(__FILE__) . "/../../../library/classes/class.mur_reports.php");
include(dirname(__FILE__) . "/../../../library/classes/AES.class.php");

$library_path = $GLOBALS['webroot'] . '/library';
$objMUR = new MUR_Reports;


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
$extension = "600";
$all_nqf_arr = array("NQF0018", "NQF0022", "NQF0421", "CMS50v2", "NQF0028", "NQF0052", "NQF0055", "NQF0086", "NQF0088", "NQF0089");
//$all_nqf_arr=array("NQF0018","NQF0022","NQF419","NQF565","NQF0028","NQF564","NQF0055","NQF0086","NQF0088","NQF0089");
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
    $sql = "SELECT appt.sa_app_start_date,appt.sa_app_starttime,appt.sa_app_endtime
			FROM schedule_appointments appt 
			WHERE appt.sa_patient_id in('" . $pid . "')
			AND appt.sa_patient_app_status_id NOT IN(203,201,18,19,20,3)";
    $enc_qry = imw_query($sql);
    while ($enc_row = imw_fetch_array($enc_qry)) {
        $sa_app_start_date = $enc_row['sa_app_start_date'];



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
        while ($fet_sup = imw_fetch_array($qry_sup)) {
            $cpt_Code = $fet_sup['cpt_Code'];
            $XML_encouter_entry .= '<entry>';
            $XML_encouter_entry .= '<act classCode="ACT" moodCode="EVN" >
                                <!--Encounter performed Act -->
                                <templateId root="2.16.840.1.113883.10.20.24.3.133"/>
                                <id root="1.3.6.1.4.1.115"  extension="5a1b8c7dcde4a30025d3d9a5"/>
                                <code code="ENC" codeSystem="2.16.840.1.113883.5.6" displayName="Encounter" codeSystemName="ActClass"/>
                                <entryRelationship typeCode="SUBJ">';
            $XML_encouter_entry .= '<encounter classCode="ENC" moodCode="EVN">';
            /* BEGIN ENCOUNTER ACTIVITIES */
            $XML_encouter_entry .= '<!-- Encounter Activities -->';
            $XML_encouter_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.49" extension="2015-08-01"/>';
            /* Encounter performed template */
            $XML_encouter_entry .= '<templateId root="2.16.840.1.113883.10.20.24.3.23" extension="2016-02-01"/>';

            //$XML_encouter_entry .= '<id nullFlavor="NI"/>';
            $XML_encouter_entry .= '<id root="1.3.6.1.4.1.115" extension="5a1b8c7dcde4a30025d3d9a5"/>';


            if ($cpt_Code == "92002") {
                $XML_encouter_entry .= '<code code="' . $cpt_Code . '" codeSystem="2.16.840.1.113883.6.12" sdtc:valueSet="2.16.840.1.113883.3.526.3.1285">
				<originalText>Encounter, Performed: Ophthalmological Services (Code List: 2.16.840.1.113883.3.526.3.1285)</originalText></code>
    			<text>Encounter, Performed: Ophthalmological Services (Code List: 2.16.840.1.113883.3.526.3.1285)</text>
				<statusCode code="completed"/>';
            } else {
                $XML_encouter_entry .= '<code code="' . $cpt_Code . '" codeSystem="2.16.840.1.113883.6.12" sdtc:valueSet="2.16.840.1.113883.3.464.1003.101.12.1001">
				<originalText>Encounter, Performed: Office Visit (Code List: 2.16.840.1.113883.3.464.1003.101.12.1001)</originalText></code>
				<text>Encounter, Performed: Office Visit (Code List: 2.16.840.1.113883.3.464.1003.101.12.1001)</text>
				<statusCode code="completed"/>';
            }
            //}

            $exp_sa_app_start_date = str_replace('-', '', $enc_row['sa_app_start_date']);
            $low_val = $exp_sa_app_start_date . str_replace(':', '', $enc_row['sa_app_starttime']);
            $high_val = $exp_sa_app_start_date . str_replace(':', '', $enc_row['sa_app_endtime']);

            if ($enc_row['sa_app_start_date'] != "") {
                $XML_encouter_entry .= '<effectiveTime>';
                $XML_encouter_entry .= '<low value="' . $low_val . '"/>';
                $XML_encouter_entry .= '<high value="' . $high_val . '"/></effectiveTime>';
            } else {
                $XML_encouter_entry .= '<effectiveTime nullFlavor="NI"/>';
            }

            /* END ENCOUNTER ACTIVITIES */
            $XML_encouter_entry .= '</encounter>';
            $XML_encouter_entry .= '</entryRelationship>';
            $XML_encouter_entry .= '</act>';
            $XML_encouter_entry .= '</entry>';
        }
        if ($cpt_Code == "99201") {

            $XML_encouter_entry .= '<entry>';
            $extension++;
            $XML_encouter_entry .= '<act classCode="ACT" moodCode="EVN" >
                            <!--Encounter performed Act -->
                            <templateId root="2.16.840.1.113883.10.20.24.3.133"/>
                            <id root="1.3.6.1.4.1.115"  extension="5a1b8c7dcde4a30025d3d9a5"/>
                            <code code="ENC" codeSystem="2.16.840.1.113883.5.6" displayName="Encounter" codeSystemName="ActClass"/>
                            <entryRelationship typeCode="SUBJ">';
            $XML_encouter_entry .= '<encounter classCode="ENC" moodCode="EVN">';
            /* BEGIN ENCOUNTER ACTIVITIES */
            $XML_encouter_entry .= '<!-- Encounter Activities -->';
            $XML_encouter_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.49" extension="2015-08-01"/>';
            /* Encounter performed template */
            $XML_encouter_entry .= '<templateId root="2.16.840.1.113883.10.20.24.3.23" extension="2016-02-01" />';

            $XML_encouter_entry .= '<id root="1.3.6.1.4.1.115" extension="5a1b8c7dcde4a30025d3d9a5"/>';

            $XML_encouter_entry .= '<code code="99201" codeSystem="2.16.840.1.113883.6.12" sdtc:valueSet="2.16.840.1.113883.3.600.1.1751">
					<originalText>Encounter, Performed: Office Visit (Code List: 2.16.840.1.113883.3.464.1003.101.12.1001)</originalText></code>
					<text>Encounter, Performed: Office Visit (Code List: 2.16.840.1.113883.3.464.1003.101.12.1001)</text>
					<statusCode code="completed"/>';

            if ($enc_row['sa_app_start_date'] != "") {
                $XML_encouter_entry .= '<effectiveTime>';
                $XML_encouter_entry .= '<low value="' . $low_val . '"/>';
                $XML_encouter_entry .= '<high value="' . $high_val . '"/></effectiveTime>';
            } else {
                $XML_encouter_entry .= '<effectiveTime nullFlavor="NI"/>';
            }

            /* END ENCOUNTER ACTIVITIES */
            $XML_encouter_entry .= '</encounter>';
            $XML_encouter_entry .= '</entryRelationship>';
            $XML_encouter_entry .= '</act>';
            $XML_encouter_entry .= '</entry>';
        }
    }

    /* END ENOCUTERS SECTION */

    /* BEGIN Communication from provider to provider SECTION */

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
        /* Communication from provider to provider */
        $XML_pro_comm_entry .= '<templateId root="2.16.840.1.113883.10.20.24.3.4"/>';
        $XML_pro_comm_entry .= ' <id nullFlavor="NI"/>';

        $arrProviderType = get_provider_code($query3_row['user_type']);

        /* DYNAMIC ENCOUNTER TYPE CODE TO GET FROM CHART ENTRY */
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
          </participant>'; */


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
        /* Communication from provider to provider */
        $XML_pro_comm_entry .= '<templateId root="2.16.840.1.113883.10.20.24.3.4"/>';
        $XML_pro_comm_entry .= ' <id nullFlavor="NI"/>';

        $arrProviderType = get_provider_code($query3_row['user_type']);

        /* DYNAMIC ENCOUNTER TYPE CODE TO GET FROM CHART ENTRY */
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
          </participant>'; */


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
        /* Communication from provider to provider */
        $XML_pro_comm_entry .= '<templateId root="2.16.840.1.113883.10.20.24.3.4"/>';
        $XML_pro_comm_entry .= ' <id nullFlavor="NI"/>';

        $arrProviderType = get_provider_code($query3_row['user_type']);

        /* DYNAMIC ENCOUNTER TYPE CODE TO GET FROM CHART ENTRY */
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
          </participant>'; */


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

    /* END Communication from provider to provider SECTION */

    /* BEGIN Diagnostic Study, Result SECTION */
    $query3 = "SELECT date_format(crv.exam_date,'%Y-%m-%d') as exam_date_new,retinal_od_summary,retinal_os_summary FROM chart_retinal_exam crv 
				WHERE crv.patient_id IN($pid) 
				AND (LOWER(retinal_od) like '%macular edema%' OR LOWER(retinal_os) like '%macular edema%')";
    $query3_run = imw_query($query3);
    while ($query3_row = imw_fetch_array($query3_run)) {

        if (strstr($query3_row['retinal_od_summary'], 'Absent Macular edema') || strstr($query3_row['retinal_os_summary'], 'Absent Macular edema')) {
            $macular_status = "Absent";
            $crv_valueset = "2.16.840.1.113883.3.526.3.1284";
            $crv_value_code = "428341000124108";
        } else {
            $macular_status = "Present";
            $crv_valueset = "2.16.840.1.113883.3.526.3.1320";
            $crv_value_code = "193350004";
        }
        $XML_test_entry .= '<entry>';
        $XML_test_entry .= '<observation classCode="OBS" moodCode="EVN">';
        $XML_test_entry .= ' <!-- Consolidated Result Observation templateId (Implied Template) -->';
        $XML_test_entry .= ' <templateId root="2.16.840.1.113883.10.20.22.4.2"/>';
        $XML_test_entry .= ' <templateId root="2.16.840.1.113883.10.20.24.3.20"/>';
        $XML_test_entry .= '<id nullFlavor="NI"/>';


        $XML_test_entry .= '<code code="32451-7" codeSystem="2.16.840.1.113883.6.1" xsi:type="CD" sdtc:valueSet="2.16.840.1.113883.3.526.3.1251">
							<originalText>Diagnostic Study Result: Macular Exam (Code List: 2.16.840.1.113883.3.526.3.1251)</originalText>
							</code><text>Diagnostic Study Result: Macular Exam (Code List: 2.16.840.1.113883.3.526.3.1251)</text>';
        $XML_test_entry .= '<statusCode code="completed"/>';

        $XML_test_entry .= '<effectiveTime>';
        $XML_test_entry .= ' <low value="' . str_replace('-', '', $query3_row['exam_date_new']) . '"/>';
        $XML_test_entry .= '</effectiveTime>';

        $XML_test_entry .= '<value code="' . $crv_value_code . '" codeSystem="2.16.840.1.113883.6.96" xsi:type="CD">
							<originalText>Macular Edema Findings ' . $macular_status . '</originalText></value>';
        $XML_test_entry .= '</act></entry>';
    }

    $query3 = "SELECT date_format(exam_date,'%Y-%m-%d') as exam_date_new,cd_val_od,cd_val_os 
				FROM chart_optic 
				WHERE patient_id IN($pid)";
    $query3_run = imw_query($query3);
    while ($query3_row = imw_fetch_array($query3_run)) {

        if ($query3_row['cd_val_od'] != "") {
            $cd_PQ_val = $query3_row['cd_val_od'];
        } else {
            $cd_PQ_val = $query3_row['cd_val_os'];
        }
        if ($cd_PQ_val != "") {
            $XML_test_entry .= '<entry>';
            $XML_test_entry .= '<observation classCode="OBS" moodCode="EVN">';
            $XML_test_entry .= ' <!-- Consolidated Result Observation templateId (Implied Template) -->';
            $XML_test_entry .= ' <templateId root="2.16.840.1.113883.10.20.22.4.2"/>';
            $XML_test_entry .= ' <templateId root="2.16.840.1.113883.10.20.24.3.20"/>';
            $XML_test_entry .= '<id nullFlavor="NI"/>';


            $XML_test_entry .= '<code code="71484-0" codeSystem="2.16.840.1.113883.6.1" xsi:type="CD" sdtc:valueSet="2.16.840.1.113883.3.526.3.1333">
								<originalText>Diagnostic Study, Result: Cup to Disc Ratio (Code List: 2.16.840.1.113883.3.526.3.1333)</originalText>
								</code><text>Diagnostic Study, Result: Cup to Disc Ratio (Code List: 2.16.840.1.113883.3.526.3.1333)</text>';
            $XML_test_entry .= '<statusCode code="completed"/>';

            $XML_test_entry .= '<effectiveTime>';
            $XML_test_entry .= ' <low value="' . str_replace('-', '', $query3_row['exam_date_new']) . '"/>';
            $XML_test_entry .= '</effectiveTime>';

            $XML_test_entry .= '<value xsi:type="PQ" value="' . $cd_PQ_val . '" />';
            $XML_test_entry .= '</observation></entry>';

            $XML_test_entry .= '<entry>';
            $XML_test_entry .= '<observation classCode="OBS" moodCode="EVN">';
            $XML_test_entry .= ' <!-- Consolidated Result Observation templateId (Implied Template) -->';
            $XML_test_entry .= ' <templateId root="2.16.840.1.113883.10.20.22.4.2"/>';
            $XML_test_entry .= ' <templateId root="2.16.840.1.113883.10.20.24.3.20"/>';
            $XML_test_entry .= '<id nullFlavor="NI"/>';


            $XML_test_entry .= '<code code="71486-5" codeSystem="2.16.840.1.113883.6.1" xsi:type="CD" sdtc:valueSet="2.16.840.1.113883.3.526.3.1334">
								<originalText>Diagnostic Study, Result: Optic Disc Exam for Structural Abnormalities (Code List: 2.16.840.1.113883.3.526.3.1334))</originalText>
								</code><text>Diagnostic Study, Result: Optic Disc Exam for Structural Abnormalities (Code List: 2.16.840.1.113883.3.526.3.1334)</text>';
            $XML_test_entry .= '<statusCode code="completed"/>';

            $XML_test_entry .= '<effectiveTime>';
            $XML_test_entry .= ' <low value="' . str_replace('-', '', $query3_row['exam_date_new']) . '"/>';
            $XML_test_entry .= '</effectiveTime>';

            $XML_test_entry .= '<value xsi:type="ST" >positive</value>';
            $XML_test_entry .= '</observation></entry>';
        }
    }

    /* END Diagnostic Study, Result SECTION */

    /* BEGIN Diagnostic Study, Result SECTION */
    $query3 = "SELECT date_format(crv.exam_date,'%Y-%m-%d') as exam_date_new FROM chart_macula crv 
				WHERE crv.patient_id IN($pid) 
				AND (LOWER(macula_od_summary) like '%macular edema%' OR LOWER(macula_os_summary) like '%macular edema%')";
    $query3_run = imw_query($query3);
    while ($query3_row = imw_fetch_array($query3_run)) {

        $XML_test_entry .= '<entry>';
        $XML_test_entry .= '<observation classCode="OBS" moodCode="EVN">';
        $XML_test_entry .= ' <!-- Consolidated Result Observation templateId (Implied Template) -->';
        $XML_test_entry .= ' <templateId root="2.16.840.1.113883.10.20.22.4.2"/>';
        $XML_test_entry .= ' <templateId root="2.16.840.1.113883.10.20.24.3.20"/>';
        $XML_test_entry .= '<id nullFlavor="NI"/>';


        $XML_test_entry .= '<code code="32451-7" codeSystem="2.16.840.1.113883.6.1" xsi:type="CD" sdtc:valueSet="2.16.840.1.113883.3.526.3.1251">
							<originalText>Diagnostic Study Result: Macular Exam (Code List: 2.16.840.1.113883.3.526.3.1251)</originalText>
							</code><text>Diagnostic Study Result: Macular Exam (Code List: 2.16.840.1.113883.3.526.3.1251)</text>';
        $XML_test_entry .= '<statusCode code="active"/>';

        $XML_test_entry .= '<effectiveTime>';
        $XML_test_entry .= ' <low value="' . str_replace('-', '', $query3_row['exam_date_new']) . '"/>';
        $XML_test_entry .= '</effectiveTime>';

        $XML_test_entry .= '<value code="428341000124108" codeSystem="2.16.840.1.113883.6.96" xsi:type="CD">
							<originalText>Macular Edema Findings Absent</originalText></value>';
        $XML_test_entry .= '</act></entry>';
    }

    /* END Diagnostic Study SECTION */

//-------BEGIN DIAGNOSTICS RAD TESTS SECTION --------------//
    $qry = "SELECT * FROM rad_test_data WHERE rad_patient_id = '" . $pid . "' and rad_status = 2";
    $res = imw_query($qry);
    while ($row = imw_fetch_assoc($res)) {
        $XML_test_entry .= '<entry typeCode="DRIV">						
						 <observation classCode="OBS" moodCode="EVN" >
    <!-- Consolidated Procedure Activity Observation templateId 
       (Implied Template) -->
    <templateId root="2.16.840.1.113883.10.20.22.4.13" extension="2014-06-09"/>
    <!-- Diagnostic Study, Performed template -->
    <templateId root="2.16.840.1.113883.10.20.24.3.18" extension="2016-02-01"/>
    <id root="1.3.6.1.4.1.115" extension="5a1e6ccecde4a364e87b0415"/>
    	<!-- <code code="' . $row['rad_loinc'] . '" codeSystem="2.16.840.1.113883.6.1" sdtc:valueSet="2.16.840.1.113883.3.464.1003.113.12.1033"> -->
    	<code code="' . $row['rad_loinc'] . '" codeSystem="2.16.840.1.113883.6.1" sdtc:valueSet="2.16.840.1.113883.3.526.3.1251">
		<originalText>Diagnostic Study, Performed: ' . $row['rad_name'] . ' (Code List: 2.16.840.1.113883.3.464.1003.113.12.1033)</originalText>
	</code>
    <text>Diagnostic Study, Performed: ' . $row['rad_name'] . ' (Code List: 2.16.840.1.113883.3.464.1003.113.12.1033)</text>
    <statusCode code="completed"/>
    <effectiveTime>
      <!-- Attribute: Start Datetime -->
      <low value="' . str_replace('-', '', $row['rad_order_date']) . '"/>
	  <high value="' . str_replace('-', '', $row['rad_order_date']) . '"/>
    </effectiveTime> <value xsi:type="CD" nullFlavor="UNK"/></observation></entry>';
    }
//-------END Diagnostic Study RAD TESTS SECTION --------------//
//-------BEGIN Interventions SECTION --------------//
    $qry = "SELECT * FROM lists WHERE pid = '" . $pid . "' and type = '5' and allergy_status='Active' and proc_type='intervention'";
    $res = imw_query($qry);
    while ($row = imw_fetch_assoc($res)) {
        $XML_intervention_entry .= '<entry>
		  <act classCode="ACT" moodCode="EVN" >
			<!-- Consolidation CDA: Procedure Activity Act template -->
			<templateId root="2.16.840.1.113883.10.20.22.4.12"/>
			<templateId root="2.16.840.1.113883.10.20.24.3.32"/>
			<id nullFlavor="NI"/>
			<code code="' . $row['ccda_code'] . '" codeSystem="2.16.840.1.113883.6.96" sdtc:valueSet="2.16.840.1.113883.3.464.1003.101.12.1046">
			<originalText>Intervention, Performed: ' . $row['title'] . ' (Code List: 2.16.840.1.113883.3.464.1003.101.12.1046)</originalText></code>
			<statusCode code="completed"/>
			<effectiveTime>
			  <low value="' . str_replace('-', '', $row['begdate']) . '"/>
			  <high value="' . str_replace('-', '', $row['begdate']) . '"/>
			</effectiveTime>
			
		  </act>
		</entry>';
    }
//-------END Interventions SECTION --------------//
//-------BEGIN Procedure SECTION --------------//
    $qry = "SELECT * FROM lists WHERE pid = '" . $pid . "' and type = '5' and allergy_status='Active' and proc_type='procedure'";
    $res = imw_query($qry);
    while ($row = imw_fetch_assoc($res)) {
        $XML_procedure_entry .= '<entry>
		  <procedure classCode="PROC" moodCode="EVN" >
			<!--  Procedure performed template -->
			<templateId root="2.16.840.1.113883.10.20.24.3.64"/>
			<!-- Procedure Activity Procedure-->
			<templateId root="2.16.840.1.113883.10.20.22.4.14"/>
			<id nullFlavor="NI"/>
			<code code="' . $row['ccda_code'] . '" codeSystem="2.16.840.1.113883.6.96" sdtc:valueSet="2.16.840.1.113883.3.464.1003.109.12.1013">
				<originalText>Procedure, Performed: ' . $row['title'] . ' (Code List: 2.16.840.1.113883.3.464.1003.109.12.1013)</originalText>';
        /* <translation code="90920" codeSystem="2.16.840.1.113883.6.12"/>
          <translation code="G0257" codeSystem="2.16.840.1.113883.6.285"/> */
        $XML_procedure_entry .= '</code>
			<text>Procedure, Performed: ' . $row['title'] . ' (Code List: 2.16.840.1.113883.3.464.1003.109.12.1013)</text>
			<statusCode code="completed"/>
			<effectiveTime>
			  <low value="' . str_replace('-', '', $row['begdate']) . '"/>
			  <high value="' . str_replace('-', '', $row['begdate']) . '"/>
			</effectiveTime>
		  </procedure>
		</entry>';
    }
//-------END Procedure SECTION --------------//

    /* BEGIN Patient Characteristics SECTION */
    $qry = imw_query("SELECT date_format(modified_on,'%Y-%m-%d') as modified_on_new,smoking_status,smoke_years_months FROM social_history WHERE patient_id = '" . $pid . "'");
    if (imw_num_rows($qry) > 0) {
        $row_social = imw_fetch_array($qry);
        $arrTmp = explode('/', $row_social['smoking_status']);
        $smoking_type = $arrTmp[0];
        $smoking_code = trim($arrTmp[1]);
        /* BEGIN SMOKING STATUS ENTRY */
        if (trim($smoking_code) == '449868002') {
            //$smoke_code_list="2.16.840.1.113883.3.600.2390";
            $smoke_code_list = "2.16.840.1.113883.3.526.3.1170";
        } else {
            $smoke_code_list = "2.16.840.1.113883.3.526.3.1170";
        }
        $modified_on_exp = explode('-', $row_social['modified_on_new']);
        $cut_year = $modified_on_exp[0];
        $cut_month = $modified_on_exp[1];
        $cut_day = $modified_on_exp[2];
        if ($row_social['smoke_years_months'] == "Years") {
            $cut_year = $cut_year - $row_social['number_of_years_with_smoke'];
        }
        if ($row_social['smoke_years_months'] == "Months") {
            $cut_month = $cut_month - $row_social['number_of_years_with_smoke'];
        }
        $smoke_start_date = date('Ymd', mktime(0, 0, 0, $cur_month, $cur_day, $cur_year));

        $XML_smoking_status_entry = '<entry>';
        $XML_smoking_status_entry .= '<observation classCode="OBS" moodCode="EVN">';
        $XML_smoking_status_entry .= '<templateId root="2.16.840.1.113883.10.20.22.4.85"/>
									<id nullFlavor="NI"/>';
        $XML_smoking_status_entry .= '<code code="ASSERTION" displayName="Assertion"  codeSystemName="ActCode" codeSystem="2.16.840.1.113883.5.4"/>';
        $XML_smoking_status_entry .= '<statusCode code="completed"/>';
        $XML_smoking_status_entry .= '<effectiveTime>';
        $XML_smoking_status_entry .= '<low value="' . $smoke_start_date . '"/><high nullFlavor="UNK"/>';
        $XML_smoking_status_entry .= '</effectiveTime>';
        $XML_smoking_status_entry .= ' <value code="' . $smoking_code . '" codeSystem="2.16.840.1.113883.6.96" xsi:type="CD" sdtc:valueSet="' . $smoke_code_list . '">
									  <originalText>Patient Characteristic: ' . $smoking_type . ' (Code List: ' . $smoke_code_list . ')</originalText>
									  </value>';
        $XML_smoking_status_entry .= '</observation>';
        $XML_smoking_status_entry .= '</entry>';

        /* END SMOKING STATUS ENTRY */
    }
    /* END Patient Characteristics SECTION */

//-------BEGIN Medication SECTION --------------//
    $qry = "SELECT * FROM lists WHERE pid = '" . $pid . "' and type in(1,4) and allergy_status='Active'";
    $res = imw_query($qry);
    while ($row = imw_fetch_assoc($res)) {
        if ($row['ccda_code'] == "1000351") {
            $code_system = "2.16.840.1.113883.3.464.1003.196.12.1253";
        } else {
            $code_system = "2.16.840.1.113883.3.526.3.1190";
        }
        $XML_procedure_entry .= '<entry>
	  <!--Medication Order -->
	  <substanceAdministration classCode="SBADM" moodCode="RQO" >
		<templateId root="2.16.840.1.113883.10.20.22.4.42"/>
		<!-- Medication, Order template -->
		<templateId root="2.16.840.1.113883.10.20.24.3.47"/>
		<id nullFlavor="NI"/>
		<text>Medication, Order: ' . $row['title'] . ' (Code List: 2.16.840.1.113883.3.526.3.1190)</text>
		<statusCode code="new"/>
		<effectiveTime xsi:type="IVL_TS">
		  <low value="' . str_replace('-', '', $row['begdate']) . '"/>
		  <high value="' . str_replace('-', '', $row['begdate']) . '"/>
		</effectiveTime>
	
		<consumable>
		  <manufacturedProduct classCode="MANU">
			<!-- Medication Information (consolidation) template -->
			<templateId root="2.16.840.1.113883.10.20.22.4.23"/>
			<id nullFlavor="NI"/>
			<manufacturedMaterial>
			  <code code="' . $row['ccda_code'] . '" codeSystem="2.16.840.1.113883.6.88" sdtc:valueSet="' . $code_system . '">
			  	<originalText>Medication, Order: ' . $row['title'] . ' (Code List: ' . $code_system . ')</originalText>
			  </code>
			</manufacturedMaterial>
		  </manufacturedProduct>
		</consumable>
		
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
            if ($problemList['ccda_code'] == "111513000") {
                $code_list = 'Diagnosis, Active: Primary Open Angle Glaucoma (POAG) (Code List: 2.16.840.1.113883.3.526.3.326)';
                $prob_valueSet = "2.16.840.1.113883.3.526.3.326";
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
                $XML_problem_section .= ' <low value="' . str_replace('-', '', $problemList['onset_date']) . '"/>';
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
            $XML_problem_section .= '<id root="1.3.6.1.4.1.115"/>';

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
                $XML_problem_section .= ' <low value="' . str_replace('-', '', $problemList['onset_date']) . '"/>';
                $XML_problem_section .= '<high nullFlavor="UNK"/></effectiveTime>';
            } else {
                $XML_problem_section .= ' <effectiveTime nullFlavor="NI"/>';
            }

            // DYNAMIC PROBLEM VALUE //
            if ($problemList['ccda_code'] != "") {
                $XML_problem_section .= '<value xsi:type="CD" code="' . $problemList['ccda_code'] . '" codeSystem="2.16.840.1.113883.6.96" 
								sdtc:valueSet="' . $prob_valueSet . '">
							   		<originalText>' . $code_list . '</originalText>';
            } else {
                $XML_problem_section .= '<value xsi:type="CD" nullFlavor="UNK">';
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
        $arr_vs_result_type = vs_result_type_srh($row_vital['vital_sign']);
        $code_list = "";
        $vs_valueSet = "";
        if ($row_vital['vital_sign'] == "B/P - Systolic") {
            $code_list = 'Physical Exam, Finding: Systolic Blood Pressure (Code List: 2.16.840.1.113883.3.526.3.1032)';
            $vs_valueSet = "2.16.840.1.113883.3.526.3.1032";
        }
        if ($row_vital['vital_sign'] == "B/P - Diastolic") {
            $code_list = 'Physical Exam, Finding: Diastolic Blood Pressure (Code List: 2.16.840.1.113883.3.526.3.1033)';
            $vs_valueSet = "2.16.840.1.113883.3.526.3.1033";
        }
        if ($row_vital['vital_sign'] == "BMI") {
            $code_list = 'Physical Exam, Finding: BMI LOINC Value (Code List: 2.16.840.1.113883.3.600.1.681)';
            $vs_valueSet = "2.16.840.1.113883.3.600.1.681";
        }

        if ($code_list != "") {
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
    
				 <id root="1.3.6.1.4.1.115" extension="5a1e6cb7cde4a364e87aff2d"/>
										
				<code code="' . $arr_vs_result_type['code'] . '" codeSystem="2.16.840.1.113883.6.1" sdtc:valueSet="' . $vs_valueSet . '">
				<originalText>' . $code_list . '</originalText>
				</code>    
				<statusCode code="completed"/>
			
				<effectiveTime>
				  <low value="' . str_replace('-', '', $row_vital['date_vital']) . '"/>
				  <high value="' . str_replace('-', '', $row_vital['date_vital']) . '"/>
				</effectiveTime>';
            if ($row_vital['range_vital'] != "") {
                $XML_physical_exams .= '<value xsi:type="PQ" value="' . trim($row_vital['range_vital']) . '" unit="' . html_entity_decode(preg_replace('/\s/', '', trim($row_vital['unit']))) . '"/>';
            } else {
                $XML_physical_exams .= '<value xsi:type="PQ" nullFlavor="NI"/>';
            }
            $XML_physical_exams .= '</observation></entry>';
        }
    }

    $sql_vital = "SELECT date_format(exam_date,'%Y-%m-%d') as exam_date_new FROM  chart_dialation 
					WHERE patient_id = '" . $pid . "' and eyeSide!=''";
    $result_vital = imw_query($sql_vital);
    while ($row_vital = imw_fetch_assoc($result_vital)) {
        $XML_physical_exams .= '
		<entry>
		  <!-- Physical Exam Finding -->
		  <observation classCode="OBS" moodCode="EVN">
			<!--  Result observation template -->
			<templateId root="2.16.840.1.113883.10.20.22.4.13"/>
			<!-- Physical Exam, Finding template -->
			<templateId root="2.16.840.1.113883.10.20.24.3.59"/>
			 <id root="1.3.6.1.4.1.115" extension="5a1e6cb7cde4a364e87aff2d"/>
									
			<code code="252779009" codeSystem="2.16.840.1.113883.6.96" sdtc:valueSet="2.16.840.1.113883.3.464.1003.115.12.1088">
				<originalText>Physical Exam, Performed: Retinal or Dilated Eye Exam (Code List: 2.16.840.1.113883.3.464.1003.115.12.1088)</originalText>
			</code>
    			<text>Physical Exam, Performed: Retinal or Dilated Eye Exam (Code List: 2.16.840.1.113883.3.464.1003.115.12.1088)</text>
			<statusCode code="completed"/>
			<effectiveTime>
			  <low value="' . str_replace('-', '', $row_vital['exam_date_new']) . '"/>
			  <high value="' . str_replace('-', '', $row_vital['exam_date_new']) . '"/>
			</effectiveTime>
			<value xsi:type="CD" nullFlavor="UNK"/>';
        $XML_physical_exams .= '</observation></entry>';
    }
    /* END PHYSICAL EXAMS */

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
							<td>40280381-3d61-56a7-013e-425ad8e9179c</td>
						</tr>';

        $XML_CLUSTER_section .= '<!-- 1..* Organizers, each containing a reference to an eMeasure -->
					  <entry>
						<organizer classCode="CLUSTER" moodCode="EVN">
						<!-- This is the templateId for Measure Reference -->
						<templateId root="2.16.840.1.113883.10.20.24.3.98" />
						<!-- This is the templateId for eMeasure Reference QDM -->
						<templateId root="2.16.840.1.113883.10.20.24.3.97" />
                        <id root="1.3.6.1.4.1.115" extension="40280381-51F0-825B-0152-22B98CFF181A"/>
						<statusCode code="completed" />
						<reference typeCode="REFR">
						<externalDocument classCode="DOC" moodCode="EVN">
						<!-- SHALL: This is the version specific identifier for eMeasure: QualityMeasureDocument/id it is a GUID-->
						<id root="40280381-3d61-56a7-013e-425ad8e9179c" />
						<!-- SHOULD: This is the NQF Number, root is an NQF OID and for eMeasure Number and extension is the eMeasure`s NQF number -->
						<id extension="0086" root="2.16.840.1.113883.3.560.1" />
                        <!-- SHALL: This is the version specific identifier for eMeasure: QualityMeasureDocument/id it is a GUID-->
                        <id root="2.16.840.1.113883.4.738" extension="40280381-51F0-825B-0152-22B98CFF181A"/>
						<!-- SHOULD This is the title of the eMeasure -->
						<text>Primary Open Angle Glaucoma (POAG): Optic Nerve Evaluation</text>
						<!-- SHOULD: setId is the eMeasure version neutral id -->
						<setId root="db9d9f09-6b6a-4749-a8b2-8c1fdb018823" />
						<!-- This is the sequential eMeasure Version number -->
						<versionNumber value="2" />
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
							<td>d90bdab4-b9d2-4329-9993-5c34e2c0dc66</td>
							<td>2</td>
							<td>0055</td>
							<td>40280381-3D61-56A7-013E-62237D5D24E1</td>
						</tr>';

        $XML_CLUSTER_section .= '<!-- 1..* Organizers, each containing a reference to an eMeasure -->
					  <entry>
						<organizer classCode="CLUSTER" moodCode="EVN">
						<!-- This is the templateId for Measure Reference -->
						<templateId root="2.16.840.1.113883.10.20.24.3.98" />
						<!-- This is the templateId for eMeasure Reference QDM -->
						<templateId root="2.16.840.1.113883.10.20.24.3.97" />
                        <id root="1.3.6.1.4.1.115" extension="40280381-51F0-825B-0152-22B98CFF181A"/>
						<statusCode code="completed" />
						<reference typeCode="REFR">
						<externalDocument classCode="DOC" moodCode="EVN">
						<!-- SHALL: This is the version specific identifier for eMeasure: QualityMeasureDocument/id it is a GUID-->
						<id root="40280381-3D61-56A7-013E-62237D5D24E1" />
						<!-- SHOULD: This is the NQF Number, root is an NQF OID and for eMeasure Number and extension is the eMeasure`s NQF number -->
						<id extension="0055" root="2.16.840.1.113883.3.560.1" />
                        <!-- SHALL: This is the version specific identifier for eMeasure: QualityMeasureDocument/id it is a GUID-->
                        <id root="2.16.840.1.113883.4.738" extension="40280381-51F0-825B-0152-22B98CFF181A"/>
						<!-- SHOULD This is the title of the eMeasure -->
						<text>Diabetes: Eye Exam</text>
						<!-- SHOULD: setId is the eMeasure version neutral id -->
						<setId root="d90bdab4-b9d2-4329-9993-5c34e2c0dc66" />
						<!-- This is the sequential eMeasure Version number -->
						<versionNumber value="2" />
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
							  <!-- This is the sequential eMeasure Version number -->
							<!--  <versionNumber value="2"/>      -->
							</externalDocument>
						  </reference>
						</organizer>
					 </entry>';
    }

    if (in_array("NQF0022", $con_nqf_id_arr) || in_array("NQF0022a", $con_nqf_id_arr) || in_array("NQF0022b", $con_nqf_id_arr) || $inc_all_nqf == "yes") {
        $XML_measure_section .= '<tr>
							 <td>Use of High-Risk Medications in the Elderly</td>
							  <td>A3837FF8-1ABC-4BA9-800E-FD4E7953ADBD</td>
							  <td>2</td>
							  <td>0022</td>
							  <td>40280381-3D61-56A7-013E-65C9C3043E54</td>
						</tr>';

        $XML_CLUSTER_section .= '<!-- 1..* Organizers, each containing a reference to an eMeasure -->
					  <entry>
						<organizer classCode="CLUSTER" moodCode="EVN">
						  <!-- This is the templateId for Measure Reference -->
						  <templateId root="2.16.840.1.113883.10.20.24.3.98"/>
						  <!-- This is the templateId for eMeasure Reference QDM -->
						  <templateId root="2.16.840.1.113883.10.20.24.3.97"/>
                          <id root="1.3.6.1.4.1.115" extension="40280381-51F0-825B-0152-22B98CFF181A"/>
						  <statusCode code="completed"/>
						  <!-- Containing isBranch external references -->
						  <reference typeCode="REFR">
							<externalDocument classCode="DOC" moodCode="EVN">
							  <!-- SHALL: This is the version specific identifier for eMeasure: QualityMeasureDocument/id it is a GUID-->
							  <id root="40280381-3D61-56A7-013E-65C9C3043E54"/>
                              <!-- SHALL: This is the version specific identifier for eMeasure: QualityMeasureDocument/id it is a GUID-->
                              <id root="2.16.840.1.113883.4.738" extension="40280381-51F0-825B-0152-22B98CFF181A"/>
							  <!-- SHOULD This is the title of the eMeasure -->
							  <text>Use of High-Risk Medications in the Elderly</text>
							  <!-- SHOULD: setId is the eMeasure version neutral id  -->
							  <setId root="A3837FF8-1ABC-4BA9-800E-FD4E7953ADBD"/>
							  <!-- This is the sequential eMeasure Version number -->
							  <versionNumber value="2"/>                  
							</externalDocument>
						  </reference>
						</organizer>
					 </entry>';
    }

    if (in_array("NQF0421a", $con_nqf_id_arr) || in_array("NQF0421b", $con_nqf_id_arr) || in_array("NQF0421", $con_nqf_id_arr) || $inc_all_nqf == "yes") {
        $XML_measure_section .= '<tr>
							 <td>Preventive Care and Screening: Body Mass Index (BMI) Screening and Follow-Up</td>
							  <td>9A031BB8-3D9B-11E1-8634-00237D5BF174</td>
							  <td>2</td>
							  <td>0421</td>
							  <td>40280381-3E93-D1AF-013E-D6E2B772150D</td>
						</tr>';

        $XML_CLUSTER_section .= '<!-- 1..* Organizers, each containing a reference to an eMeasure -->
					  <entry>
						<organizer classCode="CLUSTER" moodCode="EVN">
						  <!-- This is the templateId for Measure Reference -->
						  <templateId root="2.16.840.1.113883.10.20.24.3.98"/>
						  <!-- This is the templateId for eMeasure Reference QDM -->
						  <templateId root="2.16.840.1.113883.10.20.24.3.97"/>
                          <id root="1.3.6.1.4.1.115" extension="40280381-51F0-825B-0152-22B98CFF181A"/>
						  <statusCode code="completed"/>
						  <!-- Containing isBranch external references -->
						  <reference typeCode="REFR">
							<externalDocument classCode="DOC" moodCode="EVN">
							  <!-- SHALL: This is the version specific identifier for eMeasure: QualityMeasureDocument/id it is a GUID-->
							  <id root="40280381-3E93-D1AF-013E-D6E2B772150D"/>
                              <!-- SHALL: This is the version specific identifier for eMeasure: QualityMeasureDocument/id it is a GUID-->
                              <id root="2.16.840.1.113883.4.738" extension="40280381-51F0-825B-0152-22B98CFF181A"/>
							  <!-- SHOULD This is the title of the eMeasure -->
							  <text>Preventive Care and Screening: Body Mass Index (BMI) Screening and Follow-Up</text>
							  <!-- SHOULD: setId is the eMeasure version neutral id  -->
							  <setId root="9A031BB8-3D9B-11E1-8634-00237D5BF174"/>
							  <!-- This is the sequential eMeasure Version number -->
							  <versionNumber value="2"/>                  
							</externalDocument>
						  </reference>
						</organizer>
					 </entry>';
    }

    if (in_array("CMS50v2", $con_nqf_id_arr) || $inc_all_nqf == "yes") {
        $XML_measure_section .= '<tr>
							  <td>Closing the referral loop: receipt of specialist report</td>
							  <td>F58FC0D6-EDF5-416A-8D29-79AFBFD24DEA</td>
							  <td>2</td>
							  <td>CMS50v2</td>
							  <td>40280381-3D61-56A7-013E-7AA509DE6258</td>
						</tr>';

        $XML_CLUSTER_section .= '<!-- 1..* Organizers, each containing a reference to an eMeasure -->
					  <entry>
						<organizer classCode="CLUSTER" moodCode="EVN">
						  <!-- This is the templateId for Measure Reference -->
						  <templateId root="2.16.840.1.113883.10.20.24.3.98"/>
						  <!-- This is the templateId for eMeasure Reference QDM -->
						  <templateId root="2.16.840.1.113883.10.20.24.3.97"/>
                          <id root="1.3.6.1.4.1.115" extension="40280381-51F0-825B-0152-22B98CFF181A"/>
						  <statusCode code="completed"/>
						  <!-- Containing isBranch external references -->
						  <reference typeCode="REFR">
							<externalDocument classCode="DOC" moodCode="EVN">
							  <!-- SHALL: This is the version specific identifier for eMeasure: QualityMeasureDocument/id it is a GUID-->
							  <id root="40280381-3D61-56A7-013E-7AA509DE6258"/>
                              <!-- SHALL: This is the version specific identifier for eMeasure: QualityMeasureDocument/id it is a GUID-->
                              <id root="2.16.840.1.113883.4.738" extension="40280381-51F0-825B-0152-22B98CFF181A"/>
							  <!-- SHOULD This is the title of the eMeasure -->
							  <text>Closing the referral loop: receipt of specialist report</text>
							  <!-- SHOULD: setId is the eMeasure version neutral id  -->
							  <setId root="F58FC0D6-EDF5-416A-8D29-79AFBFD24DEA"/>
							  <!-- This is the sequential eMeasure Version number -->
							  <versionNumber value="2"/>                  
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
							  <td>40280381-3D61-56A7-013E-5CD94A4D64FA</td>
						</tr>';

        $XML_CLUSTER_section .= '<!-- 1..* Organizers, each containing a reference to an eMeasure -->
					  <entry>
						<organizer classCode="CLUSTER" moodCode="EVN">
						  <!-- This is the templateId for Measure Reference -->
						  <templateId root="2.16.840.1.113883.10.20.24.3.98"/>
						  <!-- This is the templateId for eMeasure Reference QDM -->
						  <templateId root="2.16.840.1.113883.10.20.24.3.97"/>
                          <id root="1.3.6.1.4.1.115" extension="40280381-51F0-825B-0152-22B98CFF181A"/>
						  <statusCode code="completed"/>
						  <!-- Containing isBranch external references -->
						  <reference typeCode="REFR">
							<externalDocument classCode="DOC" moodCode="EVN">
							  <!-- SHALL: This is the version specific identifier for eMeasure: QualityMeasureDocument/id it is a GUID-->
							  <id root="40280381-3D61-56A7-013E-5CD94A4D64FA"/>
                              <!-- SHALL: This is the version specific identifier for eMeasure: QualityMeasureDocument/id it is a GUID-->
                              <id root="2.16.840.1.113883.4.738" extension="40280381-51F0-825B-0152-22B98CFF181A"/>
							  <!-- SHOULD This is the title of the eMeasure -->
							  <text>Preventive Care and Screening: Tobacco Use: Screening and Cessation Intervention</text>
							  <!-- SHOULD: setId is the eMeasure version neutral id  -->
							  <setId root="E35791DF-5B25-41BB-B260-673337BC44A8"/>
							  <!-- This is the sequential eMeasure Version number -->
							  <versionNumber value="2"/>                  
							</externalDocument>
						  </reference>
						</organizer>
					 </entry>';
    }

    if (in_array("NQF0052", $con_nqf_id_arr) || $inc_all_nqf == "yes") {
        $XML_measure_section .= '<tr>
							  <td>Use of Imaging Studies for Low Back Pain</td>
							  <td>B6016B47-B65D-4BE0-866F-1D397886CA89</td>
							  <td>3</td>
							  <td>0052</td>
							  <td>40280381-3D61-56A7-013E-62790FE42CA1</td>
						</tr>';

        $XML_CLUSTER_section .= '<!-- 1..* Organizers, each containing a reference to an eMeasure -->
					  <entry>
						<organizer classCode="CLUSTER" moodCode="EVN">
						  <!-- This is the templateId for Measure Reference -->
						  <templateId root="2.16.840.1.113883.10.20.24.3.98"/>
						  <!-- This is the templateId for eMeasure Reference QDM -->
						  <templateId root="2.16.840.1.113883.10.20.24.3.97"/>
                          <id root="1.3.6.1.4.1.115" extension="40280381-51F0-825B-0152-22B98CFF181A"/>
						  <statusCode code="completed"/>
						  <!-- Containing isBranch external references -->
						  <reference typeCode="REFR">
							<externalDocument classCode="DOC" moodCode="EVN">
							  <!-- SHALL: This is the version specific identifier for eMeasure: QualityMeasureDocument/id it is a GUID-->
							  <id root="40280381-3D61-56A7-013E-62790FE42CA1"/>
                              <!-- SHALL: This is the version specific identifier for eMeasure: QualityMeasureDocument/id it is a GUID-->
                              <id root="2.16.840.1.113883.4.738" extension="40280381-51F0-825B-0152-22B98CFF181A"/>
							  <!-- SHOULD This is the title of the eMeasure -->
							  <text>Use of Imaging Studies for Low Back Pain</text>
							  <!-- SHOULD: setId is the eMeasure version neutral id  -->
							  <setId root="B6016B47-B65D-4BE0-866F-1D397886CA89"/>
							  <!-- This is the sequential eMeasure Version number -->
							  <versionNumber value="3"/>                  
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

    $XML_file_name = "qrda_xml/qrda_cat1/" . $rowPatient['fname'] . '_' . $rowPatient['lname'] . ".xml";
    $rqfileName_arr[] = $rowPatient['fname'] . '_' . $rowPatient['lname'] . ".xml";

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
            $pat_nqf_arr[$con_nqf_id_name][] = $rowPatient['fname'] . '_' . $rowPatient['lname'] . ".xml";
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
    if ($zip->open($file_path . $archive_file_name, ZIPARCHIVE::OVERWRITE) !== TRUE) {
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
    $sql = "SELECT * FROM pt_problem_list WHERE pt_id IN($pid) AND status='Active'";
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
?>


