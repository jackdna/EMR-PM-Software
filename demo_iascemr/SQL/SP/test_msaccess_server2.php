<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
error_reporting(E_ALL);
ini_set("display_errors",1);
include('adodb5/adodb.inc.php');

$accessPath = "\\\\Vinemain\\Database\\EyeLive.mdb"; 
//$accessPath = "D:\\SampleData\\EyeLive.mdb"; 

$accessUid = "";
$accessPwd = "";
$db =&ADONewConnection('access');	

$dsn = "Driver={Microsoft Access Driver (*.mdb)};Dbq=$accessPath;Uid=$accessUid;Pwd=$accessPwd;";	
$db->Connect($dsn) or die('error1'.odbc_error());

	$patient_dos = '2009-05-14';
	if($patient_dos) {
		$appt_dos = date("d-m-Y",strtotime($patient_dos));
	}
		$sql_appointments="
					SELECT TOP 5 appointments.* FROM appointments 
					WHERE appointments.appointment_date_time LIKE '$appt_dos%'  AND appointments.appointment_status=0 
					ORDER BY DateValue([appointment_date_time]), TimeValue([appointment_date_time]);
					";
					

		$rs_appointments = $db->Execute($sql_appointments) or die('error2'.odbc_errormsg());

		if($rs_appointments) {
			print '<pre>';
			print_r($rs_appointments);
		}


?>