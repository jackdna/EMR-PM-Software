<?php 
/*
File: index.php
Coded in PHP7
Purpose: Main Report Screen
Access Type: Direct access
*/
require_once("../../config/config.php");
$_SESSION['default_tab']="report";
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1" charset="UTF-8" />
<title>Optical</title>
<link rel="stylesheet" href="../../library/css/inv_css.css?<?php echo constant("cache_version"); ?>" type="text/css" />
<script src="../../library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script type="text/javascript">
$(document).ready(function(e) {
	//Default Action
	$(".tab_content").hide(); //Hide all content
	$("ul.tabs li:first").addClass("li_select").show(); //Activate first tab
	$(".tab_content:first").show(); //Show first tab content
	
	//On Click Event
	$("ul.tabs li").click(function() {
		if($(".tab_container").css('display')=='none'){
			$('#reports_iframe').attr('src','about:blank').hide();
			$(".tab_container").fadeIn();
		}
		$("ul.tabs li").removeClass("li_select"); //Remove any "active" class
		$("ul.tabs li a").removeClass("a_select"); //Remove any "active" class
		$(this).addClass("li_select"); //Add "active" class to selected tab
		$(".tab_content").hide(); //Hide all tab content
		var activeTab = $(this).find("a").attr("href"); //Find the rel attribute value to identify the active tab + content
		var activeTabLink = $(this).find("a").attr("id"); //Find the rel attribute value to identify the active tab + content
		$(activeTab).fadeIn(); //Fade in the active content
		$("#"+activeTabLink).addClass('a_select');
		
		//BUTTONS
		var mainBtnArr = new Array();
		top.btn_show("admin",mainBtnArr);
		
		return false;
	});
	
	$(".tab_container div.tab_content ul li a").click(function(){
		href = $(this).attr('href');
		$(".tab_container").hide();
		$('#reports_iframe').attr('src',href).fadeIn();
		
		//BUTTONS
		var mainBtnArr = new Array();
		top.btn_show("admin",mainBtnArr);
		
		return false;
	});
	
	
	//BUTTONS
	var mainBtnArr = new Array();
	top.btn_show("admin",mainBtnArr);
});

$(document).unbind('keydown').bind('keydown', function (event) {
    var doPrevent = false;
    if (event.keyCode === 8) {
        var d = event.srcElement || event.target;
        if ((d.tagName.toUpperCase() === 'INPUT' && (d.type.toUpperCase() === 'TEXT' || d.type.toUpperCase() === 'PASSWORD' || d.type.toUpperCase() === 'FILE')) 
             || d.tagName.toUpperCase() === 'TEXTAREA') {
            doPrevent = d.readOnly || d.disabled;
        }
        else {
            doPrevent = true;
        }
    }

    if (doPrevent) {
        event.preventDefault();
    }
});

function loading(mode){
	$('#loading').css('display', mode);
}
</script>
</head>
<body>
<div id="loading" style="display:none;">
    <img src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/loading_image.gif" />
</div>

    <ul class="tabs" style="width:100%;">
        <li class="li_select" style="width:80px;"><a class="a_select" id="link1" href="#tab1">Reports</a></li>
    </ul>            

     <div class="tab_container" style="float:left; width:100%;">
        <div id="tab1" class="tab_content">
            <div class="icons">
                <ul style="float:left;border-bottom:1px dotted #6ab8e6;width:100%;">
					<li><a href="stock_in_hand.php" class="text_purpule"><img border="0" src="../../images/reports/stock.jpg" /><br />Valuation Report</a></li> 
					<li><a href="stock_ledger.php" class="text_purpule"><img border="0" src="../../images/Stock_Ledger_icon.jpg" /><br />Stock Ledger</a></li>
					 <li><a href="general_inventory_control.php" class="text_purpule"><img border="0" src="../../images/reports/stock_ledger.jpg" /><br />General Inventory control</a></li>
					 <li><a href="day_report.php" class="text_purpule"><img border="0" src="../../images/Day_Report_icon.jpg" /><br />Day Report</a></li> 
					  <li><a href="ordered.php" class="text_purpule"><img border="0" src="../../images/Patient_Orders_icon.jpg" /><br />Patient Orders</a></li>
                
					<li><a href="item_profile.php" class="text_purpule"><img border="0" src="../../images/item_profile_icon.jpg" /><br />Item Profile</a></li>
					<li><a href="day_order_report.php" class="text_purpule"><img border="0" src="../../images/day_order_icon.jpg" /><br />Day Order Report</a></li>
					<li><a href="capture_report.php" class="text_purpule"><img border="0" src="../../images/capture_report.png" /><br />Capture Report</a></li>
				</ul>
				<ul style="float:left;">
					<li><a href="medication.php" class="text_purpule"><img border="0" src="../../images/Medications-report.jpg" /><br />Medication</a></li>
                    <li><a href="medication_orders.php" class="text_purpule"><img border="0" src="../../images/Medications-report.jpg" /><br />Medication Orders</a></li>
					<li><a href="lab_orders.php" class="text_purpule"><img border="0" src="../../images/Medications-report.jpg" style="width: 77px;float: left;border: 1px solid #CCC;height: 77px;border-radius: 4px;box-shadow: 1px 1px 0px #999;padding:1px;margin-bottom:8px;" /><br />Lab Orders</a></li>                    
					<li><a href="return_order.php" class="text_purpule"><img border="0" src="../../images/Return_Order_icon.jpg" /><br />Return Orders</a></li>
					<li><a href="remake_return.php" class="text_purpule"><img border="0" src="../../images/remake.jpg" style="width: 77px;float: left;border: 1px solid #CCC;height: 77px;border-radius: 4px;box-shadow: 1px 1px 0px #999;padding:1px;margin-bottom:8px;" /><br />Remake & Return</a></li>
	                <li><a href="stock_recon.php" class="text_purpule"><img border="0" src="../../images/stock_reconciliation.jpg" style="width: 77px;float: left;border: 1px solid #CCC;height: 77px;border-radius: 4px;box-shadow: 1px 1px 0px #999;padding:1px;margin-bottom:8px;" /><br />Stock Reconciliation</a></li>    
					<li><a href="order_post_status.php" class="text_purpule"><img border="0" src="../../images/posted_order.png" /><br />Posted/Not Posted orders</a></li>
					<li><a href="cost_of_goods_report.php" class="text_purpule"><img border="0" src="../../images/cost_of_goods_report.png" /><br />Cost of Goods Report</a></li>
                    <!--<li><a href="dead_stock_report.php" class="text_purpule"><img border="0" src="../../images/noimage_icon.jpg" /><br />Dead Stock</a></li>-->
                    <!--<li><a href="vendor_report.php" class="text_purpule"><img border="0" src="" style="width: 77px;float: left;border: 1px solid #CCC;height: 77px;border-radius: 4px;box-shadow: 1px 1px 0px #999;padding:1px;margin-bottom:8px;" /><br />Vendor Report</a></li>-->
                </ul>     
            </div>
       </div>
     
        <div id="tab2" class="tab_content" style="display:none;">
            <div class="icons">
                <ul style="float:left;border-bottom:1px dotted #6ab8e6;width:100%;">            
               
                </ul>            
            </div>
       </div>
    </div>      
    <iframe src="about:blank" name="reports_iframe" id="reports_iframe" style="width:100%; height:<?php echo $_SESSION['wn_height']-350;?>px; margin:0px; display:none; overflow:hidden;" frameborder="0" framespacing=0 scrolling="no"></iframe>  
</body>
</html>    
    
