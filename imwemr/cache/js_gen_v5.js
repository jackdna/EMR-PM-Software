
var escapeRegExp=function(string){return string.replace(/([.*+?^${}()|\[\]\/\\])/g,"\\$1");}
function rgp(s,m){if(typeof(s)!="undefined"&&s!=""){if(typeof(m)=="undefined"){m="gi";}
return new RegExp(s,m);}
return s;}
function js_htmlentities(str){return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');}
function mk_var_nm(str,wh){str=str.replace(/[^0-9a-z]/gi,'');str=wh+str;return str;}
function regReplace(ptrn,repW,str){if($.trim(ptrn)!=""){var reg=new RegExp(ptrn,"g");var mtch=str.match(reg);if(mtch){str=str.replace(reg,"");}else{}}
return $.trim(str);}
function gebi(id,t){var o;if(t==1){o=document.getElementsByName(id);}else if(t==2){o=document.getElementsByTagName(id);}else{o=document.getElementById(id);if(o==null){o=document.getElementsByName(id)[0];}}
return o;}
function stopClickBubble(flg)
{var ev=window.event;if(ev){if(typeof(flg)!="undefined"&&flg==1){var chk=ev.srcElement.nodeName;var chktag=ev.srcElement.tagName;var eType=ev.srcElement.type;if(chk=="INPUT"||chk=="TEXTAREA"||((eType=="change"||eType=="keyup"||eType=="mousedown")&&chk=="SELECT")){return;}}
ev.cancelBubble=true;if(ev.stopPropagation)ev.stopPropagation();}}
function get_src_center(){var left=($(window).width()/2);var top=($(window).height()/2);return{'top':top,'left':left};}
function scroll_into_view(sele,hid){var offset=$(sele).offset();var diff=0;var scrollTop=$(window).scrollTop();var scrollBottom=scrollTop+$(window).height();var ofHgt=$(sele).height();var offsetBottom=offset.top+ofHgt;if(offsetBottom>scrollBottom){diff=scrollTop+(offsetBottom-scrollBottom);$('html, body').animate({scrollTop:diff},'slow','linear');}}
function get_js_dt_frmt(){var d=(typeof(top.jquery_date_format)!="undefined"&&top.jquery_date_format!="")?top.jquery_date_format:"mm-dd-yy";if(d=="m-d-Y"){d="mm-dd-yy";}
return d;}
function currenttime(){var Digital=new Date()
var hours=Digital.getHours()
var minutes=Digital.getMinutes()
var seconds=Digital.getSeconds()
var dn="PM"
if(hours<12)
dn="AM"
if(hours>12)
hours=hours-12
if(hours==0)
hours=12
if(minutes<=9)
minutes="0"+minutes
if(seconds<=9)
seconds="0"+seconds
var ctime=hours+":"+minutes+dn
return ctime;}
function insertDate(obj){var id=obj.name.replace(/Txt/g,"Dt");var tobj=gebi(id);if(tobj){tobj.value=$.datepicker.formatDate('mm-dd-yy',new Date());}}
function insertTime(obj){var id=obj.name.replace(/finding/g,"timeStamp");var tobj=gebi(id);if(tobj&&tobj.value==""){tobj.value=currenttime();if(typeof(tobj.onblur)=="function"){tobj.onblur();}}
if(obj.name.indexOf("schirmer")!=-1){var id=obj.name.replace(/finding/g,"date");var tobj=gebi(id);if(tobj&&tobj.value==""){tobj.value=$.datepicker.formatDate('mm-dd-yy',new Date());}}}
function fun_mselect(o,w,v,t,f){if(w=="val"){var x=$(o).selectpicker('val');if(!x){x="";}return x;}else if(w=="title"){return $(o).parent().find("button").attr("title");}else if(w=='render'||w=='refresh'){$(o).selectpicker(w);}else if(w=='select'){if(typeof(f)!="undefined"&&f!=""){if(typeof(v)!="undefined"&&v.length>0){var ar_chk=v.split(', ');}}
$(o).find('option[value]').each(function(){var art=this.value.split('\*\*');var tv=this.value;if(typeof(f)!="undefined"&&f!=""){if(typeof(ar_chk)!="undefined"&&ar_chk.length>0){this.selected=(ar_chk.indexOf(tv)!=-1)?true:false;}}else{this.selected=(typeof(v)!="undefined"&&v.length>0&&(v.indexOf(art[0])!=-1||v.indexOf(tv)!=-1))?true:false;}});fun_mselect(o,'render');if(typeof(t)!="undefined"&&t!=""){fun_mselect(o,'settitle',t);}}else if(w=="settitle"){var op=$(o).parent();var ob=op.find("button");ob.attr("title",v);if(v==""){ob.find("span.filter-option").html(v);op.find("ul li").removeClass("selected");}}else if(w=='unselect'){$(o).find('option[value]').each(function(){if(typeof(v)!="undefined"&&this.value==v){this.selected=false;}});fun_mselect(o,'render');}else if(w=='onchange'){$(o).on('changed.bs.select',v);}else if(w=='width'){var aw=0,w="";aw=$("#tblSuperbill").data("dx_width");if(typeof(aw)=="undefined"){$("#tblSuperbill").hide();var otd=$("#tblSuperbill").parent().find(".grythead th:nth-child(3)");aw=otd[0].offsetWidth;if(typeof(aw)=="undefined"){aw=otd.attr("width");}
$("#tblSuperbill").data("dx_width",aw);$("#tblSuperbill").show();}
if(typeof(aw)=="undefined"){aw=0;}
$(o).each(function(){if($(this).parents("#tblSuperbill").length==0){return true;}
if(aw!=0){$(this).parents("td.dx").attr("width",aw);var aw1=aw;$(this).parents(".bootstrap-select").css({"width":aw1+"px"});$(this).parents(".bootstrap-select").find("span.filter-option").css({"white-space":"normal"});}
var ttl=$(this).attr("title");if(typeof(ttl)!="undefined"&&$.trim(ttl)!=""){$(this).parents(".bootstrap-select").find("button").attr("title",ttl);}});}}
function setProcessImg(f,id,msg,t,lft,delay){var o=null;if(o==null){var ogsc=get_src_center();var left=ogsc.left-100;var top=ogsc.top-100;o={'top':top,'left':left};id="";}
if(f==0){if(typeof(id)!="undefined"&&$("#div_loading_image.prcsing"+id+"").length>0){$("#div_loading_image.prcsing"+id+"").hide().remove();}else{$("#div_loading_image").hide().remove();}
return;}else if(f==1){if(o!=null){if($("#div_loading_image.prcsing"+id+"").length<=0){if(typeof(msg)!="undefined"&&msg!=""){msg="<div id=\"div_loading_text\" class=\"text-info\">"+msg+"</div>";}else{msg="";}
var prsc="<div id=\"div_loading_image\" class=\"text-center prcsing"+id+"\">"+"<div class=\"loading_container\">"+"<div class=\"process_loader\"></div>"+""+msg+""+"</div>"+"</div>";$("body").append(prsc);$("#div_loading_image.prcsing"+id+"").css({"left":lft+"px","top":t+"px","position":"fixed","z-index":"1000000"});if(typeof(delay)!="undefined"&&delay!=""&&!isNaN(delay)){$("#div_loading_image.prcsing"+id+"").delay(delay).fadeOut(400);setTimeout(function(){$("#div_loading_image.prcsing"+id+"").hide().remove();},parseInt(delay)+400);}}}}}
function displayConfirmYesNo_v2(title,msg,btn1,btn2,func,showCancel,showImage,misc){if(typeof(func)=="undefined"){func="";}
if((typeof(showImage)=="undefined")||(showImage!=0)){showImage="<span class=\"ui-icon ui-icon-alert\" >";}else{showImage="";}
var htm=""+"<div id=\"dialog-confirm\" title=\""+title+"\">"+"<p>"+showImage+"</span>"+msg+"</p>"+"</div>";$("#dialog-confirm").remove();$("body").append(htm);var btn=[];if(typeof(btn1)!="undefined"&&btn1!=""&&btn1!="0"){btn[btn.length]={text:""+btn1,'class':"ui-btn-sucus",click:function(){if(func!=""){eval(""+func+"(1);");};$(this).dialog("close");$("#dialog-confirm").remove();}};}
if(typeof(btn2)!="undefined"&&btn2!=""&&btn2!="0"){btn[btn.length]={text:""+btn2,'class':"ui-btn-sucus",click:function(){if(func!=""){eval(""+func+"(0);");};$(this).dialog("close");$("#dialog-confirm").remove();}};}
if((typeof showCancel=="undefined")||(showCancel!=0)){btn[btn.length]={text:"Cancel",'class':"ui-btn-cncl",click:function(){if(func!=""){eval(""+func+"(-1);");};$(this).dialog("close");$("#dialog-confirm").remove();}};}
$("#dialog-confirm").dialog({resizable:false,height:"auto",width:400,modal:true,buttons:btn});}
function open_print_window(resp){var parWidth=595;var parHeight=841;var printOptionStyle;printOptionStyle='p';window.open(''+zPath+'/../library/html_to_pdf/createPdf.php?op='+printOptionStyle+'&file_location='+resp+'','_parent','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+'');}
function hide_modal(id){$("#"+id).modal('hide');setTimeout(function(){$("#"+id+", .modal-backdrop").remove();},"500");}
function renew_title(o,s){if($.trim(s)==""){$(o).attr('title',"").tooltip('disable');}else{$(o).tooltip('enable');$(o).attr('title',""+s).tooltip('fixTitle').tooltip('setContent');}}
function single_chkbx(ob){var nm=ob.name;var o=document.getElementsByName(nm);var ln=o.length;for(i=0;i<ln;i++){if(o[i].id!=ob.id){o[i].checked=false;}}}
var flash_msg_c=0;function flash_msg(msg,type){if(typeof(msg)!="undefined"&&msg!=""){var tp=10+(10*flash_msg_c);var htm='<div id="flash_msg_c'+flash_msg_c+'" style="position:fixed;z-index:1000;top:'+tp+'%;right:0%;min-width:200px;min-height:20px;" class="flsh alert alert-'+type+'">'+''+msg+'</div>';$("body").append(htm);setTimeout(function(){$('.flsh').fadeOut('slow');if(flash_msg_c>0){flash_msg_c=flash_msg_c-1;}},1000);flash_msg_c=flash_msg_c+1;}}