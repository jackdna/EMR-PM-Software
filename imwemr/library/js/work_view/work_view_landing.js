function unpin_icon(pin_unpin,authUserID){
	$("#pin_unpin_div").css({"display":"none"});
	icon_ajax(pin_unpin,'unpin',authUserID);
	//pin_icon(pin_unpin,authUserID);
}
function pin_icon(pin_unpin,authUserID){
	$("#"+pin_unpin+" ").append('<a id="pin_'+pin_unpin+'" style="position: absolute;left: 0;width: auto;top: 0;"><img src="'+top.JS_WEB_ROOT_PATH+'/library/images/pin.png"></a>').css({"position":"relative","cursor":"pointer"}).on("click",function(){
		var remove_obj=$(this).attr("id");
		$("#pin_"+remove_obj).remove();
		$('.okayNav__nav--visible').append($("#"+remove_obj));
		icon_ajax(pin_unpin,'pin',authUserID);
	});
	
}
function icon_ajax(obj_val,status,authUserID){
	
	if(typeof(top.JS_WEB_ROOT_PATH)=="undefined"){ return; }
	
	$.ajax({
		url:top.JS_WEB_ROOT_PATH+"/interface/chart_notes/view/ajax_work_view_icon.php?objval="+obj_val+"&status="+status+"&authUserID="+authUserID,
		success:function(data){
			if($.trim(data)){
				var cnt=data.split(",");
				for(var i=0;i<cnt.length;i++){
					pin_unpin=cnt[i];
					if(pin_unpin){
						$('.okayNav__nav--invisible').append($("#"+pin_unpin));
						pin_icon(pin_unpin,authUserID);
					}
				}
				
			}
		}
	});
	//navigation.okayNav('recalcNav');
}


function check_refusal(id){
	$("#refusal_row").val(id);
	$('#refusal_reason').val($("#refusal_reason"+id).val());
	$('#m_refusal_snomed').val($("#refusal_snomed"+id).val());
}


function check_refusal_values(){
	var ID = $("#refusal_row").val();
    
	var refusal_reason	= $('#refusal_reason').val();
	var refusal_snomed	= $('#m_refusal_snomed').val();
    if(!ID) {
        return false;
    }
	//if(refusal_snomed != "" && ID != ""){
		$("#refusal"+ID).val(1);
		$("#refusal_reason"+ID).val(refusal_reason); 
		$("#refusal_snomed"+ID).val(refusal_snomed); 
	//}
	$("#myModal").modal('hide');
}



var okay_nav_obj = {};
$(document).ready(function(){
	

	var navigation = $('#nav-main').okayNav();
	
	setTimeout(function(){
	
	//check if workview title bar is loaded or not.
	if(!top.$("#first_toolbar").hasClass("Work_View") && !top.$("#first_toolbar").hasClass("user_fst_landing")){
		top.$("#first_toolbar").removeClass();		
		top.$("#first_toolbar").addClass("usersection Work_View");		
		top.$("#Work_View").triggerHandler("click");
		return;
	}
		
	//Temp Fix 	
	if(typeof(authUserID)=="undefined"){ authUserID=0; }
	
	//style
	$('[data-toggle="tooltip"]').tooltip(); 		
	//
	//applyTypeAhead(); //double typeahead
	$('#ContactLens ul.typeahead').css({'max-width': '350px', 'width': '350px'});
	
	$("#ContactLens ul.typeahead li").each(function(){
		$(this).css({'max-width': '350px', 'width': '350px'});
	});
	$('#pgd_showpop').tooltip({
    selector: '[data-toggle="tooltip"]',
		container:'#pgd_showpop'
	});
	$('#ContactLens .dropdown-menu li a').click(function(){
		$(this).parentsUntil('div').parent().siblings('input').val($(this).html());
	});
	
	//
	
	//========================Right Click show unpin icon==========================//
	var pin_unpin=chk_pin="";
	$('#nav-main .okayNav__nav--visible li').each(function(id,elem){
		
		$(elem).on('contextmenu',function(){
			var str=$(elem).parent().attr("class");
			if(str.indexOf("invisible")!=-1){return false;}
				var left_pos=($(this).position().left);
				var top_pos=($(this).position().top);
				$("#pin_unpin_div").css({"display":"block",top:top_pos, left: left_pos});
				setTimeout(function(){ $("#pin_unpin_div").css({"display":"none"});}, 5000);
				pin_unpin=$(this).attr("id");
				return false;
			
		});
	});
	//=====================Click to pin/unpin icon======================================//
	$("#pin_unpin_div img").click(function(){
		unpin_icon(pin_unpin,authUserID);
	});
	
	//
	icon_ajax("","",authUserID);
	
	//
	$("body").on('change', '.checkbox',function() {
	if(this.checked) {
		var row_id =   this.id;
			$("#hc_modal").modal('show');
			$("#myModal").modal('show');
			$("#rowID").val(row_id);	
		}
	});
	
	if(typeof(isDssEnable)!="undefined" && isDssEnable == 1) { dssLoadTiuTitles();}
	},180);	
	//
	top.$(".elchart, #div_pt_name").css("display","block");
	//remove wraper
	$("#dvloading").remove();
});