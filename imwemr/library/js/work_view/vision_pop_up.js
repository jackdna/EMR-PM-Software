var isiPad=(navigator.userAgent.match(/iPad/i) != null) ? 1 : 0;
function get_open_modal_id(){
	var ret;
	$(".modal").each(function(){ if($(this).css("display")=="block"){ ret = $(this).attr("id"); }  });
	return ret;	
}
function no_change(){
	var popmodal="";
	popmodal = get_open_modal_id();
	if(popmodal=="")return;
	//if($('#popDistanceModal').css("display") == "block"){popmodal="popDistanceModal";}
	//else if($('#popMrModal').css("display") == "block"){popmodal="popMrModal";}
	
	
	//IF NO CHANGE THEN CHANGE COLOR OF ALL FILLED VALUES
	$('#'+popmodal+' input[type="text"]').each(function(index, element) {
		tid = this.id.replace(/_text_input|_input/,"");
		if(typeof(tid)!="undefined" && tid!=""){
			v = this.value||"";
			if(v!="" && v!="20/"){ top.fmain.$("#"+tid).trigger("change");	}
		}
	});
	
	//
	if(popmodal == "popDistanceModal"){
		var str = 	"#elem_visDisOdSel1, #elem_visDisOsSel1, #elem_visDisOuSel1,"+
				"#elem_visDisOdSel2, #elem_visDisOsSel2, #elem_visDisOuSel2";					
	}else if(popmodal == "popNearModal"){
		var str = 	"#elem_visNearOdSel1, #elem_visNearOsSel1, #elem_visNearOuSel1,"+
				"#elem_visNearOdSel2, #elem_visNearOsSel2, #elem_visNearOuSel2";
	}else if(popmodal.indexOf("popMR") != -1){
		var str = 	"";
		var ar_mr = ['S','C','A','Add','P','Sel1','Slash','Prism','Txt1','Txt2','Sel2','Sel2Vision'];		
		var x = popmodal.replace(/popMR|Modal/g,"");
		if(x==""){x=1;}
		
		var el_src="elem_visMr";
		//var ar_mr_opt=["MR1","MR2","MR3"];
		//for(var j in ar_mr_opt){
			var el_src_sfx="";
			var el_src_othr="";
			//var src = ar_mr_opt[j];
			if(x>1){ el_src_othr="Other"; }
			if(x>2){ el_src_sfx="_"+x; }
			
			for(var x in ar_mr){
				fld_var = ar_mr[x];
				str += "#"+el_src+el_src_othr+'Od'+fld_var+el_src_sfx+",";
				str += "#"+el_src+el_src_othr+'Os'+fld_var+el_src_sfx+",";
			}
		//}
	}else if(popmodal == "popAdAcuityModal"){
		var str = "#elem_visDisOuSel3, #elem_visDisOdTxt3, #elem_visDisOsTxt3, #elem_visDisOuTxt3";
	}	
	var flg_clk=0;
	if(typeof(str)!="undefined" && str!=""){
	str = str.substring(0,str.length-1);	
	top.fmain.$(""+str).each(function(){				
				var v = $(this).val();					
				if(v!="" && v!="20/"){ var tid = this.id;  top.fmain.$("#"+tid).trigger("change"); if(flg_clk==0 && popmodal.indexOf("popMR") != -1){ top.fmain.$("#"+tid).trigger("blur"); flg_clk=1; } }
			});
	}
	$(" #btnclose ").trigger("click");	
}

