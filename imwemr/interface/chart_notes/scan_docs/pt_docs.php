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
include_once(dirname(__FILE__)."/../../../config/globals.php");
require_once(dirname(__FILE__)."/../../../library/patient_must_loaded.php");
$library_path = $GLOBALS['webroot'].'/library';
$pg_title = 'Pt. Docs';
$blClientBrowserIpad = false;
if(stristr($_SERVER['HTTP_USER_AGENT'], 'ipad') == true) {
	$blClientBrowserIpad = true;
}

$consentScroll=' scrolling="no" ';
if($blClientBrowserIpad == true){
	$consentScroll = ' scrolling="yes" ';	
}

$browserIpad = 'no';	
$ptDocTargetArea = 'ifrm_FolderContent';
$loadingImage = 'inline';
if(stristr($_SERVER['HTTP_USER_AGENT'], 'ipad') == true) {
	$browserIpad = 'yes';
	$ptDocTargetArea = '_blank';
	$loadingImage = 'none';
}
//$objManageData->Smarty->assign("ptDocTargetArea", $ptDocTargetArea);
//$objManageData->Smarty->assign("loadingImage", $loadingImage);

//--- GET ALL PT DOCS TEMPLATE CATEGORY DETAILS ----
$pt_doc_cat_qry = "select cat_id, category_name, delete_status from main_template_category where template_name = 'pt_docs'";
$pt_doc_cat_qry_res = get_array_records_query($pt_doc_cat_qry);
$pt_docs_category_arr = array();
$Unsaved_pt_docs_category_arr = array();
$deleted_pt_docs_category_arr = array();
for($i=0;$i<count($pt_doc_cat_qry_res);$i++){
	$cat_id = $pt_doc_cat_qry_res[$i]['cat_id'];
	$category_name = $pt_doc_cat_qry_res[$i]['category_name'];
	$pt_docs_category_arr[$cat_id] = $category_name;
	if($pt_doc_cat_qry_res[$i]['delete_status'] == '0'){
	  $Unsaved_pt_docs_category_arr[$cat_id] = $category_name; //Unsaved template category
	}
	
	if($pt_doc_cat_qry_res[$i]['delete_status'] == '1'){
	  $deleted_pt_docs_category_arr[$cat_id] = $category_name; //Deleted template category
	}
}

//--- CHANGE PT DOCS AND COLLECTION LETTERS STATUS AS DELETED ---
if($mode == 'delete' and empty($temp_id) === FALSE){
	$delQry=$AndUpdtQry="";
	
	if($temp_id=='collection'){ //FOR COLLECTION LETTERS
		if($ptdoc_id){ 
			$delQry = "Update pt_docs_collection_letters set delete_status = '1'
						where id = '$ptdoc_id'";
		}
	}else{	//FOR PT DOCS
		if($ptdoc_id) {  $AndUpdtQry = " AND pt_docs_patient_templates_id = '$ptdoc_id' ";}
		$delQry = "update pt_docs_patient_templates set delete_status = '1'
					where pt_doc_primary_template_id = '$temp_id' 
					$AndUpdtQry
					";
	}
	if($delQry){
		imw_query($delQry);
	}
}

/*Mark Fax Deleted*/
$fax_id = (isset($_REQUEST['fax_id']))?(int)$_REQUEST['fax_id']:0;
if($mode==='deleteFax' && $fax_id !== 0){
	$sqlDel = 'UPDATE `inbound_fax` SET `del_status`=1, `del_by`='.((int)$_SESSION['authId']).', `del_at`=\''.date('Y-m-d H:i:s').'\' WHERE `id`='.$fax_id;
	imw_query($sqlDel);
}

/*Mark Fax Pending*/
$fax_id = (isset($_REQUEST['fax_id']))?(int)$_REQUEST['fax_id']:0;
if($mode==='restoreFax' && $fax_id !== 0){
	$sqlDel = 'UPDATE `inbound_fax` SET `patient_id`=0, `pending_by`='.((int)$_SESSION['authId']).', `moved_pending_at`=\''.date('Y-m-d H:i:s').'\', `fax_folder`=\'\' WHERE `id`='.$fax_id;
	imw_query($sqlDel);
}

/*Undelete Fax*/
$fax_id = (isset($_REQUEST['fax_id']))?(int)$_REQUEST['fax_id']:0;
if($mode==='undelFax' && $fax_id !== 0){
	$sqlDel = 'UPDATE `inbound_fax` SET `del_status`=0 WHERE `id`='.$fax_id;
	imw_query($sqlDel);
}

