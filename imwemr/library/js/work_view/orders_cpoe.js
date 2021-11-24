//-- ORDER / ORDER SET ---------
//#assessplan .planbox



//
function cpoe_printRx(){		
	var parWidth = parent.document.body.clientWidth;
	var parHeight = parent.document.body.clientHeight;		
	top.popup_win(zPath+'/chart_notes/requestHandler.php?printType=1&elem_formAction=print_patient_rx','printPatientRx','scrollbars=1,resizable=1,width='+parWidth+'height='+parHeight+',top=0,left=0');
}

//ptmed
function funInsMed() {
	var obj_txtarea = document.getElementsByName('elem_plan[]');
	var val_txtarea='';
	var med_len = (document.getElementById('hidd_count_med'))?document.getElementById('hidd_count_med').value:0;
	var obj_sel_split = new Array;
	var j= obj_len = valobjIndex = val_spntxt = txt = spnMedObj = '';
	for(var i=0;i<med_len;i++) {
		obj_len = '';
		obj_sel = $("#selMed"+i).selectedValuesString()	;	
		spnMedObj='';
		if(obj_sel!="") {
			spnMedObj = document.getElementById('spnOcMed'+i);	
		}
		txt='';
		obj_sel_split = obj_sel.split(',');
		for(j=0;j<obj_sel_split.length;j++) {
			valobjIndex = obj_sel_split[j];//alert(valobjIndex);
			valobjIndex = (parseFloat(valobjIndex)-1);
			if(obj_txtarea.item(parseFloat(valobjIndex))) {
				val_txtarea = $.trim(""+obj_txtarea.item(parseFloat(valobjIndex)).value);
				if(spnMedObj) {
					val_spntxt = $.trim(""+spnMedObj.innerText);
				}
				if($.trim(val_txtarea)!="" || j!=0) { txt +='\n'; }
				txt =val_spntxt;
				if(obj_txtarea.item(parseFloat(valobjIndex)).value!="") {
					var txtTmpVar = $.trim(obj_txtarea.item(parseFloat(valobjIndex)).value);
					if(txtTmpVar.indexOf(txt)==-1){
						obj_txtarea.item(parseFloat(valobjIndex)).value='';					
						obj_txtarea.item(parseFloat(valobjIndex)).value = $.trim(txtTmpVar+'\n'+txt);
					}
				}else {
					obj_txtarea.item(parseFloat(valobjIndex)).value=$.trim(txt);	
				}
				obj_txtarea.item(parseFloat(valobjIndex)).onkeyup();
			}
		}
	}
	//var blnkMed = document.getElementById('blank_med_txt');
	//obj_sel_blnk_med = $("#selMedBlank").selectedValuesString()	;
}


