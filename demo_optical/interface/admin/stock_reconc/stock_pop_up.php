<?php 
require_once(dirname('__FILE__')."/../../../config/config.php");
require_once(dirname('__FILE__')."/../../../library/classes/functions.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Stock Batches</title>
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<style>
table {
	margin: 10px 0 0 0;
	width: 100%
}
table tr:nth-child(even)
{
	background:#F7F7F7;
}
.paginate {
	margin: 20px 0 0 370px !important;
}
a {
	text-decoration: none !important;
}
</style>
<script>
function get_batch(batch_id,status)
{
	window.opener.location.href="index.php?batch="+batch_id+"&status="+status;
	window.close(this);
}

</script>
</head>
<body>
<div class="listheading">Batches</div>
<?php 
$user_table=imw_query("select * from users where id=".$_SESSION['authId']);
$row=imw_fetch_array($user_table);
$priv=$row['access_pri'];
$priv_find=unserialize(html_entity_decode($priv));
if($priv_find['priv_admin']==1)
{
	{
$query=imw_query("select * from in_batch_table where facility='".$_SESSION["pro_fac_id"]."'");
$total_pages = 1;
if($query){
			$total_pages = imw_num_rows($query);
		}
$limit=15;
$page = imw_escape_string($_GET['page']);
					
					if($page){
						$start = ($page - 1) * $limit; 
					}else{
						$start = 0;	
					}
					
$query1=imw_query("select `batch`.*, `location`.`loc_name` from `in_batch_table` `batch` LEFT JOIN `in_location` `location` ON(`batch`.`facility`=`location`.`id`) WHERE `batch`.`facility`='".$_SESSION["pro_fac_id"]."' order by `batch`.`id` desc limit $start, $limit");

$nums = imw_num_rows($query1);					
$i=1;
if(imw_num_rows($query1)>0)
{
	echo '<table border="1" style="border-collapse:collapse;text-align:center;border:1px solid #E8E8E8 !important;">
  <tr class="listheading">
    <th>S.No.</th>
    <th>Batch Saved On</th>
    <th>User Name</th>
	<td>Status</td>
	<td>Facility</td>
  </tr>';

while($row=imw_fetch_array($query1)){
$date=date("m-d-Y",strtotime($row['save_date']));
$time=date("h:i:s",strtotime($row['save_date']));
?>
<tr>
  <td><a href="#" onclick="get_batch(<?php echo $row['id'].",'".$row['status']."'" ?>)"><?php echo $row['id'];?></a></td>
  <td><a href="#" onclick="get_batch(<?php echo $row['id'].",'".$row['status']."'" ?>)"><?php echo $date." ".$time;?></a></td>
  <?php $a=imw_query("select * from users where id=".$row['user_id']."");
  $user=imw_fetch_array($a);
  ?>
  <td><a href="#" onclick="get_batch(<?php echo $row['id'].",'".$row['status']."'" ?>)"><?php echo $user['lname'].", ". $user['fname']?></a></td>
  <?php  
  $status=""; if($row['status']=='saved')
  {
	  $status="In Progress";
  }
  else
  {
	  $status="Reconciled";
  }
  ?>
  <td><a href="#" onclick="get_batch(<?php echo $row['id'].",'".$row['status']."'" ?>)"><?php echo $status; ?></a></td>
  <td><a href="#" onclick="get_batch(<?php echo $row['id'].",'".$row['status']."'" ?>)"><?php echo $row['loc_name']; ?></a></td>
</tr>
<?php $i++;
}
}
else
{
	echo "<p style='width:90%;margin:10px 0 0 10px;float:left;color:#F00;font-weight:bold'>No batches available</p>";
}?>
</table>
<?php 

}
}
else
{
	$query=imw_query("select * from in_batch_table where user_id=".$_SESSION["authId"]." and facility='".$_SESSION["pro_fac_id"]."'");
$total_pages = 1;
if($query){
			$total_pages = imw_num_rows($query);
		}
$limit=15;
$page = imw_escape_string($_GET['page']);
					
					if($page){
						$start = ($page - 1) * $limit; 
					}else{
						$start = 0;	
					}
					
$query1=imw_query("select `batch`.*, `location`.`loc_name` from `in_batch_table` `batch` LEFT JOIN `in_location` `location` ON(`batch`.`facility`=`location`.`id`) where `batch`.`user_id`=".$_SESSION["authId"]." AND `batch`.`facility`='".$_SESSION["pro_fac_id"]."' order by `batch`.`id` desc limit $start, $limit");

$nums = imw_num_rows($query1);					
$i=1;
if(imw_num_rows($query1)>0)
{
	echo '<table border="1" style="border-collapse:collapse;text-align:center;border:1px solid #E8E8E8 !important;">
  <tr class="listheading">
    <th>S.No.</th>
    <th>Batch Saved On</th>
    <th>User Name</th>
	<td>Status</td>
	<td>Facility</td>
  </tr>';

while($row=imw_fetch_array($query1)){
$date=date("m-d-Y",strtotime($row['save_date']));
$time=date("h:i:s",strtotime($row['save_date']));
?>
<tr>
  <td><a href="#" onclick="get_batch(<?php echo $row['id'].",'".$row['status']."'" ?>)"><?php echo $row['id'];?></a></td>
  <td><a href="#" onclick="get_batch(<?php echo $row['id'].",'".$row['status']."'" ?>)"><?php echo $date." ".$time;?></a></td>
  <?php $a=imw_query("select * from users where id=".$row['user_id']."");
  $user=imw_fetch_array($a);
  ?>
  <td><a href="#" onclick="get_batch(<?php echo $row['id'].",'".$row['status']."'" ?>)"><?php echo $user['lname'].", ". $user['fname']." - ".$user['id']?></a></td>
  <?php  
  $status=""; if($row['status']=='saved')
  {
	  $status="Pending";
  }
  else
  {
	  $status=ucwords($row['status']);
  }
  ?>
  <td><a href="#" onclick="get_batch(<?php echo $row['id'].",'".$row['status']."'" ?>)"><?php echo $status; ?></a></td>
  <td><a href="#" onclick="get_batch(<?php echo $row['id'].",'".$row['status']."'" ?>)"><?php echo $row['loc_name']; ?></a></td>
</tr>
<?php $i++;
}
}
else
{
	echo "<p style='width:90%;margin:10px 0 0 10px;float:left;color:#F00;font-weight:bold'>No batches available</p>";
}?>
</table>
<?php
}
		$targetpage = "stock_pop_up.php";
		$stages = 3;
		require_once'../paging_new.php';
	?>
</body>
</html>