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

include("../../../config/globals.php");

if(isset($_GET["cancel_alert"])){
	if($_GET["cancel_alert"]=="Y"){
		$_SESSION['PT_EDU_ALERT_STATUS']=1;
		$_SESSION['PT_EDU_ALERT_ARRAY']=$_SESSION['PT_EDU_ARRAY'];
	}
	exit();
}

if(isset($_GET["elem_formAction"]) && $_GET["elem_formAction"]=="get_pt_edu_alert"){
	get_pt_edu_alert();
	exit();
}


//include_once("../../admin/schedular/fckeditor/fckeditor.php");
//require_once($GLOBALS['srcdir']."/ckeditor/ckeditor.php");
include_once($GLOBALS['srcdir']."/classes/work_view/wv_functions.php");
include_once($GLOBALS['srcdir']."/classes/work_view/ChartAP.php");
include_once($GLOBALS['srcdir']."/classes/work_view/PnTempParser.php");
include_once($GLOBALS['srcdir']."/classes/functions.smart_tags.php");
if(isERPPortalEnabled()) {
	require_once($GLOBALS['fileroot'] . '/library/html_to_pdf/createPdf.inc.php');
	require_once($GLOBALS['fileroot'] . '/interface/chart_notes/scan_docs/load_pt_docs_inc.php');
}

  
//include_once("../../main/classObjectFunction.php");
$upload_dir = "../../../data/".constant('PRACTICE_PATH');
$objParser = new PnTempParser;
$OBJsmart_tags = new SmartTags;

