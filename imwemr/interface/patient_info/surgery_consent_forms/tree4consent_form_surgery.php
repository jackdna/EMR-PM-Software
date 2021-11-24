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
require_once("../../../config/globals.php");
require_once("../../../library/patient_must_loaded.php");
require_once("../../../library/classes/dhtmlgoodies_tree.class.php");
$library_path = $GLOBALS['webroot'].'/library';

$patient_id = $_SESSION['patient'];
// Hold Action to perform
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

// Hold Consent || Document Id
$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
$id = (int) $id;

$return = array();

if( $id && $action == 'move_to_trash' )
{
	$qry = "UPDATE surgery_consent_filled_form SET movedToTrash = '1',
							modified_operator_id 		= '".$_SESSION['authId']."',
							modified_form_created_date 	= '".date('Y-m-d H:i:s')."' 
							WHERE surgery_consent_id 	= '".$id."'";
	$rsQry = imw_query($qry);
	$return['msg'] = ($rsQry ? 'Document moved successfully' : false);
	
}

else if( $id && $action == 'move_to_signed' )
{
	$qry = "UPDATE surgery_consent_filled_form SET movedToTrash = '0',
							modified_operator_id 		= '".$_SESSION['authId']."',
							modified_form_created_date 	= '".date('Y-m-d H:i:s')."' 
							WHERE surgery_consent_id 	= '".$id."'";
	$rsQry = imw_query($qry);
	$return['msg'] = ($rsQry ? 'Document moved successfully' : false);
}

else if( $id && $action == 'del_pt_doc' )
{
	$qry = "select scan_doc_add from surgery_center_patient_scan_docs where id = ". $id." ";
	$sql = imw_query($qry);	
	$row = imw_fetch_object($sql);
	$scanDocPath = $row->scan_doc_add;
	if($scanDocPath)
	{
		$scanDocPath = data_path().$scanDocPath;

		if(file_exists($scanDocPath)){
			unlink($scanDocPath);
			$qryDeleteScanDoc = "delete from surgery_center_patient_scan_docs where id = ". $id ." ";
			$rsDeleteScanDoc = imw_query($qryDeleteScanDoc);	
			$return['msg'] = ($rsDeleteScanDoc ? 'Scanned/uploaded document successfully deleted' : false);
		}
	}
}
$return['data'] = html_tree();

