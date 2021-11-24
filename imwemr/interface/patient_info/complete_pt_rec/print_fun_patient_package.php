<?php
$ignoreAuth=true;
require_once '../../../config/globals.php';
include_once($GLOBALS['fileroot'].'/interface/patient_info/complete_pt_rec/print_functions_class.php');
$cpr = New CmnFunc($pid);

global $ChartNoteImagesString;//$_REQUEST['patient'] = '57053';$_REQUEST['form_id'] ='63500';
$_SESSION['patient']=$_REQUEST['patient'];
$_SESSION['authId'] = $_REQUEST['authId'];
$pid = $_REQUEST['patient'];
$form_id=$_REQUEST['form_id'];//"17989";//

$dos=$_REQUEST['dos'];
$tdate=date("m-d-Y");
//print_r($_GET);

//---get Detail For Patient -------
$qry ="select * from patient_data where id = '$pid'";
$patientDetails = get_array_records_query($qry);
$patientName = $patientDetails[0]['lname'].', '.$patientDetails[0]['fname'].' ';
$patientName .= $patientDetails[0]['mname'];
//Encryption Variables//
$fileNamewith=$patientDetails[0]["lname"]."_".$patientDetails[0]["fname"];
$AESPatientLName=$patientDetails[0]["lname"];
$AESPatientDOB=$patientDetails[0]['DOB'];
$encPassword=core_pt_secret_phrase($_SESSION["patient"], $AESPatientLName, $AESPatientDOB);
//Encryption Variables//

$date = substr($patientDetails[0]['date'],0,strpos($patientDetails[0]['date'],' '));
$created_date = get_date_format($date);
$date_of_birth = get_date_format($patientDetails[0]['DOB']);
$cityAddress = $patientDetails[0]['city'];
if($patientDetails[0]['state'])
	$cityAddress .= ', '.$patientDetails[0]['state'].' ';
else
 	$cityAddress .= ' ';
$cityAddress .= $patientDetails[0]['postal_code'];
list($y,$m,$d) = explode('-',$patientDetails[0]['DOB']);
$age =getAge($patientDetails[0]['DOB']) ;//date('Y') - $y ;
//--- Get Physician Details --------
if((int)$patientDetails[0]['providerID'] > 0){
	$phyId = $patientDetails[0]['providerID'];
}
else{
	$appointmentQryRes = get_patient_last_appointment($pid);
	$phyId = $appointmentQryRes[0]['sa_doctor_id'];
}
if($phyId){
	$qry = "select concat(fname,', ',lname) as name, mname from users
			where id = '$phyId'";
	$phyDetails = get_array_records_query($qry);
	$phyName = trim($phyDetails[0]['name'].' '.$phyDetails[0]['mname']);
}

//--- Get Reffering Physician Details --------
$primary_care_phy_name=$patientDetails[0]['primary_care_phy_name'];
$reffPhyId = $patientDetails[0]['primary_care_id'];
$qry = "select concat(FirstName,', ',LastName) as name, MiddleName from refferphysician
		where physician_Reffer_id = '$reffPhyId'";
$refPhyDetails = get_array_records_query($qry);
$reffPhyName = trim($refPhyDetails[0]['name'].' '.$refPhyDetails[0]['MiddleName']);
//if(!$reffPhyName) $reffPhyName = $phyName;
//---- Get Patient Facility Details -------
$default_facility = $patientDetails[0]['default_facility'];
$qry = "select facilityPracCode from pos_facilityies_tbl 
		where pos_facility_id = '$default_facility'";
$facilityRes = get_array_records_query($qry);

//--- Get Detail How create patient -------
$created_by = $patientDetails[0]['created_by'];
$qry = "select fname, lname, mname from users
		where id = '$created_by'";
$createByDetail = get_array_records_query($qry);
$createByName = core_name_format($createByDetail[0]['fname'],$createByDetail[0]['mname'],$createByDetail[0]['lname'],'');
//$createByName = substr(trim($createByDetail[0]['fname']), 0, 1).substr(trim($createByDetail[0]['lname'], 0 ,1));

//--- Get Patient Responsible Party Details -----
$qry = "select * from resp_party where patient_id = '$pid'
		and fname != '' and lname != ''";
$res_party_detail = get_array_records_query($qry);
//--- Get Patient Occupation Details ------
$qry = "select * from employer_data where pid = '$pid' and name != ''";
$emp_details = get_array_records_query($qry);


