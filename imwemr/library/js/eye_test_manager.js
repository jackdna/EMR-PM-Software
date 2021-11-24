var oPF		=	window.opener.top.fmain.oPF || [];
var oPO 	= window.opener.top.fmain.oPO;
var oPUF 	= window.opener.top.fmain.oPUF;
var oPF 	= window.opener.top.fmain.oPF;	
var zPath 	= js_php_arr.root_dir;

var strScanArr = js_php_arr.strScanArr;
ArrScanHref = strScanArr;

var strTestWiseImg = js_php_arr.strTestWiseImg;
ArrTestWiseScanHref = strTestWiseImg;

var strTestsSeq = js_php_arr.strTestsSeq;
ArrTestSeq = strTestsSeq; 

var firstTestName = null;
var ArrFirstTestImages = null;
temmp_test_cnt = 0;
for(x in ArrTestWiseScanHref){
	if(temmp_test_cnt==0 && ArrFirstTestImages==null){
		var firstTestName = x;
		var ArrFirstTestImages = ArrTestWiseScanHref[x];
	}
}

top.JS_WEB_ROOT_PATH = window.opener.top.JS_WEB_ROOT_PATH

var SelectedTestImages = new Array();
fmain = new Object;
$ = jQuery;


function pageHeight(){ 
	return window.innerHeight != null? window.innerHeight : document.documentElement && document.documentElement.clientHeight ? document.documentElement.clientHeight : document.body != null? document.body.clientHeight : null;
}

$(document).ready(function(){
	$.get(top.JS_WEB_ROOT_PATH+"/interface/chart_notes/test_manager/ajax_handler.php?task=getSetting&setiname=tman_startup_prvw",function(old_chk_loadBigImage){
		if(old_chk_loadBigImage=="1"){$('#chk_loadBigImage').prop('checked',true);loadDefaultPreview();}
	});
	$('#chk_loadBigImage').click(function(){
		if($(this).prop('checked'))new_chk_loadBigImage="1"; else new_chk_loadBigImage="0";
		$.get(top.JS_WEB_ROOT_PATH+"/interface/chart_notes/test_manager/ajax_handler.php?task=setSetting&setiname=tman_startup_prvw&setival="+new_chk_loadBigImage,function(r){
			//if(r!='true' && r!='')top.fAlert(r);
		});
		loadDefaultPreview();
	});
	
	loadDefaultPreview(ArrFirstTestImages);
	//$(".jpg_colorbox").colorbox({rel:'jpg_colorbox'});
	$(".pdf_colorbox").colorbox({rel:'pdf_colorbox',iframe:true, width:"90%", height:"90%"});
	$(".jpg_colorbox").colorbox({rel:'jpg_colorbox',iframe:false, width:"auto", height:"90%"});
	
	//------FILLING MOUSE HOVER BIG IMAGE--
	$(".pdf_colorbox, .jpg_colorbox").hover(function(e){
		if($("#temp_container").html()==''){
			imgsrc = $(this).find('img').prop('src');
			imgsrc = imgsrc.replace("thumbnail","thumb");
			$("#temp_container").html('<div class="div_shadow" style="width:auto;"><div class="div_shadow_container" style="overflow:hidden"><img src="'+imgsrc+'" style="height:370px; max-width:500px;"></div></div>');
			$("#temp_container").fadeIn();
		}
	});

	//------HIDING MOUSE HOVER BIG IMAGE--	
	$(".pdf_colorbox, .jpg_colorbox").mouseout(function(e){
		$("#temp_container").fadeOut();
		$("#temp_container").html('').css({'display':'none'})
	});
	
	//------POSITIONING MOUSE HOVER BIG IMAGE--
	$(".pdf_colorbox, .jpg_colorbox").mousemove(function(e){
		ww = $('#page_body').width();
		relX = e.pageX+10;
		relY = e.pageY-200;
		//if(ww-relX <= 350){relX= relX-470;relY= relY;}
		if(relY < 10) relY = 10;
		w = $('#temp_container').width();
		mx_w = parseInt(w+relX);
	//	console.log('ww='+ww+' :: mx_w='+mx_w);
		if(ww<=mx_w) relX = parseInt(relX)-(parseInt(w)+20);
		$('#temp_container').css({'top':relY+'px','left':relX+'px'});
	});
	
});