function shiftToParent(){	
	//SET OLD VALUE VARIABLES
	var tid="", v="";	
	
	var popmodal="";
	popmodal = get_open_modal_id();
	/*if($('#popDistanceModal').css("display") == "block"){popmodal="popDistanceModal";}
	else if($('#popArModal').css("display") == "block"){popmodal="popArModal";}
	else if($('#popPcModal').css("display") == "block"){popmodal="popPcModal";}
	else if($('#popMrModal').css("display") == "block"){popmodal="popMrModal";}*/
	
	if(popmodal!=""){
		$('#'+popmodal+' input[type="text"]').each(function(index, element) {
			tid = this.id.replace(/_text_input|_input/,"");
			if(typeof(tid)!="undefined" && tid!=""){
				v = this.value||"";	
				if(top.fmain.$("#"+tid).length>0){	
					top.fmain.$("#"+tid).val($.trim(v)).filter(function(){ return (this.id.indexOf("Add")!=-1 && $.trim($(this).val())=="") ? false : true;  }).trigger("change");						
				}else{
					//alert("#"+tid);
				}	
			}
		});
		
		if(popmodal.indexOf("popMR")!=-1){
			var i = ""+popmodal.replace(/(popMR|Modal)/g,"");
			//for(var i=1;i<=3;i++){
				top.fmain.$("#row_mr_"+i+" .active[value]").filter(function () { return this.value!=""&&this.value!="20/"}).eq(0).trigger("blur");	
			//}	
		}
		//BELOW CONDITION ADDED FOR PRS EXTERMAL VA WORK 
		if(popmodal=="popExtMR"){
			var i = ""+popmodal.replace(/(popExtMR|Modal)/g,"");
			//for(var i=1;i<=3;i++){
				top.fmain.$("#row_ext_mr"+i+" .active[value]").filter(function () { return this.value!=""&&this.value!="20/"}).eq(0).trigger("blur");	
			//}	
		}
	}//
	
	//
	if(popmodal=="popDistanceModal"){
		var val_disDesc = '', t = "";
		if($('#disOldBlockAOd').val()!=''){	t = $('#disOldBlockAOd').val()||""; val_disDesc+=$.trim('OD: '+t)+" ";}
		if($('#disOldBlockAOs').val()!=''){	t = $('#disOldBlockAOd').val()||""; val_disDesc+=$.trim('OS: '+t)+" ";}
		if($('#disOldBlockAOu').val()!=''){	t = $('#disOldBlockAOd').val()||""; val_disDesc+=$.trim('OU: '+t)+" ";}
		top.fmain.$("#elem_disDesc").val($.trim(val_disDesc)).trigger("click");
		
		var sel1 = ""+$('#disBlockAType').val()||"", sel2 = ""+$('#disBlockBType').val()||"";
		top.fmain.$("#elem_visDisOdSel1, #elem_visDisOsSel1, #elem_visDisOuSel1").val(sel1).trigger("change");
		top.fmain.$("#elem_visDisOdSel2, #elem_visDisOsSel2, #elem_visDisOuSel2").val(sel2).trigger("change");
	}
	if(popmodal=="popNearModal"){
		var sel1 = ""+$('#nearBlockAType').val()||"", sel2 = ""+$('#nearBlockBType').val()||"";
		top.fmain.$("#elem_visNearOdSel1, #elem_visNearOsSel1, #elem_visNearOuSel1").val(sel1).trigger("change");
		top.fmain.$("#elem_visNearOdSel2, #elem_visNearOsSel2, #elem_visNearOuSel2").val(sel2).trigger("change");	
	}
	$(" #btnclose ").trigger("click");	
}

function vis_pop_show(s){	
	var popmodal="";
	popmodal = get_open_modal_id();
	/*if($('#popDistanceModal').css("display") == "block"){popmodal="popDistanceModal";}
	if($('#popPcModal').css("display") == "block"){popmodal="popPcModal";}
	if($('#popMrModal').css("display") == "block"){popmodal="popMrModal";}	*/
	
	$("#"+popmodal+" div[id*=row_] ").addClass("hidden");
	if(s==1){ s="row_addacu"; }
	else if(s==2){ s="row_pam"; }
	else if(s==3){ s="row_bat"; }
	else if(s==4){ s="row_distance"; }
	else if(s.indexOf("pc")!=-1||s.indexOf("mr")!=-1){ s="row_"+s; }
	
	$("#"+s).removeClass("hidden");	
}

