<?php
/*
File: cont_lens_sync.php
Coded in PHP7
Purpose: Sync Contact Lens manufacturers and Styles from iDoc 
Access Type: Direct access
*/
require_once(dirname('__FILE__')."/../../../config/config.php");
require_once(dirname('__FILE__')."/../../../library/classes/functions.php");
$msg_stat = "none";
$opr_id = $_SESSION['authId'];
$date = date("Y-m-d");
$time = date("h:i:s");

?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Optical</title>
<link rel="stylesheet" href="../../../library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<script src="../../../library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<style type="text/css">
..container1{color:#333333;}
.container1>div{padding-left: 10px;}
h3{margin:0;}
h4{margin: 5px 0;}
.left{width:200px;}
.right{width:500px;}
.left, .right{display:inline-block;}
</style>
</head>
<body>
<?php
	$details_count = array('manufacturers'=>0, 'styles'=>0, 'items'=>0);
	$count_sql = "SELECT COUNT(`id`) AS 'count', 'manufacturers' AS 'label' FROM `in_manufacturer_details` WHERE `cont_lenses_chk`=1 AND `del_status`=0
				UNION
				SELECT COUNT(`id`) AS 'count', 'styles' AS 'label' FROM `in_contact_brand` WHERE `del_status`=0
				UNION
				SELECT COUNT(`id`) AS 'count', 'items' AS 'label' FROM `in_item` WHERE `module_type_id`=3 AND `del_status`=0";
	$count_resp = imw_query($count_sql);
	if($count_resp && imw_num_rows($count_resp)>0){
		while($row = imw_fetch_object($count_resp)){
			$details_count[$row->label] = $row->count;
		}
	}
	imw_free_result($count_resp);

?>
<div class="container1">
	<div class="listheading">
    	<h3>Contact Lens Details</h3>
    </div>
    <div>
    	<div class="left">
			<h4>Manufacturers</h4>
        </div>
        <div class="right">
	        <spam style="font-weight:bold;"><?php echo $details_count['manufacturers']; ?></span>
        </div>
    </div>
    <div>
    	<div class="left">
			<h4>Styles</h4>
        </div>
        <div class="right">
	        <spam style="font-weight:bold;"><?php echo $details_count['styles']; ?></span>
        </div>
    </div>
    <div>
    	<div class="left">
			<h4>Items</h4>
        </div>
        <div class="right">
	        <spam style="font-weight:bold;"><?php echo $details_count['items']; ?></span>
        </div>
    </div>
</div>
<div id="loading" style="display:none;">
    <img src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/loading_image.gif" />
</div>
<script type="text/javascript">
function idocSync(){
	
	
	if($("#loading").css('display')!="none"){
		return;
	}
	
	var params = {};
	params.action = "iDocSync";
	
	$.ajax({
		method: 'POST',
		data: params,
		url: top.WRP+'/interface/admin/ajax.php',
		beforeSend: function(){
			$("#loading").show();
		},
		success: function(){
			document.location.reload();
		}
	});
}

$(document).ready(function(){
	
	var mainBtnArr = new Array();
	mainBtnArr[0] = new Array("frame","iDoc Sync","top.main_iframe.admin_iframe.idocSync();");
	top.btn_show("admin",mainBtnArr);
});
</script>
</body>
</html>