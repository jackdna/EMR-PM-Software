// common.js JavaScript Document   ......
function invalid_input_msg(obj,msg,class_name){
	class_name = typeof(class_name) == 'undefined' ? 'mandatory' : class_name;
	obj.className += " " + class_name;
	//obj.className = class_name;
	//alert(msg);
	
	if(typeof(top.fAlert)!="undefined") {
		top.fAlert(msg);
	}else if(typeof(fancyAlert)!="undefined") {
		top.fancyAlert(msg);
	}
	 
	obj.select();
	obj.focus();
	obj.value='';
	//return false;
}
String.prototype.trim = function() {
    return this.replace(/^\s+|\s+$/g, "");
};
String.prototype.trimMultiSpace = function() {
    return this.replace(/\s{2,}/g, " ");
};
String.prototype.trimHyphen = function() {
    return this.replace(/^\-|\-$/g, "");
};
String.prototype.trimMultiHyphen = function() {
    return this.replace(/\-{2,}/g, "-");
};
function set_phone_format(objPhone,default_format,phone_length,msg_txt,class_name){
	
	phone_length = phone_length || top.phone_length;
	phone_min_length = top.phone_min_length;
	default_format = default_format || top.phone_format;
	msg_txt = msg_txt || "phone";
	class_name = typeof(class_name) == 'undefined' ? 'mandatory' : class_name;
	phone_reg_exp_js =  (typeof(top.phone_reg_exp_js)!="undefined" && typeof(top.phone_reg_exp_js)!=null && top.phone_reg_exp_js!='') ? top.phone_reg_exp_js : "[^0-9+]";
	regExp = new RegExp(phone_reg_exp_js,'g');
	var refinedPh = objPhone.value.replace(regExp,"");	
	refinedPh = refinedPh.trim();
	refinedPh = refinedPh.trimHyphen();
	refinedPh = refinedPh.trimMultiHyphen();
	refinedPh = refinedPh.trimMultiSpace();
	if(refinedPh.length < phone_min_length){
		invalid_input_msg(objPhone, "Please Enter a valid "+msg_txt+" number",class_name);return;
	}else{
			refinedPh = refinedPh.substr(0,phone_length);
			//invalid_input_msg(objPhone, "Please Enter a valid phone number");
			switch(default_format){
				case "###-###-####":
					objPhone.value = refinedPh.substring(0,3)+"-"+refinedPh.substring(3,6)+"-"+refinedPh.substring(6,10);
				break;
				case "(###) ###-####":
					objPhone.value = "("+refinedPh.substring(0,3)+") "+refinedPh.substring(3,6)+"-"+refinedPh.substring(6,10);
				break;
				case "(##) ###-####":
					objPhone.value = "("+refinedPh.substring(0,2)+") "+refinedPh.substring(2,5)+"-"+refinedPh.substring(5,9);
				break;
				case "(###) ###-###":
					objPhone.value = "("+refinedPh.substring(0,3)+") "+refinedPh.substring(3,6)+"-"+refinedPh.substring(6,9);
				break;
				case "(####) ######":
					objPhone.value = "("+refinedPh.substring(0,4)+") "+refinedPh.substring(4,10);
				break;
				case "(####) #####":
					objPhone.value = "("+refinedPh.substring(0,4)+") "+refinedPh.substring(4,9);
				break;
				case "(#####) #####":
					objPhone.value = "("+refinedPh.substring(0,5)+") "+refinedPh.substring(5,10);
				break;
				case "(#####) ####":
					objPhone.value = "("+refinedPh.substring(0,5)+") "+refinedPh.substring(5,9);
				break;
				default:
					objPhone.value = refinedPh;
				break;
			}
	}
	//changeClass(objPhone);
}
function core_phone_format(phone,default_format,phone_length){
	phone_length = phone_length || top.phone_length;
	phone_min_length = top.phone_min_length;
	default_format = default_format || top.phone_format;
	phone_reg_exp_js =  (typeof(top.phone_reg_exp_js)!="undefined" && typeof(top.phone_reg_exp_js)!=null && top.phone_reg_exp_js!='') ? top.phone_reg_exp_js : "[^0-9+]";
	regExp = new RegExp(phone_reg_exp_js,'g');
	var refinedPh = phone.replace(regExp,"");	
	refinedPh = refinedPh.trim();
	refinedPh = refinedPh.trimHyphen();
	refinedPh = refinedPh.trimMultiHyphen();
	refinedPh = refinedPh.trimMultiSpace();
	var formatted_phone = '';
	if(refinedPh.length < phone_min_length){
		return formatted_phone;
	}else{
			refinedPh = refinedPh.substr(0,phone_length);
			//invalid_input_msg(objPhone, "Please Enter a valid phone number");
			switch(default_format){
				case "###-###-####":
					formatted_phone = refinedPh.substring(0,3)+"-"+refinedPh.substring(3,6)+"-"+refinedPh.substring(6,10);
				break;
				case "(###) ###-####":
					formatted_phone = "("+refinedPh.substring(0,3)+") "+refinedPh.substring(3,6)+"-"+refinedPh.substring(6,10);
				break;
				case "(##) ###-####":
					formatted_phone= "("+refinedPh.substring(0,2)+") "+refinedPh.substring(2,5)+"-"+refinedPh.substring(5,9);
				break;
				case "(###) ###-###":
					formatted_phone = "("+refinedPh.substring(0,3)+") "+refinedPh.substring(3,6)+"-"+refinedPh.substring(6,9);
				break;
				case "(####) ######":
					formatted_phone = "("+refinedPh.substring(0,4)+") "+refinedPh.substring(4,10);
				break;
				case "(####) #####":
					formatted_phone= "("+refinedPh.substring(0,4)+") "+refinedPh.substring(4,9);
				break;
				case "(#####) #####":
					formatted_phone = "("+refinedPh.substring(0,5)+") "+refinedPh.substring(5,10);
				break;
				case "(#####) ####":
					formatted_phone = "("+refinedPh.substring(0,5)+") "+refinedPh.substring(5,9);
				break;
				default:
					formatted_phone = refinedPh;
				break;
		}
		return formatted_phone;
	}
}

function isDefined(variable){return (!(!( variable||false )))}
window.onload = make_maindiv_full;
function make_maindiv_full(){wh = window.screen.availHeight-100;//alert(typeof(main));
if(isDefined(dgi('main'))) {dgi('main').style.height=wh+'px';if(isDefined(dgi('iMedicDiv')))dgi('iMedicDiv').style.height=wh-180 +"px"; if(isDefined(dgi('iMedicFrame')))dgi('iMedicFrame').style.height=wh-182 +"px";}
	//	alert(dgi('main').style.height+'::'+screen.availHeight);
}//end of make_maindiv_full

function foo(){} // only to pass in href of Anchors which fires event for onClick.
function ele(id,task){dgi(id).style.display=task;} // to show or hide any block with id.

//functions below are just the short codes
function dgi(id){return document.getElementById(id);}
function dgn(name){return document.getElementsByName(name);}
function dgt(tag){return document.getElementsByTagName(tag);}
function charlimit(field,count,showin){var tex = field.value; var len = tex.length; if(len > count){tex = tex.substring(0,count); field.value = tex; return false;}dgi(showin).innerHTML = count-len;}
function trim(val) { return val.replace(/^\s+|\s+$/, ''); }
function alphanum(o,w){var r={'special':/[\W]/g}; o.value = o.value.replace(r[w],'');}
function emv(entered){with (entered){apos=entered.indexOf("@");dotpos=entered.lastIndexOf(".");lastpos=entered.length-1;if (apos<1 || dotpos-apos<2 || lastpos-dotpos>3 || lastpos-dotpos<2) {return false;}else {return true;}}}
function wl(url){window.location.href=url;}

function pageWidth() { return window.innerWidth != null ? window.innerWidth : document.documentElement && document.documentElement.clientWidth ? document.documentElement.clientWidth : document.body != null ? document.body.clientWidth : null;}

// calculate the current window height //
function pageHeight() { return window.innerHeight != null? window.innerHeight : document.documentElement && document.documentElement.clientHeight ? document.documentElement.clientHeight : document.body != null? document.body.clientHeight : null;}

// calculate the current window vertical offset //
function topPosition() { return typeof window.pageYOffset != 'undefined' ? window.pageYOffset : document.documentElement && document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop ? document.body.scrollTop : 0;}

// calculate the position starting at the left of the window //
function leftPosition() { return typeof window.pageXOffset != 'undefined' ? window.pageXOffset : document.documentElement && document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft ? document.body.scrollLeft : 0;}


