<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
session_start();
set_time_limit(900);

include("common/conDb.php");
if($_SESSION["loginUserId"]=="" && $_SESSION['loginUserName']=="") {
	echo '<script>top.location.href="index.php"</script>';
}
include_once("admin/classObjectFunction.php");

$csv_content = "";
//$csv_content1 = "";

$objManageData = new manageData;
$fac_con="";
if($_SESSION['iasc_facility_id'])
{
	$fac_con=" and stub_tbl.iasc_facility_id='".$_SESSION["iasc_facility_id"]."' "; 
}

//MAKING FILE NAME
$clientId= PRESS_GANEY_CLIENT_ID;
$survey_designator= PRESS_GANEY_SURVEY_DESIGNATOR;

$f_path="admin/pdfFiles/";
$f_name= $clientId.date('mdY');
if(file_exists($f_path.$f_name.'.csv')){
	for($i=1; $i<=50; $i++){
		if(!file_exists($f_path.$f_name.'_'.$i.'.csv')){
			$f_name= $f_name.'_'.$i;
			break;
		}
	}
}
$filename= $f_path.$f_name.'.csv';
$pfx=",";


function addDoubleQuaotes($stringVal){
	if($stringVal!=""){
	 $stringVal='"'.$stringVal.'"';
	 }
	 return $stringVal;
}

$startdate=$_REQUEST['startdate'];
$enddate=$_REQUEST['enddate'];
$get_http_path=$_REQUEST['get_http_path'];

//extracting strating date
if($startdate!=0){
	$start_date=explode("-",$startdate);
	$start_date[0];
	$start_date[1];
	$start_date[2];
	$from_date=$start_date[2]."-".$start_date[0]."-".$start_date[1];
}
//extracting end date
$enddate=$_REQUEST['enddate'];
if($enddate!=0){
	$end_date=explode("-",$enddate);
	$end_date[0];
	$end_date[1];
	$end_date[2];
	$to_date=$end_date[2]."-".$end_date[0]."-".$end_date[1];
}


//LANGUAGES CODES
$arr_language_codes=array(
"Albanian"=>"57", "Arabic"=>"22", "Armenian"=>"31", "Bengali"=>"60", "Bosnian"=>"50", "Bosnian-Croatian"=>"49",
"Bosnian-Muslim"=>"48", "Bosnian-Serbian"=>"32", "Cambodian"=>"34", "Chao-Chou"=>"41", "Chinese-Simplified"=>"12",
"Chinese-Traditional"=>"10", "Chuukese"=>"23", "Creole"=>"21", "Croatian"=>"52", "English/Spanish"=>"33", "English"=>"0", "Farsi"=>"59",
"French-Canadian"=>"35", "French-France"=>"20", "German"=>"4", "Greek"=>"7", "Haitian-Creole"=>"36", "Hebrew"=>"37", "Hindi"=>"38",
"Hmong"=>"26", "Ilocano"=>"56", "Indonesian"=>"42", "Italian"=>"5", "Japanese"=>"28", "Korean"=>"29", "Laotian"=>"43", "Malayalam"=>"58",
"Malayan"=>"44", "Marshallese"=>"24", "Polish"=>"6", "Portuguese-Brazilian"=>"8", "Portuguese-Continental"=>"47", "Punjabi"=>"54",
"Romanian"=>"55", "Russian"=>"3", "Samoan"=>"25", "Serbian"=>"51", "Somali"=>"27", "Spanish"=>"1", "Swahili"=>"45", "Tagalog"=>"30", "Thai"=>"46",
"Turkish"=>"53", "Urdu"=>"39", "Vietnamese"=>"13", "Yiddish"=>"40");

//GETTING ALL PROCEDURES
$arrProcedures=array();
$qry="	SELECT p.procedureId, p.name, p.code 
		FROM procedures p 
		INNER JOIN procedurescategory pc ON (p.catId = pc.proceduresCategoryId AND pc.name != 'Miscellaneous')
		WHERE p.name !=''
		ORDER BY p.procedureId ";
$rs=imw_query($qry);
while($res=imw_fetch_assoc($rs)){
	$code = ($res['code']!='') ? $res['code'] : $res['name'];
	$arrProcedures[$res['procedureId']] = $code;
}


