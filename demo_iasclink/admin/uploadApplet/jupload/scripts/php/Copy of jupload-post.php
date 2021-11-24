<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
/**
 * JUpload-Post Handler
 * 
 * These scripts are not for re-distribution and for use with JUpload only.
 * 
 * If you want to use these scripts outside of its JUpload-related context,
 * please write a mail and check back with us @ info@jupload.biz
 * 
 * @author Dominik Seifert, dominik.seifert@smartwerkz.com
 * @copyright Smartwerkz, Haller Systemservices: www.jupload.biz
 */

global $_ju_listener, $_ju_uploadRoot, $_ju_fileDir, $_ju_thumbDir, $_ju_maxSize;

// Include a file which provides several helper functions and is configured through the jupload.cfg.php
include_once(dirname(__FILE__) . "/inc/jupload.inc.php");

// Upload is starting
$_ju_listener->onStart($_SERVER["HTTP_X_JUPLOAD_ID"]);

/**
 * Iterate over all received files.
 */
foreach($_FILES as $tagname=>$fileinfo) {
	// get the name of the temporarily saved file (e.g. /tmp/php34634.tmp)
	$tempPath = $fileinfo['tmp_name'];

	// The filename and relative path within the Upload-Tree (eg. "/my documents/important/Laura.jpg")
	$relativePath = $_POST[$tagname . '_relativePath'];
	
	// Do we have a valid file?
	if (!checkSavePath($relativePath) || !$_ju_listener->checkValid($relativePath, $tempPath)) {
		continue;
	}
	
	$files[$relativePath] = $tempPath;
}

if ($files) {
	foreach ($files as $relativePath => $tempPath)  {
		// Do we have a thumbnail? If it is not a thumbnail, it is a regular file.
		$isThumb = $_POST[$tagname . '_thumbnail'];
	
		// Where to save the file? Determine the target-directory, depending on if it is a thumbnail or a file
		$filepath = $_ju_uploadRoot . ($isThumb ? $_ju_thumbDir : $_ju_fileDir) . "/$relativePath";
	
		// Create folders
		mkdirs(dirname($filepath = normalize($filepath)));
		
		// Move the temporary file to the target directory
		move_uploaded_file($tempPath, $filepath) or die("Error while moving temporary file to target path: " . $relativePath);
		
		// Tell the listener that another file has successfully been received.
		$_ju_listener->onReceived($filepath, $relativePath, $isThumbs);
	}
}

$_ju_listener->finished();
?>