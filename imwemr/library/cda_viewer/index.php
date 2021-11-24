<?php
require_once(dirname(__FILE__).'/../../config/globals.php');
set_time_limit(60);
$dir_path	=$GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH');
$contentArr = $return_arr = $tmp_arr = array();
$ccda_file 	= trim($_GET['ccda_file']);
$ccda_file_path_backup  = $ccda_file;
$check_xsl 	= trim($_GET['check_xsl']);

$webDir_path = $GLOBALS['webroot'].'/data/'.constant('PRACTICE_PATH');
if($_GET['source']=='tempunzipped'){
	$ccda_file_base = $dir_path.'/users/UserId_'.$_SESSION['authId'].'/mails/tempunzipped/'.$ccda_file;
	$ccda_file = $webDir_path.'/users/UserId_'.$_SESSION['authId'].'/mails/tempunzipped/'.$ccda_file;
}else{
	$ccda_file_base = $dir_path.'/users'.$ccda_file;
	$ccda_file = $webDir_path.'/users'.$ccda_file;
} 

$temp_zip_xml_location = $dir_path.'/users/UserId_'.$_SESSION['authId'].'/mails/tempunzipped';

// Clean the temp unzip directory
if(isset($_REQUEST['cleanDirectory']) && $_REQUEST['cleanDirectory'] == 1){
	/***cleanup tempdir***/
	if(!file_exists($temp_zip_xml_location) || !is_dir($temp_zip_xml_location)){
		mkdir($temp_zip_xml_location,777,true);
		chmod($temp_zip_xml_location,0777);
	}else{
		if(is_dir("$temp_zip_xml_location")){
		$handle=opendir($temp_zip_xml_location);
		while (false!==($file = readdir($handle))) {
			if ($file != "." && $file != "..") {
				unlink("$temp_zip_xml_location/$file");				
			}
		}
		closedir($handle);
		}
	}
	echo json_encode(array('success' => true));
	exit;
}


// File to show from the bulk ( Zip File )
$fileToShow = (isset($_REQUEST['fileToShow']) && $_REQUEST['fileToShow']) ? trim($_REQUEST['fileToShow']) : false;

// Random string generation for file names
function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}

$ccda_file 		= str_replace('//','/',$ccda_file);
$ccda_file_base = str_replace('//','/',$ccda_file_base);
$arrName   		= explode("/",$ccda_file);
$file_name 		= end($arrName);

libxml_use_internal_errors(true);

if(strpos($file_name,".zip") !== false){
	$zip = new ZipArchive;
	if($zip->open($ccda_file_base) == TRUE){
		for($i=0; $i<$zip->numFiles; $i++){
			$name = $zip->getNameIndex($i);
			if(strpos(strtolower($name),".xml") !== false && strpos(strtolower($name),"metadata.xml") === false){
				if(!isset($tmp_arr['content']) || $tmp_arr['content']==''){
					// If exact single file is requested to show from zip, parse that one & skip all others
					if($fileToShow && $fileToShow != $name) continue;

					$cda_file_contents = $zip->getFromIndex($i);
					$new_temp_ccda_file_name = 'cda'.date('YmdHis').'_'.generateRandomString().'.xml';
					file_put_contents($temp_zip_xml_location.'/'.$new_temp_ccda_file_name,$cda_file_contents);
					$ccda_file = $webDir_path.'/users/UserId_'.$_SESSION['authId'].'/mails/tempunzipped'.'/'.$new_temp_ccda_file_name;
					$ccda_file_base = $dir_path.'/users/UserId_'.$_SESSION['authId'].'/mails/tempunzipped/'.$new_temp_ccda_file_name;
				}
			}
		}
	}
}else{
	$cda_file_contents = utf8_encode(utf8_decode(trim(file_get_contents($ccda_file_base))));
}

$xml = simplexml_load_string($cda_file_contents);

$xml_read_errors = array();
$ccda_xml = false;