//GETTING PATIENT DECEASED STATUS FROM IDOC
$arriDocPatDet=array();
imw_close($link); //CLOSE SURGERYCENTER CONNECTION
include_once('connect_imwemr.php'); // imwemr connection
$qry="Select id, patientStatus, email from patient_data";
$rs=imw_query($qry);
while($res=imw_fetch_assoc($rs)){
	$arriDocPatDet[$res['id']]['deceased']= strtolower($res['patientStatus']);
	
	if(strtolower($res['email'])=='na' || strtolower($res['email'])=='n/a')$res['email']='';
	$arriDocPatDet[$res['id']]['email']= $res['email'];
}
imw_close($link_imwemr); //CLOSE IMWEMR CONNECTION
include("common/conDb.php");  //SURGERYCENTER CONNECTION	


//MAIN QUERY
$qry="Select conf.patientId, conf.discharge_status, conf.no_publicity, stub_tbl.dos, conf.patient_primary_procedure_id, conf.patient_secondary_procedure_id, conf.patient_tertiary_procedure_id,
conf.ascId, pd.patient_fname, pd.patient_mname, pd.patient_lname, pd.street1, pd.street2, pd.city, pd.state, pd.zip, pd.sex, pd.homePhone, pd.workPhone, 
DATE_FORMAT(pd.date_of_birth, '%m-%d-%Y') as 'dob', pd.language, pd.imwPatientId     
FROM stub_tbl 
JOIN patientconfirmation conf ON conf.patientConfirmationId = stub_tbl.patient_confirmation_id 
JOIN patient_data_tbl pd ON pd.patient_id = conf.patientId 
WHERE (stub_tbl.dos BETWEEN '".$from_date."' AND '".$to_date."') ".$fac_con." AND conf.ascId>0 
ORDER BY stub_tbl.dos, stub_tbl.stub_id";

$rs=imw_query($qry);
while($res=imw_fetch_assoc($rs)){
	$pid= $res['patientId'];
	$languange_code=$discharge_status='';
	
	//MIDDLE NAME INITIAL
	$pmname_initial='';
	if($res['patient_mname']!=''){
		$pmname_initial= strtoupper(substr($res['patient_mname'], 0, 1));
	}
	
	//PHONE
	$phone = ($res['homePhone']!='') ? $res['homePhone'] : $res['workPhone'];

	//GENDER
	if(strtolower($res["sex"])=='m')$gender='1';
	else if(strtolower($res["sex"])=='f')$gender='2';
	else $gender='M';

	//LANGUAGE CODE
	if($res['language']=='French')$res['language']='French-France';
	else if($res['language']=='Portuguese')$res['language']='Portuguese-Continental';
	if(empty($res['language'])==false){
		$languange_code = $arr_language_codes[$res['language']];
	}

	//CPT CODES
	$cptCode1=$cptCode2=$cptCode3='';
	$arrCPTCodes=array();
	if($res['patient_primary_procedure_id']>0)$cptCode1= $arrProcedures[$res['patient_primary_procedure_id']];
	if($res['patient_secondary_procedure_id']>0)$cptCode2= $arrProcedures[$res['patient_secondary_procedure_id']];
	if($res['patient_tertiary_procedure_id']>0)$cptCode3= $arrProcedures[$res['patient_tertiary_procedure_id']];

	//CHECK DECEASED
	$deceased='N';
	if($arriDocPatDet[$res['imwPatientId']]['deceased']=='deceased')$deceased='Y';
	
	//DISCHARGE STATUS
	if($res['discharge_status']>0){
		$discharge_status=$res['discharge_status'];
		if($res['discharge_status']<10)$discharge_status='0'.$res['discharge_status'];
	}

	//NO PUBLICITY FLAG
	$no_publicity = ($res['no_publicity']=='1') ? 'Y' : 'N';		

	//BILLING ID
	if($res["ascId"]<=0)$res["ascId"]='';
	

	$contentPart.=
	$survey_designator.$pfx.
	$clientId.$pfx.
	addDoubleQuaotes($res['patient_lname']).$pfx.
	addDoubleQuaotes($pmname_initial).$pfx.
	addDoubleQuaotes($res['patient_fname']).$pfx.
	addDoubleQuaotes($res['street1']).$pfx.
	addDoubleQuaotes($res['street2']).$pfx.
	addDoubleQuaotes($res['city']).$pfx.
	addDoubleQuaotes($res['state']).$pfx.
	addDoubleQuaotes($res['zip']).$pfx.
	addDoubleQuaotes($phone).$pfx.
	addDoubleQuaotes($gender).$pfx.
	addDoubleQuaotes($res["dob"]).$pfx.
	addDoubleQuaotes($languange_code).$pfx.
	addDoubleQuaotes($pid).$pfx.
	addDoubleQuaotes($res["ascId"]).$pfx.
	addDoubleQuaotes($res["dos"]).$pfx.
	addDoubleQuaotes($discharge_status).$pfx.
	addDoubleQuaotes($arriDocPatDet[$res['imwPatientId']]['email']).$pfx.
	addDoubleQuaotes($cptCode1).$pfx.
	addDoubleQuaotes($cptCode2).$pfx.
	addDoubleQuaotes($cptCode3).$pfx.
	addDoubleQuaotes("").$pfx.
	addDoubleQuaotes("").$pfx.
	addDoubleQuaotes("").$pfx.
	addDoubleQuaotes($deceased).$pfx.
	addDoubleQuaotes($no_publicity).$pfx.
	"$";
	
	$contentPart.="\n";	

}