function getValuesFromParent(popmodal){
	
	if(typeof(popmodal)=="undefined"){ popmodal=""; }
	//var popmodal="";
	//if($('#popDistanceModal').css("display") == "block"){popmodal="popDistanceModal";}
	//else if($('#popArModal').css("display") == "block"){popmodal="popArModal";}
	//else if($('#popMrModal').css("display") == "block"){popmodal="popMrModal";}
	
	//SET OLD VALUE VARIABLES
	var tid="", sid="", v="";
	if(popmodal!=""){
		$('#'+popmodal+' input[type="text"]').each(function(index, element) {		
			tid = this.id.replace(/_text_input|_input/,"");
			sid = this.id;
			if(typeof(tid)!="undefined" && tid!=""){
				
				v = "";
				v = top.fmain.$("#"+tid).val()||"";
				
				v = $.trim(v);
				if(sid.indexOf("A_text_input")!=-1 && v.length<3 && v.length>0){ v = (v.length==1) ? "00"+v : "0"+v ; }
				$("#"+sid).val(v);
				
				if($("select[id="+tid+"_input]").length>0){
					$("select[id=\""+tid+"_input\"] option").prop("selected", false);
					$("select[id=\""+tid+"_input\"] option[value=\""+v+"\"]").prop("selected", true);
				}
			}
		});
	}
	//
	if(popmodal=="popDistanceModal"){
		var val_disDesc = '', t = "";
		val_disDesc = ""+top.fmain.$("#elem_disDesc").val()||"";
		val_disDesc = $.trim(val_disDesc);
		if(val_disDesc!=''){
			var arrT=[], dod='', dos='', dou='';
			val_disDesc = val_disDesc.replace(/,/g,'');
			arrT= val_disDesc.split('OS:');
			dod = arrT[0]||"";
			dod = dod.replace('OD:','');
			if(arrT[1]){arrT= arrT[1].split('OU:'); }
			dos=arrT[0];		
			dou=arrT[1];	

			if(dod!=''){ $('#disOldBlockAOd').val(dod); }
			if(dos!=''){ $('#disOldBlockAOs').val(dos); }
			if(dou!=''){ $('#disOldBlockAOu').val(dou); }		
		}
		
		//	
		var sel1 = ""+top.fmain.$("#elem_visDisOdSel1").val()||"", sel2 = ""+top.fmain.$("#elem_visDisOdSel2").val()||"";
		$('#disBlockAType').val(sel1);
		$('#disBlockBType').val(sel2);
	}	
	if(popmodal=="popNearModal"){	
		var sel1 = ""+top.fmain.$("#elem_visNearOdSel1").val()||"", sel2 = ""+top.fmain.$("#elem_visNearOdSel2").val()||"";
		$('#nearBlockAType').val(sel1);
		$('#nearBlockBType').val(sel2);
	}
}