//fancyAlert is just a replace of traditional javascript alert msgbox.
function fancyAlert(msg,title,callback, obj,facnyBtn1,facnyBtn2,func1,func2,mask,maskOpacity, width, height_adjustment, autoClose, showClose, left, top, drag,facnyBtn3,func3){
	//alert(height_adjustment);
	var isCenter = "";
	left = left || "";
	top = top || "";
	if(typeof(width) == "undefined") width = 300;
	if(typeof(autoClose) == "undefined" || !autoClose) autoClose = "yes";
	if(typeof(title)!="string") title='imwemr';
	if(title =="") title='imwemr';
	if(callback==null){callback='';}
	if(!facnyBtn1) {facnyBtn1='OK';}
	if(!facnyBtn2) {facnyBtn2=false;}
	if(!facnyBtn3) {facnyBtn3=false;}
	if(!func1) {func1=callback;}
	if(!func2) {func2=callback;}
	if(!func3) {func3=callback;}
	if(typeof(mask) != "boolean"){mask=true;}
	if(typeof(drag) != "boolean"){drag=true;}
	if(autoClose == "yes"){
		func1 = 'closeDialog();'+func1;
		func2 = 'closeDialog();'+func2;
	}
	if((left != "") || (top != "")){
		isCenter = false
	}
	dialogBox(title,msg,facnyBtn1,facnyBtn2,func1,func2,false,mask,drag,callback, width, "", isCenter, left, top, showClose, obj,maskOpacity, height_adjustment,facnyBtn3,func3);
}
function dialogBox(title,msgg,btn1,btn2,func1,func2,btnCancel,mask,drag,callback,w,h,isCenter,l,t,showClose,inThis,maskOpacity, height_adjustment,btn3,func3)
{
/*********************A MASTER FUNCTION TO DISPLAY ALL TYPES OF DIALOG BOXES***************/
/*  
	Date: 13 August 2010
	
PARAMETER DESCRIPTION
title 	  -> to diaplay text on the title bar of the dialog box.
msg   	  -> this text will be displayed as the body of the dialog box.
btn1      -> text value displayed as the caption of 1st button. ("false" if want no button)
btn2      -> text value displayed as the caption of 2nd button. ("false" if want no button)
func1     -> Function(with parameter if applicable) Executed by click of btn1. ("" if you are using no button)
func2     -> Function(with parameter if applicable) Executed by click of btn2. ("" if you are using no button)
btnCancel -> OPTIONAL. value can be true/false. Default="false". To display an additonal CANCEL hide dialog box.
mask	  -> OPTIONAL. value can be true/false. Setting it to "true" will lock & cover the body with transparency.
drag      -> OPTIONAL. value can be true/false. Setting it to "true" will enable the user to drag the dialog box.
callback  -> OPTIONAL. Any function which you want to triger when dialog box will be closed/dis-appeared.
w		  -> OPTIONAL. Integer value. By Default is 300 (pixels). Width of the dialog box.
h		  -> OPTIONAL. Integer value. By Default is "auto". Height of the dialog box.
isCenter  -> OPTIONAL. value can be true/fale. Default="true". This will appear dialog box in centre of web page.
l		  -> OPTIONAL. Integer Value. Left spacing of dialog box. Work only if "isCenter" property is 'false'.
t		  -> OPTIONAL. Integer Value. Top spacing of dialog box. Work only if "isCenter" property is 'false'.
showClose -> OPTIONAL. Value can be true or false. Default=true. To show close button on top-right corner the title bar of dialog box.
*****************************************************************************************************/

	var text = ''; 
	if((typeof(w) == "string" && w=='default') || typeof(w) == "undefined" || w<=0){w="500px";}else{w=(parseInt(w)+60)+"px";}
	if((typeof(h) == "string" && h=='default') || typeof(h) == "undefined" || h<=0){h="auto";}else{h=parseInt(h)+"px";}
	if(typeof(isCenter) != "boolean"){isCenter=true;}
	if(!maskOpacity) {maskOpacity=50;maskBrowserOpacity=0.5;}
	if(typeof(mask) != "boolean"){mask=false;maskOpacity=0;maskBrowserOpacity=0;}
	if(maskOpacity > 0) { maskBrowserOpacity = maskOpacity/100; }
	if(typeof(drag) != "boolean"){drag=false;}
	if(typeof(btnCancel) != "boolean"){btnCancel=false;}
	if(typeof(showClose) != "boolean"){showClose=true;}	
	var width = pageWidth();
  	var height = pageHeight();
	var left = leftPosition();
	var topP = topPosition();
	if(typeof(l) != "undefined" && !isCenter){left=l+"px";}
	if(typeof(t) != "undefined" && !isCenter){topP=t+"px";}
	var dialogwidth = w;  
	var dialogheight = h; //alert('TypeofLeft='+typeof(dialogheight)+', height='+typeof(height))
	var topposition = 100;
	var leftposition = parseInt(left) + (width / 2) - (parseInt(dialogwidth) / 2);
	if(typeof(dialogBoxCounter)=="undefined") dialogBoxCounter=0; else dialogBoxCounter++;
	if(callback==null){callback=false;}else if(callback==''){callback=false;}


	
	/*trying to embedd Messi--*/
	var butonnsArr = new Array();
	YsAction = NoAction = ThirdAction = '';
	if(typeof(btn1) == "string" && typeof(func1) == "string"){
		butonnsArr[0] = {'id': 0, 'label': btn1, 'val': 'Y'}
		YsAction = func1;
		if(YsAction.indexOf('window.top.')==-1 && YsAction.indexOf('top.')==0){YsAction = "window."+YsAction;}
	}
	if(typeof(btn2) == "string" && typeof(func2) == "string"){
		butonnsArr[butonnsArr.length] = {'id': 1, 'label': btn2, 'val': 'N'}
		if(func2!='return' && func2!='return false'){
			//func2 = func2.replace(Array('return false;','return;','return false'),'');
			NoAction = func2;
			if(NoAction!=false && NoAction.indexOf('window.top.')==-1 && NoAction.indexOf('top.')==0){NoAction = "window."+NoAction;}
		}
	}
	if(typeof(btn3) == "string" && typeof(func3) == "string"){
		butonnsArr[butonnsArr.length] = {'id': 2, 'label': btn3, 'val': 'three'}
		if(func3!='return' && func3!='return false'){
			ThirdAction = func3;
			if(ThirdAction!=false && ThirdAction.indexOf('window.top.')==-1 && ThirdAction.indexOf('top.')==0){ThirdAction = "window."+NoAction;}
		}
	}
	
	MessiOptionsArr = {
		'title':		title,
		'modal':		true,
		'closeButton':	true,
		'width':		w,
		'height':		h,
		'buttons':		butonnsArr,
		'callback':		function(val){
							if(val=='Y'){
								eval(YsAction);
							//	try{eval(YsAction);} catch(e){alert(e.message);}
							}else if(val=='N' && NoAction!=false){
								eval(NoAction);
							//	try{eval(NoAction);}catch(e){alert(e.message);}
							}else if(val=='three' && ThirdAction!=false){
								eval(ThirdAction);
							//	try{eval(ThirdAction);}catch(e){alert(e.message);}
							}
						}
	};
	//a=window.open();a.document.write(btn1+"<hr>"+func1+'<hr><hr>'+btn2+"<hr>"+func2);
	max_h = parseInt(height)-180;
	msgg = '<div style="max-height:'+max_h+'px; overflow:auto;">'+msgg+'</div>';
	new window.top.Messi(msgg, MessiOptionsArr);
	/* Messi embedding end---*/


}//end of function dialogBox.

function closeDialog(callback){return;
	if(callback==null){callback=false;}else if(callback==''){callback=false;}else{callback='function(){'+callback+';}';}
	//alert(callback);
	$('body .dialogMask').fadeOut('slow');
	$('#divCon').fadeOut('slow');
	$('#divCon').parent('div,span').html('');
//	el = dgi('divCon');
//	el.parentNode.removeChild(el);
}

//--DRAG HANDLER START
var DragHandler = {
	// private property.
	_oElem : null,

	// public method. Attach drag handler to an element.
	attach : function(oElem) {
		oElem.onmousedown = DragHandler._dragBegin;
		// callbacks
		oElem.dragBegin = new Function();
		oElem.drag = new Function();
		oElem.dragEnd = new Function();
		return oElem;
	},

	// private method. Begin drag process.
	_dragBegin : function(e) {
		var oElem = DragHandler._oElem = this;
		if (isNaN(parseInt(oElem.style.left))) { oElem.style.left = '0px'; }
		if (isNaN(parseInt(oElem.style.top))) { oElem.style.top = '0px'; }
		var x = parseInt(oElem.style.left);
		var y = parseInt(oElem.style.top);
		e = e ? e : window.event;
		oElem.mouseX = e.clientX;
		oElem.mouseY = e.clientY;
		oElem.dragBegin(oElem, x, y);
		document.onmousemove = DragHandler._drag;
		document.onmouseup = DragHandler._dragEnd;
		return false;
	},
 
	// private method. Drag (move) element.
	_drag : function(e) {
		var oElem = DragHandler._oElem;
		var x = parseInt(oElem.style.left);
		var y = parseInt(oElem.style.top);
		e = e ? e : window.event;
		oElem.style.left = x + (e.clientX - oElem.mouseX) + 'px';
		oElem.style.top = y + (e.clientY - oElem.mouseY) + 'px';
		oElem.mouseX = e.clientX;
		oElem.mouseY = e.clientY;
 		oElem.drag(oElem, x, y);
 		return false;
	},
 
 // private method. Stop drag process.
	_dragEnd : function() {
		var oElem = DragHandler._oElem;
 		var x = parseInt(oElem.style.left);
		var y = parseInt(oElem.style.top);
 		oElem.dragEnd(oElem, x, y);
 		document.onmousemove = null;
		document.onmouseup = null;
		DragHandler._oElem = null;
	}
}
//----END OF DRAG HANDLER----------

/*-----------function to get, provided object is full vertically scrolled or not---*/
function isScrolledV(id){id='#'+id;OuterHeight = $(id).height();InnerHeight = $(id).prop('scrollHeight');Scrolled = $(id).scrollTop();
//console.log('id='+id+', OuterHeight='+OuterHeight+', InnerHeight='+InnerHeight+', Scrolled='+Scrolled);
if(OuterHeight+Scrolled >= InnerHeight) return true; else return false;}

//---STARTING FUNCTION TO STOP F5, CTRL+F5, CTRL+N --------

window.document.onkeydown=function(event)
{
	switch (event.keyCode){
		case 121 : //F10 logout
			event.returnValue=false;
			event.keyCode=0;
			alert('This will Close all the work and LogOut');
			return false;
		break;
		case 122 : //F11 stop full screen 
			event.returnValue=false;
			event.keyCode=0;
			top.preview_patient_details(event);
			return false;
		break;
		case 27 : //Escape
		case 116 : //F5
			do_audit = 'no';
			auditHome = 'no';
			audit = 'no';
			
			var switchUser = ($("#div_switch_user").data('bs.modal') || {}).isShown;
			if(switchUser === true){
				//event.stopPropagation();
				return false;	
			}
			
			break;
		case 82 : //ctrl+r
			if (event.ctrlKey){
				do_audit = 'no';
				auditHome = 'no';
				audit = 'no';		
				var switchUser = ($("#div_switch_user").data('bs.modal') || {}).isShown;
				if(switchUser === true){
					event.preventDefault();
					return false;	
				}	
			}
		break;
	}
}
window.document.oncontextmenu=function(event){
	var switchUser = ($("#div_switch_user").data('bs.modal') || {}).isShown;
	if(switchUser === true){
		 event.stopPropagation();
		return false;	
	}
}

//--------END OF REFRESH AND NEW WINDOW KEY STROKE PREVENTION-----

