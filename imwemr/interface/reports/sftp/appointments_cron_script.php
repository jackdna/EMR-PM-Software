<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
?>
<?php
$ignoreAuth = true;

if($argv[1]){
	$practicePath = trim($argv[1]);
	$_SERVER['REQUEST_URI'] = $practicePath;
}

include_once(dirname(__FILE__)."/../../../config/globals.php");
include_once('../../../library/classes/cls_common_function.php');

/*
FILE : cron_for_appointments.php
PURPOSE : 
ACCESS TYPE : Indirect

*/

$page_title = "Export CSV Feed";
$dirName='export_files_'.$practicePath;
if(!is_dir($dirName)){
	mkdir($dirName);
}

$fileName='appt.txt';
$filePath= $dirName.'/'.$fileName;
$pfx=",";

//TEMPRORY JUST RECORDING EXECUTION CONFIRMATION OF FILE
file_put_contents('test_file_log.txt', "Executed on :".date('Y-m-d H:i:s')."\n", FILE_APPEND);

//GET ID FOR FACILITY "Pilkinton Eye Center"
$res = imw_query("Select id from facility where TRIM(LOWER(name))='pilkinton eye center'");
$row = imw_fetch_assoc($res);
$facility_id=$row["id"];


//GETTING ID OF "Post Op/One Day" and "Post Op/DSAEK w RDP/One Day" PROCEDURE BECAUSE THAT WILL NOT DISPLAY IN REPORT.
$rs=imw_query("Select id FROM slot_procedures WHERE LOWER(proc) IN ('post op/one day', 'post op/dsaek w rdp/one day')");
$arr_procedure_id=array();
while($res=imw_fetch_assoc($rs)){
	$arr_procedure_id[$res['id']]=$res['id'];
}
$str_procedure_id='';
if(sizeof($arr_procedure_id)>0){
	$str_procedure_id = implode(',', $arr_procedure_id);	
}

//GETTING WORKING DAY DATES BECAUSE CRON WILL EXECUTE 1 DAY BEFORE AT 9:30 PM
$day=date('l');
if($day=='Wednesday' || $day=='Thursday' || $day=='Friday'){
	$from_date=date('Y-m-d', strtotime("+5 days"));
	$to_date=$from_date;
	
}else{
	$from_date=date('Y-m-d', strtotime("+3 days"));
	$to_date=$from_date;
}

//$from_date='2016-01-01';
//$to_date='2016-09-30';

$recno=0;
$exceltext="";
$exceltext.="Patient Name".$pfx;
$exceltext.="Patient Home Phone".$pfx;
$exceltext.="Patient Mobile Number".$pfx;
$exceltext.="Appointment Date".$pfx;
$exceltext.="Appointment Time".$pfx;
$exceltext.="Patient Account Number".$pfx;
$exceltext.="Doctor Number".$pfx;
$exceltext.="Procedure Number".$pfx;

$exceltext.="Doctor Name".$pfx;
$exceltext.="Procedure Name".$pfx;
$exceltext.="Location (office) Name".$pfx;

$exceltext.="Patient Address".$pfx;
$exceltext.="Patient City".$pfx;
$exceltext.="Patient State".$pfx;
$exceltext.="Patient Zip Code".$pfx;
$exceltext.="Patient Email Address";
$fp=fopen($filePath,"w");
fwrite($fp,$exceltext);
fclose($fp);
		
$qry="SELECT *,schedule_appointments.id as appt_id, TIME_FORMAT(schedule_appointments.sa_app_starttime, '%h:%i %p') as 'app_start_time'  
FROM schedule_appointments
INNER JOIN patient_data ON patient_data.id = schedule_appointments.sa_patient_id
WHERE patient_data.hipaa_voice = '1' 
AND (schedule_appointments.sa_app_start_date BETWEEN '$from_date' AND '$to_date') 
AND schedule_appointments.sa_patient_app_status_id NOT IN(201,18,203)";
if(empty($str_procedure_id)==false){
	$qry.=" AND schedule_appointments.procedureid NOT IN (".$str_procedure_id.")";
}
if($facility_id>0){ //FETCH RECORD ONLY FOR FACILITY "Pilkinton Eye Center"
	$qry.=" AND schedule_appointments.sa_facility_id ='".$facility_id."'";	
}

$rs=imw_query($qry);

