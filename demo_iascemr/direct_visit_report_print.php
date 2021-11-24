<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
session_start();
if($_REQUEST['hidd_report_format']!='csv') {
	echo '<table id="loader_tbl" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif;">
			<tr class="text_9" height="20" bgcolor="#EAF0F7" valign="top">
				<td align="center">Please wait while data is retrieving from the server.</td>
			</tr>
			<tr class="text_9" height="20" bgcolor="#EAF0F7" valign="top">
				<td align="center"><img src="images/pdf_load_img.gif"></td> 
			</tr>
		</table>';
}
set_time_limit(500);
include("common/conDb.php");
if($_SESSION["loginUserId"]=="" && $_SESSION['loginUserName']=="") {
	echo '<script>top.location.href="index.php"</script>';
}
$get_http_path = $_REQUEST['get_http_path'];
$selected_date = $_REQUEST['startdate'];
$selected_date2 = $_REQUEST['enddate'];
$hidd_report_format = $_REQUEST['hidd_report_format'];

include_once("admin/classObjectFunction.php");
global $objManageData;
$objManageData = new manageData;

// $loginUser 	= $_SESSION['loginUserId'];
// $asc 		= $_REQUEST['asc'];
// $ascTmp 	= $asc;
// if(!$ascTmp) { $ascTmp = $_SESSION['facility']; }

$query = "SELECT 
            `pc`.`ascId`, 
            `pt`.`dos`, 
            `pt`.`surgery_time`, 
            `pt`.`surgeon_fname`, 
            `pt`.`surgeon_lname`, 
            `pdt`.`patient_fname`, 
            `pdt`.`patient_lname`, 
            `pt`.`patient_primary_procedure`, 
            `pt`.`patient_secondary_procedure`, 
            `pt`.`patient_tertiary_procedure` 
        FROM  `patient_in_waiting_tbl` AS `pt`
            INNER JOIN `patient_data_tbl` AS `pdt` ON `pdt`.`patient_id` = `pt`.`patient_id`
            LEFT JOIN `stub_tbl` AS `st` ON `st`.`iolink_patient_in_waiting_id` = `pt`.`patient_in_waiting_id`
            LEFT JOIN `patientconfirmation` AS `pc` ON `pc`.`patientConfirmationId`  = `st`.`patient_confirmation_id`
        WHERE 
            `pt`.`source` = '' 
            AND 
                `pt`.`dos` BETWEEN '".$selected_date."' AND '".$selected_date2."' 
            AND 
                `pc`.`ascId` != ''
    ";

$queryRs 	= imw_query($query);
$recordsFlag	=	imw_num_rows($queryRs) > 0;

if($recordsFlag) {

    if($hidd_report_format == 'csv') {

        $csv_content = array();
        if( imw_num_rows($queryRs) > 0 )
        {
            while($row = imw_fetch_assoc($queryRs)) {
                $csv_content[] = $row;
            }

            $file_name = $_SERVER['DOCUMENT_ROOT'].'/'.$surgeryCenterDirectoryName.'/admin/pdfFiles/missing_visit_id_report.csv';

            if(file_exists($file_name)) {
                @unlink($file_name);
            }
            $fp1 = fopen($file_name,'w');

            // CSV Headers
            $csv_content_header[] = array('ASC ID', 'DOS', 'Surgery Time', 'Surgeon F.Name', 'Surgeon L.Name', 'Patient F.Name', 'Patient L.Name', 'Primary Procedure', 'Secondary Procedure', 'Tertiary Procedure');

            foreach ($csv_content_header as $fields1){
                fputcsv($fp1, $fields1, ';');
            }
            foreach ($csv_content as $fields){
                fputcsv($fp1, $fields, ';');
            }
            $objManageData->download_file($file_name, basename($file_name));
            fclose($fp1);
            exit;
        }
    }
}