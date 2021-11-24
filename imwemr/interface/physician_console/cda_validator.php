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
require_once(dirname(__FILE__).'/../../config/globals.php');
$dir_path=$GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH');
$ccda_file = trim($_GET['ccda_file']);
set_time_limit(60);
if($_GET['source']=='tempunzipped'){
	$ccda_file = $dir_path.'/users/UserId_'.$_SESSION['authId'].'/mails/tempunzipped/'.$ccda_file;
}else{
	$ccda_file = $dir_path.'/users'.$ccda_file;
}



$arrName   = explode("/",$ccda_file);
$file_name = end($arrName);
$extension = pathinfo($ccda_file, PATHINFO_EXTENSION);
//die('end='.substr($file_name,0,-4));
$txtRootPath = $dir_path.'/users/UserId_'.$_SESSION['authId'].'/mails/tempunzipped';
if(is_dir($txtRootPath) == false){mkdir($txtRootPath, 0755, true);}
$txtFileFullpath = $txtRootPath.'/'.substr($file_name,0,-4).'.txt';
//die($txtFileFullpath);

$validationObjective = 'C-CDA_IG_Plus_Vocab';
$referenceFileName	 = 'Readme.txt';
if(!file_exists($txtFileFullpath) || !is_file($txtFileFullpath)){
	$AccessURL = "https://ttpds.sitenv.org:8443/referenceccdaservice/?validationObjective=".$validationObjective."&referenceFileName=".$referenceFileName;
	$curl = curl_init($AccessURL);  
	$a=curl_custom_postfields($curl, array('a'=>'b'), array('ccdaFile'=>$ccda_file)) ;
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
	curl_setopt($curl, CURLOPT_FAILONERROR, false);  
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);  
	//curl_setopt($curl, CURLOPT_HEADER, true);
	//curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);   
	//curl_setopt($curl, CURLOPT_POST, 1);                                         
	//curl_setopt($curl, CURLOPT_POSTFIELDS, array('name' => 'ccdaFile', 'file' => "@".$file));
	$response = curl_exec($curl);
	$error = '';
	if(!$response){$error = curl_error($curl);}else{
		file_put_contents($txtFileFullpath,$response);
	}
	$eventType = 'curl';
	//array_map('unlink', glob( "$txtRootPath/*.txt"));
}else{
	$response = file_get_contents($txtFileFullpath);
	$error = '';
	$eventType = 'file';
}


if($response){
	$result = json_decode($response,false);
	if(is_object($result)){
		$metaData = $result->resultsMetaData;
		unset($metaData->ccdaFileName);
		unset($metaData->ccdaFileContents);
		$TotalErrors = 0;
		foreach($metaData->resultMetaData as $i=>$error){
			if($i==0 || $i==3 || $i==6)
			$TotalErrors += (int)$error->count;
		}
		if($TotalErrors > 0 || $serviceErrorMessage != '') unset($metaData->resultMetaData);
		$result->resultsMetaData->TotalErrors = $TotalErrors;
	}
	
	if($eventType == 'curl'){
		echo json_encode($metaData);
		die;
	}else{
		echo $eventType;
		die;
	}
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>:: imwemr ::</title>
    <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/common.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css">
    <link href="<?php echo $GLOBALS['webroot']; ?>/library/css/physician_console.css" rel="stylesheet" type="text/css">
    <!--[if lt IE 9]>
        <script src="<?php echo $GLOBALS['webroot'] ?>/library/js/html5shiv.min.js"></script>
        <script src="<?php echo $GLOBALS['webroot'] ?>/library/js/respond.min.js"></script>
    <![endif]-->
     <!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
     <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.min.1.12.4.js"></script> 
     <!-- Include all compiled plugins (below), or include individual files as needed --> 
     <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap.min.js"></script> 
     <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.mCustomScrollbar.concat.min.js"></script>
     <script src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap-typeahead.js"></script>
     <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/common.js"></script>
     <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/messi/messi.js"></script>
     <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/console.js?<?php echo filemtime('../../library/js/console.js');?>"></script>
	<style type="text/css">
		body{background-color:#FFF;}
	</style>
</head>
<body>
<?php if($eventType == 'file'){?>
	
    
<?php }?>
</body>
</html>
<?php
function curl_custom_postfields($ch, array $assoc = array(), array $files = array()) {
	/**
	* For safe multipart POST request for PHP5.3 ~ PHP 5.4.
	*
	* @param resource $ch cURL resource
	* @param array $assoc "name => value"
	* @param array $files "name => path"
	* @return bool
	*/   
    // invalid characters for "name" and "filename"
    static $disallow = array("\0", "\"", "\r", "\n");
   
    // build normal parameters
    foreach ($assoc as $k => $v) {
        $k = str_replace($disallow, "_", $k);
        $body[] = implode("\r\n", array(
            "Content-Disposition: form-data; name=\"{$k}\"",
            "",
            filter_var($v),
        ));
    }
   
    // build file parameters
    foreach ($files as $k => $v) {
        switch (true) {
            case false === $v = realpath(filter_var($v)):
            case !is_file($v):
            case !is_readable($v):
                continue; // or return false, throw new InvalidArgumentException
        }
        $data = file_get_contents($v);
        $v = call_user_func("end", explode(DIRECTORY_SEPARATOR, $v));
        $k = str_replace($disallow, "_", $k);
        $v = str_replace($disallow, "_", $v);
        $body[] = implode("\r\n", array(
            "Content-Disposition: form-data; name=\"{$k}\"; filename=\"{$v}\"",
            "Content-Type: application/octet-stream",
            "",
            $data,
        ));
    }
   
    // generate safe boundary
    do {
        $boundary = "---------------------" . md5(mt_rand() . microtime());
    } while (preg_grep("/{$boundary}/", $body));
   
    // add boundary for each parameters
    array_walk($body, function (&$part) use ($boundary) {
        $part = "--{$boundary}\r\n{$part}";
    });
   
    // add final boundary
    $body[] = "--{$boundary}--";
    $body[] = "";
   
    // set options
    return @curl_setopt_array($ch, array(
        CURLOPT_POST       => true,
        CURLOPT_POSTFIELDS => implode("\r\n", $body),
        CURLOPT_HTTPHEADER => array(
            "Expect: 100-continue",
            "Content-Type: multipart/form-data; boundary={$boundary}", // change Content-Type
        ),
    ));
}

?>