while($row=imw_fetch_assoc($rs)){

	$nameFormated="";
	$nameFormated=$row["lname"]."".$row["suffix"].",".$row["fname"]." ".substr($row["mname"],0,1);

	$app_time = $row["app_start_time"];
	$phone_default = $row["phone_home"];
	$prefer_contact = $row["preferr_contact"];
	if($prefer_contact == 0)
	{
		if(trim($row["phone_home"]) != ""){$phone_default = $row["phone_home"]; }
	}
	else if($prefer_contact == 1)
	{
		if(trim($row["phone_biz"]) != ""){$phone_default = $row["phone_biz"]; }				
	}
	else if($prefer_contact == 2)
	{
		if(trim($row["phone_cell"]) != ""){$phone_default = $row["phone_cell"]; }				
	}

	$dat_app1=strtotime($row['sa_app_start_date']);
	$dat_apP = date("".phpDateFormat()."",$dat_app1);
	
	$my_arr[0] =addDoubleQuaotes(($nameFormated));
	$my_arr[1] =addDoubleQuaotes($phone_default);
	$my_arr[2] =addDoubleQuaotes($row["phone_cell"]);
	$my_arr[3] =addDoubleQuaotes($dat_apP);
	$my_arr[4] =addDoubleQuaotes($app_time);
	$my_arr[5] =$row["sa_patient_id"];
	$my_arr[6] =$row["sa_doctor_id"];
	$my_arr[7] =$row["procedureid"];
	
	$my_arr[8] =addDoubleQuaotes(str_replace("&nbsp;"," ",dispDoctorName($row["sa_doctor_id"])));
	$my_arr[9] =addDoubleQuaotes(getProcedureName_lc($row["procedureid"]));
	$my_arr[10] =addDoubleQuaotes(getLocationName($row["sa_facility_id"]));
	
	$my_arr[11] =addDoubleQuaotes(spchar($row["street"]));//Address
	$my_arr[12] =addDoubleQuaotes(spchar($row["city"]));
	$my_arr[13] =addDoubleQuaotes(spchar($row["state"]));
	$my_arr[14] =addDoubleQuaotes($row["postal_code"]);
	$my_arr[15] =addDoubleQuaotes($row["email"]);	
	

	$exceltext="";	
	$exceltext="\n";
	for($k=0;$k<count($my_arr);$k++)
	{
		$exceltext.=$my_arr[$k].$pfx;
	}
	$exceltext=@substr($exceltext,0,strlen($exceltext)-1);
	$fp=@fopen($filePath,"a+");
	@fwrite($fp,$exceltext);
	@fclose($fp);	
}




//UPLOAD FILE ON SERVER
if(file_exists($filePath)){
	include 'upload.php';
}

//FUNCTIONS
function addDoubleQuaotes($stringVal){
	if($stringVal!=""){
	 $stringVal='"'.$stringVal.'"';
	 }
	 return $stringVal;
}
function dispDoctorName($id,$flg="0")
{
	if(($id != 0) && !empty($id))
	{
		$sql = "SELECT lname, mname, fname, pro_suffix,id
				FROM users
				WHERE id = '$id';
				";
		$rez = sqlQuery($sql);
		$lname = !empty($rez["lname"]) ? $rez["lname"].",&nbsp;" : "";
		$mname = !empty($rez["mname"]) ? $rez["mname"]."&nbsp;" : "";
		$fname = !empty($rez["fname"]) ? $rez["fname"]."&nbsp;" : "";
		$ps = $rez["pro_suffix"];

		if($flg=="2"){
			$name = $lname.$fname.$mname.$ps;

		}else if($flg=="1"){
			$name = $rez['fname'];
			$name .= !empty($rez['lname']) ? "&nbsp;".strtoupper(substr($rez['lname'],0,1))."" : "" ;
			$name = (strlen($name) > 30) ? substr($name,0,28).".." : $name;
		}else {
			$name = $lname.$fname.$mname;
		}

		return $name;
	}
	return "";
}
function getProcedureName_lc($procId){
	$retName="";
	if($procId!=""){
	$res = sqlStatement("select proc from slot_procedures where id='$procId'");
	while($row = sqlFetchArray($res)){
	$retName=$row["proc"];
	}
  }	
  return $retName;
}
function getLocationName($locId){
	$retName="";
	if($locId!=""){
	$res = sqlStatement("select * from facility where id='$locId'");
	while($row = sqlFetchArray($res)){
	$retName=$row["name"];
	}
  }	
  return $retName;
}
function spchar($val){
	$val=trim(str_replace("&","&amp;",$val));
	$val=str_replace("<","&lt;",$val);
	$val=str_replace(">","&gt;",$val);
	$val=str_replace("'","&apos;",$val);
	$val=str_replace("","",$val);
	$val=str_replace("\"","&quot;",$val);
	$car_aux = "\n";
	$val=ereg_replace($car_aux,"",$val);
	$car_aux = "\r";
	$val=ereg_replace($car_aux,"",$val);
	$car_aux = "\r\n";
	$val=ereg_replace($car_aux,"",$val);
	$rval="";
	for ($i=0;$i<strlen($val);$i++){
		if(ord(substr($val,$i,1))<=127){
			$rval.=substr($val,$i,1);
		}
	}
	return $rval;
}
?>