/*---MODIFIED FANCY ALERT (USED IN TESTS)--*/
function fAlert(msg, title, actionToPerform, width, height, BtnCaption,ModalMode) {
	if(typeof(title)=='undefined' || title=='') 			title='imwemr';
	if(typeof(actionToPerform)=='undefined') 				actionToPerform='';
	if(typeof(width)=='undefined' || width=='') 			width='500px';
	if(typeof(height)=='undefined' || height=='') 			height='auto';
	if(typeof(BtnCaption)=='undefined' || BtnCaption=='') 	BtnCaption='OK';
	if(typeof(ModalMode)!='boolean')						ModalMode=true;
	if(BtnCaption=='CLOSE' || BtnCaption=='Close'){closeBtn=false;} else{closeBtn=true;}
	//if(actionToPerform.indexOf('window.top.')== -1 && actionToPerform.indexOf('top.')== 0){actionToPerform = "window."+actionToPerform;}
	msg = '<div style="max-height:500px; overflow:auto;">'+msg+'</div>';
	if(typeof(Messi)=='function'){
		new Messi(msg, {'title': title, modal: ModalMode, 'width': width, 'closeButton':closeBtn, buttons: [{id: 0, label: BtnCaption, val: 'X'}],callback: function(){if(typeof(actionToPerform)=='string' && actionToPerform!='' && actionToPerform.indexOf('/*callmebeforeclosingmessi*/')>=0){actionToPerform;}else if(typeof(actionToPerform)=='string' && actionToPerform!='' && actionToPerform.indexOf('/*callmebeforeclosingmessi*/')<0){eval(actionToPerform);}else if(typeof(actionToPerform)=='object'){actionToPerform.focus();}}});
	}else if(typeof(window.top.Messi)=='function'){
		new window.top.Messi(escape(msg), {'title': title, modal: ModalMode, 'width': width, 'closeButton':closeBtn, buttons: [{id: 0, label: BtnCaption, val: 'X'}],callback: function(){if(typeof(actionToPerform)=='string' && actionToPerform!='' && actionToPerform.indexOf('/*callmebeforeclosingmessi*/')>=0){actionToPerform;}else if(typeof(actionToPerform)=='string' && actionToPerform!='' && actionToPerform.indexOf('/*callmebeforeclosingmessi*/')<0){eval(actionToPerform);}else if(typeof(actionToPerform)=='object'){actionToPerform.focus();}}});	
	}
}
function fancyConfirm(msg, title, YsAction, NoAction, width, height,btn1,btn2) {
	if(typeof(YsAction)=='undefined') {YsAction=title; title='';}
	if(typeof(title)=='undefined' || title=='') title='imwemr';
	if(typeof(width)=='undefined' || width=='') width='500px';
	if(typeof(NoAction)=='undefined' || NoAction=='') NoAction=false;
	if(YsAction.indexOf('window.top.')==-1 && YsAction.indexOf('top.')==0){YsAction = "window."+YsAction;}
	if(NoAction!=false && NoAction.indexOf('window.top.')==-1 && NoAction.indexOf('top.')==0){NoAction = "window."+NoAction;}
	if(typeof(btn1)=='undefined' || btn1=='') btn1='Yes';
	if(typeof(btn2)=='undefined' || btn2=='') btn2='No';
	
	new window.top.Messi(msg, {'title': title, modal: true, 'width': width, buttons: [{id: 0, label: btn1, val: 'Y', "class": 'btn-success'}, {id: 1, label: btn2, val: 'N', "class": 'btn-danger'}],callback: function(val){if(val=='Y'){eval(YsAction);}else if(val=='N' && NoAction!=false){eval(NoAction);}}});
}
function fancyModal(html,title,w,h,closeBtn,po,ModalMode){//positionObject=po;
	if(typeof(title)!="string") 			title=null;
	if(typeof(w)!="string") 				w='';
	if(typeof(h)!="string") 				h='';
	if(typeof(closeBtn)=="undefined")		closeBtn=true;
	if(typeof(po)!='undefined') 			{of = $(po).offset();}else po = false;
	if(typeof(ModalMode)!='boolean')		ModalMode=true;
	if(po){new top.Messi(html,{'title':title,modal:ModalMode,'width':w,'height':h,'closeButton':closeBtn,center:false,viewport:{top:of.top+25,left:of.left+25}});}
	else{new top.Messi(html,{'title':title,modal:ModalMode,'width':w,'height':h,'closeButton':closeBtn});}
}
function removeMessi(){window.top.$('.messi-modal,.messi').remove();}


//Stopping value save/pwd save prompts and autocomplete.
if(typeof($)!='undefined'){
	$(document).ready(function(e){$('form, input').attr('autocomplete','off');});
	$(document).ajaxComplete(function(event, request, settings) {
    if (request.getResponseHeader('REQUIRES_AUTH') === '1') {
			top.window.location = top.JS_WEB_ROOT_PATH + '/interface/login/index.php';
    }
	});
}
/*---END OF MODIFIED (USED IN TESTS)--*/
/*----------------BEGIN FUNCTIONS CREATED FOR INTERNATIONALIZATION WORK-----------------*/
function fnArrDate(dateString,format){
	dateString = dateString || '';
	format = format || 'yyyy-mm-dd'
	separator = get_date_separator(dateString)
	arrBirthDate = dateString.split(separator);
	switch(format){
		case "dd"+separator+"mm"+separator+"yyyy":
			date =  arrBirthDate[0];
			month = arrBirthDate[1];
			year = arrBirthDate[2];
		break;
		case "mm"+separator+"dd"+separator+"yyyy":
			month =  arrBirthDate[0];
			date = arrBirthDate[1];
			year = arrBirthDate[2];
		break;
		case "yyyy"+separator+"mm"+separator+"dd":
			year =  arrBirthDate[0];
			month = arrBirthDate[1];
			date = arrBirthDate[2];
		break;
		case "yyyy"+separator+"dd"+separator+"mm":
			year =  arrBirthDate[0];
			month = arrBirthDate[2];
			date = arrBirthDate[1];
		break;
		default://---"mm-dd-yyyy":
			month =  arrBirthDate[0];
			date = arrBirthDate[1];
			year = arrBirthDate[2];
	}
	return [year,month,date]
}
function get_date_separator(date){
	date = date || '';
	separator = '-';
	if(date.search("-")!="-1")
	separator = "-";
	else if(date.search("/")!="-1")
	separator = "/";
	else if(date.search("/\\/")!="-1")
	separator = "\\";
	return separator;
}
function validate_date(objName, format) {
	date = typeof(objName)=="object" ? objName.value : objName;
	if(date!=''){
		//jquery_date_format = (typeof(top.opener) != 'undefined' ? top.opener.top.jquery_date_format:(typeof(opener) != 'undefined' ? opener.top.jquery_date_format:top.jquery_date_format));
		if(typeof(top.jquery_date_format) != "undefined"){
			jquery_date_format = top.jquery_date_format;
		}else if(typeof(opener.top.jquery_date_format) != "undefined"){
			jquery_date_format = opener.top.jquery_date_format;
		}else if(typeof(top.opener.top.jquery_date_format) != "undefined"){
			jquery_date_format = top.opener.top.jquery_date_format;
		}
		format = format || jquery_date_format;
		separator = get_date_separator(format);
		var regex = "";
		switch(format){
			case "dd"+separator+"mm"+separator+"yyyy":
			var regex = new RegExp("(((0[1-9]|[12][0-9]|3[01])["+separator+"](0[13578]|1[02])["+separator+"](19|20[0-9]{2}))|((0[1-9]|[12][0-9]|3[0])["+separator+"](0[469]|1[1])["+separator+"](19|20[0-9]{2}))|((0[1-9]|1[0-9]|2[0-9])["+separator+"](02)["+separator+"]((19|20)(04|08|[2468][048]|[13579][26])))|((0[1-9]|1[0-9]|2[0-8])["+separator+"](02)["+separator+"]((19|20)[0-9]{2})))");
			if(typeof(objName)=="object")
			date = date.replace(/(\d{2})(\d{2})(\d{4})/,'$1'+separator+'$2'+separator+'$3');
			break;
			case "mm"+separator+"dd"+separator+"yyyy":
			var regex = new RegExp("(((0[13578]|1[02])["+separator+"](0[1-9]|[12][0-9]|3[01])["+separator+"](19|20[0-9]{2}))|((0[469]|1[1])["+separator+"](0[1-9]|[12][0-9]|3[0])["+separator+"](19|20[0-9]{2}))|((02)["+separator+"](0[1-9]|1[0-9]|2[0-9])["+separator+"]((19|20)(04|08|[2468][048]|[13579][26])))|((02)["+separator+"](0[1-9]|1[0-9]|2[0-8])["+separator+"]((19|20)[0-9]{2})))");
			if(typeof(objName)=="object")
			date = date.replace(/(\d{2})(\d{2})(\d{4})/,'$1'+separator+'$2'+separator+'$3');
			break;
			case "mm"+separator+"dd"+separator+"yy":alert("mm"+separator+"dd"+separator+"yy");
			var regex = new RegExp("(((0[13578]|1[02])["+separator+"](0[1-9]|[12][0-9]|3[01])["+separator+"](19|20[0-9]{2}))|((0[469]|1[1])["+separator+"](0[1-9]|[12][0-9]|3[0])["+separator+"](19|20[0-9]{2}))|((02)["+separator+"](0[1-9]|1[0-9]|2[0-9])["+separator+"]((19|20)(04|08|[2468][048]|[13579][26])))|((02)["+separator+"](0[1-9]|1[0-9]|2[0-8])["+separator+"]((19|20)[0-9]{2})))");
			if(typeof(objName)=="object")
			date = date.replace(/(\d{2})(\d{2})(\d{2})/,'$1'+separator+'$2'+separator+'$3');
			break;
			case "yyyy"+separator+"mm"+separator+"dd":
			var regex = new RegExp("(((19|20[0-9]{2})["+separator+"](0[13578]|1[02])["+separator+"](0[1-9]|[12][0-9]|3[01]))|((19|20[0-9]{2})["+separator+"](0[469]|1[1])["+separator+"](0[1-9]|[12][0-9]|3[0]))|(((19|20)(04|08|[2468][048]|[13579][26]))["+separator+"](02)["+separator+"](0[1-9]|1[0-9]|2[0-9]))|(((19|20)[0-9]{2}["+separator+"](02)["+separator+"](0[1-9]|1[0-9]|2[0-8]))))");
			//var regex = new RegExp("([1-9]{4}["+separator+"][0-9]{2}["+separator+"][0-9]{2})");
			if(typeof(objName)=="object")
			date = date.replace(/(\d{4})(\d{2})(\d{2})/,'$1'+separator+'$2'+separator+'$3');
			break;
			case "yyyy"+separator+"dd"+separator+"mm":
			var regex = new RegExp("(((19|20[0-9]{2})["+separator+"](0[1-9]|[12][0-9]|3[01])["+separator+"](0[13578]|1[02]))|((19|20[0-9]{2})["+separator+"](0[1-9]|[12][0-9]|3[0])["+separator+"](0[469]|1[1]))|(((19|20)(04|08|[2468][048]|[13579][26]))["+separator+"](0[1-9]|1[0-9]|2[0-9])["+separator+"](02))|(((19|20)[0-9]{2}["+separator+"](0[1-9]|1[0-9]|2[0-8])["+separator+"](02))))");
			if(typeof(objName)=="object")
			date = date.replace(/(\d{4})(\d{2})(\d{2})/,'$1'+separator+'$2'+separator+'$3');
			break;
			default://----mm-dd-yyyy
			var regex = new RegExp("(((0[13578]|1[02])["+separator+"](0[1-9]|[12][0-9]|3[01])["+separator+"](19|20[0-9]{2}))|((0[469]|1[1])["+separator+"](0[1-9]|[12][0-9]|3[0])["+separator+"](19|20[0-9]{2}))|((02)["+separator+"](0[1-9]|1[0-9]|2[0-9])["+separator+"]((19|20)(04|08|[2468][048]|[13579][26])))|((02)["+separator+"](0[1-9]|1[0-9]|2[0-8])["+separator+"]((19|20)[0-9]{2})))");
			if(typeof(objName)=="object")
			date = date.replace(/(\d{4})(\d{2})(\d{2})/,'$1'+separator+'$2'+separator+'$3');
		}
		if(regex.test(date)){
			flag = true;
			objName.value = date;
		}
		else flag = false;
		return flag;
	}
}
function getDateFormat(date,inFormat,outFormat){ //--------------This function takes date and return date  in desired format
	arrDate = fnArrDate(date,inFormat);			//---------------return array of date 0-yyyy 1-mm 2-dd
	yy = arrDate[0];mm = arrDate[1]; dd = arrDate[2];
	date_result = '';
	if(date != ''){
		opener_top_date_format=''
		if(opener!=null)opener_top_date_format=opener.top.date_format
		outFormat = outFormat || top.date_format || opener_top_date_format;
		separator = get_date_separator(outFormat);
		switch(outFormat){
			case "dd"+separator+"mm"+separator+"yyyy":
			date_result = dd+separator+mm+separator+yy;
			break;
			case "yyyy"+separator+"mm"+separator+"dd":
			date_result = yy+separator+mm+separator+dd;
			break;
			case "yyyy"+separator+"dd"+separator+"mm":
			date_result = yy+separator+dd+separator+mm;
			break;
			default://---mm-dd-yyyy
			date_result = mm+separator+dd+separator+yy;
		}
	}
	return date_result;
}
function validate_zip(inpObj){
	var borwser_name=get_browser();
	if(typeof(top.int_country) != "undefined" && top.int_country == "UK")return true;
	zip_type = (typeof(opener) != 'undefined' && borwser_name!='chrome') ? opener.top.zip_type : top.zip_type;
	switch(zip_type){
		case "numeric":
			regex = new RegExp("^[0-9- ]{0,"+top.zip_length+"}$","gi");
			replaceExp = new RegExp("[^0-9- ]","gi");
		break;
		case "alphanumeric":
			regex = new RegExp("^[0-9a-zA-Z- ]{0,"+top.zip_length+"}$","gi");
			replaceExp = new RegExp("[^0-9a-zA-Z- ]","gi");
		break;
		default:
			regex = new RegExp("^[0-9- ]{0,"+top.zip_length+"}$","gi");
			replaceExp = new RegExp("[^0-9- ]","gi");
	}
	zip = inpObj.value;
	newZip = '';
	if(zip != "")
	newZip = zip.replace(replaceExp,"");
	if(zip!=newZip)
	inpObj.value = newZip 
	if(regex.test(zip))
	return true
	else{
		return false;
	}
}
function validate_ssn(objName,format,length){
	ssn = typeof(objName)=="object" ? objName.value : objName;
	format = format || top.ssn_format;
	length = length || top.ssn_length;
	ssn_reg_exp_js =  (typeof(top.ssn_reg_exp_js)!="undefined" && typeof(top.ssn_reg_exp_js)!=null && top.ssn_reg_exp_js!='') ? top.ssn_reg_exp_js : "[^0-9\-+]";
	regExp = new RegExp(ssn_reg_exp_js,'g');
	refinedSSN = ssn.replace(regExp,"");
	refinedSSN = refinedSSN.replace(/[^0-9A-Za-z+]/g,'');
	refinedSSN = refinedSSN.substr(0,length);
	ssnLength = refinedSSN.replace(/[^0-9A-Za-z+]/g,'').length;
	if(ssnLength >0 && ((format!='' && ssnLength != length) || (format=='' && ssnLength > length))){
		//invalid_input_msg(objName, "Please Enter a valid SSN");
		if(typeof(opener) != "undefined"){
			top.fAlert("Invalid Social Security Number (SSN)");
		}else{
			top.fAlert("Invalid Social Security Number (SSN)");
		}
		objName.value = '';
		return false;
	}else{
		switch(format){
			case "###-###-####":
			objName.value = refinedSSN.replace(/(\d{3})(\d{3})(\d{4})/,'$1-$2-$3');
			break;
			case "###-##-####":
			objName.value = refinedSSN.replace(/(\d{3})(\d{2})(\d{4})/,'$1-$2-$3');
			break;
			default:	
			objName.value = refinedSSN;
		}
	}
	return true;
}
function checkdate(objName, type) {
	type = type || '';
	if (validate_date(objName,type) == false && objName.value!='') {
		objName.value="";
		objName.focus();
		if(typeof(opener) == 'undefined' && typeof(top.opener) == 'undefined'){
			fAlert('Please enter a valid Date');
			top.document.getElementById("divCommonAlertMsg").style.display = "block";
		}else{
			fAlert('Please enter a valid Date');
		}
		return false;
	}
	else {
		return true;
   }
}
/*----------------END FUNCTIONS FOR INTERNATIONALIZATION WORK-----------------*/

