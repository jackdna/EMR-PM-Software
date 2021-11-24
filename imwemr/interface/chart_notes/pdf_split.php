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
include("../../config/globals.php");
include($GLOBALS['srcdir'].'/classes/folder_function.php');
include($GLOBALS['srcdir'].'/classes/SaveFile.php');

$save = new saveFile();
//creating pdfsplit folder
$save->ptDir('pdfSplit/tmp');

if($phpServerIP != $_SERVER['HTTP_HOST'])	
{
	$phpServerIP=$_SERVER['HTTP_HOST'];
	$GLOBALS['php_server'] = $phpHTTPProtocol.$phpServerIP.$phpServerPort.$web_root;
}

$action = ($_REQUEST['action']) ? $_REQUEST['action'] : 'scan';
$currDt 		= date('Y-m-d H:i:s');
$selDos			= $_REQUEST['selDos'];
$file_path 	= $GLOBALS['fileroot'].'/data/'.PRACTICE_PATH.'/pdfSplit';
$file_path_web 	= $GLOBALS['webroot'].'/data/'.PRACTICE_PATH.'/pdfSplit';
$destniFile = $GLOBALS['fileroot'].'/data/'.PRACTICE_PATH;

//NEW UPLOAD CONTROL
$activex = $_REQUEST['activex'];
$upload = array();
if ($hiddPdfSplit == 'yes')
{	
	require_once($GLOBALS['srcdir'].'/upload/server/php/UploadHandler.php');
	
	// Change Default Option for upload handler before calling class constructor
	$options = array(
		'script_url' => $GLOBALS['php_server'].'/chart_notes/pdf_split.php',
		'upload_dir' => $file_path.'/tmp/',
		'upload_url' => $file_path_web.'/tmp/',
		'access_control_allow_origin' => '*','access_control_allow_credentials' => false,
		'access_control_allow_methods' => array('OPTIONS','HEAD','GET','POST','PUT','PATCH','DELETE'),
		'access_control_allow_headers' => array('Content-Type','Content-Range','Content-Disposition'),
		'inline_file_types' => '/\.(pdf)$/i', 'accept_file_types' => '/\.(pdf)$/i',
		'max_file_size' => null,'min_file_size' => 1,'max_number_of_files' => null,'max_width'=>null,'max_height'=>null,'min_width'=>1,'min_height'=>1,
		'discard_aborted_uploads'=>true,'orient_image'=>false
	);
	
	$upload_handler = new UploadHandler($options,true);
	$response = (object) $upload_handler->response;
		
	if( $response->files )
	{
		foreach($response->files as $file)
		{
			if($file->type && !$file->error && $file->url )
			{
				$pdfFileNme = urldecode($file->name);
				$pdfFileNme = urldecode($pdfFileNme);
				$pdfFileNme = str_ireplace(" ","-",$pdfFileNme);
				$pdfFileNme = str_ireplace(",","-",$pdfFileNme);
				$pdfFileNme = str_ireplace("'","-",$pdfFileNme);
				$pdfFileNme = str_ireplace('.pdf','',$pdfFileNme);
				$pdfFileNme = str_ireplace(".","",$pdfFileNme);
				
				$pdfFileNmeValue = $pdfFileNme.'_'.$_SESSION['authId'].'_';
				exec('pdftk '.$file_path.'/tmp/'.$pdfFileNme.'.pdf  burst output '.$file_path.'/'.$pdfFileNmeValue.'%03d.pdf');
				
				if(file_exists($file_path.'/tmp/'.$pdfFileNme.'.pdf')) {
					unlink($file_path.'/tmp/'.$pdfFileNme.'.pdf');
				}
			}
		}
	}
	die;
}
//NEW UPLOAD CONTROL


//START CODE TO DELETE PENDING PDF FILES
if($_REQUEST['delPdfSplit']=='yes')
{
	$hidd_pdfFileNmeArr = $_REQUEST['hidd_pdfFileNme'];
	foreach($hidd_pdfFileNmeArr as $key=> $hidd_pdfFileNme) {
		$chkbxPdf = $_REQUEST['chkbxPdf'.($key+1)];
		if($chkbxPdf=='yes') {
			$msgRec='Record Deleted Successfully';
			if(file_exists($file_path.'/'.$hidd_pdfFileNme)) {
				unlink($file_path.'/'.$hidd_pdfFileNme);
			}
		}
	}
}
//END CODE TO DELETE PENDING PDF FILES
	
