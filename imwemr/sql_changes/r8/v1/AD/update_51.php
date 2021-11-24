<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
//include("../../../../library/classes/common_function.php");

$hm = constant('HASH_METHOD');

$q = "SELECT id,password FROM users WHERE (LENGTH(password) != 32 AND LENGTH(password) != 64) AND password != ''";
$res = imw_query($q);
$status = array();
if($res && imw_num_rows($res)>0){
	$status['found'] = imw_num_rows($res);	

	//CREATING TABLE BACKUP.
	$new_backuptable = "users_".date('YmdHis');
	$back_res1 = imw_query("CREATE TABLE `".$new_backuptable."` LIKE `users`");
	$back_res2 = imw_query("INSERT INTO `".$new_backuptable."` (SELECT * FROM `users`)");		

	while($rs = imw_fetch_assoc($res)){
		$rec_id	= $rs['id'];
		$old_pw = $rs['password'];
		$new_pw = hashPassword($old_pw);
		//echo $old_pw.' :: '.$new_pw.'<br>';
		$q_update = "UPDATE users SET password = '$new_pw' WHERE id = '$rec_id' LIMIT 1";
		$res_update = imw_query($q_update);
		if($res_update && imw_affected_rows()==1){
			$status['corrected'][] = '1';
		}
	}
	
		
}

?>
<html>
<head>
<title>Update 51</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<h3>New User Login Bugfix</h3>
Plain passwords found: <?php echo intval($status['found']);?><br>
Corrections done for: <?php echo count($status['corrected']);?><br>

<h3 style="color:#060"><?php if(intval($status['found'])==count($status['corrected'])){echo 'SUCCESSFULL!';}?></h3>
<h1 style="color:#F00"><?php if(intval($status['found'])>count($status['corrected'])){echo 'FAILED! NEED MANUAL CHECK.';}?></h1>    
</body>
</html>