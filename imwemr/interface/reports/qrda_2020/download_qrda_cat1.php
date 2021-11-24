<?php

ob_start();
include_once(dirname(__FILE__) . "/../../../config/globals.php");
include_once(dirname(__FILE__) . "/../../../library/classes/qrda/ecqm_medical_hx.php");
include_once(dirname(__FILE__) . "/../../../library/classes/qrda/ecqm_encounters.php");
include_once(dirname(__FILE__) . "/../../../library/classes/qrda/ecqm_patients.php");


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

$req_arr=array();
$req_arr=$_REQUEST['selected_measures'];

$data_path=data_path()."UserId_".$pro_id."/";

$XML_file_name='';
$pat_nqf_arr=$rqfileName_arr=array();
$all_cms_ids=array('CMS50v8','CMS68v9','CMS131v8','CMS132v8','CMS133v8','CMS138v8','CMS142v8','CMS143v8','CMS156v8','CMS165v8');

//clean user qrda_xml folder for category 1 download.
destroy($data_path."qrda_xml/qrda_cat1");


if(empty($req_arr) == false) {
    foreach($req_arr as $CMS_ID => $patient_ids_arr) {
        $patient_ids_arr = explode(',', $patient_ids_arr);
        if(in_array($CMS_ID,$all_cms_ids) && empty($patient_ids_arr)==false) {
            foreach($patient_ids_arr as $patient_id) {

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
                $XML_medication_entry = "";
                $XML_smoking_status_entry = "";     //Not used
                $XML_problem_section = "";
                $XML_problem_entry = "";    //Not used
                $XML_physical_exams = "";
                $XML_CLUSTER_section = "";
                $XML_patient_payer = "";
                $XML_referral_loop = "";

                $ECQMPatients = new ECQMPatients($patient_id,$pro_id,$CMS_ID);
                $ECQMMedicalHx = new ECQMMedicalHx($patient_id,$pro_id,$CMS_ID);
                $ECQMEncounters = new ECQMEncounters($patient_id,$pro_id,$CMS_ID);

                $patient_data=$ECQMPatients->patient_data();
                $author_data=$ECQMPatients->user_data();
                $custodian_data=$ECQMPatients->facility_data();
                $encounters_data=$ECQMEncounters->get_pt_encounter_list();
                $problems_data=$ECQMMedicalHx->get_pt_problem_list();

                $pt_referral_data=$ECQMPatients->get_referral_loop_data();
                $ptCharacteristicPayer=$ECQMPatients->ptCharacteristicPayer();
                $pt_physical_exam=$ECQMMedicalHx->pt_physical_exam();
                $pt_assessment_data=$ECQMPatients->pt_assessment_data();
                $pt_medications_data=$ECQMMedicalHx->pt_medications_data();
                $pt_procedure_data=$ECQMMedicalHx->pt_procedure_data();
                $pt_interventions_data=$ECQMMedicalHx->pt_interventions_data();
                $pt_surgery_data=$ECQMMedicalHx->pt_surgery_data();
                $pt_optic_diagnostic_data=$ECQMMedicalHx->pt_optic_diagnostic_data();
                $pt_macular_diagnostic_data=$ECQMMedicalHx->pt_macular_diagnostic_data();
                $pt_communication_performed=$ECQMPatients->pt_communication_performed();
                

                /* START Measure SECTION */
                if(empty($patient_data)==false) {
                    $XML_measure_section=$ECQMPatients->measure_section_xml($CMS_ID);
                }
                /* END Measure SECTION */

                /* BEGIN PATIENT DATA xml content*/
                if(empty($patient_data)==false) {
                    $XMLpatient_data.=$ECQMPatients->get_patient_data_xml($patient_data);
                }
                /* END PATIENT DATA */

                /* BEGIN AUTHOR DATA xml content*/
                if(empty($author_data)==false) {
                    $XML_author_data.=$ECQMPatients->get_author_data_xml($author_data,$currentDate);

                    /* BEGIN documentationof DATA xml content*/
                    $XML_documentationof_data.=$ECQMPatients->get_documentationOf_data_xml($author_data,$currentDate);
                }
                /* END AUTHOR DATA   */

                /* BEGIN CUSTODIAN (FACILITY) DATA xml content*/
                if(empty($custodian_data)==false) {
                    $XML_custodian_data.=$ECQMPatients->get_custodian_data_xml($custodian_data);

                    /* BEGIN Leagal Auth DATA xml content*/
                    $XML_leagalauth_data.=$ECQMPatients->get_leagalauth_data_xml($custodian_data,$currentDate);
                }
                /* END CUSTODIAN (FACILITY) DATA */

                /* BEGIN ENOCUNTERS SECTION */
                if(empty($encounters_data)==false) {
                    $XML_encouter_entry.=$ECQMEncounters->get_encounter_data_xml($encounters_data);
                }
                /* END ENOCUTERS SECTION */

                /* BEGIN Diagnostic Study, Result SECTION */
                if(empty($pt_optic_diagnostic_data)==false) {
                    $XML_test_entry.=$ECQMMedicalHx->get_optic_diagnostic_data_xml($pt_optic_diagnostic_data);
                } else {
                    if(empty($pt_macular_diagnostic_data)==false) {
                        $XML_test_entry.=$ECQMMedicalHx->get_macular_diagnostic_data_xml($pt_macular_diagnostic_data);
                    }
                }
                /* END Diagnostic Study, Result SECTION */

                /* BEGIN intervention SECTION */
                if( empty($pt_interventions_data)==false && (empty($pt_referral_data['referral_data']) &&  $CMS_ID!='CMS50v8') ) {
                    $XML_intervention_entry.=$ECQMMedicalHx->get_intervention_data_xml($pt_interventions_data);
                }
                /* END intervention SECTION */

                //-------BEGIN Procedure SECTION --------------//
                if(empty($pt_procedure_data)==false) {
                    $XML_procedure_entry.=$ECQMMedicalHx->get_procedure_data_xml($pt_procedure_data);
                }
                //-------END Procedure SECTION --------------//
                
                //-------BEGIN Surgery SECTION --------------//
                if(empty($pt_surgery_data)==false) {
                    $XML_procedure_entry.=$ECQMMedicalHx->get_surgery_data_xml($pt_surgery_data);
                }
                //-------END Surgery SECTION --------------//

                /* BEGIN PROBLEM SECTION */
                if(empty($problems_data)==false) {
                    $XML_problem_section.=$ECQMMedicalHx->get_problems_data_xml($problems_data);
                }
                /* END PROBLEM SECTION */

                /* BEGIN PHYSICAL EXAMS */
                if(empty($pt_physical_exam)==false) {
                    $XML_physical_exams.=$ECQMMedicalHx->physical_exam_data_xml($pt_physical_exam);
                }
                /* END PHYSICAL EXAMS */
                
                /* BEGIN Assessments */
                if(empty($pt_assessment_data)==false) {
                    $XML_physical_exams.=$ECQMPatients->get_pt_assessment_xml($pt_assessment_data);
                }
                /* End Assessments */
                
                /* BEGIN Medication DATA */
                if(empty($pt_medications_data)==false) {
                    $XML_medication_entry.=$ECQMMedicalHx->pt_medications_data_xml($pt_medications_data);
                }
                /* END Medication DATA  */
                
                /* Start Referal Loop */
                if(empty($pt_referral_data)==false) {
                    $XML_referral_loop.=$ECQMPatients->get_referral_loop_xml($pt_referral_data,$pt_interventions_data);
                }
                /* End Referal Loop */

                /* BEGIN Characteristic Payer */
                if(empty($ptCharacteristicPayer)==false) {
                    $XML_patient_payer.=$ECQMPatients->get_ptCharacteristicPayer_xml($ptCharacteristicPayer);
                }
                /* END Characteristic Payer */
                
                /* BEGIN Pt Communication performed */
                if(empty($pt_communication_performed)==false) {
                    $XML_pro_comm_entry.=$ECQMPatients->get_ptCommunicationPerformed_xml($pt_communication_performed);
                }
                /* END Pt Communication performed */


                $dtfrom1_exp = explode('-', $dtfrom1);
                $dtupto1_exp = explode('-', $dtupto1);
                $rep_dtfrom1 = date('j F Y', mktime(0, 0, 0, $dtfrom1_exp[1], $dtfrom1_exp[2], $dtfrom1_exp[0]));
                $rep_dtto1 = date('j F Y', mktime(0, 0, 0, $dtupto1_exp[1], $dtupto1_exp[2], $dtupto1_exp[0]));

                $patient_data_section = ' <component>
                    <section>
                    <!-- This is the templateId for Patient Data section -->
                    <templateId root="2.16.840.1.113883.10.20.17.2.4"/>
                    <!-- This is the templateId for Patient Data QDM section -->
                    <templateId extension="2017-08-01" root="2.16.840.1.113883.10.20.24.2.1"/>
                    <templateId extension="2018-02-01" root="2.16.840.1.113883.10.20.24.2.1.1"/>
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
                            <templateId extension="2016-03-01" root="2.16.840.1.113883.10.20.17.3.8.1"/>
                            <id extension="5ec09330-c019-0137-3bbb-0eca209bc306" root="1.3.6.1.4.1.115"/>
                            <code code="252116004" codeSystem="2.16.840.1.113883.6.96" displayName="Observation Parameters"/>
                            <effectiveTime>
                                <low value="' . $dtfrom1_exp[0] . $dtfrom1_exp[1] . $dtfrom1_exp[2] . '000000"/>
                                <high value="' . $dtupto1_exp[0] . $dtupto1_exp[1] . $dtupto1_exp[2] . '235959"/>
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
                $XML_cda_body .= $XML_medication_entry;
                $XML_cda_body .= $XML_referral_loop;
                $XML_cda_body .= $XML_patient_payer;

                $XML_cda_body .= ' </section></component></structuredBody>';
                $XML_cda_body .= '</component>';
                /* END XML BODY */

                $xml='';
                $xml.='<?xml version="1.0" encoding="utf-8"?>';
                /* $xml .= '<?xml-stylesheet type="text/xsl" href="CDA.xsl"?>'; */
                $xml.= '<ClinicalDocument xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                xmlns="urn:hl7-org:v3"
                xmlns:voc="urn:hl7-org:v3/voc"
                xmlns:sdtc="urn:hl7-org:sdtc">
                <!-- QRDA Header -->
                <realmCode code="US"/>
                <typeId root="2.16.840.1.113883.1.3" extension="POCD_HD000040"/>
                <!-- US Realm Header Template Id -->
                <templateId root="2.16.840.1.113883.10.20.22.1.1" extension="2015-08-01"/>
                <!-- QRDA templateId -->
                <templateId root="2.16.840.1.113883.10.20.24.1.1" extension="2017-08-01"/>
                <!-- QDM-based QRDA templateId -->
                <templateId root="2.16.840.1.113883.10.20.24.1.2" extension="2017-08-01"/>
                <!-- CMS QRDA templateId -->
                <templateId root="2.16.840.1.113883.10.20.24.1.3" extension="2018-02-01"/>
                <!-- This is the globally unique identifier for this QRDA document -->
                <id root="5eb73cc0-c019-0137-3bbb-0eca209bc306"/>
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



                $XML_file_name = $data_path."qrda_xml/qrda_cat1/" .$patient_id . '_' . $patient_data['fname'] . '_' . $patient_data['lname'] . ".xml";
                $rqfileName_arr[] = $patient_id . '_' . $patient_data['fname'] . '_' . $patient_data['lname'] . ".xml";

                $pat_nqf_arr[$CMS_ID][] = $patient_id . '_' . $patient_data['fname'] . '_' . $patient_data['lname'] . ".xml";

                file_put_contents($XML_file_name, $xml);

            }
        }
    }
}

