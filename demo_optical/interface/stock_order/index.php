<?php 
/*
File: index.php
Coded in PHP7
Purpose: Main Stock Screen
Access Type: Direct access
*/
require_once("../../config/config.php");
$_SESSION['default_tab']="stock";
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
        <li class="li_select" style="width:100px;"><a class="a_select" id="link1" href="#tab1">Inventory</a></li>
    </ul>            

     <div class="tab_container" style="float:left; width:100%;">
        <div id="tab1" class="tab_content">
            <div class="icons">
                <ul style="float:left;border-bottom:1px dotted #6ab8e6;width:100%;">
                	<li><a href="../admin/frame/index.php" class="text_purpule" onClick="top.WindowDialog.closeAll();"><img border="0" src="../../images/fram_icon.jpg" /><br />Frames</a></li> 
       				<li><a href="../admin/lens/index.php" class="text_purpule" onClick="top.WindowDialog.closeAll();"><img border="0" src="../../images/lense.jpg" /><br />Lenses</a></li> 
                    <li><a href="../admin/contact_lens/index.php" class="text_purpule" onClick="top.WindowDialog.closeAll();"><img border="0" src="../../images/contact_lense.jpg" /><br />Contact Lenses</a></li> 
                    <li><a href="../admin/supplies/index.php" class="text_purpule" onClick="top.WindowDialog.closeAll();"><img border="0" src="../../images/supplies_icon.jpg" /><br />Supplies</a></li> 
                    <!--<li><a href="../admin/medicines/index.php" class="text_purpule" onClick="top.WindowDialog.closeAll();"><img border="0" src="../../images/medicines1.jpg" /><br />Medicines</a></li>-->
					<li><a href="../admin/medicines/index_new.php" class="text_purpule" onClick="top.WindowDialog.closeAll();"><img border="0" src="../../images/medicines1.jpg" /><br />Medicines</a></li>
                    <li><a href="../admin/accessories/index.php" class="text_purpule" onClick="top.WindowDialog.closeAll();"><img border="0" src="../../images/accessories.jpg" /><br />Accessories</a></li>
                	<li><a href="../admin/stock_search.php?open=front" class="text_purpule" onClick="top.WindowDialog.closeAll();"><img border="0" src="../../images/sea_icon.jpg" /><br />Search</a></li>
					<li><a href="../admin/stock_reconc/index.php" class="text_purpule" onClick="top.WindowDialog.closeAll();"><img border="0" src="../../images/Stock_Check.jpg" style="width:79px;border:1px solid #999;border-radius:5px;height:79px" /><br />
Stock Reconciliation</a></li>
					<li><a href="../admin/stock_reconc/missing_stock.php" class="text_purpule" onClick="top.WindowDialog.closeAll();"><img border="0" src="../../images/Stock_Check.jpg" style="width:79px;border:1px solid #999;border-radius:5px;height:79px" /><br />
Missing Stock </a></li>
					<li><a href="../admin/stock_reconc/stock_print.php" class="text_purpule" onClick="top.WindowDialog.closeAll();"><img border="0" src="../../images/print_st.png" style="width:59px;border:1px solid #999;border-radius:5px;height:59px;padding:10px" /><br />
Printing</a></li>
                </ul> 
                       
            </div>
       </div>
    </div>    
    <iframe src="about:blank" name="admin_iframe" id="admin_iframe" style="width:100%; height:<?php echo $_SESSION['wn_height']-350;?>px; margin:0px; display:none; overflow:hidden;" frameborder="0" framespacing=0 scrolling="no"></iframe>  
</body>
</html>    
    
