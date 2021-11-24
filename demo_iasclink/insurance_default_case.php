<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
include('connect_imwemr.php'); // CONNECTION
//START CODE TO GET DEFAULT CASE
$defaultCaseId	='';
$defaultCaseName='';
$defaultCaseTypeQry 	= "SELECT case_id,case_name FROM insurance_case_types WHERE normal = '1'";
$defaultCaseTypeRes 	= imw_query($defaultCaseTypeQry)or die(imw_error());
$defaultCaseTypeNumRow 	= imw_num_rows($defaultCaseTypeRes);
if($defaultCaseTypeNumRow>0) {
	$defaultCaseTypeRow = imw_fetch_array($defaultCaseTypeRes);
	$defaultCaseId 		= $defaultCaseTypeRow['case_id'];
	$defaultCaseName 	= $defaultCaseTypeRow['case_name'];
}
//END CODE TO GET DEFAULT CASE


imw_close($link_imwemr); //CLOSE CONNECTION
?>