echo json_encode($return);
function html_tree()
{
	$patient_id = $_SESSION["patient"];
	$tree = new dhtmlgoodies_tree();
	include_once($GLOBALS['fileroot']."/interface/common/docs_name_header.php");
	if(!$p) { $p=1;}
	$p++;
	
	if($_REQUEST['doc_name'] == "surgery_consent_template")	{
		$p++;
		if(!$subTemplateCnt) {
			$subTemplateCnt = $p;
		}	
		$p++;
		//$a=$p;
		
		
		$tree->addToArray($p,"Surgery Consent Templates",$subTemplateCnt,"","","icon-folder","","","","","","","","","","","","","active");
		$zz = $p;
		//---- get consent forms -------
		$qry1 = "SELECT category_id,category_name from surgery_center_consent_category where category_status != 'true' order by category_id";
		$consentCatName = get_array_records_query($qry1);
		$c = $p;
		$d = 0;
		for($i=0;$i<count($consentCatName);$i++){
			$consentCategoryNameId = $consentCatName[$i]['category_id'];
			$consentCategoryName = trim(ucwords($consentCatName[$i]['category_name']));
			$c++;
			$d = $c;
		
			$qry2 = "select consent_id,consent_name from surgery_center_consent_forms_template where consent_delete_status != 'true' and consent_category_id  = '$consentCategoryNameId' order by consent_id";
			$consentDetail = get_array_records_query($qry2);
			$folderOnceDisplay=false;
			for($a=0;$a<count($consentDetail);$a++){
				$consentFormId = $consentDetail[$a]['consent_id'];					
				//START DISPLY FOLDER NAME
				if($consentDetail) {
					if($folderOnceDisplay==false) {
						$tree->addToArray($c,$consentCategoryName,$zz,"");
						$folderOnceDisplay=true;
					}
				}
				//END DISPLY FOLDER NAME
				$consentFormName = trim(ucwords(stripslashes($consentDetail[$a]['consent_name'])));
				$c++;
				$tree->addToArray($c,$consentFormName,$d,"consentFormDetailsSurgery.php?consent_form_id=$consentFormId","consent_data_surgery","glyphicon-open-file");
			}		
		}
		$c++;
		$p=$c;
		$tree->addToArray($c,"Patient Info",$zz,"","","icon-folder","scan-icon","surgery_consent_patient_scan.php?scanTypeFolder=0","consent_data_surgery");
		$patientifoNodeId = $c;
		
		$qryGetPatientInfoScan = "SELECT sd.id,sd.patient_id,sd.scan_doc_add,sd.mask,  
									DATE_FORMAT(sd.surgery_patient_scan_date, '".get_sql_date_format('','Y','-')." %h:%i %p') AS scan_upload_date_time,
									concat(u.lname,', ',u.fname,' ',u.mname) AS operator_name
									FROM surgery_center_patient_scan_docs sd 
									LEFT JOIN users u ON(u.id = sd.surgery_patient_scan_operator)
									WHERE sd.patient_id='".$patient_id."' and sd.scan_type_folder = '0' 
									ORDER BY sd.created_date DESC" ;
		$rsGetPatientInfoScan = get_array_records_query($qryGetPatientInfoScan);
		for($x=0;$x<count($rsGetPatientInfoScan);$x++){
			$patientifoNodeId++;
			$scanDocAdd = $rsGetPatientInfoScan[$x]['scan_doc_add'];
			$scanDocId = $rsGetPatientInfoScan[$x]['id'];
			$mask = $rsGetPatientInfoScan[$x]['mask'];
			$scanDocAdd = explode("/",$scanDocAdd);
		
			if($mask){
				$arrMask 	 = explode('.',$mask);
				$filename 	 = $arrMask[0];
			}
			else{
				$arrFilename = explode('.',$scanDocAdd[2]);
				$filename 	 = $arrFilename[0];
			}
			
			$showInfo		 = "yes";
			$scanUploadDateTime = $rsGetPatientInfoScan[$x]['scan_upload_date_time'];
			$operatorName 	 = stripslashes($rsGetPatientInfoScan[$x]['operator_name']);
			
			$tree->addToArray($patientifoNodeId,$filename,$c,"show_scan_doc.php?scanDocId=$scanDocId","consent_data_surgery","pdf-icon","remove-icon","javascript:top.fmain.left_panel('del_pt_doc',$scanDocId)","consent_data_surgery","Delete Patient Info Scan Document",true,"onMouseDown","divOpenClose(event,'".$patientifoNodeId."','".$scanDocId."');",$showInfo,$scanUploadDateTime,$operatorName,"","");
		}
		
		$patientifoNodeId++;
		$p=$patientifoNodeId;
		$tree->addToArray($patientifoNodeId,"Clinical",$zz,"","","icon-folder","scan-icon","surgery_consent_patient_scan.php?scanTypeFolder=1","consent_data_surgery");
		$clinicalNodeId = $patientifoNodeId;
	
		$qryGetPatientInfoScan = "SELECT sd.id,sd.patient_id,sd.scan_doc_add,sd.mask,  
									DATE_FORMAT(sd.surgery_patient_scan_date, '".get_sql_date_format('','Y','-')." %h:%i %p') AS scan_upload_date_time,
									concat(u.lname,', ',u.fname,' ',u.mname) AS operator_name
									FROM surgery_center_patient_scan_docs sd 
									LEFT JOIN users u ON(u.id = sd.surgery_patient_scan_operator)
									WHERE sd.patient_id='".$patient_id."' and sd.scan_type_folder = '1' 
									ORDER BY sd.created_date DESC" ;
		
		$rsGetPatientInfoScan = get_array_records_query($qryGetPatientInfoScan);	
		for($x=0;$x<count($rsGetPatientInfoScan);$x++){
			$clinicalNodeId++;
			$scanDocAdd = $rsGetPatientInfoScan[$x]['scan_doc_add'];
			$scanDocId = $rsGetPatientInfoScan[$x]['id'];
			$scanDocAdd = explode("/",$scanDocAdd);	
			$mask = $rsGetPatientInfoScan[$x]['mask'];
		
			if($mask){
				$arrMask = explode('.',$mask);
				$filename = $arrMask[0];
			}
			else{
				$arrFilename = explode('.',$scanDocAdd[2]);
				$filename = $arrFilename[0];
			}
			//tree4consent_form_surgery.php?deletePatientInfoDoc=$scanDocId
			$showInfo		 = "yes";
			$scanUploadDateTime = $rsGetPatientInfoScan[$x]['scan_upload_date_time'];
			$operatorName 	 = stripslashes($rsGetPatientInfoScan[$x]['operator_name']);
			
			$tree->addToArray($clinicalNodeId,$filename,$patientifoNodeId,"show_scan_doc.php?scanDocId=$scanDocId","consent_data_surgery","pdf-icon","remove-icon","javascript:top.fmain.left_panel('del_pt_doc',$scanDocId)","consent_data_surgery","Delete Clinical Scan Document",true,"onMouseDown","divOpenClose(event,'".$clinicalNodeId."','".$scanDocId."');",$showInfo,$scanUploadDateTime,$operatorName,"","");
		}
		
		$clinicalNodeId++;
		$p=$clinicalNodeId;
		$tree->addToArray($clinicalNodeId,"Health Questionnaire",$zz,"","","icon-folder","scan-icon","surgery_consent_patient_scan.php?scanTypeFolder=2","consent_data_surgery");
		$healthQuesNodeId = $clinicalNodeId;
		$healthQuesNodeId++;
		$p=$healthQuesNodeId;
		$tree->addToArray($healthQuesNodeId,"Pre-op Health Questionnaire",$clinicalNodeId,"pre_op_health_ques_surgery.php?patientId=$patient_id","consent_data_surgery","glyphicon-open-file");
		
		$qryGetPatientInfoScan = "SELECT sd.id,sd.patient_id,sd.scan_doc_add,sd.mask,  
									DATE_FORMAT(sd.surgery_patient_scan_date, '".get_sql_date_format('','Y','-')." %h:%i %p') AS scan_upload_date_time,
									concat(u.lname,', ',u.fname,' ',u.mname) AS operator_name
									FROM surgery_center_patient_scan_docs sd 
									LEFT JOIN users u ON(u.id = sd.surgery_patient_scan_operator)
									WHERE sd.patient_id='".$patient_id."' and sd.scan_type_folder = '2' 
									ORDER BY sd.created_date DESC" ;
	
		$rsGetPatientInfoScan = get_array_records_query($qryGetPatientInfoScan);	
		for($x=0;$x<count($rsGetPatientInfoScan);$x++){
			$healthQuesNodeId++;
			$scanDocAdd = $rsGetPatientInfoScan[$x]['scan_doc_add'];
			$scanDocId = $rsGetPatientInfoScan[$x]['id'];
			$scanDocAdd = explode("/",$scanDocAdd);	
			$mask = $rsGetPatientInfoScan[$x]['mask'];
		
			if($mask){
				$arrMask = explode('.',$mask);
				$filename = $arrMask[0];
			}
			else{
				$arrFilename = explode('.',$scanDocAdd[2]);
				$filename = $arrFilename[0];
			}
			
			$showInfo		 	= "yes";
			$scanUploadDateTime = $rsGetPatientInfoScan[$x]['scan_upload_date_time'];
			$operatorName 	 	= stripslashes($rsGetPatientInfoScan[$x]['operator_name']);
			$tree->addToArray($healthQuesNodeId,$filename,$clinicalNodeId,"show_scan_doc.php?scanDocId=$scanDocId","consent_data_surgery","pdf-icon","remove-icon","javascript:top.fmain.left_panel('del_pt_doc',$scanDocId)","consent_data_surgery","Delete Health Questionnaire Scan Document",true,"onMouseDown","divOpenClose(event,'".$healthQuesNodeId."','".$scanDocId."');",$showInfo,$scanUploadDateTime,$operatorName,"","");
		}
		
		$healthQuesNodeId++;
		
		//START OCULAR HX FOLDER
		$p=$healthQuesNodeId;
		$tree->addToArray($healthQuesNodeId,"Ocular Hx",$zz,"","","icon-folder","scan-icon","surgery_consent_patient_scan.php?scanTypeFolder=5","consent_data_surgery");
		$ocularNodeId = $healthQuesNodeId;
		
		$qryGetPatientInfoScan = "SELECT sd.id,sd.patient_id,sd.scan_doc_add,sd.mask,  
									DATE_FORMAT(sd.surgery_patient_scan_date, '".get_sql_date_format('','Y','-')." %h:%i %p') AS scan_upload_date_time,
									concat(u.lname,', ',u.fname,' ',u.mname) AS operator_name
									FROM surgery_center_patient_scan_docs sd 
									LEFT JOIN users u ON(u.id = sd.surgery_patient_scan_operator)
									WHERE sd.patient_id='".$patient_id."' and sd.scan_type_folder = '5' 
									ORDER BY sd.created_date DESC" ;
		$rsGetPatientInfoScan = get_array_records_query($qryGetPatientInfoScan);	
		for($x=0;$x<count($rsGetPatientInfoScan);$x++){
			$ocularNodeId++;
			$scanDocAdd = $rsGetPatientInfoScan[$x]['scan_doc_add'];
			$scanDocId = $rsGetPatientInfoScan[$x]['id'];
			$scanDocAdd = explode("/",$scanDocAdd);	
			$mask = $rsGetPatientInfoScan[$x]['mask'];
		
			if($mask){
				$arrMask = explode('.',$mask);
				$filename = $arrMask[0];
			}
			else{
				$arrFilename = explode('.',$scanDocAdd[2]);
				$filename = $arrFilename[0];
			}
			$showInfo		 	= "yes";
			$scanUploadDateTime = $rsGetPatientInfoScan[$x]['scan_upload_date_time'];
			$operatorName 	 	= stripslashes($rsGetPatientInfoScan[$x]['operator_name']);
			$tree->addToArray($ocularNodeId,$filename,$healthQuesNodeId,"show_scan_doc.php?scanDocId=$scanDocId","consent_data_surgery","pdf-icon","remove-icon","javascript:top.fmain.left_panel('del_pt_doc',$scanDocId)","consent_data_surgery","Delete H&P Scan Document",true,"onMouseDown","divOpenClose(event,'".$ocularNodeId."','".$scanDocId."');",$showInfo,$scanUploadDateTime,$operatorName,"","");
		}
		
		$ocularNodeId++;
		//END  OCULAR HX FOLDER
		$p=$ocularNodeId;
		$tree->addToArray($ocularNodeId,"H&P",$zz,"","","icon-folder","scan-icon","surgery_consent_patient_scan.php?scanTypeFolder=3","consent_data_surgery");
		$hpNodeId = $ocularNodeId;
		$qryGetPatientInfoScan = "SELECT sd.id,sd.patient_id,sd.scan_doc_add,sd.mask,  
									DATE_FORMAT(sd.surgery_patient_scan_date, '".get_sql_date_format('','Y','-')." %h:%i %p') AS scan_upload_date_time,
									concat(u.lname,', ',u.fname,' ',u.mname) AS operator_name
									FROM surgery_center_patient_scan_docs sd 
									LEFT JOIN users u ON(u.id = sd.surgery_patient_scan_operator)
									WHERE sd.patient_id='".$patient_id."' and sd.scan_type_folder = '3' 
									ORDER BY sd.created_date DESC" ;
		$rsGetPatientInfoScan = get_array_records_query($qryGetPatientInfoScan);	
		for($x=0;$x<count($rsGetPatientInfoScan);$x++){
			$hpNodeId++;
			$scanDocAdd = $rsGetPatientInfoScan[$x]['scan_doc_add'];
			$scanDocId = $rsGetPatientInfoScan[$x]['id'];
			$scanDocAdd = explode("/",$scanDocAdd);	
			$mask = $rsGetPatientInfoScan[$x]['mask'];
		
			if($mask){
				$arrMask = explode('.',$mask);
				$filename = $arrMask[0];
			}
			else{
				$arrFilename = explode('.',$scanDocAdd[2]);
				$filename = $arrFilename[0];
			}
			$showInfo		 	= "yes";
			$scanUploadDateTime = $rsGetPatientInfoScan[$x]['scan_upload_date_time'];
			$operatorName 	 	= stripslashes($rsGetPatientInfoScan[$x]['operator_name']);
			$tree->addToArray($hpNodeId,$filename,$ocularNodeId,"show_scan_doc.php?scanDocId=$scanDocId","consent_data_surgery","pdf-icon","remove-icon","javascript:top.fmain.left_panel('del_pt_doc',$scanDocId)","consent_data_surgery","Delete H&P Scan Document",true,"onMouseDown","divOpenClose(event,'".$hpNodeId."','".$scanDocId."');",$showInfo,$scanUploadDateTime,$operatorName,"","");
		}
		
		$hpNodeId++;
		$p=$hpNodeId;
		$tree->addToArray($hpNodeId,"EKG",$zz,"","","icon-folder","scan-icon","surgery_consent_patient_scan.php?scanTypeFolder=4","consent_data_surgery");
		$ekgNodeId = $hpNodeId;
	
		$qryGetPatientInfoScan = "SELECT sd.id,sd.patient_id,sd.scan_doc_add,sd.mask,  
									DATE_FORMAT(sd.surgery_patient_scan_date, '".get_sql_date_format('','Y','-')." %h:%i %p') AS scan_upload_date_time,
									concat(u.lname,', ',u.fname,' ',u.mname) AS operator_name
									FROM surgery_center_patient_scan_docs sd 
									LEFT JOIN users u ON(u.id = sd.surgery_patient_scan_operator)
									WHERE sd.patient_id='".$patient_id."' and sd.scan_type_folder = '4' 
									ORDER BY sd.created_date DESC" ;
		$rsGetPatientInfoScan = get_array_records_query($qryGetPatientInfoScan);	
		for($x=0;$x<count($rsGetPatientInfoScan);$x++){
			$ekgNodeId++;
			$scanDocAdd = $rsGetPatientInfoScan[$x]['scan_doc_add'];
			$scanDocId = $rsGetPatientInfoScan[$x]['id'];
			$scanDocAdd = explode("/",$scanDocAdd);	
			$mask = $rsGetPatientInfoScan[$x]['mask'];
		
			if($mask){
				$arrMask = explode('.',$mask);
				$filename = $arrMask[0];
			}
			else{
				$arrFilename = explode('.',$scanDocAdd[2]);
				$filename = $arrFilename[0];
			}
			$showInfo		 	= "yes";
			$scanUploadDateTime = $rsGetPatientInfoScan[$x]['scan_upload_date_time'];
			$operatorName 	 	= stripslashes($rsGetPatientInfoScan[$x]['operator_name']);
			$tree->addToArray($ekgNodeId,$filename,$hpNodeId,"show_scan_doc.php?scanDocId=$scanDocId","consent_data_surgery","pdf-icon","remove-icon","javascript:top.fmain.left_panel('del_pt_doc',$scanDocId)","consent_data_surgery","Delete EKG Scan Document",true,"onMouseDown","divOpenClose(event,'".$ekgNodeId."','".$scanDocId."');",$showInfo,$scanUploadDateTime,$operatorName,"","");
			
		}
		$p=$ekgNodeId;
		include_once($GLOBALS['fileroot']."/interface/common/docs_name.php");
		$p++;
		
	}
	ob_start();
	$tree->writeCSS();
	$tree->writeJavascript();
	//echo '<div style="width:'.(($GLOBALS['gl_browser_name']=='ipad')?"256px":"100%").'; height:100%; overflow:hidden; ">';
	$tree->drawTree();
	//echo '</div>';
	$html = ob_get_contents();
	ob_end_clean();
	
	unset($tree);
	
	return $html;
	
}


?>

