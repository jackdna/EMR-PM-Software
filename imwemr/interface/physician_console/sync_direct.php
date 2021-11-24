<?php
require_once(dirname(__FILE__).'/../../config/globals.php');
set_time_limit(90);
require_once($GLOBALS['fileroot'].'/library/classes/msgConsole.php');
include_once($GLOBALS['fileroot'].'/library/classes/direct_class.php');
include_once($GLOBALS['fileroot'].'/library/updox/updoxDirect.php');
require_once($GLOBALS['fileroot'].'/library/classes/SaveFile.php');

//Get Modal
if(isset($_GET["upld_attch"]) && $_GET["upld_attch"]==1){
	require_once($GLOBALS['fileroot'].'/library/classes/work_view/wv_functions.php');
	require_once($GLOBALS['fileroot'].'/library/classes/work_view/Uploader.php');
	$oUpldr = new Uploader();	
	$oUpldr->receive_uploaded_files($_GET["sec"]."_mails");
	exit();
}

//
$objMsgCons = new msgConsole();
$email_sender_id = (int)$_POST['from_email_send'];
$email_sender_id = ($email_sender_id > 0 && $_REQUEST['sync_type'] == "send_mail") ? $email_sender_id : (int)$_SESSION['authId'];

$arrDirectCre = $objMsgCons->pt_direct_credentials($email_sender_id);
$error_msg = array();

//Checking if request is from allowed user instead of logged in user
$directUserID = (isset($_REQUEST['from_email_send']) && empty($_REQUEST['from_email_send']) == false) ? $_REQUEST['from_email_send'] : (int)$_SESSION['authId'];