function showTranspose_visionpopup(indx){		

	var arreye = ["Od","Os"];
	var othr = indx;
	var sufx = "";
	
	if(othr.indexOf("Pc") != -1){
		pci = othr.replace(/Pc/, "");
		othr="Pc";sufx=pci+sufx;
	}else if(othr.indexOf("Mr") != -1){		
		pci = othr.replace(/Mr/, "");		
		if(pci>1){ othr="MrOther"; }else{ othr="Mr"; }
		if(pci>2){ sufx="_"+pci; }	
	}
	
	for(var x in arreye){
		var eye = arreye[x];				
		
		var vs="elem_vis"+othr+eye+"S"+sufx+"_text_input";
		var vc="elem_vis"+othr+eye+"C"+sufx+"_text_input";
		var va="elem_vis"+othr+eye+"A"+sufx+"_text_input";
		
		//od
		var s = $("#"+vs+"").val();s=$.trim(s);
		var c = $("#"+vc+"").val();c=$.trim(c);
		var a = $("#"+va+"").val();a=$.trim(a);
		
		
		if(s.toUpperCase()=="PLANO"){ s="0"; }
		
		if(typeof(s)=="undefined"  || s=="" || isNaN(s)){	continue;	}
		
		if(typeof(c)=="undefined" || c.toUpperCase()=="SPHERE" || c=="" || isNaN(c)){ continue; }
		
		//if(s.toUpperCase()=="PLANO" || c.toUpperCase()=="SPHERE" || s == "0" || c == "0"){continue;}
		
		var sig_sc=parseFloat(s) + parseFloat(c);
		sig_sc = sig_sc.toFixed(2);
		if(""+sig_sc.indexOf("+")==-1 && ""+sig_sc.indexOf("-")==-1){ sig_sc = "+"+sig_sc;  }
		
		var op_sign_c = (-1) * parseFloat(c);
		op_sign_c = op_sign_c.toFixed(2);
		op_sign_c = ""+op_sign_c;		
		if(""+op_sign_c.indexOf("+")==-1 && ""+op_sign_c.indexOf("-")==-1){ op_sign_c = "+"+op_sign_c;  }
		
		if(typeof(a)!="undefined" && a!="" && !isNaN(a)){	
		var new_axis = parseFloat(a) + 90;
		if(new_axis<1 || new_axis>180){	new_axis = a - 90;	}
		}
		
		if(new_axis<100&&new_axis>9){new_axis="0"+new_axis;}
		if(new_axis<10){new_axis="00"+new_axis;}
		
		//var trns_od = "<label class=\"od\">OD</label> : "+sig_sc+" "+op_sign_c+" X "+new_axis+"<br/></br>";	
		$("#"+vs+"").val(sig_sc);		
		$("#"+vc+"").val(op_sign_c);
		$("#"+va+"").val(new_axis);
		
	
		
		
		$("#elem_vis"+othr+eye+"S"+sufx+"_input option").prop("selected", false);
		$("#elem_vis"+othr+eye+"C"+sufx+"_input option").prop("selected", false);
		$("#elem_vis"+othr+eye+"A"+sufx+"_input option").prop("selected", false);
		$("#elem_vis"+othr+eye+"S"+sufx+"_input option[value=\""+sig_sc+"\"]").prop("selected", true);
		$("#elem_vis"+othr+eye+"C"+sufx+"_input option[value=\""+op_sign_c+"\"]").prop("selected", true);
		$("#elem_vis"+othr+eye+"A"+sufx+"_input option[value=\""+new_axis+"\"]").prop("selected", true);	
		
	}	
}

function bl_exe(o){	
	
	var ar = $(o).parents("tr");
	if(ar.length<=0){ ar = $(o).parents(".row");}
	
	ar.find(":input").each(function(){
			var id = $(this).attr("id");
			if(id.indexOf("Od")!=-1){
				var v = $(this).val();
				var o_type = this.type;
				if(o_type=="text"){
				var id_os = id.replace(/Od/,"Os");
				$("#"+id_os+"").val(v).trigger("click");
				}else{
				var id_os = id.replace(/Od/,"Os");
				$("#"+id_os+" option ").prop("selected", false);	
				$("#"+id_os+" option[value='"+v+"'] ").prop("selected", true);
				var ev = (isiPad==1) ? "blur" : "change"; 
				$("#"+id_os+"").trigger(ev);
				}	
			}
		});	
	
}