function get_browser(){
	browser = '';
	if(navigator.userAgent.indexOf("MSIE") != -1 || !!navigator.userAgent.match(/Trident\/7\./)){
		browser =  "ie";
	}
	else if(typeof(window.mozilla) == "object"){
		browser =  "mozilla";
	}
	else if(typeof(window.chrome) == "object"){
		browser =  "chrome";
	}else if(navigator.userAgent.indexOf("Safari") != -1){
		browser =  "safari";
	}
	return browser;
}

function show_clock(){ 
	//var newDate = new Date().toLocaleString("en-US", {timeZone: top.currentTimeZone});
	var newDate = new Date();
	var C=new Date(newDate);var h=C.getHours();var m=C.getMinutes();var s=C.getSeconds();
	var dn="PM";if(h<12)dn="AM";if(h>12)h=h-12;if(h==0)h=12;if(h<=9)h="0"+h;if(m<=9)m="0"+m;if(s<=9)s="0"+s;var tm=h+":"+m+":"+s+" "+dn;
	dgi("tick2").innerHTML=tm;setTimeout("show_clock()",1000);
}

function set_caret_position(elemId, caretPos)
{
	var elem = document.getElementById(elemId);
	if(elem != null)
	{
		if(elem.createTextRange)
		{
			var range = elem.createTextRange();
			range.move('character', caretPos);
			range.select();
		}
		else
		{
			if(elem.selectionStart)
			{
				elem.focus();
				elem.setSelectionRange(caretPos, caretPos);
			}
			else
				elem.focus();
		}
	}
}

function get_focus_obj(obj)
{
	var obj_id 	= obj.id;
	if(document.getElementById(obj_id))
	{
		var str = document.getElementById(obj_id).value;
        if(str && str.length > 0)
		set_caret_position(obj_id, str.length);
	}
	
}

function alert_notification_show(m)
{
	o1 = $('#div_alert_notifications');
	o2 = $('#div_alert_notifications').find('.notification_span').html(m);
	o1.fadeIn('slow');
	ACT = 2;//Auto Close Time (seconds).
	t = setInterval(function(){if(ACT>0){ACT--;}else{clearInterval(t);o1.fadeOut('slow');o2.html('');}},1000);
	
}

// Common Bootstrap Modal Box 
function show_modal(div_id,header_title,content,footer_cont,cont_height,size,dismissible,cross_btn){
	//Arguments
		//div_id -> id that will be used for modal box. [string]
		//header_title -> title of the modal box. [string]
		//content -> content that will be shown in the modal box. [string]
		//footer_cont -> content that will be shown in the footer of modal box. [string]
		//cont_height -> defines the height of content inside the modal box. [string -> 300]	
		//size -> defines the size of modal box. [modal-lg / modal-sm / empty -> default size]	
		//dismissible -> whether modal box will be closed or not when user click outside of the modal box. [true / false]
		//cross_btn -> if some value send in this variable then top right cross image will not display.
	
	if(header_title == '' || typeof(header_title) != 'string'){
		header_title = 'imwemr';
	}
	
	if(footer_cont == '' || typeof(footer_cont) != 'string'){
		footer_cont = '<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>';
	}
	
	if(cont_height == '' || typeof(cont_height) != 'string'){
		cont_height = '300';
	}
	
	var cross_btn_html='<button type="button" class="close" data-dismiss="modal">×</button>';
	if(typeof(cross_btn) !== 'undefined' && cross_btn!=''){
		cross_btn_html='';
	}
	
	var header_content = '<div class="modal-header bg-primary">'+cross_btn_html+'<h4 class="modal-title">'+header_title+'</h4></div>';
	var body_content   = '<div class="modal-body" style="max-height:'+cont_height+'px;overflow:auto;overflow-y:scroll">'+content+'</div>';	
	var footer_content = '<div id="module_buttons" class="modal-footer ad_modal_footer"><div class="col-sm-12">'+footer_cont+'</div></div>';
	var final_str = header_content+body_content+footer_content;
	var modal = '';
	modal += '<div class="common_modal_wrapper">';
	modal += '<div class="modal-dialog '+size+'"><div class="modal-content">'+final_str+'</div></div>';
	modal += '</div>';
	
	if($('#'+div_id+'').length > 0){
		$('#'+div_id+'').html(modal); 
	}else{
		$('body').append('<div class="modal" id="'+div_id+'" role="dialog"><div>');
		$('#'+div_id+'').html(modal);
	} 
	
	if(dismissible != 'false'){
		$('#'+div_id+'').modal({
			backdrop: 'static',
			keyboard: false,
		});
	}	
	
	$('#'+div_id+'').modal('show');
	return false;
}

function checkIfAlphabet(e, ctrlId)
{
	txtVal ='';
	var txtVal = dgi(ctrlId).value;
	if(txtVal!=''){
		var unicode= e.keyCode? e.keyCode : e.charCode;
		//var selChar = String.fromCharCode(unicode);
		if(!(unicode>=65 && unicode<=90) && !(unicode==8 || unicode==46 || unicode==13 || unicode==9
		 || unicode==16 || unicode==17 || unicode==18 || unicode==37 || unicode==38 || unicode==39 || unicode==40 || unicode==32)){
			alert("Only Character values accepted");
			var rightVal = txtVal.substr(0, parseInt(txtVal.length)-1);
			dgi(ctrlId).value = rightVal;
			return false;
		}
	}
}

//Messages Management  -- Same function exists for php also
function imw_msg_js(key){
	var msgArray = { 
		'no_rec' : 'No record found.',
		'drop_sel': 'Please Select.',
		'sel_rec' : 'Please select a record.',
		'del_rec': 'Are you sure to delete the selected record(s) ?'};
		
	if(key!=""){
		return msgArray[key];
	}else{
		return msgArray;
	}
}

// Multi Level Dropdown [Simple Menu]
function set_val_text(ths,menuId,elemId,liId){
	if(ths === '' || typeof(ths) === 'undefined'){
		if(typeof(liId) != 'undefined'){
			var parent_elem = $('#drop_li_'+liId).parent();
			parent_elem.find('.dropdown-submenu').each(function(id,elem){
				if($(elem).attr('id') != 'drop_li_'+liId){
					$(elem).find('.dropdown-menu').removeClass('show');
				}
			});
		}
	}else{
		if($('.dropdown-menu').hasClass("show")){
			$('.dropdown-menu').removeClass('show');
			$('#'+elemId).parent().find('.open').removeClass('open');
		}
		var elem_val = $(ths).parent().find('input').val();
		if(elem_val != '' || typeof(elem_val) != 'undefined'){
			$('#'+elemId+'').val(elem_val).change();
			$('#'+menuId+'').dropdown('toggle');
		}
	}
}

if(window.jQuery) {
	$(document).ready(function(){
		$('body').on("click",".dropdown-submenu a",function(e){

			if ($(this).next('ul').hasClass("show")) {
				$('.dropdown-submenu').removeClass('show');
			} else {
				$('.dropdown-submenu').removeClass('show');
				$(this).next('ul').toggleClass('show');
			}

			e.stopPropagation();
			e.preventDefault();	
			$('.dropdown').on('hide.bs.dropdown', function () {
				if($('.dropdown-menu .dropdown-submenu').find('ul').hasClass('show')){
					$('.dropdown-menu .dropdown-submenu').find('ul').removeClass('show').addClass('hide');
				}
			});		
		});

		$('body').on('show.bs.dropdown','.dropdown',function(elem){
			var target_dropdown = elem.currentTarget;
			var get_dropDown = $(target_dropdown).find('ul.dropdown-menu').width();
			var obj_offset = $(target_dropdown).offset();
			if($(window).width() < ( obj_offset.left + get_dropDown)){
				var difference = $(window).width() - ( obj_offset.left + get_dropDown + 10);
				$(target_dropdown).find('ul.dropdown-menu').css('left',difference);
			}
		});
	});
}

