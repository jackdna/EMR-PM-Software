<?php
include_once(dirname(__FILE__) . "/../../../config/globals.php");

$data = array();
$data['success'] = false;
$q = isset($_REQUEST['query']) ? trim($_REQUEST['query']) : '';

if( $q ) {
    $qry = "Select P.id, CONCAT(P.lname,', ',P.fname, ' - ', P.id) as title From patient_data P Where lname like '".$q."%' OR fname like '".$q."%' OR id = '".$q."' "; 
    $sql = imw_query($qry) or die(imw_error());
    $cnt = imw_num_rows($sql);
    if( $cnt ) {
        $data['success'] = true;
        while( $row = imw_fetch_assoc($sql) ){
            $data['mylist'][] = $row;
        }
    }
}

echo json_encode($data);
?>