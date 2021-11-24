<?php 
/*
File: lab_detail.php
Coded in PHP7
Purpose: Show Lab Detail 
Access Type: Direct access
*/
require_once("../../config/config.php");
require_once("../../library/classes/functions.php"); 
$lab_id=$_REQUEST['lab_id'];
$lab_detail_id=$_REQUEST['lab_detail_id'];
$lab_ship_detail_id=$_REQUEST['lab_ship_detail_id'];
$rxCount=$_REQUEST['rxCount'];

/*Get vision web location id to queried*/
$loc_user_id_qry = "";
if(isset($_REQUEST['vw_loc_id']) && trim($_REQUEST['vw_loc_id']) !=''){
	
	$sql_user = "SELECT `id` FROM `in_vision_web` WHERE `vw_loc_id` IN(".trim($_REQUEST['vw_loc_id']).")";
	$resp_user = imw_query($sql_user);
	$loc_user_ids = array();
	if($resp_user && imw_num_rows($resp_user)>0){
		while($row = imw_fetch_assoc($resp_user)){
			array_push($loc_user_ids, $row['id']);
		}
	}
	
	if(count($loc_user_ids)>0){
		$loc_user_id_qry = " AND `in_lens_lab_detail`.`vw_user_id` IN(".implode(',', $loc_user_ids).") ";
	}
}
/*End Get vision web location id to queried*/

$qry=imw_query("select in_lens_lab.lab_name,in_lens_lab_detail.* from in_lens_lab join in_lens_lab_detail on in_lens_lab.id=in_lens_lab_detail.lab_id where (vw_billing_number!='' or vw_shipping_number!='') and in_lens_lab_detail.lab_id='$lab_id'".$loc_user_id_qry." order by in_lens_lab_detail.street_name");
while($row=imw_fetch_array($qry)){
	if($row['vw_billing_number']!=""){
		$lab_data['billing'][]=$row;
	}
	if($row['vw_shipping_number']!=""){
		$lab_data['shipping'][]=$row;
	}
	$lab_name=$row['lab_name'];
}
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Optical</title>
<link rel="stylesheet" href="../../library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<!--for fancy models-->
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.modal.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.modal.theme-xenon.css?<?php echo constant("cache_version"); ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/jquery.modal.theme-atlant.css?<?php echo constant("cache_version"); ?>" />
<!--for fancy models end here-->
<script src="../../library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<!--for fancy models-->
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.modal.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery.modal.function.js?<?php echo constant("cache_version"); ?>"></script>
<!--for fancy models end here-->
<script type="text/javascript">
	window.opener = window.opener.main_iframe.admin_iframe;
	function fillParentForm(){
		var bill_id=$('.lab_billing:checked').val();
		var ship_id=$('.lab_shipping:checked').val();
		if(typeof(bill_id)=="undefined"){bill_id=0;}
		if(typeof(ship_id)=="undefined"){ship_id=0;}
		window.opener.document.getElementById('lab_detail_id_<?php echo $rxCount; ?>_lensD').value=bill_id;
		window.opener.document.getElementById('lab_ship_detail_id_<?php echo $rxCount; ?>_lensD').value=ship_id;
		window.close();
	}
</script>
<style type="text/css">
.prismContainer{
	width:40%;
	display:inline-block;
}
.prismContainer:first-child{
	text-align:right;
	margin-right:10px;
}
.prismContainer:last-child{
	text-align:left;
	margin-left:10px;
}
.custom_rx{
	color: #03F;
    font-weight: bold;
    font-size: 12px;
}
#contextMenu{
	position: absolute;
    top: 0;
    left: 0;
    z-index: 999;
    background-color: #CECECE;
    padding: 5px;
    border: 1px solid #000000;
	display:none;
}
#contextMenu span{
	width: 100%;
    font-weight: bold;
    text-decoration: none;
    font-size: 18px;
    color: #0004B3;
    display: block;
	cursor:pointer;
}
</style>
</head>
<body>
	<div style="padding:0px; width:100%;">
        <div class="listheading">
            <div class="fl">Lens Lab Address</div>
            <div class="fl" style="width:auto; text-align:left; margin-left:180px;"><?php echo $lab_name; ?></div>	
        </div>
        
