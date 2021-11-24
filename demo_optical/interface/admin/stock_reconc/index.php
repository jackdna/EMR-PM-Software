<?php
/*pro_fac_id*/
require_once(dirname('__FILE__')."/../../../config/config.php");
require_once(dirname('__FILE__')."/../../../library/classes/functions.php");

/*Get Facility Name*/
	$location_name = "";
	$resp_fac = imw_query("SELECT `loc_name` FROM `in_location` WHERE `id`='".$_SESSION["pro_fac_id"]."'");
	if($resp_fac && imw_num_rows($resp_fac)>0){
		$location_name_data = imw_fetch_object($resp_fac);
		$location_name = $location_name_data->loc_name;
	}
	$user_qry=imw_query("select * from users where id=".$_SESSION['authId']."");
  	$user_row=imw_fetch_array($user_qry);
	$user_name=$user_row['lname'].", ". $user_row['fname'];
/*End Get Facility Name*/

?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Optical</title>
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script type="text/javascript">
var WEB_PATH='<?php echo $GLOBALS['WEB_PATH'];?>';
$(document).ready(function(){
	var mainBtnArr = new Array();
	mainBtnArr[0] = new Array("frame","Create New Batch","top.main_iframe.admin_iframe.show_recon();");
	mainBtnArr[1] = new Array("frame","Archive","top.main_iframe.admin_iframe.move_to_archive();");
	top.btn_show("admin",mainBtnArr);
	
	$("#selectallArc").click(function(){		
		if($(this).is(":checked")){
			$(".archive_item").prop('checked', true);
		}else{
			$(".archive_item").prop('checked', false);
		}
	});
});
function show_recon()
{
	//window.location.href=WEB_PATH+'/interface/admin/stock_reconc/create_batch.php';
	var width=window.outerWidth-50;
	var height=window.outerHeight-70;
	//$('#loading').show();
	window.open(WEB_PATH+"/interface/admin/stock_reconc/create_batch.php",' Batch Detail','width='+width+',height='+height);
}
function move_to_archive()
{
	var batch_id="";
	var len=document.getElementsByName('archive_item[]').length;
	var item1=document.getElementsByName('archive_item[]');
	for(var i=0;i<len;i++)
	{
		if(item1[i].checked==true)
		{
			batch_id+=(item1[i].value)+",";
		}
	}
	if(batch_id=="")
	{
		top.falert("Please Select record(s) to Archive");
		return false;
	}
	batch_id=batch_id.substring(0, ((batch_id.length)-1));
	$('#loading').show();
	$.ajax({
		type:"POST",
		url:"ajax.php",
		data:"archive=1&batch_ids="+batch_id,
		success: function(msg)
		{
			if(msg)
			{
				//reload page						
			}
		},
		complete: function(){
			//reload page
			window.location.href=WEB_PATH+'/interface/admin/stock_reconc/index.php';
		}
	});
}
function reloadWithChange()
{
	$('#loading').show();
	var showArchive=$("#show_archive").prop('checked');
	window.location.href=WEB_PATH+'/interface/admin/stock_reconc/index.php?show_archive='+showArchive;
}
function get_batch(batch_id,status,page)
{
	var width=window.outerWidth-50;
	var height=window.outerHeight-70;
	var page_url='';
	if(page)page_url=page;
	else page_url='create_batch_old.php';
	//$('#loading').show();
	window.open(WEB_PATH+"/interface/admin/stock_reconc/"+page_url+"?batch="+batch_id+"&status="+status,' Batch Detail','width='+width+',height='+height);
}
function show_alert_div(a)
{
	$('.err_div').css({"display":"block","color":"#F00","width":"200px","position":"absolute","left": "430px","top":"-5px"});
	$(".err_div").html("Data "+a+" Successfully");
	$('.err_div').delay(3000).hide(1);
}

</script>
<style>
a{text-decoration: none !important;}
</style>
</head>
<body>
<div class="mt10 rec_con">
  <div class="listheading" style="margin-bottom:5px;">Stock Reconciliation - <?php echo $location_name; ?>
  <div style="float: right"><label><input type="checkbox" name="show_archive" id="show_archive" value="1" onChange="reloadWithChange();" <?php echo($_REQUEST['show_archive']=='true')?'checked':'';?>>Include Archived</label></div>
  </div>
