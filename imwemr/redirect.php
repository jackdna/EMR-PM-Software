<?php
set_time_limit(0);

/*Validate Requests from ios App*/
$ignoreAuth = true;
require_once(dirname(__FILE__)."/config/globals.php");

if( isset($_SESSION['allowAppAccess']) && (bool)$_SESSION['allowAppAccess'] === true )
{	
	$file_queried = $_SERVER['DOCUMENT_ROOT'].$_GET['file'];
	header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($file_queried).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file_queried));
	readfile($file_queried);
    exit;
}
else
{
	include_once("$srcdir/classes/auth.php");
}
/*End Request Validation for IOS App*/


$url = $_GET['file'];

$new_url = $url;
$info = pathinfo($url);
/*
if($info['extension']=='pdf'){
	$new_url = $info['dirname'].'/'.$info['filename'].'.pdfxx';
}else if($info['extension']=='PDF'){
	$new_url = $info['dirname'].'/'.$info['filename'].'.PDFXX';
} */
//echo $new_url;die;
//header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
//header("REDIRECT_DONE:true");
//header("Location: ".$new_url);

echo '<script type="text/javascript">window.location.href="'.$new_url.'"</script>';
exit;
?>
