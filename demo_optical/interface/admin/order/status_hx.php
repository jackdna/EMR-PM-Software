<?php
	/*
	File: status_hx.php
	Purpose: view Order Status hx
	Access Type: Direct access
	*/
	require_once(dirname('__FILE__')."/../../../config/config.php");
	require_once(dirname('__FILE__')."/../../../library/classes/functions.php");
	$patient_id=$_SESSION['patient_session_id'];
	$order_id=$_REQUEST['ord_id'];
	$disp_qry=imw_query("select sts.*, in_item.name, concat(users.lname,', ',users.fname) as user_name from in_order_detail_status sts 
	left join in_item on sts.item_id=in_item.id 
	left join users on sts.operator_id=users.id 
	where order_id=$order_id order by id desc");// where order_status='dispensed'
	

?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0; charset=UTF-8" />
<title>Optical</title>
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<style type="text/css">
.delRow{text-decoration:line-through;color:#F00}
tr td{vertical-align:top;}
</style>
<!--for fancy models-->
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.modal.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.modal.theme-xenon.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.modal.theme-atlant.css?<?php echo constant("cache_version"); ?>" />
<!--for fancy models end here-->

<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/common.js?<?php echo constant("cache_version"); ?>"></script>

<!--for fancy models-->
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.modal.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.modal.function.js?<?php echo constant("cache_version"); ?>"></script>
<!--for fancy models end here-->
</head>

<body>
	<div style="width:100%; margin:0 auto;">
       <div class="module_border">
        <?php
		 if(imw_num_rows($disp_qry)>0){
		?>
            <table class="table_collapse table_cell_padd2">
             <tr class="listheading">
             	<td>Sr.</td>
             	<td>Item</td>
             	<td>Date</td>
             	<td>User</td>
             	<td>Status</td>
			 </tr>
    		 <?php
			 while($disp_data=imw_fetch_array($disp_qry)){$counter++;
			?>
     		 <tr>
             	<td><?php echo $counter;?></td>
             	<td><?php echo $disp_data['name'];?></td>
             	<td><?php echo getDateFormat($disp_data['order_date']).' '.$disp_data['order_time'];?></td>
             	<td><?php echo $disp_data['user_name'];?></td>
             	<td><?php echo $disp_data['order_status'];?></td>
			 </tr>
    	<? }?>
     	</table>
         <?php }else{echo'No Record Found.';} ?>   
         </div>
         
       </div>
       
    
    
</body>
</html>