function reload_simple_menu(id,val){
	if($('#'+id)){
		$('#'+id).find('label').remove();
		var final_val=$('#'+id).html() + val;
		$('#'+id).html(final_val);
	}
}

function html_to_pdf(html_file_loc,op,pdf_name,on_page,one_page_pdf,call_for){
	//html_file_loc => where is the html file located [use write_html() to get html file location and pass it here ]
	//op => orientation of pdf
	//pdf_name => name of the generated pdf
	var on_page_dir=false;
	if(on_page != '' && typeof(on_page) != 'undefined')
	{on_page_dir=on_page;}
	if(html_file_loc != '' && typeof(html_file_loc) != 'undefined' && html_file_loc.length > 0){
		if(op == '' || typeof(op) == 'undefined'){
			op = 'p';
		}
		if(pdf_name == '' || typeof(pdf_name) == 'undefined'){
			pdf_name = 'new_pdf';
		}
		if(call_for == '' || typeof(call_for) == 'undefined'){
			call_for = 'html_to_pdf';
		}
		var wo=top.JS_WEB_ROOT_PATH;
		/* if(window.opener!=null){
			wo=window.opener.top.JS_WEB_ROOT_PATH;
		} */
		if(on_page_dir==true)
		{	
			if(typeof(wo) == 'undefined'){wo=window.opener.top.JS_WEB_ROOT_PATH;}
			window.location.href=wo+'/library/'+call_for+'/createPdf.php?onePage='+one_page_pdf+'&op='+op+'&file_location='+html_file_loc+'&pdf_name='+pdf_name+'';	
		}
		else
		{
		window.open(''+wo+'/library/'+call_for+'/createPdf.php?onePage='+one_page_pdf+'&op='+op+'&file_location='+html_file_loc+'&pdf_name='+pdf_name+'');
		}
	}else{
		alert('Invalid file location provided');
	}
}

function era_file_fun(){
	var enc_id="";
	if(top.fmain.$('#enc_id_read').length>0){
		enc_id = top.fmain.$('#enc_id_read').val();
	}
	var wname = "ERA";
	var features = "width=1200px,height=450px,resizable=1,scrollbars=1";
	top.popup_win("../../interface/accounting/era_file.php?enc_id="+enc_id,features);	
}

function claim_file_fun(clm_status){
	var enc_id="";
	if(top.fmain.$('#enc_id_read').length>0){
		enc_id = top.fmain.$('#enc_id_read').val();
	}
	var url = '../../interface/accounting/claims_file.php?enc_id='+enc_id;
	var wname = "Claims";
	var features = "width=1200px,height=450px,resizable=1,scrollbars=1";
	if(claim_status_request_js=='YES'){
		if(typeof(clm_status)!='undefined' && clm_status != ''){
			var url = '../../interface/accounting/claims_status.php?clm_status=true&enc_id='+enc_id;
			var wname = "ClaimsStatus";
		}
	}
	top.popup_win(url,features);
}

function statement_file_fun(){
	var patid="";
	if(typeof(pat_js)!='undefined' && pat_js != ''){
		patid=pat_js;
	}else{
		if($('#patient_id').length>0){
			patid=$('#patient_id').val();
		}
	}
	var url = '../../interface/reports/accountingStatementsResult.php?Submit=Get Report&pevious=yes&patientId='+patid;
	var wname = "statements";
	var features = "width=900px,height=650px,resizable=1,scrollbars=1";
	if(patid>0){
		top.popup_win(url,features);
	}else{
		top.fAlert('Please select a patient.');
	}
}

/***To prevent pressing enter on any textarea****/
function stop_enter_key_on(idd){
	$('#'+idd).bind('keypress', function(e) {
	  if ((e.keyCode || e.which) == 13) {
		e.preventDefault();
		return false;
	  }
	});
}

/****CHECK IF STRING HAVING A NUMBER IN IT****/
function hasNumbers(t){
    return /\d/.test(t);
}

// -- Opens Pt. search popup
function search_pt(callback_function){
	var parWidth = parseInt($(window).width() - 500);
	var win_height = parseInt($(window).height());
	top.popup_win(top.JS_WEB_ROOT_PATH+'/interface/billing/search_patient_popup.php?callback_function='+callback_function+'',"width="+parWidth+",scrollbars=0,resizable=yes,height="+win_height+",top=0,left=10,addressbar=1");
}

function check_checkboxes() {
	$('#chk_sel_all').click(function(){
		var status = this.checked; // "select all" checked status
		$('.chk_sel').each(function(){ //iterate all listed checkbox items
			this.checked = status; //change ".checkbox" checked status
		});
	});
}

/*
* Function : current_date 
* Purpose - return current Date
* Params -	
* format - Holds Date format in which date will be parsed
*					 IF Not Defined Then Default will be Used from Top 
*/
function current_date(d_format,year_digit,time,t_format)
{ 
	d_format = d_format || top.jquery_date_format || window.top.opener.jquery_date_format;
	d_format = d_format.toLowerCase()
	year_digit = parseInt(year_digit)
	year_digit = (year_digit !== 2 && year_digit !== 4) ? 4 : year_digit;
	
	time = time || false;
	time = (typeof time != 'boolean') ? false : time;
	t_format = t_format || '12'
	t_format = (t_format == '24' || t_format == '12') ? t_format : '12'
	
	var t = new Date();
	var mon = (parseInt(t.getMonth()) + 1).toString();
	
	var d = ('0'+t.getDate()).slice(-2);
	var m = ('0'+mon).slice(-2); 
	var y = t.getFullYear()
	if(year_digit === 2) y = y.toString().substr(2,2);
	
	d_format = d_format.replace(/y/g, y);
	d_format = d_format.replace(/m/g, m);
	d_format = d_format.replace(/d/g, d);
	
	var time_string = '';
	if( time )
	{ 
		var h = parseInt(t.getHours());
		var m = parseInt(t.getMinutes());
		var a = (h < 12) ? 'AM' : 'PM';
		
		m = m < 10 ? '0' + m : m;
		h = (t_format == '12' && h > 12) ? h - 12 : h;
		time_string = ' ' + h + ':' + m + (t_format == 12 ? ' ' + a : '');
	} 	
	return d_format + time_string; 
}

function section_highlight(section_id,arr_all_sections,di_highlight_all){
	$(arr_all_sections).each(function(){
		$('#' + this).css({'background-color':'#FFF'});
	});
	if(typeof(di_highlight_all) == 'undefined' || !di_highlight_all){
		$('#' + section_id).css({'background-color':'#FFFFCC'});//removeClass('tblBg').addClass('bg3');
	}
}

function search_email(e, div_object, left, top)
{
	var object = document.getElementById(div_object);
	var nextObject = object.getElementsByTagName('select').item(0);
	
	if(e.keyCode == 64 && object.style.display == "none"){
		object.style.left = left;
		object.style.top = top;
		object.style.display = "block";
		nextObject.options[0].selected = true;
		nextObject.focus();
	}else if(e.keyCode != 64 && object.style.display == "block"){
		object.style.display = "none";
	}
}

var typed_val = "";
function select_option(e, object, object_id){
	var text = document.getElementById(object_id);
	var value = text.value;
	var splited_value = value.split("@");
	
	if(e.keyCode==13){
		var value_without_website_address = splited_value[0];
		var value_with_new_website_address = value_without_website_address+object.value;
		//text.setAttribute("value", value_with_new_website_address);
		text.value= value_with_new_website_address;
		object.parentNode.style.display = "none";
		text.focus();
	}else if(e.keyCode!=103 && e.keyCode!=104 && e.keyCode!=109 && e.keyCode!=97){
		if(typed_val != ""){
			var value_without_website_address = splited_value[0]+"@"+typed_val;
		}else{
			var value_without_website_address = splited_value[0]+"@";
		}
		//alert("amit");
		var value_with_new_website_address = value_without_website_address;
		//text.setAttribute("value", value_with_new_website_address);
		text.value= value_with_new_website_address;
		typed_val = "";
		object.parentNode.style.display = "none";
		text.focus();
	}else{
		typed_val = typed_val + String.fromCharCode(e.keyCode);
	}
}

function select_option_with_mouse(object, object_id){
	var text = document.getElementById(object_id);
	var value = text.value;
	var splited_value = value.split("@");	
	var value_without_website_address = splited_value[0];	
	var value_with_new_website_address = value_without_website_address+object.value;
	//text.setAttribute("value", value_with_new_website_address);
	text.value= value_with_new_website_address;
	object.parentNode.style.display = "none";
}

//short form of big function.
function gebi(el){return document.getElementById(el);}

function zip_vs_state_R6(objZip,objCity,objState,objCountry,objCounty)
{
	zip_code = $(objZip).val();
	if(zip_code == ''){
		$(objCity).val('');
		$(objState).val('');
		return false;
	}
	$.ajax({
			url:top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/demographics/add_city_state.php?zipcode="+zip_code,
			success:function(data)
			{
				var changecolor='#F6C67A';	
				var val=data.split("-");
				var city = $.trim(val[0]);
				var state = $.trim(val[1]);
				var country = $.trim(val[3]);
				var county = $.trim(val[4]); 
				if(city!="")
				{
					$(objCity).val(city);
					$(objState).val(state);
					if(country){$(objCountry).val(country);}
					if(county){$(objCounty).val(county);}
					return;
					
					if(typeof(top.int_country) !="undefined" && top.int_country == "UK"){
						if(typeof(document.getElementById("zipCodeStatus"))!="undefined" && document.getElementById("zipCodeStatus") != null && document.getElementById("zipCodeStatus") != "undefined")
						document.getElementById("zipCodeStatus").value="OK";
						return;
					}
					if(typeof(top.stop_zipcode_validation) !="undefined" && top.stop_zipcode_validation == "YES"){
						//DO NOT VALIDATE ZIPCODE
						return;
					}
					
					$(objZip).val("");
					changeClass(document.getElementById($(objZip).attr("id")),1)
					if(typeof(top.fAlert) != "undefined")
						top.fAlert('Please enter correct '+top.zipLabel);
					else{
						alert('Please enter correct '+top.zipLabel);
					}
					$(objZip).select();
					$(objCity).val("");
					$(objState).val("");
					$(objCounty).val("");
				}
		  }
	});
}

function set_header_title(title){
	top.$('#acc_page_name').html(title); 
}

function showHidemodal(action, div_id, dismissible, backdrop){
	
	action = (action == 'show' || 'hide') ? action : 'show';
	backdrop = (typeof backdrop === 'boolean') ? backdrop : false;
	dismissible = (typeof dismissible === 'boolean') ? dismissible : false;
	
	if( action == 'hide') {
		$('#'+div_id+'').modal(action);		
	}
	else {
		backdrop = ( dismissible) ? 'static' : backdrop;
		$('#'+div_id+'').modal({backdrop: backdrop,keyboard:false});
		if( !$('#'+div_id+'').hasClass('in') ){
			$('#'+div_id+'').modal('show');
		}
	}
	
	return false;
}
function ado_scan_fun(show,formNme,editId){
	if( typeof show == 'undefined')	show = 'scan';
	if( typeof formNme == 'undefined')	return;
	if( typeof editId == 'undefined' || editId == '')	editId = 0;
	
	var url = top.JS_WEB_ROOT_PATH + '/library/classes/scan_ptinfo_medhx_images.php?formName='+formNme+'&show='+show;
	url += (editId > 0) ? '&edit_id='+editId : '';
	top.popup_win(url,'scan_patient_info');
}