if ($xml === false){
	foreach(libxml_get_errors() as $error){
		if(count($xml_read_errors)==0){
			$xml_read_errors = $error;
		}
	}
}else{
	//If XML is valid; check if it is C-CDA ...
//	if(isset($xml->templateId['root'][0]) && $xml->templateId['root'][0]=='2.16.840.1.113883.10.20.22.1.1'){
	if(isset($xml->templateId['root'][0]) && in_array($xml->templateId['root'][0],array('2.16.840.1.113883.10.20.22.1.1','1.2.840.114350.1.72.1.51693', '2.16.840.1.113883.10','2.16.840.1.113883.10.20.22.1.2','2.16.840.1.113883.10.20.22.1.14'))){
		$ccda_xml = true;
		
		/*****NEW CODE TO GET ATTACHEMENT ID***/
		if($ccda_file_path_backup){
			$temp_res = imw_query("SELECT id FROM direct_messages_attachment WHERE complete_path='$ccda_file_path_backup' LIMIT 0,2");
			if($temp_res && imw_num_rows($temp_res)==1){
				$temp_rs = imw_fetch_assoc($temp_res);
				$_SESSION['opened_attachment_id'] = $temp_rs['id'];
				imw_query("UPDATE direct_messages_attachment SET is_cda='1' WHERE is_cda='0' AND id = '".$temp_rs['id']."' LIMIT 1");
			}else{
				$_SESSION['opened_attachment_id'] = 0;
			}
		}
		/*****NEW CODE END********************/
	}	
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="CDA Document Viewer">
	<title>HL7 C-CDA Viewer</title>
	<link rel="stylesheet" type="text/css" media="all" href="<?php echo $GLOBALS['webroot']; ?>/library/cda_viewer/css/cda.css" />
	<link rel="stylesheet" type="text/css" media="all" href="<?php echo $GLOBALS['webroot']; ?>/library/cda_viewer/css/pure-min.css" />
	<link rel="stylesheet" type="text/css" media="all" href="<?php echo $GLOBALS['webroot']; ?>/library/cda_viewer/css/font-awesome.css">
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo $GLOBALS['webroot']; ?>/library/cda_viewer/css/marketing.css">
	<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/jquery.min.1.12.1.js"></script>	
	<script src="<?php echo $GLOBALS['webroot']; ?>/library/cda_viewer/js/core.js"></script>
	<script src="<?php echo $GLOBALS['webroot']; ?>/library/cda_viewer/js/packery.pkgd.min.js"></script>
	<script src="<?php echo $GLOBALS['webroot']; ?>/library/cda_viewer/js/draggabilly.pkgd.min.js"></script>
	<script src="<?php echo $GLOBALS['webroot']; ?>/library/cda_viewer/js/xslt/xslt.js"></script>
	<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/console.js"></script>
    <script type="text/javascript">
   // cdaxml_new = '../../library/cda_viewer/170.315_b2_ciri__r21_sample1_rn_recon_v9.xml';
	var cdaxml_new = '<?php echo $ccda_file;?>';
	var cdaxml_xsl = '<?php echo $GLOBALS['webroot']; ?>/library/cda_viewer/cda.xsl?<?php echo filemtime($GLOBALS['fileroot'].'/library/cda_viewer/cda.xsl');?>';
	$(document).ready(function() {
		$('#btn_val2').hide();
		<?php if($ccda_xml){?>
	    new Transformation().setXml(cdaxml_new).setXslt(cdaxml_xsl).transform("viewcda");
    	<?php }?>
	});
	
	</script>
    <style type="text/css">
	.button-success,
        .button-error,
        .button-warning,
        .button-secondary {
            color: white;
            border-radius: 4px;
            text-shadow: 0 1px 1px rgba(0, 0, 0, 0.2);
        }

        .button-success {
            background: rgb(28, 184, 65); /* this is a green */
        }

        .button-error {
            background: rgb(202, 60, 60); /* this is a maroon */
        }

        .button-warning {
            background: rgb(223, 117, 20); /* this is an orange */
        }

        .button-secondary {
            background: rgb(66, 184, 221); /* this is a light blue */
        }
	</style>
  
</head>
<body>
<div align="right" style="margin:10px; display:none;" id="div_button_con"><button class="pure-button" onClick="check_validation_status('<?php echo trim($_GET['ccda_file']);?>', this,$('#btn_val2'));" title="Click to validate this clinical document." id="btn_val1"/>VALIDATE</button><button class="pure-button" onClick="view_ccd_validation_details('<?php echo trim($_GET['ccda_file']);?>', this,$('#btn_val1'));" title="Click to Details." id="btn_val2"/>View Details</button>&nbsp;&nbsp;&nbsp;</div>
<div class="cdaview" id="viewcda">
	<p align="center">
	<?php if(!$ccda_xml){echo 'Invalid XML data. Not parseable.';}else{?>
	<img src="<?php echo $GLOBALS['webroot']; ?>/library/images/loading.gif" align="top"> Loading, please wait...
    <?php }?>
  	</p>
</div>
</body>
</html>