function loadDefaultPreview(arrTimage){
	initDisp();
	if($('#chk_loadBigImage').prop('checked')==false){$('#page_body').html('');return;}
	
	HTML = '';
	$('#page_body').html(HTML); //hiding upper blank part.
	
	if(typeof(arrTimage)=='undefined'){arrTimage = ArrFirstTestImages;}
	reqH = $('#page_body').height()-35;
	reqW = $('#page_body').width()-35;
	firstTestName1 = firstTestName.split('@');
	firstTestName1 = '<b>'+firstTestName1[0]+'</b> '+firstTestName1[1];
	test_id_tag = '<span class="badge badge-primary" onClick="$(\'a\').get(0).click();">'+firstTestName1+'</span>';
	$('#page_body').html(test_id_tag);
	arrTimage1 = new Array();
	if(arrTimage.length>=3){//CODE TO LOAD SMALL RES IMAGES IF NEED TO SHOW >=3 IMAGES
		$(arrTimage).each(function(i,e){
			filename = arrTimage[i].split('/').pop();
			filename = arrTimage[i].replace(filename,filename);
			arrTimage1[i] = filename;
			//alert(arrTimage[0]+ "\n"+filename);
		});
	}else{arrTimage1 = arrTimage;}
	var big_img_file_name_new = js_php_arr.big_new_img;
	for(xx=0;xx<arrTimage1.length;xx++){
		big_img_file_name = arrTimage1[xx];
		big_img_file_ext  = big_img_file_name.substr(-4);
		if(big_img_file_ext.toLowerCase()=='.pdf'){
			big_img_file_name = big_img_file_name.replace(big_img_file_ext,'.jpg');
			if(big_img_file_name_new) {
				big_img_file_name = big_img_file_name_new;
			}
			arrTimage1[xx] = big_img_file_name;
		}
	}
	
	switch(arrTimage1.length){
		case 1:
			HTML 	+= '<img class="page_body_img border" src="'+arrTimage1[0]+'">';
			$('#page_body').append(HTML);
			$('.page_body_img').css({'max-height':reqH,'max-width':reqW});
			break;
		case 2:
			HTML	+= '<table class="table" style="height:'+$('#page_body').height()	+'px;"><tr>';
			HTML 	+= '<td style="width:50%;"><img class="page_body_img border" src="'+arrTimage1[0]+'"></td>';
			HTML 	+= '<td style="width:50%;"><img class="page_body_img border" src="'+arrTimage1[1]+'"></td>';
			HTML 	+= '</tr><table>';
			$('#page_body').append(HTML);
			reqW = reqW/2;
			$('.page_body_img').each(function(){
				$(this).css({'max-height':reqH,'max-width':reqW});
			});				
			break;
		case 3:
			//alert(arrTimage1);
			HTML	+= '<table class="table" style="height:'+$('#page_body').height()	+'px;"><tr>';
			HTML 	+= '<td style="width:33%;"><img class="page_body_img" src="'+arrTimage1[0]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td style="width:34%;"><img class="page_body_img" src="'+arrTimage1[1]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td style="width:33%;"><img class="page_body_img" src="'+arrTimage1[2]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '</tr><table>';
			$('#page_body').append(HTML);
			reqW = reqW/3;
			$('.page_body_img').each(function(){
				$(this).css({'max-height':reqH,'max-width':reqW});
			});
			break;
		case 4:
			HTML	+= '<table class="table"  style="height:'+$('#page_body').height()	+'px;"><tr>';
			HTML 	+= '<td style="width:25%;"><img class="page_body_img" src="'+arrTimage1[0]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td style="width:25%;"><img class="page_body_img" src="'+arrTimage1[1]+'" border=0 style="width:100%;"></td></tr>';
			HTML 	+= '<tr><td style="width:25%;"><img class="page_body_img" src="'+arrTimage1[2]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td style="width:25%;"><img class="page_body_img" src="'+arrTimage1[3]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '</tr><table>';
			$('#page_body').append(HTML);
			reqH = parseInt(reqH/2)-5;
			reqW = parseInt(reqW/2)-5;
			break;				
		case 5:
		case 6:
			HTML	+= '<table class="table" style="height:'+$('#page_body').height()	+'px;"><tr>';
			HTML 	+= '<td style="width:33%;"><img class="page_body_img" src="'+arrTimage1[0]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td style="width:34%;"><img class="page_body_img" src="'+arrTimage1[1]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td style="width:33%;"><img class="page_body_img" src="'+arrTimage1[2]+'" border=0 style="width:100%;"></td></tr>';
			HTML 	+= '<tr><td><img class="page_body_img" src="'+arrTimage1[3]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td><img class="page_body_img" src="'+arrTimage1[4]+'" border=0 style="width:100%;"></td>';
			if(typeof(arrTimage1[5])=='undefined'){
				HTML 	+= '<td>&nbsp;</td>';
			}else{
				HTML 	+= '<td><img class="page_body_img" src="'+arrTimage1[5]+'" border=0 style="width:100%;"></td>';	
			}
			HTML 	+= '</tr><table>';
			$('#page_body').append(HTML);
			reqH = parseInt(reqH/2)-5;
			reqW = parseInt(reqW/3)-5;
			break;
		case 7:
		case 8:
			HTML	+= '<table class="table" style="height:'+$('#page_body').height()	+'px;"><tr>';
			HTML 	+= '<td style="width:25%;"><img class="page_body_img" src="'+arrTimage1[0]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td style="width:25%;"><img class="page_body_img" src="'+arrTimage1[1]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td style="width:25%;"><img class="page_body_img" src="'+arrTimage1[2]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td style="width:25%;"><img class="page_body_img" src="'+arrTimage1[3]+'" border=0 style="width:100%;"></td></tr>';
			HTML 	+= '<tr><td><img class="page_body_img" src="'+arrTimage1[4]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td><img class="page_body_img" src="'+arrTimage1[5]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td><img class="page_body_img" src="'+arrTimage1[6]+'" border=0 style="width:100%;"></td>';				
			if(typeof(arrTimage1[7])=='undefined'){
				HTML 	+= '<td>&nbsp;</td>';
			}else{
				HTML 	+= '<td><img class="page_body_img" src="'+arrTimage1[7]+'" border=0 style="width:100%;"></td>';
			}
			HTML 	+= '</tr><table>';
			$('#page_body').append(HTML);
			reqH = parseInt(reqH/2)-5;
			reqW = parseInt(reqW/4)-5;
			break;
		case 9:
			HTML	+= '<table class="table" style="height:'+$('#page_body').height()	+'px;"><tr>';
			HTML 	+= '<td style="width:33%;"><img class="page_body_img" src="'+arrTimage1[0]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td style="width:34%;"><img class="page_body_img" src="'+arrTimage1[1]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td style="width:33%;"><img class="page_body_img" src="'+arrTimage1[2]+'" border=0 style="width:100%;"></td></tr>';
			HTML 	+= '<tr><td><img class="page_body_img" src="'+arrTimage1[3]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td><img class="page_body_img" src="'+arrTimage1[4]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td><img class="page_body_img" src="'+arrTimage1[5]+'" border=0 style="width:100%;"></td></tr>';
			HTML 	+= '<tr><td><img class="page_body_img" src="'+arrTimage1[6]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td><img class="page_body_img" src="'+arrTimage1[7]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td><img class="page_body_img" src="'+arrTimage1[8]+'" border=0 style="width:100%;"></td></tr>';
			HTML 	+= '</tr><table>';
			$('#page_body').append(HTML);
			reqH = parseInt(reqH/3)-5;
			reqW = parseInt(reqW/3)-5;
			break;
		case 10:
		case 11:
		case 12:
			HTML	+= '<table class="table" style="height:'+$('#page_body').height()	+'px;"><tr>';
			HTML 	+= '<td style="width:25%;"><img class="page_body_img" src="'+arrTimage1[0]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td style="width:25%;"><img class="page_body_img" src="'+arrTimage1[1]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td style="width:25%;"><img class="page_body_img" src="'+arrTimage1[2]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td style="width:25%;"><img class="page_body_img" src="'+arrTimage1[3]+'" border=0 style="width:100%;"></td></tr>';
			HTML 	+= '<tr><td><img class="page_body_img" src="'+arrTimage1[4]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td><img class="page_body_img" src="'+arrTimage1[5]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td><img class="page_body_img" src="'+arrTimage1[6]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td><img class="page_body_img" src="'+arrTimage1[7]+'" border=0 style="width:100%;"></td></tr>';
			HTML 	+= '<tr><td><img class="page_body_img" src="'+arrTimage1[8]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td><img class="page_body_img" src="'+arrTimage1[9]+'" border=0 style="width:100%;"></td>';
			if(typeof(arrTimage1[10])=='undefined'){
				HTML 	+= '<td>&nbsp;</td>';
			}else{
				HTML 	+= '<td><img class="page_body_img" src="'+arrTimage1[10]+'" border=0 style="width:100%;"></td>';
			}
			if(typeof(arrTimage1[11])=='undefined'){
				HTML 	+= '<td>&nbsp;</td>';
			}else{
				HTML 	+= '<td><img class="page_body_img" src="'+arrTimage1[11]+'" border=0 style="width:100%;"></td></tr>';
			}
			HTML 	+= '</tr><table>';
			$('#page_body').append(HTML);
			reqH = parseInt(reqH/3)-5;
			reqW = parseInt(reqW/4)-5;
			break;
		case 13:
		case 14:
		case 15:
			HTML	+= '<table class="table table-bordered" style="height:'+$('#page_body').height()	+'px;"><tr>';
			HTML 	+= '<td style="width:20%;"><img class="page_body_img" src="'+arrTimage1[0]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td style="width:20%;"><img class="page_body_img" src="'+arrTimage1[1]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td style="width:20%;"><img class="page_body_img" src="'+arrTimage1[2]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td style="width:20%;"><img class="page_body_img" src="'+arrTimage1[3]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td style="width:20%;"><img class="page_body_img" src="'+arrTimage1[4]+'" border=0 style="width:100%;"></td></tr>';
			HTML 	+= '<tr><td><img class="page_body_img" src="'+arrTimage1[5]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td><img class="page_body_img" src="'+arrTimage1[6]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td><img class="page_body_img" src="'+arrTimage1[7]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td><img class="page_body_img" src="'+arrTimage1[8]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td><img class="page_body_img" src="'+arrTimage1[9]+'" border=0 style="width:100%;"></td></tr>';
			HTML 	+= '<tr><td><img class="page_body_img" src="'+arrTimage1[10]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td><img class="page_body_img" src="'+arrTimage1[11]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td><img class="page_body_img" src="'+arrTimage1[12]+'" border=0 style="width:100%;"></td>';
			if(typeof(arrTimage1[13])=='undefined'){
				HTML 	+= '<td>&nbsp;</td>';
			}else{
				HTML 	+= '<td><img class="page_body_img" src="'+arrTimage1[13]+'" border=0 style="width:100%;"></td>';
			}
			if(typeof(arrTimage1[14])=='undefined'){
				HTML 	+= '<td>&nbsp;</td>';
			}else{
				HTML 	+= '<td><img class="page_body_img" src="'+arrTimage1[14]+'" border=0 style="width:100%;"></td></tr>';
			}
			HTML 	+= '</tr><table>';
			$('#page_body').append(HTML);
			reqH = parseInt(reqH/3)-5;
			reqW = parseInt(reqW/5)-5;
			break;
		case 16:
		case 17:
		case 18:
			HTML	+= '<table class="table table-bordered" style="height:'+$('#page_body').height()	+'px;"><tr>';
			HTML 	+= '<td style="width:16.4%;"><img class="page_body_img" src="'+arrTimage1[0]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td style="width:17%;"><img class="page_body_img" src="'+arrTimage1[1]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td style="width:16.3%;"><img class="page_body_img" src="'+arrTimage1[2]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td style="width:17%;"><img class="page_body_img" src="'+arrTimage1[3]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td style="width:16.3%;"><img class="page_body_img" src="'+arrTimage1[4]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td style="width:17%;"><img class="page_body_img" src="'+arrTimage1[5]+'" border=0 style="width:100%;"></td></tr>';
			HTML 	+= '<tr><td><img class="page_body_img" src="'+arrTimage1[6]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td><img class="page_body_img" src="'+arrTimage1[7]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td><img class="page_body_img" src="'+arrTimage1[8]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td><img class="page_body_img" src="'+arrTimage1[9]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td><img class="page_body_img" src="'+arrTimage1[10]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td><img class="page_body_img" src="'+arrTimage1[11]+'" border=0 style="width:100%;"></td></tr>';
			HTML 	+= '<tr><td><img class="page_body_img" src="'+arrTimage1[12]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td><img class="page_body_img" src="'+arrTimage1[13]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td><img class="page_body_img" src="'+arrTimage1[14]+'" border=0 style="width:100%;"></td>';
			HTML 	+= '<td><img class="page_body_img" src="'+arrTimage1[15]+'" border=0 style="width:100%;"></td>';
			if(typeof(arrTimage1[16])=='undefined'){
				HTML 	+= '<td>&nbsp;</td>';
			}else{
				HTML 	+= '<td><img class="page_body_img" src="'+arrTimage1[16]+'" border=0 style="width:100%;"></td>';
			}
			if(typeof(arrTimage1[17])=='undefined'){
				HTML 	+= '<td>&nbsp;</td>';
			}else{
				HTML 	+= '<td><img class="page_body_img" src="'+arrTimage1[17]+'" border=0 style="width:100%;"></td></tr>';
			}
			HTML 	+= '</tr><table>';
			$('#page_body').append(HTML);
			reqH = parseInt(reqH/3)-5;
			reqW = parseInt(reqW/6)-5;
			break;
		default:
	}
	if(arrTimage.length>=4){
		$('.page_body_img').each(function(){
			propW = ($(this).width() / $(this).height()) * reqH;
			$(this).css({'max-height':reqH,'max-width':propW});
		});			
	}
}