//--- Get Default Facility Details -------
$qry = "select default_group from facility where facility_type = 1";
$facilityDetail = get_array_records_query($qry);
if(count($facilityDetail)>0){
	$gro_id = $facilityDetail[0]['default_group'];
	$qry = "select * from groups_new where gro_id = '$gro_id'";
	$groupDetails = get_array_records_query($qry);
}

	// IF PRINT THEN FORM ID
$_REQUEST['formIdToPrint']=$form_id;
$_REQUEST["chart_nopro"]=array();
$_REQUEST["chart_nopro"][0] = 'All';
$_REQUEST["chart_nopro"][1] = 'Chart Notes';
$_REQUEST["chart_nopro"][2] = 'Medical History';
$_REQUEST["chart_nopro"][3] = 'Patient Communication';
$_REQUEST["chart_nopro"][4] = 'Diagnostic Tests';
$_REQUEST["chart_nopro"][5] = 'Patient Amendment';
$_REQUEST["chart_nopro"][6] = 'Problem List';
$_REQUEST["chart_nopro"][7] = 'Medication List';
$_REQUEST["chart_nopro"][8] = 'Allergies List';
	
	$print_form_id = $form_id;
	if($print_form_id){
		$form_id = $print_form_id;
	}
	// IF PRINT THEN FORM ID

// IF PRINT DOS SELECT FORM ID THEN FORM ID
if(is_array($_REQUEST["chart_nopro"])){
	if(count($_REQUEST["chart_nopro"])>0){
		$formIdToPrint = $_REQUEST['formIdToPrint'][0];
		if($formIdToPrint!="" && in_array("Chart Notes",$_REQUEST["chart_nopro"])){//&& in_array("This Visit",$_REQUEST["chart_nopro"])
			$form_id = $formIdToPrint;
		}
	}
}
	
// IF PRINT THEN FORM ID
	if(intval($form_id)==0 && intval($formIdToPrint)>0){
		//$qry1=imw_query("select date_of_service from  chart_left_cc_history where patient_id='$pid' and form_id='$formIdToPrint'");
		$qry1=imw_query("select date_of_service from  chart_master_table where patient_id='$pid' and id='$formIdToPrint'");
		$co=imw_num_rows($qry1);
		$databaseDateOfService = '';
		if(($co > 0)){
			$crow=imw_fetch_assoc($qry1);
			$databaseDateOfService = $crow["date_of_service"];
		 }
	}else{
		//$qry1=imw_query("select date_of_service from  chart_left_cc_history where patient_id='$pid' and form_id='$form_id'");
		$qry1=imw_query("select date_of_service from  chart_master_table where patient_id='$pid' and id='$form_id'");
		$co=imw_num_rows($qry1);
		$databaseDateOfService = '';
		if(($co > 0)){
			$crow=imw_fetch_assoc($qry1);
			$date_of_service = date("m-d-Y", strtotime($crow["date_of_service"]));	
			$databaseDateOfService = $crow["date_of_service"];
		 }
	}
/////End date of sevice Code////////////////
//--- Face Sheet Check
$dontshowPDF=false;
if(count($patient_info)>0){
	$dontshowPDF=true;
	$face_check = true;
}
for($p=0;$p<count($patient_info);$p++){
	if($patient_info[$p] == 'all'){
		$face_check = false;
	}
	else if($patient_info[$p] == 'printMedicalHistory.php'){
		$face_check = false;
	}
	else if($patient_info[$p] == 'print_legal.php'){
		$face_check = false;
	}
}
if(count($patient_info) == 1 && $patient_info[0] != 'face_sheet'){
	$face_check = false;
}
	