/* Creating buttons for modals */
function set_modal_btns(parent_id,btn_arr){
	var parent_elem = '';
	var btn_str = '';
	var footer_str = '';
	var default_type = 'button';
	var btn_debug_arr = new Array;
	
	//Getting Parent element
	if($('#'+parent_id).length > 0){
		parent_elem = $('#'+parent_id);
	}else if($('.'+parent_id).length > 0){
		parent_elem = $('.'+parent_id);
	}
    
    var onclick='';
    if(parent_id=='new_priv_div .modal-footer') {
        onclick='onclick="resetToOldPriv();"';
    }
	
	if(parent_elem.length){
		parent_elem.addClass('btn-ftr-unique');
		parent_elem.attr('id','module_buttons');
		if(btn_arr.length > 0){ //If something is there in array
			$.each(btn_arr,function(id,val){
				var btn_name_str = '';
				var btn_val = btn_arr[id][0].charAt(0).toUpperCase() + btn_arr[id][0].slice(1);
				var btn_class = get_btn_class(btn_val);
				var btn_name = btn_arr[id][1];
				var btn_func = btn_arr[id][2];
				var btn_type = default_type;
				if(btn_name.length){
					btn_type = 'submit';
					btn_name_str = 'name="'+btn_name+'" value="'+btn_name+'"';
				}
				btn_str += '<button type="'+btn_type+'" class="'+btn_class+'" '+btn_name_str+' id="'+btn_val+'" onclick="'+btn_func+'">'+btn_val+'</button>';
			});
			btn_str += '<button type="button" class="btn btn-danger" data-dismiss="modal" '+onclick+'>Close</button>';
			footer_str = '<div class="row mdl_btns_dp"><div class="col-sm-12 text-center">'+btn_str+'</div></div>';
		}else{ // else send only close button
			btn_str += '<button type="button" class="btn btn-danger" data-dismiss="modal" '+onclick+'>Close</button>';
			footer_str = '<div class="row mdl_btns_dp"><div class="col-sm-12 text-center">'+btn_str+'</div></div>';
		}
		
		//Appending buttons
		if($('body').find('.btn-ftr-unique .mdl_btns_dp').length == 0){
			//Removing already existing buttons or inputs in the footer
			$('body').find('.btn-ftr-unique').find('[type=button],[type=submit]').not('input[type=hidden]').remove();
			$('body').find('.btn-ftr-unique').append(footer_str);
		}else{
			$('body').find('.btn-ftr-unique .mdl_btns_dp').html(footer_str);
		}
		
		if($('body').find('.btn-ftr-unique').hasClass('ad_modal_footer') === false){
			$('body').find('.btn-ftr-unique').addClass('ad_modal_footer');
		}
		
		
		if(parent_elem.hasClass('btn-ftr-unique')){
			parent_elem.removeClass('btn-ftr-unique');
		}
	}
}

function get_btn_class(btn_val){
	var btn_class = 'btn btn-default';
	switch(btn_val.toLowerCase()){
		case 'save':
		case 'done':
		case 'ok':
		case 'yes':
			btn_class = 'btn btn-success';				
			break;
		case 'cancel':
		case 'no':
		case 'Close':
			btn_class = 'btn btn-danger';
			break;
		case 'delete selected':
			btn_class = 'btn btn-danger';
			break;
		case 'print':
		case 'download':
		case 'add':
		case 'upload':
			btn_class = 'btn btn-info';
			break;
	}
	return btn_class;
}

//Sets modal dynamic max height
function set_modal_height(object,debug){
	var obj = '';
	var debug_arr = new Array;
	if(typeof(object) == 'object'){
		obj = $(object);
	}else{
		if($('#'+object).length > 0){
			obj = $('#'+object);
		}else if($('.'+object).length > 0){
			obj = $('.'+object);
		}
	}
	
	debug_arr['object'] = obj;
	if(obj != ''){
		if(obj.hasClass('in')){
			var elem = obj;
			var elem_height = elem.outerHeight();
			
			debug_arr['modal_height'] = elem_height;
			
			var header_position = $('#first_toolbar',top.document).position();
			var window_height = parseInt(window.innerHeight - $('footer',top.document).outerHeight() - header_position.top - $('#first_toolbar').outerHeight());
			
			debug_arr['window_height'] = window_height;
			
			if(elem_height > window_height){
				var diff = parseInt(elem_height - window_height);
				var new_height = parseInt(elem_height - diff);
				//Subtracting modal header and footer 
				var modal_extra = parseInt(elem.find('.modal-header').outerHeight() + elem.find('.modal-footer').outerHeight());
				new_height = parseInt(new_height - modal_extra)
				debug_arr['modal_extra_height'] = new_height;
				elem.find('.modal-body').css('max-height',new_height);
				if (elem.find('.modal-body').css("overflow-x") != 'hidden'){
					elem.find('.modal-body').css({
						overflow: 'auto',
						'overflow-x': 'hidden'
					});
				}
			}
		}
	}
	if(debug){
		console.log(debug_arr);
	}
	
}

function checkSingle(elemId,grpName){
	var obgrp = document.getElementsByName(grpName);
	var objele = document.getElementById(elemId);
	var len = obgrp.length;		
	if(objele.checked == true){		
		for(var i=0;i<len;i++){
			if((obgrp[i].id != objele.id) && (obgrp[i].checked == true) ){
				obgrp[i].checked=false;
			}
		}	
	}
}

function changeChbxColor(grpNode){
	var objGrp = $('input[name="'+grpNode+'"]');
	var len = objGrp.length;
	var is_checked = false;
	for(var i=0;i<len;i++){
		if(objGrp[i].checked == true){
			is_checked = true;
		}
	}
	if( is_checked )	
		objGrp.parent('div').removeClass('checkbox-mandatory');
	else 
		objGrp.parent('div').addClass('checkbox-mandatory');	
}

function day_charges(){
	var sc_wd=(screen.availWidth)-100;
	var sc_hg=(screen.availHeight)-120;
	var features = 'width='+sc_wd+',height='+sc_hg+',left=150,top=80,location=0,status=1,resizable=1,left=0,top=0,scrollbars=0';
	top.popup_win("../../interface/accounting/day_charges_list.php",features);
}

function popup_resize(maxWidth, maxHeight, maxAvail)
{
	maxWidth 	= maxWidth || 1270;
	maxHeight = maxHeight || 850;
	maxAvail 	= maxAvail || 0.9;
	
	var avail_w_90 = (screen.availWidth * maxAvail )
	var avail_h_90 = (screen.availHeight * maxAvail )
	var parWidth = (screen.availWidth > maxWidth) ? maxWidth :  avail_w_90;
	var parHeight = (screen.availHeight > maxHeight) ? maxHeight :  avail_h_90;
		
	window.resizeTo(parWidth,parHeight);
	
	var t = parseInt((screen.availHeight - window.outerHeight) / 2)
	var l = parseInt((screen.availWidth - window.outerWidth) / 2)
	window.moveTo(l,t);
}

function show_iportal_changes_alert_data(filter,pt_id){
	var form_data = 'iportal_filter='+filter+'&iportal_pt='+pt_id;
	var modal_title = '';
	var modal_id = '';
	
	switch(filter){
		case 'demographics':
		case 'medical':
			modal_title = 'Patient Portal - Demographics Changes';
			modal_id = 'iportal_changes_demograph';
		break;
		
		case 'cl_order':
			modal_title = 'Patient Portal - Contact Lens Order';
			modal_id = 'iportal_changes_cl_order';
		break;
		
		case 'registered_patients':
			modal_title = 'Patient Portal - Patient Registration';
			modal_id = 'iportal_changes_reg_pt';
		break;
        
		case 'iportal_payments':
			modal_title = 'Patient Portal - Payment(s)';
			modal_id = 'iportal_patient_payments';
		break;
	}
	
	
	$.ajax({
		url:top.JS_WEB_ROOT_PATH + '/interface/physician_console/handle_pt_changes.php',
		data:form_data,
		type:'POST',
		dataType:'JSON',
		beforeSend:function(){
//			top.show_loading_image('show');
		},
		success:function(response){
			if(response){
			if(response.data_length > 0 && response.data.length){
				var modal_html = generate_iportal_alert_html(response,filter);
				if(modal_html){
					var div_id = modal_id;
					var title = modal_title;
					var footer = '<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button> <button type="button" class="btn btn-success" onclick="window.top.fmain.location.reload();">Reload with approved changes</button> <button type="button" class="btn btn-success" onclick="top.fmain.approve_all_operation(0,\''+modal_id+'\', \''+filter+'\');">Approve All</button>';
					if(filter=='iportal_payments'){
                        footer = '<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button> <button type="button" class="btn btn-success" onclick="top.fmain.iportal_payment_message_read(0,\''+modal_id+'\', \''+filter+'\');">Ok</button>';
                    }
					
					if(!top.$('#iportal_modal_val_'+modal_id).length){
						show_modal(div_id,title,modal_html,footer,'400','modal_90');
					}
					
					$('body').on('hide.bs.modal','#'+modal_id,function(event){
						var iportal_input = $('<input>');
						iportal_input.attr({
							'id' : 'iportal_modal_val_'+modal_id,
							'value':'shown',
							'type':'hidden',
						});
						top.$('body').append(iportal_input);
					});
				}
				else{
					if($("#"+modal_id).length > 0 ) $("#"+modal_id).html('').modal('hide');
				}
			}
			}
		},
		complete:function(){
			top.show_loading_image('hide');
		}
	});
}

