// imedicmonitornew JavaScript Document
function initDisplay(arDim,page){
	var h = arDim['h']; var w = arDim['w'];
	$('#page_bottom_bar').css({'top':(h-55),'width':w-10,'display':'block'});/*SETTING POSITION AND WIDTH OF FOOTER*/
	if(page=='main'){
		ldivH = Math.round((h-fixObjH)/3,0);
		rdivH1 = Math.round((ldivH*2),0);
		rdivH2 = Math.round((h-fixObjH)-rdivH1,0);
		$(arrLeftDivs).each(function(){
			$('#'+this).css({'height':ldivH-6});
		});
		$('#box_waitingpt').css({'height':rdivH1-5,'float':'right'});
		$('#box_wr').css({'height':rdivH2-5,'float':'right'});
		$(allObj).each(function(i){
			$('#'+allObj[i]).show().css({'width':'49.6%'});
		});
		TableScroller();
	}else{
		$('#btn_close').css({'position':'absolute', 'left':((w/2)-4)-($('#btn_close').width()/2)});
	}
	if(page!='co')bigbox_height= rdivH1-5;
}

function initDisplayRoomView(arDim,page){
	var h = arDim['h']; var w = arDim['w'];
	$('#page_bottom_bar').css({'top':(h-55),'width':w-10,'display':'block'});/*SETTING POSITION AND WIDTH OF FOOTER*/
	if(page=='main'){
		ldivH = Math.round((h-fixObjH)/3,0);
		rdivH1 = Math.round((ldivH*2),0);
		rdivH2 = Math.round((h-fixObjH)-rdivH1,0);
		$(arrLeftDivs).each(function(){
			$('#'+this).css({'height':ldivH-10});
		});
		$('#box_waitingpt').css({'height':rdivH1-5});
		$('#box_waitingpt_left').css({'height':rdivH1-5});
		$('#box_wr').css({'height':rdivH2-10,'float':'right'});
		$(allObj).each(function(i){
			if(allObj[i]=='box_waitingpt'){
				$('#'+allObj[i]).show().css({'width':'32%'});	
				$('.div_group_container').height($('#box_waitingpt').height()-20);
			}else if(allObj[i]=='box_waitingpt_left'){
				$('#'+allObj[i]).show().css({'width':'67%'});
			}else{
				$('#'+allObj[i]).show().css({'width':'49.6%'});
			}
		});
		//alert($('#box_waitingpt_left').css('height'));
		TableScroller();
	}else{
		$('#btn_close').css({'position':'absolute', 'left':((w/2)-4)-($('#btn_close').width()/2)});
	}
	bigbox_height= rdivH1-5;
}

function initDisplayExtendedView(arDim,page){
	var h = arDim['h']; var w = arDim['w'];
	$('#page_bottom_bar').css({'top':(h-55),'width':w-10,'display':'block'});/*SETTING POSITION AND WIDTH OF FOOTER*/
	if(page=='extended'){
		$('#box_extended_ptlist').css({'height':(h-fixObjH)-5});
		$('.div_group_container').height($('#box_extended_ptlist').height()-20);
		$('#active_patients_header').width(($('#active_patients_header').width()-28)+'px');
	}
}

function innerDim(){
	var arDim = new Array();
	arDim['h'] = $(window).innerHeight()-4;
	arDim['w'] = $(window).innerWidth();
	return arDim;
}
function BigMe(o,from){
	if(typeof(from)=='undefined') from=false;
	arDim = innerDim();
	if(!boxBigged){
		/*****MAKING ALL BOXES HIDDEN******/
		currObj = $(o).parent().parent('div').attr('id');
		$(allObj).each(function(i){
			if(allObj[i] != currObj){
				if(currObj!='box_wr' && currObj!='box_schp')
					$('#'+allObj[i]).not('#box_wr, #box_schp').hide();
				else
					$('#'+allObj[i]).hide();
			}
		});
		
		/*****MAKING CURRENT CLICKED BOX BIGGER AND CHANGING ICON CLASS*******/
		if(currObj!='box_wr' && currObj!='box_schp')
			$('#'+currObj).css({'width':(arDim['w']-12),'height':bigbox_height});//,'height':((arDim['h']-10)-fixObjH)
		else
			$('#'+currObj).css({'width':(arDim['w']-12),'height':((arDim['h']-10)-fixObjH)});
		
		/*****SWITCHING CLASS OF ICON******/
		$('#'+currObj).children('.section_header').children('span').removeClass('icon20_expand').addClass('icon20_collapse');

		/*****ARRANGING ROOM GROUP WIDTH (ROOM VIEW SPECIFIC)*****/
		if(from=='roomview') {
			//$('.div_group_container').height($('#box_waitingpt').height());
			div_grp_length = $('.div_group').length;
			div_grp_width = '99%';
			if(div_grp_length==2)	div_grp_width = '48.5%';
			else if(div_grp_length==3)	div_grp_width = '31.8%';
			else if(div_grp_length>3)	div_grp_width='24.2%';
			$('.div_group').css({'width':div_grp_width});
		}
		boxBigged = true;
		TableScroller($('#'+currObj));
	}else{
		boxBigged = false;
		if(from=='roomview'){
			initDisplayRoomView(arDim,'main');
			div_grp_length = $('.div_group').length;
			div_grp_width = '99%';
			if(div_grp_length==2)	div_grp_width = '48.5%';
			else if(div_grp_length>=3)	div_grp_width = '32.4%';
			$('.div_group').css({'width':div_grp_width});
		}else initDisplay(arDim,'main');
		$(o).removeClass('icon20_collapse').addClass('icon20_expand');
	}
}

