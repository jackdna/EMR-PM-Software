//--AP Functions --
function fill_orders(){
	
	for(order_id in order_type){
		order_type_name = order_type[order_id].replace(/[^A-Za-z0-9\-]/,'_');
		arr_order_type_name = new Array();
		$('#ele_order_'+order_type_name+' option').each(function(){ 
		 	id = $(this).val();
		 	arr_order_type_name[id] = $(this).text(); 
		});
		//---BEGIN SET ALL OPTIONS FOR ORDERS-------
		var all_orders_tmp = "all_orders_option_"+order_type_name.replace(/[^A-Za-z0-9\-]/,'_');
		if(typeof window[all_orders_tmp] != "undefined"){
			var all_orders = eval("all_orders_option_"+order_type_name.replace(/[^A-Za-z0-9\-]/,'_'));
			o_all = document.getElementById("all_"+order_type_name);
			if(o_all) {
			o_all.options.length = 0;
			for(var i in all_orders){
				var option = document.createElement("option");
				option.text = all_orders[i].name;
				option.value = all_orders[i].id;
				//option.selected = 'selected';
				if(typeof(arr_order_type_name[option.value])=="undefined")
				o_all.appendChild(option);
			}
			}
		}
		//---END SET ALL OPTIONS FOR ORDERS-------
	}
	//---BEGIN SET ALL OPTIONS FOR ORDER SET-------
	arr_order_sets = new Array();
	$('#ele_order_orderset option').each(function(){ 
		id = $(this).val();
		arr_order_sets[id] = $(this).text(); 
	});
	o_orderset_all = document.getElementById("all_orderset");
	o_orderset_all.options.length = 0;
	if(typeof all_orderset_options != "undefined" && typeof all_orderset_options != "null" ){
		for(var i in all_orderset_options){
			var option = document.createElement("option");
			option.text = all_orderset_options[i].name;
			option.value = all_orderset_options[i].id;
			//option.selected = 'selected';
			if(typeof(arr_order_sets[option.value])=="undefined")
			o_orderset_all.appendChild(option);
		}
	}
	//---END SET ALL OPTIONS FOR ORDER SET-------
}
function anp_sort_sel(sid) {
	//sort
	var opt = $("#" + sid + " option").sort(function (a, b) {
		return a.innerHTML.toUpperCase().localeCompare(b.innerHTML.toUpperCase())
	});
	$("#" + sid).append(opt);
}
function popup_dbl_Meds(divid, sourceid, destinationid, act, odiv) {
	if (act == "single" || act == "all") {
		var ptrn = "";
		if (act == 'single') {
			//$("#"+sourceid+" option:selected").appendTo("#"+destinationid);
			ptrn = "#dvordermedlist :checked[type=checkbox], .relorders :checked[type=checkbox]";
		} else if (act == "all") {
			//$("#"+sourceid+" option").appendTo("#"+destinationid);				
			ptrn = "#dvordermedlist input[type=checkbox], .relorders  input[type=checkbox]";
		}
		if (ptrn != "") {
			var chk_arr = [];
			$("#" + destinationid + " option").each(function () {
				chk_arr[chk_arr.length] = "" + this.value;
			});

			//$("#"+destinationid).html("");
			var stro = "";
			$(ptrn).each(function () {
				var id = this.value;
				if (chk_arr.indexOf(id) != -1 || this.id.indexOf("master") != -1) { /*donothing*/
				} else {
					chk_arr[chk_arr.length] = id;
					var txt = $("label[for='" + this.id + "']").html();
					stro += "<option value=\"" + id + "\">" + txt + "</option>";
				}
			});
			$(stro).appendTo("#" + destinationid);

			//sort
			anp_sort_sel(destinationid);
		}

	} else if (act == "single_remove" || act == "all_remove") {
		//if(act=="single_remove"){$("#"+sourceid+"  option:selected").appendTo("#"+destinationid);}
		//if(act=="all_remove")	{$("#"+sourceid+"  option").appendTo("#"+destinationid);}
		if (act == "single_remove") {
			$("#" + sourceid + "  option:selected").remove();
		}
		if (act == "all_remove") {
			$("#" + sourceid + "  option").remove();
		}

	} else {
		$("#" + destinationid + " option").remove();
		$("#" + odiv + " option").clone().appendTo("#" + destinationid);
		//sort
		anp_sort_sel(destinationid);
		$("#" + divid).show("clip");
	}
}