function SelectMe(co,t,tid,sid){//co=current object; t=testName; tid=test Id; sid = scan Id;
	hasC = $(co).prop('class');
	newIndex	= tid+'_'+sid;
	if(hasC=='scanDate'){
		cnt=0;
		for(id in SelectedTestImages){cnt++;}
		//if(cnt>=4){alert('You can select upto 4 image at once.');return;}
		currentSelection = new Array();
		currentSelection['testName']	= t;
		currentSelection['testId']		= tid;
		currentSelection['scanId']		= sid;
		SelectedTestImages[newIndex]	= currentSelection;
	}else{
		delete SelectedTestImages[newIndex];
	}
	$(co).toggleClass('scanDateSel');
	$(co).parent().find('img.border').toggleClass('border2');
	//readSelected();
}

function readSelected(){
	h='';
	for(id in SelectedTestImages){
		row = SelectedTestImages[id];
		for(col in row){
			h +=	col+' -> '+row[col]+"\n";
		}
		h	+= "\n";
	}		
	$('#arr_text').html('<pre>'+h+'</pre>');
}

function ShowSelected(m){
	if(typeof(m)=='undefined') m=0;
	if(m==1){
		features = 'left=0,top=0,width='+(screen.availWidth * 0.98)+',height='+screen.availHeight * 0.98+',menuBar=no, scrollBars=0, toolbar=0, resizable=yes';
		oPO['test_img_inter'] = www = window.open('test_interpretation.php','test_img_inter',features);
		//www.SelectedTestImages = SelectedTestImages;
		return;
	}
	
	var hrefArr = new Array();
	var testInterArray = new Array();
	i		= 0;
	cells 	= 0;
	ht 		= '';
	hgt 	= $('#page_body').height()-10;
	wdt 	= $('#page_body').width()-5;
	if(m==1){wdt=1075;}
	for(id in SelectedTestImages){
		row = SelectedTestImages[id];
		tn = row['testName'];
		tid = row['testId'];
		scan_id = 'a'+row['scanId'];
		href_url = ArrScanHref[scan_id];
		hrefArr[i] = href_url;
		i++;
		
		if($.isArray(testInterArray[tid+'_aabb_'+tn])){
			testInterArray[tid+'_aabb_'+tn].push(href_url);
			//cells++;
		}else{
			testInterArray[tid+'_aabb_'+tn] = Array(href_url);
			cells += 1;
		}
	}
	
	if(i==0){top.fAlert('Test image not selected.');return false;}
	
	/*****HIDING FILM STRIP*** AND OTHER UNREQUIRED THINGS*/
	$('#div_film_strip, #header_row, #div_module_buttons').hide();
	$('#page_body').show();
	initDisp2();
	
	
	if(hrefArr.length==1){
		ht += '<div class="iviewer" id="img_con_1" style="width:98%; height:'+hgt+'px;"></div>';
		
	}else if(hrefArr.length>1){
		if(hrefArr.length==2 && m==0){tdH = hgt;}else{tdH = parseInt(hgt/2);}
		ht += '<table class="table"><tr>';
		td = tr = '';
		tdcnt = 0;
		forloop = hrefArr.length;
		if(m==1)forloop = cells;
		for(k=0;k<forloop;k++){
			id = k+1;
			td += '<td class="iviewer" id="img_con_'+id+'" style="width:50%; height:'+tdH+'px;"></td>';
			tdcnt++;
			if(m==0){
				if(tdcnt==2){
					tr = '</tr>';
					tdcnt = 0;
				}
			}else{tr = '</tr>';tdcnt = 0;}
			td += tr;
			tr = '';
		}
		if(tdcnt==1){
			td += '</tr>';
		}
		ht += td;
		ht += '</table>';
		
	}
	
	ht1 = '<span id="div_right_preview_close" class="glyphicon glyphicon-remove pull-right" onClick="loadDefaultPreview();"></span>';
	ht1 += '<div id="div_right_preview" style="height:'+(hgt+5)+'px; overflow:hidden; overflow-y:auto;">'+ht+'</div>';
	
	ht = ht1;
	
	$('#page_body').html(ht);
//	left_pan_width = parseInt(($('#page_body').width()*33)/100);
//	loadLeftPreview(ArrFirstTestImages,left_pan_width);
//		$.colorbox({html:ht,width:"96%", height:"96%",onComplete:function(){
		if(m==0){
			for(k=0;k<hrefArr.length;k++){
				id = k+1;
				file_url = hrefArr[k];
				file_ext = file_url.substr(file_url.length-3);
				if(file_ext.toLowerCase()=='pdf'){
					file_url = file_url.substr(0,file_url.length-3)+'jpg';
				}
				var iv1 = $("#img_con_"+id).iviewer({
					   src: file_url,
					   update_on_resize: false,
					   zoom_animation: true,
					   mousewheel: true,
					   onMouseMove: function(ev, coords) { },
					   onStartDrag: function(ev, coords) { }, //return false; this image will not be dragged
					   onDrag: function(ev, coords) { }
				});
				
			}
			setIviewerImgHeight();
		}else if(m==1){
			L = 1;
			numoftests = 0
			for(xx in testInterArray){
				numoftests++;
			}
			for(yy in testInterArray){
				yy_arr = yy.split('_aabb_');//tid+'_aabb_'+tn
				tid = yy_arr[0];
				tn  = yy_arr[1];
				openTest(tn,tid,m,L,testInterArray,numoftests);
				L++;
			}
		}
//	}});

}