//START CODE TO SAVE RECORD
if($_REQUEST['savePdfSplit']=='yes' && $_REQUEST['delPdfSplit'] <> 'yes')
{
	$hidd_pdfFileNmeArr		= $_REQUEST['hidd_pdfFileNme'];
	$pdfFileNmeArr 				= $_REQUEST['pdfFileNme'];
	$ptNmeListArr 				= $_REQUEST['ptNmeList'];
	$folderNmeListArr 		= $_REQUEST['folderNmeList'];
	
	foreach($hidd_pdfFileNmeArr as $key=> $hidd_pdfFileNme)
	{
		$msgRec='Record Saved';
		
		$pdfFileNme 				= $pdfFileNmeArr[$key];
		$ptNmeList 					= $ptNmeListArr[$key];
		$hidd_ptNmeListId		= $_REQUEST['hidd_ptNmeListId'.($key+1)];
		$folderNmeList 			= $folderNmeListArr[$key];

		$hidd_pdfFileNme 		= trim(str_ireplace('.pdf','',$hidd_pdfFileNme));
		$hidd_pdfFileNme 		= trim(str_ireplace('.','',$hidd_pdfFileNme));
		$pdfFileNme 				= trim(str_ireplace('.pdf','',$pdfFileNme));
		$pdfFileNme 				= trim(str_ireplace('.','',$pdfFileNme));
		$pdfFileNme 				= trim(str_ireplace("'","",$pdfFileNme));
		
		if($hidd_pdfFileNme!=$pdfFileNme) {
			if(file_exists($file_path.'/'.$hidd_pdfFileNme.".pdf")) { 
				rename($file_path.'/'.$hidd_pdfFileNme.".pdf",$file_path.'/'.$pdfFileNme.".pdf"); //RENAME ON EXISTING FOLDER
			}
		}
		
		unset($ptSave);
		if(file_exists($file_path.'/'.$pdfFileNme.".pdf"))
		{
			
			if($hidd_ptNmeListId!='' && $folderNmeList!='')
			{
				$pdfFileNme 		= trim(str_ireplace('.pdf','',$pdfFileNme)); //IF USER REPEAT MORE THAN ONCE
				$pdfFileNme 		= trim(str_ireplace('.','',$pdfFileNme)); //IF USER REPEAT MORE THAN ONCE
				$pdfFileNmeTmp	= $pdfFileNme;
				$pdfFileNme			= $pdfFileNme.".pdf"; //APPLY ONLY AT THE END OF PDF FILE NAME
				$PSize 					= @filesize($file_path.'/'.$pdfFileNme);
				
				$ptSave = new SaveFile($hidd_ptNmeListId);
				$ptSave->ptDir('Folder/id_'.$folderNmeList);
				
				$fullDOS='0000-00-00';
				$chart_note = 'no';
				$inserIdScanUpload='';
				if(trim($PSize))
				{
					$qryInsertScanDocs = "INSERT INTO ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl set 
																folder_categories_id='".$folderNmeList."',
																patient_id='".$hidd_ptNmeListId."',
																doc_title='".$pdfFileNme."',chart_note='".$chart_note."',
																doc_type='pdf',doc_upload_type='upload', chart_note_date='".$fullDOS."',
																pdf_url='".$pdfFileNme."',upload_operator_id='".$_SESSION['authId']."'";
					$resInsertScanDocs = imw_query($qryInsertScanDocs)or die(imw_error());
					$inserIdScanUpload = imw_insert_id();
					
					$pdfFileNmeSavePath = $pdfFileNmeTmp."_".$inserIdScanUpload.".pdf";
					$file_path_new			= "/PatientId_".$hidd_ptNmeListId."/Folder/id_".$folderNmeList."/".$pdfFileNmeSavePath;
					$newFilePath = $destniFile.$file_path_new;
					if(file_exists($file_path.'/'.$pdfFileNme)) {
						$renameFiles = rename($file_path.'/'.$pdfFileNme,$newFilePath);
					}
					
					$updtScanDocQry = "UPDATE ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl set file_path='".$file_path_new."',doc_size='".trim($PSize)."', upload_docs_date='".$currDt."' WHERE scan_doc_id='".$inserIdScanUpload."'";
					$updtScanDocRes=imw_query($updtScanDocQry)or die(imw_error());
					
					
				}else{
					echo "Unable to copy the file: ".$pdfFileNme."<br>";		
				}
			}
		}
	}
}
//END CODE TO SAVE RECORD

