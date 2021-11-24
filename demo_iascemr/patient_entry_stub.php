<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
include("common/conDb.php");
include("common/linkfile.php");
include_once("admin/classObjectFunction.php");
$dos=$_REQUEST['dos'];
$surgeon_fname=$_REQUEST['surgeon_fname'];
$surgeon_mname=$_REQUEST['surgeon_mname'];
$surgeon_lname=$_REQUEST['surgeon_lname'];

$surgery_time_hh=$_REQUEST['surgery_time_hh'];
$surgery_time_mm=$_REQUEST['surgery_time_mm'];
$surgery_time_ss=$_REQUEST['surgery_time_ss'];

$pickup_time_hh=$_REQUEST['pickup_time_hh'];
$pickup_time_mm=$_REQUEST['pickup_time_mm'];
$pickup_time_ss=$_REQUEST['pickup_time_ss'];

$arrival_time_hh=$_REQUEST['arrival_time_hh'];
$arrival_time_mm=$_REQUEST['arrival_time_mm'];
$arrival_time_ss=$_REQUEST['arrival_time_ss'];


$pickup_time=$_REQUEST['pickup_time'];
$arrival_time=$_REQUEST['arrival_time'];
$patient_first_name=$_REQUEST['patient_first_name'];
$patient_middle_name=$_REQUEST['patient_middle_name'];
$patient_last_name=$_REQUEST['patient_last_name'];
$patient_dob=$_REQUEST['patient_dob'];
$patient_sex=$_REQUEST['patient_sex'];
$patient_street1=$_REQUEST['patient_street1'];
$patient_street2=$_REQUEST['patient_street2'];
$patient_city=$_REQUEST['patient_city'];
$patient_state=$_REQUEST['patient_state'];
$patient_zip=$_REQUEST['patient_zip'];
$patient_home_phone=$_REQUEST['patient_home_phone'];
$patient_work_phone=$_REQUEST['patient_work_phone'];
$patient_primary_procedure=$_REQUEST['patient_primary_procedure'];
$patient_secondary_procedure=$_REQUEST['patient_secondary_procedure'];
$site=$_REQUEST['site'];
$assisted_by_translator=$_REQUEST['assisted_by_translator'];
$anesthesiologist_fname=$_REQUEST['anesthesiologist_fname'];
$anesthesiologist_mname=$_REQUEST['anesthesiologist_mname'];
$anesthesiologist_lname=$_REQUEST['anesthesiologist_lname'];
$confirming_nurse_fname=$_REQUEST['confirming_nurse_fname'];
$confirming_nurse_mname=$_REQUEST['confirming_nurse_mname'];
$confirming_nurse_lname=$_REQUEST['confirming_nurse_lname'];
if($_REQUEST['submit']!='') {
	$dosTemp = list($stubMonth,$stubDay,$stubYear) = explode('-',$dos);
	$dos = $stubYear."-".$stubMonth."-".$stubDay;
	
	$patient_dobTemp = list($patient_dobMonth,$patient_dobDay,$patient_dobYear) = explode('-',$patient_dob);
	$patient_dob = $patient_dobYear."-".$patient_dobMonth."-".$patient_dobDay;
	
	if($surgery_time_ss=="PM") {
		$surgery_time_hh=$surgery_time_hh+12;
	}
	$surgery_time = $surgery_time_hh.":".$surgery_time_mm.":00";
	
	if($pickup_time_ss=="PM") {
		$pickup_time_hh=$pickup_time_hh+12;
	}
	$pickup_time = $pickup_time_hh.":".$pickup_time_mm.":00";

	if($arrival_time_ss=="PM") {
		$arrival_time_hh=$arrival_time_hh+12;
	}
	$arrival_time = $arrival_time_hh.":".$arrival_time_mm.":00";

	
	$insertStubQuery=("INSERT INTO `stub_tbl` (`stub_id`, `dos`, `surgeon_fname`, `surgeon_mname`, `surgeon_lname`, `surgery_time`, `pickup_time`, `arrival_time`, `patient_first_name`, `patient_middle_name`, `patient_last_name`, `patient_dob`, `patient_sex`, `patient_street1`, `patient_street2`, 
	`patient_city`, `patient_state`, `patient_zip`, `patient_home_phone`, `patient_work_phone`, 
	`patient_primary_procedure`, `patient_secondary_procedure`, `patient_status`, `ascId`, `site`, `assisted_by_translator`, `anesthesiologist_fname`, `anesthesiologist_mname`, `anesthesiologist_lname`, `confirming_nurse_fname`, `confirming_nurse_mname`, `confirming_nurse_lname`) 
	VALUES (
	'Null', '$dos', '$surgeon_fname', '$surgeon_mname', '$surgeon_lname', '$surgery_time', '$pickup_time', '$arrival_time', '$patient_first_name', '$patient_middle_name', '$patient_last_name', '$patient_dob', '$patient_sex', '$patient_street1', '$patient_street2', 
	'$patient_city', '$patient_state', '$patient_zip', '$patient_home_phone', '$patient_work_phone', 
	'$patient_primary_procedure', '$patient_secondary_procedure', 'Scheduled', 'Null', '$site', '$assisted_by_translator', '$anesthesiologist_fname', '$anesthesiologist_mname', '$anesthesiologist_lname', '$confirming_nurse_fname', '$confirming_nurse_mname', '$confirming_nurse_lname') 
	");
	//echo $insertStubQuery;
	
	imw_query($insertStubQuery);
	echo "<h5 align='center'>Record Inserted</h5>";
}
?>
<style>
	.text_10
	{
	font-family:verdana;
	font-size:14;
	}
