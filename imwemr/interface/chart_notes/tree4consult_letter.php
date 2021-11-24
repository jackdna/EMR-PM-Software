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

//-----  Get data from remote server -------------------
//$zRemotePageName = "tree4consult_letter";
//require(dirname(__FILE__)."/get_chart_from_remote_server.inc.php");
//-----  Get data from remote server -------------------
include_once(dirname(__FILE__)."/../../config/globals.php");
include_once(dirname(__FILE__)."/../../library/classes/cls_common_function.php");
//$objManageData = new ManageData;
require_once("../../library/classes/dhtmlgoodies_tree.class.php");

$tree = new dhtmlgoodies_tree();
//$tree->addToArray(1,"Signed Forms",0,"");
$patient_id = $_SESSION['patient'];
$form_id = $_SESSION['form_id'];
$browserIpad = 'no';
$targetArea = 'consent_data';
if(stristr($_SERVER['HTTP_USER_AGENT'], 'ipad') == true) {
	$browserIpad = 'yes';
	$targetArea = '_blank';
}
//function by Er. Ravi Mantra for making 2 Dimensional array
function makeUniqueInfo($array){
    $dupes=array();
    foreach($array as $values){
        if(!in_array($values,$dupes))
            $dupes[]=$values;
    }
    return $dupes;
}

//--- Update Status of templates ------
if($moveToTrashConsentId || $moveToTrashConsentOriginalId){
	if($moveToTrashConsentId) {
		$qry = "update send_fax_log_tbl set status = '$st', cur_date_time = '".date("Y-m-d H:i:s")."' 
			where id = '$moveToTrashConsentId'";
		
	}else{
		$qry = "update patient_consult_letter_tbl set status = '$st',cur_date = '".date("Y-m-d H:i:s")."' 
					where patient_consult_id = '$moveToTrashConsentOriginalId'";
	}
	imw_query($qry);
	/*
	$sendfaxlog_qry = "update `send_fax_log_tbl` set status = '$st', cur_date_time = now() 
				where id = '$moveToTrashConsentId'";
	
	imw_query($sendfaxlog_qry);*/
}

/*Move back to pending Fax*/
if($moveToPendingFax){
	
	if(!$cnfrm){
		echo '<script>top.fancyConfirm("Sure! you want to move the fax to pending ?","top.fmain.restoreFax('.$moveToPendingFax.',true)");</script>';
	}
	else{
		$sqlDel = 'UPDATE `inbound_fax` SET `patient_id`=0, `pending_by`='.((int)$_SESSION['authId']).', `moved_pending_at`=\''.date('Y-m-d H:i:s').'\', `fax_folder`=\'\' WHERE `id`='.$moveToPendingFax;
		imw_query($sqlDel);
	}
}

$operator_id = $_SESSION['authId'];
if(empty($scanIdConsultLetter) != true){
	$qry = "update ".constant("IMEDIC_SCAN_DB").".scans set status = '$st',
			modi_date = now(),operator_id = '$operator_id'
			where scan_id = '$scanIdConsultLetter'";
	imw_query($qry);
}
//--- Get All Physician Name --------
$qry = "select id,lname,fname, concat(lname,', ',fname,' ',mname) AS phyFullName from users";
$phyQryRes = get_array_records_query($qry);
for($i=0;$i<count($phyQryRes);$i++){
	$phyname = ucfirst($phyQryRes[$i]['fname'][0]);
	$phyname .= ucfirst($phyQryRes[$i]['lname'][0]);
	$id = $phyQryRes[$i]['id'];
	$phyFullName = $phyQryRes[$i]['phyFullName'];
	$phyNameArr[$id] = $phyname;
	$phyFullNameArr[$id] = trim(stripslashes($phyFullName));
}
//function by Er. Ravi Mantra for making 2 Dimensional array


//---- Get Patient Consent Forms Signed Date(s)-------
$qry = "SELECT distinct DATE_FORMAT(date, '".get_sql_date_format()."') sortDate, DATE_FORMAT(date, '".get_sql_date_format('','Y','/')."') formCreatedDate from patient_consult_letter_tbl  where patient_id=$patient_id and status = '0' ORDER BY `date` desc" ;
$patientConsultLetterCreatedDate = get_array_records_query($qry);

//CODE FOR SCAN DISPLAY
$qryScanUploadConsultLetter = "SELECT distinct DATE_FORMAT(created_date, '".get_sql_date_format('','Y','/')."') formCreatedDate 
		from ".constant("IMEDIC_SCAN_DB").".scans  where patient_id=$patient_id 
		AND image_form='consultLetter' and status != '1' ORDER BY created_date desc" ;