function popup_dbl(divid, sourceid, destinationid, act, odiv) {
	if(divid.indexOf("Meds")!=-1){ popup_dbl_Meds(divid, sourceid, destinationid, act, odiv); return;}
	if (act == "single" || act == "all") {
		if (act == 'single') {
			$("#" + sourceid + " option:selected").appendTo("#" + destinationid);
		} else if (act == "all") {
			$("#" + sourceid + " option").appendTo("#" + destinationid);
		}
	} else if (act == "single_remove" || act == "all_remove") {
		if (act == "single_remove") {
			$("#" + sourceid + "  option:selected").appendTo("#" + destinationid);
		}
		if (act == "all_remove") {
			$("#" + sourceid + "  option").appendTo("#" + destinationid);
		}
		$("#" + destinationid).append($("#" + destinationid + " option").remove().sort(function (a, b) {
			var at = $(a).text(), bt = $(b).text();
			return (at > bt) ? 1 : ((at < bt) ? -1 : 0);
		}));
		$("#" + destinationid).val('');
	} else {
		$("#" + destinationid + " option").remove();
		$("#" + odiv + " option").clone().appendTo("#" + destinationid);
		//sort
		anp_sort_sel(destinationid);
		$("#" + divid).show("clip");
	}
}
function selected_ele_close(divid, sourceid, destinationid, div_cover, action) {	
	if (action == "done") {		
		var sel_cnt = $("#" + sourceid + " option").length;
		$("#" + divid).hide("clip");
		$("#" + destinationid + " option").each(function () {
			$(this).remove();
		})
		$("#" + sourceid + " option").appendTo("#" + destinationid);
		$("#" + destinationid + " option").prop("selected", true);
		//$("#" + div_cover).width(parseInt($("#" + destinationid).width()) + 'px');
		if (sel_cnt > 8) {
			//$("#" + div_cover).width(parseInt($("#" + destinationid).width() - 15) + "px");
		}
	} else if (action == "close") {
		$("#" + divid).hide("clip");
	}
}

var menu_position = 1;

function myTrim(str)
{
	str = str.replace(/^\s+|\s+$/, '');
	return str;
}
function ap_hasOrders() {
	var flg = 0;
	$("select[id*=ele_order_]").each(function () {
		var t = $(this).val();
		if (t && t != "") {
			flg = 1;
		}
	});
	return flg;
}

function to_do2(val, stat) {
	if (stat == "no") {
		stat = "yes";
		location.href = "console_to_do.php?todoid=" + val + "&status=" + stat;
	} else {
		stat = "no";
		location.href = "console_to_do.php?todoid=" + val + "&status=" + stat;
	}
}

function checkDesc(obj) {
	var temp = document.getElementById("elem_assocDx");
	var temp = document.getElementById("elem_assocDx10");
	
	
	/*
	temp.value = ""; //Empty fields
	var strVal_f = myTrim(obj.value);
	var arr_strVal = strVal_f.split("-");
	strVal = $.trim(arr_strVal[0]);
	var len = arrTHDesc2.length;
	for (var i = 0; i < len; i++) {
		if (myTrim(arrTHDesc2[i].toLowerCase()) == strVal.toLowerCase()) {
			if (temp != null) {
				var temp_val = (typeof (arrTHPracCode[i]) == "undefined") ? "" : arrTHPracCode[i];
				temp.value = "" + temp_val;
			}
			break;
		}
	}

	var temp = document.getElementById("elem_assocDx10");
	temp.value = ""; //Empty fields
	var strVal_f = myTrim(obj.value);
	var arr_strVal = strVal_f.split("-");
	strVal = $.trim(arr_strVal[0]);
	var len = arrTHDesc2_10.length;
	for (var i = 0; i < len; i++) {
		if (myTrim(arrTHDesc2_10[i].toLowerCase()) == strVal.toLowerCase()) {
			if (temp != null) {
				temp.value = "" + arrTHPracCode10[i];
			}
			break;
		}
	}
	*/
}

function checkCode(obj) {
	checkDesc(obj);
	return 0;
}

function setConsoleDxCode(obj, mode) {
	//arrTHPracCode
	obj.value = $.trim(obj.value);
	if ((obj.value != "")) {
		/*
		var strRet = "";
		var strDx = "" + obj.value;
		var arr = new Array();
		if (strDx.indexOf(",") != -1) {
			arr = strDx.split(",");
		} else {
			arr[0] = strDx;
		}
		
		
		var len = arr.length;
		var lenPracCd = arrTHPracCode.length;
		for (var i = 0; i < len; i++) {
			for (j = 0; j < lenPracCd; j++) {
				var valIndx = myTrim(arr[i]);
				if (myTrim(valIndx) != "" && strRet.indexOf(valIndx) == -1 && arrTHPracCode[j] == valIndx) {
					strRet += ($.trim(strRet) != "") ? ", " + valIndx : "" + valIndx;
					break;
				}
			}
		}

		if (strRet != "") {
			obj.value = strRet+", ";
		} else {
		*/	
		
			var dx = obj.value;
			var dxId = $(obj).data("dxid");
		
			//search in db
			if (mode != "getDxCode10") {
				var mode = "getDxCode";
			}
			var url = zPath+"/physician_console/anp_policies.php";
			var parms = "mode=" + mode + "&";
			parms += "elem_chkDx=" + dx + "&";
			parms += "elem_chkDxId=" + dxId + "&";

			$.get("" + url, "" + parms, function (data) {
				if (data && data.dxCode && data.dxCode != "") {
					obj.value = data.dxCode+",";
					if(mode == "getDxCode10"){$(obj).data("dxid",data.dxCodeId+",");}
				} else {
					//alert("Please Enter valid Dx Code.");
					obj.value = "";
					if(mode == "getDxCode10"){$(obj).data("dxid","");}
				}
			}, "json");
		//}
	}
}

