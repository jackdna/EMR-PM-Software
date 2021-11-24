// JavaScript Document


//all_admin_content_agree_multi

$(window).load(function() {
   
$(document).ready(function() {
	
	//alert(screen.availWidth);
	//alert(screen.availHeight);
	window.moveTo(0, 0);
	window.resizeTo(screen.availWidth, screen.availHeight);
	
    var hippa =	$('.all_admin_content_agree');
	//var hippa = top.document.getElementById('div_ovrflow');
	var hippa_win = $(window).height();
	var header_height = $('.header_full_wrap').outerHeight(true);
	var footer_height = $('.footer_wrap ').outerHeight(true);
	var bottom_btns_h = $('.btn-footer-slider').outerHeight(true);
	var sub_head =$('.subtracting-head').outerHeight(true);
	var height_custom_scroll = $('.scrollable_yes');
	var height_custom = hippa_win - (header_height+footer_height+bottom_btns_h+5);
	var height_custom2 = hippa_win - (header_height+footer_height+bottom_btns_h+sub_head+30);
	var height_custom_scroll2 = $('.scrollable_agree');
	
	//alert(hippa_win);
	hippa.css({ 'min-height' : height_custom , 'max-height': height_custom });
    height_custom_scroll.css({ 'min-height' : height_custom2 , 'max-height': height_custom2});
    height_custom_scroll2.css({ 'min-height' : height_custom2 , 'max-height': height_custom2});

});
});
//all_admin_content_agree_multi
