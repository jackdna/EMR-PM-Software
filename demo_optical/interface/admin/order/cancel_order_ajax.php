<?php
	/*
	File: order_ajax.php
	Coded in PHP7
	Purpose: Edit/Save Order Status
	Access Type: Direct access
	*/
	require_once(dirname('__FILE__')."/../../../config/config.php");
	require_once(dirname('__FILE__')."/../../../library/classes/functions.php");
	$patient_id=$_SESSION['patient_session_id'];
	
	$redirectFlag = false;
	if(isset($_REQUEST['source']) && $_REQUEST['source']=="itemDetailPage"){
		$ord_id=$_SESSION['order_id'];
		$redirectFlag = true;
	}
	else{
		$ord_id=substr($_REQUEST['ord_id'],0,strlen($_REQUEST['ord_id'])-1);
	}
	
	if($_POST['order_ids'] || $_REQUEST['ord_id'])
	{
		if(!$_POST['order_ids'])$_POST['order_ids']=$_REQUEST['ord_id'];
		if(!$_POST['reason'])$_POST['reason']=$_REQUEST['reason'];
		$orderid = explode(',',$_POST['order_ids']);

		$operator_id=$_SESSION['authId'];
		$entered_date=date('Y-m-d');
		$entered_time=date('H:i:s');
		$en_date = date("Y-m-d H:i:s");
	
		for($i=0;$i<count($orderid);$i++)
		{
			$order_id=$orderid[$i];
			imw_query("update in_order_details set del_status='1',del_date='$entered_date',del_time='$entered_time',del_operator_id='$operator_id' where order_id='$order_id'");
			
			imw_query("update in_order set del_reason='". imw_real_escape_string($_POST['reason']) ."' where id='$order_id'");
			
			//update qty and price in in_order table
			update_qty_price_order($order_id);
			//update order overall status if all items cancelled
			update_ordre_del_status($order_id);
		}
		
		if($redirectFlag){
			echo "<script>window.opener.main_iframe.".$_REQUEST['frameName'].".parent.location.href='../../patient_interface/index.php'; window.close();</script>";
		}
		else{
			echo "<script>var page_loc=window.opener.main_iframe.".$_REQUEST['frameName'].".location;window.opener.main_iframe.".$_REQUEST['frameName'].".location.href=page_loc; window.close();</script>";
		}
		
	}
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0; charset=UTF-8" />
<title>Optical</title>
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />

<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/common.js?<?php echo constant("cache_version"); ?>"></script>

</head>
<body>
	<div style="width:750px; margin:0 auto;">
    <form name="addframe" id="firstform1" method="post" style="margin:0px;">
       <div class="module_border">
            <div class="listheading"> Cancel Orders # <?php echo $ord_id; ?>
           
            </div>
            
                <strong class="label">Reason:</strong><br>
            <div style="text-align:center">
				<textarea name="reason" id="reason" rows="5" cols="10" style="width:95%"></textarea>
            </div>
       </div>
        <div class="btn_cls mt10">
            <input type="submit" name="save" value="Save" /> 
             <input type="hidden" name="order_ids" value="<?php echo $ord_id; ?>"/>                        
        </div>
        <input type="hidden" name="save" id="save" value="Save" />
        <input type="hidden" name="cancel" id="cancel" value="Cancel" />
        </form>
    </div>
</body>
</html>