</style>
<script language="javascript">
	function formValidation()
	{	
		
			
		
		var msg="Please fill all the mandatory fields:- \n";
		var flag = 0;
		
		var dos=document.stubEntry.dos.value;
		var surgeon_fname=document.stubEntry.surgeon_fname.value;
		var patient_first_name=document.stubEntry.patient_first_name.value;
		var patient_last_name=document.stubEntry.patient_last_name.value;
		var patient_dob=document.stubEntry.patient_dob.value;
		var patient_street1=document.stubEntry.patient_street1.value;
		var patient_city=document.stubEntry.patient_city.value;
		var patient_state=document.stubEntry.patient_state.value;
		var patient_zip=document.stubEntry.patient_zip.value;
		var patient_home_phone=document.stubEntry.patient_home_phone.value;
		var patient_primary_procedure=document.stubEntry.patient_primary_procedure.value;
		var site=document.stubEntry.site.value;
		var anesthesiologist_fname=document.stubEntry.anesthesiologist_fname.value;
		var confirming_nurse_fname=document.stubEntry.confirming_nurse_fname.value;
		
		
		if(dos==''){ msg = msg+"\t� Dos\n"; ++flag; }
		if(surgeon_fname==''){ msg = msg+"\t� Surgeon_Name\n"; ++flag; }
		if(patient_first_name==''){ msg = msg+"\t� Patient First Name\n"; ++flag; }
		if(patient_last_name==''){ msg = msg+"\t� Patient Last Name\n"; ++flag; }
		if(patient_dob==''){ msg = msg+"\t� Patient_DOB\n"; ++flag; }
		if(patient_street1==''){ msg = msg+"\t� Patient_Street1\n"; ++flag; }
		if(patient_city==''){ msg = msg+"\t� Patient_City\n"; ++flag; }
		if(patient_state==''){ msg = msg+"\t� Patient_State\n"; ++flag; }
		if(patient_zip==''){ msg = msg+"\t� Patient_Zip\n"; ++flag; }
		if(patient_home_phone==''){ msg = msg+"\t� Patient_Home_Phone\n"; ++flag; }
		if(patient_primary_procedure==''){ msg = msg+"\t� Patient_Primary_Procedure\n"; ++flag; }
		if(site==''){ msg = msg+"\t� Site\n"; ++flag; }
		if(anesthesiologist_fname==''){ msg = msg+"\t� Anesthesiologist_Name\n"; ++flag; }
		if(confirming_nurse_fname==''){ msg = msg+"\t� Confirming_Nurse_Name\n"; ++flag; }
		
		if(flag > 0){
				alert(msg);
				return false;	
		}
		document.stubEntry.submit();
		/*
		//var dos=document.stubEntry.dos.value;
		alert(sname);
				if(surgeon_fname==''){ msg = msg+"\t� Name\n"; ++flag; }
				//if(dos==''){ msg = msg+"\t� Name\n"; ++flag; }
		*/		

		
	}
	// CALENDER
var today = new Date();
var day = today.getDate();
var month = today.getMonth()
var year = y2k(today.getYear());
var mon=month+1;
if(mon<=9){
	mon='0'+mon;
}
var todaydate=mon+'-'+day+'-'+year;

