<?php 
$page_title = "Export CSV Feed";
$filename = data_path().'users/UserId_'.$_SESSION['authId'].'/appt.txt';
$fileInfo = pathinfo($filename);
if(!is_dir($fileInfo['dirname'])) mkdir($fileInfo['dirname'], 0777, true);

if(in_array(strtolower($billing_global_server_name), array('arizonaeye'))){
	$pfx="|";
} else{
	$pfx=",";	
}

function mysql_date_format($strdate){
	if($strdate!=""){
	$date_array=explode("-",$strdate);
	$sqldate=$date_array["2"]."-".$date_array["0"]."-".$date_array["1"];//yy,mm,dd
	}else{
	$sqldate="0000-00-00";
	}
	return($sqldate);	
	}
function spchar($val){
	$val=trim(str_replace("&","&amp;",$val));
	$val=str_replace("<","&lt;",$val);
	$val=str_replace(">","&gt;",$val);
	$val=str_replace("'","&apos;",$val);
	$val=str_replace("","",$val);
	$val=str_replace("\"","&quot;",$val);
	$car_aux = "\n";
	$val=preg_replace($car_aux,"",$val);
	$car_aux = "\r";
	$val=preg_replace($car_aux,"",$val);
	$car_aux = "\r\n";
	$val=preg_replace($car_aux,"",$val);
	$rval="";
	for ($i=0;$i<strlen($val);$i++){
		if(ord(substr($val,$i,1))<=127){
			$rval.=substr($val,$i,1);
		}
	}
	return $rval;
}


