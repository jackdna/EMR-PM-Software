<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
/**
 * JUpload-PUT Handler
 * 
 * These scripts are not for re-distribution and for use with JUpload only.
 * 
 * If you want to use these scripts outside of its JUpload-related context,
 * please write a mail and check back with us @ info@jupload.biz
 * 
 * @author Dominik Seifert, dominik.seifert@smartwerkz.com
 * @copyright Smartwerkz, Haller Systemservices: www.jupload.biz
 */

// Include a file which provides several helper functions and is configured through the jupload.cfg.php
include_once(dirname(__FILE__) . "/inc/jupload.inc.php");

// Upload is starting
$_ju_listener->onStart($_SERVER["HTTP_X_JUPLOAD_ID"]);

// The filename and relative path within the Upload-Tree (eg. "/my documents/important/Laura.jpg")
$relativePath = $_SERVER["HTTP_X_JUPLOAD_RELATIVEFILENAME"];

// Do we have a valid file?
if (isSavePath($relativePath) && $_ju_listener->isValid($filename)) {

	// Do we have a thumbnail? If it is not a thumbnail, it is a regular file.
	$isThumbs = $_SERVER["HTTP_X_JUPLOAD_THUMBNAIL"];
	
	// Where to save the file? Determine the target-directory, depending on if it is a thumbnail or a file
	$filepath = $_ju_uploadRoot . ($isThumb ? $_ju_thumbDir : $_ju_fileDir) . "/$relativePath";
	
	// Create all necessary directories
	$filepath = normalize($filepath);
	mkdirs(dirname($filepath));
	
	
	// Access the file being sent
	$in = fopen("php://input","r") or $in = fopen("php://stdin", "r") or die("Could not access input-stream.");
	
	// Resume old file?
	if (file_exists($filepath)) {
		// access the local file
		$out = fopen($filepath, 'rw+') or die("Could not open output file \"$filepath\". Do you have sufficient rights?");
		$cr = $_SERVER['HTTP_CONTENT_RANGE'];
		if ($cr != null && substr($cr,0,6)=='bytes ') {
			$pos1 = strpos($cr,'-');
			$pos2 = strpos($cr,'/');
			$offset = substr($cr, 6, $pos1-6);
			//$max = substr($cr,$pos1+1,$pos2-$pos1-1);
			//$total = substr($cr,$pos2+1);
			fseek($out , $offset) == 0 or die("Could not seek to offset: " . $offset);
		}
	}
	
	// Create a new file
	else {
		$out = fopen($filepath, 'wb') or die("Could not open output file \"$filepath\". Do you have sufficient rights?");
		$offset = 0;
	}
	
	
	// Write the file and count the bytes
	$total = 0;
	while ($data = fread($in , 1024) && (!isset($_ju_maxSize) || $total < $_ju_maxSize)) {
		$total += fwrite($out , $data);
	}
	
	// Finished -> Close the file
	fclose($in);
	fclose($out);
	
	$fsize = filesize($filepath);
	$total = $offset + $total;
	
	// Correct file size: Notify event
	if ($fsize == $total) {
		$_ju_listener->onReceived($filepath, $relativePath, $isThumbs);
	}
	
	// Incorrect file size
	die("Sizes do not match for " . $relativePath . " - Filesize: $fsize; Offset: $offset; Received: $total => $fsize != " . $total);
}

$_ju_listener->finished();
?>