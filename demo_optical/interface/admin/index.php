<?php 
/*
File: index.php
Coded in PHP7
Purpose: Admin Main File
Access Type: Direct access
*/
require_once("../../config/config.php");
$_SESSION['default_tab']="admin";
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

		//CLEAR BUTTON ARRAY
		var mainBtnArr = new Array();
		top.btn_show("admin",mainBtnArr);	
		
		return false;
	});
	
	$(".tab_container div.tab_content ul li a").click(function(){
		href = $(this).attr('href');
		$(".tab_container").hide();
		$('#admin_iframe').attr('src',href).fadeIn();

		//CLEAR BUTTON ARRAY
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
</script>
</head>
<body>
    <ul class="tabs" style="width:100%;">
        <li class="li_select" style="width:80px;"><a class="a_select" id="link1" href="#tab1">Frames</a></li>
        <li style="width:80px;"><a id="link2" href="#tab2" onClick="top.WindowDialog.closeAll();">Lenses</a></li>
        <li style="width:130px;"><a id="link3" href="#tab3" onClick="top.WindowDialog.closeAll();">Contact Lenses</a></li>
        <li style="width:100px;"><a id="link4" href="#tab4" onClick="top.WindowDialog.closeAll();">Medicines</a></li>
        <li style="width:80px;"><a id="link5" href="#tab5" onClick="top.WindowDialog.closeAll();">Supplies</a></li>
        <li style="width:80px;"><a id="link6" href="#tab6" onClick="top.WindowDialog.closeAll();">Set Up</a></li>
    </ul>            

     <div class="tab_container" style="float:left; width:100%;">
        <div id="tab1" class="tab_content">
            <div class="icons">
                <ul style="float:left;border-bottom:1px dotted #6ab8e6;width:100%;">
                <li><a href="frame/frame_brand.php?alpha=a" class="text_purpule"><img border="0" src="../../images/brand.jpg" /><br />Brand</a></li> 
                <li><a href="frame/frame_color.php?alpha=a" class="text_purpule"><img border="0" src="../../images/color_icon.jpg" /><br />Color</a></li>
                <li><a href="frame/frame_type.php?alpha=a" class="text_purpule"><img border="0" src="../../images/type_icon.jpg" /><br />Type</a></li>
                <li><a href="frame/frame_shape.php?alpha=a" class="text_purpule"><img border="0" src="../../images/style_icon.jpg" /><br />Shape</a></li>
				 <li><a href="frame/frame_style.php?alpha=a" class="text_purpule"><img border="0" src="../../images/frame_style_icon.jpg" /><br />Style</a></li>
                </ul>
            </div>
       </div>
     
        <div id="tab2" class="tab_content" style="display:none;">
            <div class="icons">
                <ul style="float:left;border-bottom:1px dotted #6ab8e6;width:100%;">            
				<li><a href="lens/lens_design.php?alpha=a" class="text_purpule"><img border="0" src="../../images/noimage_icon.jpg" style="width: 69px;float: left;border: 1px solid #CCC;height: 69px;border-radius: 4px;box-shadow: 1px 1px 0px #999;padding:5px;margin: 0 0 5px 4px;"/><br />Design</a></li>
				<!--<li><a href="lens/lens_type.php?alpha=a" class="text_purpule"><img border="0" src="../../images/lens_type_icon.jpg" /><br />Type</a></li>-->
                <li><a href="lens/lens_material.php?alpha=a" class="text_purpule"><img border="0" src="../../images/material_icon.jpg" /><br />Material</a></li>
                <li><a href="lens/lens_ar.php?alpha=a" class="text_purpule"><img border="0" src="../../images/ar_icon.jpg" /><br />Treatment</a></li>
                <!--<li><a href="lens/lens_transition.php?alpha=a" class="text_purpule"><img border="0" src="../../images/transition_icon.jpg" /><br />Transition</a></li>
                <li><a href="lens/lens_polarized.php?alpha=a" class="text_purpule"><img border="0" src="../../images/polarized.jpg" /><br />Polarized</a></li>                       
                <li><a href="lens/lens_progressive.php?alpha=a" class="text_purpule"><img border="0" src="../../images/progressive_icon.jpg"/><br />Progressive</a></li>                       
                
                <li><a href="lens/lens_color.php?alpha=a" class="text_purpule"><img border="0" src="../../images/color_icon.jpg" /><br />Color</a></li>
                <li><a href="lens/lens_tint.php?alpha=a" class="text_purpule"><img border="0" src="../../images/tint.jpg" /><br />Tint</a></li>
                <!-- <li><a href="lens/lens_labs.php?alpha=a" class="text_purpule"><img border="0" src="../../images/lab.jpg" /><br />Labs</a></li>- ->
				<li><a href="lens/lens_edge.php?alpha=a" class="text_purpule"><img border="0" src="../../images/edge.jpg" /><br />Edge</a></li>-->
				<li><a href="lens/min_seg_ht.php?alpha=a" class="text_purpule"><img border="0" src="../../images/min-seg-ht.jpg" style="width: 69px;float: left;border: 1px solid #CCC;height: 69px;border-radius: 4px;box-shadow: 1px 1px 0px #999;padding:5px;margin: 0 0 5px 4px;"/><br />Min Seg Ht</a></li>
				<li><a href="lens/seg_type.php?alpha=a" class="text_purpule"><img border="0" src="../../images/noimage_icon.jpg" /><br />Up-Charge</a></li>
                </ul>            
                <ul style="float:left;">
                
                                
                </ul>            
            </div>
       </div>
                          
        <div id="tab3" class="tab_content" style="display:none;">
            <div class="icons">
               <ul style="float:left;border-bottom:1px dotted #6ab8e6;width:100%;">
			   <li><a href="contact_lens/cont_lens_brand.php?alpha=a"  class="text_purpule" ><img border="0" src="../../images/brand.jpg" /><br/>Brand</a></li> 
               <li><a href="contact_lens/cont_lens_packaging.php?alpha=az" class="text_purpule"><img border="0" src="../../images/packaging.jpg" style="width: 69px;float: left;border: 1px solid #CCC;height: 69px;border-radius: 4px;box-shadow: 1px 1px 0px #999;padding:5px;margin: 0 0 5px 4px;"/><br />Packaging</a></li>
               <li><a href="contact_lens/cont_lens_caj.php?alpha=a"    class="text_purpule"><img border="0" src="../../images/cat.jpg" /><br />Wear Scheduler</a></li>
               <li><a href="contact_lens/cont_lens_replenishment.php?alpha=az" class="text_purpule"><img border="0" src="../../images/replacement.jpg" style="width: 69px;float: left;border: 1px solid #CCC;height: 69px;border-radius: 4px;box-shadow: 1px 1px 0px #999;padding:5px;margin: 0 0 5px 4px;"/><br />Replacement</a></li>
               <li><a href="contact_lens/cont_lens_supply.php?alpha=a" class="text_purpule"><img border="0" src="../../images/supply_icon.jpg" /><br />Annual Supply</a></li> 
			   <li><a href="contact_lens/cont_lens_type.php?alpha=a"   class="text_purpule"><img border="0" src="../../images/contacts_type_icon.jpg" /><br />Material</a></li>
               <li><a href="contact_lens/cont_lens_color.php?alpha=a"  class="text_purpule"><img border="0" src="../../images/color_icon.jpg" /><br />Color</a></li>
               <li><a href="contact_lens/cont_lens_disinfecting.php?alpha=az" class="text_purpule"><img border="0" src="../../images/disinfecting.jpg" style="width: 69px;float: left;border: 1px solid #CCC;height: 69px;border-radius: 4px;box-shadow: 1px 1px 0px #999;padding:5px;margin: 0 0 5px 4px;"/><br />Disinfecting</a></li>
                <li><a href="contact_lens/cont_lens_sync.php" class="text_purpule"><img border="0" src="../../images/sync.png" style="width: 69px;float: left;border: 1px solid #CCC;height: 69px;border-radius: 4px;box-shadow: 1px 1px 0px #999;padding:5px;margin: 0 0 5px 4px;"/><br />iDoc Sync</a></li>
               </ul>
            </div>
       </div>  
       
        <div id="tab4" class="tab_content" style="display:none;">
            <div class="icons">
                <ul style="float:left;border-bottom:1px dotted #6ab8e6;width:100%;">            
                 <li><a href="../admin/medicines/medicines_type.php?alpha=a" class="text_purpule"><img border="0" src="../../images/medicine-type.jpg" style="width: 69px;float: left;border: 1px solid #CCC;height: 69px;border-radius: 4px;box-shadow: 1px 1px 0px #999;padding:5px;margin: 0 0 5px 4px;"/><br />Medication</a></li>
                </ul>            
            </div>
       </div>
       
       
       <div id="tab5" class="tab_content" style="display:none;">
            <div class="icons">
                <ul style="float:left;border-bottom:1px dotted #6ab8e6;width:100%;">            
                 <li><a href="../admin/supplies/measurment.php?alpha=a" class="text_purpule"><img border="0" src="../../images/Measurment.jpg" /><br />Measurement</a></li>
                 <li><a href="../admin/supplies/size.php?alpha=a" class="text_purpule"><img border="0" src="../../images/size.jpg" /><br />Size</a></li> 

                </ul>            
            </div>
       </div>
       
    <div id="tab6" class="tab_content" style="display:none;">
            <div class="icons">
                <ul style="float:left;border-bottom:1px dotted #6ab8e6;width:100%;">            
                 <li><a href="../admin/manufacturer/manufacturer.php?alpha=a" class="text_purpule"><img border="0" src="../../images/manufacturer_icon.jpg" /><br />Manufacturer</a></li> 
                  <li><a href="../admin/vendor/vendor.php?alpha=a" class="text_purpule"><img border="0" src="../../images/vendor_icon.jpg" /><br />Vendor</a></li>
                 <li><a href="../admin/other/location.php?alpha=az" class="text_purpule"><img border="0" src="../../images/location_icon.jpg" /><br />Location</a></li>
                 <li><a href="../admin/other/reason.php?alpha=a" class="text_purpule"><img border="0" src="../../images/reason_icon.jpg" /><br />Reason</a></li> 
                 <li><a href="../admin/other/misce.php?alpha=a" class="text_purpule"><img border="0" src="../../images/alternatives_icon.jpg" /><br />Alternatives</a></li> 
                <li><a href="../admin/other/reason_return.php?alpha=a" class="text_purpule"><img border="0" src="../../images/Reason_For_Return_icon.jpg" /><br />Remake & Returns</a></li>
                <li><a href="other/print_setup.php" class="text_purpule"><img border="0" src="../../images/print_set.png" style="width: 59px;height: 59px;border: 1px solid #CCC;border-radius: 5px;padding: 10px;"/><br />Print Setting</a></li> 
                <li><a href="other/practice_codes.php" class="text_purpule"><img border="0" src="../../images/practice-codes.jpg" style="width: 69px;float: left;border: 1px solid #CCC;height: 69px;border-radius: 4px;box-shadow: 1px 1px 0px #999;padding:5px;margin: 0 0 5px 4px;"/><br />Practice Codes</a></li>
                <li><a href="other/usage_type.php" class="text_purpule"><img border="0" src="../../images/usage-new.jpg" style="width: 69px;float: left;border: 1px solid #CCC;height: 69px;border-radius: 4px;box-shadow: 1px 1px 0px #999;padding:5px;margin: 0 0 5px 4px;" /><br />Usage & Type</a></li>
               
                </ul>
            </div>
            <div class="icons">
                <ul style="float:left;border-bottom:1px dotted #6ab8e6;width:100%;">      
                 <li><a href="lens/lens_labs.php?alpha=az" class="text_purpule"><img border="0" src="../../images/lab.jpg" /><br />Labs</a></li>      
                 <li><a href="../admin/framesdata_setup.php" class="text_purpule"><img border="0" src="../../images/noimage_icon.jpg" style="width: 69px;float: left;border: 1px solid #CCC;height: 69px;border-radius: 4px;box-shadow: 1px 1px 0px #999;padding:5px;margin: 0 0 5px 4px;"/><br />Frames Data Auth</a></li> 
				 <li><a href="../admin/framesData.php" class="text_purpule"><img border="0" src="../../images/frames-data.jpg" style="width: 69px;float: left;border: 1px solid #CCC;height: 69px;border-radius: 4px;box-shadow: 1px 1px 0px #999;padding:5px;margin: 0 0 5px 4px;"/><br />Frames Data Import</a></li> 
                 <li><a href="../admin/framesDataSetting.php" class="text_purpule"><img border="0" src="../../images/noimage_icon.jpg" style="width: 69px;float: left;border: 1px solid #CCC;height: 69px;border-radius: 4px;box-shadow: 1px 1px 0px #999;padding:5px;margin: 0 0 5px 4px;"/><br />Frames Data Not in Use</a></li>
				 <li><a href="../admin/framesPricecChange.php" class="text_purpule"><img border="0" src="../../images/noimage_icon.jpg" style="width: 69px;float: left;border: 1px solid #CCC;height: 69px;border-radius: 4px;box-shadow: 1px 1px 0px #999;padding:5px;margin: 0 0 5px 4px;"/><br />Frames Data Price Change</a></li>
                <?php if($GLOBALS['connect_visionweb']!=""){ ?>
                 <li><a href="../admin/other/visionweb_setup.php" class="text_purpule"><img border="0" src="../../images/noimage_icon.jpg" style="width: 69px;float: left;border: 1px solid #CCC;height: 69px;border-radius: 4px;box-shadow: 1px 1px 0px #999;padding:5px;margin: 0 0 5px 4px;"/><br />VisionWeb</a></li> 
                <?php } ?>
				<li><a href="../admin/markup/index.php" class="text_purpule"><img border="0" src="../../images/noimage_icon.jpg" style="width: 69px;float: left;border: 1px solid #CCC;height: 69px;border-radius: 4px;box-shadow: 1px 1px 0px #999;padding:5px;margin: 0 0 5px 4px;"/><br />Markup</a></li> 
                 </ul>
            </div>
       </div>        
    </div>      
    <iframe src="about:blank" name="admin_iframe" id="admin_iframe" style="width:100%; height:<?php echo $_SESSION['wn_height']-350;?>px; margin:0px; display:none; overflow:hidden;" frameborder="0" framespacing=0 scrolling="no"></iframe>  
</body>
</html>    
    
