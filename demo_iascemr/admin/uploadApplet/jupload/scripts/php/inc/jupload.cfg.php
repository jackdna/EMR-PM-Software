<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?
//include_once(dirname(__FILE__)."/../../../../../../globals.php");		
//include_once($GLOBALS['incdir']."/admin/manage_folder/folder_function.php");

/**
 * jupload.cfg.php
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
/**
 * This config defines the defaults (if not defined before) of the following variables which are needed by JUpload:
 *
 * @var JUBaseListener $_ju_listener The listener for the current Upload session.
 * 
 * @var string $_ju_uploadRoot The root-directory for the files to be uploaded to.
 * 
 * @var string $_ju_fileDir The sub-directory under the $_ju_uploadRoot where the actual files will be stored.
 * 
 * @var string $_ju_thumbDir The sub-directory under the $_ju_uploadRoot where the thumbnails will be stored.
 * 
 * @var int $_ju_maxSize is not set by default but, if set, determines the maximum of bytes that should be received by the PUT-received-handler
 */

if (!$_ju_listener) {
	require_once dirname(__FILE__) . "/../listener/JUDefaultListener.class.php";
	
	$_ju_listener = new JUDefaultListener();
}

if (!isset($_ju_uploadRoot)) {
	$_ju_uploadRoot = dirname(__FILE__) . "/../../../uploaded/";
}

if (!isset($_ju_fileDir)) {
	$_ju_fileDir = "files";
}

if (!isset($_ju_thumbDir)) {
	$_ju_thumbDir = "thumbnails";
}


?>