function setConsoleCptCode(obj, id) {
	if ((obj.value != "")) {
		//var url = "../chart_notes/getSuperBillInfo.php";
		var url = zPath+"/physician_console/anp_policies.php";
		params = "elem_formAction=setConsoleCptCode";
		params += "&elem_strCptCode=" + obj.value;

		$.post(url, params,
				function (data) {
					if (data != "") {
						obj.value = "" + data;
					} else {
						alert("Please Enter valid CPT Code.");
						obj.value = "";
					}
				});
	}
}

function addConsoleDxCode(obj, id) {
	if (obj.value != "") {
		var objStr = gebi("" + id);
		var strDx = objStr.value;
		var curVal = myTrim(obj.value);
		if (curVal != "") {
			strDx += (myTrim(strDx) != "") ? ", " + curVal : "" + curVal;
		}
		objStr.value = strDx;
		objStr.onchange();
	}
}

function addConsoleCptCode(obj, id) {
	//
	var cptCd = myTrim(obj.value);
	var cptCdFull = myTrim(obj.value);
	if ((obj.value != "") && (cptCd.length >= 3)) {
		var indx = cptCd.lastIndexOf("~!~");
		cptCd = (indx != -1) ? myTrim(cptCd.substring(0, indx)) : cptCd;
	}

	//
	if (cptCd != "") {
		var objStr = gebi("" + id);
		var strCpt = objStr.value;
		var curVal = myTrim(cptCd);
		if (curVal != "") {
			strCpt += (myTrim(strCpt) != "") ? ", " + curVal : "" + curVal;
		}
		objStr.value = strCpt;
		objStr.onchange();
	}
}

function cancelForm() {
	window.location.href = 'console_to_do.php';
}

///order meds
var showrelorders_flg;
function showrelorders(id, flg, obj) {
	if (flg == 3) {
		clearTimeout(showrelorders_flg);
		showrelorders_flg = null;
	} else if (flg == 2) {
		if (showrelorders_flg) {
			$("#dv_relorders" + id).hide();
			clearTimeout(showrelorders_flg);
			showrelorders_flg = null;
		}
	} else if (flg == 1) {

		//alert(window.event.pageX+" - "+window.event.pageY+" - "+$("#dvordermedlist").scrollTop());
		var lft = 100;//parseInt(window.event.pageX)+"px";
		var tp = 100;//parseInt(window.event.pageY);		
		$(".relorders").not("#dv_relorders" + id).hide();
		var dd = $(obj).position();
		//alert(dd.top+" - "+dd.left);
		lft = "" + dd.left;
		tp = "" + dd.top;
		//alert("11");
		$("#dv_relorders" + id).show();
		$("#dv_relorders" + id).css({"left": lft + "px", "top": tp + "px","z-index":"1030"});
		//$("#dv_relorders"+id).show().position({my: "left top",  at: "left top",  of: window.event});//,  within: "#pop_up_Meds"
	} else {
		if (!showrelorders_flg) {
			showrelorders_flg = setTimeout(function () {
				showrelorders(id, 2, obj);
			}, 500);
		}
	}
}

function checkrelorders(obj) {
	$("#dv_relorders" + obj.value + " input").each(function () {
		this.checked = (obj.checked) ? true : false;
	});
}

function checkSevLocOpts(o, f, sv, lc,rem) {
	if(typeof(rem)!="undefined" && rem!=""){ $("#"+rem).val(''); return;	}	
	if (f == 1) {
		$("#severity,#location").html("<option value=\"\">Please Select</option>");
		return;
	}
	
	var a = $.trim(o.value);
	if (a != "") {		
		// check finding		
		if(arrTHSym.indexOf(a)==-1){
			msg = "Entered Finding does not match with existing \"Finding\", Do you want to continue?";					
			var tt = (window.top.fmain) ? "window.top.fmain" : "top";	
			tt = ""+tt+".checkSevLocOpts('', '', '', '','"+o.id+"');";	
			top.fancyConfirm(msg, "", "", ""+tt);
			return;
		}
		// check finding
		
		$("#innerLoader").show();
		$.get(zPath+"/physician_console/anp_policies.php?finding=" + a, function (data) {
			$("#innerLoader").hide();
			if (data) {
				if (data.Severity && data.Severity.length > 0) {
					var opt = "";
					for (var x in data.Severity) {
						var sel = (typeof (sv) != "undefined" && sv != "" && (sv + ",").indexOf(data.Severity[x] + ",") != -1) ? "selected" : "";
						opt += "<option value=\"" + data.Severity[x] + "\" " + sel + ">" + data.Severity[x] + "</option>";
					}
					$("#severity").html("" + opt);
					$('.selectpicker').selectpicker('refresh');
				}
				/*
				 if(data.Location&&data.Location.length>0){ 									
				 var opt="";	for(var x in data.Location){	
				 var sel = (typeof(lc)!="undefined"&&lc!=""&&lc==data.Location[x]) ? "selected" : "";
				 opt+="<option value=\""+data.Location[x]+"\" "+sel+" >"+data.Location[x]+"</option>";	
				 }
				 $("#location").html("<option value=\"\"></option>"+opt);
				 }
				 */

				//Multi select			
				//	$("#severity").multipleSelect({'width':'140px','allSelected':false, 'countSelected':false });

			}
		}, "json");
	}
}