<?php  
if(imw_num_rows($qry)>0) { ?>
<div style="height:180px; overflow-y:auto" id="lab_listingB">
<table class="table_collapse" width="99.9.5%" id="lab_listing_head">
    <tr class="listheading">
      <td class="alignCenter" style="padding-left:0;width:115px;" colspan="2">Street</td>
      <td class="alignCenter" style="padding-left:0;width:60px;">City</td>
      <td class="alignCenter" style="padding-left:0;width:60px;">State</td>
	  <td class="alignCenter" style="width:60px">ZIP</td>
	  <td class="alignCenter" style="width:100px">VW Billing#</td>
    </tr>
 
<?php
	for($i=0;$i<count($lab_data['billing']);$i++){
		$row_lab=$lab_data['billing'][$i];
?>
    <tr class="cellBorder <?php if($lab_detail_id==$row_lab['id']){echo"reportHeadBG1";}else{"odd";}?>" style="height:22px;">
   	  <td class="alignCenter" style="width:10px"><input type="radio" name="lab_billing" value="<?php echo $row_lab['id'];?>" class="lab_billing" <?php if($lab_detail_id==$row_lab['id']){echo"checked";} ?>></td>
	  <td class="alignLeft" style="width:115px; padding-left:10px;"><?php echo $row_lab['street_name'];?></td>
	  <td class="alignCenter" style="width:60px"><?php echo $row_lab['city'];?></td>
      <td class="alignCenter" style="width:60px"><?php echo $row_lab['state'];?></td>
	  <td class="alignCenter" style="width:60px"><?php echo $row_lab['zip_code'];?></td>
	  <td class="alignCenter" style="width:100px"><?php echo $row_lab['vw_billing_number'];?></td>
    </tr>
    
    <?php 
}
?>
</table>
</div>
<div style="height:180px; overflow-y:auto" id="lab_listingS">
<table class="table_collapse" width="99.9.5%" id="lab_listing_head">
	<tr class="listheading">
	  <td class="alignCenter" style="padding-left:0;width:115px;" colspan="2">Street</td>
	  <td class="alignCenter" style="padding-left:0;width:60px;">City</td>
	  <td class="alignCenter" style="padding-left:0;width:60px;">State</td>
	  <td class="alignCenter" style="width:60px">ZIP</td>
	  <td class="alignCenter" style="width:100px">VW Shipping#</td>
	</tr>

<?php
	for($i=0;$i<count($lab_data['shipping']);$i++){
		$row_lab=$lab_data['shipping'][$i];
?>
    <tr class="cellBorder <?php if($lab_ship_detail_id==$row_lab['id']){echo"reportHeadBG1";}else{"odd";}?>" style="height:22px;">
   	  <td class="alignCenter" style="width:10px"><input type="radio" name="lab_shipping" value="<?php echo $row_lab['id'];?>" class="lab_shipping" <?php if($lab_ship_detail_id==$row_lab['id']){echo"checked";} ?>></td>
	  <td class="alignLeft" style="width:115px; padding-left:10px;"><?php echo $row_lab['street_name'];?></td>
	  <td class="alignCenter" style="width:60px"><?php echo $row_lab['city'];?></td>
      <td class="alignCenter" style="width:60px"><?php echo $row_lab['state'];?></td>
	  <td class="alignCenter" style="width:60px"><?php echo $row_lab['zip_code'];?></td>
	  <td class="alignCenter" style="width:100px"><?php echo $row_lab['vw_shipping_number'];?></td>
    </tr>
    
    <?php 
}
?>
</table>
</div>
<div class="btn_cls">
	<input type="button" name="done" value="Done" onClick="fillParentForm();" />                        
</div>
<?php  }else{ echo '<div style="height:220px;" class="alignCenter" id="lab_not_found">No Record Exists.</div>';}?>


	
 </div>

</body>
</html>