<?php 
$ignoreAuth = true;
require_once(dirname('__FILE__')."/../config/config.php");

$sql = $errors = array();
//backup table first
$tb_name="in_optical_order_form_".date('d_M_y');
imw_query("CREATE TABLE $tb_name AS SELECT * FROM in_optical_order_form") or $errors[] = imw_error();

$q=imw_query("select fname, mname, lname, user_npi, id from users") or $errors[] = imw_error();
while($d=imw_fetch_object($q))
{
	$users[$d->id]['fname']=$d->fname;
	$users[$d->id]['mname']=$d->mname;
	$users[$d->id]['lname']=$d->lname;
	$users[$d->id]['user_npi']=$d->user_npi;
}

$rec=0;
$q1=imw_query("select id, physician_id from in_optical_order_form where physician_id!=0 order by id asc") or $errors[] = imw_error();
while($d1=imw_fetch_object($q1))
{
	$user_exit=$whr='';
	if($user_exit=$users[$d1->physician_id])
	{
		/*if($user_exit['user_npi'])
		{
			$whr=" and NPI ='$user_exit[user_npi]'";
		}*/
		if(!$refPhyId[$d1->physician_id])
		{
			$q3=imw_query("select physician_Reffer_id from refferphysician where `LastName` LIKE '$user_exit[lname]' and `FirstName` LIKE '$user_exit[fname]' LIMIT 0,1") or $errors[] = imw_error();
			if(imw_num_rows($q3)>=1)
			{
				$d3=imw_fetch_object($q3);
				$refPhyId[$d1->physician_id]=$d3->physician_Reffer_id;
			}
		}
		
		if($refPhyId[$d1->physician_id]){
			imw_query("update in_optical_order_form set physician_id='".$refPhyId[$d1->physician_id]."' where id=$d1->id") or $errors[] = imw_error();
			$rec++;
		}
	}
}
if(count($errors)>0){
	print "<div style=\"color:red;\"><br><br><pre>";
	print implode("<br>", $errors);
	print "</pre></div>";
}
else{
	echo '<div style="color:green;"><br><br>Update 128 run successfully. '.$rec.' records updated</div>';
}

?>