//--- GET SAVED PT DOCS TEMPLATE NAME ----
$patient_id = $_SESSION['patient'];

$ptDocSaveTmpQry = "select pd.pt_doc_primary_template_id, pd.pt_docs_patient_templates_id,
				DATE_FORMAT(pd.created_date,'".get_sql_date_format()."') as temp_created_date,
				DATE_FORMAT(pd.created_date, '".get_sql_date_format('','Y','-')." %h:%i %p') AS pt_docs_date_time, 
				CONCAT(u.lname,', ',u.fname,' ',u.mname) AS operator_name,
				pdt.pt_docs_template_name, pdt.pt_docs_template_category_id
				FROM pt_docs_patient_templates pd 
				JOIN pt_docs_template pdt ON(pdt.pt_docs_template_id = pd.pt_doc_primary_template_id)
				LEFT JOIN users u ON(u.id = pd.operator_id)
				WHERE pd.patient_id = '$patient_id' 
				AND pd.delete_status = '0'
				ORDER BY pd.pt_docs_patient_templates_id DESC";
$ptDocSaveTmpQryRes = get_array_records_query($ptDocSaveTmpQry);
$savedTemplateArr = array();
$saveTepmlIdArr = array();
for($i=0;$i<count($ptDocSaveTmpQryRes);$i++){
	$temp_created_date = $ptDocSaveTmpQryRes[$i]['temp_created_date'];
	//--- CATEGORY NAME ----
	$pt_docs_template_category_id = $ptDocSaveTmpQryRes[$i]['pt_docs_template_category_id'];
	$cat_name = $pt_docs_category_arr[$pt_docs_template_category_id];
	
	$savedTemplateArr[$cat_name][$temp_created_date][] = $ptDocSaveTmpQryRes[$i];
	$saveTepmlIdArr[] = $ptDocSaveTmpQryRes[$i]['pt_doc_primary_template_id'];
}
$saveTepmlIdStr = join(',',$saveTepmlIdArr);
ksort($savedTemplateArr);
//$objManageData->Smarty->assign('savedTemplateArr', $savedTemplateArr);

//GET SAVED COLLECTION LETTERS
$savedCollectionArr=array();
$qry="SELECT pd.id, pd.sent_to, date_format(pd.created_date,'".get_sql_date_format()."') as created_date, 
		clt.collection_name,
		DATE_FORMAT(pd.created_date, '".get_sql_date_format('','Y','-')." %h:%i %p') AS pt_docs_date_time, 
		CONCAT(u.lname,', ',u.fname,' ',u.mname) AS operator_name
		FROM pt_docs_collection_letters pd
		JOIN collection_letter_template clt ON (clt.id= pd.template_id)
		LEFT JOIN users u ON(u.id = pd.operator_id)
		WHERE pd.patient_id='".$patient_id."' 
		AND pd.delete_status = '0' 
		ORDER BY pd.created_date DESC";
$rs=imw_query($qry);
while($res=imw_fetch_assoc($rs)){
	$created_date = $res['created_date'];
	if($res['sent_to']==1)$savedCollectionArr['Insurance'][$created_date][]= $res;
	else $savedCollectionArr['Patient'][$created_date][]= $res;
}
//$objManageData->Smarty->assign('savedCollectionArr', $savedCollectionArr);

//Get All insruance cards
/**/
$insCardData = [];
$qry = "SELECT inst.case_name, inc.ins_caseid, ins.type, ins.scan_card,ins.scan_card2 FROM insurance_data ins
							JOIN insurance_case inc ON (ins.ins_caseid= inc.ins_caseid and inc.del_status = 0  )
							JOIN insurance_case_types inst ON (inst.case_id = inc.ins_case_type)
							WHERE ins.pid=".(int)$_SESSION['patient']."
							AND inc.patient_id=".(int)$_SESSION['patient']." 
							AND (ins.scan_card <> '' OR ins.scan_card2 <> '')
							ORDER BY inc.ins_caseid Desc, ins.type Asc";
$sql = imw_query($qry) or die($qry.'=='.imw_error());

