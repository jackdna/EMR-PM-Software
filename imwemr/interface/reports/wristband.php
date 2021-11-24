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
set_time_limit(0);
$ignoreAuth = true;
session_start();
include_once(dirname(__FILE__)."/../../config/globals.php");
//$updir=substr(data_path(), 0, -1);
//$filepath = $updir."/patient_appointments_wristband.csv";
$filepath = "../../data/".PRACTICE_PATH."/patient_appointments_wristband.csv";
function download_file($fileNamePath){
	$filename = $fileNamePath;
	if($display_name==''){
		$display_name= end(explode("/",$filename));
	}
	$content_type = "text/csv";
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	
	header("Cache-Control: private",false);
	header("Content-Description: File Transfer");
	
	header("Content-Type: ".$content_type."; charset=utf-8");
	//die();
	header("Content-disposition:attachment; filename=\"".$display_name."\"");
	
	header("Content-Length: ".@filesize($filename));
	//echo filesize($filename);
	@readfile($filename) or die("File not found.");
	exit;	
}

$uname = $_REQUEST["uname"];
$pass = $_REQUEST["pass"];
if(trim($uname)=="") {
	$uname = $_SESSION["uname"];	
}
if(trim($pass)=="") {
	$pass = $_SESSION["pass"];	
}
/*if($_SERVER["REMOTE_ADDR"]!="71.178.167.95" && $_SERVER["REMOTE_ADDR"]!="96.95.2.229" && $_SERVER["REMOTE_ADDR"]!="122.180.85.165") {//RUN WITHOUT ACCESS ONLY FOR PALISADES SERVER
	if($uname!="" && $pass!="") {
		if($uname=="wristbandlist" && $pass=="R6mA%[3Uav") {
			//do nothing
			$_SESSION["uname"]=$uname;
			$_SESSION["pass"]=$pass;
		}else {
			$uname = $_REQUEST["uname"] = "";
			$pass = $_REQUEST["pass"] = "";	
		?>
			<span style="color:#F41E22;">Incorrect username or password</span>
		<?php		
		}
	}

	if($uname=="" || $pass=="") {
	?>
		<form name="frm_chk" method="post" enctype="multipart/form-data" action="wristband.php?dd=<?php echo $_REQUEST['dd'];?>">
		<input type="hidden" name="fac_id" value="<?php echo $_REQUEST["fac_id"];?>">
		<table cellpadding="5" cellspacing="5" border="0" style="text-align:center">
			<tr>
				<td>Username</td><td><input type="text" name="uname" required autocomplete="off"></td>
			</tr>
			<tr>    
				<td>Password</td><td><input type="password" name="pass" required autocomplete="off"></td>
			</tr>
			<tr>    
				<td colspan="2"><input type="submit" value="Submit"></td>
			</tr>
		</table>
		</form>
	<?php
		exit;
	}
}*/

//pre($_REQUEST);
$appointment_date="";
if($_REQUEST['dd']){
	$appt_date=$_REQUEST['dd'];
	$appt_dd=$appt_mm=$appt_yy="";
	$appt_yy=substr($appt_date,0,4);
	$appt_mm=substr($appt_date,4,2);
	$appt_dd=substr($appt_date,6,2);
	
	if(checkdate($appt_mm,$appt_dd,$appt_yy)==true){
		$appointment_date=$appt_yy."-".$appt_mm."-".$appt_dd;		
	}
}
if($appointment_date==""){
	$appointment_date=date("Y-m-d");	
}

$andSchFacQry = "";
$fac_id = trim($_REQUEST["fac_id"]);
if($fac_id) {
	$andSchFacQry = " AND sch.sa_facility_id = '".$fac_id."' ";	
}
	
$qrySchAppt="SELECT pd.fname as pt_fname,pd.lname as pt_lname,date_format(pd.DOB,'%m-%d-%Y') as pt_dob,sch.procedure_site as site,concat(us.lname,',',us.fname,' ',us.mname) as provider_name,sp.proc as pri_procedure,sp_s.proc as sec_procedure,sp_t.proc as tri_procedure  from schedule_appointments sch 
			LEFT JOIN patient_data pd ON pd.id = sch.sa_patient_id
			LEFT JOIN slot_procedures sp ON sp.id = sch.procedureid 
			LEFT JOIN slot_procedures sp_s ON sp_s.id = sch.sec_procedureid 
			LEFT JOIN slot_procedures sp_t ON sp_t.id = sch.tertiary_procedureid 
			LEFT JOIN users us ON us.id = sch.sa_doctor_id 
			WHERE sch.sa_app_start_date='".$appointment_date."' AND sch.sa_patient_app_status_id NOT IN(201,18,203) ".$andSchFacQry;
$resSchAppt=imw_query($qrySchAppt);
if(imw_num_rows($resSchAppt)>0){
	$fpH1 = fopen($filepath,'w');
	$content = '"Patient First Name"'.','.'"Patient Last Name"'.','.'"Date of Birth"'.','.'"Primary Procedure"'.','.'"Site"'.','.'"Surgeon"'.','.'"Secondary_Procedure"'.','.'"Tertiary_Procedure"';
	fwrite($fpH1, $content."\n");
	while($rowSchAppt=imw_fetch_assoc($resSchAppt)){
		$patFname='"'." ".'"';
		if(empty($rowSchAppt['pt_fname'])==false){
			$patFname=$rowSchAppt['pt_fname'];
			$patFname='"'.trim($patFname).'"';
		}
		$patLname='"'." ".'"';
		if(empty($rowSchAppt['pt_lname'])==false){
			$patLname=$rowSchAppt['pt_lname'];
			$patLname='"'.trim($patLname).'"';
		}
		$patDOB='"'." ".'"';
		if(empty($rowSchAppt['pt_dob'])==false){
			$patDOB=$rowSchAppt['pt_dob'];
			$patDOB='"'.trim($patDOB).'"';
		}
		$patSite='"'." ".'"';
		if(empty($rowSchAppt['site'])==false){
			$patSite=$rowSchAppt['site'];
			$patSite='"'.trim($patSite).'"';
		}
		$providerName='"'." ".'"';
		if(empty($rowSchAppt['provider_name'])==false){
			$providerName=$rowSchAppt['provider_name'];
			$providerName='"'.trim($providerName).'"';
		}
		$priProcedure='"'." ".'"';
		if(empty($rowSchAppt['pri_procedure'])==false){
			$priProcedure=$rowSchAppt['pri_procedure'];
			$priProcedure='"'.trim($priProcedure).'"';
		}
		$secProcedure='"'." ".'"';
		if(empty($rowSchAppt['sec_procedure'])==false){
			$secProcedure=$rowSchAppt['sec_procedure'];
			$secProcedure='"'.trim($secProcedure).'"';
		}
		$triProcedure='"'." ".'"';
		if(empty($rowSchAppt['tri_procedure'])==false){
			$triProcedure=$rowSchAppt['tri_procedure'];
			$triProcedure='"'.trim($triProcedure).'"';
		}
		$content = $patFname.','.$patLname.','.$patDOB.','.$priProcedure.','.$patSite.','.$providerName.','.$secProcedure.','.$triProcedure;
		fwrite($fpH1, $content."\n");
	}	
	download_file($filepath);
	fclose($fpH1);
}else{
	echo "No appointment created on ".$appointment_date."";	
}			
					
					
	


?>