</div>
<div class="err_div"></div>
<table style="border-collapse:collapse;text-align:center;width:100%;">
<?php 
	$del_check='AND  bt.del_status=0';
	if($_REQUEST['show_archive']=='true')$del_check='';
	  $sql="SELECT 
				`bt`.*, 
				CONCAT(`u`.`lname`, ', ', `u`.`fname`) AS 'username', 
				lot.wholesale_price as wholesale_cost, 
				`br`.`prev_tot_qty`, 
				`br`.`in_item_quant`, 
				`br`.`in_fac_prev_qty`,
				`br`.`in_fac_lot_prev_qty`,
				`br`.`lot_no`
			FROM 
				`in_batch_table` `bt` 
				LEFT JOIN `in_batch_records` `br` ON (`bt`.`id` = `br`.`in_batch_id`) 
				LEFT JOIN `in_item` `i` ON (`br`.`in_item_id` = `i`.`id`)  
				LEFT JOIN `in_item_lot_total` `lot` ON (br.in_item_id=lot.item_id and br.lot_no=lot.lot_no)
				LEFT JOIN `users` `u` ON(`u`.`id` = `bt`.`user_id`) 
			WHERE 
				`bt`.`facility` = '".$_SESSION["pro_fac_id"]."' 
				$del_check
			ORDER BY 
				`bt`.`save_date` DESC";
	$query_batches=imw_query($sql)or die(imw_error());
	while($row_bat=imw_fetch_array($query_batches))
		{
			$mod_date="";
			$save_date=date("m-d-Y h:i:s",strtotime($row_bat['save_date']));
			if($row_bat['updated_date']!='0000-00-00 00:00:00')
			{
				$mod_date=date("m-d-Y h:i:s",strtotime($row_bat['updated_date']));
			}
			$compare_qty=($row_bat['in_fac_lot_prev_qty']>0)?$row_bat['in_fac_lot_prev_qty']:$row_bat['in_fac_prev_qty'];
			$qty=$row_bat['in_item_quant']-$compare_qty;
			$batch_arr[$row_bat['id']]['save_date']=$save_date;
			$batch_arr[$row_bat['id']]['status']=$row_bat['status'];
			$batch_arr[$row_bat['id']]['del_status']=$row_bat['del_status'];
			$batch_arr[$row_bat['id']]['user_name']=$row_bat['username'];
			$batch_arr[$row_bat['id']]['mod_date']=$mod_date;
			$batch_arr[$row_bat['id']]['org_amt']+=($row_bat['wholesale_cost']*$compare_qty);
			$batch_arr[$row_bat['id']]['adj_amt']+=($row_bat['wholesale_cost']*$qty);
			$batch_arr[$row_bat['id']]['lot_wise_check']=$row_bat['lot_no'];//this is just to check is that lot wise batch or not
		}
	if(imw_num_rows($query_batches)>0){
		echo '<tr class="listheading">
		<th style="width:50px;"><input type="checkbox" name="selectallArc" id="selectallArc" style="display: none"><label for="selectallArc">S.No</label></th>
		<th style="width:210px;">Batch Created On</th>
		<th style="width:230px;">User Name</th>
		<th style="width:170px;">Original Amt.</th>
		<th style="width:140px;">Adj. Amt.</th>
		<th style="width:190px;">Reconciled Amt.</th>
		<th style="width:150px;">Facility</th>
		<th style="width:140px;">Status</th>
		<th style="width:210px;">Batch Modified On</th></tr></table></div>
		<div style="height:'.($_SESSION['wn_height']-428).'px;overflow-y:scroll;overflow-x:hidden;"><table border="1" style="border-collapse:collapse;text-align:center;border:1px solid #E8E8E8 !important;width:100%;margin-top:0px;"><tr><th style="width: 59px;"></th>
		<th style="width: 186px;"></th>
		<th style="width: 171px;"></th>
		<th style="width: 153px;"></th>
		<th style="width: 124px;"></th>
		<th style="width: 178px;"></th>
		<th style="width: 135px;"></th>
		<th style="width: 113px;"></th>
		<th style="width: 191px;"></th>
		</tr>';
		$i=1; 
		foreach($batch_arr as $batch_key=>$batch_val)
		{
			$batch_id=$batch_key;
			$save_date=$batch_arr[$batch_key]['save_date'];
			$mod_date=$batch_arr[$batch_key]['mod_date'];
			$org_amt=$batch_arr[$batch_key]['org_amt'];
			$adj_amt=$batch_arr[$batch_key]['adj_amt'];
			$db_status=$batch_arr[$batch_key]['status'];
			$del_status=$batch_arr[$batch_key]['del_status'];
			$batch_user_name=$batch_arr[$batch_key]['user_name'];
			$lot_wise_check=$batch_arr[$batch_key]['lot_wise_check'];
			
			$page=$status=""; 
			$page=($lot_wise_check>0)?"create_batch.php":"create_batch_old.php";
			if($db_status=='saved')
			{
				$status="In Progress";
			}
			else
			{
				$status="Reconciled";
			}
			$class='';
			if($del_status==1)$class='archived';
	?>
		 <tr class="<?php echo $class;?>">
		  <td><label style="cursor: pointer"><input type='checkbox' class='archive_item' name='archive_item[]' value='<?php echo $batch_id;?>'><?php echo $i;?></label></td>
		  <td><a href="#" onclick="get_batch(<?php echo $batch_id.",'".$db_status."','$page'"; ?>)"><?php echo $save_date;?></a></td>
		  <td><a href="#" onclick="get_batch(<?php echo $batch_id.",'".$db_status."','$page'"; ?>)"><?php echo $batch_user_name;?></a></td>
		  <td style="text-align:right; padding-right:5px;"><a href="#" onclick="get_batch(<?php echo $batch_id.",'".$db_status."','$page'"; ?>)"><?php echo number_format($org_amt,2); ?></a></td>
		  <td style="text-align:right; padding-right:5px;"><a href="#" onclick="get_batch(<?php echo $batch_id.",'".$db_status."','$page'"; ?>)"><?php echo number_format($adj_amt,2); ?></a></td>
		  <td style="text-align:right; padding-right:5px;"><a href="#" onclick="get_batch(<?php echo $batch_id.",'".$db_status."','$page'"; ?>)"><?php echo number_format(($org_amt+$adj_amt),2); ?></a></td>
		  <td><a href="#" onclick="get_batch(<?php echo $batch_id.",'".$db_status."','$page'"; ?>)"><?php echo $location_name; ?></a></td>
		  <td><a href="#" onclick="get_batch(<?php echo $batch_id.",'".$db_status."','$page'"; ?>)"><?php echo $status; ?></a></td>
		  <td><a href="#" onclick="get_batch(<?php echo $batch_id.",'".$db_status."','$page'"; ?>)"><?php echo $mod_date;?></a></td>
		</tr>
	 <?php
		$i++;
		}
	}
 ?>
 </table>
<div id="loading" style="display:none;">
    <img src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/loading_image.gif" />
</div>
<?php
if($_REQUEST['batch_status']!=''){
	echo"<script>show_alert_div('".$_REQUEST['batch_status']."');</script>";
}
?>
</body>
</html>