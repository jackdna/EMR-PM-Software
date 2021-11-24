<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
include_once(dirname(__FILE__)."/../../../../../../globals.php");		
include_once($GLOBALS['incdir']."/admin/manage_folder/folder_function.php");

/**
 * jupload.inc.php
 * 
 * These scripts are not for re-distribution and for use with JUpload only.
 * 
 * If you want to use these scripts outside of its JUpload-related context,
 * please write a mail and check back with us @ info@jupload.biz
 * 
 * @author Dominik Seifert, dominik.seifert@smartwerkz.com
 * @copyright Smartwerkz, Haller Systemservices: www.jupload.biz
 */

include_once(dirname(__FILE__) . "/jupload.cfg.php");

/**
 * Makes necessary checks for wether this path is save or not.
 * 
 * Paths should always be save or else something is seriously wrong, therefore let the script die in case
 * that the given path is not save.
 */
function checkSavePath($relativePath) {
	if (strStartsWith($relativePath, "../") || strstr($relativePath, "/../") || strstr($relativePath, "/./")) {
		$msg = date("Y m d H:i:s") . "Client submitting illegal path: $relativePath";
		syslog(LOG_WARNING, $msg);
		
		die($msg);
	}
	
	return true;
}

/**
 * Recursively create subfolders
 */
function mkdirs($dir, $dirmode=0711) {
	if (empty($dir)) return;
	if (file_exists($dir)) return;

	preg_match_all('/([^\/]*)\/?/i', $dir, $parts);
	$base='';
	foreach ($parts[0] as $key=>$val) {
		$base = $base.$val;
		if(file_exists($base)) continue;
		if (!mkdir($base,$dirmode)) {
			echo 'Error: Cannot create '.$base;
			return;
		}
	}
	return;
}

/**
 * Remove double slashes and backslashes for better looks.
 */
function normalize($path) {
	$path  = preg_replace("/(\\|\/)+/", '/', $path);
	return $path;
}

/**
 * @return bool Wether or not the MD5-checksum of the file at the given $filePath equals $origHash.
 */
function checkMD5($filePath, $origHash) {
	return md5_file($filePath) == $origHash;
}


/**
 * @return bool Wether or not the given string $str starts with the givens tring $start.
 */
function strStartsWith($str, $start) {
	return substr($str, 0, strlen($start)) == $start;
}


/**
 * @return bool Wether or not the given string $str ends with the givens tring $start.
 */
function strEndsWith($str, $end) {
	return substr($str, -strlen($end), strlen($end)) == $end;
}

?>