function vis_copy_from(o){
	var src = o.value;	
	if(src!=""){
		//src = src.toUpperCase();
		var src_type="";
		if(src.indexOf("pc")!=-1){src_type="PC";}
		else if(src.indexOf("mr")!=-1){src_type="MR";}
		
		var src_indx="";
		src_indx = src.replace(/pc|mr/g,"");
		
		var cp2_type="";
		if(o.id.indexOf("pc")!=-1){cp2_type="PC";}
		else if(o.id.indexOf("mr")!=-1){cp2_type="MR";}
		
		var cp2_indx="";
		cp2_indx = o.id.replace(/el_copyfrm_|pc|mr/g,"");
		cp2_indx = $.trim(cp2_indx);
		
		var ar_pc = ['S','C','A','Add','P','Sel2','Slash','Prism','Sel1','OverrefS','OverrefC','OverrefA','OverrefV','','','','']; //PC
		var ar_mr = ['S','C','A','Add','P','Sel1','Slash','Prism','','','','','','Txt1','Txt2','Sel2','Sel2Vision'];
		var el_pc = "elem_visPc";
		var el_mr = "elem_visMr";
		var el_trgt="";
		var trgt_ar = [];
		var el_trgt_sfx="";
		var el_trgt_othr="";		
		
		if(cp2_type=="PC"){
			trgt_ar = ar_pc;
			el_trgt=el_pc;
			el_trgt_sfx=cp2_indx;			
		}else{
			trgt_ar = ar_mr;
			el_trgt=el_mr;			
			if(cp2_indx>1){ el_trgt_othr="Other"; }
			if(cp2_indx>2){ el_trgt_sfx="_"+cp2_indx; }			
		}
		
		var el_src="";
		var src_ar = [];
		var el_src_sfx="";
		var el_src_othr="";
		
		if(src_type=="PC"){
			src_ar = ar_pc;
			el_src=el_pc;
			if(src_indx>1){el_src_sfx=src_indx;}			
		}else{
			src_ar = ar_mr;
			el_src=el_mr;
			if(src_indx>1){ el_src_othr="Other"; }
			if(src_indx>2){ el_src_sfx="_"+src_indx; }
		}
		
		var fld_var = trgt_var = "";
		for(var x in src_ar){			
			fld_var = src_ar[x];
			trgt_var = trgt_ar[x]; 
			if(fld_var=="" && trgt_var==""){ continue; }
			
			var src_var_od = el_src+el_src_othr+'Od'+fld_var+el_src_sfx;
			var trgt_var_od_in = el_trgt+el_trgt_othr+'Od'+trgt_var+el_trgt_sfx+"_input";
			var trgt_var_od_txt_in = el_trgt+el_trgt_othr+'Od'+trgt_var+el_trgt_sfx+"_text_input";
			
			var src_var_os = el_src+el_src_othr+'Os'+fld_var+el_src_sfx;
			var trgt_var_os_in = el_trgt+el_trgt_othr+'Os'+trgt_var+el_trgt_sfx+"_input";
			var trgt_var_os_txt_in = el_trgt+el_trgt_othr+'Os'+trgt_var+el_trgt_sfx+"_text_input";
			
			var v="";
			if(top.fmain.$("#"+src_var_od+"").length>0){
				v=top.fmain.$("#"+src_var_od+"").val()||"";
				if(v!=""){ top.$("#"+trgt_var_od_in+" option").prop("selected", false); top.$("#"+trgt_var_od_in+" option[value='"+v+"']").prop("selected", true);  top.$("#"+trgt_var_od_txt_in+"").val(v); 	 } 
			}
			
			v="";
			if(top.fmain.$("#"+src_var_os+"").length>0){
				v=top.fmain.$("#"+src_var_os+"").val()||"";
				if(v!=""){ top.$("#"+trgt_var_os_in+" option").prop("selected", false); top.$("#"+trgt_var_os_in+" option[value='"+v+"']").prop("selected", true);  top.$("#"+trgt_var_os_txt_in+"").val(v);  } 
			}
			
			//OU
			if(fld_var=="Txt1"){				
				var src_var_ou = el_src+el_src_othr+'Ou'+fld_var+el_src_sfx;
				var trgt_var_ou_in = el_trgt+el_trgt_othr+'Ou'+trgt_var+el_trgt_sfx+"_input";
				var trgt_var_ou_txt_in = el_trgt+el_trgt_othr+'Ou'+trgt_var+el_trgt_sfx+"_text_input";
				
				var v="";
				if(top.fmain.$("#"+src_var_ou+"").length>0){
					v=top.fmain.$("#"+src_var_ou+"").val()||"";
					if(v!=""){ top.$("#"+trgt_var_ou_in+" option").prop("selected", false); top.$("#"+trgt_var_ou_in+" option[value='"+v+"']").prop("selected", true);  top.$("#"+trgt_var_ou_txt_in+"").val(v); 	 } 
				}				
			}		
		}		
	}
}

