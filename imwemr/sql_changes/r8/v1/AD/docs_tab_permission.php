<?php
$ignoreAuth = true;
include("../../../../config/globals.php");

$status = imw_query("CREATE TABLE users_bkup_before_docs_priv_April2018 AS SELECT * FROM users");

$documents = array();
$documents['priv_document'] = 1;
$documents['priv_admn_docs_Collection'] = 1;
$documents['priv_admn_docs_Consent'] = 1;
$documents['priv_admn_docs_Consult'] = 1;
$documents['priv_admn_docs_Education'] = 1;
$documents['priv_admn_docs_Instructions'] = 1;
$documents['priv_admn_docs_Logos'] = 1;
$documents['priv_admn_docs_Op_Notes'] = 1;
$documents['priv_admn_docs_Package'] = 1;
$documents['priv_admn_docs_Panels'] = 1;
$documents['priv_admn_docs_Prescriptions'] = 1;
$documents['priv_admn_docs_Pt_Docs'] = 1;
$documents['priv_admn_docs_Recalls'] = 1;
$documents['priv_admn_docs_Scan_Upload_Folders'] = 1;
$documents['priv_set_margin'] = 1;
$documents['priv_admn_docs_Smart_Tags'] = 1;
$documents['priv_admn_docs_Statements'] = 1;

$counter=0;

//$query=imw_query("Select id,access_pri from users where id=1");
$query_rs=imw_query("Select id,access_pri from users where modified_on<'2018-04-13' ");
//echo imw_num_rows($query_rs); die;
while ($row = imw_fetch_assoc($query_rs)) {
    $user_priviliges=unserialize(html_entity_decode(trim($row['access_pri'])));

    $privileges=array();
    $temp=array();
    foreach($user_priviliges as $key => $val) {
        $privileges[$key]=$val;
    }
    $privileges = array_merge($privileges, $documents);
    
    $rq = "UPDATE users SET access_pri = '".serialize($privileges)."' WHERE id = '".$row['id']."'";
    $rq_obj = imw_query($rq);
    $counter++;
}

$msg_info[]= "<b>Update done for $counter users. </b><br />";
$color = "green";


?>
<html>
<head>
<title>Enable Document Tab</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>