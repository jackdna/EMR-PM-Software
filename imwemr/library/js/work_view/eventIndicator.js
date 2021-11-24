var ei_arrEv = new Array("click","keyup","keydown","keypress");
var ei_conName = [];
//
ei_detechIndicator = function(dvId){
	// remove event
	for(var i in ei_arrEv){
		//document.detachEvent("on"+ei_arrEv[i], ei_checkEvent);
		//console.log(dvId, ei_checkEvent);
		$("#"+dvId).unbind(""+ei_arrEv[i], ei_checkEvent).removeClass("bggrey");
	}
	//Set Status
	if((typeof ei_conName[dvId] != "undefined") && (ei_conName[dvId] != "")){
		var oCon = gebi(ei_conName[dvId]);
		if(oCon){
			oCon.value = "1";
		}
	}
}

//
ei_checkEvent = function(){
	var e = window.event;
	if(e){}else{return;}
	var eo = e.srcElement;
	var efrm = eo.type;
	var etype = e.type;
	
	var flgCanvas=false;
	if(eo.tagName=="CANVAS"){
		$(eo).parent("div[id*=divCanvas]").triggerHandler("mouseout");
		var val = $("input[name='hidCanvasWNL']").val();				
		if(typeof(val)!="undefined" && val.indexOf("no")!=-1){ //ifthere is a 'no' then flag = yes
			flgCanvas=true;
		}	
	}	
	
	if((efrm == "text") || (efrm == "textarea") || (efrm == "select-one") || (efrm == "checkbox") || (efrm == "radio") || flgCanvas==true ){
		var dvId = $(eo).parents(".tab-pane").attr("id");
		ei_detechIndicator(dvId);
	}
}

//
ei_attachIndicator = function(conName,sttsElem){
	if(typeof conName != "undefined"){
		ei_conName[conName] = ""+sttsElem;
	}

	// attach event
	var len = ei_arrEv.length;
	for(var i=0;i<len;i++){
		//document.attachEvent("on"+ei_arrEv[i], ei_checkEvent);
		$("#"+conName).bind(""+ei_arrEv[i], ei_checkEvent);
	}
}

function ei_attachIndicator_eom(){ //EOM
	var ed = $("#elem_editMode_load").val();
	var ch = $("#elem_chng_divEom").val();
	var ch2 = $("#elem_chng_divEom2").val();
	var ch3 = $("#elem_chng_divEom3").val();
	if(ed==0 || ch==0){
		ei_attachIndicator('divEom','elem_chng_divEom');	
	}
	if(ed==0 || ch2==0){
		ei_attachIndicator('divEom2','elem_chng_divEom2');	
	}
	if(ed==0 || ch3==0){
		ei_attachIndicator('divEom3','elem_chng_divEom3');	
	}
}

