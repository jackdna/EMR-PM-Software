<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
session_start();
//include_once(dirname(__FILE__)."/../../../../../../globals.php");		
//include_once($GLOBALS['incdir']."/admin/manage_folder/folder_function.php");

/**
 * JUDefaultListener
 * 
 * These scripts are not for re-distribution and for use with JUpload only.
 * 
 * If you want to use these scripts outside of its JUpload-related context,
 * please write a mail and check back with us @ info@jupload.biz
 * 
 * @author Dominik Seifert, dominik.seifert@smartwerkz.com
 * @copyright Smartwerkz, Haller Systemservices: www.jupload.biz
 */

include_once(dirname(__FILE__) . "/JUBaseListener.class.php");

class JUDefaultListener extends JUBaseListener {
	/**
	 * Upload starts here
	 */
	function onStart($sessionId) {
		// Check for valid agent
		$agent = $_SERVER["HTTP_USER_AGENT"];
		if (!strStartsWith($agent, "JUpload")) {
			header("HTTP/1.1 200 Restricted");
			die("Invalid request.");
		}
		echo "<html>";
	}


	/**
	 * @return Wether or not the given file may be stored.
	 */
	function checkValid($relativePath, $tempPath) {
		// Do not allow .htaccess files
		if (strEndsWith($relativePath, ".htaccess")) {
			$path = htmlspecialchars($relativePath, ENT_QUOTES, "UTF-8");
			echo "<div halign=left>File is invalid: $filename</div>";
			
			return false;
		}
		return true;
	}


	/**
	 * The file at the given path with the given relativepath within the uploader's tree structure has been received.
	 *
	 * @param String $filepath
	 * @param String $relativepath
	 */
	function onReceived($filepath, $relativepath, $isThumb) {
		// File received: Success message
		if (!$isThumb) {
			$path = htmlspecialchars($relativepath, ENT_QUOTES, "UTF-8");
			echo "<div halign=left>Saved: $relativepath";
		}
	}
	
	/**
	 * Done...
	 */
	function finished() {
		echo "</html>";
	}
}

?>