function loadLeftPreview(arrTimage,left_pan_width){
	ht = "";
	arrTimage1 = new Array();
	$(arrTimage).each(function(i,e){
		filename = arrTimage[i].split('/').pop();
		filename = arrTimage[i].replace(filename,filename);
		file_ext = filename.substr(-4);
		if(file_ext.toLowerCase()=='.pdf'){
			filename = filename.replace(file_ext,'.jpg');
		}else{
			filename = arrTimage[i];
		}
		arrTimage1[i] = filename;
		ht += '<div class="iviewer"><img src="'+filename+'" class="border" onClick="showIviewInColorBox(this);"></div>';
	});//alert(ht);a=window.open();a.document.write(ht);
	firstTestName1 = firstTestName.split('@');
	firstTestName1 = '<b>'+firstTestName1[0]+'</b> '+firstTestName1[1];
	test_id_tag = '<span style="display:inline-block; width:auto; padding:5px; position:absolute;z-index:1000;border-bottom-right-radius:10px;opacity:.7; filter:alpha(opacity=70);" class="bg4 link_cursor" onClick="$(\'a\').get(0).click();">'+firstTestName1+'</span>';
	$('#div_left_preview').html(test_id_tag+ht);
	tdw = left_pan_width-5;
	//alert(left_pan_width+" :: "+tdw+" :: "+$('#div_left_preview div.iviewer img').prop('src'));
	$('#div_left_preview div.iviewer img').css({'max-width':tdw});
	$('#div_left_preview').width(left_pan_width);
	setIviewerImgHeight();
}

