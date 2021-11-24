<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
session_start();
//include_once("../../../../../../common/conDb.php");
//include_once($GLOBALS['incdir']."/admin/manage_folder/folder_function.php");

/**
 * JUBaseListener
 * 
 * These scripts are not for re-distribution and for use with JUpload only.
 * 
 * If you want to use these scripts outside of its JUpload-related context,
 * please write a mail and check back with us @ info@jupload.biz
 * 
 * @author Dominik Seifert, dominik.seifert@smartwerkz.com
 * @copyright Smartwerkz, Haller Systemservices: www.jupload.biz
 */

include_once(dirname(__FILE__) . "/../inc/jupload.inc.php");

class JUBaseListener{

	/**
	 * Upload starts here.
	 * The given $sessionId indicates to which JUpload-session the following files belong.
	 * Everytime JUpload restarts the sessionId will be resetted.
	 * 
	 * @param $sessionId Unique identifier for each JUpload-runtime.
	 */
	function onStart($sessionId) {
		die("JUBaseListener::onStart() not implemented by inheriting class: " . get_class($this));
	}


	/**
	 * Checks the file with the given $relativePath for validity.
	 * Should notify the client and return false in case that the given name is not valid.
	 * 
	 * @param string $tempPath The temporary location of the received file in case of POST (null in case of PUT).
	 * 
	 * @return Wether or not the given file may be stored.
	 */
	function checkValid($relativePath, $tempPath = null) {
		die("JUBaseListener::checkValidity() not implemented by inheriting class: " . get_class($this));
	}


	/**
	 * The given file has been received. You can now check in this method if it matches your sepcifications.
	 *
	 * @param String $filepath Path to the file within the local filesystem.
	 * @param String $relativepath Relative path within the uploader's tree structure.
	 */
	function onReceived($filepath, $relativepath, $isThumb) {
		die("JUBaseListener::onReceived() not implemented by inheriting class: " . get_class($this));
	}

	/**
	 * All files of the current session have been received.
	 */
	function finished() {
		die("JUBaseListener::finished() not implemented by inheriting class: " . get_class($this));
	}
}

?>