if($face_check == true){
	unset($patient_info);
	$patient_info[0] = 'face_sheet';
	$border = 0;
}
else{
	$border = 0;
}
if($_REQUEST["chart_nopro"] != '' && ($_REQUEST["glaucoma"] != '' || count($special_all)>0 || $face_check)){
$dontshowPDF=true;

}
if($_REQUEST["glaucoma"] != '' || count($special_all)>0){
	$dontshowPDF=true;
}
//Code to Show Patient Image
$p_imagename = $patientDetails[0]['p_imagename'];
if($p_imagename){
	$dirPath = substr(data_path(0),0,-1).$p_imagename;
	$copy_path = substr(data_path(0),0,-1).'/tmp/';
	$img_name = substr($p_imagename,strrpos($p_imagename,'/')+1);	
	copy($dirPath,$copy_path.$img_name);
	$imageNameTmp = substr(data_path(1),0,-1).$p_imagename;
	if(file_exists($dirPath)){
		$patient_img['patient'] = $img_name;
		$fileSize = getimagesize($dirPath);
		if($fileSize[0]>80 || $fileSize[0]>90){
			$imageWidth2 = imageResize($fileSize[0],$fileSize[1],90);
			$patientImage = "<img style=\"cursor:pointer\" src=\"".$imageNameTmp."\" alt=\"patient Image\" ".$imageWidth2.">";
		}
		else{
			$patientImage = "<img style=\"cursor:pointer\" src=\"".$imageNameTmp."\" alt=\"patient Image\">";
		}		
	}
}
//End Code to Show Patient Image//
/////END  Opertator Details/////
$qryOPNM = "select id,lname,fname,mname from users where id ='".$_SESSION['authId']."'";
$phyQryRes = imw_query($qryOPNM);
$phyNameArr =imw_fetch_array($phyQryRes);
$phyNameCurrentUser=substr($phyNameArr['fname'],0,1);
$phyNameCurrentUser.=substr($phyNameArr['lname'],0,1);
$opertator_name = strtoupper($phyNameCurrentUser);
/////END  Opertator Details/////

