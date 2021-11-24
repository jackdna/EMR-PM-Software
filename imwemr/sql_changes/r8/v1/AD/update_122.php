<?php
$ignoreAuth = true;
include("../../../../config/globals.php");

$status = imw_query("CREATE TABLE users_bkup_before_update_122 AS SELECT * FROM users");
$status1 = imw_query("CREATE TABLE groups_prevlgs_bkup_before_update_122 AS SELECT * FROM groups_prevlgs");

$arr_users=array();
$counter=0;
$query_rs=imw_query("Select id,access_pri from users where delete_status=0 ") or $msg_info[] = imw_error();
while($row=imw_fetch_assoc($query_rs)){
	$user_id=$row['id'];
	$user_priv=($row['access_pri']);
	$userpriviliges=unserialize(html_entity_decode($user_priv));
    if($userpriviliges['priv_admin'] == 1 || $userpriviliges['priv_admin_billing'] == 1){
        $arr_users[$user_id]=$userpriviliges;
    }
}

if(empty($arr_users)==false) {
    foreach($arr_users as $user_id => $user_priviliges){
        $user_priviliges['priv_Office_Hours_Settings'] = 0;
        $user_priviliges['priv_billing_Payment_Methods'] = 0;
        $user_priviliges['priv_billing_Manage_POS'] = 0;
        if($user_priviliges['priv_admin'] == 1){
            $user_priviliges['priv_Office_Hours_Settings'] = 1;
        }
        if($user_priviliges['priv_admin_billing'] == 1){
            $user_priviliges['priv_billing_Payment_Methods'] = 1;
            $user_priviliges['priv_billing_Manage_POS'] = 1;
        }
        if($user_priviliges){
            $user_grant_priviliges=htmlentities(serialize($user_priviliges));
            $qryUpdate="UPDATE users set access_pri='".$user_grant_priviliges."' where id='".$user_id."' ";
            $resUpdate=imw_query($qryUpdate);
            if($resUpdate){
                $counter++;
            }
        }
    }
}

$grup_prevs=array();
$counter1=0;
$query_rs1=imw_query("Select id,prevlgs from groups_prevlgs where deleted_by=0 ") or $msg_info[] = imw_error();
while($row=imw_fetch_assoc($query_rs1)){
	$grup_id=$row['id'];
	$grup_priv=$row['prevlgs'];
	$gruppriviliges=unserialize(html_entity_decode($grup_priv));
    if($gruppriviliges['priv_admin'] == 1 || $gruppriviliges['priv_admin_billing'] == 1){
        $grup_prevs[$grup_id]=$gruppriviliges;
    }
}

if(empty($grup_prevs)==false) {
    foreach($grup_prevs as $grup_id => $grup_priviliges){
        $grup_priviliges['priv_Office_Hours_Settings'] = 0;
        $grup_priviliges['priv_billing_Payment_Methods'] = 0;
        $grup_priviliges['priv_billing_Manage_POS'] = 0;
        if($grup_priviliges['priv_admin'] == 1){
            $grup_priviliges['priv_Office_Hours_Settings'] = 1;
        }
        if($grup_priviliges['priv_admin_billing'] == 1){
            $grup_priviliges['priv_billing_Payment_Methods'] = 1;
            $grup_priviliges['priv_billing_Manage_POS'] = 1;
        }
        if($grup_priviliges){
            $grup_grant_priviliges=serialize($grup_priviliges);
            $grupqryUpdate="UPDATE groups_prevlgs set prevlgs='".$grup_grant_priviliges."' where id='".$grup_id."' ";
            $grupresUpdate=imw_query($grupqryUpdate);
            if($grupresUpdate){
                $counter1++;
            }
        }
    }
}

$msg_info[] ="<br><b>Total User Record Updated: ".$counter."</b>";
$msg_info[] ="<br><b>Total Group Record Updated: ".$counter1."</b>";
$msg_info[] = "<br><b>Release :<br> Update Success.</b>";

$color = "green";	

?>
<html>
<head>
    <title>Update 122</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br>
<br>
    <font face="Arial, Helvetica, sans-serif" color="<?php echo $color;?>" size="2">
        <?php echo(implode("<br>",$msg_info));?>
    </font>
</body>
</html>