if($_REQUEST['savePdfSplit'] == 'yes' || $_REQUEST['delPdfSplit'] == 'yes')
{
	echo '<script>top.fmain.ifrm_FolderContent.window.location="'.$GLOBALS['webroot'].'/interface/chart_notes/pdf_split.php"</script>';
}

// Source Type
$browser = browser();
$action_btn_title = ($action == 'upload') ? 'Scan' : 'Upload';
echo "<script>autoScan='no';</script>";
$scanUploadSrc = $GLOBALS['fileroot']."/library/scan/scan_control.php";
if( $action == 'upload' )
{
	$scanUploadSrc = $GLOBALS['fileroot']."/library/upload/index.php";
	$upload_from = 'pdfsplitter';
}

/* Start Loading defaults in array */
$ptNameArr = array();
$ptIdQry = "SELECT pid,fname,lname FROM patient_data ORDER BY pid" ;
$ptIdRes = imw_query($ptIdQry) or die(imw_error());	
if(imw_num_rows($ptIdRes)>0) {
	while($ptIdRow = imw_fetch_array($ptIdRes)) {	
		$ptName = $ptIdRow['lname'].', '.$ptIdRow['fname'];
		$ptNameId = $ptName.' - '.$ptIdRow['pid'];
		$ptNameArr[$ptIdRow['pid']] = $ptNameId;
	}
}

$categoryNameArr = $pckNameArr = array();
$catQry = "SELECT cat_id,category_name FROM consent_category ORDER BY category_name";                    
$catRes = imw_query($catQry) or die(imw_error());
if(imw_num_rows($catRes)>0) {
	while($catRow = imw_fetch_array($catRes)) {
		$catId 					 = $catRow["cat_id"];
		$categoryNameArr[$catId] = $catRow["category_name"];
	}
}

$pckQry = "SELECT package_category_id,package_category_name FROM consent_package ORDER BY package_category_name";
$pckRes = imw_query($pckQry) or die(imw_error());
if(imw_num_rows($pckRes)>0) {
	while($pckRow = imw_fetch_array($pckRes)) {
		$pckId 					 = $pckRow["package_category_id"];
		$pckNameArr[$pckId] 	 = $pckRow["package_category_name"];
	}
}

$signedFolderNameArr = $signedFolderIdArr = $pckFolderNameArr = $pckFolderIdArr = array();
$scnQry = "SELECT folder_categories_id,folder_name FROM ".constant("IMEDIC_SCAN_DB").".folder_categories  WHERE folder_status ='active' AND patient_id=0 AND parent_id IN(SELECT folder_categories_id FROM ".constant("IMEDIC_SCAN_DB").".folder_categories WHERE folder_name='Signed Consents' AND folder_status ='active' AND patient_id=0 ORDER BY folder_categories_id )  ORDER BY folder_categories_id";
$scnRes = imw_query($scnQry) or die(imw_error());
if(imw_num_rows($scnRes)>0) {
	while($scnRow = imw_fetch_array($scnRes)) {
		$signedFolderId 						= $scnRow["folder_categories_id"];
		$signedFolderName 						= strtolower($scnRow["folder_name"]);
		$signedFolderNameArr[$signedFolderId] 	= $signedFolderName;
		$signedFolderIdArr[$signedFolderName] 	= $signedFolderId;
	}
}

$scnPckQry = "SELECT folder_categories_id,folder_name FROM ".constant("IMEDIC_SCAN_DB").".folder_categories  WHERE folder_status ='active' AND patient_id=0 AND parent_id IN(SELECT folder_categories_id FROM ".constant("IMEDIC_SCAN_DB").".folder_categories WHERE folder_name='Signed Package' AND folder_status ='active' AND patient_id=0 ORDER BY folder_categories_id )  ORDER BY folder_categories_id";
$scnPckRes = imw_query($scnPckQry) or die(imw_error());
if(imw_num_rows($scnPckRes)>0) {
	while($scnPckRow = imw_fetch_array($scnPckRes)) {
		$pckFolderId 					= $scnPckRow["folder_categories_id"];
		$pckFolderName 					= strtolower($scnPckRow["folder_name"]);
		$pckFolderNameArr[$pckFolderId] = $pckFolderName;
		$pckFolderIdArr[$pckFolderName] = $pckFolderId;
	}
}

