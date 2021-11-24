// JavaScript Document
var icd10_unique_obj_id = '';
function icd10_split(val){return val.split( />>\s*/ );}
function icd10_extractLast(term){return icd10_split( term ).pop();}
function icd10_extractICD10(str){
	ICD10 = '';
	vARR 	= str.split('[ICD-10: ');
	if(vARR.length>1){
		ICD10_ARR	= vARR[1].split(',');
		if(ICD10_ARR.length>1){
			ICD10	= ICD10_ARR[0];
		}
	}
	return ICD10;
}
function bind_autocomp(obj,getDataFilePath,showMode,hiddenOBJ){
	var flg_LitSev=0;
	if(typeof(showMode)=='undefined'){showMode = '';}
	obj.bind("focus",function(){
		if($(obj).attr('id')!=icd10_unique_obj_id){
			icd10_unique_obj_id = $(obj).attr('id');
			delete (uV); delete (vARR); delete (ICD10_ARR); delete (ICD10); delete (uV1); delete (vARR1); delete (ICD10_ARR1); delete (laterality); delete (dX_code); delete (NewdX_code);
		}
	});
	obj.bind("click", function(){
		fv = obj.val();
		if(fv.substr(-1,1)=='-' && fv.substr(-2,2)!='--' && fv.substr(-3,3)!='-X-' && fv.substr(-3,3)!='-x-'){
			fv = fv.replace('-','');
		}else if(fv.substr(-2,2)=='--'){
			fv = fv.replace('--','');
		}else if(fv.substr(-3,3)!='-x-' || fv.substr(-3,3)!='-X-'){
			fv = fv.replace('-x-','');
			fv = fv.replace('-X-','');
		}
		obj.val(fv);
		obj.keydown();
	});
    
    var autoConfig = {
		source: function(request,response){// delegate back to autocomplete, but extract the last term		
			uV		= request.term;//UI selected value
			vARR 	= uV.split('[ICD-10: ');
			if(vARR.length>1){
				ICD10_ARR	= vARR[1].split(',');
				if(ICD10_ARR.length>1){
					ICD10	= ""+ICD10_ARR[0];
					request.term = ""+ICD10;
				}
			}else{
				delete ICD10;
				//work only in superbill
				if($("#tblSuperbill").length>0 && ""+uV.indexOf("-") != -1){ICD10 = ""+uV;}			
			}	
			
			//
			flg_LitSev=0;
			var str_data = "term="+request.term;
			var oe = this.element[0];
			if(oe.name.indexOf("elem_assessment_dxcode")!=-1 || $(oe).hasClass("dxallcodes")){
				str_data += "&show_pop=1";
				//var tmp_pos = parseInt(""+request.term.lastIndexOf('-')) + 1; 
				//if(request.term.length>3 && request.term.length<=10){
				//	if(request.term.length==tmp_pos || request.term.charAt(6) == "-"){
						//flg_LitSev=1;
				//	}
				//}
			}
			var chart_dos="";
			
			if(typeof($("#elem_dos").val())!="undefined" && (typeof(sb_testName) == "undefined" || sb_testName=="")  && (oe.name.indexOf("elem_assessment_dxcode")!=-1 || $(oe).hasClass("dxallcodes"))){
				chart_dos=$("#elem_dos").val();
			}else if(typeof($("#elem_examDate").val())!="undefined"){
				chart_dos=$("#elem_examDate").val();
			}else if(typeof($("#dos_id").val())!="undefined"){
				chart_dos=$("#dos_id").val();
			}else if(typeof($("#date_of_service").val())!="undefined"){
				chart_dos=$("#date_of_service").val();
			}else if(typeof($("#elem_chart_DOS").val())!="undefined"){
				chart_dos=$("#elem_chart_DOS").val();	
			}
			if(chart_dos!=""){
				str_data += "&chart_dos="+chart_dos;
			}
			//set value when search starts			
			if(typeof($(oe).data("val1stsrch"))=="undefined"){
				$(oe).data("val1stsrch",request.term);
			}
			
			//dxid
			if((typeof(ICD10) != 'undefined' && typeof(ICD10) == 'string' && ""+ICD10.substr(-1)=='-') || ($(oe).val().indexOf('>>') > 0)){
				var str_dx_id="";
				if(typeof($(oe).data("dxid"))!="undefined"){
					var srch_dxid = $.trim($(oe).data("dxid"));
					if(srch_dxid!=""){str_data += "&dxid="+srch_dxid;}
				}
			}
			
			//alert(str_data);
			//$('#temp_txt').val('Request= '+request.term);	
			$.ajax({				
				url: getDataFilePath,
				data : str_data,
				dataType: "json",
				success: function(availableTags){
					/*
					var rr = "";
						for(var x in availableTags['results']){ //ui['content'][1]
							rr+=x+"-"+availableTags['results'][x]+"<br>";
						}
					*/	
					//var oalert = window.open("","dd","width=200,height=200,resizable=1");					
					//oalert.document.write(typeof(availableTags)+"\n\r"+availableTags);
					//return;
					
					/*if(availableTags.length == 1){
						if(obj.val().indexOf('>>') > 0){
							obj.autocomplete("close");
						}
					}*/
					//$('#temp_txt1').val('Response= '+availableTags);
					
					
					if(typeof(availableTags.flg_LitSev)!="undefined" && (availableTags.flg_LitSev==1||availableTags.flg_LitSev==0)){
						flg_LitSev=availableTags.flg_LitSev;						
						//
						if(typeof(availableTags.icd10_dxdb)!="undefined"){							
							$(oe).data("valbakdb",availableTags.icd10_dxdb);
						}
						//
						if(typeof(availableTags.srchd_code)!="undefined"){
							$(oe).data("valsrchd",availableTags.srchd_code);
						}
						//
						if(typeof(availableTags.icd10_dxdesc)!="undefined"){							
							$(oe).data("valdxdesc",availableTags.icd10_dxdesc);
						}
						
						availableTags=availableTags.results;
					}					
					
					var tmp=0;
					if(vARR.length>1||($("#tblSuperbill").length>0 && ""+uV.indexOf("-") != -1)){request.term = '';}
					if(flg_LitSev==1){
						
						//alert("11");
						
						//var tmp_pos = parseInt(""+uV.lastIndexOf('-')) + 1; 
						//if(uV.length>3 && uV.length<=10){
						//	if(uV.length==tmp_pos || uV.charAt(6) == "-"){
								response(availableTags);
								tmp=1;
						//	}
						//}
						
					}
					
					if(tmp==0){						
						response($.ui.autocomplete.filter(availableTags,icd10_extractLast(request.term)));
					}
					
				}
			});
		},
		response: function( event, ui){
			
			//*			
			if(flg_LitSev){
				
				if(cn_isFulldxAdded(this)){
					//	
				}else{ //dx code is not full added : //big else starts
				
				//remove attributes
				$("#"+this.id).removeData( "val1stsrch" );	
				
				if($("#dialogLSS").length>0){	 $("#dialogLSS").remove();	}
				//*
				
				var rr="";				
				//var i=1;
				for(var i=0;i<3;i++){
					var ee="",ee1="";
					var lat_ct=0;				
					for(var x in ui['content'][i]){    //ui['content'][0]
						//rr+=x+"-"+ui['content'][x].value+"<br>";		
						lat_ct=parseInt(lat_ct)+1;
						var lat_ct_show="";				
						var tmp = ""+ui['content'][i][x];
						if(typeof(tmp)=="undefined" || tmp=="" || tmp=="undefined"){ continue; }
						var arr_tmp = tmp.split("-");
						var showtxt = $.trim(arr_tmp[0]); 
						var showval = $.trim(arr_tmp[1]); 
						var fldid = ""+this.id;
						//alert(fldid);
						//alert(tmp);
						if(i==0){ lat_ct_show="txt_lat"; }else if(i==1){ lat_ct_show="txt_sev";  }else if(i==2){ lat_ct_show="txt_stag";  }	
						ee += "<span id='txt_span_latt' onclick=\"cn_resetasval('"+fldid+"', '"+showval+"','"+i+"', this);\"></span>";
						if(i==0){
							if(showtxt.toLowerCase().indexOf("lid")!=-1){
								if(showtxt.toLowerCase().indexOf("right")!=-1){
									ee += "<tr><td  id='"+lat_ct_show+"_"+lat_ct+"' dbid='"+showval+"'  onclick=\"cn_resetasval('"+fldid+"', '"+showval+"','"+i+"', this);\">"+showtxt+"</td></tr>";//
								}else{
									ee1 += "<tr><td  id='"+lat_ct_show+"_"+lat_ct+"' dbid='"+showval+"' onclick=\"cn_resetasval('"+fldid+"', '"+showval+"','"+i+"', this);\">"+showtxt+"</td></tr>";//
								}
								continue;
							}
						}
						
						ee += "<tr><td  id='"+lat_ct_show+"_"+lat_ct+"' dbid='"+showval+"' onclick=\"cn_resetasval('"+fldid+"', '"+showval+"','"+i+"', this);\">"+showtxt+"</td></tr>";//
					}
					
					if(ee!=""){
						var hd="";
						if(i==0){ hd="Eye"; }else if(i==1){ hd="Stage";  }else if(i==2){ hd="Encounter";  }						
						if(i==0 && ee1!=""){//lids
							ee="<table id='LSS_od"+i+"' >"+"<tr><td><b>Right Eye</b></td></tr>"+ee+""+"</table>";
							rr+="<td>"+ee+"</td>";
							ee1="<table id='LSS_os"+i+"' >"+"<tr><td><b>Left Eye</b></td></tr>"+ee1+"</table>";
							rr+="<td>"+ee1+"</td>";
						}else{						
							ee="<table id='LSS"+i+"' >"+"<tr><td><b>"+hd+"</b></td></tr>"+ee+"</table>";
							rr+="<td>"+ee+"</td>";
						}
						
						if(i==1||i==2){ break; } //either severity or stage
					}
				}
				
				if(rr!=""){		
					var js_icd_data_str="";
					var js_icd_data_ee="";
					var full_dx_val=$.trim($("#"+fldid).data("valbakdb"));
					var final_dx_val=full_dx_val.toLowerCase();
					
					if( typeof(js_icd_data_arr)!="undefined" && typeof(js_icd_data_arr[final_dx_val])!="undefined"){
						for(var k=0;k<js_icd_data_arr[final_dx_val].length;k++){
							js_icd_data_str+=js_icd_data_arr[final_dx_val][k];
						}
					}
					if(js_icd_data_str!=""){
						js_icd_data_ee="<table id='LSk3'><tr><td style='padding:0px 0px 4px 0px;'><input type='hidden' id='fldid_chk' name='fldid_chk' value='"+fldid+"' ><b>Causative Factors</b></td></tr>"+js_icd_data_str+"</table>";
					}
					var obj_comm_div="";
					if(fldid.indexOf("elem_assessment_dxcode")!=-1){
						var fldid_comm_id = fldid.replace("_dxcode","");
						var obj_comm_exp = ($("#"+fldid_comm_id).val()).split(';');
						var obj_comm_val="";
						if(typeof(obj_comm_exp[2])!="undefined"){
							for(l=1;l<obj_comm_exp.length;l++){
								var obj_comm_exp_chk="";
								if(obj_comm_exp[l]!="" && typeof(obj_comm_exp[l])!="undefined"){
									obj_comm_exp_chk=$.trim(obj_comm_exp[l].toLowerCase());
									if(l==1 && obj_comm_exp_chk!='ou' && obj_comm_exp_chk!='od' && obj_comm_exp_chk!='os'){
									}else{
										if(obj_comm_val!=''){
											obj_comm_val += ';';
										}
										obj_comm_val += obj_comm_exp[l];
									}
								}
								
							}
						}
						var comm_width="410px";
						if(js_icd_data_ee!=""){
							comm_width="630px";
						}
						obj_comm_div="<table id='dvLssComm'style='padding:5px 0px 5px 0px;'><tr><td><b>Comment</b></td></tr><tr><td><textarea class='input_text_10' name='elem_ass_comm' id='elem_ass_comm' style='width:"+comm_width+"; height:40px;'>"+obj_comm_val+"</textarea></td></tr></table>";
					}
					if(fldid.indexOf("elem_assessment_dxcode")!=-1 || fldid.indexOf("elem_assessment")!=-1){
						var show_ass_id = fldid.replace("elem_assessment_dxcode","elem_assessment");
						if($("#"+show_ass_id).val()==""){
							var show_ass_val = ($("#"+fldid).val()).split(';');
						}else{
							var show_ass_val = ($("#"+show_ass_id).val()).split(';');
						}
							show_ass_val = (show_ass_val[0]).split('[');
					}else{
						var show_ass_id = fldid;
						var show_ass_val = ($("#"+show_ass_id).val()).split('[');
					}
					
					rr = "<div class=\"dvLss\"><table id=\"bigtbl\" border='0' width='100%'><tr valign='top'>"+rr+"</tr></table></div>";
					var dd = "<div id=\"dialogLSS\" title=\"Select ICD 10 Dx code\">"+
							"<input type=\"hidden\" id=\"pop_ass_flag\" value=\"1\" ><textarea class=\"input_text_10\" id='pop_"+show_ass_id+"' name='pop_elem_assessment[]' style=\"width:410px;height:20px;\">"+show_ass_val[0]+"</textarea>"+
							rr+"<input type=\"hidden\" id=\"olen\" value=\"1\" >"+
							js_icd_data_ee+obj_comm_div+"</div>";
					
					var del_btn_val =  "Delete Dx code";	
					if(fldid.indexOf("elem_assessment_dxcode")!=-1){
						del_btn_val = "Delete Assessment and Dx code";	
					}
					
					var show_staging_link_val=show_staging_link(show_ass_id);
					$("body").append(dd);
					
					//buttons
					var obtns=[];
					/*var obtns = [{
										text : ""+del_btn_val,
										click : function() {
										  $( this ).dialog( "close" );
										  $("#"+fldid).val(""); 	
										  if(fldid.indexOf("elem_assessment_dxcode")!=-1){
											  var fldid_tmp = fldid.replace("elem_assessment_dxcode","elem_assessment");
											  $("#"+fldid_tmp).val("");
										  }		
										}										
										}];*/
						if(show_staging_link_val!=""){				
							obtns[obtns.length] ={
								id: "dmb_staging_code",
								text : "Staging Code",
								click : function() {
									staging_div_view('show');
								}										
							}
						}
						
						obtns[obtns.length] ={
							text : "Done",
							'class':"ui-btn-sucus",
							click : function() {
							
								if($("#dialogLSS table[id*=LSk3]").length>0){
									$("#dialogLSS table[id*=LSk3] td").addClass("doneCF");
								}	
								
								if($("#dialogLSS table[id*=LSS_o]").length>0){												
									$("#dialogLSS table[id*=LSS_o] td").addClass("doneLPU");
									//alert($("#dialogLSS table[id*=LSS_o] td.doneCF").length);
								}		
								if($("#dialogLSS table[id*=LSS0]").length>0){
									$("#dialogLSS table[id*=LSS0] td").addClass("doneLPU");
								}else if($("#dialogLSS table[id*=LSS1]").length>0){
									$("#dialogLSS table[id*=LSS1] td").addClass("doneLPU");
								}else if($("#dialogLSS table[id*=LSS2]").length>0){
									$("#dialogLSS table[id*=LSS2] td").addClass("doneLPU");
								}
								
								if($("#dialogLSS td.highlight").length>0){
									$("#dialogLSS td.highlight").eq(0).triggerHandler("click");
								}else{
									if($("#elem_ass_comm").val()!=""){
										$("#dialogLSS td [id=txt_span_latt]").eq(0).triggerHandler("click");
									}
								}
								if($("#elem_ass_comm")){
									$("#elem_ass_comm").val('');
								}
								$( this ).dialog( "close" ); 
							}										
						}
						
						obtns[obtns.length] ={
							id: "dmb_reset",
							text : "Reset",
							click : function() {
								//$( this ).dialog( "close" ); 
								reset_assessment(fldid);
							}										
						}
						
						if(fldid.indexOf("elem_assessment_dxcode")!=-1){
							obtns[obtns.length] = {
								id : "dialog_add_mutli_dx",
								text : "Add Multiple Dx",
								display : "none",
								click : function() {
								  //$( this ).dialog( "close" );
								  
								  $("#dialogLSS").append("");	
								  $("#dialogLSS div.dvLss table#bigtbl").clone().appendTo("#dialogLSS  div.dvLss");
									//.		
								 // alert("3: "+$("#dialogLSS table#bigtbl").length);
								  
								  var len = $("#dialogLSS table[id*=bigtbl]").length;
								  var inx = len - 1;
								  var oo = $("#dialogLSS table[id*=bigtbl]").get(inx);
									oo.id = "bigtbl"+inx;
									$("#dialogLSS table[id*="+oo.id+"] td").removeClass("highlight highlight_2");
									//alert(oo.id);
									$("#dialogLSS #olen").val(len);
								  //$("#dialogLSS table#bigtbl").css({"background-color":"red;"}); 	
									
								}										
							}	
						}	
					
					if(js_icd_data_ee!=""){
						var dialogLSS_width='700';
					}else{
						var dialogLSS_width='450';
					}
					$( "#dialogLSS" ).dialog({
							close : function(){
							if($("#elem_ass_comm")){
								$("#elem_ass_comm").val('');
							}
						},buttons: obtns,minWidth: dialogLSS_width});
					$("#dialog_add_mutli_dx").hide();
					$("#dmb_reset").css({"float":"right"});
					$("#dmb_staging_code").css({"float":"left","background":"none","color":"purple","border":"none","font-weight":"bold"});
					
					//
					//
					cn_typeahead();
					//$("#elem_ass_comm").blur();	
					var SeverityArr =  ["Mild", "Moderate", "Severe", "Indeterminate", "Unspecified","Early","Intermediate","Adv atrophic w/o subfoveal Involvement","Adv atrophic w subfoveal Involvement","Active neovascularization","Inactive neovasculization","Inactive scar","w/ macular edema","w/ret neovaculaization","stable"];
					var StageArr =  ["Initial Encounter", "Subsequent Encounter", "Sequlae"];	
					var StageArr_Sec =  ["Initial Encounter Closed Fracture", "Initial Encounter Open Fracture", "Subsequent Encounter for Fracture with Routine Healing", "Subsequent Encounter for Fracture with Delayed Healing", "Subsequent Encounter for Fracture with Non Union", "Sequela"];	
					var StageArr_Small =  ["Initial", "Subsequent", "Sequlae"];	
					var StageArr_Sec_Small =  ["Closed", "Open", "Routine", "Delayed", "Union", "Sequela"];		      
					if($("#dialog_add_mutli_dx").length>0){						
						if(fldid.indexOf("elem_assessment_dxcode")!=-1){							
							var tt = $("#"+fldid).val();							
							var tt_as = fldid.replace("_dxcode","");
							var tt_as_v = $("#"+tt_as).val();							
							var ar_tt_as_v = tt_as_v.split(";");							
							var ar_tt_as_v_op=[];
							if(typeof(ar_tt_as_v[1])!="undefined"){ var ar_tt_as_v_1 = $.trim(""+ar_tt_as_v[1]);ar_tt_as_v_op = ar_tt_as_v_1.split(", "); }
							if(tt.indexOf("[ICD-10:")==-1){								
								var artt = tt.split(",");
								var lentt=artt.length;
								if(typeof(ar_tt_as_v_1)!="undefined"){//if lids opts, half the length
									if(""+ar_tt_as_v_1.toLowerCase().indexOf("lid")!=-1){
										//lentt=lentt/2;
										lentt=1;	
									}
								}	
							}else{									
								var artt =[tt];
								var lentt=1;	
							}							
							for(var t1=0;t1<lentt;t1++){
								if(typeof(ar_tt_as_v_op[t1])!="undefined"&&ar_tt_as_v_op[t1]!=""){
									
									var tmpspx=(t1==0)?"":t1;
									var ar_tt_as_v_op_last=replace_cid10_extra_string(ar_tt_as_v_op[t1]);
										ar_tt_as_v_op_last = ar_tt_as_v_op_last.split(" ");
									//alert(typeof(ar_tt_as_v_op_last[1]));
									var chk_stg_len=$("#dialogLSS table[id*=LSS1]").length;
									if((ar_tt_as_v_op_last[0]=="Right" || ar_tt_as_v_op_last[0]=="Left" || ar_tt_as_v_op_last[0]=="Both") && typeof(ar_tt_as_v_op_last[1])=="undefined" && chk_stg_len<1){
									}else if($.inArray(ar_tt_as_v_op[0],SeverityArr)!= -1){
										if(t1>0){									
											$("#dialog_add_mutli_dx").trigger("click");									
										}
										if(typeof(ar_tt_as_v_op_last[0])!="undefined" && ar_tt_as_v_op_last[0]!=""){
											$("#dialogLSS table[id*=bigtbl"+tmpspx+"] table[id*=LSS1] td").each(function(){
												var strchk = $(this).html();
												//alert(zt+" - "+strchk);
												if(typeof(strchk)!="undefined" && strchk!="" && ar_tt_as_v_op_last[0].indexOf(strchk)!=-1){
													var set_val=$(this).attr("dbid");
													//alert(set_val+'--'+strchk);
													$(this).addClass("highlight");
													$(this).data("vval",set_val);
												}else{
													$(this).removeClass("highlight");
												}	
											});
										}
									}else if($.inArray(ar_tt_as_v_op[0],StageArr)!= -1){
										if(t1>0){									
											$("#dialog_add_mutli_dx").trigger("click");									
										}
										/*if(ar_tt_as_v_op_last[0]=='Initial' || ar_tt_as_v_op_last[0]=='Subsequent'){
											ar_tt_as_v_op_last[0] = ar_tt_as_v_op_last[0]+' Encounter';
										}*/
										ar_tt_as_v_op_last[0]=replace_cid10_extra_string(ar_tt_as_v_op_last[0]);
										if(typeof(ar_tt_as_v_op_last[0])!="undefined" && ar_tt_as_v_op_last[0]!=""){
											$("#dialogLSS table[id*=bigtbl"+tmpspx+"] table[id*=LSS2] td").each(function(){
												var strchk = $(this).html();
												strchk=replace_cid10_extra_string(strchk);
												//alert(zt+" - "+strchk);
												if(typeof(strchk)!="undefined" && strchk!="" && ar_tt_as_v_op_last[0].indexOf(strchk)!=-1){
													var set_val=$(this).attr("dbid");
													//alert(set_val+'--'+strchk);
													$(this).addClass("highlight");
													$(this).data("vval",set_val);
												}else{
													$(this).removeClass("highlight");
												}	
											});
										}
									}else if($.inArray(ar_tt_as_v_op[0],StageArr_Sec)!= -1){
										if(t1>0){									
											$("#dialog_add_mutli_dx").trigger("click");									
										}
										ar_tt_as_v_op_last[0]=replace_cid10_extra_string(ar_tt_as_v_op[0]);
										if(typeof(ar_tt_as_v_op_last[0])!="undefined" && ar_tt_as_v_op_last[0]!=""){
											$("#dialogLSS table[id*=bigtbl"+tmpspx+"] table[id*=LSS2] td").each(function(){
												var strchk = $(this).html();
												strchk=replace_cid10_extra_string(strchk);
												//alert(ar_tt_as_v_op_last[0]+"-"+strchk);
												//alert(zt+" - "+strchk);
												if(typeof(strchk)!="undefined" && strchk!="" && ar_tt_as_v_op_last[0].indexOf(strchk)!=-1){
													var set_val=$(this).attr("dbid");
													//alert(set_val+'--'+strchk);
													$(this).addClass("highlight");
													$(this).data("vval",set_val);
												}else{
													$(this).removeClass("highlight");
												}	
											});
										}
									}else if(ar_tt_as_v_op[0].indexOf("Right eye category")!=-1||ar_tt_as_v_op[0].indexOf("Left eye category")!=-1){ //$.inArray(ar_tt_as_v_op[0],ICD10_CM)!= -1
										//?? why is this --
										if(t1>0){									
											$("#dialog_add_mutli_dx").trigger("click");									
										}
										//End ?? why is this --
										//highlight
										for(var zt=0;zt<2;zt++){		
											var lss_td_id=zt;
											if(typeof(ar_tt_as_v_op[t1])!="undefined" && ar_tt_as_v_op[t1]!=""){
												$("#dialogLSS table[id*=bigtbl"+tmpspx+"] table[id*=LSS"+lss_td_id+"] td").each(function(){
														var strchk = $(this).html();
														//strchk=replace_cid10_extra_string(strchk);
														//alert(zt+" - "+strchk+" - "+ar_tt_as_v_op_last[zt]);
														if(typeof(strchk)!="undefined" && strchk!="" && ar_tt_as_v_op[t1].indexOf(strchk)!=-1){
															var set_val=$(this).attr("dbid");
															$(this).addClass("highlight");
															$(this).data("vval",set_val);
														}else{
															$(this).removeClass("highlight");
														}
												});
											}
										}	
									}else{
										if(t1>0){									
											$("#dialog_add_mutli_dx").trigger("click");									
										}
										for(var zt=0;zt<3;zt++){		
											var lss_td_id=zt;
											//if(ar_tt_as_v_op_last[zt]=="Initial" || ar_tt_as_v_op_last[zt]=="Subsequent" || ar_tt_as_v_op_last[zt]=="Sequlae"){
											if($.inArray(ar_tt_as_v_op_last[zt],StageArr_Small)!= -1 || $.inArray(ar_tt_as_v_op_last[zt],StageArr_Sec_Small)!= -1){	
												var lss_td_id=2;
											}
											if(typeof(ar_tt_as_v_op_last[zt])!="undefined" && ar_tt_as_v_op_last[zt]!=""){
												$("#dialogLSS table[id*=bigtbl"+tmpspx+"] table[id*=LSS"+lss_td_id+"] td").each(function(){
														var strchk = $(this).html();
														strchk=replace_cid10_extra_string(strchk);
														//alert(zt+" - "+strchk+" - "+ar_tt_as_v_op_last[zt]);
														if(typeof(strchk)!="undefined" && strchk!="" && ar_tt_as_v_op_last[zt].indexOf(strchk)!=-1){
															var set_val=$(this).attr("dbid");
															//alert(set_val+'--'+strchk);
															$(this).addClass("highlight");
															$(this).data("vval",set_val);
														}else{
															$(this).removeClass("highlight");
														}	
												});
											}
										}
									}
								}
							}
							
							for(var t1=0;t1<3;t1++){
								if(typeof(ar_tt_as_v_op[t1])!="undefined"){
									var tmpspx=0;
									var ar_tt_as_v_op_last=replace_cid10_extra_string(ar_tt_as_v_op[t1]);
										ar_tt_as_v_op_last = ar_tt_as_v_op_last.split(" ");
									var chk_stg_len=$("#dialogLSS table[id*=LSS1]").length;	
									if((ar_tt_as_v_op_last[0]=="Right" || ar_tt_as_v_op_last[0]=="Left" || ar_tt_as_v_op_last[0]=="Both") && typeof(ar_tt_as_v_op_last[1])=="undefined" && chk_stg_len<1){
										if(typeof(ar_tt_as_v_op[t1])!="undefined" && ar_tt_as_v_op[t1]!=""){
											$("#dialogLSS table[id*=bigtbl] table[id*=LSS"+tmpspx+"] td").each(function(){
													var strchk = $(this).html();
														strchk=replace_cid10_extra_string(strchk);
													if(typeof(strchk)!="undefined" && strchk!="" && ar_tt_as_v_op[t1].indexOf(strchk)!=-1){
														var set_val=$(this).attr("dbid");
														//alert(set_val+'--'+strchk);
														$(this).addClass("highlight");
														$(this).data("vval",set_val);
													}else{
														//$(this).removeClass("highlight");
													}	
											});
										}
									}
								}
							}
							
							for(var t1=0;t1<4;t1++){
								var tmpspx=0;
								if(typeof(ar_tt_as_v_op[t1])!="undefined"){
									if(""+ar_tt_as_v_op[t1].toLowerCase().indexOf("lid")!=-1){
										if(typeof(ar_tt_as_v_op[t1])!="undefined" && ar_tt_as_v_op[t1]!=""){
											$("#dialogLSS table[id*=bigtbl] table[id*=LSS_od"+tmpspx+"] td").each(function(){
													var strchk = $(this).html();
													if(typeof(strchk)!="undefined" && strchk!="" && ar_tt_as_v_op[t1].indexOf(strchk)!=-1){
														var set_val=$(this).attr("dbid");
														//alert(set_val+'--'+strchk);
														$(this).addClass("highlight");
														$(this).data("vval",set_val);
													}else{
														//$(this).removeClass("highlight");
													}	
											});
											$("#dialogLSS table[id*=bigtbl] table[id*=LSS_os"+tmpspx+"] td").each(function(){
													var strchk = $(this).html();
													//alert(t1+" - "+strchk+" - "+ar_tt_as_v_op[t1]+" - "+ar_tt_as_v_op[t1].indexOf(strchk));
													if(typeof(strchk)!="undefined" && strchk!="" && ar_tt_as_v_op[t1].indexOf(strchk)!=-1){
														var set_val=$(this).attr("dbid");
														//alert(set_val+'--'+strchk);
														$(this).addClass("highlight");
														$(this).data("vval",set_val);
													}else{
														//$(this).removeClass("highlight");
													}	
											});
										}
									}
								}
							}
						}
					}
				}		
				//*/
				}//big else end
				//console.log("jQuery is not loaded"); 				
				$( this ).autocomplete( "close" );
				//alert("22");
			}		
			return false;
			//*/
		},
		focus: function(){// prevent value inserted on focuS
			return false;
		},
		close : function(event,ui){
			if(obj){				
				if((typeof(ICD10) != 'undefined' && typeof(ICD10) == 'string' && ""+ICD10.substr(-1)=='-') || (obj.val().indexOf('>>') > 0)){
					obj.autocomplete("search");
				}
			}
		},
		select: function(event,ui){
			if(typeof(hiddenOBJ)!='undefined' && hiddenOBJ.get(0)){hiddenOBJ.val('');}
			uV1		= ui.item.value;//UI selected value
			
            
            // For problem list in medical hx typeahead
            if(showMode=='mhx' && uV1.indexOf('[ICD-10: ')>-1) {
                uV=uV1;
            }
			vARR1 	= uV1.split('[ICD-10: ');
			if(vARR1.length>1){
				ICD10_ARR1	= vARR1[1].split(',');
				if(ICD10_ARR1.length>1){
					uV1	= ICD10_ARR1[0];
				}
			}
			laterality = uV1.substr((uV1.length-1),1);
			if(showMode!='ap'){
				if(uV1.indexOf('.')>0 && uV1.indexOf('-')<0){
					ICD10		= uV1;
				}else if(uV1.length>=3 && uV1.indexOf('-')<0){
					ICD10		= uV1;
				}
			}
			//$('#temp_txt2').val('UI_selected_val = '+uV1+', Lat='+laterality+'ICD10 = '+typeof(ICD10));
			if(laterality=='-'){
				laterality = false;
				var terms = icd10_split( this.value );
				terms.pop();
				terms.push(ui.item.value);
				terms.push("");
				this.value = terms.join(">>");
			}else if(typeof(ICD10) != 'undefined' && ICD10!=null && typeof(ICD10) == 'string'){
				//$('#temp_txt3').val('ICD10 = '+ICD10+', Curr_OBJ_val = '+this.value);
				dX_code 	= ""+ICD10.replace('-',laterality);
				if(flg_LitSev){					
				}else{
				FirstHyphen		= dX_code.indexOf("-");
				if(FirstHyphen >= 7){
					icd_value	= this.value;
					NewdX_code 	= ""+ICD10.replace('-',laterality);
					dX_code		= icd_value.replace(ICD10,NewdX_code);
					dX_code		= dX_code.replace('>>','');
					if(typeof(hiddenOBJ)!='undefined' && hiddenOBJ.get(0)){
						hiddenOBJ.val(NewdX_code);
					}
				}
				}
				if(showMode=='ap'){
					icd_value	= this.value;
					NewdX_code 	= ""+ICD10.replace('-',laterality);
					dX_code		= icd_value.replace(ICD10,NewdX_code);
					dX_code		= dX_code.replace('>>','');
					if(typeof(hiddenOBJ)!='undefined' && hiddenOBJ.get(0)){
						hiddenOBJ.val(NewdX_code);
					}
					//arr_dX_code	= dX_code.split('[ICD-10:');
					//dX_code		= arr_dX_code[0];
				}
                // For problem list in medical hx typeahead
                if(showMode=='mhx' && dX_code.indexOf('-')== -1) {
                    var prob_arr_dX_code	= uV.split('[ICD-10:');
                    var prob_dX_code=prob_arr_dX_code[0]+' (ICD-10-CM '+dX_code+')';
                    dX_code=prob_dX_code;
                }
				this.value 	= dX_code;
			}else{
				if(showMode=='ap'){
					this.value 	= ui.item.value;
					if(typeof(hiddenOBJ)!='undefined' && hiddenOBJ.get(0)){
						hiddenOBJ.val(uV1);
					}
				}else{
					FirstHyphen		= ui.item.value.indexOf("-");
					if(FirstHyphen >= 7){
						this.value 	= ui.item.value;
					}else{
						this.value 	= uV1;
					}
				}
			}
			
			//add dxid
			var dxid = (typeof(ui.item.id)!="undefined") ? ui.item.id : "" ;
			if(dxid!=""){$(this).data("dxid", dxid);}		
			
			return false;
		}
	}
    
    if(showMode=='mhx') { autoConfig['position'] = { my: "left bottom", at: "left top" } }
    
	obj.bind( "keydown", function(event){
		if(typeof(hiddenOBJ)!='undefined' && hiddenOBJ.get(0)){hiddenOBJ.val('');}
		if (event.keyCode === $.ui.keyCode.TAB && $(this).data("ui-autocomplete").menu.active){
			event.preventDefault();
		}
	}).autocomplete(autoConfig);
    
}
function cn_set_sub_dx(id,val_post){
	var nogo=1;
	if($("#fldid_chk")){
		$(".elem_sub_dx").each(function(){
			var chk_box_id=this.id;
			var chk_box_val = $('#'+chk_box_id).data("valbakdb");
			chk_box_val_exp=chk_box_val.split("-");
			if(chk_box_val_exp.length>1){ 
				var val = $("#dialogLSS #bigtbl table[id*=LSS0] td.highlight").data("vval"); 
				if(val!=null && val!=""&&typeof(val)!="undefined"){
					chk_box_val = chk_box_val.replace('-',val);
					nogo=0;
					$('#'+chk_box_id).val(""+chk_box_val);
				} 
			}else{
				if(typeof($('#LSS0').html())!="undefined"){
					var val = $("#dialogLSS #bigtbl table[id*=LSS0] td.highlight").data("vval"); 
					if(val!=null && val!=""&&typeof(val)!="undefined"){
						nogo=0;
					}
				}else{
					nogo=0;
				}
			}
		});
	}
	if(nogo==1){return;}
	
	var ass_id=fldid_chk.value;
	if(ass_id.indexOf("elem_assessment")!=-1 || ass_id.indexOf("elem_assessment_dxcode")!=-1){
		for(var j=1; j<=50; j++){
			var ass_new_id_num=j;
			if($('#elem_assessment'+ass_new_id_num).val()=="" && $('#elem_assessment_dxcode'+ass_new_id_num).val()==""){
				break;
			}
		}
		
		var ass_id="elem_assessment"+ass_new_id_num;
		var ass_dx_id="elem_assessment_dxcode"+ass_new_id_num;
		
		var chk_box_id="elem_sub_"+id;
		var chk_box_val = $('#'+chk_box_id).val();
		
		var chk_box_desc_id = "elem_sub_desc_"+id;
		var chk_box_desc_val = $('#'+chk_box_desc_id).val();
		
		if(val_post!="yes"){
			$(".elem_sub_dx").each(function(){ 
				if(this.id!=chk_box_id){
					$('label[for='+this.id+']').css({"color":"purple","font-weight":"normal"}).removeClass("highlight");
					this.checked=false;
				}
			});
		}
		
		if($('#'+chk_box_id).is(':checked')==true){
			$('label[for='+chk_box_id+']').css({"color":"red","font-weight":"bold"}).addClass("highlight");
			if(val_post=="yes"){
				//$("#dialogLSS table[id*=LSS] td.highlight").triggerHandler("click");
				$('#'+ass_id).val(chk_box_desc_val);
				$('#'+ass_dx_id).val(chk_box_val);
				$("#"+ass_id).triggerHandler("change");
				$("#"+ass_id).triggerHandler("blur");
			}
		}else{			
			$('label[for='+chk_box_id+']').css({"color":"purple","font-weight":"normal"}).removeClass("highlight");
		}
	}else{
		var chk_box_id="elem_sub_"+id;
		var chk_box_val = $('#'+chk_box_id).val();
		for(var k=1;k<13;k++){
			if(val_post!="yes"){
				$(".elem_sub_dx").each(function(){ 
					if($('#elem_dxCode_'+k).val()==this.value){
						$('#elem_dxCode_'+k).val('');
					}
					if(this.id!=chk_box_id){
						$('label[for='+this.id+']').css({"color":"purple","font-weight":"normal"}).removeClass("highlight");
						this.checked=false;
					}
				});
			}
			var elem_dxCode = $('#elem_dxCode_'+k);
			var elem_dxCode_val= $('#elem_dxCode_'+k).val();
			if($('#'+chk_box_id).is(':checked')==true){
				$('label[for='+chk_box_id+']').css({"color":"red","font-weight":"bold"}).addClass("highlight");
				if($('#elem_dxCode_'+k) && elem_dxCode_val=="" && val_post=="yes"){
					$('#elem_dxCode_'+k).val(chk_box_val);
					//$("#dialogLSS table[id*=LSS] td.highlight").triggerHandler("click");
					return false;
				}
			}else{			
				$('label[for='+chk_box_id+']').css({"color":"purple","font-weight":"normal"}).removeClass("highlight");
				if(elem_dxCode_val==chk_box_val && val_post=="yes"){
					$('#elem_dxCode_'+k).val('');
					return false;
				}
			}
		}	
	}
}
function show_sec_icd(val){
	var show_icd="";
	if($('.'+val).css('display')=='none'){
		var show_icd=val;
		$('.icd10_data_td').css({"display":"none"});
	}
	$('.icd10_data_td').css({"display":"none"});
	if(show_icd!=""){
		$('.'+show_icd).css({"display":"table-row"});
	}
	//$('.'+val).toggle();
}
