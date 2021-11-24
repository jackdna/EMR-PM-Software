<?php
set_time_limit(0);
$ignoreAuth = true;

/*Set Practice Name - for dynamically including config file*/
if(!isset($argv[1])) $argv[1] = 'imwemr';
if($argv[1]){
	$practicePath = trim($argv[1]);
	$_SERVER['REQUEST_URI'] = $practicePath;
	$_SERVER['HTTP_HOST']= $practicePath;
}
require_once(dirname(__FILE__)."/../../config/globals.php");
function LogResponse($text){
	echo date('Y-m-d H:i:s').': '.$text.'<br>';
}

$outbound_dir = constant('OUTBOUND_HL7_DIR');
if($outbound_dir == '') {$outbound_dir = false;}

$CURR_date 				= date('Y-m-d');
list($year,$month,$day) = explode('-',$CURR_date);
$BEGIN_period			= date('Y-m-d',mktime(0,0,0,$month,$day-2,$year));
//$END_period				= date('Y-m-d',mktime(0,0,0,$month,$day-2,$year));

$sql="SELECT id, msg, msg_type 
		FROM ".constant('IMEDIC_SC').".hl7_sent 
		WHERE sent = 0 AND send_to = 'PPMC' AND DATE_FORMAT(saved_on,'%Y-%m-%d') <= '$BEGIN_period'";

$res = imw_query($sql);
if($outbound_dir){//CREATE TXT FILES AND UPLOAD
	$ADT_DIR 	= $outbound_dir;
	$DFT_DIR	= $outbound_dir;
	$ARCHIVE_DIR= 'archive';
	$ADT_FILE	= 'ADT_'.date('YmdHis').'.txt';
	$DFT_FILE	= 'DFT_'.date('YmdHis').'.txt';
	$adt_Write	= $dft_Write = false;
	if($res && imw_num_rows($res)>0){
		while($rs = imw_fetch_assoc($res)){
			$msgID	 = $rs['id']; 
			$msgText = chr(11).stripslashes($rs['msg']).chr(28).chr(13);
			$msgType = $rs['msg_type'];
			switch($msgType){
				case 'Detailed Financial Transaction':
				case 'DFT':
					$fp		= fopen($DFT_DIR.DIRECTORY_SEPARATOR.$DFT_FILE,'a+');
					$fw		= fwrite($fp,$msgText);
					fclose($fp);
					MarkHL7Filed($msgID);
					$dft_Write = true;
					break;
				case 'Update_Patient':
				case 'Add_New_Patient':
				case 'ADT':
					$fp		= fopen($ADT_DIR.DIRECTORY_SEPARATOR.$ADT_FILE,'a+');
					$fw		= fwrite($fp,$msgText);
					fclose($fp);
					MarkHL7Filed($msgID);
					$adt_Write = true;
					break;
				default:
					//echo $msgType.'<br>';
			}
		}
		
		$FILE_ARRAY = array();
		if($adt_Write){
			echo 'ADT text file created<br>';
			$FILE_ARRAY[] = $ADT_FILE;
		}
		if($dft_Write){
			echo 'DFT text file created<br><hr>';
			$FILE_ARRAY[] = $DFT_FILE;
		}
		echo 'Trying to upload files....<br>';
		
		if(count($FILE_ARRAY)>0){
			FUNC_uploadFile($FILE_ARRAY);
		}
		
	}else{
		$msg_info[] = 'NO pending message found';
	}	
}


function MarkHL7Filed($msgId){
	$q = "UPDATE ".constant('IMEDIC_SC').".hl7_sent SET sent = 1, sent_on = '".date('Y-m-d H:i:s')."'  WHERE id = '".$msgId."'";
	$res = imw_query($q);
}


