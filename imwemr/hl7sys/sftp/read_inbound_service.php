<?php
set_time_limit(0);
$ignoreAuth = true;

/*Set Practice Name - for dynamically including config file*/
$listenerCount = 0;
if( $argv[1] )
		{
	$practicePath = trim($argv[1]);
	if( isset($argv[2]) && (int)$argv[2] > 0)
	{
		$listenerCount = (int)$argv[2];
	}
	$_SERVER['REQUEST_URI'] = $practicePath;
	$_SERVER['HTTP_HOST']= $practicePath;
}

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
/*
$counter_file = dirname(__FILE__)."/counter.txt";
$file_checked = 0;
if(!file_exists($counter_file) || !is_file($counter_file)){
	file_put_contents($counter_file,'0');
}else{
	$file_checked = intval(file_get_contents($counter_file));	
}
*/
$inboundConfigARR = $GLOBALS["SSH_INBOUND"];
if(!$inboundConfigARR) LogResponse('No SSH Inbound cnfiguration found.');

//STARTING INFINITE LOOP.
do{
	$ssh_domainIP	= $inboundConfigARR['domainIP'];
	$ssh_port		= $inboundConfigARR['port'];
	$ssh_path		= $inboundConfigARR['path'];
	$ssh_user		= $inboundConfigARR['user'];
	$ssh_pass		= $inboundConfigARR['pass'];
	$post2url		= isset($inboundConfigARR['post2url']) ? $inboundConfigARR['post2url'] : '';

	if(isset($connection)) unset($connection);
	if(isset($handle)) unset($handle);
	
	$connection = ssh2_connect($ssh_domainIP, $ssh_port);
	if (!$connection) LogResponse("Connection failed:");
	else LogResponse("SFTP connection success.");
	
	
	@ssh2_auth_password($connection, $ssh_user, $ssh_pass);

	$sftp = @ssh2_sftp($connection);
	$sftp_fd = intval($sftp);

	$file_root 			= "ssh2.sftp://$sftp_fd/".$ssh_path;

	$handle = opendir($file_root);
	if($handle) LogResponse($ssh_path." directory opened for reading.");
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
		if($entry!='.' && $entry!='..' && $entry!='archive') $files[$tmp1[0]] = $entry;
	}
//	pre($files);
	if(!$files || count($files)==0) {sleep (10); continue;}
	ksort($files);
	
	LogResponse(count($files).' files found for reading. Reading now.');
	foreach($files as $fileid=>$entry){
	//	echo "$entry".'<br>';
		LogResponse("$file_root/$entry");
		$stream = @fopen("$file_root/$entry", 'r');
		if (! $stream)
			LogResponse("Could not open file: $remote_file");
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
			LogResponse('Posting content for Parsing.');
			/*HL7 Parser Class*/
		//	$objHL7Reader = new HL7Reader();
		//	$objHL7Reader->router($contents, 0);
		
			$a = do_post_request($contents,$post2url); //using curl call, posting data to parsing program.
			LogResponse($a);
		//	echo '<br>';
			
			//$archiveDir		  = $file_root.'/archive';
			$archiveDir 	  = $webserver_root.'/data/'.PRACTICE_PATH.'/HL7_INBOUND/archive';
				if(!is_dir($archiveDir)){
					@mkdir($archiveDir,0777,true);
					chmod($archiveDir,777);
				}
			if(is_dir($archiveDir)){
				copy("$file_root/$entry",$archiveDir.'/'.$basename);
				if(file_exists($archiveDir.'/'.$basename)){
					//@ssh2_sftp_unlink($sftp, "$file_root/$entry");
					@unlink("$file_root/$entry");
				}
			}		
			//unset($objHL7Reader);
		}
		sleep (1);
	}
	sleep (5);
}while(true);
?>