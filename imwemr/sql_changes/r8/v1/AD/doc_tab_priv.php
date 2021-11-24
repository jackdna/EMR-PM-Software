<?php
$ignoreAuth = true;
include("../../../../config/globals.php");
        
$counter=0;
$query_rs=imw_query("Select id,access_pri from users where delete_status=0 ");
while ($row = imw_fetch_assoc($query_rs)) {
    $user_priviliges=array();
    $user_priviliges=unserialize(html_entity_decode(trim($row['access_pri'])));
    $user_priviliges['priv_document'] = 1;
    $user_priviliges['priv_admn_docs_Collection'] = 1;
    $user_priviliges['priv_admn_docs_Consent'] = 1;
    $user_priviliges['priv_admn_docs_Consult'] = 1;
    $user_priviliges['priv_admn_docs_Education'] = 1;
    $user_priviliges['priv_admn_docs_Instructions'] = 1;
    $user_priviliges['priv_admn_docs_Logos'] = 1;
    $user_priviliges['priv_admn_docs_Op_Notes'] = 1;
    $user_priviliges['priv_admn_docs_Package'] = 1;
    $user_priviliges['priv_admn_docs_Panels'] = 1;
    $user_priviliges['priv_admn_docs_Prescriptions'] = 1;
    $user_priviliges['priv_admn_docs_Pt_Docs'] = 1;
    $user_priviliges['priv_admn_docs_Recalls'] = 1;
    $user_priviliges['priv_admn_docs_Scan_Upload_Folders'] = 1;
    $user_priviliges['priv_set_margin'] = 1;
    $user_priviliges['priv_admn_docs_Smart_Tags'] = 1;
    $user_priviliges['priv_admn_docs_Statements'] = 1;

    $rq = "UPDATE users SET access_pri = '".serialize($user_priviliges)."' WHERE id = '".$row['id']."'";
    $rq_obj = imw_query($rq);
    $counter++;
}

echo "Update done for $counter users <br />";



?>