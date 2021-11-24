
<?php

//error_reporting(-1);
//ini_set("display_errors",-1);

$ignoreAuth = true;
//$practicePath = 'idoc';
if(isset($argv[1])){
	$practicePath = trim($argv[1]);
	$_SERVER['REQUEST_URI'] = $practicePath;
	$_SERVER['HTTP_HOST']= $practicePath;
}


require_once(dirname(__FILE__)."/../../config/globals.php");
include_once(dirname(__FILE__)."/../../library/classes/SaveFile.php");
set_time_limit(0);

if(isset($GLOBALS["CUBIXX_CHARGES_XML_CONF"]) && is_array($GLOBALS["CUBIXX_CHARGES_XML_CONF"]) && constant('GENERATE_CUBIXX_CHARGES_XML') && strtolower(constant('GENERATE_CUBIXX_CHARGES_XML'))=='yes'){	
	function cubixx_get_pending_xmls(){
		$q = "SELECT id,xml_text FROM xml_outbound_interface WHERE sent=0";
		$res = imw_query($q);
		if($res && imw_num_rows($res)){
			$return = array();
			while($rs = imw_fetch_assoc($res)){
				$return[] = $rs;
			}
			return $return;
		}
		return false;
	}
	
	function cubixx_make_xmls_file($pendingMsg){
		global $oSaveFile;
		$filename	= "XMLcharges".date('YmdHis').".xml";
		$xml_file	= $oSaveFile->upDir.'/tmp/'.$filename;
		if(!is_dir($oSaveFile->upDir.'/tmp/')){
			mkdir($oSaveFile->upDir.'/tmp/',777,true);
		}
		$write = file_put_contents($xml_file, '<?xml version="1.0" encoding="UTF-8"?>'."\n", FILE_APPEND);
		$id_array = array();
		foreach($pendingMsg as $rs){
			$id = $rs['id'];
			$id_array[] = $id;
			$xml= $rs['xml_text'];
			$write = file_put_contents($xml_file, $xml."\n", FILE_APPEND);
		}
		if($write && $write>0){
			$id_str = implode(',',$id_array);
			cubixx_upload_xml_to_sftp($xml_file,$filename,$id_str);
		}
	}
	
	function cubixx_upload_xml_to_sftp($xml_file,$filename,$id){
		$outboundConfigARR = $GLOBALS["CUBIXX_CHARGES_XML_CONF"];
		$ssh_domainIP	= $outboundConfigARR['domainIP'];
		$ssh_port		= $outboundConfigARR['port'];
		$ssh_path		= $outboundConfigARR['path'];
		$ssh_user		= $outboundConfigARR['user'];
		$ssh_pass		= $outboundConfigARR['pass'];
		$msg			= file_get_contents($xml_file);
		$connection = ssh2_connect($ssh_domainIP, $ssh_port);
		ssh2_auth_password($connection, $ssh_user, $ssh_pass);
		$sftp = ssh2_sftp($connection);
		$sftp_fd = intval($sftp);
		$file_root = "ssh2.sftp://$sftp_fd/".$ssh_path;
		
		file_put_contents($file_root."/".$filename,$msg);
		if(file_exists($file_root."/".$filename)){
			cubixx_mark_xml_as_sent($id,$filename);
		}
		
		//cubixx_mark_xml_as_sent($id);
	}
	
	function cubixx_mark_xml_as_sent($msgId,$filename){
		$res = imw_query("UPDATE xml_outbound_interface SET sent=1, sent_on='".date('Y-m-d H:i:s')."',uploaded_filename='".$filename."' WHERE id IN (".$msgId.")");
	}
	
	
	$pendingMsg = cubixx_get_pending_xmls();
	if($pendingMsg && is_array($pendingMsg)){
		$oSaveFile 	= new SaveFile;
		cubixx_make_xmls_file($pendingMsg);
	}else{
		//no pending message found.
	}

}else{
	die('CUBIXX Interface not defined');
}
?>