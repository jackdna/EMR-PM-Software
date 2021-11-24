<?php
$ignoreAuth = true;
include("../../../../config/globals.php");

$arr_users=array();
$counter=0;
$query_rs=imw_query("Select id,access_pri from users where delete_status=0 ") or $msg_info[] = imw_error();

while($row=imw_fetch_assoc($query_rs)){
	$user_id=$row['id'];
	$user_priv=($row['access_pri']);
	$userpriviliges=unserialize(html_entity_decode($user_priv));
	if(count($userpriviliges)>0 && $userpriviliges['priv_Billing']==1){
		$arr_users[$user_id]=$userpriviliges;
	}
}
if(empty($arr_users)==false) {
    foreach($arr_users as $user_id => $user_privliges){
        if(trim($user_privliges['priv_Billing'])==1){
            $user_privliges['priv_bi_day_chrg_rept']=1;

            $user_grant_priviliges=htmlentities(serialize($user_privliges));
            $qryUpdate="UPDATE users set access_pri='".$user_grant_priviliges."' where id='".$user_id."' ";
            $resUpdate=imw_query($qryUpdate);
            if($resUpdate){
                $counter++;
            }
        }
    }
}

$msg_info[] ="<br><b>Total Record Updated: ".$counter."</b>";
$msg_info[] = "<br><b>Release :<br> Update Success.</b>";

$color = "green";	

?>
<html>
<head>
<title>Update 73</title>
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