function generate_iportal_alert_html(result,filter)
{
	var $_html = '';
	if(	result.data_length ==  0 ) return false;
	var table_header = '';
	var table_rows = '';
	if(filter == 'medical' || filter == 'demographics'){
		table_header += '<th class="col-xs-1 text-nowrap">Field Changed</th>';
		table_header += '<th class="col-xs-4">Old Value</th>';
		table_header += '<th class="col-xs-4">New Value</th>';
		table_header += '<th class="col-xs-3">Action</th>';	
		
		$.each(result.data,function(id,val){
			table_rows += '<tr>';
				table_rows += '<td>'+val['col_lbl']+'</td>';
				table_rows += '<td>'+val['old_val']+'</td>';
				table_rows += '<td>'+val['new_val']+'</td>';
				table_rows += '<td id="iportal_approve_'+val['id']+'" class="text-center">';
					table_rows += '<div class="btn-group">';
					table_rows += '<button class="btn btn-success" data-filter="'+filter+'" onClick="top.fmain.approve_operation(\''+val['id']+'\', this);">Approve</button> ';
					table_rows += '<button class="btn btn-danger"data-filter="'+filter+'" onClick="top.fmain.disapprove_operation(\''+val['id']+'\', this);">Decline</button>';
					table_rows += '</div>';
				table_rows += '</td>';
			table_rows += '</tr>';
		});
	}
	
	if(filter == 'iportal_payments'){
		table_header += '<th class="col-xs-4 text-nowrap">Field Changed</th>';
		table_header += '<th class="col-xs-8">New Value</th>';
		
		$.each(result.data,function(id,val){
			table_rows += '<tr>';
				table_rows += '<td>'+val['col_lbl']+'</td>';
				table_rows += '<td>'+val['new_val']+'</td>';
			table_rows += '</tr>';
		});
	}
	
	if(filter == 'cl_order'){
		table_header += '<th>Patient</th>';
		table_header += '<th class="text-nowrap">Date</th>';
		table_header += '<th>Eye</th>';
		table_header += '<th>Brand</th>';
		table_header += '<th>Manufacturer</th>';
		table_header += '<th>Disposable</th>';
		table_header += '<th>Supplies</th>';
		table_header += '<th>#Boxes</th>';
		table_header += '<th>Action</th>';
		
		var eye_str = '';
		var old_order_num = 0;
		$.each(result.data,function(id,val){	
			var td_date = '';
			var td_patient = '';
			var td_approval = '';
			var ordNum = val['temp_order_num'];
			if(old_order_num != ordNum){ cnt=0;}
			
			if(cnt==0){
				rowspan = val['order_count'];
				td_date = '<td rowspan="'+rowspan+'">'+val['orderedDate']+'</td>';
				td_patient = '<td rowspan="'+rowspan+'">'+val['pt_name']+'</td>';
				td_approval += '<td id="iportal_approve_'+val['id']+'" class="text-center" rowspan="'+rowspan+'">';
					td_approval += '<div class="btn-group">';
					td_approval += '<button class="btn btn-success" data-filter="'+filter+'" onClick="top.fmain.approve_operation(\''+ordNum+'\', this);">Approve</button> ';
					td_approval += '<button class="btn btn-danger" data-filter="'+filter+'" onClick="top.fmain.disapprove_operation(\''+ordNum+'\', this);">Decline</button>';
					td_approval += '</div>';
				td_approval += '</td>';
			}
			
			table_rows += '<tr>';
				table_rows += td_patient;	
				table_rows += td_date;	
				table_rows += '<td>'+val['eye']+'</td>';	
				table_rows += '<td>'+val['brand']+'</td>';	
				table_rows += '<td>'+val['manufacturer']+'</td>';	
				table_rows += '<td>'+val['disposable']+'</td>';	
				table_rows += '<td>'+val['supplies']+'</td>';	
				table_rows += '<td>'+val['boxes']+'</td>';	
				table_rows += td_approval;
			table_rows += '</tr>';
			
			old_order_num = ordNum;		
			cnt++;
		});
	}
	
	if(filter == 'registered_patients'){
		table_header += '<th>Patient Data</th>';
		table_header += '<th>Email Verification</th>';
		table_header += '<th>Action</th>';
		$.each(result.data,function(id,val){
			var pt_details = '';
			
			pt_details += '<div class="row">';
				pt_details += '<div class="col-sm-4">';
					pt_details += '<div class="form-group">';
						pt_details += '<strong>First Name :</strong>    '+val['fname'];	
					pt_details += '</div>';
				pt_details += '</div>';
				pt_details += '<div class="col-sm-4">';
					pt_details += '<div class="form-group">';
						pt_details += '<strong>Last Name :</strong>    '+val['lname'];	
					pt_details += '</div>';
				pt_details += '</div>';
				
				pt_details += '<div class="clearfix"></div>';
				
				pt_details += '<div class="col-sm-4">';
					pt_details += '<div class="form-group">';
						pt_details += '<strong>Sex :</strong>    '+val['sex'];
					pt_details += '</div>';
				pt_details += '</div>';
				pt_details += '<div class="col-sm-4">';
					pt_details += '<div class="form-group">';
						pt_details += '<strong>DOB :</strong>    '+val['dob'];
					pt_details += '</div>';
				pt_details += '</div>';
				pt_details += '<div class="col-sm-4">';
					pt_details += '<div class="form-group">';
						pt_details += '<strong>Email :</strong>    '+val['email'];
					pt_details += '</div>';
				pt_details += '</div>';
				
				pt_details += '<div class="clearfix"></div>';
				
				pt_details += '<div class="col-sm-4">';
					pt_details += '<div class="form-group">';
						pt_details += '<strong>Home Phone :</strong>    '+val['phone_home'];
					pt_details += '</div>';
				pt_details += '</div>';
				pt_details += '<div class="col-sm-4">';
					pt_details += '<div class="form-group">';
						pt_details += '<strong>Cell Phone :</strong>    '+val['phone_cell'];
					pt_details += '</div>';
				pt_details += '</div>';
				pt_details += '<div class="col-sm-4">';
					pt_details += '<div class="form-group">';
						pt_details += '<strong>Work Phone :</strong>    '+val['phone_biz_ext']+' '+val['phone_biz'];
					pt_details += '</div>';
				pt_details += '</div>';
				
				pt_details += '<div class="clearfix"></div>';
				
				pt_details += '<div class="col-sm-4">';
					pt_details += '<div class="form-group">';
						pt_details += '<div class="row">';
							pt_details += '<div class="col-sm-4">';
								pt_details += '<strong>Address :</strong>';
							pt_details += '</div>';
							pt_details += '<div class="col-sm-8">';
								pt_details += val['address'];
							pt_details += '</div>';
						pt_details += '</div>';
					pt_details += '</div>';
				pt_details += '</div>';
				
				pt_details += '<div class="col-sm-4">';
					pt_details += '<div class="form-group">';
						pt_details += '<strong>City :</strong>    '+val['city'];
					pt_details += '</div>';
				pt_details += '</div>';
				pt_details += '<div class="col-sm-4">';
					pt_details += '<div class="form-group">';
						pt_details += '<strong>'+top.zipLabel+' :</strong>    '+val['postal_code'];
					pt_details += '</div>';
				pt_details += '</div>';
			pt_details += '</div>';
			
			table_rows += '<tr>';
				table_rows += '<td>'+pt_details+'</td>';
				table_rows += '<td>'+val['auth_status']+'</td>';
				table_rows += '<td id="iportal_approve_'+val['id']+'" class="text-center">';
					table_rows += '<div class="btn-group">';
					table_rows += '<button class="btn btn-success" data-filter="'+filter+'" onClick="top.fmain.approve_operation(\''+val['id']+'\', this);">Approve</button> ';
					table_rows += '<button class="btn btn-danger" data-filter="'+filter+'"  onClick="top.fmain.disapprove_operation(\''+val['id']+'\', this);">Decline</button>';
					table_rows += '</div>';
				table_rows += '</td>';
			table_rows += '</tr>';
		});
	}
	
	
	$_html += '<div class="row">';
    var h1_title=' Following Patients has registered from Patient Portal ';
    var h2_title=' Please take the appropriate action.';
    if(filter == 'iportal_payments'){
        h1_title=' Following Payment(s) made from Patient Portal ';
        h2_title=' This is alert message for patient payment(s). ';
    }
    $_html += '<h1 style="font-size:18px;font-weight:normal;margin:0px;padding:0px;">'+h1_title+'</h1>';
    $_html += '<h2 style="font-size:14px;font-weight:normal;margin:5px 0px 10px 0px;padding:0px;">'+h2_title+'</h2>';
	$_html += '<div class="col-xs-12">';
	$_html += '<table class="table table-bordered table-hover table-striped">';
	$_html += '<thead class="header">';
	$_html += '<tr class="grythead">';
	$_html += table_header;
	$_html += '</tr>';
	$_html += '</thead>';
	$_html += '<tbody id="newTbodyBorder">';
	$_html	+=	table_rows;
	$_html	+=	'</tbody>';
	$_html	+=	'</table>';
	$_html	+=	'<input type="hidden" name="hidd_iportal_approve" id="hidd_iportal_approve" value="'+result.row_id_str+'">';
	$_html	+=	'</div>';
	$_html	+=	'</div>';
	 
	return $_html;
}

function approve_operation(row_id,ths)
{
	var processing_btn = '<button class="btn btn-warning"><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Processing...</button>';
	var approved_btn = '<button class="btn btn-success"><span class="glyphicon glyphicon-ok"></span> Successfully Approved</button>';
	
	var filter = $(ths).data('filter');
	var url = '/interface/physician_console/handle_pt_changes.php';
	if(filter == 'cl_order'){
		url = '/iportal_config/approve_cl_orders.php';
	}
	
	if(filter == 'registered_patients'){
		url = '/iportal_config/handle_pt_registration.php';
	}
	
	ths_parent = $(ths).parent();	
	if(row_id != "" && parseInt(row_id))
	{
		ths_parent.html(processing_btn);	
	}
	$.ajax({
		url:top.JS_WEB_ROOT_PATH + url,
		data:'sel_op=approve&row_id='+row_id+'&alert_filter='+filter,
		type:'POST',
		success:function(response){
			if(filter == 'registered_patients'){
				var resp = JSON.parse(response);
				if(resp.status=="success"){
					ths_parent.html(approved_btn).append('<div style="color:#090;font-weight:bold;">Approved<br />Patient Id: '+resp.pt_id+'</div>');		
				}
				else if(resp.status=="error"){
					ths_parent.html(processing_btn).append('<div style="color:#CC0000;font-weight:bold;">Error while approving</div>');			
				}
			}
		},
		complete:function(respData){
			if(filter == 'registered_patients'){
				
			}else{
				ths_parent.html(approved_btn);
			}
		}
	});
	
	return false;
}

function disapprove_operation(row_id,ths)
{
	var processing_btn = '<button class="btn btn-warning"><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Processing...</button>';
	var declined_btn = '<button class="btn btn-success"><span class="glyphicon glyphicon-ok"></span> Successfully Declined</button>';
	
	var filter = $(ths).data('filter');
	var url = '/interface/physician_console/handle_pt_changes.php';
	if(filter == 'cl_order'){
		url = '/iportal_config/approve_cl_orders.php';
	}
	
	if(filter == 'registered_patients'){
		url = '/iportal_config/handle_pt_registration.php';
	}
	
	ths_parent = $(ths).parent();
	if(row_id != "" && parseInt(row_id))
	{
		ths_parent.html(processing_btn);	
	}
	$.ajax({
		url:top.JS_WEB_ROOT_PATH + url,
		data:'sel_op=decline&row_id='+row_id,
		type:'POST',
		complete:function(respData)
		{
			resultData = respData.responseText;
			ths_parent.html(declined_btn);				
		}
	});		
}