while($res = imw_fetch_assoc($sql) )
{
	$case = $res['case_name'].'-'.$res['ins_caseid'];
	$type = $res['type'];
	$scan_card1 = substr($res['scan_card'],1);
	$scan_card2 = substr($res['scan_card2'],1);

	$scanPath1 = data_path() . $scan_card1;
	$scanPath2 = data_path() . $scan_card2;
	//echo '<br>'.$type.'---'.$scan_card1 .'=='.file_exists($scanPath1);
	//echo '<br>'.$type.'---'.$scan_card2 .'=='.file_exists($scanPath1);
	$scanCard1 = $scan_card1 && file_exists($scanPath1) ? data_path(1).$scan_card1 : "";
	$scanCard2 = $scan_card2 && file_exists($scanPath2) ? data_path(1).$scan_card2 : "";

	if( $scanCard1 || $scanCard2 ) {

		if( !array_key_exists($case,$insCardData)) $insCardData[$case] = [];

		if( !array_key_exists($type,$insCardData[$case])) $insCardData[$case][$type] = [];

		if( $scanCard1) $insCardData[$case][$type][] = array('path' => $scanCard1 , 'name' => end(explode("/",$scanCard1)) );

		if( $scanCard2) $insCardData[$case][$type][] = array('path' => $scanCard2 , 'name' => end(explode("/",$scanCard2)) );
	}

}

//Interpretation 
$interpretations = [];
//$interpretations["2019"] = array('path' => "/test/test/test.jpg" , 'name' => "STEST" );
//$interpretations["2018"] = array('path' => "/test/test/test1.jpg" , 'name' => "STEST1" );
$qry = "SELECT 
		c1.patient_id,c1.form_id,c1.exam_name,
		c2.id,
		c2.order_by,
		c2.order_on,
		c2.test_type,
		c2.assessment,
		c2.dx, c2.dxid,
		c2.plan,
		c3.drawing_image_path
		  FROM chart_drawings c1 
				INNER JOIN chart_draw_inter_report c2 ON c1.id = c2.id_chart_draw
				INNER JOIN ".constant("IMEDIC_SCAN_DB").".idoc_drawing c3 ON c3.id = c1.idoc_drawing_id
				WHERE c1.patient_id = '".$patient_id."' AND c1.purged='0'
				AND c1.exam_name='FundusExam' AND c2.del_by='0'
				ORDER BY c2.order_on DESC
			";
$interArr = get_array_records_query($qry);
$inter_data_arr = array();
$inter_data_len = count($interArr);
for($i=0;$i<$inter_data_len;$i++){
	$order_on = $interArr[$i]["order_on"];
	$drawing_image_path = $interArr[$i]["drawing_image_path"];
	$interpretations[$order_on] = array('path' => $drawing_image_path , 'name' => $order_on, 'id' => $interArr[$i]["id"], 'pid' => $interArr[$i]["patient_id"], 'fid' => $interArr[$i]["form_id"], 'exam_name' => $interArr[$i]["exam_name"] );
}

//--- GET UN SAVED PT DOCS TEMPLATE NAME ----				
$ptDocTmpQry = "select pt_docs_template_id,  pt_docs_template_name, pt_docs_template_category_id
				from pt_docs_template where pt_docs_template_status = '0' group by pt_docs_template_id order by pt_docs_template_name";
$unsavedTemplateArr = get_array_records_query($ptDocTmpQry);
$template_data_arr = array();
for($i=0;$i<count($unsavedTemplateArr);$i++){
	$pt_docs_template_category_id = $unsavedTemplateArr[$i]['pt_docs_template_category_id'];
	$cat_name = $Unsaved_pt_docs_category_arr[$pt_docs_template_category_id];
	if($deleted_pt_docs_category_arr[$pt_docs_template_category_id]) { continue; }
	if( $cat_name) $template_data_arr[$cat_name][] = $unsavedTemplateArr[$i];
}
ksort($template_data_arr);
$unsavedTemplateArr = $template_data_arr;
//$objManageData->Smarty->assign('unsavedTemplateArr', $template_data_arr);

//--- SET MAIN CSS FILES ---
//$objManageData->Smarty->assign('css_patient', $css_patient);
//$objManageData->Smarty->assign('css_header', $css_header);

//--- SET IFRAME HEIGHT ----
//$objManageData->Smarty->assign('frame_height', $_SESSION['wn_height'] - 182);

//--- CHANGE ORDER STATUS AS DELETED ---
if($mode == 'delete' and empty($print_orders_data_id) === FALSE){
	$delQry = "update print_orders_data set delete_status = '1'
				where print_orders_data_id = '$print_orders_data_id'";
	imw_query($delQry);
}

