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
$contentArr = $return_arr = $tmp_arr = array();
$ccda_file = trim($_GET['ccda_file']);
$check_xsl = trim($_GET['check_xsl']);
$force_ccd_viewer = trim($_GET['force_ccd_viewer']);

if($_GET['source']=='tempunzipped'){
	$ccda_file = $dir_path.'/users/UserId_'.$_SESSION['authId'].'/mails/tempunzipped/'.$ccda_file;
}else{
	$ccda_file = $dir_path.'/users'.$ccda_file;
}

$arrName = explode("/",$ccda_file);
$file_name = end($arrName);

if(strpos($file_name,".zip") !== false){
	$folder_name = str_replace(".zip","",$file_name);
	$zip = new ZipArchive;
	if($zip->open($ccda_file) == TRUE){
		for($i=0; $i<$zip->numFiles; $i++){
			$name = $zip->getNameIndex($i);
			if(strpos(strtolower($name),".xml") !== false || strpos(strtolower($name),".txt") !== false){
				if(!isset($tmp_arr['content']) || $tmp_arr['content']==''){
					$tmp_arr['content'] = $zip->getFromIndex($i);
				}
			}
		}
	}
}else{
	$tmp_arr['content'] = utf8_encode(utf8_decode(trim(file_get_contents($ccda_file))));
}

/******CHECK IF CONTENT IS VALID XML*******/
//Store errors in memory rather than outputting them
libxml_use_internal_errors(true);
$xml = simplexml_load_string($tmp_arr['content']);

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
	if(isset($xml->templateId['root'][0]) && in_array($xml->templateId['root'][0],array('2.16.840.1.113883.10.20.22.1.1','1.2.840.114350.1.72.1.51693', '2.16.840.1.113883.10','2.16.840.1.113883.10.20.22.1.2','2.16.840.1.113883.10.20.22.1.14'))){
		$ccda_xml = true;
	}
}

$xml_read_errors = array();
if((count($xml_read_errors)==0 && $ccda_xml) || $force_ccd_viewer=='yes'){
	if($check_xsl != 'check_xsl' || $force_ccd_viewer=='yes'){
		require_once('../../library/cda_viewer/index.php'); die;
		/*
		$proc = new XSLTProcessor();
		$proc->importStylesheet(DOMDocument::loadXML(file_get_contents('CDA.xsl'))); //load XSL script
		echo $xml_doc = $proc->transformToXML(DOMDocument::loadXML($tmp_arr['content']));
		*/
	}else{
		$xml = new DOMDocument;
		$xml->loadXML($tmp_arr['content']);
		$xpath = new DOMXpath($xml);
		$stylesheet = $xpath->query('//processing-instruction()[name() = "xml-stylesheet"]');
		if(isset($stylesheet->item(0)->nodeValue)){
			$href_str = stristr($stylesheet->item(0)->nodeValue,'href=');
			$href_arr = explode('"',$href_str);
			for($i;$i<count($href_arr);$i++){
				$cda_stylesheet = $href_arr[$i];
				if(strtolower(substr($cda_stylesheet,-3))=='xsl'){
					break;
				}else{
					$cda_stylesheet = '';
				}
			}
			if($cda_stylesheet==''){
				echo '<h3>No CDA stylesheet link found in XML data</h3>';
				echo '<h4>You may view this document using <a href="cda_viewer.php?ccda_file='.$_GET['ccda_file'].'&source='.$_GET['source'].'">CDA viewer</a>.</h4>';
				echo '<pre>'.htmlentities($tmp_arr['content']).'</pre>';
			}else{
				$cda_root = pathinfo($ccda_file,PATHINFO_DIRNAME).'/';
				if(substr($cda_stylesheet,0,4)=='http'){
					$cda_stylesheet_data = file_get_contents($cda_stylesheet);
				}else if(file_exists($cda_root.$cda_stylesheet) && is_file($cda_root.$cda_stylesheet)){
					$cda_stylesheet_data = file_get_contents($cda_stylesheet);	
				}
				if($cda_stylesheet_data && strlen($cda_stylesheet_data)>100){
					$proc = new XSLTProcessor();
					$proc->importStylesheet($xml->loadXML($cda_stylesheet_data)); //load XSL script
					echo $xml_doc = $proc->transformToXML($xml->loadXML($tmp_arr['content']));
				}else{
					echo '<h3>Bad CDA stylesheet reference found in XML data</h3>';
					echo '<h4>You may view this document using <a href="cda_viewer.php?ccda_file='.$_GET['ccda_file'].'&source='.$_GET['source'].'">CDA viewer</a>.</h4>';
					echo '<pre>'.htmlentities($tmp_arr['content']).'</pre>';
				}
			}
			
		}else{
			echo '<h3>No CDA stylesheet reference found in XML data</h3>';
			echo '<h4>You may view this document using <a href="cda_viewer.php?ccda_file='.$_GET['ccda_file'].'&source='.$_GET['source'].'">CDA viewer</a>.</h4>';
			echo '<pre>'.htmlentities($tmp_arr['content']).'</pre>';
		}
		
	}
}else if(count($xml_read_errors)==0 && !$ccda_xml){
	echo '<h3>Document not looks like a valid CDA Document</h3>';
	echo '<h4>You may try to view this document using <a href="cda_viewer.php?ccda_file='.$_GET['ccda_file'].'&source='.$_GET['source'].'&force_ccd_viewer=yes">CDA viewer</a>.</h4>';
	echo '<pre>'.htmlentities($tmp_arr['content']).'</pre>';
}else if(count($xml_read_errors)>0){
	?><!DOCTYPE html>
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
	<table class="table">
	<thead><tr class="bg-danger"><th colspan="2" class="text-danger">XML Parsing Error!</th></tr></thead>
	<tbody>
	<?php
		foreach($xml_read_errors as $k=>$v){
			if($k!='message' && $k!='line') continue;
			echo '<tr><th>'.ucwords($k).'</th><td>'.$v.'</td></tr>';
		}?>
	</tbody></table>
	</body>
	</html>
	<?php 
}
?>
