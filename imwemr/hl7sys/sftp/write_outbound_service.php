<?php
set_time_limit(0);
$ignoreAuth = true;

/*Set Practice Name - for dynamically including config file*/
if($argv[1]){
	$practicePath = trim($argv[1]);
	$_SERVER['REQUEST_URI'] = $practicePath;
	$_SERVER['HTTP_HOST']= $practicePath;
}

require_once(dirname(__FILE__)."/../../config/globals.php");
include_once(dirname(__FILE__)."/../sender/commonFunctions.php");
include_once(dirname(__FILE__)."/../hl7GP/hl7Create.php");
/*
error_reporting(-1);
ini_set("display_errors",-1);
die('good');
*/

$hl7Class = new hl7Create();
$flagFile = $hl7Class->hl7FlagPath();
$flagFile .= DIRECTORY_SEPARATOR.'senderCheckDB.log';

$outboundConfigARR = $GLOBALS["SSH_OUTBOUND"];
if(!$outboundConfigARR) die('No SSH Outbound cnfiguration found.');


do{
	$db_check_flag = intval(file_get_contents($flagFile));
	if($db_check_flag=='1' || 1==1){
		$ssh_domainIP	= $outboundConfigARR['domainIP'];
		$ssh_port		= $outboundConfigARR['port'];
		$ssh_path		= $outboundConfigARR['path'];
		$ssh_user		= $outboundConfigARR['user'];
		$ssh_pass		= $outboundConfigARR['pass'];
		
		
		$sql="SELECT id, msg, msg_type FROM hl7_sent WHERE sftp_sent = 0 ORDER BY id DESC";
		$res = imw_query($sql);
		if($res && imw_num_rows($res)>0){
			LogResponse(imw_num_rows($res).' Messages pending to send.<br>');
			$connection = ssh2_connect($ssh_domainIP, $ssh_port);
			ssh2_auth_password($connection, $ssh_user, $ssh_pass);
			
			$sftp = ssh2_sftp($connection);
			$sftp_fd = intval($sftp);
			
			$file_root = "ssh2.sftp://$sftp_fd/".$ssh_path;
			$msg_type_dir_arr = false;
			if(is_array($GLOBALS["SSH_OUTBOUND_DIR_MAPPING"])){
				$msg_type_dir_arr = $GLOBALS["SSH_OUTBOUND_DIR_MAPPING"];
			}

			while($rs = imw_fetch_assoc($res)){
				$msgid = $rs['id'];
				$msg   = $rs['msg'];
				$msg_type = $rs['msg_type'];
				$zms_path = $ssh_path;
				
				switch($msg_type){
					case 'ZMS^Z01':
					case 'ZMS^Z02':
					case 'ZMS^Z03':
					case 'ZMS':
					case 'prescription':
						$zms_path = $msg_type_dir_arr['ZMS'];
						$file_root = "ssh2.sftp://$sftp_fd/".$zms_path;				
						break;
					case 'book_appointemnt':
					case 'checkIn_appointment':
					case 'cancel_appointemnt':
					case 'reschedule_appointemnt':
					case 'update_appointment':
					case 'checkOut_appointment':
					case 'SIU':
						$siu_path = $msg_type_dir_arr['SIU'];
						$file_root = "ssh2.sftp://$sftp_fd/".$siu_path;				
						break;
					default:
						$file_root = "ssh2.sftp://$sftp_fd/".$ssh_path;
						break;			
			
				}

				file_put_contents($file_root."/".$msgid.".HL7",$msg);
				if(file_exists($file_root."/".$msgid.".HL7")){
					LogResponse('Message file written to OUTBOUND Directory ('.$zms_path.').');
					$r2 = imw_query("UPDATE hl7_sent SET sftp_sent='1', sftp_sent_on='".date('Y-m-d H:i:s')."' WHERE id='".$msgid."' LIMIT 1");
					if($r2) LogResponse('Message marked as SENT.');
				}		
			}
			
			@ssh2_disconnect($connection);
			$connection = NULL;
			unset($connection);
			sleep(3);
			
		}else{
		//	file_put_contents($flagFile,0);
			sleep(3);
		}
	}
}while(true);
?>