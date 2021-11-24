//Procedures ---------------------
function showProcedure(id){
	var qry = (id!="") ? "&chart_proc_id="+id : "";
	window.location.replace(zPath+"/chart_notes/onload_wv.php?elem_action=Procedures"+qry);
}
function proc_showTabs(id){
	/*
	$(".proctab .nav-tabs li").removeClass("active");
	if(id!=""){
		$("#"+id+", #lbl"+id).addClass("active");
	}else{
		$("#proc_note, #lblproc_note").addClass("active");
	}
	*/
	//tablblOn
	//$(".tab").removeClass("tabOn");
	//$("#lbl"+id).addClass("tabOn");
	$("#elem_btnFinAmdmnt, #elem_btnDoneAmdmnt").addClass("hidden");

	if(id == "procnote"){
		getConsentForm();
		$("#elem_btnPrint").show();
	}else if(id == "amendment"){
		$("#elem_btnFinAmdmnt, #elem_btnDoneAmdmnt").removeClass("hidden");
		$("#elem_btnPrint").show();
	}else if(id == "consent_form"){
		getConsentForm();
		$("#elem_btnPrint").hide();

		//
		var cfi = $("#curConfrmId").val();
		var fii = $("#proc_con_frm_id").val();
		if(typeof(cfi)!="undefined" && typeof(fii)!="undefined" && cfi!="" && fii!="" && cfi!="0" && fii!="0" ){
			$("#elem_btnPrint").show();
		}

	}else if(id == "op_report"){
		loadOpTemp();
		getConsentForm();
		$("#elem_btnPrint").hide();

		//
		var pri = $("#proc_pn_rep_Id").val();
		if(typeof(pri)!="undefined" && pri!="" && pri!="0"){
			$("#elem_btnPrint").show();
		}

	}else{
		$("#elem_btnPrint").show();
	}
}

function loadOpTemp(){
	var idProc = $(":input[name=elem_chart_procedures_id]").val();
	var s = $(":input[name=elem_OpTempId]").val();
	if(s=="0"){  s=""; }
	var curOpId=$("#curOpId").val();
	//var pd = CKEDITOR.instances['elem_pnData'].getData();
	var pd = $('#elem_pnData').redactor('code.get');

	if((s!="" && curOpId!=s) || (curOpId!="" && s!="" && pd=="")){
		$("#curOpId").val(s);

		var e = $(":checked[name=elem_site]").val();
		if(typeof(e)=="undefined")e="";
		//alert("procedures.php?elem_opNoteId="+s+"&elem_opNoteEye="+e+"&chart_procedures_id="+idProc);
		var selText = '';
		selText = ""+fun_mselect('#elem_dxCode', 'val');
		//alert("selText: " + selText);
		var spotDuration = $("#spot_duration").val();
		var spotSize = $("#spot_size").val();
		var power = $("#power").val();
		var shots = $("#shots").val();
		var total_energy = $("#total_energy").val();
		var degree_of_opening = $("#degree_of_opening").val();
		var exposure = $("#exposure").val();
		var count = $("#count").val();

		var url = zPath+"/chart_notes/onload_wv.php?elem_action=Procedures";
		//console.log(url+"&elem_opNoteId="+s+"&elem_opNoteEye="+e+"&chart_procedures_id="+idProc+"&elem_opNotedxCode="+selText);
		$.get(url+"&elem_opNoteId="+s+"&elem_opNoteEye="+e+"&chart_procedures_id="+idProc+"&elem_opNotedxCode="+selText+"&elem_spotDuration="+spotDuration+"&elem_spotSize="+spotSize+"&elem_power="+power+"&elem_shots="+shots+"&elem_total_energy="+total_energy+"&elem_degree_of_opening="+degree_of_opening+"&elem_exposure="+exposure+"&elem_count="+count, function(data){
			//alert("data: " + data);
			if(data!=""){
				//CKEDITOR.instances.elem_pnData.setData( ''+data );
				//if($.trim(selText) == ""){
				//	$('#elem_pnData').redactor('code.set','');
				//}else{
					$('#elem_pnData').redactor('code.set',''+data);
				//}

			}
		});
	}
}

function getConsentForm(flgtemp){
	var idProc = $(":input[name=elem_chart_procedures_id]").val();
	var s = $(":input[name=elem_consentForm]").val();
	var curConfrmId = $("#curConfrmId").val();
	var a = $("#consent_form  iframe").attr("src");
	var chartId = $("#elem_form_id").val();
	// the below site code used for replacing  the {SITE} variable value with the procedures site for procedure consent iframe.
	var site='';
	if($("#elem_site_ou").is(":checked") || $("#elem_site_od").is(":checked") || $("#elem_site_os").is(":checked")){
		document.getElementById('consent_form').src = document.getElementById('consent_form').src;
		if($("#elem_site_ou").is(":checked")){
			site='both eyes';
		}else if($("#elem_site_od").is(":checked")){
			site='right eye';
		}else if($("#elem_site_os").is(":checked")){
			site='left eye';
		}
	}
	if((s!="" && curConfrmId!=s) || (s!="" && typeof(a)=="undefined") ){ //|| typeof(a)=="undefined"
		$("#curConfrmId").val(s);
		var u = (typeof(zPath_remote) != "undefined") ? zPath_remote : zPath ;
		var hgt = parseInt($("#iframeConsentForm").css("height"));
		$("#consent_form  iframe").attr("src",u+"/patient_info/consent_forms/consentFormDetails.php?consent_form_id="+s+"&pop_procedure=1&chart_procedures_id="+idProc+"&pophgt="+hgt+"&site="+site+"&chartId="+chartId+"");
	}
}
//
function set_lot_val(arr_lot_no,lot_obj_name,obj_item_id){
	if(typeof(arr_lot_no[obj_item_id])!='undefined'){
		var str_lot=arr_lot_no[obj_item_id];
		var str_split_arr="";
		if(str_lot.indexOf(",")>0){
			str_split_arr=str_lot.split(",");
			//new actb($("input:text[name="+lot_obj_name+"]")[0],str_split_arr); //todo
		}else{
			$("input:text[name="+lot_obj_name+"]").val(str_lot);
			str_split_arr=str_lot.split(",");
			//new actb($("input:text[name="+lot_obj_name+"]")[0],str_split_arr); //todo
		}
	}
}
//
function set_value_qty_texts(arr_med_name,arr_item_no_qty,arr_thrash,arr_lot_no){
	$("#divPreOpMeds input[type='text']").each(function() {
		var curr_obj_ctr=this.name.substr(-1);
		$("input:text[name=elem_med_preop_lot_qty"+curr_obj_ctr+"]").val("");
		var medi_name=$.trim($("input:text[name=elem_PreOpMed"+curr_obj_ctr+"]").val());
		var opt_med_id=arr_med_name[medi_name];
		if(typeof(arr_item_no_qty[opt_med_id])!='undefined'){
			$("input:text[name=elem_med_preop_lot_qty"+curr_obj_ctr+"]").val(arr_item_no_qty[opt_med_id]);
		}
		if(typeof(arr_lot_no)!='undefined' && typeof(opt_med_id)!='undefined'){
			set_lot_val(arr_lot_no,"elem_PreOpMedLot"+curr_obj_ctr,opt_med_id);
		}
	});
	$("#divIntraVitrealMeds input[type='text']").each(function() {
		var curr_obj_ctr=this.name.substr(-1);
		var inter_medi_name=$.trim($("input:text[name=elem_IntraVitrealMeds"+curr_obj_ctr+"]").val());
		var opt_inter_med_id=arr_med_name[inter_medi_name];
		$("input:text[name=elem_med_intravitreal_lot_qty"+curr_obj_ctr+"]").val("");
		if(typeof(arr_item_no_qty[opt_inter_med_id])!='undefined'){
			$("input:text[name=elem_med_intravitreal_lot_qty"+curr_obj_ctr+"]").val(arr_item_no_qty[opt_inter_med_id]);
		}
		if(typeof(arr_lot_no)!='undefined' && typeof(opt_inter_med_id)!='undefined'){
			set_lot_val(arr_lot_no,"elem_IntraVitrealMedsLot"+curr_obj_ctr,opt_inter_med_id);
		}
	});
	$("#divPostOpMeds input[type='text']").each(function(){
		var lot_no=this.value;
		var curr_obj_ctr=this.name.substr(-1);
		$("input:text[name=elem_med_postop_lot_qty"+curr_obj_ctr+"]").val("");
		var post_medi_name=$.trim($("input:text[name=elem_PostOpMeds"+curr_obj_ctr+"]").val());
		var opt_post_med_id=arr_med_name[post_medi_name];
		if(typeof(arr_item_no_qty[opt_post_med_id])!='undefined'){
			$("input:text[name=elem_med_postop_lot_qty"+curr_obj_ctr+"]").val(arr_item_no_qty[opt_post_med_id]);
		}
		if(typeof(arr_lot_no)!='undefined' && typeof(post_medi_name)!='undefined'){
			set_lot_val(arr_lot_no,"elem_PostOpMedsLot"+curr_obj_ctr,opt_post_med_id);
		}
	});
}