$arrFolder	= array();
$qry 				= "SELECT folder_categories_id,folder_name FROM ".constant("IMEDIC_SCAN_DB").".folder_categories WHERE parent_id=0 AND patient_id='0' AND folder_status='active' order by folder_name";
$dia_res		= imw_query($qry);
$dia_num		= imw_num_rows($dia_res);
if($dia_num>0){
	$mainArr = array();
	$tempArr = array();
	while($rowFolder = imw_fetch_assoc($dia_res))
	{
		$level = 0;
		$categoryID = $rowFolder['folder_categories_id'];
		$categoryName = $rowFolder['folder_name'];
		$parentID = $rowFolder['parent_id'];
		$mainArr[$categoryID] = '&gt;&gt;'.$categoryName;
		$tempArr = getChild1($categoryID, $level,'no_pid');
		if(is_array($tempArr) && count(($tempArr)) > 0  )
			$mainArr = mergeArr($mainArr,$tempArr);
	}
	$catArr = $mainArr;
}

?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Multi Upload Splitter :: imwemr</title>
<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.css" type="text/css">
<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap-select.css" type="text/css">
<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css" type="text/css">
<link href="<?php echo $GLOBALS['webroot'];?>/library/messi/messi.css" rel="stylesheet" type="text/css">

<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.min.1.12.4.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/bootstrap.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/bootstrap-select.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/core_main.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/common.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/messi/messi.js"></script>
<script>
var cPtFldObj = '';
var wo=top.JS_WEB_ROOT_PATH;
function trim(val){ 
	return val.replace(/^\s+|\s+$/, ''); 
}

function scanUploadFun() {
	top.show_loading_image('show','-60','Please Wait');
	top.fmain.ifrm_FolderContent.window.location.href=wo+"/interface/chart_notes/pdf_split.php?action=<?php echo strtolower($action_btn_title);?>";	
}
function submit_frm() {
	top.show_loading_image('show','-60','Please Wait');
	top.fmain.ifrm_FolderContent.document.frmPdfSplit.submit();
}

function delPdfSplitFun(cntr,cnfrm) {
	cntr = cntr || document.getElementById('counterPdfMinusOne').value;
	//START CODE TO CHECK THE RECORD SELECTED TO DELETE
	var delPdfFiles=false;
	for(var i=1;i<=cntr;i++) {
		if(document.getElementById('chkbxPdf'+i)) {
			if(document.getElementById('chkbxPdf'+i).checked==true) {
				delPdfFiles=true;
			}
		}
	}
	//END CODE TO CHECK THE RECORD SELECTED TO DELETE
	
	if(delPdfFiles==true) {
		
		if (typeof(cnfrm)=="undefined") {
			top.fancyConfirm('Delete Record(s)! Are you sure ?','','top.fmain.ifrm_FolderContent.delPdfSplitFun("'+cntr+'",true)');
			var l = top.$(".messi").offset().left;
			var w =$("#list_pdf_div").outerWidth(true);
			var l_pos = parseInt(parseInt(l+w)-50);
			top.$(".messi").css({left: l_pos, position:'absolute'});
		}
		else{
			if(document.getElementById('delPdfSplit')) {
				top.show_loading_image('show','-60','Please Wait');
				document.getElementById('delPdfSplit').value='yes';
				document.frmPdfSplit.submit();
			}
		}
	}else {
		top.fAlert('Please select record(s) to delete');
		var l = top.$(".messi").offset().left;
		var w =$("#list_pdf_div").outerWidth(true);
		var l_pos = parseInt(parseInt(l+w)-50);
		top.$(".messi").css({left: l_pos, position:'absolute'});
	}
}
function setPtInfoAutoFill(objNumber,obj){
	var strString=obj.value;
	if(strString!=""){
		var strArray=strString.split("-");
		if(document.getElementById("hidd_ptNmeListId"+objNumber)) {
			document.getElementById("hidd_ptNmeListId"+objNumber).value='';
			if(document.getElementById("hidd_ptNmeListId"+objNumber) && strArray[1] && strArray[2]){
				document.getElementById("hidd_ptNmeListId"+objNumber).value=strArray[1]+'-'+strArray[2];
			}
		}	
		if(document.getElementById("ptNmeListId"+objNumber) && strArray[0]){
			document.getElementById("ptNmeListId"+objNumber).value=strArray[0];
		}
	}
}	
function dispHidOptTxtBox(ObjTdPtnameOptBoxId,ObjTdPtnameTxtBoxId,objPtnameOptId,objPtNmeListId,objHiddPtNmeListId) {
	if(objPtnameOptId) 		{ objPtnameOptId.value='';}
	if(objPtNmeListId) 		{ objPtNmeListId.value='';}
	if(objHiddPtNmeListId) 	{ objHiddPtNmeListId.value='';}
	
	if(ObjTdPtnameOptBoxId && ObjTdPtnameTxtBoxId) {
		
		if(ObjTdPtnameOptBoxId.style.display=='block') {
			ObjTdPtnameOptBoxId.style.display='none';
			ObjTdPtnameTxtBoxId.style.display='block';
		}else if(ObjTdPtnameTxtBoxId.style.display=='block') {
			ObjTdPtnameTxtBoxId.style.display='none';
			ObjTdPtnameOptBoxId.style.display='block';
		}
	}	
}
function KeyCheckSplit(evt,searchType,obj) {
	evt = (evt) ? evt : ((event) ? event : null);
	var evver = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null );
	var keynumber = evt.keyCode;
	var objVal=obj.value;
	var objValArr = objVal.split('-')
	var val1 = objValArr[0];
	var val2 = objValArr[1];
	var boolVal = false;
	if(keynumber==13){
		if(val1!='' && val2!=='') {
			if(!isNaN(val1) && !isNaN(val2)) {
				obj.blur();
			}else {
				boolVal=true;	
			}
		}else {
			boolVal=true;	
		}
		if(boolVal==true) {
			if(searchType == 'searchPdfPatient') {
				if(obj) {
					getPdfPtSearch(obj);
				}
			}
		}
	}
}
function getPdfPtSearch(obj) {
	if(obj) {
		var objId = obj.id;
		var txtSearch = obj.value; 
		var height=500;
		var web_root = '<?php echo $GLOBALS['webroot'];?>';
		cPtFldObj = objId;
		window.open( web_root+ "/interface/scheduler/search_patient_popup.php?btn_enter=Active&btn_sub="+txtSearch+"&call_from=pdfSplitter","pdfPatientSearchWindow","width=800,height="+height+",top=90,left=10,scrollbars=yes");
	}
}