//START CODE TO SET LOG OF PRINTED RECORDS
if(intval($form_id)==0 && intval($formIdToPrint)>0){
	//setLogOfPtPrintedRec($pid,$formIdToPrint,$_SESSION['authId'],$databaseDateOfService,'iDoc');
}else{
	//setLogOfPtPrintedRec($pid,$form_id,$_SESSION['authId'],$databaseDateOfService,'iDoc');
}
//END CODE TO SET LOG OF PRINTED RECORDS
?>
<HTML>
<HEAD>
<LINK rel="stylesheet" href="<?php echo $css_patient;?>" type="text/css">
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<script language="javascript" src="<?php echo $GLOBALS['webroot'];?>.'/library/js/common_function.js"></script>
<script> 
<?php if(!isset($_REQUEST['faxSubmit']) || intval($_REQUEST['faxSubmit'])==0){?>
top.window.moveTo(0,0);
if (document.all){
	top.window.resizeTo(screen.availWidth,screen.availHeight);
}
else if(document.layers || document.getElementById){ 
	if (top.window.outerHeight < screen.availHeight || top.window.outerWidth < screen.availWidth){
		top.window.outerHeight = top.screen.availHeight;
		top.window.outerWidth = top.screen.availWidth;
	}
}
<?php }?>
function setDownLoadWindowSize(){
	window.title="Please wait... Your Download will start shortly."
	top.window.moveTo(100,100); 
	if (document.all) 
	   { 
		top.window.resizeTo(450,250); 
		} 
		else if(document.layers){ 
				if(top.window.outerHeight < screen.availHeight || top.window.outerWidth < screen.availWidth)
				 { 
				   top.window.outerHeight =250; //top.screen.availHeight-10; 
				   top.window.outerWidth = 450;//.screen.availWidth-10;  
				}
		 } 
}
</script>
<?php if(($_REQUEST["chart_nopro"]==3 || $_REQUEST["chart_nopro"]==4 || $_REQUEST["chart_nopro"]==5 || $_REQUEST["chart_nopro"]==6) && ($dontshowPDF==false || $face_check==true)){?>
	<script language="JavaScript">
	<?php //echo $_REQUEST['faxSubmit'];die();
		if(!isset($_REQUEST['faxSubmit']) || intval($_REQUEST['faxSubmit'])==0){?>
		window.moveTo(0,0);
		window.resizeTo(screen.width,screen.height);
	<?php }?>
	</script>
<?php } ?>
<style>
	/**---Style Sheet Changes For Printing Only----*/
		.text_9		{ font-family:"verdana"; font-size:10px; color:#000000;}
		.text_9b	{ font-family:"verdana"; font-size:10px; color:#000000; font-weight:bold;}
		.text_9b1	{ font-family:"verdana"; font-size:9px; color:#000000; font-weight:bold;}
		.text_9b2	{ font-family:"verdana"; font-size:9px; color:#000000;}
		.text_10	{ font-family:"verdana"; font-size:11px; color:#000000;}
		.text_10b	{ font-family:"verdana"; font-size:12px; color:#000000; font-weight:bold;  }
		.text		{ font-family:"verdana"; font-size:11px; color:#000000;}	
	/**---Style Sheet Changes For Printing Only----**/
</style>

</HEAD>
<BODY class="body_c" topmargin="0" rightmargin="0" leftmargin="0" bottommargin="0" marginwidth="0" marginheight="0" >

<!------ Start Hedding Section 570---->
<?php
ob_start();
?>

<?php
$chart_notprintinginclude=false;
if(is_array($_REQUEST["chart_nopro"])){
	if(count($_REQUEST["chart_nopro"])>0){
	 $chart_notprintinginclude=true;
	}
}
$heightTb = 675;
if($face_check) $heightTb = '100%';
?>
<table width="<?php print $heightTb; ?>" border="0" cellspacing="0" rules="none" cellpadding="0">
	<?php
	if($face_check && $dontshowPDF==true){}
	else{
		

if($chart_notprintinginclude==false && $dontshowPDF==true){}
	}
	?>	
</table>	
<!------ Hedding Section---->
<?php
$headData = ob_get_contents();


ob_end_clean();
	//---- Get Patient Details ------------
	if($face_check == false){
		//print $headData;
	}
	if(count($patient_info)>0){}
	if(count($special_all)>0){}
//By Ram To Show PDF Demo

$chart_notprintinginclude=true;
if($chart_notprintinginclude==true){
	if(is_array($_REQUEST["chart_nopro"])){
		if(!in_array("Include Provider Notes",$_REQUEST["chart_nopro"])){ 
			$AuditEntryFor="chart_notes_without_provider_notes";
			$AuditEntryFor="chart_notes_details";
		}
	}
	$chart_notprintinginclude=true;
	if($chart_notprintinginclude==true){
		$reportName="Visit Notes";
		$lenFIds=count($_REQUEST["formIdToPrint"]);
		
		ob_start();
		echo "<page backtop=\"5mm\" backbottom=\"5mm\">";
		$print_form_id = $_REQUEST['form_id'];
		if($print_form_id!=""){
			$form_id = $print_form_id;
		}		
		$strDosToPrint1="'".$_REQUEST['dos']."'";
		$_REQUEST["medicationActive"]="Active";
		$_REQUEST["ocularAction"]="Active";
		$_REQUEST["sysAction"]="Active";
		$_REQUEST["allergies_testActive"]="Active";
		
		include("visionPrintWithNotes_1.php");			
		$zFormId=$print_form_id;	
		include("visionPrintWithNotes.php");		
				
		//Consult Letters
		$lenclIds=count($_REQUEST["consultLetterToPrint"]);
		if($lenclIds>0){		
			$strCLIds="'".implode("', '",$_REQUEST["consultLetterToPrint"])."'";
			print_ConsultLetters($pid, '', $strCLIds);			
		}
		
		//Op Notes
		$lenOpNIds=count($_REQUEST["opNoteToPrint"]);
		if($lenOpNIds>0){		
			$strOpNIds="'".implode("', '",$_REQUEST["opNoteToPrint"])."'";
			print_OpNotes($pid, '', $strOpNIds);		
		}	

		echo "</page>";
			$patient_workprint_data = ob_get_contents();
			//exit("DONE");
			ob_end_clean();

	}
	$headDataRR=$patient_workprint_data;
	$fp = fopen('../../../library/html_to_pdf/pdffile.html','w');
	$putData = fputs($fp,$headDataRR);
	fclose($fp);
	
	$ChartNoteImagesStringFinal=implode(",",$ChartNoteImagesString);
 	if(isset($_REQUEST['faxSubmit']) && intval($_REQUEST['faxSubmit'])==1){
		echo '<script type="text/javascript">window.location="sendfax_chart_summary.php?txtFaxRecipent='.trim($_REQUEST['selectReferringPhy']).'&txtFaxNo='.trim($_REQUEST['send_fax_number']).'";</script>';
		exit();
	}
	
//---------BEGIN MAKE AN ARRAY OF ALL SELECTED TEST IDS FOR PDF MERGING-----------------	
$arr = array();
$arr[] = $_REQUEST["printTestRadioVF"];
$arr[] = $_REQUEST["printTestRadioHRT"];
$arr[] = $_REQUEST["printTestRadioOCT"];
$arr[] = $_REQUEST["printTestRadioGDX"];
$arr[] = $_REQUEST["printTestRadioPachy"];
$arr[] = $_REQUEST["printTestRadioIVFA"];
$arr[] = $_REQUEST["printTestRadioICG"];
$arr[] = $_REQUEST["printTestRadioFundus"];
$arr[] = $_REQUEST["printTestRadioExternal_Anterior"];
$arr[] = $_REQUEST["printTestRadioTopography"];
$arr[] = $_REQUEST["printTestRadioCellCount"];
$arr[] = $_REQUEST["printTestRadioLaboratories"];
$arr[] = $_REQUEST["printTestRadioBscan"];
$arr[] = $_REQUEST["printTestRadioOther"];
$arrTestIds = array();$strAllTestIds = '';
if(count($arr)>0){
	foreach($arr as $attTmp){
		if(count($attTmp)>0)
		$arrTestIds[] = implode(',',$attTmp);
	}
	if(count($arrTestIds)>0){
		$strAllTestIds = implode(",",$arrTestIds);
	}
}

$pdfFile = "patient_".$_REQUEST['patient']."_".date("H_i_s");//die();
?>	
	<!--<form name="printFrm" action="../common/new_html2pdf/createPdf.php" method="post">
		<input type="hidden" name="page" value="1.3" >
		<input type="hidden" name="op" value="P" >
		<input type="hidden" name="font_size" value="7.5">
        <input type="hidden" name="saveOption" value="F">
        <input type="hidden" name="name" value="<?php print $pdfFile;?>">
        <input type="hidden" name="htmlFileName" value="<?php echo 'pdffile';?>">
        <input type="hidden" name="testIds" id="testIds" value="<?php echo $strAllTestIds;?>">
         <input type="hidden" name="mergePDF" id="testIds" value="<?php echo '../printing/merge_pdf.php';?>">
		<?php
		if($_REQUEST["hidexport_report"]=="Yes"){?>
		<input type="hidden" name="name" value="<?php print $fileNamewith;?>">
		<input type="hidden" name="saveOption" value="F">
		<input type="hidden" name="encPassword" value="<?php echo($encPassword);?>">
		<?php }
		?>
		<input type="hidden" name="images" value="<?php print $ChartNoteImagesStringFinal; ?>" >
   </form>
	<script type="text/javascript">
	<?php if($_REQUEST["hidexport_report"]=="Yes"){?>setDownLoadWindowSize(); <?php }?>
	//document.printFrm.submit();
	</script>-->
	<?php
		
}

//By Ram To Show PDF Demo
	if($_REQUEST["glaucoma"]=="1"){
		echo("<tr><Td width='100%'>");
		include_once("chart_glucoma_print_inc.php");
		echo("</Td></tr>");
	}
	
echo("</table>");
if($face_check == false){
?>
<table width="675">
	<tr>
		<td colspan="3"><hr size="1" class="text"></td>
	</tr>
</table>
<?php
}
?>
<table  width="30%" border="0" cellpadding="3" cellspacing="3" align="left" id="print_btntbl" style="display:block;">
	<tr>
		<td width="9%" align="left"><input type="button" value="Print Report" class="dff_button" id="print_rep" onMouseOver="button_over('print_rep')" onMouseOut="button_over('print_rep','')" onClick="javascript:print_report();"></td>
		<td width="3%" align="center" class="text_9b">Or</td>
	</tr>
</table>
<table  width="30%" border="0" cellpadding="3" cellspacing="3" align="left" id="print_btntblback" style="display:block;">
	<tr>
		
		<td width="88%" align="left"><input type="button" value="Go Back" class="dff_button" id="go_back" onMouseOver="button_over('go_back')" onMouseOut="button_over('go_back','')" onClick="javascrip:window.location.href='../common/print_function.php'"></td>		
	</tr>	
</table>
<script language="javascript" src="../common/script_function.js"></script>
<script type="text/javascript">
	var auditCat = '<?php if(count($patient_info)>0){echo implode(",", $patient_info);} ?>';	
	auditCat += '<?php if($_REQUEST["glaucoma"]){echo ",glaucoma";} ?>';	
	window.onafterprint = function(){
	var url = 'print_audit.php?print_op='+auditCat;
	$.ajax({
		url : url,
		type:POST,
			success:function(res){									
				if(res == "DONE") return true;
			}
		});
	}
	function print_report(){
		var obj=document.getElementById("print_btntbl");
		var obj2=document.getElementById("print_btntblback");
		obj.style.display="None";
		obj2.style.display="None";
		window.print();
	}
</script>
<?php
	if($_GET["finalPrint"] == "1")
	{
		echo "<script>";
			echo "print_report();";
		echo "</script>";
	}
if($_GET["directPrint"] == "Print")
	{
		echo "<script>";
			echo "print_report();";
		echo "</script>";	
	}
?>
</BODY>
</HTML>