// Botox--
function botox_insertOldBtxVal(id){
	var url = zPath+"/chart_notes/onload_wv.php?elem_action=Procedures";
	url+="&btxid="+id;

	$.get(url, function(data){
		if(data && data.id && typeof(data.id)!="undefined"){
			if(data.btx_total && typeof(data.btx_total)!="undefined"){  $("#elem_botox_total").val(data.btx_total); }
			if(data.lot && typeof(data.lot)!="undefined"){  $("#elem_botox_lot").val(data.lot); }

			if(data.vis_sc_od && typeof(data.vis_sc_od)!="undefined"){  $("#elem_botox_sc_od").val(data.vis_sc_od); }
			if(data.vis_cc_od && typeof(data.vis_cc_od)!="undefined"){  $("#elem_botox_cc_od").val(data.vis_cc_od); }
			if(data.vis_othr_od && typeof(data.vis_othr_od)!="undefined"){ $("#elem_botox_other_od").val(data.vis_othr_od); }

			if(data.vis_sc_os && typeof(data.vis_sc_os)!="undefined"){  $("#elem_botox_sc_os").val(data.vis_sc_os); }
			if(data.vis_cc_os && typeof(data.vis_cc_os)!="undefined"){  $("#elem_botox_cc_os").val(data.vis_cc_os); }
			if(data.vis_othr_os && typeof(data.vis_othr_os)!="undefined"){  $("#elem_botox_other_os").val(data.vis_othr_os); }

			//rd_injctn - $elem_botox_inject_radio
			if(data.rd_injctn && typeof(data.rd_injctn)!="undefined"){  $(":input[name=elem_botox_inject_radio][value='"+data.rd_injctn+"']").prop("checked", true); }
			else{ $(":input[name=elem_botox_inject_radio]").prop("checked", false); }

			//rbdcs - $elem_botox_rbdcs
			if(data.rbdcs && typeof(data.rbdcs)!="undefined"){  $(":input[name=elem_botox_rbdcs][value='"+data.rbdcs+"']").prop("checked", true); }
			else{ $(":input[name=elem_botox_rbdcs]").prop("checked", false); }

			if(data.drw_coords && typeof(data.drw_coords)!="undefined"){  setBottoxDrw_change(data.drw_coords);	}

		}
	},'json');
}
//--

var setBottoxDrw_canvas;
function setBottoxDrw_change(xjson){
	var ut=0;
	if($.trim(xjson)!=""){ xjson = JSON.parse(xjson); setBottoxDrw_canvas.loadFromJSON(xjson, setBottoxDrw_canvas.renderAll.bind(setBottoxDrw_canvas), function(o, object){ var t = object.getText(); t=$.trim(t); if(t=="x"){ }else{ ut = parseFloat(ut) + parseFloat(t); } }); if(isNaN(ut)){ut="";} $("#elem_botox_used").val(ut).trigger("change"); $("#div_cnvs_bottox").trigger("mouseout");  }
}

function setBottoxDrw(){
	//
	var c="div_cnvs_bottox";
	var ocan_all = $('#'+c+' canvas');
	var ocan =  ocan_all[0];

	var canvas = this.__canvas = new fabric.Canvas(ocan,{ perPixelTargetFind: true,targetFindTolerance: 4});
	setBottoxDrw_canvas = canvas;
	fabric.Object.prototype.transparentCorners = false;
	canvas.selection = false;

	//menu
	//var arrMenu=[];
	var calcUsed = function(){  var ut=0;canvas.forEachObject(function(o){var t = o.getText();  t=$.trim(t); if(t=="x"){ }else{  ut = parseFloat(ut) + parseFloat(t);} }); if(isNaN(ut)){ut="";}	$("#elem_botox_used").val(ut).trigger("change");};
	var addExamNmOnCanvas = function(o){
		//$(".drw_menu").remove();
		var otgt = o.target;
		var e = o.e;

		var pointer = canvas.getPointer(e);

		var a = $(":checked[name=elem_btxds_opts]").val();
		var tlen = canvas.getObjects().length;
		if(typeof(tlen)=="undefined"){ tlen=0; }

		//a=sv+"x\n"+a;
		var sv=a;
		a="x";
		var uidx="ox"+tlen, uiddsg="odsg"+tlen;

		var fi=0;
		canvas.forEachObject(function(o){ if(o.getFill()=="red"){
			var xid = o.get("unit");
			if(xid.indexOf("ox")!=-1){
				var dsgid = xid.replace("ox","odsg");

				canvas.forEachObject(function(o1){
					if(o1.get("unit")==dsgid){ o1.setText(sv); }
				});

				fi=1;
			}
		}});

		if(fi==0){
			var tmp = new fabric.Citextb(a, {      left: parseInt(pointer.x)-2,     top: parseInt(pointer.y)-7,      fontFamily: 'Arial',     fill: '#171717',    fontSize: '14', selectable:false   });
			canvas.add(tmp);
			tmp.set({unit:uidx});

			var tmp = new fabric.Citextb(sv, {      left: parseInt(pointer.x)-2,     top: parseInt(pointer.y)-7+15,      fontFamily: 'Arial',     fill: '#171717',    fontSize: '9', selectable:false   });
			canvas.add(tmp);
			tmp.set({unit:uiddsg});
		}
		calcUsed();
		//btx_title();
	};

	var delExamNmOnCanvas = function(){
		canvas.forEachObject(function(o){ if(o.getFill()=="red"){
			var xid = o.get("unit");
			if(xid.indexOf("ox")!=-1){
				var dsgid = xid.replace("ox","odsg");
				canvas.forEachObject(function(o1){
					if(o1.get("unit")==dsgid){ canvas.remove(o1); }
				});
			}

			canvas.remove(o);
		}});
		calcUsed();
	};
	/*
	var btx_title = function(m, e){
		$("#dvbxtitle").remove();
		if(m==1){
			var sx = parseInt(window.event.clientX)+30, sy = parseInt(window.event.clientY)+30;
			var o = e.target;
			var v= o.get("unit");
			$("body").append("<div id=\"dvbxtitle\" style=\"position:absolute;background:white;border:1px solid black;min-width:30px;line-height:30px;top:10px;left:10px;\">"+v+"</div>");
			$("#dvbxtitle").css({ "left":sx, "top":sy  }).show();
		}
	};
	*/

	//Disable context menu
	$('#'+c+" .upper-canvas").bind('contextmenu', function(e) {	delExamNmOnCanvas();	/*showCanvasMenu(e);*/		e.preventDefault();   		return false;		    });

	canvas.on('mouse:over', function(e) {	/*btx_title(1, e);*/ var tx = e.target.getText(); if($.trim(tx)=="x"){   e.target.setFill('red');	    canvas.renderAll();}	  });

	canvas.on('mouse:down', function(e) { 	addExamNmOnCanvas(e); });

	canvas.on('mouse:out', function(e) {	/*btx_title();*/    e.target.setFill('black');    canvas.renderAll();	  });

	$("#"+c).on("mouseout", function(){ $("#elem_cnvs_bottox_drw").val(ocan.toDataURL("image/png"));  var s = ""+JSON.stringify(canvas); /*s=s.replace(/\\n/g,"lshl");*/  $("#elem_cnvs_bottox_drw_coords").val(s);	} );

	//
	var xjson = z_cnvs_bottox_drw_coords;
	//alert(xjson);
	if($.trim(xjson)!=""){  /*xjson=xjson.replace(/lshl/g,'\\n'); console.log(xjson);*/    xjson = JSON.parse(xjson); canvas.loadFromJSON(xjson, canvas.renderAll.bind(canvas), function(o, object){ object.set({selectable:false});  }); $("#"+c).trigger("mouseout"); }
}

function setBottox(flgonload){
	var x = $("#elem_procedure option:selected").text();
	if(x.toLowerCase().indexOf("botox")!=-1){
		$("#tbl_pronote_med, #tbl_pronote_timeout, #dvcmt").hide();
		$("#divBottox, #divAddFields").removeClass("hidden");
		$("#elem_bottox_open_flg").val(1);
		$("#elem_lasik_open_flg").val(0);
		setBottoxDrw();
		$("#elem_botox_total,#elem_botox_used").on("change",function(){ var tmp=tmp1=tmp2=""; tmp1=$("#elem_botox_total").val(); tmp2=$("#elem_botox_used").val(); tmp=parseFloat(tmp1)-parseFloat(tmp2); if(isNaN(tmp)){ tmp=""; }   $("#elem_botox_wasted").val(tmp); });
		$("#tbl_pronote_med :input[type=text], #tbl_pronote_timeout :input[type=text], #dvcmt :input[type=text]").val("");
		$("#tbl_pronote_timeout select").val("");
		$("#tbl_pronote_timeout :input[type=checkbox]").prop("checked",false);
		if(typeof(flgonload)=="undefined" || flgonload!=1){	setBottoxVisual();	}

		$(":input[name^=elem_PreOpMed]").val('');
		$(":input[name^=elem_PostOpMeds]").val('');
		$(":input[name^=elem_IntraVitrealMeds]").val('');

		//
		//$("#cpt_multi_select").css({"width":"160px"});
		//$("#cpt_multi_select").multiselect("option", {"minWidth":"160"} );
		$("#dvTypeBtx").removeClass("hidden");
		$("#dvlids").removeClass("hidden");
		$("#dvPtOccu, #dvPtHobby, #div_lasik_proc").addClass("hidden");
		$( "#elem_lot_expr_dt" ).datepicker({dateFormat:top.jQueryIntDateFormat});

	}else if(x.toLowerCase() == 'lasik' ){	//.indexOf("lasik")!=-1
		$("#tbl_pronote_med, #tbl_pronote_timeout, #dvcmt ").hide();
		$("#div_lasik_proc").removeClass("hidden");
		$("#divBottox, #divAddFields").addClass("hidden");
		$("#dvTypeBtx").addClass("hidden");
		$("#elem_lasik_open_flg").val(1);
		$("#div_lasik_proc :checkbox").bind("click", function(){ single_chkbx(this); });
		$("#dvlids").addClass("hidden");
		$("#dvPtOccu, #dvPtHobby").removeClass("hidden");

		$(":input[name^=elem_PreOpMed]").val('');
		$(":input[name^=elem_PostOpMeds]").val('');
		$(":input[name^=elem_IntraVitrealMeds]").val('');


	}else{
		$("#tbl_pronote_med, #tbl_pronote_timeout, #dvcmt").show();
		$("#divBottox, #divAddFields, #div_lasik_proc").addClass("hidden");
		$("#elem_bottox_open_flg").val(0);
		$("#elem_lasik_open_flg").val(0);
		//
		//$("#cpt_multi_select").css({"width":"314px"});
		//$("#cpt_multi_select").multiselect("option", {"minWidth":"207"} );
		$("#dvTypeBtx").addClass("hidden");
		$("#dvlids").removeClass("hidden");
		$("#dvPtOccu, #dvPtHobby").addClass("hidden");
	}
}

