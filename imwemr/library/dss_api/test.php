<?php
$ignoreAuth = true;

include_once(dirname(__FILE__)."/../../config/globals.php");
require_once(dirname(__FILE__)."/../../library/dss_api/dss_api.php");

$objDss_api=new Dss_api();

if(isset($_GET['method']) && $_GET['method']!='') {
    switch($_GET['method']) {
        case 'auth':
            include_once(dirname(__FILE__)."/library/dss_api/dss_api.php");
            $objDss_api=new dss_api();
            
            $return=$objDss_api->auth();
            
            pre($return); die;
            break;

    }
}
?>