function TableScroller(o){
	if(o){
		//$('.tableCon').height(o.height()-21);
	}
	else{
		$('.tableCon').each(function(){
							 		 o=$(this).parent('div');
									 $(this).height(o.height()-21);
									 });
	}
}

function show_clock(){ 
		var C = new Date(); var h = C.getHours(); var m = C.getMinutes(); var s = C.getSeconds();
		var dn = "PM"; if (h < 12) dn = "AM"; if (h > 12) h = h-12; if (h == 0) h = 12; if (m <=9 ) m = "0" + m; 
		if(s <= 9) s = "0" + s; var tm = h + ":" + m + ":" + s + " " + dn;
		$("#div_now").text(tm); setTimeout("show_clock()", 1000);
}
String.prototype.trim = function() {
	return this.replace(/^\s+|\s+$/g,"");
}

function stristr (haystack, needle, bool) {
    var pos = 0;
    haystack += '';
    pos = haystack.toLowerCase().indexOf((needle + '').toLowerCase());
	if (pos == -1) {
        return false;
    }else{
        if(bool){
            return haystack.substr(0, pos);
		}else{
            return true; //haystack.slice(pos);
        }
    }
}

function showPAG(pt_id){
	hw = innerDim();
	if(parseInt(hw['w']) >= 1200) wid = 1150; else wid = parseInt(hw['w'])-50;
	if(parseInt(hw['h']) >= 700) hei = 650; else hei = parseInt(hw['h'])-50;
	
	top.fancyModal('<iframe name="messimodal" id="messimodal" src="../../interface/chart_notes/past_diag/chart_patient_diagnosis.php?p_id='+pt_id+'&cameFrom=imedicmonitor" frameborder=0 style="width:'+wid+'px; height:'+hei+'px;"></iframe>','',(wid+20)+'px',(hei)+'px');
}

function switchView(v){
	f = $('#sel_facility').val();
	p = $("#sel_provider").selectedValuesString();
	m = $("#view_mode").val();
	act_pro_id = $("#active_pro").val();
	act_fac_id = $("#active_fac").val();
	u = "main.php";
	if(v=='room') 			u = "main_rooms.php";
	else if(v=='extended') 	u = "main_extended.php";
	url = u+'?dd_fac_id='+f+'&dd_prov_id='+escape(p)+'&view_mode='+m+'&act_pro_id='+act_pro_id+'&act_fac_id='+act_fac_id;
	if (typeof(Storage) !== "undefined") {
		localStorage.imon_view = u;
	}
	window.location.href= url;
}

function room_priority_change(task,sch_id,room){
	if(typeof(room)=='undefined') room = '';
	//alert(task+'::'+sch_id+' :: '+room);
	
	url = "change_pt_room_priority.php?sch_id="+sch_id+"&task="+escape(task)+"&room="+escape(room);
	$.ajax({type:"GET",url:url,success:function(r){//alert(url+"\n\n"+r);
	
			if(typeof(page_loader2)!='undefined'){
				clearInterval(page_loader2);
				delete page_loader2;
			}
			getSchData(true);								
		}
	});
	
}

function set_refresh_mode(m){
	if(typeof(m)=='undefined') m = $('#refresh_mode').val();
	if(m=='toogle'){
		if(m=='manual') m='auto';
		else if(m=='auto') m='manual';
	}
	o = $('#btn_refresh_mode');
	if((typeof(m)!='undefined' && m.toLowerCase()=='manual') || o.hasClass('active')){
		if(o.hasClass('active')) o.removeClass('active');
		$('#refresh_mode').val('manual');
		o.val('Manual');
	}else{
		if(!o.hasClass('active')) o.addClass('active');
		$('#refresh_mode').val('auto');
		o.val('Auto');
		getSchData();
	}
}

function set_view_mode(m){
	if(typeof(m)=='undefined') m = $('#view_mode').val();
	//if(m=='toogle'){
		if(m=='public') m='private';
		else if(m=='private') m='public';
	//}
	o = $('#btn_view_mode');
	if((typeof(m)!='undefined' && m.toLowerCase()=='public') || o.hasClass('active')){
		if(o.hasClass('active')) o.removeClass('active');
		$('#view_mode').val('public');
		o.val('Public');
	}else{
		if(!o.hasClass('active')) o.addClass('active');
		$('#view_mode').val('private');
		o.val('Private');
		getSchData();
	}
}

function LoadWorkView(ptid) {
	a = top.JS_WEB_ROOT_PATH+"/interface/core/index.php?loadPtHash="+ptid;
	myWindow = window.open(a, "imedic_frameR8");
}

function set_active_tabs(evnt){
	oap = $('#active_pro').val()
	selected_Provs = $("#sel_provider").selectedValuesString();
	arr_provIDs = selected_Provs.split(',');
	tabshtml = ''; defaultProv = '';
	$('#processing_image').show();
	if(selected_Provs!= '' && arr_provIDs.length>0){
		for(i=0;(i<arr_provIDs.length && i<5);i++){
			provid = arr_provIDs[i];
			tabshtml += '<input type="button" id="prv_btn_'+provid+'" class="btn_normal" value="'+arr_provs[provid]+'" onClick="load_pts(\'get\',\''+provid+'\')"> ';
			if(i==0){defaultProv=provid;}
		}
		$('#prov_tabs_div').html(tabshtml);
		if(typeof(evnt)=='undefined') $('#active_pro').val(defaultProv);
		else{
			defaultProv = $('#active_pro').val();
		}
		load_pts('get',defaultProv);
	}else{
		$('#prov_tabs_div').html('');
		$('#active_pro').val('');
		load_pts('get');
	}
}
function reset_prov_btns(){
	$('.btn_normal').each(function(){$(this).removeClass('active_btn');});
}

String.prototype.JSstripSlashes = function(){
    return this.replace(/\\(.)/mg, "$1");
}