function initDisp(){
	h 	= pageHeight();
	$('#page_body').hide();
	$('#div_film_strip, #header_row, #div_module_buttons').show();
	
//	var scrollDivH = parseInt($(".HscrollDiv").parent().parent().outerHeight(true));
	var footerBtnH = parseInt($("#module_buttons").outerHeight(true));
	var headerRowH = parseInt($("#header_row").outerHeight(true));
	hh 	= (h-(footerBtnH + headerRowH + 75 ));
	$('.HscrollDiv').css('max-height',hh);
}

function initDisp2(){ //when image interpretation starts
	h 	= pageHeight();
//	var scrollDivH = parseInt($(".HscrollDiv").parent().parent().outerHeight(true));
//	var footerBtnH = parseInt($("#module_buttons").outerHeight(true));
//	var headerRowH = parseInt($("#header_row").outerHeight(true));
	hh 	= (h-35);
	$('#page_body').height(hh);
}

function openTest(tn,tid,m,L,testInterArray,numoftests){
	if(typeof(m)=='undefined'){m=0;}
	if(m==0){
		showFinalize(tn,'0','0','0','0',tid);
	}else if(m==1){
		switch(tn)
		{
			case "A/Scan":
			case "Ascan":
				url = "ascan.php?pop=1";w = "1140";h = "775",n="docAS";
				break;
			case "B-Scan":
				url = "test_bscan.php?pop=1";w = "720";h = "670",n="docbscan";
				break;
			case "Cell Count":
			case "CellCount":
				url = "test_cellcount.php?pop=1";w = "720";h = "670",n="doccellcnt";
				break;
			case "Topography":
				url = "test_topography.php?pop=1";w = "720";h = "670",n="docTopo";
			break;
			case "IVFA":
				url = "test_ivfa.php?pop=1";w = "750";h = "670",n="docIvfa";
			break;
			case "HRT":
			case "NFA":
				url = "test_nfa.php?pop=1";w = "730";h = "670",n="docNfa";
			break;
			case "ICG":
				url = "test_icg.php?pop=1";w = "750";h = "670",n="docIcg";
				break;
			case "IOL Master":
			case "IOL_Master":
				url = "iol_master.php?pop=1";w = "1140";h = "775",n="docIOL_Master";
				break;
			case "test_labs":
				url = "test_labs.php?pop=1";w = "720";h = "670",n="docLabs";
				break;
			case "Pachy":
				url = "test_pacchy.php?pop=1";w = "680";h = "670",n="docPachy";
			break;
			case "VF":
				url = "test_vf.php?pop=1";w = "713";h = "670",n="docVF";
			break;
			case "VF-GL":
				url = "test_vf_gl.php?pop=1";w = "713";h = "670",n="docVFGL";
				str=n="VFGL";
			break;
			case "OCT":
				url = "test_oct.php?pop=1";w = "670";h = "670",n="docOCT";
			break;
			case "OCT-RNFL":
				url = "test_oct_rnfl.php?pop=1";w = "830";h = "670",n="docOCTRNFL";
				str=n="OCTRNFL";
			break;
			case "Other":
				url = "test_other.php?pop=1";w = "720";h = "670",n="docOthr";
				break;
			case "GDX":
				url = "test_gdx.php?pop=1";w = "670";h = "670",n="docGDX";
			break;
			case "External":				
				url = "test_external.php?pop=1";w = "890";h = "670",n="docExAnt";
			break;
			case "Disc":
			case "Fundus":
				url = "test_disc.php?pop=1";w = "730";h = "670",n="docDisc";
			break;
		}
		url += "&tId="+tid+'&doNotShowRightSide=yes&noP=1';
		if(numoftests>1){
			h = ($('#page_body').height()-20)/2;
		}else{
			h = ($('#page_body').height()-20);	
		}
		w = 750;
		htmvar = '<table border=0 class="table bg1" style="height:'+h+'px;"><tr><td class="valignTop" style="width:50%;"><iframe style="width:100%; height:'+h+'px;" src="../'+url+'" scrolling="yes" frameborder=0></iframe></td><td class="valignTop" style="width:50%;" id="imageNTestInterImgBox'+L+'">&nbsp;</td></tr></table>';
		$("#img_con_"+L).html(htmvar);
		var imgArr = testInterArray[tid+'_aabb_'+tn];
		var imgTags = '';
		ww = ($('#div_right_preview').width()-w);
		cnt_images = imgArr.length;
		for(i=0;i<cnt_images;i++){
			href = imgArr[i];
			file_ext = href.substr(href.length-3);
			if(file_ext.toLowerCase()=='pdf'){
				pdf_iframe_height = parseInt(h);
				if(cnt_images>1){
					pdf_iframe_height = parseInt(h); //pdf frame height can be half /2 or full.
				}
				imgTags += '<iframe src="'+href+'" style="width:'+(ww-25)+'px; height:'+pdf_iframe_height+'px;"></iframe><br>';
			}else{
				imgTags += '<div id="iv'+(i+1)+'" class="iviewer"><img src="'+href+'" style="width:'+(ww-25)+'px;" onClick="showIviewInColorBox(this);"></div>';
			}
		}
		imgTags = '<div style="height:'+parseInt(h)+'px;width:'+ww+'px;overflow:auto;">'+imgTags+'</div>';
		$('#imageNTestInterImgBox'+L).html(imgTags);
	}
	setIviewerImgHeight();
	
}