//--- GET PATIENT ORDER SET PRINTS ----
$orderQry ="SELECT po.order_file_content, DATE_FORMAT(po.created_date,'".get_sql_date_format()."') as created_date,po.print_orders_data_id,  
			DATE_FORMAT(po.created_date, '".get_sql_date_format('','Y','-')."') AS pt_docs_date_time, 
			CONCAT(u.lname,', ',u.fname,' ',u.mname) AS operator_name
			FROM print_orders_data po
			LEFT JOIN users u ON(u.id = po.created_by)
			WHERE po.patient_id = '$patient_id'
			AND po.delete_status = '0'";
$orderQryRes = get_array_records_query($orderQry);
$orderTreeData = array();
for($i=0;$i<count($orderQryRes);$i++){
	$created_date = $orderQryRes[$i]['created_date'];
	$orderTreeData[$created_date][] = $orderQryRes[$i];
}

//$objManageData->Smarty->assign("orderTreeData", $orderTreeData);


/*
	
	Purpose: Display CCDA document for the patient
*/
if($mode=="delete_ccda" && empty($ccda_doc_id) === FALSE){
	imw_query("UPDATE `ccda_docs` SET `deletedBy`='".$_SESSION['authUserID']."' WHERE `id`='".$ccda_doc_id."'");
}
$sql = "SELECT cd.id, cd.file_path, 
		DATE_FORMAT(cd.time, '".get_sql_date_format('','Y','-')." %h:%i %p') AS pt_docs_date_time, 
		concat(u.lname,', ',u.fname,' ',u.mname) AS operator_name
		FROM `ccda_docs` cd 
		LEFT JOIN users u ON(u.id = cd.operator_id)
		WHERE `patient_id`='".$patient_id."' AND `deletedBy`=0";
$docs = imw_query($sql);
if($docs && imw_num_rows($docs)>0){
	$documents = array();
	while($row = imw_fetch_assoc($docs)){
		$name 										= explode("/",$row['file_path']);
		$name 										= end($name);
		$documents[$row['id']]['name'] 				= trim($name,".zip");
		$documents[$row['id']]['path'] 				= $row['file_path'];
		$documents[$row['id']]['operator_name'] 	= $row['operator_name'];
		$documents[$row['id']]['pt_docs_date_time'] = $row['pt_docs_date_time'];
	}
//	$objManageData->Smarty->assign("ccda_docs", $documents);
}
/*End Modification by Pankaj*/

/**
 * Inbound Fax docuemnts assigned to the patient
 */
$faxDocs = '';
if( is_updox('fax') ){
	$qry = "SELECT id, from_number, files, message, DATE_FORMAT(received_at, '%m-%d-%Y') AS 'date_received', 
			DATE_FORMAT(received_at, '%h:%i %p') AS 'time_received', 
			DATE_FORMAT(received_at, '".get_sql_date_format('','Y','-')." %h:%i %p') AS pt_docs_date_time  
			FROM inbound_fax 
			WHERE patient_id=".$patient_id." 
			AND del_status=0 
			AND fax_folder='pt_docs' 
			ORDER BY received_at ASC";
	$faxes = imw_query($qry);
	if($faxes && imw_num_rows($faxes)>0){
		$faxDocs = array();
		while($fax = imw_fetch_assoc($faxes)){
			$data 						= array();
			$data['id'] 				= $fax['id'];
			$data['from'] 				= core_phone_format($fax['from_number']);
			$data['link'] 				= data_path(1).'fax_files/'.$fax['files'];
			$data['files'] 				= $fax['files'];
			$data['time'] 				= $fax['time_received'];
			$data['pt_docs_date_time'] 	= $fax['pt_docs_date_time'];
			
			if(!isset($faxDocs[$fax['date_received']]))
				$faxDocs[$fax['date_received']] = array();
				
			array_push($faxDocs[$fax['date_received']], $data);
		}
	}
}
//$objManageData->Smarty->assign("faxDocs", $faxDocs);
 /**
 * End Inbound Fax docuemnts assigned to the patient
 */
 
/**
 * Outgoing Fax docuemnts assigned to the patient
 */
$faxSent = '';
$qry = "SELECT sf.id, sf.template_name, sf.updox_id, sf.updox_status, sf.file_name, sf.operator_id, 
		date_format(sf.cur_date_time, '%m-%d-%Y') AS 'folder_date', sf.fax_number, LOWER(updox_status) AS 'delivery_status',  
		DATE_FORMAT(sf.cur_date_time, '".get_sql_date_format('','Y','-')." %h:%i %p') AS pt_docs_date_time,
		concat(u.lname,', ',u.fname,' ',u.mname) AS operator_name
		FROM send_fax_log_tbl sf 
		LEFT JOIN users u ON(u.id = sf.operator_id)
		WHERE sf.patient_id=".$patient_id." AND sf.status=0 AND sf.section_name='complete_patient_record' 
		ORDER BY date_format(sf.cur_date_time, '%m-%d-%Y') DESC";