$ptScanUploadConsultLetterCreatedDate = get_array_records_query($qryScanUploadConsultLetter);
$patientConsultLetterCreatedDate = array_merge($patientConsultLetterCreatedDate,$ptScanUploadConsultLetterCreatedDate);
//rsort($patientConsultLetterCreatedDate);
$patientConsultLetterCreatedDate = makeUniqueInfo($patientConsultLetterCreatedDate);
//CODE FOR SCAN DISPLAY

include_once($GLOBALS['fileroot']."/interface/common/docs_name_header.php");
if(!$p) { $p=1;}
$p++;
$z = $p;
$tree->addToArray($z,"Consult Letter",0,"","",$initConsultLetterClass);
$a=$z;
$t=$z;
foreach($patientConsultLetterCreatedDate as $z=>$val) {
	$p++;
	$formCreatedDate=$patientConsultLetterCreatedDate[$z]['formCreatedDate'];
	$tree->addToArray($p,$formCreatedDate,$a,"","","icon-folder-filled");	
	//---- Get Patient Signed Consent Forms Created Date(s)-------
		$qrynew = "SELECT pclt.patient_consult_id,pclt.patient_form_id,pclt.templateData,
					pclt.date,pclt.cur_date ,pclt.templateId,pclt.templateName,pclt.operator_id, m.source_id as media_id,
					DATE_FORMAT(pclt.cur_date, '".get_sql_date_format('','Y','-')." %h:%i %p') AS consult_date_time,
					CONCAT(u.lname,', ',u.fname,' ',u.mname) AS operator_name
					FROM patient_consult_letter_tbl pclt 
					LEFT JOIN ".constant('IMEDIC_SCAN_DB').".media m ON (pclt.patient_consult_id=m.source_id AND m.source='consult_letter') 
					LEFT JOIN users u ON(u.id = pclt.operator_id)
					WHERE pclt.patient_id = '".$patient_id."' 
					and DATE_FORMAT(date, '".get_sql_date_format('','Y','/')."')='$formCreatedDate' 
					and status != '1' ORDER BY status desc" ;
		$patientConsultLetter = get_array_records_query($qrynew);//echo $qrynew.'<hr>';
		$b=$p;
		for($x=0;$x<count($patientConsultLetter);$x++){
			$p++;
			$media_id = $patientConsultLetter[$x]['media_id'];
			$pdf_icon_type = 1; //if(constant('AV_MODULE')=='YES' && $media_id>0){$pdf_icon_type=2;}
			$consentFormId = $patientConsultLetter[$x]['templateId'];
			$operator_id = $patientConsultLetter[$x]['operator_id'];
			$consentFormInfoId = $patientConsultLetter[$x]['patient_form_id'];
			$patient_consult_id = $patientConsultLetter[$x]['patient_consult_id'];
			$mod_date = date("g:i A",strtotime($patientConsultLetter[$x]['cur_date']));
			$userName = $patientConsultLetter[$x]['fname'][0];
			$userName .= $patientConsultLetter[$x]['lname'][0];
			$userName = strtoupper(trim($userName));
			$consentFormName = $patientConsultLetter[$x]['templateName'];//."(".$form_created_time.")";
			$consentFormName = trim(ucwords($consentFormName));
			$showInfo		 = "yes";
			$consultDateTime = $patientConsultLetter[$x]['consult_date_time'];
			$operatorName 	 = stripslashes($patientConsultLetter[$x]['operator_name']);
			$tree->addToArray($p,$consentFormName,$b,"templatepri.php?tempId=$patient_consult_id&media_id=$media_id",$targetArea,"pdf-icon","remove-icon",$GLOBALS['webroot']."/interface/chart_notes/consult_letter_page.php?moveToTrashConsentOriginalId=$patient_consult_id&st=1","","Move To Trash","","","",$showInfo,$consultDateTime,$operatorName,"","","",true,"","","",true);
		}
		
		//START CODE FOR SCAN
		$qryScan = "SELECT * FROM ".constant("IMEDIC_SCAN_DB").".scans 
					where patient_id=$patient_id 
					AND image_form='consultLetter'
					AND DATE_FORMAT(created_date, '%m/%d/%Y')='$formCreatedDate'
					And status != '1'   
					ORDER BY scan_id";
		$scanPatientConsultLetter = get_array_records_query($qryScan);
		for($w=0;$w<count($scanPatientConsultLetter);$w++){
			$p++;
			$scanIdConsultLetter = $scanPatientConsultLetter[$w]['scan_id'];			
			$scanFileType 		 = $scanPatientConsultLetter[$w]['file_type'];
			$consentFormNameScan = $scanPatientConsultLetter[$w]['image_name'];//."(".$form_created_time.")";
			$operator_id = $scanPatientConsultLetter[$w]['operator_id'];
			$mod_date = date("g:i A",strtotime($scanPatientConsultLetter[$w]['modi_date']));
			$scanIcon="../../images/dhtml_sheet.gif";
			if($scanFileType=='application/pdf') {
				$scanIcon="pdf-icon";
			}
			$consentFormNameScan = trim(ucwords($consentFormNameScan));
			if(empty($mod_date) != true || empty($phyNameArr[$operator_id]) != true){
				$consentFormNameScan .= '('.$mod_date.' '.$phyNameArr[$operator_id].')';
			}
			$tree->addToArray($p,urldecode($consentFormNameScan),$b,"logoImg.php?from=scanImage&scan_id=$scanIdConsultLetter",$targetArea,$scanIcon,"remove-icon",$GLOBALS['webroot']."/interface/chart_notes/consult_letter_page.php?scanIdConsultLetter=$scanIdConsultLetter&st=1","","Move To Trash","","","","","","","","","",true,"","","",true);
		}
		//END CODE FOR SCAN
}
$b=$p;