function FUNC_uploadFile($UPLOAD_FILES){
	$strServer = "login.ppmcbilling.com";
	$strServerIP = "216.107.216.226";//"72.12.80.195";
	$strServerPort = "22";
	$strTimeOut=1000;//seconds
	
	if(strtolower($GLOBALS["LOCAL_SERVER"])=='palisades'){
		$strServerUsername = "palisades";
		$strServerPassword = "#pAs5q7!";
	}else if(in_array(strtolower($GLOBALS["LOCAL_SERVER"]),array('albany','waltham'))){
		$strServerUsername = "jms";
		$strServerPassword = "#Jms26!";
	}
	/* Set the correct include path to 'phpseclib'. Note that you will need 
	   to change the path below depending on where you save the 'phpseclib' lib.
	   The following is valid when the 'phpseclib' library is in the same 
	   directory as the current file.
	*/
	
	//set_include_path(get_include_path() . PATH_SEPARATOR . './phpseclib0.3.8');

	include('Net/SFTP.php');

	//include('phpseclib0.3.8/Net/SFTP.php');
	
	/* Change the following directory path to your specification */
	$local_directory = constant('OUTBOUND_HL7_DIR');
	$local_archive_directory = constant('OUTBOUND_HL7_DIR').DIRECTORY_SEPARATOR.'archive';//local archive dir to move file after successful upload
	if(!file_exists($local_archive_directory) || !is_dir($local_archive_directory))//checking is archive dir exist if not then create it
	{
		mkdir($local_archive_directory);
	}
	
	if(strtolower($GLOBALS["LOCAL_SERVER"])=='palisades'){
		$ADT_remote_directory = '/home/palisades/Demos';//remote dir to upload files
		$DFT_remote_directory = '/home/palisades/Reports';//remote dir to upload files
	}else if(strtolower($GLOBALS["LOCAL_SERVER"])=='albany'){
		$ADT_remote_directory = '/home/jms/Albany/Demos';//remote dir to upload files
		$DFT_remote_directory = '/home/jms/Albany/Reports';//remote dir to upload files
	}else if(strtolower($GLOBALS["LOCAL_SERVER"])=='waltham'){
		$ADT_remote_directory = '/home/jms/Boston/Demos';//remote dir to upload files
		$DFT_remote_directory = '/home/jms/Boston/Reports';//remote dir to upload files
	}
	//$remote_directory = '/home/demo/public_html/uploads/';//providing physical(full) path
	
	/* Add the correct FTP credentials below */
	$sftp = new Net_SFTP($strServerIP,$strServerPort,$strTimeOut);
	if (!$sftp->login($strServerUsername,$strServerPassword))
	{
		exit('Login Failed');
	}else{
		echo 'Login Success.<br>';
	}
	
	try
		{
			//now check is that folder exist on server if not then create it
			if(!$sftp->file_exists($remote_directory))
			{
				//create directory
				$sftp->mkdir($remote_directory);
			}
			
		}
		catch (Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
		
		
	  /* Upload the local file to the remote server 
		 put('remote file', 'local file');
	   */
	   foreach($UPLOAD_FILES as $UPLOAD_FILE){
		   if(file_exists($local_directory.DIRECTORY_SEPARATOR.$UPLOAD_FILE))
			{
				if(substr($UPLOAD_FILE,0,3)=='ADT'){$remote_directory = $ADT_remote_directory;}
				else if(substr($UPLOAD_FILE,0,3)=='DFT'){$remote_directory = $DFT_remote_directory;}
				echo" Tring to upload in ".$remote_directory.'/'.$UPLOAD_FILE.'<br/><br/>';
				$success = $sftp->put($remote_directory.'/'.$UPLOAD_FILE, $local_directory.DIRECTORY_SEPARATOR.$UPLOAD_FILE, NET_SFTP_LOCAL_FILE);
				echo "upload :".$success;
				if($success){
					rename($local_directory.DIRECTORY_SEPARATOR.$UPLOAD_FILE,$local_archive_directory.DIRECTORY_SEPARATOR.$UPLOAD_FILE);//move that file to archieve folder
				}
			}else{
				echo $local_directory.$UPLOAD_FILE." file not exist";
			}
	   }

}
?>