//print_r($_POST);
$idCommaSep = xss_rem($_REQUEST['idCommaSep']);
$patient_id = $_SESSION['patient'];
$form_id = $_SESSION["form_id"];
$qry_doc = imw_query("select * from document where id in(".$idCommaSep.")");
$pt_doc_arr=array();
if(imw_num_rows($qry_doc)>0){
	while($doc_fet = imw_fetch_array($qry_doc)) {
		$pt_doc_id='';
		$pt_id = $doc_fet['id'];
		$name = $doc_fet['name'];
		$visit = $doc_fet['visit'];
		$tests = $doc_fet['tests'];
		$dx = $doc_fet['dx'];
		$cpt = $doc_fet['cpt'];
		$medications = $doc_fet['medications'];
		$content = $doc_fet['content'];
		$old_name = $doc_fet['name'];
		$scan_id = $doc_fet['scan_id'];
		$pt_edu = $doc_fet['pt_edu'];
		$pt_test = $doc_fet['pt_test'];
		
		$doc_from = $doc_fet['doc_from'];
		$scan_doc_file_path = $doc_fet['scan_doc_file_path'];
		$upload_doc_file_path = $doc_fet['upload_doc_file_path'];
		$upload_doc_type = $doc_fet['upload_doc_type'];
		$scan_doc_date = $doc_fet['scan_doc_date'];
		$upload_doc_date = $doc_fet['upload_doc_date'];
		
		$FCKeditor1 = addslashes($_POST['FCKeditor1']);
	
		$contents = stripslashes($content); 
		$contents = $objParser->getDataParsed($contents,$patient_id,$form_id,'');
		$contents = str_replace('cccccc','ffffff',$contents);
		str_ireplace('<div style="margin: 0in 0in 0pt; line-height: normal; text-align: center;"><u><strong><span style="font-size: larger"><span style="font-family: Arial"><font size="2">Document</font></span></span></strong></u></div>', '', $contents);
	
				
		$operator_id = $_SESSION['authId'];
		$contents = addslashes($contents);
		$query1= "insert into document_patient_rel set doc_id='$pt_id',name='$name',
							p_id='$patient_id',form_id='$form_id',date_time=now(),doc_from='admin',
							scan_id='$scan_id',operator_id='$operator_id',visit_rel='$visit',tests_rel='$tests',
							dx_rel='$dx',cpt_rel='$cpt',medications_rel='$medications',
							doc_scn_upload_from='$doc_from',scan_doc_file_path='$scan_doc_file_path',upload_doc_file_path='$upload_doc_file_path',
							upload_doc_type='$upload_doc_type',scan_doc_date='$scan_doc_date',upload_doc_date='$upload_doc_date',
							description='".$contents."' "; 
		$ins_doc_pat=imw_query($query1);
		$pt_doc_id=imw_insert_id();					
		//PT. Instructions DOCS - ptInsDocs
		if(isERPPortalEnabled() ){
			$pt_doc_arr[]=$pt_doc_id;
		}

		//START CODE TO SAVE SCAN/UPLOAD DOCS FOR PATIENT-DOCUMENTS
		$chkScnUpldQry = "SELECT upload_lab_rad_data_id,givenToEduMultiPtId FROM upload_lab_rad_data WHERE uplaod_primary_id='".$pt_id."' AND scan_from='admin_documents' AND upload_status='0'";
		$chkScnUpldRes = imw_query($chkScnUpldQry) or die($chkScnUpldQry.imw_error());
		if(imw_num_rows($chkScnUpldRes)>0) {
			while($chkScnUpldRow = imw_fetch_array($chkScnUpldRes)) {
				$upload_lab_rad_data_id =  $chkScnUpldRow['upload_lab_rad_data_id'];
				$givenToEduMultiPtId = trim($chkScnUpldRow['givenToEduMultiPtId']);
				$saveGivenToPtId = "'".$patient_id."'";
				if($givenToEduMultiPtId) {
					$saveGivenToPtId = $givenToEduMultiPtId.","."'".$patient_id."'";	
				}
				$updtScnUpldQry = 'UPDATE upload_lab_rad_data SET givenToEduMultiPtId="'.$saveGivenToPtId.'" WHERE upload_lab_rad_data_id="'.$upload_lab_rad_data_id.'"';
				$updtScnUpldRes = imw_query($updtScnUpldQry) or die($updtScnUpldQry.imw_error());
			}
		}
		//END CODE TO SAVE SCAN/UPLOAD DOCS FOR PATIENT-DOCUMENTS

		
	}

	if(isERPPortalEnabled() && count($pt_doc_arr)>0) {
		$des_pth = "pt_msg_mails";
		foreach($pt_doc_arr as $pt_doc_id) {
			$ar_atch_files[] = create_html_file_4pdf($patient_id, $mode="print", $pt_doc_id, $type="PtInstructionsDocs", $des_pth  );
		}
		
		// Get file info
		$ar_file_info = array();
		$atch_lm = 3000000;$cntr_lm = 0;
		if(count($ar_atch_files) > 0){
			foreach($ar_atch_files as $k => $arfile_path){
			  if(file_exists($arfile_path[0])){
				$tfile_pth = $arfile_path[0];
				$tsize = filesize($tfile_pth);
				$cntr_lm = $cntr_lm + $tsize;
				$arrAttachment_tmp = array(
							"complete_path"=>$tfile_pth,
							"mime"=> mime_content_type($tfile_pth),
							"file_name"=>basename($tfile_pth),
							"size"=>$tsize,
							"file_path"=>$arfile_path[1]
						);
				$ar_file_info[] = $arrAttachment_tmp;
			  }
			}
		}	

		if($cntr_lm>$atch_lm){
			//remove files
			foreach($ar_atch_files as $k => $arfile_path){
			  if(file_exists($arfile_path[0])){
				unlink($arfile_path[0]);
			  }
			}

			$ar_file_info["error"] = "Attachments exceeds the limit! Please attach than 3MB.";
		}
		
		$patientId = trim($patient_id);
		$pt_msg_id = "";
		
		
		$qry_part = "";
		$message_urgent = 0;
		$msg_subject = 'Education material from your doctor';
		$msg_data = 'Education material from your doctor';
		$msg_erp_ext_id = '';
		
		$name_sendTo = getUserFirstName($_SESSION['authId'],$flgFull=1);
		$msg_data_portal = $msg_data;
		$msg_subject = imw_real_escape_string($msg_subject);
		$msg_data = imw_real_escape_string($msg_data);
		$req_qry = "INSERT INTO patient_messages SET receiver_id = '" . $patientId . "', sender_id = '" . $_SESSION['authId'] . "', communication_type = 1, msg_subject = '" . $msg_subject . "', msg_data = '" . $msg_data . "', message_urgent='" . $message_urgent . "'" . $qry_part;
		$req_qry_obj = imw_query($req_qry);
		$pt_msg_id_new = imw_insert_id();
		
		
		//save attachments
		$ar_atch_ptpr=array();
		$ar_not_atch_ptpr=array();
		$ar_attch_files = $ar_file_info;
		$atch_lm = 3000000;
		if(count($ar_attch_files)>0 && $pt_msg_id_new>0){
			foreach($ar_attch_files as $k => $o_arrMail){
			  $complete_path = $o_arrMail['file_path'];
			  $file_name = $o_arrMail['file_name'];
			  $mime = $o_arrMail['mime'];
			  $size = $o_arrMail['size'];

			  if($file_name != ""){
				$sql_ins = "INSERT INTO patient_messages_attachment SET
					  patient_messages_id = '".$pt_msg_id_new."',
					  file_name = '".$file_name."',
					  size = '".$size."',
					  mime = '".$mime."',
					  complete_path = '".imw_real_escape_string($complete_path)."',
					  patient_id = '".$patientId."',
					  op_time = '".date("Y-m-d H:i:s")."'
					  ";

				imw_query($sql_ins);

				$oSaveFile = new SaveFile($_SESSION["authId"],1,"users");
				if(isset($complete_path) && !empty($complete_path)){
					$complete_path = $oSaveFile->getFilePath($complete_path,'i');
					if(file_exists($complete_path)){
						if($atch_lm>0 && $atch_lm>=$size){
						  $atch_lm = $atch_lm - $size;
						  $ar_atch_ptpr[]=array("fileName"=>$file_name, "fileData"=>base64_encode(file_get_contents($complete_path)));
						}else{
						  $ar_not_atch_ptpr[]=array("fileName"=>$file_name, "fileData"=>base64_encode(file_get_contents($complete_path)));
						}
					}
				}
			  }
			}
		}
		
		//add into user_messages
		if(!empty($_REQUEST["message_pt_comm"])){
		   add_pt_msg_into_usr_msgs($pt_msg_id_new);
		}
		

		//START CODE TO SEND MESSAGE TO PATIENT PORTAL
		$erp_error=array();
		if($pt_msg_id_new && isERPPortalEnabled()) {
			try {
				include($GLOBALS['fileroot']."/library/erp_portal/rabbitmq_exchange.php");
				$OBJRabbitmqExchange = new Rabbitmq_exchange();

				$is_rsp = false;
				$rsp_id = '';
				$qry_rsp = "SELECT id FROM resp_party WHERE patient_id = '".$patientId."' LIMIT 0, 1";
				$res_rsp = imw_query($qry_rsp);
				if(imw_num_rows($res_rsp)>0) {
					$row_rsp = imw_fetch_assoc($res_rsp);
					$rsp_id = $row_rsp['id'];
					$is_rsp = true;
				}
				$msg_priority = 'Normal';
				$i=0;
				$pt_cur_dt = date("Y-m-d");
				$pt_cur_tm = date("H:i:s");
				$pt_msg_arr['fromSecureRecipientExternalId'] 			= $_SESSION['authId'];
				$pt_msg_arr['fromSecureRecipientName'] 					= trim($_SESSION['authProviderName']);
				$pt_msg_arr['patients'][$i]['externalId'] 				= $patientId;
				$pt_msg_arr['patients'][$i]['isRepresentative'] 		= $is_rsp;
				$pt_msg_arr['patients'][$i]['representativeExternalId'] = $rsp_id;
				$pt_msg_arr['subject'] 									= $msg_subject;
				$pt_msg_arr['body'] 									= $msg_data_portal;
				$pt_msg_arr['status'] 									= 'sent';
				$pt_msg_arr['priority'] 								= $msg_priority; //normal by default, if urgnt then high
				$pt_msg_arr['creationDate'] 							= $pt_cur_dt.'T0'.$pt_cur_tm;
				$pt_msg_arr['sentDate'] 								= $pt_cur_dt.'T0'.$pt_cur_tm;
				$pt_msg_arr['fileData']									= $ar_atch_ptpr;
				//$pt_msg_arr['fileData'][$i]['fileName'] 				= '';
				//$pt_msg_arr['fileData'][$i]['fileData'] 				= '';
				$pt_msg_arr['id'] 										= $msg_erp_ext_id; //first time blank and then response will generte id patient_messages tbl update that id - erp_portal_id
				$pt_msg_arr['externalId'] 								= $pt_msg_id_new;

				$id 													= $patientId;
				$resource 												= 'OutgoingSecureMessages';
				$method													= 'POST';


				$response_encode= $OBJRabbitmqExchange->send_request($pt_msg_arr,$id,$resource,$method);
				$response 		= json_decode($response_encode);
				if($response && $response->id && $pt_msg_id_new) {
					$qry_msg_up = "UPDATE patient_messages SET iportal_msg_id = '".$response->id."' WHERE pt_msg_id = ".$pt_msg_id_new;
					imw_query($qry_msg_up);
				}
			} catch(Exception $e) {
				$erp_error[]='Unable to connect to ERP Portal';
			}
		}
		//END CODE TO SEND MESSAGE TO PATIENT PORTAL
	}
	
}
?>
