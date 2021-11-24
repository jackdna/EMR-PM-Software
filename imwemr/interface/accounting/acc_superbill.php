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
require_once('acc_header.php');

/*** Functions ****/

	
	function getRefferPhysicianName($id)
	{
		$str = "";
		if(!empty($id))
		{
			$sql = imw_query("SELECT Title,LastName,FirstName,MiddleName FROM refferphysician
				  WHERE physician_Reffer_id ='".$id."' ");
			$row = imw_fetch_array($sql);
			if($row != false)
			{
				//MD Amos, Joe asad:::Title LastName, FirstName MiddleName ;
				
				$str = !empty($row["LastName"]) ? $row["LastName"] : "";
				$str .= ", ";
				$str .= !empty($row["FirstName"]) ? $row["FirstName"]." " : "";
				$str .= !empty($row["MiddleName"]) ? $row["MiddleName"]." " : "";
				$str .= !empty($row["Title"]) ? $row["Title"]." " : "";
			}
		}
		return $str;
	}
	
	// perportion Image
	function showThumbImage($fileName,$targetWidth=1,$targetHeight=1)
	{
		if(file_exists($fileName))
		{
			 $img_size=getimagesize($fileName);
			 $width=$img_size[0];
			 $height=$img_size[1];
			$fileName = str_ireplace($GLOBALS['fileroot'],$GLOBALS['webroot'],$fileName);
			 do
			 {
				 if($width > $targetWidth)
				 {
					$width=$targetWidth;
					$percent=$img_size[0]/$width;
					$height=$img_size[1]/$percent;
				 }
				 if($height > $targetHeight)
				 {
					$height=$targetHeight;
					$percent=$img_size[1]/$height;
					$width=$img_size[0]/$percent;
				 }

			 }while($width > $targetWidth || $height > $targetHeight);

			return "<img src='$fileName' class='img-responsive' width='$width' height='$height'>";
		}
		return "";
	}	
	/*
	function remLineBrk($str){
		return str_replace(array("\r","\n"),array("\\r","\\n"),$str);
	}
	*/
	//Function Remove line breaks
	function removeLineBreaks($str)
	{
		return preg_replace("(\r\n|\n|\r)", " ", $str);
	}
	
	function isAllCNFinalized($pid){
		$sql = "SELECT id FROM chart_master_table ".
				"WHERE patient_id = '".$pid."' ".
				"AND finalize = '0' AND delete_status='0' ";
		$rez = imw_query($sql);
		if(imw_num_rows($rez) > 0){
			return false ; // Active
		}
		return true; // Finalized
	}
	
	function isChartReviewable($formId,$loggedId,$flgEd=0){
		$isReviewable=false;
		$isEditable=false;
		$iscur_user_vphy=false;
		//Finalize Provider
		$sql = "SELECT c1.id, c1.update_date, c1.patient_id, c1.providerId,c1.finalizerId ".
				"FROM chart_master_table c1 ".
				"WHERE c1.id='".$formId."' ";
		$sql_qry = imw_query($sql);
		$row= imw_fetch_array($sql_qry);
		if($row != false)
		{
			$finalizeDate = $row["update_date"];
			$patientId = $row["patient_id"];
			$providerId = $row["providerId"];
			$finalizeDoctorId = $row["finalizerId"];
		}
		
		//
		$arrProIds=array();
		$sql = "SELECT c2.id FROM chart_signatures c1
				LEFT JOIN users c2 ON c1.pro_id =  c2.id
				WHERE form_id='".$formId."' AND c2.user_type!='13'
				ORDER BY sign_type  ";
		$rez = imw_query($sql);
		for($i=1; $row=imw_fetch_array($rez);$i++){
			if($row["id"]!=false){ 
				$arrProIds[]=$row["id"]; 
				if($i==1){ $doctorId = $row["id"];   }
				if($i==2){ $coSignerId = $row["id"];   }
			}
		}

		//Check for previous data
		if(empty($finalizeDoctorId)){
			$finalizeDoctorId = (!empty($doctorId)) ? $doctorId : $providerId;
		}
		//Check Doctor
		$doctorId = (!empty($doctorId)) ? $doctorId : $providerId;

		if(($finalizeDoctorId == $loggedId)){
			//time
			$sql = imw_query("SELECT chart_timer FROM facility WHERE facility_type = '1' ORDER BY id limit 0,1 ");
			$row = imw_fetch_array($sql);
			if($row != false){
				$reviewTime = $row["chart_timer"];
			}
			
			//if $reviewTime is empty, check in id = 1 ---
			if(!empty($reviewTime)){
				//time
				$sql = imw_query("SELECT chart_timer FROM facility WHERE id = '1' ");
				$row = imw_fetch_array($sql);
				if($row != false){
					$reviewTime = $row["chart_timer"];
				}
			}
			//if $reviewTime is empty, check in id = 1 ---
			
			
			if(!empty($reviewTime)){
				$reviewTimeHrs = 24 * $reviewTime;
				$sql = imw_query("SELECT UNIX_TIMESTAMP(DATE_ADD('".$finalizeDate."', INTERVAL ".$reviewTimeHrs." HOUR)) as reviewableTime, ".
					 "UNIX_TIMESTAMP(NOW()) as curTime ");
				$row = imw_fetch_array($sql);
				if($row != false){
					if($row["reviewableTime"] > $row["curTime"]){
						$isReviewable=true;
					}
				}
			}
		}

		//isEditable
		//if Loggin user is signer/cosigner
		if(($doctorId == $loggedId) || ($coSignerId == $loggedId)){
		//if(in_array($loggedId, $arrProIds)){
			//Check if all records are finalized
			if(isAllCNFinalized($patientId)){
				$isEditable=true;
			}
			//Check valid physician
			$iscur_user_vphy = true;
		}

		return ($flgEd==1) ? array($isReviewable,$isEditable,$iscur_user_vphy):$isReviewable ;
	}
	/*
	//Doctor Name
	function showDoctorName($id,$flg="0")
	{
		if(($id != 0) && !empty($id))
		{
			$sql = imw_query("SELECT lname, mname, fname, pro_suffix,id
					FROM users
					WHERE id = '$id';
					");
			$rez = imw_fetch_array($sql);
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
	*/
/******************/

$patient_id=$_SESSION['patient'];
$page_reload = "";
// Array Physicians
	$arrPhysiciansMenu = array();	
	$arrPhysiciansHidden = array();		
	$rez = "SELECT lname, mname, fname, id FROM users WHERE user_type='1' AND delete_status = '0' ORDER BY lname,fname";
	$sql_qry = imw_query($rez);
	for($i=1;$row=imw_fetch_array($sql_qry);$i++)
	{
		$mnameTemp = ($row["mname"] != NULL) ? $row["mname"]."" : "";
		$phyNameTemp = $row["lname"].", ".$row["fname"]." ".$mnameTemp;
		$arrPhysiciansMenu[] = array($phyNameTemp,$arrEmpty,$phyNameTemp."~!~".$row["id"]);
		$arrPhysiciansHidden[$row["id"]] = addslashes($phyNameTemp); 
		$arrPhysiciansHidden_id[$phyNameTemp] = $row["id"]; 
	}
	$strPhysician4TypeAhead = remLineBrk("'".implode("', '",$arrPhysiciansHidden)."'");
	
//Get Insurance Case Types
	$act_ins_caseid_arr=array();
	$act_ins_data_arr=array();
	$getInsPriCoStr="SELECT * FROM insurance_data 
						WHERE pid='$patient_id'
						AND actInsComp = '1'
						AND provider!=''";
	$getInsPriCoQry = imw_query($getInsPriCoStr);
	while($getInsPriCoRow=@imw_fetch_array($getInsPriCoQry)){
		$act_ins_caseid_arr[$getInsPriCoRow['ins_caseid']]=$getInsPriCoRow['ins_caseid'];
		$act_ins_data_arr[$getInsPriCoRow['ins_caseid']][$getInsPriCoRow['type']]=$getInsPriCoRow;
	}
	$act_ins_caseid_imp=implode(',',$act_ins_caseid_arr);
	$getInsCaseStr = "SELECT a.*, b.* FROM 
					insurance_case a, 
					insurance_case_types b 
					WHERE a.patient_id='$patient_id' 
					AND a.case_status='Open'
					AND a.ins_case_type=b.case_id
					and a.ins_caseid in($act_ins_caseid_imp)";
	$getInsCase=@imw_query($getInsCaseStr);
	while($getInsCaseRow=@imw_fetch_array($getInsCase)){
		$openInsCaseName=$getInsCaseRow['case_name'];
		$openInsCaseId=$getInsCaseRow['ins_caseid'];
		$openInsCaseNameID=$openInsCaseName." - ".$openInsCaseId;
		$caseType = $openInsCaseName." - ".$openInsCaseId;
		$openInsCaseNameIDArray[]= array($caseType);
		$openInsCaseIdArray[]=$openInsCaseId;
		$insCasesArr[]=$caseType;
		$act_ins_case_data_arr[$getInsCaseRow['ins_caseid']]=$getInsCaseRow;
	}
	if($_POST["save"]!="" || $_POST["save_print"]!="" || $_POST["save_form"]=='1'){
		if($_POST["save_print"]!=""){
			$printMode=1;
		}
		$super_update_chk=1;
		$encounterId=$_SESSION['cn_enc'];
		$patient_id=$_SESSION['patient'];
		include "../chart_notes/superbill_save.php";
	}

	// Array POS
	$arrPos = array();
	$arrPos4TypeAhead = array();
	$def_pos = !empty($GLOBALS["SB_POS_Default"]) ? $GLOBALS["SB_POS_Default"] : "O";
	$posPracCodeDefault = $def_pos;
	$rez = imw_query("SELECT * FROM pos_tbl order by pos_prac_code");
	for($i=1;$row=imw_fetch_array($rez);$i++)
	{
		$arrPos[] = array($row["pos_prac_code"]."-".$row["pos_description"],$arrEmpty,$row["pos_prac_code"]);
		$arrPos4TypeAhead[] = addslashes(removeLineBreaks($row["pos_prac_code"]));		
		if(strtoupper($row["pos_description"]) == strtoupper($posPracCodeDefault))
		{
			$posPracCodeDefault = $row["pos_prac_code"];	
		}
	}
	
	// implode into string		
	$strPos4TypeAhead = remLineBrk("'".implode("', '",$arrPos4TypeAhead)."'");	
	
	// Array Tos
	$arrTos = array();
	$arrTos4TypeAhead = array();
	
	$sel_tos_qry=imw_query("select tos_prac_cod from tos_tbl where headquarter ='1'");
	$sel_tos_fet=imw_fetch_array($sel_tos_qry);
	$tosPracCodeDefault=$sel_tos_fet['tos_prac_cod'];
	
	$sel_gcode=imw_query("update cpt_fee_tbl set 	commonlyUsed='1' where
							(cpt4_code='G8447' or cpt_prac_code='G8447' or cpt_desc='G8447') 
							or (cpt4_code='G8553' or cpt_prac_code='G8553' or cpt_desc='G8553')");
							
	$rezTosCat = imw_query("Select * FROM tos_category_tbl ORDER BY tos_category");
	for($i=1;$rowCat=imw_fetch_array($rezTosCat);$i++)
	{
		$arrTosTemp = array();
		
		$rez = imw_query("SELECT * FROM tos_tbl WHERE tos_cat_id='".$rowCat["tos_cat_id"]."' order by tos_prac_cod");
		for($j=1;$row=imw_fetch_array($rez);$j++)
		{
			$arrTosTemp[] = array($row["tos_prac_cod"]."-".$row["tos_description"],$arrEmpty,$row["tos_prac_cod"]);
			$arrTos4TypeAhead[] = addslashes(removeLineBreaks($row["tos_prac_cod"]));			
			if(strtoupper($tosPracCodeDefault) == strtoupper($row["tos_description"]))
			{				
				$tosPracCodeDefault = $row["tos_prac_cod"];
			}		
		}		
		if(count($arrTosTemp) > 0)
		{
			$arrTos[] = array($rowCat["tos_category"],$arrTosTemp);			
		}	
	} 	
	// implode into string		
	$strTos4TypeAhead = remLineBrk("'".implode("', '",$arrTos4TypeAhead)."'");	
?>
<!--
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Super Bill</title>
-->

<style>
.confirmTable3{background-color:#4684ab;border:2px solid #4684ab;font-weight:bold;color:white;}
.confirmBackground{background-color:#ECE9D8;color:black;}
#superbill #divChooseDxCodes{left:160px; top:40px;}
.sm_base{width:auto; height:auto; border:none;	}
.adminbox {
    min-height: auto;
    padding: 0;
}
.adminbox .tblBg{padding:0px 10px 7px 10px}
.superbillhd label{margin:0px!important;}
</style>

<!-- required -->
<link href="<?php echo $GLOBALS['webroot']; ?>/library/messi/messi.css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/workview.css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/wv_landing.css" rel="stylesheet">
<link href="<?php echo $GLOBALS['webroot'];?>/library/css/superbill.css" rel="stylesheet">
<script src="<?php echo $GLOBALS['webroot']; ?>/library/messi/messi.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/icd10_autocomplete.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/work_view/typeahead.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/work_view/superbill.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/work_view/js_gen.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/work_view/work_view.js"></script>

<script type="text/javascript">
	$(document).ready(function(){
		$('.sm_base').html('<img src="<?php echo $GLOBALS['webroot'];?>/images/scrollDown.gif"  style="cursor:pointer">');
	});
	var imgPath = "<?php echo $GLOBALS['webroot'];?>";
	var zPath = "<?php echo $GLOBALS['rootdir'];?>";
	var wpage="accSB";	
	
	
	//Set Propmt Start
	//objCreatePopUp.setStartPoint(90,340);
	var arrPhysician4TypeAhead = new Array(<?php echo $strPhysician4TypeAhead;?>);
	var arrPos4TypeAhead = new Array(<?php echo $strPos4TypeAhead;?>);
	var arrTos4TypeAhead = new Array(<?php echo $strTos4TypeAhead;?>);
	<?php
		echo $page_reload;
	?>
	function checkRefferingPhysician(objElem)
	{
		var objRefPhyDefault = document.getElementById("elem_refferingPhysicianDefault");
		var refPhyNew = trim(objElem.value);
		var refPhyDefault = trim(objRefPhyDefault.value);	
		if((refPhyNew.length > 0))
		{
			if((refPhyDefault.length > 0) && (refPhyNew != refPhyDefault))
			{
				if(confirm("Do you want to change referring physician in patient info?"))
				{		
					objRefPhyDefault.value = refPhyNew;	
				}			
			}
			else
			{
				objRefPhyDefault.value = refPhyNew;	
			}		
		}	
	}
	//Refering Physician
	function searchWindow(){
		window.open("../common/searchPhysician.php","window1","width=500,height=500,scrollbars=yes");
	}
	function get_phy_name_from_search(name,id,type)
	{
		var objReff = document.getElementById("elem_refferingPhysician");
		var objReffName = document.getElementById("elem_refferingPhysicianName");
		if(typeof id != "undefined"){
			objReff.value = id;
			objReffName.value = name;
			objReff.onchange();
		}
	}
	function setPhysicianId(objElem){
		var a=objElem.value;
		if(typeof(a)!='undefined'){
			var ar=a.split("~!~");
			$("#elem_physicianName").val(""+ar[0]);
			$("#elem_physicianId").val(""+ar[1]);
		}
	}
	
	function check4submit(){
		var msg = "Please fill in the following:- <br />";
		var fFalse=0;
		//Super Bill
		var oSB = isSuperBillMade();
		var sb_dxids="";	
		if(oSB.SBill == true){
			if(( oSB.DXCodeOK == false ) || ( oSB.DXCodeAssocOK == false )){
			msg += '<li>Dx code in Super bill for CPT Codes:- '+oSB.CptNotAssoc+'</li>';
			fFalse += 1;
			}
			if(oSB.DXCodeComplete==false){
				msg += '<li>Incomplete ICD-10 DX code(s) in Super bill</li>';
				fFalse += 1;
			}	
			sb_dxids=oSB.dxids;	
		}
		$("#sb_dxids").val(sb_dxids);
		
		if(fFalse > 0){			
			//top.fAlert(""+msg);
			displayConfirmYesNo_v2("Error in saving",msg,"OK","","", 0, 0);
			return false;
		}		
		return true;
	}
	function tos_ajaxfunction(cont){
		if(cont==1){
			var proc_id="elem_cptCode_"+cont;
			var proc_code = document.getElementById(proc_id).value;	
			$.ajax({
				type:'GET',
				url:"get_tos_info_ajax.php?proc_code="+proc_code,
				success:function(response){
					var result = response;
					if(result!=""){
						document.getElementById("elem_tos").value=result;
					}
				}
			});
		}
	}
	
	$('.selectpicker').selectpicker();
</script>

<!--
</head>

<body>
-->
<form name="frmSuperBill" action="<?php echo $GLOBALS['webroot']; ?>/interface/chart_notes/saveCharts.php" method="post" onsubmit="return check4submit();">
<?php 
require_once("../../library/classes/work_view/ChartAP.php");
$form_id=$_REQUEST['form_id'];
$patient_id=$_SESSION['patient'];
	
	//Default Reffering Patient
	$primary_care_phy_sql = imw_query("SELECT primary_care_id FROM patient_data WHERE id = '".$patient_id."'");
	$primary_care_phy_result =  imw_fetch_array($primary_care_phy_sql);
	if(($primary_care_phy_result != false) && ($primary_care_phy_result["primary_care_id"] != ""))
	{
		$refferingPhysicianDefault = $primary_care_phy_result["primary_care_id"];
	}
	if(isset($_GET["id"]) && ($_GET["id"] == "-1")){
	$sql = "SELECT ".			   
			   "chart_master_table.*, ".
			   "chart_master_table.time_of_service, ".
			   "chart_master_table.id AS formID ".
			   "FROM ".
			   "chart_master_table ".			   
			   "LEFT JOIN chart_left_cc_history ON chart_left_cc_history.form_id = chart_master_table.id ".			   
			   "WHERE chart_master_table.id='".$form_id."' AND chart_master_table.patient_id='".$patient_id."' ";		
		$sql_qry = imw_query($sql);
		$row = imw_fetch_array($sql_qry);		
		if(($row != false))
		{
			$elem_edId = "";
			$elem_edMode = "New";						
			$formId = $row["formID"];
			$releaseNumber = $row["releaseNumber"];
			$insuranceCaseId = $row["caseId"];
			$elem_physicianId = $row["providerId"];			
			$_SESSION['cn_enc']=$encounterId = $row["encounterId"];
			$chartNotesFinalize = $row["finalize"];
			$elem_vipSuperBill = $row["isVip"];
			$elem_dos = $row["date_of_service"];
			$elem_time = $row["time_of_service"];
			$physicianId = $row["doctorId"];
			if(empty($elem_dos)||$elem_dos=="0000-00-00")$elem_dos = $row["create_dt"];
			
			$elem_masterCaseId = $row["case_id"];
			$refferingPhysician = $refferingPhysicianDefault;
			$elem_tos = $tosPracCodeDefault;
			$elem_pos = $posPracCodeDefault;
			$enc_icd10 = $row['enc_icd10'];
			$finalize_flag= $row['finalize'];
		}
	}else{
		$sql = "SELECT ".			   
			   " superbill.*, chart_assessment_plans.*, chart_master_table.enc_icd10, chart_master_table.finalize ".
			   "FROM superbill ".
			   "LEFT JOIN chart_assessment_plans ON chart_assessment_plans.form_id = superbill.formId ".
			   "LEFT JOIN chart_master_table ON chart_master_table.id = superbill.formId ".
			   "WHERE idSuperBill='".$_GET["id"]."' and del_status='0'";		
		$sql_qry = imw_query($sql);
		$row = imw_fetch_array($sql_qry);		   
		if($row != false)
		{
			$elem_edId = $row["idSuperBill"];
			$elem_edMode = "Update";
			$formId = $row["formId"];
			$form_id = $row["formId"];
			$insuranceCaseId = $row["insuranceCaseId"];
			$elem_physicianId = $row["physicianId"];
			$_SESSION['cn_enc']=$encounterId = $row["encounterId"];
			$elem_doctorName = $row["doctor_name"];
			$elem_dos = $row["dateOfService"];			
			$refferingPhysician = $row["refferingPhysician"];			
			$todaysCharges = number_format($row["todaysCharges"],2);
			$financialStatus = $row["financialStatus"];
			$elem_vipSuperBill = $row["vipSuperBill"];
			$elem_notesSuperBill = $row["notesSuperBill"];
			$refferingPhysician = $refferingPhysicianDefault;
			$elem_pos = $row["pos"];
			$elem_tos = $row["tos"];
			$patientStatus=$row['patientStatus'];
			$enc_icd10 = ($row['enc_icd10']=="1" || $row['sup_icd10']=="1") ? "1" : "0" ;			
			$finalize_flag= $row['finalize'];
		}
	
	}
	
	list($isReviewable,$isEditable,$iscur_user_vphy) = isChartReviewable($form_id,$_SESSION["authUserID"],1);
	
	$patientStatus = ($patientStatus == "") ? 'Active' : $patientStatus;
	$financialStatus = ($financialStatus == "") ? 'Self' : $financialStatus;
	/* Getting Patients Data */
	$getPatientDetailsStr="SELECT * FROM patient_data WHERE id='$patient_id'";
	$getPatientDetailsQry=@imw_query($getPatientDetailsStr);
	$getPatientDetailsRow=@imw_fetch_array($getPatientDetailsQry);
	$fname=$getPatientDetailsRow['fname'];
	$mname=$getPatientDetailsRow['mname'];
	$lname=$getPatientDetailsRow['lname'];
	$patientName=$lname.", ".$fname." ".$mname;
	$primaryCare=$getPatientDetailsRow['primary_care'];
	$referrer=$getPatientDetailsRow['primary_care'];
	$patientSex=$getPatientDetailsRow['sex'];
	$DOB=$getPatientDetailsRow['DOB'];
	$phoneHome=$getPatientDetailsRow['phone_home'];
	if(empty($phoneHome)){
		$phoneHome = $getPatientDetailsRow['phone_biz'];
	}else if(empty($phoneHome)){
		$phoneHome = $getPatientDetailsRow['phone_contact'];
	}
	else if(empty($phoneHome)){
		$phoneHome = $getPatientDetailsRow['phone_cell'];
	}
	$physicianName = trim(showDoctorName($elem_physicianId));
	
//--

	/* Getting Data of Responsible Party */
	$getRespPartyStr="SELECT * FROM resp_party WHERE patient_id='$patient_id'";
	$getRespPartyQry=@imw_query($getRespPartyStr);
	$getRespPartyRow=@imw_fetch_array($getRespPartyQry);
	if((imw_num_rows($getRespPartyQry) > 0) && ($getRespPartyRow["relation"] != "self") && !empty($getRespPartyRow["relation"])){
		$fname=$getRespPartyRow['fname'];
		$lname=$getRespPartyRow['lname'];
		$name=$fname.", ".$lname;
		$address=$getRespPartyRow['address'];
		$homePh=$getRespPartyRow['home_ph'];
		$workPh=$getRespPartyRow['work_ph'];
		$city=$getRespPartyRow['city'];
		$state=$getRespPartyRow['state'];
		$postal_code = $getRespPartyRow['zip'];
	}else{
		//Patient Data
		$fname=$getPatientDetailsRow['fname'];
		$lname=$getPatientDetailsRow['lname'];
		$name=$fname.", ".$lname;
		$address=$getPatientDetailsRow['street'].", ".$getPatientDetailsRow['street2'];
		$postal_code = $getPatientDetailsRow['postal_code'];
		$homePh=$getPatientDetailsRow['phone_home'];
		$workPh=$getPatientDetailsRow['phone_biz'];
		$city=$getPatientDetailsRow['city'];
		$state=$getPatientDetailsRow['state'];
	}
	/* Getting Data of Responsible Party */
	
	$default_facility=$getPatientDetailsRow['default_facility'];
	$getFacilityDetailsStr="SELECT * FROM pos_facilityies_tbl WHERE pos_facility_id='$default_facility'";
	$getFacilityDetailsQry=@imw_query($getFacilityDetailsStr);
	$getFacilityDetailsRow=@imw_fetch_array($getFacilityDetailsQry);
	$facilityName=$getFacilityDetailsRow['facility_name'];
	$facilityStreet=$getFacilityDetailsRow['pos_facility_address'];
	$facilityState=$getFacilityDetailsRow['pos_facility_state'];
	$facilityCity=$getFacilityDetailsRow['pos_facility_city'];	
	$facilityZip=$getFacilityDetailsRow['pos_facility_zip'];	
	$sql = imw_query("SELECT ".
	 "groups_new.group_Telephone, ".
	 "groups_new.group_Fax, ".
	 "facility.logo ".
	 "FROM facility ".
	 "LEFT JOIN groups_new ON facility.default_group = groups_new.gro_id ".
	 "WHERE facility.facility_type = '1' OR facility.id = '1' ".
	 "LIMIT 0,1");
	$row = imw_fetch_array($sql);
	if($row != false){
		$facilityPhone=$row['group_Telephone'];
		$facilityFax=$row['group_Fax'];	
		$facilityLogo=$row['logo'];	
	}
 
$sql = imw_query("SELECT * FROM chart_assessment_plans WHERE patient_id = '".$patient_id."' AND form_id = '".$form_id."' ");
$row = imw_fetch_array($sql);
if($row != false){
	if(!empty($row["doctorId"]))$elem_physicianId= $row["doctorId"];
	$strXml = stripslashes($row["assess_plan"]);
	
	//Set Assess Plan Resolve NE Value FROM xml ---
	$oChartApXml = new ChartAP($patient_id,$form_id);
	//$arrApVals = $oChartApXml->getVal_Str($strXml);
	$arrApVals = $oChartApXml->getVal();
	$arrAp = $arrApVals["data"]["ap"];
	$lenAssess = count($arrAp);
	$lenAssess=$lenAssess+1; //ADD EMPTY ROW
	echo '<ul id="assessplan" style="display:none;">';
		for($i=0;$i<$lenAssess;$i++){
			$j=$i+1;
			$elem_assessment = "elem_assessment".$j;
			$$elem_assessment = $arrAp[$i]["assessment"];
			$elem_plan = "elem_plan".$j;
			$$elem_plan = $arrAp[$i]["plan"];
			$elem_resolve = "elem_resolve".$j;
			$$elem_resolve = $arrAp[$i]["resolve"];
			$no_change_Assess = "no_change_".$j;
			$$no_change_Assess = $arrAp[$i]["ne"];
			
			if($arrAp[$i]["eye"]=="OU"){
				$elem_apOu = "elem_apOu".$j;
				$$elem_apOu = "1";
			}elseif($arrAp[$i]["eye"]=="OD"){
				$elem_apOd = "elem_apOd".$j;
				$$elem_apOd = "1";
			}elseif($arrAp[$i]["eye"]=="OS"){
				$elem_apOs = "elem_apOs".$j;
				$$elem_apOs = "1";
			}		
			
			//--
			$elem_assessmentDx = "elem_assessment_dxcode".$j;
			$vAssess = $arrAp[$i]["assessment"];
			$lastChar = substr(trim($vAssess), -1);
			$dxcode = '';
			if($lastChar == ")"){
				preg_match("/\([^\(]*$/",trim($vAssess),$arr_match);		
				$ptrn22="/[0-9]+/";					
				if(preg_match($ptrn22, $arr_match[0])){ //check alphanumeric dx code, if not do not remove
					$dxcode = preg_replace("/^\(|\)$/",'',$arr_match[0]);
					$arrAssSplit = preg_split("/\([^\(]*$/",$vAssess);
					$vAssess = $arrAssSplit[0];
					$dxcode = trim($dxcode);
					$$elem_assessmentDx=$dxcode;	
				}
			}
		echo '<li class="planbox">';	
		echo '<input type="hidden" id="'.$elem_assessment.'" name="'.$elem_assessment.'" value="'.$$elem_assessment.'" class="assnm" >';
		echo '<input type="hidden" id="'.$elem_assessmentDx.'" name="'.$elem_assessmentDx.'" value="'.$$elem_assessmentDx.'">';	
		echo '<input type="hidden" id="'.$no_change_Assess.'" name="'.$no_change_Assess.'" value="'.$$no_change_Assess.'">';	
		echo '<input type="hidden" id="'.$elem_resolve.'" name="'.$elem_resolve.'" value="'.$$elem_resolve.'">';
		echo '<input type="hidden" id="'.$elem_apOs.'" name="'.$$elem_apOs.'" value="'.$$elem_apOs.'">';	
		echo '</li>';
		
	}
	echo '</ul>';
		
	//Plus one for empty row 
	$lenAssess = ($cntrLenAssess>=5) ? $cntrLenAssess : 5;	
}

//------------------- chart_left_provider_issue -------------------//
$complaint1StrDB = $complaint2StrDB = $complaint3StrDB = "";
$sql = imw_query("SELECT ".
	"pr_is_id, ocularMeds, complaint1Str, complaint2Str, complaint3Str, complaintHead, selectedHeadText, titleHead ".
	"FROM chart_left_provider_issue ".
	"WHERE patient_id = '".$patient_id."' AND form_id = '".$form_id."' ");
$row=imw_fetch_array($sql);
if($row != false){
	$complaint1StrDB = addcslashes($row["complaint1Str"],"\\\'\"&\n\r<>");
	$complaint2StrDB = addcslashes($row["complaint2Str"],"\\\'\"&\n\r<>");
	$complaint3StrDB = addcslashes($row["complaint3Str"],"\\\'\"&\n\r<>");
}

//------------------- chart_left_provider_issue -------------------//

?>

<script>
	var complaint1 = new Array(<?php echo ($complaint1StrDB)? "'".str_replace(",","','",$complaint1StrDB)."'" : "" ;?>);
	var complaint2 = new Array(<?php echo ($complaint2StrDB)? "'".str_replace(",","','",$complaint2StrDB)."'" : "" ;?>);
	var complaint3 = new Array(<?php echo ($complaint3StrDB)? "'".str_replace(",","','",$complaint3StrDB)."'" : "" ;?>);
	
	var finalize_flag = "<?php echo ($finalize_flag) ? 1:0;?>";
	var isReviewable = "<?php echo ($isReviewable) ? 1 : 0;?>";
	var elem_per_vo = "<?php echo $elem_per_vo;?>";
</script>
<div class="purple_bar">
	<div class="row">
		<div class="col-sm-4">
			<b>Super Bill</b>
		</div>	
		<div class="col-sm-4 text-center">
			<b><?php echo $patientName.' - '.$patient_id; ?></b>
		</div>	
	</div>
</div>
<div id="divWorkView">
	<div class="row pt10">
		<div class="col-sm-10">
			<div class="row">
				<input type="hidden" id="elem_masterId" name="elem_masterId" value="<?php echo $form_id;?>">
				<input type="hidden" name="elem_dos" value="<?php echo $elem_dos;?>">
				<input type="hidden" name="elem_masterCaseId" value="<?php echo $elem_masterCaseId;?>">
				<input type="hidden" name="save_form" id="save_form" value="">
				<input type="hidden" name="elem_saveForm" id="elem_saveForm" value="SaveSuperbill">
				<input type="hidden" name="super_update_chk" id="super_update_chk" value="1">					
				<input type="hidden" id="hid_icd10" name="hid_icd10" value="<?php echo $enc_icd10;?>"/>
				<input type="hidden" id="hid_save_section" name="hid_save_section" value="accounts"/>
				
				<!-- Encounter details Block -->
				<div class="col-sm-12">
					<div class="adminbox">
						<div class="tblBg">
							<div class="row">
								<div class="col-sm-3">
									<div class="form-group">
										<label><strong>E. Id:</strong></label>&nbsp;
										<label><?php echo $encounterId;?></label>
									</div>	
								</div>
								
								<div class="col-sm-3">
									<div class="form-group">
										<label><strong>DOS:</strong></label>&nbsp;
										<label><?php echo get_date_format($elem_dos); ?></label>
										<input type="hidden" name="date_of_service" value="<?php echo $elem_dos; ?>">	
									</div>	
								</div>

								<div class="col-sm-3">
									<div class="form-group">
										<label><strong>Time:</strong></label>&nbsp;
										<label><?php echo $elem_time; ?></label>
									</div>	
								</div>
								
								<div class="col-sm-3">
									<label><strong>Ref. Phy. : </strong></label>&nbsp;	
									<label><?php echo getRefferPhysicianName($refferingPhysician);?></label>	
								</div>
								
								
							</div>
							
							<div class="row">
								<div class="col-sm-3">
									<div class="form-group">
										<div class="row">
											<div class="col-sm-3">
												<label><strong>Ins. Case:</strong></label>		
											</div>
											<div class="col-sm-9">
												<select name="insurancetypeId" class="selectpicker" data-width="100%" data-size="5">
													<option value="">Select</option>
													 <?php  if(count($insCasesArr)>0){ 
													foreach($insCasesArr as $caseTypeNameId){
													$case_type_exp_arr=explode('-',$caseTypeNameId);
													$case_type_exp_val=end($case_type_exp_arr);
													 ?>
														<option value="<?php echo $case_type_exp_arr[1]; ?>"<?php if($insuranceCaseId>0 && $case_type_exp_arr[1]==$insuranceCaseId){ echo "selected";} ?>><?php echo $caseTypeNameId; ?></option>
														<?php 
														}
													}
													?>
												</select>
											</div>	
										</div>	
									</div>							
								</div>
							
							
								<div class="col-sm-3">
									<div class="form-group">
										<div class="row">
											<div class="col-sm-3">
												<label><strong>Physician:</strong></label>
											</div>
											<div class="col-sm-9">
												<input type="text" name="elem_physicianName" id="elem_physicianName" value="<?php echo $physicianName;?>" class="text form-control" onChange="setPhysicianId(this);" style="width:100%"><input type="hidden" name="elem_physicianId" id="elem_physicianId" value="<?php echo $elem_physicianId;?>" >	
												<?php // echo getSimpleMenu($arrPhysiciansMenu,"menu_elem_physicianName","elem_physicianName");?>
												<script type="text/javascript">
													$('#elem_physicianName').typeahead({
														source: arrPhysician4TypeAhead,
													});
												</script>
											</div>	
										</div>	
									</div>	
								</div>
								
								<div class="col-sm-3">
									<div class="row">
										<div class="col-sm-6">
											<div class="form-group">
												<div class="row">
													<div class="col-sm-4">
														<label><strong>TOS:</strong></label>
													</div>
													<div class="col-sm-8">
														<input type="text" name="elem_tos" id="elem_tos" value="<?php echo $elem_tos;?>" class="text form-control input-sm">
														<?php // echo getSimpleMenu($arrTos,"menu_elem_tos","elem_tos");?>		
														<script type="text/javascript">
															$('#elem_tos').typeahead({
																source: arrTos4TypeAhead,
															});
														</script>		
													</div>	
												</div>	
											</div>
										</div>	
										
										<div class="col-sm-6">
											<div class="form-group">
												<div class="row">
													<div class="col-sm-4">
														<label><strong>POS:</strong></label>
													</div>
													<div class="col-sm-8">
														<input type="text" name="elem_pos" id="elem_pos" value="<?php echo $elem_pos;?>" class="text form-control input-sm" style="width:100%">
														<?php // echo getSimpleMenu($arrPos,"menu_elem_pos","elem_pos");?>
														<script type="text/javascript">
															$('#elem_pos').typeahead({
																source: arrPos4TypeAhead,
															});
														</script>	
													</div>	
												</div>	
											</div>
										</div>
									</div>	
								</div>
								
								<div class="col-sm-3">
									<div class="form-group">
										<div class="row">
											<div class="col-sm-2">
												<label><strong>Notes:</strong></label>
											</div>
											<div class="col-sm-10">
												<textarea name="elem_notesSuperBill" id="elem_notesSuperBill" class="text form-control" rows="1" cols="25"><?php echo $elem_notesSuperBill;?></textarea>	
											</div>	
										</div>	
									</div>
								</div>
							</div>
						</div>	
					</div>
				</div>
				
				<!-- Responsible party Block -->
				<div class="col-sm-12">
					<div class="adminbox">
						<div class="head">
							<span>Responsible Party</span>
						</div>	
						<div class="tblBg">
							<div class="row">
								<div class="col-sm-4">
									<label><strong>Name:</strong></label>	&nbsp;
									<label><?php echo $name;?></label>	
								</div>	
								
								<div class="col-sm-4">
									<label><strong>Home #:</strong></label>	&nbsp;
									<label><?php echo core_phone_format($homePh);?></label>	
								</div>	
								
								<div class="col-sm-4">
									<label><strong>Mobile/Work:</strong></label>	&nbsp;
									<label><?php echo core_phone_format($workPh);?></label>	
								</div>		
							</div>
							
							<div class="row">
								<div class="col-sm-4">
									<label><strong>Address:</strong></label>	&nbsp;
									<label><?php echo $address;?></label>	
								</div>	
								
								<div class="col-sm-4">
									<label><strong>Pt. Status:</strong></label>	&nbsp;
									<label><?php echo $patientStatus;?></label>	
								</div>

								<div class="col-sm-4">
									<label><strong>Financial Status:</strong></label>	&nbsp;
									<label><?php echo $financialStatus;?></label>	
								</div>	
							</div>	
						</div>
					</div>	
				</div>	
			</div>
		</div>
		
		<!-- Practice Block -->
		<div class="col-sm-2">
			<div class="row">
				<div class="col-sm-12">
					<div class="adminbox">
						<div class="head">
							<span>Practice</span>
						</div>	
						<div class="tblBg">
							<div class="row">
								<div class="col-sm-12 form-inline">
									<?php
										if(isset($facilityLogo) && !empty($facilityLogo)){
											$uploadPath = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/facilitylogo/";
											echo showThumbImage($uploadPath.$facilityLogo,100,100);
										}
									?>	
								</div>
								<div class="col-sm-12">
									<?php echo "<b>".$facilityName."</b>"; ?>
								</div>
								<div class="col-sm-12">
									<?php echo $facilityStreet; ?>
								</div>
								<div class="col-sm-12">
									<?php echo $facilityCity.", ".$facilityState." ".$facilityZip; ?>
								</div>	
								<div class="col-sm-12">
									<?php echo "<b>Ph. # </b> ".core_phone_format($facilityPhone); ?>
								</div>	
								<div class="col-sm-12">
									<?php echo "<b>Fax # </b> ".core_phone_format($facilityFax); ?>
								</div>		
							</div>
						</div>
					</div>
				</div>	
			</div>
		</div>
		
		<!-- Procedure Details Block -->
		<div class="col-sm-12">
			<div class="adminbox">
				<div class="head">
					<span>Procedure Details</span>	
				</div>
				<div class="tblBg">
					<div class="row">
						<div class="col-sm-12" > <!-- style="overflow-y:scroll;height:200px" -->
						<!-- Super bill -->
							<div id="superbill">
								<span class="loader">Loading..</span>
							</div>
						<!-- Super bill -->	
							<script type="text/javascript">
								$.ajax({
									url:'../chart_notes/onload_wv.php',
									type:'POST',
									data:{ 'elem_action':"GetSuperBill",'accSB':"1"},
									success:function(response){
										$('#superbill').html(response);
										tos_ajaxfunction(1);
										resize_sb_win();
									},
									complete: function(){
										$('body').trigger('page_loaded');	
										$( window ).trigger('resize');
										$("[data-toggle='tooltip']").tooltip();
										fun_mselect('.selectpicker','width');	
									}
								});
							</script>
						</div>	
					</div>	
				</div>
			</div>
		</div>
	</div>
</div>
	<div id="module_buttons" class="row">
		<div class="col-sm-12 ad_modal_footer text-center">
			<input type="submit" name="save"  value="Save"  class="btn btn-success" id="save" >	
			<input type="submit" name="save_print"  value="Save & Print"  class="btn btn-success" id="save_print" >
			<input type="button" name="close_frm"  value="Close"  class="btn btn-danger" id="close_frm" onClick="window.close();">		
		</div>
	</div>
</form>

<script>
	function clientSize(win) {
		var width, height;

		if(win.innerWidth || win.innerHeight) {
			width = win.innerWidth;
			height = win.outerHeight;
		} else {
			var doc = win.document;
			width = doc.documentElement.clientWidth || doc.body.clientWidth;
			height = doc.documentElement.clientHeight || doc.body.clientHeight;
		}

		return { width:width, height:height }
	}
	
	function set_elem_heights(){
		var openerSize = clientSize(window.opener.top);
		
		var current_win = {};
		
		var final_width  = (openerSize.width - ( openerSize.width * 10 / 100 )).toFixed(2);
		var final_height  = (openerSize.height - 30).toFixed(2);
		
		current_win.width = final_width;
		current_win.height = final_height;
		
		$(window)[0].resizeTo(current_win.width, current_win.height);
		
	}
	
	$(document).ready(function(){
		set_elem_heights();		
	});
	
	function resize_sb_win(){
		var prnt_height = $('.mainwhtbox',document).outerHeight();
		if(prnt_height > $(window).height()){
			var diff = prnt_height - ($(window).height() - $('#module_buttons').outerHeight());
			var new_height = $(window).height() - (diff + 50);
			$('#divWorkView > .row').css({
				'height':new_height,
				'overflowX':'hidden',
				'overflowY':'auto'
			});
		}
	}
	
	$('body').on('page_loaded',function(){
		//resize_sb_win();
	});
	$( window ).on('resize',function() {
		var a = parseInt($('#divWorkView > .row').css('height'));
		var b = $(window).height();
		var c = $('.mainwhtbox',document).outerHeight();
		var d = $('#module_buttons').outerHeight();		
		var e = b - (c+d);
		a = a + e + 20; 
		$('#divWorkView > .row').css('height', a);
	});
</script>

</body>
</html>