//--- Trash Folders ---------

$p_trash = $p+1;
$p = $p+1;
$f = 0;
$k=$p;
$a=$t;
$tree->addToArray($p,"Trash",$a);

$qryConsultTrash = "SELECT distinct DATE_FORMAT(date, '".get_sql_date_format('','Y','/')."') as formCreatedDate 
from patient_consult_letter_tbl where patient_id='$patient_id' 
and status = '1'  ORDER BY date desc" ;
$qryConsultTrashRes = get_array_records_query($qryConsultTrash);	

$trashQryRes = array();
$trashQryRes = array_merge($qryConsultTrashRes,$trashQryRes);
array_unique($trashQryRes);
rsort($trashQryRes);

$trashQryResNew = array();
$tmpDtArr = array();
for($d_fax_tmp=0;$d_fax_tmp<count($trashQryRes);$d_fax_tmp++){
	$aTemp =0;
	if(!in_array($trashQryRes[$d_fax_tmp]['formCreatedDate'],$tmpDtArr)) {
		$tmpDtArr[] = $trashQryRes[$d_fax_tmp]['formCreatedDate'];
		$trashQryResNew[$d_fax_tmp]['formCreatedDate'] = $trashQryRes[$d_fax_tmp]['formCreatedDate'];
		$aTemp++;	
	}
}
$b=$p;	
for($d=0;$d<count($trashQryResNew);$d++){
	$p++;
	//$p++;
	$formCreatedDate = $trashQryResNew[$d]['formCreatedDate'];
	$sendfaxlogId = $trashQryResNew[$d]['sendfaxlog_id'];
	if(trim($formCreatedDate)) {
		$tree->addToArray($p,$formCreatedDate,$b,"");
	}
	
	$qrynewTrash = "SELECT pclt.patient_consult_id,pclt.patient_form_id,pclt.templateData,
				pclt.date,pclt.cur_date ,pclt.templateId as template_id,pclt.templateName as template_name,pclt.operator_id,
				DATE_FORMAT(pclt.cur_date, '".get_sql_date_format('','Y','-')." %h:%i %p') AS consult_date_time,
				CONCAT(u.lname,', ',u.fname,' ',u.mname) AS operator_name
				FROM patient_consult_letter_tbl pclt
				LEFT JOIN users u ON(u.id = pclt.operator_id)
				WHERE patient_id = '$patient_id' 
				and DATE_FORMAT(date, '".get_sql_date_format('','Y','/')."')='$formCreatedDate' 
				and status != '0' ORDER BY status desc" ;
	
	$resnewTrash = get_array_records_query($qrynewTrash);
	//BELOW CODE COMMENTED TO STOP DISPLAY SEND_FAX_LOG_TBL SEND FAX RECORD INTO SEND FAX TRASH SECTION UNDER CONSULT LETTER ICON
	$c=$p;
	for($x=0;$x<count($resnewTrash);$x++,$p++){
		$p++;
		$mod_date = date("g:i A",strtotime($resnewTrash[$x]['cur_date']));
		$sendfaxlogId = $resnewTrash[$x]['sendfaxlogId'];
		$consentFormId = $resnewTrash[$x]['template_id'];
		$operator_id = $resnewTrash[$x]['operator_id'];
		$patient_consult_id = $resnewTrash[$x]['patient_consult_id'];
		$consentFormName = $resnewTrash[$x]['template_name'];//."(".$form_created_time.")";
		$consentFormName = trim(ucwords($consentFormName));
		$showInfo		 = "yes";
		$consultDateTime = $resnewTrash[$x]['consult_date_time'];
		$operatorName 	 = stripslashes($resnewTrash[$x]['operator_name']);
		$tree->addToArray($p,$consentFormName,$c,"templatepri.php?tempId=$patient_consult_id",$targetArea,"pdf-icon","restore-icon",$GLOBALS['webroot']."/interface/chart_notes/consult_letter_page.php?moveToTrashConsentId=&moveToTrashConsentOriginalId=$patient_consult_id&st=0","","Move To Forms","","","",$showInfo,$consultDateTime,$operatorName,"","","",true);
	}
}