//-- AP Functions --


function load_main(p,f,s,so,currLink){//p=practice code, f=fac code, s=status, so=sort by;
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Loading AP Policies...');
	
	if(typeof(s)!='string' || s==''){s = 'Active';}
	s_url = "&s="+s;
	
	if(typeof(p)=='undefined'){p_url='';}else{p_url='&p='+p};
	if(typeof(f)=='undefined'){f_url='';}else{f_url='&f='+f};
	
	oso		= $('#ord_by_field').val(); //old_so
	soAD	= $('#ord_by_ascdesc').val();
	if(typeof(so)=='undefined' || so==''){
		so 		= $('#ord_by_field').val();
	}else{
		$('#ord_by_field').val(so);
		if(oso==so){
			if(soAD=='ASC') soAD = 'DESC';
			else  soAD = 'ASC';
		}else{
			soAD = 'ASC';
		}
		$('#ord_by_ascdesc').val(soAD);
	};
	//so 		= 'pos_prac_code';
	$('thead span').removeClass('glyphicon glyphicon-chevron-up glyphicon-chevron-down');		
	
	if($(currLink).find('span').length==0){ $(currLink).append(' <span></span>'); }
	if(soAD=='ASC')	$(currLink).find('span').addClass('glyphicon glyphicon-chevron-up');
	else $(currLink).find('span').addClass('glyphicon glyphicon-chevron-down');
	
	var so_url='&so='+so+'&soAD='+soAD;		
	var ajaxURL = "ajax.php?aptask=show_list"+s_url+p_url+f_url+so_url;	
	$.get(ajaxURL,function(d){$("#result_set").html(d); top.show_loading_image('hide');});
	
}

//------------

//type ahead fu
function cn_ta_fu() {
	var ta7 = function (ota7) {
		$(ota7).autocomplete({
			source: "common/requestHandler.php?elem_formAction=TypeAhead&mode=FUVisit",
			minLength: 1,
			autoFocus: true,
			focus: function (event, ui) {
				return false;
			},
			select: function (event, ui) {
				if (ui.item.value != "") {
					this.value = "" + ui.item.value;
				}
			}
		});
	};
	$("#listFU input[type=text][id*=elem_followUpVistType_]").bind("focus", function (event) {
		if (!$(this).hasClass("ui-autocomplete-input")) {
			ta7(this);
		}
		;
	});
}



function changeOther(obj, val) {
	if ((obj.value == 'Other') || (typeof val != 'undefined')) {
		val = (typeof val == 'undefined') ? "" : val;
	
		var idOthr = obj.id.replace(/elem_followUpVistType/g, "elem_followUpVistTypeOther");
		//var idObjDd = obj.id.replace(/elem_followUpVistType/g,"sp_followUpVistTypeMenu");			
		var idOthrIcon = obj.id.replace(/elem_followUpVistType/g, "sp_fu_vis_other");

		var oOthr = gebi(idOthr);
		//var oObjDd = gebi(idObjDd);		
		var oOthrIcon = gebi(idOthrIcon);
		if (oOthr && oOthrIcon) {
			oOthr.value = "1";
			obj.value = "";
			obj.focus();
			//oObjDd.style.visibility = "hidden";
			oOthrIcon.style.visibility = "visible";
		}
	}
}