function setpatient(pid,fname,mname,lname,suffix,ph,pm,pb,ps,pd,pstreet,pcity,pstate,pzip,ttl,tmp,p1,p2,p3,p4,sel_follow,sel_follow_val,c1,c2,c3)
{
	if( cPtFldObj )
	{
		var hidd_objId = 'hidd_'+cPtFldObj;
		var ptNme = lname+', '+fname+' - '+pid;
		
		if(document.getElementById(cPtFldObj)) {
			document.getElementById(cPtFldObj).value = ptNme;
		}
		if(document.getElementById(hidd_objId)) {
			document.getElementById(hidd_objId).value = pid;
		}	
	}
}

//START FUNCTION TO COPY PATIENT-ID AND WAITING-D IN HIDDEN FIELD WHEN COPY PATIENT-NAME FROM ONE TEXTBOX TO OTHER
function copyPdfFun(chkCountr,obj,obj_hidd_ptNmeListId,objFolder) {
	var cpArr = new Array();
	var hidd_cpArr = new Array();
	var ptIdFolderIdArr = new Array();
	var cp=hidd_cp='';
	var objId = obj.id;
	var objVal = obj.value;
	if(objVal.search(' - ')>=0) { 
		if(chkCountr) {
			for(var i=1;i<=chkCountr;i++) {
				if(document.getElementById('ptNmeListId'+i)) {
					if(objId!='ptNmeListId'+i) {
						if(document.getElementById('ptNmeListId'+i).value) {
							cp		= cp+'~'+document.getElementById('ptNmeListId'+i).value;
							hidd_cp	= hidd_cp+'~'+document.getElementById('hidd_ptNmeListId'+i).value;
						}
					}
				}
			}
			if(cp) {
				cpArr 		= cp.split('~');
				hidd_cpArr 	= hidd_cp.split('~');
				for(var j=0;j<cpArr.length;j++) {
					if(trim(obj.value)==trim(cpArr[j])) {
						if(document.getElementById("hidd_"+objId)){
							document.getElementById("hidd_"+objId).value=hidd_cpArr[j];
						}	
					}
				}
			}
		}
	}else if(objVal.search('-')>=0) { 
		ptIdFolderIdArr = objVal.split('-');
		ptId			= parseFloat(trim(ptIdFolderIdArr[0]));
		folderId		= parseFloat(trim(ptIdFolderIdArr[1]));
		if(!isNaN(ptId) && !isNaN(folderId)) {
			obj_hidd_ptNmeListId.value=ptId;
			ptname_ajax_url = 'pdf_split_search_patient.php?getPtInfoAjax=yes&ptId='+ptId;
			$.ajax({
				url: ptname_ajax_url,
				success: function(respRes){
					//alert(respRes);
					if(respRes == 'patient not exist') {
						top.fAlert("Please select correct patient id");
						obj.value='';	
						obj_hidd_ptNmeListId.value='';
					}else {
						obj.value = respRes;	
					}
				}
			});		
		}
	}else if(!isNaN(objVal)) { 
		obj_hidd_ptNmeListId.value=objVal;
		ptname_ajax_url = 'pdf_split_search_patient.php?getPtInfoAjax=yes&ptId='+objVal;
		$.ajax({
			url: ptname_ajax_url,
			success: function(respRes){
				//alert(respRes);
				if(respRes == 'patient not exist') {
					top.fAlert("Please select correct patient id");
					obj.value='';	
					obj_hidd_ptNmeListId.value='';
				}else {
					obj.value = respRes;	
				}
			}
		});		
	}
}
//END FUNCTION TO COPY PATIENT-ID IN HIDDEN FIELD WHEN COPY PATIENT-NAME FROM ONE TEXTBOX TO OTHER