function check_words($str,$num){
	$retval= substr($str,0,$num);
	if((substr($str,$num,1)!=" ") && (ord(substr($str,$num,1))!=0)){
		$retval=substr($retval,0,strrpos($retval," "));
	}
	return $retval;
}
function addDoubleQuaotes($stringVal){
	if($stringVal!=""){
		if(in_array(strtolower($billing_global_server_name), array('arizonaeye'))){
			$stringVal = $stringVal;
		}else {
			$stringVal = '"'.$stringVal.'"'; 
		}
	 }
	 return $stringVal;
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
///Functions To Format Data Export//
$to_date=mysql_date_format($_REQUEST["to_date"]);
$from_date=mysql_date_format($_REQUEST["from_date"]);

$doctor_ID="";
if(is_array($_REQUEST["doctor_IDS"])){
	foreach($_REQUEST["doctor_IDS"] as $intKey => $intVal){
		if(empty($intVal) == true){
			unset($_REQUEST["doctor_IDS"][$intKey]);
		}
	}
	$doctor_ID=implode(",",$_REQUEST["doctor_IDS"]);	
//print($doctor_ID);

}else if($_REQUEST["doctor_ID"]!=""){
	$doctor_ID=$_REQUEST["doctor_ID"];
}else{
	$doctor_ID="";
}

//To get Procedure Id
$procedure_ID="";
if(is_array($_REQUEST["procedureType"]) && (sizeof($arrDDProcCount)!=sizeof($_REQUEST["procedureType"]))){
	foreach($_REQUEST["procedureType"] as $intKey => $intVal){
		if(empty($intVal) == true){
			unset($_REQUEST["procedureType"][$intKey]);
		}
	}
	$procedure_ID=implode(",",$_REQUEST["procedureType"]);	
//print($doctor_ID);

}

$chkhipaa_voice="";	$pam=''; $phoneTree='';
if($_REQUEST['repType']=='House_Calls')
{
	$chkhipaa_voice = "Yes";
}
if($_REQUEST['repType']=='pam')
{
	$pam = 1;
}
if($_REQUEST['repType']=='phoneTree')
{
	$phoneTree = 1;
}
if($_REQUEST['repType']=='send_email')
{
	$send_email = 1;
	$hipaaEmail = " AND patient_data.hipaa_email='1' AND patient_data.email<>''";
}

if($excSentEmail)
{
	//get ids with sent mail in date range
	$query=imw_query("select appt_id from exclude_sent_email where appt_date >= '$from_date' and appt_date <='$to_date' and report='Day Appt'");
	if(imw_num_rows($query)>=1)
	{
		while($data=imw_fetch_object($query))
		{
			$apptIds[]=$data->appt_id;
		}	
		$apptIdStr=implode(',',$apptIds);
	}
	
	if($apptIdStr)
	{
		$hipaaEmail .= " AND schedule_appointments.id NOT IN($apptIdStr)";	
	}
}
		
$facl="";
if(is_array($_REQUEST["facl"])){
	$facl=implode(",",$_REQUEST["facl"]);
//print($doctor_ID);

}else if($_REQUEST["facl"]!=""){
	$facl=$_REQUEST["facl"];
}else{
	$facl="";
}


if(($recno=="") || ($recno==0)){
	if($pam==1){
		$recno=0;
		$exceltext="";
		$exceltext.="Account-ID".$pfx;
		$exceltext.="Message Type".$pfx;
		$exceltext.="Office".$pfx;
		$exceltext.="Language Type".$pfx;
		$exceltext.="Patient Fname".$pfx;
		$exceltext.="Patient Lname".$pfx;
		$exceltext.="App Date".$pfx;
		
		$exceltext.="App Time".$pfx;
		$exceltext.="Provider".$pfx;
		$exceltext.="App Type".$pfx;
		
		$exceltext.="Home Phone".$pfx;
		$exceltext.="Email".$pfx;
		$exceltext.="Cell Phone";
		$fp=fopen($filename,'w');
		@fwrite($fp,$exceltext);
		@fclose($fp);
	}
	else{
		$fp=fopen($filename,'w');
		@fwrite($fp,$exceltext);
		@fclose($fp);
	}
	if($chkhipaa_voice=='Yes'){
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
		$fp=fopen($filename,'w');
		@fwrite($fp,$exceltext);
		@fclose($fp);
	}
	else{
		$fp=fopen($filename,'w');
		@fwrite($fp,$exceltext);
		@fclose($fp);
	}
	if($phoneTree==1){
		$recno=0;
		$exceltext="";
		$exceltext.="Patient Name".$pfx;
		$exceltext.="Appointment Date".$pfx;
		$exceltext.="Appointment Time".$pfx;
		$exceltext.="Home Phone".$pfx;
		$exceltext.="Mobile Phone".$pfx;
		$exceltext.="Email Address".$pfx;
		$exceltext.="Doctor Name".$pfx;
		$exceltext.="Location(office)".$pfx;
		$exceltext.="Appointment Type".$pfx;
		$fp=fopen($filename,'w');
		@fwrite($fp,$exceltext);
		@fclose($fp);
	}
	else{
		$fp=fopen($filename,'w');
		@fwrite($fp,$exceltext);
		@fclose($fp);
	}
	
}
$chk_patient_avail=" and schedule_appointments.sa_patient_app_status_id NOT IN(201,18,203)";
if($procedure_ID != ""){
	$chk_patient_avail.= " AND schedule_appointments.procedureid IN(".$procedure_ID.")" ;
}
if($patHavingPhoneNotEmail=='1'){
	$chk_patient_avail.= " AND (patient_data.email='' AND (patient_data.phone_home!='' OR patient_data.phone_biz!='' OR patient_data.phone_cell!=''))";
}

if($chkhipaa_voice=="" && $facl!="" && $doctor_ID==""){
		$query_no="SELECT count(patient_data.id) FROM schedule_appointments USE INDEX(sa_multiplecol)
		INNER JOIN patient_data ON patient_data.id = schedule_appointments.sa_patient_id
		WHERE 
		schedule_appointments.sa_facility_id in($facl)
		AND schedule_appointments.sa_app_start_date >= '$from_date' and schedule_appointments.sa_app_start_date <='$to_date'
		$chk_patient_avail
		$hipaaEmail";
	} else if($chkhipaa_voice=="Yes" && $facl!="" && $doctor_ID==""){
		
		$query_no="SELECT count(patient_data.id) FROM schedule_appointments USE INDEX(sa_multiplecol)
		INNER JOIN patient_data ON patient_data.id = schedule_appointments.sa_patient_id
		WHERE patient_data.hipaa_voice = '1'
		AND schedule_appointments.sa_facility_id in($facl)
		AND schedule_appointments.sa_app_start_date >= '$from_date' and schedule_appointments.sa_app_start_date <='$to_date'
		$chk_patient_avail
		$hipaaEmail";
	} else 	if($doctor_ID!="" && $facl!="" && $chkhipaa_voice==""){
		
		$query_no = "SELECT count(patient_data.id) FROM schedule_appointments USE INDEX(sa_multiplecol)
		INNER JOIN patient_data ON patient_data.id = schedule_appointments.sa_patient_id
		WHERE  schedule_appointments.sa_doctor_id in($doctor_ID)
		AND schedule_appointments.sa_facility_id in($facl)
		AND schedule_appointments.sa_app_start_date >= '$from_date' and schedule_appointments.sa_app_start_date <='$to_date'
		$chk_patient_avail
		$hipaaEmail";
	}else 	if($doctor_ID!="" && $facl=="" && $chkhipaa_voice=="Yes"){
		//echo '3';
		$query_no="SELECT count(patient_data.id) FROM schedule_appointments USE INDEX(sa_multiplecol)
		INNER JOIN patient_data ON patient_data.id = schedule_appointments.sa_patient_id
		WHERE patient_data.hipaa_voice = '1' 
		AND schedule_appointments.sa_doctor_id in($doctor_ID)
		AND schedule_appointments.sa_app_start_date >= '$from_date' and schedule_appointments.sa_app_start_date <='$to_date'
		$chk_patient_avail
		$hipaaEmail";
	}else if($doctor_ID!="" && $facl!="" && $chkhipaa_voice=="Yes"){
		//echo '4';
		$query_no="SELECT count(patient_data.id) FROM schedule_appointments USE INDEX(sa_multiplecol)
		INNER JOIN patient_data ON patient_data.id = schedule_appointments.sa_patient_id
		WHERE patient_data.hipaa_voice = '1' 
		AND schedule_appointments.sa_doctor_id in($doctor_ID)
		AND schedule_appointments.sa_facility_id in($facl)
		AND schedule_appointments.sa_app_start_date >= '$from_date' and schedule_appointments.sa_app_start_date <='$to_date'
		$chk_patient_avail
		$hipaaEmail";
	}else if($doctor_ID!="" && $facl=="" && $chkhipaa_voice==""){
		//echo '4';
		$query_no="SELECT count(patient_data.id) FROM schedule_appointments USE INDEX(sa_multiplecol)
		INNER JOIN patient_data ON patient_data.id = schedule_appointments.sa_patient_id
		WHERE  schedule_appointments.sa_doctor_id in($doctor_ID)
		AND schedule_appointments.sa_app_start_date >= '$from_date' and schedule_appointments.sa_app_start_date <='$to_date'
		$chk_patient_avail
		$hipaaEmail";
	}else{
		$query_no = "select count(patient_data.id) from schedule_appointments USE INDEX(sa_multiplecol)
		INNER JOIN patient_data ON patient_data.id = schedule_appointments.sa_patient_id
		
		where sa_app_end_date>='$from_date' and sa_app_end_date <='$to_date' $chk_patient_avail
		$hipaaEmail";
	
	}
	$result_no =@imw_query($query_no) or die($query_no.imw_error());
	$res=@imw_fetch_row($result_no);
	$tot_rec=$res[0];
	$HTMLCreated=0;
	if($tot_rec>0){
	$r=$recno+1;
	if($send_email!=1){
		echo"<p class='text_10b' align='center' style='font-size:12px;'>Please do not hit the <font color=red><b>BACK, STOP or REFRESH or Any Tab</b></font> Button until the export completed message appears.</p>";
		$title="<p class='text_10b' align='center' style='font-size:12px;'>Exported <font color=red>$tot_rec</font> Appointments";
		echo "<title>Exported $tot_rec properties</title>";
		echo $title;
	}
	if($chkhipaa_voice=="" && $facl!="" && $doctor_ID==""){
		$query="SELECT patient_data.*, schedule_appointments.*, schedule_appointments.id as appt_id FROM schedule_appointments USE INDEX(sa_multiplecol)
		INNER JOIN patient_data ON patient_data.id = schedule_appointments.sa_patient_id
		WHERE 
		schedule_appointments.sa_facility_id in($facl)
		AND schedule_appointments.sa_app_start_date >= '$from_date' and schedule_appointments.sa_app_start_date <='$to_date'
		$chk_patient_avail
		$hipaaEmail";
	} else if($chkhipaa_voice=="Yes" && $facl!="" && $doctor_ID==""){
		
		$query="SELECT patient_data.*, schedule_appointments.*, schedule_appointments.id as appt_id FROM schedule_appointments USE INDEX(sa_multiplecol)
		INNER JOIN patient_data ON patient_data.id = schedule_appointments.sa_patient_id
		WHERE patient_data.hipaa_voice = '1'
		AND schedule_appointments.sa_facility_id in($facl)
		AND schedule_appointments.sa_app_start_date >= '$from_date' and schedule_appointments.sa_app_start_date <='$to_date'
		$chk_patient_avail
		$hipaaEmail";
	} else 	if($doctor_ID!="" && $facl!="" && $chkhipaa_voice==""){
		
		$query = "SELECT patient_data.*, schedule_appointments.*, schedule_appointments.id as appt_id FROM schedule_appointments USE INDEX(sa_multiplecol)
		INNER JOIN patient_data ON patient_data.id = schedule_appointments.sa_patient_id
		WHERE  schedule_appointments.sa_doctor_id in($doctor_ID)
		AND schedule_appointments.sa_facility_id in($facl)
		AND schedule_appointments.sa_app_start_date >= '$from_date' and schedule_appointments.sa_app_start_date <='$to_date'
		$chk_patient_avail
		$hipaaEmail";
	}else 	if($doctor_ID!="" && $facl=="" && $chkhipaa_voice=="Yes"){
		//echo '3';
		$query="SELECT patient_data.*, schedule_appointments.*, schedule_appointments.id as appt_id FROM schedule_appointments USE INDEX(sa_multiplecol)
		INNER JOIN patient_data ON patient_data.id = schedule_appointments.sa_patient_id
		WHERE patient_data.hipaa_voice = '1' 
		AND schedule_appointments.sa_doctor_id in($doctor_ID)
		AND schedule_appointments.sa_app_start_date >= '$from_date'
		and schedule_appointments.sa_app_start_date <='$to_date'
		$chk_patient_avail
		$hipaaEmail";
	}else if($doctor_ID!="" && $facl!="" && $chkhipaa_voice=="Yes"){
		//echo '4';
		$query="SELECT patient_data.*, schedule_appointments.*, schedule_appointments.id as appt_id FROM schedule_appointments USE INDEX(sa_multiplecol)
		INNER JOIN patient_data ON patient_data.id = schedule_appointments.sa_patient_id
		WHERE patient_data.hipaa_voice = '1'
		AND schedule_appointments.sa_doctor_id in($doctor_ID)
		AND schedule_appointments.sa_facility_id in($facl)
		AND schedule_appointments.sa_app_start_date >= '$from_date' and schedule_appointments.sa_app_start_date <='$to_date'
		$chk_patient_avail
		$hipaaEmail";
	}else if($doctor_ID!="" && $facl=="" && $chkhipaa_voice==""){
		//echo '4';
		$query="SELECT patient_data.*, schedule_appointments.*, schedule_appointments.id as appt_id FROM schedule_appointments USE INDEX(sa_multiplecol)
		INNER JOIN patient_data ON patient_data.id = schedule_appointments.sa_patient_id
		WHERE  schedule_appointments.sa_doctor_id in($doctor_ID)
		AND schedule_appointments.sa_app_start_date >= '$from_date' and schedule_appointments.sa_app_start_date <='$to_date'
		$chk_patient_avail
		$hipaaEmail";
	}else{
		$query = "select patient_data.*, schedule_appointments.*, schedule_appointments.id as appt_id from schedule_appointments USE INDEX(sa_multiplecol)
		INNER JOIN patient_data ON patient_data.id = schedule_appointments.sa_patient_id
		where sa_app_start_date>='$from_date' and sa_app_start_date <='$to_date' $chk_patient_avail
		$hipaaEmail";
	
	}
		$result = imw_query($query) or die(imw_error());
		//echo($query);
		imw_data_seek($result,$recno);
		
		
		if($send_email==1)
		{
			
			$recallTemplatesListId=$_REQUEST['recallTemplatesListId'];
			
			$page_data='<form name="send_email" id="send_email" action="export_house_send_email.php" method="post" target="iframeHidden">
			<input type="hidden" name="recallTemplatesListId1" value="'.$recallTemplatesListId.'">
			<input type="hidden" name="report_name" value="Day Appts">
			<input type="hidden" name="sendUnique" value="'.$_REQUEST['sendUnique'].'">
			<input type="hidden" name="from_date" value="'.$from_date.'">
			<table class="rpt_table rpt_table-bordered" bgcolor="#FFF3E8">
			<tr>
				<td style="width:70px" class="text_b_w alignCenter"><input type="checkbox" name="check_all" id="check_all" onClick="">S.No.</td>	
				<td style="width:250px" class="text_b_w alignCenter">Patient Name - ID</td>
				<td style="width:250px" class="text_b_w alignCenter">Email</td>
				<td style="width:100px" class="text_b_w alignCenter">Phone#</td>
				<td style="width:100px" class="text_b_w alignCenter">App. Date</td>
				<td style="width:100px" class="text_b_w alignCenter">App. Time</td>
				<td style="width:auto" class="text_b_w alignCenter">Appt. Comments</td>					
			</tr>';
		}
		
		while($row = @imw_fetch_array($result)){
			$nameFormated="";
			$nameFormated=$row["lname"]."".$row["suffix"].",".$row["fname"]." ".substr($row["mname"],0,1);
	
			$app_time = getMainAmPmTime($row["sa_app_starttime"]);
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
			
			if($send_email==1)
			{
				$i++;
				$dat_app1=strtotime($row['sa_app_start_date']);
				$dat_app=date("".phpDateFormat()."",$dat_app1);
				//By Karan
				$SA_Comments = str_replace("$","".show_currency()."",$row['sa_comments']);
				$chkBox='<input type="checkbox" id="pat_email_'.$i.'" name="pat_email[]" value="'.$row['appt_id'].'" class="checkbox1">';
				$page_data.='
					<tr>
						<td class="text alignCenter white">'.$chkBox.'&nbsp;'.$i.'</td>
						<td class="text alignLeft white">&nbsp;'.$nameFormated.'-'.$row['sa_patient_id'].'</td>
						<td class="text alignLeft white pl10">'.$row['email'].'</td>
						<td class="text alignLeft white">&nbsp;'.$phone_default.'</td>
						<td class="text alignLeft white">&nbsp;'.$dat_app.'</td>
						<td class="text alignLeft white">&nbsp;'.$app_time.'</td>
						<td class="text alignLeft white">&nbsp;'.$SA_Comments.'</td>
					</tr>';
			}
			elseif($pam==1){
					
					$dat_app1=strtotime($row['sa_app_start_date']);
					$dat_app = date("".phpDateFormat()."",$dat_app1);
					
					$fac_id=$row["sa_facility_id"];

					$phone_num=str_replace(' ','',str_replace('(','',str_replace(')','',str_replace('-','',$phone_default))));
					$phone_home=str_replace(' ','',str_replace('(','',str_replace(')','',str_replace('-','',$row["phone_home"]))));
					$phone_cell=str_replace(' ','',str_replace('(','',str_replace(')','',str_replace('-','',$row["phone_cell"]))));
					$phone_biz=str_replace(' ','',str_replace('(','',str_replace(')','',str_replace('-','',$row["phone_biz"]))));
					
					$phone=($phone_home!='') ?  $phone_home :$phone_num;
					$cell_phone= ($phone_cell!='') ?  $phone_cell :$phone_biz;
					if($phone==$cell_phone){ $cell_phone='';}
										
					$fac_qry=imw_query("select UPPER(city) as city  from facility where id ='$fac_id'");
					$fac_run=imw_fetch_array($fac_qry);
					$office_code="01";
					
					$fac_city_key=array_search($fac_run['city'],$GLOBALS['PAM2000']);
					if($fac_city_key) $office_code=$fac_city_key;
					
					$my_arr[0] ='"'.$row['sa_patient_id'].'"';
					$my_arr[1] ='"01"';
					$my_arr[2] ='"'.$office_code.'"';
					$my_arr[3] ='"01"';
					$my_arr[4] ='"'.strtoupper($row["fname"]).'"';
					$my_arr[5] ='"'.strtoupper($row["lname"]).'"';
					
					$my_arr[6] ='"'.$dat_app.'"';
					$my_arr[7] ='"'.$app_time.'"';
					$my_arr[8] ='"'.$row["sa_doctor_id"].'"';
					
					$my_arr[9] ='"'.$row["procedureid"].'"';
					$my_arr[10] ='"'.$phone.'"';
					$my_arr[11] ='"'.$row['email'].'"';
					$my_arr[12] ='"'.$cell_phone.'"';
		
				}else if($phoneTree==1){
					$dat_app1=strtotime($row['sa_app_start_date']);
					$dat_aPP= date("".phpDateFormat()."",$dat_app1);
					
					$my_arr[0] =addDoubleQuaotes(($nameFormated));
					$my_arr[1] =addDoubleQuaotes($dat_aPP);
					$my_arr[2] =addDoubleQuaotes($app_time);
					$my_arr[3] =addDoubleQuaotes($phone_default);
					$my_arr[4] =addDoubleQuaotes($row["phone_biz"]);
					$my_arr[5] =addDoubleQuaotes($row["email"]);
					$my_arr[6] =addDoubleQuaotes(str_replace("&nbsp;"," ",showDoctorName($row["sa_doctor_id"])));
					$my_arr[7] =addDoubleQuaotes(getLocationName($row["sa_facility_id"]));
					$my_arr[8] =addDoubleQuaotes(getProcedureName_lc($row["procedureid"]));
					
					
				}else{
					$dat_app1=strtotime($row['sa_app_start_date']);
					$dat_apP = date("".phpDateFormat()."",$dat_app1);
					
					$my_arr[0] =addDoubleQuaotes(($nameFormated));
					//$my_arr[0] =addDoubleQuaotes(spchar($row["sa_patient_name"]));
					$my_arr[1] =addDoubleQuaotes($phone_default);
					$my_arr[2] =addDoubleQuaotes($row["phone_cell"]);
					$my_arr[3] =addDoubleQuaotes($dat_apP);
					$my_arr[4] =addDoubleQuaotes($app_time);
					$my_arr[5] =$row["sa_patient_id"];
					$my_arr[6] =$row["sa_doctor_id"];
					$my_arr[7] =$row["procedureid"];
					
					$my_arr[8] =addDoubleQuaotes(str_replace("&nbsp;"," ",showDoctorName($row["sa_doctor_id"])));
					$my_arr[9] =addDoubleQuaotes(getProcedureName_lc($row["procedureid"]));
					$my_arr[10] =addDoubleQuaotes(getLocationName($row["sa_facility_id"]));
					
					$my_arr[11] =addDoubleQuaotes(spchar($row["street"]));//Address
					$my_arr[12] =addDoubleQuaotes(spchar($row["city"]));
					$my_arr[13] =addDoubleQuaotes(spchar($row["state"]));
					$my_arr[14] =addDoubleQuaotes($row["postal_code"]);
					$my_arr[15] =addDoubleQuaotes($row["email"]);
					//$my_arr[14] =addDoubleQuaotes($row["sa_patient_app_status_id"]);
				}
				$exceltext="";	
				$exceltext="\n";
				
				for($k=0;$k<count($my_arr);$k++)
				{
					$exceltext.=$my_arr[$k].$pfx;
				}
				$exceltext=@substr($exceltext,0,strlen($exceltext)-1);
				$fp=@fopen($filename,"a+");
				@fwrite($fp,$exceltext);
				@fclose($fp);
				if($recno<$tot_rec-1){
				$recno++;
				}
		}
		$page_data.='</table></form>';
		if($send_email==1)
		{
			//show data here
			echo $page_data;
			echo'<iframe name="iframeHidden" id="iframeHidden" height="0" width="0" src=""></iframe>';	
		}else{
		$exceltext="";
			echo "<p class='text_10b' align='center' style='font-size:15px;'><font color=red>Export Completed.</font>";
			echo("&nbsp;If file does not open automatically then :<br/><br/><input type='button' name='saveFile' class='btn btn-success' onClick=\"javascript:location.href='save_house_csv.php?fn=".$filename."'\" value='Click To Save File' class=\"dff_button\"");
		}
		$HTMLCreated = 1;
	}else{
	echo '<div class="text-center alert alert-info">No record found for selected date range.</div>';
	if($send_email!=1){}else{?>
		<script type="text/javascript">
			var ar = [["export","Get Report","top.fmain.sbmtForm();"]];
			top.btn_show("O4A",ar);		
		</script>
	<?php }//End of big if check
	}
?>