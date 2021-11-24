<?php 
/*
File: index.php
Coded in PHP7
Purpose: Order Tracking Screen
Access Type: Direct access
*/
require_once("../../config/config.php");
$_SESSION['default_tab']="order";
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0; charset=UTF-8" />
<title>Optical</title>
<link rel="stylesheet" href="../../library/css/inv_css.css?<?php echo constant("cache_version"); ?>" type="text/css" />
<script src="../../library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script type="text/javascript">
$(document).ready(function(e) {
	//BUTTONS
	var mainBtnArr = new Array();
	top.btn_show("admin",mainBtnArr);		

	//Default Action
	$(".tab_content").hide(); //Hide all content
	$("ul.tabs li:first").addClass("li_select").show(); //Activate first tab
	$(".tab_content:first").show(); //Show first tab content
	
	//On Click Event
	$("ul.tabs li").click(function() {
		if($(".tab_container").css('display')=='none'){
			$('#admin_iframe').attr('src','about:blank').hide();
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
		top.btn_show("admin",mainBtnArr);		
		return false;
	});
	
	$(".tab_container div.tab_content ul li a").click(function(){
		href = $(this).attr('href');
		$(".tab_container").hide();
		$('#admin_iframe').attr('src',href).fadeIn();
		//BUTTONS
		top.btn_show("admin",mainBtnArr);		
				
		return false;
	});
	
	
	
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
</script>
</head>
<body>
    <ul class="tabs" style="width:100%;">
        <li class="li_select" style="width:138px;"><a class="a_select" id="link1" href="#tab1">Order Tracking</a></li>
    </ul>
     <div class="tab_container" style="float:left; width:100%;">
       <div id="tab1" class="tab_content" style="display:none;">
            <div class="icons">
                <ul style="float:left;border-bottom:1px dotted #6ab8e6;width:100%;">
                	<li><a href="../admin/order/index.php?order=all&rec_type=all" class="text_purpule" onClick="top.WindowDialog.closeAll();"><img border="0" src="../../images/view_all_icon.jpg" /><br />View All</a></li> 
                    <li><a href="../admin/order/index.php?order=pending&rec_type=pending" class="text_purpule" onClick="top.WindowDialog.closeAll();"><img border="0" src="../../images/Pending_icon.jpg" /><br />Pending</a></li> 
                    <li><a href="../admin/order/index.php?order=ordered&rec_type=ordered" class="text_purpule" onClick="top.WindowDialog.closeAll();"><img border="0" src="../../images/ordered_icon.jpg" /><br />Ordered</a></li> 
                    <li><a href="../admin/order/index.php?order=received&rec_type=received" class="text_purpule" onClick="top.WindowDialog.closeAll();"><img border="0" src="../../images/received_icon.jpg" /><br />Received</a></li>
					<li><a href="../admin/order/index.php?order=notified&rec_type=notified" class="text_purpule" onClick="top.WindowDialog.closeAll();"><img border="0" src="../../images/notified_icon.jpg" /><br />Notified</a></li>
                    <li><a href="../admin/order/index.php?order=dispensed&rec_type=dispensed" class="text_purpule" onClick="top.WindowDialog.closeAll();"><img border="0" src="../../images/dispensed_icon.jpg" /><br />Dispensed</a></li> 
					<li><a href="../admin/order/remove_order.php" class="text_purpule" onClick="top.WindowDialog.closeAll();"><img border="0" src="../../images/remove_icon.jpg" /><br />Removed</a></li> 
                    <li><a href="../admin/order/index.php?order=archived&rec_type=archived" class="text_purpule" onClick="top.WindowDialog.closeAll();" ><img border="0" src="../../images/archieved-2.jpg" style="width: 69px;float: left;border: 1px solid #CCC;height: 69px;border-radius: 4px;box-shadow: 1px 1px 0px #999;padding:5px;margin: 0 0 5px 4px;"/><br />Archived</a></li>      				
                </ul>            
            </div>
       </div>
    </div>    
    <iframe src="about:blank" name="admin_iframe" id="admin_iframe" style="width:100%; height:<?php echo $_SESSION['wn_height']-350;?>px; margin:0px; display:none; overflow:hidden;" frameborder="0" framespacing=0 scrolling="no"></iframe>  
</body>
</html>    
    