function setBottoxVisual(){
	var wo = window.opener.top.fmain;
	var v_sc=[],v_cc=[],v_sc_p=[],v_cc_p=[];
	var ars=["Od","Os"];
	for(var sv in ars){
		var s=ars[sv];
		for(var i=1;i<=3;i++){
			var oa = wo.$(":input[name=elem_visDis"+s+"Sel"+i+"]");
			var ob = wo.$(":input[name=elem_visDis"+s+"Txt"+i+"]");
			var a = oa.val();
			var b = ob.val();
			if(typeof(b)!="undefined" && b!="" && b!="20/"){
				if(a=="SC"){
					if(oa.hasClass("active") || ob.hasClass("active")){ if((typeof(v_sc[s])=="undefined" || v_sc[s]=="")){v_sc[s]=b;} }
					else{  if((typeof(v_sc_p[s])=="undefined" || v_sc_p[s]=="")){v_sc_p[s]=b;}  }
				}else if(a=="CC"){
					if(oa.hasClass("active") || ob.hasClass("active")){ if((typeof(v_cc[s])=="undefined" || v_cc[s]=="")){v_cc[s]=b;} }
					else{  if((typeof(v_cc_p[s])=="undefined" || v_cc_p[s]=="")){v_cc_p[s]=b;}  }
				}
			}
		}
	}

	//
	for(var sv in ars){
		var s=ars[sv];
		var sl=s.toLowerCase();
		$("#elem_botox_sc_"+sl).val(function(){ if(typeof(v_sc[s])!="undefined" && v_sc[s]!=""){ return v_sc[s]; }else if(typeof(v_sc_p[s])!="undefined" && v_sc_p[s]!=""){ return v_sc_p[s]; }else{ return ""; } });
		$("#elem_botox_cc_"+sl).val(function(){ if(typeof(v_cc[s])!="undefined" && v_cc[s]!=""){ return v_cc[s]; }else if(typeof(v_cc_p[s])!="undefined" && v_cc_p[s]!=""){ return v_cc_p[s]; }else{ return ""; } });
	}
}

function check_selection_cpt(selected_cpt,sel_option){

	sel_option = $.trim(sel_option);
	var arsel_option = sel_option!="" ? sel_option.split(" - ") : [];
	arsel_option[0]=$.trim(arsel_option[0]);
	if(arsel_option[1]!=""){
		arsel_option[1]=$.trim(arsel_option[1]);
	}

	var t_sel="";
	var ln = selected_cpt.length;
	for(var i=0; i<ln; i++){
		var t  = $.trim(selected_cpt[i]);
		if(t!=""){
			if(t == $.trim(sel_option)){
				t_sel="selected";
			}else{

			var art = t.split(" - ");
			art[0]=$.trim(art[0]);
			if(art[0]!=""){
				art[1]=$.trim(art[1]);

				if(art[1]!=""){
					if(arsel_option[0] == art[0] && arsel_option[1] == art[1]){t_sel="selected";}
				}else{
					//if(arsel_option[0] == art[0]){t_sel="selected";}
				}
			}

			}
			if(t_sel=="selected"){ break; }
		}
	}

	return t_sel;
}