if ($XML_file_name != "") {
    $rqZipfileName_arr = array();
    $file_path = $data_path.'qrda_xml/qrda_cat1/';
    for ($i = 0; $i < count($all_cms_ids); $i++) {
        if ($all_cms_ids[$i] !== "") {
            if (count($pat_nqf_arr[$all_cms_ids[$i]]) > 0) {
                $file_names = $pat_nqf_arr[$all_cms_ids[$i]];
                $archive_file_name = $all_cms_ids[$i] . '.zip';
                zipFilesAndDownload($file_names, $archive_file_name, $file_path);
                $rqZipfileName_arr[] = $archive_file_name;
            }
        }
    }

    $archive_file_name = 'qrda_cat1.zip';
    $file_names = $rqZipfileName_arr;
    zipFilesAndDownload($file_names, $archive_file_name, $file_path, 'yes');
}





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

function zipFilesAndDownload($file_names='', $archive_file_name='', $file_path='', $download_status='') {
    $zip = new ZipArchive();
    //create the file and throw the error if unsuccessful
    if ($zip->open($file_path . $archive_file_name, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE) !== TRUE) {
        exit("cannot open <$archive_file_name>\n");
    }
    //add each files of $file_name array to archive
    foreach ($file_names as $files) {
        $zip->addFile($file_path . $files, $files);
    }
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