$faxes = imw_query($qry);
if($faxes && imw_num_rows($faxes)>0){
	$faxSent = array();
	while($fax = imw_fetch_assoc($faxes)){
		$data = array();
		$data['id'] 				= $fax['id'];
		$data['link'] 				= $GLOBALS['webroot'].'/data/'.constant('PRACTICE_PATH').'/PatientId_'.$patient_id.'/fax_log/'.$fax['file_name'];
		$receiving_fax_no 			= explode(',', $fax['fax_number']);
		$receiving_fax_no 			= array_map('core_phone_format', $receiving_fax_no);
		$receiving_fax_no 			= implode(', ', $receiving_fax_no);
		$data['fax_number']			= $receiving_fax_no;
		$data['files'] 				= ($fax['template_name']!=='')?$fax['template_name']:$receiving_fax_no;
		$data['date'] 				= $fax['folder_date'];
		$data['pt_docs_date_time']	= $fax['pt_docs_date_time'];
		$data['operator_name'] 		= $fax['operator_name'];
		
		$data['success'] = ($fax['delivery_status']==='success')? 'glyphicon-ok': 'outgoing-fax';
		
		if(!isset($faxSent[$fax['folder_date']]))
			$faxSent[$fax['folder_date']] = array();
			
		array_push($faxSent[$fax['folder_date']], $data);
	}
}
//$objManageData->Smarty->assign("faxSent", $faxSent);
 /**
 * End Inbound Fax docuemnts assigned to the patient
 */

 /**
 * Inbound Fax docuemnts assigned to the patient - Deleted
 */
$faxDocsDel = '';
if( is_updox('fax') ){
	$qry = "SELECT `id`, `from_number`, `files`, `message`, date_format(`received_at`, '%m-%d-%Y') AS 'date_received', date_format(`received_at`, '%h:%i %p') AS 'time_received' FROM `inbound_fax` WHERE `patient_id`=".$patient_id." AND `del_status`=1 AND `fax_folder`='pt_docs' ORDER BY `received_at` ASC";
	$faxes = imw_query($qry);
	if($faxes && imw_num_rows($faxes)>0){
		$faxDocsDel = array();
		while($fax = imw_fetch_assoc($faxes)){
			$data = array();
			$data['id'] = $fax['id'];
			$data['from'] = $fax['from_number'];
			$data['link'] = data_path(1).'fax_files/'.$fax['files'];
			$data['files'] = $fax['files'];
			$data['time'] = $fax['time_received'];
			
			if(!isset($faxDocsDel[$fax['date_received']]))
				$faxDocsDel[$fax['date_received']] = array();
				
			array_push($faxDocsDel[$fax['date_received']], $data);
		}
	}
}
//$objManageData->Smarty->assign("faxDocsDel", $faxDocsDel);
 /**
 * End Inbound Fax docuemnts assigned to the patient - Deleted
 */