function getProcedureInfo(flgOnLoad){
		if(typeof(flgOnLoad)=="undefined" || flgOnLoad == ""){  flgOnLoad = "0" ;  }
		var cntCptGrid = $("#cntCptGrid").val();
		var a = $(":input[name=elem_procedure]").val();
		if(a!=""&&a!=0){

			if(flgOnLoad == "0"){ //work when procedure is changed
                $(":input[name^=elem_PreOpMed]").val('');
                $(":input[name^=elem_PostOpMeds]").val('');
                $(":input[name^=elem_IntraVitrealMeds]").val('');

                $("#laser_procedure_note,#spot_duration,#spot_size,#power,#shots,#total_energy,#degree_of_opening,#exposure,#count").val("");
				proc_showTabs(''); //display off tabs
				for(var j=1;j<=cntCptGrid;j++) {
					$("#elem_cptCode"+j).val("");$("#elem_mod_code"+j).val("");
				}
			}


			var url = zPath+"/chart_notes/onload_wv.php?elem_action=Procedures";
			url+= "&getProceduresInfo="+a;
			$.get(url, function(data){

				//console.log(data);

				//alert(data.arr_admin_cpt_code[0]);

				var cpt_opt_split=cptopt_split='';
				var cpt_options='';var a_cpt="";var all_cpts=new Array();var all_mods=new Array();
				var str_cpt_mod=$.trim(data.arr_cpt_mod);
				var option_val='';

				if(flgOnLoad == "1"){
					var selected_cpt = $("#cpt_multi_select").val();
				}	

				$("#cpt_multi_select").html('');
				$("#hid_str_cpt_mod").val('');
				if(str_cpt_mod){
					arr_cpt_mod=str_cpt_mod.split("~~");
					if(arr_cpt_mod.length>=1){
						cptopt_modifier = "";
						var sel_option="", sel_option_dis="" ;
						for(var i=0;i<arr_cpt_mod.length;i++){
							cptopt_split=arr_cpt_mod[i].split("||");
							if($.trim(cptopt_split[0])){
								var cpt_desc = "";
								var all_cpt_dec = cptopt_split[0].split(" - ");
								cptopt_split[0] = ""+all_cpt_dec[0];
								if($.trim(all_cpt_dec[1])){ cpt_desc = ""+all_cpt_dec[1];  }

								a_cpt=arr_cpt_mod[i];all_cpts[i]=cptopt_split[0];all_mods[i]=cptopt_split[1];

								//console.log( cptopt_split[0], cpt_desc, all_cpt_dec );

								if($.trim(cptopt_split[1])){
									cptopt_modifier = " ("+$.trim(cptopt_split[1])+")";
								}
								cpt_options+="<div id='' onclick='set_cpt_val(\""+a_cpt+"\")' style='border-bottom:1px solid blue;width:151px;cursor:pointer;'>&nbsp;"+cptopt_split[0]+cptopt_modifier+"</div>";
								sel_option=cptopt_split[0];
								sel_option_dis=cptopt_split[0];
								if(cpt_desc){ sel_option_dis+= " - "+cpt_desc;  }
								if(cptopt_split[1]){
									sel_option=cptopt_split[0]+" - "+cptopt_split[1];
									sel_option_dis=sel_option_dis+" - "+cptopt_split[1];
								}

								///
								var t_sel="";
								if(flgOnLoad == "1"){
									t_sel = check_selection_cpt(selected_cpt,sel_option);
								}else{
									t_sel="selected";
								}

								option_val+='<option '+t_sel+' value="'+sel_option+'">'+sel_option_dis+'</option>';
							}
						}
						$("#hid_str_cpt_mod").val(''+str_cpt_mod);
					}

					if(a_cpt.length>0){
						//set_cpt_val(arr_cpt_mod[0],"1",all_cpts,all_mods);
					}
					for(var j=1;j<=cntCptGrid;j++) {
						//$("#menu_cpt_div"+j).html(cpt_options);
					}
					$("#cpt_multi_select").html(option_val);
				}
				//$("#cpt_multi_select").multiselect('refresh');
				fun_mselect("#cpt_multi_select", "refresh");
				$('#cpt_multi_select').trigger('changed.bs.select');

				var dx_option="";
				if(data.dx_code!=""){

					if(flgOnLoad == "1"){
						var selected_dx = $("#elem_dxCode").val();
					}

					//$("input[name=elem_dxCode]").val(""+data.dx_code);
					var dx_split=$.trim(data.dx_code).split(";");
					if(dx_split.length>0){
						var dx_id_split=$.trim(data.dx_code_id).split(";");
						if(dx_id_split.length!=dx_split.length){ dx_id_split=[]; }

						$.each(dx_split,function(index,dx_value){
							var dx_value=$.trim(dx_value)
							if($.trim(dx_value)){
								var t_dx_value = dx_value;
								if(dx_id_split.length>0 && typeof(dx_id_split[index])!="undefined" && dx_id_split[index]!=""){
									t_dx_value = t_dx_value+"@~@"+dx_id_split[index];
								}

								var t_sel="";
								if(flgOnLoad == "1"){
									if(selected_dx.indexOf(t_dx_value)!=-1 || selected_dx.indexOf(dx_value)!=-1){ t_sel="selected"; }
								}
								else{ t_sel="selected"; }

								dx_option+="<option "+t_sel+" value='"+t_dx_value+"'>"+dx_value+"</option>";
							}
						});
					}
				}
				$("#elem_dxCode").html(dx_option);
				//$("#elem_dxCode").multiselect('refresh');
				fun_mselect("#elem_dxCode", "refresh");
				$('#elem_dxCode').trigger('changed.bs.select');

				if(flgOnLoad == "1"){ return; } //onload : only set cpt and dx menus and return

				//preop
				if(data.pre_op_meds !=""){
					var arr = data.pre_op_meds.split("|");
					if(arr.length>0){
						for(var x in arr){
							var a = parseInt(x)+1;
							if(arr[x]!=""){  $("input[name=elem_PreOpMed"+a+"]").val(arr[x]); }
						}
					}
				}

				//intrav
				$("#div_laser_procedure_notes").hide();
				$("#div_intravitreal_meds,#lbl_intravitreal_meds").show();
				if(data.intraviteral_meds !="" && data.laser_procedure_note !="1"){
					var arr = data.intraviteral_meds.split("|");
					if(arr.length>0){
						for(var x in arr){
							var a = parseInt(x)+1;
							if(arr[x]!=""){  $("input[name=elem_IntraVitrealMeds"+a+"]").val(arr[x]); }
						}
					}
					$("#laser_procedure_note,#spot_duration,#spot_size,#power,#shots,#total_energy,#degree_of_opening,#exposure,#count").html("");
				}else if(data.laser_procedure_note=="1"){
					$("#div_intravitreal_meds,#lbl_intravitreal_meds").hide();
					$("#div_laser_procedure_notes").show();
					$("#laser_procedure_note").val("1");
					$("#spot_duration").val(data.spot_duration);
					$("#spot_size").val(data.spot_size);
					$("#power").val(data.power);
					$("#shots").val(data.shots);
					$("#total_energy").val(data.total_energy);
					$("#degree_of_opening").val(data.degree_of_opening);
					$("#exposure").val(data.exposure);
					$("#count").val(data.count);
				}

				//postop
				if(data.post_op_meds !=""){
					var arr = data.post_op_meds.split("|");
					if(arr.length>0){
						for(var x in arr){
							var a = parseInt(x)+1;
							if(arr[x]!=""){  $("input[name=elem_PostOpMeds"+a+"]").val(arr[x]); }
						}
					}
				}

				//time_out_request
				if(data.time_out_request == "yes" ){
					$("#elem_timeout").prop("checked",true).triggerHandler("click");
				}

				//consentform
				//var consent_arr =<?php echo $arr_consent;?>;
				if(data.consent_form_id != "" ){
					var v=data.consent_form_id
					var sel_form=data.consent_form_id_sel
					var c_optval;
					if(v.indexOf(",")>0){
						v_arr=v.split(",");
						if(v_arr.length>1){
							c_optval='<option value=\'\'></option>';
							for(c=0;c<v_arr.length;c++){
								o_id=$.trim(v_arr[c]);
								if(typeof consent_arr[o_id] != "undefined"){
									c_optval+='<option value="'+o_id+'">'+consent_arr[o_id]+'</option>';
								}
							}
						}
					}else if($.trim(v) && typeof consent_arr[v] != "undefined"){
						c_optval='<option selected="selected" value="'+v+'">'+consent_arr[v]+'</option>';
						sel_form=v;
					}else{
						c_optval='<option value=\'\'></option>';
						$.each(consent_arr,function(index,value){
							c_optval+='<option value="'+index+'">'+value+'</option>';	;
						});
						sel_form="";
					}
					$(":input[name=elem_consentForm]").html("");
					//$(":input[name=elem_consentForm]").css({"width":"292px"});
					$(":input[name=elem_consentForm]").html(c_optval);
					$(":input[name=elem_consentForm]").val(sel_form);
					//getConsentForm(sel_form);

				}else{
					var c_optval='<option value=\'\'></option>';
					$.each(consent_arr,function(index,value){
						c_optval+='<option value="'+index+'">'+value+'</option>';	;
					});
					$(":input[name=elem_consentForm]").html("");
					//$(":input[name=elem_consentForm]").css({"width":"292px"});
					$(":input[name=elem_consentForm]").html(c_optval);
					//getConsentForm("");
					$("#consent_form  iframe").attr("src","");
				}

				//opreport
				//var opnotes_arr =<?php echo $arr_opnotes;?>;
				if(data.op_report_id != "" ){
					var op_optval="";
					//$(":input[name=elem_OpTempId]").val(data.op_report_id);
					var op=data.op_report_id;
					var op_sel_form=data.op_report_id_sel;
					var c_optval;
					if(op.indexOf(",")>0){
						op_arr=op.split(",");
						if(op_arr.length>1){
							op_optval='<option value=\'0\'></option>';
							for(o=0;o<op_arr.length;o++){
								o_id=$.trim(op_arr[o]);
								if(typeof opnotes_arr[o_id] != "undefined"){
									op_optval+='<option value="'+o_id+'">'+opnotes_arr[o_id]+'</option>';
								}
							}
						}
					}else if($.trim(op) && typeof opnotes_arr[op] != "undefined"){
						op_optval='<option selected="selected" value="'+op+'">'+opnotes_arr[op]+'</option>';
						op_sel_form=op;
					}else{
						op_optval='<option value=\'0\'></option>';
						$.each(opnotes_arr,function(index,value){
							op_optval+='<option value="'+index+'">'+value+'</option>';
						});
						op_sel_form="";
					}
					$(":input[name=elem_OpTempId]").html("");
					$(":input[name=elem_OpTempId]").html(op_optval);
					//$(":input[name=elem_OpTempId]").css({"width":"292px"});
					$(":input[name=elem_OpTempId]").val(op_sel_form);
				}else{
					var op_optval;
					op_optval='<option value=\'0\'></option>';
					$.each(opnotes_arr,function(index,value){
						op_optval+='<option value="'+index+'">'+value+'</option>';
					});
					$(":input[name=elem_OpTempId]").html("");
					$(":input[name=elem_OpTempId]").html(op_optval);
					//$(":input[name=elem_OpTempId]").css({"width":"292px"});
				}
				//check is consent signed or not
				if(data.consent_signed){
					$("#consent_select").html("&nbsp;<img src='"+zPath+"/../library/images/flag_green.png' border='0' title='Saved'>");
				}else{
					$("#consent_select").html("&nbsp;<img src='"+zPath+"/../library/images/flag_red.png' border='0' title='Not Saved'>");
				}

				//setSuperBillValue(data);
			},"json");
		}
	}

	//

	//fill codes in proc sb
	var ogetprocdxcodes;
	function fill_codes_in_proc_sb(d){
		var ar_highlight = d.ar_highlight;
		var ar_dx = d.dx;
		var dx_option="";

		if(ar_dx && ar_dx.length>0){
			var k =1;
			for(var z in ar_dx){
				if(ar_dx[z] && typeof(ar_dx[z])!="undefined" && ar_dx[z]!=""){

					var tardx = ar_dx[z].split(" :Dsc: ");
					var tdx = tardx[0]; var tdsc = tardx[1]; var tdxid = tardx[2];
					if(typeof(tdxid)=="undefined" || tdxid==""){ tdxid=""; }

					dx_option+="<option value='"+tdx+"' selected>"+tdx+"</option>";
					$('#elem_dxCode_'+k).val(tdx).attr('title', tdsc).data("dxid", tdxid).tooltip();
					if(ar_highlight && ar_highlight.length>0){
						if(ar_highlight.indexOf(tdx)!=-1){  $('#elem_dxCode_'+k).addClass("el-highlight"); }
						else{
							for(var t in ar_highlight){
								if(typeof(ar_highlight[t])!="undefined" && ar_highlight[t]!="" && sb_check_dx_in_icd10(ar_highlight[t],tdx)){
									$('#elem_dxCode_'+k).addClass("el-highlight");
									break;
								}
							}
						}
					}

					k++;
				}
			}
		}

		//Botox
		var flg_btx=0; var unit=""; var wunit="";
		var proc = $.trim($("#elem_procedure option:selected").html());
		if(proc!=''){
			proc = proc.toLowerCase();
			if(proc.indexOf("botox")!=-1){
				flg_btx=1;
				var btx_type = $("#dvTypeBtx :checked[type=radio][name=elem_typeBtx]").val();
				//if(btx_type=="Medical"){unit=$("#elem_botox_total").val();}
				if(btx_type=="Medical"){
					unit=$("#elem_botox_used").val();
					unit = Math.round(unit);
					var tunit = $("#elem_botox_total").val();
					var wunit = tunit - unit ;
					wunit = Math.round(wunit);
				}
				else if(btx_type=="Cosmetic"){unit=$("#elem_botox_used").val();}
			}
		}

		//cpt_multi_select --
		var j = 1;
		var arcpt = fun_mselect("#cpt_multi_select", "val");
		if(typeof(arcpt)!="object"){ arcpt=[]; }
		for(var z in arcpt){
			if(typeof(arcpt[z])!="undefined" && arcpt[z]!=""){
				var tmp = arcpt[z].split(" - ");
				var tmp_cpt = tmp[0];
				var tmp_md = tmp[1];
				if(typeof(tmp_cpt)!="undefined" && tmp_cpt!=""){

					var flgRpt=0;
					do{
						if($('#elem_cptCode_'+j).length<=0){opAddCptRow("LASTINDX");}
						$('#elem_cptCode_'+j).val(tmp_cpt).triggerHandler("blur");

						//check bottox code and add units --
						if(flg_btx==1 && tmp_cpt.match(/^j/i)){
							var tunit = (flgRpt==1 && tmp_md=="JW") ? wunit : unit ;
							if(tunit!=""&&tunit!="0"){
								if(typeof(j)!="undefined" && $("#elem_procUnits_"+j).length > 0){$("#elem_procUnits_"+j).data("unit", tunit);$("#elem_procUnits_"+j).val(tunit);}
								flgRpt=(flgRpt==0 && wunit>0) ? 1 : 0 ;
							}
						}
						//check bottox code and add units --

						if(typeof(tmp_md)!="undefined" && tmp_md!=""){
							var ar_tmp_md = tmp_md.split(";");
							if(ar_tmp_md.length>0){
								for(var x in ar_tmp_md){
									if(typeof(ar_tmp_md[x])!="undefined" && ar_tmp_md[x]!=""){
										var y = parseInt(x)+1;
										$("#elem_modCode_"+j+"_"+y).val(ar_tmp_md[x]);
									}
								}
							}
						}

						if(dx_option!=""){
							$("#elem_dxCodeAssoc_"+j).html(dx_option);
							$('#elem_dxCodeAssoc_'+j).selectpicker('refresh');
						}

						//Botox JW for wasted
						if(flgRpt==1){
							if(wunit!=""){
								tmp_md="JW";
							}
						}

						j++;
					}while(flgRpt);
				}
			}
		}
		getMxSB();
		//cpt_multi_select --

	}

	//For Procedure Superbill
	function setSuperBillValue_onchange(){
		opRemAllCptRow(2);
		$(".dxallcodes").val("").removeClass("el-highlight");

		var ar_dx_code = [], ar_dx_code_id = [];
		var ardx = fun_mselect("#elem_dxCode", "val");
		if(typeof(ardx)!="object"){ ardx=[]; }
		var k =1;
		//var dx_option="";
		for(var z in ardx){
			if(typeof(ardx[z])!="undefined" && ardx[z]!=""){
				var tmp_dx = ardx[z].split("@~@");
				var t_dx_id = $.trim(tmp_dx[1]);
				if(typeof(t_dx_id)=="undefined" || t_dx_id==""){t_dx_id ="";}

				var tmp_dx = ardx[z].split(" - ");
				var t_dx = $.trim(tmp_dx[0]);
				//dx_option+="<option  value='"+t_dx+"' selected>"+t_dx+"</option>";
				//$('#elem_dxCode_'+k).val(t_dx);
				ar_dx_code[ar_dx_code.length] = t_dx;
				ar_dx_code_id[ar_dx_code_id.length] = t_dx_id;
				k++;
			}
		}

		//check dx codes from visit
		var eye = $(":checked[name=elem_site]").val();
		var lids = "";
		$("#elem_lidsopt_rul, #elem_lidsopt_rll, #elem_lidsopt_lul, #elem_lidsopt_lll").each(function(){ if(this.checked){  lids += ""+this.value+",";  } });

		var url = zPath+"/chart_notes/requestHandler.php";
		var prm = {'elem_formAction':'get_procedure_dx_codes', 'eye':eye, 'lids':lids, 'dx':ar_dx_code, 'dx_id':ar_dx_code_id };
		$.post(url, prm, function(xx){
				//var flg_show_pop="0";
				//var tmp = xx.getElementsByTagName("flg_show_pop")[0];
				//if(tmp && tmp.firstChild){
				//	flg_show_pop = tmp.firstChild.nodeValue;
				//}

				//
				var flg_show_pop="0";
				var tmp = xx.getElementsByTagName("flg_show_pop")[0];
				if(tmp && tmp.firstChild){
					flg_show_pop = tmp.firstChild.nodeValue;
				}

				var strdx="";
				var tmp = xx.getElementsByTagName("ardx")[0];
				if(tmp && tmp.firstChild){
					strdx = tmp.firstChild.nodeValue;
				}

				var strdxhgt="";
				var tmp = xx.getElementsByTagName("arhighlight")[0];
				if(tmp && tmp.firstChild){
					strdxhgt = tmp.firstChild.nodeValue;
				}

				var str="";
				if(flg_show_pop!="1"){
					var oAsInfo = xx.getElementsByTagName("assess");
					str = get_dx_popup_lss(oAsInfo, 'get_proc_dx_codes');
					if(str!=""){
						ogetprocdxcodes = {};
						ogetprocdxcodes.ar_highlight = strdxhgt.split(",");
					}
				}

				if(str==""){
					var d = {};
					d.dx = strdx.split("!DX!");
					d.ar_highlight = strdxhgt.split(",");
					fill_codes_in_proc_sb(d);
				}

				//

			},"xml");

	}

	/*
	function setSuperBillValue(data) {
		opRemAllCptRow(2);
		$(".dxallcodes").val("");

		//--
		//
		var dx_option="";
		if(data.dx_code!=""){
			var k =1;
			var dx_split=$.trim(data.dx_code).split(";");
			if(dx_split.length>0){
				$.each(dx_split,function(index,dx_value){
					var dx_value=$.trim(dx_value)
					if($.trim(dx_value)){
						var ar_dx = dx_value.split(" - ");
						var t_dx = $.trim(ar_dx[0]);
						if(typeof(t_dx)!="undefined" && t_dx!=""){
							dx_option+="<option  value='"+t_dx+"' selected>"+t_dx+"</option>";
							$('#elem_dxCode_'+k).val(t_dx);
							k++;
						}
					}
				});
			}
		}
		//
		var str_cpt_mod=$.trim(data.arr_cpt_mod);
		if(str_cpt_mod){
			arr_cpt_mod=str_cpt_mod.split("~~");
			if(arr_cpt_mod.length>=1){
				var j = 1;
				for(var i=0;i<arr_cpt_mod.length;i++){
					arr_cpt_mod[i] = arr_cpt_mod[i].replace('||', ' - ');
					var tmp_cpt = arr_cpt_mod[i];
					var ar_cpt = tmp_cpt.split(",");
					if(ar_cpt.length > 0 ){
						for(var kx in ar_cpt){
							var tmp_x = ar_cpt[kx];
							if(typeof(tmp_x)!="undefined" && tmp_x!=""){
								var ar_tmp_x = tmp_x.split(" - ");
								var tmp_cpt = ar_tmp_x[0];
								var tmp_md = ar_tmp_x[2];
								if(typeof(tmp_cpt)!="undefined" && tmp_cpt!=""){
									if($('#elem_cptCode_'+j).length<=0){opAddCptRow("LASTINDX");}
									$('#elem_cptCode_'+j).val(tmp_cpt);
									if(typeof(tmp_md)!="undefined" && tmp_md!=""){
										var ar_tmp_md = tmp_md.split(";");
										if(ar_tmp_md.length>0){
											for(var x in ar_tmp_md){
												if(typeof(ar_tmp_md[x])!="undefined" && ar_tmp_md[x]!=""){
													var y = parseInt(x)+1;
													$("#elem_modCode_"+j+"_"+y).val(ar_tmp_md[x]);
												}
											}
										}
									}
									if(dx_option!=""){
										$("#elem_dxCodeAssoc_"+j).html(dx_option);
										$('#elem_dxCodeAssoc_'+j).selectpicker('refresh');
									}
									j++;
								}
							}
						}
					}
					//
					//$('#elem_cptCode_'+j).val(arr_cpt_mod[i]);
				}
			}
		}
		//--

		/*
		var url = zPath+"/chart_notes/onload_wv.php?elem_action=Procedures&loadSuperBill=load";
		$.ajax({
			url: url,
			dataType: 'text',
			success: function(data){
				$('#superbill').html(data);
			},
			complete: function() {



			}
		});
		**-/
		//console.log(data);

	}
	*/

	function checkDisTimeout(o){
		if(o.checked){
			$("#tbl_Timeout").removeClass("hidden");
			//$(":input[name=elem_corrctprocedure]").val(""+$(":input[name=elem_procedure]").val());

			$("#elem_corrctsite_ou").prop("checked",$(":checked[id=elem_site_ou]").length);
			$("#elem_corrctsite_od").prop("checked",$(":checked[id=elem_site_od]").length);
			$("#elem_corrctsite_os").prop("checked",$(":checked[id=elem_site_os]").length);

		}else{
			$("#tbl_Timeout").addClass("hidden");
		}
	}

	function saveProcedure(){

		// if View Only Access
		var final_flg = $("#elem_finalized_status").val();
		if(elem_per_vo == "1" || final_flg==1){
			var pr = $("#elem_hidPrint").val();
			if(pr!="1" && pr!="2"){
				window.close();return;
			}
		}

		//Check  b4 save --
		var msg ="";
		//procedure name + site
		var x1 = $(":input[name=elem_procedure]").val();
		var x1Txt = $("#elem_procedure option:selected").text();
		msg += (x1=="") ? "- Procedure<br />" : "";
		var y1 = $(":checked[name=elem_site]");
		var yl1 = $(":checked[name*=elem_lidsopt]");
		//4.       Site is optional for botox
		if(x1Txt.toLowerCase().indexOf("botox")==-1){ msg += (y1.length<=0&&yl1.length<=0) ? "- Site/Lids<br />" : ""; }

		//Timeout
		if($(":checked[id=elem_timeout]").length>0 && x1Txt.toLowerCase().indexOf("botox")==-1 && x1Txt.toLowerCase()!="lasik"){
			x = $(":input[name=elem_corrctprocedure]").val();
			var x2 = $(":input[name=elem_providers]").val();

			msg += (x=="") ? "- Correct Procedure<br />" : "";
			var y2=$(":checked[name=elem_corrctsite]");
			var yl2 = $(":checked[name*=elem_cor_lidsopt]");
			msg += (y2.length<=0&&yl2.length<=0) ? "- Correct Site/Correct Lids<br />" : "";
			/*
			msg += ($(":checked[id=elem_siteMarked]").length<=0) ? "- Site marked\n" : "";
			msg += ($(":checked[id=elem_positionProstheses]").length<=0) ? "- Position, prostheses, implants verified and equipment available if required \n" : "";
			msg += ($(":checked[id=elem_consentCompletedSigned]").length<=0) ? "- Consent completed and signed\n" : "";
			*/
			msg += (x2=="") ? "- Providers<br />" : "";

			var warn_msg="";
			// check unequal procedures
			if(x1!="" && x!="" && x1!=x){
				warn_msg+="Procedure and Correct Procedure do not match.";
			}
			//check unequal eyesite
			if(y1.length>0 && y2.length>0 && y1.val() != y2.val()){
				warn_msg+="Site and Correct Site do not match.";
			}
			if(warn_msg!=""){
				var cfrm = confirm(""+warn_msg+"\nDo you want to continue?");
				if(cfrm==false){	return; }
			}
		}

        //Super Bill
	var is_sb_md=0; sb_dxids="";
        var oSB = isSuperBillMade();
        if(oSB.SBill == true){

            //if(oSB.DXCodeOK == false  ||  oSB.DXCodeAssocOK == false ){
                //msg += '&bull; Dx code in Super bill<br>';
		//clearSuperBill(); //clear superbill without error message: procedures can be created w/o superbill
            //}

            if(oSB.DXCodeComplete==false){
                msg += '&bull; Incomplete ICD-10 DX code(s) in Super bill<br>';
            }else{
		is_sb_md=1;sb_dxids=oSB.dxids;
		}
        }

		if(msg!=""){
			//top.fAlert("Please fill following values :-<br />"+msg);
			displayConfirmYesNo_v2("Save checks", "Please fill following values :-<br />"+msg.replace(/\\n/g, "<br/>"));
			return;
		}
		//--

		//Consent Form --
		var curConfrmId = $("#curConfrmId").val();
		var s = $(":input[name=elem_consentForm]").val();

		if(s!="" && s!="0" && curConfrmId==s){
			if(typeof(top.iframeConsentForm.$)!="undefined"){
				//hidd_phy_sign_var VAL COMES FROM consentFormDetails.php WHEN CONSENT DOCUMENT WILL HAVE {PHYSICIAN SIGNATURE} VARIABLE THEN SELECT CONSENT WILL ALSO BE GO ON HOLD.
				var phy_sign_var_from_consent="";
				if((top.iframeConsentForm.document.getElementById('hidd_phy_sign_var')!="") && (top.iframeConsentForm.document.getElementById('hidd_phy_sign_var')==null)){
					phy_sign_var_from_consent = top.iframeConsentForm.$("#hidd_phy_sign_var").val();
				}

				if($("#consent_form").hasClass("active") || (typeof(phy_sign_var_from_consent)!='undefined' && typeof(phy_sign_var_from_consent)!='' && typeof(phy_sign_var_from_consent)!==null)){
					if($('#hold_to_physician').val()!=""){
						top.iframeConsentForm.$("#hidd_hold_to_physician").val($('#hold_to_physician').val());
					}
					//

					top.iframeConsentForm.save_form('save_form',1);
					//Get FormData --\\
					var ofrm = top.iframeConsentForm.document.consent_frm;
					var ln = ofrm.elements.length;
					var appdelem="";
					for(var i=0;i<ln;i++){
						//alert(ofrm.elements[i].name+" - "+ofrm.elements[i].value);

						if(ofrm.elements[i].name!=""){

							if((ofrm.elements[i].type=="checkbox" || ofrm.elements[i].type=="radio") && ofrm.elements[i].checked==false){ continue; }

							appdelem+="<input type='hidden' name='"+ofrm.elements[i].name+"' value='"+ofrm.elements[i].value+"' />";

						}
					}

					if(appdelem!=""){		$('form[name=frmProcedure]').append(""+appdelem);		}
					//--
				}
			}
		}
		//--

		//Op Note --
		var curOpId=$("#curOpId").val();
		var s = $(":input[name=elem_OpTempId]").val();
		if(s!="" && s!="0" && curOpId==s){
			if(!$("#op_report").hasClass("active")){
				$("#op_report_id").val("");
			}
		}else{
			$("#op_report_id").val("");
		}

		//--
		var re=check_qty_val();
		if(re==1){return false;}
		//save

		//document.frmProcedure.submit();


		$("#sb_dxids").val(sb_dxids);
		var strsave=$("form").serialize();
		strsave+="&savedby=ajax";

		//var template_content = CKEDITOR.instances['elem_pnData'].getData() ;
		var template_content = $('#elem_pnData').redactor('code.get');
		strsave += "&elem_pnData="+encodeURIComponent(template_content);
		strsave += "&is_sb_md="+is_sb_md;

		if(typeof(setProcessImg) != 'undefined' )setProcessImg("1");

		$.post(zPath+"/chart_notes/saveCharts.php", strsave, function(data) {
			if(typeof(setProcessImg) != 'undefined' )setProcessImg("0");
			if(data){
				if(data.up_iop_summery && data.up_iop_summery==1){
					if(typeof(window.opener.top.fmain.loadExamsSummary)!="undefined"){ window.opener.top.fmain.loadExamsSummary("iop_gon");}
				}
				if(data.print_pdf && data.print_pdf!=""){
					open_print_window(data.print_pdf);
					return;
				}
			}
			top.window.close();
		},'json');
	}

	function hold_dr_sig(){
		//scrol = $(window).scrollTop();
		//$('#hold_to_phy_div').css('top',scrol+310);
		//$('#hold_to_phy_div').show();
		//$(window).scroll(function(){$('#hold_to_phy_div').css('top',$(window).scrollTop()+310);});
		//$('#hold_to_phy_div .hold').click(function(){
			if($('#hold_to_physician').val()==''){
				//top.fAlert('Please select a physician');
				//displayConfirmYesNo_v2("Hold sign", "Please select a physician");
				$("<div class=\"alert alert-danger\"> Please select a physician.</div>").insertBefore("#hold_to_phy_div .modal-body");
			}else{
				$('#hidd_hold_to_physician').val($('#hold_to_physician').val());
				document.getElementById('elem_btnSave').click();
				$('#hold_to_phy_div .close').trigger("click");
			}
		//});
	}

	function delProcedure(id){
		if(confirm("Are you sure to delete this procedure note? ")){
			var qry = (id!="") ? "elem_saveForm=procedures_save&chart_del_id="+id : "";
			if(qry!=""){
				if(typeof(setProcessImg) != 'undefined' )setProcessImg("1");
				$.post(zPath+"/chart_notes/saveCharts.php", qry, function(data) {
						//console.log(data);
						window.close();
				});
				//window.location.replace(zPath+"/chart_notes/saveCharts.php"+qry);
			}
		}
	}

	function finalizeProcedure(){
		var op_finalize = $("#elem_finalized_status").val()=="1" ? "0" : "1";
		var id = $("#elem_chart_procedures_id").val();
		if(typeof(id)=="undefined" || id==""){ return; }
		var qry = "elem_saveForm=procedures_save&chart_finalize_id="+id+"&op_finalize="+op_finalize+" " ;
		if(qry!=""){
			if(typeof(setProcessImg) != 'undefined' )setProcessImg("1");
			$.post(zPath+"/chart_notes/saveCharts.php", qry, function(data) {
					if(typeof(setProcessImg) != 'undefined' )setProcessImg("0");
					$("#elem_finalized_status").val(data.finalize);
					$("#elem_btnFinalize").html(""+data.btnfinalize).addClass("hidden");
					if(data.finalize=="1"){ $("#elem_btnSave, #hold_btn, #del_btn").addClass("hidden"); }else{ $("#elem_btnSave, #hold_btn").removeClass("hidden"); }
					//console.log(data);
					window.location.reload();
			},'json');
		}
	}

	function print_procedure(flg){
		//check tab
		var tb = $('.nav-tabs li.active a').text();
		if(typeof(tb)!="undefined" && (tb=="Consent Form" || tb=="Op Report")){
			var url="";

			if(tb=="Consent Form"){
				var cfi = $("#curConfrmId").val();
				var fii = $("#proc_con_frm_id").val();
				if(typeof(cfi)!="undefined" && typeof(fii)!="undefined" && cfi!="" && fii!="" && cfi!="0" && fii!="0"){
					url=zPath + "/patient_info/consent_forms/print_consent_form.php?consent_form_id="+cfi+"&consent=yes&form_information_id="+fii;
				}
			}else if(tb=="Op Report"){
				var pri = $("#proc_pn_rep_Id").val();
				if(typeof(pri)!="undefined" && pri!="" && pri!="0"){
					url=zPath + "/chart_notes/progress_notes/load_file.php?elem_pnRepId="+pri+"&media_id=";
				}
			}

			if(url!=""){
				window.location.replace(url);
			}
			return ;
		}


		var amnd = $.trim($("#elem_amndmnt").val());
		var hid_pa_sign_by = $.trim($("#hid_pa_sign_by").val());
		if(amnd!=""){ //|| (hid_pa_sign_by!="" && hid_pa_sign_by!="0")
			var id = $("#hid_pa_final_by").val();
			if(id=="" || id=="0"){
				if(typeof(flg)=="undefined"){
					fancyConfirm("Amendment of this procedure note is not finalized. Do you want to print this? ","", "print_procedure(true)");
					return;
				}
			}
		}

		$("#elem_hidPrint").val("1");
		if($("#elem_btnSave").length>0 && !$("#elem_btnSave").hasClass("hidden")){	$("#elem_btnSave").trigger("click");}
		else{ $("#elem_hidPrint").val("2"); 	saveProcedure();  } //print only
	}

	function checksignle(ob){
		$("#pt_site_lasik").html("").removeClass();
		single_chkbx(ob);
		refreshconsentIframe();
		refreshOpnotesiteval();
		var v = $(":checked[name=elem_site]").val();
		var cv = ""+v.toLowerCase()+"col";
		$("#pt_site_lasik").html(v).addClass(" pt_info bg-info "+cv);
	}

	function insertTimeIOP(){

		var x = $(":input[name=elem_iopType]").val();
		if(x==""){ $(":input[name=elem_iopType]").val("TA"); }

		var o = document.getElementById("elem_iopTime");
		insertTime(o);
	}

	//This function used for refresh the procedure consent iframe for reloading the site value on click of refresh button.
	function refreshconsentIframe(){

		var idProc = $(":input[name=elem_chart_procedures_id]").val();
		var s = $(":input[name=elem_consentForm]").val();
		var curConfrmId = $("#curConfrmId").val();
		var a = $("#consent_form  iframe").attr("src");
		var chartId = $("#elem_form_id").val();
	//	alert(a);
		var site='';
		if($("#elem_site_ou").is(":checked") || $("#elem_site_od").is(":checked") || $("#elem_site_os").is(":checked")){
			document.getElementById('consent_form').src = document.getElementById('consent_form').src;
			if($("#elem_site_ou").is(":checked")){
				site='both eyes';
			}else if($("#elem_site_od").is(":checked")){
				site='right eye';
			}else if($("#elem_site_os").is(":checked")){
				site='left eye';
			}
		}
		if((s!="") || typeof(a)=="undefined"){
				$("#curConfrmId").val(s);
				var u = (typeof(zPath_remote) != "undefined") ? zPath_remote : zPath ;
				var hgt = parseInt($("#iframeConsentForm").css("height"));
				$("#consent_form  iframe").attr("src",u+"/patient_info/consent_forms/consentFormDetails.php?consent_form_id="+s+"&pop_procedure=1&chart_procedures_id="+idProc+"&pophgt="+hgt+"&site="+site+"&chartId="+chartId+"");
		}

	}

	function refreshOpnotesiteval(){

		var idProc = $(":input[name=elem_chart_procedures_id]").val();
		var s = $(":input[name=elem_OpTempId]").val();
		if(s=="0"){  s=""; }
		var curOpId=$("#curOpId").val();
		//var pd = CKEDITOR.instances.elem_pnData.getData();
		var pd = $('#elem_pnData').redactor('code.get');

		if((s!="") || (s!="" && pd=="")){
			$("#curOpId").val(s);

			var e = $(":checked[name=elem_site]").val();
			if(typeof(e)=="undefined")e="";
			var selText = '';
			var selectedoptions = fun_mselect('#elem_dxCode',"val"); // $('#elem_dxCode').multiselect('getChecked');
			$(selectedoptions).each(function(){
				if(document.getElementById($(this)) !== undefined && document.getElementById($(this)) !== null){
					selText +=  $(this).val()+';';
				}else{
					selText += "";
				}
			});

			var url = zPath+"/chart_notes/onload_wv.php?elem_action=Procedures";
			url += "&elem_opNoteId="+s+"&elem_opNoteEye="+e+"&chart_procedures_id="+idProc+"&elem_opNotedxCode="+selText;
			$.get(url, function(data){
				if(data!=""){
					//CKEDITOR.instances.elem_pnData.setData( ''+data );
					$('#elem_pnData').redactor('code.set',''+selText);
				}
			});
		}
	 }

	 function check_qty_val(){
		var arr_qty_obj={"elem_PreOpMed":"elem_med_preop_lot_qty",
									"elem_IntraVitrealMeds":"elem_med_intravitreal_lot_qty",
									"elem_PostOpMeds":"elem_med_postop_lot_qty"};
		var arr_lot_obj={"elem_PreOpMed":"elem_PreOpMedLot",
									"elem_IntraVitrealMeds":"elem_IntraVitrealMedsLot",
									"elem_PostOpMeds":"elem_PostOpMedsLot"};
		var ret=1;
		var error_msg=preop_med_name=intravitreal_med_name=postop_med_name="";
		var ln = $("#procnote span input[name*=elem_PreOpMed]").length;

		for(var i=1;i<=ln;i++){
			if($("input[name=elem_med_preop_lot_qty"+i+"]").val()!="" && $.trim($("input[name=elem_PreOpMedLot"+i+"]").val())=="" && $("input[name=elem_med_preop_lot_qty"+i+"]").val()!="0"){
				preop_med_name=$("input[name=elem_PreOpMed"+i+"]").val();
				if(typeof(preop_med_name)!="undefined" && preop_med_name!=""){
				error_msg+="\n Please enter Lot# for '"+preop_med_name+"'";
				$("input[name=elem_PreOpMedLot"+i+"]").css("border","1px solid red");
				}
			}
		}
		var ln = $("#div_intravitreal_meds span input[name*=elem_IntraVitrealMeds]").length;
		for(var i=1;i<=ln;i++){
			if($("input[name=elem_med_intravitreal_lot_qty"+i+"]").val()!="" && $.trim($("input[name=elem_IntraVitrealMedsLot"+i+"]").val())=="" && $("input[name=elem_med_intravitreal_lot_qty"+i+"]").val()!="0"){
				intravitreal_med_name=$("input[name=elem_IntraVitrealMeds"+i+"]").val();
				if(typeof(intravitreal_med_name)!="undefined" && intravitreal_med_name!=""){
				error_msg+="\n Please enter Lot# for '"+intravitreal_med_name+"'";
				$("input[name=elem_IntraVitrealMedsLot"+i+"]").css("border","1px solid red");
				}
			}
		}
		var ln = $("#procnote span input[name*=elem_PostOpMeds]").length;
		for(var i=1;i<=ln;i++){
			if($("input[name=elem_med_postop_lot_qty"+i+"]").val()!="" && $.trim($("input[name=elem_PostOpMedsLot"+i+"]").val())=="" && $("input[name=elem_med_postop_lot_qty"+i+"]").val()!="0"){
				postop_med_name=$("input[name=elem_PostOpMeds"+i+"]").val();
				if(typeof(postop_med_name)!="undefined" && postop_med_name!=""){
				error_msg+="\n Please enter Lot# for '"+postop_med_name+"'";
				$("input[name=elem_PostOpMedsLot"+i+"]").css("border","1px solid red");
				}
			}
		}
		/*for(var i=1;i<=8;i++){
			if($("input:text[name=elem_IntraVitrealMeds"+i).val()=="" && $("input:text[name=elem_med_intravitreal_lot_qty"+i).val()!="" && $("input:text[name=elem_med_intravitreal_lot_qty"+i).style.display=="inline-block"){
				med_name=$("input:text[name=elem_PreOpMedLot"+i).val();
				error_msg+="\n Please enter Lot of Medication '"+med_name+"'";
			}
		}*/
		if(error_msg){
			//top.fAlert(error_msg);
			displayConfirmYesNo_v2("Save checks", error_msg);
			return 1;
		}else{return 0;	}
	}

	function proc_add_med_fields(o){
		var flg=1;
		$(o).parent().parent().find("input").each(function(){ if($.trim(this.value)==""){ flg=0; } });
		if(flg==1){
			var on = o.name;
			var idxo = on.replace(/elem_PreOpMed|elem_PostOpMeds|elem_IntraVitrealMeds/,"");
			idx = parseInt(idxo)+1;
			if(on.indexOf("elem_PreOpMed")!=-1){
				var a="divPreOpMeds";
				var b="elem_PreOpMed"+idx;
				var c="elem_PreOpMedLot";
				var d="elem_med_preop_lot_qty";
				var e="elem_med_preop_lot_id"+idx;
				var f="elem_med_preop_lot_qty";
			}else if(on.indexOf("elem_PostOpMeds")!=-1){
				var a="divPostOpMeds";
				var b="elem_PostOpMeds"+idx;
				var c="elem_PostOpMedsLot";
				var d="elem_med_postop_lot_qty";
				var e="elem_med_postop_lot_id"+idx;
				var f="elem_med_postop_lot_qty";

			}else if(on.indexOf("elem_IntraVitrealMeds")!=-1){
				var a="divIntraVitrealMeds";
				var b="elem_IntraVitrealMeds"+idx;
				var c="elem_IntraVitrealMedsLot";
				var d="elem_med_intravitreal_lot_qty";
				var e="elem_med_intravitreal_lot_id"+idx;
				var f="elem_med_intravitreal_lot_qty";
			}

			var h1="", h2="", h3="";
			h1 = "<span id=\""+a+"\"><input type=\"text\" name=\""+b+"\" value=\"\" class=\"form-control\" onchange=\"getMedName(this);\" ></span>";
			h2 = "<input type=\"text\" name=\""+c+idx+"\" value=\"\" class=\""+d+" form-control\" >"+
				"<input type=\"hidden\" name=\""+e+"\" value=\"\" >";
			h3 = "<input type=\"text\" readonly=\"\" name=\""+f+idx+"\" value=\"\" class=\"form-control\" >";

			$(o).parent().parent().append(h1);
			$("input[name="+c+idxo+"]").parent().append(h2);
			$("input[name="+f+idxo+"]").parent().append(h3);
			cn_ta_procedures();
		}
	}

	function getMedName(fieldObj)
	{
		if($.trim(fieldObj.value) == ""){
			return;
		}
		var med_upc=fieldObj.value;
		//console.log(zPath+"/chart_notes/onload_wv.php?elem_action=Procedures&med_upc="+med_upc);
		$.ajax({
				type: "GET",
				url: zPath+"/chart_notes/onload_wv.php?elem_action=Procedures&med_upc="+med_upc,
				success: function(resp){//top.fAlert(resp);
					//
					if(resp && resp.indexOf("ERROR")==-1&& resp.indexOf("DOCTYPE")==-1){fieldObj.value=resp;}else{console.log(resp);}
				}
			});
		proc_add_med_fields(fieldObj);
	}

	function set_qty_in_hand(obj,arr_med_name,arr_item_no_qty,arr_thrash,arr_lot_no){
		var arr_qty_obj={"elem_PreOpMed":"elem_med_preop_lot_qty",
									"elem_IntraVitrealMeds":"elem_med_intravitreal_lot_qty",
									"elem_PostOpMeds":"elem_med_postop_lot_qty"};
		var arr_lot_obj={"elem_PreOpMed":"elem_PreOpMedLot",
									"elem_IntraVitrealMeds":"elem_IntraVitrealMedsLot",
									"elem_PostOpMeds":"elem_PostOpMedsLot"};
		var curr_obj_ctr=obj.name.substr(-1);
		var obj_len=parseInt(obj.name.length)-parseInt(1);
		var cur_qty_obj=obj.name.substr(0,obj_len);
		var curent_qty_obj=arr_qty_obj[cur_qty_obj];
		var curent_lot_obj=arr_lot_obj[cur_qty_obj];
		$("input:text[name="+curent_qty_obj+curr_obj_ctr+"]").val("0");
		var medi_name=$.trim(obj.value);
		var opt_med_id=arr_med_name[medi_name];
		if(typeof(arr_item_no_qty[opt_med_id])!='undefined'){
			$("input:text[name="+curent_qty_obj+curr_obj_ctr+"]").val(arr_item_no_qty[opt_med_id]);
			if(typeof(arr_thrash[opt_med_id])!='undefined' && arr_thrash[opt_med_id]<=arr_item_no_qty[opt_med_id]){
				top.fAlert(medi_name+" has reached threshold limit, please re-order")
			}
		}
		var lot_obj_name=curent_lot_obj+curr_obj_ctr;
		set_lot_val(arr_lot_no,lot_obj_name,opt_med_id)
	}

	function get_surgeon_sign(){
		var id = $("#el_post_op_surgeon").val();
		if(typeof(id)=="undefined" || id==""){ top.fAlert("Please select surgeon."); return;}
		var sgn = $("#el_surgeon_sign_path").val();
		if(sgn!=""){
			var s = "<img src=\""+sgn+"\" alt=\"sign\" height=\"30\">";
			$("#div_sgn_sign").html(s);
			$("#el_surgeon_sign").val("1");
			$("#el_surgeon_sign_dos").val($("#el_surgeon_sign_dos").data("dtcr"));
		}
	}

	function set_finalized_view(){

		$(".final_proc :input").each(function(){
			if($(this).parents("#amendment").length>0){return;}
			var prop = (this.type!="text" && this.type!="textarea") ? "disabled":"readonly";
			$(this).prop(prop, true);

			if(this.id == "elem_startTime" || this.id == "elem_endTime" || this.id == "elem_iopOd" || this.id == "elem_iopOs"){
				$(this).prop("onclick", null).off("click");
			}

			$(this).on("click", function(){ fAlert("This Proc Note is finalized, please create an Amendment to document changes"); this.blur();  });

		});

		//
		var hgt = parseInt($("#dvprocedure_note").css("height"))*0.4;
		$("#elem_amndmnt").css("cssText", "height:"+hgt+"px !important");

		$('#elem_amndmnt').bind('focus',function(){  get_operator_name_date(); });
		$('#elem_amndmnt').on('blur',function(){ remove_operator_name_date(); });

	}

	function saveProcedure_amnd(flgfin){
		if(elem_per_vo == "1"){ return; }
		if(typeof(flgfin)=="undefined"){ flgfin="0"; }

		var strsave="elem_saveForm=proc_amedment_save"; //$("form").serialize();
		strsave += "&elem_amndmnt="+encodeURIComponent($("#elem_amndmnt").val());
		strsave += "&hid_pa_sign="+$("#hid_pa_sign").val();
		strsave += "&hid_fin="+flgfin;
		strsave += "&elem_chart_procedures_id="+$("#elem_chart_procedures_id").val();
		strsave+="&savedby=ajax";
		if(typeof(setProcessImg) != 'undefined' )setProcessImg("1");

		$.post(zPath+"/chart_notes/saveCharts.php", strsave, function(data) {
			if(typeof(setProcessImg) != 'undefined' )setProcessImg("0");
				top.window.close();
			});
	}

	function finalizeProcedure_amnd(){
		if(elem_per_vo == "1"){ return; }

		var sign = $.trim($("#hid_pa_sign").val());
		var signby = $.trim($("#hid_pa_sign_by").val());
		var amndmnt = $.trim($("#elem_amndmnt").val());

		var msg="";
		if(amndmnt==""){
			msg+="<br/> - Amendments";
		}
		if(sign=="" || signby==""){
			msg+="<br/> - Signatures";
		}

		if(msg!=""){
			fAlert("Please Enter followings:-"+msg);
		}else{
			saveProcedure_amnd(1);
		}
	}

	function proc_get_phy_sign(){
		var phyid = $("#hid_pa_sign_by").val();
		var procid = $("#elem_chart_procedures_id").val();
		var finalby = $("#hid_pa_final_by").val();
		if(typeof(phyid)=="undefined" || phyid=="" || typeof(procid)=="undefined" || procid=="" || elem_per_vo == "1" || (typeof(finalby)!="undefined" && finalby!="" && finalby!="0")){ return; }
		var strsave="elem_formAction=getUserSign";
		strsave += "&req_ptwo=1";
		strsave += "&elem_form_id=0";
		strsave += "&procid="+procid;
		strsave += "&elem_physicianId="+phyid;
		strsave += "&num=1";
		$.post(zPath+"/chart_notes/requestHandler.php", strsave, function(dt) {
			if(typeof(setProcessImg) != 'undefined' )setProcessImg("0");

			if(dt && dt[1] && dt[0]){
				var oT = gebi("td_signature_applet1");
				var oSP= gebi("hid_pa_sign");
				if(oT){
					if(oSP){oSP.value=""+dt[1];}
					oT.innerHTML = "<img src=\""+dt[0]+"\" alt=\"img\">";
					flgSave1=1;
				}
			}else{
				//fAlert("User signatures do not exists!");
				$("#td_signature_applet1").trigger("click");
			}

			//------  processing done --------------------------
			},'json');
	}