function reload_frm() {
	top.show_loading_image("show","-60","Please Wait");
	top.fmain.ifrm_FolderContent.window.location.reload(true);
}

function selectDefaultFolder() {
	var defaul_folder_id = '';
	if(document.getElementById('sel_defaul_folder')) {
		defaul_folder_id = document.getElementById('sel_defaul_folder').value; 	
	}
	if(defaul_folder_id) {
		obj = document.getElementsByName("folderNmeList[]");
		objLength = document.getElementsByName("folderNmeList[]").length;
		for(i=0; i<objLength; i++){
			if(obj[i]) {
				obj[i].value = defaul_folder_id;
			}
		}
	}
}

function init()
{
	window.focus();
	$(".selectpicker").selectpicker();
	var parWidth = (screen.availWidth > 1300) ? 1300 : screen.availWidth;
	var parHeight = browser_name == 'msie' ? 720 : 750;
	window.resizeTo(parWidth,parHeight);
	var t = 20;
	var l = parseInt((screen.availWidth - window.outerWidth) / 2)
	window.moveTo(l,t);
}


function chk_all(_this)
{
	var c = ($(_this).is(':checked')) ? true : false;
	$('[id^="chkbxPdf"]').prop('checked',c);
}

// Scan/Upload Options 
var action = '<?php echo $action;?>';
var browser_name = '<?php echo $browser['name'];?>';
var web_root = '<?php echo $GLOBALS['php_server'];?>';

if(action == 'scan')
{
	var multiScan = 'yes';
	var no_of_scans = 100;
	var upload_scan_url = web_root + '/interface/chart_notes/pdf_split_upload_file.php?imwemr=<?php echo session_id();?>&method=scan';
}
else
{
	var upload_url = web_root + '/interface/chart_notes/pdf_split.php?imwemr=<?php echo session_id();?>&method=upload';	
}

init();

</script>
</head>
<?php