function setIviewerImgHeight(){return;
	$('div.iviewer,td.iviewer').each(function(){
		conH = $(this).height();
		conW = $(this).width();
		$(this).find('img').css({'max-height':conH-25,'max-width':conW});
	});
}

function showIviewInColorBox(o){
	con = $(o).parent('div,td');
	src = $(o).prop('src');
	con.css({height:$(o).height(),width:$(o).width()});
	con.html('');
	var iv11 = con.iviewer({
		   src: src,
		   update_on_resize: false,
		   zoom_animation: true,
		   mousewheel: true,
		   onMouseMove: function(ev, coords) { },
		   onStartDrag: function(ev, coords) { }, //return false; this image will not be dragged
		   onDrag: function(ev, coords) { }
	});
}
function get_loading_div(){
	str = '<div id="div_loading_image" class="loading" style="left:auto;top:auto;margin-top:10px;display:none;"><div id="div_loading_text" style="width:100px;position:relative;padding-top:50px;display:none;text-align:center;"></div></div>';
	return str;
}
function show_images(testName, id, obj,test_id,examDate,testType,span_id){
	test_id = test_id || '';
	testType = testType || 0;
	examDate = examDate || '';
	HIDEId = "div_"+testName;
	
	$(obj).parent().addClass("active");
	if($(obj).prop("tagName") == "LI"){
		ele = $(obj).parents('span');
		date = $(obj).html();
		$('<span class="active"><a href="#" class="a_clr1" onClick="show_images(\''+testName+'\',\''+id+'\',this,\''+test_id+'\',\''+examDate+'\');" onDblClick="openTest(\''+testName+'\',\''+test_id+'\');">'+date+'</a></span>').insertBefore(ele);
		if($(obj).parents('ul').children('li').length == 1)
		$(obj).parents('span').remove();
		$(obj).remove();
		
	}
	jId = "#"+id.replace(" ","_");
	if($(jId).length){
		
		$("div[id='"+HIDEId+"'] div.filmstrip").each(function(index, element) {
			$(this).hide();
		});
		
		$("div[id='"+HIDEId+"'] .test_tabs ul li").each(function(index, element) {
			$(this).removeClass('active');
			$(this).children('ul').hide();
		});
		
		$(jId).show();
	}
	else{
		
		testNameSpan = testName.replace(" ","_");
		if(typeof(span_id)=='undefined') span_id = "#span_"+testNameSpan+"_"+test_id;
		$(span_id).html(get_loading_div());
		//window.opener.top.show_loading_image("show");
		$(span_id).show();
		$.ajax({
				url: "load_images.php?testDname="+testName+"&test_id="+test_id+"&examDate="+examDate+"&testType="+testType,
				type: "POST",
				dataType:"json",
				success: function(r){
					
					$("div[id='"+HIDEId+"'] div.filmstrip").each(function(index, element) {
						$(this).hide();
					});
					
					$("div[id='"+HIDEId+"'] .test_tabs ul li").each(function(index, element) {
						$(this).removeClass('active');
						$(this).children('ul').hide();
					});
					
					
					$(span_id).html(r.html);
					scanImgArr = r.scanImgArr;
					for(id in scanImgArr){
						ArrScanHref[id] = scanImgArr[id];
					}
					TestWiseImgs = r.TestWiseImgs;
					for(id in TestWiseImgs){
						ArrTestWiseScanHref[id] = TestWiseImgs[id];
					}
					$(span_id).show();
					//--------------------
					$(".pdf_colorbox").colorbox({rel:'pdf_colorbox',iframe:true, width:"90%", height:"90%"});
					$(".jpg_colorbox").colorbox({rel:'jpg_colorbox',iframe:false, width:"auto", height:"90%"});
					$(".pdf_colorbox, .jpg_colorbox").mousemove(function(e){
						ww = $('#page_body').width();
						relX = e.pageX;
						relY = e.pageY-400;
						if(ww-relX <= 350){relX= relX-470;relY= relY;}
						$('#temp_container').css({'top':relY+'px','left':relX+'px'});
					});
					$(".pdf_colorbox, .jpg_colorbox").hover(function(e){
						if($("#temp_container").html()==''){
							imgsrc = $(this).find('img').prop('src');
							imgsrc = imgsrc.replace("thumbnail","thumb");
							$("#temp_container").html('<div class="div_shadow" style="width:auto;"><div class="div_shadow_container" style="overflow:hidden"><img src="'+imgsrc+'" style="height:370px;"></div></div>');
							$("#temp_container").css({'display':'block'});;
						}
					});
					$(".pdf_colorbox, .jpg_colorbox").mouseout(function(e){
						//$("#temp_container").fadeOut();
						$("#temp_container").html('').css({'display':'none'});
					});

					//------POSITIONING MOUSE HOVER BIG IMAGE--
					$(".pdf_colorbox, .jpg_colorbox").mousemove(function(e){
						ww = $('#page_body').width();
						relX = e.pageX+10;
						relY = e.pageY-200;
						//if(ww-relX <= 350){relX= relX-470;relY= relY;}
						if(relY < 10) relY = 10;
						w = $('#temp_container').width();
						mx_w = parseInt(w+relX);
					//	console.log('ww='+ww+' :: mx_w='+mx_w);
						if(ww<=mx_w) relX = parseInt(relX)-(parseInt(w)+20);
						$('#temp_container').css({'top':relY+'px','left':relX+'px'});
					});
				}
		});
		$('.iviewer_selector').iviewer('loadImage', "load_images.php?testDname="+testName+"&test_id="+test_id+"&examDate="+examDate+"&testType="+testType);
	}
}

window.onresize = initDisp;

function selectAllImages(){
	var a1=".scanDate", a2=".scanDateSel", v='Select All Images';
	if($(a2).length > 0 && $(a1).length > 0 && $(a2).length!=$(a1).length){ $(a1).not(a2).trigger("click"); }
	else{ $(a1).trigger("click");  }
	
	if($(a2).length==$(a1).length){ v="Un"+v; }
	$("#btn_sel_all_images").val(v);
}
