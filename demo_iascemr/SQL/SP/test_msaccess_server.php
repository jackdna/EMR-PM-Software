<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(500);
include('adodb5/adodb.inc.php');
//$accessPath = "D:\\surgicenterlive\\EyeLive.mdb"; 
//$accessPath = "//192.168.0.3\\Documents\\Surinder\\surgicenterlive\\EyeLive.mdb"; 
$accessPath = "\\\\Vinemain\\Database\\EyeLive.mdb"; 
$accessUid = "";
$accessPwd = "";
$db =&ADONewConnection('access');	

$dsn = "Driver={Microsoft Access Driver (*.mdb)};Dbq=$accessPath;Uid=$accessUid;Pwd=$accessPwd;";	
$db->Connect($dsn) or die('error1'.odbc_error());

	//$patient_dos = '2009-03-11';
	$patient_dos = '2009-05-26';
	
	if($patient_dos) {
		$appt_dos = date("d/m/Y",strtotime($patient_dos));
	}
	if($appt_dos=="") {
		$appt_dos = date("d/m/Y");
	}
	list($appt_dosDay,$appt_dosMonth,$appt_dosYear) = explode('/',$appt_dos);
	$appt_dosNextDay = date("d/m/Y",mktime(0,0,0,$appt_dosMonth,$appt_dosDay+1,$appt_dosYear));
	
	
		echo $sql_appointments="
					SELECT appointments.* FROM appointments 
					WHERE appointments.appointment_date_time > #$appt_dos#  
					AND appointments.appointment_date_time < #$appt_dosNextDay# 
					AND appointments.appointment_status=0 
					ORDER BY DateValue([appointment_date_time]), TimeValue([appointment_date_time]);
					";
					
		$rs_appointments = $db->Execute($sql_appointments) or die('error2'.odbc_errormsg());
		if($rs_appointments) {
			print '<pre>';
			print_r($rs_appointments);
		}

?>