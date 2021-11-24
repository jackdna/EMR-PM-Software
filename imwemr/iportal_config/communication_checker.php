<?php

$ignoreAuth=true;
include_once("../config/globals.php");

$action = trim($_REQUEST['action']);

if( $action == 'REQUEST_FROM_IPORTAL' )
{
	$filePath = $GLOBALS['fileroot'].'/data/'.PRACTICE_PATH.'/iportal_reponse.log';
	file_put_contents( $filePath, "1");
}