function y2k(number){
	return (number < 1000)? number+1900 : number;
}
function newWindow(q){
	mywindow=open('mycal1.php?md='+q,'','width=200,height=250,top=200,left=300');
	if(mywindow.opener == null)
		mywindow.opener = self;
}
function restart(q){
	fillDate = ''+ padout(month - 0 + 1) + '-'  + padout(day) + '-' +  year;
	if(q==8){
		if(fillDate > todaydate){
			alert("Date Of Service can not be a future date")
			return false;
		}
	}
	document.getElementById(q).value=fillDate;
	mywindow.close();
}
function padout(number){
	return (number < 10) ? '0' + number : number;
}
// CALENDER
</script>

<body leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0">
<form name="stubEntry" action="patient_entry_stub.php"  method="post" onSubmit="return formValidation();">
<table width="100%"  cellpadding="0" cellspacing="0">
	<tr>
		<td>
			<table width="60%"  border="1" align="center" class="text_10" bgcolor="#FFFFFF" cellpadding="0" cellspacing="0">
				<tr>
					<td align="center" colspan="2">Patient Registration</td>
				</tr>
				<tr>
					<td width="40%">
						*Dos
					</td>
					<td width="60%">
						<input type="text" id="dos_id" size="10" name="dos" value="<?php //echo $dos; ?>">
						<img onClick="newWindow('dos_id')" src="images/icon_cal.jpg" width="20" height="20" border="0" align="middle" >
					</td>
				</tr>
				<tr>
					<td width="40%">
						*Surgeon_Name
					</td>
					<td width="60%">
						<input type="text" size="10" name="surgeon_fname" value="<?php echo $surgeon_fname; ?>">
						<input type="text" size="5" name="surgeon_mname" value="<?php echo $surgeon_mname; ?>">
						<input type="text" size="10" name="surgeon_lname" value="<?php echo $surgeon_lname; ?>">
					</td>
				</tr>
				<tr>
					<td width="40%">
						*Surgery_Time
					</td>
					<td width="60%">
						HH:<select name="surgery_time_hh">
							<!-- <option value="">Select</option> -->
							<?php for($i=1;$i<=12;$i++)
							{
							?>
							<option value="<?php echo $i; ?>" <?php if($i==10) { echo "selected";}?>><?php echo $i; ?></option>
							<?php } ?>
						</select>
						MM:<select name="surgery_time_mm">
							<!-- <option value="">Select</option> -->
							<?php for($i=0;$i<60;$i++)
							{
							?>
							<option value="<?php echo $i; ?>" <?php if($i==30) { echo "selected";}?>><?php echo $i; ?></option>
							<?php } ?>
						</select>
						<select name="surgery_time_ss">
							<option value="AM" selected>AM</option>
							<option value="PM">PM</option>
						</select>
					</td>
				</tr>
				<tr>
					<td width="40%">
						*Pickup_Time
					</td>
					<td width="60%">
					HH:<select name="pickup_time_hh">
							<!-- <option value="">Select</option> -->
							<?php for($i=1;$i<=12;$i++)
							{
							?>
							<option value="<?php echo $i; ?>" <?php if($i==10) { echo "selected";}?>><?php echo $i; ?></option>
							<?php } ?>
						</select>
						MM:<select name="pickup_time_mm">
							<!-- <option value="">Select</option> -->
							<?php for($i=0;$i<60;$i++)
							{
							?>
							<option value="<?php echo $i; ?>" <?php if($i==15) { echo "selected";}?>><?php echo $i; ?></option>
							<?php } ?>
						</select>
						<select name="pickup_time_ss">
							<option value="AM" selected>AM</option>
							<option value="PM">PM</option>
						</select>
					</td>
				</tr>
				<tr>
					<td width="40%">
						*Arrival_Time
					</td>
					<td width="60%">
					HH:<select name="arrival_time_hh">
							<!-- <option value="">Select</option> -->
							<?php for($i=1;$i<=12;$i++)
							{
							?>
							<option value="<?php echo $i; ?>" <?php if($i==10) { echo "selected";}?>><?php echo $i; ?></option>
							<?php } ?>
						</select>
						MM:<select name="arrival_time_mm">
							<!-- <option value="">Select</option> -->
							<?php for($i=0;$i<60;$i++)
							{
							?>
							<option value="<?php echo $i; ?>" <?php if($i==20) { echo "selected";}?>><?php echo $i; ?></option>
							<?php } ?>
						</select>
						<select name="arrival_time_ss">
							<option value="AM" selected>AM</option>
							<option value="PM">PM</option>
						</select>
					</td>
				</tr>
				<tr>
					<td width="40%">
						*Patient_Name
					</td>
					<td width="60%">
						<input type="text" size="10" name="patient_first_name" value="<?php echo $patient_first_name; ?>">
						<input type="text" size="5" name="patient_middle_name" value="<?php echo $patient_middle_name; ?>">
						<input type="text" size="10" name="patient_last_name" value="<?php echo $patient_last_name; ?>">
					</td>
				</tr>
				
				<tr>
					<td width="40%">
						*Patient_DOB
					</td>
					<td width="60%">
						<input type="text" size="10" name="patient_dob" value="">
						<img onClick="newWindow('patient_dob')" src="images/icon_cal.jpg" width="20" height="20" border="0" align="middle" >
					</td>
				</tr>
				<tr>
					<td width="40%">
						*Patient_Sex
					</td>
					<td width="60%">
						Male:<input type="radio" name="patient_sex" checked  value="m">
						Female:<input type="radio" name="patient_sex"  value="f">
					</td>
				</tr>
				<tr>
					<td width="40%">
						*Patient_Street1
					</td>
					<td width="60%">
						<input type="text" size="30" name="patient_street1" value="<?php echo $patient_street1; ?>">
					</td>
				</tr>
				<tr>
					<td width="40%">
						*Patient_Street2
					</td>
					<td width="60%">
						<input type="text" size="30" name="patient_street2" value="<?php echo $patient_street2; ?>">
					</td>
				</tr>
				<tr>
					<td width="40%">
						*Patient_City
					</td>
					<td width="60%">
						<input type="text" size="30" name="patient_city" value="<?php echo $patient_city; ?>">
					</td>
				</tr>
				<tr>
					<td width="40%">
						*Patient_State
					</td>
					<td width="60%">
						<input type="text" size="30" name="patient_state" value="<?php echo $patient_state; ?>">
					</td>
				</tr>
				<tr>
					<td width="40%">
						*Patient_Zip
					</td>
					<td width="60%">
						<input type="text" size="30" name="patient_zip" value="<?php echo $patient_zip; ?>">
					</td>
				</tr>
				<tr>
					<td width="40%">
						*Patient_Home_Phone
					</td>
					<td width="60%">
						<input type="text" size="30" name="patient_home_phone" value="<?php echo $patient_home_phone; ?>">
					</td>
				</tr>
				<tr>
					<td width="40%">
						*Patient_Work_Phone
					</td>
					<td width="60%">
						<input type="text" size="30" name="patient_work_phone" value="<?php echo $patient_work_phone; ?>">
					</td>
				</tr>
				<tr>
					<td width="40%">
						*Patient_Primary_Procedure
					</td>
					<td width="60%">
						<input type="text" size="30" name="patient_primary_procedure" value="<?php echo $patient_primary_procedure; ?>">
					</td>
				</tr>
				<tr>
					<td width="40%">
						*Patient_Secondary_Procedure
					</td>
					<td width="60%">
						<input type="text" size="30" name="patient_secondary_procedure" value="<?php echo $patient_secondary_procedure; ?>">
					</td>
				</tr>
				<tr>
					<td width="40%">
						*Site
					</td>
					<td width="60%">
						<select name="site" value="<?php echo $site; ?>">
							<option value="select">Select Site</option>
							<option value="left" selected>Left</option>
							<option value="right">Right</option>
							<option value="both">Both</option>
						</select>
					</td>
				</tr>
				<!--tr>
					<td width="40%">
						*Assisted_By_Translator
					</td>
					<td width="60%">
						<input type="text" size="30" name="assisted_by_translator" value="<?php echo $assisted_by_translator; ?>">
					</td>
				</tr-->
				<tr>
					<td width="40%">
						*Anesthesiologist_Name
					</td>
					<td width="60%">
						<input type="text" size="10" name="anesthesiologist_fname" value="<?php echo $anesthesiologist_fname; ?>">
						<input type="text" size="5" name="anesthesiologist_mname" value="<?php echo $anesthesiologist_mname; ?>">
						<input type="text" size="10" name="anesthesiologist_lname" value="<?php echo $anesthesiologist_lname; ?>">
					</td>
				</tr>
				
				<tr>
					<td width="40%">
						*Confirming_Nurse_Name
					</td>
					<td width="60%">
						<input type="text" size="10" name="confirming_nurse_fname" value="<?php echo $confirming_nurse_fname; ?>">
						<input type="text" size="5" name="confirming_nurse_mname" value="<?php echo $confirming_nurse_mname; ?>">
						<input type="text" size="10" name="confirming_nurse_lname" value="<?php echo $confirming_nurse_lname; ?>">
					</td>
				</tr>
				
				<tr>
					<td colspan="2" align="center"><input type="submit" name="submit" value="Register" onClick="return formValidation();"></td>
				</tr>
				
			</table>
		</td>
	</tr>
	
</table>
</form>
</body>