function getAssessmentSign(num,n,coords,sdata,simg, cnfrm)
{
	var final_flg = ($("#hid_pa_final_by").val()!="" && $("#hid_pa_final_by").val()!="0")  ? 1 : 0 ;
	// if View Only Access
	if(elem_per_vo == "1" || final_flg==1){ return; }
	if(typeof(num) == "undefined"){num=1;}


		var v1="td_signature_applet"+num;
		var v2="dv_ShowSign"+num;
		var v5="hid_pa_sign";


	if(typeof(n) == "undefined"){
		var w = parseInt(225*2.5);
		var h =  parseInt(45*2.5);
		var opd= 0; //$("#divWorkView").scrollTop();
		var tmp = $('#'+v1).position();
		if($("#"+v2).length<=0){
			var strpixls="";
			var img =  "<iframe id=\"ifrm_signApp"+num+"\" src=\"signApplet.php?sec=proc&final_flg="+final_flg+"&signType="+num+"\" border=\"0\" height=\"100%\" width=\"100%\" scrolling=\"0\"></iframe>";
			var str = "<div id=\""+v2+"\" >"+
					img+"</div>";
			$('#'+v1).append(str);
			$('#'+v2).css({'position':'absolute','width':''+w+"px",'height':''+h+"px",'background-color':'white','border':'1px solid black','z-Index':2,'overflow':'hidden'});
		}else{
			$("#"+v2).show();
		}
		$("#"+v2).css({"left":tmp.left+"px","top":(opd+tmp.top-h)+"px"});
	}else if(n==1){
			var fid = $("#elem_chart_procedures_id").val();
			$('#'+v1+' img').remove();
			$("#"+v2).hide();
			var proId=(final_flg==1) ? $('#hid_pa_sign_by').val() : "";
			if(typeof(setProcessImg) != 'undefined' )setProcessImg(1,""+v1);
			var p = { 'elem_formAction':'GetSign','strpixls':''+coords+'','fid':''+fid,'signType':num,'final_flg':final_flg,'proId':proId,'sData':sdata,'sImg':simg};
			$.post('requestHandler.php',p,function(data){
					if(typeof(setProcessImg) != 'undefined' )setProcessImg(0,""+v1);
					if(data&&data.src&&data.src!=''){
					var u = zPath;
					$('#'+v1).append("<img src=\""+data.src+"\" alt=\"sign\" width=\"150\" height=\"30\" >");
					$('input[name='+v5+']').val(data.sign_path);
				}  }, "json");

	}else if(n==2){
		$("#"+v2).hide();
	}else if(n==3){ //Clear Sign

	}
}

