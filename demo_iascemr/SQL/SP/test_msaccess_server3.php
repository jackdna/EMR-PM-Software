<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(500);
include('adodb5/adodb.inc.php');

$accessPath = "\\\\Vinemain\\Database\\EyeLive.mdb"; 
//$accessPath = "D:\\SampleData\\EyeLive.mdb"; 

$accessUid = "";
$accessPwd = "";
$db =&ADONewConnection('access');	

$dsn = "Driver={Microsoft Access Driver (*.mdb)};Dbq=$accessPath;Uid=$accessUid;Pwd=$accessPwd;";	
$db->Connect($dsn) or die('error1'.odbc_error());

	$patient_dos = '2009-05-26';
	if($patient_dos) {
		$appt_dos = date("Y-m-d",strtotime($patient_dos));
	}
		$sql_appointments="
					SELECT appointments.appointment_date_time FROM appointments 
					WHERE appointments.appointment_date_time > #5/26/2009#  AND 
					appointments.appointment_date_time < #5/27/2009# AND
					appointments.appointment_status=0 order by appointment_date_time
					
					";
					

		$rs_appointments = $db->Execute($sql_appointments) or die('error2'.odbc_errormsg());

		if($rs_appointments) {
			print '<pre>';
			print_r($rs_appointments);
		}


?>