<?
error_reporting(E_ALL);

if($sftp_credentials_set=='1'){
	//$strServer = "ssh3.mytelevox.com";
	$strServerIP = $sftp_strServerIP;
	$strServerPort = $sftp_strServerPort;
	$strServerUsername = $sftp_strServerUsername;
	$strServerPassword = $sftp_strServerPassword;
	$remote_directory_path= $remote_directory;
}else{
	$strServer = "ssh3.mytelevox.com";
	$strServerIP = "ssh3.mytelevox.com";
	$strServerPort = "22";
	$strServerUsername = "543346";
	$strServerPassword = "pKe389Bg";
	$remote_directory_path='/Home/';
}
/* Set the correct include path to 'phpseclib'. Note that you will need 
   to change the path below depending on where you save the 'phpseclib' lib.
   The following is valid when the 'phpseclib' library is in the same 
   directory as the current file.
 */



set_include_path(get_include_path() . PATH_SEPARATOR . './phpseclib0.3.8');
include('phpseclib0.3.8/Net/SFTP.php');


/* Change the following directory path to your specification */
$local_directory = $dirName.'/';
$remote_directory1 = $remote_directory_path;//providing physical(full) path
$file= $fileName;

/* Add the correct FTP credentials below */
$sftp = new Net_SFTP($strServerIP,$strServerPort,'1000');
if (!$sftp->login($strServerUsername,$strServerPassword)) 
{
   //exit('Login Failed');
} else{
	//echo 'Login Successful';
}

	if(file_exists($local_directory.$file))
	{
	  /* Upload the local file to the remote server 
		 put('remote file', 'local file');
	   */
	  $success = $sftp->put($remote_directory1 . $file, 
							$local_directory . $file, 
							 NET_SFTP_LOCAL_FILE);
	// echo "upload physical :".$success;
	 
	}
	else
	{
		//echo 'file not found';
	}
?>