try{

	if( is_updox('direct'))
	{
		$objDirect = new updoxDirect($directUserID);
	}
	else
	{
		$objDirect = new Direct($arrDirectCre['email'],$arrDirectCre['email_password']);
	}

	if($_REQUEST['sync_type'] == "inbox")
	{
		if(method_exists($objDirect, 'readInbox')) $objDirect->readInbox();
		foreach($objDirect->arrInbox as $arr){
			$qry = "SELECT * FROM direct_messages
					WHERE imedic_user_id = '".$_SESSION['authId']."'
					AND MID = '".$arr['mID']."'
					AND folder_type = 1
					";
			$res = imw_query($qry);
			if(imw_num_rows($res)<=0){
				$sql_ins = "INSERT INTO direct_messages SET
							to_email = '".$arrDirectCre['email']."',
							from_email = '".$arr['from']."',
							subject = '".$arr['subject']."',
							message = '".$arr['body']."',
							folder_type = '1',
							MID = '".$arr['mID']."',
							MSID = '".$arr['msID']."',
							FromUID = '".$arr['fromUID']."',
							msgSize = '".$arr['msgSize']."',
							del_status = 0,
							imedic_user_id = '".$_SESSION['authId']."',
							direct_datetime = '".$arr['datTime']."',
							local_datetime = '".date('Y-m-d H:i:s')."'
							";
				imw_query($sql_ins);
				$direct_message_id = imw_insert_id();				
				foreach($arr['attachment'] as $arrAttachment){
					$sql_ins = "INSERT INTO direct_messages_attachment SET
								direct_message_id = '".$direct_message_id."',
								file_name = '".$arrAttachment['name']."',
								size = '".$arrAttachment['size']."',
								mime = '".$arrAttachment['mime']."',
								complete_path = '".imw_real_escape_string($arrAttachment['complete_path'])."'
								";
					imw_query($sql_ins);
				}
			}
		}
	}
	elseif($_REQUEST['sync_type'] == "send_mail"){

		$objDirect->arrMail['to_email'] = $_REQUEST['to_email'];
		$objDirect->arrMail['from_email'] = $arrDirectCre['email'];
		$objDirect->arrMail['subject'] = $_REQUEST['subject'];
		$objDirect->arrMail['body'] = $_REQUEST['body'];
		$objDirect->arrMail['attachment'][0]='';
		$patientId = $_REQUEST["patientId"];
		$form_id = $_REQUEST["cmbxElectronicDOS"];
		if($form_id=='' or strtolower($form_id)=='all'){//make it for recent form_id.
			$fid_res = imw_query("SELECT id FROM chart_master_table WHERE patient_id='".$patientId."' AND purge_status='0' AND delete_status='0' ORDER BY date_of_service DESC LIMIT 1");
			if($fid_res && imw_num_rows($fid_res)==1){
				$fid_rs = imw_fetch_assoc($fid_res);
				$form_id = $fid_rs['id'];
			}
		}

		if(is_updox('direct')){
			$VID=$objDirect->validateMail();
			if($VID!=''){
				throw new Exception('error'.'--->'.$VID);
			}
		}

		$sql = "SELECT * FROM log_ccda_creation WHERE id = '".$_REQUEST['ccda_log_id']."' AND type = 1";
		$res = imw_query($sql);
		if(imw_num_rows($res)>0){
			$row = imw_fetch_assoc($res);

			$arrAttachment = array();
			$arrAttachment[] = array(
				"complete_path"=>$GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/users".$row['file_path'],
				"mime"=>($_REQUEST['attachment_type'] == 'xml') ? 'application/xml' : $row['mime'],
				"file_name"=>$row['file_name'],
				"size"=>$row['size'],
				"file_path"=>$row['file_path']
			);

			//Attach Files based on request
			switch($_REQUEST['attachment_type']){
				case 'xml':
					$objDirect->arrMail['attachment'][0] = 	$arrAttachment[0];
				break;

				case 'ccda':
					$XSL_size = filesize($GLOBALS['fileroot'].'/library/classes/CDA.xsl');
					$arrAttachment[] = array(
						"complete_path"=>$GLOBALS['fileroot'].'/library/classes/CDA.xsl',
						"mime"=>'xsl',
						"file_name"=>'CDA.xsl',
						"size"=>$XSL_size,
						"file_path"=>$GLOBALS['fileroot'].'/library/classes/CDA.xsl'
					);
					$zip = new ZipArchive();
					$zipname = 'CCDA-'.time().'.zip';
					$zipPath = $objDirect->save_directory."/".$zipname;
					$zip->open($zipPath, ZipArchive::CREATE);
					foreach ($arrAttachment as $file) {
					  $zip->addFile($file['complete_path'],$file['file_name']);
					}
					$zip->close();

					$objDirect->arrMail['attachment'][0] = 	array(
						"complete_path"=>$zipPath,
						"mime"=>'application/zip',
						"file_name"=>$zipname,
						"size"=>filesize($zipPath),
						"file_path"=>"/UserId_".$email_sender_id."/mails/".$zipname
					);
				break;
			}
			$sha_key_val = get_checksum_key_val($zipPath);
			if(empty($sha_key_val) == false){
				$objDirect->arrMail['body'] .= "\n\n SHA2 Key value : ".$sha_key_val;
			}
		}

		//add uploaded files --
		$attchd_files = $_POST["attchd_files"];
		$ar_attchd_files = json_decode($attchd_files, true);
		if(count($ar_attchd_files)>0){
			if($objDirect->arrMail['attachment'][0]==""){ $objDirect->arrMail['attachment']=array(); }
			$oSaveFile = new SaveFile($_SESSION["authId"],1,"users");
			foreach($ar_attchd_files as $k => $o_attchd_files){
				//
				$file_pointer_full = ""; $file_name_full= "";
				if(isset($o_attchd_files['curfile']) && !empty($o_attchd_files['curfile'])){
					$file_pointer_full = $oSaveFile->getFilePath($o_attchd_files['curfile'],'i');
					if(!file_exists($file_pointer_full)){	$file_pointer_full="";}
					else{ $file_name_full = basename($file_pointer_full); }
				}

				$arrAttachment_tmp = array(
					"complete_path"=>$file_pointer_full,
					"mime"=> $o_attchd_files['type'],
					"file_name"=>$file_name_full,
					"size"=>$o_attchd_files['size'],
					"file_path"=>$o_attchd_files['curfile']
				);
				$objDirect->arrMail['attachment'][] = $arrAttachment_tmp;

			}
		}
		
		//add uploaded files --

		//Pt Docs files
    $attchd_files_pt_docs = $_POST["attchd_files_pt_docs"];
		$tmp_attchd_files = json_decode($attchd_files_pt_docs, true);
    if(count($tmp_attchd_files)>0){
        if($objDirect->arrMail['attachment'][0]==""){ $objDirect->arrMail['attachment']=array(); }	
				$objDirect->arrMail['attachment'] = array_merge($objDirect->arrMail['attachment'], $tmp_attchd_files);
    }

		$MID_arr = $objDirect->sendMail();
		if($MID_arr['status']=='failed'){
			throw new Exception($MID_arr['statusCode'].'--->'.$MID_arr['message']);
		}elseif($MID_arr['data']->messageId>0){
			$MID=$MID_arr['data']->messageId;
		}else{
			$MID=$MID_arr;
		}
		if($MID != "" && $MID>0){
			$folder_type = "3";
		}else{
			$folder_type = "2";
		}
		if($folder_type = "3"){
			$email_status= ($MID_arr['status']=='failed')? 'failed' : 'sent';

			$sql_ins = "INSERT INTO direct_messages SET
							to_email = '".$objDirect->arrMail['to_email']."',
							from_email = '".$objDirect->arrMail['from_email']."',
							subject = '".$objDirect->arrMail['subject']."',
							message = '".$objDirect->arrMail['body']."',
							folder_type = '".$folder_type."',
							MID = '".$MID."',
							del_status = 0,
							reply_of = '".$_REQUEST['reply_of']."',
							imedic_user_id = '".$email_sender_id."',
							local_datetime = '".date('Y-m-d H:i:s')."',
							org_sender_id='".(int)$_SESSION['authId']."',
							email_status = '".$email_status."'
							";

				imw_query($sql_ins);
				$direct_message_id = imw_insert_id();

				if(isset($objDirect->arrMail['attachment']) && $direct_message_id>0){
					foreach($objDirect->arrMail['attachment'] as $k => $o_arrMail){
						$complete_path = $o_arrMail['file_path'];
						$file_name = $o_arrMail['file_name'];
						$mime = $o_arrMail['mime'];
						$size = $o_arrMail['size'];

						if($file_name != ""){
							$sql_ins = "INSERT INTO direct_messages_attachment SET
										direct_message_id = '".$direct_message_id."',
										file_name = '".$file_name."',
										size = '".$size."',
										mime = '".$mime."',
										complete_path = '".imw_real_escape_string($complete_path)."',
										patient_id = '".$patientId."',
										form_id = '".$form_id."'
										";
							imw_query($sql_ins);
						}
					}
				}
		}
			/*echo "<script>location.href='direct_messages.php?folder_type=".$folder_type."'</script>";*/

	}
}
catch(Exception $e)
{
	if($e->getMessage()){
		if(strpos($e->getMessage(),"--->")){
			$msg = explode('--->',$e->getMessage());
			$error_msg['error'] = $msg[1];
		}
	}
}

if(count($error_msg) > 0){
	echo json_encode($error_msg);
}else{
	echo json_encode('done');
}
?>