//AP Policy
function isStrExistsInPlan(pln, ndl, flgodr, odrnm){
		//Remove special characters " '
		pln = pln.replace(/\&\#3(9|4)\;|\'|\"/g, "");
		ndl = ndl.replace(/\&\#3(9|4)\;|\'|\"/g, "");
	
		pln=$.trim(pln);
		pln = pln.toLowerCase();
		pln="\n"+pln+"\n";		
		ndl = ndl.toLowerCase();
		
		//alert(ndl);
	
		if( pln.indexOf("\n"+ndl+"\n")!=-1){
			return 1;	
		}else if( pln.indexOf(""+ndl)!=-1){
			var arr = pln.split("\n");
			var ln=arr.length;			
			if(ln>0){
				ndl = $.trim(ndl);				
				var c=0;
				for(var a=0; a<ln;a++){
					var ta = $.trim(arr[a]);
					
					if(ta == ndl){return 1;}
					
					//remove last character if it is 173.
					if(ta!="" && ta.length>0){if(ta.charCodeAt(ta.length-1)==173){var ta2 =ta.substr(0,ta.length-1);if(ta2 == ndl){return 1;}}}
					
					//check similar :: and it should be in start.:: random number 11 is taken
					var fndinx = ta.indexOf(""+ndl);
					if(fndinx != -1 && fndinx<11){
						c+=1;
					}
				}
				
				//if only one similar, then accept
				if(c==1){
					return 1;
				}
			}		
		}
		
		//check if ORDER --
		if($.trim(flgodr) == "ORDER" || $.trim(flgodr) == "2"){
			odrnm = odrnm.toLowerCase();
			if( pln.indexOf(""+odrnm)!=-1){
				return 1;
			}
		}
	//}
	return 0;
}

function cpoe_emdeoncheck(){
	//alert("Emdeon Checks: processing..");
	
	var arrAllOrdrAscId=[], arrAllOrdrDetId=[];
	var oapa = gebi("elem_apa",1);
	var lnapa = oapa.length;
	for(var a=0;a<lnapa;a++){
		var tmp = "";var cmnsep="~cmnsep~";
		var strAssess=oapa[a].value;
		var oapp = gebi("elem_app"+a,1);
		var lnapp = oapp.length;			
		var a_n = parseInt(a)+1;
		
		for(var b=0;b<lnapp;b++){
			var t = $.trim(""+oapp[b].value);				
			if(t!=""){
				var remflg=1;
				//if(tmp!=""&&tmp.indexOf(t+cmnsep)!=-1){ continue; }
				var oOrdrAscId = $(oapp[b]).attr("data-order_asoc_id");
				oOrdrDetId = $(oapp[b]).attr("data-order_det_id");
				if((typeof(oOrdrAscId)!="undefined" && oOrdrAscId!="") || 
					(typeof(oOrdrDetId)!="undefined" && oOrdrDetId!="")){}
					else{ continue;  }
				//alert("check: "+strAssess+" - "+t);
				//if(tmp!=""){
					//Insert Assess and Plan
					//t=t+"\0";//
					
					//add special hidden character
					//t=t+"\u00AD";
				
					//top.fmain.addAssessOption(strAssess,t,0,"def",remflg);
					
					//get order nm
					var oOrdrAscId = $(oapp[b]).attr("data-order_asoc_id");	
					if(typeof(oOrdrAscId)!="undefined" && oOrdrAscId!=""){							
					arrAllOrdrAscId[arrAllOrdrAscId.length]=oOrdrAscId;
					}
					
					if(oapp[b].checked){
						oOrdrDetId = $(oapp[b]).attr("data-order_det_id");
						if(typeof(oOrdrDetId)!="undefined" && oOrdrDetId!=""){							
						arrAllOrdrDetId[arrAllOrdrDetId.length]=oOrdrDetId;
						}	
					}						
				//}		
			}
		}
	}
	
	//alert(arrAllMeds.join(","));
	
	if(arrAllOrdrDetId.length>0){ ///arrAllOrdrAscId.length>0 ||
		var str_or_asc_id = arrAllOrdrAscId.join(",");
		str_or_asc_id=encodeURI(str_or_asc_id);
		
		var str_or_det_id = arrAllOrdrDetId.join(",");
		str_or_det_id=encodeURI(str_or_det_id);
		
		if(typeof(setProcessImg) != 'undefined' )setProcessImg("1","btnAP_ok", "250","530","Please wait! Checking with Emdeon...");
		
		var url ="requestHandler.php?elem_formAction=GetEmdeonWarnings&str_or_asc_id="+str_or_asc_id+"&str_or_det_id="+str_or_det_id;		
		$.get(url,function(data){
				
					if(typeof(setProcessImg) != 'undefined' )setProcessImg("0","btnAP_ok");
			
					if(""+data == "No warning!"){
						funApPlan(1,'',1);
					}else{
						$("body").append("<div id=\"dialog_emd\" title=\"Emdeon Warning!\">"+data+"</div>");
						$( "#dialog_emd" ).dialog({buttons: {
											"Save CPOE": function() {
											  $( this ).dialog( "close" );funApPlan(1,'',1);													
											},
											Cancel: function() {
											  $( this ).dialog( "close" );
											}
										      }});
					}
			});		
	}else{
		funApPlan(1,'',1);
	}
}

function isPreviousPlanAOrder(data, x, pln,flgdel){
	pln=$.trim(pln);	
	var oplan = [];
	var ret=0;
	//*
	var ptrn = "\\s*\\d{2}\-\\d{2}\-\\d{4}"; var patt = new RegExp(ptrn);
	var pln_2 = ""+pln.replace(patt,"");
	
	
	//loop orders
	if(data.order&& data.order[x] ){ //&& data.order[x].length>0
		
		for(var xx in data.order[x]){
			
			if(data.order[x][xx] && data.order[x][xx].length>0){
		
				var aror = data.order[x][xx];
				var lnor= aror.length;							
				for(var j=0;j<lnor;j++){
					
					if(aror[j] == null || typeof(aror[j][0]) == "undefined"){ continue; }
					
					if($.trim(aror[j][2])=="ORDER"){						
						var arshw = $.trim(aror[j][0]);
						var arshid = aror[j][1];
						var ordersetid = $.trim(aror[j][3]);	
						var ordernm = $.trim(aror[j][4]);
						var ordersupli = $.trim(aror[j][5]);
						var arshw_wsitesig = $.trim(aror[j][6]);
						var ordersig = "";
						var ordersig_s ="";
						var ordersig_s_loc ="";
						//ret=2;
					}else{
						var arshw = $.trim(aror[j][0]);
						var arshid = aror[j][1];
						var ordersetid = $.trim(aror[j][2]);
						var ordernm = $.trim(aror[j][3]);
						var ordersupli = 0;
						var ordersig = $.trim(aror[j][4]);
						var ordersig_s = $.trim(aror[j][5]);
						var ordersig_s_loc = $.trim(aror[j][6]);
						var arshw_wsitesig = "";
						//ret=1;
					}
					//
					if(typeof(ordersetid)=="undefined" || ordersetid==""){ ordersetid="0";  }
					
					//alert(arshw+" - "+arshid+" - "+ordersetid);
					//remove site and check
					
					
					// check for matching orders with plans
					var flg_match=0;
					
					//console.log(""+pln+" == "+arshw+" == "+arshw_wsitesig+" == "+ordersig_s_loc+" == "+flg_match);
					
					if(pln == arshw || pln_2 == arshw ||flg_match==1){
						oplan = [arshid,arshw,ordersetid,ordernm,ordersupli,ordersig,ordersig_s,ordersig_s_loc];
						if($.trim(aror[j][2])=="ORDER"){
							ret=2; 	
						}else{
							ret=1;
						}
						
						if(flgdel==1){
							delete data.order[x][xx][j];
						}
					}	
					
				}			
			}		
		}
	}
	
	//*/
	return [ret,oplan,data.order[x]]; //data.order_assess_admin[x],
	
}

//flgEm = check with emdeon
//strSC = smart chart variable
function funApPlan(val,strSC,flgEm){
	var ptmedRes='';
	if((typeof(val)=="undefined"||val=="")&&val!="0"){
	
	var arAs=[];
	var arAsIndx=[];
	var oAs = $("#assessplan textarea[name*=\"elem_assessment[]\"]");
	var lnAs = oAs.length;
	for(var i=0;i<lnAs;i++){
		var as = oAs[i].value;
		if(as!=""){
			arAs[arAs.length]=as;
			arAsIndx[arAs.length]=parseInt(i)+1;
		}
	}
	var arAsDx=[];
	var oAsDx = $("#assessplan textarea[name*=elem_assessment_dxcode]");
	var dxLn = $("#assessplan textarea[name*=elem_assessment_dxcode]").length;
	for(var i=0;i<dxLn;i++){
		if(oAs[i].value!="")
			arAsDx[arAsDx.length] = oAsDx[i].value;
	}
	
	//start 
	var final_flag 	= document.getElementById('hidd_final_flag').value;
	var form_id 	= document.getElementById('hidd_formId').value;
	var ptmed = "";
	
	
	//
	if(typeof(strSC) == "undefined"){ strSC=""; }	
	
	if(arAs.length>0||strSC!=""){
		$("#divApPlan").remove();
		var str = arAs.join("~!~");
		str = encodeURIComponent(str);
		var strIndx = arAsIndx.join("~!~");
		strIndx = encodeURIComponent(strIndx);	
		var strDx = arAsDx.join("~!~");
		strDx = encodeURIComponent(strDx);	
			
		
		//start
		if(typeof(setProcessImg) != 'undefined' )setProcessImg(1,"linkPrevPlan");
		$.get( "requestHandler.php?elem_formAction=GetPlansofAsmt&asmt="+str+"&strSC="+strSC+"&final_flag="+final_flag+"&form_id="+form_id+"&lnAs="+lnAs+"&strIndx="+strIndx+"&strDx="+strDx,
			function(data){				
				if(typeof(setProcessImg) != 'undefined' )setProcessImg(0,"linkPrevPlan");
				
				//var myWindow = window.open("","MsgWindow","width=200,height=100, resizable=1,scrollbars=1");
				//myWindow.document.write(""+data);
				//console.log(data);	
				
				if(data.assess){
					var tmp =  "";					
					var c=0;
					var k=0; 
					for(var x in data.assess){
						var as_full = as = x;	
						as = js_htmlentities(as);	
						if(as.length>50){as=""+as.substring(0,50)+"..";}
						var arpl = data.assess[x];
						var lnPl = arpl.length;
						var bckcolor=(c%2==0) ? "#f5f5f5" : "white" ;
						var ap_num = c + 1;
						if(data.arAsIndx && data.arAsIndx[x]){ ap_num = data.arAsIndx[x]; }
						
						
						//arr for duplicate name orders
						var tmp_dup_med_ordr=[];
						var tmp_dup_med_ordr_htm=[];
						
						
						//make btns for orders --
						var type_arr = new Array("Meds", "Labs", "Imaging/Rad", "Procedure/Sx", "Info/Inst", "Order Sets");
						var strBtnOrdr="";
						for(var xz in type_arr){							
							//strBtnOrdr+="<label class=\"clickable\" onclick=\"showOrdersInAP(this, '"+ap_num+"')\">"+type_arr[xz]+"</label>";
							strBtnOrdr+="<button class=\"allbut\" type=\"button\" onclick=\"showOrdersInAP(this, '"+ap_num+"')\" >"+type_arr[xz]+"</button>";		
						}
						//make btns for orders --	

						//Continue Meds --	
						//Remove continue meds-	
						var tmpConMeds = "";//($("input[id=elem_apConMeds"+ap_num+"]").val()==1) ? "CHECKED" : "" ;
						var strConMeds = "";//"<input type=\"checkbox\" name=\"elem_apConMeds"+ap_num+"_dum\" value=\"1\" "+tmpConMeds+" > Continue Meds.";
						//Continue Meds --	
						
						var clstr=(c%2==0) ? "asttd" : "asttd1" ;
						
						tmp+="<tr valign=\"top\" class=\""+clstr+"\" >";						
						tmp+="<td id=\"td_appu_"+ap_num+"\"><div><label class=\"as\" >"+ap_num+". "+as+"</label>"+strConMeds+strBtnOrdr+"</div>"+
								"<input type=\"hidden\" name=\"elem_apa\" value=\""+as_full+"\">";
						
						if(lnPl>0){
							var tmp_pln ="", tmp_pln_chk="";
							
							for(var j=0;j<lnPl;j++){
								var tmp_row ="";
								//
								if(arpl[j] == null){ continue; }
								
								var arshw = $.trim(arpl[j]);
								
								var flgRed=0;
								if(arpl[j].indexOf("~HI@GH#")!=-1){
									arpl[j] = $.trim(arpl[j].replace(/~HI@GH#/g,""));									
									arshw = "<span class=\"color_red\">"+arpl[j]+"</span>";
									flgRed=1;
								}								
								
								//check in orders for previous plans only-- 
								
								var flgshowOrder=0;
								//									
								var tmpans = isPreviousPlanAOrder(data, x, arpl[j],flgRed);
								if(flgRed==1){
									if(tmpans[0]==1){	//order admin
										data.order[x] = tmpans[2];
										flgshowOrder=1;
									}else if(tmpans[0]==2){ //orders 
										data.order[x] = tmpans[2];
										flgshowOrder=2;
									}
								}else{
									if(tmpans[0]==1 || tmpans[0]==2){
										continue;	
									}										
								}	
								//}
								
								//check in orders for previous plans only-- 
								
								//check applan in plan --
								tmp_pln_chk="";
								if(arpl[j]!="" && typeof($("#elem_plan"+ap_num).val())!="undefined" && $.trim(""+$("#elem_plan"+ap_num).val())!="" ){ //
									//if( $("#elem_plan"+ap_num).val().toLowerCase().indexOf(""+arpl[j].toLowerCase())!=-1){
									if(isStrExistsInPlan($("#elem_plan"+ap_num).val(), arpl[j], tmpans[0],tmpans[1][3])){	///check if values exists in plan
										tmp_pln_chk="CHECKED";
									}
								}
								//check applan in plan --								
								
								if(flgshowOrder==2){ //orders
									var tmp_arshid=tmpans[1][0];
									//var tmp_onm=tmpans[1][3];
									tmp_row+="<input type=\"checkbox\" id=\"elem_app_pln"+c+k+"\" name=\"elem_app"+c+"\" value=\""+arpl[j]+"\" hspace=\"23\" "+tmp_pln_chk+" onclick=\"delOrderDetail(this)\" data-order_asoc_id=\""+tmp_arshid+"\" ><label for=\"elem_app_pln"+c+k+"\" ondblclick=\"showOrderDetail('"+ap_num+"', '0', '"+tmp_arshid+"')\" >"+arshw+"</label>&nbsp;&nbsp;<label  id=\"elem_app_pln"+c+k+"_lbl_del\"  onclick=\"saveOrderDetail(3, '"+ap_num+"', '"+tmp_arshid+"','del')\" title=\"Delete\" style=\"cursor:pointer; font-weight:bold;color:purple;display:none;\">X</label>"; //<br/>
								}else if(flgshowOrder==1){ //order admin
									var tmp_arshid=tmpans[1][0];
									var tmp_ordersetid=tmpans[1][2];
									var tmp_ordernm=tmpans[1][3];
									var tmp_ordersig=tmpans[1][5];
									var tmp_ordersig_s=tmpans[1][6];
									var tmp_ordersig_s_loc=tmpans[1][7];
									
									var pntr="";
									//strEyeOpts --
									var strEyeOpts = 	"<div class=\"form-inline div_eye_site\"><div class=\"radio\"><input type=\"radio\" name=\"elem_ocu_med_site"+c+k+"\" id=\"elem_ocu_med_site_ou"+c+k+"\" value=\"OU\" checked><label for=\"elem_ocu_med_site_ou"+c+k+"\"><b class=\"ou\">OU</b></label></div>&nbsp;"+
													"<div class=\"radio\"><input type=\"radio\" name=\"elem_ocu_med_site"+c+k+"\" id=\"elem_ocu_med_site_od"+c+k+"\" value=\"OD\" ><label for=\"elem_ocu_med_site_od"+c+k+"\"><b class=\"od\">OD</b></label></div>&nbsp;"+
													"<div class=\"radio\"><input type=\"radio\" name=\"elem_ocu_med_site"+c+k+"\" id=\"elem_ocu_med_site_os"+c+k+"\" value=\"OS\" ><label for=\"elem_ocu_med_site_os"+c+k+"\"><b class=\"os\">OS</b></label></div></div>&nbsp;";
									//strEyeOpts --
									
									//str_sig --									
									var strSigOpts ="";
									if(typeof(tmp_ordersig)!="undefined" && tmp_ordersig!=""){
										strSigOpts ="";
										var arr_tmp_ordersig = tmp_ordersig.split("\n");
										for(var cx in arr_tmp_ordersig){
											if(arr_tmp_ordersig[cx] && typeof(arr_tmp_ordersig[cx])!="undefined" && arr_tmp_ordersig[cx]!=""){
												strSigOpts +="<option value=\""+arr_tmp_ordersig[cx]+"\">"+arr_tmp_ordersig[cx]+"</option>";
											}
										}
										
										if(strSigOpts!=""){
											strSigOpts = "<select id=\"elem_ocu_med_sig"+c+k+"\" class=\"form-control\" ><option value=\"\"></option>"+strSigOpts+"</select>";
										}										
									}
									
									//text--
									strSigOpts += "&nbsp;&nbsp;<input type=\"text\" id=\"elem_ocu_med_sig_other"+c+k+"\" value=\"\"  placeholder=\"Sig\" class=\"form-control\" >";
									//str_sig --
									
									var tmp_pln002 = "<input type=\"checkbox\" id=\"elem_app"+c+k+"\" name=\"elem_app"+c+"\" value=\""+arpl[j]+"\" hspace=\"23\" "+tmp_pln_chk+" data-order_det_id=\""+tmp_arshid+"\" data-order_set_id=\""+tmp_ordersetid+"\" data-j=\""+k+"\" data-ordernm=\""+tmp_ordernm+"\" data-sig_s=\""+tmp_ordersig_s+"\" data-sig_s_loc=\""+tmp_ordersig_s_loc+"\"  ><label for=\"elem_app"+c+k+"\" ><b>"+arshw+"</b></label>&nbsp;&nbsp;"; 
									//
									if(tmp_dup_med_ordr.indexOf(tmp_ordernm)==-1){									
										tmp_dup_med_ordr[tmp_dup_med_ordr.length] = tmp_ordernm;
										
										pntr="<span class=\"pointer ui-icon ui-icon-blank002\" ></span>";
										if(data.dup_med_order[as_full] && data.dup_med_order[as_full][tmp_ordernm] && data.dup_med_order[as_full][tmp_ordernm]>1){
											pntr= "<span class=\"pointer  ui-icon ui-icon-circle-arrow-e\" onmouseover=\"showrelorders('"+tmp_ordernm+"',1, this)\" onmouseout=\"showrelorders('"+tmp_ordernm+"',0)\" ></span>";
											
											//make div from more options	
											var tmphtm = tmp_dup_med_ordr_htm[tmp_ordernm]||"";										
											tmphtm = tmphtm + tmp_pln002+"&nbsp;&nbsp;"+strEyeOpts+"&nbsp;&nbsp;"+strSigOpts+"<br/>";
											tmp_dup_med_ordr_htm[tmp_ordernm] = tmphtm;
											
											//make handle for childern orders --
											var tmp_pln003 = "<input type=\"checkbox\" id=\"elem_app"+c+k+"master\" name=\"elem_app"+c+"master\" value=\""+tmp_ordernm+"\" hspace=\"23\" onclick=\"checkrelorders(this)\" ><label for=\"elem_app"+c+k+"master\" ><b>"+tmp_ordernm+"</b></label>&nbsp;&nbsp;"; 
											tmp_pln003 += pntr+"&nbsp;&nbsp;"; //<br/>
											tmp_row+= tmp_pln003;
											//--
											
										}else{
											tmp_pln002 += pntr+"&nbsp;&nbsp;"+strEyeOpts+"&nbsp;&nbsp;"+strSigOpts+""; //<br/>
											tmp_row+= tmp_pln002;	
										}
										
									}else{
									
										//make div from more options	
										var tmphtm = tmp_dup_med_ordr_htm[tmp_ordernm]||"";										
										tmphtm = tmphtm + tmp_pln002+"&nbsp;&nbsp;"+strEyeOpts+"&nbsp;&nbsp;"+strSigOpts+""; //<br/>
										tmp_dup_med_ordr_htm[tmp_ordernm] = "<div>"+tmphtm+"</div>";
									}

									
								}else{								
									tmp_row+="<input type=\"checkbox\" id=\"elem_app"+c+k+"\" name=\"elem_app"+c+"\" value=\""+arpl[j]+"\" hspace=\"23\" "+tmp_pln_chk+" ><label for=\"elem_app"+c+k+"\">"+arshw+"</label>"; //<br/>
								}
								
								//Add
								tmp_pln+="<div class=\"form-inline\">"+tmp_row+"</div>";
								
								k++;
							}
							//document.write(tmp_pln);
							tmp += (tmp_pln!="") ? "<div id=\"divapplan"+ap_num+"\" >"+tmp_pln+"</div>" : "" ;
							
						}
						
						//orders--
						//*						
						//var arr_done_order=[];
						
						var tmp_order =tmp_order_supli="";
						if(data.order && data.order[x] ){ //&& data.order[x].length>0
							
							for(var xx in data.order[x]){								
								
								if(data.order[x][xx] && data.order[x][xx].length>0){
										
									var aror = data.order[x][xx];
									var lnor= aror.length;									
									
									//alert("c: "+c+", K: "+k);
									
									for(var j=0;j<lnor;j++){
										
										if(aror[j] == null || typeof(aror[j][0]) == "undefined"){ continue; }
										
										if($.trim(aror[j][2])=="ORDER"){
											var arshw = $.trim(aror[j][0]);
											var arshid = aror[j][1];
											var ordersetid = $.trim(aror[j][3]);	
											var ordernm = $.trim(aror[j][4]);
											var ordersupli = $.trim(aror[j][5]);
											var ordersig = "";
											var ordersig_s ="";
											var ordersig_s_loc ="";
										}else{
											var arshw = $.trim(aror[j][0]);
											var arshid = aror[j][1];
											var ordersetid = $.trim(aror[j][2]);
											var ordernm = $.trim(aror[j][3]);
											var ordersupli = 0;
											var ordersig = $.trim(aror[j][4]);
											var ordersig_s = $.trim(aror[j][5]);
											var ordersig_s_loc = $.trim(aror[j][6]);
										}
										//
										if(typeof(ordersetid)=="undefined" || ordersetid==""){ ordersetid="0";  }
										
										
										//check applan in plan --								
										tmp_pln_chk="";
										if(arshw!="" && typeof($("#elem_plan"+ap_num).val())!="undefined" && $.trim(""+$("#elem_plan"+ap_num).val())!="" ){ //
											//if( $("#elem_plan"+ap_num).val().toLowerCase().indexOf(""+arshw.toLowerCase())!=-1){
											if(isStrExistsInPlan($("#elem_plan"+ap_num).val(), ""+arshw, aror[j][2], ordernm)){
												tmp_pln_chk="CHECKED";
											}
										}
										//check applan in plan --
										
										//for filtering orders name wise
										//arr_done_order[arr_done_order.length]=arshw;
										/*tmp_order+="&bull;&nbsp;<label onclick=\"showOrderDetail('"+ap_num+"', '0', '"+arshid+"')\" style=\"cursor:pointer\">"+arshw+"</label>&nbsp;&nbsp;<label onclick=\"saveOrderDetail(3, '"+ap_num+"', '"+arshid+"','del')\" title=\"Delete\" style=\"cursor:pointer; font-weight:bold;color:purple;\">X</label><br/>";*/
										if($.trim(aror[j][2])=="ORDER"){
											if(ordersupli==1){ //if order is not in console
											tmp_order_supli+="<div class=\"form-inline\"><input type=\"checkbox\" id=\"elem_app"+c+k+"\" name=\"elem_app"+c+"\" value=\""+arshw+"\" hspace=\"23\" "+tmp_pln_chk+" onclick=\"delOrderDetail(this)\" data-order_asoc_id=\""+arshid+"\" ><label for=\"elem_app"+c+k+"\" ondblclick=\"showOrderDetail('"+ap_num+"', '0', '"+arshid+"')\" >"+arshw+"</label>&nbsp;&nbsp;<label  id=\"elem_app"+c+k+"_lbl_del\"  onclick=\"saveOrderDetail(3, '"+ap_num+"', '"+arshid+"','del')\" title=\"Delete\" style=\"cursor:pointer; font-weight:bold;color:purple;display:none;\">X</label></div>"; //<br/>	
											}else{											
											tmp_order+="<div class=\"form-inline\"><input type=\"checkbox\" id=\"elem_app"+c+k+"\" name=\"elem_app"+c+"\" value=\""+arshw+"\" hspace=\"23\" "+tmp_pln_chk+" onclick=\"delOrderDetail(this)\" data-order_asoc_id=\""+arshid+"\" ><label for=\"elem_app"+c+k+"\" ondblclick=\"showOrderDetail('"+ap_num+"', '0', '"+arshid+"')\" >"+arshw+"</label>&nbsp;&nbsp;<label  id=\"elem_app"+c+k+"_lbl_del\"  onclick=\"saveOrderDetail(3, '"+ap_num+"', '"+arshid+"','del')\" title=\"Delete\" style=\"cursor:pointer; font-weight:bold;color:purple;display:none;\">X</label></div>"; //<br/>
											}
										}else{
											if(arshw!="" && arshid!=""){
												
												
												var pntr="";
												//strEyeOpts --
												var strEyeOpts = 	"<div class=\"form-inline div_eye_site\"><div class=\"radio\"><input type=\"radio\" name=\"elem_ocu_med_site"+c+k+"\" id=\"elem_ocu_med_site_ou"+c+k+"\" value=\"OU\" checked><label for=\"elem_ocu_med_site_ou"+c+k+"\"><b class=\"ou\">OU</b></label></div>&nbsp;"+
																"<div class=\"radio\"><input type=\"radio\" name=\"elem_ocu_med_site"+c+k+"\" id=\"elem_ocu_med_site_od"+c+k+"\" value=\"OD\" ><label for=\"elem_ocu_med_site_od"+c+k+"\"><b class=\"od\">OD</b></label></div>&nbsp;"+
																"<div class=\"radio\"><input type=\"radio\" name=\"elem_ocu_med_site"+c+k+"\" id=\"elem_ocu_med_site_os"+c+k+"\" value=\"OS\" ><label for=\"elem_ocu_med_site_os"+c+k+"\"><b class=\"os\">OS</b></label></div></div>&nbsp;";
												//strEyeOpts --
												//str_sig --									
												var strSigOpts ="";
												if(typeof(ordersig)!="undefined" && ordersig!=""){
													strSigOpts ="";
													var arr_tmp_ordersig = ordersig.split("\n");
													for(var cx in arr_tmp_ordersig){
														if(arr_tmp_ordersig[cx] && typeof(arr_tmp_ordersig[cx])!="undefined" && arr_tmp_ordersig[cx]!=""){
															strSigOpts +="<option value=\""+arr_tmp_ordersig[cx]+"\">"+arr_tmp_ordersig[cx]+"</option>";
														}
													}
													
													if(strSigOpts!=""){
														strSigOpts = "<select id=\"elem_ocu_med_sig"+c+k+"\" class=\"form-control\" ><option value=\"\"></option>"+strSigOpts+"</select>";
													}										
												}
												//text--
												strSigOpts += "&nbsp;&nbsp;<input type=\"text\" id=\"elem_ocu_med_sig_other"+c+k+"\" value=\"\" class=\"form-control\"  placeholder=\"Sig\">";
												//str_sig --
												
												
												var tmp_order002 ="<input type=\"checkbox\" id=\"elem_app"+c+k+"\" name=\"elem_app"+c+"\" value=\""+arshw+"\" hspace=\"23\" data-order_det_id=\""+arshid+"\" data-order_set_id=\""+ordersetid+"\" data-j=\""+k+"\" data-ordernm=\""+ordernm+"\" data-sig_s=\""+ordersig_s+"\" data-sig_s_loc=\""+ordersig_s_loc+"\"  ><label for=\"elem_app"+c+k+"\" >"+arshw+"</label>";
												
												
												if(tmp_dup_med_ordr.indexOf(ordernm)==-1){	
													tmp_dup_med_ordr[tmp_dup_med_ordr.length] = ordernm;
												
													pntr="<span class=\"pointer ui-icon ui-icon-blank002\" ></span>";
													
													if(data.dup_med_order[as_full] && data.dup_med_order[as_full][ordernm] && data.dup_med_order[as_full][ordernm]>1){
														pntr= "<span class=\"pointer  ui-icon ui-icon-circle-arrow-e\" onmouseover=\"showrelorders('"+ordernm+"',1, this)\" onmouseout=\"showrelorders('"+ordernm+"',0)\" ></span>";
														
														//make div for more options	
														var tmphtm = tmp_dup_med_ordr_htm[ordernm]||"";
														tmphtm = tmphtm + tmp_order002+"&nbsp;&nbsp;"+strEyeOpts+"&nbsp;&nbsp;"+strSigOpts+"<br/>";
														tmp_dup_med_ordr_htm[ordernm] = tmphtm;
														
														//make handle for childern orders --
														var tmp_order003 ="<input type=\"checkbox\" id=\"elem_app"+c+k+"master\" name=\"elem_app"+c+"master\" value=\""+ordernm+"\" hspace=\"23\"  onclick=\"checkrelorders(this)\"  ><label for=\"elem_app"+c+k+"master\" >"+ordernm+"</label>";													
														tmp_order003 +="&nbsp;&nbsp;"+pntr+"&nbsp;&nbsp;"; //<br/>
														tmp_order+= "<div class=\"form-inline\">"+tmp_order003+"</div>";
														//--
														
														
														
													}else{												
														tmp_order002 +="&nbsp;&nbsp;"+pntr+"&nbsp;&nbsp;"+strEyeOpts+"&nbsp;&nbsp;"+strSigOpts+""; //<br/>
														tmp_order+= "<div class=\"form-inline\">"+tmp_order002+"</div>";
													}
												
												}else{
													
													
													
													//make div for more options	
													var tmphtm = tmp_dup_med_ordr_htm[ordernm]||"";
													tmphtm = tmphtm + tmp_order002+"&nbsp;&nbsp;"+strEyeOpts+"&nbsp;&nbsp;"+strSigOpts+""; //<br/>
													tmp_dup_med_ordr_htm[ordernm] = "<div class=\"form-inline\">"+tmphtm+"</div>";													
												}													
											}
										}
										
										k++;
									}								
								}
							}						
						}
						
						//make div of classified orders--
						var tmp_str="";
						for( var xordr in tmp_dup_med_ordr_htm){
							var tmphtm = tmp_dup_med_ordr_htm[xordr];
							if(typeof(tmphtm)!="undefined" && tmphtm!=""){
								///alert(xordr +"\n\n"+ tmphtm);								
								var xordr_var=xordr.replace(" ","_");
								tmp_str+="<div id=\"dv_relorders"+xordr_var+"\" class=\"relorders\" onmouseover=\"showrelorders('"+xordr+"',3)\" onmouseout=\"showrelorders('"+xordr+"',0)\" >"+tmphtm+"</div>";
							}
						}			
						tmp_order+=tmp_str;
						
						//*/
						//orders--
						
						//order_assess_admin--
						/*
						var tmp_order_admin ="";
						if(data.order_assess_admin&& data.order_assess_admin[x] && data.order_assess_admin[x].length>0){
							var aror = data.order_assess_admin[x];
							var lnor= aror.length;							
							for(var j=0;j<lnor;j++){
								
								if(aror[j] == null || typeof(aror[j][0]) == "undefined"){ continue; }
								
								var arshw = $.trim(aror[j][0]);
								var arshid = $.trim(aror[j][1]);
								var ordersetid = $.trim(aror[j][2]);
								if(typeof(ordersetid)=="undefined" || ordersetid==""){ ordersetid="0";  }
								if(arshw!="" && arshid!=""){
									
									//filter orders by name
									//if(arr_done_order.indexOf(arshw)!=-1){ continue; }
									
									/*tmp_order+="&bull;&nbsp;<label onclick=\"showOrderDetail('"+ap_num+"', '0', '"+arshid+"')\" style=\"cursor:pointer\">"+arshw+"</label>&nbsp;&nbsp;<label onclick=\"saveOrderDetail(3, '"+ap_num+"', '"+arshid+"','del')\" title=\"Delete\" style=\"cursor:pointer; font-weight:bold;color:purple;\">X</label><br/>";*-/
									tmp_order_admin+="<input type=\"checkbox\" name=\"elem_app"+c+"\" value=\""+arshw+"\" hspace=\"23\" data-order_det_id=\""+arshid+"\" data-order_set_id=\""+ordersetid+"\" >"+arshw+"<br/>";
								}
							}
						}
						*/
						//order_assess_admin--
						
						//insert orders -----
						//tmp += (tmp_order_admin!="") ? "<div id=\"divorderadmin"+ap_num+"\" style=\"margin-left:10px;\">"+tmp_order_admin+"</div>" : "" ;
						if(tmp_order_supli!="" || tmp_order!=""){
							
							var tmp002 = "<div id=\"divorder"+ap_num+"\" >";
							
							if(tmp_order!=""){
								tmp002 += tmp_order;
							}
							
							if(tmp_order_supli!=""){
								tmp002 += "<label class=\"boxhead suppordr\" >Supplemental Orders</label>"+tmp_order_supli;
							}							
							
							tmp002 +="</div>";
							
							tmp += tmp002;
						}else{
							tmp += "";
						}
						//insert orders -----
						
						
						tmp+="</td>";
						tmp+="</tr>";
						c++;
					}
					
					//
					/*
					if(typeof(data.ocumed)!="undefined"&&data.ocumed!=""){
						ptmed=data.ocumed;
						//document.write(ptmed);		
					}//*/					
					
					if(tmp!=""){
						
						//get Rx html
						var strdivRxHidden01 = "";
						if($("input[name=elem_resiHxReviewd][type=hidden]").length>0){
							var tmpCheck = ($("input[name=elem_resiHxReviewd]").val()==1) ? "checked" : "" ;							
							strdivRxHidden01 += "<input type=\"checkbox\" id=\"elem_resiHxReviewd_01\"  name=\"elem_resiHxReviewd_01\" value=\"1\"  "+tmpCheck+">"+
											"<label for=\"elem_resiHxReviewd_01\"  >Resident's hx reviewed, pt interviewed, examined. </label>";
						}						
						
						strdivRxHidden01 += "<div class=\"form-group\"><label>Rx HW</label> "+
										"<select id=\"elem_rxhandwritten_01\" name=\"elem_rxhandwritten_01\" title=\"Rx Handwritten\" class=\"form-control minimal\">"+
											"<option value=\"\"></option>"+
											"<option value=\"0\" >0</option>"+
											"<option value=\"1\" >1</option>"+
											"<option value=\"2\" >2</option>"+
											"<option value=\"3\" >3</option>"+
											"<option value=\"4\" >4</option>"+
											"<option value=\"5\" >5</option>"+
										"</select></div>";
						
						strdivRxHidden01 += "<div class=\"form-group\"><label>LAB HW</label> "+
										"<select id=\"elem_labhandwritten_01\" name=\"elem_labhandwritten_01\" title=\"LAB Handwritten\" class=\"form-control minimal\">"+
											"<option value=\"\"></option>"+
											"<option value=\"0\" >0</option>"+
											"<option value=\"1\" >1</option>"+
											"<option value=\"2\" >2</option>"+
											"<option value=\"3\" >3</option>"+
											"<option value=\"4\" >4</option>"+
											"<option value=\"5\" >5</option>"+
										"</select></div>";
										
						strdivRxHidden01 += "<div class=\"form-group\"><label>RAD HW</label> "+
										"<select id=\"elem_radhandwritten_01\" name=\"elem_radhandwritten_01\" title=\"RAD Handwritten\" class=\"form-control minimal\">"+
											"<option value=\"\"></option>"+
											"<option value=\"0\" >0</option>"+
											"<option value=\"1\" >1</option>"+
											"<option value=\"2\" >2</option>"+
											"<option value=\"3\" >3</option>"+
											"<option value=\"4\" >4</option>"+
											"<option value=\"5\" >5</option>"+
										"</select></div>";	
						
						
						var htm =  "<div id=\"divApPlan\" class=\"whtbox assignmen\" >"+
								"<figure onclick=\"funApPlan(0);\"><span class=\"glyphicon glyphicon-remove-sign\" ></span></figure>"+
								"<div class=\"asshead\" id=\"boxhead\"  >"+
									"<div class=\"row\">"+
								
									//"<span class=\"closeBtn\" onclick=\"document.getElementById('divApPlan').style.display='none';\"></span>"+
									"<div class=\"col-sm-3\"><h2>Assessment & Plan</h2></div>"+
									"<div class=\"col-sm-9 form-inline\">"+
									//--
									//"<span id=\"divRxHidden01\">"+
									strdivRxHidden01+
									//"</span>"+
									"</div>"+
									"</div>"+
									"<div class=\"clearfix\"></div>"+
									//--	
						
								"</div>"+
								"<div class=\"table-responsive boxcontent\" >"+
								"<table class=\"table table-bordered\" width=\"100%\">"+
								//"<tr><th>Assessment</th><th>Plan</th></tr>"+
								"";
						htm +=tmp; //content
						htm += "</table>";
						
						/*
						htm += "<div class=\"boxhead\" style=\"cursor:move;\" >"+
									"&nbsp;&nbsp;Patient Medication"+
								"</div>"+
								"<table width=\"100%\">"+
								"<tr><td colspan=\"2\">"+ptmed+"</td></tr>";
						
						htm +=	"</table>";
						//*/
						htm +=	"</div>";
						
						/*htm += "<div style=\"clear:both; width:100%; margin-bottom:5px; text-align:center;\">"+
									"<input type=\"radio\" value=\"ocular\" name=\"medication\" id=\"medOcular\">Ocular&nbsp;"+
									"<input type=\"radio\" value=\"\" name=\"medication\" id=\"medAll\" checked=\"checked\">All"+
								"</div><div>&nbsp;</div>";*/
						var btnRxPrt="";
						if(z_printbtnRx=="1"){btnRxPrt = "<input type=\"button\"  class=\"dff_button btn btn-info\" id=\"prntRxBtnId\" onClick=\"funApPlan(3)\" value=\"Rx Print\" align=\"bottom\" />&nbsp;";}		
						htm +=""+
								"<div class=\"dvbtn\" >"+
								"<input type=\"button\" class=\"dff_button btn btn-success\" id=\"btnAP_ok\" onClick=\"funApPlan(1)\" value=\"Done\"/> "+
								"<input type=\"button\"  class=\"dff_button btn btn-info\" id=\"prntMedBtnId\" onClick=\"funApPlan(2)\" value=\"Print Meds\" align=\"bottom\" />&nbsp;"+
								"<input type=\"button\"  class=\"dff_button btn btn-info\" id=\"prntMedsAdmn\" onClick=\"funApPlan(4)\" value=\"Meds Administered\" align=\"bottom\" />&nbsp;"+
								btnRxPrt+
								"<input type=\"button\"  align=\"center\" class=\"dff_button btn btn-danger\" id=\"btnAP_close\" onClick=\"funApPlan(0)\" value=\"Close\"/>"+
								"</div>"+
								"";
								
						htm += "</div>";						
						
						
						//console.log(htm);
						
						$("body").append(""+htm);
						$("#divApPlan input[type=checkbox], #divApPlan label[for]").addClass("frcb");
						
						$("#divApPlan").draggable({handle:".boxhead"});	
						//var ofs=$("#divWorkView").scrollTop();
						//$("#divApPlan").css({"top":(ofs+20)+"px"}).show();
						$("#divApPlan").show();
						$("#divApPlan .boxhead").each(function(){$(this).triggerHandler("mousedown");});
						//
						var dd_pro = new Array();
						dd_pro["listHeight"] = 300;
						dd_pro["noneSelected"] = "Select All";						
						
						if($(":input[name=selMed]").length>0){	$(":input[name=selMed]").multiSelect(dd_pro); }
						
						var tmp = ""+$("input[name=elem_rxhandwritten]").val();
						if(tmp != ""){ $("select[name=elem_rxhandwritten_01]").val(tmp); }
						
						var tmp = ""+$("input[name=elem_labhandwritten]").val();
						if(tmp != ""){ $("select[name=elem_labhandwritten_01]").val(tmp); }
						
						var tmp = ""+$("input[name=elem_radhandwritten]").val();
						if(tmp != ""){ $("select[name=elem_radhandwritten_01]").val(tmp); }
						
					}else{
						top.fAlert("Please enter an assessment.");	
					}	
				}				
				
				},'json');
	}else{
		top.fAlert("Please enter an assessment.");	
	}
	}else if(val==0){
		$(".OrderDetail, .OrderList").remove();	
		$("#divApPlan").remove();
	}else if(val==1){
		
		if((elem_per_vo == "1") || ((finalize_flag == "1") && (isReviewable != "1"))){	$("#divApPlan").remove();	return; }
		
		//
		if(typeof(flgEm)=="undefined"){
			//Go to emdeon checks	
			cpoe_emdeoncheck();
			
		}else{		
		
		//---
		$("#elem_resiHxReviewd").val(0);	
		if($("#elem_resiHxReviewd_01").prop("checked")){$("#elem_resiHxReviewd").val(1);$("#assessplan #spnRxHxRvd").addClass("active");}else{$("#assessplan #spnRxHxRvd").removeClass("active");}
		var tmp = ""+$("#elem_rxhandwritten_01").val();
		$("#elem_rxhandwritten").val(tmp);
		var tmp = ""+$("#elem_labhandwritten_01").val();
		$("#elem_labhandwritten").val(tmp);
		var tmp = ""+$("#elem_radhandwritten_01").val();
		$("#elem_radhandwritten").val(tmp);
		//---		
		
		//	
		//insert
		var oapa = gebi("elem_apa",1);
		var lnapa = oapa.length;
		for(var a=0;a<lnapa;a++){
			var tmp = "";var cmnsep="~cmnsep~";
			var strAssess=oapa[a].value;
			var oapp = gebi("elem_app"+a,1);
			var lnapp = oapp.length;			
			var a_n = parseInt(a)+1;
			if(oapa[a] && $(oapa[a]).length>0){ 
				if($(oapa[a]).parent().length > 0 && typeof($(oapa[a]).parent()[0].id) != "undefined"){ a_n = $(oapa[a]).parent()[0].id.replace(/td_appu_/, ""); }
			}
			
			for(var b=0;b<lnapp;b++){
				var t = $.trim(""+oapp[b].value);				
				if(t!=""){
					var remflg=1;
					
					//if(oapp[b].checked){
						if(tmp!="" ) tmp += "\n";
						
						var oOdrJ = $(oapp[b]).attr("data-j");						
						
						//Get Sig--
						var el_oapp_sig = oapp[b].name.replace("elem_app", "elem_ocu_med_sig");
						var val_oapp_sig = $("#"+el_oapp_sig+oOdrJ+"").val();
						val_oapp_sig=$.trim(val_oapp_sig);
						if(typeof(val_oapp_sig)=="undefined"){ val_oapp_sig=""; }
						//other sig
						var el_oapp_sig_othr = oapp[b].name.replace("elem_app", "elem_ocu_med_sig_other");
						var val_oapp_sig_othr = $("#"+el_oapp_sig_othr+oOdrJ+"").val();
						val_oapp_sig_othr=$.trim(val_oapp_sig_othr);
						if(typeof(val_oapp_sig_othr)=="undefined"){ val_oapp_sig_othr=""; }
						else{ if(val_oapp_sig!=""){val_oapp_sig+=", ";} val_oapp_sig+=val_oapp_sig_othr; }
						
						if(val_oapp_sig!=""){
							val_oapp_sig=val_oapp_sig.replace(/[\r\n]/g,"");
							//get order nm
							var oOdrNm = $(oapp[b]).attr("data-ordernm");
							if(typeof(oOdrNm)=="undefined"){ oOdrNm=""; }
							if(oOdrNm!=""){//add site value	
								var oOdrSigSLoc = $(oapp[b]).attr("data-sig_s_loc");
								if(typeof(oOdrSigSLoc)!="undefined" && oOdrSigSLoc!=""){
									t = ""+oOdrSigSLoc.replace("SIGLOCATION",""+val_oapp_sig+"");
								}else{
									//if Single Sig exists and Other is added, then take Other and remove existing.
									var oOdrSigS = $(oapp[b]).attr("data-sig_s");
									if(typeof(oOdrSigS)!="undefined" && oOdrSigS!=""){t = t.replace(oOdrSigS,"");}
									
									t = t.replace(oOdrNm,oOdrNm+" "+val_oapp_sig+"");
								}
								
							}
						}						
						
						//Get Site--
						
						var el_oapp_site = oapp[b].name.replace("elem_app", "elem_ocu_med_site");
						var val_oapp_site = $(":checked[name="+el_oapp_site+oOdrJ+"]").val();
						if(typeof(val_oapp_site)=="undefined"){ val_oapp_site=""; }			
						if(val_oapp_site!=""){
							//get order nm
							var oOdrNm = $(oapp[b]).attr("data-ordernm");
							if(typeof(oOdrNm)=="undefined"){ oOdrNm=""; }
							if(oOdrNm!=""){//add site value
								t = t.replace(oOdrNm,oOdrNm+" ("+val_oapp_site+")");
								
								//Add Date --
								t = add_dt_to_order(t);
							}
						}
						//val_oapp_site="";
						//--
						
						
						
						//tmp += t+cmnsep;
					if(oapp[b].checked){
						remflg=0;
					}
					
					//alert("check: "+strAssess+" - "+t);
					//if(tmp!=""){
						//Insert Assess and Plan
						//t=t+"\0";//
						
						//add special hidden character : it shows that values are adding from orders
						t=t+"\u00AD";
						if(tmp!=""&&tmp.indexOf(t+cmnsep)!=-1){ if(remflg==1){ continue; }}else{ tmp += t+cmnsep; } //checking same plan is processed then do not fire again
						top.fmain.addAssessOption(strAssess,t,0,"def",remflg,'','',a_n);
						if(oapp[b].checked){
							// if checked, apply order to chart note --
							var idOdr="";
							var oOdr = $(oapp[b]).attr("data-order_det_id");
							if(oOdr && ""+oOdr.length>0){ idOdr=""+oOdr;}
							//ordersetid
							var idOdrset="";
							var oOdrset = $(oapp[b]).attr("data-order_set_id");
							if(oOdrset && ""+oOdrset.length>0){ idOdrset=""+oOdrset;}							
							///						
							
							//Insert FU of assess plan
							setApPolFU("",strAssess,t,a_n,idOdr,idOdrset,val_oapp_site, val_oapp_sig,1); //insert if plan is previous order and site and sig values are null
						}						
					//}		
				}
			}	
			
			//conMed--			
			var tmp_conmed = 0; //($("input[name=elem_apConMeds"+a_n+"_dum]").prop("checked")) ? 1 : 0 ;
			$("#elem_apConMeds"+a_n+"").val(tmp_conmed);
			//conMed--			
		}
		funInsMed();//insert medication
		$("#divApPlan").remove();
		
		}
		
	}else if(val==2){
		printPlanFun(1);//Print Medication
	}else if(val==3){
		//printPlanFun(1);//Print Medication
		cpoe_printRx();
	}else if(val==4){		
		var form_id 	= document.getElementById('hidd_formId').value;
		// If selected show all the Meds ordered in all the Orders (Meds, Labs, Imaging/Rad….). with Administered check box next to them.  If Any medication in any Order has Today date, then automatically select the Administered check box.  They can select/unselect any meds they want to administered.  On Done add all the meds Administered in Medical Hx with DOS as the date.
		if(typeof(setProcessImg) != 'undefined' )setProcessImg(1,"divApPlan");
		$.get("requestHandler.php?elem_formAction=MedAdmnsrd&form_id="+form_id,function(d){
				if(typeof(setProcessImg) != 'undefined' )setProcessImg(0,"divApPlan");
				var htm=d;
				$("body").append(""+htm);
				$("#divMedAdmnrd").draggable({handle:".boxhead"});	
				//var ofs=$("#divWorkView").scrollTop();
				$("#divMedAdmnrd").show(); //css({"top":(ofs+20)+"px"})
				$("#divMedAdmnrd .boxhead").each(function(){$(this).triggerHandler("mousedown");});			
			});
	}else if(val==51||val==50){
		var form_id 	= document.getElementById('hidd_formId').value;
		if(val==51){
			var all_ids = [];
			$("#divMedAdmnrd :checked").each(function(){ all_ids[all_ids.length] = this.value;  });	
			if(all_ids.length<=0){
				top.fAlert("Please select any order!");				
			}else{
				
				var str_ids = all_ids.join(",");	
				if(typeof(setProcessImg) != 'undefined' )setProcessImg(1,"divMedAdmnrd");				
				$.get("requestHandler.php?elem_formAction=MedAdmnsrd&form_id="+form_id+"&or_det_id="+str_ids,function(d){
					if(typeof(setProcessImg) != 'undefined' )setProcessImg(0,"divMedAdmnrd");					
					if(d==1){top.fAlert("Orders are moved to medical Hx.!");}else{ console.log(d); }
					$("#divMedAdmnrd").remove();	
				});
				
			}			
		}else{
			$("#divMedAdmnrd").remove();
		}	
	}
	
}

//--- Order set for chart notes -----
function set_order_pop_up(num,FrmId){
	var FrmId;
	if(FrmId=='')
	{
	   top.fAlert('Please Open New Chart Note!');
	   return false;
	}
	
	var width = screen.width, height = screen.height;
	var left = 0;
	var top = 0;
	var styleStr = 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,width=1280,height='+height;
	var asses_val = gebi("elem_assessment"+num).value;
	asses_val = encodeURIComponent(asses_val);
	oPUF["order_set"] = window.open('chart_notes_order_set.php?asses_val='+asses_val+'&plan_num='+num,'order_set',styleStr);
	oPUF["order_set"].moveTo(0,0);
	oPUF["order_set"].focus();  
//	window.open('chart_notes_order_set.php?asses_val='+asses_val+'&plan_num='+num,'order_set',styleStr);
}

//insert orders --
function showOrdersInAP(obj,c, o_type, srch ){
	if((elem_per_vo == "1") || ((finalize_flag == "1") && (isReviewable != "1"))){	return; }
	if(typeof(o_type)!="undefined" && o_type!=""){
		var ordr_type = o_type;
		var srch = "&srch="+srch;	
	}else{
		$(".OrderDetail, .OrderList").remove();
		var ordr_type = $.trim(obj.innerText);
		var srch='';
	}
	
	if(""+ordr_type.toLowerCase()=="info/inst"){ ordr_type = "Information/Instructions"; }
		
	//if(ordr_type == "Order Sets"){ 
	if(ordr_type == "ORDER SETS"||ordr_type == "Order Sets"){ 
		var fid = $("#elem_masterId").val();
		set_order_pop_up(c, fid);
	}else{
		
		
		var prm="requestHandler.php?elem_formAction=showOrdersInAP&ordertype="+ordr_type+"&assi="+c+srch ;
		$.get(""+prm, function(data){			
				//$("#divApPlan").append(data);
				//console.log(data);
				if(typeof(o_type)!="undefined" && o_type!=""){
					$( ".OrderList .purple_bar" ).nextAll().remove();
					$( ".OrderList .purple_bar" ).after(data);
				}else{			
					var opos_top = $("#divApPlan").css("top");
					var opos_left = $("#divApPlan").css("left");				
					$("body").append(""+data);						
					$(".OrderList").css({"top":opos_top+""}).draggable({handle:".boxhead"});
					$(".OrderList .boxhead").css({"cursor":"move"});
					$("#elem_searchOrders").trigger("focus");
				}
				$(".OrderList :checkbox, .OrderList label[for]").addClass("frcb");
				
			});
	}	
}

function searchOrders(obj, ordr_type, c){	
	var search= $.trim(obj.value);
	showOrdersInAP('',c, ordr_type, search );
}

var showrelorders_flg;
function showrelorders(id, flg,obj){
	if(flg==3){		
		clearTimeout(showrelorders_flg);
		showrelorders_flg=null;
	}else if(flg==2){		
		 if(showrelorders_flg){
			var xordr_var=id.replace(" ","_");
			$("#dv_relorders"+xordr_var).hide();
			clearTimeout(showrelorders_flg);
			showrelorders_flg=null;	
		 }	 
	}else if(flg==1){
		var xordr_var=id.replace(" ","_");
		$(".relorders").not("#dv_relorders"+xordr_var).hide();
		$("#dv_relorders"+xordr_var).show().position({my:"left top", at:"right bottom", of:obj});
	}else{		
		if(!showrelorders_flg){
			showrelorders_flg = setTimeout(function(){ showrelorders(id, 2,obj); }, 500);
		}
	}
}

//
function checkrelorders(obj){ 	var id = obj.value;	var xordr_var=id.replace(" ","_");	$("#dv_relorders"+xordr_var+" input[type=checkbox]").each(function(){ this.checked = (obj.checked) ? true : false;  }); }

//show details
function showOrderDetailStart(c,ordertype){	
	var oid="";
	$("#divOrderList :checked[id*=elem_inorder]").each(function(indx){
			if(oid!=""){oid+=",";}
			oid+=this.value;			
		});	
	if(oid==""){top.fAlert("Please select an order.");return;}	
	showOrderDetail(c, oid,0,ordertype);
}

//show details
function showOrderDetail(c, oid,coid, ordertype){	
	$(".OrderDetail, .OrderList").remove();
	//alert(c+", "+oid+", "+coid);
	
	if(typeof(coid)=="undefined"||coid=="0"){ coid="";  }
	if(typeof(oid)=="undefined"||oid=="0"){ oid="";  }
	if(typeof(flgdel)=="undefined"){ flgdel="";  }
	if(typeof(ordertype)=="undefined"){ ordertype="";  }
	//elem_formAction=showOrderDetail&oid="+oid+"&assi="+c+"&coid="+coid+"&c_otype="+ordertype
	var assi = c;
	var z_orderId = oid;			
	var chart_ordr_id = coid;	
	var c_otype = ordertype;
	
	var form_id = '';
	
	
	//console.log(assi,z_orderId,chart_ordr_id,c_otype);
	
	var str="";
	if((z_orderId!='')||(chart_ordr_id!='')){
		//*				
		//include_once($GLOBALS['incdir']."/admin/order_sets/Order/orderform.php");				
		if((c_otype!='') && c_otype=="Meds" && (z_orderId!='') && (chart_ordr_id=='')){
			//med order only	
			//../admin/order_sets/Order/popup_medorders.php
			str = ""+"requestHandler.php?elem_formAction=GetOrderDetail&req_ptwo=1&mode=med&id="+z_orderId+"&callFrom=WV&assi="+c;
			/*
			str = ""+
			"<div id='divpopupWV' class=\"panel panel-default\" >"+			
			"<div class='panel-heading boxhead' id='divHeader' ><span class='glyphicon glyphicon-remove pull-right' onClick=\"$('#divpopupWV').remove()\"></span>Orders</div>"+			
			"<div id=\"loadingdiv\" class=\"alert alert-info\" >Loading! Please wait. </div>"+
			"<iframe id='frame_order' src='requestHandler.php?elem_formAction=GetOrderDetail&mode=med&id="+z_orderId+"&callFrom=WV&assi="+c+"' frameBorder=0 scrolling='no' width='100%' ></iframe>"+				
			"</div>";
			*/
			
		}else{
		//../admin/order_sets/Order/popup.php	
		str = ""+"requestHandler.php?elem_formAction=GetOrderDetail&req_ptwo=1&id="+z_orderId+"&callFrom=WV&assi="+c+"&form_id="+form_id+"&chart_order_id="+chart_ordr_id;
		/*
		str = ""+
		"<div id='divpopupWV' class=\"panel panel-default\" >"+
		"<div class='panel-heading boxhead' id='divHeader' ><span class='glyphicon glyphicon-remove pull-right' onClick=$('#divpopupWV').remove()></span>Orders</div>"+
		"<div id=\"loadingdiv\" class=\"alert alert-info\">Loading! Please wait. </div>"+
		"<iframe id='frame_order' src='requestHandler.php?elem_formAction=GetOrderDetail&id="+z_orderId+"&callFrom=WV&assi="+c+"&form_id="+form_id+"&chart_order_id="+chart_ordr_id+"' frameBorder=0 scrolling='no' width='100%'></iframe>"+			
		"</div>";
		*/
		}
		
		
		
		//*/
	}else if(c!=''){
		//../admin/order_sets/Order/popup.php
		str = ""+"requestHandler.php?elem_formAction=GetOrderDetail&req_ptwo=1&callFrom=WV&assi="+c+"&form_id="+form_id+"&c_otype="+c_otype;
		/*str = ""+
		"<div id='divpopupWV' class=\"panel panel-default\" >"+
		"<div class='panel-heading boxhead' id='divHeader' ><span class='glyphicon glyphicon-remove pull-right' onClick=$('#divpopupWV').remove()></span>Orders</div>"+
		"<div id=\"loadingdiv\" class=\"alert alert-info\">Loading! Please wait. </div>"+
		"<iframe id='frame_order' src='"++"'  frameBorder=0 scrolling='no' width='100%'></iframe>"+
		"</div>";*/
	}
	
	if(str!=''){
		str = ""+
		"<div id='divpopupWV' class=\"panel panel-primary\" >"+
		"<div class='panel-heading purple_bar' id='divHeader' ><span class='glyphicon glyphicon-remove pull-right' onClick=$('#divpopupWV').remove()></span>Orders</div>"+
		"<div id=\"loadingdiv\" class=\"alert alert-info\">Loading! Please wait. </div>"+
		"<iframe id='frame_order' src='"+str+"'  frameBorder=0 scrolling='auto' width='100%'></iframe>"+
		"</div>";
		$("body").append(""+str);	
		$("#divpopupWV").draggable({handle:"#divHeader"});
	}
	
	
	/*
	var prm="requestHandler.php?elem_formAction=showOrderDetail&oid="+oid+"&assi="+c+"&coid="+coid+"&c_otype="+ordertype;
	//alert(prm);
	$.get(""+prm, function(data){	
		
			//$("#divApPlan").append(data);
			var opos_top = $("#divApPlan").css("top");
			var opos_left = $("#divApPlan").css("left");
		
			$("body").append(""+data);
			//$("#divpopup").css({"top":opos_top+""}).draggable({handle:"#divHeader"});
			$("#divpopupWV").draggable({handle:"#divHeader"});
			//$("#divpopup #divHeader").css({"cursor":"move"});
		
		});
	*/
}

function add_dt_to_order(str){
	//Add Date --
	var dts = $("#elem_masterUpdateDate").data("date-show");							
	if(typeof(dts)!="undefined" && dts!=""){str += " "+dts; }
	return str;
}

function del_order_from_plan(str,strtmp){	
	var ptrn = ""+str;
	ptrn = ptrn.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&'); 
	ptrn = ""+ptrn+"(\\s*\\d{2}\-\\d{2}\-\\d{4})?"+"";
	var patt = new RegExp(ptrn);						
	strtmp = strtmp.replace(patt,"");
	return strtmp;	
}

function saveOrderDetail(flgsave, c, coid, flgDel, cnfrm){
	
	if(flgsave=="3"){
			
		if(typeof(cnfrm)=="undefined"){
			top.fancyConfirm("Are you sure you want to delete this?","","top.fmain.saveOrderDetail('"+flgsave+"', '"+c+"', '"+coid+"', '"+flgDel+"', true);");
			return ;
		}
		
		//check if label exists in Plans, make it text
		if(delOrderDetail_obj!=""){			
			$("#"+delOrderDetail_obj).unbind("click", function(){ delOrderDetail(this) });
			$("label[for="+delOrderDetail_obj+"]").unbind("dblclick");
			$("#"+delOrderDetail_obj+"_lbl_del").remove();
			
			//var plntxt = $("label[for="+delOrderDetail_obj+"]").html();			
			//$("#"+delOrderDetail_obj+", #"+delOrderDetail_obj+"_lbl_del").remove();
			//$("label[for="+delOrderDetail_obj+"]").replaceWith(plntxt);
			
			delOrderDetail_obj="";
		}
		//check if label exists in Plans, make it text
		
		
		if(typeof(flgDel)=="undefined"){ flgDel=""; }
		
		if(typeof(coid) != "undefined" && coid!=""){	
			
		var strOrderDetails = {"order_set_associate_details_id":coid, "elem_delete":1};		
		$.post("saveCharts.php?elem_saveForm=SaveOrdersDetails", strOrderDetails, function(data) {
				//console.log(data);
				//update orders in div_c : pending			
				var strtmp= $("#elem_plan"+c).val();			
				if(data.old_summ && data.old_summ!=""){ //Delete Old Order Summ in plan										
					data.old_summ = $.trim(data.old_summ);
					if(strtmp.indexOf(""+data.old_summ)!=-1){
						strtmp = del_order_from_plan(""+data.old_summ,strtmp);
					}
				}
				strtmp = $.trim(strtmp);				
				$("#elem_plan"+c).val(strtmp).triggerHandler("keyup");
			
				updateOrderDetail(c);
			},"json");
		}
		return;
	}
	
	if(flgsave=="1" || flgsave=="2"){ //Save		
		//order name (site)(Instruction) - Option Optionname
		var strOrderDetails = $("#frmOrderDetail").serialize();		
		$.post("saveCharts.php?elem_saveForm=SaveOrdersDetails", strOrderDetails, function(data) {
			
			//
			if(data.order_sum && data.order_sum!=""){
				
				var strtmp= $("#elem_plan"+data.plan_num).val();
				if(data.old_summ && data.old_summ!=""){ //Delete Old Order Summ in plan
					strtmp = strtmp.replace(data.old_summ,"");
				}
				strtmp = $.trim(strtmp);
				
				if(data.order_type && data.order_type == "Meds"){
					if(strtmp!=""){strtmp="\n"+strtmp;}	
					strtmp = data.order_sum+strtmp;					
				}else{
					if(strtmp!=""){strtmp=strtmp+"\n";}	
					strtmp = strtmp+data.order_sum;
				}
				
				$("#elem_plan"+data.plan_num).val(strtmp).triggerHandler("keyup");
				
				updateOrderDetail(data.plan_num);
			}
			
		},"json");
	}
	if(flgsave=="2"){
		if(top.$("#tl_erx .icon24_eRx").length>0){			
			if(typeof top.fmain.open_erx != "undefined")top.fmain.open_erx(); // will check it later
		}
	}
	
	$('.OrderDetail').remove();	
}

function saveOrderDetail_new(form,flgsave, c, coid, flgDel){
	if(flgsave=="1" || flgsave=="2"){		
		var ofrm = (top.fmain.frame_order.contentWindow) ? top.fmain.frame_order.contentWindow: top.fmain.frame_order;
		var tmpCKE=top.fmain.frame_order.CKEDITOR;
		if(top.fmain.frame_order.CKEDITOR==null || typeof(top.fmain.frame_order.CKEDITOR)=="undefined"){tmpCKE=(top.fmain.frame_order.contentWindow && top.fmain.frame_order.contentWindow.CKEDITOR) ? top.fmain.frame_order.contentWindow.CKEDITOR : null ;}		
		template_content = (tmpCKE) ? tmpCKE.instances['FCKeditor1'].getData() : "";		
		frm_data = $(form).serialize();
		frm_data += "&FCKeditor1="+escape(template_content);
		
			
		//Set P image
		if(typeof(ofrm.disProcessing) != 'undefined' ) ofrm.disProcessing(1);
		
		$.post("saveCharts.php?elem_saveForm=SaveOrdersDetails", frm_data, function(data1) {
			
				//var myWindow = window.open("","MsgWindow","width=200,height=100, resizable=1,scrollbars=1");
				//myWindow.document.write(""+data1);
				//Set P image
				if(typeof(ofrm.disProcessing) != 'undefined' ) ofrm.disProcessing(0);
			
				if(data1 && data1.cpoe_error && data1.cpoe_error!=""){
					//alert("test");
					
					var s =ofrm.$("#div_cpoe_error");
					//alert(typeof(top.fmain.frame_order)+" - "+typeof(top.fmain.frame_order.document.getElementById("div_cpoe_error")));
					if(s){ s.html("<div class=\"alert alert-warning\">Emdeon Warning !</div>"+data1.cpoe_error); 	ofrm.set_frame_height(); ofrm.set_emdeon_off(); }
					
				}else if(data1 && data1.cpoe_site_care && data1.cpoe_site_care!=""){
					var s =ofrm.$("#div_cpoe_error");
					if(s){ s.html(data1.cpoe_site_care); 	ofrm.set_frame_height(); ofrm.set_emdeon_off(); }
					
				}else {
				
				if(data1 && data1.length>0){
				var flg_plan_num=0;
				for(var xx in data1){
					var data = data1[xx];	
					//alert(xx+" - "+data1[xx]);
					
				//
				if(data.order_sum && data.order_sum!=""){
					var strtmp= $("#elem_plan"+data.plan_num).val();
					if(data.old_summ && data.old_summ!=""){ //Delete Old Order Summ in plan
						//strtmp = strtmp.replace(data.old_summ,"");
						strtmp = del_order_from_plan(""+data.old_summ, strtmp);
					}
					strtmp = $.trim(strtmp);
					
					
					if(data.order_type && data.order_type == "Meds" && 1==2){//stopped:TUFTS feedback:16-02-2015
						if(strtmp!=""){strtmp="\n"+strtmp;}	
						strtmp = data.order_sum+strtmp;					
					}else{
						if(strtmp!=""){strtmp=strtmp+"\n";}	
						var t = add_dt_to_order(data.order_sum);	
						strtmp = strtmp+t;
					}					
					
					$("#elem_plan"+data.plan_num).val(strtmp).triggerHandler("keyup");										
					
					if(flg_plan_num==0){	flg_plan_num=data.plan_num;	}					
					
				}
				
				}
				
				updateOrderDetail(flg_plan_num); //update order pop up
				}				
				
				
				$('.OrderDetail').remove();	
				$('#divpopupWV').remove();
				$(".OrderDetail, .OrderList").remove();
				
				}
				
			},"json");
	}else{
	top.show_loading_image('hide');
	}
}

//#assessplan .planbox
function updateOrderDetail(c){
	
	var asmt = $("#assessplan textarea[id=elem_assessment"+c+"]").val();
	var strDx= $("#assessplan textarea[id=elem_assessment_dxcode"+c+"]").val();	
	var form_id = document.getElementById('hidd_formId').value;	
	var prm="requestHandler.php?elem_formAction=updateOrderDetail&assi="+c+"&form_id="+form_id+"&asmt="+encodeURI(asmt)+"&strDx="+encodeURI(strDx);	
	//alert(prm);
	
	//get unchecked
	var ar_uchked=[];
	$("#td_appu_"+c+" #divorder"+c+"  input[type=checkbox][id*=elem_app]").each(function(){
			if(this.checked == false){ar_uchked[ar_uchked.length]=this.value;}
		});
	
	$.get(""+prm, function(data){			
			
			//alert(data);
			//var myWindow = window.open("","MsgWindow","width=200,height=100, resizable=1,scrollbars=1");
			//myWindow.document.write(""+data);
		
		
			//alert(data.length);
			var k=$("#td_appu_"+c+"  input[type=checkbox]").length || 0;
			if(k>0){
				var tk=0, k=0;
				$("#td_appu_"+c+"  input[type=checkbox][id*=elem_app]").each(function(){
						if(this.id && this.name){
							tk = this.id.replace(this.name,'');
							if(typeof(tk)!="undefined" && tk!=""){
								tk = parseInt(tk);
								if(k<tk){k=tk;}	
							}
						}
					});
				if(k>0){k+=1;}
			}		
		
			ap_num = c;
			var str=str_supli="";		
			if(data){	
				
				for(var z in data){					
					
					//alert(z+" - "+data[z]);
					
					if(data[z] && data[z].length){
						
						//for(var zz in data[z]){
							
							//alert(z+" - "+" -- "+data[z]);
							
							
						//	if(data[z][zz] && data[z][zz].length>0){
							
								var aror = data[z];
								var lnor= aror.length;
						
								//arr for duplicate name orders
								var tmp_dup_med_ordr=[];
								var tmp_dup_med_ordr_htm=[];
								
								for(var j=0;j<lnor;j++){
									
									if(aror[j] == null || typeof(aror[j][0]) == "undefined"){ continue; }
									
									if($.trim(aror[j][2])=="ORDER"){
										var arshw = $.trim(aror[j][0]);
										var arshid = aror[j][1];
										var ordersetid = $.trim(aror[j][3]);	
										var ordernm = $.trim(aror[j][4]);
										var ordersupli = $.trim(aror[j][5]);
										var ordersig="";										
										var ordersig_s="";
										var ordersig_s_loc="";
									}else{
										var arshw = $.trim(aror[j][0]);
										var arshid = aror[j][1];
										var ordersetid = $.trim(aror[j][2]);
										var ordernm = $.trim(aror[j][3]);
										var ordersupli = 0;
										var ordersig=$.trim(aror[j][4]);
										var ordersig_s=$.trim(aror[j][5]);
										var ordersig_s_loc=$.trim(aror[j][6]);
									}
									//
									if(typeof(ordersetid)=="undefined" || ordersetid==""){ ordersetid="0";  }
									
									//alert(" CC "+arshw+" pp "+arshid+" - "+ordersetid);
									//continue;
									
									//check applan in plan --								
									tmp_pln_chk="";
									if(arshw!="" && typeof($("#elem_plan"+ap_num).val())!="undefined" && $.trim(""+$("#elem_plan"+ap_num).val())!="" ){ //
										//if( $("#elem_plan"+ap_num).val().toLowerCase().indexOf(""+arshw.toLowerCase())!=-1){
										if(isStrExistsInPlan($("#elem_plan"+ap_num).val(), ""+arshw, aror[j][2], ordernm)){
											if(ar_uchked.length>0 && ar_uchked.indexOf(ordernm)!=-1){} //
											else{tmp_pln_chk="CHECKED";}
										}
									}
									//check applan in plan --
									
									//elem number less then 0ne
									var xc = parseInt(c)-1;
									xc = ""+xc+"00";//add so make id unique
									
									//check in plans--
									var flgNoshow=0;
									$("#divapplan"+ap_num+" span.color_red").each(function(){  
											var tmpchk=$.trim(""+this.innerText); 
											if(tmpchk==arshw){ 
												//check if label exists in Plans, make it link of order
												if($.trim(aror[j][2])=="ORDER"){
													if(ordersupli==1){ //if order is not in console
														var olbl = $(this).parent();
														var chkid=olbl.attr("for");
														$("#"+chkid).prop("checked", true).bind("click", function(){ delOrderDetail(this) });														
														olbl.bind("dblclick", function(){ showOrderDetail(ap_num, '0', arshid) });
														$( "&nbsp;&nbsp;<label  id=\""+chkid+"_lbl_del\"  onclick=\"saveOrderDetail(3, '"+ap_num+"', '"+arshid+"','del')\" title=\"Delete\" style=\"cursor:pointer; font-weight:bold;color:purple;display:none;\">X</label>").insertAfter( olbl );
													}
												}
												//check if label exists in Plans, make it link of order
												
												flgNoshow=1; 
											}
										});
									if(flgNoshow==1){continue;}	
									//check in plans--									
									
									//for filtering orders name wise
									//arr_done_order[arr_done_order.length]=arshw;
									/*tmp_order+="&bull;&nbsp;<label onclick=\"showOrderDetail('"+ap_num+"', '0', '"+arshid+"')\" style=\"cursor:pointer\">"+arshw+"</label>&nbsp;&nbsp;<label onclick=\"saveOrderDetail(3, '"+ap_num+"', '"+arshid+"','del')\" title=\"Delete\" style=\"cursor:pointer; font-weight:bold;color:purple;\">X</label><br/>";*/
									if($.trim(aror[j][2])=="ORDER"){
										if(ordersupli==1){ //if order is not in console
										str_supli+="<div class=\"form-inline\"><input type=\"checkbox\" id=\"elem_app"+xc+k+"\" name=\"elem_app"+xc+"\" value=\""+arshw+"\" hspace=\"23\" "+tmp_pln_chk+" onclick=\"delOrderDetail(this)\" ><label for=\"elem_app"+xc+k+"\" ondblclick=\"showOrderDetail('"+ap_num+"', '0', '"+arshid+"')\" >"+arshw+"</label>&nbsp;&nbsp;<label  id=\"elem_app"+xc+k+"_lbl_del\"  onclick=\"saveOrderDetail(3, '"+ap_num+"', '"+arshid+"','del')\" title=\"Delete\" style=\"cursor:pointer; font-weight:bold;color:purple;display:none;\">X</label></div>"; //<br/>	
										}else{
										str+="<div class=\"form-inline\"><input type=\"checkbox\" id=\"elem_app"+xc+k+"\" name=\"elem_app"+xc+"\" value=\""+arshw+"\" hspace=\"23\" "+tmp_pln_chk+" onclick=\"delOrderDetail(this)\" ><label for=\"elem_app"+xc+k+"\" ondblclick=\"showOrderDetail('"+ap_num+"', '0', '"+arshid+"')\" >"+arshw+"</label>&nbsp;&nbsp;<label  id=\"elem_app"+xc+k+"_lbl_del\"  onclick=\"saveOrderDetail(3, '"+ap_num+"', '"+arshid+"','del')\" title=\"Delete\" style=\"cursor:pointer; font-weight:bold;color:purple;display:none;\">X</label></div>"; //<br/>
										}
									}else{
										if(arshw!="" && arshid!=""){
											
											//strEyeOpts --
											var strEyeOpts = 	"<div class=\"form-inline div_eye_site\"><div class=\"radio\"><input type=\"radio\" name=\"elem_ocu_med_site"+xc+k+"\" id=\"elem_ocu_med_site_ou"+xc+k+"\" value=\"OU\" checked><label for=\"elem_ocu_med_site_ou"+xc+k+"\"><b class=\"ou\">OU</b></label></div>&nbsp;"+
															"<div class=\"radio\"><input type=\"radio\" name=\"elem_ocu_med_site"+xc+k+"\" id=\"elem_ocu_med_site_od"+xc+k+"\" value=\"OD\" ><label for=\"elem_ocu_med_site_ou"+xc+k+"\"><b class=\"od\">OD</b></label></div>&nbsp;"+
															"<div class=\"radio\"><input type=\"radio\" name=\"elem_ocu_med_site"+xc+k+"\" id=\"elem_ocu_med_site_os"+xc+k+"\" value=\"OS\" ><label for=\"elem_ocu_med_site_ou"+xc+k+"\"><b class=\"os\">OS</b></label></div></div>&nbsp;";
											//strEyeOpts --
											
											//str_sig --									
											var strSigOpts ="";
											if(typeof(ordersig)!="undefined" && ordersig!=""){
												strSigOpts ="";
												var arr_tmp_ordersig = ordersig.split("\n");
												for(var cx in arr_tmp_ordersig){
													if(arr_tmp_ordersig[cx] && typeof(arr_tmp_ordersig[cx])!="undefined" && arr_tmp_ordersig[cx]!=""){
														strSigOpts +="<option value=\""+arr_tmp_ordersig[cx]+"\">"+arr_tmp_ordersig[cx]+"</option>";
													}
												}
												
												if(strSigOpts!=""){
													strSigOpts = "<select id=\"elem_ocu_med_sig"+xc+k+"\" class=\"form-control\" ><option value=\"\"></option>"+strSigOpts+"</select>";
												}										
											}
											strSigOpts += "&nbsp;&nbsp;<input type=\"text\" id=\"elem_ocu_med_sig_other"+xc+k+"\" value=\"\"  placeholder=\"Sig\" class=\"form-control\" >";
											//str_sig --
											
											var tmp_pln002 ="<input type=\"checkbox\" id=\"elem_app"+xc+k+"\" name=\"elem_app"+xc+"\" value=\""+arshw+"\" hspace=\"23\" data-order_det_id=\""+arshid+"\" data-order_set_id=\""+ordersetid+"\" data-j=\""+k+"\" data-ordernm=\""+ordernm+"\" data-sig_s=\""+ordersig_s+"\" data-sig_s_loc=\""+ordersig_s_loc+"\"  ><label for=\"elem_app"+xc+k+"\" >"+arshw+"</label>";
											
											//tt
											if(tmp_dup_med_ordr.indexOf(ordernm)==-1){									
												tmp_dup_med_ordr[tmp_dup_med_ordr.length] = ordernm;
												
												pntr="<span class=\"pointer ui-icon ui-icon-blank002\" ></span>";
												if(data.dup_med_order && data.dup_med_order[ordernm] && data.dup_med_order[ordernm]>1){
													pntr= "<span class=\"pointer  ui-icon ui-icon-circle-arrow-e\" onmouseover=\"showrelorders('"+ordernm+"',1, this)\" onmouseout=\"showrelorders('"+ordernm+"',0)\" ></span>";
													
													//make div from more options	
													var tmphtm = tmp_dup_med_ordr_htm[ordernm]||"";										
													tmphtm = tmphtm + tmp_pln002+"&nbsp;&nbsp;"+strEyeOpts+"&nbsp;&nbsp;"+strSigOpts+""; //<br/>
													tmp_dup_med_ordr_htm[ordernm] = "<div class=\"form-inline\">"+tmphtm+"</div>";
													
													var tmp_pln003 ="<input type=\"checkbox\" id=\"elem_app"+xc+k+"master\" name=\"elem_app"+xc+"master\" value=\""+ordernm+"\" hspace=\"23\"   onclick=\"checkrelorders(this)\" ><label for=\"elem_app"+xc+k+"master\" >"+ordernm+"</label>";
													tmp_pln003 += pntr+"&nbsp;&nbsp;"; //<br/>
													str+= "<div class=\"form-inline\">"+tmp_pln003+"</div>";

													//console.log("TEST");		
													
												}else{
													tmp_pln002 += pntr+"&nbsp;&nbsp;"+strEyeOpts+"&nbsp;&nbsp;"+strSigOpts+""; //<br/>
													str+= "<div class=\"form-inline\">"+tmp_pln002+"</div>";	
												}
												
											}else{
												//make div from more options	
												var tmphtm = tmp_dup_med_ordr_htm[ordernm]||"";										
												tmphtm = tmphtm + tmp_pln002+"&nbsp;&nbsp;"+strEyeOpts+"&nbsp;&nbsp;"+strSigOpts+""; //<br/>
												tmp_dup_med_ordr_htm[ordernm] = "<div class=\"form-inline\">"+tmphtm+"</div>";
											}											
										}
									}
									k++;
								}
							//}
							
						//}
					}
					
				}
				
				
			}

		
			//make div of classified orders--
			var tmp_str="";
			for( var xordr in tmp_dup_med_ordr_htm){
				var tmphtm = tmp_dup_med_ordr_htm[xordr];
				if(typeof(tmphtm)!="undefined" && tmphtm!=""){
					///alert(xordr +"\n\n"+ tmphtm);
					var xordr_var=xordr.replace(" ","_");
					tmp_str+="<div id=\"dv_relorders"+xordr_var+"\" class=\"relorders\" onmouseover=\"showrelorders('"+xordr+"',3)\" onmouseout=\"showrelorders('"+xordr+"',0)\" >"+tmphtm+"</div>";								
				}
			}			
			str+=tmp_str;			
			
			if(str_supli!=""){
				str = str+"<label class=\"boxhead suppordr\" >Supplemental Orders</label>"+str_supli;
			}
			
			if($("#divorder"+c).length>0){
				$("#divorder"+c).html(""+str);
			}else{
				var str = "<div id=\"divorder"+c+"\" >"+str+"</div>";
				$("#td_appu_"+c).append(str);
			}
			
			//make
			$("#divorder"+c+" :checkbox, #divorder"+c+" label[for]").addClass("frcb");
			
		},"json");
}
//--

var delOrderDetail_obj="";//, delOrderDetail_obj_ordr="";
function delOrderDetail(obj){
	var nm = obj.id;
	if(obj.checked == false){
		
		//check if label exists in Plans, make it text
		delOrderDetail_obj=""; //delOrderDetail_obj_ordr="";
		if($(obj).parents("div[id*=divorder]").length<=0){  
			delOrderDetail_obj=nm;							
		}
		/*else{
			delOrderDetail_obj_ordr=nm;	
		}*/
		//--
		
		$("#"+nm+"_lbl_del").click();
	}
}

// attach orders to plan
//
function attachOrder2Chart(indx, ordrids){
	if(typeof(ordrids)=="undefined")ordrids="";
	if(typeof(indx)=="undefined"){indx="";}
	
	if($.trim(ordrids)==""){ return; }
	
	var url="requestHandler.php";
	var p={"elem_formAction":"attachOrder2Chart",
			"strIdOdr":""+ordrids,
			"a_n":indx
			};
		
	if(typeof(setProcessImg) != 'undefined' )setProcessImg("1","follow_up");
	
	//alert(indx+", "+ordrids);
	//return;			
			
	$.post(url,p,function(data){
		
		//alert(data);
		
		if(typeof(setProcessImg) != 'undefined' )setProcessImg("0","follow_up");
		
		if(data!="0"){
			//alert(data);
		}		
			
	},"text");
	
}

//-- ORDER / ORDER SET ---------