//Signature Script ------------------

function showAllergys(v){
		if(v==1){
			$("#divAllergy").show();
		}else{
			$("#divAllergy").hide();
		}
	}

//Date in --
function get_operator_name_date()
{
	var final_flg = ($("#hid_pa_final_by").val()!="" && $("#hid_pa_final_by").val()!="0")  ? 1 : 0 ;
	// if View Only Access
	if(elem_per_vo == "1" || final_flg==1){ return; }

	var t = current_date(top._dtFormat,2,true) + ' ' + operator + ': \n' + $("#elem_amndmnt").val();
	$("#elem_amndmnt").val(t);
	var el = $("#elem_amndmnt")[0];
	setTimeout(function(){set_caret_position("elem_amndmnt", 21)}, 30);
}

function remove_operator_name_date()
{
	var str_text = $("#elem_amndmnt").val();
	var match_string = current_date(top._dtFormat,2,true) + ' ' + operator + ': \n';
	if(str_text == match_string )	$("#elem_amndmnt").val('');
}
//--

//====================
$(document).ready(function () {
	//====VALIDATION TO STOP DOCTORS TO FILL SITE/LIDS VALUE BEFORE GOTO CONSENT/OPNOTE TAB=========//
	$('.nav-tabs a').on('shown.bs.tab', function(event){
		var x = $(event.target).text();         // active tab
		var y = $(event.relatedTarget).text();  // previous tab

		if(y == 'Proc Note'){
			var chkds = $("input[name='elem_site']:checked");
			if (chkds.length == 0 && x != 'Amendment')  {
				fAlert('Please select Site/Lids');
				$('.nav-tabs a[href="#procnote"]').tab('show');
				return false;
			}
		}

		if(x == 'Amendment'){
			if($(event.target).parent().hasClass("disabled")){
				$(event.relatedTarget).trigger("click");
				return false;
			}
		}

	});

	cn_ta_procedures();
	sb_addTypeAhead();
	cn_typeahead();
	proc_showTabs('proc_note');

	//==============Function for get qty on blur==============================//
	set_value_qty_texts(arr_med_name,arr_item_no_qty,arr_thrash);
	//===========Function for set saved values qty on Load===================//

	$("#elem_procedure").change(function(){
		getProcedureInfo();
		//showTabs('consent_form');
		setTimeout(function(){set_value_qty_texts(arr_med_name,arr_item_no_qty,arr_thrash,arr_lot_no)},1500);
		//Bottox
		setBottox();
	});

	//fun_mselect("#elem_dxCode, #cpt_multi_select", "onchange", function(){setSuperBillValue_onchange();});

	//
	//CKEDITOR.replace( 'elem_pnData', { width:'90%', height:'100%'} );
	getProcedureInfo(1);
	//Drop Downs
	$('#cpt_multi_select, #elem_dxCode').selectpicker('render');

	//Bottox check
	setBottox(1);

	//Finalize
	if($("#dvprocedure_note_sec").hasClass("final_proc")){
		set_finalized_view();
	}


	$('#cpt_multi_select, #elem_dxCode').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
		var o = $(e.target).parents("div.bootstrap-select");
		var w = o[0].offsetWidth;
		o.removeClass("form-control").css({"width":w+"px"});
		o.find("span.filter-option").css({"white-space":"normal"});
	});

	//
	$("[data-toggle='tooltip']").tooltip();
});
