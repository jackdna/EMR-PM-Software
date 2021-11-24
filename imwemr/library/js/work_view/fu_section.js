	function fu_add(flg){
		var otbl = $("#listFU");
		var len = otbl.find(".fu").length;
		var iter = $("#listFU").attr("data-cntrfu");
		iter = parseInt(iter) + 1;

		//Coords --
		var vCords1 = $("#listFU").attr("data-fuCntr1");
		var vCords2 = $("#listFU").attr("data-fuCntr2");

		//FU Pro DD --
		var fuProDD="";
		if($("#listFU .fu select[id*='elem_fuProName_']").length >0){
			fuProDD="<select name=\"elem_fuProName[]\" id=\"elem_fuProName_"+iter+"\" class=\"form-control \" "+
					" onchange=\"fu_pro_change(this)\" "+
					" onmouseover=\"if(this.selectedIndex)this.title=this.options[this.selectedIndex].text+'-'+this.value;\" tabindex=\""+iter+"4\" >";
			fuProDD+=$("#listFU .fu select[id*='elem_fuProName_']").eq(0).html();
			fuProDD+="</select> ";
		}

		//FU Pro DD --
		var fuInpCheck="";
		var embedin = $("#listFU").attr("data-fuembedin")||"";
		if(embedin=="work_view"){
			fuInpCheck+="<div class=\"checkbox\"><input type=\"checkbox\" id=\"el_fu_choose_"+iter+"\" value=\""+iter+"\" checked title=\"click to add\" tabindex=\""+iter+"0\" ><label for=\"el_fu_choose_"+iter+"\"></label></div>";
		}
		
		//menu
		var mn = $("#listFU .fu .menu_followUpNumber")[0].outerHTML;
		mn = mn.replace(/elem_followUpNumber_1/g, "elem_followUpNumber_"+iter);		
		
		var mv = $("#listFU .fu .menu_followUpVistType")[0].outerHTML;
		mv = mv.replace(/elem_followUpVistType_1/g, "elem_followUpVistType_"+iter);
		
		var str = "<div class=\"row fu\" > "+
					"<div class=\"col-sm-1\" >"+
					fuInpCheck+
					"</div>"+
					"<div class=\"col-sm-2\" >"+
					"<div class=\"input-group plain\" >"+
					"<input type=\"text\" name=\"elem_followUpNumber[]\" id=\"elem_followUpNumber_"+iter+"\" value=\"\""+
					" onchange=\"fu_refineNum(this)\" class=\"form-control \" tabindex=\""+iter+"1\"> "+
					mn+
					"</div>"+
					"</div>"+
					//""+getSimpleMenuJs("elem_followUpNumber_"+iter,"menu_FuNum",imgPath,1,0,"divWorkView",vCords1,1)+" "+
					//Select of Time
					"<div class=\"col-sm-2\" >"+
					"<select name=\"elem_followUp[]\" id=\"elem_followUp_"+iter+"\" onchange=\"fu_move(this)\" class=\"form-control \"  tabindex=\""+iter+"2\">"+
					"<option value=\"\"></option>"+
					"<option value=\"Days\" >Days</option>"+
					"<option value=\"Weeks\" >Weeks</option>"+
					"<option value=\"Months\" >Months</option>"+
					"<option value=\"Year\" >Year</option>"+
					"</select> "+
					"</div>"+
					"<div class=\"col-sm-3\" >"+
					"<div class=\"input-group plain\" >"+
					"<input type=\"text\" name=\"elem_followUpVistType[]\" id=\"elem_followUpVistType_"+iter+"\" value=\"\" onchange=\"changeOther(this);\" class=\"form-control \" tabindex=\""+iter+"3\" > "+
					//""+getSimpleMenuJs("elem_followUpVistType_"+iter,"menu_FuOptions",imgPath,0,0,"divWorkView",vCords2)+" "+
					"<input type=\"hidden\" name=\"elem_followUpVistTypeOther[]\" id=\"elem_followUpVistTypeOther_"+iter+"\" value=\"\">"+
					mv+
					"</div>"+
					"</div>"+
					"<div class=\"col-sm-3\" >"+
					fuProDD+
					"</div>"+
					"<div class=\"col-sm-1\">"+
					"<span class=\"glyphicon glyphicon-remove\" title=\"Remove FU\" id=\"fu_del"+iter+"\" onclick=\"fu_del('"+iter+"')\"  />"+
					"</div>"+
				"</div>";
					//"<div class=\"clearfix\"></div>";
		//otbl.append(str);
		$(str).insertBefore($("#listFU #elem_fuCntr"));
		otbl.attr("data-cntrfu",iter);
		
		wv_activate_menu_click(".fu:last-of-type .menu a");		

		//FU Pro DD: --
		if(fuProDD!=""){
			var tmp=alwaysDocFU;
			if(typeof(alwaysDocFU)=='undefined'||alwaysDocFU==''){
				if(typeof(ssFollowPhy)!="undefined" && ssFollowPhy!=""){
					tmp = ssFollowPhy;
				}else if($(":input[name*=elem_physicianId][value!=''][type!=text]").length>0){
					tmp = ""+$(":input[name*=elem_physicianId][value!=''][type!=text]").val();
				}
			}
			$("#elem_fuProName_"+iter).val(tmp);
		}
		//FU Pro DD --
		
		//Fu visit
		cn_ta_fu();

		/*
		//if(typeof oiter.onchange != "undefined") oiter.onchange(); Not Working
		var id = "tr_fuId_"+iter;
		var row = otbl.insertRow(len); // Insert new Row
		row.id = ""+id;
		
		//add cells
		var cell1 = row.insertCell(0);
		var cell2 = row.insertCell(1);
		var cell3 = row.insertCell(2);
		var cell4 = row.insertCell(3);
		var cell5 = row.insertCell(4);
		var cell6 = row.insertCell(5);

		//Select of number
		/*
		var str1 = "<select name=\"elem_followUpNumber[]\"  class=\"txt_10\" size=\"1\" multiple=\"multiple\">"+
				"<option value=\"\"></option>";
		var txt;
		for(i=1;i<=14;i++) {
			txt = ""+i;
			if(i == 13){
				txt = "PRN"; 
			}
			if(i == 14){
				txt = "PMD"; 
			}
			str1 += "<option value=\""+i+"\">"+txt+"</option>";
		}	
		str1 += "</select>";
		*-/
		var str1 = "<table cellpadding=\"0\" cellspacing=\"0\">"+
				"<tr>"+
				"<td><input type=\"text\" name=\"elem_followUpNumber[]\" id=\"elem_followUpNumber_"+iter+"\" value=\"\" size=\"10\" class=\"txt_10\" onchange=\"fu_refineNum(this);\"></td>"+
				"<td>"+getSimpleMenuJs("elem_followUpNumber_"+iter,"menu_FuNum",imgPath,1,0,"",vCords1,1)+"</td>"+
				"</tr>"+
				"</table>";
		
		//Select of Time		
		var str2 = "<select name=\"elem_followUp[]\" id=\"elem_followUp_"+iter+"\" class=\"txt_10\" onchange=\"fu_move(this)\" >"+
				"<option value=\"\"></option>"+
				"<option value=\"Days\" >Days</option>"+
				"<option value=\"Weeks\" >Weeks</option>"+
				"<option value=\"Months\" >Months</option>"+
				"<option value=\"Year\" >Year</option>"+																	
				"</select>";
				
		var str3 = "<table cellpadding=\"0\" cellspacing=\"0\">"+
				"<tr>"+
				"<td id=\"sp_followUpVistType_"+iter+"\">"+
				"<input type=\"text\" name=\"elem_followUpVistType[]\" id=\"elem_followUpVistType_"+iter+"\" value=\"\" size=\"32\" class=\"txt_10\" onchange=\"changeOther(this);\">"+				
				"<input type=\"hidden\" name=\"elem_followUpVistTypeOther[]\" id=\"elem_followUpVistTypeOther_"+iter+"\" value=\"\">"+
				"</td>"+
				"<td id=\"sp_followUpVistTypeMenu_"+iter+"\">"+getSimpleMenuJs("elem_followUpVistType_"+iter,"menu_FuOptions",imgPath,0,0,"",vCords2)+"</td>"+
				"<td id=\"sp_fu_vis_other_"+iter+"\" width=\"14\" align=\"center\" style=\"visibility:hidden;\">"+
				//"<input type=\"text\" size=\"32\" class=\"txt_10\" name=\"elem_followUpVistTypeOther[]\" id=\"elem_followUpVistTypeOther_"+iter+"\" value=\"\">"+
				"<span class=\"fu_num_close\" onclick=\"removeMe('"+iter+"');\"></span>"+
				"</td>"+
				"</tr>"+
				"</table>";
		
		//Add elements
		cell1.innerHTML = ""+str1;
		cell3.innerHTML = ""+str2;
		cell5.innerHTML = ""+str3;
		cell5.id = "followUpTr_"+iter;
		cell6.innerHTML = "<span class=\"spnFuDel\"></span>";
		cell6.id = "td6_fuId_"+iter;
		cell6.className = "txt_10b hand_cur";
		cell6.width = "15";
		cell6.align = "center";
		//cell6.style.paddingTop = "5px";
		cell6.title = "Remove F/U";
		cell6.onclick = function(){fu_del(iter);};	
		*/

		if(flg == 1){
			return iter;
		}
		
	}
	
	function fu_del(itr){
		
		if(itr>1){
			$("#listFU>.fu:has(#elem_followUp_"+itr+")").remove();
		}else{			
			$("#listFU>.fu:has(#elem_followUp_"+itr+") :input").val("");
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
		
		//alert("CHK1: "+iter);
		
		if(iter > 0){
			//alert("CHK: "+iter);
			iter = parseInt(iter);
			tbl.deleteRow(iter);
		}
		*/

	}
	//
	function fu_checkChoosebtn(obj){
		//check checkbox
		ochk= $("#"+obj.id.replace(/elem_followUpNumber_|elem_followUp_|elem_followUpVistType_|elem_fuProName_/,"el_fu_choose_"));			
		if(ochk.length>0){ ochk.prop("checked", true); }
	}
	
	function fu_pro_change(obj){
		fu_checkChoosebtn(obj);
	}
	
	function fu_refineNum(obj){
		var arr  =obj.value.split(",");
		var len = arr.length;
		var str = "";		
		var tmp = "";
		var flgMv = 0;	
		if(len > 1){			
				tmp = arr[0];
				if(tmp=="-"){
					if(arr[1].indexOf("-")==-1) str = arr[1]+tmp;
				}else{
					var ar2 = arr[1].split("-");					
					if(typeof ar2[1] == "string") ar2[1]=$.trim(ar2[1]);
					if(ar2.length==2 && typeof ar2[1] != "undefined" && ar2[1]==""){
						str = arr[1]+tmp;
						flgMv = 1;
					}else{
						str = tmp;
					}
				}
		}else{			
			str = obj.value;		
		}
		
		obj.value = ""+$.trim(str);

		//check cb	
		fu_checkChoosebtn(obj);	

		//Enable Days, Weeks, Month and Year drop down.
		var oSel = null;
		if(obj.id){
			oSel = gebi(obj.id.replace(/elem_followUpNumber_/,"elem_followUp_"));
			if(oSel){
				oSel.disabled = false;	
			}
		}
		
		//Calendar
		if(obj.value.indexOf("Calendar") != -1){
			obj.value = "";			
			
			$("#"+ obj.id).datepicker({dateFormat:z_js_dt_frmt});
			$("#"+ obj.id).datepicker('show');
			
			//Disable Days, Weeks, Month and Year drop down.			
			if(oSel){
				oSel.disabled = true;	
			}
			//Open Calender Pop up
			//newWindow(""+obj.id);	
			return;
		}else{
			$("#"+ obj.id).datepicker('destroy');
		}	
		//
		
		//Move
		if(flgMv == 1){
			fu_move(obj); 
		}
	}
	
	function fu_move(obj){		
		var id = obj.id;
		var t = null;
		if(id.indexOf("elem_followUpNumber") != -1){
			t = gebi(id.replace(/elem_followUpNumber/, "elem_followUp"));
		}else if(id.indexOf("elem_followUp") != -1){
			t = gebi(id.replace(/elem_followUp/, "elem_followUpVistType"));
		}
		if((obj.value != "") && (t != null)){			
			t.focus();
		}
		//check cb	
		fu_checkChoosebtn(obj);	
	}
	
	function changeOther(obj,val){
		//check cb	
		fu_checkChoosebtn(obj);
		if((obj.value == 'Other') || (typeof val != 'undefined')){
			val = (typeof val == 'undefined') ? "" : val;
			//gebi('followUpTr1').innerHTML = '<input type="text" size="42" class="txt_10" name="elem_followUpVistTypeOther" value="'+val+'">&nbsp;<img src="../../images/close_chart.gif" title="Change" onclick="removeMe();" style="cursor:hand;" width="10" align="center" hspace="0" vspace="0">';			
			var idOthr = obj.id.replace(/elem_followUpVistType/g,"elem_followUpVistTypeOther");		
			//var idObjDd = obj.id.replace(/elem_followUpVistType/g,"sp_followUpVistTypeMenu");			
			var idOthrIcon = obj.id.replace(/elem_followUpVistType/g,"sp_fu_vis_other");
			
			var oOthr = gebi(idOthr);
			//var oObjDd = gebi(idObjDd);		
			var oOthrIcon = gebi(idOthrIcon);
			if(oOthr && oOthrIcon){				
				oOthr.value="1";
				obj.value = "";
				obj.focus();
				//oObjDd.style.visibility = "hidden";
				oOthrIcon.style.visibility = "visible";			
			}
		}
	}

	function removeMe(id){
		var objTxt = gebi("elem_followUpVistType_"+id);
		//var objDd = gebi("sp_followUpVistTypeMenu_"+id);
		var objOthr = gebi("elem_followUpVistTypeOther_"+id);
		var objOthrIcon = gebi("sp_fu_vis_other_"+id);
		if(objOthr && objOthrIcon && objTxt){
			objTxt.value = objTxt.defaultValue;
			objOthr.value = "0";
			//objDd.style.visibility = "visible";
			objOthrIcon.style.visibility = "hidden";
		}
	}

	function fu_checkEmpty_Dup(num,tm,tp){
		
		var eid = "";
		var dup = 0;

		var oiter = gebi("elem_fuCntr");
		var otbl = gebi("tblFU");
		//var len = parseInt(oiter.value);
		var len = $("#listFU").attr("data-cntrfu");
		len = parseInt(len);
		
		var n,t,p;
		for(var i=1;i<=len;i++){

			if($("#elem_followUpNumber_"+i).length > 0){
				
				n = $("#elem_followUpNumber_"+i).val();
				t = $("#elem_followUp_"+i).val();
				p = $("#elem_followUpVistType_"+i).val();
				
				//Emp
				if(eid == "" && n == "" && t == "" && p == ""){
					eid = i;		
				}
				
				if($.trim(n) == $.trim(num) && $.trim(t) == $.trim(tm) && $.trim(p) == $.trim(tp)){
					dup = 1;
					break;
				}			
			}
		}
		
		return {"e":eid,"d":dup};
	}

	function fu_addOpts(num,tm,tp,prid){		
		
		var oCh = fu_checkEmpty_Dup(num,tm,tp);
		if(oCh.d == 1){
			return;
		}
		
		var itr = "";	
		if(oCh.e != ""){
			itr = oCh.e; 
		}else{
			itr = fu_add(1);
		}
		
		if(itr != ""){
			$("#elem_followUpNumber_"+itr).val(num);
			$("#elem_followUp_"+itr).val(tm);
			$("#elem_followUpVistType_"+itr).val(tp);
			if(typeof(prid)!="undefined" && prid!=""){
				$("#elem_fuProName_"+itr).val(prid);
			}else if($(":input[name=elem_curPhysicianId]").val()!=""){
				$("#elem_fuProName_"+itr).val(""+$(":input[name=elem_curPhysicianId]").val());
			}
		}		
	}
	
	//
	//type ahead fu
	function cn_ta_fu(){
		var ta7 = function(ota7){
			$( ota7 ).autocomplete({ 
			    source: "requestHandler.php?elem_formAction=TypeAhead&mode=FUVisit&wh="+ota7.name,
			    minLength: 1,
			    autoFocus: true,	
			    focus: function( event, ui ) {return false;},		
			    select: function( event, ui ) { if(event.keyCode === 9){ return false;}  if(ui.item.value!=""){this.value=""+ui.item.value; }}
			 });	
		};
		//[id*=elem_followUpVistType_]
		$( "#listFU input[type=text]" ).bind( "focus", function(event){if(!$(this).hasClass("ui-autocomplete-input")){ta7(this);};});
	}
	//
	
	function op_fu_sec(dv){
		/*
		var div_fu_core = ""; $("#listFU").html();
		var div_fu = "<div id=\"dv_fu_sec\" style=\"position:absolute;border:1px solid black;padding:1px;display:none;background-color:white;z-index:2;left:20%;top:20px;\">"+
		"<div style=\"padding:1px;background-color:#D3D3D3;\">Follow Up <span style=\"float:right;\" onclick=\"op_fu_sec(1)\">Close</span></div>"+
		"<div style=\"width:600px;height:300px;overflow:auto;\">"+div_fu_core+"</div>"+
		"<center><input type=\"button\" id=\"btn_fu_save\" name=\"btn_fu_save\" value=\"Done\" class=\"dff_button_sm\" onclick=\"op_fu_sec(3)\"></center>"+		
		"</div>" ;
		*/
		if(dv==1||dv==3){
			
			//--
			if(dv==1){	
			$("#listFU input[type=checkbox]").each(function(indx){ if(this.checked==false){ j=indx+1; fu_del(j); } });			
			}
			//--
			
			$("#dv_fu_sec").removeClass("fubig panel panel-success");
			$("#dv_fu_sec .fu_con").removeClass("panel-body");
			$("#dv_fu_sec .fu_con").css({"max-height":"none", "overflow":"visible"});
		}else{
			$("#listFU input[type=checkbox]").each(function(indx){ this.checked=false; });	
			$("#dv_fu_sec").addClass("fubig panel panel-success");
			$("#dv_fu_sec .fu_con").addClass("panel-body");
			$('#dv_fu_sec').draggable({handle:$('#assessplan #follow_up .fubig .hdr')});
			$("#dv_fu_sec .fu_con").css({"max-height": ""+parseInt($(window).height()*70/100)+"px", "overflow":"auto"});
			
		}
	}