//Follow up
//Add
function fu_add(flg) {
	var otbl = $("#listFU");
	var len = otbl.children().length;
	var iter = $("#listFU").attr("data-cntrfu");
	iter = parseInt(iter) + 1;
	$("#listFU").attr("data-cntrfu", iter);

	//Coords --
	var vCords1 = $("#listFU").attr("data-fuCntr1");
	var vCords2 = $("#listFU").attr("data-fuCntr2");

	//FU Pro DD --
	var fuProDD = "";
	if ($("#listFU li select[id*='elem_fuProName_']").length > 0) {
		fuProDD = "<select name=\"elem_fuProName[]\" id=\"elem_fuProName_" + iter + "\" " +
				" onmouseover=\"if(this.selectedIndex)this.title=this.options[this.selectedIndex].text+'-'+this.value;\" >";
		fuProDD += $("#listFU li select[id*='elem_fuProName_']").eq(0).html();
		fuProDD += "</select> ";
	}

	var numMenu = menu1.text();
	numMenu = numMenu.replace(/#dynID#/g, iter);

	var visitMenu = menu2.text();
	visitMenu = visitMenu.replace(/#dynID#/g, iter);
	//FU Pro DD --

	var str = "<div class=\"row pt10\"><div class=\"col-sm-3\">" +
			"<div class='input-group'><input type=\"text\" class=\"form-control\" name=\"elem_followUpNumber[]\" id=\"elem_followUpNumber_" + iter + "\" value=\"\"" +
			" onchange=\"fu_refineNum(this)\" >" + numMenu + "</div></div>" +
			//""+getSimpleMenuJs("elem_followUpNumber_"+iter,"menu_FuNum",imgPath,1,0,"divWorkView",vCords1,1)+" "+
			//Select of Time
			"<div class=\"col-sm-3\"><select name=\"elem_followUp[]\" id=\"elem_followUp_" + iter + "\" onchange=\"fu_move(this)\" class=\"form-control minimal\">" +
			"<option value=\"\"></option>" +
			"<option value=\"Days\" >Days</option>" +
			"<option value=\"Weeks\" >Weeks</option>" +
			"<option value=\"Months\" >Months</option>" +
			"<option value=\"Year\" >Year</option>" +
			"</select> </div>" +
			"<div class=\"col-sm-3\"><div class='input-group'><input type=\"text\" name=\"elem_followUpVistType[]\" id=\"elem_followUpVistType_" + iter + "\" value=\"\" onchange=\"changeOther(this);\" class=\"form-control\">" +
			visitMenu + "</div>" +
//""+getSimpleMenuJs("elem_followUpVistType_"+iter,"menu_FuOptions",imgPath,0,0,"divWorkView",vCords2)+" "+
			"<input type=\"hidden\" name=\"elem_followUpVistTypeOther[]\" id=\"elem_followUpVistTypeOther_" + iter + "\" value=\"\"></div>" +
			fuProDD +
			"<div class=\"col-sm-3 pdl_10\"><span class=\"pdl_10 pt5 spnFuDel glyphicon glyphicon-remove link_cursor\" title=\"Remove F/U\" onclick=\"fu_del('" + iter + "');\"></span></div>" +
			"</div>";
	otbl.append(str);
	otbl.attr("data-cntrfu", iter);

	//FU Pro DD: --
	if (fuProDD != "") {
		var tmp = alwaysDocFU;
		if (typeof (alwaysDocFU) == 'undefined' || alwaysDocFU == '') {
			if (typeof (ssFollowPhy) != "undefined" && ssFollowPhy != "") {
				tmp = ssFollowPhy;
			} else if ($(":input[name*=elem_physicianId][value!=''][type!=text]").length > 0) {
				tmp = "" + $(":input[name*=elem_physicianId][value!=''][type!=text]").val();
			}
		}
		$("#elem_fuProName_" + iter).val(tmp);
	}
	//FU Pro DD --

	//Fu visit
	cn_ta_fu();

	if (flg == 1) {
		return iter;
	}

}

function fu_del(itr) {

	if (itr > 1) {
		$("#listFU>div:has(#elem_followUp_" + itr + ")").remove();
	} else {
		$("#listFU>div:has(#elem_followUp_" + itr + ") :input").val("");
	}

	/*
	 var tbl = gebi("tblFU");
	 
	 if(typeof itr != "undefined"){			
	 var id = "td6_fuId_"+itr;
	 }else{
	 var id = this.id;		
	 }
	 
	 var iter = ""+id.replace(/td6_fuId_/g,"");
	 var orows = tbl.rows;
	 var len = orows.length;
	 
	 for(var i=0;i<len;i++){
	 if(orows[i].id == "tr_fuId_"+iter){
	 iter = i;
	 break;	
	 }
	 }				
	 
	 //top.fAlert("CHK1: "+iter);
	 
	 if(iter > 0){
	 //top.fAlert("CHK: "+iter);
	 iter = parseInt(iter);
	 tbl.deleteRow(iter);
	 }
	 */

}

function fu_refineNum(obj) {
	var arr = obj.value.split(",");
	var len = arr.length;
	var str = "";
	var tmp = "";
	var flgMv = 0;
	if (len > 1) {
		tmp = arr[0];
		if (tmp == "-") {
			if (arr[1].indexOf("-") == -1)
				str = arr[1] + tmp;
		} else {
			var ar2 = arr[1].split("-");
			if (typeof ar2[1] == "string")
				ar2[1] = myTrim(ar2[1]);
			if (ar2.length == 2 && typeof ar2[1] != "undefined" && ar2[1] == "") {
				str = arr[1] + tmp;
				flgMv = 1;
			} else {
				str = tmp;
			}
		}
	} else {
		str = obj.value;
	}

	obj.value = "" + myTrim(str);

	//Enable Days, Weeks, Month and Year drop down.
	var oSel = null;
	if (obj.id) {
		oSel = gebi(obj.id.replace(/elem_followUpNumber_/, "elem_followUp_"));
		if (oSel) {
			oSel.disabled = false;
		}
	}

	if (Date.parse(obj.value) && !$.isNumeric(obj.value)) {
		oSel.disabled = true;
	} else {
		oSel.disabled = false;
	}

	//Calendar
	if (obj.value.indexOf("Calendar") != -1) {
		obj.value = "";
		
		var date_global_format = 'm-d-Y';
		$("#"+ obj.id).datetimepicker({
			timepicker: false,
			format: date_global_format,
			formatDate: 'Y-m-d',
			scrollInput: false,
		});
		$("#"+ obj.id).datetimepicker('show');
		
		//Disable Days, Weeks, Month and Year drop down.			
		if (oSel) {
			oSel.disabled = true;
		}
		//Open Calender Pop up
		//newWindow("" + obj.id);
		return false;
	} else {
		$("#"+ obj.id).datetimepicker('destroy');
	}
	//

	//Move
	if (flgMv == 1) {
		fu_move(obj);
	}
}

function fu_move(obj) {
	var id = obj.id;
	var t = null;
	if (id.indexOf("elem_followUpNumber") != -1) {
		t = gebi(id.replace(/elem_followUpNumber/, "elem_followUp"));
	} else if (id.indexOf("elem_followUp") != -1) {
		t = gebi(id.replace(/elem_followUp/, "elem_followUpVistType"));
	}
	if ((obj.value != "") && (t != null)) {
		t.focus();
	}
}

function fillEditData(pkId){	
	if(typeof(pkId)=='undefined' || pkId==""){return;}
	var formObjects		   = new Array('to_do_id','procedure_name');
	var arrAllShownRecords = [];			
	$.get("ajax.php?aptask=get_edit_val&eid="+pkId, function(d){
		arrAllShownRecords=d;
		//console.log(arrAllShownRecords);
	
		f = document.add_edit_frm;
		e = f.elements;
		add_edit_frm.reset();
		$('#to_do_id').val(pkId);
		
		var fu_innerHTML="";
		for(i=0;i<e.length;i++){
			o = e[i];
			if($.inArray(o.phrase,formObjects)){
				on	= o.name;				
				v	= arrAllShownRecords[on];
				if (o.tagName == "INPUT" || o.tagName == "SELECT" || o.tagName == "TEXTAREA"){
					if(o.type == "select-multiple"){
					  if(v != "undefined" && v != null)o.options.length = 0;
						  for(var j in v){
								var option = document.createElement("option");
								if(typeof v[j].name !="undefined"){
									option.text = v[j].name;
								}else{
									option.text = v[order_id];
								}
								if(typeof v[j].id !="undefined"){
									option.value = v[j].id;
								}else{
									option.value = order_id;
								}
								option.selected = 'selected';
								o.appendChild(option);
							}	
						 /* for(j in v){
								var option = document.createElement("option");
								option.text = v[j].name;
								option.value = v[j].id;
								option.selected = 'selected';
								o.appendChild(option);
						  }*/
						/**/
					}
					else if ((o.type == "checkbox") || o.type == "radio"){
						var ff = ((o.type == "checkbox" && on.indexOf("[]")==-1)||o.type == "radio") ? 1 : 0;
						if(ff==1){	oid = on+'_'+v;$('#'+oid).prop('checked',true);}
					} else if(o.type!='submit' && o.type!='button'){
						if( typeof v !== 'undefined'){
							o.value = v;
							if(o.name == "elem_assocDx10"){
								var dxid = arrAllShownRecords[o.name+"Id"];
								if(typeof(dxid)!="undefined" && dxid!=""){
									$(o).data("dxid", dxid);	
								}
							}
						}
					}
				}
			}
		}		
		
		/*
		$("#listFU").html("<div class='doing'></div>");
		
		$.ajax({
			type: "POST",
			url: "ajax.php?aptask=get_fu&elem_Followup="+escape(elem_Followup),
			success: function(r) {				
				$("#listFU").html(""+r);
			}
		});
		*/
		elem_Followup = arrAllShownRecords['xmlFU'];
		ap_set_fu(elem_Followup);
		setTimeout(function(){setTaPlanHgt();}, 100);
		//fill severity and location
		checkSevLocOpts($("#task")[0],0,arrAllShownRecords['severity'],arrAllShownRecords['location']);
		//$('#fu_inner').html(fu_innerHTML);	
		
	}, 'json');
	
	
}

function ap_set_fu(elem_Followup){	
	$("#listFU").html("<div class='doing'></div>");	
	$.ajax({
		type: "POST",
		url: "ajax.php?aptask=get_fu&elem_Followup="+escape(elem_Followup),
		success: function(r) {				
			$("#listFU").html(""+r);
		}
	});
}

//------------

function addNew(ed,pkId){
	document.add_edit_frm.reset(); 	
	if((typeof(ed)!='undefined' && ed!='') && (typeof(pkId)!='undefined' && pkId>0)){
		fillEditData(pkId);		
	}else{
		$('#to_do_id').val("");
		fill_orders();
		document.getElementById("ele_order_orderset").options.length = 0;
		for(order_id in order_type){
			order_type_name = order_type[order_id].replace(/[^A-Za-z0-9\-]/,'_');
			$('#ele_order_'+order_type_name+' option').remove();
		}
		ap_set_fu('');
		setTimeout(function(){setTaPlanHgt();}, 100);
		
	}
	$('#myModal').modal('show'); 
}

function dis_menu(f){		
	$("input[name=menuOptionValue], input[name=elemTargetName], input[name=elemMenuMulti]").prop("disabled", f);
}
	
function saveFormData(){
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Saving data...');
	//
	dis_menu(true);
	
	frm_data = $('#add_edit_frm').serialize()+'&aptask=save_update';
	var msg="";
	if($.trim($('#elem_assessment').val())==""){
		msg+=" - Enter the Assessment<br>";
	}				
	var fo = ap_hasOrders();				
	if($.trim($('#plan').val())==""&&fo==0){
		msg+=" - Enter the Plan/Order";
	}	
	if(msg){
		top.fAlert(msg);
		top.show_loading_image('hide');
		return false;
	}
	
	var sev_v = fun_mselect('#severity', "val"); //$('#severity').multipleSelect('getSelects');
	if(typeof(sev_v)!="undefined" && sev_v!=""){	sev_v = encodeURIComponent(sev_v); 	}	
	frm_data +="&elem_severity="+sev_v;	
	//
	var xx = ['ele_order_Meds','ele_order_Labs','ele_order_Imaging_Rad','ele_order_Procedure_Sx','ele_order_Information_Instructions','ele_order_orderset'];
	for(var x=0;x<xx.length;x++){
		var fo = []; 
		$('#'+xx[x]+' :selected').each(function(i, selected){ 
		  fo[i] = $(selected).val(); 
		});
		var sfo = fo.join(',');
		sfo = sfo || "";
		frm_data +="&"+xx[x]+"_x="+encodeURIComponent(sfo);
	}

	//ICD10	
	var dx10 = $("#elem_assocDx10").val();
	var dx10Id = $("#elem_assocDx10").data("dxid");
	if(typeof(dx10Id)=="undefined"){ dx10Id=""; }
	frm_data +="&elem_aDx10id="+$.trim(dx10Id);	
	
	$.ajax({
		type: "POST",
		url: "ajax.php",
		data: frm_data,
		success: function(d) {
			//console.log(d);
			top.show_loading_image('hide');
			if(d=='enter_unique'){
				top.fAlert('Record already exist.');		
				return false;
			}
			if(d.toLowerCase().indexOf('success') > 0){
				top.alert_notification_show(d);
			}else{
				top.fAlert(d);
			}
			document.add_edit_frm.reset();
			$('.closeBtn').click();
			load_main();
			dis_menu(false);
		}
	});
}
	
//--
function deleteSelectet(){
	var pos_id = '';
	$('.chk_sel').each(function(){
		if($(this).prop('checked')){
			pos_id += $(this).val()+', ';
		}
	})
	if(pos_id!=''){
		top.fancyConfirm("Are you sure you want to delete?","", "window.top.fmain.deleteModifiers('"+pos_id+"')");
	}else{
		top.fAlert('No Record Selected.');
	}
}
function deleteModifiers(pos_id) {
	pos_id = pos_id.substr(0,pos_id.length-2);
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Deleting Record(s)...');
	var frm_data = 'pkId='+pos_id+'&aptask=delete';	
	$.ajax({
		type: "POST",
		url: "ajax.php",
		data: frm_data,
		success: function(d) {
			top.show_loading_image('hide');
			if(d=='1'){top.alert_notification_show('Record Deleted'); load_main();}
			else{top.fAlert(d+'Record delete failed. Please try again.');}
		}
	});
}
//--
	
	
//Settings --	
function settingsAP(){	
	$.ajax({
		type: "POST",
		url: "ajax.php?aptask=getSettings",
		success: function(r) {			
			$("#elem_eCAWV").prop("checked", function( i, t ){ return (r.indexOf(this.value)!==-1) ? true : false ;  }); 
			$("#elem_eDAWV").prop("checked", function( i, t ){ return (r.indexOf(this.value)!==-1) ? true : false ;  });
			$('#settingsModal').modal('show');	
		}
	});		
}

function saveFormDataSettings(){
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Saving data...');
	frm_data = $('#frmset').serialize()+'&aptask=saveSettings';
	$.ajax({
		type: "POST",
		url: "ajax.php",
		data: frm_data,
		success: function(d) {
			top.show_loading_image('hide');						
			if(d.toLowerCase().indexOf('success') > 0){
				top.alert_notification_show(d);
			}else{
				top.fAlert(d);
			}
			$('.closeBtn').click();						
		}
	});
	
}

function admin_ap_typeahead(){
	$('#elem_assocDx10, #elem_assocDx').autocomplete({			
			source: function( request, response ) {				
				var icd = (this.element[0].id.indexOf("10") == -1) ? 9 : 1;
			    $.getJSON( zPath+"/chart_notes/requestHandler.php?elem_formAction=TypeAhead&mode=get_dx_data&ICD10="+icd+"&req_ptwo=1", {
				term: $.trim(request.term) 				    
			    }, response );
			},
			search: function() {
			    // custom minLength
			    var term = $.trim(this.value);			   
			},
			 minLength: 2,
			 autoFocus: true,	
			 focus: function( event, ui ) {return false;},		
			 select: function( event, ui ) { if(ui.item.value!=""){
					var t = this.value ;
					var s = $(this).data("dxid");
					if(typeof(t)!="undefined" && t!=""){						
						if(t.indexOf(",")!=-1){
							var ar = t.split(",");
							var ar_s = (typeof(s)!="undefined" && s!="") ? s.split(",") : [] ; 
							if(ar.length>1){
								ar.length = ar.length-1;
								var t1 ="", s1 ="" ;
								for(var z in ar){
									if(typeof(ar[z])!="undefined" && $.trim(ar[z])!=""){
										t1 += ar[z]+",";
										s1 += (typeof(ar_s[z])!="undefined" && $.trim(ar_s[z])!="") ? ar_s[z]+"," : ",";
									}
								}
								
								//t = ar.join(","); 
								//t=t+",";
								
								t = t1;
								s = s1;
								
							}else{ t=""; s=""; }
						}else{ t=""; s=""; }
					}					
					
					var r = ""+ui.item.value;
					var rid = ""+ui.item.id;	
					t =  $.trim(""+t+""+r+",");
					s =  $.trim(""+s+""+rid+",");
					//t=""+t.replace(/^,/,"");
					this.value = t;
					$(this).data("dxid", s);    
					$(this).triggerHandler("change");
					$(this).triggerHandler("blur");
					$(this).triggerHandler("click");
					    
					return false;
				} }
		});
			
}

//---

//--

function setTaPlanHgt(){
	//stop in safari
	if(navigator.userAgent.indexOf("Safari") > -1 && navigator.userAgent.indexOf("Chrome") == -1){return;}
	$("#plan").attr("style", "height: 150px !important");
	var x = $("#plan")[0].scrollHeight;	
	var y = (typeof(x)!="undefined" && x>150) ? x : 150;	
	$("#plan").attr("style", "height: "+y+"px !important");
}
//--


var ar = [["add_new","Add New","top.fmain.addNew();"],		
		["del","Delete","top.fmain.deleteSelectet();"],
		["settings","Settings","top.fmain.settingsAP();"],
		];

$(document).ready(function(){
	set_header_title('AP Policies');
	load_main();
	check_checkboxes();
	top.btn_show("ADMN",ar);
	
	//TA--
	//$('#elem_assocDx').typeahead({source: tmpTHDx});
	//$('#elem_assocDx10').typeahead({source: tmpTHDx10});
	admin_ap_typeahead();
	
	
	//$('#task').typeahead({source: arrTHSym});
	$('#task').autocomplete({source:arrTHSym, 
				minLength: 2,
				focus: function (event, ui) {
					return false;
				},
				select: function (event, ui) {
					if (ui.item.value != "") {
						var ar = ui.item.value;						
						this.value = "" + ar;
						this.onblur();
					}
					return false;
				}
			});

	
	$("#elem_assessment").autocomplete({
		source: zPath+"/physician_console/anp_policies.php?mode=getICD10Data",
		minLength: 2,
		focus: function (event, ui) {
			return false;
		},
		select: function (event, ui) {
			if (ui.item.value != "") {
				var ar = ui.item.value.split("[ICD-10:");
				var ard = $.trim(ar[0]);
				this.value = "" + ard;
				var dxid = ui.item.id;
				var ardx = ar[1].split("ICD-9:");
				var tdx = $.trim(ardx[0]);
				tdx = tdx.replace(",", ""); tdx=$.trim(tdx);
				if(typeof(tdx)!="undefined"){
					$("#elem_assocDx10").val(tdx).data("dxid", dxid);
				}
				//icd9
				var tdx = $.trim(ardx[1]);
				tdx = tdx.replace("]", ""); tdx=$.trim(tdx);
				if(typeof(tdx)!="undefined"){
					$("#elem_assocDx").val(tdx).data("dxid", dxid);
				}
				//this.onchange();
			}
			return false;
		}

	});
	
	top.show_loading_image('hide');
	
});