function approve_all_operation(indx,modal_id,filter) 
{
	var url = '/interface/physician_console/handle_pt_changes.php';
	if(filter == 'cl_order'){
		url = '/iportal_config/approve_cl_orders.php';
	}
	
	if(filter == 'registered_patients'){
		url = '/iportal_config/handle_pt_registration.php';
	}
	
	var processing_btn = '<button class="btn btn-warning"><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span> Processing...</button>';
	var approved_btn = '<button class="btn btn-success"><span class="glyphicon glyphicon-ok"></span> Successfully Approved</button>';
	
	all_id = $('#'+modal_id+' #hidd_iportal_approve').val();
	var all_id_arr = new Array();
	if(all_id) 
	{
		all_id_arr = all_id.split(',');
		row_id = all_id_arr[indx];
		if(all_id_arr.length>indx)
		{
			if(row_id != "" && parseInt(row_id))
			{
				if($('#iportal_approve_'+row_id).text() !="Declined")
					$('#iportal_approve_'+row_id).html(processing_btn);
			}
			
			$.ajax({
				url:top.JS_WEB_ROOT_PATH + url,
				data:'sel_op=approve&row_id='+row_id+'&alert_filter='+filter,
				type:'POST',
				complete:function(r)
				{
					if(r.responseText == "approved")
					$('#iportal_approve_'+row_id).html(approved_btn);		
					approve_all_operation(parseInt(indx)+1,modal_id,filter);
				}
			});	
		}
		if(all_id_arr.length == indx) top.fmain.location.reload(true);
	}else{
		if($("#"+modal_id).length > 0 ) $("#"+modal_id).html('').modal('hide');
	}
	return false;
}

function iportal_payment_message_read(indx,modal_id,filter){
    var url = '/interface/physician_console/handle_pt_changes.php';
    var all_id = $('#'+modal_id+' #hidd_iportal_approve').val();
	var all_id_arr = new Array();
	if(all_id) 
	{
		all_id_arr = all_id.split(',');
		var row_id = all_id_arr[indx];
		if(all_id_arr.length>indx)
		{
			$.ajax({
				url:top.JS_WEB_ROOT_PATH + url,
				data:'sel_op=iportal_payment_read&row_id='+row_id+'&alert_filter='+filter,
				type:'POST',
				complete:function(r)
				{ iportal_payment_message_read(parseInt(indx)+1,modal_id,filter); }
			});	
		}
		if(all_id_arr.length == indx) top.fmain.location.reload(true);
	}else{
		if($("#"+modal_id).length > 0 ) $("#"+modal_id).html('').modal('hide');
	}
	return false;
}

function view_only_acc_call(mode){
	top.fAlert("You do not have permission to perform this action.",'',340);
	if(mode == 1){
		return false;
	}
}

function changeColumnSelection(e) {
	var fid = e.currentTarget.id
	var obj = $('#'+fid);
	var values = obj.val();
	var page = obj.data('page');
	
	$.ajax({
		url:top.JS_WEB_ROOT_PATH + '/interface/common/column_settings.php',
		type:'post',
		dataType:'json',
		data:{page:page,cols:(values?values.join(','):'')},
		beforeSend:function(){
			top.show_loading_image('show');
		},
		complete:function(){
			top.show_loading_image('hide');
		},
		success:function(r){
			if( r.success ) {
					obj.find('option').each(function(){
					var v = $(this).attr('value'); 
					var t = v.replace(/-/g, "");
					if( $.inArray(v,values) === -1 ){
						$("td."+t).addClass('hide');}
					else 
						$("td."+t).removeClass('hide');
				});	
						
			}
			else
				top.fAlert('Error: please try again');
		}
		
	})
	
	
}

// Functions for Pt Alerts
function loadItsDesc(selObj,textObj){
	var val = selObj.value; 
	var arrVal = val.split('-'); 	
	textObj.style.visibility = "visible";						
	if(arrVal[2]){
		textObj.value = arrVal[2];								
	}
	else{								
		textObj.value = "";
	}

}
function loadItsDescImmu(selObj,textObj){
	var val = selObj.value; 
	var arrVal = val.split('-'); 	
	textObj.style.visibility = "visible";													
	if(arrVal[2]){
		textObj.value = arrVal[2];								
	}
	else if(arrVal[0] == ""){
		textObj.value = "";
		textObj.style.visibility = "hidden";
	}
	else{								
		textObj.value = "";
	}

}
function drag_div_move(ele, ev) {
	 $(ele).draggable();
}
function acknowledged(op,form){
	op = op || 0; 
	if(document.getElementById('patSpesificDivAlert')){
		if(op ==1) {
			$(form).children('#disablePatAlertThisSession').val('yes');
		}
		else {
			$(form).children('#disablePatAlertThisSession').val('');
		}
		$(form).children('#patSpesificDivAlert').css('display','none');
	}
}
// End Functions for Pt Alerts

/*********FUNCTION BELOW TO MANAGE AN CONTAINER HEIGHT ACCORDING TO OTHER OR REMAINING AFTER OTHERS.***/
function manage_section_height(dest_obj,source_obj_array,flexibility){
	//---OBJ = .class_name or #id_of_obj.
	if(typeof(flexibility)=='undefined') flexibility = 0;
	h = 0;
	//console.log(source_obj_array);
	$(source_obj_array).each(function(index, element) {
		//console.log($(source_obj_array[index]));
        h += $(source_obj_array[index]).height();
    });
	if(flexibility < 0) h = h - parseInt(flexibility);
	else h = h + parseInt(flexibility);
	wh = window.innerHeight;
	//alert(h+' :: '+wh+' :: '+flexibility);
	$(dest_obj).height(wh-h);
}

//Open new patient window
function newPatient_info_main(){
	var parWidth = parseInt($(window).width() - 500);
	var win_height = $(window).height();
	top.popup_win(top.JS_WEB_ROOT_PATH+"/interface/scheduler/common/new_patient_info_popup_new.php?source=demographics&search=true&frm_status=show_check_in&popheight="+win_height, "width="+parWidth+",scrollbars=0,resizable=yes,height="+win_height+",top=0,left=10,addressbar=1")
	//window.open(top.JS_WEB_ROOT_PATH+"/interface/scheduler/common/new_patient_info_popup_new.php?source=demographics&search=true&frm_status=show_check_in&popheight="+win_height, "newPatientWindow", "width="+parWidth+",scrollbars=0,resizable=yes,height="+win_height+",top=0,left=10,addressbar=1");
}


function check_rule_manager(obj_value, section) {
    section=(section)?section:'';
    var post_data = {obj_value: obj_value,section:section}
    $.ajax({
		url:top.JS_WEB_ROOT_PATH + '/interface/common/assign_new_task.php',
		type:'post',
		dataType:'json',
		data:post_data,
		beforeSend:function(){
			top.show_loading_image('show');
		},
		complete:function(){
			top.show_loading_image('hide');
		},
		success:function(r){
            
		}
		
	});
	
}
var o = '';
function del_doc(__this,confirm){
	o = __this || o;
	if( typeof confirm == 'undefined' ) {
		top.fmain.frames[0].document.write('');
		top.fancyConfirm("Sure! you want to delete this document ?","top.del_doc('',true)");
	}
	else { 
		var u = $(o).data('url');
		if($(o).length > 0 && typeof u !== 'undefined' ) {
			if( top.fmain ) top.fmain.location.href=u;
			if( top.all_data ) top.all_data.location.href=u;
		}
		else	
			top.fAlert('Unable to delete. Please refresh the tab and try again. ')
	}

}
 
function dismissTaskAlert () {
	top.master_ajax_tunnel(top.JS_WEB_ROOT_PATH+'/interface/core/ajax_handler.php?task=task_alert_shown');
	$("#task_alerts",top.document).modal('hide');
}

/*---for zeiss integration***/
function ZeissViewer(pid, user, pass, path){
	if(pid=="")pid = window.top.$("#patient_id").val();
    u=user;
	p=pass;
	params = " -username "+u+" -password "+p+" -patientId "+pid;
	exeURL= '"'+path+'\\launchFORUM.cmd"'+params;
	try{
		WSH=new ActiveXObject("WScript.Shell");
		WSH.run(exeURL);
	}catch(e){
		if(e!=''){
			try{
				exeURL = exeURL.replace('Program Files','Program Files (x86)');
				WSH=new ActiveXObject("WScript.Shell");
				WSH.run(exeURL);
			}catch(er){
				top.fAlert('<div class="text-center">Check if Zeiss Application installed.<br>'+er.message+'</div>');
			}
		}
	}
}

function sig_web_alert(){
	var msg = 'Error, make sure you have installed SigWeb.<br> <a href="https://www.topazsystems.com/software/sigweb.exe" target="_BLANK" class="purple-text">Click here</a> to download SigWeb service.<br>';
	msg += '<a href="https://www.topazsystems.com/Software/sigweb_install.pdf" target="_BLANK" class="purple-text">Click here</a> to read installation instructions before installing';
	top.fAlert(msg);
}

var heardAboutSearch = ['Family','Friends','Doctor','Previous Patient.','Previous Patient'];
function searchHeardAbout(){
	var WRP = top.JS_WEB_ROOT_PATH
	var search_val = $("#heardAbtSearch").val();
	search_val = $.trim(search_val);
	target_search_val = '';
	if(search_val!="")
	{
		search_val_arr = search_val.split(',');
		if(search_val_arr[0]!="" && typeof search_val_arr[0] != "undefined")
		{
			last_name_val = $.trim(search_val_arr[0]); 
			last_mid_name_arr = last_name_val.split(' ');			
			target_search_val = $.trim(last_mid_name_arr[1]); 	
			if(target_search_val == "" || typeof target_search_val == "undefined")
			{
				target_search_val = last_mid_name_arr[0];			
			}
		}
		else
		{
			target_search_val = $.trim(search_val_arr[0]); 	
		}
	}
	
	if( !target_search_val ) return false;
	
	var heardAbtVal = $("#elem_heardAbtUs").val();
	var tmpArr = heardAbtVal.split("-");
	
	heardAbtVal = tmpArr[1].trim();
	
	if( !heardAbtVal ) return false;
	
	if( $.inArray(heardAbtVal, heardAboutSearch) !== -1) {
		
		if( WRP == 'undefined') {
			if( typeof window.opener !== 'undefined' ){
				WRP = window.opener.top.JS_WEB_ROOT_PATH;
			}
		}
		if( heardAbtVal == 'Doctor' ) {
			window.open(WRP + "/interface/admin/users/searchPhysician.php?btn_sub=search&sel_by=LastName&txt_for="+target_search_val,"window1","width=800,height=500,scrollbars=yes, status=1");	
		}
		else { 
			var w = window.open(WRP + "/interface/scheduler/search_patient_popup.php?sel_by=Active&txt_for="+target_search_val+"&btn_sub=Search&call_from=demographics","PatientWindow","width=800,height=500,top=420,left=150,scrollbars=yes");
		}
	}
	
}

function setHeardAboutUsVal(pid,name){
	document.getElementById('heardAbtSearchId').value = pid;
	document.getElementById('heardAbtSearch').value = name + '-' + pid;
}

function get_phy_name_from_search(strVal,id){
	if(top.$('#appt_scheduler_status').val()=='loaded') {
		document.getElementById('front_primary_care_id').value = id;
		document.getElementById('front_primary_care_name').value = strVal;
	}
	else{
		document.getElementById('heardAbtSearchId').value = id;
		document.getElementById('heardAbtSearch').value = strVal;
	}
	
}
//number only
function validate_num(obj){
	var num = $(obj).val();
	num = num.trim();
	if(num!=""){
		num = num.replace(/[^\d.]/, '');	/*Strip non int values*/
		if(num=="")num=0;
	}
	$(obj).val(num);
}

function capitalize_letter(str) {
	return str.replace(/(^\S|\s\S)/g, function(match, g1, offset, origstr) {
  	return g1.toUpperCase(); 
  });
}

function view_only_call(){
	top.fAlert("You do not have permission to perform this action.");
	return false;
}