<?php

/*
 * Filenme: autoload.php
 * This is PSR-0 compliant autoloader function.
 * It is basically used to load Klein php router library for REST API
 **/
$ignoreAuth = true;
require_once("../config/globals.php");
 
 
function autoload($className)
{
	$className = ltrim($className, '\\');
    $fileName  = '';
    $namespace = '';
    if ($lastNsPos = strrpos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
	
    require $fileName;
}
$data = spl_autoload_register('autoload');

/*
 * Sub-directory configuraton for Klein router library  
 **/
//$base  = dirname($_SERVER['PHP_SELF']);
//if(ltrim($base, '/')){
//	$_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], strlen($base));	
//}

$base  = $_SERVER['REQUEST_URI'];
if(ltrim($base, '/')){
	
	$base = substr($_SERVER['REQUEST_URI'], strpos($base, 'IMWAPI')+6);
	
	if( strpos($base, '?') > 1 )
		$base = stristr($base, '?', true);
	
	$base = trim($base, '/');
	$base = '/'.$base;
	
	$_SERVER['REQUEST_URI'] = $base.'?'.$_SERVER['QUERY_STRING'];
}

?>