if(empty($contentPart)==false){
	$fileContent="";
	$fileContent.="Survey Designator".$pfx;
	$fileContent.="Client ID".$pfx;
	$fileContent.="Last Name".$pfx;
	$fileContent.="Middle Initial".$pfx;
	$fileContent.="First Name".$pfx;
	$fileContent.="Address 1".$pfx;
	$fileContent.="Address 2".$pfx;
	$fileContent.="City".$pfx;
	$fileContent.="State".$pfx;
	$fileContent.="Zip Code".$pfx;
	$fileContent.="Telephone Number".$pfx;
	$fileContent.="Gender".$pfx;
	$fileContent.="Date of Birth".$pfx;
	$fileContent.="Language".$pfx;
	$fileContent.="Medical Record Number".$pfx;
	$fileContent.="Unique Billing Code ID".$pfx;
	$fileContent.="Visit or Admit Date".$pfx;
	$fileContent.="Alternative Status".$pfx;
	$fileContent.="Email".$pfx;
	$fileContent.="Procedure Code 1".$pfx;
	$fileContent.="Procedure Code 2".$pfx;
	$fileContent.="Procedure Code 3".$pfx;
	$fileContent.="Procedure Code 4".$pfx;
	$fileContent.="Procedure Code 5".$pfx;
	$fileContent.="Procedure Code 6".$pfx;
	$fileContent.="Deceased Flag".$pfx;
	$fileContent.="No Publicity Flag".$pfx;
	$fileContent.="E.O.R Indicator";
	$fileContent.="\n";
	
	$fileContent.= $contentPart;

	$fp=@fopen($filename,"w");
	@fwrite($fp,$fileContent);
	@fclose($fp);

	$objManageData->download_file($filename, $f_name.'.csv');
}

?>	
<!DOCTYPE html>
<html>
<head>
<title>Presss Ganey Report</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="nofollow">
<meta name="googlebot" content="noindex">
<link rel="stylesheet" href="css/style_surgery.css" type="text/css" >   
<script language="javascript">
	window.focus();
	function submitfn()
	{
		document.printFrm.submit();
	}
</script>
</head>
<body>
 <form name="printFrm" action="new_html2pdf/createPdf.php?op=l" method="post">

</form>
<?php
if(@imw_num_rows($physician)>0){?>		
	<script type="text/javascript">
        submitfn();
    </script>
<?php 
}else {
?>
	<script>
		if(document.getElementById("loader_tbl")) {
			document.getElementById("loader_tbl").style.display = "none";	
		}
	</script>	

<?php	
	if($_REQUEST['phy_save']=='yes') {
	?>
	<script type="text/javascript">
        location.href = "press_ganey_report.php?no_record=yes";
    </script>
	<?php
	}
	?>
	<table style=" font-family:Verdana, Geneva, sans-serif; font-size:12px; background-color:#EAF0F7; width:100%; height:100%;">
		<tr>
			<td class="alignCenter valignTop" style="width:100%;"><b>No Record Found</b></td> 
		</tr>
	</table>
<?php		
}?>
</body>
</html>