//--- GET PT DOCS TEMPLATE ----
//$objManageData->Smarty->display(dirname(__FILE__)."/pt_docs.tpl");
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title><?php echo 'Pt. Docs :: imwemr ::';?></title>
            
        <!-- Bootstrap -->
        <link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
        <link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet">
        <link href="<?php echo $library_path; ?>/css/document.css" rel="stylesheet">
        <link href="<?php echo $library_path; ?>/css/bootstrap.min.css" rel="stylesheet" type="text/css">
        <link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet" type="text/css">
        <script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js"></script>
        <script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap.js"></script>
        <script src="<?php echo $library_path; ?>/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="<?php echo $library_path; ?>/js/mootools.js"></script>
        <script type="text/javascript" src="<?php echo $library_path; ?>/js/dg-filter.js"></script>
         
        <script>
			var wo=top.JS_WEB_ROOT_PATH;
			var doc_name = "<?php echo $_REQUEST["doc_name"];?>";
			function delDocs(doc_id,cnfrm){
				
				if (typeof(cnfrm)=="undefined") {
					top.fmain.frames['ifrm_FolderContent'].document.write('');
					top.fancyConfirm("Sure! you want to delete order ?","top.fmain.delDocs('"+doc_id+"',true)");
					return;
				}
				else{
					top.fmain.location.href = wo+"/interface/chart_notes/scan_docs/pt_docs.php?print_orders_data_id="+doc_id+"&mode=delete&doc_name="+doc_name;
				}	
			}
			
			function delFax(fax_id, cnfrm){
				if (typeof(cnfrm)=="undefined") {
					top.fmain.frames['ifrm_FolderContent'].document.write('');
					top.fancyConfirm("Sure! you want to delete the fax ?","top.fmain.delFax('"+fax_id+"',true)");
					return;
				}
				else{
					top.fmain.location.href = wo+"/interface/chart_notes/scan_docs/pt_docs.php?fax_id="+fax_id+"&mode=deleteFax&doc_name="+doc_name;
				}	
			}
			
			function restoreFax(fax_id, cnfrm){
				if (typeof(cnfrm)=="undefined") {
					top.fmain.frames['ifrm_FolderContent'].document.write('');
					top.fancyConfirm("Sure! you want to move the fax to pending ?","top.fmain.restoreFax('"+fax_id+"',true)");
					return;
				}
				else{
					top.fmain.location.href = wo+"/interface/chart_notes/scan_docs/pt_docs.php?fax_id="+fax_id+"&mode=restoreFax&doc_name="+doc_name;
				}	
			}
			
			function unDelFax(fax_id, cnfrm){
				if (typeof(cnfrm)=="undefined") {
					top.fmain.frames['ifrm_FolderContent'].document.write('');
					top.fancyConfirm("Sure! you want to restore the fax fot the selected patient ?","top.fmain.unDelFax('"+fax_id+"',true)");
					return;
				}
				else{
					top.fmain.location.href = wo+"/interface/chart_notes/scan_docs/pt_docs.php?fax_id="+fax_id+"&mode=undelFax&doc_name="+doc_name;
				}	
			}
			
			function delPtDocs(doc_id,ptdoc_id,cnfrm){
				if (typeof(cnfrm)=="undefined") {
					top.fmain.frames['ifrm_FolderContent'].document.write('');
					if(doc_id=='collection'){
						top.fancyConfirm("Sure! you want to delete collection letters ?","top.fmain.delPtDocs('collection','"+ptdoc_id+"',true)");
					}else{
						top.fancyConfirm("Sure! you want to delete patient docs ?","top.fmain.delPtDocs('"+doc_id+"','"+ptdoc_id+"',true)");
					}
					return;
				}
				else{
					top.fmain.location.href = wo+"/interface/chart_notes/scan_docs/pt_docs.php?temp_id="+doc_id+"&ptdoc_id="+ptdoc_id+"&mode=delete&doc_name="+doc_name;
				}	
			}
			
			function delCcda(ccda_doc_id,cnfrm){
				
				if (typeof(cnfrm)=="undefined") {
					top.fmain.frames['ifrm_FolderContent'].document.write('');
					top.fancyConfirm("Sure! you want to delete CCDA ?","top.fmain.delCcda('"+ccda_doc_id+"',true)");
					return;
				}
				else{
					top.fmain.location.href = wo+"/interface/chart_notes/scan_docs/pt_docs.php?ccda_doc_id="+ccda_doc_id+"&mode=delete_ccda&doc_name="+doc_name;
				}
			}		
		</script>
        
	</head>
    <body onUnload="top.btn_show('');">
  	<?php 
			$col_height = (int) ($_SESSION['wn_height'] - ($GLOBALS['gl_browser_name']=='ipad' ? 65 : 310)) ;
		?>
  	<div class="col-xs-12 bg-white">
    	<div class="row">
        <div class=" col-xs-2 " style="height:<?php echo ($col_height);?>px; max-height:100%; overflow:scroll">
				  <?php include_once('tree4pt_docs.php'); ?>
        </div>
        
        <div class="col-xs-10 ">
            <div class="row">
                <div class="well pd0 margin_0 nowrap" style="vertical-align:text-top;">
                <iframe name="ifrm_FolderContent" id="ifrm_FolderContent" <?php echo $consentScroll;?>  style="width:100%; height:<?php echo $col_height;?>px;" src="blank_pt_docs.php" frameborder="0"></iframe>
                </div>   
            </div>
        </div>
        
      </div>
    </div>
    <script>
			$(function(){
				$('[data-toggle="tooltip"]').tooltip({container:'body'});
			});	
	top.$('#acc_page_name').html('<?php echo $pg_title; ?>');
	</script>	
    </body>
</html>