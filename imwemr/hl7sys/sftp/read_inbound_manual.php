<?php
$ignoreAuth = true;
require_once(dirname(__FILE__)."/../../config/globals.php");
set_time_limit(0);
//require_once "Net/HL7/Message.php";
//require_once(dirname(__FILE__)."/../old/HL7Reader/".HL7_READER_VERSION.".php");
require_once(dirname(__FILE__).'/commonFunctions.php');
/*
error_reporting(-1);
ini_set("display_errors",-1);
die('good');
*/

$inboundConfigARR = $GLOBALS["SSH_INBOUND"];
if(!$inboundConfigARR) die('No SSH Inbound cnfiguration found.');
$ssh_domainIP	= $inboundConfigARR['domainIP'];
$ssh_port		= $inboundConfigARR['port'];
$ssh_path		= $inboundConfigARR['path'];
$ssh_user		= $inboundConfigARR['user'];
$ssh_pass		= $inboundConfigARR['pass'];
$post2url		= isset($inboundConfigARR['post2url']) ? $inboundConfigARR['post2url'] : '';

$connection = ssh2_connect($ssh_domainIP, $ssh_port);
if (!$connection) die("Connection failed. Not able to connect to $ssh_user@$ssh_domainIP");
ssh2_auth_password($connection, $ssh_user, $ssh_pass);

$sftp = ssh2_sftp($connection);
$sftp_fd = intval($sftp);

$file_root 			= "ssh2.sftp://$sftp_fd/".$ssh_path;

$handle = opendir($file_root);
//echo "Directory handle: $handle\n";
//echo "Entries:\n";
$files = array();
$read_extension = false;
if(constant('HL7_READ_EXTENSION') && constant('HL7_READ_EXTENSION')!=''){
	$read_extension = constant('HL7_READ_EXTENSION');
}
while (false != ($entry = readdir($handle))){
	if($read_extension && substr($entry,-4) !== $read_extension) continue;
		
	$tmp1 = explode('.',$entry);
	$files[$tmp1[0]] = $entry;
}
ksort($files);
pre($files);

foreach($files as $fileid=>$entry){
	echo "$entry".'<br>';
	if($entry!='.' && $entry!='..' && $entry!='archive'){
		//echo "$entry".'<br>';
		$stream = @fopen("$file_root/$entry", 'r');
		if (! $stream)
			die("Could not open file: $remote_file");
		$size = filesize("$file_root/$entry"); 
		$basename = basename("$file_root/$entry");         
		$contents = '';
		$read = 0;
		$len = $size;
		while ($read < $len && ($buf = fread($stream, $len - $read))) {
		  $read += strlen($buf);
		  $contents .= $buf;
		}   
		@fclose($stream);    

		if($contents!=''){
				echo ('Posting content for Parsing.<br>');
				/*HL7 Parser Class*/
			//	$objHL7Reader = new HL7Reader();
			//	$objHL7Reader->router($contents, 0);
			
				$a = do_post_request($contents,$post2url); //using curl call, posting data to parsing program.
				echo ($a);
				echo '<br>';
				
				//$archiveDir		  = $file_root.'/archive';
				$archiveDir 	  = $webserver_root.'/data/'.PRACTICE_PATH.'/HL7_INBOUND/archive';
				if(!is_dir($archiveDir)){
					@mkdir($archiveDir,0777,true);
					chmod($archiveDir,777);
				}
				echo $archiveDir;//die;
				if(is_dir($archiveDir)){
					echo '<br>moving file..';
					copy("$file_root/$entry",$archiveDir.'/'.$basename);
					if(file_exists($archiveDir.'/'.$basename)){
						//@ssh2_sftp_unlink($sftp, "$file_root/$entry");
						@unlink("$file_root/$entry");
					}
				}		
				//unset($objHL7Reader);
			}

		//echo $contents.'<hr>';

		die('<hr>END OF PROCESSING 1 MSG');
		echo '<hr>';
	}
}

?>