?>
<body>
	<div class="panel panel-primary">
  	<div class="panel-heading">Split PDF Files Using Upload/Scan
    	<span class="pull-right" style="margin-top:-7px;">
      	<select name="sel_defaul_folder" id="sel_defaul_folder" class="selectpicker" style="color:#333" onChange="selectDefaultFolder();" data-size="10" data-style="btn-warning">
        	<option value="">Select Folder</option>
          <?php
						if($dia_num>0){
							foreach($catArr as $key => $val){
								if($key == $parent_cat1) {
									echo '<option value="'.$key.'" selected><b>'.$val.'</b></option>';
								}else {
									echo '<option value="'.$key.'" ><b>'.$val.'</b></option>';
								}
							}
						}
					?>
       	</select>	
      </span>
    </div><!--max-height:575px; height:575px; overflow:hidden-->
    <div id="pdf_split_main_div" class="panel-body popup-panel-body" style="overflow:hidden">
    	<div class="col-xs-12 col-sm-7" style="max-height:100%; height:100%; overflow:hidden; overflow-y:auto;">
      	<?php
          
          if( $action == 'upload')
          {
						include_once $scanUploadSrc;	
          }
          else
          {
            if($browser['name'] == 'msie' || $browser['name'] != "chrome" )
            { 
              include_once $scanUploadSrc;
            }
						else {
							$scanUploadSrc = $GLOBALS['fileroot']."/library/scanc/scan_control.php";
							include_once $scanUploadSrc;
						}
					}
          
        ?>
        
      </div>
      
      <div id="list_pdf_div" class="col-xs-12 col-sm-5" style="max-height:100%; height:100%; overflow:hidden; overflow-y:auto;">
  
        <form name="frmPdfSplit" method="post" action="pdf_split.php" enctype="multipart/form-data" >
          <input type="hidden" name="selDos" value="<?php echo $selDos;?>">
          <input type="hidden" name="savePdfSplit" id="savePdfSplit" value="yes">
          <input type="hidden" name="delPdfSplit" id="delPdfSplit" value="">
                        
          <?php  
        
            if ($handle = @opendir($file_path))
            {
              //START CODE TO GET COUNT OF ALL UPDALOED FILES
              $chkHandle = @opendir($file_path);
              $chkCountr = 0;
              while (false !== ($chkFile = readdir($chkHandle))) {
								//|| $chkFile == "." || $chkFile == ".."
								if( is_dir($chkFile) || !(stripos($chkFile,'.pdf')) ) continue;
                else { ++$chkCountr; }
              }
              //echo '<br>Count is '.$chkCountr;
              //END CODE TO GET COUNT OF ALL UPLOADED FILES
          ?>
              
                <table class="table table-bordered table-hover table-striped scroll release-table">
                  <thead class="grythead">
                    <tr>
                      <th width="10%">
                      	<div class="checkbox text-center">
                        	<input type="checkbox" id="chkAll" onChange="chk_all(this);">
                        	<label for="chkAll"></label>
                       	</div>
                      </th>
                      <th width="30%">PDF Name</th>
                      <th width="30%">Patient Name - ID</th>
                      <th width="30%">Folder Name</th>
                    </tr>
                  </thead>
                  <tbody>
										<?php 
                      $counter=1;
                      /* This is the correct way to loop over the directory. */
                      $arrFiles = array();
                      while (false !== ($file = readdir($handle))) {
                        $arrFiles[] = $file;
                      }
                      natsort($arrFiles);
                      
                      foreach($arrFiles as $file)
                      {	
                        if ($file != "." && $file != ".." && $file != "tmp" && stristr($file,".pdf") )
                        {
                          $barCodeTmp = exec("zbarimg -q ".$file_path.'/'.$file);
                          $zBarArr = explode(":",$barCodeTmp);
                          $barCodePtId='';
                          $parent_cat1='';
                          if( count($zBarArr) > 0 )
                          {
                            $barCode = $zBarArr[1];
                            $barCodeArr = explode("-",$barCode);
                            $barCodePtId = intval($barCodeArr[0]);
                            $barCodePtNameId = $ptNameArr[$barCodePtId];
                            if($barCodePtId=='0' || !$barCodePtId) {
                              $barCodePtNameId = '';		
                            }
                            $folderIdTemp = $barCodeArr[1]; 
                            if($folderIdTemp[0]=='1')
                            {
                              $folderId = intval(substr($folderIdTemp,1));	
                              $folderName = strtolower($pckNameArr[$folderId]);
                              if(in_array($folderName,$pckFolderNameArr)) {
                                $parent_cat1 = $pckFolderIdArr[$folderName];	
                              }
                            }
                            else
                            {
                              $folderId = intval($folderIdTemp);	
                              $folderName = strtolower($categoryNameArr[$folderId]);
                              if(in_array($folderName,$signedFolderNameArr)) {
                                $parent_cat1 = $signedFolderIdArr[$folderName];	
                              }
                            }
                          }
                  	?>
                        <tr>
                          <td class="text-center" width="5%">
                            <input type="hidden" name="hidd_pdfFileNme[]" value="<?php echo $file;?>" />
                            <div class="checkbox">
                              <input type="checkbox" name="chkbxPdf<?php echo($counter);?>" id="chkbxPdf<?php echo $counter;?>" value="yes">
                              <label for="chkbxPdf<?php echo $counter;?>"></label>
                            </div>
                          </td>
                          <td>
                            <div class="input-group">
                              <label class="input-group-addon pointer pd2">
                                <img src="<?php echo $GLOBALS['webroot'];?>/library/images/pdf_small.png" alt="<?php echo $file;?>" onClick="window.open('<?php echo $file_path_web.'/'.$file;?>')" style="max-width:none;" width="15">
                              </label>
                              <input class="form-control" type="text" name="pdfFileNme[]" value="<?php echo $file;?>">
                            </div>
                          </td>
                          <td id="TdPtnameTxtBoxId<?php echo $counter;?>">
                            <input type="text" class="form-control" value="<?php echo $barCodePtNameId;?>" name="ptNmeListId<?php echo $counter;?>" id="ptNmeListId<?php echo $counter;?>" onBlur="copyPdfFun('<?php echo $chkCountr;?>',this,document.getElementById('hidd_ptNmeListId<?php echo $counter;?>'),document.getElementById('folderNmeListId<?php echo $counter;?>'));" onKeyPress="KeyCheckSplit(event,'searchPdfPatient',this);">
                            <input type="hidden" name="hidd_ptNmeListId<?php echo $counter;?>" id="hidd_ptNmeListId<?php echo $counter;?>" onChange="javascript: setPtInfoAutoFill('<?php echo($counter);?>',this);" value="<?php echo $barCodePtId;?>">
                          </td>
                          
                          <td>
                            <select name="folderNmeList[]" id="folderNmeListId<?php echo $counter;?>" class="minimal" style="width:100%" >
                              <option value="">Select Folder</option>
                              <?php
                                if($dia_num>0){
                                  foreach($catArr as $key => $val){
                                    if($key == $parent_cat1) {
                                      echo "<option value=$key selected><b>$val</b></option>";
                                    }else {
                                      echo "<option value=$key><b>$val</b></option>";
                                    }
                                  }
                                }
                              ?>
                            </select>
                          </td>
                          
                        </tr>	
                <?php 
                        $counter++;
                      }	
                    }
                  ?>
                    <tr>
                      <td colspan="5">
                        <input type="hidden" name="counterPdf" id="counterPdf" value="<?php echo($counter);?>">
                        <input type="hidden" name="counterPdfMinusOne" id="counterPdfMinusOne" value="<?php echo ($counter-1);?>">
                        <?php
												
												?>
                      </td>
                    </tr>
                  </tbody>
                </table>
              
          <?php
              closedir($handle);
            }
          
          ?>	
        </form>
  
  		</div>

			<script>
        selectDefaultFolder();
        top.show_loading_image("hide");

		var dh_split = $( document ).height()-45;
		$("#pdf_split_main_div").height(dh_split+'px');
		$('#pdf_split_main_div').css('max-height', dh_split+'px');
		 
		if(top.document.getElementById("scanUploadBtnPdf")) {
			top.document.getElementById("scanUploadBtnPdf").value = "<?php echo $action_btn_title;?>";	
		}
		var chkCountr = "<?php echo $chkCountr;?>";
		if(parseInt(chkCountr)>0) {
			top.$("#savePdfBtn").show();
			top.$("#deletePdfSplitSelected").show();	
		}else {
			top.$("#savePdfBtn").hide();
			top.$("#deletePdfSplitSelected").hide();	
		}
      </script>
		</div>
    
   <!-- <footer class="panel-footer">
        <input type="button" class="btn btn-info" id="scanUploadBtnPdf" name="scanUploadBtnPdf" value="<?php echo $action_btn_title;?>" onClick="javascript:top.fmain.ifrm_FolderContent.scanUploadFun();" >
        <input type="button" class="btn btn-primary" id="refreshBtnPdf" name="refreshBtnPdf" value="Refresh" onClick="javascript:top.fmain.ifrm_FolderContent.reload_frm();" >
        <input type="button" class="btn btn-danger" id="CloseBtnPdf" name="CloseBtnPdf" value="Close" onClick="javascript:top.window.close();" >
    <span id="span_splitt_button" class="<?php echo ($chkCountr > 0 ? '' : 'hidden');?>" >
        <input type="button" class="btn btn-success" id="savePdfBtn" name="savePdfBtn" value="Save" onClick="javascript:top.show_loading_image('show','-60','Please Wait');top.fmain.ifrm_FolderContent.submit_frm();" >
        <input type="button" class="btn btn-warning" id="deletePdfSplitSelected" name="deletePdfSplitSelected" value="Delete" onClick="javascript:top.fmain.ifrm_FolderContent.delPdfSplitFun(document.getElementById('counterPdfMinusOne').value);" >
    </span>
    </footer>-->
    
	</div>    	      
	<?php
		if($msgRec!=''){
			echo '<script>top.alert_notification_show("'.$msgRec.'");</script>';
		}
	?>
</body>
</html>