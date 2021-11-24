<?php 
$ignoreAuth = true;
include_once("../../../../config/globals.php");

$qry1=array();
$qry1[] =  "INSERT INTO `cqm_v8_valueset`
            SET `id` = '15597',
                `CMS_ID` = 'CMS68v9',
                `NQF_Number` = '0419e',
                `Value_Set_Name` = 'Current Medications Documented',
                `Value_Set_OID` = '2.16.840.1.113762.1.4.1116.361',
                `QDM_Category` = 'Procedure',
                `Definition_Version` = '20170927',
                `Expansion_Version` = 'eCQM Update 2019-09',
                `Code` = '428191000124101',
                `Description` = 'Documentation of current medications',
                `Code_System` = 'SNOMEDCT',
                `Code_System_OID` = '2.16.840.1.113883.6.96',
                `Code_System_Version` = '2019-09'
            ";

$qry1[] =  "INSERT INTO `cqm_v8_valueset`
            SET `id` = '15598',
                `CMS_ID` = 'CMS131v8',
                `NQF_Number` = '',
                `Value_Set_Name` = 'Discharge to healthcare facility for hospice care',
                `Value_Set_OID` = '2.16.840.1.113883.3.117.1.7.1.207',
                `QDM_Category` = 'Procedure',
                `Definition_Version` = '20170725',
                `Expansion_Version` = 'eCQM Update 2019-09',
                `Code` = '428371000124100',
                `Description` = 'Discharge to healthcare facility for hospice care (procedure)',
                `Code_System` = 'SNOMEDCT',
                `Code_System_OID` = '2.16.840.1.113883.6.96',
                `Code_System_Version` = '2019-09'
            ";

$qry1[] =  "INSERT INTO `cqm_v8_valueset`
            SET `id` = '15599',
                `CMS_ID` = 'CMS131v8',
                `NQF_Number` = '',
                `Value_Set_Name` = 'Discharge to home for hospice care',
                `Value_Set_OID` = '2.16.840.1.113883.3.7587.2.1006',
                `QDM_Category` = 'Procedure',
                `Definition_Version` = '20171222',
                `Expansion_Version` = 'eCQM Update 2019-09',
                `Code` = '428361000124107',
                `Description` = 'Discharge to home for hospice care (procedure)',
                `Code_System` = 'SNOMEDCT',
                `Code_System_OID` = '2.16.840.1.113883.6.96',
                `Code_System_Version` = '2019-09'
            ";

$qry1[] =  "INSERT INTO `cqm_v8_valueset`
            SET `id` = '15600',
                `CMS_ID` = 'CMS133v8',
                `NQF_Number` = '0565e',
                `Value_Set_Name` = 'Best Corrected Visual Acuity',
                `Value_Set_OID` = '2.16.840.1.113883.3.526.2.1920',
                `QDM_Category` = 'Physical Exam',
                `Definition_Version` = '20170504',
                `Expansion_Version` = 'eCQM Update 2019-09',
                `Code` = '419775003',
                `Description` = 'Best corrected visual acuity (observable entity)',
                `Code_System` = 'SNOMEDCT',
                `Code_System_OID` = '2.16.840.1.113883.6.96',
                `Code_System_Version` = '2019-09'
            ";

$qry1[] =  "INSERT INTO `cqm_v8_valueset`
            SET `id` = '15601',
                `CMS_ID` = 'CMS142v8',
                `NQF_Number` = '0089e',
                `Value_Set_Name` = 'Macular Edema Findings Absent',
                `Value_Set_OID` = '2.16.840.1.113883.3.526.2.1373',
                `QDM_Category` = 'Physical Exam',
                `Definition_Version` = '20170504',
                `Expansion_Version` = 'eCQM Update 2019-09',
                `Code` = '428341000124108',
                `Description` = 'Macular edema absent (situation)',
                `Code_System` = 'SNOMEDCT',
                `Code_System_OID` = '2.16.840.1.113883.6.96',
                `Code_System_Version` = '2019-09'
            ";

$qry1[] =  "INSERT INTO `cqm_v8_valueset`
            SET `id` = '15602',
                `CMS_ID` = 'CMS142v8',
                `NQF_Number` = '0089e',
                `Value_Set_Name` = 'Medical practitioner',
                `Value_Set_OID` = '2.16.840.1.113762.1.4.1099.30',
                `QDM_Category` = 'Communication',
                `Definition_Version` = '20190418',
                `Expansion_Version` = 'eCQM Update 2019-09',
                `Code` = '158965000',
                `Description` = 'Medical practitioner (occupation)',
                `Code_System` = 'SNOMEDCT',
                `Code_System_OID` = '2.16.840.1.113883.6.96',
                `Code_System_Version` = '2019-09'
            ";

$qry1[] =  "INSERT INTO `cqm_v8_valueset`
            SET `id` = '15603',
                `CMS_ID` = 'CMS142v8',
                `NQF_Number` = '0089e',
                `Value_Set_Name` = 'Healthcare professional',
                `Value_Set_OID` = '2.16.840.1.113762.1.4.1099.30',
                `QDM_Category` = 'Communication',
                `Definition_Version` = '20190418',
                `Expansion_Version` = 'eCQM Update 2019-09',
                `Code` = '223366009',
                `Description` = 'Healthcare professional (occupation)',
                `Code_System` = 'SNOMEDCT',
                `Code_System_OID` = '2.16.840.1.113883.6.96',
                `Code_System_Version` = '2019-09'
            ";

$qry1[] = "INSERT INTO `cqm_v8_valueset` (`id`, `CMS_ID`, `NQF_Number`, `Value_Set_Name`, `Value_Set_OID`, `QDM_Category`, `Definition_Version`, `Expansion_Version`, `Purpose:_Clinical_Focus`, `Purpose:_Data_Element_Scope`, `Purpose:_Inclusion_Criteria`, `Purpose:_Exclusion_Criteria`, `Code`, `Description`, `Code_System`, `Code_System_OID`, `Code_System_Version`, `Expansion_ID`) VALUES
(15604, 'CMS68v9', '', 'ED', '2.16.840.1.113883.3.464.1003.101.12.1085', 'Encounter', '20190315', 'eCQM Update 2019-05-10', 'This value set contains concepts related to an ED visit.', 'This value set may use Quality Data Model (QDM) datatype related to Encounter, Performed.', 'Includes only relevant concepts associated with comprehensive history, evaluation, and management of a patient in an ED. This is a grouping value set of CPT and SNOMED codes.', 'No exclusions.', '4525004', 'Emergency department patient visit (procedure)', 'SNOMEDCT', '2.16.840.1.113883.6.96', '2018-09', '20190510');";


foreach($qry1 as $qry11) {
    imw_query($qry11) or $msg_info[] = imw_error();
}

if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 2 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 2 completed successfully.</b>";
	$color = "green";
}
?>

<html>
<head>
<title>Update 2</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>