function ev_attachIndicator_iop(md){

	var strClss="";
	//Iop -------
	strClss = $("#elem_chng_iop").val()=="0"  ? "greyAll_v2" : ""; //"<?php echo ($elem_chng_iop=='0') ? 'greyAll_v2' : '' ;?>";
	$("#divIopElem :input,#hdr_iop :input").addClass(""+strClss);
	$("#divIopElem :input,#hdr_iop :input, #divIopElem label[for],#hdr_iop label[for]").bind("keyup mouseup",function(){
		//set Indicator
		var tmp = $("#elem_ci_iop").val();
		tmp = (typeof tmp == "undefined") ? 1 : parseInt(tmp)+1;
		$("#elem_ci_iop").val(tmp);
		//set this class
		$("#divIopElem :input,#hdr_iop :input").removeClass("greyAll_v2");
		$("#elem_chng_iop").val(1);//status
		
	});
	//Iop -------

	//Gonio -------
	var ptrn = "";
	if($("#elem_chng_divIop_Od").val() != "1"){
		ptrn += "#divIop :input[name*='Od_'], "+
				"#divIop :input[name*='_od'],"+
				"#divIop :input[name*='Od'],"+
				"#divIop :input[name*='od_']";
	}

	if($("#elem_chng_divIop_Os").val() != "1"){
		ptrn += (ptrn != "") ? "," : "";
		ptrn += "#divIop :input[name*='Os_'], "+
				"#divIop :input[name*='_os'],"+
				"#divIop :input[name*='Os'],"+
				"#divIop :input[name*='os_']";
	}
	
	//	
	$(""+ptrn).addClass("greyAll_v2").bind("keyup mouseup",function(){

		//set Indicator
		var tmp = $("#elem_ci_gonio").val();
		tmp = (typeof tmp == "undefined") ? 1 : parseInt(tmp)+1;
		$("#elem_ci_gonio").val(tmp);

		//set this class
		$(this).removeClass("greyAll_v2");

		//Check OD or OS
		var eye = "";
		var cnm = $(this).attr("name");
		if(cnm.toLowerCase().indexOf("od_") != -1 || cnm.toLowerCase().indexOf("_od") != -1){
			eye = "od";
		}else if(cnm.toLowerCase().indexOf("os_") != -1 || cnm.toLowerCase().indexOf("_os") != -1){
			eye = "os";
		}else if(cnm.toLowerCase().indexOf("od") != -1 || cnm.toLowerCase().indexOf("od") != -1){
			eye = "od";
		}else if(cnm.toLowerCase().indexOf("os") != -1 || cnm.toLowerCase().indexOf("os") != -1){
			eye = "os";
		}

		newET_setGray_Exe("divIop",eye);
	});
	
	//
	$("#divIop label[for*='od_'], #divIop label[for*='os_'] ").bind("keyup mouseup",function(){
		var idelem = $(this).attr("for");
		if(idelem && typeof(idelem)!="undefined"){	$("#"+idelem).triggerHandler("mouseup");	}
	});

	//Gonio -------

	//Draw --------
	var ptrn = "";
	if($("#elem_chng_divIop3_Od").val() != "1"){
		ptrn += "#divIop3 :input[name*='Od_'],"+
				"#divIop3 :input[name*='_od'],"+
				"#divIop3 :input[name*='Od'],"+
				"#divIop3 :input[name*='od_']";
	}

	if($("#elem_chng_divIop3_Os").val() != "1"){
		ptrn += (ptrn != "") ? "," : "";
		ptrn += "#divIop3 :input[name*='Os_'],"+
				"#divIop3 :input[name*='_os'],"+
				"#divIop3 :input[name*='Os'],"+
				"#divIop3 :input[name*='os_']";
	}

	$(""+ptrn).addClass("greyAll_v2").bind("keyup mouseup",function(){
		//set Indicator
		var tmp = $("#elem_ci_gonio").val();
		tmp = (typeof tmp == "undefined") ? 1 : parseInt(tmp)+1;
		$("#elem_ci_gonio").val(tmp);

		//set this class
		$(this).removeClass("greyAll_v2");

		//Check OD or OS
		var eye = "";
		var cnm = $(this).attr("name");
		if(cnm.toLowerCase().indexOf("od_") != -1 || cnm.toLowerCase().indexOf("_od") != -1){
			eye = "od";
		}else if(cnm.toLowerCase().indexOf("os_") != -1 || cnm.toLowerCase().indexOf("_os") != -1){
			eye = "os";
		}else if(cnm.toLowerCase().indexOf("od") != -1 || cnm.toLowerCase().indexOf("od") != -1){
			eye = "od";
		}else if(cnm.toLowerCase().indexOf("os") != -1 || cnm.toLowerCase().indexOf("os") != -1){
			eye = "os";
		}

		newET_setGray_Exe("divIop3",eye);
	});
	//Draw --------

	//Dialation -------
	strClss = "";
	strClss =  $("#elem_chng_dilation").val()=="0"  ? "greyAll_v2" : ""; //"<?php echo ($elem_chng_dilation=='0') ? 'greyAll_v2' : '' ;?>";
	$("#divDilation :input").addClass(""+strClss);
	$("#divDilation :input, #divDilation label[for]").bind("keyup mouseup",function(){
		//set Indicator
		var tmp = $("#elem_ci_dilation").val();
		tmp = (typeof tmp == "undefined") ? 1 : parseInt(tmp)+1;
		$("#elem_ci_dilation").val(tmp);
		//set this class
		$("#divDilation :input").removeClass("greyAll_v2");
		$("#elem_chng_dilation").val(1);
	});
	//Dialation -------
	
	//OOD -----
	strClss = "";
	strClss = $("#elem_chng_OOD").val()=="0"  ? "greyAll_v2" : ""; //"<?php echo ($elem_chng_OOD=='0') ? 'greyAll_v2' : '' ;?>";
	$("#divOOD :input").addClass(""+strClss);
	
	$("#divOOD :input, #divOOD label[for]").bind("keyup mouseup",function(){
		//set Indicator
		var tmp = $("#elem_ci_OOD").val();
		tmp = (typeof tmp == "undefined") ? 1 : parseInt(tmp)+1;
		$("#elem_ci_OOD").val(tmp);
		//set this class
		$("#divOOD :input").removeClass("greyAll_v2");
		$("#elem_chng_OOD").val(1);
			
	});
	//OOD -----
}