$p = $p+1;
$d=$p;
$qry = "SELECT distinct DATE_FORMAT(created_date, '".get_sql_date_format('','Y','/')."') as formCreatedDate 
		from ".constant("IMEDIC_SCAN_DB").".scans where patient_id='$patient_id' 
		and image_form='consultLetter'
		and status = '1' ORDER BY created_date desc" ;
$trashQryRes = get_array_records_query($qry);
for($d=0;$d<count($trashQryRes);$d++){	
	$p++;
	$formCreatedDate = $trashQryRes[$d]['formCreatedDate'];
	$tree->addToArray($p,$formCreatedDate,$d,"");
	//START CODE FOR SCAN
	$qryScan = "SELECT * FROM ".constant("IMEDIC_SCAN_DB").".scans 
				where patient_id='$patient_id' 
				AND image_form='consultLetter'
				AND DATE_FORMAT(created_date, '".get_sql_date_format('','Y','/')."')='$formCreatedDate'
				And status = '1'   
				ORDER BY scan_id";
	$scanPatientConsultLetter = get_array_records_query($qryScan);
	$e = $p;
	for($w=0;$w<count($scanPatientConsultLetter);$w++,$p++){
		$p++;
		$scanIdConsultLetter = $scanPatientConsultLetter[$w]['scan_id'];
		$scanFileType 		 = $scanPatientConsultLetter[$w]['file_type'];
		$consentFormNameScan = $scanPatientConsultLetter[$w]['image_name'];//."(".$form_created_time.")";
		$operator_id = $scanPatientConsultLetter[$w]['operator_id'];
			$mod_date = date("g:i A",strtotime($scanPatientConsultLetter[$w]['modi_date']));
		$scanIcon="../../images/dhtml_sheet.gif";
		if($scanFileType=='application/pdf') {
			$scanIcon="pdf-icon";
		}
		$consentFormNameScan = trim(ucwords($consentFormNameScan));
		$consentFormNameScan = trim(ucwords($consentFormNameScan));
		if(empty($mod_date) != true || empty($phyNameArr[$operator_id]) != true){
			$consentFormNameScan .= '('.$mod_date.' '.$phyNameArr[$operator_id].')';
		}
		$tree->addToArray($p,urldecode($consentFormNameScan),$e,"logoImg.php?from=scanImage&scan_id=$scanIdConsultLetter",$targetArea,$scanIcon,"../../images/restore_icon.png",$GLOBALS['webroot']."/interface/chart_notes/consult_letter_page.php?scanIdConsultLetter=$scanIdConsultLetter&st=0","","Move To Forms","","","","","","","","","",true);
	}
}

include_once($GLOBALS['fileroot']."/interface/common/docs_name.php");
$p++;

$tree->writeCSS();
$tree->writeJavascript();
$tree->drawTree();
?>
<script type="text/javascript">
function restoreFax(fax_id, cnfrm){
	if (typeof(cnfrm)!="undefined" && cnfrm===true) {
		var wbr = "<?php echo $GLOBALS['webroot'];?>";
		window.location.href = wbr+"/interface/chart_notes/consult_letter_page.php?moveToPendingFax="+fax_id+"&cnfrm=true";
	}
}
$(function(){
	$('[data-toggle="tooltip"]').tooltip({container:'body'});
});	
</script>