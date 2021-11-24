// common.js JavaScript Document   ......
function control_on_button(id){
		top.document.getElementById(id).focus();
}
function invalid_input_msg(obj,msg){
	obj.className = 'mandatory';
//	alert(msg);
	
	if(typeof(top.fAlert)!="undefined") {
		top.fAlert(msg);
	}else if(typeof(fancyAlert)!="undefined") {
		alert(msg);
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
function set_phone_format(objPhone, default_format,phone_length,msg_txt){
	
	phone_length = phone_length || top.phone_length;
	phone_min_length = top.phone_min_length;
	default_format = default_format || top.phone_format;
	msg_txt = msg_txt || "phone";
	phone_reg_exp_js =  (typeof(top.phone_reg_exp_js)!="undefined" && typeof(top.phone_reg_exp_js)!=null && top.phone_reg_exp_js!='') ? top.phone_reg_exp_js : "[^0-9+]";
	regExp = new RegExp(phone_reg_exp_js,'g');
	var refinedPh = objPhone.value.replace(regExp,"");	
	refinedPh = refinedPh.trim();
	refinedPh = refinedPh.trimHyphen();
	refinedPh = refinedPh.trimMultiHyphen();
	refinedPh = refinedPh.trimMultiSpace();
	if(refinedPh.length < phone_min_length){
		invalid_input_msg(objPhone, "Please Enter a valid "+msg_txt+" number");return;
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

function set_fax_format(obj, format){
	
	fax_min_length = top.phone_min_length;
	default_format = format || top.phone_format;
	phone_reg_exp_js = "[^0-9+]";
	
	regExp = new RegExp(phone_reg_exp_js,'g');
	var refinedPh = obj.value;
	refinedPh = refinedPh.trim();
	if(refinedPh==''){
		return;
	}
	
	refinedPh = refinedPh.replace(regExp,"");
		
	if(refinedPh.length < fax_min_length){
		invalid_input_msg(obj, "Please Enter a valid Fax number");return;
	}else{
			switch(default_format){
				case "###-###-####":
					obj.value = refinedPh.substring(0,3)+"-"+refinedPh.substring(3,6)+"-"+refinedPh.substring(6);
				break;
				case "(###) ###-####":
					obj.value = "("+refinedPh.substring(0,3)+") "+refinedPh.substring(3,6)+"-"+refinedPh.substring(6);
				break;
				case "(##) ###-####":
					obj.value = "("+refinedPh.substring(0,2)+") "+refinedPh.substring(2,5)+"-"+refinedPh.substring(5);
				break;
				case "(###) ###-###":
					obj.value = "("+refinedPh.substring(0,3)+") "+refinedPh.substring(3,6)+"-"+refinedPh.substring(6);
				break;
				case "(####) ######":
					obj.value = "("+refinedPh.substring(0,4)+") "+refinedPh.substring(4);
				break;
				case "(####) #####":
					obj.value = "("+refinedPh.substring(0,4)+") "+refinedPh.substring(4);
				break;
				case "(#####) #####":
					obj.value = "("+refinedPh.substring(0,5)+") "+refinedPh.substring(5);
				break;
				case "(#####) ####":
					obj.value = "("+refinedPh.substring(0,5)+") "+refinedPh.substring(5);
				break;
				default:
					obj.value = refinedPh;
				break;
			}
	}
	//changeClass(obj);
}

function isDefined(variable){return (!(!( variable||false )))}
window.onload = make_maindiv_full;
function make_maindiv_full(){wh = window.screen.availHeight-100;//alert(typeof(main));
if(isDefined(dgi('main'))) {dgi('main').style.height=wh+'px';if(isDefined(dgi('iMedicDiv')))dgi('iMedicDiv').style.height=wh-180 +"px"; if(isDefined(dgi('iMedicFrame')))dgi('iMedicFrame').style.height=wh-182 +"px";}
	//	alert(dgi('main').style.height+'::'+screen.availHeight);
}//end of make_maindiv_full

function foo(){} // only to pass in href of Anchors which fires event for onClick.
function ele(id,task){dgi(id).style.display=task;} // to show or hide any block with id.

//function below is to toggle the display of any block.
function disp_toggle(id){if(dgi(id).style.display == 'block'){ele(id,'none');} else {ele(id,'block');}}
function toggleDisabled(el) {
 try {el.disabled = el.disabled ? false : true;}
 catch(E){}
 if (el.childNodes && el.childNodes.length > 0){
	for (var x = 0; x < el.childNodes.length; x++){
		toggleDisabled(el.childNodes[x]);
	}
 }
}
//functions below are just the short codes
function dgi(id){return document.getElementById(id);}
function dgn(name){return document.getElementsByName(name);}
function dgt(tag){return document.getElementsByTagName(tag);}
function charlimit(field,count,showin){var tex = field.value; var len = tex.length; if(len > count){tex = tex.substring(0,count); field.value = tex; return false;}dgi(showin).innerHTML = count-len;}
function trim(val) { return val.replace(/^\s+|\s+$/, ''); }
function alphanum(o,w){var r={'special':/[\W]/g}; o.value = o.value.replace(r[w],'');}
function emv(entered){with (entered){apos=entered.indexOf("@");dotpos=entered.lastIndexOf(".");lastpos=entered.length-1;if (apos<1 || dotpos-apos<2 || lastpos-dotpos>3 || lastpos-dotpos<2) {return false;}else {return true;}}}
function wl(url){window.location.href=url;}


//--AJAX CLASS-FUNCTION BELOW--- SHORTEST EXECUTION ---
function gHObj(){var xmlhttp;if(window.XMLHttpRequest){xmlhttp = new XMLHttpRequest();}else if (window.ActiveXObject){xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");if (!xmlhttp){xmlhttp=new ActiveXObject("Msxml2.XMLHTTP");}}return xmlhttp;}
function AjaxConnection(url){this.setOptions=setOptions;this.getOptions=getOptions;this.connect=connect;this.uri=url;}function setOptions(opt){for(i=0;i<opt.length;i++){this.options += "&"+opt[i];}this.options += "&rand="+Math.random()*999999;}function getOptions(){return this.options;}function connect(return_func,showin){with(this){x=init_object();x.open("POST", uri,true);x.onreadystatechange = function(){if (x.readyState != 4)return;eval(return_func + '(x.responseText,showin)');delete x;}
x.setRequestHeader('Content-Type','application/x-www-form-urlencoded');x.send(options);}}function init_object(){var x;try{     x=new ActiveXObject("Msxml2.XMLHTTP");}catch(e){try{x=new ActiveXObject("Microsoft.XMLHTTP");}catch(oc){x=null;}}if(!x && typeof XMLHttpRequest != "undefined")x = new XMLHttpRequest();if(x)return x;}function callBack(content,showin){if(content.indexOf("{js}") != -1){var responseARR = content.split("{js}"); content= responseARR[0];eval(responseARR[1]);}if(typeof(showin)!="undefined") dgi(showin).innerHTML=content;}
function fajax(url,opts,dispin,proshow){if(typeof proshow != 'undefined') dgi(dispin).innerHTML='<span class="doing"></span>';	connection2 = new AjaxConnection(url);	connection2.setOptions(opts); connection2.connect('callBack',dispin);}
//----END OF AJAX CLASS-FUNCTION-----------------

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
function fancyAlert_dynam(msg,title,callback, obj,facnyBtn1,facnyBtn2,func1,func2,mask,maskOpacity, width, height_adjustment, autoClose, showClose, left, top, drag,facnyBtn3,func3){
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
		obj_id = $(obj).attr("id");
		func1 = 'closeDialog_dynam(\'null\',\''+obj_id+'\');'+func1;
		func2 = 'closeDialog_dynam(\'null\',\''+obj_id+'\');'+func2;
	}
	if((left != "") || (top != "")){
		isCenter = false
	}
	dialogBox_dynam(title,msg,facnyBtn1,facnyBtn2,func1,func2,false,mask,drag,callback, width, "", isCenter, left, top, showClose, obj,maskOpacity, height_adjustment,facnyBtn3,func3);
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
	new window.top.Messi(msgg, MessiOptionsArr);
	/* Messi embedding end---*/


}//end of function dialogBox.

function dialogBox_dynam(title,msgg,btn1,btn2,func1,func2,btnCancel,mask,drag,callback,w,h,isCenter,l,t,showClose,inThis,maskOpacity, height_adjustment,btn3,func3)
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
	if((typeof(w) == "string" && w=='default') || typeof(w) == "undefined" || w<=0){w="300px";}else{w=parseInt(w)+"px";}
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
	var top = topPosition();
	if(typeof(l) != "undefined" && !isCenter){left=l+"px";}
	if(typeof(t) != "undefined" && !isCenter){top=t+"px";}
	var dialogwidth = w;  
	var dialogheight = h; //alert('TypeofLeft='+typeof(dialogheight)+', height='+typeof(height))
	var topposition = 100;
	var leftposition = parseInt(left) + (width / 2) - (parseInt(dialogwidth) / 2);
	if(typeof(dialogBoxCounter)=="undefined") dialogBoxCounter=0; else dialogBoxCounter++;
	if(callback==null){callback=false;}else if(callback==''){callback=false;}
	//alert(showClose);
	inThis_id = $(inThis).attr('id');
	divCon_id = "divCon_"+inThis_id;
	divConNew_id = "divConNew_"+inThis_id;
	
	if(showClose){title = '<span class="closeBtn" onClick="closeDialog_dynam(\'callback\',\''+inThis_id+'\');"></span>'+title;}
	text += '<div id="'+divCon_id+'" style="position:absolute; z-index:1000; width:350px; background-color:#ffffff; color:#000000; display:none; border:1px solid #999999;"><div id="'+divConNew_id+'" class="boxhead alignLeft" style="cursor:move;">'+title+'</div>';
	
	//alert(height_adjustment);
	
	dialogMsgId = "dialogMsg_"+inThis_id;
	
	if(isNaN(height_adjustment) == false){
		text += '<p style="text-align:left;max-height:'+height_adjustment+'px;overflow:auto;overflow-x:hidden" class="mt10 padd10" id="'+dialogMsgId+'"></p>';
	}else{
		text += '<p style="text-align:left" class="mt10 padd10" id="'+dialogMsgId+'"></p>';
	}

	text += '<div class="mt10 padd10" style="text-align:center;">';
	dialogBtn1 = "dialogBtn1_"+inThis_id;
	if(typeof(btn1) == "string")
		text += '<input id="'+dialogBtn1+'" type="button" value="'+btn1+'" onClick="'+func1+'" class="dff_button">';
	if(typeof(btn2) == "string")
		text += ' &nbsp; <input type="button" value="'+btn2+'" onClick="'+func2+'" class="dff_button">';
	if(typeof(btn3) == "string")
		text += ' &nbsp; <input type="button" value="'+btn3+'" onClick="'+func3+'" class="dff_button">';	
	if(btnCancel)
		text += ' &nbsp; <input type="button" value="Cancel" onClick="closeDialog_dynam("null","'+inThis+'");" name="cancel" class="dff_button">';
	text += '</div>';
	text += '</div>';
	if(mask){text += '<div style="width:'+width+'px; height:'+height+'px; z-index:999; filter:alpha(opacity='+maskOpacity+');opacity:'+maskBrowserOpacity+';" class="dialogMask">' + text + '</div>';}
	
	if(typeof(inThis) != "object"){
	//	document.write(text);
		if(typeof(sb_testName)!="undefined"){			
			$("body").append(text);
		}else{
			pageBody = dgt('body');		
			pageBody['0'].innerHTML += text;
		}
	}else{
		inThis.innerHTML = text;
	}//alert(dgi('dialogMsg'));
	//alert(msgg);
	//msgg = unescape(msgg);
	jId= "#"+dialogMsgId;
	$(jId).html(msgg);
	
	jdivConId= "#"+divCon_id;
	dgi(divCon_id).style.width = w;	dgi(divCon_id).style.height = h;
	if(isCenter){
		top = $("div[id^=divCon]").css("top");
		if(top != "auto"){
		top = parseInt(top.replace('px',''));
		top = eval(parseInt(top)+40);
		}else{
			top = topposition;
		}
		
		
		left = $("div[id^=divCon]").css("left");
		if(left != "auto"){
		left = left.replace('px','');
		left = eval(parseInt(left)+40);
		}else{
			left = leftposition;
		}
		
		dgi(divCon_id).style.top = top + "px";
		dgi(divCon_id).style.left = left + "px";
		//dgi(divCon_id).style.top = topposition + "px";
		//dgi(divCon_id).style.left = leftposition + "px";
	}else
	{
		dgi(divCon_id).style.top = top;
		dgi(divCon_id).style.left = left;
	}

	if(typeof window.top.chk_window_opened =="undefined")
	{
		window.top.chk_window_opened = new Array();
		window.top.chk_window_opened["chkinwin"]  = false;					
	}

	$(jdivConId).fadeIn('fast',function(){if(typeof(btn1) == "string" && window.top.chk_window_opened["chkinwin"]!=true){dgi(dialogBtn1).focus();}});
	//if(drag) DragHandler.attach(dgi('divCon'));
	if(drag) {
		jdivConNew_id = "#"+divConNew_id;
		$(jdivConId).draggable({ handle: jdivConNew_id });	
	}
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
function closeDialog_dynam(callback,obj_id){
	if(callback==null){callback=false;}else if(callback==''){callback=false;}else{callback='function(){'+callback+';}';}
	//alert(callback);
	$('body .dialogMask').fadeOut('slow');

	jdiv = "#divCon_"+obj_id;
	$(jdiv).fadeOut('slow');
	$(jdiv).parent('div,span').html('');
//	el = dgi('divCon');
//	el.parentNode.removeChild(el);
}
function alert_notification_show(m){
	o1 = $('#div_alert_notifications');
	o2 = $('#div_alert_notifications').find('.notification_span').html(m);
	o1.show('drop',1000,function(){o1.effect('bounce');});

	ACT = 2;//Auto Close Time (seconds).
	t = setInterval(function(){if(ACT>0){ACT--;}else{clearInterval(t);o1.fadeOut('slow');o2.html('');}},1000);
	
}

function removeElement(id){
	var d = dgi('main');
	var olddiv = document.getElementById(id);
	d.removeChild(olddiv);
}

var MakeItCenter = {
	_oElem : null,
	// public method. Attach drag handler to an element.
	attach : function(oElem) {
		wid = pageWidth();
		x = parseInt(oElem.style.width);
		oElem.style.left = parseInt((wid/2)-(x/2))+'px';
		return oElem;
	}
}//end of MakeItCenter.

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
/*FUNCTION TO MAKE SCROLLABLE TABLE*/
/**
*
*  Scrollable HTML table
*  http://www.webtoolkit.info/
*
**/
 
function ScrollableTable (tableEl, tableHeight, tableWidth) {
	this.initIEengine = function () {
		this.containerEl.style.overflowY = 'auto';
		if (this.tableEl.parentElement.clientHeight - this.tableEl.offsetHeight < 0) {
			this.tableEl.style.width = this.newWidth - this.scrollWidth +'px';
		} else {
			this.containerEl.style.overflowY = 'hidden';
			this.tableEl.style.width = this.newWidth +'px';
		}
 
		if (this.thead) {
			var trs = this.thead.getElementsByTagName('tr');
			for (x=0; x<trs.length; x++) {
				trs[x].style.position ='relative';
				trs[x].style.setExpression("top",  "this.parentElement.parentElement.parentElement.scrollTop + 'px'");
			}
		}
		if (this.tfoot) {
			var trs = this.tfoot.getElementsByTagName('tr');
			for (x=0; x<trs.length; x++) {
				trs[x].style.position ='relative';
				trs[x].style.setExpression("bottom",  "(this.parentElement.parentElement.offsetHeight - this.parentElement.parentElement.parentElement.clientHeight - this.parentElement.parentElement.parentElement.scrollTop) + 'px'");
			}
		}
		eval("window.attachEvent('onresize', function () { document.getElementById('" + this.tableEl.id + "').style.visibility = 'hidden'; document.getElementById('" + this.tableEl.id + "').style.visibility = 'visible'; } )");
	};
	this.initFFengine = function () {
		this.containerEl.style.overflow = 'hidden';
		this.tableEl.style.width = this.newWidth + 'px';
		var headHeight = (this.thead) ? this.thead.clientHeight : 0;
		var footHeight = (this.tfoot) ? this.tfoot.clientHeight : 0;
		var bodyHeight = this.tbody.clientHeight;
		var trs = this.tbody.getElementsByTagName('tr');
		if (bodyHeight >= (this.newHeight - (headHeight + footHeight))) {
			this.tbody.style.overflow = '-moz-scrollbars-vertical';
			for (x=0; x<trs.length; x++) {
				var tds = trs[x].getElementsByTagName('td');
				tds[tds.length-1].style.paddingRight += this.scrollWidth + 'px';
			}
		} else {
			this.tbody.style.overflow = '-moz-scrollbars-none';
		}
		var cellSpacing = (this.tableEl.offsetHeight - (this.tbody.clientHeight + headHeight + footHeight)) / 4;
		this.tbody.style.height = (this.newHeight - (headHeight + cellSpacing * 2) - (footHeight + cellSpacing * 2)) + 'px';
	};
	this.tableEl = tableEl;
	this.scrollWidth = 16;
	this.originalHeight = this.tableEl.clientHeight;
	this.originalWidth = this.tableEl.clientWidth;
	this.newHeight = parseInt(tableHeight);
	this.newWidth = tableWidth ? parseInt(tableWidth) : this.originalWidth;
	this.tableEl.style.height = 'auto';
	this.tableEl.removeAttribute('height');
	this.containerEl = this.tableEl.parentNode.insertBefore(document.createElement('div'), this.tableEl);
	this.containerEl.appendChild(this.tableEl);
	this.containerEl.style.height = this.newHeight + 'px';
	this.containerEl.style.width = this.newWidth + 'px';
	var thead = this.tableEl.getElementsByTagName('thead');
	this.thead = (thead[0]) ? thead[0] : null;
	var tfoot = this.tableEl.getElementsByTagName('tfoot');
	this.tfoot = (tfoot[0]) ? tfoot[0] : null;
	var tbody = this.tableEl.getElementsByTagName('tbody');
	this.tbody = (tbody[0]) ? tbody[0] : null;
	if (!this.tbody) return;
	if (document.all && document.getElementById && !window.opera) this.initIEengine();
	if (!document.all && document.getElementById && !window.opera) this.initFFengine();
}

function ScrollableTableRpt (tableEl, tableHeight, tableWidth) {
	this.initIEengine = function () {
		this.containerEl.style.overflowY = 'auto';
		if (this.tableEl.parentElement.clientHeight - this.tableEl.offsetHeight < 0) {
			this.tableEl.style.width = this.newWidth - this.scrollWidth +'px';
		} else {
			this.containerEl.style.overflowY = 'hidden';
			this.tableEl.style.width = this.newWidth +'px';
		}
 
		if (this.thead) {
			var trs = this.thead.getElementsByTagName('tr');
			for (x=0; x<trs.length; x++) {
				trs[x].style.position ='relative';
				//trs[x].style.setExpression("top",  "this.parentElement.parentElement.parentElement.scrollTop + 'px'");
			}
		}
		if (this.tfoot) {
			var trs = this.tfoot.getElementsByTagName('tr');
			for (x=0; x<trs.length; x++) {
				trs[x].style.position ='relative';
				//trs[x].style.setExpression("bottom",  "(this.parentElement.parentElement.offsetHeight - this.parentElement.parentElement.parentElement.clientHeight - this.parentElement.parentElement.parentElement.scrollTop) + 'px'");
			}
		}
		eval("window.attachEvent('onresize', function () { document.getElementById('" + this.tableEl.id + "').style.visibility = 'hidden'; document.getElementById('" + this.tableEl.id + "').style.visibility = 'visible'; } )");
	};
	this.initFFengine = function () {
		this.containerEl.style.overflow = 'hidden';
		this.tableEl.style.width = this.newWidth + 'px';
		var headHeight = (this.thead) ? this.thead.clientHeight : 0;
		var footHeight = (this.tfoot) ? this.tfoot.clientHeight : 0;
		var bodyHeight = this.tbody.clientHeight;
		var trs = this.tbody.getElementsByTagName('tr');
		if (bodyHeight >= (this.newHeight - (headHeight + footHeight))) {
			this.tbody.style.overflow = '-moz-scrollbars-vertical';
			for (x=0; x<trs.length; x++) {
				var tds = trs[x].getElementsByTagName('td');
				tds[tds.length-1].style.paddingRight += this.scrollWidth + 'px';
			}
		} else {
			this.tbody.style.overflow = '-moz-scrollbars-none';
		}
		var cellSpacing = (this.tableEl.offsetHeight - (this.tbody.clientHeight + headHeight + footHeight)) / 4;
		this.tbody.style.height = (this.newHeight - (headHeight + cellSpacing * 2) - (footHeight + cellSpacing * 2)) + 'px';
	};
	this.tableEl = tableEl;
	this.scrollWidth = 16;
	this.originalHeight = this.tableEl.clientHeight;
	this.originalWidth = this.tableEl.clientWidth;
	this.newHeight = parseInt(tableHeight);
	this.newWidth = tableWidth ? parseInt(tableWidth) : this.originalWidth;
	this.tableEl.style.height = 'auto';
	this.tableEl.removeAttribute('height');
	this.containerEl = this.tableEl.parentNode.insertBefore(document.createElement('div'), this.tableEl);
	this.containerEl.appendChild(this.tableEl);
	this.containerEl.style.height = this.newHeight + 'px';
	this.containerEl.style.width = this.newWidth + 'px';
	var thead = this.tableEl.getElementsByTagName('thead');
	this.thead = (thead[0]) ? thead[0] : null;
	var tfoot = this.tableEl.getElementsByTagName('tfoot');
	this.tfoot = (tfoot[0]) ? tfoot[0] : null;
	var tbody = this.tableEl.getElementsByTagName('tbody');
	this.tbody = (tbody[0]) ? tbody[0] : null;
	if (!this.tbody) return;
	if (document.all && document.getElementById && !window.opera) this.initIEengine();
	if (!document.all && document.getElementById && !window.opera) this.initFFengine();
}

/*-----------function to get, provided object is full vertically scrolled or not---*/
function isScrolledV(id){id='#'+id;OuterHeight = $(id).height();InnerHeight = $(id).attr('scrollHeight');Scrolled = $(id).scrollTop();
//alert('id='+id+', OuterHeight='+OuterHeight+', InnerHeight='+InnerHeight+', Scrolled='+Scrolled);
if(OuterHeight+Scrolled >= InnerHeight) return true; else return false;}

//---STARTING FUNCTION TO STOP F5, CTRL+F5, CTRL+N --------

window.document.onkeydown=function(event)
{
	//alert('keycode='+event.keyCode + ',  event.ctrlKey='+event.ctrlKey );
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
		case 116 : //F5
		//	event.returnValue=false;
		//	event.keyCode=0;
		//	window.status = 'Refresh is disabled';
		//	return false;
			do_audit = 'no';
			auditHome = 'no';
			audit = 'no';
			break;
/*		case 78: //n
			if (event.ctrlKey) {
				event.returnValue=false;
				event.keyCode=0;
				window.status = 'New window is disabled';
				return false;
			}
*/
		case 82 : //ctrl+f5
		if (event.ctrlKey){
	//		event.returnValue=false;
	//		event.keyCode=0;
	//		window.status = 'Refresh is disabled';
	//		return false;
			do_audit = 'no';
			auditHome = 'no';
			audit = 'no';			
		}
			break;
	
/*		
		case 8 : //backspace
			event.returnValue=false;
			event.keyCode=0;
			window.status = 'Backspace is disabled';
			return false;
	*/	
	}
}
//--------END OF REFRESH AND NEW WINDOW KEY STROKE PREVENTION-----

function show_time_footer(){var C=new Date(); var h=C.getHours(); var m=C.getMinutes(); var s=C.getSeconds();
	var dn="PM"; if (h<12) dn="AM"; if (h>12) h=h-12; if (h==0) h=12; if (m<=9) m="0"+m; if(s<=9) s="0"+s; var tm=h+":"+m+":"+s+" "+dn;
	dgi('dt_tm').innerHTML="<span style='color:#4A67A2'>"+tm+"</span>"; setTimeout("show_time_footer('dt_tm')",1000);
}//end of show2

//expand and shrink select-multiselect
var select_opened, close_opened;
function ShrinkSelect(id,size){
	if(select_opened) clearInterval(select_opened);
  	$('#'+id).animate({height:size}, 10, 'linear');

}

function ExpandSelect(id, size){
	select_opened = setInterval(function(){clearInterval(select_opened);$('#'+id).css('position','absolute');$('#'+id).animate({height:size}, 100, 'linear');},200);
}

function section_highlight(section_id,arr_all_sections,di_highlight_all){
	$(arr_all_sections).each(function(){
		$('#' + this).removeClass('bg3').addClass('tblBg');
	});
	if(typeof(di_highlight_all) == 'undefined' || !di_highlight_all){
		$('#' + section_id).removeClass('tblBg').addClass('bg3');
	}
}

function onKeyDown(func1) {
	/*
	if(event.keyCode == 122) {
		top.preview_patient_details(e)
		event.keyCode = 0;	
		event.returnValue = false;
		if(func1) {
			eval(func1);
		}
	}*/
	/*
	if ( (event.altKey) || ((event.keyCode == 8) && 
		(event.srcElement.type != "text" &&
		event.srcElement.type != "textarea" &&
		event.srcElement.type != "password")) || 
		((event.ctrlKey) && ((event.keyCode == 82)) ) ||
		(event.keyCode == 122) ) {alert('hi');
		event.keyCode = 0;
		event.returnValue = false;
	}*/
	}
	

/*---MODIFIED FANCY ALERT (USED IN TESTS)--*/
function fAlert(msg, title, actionToPerform, width, height, BtnCaption,ModalMode) {
	if(typeof(title)=='undefined' || title=='') 			title='imwemr';
	if(typeof(width)=='undefined' || width=='') 			width='500px';
	if(typeof(height)=='undefined' || height=='') 			height='auto';
	if(typeof(BtnCaption)=='undefined' || BtnCaption=='') 	BtnCaption='OK';
	if(typeof(ModalMode)!='boolean')						ModalMode=true;
	if(BtnCaption=='CLOSE' || BtnCaption=='Close'){closeBtn=false;} else{closeBtn=true;}
	//if(actionToPerform.indexOf('window.top.')== -1 && actionToPerform.indexOf('top.')== 0){actionToPerform = "window."+actionToPerform;}
	new window.top.Messi(msg, {'title': title, modal: ModalMode, 'width': width, 'closeButton':closeBtn, buttons: [{id: 0, label: BtnCaption, val: 'X'}],callback: function(){if(typeof(actionToPerform)=='string' && actionToPerform.indexOf('/*callmebeforeclosingmessi*/')>=0){actionToPerform;}else if(typeof(actionToPerform)=='string' && actionToPerform.indexOf('/*callmebeforeclosingmessi*/')<0){eval(actionToPerform);}else if(typeof(actionToPerform)=='object'){actionToPerform.focus();}}});
}
function fancyConfirm(msg, title, YsAction, NoAction, width, height) {
	if(typeof(YsAction)=='undefined') {YsAction=title; title='';}
	if(typeof(title)=='undefined' || title=='') title='imwemr';
	if(typeof(width)=='undefined' || width=='') width='500px';
	if(typeof(NoAction)=='undefined' || NoAction=='') NoAction=false;
	if(YsAction.indexOf('window.top.')==-1 && YsAction.indexOf('top.')==0){YsAction = "window."+YsAction;}
	if(NoAction!=false && NoAction.indexOf('window.top.')==-1 && NoAction.indexOf('top.')==0){NoAction = "window."+NoAction;}
	new window.top.Messi(msg, {'title': title, modal: true, 'width': width, buttons: [{id: 0, label: 'Yes', val: 'Y', "class": 'btn-success'}, {id: 1, label: 'No', val: 'N', "class": 'btn-danger'}],callback: function(val){if(val=='Y'){eval(YsAction);}else if(val=='N' && NoAction!=false){eval(NoAction);}}});
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
		//date_format = (typeof(top.opener) != 'undefined' ? top.opener.top.date_format:(typeof(opener) != 'undefined' ? opener.top.date_format:top.date_format));
		if(typeof(top.date_format) != "undefined"){
			date_format = top.date_format;
		}else if(typeof(opener.top.date_format) != "undefined"){
			date_format = opener.top.date_format;
		}else if(typeof(top.opener.top.date_format) != "undefined"){
			date_format = top.opener.top.date_format;
		}
		format = format || date_format;
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
		outFormat = outFormat || top.date_format || opener.top.date_format;
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
			alert("Invalid Social Security Number (SSN)");
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
	var separator = get_date_separator();
	var date_val = $(objName).val().split(separator);
	$.each(date_val,function(id,val){
		if(val.length == 1){
			val = '0'+val;
		}
		date_val[id] = val;
	});
 date_val = date_val.join(separator);
 if(date_val != '' || typeof(date_val) != 'undefined' || typeof(date_val) != 'null'){
  $(objName).val(date_val);
 }
 if (validate_date(objName,type) == false && objName.value!='') {
  objName.value="";
  objName.focus();
  if(typeof(opener) == 'undefined' && typeof(top.opener) == 'undefined'){
   top.fAlert('Please enter a valid Date');
   top.document.getElementById("divCommonAlertMsg").style.display = "block";
  }else{
   top.fAlert('Please enter a valid Date');
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

function ZeissViewer(pid, user, pass, path){
	if(pid=="")pid = window.top.$("#sess_patient_id").val();
    u=user;
	p=pass;
	params = " -username "+u+" -password "+p+" -patientId "+pid;
	exeURL= '"'+path+'\\launchFORUM.cmd"'+params;
	try{
		WSH=new ActiveXObject("WScript.Shell");
		WSH.run(exeURL);
	}catch(e){
		if(e!=''){
			exeURL = exeURL.replace('Program Files','Program Files (x86)');
			WSH=new ActiveXObject("WScript.Shell");
			WSH.run(exeURL);
		}
	}
}

function fillInterpretationProfileData(id){//alert(id+' :: '+arr_interpretation_pofiles[1]);
	if(typeof(arr_interpretation_pofiles)=='undefined') return;
	main_data_array = arr_interpretation_pofiles[id];
	data_array = jQuery.parseJSON(main_data_array);
	f = document.forms[0];//.test_form;
	if(id=='' && typeof(reset_form_if_no_inter_pro_selected)=='boolean' && reset_form_if_no_inter_pro_selected) {f.reset();return;}
	e = f.elements;
	for(i=0;i<e.length;i++){
		o = e[i];
		on	= o.name;
		v	= data_array[on];
		
		/*if name of the element is an array*/
		if(on && on.indexOf('[]')!= '-1' && on.substr(on.length - 2)=='[]'){
			on = on.trim();
			name1 = on.substr(0, (on.length - 2));
			v1	= data_array[name1];
			elementarray = document.getElementsByName(on);
			if(elementarray.length && v1){
				for(j=0;j<elementarray.length;j++){
					elem = elementarray[j];
					val = v1[j];
					if (elem.tagName == "INPUT" || elem.tagName == "SELECT" || elem.tagName == "TEXTAREA"){
						if (elem.type == "checkbox"){
							if(val==elem.value) $(elem).prop('checked',true);
						}else if(elem.type == "radio"){
							if(val==elem.value) $(elem).prop('checked',true);	
						}else if(elem.type!='submit' && elem.type!='button'){
							elem.value = val;
							if(elem.tagName == "TEXTAREA"){elem.value = elem.value.replace(/<br>/g,"\n");}
						}
					}
				}
			}
		}

		if(typeof(v)=='undefined')continue;
		if (o.tagName == "INPUT" || o.tagName == "SELECT" || o.tagName == "TEXTAREA"){
			if (o.type == "checkbox"){
				if(v==o.value) $(o).prop('checked',true);
			}else if(o.type == "radio"){
				if(v==o.value) $(o).prop('checked',true);	
			}
			else if(o.type!='submit' && o.type!='button'){
				o.value = v;
				if(o.tagName == "TEXTAREA"){o.value = o.value.replace(/<br>/g,"\n");}
			}
		}
	}
	
	if($("input[name='elem_saveForm']")){
		if($("input[name='elem_saveForm']").val()=="Disc" && data_array['elem_diagnosis']){
			setDiagOpts();
			$('select[name="elem_diagnosis"]').val(data_array['elem_diagnosis']);
			var fu_retina = gebi('elem_fuRetina');
			document.getElementById("coment").style.visibility = (fu_retina.checked == true) ? "visible" : "hidden" ;
		}
		else if($("input[name='elem_saveForm']").val()=="ICG"){
			if($("input[name='elem_ArgonLaserEye']:checked").length>0){
				$("#als").css('visibility','visible');
			}
			if($("#Hot_Spot_OD:checked").length>0){
				$("#Hot_Spot_Val_OD").attr('disabled',false);
			}
			if($("#Hot_Spot_OS:checked").length>0){
				$("#Hot_Spot_Val_OS").attr('disabled',false);
			}
		}
		else if($("input[name='elem_saveForm']").val()=="IVFA"){
			if($("input[name='elem_ArgonLaserEye']:checked").length>0){
				$("#als").css('visibility','visible');
			}
		}
		else if($("input[name='elem_saveForm']").val()=="Pachy"){
			if($("#elem_pachy_od_average").val()!=""){
				$("#td_pachy_od_avg").css('display', 'block');
			}
			if($("#elem_pachy_od_correction_value").val()!=""){
				$("#td_pachy_od_correction_value").css('display', 'block');
			}
			if($("#elem_pachy_os_average").val()!=""){
				$("#td_pachy_os_avg").css('display', 'block');
			}
			if($("#elem_pachy_os_correction_value").val()!=""){
				$("#td_pachy_os_correction_value").css('display', 'block');
			}
		}
	}
}

/*FUNCTION TO SHOW MEDIA LIST & PLAYER*/
function showRecordingControl(source,source_id,callBack){
	/*	source: 	like VF/ VF-GL / work_view / consult etc.
		source_id:	like test_id, form_id, letter_id etc.
		callBack: 	Function to call after recording done and 'close' button pressed	
	*/
	path_prefix = '';
	if(top.WRP){path_prefix = top.WRP+'/interface';}
	else if(top.zPath){path_prefix = top.zPath;}
	else if(window.top.opener && window.top.opener.top.WRP){path_prefix = window.top.opener.top.WRP+'/interface';}
	else if(window.top.opener.top.opener && window.top.opener.top.opener.top.WRP){path_prefix = window.top.opener.top.opener.top.WRP+'/interface';}

	width 		= 550; //DEFAULT WIDTH
	height 		= 400; //DEFAULT HEIGHT
	source_file	= '';
	if(typeof(callBack)!='string'){callBack="";} //not in use for now, but created for later requirement.
	
	switch(source){
		case 'work_view':
		case 'exam_pupil':
		case 'exam_eom':
		case 'exam_external':
		case 'exam_la':
		case 'exam_iop':
		case 'exam_sle':
		case 'exam_fundus':
		case 'exam_refractive':
			form_id = source_id;
			//width = 650; height = 470;
			break;
		case 'consult_letter':
		case 'opnote':
			source_file	= 'audio.php';
			form_id = source_id;
			width = 500; height = 220;
			break;
		case 'BScan':
		case 'CellCount':
		case 'discExternal':
		case 'Disc':
		case 'GDX':
		case 'NFA':
		case 'ICG':
		case 'IVFA':
		case 'TestLabs':
		case 'OCT':
		case 'OCT-RNFL':
		case 'Pacchy':
		case 'Topogrphy':
		case 'VF':
		case 'VF-GL':
		case 'TestOther':
		case 'TemplateTests':
		case 'Ascan':
		case 'IOL_Master':
			temp_IDs 	= source_id.split('_');
			source_id	= temp_IDs[0];
			form_id		= temp_IDs[1] ? temp_IDs[1] : '';
			if(callBack!='' && typeof(callBack)=='string'){
				if(top.fmain){callBack = 'window.top.fmain.ifrm_FolderContent.'+callBack;}
			}
			break;
		
	}
	top.fAlert('<iframe name="messiframe" id="messiframe" style="height:'+height+'px; width:100%;" frameborder=0 src="'+path_prefix+'/common/record_av/'+source_file+'?tId='+source_id+'&testName='+source+'&form_id='+form_id+'"></iframe><div class="alignCenter">','Record MultiMedia Message',callBack,width,height+10,'CLOSE');
}

function showMultiMediaMessage(source,source_id){
	/*	source: 	like VF/ VF-GL / work_view / consult_letter etc.
		source_id:	like test_id, form_id, letter_id etc.
	*/
	path_prefix = '';
	if(top.WRP){path_prefix = top.WRP+'/interface';}
	else if(top.zPath){path_prefix = top.zPath;}
	else if(window.top.opener && window.top.opener.top.WRP){path_prefix = window.top.opener.top.WRP+'/interface';}
	else if(window.top.opener.top.opener && window.top.opener.top.opener.top.WRP){path_prefix = window.top.opener.top.opener.top.WRP+'/interface';}
	
	width = 790;
	height = 400;
	if(source=='consult_letter' || source=='opnote'){
		height 	= 250;
		width	= 750;
	}
//a=window.open();
//a.document.write(path_prefix+'/common/record_av/multimedia_messages.php?source_id='+source_id+'&source='+source);
	top.fAlert('<iframe name="messiframe" id="messiframe" style="height:'+height+'px; width:100%;" frameborder=0 src="'+path_prefix+'/common/record_av/multimedia_messages.php?source_id='+source_id+'&source='+source+'"></iframe><div class="alignCenter">','MultiMedia Message','',width,'','CLOSE');
}

function showMediaList(ptid,tid,tname,delm_IDs,cnfrm){
	if(typeof(delm_IDs)=='undefined'){delm_IDs='';}
	path_prefix = '';
	if(window.top.WRP){path_prefix = window.top.WRP+'/interface';}
	else if(window.top.zPath){path_prefix = window.top.zPath;}
	else if(window.top.opener && window.top.opener.top.WRP){path_prefix = window.top.opener.top.WRP+'/interface';}
	else if(window.top.opener.top.opener && window.top.opener.top.opener.top.WRP){path_prefix = window.top.opener.top.opener.top.WRP+'/interface';}
	$('#div_media_list').load(path_prefix+'/common/record_av/media_list_tests.php?patient_id='+ptid+'&tId='+tid+'&source='+tname+'&delm_ids='+delm_IDs,'',function(){
		cnfrm=false;
		$(this).find('span.icon_delete').click(function(){
			delm_IDs = $(this).attr('media_ids');
			if(typeof(cnfrm)=='undefined' || cnfrm==false){
				delFun = "showMediaList('"+ptid+"','"+tid+"','"+tname+"','"+delm_IDs+"',"+true+")";
				if(top.fmain && tname!='work_view'){
					delFun='window.top.fmain.ifrm_FolderContent.'+delFun;
				}
				if(tname=='work_view' || tname=='exam_pupil' || tname=='exam_eom' || tname=='exam_external' || tname=='exam_la' || tname=='exam_iop' || tname=='exam_sle' || tname=='exam_fundus' || tname=='exam_refractive' || tname=='consult_letter' || tname!='opnote'){
					fancyConfirm('Are you sure want to delete this recording?','',delFun);
				}else{
					top.fancyConfirm('Are you sure want to delete this recording?','',delFun);
				}
			}
			else{showMediaList(ptid,tid,tname,delm_IDs);}
		});
	});	
}

function playMedia(json_str_filenames,json_str_filetypes,ptid,mtype){
	if(mtype=='audio'){
		h = 100; w = 400;

	}else{
		h = 450; w = 600;
	}
	
	path_prefix = '';
	if(top.WRP){path_prefix = top.WRP+'/interface';}
	else if(top.zPath){path_prefix = top.zPath;}
	else if(window.top.opener && window.top.opener.top.WRP){path_prefix = window.top.opener.top.WRP+'/interface';}
	else if(window.top.opener.top.opener && window.top.opener.top.opener.top.WRP){path_prefix = window.top.opener.top.opener.top.WRP+'/interface';}
	top.fancyModal('<iframe name="messiframe" id="messiframe" style="height:'+h+'px; width:'+w+'px;" frameborder=0 src="'+path_prefix+'/common/record_av/player.php?jsfn='+escape(json_str_filenames)+'&jsft='+escape(json_str_filetypes)+'&mt='+mtype+'&pt='+ptid+'"></iframe>','MultiMedia Message Player');
}
